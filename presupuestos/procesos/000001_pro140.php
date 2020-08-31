<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Seleccion de Gastos de Explicaciones Para Costos</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro140.php Ver. 2016-04-29</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='000001_pro140.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	

	

	if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wcco1) or !isset($wcco2) or !isset($wper1) or $wper1 < 1 or $wper1 > 12)
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>SELECCION DE GASTOS DE EXPLICACIONES PARA COSTOS</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o  Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Mes Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Unidad de Proceso Inicial</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Unidad de Proceso Final</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco2' size=4 maxlength=4></td></tr>";
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
		$query = "delete from ".$empresa."_000092  ";
		$query = $query."where Mgaano =  ".$wanop;
		$query = $query."  and Mgaemp = '".$wemp."' ";
		$query = $query."  and mgaper =  ".$wper1;
		$query = $query."  and mgacco between '".$wcco1."' and '".$wcco2."' ";
		$query = $query."  and Mgatip =  'EXPLICACIONES' ";
		$err = mysql_query($query,$conex);
		$query  = "SELECT Expcco, Expcpr, Expnit, sum(Expmon)  from ".$empresa."_000011,".$empresa."_000139  ";
		$query .= "  where Expano =  ".$wanop;
		$query .= "    and Expemp = '".$wemp."' ";
		$query .= "    and Expper =  ".$wper1;
		$query .= "    and Expcco between '".$wcco1."' and '".$wcco2."' ";
		$query .= "    and Expemp =  Pfeemp ";
		$query .= "    and Expcco =  Pfecco ";
		$query .= "    and Expnit =  Pfenit ";
		$query .= "    and Expcpr =  Pferub ";
		$query .= "    and Pfecri = 'on' ";
		$query .= "  group by Expcco, Expcpr, Expnit " ;
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		$count=0;
		for ($i=0;$i<$num;$i++)
		{	
			$row = mysql_fetch_array($err);
			$fecha = date("Y-m-d");
			$hora = (string)date("H:i:s");
			$query = "insert ".$empresa."_000092 (medico,fecha_data,hora_data,Mgaemp, Mgaano, Mgaper, Mgacco, Mgagas, Mgasga, Mgaval, Mgatip , seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wper1.",'".$row[0]."','".$row[1]."','".$row[2]."',".$row[3].",'EXPLICACIONES','C-".$empresa."')";
			$err1 = mysql_query($query,$conex) or die("Error en la Insercion del Driver");
			$count++;
			echo "REGISTROS INSERTADOS : ".$count."<BR>";
		}
		echo "<B>REGISTROS ADICIONADOS : ".$count."</B><BR>";
	}
}
?>
</body>
</html>
