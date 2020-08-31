<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Generacion Resumen de Ingresos Presupuestados (T43)</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro109.php Ver. 2015-11-06</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro109.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wesc) or $wesc < 1 or $wesc > 3  or !isset($wemp) or $wemp == "Seleccione")
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTACION</td></tr>";
			echo "<tr><td align=center colspan=2>GENERACION RESUMEN DE INGRESOS PRESUPUESTADOS (T43)</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Presupuestacion</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Escenario de Incremento de Ingresos (1 o 2 o 3)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wesc' size=1 maxlength=1></td></tr>";
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
				$var=array();
				$query = "delete from ".$empresa."_000043 ";
				$query = $query."  where resano = ".$wanop;
				$query = $query."    and resemp = '".$wemp."' ";
				$query = $query."    and resind = '109'";
				$err = mysql_query($query,$conex);
				switch ($wesc)
				{
					case 1:
						$query = "SELECT Dipcco, '100', Dipano, Dipmes,sum(Dipip1)  from ".$empresa."_000033 ";
						$query = $query."  where Dipano = ".$wanop;
						$query = $query."    and Dipemp = '".$wemp."' ";
						$query = $query."  group by Dipcco, Dipano, Dipmes ";
						$query = $query."  union ";
						$query = $query." SELECT Dipcco, '900', Dipano, Dipmes,sum(Dipit1)  from ".$empresa."_000033 ";
						$query = $query."  where Dipano = ".$wanop;
						$query = $query."    and Dipemp = '".$wemp."' ";
						$query = $query."  group by Dipcco, Dipano, Dipmes ";
					break;
					case 2:
						$query = "SELECT Dipcco, '100', Dipano, Dipmes,sum(Dipip2)  from ".$empresa."_000033 ";
						$query = $query."  where Dipano = ".$wanop;
						$query = $query."    and Dipemp = '".$wemp."' ";
						$query = $query."  group by Dipcco, Dipano, Dipmes ";
						$query = $query."  union ";
						$query = $query." SELECT Dipcco, '900', Dipano, Dipmes,sum(Dipit2)  from ".$empresa."_000033 ";
						$query = $query."  where Dipano = ".$wanop;
						$query = $query."    and Dipemp = '".$wemp."' ";
						$query = $query."  group by Dipcco, Dipano, Dipmes ";
					break;
					case 3:
						$query = "SELECT Dipcco, '100', Dipano, Dipmes,sum(Dipip3)  from ".$empresa."_000033 ";
						$query = $query."  where Dipano = ".$wanop;
						$query = $query."    and Dipemp = '".$wemp."' ";
						$query = $query."  group by Dipcco, Dipano, Dipmes ";
						$query = $query."  union ";
						$query =  $query." SELECT Dipcco, '900', Dipano, Dipmes,sum(Dipit3)  from ".$empresa."_000033 ";
						$query = $query."  where Dipano = ".$wanop;
						$query = $query."    and Dipemp = '".$wemp."' ";
						$query = $query."  group by Dipcco, Dipano, Dipmes ";
					break;
				}
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$query = "insert ".$empresa."_000043 (medico,fecha_data,hora_data,Resemp, Rescco, Rescpr, Resano ,Resper, Resmon ,Resind ,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$row[0]."','".$row[1]."',".$row[2].",".$row[3].",".round($row[4], 0).",'109','C-".$empresa."')";
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
