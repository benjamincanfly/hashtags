<?php

	//error_reporting(E_ALL);
	//ini_set('display_errors', 'On');
	
	error_reporting(0);
	ini_set('display_errors', 'Off');
	
	session_start();
	
	
	set_time_limit(360);
	
	$link=mysql_connect("127.0.0.1", "bapple_ht", "vaejEMjs");
	mysql_select_db("bapple_ht", $link);

	$config=array();

	$qs="select * from config";
	$q=mysql_query($qs);

	while($thisSetting = mysql_fetch_assoc($q)){
		$config[$thisSetting['configKey']] = $thisSetting['configValue'];
	}
	
	$body=isset($body)?$body:"";
	$head=isset($head)?$head:"";
	
	function body($code){
		global $body;
		$body.=$code.'';
	}
	
	function console($code){
		global $body;
		$body.=$code.'<br/>';
	}
	
	function head($code){
		global $head;
		$head.=$code;
	}
	
?>