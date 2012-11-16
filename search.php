<?php

	include("config.php");

	$body="";
	
	$config['hashtag'];
	$qs=sprintf("select tweet_id from tweets where hashtag='%s' order by tweet_id DESC", mysql_real_escape_string($config['hashtag']));
	$body.=$qs.'<br/>';
	
	$q=mysql_query($qs);
	
	$saved_tweets=array();
	$saved_tweets_assoc=array();
	
	while($tweet=mysql_fetch_assoc($q)){
		//$body.=$tweet['tweet_id'].'<br/>';
		$saved_tweets[]=$tweet['tweet_id'];
		$saved_tweets_assoc[$tweet['tweet_id']]=true;
	}
	
	//$body.='<pre>'.print_r($saved_tweets, true).'</pre>';
	
	
	// include("html2.php");
	// die();
	
	
	/* Load required lib files. */
	session_start();
	require_once('twitteroauth/twitteroauth/twitteroauth.php');
	require_once('twitteroauth/config.php');

	/* If access tokens are not available redirect to connect page. */
	if (empty($_SESSION['access_token']) || empty($_SESSION['access_token']['oauth_token']) || empty($_SESSION['access_token']['oauth_token_secret'])) {
	    header('Location: ./twitteroauth/clearsessions.php');
	}
	
	$access_token = $_SESSION['access_token'];
	$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
	$content = $connection->get('account/verify_credentials');
	
	$jimmy_id=268837894528065536;
	$since_id=$jimmy_id-1;
	
	$lowest_tweet_saved=count($saved_tweets)?$saved_tweets[count($saved_tweets)-1]:false;
	$highest_tweet_saved=count($saved_tweets)?$saved_tweets[0]:false;
	$got_tweets_since_jimmy=false;
	
	$body.="lowest and highest tweets saved: ".$lowest_tweet_saved." ".$highest_tweet_saved."<br/><br/>";
	
	/*
	include("html2.php");
	die();
	*/
	
	$all_tweets=array();
	
	$finished=false;
	
	$i=0;
	
	while(!$finished && $i<10){
		
		$i++;
		
		$url="https://api.twitter.com/1.1/search/tweets.json?q=".urlencode("#".$config['hashtag'].' -rt -filter:links');
		
		$url.="&result_type=recent&count=100";
		
		if($lowest_tweet_saved==$jimmy_id){
			$url.="&since_id=".($highest_tweet_saved);
		} else {
			$url.="&since_id=".($jimmy_id-1);
		}
		
		if ($lowest_tweet_saved && $lowest_tweet_saved != $jimmy_id){
			$url.="&max_id=".($lowest_tweet_saved-1);
		}
		
		
		$body.="<br/><h2>URL: ".$url."</h2><br/>";
		
		$thing=$connection->get($url);
		
		if($thing->statuses && count($thing->statuses)>0){
			foreach($thing->statuses as $tweet){
				$body.="Tweet #".$tweet->id.' at '.$tweet->created_at.' by '.$tweet->user->screen_name.'</br>';
				$lowest_tweet_saved=$tweet->id;
				$all_tweets[]=$tweet;
			}
			
		} else {
			
			$body.='<pre>'.print_r($thing,true).'</pre>';
			
			$finished=true;
			break;
		}
		
		$num=count($thing->statuses);
		
	}
	
	$body.="<br/><h1>All together: ".count($all_tweets)."</h1>";
	
	
	//$body.='<pre>'.print_r($all_tweets,true).'</pre>';
	
	for($i=0;$i<count($all_tweets);$i++){

		if(!$saved_tweets_assoc[$all_tweets[$i]->id]){

			$qs=sprintf("insert into tweets (tweet_id, username, tweet, ttime, hashtag) VALUES ('%s', '%s', '%s', '%s', '%s')",
				mysql_real_escape_string($all_tweets[$i]->id),
				mysql_real_escape_string($all_tweets[$i]->user->screen_name),
				mysql_real_escape_string($all_tweets[$i]->text),
				mysql_real_escape_string(strftime('%Y-%m-%d %H:%M:%S', strtotime($all_tweets[$i]->created_at))),
				mysql_real_escape_string($config['hashtag'])			
			);
			$q=mysql_query($qs);
			$saved_tweets[$all_tweets[$i]->id]=true;
			
			// $body.=$qs.'<br/>';
			// $body.="Inserted tweet #".$i.": ".$all_tweets[$i]->id."<br/>";
			
		}

	}
	
	include("html2.php");

?>