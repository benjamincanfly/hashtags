<?php

include("config.php");

$body="";

if($_POST){
		
	$qs=sprintf("update config set configValue='%s' where configKey='hashtag'", mysql_real_escape_string($_POST['hashtag']));
	$q=mysql_query($qs);
	
	$body.="<p>Saved! New hashtag: ".$_POST['hashtag']."</p><br/>";
	
}

$body.="<form action='settings.php' method='post'>Hashtag: <input type='text' name='hashtag' value='".$config['hashtag']."'/> <input type='submit' value='Save'/></form>";

include("config.php");

include("html2.php");

?>