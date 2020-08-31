<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Generacion Resumen de Gastos de Personal (T35 a T43)</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro114.php Ver. 2017-06-14</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro114.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1) or $wper1 < 1 or $wper1 > 12 or !isset($wper2) or $wper2 < 1 or $wper2 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTACION</td></tr>";
			echo "<tr><td align=center colspan=2>GENERACION RESUMEN DE GASTOS DE PERSONAL (T35 a T43)</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Presupuestaci&oacute;n</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Mes de Inicial</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Mes de Final</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
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
				$mult=1;
				$query = "delete from ".$empresa."_000043 ";
				$query = $query."  where resano = ".$wanop;
				$query = $query."    and resemp = '".$wemp."' ";
				$query = $query."    and resind = '114'";
				$err = mysql_query($query,$conex);
				$query = "SELECT Nopcco, Rcncpr, Nopano, Nopper,sum(Nopmon)  from ".$empresa."_000035,".$empresa."_000158 ";
				$query = $query."  where Nopano = ".$wanop;
				$query = $query."    and Nopemp = '".$wemp."' ";
				$query = $query."    and Nopper between ".$wper1." and ".$wper2;
				$query = $query."    and Nopcco = Rcncco ";
				$query = $query."    and Nopemp = Rcnemp ";
				$query = $query."    and Rcntip = 'N' ";
				$query = $query."  group by 1,2,3,4 ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				$suma=0;
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$suma += $row[4];
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
				
					$query = "insert ".$empresa."_000043 (medico,fecha_data,hora_data,Resemp, Rescco, Rescpr, Resano ,Resper, Resmon ,Resind ,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$row[0]."','".$row[1]."',".$row[2].",".$row[3].",".$row[4].",'114','C-".$empresa."')";
					$err1 = mysql_query($query,$conex);
					if ($err1 != 1)
						echo mysql_errno().":".mysql_error()."<br>";
					else
					{
						$k++;
						echo "REGISTRO INSERTADO  : ".$k."<br>";
					}
				}
				echo "REGISTROS INSERTADOS : ".$k;
				$query = "SELECT Nopcco  from ".$empresa."_000035 ";
				$query = $query."   where Nopano = ".$wanop;
				$query = $query." 	  and Nopemp = '".$wemp."' ";
				$query = $query." 	  and Nopcco not in(select Rcncco from costosyp_000158 where Rcnemp = '".$wemp."' and Rcntip = 'N' )";
				$query = $query."Group by 1 ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if($num > 0)
				{
					echo "<br><br><center><table border=0 aling=center>";
					echo "<tr><td>CONTROS DE COSTOS SIN RUBRO EN T158</tr></td>";
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						echo "<tr><td>".$row[0]."</tr></td>";
					}
					echo "</table>";
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
