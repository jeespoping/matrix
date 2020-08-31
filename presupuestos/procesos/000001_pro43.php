<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Grabacion de Centros de Servicio Reales Distribuidos(T69 a T26)</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro43.php Ver. 2015-11-20</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro43.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>GRABACION DE CENTROS DE SERVICIO REALES DISTRIBUIDOS(T69 a T26)</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
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
			$query = "SELECT cierre_real from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and mes =   ".$wper1;
			$query = $query."    and emp = '".$wemp."' ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if($num > 0 and $row[0] == "off")
			{
			$query = "delete from ".$empresa."_000026 ";
			$query = $query."  where mecano = ".$wanop;
			$query = $query."    and mecmes = ".$wper1;
			$query = $query."    and mecemp = '".$wemp."' ";
			$query = $query."    and meccue = '99999999'";
			$err2 = mysql_query($query,$conex);
			$count=0;
			$query = "select resccd,ccoclas,sum(resmon) ";
			$query = $query." from ".$empresa."_000069,".$empresa."_000005 ";
			$query = $query." where resano = ".$wanop;
			$query = $query."   and resmes = ".$wper1;
			$query = $query."   and resemp = '".$wemp."' ";
			$query = $query."   and restip = 'R' ";
			$query = $query."   and resccd = ccocod ";
			$query = $query."   and resemp = ccoemp ";
			$query = $query." group by resccd,ccoclas ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($row[1] == "UNI")
					$wcpr="598";
				else
					$wcpr="298";
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
        		$query = "insert ".$empresa."_000026 (medico,fecha_data,hora_data,mecemp,meccco,meccpr,mecano,mecmes,meccue,mecval,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$row[0]."','".$wcpr."',".$wanop.",".$wper1.",'99999999',".$row[2].",'C-".$empresa."')";
				$err1 = mysql_query($query,$conex);
				$count++;
				echo "REGISTRO INSERTADO NRo   : ".$count."<br>";
    		}
    		$query = "select rescco,ccoclas,sum(resmon) ";
			$query = $query." from ".$empresa."_000069,".$empresa."_000005 ";
			$query = $query." where resano = ".$wanop;
			$query = $query."   and resmes = ".$wper1;
			$query = $query."   and resemp = '".$wemp."' ";
			$query = $query."   and restip = 'R' ";
			$query = $query."   and rescco = ccocod ";
			$query = $query."   and resemp = ccoemp ";
			$query = $query." group by rescco,ccoclas ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($row[1] == "UNI")
					$wcpr="599";
				else
					$wcpr="299";
				$row[2] = $row[2] * (-1);
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
        		$query = "insert ".$empresa."_000026 (medico,fecha_data,hora_data,mecemp,meccco,meccpr,mecano,mecmes,meccue,mecval,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$row[0]."','".$wcpr."',".$wanop.",".$wper1.",'99999999',".$row[2].",'C-".$empresa."')";
				$err1 = mysql_query($query,$conex);
				$count++;
				echo "REGISTRO INSERTADO NRo   : ".$count."<br>";
    		}
			echo "REGISTROS INSERTADOS   : ".$count."<br>";
         }
         else
		{
			echo "<center><table border=0 aling=center>";
			echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
			echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>EL PERIODO  ESTA!!! CERRADO -- LLAME A COSTOS Y PRSUPUESTOS</MARQUEE></FONT>";
			echo "<br><br>";			
		}
		}
	}
?>
</body>
</html>
