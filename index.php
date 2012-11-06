<?php

include("config.php");

$body="";

$body.="Welcome! Here's what you can do:<ol id='options'><li><a href='/settings.php'>Settings</a> - set the current hashtag.</li><li><a href='/getFavorites.php'>Get Favorites</a> - access the 'Favorites' lists of the LNJF hashtaggers and save them in a database.</li><li><a href='/rateTweets.php'>Rate Tweets</a> - go through all the favorited tweets and rate them as S, S/T, or T</li><li><a href='/ratedTweets.php'>View Rated Tweets</a> - a categorized list of this week's rated tweets which you can easily copy or save.</li></ol>";

include("html2.php");

?>