<?php
	
	include("config.php");
	
	$qs=sprintf("select * from tweets where hashtag='%s' order by tweet_id ASC limit 0,100",
		mysql_real_escape_string($config['hashtag']));
	$q=mysql_query($qs);
	
	$tweets=array();
	
	while($tweet=mysql_fetch_assoc($q)){
		$tweets[]$tweet;
	}
	
?>

