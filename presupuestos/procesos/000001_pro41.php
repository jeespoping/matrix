<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Calculo de Costos de Apoyo Variables</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro41.php Ver. 2015-11-20</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro41.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1)  or !isset($wres) or (strtoupper ($wres) != "R" and strtoupper ($wres) != "P"))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>CALCULO DE COSTOS DE APOYO VARIABLES</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
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
			$count=0;
			$query = "delete from ".$empresa."_000069 ";
			$query = $query."  where resano = ".$wanop;
			$query = $query."    and resmes = ".$wper1;
			$query = $query."    and resemp = '".$wemp."' ";
			$query = $query."    and restic = 'V' ";
			$query = $query."    and restip= '".$wres."'";
			$err = mysql_query($query,$conex);
			$query = "SELECT msecco,mseccd,sum(msecan*tasmon) from ".$empresa."_000072,".$empresa."_000071 ";
			$query = $query."  where mseano = ".$wanop;
			$query = $query."    and msemes = ".$wper1;
			$query = $query."    and mseemp = '".$wemp."' ";
			$query = $query."    and msetip = '".$wres."'";
			$query = $query."    and mseano = tasano ";
			$query = $query."    and msemes = tasmes ";
			$query = $query."    and mseemp = tasemp ";
			$query = $query."    and msecod = tascod ";
			$query = $query."    and msetip = tastip ";
			$query = $query."    and mseusu = tasusu ";
			$query = $query."    group by  msecco,mseccd";
			$query = $query."    order by  msecco,mseccd";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
        		$query = "insert ".$empresa."_000069 (medico,fecha_data,hora_data,resemp,resano,resmes,rescco,resccd,resmon,restic,restip,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wper1.",'".$row[0]."','".$row[1]."',".$row[2].",'V','".$wres."','C-".$empresa."')";
				$err1 = mysql_query($query,$conex);
				$count++;
				echo "REGISTRO INSERTADO NRO : ".$count."<br>";
    		}
			echo "TOTAL REGISTROS INSERTADOS : ".$count;
		}
	}
?>
</body>
</html>
