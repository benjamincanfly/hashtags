<?php
	
	require_once("config.php");
	$pageID="tweets_4";
	
	$qs=sprintf("select * from tweets where hashtag='%s' and (rating_1='2' or rating_2='1') order by tweet DESC", mysql_real_escape_string($config['hashtag']));
	$q=mysql_query($qs);
	
	$tweets=array();
	
	$jsontweets=array();
	
	while($tweet=mysql_fetch_assoc($q)){
		$tweets[]=$tweet;
		$jsontweets[$tweet['tweet_id']]=$tweet;
	}
	

	if(count($tweets)==0){
		body('<div id="none">There\'s nothing here yet.</div>');
		include("html.php");
		die();
	}
	
	$tweets3="";
	$tweets2="";
	$tweets1="";
	$tweets0="";
	$tweetall0="";
	
	//print_r($tweets);
	
	$numOf=array();
	
	for($i=0;$i<count($tweets);$i++){
		$numOf[$tweets[$i]['rating_3']]++;
		$numOf['all']++;
		
		$tweetCode='<div class="tweet" tweet-id="'.$tweets[$i]['tweet_id'].'" rating="'.$tweets[$i]['rating_3'].'"><div class="text">'.$tweets[$i]['tweet'].'</div></div>';
		
		$tweetsall.=$tweetCode;
		
		
		switch (intval($tweets[$i]['rating_3'])){
			case 3:
			$tweets3.=$tweetCode;
			break;
			
			case 2:
			$tweets2.=$tweetCode;
			break;
			
			case 1:
			$tweets1.=$tweetCode;
			break;
			
			default:
			$tweets0.=$tweetCode;
			break;
		}
	}
	head('<link type="text/css" rel="stylesheet" href="/tweets.css"/>');
	head('<link type="text/css" rel="stylesheet" href="/tweets_4.css"/>');
	
	head('<script type="text/javascript">var json_tweets='.json_encode($jsontweets).';</script>');
	head('<script type="text/javascript" src="/tweet_favoriting.js"></script>');
	head('<script type="text/javascript" src="/tweets_4.js"></script>');
	
	$body.="<h2>Rated #".$config['hashtag']." tweets</h2><br/><br/>";
	
	$body.="<div id='viewControls' rating='3'><span class='viewButtons'><input type='button' which='3' value='Ts (".$numOf[3].")' rating='3'/><input type='button' which='2' value='S/Ts (".$numOf[2].")' rating='2'/><input type='button' which='1' value='Ss (".$numOf[1].")' rating='1'/><input type='button' which='0' value='Un-rated (".($numOf[0]>0?$numOf[0]:'0').")' rating='0'/><input type='button' which='all' rating='all' value='All (".$numOf['all'].")'/></span><div id='formatting'><input type='checkbox' name='includehash' id='includehashtag'/><label for='includehashtag'>Hashtags</label>&nbsp;&nbsp;&nbsp;<input type='checkbox' name='includejimmy' id='includejimmy'/><label for='includejimmy'>@jimmys</label>&nbsp;&nbsp;&nbsp;<input type='checkbox' name='includeusername' id='includeusername'/><label for='includeusername'>Usernames</label>&nbsp;&nbsp;&nbsp;<input type='checkbox' name='dofont' id='dofont'/><label for='dofont'>12 pt Times</label>&nbsp;&nbsp;&nbsp;<input type='checkbox' name='shownumbers' id='shownumbers'/><label for='shownumbers'>Number & divide by</label> <select name='divideby'><option value='2'>2</option><option value='3' selected='selected'>3</option><option value='4'>4</option></select><!--&nbsp;&nbsp;&nbsp;Sort by: <select name='sort'><option value='tweet'>Newest</option><option value='username'>Username</option></select>--></div></div><br/><br/><div id='tweets' class='read' rating='3'><div id='tweets3' class='tweets' rating='3'>".$tweets3."</div><div id='tweets2' class='tweets' rating='2'>".$tweets2."</div><div id='tweets1' class='tweets' rating='1'>".$tweets1."</div><div id='tweets0' class='tweets' rating='0'>".$tweets0."</div><div id='tweetsall' class='tweets' rating='all'>".$tweetsall."</div></div>";

	include("html.php");
	
?>