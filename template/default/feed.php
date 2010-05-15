<?php
header('Content-type: application/atom+xml');
header('Cache-Control: max-age=3600', true);
header('Expires: ' . gmdate("D, d M Y H:i:s", time() + 3600) . " GMT", true);

$feed_url = current_url();

$updated = false;
if($updates) {
    $first = current($updates);
    $updated = $first['pub_date'];
} else {
    $updated = time();
}

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<feed xmlns="http://www.w3.org/2005/Atom" xml:lang="en">
    <id><?=$feed_url;?></id>
    <title><?=$config['title'];?></title>
    <updated><?=date('c', $updated);?></updated>
    <link rel="self" type="application/atom+xml" href="<?=$feed_url;?>" />
    <?php foreach($updates as $update) {
        $urlbase = false;
        switch($update['type']) {
            case 'comic':
                $type = "Comic";
                $urlbase = url('comic/', true);
                break;
            case 'rant':
                $type = "Rant";
                $urlbase = url('rant/', true);
                break;
        }
        if(!$urlbase) {
            continue;
        }
        $entry_url = $urlbase . $update['id'];
        ?>
        <entry>
            <id><?=$entry_url;?></id>
            <link rel="alternate" type="text/html" href="<?=$entry_url;?>" />
            <title><![CDATA[<?=$type . ': ' . $update['title']?>]]></title>
            <author>
                <name><![CDATA[<?=$config['author'];?>]]></name>
                <uri><?=$config['author_url'] ? $config['author_url'] : url('', true);?></uri>
            </author>
            <published><?=date('c', $update['pub_date']);?></published>
            <updated><?=date('c', $update['pub_date']);?></updated>
            <?php if($config['full_feed']) { ?>
            <content type="html"><![CDATA[<?php
            switch($update['type']) {
                case 'comic':
                    ?><img src="<?php echo current_domain(), $urlbase, 'image/', $update['id']; ?>" alt="comic" title="<?=$update['alt_text']?>" /><?php
                    if($update['description']) {
                        ?><div><?=$update['description'];?></div><?php
                    }
                    break;
                case 'rant':
                    echo $update['text'];
                    break;
            }
            ?>]]></content>
            <?php } else { ?>
            <summary type="html"><![CDATA[<?php
            echo '<a href="', $entry_url, '">View ', $update['type'], ' here</a>';
            ?>]]></summary>
            <?php } ?>
        </entry>
    <?php } ?>
</feed>