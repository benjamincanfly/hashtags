<?php
	
	session_start();
	
	echo "Request:".print_r($_REQUEST, true);
	
	foreach($_REQUEST as $key=>$value){
		$_SESSION[$key]=$value;
	}
	
	echo "Session:". print_r($_SESSION, true);
	
?>