<?php 
    $notesConfig = service('notes');
    $notesPath = $notesConfig['path'] ?? null;
?>

<?php if (is_file($notesPath)) { ?>
    <?php 
    $content = file_get_contents($notesPath)
    ?>
    <div class="flex flex-col card">
        <form class="flex flex-col gap-3 " method="POST" action="/resources/modules/notes/actions/update-notes.php">
            <b>Notes</b>
            <textarea name="text" rows="10" placeholder="No content for now"><?= $content ?></textarea>
            <button class="bg-blue-500 hover:bg-blue-600 transition-all rounded-md" >Save</button>
        </form>
    </div>
<?php } else { ?>
    <b>Could not read notes : <?= $notesPath ?? "???" ?> is not a file</b>
<?php } ?>
