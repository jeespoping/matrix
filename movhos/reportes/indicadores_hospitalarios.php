<html>
<head>
<title>MATRIX - [INDICADORES HOSPITALARIOS]</title>
</head>

<body>

<script type="text/javascript">
function enter(){ document.forms.indhosp.submit(); }
function cerrarVentana() { window.close(); }
</script>

<?php
include_once("conex.php");
include_once("root/comun.php");

$wactualiz = " 2012-07-11";

/******************************************************************************************************
 *          INDICADORES HOSPITALARIOS          											              *
 * 	2012-07-11:  Se agregan las consultas consultarCentroCostos y dibujarSelect que listan los centros*
 *              de costos de un grupo dado en orden alfabetico y dibuja el select con esos centros    *
 *              de costo respectivamente Viviana Rodas												  *									   *
 * Actualizado: 04-Ago-2008 (Msanchez):  Se modifica reporte para que apunte a la tabla 38            *
 * Actualizado: 17-Abr-2009 (Msanchez):  Soporte de hoja de estilos nueva				              *
 * Actualizado: 12-Ago-2009 (Msanchez):  Revision de errores segun reunion				              *
 ******************************************************************************************************/
if (!isset($user))
if(!isset($_SESSION['user']))
session_register("user");

if(!isset($_SESSION['user']))
terminarEjecucion($MSJ_ERROR_SESION_CADUCADA);
else{
	$conex = obtenerConexionBD("matrix");
	$wfecha=date("Y-m-d");
	$whora = (string)date("H:i:s");
	if(!isset($wemp_pmla)){
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
	}
	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
	$winstitucion = $institucion->nombre;

	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$wtabcco = consultarAliasPorAplicacion($conex, $wemp_pmla, "tabcco");

	encabezado("INDICADORES HOSPITALARIOS", $wactualiz, "clinica");

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
		//Cuerpo de la pagina
		
			
		echo '<span class="subtituloPagina2">';
		echo "Parametros de consulta";
		echo "</span>";
		echo "<br>";
		echo "<br>";
			
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$q = " SELECT ".$wtabcco.".ccocod, ".$wtabcco.".cconom "
		."   FROM ".$wtabcco.", ".$wbasedato."_000011"
		."  WHERE ".$wtabcco.".ccocod = ".$wbasedato."_000011.ccocod ";
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);

		//Servicio
		/*echo "<tr><td class='fila1' width=200>Servicio</td>";
		echo "<td class='fila2' align='center' width=250>";
		echo "<select name='wcco' class='textoNormal'>";
		echo "<option>% - Todos</option>";
		for ($i=1;$i<=$num;$i++)
		{
			$row = mysql_fetch_array($res);
			echo "<option>".$row[0]." - ".$row[1]."</option>";
		}
		echo "</select>";
		echo "</td>";
		echo "</tr>";*/
		$cco="Ccohos";
		$sub="off";
		$tod="Todos";
		//$cco=" ";
		$ipod="off";
		$centrosCostos = consultaCentrosCostos($cco);
		echo "<table align='center' border=0 >";
		$dib=dibujarSelect($centrosCostos, $sub, $tod, $ipod, "wcco");
					
		echo $dib;
		echo "</table>";
        
		echo "<table align='center' border=0 width=402>";
		//Fecha inicial
		echo "<tr><td class='fila1' width=85>Fecha inicial</td>";
		echo "<td class='fila2' align='center' width=250>";

		if (!isset($wfec_i))
		$wfec_i=date("Y-m-d");
		$cal="calendario('wfec_i','1')";

		echo "<input type='TEXT' name='wfec_i' size=10 maxlength=10  id='wfec_i' readonly='readonly' value=".$wfec_i." class='textoNormal'><button id='trigger1' onclick=".$cal.">...</button>";
		funcionJavascript("Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'12',electric:false,inputField:'wfec_i',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});");
//		campoFecha("wfec_i");
		echo "</td>";
		echo "</tr>";
			
		//Fecha final
		echo "<tr><td class='fila1' width=85>Fecha final</td>";
		echo "<td class='fila2' align='center' width=250>";

		if (!isset($wfec_f))
//		$wfec_f=date("Y-m-d");
//		$cal="calendario('wfec_f','1')";
//
//		echo "<input type='TEXT' name='wfec_f' size=10 maxlength=10  id='wfec_f' readonly='readonly' value=".$wfec_f." class='textoNormal'><button id='trigger2' onclick=".$cal.">...</button>";

//		funcionJavascript("Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'12',electric:false,inputField:'wfec_f',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});");
		campoFecha("wfec_f");

		echo "</td>";
		echo "</tr>";
			
		echo "<tr><td align=center bgcolor=cccccc colspan=2></b><input type='submit' value='Consultar'>&nbsp;|&nbsp;<input type=button value='Cerrar Ventana' onclick='javascript:cerrarVentana();'></b></td></tr></center>";
		echo "</table>";
	}else{
		echo "<table align='center' border=0>";
			
		echo '<span class="subtituloPagina2">';
		echo "Indicadores por servicio entre los días $wfec_i y $wfec_f";
		echo "</span>";
		echo "<br>";
		echo "<br>";

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
			echo "<tr class='encabezadoTabla'>";
			echo "<th rowspan=2 colspan=2>Servicio</th>";
			echo "<th colspan=4>Ingresos</th>";
			echo "<th rowspan=2>Ingresos por traslado</th>";
			echo "<th rowspan=2>Ingresos y egresos dia</th>";
			echo "<th colspan=4>Egresos</th>";
			echo "<th rowspan=2>Egresos por traslado</th>";
			echo "<th colspan=2>Dias estancia</th>";
			echo "<th rowspan=2>Pacientes día ant.</th>";
			echo "<th rowspan=2>Pacientes a la fecha</th>";
			echo "<th rowspan=2>Nro Camas</th>";
			echo "<th rowspan=2>Días Cama Disponible</th>";
			echo "<th rowspan=2>Días Cama Ocupada</th>";
			echo "<th rowspan=2>Prom. Camas Ocupadas</th>";
			echo "<th rowspan=2>Días de<br>Estancia</th>";
			echo "<th rowspan=2>% de Ocupacion</th>";
			echo "<th rowspan=2>Promedio Dias<br>Estancia</th>";
			echo "<th rowspan=2>Rendimiento<br>Hospitalario</th>";
			echo "<th rowspan=2>Indice de<br>Sustitución</th>";
			echo "<th rowspan=2>Tasa de <br>Mortalidad</th>";
			echo "<th rowspan=2>Tasa Mortalidad<br> > 48 Horas</th>";
			echo "<th rowspan=2>Tasa Mortalidad<br> < 48 Horas</th>";
			echo "</tr>";
			echo "<tr class='encabezadoTabla'>";
			echo "<th>Urgencias</font></th>";
			echo "<th>Admisiones</font></th>";
			echo "<th>Cirugia</font></th>";
			echo "<th>Total</font></th>";
			echo "<th>Altas</font></th>";
			echo "<th>Muertes > 48 Horas</font></th>";
			echo "<th>Muertes < 48 Horas</font></th>";
			echo "<th>Total</font></th>";
			echo "<th>Altas y muertes</font></th>";
			echo "<th>Egresos por traslado</font></th>";
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
				$wclase="fila1";
				else
				$wclase="fila2";
					
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
				$tdco = abs($wsalant+$wing-$wegr+$wiye);
					
				$tdcd = abs($wdis+$wocu);                        //Total Días Cama Disponible = Total días cama disponible (Este sale del campo 'ciedis' de la tabla)
					
				$wtdcdcli=$wtdcdcli+$tdcd;			       //Total Clínica Días Cama Disponible
					
				$wtdcocli=$wtdcocli+$tdco;                    //Total Clínica Días Cama Ocupada
					
				//			$tdco=$row[2]+$wiye;
					
				if ($tdcd==0)
				$Porc_ocupacion=0;
				else
				$Porc_ocupacion = (($tdco/$tdcd)*100);   //Porcentaje Ocupacional
					
				echo "<tr>";
				echo "<td align=center class='$wclase'>".$wser."</td>";                                    //Codigo Centro de Costo
				echo "<td align=center class='$wclase'>".$wnse."</td>";                                    //Nombre Centro de Costo
				echo "<td align=center class='$wclase'>$ingU</td>";
				echo "<td align=center class='$wclase'>$ingA</td>";
				echo "<td align=center class='$wclase'>$ingC</td>";
				echo "<td align=center class='$wclase'>".($ingU+$ingA+$ingC)."</td>";                                    //Cant. Ingresos
				echo "<td align=center class='$wclase'>$ingT</td>";
				echo "<td align=center class='$wclase'>".$wiye."</td>";                                    //Cant. Ingreso y Egresos mismo Dia
				echo "<td align=center class='$wclase'>$egrA</td>";
				echo "<td align=center class='$wclase'>".$wmay48."</td>";                                  //Muertes > a 48 horas
				echo "<td align=center class='$wclase'>".$wmen48."</td>";                                  //Muertes < a 48 horas
				echo "<td align=center class='$wclase'>".($egrA+$wmay48+$wmen48)."</td>";                                    //Cant. Egresos
				echo "<td align=center class='$wclase'>$egrT</td>";
				echo "<td align=center class='$wclase'>$diasAltasMuertes</td>";
				echo "<td align=center class='$wclase'>$diasTraslado</td>";
				echo "<td align=center class='$wclase'>".$wsalant."</td>";                                 //Saldo Anterior
				//===========================================================================================================================================
				//PACIENTES A LA FECHA
				//===========================================================================================================================================
				//Pacientes a la fecha = Pacientes dia anterior + Ingresos - Egresos

				$wpacact=abs($wsalant+$wing-$wtotegr);
				$wpacactcli+=$wpacact;    //Pacientes Actuales Clínica a la Fecha
				$nroCamas = $tdcd/$wdias;
				@$totalAcumCamas += $tdco * $wdias;

				echo "<td align=center class='$wclase'>".number_format($wpacact,0,'.',',')."</td>";
				echo "<td align=center class='$wclase'>".number_format($nroCamas,0,'.',',')."</td>";    //Número de Camas
				echo "<td align=center class='$wclase'>".$tdcd."</td>";                                    //Días Camas Disponibles
				echo "<td align=center class='$wclase'>".$tdco."</td>";                                    //Días Cama Ocupada
				//===========================================================================================================================================
				echo "<td align=center class='$wclase'>".number_format($tdco/$wdias,0,'.',',')."</td>";    //Promedio Camas Ocupadas
				echo "<td align=center class='$wclase'>".$wdes."</td>";                                    //Días de estancia a partir de los egresos
				echo "<td align=center class='$wclase'>".number_format($Porc_ocupacion,2,'.',',')."</td>"; //Porcentaje de Ocupacion


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

				echo "<td align=center class='$wclase'>".number_format($prom_estancia,2,'.',',')."</td>";
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

				echo "<td align=center class='$wclase'>".number_format($wrend_hosp,2,'.',',')."</td>";
				//===========================================================================================================================================


				//===========================================================================================================================================
				//INDICE DE SUSTITUCION
				//===========================================================================================================================================
				//indice de sustitucion = % de desocupacion/Promedio dias estancia
				if ($Porc_ocupacion != 0)
				$wind_sust=(((100-$Porc_ocupacion)*$prom_estancia)/($Porc_ocupacion));
				else
				$wind_sust=0;

				echo "<td align=center class='$wclase'>".number_format($wind_sust,2,'.',',')."</td>";
				//===========================================================================================================================================


				//===========================================================================================================================================
				//TASA DE MORTALIDAD
				//===========================================================================================================================================
				//Tasa de Mortalidad = ((número de muertes del período/Total egresos del período)*100)
				//			echo "wmay48 $wmay48";
				//			echo "wmen48 $wmen48";
				//			echo "wtotegr $wtotegr";
					
				if (($wmay48+$wmen48) > 0 && $wtotegr>0)
					$wtasmor=((($wmay48+$wmen48)/$wtotegr)*100);
				else
					$wtasmor=0;

				echo "<td align=center class='$wclase'>".number_format($wtasmor,2,'.',',')."</td>";
				//===========================================================================================================================================


				//===========================================================================================================================================
				//TASA DE MORTALIDAD > 48 HORAS
				//===========================================================================================================================================
				//Tasa de Mortalidad > 48 = ((número de (muertes > 48) del período/Total egresos del período)*100)
				if (($wmay48) > 0)
				$wtasmay48=(($wmay48/$wtotegr)*100);
				else
				$wtasmay48=0;

				echo "<td align=center class='$wclase'>".number_format($wtasmay48,2,'.',',')."</td>";
				//===========================================================================================================================================


				//===========================================================================================================================================
				//TASA DE MORTALIDAD < 48 HORAS
				//===========================================================================================================================================
				//Tasa de Mortalidad < 48 = ((número de (muertes < 48) del período/Total egresos del período)*100)
				if ($wmen48 > 0 && $wtotegr > 0)
					$wtasmen48=(($wmen48/$wtotegr)*100);
				else
					$wtasmen48=0;

				echo "<td align=center class='$wclase'>".number_format($wtasmen48,2,'.',',')."</td>";
				//===========================================================================================================================================

				echo "</tr>";
			}

			echo "<tr class='encabezadoTabla'>";
			echo "<th align=center  colspan=2>Totales</th>";
			echo "<th align=center >".number_format($sumaIngU,0,'.',',')."</th>";
			echo "<th align=center >".number_format($sumaIngA,0,'.',',')."</th>";
			echo "<th align=center >".number_format($sumaIngC,0,'.',',')."</th>";
			echo "<th align=center >".number_format(($sumaIngU+$sumaIngA+$sumaIngC),0,'.',',')."</th>";
			echo "<th align=center >".number_format($sumaIngT,0,'.',',')."</th>";
			echo "<th align=center >".number_format($wiyecli,0,'.',',')."</th>";
			echo "<th align=center >".number_format($sumaEgrA,0,'.',',')."</th>";
			echo "<th align=center >".number_format($sumaMMay,0,'.',',')."</th>";
			echo "<th align=center >".number_format($sumaMMen,0,'.',',')."</th>";
			echo "<th align=center >".number_format(($sumaEgrA+$sumaMMay+$sumaMMen),0,'.',',')."</th>";
			echo "<th align=center >".number_format($sumaEgrT,0,'.',',')."</th>";
			echo "<th align=center >".number_format($sumaDiasAltasMuertes,2,'.',',')."</th>";
			echo "<th align=center >".number_format($sumaDiasTraslado,2,'.',',')."</th>";
			echo "<th align=center >".number_format($wsalantcli,0,'.',',')."</th>";		//Suma pacientes dia anterior
			echo "<th align=center >".number_format($wpacactcli,0,'.',',')."</th>";		//Suma pacientes a la fecha
			echo "<th align=center >".number_format($wncamcli,0,'.',',')."</th>";        //Total numero de camas clinica
			echo "<th align=center >".number_format($wtdcdcli,0,'.',',')."</th>";        //Total dias cama disponible clinica
			echo "<th align=center >wtdcocli".number_format($wtdcocli,0,'.',',')."</th>";        //Total dias cama ocupada clinica
			echo "<th align=center >".number_format($wpcoccli,0,'.',',')."</th>";        //Promedio camas ocupadas clinica
			echo "<th align=center >".number_format($wdescli,2,'.',',')."</th>";
//			$Porc_ocupacion = (($wtdcocli/$wtdcdcli)*100); 
			$Porc_ocupacion = ($totalAcumCamas/$wtdcdcli)*100;                                                //Porcentaje Ocupacional Clínica
			echo "<th align=center >'$totalAcumCamas' / '$wtdcdcli'".number_format($Porc_ocupacion,2,'.',',')."</th>";
			$prom_estancia=$wtdecli/$wegrcli;                                                                //Promedio Clínica Días de Estancia
			echo "<th align=center >".number_format($prom_estancia,2,'.',',')."</th>";
			$wrend_hosp=($wegrcli/$wnumcamcli);                                                              //Rendimiento Hospitalario Clínica
			echo "<th align=center >".number_format($wrend_hosp,2,'.',',')."</th>";
			$wind_sust=(((100-$Porc_ocupacion)*$prom_estancia)/($Porc_ocupacion));                           //Indice de Sustitucion Clínica
			echo "<th align=center >".number_format($wind_sust,2,'.',',')."</th>";

			$wtasmor=((($sumaMMay+$sumaMMen)/($sumaEgrA+$sumaMMay+$sumaMMen+$sumaEgrT))*100);
			//Tasa de Mortalidad Clínica
			echo "<th align=center >".number_format($wtasmor,2,'.',',')."</th>";
			//Tasa mortalidad mayor 48 horas

			//		echo "sumaMMay $sumaMMay";
			//		echo "sumaMMen $sumaMMen";
			//		echo "sumaEgrA $sumaEgrA";
			//		echo "sumaMMay $sumaMMay";
			//		echo "sumaMMen $sumaMMen";
			//		echo "sumaEgrT $sumaEgrT";

			//		$wtasmor=(($sumaMMay/($sumaEgrA+$sumaMMay+$sumaMMen))*100);
			$wtasmor=(($sumaMMay/($sumaEgrA+$sumaMMay+$sumaMMen+$sumaEgrT))*100);
		 //Tasa de Mortalidad Clínica Mayor a 48 horas
			echo "<th align=center >".number_format($wtasmor,2,'.',',')."</th>";
			//Tasa mortalidad menor 48 horas
			$wtasmor=(($sumaMMen/($sumaEgrA+$sumaMMay+$sumaMMen+$sumaEgrT))*100);                                                            //Tasa de Mortalidad Clínica Menor a 48 horas
			echo "<th align=center >".number_format($wtasmor,2,'.',',')."</th>";
			echo "</tr>";
		}
		else
		echo "<td align=center  colspan=21><font size=4><b>PARA ESTE PERIODO NO EXISTE LA TABLA DE CIERRE DIARIO DE CAMAS</b></font></td>";

		echo "<tr>";
		echo "<td align=center colspan=21>";
		echo "<input type='submit' value='Retornar'>&nbsp;|&nbsp;<input type=button value='Cerrar Ventana' onclick='javascript:cerrarVentana();'>";
		echo "</td>";
		echo "</tr>";
	}
	echo "</table>";
	echo "</form>";

	echo "</table>";
}
liberarConexionBD($conex);
?>
</body>
</html>