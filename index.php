<?php

$start_time = microtime(true);

ob_start();

define('BASEDIR', dirname(__FILE__));

require_once 'include/db.php';
require_once 'include/page.php';
require_once 'include/common.php';
require_once 'include/config.php';

if(DEBUG) {
    ini_set('display_errors',1); 
    error_reporting(E_ALL);
}

define('STATUS_CLOSED', 1);

$template_fallbacks = array(
    // 'template_name' => 'template_to_use_instead'
    'admin_head.php' => 'head.php',
    'admin_foot.php' => 'foot.php',
    'archive_head.php' => 'head.php',
    'archive_foot.php' => 'foot.php',
    'comic_head.php' => 'head.php',
    'comic_foot.php' => 'foot.php',
    'chapter_head.php' => 'head.php',
    'chapter_foot.php' => 'foot.php',
    'rant_head.php' => 'head.php',
    'rant_foot.php' => 'foot.php',
);

$request = isset($_REQUEST['q']) && $_REQUEST['q'] && $_REQUEST['q'] != '/' ? $_REQUEST['q'] : 'index';
$request = array_values(array_filter(explode('/', $request)));

$page = new Page();
$page->title = config('title');
// $page->add_css("template/{config('template')}/style.css");
$page->add_css(template_path('style.css'));
// $page->add_js('https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js');
$page->add_js(template_path('nav.js'));
$page->set_start_time($start_time);

switch($request[0]) {
    case 'index':
        // frontpage
        $selection_style = config('frontpage_comic', 'latest');
        $page->debug('selection_style', $selection_style);
        switch($selection_style) {
            case 'first':
                $comic = $db->fetch_first("SELECT * FROM comics WHERE pub_date <= UNIX_TIMESTAMP() ORDER BY pub_date ASC LIMIT 1");
                break;
            case 'first-of-latest-day':
                $latest = $db->quick("SELECT pub_date FROM comics WHERE pub_date <= UNIX_TIMESTAMP() ORDER BY pub_date DESC LIMIT 1");
                $midnight = strtotime(date('Y-m-d', $latest));
                $comic = $db->fetch_first("SELECT * FROM comics WHERE pub_date >= %d AND pub_date <= UNIX_TIMESTAMP() ORDER BY pub_date ASC LIMIT 1", $midnight);
                break;
            case 'latest':
            default:
                $comic = $db->fetch_first("SELECT * FROM comics WHERE pub_date <= UNIX_TIMESTAMP() ORDER BY pub_date DESC LIMIT 1");
                break;
        }
        if($comic) {
            $comic['text'] = fetch_text($comic['comicid']);
            $comic['nav'] = fetch_navigation($comic);
        }
        $rant = $db->fetch_first("SELECT * FROM rants r LEFT JOIN rants_text t ON r.rantid = t.rantid WHERE pub_date <= UNIX_TIMESTAMP() ORDER BY pub_date DESC LIMIT 1");
        template('index', array(
            'comic' => $comic,
            'rant' => $rant,
            'updates' => fetch_recent_updates(),
        ));
        break;
    case 'comic':
        if($request[1] == 'image') {
            // image display!
            $comic = $db->fetch_first("SELECT * FROM comics WHERE comicid = %d AND pub_date <= UNIX_TIMESTAMP()", $request[2]);
            if($comic) {
                $file = BASEDIR . config('comicpath') . '/' . $comic['filename'];
                if(file_exists($file)) {
                    $mime = array(
                        'gif'  => 'image/gif',
                        'jpg'  => 'image/jpeg',
                        'jpe'  => 'image/jpeg',
                        'jpeg' => 'image/jpeg',
                        'png'  => 'image/png',
                    );
                    $filetime = filemtime($file);
                    $path = pathinfo($file);
                    $ext = strtolower($path['extension']);
                    $type = $mime[$ext];
                    header("Content-Type: {$type}");
                    $content_length = filesize($file);
                    if($content_length !== false) {
                        header("Content-Length: {$content_length}");
                    }

                    // Allow HTTP 1.1 compliant browsers to cache based on modifcation time
                    if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
                        $mod = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
                        if ($mod !== -1 && $mod >= $filetime) {
                            header('HTTP/1.1 304 Not Modified');
                            die;
                        }
                    }
                    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $filetime) . ' GMT');
                    // Suggest default filename?
                    // header('Content-Disposition: filename=' . urlencode($comic['filename']));
                    readfile($file);
                    die;
                }
            }
            header('HTTP/1.1 404 Not Found');
            echo 'No such comic file';
            die;
        }
        // comic page
        if (!is_numeric($request[1])) {
            $comic = $db->fetch_first("SELECT * FROM comics WHERE slug = %s AND pub_date <= UNIX_TIMESTAMP()", $request[1]);
        } else {
            $comic = $db->fetch_first("SELECT * FROM comics WHERE comicid = %d AND pub_date <= UNIX_TIMESTAMP()", $request[1]);
        }
        if(!$comic) {
            redirect("");
        }
        $comic['text'] = fetch_text($comic['comicid']);
        $comic['nav'] = fetch_navigation($comic);
        template('comic_page', array('comic'=>$comic));
        break;
    case 'archive':
        // full strip listing
        $query = "SELECT c.*, ch.title AS chapter_title, ch.slug AS chapter_slug, ct.description AS chapter_description FROM comics c LEFT JOIN chapters ch ON c.chapterid = ch.chapterid LEFT JOIN chapters_text ct ON c.chapterid = ct.chapterid WHERE c.pub_date <= UNIX_TIMESTAMP() ORDER BY ";
        switch (config('archive_order', 'date')) {
            case 'chapter':
                $query .= "ch.order ASC, ";
            case 'date':
                $query .= "c.pub_date ASC";
                break;
        }
        $comics = $db->fetch($query);
        template('archive', array('comics' => $comics));
        break;
    case 'chapters':
        // chapter listing
        $chapters = $db->fetch("SELECT * FROM chapters ORDER BY `order` ASC");
        template('chapter_list', array('chapters'=>$chapters));
        break;
    case 'chapter':
        if(isset($request[1])) {
            // specific chapter
            $slug = $request[1];
            $chapter = $db->fetch_first("SELECT c.*, t.description FROM chapters c LEFT JOIN chapters_text t ON c.chapterid = t.chapterid  WHERE c.slug=%s", $slug);
            if(!$chapter) {
                redirect("chapters");
            }
            $comics = $db->fetch("SELECT * FROM comics WHERE chapterid=%d AND pub_date <= UNIX_TIMESTAMP() ORDER BY pub_date ASC", $chapter['chapterid']);
            template('chapter', array(
                'chapter' => $chapter,
                'comics' => $comics,
            ));
        } else {
            redirect("chapters");
        }
        break;
    case 'rants':
        $rants = $db->fetch("SELECT * FROM rants ORDER BY pub_date DESC");
        template('rant_list', array('rants'=>$rants));
        break;
    case 'rant':
        if(isset($request[1])) {
            // specific rant
            $rant = $db->fetch_first("SELECT * FROM rants r LEFT JOIN rants_text t ON r.rantid = t.rantid WHERE r.rantid=%d", $request[1]);
            if(!$rant) {
                redirect("rants");
            }
            template('rant_page', array(
                'rant' => $rant,
            ));
        } else {
            redirect("chapters");
        }
        break;
    case 'admin':
        require 'include/admin.php';
        break;
    case 'feed':
        template('feed', array(
            'updates' => fetch_recent_updates(true),
        ));
        break;
    default:
        if(template('page_'.$request[0])) {
            // This is *so* relying on side-effects. :P
            // (template returns true if a valid template was found and displayed)
            return;
        }
        // 404
        header("HTTP/1.0 404 Not Found");
        echo '404';
        break;
}

?>
