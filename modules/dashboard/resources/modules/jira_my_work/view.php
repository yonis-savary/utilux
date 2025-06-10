<?php

function jiraCurl(string $url, array $getParams = [], array $portParams = [])
{
    $host = env('UTILUX_JIRA_HOST');
    $username = env('UTILUX_JIRA_EMAIL');
    $password = env('UTILUX_JIRA_TOKEN');

    if (!($username && $password && $host))
    {
        stdlog("Please configure your jira credentials with utilux-config");
        return [];
    }

    $url = $host . $url;

    return curl($url, $getParams, $portParams, [], function (&$curl) use ($username, $password) {
        curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
    });
}

$myWork = cache(
    'jira-my-work',
    fn() => jiraCurl('/search/jql', ['fields' => 'status,summary', 'jql' => 'assignee = currentUser() AND status NOT IN (Annule, Annulé, Clôturé, Done, Finished, "Résolu Automatiquement", Terminé) ORDER BY priority DESC, created DESC'])['issues']
);
?>

<style>
    .issue {
        font-weight: bolder;
        padding: .25em .5em;
        border-radius: 4px;
        font-size: .75em;
        max-width: max-content;
    }

    .issue.yellow {
        background: #E9F2FF;
        color: #0055CC;
    }

    .issue.blue-gray {
        background: #091E42;
        color: #44546F
    }

    .issue-title {
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
    }

    .issue-status 
    {
        white-space: nowrap;
    }
</style>

<div class="flex flex-col gap-4" id="jira-issue-list">
    <div class="flex justify-between items-center">
        <div class="text-2xl font-bold">Jira - My Work</div>
        <?= getCacheTimestamps('jira-my-work') ?>
        <?= clearCacheButton('jira-my-work') ?>
    </div>

    <?php
    usort($myWork, fn($a, $b) => $a['key'] > $b['key'] ? 1:-1);
    foreach ($myWork as $issue) { ?>
        <div class="flex flex-col jira" issue="<?= $issue['key'] ?>">
            <a href="<?= preg_replace('/\\/rest.+/', '', env('UTILUX_JIRA_HOST', service('jira')['host'] ?? false)) . '/browse/' . $issue['key']  ?>" class="flex flex-col card" target='_blank'>
                <div class="flex flex-row items-center">
                    <p class="issue-title" title="<?= $issue['fields']['summary'] ?>">
                        <b><?= $issue['key'] ?></b> - <?= $issue['fields']['summary'] ?>
                    </p>
                    <small class="issue issue-status ml-auto <?= $issue['fields']['status']['statusCategory']['colorName'] ?>">
                        <?= $issue['fields']['status']['name'] ?>
                    </small>
                </div>
            </a>
            <div class="slot"></div>
        </div>
    <?php } ?>
</div>