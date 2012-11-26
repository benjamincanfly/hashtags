<?php

	session_start();
	
	require_once("config.php");
	
	$qs=sprintf("update tweets set assigned_to_user_id='%s' where assigned_to_user_id='0' and hashtag='%s' order by tweet_id ASC limit 100",
		mysql_real_escape_string($_SESSION['user_id']),
		mysql_real_escape_string($config['hashtag']));
	$q=mysql_query($qs);
	
	$response=array();
	
	$response['status']='ok';
	
	echo json_encode($response);
	
?>