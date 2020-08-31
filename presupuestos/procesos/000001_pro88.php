<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Generacion de Empresas x Escenario (Presupuestacion T6)</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro88.php Ver. 2015-09-07</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro88.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wanob) or !isset($wnum) or !isset($wper1) or !isset($wper2) or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTACION</td></tr>";
			echo "<tr><td align=center colspan=2>GENERACION DE EMPRESAS X ESCENARIO (PRESUPUESTACION T6)</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>A&ntilde;o Base</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<td bgcolor=#cccccc align=center>A&ntilde;o de Presupuestacion</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanob' size=4 maxlength=4></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Mes de Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Mes de Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Numero de Empresas</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wnum' size=2 maxlength=2></td></tr>";
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
			$query = "SELECT count(*) from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanob;
			$query = $query."    and Cierre_ppto =   'on' ";
			$query = $query."    and emp = '".$wemp."' ";
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if($row[0] > 0)
			{
				$wanopn = $wanop + 1;
				#INICIO PROGRAMA
				$query = "SELECT Empcin,Empdes,Empseg,sum(Mioito) as monto  from ".$empresa."_000063,".$empresa."_000061 ";
				$query = $query." where Mioano = ".$wanop;
				$query = $query."   and Miomes between ".$wper1." and ".$wper2;
				$query = $query."   and Mioemp =  '".$wemp."' ";
				$query = $query."   and Mioemp =  Empemp ";
				$query = $query."   and Mionit =  Epmcod ";
				$query = $query."   group by Empcin,Empdes,Empseg ";
				$query = $query."   order by monto desc ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if($num > 0)
				{
					$data=array();
					$k=0;
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						if($i <= $wnum-1)
						{
							$data[$k][0]=$wanopn;
							$data[$k][1]=$row[0];
							$data[$k][2]=$row[1];
							$data[$k][3]=$row[2];
							$data[$k][4]=0;
							$data[$k][5]=$k+1;
							$query = "SELECT Piaes1, Piaes2, Piaes3  from ".$empresa."_000006 ";
							$query = $query." where Piaano = ".$wanopn;
							$query = $query."   and Piacin = '".$row[0]."'";
							$query = $query."   and Piaemp = '".$wemp."'";
							$err1 = mysql_query($query,$conex);
							$num1 = mysql_num_rows($err1);
							if($num1 > 0)
							{
								$row1 = mysql_fetch_array($err1);
								$data[$k][6]=$row1[0];
								$data[$k][7]=$row1[1];
								$data[$k][8]=$row1[2];
							}
							else
							{
								$data[$k][6]=0;
								$data[$k][7]=0;
								$data[$k][8]=0;
							}
							$data[$k][9]=$row[3];
							$k++;
							if($k <= $wnum)
							{
								$data[$k][0]=$wanopn;
								$data[$k][1]="O";
								$data[$k][2]="OTRAS";
								$data[$k][3]="N/A";
								$data[$k][4]=0;
								$data[$k][5]=$k+1;
								$query = "SELECT Piaes1, Piaes2, Piaes3  from ".$empresa."_000006 ";
								$query = $query." where Piaano = ".$wanopn;
								$query = $query."   and Piacin = 'O' ";
								$query = $query."   and Piaemp = '".$wemp."'";
								$err1 = mysql_query($query,$conex);
								$num1 = mysql_num_rows($err1);
								if($num1 > 0)
								{
									$row1 = mysql_fetch_array($err1);
									$data[$k][6]=$row1[0];
									$data[$k][7]=$row1[1];
									$data[$k][8]=$row1[2];
								}
								else
								{
									$data[$k][6]=0;
									$data[$k][7]=0;
									$data[$k][8]=0;
								}
								$data[$k][9]=0;
							}
						}
						else
							$data[$k][9]+=$row[3];
					}
					$k++;
					$data[$k][0]=$wanopn;
					$data[$k][1]="I";
					$data[$k][2]="INSUMOS";
					$data[$k][3]="N/A";
					$data[$k][4]=0;
					$data[$k][5]=$k+1;
					$query = "SELECT Piaes1, Piaes2, Piaes3  from ".$empresa."_000006 ";
					$query = $query." where Piaano = ".$wanopn;
					$query = $query."   and Piacin = 'I' ";
					$query = $query."   and Piaemp = '".$wemp."'";
					$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					if($num1 > 0)
					{
						$row1 = mysql_fetch_array($err1);
						$data[$k][6]=$row1[0];
						$data[$k][7]=$row1[1];
						$data[$k][8]=$row1[2];
					}
					else
					{
						$data[$k][6]=0;
						$data[$k][7]=0;
						$data[$k][8]=0;
					}
					$data[$k][9]=0;
				}
				$tot=0;
				for ($i=0;$i<=$k;$i++)
					$tot+=$data[$i][9];
				for ($i=0;$i<=$k;$i++)
					$data[$i][4]=$data[$i][9] / $tot * 100;	
				echo "<center><table border=1>";
				$query = "delete from ".$empresa."_000006 ";
				$query = $query."  where Piaano = ".$wanopn;
				$query = $query."    and Piaemp = '".$wemp."'";
				$err1 = mysql_query($query,$conex);
				for ($i=0;$i<=$k;$i++)
				{
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$query = "insert ".$empresa."_000006 (medico,fecha_data,hora_data,Piaemp, Piaano, Piacin, Piades, Piaseg, Piappa, Piapos, Piaes1, Piaes2, Piaes3, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$data[$i][0].",'".$data[$i][1]."','".$data[$i][2]."','".$data[$i][3]."',".$data[$i][4].",".$data[$i][5].",".$data[$i][6].",".$data[$i][7].",".$data[$i][8].",'C-".$empresa."')";
					$err1 = mysql_query($query,$conex);
					echo "<tr><td>".$data[$i][0]."</td><td>".$data[$i][1]."</td><td>".$data[$i][2]."</td><td>".$data[$i][3]."</td><td>".$data[$i][4]."</td><td>".$data[$i][5]."</td><td>".$data[$i][6]."</td><td>".$data[$i][7]."</td><td>".$data[$i][8]."</td><td>".number_format((double)$data[$i][9],2,'.',',')."</td></tr>";
				}
				echo "</table>";
			}
			else
			{
				echo "<center><table border=0 aling=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>EL PROCESO DE PRESUPUESTACION YA ESTA CERRADO</MARQUEE></FONT>";
				echo "<br><br>";
			}
        }
}		
?>
</body>
</html>
