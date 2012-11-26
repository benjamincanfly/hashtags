<?php

	require_once("config.php");

	$qs=sprintf("update tweets set rating_%s='%s' where tweet_id='%s'",
		mysql_real_escape_string($_POST['level']),
		mysql_real_escape_string($_POST['rating']),
		mysql_real_escape_string($_POST['id']));
	$q=mysql_query($qs);

?>