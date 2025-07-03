<?php

$repositories = cache('git-branches', function () {
    $repositoriesData = [];

    $repositories = service('git')['repositories'];
    $branchesToIgnores = service('git')['ignore_branches'] ?? [];
    foreach ($repositories as $repository) {

        $currentBranch = null;
        $branches = command("git branch", $repository);
        $branches = explode("\n", $branches);
        $branches = array_filter(array_map(function ($branch) use (&$currentBranch) {
            $cleanBranchName = trim(preg_replace('/^[* ]+/', '', $branch));
            if (str_starts_with($branch, '*'))
                $currentBranch = $cleanBranchName;
            return $cleanBranchName;
        }, $branches));

        $hasUnstagedFiles = command("git status --short | grep -v -E \"^ +\?.+$\"", $repository);
        $unpushedBranches = command("git log --branches --not --remotes --decorate=short --pretty=format:'%D' | sed 's/^HEAD -> //g' | sort -u", $repository) ?? '';

        $needPush = $unpushedBranches || $hasUnstagedFiles;
        $unpushedBranches = explode("\n", $unpushedBranches);

        $repoData = [
            'name' => basename($repository),
            'directory' => $repository,
            'checked_out_branch' => $currentBranch,
            'need_push' => $needPush,
            'unstaged_files' => $hasUnstagedFiles,
            'unpushed_branches' => $unpushedBranches,
            'branches' => array_diff($branches, $branchesToIgnores)
        ];

        $repositoriesData[] = $repoData;
    }

    usort($repositoriesData, fn($a, $b) =>  count($a['branches']) < count($b['branches']) ? 1:-1);

    return $repositoriesData;
});

function supportIssueNameInBranchName(string $branch)
{
    if (! $jiraHost = env('UTILUX_JIRA_HOST', false))
        return $branch;


    $jiraHost = preg_replace("~/rest/.+~", '', $jiraHost);

    $issues = [];
    preg_match_all("~\w+\-\d+~", $branch, $issues);
    foreach ($issues[0] as $issue) 
        $branch = str_replace($issue, "<a target='_blank' class='underline' href='$jiraHost/browse/$issue'>$issue</a>", $branch);

    return $branch;
}

?>
<div class="flex flex-col gap-4">
    <div class="flex justify-between items-center">
        <div class="text-2xl font-bold">Local Git Branches</div>
        <?= getCacheTimestamps('git-branches') ?>
        <?= clearCacheButton('git-branches') ?>
    </div>

    <?php foreach ($repositories as $repo) { ?>
        <?php if (count($repo['branches'])) { ?>
            <div class="flex flex-col card">
                <div class="flex items-center gap-5 justify-between">
                    <b><?= $repo['name'] ?></b>
                    <a href="/resources/actions/open-code.php?directory=<?= urlencode($repo['directory']) ?>">
                        <small class="flex flex-row gap-3">
                            <?= $repo['directory'] ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-folder-symlink" viewBox="0 0 16 16">
                                <path d="m11.798 8.271-3.182 1.97c-.27.166-.616-.036-.616-.372V9.1s-2.571-.3-4 2.4c.571-4.8 3.143-4.8 4-4.8v-.769c0-.336.346-.538.616-.371l3.182 1.969c.27.166.27.576 0 .742"/>
                                <path d="m.5 3 .04.87a2 2 0 0 0-.342 1.311l.637 7A2 2 0 0 0 2.826 14h10.348a2 2 0 0 0 1.991-1.819l.637-7A2 2 0 0 0 13.81 3H9.828a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 6.172 1H2.5a2 2 0 0 0-2 2m.694 2.09A1 1 0 0 1 2.19 4h11.62a1 1 0 0 1 .996 1.09l-.636 7a1 1 0 0 1-.996.91H2.826a1 1 0 0 1-.995-.91zM6.172 2a1 1 0 0 1 .707.293L7.586 3H2.19q-.362.002-.683.12L1.5 2.98a1 1 0 0 1 1-.98z"/>
                            </svg>
                        </small>
                    </a>
                </div>
                <details <?= count($repo['branches']) > 2 ? 'open' : '' ?>>
                    <summary>Local Branches (<?= count($repo['branches']) ?>)</summary>
                    <ul class="list-disc pl-5">
                        <?php foreach ($repo['branches'] as $branch) { ?>
                            <li class="<?= $repo['checked_out_branch'] == $branch ? 'font-bold' : '' ?>">
                                <?= in_array($branch, $repo['unpushed_branches']) ? "*": "" ?>
                                <?= supportIssueNameInBranchName($branch) ?>
                            </li>
                        <?php } ?>
                    </ul>
                </details>
                <?php if ($unstagedFiles = $repo['unstaged_files']) { ?>
                    <b class="pt-5">
                        Has unstaged files : 
                        <ul>
                            <?php foreach (explode("\n", $unstagedFiles) as $file) { ?>
                                <li><?= $file ?></li>
                            <?php } ?>
                        </ul>
                    </b>
                <?php } ?>
            </div>
        <?php } else { ?>
            <div class="flex items-center gap-5 justify-between">
                <span><?= $repo['name'] ?> <?= $repo['need_push'] ? '(Need push)': '' ?> : No feature/fix branches</span>
                <a href="/resources/actions/open-code.php?directory=<?= urlencode($repo['directory']) ?>">
                    <small class="flex flex-row gap-3">
                        <?= $repo['directory'] ?>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-folder-symlink" viewBox="0 0 16 16">
                            <path d="m11.798 8.271-3.182 1.97c-.27.166-.616-.036-.616-.372V9.1s-2.571-.3-4 2.4c.571-4.8 3.143-4.8 4-4.8v-.769c0-.336.346-.538.616-.371l3.182 1.969c.27.166.27.576 0 .742"/>
                            <path d="m.5 3 .04.87a2 2 0 0 0-.342 1.311l.637 7A2 2 0 0 0 2.826 14h10.348a2 2 0 0 0 1.991-1.819l.637-7A2 2 0 0 0 13.81 3H9.828a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 6.172 1H2.5a2 2 0 0 0-2 2m.694 2.09A1 1 0 0 1 2.19 4h11.62a1 1 0 0 1 .996 1.09l-.636 7a1 1 0 0 1-.996.91H2.826a1 1 0 0 1-.995-.91zM6.172 2a1 1 0 0 1 .707.293L7.586 3H2.19q-.362.002-.683.12L1.5 2.98a1 1 0 0 1 1-.98z"/>
                        </svg>
                    </small>
                </a>
            </div>
        <?php } ?>
    <?php } ?>
</div>