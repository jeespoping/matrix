<html>
<head>
<title>CENSO DIARIO DE PACIENTES</title>
</head>
<font face='arial'>
<BODY TEXT="#000066">
<?php
include_once("conex.php");
include_once("root/comun.php");
$wemp_pmla=$_REQUEST['wemp_pmla'];
$wcominf = consultarAliasPorAplicacion($conex, $wemp_pmla, "invecla");
/********************************************************
 *     REPORTE DE CENSO DIARIO DE PACIENTES		        *
 *														*
 *********************************************************/

//==================================================================================================================================
//PROGRAMA						:Reporte de censo diario de pacientes
//AUTOR							:Juan David Londoño
//FECHA CREACION				:FEBRERO 2006
//FECHA ULTIMA ACTUALIZACION 	:Agosto 1 de 2008 (Msanchez)
//DESCRIPCION					:Este reporte muestra el ceso de los pacientes que se encuentran en la clinica, consulta en los
//								 formularios cominf-000032 y cominf-000033.
//ACTUALIZACIONES				:
//								 2007-03-29: Se quitaron los egresos por traslado cuando se escoge la opcion 9999-todos los servicios
//								 2007-04-02: Se quitaron los egresos por traslado para calcular la tasa de moratalidad cuando
//											 escoge la opcion 9999-todos los servicios.
//								 2008-08-01 (Msanchez): Se toman todas las variables generadas por el reporte desde la tabla movhos_000038
//
//==================================================================================================================================

include_once("root/comun.php");

if(!isset($_SESSION['user']))
terminarEjecucion($MSJ_ERROR_SESION_CADUCADA);
else
{
	$key = substr($user,2,strlen($user));

	$conex = obtenerConexionBD("matrix");

	// COMPROBAR QUE LOS PARAMETROS ESTEN puestos(unidad, fecha inicial y fecha final)
	echo "<form action='' method=post>";


	$apMovhos = consultarAliasPorAplicacion($conex,$wemp_pmla,"movhos");
	$apTabcco = consultarAliasPorAplicacion($conex,$wemp_pmla,"tabcco");

	// ENCABEZADO
	if(!isset($unidad)  or !isset($fecha1) or !isset($fecha2))
	{
		echo "<center><table border=0 width=400>";
		echo "<tr><td align=center colspan=3><b>CLÍNICA MEDICA LAS AMERICAS </b></td></tr>";
		echo "<tr><td align=center colspan=3>CENSO DIARIO (NUEVO)</td></tr>";

		
		/*$query = "select cominf_000038.Historia_clinica, Num_ingreso, cominf_000039.Num_evento, Unidad, Fecha_evento, Hora_evento, Evento, Reporta, Descripcion, Enf_base, Personal_invol, Especialidad, Alto_riesgo, Edoconc, Urgente, Ap_similar, Creencia, No_adher, No_instru, Contardia, Cta_vol, Retraso_intercon, Retraso_dx, Retraso_tto, Barrera_cla, Barrera_eapb, Lejania, Retrazo_transp, Remision_tardia, Dx_ni, Tto_ni, Tto_omitido, Tto_cambio, Procmq_ni, Procmq_omitido, Procenf_omitido, Procenf_ni, Otro_ni, Otro_omitido, Alimento_ni, Identidad, Riesgo_noeval, Prev_noim, No_protocolo, Incumple_proto, Registro_insuf, Registro_confuso, Falla_plan, Orden_incum, Instrucc_insuf, Medicam_prepa, Medicam_uso, Curac_disp, Despla_na, Infraes_na, No_dispon, Malfunc, Maluso, No_especifica, Competencia, Supervision, Fatiga, Externa, Frec_md, Frec_enfer, Frec_otros, Novedad, Entrega_turno, Inter_servicios, Inter_ips, Remision_inadec, Clasificacion, Evitabilidad, Observa, Directa_muer, Basica_muer, Asociada_muer, Gestionado, Accion_mejora, Analisis_por  
				  from cominf_000038, cominf_000039   
				  where Fecha_evento between '2010-01-01' and '2010-06-30' 
				  and cominf_000038.Historia_clinica=cominf_000039.Historia_clinica"; 
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);	
		//echo mysql_errno() ."=". mysql_error();
		for ($j=0;$j<$num;$j++)
				{
					$row = mysql_fetch_array($err);
					echo $row[0]."|".$row[1]."|".$row[2]."|".$row[3]."|".$row[4]."|".$row[5]."|".$row[6]."|".$row[7]."|".$row[8]."|".$row[9]."|".$row[10]."|".$row[11]."|".$row[12]."|".$row[13]."|".$row[14]."|".$row[15]."|".$row[16]."|".$row[17]."|".$row[18]."|".$row[19]."|".$row[20]."|".$row[21]."|".$row[22]."|".$row[23]."|".$row[24]."|".$row[25]."|".$row[26]."|".$row[27]."|".$row[28]."|".$row[29]."|".$row[30]."|".$row[31]."|".$row[32]."|".$row[33]."|".$row[34]."|".$row[35]."|".$row[36]."|".$row[37]."|".$row[38]."|".$row[39]."|".$row[40]."|".$row[41]."|".$row[42]."|".$row[43]."|".$row[44]."|".$row[45]."|".$row[46]."|".$row[47]."|".$row[48]."|".$row[48]."|".$row[49]."|".$row[50]."|".$row[51]."|".$row[52]."|".$row[53]."|".$row[54]."|".$row[55]."|".$row[56]."|".$row[57]."|".$row[58]."|".$row[59]."|".$row[60]."|".$row[61]."|".$row[62]."|".$row[63]."|".$row[64]."|".$row[65]."|".$row[66]."|".$row[67]."|".$row[68]."|".$row[69]."|".$row[70]."|".$row[71]."|".$row[72]."|".$row[73]."|".$row[74]."|".$row[75]."|".$row[76];
					echo "<br>";
				}*/
			
					   
		
		
		echo "<table border=(1) align=center>";

		echo "<tr><td>Unidad:</td><td><select name='unidad'>";

		// este query me trae todos los centro de costos hospitalarios
		/*$query = "SELECT Ccocod, Cconom
						FROM ".$apTabcco." 
					   WHERE Ccouni = '2H' 
					   ORDER by 1";*/
					   
		
		//2010-04-12 se modifa este query para traer un cc hibrido
		$query = "select Ccocod, Cconom 
					from ".$apMovhos."_000011 
					where Ccourg='off' 
					and Ccohos='on' 
					and Ccoayu='off'"; 
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num>0)
		{
			echo "<option>TODOS LOS SERVICIOS</option>";
			for ($j=0;$j<$num;$j++)
			{
				$row = mysql_fetch_array($err);
				echo "<option>".$row[0]."-".$row[1]."</option>";
					
			}
		}
		echo "</select></td></tr>";
		echo "<tr><td>Fecha Inicial:</td><td><input type='TEXT' name='fecha1' size=10 maxlength=10>AAAA-MM-DD</td></tr>";
		echo "<tr><td>Fecha Final:</td><td><input type='TEXT' name='fecha2' size=10 maxlength=10>AAAA-MM-DD</td></tr>";
		echo "<tr><td colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		echo "</table>";


	}
	// estas son las validaciones para cuando se ponen fechas erradas
	else if($fecha1=='')
	{
		echo"<br>";
		echo"<br>";
		echo "<font size='6'color=#FF3300><i>DEBE INGRESAR UNA FECHA INICIAL DE CORTE</MARQUEE></FONT>";
	}
	else if($fecha2=='')
	{
		echo"<br>";
		echo"<br>";
		echo "<font size='6'color=#FF3300><i>DEBE INGRESAR UNA FECHA FINAL DE CORTE</MARQUEE></FONT>";
	}else if($fecha1>$fecha2)
	{
		echo"<br>";
		echo"<br>";
		echo "<font size='6'color=#FF3300><i>LA FECHA INICIAL DEBE SER MENOR QUE LA FECHA FINAL</MARQUEE></FONT>";
	}
	else
	{
		if ($unidad=="TODOS LOS SERVICIOS")
		{
			$servicio="%";
			$variable="";
			
			$q = "SELECT
			  		SUM(Cieing) Cieing, SUM(Cieegr) Cieegr, SUM(Ciedes) Ciedes, SUM(Ciemmay) Ciemmay, SUM(Ciemmen) Ciemmen, SUM(Cieinu) Cieinu, SUM(Cieinc) Cieinc, SUM(Cieina) Cieina, SUM(Cieint) Cieint, SUM(Ciegrt) Ciegrt, SUM(Ciedit) Ciedit, SUM(Ciediam) Ciediam, SUM(Cieeal) Cieeal 
			FROM 
				".$apMovhos."_000038 
			WHERE
				(Fecha_data BETWEEN '$fecha1' AND '$fecha2')
				AND Cieser LIKE '".$servicio."' ";
		}else
		{
			$vecCco=explode('-',$unidad);
			$servicio="'$vecCco[0]'";
			$variable="and Servicio='".$vecCco[0]."'";
			
			$q = "SELECT 			  	 
					SUM(Cieing) Cieing, SUM(Cieegr) Cieegr, SUM(Ciedes) Ciedes, SUM(Ciemmay) Ciemmay, SUM(Ciemmen) Ciemmen, SUM(Cieinu) Cieinu, SUM(Cieinc) Cieinc, SUM(Cieina) Cieina, SUM(Cieint) Cieint, SUM(Ciegrt) Ciegrt, SUM(Ciedit) Ciedit, SUM(Ciediam) Ciediam, SUM(Cieeal) Cieeal
				FROM 
					".$apMovhos."_000038 
				WHERE
					(Fecha_data BETWEEN '$fecha1' AND '$fecha2')
					AND Cieser = ".$servicio." ";
		}
		$rs = mysql_query($q,$conex);
		$numFilas = mysql_num_rows($rs);
			
		if($numFilas > 0){
			$resultado = mysql_fetch_array($rs);

			//Ingresos por urgencias
			$ingU = $resultado['Cieinu'];

			//Ingresos por admisiones
			$ingA = $resultado['Cieina'];

			//Ingresos por cirugia
			$ingC = $resultado['Cieinc'];

			//Ingresos por traslado
			$ingT = $resultado['Cieint'];

			//Egresos por alta
			$egrA = $resultado['Cieeal'];

			//Egresos por traslado
			$egrT = $resultado['Ciegrt'];

			//Egresos de muerte menor a 48 horas
			$egrMmen48 = $resultado['Ciemmen'];

			//Egresos de muerte mayor a 48 horas
			$egrMmay48 = $resultado['Ciemmay'];

			//Dias estancia traslados
			$diasTraslado = $resultado['Ciedit'];

			//Dias estancia altas y muertes
			$diasAltasMuertes = $resultado['Ciediam'];

			//Dias estancia totales
			$diasTotales = $resultado['Ciedes'];
		}

		//Total ingresos
		$ingTotales = $ingU+$ingA+$ingC;
			
		// QUEDA PENDIENTE LO DE LA FUGA
		$query = "SELECT * FROM ".$wcominf."_000033  WHERE (Fecha_egre_serv between '$fecha1' and '$fecha2')
		and Tipo_egre_serv ='06-Voluntaria o fuga' ".$variable."";
		$err = mysql_query($query,$conex);
		//echo mysql_errno() ."=". mysql_error();
			
		$numfu = mysql_num_rows($err);
		if($numfu==0)
		{
			$numfu=0;
		}
			
		if ($numfu>0)
		{
			$totfu=0;
			for ($i=0;$i<$numfu;$i++)
			{
				$funum = mysql_fetch_array($err);
				$totfu=$totfu+$funum['Dias_estan_serv'];
			}
		}else{
			$totfu=0;
		}

		//Total egresos
		$egrTotales = $egrA+$egrMmay48+$egrMmen48+$numfu;

		//Dias totales estancia con dias fuga
		$diasTotales += $totfu;
			
		//Promedio estancia:  Total dias estancia / Total numero de egresos
		//Tasa de mortalidad: Total muertes / Total numero de egresos
		$promEstancia = 0;
		$tasaMortalidad = 0;
		if($egrTotales > 0){
			$promEstancia = round(($diasTotales / ($egrA+$egrMmay48+$egrMmen48+$egrT)),2);
			$tasaMortalidad = ($egrMmay48+$egrMmen48) / $egrTotales;
		}


		/* IMPRESION DE LOS DATOS EN PANTALLA*/
		echo "<table border=0 align=center >";
		echo "<tr><td align=center rowspan=2 colspan=3><img SRC='/MATRIX/images/medical/root/clinica.jpg'></td>";
		echo "<tr><td align=center colspan=7><font size='6'color=#000066><i>CENSO DIARIO DE PACIENTES<br><font size='1'>Ver. 2007-07-13</font></td></tr>";
		echo "</table>";
		echo "<table border=1 align=center bgcolor=#DDDDDD>";
		echo "<tr><td align=center COLSPAN=2><font size='4'>Servicio:</td><td COLSPAN=3> ".$unidad."</td></tr>";
		echo "<tr><td align=center COLSPAN=2><font size='4'>Fechas:</td><td COLSPAN=3> <i>Desde</i> <b>".$fecha1."</b> <i>hasta</i><b> ".$fecha2."</b></td></tr>";
		echo "</table>";
		echo"<br>";
		echo "<table border=1 align=center bgcolor=#99CCFF>";
		echo "<tr><td align=center colspan=4><font size='2'>INGRESOS</td><td align=center rowspan=2><font size='2'>INGRESOS POR TRASLADOS</td>
			<td align=center colspan=5><font size='2'>EGRESOS</td><td align=center rowspan=2><font size='2'>EGRESOS POR TRASLADOS</td>
			<td align=center rowspan=2><font size='2'>DIAS ESTANCIA</td><td align=center rowspan=2><font size='2'>DIAS TRASLADOS</td></tr>";
		echo "<tr><td>Urgencias</td><td>Admisiones</td><td>Cirugia</td><td><b>Total</td><td>Alta</td><td>Muerte -48h</td>
			<td>Muerte +48h</td><td>Fuga</td><td><b>Total</td></tr>";
		echo "<tr><td align=center>".$ingU."</td><td align=center>".$ingA."</td><td align=center>".$ingC."</td><td align=center>".($ingU+$ingA+$ingC)."</td><td align=center>".$ingT."</td>
			<td align=center>".$egrA."</td><td align=center>".$egrMmen48."</td><td align=center>".$egrMmay48."</td><td align=center>".$numfu."</td><td align=center>".($egrA+$egrMmay48+$egrMmen48)."</td><td align=center>".$egrT."</td>
			<td>".$diasTotales."</td><td>".$diasTraslado."</td></tr>";
		echo "</table>";
		echo"<br>";
		echo"<br>";
		echo "<table border=0 align=center >";
		echo "<tr><td align=center colspan=7><font size='5'color=#000066><i>INDICADORES</td></tr>";
		echo "</table>";
		echo"<br>";
		echo "<table border=1 align=center bgcolor=#DDDDDD>";
		echo "<tr><td align=center COLSPAN=2><font size='4'>Promedio estancia:</td><td COLSPAN=3> ".$promEstancia." dias</td></tr>";
		echo "</table>";
		echo"<br>";
		echo "<table border=1 align=center bgcolor=#DDDDDD>";
		echo "<tr><td align=center COLSPAN=2><font size='4'>Tasa de Mortalidad:</td><td COLSPAN=3> ".number_format($tasaMortalidad,2,'',".")."%</td></tr>";
		echo "</table>";
	}
	liberarConexionBD($conex);
}
?>
</BODY>
</font>
</html>