<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Montaje de Informacion de Depreciacion</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro46.php Ver. 2016-12-05</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='000001_pro46.php' method=post>";
	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	echo "<center><input type='HIDDEN' name= 'ODBC' value='".$ODBC."'>";
	

	

	if(!isset($wanop) or !isset($wper1) or !isset($wemp) or $wemp == "Seleccione")
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>MONTAJE DE INFORMACION DE DEPRECIACION</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o  Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Mes Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Empresa de  Proceso</td><td bgcolor=#cccccc align=center>";
		$query = "SELECT Empcod,Empdes  from ".$empresa."_000153 order by Empcod";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<select name='wemp'>";
			echo "<option>Seleccione</option>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				echo "<option>".$row[0]."-".$row[1]."</option>";
			}
			echo "</select>";
		}
		echo "</td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$wemp = substr($wemp,0,2);
		$query = "delete from ".$empresa."_000076  ";
		$query = $query."  where depano =  ".$wanop;
		$query = $query."    and depmes =  ".$wper1;
		$query = $query."    and depemp = '".$wemp."' ";
		$err = mysql_query($query,$conex);
		$query = "SELECT query, Odbc from ".$empresa."_000049  ";
		$query = $query."  where codigo =  12";
		$query = $query."    and empresa = '".$wemp."' ";
		$err = mysql_query($query,$conex) or die("No Existe el Query Especificado Consulte la tabla ".$empresa."_000049");
		$row = mysql_fetch_array($err);
		$ODBC = $row[1];
		$conex_o = odbc_connect($ODBC,'','');
		$query = $row[0];
		$query=str_replace("ANO",$wanop,$query);
		$query=str_replace("MES",$wper1,$query);
		$err_o = odbc_do($conex_o,$query);
		$campos= odbc_num_fields($err_o);
		$count=0;
		while (odbc_fetch_row($err_o))
		{
			$odbc=array();
			for($m=1;$m<=$campos;$m++)
			{
				$odbc[$m-1]=odbc_result($err_o,$m);
			}
			if($empresa == "cyppat")
			{
				$query = "SELECT Parccf from ".$empresa."_000146 ";
				$query = $query." where parcci = '".$odbc[0]."'";
				$query = $query."   and paremp = '".$wemp."' ";
				$query = $query. "  and parcon = 'cyppat'";
				$query = $query. "  and parest = 'on'";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				if ($num1 >  0)
				{
					$row1 = mysql_fetch_array($err1);
					$odbc[0] = $row1[0];
				}
			}
			$wvdep = $odbc[2] * ($odbc[4] / 100);
			$wvact = $odbc[3] * ($odbc[4] / 100);
			$fecha = date("Y-m-d");
			$hora = (string)date("H:i:s");
			$query = "insert ".$empresa."_000076 (medico,fecha_data,hora_data,Depemp,Depano, Depmes, Depcco, Depcod, Depvde, Depvac ,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wper1.",'".$odbc[0]."','".$odbc[1]."',".$wvdep.",".$wvact.",'C-".$empresa."')";
			$err1 = mysql_query($query,$conex) or die("Error en la Insercion de la Explicacion");
			$count++;
			echo "REGISTROS INSERTADOS : ".$count."<BR>";
		}
		echo "<B>REGISTROS ADICIONADOS : ".$count."</B><BR>";
	}
}
?>
</body>
</html>
