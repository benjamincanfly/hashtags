<?php

	require_once("config.php");
	
	head('<link type="text/css" rel="stylesheet" href="/graph.css"/>');
	
	$qs=sprintf("select tweet_id from tweets where hashtag='%s' order by tweet_id DESC", mysql_real_escape_string($config['hashtag']));
	
	$q=mysql_query($qs);
	
	$saved_tweets=array();
	$saved_tweets_assoc=array();
	
	while($tweet=mysql_fetch_assoc($q)){
		$saved_tweets[]=$tweet['tweet_id'];
		$saved_tweets_assoc[$tweet['tweet_id']]=true;
	}
	// BEGIN: TWITTER OAUTH
	require_once('twitteroauth/config.php');
	require_once('twitteroauth/twitteroauth/twitteroauth.php');
	$connection = getConnectionWithAccessToken("8005672-VseKrlY1CPy0qV00dJ5roTvGwx6tzL5knetNvwnc", "G4zEoHTeslBju5T3M7Jd2cMhbohosuPR8witHzesI");
	$content = $connection->get('account/verify_credentials');
	// END: TWITTER OAUTH
	
	// Get the oldest tweet of Jimmy's with this hashtag.
	
	if(!$_SESSION['jimmy_tweet']['id']){
		$url="https://api.twitter.com/1.1/search/tweets.json?q=".urlencode("#".$config['hashtag'].' from:@jimmyfallon')."&result_type=recent";
		$thing=$connection->get($url);
		$jimmyTweets=array();
		if($thing->statuses && count($thing->statuses)>0){
			foreach($thing->statuses as $tweet){
				$jimmyTweets[]=$tweet;
			}	
		}
		$_SESSION['jimmy_tweet']['id'] = $jimmyTweets[count($jimmyTweets)-1]->id_str;
	} else { }
	
	//body("Jimmy's oldest #".$config['hashtag']." tweet: ".$_SESSION['jimmy_tweet']['id']."<br/>");
	
	// Now we have the ID of the lowest/oldest tweet (Jimmy's) we will ever retrieve.
	
	$_SESSION['lowest_tweet_saved']=count($saved_tweets)?$saved_tweets[count($saved_tweets)-1]:false;
	$_SESSION['highest_tweet_saved']=count($saved_tweets)?$saved_tweets[0]:false;
	
	// Get latest tweet (from anyone) with this hashtag
		$url="https://api.twitter.com/1.1/search/tweets.json?q=".urlencode("#".$config['hashtag'].' -rt -filter:links')."&result_type=recent&count=1";
	
	$thing=$connection->get($url);
	
	foreach($thing->statuses as $tweet){
		$newestTweet=$tweet;
	}
	
	//body('Newest tweet: '.$newestTweet->id_str.'<br/>');
	
	$rough_saved=$_SESSION['highest_tweet_saved']-$_SESSION['lowest_tweet_saved'];
	
	$tweet_percentile=count($saved_tweets)/($_SESSION['highest_tweet_saved']-$_SESSION['lowest_tweet_saved']);
	
	$tweets_from_jimmy_to_now=$newestTweet->id_str-$_SESSION['jimmy_tweet']['id'];
	
	$probable_hashtagged_tweets=$tweet_percentile*$tweets_from_jimmy_to_now;
	
	$percent_saved=round(100*(count($saved_tweets)/$probable_hashtagged_tweets));
	
	$percent_before_saved=round(100*(($_SESSION['lowest_tweet_saved']-$_SESSION['jimmy_tweet']['id'])/$tweets_from_jimmy_to_now));
	
	body('<h2 class="header">Hello, this is the tweet graph page.</h2><p>This app searches Twitter for tweets marked with this week\'s hashtag (currently #'.$config['hashtag'].') and saves them to a database so we can easily view, favorite, and rate them. Of course we want to get ALL of the #'.$config['hashtag'].' tweets, but unfortunately, Twitter only allows apps to retrieve a limited number of tweets at a time. Because of this, we have to pick a starting point (the most recent #'.$config['hashtag'].' tweet in existence) and work our way from there back to Jimmy\'s original "Let\'s play the hashtag game" tweet, a few hundred tweets at a time.</p><p>This graph shows how many tweets have been retrieved so far.</p>');
	
	//$_SESSION['jimmy_tweet']['id']=10000;
	//$_SESSION['lowest_tweet_saved']=25263;
	//$_SESSION['highest_tweet_saved']=75601;
	
	$tweetCount=count($saved_tweets);
	$newestTweetId=$newestTweet->id_str;
	
	$rough_saved=$_SESSION['highest_tweet_saved']-$_SESSION['lowest_tweet_saved'];
	
	if($newestTweetId){
		$rough_total_count=$newestTweetId-$_SESSION['jimmy_tweet']['id'];	
	} else {
		$rough_total_count=$_SESSION['highest_tweet_saved']-$_SESSION['jimmy_tweet']['id'];
	}
	
	$percent_saved=round(100*($rough_saved/$rough_total_count));
	$percent_before_saved=round(100*(($_SESSION['lowest_tweet_saved']-$_SESSION['jimmy_tweet']['id'])/$rough_total_count));
	$percent_of_twitter=$tweetCount/$rough_saved;
	
	body('<div id="graph"><div id="saved" style="');
		if($percent_saved>0){
			body('width:'.$percent_saved.'%;left:'.$percent_before_saved.'%;');
		}
	body('"><span class="saved">'.$tweetCount.' tweets saved to database</span><span class="lowest">Lowest tweet saved:<br/>'.$_SESSION['lowest_tweet_saved'].'</span><span class="highest">Highest tweet saved:<br/>'.$_SESSION['highest_tweet_saved'].'</span></div><span class="jimmy">Jimmy\'s "Let\'s play" tweet:<br/>'.$_SESSION['jimmy_tweet']['id'].'</span><span class="newest">Newest #'.$config['hashtag'].' tweet on Twitter:<br/>'.$newestTweetId.'</span></div>');
	
	body('ID of Jimmy\'s oldest #'.$config['hashtag'].' tweet: '.$_SESSION['jimmy_tweet']['id'].'<br/>');
	body('ID of oldest tweet saved to database so far: '.$_SESSION['lowest_tweet_saved'].'<br/>');
	body('ID of newest tweet saved to database so far: '.$_SESSION['highest_tweet_saved'].'<br/>');
	body('ID of most recent #'.$config['hashtag'].' tweet on Twitter: '.$newestTweetId.'<br/><br/>');
	body('About '.round(100*$percent_of_twitter,2).'% of Twitter is #'.$config['hashtag'].' tweets right now.<br/><br/>');
	
	body('#'.$config['hashtag'].' tweets saved to database: '.$tweetCount.'<br/>');
	body('Estimated percent of #'.$config['hashtag'].' tweets saved to database: '.$percent_saved.'%<br/>');
	//body('Percent before saved: '.$percent_before_saved.'% (about '.')<br/><br/>');
	
	
	include("html.php");

?>