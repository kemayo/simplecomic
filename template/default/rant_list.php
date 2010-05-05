<?php
/*
available variables:
 $page: array of info about the page
 $rants: array of info about the rants
*/
template('head');
?>
<div class="rants">
    <h3>Rants</h3>
    <?php if($rants) { ?>
    <ul>
        <?php foreach($rants as $rant) { ?>
        <li><a href="<?php echo BASEURL, '/rant/', $rant['rantid']; ?>"><?php echo $rant['title'] ?></a></li>
        <?php } ?>
    </ul>
    <?php } else { ?>
    <p>No Rants</p>
    <?php } ?>
</div>
<?php
template('foot');
?>