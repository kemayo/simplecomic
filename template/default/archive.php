<?php
/*
available variables:
 $page: info about the page
 $chapter: array of info about the chapter
 $comics: array of comics
*/
template('archive_head');
?>
<div class="archive">
    <h3>Archive</h3>
    <ul>
        <?php foreach($comics as $comic) { ?>
        <li>
            <a href="<?php echo url('comic/'.$comic['comicid']); ?>">
                <?php echo date('Y-m-d', $comic['pub_date']); ?>:
                <?php echo $comic['title']; ?>
            </a>
        </li>
        <?php } ?>
    </ul>
</div>
<?php
template('archive_foot');
?>