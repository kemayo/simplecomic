<?php
/*
available variables:
 $page: array of info about the page
 $rant: array of info about the rant
*/
template('head');
template('rant', $rant);
template('foot');
?>