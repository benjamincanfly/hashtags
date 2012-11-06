<?php

include("config.php");

$pageID="rate";

$body="";

$qs=sprintf("select * from faves where hashtag='%s' order by tweet DESC", mysql_real_escape_string($config['hashtag']));
$q=mysql_query($qs);

$tweets=array();

$jsontweets=array();

while($tweet=mysql_fetch_assoc($q)){
	$tweets[]=$tweet;
	$jsontweets[$tweet['tweet']]=$tweet;
}

$body.="<h2 class='header'>Rate #".$config['hashtag']." tweets already cross-favorited by LNJF hashtaggers</h2>";

$body.="<div id='rateFormatting'><input type='checkbox' name='removestuff' id='removestuff' checked='checked'/> <label for='removestuff'>Remove hashtags and @jimmys</label></div>";

$qs=sprintf("select * from faves where hashtag='%s' order by tweet DESC", mysql_real_escape_string($config['hashtag']));
$q=mysql_query($qs);

$tweets=array();

while($tweet=mysql_fetch_assoc($q)){
	$tweets[]=$tweet;
}

$body.="<div id='tweets'>";

for($i=0;$i<count($tweets);$i++){
	$body.='<div class="tweet" tweetid="'.$tweets[$i]['tweet'].'" rating="'.$tweets[$i]['rating'].'"><div class="text">'.$tweets[$i]['text'].'</div><div class="controls"><button name="rating" value="1" tweetid="'.$tweets[$i]['tweet'].'">S</button><button name="rating" value="2" tweetid="'.$tweets[$i]['tweet'].'">S/T</button><button name="rating" value="3" tweetid="'.$tweets[$i]['tweet'].'">T</button></div></div>';
}

$body.="</div>";


$body.='<div id="finished">Fuck yeah! All done! <a href="/ratedTweets.php">View Rated Tweets!</a></div>';

$body.='<div id="progress">You are <span><em class="percent">0</em>%</span> done rating tweets. So far you have <span><em class="Ts">0</em> Ts</span> and <span><em class="STs">0</em> S/Ts</span></div>';

$body.='<script type="text/javascript">var jsonTweets='.json_encode($jsontweets).';</script>';
$body.='<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>';
$body.='<script type="text/javascript" src="/rate.js"></script>';

include("html2.php");

?>