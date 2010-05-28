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
    <?php if(isset($nav['next'])) { ?><a href="<?php echo url('comic/'.$nav['next']); ?>"><?php } ?>
    <img src="<?php echo url('comic/image/' . $comicid); ?>" alt="comic" <?php
    if($text['alt_text']) {
        echo 'title="'.$text['alt_text'].'"';
    }
    ?> />
    <?php if(isset($nav['next'])) { ?></a><?php } ?>
    <?php template('comicnav', $nav); ?>
    <?php
    if($text['description']) {
        echo '<div class="description"><h4>Description</h4>';
        echo $text['description'];
        echo '</div>';
    }
    if($text['transcript']) {
        echo '<div class="transcript"><h4>Transcript</h4>';
        echo $text['transcript'];
        echo '</div>';
    }
    ?>
    <?php } else { ?>
    No comic.
    <?php } ?>
</div>