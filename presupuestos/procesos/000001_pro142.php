<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Consolidacion De Facturacion</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro142.php Ver. 2016-04-29</b></font></td></tr></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='000001_pro142.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	

	

	if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>>CONSOLIDACION DE FACTURACION</td></tr>";
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
		$wemp=substr($wemp,0,strpos($wemp,"-"));
		$wres=strtoupper ($wres);
		$query = "delete from ".$empresa."_000137  ";
		$query = $query."  where fddano =  ".$wanop;
		$query = $query."    and fddemp = '".$wemp."' ";
		$query = $query."    and fddmes =  ".$wper1;
		$err = mysql_query($query,$conex);
		//                   0       1       2       3       4         5            6             7
		$query = "SELECT   Empcin, Fdtcco, Fdtcon, Fdtcod, Fdttip, sum(Fdtcan), sum(Fdtite), sum(Fdtipr)  from ".$empresa."_000136,".$empresa."_000061  ";
		$query = $query."  where Fdtano =  ".$wanop;
		$query = $query."    and Fdtemp = '".$wemp."' ";
		$query = $query."    and Fdtmes =  ".$wper1;
		$query = $query."    and Fdtemp =  Empemp " ;
		$query = $query."    and Fdtnit =  Epmcod " ;
		$query = $query."  group by  Empcin, Fdtcco, Fdtcon, Fdtcod, Fdttip " ;
		$query = $query."  union all ";
		$query = $query."  SELECT   '991' as Empcin, Fdtcco, Fdtcon, Fdtcod, Fdttip, sum(Fdtcan), sum(Fdtite), sum(Fdtipr)  from ".$empresa."_000136  ";
		$query = $query."  where Fdtano =  ".$wanop;
		$query = $query."    and Fdtemp = '".$wemp."' ";
		$query = $query."    and Fdtmes =  ".$wper1;
		$query = $query."    and Fdtnit NOT IN (select Epmcod from  ".$empresa."_000061 where Empemp = '".$wemp."')" ;
		$query = $query."  group by  Empcin, Fdtcco, Fdtcon, Fdtcod, Fdttip " ;
		$query = $query."  order by  Empcin, Fdtcco, Fdtcon, Fdtcod, Fdttip " ;
		//echo $query."<br>";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		$count=0;
		for ($i=0;$i<$num;$i++)
		{	
			$row = mysql_fetch_array($err);
			$fecha = date("Y-m-d");
			$hora = (string)date("H:i:s");
			$query = "insert ".$empresa."_000137 (medico,fecha_data,hora_data,Fddemp, Fddano, Fddmes, Fddcco, Fddcon, Fddcod, Fddnit, Fddcan, Fddite, Fddipr, Fddtip, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wper1.",'".$row[1]."','".$row[2]."','".$row[3]."','".$row[0]."',".$row[5].",".$row[6].",".$row[7].",'".$row[4]."','C-".$empresa."')";
			$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()." Error en la Insercion de la Facturacion Definitiva");
			$count++;
			echo "REGISTROS INSERTADOS : ".$count."<BR>";
		}
		echo "<B>REGISTROS ADICIONADOS : ".$count."</B><BR>";
	}
}
?>
</body>
</html>
