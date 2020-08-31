<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Consulta de Tablas en las Systables de Informix</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_ser01.php Ver. 2008-01-09</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
function search($tabla,$codbc,$tipo,$pos,$pla,$tabs,$cons,$path,$xc,&$wpath)
{
	if(isset($wpath[$pla][$pos][1]))
	{
		$wpath[$pla][$pos][2]=substr($wpath[$pla][$pos][2],0,strpos($wpath[$pla][$pos][2],"-"));
		$wnomtab="";
		if(isset($wpath[$wpath[$pla][$pos][3]][$wpath[$pla][$pos][2]][0]) and ($wpath[$pla][$pos][3] != $pla or $wpath[$pla][$pos][2] != $pos))
		{
			$wnomtab=$wpath[$wpath[$pla][$pos][3]][$wpath[$pla][$pos][2]][5].$wpath[$wpath[$pla][$pos][3]][$wpath[$pla][$pos][2]][4];
		}
		elseif(isset($wpath[$pla][$pos][7]))
				{
					if($wpath[$pla][$pos][6] < "2000-01-01")
					{
						$p=-1;
						while(strlen($wnomtab) < (10 - strlen($wpath[$pla][$pos][8])))
						{
							$p++;
							if(($p + 1) < strlen($wpath[$pla][$pos][0]))
								$wnomtab=$wnomtab.substr($wpath[$pla][$pos][0],$p,1);
						}
						$wnomtab=$wnomtab.$wpath[$pla][$pos][8];
						$wnomtab=str_replace(" ","_",$wnomtab);
					}
					else
					{
						$wnomtab=substr($wpath[$pla][$pos][0],0,5);
						for ($w=0;$w<5-strlen($wpath[$pla][$pos][8]);$w++)
							$wnomtab=$wnomtab."0";
						$wnomtab=$wnomtab.$wpath[$pla][$pos][8];
						$wnomtab=str_replace(" ","_",$wnomtab);
					}
				}
		if($wnomtab != "")
		{
			$query = " UPDATE systables set dirpath = '".$wnomtab."' where tabname='".$tabla."'";
			$err_o = odbc_do($codbc,$query);
		}
	}
	$query = " SELECT tabname, dirpath,tabid,created from systables where tabname='".$tabla."'";
	$err_o = odbc_do($codbc,$query);
	$campos= odbc_num_fields($err_o);
	$count=0;
	while (odbc_fetch_row($err_o))
	{
		$odbc=array();
		$odbc[4]=$tipo;
		for($m=1;$m<=$campos;$m++)
			$odbc[$m-1]=odbc_result($err_o,$m);
		echo "<tr>";
		echo "<td align=center>".$pos."</td>";
		echo "<td align=center>".$pla."</td>";
		echo "<td>".$odbc[4]."</td>";
		echo "<td>".$odbc[0]."</td>";
		echo "<td>".$odbc[1]."</td>";
		$cero=0;
		$uno=1;
		$dos=2;
		$tres=3;
		$cuatro=4;
		$cinco=5;
		$seis=6;
		$siete=7;
		$ocho=8;
		$wpath[$pla][$pos][0]=$odbc[0];
		$wpath[$pla][$pos][3]=$pla;
		$wpath[$pla][$pos][4]=$odbc[1];
		$wpath[$pla][$pos][5]=$path;
		$wpath[$pla][$pos][6]=$odbc[3];
		$wpath[$pla][$pos][8]=$odbc[2];
		echo "<input type='HIDDEN' name='wpath[".$pla."][".$pos."][".$cero."]' value='".$wpath[$pla][$pos][0]."'>";
		echo "<input type='HIDDEN' name='wpath[".$pla."][".$pos."][".$tres."]' value='".$wpath[$pla][$pos][3]."'>";
		echo "<input type='HIDDEN' name='wpath[".$pla."][".$pos."][".$cuatro."]' value='".$wpath[$pla][$pos][4]."'>";
		echo "<input type='HIDDEN' name='wpath[".$pla."][".$pos."][".$cinco."]' value='".$wpath[$pla][$pos][5]."'>";
		echo "<input type='HIDDEN' name='wpath[".$pla."][".$pos."][".$seis."]' value='".$wpath[$pla][$pos][6]."'>";
		echo "<input type='HIDDEN' name='wpath[".$pla."][".$pos."][".$ocho."]' value='".$wpath[$pla][$pos][8]."'>";
		echo "<td align=center><select name='wpath[".$pla."][".$pos."][".$dos."]'>";
		for ($j=0;$j<$cons;$j++)
			echo "<option>".$j."-".$xc[$j]."</option>";
		echo "</td>";
		echo "<td align=center><input type='checkbox' name='wpath[".$pla."][".$pos."][".$siete."]'></td></td>";
		echo "<td align=center><input type='checkbox' name='wpath[".$pla."][".$pos."][".$uno."]'></td>";
		echo "<td align=center>".$odbc[3]."</td>";
		echo "<td align=center>".$odbc[2]."</td></tr>";
	}
}
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='000001_ser01.php' method=post>";
	

	

	if(!isset($wfecha))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>CONSULTA DE TABLAS EN LAS SYSTABLES DE INFORMIX</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha de Montaje</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wfecha' size=10 maxlength=10></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		$wpath=array();
	}
	else
	{
		$xconex=array();
		echo "<center><table border=1>";
		echo "<tr><td align=center colspan=11><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center  colspan=11>APLICACION DE PRESUPUESTOS</td></tr>";
		echo "<tr><td align=center  colspan=11>CONSULTA DE TABLAS EN LAS SYSTABLES DE INFORMIX</td></tr>";
		echo "<tr><td bgcolor=#cccccc><b>CONEXION</b></td><td bgcolor=#cccccc align=center><b>NRO TABLA</b></td><td bgcolor=#cccccc><b>APLICACION</b></td><td bgcolor=#cccccc><b>TABLA</b></td><td bgcolor=#cccccc><b>RUTA</b></td><td bgcolor=#cccccc align=center><b>CONEXION<BR>ASOCIADA</b></td><td bgcolor=#cccccc><b>DESAPUNTAR</b></td><td bgcolor=#cccccc><b>ACTUALIZA</b></td><td bgcolor=#cccccc align=center><b>FECHA CREACION</b></td><td bgcolor=#cccccc><b>TABID</b></td></tr>";
		$query = "select count(*) from root_000024 where fecha='".$wfecha."'";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			$row = mysql_fetch_array($err);
			$wtab=$row[0];
		}
		else
			$wtab=-1;
		$query = "select aplicacion from root_000023 ";
		$err1 = mysql_query($query,$conex);
		$num1 = mysql_num_rows($err1);
		if($num1 > 0)
		{
			for ($i=0;$i<$num1;$i++)
			{	
				$row1 = mysql_fetch_array($err1);
				$xconex[$i]=$row1[0];
			}
			$wcon=$num1;
		}
		else
			$wcon=-1;
		$query = "select tabla from root_000024 where fecha='".$wfecha."'";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			for ($i=0;$i<$num;$i++)
			{	
				$row = mysql_fetch_array($err);
				$query = "select conexion,aplicacion,ruta from root_000023 ";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				if($num1 > 0)
				{
					for ($j=0;$j<$num1;$j++)
					{	
						$row1 = mysql_fetch_array($err1);
						$conex_o = odbc_connect($row1[0],'','') or die ("ERROR CONECTANDO A ORIGEN DE DATOS ".$row1[0]);
						search($row[0],$conex_o,$row1[1],$j,$i,$wtab,$wcon,$row1[2],$xconex,$wpath);
						
						odbc_close($conex_o);
						odbc_close_all();
					}
				}
			}
		}
		echo "<tr><td bgcolor=#cccccc  colspan=11 align=center><input type='submit' value='ENTER'></td></tr></table>";
		echo "<input type='HIDDEN' name= 'wfecha' value='".$wfecha."'>";
	}
}
?>
</body>
</html>