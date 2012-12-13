<?php

	session_start();
	
	require_once("config.php");
	
	$qs=sprintf("select count(*) as num from tweets where hashtag='%s' and assigned_to_user_id='%s'", $config['hashtag'], $_SESSION['user_id']);
	$q=mysql_query($qs);
	$row=mysql_fetch_assoc($q);
	$num=$row['num'];
	
	$limit=100;
	
	if(($num % 100)>0){
		$limit=100-($num % 100);
	}         
	
	$qs=sprintf("update tweets set assigned_to_user_id='%s', assignment_time=CURRENT_TIMESTAMP where assigned_to_user_id='0' and hashtag='%s' order by tweet_id ASC limit %s",
		mysql_real_escape_string($_SESSION['user_id']),
		mysql_real_escape_string($config['hashtag']),
		$limit);
	$q=mysql_query($qs);
	
	$response=array();
	
	$response['status']='ok';
	
	echo json_encode($response);
	
?>