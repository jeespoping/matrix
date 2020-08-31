<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Reclasificacion Informacion de Activos </b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro27.php Ver. 2015-09-18</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro27.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wmesp) or !isset($wemp) or $wemp == "Seleccione" or !isset($wmesp) or !isset($wcco1) or !isset($wcco2) or !isset($wcod) or !isset($wok))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center>APLICACION DE PRESUPUESTOS</td></tr>";
			echo "<tr><td align=center>RECLASIFICACION INFORMACION DE ACTIVOS</td></tr>";
			echo "<tr>";
			if(!isset($wanop))
			{
				echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o  Proceso</td>";
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
				echo "<td bgcolor=#cccccc align=center>Mes de Proceso</td>";
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesp' size=2 maxlength=2></td></tr>";
				echo "<td bgcolor=#cccccc align=center>Centro de Costos Origen</td>";
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
				echo "<td bgcolor=#cccccc align=center>Centro de Costos Destino</td>";
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco2' size=4 maxlength=4></td></tr>";
				echo "<td bgcolor=#cccccc align=center>Codigo</td>";
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcod' size=3 maxlength=3></td></tr>";
			}
			else
			{
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
				echo "<td bgcolor=#cccccc align=center>Desea Reclasificar de ".$wcco1." a ".$wcco2." de ".$wanop."-".$wmesp." Codigo ".$wcod." (S/N)</td>";
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wok' size=1 maxlength=1></td></tr>";
				echo "<input type='HIDDEN' name= 'wanop' value='".$wanop."'>";
				echo "<input type='HIDDEN' name= 'wmesp' value='".$wmesp."'>";
				echo "<input type='HIDDEN' name= 'wcco1' value='".$wcco1."'>";
				echo "<input type='HIDDEN' name= 'wcco2' value='".$wcco2."'>";
				echo "<input type='HIDDEN' name= 'wcod' value='".$wcod."'>";
			}
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$wemp = substr($wemp,0,2);
			if ($wok == "S")
			{
				$count=0;
				$query = "SELECT * from ".$empresa."_000037 ";
				$query = $query." where orucco = '".$wcco1."'";
				$query = $query."   and oruano = ".$wanop;
				$query = $query."   and orumes = ".$wmesp;
				$query = $query."   and oruemp = '".$wemp."' ";
				$query = $query."   and orucod = '".$wcod."'";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						$actual=0;
        				$query = "SELECT * from ".$empresa."_000037 ";
						$query = $query." where orucco = '".$wcco2."'";
						$query = $query."   and oruano = ".$wanop;
						$query = $query."   and orumes = ".$wmesp;
						$query = $query."   and oruemp = '".$wemp."' ";
						$query = $query."   and orucod = '".$wcod."'";
        				$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
						$num1 = mysql_num_rows($err1);
						if ($num1 > 0)
						{
							$row1 = mysql_fetch_array($err1);
							$monto=$row1[8] + $row[8];
                			$query = "delete from ".$empresa."_000037 ";
							$query = $query." where id = '".$row1[10]."'";
        					$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
            			}
            			else
            			{
	            			$monto= $row[8];
            			}
        				$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
        				$query = "insert ".$empresa."_000037 (medico,fecha_data,hora_data,oruemp,orucco,oruano,orumes,orucod,orumon,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$wcco2."',".$wanop.",".$wmesp.",'".$wcod."',".$monto.",'C-".$empresa."')";
						$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
						$count++;
					}
                }
				$query = "delete from ".$empresa."_000037 ";
				$query = $query." where orucco = '".$wcco1."'";
				$query = $query."   and oruano = ".$wanop;
				$query = $query."   and orumes = ".$wmesp;
				$query = $query."   and oruemp = '".$wemp."' ";
				$query = $query."   and orucod = '".$wcod."'";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
      			echo "<tr><td  colspan=6>REGISTROS RECLASIFICADOS : ".$count."</td></tr>";
		}
		else
			echo "PROCESO ABORTADO";
	}
}
?>
</body>
</html>
