<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Distribucion de Obligaciones Financieras a Presupuestos</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro137.php Ver. 2015-09-25</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro137.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione")
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTACION</td></tr>";
			echo "<tr><td align=center colspan=2>DISTRIBUCION DE OBLIGACIONES FINANCIERAS A PRESUPUESTOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
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
			$k=0;
			$wanopa = $wanop - 1;
			$query = "delete from ".$empresa."_000122 ";
			$query = $query."  where Gahano = ".$wanop;
			$query = $query."    and Gahemp = '".$wemp."' ";
			$query = $query."    and Gahcpr = '601' ";
			$query = $query."    and Gahtip = '2' ";
			$err = mysql_query($query,$conex);
			//                  0       1      2       3       4       5       6       7
			$query = "SELECT Movtob, Movnid, Movano, Movmes, Movcin, Mofent, Dofcco, Dofpor from ".$empresa."_000135, ".$empresa."_000132, ".$empresa."_000133";
			$query = $query."  where Movano = ".$wanop;
			$query = $query."    and Movemp = '".$wemp."' ";
			$query = $query."    and Movemp = Mofemp ";
			$query = $query."    and Movtob = Moftob ";
			$query = $query."    and Movnid = Mofnid ";
			$query = $query."    and Movemp = Dofemp ";
			$query = $query."    and Movtob = Doftob ";
			$query = $query."    and Movnid = Dofnid ";
			$query = $query."   Order by Movtob, Movnid, Movano, Movmes, Dofcco ";
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error()."<br>");
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
				$wval = $row[4] * $row[7];
				$wpor = ($row[7] * 100);
				$wdes = "CAUS. ITERESES ".$row[0]." ".$row[5]." ".$row[1]." ".(string)number_format((double)$wpor,2,'.',',')."%";
        		$query = "insert ".$empresa."_000122 (medico,fecha_data,hora_data,Gahemp, Gahano, Gahmes, Gahcco, Gahcpr, Gahval, Gahdes, Gahtip, Gahprg, Gahccr, Gahmic, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$row[2].",".$row[3].",'".$row[6]."','601',".$wval.",'".$wdes."','2','0','2046',13,'C-".$empresa."')";
				$err1 = mysql_query($query,$conex);
				if ($err1 != 1)
					echo mysql_errno().":".mysql_error()."<br>";
				else
				{
           			$k++;
           			echo "REGISTRO INSERTADO  : ".$k."<br>";
				}
			}
			echo "<B>TOTAL REGISTROS ACTUALIZADOS : ".$k."</B>";
		}
	}
?>
</body>
</html>
