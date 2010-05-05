<?php

// Code taken from the PHP.net section on disabling magic_quotes.
// Makes sure that magic_quotes is, effectively, always off.
if (get_magic_quotes_gpc()) {
    function stripslashes_deep($value)
    {
        $value = is_array($value) ?
            array_map('stripslashes_deep', $value) :
            stripslashes($value);
        return $value;
    }
    $_POST = array_map('stripslashes_deep', $_POST);
    $_GET = array_map('stripslashes_deep', $_GET);
    $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
    $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
}

/**
 * Includes a template
 *
 * @param string $name
 * @param array $vars
 * @return void
 */
function template($name, $vars = false) {
    global $config, $page;
    if($vars) {
        extract($vars);
    }
    $template = BASEDIR . '/template/' . $config['template'] . '/' . $name . '.php';
    if(file_exists($template)) {
        include $template;
    } else {
        include BASEDIR . '/template/default/' . $name . '.php';
    }
}

function redirect($where) {
    if(!preg_match("/^[^:]+:\/\//", $where)) {
        $where = BASEURL . $where;
    }
    ob_end_clean();
    if(DEBUG) {
        echo '<p>Redirect to: <a href="', $where, '">', $where, '</a></p>';
        template('foot');
        die;
    }
    header("Location:{$where}");
    die;
}

function debug($key, $details) {
    if(!DEBUG) {
        // no point in accumulating
        return;
    }
    global $page;
    $page['debug'][$key] = $details;
}

function ob_template($name, $vars = false) {
    ob_start();
    template($name, $vars);
    return ob_get_clean();
}

function fetch_navigation($comic) {
    global $db;
    $comics_today = $db->quick("SELECT COUNT(*) FROM comics WHERE pub_date = %d", $comic['pub_date']);
    return array(
        'current' => $comic['comicid'],
        'prev' => $db->quick("SELECT comicid FROM comics WHERE pub_date < %d AND pub_date <= UNIX_TIMESTAMP() ORDER BY pub_date DESC LIMIT 1", $comic['pub_date']),
        'next' => $db->quick("SELECT comicid FROM comics WHERE pub_date > %d AND pub_date <= UNIX_TIMESTAMP() ORDER BY pub_date ASC LIMIT 1", $comic['pub_date']),
        'first' => $db->quick("SELECT comicid FROM comics WHERE pub_date <= UNIX_TIMESTAMP() ORDER BY pub_date ASC LIMIT 1"),
        'last' => $db->quick("SELECT comicid FROM comics WHERE pub_date <= UNIX_TIMESTAMP() ORDER BY pub_date DESC LIMIT 1"),
    );
}

function fetch_text($comicid) {
    global $db;
    $text = $db->fetch_first("SELECT description, transcript, alt_text FROM comics_text WHERE comicid = %d", $comicid);
    if(!$text) {
        return array(
            'description' => '',
            'transcript' => '',
            'alt_text' => '',
        );
    }
    return $text;
}

function fetch_recent_updates() {
    global $db;
    $rants = $db->fetch("SELECT 'rant' as type, rantid AS id, title, pub_date FROM rants WHERE pub_date <= UNIX_TIMESTAMP() ORDER BY pub_date DESC LIMIT 5");
    $comics = $db->fetch("SELECT 'comic' as type, comicid AS id, title, pub_date FROM comics WHERE pub_date <= UNIX_TIMESTAMP() ORDER BY pub_date DESC LIMIT 5");
    $updates = array_merge($rants, $comics);
    uasort($updates, 'compare_updates');

    return array_slice($updates, 0, 5);
}

function compare_updates($a, $b) {
    if ($a['pub_date'] == $b['pub_date']) {
        return 0;
    }
    return ($a['pub_date'] > $b['pub_date']) ? -1 : 1;
}

?>
