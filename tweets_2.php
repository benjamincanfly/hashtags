<?php
	
	session_start();
	
	require_once("config.php");
	
	$pageID='tweets_2';
	
	if($_SESSION['crossfavoriting_id']){
		
		$qs=sprintf("select * from tweets where hashtag='%s' and assigned_to_user_id='%s' and rating_1='1'", $config['hashtag'], $_SESSION['crossfavoriting_id']);
		$q=mysql_query($qs);
		$tweets=array();
	
		while($tweet=mysql_fetch_assoc($q)){
			$tweets[]=$tweet;
		}
	
		head('<script type="text/javascript">var tweets='.json_encode($tweets).';</script>');
		
	}
	
	head('<script src="/tweet_favoriting.js"></script>');
	
	head('<link type="text/css" rel="stylesheet" href="/tweets.css"/>');
	head('<link type="text/css" rel="stylesheet" href="/tweets_1.css"/>');
	head('<link type="text/css" rel="stylesheet" href="/tweets_2.css"/>');
	
	body('<div id="iamfavoriting">Hello <select id="whoiam">');
	
	$qs=sprintf("select * from users order by name ASC");
	$q=mysql_query($qs);
	while($user=mysql_fetch_assoc($q)){
		body('<option value="'.$user['id'].'"');
		
		if($user['id']==$_SESSION['user_id']){
			body(' selected="selected"');
		}
		body('>'.$user['name'].'</option>');
	}
	
	body('</select> ! You are cross-favoriting tweets favorited by <select id="whoiamfavoriting">');
	
	$qs=sprintf("select * from users order by name ASC");
	$q=mysql_query($qs);
	while($user=mysql_fetch_assoc($q)){
		body('<option value="'.$user['id'].'"');
		if($user['id']==$_SESSION['crossfavoriting_id']){
			body(' selected="selected"');
		}
		body('>'.$user['name'].'</option>');
	}
	
	body('</select>');
	
	body('</div><div id="tweets"></div>');
	
	if(count($tweets)==0){
		body('<div id="none">There\'s nothing here yet.</div>');
	}
	include("html2.php");
	
?>