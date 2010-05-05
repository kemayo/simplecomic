<?php
switch($request[1]) {
case 'comic':
    // editing an individual comic
    $comic = array();
    if($request[2] != 'new') {
        $comic = $db->fetch_first("SELECT * FROM comics WHERE comicid = %d", $request[2]);
        if($comic) {
            $comic['text'] = fetch_text($comic['comicid']);
        }
    }
    if(isset($_POST['submit'])) {
        // this would need proper errors...
        $pub_date = strtotime(date('Y-m-d H:i:s', strtotime($_POST['pub_date'])));
        if(!$pub_date) {
            die("Bad date");
        }
        if($comic) {
            $comicid = $comic['comicid'];
            $db->query("UPDATE comics SET title=%s, pub_date=%d, filename=%s, chapterid=%d WHERE comicid=%d",
                array($_POST['title'], $pub_date, $_POST['filename'], $_POST['chapterid'], $comicid));
        } else {
            if(isset($_POST['filename']) && $_POST['filename']) {
                if(!file_exists(BASEDIR.$config['comicpath'].'/'.$_POST['filename'])) {
                    die("Comic file does not exist");
                }
                $filename = $_POST['filename'];
            } else {
                if($_FILES['comicfile']['error'] == UPLOAD_ERR_NO_FILE) {
                    die("No file uploaded");
                }
                $uploadpath = BASEDIR.$config['comicpath'].'/'.basename($_FILES['comicfile']['name']);
                if(file_exists($uploadpath)) {
                    die("File exists");
                }
                if(!move_uploaded_file($_FILES['comicfile']['tmp_name'], $uploadpath)) {
                    die("Couldn't move uploaded file");
                }
                $filename = basename($_FILES['comicfile']['name']);
            }
            if(!$filename) {
                die("No filename");
            }
            $comicid = $db->insert_id(
                "INSERT INTO comics (title, pub_date, filename, chapterid) VALUES (%s, %d, %s, %d)",
                array($_POST['title'], $pub_date, $filename, $_POST['chapterid']));
        }
        $db->query(
            "REPLACE INTO comics_text (comicid, description, transcript, alt_text) VALUES (%d, %s, %s, %s)",
            array($comicid, $_POST['description'], $_POST['transcript'], $_POST['alt_text']));

        redirect("/admin/comic/".$comicid);
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
    if(isset($_POST['submit'])) {
        if($chapter) {
            $chapterid = $chapter['chapterid'];
            $db->query("UPDATE chapters SET title = %s, slug = %s WHERE chapterid = %d",
                array($_POST['title'], $_POST['slug'], $chapterid));
        } else {
            $order = $db->quick("SELECT MAX(order) + 1 FROM chapters");
            $chapterid = $db->insert_id(
                "INSERT INTO chapters (`title`, `slug`, `order`) VALUES (%s, %s, %d)",
                array($_POST['title'], $_POST['slug'], $order));
        }
        if(!$chapterid) {
            die("Error saving");
        }
        $db->query(
            "REPLACE INTO chapters_text (chapterid, description) VALUES (%d, %s)",
            array($chapterid, $_POST['description']));

        redirect("/admin/chapter/".$chapterid);
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
    if(isset($_POST['submit'])) {
        $pub_date = strtotime(date('Y-m-d H:i:s', strtotime($_POST['pub_date'])));
        if(!$pub_date) {
            die("Bad date");
        }
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
            die("Error saving");
        }
        $db->query(
            "REPLACE INTO rants_text (rantid, text) VALUES (%d, %s)",
            array($rantid, $_POST['text']));

        redirect("/admin/rant/".$rantid);
        die;
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
