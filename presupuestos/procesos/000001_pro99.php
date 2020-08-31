<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Autollenado de Numero de Procedimientos x Linea x CC (T21)</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro99.php Ver. 2015-11-06</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro99.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wanob) or !isset($wemp) or $wemp == "Seleccione")
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTACION</td></tr>";
			echo "<tr><td align=center colspan=2>AUTOLLENADO DE NUMERO DE PROCEDIMIENTOS X LINEA X CC (T21)</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>A&ntilde;o Base de Presupuestacion</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Presupuestacion</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanob' size=4 maxlength=4></td></tr>";
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
			$query = $query."  where ano = ".$wanob;
			$query = $query."    and mes = 0 ";
			$query = $query."    and Emp = '".$wemp."' ";
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if($num > 0 and $row[0] == "on")
			{
				$k=0;
				$query = "DELETE from ".$empresa."_000021 ";
				$query = $query." where Aprano = ".$wanop;
				$query = $query."   and Apremp = '".$wemp."' ";
				$err = mysql_query($query,$conex);
				$query = "select Ocnano, Ocncco, Ocncod, Ocnmes, Ocntip  from ".$empresa."_000022 ";
				$query = $query." where Ocnano = ".$wanop; 
				$query = $query."   and Ocnemp = '".$wemp."' ";
				$query = $query."   and Ocntip != 0 ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if($num > 0)
				{
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						$query = "select  sum(Morcan)  from ".$empresa."_000032 ";
						$query = $query." where Morano =  ".$row[0]; 
						$query = $query." 	and Mormes between 1 and ".$row[3];
						$query = $query."   and Moremp = '".$wemp."' ";
						$query = $query." 	and Morcco = '".$row[1]."'";
						$query = $query."   and Morcod = '".$row[2]."'";
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						if($num1 > 0)
						{
							$row1 = mysql_fetch_array($err1);
							$totac=$row1[0];
							if($row[4] == "1")
								$ing = $row1[0] / $row[3];
							else
							{
								$wanopa=$row[0] - 1;
								$query = "select  sum(Morcan)  from ".$empresa."_000032 ";
								$query = $query." where Morano =  ".$wanopa; 
								$query = $query." 	and Mormes between 1 and ".$row[3];
								$query = $query."   and Moremp = '".$wemp."' ";
								$query = $query." 	and Morcco = '".$row[1]."'";
								$query = $query."   and Morcod = '".$row[2]."'";
								$err1 = mysql_query($query,$conex);
								$row1 = mysql_fetch_array($err1);
								$totan=$row1[0];
							}
							$mes=$row[3] + 1;
							for ($j=$mes;$j<= 12;$j++)
							{
								if($row[4] != "1")
								{
									$query = "select  sum(Morcan)  from ".$empresa."_000032 ";
									$query = $query." where Morano =  ".$wanopa; 
									$query = $query." 	and Mormes = ".$j;
									$query = $query."   and Moremp = '".$wemp."' ";
									$query = $query." 	and Morcco = '".$row[1]."'";
									$query = $query."   and Morcod = '".$row[2]."'";
									$err1 = mysql_query($query,$conex);
									$row1 = mysql_fetch_array($err1);
									$ing = $row1[0] * ($totac / $totan);
								}
								$fecha = date("Y-m-d");
								$hora = (string)date("H:i:s");
								$query = "insert ".$empresa."_000021 (medico,fecha_data,hora_data,Apremp, Aprano, Aprmes, Aprcco, Aprcod, Aprcan, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$row[0].",".$j.",'".$row[1]."','".$row[2]."',".round($ing, 0).",'C-".$empresa."')";
								$err2 = mysql_query($query,$conex);
								if ($err2 != 1)
									echo mysql_errno().":".mysql_error()."<br>";
								else
								{
									$k++;
									echo "REGISTRO INSERTADO  : ".$k." CENTRO DE COSTOS : ".$row[1]." LINEA".$row[2]."<br>";
								}
							}
						}
					}
					echo "<b>NUMERO DE REGISTROS INSERTADOS : ".$k."</b><br>";
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
