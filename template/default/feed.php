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
    <id><?php echo $feed_url; ?></id>
    <title><?php echo $config['title']; ?></title>
    <updated><?php echo date('c', $updated); ?></updated>
    <link rel="self" type="application/atom+xml" href="<?php echo $feed_url; ?>" />
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
            <id><?php echo $entry_url;?></id>
            <link rel="alternate" type="text/html" href="<?php echo $entry_url;?>" />
            <title><![CDATA[<?php echo $type . ': ' . $update['title']?>]]></title>
            <author>
                <name><![CDATA[<?php echo $config['author'];?>]]></name>
                <uri><?php echo $config['author_url'] ? $config['author_url'] : url('', true);?></uri>
            </author>
            <published><?php echo date('c', $update['pub_date']);?></published>
            <updated><?php echo date('c', $update['pub_date']);?></updated>
            <?php if($config['full_feed']) { ?>
            <content type="html"><![CDATA[<?php
            switch($update['type']) {
                case 'comic':
                    ?><img src="<?php echo $urlbase, 'image/', $update['id']; ?>" alt="comic" title="<?php echo $update['alt_text']?>" /><?php
                    if($update['description']) {
                        ?><div><?php echo $update['description'];?></div><?php
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