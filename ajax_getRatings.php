<?php

require_once("config.php");

$qs=sprintf("select tweet_id, rating_3 from tweets where hashtag='%s' and (rating_1='2' or rating_2='1')",
	mysql_real_escape_string($_REQUEST['hashtag']));
$q=mysql_query($qs);

$tweets=array();

while($thisTweet = mysql_fetch_assoc($q)){
	$tweets[$thisTweet['tweet_id']] = $thisTweet['rating_3'];
}

echo json_encode($tweets);

?>