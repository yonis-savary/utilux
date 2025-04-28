<?php

function jiraCurl(string $url, array $getParams = [], array $portParams = [])
{
    $jiraService = service('jira');

    $serviceURL = env('UTILUX_JIRA_HOST', $jiraService['host']) ?? null;
    $url = $serviceURL . $url;

    return curl($url, $getParams, $portParams, [], function (&$curl) use (&$jiraService) {
        $username = env('UTILUX_JIRA_EMAIL', $jiraService['email']);
        $password = env('UTILUX_JIRA_TOKEN', $jiraService['token']);
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
        max-width: 65ch;
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
    }
</style>

<div class="flex flex-col gap-4">
    <div class="flex justify-between items-center">
        <div class="text-2xl font-bold">Jira - My Work</div>
        <?= getCacheTimestamps('jira-my-work') ?>
        <?= clearCacheButton('jira-my-work') ?>
    </div>

    <?php
    usort($myWork, fn($a, $b) => $a['key'] > $b['key']);
    foreach ($myWork as $issue) { ?>
        <a href="<?= preg_replace('/\\/rest.+/', '', env('UTILUX_JIRA_HOST', service('jira')['host'])) . '/browse/' . $issue['key']  ?>" class="flex flex-col card" target='_blank'>
            <div class="flex flex-row items-center">
                <p class="issue-title" title="<?= $issue['fields']['summary'] ?>">
                    <?= $issue['key'] ?> - <?= $issue['fields']['summary'] ?>
                </p>
                <small class="issue ml-auto <?= $issue['fields']['status']['statusCategory']['colorName'] ?>">
                    <?= $issue['fields']['status']['name'] ?>
                </small>
            </div>
        </a>
    <?php } ?>
</div>