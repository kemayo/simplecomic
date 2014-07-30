<?php

if (
    $_POST
    &&
    (
        !(isset($_POST['authtoken']))
        ||
        $_POST['authtoken'] != authtoken()
    )
) {
    die_error("Post authentication failed.");
}

$page->add_js('https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js');
$page->add_js('http://cdn.jquerytools.org/1.2.3/form/jquery.tools.min.js');
$page->add_js(template_path('admin.js'));
$page->add_css(template_path('cal.css'));
$page->add_breadcrumb("Admin", "admin/");

$valid_auth = sha1(config('adminuser').config('adminpass'));
if (config('adminuser') && (empty($_COOKIE['admin']) || $_COOKIE['admin'] !== $valid_auth)) {
    if (isset($_POST['adminuser'], $_POST['adminpass']) && sha1($_POST['adminuser'].$_POST['adminpass']) == $valid_auth) {
        setcookie('admin', $valid_auth);
        redirect("admin/", "Welcome");
    } else {
        header('HTTP/1.0 401 Unauthorized');
        ?>No access for you.<br><br>

        <form method="POST">
            <?php echo authtoken_input(); ?>
            Username: <input name="adminuser"><br>
            Password: <input name="adminpass" type="password"><br>
            <input type="submit" value="Login">
        </form>
        <?php
    }
    exit;
}

switch(isset($request[1]) ? $request[1] : '') {
case 'comic':
    // editing an individual comic
    $comic = array();
    if($request[2] != 'new') {
        $comic = $db->fetch_first("SELECT * FROM comics WHERE comicid = %d", $request[2]);
        if($comic) {
            $comic['text'] = fetch_text($comic['comicid']);
        }
    }
    if($comic && isset($_POST['delete'])) {
        $db->query("DELETE FROM comics WHERE comicid = %d", $request[2]);
        $db->query("DELETE FROM comics_text WHERE comicid = %d", $request[2]);
        redirect("admin/", "Comic {$request[2]} deleted");
    } elseif(isset($_POST['submit'])) {
        // this would need proper errors...
        $pub_date = strtotime(date('Y-m-d H:i:s', strtotime($_POST['pub_date'])));
        if(!$pub_date) {
            die_error("Bad date");
        }
        if (!empty($_POST['slug']) || is_numeric($_POST['slug']) || !preg_match('/^\w*$/', $_POST['slug'])) {
            die_error("Slug can't be just a number. That confuses things.");
        }
        if($comic) {
            $comicid = $comic['comicid'];
            if($db->quick("SELECT comicid FROM comics WHERE pub_date = %d AND comicid != %d", array($pub_date, $comicid))) {
                die_error("There is already a comic with that exact date. Please choose a different date.");
            }
            if($_POST['slug'] && $db->quick("SELECT comicid FROM comics WHERE slug = %s AND comicid != %d", array($_POST['slug'], $comicid))) {
                die_error("There is already a comic with that slug. Please choose a different one.");
            }
            $db->query("UPDATE comics SET title=%s, slug=%s, pub_date=%d, filename=%s, chapterid=%d WHERE comicid=%d",
                array($_POST['title'], $_POST['slug'], $pub_date, $_POST['filename'], $_POST['chapterid'], $comicid));
        } else {
            if($db->quick("SELECT comicid FROM comics WHERE pub_date = %d", $pub_date)) {
                die_error("There is already a comic with that exact date. Please choose a different date.");
            }
            if(isset($_POST['filename']) && $_POST['filename']) {
                if(!file_exists(BASEDIR . config('comicpath') . '/' . $_POST['filename'])) {
                    die_error("Comic file does not exist");
                }
                $filename = $_POST['filename'];
            } else {
                if($_FILES['comicfile']['error'] == UPLOAD_ERR_NO_FILE) {
                    die_error("No file uploaded");
                }
                $uploadpath = BASEDIR . config('comicpath') . '/' . basename($_FILES['comicfile']['name']);
                if(file_exists($uploadpath)) {
                    die_error("File exists");
                }
                if(!move_uploaded_file($_FILES['comicfile']['tmp_name'], $uploadpath)) {
                    die_error("Couldn't move uploaded file");
                }
                $filename = basename($_FILES['comicfile']['name']);
            }
            if(!$filename) {
                die_error("No filename");
            }
            $comicid = $db->insert_id(
                "INSERT INTO comics (title, slug, pub_date, filename, chapterid) VALUES (%s, %d, %s, %d)",
                array($_POST['title'], $_POST['slug'], $pub_date, $filename, $_POST['chapterid']));
        }
        $db->query(
            "REPLACE INTO comics_text (comicid, description, transcript, alt_text) VALUES (%d, %s, %s, %s)",
            array($comicid, $_POST['description'], $_POST['transcript'], $_POST['alt_text']));

        redirect("admin/comic/".$comicid, "Saving comic was successful.");
        die;
    }
    $comic['chapters'] = $db->fetch("SELECT * FROM chapters ORDER BY `order` DESC");
    template('admin_comic', $comic);
break;
case 'chapter':
    $chapter = false;
    if($request[2] != 'new') {
        $chapter = $db->fetch_first("SELECT * FROM chapters WHERE chapterid = %d", $request[2]);
        if($chapter) {
            $chapter['description'] = $db->quick("SELECT description FROM chapters_text WHERE chapterid = %d", $request[2]);
        }
    }
    if($chapter && isset($_POST['delete'])) {
        $remaining = $db->quick("SELECT COUNT(*) FROM comics WHERE chapterid = %d", $request[2]);
        if ($remaining > 0) {
            die_error("Can't delete a chapter while it contains comics. This one has {$remaining} left.");
        }
        $db->query("DELETE FROM chapters WHERE chapterid = %d", $request[2]);
        $db->query("DELETE FROM chapters_text WHERE chapterid = %d", $request[2]);
        redirect("admin/", "Chapter {$request[2]} deleted");
    } elseif(isset($_POST['submit'])) {
        if($chapter) {
            $chapterid = $chapter['chapterid'];
            if(in_array($request[3], array('up', 'down'))) {
                // for reference: the list on the admin page is "ORDER BY order DESC", so
                // "up" means increase the order, "down" means decrease the order
                switch($request[3]) {
                    case 'up':
                        // can we actually go higher?
                        $max = $db->quick("SELECT MAX(`order`) FROM chapters");
                        if($chapter['order'] == $max) {
                            continue;
                        }

                        // first: move the one next in the list up
                        $db->query("UPDATE chapters SET `order` = %d WHERE `order` = %d",
                            array($chapter['order'], $chapter['order'] + 1));
                        // now: move the selected chapter into the old one's place
                        $db->query("UPDATE chapters SET `order` = %d WHERE chapterid = %d",
                            array($chapter['order'] + 1, $chapterid));
                        break;
                    case 'down':
                        if($chapter['order'] == 0) {
                            continue;
                        }
                        $db->query("UPDATE chapters SET `order` = %d WHERE `order` = %d",
                            array($chapter['order'], $chapter['order'] - 1));
                        // now: move the selected chapter into the old one's place
                        $db->query("UPDATE chapters SET `order` = %d WHERE `chapterid` = %d",
                            array($chapter['order'] - 1, $chapterid));
                        break;
                }
                redirect("admin#chapter" . $chapter['chapterid']);
            }
            $status = 0;
            if(!empty($_POST['closed'])) {
                $status = STATUS_CLOSED;
            }
            if($db->quick("SELECT chapterid FROM chapters WHERE slug = %s AND chapterid != %d", array($_POST['slug'], $chapterid))) {
                die_error("There is already a chapter with that slug. Please choose a different one.");
            }
            $db->query("UPDATE chapters SET title = %s, slug = %s, status = %d WHERE chapterid = %d",
                array($_POST['title'], $_POST['slug'], $status, $chapterid));

            // A few sanity checks
            $count = $db->quick("SELECT COUNT(*) FROM chapters WHERE `order` = %d", $chapter['order']);
            if ($count > 1 || $chapter['order'] == 0) {
                $order = $db->quick("SELECT MAX(`order`) + 1 FROM chapters");
                $db->query("UPDATE chapters SET `order` = %d WHERE chapterid = %d", array($order, $chapterid));
            }
        } else {
            $order = $db->quick("SELECT MAX(`order`) + 1 FROM chapters");
            $chapterid = $db->insert_id(
                "INSERT INTO chapters (`title`, `slug`, `order`) VALUES (%s, %s, %d)",
                array($_POST['title'], $_POST['slug'], $order));
        }
        if(!$chapterid) {
            die_error("Error saving");
        }
        $db->query(
            "REPLACE INTO chapters_text (chapterid, description) VALUES (%d, %s)",
            array($chapterid, $_POST['description']));

        redirect("admin/chapter/".$chapterid, "Saving chapter was successful.");
        die;
    }
    template('admin_chapter', $chapter);
break;
case 'rant':
    $rant = false;
    if($request[2] != 'new') {
        $rant = $db->fetch_first("SELECT * FROM rants WHERE rantid = %d", $request[2]);
        if($rant) {
            $rant['text'] = $db->quick("SELECT text FROM rants_text WHERE rantid = %d", $request[2]);
        }
    }
    if($rant && isset($_POST['delete'])) {
        $db->query("DELETE FROM rants WHERE rantid = %d", $request[2]);
        $db->query("DELETE FROM rants_text WHERE rantid = %d", $request[2]);
        redirect("admin/", "Rant {$request[2]} deleted");
    } elseif(isset($_POST['submit']) || isset($_POST['preview'])) {
        $pub_date = strtotime(date('Y-m-d H:i:s', strtotime($_POST['pub_date'])));
        if(!$pub_date) {
            die_error("Bad date");
        }
        if(isset($_POST['preview']) && $_POST['preview']) {
            $rant = array(
                'rantid' => 'preview',
                'title' => $_POST['title'],
                'pub_date' => $pub_date,
                'text' => $_POST['text'],
                'preview' => true,
            );
        } else {
            if($rant) {
                $rantid = $rant['rantid'];
                $db->query("UPDATE rants SET title = %s, pub_date = %s WHERE rantid = %d",
                    array($_POST['title'], $pub_date, $rantid));
            } else {
                $rantid = $db->insert_id(
                    "INSERT INTO rants (`title`, `pub_date`) VALUES (%s, %s)",
                    array($_POST['title'], $pub_date));
            }
            if(!$rantid) {
                die_error("Error saving");
            }
            $db->query(
                "REPLACE INTO rants_text (rantid, text) VALUES (%d, %s)",
                array($rantid, $_POST['text']));

            redirect("admin/rant/".$rantid, "Saving rant was successful.");
            die;
        }
    }

    template('admin_rant', $rant);
    break;
default:
    // index
    $comics = $db->fetch("SELECT comicid, title, pub_date FROM comics ORDER BY pub_date DESC");
    $chapters = $db->fetch("SELECT * from chapters ORDER BY `order` DESC");
    $rants = $db->fetch("SELECT * from rants ORDER BY pub_date DESC");
    template('admin', array(
        'comics' => $comics,
        'chapters' => $chapters,
        'rants' => $rants,
    ));
break;
}
?>
