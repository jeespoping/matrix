<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Calculo de Promedios de Centros de Servicios Reales</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro44.php Ver. 2015-11-20</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro44.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wres) or (strtoupper ($wres) != "R" and strtoupper ($wres) != "P") or !isset($wper) or !isset($wanop1) or !isset($wper1) or !isset($wanop2) or !isset($wper2) or $wper < 1 or $wper > 12 or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>CALCULO DE PROMEDIOS DE CENTROS DE SERVICIOS REALES</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso Inicial</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso Inicial</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso Final</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop2' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso Final</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Tipo de Informacion (R - Real / P - Presupuestada)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wres' size=1 maxlength=1></td></tr>";
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
			$k=0;
			if($wanop1 != $wanop2)
				$k=1;
			$wmeses = 12*$k + $wper2 - $wper1 + 1;
			$query = "delete from ".$empresa."_000073 ";
			$query = $query."  where proano = ".$wanop;
			$query = $query."    and promes = ".$wper;
			$query = $query."    and Proemp = '".$wemp."' ";
			$query = $query."    and protip = '".$wres."'";
			$err2 = mysql_query($query,$conex);
			$count=0;
			$query = "select rescco,resccd,sum(resmon) ";
			$query = $query."from ".$empresa."_000069 ";
			$query = $query." where resano  between ".$wanop1." and ".$wanop2;
			if($wanop1 != $wanop2)
			{
				$query = $query." and (resmes  between ".$wper1." and  12 ";
				$query = $query." or resmes  between 1 and ".$wper2.") ";
			}
			else
				$query = $query." and resmes  between ".$wper1." and ".$wper2;
			$query = $query."    and resemp = '".$wemp."' ";
			$query = $query."    and restic = 'D' ";
			$query = $query."    and restip = '".$wres."'";
			$query = $query." group by rescco,resccd,restip ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$wmon = $row[2] / $wmeses;
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
        		$query = "insert ".$empresa."_000073 (medico,fecha_data,hora_data,proemp,proano,promes,procco,proccd,promon,protic,protip,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wper.",'".$row[0]."','".$row[1]."',".$wmon.",'D','".$wres."','C-".$empresa."')";
				$err1 = mysql_query($query,$conex);
				$count++;
				echo "REGISTRO INSERTADO NRO  : ".$count."<br>";
    		}
    		$query = "select rescco,resccd,sum(resmon) ";
			$query = $query."from ".$empresa."_000069 ";
			$query = $query." where resano  = ".$wanop;
			$query = $query."    and resmes  = ".$wper;
			$query = $query."    and resemp = '".$wemp."' ";
			$query = $query."    and restic = 'V' ";
			$query = $query."    and restip = '".$wres."'";
			$query = $query." group by rescco,resccd,restip ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$wmon = $row[2];
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
        		$query = "insert ".$empresa."_000073 (medico,fecha_data,hora_data,proemp,proano,promes,procco,proccd,promon,protic,protip,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wper.",'".$row[0]."','".$row[1]."',".$wmon.",'V','".$wres."','C-".$empresa."')";
				$err1 = mysql_query($query,$conex);
				$count++;
				echo "REGISTRO INSERTADO NRO  : ".$count."<br>";
    		}
			echo "REGISTROS INSERTADOS   : ".$count."<br>";
		}
	}
?>
</body>
</html>
