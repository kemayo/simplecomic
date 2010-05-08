<?php

define('BASEURL', ''); // no trailing slash
define('DEBUG', false);

$config = array(
    'title' => "A Simple Comic",
    'author' => "Author Name",
    'author_site' => false, // author's url, if different to the comic's
    'comicpath' => "/comics",
    'template' => "default",
    'full_feed' => true, // means that comic images and rants appear in the feed
    'pretty_urls' => true,
    'adminuser' => "admin",
    'adminpass' => "admin",
);

$db = new DataBase('localhost', 'database', 'username', 'password', DEBUG);

?>
