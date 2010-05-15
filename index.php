<?php

$start_time = microtime(true);

ob_start();

define('BASEDIR', dirname(__FILE__));

require_once 'include/db.php';
require_once 'include/page.php';
require_once 'include/common.php';
require_once 'include/config.php';

$request = isset($_REQUEST['q']) && $_REQUEST['q'] && $_REQUEST['q'] != '/' ? $_REQUEST['q'] : 'index';
$request = array_values(array_filter(explode('/', $request)));

$page = new Page();
$page->title = $config['title'];
$page->add_css("template/{$config['template']}/style.css");
$page->set_start_time($start_time);

switch($request[0]) {
    case 'index':
        // frontpage
        $comic = $db->fetch_first("SELECT * FROM comics WHERE pub_date <= UNIX_TIMESTAMP() ORDER BY pub_date DESC LIMIT 1");
        $comic['text'] = fetch_text($comic['comicid']);
        $comic['nav'] = fetch_navigation($comic);
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
                $file = BASEDIR . $config['comicpath'] . '/' . $comic['filename'];
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
        $comic = $db->fetch_first("SELECT * FROM comics WHERE comicid = %d AND pub_date <= UNIX_TIMESTAMP()", $request[1]);
        if(!$comic) {
            redirect("/");
        }
        $comic['text'] = fetch_text($comic['comicid']);
        $comic['nav'] = fetch_navigation($comic);
        template('comic_page', array('comic'=>$comic));
        break;
    case 'chapters':
        // chapter listing
        $chapters = $db->fetch("SELECT * FROM chapters ORDER BY `order` DESC");
        template('chapter_list', array('chapters'=>$chapters));
        break;
    case 'chapter':
        if(isset($request[1])) {
            // specific chapter
            $slug = $request[1];
            $chapter = $db->fetch_first("SELECT * FROM chapters c LEFT JOIN chapters_text t ON c.chapterid = t.chapterid  WHERE c.slug=%s", $slug);
            if(!$chapter) {
                redirect("/chapters");
            }
            $comics = $db->fetch("SELECT * FROM comics WHERE chapterid=%d AND pub_date <= UNIX_TIMESTAMP() ORDER BY pub_date DESC", $chapter['chapterid']);
            template('chapter', array(
                'chapter' => $chapter,
                'comics' => $comics,
            ));
        } else {
            redirect("/chapters");
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
                redirect("/rants");
            }
            template('rant_page', array(
                'rant' => $rant,
            ));
        } else {
            redirect("/chapters");
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
