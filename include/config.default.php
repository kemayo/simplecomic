<?php

define('BASEURL', ''); // no trailing slash
define('DEBUG', false);

$config = array(
    'title' => "A Simple Comic",
    'comicpath' => "/comics",
    'template' => "default",
    'adminuser' => "admin",
    'adminpass' => "admin",
);

$db = new DataBase('localhost', 'database', 'username', 'password', DEBUG);

?>
