<template>
    <div class="mr-tree-wrapper">
        <div class="mr-tree" :style="{ width: layout.width + 'px', height: layout.height + 'px' }">
            <svg class="mr-tree-edges" :width="layout.width" :height="layout.height">
                <path v-for="edge in layout.edges" :key="edge.id" :d="edge.path" class="mr-tree-edge"/>
            </svg>
            <div
                v-for="marker in layout.markers"
                :key="marker.id"
                class="mr-tree-node"
                :style="{ left: marker.x + 'px', top: marker.y + 'px', width: LABEL_BLOCK_WIDTH + 'px' }"
            >
                <MergeRequestTreeBranchMarker :branch-name="marker.branchName"/>
            </div>
            <div
                v-for="node in layout.nodes"
                :key="node.mergeRequest.iid"
                class="mr-tree-node"
                :style="{ left: node.x + 'px', top: node.y + 'px', width: LABEL_BLOCK_WIDTH + 'px' }"
            >
                <MergeRequestTreeNode :merge-request="node.mergeRequest"/>
            </div>
        </div>
    </div>
</template>

<style>

.mr-tree-wrapper {
    overflow: auto;
    padding: 8px;
}

.mr-tree {
    position: relative;
}

.mr-tree-edges {
    position: absolute;
    top: 0;
    left: 0;
    pointer-events: none;
}

.mr-tree-edge {
    fill: none;
    stroke: rgba(255, 255, 255, 0.25);
    stroke-width: 2;
}

.mr-tree-node {
    position: absolute;
}

.mr-tree-node small {
    font-size: 0.7em;
}

</style>

<script setup lang="ts">
import { computed } from 'vue';
import { MergeRequest } from '../../Types/GitlabMergeRequests';
import MergeRequestTreeNode from './MergeRequestTreeNode.vue';
import MergeRequestTreeBranchMarker from './MergeRequestTreeBranchMarker.vue';

const props = defineProps<{
    mergeRequests: MergeRequest[]
}>()

const NODE_SIZE = 44
const LABEL_BLOCK_WIDTH = 96
const COL_WIDTH = 150
const ROW_HEIGHT = 88
const ROW_TOP_PAD = 12
// Branch-name labels are wider than the spheres they sit under, so the outermost
// columns need extra room on each side or their labels get clipped by the container.
const PADDING_X = 30

type MrNode = { kind: 'mr', mergeRequest: MergeRequest, children: TreeNode[], row: number, col: number }
type MarkerNode = { kind: 'marker', branchName: string, children: TreeNode[], row: number, col: number }
type TreeNode = MrNode | MarkerNode

// A merge request is a child of the merge request whose source branch it targets
// (stacked-MR chains). If that chain loops back on itself (invalid/corrupted data),
// the offending link is cut so the layout below never recurses infinitely.
// MRs left without an MR parent (real roots) are grouped by their target branch:
// several independent chains merging into the same branch (e.g. "develop") share a
// single marker node instead of one marker per chain.
function buildForest(mergeRequests: MergeRequest[]): MarkerNode[] {
    const bySourceBranch = new Map<string, MergeRequest>()
    mergeRequests.forEach(mr => bySourceBranch.set(mr.source_branch, mr))

    const mrNodeByIid = new Map<number, MrNode>()
    mergeRequests.forEach(mr => mrNodeByIid.set(mr.iid, { kind: 'mr', mergeRequest: mr, children: [], row: 0, col: 0 }))

    const parentIid = new Map<number, number>()
    mergeRequests.forEach(mr => {
        const parent = bySourceBranch.get(mr.target_branch)
        if (parent && parent.iid !== mr.iid) parentIid.set(mr.iid, parent.iid)
    })

    for (const startIid of parentIid.keys()) {
        const seen = new Set<number>()
        let current = startIid
        while (parentIid.has(current)) {
            if (seen.has(current)) {
                parentIid.delete(startIid)
                break
            }
            seen.add(current)
            current = parentIid.get(current)!
        }
    }

    const mrRoots: MrNode[] = []
    mergeRequests.forEach(mr => {
        const node = mrNodeByIid.get(mr.iid)!
        const parent = parentIid.has(mr.iid) ? mrNodeByIid.get(parentIid.get(mr.iid)!) : undefined
        if (parent) parent.children.push(node)
        else mrRoots.push(node)
    })

    const markerByBranch = new Map<string, MarkerNode>()
    const markers: MarkerNode[] = []
    mrRoots.forEach(root => {
        const branchName = root.mergeRequest.target_branch
        let marker = markerByBranch.get(branchName)
        if (!marker) {
            marker = { kind: 'marker', branchName, children: [], row: 0, col: 0 }
            markerByBranch.set(branchName, marker)
            markers.push(marker)
        }
        marker.children.push(root)
    })

    return markers
}

// First child stays on its parent's row; every other child starts a new row below.
// Depth becomes the column. This keeps single stacked chains on one line while
// branching chains fan out downward, like the tree in the sketch. Marker nodes are
// laid out exactly like merge-request nodes, so a marker with several children
// (several chains sharing the same target branch) fans out the same way.
function layoutForest(roots: TreeNode[]): number {
    let nextRow = 0

    const layoutNode = (node: TreeNode, col: number): number => {
        node.col = col
        if (node.children.length === 0) {
            node.row = nextRow++
        } else {
            node.children.forEach(child => layoutNode(child, col + 1))
            node.row = node.children[0].row
        }
        return node.row
    }

    roots.forEach((root, i) => {
        if (i > 0) nextRow++
        layoutNode(root, 0)
    })

    return nextRow
}

function flatten(roots: TreeNode[]): TreeNode[] {
    const result: TreeNode[] = []
    const visit = (node: TreeNode) => {
        result.push(node)
        node.children.forEach(visit)
    }
    roots.forEach(visit)
    return result
}

function collectEdges(roots: TreeNode[]): { parent: TreeNode, child: TreeNode }[] {
    const edges: { parent: TreeNode, child: TreeNode }[] = []
    const visit = (node: TreeNode) => {
        node.children.forEach(child => {
            edges.push({ parent: node, child })
            visit(child)
        })
    }
    roots.forEach(visit)
    return edges
}

const layout = computed(() => {
    const roots = buildForest(props.mergeRequests)
    const rowCount = layoutForest(roots)
    const allNodes = flatten(roots)
    const edges = collectEdges(roots)

    const colCount = allNodes.reduce((max, node) => Math.max(max, node.col), 0) + 1

    // Sphere center: top-anchored in its row band (ROW_TOP_PAD from the top), leaving
    // the rest of ROW_HEIGHT for the branch-name label without touching the next row.
    // Markers are real tree nodes now, so they naturally land in column 0.
    const center = (col: number, row: number) => ({
        x: PADDING_X + col * COL_WIDTH + NODE_SIZE / 2,
        y: row * ROW_HEIGHT + ROW_TOP_PAD + NODE_SIZE / 2,
    })

    const block = (node: TreeNode) => {
        const c = center(node.col, node.row)
        return { x: c.x - LABEL_BLOCK_WIDTH / 2, y: c.y - NODE_SIZE / 2 }
    }

    const nodeId = (node: TreeNode) => node.kind === 'mr' ? `mr-${node.mergeRequest.iid}` : `marker-${node.branchName}`

    const edgePath = (p: { x: number, y: number }, c: { x: number, y: number }) => {
        const x1 = p.x + NODE_SIZE / 2
        const x2 = c.x - NODE_SIZE / 2
        const midX = (x1 + x2) / 2
        return `M ${x1},${p.y} C ${midX},${p.y} ${midX},${c.y} ${x2},${c.y}`
    }

    return {
        width: 2 * PADDING_X + colCount * COL_WIDTH,
        height: Math.max(rowCount, 1) * ROW_HEIGHT,
        nodes: allNodes
            .filter((node): node is MrNode => node.kind === 'mr')
            .map(node => ({ mergeRequest: node.mergeRequest, ...block(node) })),
        markers: allNodes
            .filter((node): node is MarkerNode => node.kind === 'marker')
            .map(node => ({ id: nodeId(node), branchName: node.branchName, ...block(node) })),
        edges: edges.map(({ parent, child }) => ({
            id: `${nodeId(parent)}--${nodeId(child)}`,
            path: edgePath(center(parent.col, parent.row), center(child.col, child.row)),
        })),
    }
})
</script>
