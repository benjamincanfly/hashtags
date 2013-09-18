<?php

	require_once("config.php");
	
	if($_POST['op']=='newhashtag'){
		
		if(mysql_real_escape_string($_POST['hashtag'])==$config['hashtag']){
			body('<h1>You tried to set the hashtag to what it already was. That doesn\'t work.</h1><hr/><br/><br/>');
		} else {
			
			$qs=sprintf("update config set configValue='%s' where configKey='hashtag'",
				mysql_real_escape_string($_POST['hashtag']));
			$q=mysql_query($qs);	
			
			$qs="update config set configValue='' where configKey='lowTweet'";
			$q=mysql_query($qs);	
			
			$qs="update config set configValue='' where configKey='lowTarget'";
			$q=mysql_query($qs);
			
			$config=array();
			
			$qs="select * from config";
			$q=mysql_query($qs);
			
			while($thisSetting = mysql_fetch_assoc($q)){
				$config[$thisSetting['configKey']] = $thisSetting['configValue'];
			}
			
			body("<h1>Hashtag set to #".$_POST['hashtag']." Now go to the <a href=\"/search_new.php\">tweet retrieval page</a> to start loading tweets.</h1><hr/><br/><br/>");
			
		}
			
	}
	
	
	$pageID='setup';
	
	
	
	
	body('<h2>Hashtags Setup</h2><br/><br/><p>On this page you can set a new hashtag, and retrieve tweets from Twitter. The current hashtag is: #'.$config['hashtag'].'</p><br/>');
	
	
	$form='<form action="/setup.php" method="post" id="setup"><input type="hidden" name="op" value="newhashtag"/>Set a new hashtag: <input type="text" name="hashtag" size="15"/> <input type="submit" value="Go"/> Don\'t include the #, just the text</form>';
	
	body($form);
	
	body('<br/><br/><br/><h2>Retrieve more tweets</h2><p>Go to the <a href="/search_new.php">tweet retrieval page</a> to load tweets into the system.');
	
	
	include("html.php");
	
?>