<html>
<head>
  <title>MATRIX</title>
  <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
    	#tipo1{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.tipo3R{color:#000066;background:#DDDDDD;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;border-style:inset;}
    	
  </style>
  <script type="text/javascript">
	<!--
	function teclado(e)  
	{ 
		var navegador = navigator.appName;
		var version = navigator.appVersion;
		if(navegador.substring(0,9) == "Microsoft")
		{
			if (event.keyCode < 48 || event.keyCode > 57  || event.keyCode == 13) event.returnValue = false;
		}
		else
		{
			//alert(e.which);
			//return ((e.which >= 48 && e.which <= 57  || e.which == 13 || e.which < 32) && e.which != 46); //Solo números
			return (e.which >= 48 && e.which <= 57  || e.which == 13); //Solo números
			//return ((e.which < 48 || e.which > 57  || e.which == 13) && e.which != 46);
		}
	}
	function enter()
	{
		document.forms.Servicios.submit();
	}
	//-->
	</script>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Ingreso de Horas y Servicios Prestados</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro155.php Ver. 2014-05-09</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form  name='Servicios' action='000001_pro155.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		echo "<center><input type='HIDDEN' name= 'ODBC' value='".$ODBC."'>";
		if(!isset($wanop) or !isset($wper) or $wper < 1 or $wper > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>INGRESO DE HORAS Y SERVICIOS PRESTADOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			if(isset($ok))
			{
				$error="";
				$wsuma=0;
				$wsumacol=0;
				
				$query = "delete from ".$empresa."_000072 ";
				$query = $query."  where Mseano = ".$wanop;
				$query = $query."    and Msemes = ".$wper;
				$query = $query."    and Mseusu = '".$key."'";
				$err2 = mysql_query($query,$conex);
				
				$query = "delete from ".$empresa."_000145 ";
				$query = $query."  where Mhsano = ".$wanop;
				$query = $query."    and Mhsmes = ".$wper;
				$query = $query."    and Mhsemp = '".$key."'";
				$err2 = mysql_query($query,$conex);
				
				for ($i=0;$i<$numS;$i++)
				{
					switch ($servicios[$i][2])
					{
						case "HORAS":
							$wsuma += $HorasT[$i];
							for ($j=0;$j<$numF;$j++)
							{
								$wsumacol += $Horas[$j][$i];
								if($Horas[$j][$i] > 0)
								{
									$fecha = date("Y-m-d");
									$hora = (string)date("H:i:s");
									$query = "insert ".$empresa."_000072 (medico,fecha_data,hora_data,Mseano,Msemes,Msecco,Mseccd,Msecod,Msecan,Msetip,Mseusu,seguridad) values ('".$empresa."','".$fecha."','".$hora."',".$wanop.",".$wper.",'".$servicios[$i][0]."','".$cc[$j]."','".$servicios[$i][1]."',".$Horas[$j][$i].",'R','".$key."','C-".$empresa."')";
									$err1 = mysql_query($query,$conex) or die("Error en la Insercion del Movimiento de Tabla 72");
								}
							}
						break;
						case "SERVICIO":
							for ($j=0;$j<$numF;$j++)
							{
								$wsumacol += $Horas[$j][$i];
								if($Horas[$j][$i] > 0)
								{
									$fecha = date("Y-m-d");
									$hora = (string)date("H:i:s");
									$query = "insert ".$empresa."_000072 (medico,fecha_data,hora_data,Mseano,Msemes,Msecco,Mseccd,Msecod,Msecan,Msetip,Mseusu,seguridad) values ('".$empresa."','".$fecha."','".$hora."',".$wanop.",".$wper.",'".$servicios[$i][0]."','".$cc[$j]."','".$servicios[$i][1]."',".$Horas[$j][$i].",'R','".$key."','C-".$empresa."')";
									$err1 = mysql_query($query,$conex) or die("Error en la Insercion del Movimiento de Tabla 72");
								}
							}
						break;
						default:
							$wsuma += $HorasT[$i];
							$fecha = date("Y-m-d");
							$hora = (string)date("H:i:s");
							$query = "insert ".$empresa."_000145 (medico,fecha_data,hora_data,Mhsano,Mhsmes,Mhscco,Mhsemp,Mhsser,Mhshot,seguridad) values ('".$empresa."','".$fecha."','".$hora."',".$wanop.",".$wper.",'".$servicios[$i][0]."','".$key."','".$servicios[$i][1]."',".$HorasT[$i].",'C-".$empresa."')";
							$err1 = mysql_query($query,$conex) or die("Error en la Insercion del Movimiento de Tabla 145");
						break;
					}
				}
				if($wsuma != $HorasG)
					$error .= "LA SUMA PARCIAL DE LAS HORAS TOTALES NO COINCIDE CON EL TOTAL DE HORAS<br>";
			}
			
			$servicios=array();
			if(!isset($HorasT[0]))
				$HorasT=array();
			$query = "select Msecco, Mseser, Msetip, Serdes, Cconom, Mseusu, Descripcion from ".$empresa."_000144, ".$empresa."_000070, ".$empresa."_000005, usuarios  ";
			$query .= "   where Mseusu = '".$key."' ";
  			$query .= "     and Msetes = 'on' ";
  			$query .= "     and Mseser = Sercod ";
  			$query .= "     and Msecco = ccocod ";
  			$query .= "     and Mseusu = Codigo ";
 			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$servicios[$i][0] = $row[0];
					$servicios[$i][1] = $row[1];
					$servicios[$i][2] = $row[2];
					$servicios[$i][3] = $row[3];
					$servicios[$i][4] = $row[4];
					$servicios[$i][5] = $row[5];
					$servicios[$i][6] = $row[6];
					if(!isset($HorasT[$i]))
					{
						$HorasT[$i]=0;
						if($servicios[$i][2] != "SERVICIO" and $servicios[$i][2] != "HORAS")
						{
							$query = "select Mhshot from ".$empresa."_000145  ";
							$query .= "   where Mhsano = ".$wanop;
							$query .= "     and Mhsmes = ".$wper;
							$query .= "     and Mhscco = '".$servicios[$i][0]."' ";
							$query .= "     and Mhsemp = '".$key."' ";
							$query .= "     and Mhsser = '".$servicios[$i][1]."' ";
							$err1 = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
							$num1 = mysql_num_rows($err1);
							if($num1 > 0)
							{
								$row1 = mysql_fetch_array($err1);
								$HorasT[$i] = $row1[0];
							}
							else
							{
								$HorasT[$i] = 0;
							}
						}
					}
					else
					{
						if($servicios[$i][2] == "SERVICIO")
						{
							$HorasT[$i]=0;
							for ($j=0;$j<$numF;$j++)
								$HorasT[$i] += $Horas[$j][$i];
						}
					}
				}
			}
			$numS=$num;
			if($numS > 0)
			{
				if(strlen($key) > 5)
					$codnom=substr($key,strlen($key)-5);
				else
					$codnom=$key;
				$conex_o = odbc_connect($ODBC,'','') or die("No se ralizo Conexion");
				$query = "select perhco from noper ";
				$query = $query."  where percod = '".$codnom."'";
				$err1 = odbc_do($conex_o,$query);
				$campos1 = odbc_num_fields($err1);
				if (odbc_fetch_row($err1))
				{
					$row1=array();
					for($i=1;$i<=$campos1;$i++)
					{
						$row1[$i-1]=odbc_result($err1,$i);
					}
				}
				$HorasCont=$row1[0];
				$query = "select Perfes,Perdom from ".$empresa."_000040  ";
				$query .= "   where Perper = ".$wper;
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if($num > 0)
				{
					$row = mysql_fetch_array($err);
					$nfes = $row[0];
					$ndom = $row[1];
				}
				else
				{
					$nfes = 0;
					$ndom = 0;
				}
				$Hbas=0;
				$Hext=0;
				$query = "select Norcod,Norhor from ".$empresa."_000036  ";
				$query .= "   where Norano = ".$wanop;
				$query .= "     and Norper = ".$wper;
				$query .= "     and Noremp = '".$codnom."'";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if($num > 0)
				{
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						if($row[0] == "0001")
							$Hbas = $row[1];
						elseif($row[0] == "0003" or $row[0] == "0004" or $row[0] == "0005" or $row[0] == "0006")
							$Hext += $row[1];
					}
				}
				//echo "h basicas : ".$Hbas." Hcont : ".$HorasCont." fes : ".$nfes. " dom : ".$ndom. " Ext : ".$Hext."<br>";
				$HorasG = (integer)($Hbas - (($nfes+$ndom)*8)*($HorasCont/240) + $Hext);
				if(!isset($Horas[0][0]))
				{
					$valores=array();
					$query  = "select Mseccd,Msecod,Msecan from ".$empresa."_000072  ";
					$query .= "   where Mseano = ".$wanop;
					$query .= "     and Msemes = ".$wper;
					$query .= "     and Mseusu = '".$key."' ";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					if($num > 0)
					{
						for($i=0;$i<$num;$i++)
						{
							$row = mysql_fetch_array($err);
							$valores[$row[0].$row[1]]=$row[2];
						}
					}
					$ncol = 3 + $numS;
					$Horas=array();
					$query = "select Ccocod, Cconom from ".$empresa."_000005  ";
					$query .= "   where Ccoest = 'on' ";
					$query .= " Order by 2 "; 
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						for ($j=0;$j<$numS;$j++)
						{
							if(isset($valores[$row[0].$servicios[$j][1]]))
							{
								$Horas[$i][$j]=$valores[$row[0].$servicios[$j][1]];
							}
							else
							{
								$Horas[$i][$j]=0;
							}
						}
					}
				}
				else
				{
					$query = "select count(*) from ".$empresa."_000005  ";
					$query .= "   where Ccoest = 'on' ";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					$row = mysql_fetch_array($err);
					$nfil = $row[0];
					$ncol = 3 + $numS;
				}
				if(isset($error) and $error != "")
					echo $error."<br><br>";
				echo "<center><table border=0>";
				echo "<tr><td align=center colspan=".$ncol."><font size=2>PROMOTORA MEDICA LAS AMERICAS S.A.</font></td></tr>";
				echo "<tr><td align=center colspan=".$ncol."><font size=2>DIRECCION DE INFORMATICA</font></td></tr>";
				echo "<tr><td align=center colspan=".$ncol."><font size=2>INGRESO DE HORAS Y SERVICIOS PRESTADOS</font></td></tr>";
				echo "<tr><td align=center bgcolor=#999999 colspan=".$ncol."><font size=2><b>A&Ntilde;O : ".$wanop." MES : ".$wper." </b></font></td></tr>";
				echo "<tr><td align=center bgcolor=#999999 colspan=".$ncol."><font size=2><b>CENTROS DE COSTOS : ".$servicios[0][0]."-".$servicios[0][4]."</b></font></td></tr>";
				echo "<tr><td align=center bgcolor=#999999 colspan=".$ncol."><font size=2><b>USUARIO: ".$servicios[0][5]."-".$servicios[0][6]."</b></font></td></tr>";
				echo "<tr><td id=tipo1 colspan=2><b>UNIDADES</b></td>";
				for ($i=0;$i<$numS;$i++)
				{
					echo "<td id=tipo1><b>".$servicios[$i][1]."<BR>".$servicios[$i][3]."</b></td>";
					echo "<input type='HIDDEN' name= 'servicios[".$i."][0]' value='".$servicios[$i][0]."'>";
					echo "<input type='HIDDEN' name= 'servicios[".$i."][1]' value='".$servicios[$i][1]."'>";
					echo "<input type='HIDDEN' name= 'servicios[".$i."][2]' value='".$servicios[$i][2]."'>";
					echo "<input type='HIDDEN' name= 'servicios[".$i."][3]' value='".$servicios[$i][3]."'>";
				}
				echo "<input type='HIDDEN' name= 'numS' value='".$numS."'>";
				echo "<td id=tipo1><b>TOTAL HORAS</b></td>";
				echo "</tr>";
				echo "<tr><td id=tipo1><b>C. DE C.</b></td><td id=tipo1><b>NOMBRE</b></td>";
				
				for ($i=0;$i<$numS;$i++)
				{
					switch ($servicios[$i][2])
					{
						case "HORAS":
							$HorasT[$i] = 0;
							for ($j=0;$j<$numF;$j++)
								$HorasT[$i] += $Horas[$j][$i];
						break;
					}
				}
				
				for ($i=0;$i<$numS;$i++)
				{
					switch ($servicios[$i][2])
					{
						case "HORAS":
							echo "<td id=tipo1><input type='TEXT' name='HorasT[".$i."]' value=".$HorasT[$i]." size=3 maxlength=3 onkeypress='return teclado(event)'></td>";
						break;
						case "SERVICIO":
							echo "<td id=tipo1><input type='TEXT' name='HorasT[".$i."]' value=".number_format((double)$HorasT[$i],0,'.',',')." readonly='readonly' size=10 maxlength=10  class=tipo3R></td>";
						break;
						default:
							echo "<td id=tipo1><input type='TEXT' name='HorasT[".$i."]' value=".$HorasT[$i]." size=3 maxlength=3 onkeypress='return teclado(event)'></td>";
						break;
					}
				}
				echo "<td id=tipo1><input type='TEXT' name='HorasG' size=3 value=".$HorasG." readonly='readonly' maxlength=3 class=tipo3R></td>";
				echo "</tr>";
				echo "<input type='HIDDEN' name= 'HorasG' value='".$HorasG."'>";
				
				//                  0      1       
				$query  = "select Ccocod, Cconom  from ".$empresa."_000005  ";
				$query .= " where Ccoest = 'on'";
				$query .= " Order by 2 ";  
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				$wdat=array();
				$k=-1;
				echo "<input type='HIDDEN' name= 'wper' value='".$wper."'>";
				echo "<input type='HIDDEN' name= 'wanop' value='".$wanop."'>";
				echo "<input type='HIDDEN' name= 'numF' value='".$num."'>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					if($i % 2 == 0)
						$color="#99CCFF";
					else
						$color="#ffffff";
					echo "<input type='HIDDEN' name= 'cc[".$i."]' value='".$row[0]."'>";
					echo "<tr><td bgcolor=".$color."><font size=2>".$row[0]."</font></td><td bgcolor=".$color."><font size=2>".$row[1]."</font></td>";
					for ($j=0;$j<$numS;$j++)
					{
						switch ($servicios[$j][2])
						{
							case "HORAS":
								echo "<td bgcolor=".$color." align=center><input type='TEXT' name='Horas[".$i."][".$j."]' value=".$Horas[$i][$j]." size=3 maxlength=3 onkeypress='return teclado(event)' onBlur='enter()'></td>";
							break;
							case "SERVICIO":
								echo "<td bgcolor=".$color." align=center><input type='TEXT' name='Horas[".$i."][".$j."]' value=".$Horas[$i][$j]." size=10 maxlength=10 onkeypress='return teclado(event)' onBlur='enter()'></td>";
							break;
							default:
								echo "<td bgcolor=".$color." align=center><input type='TEXT' name='Horas[".$i."][".$j."]' value=".$Horas[$i][$j]." size=3 maxlength=3 readonly='readonly' class=tipo3R></td>";
							break;
						}
					}
					echo "<td bgcolor=".$color." align=right></td>";
					echo "</tr>";
				}
				echo "<input type='HIDDEN' name= 'num' value='".$k."'>";
				echo "<tr><td align=center colspan=56 bgcolor=#999999><b>GRABAR</b><input type='checkbox' name='ok' onclick='enter()'></td></tr>";
				echo "</table></center>";
			}
		}
	}
?>
</body>
</html>
