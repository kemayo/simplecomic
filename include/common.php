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
 * If the template is not found it will try the following:
 *  1. If the template is in $template_fallbacks, that template instead
 *  2. The same template in default
 *  3. If the template is in $template_fallbacks, that template in default instead
 *
 * @param string $name
 * @param array $vars
 * @return void
 */
function template($name, $vars = false, $template_dir = false, $allow_fallback = true) {
    global $page;
    if($file = template_path("{$name}.php", $template_dir, $allow_fallback)) {
        if(is_array($vars)) {
            extract($vars);
        }
        if(isset($page)) { $page->debug('template', $file); }
        include $file;
        return true;
    }
    return false;
}

function template_path($filename, $template_dir = false, $allow_fallback = true) {
   global $template_fallbacks;
    if(!$template_dir) {
        $template_dir = config('template');
    }
    $template = "template/{$template_dir}/{$filename}";
    if(file_exists(BASEDIR . "/" . $template)) {
        return $template;
    } elseif(isset($template_fallbacks[$filename]) && $template = template_path($template_fallbacks[$filename], $template_dir, false)) {
        return $template;
    } elseif($template_dir != 'default') {
        return template_path($filename, 'default');
    }
    return false;
}

/**
 * Returns a canonical url
 * Urls beginning with http:// or / are returned as-is
 * Other urls are assumed to be relative to BASEURL
 *
 * @param string $url
 * @param bool $absolute - return an absolute URL
 * @param bool $always_pretty - force pretty_urls for this one
 *              (intended for cases where a real file is being linked to)
 *
 */
function url($url, $absolute = false, $always_pretty = false) {
    if(is_array($url)) {
        if(!empty($url['comicid'])) {
            $url = "comic/" . ($url['slug'] ?: $url['comicid']);
        } elseif (!empty($url['chapterid'])) {
            $url = "chapter/" . $url['slug'];
        } else {
            $url = "";
        }
    }
    if(preg_match("!^([^:]+://|/)!", $url)) {
        // external link
        return $url;
    }
    $beginning = ($absolute ? current_domain() : '') . BASEURL . '/';
    if(!$url || config('pretty_urls') || $always_pretty) {
        return  $beginning . $url;
    }
    return $beginning . "/index.php?q=" . $url;
}

function redirect($where, $message = false) {
    $where = url($where);
    ob_end_clean();
    if($message || DEBUG) {
        template('head');
        if($message) {
            echo '<div class="message">', $message, '</div>';
        }
        echo '<div class="redirect">','Continue to: <a href="', $where, '">', $where, '</a></div>';
        template('foot');
        die;
    }
    header("Location:{$where}");
    die;
}

function die_error($error) {
    ob_end_clean();
    template('head');
    echo '<div class="error">Error: ', $error, '</div>';
    template('foot');
    die;
}

function ob_template($name, $vars = false) {
    ob_start();
    template($name, $vars);
    return ob_get_clean();
}

function fetch_navigation($comic) {
    global $db;
    // comics that were published at the exact moment that this one was:
    // $comics_now = $db->quick("SELECT COUNT(*) FROM comics WHERE pub_date = %d", $comic['pub_date']);
    $prev_comic = $db->fetch_first("SELECT comicid, slug FROM comics WHERE pub_date < %d AND pub_date <= UNIX_TIMESTAMP() ORDER BY pub_date DESC LIMIT 1", $comic['pub_date']);
    $next_comic = $db->fetch_first("SELECT comicid, slug FROM comics WHERE pub_date > %d AND pub_date <= UNIX_TIMESTAMP() ORDER BY pub_date ASC LIMIT 1", $comic['pub_date']);
    $first_comic = $db->fetch_first("SELECT comicid, slug FROM comics WHERE pub_date <= UNIX_TIMESTAMP() ORDER BY pub_date ASC LIMIT 1");
    $last_comic = $db->fetch_first("SELECT comicid, slug FROM comics WHERE pub_date <= UNIX_TIMESTAMP() ORDER BY pub_date DESC LIMIT 1");

    $current = !empty($comic) ? ($comic['slug'] ?: $comic['comicid']) : false;
    $prev = !empty($prev_comic) ? ($prev_comic['slug'] ?: $prev_comic['comicid']) : false;
    $next = !empty($next_comic) ? ($next_comic['slug'] ?: $next_comic['comicid']) : false;
    $first = !empty($first_comic) ? ($first_comic['slug'] ?: $first_comic['comicid']) : false;
    $last = !empty($last_comic) ? ($last_comic['slug'] ?: $last_comic['comicid']) : false;
    return compact('current', 'prev', 'next', 'first', 'last');
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

function fetch_recent_updates($text = false, $count = 5) {
    global $db;
    if($text) {
        $rants = $db->fetch("SELECT 'rant' as type, r.rantid AS id, r.title, r.pub_date, t.text FROM rants r LEFT JOIN rants_text t ON r.rantid = t.rantid WHERE r.pub_date <= UNIX_TIMESTAMP() ORDER BY r.pub_date DESC LIMIT 5");
        $comics = $db->fetch("SELECT 'comic' as type, c.comicid AS id, c.title, c.pub_date, t.description, t.alt_text FROM comics c LEFT JOIN comics_text t ON c.comicid = t.comicid WHERE c.pub_date <= UNIX_TIMESTAMP() ORDER BY c.pub_date DESC LIMIT 5");
    } else {
        $rants = $db->fetch("SELECT 'rant' as type, rantid AS id, title, pub_date FROM rants WHERE pub_date <= UNIX_TIMESTAMP() ORDER BY pub_date DESC LIMIT 5");
        $comics = $db->fetch("SELECT 'comic' as type, comicid AS id, title, pub_date FROM comics WHERE pub_date <= UNIX_TIMESTAMP() ORDER BY pub_date DESC LIMIT 5");
    }
    $updates = array_merge($rants, $comics);
    uasort($updates, 'compare_updates');

    return array_slice($updates, 0, $count);
}

function compare_updates($a, $b) {
    if ($a['pub_date'] == $b['pub_date']) {
        return 0;
    }
    return ($a['pub_date'] > $b['pub_date']) ? -1 : 1;
}

function current_domain() {
    $url = 'http';
    if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
        $url .= "s";
    }
    $url .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $url .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];
    } else {
        $url .= $_SERVER["SERVER_NAME"];
    }
    return $url;
}

function current_url() {
    return current_domain() . $_SERVER["REQUEST_URI"];
}

function authtoken($salt = '') {
    return md5($salt . config('adminuser') . config('adminpass'));
}

function authtoken_input($salt = '') {
    $authtoken = authtoken($salt);
    return '<input type="hidden" name="authtoken" value="' . $authtoken . '" />';
}

function post_link($url, $label) {
    ?><form action="<?php echo url($url); ?>" method="POST" enctype="multipart/form-data" class="post-link"><?php
    echo authtoken_input();
    ?><input type="submit" name="submit" value="<?php echo $label; ?>"><?php
    ?></form><?php

}

function config($key, $default = false) {
    global $config;
    if (isset($config[$key])) {
        return $config[$key];
    }
    return $default;
}

function default_datetime() {
    return strtotime(config('default_time', '00:00:00'));
}
