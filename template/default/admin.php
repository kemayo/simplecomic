<?php template('admin_head'); ?>
<div>For the purpose of comic/rant publishing, the date is: <?=date('Y-m-d H:i:s')?></div>
<h3>Comics</h3>
<ul>
<?php
echo '<li><a href="', url('admin/comic/new'), '">Add a new one</a></li>';
foreach($comics as $c) {
    echo '<li>';
    echo '<a href="', url('admin/comic/'.$c['comicid']), '">';
    echo date('Y-m-d', $c['pub_date']), ' : ', $c['title'];
    echo '</a>';
    if($c['pub_date'] > time()) {
        echo ' UNPUBLISHED';
    }
    echo '</li>';
}
?>
</ul>

<h3>Rants</h3>
<ul>
<?php
echo '<li><a href="', url('admin/rant/new'), '">Add a new one</a></li>';
foreach($rants as $c) {
    echo '<li>';
    echo '<a href="', url('admin/rant/'.$c['rantid']), '">';
    echo date('Y-m-d', $c['pub_date']), ' : ', $c['title'];
    echo '</a>';
    if($c['pub_date'] > time()) {
        echo ' UNPUBLISHED';
    }
    echo '</li>';
}
?>
</ul>

<h3>Chapters</h3>
<ul>
<?php
echo '<li><a href="', url('admin/chapter/new'), '">Add a new one</a></li>';
foreach($chapters as $c) {
    echo '<li>';
    echo '<a href="', url('admin/chapter/'.$c['chapterid']), '">';
    echo $c['title'];
    echo '</a>';
    echo '</li>';
}
?>
</ul>
<?php template('admin_foot'); ?>
