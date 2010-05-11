<?php template('head'); ?>
<form action="" method="POST" enctype="multipart/form-data">
    <?php if(isset($chapterid) && $chapterid && $chapterid != 'new') { ?>
        <input name="chapterid" value=<?php echo $chapterid; ?> type="hidden" />
    <?php } ?>
    <label>Title</label>
    <input name="title" value="<?php echo isset($title) ? $title : ''; ?>" />

    <label>Slug</label>
    <input name="slug" value="<?php echo isset($slug) ? $slug : ''; ?>" />

	<label>Description</label>
	<textarea name="description" rows="8" cols="40"><?php echo htmlentities($description); ?></textarea>

	<input type="submit" name="submit" value="save" />
</form>

<?php template('foot'); ?>