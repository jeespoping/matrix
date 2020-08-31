<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Aplicacion Incrementos Nominales a Pptos Manuales (T122 a T12)</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro122.php Ver. 2015-11-17</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro122.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione")
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center>APLICACION DE PRESUPUESTOS</td></tr>";
			echo "<tr><td align=center>APLICACION INCREMENTOS NOMINALES A PPTOS MANUALES (T122 A T12)</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>A&ntilde;o de Presupuestaci&oacute;n</td>";
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
			$wemp=substr($wemp,0,strpos($wemp,"-"));
			$query = "SELECT Cierre_Ppto from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and mes = 0 ";
			$query = $query."    and Emp = '".$wemp."' ";
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if($num > 0 and $row[0] == "on")
			{
				$k=0;
				$query = "DELETE from ".$empresa."_000012  ";
				$query = $query."where Gasano=".$wanop;
				$query = $query."  and Gasemp = '".$wemp."' ";
				$err = mysql_query($query,$conex);
				//                  0      1        2      3        4      5       6       7
				$query = "SELECT Gahmes, Gahcco, Gahcpr, Gahval, Gahdes, Gahprg, Gahccr, Gahmic  from ".$empresa."_000122 ";
				$query = $query."  where Gahano=".$wanop;
				$query = $query."    and Gahemp = '".$wemp."' ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					for($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						$monto=$row[3];
						$query = "SELECT Icgpor, Icgper from ".$empresa."_000046 ";
						$query = $query." where Icgano = ".$wanop;
						$query = $query."   and Icgemp = '".$wemp."' ";
						$query = $query."   and Icgcpr = '".$row[2]."'";
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						if ($num1>0)
						{
							$row1 = mysql_fetch_array($err1);
							if($row[7] > 0)
								$row1[1]=$row[7];
							if($row[0] >= $row1[1])
								$monto=$row[3] * (1 + ($row1[0] / 100));
						}
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$query = "insert ".$empresa."_000012 (medico,fecha_data,hora_data,Gasemp, Gascco, Gascod, Gasano, Gasmes, Gasval, Gasdes, Gasprg, Gasccr, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$row[1]."','".$row[2]."',".$wanop.",".$row[0].",".$monto.",'".$row[4]."','".$row[5]."','".$row[6]."','C-".$empresa."')";
						$err2 = mysql_query($query,$conex);
						$k++;
						echo "REGISTROS INSERTADOS      : ".$k."<br>";
					}
					echo "NUMERO DE REGISTROS INSERTADOS      : ".$k."<br>";
				}
			}
			else
			{
				echo "<center><table border=0 aling=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>EL PERIODO  ESTA CERRADO !! -- NO SE PUEDE EJECUTAR ESTE PROCESO</MARQUEE></FONT>";
				echo "<br><br>";			
			}
		}
}
?>
</body>
</html>
