<script>
    document.querySelectorAll('.jira[issue]').forEach(jiraSection => {
        let issue = jiraSection.getAttribute('issue')
        let mergeSections = Array.from(document.querySelectorAll(`.merge-request[issue='${issue}']`))
        if (!mergeSections.length)
            return;

        mergeSections.forEach(mergeSection => {
            if (mergeSections.length === 1)
                mergeSection.querySelector('.title').remove()

            mergeSection.querySelectorAll('svg').forEach(svg => {
                svg.setAttribute('height', Math.floor(parseInt(svg.getAttribute('height')) * 0.8));
                svg.setAttribute('width', Math.floor(parseInt(svg.getAttribute('width')) * 0.8));
            })

            mergeSection.style.setProperty('--size', "18px");
            mergeSection.style.setProperty('padding', '.5em 1em')
            jiraSection.querySelector('.slot').appendChild(mergeSection);
        })
        jiraSection.setAttribute('has_merge_requests', true)

    });

    (_ => {
        let hasOrphanMergeRequest = document.querySelector('#merge-request-list .merge-request');
        if (!hasOrphanMergeRequest)
            document.getElementById('merge-request-list').remove()
    })();

    document.getElementById('jira-issue-list').querySelector('.clear-cache-button').href += ',gitlab-merge-requests'

    <?php if (service('jira_gitlab_merger')['reorder'] ?? false) { ?>

        let issues = Array.from(document.querySelectorAll('.jira'));

        issues.sort((a, b) => {
            if (a.getAttribute('has_merge_requests') != b.getAttribute('has_merge_requests'))
                return a.getAttribute('has_merge_requests') ? -1 : 1

            return a.getAttribute('issue') > b.getAttribute('issue') ? 1 : -1
        })

        let issueList = document.getElementById('jira-issue-list');
        issues.forEach(x => issueList.appendChild(x))

    <?php } ?>
</script>