<?php
/*
available variables:
 $page: info about the page
 $chapter: array of info about the chapter
 $comics: array of comics
*/
template('archive_head');
$current_chapter = -1;
?>
<div class="archive">
    <h3>Archive</h3>
    <ul>
        <?php
        foreach($comics as $comic) {
            if($comic['chapterid'] && $comic['chapterid'] != $current_chapter) {
                $current_chapter = $comic['chapterid'];
                ?><li class="chapter">
                    <a href="<?php echo url('chapter/'.$comic['chapter_slug']); ?>"><?php echo $comic['chapter_title']; ?></a>
                </li><?php
            }
        ?>
        <li class="comic">
            <a href="<?php echo url($comic); ?>">
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