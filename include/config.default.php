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

$template_fallbacks = array(
    // 'template_name' => 'template_to_use_instead'
    'admin_head' => 'head',
    'admin_foot' => 'foot',
    'archive_head' => 'head',
    'archive_foot' => 'foot',
    'comic_head' => 'head',
    'comic_foot' => 'foot',
    'chapter_head' => 'head',
    'chapter_foot' => 'foot',
    'rant_head' => 'head',
    'rant_foot' => 'foot',
);

$db = new DataBase('localhost', 'database', 'username', 'password', DEBUG);

?>
