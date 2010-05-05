<?php
/*
available variables:
 $page: array of info about the page
 $comic: array of info about the comic
*/
template('head');
template('comic', $comic);
template('foot');
?>