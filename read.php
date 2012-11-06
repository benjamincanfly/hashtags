<?php

$link=mysql_connect("127.0.0.1", "bapple_ht", "vaejEMjs");
mysql_select_db("bapple_ht", $link);

$qs="select * from faves order by tweet DESC";
$q=mysql_query($qs);

while($fave=mysql_fetch_assoc($q)){
	echo "<pre>".print_r($fave,true)."</pre>";
}

?>