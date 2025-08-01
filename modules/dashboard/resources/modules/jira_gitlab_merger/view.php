<script>
    document.querySelectorAll('.jira[issue],.jira-parent[issue]').forEach(jiraSection => {
        let issue = jiraSection.getAttribute('issue')
        let mergeSections = Array.from(document.querySelectorAll(`.merge-request[issue='${issue}']`))
        if (!mergeSections.length)
            return;

        const isParentIssue = jiraSection.classList.contains('jira-parent');

        mergeSections.forEach(mergeSection => {
            if (!isParentIssue)
                mergeSection.querySelector('.title')?.remove()

            mergeSection.querySelectorAll('svg').forEach(svg => {
                svg.setAttribute('height', Math.floor(parseInt(svg.getAttribute('height')) * 0.6));
                svg.setAttribute('width', Math.floor(parseInt(svg.getAttribute('width')) * 0.6));
            })

            mergeSection.style.setProperty('margin-left', '1em');

            console.log(jiraSection, jiraSection.classList.contains('jira-parent'))

            if (!isParentIssue)
                mergeSection.style.setProperty('--size', "18px");

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
</script>