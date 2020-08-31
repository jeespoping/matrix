<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Distribucion Gastos Depreciacion a Subprocesos</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro58.php Ver. 2016-05-19</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='000001_pro58.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	

	

	if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wcco1) or !isset($wcco2) or !isset($wper1) or $wper1 < 1 or $wper1 > 12)
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>DISTRIBUCION GASTOS DEPRECIACION A SUBPROCESOS</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o  Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Mes Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Centro de Costos Inicial</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Centro de Costos Final</td>";
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
		$wemp = substr($wemp,0,2);
		$wtott=0;
		$query = "delete from ".$empresa."_000087  ";
		$query = $query."  where Gasano =  ".$wanop;
		$query = $query."    and Gasemp = '".$wemp."'";
		$query = $query."    and Gasmes =  ".$wper1;
		$query = $query."    and Gascco between '".$wcco1."' and '".$wcco2."' ";
		$query = $query."    and Gastip =  'ACTIVO' ";
		$err = mysql_query($query,$conex);
		$query = "SELECT  Depcco, Dacsub,Acftip,Depcod, Dacpor,Ccoclas,sum(Depvde) from ".$empresa."_000076,".$empresa."_000084,".$empresa."_000075,".$empresa."_000005  ";
		$query = $query."  where Depano =  ".$wanop;
		$query = $query."    and Depemp = '".$wemp."'";
		$query = $query."    and Depmes =  ".$wper1;
		$query = $query."    and Depcco between '".$wcco1."' and '".$wcco2."' ";
		$query = $query."    and Depemp =  Dacemp";
		$query = $query."    and Depano =  Dacano";
		$query = $query."    and Depmes =  Dacmes";
		$query = $query."    and Depcco =  Daccco";
		$query = $query."    and Depcod =  Daccod";
		$query = $query."    and Depemp =  Acfemp";
		$query = $query."    and Depcod =  Acfcod";
		$query = $query."    and Depemp =  ccoemp";
		$query = $query."    and Depcco =  ccocod";
		$query = $query."    and ccocos =  'S' ";
		$query = $query."  group by  Depcco, Dacsub,Acftip,Depcod, Dacpor,Ccoclas " ;
		$query = $query."  order by  Depcco, Dacsub,Acftip  " ;
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		$key="";
		$count=0;
		for ($i=0;$i<$num;$i++)
		{	
			$row = mysql_fetch_array($err);
			if($row[0].$row[1].$row[2] != $key)
			{
				if($i != 0)
				{
					if($wtott > 0)
					{
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$wfin=strlen($key)-5;
						$query = "insert ".$empresa."_000087 (medico,fecha_data,hora_data,Gasemp, Gasano, Gasmes, Gascco, Gasgas, Gassub, Gasrub, Gasval, Gastip, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wper1.",'".substr($key,0,4)."','".$wgasgas."','".substr($key,4,$wfin)."','".$wgasrub."',".$wtott.",'ACTIVO','C-".$empresa."')";
						$err1 = mysql_query($query,$conex) or die("Error en la Insercion del Driver");
						$count++;
						echo "REGISTROS INSERTADOS : ".$count."<BR>";
					}
				}
				$key=$row[0].$row[1].$row[2];
				$wtott=0;
			}
			if($row[5] == "IND")
				$wgasgas="517";
			else
				$wgasgas="203";
			if($row[2] == "0")
				$wgasrub="CEDI";
			else
				$wgasrub="CMEE";
			$wtott+=$row[4]*$row[6];
		}
		if($wtott > 0)
		{
			$fecha = date("Y-m-d");
			$hora = (string)date("H:i:s");
			$wfin=strlen($key)-5;
			$query = "insert ".$empresa."_000087 (medico,fecha_data,hora_data,Gasemp, Gasano, Gasmes, Gascco, Gasgas, Gassub, Gasrub, Gasval, Gastip, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wper1.",'".substr($key,0,4)."','".$wgasgas."','".substr($key,4,$wfin)."','".$wgasrub."',".$wtott.",'ACTIVO','C-".$empresa."')";
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
