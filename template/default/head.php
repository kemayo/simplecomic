<?php

/*
available variables:
 $page: array of info about the page
 $config: general information about the site
*/

?><!DOCTYPE html5>
<html>
<head>
<title><?php echo $page['title']; ?></title>
<link rel="stylesheet" href="<?php echo BASEURL; ?>/template/<?php echo $config['template']; ?>/style.css" type="text/css" />
<link rel="alternate" type="application/atom+xml" title="<?php echo $config['sitename']?> updates" href="<?php echo BASEURL?>/feed" />
</head>
<body>
<h1>
    <a href="<?php echo BASEURL ?>"><?php echo $page['title']; ?></a>
</h1>
<?php if($page['breadcrumbs']) { ?>
<ul class="breadcrumbs"><?php foreach($page['breadcrumbs'] as $i => $crumb) {
    echo '<li>';
    if(isset($crumb[1])) {
        echo '<a href="', BASEURL, $crumb[1], '">';
    }
    echo $crumb[0];
    if(isset($crumb[1])) {
        echo '</a>';
    }
    echo '</li>';
} ?></ul>
<?php } ?>
<div class="maincontent">