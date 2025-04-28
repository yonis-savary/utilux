<?php
$mergeRequests = cache(
    'gitlab-merge-requests',
    fn() => gitlabCurl('/merge_requests', ['author_username' => 'yonis.savary', 'state' => 'opened'])
);
?>
<div class="flex flex-col gap-4">
    <div class="flex justify-between items-center">
        <div class="text-2xl font-bold">Gitlab Merge Requests</div>
        <?= getCacheTimestamps('gitlab-merge-requests') ?>
        <?= clearCacheButton('gitlab-merge-requests') ?>
    </div>

    <?php foreach ($mergeRequests as $mr) { ?>
        <a href="<?= $mr['web_url'] ?>" class="flex flex-col card" target='_blank'>
            <div class="flex items-center gap-2">
                <div class="flex flex-col flex-grow-1">
                    <b><?= $mr['title'] ?></b>
                    <small>
                        <?= $mr['source_branch'] ?>
                        →
                        <?= $mr['target_branch'] ?>
                    </small>
                </div>
                <div class="flex">
                    <?php if ($mr['draft'] === true) {  ?>
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                        </svg>
                    <?php } else if ($mr['state'] === 'merged') { ?>
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-sign-merge-left-fill" viewBox="0 0 16 16">
                            <path d="M9.05.435c-.58-.58-1.52-.58-2.1 0L.436 6.95c-.58.58-.58 1.519 0 2.098l6.516 6.516c.58.58 1.519.58 2.098 0l6.516-6.516c.58-.58.58-1.519 0-2.098zM7.25 6H6.034a.25.25 0 0 1-.192-.41l1.966-2.36a.25.25 0 0 1 .384 0l1.966 2.36a.25.25 0 0 1-.192.41H8.75v6h-1.5V8.823c-.551.686-1.229 1.363-1.88 2.015l-.016.016-.708-.708c.757-.756 1.48-1.48 2.016-2.196q.377-.499.588-.95z" />
                        </svg>
                    <?php } else if (($mr['approved'] ?? 'no') === 'yes') { ?>
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                            <path d="m10.97 4.97-.02.022-3.473 4.425-2.093-2.094a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05" />
                        </svg>
                    <?php } ?>
                </div>
            </div>
        </a>
    <?php } ?>
</div>