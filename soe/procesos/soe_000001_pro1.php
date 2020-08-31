<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<script type="text/javascript">
<!--
	function enter()
	{
		document.forms.soe.submit();
	}
//-->
</script>
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Actualizacion de Odontograma x Paciente</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> soe_000001_pro1.php Ver. 2006-04-12</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='soe' action='soe_000001_pro1.php' method=post>";
	

	

	if(isset($ok1) and $actividad != "" and $W != ".")
	{
		$ubicacion="N/A";
		$fecha = date("Y-m-d");
		$hora = (string)date("H:i:s");
		$query = "insert soe1_000001 (medico,fecha_data,hora_data, Identificacion, Fecha, Hora, Diente, Actividad, Ubicacion, Comentarios, Odontologo, seguridad) values ('soe1','".$fecha."','".$hora."','".$paciente."','".$fecha."','".$hora."',99,'".$actividad."','".$ubicacion."','".$W."','".$key."','C-soe1')";
		$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		unset($ok);
		unset($R);
	}
	if(isset($ok))
	{
		switch ($S)
		{
			case 1:
				for ($i=1;$i<19;$i++)
				{
					if(isset($A[$i]) and (isset($U[8*$i - 7]) or isset($U[8*$i - 6]) or isset($U[8*$i - 5]) or isset($U[8*$i - 4]) or isset($U[8*$i - 3]) or isset($U[8*$i - 2]) or isset($U[8*$i - 1]) or isset($U[8*$i])))
					{
						if($i == 18)
							$actividad=$A1;
						else
							$actividad=$e[$i];
						$ubicacion="";
						if(isset($U[8*$i - 1]))
							$ubicacion="Todas";
						elseif(isset($U[8*$i ]))
									$ubicacion="N/A";
								else
								{
									if(isset($U[8*$i - 7]))
										$ubicacion .="Mesi/";
									if(isset($U[8*$i - 6]))
										$ubicacion .="Oclu/";
									if(isset($U[8*$i - 5]))
										$ubicacion .="Dist/";
									if(isset($U[8*$i - 4]))
										$ubicacion .="Vest/";
									if(isset($U[8*$i - 3]))
										$ubicacion .="Ling/";
									if(isset($U[8*$i - 2]))
										$ubicacion .="Cerv/";
								}
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						if(isset($PRE[$i]))
							$C[$i]=" PREEXISTENTE -- ".$C[$i];
						$query = "insert soe1_000001 (medico,fecha_data,hora_data, Identificacion, Fecha, Hora, Diente, Actividad, Ubicacion, Comentarios, Odontologo, seguridad) values ('soe1','".$fecha."','".$hora."','".$paciente."','".$fecha."','".$hora."','".$R1."','".$actividad."','".$ubicacion."','".$C[$i]."','".$key."','C-soe1')";
						$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					}
				}
				unset($ok);
				unset($R);
			break;
			case 2:
				for ($h=0;$h<$num;$h++)
					for ($l=0;$l<35;$l++)
						if(isset($M[$h-1][$l]))
						{
							for ($i=1;$i<19;$i++)
							{
								if(isset($A[$i][$E[$h-1][$l]]) and (isset($U[8*$i - 7][$E[$h-1][$l]]) or isset($U[8*$i - 6][$E[$h-1][$l]]) or isset($U[8*$i - 5][$E[$h-1][$l]]) or isset($U[8*$i - 4][$E[$h-1][$l]]) or isset($U[8*$i - 3][$E[$h-1][$l]]) or isset($U[8*$i - 2][$E[$h-1][$l]]) or isset($U[8*$i - 1][$E[$h-1][$l]]) or isset($U[8*$i][$E[$h-1][$l]])))
								{
									if($i == 18)
										$actividad=$A1[$E[$h-1][$l]];
									else
										$actividad=$e[$i];
									$ubicacion="";
									if(isset($U[8*$i - 1][$E[$h-1][$l]]))
										$ubicacion="Todas";
									elseif(isset($U[8*$i ][$E[$h-1][$l]]))
												$ubicacion="N/A";
											else
											{
												if(isset($U[8*$i - 7][$E[$h-1][$l]]))
													$ubicacion .="Mesi/";
												if(isset($U[8*$i - 6][$E[$h-1][$l]]))
													$ubicacion .="Oclu/";
												if(isset($U[8*$i - 5][$E[$h-1][$l]]))
													$ubicacion .="Dist/";
												if(isset($U[8*$i - 4][$E[$h-1][$l]]))
													$ubicacion .="Vest/";
												if(isset($U[8*$i - 3][$E[$h-1][$l]]))
													$ubicacion .="Ling/";
												if(isset($U[8*$i - 2][$E[$h-1][$l]]))
													$ubicacion .="Cerv/";
											}
									$fecha = date("Y-m-d");
									$hora = (string)date("H:i:s");
									if(isset($PRE[$i][$E[$h-1][$l]]))
										$C[$i][$E[$h-1][$l]]=" PREEXISTENTE -- ".$C[$i][$E[$h-1][$l]];
									$query = "insert soe1_000001 (medico,fecha_data,hora_data, Identificacion, Fecha, Hora, Diente, Actividad, Ubicacion, Comentarios, Odontologo, seguridad) values ('soe1','".$fecha."','".$hora."','".$paciente."','".$fecha."','".$hora."','".$E[$h-1][$l]."','".$actividad."','".$ubicacion."','".$C[$i][$E[$h-1][$l]]."','".$key."','C-soe1')";
									$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
								}
							}
						}
			break;
		}
	}
	if(isset($ok))
		unset($R);
	if(!isset($paciente))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>ACTUALIZACION DE ODONTOGRAMA X PACIENTE</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Paciente</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='paciente' size=12 maxlength=12></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$query = "select Identificacion, Nombre1, Nombre2, Apellido1  from  soe1_000002 ";
 		$query .= " where identificacion = '".$paciente."'";
		$err = mysql_query($query,$conex);
		$num1 = mysql_num_rows($err);
		if($num1 > 0)
		{
			$row1 = mysql_fetch_array($err);
			$nom=$row1[0]."-".$row1[1]." ".$row1[2]." ".$row1[3];
			
			echo "<table border=0 align=center>";
			echo "<tr><td  align=center bgcolor=#cccccc colspan=36 align=center valign=middle><IMG SRC='/MATRIX/images/medical/root/clinica.jpg' >&nbsp &nbsp <font size=5><b>PROMOTORA MEDICA LAS AMERICAS S.A. - SALUD ORAL ESPECIALIZADA (SOE)</b></font></td></tr>";
			echo "<tr><td bgcolor=#CCCCCC colspan=36 align=center><b>ACTUALIZACION DE ODONTOGRAMA X PACIENTE</b></td></tr>";
			echo "<tr><td bgcolor=#CCCCCC colspan=36 align=center><b>PACIENTE : ".$nom."</b></td></tr>";
			echo "<tr><td bgcolor=#999999 colspan=36 align=center><b><font size=4>ODONTOGRAMA</font></b></td></tr>";
			if(!isset($S))
				$S=1;
			echo "<tr><td bgcolor=#dddddd colspan=36 align=center>SELECION MULTIPLE : ";
			if($S == 1)
			{
				echo "<input type='radio' name='S' value=1 onclick='enter()' checked>Inactiva";
				echo"<input type='radio' name='S' value=2 onclick='enter()'>Activa";
			}
			else
			{
				echo "<input type='radio' name='S' value=1 onclick='enter()'>Inactiva";
				echo"<input type='radio' name='S' value=2 onclick='enter()' checked>Activa";
			}
			echo"</td></tr>";
			echo "<tr>";
			$D=array();
			$D[11]=false;
			$D[12]=false;
			$D[13]=false;
			$D[14]=false;
			$D[15]=false;
			$D[16]=false;
			$D[17]=false;
			$D[18]=false;
			$D[21]=false;
			$D[22]=false;
			$D[23]=false;
			$D[24]=false;
			$D[25]=false;
			$D[26]=false;
			$D[27]=false;
			$D[28]=false;
			$D[31]=false;
			$D[32]=false;
			$D[33]=false;
			$D[34]=false;
			$D[35]=false;
			$D[36]=false;
			$D[37]=false;
			$D[38]=false;
			$D[41]=false;
			$D[42]=false;
			$D[43]=false;
			$D[44]=false;
			$D[45]=false;
			$D[46]=false;
			$D[47]=false;
			$D[48]=false;
			$D[51]=false;
			$D[52]=false;
			$D[53]=false;
			$D[54]=false;
			$D[55]=false;
			$D[61]=false;
			$D[62]=false;
			$D[63]=false;
			$D[64]=false;
			$D[65]=false;
			$D[71]=false;
			$D[72]=false;
			$D[73]=false;
			$D[74]=false;
			$D[75]=false;
			$D[81]=false;
			$D[82]=false;
			$D[83]=false;
			$D[84]=false;
			$D[85]=false;
			$query = "select Diente from  soe1_000001 ";
	 		$query .= " where identificacion = '".$paciente." '";
	 		$query .= " Group by Diente ";
			$err = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err);
			if($num1 > 0)
			{
				for ($i=0;$i<$num1;$i++)
				{
					$row1 = mysql_fetch_array($err);
					$D[$row1[0]]=true;
				}
			}
			$row=array();
			$row[0]="----------------------------------------------------------------------";
			$row[1]="..18..17..16..15..14..13..12..11..||..21..22..23..24..25..26..27..28..";
			$row[2]="..<>..<>..<>..<>..<>..<>..<>..<>..||..<>..<>..<>..<>..<>..<>..<>..<>..";
			$row[3]="..................................||..................................";
			$row[4]="..............55..54..53..52..51..||..61..62..63..64..65..............";
			$row[5]="..............<>..<>..<>..<>..<>..||..<>..<>..<>..<>..<>..............";
			$row[6]="----------------------------------------------------------------------";
			$row[7]="..............85..84..83..82..81..||..71..72..73..74..75..............";
			$row[8]="..............<>..<>..<>..<>..<>..||..<>..<>..<>..<>..<>..............";
			$row[9]="..................................||..................................";
			$row[10]="..48..47..46..45..44..43..42..41..||..31..32..33..34..35..36..37..38..";
			$row[11]="..<>..<>..<>..<>..<>..<>..<>..<>..||..<>..<>..<>..<>..<>..<>..<>..<>..";
			$row[12]="----------------------------------------------------------------------";
			$num=13;
			$E=array();
			for ($i=0;$i<$num;$i++)
			{
				for ($j=0;$j<35;$j++)
				{
					$E[$i][$j]=substr($row[$i],0,2);
					$row[$i] = substr($row[$i],2);
					switch ($E[$i][$j])
					{
						case "..":
							echo "<td bgcolor=#ffffff>&nbsp &nbsp </td>";
						break;
						case "<>":
						$w=$i-1;
							if($D[$E[$i-1][$j]])
								if($S == 2)
									if(isset($M[$w][$j]))
										echo "<td bgcolor=#000099   align=center><input type='checkbox' name='M[".$w."][".$j."]' checked></td>";
									else
										echo "<td bgcolor=#000099   align=center><input type='checkbox' name='M[".$w."][".$j."]'></td>";
								else
									echo "<td bgcolor=#000099   align=center><input type='radio' name='R' value=".$E[$i-1][$j]." onclick='enter()'></td>";
							else
								if($S == 2)
									if(isset($M[$w][$j]))
										echo "<td bgcolor=#00FFFF   align=center><input type='checkbox' name='M[".$w."][".$j."]' checked></td>";
									else
										echo "<td bgcolor=#00FFFF   align=center><input type='checkbox' name='M[".$w."][".$j."]'></td>";
								else
									echo "<td bgcolor=#00FFFF   align=center><input type='radio' name='R' value=".$E[$i-1][$j]." onclick='enter()'></td>";
						break;
						case "||":
							echo "<td bgcolor=#ffffff><b>|</b></td>";
						break;
						case "--":
							echo "<td bgcolor=#ffffff><b>--</b></td>";
						break;
						default:
							if(is_numeric($E[$i][$j]))
								echo "<td bgcolor=#ffffff align=center><b>".$E[$i][$j]."</b></td>";
							else
							{
								echo "<td bgcolor=#00FFFF>&nbsp &nbsp </td>";
								$wsw=1;
							}
						break;
					}
				}
				echo "</tr>";
			}
			echo "<input type='HIDDEN' name= 'num' value='".$num."'>";
			echo "<input type='HIDDEN' name= 'nom' value='".$nom."'>";
			echo "<input type='HIDDEN' name= 'paciente' value='".$paciente."'>";
			if($S == 2)
				echo "<tr><td bgcolor=#dddddd colspan=36 align=center>DATOS : <input type='checkbox' name='Data' onclick='enter()'></td></tr>";
			echo "<tr><td bgcolor=#999999 colspan=36 align=center><b>&nbsp</b></td></tr>";
			echo"</table><br><br>";
			echo"<center>";
			echo "<a href='/matrix/soe/reportes/actividades.php?paciente=".$paciente."' target='_blank'>. Informe General de Actividades</a><br><br>";
			echo"</center>";
			for ($i=0;$i<$num;$i++)
				for ($j=0;$j<35;$j++)
					echo "<input type='HIDDEN' name= 'E[".$i."][".$j."]' value='".$E[$i][$j]."'>";
			$e=array();
			$e[1]="Caries ";
			$e[2]="Resina Real. ";
			$e[3]="Resina Pend. ";
			$e[4]="Amalgama Real. ";
			$e[5]="Amalgama Pend. ";
			$e[6]="Corona Real. ";
			$e[7]="Corona Pend. ";
			$e[8]="Sellante ";
			$e[9]="Endodoncia Real. ";
			$e[10]="Endodoncia Pend. ";
			$e[11]="Nucleo Real. ";
			$e[12]="Nucleo Pend. ";
			$e[13]="Provisional Real. ";
			$e[14]="Provisional Pend. ";
			$e[15]="Diente Ausente ";
			$e[16]="Exodoncia Real. ";
			$e[17]="Exodoncia Pend. ";
			$e[18]="Otra : ";
			switch ($S)
			{
				case 1:
					if(isset($R))
					{
						$R1=$R;
						echo "<input type='HIDDEN' name= 'R1' value='".$R1."'>";
						echo "<table border=0 align=center>";
						echo "<tr><td bgcolor=#999999 colspan=6 align=center><b><font size=3>PIEZA DENTAL NRo. ".$R."</font></b></td></tr>";
						echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><b><font size=2>ACTIVIDAD</font></b></td><td bgcolor=#cccccc  colspan=2 align=center><b><font size=2>SUPERFICIES</font></b></td><td bgcolor=#cccccc  colspan=1 align=center><b><font size=2>COMENTARIOS</font></b></td><td bgcolor=#cccccc  colspan=1 align=center><b><font size=2>PREEXISTENTE</font></b></td></tr>";
						for ($i=1;$i<19;$i++)
						{
							echo "<input type='HIDDEN' name= 'e[".$i."]' value='".$e[$i]."'>";
							if($i == 18)
								echo "<tr><td bgcolor=#dddddd colspan=2><input type='checkbox' name='A[".$i."]'>".$e[$i]."<input type='TEXT' name='A1' size=20 maxlength=60></td><td bgcolor=#dddddd colspan=2>";
							else
								echo "<tr><td bgcolor=#dddddd colspan=2><input type='checkbox' name='A[".$i."]'>".$e[$i]."</td><td bgcolor=#dddddd colspan=2>";
							$j=8*$i - 7;
							echo "<input type='checkbox' name='U[".$j."]'>Mesi ";
							$j=8*$i - 6;
							echo "<input type='checkbox' name='U[".$j."]'>Oclu ";
							$j=8*$i - 5;
							echo "<input type='checkbox' name='U[".$j."]'>Dist ";
							$j=8*$i - 4;
							echo "<input type='checkbox' name='U[".$j."]'>Vest ";
							$j=8*$i - 3;
							echo "<input type='checkbox' name='U[".$j."]'>Ling ";
							$j=8*$i - 2;
							echo "<input type='checkbox' name='U[".$j."]'>Cervi ";
							$j=8*$i - 1;
							echo "<input type='checkbox' name='U[".$j."]'>Todas ";
							$j=8*$i;
							echo "<input type='checkbox' name='U[".$j."]'>N/A</td><td bgcolor=#dddddd colspan=1><textarea name='C[".$i."]' cols=30 rows=2>.</textarea></td><td bgcolor=#dddddd colspan=1 align=center><input type='checkbox' name='PRE[".$i."]'></td></tr>";
						}
						echo "<tr><td bgcolor=#999999 colspan=6 align=center>DATOS OK!! <input type='checkbox' name='ok' onclick='enter()'></td></tr>";
						echo "<tr><td bgcolor=#cccccc align=center><b><font size=2>Fecha</font></b></td><td bgcolor=#cccccc align=center><b><font size=2>Hora</font></b></td><td bgcolor=#cccccc align=center><b><font size=2>Actividad</font></b></td><td bgcolor=#cccccc align=center><b><font size=2>Superficies</font></b></td><td bgcolor=#cccccc align=center><b><font size=2>Comentarios</font></b></td><td bgcolor=#cccccc align=center><b><font size=2>Odontologo</font></b></td></tr>";
						$query = "select Fecha, Hora, Actividad, Ubicacion, Comentarios, Odontologo from  soe1_000001 ";
				 		$query .= " where identificacion = '".$paciente." '";
				 		$query .= "      and Diente = ".$R;
				 		$query .= " Order by Fecha desc, Hora desc";
						$err = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err);
						if($num1 > 0)
						{
							for ($i=0;$i<$num1;$i++)
							{
								$row1 = mysql_fetch_array($err);
								echo "<tr><td bgcolor=#dddddd align=center><b><font size=2>".$row1[0]."</font></b></td><td bgcolor=#dddddd align=center><b><font size=2>".$row1[1]."</font></b></td>";
								if(strpos($row1[2],"Pend.") === false)
									echo "<td bgcolor=#dddddd><b><font size=2>".$row1[2]."</font></b></td>";
								else
									echo "<td bgcolor=#CC99FF><b><font size=2>".$row1[2]."</font></b></td>";
								echo "<td bgcolor=#dddddd><b><font size=2>".$row1[3]."</font></b></td><td bgcolor=#dddddd><b><font size=2>".$row1[4]."</font></b></td><td bgcolor=#dddddd><b><font size=2>".$row1[5]."</font></b></td></tr>";
							}
						}
						echo"</table><br><br>";
					}
				break;
				case 2:
					if(isset($Data))
					{
						echo "<table border=0 align=center>";
						for ($h=0;$h<$num;$h++)
							for ($l=0;$l<35;$l++)
								if(isset($M[$h-1][$l]))
								{
									echo "<tr><td bgcolor=#999999 colspan=6 align=center><b><font size=3>PIEZA DENTAL NRo. ".$E[$h-1][$l]."</font></b></td></tr>";
									echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><b><font size=2>ACTIVIDAD</font></b></td><td bgcolor=#cccccc  colspan=2 align=center><b><font size=2>SUPERFICIES</font></b></td><td bgcolor=#cccccc  colspan=1 align=center><b><font size=2>COMENTARIOS</font></b></td><td bgcolor=#cccccc  colspan=1 align=center><b><font size=2>PREEXISTENTE</font></b></td></tr>";
									for ($i=1;$i<19;$i++)
									{
										echo "<input type='HIDDEN' name= 'e[".$i."]' value='".$e[$i]."'>";
										if($i == 18)
											echo "<tr><td bgcolor=#dddddd colspan=2><input type='checkbox' name='A[".$i."][".$E[$h-1][$l]."]'>".$e[$i]."<input type='TEXT' name='A1[".$E[$h-1][$l]."]' size=20 maxlength=60></td><td bgcolor=#dddddd colspan=2>";
										else
											echo "<tr><td bgcolor=#dddddd colspan=2><input type='checkbox' name='A[".$i."][".$E[$h-1][$l]."]''>".$e[$i]."</td><td bgcolor=#dddddd colspan=2>";
										$j=8*$i - 7;
										echo "<input type='checkbox' name='U[".$j."][".$E[$h-1][$l]."]''>Mesi ";
										$j=8*$i - 6;
										echo "<input type='checkbox' name='U[".$j."][".$E[$h-1][$l]."]''>Oclu ";
										$j=8*$i - 5;
										echo "<input type='checkbox' name='U[".$j."][".$E[$h-1][$l]."]''>Dist ";
										$j=8*$i - 4;
										echo "<input type='checkbox' name='U[".$j."][".$E[$h-1][$l]."]''>Vest ";
										$j=8*$i - 3;
										echo "<input type='checkbox' name='U[".$j."][".$E[$h-1][$l]."]''>Ling ";
										$j=8*$i - 2;
										echo "<input type='checkbox' name='U[".$j."][".$E[$h-1][$l]."]''>Cervi ";
										$j=8*$i - 1;
										echo "<input type='checkbox' name='U[".$j."][".$E[$h-1][$l]."]''>Todas ";
										$j=8*$i;
										echo "<input type='checkbox' name='U[".$j."][".$E[$h-1][$l]."]''>N/A</td><td bgcolor=#dddddd colspan=1><textarea name='C[".$i."][".$E[$h-1][$l]."]' cols=30 rows=2>.</textarea></td><td bgcolor=#dddddd colspan=1 align=center><input type='checkbox' name='PRE[".$i."][".$E[$h-1][$l]."]'></td></tr>";
									}
									echo "<tr><td bgcolor=#cccccc align=center><b><font size=2>Fecha</font></b></td><td bgcolor=#cccccc align=center><b><font size=2>Hora</font></b></td><td bgcolor=#cccccc align=center><b><font size=2>Actividad</font></b></td><td bgcolor=#cccccc align=center><b><font size=2>Superficies</font></b></td><td bgcolor=#cccccc align=center><b><font size=2>Comentarios</font></b></td><td bgcolor=#cccccc align=center><b><font size=2>Odontologo</font></b></td></tr>";
									$query = "select Fecha, Hora, Actividad, Ubicacion, Comentarios, Odontologo from  soe1_000001 ";
							 		$query .= " where identificacion = '".$paciente." '";
							 		$query .= "      and Diente = ".$E[$h-1][$l];
							 		$query .= " Order by Fecha desc, Hora desc";
									$err = mysql_query($query,$conex);
									$num1 = mysql_num_rows($err);
									if($num1 > 0)
									{
										for ($i=0;$i<$num1;$i++)
										{
											$row1 = mysql_fetch_array($err);
											echo "<tr><td bgcolor=#dddddd align=center><b><font size=2>".$row1[0]."</font></b></td><td bgcolor=#dddddd align=center><b><font size=2>".$row1[1]."</font></b></td>";
											if(strpos($row1[2],"Pend.") === false)
												echo "<td bgcolor=#dddddd><b><font size=2>".$row1[2]."</font></b></td>";
											else
												echo "<td bgcolor=#CC99FF><b><font size=2>".$row1[2]."</font></b></td>";
											echo "<td bgcolor=#dddddd><b><font size=2>".$row1[3]."</font></b></td><td bgcolor=#dddddd><b><font size=2>".$row1[4]."</font></b></td><td bgcolor=#dddddd><b><font size=2>".$row1[5]."</font></b></td></tr>";
										}
									}
								}
								echo "<tr><td bgcolor=#999999 colspan=6 align=center>DATOS OK!! <input type='checkbox' name='ok' onclick='enter()'></td></tr>";
								echo"</table><br><br>";
					}
				break;
			}
			echo "<table border=0 align=center>";
			echo "<tr><td bgcolor=#999999 colspan=6 align=center><b><font size=3>ACTIVIDADES GENERALES</font></b></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><b><font size=2>Actividad</font></b></td><td bgcolor=#cccccc  colspan=4 align=center><b><font size=2>Comentarios</font></b></td></tr>";
			echo "<tr><td bgcolor=#dddddd colspan=2><input type='TEXT' name='actividad' size=30 maxlength=60></td><td bgcolor=#dddddd colspan=4><textarea name='W' cols=60 rows=5>.</textarea></td></tr>";
			echo "<tr><td bgcolor=#999999 colspan=6 align=center>DATOS OK!! <input type='checkbox' name='ok1' onclick='enter()'></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center><b><font size=2>Fecha</font></b></td><td bgcolor=#cccccc align=center><b><font size=2>Hora</font></b></td><td bgcolor=#cccccc align=center><b><font size=2>Actividad</font></b></td><td bgcolor=#cccccc align=center><b><font size=2>Superficies</font></b></td><td bgcolor=#cccccc align=center><b><font size=2>Comentarios</font></b></td><td bgcolor=#cccccc align=center><b><font size=2>Odontologo</font></b></td></tr>";
			$query = "select Fecha, Hora, Actividad, Ubicacion, Comentarios, Odontologo from  soe1_000001 ";
	 		$query .= " where identificacion = '".$paciente." '";
	 		$query .= "      and Diente = 99";
	 		$query .= " Order by Fecha desc, Hora desc";
			$err = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err);
			if($num1 > 0)
			{
				for ($i=0;$i<$num1;$i++)
				{
					$row1 = mysql_fetch_array($err);
					echo "<tr><td bgcolor=#dddddd align=center><b><font size=2>".$row1[0]."</font></b></td><td bgcolor=#dddddd align=center><b><font size=2>".$row1[1]."</font></b></td><td bgcolor=#dddddd><b><font size=2>".$row1[2]."</font></b></td><td bgcolor=#dddddd><b><font size=2>".$row1[3]."</font></b></td><td bgcolor=#dddddd><b><font size=2>".$row1[4]."</font></b></td><td bgcolor=#dddddd><b><font size=2>".$row1[5]."</font></b></td></tr>";
				}
			}
			echo"</table>";
		}
	}
}
?>
</body>
</html>