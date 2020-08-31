<?php
include_once("conex.php"); header('Content-type: text/html;charset=ISO-8859-1'); ?>

<?php
//Para que en las solicitudes ajax no imprima <html><head> etc
if( isset($consultaAjax) == false ){	
?>
<html>
 
<head>
<title>MATRIX - [INDICADORES HOSPITALARIOS DIA POR DIA]</title>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript" ></script>
<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>
<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />
</head>

<style type="text/css">
 .caja_flotante_query{
        position: absolute;
        top:0;
        /*width:624px;*/
       
    }
	.fixed-dialog{
	  position: fixed;
	  top: 100px;
	  left: 100px;
	}
	.ui-dialog .ui-dialog-content { 
		background: #E3F6CE; 
	}
	.ui-widget-overlay {
	 background: #AAA url(images/ui-bg_flat_0_aaaaaa_40x100.png) 50% 50% repeat-x;
	   opacity: .30;
	   filter: Alpha(Opacity=30);
	}
	.tdcentrado td{	
		text-align: center;
	}
	#tooltip{color: #2A5DB0;font-family: Arial,Helvetica,sans-serif;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:5px;opacity:1;}
	#tooltip h3, #tooltip div{margin:0; width:auto}
</style>

<body>

<script type="text/javascript">

//*******************************************PARA HACER QUE EL ENCABEZADO DE LA TABLA SE MUESTRE CUANDO HAGA SCROLL HACIA ABAJO
$(document).ready(function() {
	//$(window).scrollTop(0);
	$(".msg_tooltip").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
$(".caja_flotante_query").hide();
	var posicion_query = $(".caja_flotante_query").offset();
	var posicion_query_ori1 = $(".caja_flotante_query_ori1").offset();
	if( posicion_query != undefined ){
		var html_text = "<table width=100% id='tb1'><tr class='encabezadoTabla'>";
		html_text+=  $(".caja_flotante_query_ori1").html()+"</tr>";
		html_text+="<tr class='encabezadoTabla'>"+$(".caja_flotante_query_ori2").html()+"</tr>";
		html_text += "</table>";
		$(".caja_flotante_query").html( html_text );
		$(".caja_flotante_query").css('background-color','white');
		
		var tamanos = new Array();
		$(".caja_flotante_query_ori1").find('td').each(function(){
			 tamanos.push( $(this).width() );
		});

		html_text = "<tr>";
		$('.caja_flotante_query_ori1').parent().find('tr:nth-child(3) td').each(function () {
			html_text+="<td style='width:"+$(this).width()+"px;'></td>";
		});
		html_text += "</tr>";
		$('#tb1').append(html_text);
	
		$(".caja_flotante_query").width( $('.caja_flotante_query_ori1').width() );
		$(window).scroll(function() {
			if ($(window).scrollTop() > posicion_query_ori1.top) {
				$(".caja_flotante_query").show();
				$(".caja_flotante_query").css('marginTop', $(window).scrollTop() );
				//$( ".ui-dialog" ).eq(0).css('marginTop', 0 );
			} else {
				$(".caja_flotante_query").css('marginTop', posicion_query_ori1.top );
				$(".caja_flotante_query").hide();
			};
		});
	}
});
//******************************************FIN

function ver_historias(info_historias, hab){
      
    if($("#"+info_historias).is(":visible")){		
		$("#"+info_historias).hide("slow");
        $("#total_"+hab).show();       
	}else{		
		$("#"+info_historias).show("slow");
        $("#total_"+hab).hide();       
	}
	
}

/******************************************************************************************************************************
 *Accion de consulta
 ******************************************************************************************************************************/
  function consultar()
  { 
	var wemp_pmla = document.forms.indhosp.wemp_pmla.value;
	//var servicio = document.forms.indhosp.wservicio.value;
	var servicio = document.forms.indhosp.wservicio.options[document.forms.indhosp.wservicio.selectedIndex].text;
	var fInicial = document.forms.indhosp.wfec_i.value;
	var fFinal = document.forms.indhosp.wfec_f.value;	
		
	var n=servicio.split("-"); ;
	//alert(n[0]);
	servicio = n[0]; 
    
 	if(esFechaMenorIgual(fInicial,fFinal)){
 		document.location.href = 'indicadores_hospitalarios_dia.php?wemp_pmla='+wemp_pmla+'&waccion=a'+'&wservicio='+servicio+'&wfechaInicial='+fInicial+'&wfechaFinal='+fFinal;	
 	} else {
 		alert("La fecha inicial debe ser menor a la fecha final de consulta.");
 	}	  
  }

/******************************************************************************************************************************
 *Redirecciona a la pagina inicial del kardex
 ******************************************************************************************************************************/
  //Redirecciona a la pagina inicial
  function inicio(wfec_i,wfec_f,wservicio)
  {
	document.location.href='indicadores_hospitalarios_dia.php?wemp_pmla='+document.forms.indhosp.wemp_pmla.value+'&wfec_i='+wfec_i+'&wfec_f='+wfec_f+'&wservicio='+wservicio+'&bandera=1';	 		
  }
  
  function consultarDetalleDiaCamaOcupada(ele, servicio, wfechaInicial, wfechaFinal){
	var wemp_pmla    = $("#wemp_pmla").val();
	ele = jQuery(ele);
	if( ele.text() == "0" ) return;
	$.blockUI({ message: $('#msjEspere') });
	
	$.post('indicadores_hospitalarios_dia.php', { 
			
			wemp_pmla: 		wemp_pmla, 
			wfechaInicial:	wfechaInicial, 
			wfechaFinal:	wfechaFinal, 
			servicio:		servicio, 			
			consultaAjax: 	'consultarDetalleDiaCamaOcupada' 
			
			} ,
		function(data) {
			$.unblockUI();			
			$("#detalle_dias_cama_ocupada").html(data);			
			$("#detalle_dias_cama_ocupada").dialog({
			  width: 600,
			  height: 500,
			  title: "Detalle Indicador Días Cama Ocupada",
			  modal: true,
			  
			});
		});
  } 
  
  
  function consultarDetalle(ele, wfecha, wcco, wcconom, wtipo){
	var wemp_pmla    = $("#wemp_pmla").val();
	ele = jQuery(ele);
	if( ele.text() == "0" ) return;
	$.blockUI({ message: $('#msjEspere') });
	$.post('indicadores_hospitalarios_dia.php', { wemp_pmla: wemp_pmla, wfechap:wfecha, wcco:wcco, wcconom:wcconom, wtipo:wtipo, consultaAjax: 'consultarDetalle' } ,
		function(data) {
			$.unblockUI();
			//$.blockUI({ message: data, css:{width:700} });	
			$("#aux_respuesta").html(data);			
			$("#aux_respuesta").dialog({
			  width: 600,
			  maxHeight: 680,
			  title: "Detalle Indicador",
			  dialogClass: 'fixed-dialog',
			  modal: true
			});
		});
  }
  
  function cerrarModal(){
	$( "#aux_respuesta" ).dialog( "close" );
	$( "#detalle_dias_cama_ocupada" ).dialog( "close" );
  }
</script>

<?php
}
/****************************************************************************************************************************
 *          INDICADORES HOSPITALARIOS
 2020-02-10, Jerson trujillo : Nuevo calculo de dias estancia por traslado y dias estancia por altas y muertes.
	Esto se hace por solicitud de Nancy (gestion de la informacion) no se tendrá el cuenta el campo calculado 
	de la movhos_38 (Ciedit, Ciediam) ya que presentá incosistencias, sino que se calculará con base al movimiento de la movhos_32 y 33
 2019-08-02, Jerson trujillo :
	Se modifica el calculo de dias cama ocupado, por definicion de registros medicos. Esto se hizo porque
	cuando se daba click en el detalle eran distintos los valores del total detallado con el general.
	
 * Actualizado: 09-Ene-2019 (Edwin MG): - El caculo de días de estancia por altas y muertes institucionales se hace de 
 *										  acuerdo a la tablas de de egresos (cliame_000108)
 *										- El promedio de días de estancia institucionales se hace de acuerdo a los días de estancia
 *										  totales institucionales sobre egresos totales institucionales
 *										- Los días de estancia por día se calcula como días de estancia de alta + dias de 
 *										  estancia por egreso del servicio sobre total de egresos
 * Actualizado: 13-Jul-2018 (Jonatan Lopez): Se corrige el denominador en el calculos de promedio de dias de estancia,
												agregandole los egresos por traslado.
 * Actualizado: 13-Jun-2018 (Jonatan Lopez): Se corrige el promedio de dias de estancia teniendo en cuenta solo egresos por *
									alta y muerte.																			*		
 * Actualizado: 12-Jul-2018 (Jonatan Lopez): Se oculta la columna dias de estancia segun solicitud de Registros Médicos y   *
 *											  Estadística ya que este dato de muestra detallado en Altas y muertes y Egresos*
 *											  por traslado																	*	
 * Actualizado: 09-Feb-2017 (Arleyda Insignares):Se agrega filtro en el campo ccoemp en caso de que el Query utilice la     *
 *                                              tabla costosyp_000005.								   						*
 * Actualizado: 18-Mar-2015 (Jonatan Lopez)  Se agrega el detalle para los dias de cama ocupada.							*
 * Actualizado: 18-Mar-2015 (Frederick Aguirre) Para el piso 1179 se cambio la condicion para el calculo para 				*
 * 												diascamaocupada, ademas de corregir un error de logica en el mismo.			*
 * Actualizado: 04-Ago-2008 (Msanchez):  Se modifica reporte para que apunte a la tabla 38 									*
 * Actualizado: 17-Abr-2009 (Msanchez):  Soporte de hoja de estilos nueva				   									*
 * Actualizado: 08-May-2009 (Msanchez):  Agrupamiento por dias y por servicio			   									*
 * Actualizado: 07-Sep-2009 (Msanchez):  Correcciones a formulas de rendimiento hosp.	   									*
 * Actualizado: 02-Feb-2011 (John M. Cadavid G.):  Corrección en función "consultarIndicadoresHospitalarios"				*
 * 												   para que no haga división por cero en algunos cálculos					*
 * Actualizado: 03-Abr-2012	(Jónatan López). Se corrige la posición de la variable $diasFechasConsulta  					*
 * 										ya que es necesaria para mostrar el promedio de camas ocupadas en el total final	*
 * 										(se ubica la variable $diasFechasConsulta despues del foreach) .					*
 * Actualizado: 03-Jul-2010 (Viviana Rodas).  Se agrega el llamado a las funciones consultaCentrosCostos que hace la 		*
 *											consulta de los centros de costos de un grupo seleccionado y dibujarSelect 		*
 *											que dibuja el select con los centros de costos obtenidos de la primera	funcion.*
 *Actualizado: 23-Oct-2012  (Frederick Aguirre) Cuando baje con el scroll en la pantalla la columna con todos los titulos 	*
 *												permanece en la parte de arriba de la pantalla								*
 *Actualizado: 08-Nov-2012  (Frederick Aguirre) Se creo una columna que muestra otro tipo de ingresos, como lo son 			*
 *											hemodinamia. En la tabla 38 hay un campo que muestra la cantidad de ingresos	*
 *											Si la suma de los ingresos por urg, tras, ciru y admisi  es menor que ese campo,*
 *											entonces la diferencia va para el campo "otros ingresos"						*
 *Actualizado: 14-Nov-2012  (Frederick Aguirre) Se quitan los titulos de la tabla para cada centro de costo, dado que en la *
 *											actualizacion del 23 de oct de 2012 los titulos se "arrastran" por la pantalla	*
 *Actualizado: 08-Ene-2013  (Frederick Aguirre) Se cambia el titulo "otros" por "hemodinamia" 		
 *Actualizado: 27-Feb-2013  (Frederick Aguirre) Para el piso 1179 se cambio el calculo para diascamaocupada 				*
 *Actualizado: 05-Abr-2013  (Frederick Aguirre) Se cambio "pacientes a la fecha" en el subtotal, no es una sumatoria, es el *
											ultimo.																			*
											Se cambio el calculo de prom" camas ocupadas" y "Nro de camas" en los subtotales*
									No divide sobre el numero de dias de la consulta ya que NO suma cuando no habian camas  *
 *Actualizado: 13-Nov-2013  (Frederick Aguirre) Se muestran los pacientes cuando se da click en la cantidad de ing y egr    *									
 *Actualizado: 14-Nov-2013  (Frederick Aguirre) Se cambia blockUI por jquery UI Dialog y se pone el texto centrado en los td*									
 ***************************************************************************************************************************/


class indicador{
	var $servicio;						//Servicio
	var $fecha;							//Fecha generación indicador
	var $camasOcupadas = 0; 			//Camas ocupadas
	var $camasDisponibles = 0;			//Camas disponibles
	var $camasDelServicio = 0;			//Camas del servicio
	
	var $ingU = 0;						//Ingresos urgencias
	var $ingA = 0;						//Ingresos admisiones
	var $ingC = 0;						//Ingresos cirugia
	var $ingT = 0;						//Ingresos por traslado
	var $ingTotales = 0;				//Ingresos totales.  No incluye ingresos por traslado.
	var $ingTotalesSinTrasl=0;			

	var $ingYEgrDia = 0;				//Ingresos y egresos del dia

	var $egrA = 0;						//Egresos por altas
	var $egrMmay48 = 0;					//Egresos por muerte mayor a 48 horas
	var $egrMmen48 = 0;					//Egresos por muerte menor a 48 horas
	var $egrT = 0;						//Egresos por traslado
	var $egrTotales = 0;				//Egresos totales
	var $egrTotalesSinTrasl=0;

	var $diasEAltasM = 0;				//Dias de estancia altas y muertes
	var $diasEEgrT = 0;					//Dias de estancia egresos por traslado

	var $pacDiaAnterior = 0;			//Pacientes dia anterior
	var $pacALaFecha = 0;				//Pacientes a la fecha

	var $nroCamas = 0;					//Numero de camas
	var $diasCamaDisponible = 0;		//Dias cama disponible para calculos de indicadores
	var $totalDiasCamaDisponible = 0;	//Dias cama disponible para total
	var $diasCamaOcupada = 0;			//Dias cama ocupada
	var $promCamasOcupadas = 0;			//Promedio dias camas ocupadas
	var $diasEstanciaTotales = 0;		//Dias estancia egresos altas y muertes mas dias estancia egresos por traslado

	var $porcOcupacion = 0;				//Porcentaje ocupación
	var $promDiasEstancia = 0;			//Promedio dias estancia
	var $rendimientoHospitalario = 0;	//Rendimiento hospitalario
	var $indiceSustitucion = 0;			//Indice sustitución
	var $tasaMortalidad = 0;			//Tasa mortalidad
	var $tasaMortalidadMayor48 = 0;		//Tasa mortalidad mayor a 48 horas
	var $tasaMortalidadMenor48 = 0;		//Tasa mortalidad menor a 48 horas
	
	var $diasEInstitucionales = 0;		//Días de estancia institucional
}

/******************************************************************************************************************
 * Enero 2 de 2019
 *
 * Los días de estancia institucional se hacen de acuerdo a la fecha de egreso y la fecha de ingreso sin tener
 * en cuenta la horas de egreso e ingreso
 ******************************************************************************************************************/
function consultarDiasEstanciaInstitucionales( $conex, $wbasedato, $wcliame, $fecha_inicial, $fecha_final )
{
	$diasEInstitucionales = 0;

	//Consultando los pacientes egresados en un rango de fechas
	$sql = "SELECT Egrfee, Ingfei
			  FROM ".$wcliame."_000108 a, ".$wcliame."_000112 b, ".$wcliame."_000101 d, ".$wbasedato."_000011
			 WHERE Egrfee BETWEEN '".$fecha_inicial."' AND '".$fecha_final."'
			   AND Egrhis = Serhis
			   AND Egring = Sering
			   AND Seregr = 'on'
			   AND Sercod = ccocod
			   AND Inghis = Egrhis
			   AND Ingnin = Egring
			   AND ccohos = 'on'";
	// echo "<pre>$sql</pre>";
	$result = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query: $sql - ".mysql_error() );
	$num 	= mysql_num_rows( $result );
	
	$fraccionesDia = 0;
	
	//Consultando los días de estancia institucionales
	if( $num > 0 ){
		
		while( $rows = mysql_fetch_array( $result ) ){
			
			$fraccionesDia = strtotime( $rows['Egrfee']." 00:00:00" ) - strtotime( $rows['Ingfei']." 00:00:00" );
			$fraccionesDia = round( $fraccionesDia/(24*3600), 2 );
			
			$diasEInstitucionales += $fraccionesDia;
		}
		
		if( $diasEInstitucionales < 0 ){
			$diasEInstitucionales = 0;
		}
	}
	
	return $diasEInstitucionales;
}

/******************************************************************************************************************************
 *Consulta los indicadores dados los parametros
 ******************************************************************************************************************************/
function consultarIndicadoresHospitalarios($wservicio,$wfechaInicial,$wfechaFinal,$diasFechasConsulta)
{

	global $wbasedato;
	global $wtabcco;
	global $conex;
	global $wemp_pmla;
	global $wcliame;
	
	$coleccion = array();

	if ($wtabcco == 'costosyp_000005'){

		$q = "    SELECT 	cieser, ciedis, cieocu, cieing, cieegr, cieiye, ciedes, Ciemmay, Ciemmen, Cieinu, Cieinc, Cieina, Cieint, Ciegrt, Ciedit, ".$wbasedato."_000011.ccohib, 
							Ciediam, Cieeal, ".$wtabcco.".cconom, A.fecha_data, 
							(SELECT  sum(cieocu) 
							   FROM  ".$wbasedato."_000038 B 
							  WHERE B.fecha_data = DATE_SUB(A.fecha_data, INTERVAL 1 DAY) AND B.cieser = A.cieser) pacDiaAnterior				
					FROM 	".$wbasedato."_000038 A, ".$wtabcco.", ".$wbasedato."_000011 
				   WHERE 	A.fecha_data BETWEEN '".$wfechaInicial."' AND '".$wfechaFinal."'
				     AND 	cieser LIKE '".$wservicio."'	
				     AND 	cieser = ".$wtabcco.".ccocod
				     AND 	cieser = ".$wbasedato."_000011.ccocod
				     AND 	Ccourg != 'on'
				     AND    ".$wtabcco.".Ccoemp = '".$wemp_pmla."'  
			    ORDER BY   	cieser,A.fecha_data";  

	}
	else{
	
		$q = "    SELECT 	cieser, ciedis, cieocu, cieing, cieegr, cieiye, ciedes, Ciemmay, Ciemmen, Cieinu, Cieinc, Cieina, Cieint, Ciegrt, Ciedit, ".$wbasedato."_000011.ccohib, 
							Ciediam, Cieeal, ".$wtabcco.".cconom, A.fecha_data, 
							(SELECT  sum(cieocu) 
							   FROM  ".$wbasedato."_000038 B 
							  WHERE B.fecha_data = DATE_SUB(A.fecha_data, INTERVAL 1 DAY) AND B.cieser = A.cieser) pacDiaAnterior				
					FROM 	".$wbasedato."_000038 A, ".$wtabcco.", ".$wbasedato."_000011 
				   WHERE 	A.fecha_data BETWEEN '".$wfechaInicial."' AND '".$wfechaFinal."'
				     AND 	cieser LIKE '".$wservicio."'	
				     AND 	cieser = ".$wtabcco.".ccocod
				     AND 	cieser = ".$wbasedato."_000011.ccocod
				     AND 	Ccourg != 'on'
			    ORDER BY   	cieser,A.fecha_data";
	}	    

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$rs = mysql_fetch_array($res);
	
	while($rs)
	{
		$indicador = new indicador();
		
		$indicador->diasEInstitucionales = 0;
		
		//Consultados
		$indicador->codServicio 		= $rs['cieser'];						//Servicio  
		$indicador->servicio 			= $rs['cieser']." - ".$rs['cconom'];	//Servicio  
		$indicador->camasDisponibles	= $rs['ciedis'];						//Camas Desocupadas
		$indicador->camasOcupadas		= $rs['cieocu'];						//Camas Ocupadas
		$indicador->ingTotales			= $rs['cieing'];						//Ingresos
		$indicador->egrTotales 			= $rs['cieegr'];						//Egresos
		$indicador->ingYEgrDia 			= $rs['cieiye'];						//Ing y Egr del mismo dia
		$indicador->diasEstanciaTotales	= $rs['ciedes'];						//Dias estancia (Egresados)
		$indicador->egrMmay48 			= $rs['Ciemmay'];						//Muertes mayores a 48 horas
		$indicador->egrMmen48			= $rs['Ciemmen'];						//Muertes menores a 48 horas
		$indicador->ingU	 			= $rs['Cieinu'];						//Ingresos por urgencias
		$indicador->ingC	 			= $rs['Cieinc'];						//Ingresos por cirugía
		$indicador->ingA 				= $rs['Cieina'];						//Ingresos por admisiones
		$indicador->ingT	 			= $rs['Cieint'];						//Ingresos por traslados
		$indicador->egrT	 			= $rs['Ciegrt'];						//Egresos por traslado
		$indicador->diasEEgrT			= $rs['Ciedit'];						//Dias estancia traslado
		$indicador->diasEAltasM			= $rs['Ciediam'];						//Dias estancia altas muertes
		$indicador->egrA 				= $rs['Cieeal'];						//Egresos por altas
		$indicador->fecha	 			= $rs['fecha_data'];
		
		if(isset($rs['pacDiaAnterior']))
		{
			$indicador->pacDiaAnterior	= $rs['pacDiaAnterior'];
		} 
		else 
		{
			$indicador->pacDiaAnterior	= 0;
		}
		
		//Calculados
		$indicador->ingTotalesSinTrasl	= intval($indicador->ingU) + intval($indicador->ingC) + intval($indicador->ingA);
		$indicador->egrTotalesSinTrasl	= intval($indicador->egrA) + intval($indicador->egrMmay48) + intval($indicador->egrMmen48);
		
		$indicador->pacALaFecha			= abs(intval($indicador->pacDiaAnterior) + intval($indicador->ingTotales) - intval($indicador->egrTotales));										//Pacientes a la fecha = Pacientes dia anterior + Ingresos - Egresos
		$indicador->diasCamaDisponible	= intval($indicador->camasDisponibles) + intval($indicador->camasOcupadas); 																//Total Días Cama Disponible = Camas disponibles + camas ocupadas
		// --> 2019-09-02, Jerson: Se modifica el calculo por definicion de registros medicos, esto se hizo porque
		//	cuando se daba click en el detalle eran distintos los valores del total detallado con el general.
		$indicador->diasCamaOcupada		= $rs['cieocu'];//abs(intval($indicador->pacDiaAnterior) + intval($indicador->ingTotales) - intval($indicador->egrTotales) + intval($indicador->ingYEgrDia));	//Total Días Cama Ocupada    = (Ingresos - Egresos + (Pacientes que Ingresaron y Egresaron el mismo día))
		
		if(intval($indicador->egrTotales) > 0)
		{
			@$indicador->promDiasEstancia		=  ( $indicador->diasEAltasM + $indicador->diasEEgrT ) / $indicador->egrTotales; //Dias estancia = Total dias estancia del período/Total egresos del período
			$indicador->tasaMortalidad			= ((intval($indicador->egrMmay48) + intval($indicador->egrMmen48) / intval($indicador->egrTotales)) * 100);								//Tasa de Mortalidad = ((número de muertes del período/Total egresos del período)*100)
			$indicador->tasaMortalidadMayor48	= ((intval($indicador->egrMmay48) / intval($indicador->egrTotales)) * 100);																//Tasa de Mortalidad = ((número de muertes del período > 48/Total egresos del período)*100)	
			$indicador->tasaMortalidadMenor48	= ((intval($indicador->egrMmen48) / intval($indicador->egrTotales)) * 100);																//Tasa de Mortalidad = ((número de muertes del período < 48/Total egresos del período)*100)
		} 
		else 
		{
			$indicador->promDiasEstancia		= 0;																								//Dias estancia = Total dias estancia del período/Total egresos del período
			$indicador->tasaMortalidad			= 0; 			
			$indicador->tasaMortalidadMayor48	= 0;
			$indicador->tasaMortalidadMenor48	= 0;
		}
			//Controlar div por cero
			$indicador->porcOcupacion		= @( intval($indicador->diasCamaOcupada) / intval($indicador->diasCamaDisponible) * 100 );   											//Porcentaje Ocupacional			
		
		//El indicador se consulta por dia.  Asi que este valor siempre será 1
		$diasFechasConsulta = 1;
		$indicador->promCamasOcupadas 		= intval($indicador->diasCamaOcupada) / $diasFechasConsulta;
		
		//if( $wservicio == '1179' ){
		if( $rs['ccohib'] == 'on' ){
			//Es para los pisos que tienen ingreso directo, como medicina nuclear
			$indicador->diasCamaOcupada		= abs(intval($indicador->pacDiaAnterior) + intval($indicador->ingTotales) - intval($indicador->egrTotales));
			$indicador->porcOcupacion		= @( intval($indicador->diasCamaOcupada) / intval($indicador->diasCamaDisponible) * 100 );  
			$indicador->promCamasOcupadas 		= intval($indicador->diasCamaOcupada);
		}

		//Controlar div por cero
		$indicador->rendimientoHospitalario = @(intval($indicador->egrTotales) / ROUND(intval($indicador->diasCamaDisponible)));  								//Rendimiento hospitalario = Total egresos del período/Numero de Camas
		$indicador->nroCamas				= intval($indicador->diasCamaDisponible) / $diasFechasConsulta;		
		
		if($indicador->porcOcupacion > 0)
		{
			$indicador->indiceSustitucion 	= ((100-$indicador->porcOcupacion)*$indicador->promDiasEstancia) / $indicador->porcOcupacion; 
		} 
		else 
		{
			$indicador->indiceSustitucion 	= 0;
		}
		
		$coleccion[] = $indicador;
		
		// echo "<pre>";
		// print_r($coleccion);
		// echo "</pre>";
		
		
		$rs = mysql_fetch_array($res);
		
	}
	
	// --> 	2020-02-04: Nuevo calculo de dias estancia por traslado y dias estancia por altas y muertes.
	//		Esto se hace por solicitud de Nancy (gestion de la informacion) no se tendrá el cuenta el campo calculado 
	//		de la movhos_38 (Ciedit, Ciediam) ya que presentá incosistencias, sino que se calculará con base al movimiento de la movhos_32 y 33
	foreach($coleccion as $idCol => &$registros){
		
		$registros->diasEEgrT 	= 0;
		$registros->diasEAltasM = 0;
		
		// --> Consultar los egresos del servicio
		$arrEstPac = array();
		$sql32 = "
		SELECT 	A.Historia_clinica, A.Num_ingreso, A.Tipo_egre_serv, A.Fecha_egre_serv, A.Hora_egr_serv, B.Fecha_ing, B.Hora_ing,
				(TIMESTAMPDIFF(HOUR, concat(B.Fecha_ing, ' ', B.Hora_ing), concat(A.Fecha_egre_serv, ' ', A.Hora_egr_serv))/24) as tiempEst
		  FROM ".$wbasedato."_000033 AS A INNER JOIN ".$wbasedato."_000032 AS B ON(
					A.Historia_clinica 	= B.Historia_clinica 
					AND A.Num_ingreso 	= B.Num_ingreso 
					AND A.`Servicio` 	= B.`Servicio`
					AND Fecha_egre_serv >= Fecha_ing)
		 WHERE A.`Fecha_egre_serv` 	= '".$registros->fecha."' 
		   AND A.`Servicio` 		= '".$registros->codServicio."'
		";
		$res32 = mysql_query($sql32, $conex) or die("<b>ERROR EN QUERY MATRIX(sql32):</b><br>".mysql_error());
		while($row32 = mysql_fetch_array($res32)){
			
			$paciente = $row32['Historia_clinica']."-".$row32['Num_ingreso'];
			
			// --> Si el egreso fue por alta o muerte
			if(stripos($row32['Tipo_egre_serv'], 'alta') !== false || stripos ($row32['Tipo_egre_serv'], 'muerte') !== false){
				// --> 	Si el query devolvio mas de un registro para la misma historia ingreso, significa que el paciente
				//		tiene mas de un ingreso al servicio, por ende debo identificar cual es el ingreso correspondiente al mismo egreso
				//		esto lo hago dejando solo para el conteo el ingreso mas proximo a la fecha de egreso, osea
				//		el registro que tenga menos tiempo de estancia.
				if(!array_key_exists($paciente, $arrEstPac) || $arrEstPac["ALTA"][$paciente] > $row32['tiempEst'])
					$arrEstPac["ALTA"][$paciente] = $row32['tiempEst'];					
			}
			else{
				if(!array_key_exists($paciente, $arrEstPac) || $arrEstPac["TRASLADO"][$paciente] > $row32['tiempEst'])
					$arrEstPac["TRASLADO"][$paciente] = $row32['tiempEst'];	;
			}
		}
		
		if(array_key_exists("ALTA", $arrEstPac)){
			foreach($arrEstPac["ALTA"] as $tiemEst1){
				$registros->diasEAltasM+=$tiemEst1;
			}
		}
		
		if(array_key_exists("TRASLADO", $arrEstPac)){
			foreach($arrEstPac["TRASLADO"] as $tiemEst2){
				$registros->diasEEgrT+=$tiemEst2;
			}
		}
		
		$registros->diasEAltasM = number_format($registros->diasEAltasM, 2);
		$registros->diasEEgrT	= number_format($registros->diasEEgrT, 2);
		
		@$registros->promDiasEstancia =  ( $registros->diasEAltasM + $registros->diasEEgrT ) / $registros->egrTotales;	
	}
	
	
	return $coleccion;
}

function consultarInfoPaciente($conex, $historia){
	
	global $wbasedato;	
	global $wemp_pmla;
	global $conex;
    
	$q = "SELECT 
			C.inghis,pacno1, pacno2, pacap1, pacap2, pactid, pacced, ingnre, pacnac, pacsex
		FROM 
			root_000036, root_000037, ".$wbasedato."_000016 C
		WHERE
			oriced       = pacced
			AND oritid   = pactid 
			AND orihis   = '".$historia."'			
			AND oriori   = '".$wemp_pmla."'
            AND C.inghis = orihis
			AND inging   = oriing ";	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	
	$info = array();
	if ($num > 0)
	{		
		$info = mysql_fetch_assoc($res);		
	}
    
	return $info;
}

function consultarDetalleDiaCamaOcupada($wemp_pmla, $servicio, $wfechaInicial, $wfechaFinal){
	
	global $wbasedato;
	global $conex;
	
	$cod_cco = explode("-", $servicio);
	$codigo_cco = trim($cod_cco[0]);
	
	$array_datos_pacientes = array();   
	
	$q = " SELECT Habcod, Habhis, Habing FROM ".$wbasedato."_000067
			WHERE Fecha_data BETWEEN '".$wfechaInicial."' AND '".$wfechaFinal."' 
			  AND Habcco = '".$codigo_cco."' 
			  AND Habhis != ''
			  AND Habhis != 'NO APLICA' ";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		
	while($rs = mysql_fetch_assoc($res))
	{
		
		$array_datos_pacientes[trim($rs['Habcod'])][trim($rs['Habhis'])."-".trim($rs['Habing'])][] = $rs;	
		
	}
    
    //print_r($array_datos_pacientes);
    $array_hab_dispon = array();
     
    $q1 = " SELECT Habcod, Habhis, Habing FROM ".$wbasedato."_000067 
			 WHERE Fecha_data BETWEEN '".$wfechaInicial."' AND '".$wfechaFinal."'";
	$res1 = mysql_query($q1, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q1 . " - " . mysql_error());
		
	while($rs1 = mysql_fetch_assoc($res1))
	{
		
		$array_hab_dispon[trim($rs1['Habcod'])][] = $rs1;	
		
	}
       
	
	//print_r($array_hab_dispon);
	echo "<table border=0 align=center>";
	echo "<tr class='encabezadoTabla'>";
	echo "<td>Cama</td>";
	echo "<td>Dias cama ocupada</td>";
    echo "<td>Dias cama disponible</td>";
    echo "<td>Porcentaje de ocupación</td>";
	echo "</tr>";
    $j = 0;
	foreach($array_datos_pacientes as $key => $value_his){
		
         if($j % 2 == 0)
    			$wclass1="fila1";
    		else
    			$wclass1="fila2";
        
        echo "<tr>";
		echo "<td class='".$wclass1."'>".$key."</td>";
		$suma_ocupada = 0;
        $cuantos = 0;
        $info_his = "";
        $i = 0;
        $cuantos_total = 0;
		foreach( $value_his as $key1 => $value ){
			
        	if($i % 2 == 0)
    			$wclass2="fila1";
    		else
    			$wclass2="fila2";
            
			$cuantos = count($value);
            $cuantos_total = $cuantos_total+$cuantos;
            $whis = explode("-",$key1); 
			$suma_ocupada = $suma_ocupada + count($value);		
            
            $datos_paciente = consultarInfoPaciente($conex, trim($whis[0]));
    		    	
    		$pacno1 = $datos_paciente['pacno1'];
    		$pacno2 = $datos_paciente['pacno2'];
    		$pacap1 = $datos_paciente['pacap1'];
    		$pacap2 = $datos_paciente['pacap2'];
                
            $info_his .= "<tr class='".$wclass2."'><td>".$key1."</td><td>".$pacno1." ".$pacno2." ".$pacap1." ".$pacap2."</td><td>".$cuantos."</td></tr>";
            
            $i++;
        
		}
        $total_cama = "<tr class='encabezadoTabla'><td colspan=2>Total</td><td>".$cuantos_total."</td></tr>";
        echo "<td style='display:none;' id='detalle_his_$key'><table border=0 class='encabezadoTabla'><tr onclick='ver_historias(\"detalle_his_$key\", \"$key\");' style='cursor:pointer;'><td>Historia</td><td>Nombre</td><td>Dias</td></tr>$info_his $total_cama</table></td>";
		echo "<td id='total_".$key."' class='".$wclass1."' onclick='ver_historias(\"detalle_his_$key\", \"$key\");' style='cursor:pointer;'>".$suma_ocupada."</td>";
        echo "<td class='".$wclass1."'>".count($array_hab_dispon[$key])."</td>";
        echo "<td class='".$wclass1."'>".round((($suma_ocupada/(count($array_hab_dispon[$key])))*100),2)."</td>";        
		echo "</tr>";
        
        $suma_ocupada_total = $suma_ocupada_total + $suma_ocupada;   
        $j++;
		
	}
	
    echo "<tr class='encabezadoTabla'><td>Subtotal</td><td>".$suma_ocupada_total."</td><td></td><td></td></tr>";
	echo "</table>";
}



function consultarDetalle( $wfecha, $wcco, $wcconom, $wtipo ){
	global $wbasedato;
	global $conex,$wemp_pmla;
	/*IU: Ingresos urgencias, piso 1130
	IA: Ingresos Admisiones, piso 1800
	IC: Ingresos Cirugia, piso 1016
	IH: Ingresos Hemodinamia
	IT: Ingresos por traslado
	EA: Egresos por alta
	EME: Egresos muerte menor a 48 horas
	EMA: Egresos muerte mayor a 48 horas
	ET: Egresos por traslado*/
	
	$tituloTipo = "INGRESOS  ";
	if( $wtipo == 'EA' || $wtipo == 'EME' || $wtipo == 'EMA' || $wtipo == 'ET' )
		$tituloTipo = "EGRESOS POR ";
	$and_query = "";
	switch($wtipo){
		case 'IU':
			$and_query =" AND 	F.Procedencia = '1130'"; 
			$tituloTipo.=" DE URGENCIAS";				
			break;
		case 'IA':
			$and_query =" AND 	F.Procedencia = '1800'";  
			$tituloTipo.=" DE ADMISIONES";
			break;
		case 'IC':
			$and_query =" AND 	F.Procedencia = '1016'";  
			$tituloTipo.=" DE CIRUGIA";
			break;
		case 'EA':
			$and_query =" AND 	F.Tipo_egre_serv = 'ALTA'";
			$tituloTipo.=" ALTA";
			break;
		case 'EMA':
			$and_query =" AND 	F.Tipo_egre_serv = 'MUERTE MAYOR A 48 HORAS'";
			$tituloTipo.=" MUERTE MAYOR A 48 HORAS";
			break;
		case 'EME':
			$and_query =" AND 	F.Tipo_egre_serv = 'MUERTE MENOR A 48 HORAS'";
			$tituloTipo.=" MUERTE MENOR A 48 HORAS";
			break;
		case 'IH':
			$and_query =" AND 	F.Procedencia IN (SELECT Ccocod
										FROM ".$wbasedato."_000011 
										WHERE Ccoest =  'on'
										  AND Ccohos !=  'on'
										  AND Ccoing =  'on'
										  AND Ccohib !=  'on'
										  AND Ccocir !=  'on'
										  AND Ccourg !=  'on'
										  AND Ccoadm !=  'on'
										ORDER by 1)";
			$tituloTipo.=" DE HEMODINAMIA";
			break;
		case 'IT':
			$and_query =" AND 	F.Procedencia IN (SELECT Ccocod
										FROM ".$wbasedato."_000011 
										WHERE Ccoest = 'on' AND Ccohos = 'on' AND ( Ccoing != 'on' OR Ccohib = 'on' )
										ORDER by 1)";
			$tituloTipo.=" POR TRASLADO";
			break;
		case 'ET':
			$and_query =" AND 	F.Tipo_egre_serv IN (SELECT Ccocod
										FROM ".$wbasedato."_000011 
										WHERE Ccoest = 'on' AND Ccohos = 'on' AND ( Ccoing != 'on' OR Ccohib = 'on' )
										ORDER by 1)";
			$tituloTipo.=" TRASLADO";
			break;
	}
	
	$q = "";
	if( $wtipo == 'IU' || $wtipo == 'IA' || $wtipo == 'IC' || $wtipo == 'IT' || $wtipo == 'IH'){
		$q = "  SELECT 	A.Pacno1, A.Pacno2, A.Pacap1, A.Pacap2, A.Pactid, A.Pacced, F.servicio as ccocod,
						F.Historia_clinica as historia, F.Num_ingreso as ingreso, F.Hora_ing as hora, P.Cconom as piso
				  FROM  root_000036 A, root_000037 B, ".$wbasedato."_000032 F LEFT JOIN ".$wbasedato."_000011 P ON (F.Procedencia=P.Ccocod)
				 WHERE 	F.Historia_clinica = B.Orihis
				   AND 	F.Fecha_ing = '".$wfecha."'
				   AND 	F.Servicio = '".$wcco."' "
				   .$and_query."
				   AND 	A.Pacced = B.Oriced
				   AND 	A.Pactid = B.Oritid
				   AND 	B.Oriori = '".$wemp_pmla."'
			  ORDER BY  Fecha_ing, hora";
	}else if( $wtipo == 'EA' || $wtipo == 'EME' || $wtipo == 'EMA' || $wtipo == 'ET'){
		$q = "  SELECT 	A.Pacno1, A.Pacno2, A.Pacap1, A.Pacap2, A.Pactid, A.Pacced, F.servicio as ccocod, 
						F.Historia_clinica as historia, F.Num_ingreso as ingreso, F.Hora_egr_serv as hora, P.Cconom as piso
				 FROM  	root_000036 A, root_000037 B, ".$wbasedato."_000033 F LEFT JOIN ".$wbasedato."_000011 P ON (F.Tipo_egre_serv=P.Ccocod)
				WHERE 	F.Historia_clinica = B.Orihis 
				  AND 	F.Fecha_egre_serv = '".$wfecha."'			
				  AND 	F.Servicio = '".$wcco."'"
				  .$and_query."
				  AND 	A.Pacced = B.Oriced 
				  AND 	A.Pactid = B.Oritid        
				  AND 	B.Oriori = '".$wemp_pmla."'
			 ORDER BY   Fecha_egre_serv, hora";
	}
	echo "<center><table align='center'>";
	$colspan = 4;
	if( $wtipo == 'IT' || $wtipo == 'ET' )
		$colspan = 5;
	echo "<tr class=titulo><td colspan=".$colspan." align='center'>";
	echo "<font size=4><b>".$tituloTipo."</b></font><BR>".$wfecha."<br>".$wcco." - ".$wcconom;
	echo "</td></tr>";
	echo "<tr class=encabezadoTabla>";
	echo "<td align='center'>Historia</td>";
	echo "<td align='center'>Ingreso</td>";
	echo "<td align='center'>Paciente</td>";
	if( $wtipo == 'IU' || $wtipo == 'IA' || $wtipo == 'IC' || $wtipo == 'IT' || $wtipo == 'IH' )
		echo "<td align='center'>Hora<br/>de Ingreso</td>";
	else if($wtipo == 'EA' || $wtipo == 'EME' || $wtipo == 'EMA' || $wtipo == 'ET')
		echo "<td align='center'>Hora<br/>de Egreso</td>";
	if( $wtipo == 'IT' )
		echo "<td align='center'>Piso<br/>Origen</td>";	
	if( $wtipo == 'ET' )
		echo "<td align='center'>Piso<br/>Destino</td>";
	echo "</tr>";
	$res= mysql_query($q, $conex);	
	$i=0;		
	while($row = mysql_fetch_assoc($res)){
		if($i % 2 == 0)
			$wclass="fila1";
		else
			$wclass="fila2";			
		$row['hora']= substr_replace( $row['hora'] ,"",-3 );
		echo "<tr class='".$wclass."'>";
		echo "<td align='center'>".$row["historia"]."</td>"; //historia
		echo "<td align='center'>".$row["ingreso"]."</td>"; //ingreso
		echo "<td align='left' nowrap='nowrap'>".$row["Pacno1"]." ".$row["Pacno2"]." ".$row["Pacap1"]." ".$row["Pacap2"]."</td>"; //paciente
		echo "<td align='center'>".$row['hora']."</td>"; //hora ingreso
		if( $wtipo == 'IT' || $wtipo == 'ET' )
			echo "<td align='center'>".$row['piso']."</td>"; //hora ingreso
		echo "</tr>";
		$i++;
	}
	echo "</table>";
	//echo "<br><input type='button' value='Cerrar' onclick='$.unblockUI()' /></center>";
	echo "<br><input type='button' value='Cerrar' onclick='cerrarModal()' /></center>";

}

/**
 * Inicio de la aplicacion
 */
include_once("root/comun.php");

$wactualiz = "2020-02-10";

if (!isset($user))
	if(!isset($_SESSION['user']))
		session_register("user");

if(!isset($_SESSION['user']))
	terminarEjecucion($MSJ_ERROR_SESION_CADUCADA);
else
{
	$conex = obtenerConexionBD("matrix");
	
	$wfecha=date("Y-m-d");

	if(!isset($wemp_pmla))
	{
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
	}
	
	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
	$winstitucion = $institucion->nombre;

	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$wtabcco = consultarAliasPorAplicacion($conex, $wemp_pmla, "tabcco");
	$wcliame = consultarAliasPorAplicacion($conex, $wemp_pmla, "facturacion");

	if (isset($consultaAjax)){
		switch($consultaAjax){ 
			
			case 'consultarDetalle':								
				echo consultarDetalle($wfechap, $wcco, $wcconom, $wtipo);		
				break;
				
			case 'consultarDetalleDiaCamaOcupada':								
				echo consultarDetalleDiaCamaOcupada($wemp_pmla, $servicio, $wfechaInicial, $wfechaFinal);		
				break;			
		}
		return;
	}
	//Encabezado 
	encabezado("INDICADORES HOSPITALARIOS DIA POR DIA",$wactualiz,"clinica");
	
	echo "<form name='indhosp' action='indicadores_hospitalarios_dia.php' method=post>";

	echo "<input type='HIDDEN' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";
	echo "<div style='display:none' id='aux_respuesta'></div>";
	echo "<div style='display:none;' id='detalle_dias_cama_ocupada'></div>";
	
	echo "<div id='msjEspere' style='display:none;'>";
	echo '<br>';
	echo "<img src='../../images/medical/ajax-loader5.gif'/>";
	echo "<br><br> Por favor espere un momento ... <br><br>";
	echo '</div>';
	
	echo '<div class="caja_flotante_query"></div>';
	
	if(!isset($waccion))
	{
		$waccion = "";
	}
	
	switch ($waccion){
		case 'a': //Consulta de los indicadores
				
			echo '<div align="center"><span class="subtituloPagina">';
			echo "Indicadores hospitalarios por servicio.  Desde $wfechaInicial hasta $wfechaFinal";
			echo "</span></div>";
			
			echo "<br>";
			
			echo "<center><br><input type='button' value='Regresar' onClick='javascript:inicio(\"$wfechaInicial\",\"$wfechaFinal\",\"$wservicio\");'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></center><br />";
			
			//echo "<div></div>";
			$title = "En los campos de ingreso y egreso ubique el mouse sobre la casilla<br>Si el icono cambia puede dar click para consultar el detalle";
			echo "<div align='left' style='margin-left:20px; font-size: 8pt;'><a href='#' class='msg_tooltip' title='".$title."' id='enlace_ayuda'>Ver detalle</a></div>";
		
		
			echo "<table align='center' border=0 class='tdcentrado'>";
			
			$fechaTemp = "";
			
			if(isset($wservicio) && isset($wfechaInicial) && isset($wfechaFinal))
			{
				
				//Variables de presentación
				$cont1 = 0;
				$clase = "";
				$mostrarSubtotales = false;
				
				//Diferencia de dias en la fecha de consulta
				$vecFechaInicial = explode("-",$wfechaInicial);
				$vecFechaFinal = explode("-",$wfechaFinal);
								
				$calcDiaInicial = mktime(0,0,0,$vecFechaInicial[1],$vecFechaInicial[2],$vecFechaInicial[0]);
				$calcDiaFinal = mktime(0,0,0,$vecFechaFinal[1],$vecFechaFinal[2],$vecFechaFinal[0]);
				
				$diasFechasConsulta = ROUND(($calcDiaFinal-$calcDiaInicial)/(60*60*24)) + 1;
			
				//Consulta de los indicadores
				$colIndicadoresHospitalarios = consultarIndicadoresHospitalarios($wservicio,$wfechaInicial,$wfechaFinal,$diasFechasConsulta);
				//echo $colIndicadoresHospitalarios."<br>";
				//echo count($colIndicadoresHospitalarios);
				
				if(count($colIndicadoresHospitalarios) > 0)
				{
					//Encabezado del reporte
					
					echo "<tr class='encabezadoTabla caja_flotante_query_ori1'>";

					echo "<td rowspan=2>Servicio</td>";
					echo "<td rowspan=2>Fecha</td>";
					echo "<td colspan=5>Ingresos</td>"; //2012-11-08 colspan=4  por colspan=5
					echo "<td rowspan=2>Ingresos por traslado</td>";					
					echo "<td colspan=4>Egresos</td>";
					echo "<td rowspan=2>Egresos por traslado</td>";
					echo "<td colspan=2>Dias estancia</td>";
					echo "<td rowspan=2>Pacientes día ant.</td>";
					echo "<td rowspan=2>Pacientes a la fecha</td>";
					echo "<td rowspan=2>Ingresos y egresos dia</td>";
					echo "<td rowspan=2>Días Cama Ocupada</td>";
					echo "<td rowspan=2>Nro Camas</td>";
					echo "<td rowspan=2>Días Cama Disponible</td>";					
					echo "<td rowspan=2>Prom. Camas Ocupadas</td>";
					//echo "<td rowspan=2>Días de<br>Estancia</td>";
					echo "<td rowspan=2>% de Ocupacion</td>";
					echo "<td rowspan=2>Promedio Dias<br>Estancia</td>";
					echo "<td rowspan=2>Rendimiento<br>Hospitalario</td>";
					echo "<td rowspan=2>Indice de<br>Sustitución</td>";
					echo "<td rowspan=2>Tasa de <br>Mortalidad</td>";
					echo "<td rowspan=2>Tasa Mortalidad<br> > 48 Horas</td>";
					echo "<td rowspan=2>Tasa Mortalidad<br> < 48 Horas</td>";

					echo "</tr>";

					echo "<tr class='encabezadoTabla caja_flotante_query_ori2'>";

					echo "<td>Urgencias</td>";
					echo "<td>Admisiones</td>";
					echo "<td>Cirugia</td>";
					echo "<td>Hemodinamia</td>"; //2012-11-08, // 2013-01-08
					echo "<td>Total</td>";
					echo "<td>Altas</td>";					
					echo "<td>Muertes < 48 Horas</td>";
					echo "<td>Muertes > 48 Horas</td>";
					echo "<td>Total</td>";
					echo "<td>Altas y muertes</td>";
					echo "<td>Egresos por traslado</td>";

					echo "</tr>";

					//Acumuladores de subtotales y totales generales
					$subIndicadores = new indicador();
					$totIndicadores = new indicador();
					$diasFechasConsulta = 0;
					//echo print_r($colIndicadoresHospitalarios);
					
					foreach ($colIndicadoresHospitalarios as $indicador)
					{
						//echo $diasFechasConsulta." linea 343<br>";
						if($cont1 % 2 == 0)
						{
							$clase = "fila1";
						}
						else
						{
							$clase = "fila2";
						}

						//Subtotal indicadores
						if($cont1 > 0 && $fechaTemp != $indicador->servicio)
						{
							echo "<tr class=encabezadoTabla>";

							echo "<td colspan=2>Subtotal</td>";
							echo "<td>$subIndicadores->ingU</td>";
							echo "<td>$subIndicadores->ingA</td>";
							echo "<td>$subIndicadores->ingC</td>";
							$otros = $subIndicadores->ingTotales - ($subIndicadores->ingU + $subIndicadores->ingA + $subIndicadores->ingC + $subIndicadores->ingT); //2012-11-08
							echo "<td>".$otros."</td>";//2012-11-08
							$subIndicadores->ingTotalesSinTrasl += $otros; // 2012-11-08 Se le suma la cantidad de otros
							echo "<td>$subIndicadores->ingTotalesSinTrasl</td>"; 
							echo "<td>$subIndicadores->ingT</td>";							
							echo "<td>$subIndicadores->egrA</td>";							
							echo "<td>$subIndicadores->egrMmen48</td>";
							echo "<td>$subIndicadores->egrMmay48</td>";
							echo "<td>$subIndicadores->egrTotalesSinTrasl</td>";
							echo "<td>$subIndicadores->egrT</td>";
							echo "<td>$subIndicadores->diasEAltasM</td>";
							echo "<td>$subIndicadores->diasEEgrT</td>";
							echo "<td>&nbsp;</td>";
							echo "<td>$subIndicadores->pacALaFecha</td>";
							echo "<td>$subIndicadores->ingYEgrDia</td>";
							echo "<td onclick='consultarDetalleDiaCamaOcupada(this, \"$fechaTemp\", \"$wfechaInicial\", \"$wfechaFinal\");' style='cursor:pointer;'>$subIndicadores->diasCamaOcupada</td>";
								
							//Calculados
							$diasTotalesRendimiento = ($subIndicadores->diasCamaDisponible / $diasFechasConsulta);
							@$acumuladoDiasTotalesCamaDisponible += $subIndicadores->diasCamaDisponible; 
							$nroCamas = round($subIndicadores->diasCamaDisponible / $diasFechasConsulta);
							
							//$subIndicadores->rendimientoHospitalario = @(($subIndicadores->egrTotalesSinTrasl+$subIndicadores->egrT)/$nroCamas);
							$subIndicadores->porcOcupacion = @(($subIndicadores->diasCamaOcupada/$subIndicadores->diasCamaDisponible)*100);
							$subIndicadores->promDiasEstancia = @( ( $subIndicadores->diasEAltasM + $subIndicadores->diasEEgrT )/($subIndicadores->egrTotalesSinTrasl + $subIndicadores->egrT));
							//echo "I'$subIndicadores->egrTotalesSinTrasl' '$subIndicadores->egrT' '$nroCamas'";
							if($subIndicadores->porcOcupacion > 0)
							{
								$subIndicadores->indiceSustitucion = @((100-$subIndicadores->porcOcupacion)*$subIndicadores->promDiasEstancia)/$subIndicadores->porcOcupacion;
							}
							else
							{
								$subIndicadores->indiceSustitucion = 0;
							}
							
							$subIndicadores->tasaMortalidad = @(($subIndicadores->egrMmay48 + $subIndicadores->egrMmen48) / ($subIndicadores->egrA + $subIndicadores->egrMmay48 + $subIndicadores->egrMmen48 + $subIndicadores->egrT))*100;
							$subIndicadores->tasaMortalidadMayor48 = @(($subIndicadores->egrMmay48) / ($subIndicadores->egrA + $subIndicadores->egrMmay48 + $subIndicadores->egrMmen48 + $subIndicadores->egrT))*100;
							$subIndicadores->tasaMortalidadMenor48 = @(($subIndicadores->egrMmen48) / ($subIndicadores->egrA + $subIndicadores->egrMmay48 + $subIndicadores->egrMmen48 + $subIndicadores->egrT))*100;
							$totIndicadores->nroCamas += $nroCamas;
							
							echo "<td>$nroCamas</td>";
							echo "<td>".$subIndicadores->diasCamaDisponible."</td>";
							echo "<td>".round($subIndicadores->promCamasOcupadas/$diasFechasConsulta)."</td>";   //Dias cama disponible
							//echo "<td>$subIndicadores->diasEstanciaTotales</td>";
							echo "<td>".number_format($subIndicadores->porcOcupacion,2,'.',',')."</td>";
							echo "<td>".number_format($subIndicadores->promDiasEstancia,2,'.',',')."</td>";
							if(intval($nroCamas) > 0)
							{
								echo "<td>".number_format((($subIndicadores->egrTotalesSinTrasl+$subIndicadores->egrT)/$nroCamas),2,'.',',')."</td>";  //Rendimiento hospitalario
							} 
							else 
							{
								echo "<td>".number_format(0,2,'.',',')."</td>";  //Rendimiento hospitalario
							}
							echo "<td>".number_format($subIndicadores->indiceSustitucion,2,'.',',')."</td>";
							echo "<td>".number_format($subIndicadores->tasaMortalidad,2,'.',',')."</td>";
							echo "<td>".number_format($subIndicadores->tasaMortalidadMayor48,2,'.',',')."</td>";
							echo "<td>".number_format($subIndicadores->tasaMortalidadMenor48,2,'.',',')."</td>";

							echo "</tr>";
							
							/*if($wservicio == "%")  //2012-11-14
							{
								echo "<tr class='encabezadoTabla'>";

								echo "<td rowspan=2>Servicio</font></td>";
								echo "<td rowspan=2>Fecha</td>";
								echo "<td colspan=5>Ingresos</td>"; //2012-11-08 colspan=4  por colspan=5
								echo "<td rowspan=2>Ingresos por traslado</td>";
								echo "<td colspan=4>Egresos</td>";
								echo "<td rowspan=2>Egresos por traslado</td>";
								echo "<td colspan=2>Dias estancia</td>";
								echo "<td rowspan=2>Pacientes día ant.</td>";
								echo "<td rowspan=2>Pacientes a la fecha</td>";
								echo "<td rowspan=2>Ingresos y egresos dia</td>";
								echo "<td rowspan=2>Días Cama Ocupada</td>";
								echo "<td rowspan=2>Nro Camas</td>";
								echo "<td rowspan=2>Días Cama Disponible</td>";
								echo "<td rowspan=2>Prom. Camas Ocupadas</td>";
								echo "<td rowspan=2>Días de<br>Estancia</td>";
								echo "<td rowspan=2>% de Ocupacion</td>";
								echo "<td rowspan=2>Promedio Dias<br>Estancia</td>";
								echo "<td rowspan=2>Rendimiento<br>Hospitalario</td>";
								echo "<td rowspan=2>Indice de<br>Sustitución</td>";
								echo "<td rowspan=2>Tasa de <br>Mortalidad</td>";
								echo "<td rowspan=2>Tasa Mortalidad<br> > 48 Horas</td>";
								echo "<td rowspan=2>Tasa Mortalidad<br> < 48 Horas</td>";

								echo "</tr>";

								echo "<tr class='encabezadoTabla'>";

								echo "<td>Urgencias</font></td>";
								echo "<td>Admisiones</font></td>";
								echo "<td>Cirugia</font></td>";
								echo "<td>Otros</font></td>"; //2012-11-08
								echo "<td>Total</font></td>";
								echo "<td>Altas</font></td>";
								echo "<td>Muertes < 48 Horas</font></td>";
								echo "<td>Muertes > 48 Horas</font></td>";
								echo "<td>Total</font></td>";
								echo "<td>Altas y muertes</font></td>";
								echo "<td>Egresos por traslado</font></td>";

								echo "</tr>";
							}*/
							
							//Acumulo sobre totales += $subIndicadores->;
							$totIndicadores->ingU+= $subIndicadores->ingU;
							$totIndicadores->ingA+= $subIndicadores->ingA;
							$totIndicadores->ingC+= $subIndicadores->ingC;
							$totIndicadores->ingTotales += $subIndicadores->ingTotales; //2012-11-08
							$totIndicadores->ingTotalesSinTrasl+= $subIndicadores->ingTotalesSinTrasl;
							$totIndicadores->ingT+= $subIndicadores->ingT;
							$totIndicadores->ingYEgrDia+= $subIndicadores->ingYEgrDia;
							$totIndicadores->egrA+= $subIndicadores->egrA;
							$totIndicadores->egrMmay48+= $subIndicadores->egrMmay48;
							$totIndicadores->egrMmen48+= $subIndicadores->egrMmen48;
							$totIndicadores->egrTotalesSinTrasl+= $subIndicadores->egrTotalesSinTrasl;
							$totIndicadores->egrT+= $subIndicadores->egrT;
							$totIndicadores->diasEAltasM+= $subIndicadores->diasEAltasM;
							$totIndicadores->diasEInstitucionales+= $subIndicadores->diasEInstitucionales;
							$totIndicadores->diasEEgrT+= $subIndicadores->diasEEgrT;
							$totIndicadores->pacDiaAnterior+= $subIndicadores->pacDiaAnterior;
							//$totIndicadores->pacALaFecha+= $subIndicadores->pacALaFecha;
							$totIndicadores->pacALaFecha= $subIndicadores->pacALaFecha; //2013-04-05
							$totIndicadores->diasCamaDisponible+= $subIndicadores->diasCamaDisponible;
							$totIndicadores->diasCamaOcupada+= $subIndicadores->diasCamaOcupada;
							$totIndicadores->promCamasOcupadas+= $subIndicadores->promCamasOcupadas;
							$totIndicadores->diasEstanciaTotales+= $subIndicadores->diasEstanciaTotales;

							$diasFechasConsulta = 0;

							$subIndicadores = new indicador();
						} 
						
						//Acumulo subtotales
						$subIndicadores->ingU 			+= $indicador->ingU;
						$subIndicadores->ingA 			+= $indicador->ingA;
						$subIndicadores->ingC 			+= $indicador->ingC;
						$subIndicadores->ingTotales 	+= $indicador->ingTotales; //2012-11-08
						$subIndicadores->ingTotalesSinTrasl += $indicador->ingTotalesSinTrasl;
						$subIndicadores->ingT 			+= $indicador->ingT;
						$subIndicadores->ingYEgrDia		+= $indicador->ingYEgrDia;
						$subIndicadores->egrA			+= $indicador->egrA;
						$subIndicadores->egrMmen48		+= $indicador->egrMmen48;
						$subIndicadores->egrMmay48		+= $indicador->egrMmay48;
						$subIndicadores->egrTotalesSinTrasl	+= $indicador->egrTotalesSinTrasl;
						$subIndicadores->egrT			+= $indicador->egrT;
						$subIndicadores->diasEAltasM	+= $indicador->diasEAltasM;
						$subIndicadores->diasEInstitucionales			+= $indicador->diasEInstitucionales;
						$subIndicadores->diasEEgrT		+= $indicador->diasEEgrT;
						$subIndicadores->pacDiaAnterior	+= $indicador->pacDiaAnterior;
						//$subIndicadores->pacALaFecha	+= $indicador->pacALaFecha;
						$subIndicadores->pacALaFecha	= $indicador->pacALaFecha; //2013-04-05
						$subIndicadores->nroCamas		+= $indicador->nroCamas;
						$subIndicadores->diasCamaOcupada+= $indicador->diasCamaOcupada;
						$subIndicadores->promCamasOcupadas	+= $indicador->promCamasOcupadas;				
						$subIndicadores->diasEstanciaTotales+= $indicador->diasEstanciaTotales;

						//Calculados
						if($diasFechasConsulta>0)
							$nroCamas = round($subIndicadores->diasCamaDisponible / $diasFechasConsulta);
						else
							$nroCamas = round($subIndicadores->diasCamaDisponible / 1);

						$subIndicadores->porcOcupacion = @(($subIndicadores->diasCamaOcupada/$subIndicadores->diasCamaDisponible)*100);
						$subIndicadores->promDiasEstancia = @($subIndicadores->diasEAltasM/($subIndicadores->egrTotalesSinTrasl + $subIndicadores->egrT));
						
						$subIndicadores->rendimientoHospitalario = @(($subIndicadores->egrTotalesSinTrasl+$subIndicadores->egrT)/$nroCamas);						
						if($subIndicadores->porcOcupacion > 0)
						{
							$subIndicadores->indiceSustitucion = @((100-$subIndicadores->porcOcupacion)*$subIndicadores->promDiasEstancia)/$subIndicadores->porcOcupacion;
						}
						else
						{
							$subIndicadores->indiceSustitucion = 0;
						}
						$subIndicadores->tasaMortalidad = @(($subIndicadores->egrMmay48 + $subIndicadores->egrMmen48) / ($subIndicadores->egrA + $subIndicadores->egrMmay48 + $subIndicadores->egrMmen48 + $subIndicadores->egrT))*100;
						$subIndicadores->tasaMortalidadMayor48 = @(($subIndicadores->egrMmay48) / ($subIndicadores->egrA + $subIndicadores->egrMmay48 + $subIndicadores->egrMmen48 + $subIndicadores->egrT))*100;
						$subIndicadores->tasaMortalidadMenor48 = @(($subIndicadores->egrMmen48) / ($subIndicadores->egrA + $subIndicadores->egrMmay48 + $subIndicadores->egrMmen48 + $subIndicadores->egrT))*100;
						$subIndicadores->diasCamaDisponible	+= $indicador->diasCamaDisponible;
						$totIndicadores->diasCamaDisponible	+= $indicador->diasCamaDisponible;
						$totIndicadores->totalDiasCamaDisponible += $indicador->diasCamaDisponible;

						$ccoCod = explode( "-", $indicador->servicio);
						$styloMano = "style='cursor: pointer;'";
						echo "<tr class=$clase>";
						
						echo "<td style='text-align:left'>$indicador->servicio</td>";
						echo "<td>$indicador->fecha</td>";
						$styloManoAux = ($indicador->ingU != 0 )? $styloMano : "";
						echo "<td ".$styloManoAux." onclick='consultarDetalle(this, \"".$indicador->fecha."\",\"".trim($ccoCod[0])."\", \"".trim($ccoCod[1])."\",\"IU\")'>$indicador->ingU</td>";
						$styloManoAux = ($indicador->ingA != 0 )? $styloMano : "";
						echo "<td ".$styloManoAux." onclick='consultarDetalle(this, \"".$indicador->fecha."\",\"".trim($ccoCod[0])."\", \"".trim($ccoCod[1])."\",\"IA\")'>$indicador->ingA</td>";
						$styloManoAux = ($indicador->ingC != 0 )? $styloMano : "";
						echo "<td ".$styloManoAux." onclick='consultarDetalle(this, \"".$indicador->fecha."\",\"".trim($ccoCod[0])."\", \"".trim($ccoCod[1])."\",\"IC\")'>$indicador->ingC</td>";
						$otros = $indicador->ingTotales - ($indicador->ingU + $indicador->ingA + $indicador->ingC + $indicador->ingT); //2012-11-08
						$styloManoAux = ($otros != 0 )? $styloMano : "";
						echo "<td ".$styloManoAux." onclick='consultarDetalle(this, \"".$indicador->fecha."\",\"".trim($ccoCod[0])."\", \"".trim($ccoCod[1])."\",\"IH\")'>".$otros."</td>";//2012-11-08		
						$indicador->ingTotalesSinTrasl += $otros; // 2012-11-08 Se le suma la cantidad de otros						
						echo "<td>$indicador->ingTotalesSinTrasl</td>";
						$styloManoAux = ($indicador->ingT != 0 )? $styloMano : "";
						echo "<td ".$styloManoAux." onclick='consultarDetalle(this, \"".$indicador->fecha."\",\"".trim($ccoCod[0])."\", \"".trim($ccoCod[1])."\",\"IT\")'>$indicador->ingT</td>";						
						$styloManoAux = ($indicador->egrA != 0 )? $styloMano : "";
						echo "<td ".$styloManoAux." onclick='consultarDetalle(this, \"".$indicador->fecha."\",\"".trim($ccoCod[0])."\", \"".trim($ccoCod[1])."\",\"EA\")'>$indicador->egrA</td>";						
						$styloManoAux = ($indicador->egrMmen48 != 0 )? $styloMano : "";
						echo "<td ".$styloManoAux." onclick='consultarDetalle(this, \"".$indicador->fecha."\",\"".trim($ccoCod[0])."\", \"".trim($ccoCod[1])."\",\"EME\")'>$indicador->egrMmen48</td>";
						$styloManoAux = ($indicador->egrMmay48 != 0 )? $styloMano : "";
						echo "<td ".$styloManoAux." onclick='consultarDetalle(this, \"".$indicador->fecha."\",\"".trim($ccoCod[0])."\", \"".trim($ccoCod[1])."\",\"EMA\")'>$indicador->egrMmay48</td>";
						echo "<td>$indicador->egrTotalesSinTrasl</td>";
						$styloManoAux = ($indicador->egrT != 0 )? $styloMano : "";
						echo "<td ".$styloManoAux." onclick='consultarDetalle(this, \"".$indicador->fecha."\",\"".trim($ccoCod[0])."\", \"".trim($ccoCod[1])."\",\"ET\")'>$indicador->egrT</td>";
						echo "<td>$indicador->diasEAltasM</td>";
						echo "<td>$indicador->diasEEgrT</td>";
						echo "<td>$indicador->pacDiaAnterior</td>";
						echo "<td>$indicador->pacALaFecha</td>";
						echo "<td>$indicador->ingYEgrDia</td>";
						
						
						echo "<td>$indicador->diasCamaOcupada</td>";//xxx modificar este
						echo "<td>$indicador->nroCamas</td>";
						echo "<td>$indicador->diasCamaDisponible</td>";				
						echo "<td>$indicador->promCamasOcupadas</td>";      //xxx modificar este
						//echo "<td>fdsfdsfds $indicador->diasEstanciaTotales</td>";
						echo "<td>".number_format($indicador->porcOcupacion,2,'.',',')."</td>";
						echo "<td>".number_format($indicador->promDiasEstancia,2,'.',',')."</td>";
						echo "<td>".number_format($indicador->rendimientoHospitalario,2,'.',',')."</td>";
						echo "<td>".number_format($indicador->indiceSustitucion,2,'.',',')."</td>";
						echo "<td>".number_format($indicador->tasaMortalidad,2,'.',',')."</td>";
						echo "<td>".number_format($indicador->tasaMortalidadMayor48,2,'.',',')."</td>";
						echo "<td>".number_format($indicador->tasaMortalidadMenor48,2,'.',',')."</td>";
						
						echo "</tr>";
						
						$fechaTemp = $indicador->servicio;
						
						$cont1++;
						if( $indicador->nroCamas != 0 ){ //2013-04-05
							$diasFechasConsulta++;
						}
					}
					
					
					
					//Subtutotales de la ultima fila
					echo "<tr class=encabezadoTabla>";

					echo "<td colspan=2>Subtotal</td>";
				    echo "<td>$subIndicadores->ingU</td>";
					echo "<td>$subIndicadores->ingA</td>";
					echo "<td>$subIndicadores->ingC</td>";
					$otros = $subIndicadores->ingTotales - ($subIndicadores->ingU + $subIndicadores->ingA + $subIndicadores->ingC + $subIndicadores->ingT); //2012-11-08
					echo "<td>".$otros."</td>";//2012-11-08	
					$subIndicadores->ingTotalesSinTrasl += $otros; // 2012-11-08 Se le suma la cantidad de otros					
					echo "<td>$subIndicadores->ingTotalesSinTrasl</td>";
					echo "<td>$subIndicadores->ingT</td>";					
					echo "<td>$subIndicadores->egrA</td>";					
					echo "<td>$subIndicadores->egrMmen48</td>";
					echo "<td>$subIndicadores->egrMmay48</td>";
					echo "<td>$subIndicadores->egrTotalesSinTrasl</td>";
					echo "<td>$subIndicadores->egrT</td>";
					echo "<td>$subIndicadores->diasEAltasM</td>";
					echo "<td>$subIndicadores->diasEEgrT</td>";
					//echo "<td>$subIndicadores->pacDiaAnterior</td>";
					echo "<td>&nbsp;</td>"; //Pacientes dia anterior
					echo "<td>$subIndicadores->pacALaFecha</td>";  //Pacientes a la fecha
					echo "<td>$subIndicadores->ingYEgrDia</td>";
					echo "<td onclick='consultarDetalleDiaCamaOcupada(this, \"$indicador->servicio\", \"$wfechaInicial\", \"$wfechaFinal\");' style='cursor:pointer;'>$subIndicadores->diasCamaOcupada</td>";
					
					//Calculados
					//Para rendimiento hospitalario del subtotal
					$diasTotalesRendimiento = ($subIndicadores->diasCamaDisponible / $diasFechasConsulta);
					@$acumuladoDiasTotalesCamaDisponible += $subIndicadores->diasCamaDisponible;
					$nroCamas = round($subIndicadores->diasCamaDisponible / $diasFechasConsulta);
					
					$subIndicadores->porcOcupacion = @(($subIndicadores->diasCamaOcupada/$subIndicadores->diasCamaDisponible)*100);
					$subIndicadores->promDiasEstancia = @( ( $subIndicadores->diasEAltasM + $subIndicadores->diasEEgrT )/($subIndicadores->egrTotalesSinTrasl + $subIndicadores->egrT));
					if(intval($subIndicadores->porcOcupacion) > 0)
					{
						$subIndicadores->indiceSustitucion = @((100-$subIndicadores->porcOcupacion)*$subIndicadores->promDiasEstancia)/$subIndicadores->porcOcupacion;
					} 
					else 
					{
						$subIndicadores->indiceSustitucion = 0;
					}
					$subIndicadores->tasaMortalidad = @(($subIndicadores->egrMmay48 + $subIndicadores->egrMmen48) / ($subIndicadores->egrA + $subIndicadores->egrMmay48 + $subIndicadores->egrMmen48 + $subIndicadores->egrT))*100;
					$subIndicadores->tasaMortalidadMayor48 = @(($subIndicadores->egrMmay48) / ($subIndicadores->egrA + $subIndicadores->egrMmay48 + $subIndicadores->egrMmen48 + $subIndicadores->egrT))*100; 
					$subIndicadores->tasaMortalidadMenor48 = @(($subIndicadores->egrMmen48) / ($subIndicadores->egrA + $subIndicadores->egrMmay48 + $subIndicadores->egrMmen48 + $subIndicadores->egrT))*100;
					
					$nroCamas = round($subIndicadores->diasCamaDisponible / $diasFechasConsulta);
					
					//echo "F'$subIndicadores->egrTotalesSinTrasl' '$subIndicadores->egrT' '$nroCamas'";					 
					$subIndicadores->rendimientoHospitalario = @(($subIndicadores->egrTotalesSinTrasl+$subIndicadores->egrT)/$nroCamas);
					
					$totIndicadores->nroCamas += $nroCamas;
					
					echo "<td>".$nroCamas."</td>";
					echo "<td>".round($subIndicadores->diasCamaDisponible)."</td>";
					echo "<td>".round($subIndicadores->promCamasOcupadas/$diasFechasConsulta)."</td>";
					//echo "<td>$subIndicadores->diasEstanciaTotales</td>";
					echo "<td>".number_format($subIndicadores->porcOcupacion,2,'.',',')."</td>";
					echo "<td>".number_format($subIndicadores->promDiasEstancia,2,'.',',')."</td>";
					echo "<td>".number_format($subIndicadores->rendimientoHospitalario,2,'.',',')."</td>";
					echo "<td>".number_format($subIndicadores->indiceSustitucion,2,'.',',')."</td>";
					echo "<td>".number_format($subIndicadores->tasaMortalidad,2,'.',',')."</td>";
					echo "<td>".number_format($subIndicadores->tasaMortalidadMayor48,2,'.',',')."</td>";
					echo "<td>".number_format($subIndicadores->tasaMortalidadMenor48,2,'.',',')."</td>";

					/*if($wservicio == "%")  //2012-11-14
					{
						echo "<tr class='encabezadoTabla'>";

						echo "<td rowspan=2>Servicio</font></td>";
						echo "<td rowspan=2>Fecha</td>";
						echo "<td colspan=5>Ingresos</td>"; //2012-11-08 colspan=4  por colspan=5
						echo "<td rowspan=2>Ingresos por traslado</td>";
						echo "<td colspan=4>Egresos</td>";
						echo "<td rowspan=2>Egresos por traslado</td>";
						echo "<td colspan=2>Dias estancia</td>";
						echo "<td rowspan=2>Pacientes día ant.</td>";
						echo "<td rowspan=2>Pacientes a la fecha</td>";
						echo "<td rowspan=2>Ingresos y egresos dia</td>";
						echo "<td rowspan=2>Días Cama Ocupada</td>";
						echo "<td rowspan=2>Nro Camas</td>";
						echo "<td rowspan=2>Días Cama Disponible</td>";
						echo "<td rowspan=2>Prom. Camas Ocupadas</td>";
						echo "<td rowspan=2>Días de<br>Estancia</td>";
						echo "<td rowspan=2>% de Ocupacion</td>";
						echo "<td rowspan=2>Promedio Dias<br>Estancia</td>";
						echo "<td rowspan=2>Rendimiento<br>Hospitalario</td>";
						echo "<td rowspan=2>Indice de<br>Sustitución</td>";
						echo "<td rowspan=2>Tasa de <br>Mortalidad</td>";
						echo "<td rowspan=2>Tasa Mortalidad<br> > 48 Horas</td>";
						echo "<td rowspan=2>Tasa Mortalidad<br> < 48 Horas</td>";

						echo "</tr>";

						echo "<tr class='encabezadoTabla'>";

						echo "<td>Urgencias</font></td>";
						echo "<td>Admisiones</font></td>";
						echo "<td>Cirugia</font></td>";
						echo "<td>Otros</font></td>"; //2012-11-08
						echo "<td>Total</font></td>";
						echo "<td>Altas</font></td>";
						echo "<td>Muertes < 48 Horas</font></td>";
						echo "<td>Muertes > 48 Horas</font></td>";
						echo "<td>Total</font></td>";
						echo "<td>Altas y muertes</font></td>";
						echo "<td>Egresos por traslado</font></td>";

						echo "</tr>";
					}*/
						
					//Totales acumulados
					//Acumulo sobre totales += $subIndicadores->;
					$totIndicadores->ingU+= $subIndicadores->ingU;
					$totIndicadores->ingA+= $subIndicadores->ingA;
					$totIndicadores->ingC+= $subIndicadores->ingC;
					$totIndicadores->ingTotales += $subIndicadores->ingTotales; //2012-11-08
					$totIndicadores->ingTotalesSinTrasl+= $subIndicadores->ingTotalesSinTrasl;
					$totIndicadores->ingT+= $subIndicadores->ingT;
					$totIndicadores->ingYEgrDia+= $subIndicadores->ingYEgrDia;
					$totIndicadores->egrA+= $subIndicadores->egrA;					
					$totIndicadores->egrMmen48+= $subIndicadores->egrMmen48;
					$totIndicadores->egrMmay48+= $subIndicadores->egrMmay48;
					$totIndicadores->egrTotalesSinTrasl+= $subIndicadores->egrTotalesSinTrasl;
					$totIndicadores->egrT+= $subIndicadores->egrT;
					$totIndicadores->diasEAltasM+= $subIndicadores->diasEAltasM;
					$totIndicadores->diasEInstitucionales+= $subIndicadores->diasEInstitucionales;
					$totIndicadores->diasEEgrT+= $subIndicadores->diasEEgrT;
					$totIndicadores->pacDiaAnterior+= $subIndicadores->pacDiaAnterior;
					//$totIndicadores->pacALaFecha+= $subIndicadores->pacALaFecha;					
					$totIndicadores->pacALaFecha = $subIndicadores->pacALaFecha; //2013-04-05					
					$totIndicadores->diasCamaOcupada+= $subIndicadores->diasCamaOcupada;
					$totIndicadores->promCamasOcupadas+= $subIndicadores->promCamasOcupadas;
					$totIndicadores->diasEstanciaTotales+= $subIndicadores->diasEstanciaTotales;
					$totIndicadores->porcOcupacion+= $subIndicadores->porcOcupacion;
					$totIndicadores->promDiasEstancia+= $subIndicadores->promDiasEstancia;					
					$totIndicadores->tasaMortalidad+= $subIndicadores->tasaMortalidad;
					$totIndicadores->tasaMortalidadMayor48+= $subIndicadores->tasaMortalidadMayor48;
					$totIndicadores->tasaMortalidadMenor48+= $subIndicadores->tasaMortalidadMenor48;

					//Subtutotales de la ultima fila
					if($wservicio == "%")
					{
					
						//Se agrega de nuevo esta parte del script, ya que al recorrer el ciclo anterior la variable $diasFechasConsulta se queda con el resultado del ultimo ciclo, y no siempre
						//el ultimo ciclo termina en la cantidad de dias del mes. 03 Abril de 2012
						$calcDiaInicial = mktime(0,0,0,$vecFechaInicial[1],$vecFechaInicial[2],$vecFechaInicial[0]);
						$calcDiaFinal = mktime(0,0,0,$vecFechaFinal[1],$vecFechaFinal[2],$vecFechaFinal[0]);				
						$diasFechasConsulta = ROUND(($calcDiaFinal-$calcDiaInicial)/(60*60*24)) + 1;
					
						$totIndicadores->diasEInstitucionales = consultarDiasEstanciaInstitucionales( $conex, $wbasedato, $wcliame, $wfechaInicial, $wfechaFinal );
					
						echo "<tr class=encabezadoTabla>";
						echo "<td colspan=2>TOTALES</td>";
						echo "<td>$totIndicadores->ingU</td>";
						echo "<td>$totIndicadores->ingA</td>";
						echo "<td>$totIndicadores->ingC</td>";
						$otros = $totIndicadores->ingTotales - ($totIndicadores->ingU + $totIndicadores->ingA + $totIndicadores->ingC + $totIndicadores->ingT); //2012-11-08
						echo "<td>".$otros."</td>";//2012-11-08	
						echo "<td>$totIndicadores->ingTotalesSinTrasl</td>";
						echo "<td>$totIndicadores->ingT</td>";
						echo "<td>$totIndicadores->egrA</td>";
						echo "<td>$totIndicadores->egrMmen48</td>";
						echo "<td>$totIndicadores->egrMmay48</td>";
						echo "<td>$totIndicadores->egrTotalesSinTrasl</td>";
						echo "<td>$totIndicadores->egrT</td>";
						echo "<td>$totIndicadores->diasEInstitucionales</td>";
						echo "<td>$totIndicadores->diasEEgrT</td>";
						echo "<td colspan=2>&nbsp;</td>";
						echo "<td>$totIndicadores->ingYEgrDia</td>";
						echo "<td>$totIndicadores->diasCamaOcupada</td>";
						echo "<td>$totIndicadores->nroCamas</td>";
						$acumuladoDiasTotalesCamaDisponible = $acumuladoDiasTotalesCamaDisponible / $diasFechasConsulta;
						echo "<td>$totIndicadores->totalDiasCamaDisponible</td>";
						echo "<td>".round($totIndicadores->promCamasOcupadas/$diasFechasConsulta)."</td>"; //Promedio de camas ocupadas
						//echo "<td>$totIndicadores->diasEstanciaTotales</td>";
						
						//Calculados
						$totIndicadores->porcOcupacion = @(($totIndicadores->diasCamaOcupada/$totIndicadores->totalDiasCamaDisponible)*100);
						$totIndicadores->promDiasEstancia = @($totIndicadores->diasEInstitucionales/($totIndicadores->egrTotalesSinTrasl));
						$totIndicadores->rendimientoHospitalario = @(($totIndicadores->egrTotalesSinTrasl)/($totIndicadores->nroCamas));
							
						$totIndicadores->tasaMortalidad = @(($totIndicadores->egrMmay48 + $totIndicadores->egrMmen48) / ($totIndicadores->egrTotalesSinTrasl))*100;
						$totIndicadores->tasaMortalidadMayor48 = @(($totIndicadores->egrMmay48) / ($totIndicadores->egrA + $totIndicadores->egrMmay48 + $totIndicadores->egrMmen48 + $totIndicadores->egrT))*100;
						$totIndicadores->tasaMortalidadMenor48 = @(($totIndicadores->egrMmen48) / ($totIndicadores->egrA + $totIndicadores->egrMmay48 + $totIndicadores->egrMmen48 + $totIndicadores->egrT))*100;
							
						if(intval($totIndicadores->porcOcupacion) > 0)
						{
							$totIndicadores->indiceSustitucion = ((100-$totIndicadores->porcOcupacion)*$totIndicadores->promDiasEstancia)/$totIndicadores->porcOcupacion;
						} 
						else 
						{
							$totIndicadores->indiceSustitucion = 0;
						}
						
						echo "<td>".number_format($totIndicadores->porcOcupacion,2,'.',',')."</td>";
						echo "<td>".number_format($totIndicadores->promDiasEstancia,2,'.',',')."</td>";
						echo "<td>".number_format($totIndicadores->rendimientoHospitalario,2,'.',',')."</td>";
						echo "<td>".number_format($totIndicadores->indiceSustitucion,2,'.',',')."</td>";
						echo "<td>".number_format($totIndicadores->tasaMortalidad,2,'.',',')."</td>";
						echo "<td>".number_format($totIndicadores->tasaMortalidadMayor48,2,'.',',')."</td>";
						echo "<td>".number_format($totIndicadores->tasaMortalidadMenor48,2,'.',',')."</td>";
					}
					
					echo "</tr>";
				}
				else
				{
				    $wfecha=date("Y-m-d");
					$wfechaInicial=$wfecha;
					$wfechaFinal=$wfecha;
					mensajeEmergente("No se encontraron indicadores para el servicio, fecha inicial y final dados.");
					funcionJavascript("inicio(\"$wfechaInicial\",\"$wfechaFinal\",\"$wservicio\");");	
				}				
			} 
			else 
			{ //si no llegan los parametros del reporte
				mensajeEmergente("Verifique los parámetros de consulta: Servicio, fecha inicial y fecha final.");				
			}
			echo "</table>";
			echo "<center><br><input type='button' value='Regresar' onClick='javascript:inicio(\"$wfechaInicial\",\"$wfechaFinal\",\"$wservicio\");'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></center>";
			
			break; //Fin consulta reporte

		default:  //Filtro
			echo "<table align='center' border=0>";
				
			$wfecha=date("Y-m-d");
			//Parámetros de consulta del reporte
			if (!isset ($bandera))
			{  			
				$wfec_i=$wfecha;
				$wfec_f=$wfecha;
				$wservicio="";
			}	
			//Petición de ingreso de parametros
			echo "<tr>";
			echo "<td height='37' colspan='2'>";
			echo '<p align="left" class="titulo"><strong> &nbsp; Seleccione los par&aacute;metros de consulta &nbsp;  &nbsp; </strong></p>';
			echo "</td></tr>";
			echo "</table>";

						
			//Centros de costos hospitalarios
			
			//**************** llamada a la funcion consultaCentrosCostos y dibujarSelect************
			$cco="Ccohos";
			$sub="off";
			$tod="Todos";
			$ipod="off";
			//$cco=" ";
			$centrosCostos = consultaCentrosCostos($cco);
					
			echo "<table align='center' border=0>";		
			$dib=dibujarSelect($centrosCostos, $sub, $tod, $ipod, "wservicio");
					
			echo $dib;
			echo "</table>";
		
			
			//Fecha inicial de consulta
			echo "<table align='center' border=0 width=403>";
			echo "<tr>";
			echo "<td class='fila1' width='86'> Fecha inicial </td>";
			echo "<td class='fila2'>";			
			campoFechaDefecto("wfec_i", $wfec_i);
			echo "</td>";
			echo "</tr>";
			
			//Fecha final de consulta
			echo "<tr>";
			echo "<td class='fila1' width='86'> Fecha final </td>";
			echo "<td class='fila2'>";
			campoFechaDefecto("wfec_f", $wfec_f);
			echo "</td>";
			echo "</tr>";

			//$wservicio1= explode("-",$wservicio);
		    //$wservicio=$wservicio1[0];
			
			echo "<tr><td align=center colspan=2><br><input type='button' value='Consultar' onClick='javascript:consultar();'> | <input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></b></td></tr></center>";
			
			echo "</table>";
			
			break;
	} //Fin switch
}
liberarConexionBD($conex);
?>