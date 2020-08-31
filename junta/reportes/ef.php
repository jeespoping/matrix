<html>
<head>
  	<title>MATRIX ESTADOS FINANCIEROS</title>
  	      <link rel="stylesheet" href="/styles.css" type="text/css">
	<style type="text/css">
	<!--
		.BlueThing
		{
			background: #99CCFF;
		}
		
		.SilverThing
		{
			background: #CCCCCC;
		}
		
		.GrayThing
		{
			background: #CCCCCC;
		}
	
	//-->
	</style>
</head>
<body BGCOLOR="FFFFFF" oncontextmenu = "return true" onselectstart = "return true" ondragstart = "return true">
<script type="text/javascript">
<!--
	function enter()
	{
		document.forms.ef.submit();
	}
//-->
</script>
<BODY TEXT="#000066">
<?php
include_once("conex.php");
function bisiesto($year)
{
	return(($year % 4 == 0 and $year % 100 != 0) or $year % 400 == 0);
}

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
	   PROGRAMA : ef.php
	   Fecha de Liberaci�n : 2006-05-08
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2007-07-24
	   
	   OBJETIVO GENERAL : 
	   Este programa permite visualizar los estados financieros comparativos de la promotora medica las americas en 
	   tres reportes (Balance General, Estado de Resultados e Indicadores Financieros).
	   
	   
	   REGISTRO DE MODIFICACIONES :
			
	   .2006-04-07
	   		Inicio del Programa a Produccion.
	   	
	   .2006-05-19
	   		Se modifico el titulo del Estado de Resultados para que no aparezca la palabra acumulado cuando el periodo
	   		es de un mes unicamente.
	   		
   	   .2006-09-14
	   		Se modifico el rango de codigos en los indicadores financieros hasta el 101 (VALOR INTRINSECO DE LA ACCION).
	   		
	   .2007-07-24
	   		Se modifico el programa cambiando el campo juncod de integer a caracter con el proposito de poder intercalar
	   		nuevas cuentas entre la ya existentes.

	   .2008-04-14
	   		Se modifico el programa para adicionar los informes de Notas al Balance General y Notas a los Estados Financieros
			Se creo para estos reportes la tabla 5  con los campos : (Notano Notmes Nottip Notcod Notnom Notexp)
            en donde la variable Nottip toma los siguientes valores : (1 Balance General / 2 Estados Financieros)

	   .2008-04-18
	   		Se modifico modifica le titulo de Notas a los Estados Financieros.
	   		
***********************************************************************************************************************/
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='ef' action='ef.php' method=post>";
	

	

	if (!isset($wtip))
	{
		$wcolor="#cccccc";
		echo "<table border=0 align=center>";
		echo "<tr><td align=center bgcolor=#000066 colspan=1><font color=#ffffff size=6><b>ESTADOS FINANCIEROS</font><font color=#33CCFF size=4>&nbsp&nbsp&nbspVer. 2008-04-18</font></b></font></td></tr>";
		echo "<tr><td align=center bgcolor=".$wcolor."><input type='RADIO' name=wtip value=1 onclick='enter()'> Balance General ";
		echo "<input type='RADIO' name=wtip value=2 onclick='enter()'> Estado de Resultados ";
		echo "<input type='RADIO' name=wtip value=3 onclick='enter()'> Indicadores Financieros ";
		echo "<input type='RADIO' name=wtip value=4 onclick='enter()'> Notas al Balance General ";
		echo "<input type='RADIO' name=wtip value=5 onclick='enter()'> Notas al Estado de Resultados </td></tr>";
		echo "</table>";  
	}
	else
	{
		if(!isset($wano))
		{
			echo "<input type='HIDDEN' name= 'wtip' value='".$wtip."'>";
			switch ($wtip)
			{
				case 1:
					echo "<center><table border=0>";
					echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
					echo "<tr><td align=center colspan=2>INFORME DEL BALANCE GENERAL COMPARATIVO</td></tr>";
					echo "<tr><td bgcolor=#cccccc align=center>A�o - Mes de Proceso</td><td bgcolor=#cccccc align=center>";
					$query = "SELECT Perano, Permes from junta_000002 where Perest ='on' order by Perano, Permes";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					if ($num>0)
					{
						echo "<select name='wano'>";
						for ($i=0;$i<$num;$i++)
						{
							$row = mysql_fetch_array($err);
							echo "<option>".$row[0]."-".$row[1]."</option>";
						}
						echo "</select>";
					}
					echo "</td></tr>";
					echo "<tr><td align=left bgcolor=#cccccc colspan=2><input type='RADIO' name=wtip1 value=1 onclick='enter()'>Comparativo Mes A�o Anterior <input type='RADIO' name=wtip1 value=2 onclick='enter()'> Comparativo Diciembre A�o Anterior </td></tr>";
					echo "</table>";  
				break;
				case 2:
					echo "<center><table border=0>";
					echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
					echo "<tr><td align=center colspan=2>INFORME DE ESTADO DE RESULTADOS ACUMULADO/MENSUAL COMPARATIVO</td></tr>";
					echo "<tr><td bgcolor=#cccccc align=center>A�o - Mes de Inicial</td><td bgcolor=#cccccc align=center>";
					$query = "SELECT Perano, Permes from junta_000002 where Perest ='on' order by Perano, Permes";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					if ($num>0)
					{
						echo "<select name='wano'>";
						for ($i=0;$i<$num;$i++)
						{
							$row = mysql_fetch_array($err);
							echo "<option>".$row[0]."-".$row[1]."</option>";
						}
						echo "</select>";
					}
					echo "</td></tr>";
					echo "<tr><td bgcolor=#cccccc align=center>A�o - Mes de Final</td><td bgcolor=#cccccc align=center>";
					$query = "SELECT Perano, Permes from junta_000002 where Perest ='on' order by Perano, Permes";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					if ($num>0)
					{
						echo "<select name='wano1'>";
						for ($i=0;$i<$num;$i++)
						{
							$row = mysql_fetch_array($err);
							echo "<option>".$row[0]."-".$row[1]."</option>";
						}
						echo "</select>";
					}
					echo "</td></tr>";
					echo "<tr><td align=center colspan=2 bgcolor=#cccccc><input type='RADIO' name=wtip1 value=1 onclick='enter()'>CONTINUAR</td></tr>";
					echo "</table>";
				break;
				case 3:
					echo "<center><table border=0>";
					echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
					echo "<tr><td align=center colspan=2>INFORME INDICADORES FINANCIEROS</td></tr>";
					echo "<tr><td bgcolor=#cccccc align=center>A�o - Mes de Proceso</td><td bgcolor=#cccccc align=center>";
					$query = "SELECT Perano, Permes from junta_000002 where Perest ='on' order by Perano, Permes";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					if ($num>0)
					{
						echo "<select name='wano'>";
						for ($i=0;$i<$num;$i++)
						{
							$row = mysql_fetch_array($err);
							echo "<option>".$row[0]."-".$row[1]."</option>";
						}
						echo "</select>";
					}
					echo "</td></tr>";
					echo "<tr><td align=left colspan=2 bgcolor=#cccccc><input type='RADIO' name=wtip1 value=1 onclick='enter()'>Comparativo Mes A�o Anterior <input type='RADIO' name=wtip1 value=2 onclick='enter()'> Comparativo Diciembre A�o Anterior </td></tr>";
					echo "</table>";  
				break;
				case 4:
					echo "<center><table border=0>";
					echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
					echo "<tr><td align=center colspan=2>NOTAS AL BALANCE GENERAL</td></tr>";
					echo "<tr><td bgcolor=#cccccc align=center>A�o - Mes Inicial de Proceso</td><td bgcolor=#cccccc align=center>";
					$query = "SELECT Perano, Permes from junta_000002 where Perest ='on' order by Perano, Permes";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					if ($num>0)
					{
						echo "<select name='wano'>";
						for ($i=0;$i<$num;$i++)
						{
							$row = mysql_fetch_array($err);
							echo "<option>".$row[0]."-".$row[1]."</option>";
						}
						echo "</select>";
					}
					echo "</td></tr>";
					echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr>";
					echo "</table>";  
				break;
				case 5:
					echo "<center><table border=0>";
					echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
					echo "<tr><td align=center colspan=2>NOTAS A LOS ESTADOS FINANCIEROS</td></tr>";
					echo "<tr><td bgcolor=#cccccc align=center>A�o - Mes Inicial de Proceso</td><td bgcolor=#cccccc align=center>";
					$query = "SELECT Perano, Permes from junta_000002 where Perest ='on' order by Perano, Permes";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					if ($num>0)
					{
						echo "<select name='wano'>";
						for ($i=0;$i<$num;$i++)
						{
							$row = mysql_fetch_array($err);
							echo "<option>".$row[0]."-".$row[1]."</option>";
						}
						echo "</select>";
					}
					echo "</td></tr>";
					echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr>";
					echo "</table>";  
				break;
			}
		}
		else
		{
			$meses=array();
			$meses[0][0]="ENE";
			$meses[1][0]="FEB";
			$meses[2][0]="MAR";
			$meses[3][0]="ABR";
			$meses[4][0]="MAY";
			$meses[5][0]="JUN";
			$meses[6][0]="JUL";
			$meses[7][0]="AGO";
			$meses[8][0]="SEP";
			$meses[9][0]="OCT";
			$meses[10][0]="NOV";
			$meses[11][0]="DIC";
			$meses[0][1]=31;
			if(bisiesto(substr($wano,0,strpos($wano,"-"))))
				$meses[1][1]=29;
			else
				$meses[1][1]=28;
			$meses[2][1]=31;
			$meses[3][1]=30;
			$meses[4][1]=31;
			$meses[5][1]=30;
			$meses[6][1]=31;
			$meses[7][1]=31;
			$meses[8][1]=30;
			$meses[9][1]=31;
			$meses[10][1]=30;
			$meses[11][1]=31;
			switch ($wtip)
			{
				case 1:
					$wmes=substr($wano,strpos($wano,"-")+1);
					$wano=substr($wano,0,strpos($wano,"-"));
					$wcolor="#cccccc";
					echo "<table border=0 align=center>";
					echo "<tr><td align=center colspan=2><IMG SRC='/matrix/images/medical/junta/promotora.png' width=50%></td></tr>";
					echo "<tr><td align=center bgcolor=#000066 colspan=2><font color=#ffffff size=4><b>BALANCE GENERAL A ".$meses[$wmes-1][0]."/".$meses[$wmes-1][1]." DE ".$wano."</font><font color=#33CCFF size=4>&nbsp&nbsp&nbspVer. 2008-04-18</font></b></font></td></tr>";
					echo "<tr><td align=center colspan=2><font size=3>(Expresado en miles de pesos)</font></td></tr>";
					echo "</table><br>";  
					echo "<table border=0 align=center>";
					echo "<tr><td align=right colspan=5><font size=2>Powered by :  MATRIX</font></td></tr>";
					$wanoa = $wano -1;
					if($wtip1 == 1)
					{
						$otr=$meses[$wmes-1][0]."/".$wanoa;
						$wmesa=$wmes;
					}
					else
					{
						$otr="DIC/".$wanoa;
						$wmesa=12;
					}
					echo "<tr><td align=center bgcolor=#000066><font color=#ffffff >RUBRO</font></td><td align=center bgcolor=#000066><font color=#ffffff >".$meses[$wmes-1][0]."/".$wano."</font></td><td align=center bgcolor=#000066><font color=#ffffff >".$otr."</font></td><td align=center bgcolor=#000066><font color=#ffffff >%</font></td><td align=center bgcolor=#000066><font color=#ffffff >EXPLICACION</font></td></tr>";
					$query = "select Junnom, Junval, Juntip, Junexp  from  junta_000001 ";
					$query .= "  where junano =".$wanoa;
					$query .= "    and Junmes =".$wmesa;
					$query .= "    and Juncod < '045'";
					$query .= "    order by Juncod ";
					$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					$query = "select Junnom, Junval, Juntip, Junexp  from  junta_000001 ";
					$query .= "  where junano =".$wano;
					$query .= "    and Junmes =".$wmes;
					$query .= "    and Juncod < '045'";
					$query .= "    order by Juncod ";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					if($num > 0)
						for ($i=0;$i<$num;$i++)
						{
							$row = mysql_fetch_array($err);
							$row1 = mysql_fetch_array($err1);
							if($row[2] == "on")
								$color="#999999";
							elseif($i % 2 == 0)
									$color="#dddddd";
								else
									$color="#ffffff";
							if($row[1] == 0 and $row1[1] == 0)
							{
								echo "<tr>";
								echo "<td bgcolor=".$color." colspan=5>".$row[0]."</td>";	
								echo "</tr>";
							}
							else
							{
								if($row1[1] != 0)
									$por=(($row[1] / $row1[1]) - 1) * 100;
								else
									$por=100;
								if($i % 2 == 0)
									$color="#dddddd";
								else
									$color="#ffffff";
								if($row[1] < 0)
									$color1 = "#CC0000";
								else
									$color1 = "#000066";
								if($row1[1] < 0)
									$color2 = "#CC0000";
								else
									$color2 = "#000066";
								if($por < 0)
									$color3 = "#CC0000";
								else
									$color3 = "#000066";
								echo "<tr>";
								if(substr($row[0],0,5) == "TOTAL")
								{
									echo "<td bgcolor=".$color."><b>".$row[0]."</b></td>";	
									echo "<td bgcolor=".$color." align=right><font color=".$color1."><b>".number_format($row[1],0,'.',',')."</b></font></td>";	
									echo "<td bgcolor=".$color." align=right><font color=".$color2."><b>".number_format($row1[1],0,'.',',')."</b></font></td>";
									echo "<td bgcolor=".$color." align=right><font color=".$color3."><b>".number_format($por,2,'.',',')."</b></font></td>";
								}
								else
								{
									echo "<td bgcolor=".$color.">".$row[0]."</td>";	
									echo "<td bgcolor=".$color." align=right><font color=".$color1.">".number_format($row[1],0,'.',',')."</font></td>";	
									echo "<td bgcolor=".$color." align=right><font color=".$color2.">".number_format($row1[1],0,'.',',')."</font></td>";
									echo "<td bgcolor=".$color." align=right><font color=".$color3.">".number_format($por,2,'.',',')."</font></td>";
								}
								echo "<td bgcolor=".$color." align=center><IMG SRC='/matrix/images/medical/junta/04.gif' alt='".$row[3]."'></td>";
								echo "</tr>";
							}
						}
					echo "<tr><td align=center bgcolor=#999999 colspan=5><input type='submit' value='Continuar'></td></tr>";
					echo "</table><br><br>"; 
				break;
				case 2:
					$wa=$wano;
					$wa1=$wano1;
					$wmes=substr($wano,strpos($wano,"-")+1);
					$wano=substr($wano,0,strpos($wano,"-"));
					$wmes1=substr($wano1,strpos($wano1,"-")+1);
					$wcolor="#cccccc";
					echo "<table border=0 align=center>";
					echo "<tr><td align=center colspan=2><IMG SRC='/matrix/images/medical/junta/promotora.png' width=50%></td></tr>";
					if($wa == $wa1)
						echo "<tr><td align=center bgcolor=#000066 colspan=2><font color=#ffffff size=4><b>ESTADO DE RESULTADOS MENSUAL A ".$meses[$wmes1-1][0]."/".$meses[$wmes1-1][1]." DE ".$wano."</font><font color=#33CCFF size=4>&nbsp&nbsp&nbspVer. 2008-04-18</font></b></font></td></tr>";
					else
						echo "<tr><td align=center bgcolor=#000066 colspan=2><font color=#ffffff size=4><b>ESTADO DE RESULTADOS ACUMULADO A ".$meses[$wmes1-1][0]."/".$meses[$wmes1-1][1]." DE ".$wano."</font><font color=#33CCFF size=4>&nbsp&nbsp&nbspVer. 2008-04-18</font></b></font></td></tr>";
					echo "<tr><td align=center colspan=2><font size=3>(Expresado en miles de pesos)</font></td></tr>";
					echo "</table><br>";  
					echo "<table border=0 align=center>";
					echo "<tr><td align=right colspan=9><font size=2>Powered by :  MATRIX</font></td></tr>";
					
					$wano1 = $wano -1;
					if($wa == $wa1)
					{
						$otr=" ".$meses[$wmes1-1][0]."/".$wano1;
						echo "<tr><td align=center bgcolor=#000066><font color=#ffffff >RUBRO</font></td><td align=center bgcolor=#000066><font color=#ffffff > ".$meses[$wmes1-1][0]."/".$wano."</font></td><td align=center bgcolor=#000066><font color=#ffffff >VR.V.</font></td><td align=center bgcolor=#000066><font color=#ffffff >".$otr."</font></td><td align=center bgcolor=#000066><font color=#ffffff >VR.V.</font></td><td align=center bgcolor=#000066><font color=#ffffff >Variacion<br>Anual</font></td><td align=center bgcolor=#000066><font color=#ffffff >%</font></td><td align=center bgcolor=#000066><font color=#ffffff >VR.H.</font></td><td align=center bgcolor=#000066><font color=#ffffff >EXPLICACION</font></td></tr>";
					}
					else
					{
						$otr="ACUM. ".$meses[$wmes1-1][0]."/".$wano1;
						echo "<tr><td align=center bgcolor=#000066><font color=#ffffff >RUBRO</font></td><td align=center bgcolor=#000066><font color=#ffffff >ACUM. ".$meses[$wmes1-1][0]."/".$wano."</font></td><td align=center bgcolor=#000066><font color=#ffffff >VR.V.</font></td><td align=center bgcolor=#000066><font color=#ffffff >".$otr."</font></td><td align=center bgcolor=#000066><font color=#ffffff >VR.V.</font></td><td align=center bgcolor=#000066><font color=#ffffff >Variacion<br>Anual</font></td><td align=center bgcolor=#000066><font color=#ffffff >%</font></td><td align=center bgcolor=#000066><font color=#ffffff >VR.H.</font></td><td align=center bgcolor=#000066><font color=#ffffff >EXPLICACION</font></td></tr>";
					}
					$query = "select Juncod, Junnom, Juntip, sum(Junval)  from  junta_000001 ";
					$query .= "  where junano =".$wano1;
					$query .= "    and Junmes between ".$wmes." and ".$wmes1;
					$query .= "    and Juncod > '044'";
					$query .= "    and Juncod < '095'";
					$query .= "    group by Juncod, Junnom, Juntip ";
					$query .= "    order by Juncod ";
					$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					$query = "select Juncod, Junnom, Juntip, sum(Junval)  from  junta_000001 ";
					$query .= "  where junano =".$wano;
					$query .= "    and Junmes between ".$wmes." and ".$wmes1;
					$query .= "    and Juncod > '044'";
					$query .= "    and Juncod < '095'";
					$query .= "    group by Juncod, Junnom, Juntip ";
					$query .= "    order by Juncod ";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					if($num > 0)
						for ($i=0;$i<$num;$i++)
						{
							$row = mysql_fetch_array($err);
							$row1 = mysql_fetch_array($err1);
							if($i == 0)
							{
								$ing1=$row[3];
								$ing2=$row1[3];
								$ing3=$row[3] - $row1[3];
								$por1=0;
								$por2=0;
								$por3=0;
							}
							else
							{
								$por1=$row[3] / $ing1 * 100;
								$por2=$row1[3] / $ing2 * 100;
								$por3=($row[3] - $row1[3]) / $ing3 * 100;
							}
							$dif=$row[3] - $row1[3];
							if($row1[3] != 0)
								$por4=(($row[3] / $row1[3]) - 1) * 100;
							else
								$por4=0;
							if($row[2] == "on")
								$color="#999999";
							elseif($i % 2 == 0)
									$color="#dddddd";
								else
									$color="#ffffff";
							if($row[3] < 0)
								$color1 = "#CC0000";
							else
								$color1 = "#000066";
							if($por1 < 0)
								$color2 = "#CC0000";
							else
								$color2 = "#000066";
							if($row1[3] < 0)
								$color3 = "#CC0000";
							else
								$color3 = "#000066";
							if($por2 < 0)
								$color4 = "#CC0000";
							else
								$color4 = "#000066";	
							if($dif < 0)
								$color5 = "#CC0000";
							else
								$color5 = "#000066";
							if($por3 < 0)
								$color6 = "#CC0000";
							else
								$color6 = "#000066";
							if($por4 < 0)
								$color7 = "#CC0000";
							else
								$color7 = "#000066";
							echo "<tr>";
							echo "<td bgcolor=".$color.">".$row[1]."</td>";	
							echo "<td bgcolor=".$color." align=right><font color=".$color1.">".number_format((double)$row[3],0,'.',',')."</font></td>";	
							echo "<td bgcolor=".$color." align=right><font color=".$color2.">".number_format((double)$por1,2,'.',',')."</font></td>";
							echo "<td bgcolor=".$color." align=right><font color=".$color3.">".number_format((double)$row1[3],0,'.',',')."</font></td>";
							echo "<td bgcolor=".$color." align=right><font color=".$color4.">".number_format((double)$por2,2,'.',',')."</font></td>";
							echo "<td bgcolor=".$color." align=right><font color=".$color5.">".number_format((double)$dif,2,'.',',')."</font></td>";
							echo "<td bgcolor=".$color." align=right><font color=".$color6.">".number_format((double)$por3,2,'.',',')."</font></td>";
							echo "<td bgcolor=".$color." align=right><font color=".$color7.">".number_format((double)$por4,2,'.',',')."</font></td>";
							$query = "select Junexp  from  junta_000001 ";
							$query .= "  where junano =".$wano;
							$query .= "    and Junmes = ".$wmes1;
							$query .= "    and Juncod = ".$row[0];
							$err2 = mysql_query($query,$conex);
							$row2 = mysql_fetch_array($err2);
							echo "<td bgcolor=".$color." align=center><IMG SRC='/matrix/images/medical/junta/04.gif' alt='".$row2[0]."'></td>";
							echo "</tr>";
						}
					echo "<tr><td align=center bgcolor=#999999 colspan=9><input type='submit' value='Continuar'></td></tr>";
					echo "</table><br><br>"; 
				break;
				case 3:
					$wmes=substr($wano,strpos($wano,"-")+1);
					$wano=substr($wano,0,strpos($wano,"-"));
					$wcolor="#cccccc";
					echo "<table border=0 align=center>";
					echo "<tr><td align=center colspan=2><IMG SRC='/matrix/images/medical/junta/promotora.png' width=50%></td></tr>";
					echo "<tr><td align=center bgcolor=#000066 colspan=2><font color=#ffffff size=4><b>INDICADORES FINANCIEROS A ".$meses[$wmes-1][0]."/".$meses[$wmes-1][1]." DE ".$wano."</font><font color=#33CCFF size=4>&nbsp&nbsp&nbspVer. 2008-04-18</font></b></font></td></tr>";
					echo "</table><br>";  
					echo "<table border=0 align=center>";
					echo "<tr><td align=right colspan=4><font size=2>Powered by :  MATRIX</font></td></tr>";
					$wanoa = $wano -1;
					if($wtip1 == 1)
					{
						$otr=$meses[$wmes-1][0]."/".$wanoa;
						$wmesa=$wmes;
					}
					else
					{
						$otr="DIC/".$wanoa;
						$wmesa=12;
					}
					echo "<tr><td align=center bgcolor=#000066><font color=#ffffff >INDICE</font></td><td align=center bgcolor=#000066><font color=#ffffff >".$meses[$wmes-1][0]."/".$wano."</font></td><td align=center bgcolor=#000066><font color=#ffffff >".$otr."</font></td><td align=center bgcolor=#000066><font color=#ffffff >EXPLICACION</font></td></tr>";
					$query = "select Junnom, Junval, Juntip, Junexp  from  junta_000001 ";
					$query .= "  where junano =".$wanoa;
					$query .= "    and Junmes =".$wmesa;
					$query .= "    and Juncod > '094'";
					$query .= "    and Juncod < '103'";
					$query .= "    order by Juncod ";
					$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					$query = "select Junnom, Junval, Juntip, Junexp  from  junta_000001 ";
					$query .= "  where junano =".$wano;
					$query .= "    and Junmes =".$wmes;
					$query .= "    and Juncod > '094'";
					$query .= "    and Juncod < '103'";
					$query .= "    order by Juncod ";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					if($num > 0)
						for ($i=0;$i<$num;$i++)
						{
							$row = mysql_fetch_array($err);
							$row1 = mysql_fetch_array($err1);
							if($row[2] == "on")
								$color="#999999";
							elseif($i % 2 == 0)
									$color="#dddddd";
								else
									$color="#ffffff";
							if($i % 2 == 0)
								$color="#dddddd";
							else
								$color="#ffffff";
							if($row[1] < 0)
								$color1 = "#CC0000";
							else
								$color1 = "#000066";
							if($row1[1] < 0)
								$color2 = "#CC0000";
							else
								$color2 = "#000066";
							echo "<tr>";
							echo "<td bgcolor=".$color.">".$row[0]."</td>";
							$wimp=substr($row[0],0,1);
							switch ($wimp)
							{
								case "C":
									echo "<td bgcolor=".$color." align=right><font color=".$color1.">".number_format((double)$row[1],0,'.',',')."</font></td>";	
									echo "<td bgcolor=".$color." align=right><font color=".$color2.">".number_format((double)$row1[1],0,'.',',')."</font></td>";
								break;
								case "M":
									echo "<td bgcolor=".$color." align=right><font color=".$color1.">".number_format((double)$row[1],0,'.',',')."</font></td>";	
									echo "<td bgcolor=".$color." align=right><font color=".$color2.">".number_format((double)$row1[1],0,'.',',')."</font></td>";
								break;
								case "L":
									echo "<td bgcolor=".$color." align=right><font color=".$color1.">".number_format((double)$row[1],2,'.',',')."</font></td>";	
									echo "<td bgcolor=".$color." align=right><font color=".$color2.">".number_format((double)$row1[1],2,'.',',')."</font></td>";
								break;
								case "P":
									echo "<td bgcolor=".$color." align=right><font color=".$color1.">".number_format((double)$row[1],2,'.',',')."</font></td>";	
									echo "<td bgcolor=".$color." align=right><font color=".$color2.">".number_format((double)$row1[1],2,'.',',')."</font></td>";
								break;
								case "V":
									echo "<td bgcolor=".$color." align=right><font color=".$color1.">".number_format((double)$row[1],0,'.',',')."</font></td>";	
									echo "<td bgcolor=".$color." align=right><font color=".$color2.">".number_format((double)$row1[1],0,'.',',')."</font></td>";
								break;
								case "E":
									$por1=$row[1]*100;
									$por2=$row1[1]*100;
									echo "<td bgcolor=".$color." align=right><font color=".$color1.">".number_format((double)$por1,2,'.',',')."%</font></td>";	
									echo "<td bgcolor=".$color." align=right><font color=".$color2.">".number_format((double)$por2,2,'.',',')."%</font></td>";
								break;
								case "S":
									$por1=$row[1]*100;
									$por2=$row1[1]*100;
									echo "<td bgcolor=".$color." align=right><font color=".$color1.">".number_format((double)$por1,2,'.',',')."%</font></td>";	
									echo "<td bgcolor=".$color." align=right><font color=".$color2.">".number_format((double)$por2,2,'.',',')."%</font></td>";
								break;
								case "R":
									$por1=$row[1]*100;
									$por2=$row1[1]*100;
									echo "<td bgcolor=".$color." align=right><font color=".$color1.">".number_format((double)$por1,2,'.',',')."%</font></td>";	
									echo "<td bgcolor=".$color." align=right><font color=".$color2.">".number_format((double)$por2,2,'.',',')."%</font></td>";
								break;
							}
							echo "<td bgcolor=".$color." align=center><IMG SRC='/matrix/images/medical/junta/04.gif' alt='".$row[3]."'></td>";
							echo "</tr>";
						}
					echo "<tr><td align=center bgcolor=#999999 colspan=4><input type='submit' value='Continuar'></td></tr>";
					echo "</table><br><br>"; 
				break;
				case 4:
					$wmes=substr($wano,strpos($wano,"-")+1);
					$wano=substr($wano,0,strpos($wano,"-"));
					$wcolor="#cccccc";
					echo "<table border=0 align=center>";
					echo "<tr><td align=center colspan=2><IMG SRC='/matrix/images/medical/junta/promotora.png' width=50%></td></tr>";
					echo "<tr><td align=center bgcolor=#000066 colspan=2><font color=#ffffff size=4><b>NOTAS AL BALANCE GENERAL DESDE : ".$meses[$wmes-1][0]." DE ".$wano."</font><font color=#33CCFF size=4>&nbsp&nbsp&nbspVer. 2008-04-18</font></b></font></td></tr>";
					echo "<tr><td align=center colspan=2><font size=3>(Expresado en miles de pesos)</font></td></tr>";
					echo "</table><br>";  
					echo "<table border=0 align=center>";
					echo "<tr><td align=right colspan=5><font size=2>Powered by :  MATRIX</font></td></tr>";
					echo "<tr><td align=center bgcolor=#000066><font color=#ffffff >RUBRO</font></td><td align=center bgcolor=#000066><font color=#ffffff >MES</font></td><td align=center bgcolor=#000066><font color=#ffffff >EXPLICACION</font></td></tr>";
					$query = "select Notnom, Notmes, Notexp from  junta_000005 ";
					$query .= "  where Notano =".$wano;
					$query .= "    and Notmes <=".$wmes;
					$query .= "    and Nottip = '1' ";
					$query .= "    order by Notcod, Notmes ";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					$NA="";
					if($num > 0)
						for ($i=0;$i<$num;$i++)
						{
							$row = mysql_fetch_array($err);
							if($NA == $row[0])
							{
								$NAp="";
							}
							else
							{
								$NA=$row[0];
								$NAp=$row[0];
							}
							if($i % 2 == 0)
								$color="#dddddd";
							else
								$color="#ffffff";
							echo "<tr>";
							echo "<td bgcolor=".$color.">".$NAp."</td>";	
							echo "<td bgcolor=".$color.">".$meses[$row[1]-1][0]."</td>";	
							echo "<td bgcolor=".$color.">".$row[2]."</td>";	
							echo "</tr>";
							
						}
					echo "<tr><td align=center bgcolor=#999999 colspan=5><input type='submit' value='Continuar'></td></tr>";
					echo "</table><br><br>"; 
				break;
				case 5:
					$wmes=substr($wano,strpos($wano,"-")+1);
					$wano=substr($wano,0,strpos($wano,"-"));
					$wcolor="#cccccc";
					echo "<table border=0 align=center>";
					echo "<tr><td align=center colspan=2><IMG SRC='/matrix/images/medical/junta/promotora.png' width=50%></td></tr>";
					echo "<tr><td align=center bgcolor=#000066 colspan=2><font color=#ffffff size=4><b>NOTAS A LOS ESTADOS FINANCIEROS DESDE : ".$meses[$wmes-1][0]." DE ".$wano."</font><font color=#33CCFF size=4>&nbsp&nbsp&nbspVer. 2008-04-18</font></b></font></td></tr>";
					echo "<tr><td align=center colspan=2><font size=3>(Expresado en miles de pesos)</font></td></tr>";
					echo "</table><br>";  
					echo "<table border=0 align=center>";
					echo "<tr><td align=right colspan=5><font size=2>Powered by :  MATRIX</font></td></tr>";
					echo "<tr><td align=center bgcolor=#000066><font color=#ffffff >RUBRO</font></td><td align=center bgcolor=#000066><font color=#ffffff >MES</font></td><td align=center bgcolor=#000066><font color=#ffffff >EXPLICACION</font></td></tr>";
					$query = "select Notnom, Notmes, Notexp from  junta_000005 ";
					$query .= "  where Notano =".$wano;
					$query .= "    and Notmes <=".$wmes;
					$query .= "    and Nottip = '2' ";
					$query .= "    order by Notcod, Notmes ";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					$NA="";
					if($num > 0)
						for ($i=0;$i<$num;$i++)
						{
							$row = mysql_fetch_array($err);
							if($NA == $row[0])
							{
								$NAp="";
							}
							else
							{
								$NA=$row[0];
								$NAp=$row[0];
							}
							if($i % 2 == 0)
								$color="#dddddd";
							else
								$color="#ffffff";
							echo "<tr>";
							echo "<td bgcolor=".$color.">".$NAp."</td>";	
							echo "<td bgcolor=".$color.">".$meses[$row[1]-1][0]."</td>";
							echo "<td bgcolor=".$color.">".$row[2]."</td>";	
							echo "</tr>";
							
						}
					echo "<tr><td align=center bgcolor=#999999 colspan=5><input type='submit' value='Continuar'></td></tr>";
					echo "</table><br><br>"; 
				break;
			}
		}
	}
}
?>