<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Analisis de Movimiento De Inventarios en Conceptos Asociados</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> Analisis.php Ver. 2008-09-30</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
function bisiesto($year)
{
	return(($year % 4 == 0 and $year % 100 != 0) or $year % 400 == 0);
}
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='Analisis.php' method=post>";
		echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wcon1) or !isset($wcon2) or !isset($wfec1) or !isset($wfec2) or !isset($wdate))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>ANALISIS DE MOVIMIENTO DE INVENTARIOS EN CONCEPTOS ASOCIADOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Concepto de Analisis</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcon1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Concepto de Asociado</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcon2' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Fecha Inicial</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wfec1' size=10 maxlength=10></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Fecha Final</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wfec2' size=10 maxlength=10></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Fecha de Corte</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wdate' size=10 maxlength=10></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			#INICIO PROGRAMA
			$query  = "select Concod, Conind, Condes from ".$empresa."_000008 ";
			$query .= "  where Concod = '".$wcon1."'";
			$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()."<br>");
			$num1 = mysql_num_rows($err1);
			$query  = "select Concod, Conind, Condes from ".$empresa."_000008 ";
			$query .= "  where Concod = '".$wcon2."'";
			$query .= "    and Concan = '".$wcon1."'";
			$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()."<br>");
			$num2 = mysql_num_rows($err2);
			if($num1 > 0)
				$row1 = mysql_fetch_array($err1);
			if($num2 > 0)
				$row2 = mysql_fetch_array($err2);
			if($num1 > 0 and $num2 > 0 and $row1[1] != $row2[1])
			{
				echo "<table border=0 align=center>";
				echo "<tr><td colspan=15 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
				echo "<tr><td colspan=15 align=center>DIRECCION DE INFORMATICA</td></tr>";
				echo "<tr><td colspan=15 align=center>ANALISIS DE MOVIMIENTO DE INVENTARIOS EN CONCEPTOS ASOCIADOS</td></tr>";
				echo "<tr><td colspan=15 align=center>CONCEPTO : <b>".$row1[0]."-".$row1[2]. "</b> CONCEPTO ASOCIADO : <b>".$row2[0]."-".$row2[2]."</b></td></tr>";
				echo "<tr><td colspan=15 align=center>DESDE : <b>".$wfec1. " </b>HASTA : <b>".$wfec2."</b></td></tr>";
				echo "<tr><td colspan=15 align=center>FECHA DE CORTE : <b>".$wdate."</b></td></tr>";
				if($row1[1] == "1")
				{
					echo "<tr><td align=center rowspan=2 bgcolor=#999999><font size=2><b>DOCUMENTO</b></font></font></td><td align=center rowspan=2 bgcolor=#999999><font size=2><b>ARTICULO</b></font></td><td align=center rowspan=2 bgcolor=#999999><font size=2><b>DESCRIPCION</b></font></td><td align=center rowspan=2 bgcolor=#999999><font size=2><b>PROVEEDOR/TERCERO</b></font></td><td colspan=4 align=center bgcolor=#999999><font size=2><b>ENTRADAS</b></font></td><td colspan=2 align=center bgcolor=#999999><font size=2><b>SALIDAS</b></font></td><td colspan=2 align=center bgcolor=#999999><font size=2><b>SALDO</b></font></td></tr>";
					echo "<tr><td align=right bgcolor=#999999><font size=2><b>CANTIDAD</b></font></td><td align=right bgcolor=#999999><font size=2><b>VALOR</b></font></td><td align=center bgcolor=#999999><font size=2><b>FECHA</b></font></td><td align=center bgcolor=#999999><font size=2><b>DOCUMENTO</b></font></td><td align=right bgcolor=#999999><font size=2><b>CANTIDAD</b></font></td><td align=right bgcolor=#999999><font size=2><b>VALOR</b></font></td><td align=right bgcolor=#999999><font size=2><b>CANTIDAD</b></font></td><td align=right bgcolor=#999999><font size=2><b>VALOR</b></font></td></tr>";
				}
				else
				{
					echo "<tr><td align=center rowspan=2 bgcolor=#999999><font size=2><b>DOCUMENTO</b></font></td><td align=center rowspan=2 bgcolor=#999999><font size=2><b>ARTICULO</b></font></td><td align=center rowspan=2 bgcolor=#999999><font size=2><b>DESCRIPCION</b></font></td><td align=center rowspan=2 bgcolor=#999999><font size=2><b>PROVEEDOR/TERCERO</b></font></td><td colspan=2 align=center bgcolor=#999999><font size=2><b>ENTRADAS</b></font></td><td colspan=4 align=center bgcolor=#999999><font size=2><b>SALIDAS</b></font></td><td colspan=2 align=center bgcolor=#999999><font size=2><b>SALDO</b></font></td></tr>";
					echo "<tr><td align=right bgcolor=#999999><font size=2><b>CANTIDAD</b></font></td><td align=right bgcolor=#999999><font size=2><b>VALOR</b></font></td><td align=right bgcolor=#999999><font size=2><b>CANTIDAD</b></font></td><td align=right bgcolor=#999999><font size=2><b>VALOR</b></font></td><td align=center bgcolor=#999999><font size=2><b>FECHA</b></font></td><td align=center bgcolor=#999999><font size=2><b>DOCUMENTO</b></font></td><td align=right bgcolor=#999999><font size=2><b>CANTIDAD</b></font></td><td align=right bgcolor=#999999><font size=2><b>VALOR</b></font></td></tr>";
				}
				//                  0      1     2      3      4      5      6      7      8      9 
				$query = "select mencon,Mendoc,Mdeart,Mdecan,Mdevto,Artnom,Menfec,Mendan,Mennit,Pronom from ".$empresa."_000010,".$empresa."_000011,".$empresa."_000001,".$empresa."_000006 ";
				$query .= "  where menfec between '".$wfec1."' and '".$wfec2."' ";
				$query .= "    and mencon = '".$wcon1."' ";
				$query .= "    and mencon = Mdecon ";
				$query .= "    and mendoc = Mdedoc ";
				$query .= "    and Mdeart = Artcod ";
				$query .= "    and Mennit = Pronit ";
				$query .= " union ";
				$query .= " select mencon,Mendan,Mdeart,Mdecan,Mdevto,Artnom,Menfec,Mendoc,Mennit,Pronom from ".$empresa."_000010,".$empresa."_000011,".$empresa."_000001,".$empresa."_000006  ";
				$query .= "  where mendan in(select mendoc from ".$empresa."_000010 where menfec between '".$wfec1."' and '".$wfec2."' and mencon = '".$wcon1."') ";
				$query .= "    and mencon = '".$wcon2."' ";
				$query .= "    and mencon = Mdecon ";
				$query .= "    and mendoc = Mdedoc ";
				$query .= "    and Mdeart = Artcod ";
				$query .= "    and Mennit = Pronit ";
				if($wcon1 > $wcon2)
					$query .= "  order by 2,1 desc,3,4 ";
				else
					$query .= "  order by 2,1,3,4 ";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()."<br>");
				$num = mysql_num_rows($err);
				$wdoc="";
				$wl=0;
				$wtotal1=0;
				$wtotal2=0;
				if($num > 0)
				{
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						if($row[4] < 0)
							$row[4] = $row[4] * (-1);
						if($wdoc != $row[1])
						{
							if($i != 0)
							{
								for ($j=1;$j<=$k;$j++)
								{
									$wl++;
									if($wl % 2 == 0)
										$color="#dddddd";
									else
										$color="#ffffff";
									$wsw1=0;
									if($data[$j][9] <= $wdate)
									{
										$data[$j][7]=$data[$j][3] - $data[$j][5];
										$data[$j][8]=$data[$j][4] - $data[$j][6];
										if($data[$j][9] != "")
											$wsw1=1;
									}
									elseif($row1[1] == "1")
										{
											$data[$j][7]=$data[$j][3] - 0;
											$data[$j][8]=$data[$j][4] - 0;
										}
										else
										{
											$data[$j][7]=0 - $data[$j][5];
											$data[$j][8]=0 - $data[$j][6];
										}
									$wtotal1 += $data[$j][8];
									$wtotal2 += $data[$j][4] - $data[$j][6];
									if($row1[1] == "1")
									{
										echo "<tr><td align=center bgcolor=".$color.">".$data[$j][0]."</td><td bgcolor=".$color.">".$data[$j][1]."</td><td bgcolor=".$color."><font size=2>".$data[$j][2]."</font></td><td bgcolor=".$color."><font size=2>".$data[$j][11]."</font></td><td align=right bgcolor=".$color.">".number_format((double)$data[$j][3],0,'.',',')."</td><td align=right bgcolor=".$color.">".number_format((double)$data[$j][4],0,'.',',')."</td>";
										if($wsw1 == 0)
											echo "<td align=center bgcolor=".$color.">".$data[$j][9]."</td><td align=center bgcolor=".$color.">".$data[$j][10]."</td><td align=right bgcolor=".$color.">".number_format((double)$data[$j][5],0,'.',',')."</td><td align=right bgcolor=".$color.">".number_format((double)$data[$j][6],0,'.',',')."</td><td align=right bgcolor=".$color.">".number_format((double)$data[$j][7],0,'.',',')."</td><td align=right bgcolor=".$color.">".number_format((double)$data[$j][8],0,'.',',')."</td></tr>";
										else
											echo "<td align=center bgcolor=#99CCFF>".$data[$j][9]."</td><td align=center bgcolor=".$color.">".$data[$j][10]."</td><td align=right bgcolor=".$color.">".number_format((double)$data[$j][5],0,'.',',')."</td><td align=right bgcolor=".$color.">".number_format((double)$data[$j][6],0,'.',',')."</td><td align=right bgcolor=".$color.">".number_format((double)$data[$j][7],0,'.',',')."</td><td align=right bgcolor=".$color.">".number_format((double)$data[$j][8],0,'.',',')."</td></tr>";
									}
									else
									{
										echo "<tr><td align=center bgcolor=".$color.">".$data[$j][0]."</td><td bgcolor=".$color.">".$data[$j][1]."</td><td bgcolor=".$color."><font size=2>".$data[$j][2]."</font></td><td bgcolor=".$color."><font size=2>".$data[$j][11]."</font></td><td align=right bgcolor=".$color.">".number_format((double)$data[$j][3],0,'.',',')."</td><td align=right bgcolor=".$color.">".number_format((double)$data[$j][4],0,'.',',')."</td><td align=right bgcolor=".$color.">".number_format((double)$data[$j][5],0,'.',',')."</td><td align=right bgcolor=".$color.">".number_format((double)$data[$j][6],0,'.',',')."</td>";
										if($wsw1 == 0)
											echo "<td align=center bgcolor=".$color.">".$data[$j][9]."</td><td align=center bgcolor=".$color.">".$data[$j][10]."</td><td align=right bgcolor=".$color.">".number_format((double)$data[$j][7],0,'.',',')."</td><td align=right bgcolor=".$color.">".number_format((double)$data[$j][8],0,'.',',')."</td></tr>";
										else
											echo "<td align=center bgcolor=#99CCFF>".$data[$j][9]."</td><td align=center bgcolor=".$color.">".$data[$j][10]."</td><td align=right bgcolor=".$color.">".number_format((double)$data[$j][7],0,'.',',')."</td><td align=right bgcolor=".$color.">".number_format((double)$data[$j][8],0,'.',',')."</td></tr>";
									}
								}
							}
							$wdoc = $row[1];
		   					$data=array();
		   					$k=0;
	   					}
	   					if($row[0] == $wcon1)
	   					{
		   					$k++;
		   					$data[$k][0]=$row[1];
		   					$data[$k][1]=$row[2];
		   					$data[$k][2]=$row[5];
		   					$data[$k][3] = 0;
			   				$data[$k][4] = 0;
			   				$data[$k][5] = 0;
			   				$data[$k][6] = 0;
			   				$data[$k][9] = "";
			   				$data[$k][10] = "";
			   				$data[$k][11] = $row[8]."-".$row[9];
		   					if($row1[1] == 1)
		   					{
			   					$data[$k][3] = $row[3];
			   					$data[$k][4] = $row[4];
		   					}
		   					else
		   					{
			   					$data[$k][5] = $row[3];
			   					$data[$k][6] = $row[4];
		   					}
		   					
	   					}
	   					else
	   					{
		   					$wsw=0;
		   					for ($j=1;$j<=$k;$j++)
		   					{
		   						if($data[$j][1] == $row[2])
		   						{
			   						$wsw=1;
			   						if($row2[1] == 1)
				   					{
					   					$data[$j][3] = $row[3];
					   					$data[$j][4] = $row[4];
					   					$data[$k][9] = $row[6];
			   							$data[$k][10] = $row[7];
				   					}
				   					else
				   					{
					   					$data[$j][5] = $row[3];
					   					$data[$j][6] = $row[4];
					   					$data[$k][9] = $row[6];
			   							$data[$k][10] = $row[7];
				   					}
				   					$j=$k+1;
		   						}
	   						}
	   						if($wsw == 0)
	   						{
		   						$k++;
			   					$data[$k][0]=$row[1];
			   					$data[$k][1]=$row[2];
			   					$data[$k][2]=$row[5];
			   					$data[$k][3] = 0;
				   				$data[$k][4] = 0;
				   				$data[$k][5] = 0;
				   				$data[$k][6] = 0;
				   				$data[$k][9] = "";
			   					$data[$k][10] = "";
			   					$data[$k][11] = $row[8]."-".$row[9];
			   					if($row2[1] == 1)
			   					{
				   					$data[$k][3] = $row[3];
				   					$data[$k][4] = $row[4];
				   					$data[$k][9] = $row[6];
			   						$data[$k][10] = $row[7];
			   					}
			   					else
			   					{
				   					$data[$k][5] = $row[3];
				   					$data[$k][6] = $row[4];
				   					$data[$k][9] = $row[6];
			   						$data[$k][10] = $row[7];
			   					}
	   						}
	   					}
	   					
   					}
	   				for ($j=1;$j<=$k;$j++)
					{
						$wl++;
						if($wl % 2 == 0)
							$color="#dddddd";
						else
							$color="#ffffff";
						$wsw1=0;
						if($data[$j][9] <= $wdate)
						{
							$data[$j][7]=$data[$j][3] - $data[$j][5];
							$data[$j][8]=$data[$j][4] - $data[$j][6];
							if($data[$j][9] != "")
								$wsw1=1;
						}
						elseif($row1[1] == "1")
							{
								$data[$j][7]=$data[$j][3] - 0;
								$data[$j][8]=$data[$j][4] - 0;
							}
							else
							{
								$data[$j][7]=0 - $data[$j][5];
								$data[$j][8]=0 - $data[$j][6];
							}
						$wtotal1 += $data[$j][8];
						$wtotal2 += $data[$j][4] - $data[$j][6];
						if($row1[1] == "1")
						{
							echo "<tr><td align=center bgcolor=".$color.">".$data[$j][0]."</td><td bgcolor=".$color.">".$data[$j][1]."</td><td bgcolor=".$color."><font size=2>".$data[$j][2]."</font></td><td bgcolor=".$color."><font size=2>".$data[$j][11]."</font></td><td align=right bgcolor=".$color.">".number_format((double)$data[$j][3],0,'.',',')."</td><td align=right bgcolor=".$color.">".number_format((double)$data[$j][4],0,'.',',')."</td>";
							if($wsw1 == 0)
								echo "<td align=center bgcolor=".$color.">".$data[$j][9]."</td><td align=center bgcolor=".$color.">".$data[$j][10]."</td><td align=right bgcolor=".$color.">".number_format((double)$data[$j][5],0,'.',',')."</td><td align=right bgcolor=".$color.">".number_format((double)$data[$j][6],0,'.',',')."</td><td align=right bgcolor=".$color.">".number_format((double)$data[$j][7],0,'.',',')."</td><td align=right bgcolor=".$color.">".number_format((double)$data[$j][8],0,'.',',')."</td></tr>";
							else
								echo "<td align=center bgcolor=#99CCFF>".$data[$j][9]."</td><td align=center bgcolor=".$color.">".$data[$j][10]."</td><td align=right bgcolor=".$color.">".number_format((double)$data[$j][5],0,'.',',')."</td><td align=right bgcolor=".$color.">".number_format((double)$data[$j][6],0,'.',',')."</td><td align=right bgcolor=".$color.">".number_format((double)$data[$j][7],0,'.',',')."</td><td align=right bgcolor=".$color.">".number_format((double)$data[$j][8],0,'.',',')."</td></tr>";
						}
						else
						{
							echo "<tr><td align=center bgcolor=".$color.">".$data[$j][0]."</td><td bgcolor=".$color.">".$data[$j][1]."</td><td bgcolor=".$color."><font size=2>".$data[$j][2]."</font></td><td bgcolor=".$color."><font size=2>".$data[$j][11]."</font></td><td align=right bgcolor=".$color.">".number_format((double)$data[$j][3],0,'.',',')."</td><td align=right bgcolor=".$color.">".number_format((double)$data[$j][4],0,'.',',')."</td><td align=right bgcolor=".$color.">".number_format((double)$data[$j][5],0,'.',',')."</td><td align=right bgcolor=".$color.">".number_format((double)$data[$j][6],0,'.',',')."</td>";
							if($wsw1 == 0)
								echo "<td align=center bgcolor=".$color.">".$data[$j][9]."</td><td align=center bgcolor=".$color.">".$data[$j][10]."</td><td align=right bgcolor=".$color.">".number_format((double)$data[$j][7],0,'.',',')."</td><td align=right bgcolor=".$color.">".number_format((double)$data[$j][8],0,'.',',')."</td></tr>";
							else
								echo "<td align=center bgcolor=#99CCFF>".$data[$j][9]."</td><td align=center bgcolor=".$color.">".$data[$j][10]."</td><td align=right bgcolor=".$color.">".number_format((double)$data[$j][7],0,'.',',')."</td><td align=right bgcolor=".$color.">".number_format((double)$data[$j][8],0,'.',',')."</td></tr>";
						}
					}
					echo "<tr><td bgcolor=#999999 colspan=11><b>SALDO X FECHA DE CORTE</b></td><td align=right bgcolor=#999999><b>".number_format((double)$wtotal1,0,'.',',')."</b></td></tr>";
					echo "<tr><td bgcolor=#999999 colspan=11><b>SALDO ACTUAL</b></td><td align=right bgcolor=#999999><b>".number_format((double)$wtotal2,0,'.',',')."</b></td></tr></table>";
				}
   			}
   			else
			{
				echo "<center><table border=0 aling=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>LOS CONCEPTOS NO ESTAN ASOCIADOS O NO EXISTEN O NO HACEN OPERACIONES CONTRARIAS</MARQUEE></FONT>";
				echo "<br><br>";			
			}
   		}
}		
?>
</body>
</html>
