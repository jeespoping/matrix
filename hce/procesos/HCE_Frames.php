<html>
<head>
<title>MATRIX - HCE-Historia Clinica Electronica</title>
</head>
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	echo "<frameset rows=70,* frameborder=0 framespacing=0>";
		echo "<frame src='HCE.php?accion=T&ok=0&empresa=".$empresa."' name='titulos' marginwidth=0 scrolling='no' marginheiht=0>";
		echo "<frameset cols=20%,80%  frameborder=0 framespacing=2>";
			echo "<frameset rows=8%,22%,70% frameborder=0 framespacing=0>";
				echo "<frame src='HCE.php?accion=U&ok=0&empresa=".$empresa."' name='usuario' marginwidth=0 scrolling='no' marginheiht=0>";
				echo "<frame src='HCE.php?accion=A&ok=0&empresa=".$empresa."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."' name='alergias' marginwidth=0 marginheiht=0>";
				echo "<frame src='HCE.php?accion=F&ok=0&empresa=".$empresa."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."' name='formularios' marginwidth=0 marginheiht=0>";
			echo "</frameset>";
			echo "<frameset rows=120,* frameborder=0 framespacing=0>";
				echo "<frame src='HCE.php?accion=D&ok=0&empresa=".$empresa."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."' name='demograficos' marginwidth=0 marginheiht=0>";
				echo "<frameset cols=85%,15%  frameborder=0 framespacing=2>";
					echo "<frame src='HCE.php?accion=M&ok=0&empresa=".$empresa."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."' name='principal' marginwidth=0 marginheiht=0>";
					echo "<frame src='HCE.php?accion=UT&ok=0&empresa=".$empresa."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."' name='utilidades' marginwidth=0 marginheiht=0>";
				echo "</frameset>";		
			echo "</frameset>";
		echo "</frameset>";
	echo "</frameset>";
}
?>	
</html>