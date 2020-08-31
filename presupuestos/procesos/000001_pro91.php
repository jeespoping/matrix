<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Calculo Segmentacion del Ingreso x C.C.(T9)</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro91.php Ver. 2015-09-25</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
function buscar($cia,&$data,$n)
{
	$wsw=-1;
	for ($i=0;$i<$n-1;$i++)
		if($cia == $data[$i][0])
			$wsw=$i;
	return $wsw;
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro91.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1)  or !isset($wper2)  or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTACION</td></tr>";
			echo "<tr><td align=center colspan=2>CALCULO SEGMENTACION DEL INGRESO X C.C. (T9)</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>A&ntilde;o Base</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
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
			$wemp = substr($wemp,0,2);
			#INICIO PROGRAMA
			$k=0;
			$wanob = $wanop + 1;
			$query = "SELECT Cierre_Ppto from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanob;
			$query = $query."    and mes = 0 ";
			$query = $query."    and Emp = '".$wemp."' ";
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if($num > 0 and $row[0] == "on")
			{
				$data=array();
				$query = "DELETE from ".$empresa."_000009 ";
				$query = $query." where siuano = ".$wanop;
				$query = $query."   and siuemp = '".$wemp."' ";
				$err = mysql_query($query,$conex);
				$wanopa=$wanop + 1;
				$query = "select Piacin from ".$empresa."_000006 ";
				$query = $query." where piaano = ".$wanopa;
				$query = $query."   and piaemp = '".$wemp."' ";
				$query = $query." order by piapos ";
				$err = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err);
				if($num1 > 0)
				{
					echo $num1."<br>";
					for ($i=0;$i<$num1- 2;$i++)
					{
						$row = mysql_fetch_array($err);
						$data[$i][0]=$row[0];
						$data[$i][1]=0;
					}
				}
				$data[$num1-2][0]="O";
				$data[$num1-2][1]=0;
				$data[$num1 - 1][0]="ALL";
				$data[$num1 - 1][1]=0;
				$query = "select Mioano,Miocco,Empcin,sum(Mioito) from ".$empresa."_000063,".$empresa."_000061 ";
				$query = $query." where mioano = ".$wanop; 
				$query = $query."   and mioemp = '".$wemp."' ";
				$query = $query."   and miomes between ".$wper1." and ".$wper2;
				$query = $query."   and mioemp = empemp  ";
				$query = $query."   and mionit = epmcod  ";
				$query = $query." group by mioano,miocco,empcin  ";
				$query = $query." order by miocco,empcin ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				$uni="";
				if($num > 0)
				{
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						if($uni !=$row[1])
						{
							if($i == 0)
							{
								$uni =$row[1];
								for ($j=0;$j<=$num1 - 1;$j++)
									$data[$j][1]=0;
							}
							else
							{
								for ($j=0;$j<$num1 - 1;$j++)
								{
									$fecha = date("Y-m-d");
									$hora = (string)date("H:i:s");
									if($data[$num1 - 1][1] != 0)
										$data[$j][1]=$data[$j][1] / $data[$num1 - 1][1]  * 100;
									else
										$data[$j][1]=0;
									$query = "insert ".$empresa."_000009 (medico,fecha_data,hora_data,Siuemp, Siuano, Siucco, Siucin, Siupor , seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",'".$uni."','".$data[$j][0]."',".$data[$j][1].",'C-".$empresa."')";
									$err2 = mysql_query($query,$conex);
									if ($err2 != 1)
										echo mysql_errno().":".mysql_error()."<br>";
									else
									{
										$k++;
										echo "REGISTROS INSERTADOS : ".$k."<br>";
									}
								}
								$uni =$row[1];
								for ($j=0;$j<=$num1 - 1;$j++)
									$data[$j][1]=0;
							}
						}
						$x=buscar($row[2],$data,$num1 - 1);
						if($x != -1)
							$data[$x][1]+=$row[3];
						else
							$data[$num1- 2][1]+=$row[3];
						$data[$num1 - 1][1]+=$row[3];
					}
					for ($j=0;$j<$num1 - 1;$j++)
					{
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$data[$j][1]=$data[$j][1] / $data[$num1 - 1][1] * 100;
						$query = "insert ".$empresa."_000009 (medico,fecha_data,hora_data,Siuemp, Siuano, Siucco, Siucin, Siupor , seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",'".$uni."','".$data[$j][0]."',".$data[$j][1].",'C-".$empresa."')";
						$err2 = mysql_query($query,$conex);
						if ($err2 != 1)
							echo mysql_errno().":".mysql_error()."<br>";
						else
						{
							$k++;
							echo "REGISTROS INSERTADOS : ".$k."<br>";
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
