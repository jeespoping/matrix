<html>
<head>
  	<title>MATRIX Mantenimiento Base De Datos</title>
  	<style type="text/css">
		.tipoTABLE{font-family:Arial;border-style:solid;border-collapse:collapse;}
		#tipoT01L{color:#000066;background:#C3D9FF;font-size:9pt;font-family:Arial;font-weight:bold;text-align:left;}
		#tipoT01C{color:#000066;background:#C3D9FF;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
		#tipoT01R{color:#000066;background:#C3D9FF;font-size:9pt;font-family:Arial;font-weight:bold;text-align:right;}
		#tipoT02L{color:#000066;background:#E8EEF7;font-size:9pt;font-family:Arial;font-weight:bold;text-align:left;}
		#tipoT02C{color:#000066;background:#E8EEF7;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
		#tipoT02R{color:#000066;background:#E8EEF7;font-size:9pt;font-family:Arial;font-weight:bold;text-align:right;}
		#tipoT03L{color:#000066;background:#DDDDDD;font-size:10pt;font-family:Arial;font-weight:bold;text-align:left;}
		#tipoT03C{color:#000066;background:#DDDDDD;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;}
		#tipoT03R{color:#000066;background:#DDDDDD;font-size:10pt;font-family:Arial;font-weight:bold;text-align:right;}
		#tipoT04C{color:#000066;background:#CCCCCC;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}
		#tipoT05R{color:#000066;background:#F5A9A9;font-size:9pt;font-family:Arial;font-weight:bold;text-align:right;}
		#tipoT06C{color:#000066;background:#FFFFFF;font-size:10pt;font-family:Arial;font-weight:normal;text-align:center;}
		#tipoT06R{color:#000066;background:#FFFFFF;font-size:10pt;font-family:Arial;font-weight:normal;text-align:right;}
	</style>
</head>
<body BGCOLOR="FFFFFF" oncontextmenu = "return false" onselectstart = "return true" ondragstart = "return false">
<script type="text/javascript">
<!--
	function enter()
	{
		document.forms.clean.submit();
	}
//-->
</script>
<BODY TEXT="#000066">
<?php
include_once("conex.php");
function bi($d,$n,$k,$i)
{
	$n--;
	if($n > 0)
	{
		$li=0;
		$ls=$n;
		while ($ls - $li > 1)
		{
			$lm=(integer)(($li + $ls) / 2);
			if(strtoupper($k) == strtoupper($d[$lm][$i]))
				return $lm;
			elseif(strtoupper($k) < strtoupper($d[$lm][$i]))
						$ls=$lm;
					else
						$li=$lm;
		}
		if(strtoupper($k) == strtoupper($d[$li][$i]))
			return $li;
		elseif(strtoupper($k) == strtoupper($d[$ls][$i]))
					return $ls;
				else
					return -1;
	}
	else
		return -1;
}

/**********************************************************************************************************************  
	   PROGRAMA : clean.php
	   Fecha de Liberación : 2006-04-07
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2013-09-22
	   
	   OBJETIVO GENERAL : 
	   Este programa permite deterninar que formularios (Tablas) tienen descripcion en las tablas de Formulario y Det_Formulario y no existen
	   Fisicamente.
	   
	   
	   REGISTRO DE MODIFICACIONES :
		.2013-09-22
	   		En el comando SHOW TABLE STATUS se cambia el formato de impresion de los datos numericos
		
		.2013-08-03
	   		Se implementa el comando SHOW TABLE STATUS para mirar los tamaños y tablas de la base de datos
	   		
		.2013-07-02
	   		Se implementa el comando FLUSH QUERY CACHE para inicializar el cache de querys.
	   		
		.2006-04-07
	   		Inicio del Programa a Produccion.

	   
***********************************************************************************************************************/
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='clean' action='clean.php' method=post>";
	

	

	if(isset($ok1))
	{
		for ($i=0;$i<$num;$i++)
		{
			if(isset($del[$i]))
			{
				$query = "kill ".$id[$i];
				$err1 = mysql_query($query,$conex) or die("ERROR CANCELANDO PROCESO");
			}
		}
		unset($ok1);
	}
	if(isset($ok))
	{
		for ($i=0;$i<$k;$i++)
		{
			if(isset($fac[$i]))
			{
				$query = "Delete from formulario where medico = '".$data[$i][0]."' and  codigo = '".$data[$i][1]."'";
				$err1 = mysql_query($query,$conex) or die("ERROR BORRANDO EN FORMULARIO");
				$query = "Delete from det_formulario where medico = '".$data[$i][0]."' and  codigo = '".$data[$i][1]."'";
				$err1 = mysql_query($query,$conex) or die("ERROR BORRANDO EN DETALLE DE FORMULARIO");
				$query = "Delete from root_000030 where Dic_Usuario = '".$data[$i][0]."' and  Dic_Formulario = '".$data[$i][1]."'";
				$err1 = mysql_query($query,$conex) or die("ERROR BORRANDO EN DICIONARIO DE DATOS");
			}
		}
		unset($ok);
		unset($wtip);
	}
	if (!isset($wtip))
	{
		$wcolor="#cccccc";
		echo "<table border=0 align=center>";
		echo "<tr><td align=center bgcolor=#000066 colspan=1><font color=#ffffff size=6><b>MANTENIMIENTO  BASE DE DATOS</font><font color=#33CCFF size=4>&nbsp&nbsp&nbspVer. 2013-09-22</font></b></font></td></tr>";
		echo "<tr><td align=center bgcolor=".$wcolor."><input type='RADIO' name=wtip value=1 checked onclick='enter()'>Formularios que No Existen Fisicamente <input type='RADIO' name=wtip value=2 onclick='enter()'> Formularios Sin Indice Unico<br><input type='RADIO' name=wtip value=3 onclick='enter()'> Lista de Procesos Corriendo en la Base de Datos<input type='RADIO' name=wtip value=4 onclick='enter()'><font color='#FF0000'><b> Defragmentar Cache de Querys </b></font><br><input type='RADIO' name=wtip value=5 onclick='enter()'>Status de la Base de Datos</td></td></tr>";
		 echo "</table>";  
	}
	else
	{
		switch ($wtip)
		{
			case 1:
				$query = "show tables ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				$tables=array();
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$tables[$i][0]=$row[0];
				}
				$tot=$num;
				$wcolor="#cccccc";
				echo "<table border=0 align=center>";
		 		echo "<tr><td align=right colspan=2><font size=2>Powered by :  MATRIX</font></td></tr>";
				echo "<tr><td align=center bgcolor=#000066 colspan=1><font color=#ffffff size=6><b>MANTENIMIENTO  BASE DE DATOS</font></b></font></td></tr>";
				echo "<tr><td  align=center bgcolor=".$wcolor.">Formularios que No Existen Fisicamente</td></tr>";
				echo "</table><br><br>";  
				echo "<table border=0 align=center>";
				echo "<tr><td align=center bgcolor=#999999 colspan=4><font color=#000066 size=3><b>DETALLE DE FORMULARIOS</b></font></td></tr>";
				echo "<tr><td align=center bgcolor=#000066><font color=#ffffff >BORRAR</font></td><td align=center bgcolor=#000066><font color=#ffffff >USUARIO</font></td><td align=center bgcolor=#000066><font color=#ffffff >CODIGO</font></td><td align=center bgcolor=#000066><font color=#ffffff >DESCRIPCION</font></td></tr>";
		 		echo "<tr><td align=center bgcolor=#999999 colspan=4><font color=#000066 size=3><b>Marcar Todos</b><input type='checkbox' name='all'  onclick='enter()'></font></td></tr>";
				if(!isset($dta))
		 		{
			 		$dta=0;
					$query = "select medico, codigo, nombre from  formulario ";
					$query .= "      order by medico,codigo ";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					if($num > 0)
					{
						$data=array();
						$dta=1;
						$k=-1;
						for ($i=0;$i<$num;$i++)
						{
							$row = mysql_fetch_array($err);
							if(bi($tables,$tot,$row[0]."_".$row[1],0) < 0)
							{
								$k +=1;
								$data[$k][0]=$row[0];
								$data[$k][1]=$row[1];
								$data[$k][2]=$row[2];
							}
						}
					}
				}
				if($dta == 1)
				{
					for ($i=0;$i<$k;$i++)
					{
						if($i % 2 == 0)
							$color="#dddddd";
						else
							$color="#cccccc";
						echo "<tr>";
						if(isset($fac[$i]) or isset($all))
							echo "<td bgcolor=".$color." align=center><input type='checkbox' name='fac[".$i."]' checked></td>";
						else
							echo "<td bgcolor=".$color." align=center><input type='checkbox' name='fac[".$i."]'></td>";
						echo "<td bgcolor=".$color.">".$data[$i][0]."</td>";	
						echo "<td bgcolor=".$color.">".$data[$i][1]."</td>";	
						echo "<td bgcolor=".$color.">".$data[$i][2]."</td>";
						echo "</tr>";
						$query = "select Campo, Descripcion, Tipo from  det_formulario where medico = '".$data[$i][0]."' and  codigo = '".$data[$i][1]."' order by campo";
						$err = mysql_query($query,$conex);
						$num = mysql_num_rows($err);
						if($num > 0)
						{
							for ($j=0;$j<$num;$j++)
							{
								$row = mysql_fetch_array($err);
								$color="#99CCFF";
								echo "<tr>";
								echo "<td bgcolor=".$color." align=center>&nbsp </td>";
								echo "<td bgcolor=".$color.">".$row[0]."</td>";	
								echo "<td bgcolor=".$color.">".$row[1]."</td>";
								switch ($row[2])
								{
									case "0":
										$row[2]="Alfanumerico";
										break;
									case "1":
										$row[2]="Entero";
										break;	
									case "2":
										$row[2]="Real";;
										break;
									case "3":
										$row[2]="Fecha";
										break;
									case "4":
										$row[2]="Texto";
										break;
									case "5":
										$row[2]="Seleccion";
										break;
									case "6":
										$row[2]="Formula";
										break;
									case "7":
										$row[2]="Grafico";
										break;
									case "8":
										$row[2]="Automatico";
										break;
									case "9":
										$row[2]="Relacion";
										break;
									case "10":
										$row[2]="Booleano";
										break;
									case "11":
										$row[2]="Hora";
										break;
									case "12":
										$row[2]="Algoritmico";
										break;
									case "13":
										$row[2]="Titulo";
										break;
									case "14":
										$row[2]="Hipervinculo";
										break;
									case "15":
										$row[2]="Algoritmico_M";
										break;
									case "16":
										$row[2]="Protegido";
										break;
									case "17":
										$row[2]="Auxiliar";
										break;
									case "18":
										$row[2]="Relacion_NE";
										break;
								}	
								echo "<td bgcolor=".$color.">".$row[2]."</td>";
								echo "</tr>";
							}
						}
						echo "<input type='HIDDEN' name= 'wtip' value=".$wtip.">";
						echo "<input type='HIDDEN' name= 'dta' value=".$dta.">";
						echo "<input type='HIDDEN' name= 'k' value=".$k.">";
						echo "<input type='HIDDEN' name= 'data[".$i."][0]' value='".$data[$i][0]."'>";
						echo "<input type='HIDDEN' name= 'data[".$i."][1]' value='".$data[$i][1]."'>";
						echo "<input type='HIDDEN' name= 'data[".$i."][2]' value='".$data[$i][2]."'>";
					}
					echo "<tr><td  bgcolor=#999999 colspan=4><b>TOTAL FORMULARIOS  : </b>".number_format((double)$k,0,'.',',')."</b></td></tr>"; 
					echo "<tr><td align=center bgcolor=#dddddd colspan=4><font color=#000066 size=5><b>BORRAR</b><input type='checkbox' name='ok'></font></td></tr>";
					echo "<tr><td align=center bgcolor=#999999 colspan=4><input type='submit' value='Ok'></td></tr>"; 
					echo "</table><br><br>"; 
				}
			break;
			case 2:
				// $query = "show tables ";
				$query = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA='matrix' AND TABLE_ROWS IS NOT NULL;";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				$tables=array();
				$k=-1;
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					// $query = "show index from ".$row[0];echo $query. "<br>";
					$query = "show index from `".$row[0]."`";//echo $query. "<br>";
					$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					$index=array();
					$wsw=0;
					for ($j=0;$j<$num1;$j++)
					{
						$row1 = mysql_fetch_array($err1);
						$index[$j][0]=$row1[0];
						$index[$j][1]=$row1[1];
						$index[$j][2]=$row1[2];
						$index[$j][3]=$row1[4];
						$index[$j][4]=$row1[7];
						if($row1[1] == 0 and $row1[2] !="PRIMARY")
							$wsw=1;
					}
					if($wsw == 0)
					{
						$k +=1;
						if($j == 0)
						{
							$tables[$k][0][0]=$row[0];
							$tables[$k][1][0]=2;
							$tables[$k][2][0]="SIN INDICES";
							$tables[$k][3][0]="";
							$tables[$k][4][0]="";
							$tables[$k][5][0]=1;
						}
						else
						{
							for ($j=0;$j<$num1;$j++)
							{
								$tables[$k][0][$j]=$index[$j][0];
								$tables[$k][1][$j]=$index[$j][1];
								$tables[$k][2][$j]=$index[$j][2];
								$tables[$k][3][$j]=$index[$j][3];
								$tables[$k][4][$j]=$index[$j][4];
								$tables[$k][5][$j]=$num1;
							}
						}
					}
				}
				$wcolor="#cccccc";
				echo "<table border=0 align=center>";
		 		echo "<tr><td align=right colspan=2><font size=2>Powered by :  MATRIX</font></td></tr>";
				echo "<tr><td align=center bgcolor=#000066 colspan=1><font color=#ffffff size=6><b>MANTENIMIENTO  BASE DE DATOS</font></b></font></td></tr>";
				echo "<tr><td  align=center bgcolor=".$wcolor.">Formularios Sin Indice Unico </td></tr>";
				echo "</table><br><br>";  
				echo "<table border=0 align=center>";
				echo "<tr><td align=center bgcolor=#999999 colspan=4><font color=#000066 size=3><b>DETALLE DE FORMULARIOS</b></font></td></tr>";
				echo "<tr><td align=center bgcolor=#000066><font color=#ffffff >INDICE</font></td><td align=center bgcolor=#000066><font color=#ffffff >TIPO<br>INDICE</font></td><td align=center bgcolor=#000066><font color=#ffffff >CAMPO</font></td><td align=center bgcolor=#000066><font color=#ffffff >EXTENSION</font></td></tr>";				
				$wtabant="";
				$widxant="";
				for ($i=0;$i<$k;$i++)
				{
					$color="#dddddd";
					echo "<tr>";
					if($wtabant != $tables[$i][0][0])
					{
						$query = "select nombre from  formulario  where medico = '".substr($tables[$i][0][0],0,strpos($tables[$i][0][0],"_"))."' and  codigo = '".substr($tables[$i][0][0],strpos($tables[$i][0][0],"_")+1)."'";
						$err = mysql_query($query,$conex);
						$row = mysql_fetch_array($err);
						$num = mysql_num_rows($err);
						if($num > 0)
							echo "<td bgcolor=".$color."  colspan=4><b>".$tables[$i][0][0]." - ".$row[0]."</b></td></tr>";
						else
							echo "<td bgcolor=".$color."  colspan=4><b>".$tables[$i][0][0]." - SISTEMA</b></td></tr>";
						echo "<tr>";
						$wtabant=$tables[$i][0][0];
						$wsw=1;
					}
					$color="#99CCFF";
					for ($j=0;$j<$tables[$i][5][0];$j++)
					{
						if($widxant != $tables[$i][2][$j] or $wsw==1)
						{
							echo "<td bgcolor=".$color.">".$tables[$i][2][$j]."</td>";	
							switch ($tables[$i][1][$j])
							{
								case 0:
									echo "<td bgcolor=".$color.">UNICO</td>";
								break;
								case 1:
									echo "<td bgcolor=".$color.">DUPLICADO</td>";
								break;
								case 2:
									echo "<td bgcolor=".$color.">NO DEFINIDO</td>";
								break;	
							}	
							$widxant = $tables[$i][2][$j];
							$wsw=0;
						}
						else
						{
							echo "<td bgcolor=".$color.">&nbsp </td>";
							echo "<td bgcolor=".$color.">&nbsp </td>";
						}
						echo "<td bgcolor=".$color.">".$tables[$i][3][$j]."</td>";
						echo "<td bgcolor=".$color.">".$tables[$i][4][$j]."</td>";
						echo "</tr>";
					}
				}
				echo "<tr><td align=center bgcolor=#999999 colspan=4><font color=#000066 size=3><b>TOTAL FORMULARIOS : ".$k."</b></font></td></tr>";
				echo "</table><br><br>"; 
				unset($wtip);
			break;
			case 3:
				$query = "show full processlist ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				$wcolor="#cccccc";
				echo "<table border=0 align=center>";
		 		echo "<tr><td align=right colspan=2><font size=2>Powered by :  MATRIX</font></td></tr>";
				echo "<tr><td align=center bgcolor=#000066 colspan=1><font color=#ffffff size=6><b>MANTENIMIENTO  BASE DE DATOS</font></b></font></td></tr>";
				echo "<tr><td  align=center bgcolor=".$wcolor."> Lista de Procesos Corriendo en la Base de Datos </td></tr>";
				echo "</table><br><br>";  
				echo "<table border=0 align=center>";
				echo "<tr><td align=center bgcolor=#999999 colspan=7><font color=#000066 size=3><b>DETALLE DE PROCESOS</b></font></td></tr>";
				echo "<tr><td align=center bgcolor=#000066><font color=#ffffff >KILL</font></td><td align=center bgcolor=#000066><font color=#ffffff >ID</font></td><td align=center bgcolor=#000066><font color=#ffffff >BASE DE<BR> DATOS</font></td><td align=center bgcolor=#000066><font color=#ffffff >COMANDO</font></td><td align=center bgcolor=#000066><font color=#ffffff >TIEMPO<br> Segundos</font></td><td align=center bgcolor=#000066><font color=#ffffff >ESTADO</font></td><td align=center bgcolor=#000066><font color=#ffffff >INFORMACION</font></td></tr>";				
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					if($i % 2 == 0)
						$color="#dddddd";
					else
						$color="#cccccc";
					echo "<tr>";
					echo "<td   align=center bgcolor=".$color."><input type='checkbox' name='del[".$i."]'></td>";
					echo "<td   align=center bgcolor=".$color.">".$row[0]."</td>";
					echo "<td   align=center bgcolor=".$color.">".$row[3]."</td>";
					echo "<td   align=center bgcolor=".$color.">".$row[4]."</td>";
					echo "<td   align=center bgcolor=".$color.">".$row[5]."</td>";
					echo "<td bgcolor=".$color.">".$row[6]."</td>";
					echo "<td bgcolor=".$color.">".$row[7]."</td>";
					echo "</tr>";
					echo "<input type='HIDDEN' name= 'num' value=".$num.">";
					echo "<input type='HIDDEN' name= 'id[".$i."]' value='".$row[0]."'>";
				}
				echo "<tr><td align=center bgcolor=#CCCCCC colspan=7><font color=#000066 size=3><b>TOTAL PROCESOS : ".$num."</b></font></td></tr>";
				echo "<tr><td align=center bgcolor=#CCCCCC colspan=7><b>CANCELAR</b><input type='checkbox' name='ok1'></td></tr>";
				echo "<tr><td align=center bgcolor=#999999 colspan=7><input type='submit' value='Ok'></td></tr>";
				echo "</table><br><br>"; 
				unset($wtip);
			break;
			case 4:
				$query = "FLUSH QUERY CACHE";
				$err = mysql_query($query,$conex);
				$query = "show full processlist ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				$wcolor="#cccccc";
				echo "<table border=0 align=center>";
		 		echo "<tr><td align=right colspan=2><font size=2>Powered by :  MATRIX</font></td></tr>";
				echo "<tr><td align=center bgcolor=#000066 colspan=1><font color=#ffffff size=6><b>MANTENIMIENTO  BASE DE DATOS</font></b></font></td></tr>";
				echo "<tr><td  align=center bgcolor=".$wcolor."> Lista de Procesos Corriendo en la Base de Datos </td></tr>";
				echo "</table><br><br>";  
				echo "<table border=0 align=center>";
				echo "<tr><td align=center bgcolor=#999999 colspan=7><font color=#000066 size=3><b>DETALLE DE PROCESOS</b></font></td></tr>";
				echo "<tr><td align=center bgcolor=#000066><font color=#ffffff >KILL</font></td><td align=center bgcolor=#000066><font color=#ffffff >ID</font></td><td align=center bgcolor=#000066><font color=#ffffff >BASE DE<BR> DATOS</font></td><td align=center bgcolor=#000066><font color=#ffffff >COMANDO</font></td><td align=center bgcolor=#000066><font color=#ffffff >TIEMPO<br> Segundos</font></td><td align=center bgcolor=#000066><font color=#ffffff >ESTADO</font></td><td align=center bgcolor=#000066><font color=#ffffff >INFORMACION</font></td></tr>";				
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					if($i % 2 == 0)
						$color="#dddddd";
					else
						$color="#cccccc";
					echo "<tr>";
					echo "<td   align=center bgcolor=".$color."><input type='checkbox' name='del[".$i."]'></td>";
					echo "<td   align=center bgcolor=".$color.">".$row[0]."</td>";
					echo "<td   align=center bgcolor=".$color.">".$row[3]."</td>";
					echo "<td   align=center bgcolor=".$color.">".$row[4]."</td>";
					echo "<td   align=center bgcolor=".$color.">".$row[5]."</td>";
					echo "<td bgcolor=".$color.">".$row[6]."</td>";
					echo "<td bgcolor=".$color.">".$row[7]."</td>";
					echo "</tr>";
					echo "<input type='HIDDEN' name= 'num' value=".$num.">";
					echo "<input type='HIDDEN' name= 'id[".$i."]' value='".$row[0]."'>";
				}
				echo "<tr><td align=center bgcolor=#CCCCCC colspan=7><font color=#000066 size=3><b>TOTAL PROCESOS : ".$num."</b></font></td></tr>";
				echo "<tr><td align=center bgcolor=#CCCCCC colspan=7><b>CANCELAR</b><input type='checkbox' name='ok1'></td></tr>";
				echo "<tr><td align=center bgcolor=#999999 colspan=7><input type='submit' value='Ok'></td></tr>";
				echo "</table><br><br>"; 
				unset($wtip);
			break;
			case 5:
				$tfil=0;
				$tram=0;
				$ram=array();
				$ram[0]="bytes";
				$ram[1]="KB";
				$ram[2]="MB";
				$ram[3]="GB";
				$ram[4]="TB";
				$query = "show table status ";
				$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num1 = mysql_num_rows($err1);
				if($num1 > 0)
				{
					echo "<center><table border=1 class='tipoTABLE'>";
					echo "<tr><td colspan=4 id='tipoT04C'>STATUS: BASE DE DATOS MATRIX ".date("Y-m-d")."</td></tr>";
					echo "<tr><td id='tipoT03C'>INDICE</td><td id='tipoT03C'>NOMBRE<br>TABLA</td><td id='tipoT03C'>NUMERO DE<br>FILAS</td><td id='tipoT03C'>TAMA&Ntilde;O</td></tr>";
					for ($i=0;$i<$num1;$i++)
					{
						$j=0;
						$row1 = mysql_fetch_array($err1);
						$dim=$row1[6]+$row1[8];
						$tfil += $row1[4];
						$tramA = $tram;
						$tram += $dim;
						$DIF = $tram - $tramA;
						while($dim > 1024)
						{
							$j++;
							$dim=$dim/1024;
						}
						$z=$i+1;
						if($z % 2 == 0)
							$color="tipoT01";
						else
							$color="tipoT02";
						if(($j == 2 and $dim > 500) or $j > 2)
							$color1="tipoT05";
						else
							$color1=$color;
						echo "<tr><td id=".$color."C>".$z."</td><td id=".$color."L>".$row1[0]."</td><td id=".$color."R>".number_format((double)$row1[4],0,',','.')."</td><td id=".$color1."R>".number_format((double)$dim,1,'.',',')." ".$ram[$j]."</td></tr>";
					}
					$vm=0;
					$j=0;
					while($tram > 1024)
					{
						if($j == 2)
							$vm = $tram;
						$j++;
						$tram=$tram/1024;
					}
					echo "<tr><td id='tipoT06C'>TOTAL GENERAL</td><td id='tipoT06R'>".number_format((double)$vm,2,',','.')." ".$ram[2]."</td><td id='tipoT06R'>".number_format((double)$tfil,0,',','.')."</td><td id='tipoT06R'>".number_format((double)$tram,1,'.',',')." ".$ram[$j]."</td></tr>";
					echo "</table></center>";
				}
				unset($wtip);
			break;
		}
	}
}
?>
