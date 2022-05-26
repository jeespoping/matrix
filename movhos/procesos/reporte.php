<html>
<head>
<title>REPORTE FACTURACION</title>
<style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
    	<!--Fondo Azul no muy oscuro y letra blanca -->
    	.tituloSup{color:#006699;background:#FFFFF;font-family:Arial;font-weight:bold;text-align:center;font-size:11pt;}
    	.tituloSup1{color:#57C8D5;background:#FFFFF;font-family:Arial;font-weight:bold;text-align:center;}
    	.titulo1{color:#FFFFFF;background:#006699;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;}
    	<!-- -->
    	.titulo2{color:#003366;background:#57C8D5;font-size:9pt;font-family:Arial;font-weight:bold;text-align:left;}
    	.titulo3{color:#003366;background:#A4E1E8;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto{color:#006699;background:#CCFFFF;font-size:9pt;font-family:Tahoma;text-align:center;}
    	.textoPeque{color:#006699;background:#CCFFFF;font-size:8pt;font-family:Tahoma;text-align:center;}
    	.acumulado1{color:#003366;background:#FFCC66;font-size:11pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.acumulado2{color:#003366;background:#FFDBA8;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.errorTitulo{color:#FF0000;font-size:11pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.textoDev{color:#003366;background:#FFDBA8;font-size:9pt;font-family:Tahoma;text-align:center;}
    	
    	
    	
    	.alert{background:#FFFFAA;color:#FF9900;font-size:11pt;font-family:Arial;text-align:center;}
    	.warning{background:#FFCC99;color:#FF6600;font-size:11pt;font-family:Arial;text-align:center;}
    	.error{background:#FFAAAA;color:#FF0000;font-size:11pt;font-family:Arial;text-align:center;}
    	    	
    </style>
</head>
<BODY TEXT="#000066" BGCOLOR="#FFFFFF">

<?php
$wemp_pmla = $_REQUEST['wemp_pmla'];

include_once("conex.php");
include_once("root/comun.php");

/****************************************************
*		REPORTE DE MEDICAMENTOS CARGADOS Y			*
*				DEVULETOS POR PDA					*
*****************************************************/

//==================================================================================================================================
//GRUPO						:PDA
//AUTOR						:Ana María Betancur V.
$wautor="Ana María Betancur V.";
//FECHA CREACIÓN			:2004-segundo semestre
//FECHA ULTIMA ACTUALIZACIÓN 	:
$wactualiz="(Versión 2006-07-04)";
//DESCRIPCIÓN					: Muestra en pantalla los artculos cargados o devueltos a un paciente.
//								  El reporte muestra los articulos grabados o devueltos a un paciente durante un turno. Se toma el
//								  turno de dia de las 7am a las 18:59:59 del mismo día y del de noche de las 19:00:00 del día que dice
//								  la fecha y las 06:59:59 del d{ia siguiente.
//								  Recibe como parametros el tipo de fuente (grabar o devolver), el cc, el Turno (DIA, NOCHE),
//								  El Paciente, La fecha.
//								  Con estos parametros busca dentro de las tablas farmpda_000001, farmpda_000003, los medicamentos
//								  ingresados o devueltos y hace un consolidado pata cada tipo de fuente.
//								  Tambien busca en la tabla farmpda_000002 si los medicamentos requieren justificacion, formula ,
//								  ninguno o ambos, pas subrayarlos en pantalla con un color especifico segun su condicion.
//								  Ademas con los codigos de los medicamentos trae las unidades correspondientes a cada uno del UNIX.
//
//------------------------------------------------------------------------------------------------------------------------------------------
//ACTUALIZACIONES
//  2012-03-01
//		Se corrige el reporte para el mes de febrero, no tenia en cuenta el dia 30 de febrero que no es fecha valida
//	2006-07-04
//		Se cambia $tipGrab por $tipTrans y $fuente donde sea necesario
//		Se modifica el query que busca los centros de costos para que vaya a la tabla farmpda_000014
//	2006-05-04
//		Se inserta el include pda/socket.php para que funcione por fuera de UNIX.
//	2006-04-11
//		Modificaciónde Clinica Medica Las Americas a Clinica Las Americas
//		Se documenta.
//	2006-04-10
//		Se cambia el query de farmpda_000001 a costosyp_00005 para buscar los centros de costos
//
//------------------------------------------------------------------------------------------------------------------------------------------
//TABLAS
//	farmpda_000001
//	ivart
//	farmpda_000003
//	farmpda_000014
/**
 * @modified 2007-07-10 Se modifica el $fenHora, pues en este no deben influir las horas del turno, solo se buscan los registros por fecha y ya en el query de fde se limitan las horas de los registros.
 */
include_once("movhos/validacion_hist.php");



$wactualiz = '2022-02-16';
$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
$wbasedato1 = strtolower( $institucion->baseDeDatos );
encabezado("MEDICAMENTOS Y MATERIAL",$wactualiz, $wbasedato1);

// COMPROBAR QUE LOS PARAMETROS ESTEN puestos(paciente medico y fecha)

if(!isset($year1)  or !isset($cc) or !isset($pac['ing']) or !isset($turno))
{

	$ini1=strpos($cc,"-");
	if($ini1>0)
	$cc=substr($cc,0,$ini1);

	echo "<form action='' method=post>";
	echo "<input type='hidden' name ='usuario' value='".$usuario."' >";

	echo "<center><table border=0 width=300>";
	// echo "<tr><td align='center' colspan='2' class='tituloSup'>MEDICAMENTOS Y MATERIAL</td></tr>";
	// echo "<tr></tr>";
	echo "<tr><td  class='titulo2'>CC: </td>";
	echo "<td class='titulo2'><select name='cco[cod]' >";
	$q = "SELECT Ccocod, Cconom  "
	."      FROM ".$bd."_000011 "
	."     WHERE Ccofac = 'on' "
	."       AND Ccoest = 'on'";
	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);
	if($num>0)
	{
		for ($j=0;$j<$num;$j++)
		{
			$row = mysql_fetch_array($err);
			if (($row[0]) == $cc)
			echo "<option selected>".$row[0]."</option>";
			else
			echo "<option>".$row[0]."</option>";
		}
	}	// fin del if $num>0
	echo "</select></td></tr>";
	echo "<tr><td class='titulo2'>TURNO: </font></td>";
	echo "<td class='titulo2'><select name='turno'>";
	echo "<option>01-DIA</option>";
	echo "<option>02-NOCHE</option>";
	echo "</select></td></tr>";
	echo "<tr><td  class='titulo2'>DESDE: </font></td>";
	echo "<td  class='titulo2'>";
	if(!isset($year1))
	{
		$year1=date('Y');
		$month1=date('m');
		$day1=date('d');
	}
	echo " <select name='year1' >";
	for($f=2004;$f<2051;$f++)
	{
		if($f == $year1)
		echo "<option selected>".$f."</option>";
		else
		echo "<option>".$f."</option>";
	}
	echo "</select> <select name='month1' >";
	for($f=1;$f<13;$f++)
	{
		if( $f == $month1)
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
	echo "</select> <select name='day1'>";
	for($f=1;$f<32;$f++) {
		if($f == $day1)
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

	echo "<tr><td  class='titulo2'>HISTORIA: </font></td>";
	echo "<td  class='titulo2'><input type='text' name='pac[his]' size='8' value='".$pac['his']."'></td></tr>";
	echo "<tr><td  class='titulo2'>INGRESO: </font></td>";
	echo "<td  class='titulo2'><input type='text' size='3' name='pac[ing]' value='".$pac['ing']."'></td></tr>";
	//echo "<input type='text' name='tipTrans' value='$tipTrans'>";
	echo"<tr><td class='titulo2' colspan='2' align='center'><center><input type='submit' value='ACEPTAR'></center></td></tr></form>";
}else {

	$fecha1=$year1."-".$month1."-".$day1;
	if(!infoPaciente($pac, $wemp_pmla))
	$pac['nom']="";
	/*	$ini1=strpos($pac,"-");
	$hist=substr($pac,0,$ini1);
	$pac=substr($pac,$ini1+1);
	*/
	
	if($turno == "02-NOCHE") { 
		$day1++;
		
		if ($day1 =='30' and $month1 =='2')	{
			if(!checkdate($month1,$day1,$year1)) {
				$day1='01';
				$month1='03';
			}
		}
		
		if ($day1 =='29' and $month1 =='2')	{
			if(!checkdate($month1,$day1,$year1)) {
				$day1='01';
				$month1='03';
			}
		}

		if (($day1 == '31' or $day1=='32') and !checkdate($month1,$day1,$year1)) {
			//echo "entro";
			$day1='01';
			$month1=(integer)$month1 +1;
			if($month1 < 10)
			$month1="0".$month1;
			else if ($month1 == '13') {
				$month1='01';
				$year1++;
			}
		}

		if ((integer)($day1) =='32') {
			$day1='01';
			$month1=(integer)$month1 +1;
			if($month1 < 10)
			$month1="0".$month1;
			else if ($month1 == '13') {
				$month1='01';
				$year1++;
			}
		}

		$fecha2=$year1."-".$month1."-".$day1;
		$fenHora="(".$bd."_000002.Fecha_data='".$fecha1."'   OR ".$bd."_000002.Fecha_data='".$fecha2."') ";
		$fdeHora="((".$bd."_000003.Fecha_data='".$fecha1."'  AND ".$bd."_000003.Hora_data between '19:00:00' AND '24:00:00') OR (".$bd."_000003.Fecha_data='".$fecha2."'  AND ".$bd."_000003.Hora_data between '00:00:00' AND '06:59:00')) ";
		$desde="DESDE 19:00 DE ".$fecha1." HASTA LAS 06:59 DE ".$fecha2;
	}else{
		$fenHora="".$bd."_000002.Fecha_data='".$fecha1."' ";
		$fdeHora="".$bd."_000003.Fecha_data='".$fecha1."' AND ".$bd."_000003.Hora_data between '07:00:00' AND  '18:59:59'";
		$desde="DESDE 07:00 HASTA LAS 18:59 DE ".$fecha1;
	}

	/* IMPRESION DE LOS DATOS EN PANTALLA*/
	echo "<table border='0' align='center' width='300'>";
	//echo "<tr><td  align=center ><font size=2 color='#000080' face='arial'><B>RREPORTE MATERIAL Y MEDICAMENTOS</b></font>";
	echo "</tr><tr><td  align=center ><font size=2 color='#000080' face='arial'><B>REPORTE ".$desde."</B></td>";
	echo "</tr><tr><td  align=center colspan='4'><font size=2 color='#000080' face='arial'><B>".$pac['his']."-".$pac['ing']." ".$pac['nom']."</b></font>";
	//echo "</tr><tr><td  align=center colspan='4'><font size=2 color='#000080' face='arial'>".$pac['nom']."</font>";
	echo "</tr><tr><td  align=center ><font size=2 color='#000080' face='arial'><B>CENTRO DE COSTOS: ".$cc."</B></font>";


	$facfen="repDiarioFacfen".date("Yis");
	$q = "CREATE TEMPORARY TABLE ".$facfen." "
	."     SELECT Fenfue, Fennum, Fencco, Fentip  "
	."       FROM ".$bd."_000002 "
	."      WHERE Fenhis = '".$pac['his']."' "
	."        AND Fening = '".$pac['ing']."' "
	."        AND Fenest = 'on' "
	."        AND ".$fenHora." "
	."        AND Fencco = '".$cco['cod']."' ";
	$err = mysql_query($q,$conex);
	
	$facfde="repDiarioFacfde".date("Yis");
	$q = " CREATE TEMPORARY TABLE ".$facfde." "
	."     SELECT Fenfue, Fennum, Fentip, Fdeart, SUM(Fdecan) as Fdecan  "
	."       FROM ".$facfen.", ".$bd."_000003 "
	."      WHERE Fdenum = Fennum "
	."        AND ".$fdeHora." "
	."        AND Fdeest = 'on' "
	." GROUP BY Fentip, Fdenum, Fdeart    ";
	$err = mysql_query($q,$conex);
	echo mysql_error();

	$q = " SELECT Fenfue, Fennum,  Fentip, Fdeart, Fdecan, Artcom, Artuni  "
	."       FROM ".$facfde.", ".$bd."_000026 "
	."      WHERE Artcod = Fdeart "
	."   ORDER BY Fentip, Artcom ";
	$err = mysql_query($q,$conex);
	echo mysql_error();
	$num = mysql_num_rows($err);
	
	if($num>0)
	{
		//echo "<tr>";
		$tipT="";
		$aprov="";
		for ($j=0;$j<$num;$j++)
		{ 
			$row = mysql_fetch_array($err);
			if(substr($row['Fentip'],0,1) != $tipT)
			{
				$tipT=substr($row['Fentip'],0,1);
				if($j!=0)
				{
					echo "</table>";
				}

				echo "</br><tr><td><table border='0' width='295' align='center'>";
				if($tipT == "C")
				{
					echo "<tr><td class='tituloSup' colspan='2'>ARTÍCULOS CARGADOS</td></tr>";
					$classTitulo='titulo1';
					$classTitulo2='titulo2';
					$classTexto='texto';
				}
				else
				{
					echo "<tr><td class='errorTitulo' colspan='2'>ARTÍCULOS DEVUELTOS</td></tr>";
					$classTitulo='acumulado1';
					$classTitulo2='acumulado2';
					$classTexto='textoDev';
				}
				$aprov="";
			}
			if($aprov != substr($row['Fentip'],1,1))
			{
				$aprov=substr($row['Fentip'],1,1);

				if($aprov == "A")
				{
					echo "<tr><td class='".$classTitulo."' colspan='2'>APROVECHAMIENTO</td></tr>";
				}
				else
				{
					echo "<tr><td class='".$classTitulo."' colspan='2'>SENCILLO</td></tr>";
				}

				echo "<tr>";
				echo "<td class='".$classTitulo2."'>Cant.</td>";
				echo "<td class='".$classTitulo2."' align='center'>Artículo.</td>";
				echo "</tr>";
			}

			echo "<TR><td class='".$classTexto."'>".$row['Fdecan']." ".$row['Artuni']."</td><td class='".$classTexto."'>".$row['Artcom']." <b>(".$row['Fdeart'].")</td></tr>";
		}
		echo "</table></td></tr>";

		echo "<tr><td align='center'><font color=#000066 face='arial'><b><A HREF='cargos.php?usuario=".$usuario."&tipTrans=".$tipTrans."&wemp_pmla=".$wemp_pmla."&bd=movhos'>Retornar con Usuario</a> </font><br>";
		echo "<tr><td align='center'><font color=#000066 face='arial'><b><A HREF='cargos.php?tipTrans=".$tipTrans."&wemp_pmla=".$wemp_pmla."&bd=movhos'>Retornar</a> </font><br>";
	}
}
?>
</body>
</html>