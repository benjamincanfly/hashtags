<?php

require_once("config.php");

$pageID="tweets_3";

$body="";

head('<link type="text/css" rel="stylesheet" href="/tweets.css"/>');
head('<link type="text/css" rel="stylesheet" href="/tweets_3.css"/>');

$qs=sprintf("select * from tweets where hashtag='%s' and (rating_1='2' OR rating_2='1') order by tweet_id DESC", mysql_real_escape_string($config['hashtag']));
$q=mysql_query($qs);

$tweets=array();

$jsontweets=array();

while($tweet=mysql_fetch_assoc($q)){
	$tweets[]=$tweet;
	$jsontweets[$tweet['tweet_id']]=$tweet;
}

$body.="<h2 class='header'>Rate #".$config['hashtag']." tweets already cross-favorited by LNJF hashtaggers</h2>";

if(count($tweets)==0){
	body('<div id="none">There\'s nothing here yet.</div>');
	include("html2.php");
	die();
}

$body.="<div id='rateFormatting'><input type='checkbox' name='removestuff' id='removestuff' checked='checked'/> <label for='removestuff'>Remove hashtags and @jimmys</label>&nbsp;&nbsp;&nbsp;<input type='checkbox' name='fx' id='fx' checked='checked'/> <label for='fx'>FX</label></div>";

$body.="<div id='tweets'>";

for($i=0;$i<count($tweets);$i++){
	$body.='<div class="tweet" tweetid="'.$tweets[$i]['tweet_id'].'" rating="'.$tweets[$i]['rating_3'].'"><div class="text">'.$tweets[$i]['tweet'].'</div><div class="controls"><button name="rating" value="1" tweetid="'.$tweets[$i]['tweet_id'].'">S</button><button name="rating" value="2" tweetid="'.$tweets[$i]['tweet_id'].'">S/T</button><button name="rating" value="3" tweetid="'.$tweets[$i]['tweet_id'].'">T</button></div><div class="rewrite"><select name="rewrite" tweet="'.$tweets[$i]['tweet_id'].'"><option value="text">Original</option><option value="rewrite">Rewrite</option></select></div></div>';
}

$body.="</div>";


$body.='<div id="finished">All done! <a href="/ratedTweets.php">View Rated Tweets!</a></div>';

$body.='<div id="progress">You are <span><em class="percent">0</em>%</span> done rating tweets. So far you have <span><em class="Ts">0</em> Ts</span> and <span><em class="STs">0</em> S/Ts</span></div>';

$body.='<script type="text/javascript">var jsonTweets='.json_encode($jsontweets).';</script>';
$body.='<script type="text/javascript" src="/tweets_3.js"></script>';

include("html2.php");

?>