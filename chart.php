<?php
	
	require_once("config.php");
	
	head('<link type="text/css" rel="stylesheet" href="/chart.css"/>');
	
	body('<h2 class="header">Hello, here are charts of tweets.</h2>');

	$qs=sprintf("SELECT hashtag FROM `tweets` group by hashtag order by ctime DESC");
	$q=mysql_query($qs);
	
	$hashtags=array();
	while($row=mysql_fetch_assoc($q)){
		$hashtags[]=$row['hashtag'];
	}
	
	body(mysql_error());
	body(count($hashtags));
	
	for($h=0;$h<count($hashtags);$h++){
		
		//if($h>0){break;}
		
		$hashtag=$hashtags[$h];
		
		body('<h3>'.$hashtag.'');
		
		$qs=sprintf("select tweet_id, username, tweet, ttime from tweets where hashtag='%s' order by tweet_id ASC", mysql_real_escape_string($hashtag));
		
		$q=mysql_query($qs);
		
		$saved_tweets=array();
		$saved_tweets_assoc=array();
		
		while($tweet=mysql_fetch_assoc($q)){
			$saved_tweets[]=$tweet;
			$saved_tweets_assoc[$tweet['tweet_id']]=$tweet;
		}
		
		body(' - '.count($saved_tweets).' tweets</h3><br/>');
		
		$first_tweet=$saved_tweets[0];
		$last_tweet=$saved_tweets[count($saved_tweets)-1];
		
		$tweetsInMinute=array();
		
		for($i=0;$i<count($saved_tweets);$i++){
			$thisMinute=floor(strtotime($saved_tweets[$i]['ttime'])/60);
			$tweetsInMinute[$thisMinute]=isset($tweetsInMinute[$thisMinute])?($tweetsInMinute[$thisMinute]+1):1;
		}
		
		//body('<br/>Test:<pre>'.print_r($tweetsInMinute,true).'<br/>');
		
		$numKeys=array_keys($tweetsInMinute);
		asort($numKeys);
		
		$numOfMinutes=$numKeys[count($tweetsInMinute)-1]-$numKeys[0];
		
		//body($numOfMinutes);
		
		//body('<pre>'.print_r($tweetsInMinute, true).'</pre>');
		//body('<pre>'.print_r($numKeys, true).'</pre>');
		
		$tweetCount=count($saved_tweets);
		
		$pixels="";
	
		$j=0;
	
		$most=0;
		
		$hour=60;
		
		$measures="";
		
		$numOfHours=0;
		$origHour=false;
		
		//echo $numOfMinutes." minutes<br/>";
		
		//body('<br/>1: '.$numKeys[0].'<br/>2: '.$numKeys[count($numKeys)-1]);
		
		for($i=$numKeys[0];$i<$numKeys[count($numKeys)-1];$i++){
			
			//body('<br/>test');
			
			if(!isset($tweetsInMinute[$i])){$j++; /*body('<br/>nothing at '.$i);*/ continue;}
			
			$thisHour=strftime('%H', $i*60);
			
			//body('<br/>hour: '.$thisHour);
			
			if(!$origHour){$origHour=$thisHour;}
			
			$pixels.='<div style="left:'.((($i-$numKeys[0])/1440)*100).'%;height:'.$tweetsInMinute[$i].'px;"></div>';
			$most=max($most, $tweetsInMinute[$i]);
			
			if($thisHour!=$hour){
				$numOfHours++;
				$hour=$thisHour;
				$measures.='<div class="hour" style="left:'.((($i-$numKeys[0])/1440)*100).'%;">'.$thisHour.'</div>';
			}
			
			if($numOfHours>=10 && $thisHour>=$origHour){break;}
			
			$j++;
		}
		
		$most_hundreds=ceil($most/100)*100;
		$num_of_hundreds=($most_hundreds/100)+1;
		
		for($i=0;$i<$num_of_hundreds;$i++){
			$measures.='<span style="bottom:'.($i*100).'px;"><em>'.($i*100).'/min</em></span>';
		}
		
		//body('Most hundreds: '.$most_hundreds);
		//body('Num of hundreds: '.$num_of_hundreds);
		
		body('<div class="chart_wrapper">'.$measures.'<div class="chart" style="height:'.($most_hundreds-1).'px">'.$pixels.'</div></div>');
	
	}
	
	include("html.php");

?>