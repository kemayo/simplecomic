<?php
/*
available variables:
 $page: array of info about the page
 $title: comic title
 $pub_date: timestamp that comic was published
 $nav: navigation info for comic
 $text: array of text related to comic
*/
?>
<div class="comic">
    <?php if (isset($title)) { ?>
    <h2><?php echo $title; ?></h2>
    <span class="date"><?php echo date('Y-m-d', $pub_date); ?></span>
    <?php template('comicnav', $nav); ?>
    <img src="<?php echo url('/comic/image/' . $comicid); ?>" alt="comic" <?php
    if($text['alt_text']) {
        echo 'title="'.$text['alt_text'].'"';
    }
    ?> />
    <?php template('comicnav', $nav); ?>
    <?php if($text['description']) {
        echo '<div class="description">';
        echo $text['description'];
        echo '</div>';
    } ?>
    <?php } else { ?>
    No comic.
    <?php } ?>
</div>