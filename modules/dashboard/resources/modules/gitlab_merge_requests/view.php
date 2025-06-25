<?php

function gitlabCurl(string $url, array $getParams = [], array $portParams = [])
{
    $host = env('UTILUX_GITLAB_HOST');
    $token = env('UTILUX_GITLAB_TOKEN');

    if (!($host && $token))
    {
        stdlog('Please configure your gitlab credentials with utilux-config');
        return;
    }

    $url = $host . $url;

    $res = curl($url, $getParams, $portParams, ['PRIVATE-TOKEN' => $token]);
    return $res;
}

list($mergeRequests, $pipelines, $approvals) = cache(
    'gitlab-merge-requests',
    function() {

        $userId = gitlabCurl('/user')['id'];
        $mergeRequests = gitlabCurl('/merge_requests', ['assignee_id' => $userId, 'state' => 'opened', 'scope' => 'all']);

        $status = [];
        $approvals=[];
        foreach ($mergeRequests as $mr)
        {
            $mrIid = $mr['iid'];
            $status[$mrIid] ??= [];
            $approvals[$mrIid] ??= [];

            $pipelines = gitlabCurl('/projects/'.$mr['project_id'].'/merge_requests/'.$mr['iid'].'/pipelines');
            usort($pipelines, fn($a, $b) => $a['updated_at'] < $b['updated_at'] ? 1:-1);
            array_push($status[$mrIid],...$pipelines);

            $mrApprovals = gitlabCurl('/projects/'.$mr['project_id'].'/merge_requests/'.$mr['iid'].'/approvals');
            array_push($approvals[$mrIid], ...$mrApprovals['approved_by']);

        }

        return [$mergeRequests, $status, $approvals];
    }
);

$status = [
    'success' => 'passed !' ,
    'failed' => 'failed !',
    'running' => ' is running !'
];

$colors = [
    'failed' => '#ff5733',
    'success' => '#73f99f',
    'running' => '#00e8ff',
];

?>
<style>
    .approver-avatar
    {
        width: var(--size, 24px);
        height: var(--size, 24px);
        border-radius: 100%;
        box-shadow: 0px 0px 12px rgba(0, 255, 0);
    }
</style>
<div class="flex flex-col gap-4" id="merge-request-list">
    <div class="flex justify-between items-center">
        <div class="text-2xl font-bold">Gitlab Merge Requests</div>
        <?= getCacheTimestamps('gitlab-merge-requests') ?>
        <?= clearCacheButton('gitlab-merge-requests') ?>
    </div>

    <?php foreach ($mergeRequests as $mr) { ?>
        <a 
            href="<?= $mr['web_url'] ?>" 
            class="flex flex-col card merge-request" 
            target='_blank' 
            issue="<?= preg_replace("~.+, ?~", "", $mr['title']) ?>"
            title="<?= $mr['title'] ?>"
        >
            <div class="flex items-center gap-2">
                <div class="flex flex-col flex-grow-1">
                    <b class="title"><?= $mr['title'] ?></b>
                    <small class="flex items-center gap-3">
                        <?php if ($mr['draft'] === true) {  ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                            </svg>
                        <?php } ?>
                        <?= $mr['source_branch'] ?> → <?= $mr['target_branch'] ?>
                    </small>
                </div>
                <div class="flex">
                    <?php foreach (($approvals[$mr['iid']] ?? []) as $approver) { ?>
                        <img class="approver-avatar" src="<?= $approver['user']['avatar_url'] ?>" title="Approved by <?= $approver['user']['name'] ?>">
                    <?php } ?>
                </div>
                <div class="flex">
                    <?php if ($pipeline = $pipelines[$mr['iid']][0] ?? false) { ?>
                        <span title="Pipeline <?= $status[$pipeline['status']] ?? ": unknown status" ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="<?= $colors[$pipeline['status']] ?? "#f7a900" ?>" class="bi bi-play-circle" viewBox="0 0 16 16"
                                >
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                <path d="M6.271 5.055a.5.5 0 0 1 .52.038l3.5 2.5a.5.5 0 0 1 0 .814l-3.5 2.5A.5.5 0 0 1 6 10.5v-5a.5.5 0 0 1 .271-.445"/>
                            </svg>
                        </span>
                    <?php }  ?>
                </div>
            </div>
        </a>
    <?php } ?>
</div>