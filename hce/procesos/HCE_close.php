<?php
include_once("conex.php");
	@session_start();
	if(!isset($_SESSION["user"]))
		echo "error";
	else
	{
		$COD = $_POST['whce'];
		if($COD == 0)
			$_SESSION["HCEON"] = 0;
		else
			echo $_SESSION["HCEON"];
	}
?>
