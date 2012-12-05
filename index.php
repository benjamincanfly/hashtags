<?php

	require_once("config.php");

	$body="";

	$body.="Welcome! Here's what you can do:<ol id='options'><li><a href='/tweets_1.php'>Favorite Tweets</a> - read through a flood of tweets to favorite the good ones or \"gold\" the great ones.</li><li><a href='/tweets_2.php'>Cross-Favorite Tweets</a> - read through another hashtagger's faved tweets and mark the ones you like too.</li><li><a href='/tweets_3.php'>Rate Tweets</a> - go through all the remaining tweets and rate them as S, S/T, or T</li><li><a href='/tweets_4.php'>View Rated Tweets</a> - a categorized list of this week's rated tweets which you can easily copy or save.</li></ol>";

	include("html.php");
	
?>