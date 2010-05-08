<?php

/*
Available variables:
    $current: id of current comic
    $first: id of first comic
    $prev: id of previos comic
    $next: id of next comic
    $last: id of final comic
*/

?>
<ul class="nav">
<?php
if($first != $current) {
    echo '<li><a href="', url('/comic/'.$first), '">First</a></li>';
} else {
    echo '<li>First</li>';
}

if($prev) {
    echo '<li><a href="', url('/comic/'.$prev), '">Prev</a></li>';
} else {
    echo '<li>Prev</li>';
}

if($next) {
    echo '<li><a href="', url('/comic/'.$next), '">Next</a></li>';
} else {
    echo '<li>Next</li>';
}

if($last && $last != $current) {
    echo '<li><a href="', url('/comic/'.$last), '">Latest</a></li>';
} else {
    echo '<li>Latest</li>';
}
?>
</ul>