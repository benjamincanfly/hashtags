<?php

$link=mysql_connect("127.0.0.1", "bapple_ht", "vaejEMjs");
mysql_select_db("bapple_ht", $link);

$config=array();

$qs="select * from config";
$q=mysql_query($qs);

while($thisSetting = mysql_fetch_assoc($q)){
	$config[$thisSetting['configKey']] = $thisSetting['configValue'];
}

?>