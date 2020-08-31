<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Evaluacion Convenios Linea Insumos</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro148.php Ver. 2011-06-09</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
function completo($empresa,$cant)
{
	while(strlen($empresa) < $cant) $empresa = "0".$empresa;
	return $empresa;
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro148.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wper1) or !isset($wanop) or $wper1 < 1 or $wper1 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE COSTOS</td></tr>";
			echo "<tr><td align=center colspan=2>EVALUACION CONVENIOS LINEA INSUMOS</td></tr>";
			echo "<tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Ano  Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$query = "delete from ".$empresa."_000108 ";
			$query .= "  where Mosano = ".$wanop;
			$query .= "    and Mosmes = ".$wper1;
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			
			//                                  0                      1                       2                                                      3                                                                           4                                            5                         6
			$query  = "select ".$empresa."_000111.Mitano,".$empresa."_000111.Mitmes,".$empresa."_000111.Mitcco,concat(repeat('0',4-length(".$empresa."_000111.Mitent)),".$empresa."_000111.Mitent),concat(repeat('0',6-length(".$empresa."_000111.Mitins)),".$empresa."_000111.Mitins),sum(".$empresa."_000111.Mitcan),sum(".$empresa."_000093.Mincpr) from ".$empresa."_000111,".$empresa."_000093 ";
			$query .= "  where ".$empresa."_000111.mitano = ".$wanop; 
			$query .= "    and ".$empresa."_000111.mitmes = ".$wper1; 
			$query .= "    and ".$empresa."_000111.mitano = ".$empresa."_000093.minano ";
			$query .= "    and ".$empresa."_000111.mitmes = ".$empresa."_000093.minmes ";
			$query .= "    and ".$empresa."_000111.mitins = ".$empresa."_000093.mincod "; 
			$query .= "   group by 1,2,3,4,5 ";
			$query .= "   order by 1,2,3,4,5 ";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			//                                  0                      1                       2                                                      3                                                                           4                                            5                      6     
			$query  = "select ".$empresa."_000137.Fddano,".$empresa."_000137.Fddmes,".$empresa."_000137.Fddcco,concat(repeat('0',4-length(".$empresa."_000137.Fddnit)),".$empresa."_000137.Fddnit),concat(repeat('0',6-length(".$empresa."_000137.Fddcod)),".$empresa."_000137.Fddcod),sum(".$empresa."_000137.Fddite),sum(".$empresa."_000137.Fddipr) from ".$empresa."_000137,".$empresa."_000060 ";
			$query .= "  where ".$empresa."_000137.fddano = ".$wanop; 
			$query .= "    and ".$empresa."_000137.fddmes = ".$wper1; 
			$query .= "    and ".$empresa."_000137.fddcon = ".$empresa."_000060.Cfacod ";
			$query .= "    and ".$empresa."_000060.Cfalin = '1' ";
			$query .= "   group by 1,2,3,4,5 ";
			$query .= "   order by 1,2,3,4,5 ";
			$err2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($err2);
			$wdata=array();
			$k1=0;
			$k2=0;
			$num=-1;
			if ($num1 ==  0)
			{
				$key1='zzzzzzzzzzzzzz';
				$k1=1;
			}
			else
			{
				$row1 = mysql_fetch_array($err1);
				$key1= $row1[2].completo($row1[3],4).completo($row1[4],6);
				$k1++;
			}
			if ($num2 ==  0)
			{
				$key2='zzzzzzzzzzzzzz';
				$k2=1;
			}
			else
			{
				$row2 = mysql_fetch_array($err2);
				$key2= $row2[2].completo($row2[3],4).completo($row2[4],6);
				$k2++;
			}
			while ($k1 <= $num1 or $k2 <= $num2)
			{
				if($key1 == $key2)
				{
					$num++;
					$wdata[$num][0] =$row1[0];
					$wdata[$num][1] =$row1[1];
					$wdata[$num][2] ="1";
					$wdata[$num][3] =$row1[2];
					$wdata[$num][4] =$row1[3];
					$wdata[$num][5] ="0";
					$wdata[$num][6] =$row1[4];
					$wdata[$num][7] =$row1[5];
					$wdata[$num][8] =$row2[6];
					$wdata[$num][9] =$row2[5];
					$wdata[$num][10]=$row1[5]*$row1[6];
					$wdata[$num][11]=$wdata[$num][8] - $wdata[$num][10];
					$wdata[$num][12]=$wdata[$num][10];
					$wdata[$num][13]=$wdata[$num][11];
					$wdata[$num][14]="on";
					$k1++;
					$k2++;
					if($k1 > $num1)
						$key1="zzzzzzzzzzzzzz";
					else
					{
						$row1 = mysql_fetch_array($err1);
						$key1= $row1[2].completo($row1[3],4).completo($row1[4],6);
					}
					if($k2 > $num2)
						$key2="zzzzzzzzzzzzzz";
					else
					{
						$row2 = mysql_fetch_array($err2);
						$key2= $row2[2].completo($row2[3],4).completo($row2[4],6);
					}
				}
				else if($key1 < $key2)
				{
					$num++;
					$wdata[$num][0] =$row1[0];
					$wdata[$num][1] =$row1[1];
					$wdata[$num][2] ="1";
					$wdata[$num][3] =$row1[2];
					$wdata[$num][4] =$row1[3];
					$wdata[$num][5] ="0";
					$wdata[$num][6] =$row1[4];
					$wdata[$num][7] =$row1[5];
					$wdata[$num][8] =0;
					$wdata[$num][9] =0;
					$wdata[$num][10]=$row1[5]*$row1[6];
					$wdata[$num][11]=$wdata[$num][8] - $wdata[$num][10];
					$wdata[$num][12]=$wdata[$num][10];
					$wdata[$num][13]=$wdata[$num][11];
					$wdata[$num][14]="off";
					$k1++;
					if($k1 > $num1)
						$key1="zzzzzzzzzzzzzz";
					else
					{
						$row1 = mysql_fetch_array($err1);
						$key1= $row1[2].completo($row1[3],4).completo($row1[4],6);
					}
				}
				else
				{
					$num++;
					$wdata[$num][0] =$row2[0];
					$wdata[$num][1] =$row2[1];
					$wdata[$num][2] ="1";
					$wdata[$num][3] =$row2[2];
					$wdata[$num][4] =$row2[3];
					$wdata[$num][5] ="0";
					$wdata[$num][6] =$row2[4];
					$wdata[$num][7] =0;
					$wdata[$num][8] =$row2[6];
					$wdata[$num][9] =$row2[5];
					$wdata[$num][10]=0;
					$wdata[$num][11]=$wdata[$num][8] - $wdata[$num][10];
					$wdata[$num][12]=$wdata[$num][10];
					$wdata[$num][13]=$wdata[$num][11];
					if($wdata[$num][6] != "0")
						$wdata[$num][14]="off";
					else
						$wdata[$num][14]="on";
					$k2++;
					if($k2 > $num2)
						$key2="zzzzzzzzzzzzzz";
					else
					{
						$row2 = mysql_fetch_array($err2);
						$key2= $row2[2].completo($row2[3],4).completo($row2[4],6);
					}
				}
			}
			$k=0;
			for ($i=0;$i<=$num;$i++)
			{
				$k++;
				echo "REGISTRO INSERTADO  : ".$k."<br>";
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
				$query = "insert ".$empresa."_000108 (medico,fecha_data,hora_data, Mosano, Mosmes, Moslin, Moscco, Mosent, Moscon, Mospro, Moscan, Mosipr, Mosite, Mosctt, Mosutt, Mosctv, Mosutv, Mostip, seguridad) values ('".$empresa."','".$fecha."','".$hora."',".$wdata[$i][0].",".$wdata[$i][1].",'".$wdata[$i][2]."','".$wdata[$i][3]."','".$wdata[$i][4]."','".$wdata[$i][5]."','".$wdata[$i][6]."',".$wdata[$i][7].",".$wdata[$i][8].",".$wdata[$i][9].",".$wdata[$i][10].",".$wdata[$i][11].",".$wdata[$i][12].",".$wdata[$i][13].",'".$wdata[$i][14]."','C-".$empresa."')";
				$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			}
			echo "<b>TOTAL REGISTROS INSERTADOS : ".$k."</b>";
		}
}
?>
</body>
</html>
