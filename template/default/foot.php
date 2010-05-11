</div>
<div class="navmenu">
    <ul>
        <li><a href="<?php echo url('/chapters/');?>">Chapters</li>
        <li><a href="<?php echo url('/rants/');?>">Rants</li>
    </ul>
</div>
<div class="foot"><a href="http://github.com/kemayo/simplecomic/">simplecomic</a></div>
<?php if(DEBUG) { ?><pre class="debug">
<?php global $db; ?>
SQL:
<?php print_r($db->debug_output); ?>
Other:
<?php print_r($page['debug']); ?>
</pre><?php } ?>
<?php
foreach($page['js'] as $js) {
    if(preg_match('/^<script/', $js)) {
        echo $js;
    } else {
        echo '<script src="', url($js, false, true), '" type="text/javascript"></script>';
    }
} ?>
</body>
</html>