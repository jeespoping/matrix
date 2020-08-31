<html>
<head>
  	<title>MATRIX Programa Para Inventario Fisico x Palm</title>
  	      <link rel="stylesheet" href="/styles.css" type="text/css">
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
</head>
<body  onload=ira() BGCOLOR="FFFFFF" oncontextmenu = "return false" onselectstart = "return true" ondragstart = "return false">
<script type="text/javascript">
<!--
	function enter()
	{
		document.forms.Fisico_Palm.submit();
	}
//-->
</script>
<BODY TEXT="#000066">
<?php
include_once("conex.php");


/**********************************************************************************************************************  
	   PROGRAMA : Fisico_Palm.php
	   Fecha de Liberación : 2006-03-01
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2006-09-14
	   
	   OBJETIVO GENERAL : 
	   Este programa permite la grabacion de un inventario fisico x fecha y conteo para realizar ajustes en el teorico del Pos.
	   
	   
	   REGISTRO DE MODIFICACIONES :
	   
	   .2006-03-01
	  	 	Programa Nuevo
	  	 	
	   .2006-09-14
	  	 	Se cambio la validacion del programa para que aceptara cantidad cero (0) como dato.
	  	 	
	   .2007-07-27
	   		Se cambio la selecion de centros de costos para que seleccionara unicamente los activos.
	   
***********************************************************************************************************************/
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='Fisico_Palm' action='Fisico_Palm.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(isset($ok) and $wcan >= 0 and $wart != "" and $wdes != "")
	{
		$wsw=0;
		$query = "SELECT Artdes from ".$empresa."_000001 where Artcod='".$wart."'";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num>0)
			$wsw=1;
		if($wsw == 1)
		{
		$fecha = date("Y-m-d");
			$hora = (string)date("H:i:s");
			$query = " insert ".$empresa."_000002(Medico,Fecha_data,Hora_data, Fisano, Fismes, Fiscco, Fisfec, Fisart, Fiscon, Fiscan, Fisest, Seguridad) values ('".$empresa."','".$fecha."','".$hora."','".date("Y")."','".date("m")."','".substr($wcco,0,strpos($wcco,"-"))."','".$wfec."','".$wart."',".$wcon.",".$wcan.", 'on'  , 'C-".$key."')";
			$err2 = mysql_query($query,$conex);
			switch (mysql_errno())
			{
				case 0:
					echo "<font size=1><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99CCFF LOOP=-1>DATOS OK!!!!</MARQUEE></FONT>";
				break;
				case 1062:
					echo "<font size=1><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF9900 LOOP=-1>ESTE ARTICULO YA FUE GRABADO!!!!</MARQUEE></FONT>";
				break;
			}
		}
		else
			echo "<font size=1><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>ARTICULO NO EXISTE -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
		unset($wart);
		unset($wcan);
	}
	else
		if(isset($wfec) and isset($wcon) and isset($ok))
			echo "<font size=1><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FFFF00 LOOP=-1>ERROR EN LOS DATOS -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
	unset($ok);
	if (!isset($wfec) or !isset($wcon) or $wcon < 1 or $wcon > 3)
	{
		echo "<table border=0 align=left>";
		echo "<tr><td align=center bgcolor=#999999><font size=1><b>GRABACION DE INVENTARIO FISICO</font></td></tr>";
		echo "<tr><td align=center bgcolor=#cccccc><font size=1><b>Ver. 2006-03-01</font></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center><font size=1>FECHA : </font></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center><font size=1><input type='TEXT' name='wfec' size=10 maxlength=10 value='".date("Y-m-d")."'></font></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center><font size=1>CONTEO : </font></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center><font size=1><input type='TEXT' name='wcon' size=2 maxlength=2></font></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center><font size=1>C. COSTOS : </font></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>";
		$query = "SELECT Ccocod, Ccodes  from ".$empresa."_000003 WHERE Ccoest='on' order by Ccocod";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<select name='wcco'>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($wccoo == $row[0]."-".$row[1])
					echo "<option selected><font size=1>".$row[0]."-".$row[1]."</font></option>";
				else
					echo "<option><font size=1>".$row[0]."-".$row[1]."</font></option>";
			}
			echo "</select>";
		}
		echo "</td></tr>";
		echo "<tr><td align=center bgcolor=#cccccc><input type='submit' value='Ok'></td></tr>"; 
		echo "</table>";  
	}
	else
	{
		$wdes="";
		$wcolor="#cccccc";
		echo "<table border=0 align=leff>";
		echo "<tr><td align=center bgcolor=#999999><font size=1><b>GRABACION DE INVENTARIO FISICO</font></td></tr>";
		echo "<tr><td align=center bgcolor=#cccccc colspan=1><font size=1><b>Ver. 2006-03-01</font></td></tr>";
		echo "<tr><td bgcolor=#dddddd align=center><font size=1>ARTICULO : </font></td></tr>";
		if(isset($wart) and $wart != "")
		{
			?>
			<script>
				function ira(){document.Fisico_Palm.wcan.focus();}
			</script>
			<?php
			echo "<tr><td bgcolor=#dddddd align=center><input type='TEXT' name='wart' size=20 maxlength=20 value='".$wart."'></td></tr>";
			$query = "SELECT Artdes from ".$empresa."_000001 where Artcod='".$wart."'";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<tr><td bgcolor=#cccccc align=center><font size=1>DESCRIPCION : ".$row[0]."</font></td></tr>";
					$wdes=$row[0];
				}
			}
			else
			{
				// ***** AQUI BUSQUEDA X CODIGO DE PROVEEDOR *****
				$query = "SELECT Artcod, Artnom from farmpda_000009 where Artcba='".$wart."'";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						echo "<tr><td bgcolor=#cccccc align=center><font size=1>DESCRIPCION : ".$row[1]."</font></td></tr>";
						$wdes=$row[1];
						$wart=$row[0];
						echo "<input type='HIDDEN' name= 'wart' value='".$wart."'>";
					}
				}
				else
				{
					echo "<tr><td bgcolor=#cccccc align=center><font size=1>ERROR - ARTICULO NO EXISTE !!!! </font></td></tr>";
					$wart="";
					$wdes="";
					echo "<input type='HIDDEN' name= 'wcan' value='".$wart."'>";
				}
			}
		}
		else
		{
			?>
			<script>
				function ira(){document.Fisico_Palm.wart.focus();}
			</script>
			<?php
			echo "<tr><td bgcolor=#dddddd align=center><input type='TEXT' name='wart' size=20 maxlength=20></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center><font size=1>DESCRIPCION : </font></td></tr>";
		}
		echo "<tr><td bgcolor=#dddddd align=center><font size=1>CANTIDAD : </font></td></tr>";
		$wuni="";
		if(isset($wart) and $wart != "")
		{
			$query = "SELECT Artuni from ".$empresa."_000001 where Artcod='".$wart."'";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
					$row = mysql_fetch_array($err);
					$wuni=$row[0];
			}
		}
		if(!isset($wcan))
			echo "<tr><td bgcolor=#dddddd align=center><input type='TEXT' name='wcan' size=5 maxlength=10><font size=1> Unidad : ".$wuni."</font></td></tr>";
		else
			echo "<tr><td bgcolor=#dddddd align=center><input type='TEXT' name='wcan' size=5 maxlength=10 value=".$wcan."><font size=1> Unidad : ".$wuni."</font></td></tr>";
		echo "<input type='HIDDEN' name= 'wfec' value='".$wfec."'>";
		echo "<input type='HIDDEN' name= 'wcon' value='".$wcon."'>";
		echo "<input type='HIDDEN' name= 'wcco' value='".$wcco."'>";
		echo "<input type='HIDDEN' name= 'wdes' value='".$wdes."'>";
		echo "<tr><td align=center bgcolor=#dddddd><font size=1><b>GRABAR</b></font><input type='checkbox' name='ok'  onclick='enter()'></td></tr>";
		echo "<tr><td align=center bgcolor=#999999><input type='submit' value='Ok'></td></tr>";
		echo "</table><br><br>"; 
	}
}
?>