<?php
/*
available variables:
 $page: array of info about the page
 $comic: array of info about the comic
*/
template('comic_head');
template('comic', $comic);
template('comic_foot');
?>