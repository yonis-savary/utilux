<?php

$repositories = cache('git-branches', function () {
    $repositoriesData = [];

    $repositories = service('git')['repositories'];
    foreach ($repositories as $repository) {

        $currentBranch = null;
        $branches = command("git branch", $repository);
        $branches = explode("\n", $branches);
        $branches = array_map(function ($branch) use (&$currentBranch) {
            $cleanBranchName = trim(preg_replace('/^[* ]+/', '', $branch));
            if (str_starts_with($branch, '*'))
                $currentBranch = $cleanBranchName;
            return $cleanBranchName;
        }, $branches);
        $branches = array_values(array_filter(array_diff($branches, ['main', 'master'])));

        $repoData = [
            'name' => basename($repository),
            'directory' => $repository,
            'checked_out_branch' => $currentBranch,
            'branches' => $branches
        ];

        $repositoriesData[] = $repoData;
    }
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

    <?php foreach ($repositories as $repo) {
        if (!count(array_diff($repo['branches'], ['main', 'develop', 'master'])))
            continue;
    ?>
        <div class="flex flex-col card">
            <div class="flex items-center gap-5 justify-between">
                <b><?= $repo['name'] ?></b>
                <a href="/resources/actions/open-code.php?directory=<?= urlencode($repo['directory']) ?>">
                    <small><?= $repo['directory'] ?></small>
                </a>
            </div>
            <details <?= count($repo['branches']) > 2 ? 'open' : '' ?>>
                <summary>Local Branches (<?= count($repo['branches']) ?>)</summary>
                <ul class="list-disc pl-5">
                    <?php foreach ($repo['branches'] as $branch) { ?>
                        <li class="<?= $repo['checked_out_branch'] == $branch ? 'font-bold' : '' ?>"><?= supportIssueNameInBranchName($branch) ?></li>
                    <?php } ?>
                </ul>
            </details>
        </div>
    <?php } ?>
</div>