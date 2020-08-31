<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Seleccion de Gastos de Traslados Para Costos</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro138.php Ver. 2016-08-30</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
function bi($d,$n,$k)
{
	$n--;
	if($n > 0)
	{
		$li=0;
		$ls=$n;
		while ($ls - $li > 1)
		{
			$lm=(integer)(($li + $ls) / 2);
			$val=strncmp(strtoupper($k),strtoupper($d[$lm][0]),20);
			if($val == 0)
				return $lm;
			elseif($val < 0)
					$ls=$lm;
				else
					$li=$lm;
		}
		if(strtoupper($k) == strtoupper($d[$li][0]))
			return $li;
		elseif(strtoupper($k) == strtoupper($d[$ls][0]))
					return $ls;
				else
					return -1;
	}
	elseif(isset($d[0][0]) and $d[0][0] == $k)
			return 0;
		else
			return -1;
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='000001_pro138.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	

	

	if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wcco1) or !isset($wcco2) or !isset($wper1) or $wper1 < 1 or $wper1 > 12)
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>SELECCION DE GASTOS DE TRASLADOS PARA COSTOS</td></tr>";
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
		$query = $query."  and Mgaper =  ".$wper1;
		$query = $query."  and Mgacco between '".$wcco1."' and '".$wcco2."' ";
		$query = $query."  and Mgatip =  'TRASLADOS' ";
		$err = mysql_query($query,$conex);
		
		$COD=array();
		$query  = "select ".$empresa."_000100.Procco,".$empresa."_000100.Procod from ".$empresa."_000100 ";
		$query .= "  where ".$empresa."_000100.Procco between '".$wcco1."' and '".$wcco2."' ";
		$query .= "    and Proemp = '".$wemp."'";
		$query .= "    and (".$empresa."_000100.Protip='2' or ".$empresa."_000100.Protip='4')";
		$query .= " GROUP by 1,2  ";
		$query .= " ORDER by 1,2  ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$COD[$i][0]=$row[0].$row[1];
			}
		}
		$numcod=$num;
		
		$query  = "SELECT Almcco, Almcpr, Almcod, sum(Almcto)  from ".$empresa."_000002,".$empresa."_000130  ";
		$query .= "  where Almano =  ".$wanop;
		$query .= "    and Almemp = '".$wemp."' ";
		$query .= "    and Almmes =  ".$wper1;
		$query .= "    and Almcco between '".$wcco1."' and '".$wcco2."' ";
		$query .= "    and Almemp =  Ifaemp ";
		$query .= "    and Almcco =  Ifacco ";
		$query .= "    and Almcod =  Ifains ";
		$query .= "    and Ifatip =  'D' ";
		//$query .= "    and Almcod not in  ";
		//$query .= "  (select ".$empresa."_000100.Procod from ".$empresa."_000100 where ".$empresa."_000100.Procco between '".$wcco1."' and '".$wcco2."'  and (".$empresa."_000100.Protip='2' or ".$empresa."_000100.Protip='4') and Proemp = '".$wemp."' group by 1)";
		$query .= "  group by Almcco, Almcpr, Almcod " ;
		//echo $query."<br>";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		$count=0;
		for ($i=0;$i<$num;$i++)
		{	
			$row = mysql_fetch_array($err);
			$pos=bi($COD,$numcod,$row[0].$row[2]);
			echo $row[0].$row[2]." ".$pos."<br>";
			if($pos == -1)
			{
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
				$query = "insert ".$empresa."_000092 (medico,fecha_data,hora_data,Mgaemp, Mgaano, Mgaper, Mgacco, Mgagas, Mgasga, Mgaval, Mgatip , seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wper1.",'".$row[0]."','".$row[1]."','".$row[2]."',".$row[3].",'TRASLADOS','C-".$empresa."')";
				$err1 = mysql_query($query,$conex) or die( mysql_errno().":".mysql_error()." Error en la Insercion del Driver");
				$count++;
				echo "REGISTROS INSERTADOS : ".$count."<BR>";
			}
		}
		echo "<B>REGISTROS ADICIONADOS : ".$count."</B><BR>";
	}
}
?>
</body>
</html>
