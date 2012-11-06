<?php

include("config.php");

$qs=sprintf("update faves set rating='%s' where tweet='%s'",
	mysql_real_escape_string($_POST['rating']),
	mysql_real_escape_string($_POST['tweet']));
$q=mysql_query($qs);

?>