<?php

include("config.php");

$qs=sprintf("select tweet, rating from faves where hashtag='%s'",
	mysql_real_escape_string($_REQUEST['hashtag']));
$q=mysql_query($qs);


$tweets=array();

while($thisTweet = mysql_fetch_assoc($q)){
	$tweets[$thisTweet['tweet']] = $thisTweet['rating'];
}

echo json_encode($tweets);

?>