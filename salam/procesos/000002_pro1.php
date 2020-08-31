<html>

<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
	/*****************************************************
	 * APLICACION PRINCIPAL PARA EL REGISTRO ANESTESICO  *
	 *          	  AUTOMATICO   CONTINUO				 *
	 *				     CONEX, FREE => OK				 *
	 *****************************************************/
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000002_pro1.php' method=post>";
		if(!isset($wmedico)  or !isset($wfechac) or !isset($wpacc) or  !isset($whic))
		{
			if(!isset($wmedico))
				$wmedico="";
			if(!isset($hi))
				$hi="";
			if(!isset($mi))
				$mi="";	
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE ANESTESIA</td></tr>";
			echo "<tr><td align=center colspan=2>CONTROL DE DATOS HEMODINAMICOS DEL PACIENTE</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Medico</td>";
			echo "<td bgcolor=#cccccc align=center><select name='wmedico'>";
			$query = "select * from det_selecciones where medico='".$key."' and codigo='002' and activo='A'";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($j=0;$j<$num;$j++)
			{	
				$row = mysql_fetch_array($err);
				$med=$row[2]."-".$row[3];
				if($med==$wmedico)
					echo "<option selected>".$row[2]."-".$row[3]."</option>";
				else
					echo "<option>".$row[2]."-".$row[3]."</option>";
			}	
			echo "</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Fecha de la Cirugia</td>";
			echo "<td bgcolor=#cccccc align=center>";
			if(!isset($year))
			{
				$year=date("Y");
				$month=date("m");
				$day=date("d");
			}
			echo "<select name='year'>";
				for($f=2004;$f<2051;$f++)
				{
					if($f == $year)
						echo "<option selected>".$f."</option>";
					else
						echo "<option>".$f."</option>";
				}
				echo "</select><select name='month'>";
				for($f=1;$f<13;$f++)
				{
					if($f == $month)
						if($f < 10)
							echo "<option selected>0".$f."</option>";
						else
							echo "<option selected>".$f."</option>";
					else
						if($f < 10)
							echo "<option>0".$f."</option>";
						else
							echo "<option>".$f."</option>";
				}
				echo "</select><select name='day'>";
				for($f=1;$f<32;$f++)
				{
				if($f == $day)
					if($f < 10)
						echo "<option selected>0".$f."</option>";
					else
						echo "<option selected>".$f."</option>";
				else
					if($f < 10)
						echo "<option>0".$f."</option>";
					else
							echo "<option>".$f."</option>";
				}
				echo "</select></td></tr>";
				echo"<tr><td bgcolor=#cccccc align=center><font color=#000066>Hora inicio: </td>";	
				echo "<td bgcolor=#cccccc align=center><select name='hi'>";
				for($p=0;$p<24;$p++)
				{
					if($p == $hi)
						if($p < 10)
							echo "<option selected>0".$p."</option>";
						else
							echo "<option selected>".$p."</option>";
					else
						if($p < 10)
							echo "<option>0".$p."</option>";
						else
							echo "<option>".$p."</option>";
				}
				echo "</select><select name='mi'>";
				for($p=0;$p<60;$p++)
				{
					if($p == $mi)
						if($p < 10)
							echo "<option selected>0".$p."</option>";
						else
							echo "<option selected>".$p."</option>";
					else
						if($p < 10)
							echo "<option>0".$p."</option>";
						else
							echo "<option>".$p."</option>";
				}
			echo "</select></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Paciente</td><td bgcolor=#cccccc align=center>";
			$query="select Paciente  from salam_000004 where Hora_inicio='".$hi.":".$mi.":00'and Fecha='".$year."-".$month."-".$day."' order by Paciente";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<select name='wpacc'>";
			for ($j=0;$j<$num;$j++)
			{	
				$row = mysql_fetch_array($err);
								echo "<option>".$row[0]."</option>";
			}
			echo "</td></tr>";
			echo "<input type='hidden' name='wfechac' value='".$year."-".$month."-".$day."'>";
			echo "<input type='hidden' name='whic' value='".$hi.":".$mi.":00'>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr>";
			echo "</table>";
		}
		else
		{
			$ruta="/matrix/images/medical/salam/";
			$ini1=strpos($wpacc,"-");
			echo "<meta http-equiv='refresh' content='20;url=000002_pro1.php?wmedico=".$wmedico."&amp;wfechac=".$wfechac."&amp;wpacc=".$wpacc."&amp;whic=".$whic."&amp;ini1=".$ini1."'>";
			$query = "select * from salam_000004 where  fecha='".$wfechac."' and paciente='".$wpacc."' and hora_inicio='".$whic."'";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				#****************************************************************************
				#Impresion de titulos y parametros de medicion                                                                                        *
				#****************************************************************************
				echo "<input type='HIDDEN' name= 'wmedico' value='".$wmedico."'>";
				echo "<input type='HIDDEN' name= 'wfechac' value='".$wfechac."'>";
				echo "<input type='HIDDEN' name= 'wpacc' value='".$wpacc."'>";
				echo "<input type='HIDDEN' name= 'whic' value='".$whic."'>";
				if(!isset($NC))
					$NC="";
				echo "<input type='HIDDEN' name= 'NC' value='".$NC."'>";

				$row = mysql_fetch_array($err);
				echo "<center><table border=0>";
				echo "<tr><td align=center rowspan=7><img SRC=".$ruta."/cirugia.jpg size=60% width=60%></td>";
				echo "<tr><td colspan=2  align=center><font  size=3 face='verdana'><b>REGISTRO ANESTESICO AUTOMATICO CONTINUO</b></font></td></tr>";
				echo "<tr><td  bgcolor=#cccccc><font  size=2 face='arial'>CIRUGIA : </td><td bgcolor=#cccccc><font  size=2 face='arial'>".$row[5]."</font></td></tr>";
				echo "<tr><td bgcolor=#cccccc><font  size=2 face='arial'>CIRUJANO : </td><td bgcolor=#cccccc><font  size=2 face='arial'>".$row[6]."</font></td></tr>";
				echo "<tr><td bgcolor=#cccccc><font  size=2 face='arial'>ANESTESIOLOGO : </td ><td bgcolor=#cccccc><font  size=2 face='arial'>".substr($wmedico,3,strlen($wmedico))."</font></td></tr>";
				echo "<tr><td bgcolor=#cccccc><font  size=2 face='arial'>PACIENTE : </td><td bgcolor=#cccccc><font  size=2 face='arial'>".strtoupper($wpacc)."</font></td></tr>";
				echo "<tr><td bgcolor=#cccccc><font  size=2 face='arial'>FECHA : </td><td bgcolor=#cccccc><font  size=2 face='arial'>".$wfechac."</font></td></tr></table><br><br>";
				
				
				
				
				#****************************************************************************
				#Se llena la primera fila de la matriz con los valores dinamicos de las horas y el titulo M vs Hora*
				#El segundo plano se utiliza para almacenar el codigo de color de la celda                                          *
				#****************************************************************************
				$V=array();
				$V[0][0][0]="M vs Hora";
				$V[0][0][1]="#999999";
				$V[0][0][2]="NO";
				$V[0][0][3]="";
				$query = "select hora,count(*) from salam_000002 where anestesiologo='".$wmedico."' and fecha='".$wfechac."' and paciente='".substr($wpacc,0,$ini1)."' and hora_inicio='".$whic."' group by hora order by hora";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				for ($j=0;$j<$num;$j++)
				{	
					$row = mysql_fetch_array($err);
					if($j == 0)
						$HI=$row[0];
					$V[0][$j+1][0]=$row[0];
					$V[0][$j+1][1]="#cccccc";
					$V[0][$j+1][2]="NO";
					$V[0][$j+1][3]="";
				}
				$NC=$num;
				#****************************************************************************
				#Se construye en la pantalla los iconos del toolbox                                                                                    *
				#****************************************************************************
				echo "<center><table border=0>";
				echo "<tr><td align=center><A HREF='acarga.php?medico=".$wmedico."&amp;fechad=".$wfechac."&amp;paciente=".substr($wpacc,0,$ini1)."&amp;horai=".$whic."' target = '_blank'><IMG SRC='".$ruta."TOOLS.gif' alt='ADQUISICION DE DATOS'></a></td><td align=center><A HREF='preanes.php?fechacx=".$wfechac."&amp;cedula=".substr($wpacc,0,$ini1)."&amp;hi=".$whic."' target = '_blank'><IMG SRC='".$ruta."PRE.gif' alt='DATOS PREANESTESIA'></a></td>";
				echo "<td><A HREF='postanes.php?med=".$wmedico."&amp;fecha=".$wfechac."&amp;paciente=".$wpacc."&amp;hi=".$whic."' target = '_blank'><IMG SRC='".$ruta."MEDIC00F.gif' alt='ENVIO DATOS POSTANESTESIA'></a></td>";
				echo "<td><A HREF='graficos.php?med=".$wmedico."&amp;horas=".$NC."&amp;fecha=".$wfechac."&amp;paciente=".$wpacc."&amp;hi=".$whic."' target = '_blank'><IMG SRC='".$ruta."LINEAS.GIF' alt='LINEAS DE TENDENCIA'></a></td></tr>";
				echo "</table></td>";
				#****************************************************************************
				#Se llena la primera columna con los rangos de valores   (1-20)                                                            *
				#****************************************************************************
				for ($j=0;$j<20;$j++)
				{	
					$V[$j+1][0][0]=(string)(200-($j*10)-9)."-".(string)(200-($j*10));
					$V[$j+1][0][1]="#cccccc";
					$V[$j+1][0][2]="NO";
					$V[$j+1][0][3]="";
				}
				#****************************************************************************
				#Se llena la  columna 21 con el rango 0                                                                                                         *
				#****************************************************************************
				$V[21][0][0]=(string)(0);
				$V[21][0][1]="#cccccc";	
				$V[21][0][2]="NO";	
				$V[21][0][3]="";
				$NF=21;
				#****************************************************************************
				#Inicializacion de la paleta de colores                                                                                                            *
				#****************************************************************************
				for ($i=1;$i<=$NF;$i++)
				{	
					for ($j=1;$j<=$NC;$j++)
					{	
						$V[$i][$j][0]="";
						$V[$i][$j][2]="NO";
						$V[$i][$j][3]="";
						if($i ==1 or $i ==2 or $i ==20 or $i ==21)
							$V[$i][$j][1]="#6699cc";
						elseif($i ==3 or $i ==4 or $i ==18 or $i ==19)
							$V[$i][$j][1]="#9999ff";
						elseif($i ==5 or $i ==6 or $i ==16 or $i ==17)
							$V[$i][$j][1]="#66ccff";
						elseif($i ==7 or $i ==8 or $i ==14 or $i ==15)
							$V[$i][$j][1]="#33ffff";
						elseif($i ==9 or $i ==10 or $i ==12 or $i ==13)
							$V[$i][$j][1]="#ccffff";
						else $V[$i][$j][1]="#ffffff";
					}
				}
				#****************************************************************************
				#Lectura de todos los datos asociados a la cirugia                                                                                     *
				#****************************************************************************
				$query = "select hora,parametro,codigo,valor,descripcion,tipo,label,codigo_a from salam_000002,salam_000001 where anestesiologo='".$wmedico."' and fecha='".$wfechac."' and paciente='".substr($wpacc,0,$ini1)."' and hora_inicio='".$whic."' and parametro=codigo_n   order by tipo,parametro,codigo,hora";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				$hora="";
				$kh=0;
				if ($num>0)
				{
					for ($j=0;$j<$num;$j++)
					{
						$row = mysql_fetch_array($err);
						#**************Seleccion de la hora del dato************
						for ($i=1;$i<=$NC;$i++)
						{
							if($row[0] == $V[0][$i][0])
								$nc=$i;
						}
						switch ($row[5])
						{
							CASE "01-GRAFICABLE":
								if($row[3] > 200)
									$row[3]=200;
								if($row[3] < 0 )
									$row[3] = 0;
								$nf=(integer)((200 - $row[3])/10) + 1;
								if($V[$nf][$nc][0] == "")
									$V[$nf][$nc][0]=$V[$nf][$nc][0].$row[6];
								else
									$V[$nf][$nc][0]=$V[$nf][$nc][0]."-".$row[6];
								if($V[$nf][$nc][3] == "")
									$V[$nf][$nc][3]=$V[$nf][$nc][3].$row[7]." : ".$row[3];
								else
									$V[$nf][$nc][3]=$V[$nf][$nc][3]."-".$row[7]." : ".$row[3];
								break;
							CASE "02-DIGITACION MULTIPLE":
								if($row[1] != "99")
								{
									$nf=-1;
									for ($i=1;$i<=$NF;$i++)
									{
										if($row[7] == $V[$i][0][0])
											$nf=$i;
									}
									if ($nf == -1)
									{
										$NF++;
										$nf=$NF;
										$V[$nf][0][0]=$row[7];
										$V[$nf][0][1]="#cccccc";
										$V[$nf][0][2]="NO";
										for ($i=1;$i<=$NC;$i++)
										{
											$V[$nf][$i][0]="";
											$V[$nf][$i][1]="#999999";
											$V[$nf][$i][2]="NO";
										}
									}
									$V[$nf][$nc][0]=$row[4];
									$V[$nf][$nc][1]="#999999";
								}
								else
								{
									$nf=-1;
									if(substr($row[2],0,1) == "0")
										$color="#99ccff";
									else
										$color="#0099ff";
									$ini=strpos($row[2],"-");
									$codigo=substr($row[2],$ini+1,strlen($row[2]));
									for ($i=1;$i<=$NF;$i++)
									{
										if($codigo == $V[$i][0][0])
											$nf=$i;
									}
									if ($nf == -1)
									{
										$NF++;
										$nf=$NF;
										$V[$nf][0][0]=$codigo;
										$V[$nf][0][1]="#cccccc";
										$V[$nf][0][2]="NO";
										for ($i=1;$i<=$NC;$i++)
										{
											$V[$nf][$i][0]="";
											$V[$nf][$i][1]=$color;
											$V[$nf][$i][2]="NO";
										}
									}
									$V[$nf][$nc][0]=$row[4];
									$V[$nf][$nc][1]=$color;
								}
								$V[$nf][$nc][0]=$row[3];
								break;
							CASE "03-DIGITACION UNICA":
								$nf=-1;
								for ($i=1;$i<=$NF;$i++)
								{
									if($row[7] == $V[$i][0][0])
										$nf=$i;
								}
								if ($nf == -1)
								{
									$NF++;
									$nf=$NF;
									$V[$nf][0][0]=$row[7];
									$V[$nf][0][1]="#cccccc";
									$V[$nf][0][2]="NO";
									for ($i=1;$i<=$NC;$i++)
									{
										$V[$nf][$i][0]="";
										$V[$nf][$i][1]="#00CCCC";
										$V[$nf][$i][2]="NO";
									}
								}
								if($V[$nf][$nc][0] == "")
									$V[$nf][$nc][0]=$V[$nf][$nc][0].$row[6];
								else
									$V[$nf][$nc][0]=$V[$nf][$nc][0]."-".$row[6];
								break;
							}
						#***************Grabacion del tipo de dato en el tercer plano*******************
						$V[$nf][$nc][2]=$row[5];
		            }
	            }
	            $query = "select codigo_a,descripcion,label from salam_000001 where tipo='01-GRAFICABLE' order by codigo_n";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				#****************************************************************************
				#Se construye en la pantalla la tabla de las convenciones                                                                                   *
				#****************************************************************************

				echo"<table border=0><tr valign=top><td width=15%><table border=0 cellpadding=2>";
				echo "<tr><td align=center colspan=2 bgcolor=#999999><font  face='arial' size='2'>CONVENCIONES</font></td></tr>";
				for ($j=0;$j<$num;$j++)
				{	
					$row = mysql_fetch_array($err);
					echo "<tr><td bgcolor=#cccccc><font  face='arial' size='2'>".$row[0]."</font></td><td align='center'bgcolor=#cccccc><IMG SRC=".$ruta.$row[2]."></td>";
				}
				echo "</table>"; 
	            #****************************************************************************
				#Generacion de la matriz con datos en pantalla                                                                                         *
				#****************************************************************************
				
	            echo "<td><center><table border=0>";
	            for ($i=0;$i<=$NF;$i++)
				{	
					if($i == 0)
						echo "<tr>";
					else
						echo "</tr><tr>";
					for ($j=0;$j<=$NC;$j++)
					{	
						switch ($V[$i][$j][2])
						{
							CASE "01-GRAFICABLE":
								echo "<td align=center bgcolor=".$V[$i][$j][1].">";
								$trama = $V[$i][$j][0];
								$ini = strpos($trama,"-");
								$trama1 = $V[$i][$j][3];
								$ini1 = strpos($trama1,"-");
								while (strlen($trama) > 0)
								{
									if($ini > 0)
									{
										$image=substr($trama,0,$ini);
										$trama=substr($trama,$ini+1,strlen($trama));
										$ini = strpos($trama,"-");
										$label=substr($trama1,0,$ini1);
										$trama1=substr($trama1,$ini1+1,strlen($trama1));
										$ini1 = strpos($trama1,"-");
									}
									else
									{
										$image=$trama;
										$trama="";
										$label=$trama1;
										$trama1="";
									}
									echo "<img src=".$ruta.$image." alt='".$label."'>";
								}
								echo "</td>";
								break;
							CASE "02-DIGITACION MULTIPLE":
								echo "<td align=center bgcolor=".$V[$i][$j][1]."><font fase='arial' size=2><b>".$V[$i][$j][0]."</b></font></td>";
								break;
							CASE "03-DIGITACION UNICA":
								echo "<td align=center bgcolor=".$V[$i][$j][1].">";
								$trama = $V[$i][$j][0];
								$ini = strpos($trama,"-");
								while (strlen($trama) > 0)
								{
									if($ini > 0)
									{
										$image=substr($trama,0,$ini);
										$trama=substr($trama,$ini+1,strlen($trama));
										$ini = strpos($trama,"-");
									}
									else
									{
										$image=$trama;
										$trama="";
									}
									echo "<img src=".$ruta.$image.">";
								}
								echo "</td>";
								break;
							CASE "NO":
								if($i == 0 and $j > 0)
									echo "<td align=center bgcolor=".$V[$i][$j][1]."><font  face='arial' size='2'><A HREF='hora.php?medico=".$wmedico."&amp;fechad=".$wfechac."&amp;paciente=".substr($wpacc,0,$ini1)."&amp;horai=".$whic."&amp;hora=".$V[$i][$j][0]."' target = '_blank'>".$V[$i][$j][0]."</font></td>";
								else
									echo "<td align=center bgcolor=".$V[$i][$j][1]." nowrap><font  face='arial' size='2'>".$V[$i][$j][0]."</font></td>";
								break;
						}	
					}
				}
				echo "</table></center></td></tr></table>";	
			}
			else
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>ESTA CIRUGIA NO ESTA PROGRAMADA -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
		}
		include_once("free.php");
}
?>
</body>
</html>
