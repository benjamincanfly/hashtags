<?php
	
	set_time_limit(0);
	
	//ignore_user_abort(1);
	
	$ajax=isset($_REQUEST['ajax'])?true:false;
	
	if($ajax){
		error_reporting(0);
		ini_set('display_errors', 'Off');
	}
	
	require_once("config.php");
	
	if($config['searching']=='yes'){
		if($ajax){
			$response=array();
			$response['status']='busy';
			echo json_encode($response);
			die();
		} else {
			echo "<h1>Already searching.</h1>";
			die();
		}
	} else {
		$qs=sprintf("update config set configValue='yes' where configKey='searching'");
		$q=mysql_query($qs);
	}
	
	$qs=sprintf("select tweet_id from tweets where hashtag='%s' order by tweet_id DESC", mysql_real_escape_string($config['hashtag']));
	body($qs.'<br/>');
	
	body(mysql_error().'<br/>');
	body(mysql_num_rows().'<br/>');
	
	$q=mysql_query($qs);
	
	$saved_tweets=array();
	$saved_tweets_assoc=array();
	
	while($tweet=mysql_fetch_assoc($q)){
		//$body.=$tweet['tweet_id'].'<br/>';
		$saved_tweets[]=$tweet['tweet_id'];
		$saved_tweets_assoc[$tweet['tweet_id']]=true;
	}
	
	$newestSavedTweet=$saved_tweets[0];
	
	body('Newest tweet SAVED:<br/>'.print_r($saved_tweets[0],true).'<br/>');
	
	//$body.='<pre>'.print_r($saved_tweets, true).'</pre>';
	
	// BEGIN: TWITTER OAUTH
	require_once('twitteroauth/config.php');
	require_once('twitteroauth/twitteroauth/twitteroauth.php');
	$connection = getConnectionWithAccessToken("8005672-VseKrlY1CPy0qV00dJ5roTvGwx6tzL5knetNvwnc", "G4zEoHTeslBju5T3M7Jd2cMhbohosuPR8witHzesI");
	$content = $connection->get('account/verify_credentials');
	// END: TWITTER OAUTH
	
	/*  				GET JIMMY TWEET ID					*/
	
	
	if(!$config['jimmyTweet']||$config['jimmyTweet']==''){
	
		body("<h2>Looking for Jimmy's oldest #".$config['hashtag']." tweet ...</h2>");
		$url="https://api.twitter.com/1.1/search/tweets.json?q=".urlencode("#".$config['hashtag'].' from:@GoodTweetss since:'.strftime("%Y-%m-%d",time()-(60*60*24*30)))."&result_type=recent";
		
		body($url.'<br/>');
		
		$thing=$connection->get($url);
		$jimmyTweets=array();
		if($thing->statuses && count($thing->statuses)>0){
			foreach($thing->statuses as $tweet){
				$jimmyTweets[]=$tweet;
			}	
		}
		
		body("<br/>Found #".$config['hashtag'].' Jimmy tweet: '.$jimmyTweets[count($jimmyTweets)-1]->id_str.' '.$jimmyTweets[count($jimmyTweets)-1]->text.'<br/><br/>');
		
		//body('<br/>Here it is:<br/>');
		
		//body('<pre>'.print_r($jimmyTweets,true).'</pre>');
		
		$jimmyTweetID = $jimmyTweets[count($jimmyTweets)-1]->id_str;
		
		$config['jimmyTweet'] = $jimmyTweetID;
		
		$qs=sprintf("update config set configValue='%s' where configKey='jimmyTweet'", $jimmyTweetID);
		$q=mysql_query($qs);
		
	} else {
		body("Jimmy's oldest #".$config['hashtag']." tweet: ".$config['jimmyTweet']."<br/>");
	}
	
	$all_tweets=array();
	
	$finished=isset($finished) ? $finished : false;
	
	$i=0;
	
	$foundTarget=false;
	
	if($saved_tweets_assoc[$config['jimmyTweet']] /*  && (!$config['lowTweet'] || $config['lowTweet']) */) {
		body("<br/>Jimmy's tweet already gotten");
		if($config['lowTarget']){
			$low_target=$config['lowTarget'];
		} else {
			$low_target=$saved_tweets[0];
		}
	} else if(!$saved_tweets_assoc[$config['jimmyTweet']]) {
		body("<br/>Jimmy's tweet not gotten yet");
		$low_target=bcadd($config['jimmyTweet'], "-1");
	}
	
	$max_query="";
	
	if($config['lowTweet'] && $config['lowTweet']!=''){
		$max_query="&max_id=".($config['lowTweet']-1);
	}
	
	while(!$finished && $i<10){
		$i++;
		$url="https://api.twitter.com/1.1/search/tweets.json?q=".urlencode("#".$config['hashtag'].' -rt');
	
		// &since_id=".(intval($_SESSION['jimmy_tweet']['id'])-)."
	
		$url.="&result_type=recent&count=100&since_id=".($low_target).$max_query;
		
		$body.="<br/>URL ".$i.": ".$url."<br/>";
	
		$thing=$connection->get($url);
	
		//body('<pre>'.print_r($thing,true).'</pre>');
	
		body("Found ".count($thing->statuses)." statuses <br/>");
		
		if($thing->statuses && count($thing->statuses)>0){
		
			$thisTweetID=false;
			foreach($thing->statuses as $tweet){
				//$body.="Tweet #".$tweet->id_str.' at '.$tweet->created_at.' by '.$tweet->user->screen_name.'</br>';
				//$lowest_tweet_saved=$tweet->id_str;
			
				//body('<br/>tweet id = '.($tweet->id_str).' low target = '.$_SESSION['low_target'].'<br/>');
			
				//body('<br/>equal: '.(intval($tweet->id_str)-1==intval($_SESSION['low_target'])).'<br/>');
			
				//$config['gap_top'];
				
				$thisTweetID=$tweet->id_str;
			
				$all_tweets[]=$tweet;
				$high_target=$tweet->id_str;
				$max_query="&max_id=".($high_target-1);
				
			}
			
			body('<h1>Set lowTweet to '.$high_target.'</h1>');
			
			$qs=sprintf("update config set configValue='%s' where configKey='lowTweet'", $high_target);
			$q=mysql_query($qs);
			
			
			$qs=sprintf("update config set configValue='%s' where configKey='lowTarget'", $low_target);
			$q=mysql_query($qs);
			
		} else if($thing->errors){
		
			$error=$thing;
			body('<h1>ERROR!</h1>');
			body('<pre>'.print_r($thing,true).'</pre>');
		
			$finished=true;
			break;
		} else {
			
			body('<h1>Set lowTweet empty!</h1>');
			
			$qs=sprintf("update config set configValue='' where configKey='lowTweet'");
			$q=mysql_query($qs);
			
			$qs=sprintf("update config set configValue='' where configKey='lowTarget'");
			$q=mysql_query($qs);
			
			body('<h1>Error or just no tweets to find.</h1>');
			$finished=true;
			break;
		}
	
	}
	
	
	$body.="<br/><h1>Tweets gotten: ".count($all_tweets)."</h1>";
	
	//$body.='<pre>'.print_r($all_tweets,true).'</pre>';
	
	$inserted_tweets=array();
	
	for($i=0;$i<count($all_tweets);$i++){
		
		if(!isset($saved_tweets_assoc[$all_tweets[$i]->id_str])){
			
			$qs=sprintf("insert into tweets (tweet_id, username, tweet, ttime, hashtag) VALUES ('%s', '%s', '%s', '%s', '%s')",
				mysql_real_escape_string($all_tweets[$i]->id_str),
				mysql_real_escape_string($all_tweets[$i]->user->screen_name),
				mysql_real_escape_string($all_tweets[$i]->text),
				mysql_real_escape_string(strftime('%Y-%m-%d %H:%M:%S', strtotime($all_tweets[$i]->created_at))),
				mysql_real_escape_string($config['hashtag'])			
			);
			$q=mysql_query($qs);
			$saved_tweets[]=$all_tweets[$i]->id_str;
			$saved_tweets_assoc[$all_tweets[$i]->id_str]=true;
			$inserted_tweets[]=$all_tweets[$i];
			// $body.=$qs.'<br/>';
			// $body.="Inserted tweet #".$i.": ".$all_tweets[$i]->id_str."<br/>";
		
		}

	}
	//body("Test!<br/>");
	//include("html.php");
	//die();
	
	if(isset($inserted_tweets) && count($inserted_tweets)){
		body('<br/>Inserted '.count($inserted_tweets)." tweets");
	} else {
		body('<br/>Did not insert any tweets.');
	}
	
	$qs=sprintf("update config set configValue='no' where configKey='searching'");
	$q=mysql_query($qs);
	
	
	if($ajax){
		$response=array();
		if(isset($error)){
			$response['status']='error';
			$response['error']="Sorry, there was some kind of error (type ".$thing->errors[0]['code']."). To try again, reload the page.";
			$response['thing']=$error;
		} else {
			$response['status']='ok';
			$response['count']=isset($inserted_tweets)?count($inserted_tweets):0;
		}
		
		echo json_encode($response);
		
	} else {
		include("html.php");
	}
	
?>