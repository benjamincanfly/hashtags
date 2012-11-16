<?php

	include("config.php");

	$body="";
	
	$qs=sprintf("select * from tweets where hashtag='%s' order by tweet_id ASC limit 0,100",
		mysql_real_escape_string($config['hashtag']));
	$q=mysql_query($qs);
	
	while($tweet=mysql_fetch_assoc($q)){
		
	}
	
	include("html2.php");

?>