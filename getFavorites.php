<?php

include("config.php");

$body="";

if(!$_POST || $_POST['action']!='go'){
	
	if($_GET['message']){
		$body.="<h1 style='color:#0d0;'>Ok, you signed into Twitter, now you can click 'Get favorites' and it will work.</h1>";
	}
	
	$body.="<h2>Get #".$config['hashtag']." tweets favorited by LNJF hashtaggers</h2><br/>Favorited tweets will be imported from these Twitter accounts:<br/><ul>";
	
	$favers=explode(' ', $config['favers']);
	
	for($i=0;$i<count($favers);$i++){
		$body.="<li>".$favers[$i]."</li>";
	}
	
	$body.="</ul>After clicking 'Get favorites' please be patient, it could take a minute!<br/><br/><form method='post' action='getFavorites.php'><input type='hidden' name='action' value='go'/><input type='submit' value='Get favorites'/></form>";
	
	include("html2.php");
	
	die();
}

$body.="<h1>#".$config['hashtag']."</h1>";

$alltweets=array();

$existingtweets=array();

function object_to_array($data)
{
    if (is_array($data) || is_object($data))
    {
        $result = array();
        foreach ($data as $key => $value)
        {
            $result[$key] = object_to_array($value);
        }
        return $result;
    }
    return $data;
}

function searchArrayForItemsWhereKeyEquals($thisKey, $thisValue, $thisArray){
	for($i=0;$i<count($thisArray);$i++){
		//echo "<p>Checking ".$thisArray." for item with key ".$thisKey." value ".$thisValue."</p>";
		if($thisArray[$i][$thisKey] && $thisArray[$i][$thisKey]==$thisValue){
			return $thisArray[$i];
		}
	}
	return false;
}

$qs=sprintf("select * from crossfaves where hashtag='%s' order by tweet DESC", mysql_real_escape_string($config['hashtag']));
$q=mysql_query($qs);

while($fave=mysql_fetch_assoc($q)){
	$existingtweets[]=$fave;
}

//echo "<pre>".print_r($existingtweets,true)."</pre>";

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

/* If method is set change API call made. Test is called by default. */
$content = $connection->get('account/verify_credentials');

/* Some example calls */
//$connection->get('users/show', array('screen_name' => 'abraham'));
//$connection->post('statuses/update', array('status' => date(DATE_RFC822)));
//$connection->post('statuses/destroy', array('id' => 5437877770));
//$connection->post('friendships/create', array('id' => 9436992));
//$connection->post('friendships/destroy', array('id' => 9436992));

//count=200&screen_name=namesfuckinhank


foreach(explode(' ', $config['favers']) as $tweeter){
	
	$max=false;
	
	$usertweets=0;
	
	$body.="<h2>Getting faves from ".$tweeter."</h2>";
	
	$user=$connection->get('users/show', array('screen_name' => $tweeter));
	$user=object_to_array($user);
	$continue=true;
	
	$i=0;
	while($continue==true && $i<3){
		$i++;
		$body.=($max?"<p>Getting next 200 faves ...</p>":"<p>Getting first 200 faves ...</p>");
		
		if($max){
			$thing=$connection->get('favorites', array('user_id' => $user['id'], 'count'=>200, 'max_id'=>$max));
		} else {
			$thing=$connection->get('favorites', array('user_id' => $user['id'], 'count'=>200));
		}
	
		//echo "<pre>".print_r($thing, true)."</pre>";
	
		foreach($thing as $tweet){
			
			$tweet=object_to_array($tweet);
			
			if(stripos($tweet['text'], '#'.$config['hashtag'])!==false){
				//echo "Adding tweet.<br/>";
				$usertweets++;
				$tweet['faver']=$tweeter;
				$alltweets[]=$tweet;
				$max=$tweet['id_str'];
			} else {
				//echo "Finished finding tweets tagged with #".$config['hashtag']."<br/>";
				//echo "Stopping at tweet ".$tweet['id_str'].".<br/>";
				$continue=false;
				$max=false;
				break;
			}
		}
	
	}

	$body.="<h3>Found ".$usertweets." tweets faved by ".$tweeter.".</h3>";

}


//echo "<h1>Existing tweets:</h1>";
//echo "<pre>".print_r($existingtweets, true)."</pre>";

//echo "<h1>Tweets to insert:</h1>";
//echo "<pre>".print_r($alltweets, true)."</pre>";

//die();

$addedtweets=0;

$insertedtweets=array();

foreach($alltweets as $tweet){
	
	$tweet=object_to_array($tweet);
	
	if(searchArrayForItemsWhereKeyEquals('tweet', $tweet['id_str'], $existingtweets) || $insertedtweets[$tweet['id_str']]){
		//echo "Tweet #".$tweet['id_str']." already in DB.<br/>";
		continue;
	}
	
	$insertedtweets[$tweet['id_str']]=true;
	
	$qs=sprintf("insert into crossfaves (tweet, hashtag, faver, user, text) VALUES ('%s', '%s', '%s', '%s', '%s')",
		mysql_real_escape_string($tweet['id_str']),
		mysql_real_escape_string($config['hashtag']),
		mysql_real_escape_string($tweet['faver']),
		mysql_real_escape_string($tweet['user']['screen_name']),
		mysql_real_escape_string($tweet['text'])
	);
	
	//echo "Inserting tweet ".$tweet['id_str']."<br/>";
	//echo $qs."<br/>";
	
	$addedtweets++;
	
	$q=mysql_query($qs);
	
	//echo "Error: ".mysql_error()."<br/>";
	
}

$body.= "<h1>Found ".count($alltweets)." faved #".$config['hashtag']." tweets. Added $addedtweets to database.</h1>";

include("html2.php");

?>