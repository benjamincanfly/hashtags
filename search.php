<?php

	$ajax=$_REQUEST['ajax']?true:false;

	require_once("config.php");
	
	if($ajax){
		if($config['searching']=='yes'){
			$response=array();
			$response['status']='busy';
			echo json_encode($response);
			die();
		} else {
			$qs=sprintf("update config set configValue='yes' where configKey='searching'");
			$q=mysql_query($qs);
		}
	}
	
	$qs=sprintf("select tweet_id from tweets where hashtag='%s' order by tweet_id DESC", mysql_real_escape_string($config['hashtag']));
	//$body.=$qs.'<br/>';
	
	$q=mysql_query($qs);
	
	$saved_tweets=array();
	$saved_tweets_assoc=array();
	
	while($tweet=mysql_fetch_assoc($q)){
		//$body.=$tweet['tweet_id'].'<br/>';
		$saved_tweets[]=$tweet['tweet_id'];
		$saved_tweets_assoc[$tweet['tweet_id']]=true;
	}
	
	//$body.='<pre>'.print_r($saved_tweets, true).'</pre>';
	
	// BEGIN: TWITTER OAUTH
	require_once('twitteroauth/config.php');
	require_once('twitteroauth/twitteroauth/twitteroauth.php');
	$connection = getConnectionWithAccessToken("8005672-VseKrlY1CPy0qV00dJ5roTvGwx6tzL5knetNvwnc", "G4zEoHTeslBju5T3M7Jd2cMhbohosuPR8witHzesI");
	$content = $connection->get('account/verify_credentials');
	// END: TWITTER OAUTH
	
	/*  				GET JIMMY TWEET ID					*/
	
	if(!$_SESSION['jimmy_tweet']||$_SESSION['jimmy_tweet']['hashtag']!=$config['hashtag']){
	
		body("<h2>Looking for Jimmy's oldest #".$config['hashtag']." tweet ...</h2>");
		$url="https://api.twitter.com/1.1/search/tweets.json?q=".urlencode("#".$config['hashtag'].' from:@jimmyfallon')."&result_type=recent";
		$thing=$connection->get($url);
		$jimmyTweets=array();
		if($thing->statuses && count($thing->statuses)>0){
			foreach($thing->statuses as $tweet){
				$jimmyTweets[]=$tweet;
			}	
		}
		
		body("<br/>Found #".$config['hashtag'].' Jimmy tweet: '.$jimmyTweets[count($jimmyTweets)-1]->id_str.' '.$jimmyTweets[count($jimmyTweets)-1]->text.'<br/><br/>');
		
		$_SESSION['jimmy_tweet'] = array('id'=>$jimmyTweets[count($jimmyTweets)-1]->id_str, 'hashtag'=>$config['hashtag'], 'ctime'=>$jimmyTweets[count($jimmyTweets)-1]->created_at);
	} else {
		body("Jimmy's oldest #".$config['hashtag']." tweet: ".$_SESSION['jimmy_tweet']['id']."<br/>");
		
	}
	
	
	/*					HAVE JIMMY TWEET ID					*/
	
	$_SESSION['lowest_tweet_saved']=count($saved_tweets)?$saved_tweets[count($saved_tweets)-1]:false;
	$_SESSION['highest_tweet_saved']=count($saved_tweets)?$saved_tweets[0]:false;
	
	
		$url="https://api.twitter.com/1.1/search/tweets.json?q=".urlencode("#".$config['hashtag'].' -rt')."&result_type=recent&count=1";
	
	$thing=$connection->get($url);
	
	foreach($thing->statuses as $tweet){
		$newestTweet=$tweet;
	}
	
	body('Newest tweet: '.$newestTweet->id_str.'<br/>');
	
	//body('<pre>'.print_r($newestTweet,true).'</pre>');
	
	$rough_saved=$_SESSION['highest_tweet_saved']-$_SESSION['lowest_tweet_saved'];
	
	if($newestTweet){
		$rough_total_count=$newestTweet->id_str-$_SESSION['jimmy_tweet']['id'];	
	} else {
		$rough_total_count=$_SESSION['highest_tweet_saved']-$_SESSION['jimmy_tweet']['id'];
	}
	
	$rough_percent=round(100*$rough_saved/$rough_total_count);
	
	body('<br/><h2>Percent scraped: '.$rough_percent.'%</h2>');
	
	body('<pre>'.print_r($_SESSION,true).'</pre>');
	
	if(count($saved_tweets)==0){
		
		body('None saved.<br/>');
		
		/* None saved
	
		Jimmy
		|-----------------------------------|
		
		We should start from the end and go until we hit Jimmy.
		
		*/
	
		$_SESSION['high_target']=$newestTweet->id_str;
		$_SESSION['low_target']=$_SESSION['jimmy_tweet']['id']-1;
	
	} else if($_SESSION['lowest_tweet_saved']!=$_SESSION['jimmy_tweet']['id']) {
		
		body('Have not gotten all tweets since Jimmy.<br/>');
		
		/*
		If we have NOT gotten all of the tweets since Jimmy (lowest saved!=Jimmy),
		then we either have:
		*/
		
		if($_SESSION['highest_tweet_saved']==$newestTweet->id_str) {
			
			body('Highest saved = highest tweeted.<br/>');
			/* Highest saved = highest tweeted (almost never)
			
			Jimmy         retrieved tweets
			|-------------oooooooooooooooooooooo|
			
			We should find the lowest ID retrieved and retrieve until we hit Jimmy.
			*/
			
			$_SESSION['high_target']=$_SESSION['lowest_tweet_saved']-1;
			$_SESSION['low_target']=$_SESSION['jimmy_tweet']['id']-1;
			
		} else if($_SESSION['highest_tweet_saved']!=$newestTweet->id_str) {
			
			body('Highest saved != highest tweeted.<br/>');
			
			/* Or highest tweet saved is not the newest tweet in existence
			
			
			Jimmy         retrieved tweets
			|-------------ooooooooooooooooo-----|
			
			We should find the lowest ID retrieved, retrieve until we hit Jimmy, and start over from the end. */
			
			$_SESSION['high_target']=$_SESSION['lowest_tweet_saved']-1;
			$_SESSION['low_target']=$_SESSION['jimmy_tweet']['id']-1;
			
			// ... AND THEN RE-START
			
		}
	} else if ($_SESSION['lowest_tweet_saved']==$_SESSION['jimmy_tweet']['id']){
		
		body('Have gotten tweets all the way back to Jimmy.<br/>');
		
		/* If we HAVE gotten the tweets back to Jimmy (lowest saved = Jimmy)
		 then we either have: */
		
		if($_SESSION['highest_tweet_saved']==$newestTweet->id_str && $config['tweetGap']=='no') {
			
			body('Highest saved = highest tweeted. No nothing.<br/>');
			
			// $_SESSION['low_target']=false;
			// $_SESSION['high_target']=false;
			
			$finished=true;
			/* Highest saved = highest tweeted
			
				Jimmy         retrieved tweets
				|ooooooooooooooooooooooooooooooooooo|			We should do nothing.
			*/
			
		} else if($_SESSION['highest_tweet_saved']==$newestTweet->id_str && $config['tweetGap']=='yes') {
			
			body('Highest saved = highest tweeted and got back to Jimmy, but there\'s a gap. Fill in gap between lower tweet id '.$config['gapLowerTweet'].' and upper tweet id '.$config['gapUpperTweet'].'<br/>');
			
			/* Highest saved = highest tweeted but with a gap
			
				Jimmy         retrieved tweets
				|ooooo-------oooooooooooooooooo|			Fill in gap.
			*/
			
			$_SESSION['high_target']=$config['gapUpperTweet']-1;
			$_SESSION['low_target']=$config['gapLowerTweet']-1;
			
		} else if($_SESSION['highest_tweet_saved']<$newestTweet->id_str){
			
			body('Highest saved < highest tweeted.<br/>');
			
			/* Highest saved < highest tweeted
			
			Jimmy      retrieved tweets
			|oooooooooooooooooooooooooo---------|
			
			We should start from the end and go until the highest tweet retrieved. */
			
			$_SESSION['high_target']=$newestTweet->id_str;
			$_SESSION['low_target']=$_SESSION['highest_tweet_saved']-1;
			
		}
	}
	
	//body('<br/><hr/><br/>');
	
	$all_tweets=array();
	
	$finished=$finished||false;
	
	$temp_high_target=$_SESSION['high_target'];
	
	$i=0;
	
	while(!$finished && $i<3){
		$i++;
		
		$url="https://api.twitter.com/1.1/search/tweets.json?q=".urlencode("#".$config['hashtag'].' -rt');
		
		// &since_id=".(intval($_SESSION['jimmy_tweet']['id'])-)."
		
		$url.="&result_type=recent&count=100&since_id=".(bcadd(floatval($_SESSION['jimmy_tweet']['id']), floatval(-1)))."&max_id=".($temp_high_target);
		
		$body.="<br/>URL: ".$url."<br/>";
		
		$thing=$connection->get($url);
		
		//body('<pre>'.print_r($thing,true).'</pre>');
		
		if($thing->statuses && count($thing->statuses)>0){
			
			$foundTarget=false;
			$thisTweetID=false;
			foreach($thing->statuses as $tweet){
				//$body.="Tweet #".$tweet->id_str.' at '.$tweet->created_at.' by '.$tweet->user->screen_name.'</br>';
				//$lowest_tweet_saved=$tweet->id_str;
				
				//body('<br/>tweet id = '.($tweet->id_str).' low target = '.$_SESSION['low_target'].'<br/>');
				
				//body('<br/>equal: '.(intval($tweet->id_str)-1==intval($_SESSION['low_target'])).'<br/>');
				
				//$config['gap_top'];
				
				$thisTweetID=$tweet->id_str;
				
				if(intval($tweet->id_str)-1==intval($_SESSION['low_target'])){
					body('<br/><h1>Found target tweet: '.$tweet->id_str.'</h1>');
					if((intval($tweet->id_str))==intval($_SESSION['jimmy_tweet']['id'])){
						body('<h1>(Jimmy tweet)</h1>');
						// If it's Jimmy's tweet we save it, because that means we have not already done so.
						$all_tweets[]=$tweet;
					}
					$foundTarget=true;
					break 2;
				}
				$all_tweets[]=$tweet;
				$temp_high_target=$tweet->id_str-1;
			
			}
			
			if(!$foundTarget){
				$qs=sprintf("update config set configValue='yes' where configKey='tweetGap'");
				$q=mysql_query($qs);

				$qs=sprintf("update config set configValue='".$thisTweetID."' where configKey='gapUpperTweet'");
				$q=mysql_query($qs);
				
				$qs=sprintf("update config set configValue='".($_SESSION['low_target']+1)."' where configKey='gapLowerTweet'");
				$q=mysql_query($qs);
			} else {
				$qs=sprintf("update config set configValue='no' where configKey='tweetGap'");
				$q=mysql_query($qs);
			}
			
		} else if($thing->errors){
			
			$error=$thing;
			body('<h1>ERROR</h1>');
			body('<pre>'.print_r($thing,true).'</pre>');
			
			$finished=true;
			break;
		} else {	
			body('<h1>Error or just no tweets to find.</h1>');
			$finished=true;
			break;
		}
		
	}
	
	//$body.="<br/><h1>All together: ".count($all_tweets)."</h1>";
	
	//$body.='<pre>'.print_r($all_tweets,true).'</pre>';
	
	$inserted_tweets=array();
	
	for($i=0;$i<count($all_tweets);$i++){

		if(!$saved_tweets_assoc[$all_tweets[$i]->id_str]){

			$qs=sprintf("insert into tweets (tweet_id, username, tweet, ttime, hashtag) VALUES ('%s', '%s', '%s', '%s', '%s')",
				mysql_real_escape_string($all_tweets[$i]->id_str),
				mysql_real_escape_string($all_tweets[$i]->user->screen_name),
				mysql_real_escape_string($all_tweets[$i]->text),
				mysql_real_escape_string(strftime('%Y-%m-%d %H:%M:%S', strtotime($all_tweets[$i]->created_at))),
				mysql_real_escape_string($config['hashtag'])			
			);
			$q=mysql_query($qs);
			$saved_tweets[$all_tweets[$i]->id_str]=true;
			$inserted_tweets[]=$all_tweets[$i];
			// $body.=$qs.'<br/>';
			// $body.="Inserted tweet #".$i.": ".$all_tweets[$i]->id_str."<br/>";
			
		}

	}
	
	if(count($inserted_tweets)){
		body('Inserted '.count($inserted_tweets)." tweets");
	} else {
		body('Did not insert any tweets.');
	}
	
	$qs=sprintf("update config set configValue='no' where configKey='searching'");
	$q=mysql_query($qs);
	
	$response=array();
	
	if($error){
		$response['status']='error';
		$response['error']="Sorry, there was some kind of error (type ".$thing->errors[0]['code']."). To try again, reload the page.";
		$response['thing']=$error;
	} else {
		$response['status']='ok';
		$response['count']=count($inserted_tweets);
	}
	
	if($ajax){
		echo json_encode($response);
	} else {
		include("html.php");
	}
	
?>