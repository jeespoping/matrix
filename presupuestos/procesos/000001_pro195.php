<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Codificacion Movimiento NIIF</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro195.php Ver. 2016-01-22</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro195.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanoi) or !isset($wanof))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTACION</td></tr>";
			echo "<tr><td align=center colspan=2>CODIFICACION MOVIMIENTO NIIF</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanoi' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanof' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			echo "<center><table border=1>";
			echo "<tr><td align=center colspan=4><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center  colspan=4>APLICACION DE PRESUPUESTOS</td></tr>";
			echo "<tr><td align=center  colspan=4>ERRORES CODIFICACION MOVIMIENTO NIIF</td></tr>";
			echo "<tr><td>CENTRO DE <br>COSTOS</td><td>DESCRIPCION</td><td>RUBRO</td><td>DESCRIPCION</td></tr>";
			$k=0;
			$wanopa=$wanop - 1;
			$query = "delete from ".$empresa."_000043 ";
			$query = $query."  where resano = ".$wanof;
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			//                          0        1       2      3       4       5       6       7
			$query = "select Rescco, Rescpr, Resano, Resper, Resmon, Resind, Mganom, Cconom ";
			$query = $query."  from ".$empresa."_000043,".$empresa."_000028,".$empresa."_000005 ";
			$query = $query."  where Resano = ".$wanoi; 
			$query = $query."    and Rescpr = Mgacod ";
			$query = $query."    and Rescco = Ccocod ";
			$err = mysql_query($query,$conex)  or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$query = "  select Rnirpf ";
				$query = $query."  from ".$empresa."_000157 ";
				$query = $query."  where Rnicco = '".$row[0]."'"; 
				$query = $query."    and Rnirpi = '".$row[1]."'"; 
				$query = $query."    and Rniest = 'on'";
				$err1 = mysql_query($query,$conex)  or die(mysql_errno().":".mysql_error());
				$num1 = mysql_num_rows($err1);
				if($num1 > 0)
				{
					$row1 = mysql_fetch_array($err1);
					$row[1] = $row1[0];
					$query = "insert ".$empresa."_000043 (medico,fecha_data,hora_data,Rescco, Rescpr, Resano ,Resper, Resmon ,Resind ,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$row[0]."','".$row[1]."',".$wanof.",".$row[3].",".$row[4].",'".$row[5]."','C-".$empresa."')";
					$err1 = mysql_query($query,$conex);
					if ($err1 != 1)
						echo mysql_errno().":".mysql_error()."<br>";
					else
					{
						$k++;
					}
				}
				else
				{
					echo "<tr><td>".$row[0]."</td><td>".$row[7]."</td><td>".$row[1]."</td><td>".$row[6]."</td></tr>";
				}
    		}
    		echo "<tr><td colspan=3>TOTAL DE REGISTROS INSERTADOS</td><td align=right>".number_format((double)$k,0,'.',',')."</td></tr>";
    		echo "</table>";
		}
	}
?>
</body>
</html>
