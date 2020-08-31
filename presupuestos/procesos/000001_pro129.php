<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Proyeccion de Procedimientos Nuevas Lineas de Servicio Ambulatorio</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro129.php Ver. 2015-11-17</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
function bisiesto($year)
{
	return(($year % 4 == 0 and $year % 100 != 0) or $year % 400 == 0);
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro129.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wcco) or !isset($wlin) or !isset($wmes) or !isset($wnpr) or !isset($wnpa) or !isset($wmax) or !isset($winp) or !isset($wpte) or !isset($wlif))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTACION</td></tr>";
			echo "<tr><td align=center colspan=2>PROYECCION DE PROCEDIMIENTOS NUEVAS LINEAS DE SERVICIO AMBULATORIO</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>A&ntilde;o de Presupuestacion</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Unidad de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco' size=4 maxlength=4></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Linea de Servicio</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wlin' size=3 maxlength=3></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Mes Inicial de Operacion</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmes' size=2 maxlength=2></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Nro. de Procedimientos Iniciales </td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wnpr' size=6 maxlength=6></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Nro. de Procedimientos Adicionales Mensuales</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wnpa' size=6 maxlength=6></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Nro. de Procedimientos Maximos</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmax' size=6 maxlength=6></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Ingreso Promedio Procedimiento</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='winp' size=10 maxlength=10></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Porcentaje para Terceros</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wpte' size=3 maxlength=3></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Linea de Facturacion</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wlif' size=2 maxlength=2></td></tr>";
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
			#INICIO PROGRAMA
			$wemp=substr($wemp,0,strpos($wemp,"-"));
			$k=0;
			$wanopa=$wanop - 1;
			$query = "delete  from ".$empresa."_000031 ";
			$query = $query."  where Mopano = ".$wanop;
			$query = $query."    and Mopemp = '".$wemp."' ";
			$query = $query."    and Mopcco = '".$wcco."'";
			$query = $query."    and Mopcod = '".$wlin."'";
			$query = $query."    and Moptip = 'N' ";
			$err = mysql_query($query,$conex);
			
			$query = "delete  from ".$empresa."_000041 ";
			$query = $query."  where Ipmano = ".$wanopa;
			$query = $query."    and Ipmemp = '".$wemp."' ";
			$query = $query."    and Ipmcco = '".$wcco."'";
			$query = $query."    and Ipmgru = '".$wlin."' ";
			$err = mysql_query($query,$conex);
			for ($i=0;$i<12;$i++)
			{
				$wmesr=$i + 1;
				if($wmesr < $wmes)
					$wcan = 0;
				elseif($wmesr == $wmes)
						$wcan = $wnpr;
					elseif(($wnpr + $wnpa) <= $wmax)
						{
							$wnpr += $wnpa;
							$wcan = $wnpr;
						}
						else
							$wcan = $wmax;
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
				$query = "insert ".$empresa."_000031 (medico,fecha_data,hora_data,Mopemp, Mopano, Mopmes, Mopcco, Mopcod, Mopcan, Moptip, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wmesr.",'".$wcco."','".$wlin."',".$wcan.",'N','C-".$empresa."')";
       			$err2 = mysql_query($query,$conex);
       			if ($err2 != 1)
					echo mysql_errno().":".mysql_error()."<br>";
				else
				{
	       			$k++;
	       			echo "REGISTRO INSERTADO T31 : ".$k."<br>";
				}
   			}
   			$fecha = date("Y-m-d");
			$hora = (string)date("H:i:s");
			$query = "insert ".$empresa."_000041 (medico,fecha_data,hora_data,Ipmemp, Ipmano, Ipmcco, Ipmgru, Ipminp, Ipmpte, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanopa.",'".$wcco."','".$wlif."',".$winp.",".$wpte.",'C-".$empresa."')"; 
   			$err2 = mysql_query($query,$conex);
   			if ($err2 != 1)
				echo mysql_errno().":".mysql_error()."<br>";
			else
			{
       			$k++;
       			echo "REGISTRO INSERTADO  T41 : ".$k."<br>";
			}
   			echo "<b>NUMERO DE REGISTROS INSERTADOS : ".$k."</b><br>";
   		}
}		
?>
</body>
</html>
