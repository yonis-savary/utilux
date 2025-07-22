<?php

function jiraCurl(string $url, array $getParams = [], array $portParams = [])
{
    $host = env('UTILUX_JIRA_HOST');
    $username = env('UTILUX_JIRA_EMAIL');
    $password = env('UTILUX_JIRA_TOKEN');

    if (!($username && $password && $host)) {
        stdlog("Please configure your jira credentials with utilux-config");
        return [];
    }

    $url = $host . $url;

    return curl($url, $getParams, $portParams, [], function (&$curl) use ($username, $password) {
        curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
    });
}

function getJiraIssueLink($issue)
{
    return preg_replace('/\\/rest.+/', '', env('UTILUX_JIRA_HOST', service('jira')['host'] ?? false)) . '/browse/' . $issue['key'];
}

$myWork = cache(
    'jira-my-work',
    fn() => jiraCurl('/search/jql', ['fields' => 'status,summary,parent', 'jql' => 'assignee = currentUser() AND status NOT IN (Annule, Annulé, Clôturé, Done, Finished, "Résolu Automatiquement", Terminé) ORDER BY priority DESC, created DESC'])['issues']
);


$groupedMyWork = [];


foreach ($myWork as $issue) {
    $parent = $issue['fields']['parent'] ?? [];
    $parentKey = $parent['key'] ?? '???';
    $groupedMyWork[$parentKey] ??= [
        'parent' => $parent,
        'issues' => []
    ];

    $groupedMyWork[$parentKey]['issues'][] = $issue;
}

$groupedMyWork = array_values($groupedMyWork);

?>

<style>
    .issue {
        font-weight: bolder;
        padding: .25em .5em;
        border-radius: 0px 4px 4px 0px;
        font-size: .75em;
        width: 10em;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 200ms ease;
        padding-left: 2em;
        clip-path: polygon(18% 0, 100% 0, 100% 100%, 0% 100%);
    }


    .jira:hover .issue {
        padding-left: 0em;
        clip-path: polygon(0% 0, 100% 0, 100% 100%, 0% 100%);
    }

    .issue.yellow {
        background: #E9F2FF;
        color: #0055CC;
    }

    .issue.blue-gray {
        background: #091E42;
        color: #527cc2
    }

    .issue.green {
        background: #09420d;
        color: #4dcb57
    }

    .issue-title {
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
        max-width: 60ch;
    }

    .issue-status {
        white-space: nowrap;
    }

    .parent-title {
        margin: 1em 0;
    }

    .jira-parent > .slot:empty {
        display: none;
    }
    .jira-parent > .slot {
        padding-bottom: 1em;
    }

    .jira .card {
        padding: 0;
        border: none
    }

    .jira .issue-title {
        padding: .4em 1em;
    }

    .issue-ready-for-qa {
        opacity: .3;
        transition: all 200ms ease;
        
    }

    .issue-ready-for-qa a 
    {
        padding: .3em 1em;
    }
    .issue-ready-for-qa:hover { opacity: 1; }

</style>

<div class="flex flex-col gap-4" id="jira-issue-list">
    <div class="flex justify-between items-center">
        <div class="text-2xl font-bold">Jira - My Work</div>
        <?= getCacheTimestamps('jira-my-work') ?>
        <?= clearCacheButton('jira-my-work') ?>
    </div>

    <?php
    usort($groupedMyWork, fn($a, $b) => ($a['parent']['key'] ?? '???') > ($b['parent']['key'] ?? '???') ? 1 : -1);
    foreach ($groupedMyWork as $group) { ?>
        <div class="flex flex-col jira-parent issue="<?= $group['parent']['key'] ?? '?' ?>">
            <?php if ($group['parent']['key'] ?? false) { ?>
                <a
                    href="<?= getJiraIssueLink($group['parent']) ?>"
                    class="parent-title"
                    title="<?= $group['parent']['fields']['summary'] ?>"
                >
                    <b><?= $group['parent']['key'] ?></b> - <?= $group['parent']['fields']['summary'] ?>
                </a>
            <?php } ?>
            <div class="slot"></div>
            <div class="flex flex-col gap-3">

                <?php
                $issues = $group['issues'];
                usort($issues, fn($a, $b) => $a['key'] > $b['key'] ? 1 : -1);
                foreach ($issues as $issue) { ?>
                    <div class="flex flex-col jira issue-<?= preg_replace('~[^a-z]~','-', strtolower($issue['fields']['status']['name'])) ?> "" issue="<?= $issue['key'] ?>">
                        <a href="<?= getJiraIssueLink($issue)  ?>" class="flex flex-col card" target='_blank'>
                            <div class="flex flex-row">
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
        </div>

    <?php } ?>
</div>