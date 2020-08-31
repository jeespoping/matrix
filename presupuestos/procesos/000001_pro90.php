<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Calculo de Ingresos x Tipo x C.C. (T7)</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro90.php Ver. 2015-09-25</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro90.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wanob) or !isset($wper1)  or !isset($wper2)  or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTACION</td></tr>";
			echo "<tr><td align=center colspan=2>CALCULO DE INGRESOS X TIPO X C.C. (T7)</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>A&ntilde;o Base</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<td bgcolor=#cccccc align=center>A&ntilde;o de Presupuestacion</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanob' size=4 maxlength=4></td></tr>";
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
				#INICIO PROGRAMA
				$query = "DELETE from ".$empresa."_000007 ";
				$query = $query." where Citano = ".$wanop;
				$query = $query."   and Citemp = '".$wemp."' ";
				$err = mysql_query($query,$conex);
				$query = "select Mioano,Miocco,Pretip,sum(Mioito) from ".$empresa."_000063,".$empresa."_000060,".$empresa."_000003 ";
				$query = $query." where mioano =  ".$wanop; 
				$query = $query."   and mioemp = '".$wemp."' ";
				$query = $query."   and miomes between ".$wper1." and ".$wper2;
				$query = $query."   and mioemp = cfaemp ";
				$query = $query."   and miocfa = cfacod ";
				$query = $query."   and cfaclas = precod  ";
				$query = $query." group by mioano,miocco,pretip  ";
				$query = $query." order by miocco ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				$uni="";
				$data=array();
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
								$sum = 0;
								$w=-1;
								$data[0][0]="";
								$data[0][1]=0;
								$data[1][0]="";
								$data[1][1]=0;
							}
							else
							{
								for ($j=0;$j<=$w;$j++)
								{
									$fecha = date("Y-m-d");
									$hora = (string)date("H:i:s");
									$data[$j][1]=$data[$j][1] / $sum * 100;
									$query = "insert ".$empresa."_000007 (medico,fecha_data,hora_data,Citemp, Citano, Citcco, Cittip, Citpor, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",'".$uni."','".$data[$j][0]."',".$data[$j][1].",'C-".$empresa."')";
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
								$sum = 0;
								$w=-1;
								$data[0][0]="";
								$data[0][1]=0;
								$data[1][0]="";
								$data[1][1]=0;
							}
						}
						$w++;
						$data[$w][0]=$row[2];
						$data[$w][1]=$row[3];
						$sum+=$row[3];
					}
					for ($j=0;$j<=$w;$j++)
					{
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$data[$j][1]=$data[$j][1] / $sum * 100;
						$query = "insert ".$empresa."_000007 (medico,fecha_data,hora_data,Citemp, Citano, Citcco, Cittip, Citpor, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",'".$uni."','".$data[$j][0]."',".$data[$j][1].",'C-".$empresa."')";
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
