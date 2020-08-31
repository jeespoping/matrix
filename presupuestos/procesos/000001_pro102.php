<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Proyeccion Numero de Procedimientos x Linea (T31)</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro102.php Ver. 2015-11-06</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
function bisiesto($year)
{
	return(($year % 4 == 0 and $year % 100 != 0) or $year % 400 == 0);
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro102.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione")
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTACION</td></tr>";
			echo "<tr><td align=center colspan=2>PROYECCION NUMERO DE PROCEDIMIENTOS X LINEA (T31)</td></tr>";
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
				$wanopa=$wanop - 1;
				$query  = "SELECT 0,Perper,Perlab from ".$empresa."_000040  ";
				$query .= "  where Perano = ".$wanopa;
				$query .= " UNION ";
				$query .= " SELECT 1,Perper,Perlab from ".$empresa."_000040  ";
				$query .= "  where Perano = ".$wanop;
				$query .= " Order by 1,2 ";
				$err = mysql_query($query,$conex) or die("No Existe Tabla 40");
				$num = mysql_num_rows($err);
				if($num > 0)
				{
					$HABILES=array();
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						$HABILES[$row[1]][$row[0]]=$row[2];
					}
				}
				
				$k=0;
				$data=array();
				$wcco="";
				$wcod="";
				$query = "delete  from ".$empresa."_000031 ";
				$query = $query."  where Mopano = ".$wanop;
				$query = $query."    and Mopemp = '".$wemp."' ";
				$query = $query."       and Mopcco in (select  Cnpcco from ".$empresa."_000023 where Cnpemp = '".$wemp."' group by Cnpcco) ";
				$query = $query."       and Moptip = 'H' ";
				$err = mysql_query($query,$conex);
				
				$query =" select Morano, Mormes, Morcco, Morcod, Morcan  from ".$empresa."_000032, ".$empresa."_000022 ";
				$query = $query." where Morano = ".$wanopa; 
				$query = $query."   and Moremp = '".$wemp."' ";
				$query = $query."   and Mortip = 'P'";
				$query = $query."   and Morcco in (select  Cnpcco from ".$empresa."_000023 where Cnpemp = '".$wemp."' group by Cnpcco)  "; 
				$query = $query."   and Morano = Ocnano  ";
				$query = $query."   and Morcco = Ocncco  ";
				$query = $query."   and Morcod = Ocncod  ";
				$query = $query."   and Ocntip != 0 ";
				$query = $query." UNION ";
				$query = $query." select Aprano , Aprmes , Aprcco , Aprcod , Aprcan  from ".$empresa."_000021 ";
				$query = $query." where Aprano = ".$wanopa; 
				$query = $query."   and Apremp = '".$wemp."' ";
				$query = $query."   and Aprcco in (select  Cnpcco from ".$empresa."_000023 where Cnpemp = '".$wemp."' group by Cnpcco)  "; 
				$query = $query." order by 1,3,4,2  "; 
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()."<br>");
				$num = mysql_num_rows($err);
				if($num > 0)
				{
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						if($wcco != $row[2] or $wcod != $row[3])
						{
							if($i != 0)
							{
								//                 0        1       2       3       4      5       6        7
								$query =" select Cnptin, Cnptpr, Cnpini, Cnpper, Cnpmax, Cnpinc, Cnpene, Cnpdic from ".$empresa."_000023 ";
								$query = $query." where Cnpano = ".$wanop; 
								$query = $query."   and Cnpemp = '".$wemp."' ";
								$query = $query."   and Cnpcco = '".$wcco."'"; 
								$query = $query."   and Cnpcod = '".$wcod."'"; 
								$err1 = mysql_query($query,$conex);
								$num1 = mysql_num_rows($err1);
								if($num1 > 0)
								{
									$row1 = mysql_fetch_array($err1);
									if($row1[0] == "on" and $row1[1] == "on")
									{
										for ($j=1;$j<=12;$j++)
										{
											$data[$j][0] = $data[$j][0] / $HABILES[$j][0] * $HABILES[$j][1]; 
										}
										for ($j=1;$j<=12;$j++)
										{
											if($j >= $row1[3])
												$data[$j][0] = $data[$j][0] * (1 + ($row1[2] / 100));
										}
									}
									elseif($row1[0] == "on" and $row1[1] == "off")
										{
											$wsuma=0;
											$wk=0;
											for ($j=1;$j<=12;$j++)
											{
												//$wsuma += ($data[$j][0] * $data[$j][1]);
												$wsuma += ($data[$j][0]);
	/*
												if($data[$j][1] == 1)
													$wk++;
	*/
											}
	/*
											if($wk != 0)
												$wprom=$wsuma / $wk;
											else
												$wprom=0;
	*/
											$wprom=$wsuma / 12;
											$wprome=$wprom * (1 + ($row1[6] / 100));
											$wpromd=$wprom * (1 + ($row1[7] / 100));
											$wpromr=(($wprom * 12) - $wprome - $wpromd)/10;
											
											for ($j=1;$j<=12;$j++)
											{
												switch ($j)
												{
													case 1:
														$wpromg = $wprome;
													break;
													case 12;
														$wpromg = $wpromd;
													break;
													default:
														$wpromg = $wpromr;
												}
													
												if($j >= $row1[3])
													$data[$j][0] = $wpromg * (1 + ($row1[2] / 100));
												else
													$data[$j][0] = $wpromg;
											}
										}
										elseif($row1[0] == "off" and $row1[1] == "on")
											{	
												for ($j=1;$j<=12;$j++)
												{
													$data[$j][0] = $data[$j][0] / $HABILES[$j][0] * $HABILES[$j][1]; 
												}
												$winc=$row1[2];
												for ($j=1;$j<=12;$j++)
													if($j >= $row1[3])
													{
														$data[$j][0] = $data[$j][0] + $winc;
														$winc += $row1[5];
														if($winc > $row1[4])
															$winc = $row1[4]; 
													}
											}
											else
											{
												$wsuma=0;
												$wk=0;
												for ($j=1;$j<=12;$j++)
												{
													$wsuma += ($data[$j][0]);
	/*
													$wsuma += ($data[$j][0] * $data[$j][1]);
													if($data[$j][1] == 1)
														$wk++;
	*/
												}
	/*
												if($wk != 0)
													$wprom=$wsuma / $wk;
												else
													$wprom=0;
	*/
												$wprom=$wsuma / 12;
												$wprome=$wprom * (1 + ($row1[6] / 100));
												$wpromd=$wprom * (1 + ($row1[7] / 100));
												$wpromr=(($wprom * 12) - $wprome - $wpromd)/10;
												$winc=$row1[2];
												for ($j=1;$j<=12;$j++)
												{
													switch ($j)
													{
														case 1:
															$wpromg = $wprome;
														break;
														case 12;
															$wpromg = $wpromd;
														break;
														default:
															$wpromg = $wpromr;
													}
													if($j >= $row1[3])
													{
														$data[$j][0] = $wpromg + $winc;
														$winc += $row1[5];
														if($winc > $row1[4])
															$winc = $row1[4]; 
													}
													else
														$data[$j][0] = $wpromg;
												}
											}
								}
								for ($j=1;$j<=12;$j++)
								{
									$fecha = date("Y-m-d");
									$hora = (string)date("H:i:s");
									$query = "insert ".$empresa."_000031 (medico,fecha_data,hora_data,Mopemp, Mopano, Mopmes, Mopcco, Mopcod, Mopcan, Moptip, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$j.",'".$wcco."','".$wcod."',".round($data[$j][0], 0).",'H','C-".$empresa."')";
									$err2 = mysql_query($query,$conex);
									if ($err2 != 1)
										echo mysql_errno().":".mysql_error()."<br>";
									else
									{
										$k++;
										echo "REGISTRO INSERTADO  : ".$k."<br>";
									}
								}
							}
							$wcco=$row[2];
							$wcod=$row[3];
							for ($j=1;$j<=12;$j++)
							{
								$data[$j][0]=0;
								$data[$j][1]=0;
							}
						}
						$data[$row[1]][0]=$row[4];
						$data[$row[1]][1]=1;
					}
					$i=$num;
					if($i != 0)
					{
						//                 0        1       2       3       4      5       6        7
						$query =" select Cnptin, Cnptpr, Cnpini, Cnpper, Cnpmax, Cnpinc, Cnpene, Cnpdic from ".$empresa."_000023 ";
						$query = $query." where Cnpano = ".$wanop; 
						$query = $query."   and Cnpemp = '".$wemp."' ";
						$query = $query."     and Cnpcco = '".$wcco."'"; 
						$query = $query."     and Cnpcod = '".$wcod."'"; 
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						if($num1 > 0)
						{
							$row1 = mysql_fetch_array($err1);
							if($row1[0] == "on" and $row1[1] == "on")
							{
								for ($j=1;$j<=12;$j++)
								{
									$data[$j][0] = $data[$j][0] / $HABILES[$j][0] * $HABILES[$j][1]; 
								}
								for ($j=1;$j<=12;$j++)
								{
									if($j >= $row1[3])
										$data[$j][0] = $data[$j][0] * (1 + ($row1[2] / 100));
								}
							}
							elseif($row1[0] == "on" and $row1[1] == "off")
								{
									$wsuma=0;
									$wk=0;
									for ($j=1;$j<=12;$j++)
									{
										//$wsuma += ($data[$j][0] * $data[$j][1]);
										$wsuma += ($data[$j][0]);
	/*
										if($data[$j][1] == 1)
											$wk++;
	*/
									}
	/*
									if($wk != 0)
										$wprom=$wsuma / $wk;
									else
										$wprom=0;
	*/
									$wprom=$wsuma / 12;
									$wprome=$wprom * (1 + ($row1[6] / 100));
									$wpromd=$wprom * (1 + ($row1[7] / 100));
									$wpromr=(($wprom * 12) - $wprome - $wpromd)/10;
									
									for ($j=1;$j<=12;$j++)
									{
										switch ($j)
										{
											case 1:
												$wpromg = $wprome;
											break;
											case 12;
												$wpromg = $wpromd;
											break;
											default:
												$wpromg = $wpromr;
										}
											
										if($j >= $row1[3])
											$data[$j][0] = $wpromg * (1 + ($row1[2] / 100));
										else
											$data[$j][0] = $wpromg;
									}
								}
								elseif($row1[0] == "off" and $row1[1] == "on")
									{	
										for ($j=1;$j<=12;$j++)
										{
											$data[$j][0] = $data[$j][0] / $HABILES[$j][0] * $HABILES[$j][1]; 
										}
										$winc=$row1[2];
										for ($j=1;$j<=12;$j++)
											if($j >= $row1[3])
											{
												$data[$j][0] = $data[$j][0] + $winc;
												$winc += $row1[5];
												if($winc > $row1[4])
													$winc = $row1[4]; 
											}
									}
									else
									{
										$wsuma=0;
										$wk=0;
										for ($j=1;$j<=12;$j++)
										{
											$wsuma += ($data[$j][0]);
	/*
											$wsuma += ($data[$j][0] * $data[$j][1]);
											if($data[$j][1] == 1)
												$wk++;
	*/
										}
	/*
										if($wk != 0)
											$wprom=$wsuma / $wk;
										else
											$wprom=0;
	*/
										$wprom=$wsuma / 12;
										$wprome=$wprom * (1 + ($row1[6] / 100));
										$wpromd=$wprom * (1 + ($row1[7] / 100));
										$wpromr=(($wprom * 12) - $wprome - $wpromd)/10;
										$winc=$row1[2];
										for ($j=1;$j<=12;$j++)
										{
											switch ($j)
											{
												case 1:
													$wpromg = $wprome;
												break;
												case 12;
													$wpromg = $wpromd;
												break;
												default:
													$wpromg = $wpromr;
											}
											if($j >= $row1[3])
											{
												$data[$j][0] = $wpromg + $winc;
												$winc += $row1[5];
												if($winc > $row1[4])
													$winc = $row1[4]; 
											}
											else
												$data[$j][0] = $wpromg;
										}
									}
						}
						for ($j=1;$j<=12;$j++)
						{
							$fecha = date("Y-m-d");
							$hora = (string)date("H:i:s");
							$query = "insert ".$empresa."_000031 (medico,fecha_data,hora_data,Mopemp, Mopano, Mopmes, Mopcco, Mopcod, Mopcan, Moptip, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$j.",'".$wcco."','".$wcod."',".round($data[$j][0], 0).",'H','C-".$empresa."')";
							$err2 = mysql_query($query,$conex);
							if ($err2 != 1)
								echo mysql_errno().":".mysql_error()."<br>";
							else
							{
								$k++;
								echo "REGISTRO INSERTADO  : ".$k."<br>";
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
