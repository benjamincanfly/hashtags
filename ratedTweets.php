<?php

include("config.php");

$pageID="view";

$body="";

$body.="<h2>Rated #".$config['hashtag']." tweets</h2><br/><br/>";

$qs=sprintf("select * from faves where hashtag='%s' order by tweet DESC", mysql_real_escape_string($config['hashtag']));
$q=mysql_query($qs);

$tweets=array();

$jsontweets=array();

while($tweet=mysql_fetch_assoc($q)){
	$tweets[]=$tweet;
	$jsontweets[$tweet['tweet']]=$tweet;
}

$tweets3="";
$tweets2="";
$tweets1="";
$tweets0="";

//print_r($tweets);

$numOf=array();

for($i=0;$i<count($tweets);$i++){
	$tweetCode='<div class="tweet" tweetid="'.$tweets[$i]['tweet'].'" rating="'.$tweets[$i]['rating'].'"><div class="text">'.$tweets[$i]['text'].'</div><br/></div>';
	
	//echo $tweets[$i]['rating']." ";
	
	$numOf[$tweets[$i]['rating']]++;
	
	switch ($tweets[$i]['rating']){
		case '3':
		$tweets3.=$tweetCode;
		break;
		
		case '2':
		$tweets2.=$tweetCode;
		break;
		
		case '1':
		$tweets1.=$tweetCode;
		break;
		
		default:
		$tweets0.=$tweetCode;
		break;
	}
}

$body.="<div id='viewControls' rating='3'><span class='viewButtons'><input type='button' which='3' value='Ts (".$numOf[3].")' rating='3'/><input type='button' which='2' value='S/Ts (".$numOf[2].")' rating='2'/><input type='button' which='1' value='Ss (".$numOf[1].")' rating='1'/><input type='button' which='0' value='Un-rated (".$numOf[0].")' rating='0'/></span><div id='formatting'><input type='checkbox' name='includehash' id='includehashtag'/><label for='includehashtag'>Include hashtag</label>&nbsp;&nbsp;&nbsp;<input type='checkbox' name='includejimmy' id='includejimmy'/><label for='includejimmy'>Include @jimmyfallon</label>&nbsp;&nbsp;&nbsp;<input type='checkbox' name='includeusername' id='includeusername'/><label for='includeusername'>Include username</label>&nbsp;&nbsp;&nbsp;<input type='checkbox' name='dofont' id='dofont'/><label for='dofont'>12 pt Times</label><!--&nbsp;&nbsp;&nbsp;Sort by: <select name='sort'><option value='tweet'>Newest</option><option value='username'>Username</option></select>--></div></div><br/><br/><div id='tweets' class='read' rating='3'><div id='tweets3' class='tweets' rating='3'>".$tweets3."</div><div id='tweets2' class='tweets' rating='2'>".$tweets2."</div><div id='tweets1' class='tweets' rating='1'>".$tweets1."</div><div id='tweets0' class='tweets' rating='0'>".$tweets0."</div></div>";

$body.='<script type="text/javascript">var jsonTweets='.json_encode($jsontweets).';</script>';
$body.='<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>';
$body.='<script type="text/javascript" src="/view.js"></script>';

include("html2.php");

?>