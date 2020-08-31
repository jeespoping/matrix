<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Calculo de Ingresos En Pesos x Linea x CC</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro95.php Ver. 2011-09-15</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro95.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wcco1) or (!isset($wall) and  !isset($wcpto))  or !isset($wper1)  or !isset($wper2)  or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTACION</td></tr>";
			echo "<tr><td align=center colspan=2>CALCULO DE INGRESOS EN PESOS X LINEA X CC</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>A&ntilde;o Base</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Todos los Conceptos de Facturacion</td>";
			echo "<td bgcolor=#cccccc align=center><input type='checkbox' name='wall'></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Concepto de Facturacion</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcpto' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#dddddd colspan=2 align=center><input type='RADIO' name=tip value=1 checked> ADICIONAR <input type='RADIO' name=tip value=2> REEMPLAZAR</td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			#INICIO PROGRAMA
			$k1=0;
			$k2=0;
			$data=array();
			$query = "delete from ".$empresa."_000018 ";
			$query = $query." where Inpano =  ".$wanop; 
			$query = $query." 	and Inptip = 'A' " ;
			$err = mysql_query($query,$conex);
			if(isset($wall))
			{
				$query = "delete from ".$empresa."_000018 ";
				$query = $query." where Inpano =  ".$wanop; 
				$query = $query." 	and Inpcco = '".$wcco1."'";
				$err = mysql_query($query,$conex);
				$query = "select Cfaclas,sum(Mioito) as monto from ".$empresa."_000063,".$empresa."_000060 ";
				$query = $query." where mioano =  ".$wanop; 
				$query = $query." and miomes between ".$wper1." and ".$wper2;
				$query = $query." and miocco = '".$wcco1."'";
				$query = $query." and miocfa = cfacod ";
				$query = $query." group by cfaclas ";
				$query = $query." order by monto desc ";
				$err = mysql_query($query,$conex);
				$row = mysql_fetch_array($err);
				$linea=$row[0];
			}
			$query = "select Mioano,Miomes,Miocco,Cfaclas,sum(Mioint),sum(Mioito) from ".$empresa."_000063,".$empresa."_000060 ";
			$query = $query." where mioano = ".$wanop; 
			$query = $query." and miomes between ".$wper1." and ".$wper2;
			$query = $query." and miocco = '".$wcco1."'"; 
			if(!isset($wall))
				$query = $query." and miocfa = '".$wcpto."'"; 
			$query = $query." and miocfa = cfacod  ";
			$query = $query." group by cfaclas,mioano,miomes,miocco ";
			$query = $query." order by cfaclas,mioano,miomes,miocco ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$query = "select Inpano, Inpmes, Inpcco, Inplin, Inping, Inppte  from ".$empresa."_000018 ";
					$query = $query." where Inpano =  ".$row[0]; 
					$query = $query." 	and Inpmes = ".$row[1];
					$query = $query." 	and Inpcco = '".$row[2]."'";
					if(isset($linea))
						$query = $query."     and Inplin = '".$linea."'";
					else
						$query = $query."     and Inplin = '".$row[3]."'";
					$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					if($num1 > 0)
					{
						$row1 = mysql_fetch_array($err1);
						if($tip == 1)
						{
							$inti = ($row1[4] * ($row1[5] / 100)) + $row[4] ;
							$tot2= $row[5] + $row1[4];
							$tot2= round($tot2, 0);
							if ($tot2 != 0)
								$tot1= ($inti / $tot2) * 100;
							else
								$tot1= 0;
						}
						else
						{
							if($row[5] != 0)
								$tot1= ($row[4] / $row[5]) * 100;
							else
								$tot1=0;
							$tot2= round($row[5], 0);
						}
						if(isset($linea))
							$query = "update ".$empresa."_000018 set Inping=".$tot2.",Inppte=".$tot1.",Inptip='R'  where Inpano=".$row[0]." and Inpmes=".$row[1]." and  Inpcco='".$row[2]."' and  Inplin='".$linea."'";
						else
							$query = "update ".$empresa."_000018 set Inping=".$tot2.",Inppte=".$tot1.",Inptip='R'   where Inpano=".$row[0]." and Inpmes=".$row[1]." and  Inpcco='".$row[2]."' and  Inplin='".$row[3]."'";
	       				$err2 = mysql_query($query,$conex);
	           			$k1++;
	           			echo "REGISTROS ACTUALIZADO  : ".$k1."<br>";
           			}
           			else
           			{
	           			$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						if($row[5] != 0)
							$wpor = ($row[4] / $row[5]) * 100;
						else
							$wpor = 0;
						if(isset($linea))
							$query = "insert ".$empresa."_000018 (medico,fecha_data,hora_data, Inpano, Inpmes, Inpcco, Inplin, Inping, Inppte, Inptip, seguridad) values ('".$empresa."','".$fecha."','".$hora."',".$row[0].",".$row[1].",'".$row[2]."','".$linea."',".round($row[5], 0).",".$wpor.",'R','C-".$empresa."')";
						else
							$query = "insert ".$empresa."_000018 (medico,fecha_data,hora_data, Inpano, Inpmes, Inpcco, Inplin, Inping, Inppte, Inptip, seguridad) values ('".$empresa."','".$fecha."','".$hora."',".$row[0].",".$row[1].",'".$row[2]."','".$row[3]."',".round($row[5], 0).",".$wpor.",'R','C-".$empresa."')";
		       			$err2 = mysql_query($query,$conex);
		       			if ($err2 != 1)
							echo mysql_errno().":".mysql_error()."<br>";
						else
						{
		           			$k2++;
		           			echo "REGISTRO INSERTADO  : ".$k2."<br>";
	   					}
   					}
   				}
   				echo "<b>NUMERO DE REGISTROS ACTUALIZADOS : ".$k1."</b><br>";
   				echo "<b>NUMERO DE REGISTROS INSERTADOS : ".$k2."</b><br>";
			}
   		}
}		
?>
</body>
</html>
