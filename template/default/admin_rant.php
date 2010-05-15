<?php template('admin_head'); ?>

<form action="" method="POST" enctype="multipart/form-data">
    <?php if(isset($rantid) && $rantid && $rantid != 'new') { ?>
        <input name="rantid" value=<?php echo $rantid; ?> type="hidden" />
    <?php } ?>
    <label>Title</label>
    <input name="title" value="<?php echo isset($title) ? $title : ''; ?>" />

    <label>Date</label>
    <input name="pub_date" class="datetime" value="<?php echo date('Y-m-d H:i:s', isset($pub_date) ? $pub_date : time()); ?>" />
    <small>YYYY-MM-DD HH:MM:SS. Rants dated in the future will not be published until that time.</small>

    <label>Text</label>
    <textarea name="text" rows="8" cols="40"><?php echo htmlentities($text); ?></textarea>

    <input type="submit" name="submit" value="save" />
</form>

<?php template('admin_foot'); ?>
