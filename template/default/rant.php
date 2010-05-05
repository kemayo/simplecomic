<?php
/*
available variables:
 $page: array of info about the page
 $title: rant title
 $pub_date: timestamp that rant was published
 $nav: navigation info for rant
 $text: text of rant
*/
?>
<div class="rant">
    <?php if (isset($title)) { ?>
    <h2><?php echo $title; ?></h2>
    <span class="date"><?php echo date('Y-m-d H:i', $pub_date); ?></span>
    <?php
    echo '<div class="description">';
    echo $text;
    echo '</div>';
    ?>
    <?php } else { ?>
    No rant.
    <?php } ?>
</div>