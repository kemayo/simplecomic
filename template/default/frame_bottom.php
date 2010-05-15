<?php
if(DEBUG) {
    global $db;
    echo '<pre class="debug">';
    echo "SQL:\n";
    print_r($db->debug_output);
    echo "Other:\n";
    print_r($page->get_debug());
    echo '</pre>';
}
$page->output_js();
?>
</body>
</html>