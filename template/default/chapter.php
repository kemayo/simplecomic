<?php
/*
available variables:
 $page: array of info about the page
 $chapter: array of info about the chapter
 $comics: array of comics
*/
template('chapter_head');
?>
<div class="chapter">
    <h3><?php echo $chapter['title']; ?></h3>
    <?php if($chapter['description']) { ?>
    <div class="description">
        <?php echo $chapter['description']; ?>
    </div>
    <?php } ?>
    <ul>
        <?php foreach($comics as $comic) { ?>
        <li><a href="<?php echo url($comic); ?>"><?php echo $comic['title'] ?></a></li>
        <?php } ?>
    </ul>
</div>
<?php
template('chapter_foot');
?>