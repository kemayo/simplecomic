<?php template('admin_head'); ?>
<form action="" method="POST" enctype="multipart/form-data">
    <?php echo authtoken_input(); ?>
    <?php if(isset($chapterid) && $chapterid && $chapterid != 'new') { ?>
        <input name="chapterid" value=<?php echo $chapterid; ?> type="hidden" />
    <?php } ?>
    <label>Title</label>
    <input name="title" value="<?php echo isset($title) ? $title : ''; ?>" />

    <label>Slug</label>
    <input name="slug" pattern="[\w\-]*" value="<?php echo isset($slug) ? $slug : ''; ?>" />

    <label>Description</label>
    <textarea name="description" rows="8" cols="40"><?php echo htmlentities($description); ?></textarea>

    <label>Closed</label>
    <input name="closed" type="checkbox" value="1"<?php
    if($status == STATUS_CLOSED) {
        echo ' checked="checked"';
    }
    ?>>

    <div class="submit-block">
        <input type="submit" name="submit" value="Save" />
        <?php if(isset($chapterid) && $chapterid && $chapterid != 'new') { ?>
        <button name="delete" class="delete" value="1">Delete</button>
        <?php } ?>
    </div>
</form>

<?php template('admin_foot'); ?>