</div>
<div class="navmenu">
    <ul>
        <li><a href="<?php echo url('archive/');?>">Archive</li>
        <li><a href="<?php echo url('chapters/');?>">Chapters</li>
        <li><a href="<?php echo url('rants/');?>">Rants</li>
    </ul>
</div>
<div class="foot">
    <a href="http://github.com/kemayo/simplecomic/">simplecomic</a>
    |
    generated in <?=number_format($page->elapsed(), 4)?>s
</div>
<?php template('frame_bottom'); ?>