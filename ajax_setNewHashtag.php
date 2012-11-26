<?php
	
	require_once("config.php");
	
	$qs=sprintf("update config set configValue='%s' where configKey='hashtag'",
		mysql_real_escape_string($_POST['hashtag']));
	$q=mysql_query($qs);
	
	echo $qs;
	
?>