<?php
include_once("conex.php");
@session_start();

if(!isset($_SESSION['user'])){	
	echo "error";	
}else{
	
	$cod = $_POST['wordenes'];
	
	if($cod == 0){
		$_SESSION['wordenes'] = 0;	
	}else{
		echo $_SESSION['wordenes'];		
	}
	
}


?>