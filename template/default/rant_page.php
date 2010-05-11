<?php
/*
available variables:
 $page: array of info about the page
 $rant: array of info about the rant
*/
template('rant_head');
template('rant', $rant);
template('rant_foot');
?>