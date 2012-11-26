<?php
	
	//error_reporting(E_ALL);
	
	error_reporting(0);
	
	set_time_limit(360);

	$link=mysql_connect("127.0.0.1", "bapple_ht", "vaejEMjs");
	mysql_select_db("bapple_ht", $link);

	$config=array();

	$qs="select * from config";
	$q=mysql_query($qs);

	while($thisSetting = mysql_fetch_assoc($q)){
		$config[$thisSetting['configKey']] = $thisSetting['configValue'];
	}
	
	$body=$body||"";
	$head=$head||"";
	
	function body($code){
		global $body;
		$body.=$code;
	}
	
	function head($code){
		global $head;
		$head.=$code;
	}
?>