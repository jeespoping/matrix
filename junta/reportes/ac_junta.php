<html>
<head>
	<META name="DownloadOptions" content="nosave">
  	<title>MATRIX ACTAS DE JUNTA DIRECTIVA</title>
	<style type="text/css">
	<!--
		.BlueThing
		{
			background: #99CCFF;
		}
		
		.SilverThing
		{
			background: #CCCCCC;
		}
		
		.GrayThing
		{
			background: #CCCCCC;
		}
	
	//-->
	</style>
	<script type="text/javascript">
		<!--
			function ejecutar(path)
			{
				window.open(path,'','fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=0,scrollbars=0,titlebar=0');
			}
		//-->
	</script>
</head>

<script type="text/javascript">
<!--
	function enter()
	{
		document.forms.ac.submit();
	}
//-->
</script>
<BODY TEXT="#000066">
<?php
include_once("conex.php");

/**********************************************************************************************************************  
	   PROGRAMA : ac_junta.php
	   Fecha de Liberación : 2006-05-09
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2007-01-18
	   
	   OBJETIVO GENERAL : 
	   Este programa permite visualizar las actas de Junta Directiva del año que usted haya seleccionado.
	   Las actas se encuentran en formato PDF. En el directorio de imagenes del usuario de desarrollo junta.
	   
	   
	   REGISTRO DE MODIFICACIONES :
			
	   .2006-05-09
	   		Inicio del Programa a Produccion.
	   
	   .2007-01-18
	   		Se modifico el programa para generalizarlo para la junta directiva de cualquier empresa del grupo.

	   
***********************************************************************************************************************/
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='ac' action='ac_junta.php' method=post>";
	echo "<input type='HIDDEN' name= 'grupo' value='".$grupo."'>";
	

	

	if(!isset($wano))
	{
		echo "<center><table border=0>";
		switch($grupo)
		{
			case "junta":
				echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			break;
			case "juntacs":
				echo "<tr><td align=center colspan=2><b>LAS AMERICAS CLINICA DEL SUR<b></td></tr>";
			break;
		}
		echo "<tr><td align=center colspan=2>ACTAS DE JUNTA DIRECTIVA</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Año</td><td bgcolor=#cccccc align=center>";
		switch($grupo)
		{
			case "junta":
				$query = "SELECT Actano from junta_000003 GROUP BY Actano";
			break;
			case "juntacs":
				$query = "SELECT Acsano from junta_000004 GROUP BY Acsano";
			break;
		}
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<select name='wano'>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				echo "<option>".$row[0]."</option>";
			}
			echo "</select>";
		}
		echo "</td></tr>";
		echo "<tr><td align=center colspan=2 bgcolor=#cccccc><input type='RADIO' name=wtip1 value=1 onclick='enter()'>CONTINUAR</td></tr>";
		echo "</table>"; 
	} 
	else
	{
		switch($grupo)
		{
			case "junta":
				$query = "select Actano, Actcod, Actfec, Actnom   from  junta_000003 ";
				$query .= "  where Actano =".$wano;
				$query .= "    order by Actcod ";
			break;
			case "juntacs":
				$query = "select Acsano, Acscod, Acsfec, Acsnom   from  junta_000004 ";
				$query .= "  where Acsano =".$wano;
				$query .= "    order by Acscod ";
			break;
		}
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		echo "<table border=0 align=center>";
		switch($grupo)
		{
			case "junta":
				echo "<tr><td align=center colspan=2><IMG SRC='/matrix/images/medical/junta/promotora.png' width=50%></td></tr>";
			break;
			case "juntacs":
				echo "<tr><td align=center colspan=2><IMG SRC='/matrix/images/medical/juntacs/logo_clisur.png' width=50%></td></tr>";
			break;
		}
		echo "<tr><td align=center bgcolor=#000066 colspan=2><font color=#ffffff size=4><b>ACTAS DE JUNTA DIRECTIVA DE ".$wano."</font><font color=#33CCFF size=4>&nbsp&nbsp&nbspVer. 2006-07-28</font></b></font></td></tr>";
		echo "</table><br>";  
		echo "<table border=0 align=center>";
		echo "<tr><td align=right colspan=4><font size=2>Powered by :  MATRIX</font></td></tr>";
		echo "<tr><td align=center bgcolor=#000066><font color=#ffffff >AÑO</font></td><td align=center bgcolor=#000066><font color=#ffffff >CONSECUTIVO</font></td><td align=center bgcolor=#000066><font color=#ffffff >FECHA</font></td><td align=center bgcolor=#000066><font color=#ffffff >NOMBRE</font></td></tr>";
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			if($i % 2 == 0)
				$color="#dddddd";
			else
				$color="#ffffff";
			echo "<tr style='cursor:pointer;'>";
			echo "<td bgcolor=".$color.">".$row[0]."</td>";	
			echo "<td bgcolor=".$color." align=CENTER>".$row[1]."</td>";	
			echo "<td bgcolor=".$color." align=CENTER>".$row[2]."</td>";
			//$path="/matrix/verPDF.php?documento=/matrix/images/medical/".$grupo."/".$row[3];
			$path="/matrix/verPDF.php?documento=".$row[3]."&wgrupo=".$grupo;
			
			//echo "<td bgcolor=".$color." align=CENTER><A HREF='/matrix/images/medical/".$grupo."/".$row[3]."' target = '_blank'>".$row[3]."</a></td>";
			echo "<td bgcolor=".$color." align=CENTER><A HREF='/matrix/images/medical/".$grupo."/".$row[3]."' target = '_blank'>".$row[3]."</a></td>";
			//echo "<td bgcolor=".$color." align=CENTER onclick='ejecutar(".chr(34).$path.chr(34).")'>".$row[3]."</td>";
			echo "</tr>";
		}
		echo "<tr><td align=center bgcolor=#999999 colspan=9><input type='submit' value='Continuar'></td></tr>";
		echo "</table><br><br>"; 
	}
}
?>
