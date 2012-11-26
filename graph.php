<?php

	require_once("config.php");

	$body="";
	
	$qs=sprintf("select tweet_id from tweets where hashtag='%s' order by tweet_id DESC", mysql_real_escape_string($config['hashtag']));
	
	$q=mysql_query($qs);
	
	$saved_tweets=array();
	$saved_tweets_assoc=array();
	
	while($tweet=mysql_fetch_assoc($q)){
		//$body.=$tweet['tweet_id'].'<br/>';
		$saved_tweets[]=$tweet['tweet_id'];
		$saved_tweets_assoc[$tweet['tweet_id']]=true;
	}
	
	/* Load required lib files. */
	session_start();
	require_once('twitteroauth/twitteroauth/twitteroauth.php');
	require_once('twitteroauth/config.php');

	/* If access tokens are not available redirect to connect page. */
	if (empty($_SESSION['access_token']) || empty($_SESSION['access_token']['oauth_token']) || empty($_SESSION['access_token']['oauth_token_secret'])) {
	    header('Location: ./twitteroauth/clearsessions.php');
	}
	/* Get user access tokens out of the session. */
	$access_token = $_SESSION['access_token'];

	/* Create a TwitterOauth object with consumer/user tokens. */
	$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
	
	/* ----------------------------------------- */

	$url="https://api.twitter.com/1.1/search/tweets.json?q=".urlencode("#".$config['hashtag'].' from:@jimmyfallon')."&result_type=recent";
		
	$thing=$connection->get($url);
		
	$jimmyTweets=array();
		
	if($thing->statuses && count($thing->statuses)>0){
		foreach($thing->statuses as $tweet){
			//$body.="Tweet #".$tweet->id.' at '.$tweet->created_at.' by '.$tweet->user->screen_name.'</br>';
			$jimmyTweets[]=$tweet;
		}	
	}
	
	$body.="<br/>Oldest #".$config['hashtag'].' Jimmy tweet: '.$jimmyTweets[count($jimmyTweets)-1]->id.' '.$jimmyTweets[count($jimmyTweets)-1]->text.'<br/><br/>';
	
	include("html2.php");

?>