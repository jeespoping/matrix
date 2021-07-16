<html>
<head>
  	<title>MATRIX Programa de Cirugias</title>
	<link rel='stylesheet' href='/matrix/ips/procesos/CartDig.css'/>
</head>
<body oncontextmenu = "return true" onselectstart = "return true" ondragstart = "return true">

<script type="text/javascript">
<!--
	function enter()
	{
		document.forms.programa.submit();
	}
	function ejecutar(path,tipo)
	{
		if(tipo == 1)
			window.open(path,'','fullscreen=0,status=0,menubar=0,toolbar =0,directories =0,resizable=0,scrollbars =1,titlebar=0,width=900,height=425');
		else
			window.open(path,'','fullscreen=0,status=0,menubar=0,toolbar =0,directories =0,resizable=0,scrollbars =1,titlebar=0,width=900,height=580');
	}
	function teclado()  
	{ 
		if ((event.keyCode < 48 || event.keyCode > 57  || event.keyCode == 13) & event.keyCode != 46) event.returnValue = false;
	}
	function teclado1()  
	{ 
		if ((event.keyCode < 48 || event.keyCode > 57 ) & event.keyCode != 46 & event.keyCode != 13)  event.returnValue = false;
	}
	function teclado2()  
	{ 
		if ((event.keyCode < 48 || event.keyCode > 57 ) & (event.keyCode < 65 || event.keyCode > 90 ) & (event.keyCode < 97 || event.keyCode > 122 ) & event.keyCode != 13) event.returnValue = false;
	}
	function teclado3()  
	{ 
		if ((event.keyCode < 48 || event.keyCode > 57 ) & (event.keyCode < 65 || event.keyCode > 90 ) & (event.keyCode < 97 || event.keyCode > 122 ) & event.keyCode != 13 & event.keyCode != 45) event.returnValue = false;
	}

//-->
</script>
<?php
include_once("conex.php");

/**********************************************************************************************************************  
[DOC]
	   PROGRAMA : programa.php
	   Fecha de Liberación : 2009-09-29
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2010-03-01
	   
	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface gráfica que permite visualizar las cirugias
	   de un dia determinado y la ubicacion del paciente en todo momento.

	   
	   REGISTRO DE MODIFICACIONES :
	   .2009-09-29
	   		Release de Versión Beta.
	   	
	   .2010-03-01
	   	Se modifico el programa para actualizar la fecha por cada ciclo de ejecucion del script.
	   .2013-10-30
	    Se cambiaron la hojas de estilo para que trabajaran con Firefox y se quitaron la sesiones.
		
	   .2021-07-05 Luis F Meneses: Se aplican nuevos estilos (/matrix/ips/procesos/CartDig.css)
	    
[*DOC]   		
***********************************************************************************************************************/
function bisiesto($year)
{
	//si es multiplo de 4 y no es multiplo de 100 o es multiplo de 400*/
	return(($year % 4 == 0 and $year % 100 != 0) or $year % 400 == 0);
}

	
//session_start();
//if(!isset($_SESSION['user']))
	//echo "error";
//else
//{
	$key = substr($user,2,strlen($user));
	echo "<form name='programa' programa='programa.php' method=post>";
	echo "<meta http-equiv='refresh' content='30;url=/matrix/tcx/procesos/Programa.php?empresa=".$empresa."'>";
	echo "<input type='hidden' name= 'empresa' value='".$empresa."'>";
	
	$wfecha=date("Y-m-d");
	$year = (integer)substr($wfecha,0,4);
	$month = (integer)substr($wfecha,5,2);
	$day = (integer)substr($wfecha,8,2);
	$nomdia=mktime(0,0,0,$month,$day,$year);
	$nomdia = strftime("%w",$nomdia);
	$wsw=0;
	$Grid=array();
	$cantFilas = 12;
	
	for ($i=0;$i<$cantFilas;$i++)
	{
		$Grid[$i]["tur"]="";
		$Grid[$i]["his"]="";
		$Grid[$i]["est"]="";
		$Grid[$i]["ubi"]="";
		$Grid[$i]["ico"]="tic";
		$Grid[$i]["col"]="N1";
		$Grid[$i]["clase"]="tdNormalSinPadding";  // estilo de la celda
	}
	switch ($nomdia)
	{
		case 0:
			$diasem = "Domingo";
			break;
		case 1:
			$diasem = "Lunes";
			break;
		case 2:
			$diasem = "Martes";
			break;
		case 3:
			$diasem = "Mi&eacute;rcoles";
			break;
		case 4:
			$diasem = "Jueves";
			break;
		case 5:
			$diasem = "Viernes";
			break;
		case 6:
			$diasem = "S&aacute;bado";
			break;
	}
	switch ($month)
	{
		case 1:
			$monthN = "enero";
			break;
		case 2:
			$monthN = "febrero";
			break;
		case 3:
			$monthN = "marzo";
			break;
		case 4:
			$monthN = "abril";
			break;
		case 5:
			$monthN = "mayo";
			break;
		case 6:
			$monthN = "junio";
			break;
		case 7:
			$monthN = "julio";
			break;
		case 8:
			$monthN = "agosto";
			break;
		case 9:
			$monthN = "septiembre";
			break;
		case 10:
			$monthN = "octubre";
			break;
		case 11:
			$monthN = "noviembre";
			break;
		case 12:
			$monthN = "diciembre";
			break;
	}
	$wfechaG=$day." de ".$monthN." de ".$year;
	
	
	
	// TITULO CARTELERA
	/*
	echo "<center><table border=0 CELLSPACING=0 id=tipoG00>";
	echo "<tr><td align=center id=tipoT01><img src='/matrix/images/medical/root/lmatrix.jpg'></td>";
	echo "<td id=tipoT02>&nbsp;CLINICA LAS AMERICAS<BR>&nbsp;PROGRAMA DE CIRUGIAS &nbsp;&nbsp;".$diasem." ".$wfechaG."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<A HREF='/MATRIX/root/Reportes/DOC.php?files=../../tcx/procesos/Programa.php' target='_blank'><font size=2>Version 2010-03-01</font></A></td></tr>";
	echo "<tr><td id=tipoT03 colspan=2></td></tr>";
	echo "</table><br>";
	*/
	
	echo "<center><table id='titTurnos'>";
	echo "<tr><td id='tdTitLogo'></td>";
	echo "<td id='tdTitDescrip'>&nbsp;PROGRAMA DE<br>CIRUG&Iacute;AS</td>";
	echo "<td id='tdTitFecha' align=right colspan=2>".$diasem."<br>".$wfechaG."</td></tr>";
	echo "</table>";
	
	// tcx__000011      0       1       2        3       4      5       6       7
	$query = "SELECT Turtur, Turhis, Turpes, Turpep, Turpeq, Turper, Turpea, Turubi as ubicacion
			from ".$empresa."_000011 
			where turfec = '".$wfecha."'
			Order by Turtur";
	//echo "$query";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);	
	if ($num < $cantFilas)
		$cantFilas = $num;
	if ($num>0)
	{
		for ($i=0;$i<$cantFilas && $i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$Grid[$i]["tur"]=$row[0];
			$Grid[$i]["his"]=$row[1];
			if($row[3] == "on")
			{
				$Grid[$i]["est"]="Preparaci&oacute;n";
				$Grid[$i]["ico"]="Ico_Preparacion";  // pep1
				$Grid[$i]["col"]="N4";
			}
			elseif($row[4] == "on")
				{
					$Grid[$i]["est"]="Quir&oacute;fano";
					$Grid[$i]["ico"]="Ico_Quirofano";  // peq1
					$Grid[$i]["col"]="N5";
				}
				elseif($row[5] == "on")
					{
						$Grid[$i]["est"]="Recuperaci&oacute;n";
						$Grid[$i]["ico"]="Ico_Recuperacion";	// per1
						$Grid[$i]["col"]="N6";
					}
					elseif($row[6] == "on")
						{
							$Grid[$i]["est"]="De alta";
							$Grid[$i]["ico"]="Ico_DeAlta";	// pea1
							$Grid[$i]["col"]="N7";
							$Grid[$i]["clase"]="tdAzulSinPadding";
						}
						elseif($row[2] == "on")
						{
							$Grid[$i]["est"]="En espera";
							$Grid[$i]["ico"]="Ico_SinIngreso";	// pes1
							$Grid[$i]["col"]="N3";
						}
						else
						{
							$Grid[$i]["est"]="Sin ingreso";
							$Grid[$i]["ico"]="Ico_SinIngreso";	// pen1
							$Grid[$i]["col"]="N2";
						}
			$Grid[$i]["ubi"]=strtoupper(substr($row['ubicacion'],0,1)) . strtolower(substr($row['ubicacion'],1));
		}
	}
	echo "<center><table id='tablaTurnos'>";
	//echo "<tr><td id=tipo11>TURNO</td><td id=tipo11B>IC</td><td id=tipo11A>ESTADO</td><td id=tipo11C>UBICACION</td><td id=tipoG11C></td><td id=tipo11>TURNO</td><td id=tipo11B>IC</td><td id=tipo11A>ESTADO</td><td id=tipo11C>UBICACION</td><td id=tipoG11C></td><td id=tipo11>TURNO</td><td id=tipo11B>IC</td><td id=tipo11A>ESTADO</td><td id=tipo11C>UBICACION</td><td id=tipoG11C></td><td id=tipo11>TURNO</td><td id=tipo11B>IC</td><td id=tipo11A>ESTADO</td><td id=tipo11C>UBICACION</td></tr>";
	//echo "<tr><td id=tipo11>TURNO</td><td id=tipo11B>IC</td><td id=tipo11A>ESTADO</td><td id=tipo11C>UBICACION</td><td id=tipoG11C></td><td id=tipo11>TURNO</td><td id=tipo11B>IC</td><td id=tipo11A>ESTADO</td><td id=tipo11C>UBICACION</td><td id=tipoG11C></td><td id=tipo11>TURNO</td><td id=tipo11B>IC</td><td id=tipo11A>ESTADO</td><td id=tipo11C>UBICACION</td></tr>";
	echo "<tr><th width='20%'>TURNO</th><th width='35%'>ESTADO</th><th width='35%'>UBICACI&Oacute;N</th><th width='10%'>IC</th></tr>";
	for ($i=0;$i<$cantFilas;$i++)
	{
		echo "<tr>";
		$tipo = "tipoG11";
		echo "<td class='".$Grid[$i]["clase"]."'>".$Grid[$i]["tur"]."</td>
			<td class='".$Grid[$i]["clase"]."'>".$Grid[$i]["est"]."</td>
			<td class='".$Grid[$i]["clase"]."'>".$Grid[$i]["ubi"]."</td>
			<td class='".$Grid[$i]["clase"]."'>
				<img width='60' height='60' src='/matrix/images/medical/tcx/".$Grid[$i]["ico"].".png'>
			</td>";
		/*	
		echo "<td id=tipoG11C></td>";
		$tipo = "tipoG11";
		echo "<td id=".$tipo."A".$Grid[$i+22]["col"].">".$Grid[$i+22]["tur"]."</td><td id=".$tipo."A".$Grid[$i+22]["col"]."><IMG SRC='/matrix/images/medical/TCX/".$Grid[$i+22]["ico"].".png'></td><td id=".$tipo."A".$Grid[$i+22]["col"].">".$Grid[$i+22]["est"]."</td><td id=".$tipo."A".$Grid[$i+22]["col"].">".$Grid[$i+22]["ubi"]."</td>";
		echo "<td id=tipoG11C></td>";
		$tipo = "tipoG11";
		echo "<td id=".$tipo."A".$Grid[$i+44]["col"].">".$Grid[$i+44]["tur"]."</td><td id=".$tipo."A".$Grid[$i+44]["col"]."><IMG SRC='/matrix/images/medical/TCX/".$Grid[$i+44]["ico"].".png'></td><td id=".$tipo."A".$Grid[$i+44]["col"].">".$Grid[$i+44]["est"]."</td><td id=".$tipo."A".$Grid[$i+44]["col"].">".$Grid[$i+44]["ubi"]."</td>";
		echo "<td id=tipoG11C></td>";
		$tipo = "tipoG11";
		echo "<td id=".$tipo."A".$Grid[$i+66]["col"].">".$Grid[$i+66]["tur"]."</td><td id=".$tipo."A".$Grid[$i+66]["col"]."><IMG SRC='/matrix/images/medical/TCX/".$Grid[$i+66]["ico"].".png'></td><td id=".$tipo."A".$Grid[$i+66]["col"].">".$Grid[$i+66]["est"]."</td><td id=".$tipo."A".$Grid[$i+66]["col"].">".$Grid[$i+66]["ubi"]."</td>";*/
		echo "</tr>";
	}
	echo "</table>";
	echo "<table id='lineaInf'><tr><td colspan='100%'></td></tr></table>";
//}
?>
</body>
</html>
