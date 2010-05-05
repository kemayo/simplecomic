<?php
/*
available variables:
 $page: array of info about the page
 $chapter: array of info about the chapter
 $comics: array of comics
*/
template('head');
?>
<div class="chapter">
    <h3><?php echo $chapter['title']; ?></h3>
    <div class="description">
        <?php echo $chapter['description']; ?>
    </div>
    <ul>
        <?php foreach($comics as $comic) { ?>
        <li><a href="<?php echo BASEURL, '/comic/', $comic['comicid']; ?>"><?php echo $comic['title'] ?></a></li>
        <?php } ?>
    </ul>
</div>
<?php
template('foot');
?>