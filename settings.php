<?php

require_once("config.php");

$body="";
$pageID='settings';

if($_POST){
		
	$qs=sprintf("update config set configValue='%s' where configKey='hashtag'", mysql_real_escape_string($_POST['hashtag']));
	$q=mysql_query($qs);
	
	$qs=sprintf("update config set configValue='%s' where configKey='favers'", mysql_real_escape_string($_POST['favers']));
	$q=mysql_query($qs);
	
	$body.="<p>Settings saved.</p><br/>";
	
}

$body.="<form action='settings.php' method='post'>Hashtag: <input type='text' name='hashtag' value='".$config['hashtag']."'/><br/><br/>Twitter accounts with cross-favorited tweets:<br/><textarea name='favers' cols='35'>".$config['favers']."</textarea><div class='note'>Separate with a space, e.g. <i>thisuser thatuser anotheruser</i></div><br/><input type='submit' value='Save settings'/></form>";

include("html.php");

?>