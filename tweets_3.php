<?php

	require_once("config.php");

	$pageID="tweets_3";

	$body="";

	head('<link type="text/css" rel="stylesheet" href="/tweets.css"/>');
	head('<link type="text/css" rel="stylesheet" href="/tweets_3.css"/>');

	$qs=sprintf("select * from tweets where hashtag='%s' and (rating_1='2' OR rating_2='1') order by tweet_id DESC", mysql_real_escape_string($config['hashtag']));
	$q=mysql_query($qs);
	
	$tweets=array();
	
	while($tweet=mysql_fetch_assoc($q)){
		$tweets[$tweet['tweet_id']]=$tweet;
	}

	head('<script type="text/javascript">var json_tweets='.json_encode($tweets).';</script>');
	head('<script type="text/javascript" src="/tweet_favoriting.js"></script>');
	head('<script type="text/javascript" src="/tweets_3.js"></script>');

	$body.="<h2 class='header'>Hello! You are rating the cross-favorited #".$config['hashtag']." tweets.</h2>";

	if(count($tweets)==0){
		body('<div id="none">There\'s nothing here yet.</div>');
		include("html.php");
		die();
	}

	$body.="<div id='rateFormatting'><input type='checkbox' name='removestuff' id='removestuff' checked='checked'/> <label for='removestuff'>Remove usernames, hashtags and @jimmys</label>&nbsp;&nbsp;&nbsp;<input type='checkbox' name='fx' id='fx' checked='checked'/> <label for='fx'>FX</label></div>";

	$body.="<div id='tweets'></div>";


	$body.='<div id="finished">All done! <a href="/ratedTweets.php">View Rated Tweets!</a></div>';

	$body.='<div id="progress">You are <span><em class="percent">0</em>%</span> done rating tweets. So far you have <span><em class="Ts">0</em> Ts</span> and <span><em class="STs">0</em> S/Ts</span></div>';

	include("html.php");

?>