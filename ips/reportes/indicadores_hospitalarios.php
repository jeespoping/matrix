<head>
<title>INDICADORES HOSPITALARIOS</title>
<link href="/matrix/root/caro.css" rel="stylesheet" type="text/css" />

<!-- Loading Theme file(s) -->
<link rel="stylesheet" href="../../zpcal/themes/winter.css" />

<!-- Loading Calendar JavaScript files -->
<script type="text/javascript" src="../../zpcal/src/utils.js"></script>
<script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
<script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>

<!-- Loading language definition file -->
<script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>
<style type="text/css">
//
body {
	background: white url(portal.gif) transparent center no-repeat scroll;
}

#tipo1 {
	color: #000066;
	background: #FFFFFF;
	font-size: 7pt;
	font-family: Tahoma;
	font-weight: bold;
}

#tipo2 {
	color: #000066;
	background: #FFFFFF;
	font-size: 7pt;
	font-family: Tahoma;
	font-weight: bold;
}

.tipo3 {
	color: #000066;
	background: #FFFFFF;
	font-size: 7pt;
	font-family: Tahoma;
	font-weight: bold;
	text-align: center;
}

.tipo4 {
	color: #000066;
	background: #dddddd;
	font-size: 7pt;
	font-family: Tahoma;
	font-weight: bold;
	text-align: center;
}

.tipo5 {
	color: #000066;
	background: #99CCFF;
	font-size: 7pt;
	font-family: Tahoma;
	font-weight: bold;
	text-align: center;
}

.tipo6 {
	color: #000066;
	background: #dddddd;
	font-size: 7pt;
	font-family: Tahoma;
	font-weight: bold;
	text-align: center;
}

.tipo7 {
	color: #000066;
	background: #999999;
	font-size: 7pt;
	font-family: Tahoma;
	font-weight: bold;
	text-align: center;
}
</style>
</head>
<body onload=ira()>
<script type="text/javascript">
	function enter()
	{
	 document.forms.indhosp.submit();
	}
	
	function cerrarVentana()
	 {
      window.close()		  
     }
</script>
<?php
include_once("conex.php");
include_once("root/comun.php");

/*******************************************************************************************
 *          INDICADORES HOSPITALARIOS          											 *
 * 																						 *
 * Actualizado: 04-Ago-2008 (Msanchez):  Se modifica reporte para que apunte a la tabla 38 *
 *******************************************************************************************/
if (!isset($user))
	if(!isset($_SESSION['user']))
		session_register("user");

if(!isset($_SESSION['user']))
	terminarEjecucion($MSJ_ERROR_SESION_CADUCADA);
else{
	$conex = obtenerConexionBD("matrix");
	//=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
	$wactualiz="(Versión Agosto 04 de 2008)";                         // Aca se coloca la ultima fecha de actualizacion de este programa //
	//=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
	$wfecha=date("Y-m-d");
	$whora = (string)date("H:i:s");
	echo "<br>";
	echo "<br>";
	if(!isset($wemp_pmla)){
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
	}

	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
	$winstitucion = $institucion->nombre;

	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$wtabcco = consultarAliasPorAplicacion($conex, $wemp_pmla, "tabcco");

	echo "<div id='header'>";
	echo "<div id='logo'>";
	echo "<h1><a href='estadisticas_altas.php'>INDICADORES HOSPITALARIOS</a></h1>";
	echo "<h2><b>".$winstitucion."</b>".$wactualiz."</h2>";
	echo "</div>";
	echo "</div></br>";

	//FORMA ================================================================
	echo "<form name='indhosp' action='indicadores_hospitalarios.php' method=post>";

	echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
	if (strpos($user,"-") > 0)
	$wusuario = substr($user,(strpos($user,"-")+1),strlen($user));

	//===============================================================================================================================================
	//ACA COMIENZA EL MAIN DEL PROGRAMA
	//===============================================================================================================================================
	if (!isset($wfec_i) or trim($wfec_i) == "" or !isset($wfec_f) or trim($wfec_f) == "" or !isset($wcco) or trim($wcco) == "" )
	{
		echo "<center><table>";

		echo '<div id="page" align="center">';
		echo '<div id="feature" class="box-pink" align="center">';
		echo '<h2 class="section" colspan=1><b>SELECCIONE EL CENTRO DE COSTO:</b></h2>';
		echo '<div class="content">';
			
			
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$q = " SELECT ".$wtabcco.".ccocod, ".$wtabcco.".cconom "
		."   FROM ".$wtabcco.", ".$wbasedato."_000011"
		."  WHERE ".$wtabcco.".ccocod = ".$wbasedato."_000011.ccocod ";
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);

		echo "<tr><td align=center colspan=2><select name='wcco'>";
		echo "<option>% - Todos</option>";
		for ($i=1;$i<=$num;$i++)
		{
			$row = mysql_fetch_array($res);
			echo "<option>".$row[0]." - ".$row[1]."</option>";
		}
		echo "</select></td></tr>";
			
			
		echo "<tr></tr><tr></tr>";

		echo "<tr>";
		echo "<td bgcolor='#dddddd' align=center><b>Fecha Inicial</b></td>";
		echo "<td bgcolor='#dddddd' align=center><b>Fecha Final</b></td>";
		echo "</tr>";

		if (!isset($wfec_i))
		$wfec_i=date("Y-m-d");
		$cal="calendario('wfec_i','1')";
		echo "<tr>";
		echo "<td bgcolor='#dddddd' align=center><input type='TEXT' name='wfec_i' size=10 maxlength=10  id='wfec_i' readonly='readonly' value=".$wfec_i." class=tipo3><button id='trigger1' onclick=".$cal.">...</button></td>";
		?>
		  <script type="text/javascript">//<![CDATA[
	       Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfec_i',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	    	//]]></script>
		<?php
			
		if (!isset($wfec_f))
		$wfec_f=date("Y-m-d");
		$cal="calendario('wfec_f','1')";
		echo "<td bgcolor='#dddddd' align=center><input type='TEXT' name='wfec_f' size=10 maxlength=10  id='wfec_f' readonly='readonly' value=".$wfec_f." class=tipo3><button id='trigger2' onclick=".$cal.">...</button></td>";
		?>
		<script type="text/javascript">//<![CDATA[
	       Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfec_f',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	    //]]></script>
		<?php
		echo "</tr>";
		echo "<tr><td align=center bgcolor=cccccc colspan=2></b><input type='submit' value='ENTRAR'></b></td></tr></center>";
		echo "</table>";
}else{
	echo "<br><br>";
	echo "<center><table>";
	echo "<th align=center colspan=29><font size=4><b>INDICADORES HOSPITALARIOS POR SERVICIO</b></font></th>";
	echo "<tr>";
	echo "<th align=center bgcolor=66CCCC colspan=14><b>Fecha Inicial</b></th>";
	echo "<th align=center bgcolor=66CCCC colspan=15><b>Fecha Final</b></th>";
	echo "</tr>";

	echo "<tr>";
	echo "<td align=center bgcolor=33FFFF colspan=14><font size=4><b>".$wfec_i."</b></font></td>";
	echo "<td align=center bgcolor=33FFFF colspan=15><font size=4><b>".$wfec_f."</b></font></td>";
	echo "</tr>";

	$wcco1 = explode("-",$wcco);

	//==========================================================================================================
	//ACA TRAIGO LOS DATOS DE LA TABLA ** CIERRE DIARIO DE CAMAS **
	//==========================================================================================================
	$q = " SELECT cieser, sum(ciedis), sum(cieocu), sum(cieing), sum(cieegr), sum(cieiye), sum(ciedes), sum(Ciemmay), sum(Ciemmen), sum(Cieinu), sum(Cieinc), sum(Cieina), sum(Cieint), sum(Ciegrt), sum(Ciedit), sum(Ciediam), sum(Cieeal), cconom "
	."   FROM ".$wbasedato."_000038 A, ".$wtabcco
	."  WHERE A.fecha_data BETWEEN '".$wfec_i."' AND '".$wfec_f."'"
	."    AND cieser       LIKE '".trim($wcco1[0])."'"
	."    AND cieser       = ccocod "
	."  GROUP BY cieser, cconom "
	."  ORDER BY cieser ";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);
	//==========================================================================================================

	if ($num > 0)
	{
		$wletra=1;   //Aca se define el tamaño de la letra en pantalla
		echo "<tr>";
		echo "<th rowspan=2 colspan=2><font size=".$wletra.">Servicio</font></th>";
		echo "<th colspan=4><font size=".$wletra.">Ingresos</font></th>";
		echo "<th rowspan=2><font size=".$wletra.">Ingresos por traslado</font></th>";		
		echo "<th rowspan=2><font size=".$wletra.">Ingresos y egresos dia</font></th>";
		echo "<th colspan=4><font size=".$wletra.">Egresos</font></th>";
		echo "<th rowspan=2><font size=".$wletra.">Egresos por traslado</font></th>";
		echo "<th colspan=2><font size=".$wletra.">Dias estancia</font></th>";
		echo "<th rowspan=2><font size=".$wletra.">Pacientes día ant.</font></th>";
		echo "<th rowspan=2><font size=".$wletra.">Pacientes a la fecha</font></th>";
		echo "<th rowspan=2><font size=".$wletra.">Nro Camas</font></th>";
		echo "<th rowspan=2><font size=".$wletra.">Días Cama Disponible</font></th>";
		echo "<th rowspan=2><font size=".$wletra.">Días Cama Ocupada</font></th>";		
		echo "<th rowspan=2><font size=".$wletra.">Prom. Camas Ocupadas</font></th>";		
		echo "<th rowspan=2><font size=".$wletra.">Días de<br>Estancia</font></th>";
		echo "<th rowspan=2><font size=".$wletra.">% de Ocupacion</font></th>";
		echo "<th rowspan=2><font size=".$wletra.">Promedio Dias<br>Estancia</font></th>";
		echo "<th rowspan=2><font size=".$wletra.">Rendimiento<br>Hospitalario</font></th>";
		echo "<th rowspan=2><font size=".$wletra.">Indice de<br>Sustitución</font></th>";
		echo "<th rowspan=2><font size=".$wletra.">Tasa de <br>Mortalidad</font></th>";
		echo "<th rowspan=2><font size=".$wletra.">Tasa Mortalidad<br> > 48 Horas</font></th>";
		echo "<th rowspan=2><font size=".$wletra.">Tasa Mortalidad<br> < 48 Horas</font></th>";		
		echo "</tr>";
		echo "<tr>";
		echo "<th><font size=".$wletra.">Urgencias</font></th>";
		echo "<th><font size=".$wletra.">Admisiones</font></th>";
		echo "<th><font size=".$wletra.">Cirugia</font></th>";
		echo "<th><font size=".$wletra.">Total</font></th>";				
		echo "<th><font size=".$wletra.">Altas</font></th>";
		echo "<th><font size=".$wletra.">Muertes > 48 Horas</font></th>";
		echo "<th><font size=".$wletra.">Muertes < 48 Horas</font></th>";
		echo "<th><font size=".$wletra.">Total</font></th>";
		echo "<th><font size=".$wletra.">Altas y muertes</font></th>";
		echo "<th><font size=".$wletra.">Egresos por traslado</font></th>";
		echo "</tr>";

		$wsalantcli=0;
		$ingU = 0;
		$sumaIngU = 0;
		$ingC = 0;
		$sumaIngC = 0;
		$ingA = 0;
		$sumaIngA = 0;
		$ingT = 0;
		$sumaIngT = 0;
		$wingcli=0;
		$egrA = 0;
		$sumaEgrA = 0;
		$egrT = 0;
		$sumaEgrT = 0;
		$wmen48cli = 0;
		$wmay48cli = 0;
		$wegrcli=0;
		$wiyecli=0;
		$wdescli=0;
		$sumaMMay=0;
		$sumaMMen=0;
		$wtdcocli=0;
		$wtdcdcli=0;
		$wsalant=0;
		$wtdecli=0;	
		$wnumcamcli=0;
		$wpacactcli=0;
		$diasTraslado=0;
		$sumaDiasTraslado=0;
		$diasAltasMuertes=0;
		$sumaDiasAltasMuertes=0;

		$wtdcocli=0;
		$wncamcli=0;
		$wpcoccli=0;

		for ($i=1;$i<=$num;$i++)
		{
			$row = mysql_fetch_array($res);

			if (is_integer($i/2))
			$wcolor="33FFFF";
			else
			$wcolor="99FFFF";
			
			//cieser, sum(ciedis), sum(cieocu), sum(cieing), sum(cieegr), sum(cieiye), sum(ciedes), sum(Ciemmay), sum(Ciemmen), sum(Cieinu), sum(Cieinc), sum(Cieina), sum(Cieint), sum(Ciegrt), sum(Ciedit), sum(Ciediam), sum(Cieeal), cconom
			$wser=$row[0];   //Centro de costo
			$wdis=$row[1];   //Camas desocupadas
			$wocu=$row[2];   //Camas Ocupadas			
			$wing=$row[3];   //Ingresos
			$wegr=$row[4];   //Egresos
			$wtotegr = $row[4]; //Egresos totales
			$wiye=$row[5];   //Ingresos y Egresos del mismo día
			$wdes=$row[6];   //Días de estancia de los egresos
			$wmay48=$row[7]; //Muertes mayores a 48 horas
			$wmen48=$row[8]; //Muertes menores a 48 horas
			$ingU = $row[9]; //Ingresos urgencias			
			$ingC = $row[10];//Ingresos cirugía
			$ingA = $row[11];//Ingresos admisiones
			$ingT = $row[12];//Ingresos por traslado
			$egrT = $row[13];//Egresos por traslado
			$diasTraslado = $row[14]; //Dias estancia por traslado
			$diasAltasMuertes = $row[15]; //Dias estancia por altas y muertes
			$egrA = $row[16];//Egresos altas
			$wnse=$row['cconom'];   //Nombre del Centro de Costos
			$totalCamas = 0;
			
			$sumaIngU+=$ingU;
			$sumaIngA+=$ingA;
			$sumaIngC+=$ingC;
			$sumaIngT+=$ingT;
			$sumaEgrA+=$egrA;
			$sumaEgrT+=$egrT;
			$sumaMMay+=$wmay48;
			$sumaMMen+=$wmen48;
			$sumaDiasTraslado+=$diasTraslado;
			$sumaDiasAltasMuertes+=$diasAltasMuertes;
			$wingcli=$wingcli+$wing;  //Total Ingresos Clinica
			$wegrcli=$wegrcli+$wegr;  //Total Egresos Clinica
			$wiyecli=$wiyecli+$wiye;  //Total Ingresos y Egresos del mismo día Clinica
			$wdescli=$wdescli+$wdes;  //Total Días de Estancia Clinica	

			//==========================================================================================================
			//Calculo los días entre las fechas dadas
			//==========================================================================================================
			$q = " SELECT DATEDIFF('".$wfec_f."','".$wfec_i."') "
			."   FROM ".$wbasedato."_000038 "
			."  WHERE id = 1 ";
			$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$row1 = mysql_fetch_array($res1);
			$wdias=$row1[0]+1;
			//==========================================================================================================


			//==========================================================================================================
			//ACA TRAIGO LOS DATOS DE LA TABLA ** CIERRE DIARIO DE CAMAS ** DEL DIA ANTERIOR A LA FECHA INICIAL
			//==========================================================================================================
			$q = " SELECT sum(cieocu) "
			."   FROM ".$wbasedato."_000038 "
			."  WHERE fecha_data = DATE_SUB('".$wfec_i."', INTERVAL 1 DAY) "  //Aca resto un dia de la fecha actual
			."    AND cieser like '".trim($wser)."'";
			$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num1 = mysql_num_rows($res);
			if ($num1 > 0)
			{
				$row1 = mysql_fetch_array($res1);
				$wsalant=$row1[0];
				$wsalantcli=$wsalantcli+$row1[0];
			}
			//==========================================================================================================


			//===========================================================================================================================================
			//PORCENTAJE DE OCUPACION
			//===========================================================================================================================================
			//% ocupacional = ((Total dias cama ocupada/Dias cama disponible) x 100)
			//$tdco = ($wsalant+($wing-$wegr)+$wiye);       //Total Días Cama Ocupada    = (Ingresos - Egresos + (Pacientes que Ingresaron y Egresaron el mismo día))
			$tdco=$row[2]+$wiye;
			$tdcd = ($wdis+$wocu);                        //Total Días Cama Disponible = Total días cama disponible (Este sale del campo 'ciedis' de la tabla)
			$wtdcocli=$wtdcocli+$tdco;                    //Total Clínica Días Cama Ocupada
			$wtdcdcli=$wtdcdcli+$tdcd;                    //Total Clínica Días Cama Disponible
			if ($tdcd==0)
				$Porc_ocupacion=0;
			else
				$Porc_ocupacion = (($tdco/$tdcd)*100);   //Porcentaje Ocupacional

			echo "<tr>";
			echo "<td align=center bgcolor=".$wcolor."><font size=".$wletra."><b>".$wser."</b></font></td>";                                    //Codigo Centro de Costo
			echo "<td align=center bgcolor=".$wcolor."><font size=".$wletra."><b>".$wnse."</b></font></td>";                                    //Nombre Centro de Costo
			echo "<td align=center bgcolor=".$wcolor."><font size=".$wletra."><b>$ingU</b></font></td>";
			echo "<td align=center bgcolor=".$wcolor."><font size=".$wletra."><b>$ingA</b></font></td>";
			echo "<td align=center bgcolor=".$wcolor."><font size=".$wletra."><b>$ingC</b></font></td>";			
			echo "<td align=center bgcolor=".$wcolor."><font size=".$wletra."><b>".($ingU+$ingA+$ingC)."</b></font></td>";                                    //Cant. Ingresos
			echo "<td align=center bgcolor=".$wcolor."><font size=".$wletra."><b>$ingT</b></font></td>";			
			echo "<td align=center bgcolor=".$wcolor."><font size=".$wletra."><b>".$wiye."</b></font></td>";                                    //Cant. Ingreso y Egresos mismo Dia
			echo "<td align=center bgcolor=".$wcolor."><font size=".$wletra."><b>$egrA</b></font></td>";
			echo "<td align=center bgcolor=".$wcolor."><font size=".$wletra."><b>".$wmay48."</b></font></td>";                                  //Muertes > a 48 horas
			echo "<td align=center bgcolor=".$wcolor."><font size=".$wletra."><b>".$wmen48."</b></font></td>";                                  //Muertes < a 48 horas
			echo "<td align=center bgcolor=".$wcolor."><font size=".$wletra."><b>".($egrA+$wmay48+$wmen48)."</b></font></td>";                                    //Cant. Egresos
			echo "<td align=center bgcolor=".$wcolor."><font size=".$wletra."><b>$egrT</b></font></td>";
			echo "<td align=center bgcolor=".$wcolor."><font size=".$wletra."><b>$diasAltasMuertes</b></font></td>";
			echo "<td align=center bgcolor=".$wcolor."><font size=".$wletra."><b>$diasTraslado</b></font></td>";
			echo "<td align=center bgcolor=".$wcolor."><font size=".$wletra."><b>".$wsalant."</b></font></td>";                                 //Saldo Anterior	
			//===========================================================================================================================================
			//PACIENTES A LA FECHA
			//===========================================================================================================================================
			//Pacientes a la fecha = Pacientes dia anterior + Ingresos - Egresos

			$wpacact=$wsalant+$wing-$wtotegr;
			$wpacactcli+=$wpacact;    //Pacientes Actuales Clínica a la Fecha

			echo "<td align=center bgcolor=".$wcolor."><font size=".$wletra."><b>".number_format($wpacact,0,'.',',')."</b></font></td>";
			echo "<td align=center bgcolor=".$wcolor."><font size=".$wletra."><b>".number_format($tdcd/$wdias,0,'.',',')."</b></font></td>";    //Número de Camas
			echo "<td align=center bgcolor=".$wcolor."><font size=".$wletra."><b>".$tdcd."</b></font></td>";                                    //Días Camas Disponibles
			echo "<td align=center bgcolor=".$wcolor."><font size=".$wletra."><b>".$tdco."</b></font></td>";                                    //Días Cama Ocupada			
			//===========================================================================================================================================
			echo "<td align=center bgcolor=".$wcolor."><font size=".$wletra."><b>".number_format($tdco/$wdias,0,'.',',')."</b></font></td>";    //Promedio Camas Ocupadas			
			echo "<td align=center bgcolor=".$wcolor."><font size=".$wletra."><b>".$wdes."</b></font></td>";                                    //Días de estancia a partir de los egresos			
			echo "<td align=center bgcolor=".$wcolor."><font size=".$wletra."><b>".number_format($Porc_ocupacion,2,'.',',')."</b></font></td>"; //Porcentaje de Ocupacion


			///$wncamcli=$wtdcocli+($tdcd/$wdias);    //Total numero de camas clinica
			$wncamcli=$wncamcli+($tdcd/$wdias);    //Total numero de camas clinica
			///$wpcoccli=$wtdcocli+($tdco/$wdias);    //Promedio cama ocupada clinica
			$wpcoccli=$wpcoccli+($tdco/$wdias);    //Promedio cama ocupada clinica
			//===========================================================================================================================================


			//===========================================================================================================================================
			//PROMEDIO DIAS ESTANCIA
			//===========================================================================================================================================
			//Xdias estancia = Total dias estancia del período/Total egresos del período

			//Total dias estancia del período
			$q = " SELECT SUM(ciedes) "
			."   FROM ".$wbasedato."_000038 "
			."  WHERE cieser     LIKE    '".$wser."'"
			."    AND fecha_data BETWEEN '".$wfec_i."' AND '".$wfec_f."'";
			$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$row1 = mysql_fetch_array($res1);
			$wtde = $row1[0];                 //Total Días de Estancia

			$wtdecli=$wtdecli+$row1[0];       //Total Clínica Días de Estancia

			//Total egresos del período
			if ($wtotegr > 0)
			$prom_estancia=$wtde/$wtotegr; //Promedio Días de Estancia
			else
			{
				$wtotegr=0;
				$prom_estancia=0;
			}

			echo "<td align=center bgcolor=".$wcolor."><font size=".$wletra."><b>".number_format($prom_estancia,2,'.',',')."</b></font></td>";
			//===========================================================================================================================================


			//===========================================================================================================================================
			//RENDIMIENTO HOSPITALARIO
			//===========================================================================================================================================
			//rendimiento hospitalario = Total egresos del período/Numero de Camas
			//2008-10-16: NOTA::: Los egresos incluyen aquellos por traslado.
			
			//Tomo el total de camas del ultimo dia del rango solicitado
			$q = " SELECT ciedis+cieocu "
			."   FROM ".$wbasedato."_000038 "
			."  WHERE cieser     LIKE    '".$wser."'"
			."    AND fecha_data = '".$wfec_f."'";
			$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$row1 = mysql_fetch_array($res1);

//			$wnumcamas = $row1[0];                         //Total Camas Habilitadas el Ultimo dia
			$wnumcamas = round($tdcd/$wdias);

			$wnumcamcli=$wnumcamcli+$wnumcamas;            //total Camas Habilitadas Clínica

			//$wnumcamas = $tdcd;                          //Total Camas Habilitadas
			if ($wnumcamas>0)
			$wrend_hosp=($wtotegr/$wnumcamas);          //Rendimiento Hospitalario
			else
			$wrend_hosp=0;

			echo "<td align=center bgcolor=".$wcolor."><font size=".$wletra."><b>".number_format($wrend_hosp,2,'.',',')."</b></font></td>";
			//===========================================================================================================================================


			//===========================================================================================================================================
			//INDICE DE SUSTITUCION
			//===========================================================================================================================================
			//indice de sustitucion = % de desocupacion/Promedio dias estancia
			if ($Porc_ocupacion != 0)
			$wind_sust=(((100-$Porc_ocupacion)*$prom_estancia)/($Porc_ocupacion));
			else
			$wind_sust=0;

			echo "<td align=center bgcolor=".$wcolor."><font size=".$wletra."><b>".number_format($wind_sust,2,'.',',')."</b></font></td>";
			//===========================================================================================================================================


			//===========================================================================================================================================
			//TASA DE MORTALIDAD
			//===========================================================================================================================================
			//Tasa de Mortalidad = ((número de muertes del período/Total egresos del período)*100)
			if (($wmay48+$wmen48) > 0)
			$wtasmor=((($wmay48+$wmen48)/$wtotegr)*100);
			else
			$wtasmor=0;

			echo "<td align=center bgcolor=".$wcolor."><font size=".$wletra."><b>".number_format($wtasmor,2,'.',',')."</b></font></td>";
			//===========================================================================================================================================


			//===========================================================================================================================================
			//TASA DE MORTALIDAD > 48 HORAS
			//===========================================================================================================================================
			//Tasa de Mortalidad > 48 = ((número de (muertes > 48) del período/Total egresos del período)*100)
			if (($wmay48) > 0)
			$wtasmay48=(($wmay48/$wtotegr)*100);
			else
			$wtasmay48=0;

			echo "<td align=center bgcolor=".$wcolor."><font size=".$wletra."><b>".number_format($wtasmay48,2,'.',',')."</b></font></td>";
			//===========================================================================================================================================


			//===========================================================================================================================================
			//TASA DE MORTALIDAD < 48 HORAS
			//===========================================================================================================================================
			//Tasa de Mortalidad < 48 = ((número de (muertes < 48) del período/Total egresos del período)*100)
			if (($wmen48) > 0)
			$wtasmen48=(($wmen48/$wtotegr)*100);
			else
			$wtasmen48=0;

			echo "<td align=center bgcolor=".$wcolor."><font size=".$wletra."><b>".number_format($wtasmen48,2,'.',',')."</b></font></td>";
			//===========================================================================================================================================

			echo "</tr>";
		}
			
		echo "<tr>";
		echo "<th align=center bgcolor=33FFFF colspan=2><font size=".($wletra+2)."><b>Totales</b></font></th>";
		echo "<th align=center bgcolor=33FFFF><font size=".($wletra+2)."><b>".number_format($sumaIngU,0,'.',',')."</b></font></th>";		
		echo "<th align=center bgcolor=33FFFF><font size=".($wletra+2)."><b>".number_format($sumaIngA,0,'.',',')."</b></font></th>";
		echo "<th align=center bgcolor=33FFFF><font size=".($wletra+2)."><b>".number_format($sumaIngC,0,'.',',')."</b></font></th>";						
		echo "<th align=center bgcolor=33FFFF><font size=".($wletra+2)."><b>".number_format(($sumaIngU+$sumaIngA+$sumaIngC),0,'.',',')."</b></font></th>";
		echo "<th align=center bgcolor=33FFFF><font size=".($wletra+2)."><b>".number_format($sumaIngT,0,'.',',')."</b></font></th>";
		echo "<th align=center bgcolor=33FFFF><font size=".($wletra+2)."><b>".number_format($wiyecli,0,'.',',')."</b></font></th>";
		echo "<th align=center bgcolor=33FFFF><font size=".($wletra+2)."><b>".number_format($sumaEgrA,0,'.',',')."</b></font></th>";
		echo "<th align=center bgcolor=33FFFF><font size=".($wletra+2)."><b>".number_format($sumaMMay,0,'.',',')."</b></font></th>";
		echo "<th align=center bgcolor=33FFFF><font size=".($wletra+2)."><b>".number_format($sumaMMen,0,'.',',')."</b></font></th>";
		echo "<th align=center bgcolor=33FFFF><font size=".($wletra+2)."><b>".number_format(($sumaEgrA+$sumaMMay+$sumaMMen),0,'.',',')."</b></font></th>";
		echo "<th align=center bgcolor=33FFFF><font size=".($wletra+2)."><b>".number_format($sumaEgrT,0,'.',',')."</b></font></th>";
		echo "<th align=center bgcolor=33FFFF><font size=".($wletra+2)."><b>".number_format($sumaDiasAltasMuertes,0,'.',',')."</b></font></th>";
		echo "<th align=center bgcolor=33FFFF><font size=".($wletra+2)."><b>".number_format($sumaDiasTraslado,0,'.',',')."</b></font></th>";
		echo "<th align=center bgcolor=33FFFF><font size=".($wletra+2)."><b>".number_format($wsalantcli,0,'.',',')."</b></font></th>";		//Suma pacientes dia anterior
		echo "<th align=center bgcolor=33FFFF><font size=".($wletra+2)."><b>".number_format($wpacactcli,0,'.',',')."</b></font></th>";		//Suma pacientes a la fecha
		echo "<th align=center bgcolor=33FFFF><font size=".($wletra+2)."><b>".number_format($wncamcli,0,'.',',')."</b></font></th>";        //Total numero de camas clinica		
		echo "<th align=center bgcolor=33FFFF><font size=".($wletra+2)."><b>".number_format($wtdcdcli,0,'.',',')."</b></font></th>";        //Total dias cama disponible clinica				
		echo "<th align=center bgcolor=33FFFF><font size=".($wletra+2)."><b>".number_format($wtdcocli,0,'.',',')."</b></font></th>";        //Total dias cama ocupada clinica
		echo "<th align=center bgcolor=33FFFF><font size=".($wletra+2)."><b>".number_format($wpcoccli,0,'.',',')."</b></font></th>";        //Promedio camas ocupadas clinica		
		echo "<th align=center bgcolor=33FFFF><font size=".($wletra+2)."><b>".number_format($wdescli,0,'.',',')."</b></font></th>";
		$Porc_ocupacion = (($wtdcocli/$wtdcdcli)*100);                                                   //Porcentaje Ocupacional Clínica
		echo "<th align=center bgcolor=33FFFF><font size=".($wletra+2)."><b>".number_format($Porc_ocupacion,2,'.',',')."</b></font></th>";
		$prom_estancia=$wtdecli/$wegrcli;                                                                //Promedio Clínica Días de Estancia
		echo "<th align=center bgcolor=33FFFF><font size=".($wletra+2)."><b>".number_format($prom_estancia,2,'.',',')."</b></font></th>";
		$wrend_hosp=($wegrcli/$wnumcamcli);                                                              //Rendimiento Hospitalario Clínica
		echo "<th align=center bgcolor=33FFFF><font size=".($wletra+2)."><b>".number_format($wrend_hosp,2,'.',',')."</b></font></th>";
		$wind_sust=(((100-$Porc_ocupacion)*$prom_estancia)/($Porc_ocupacion));                           //Indice de Sustitucion Clínica
		echo "<th align=center bgcolor=33FFFF><font size=".($wletra+2)."><b>".number_format($wind_sust,2,'.',',')."</b></font></th>";
		$wtasmor=((($sumaMMay+$sumaMMen)/($sumaEgrA+$sumaMMay+$sumaMMen))*100);
		//Tasa de Mortalidad Clínica
		echo "<th align=center bgcolor=33FFFF><font size=".($wletra+2)."><b>".number_format($wtasmor,2,'.',',')."</b></font></th>";
		//Tasa mortalidad mayor 48 horas
		$wtasmor=(($sumaMMay/($sumaEgrA+$sumaMMay+$sumaMMen))*100);                                                            //Tasa de Mortalidad Clínica Mayor a 48 horas
		echo "<th align=center bgcolor=33FFFF><font size=".($wletra+2)."><b>".number_format($wtasmor,2,'.',',')."</b></font></th>";
		//Tasa mortalidad menor 48 horas
		$wtasmor=(($sumaMMen/($sumaEgrA+$sumaMMay+$sumaMMen))*100);                                                            //Tasa de Mortalidad Clínica Menor a 48 horas
		echo "<th align=center bgcolor=33FFFF><font size=".($wletra+2)."><b>".number_format($wtasmor,2,'.',',')."</b></font></th>";		
		echo "</tr>";
	}
	else
	echo "<td align=center bgcolor=33FFFF colspan=21><font size=4><b>PARA ESTE PERIODO NO EXISTE LA TABLA DE CIERRE DIARIO DE CAMAS</b></font></td>";

	echo "<tr>";
	echo "<td align=center colspan=21><A href='indicadores_hospitalarios.php?wemp_pmla=".$wemp_pmla."'><b>Retornar</b></A></td>";
	echo "</tr>";
}
echo "</table>";
echo "</form>";

echo "<br>";
echo "<center><table>";
echo "<tr><td align=center><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
echo "</table>";
}
liberarConexionBD($conex);
?>