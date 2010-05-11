<?php

/*
available variables:
 $page: array of info about the page
 $config: general information about the site
*/

template('frame_top');
?>
<h1>
    <a href="<?php echo url('/'); ?>"><?php echo $page['title']; ?></a>
</h1>
<?php if($page['breadcrumbs']) { ?>
<ul class="breadcrumbs"><?php foreach($page['breadcrumbs'] as $i => $crumb) {
    echo '<li>';
    if(isset($crumb[1])) {
        echo '<a href="', url($crumb[1]), '">';
    }
    echo $crumb[0];
    if(isset($crumb[1])) {
        echo '</a>';
    }
    echo '</li>';
} ?></ul>
<?php } ?>
<div class="maincontent">