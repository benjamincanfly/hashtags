<?php
	
	require_once("config.php");
	
	$qs="update config set configValue='' where configKey='lowTweet'";
	$q=mysql_query($qs);	
	
	$qs="update config set configValue='' where configKey='lowTarget'";
	$q=mysql_query($qs);
	
	
?>