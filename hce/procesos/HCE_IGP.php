<html>
<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>
<head>
	<title>HCE INFORMACION GENERAL DEL PACIENTE</title>
	<link type='text/css' href='HCE_IGP.css' rel='stylesheet'>
	<script>
		function toggleDisplay(id)
		{
			if (id.style.display=="none")
			{
				id.style.display="";
			}
			else 
			{
				id.style.display="none";
			}
		}
	</script>
<?php 
$consultaAjax = '';
include_once("conex.php");
include("root/comun.php");

/**********************************************************************************************************************  
[DOC]
	   PROGRAMA : HCE_IGP.php
	   Fecha de Liberacion : 2013-06-17
	   Autor : Pedro Ortiz Tamayo
	   Version Inicial : 2013-06-17
	   Version actual  : 2017-05-17
	   
	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface grafica que permite visualizar la informacion
	   del estado general del los pacientes en relacion con la informacion almacenada en las tablas de Historia Clinica
	   y Movimiento Hospitalario.
	   
	   REGISTRO DE MODIFICACIONES :
	   .2017-05-17
	   		Se cambia el formulario 69 de evolucion por el formulario 367 de nueva evolución.
	
	   .2013-08-05
	   		Se incluyen en los items de medidas del paciente y signos vitales, formularios correspondientes a pacientes
	   		pediatricos y neonatales.
	   		
	   .2013-06-17
	   		Release de Version Beta.
[*DOC]   		
***********************************************************************************************************************/
function lcolor($i)
{
	$gridcolor1="tipoL02GRID1";
	$gridcolor2="tipoL02GRID2";
	if($i % 2 == 0)
		$color = $gridcolor1;
	else
		$color = $gridcolor2;
	return $color;
}
function diagnosticos($text)
{
	$text=str_replace("</OPTION>"," ",$text);
	$text=str_replace("</option>"," ",$text);
	$imp=0;
	$def="";
	$ndiag=0;
	$wclase=0;
	for ($z=0;$z<strlen($text);$z++)
	{
		if(substr($text,$z,1) == "<")
			$imp=1;
		if(substr($text,$z,1) == ">")
		{
			$imp=0;
			$z++;
			$ndiag++;
			if($ndiag > 1)
				$def .= "|(".$ndiag.") ";
			else
				$def .= "(".$ndiag.") ";
		}
		if($imp == 0)
		{
			$def .= substr($text,$z,1);
		}
	}
	$def=str_replace("M-","",$def);
	$def=str_replace("O-","",$def);
	$def=str_replace(".","",$def);
	$text=$def;
	return $text;
}
function comparacion($vec1,$vec2)
{
	if($vec1[0] > $vec2[0])
		return 1;
	elseif ($vec1[0] < $vec2[0])
				return -1;
			else
				return 0;
}
function ver($chain)
{
	if(strpos($chain,"-") === false)
		return $chain;
	else
		return substr($chain,strpos($chain,"-")+1);
}

function diferencia($fechaf,$fechai)
{
	$a1=(integer)substr($fechaf,0,4)*360 +(integer)substr($fechaf,5,2)*30 + (integer)substr($fechaf,8,2);
	$a2=(integer)substr($fechai,0,4)*360 +(integer)substr($fechai,5,2)*30 + (integer)substr($fechai,8,2);
	$diff=$a1 - $a2;
	return $diff;
}

if(!isset($accion))
{
	echo "<frameset rows='10%,15%,20%,20%,*'>";
	echo "  <frame src='HCE_IGP.php?wemp_pmla=".$wemp_pmla."&accion=T&ok=0&empresa=".$empresa."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."' frameborder=0 framespacing=0 scrolling=no>";
	echo "  <frameset cols='33%,33%,*'>";
		echo "<frame src='HCE_IGP.php?wemp_pmla=".$wemp_pmla."&accion=D&ok=0&empresa=".$empresa."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."' scrolling=yes frameborder=0 framespacing=0 location=no>";
		echo "<frame src='HCE_IGP.php?wemp_pmla=".$wemp_pmla."&accion=M&ok=0&empresa=".$empresa."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."' scrolling=yes frameborder=0 framespacing=0 location=no>";
		echo "<frame src='HCE_IGP.php?wemp_pmla=".$wemp_pmla."&accion=F&ok=0&empresa=".$empresa."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."' scrolling=yes frameborder=0 framespacing=0 location=no>";
	echo "  </frameset>";
	echo "  <frameset cols='50%,*'>";
		echo "<frame src='HCE_IGP.php?wemp_pmla=".$wemp_pmla."&accion=G&ok=0&empresa=".$empresa."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."' scrolling=yes frameborder=0 framespacing=0 location=no>";
		echo "<frame src='HCE_IGP.php?wemp_pmla=".$wemp_pmla."&accion=A&ok=0&empresa=".$empresa."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."' scrolling=yes frameborder=0 framespacing=0 location=no>";
	echo "  </frameset>";
	echo "  <frameset cols='50%,*'>";
		echo "<frame src='HCE_IGP.php?wemp_pmla=".$wemp_pmla."&accion=C&ok=0&empresa=".$empresa."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."' scrolling=yes frameborder=0 framespacing=0 location=no>";
		echo "<frame src='HCE_IGP.php?wemp_pmla=".$wemp_pmla."&accion=P&ok=0&empresa=".$empresa."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."' scrolling=yes frameborder=0 framespacing=0 location=no>";
	echo "  </frameset>";
	echo "  <frameset cols='50%,*'>";
		echo "<frame src='HCE_IGP.php?wemp_pmla=".$wemp_pmla."&accion=E&ok=0&empresa=".$empresa."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."' scrolling=yes frameborder=0 framespacing=0 location=no>";
		echo "<frame src='HCE_IGP.php?wemp_pmla=".$wemp_pmla."&accion=I&ok=0&empresa=".$empresa."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."' scrolling=yes frameborder=0 framespacing=0 location=no>";
	echo "  </frameset>";
	echo "</frameset>";
}
else
{
	@session_start();
	if(!isset($_SESSION['user']))
		echo "ERROR SESION CERRADA";
	else
	{
		$key = substr($user,2,strlen($user));
		global $wbasedatomovhos;
		$user_session = explode('-', $_SESSION['user']);
        $wuse = $user_session[1];
        mysql_select_db("matrix");
		$wbasedatocenpro = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cenmez');
		$wbasedatomovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
		$conex = obtenerConexionBD("matrix");

		
 	
		$key = substr($user,2,strlen($user));
		//echo "<script type='text/javascript' src='HCE_Seguridad.js' ></script>";
		echo "<form name='HCE_IGP' action='HCE_IGP.php?wemp_pmla=".$wemp_pmla."' method=post>";
		//                 0      1      2      3      4      5      6      7      8      9      10     11                     12
		$query = "select Pacno1,Pacno2,Pacap1,Pacap2,Pacnac,Pacsex,Orihis,Oriing,Ingnre,Ubisac,Ubihac,Cconom,".$wbasedatomovhos."_000016.fecha_data from root_000036,root_000037,".$wbasedatomovhos."_000016,".$wbasedatomovhos."_000018,".$wbasedatomovhos."_000011 ";
		$query .= " where pacced = '".$wcedula."'";
		$query .= "   and pactid = '".$wtipodoc."'";
		$query .= "   and  pacced = oriced ";
		$query .= "   and  pactid = oritid ";
		$query .= "   and oriori = '".$wemp_pmla."' ";
		$query .= "   and inghis = orihis ";
		$query .= "   and  inging = oriing ";
		$query .= "   and ubihis = inghis "; 
		$query .= "   and ubiing = inging ";
		$query .= "   and ccocod = ubisac ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$row = mysql_fetch_array($err);
		$sexo="MASCULINO";
		if($row[5] == "F")
			$sexo="FEMENINO";
		$ann=(integer)substr($row[4],0,4)*360 +(integer)substr($row[4],5,2)*30 + (integer)substr($row[4],8,2);
		$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
		$ann1=($aa - $ann)/360;
		$meses=(($aa - $ann) % 360)/30;
		if ($ann1<1)
		{
			$dias1=(($aa - $ann) % 360) % 30;
			$wedad=(string)(integer)$meses." Meses ".(string)$dias1." Dias";	
		}
		else
		{
			$dias1=(($aa - $ann) % 360) % 30;
			$wedad=(string)(integer)$ann1." A&ntilde;os ".(string)(integer)$meses." Meses ".(string)$dias1." Dias";
		}
		$wpac = strtoupper($row[2])." ".strtoupper($row[3])." ".ucfirst(strtolower($row[0]))." ".ucfirst(strtolower($row[1]));
		$widen = $wtipodoc." ".$wcedula;
		$whis = $row[6];
		if(!isset($wing))
			$wing=$row[7];
		$wfei = $row[12];
		$whab = $row[10];
		$wser = $row[11];
		$weps = $row[8];
		switch($accion)
		{
			case "T":
				echo "<body style='background-color:#E8EEF7' FACE='ARIAL' LINK='BLACK'>";				
				echo "<table border=0 CELLSPACING=0>";
				echo "<tr><td align=center id=tipoT01><IMG SRC='/matrix/images/medical/root/lmatrix.jpg'></td>";
				echo "<td id=tipoT02>&nbsp;CLINICA LAS AMERICAS<BR>&nbsp;HCE - INFORMACION GENERAL DEL PACIENTE&nbsp;&nbsp;<A HREF='/matrix/root/Reportes/DOC.php?files=../../hce/Procesos/HCE_IGP.php' target='_blank'>Version 2022-04-22</A></td><td id=tipoT02D><IMG SRC='/matrix/images/medical/HCE/button.gif' onclick='javascript:top.close();'></IMG></td></tr>";
				echo "<tr><td id=tipoT03 colspan=3></td></tr>";
				echo "</table>";
				echo"</form>";
			break;
			case "D":
				$gridcolor1="tipoL02GRID1";
				$gridcolor2="tipoL02GRID2";
				echo "<body style='background-color:#E8EEF7' FACE='ARIAL' LINK='BLACK'>";				
				echo "<table align=center border=1 class=tipoTABLEGRID CELLPADDING=3>";
				echo "<tr><td id=tipoL06GRID>INFORMACION DEMOGRAFICA</td></tr>";
				echo "<tr><td class=".$gridcolor1.">".$wpac."</td></tr>";
				echo "<tr><td class=".$gridcolor2.">".$widen."</td></tr>";
				echo "<tr><td class=".$gridcolor1.">HIS-ING:".$whis."-".$wing."</td></tr>";
				echo "<tr><td class=".$gridcolor2.">".$wedad."</td></tr>";
				echo "<tr><td class=".$gridcolor1.">".$sexo."</td></tr>";
				echo "</table>";
				echo"</form>";
			break;
			case "M":
				$gridcolor1="tipoL02GRID1";
				$gridcolor2="tipoL02GRID2";
				echo "<body style='background-color:#E8EEF7' FACE='ARIAL' LINK='BLACK'>";				
				echo "<table align=center border=1 class=tipoTABLEGRID CELLPADDING=3>";
				echo "<tr><td id=tipoL06GRID colspan=3>MEDIDAS DEL PACIENTE</td></tr>";
				//                    0          1    
				$query = "select Fecha_data, Hora_data ";
				$query .= "  from ".$empresa."_000036  "; 
				$query .= "   where Firpro = '000085' ";
				$query .= " 	and Firhis = '".$whis."' ";
				$query .= " 	and Firing = '".$wing."' ";
				$query .= " UNION  ";
				$query .= "select Fecha_data, Hora_data ";
				$query .= "  from ".$empresa."_000036  "; 
				$query .= "   where Firpro = '000065' ";
				$query .= " 	and Firhis = '".$whis."' ";
				$query .= " 	and Firing = '".$wing."' ";
				$query .= " UNION  ";
				$query .= "select Fecha_data, Hora_data ";
				$query .= "  from ".$empresa."_000036  "; 
				$query .= "   where Firpro = '000158' ";
				$query .= " 	and Firhis = '".$whis."' ";
				$query .= " 	and Firing = '".$wing."' ";
				$query .= "   order by 1 desc,2 desc ";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num = mysql_num_rows($err);
				if($num > 0)
				{
					$row = mysql_fetch_array($err);
					$query = "select '1',movcon,movdat,Detume ";
					$query .= "  from ".$empresa."_000085,".$empresa."_000002 "; 
					$query .= "   where ".$empresa."_000085.Fecha_data = '".$row[0]."' ";
					$query .= "     and ".$empresa."_000085.Hora_data = '".$row[1]."' ";
					$query .= " 	and movhis = '".$whis."' ";
					$query .= " 	and moving = '".$wing."' ";
					$query .= " 	and movcon in (97,98,99) ";
					$query .= " 	and movpro = Detpro ";
					$query .= " 	and movcon = Detcon ";
					$query .= " UNION  ";
					$query .= "select '2',movcon,movdat,Detume ";
					$query .= "  from ".$empresa."_000158,".$empresa."_000002 "; 
					$query .= "   where ".$empresa."_000158.Fecha_data = '".$row[0]."' ";
					$query .= "     and ".$empresa."_000158.Hora_data = '".$row[1]."' ";
					$query .= " 	and movhis = '".$whis."' ";
					$query .= " 	and moving = '".$wing."' ";
					$query .= " 	and movcon in (127,130,131) ";
					$query .= " 	and movpro = Detpro ";
					$query .= " 	and movcon = Detcon ";
					$query .= " UNION  ";
					$query .= "select '3',movcon,movdat,Detume ";
					$query .= "  from ".$empresa."_000065,".$empresa."_000002 "; 
					$query .= "   where ".$empresa."_000065.Fecha_data = '".$row[0]."' ";
					$query .= "     and ".$empresa."_000065.Hora_data = '".$row[1]."' ";
					$query .= " 	and movhis = '".$whis."' ";
					$query .= " 	and moving = '".$wing."' ";
					$query .= " 	and movcon in (82,83) ";
					$query .= " 	and movpro = Detpro ";
					$query .= " 	and movcon = Detcon ";
					$query .= "   order by 1  ";
					$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$num1 = mysql_num_rows($err1);
					if($num1 > 0)
					{
						$wkey="";	
						for ($i=0;$i<$num1;$i++)
						{
							$row1 = mysql_fetch_array($err1);
							if($i % 2 == 0)
								$color = $gridcolor1;
							else
								$color = $gridcolor2;
							switch($row1[0])
							{
								case '1':
									switch($row1[1])
									{
										case 97:
											echo "<tr><td class=".$color.">PESO</td><td class=".$color.">".$row1[2]."</td><td class=".$color.">".$row1[3]."</td></tr>";
										break;
										case 98:
											echo "<tr><td class=".$color.">TALLA</td><td class=".$color.">".$row1[2]."</td><td class=".$color.">".$row1[3]."</td></tr>";
										break;
										case 99:
											echo "<tr><td class=".$color.">IMC</td><td class=".$color.">".number_format((double)$row1[2],2,'.',',')."</td><td class=".$color.">".$row1[3]."</td></tr>";
										break;
									}
								break;
								case '2':
									switch($row1[1])
									{
										case 127:
											echo "<tr><td class=".$color.">PESO</td><td class=".$color.">".$row1[2]."</td><td class=".$color.">".$row1[3]."</td></tr>";
										break;
										case 130:
											echo "<tr><td class=".$color.">TALLA</td><td class=".$color.">".$row1[2]."</td><td class=".$color.">".$row1[3]."</td></tr>";
										break;
										case 131:
											echo "<tr><td class=".$color.">SUPERFICIE CORPORAL</td><td class=".$color.">".number_format((double)$row1[2],2,'.',',')."</td><td class=".$color.">".$row1[3]."</td></tr>";
										break;
									}
								break;
								case '3':
									switch($row1[1])
									{
										case 82:
											echo "<tr><td class=".$color.">PESO</td><td class=".$color.">".$row1[2]."</td><td class=".$color.">".$row1[3]."</td></tr>";
										break;
										case 83:
											echo "<tr><td class=".$color.">TALLA</td><td class=".$color.">".$row1[2]."</td><td class=".$color.">".$row1[3]."</td></tr>";
										break;
									}
								break;
							}
						}
					}
				}
				echo "</table>";
				echo"</form>";
			break;
			case "F":
				$gridcolor1="tipoL02GRID1";
				$gridcolor2="tipoL02GRID2";
				echo "<body style='background-color:#E8EEF7' FACE='ARIAL' LINK='BLACK'>";				
				echo "<table align=center border=1 class=tipoTABLEGRID CELLPADDING=3>";
				echo "<tr><td id=tipoL06GRID colspan=2>INFORMACION DE INGRESO</td></tr>";
				echo "<tr><td class=".$gridcolor1.">FECHA DEL INGRESO</td><td class=".$gridcolor1.">".$wfei."</td></tr>";
				echo "<tr><td class=".$gridcolor2.">HABITACION</td><td class=".$gridcolor2.">".$whab."</td></tr>";
				echo "<tr><td class=".$gridcolor1.">SERVICIO</td><td class=".$gridcolor1.">".$wser."</td></tr>";
				echo "<tr><td class=".$gridcolor2.">RESPONSABLE</td><td class=".$gridcolor2.">".$weps."</td></tr>";
				echo "</table>";
				echo"</form>";
			break;
			case "G":
				$gridcolor1="tipoL02GRID1";
				$gridcolor2="tipoL02GRID2";
				echo "<body style='background-color:#E8EEF7' FACE='ARIAL' LINK='BLACK'>";	
				echo "<table align=center border=1 class=tipoTABLEGRID CELLPADDING=3>";
				echo "<tr><td id=tipoL06GRID colspan=2>MEDICOS TRATANTES</td></tr>";
				echo "<tr><td id=tipoL07GRID>NOMBRE</td><td id=tipoL07GRID>ESPECIALIDAD</td></tr>";
				//                 0       1      2       3      4       5       6       7
				$query = "select Methis,Meting,Medno1, Medno2, Medap1, Medap2, Metesp, Espnom, max(Metfek) ";
				$query .= "  from ".$wbasedatomovhos."_000018,".$wbasedatomovhos."_000047,".$wbasedatomovhos."_000048,".$wbasedatomovhos."_000044 "; 
				$query .= "   where Ubiald = 'off' ";
				$query .= " 	and Ubihis = '".$whis."' ";
				$query .= " 	and Ubiing = '".$wing."' ";
				$query .= " 	and Ubihis = Methis ";
				$query .= " 	and Ubiing = Meting ";
				$query .= " 	and Metest = 'on' ";
				$query .= " 	and Mettdo = Medtdo ";  
				$query .= " 	and Metdoc = Meddoc "; 
				$query .= " 	and Metesp = Espcod "; 
				$query .= "   group by 1,2,3,4,5,6 ";
				$query .= "   order by 1,2,9 desc,3,4,5,6 ";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num = mysql_num_rows($err);
				if($num > 0)
				{
					$wkey="";	
					for ($i=0;$i<$num;$i++)
					{
						if($i % 2 == 0)
							$color = $gridcolor1;
						else
							$color = $gridcolor2;
						$row = mysql_fetch_array($err);
						if($i == 0)
							$wkey=$row[8];	
						if($row[8] == $wkey)
						{
							echo "<tr><td class=".$color.">".$row[2]." ".$row[3]." ".$row[4]." ".$row[5]."</td><td class=".$color.">".$row[7]."</td></tr>";
						}
					}
				}					
				echo "</table>";
				echo"</form>";
			break;
			case "A":
				$gridcolor1="tipoL02GRID1";
				$gridcolor2="tipoL02GRID2";
				echo "<body style='background-color:#FFDDDD' FACE='ARIAL' LINK='BLACK'>";				
				echo "<table align=center border=1 class=tipoTABLEGRID CELLPADDING=3>";
				echo "<tr><td id=tipoL06GRID colspan=3>ALERTAS Y ALERGIAS</td></tr>";
				$query = "SELECT Encpro  from ".$empresa."_000001 ";
				$query .= " where Encale = 'on' ";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num = mysql_num_rows($err);
				if ($num > 0)
				{
					$knumw=-1;
					$ALERTAS=array();
					$row = mysql_fetch_array($err);
					$wformulario=$row[0];
					echo "<tr><td id=tipoL07GRID>FECHA</td><td id=tipoL07GRID>HORA</td><td id=tipoL07GRID>DESCRIPCION</td></tr>";
					$query  = " select ".$empresa."_".$wformulario.".fecha_data,".$empresa."_".$wformulario.".hora_data,max(".$empresa."_".$wformulario.".movcon) as a from ".$empresa."_".$wformulario." "; 
					$query .= " where ".$empresa."_".$wformulario.".movhis='".$whis."' ";
					$query .= "   and ".$empresa."_".$wformulario.".moving='".$wing."' ";
					$query .= " group by 1,2  ";
					$query .= " having a >= 1000 ";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					if ($num > 0)
					{
						for ($i=0;$i<$num;$i++)
						{
							$row = mysql_fetch_array($err);
							//                                                    0                                       1                            2                                   3                                    4
							$query  = " select ".$empresa."_".$wformulario.".fecha_data,".$empresa."_".$wformulario.".Hora_data,".$empresa."_000002.Detorp,".$empresa."_".$wformulario.".movdat,".$empresa."_".$wformulario.".movtip from ".$empresa."_".$wformulario.",".$empresa."_000002 ";
							$query .= " where ".$empresa."_".$wformulario.".movpro='".$wformulario."' ";
							$query .= "   and ".$empresa."_".$wformulario.".fecha_data = '".$row[0]."' "; 
							$query .= "   and ".$empresa."_".$wformulario.".hora_data = '".$row[1]."' ";
							$query .= "   and ".$empresa."_".$wformulario.".movhis='".$whis."' ";
							$query .= "   and ".$empresa."_".$wformulario.".moving='".$wing."' ";
							$query .= "   and ".$empresa."_".$wformulario.".movtip in ('Texto','Booleano') ";
							$query .= "   and ".$empresa."_".$wformulario.".movpro = ".$empresa."_000002.Detpro ";
							$query .= "   and ".$empresa."_".$wformulario.".movcon = ".$empresa."_000002.Detcon ";
							$query .= "  order by 1,2,3 ";
							$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
							$num1 = mysql_num_rows($err1);
							if ($num1 > 0)
							{
								$wfalert="";
								$whalert="";
								for ($j=0;$j<$num1;$j++)
								{
									$row1 = mysql_fetch_array($err1);
									if($row1[0] != $wfalert or $row1[1] != $whalert)
									{
										$knumw++;
										$wfalert=$row1[0];
										$whalert=$row1[1];
										$ALERTAS[$knumw][0]=$row1[0];
										$ALERTAS[$knumw][1]=$row1[1];
									}
									if($row1[4] == "Booleano")
										$ALERTAS[$knumw][2]=$row1[3];
									else
										$ALERTAS[$knumw][3]=$row1[3];
								}
							}
						}
					}
				}
				for ($j=0;$j<=$knumw;$j++)
				{
					if($j % 2 == 0)
						$color = $gridcolor1;
					else
						$color = $gridcolor2;
					if($ALERTAS[$j][2] == "CHECKED")
						echo "<tr><td class=".$color.">".$ALERTAS[$j][0]."</td><td class=".$color.">".$ALERTAS[$j][1]."</td><td class=".$color.">".$ALERTAS[$j][3]."</td></tr>";
				}
				echo "</table>";
				echo"</form>";
			break;
			case "C":
				$gridcolor1="tipoL02GRID1";
				$gridcolor2="tipoL02GRID2";
				echo "<body style='background-color:#E8EEF7' FACE='ARIAL' LINK='BLACK'>";				
				echo "<table align=center border=1 class=tipoTABLEGRID CELLPADDING=3>";
				echo "<tr><td id=tipoL06GRID>DIAGNOSTICOS</td></tr>";
				//                    0          1    
				$query = "select Fecha_data, Hora_data ";
				$query .= "  from ".$empresa."_000036  "; 
				$query .= "   where Firpro = '000367' ";
				$query .= " 	and Firhis = '".$whis."' ";
				$query .= " 	and Firing = '".$wing."' ";
				$query .= "   order by 1 desc,2 desc ";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num = mysql_num_rows($err);
				if($num > 0)
				{
					$row = mysql_fetch_array($err);
					$query = "select movcon,movdat ";
					$query .= "  from ".$empresa."_000367 "; 
					$query .= "   where ".$empresa."_000367.Fecha_data = '".$row[0]."' ";
					$query .= "     and ".$empresa."_000367.Hora_data = '".$row[1]."' ";
					$query .= " 	and movhis = '".$whis."' ";
					$query .= " 	and moving = '".$wing."' ";
					$query .= " 	and movcon = 55 ";
					$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$num1 = mysql_num_rows($err1);
					if($num1 > 0)
					{
						$row1 = mysql_fetch_array($err1);
						$diag=explode("|",diagnosticos($row1[1]));
						for ($j=0;$j<count($diag);$j++)
						{
							if($j % 2 == 0)
								$color = $gridcolor1;
							else
								$color = $gridcolor2;
							echo "<tr><td class=".$color.">".$diag[$j]."</td></tr>";
						}
					}
				}
				echo "</table>";
				echo"</form>";
			break;
			case "P":
				$gridcolor1="tipoL02GRID1";
				$gridcolor2="tipoL02GRID2";
				echo "<body style='background-color:#E8EEF7' FACE='ARIAL' LINK='BLACK'>";				
				echo "<table align=center border=1 class=tipoTABLEGRID CELLPADDING=3>";
				echo "<tr><td id=tipoL06GRID colspan=3>ANTECEDENTES</td></tr>";
				echo "<tr><td id=tipoL10GRID colspan=3>ANTECEDENTES PERSONALES</td></tr>";
				echo "<tr><td id=tipoL07GRID>DESCRIPCION</td><td id=tipoL07GRID>VALOR</td><td id=tipoL07GRID>OBSERVACIONES</td></tr>";
				//                    0          1    
				$query = "select Fecha_data, Hora_data ";
				$query .= "  from ".$empresa."_000036  "; 
				$query .= "   where Firpro = '000051' ";
				$query .= " 	and Firhis = '".$whis."' ";
				$query .= " 	and Firing = '".$wing."' ";
				$query .= "   order by 1 desc,2 desc ";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num = mysql_num_rows($err);
				if($num > 0)
				{
					$row = mysql_fetch_array($err);
					$query = "select movcon,movdat,Detorp,movtip,Detdes ";
					$query .= "  from ".$empresa."_000051,".$empresa."_000002 "; 
					$query .= "   where ".$empresa."_000051.Fecha_data = '".$row[0]."' ";
					$query .= "     and ".$empresa."_000051.Hora_data = '".$row[1]."' ";
					$query .= " 	and movhis = '".$whis."' ";
					$query .= " 	and moving = '".$wing."' ";
					$query .= " 	and movcon in (153,39,145,48,88,51,214,54,215,81,216,57,59,45,223,221,24,222,55,75) ";
					$query .= " 	and Detpro = '000051' ";
					$query .= " 	and movcon = Detcon ";
					$query .= " order by 3 ";
					$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$num1 = mysql_num_rows($err1);
					if($num1 > 0)
					{
						$ciclo=0;
						$hcedata=array();
						$hcelin=-1;
						$hcedata[0]="";
						$hcedata[1]="";
						$hcedata[2]="";
						for ($j=0;$j<$num1;$j++)
						{
							$row1 = mysql_fetch_array($err1);
							$ciclo++;
							if(($ciclo == 2 and $row1[3] == "Seleccion") or $ciclo > 2)
							{
								$hcelin++;
								if($hcelin % 2 == 0)
									$color = $gridcolor1;
								else
									$color = $gridcolor2;
								echo "<tr><td class=".$color.">".$hcedata[0]."</td><td class=".$color.">".$hcedata[1]."</td><td class=".$color.">".$hcedata[2]."</td></tr>";
								$ciclo=1;
								$hcedata[0]="";
								$hcedata[1]="";
								$hcedata[2]="";
							}
							if($row1[3] == "Seleccion")
							{
								$hcedata[0]=$row1[4];
								$row1[1]=substr($row1[1],strpos($row1[1],"-")+1);
							}
							$hcedata[$ciclo]=$row1[1];
						}
						if($hcelin % 2 == 0)
							$color = $gridcolor1;
						else
							$color = $gridcolor2;
						echo "<tr><td class=".$color.">".$hcedata[0]."</td><td class=".$color.">".$hcedata[1]."</td><td class=".$color.">".$hcedata[2]."</td></tr>";
					}
					echo "<tr><td id=tipoL10GRID colspan=3>ANTECEDENTES FAMILIARES</td></tr>";
					echo "<tr><td id=tipoL07GRID>DESCRIPCION</td><td id=tipoL07GRID>VALOR</td><td id=tipoL07GRID>OBSERVACIONES</td></tr>";
					$query = "select movcon,movdat,Detorp,movtip,Detdes ";
					$query .= "  from ".$empresa."_000051,".$empresa."_000002 ";  
					$query .= "   where ".$empresa."_000051.Fecha_data = '".$row[0]."' ";
					$query .= "     and ".$empresa."_000051.Hora_data = '".$row[1]."' ";
					$query .= " 	and movhis = '".$whis."' ";
					$query .= " 	and moving = '".$wing."' ";
					$query .= " 	and movcon in (74,76,79,80,83,85,201,89,218,100,185,21) ";
					$query .= " 	and Detpro = '000051' ";
					$query .= " 	and movcon = Detcon ";
					$query .= " order by 3 ";
					$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$num1 = mysql_num_rows($err1);
					if($num1 > 0)
					{
						$ciclo=0;
						$hcedata=array();
						$hcelin=-1;
						$hcedata[0]="";
						$hcedata[1]="";
						$hcedata[2]="";
						for ($j=0;$j<$num1;$j++)
						{
							$row1 = mysql_fetch_array($err1);
							$ciclo++;
							if(($ciclo == 2 and $row1[3] == "Seleccion") or $ciclo > 2)
							{
								$hcelin++;
								if($hcelin % 2 == 0)
									$color = $gridcolor1;
								else
									$color = $gridcolor2;
								echo "<tr><td class=".$color.">".$hcedata[0]."</td><td class=".$color.">".$hcedata[1]."</td><td class=".$color.">".$hcedata[2]."</td></tr>";
								$ciclo=1;
								$hcedata[0]="";
								$hcedata[1]="";
								$hcedata[2]="";
							}
							if($row1[3] == "Seleccion")
							{
								$hcedata[0]=$row1[4];
								$row1[1]=substr($row1[1],strpos($row1[1],"-")+1);
							}
							$hcedata[$ciclo]=$row1[1];
						}
						if($hcelin % 2 == 0)
							$color = $gridcolor1;
						else
							$color = $gridcolor2;
						echo "<tr><td class=".$color.">".$hcedata[0]."</td><td class=".$color.">".$hcedata[1]."</td><td class=".$color.">".$hcedata[2]."</td></tr>";
					}
				}
				else
				{
						//                    0          1    
					$query = "select Fecha_data, Hora_data ";
					$query .= "  from ".$empresa."_000036  "; 
					$query .= "   where Firpro = '000138' ";
					$query .= " 	and Firhis = '".$whis."' ";
					$query .= " 	and Firing = '".$wing."' ";
					$query .= "   order by 1 desc,2 desc ";
					$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$num = mysql_num_rows($err);
					if($num > 0)
					{
						$row = mysql_fetch_array($err);
						$query = "select movcon,movdat,Detorp,movtip,Detdes ";
						$query .= "  from ".$empresa."_000138,".$empresa."_000002 "; 
						$query .= "   where ".$empresa."_000138.Fecha_data = '".$row[0]."' ";
						$query .= "     and ".$empresa."_000138.Hora_data = '".$row[1]."' ";
						$query .= " 	and movhis = '".$whis."' ";
						$query .= " 	and moving = '".$wing."' ";
						$query .= " 	and movcon in (33,36,38,39,41,42,44,45,47,48,50,51,53,54,56,57,127,146,148,149) ";
						$query .= " 	and Detpro = '000138' ";
						$query .= " 	and movcon = Detcon ";
						$query .= " order by 3 ";
						$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
						$num1 = mysql_num_rows($err1);
						if($num1 > 0)
						{
							$ciclo=0;
							$hcedata=array();
							$hcelin=-1;
							$hcedata[0]="";
							$hcedata[1]="";
							$hcedata[2]="";
							for ($j=0;$j<$num1;$j++)
							{
								$row1 = mysql_fetch_array($err1);
								$ciclo++;
								if(($ciclo == 2 and $row1[3] == "Seleccion") or $ciclo > 2)
								{
									$hcelin++;
									if($hcelin % 2 == 0)
										$color = $gridcolor1;
									else
										$color = $gridcolor2;
									echo "<tr><td class=".$color.">".$hcedata[0]."</td><td class=".$color.">".$hcedata[1]."</td><td class=".$color.">".$hcedata[2]."</td></tr>";
									$ciclo=1;
									$hcedata[0]="";
									$hcedata[1]="";
									$hcedata[2]="";
								}
								if($row1[3] == "Seleccion")
								{
									$hcedata[0]=$row1[4];
									$row1[1]=substr($row1[1],strpos($row1[1],"-")+1);
								}
								$hcedata[$ciclo]=$row1[1];
							}
							if($hcelin % 2 == 0)
								$color = $gridcolor1;
							else
								$color = $gridcolor2;
							echo "<tr><td class=".$color.">".$hcedata[0]."</td><td class=".$color.">".$hcedata[1]."</td><td class=".$color.">".$hcedata[2]."</td></tr>";
						}
						echo "<tr><td id=tipoL10GRID colspan=3>ANTECEDENTES FAMILIARES</td></tr>";
						echo "<tr><td id=tipoL07GRID>DESCRIPCION</td><td id=tipoL07GRID>VALOR</td><td id=tipoL07GRID>OBSERVACIONES</td></tr>";
						$query = "select movcon,movdat,Detorp,movtip,Detdes ";
						$query .= "  from ".$empresa."_000138,".$empresa."_000002 ";  
						$query .= "   where ".$empresa."_000138.Fecha_data = '".$row[0]."' ";
						$query .= "     and ".$empresa."_000138.Hora_data = '".$row[1]."' ";
						$query .= " 	and movhis = '".$whis."' ";
						$query .= " 	and moving = '".$wing."' ";
						$query .= " 	and movcon in (80,81,83,84,86,87,89,90,92,93,95,96) ";
						$query .= " 	and Detpro = '000138' ";
						$query .= " 	and movcon = Detcon ";
						$query .= " order by 3 ";
						$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
						$num1 = mysql_num_rows($err1);
						if($num1 > 0)
						{
							$ciclo=0;
							$hcedata=array();
							$hcelin=-1;
							$hcedata[0]="";
							$hcedata[1]="";
							$hcedata[2]="";
							for ($j=0;$j<$num1;$j++)
							{
								$row1 = mysql_fetch_array($err1);
								$ciclo++;
								if(($ciclo == 2 and $row1[3] == "Seleccion") or $ciclo > 2)
								{
									$hcelin++;
									if($hcelin % 2 == 0)
										$color = $gridcolor1;
									else
										$color = $gridcolor2;
									echo "<tr><td class=".$color.">".$hcedata[0]."</td><td class=".$color.">".$hcedata[1]."</td><td class=".$color.">".$hcedata[2]."</td></tr>";
									$ciclo=1;
									$hcedata[0]="";
									$hcedata[1]="";
									$hcedata[2]="";
								}
								if($row1[3] == "Seleccion")
								{
									$hcedata[0]=$row1[4];
									$row1[1]=substr($row1[1],strpos($row1[1],"-")+1);
								}
								$hcedata[$ciclo]=$row1[1];
							}
							if($hcelin % 2 == 0)
								$color = $gridcolor1;
							else
								$color = $gridcolor2;
							echo "<tr><td class=".$color.">".$hcedata[0]."</td><td class=".$color.">".$hcedata[1]."</td><td class=".$color.">".$hcedata[2]."</td></tr>";
						}
					}
				}
				echo "</table>";
				echo"</form>";
			break;
			case "E":
				$gridcolor1="tipoL02GRID1";
				$gridcolor2="tipoL02GRID2";
				$gridcolor3="tipoL09GRID";
				echo "<body style='background-color:#E8EEF7' FACE='ARIAL' LINK='BLACK'>";				
				echo "<table align=center border=1 class=tipoTABLEGRID CELLPADDING=3>";
				echo "<tr><td class=tipoL08GRID>ESTADO DEL PACIENTE</td></tr>";
				echo "</table>";
				echo "<h3 OnClick='toggleDisplay(div1)' class=tipo3>SIGNOS VITALES (click)</h3>";
				echo "<center><table align=center border=1 class=tipoTABLEGRID CELLPADDING=3 id='div1' style='display: none' colspan=3>";
				//                                        0                             1      2      3       4      5      6
				$query = "select ".$empresa."_000080.Fecha_data,".$empresa."_000080.Hora_data,movcon,movdat,Detorp,movtip,Detdes ";
				$query .= "  from ".$empresa."_000080,".$empresa."_000002 "; 
				$query .= "   where movhis = '".$whis."' ";
				$query .= " 	and moving = '".$wing."' ";
				$query .= " 	and movcon in (5,6,7,8,10,11,12,13) ";
				$query .= " 	and Detpro = '000080' ";
				$query .= " 	and movcon = Detcon ";
				$query .= " UNION ";
				$query .= "select ".$empresa."_000096.Fecha_data,".$empresa."_000096.Hora_data,movcon,movdat,Detorp,movtip,Detdes ";
				$query .= "  from ".$empresa."_000096,".$empresa."_000002 "; 
				$query .= "   where movhis = '".$whis."' ";
				$query .= " 	and moving = '".$wing."' ";
				$query .= " 	and movcon in (13,14,15,18,19,20,21) ";
				$query .= " 	and Detpro = '000096' ";
				$query .= " 	and movcon = Detcon ";
				$query .= " order by 1 desc, 2 desc,5 ";
				$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num1 = mysql_num_rows($err1);
				if($num1 > 0)
				{
					$hcedata=array();
					$numfech=0;
					$hcekey="";
					$hcedata["0"][0]="PARAMETROS";
					for ($i=0;$i<$num1;$i++)
					{
						$row1 = mysql_fetch_array($err1);
						if($row1[0].$row1[1] != $hcekey)
						{
							$hcekey=$row1[0].$row1[1];
							$numfech++;
							if($numfech > 3)
							{
								$i=$num1;
								break;
							}
							else
								$hcedata["0"][$numfech]=$row1[0]."<br>".$row1[1];
						}
						$hcedata[$row1[6]][0]=$row1[6];
						if($row1[5] == "Formula")
							$hcedata[$row1[6]][$numfech]=number_format((double)$row1[3],2,'.',',');
						elseif($row1[5] == "Seleccion")
								$hcedata[$row1[6]][$numfech]=substr($row1[3],strpos($row1[3],"-")+1);
							else
								$hcedata[$row1[6]][$numfech]=$row1[3];
					}
					$i=-1;
					foreach($hcedata as $valorhce)
					{
						$i++;
						if($i == 0)
							$color = $gridcolor3;
						elseif($i % 2 == 0)
							$color = $gridcolor1;
						else
							$color = $gridcolor2;
						echo "<tr>";
						if($numfech < 3)
							$numfech++;
						for ($j=0;$j<$numfech;$j++)
						{
							if(isset($valorhce[$j]))
								echo "<td class=".$color.">".$valorhce[$j]."</td>";
							else
								echo "<td class=".$color."></td>";
						}
						echo "</tr>";
					}
				}
				echo"</table>";
				echo "<h3 OnClick='toggleDisplay(div2)' class=tipo3>GLUCOMETRIAS (click)</h3>";
				echo "<center><table align=center border=1 class=tipoTABLEGRID CELLPADDING=3 id='div2' style='display: none' colspan=3>";
				//                                        0                             1      2      3       4      5      6      7
				$query = "select ".$empresa."_000092.Fecha_data,".$empresa."_000092.Hora_data,movcon,movdat,Detorp,movtip,Detdes,Detume ";
				$query .= "  from ".$empresa."_000092,".$empresa."_000002 "; 
				$query .= "   where movhis = '".$whis."' ";
				$query .= " 	and moving = '".$wing."' ";
				$query .= " 	and movcon in (4,5) ";
				$query .= " 	and Detpro = '000092' ";
				$query .= " 	and movcon = Detcon ";
				$query .= " order by 1 desc, 2 desc,5 ";
				$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num1 = mysql_num_rows($err1);
				if($num1 > 0)
				{
					$hcedata=array();
					$numfech=0;
					$hcekey="";
					$hcedata["0"][0]="PARAMETROS";
					for ($i=0;$i<$num1;$i++)
					{
						$row1 = mysql_fetch_array($err1);
						if($row1[0].$row1[1] != $hcekey)
						{
							$hcekey=$row1[0].$row1[1];
							$numfech++;
							if($numfech > 3)
							{
								$i=$num1;
								break;
							}
							else
								$hcedata["0"][$numfech]=$row1[0]."<br>".$row1[1];
						}
						$hcedata[$row1[6]][0]=$row1[6];
						if($row1[5] == "Formula")
							$hcedata[$row1[6]][$numfech]=number_format((double)$row1[3],2,'.',',');
						elseif($row1[5] == "Seleccion")
								$hcedata[$row1[6]][$numfech]=substr($row1[3],strpos($row1[3],"-")+1);
							elseif($row1[5] == "Numero")
									$hcedata[$row1[6]][$numfech]=$row1[3]." ".$row1[7];
								else
									$hcedata[$row1[6]][$numfech]=$row1[3];
					}
					$i=-1;
					foreach($hcedata as $valorhce)
					{
						$i++;
						if($i == 0)
							$color = $gridcolor3;
						elseif($i % 2 == 0)
							$color = $gridcolor1;
						else
							$color = $gridcolor2;
						echo "<tr>";
						if($numfech < 3)
							$numfech++;
						for ($j=0;$j<$numfech;$j++)
						{
							if(isset($valorhce[$j]))
								echo "<td class=".$color.">".$valorhce[$j]."</td>";
							else
								echo "<td class=".$color."></td>";
						}
						echo "</tr>";
					}
				}
				echo"</table>";
				
				echo "<h3 OnClick='toggleDisplay(div3)' class=tipo3>BALANCE DE LIQUIDOS (click)</h3>";
				$DATA1=array();
				$k1=-1;
				$klave="";
				$wformulario="000089";
				$wfechaf = date ('Y-m-d');
				$wfechai = strtotime ('-72 hour' , strtotime ($wfechaf)) ;
				$wfechai = date ('Y-m-d' , $wfechai);
				//                                                    0                                       1                                     2                                    3  
				$query  = " select ".$empresa."_".$wformulario.".fecha_data,".$empresa."_".$wformulario.".Hora_data,".$empresa."_".$wformulario.".movcon,".$empresa."_".$wformulario.".movdat from ".$empresa."_".$wformulario." ";
				$query .= " where ".$empresa."_".$wformulario.".movpro='".$wformulario."' ";
				$query .= "   and ".$empresa."_".$wformulario.".movhis='".$whis."' ";
				$query .= "   and ".$empresa."_".$wformulario.".moving='".$wing."' ";
				$query .= "   and ".$empresa."_".$wformulario.".movcon in (2,3,6,7,8,11) ";
				$query .= "   and ".$empresa."_".$wformulario.".fecha_data BETWEEN '".$wfechai."' and '".$wfechaf."'";
				$query .= " order by 1,2,3  ";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num = mysql_num_rows($err);
				if($num > 0)
				{
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						if($klave != $row[0].$row[1])
						{
							if(($k1 > -1 and $DATA1[$k1]['7'] != "" and $DATA1[$k1]['HORA'] != "") or $k1 == -1)
								$k1++;
							$DATA1[$k1]['CLAVE']="";
							$DATA1[$k1]['FECHA']="";
							$DATA1[$k1]['HORA']="";
							$DATA1[$k1]['6']="";
							$DATA1[$k1]['7']="";
							$DATA1[$k1]['8']="";
							$klave = $row[0].$row[1];
						}
						if($row[2] == "2")
						{
							$DATA1[$k1]['FECHA']=$row[3];
							$DATA1[$k1]['CLAVE']=$row[3];
						}
						else
							if($row[2] == "3")
								$DATA1[$k1]['CLAVE'] .= $row[3];
							else
								if($row[2] == "11")
								{
									$DATA1[$k1]['HORA']=(integer)$row[3];
									if((integer)$row[3] < 10)
										$DATA1[$k1]['CLAVE'] = substr($DATA1[$k1]['CLAVE'],0,10)."0".$row[3].substr($DATA1[$k1]['CLAVE'],11);
									else
										$DATA1[$k1]['CLAVE'] = substr($DATA1[$k1]['CLAVE'],0,10).$row[3].substr($DATA1[$k1]['CLAVE'],11);
								}
								else
									$DATA1[$k1][(string)$row[2]]=$row[3];
					}
					if($k1 > -1 and ($DATA1[$k1]['7'] == "" or $DATA1[$k1]['HORA'] == ""))
						$k1--;
				}
				$DATA2=array();
				$k2=-1;
				$klave="";
				//                                                    0                                       1                                     2                                    3  
				$query  = " select ".$empresa."_".$wformulario.".fecha_data,".$empresa."_".$wformulario.".Hora_data,".$empresa."_".$wformulario.".movcon,".$empresa."_".$wformulario.".movdat from ".$empresa."_".$wformulario." ";
				$query .= " where ".$empresa."_".$wformulario.".movpro='".$wformulario."' ";
				$query .= "   and ".$empresa."_".$wformulario.".movhis='".$whis."' ";
				$query .= "   and ".$empresa."_".$wformulario.".moving='".$wing."' ";
				$query .= "   and ".$empresa."_".$wformulario.".movcon in (2,3,30,25,26) ";
				$query .= "   and ".$empresa."_".$wformulario.".fecha_data BETWEEN '".$wfechai."' and '".$wfechaf."'";
				$query .= " order by 1,2,3  ";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num = mysql_num_rows($err);
				if($num > 0)
				{
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						if($klave != $row[0].$row[1])
						{
							if(($k2 > -1 and $DATA2[$k2]['26'] != "" and $DATA2[$k2]['HORA'] != "") or $k2 == -1)
								$k2++;
							$DATA2[$k2]['CLAVE']="";
							$DATA2[$k2]['FECHA']="";
							$DATA2[$k2]['HORA']="";
							$DATA2[$k2]['25']="";
							$DATA2[$k2]['26']="";
							$klave = $row[0].$row[1];
						}
						if($row[2] == "2")
						{
							$DATA2[$k2]['FECHA']=$row[3];
							$DATA2[$k2]['CLAVE']=$row[3];		
						}
						else
							if($row[2] == "3")
								$DATA2[$k2]['CLAVE'] .= $row[3];
							else
								if($row[2] == "30")
								{
									$DATA2[$k2]['HORA']=(integer)$row[3];
									if((integer)$row[3] < 10)
										$DATA2[$k2]['CLAVE'] = substr($DATA2[$k2]['CLAVE'],0,10)."0".$row[3].substr($DATA2[$k2]['CLAVE'],11);
									else
										$DATA2[$k2]['CLAVE'] = substr($DATA2[$k2]['CLAVE'],0,10).$row[3].substr($DATA2[$k2]['CLAVE'],11);
								}
								else
									$DATA2[$k2][(string)$row[2]]=$row[3];
					}
					if($k2 > -1 and ($DATA2[$k2]['26'] == "" or $DATA2[$k2]['HORA'] == ""))
						$k2--;
				}
				$DATA0=array();
				for ($i=0;$i<=$k1;$i++)
				{
					$DATA0[$DATA1[$i]['CLAVE']][0]=$DATA1[$i]['CLAVE'];
					$DATA0[$DATA1[$i]['CLAVE']]['FECHA']=$DATA1[$i]['FECHA'];
					$DATA0[$DATA1[$i]['CLAVE']]['HORA']=$DATA1[$i]['HORA'];
					$DATA0[$DATA1[$i]['CLAVE']]['6']=$DATA1[$i]['6'];
					$DATA0[$DATA1[$i]['CLAVE']]['7']=$DATA1[$i]['7'];
					$DATA0[$DATA1[$i]['CLAVE']]['8']=$DATA1[$i]['8'];
					$DATA0[$DATA1[$i]['CLAVE']]['25']="";
					$DATA0[$DATA1[$i]['CLAVE']]['26']="";
				}
				for ($i=0;$i<=$k2;$i++)
				{
					if(isset($DATA0[$DATA2[$i]['CLAVE']][0]))
					{
						$DATA0[$DATA2[$i]['CLAVE']]['25']=$DATA2[$i]['25'];
						$DATA0[$DATA2[$i]['CLAVE']]['26']=$DATA2[$i]['26'];
					}
					else
					{
						$DATA0[$DATA2[$i]['CLAVE']][0]=$DATA2[$i]['CLAVE'];
						$DATA0[$DATA2[$i]['CLAVE']]['FECHA']=$DATA2[$i]['FECHA'];
						$DATA0[$DATA2[$i]['CLAVE']]['HORA']=$DATA2[$i]['HORA'];
						$DATA0[$DATA2[$i]['CLAVE']]['6']="";
						$DATA0[$DATA2[$i]['CLAVE']]['7']="";
						$DATA0[$DATA2[$i]['CLAVE']]['8']="";
						$DATA0[$DATA2[$i]['CLAVE']]['25']=$DATA2[$i]['25'];
						$DATA0[$DATA2[$i]['CLAVE']]['26']=$DATA2[$i]['26'];
					}
				}
				usort($DATA0,'comparacion');
				
				$DATA=array();
				$k=-1;
				while($DATA3 = each($DATA0))
				{
					$k++;
					$DATA[$k]['FECHA']=$DATA3[1]['FECHA'];
					$DATA[$k]['HORA']=$DATA3[1]['HORA'];
					$DATA[$k]['6']=$DATA3[1]['6'];
					$DATA[$k]['7']=$DATA3[1]['7'];
					$DATA[$k]['8']=$DATA3[1]['8'];
					$DATA[$k]['25']=$DATA3[1]['25'];
					$DATA[$k]['26']=$DATA3[1]['26'];
				}
				
				
				if($k > -1)
					$Fenat=$DATA[0]['FECHA'];
				$wtotA=0;
				$wtotE=0;
				$Hant=" ";
				$DIA=0;
				echo "<center><table align=center border=1 class=tipoTABLEGRID CELLPADDING=3 id='div3' style='display: none'>";
				echo "<tr><td id=tipoL07GRID colspan=9>BALANCE DE LIQUIDOS</td></tr>";
				echo "<tr><td id=tipoL07GRID colspan=9>DESDE ".$wfechai." HASTA ".$wfechaf."</td></tr>";
				echo "<tr><td id=tipoT02A colspan=5>ADMINISTRADOS</td><td id=tipoT02B colspan=4>ELIMINADOS</td></tr>";
				echo "<tr><td id=tipoT02C>Fecha</td><td id=tipoT02C>Hora</td><td id=tipoT02C>Via</td><td id=tipoT02C>Descripci&oacute;n</td><td id=tipoT02C>Volumen</td><td id=tipoT02C>Fecha</td><td id=tipoT02C>Hora</td><td id=tipoT02C>Via</td><td id=tipoT02C>Volumen</td></tr>";
				$color="tipoG03BX";
				$DIA++;
				echo "<tr><td id=".$color." colspan=9>DIA ".$DIA."</td></tr>";
				for ($i=0;$i<=$k;$i++)
				{			
					$diff=diferencia($DATA[$i]['FECHA'],$Fenat);
					$Fenat=$DATA[$i]['FECHA'];
					if($diff > 1)
					{
						$color="tipoG03B";
						echo "<tr><td id=".$color." colspan=4>TOTAL ADMINISTRADOS</td><td id=".$color."D>".number_format((double)$wtotA,0,'.',',')."</td><td id=".$color." colspan=3>TOTAL ELIMINADOS</td><td id=".$color."D>".number_format((double)$wtotE,0,'.',',')."</td></tr>";
						$balance=$wtotA-$wtotE;
						$color="tipoG03BH";
						if($balance > 0)
							echo "<tr><td id=".$color." colspan=8>BALANCE</td><td id=".$color."D>".number_format((double)$balance,0,'.',',')."</td></tr>";
						else
							echo "<tr><td id=".$color." colspan=8>BALANCE</td><td id=".$color."DN>".number_format((double)$balance,0,'.',',')."</td></tr>";
						$color="tipoG03BX";
						$DIA++;
						echo "<tr><td id=".$color." colspan=9>DIA ".$DIA."</td></tr>";
						$wtotA=0;
						$wtotE=0;
					}
					else
					{
						if(($DATA[$i]['HORA'] > 6 and $Hant <= 6 and $Hant != " ") or ($DATA[$i]['HORA'] > 6 and $Hant > $DATA[$i]['HORA'] and $Hant != " " and $diff == 1))
						{
							$color="tipoG03B";
							echo "<tr><td id=".$color." colspan=4>TOTAL ADMINISTRADOS</td><td id=".$color."D>".number_format((double)$wtotA,0,'.',',')."</td><td id=".$color." colspan=3>TOTAL ELIMINADOS</td><td id=".$color."D>".number_format((double)$wtotE,0,'.',',')."</td></tr>";
							$balance=$wtotA-$wtotE;
							$color="tipoG03BH";
							if($balance > 0)
								echo "<tr><td id=".$color." colspan=8>BALANCE</td><td id=".$color."D>".number_format((double)$balance,0,'.',',')."</td></tr>";
							else
								echo "<tr><td id=".$color." colspan=8>BALANCE</td><td id=".$color."DN>".number_format((double)$balance,0,'.',',')."</td></tr>";
							$color="tipoG03BX";
							$DIA++;
							echo "<tr><td id=".$color." colspan=9>DIA ".$DIA."</td></tr>";
							$wtotA=0;
							$wtotE=0;
						}
						elseif(($DATA[$i]['HORA'] > 18 and $Hant <= 18 and $Hant != " ") or ($DATA[$i]['HORA'] < 7 and $Hant <= 18  and $Hant != " " and $diff == 1))
							{
								$X19=1;
								$X7=0;
								$color="tipoG03B";
								echo "<tr><td id=".$color." colspan=4>SUBTOTAL ADMINISTRADOS</td><td id=".$color."D>".number_format((double)$wtotA,0,'.',',')."</td><td id=".$color." colspan=3>SUBTOTAL ELIMINADOS</td><td id=".$color."D>".number_format((double)$wtotE,0,'.',',')."</td></tr>";
							}
						
					}
					$Hant=$DATA[$i]['HORA'];
					if($i % 2 == 0)
					{
						$color="TipoG03W";
						$color1="TipoG04W";
					}
					else
					{
						$color="TipoG03";
						$color1="TipoG03";
					}
					
					if($DATA[$i]['7'] != "" and $DATA[$i]['7'] != 0 and $DATA[$i]['26'] != "" and $DATA[$i]['26'] != 0)
					{
						$wtotA += $DATA[$i]['7'];
						$wtotE += $DATA[$i]['26'];
						echo "<tr><td id=".$color.">".$DATA[$i]['FECHA']."</td><td id=".$color.">".$DATA[$i]['HORA']."</td><td id=".$color.">".ver($DATA[$i]['6'])."</td><td id=".$color.">".ver($DATA[$i]['8'])."</td><td id=".$color."D>".number_format((double)$DATA[$i]['7'],0,'.',',')."</td><td id=".$color1.">".$DATA[$i]['FECHA']."</td><td id=".$color1.">".$DATA[$i]['HORA']."</td><td id=".$color1.">".ver($DATA[$i]['25'])."</td><td id=".$color1."D>".number_format((double)$DATA[$i]['26'],0,'.',',')."</td></tr>";
					}
					elseif($DATA[$i]['7'] != "" and $DATA[$i]['7'] != 0)
						{
							$wtotA += $DATA[$i]['7'];
							echo "<tr><td id=".$color.">".$DATA[$i]['FECHA']."</td><td id=".$color.">".$DATA[$i]['HORA']."</td><td id=".$color.">".ver($DATA[$i]['6'])."</td><td id=".$color.">".ver($DATA[$i]['8'])."</td><td id=".$color."D>".number_format((double)$DATA[$i]['7'],0,'.',',')."</td><td id=".$color1."></td><td id=".$color1."></td></td><td id=".$color1."></td><td id=".$color1."></td></tr>";
						}
						elseif($DATA[$i]['26'] != "" and $DATA[$i]['26'] != 0)
							{
								$wtotE += $DATA[$i]['26'];
								echo "<tr><td id=".$color."></td><td id=".$color."></td><td id=".$color."></td><td id=".$color."></td><td id=".$color."D></td><td id=".$color1.">".$DATA[$i]['FECHA']."</td><td id=".$color1.">".$DATA[$i]['HORA']."</td><td id=".$color1.">".ver($DATA[$i]['25'])."</td><td id=".$color1."D>".number_format((double)$DATA[$i]['26'],0,'.',',')."</td></tr>";
							}
				}
				$color="tipoG03B";
				echo "<tr><td id=".$color." colspan=4>TOTAL ADMINISTRADOS</td><td id=".$color."D>".number_format((double)$wtotA,0,'.',',')."</td><td id=".$color." colspan=3>TOTAL ELIMINADOS</td><td id=".$color."D>".number_format((double)$wtotE,0,'.',',')."</td></tr>";
				$balance=$wtotA-$wtotE;
				$color="tipoG03BH";
				if($balance > 0)
					echo "<tr><td id=".$color." colspan=8>BALANCE</td><td id=".$color."D>".number_format((double)$balance,0,'.',',')."</td></tr>";
				else
					echo "<tr><td id=".$color." colspan=8>BALANCE</td><td id=".$color."DN>".number_format((double)$balance,0,'.',',')."</td></tr>";
				$color="tipoG03BX";
				echo "</table>";
				echo"</form>";
			break;
			case "I":
				$gridcolor1="tipoL02GRID1";
				$gridcolor2="tipoL02GRID2";
				$gridcolor3="tipoL09GRID";
				$wfechat=date ('Y-m-d');
				echo "<body style='background-color:#E8EEF7' FACE='ARIAL' LINK='BLACK'>";				
				echo "<table align=center border=1 class=tipoTABLEGRID CELLPADDING=3>";
				echo "<tr><td class=tipoL08GRID>DATOS CLINICOS</td></tr>";
				echo "</table>";
				echo "<h3 OnClick='toggleDisplay(div4)' class=tipo3>MEDICAMENTOS ACTIVOS (click)</h3>";
				echo "<center><table align=center border=1 class=tipoTABLEGRID CELLPADDING=3 id='div4' style='display: none'>";
				echo "<tr><td id=tipoL07GRID>ARTICULO</td><td id=tipoL07GRID>NOMBRE GENERICO</td><td id=tipoL07GRID>DOSIS</td><td id=tipoL07GRID>UNIDAD</td><td id=tipoL07GRID>VIA</td><td id=tipoL07GRID>FRECUENCIA<br>DIAS</td></tr>";
				//                 0                    1                  2                3                        4              5  
				$query = "SELECT kadart as articulo, artgen as Generico, kadcfr as Dosis, kadufr as UnidadDeDosis, viades as via, perequ as frecuencia ";
				$query .= "  FROM ".$wbasedatomovhos."_000054,".$wbasedatomovhos."_000026,".$wbasedatomovhos."_000043,".$wbasedatomovhos."_000040 ";
				$query .= "  WHERE kadhis = '".$whis."' ";
				$query .= "    AND kading = '".$wing."' ";
				$query .= "    AND kadfec = '".$wfechat."'";
				$query .= "    AND kadest = 'on' ";	
				$query .= "    AND kadsus = 'off' ";
				$query .= "    AND artcod = kadart ";
				$query .= "    AND artest = 'on' ";
				$query .= "    AND kadvia = viacod ";
				$query .= "    AND kadper = percod ";
				$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num1 = mysql_num_rows($err1);
				if($num1 > 0)
				{
					for ($i=0;$i<$num1;$i++)
					{
						if($i % 2 == 0)
							$color = $gridcolor1;
						else
							$color = $gridcolor2;
						$row1 = mysql_fetch_array($err1);
						echo "<tr><td class=".$color.">".$row1[0]."</td><td class=".$color.">".$row1[1]."</td><td class=".$color.">".$row1[2]."</td><td class=".$color.">".$row1[3]."</td><td class=".$color.">".$row1[4]."</td><td class=".$color.">".$row1[5]."</td></tr>";
					}
				}
				echo "</table>";
				
				$wfechaf = date ('Y-m-d');
				$wfechai = strtotime ('-72 hour' , strtotime ($wfechaf)) ;
				$wfechai = date ('Y-m-d' , $wfechai);
				echo "<h3 OnClick='toggleDisplay(div5)' class=tipo3>MEDICAMENTOS SUSPENDIDOS LAS ULTIMAS 72 HORAS (click)</h3>";
				echo "<center><table align=center border=1 class=tipoTABLEGRID CELLPADDING=3 id='div5' style='display: none'>";
				echo "<tr><td id=tipoL07GRID>ARTICULO</td><td id=tipoL07GRID>NOMBRE GENERICO</td><td id=tipoL07GRID>DOSIS</td><td id=tipoL07GRID>UNIDAD</td><td id=tipoL07GRID>VIA</td><td id=tipoL07GRID>FRECUENCIA<br>DIAS</td></tr>";
				//                 0                    1                  2                3                        4              5  
				$query = "SELECT kadart as articulo, artgen as Generico, kadcfr as Dosis, kadufr as UnidadDeDosis, viades as via, perequ as frecuencia ";
				$query .= "  FROM ".$wbasedatomovhos."_000054,".$wbasedatomovhos."_000026,".$wbasedatomovhos."_000043,".$wbasedatomovhos."_000040 ";
				$query .= "  WHERE kadhis = '".$whis."' ";
				$query .= "    AND kading = '".$wing."' ";
				$query .= "    AND kadfec BETWEEN '".$wfechai."' and '".$wfechaf."'";
				$query .= "    AND kadest = 'on' ";	
				$query .= "    AND kadsus = 'on' ";
				$query .= "    AND artcod = kadart ";
				$query .= "    AND artest = 'on' ";
				$query .= "    AND kadvia = viacod ";
				$query .= "    AND kadper = percod ";
				$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num1 = mysql_num_rows($err1);
				if($num1 > 0)
				{
					for ($i=0;$i<$num1;$i++)
					{
						if($i % 2 == 0)
							$color = $gridcolor1;
						else
							$color = $gridcolor2;
						$row1 = mysql_fetch_array($err1);
						echo "<tr><td class=".$color.">".$row1[0]."</td><td class=".$color.">".$row1[1]."</td><td class=".$color.">".$row1[2]."</td><td class=".$color.">".$row1[3]."</td><td class=".$color.">".$row1[4]."</td><td class=".$color.">".$row1[5]."</td></tr>";
					}
				}
				echo "</table>";
				echo "<h3 OnClick='toggleDisplay(div6)' class=tipo3>HISTORIAL DE ADMINISTRACION DE ANTIBIOTICOS (click)</h3>";
				echo "<center><table align=center border=1 class=tipoTABLEGRID CELLPADDING=3 id='div6' style='display: none'>";
				echo "<tr><td id=tipoL07GRID>ARTICULO</td><td id=tipoL07GRID>NOMBRE GENERICO</td><td id=tipoL07GRID>DOSIS</td><td id=tipoL07GRID>UNIDAD</td><td id=tipoL07GRID>VIA</td><td id=tipoL07GRID>FRECUENCIA<br>DIAS</td><td id=tipoL07GRID>FECHA<br>INICIAL</td><td id=tipoL07GRID>FECHA<br>FINAL</td></tr>";
				
				//                 0                   1                   2                3                 4              5                     6
				$query = "SELECT kadart as articulo, artgen as Generico, kadcfr as Dosis, kadufr as Unidad, viades as via, perequ as frecuencia, kadfec as Fecha ";
				$query .= " FROM ".$wbasedatomovhos."_000054,".$wbasedatomovhos."_000026,".$wbasedatomovhos."_000043,".$wbasedatomovhos."_000040 ";
				$query .= " WHERE kadhis = '".$whis."' ";
				$query .= "   AND kading = '".$wing."' ";
				$query .= "   AND kadest = 'on' ";	
				$query .= "   AND kadsus = 'off' ";
				$query .= "   AND kadart = artcod ";
				$query .= "   AND artgru LIKE 'J00%' ";
				$query .= "  AND kadori = 'SF' ";
				$query .= "   AND kadvia = viacod ";
				$query .= "   AND kadper = percod ";
				$query .= " UNION ";
				$query .= " SELECT kadart as articulo, artgen as Generico, kadcfr as Dosis, kadufr as Unidad, viades as via, perequ as frecuencia, kadfec as Fecha ";
				$query .= " FROM ".$wbasedatomovhos."_000054,".$wbasedatocenpro."_000002,".$wbasedatomovhos."_000043,".$wbasedatomovhos."_000040 ";
				$query .= " WHERE kadhis = '".$whis."' ";
				$query .= "   AND kading = '".$wing."' ";
				$query .= "   AND kadest = 'on'	";
				$query .= "   AND kadsus = 'off' ";
				$query .= "   AND kadart = artcod ";
				$query .= "   AND kadori = 'CM' ";
				$query .= "   AND kadvia = viacod ";
				$query .= "  AND kadper = percod ";
				$query .= "  AND kadart IN (SELECT Pdepro FROM ".$wbasedatocenpro."_000003, ".$wbasedatocenpro."_000002 WHERE Pdeins like 'MA%' AND pdeest = 'on' AND artcod = Pdepro) ";
				$query .= " order by 1,7 ";
				$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num1 = mysql_num_rows($err1);
				if($num1 > 0)
				{
					$hcekey="";
					$hcedata=array();
					$nl=-1;
					for ($i=0;$i<$num1;$i++)
					{
						$row1 = mysql_fetch_array($err1);
						if($row1[0] != $hcekey)
						{
							if($i > 0)
							{
								$nl++;
								$color=lcolor($nl);
								echo "<tr><td class=".$color.">".$hcedata[0]."</td><td class=".$color.">".$hcedata[1]."</td><td class=".$color.">".$hcedata[2]."</td><td class=".$color.">".$hcedata[3]."</td><td class=".$color.">".$hcedata[4]."</td><td class=".$color.">".$hcedata[5]."</td><td class=".$color.">".$hcedata[6]."</td><td class=".$color.">".$hcedata[7]."</td></tr>";
							}
							$hcedata[0]=$row1[0];
							$hcedata[1]=$row1[1];
							$hcedata[2]=$row1[2];
							$hcedata[3]=$row1[3];
							$hcedata[4]=$row1[4];
							$hcedata[5]=$row1[5];
							$hcedata[6]=$row1[6];
							if(isset($wfant))
								$hcedata[7]=$wfant;
							else
								$hcedata[7]=$row1[6];
							$wfant=$row1[6];
							$hcekey=$row1[0];
						}
						if($wfant != $row1[6])
						{
							$hcedata[7]=$wfant;
							$wfechaf = strtotime ('+1 day' , strtotime ($wfant)) ;
							$wfechaf = date ('Y-m-d' , $wfechaf);
							if($wfechaf != $row1[6])
							{
								$nl++;
								$color=lcolor($nl);
								echo "<tr><td class=".$color.">".$hcedata[0]."</td><td class=".$color.">".$hcedata[1]."</td><td class=".$color.">".$hcedata[2]."</td><td class=".$color.">".$hcedata[3]."</td><td class=".$color.">".$hcedata[4]."</td><td class=".$color.">".$hcedata[5]."</td><td class=".$color.">".$hcedata[6]."</td><td class=".$color.">".$hcedata[7]."</td></tr>";
							}
							$wfant=$row1[6];
						}
					}
					$hcedata[7]=$wfant;
					$nl++;
					$color=lcolor($nl);
					echo "<tr><td class=".$color.">".$hcedata[0]."</td><td class=".$color.">".$hcedata[1]."</td><td class=".$color.">".$hcedata[2]."</td><td class=".$color.">".$hcedata[3]."</td><td class=".$color.">".$hcedata[4]."</td><td class=".$color.">".$hcedata[5]."</td><td class=".$color.">".$hcedata[6]."</td><td class=".$color.">".$hcedata[7]."</td></tr>";
				}
				echo "</table>";
				echo "<h3 OnClick='toggleDisplay(div7)' class=tipo3>EVOLUCIONES POR ESPECIALIDAD (click)</h3>";
				echo "<center><table align=center border=1 class=tipoTABLEGRID CELLPADDING=3 id='div7' style='display: none'>";
				echo "<tr><td id=tipoL07GRID>ESPECIALIDAD</td></tr>";
				//                 0      1
				$query = "select Firrol,Espnom ";
				$query .= "   from ".$empresa."_000367,".$empresa."_000036,".$wbasedatomovhos."_000044 ";
				$query .= "    where movhis = '".$whis."' ";
				$query .= " 	 and moving = '".$wing."' ";   
				$query .= " 	 and Firpro = '000367' ";
				$query .= " 	 and ".$empresa."_000367.Fecha_data = ".$empresa."_000036.Fecha_data ";
				$query .= " 	 and ".$empresa."_000367.Hora_data = ".$empresa."_000036.hora_data ";
				$query .= " 	 and movhis = firhis ";
				$query .= " 	 and moving = firing ";
				$query .= " 	 and Firrol = Espcod ";
				$query .= "  group by 1 ";
				$query .= "  order by 1 ";
				$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num1 = mysql_num_rows($err1);
				if($num1 > 0)
				{
					for ($i=0;$i<$num1;$i++)
					{
						if($i % 2 == 0)
							$color = $gridcolor1;
						else
							$color = $gridcolor2;
						$row1 = mysql_fetch_array($err1);
						echo "<tr><td class=".$color."  OnClick='toggleDisplay(div7".$i.")'>".$row1[1]."</td></tr>";
						echo "<tr id='div7".$i."' style='display: none'><td colspan=3><center><table align=center border=1 class=tipoTABLEGRID>";
						$query = "select ".$empresa."_000367.Fecha_data,".$empresa."_000367.Hora_data,Detdes,movdat ";
						$query .= "   from ".$empresa."_000367,".$empresa."_000002,".$empresa."_000036 ";
						$query .= "    where movhis = '".$whis."' "; 
						$query .= " 	 and moving = '".$wing."' "; 
						$query .= " 	 and movcon in (62,7,65,53) ";
						$query .= " 	 and Detpro = '000367' ";
						$query .= " 	 and movcon = Detcon ";
						$query .= " 	 and Firpro = '000367' ";
						$query .= " 	 and ".$empresa."_000367.Fecha_data = ".$empresa."_000036.Fecha_data ";
						$query .= " 	 and ".$empresa."_000367.Hora_data = ".$empresa."_000036.hora_data ";
						$query .= " 	 and movhis = firhis ";
						$query .= " 	 and moving = firing ";
						$query .= " 	 and Firrol = '".$row1[0]."'";
						$query .= "  order by 1 desc, 2 desc,Detorp ";
						$err2 = mysql_query($query,$conex);
						$num2 = mysql_num_rows($err2);
						$keyevo="";
						echo "<tr><td id=tipoL06GRID>PARAMETRO</td><td id=tipoL06GRID>DATO</td></tr>";
						for ($j=0;$j<$num2;$j++)
						{
							$row2 = mysql_fetch_array($err2);
							if($keyevo != $row2[0].$row2[1])
							{
								echo "<tr><td colspan=2 id=tipoL07GRID>".$row2[0]." ".$row2[1]."</td></tr>";
								$keyevo = $row2[0].$row2[1];
							}
							if($j % 2 == 0)
								$color = $gridcolor1;
							else
								$color = $gridcolor2;
							echo"<tr><td class=".$color.">".$row2[2]."</td><td  class=".$color.">".$row2[3]."</td></tr>";
						}
						echo "</table></center>";
						echo "</td></tr>";
					}
				}
				echo"</form>";
			break;
		}
	}		
}
?>
</body>
</html>
