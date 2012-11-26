<?php
	
	session_start();
	
	require_once("config.php");
	
	$pageID='tweets_1';
	
	head('<script src="/tweet_favoriting.js"></script>');
	
	head('<link type="text/css" rel="stylesheet" href="/tweets.css"/>');
	head('<link type="text/css" rel="stylesheet" href="/tweets_1.css"/>');
	
	if($_SESSION['user_id']){
		
		$qs=sprintf("select count(*) as num from tweets where hashtag='%s' and assigned_to_user_id='%s'", $config['hashtag'], $_SESSION['user_id']);
		$q=mysql_query($qs);
		$row=mysql_fetch_assoc($q);
		$num=$row['num'];
		$numPages=ceil($num/100);
		
		$thisPage=$_GET['page']?$_GET['page']:$numPages;
	
		$qs=sprintf("select * from tweets where hashtag='%s' and assigned_to_user_id='%s' order by tweet_id ASC limit %s,100",
			mysql_real_escape_string($config['hashtag']),
			mysql_real_escape_string($_SESSION['user_id']),
			mysql_real_escape_string(($thisPage-1)*100));
		$q=mysql_query($qs);
	
		$tweets=array();
	
		while($tweet=mysql_fetch_assoc($q)){
			$tweets[]=$tweet;
		}
	
		head('<script type="text/javascript">var tweets='.json_encode($tweets).';</script>');
		
		$qs=sprintf("select count(*) as num from tweets where hashtag='%s' and assigned_to_user_id='0'", $config['hashtag']);
		$q=mysql_query($qs);
		$row=mysql_fetch_assoc($q);
		$unclaimed=$row['num'];

		if($unclaimed<500){
			head('<script type="text/javascript">var retrieveMoreTweets=true;</script>');
		}
		
	} else {
		
		
		body('<div id="iam">Who are you? <select id="whoiam">');

		$qs=sprintf("select * from users order by name ASC");
		$q=mysql_query($qs);
		while($user=mysql_fetch_assoc($q)){
			body('<option value="'.$user['id'].'">'.$user['name'].'</option>');
		}

		body('</select></div>');
		include("html2.php");
		die();
		
	}
	
	//body('<pre>'.print_r($_SESSION, true).'</pre>');
	
	body('<div id="iam">Hello <select id="whoiam">');
	
	$qs=sprintf("select * from users order by name ASC");
	$q=mysql_query($qs);
	while($user=mysql_fetch_assoc($q)){
		body('<option value="'.$user['id'].'"');
		
		if($user['id']==$_SESSION['user_id']){
			body(' selected="selected"');
		}
		body('>'.$user['name'].'</option>');
	}
	
	body('</select> !');
	
	if($num && $num > 100){
		body(' You are viewing <select id="page">');
		for($i=1;$i<=ceil($num/100);$i++){
			
			body('<option value="'.$i.'"');
			
			if($thisPage==$i){
				body(' selected="selected"');
			}
			
			body('>Page '.$i.'</option>');
			
		}
		body('</select> of the #'.$config['hashtag'].' tweets assigned to you.');
	} else {
		body(' Here are some tweets for you to favorite.');
	}
	
	body('</div><div id="tweets"></div>');
	
	body('<div id="more"><input type="button" id="morebutton" value="Get 100 more #'.$config['hashtag'].' tweets"/></div>');
	
	include("html2.php");
	
	
?>