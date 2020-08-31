<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Calculo de  Incrementos Nominales x Unidad x Escenario (T10)</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2><b> 000001_pro92.php Ver. 2015-09-25</b></font></td></tr></table>
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
		

		

		echo "<form action='000001_pro92.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione")
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTACION</td></tr>";
			echo "<tr><td align=center colspan=2>CALCULO DE  INCREMENTOS NOMINALES X UNIDAD X ESCENARIO (T10)</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>A&ntilde;o de Presupuestacion</td>";
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
			#INICIO PROGRAMA
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
				$data=array();
				$wanopa=$wanop - 1;
				$query = "DELETE from ".$empresa."_000010 ";
				$query = $query." where ineano = ".$wanop;
				$query = $query."   and ineemp = '".$wemp."' ";
				$err = mysql_query($query,$conex);
				$query = "select Piaes1, Piaes2, Piaes3 from ".$empresa."_000006 ";
				$query = $query." where Piacin =  'I' "; 
				$query = $query."   and piaano =  ".$wanop; 
				$query = $query."   and piaemp = '".$wemp."' ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				$est=array();
				if($num > 0)
				{
					$row = mysql_fetch_array($err);
					$est[0]=$row[0];
					$est[1]=$row[1];
					$est[2]=$row[2];
				}
				else
				{
					$est[0]=0;
					$est[1]=0;
					$est[2]=0;
				}
				$query = "select Piaano,Siucco,sum(Siupor * Piaes1 /100),sum(Siupor * Piaes2 /100),sum(Siupor * Piaes3 /100) from ".$empresa."_000009,".$empresa."_000006 ";
				$query = $query." where siuano =  ".$wanopa; 
				$query = $query."   and siuemp = '".$wemp."' ";
				$query = $query."   and piaano =  ".$wanop; 
				$query = $query."   and siuemp = piaemp  ";
				$query = $query."   and siucin = piacin  ";
				$query = $query." group by piaano,siucco  ";
				$query = $query." order by piaano,siucco ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if($num > 0)
				{
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$query = "insert ".$empresa."_000010 (medico,fecha_data,hora_data,ineemp, Ineano, Inecco, Inetip, Inees1, Inees2, Inees3, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",'".$row[1]."','H',".$row[2].",".$row[3].",".$row[4].",'C-".$empresa."')";
						$err2 = mysql_query($query,$conex);
						if ($err2 != 1)
							echo mysql_errno().":".mysql_error()."<br>";
						else
						{
							$k++;
							echo "REGISTROS INSERTADOS H : ".$k."<br>";
						}
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$query = "insert ".$empresa."_000010 (medico,fecha_data,hora_data,ineemp, Ineano, Inecco, Inetip, Inees1, Inees2, Inees3, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",'".$row[1]."','I',".$est[0].",".$est[1].",".$est[2].",'C-".$empresa."')";
						$err2 = mysql_query($query,$conex);
						if ($err2 != 1)
							echo mysql_errno().":".mysql_error()."<br>";
						else
						{
							$k++;
							echo "REGISTROS INSERTADOS I : ".$k."<br>";
						}
						$query = "select Cittip,Citpor from ".$empresa."_000007 ";
						$query = $query." where Citano =  ".$wanopa; 
						$query = $query."   and Citemp = '".$wemp."' ";
						$query = $query."   and Citcco ='".$row[1]."'";
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						$ponH=0;
						$ponI=0;
						if($num1 > 0)
						{
							for ($j=0;$j<$num1;$j++)
							{
								$row1 = mysql_fetch_array($err1);
								if($row1[0] == "H")
									$ponH=$row1[1];
								else
									$ponI=$row1[1];
							}
						}
						$est1=(($row[2]*$ponH ) + ($est[0]*$ponI))/100;
						$est2=(($row[3]*$ponH) + ($est[1]*$ponI ))/100;
						$est3=(($row[4]*$ponH) + ($est[2]*$ponI))/100;
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$query = "insert ".$empresa."_000010 (medico,fecha_data,hora_data,ineemp, Ineano, Inecco, Inetip, Inees1, Inees2, Inees3, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",'".$row[1]."','P',".$est1.",".$est2.",".$est3.",'C-".$empresa."')";
						$err2 = mysql_query($query,$conex);
						if ($err2 != 1)
							echo mysql_errno().":".mysql_error()."<br>";
						else
						{
							$k++;
							echo "REGISTROS INSERTADOS P : ".$k."<br>";
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
