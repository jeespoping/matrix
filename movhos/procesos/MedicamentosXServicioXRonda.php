<head>
  <title>MEDICAMENTOS POR SERVICIO Y RONDA</title>

  <style type="text/css">

    	A	{text-decoration: none;color: #000066;}
    	.tipo3V{color:#000066;background:#dddddd;font-size:15pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px}
        .tipo3V:hover {color: #000066; background: #999999;}
        .tipo4V{color:#000066;background:#dddddd;font-size:15pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px}
        .tipo4V:hover {color: #000066; background: #999999;}

		
		BODY            
		{
			font-family: verdana;
			font-size: 10pt;
			height: 1024px;
			width: 1280px;
		}
		.encabezadoTabla                                 
		{
			 background-color: #2A5DB0;
			 color: #FFFFFF;
			 font-size: 10pt;
			 font-weight: bold;
		}
		.fila1                                
		{
			 background-color: #C3D9FF;
			 color: #000000;
			 font-size: 10pt;
		}
		.fila2                                
		{
			 background-color: #E8EEF7;
			 color: #000000;
			 font-size: 10pt;
		}
		
		.tituloPagina                     
		{
			 font-family: verdana;
			 font-size: 18pt;
			 overflow: hidden;
			 text-transform: uppercase;
			 font-weight: bold;
			 height: 30px;
			 border-top-color: #2A5DB0;
			 border-top-width: 1px;
			 border-left-color: #2A5DB0;
			 border-left-width: 1px;
			 border-right-color: #2A5DB0;
			 border-bottom-color: #2A5DB0;
			 border-bottom-width: 1px;
			 margin: 2pt;
		}
		.deshabilitado
		{
			color: #3333!important;
		}
    </style>

</head>

<script type="text/javascript" src="../../../include/root/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.core.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.accordion.js"></script>	<!-- Acordeon -->
<script type="text/javascript" src="../../../include/root/ui.tabs.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.draggable.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>

<script type='text/javascript' src='../../../include/root/jquery.ajaxQueue.js'></script>	<!-- Autocomplete -->
<script type='text/javascript' src='../../../include/root/jquery.bgiframe.min.js'></script>	<!-- Autocomplete -->
<script type='text/javascript' src='../../../include/root/jquery.autocomplete.js'></script>	<!-- Autocomplete -->
<script type="text/javascript" src="../../../include/root/jquery.dimensions.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.simple.tree.js"></script>

<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
	

<script type="text/javascript">

$(document).ready(function() 
{
	// -------------------------------------
	//	Tooltip
	// -------------------------------------
		var cadenaTooltipDetalle = $("#tooltipDetalle").val();
		
		cadenaTooltipDetalle = cadenaTooltipDetalle.split("|");
		
		for(var i = 0; i < cadenaTooltipDetalle.length-1;i++)
		{
			$( "#"+cadenaTooltipDetalle[i] ).tooltip();
		}
	// -------------------------------------
		
});

function descargarArchivoContingencia(nameFile)
{
	// grab the content of the form field and place it into a variable
	var textToWrite = $( document.documentElement ).clone();
	$("meta",textToWrite).remove();
	textToWrite = $( textToWrite ).html();
	
	if( textToWrite != '' ){
		
		//  create a new Blob (html5 magic) that conatins the data from your form feild
		var textFileAsBlob = new Blob([textToWrite], {type:'text/plain'});
		// Specify the name of the file to be saved
		var fileNameToSaveAs = nameFile+".html";
		 
		// create a link for our script to 'click'
		// var downloadLink = document.createElement("a");
		var downloadLink = $( "#aDownload" )[0];
		//  supply the name of the file (from the var above).
		// you could create the name here but using a var
		// allows more flexability later.
		downloadLink.download = fileNameToSaveAs;
		
		// allow our code to work in webkit & Gecko based browsers
		// without the need for a if / else block.
		window.URL = window.URL || window.webkitURL;
			  
		// Create the link Object.
		downloadLink.href = window.URL.createObjectURL(textFileAsBlob);
		
		// click the new link
		downloadLink.click();
		
	}
}

function intercalar(idElemento, ronda )
{
	var rowspan = $( "#tdRonda"+ronda ).attr( "rowspan" );
	
	if( document.getElementById(idElemento).style.display=='')
	{
		document.getElementById(idElemento).style.display='none';
		$( "#tdRonda"+ronda ).attr( "rowspan", --rowspan );
	}
	else
	{
		 document.getElementById(idElemento).style.display='';
		 $( "#tdRonda"+ronda ).attr( "rowspan", ++rowspan );
	}
}

function parpadear() {
  var blink = document.all.tags("BLINK")
  for (var i=0; i < blink.length; i++)
    blink[i].style.visibility = blink[i].style.visibility == "" ? "hidden" : ""
}

function empezar() {
    
	setInterval("parpadear()",500)

	$('#wccoo').change(function() {
        if ($( this ).val().substr( 0,4 ) == '1051') {
            $('#selectTipos').show();
            $('#encabezadoTipos').show();
        }
        else {
            $('#encabezadoTipos').hide();
            $('#selectTipos').hide();
        }
    });
	
	$( "#wccoo" ).change(function(){
		if( $( this ).val().substr( 0,4 ) == '1050' ){
			//Este es la opcion todos
			//Si se seleccionar 1050 esta opción no debe aparecer
			$( "#wcco option" ).eq(1)
				.css({display: "none" })
				.attr({disabled: true });

			
		}
		else{
			$( "#wcco option" ).eq(1)
				.css({display: "" })
				.attr({disabled: false });
			
		}
		
		
		var ccoOrigen = $("#wccoo").val();
		ccoOrigen = ccoOrigen.split("-");
		
		var ccoCliclos24 = $("#ccoCiclos24").val();
			
		if(ccoCliclos24!="")
		{
			ccosCliclos24 = ccoCliclos24.split(",");
			
			if( ccoOrigen[0] == '3053' ){
				
				$( "#wcco option" )
					.css({display: "none" })
					.attr({disabled: true });
				
				$( "#wcco option[dom=on]" )
					.css({display: "" })
					.attr({disabled: false });
				
			}
			else{
				$( "#wcco option" )
					.css({display: "" })
					.attr({disabled: false });
				
				$("#wcco option").each(function(){
					
					var opcionCco = $(this).val();
					opcionCco = opcionCco.split("-");
					
					if(jQuery.inArray(opcionCco[0],ccosCliclos24) != -1)
					{
						if( ccoOrigen[0] == '1050' )
						{
							$(this).css({display: "none" });
							$(this).attr({disabled: true });
						}
						else
						{
							$(this).css({display: "" });
							$(this).attr({disabled: false });
						}
					}
				});
			}
			
		}
		
	});
}
window.onload = empezar;

function Exportar()
{
	 //Creamos un Elemento Temporal en forma de enlace
	 var tmpElemento = document.createElement('a');
	 var data_type = 'data:application/vnd.ms-excel'; //Formato anterior xls

	 // Obtenemos la información de la tabla
	 var tabla_div = document.getElementById('tablaExcel');
	 var tabla_html = tabla_div.outerHTML.replace(/ /g, '%20');
	 
	 tmpElemento.href = data_type + ', ' + tabla_html;
	 //Asignamos el nombre a nuestro EXCEL
	 tmpElemento.download = 'Monitor_medicamentos_x_ronda.xls';
	 // Simulamos el click al elemento creado para descargarlo
	 tmpElemento.click();

}

function enter()
	{
	 document.forms.separacionXRonda.submit();
	}

function cerrarVentana()
	 {
      window.close()
     }

</script>

<body>

<?php
include_once("conex.php");
/****************************************************************************************************************************************************************
 * Actualizaciones:
 * Octubre 1 de 2019 Edwin Molina 	- No se muestran los articulos de stock, ni los grupos E00 de medicamentos ni las dosis unicas aplicadas
 * Octubre 1 de 2019 Jessica Madrid - Se modifican los estilos de los filtros de búsqueda.
 * Julio 07 de 2018 Juan Felipe Balcero - Se agrega filtro por tipo de producto para central de mezclas
 *										- Se agrega función  de exportar a excel
 * Mayo 21 de 2018 Jessica Madrid 	-  En la función consultarSiDAexiste() se agrega a la consulta el filtro con cenpro_000002 para saber si la dosis adaptada 
									   esta activa de esta forma, si la inactivaron permite crear otra, es decir, se muestra Crear producto ya que antes de este 
									   cambio si creaban una dosis adaptada y la desactivaban solo se mostraba ir al perfil.
 * Abril 3 de 2018			Edwin	- Si la unidad del medicamento prescrito por el medico es diferente a la unidad del insumo en central de mezclas
									  se modifica el calculo para que adicional al valor de conversión se tenga en cuenta la concentración del articulo.
							Jessica	- Para las dosis adaptadas se redondea la dosis prescrita por el medico a dos decimales, de esta forma si hubo 
									  conversiones el resultado del calculo sea más exacto y no afecte el inventario.
 * Noviembre 01 de 2017 Edwin MG	En la función consultarArticulosPorPaciente se quita la variable global $wfecha y se reemplaza por la actual. Esto es para que la validación
 *									de la consulta por articulo de CM se haga para el día actual.
 * Octubre 23 de 2017 Edwin MG 		Se hacen cambios varios para mostrar el reporte:
 *									- Se crea funciones nuevas en movhos.inc.php(informacionPacienteM18 y query_todos_articulos_cco) para no usar repetitivamente 
 *									  las consultas de las funciones detalleArticulo y query_articulos_cco
 *									- Se usa la clase clRegleta el cual está en el include classRegleta para saber si un articulo para un paciente pertenece a una ronda
 *									- Se elimina la función detalleArticulo ya que hacía algo similar a query_articulos_cco pero por articulo
 * Septiembre 28 de 2017 Jessica Madrid - Se pone en negrita la ronda de cada articulo en la lista PACIENTES CON MEDICAMENTOS EN ESTA RONDA
 * Septiembre 26 de 2017 Edwin MG   Se hacen cambios en el query de la función detalleArticulo para que consulte el kardex del día anterior en los pacientes que no se
 *									le ha generado ordenes durante el curso del día
 * Septiembre 26 de 2017 Jessica Madrid - Se agrega la ronda de cada articulo a la lista PACIENTES CON MEDICAMENTOS EN ESTA RONDA
 *										- Se corrige para que al pintar los medicamentos quede agrupado en la ronda menor ya que en algunos casos quedaba con la ronda mayor.
 * Septiembre 14 de 2017 Jessica Madrid - Se agrega validación: cuando el centro de costos de origen es 1050 no debe mostrar los centros de costo con ciclos de 
 *										producción de 24 horas (Ccoc24 en movhos_000011)
 *										- Muestra con fondo amarillo los pacientes que están en proceso de alta en el detalle por medicamento
 * Septiembre 12 de 2017 Jessica Madrid Se corrige para que no tenga en cuenta las ordenes del día anterior que aun no se ha generado el Kardex del día actual, 
									enviando un parametro adicional a la funcion query_articulos_cco.
 *									Si el centro de costos de origen es 1051 muestra para cada articulo crear producto, crear lote o ir al perfil segun el caso.
 *									Se corrigen las dosis duplicadas cuando los medicamentos tienen una condicion a necesidad
 * Febrero 7 de 2017: 	Edwin MG.	Para central de mezclas se permite buscar por todos los centros de costos y se filtra para que no aparezcan los articulos
 *									genéricos LQ e IC.
 * Febrero 2 de 2015: 	Edwin MG.	Se corrige sbuquerie de saldos cuando el paciente tiene un medicamentos con condición a necesidad
 * 14 Noviembre de 2014: Se modifica el script para que excluya los pacientes que provienen de urgencias y que aún estan en proceso de traslado, buscar la variable $tabla_pacientes
 * Octubre 9 de 2012	Edwin MG.	El programa muestra las rodas correspondientes si el cco seleccionado tiene tiempo de dispensacion propio.
 * Agosto 1 de 2012		Edwin MG.	Se muestra la ubicación del medicamento en el reporte.
 * Julio 24 de 2012 	Edwin MG.	Se ordena los articulos por ubicacion, para esto se modifica la funcion query_articulos_cco en movhos.inc.php y se agrega
 *									mensaje 7 en las condiciones que muestra el mensaje.
 * Julio 10 de 2012 Viviana Rodas.  Se agrega el llamado a las funciones consultaCentrosCostos que hace la consulta de los centros de costos
 * 									de un grupo seleccionado y dibujarSelect que dibuja el select con los centros de costos obtenidos de la primera funcion.
 * Abril 13 de 2012.Jerson Trujillo Se modifica el reporte de "Pacientes Con Medicamentos En Esta Ronda" para que liste los medicamentos pendientes, asociados a
									la habitacion correspondiente.
 * Marzo 7 de 2012.		Edwin MG.	Se cambia el programa para que muestre solo los cco que tengan activo el ciclo de producción
 *									Cambio las rondas a mostrar para que sean dinámicas
 * Febrero 15 de 2012.	Edwin MG.	Se cambia query de detalle_articulos para que tenga en cuentas el total de rondas posibles a dispensar segun
 *									la configuración de la tabla de tipos de articulos (movhos_000099)
 *									Se quita funcion query_pacientes_cco usada para mostrar los pacientes que tienen articulos para la ronda seleccionada,
 *									en su lugar se crea un array para manejarlo.
 ****************************************************************************************************************************************************************/


  /*********************************************************
   *     REPORTE PARA DISPENSACION POR SERVICIO Y RONDA    *
   *     			 CONEX, FREE => OK				       *
   *********************************************************/
session_start();

if (!isset($user))
	if(!isset($_SESSION['user']))
	  session_register("user");


if(!isset($_SESSION['user']))
	echo "error";
else
{

  
  
  include_once("root/comun.php");
  include_once("movhos/movhos.inc.php");
  include_once("movhos/classRegleta.php");
  
  $wcenmez = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cenmez');


  $pos = strpos($user,"-");
  $wusuario = substr($user,$pos+1,strlen($user));


															// =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
  $wactualiz  ="Octubre 1 de 2019";             		// Aca se coloca la ultima fecha de actualizacion de este programa //
															// =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //

  //*******************************************************************************************************************************************
  //F U N C I O N E S
  //===========================================================================================================================================
  
	function esCcoDomiciliarioMSR( $conex, $wbasedato, $wcco ){
		
		$val = false;

		$sql = "SELECT Ccodom
				  FROM ".$wbasedato."_000011
				 WHERE ccocod = '".$wcco."'
				   AND ccodom = 'on'
				 ;";

		$res = mysql_query($sql, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
		$num = mysql_num_rows($res);
		
		if($num > 0)
		{
			$val = true;
		}
		
		return $val;
	}
  
	/**********************************************************************************************************************************
	 * Indica si un articulo peretenece al stock
	 * 
	 * @param $conex
	 * @param $wbasedato
	 * @param $articulo
	 * @param $cco
	 * @return unknown_type
	 **********************************************************************************************************************************/
	function esStock( $conex, $wbasedato, $articulo, $cco ){
		
		$val = false;
		
		$sql = "SELECT
					Arscod
				FROM
					{$wbasedato}_000091
				WHERE
					arscco = '$cco'
					AND arscod = '$articulo'
					AND arsest = 'on'
				"; //echo $sql;
					
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".msyql_error() );
		$numrows = mysql_num_rows( $res );
		
		if( $numrows > 0 ){
			$val = true;
		}
		
		return $val;
	}
  
  
	 //funcion
	function consultarConcentracionArticuloSF( $conex, $wmovhos, $artcod )
	{

		$val = 1;

		//Consulto el codigo correspondiente en CM
		$sql = "SELECT Relcon
				  FROM ".$wmovhos."_000026 a, ".$wmovhos."_000115 b, ".$wmovhos."_000059 c
				 WHERE a.artcod = '".$artcod."'
				   AND a.artcod = b.relart
				   AND c.defart = a.artcod
				   AND a.artuni != c.deffru
				   AND c.defcco = '1050'
				";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query - $sql - ".mysql_error() );
		
		if( $rows = mysql_fetch_array($res) ){
			$val = $rows[ 'Relcon' ];
		}
		
		return $val;
	}
	
	function consultarCcoCiclos24()
	{
		global $conex;
		global $wbasedato;
		
		//Consulta si el centro de costo funciona con ciclos de 24 horas
		$queryCiclos = "SELECT Ccocod 
						 FROM ".$wbasedato."_000011 
						WHERE Ccoc24='on' 
						  AND Ccoest='on';";
						  
		$resCiclos = mysql_query($queryCiclos,$conex) or die("Error: " . mysql_errno() . " - en el query (Consultar cco con ciclos de 24 horas): ".$queryCiclos." - ".mysql_error());
		$numCiclos = mysql_num_rows($resCiclos);
				
		$ccosCiclos = "";
		if($numCiclos > 0)
		{
			while($rowCiclos = mysql_fetch_array($resCiclos))
			{
				$ccosCiclos .= $rowCiclos['Ccocod'].",";
				
			}
			
			$ccosCiclos = substr($ccosCiclos, 0, -1);
		}
		
		return $ccosCiclos;
	}
	
	function consultarPurgaDA($cco)
	{
		global $conex;
		global $wbasedato;
		
		//Consulta el valor de la purga para las dosis adaptadas por centro de costo
		$queryPurga = "SELECT Ccopda 
						 FROM ".$wbasedato."_000011 
						WHERE Ccocod='".$cco."' 
						  AND Ccoest='on';";

		$resPurga = mysql_query($queryPurga,$conex) or die("Error: " . mysql_errno() . " - en el query (Consultar purga DA): ".$queryPurga." - ".mysql_error());
		$numPurga = mysql_num_rows($resPurga);
				
		$purga = 0;
		if($numPurga > 0)
		{
			$rowPurga = mysql_fetch_array($resPurga);
			$purga = $rowPurga['Ccopda'];
		}
		
		return $purga;
	}
	
	function consultarTipoProductoCM($codProdCM)
	{
		global $conex;
		global $wbasedato;
		global $wemp_pmla;
		$wcenmez = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cenmez');
		
		$qDA = 	" SELECT Arttip,Tiptpr 
					FROM ".$wcenmez."_000002,".$wcenmez."_000001
				   WHERE Artcod='".$codProdCM."' 
					 AND Artest='on'
					 AND Arttip=Tipcod
					 AND Tiptpr IN ('DA','DS','DD');";
							 
		$resDA = mysql_query($qDA, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qDA . " - " . mysql_error());
		$numDA = mysql_num_rows($resDA);
		
		$esDA = false;
		if($numDA > 0)
		{
			$esDA = true;
		}
		
		return $esDA;
	}
	
	function esArticuloGenericoCM( $conexion, $wbasedatoMH, $wbasedatoCM, $codArticulo ){
	
		$sql = "SELECT
					*
				FROM
					{$wbasedatoMH}_000068,
					{$wbasedatoCM}_000002,
					{$wbasedatoCM}_000001
				WHERE
					arkcod = '$codArticulo'
					AND artcod = arkcod
					AND arttip = tipcod
					AND tiptpr = arktip
					AND artest = 'on'
					AND arkest = 'on'
					AND tipest = 'on' 
				";
		
		$res = mysql_query( $sql, $conexion ) or die( mysql_errno(). " - Error en el query - ".mysql_errno()  );
		$numrows = mysql_num_rows( $res );
		
		if( $numrows > 0 ){
			return true;
		}
		else{
			return false;
		}
	}
	
	function consultarDosisSiMedicamentoCompuesto($historia,$ingreso,$articulo,$ido)
	{
		global $conex;
		global $wbasedato;
		
		$queryMedCompuesto = "SELECT Defcmp,Defcpa,Defcsa 
								FROM ".$wbasedato."_000059 
							   WHERE Defart='".$articulo."' 
								 AND Defest='on';";
								 
		$resMedCompuesto = mysql_query($queryMedCompuesto, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryMedCompuesto . " - " . mysql_error());
		$numMedCompuesto = mysql_num_rows($resMedCompuesto);
		
		$dosisAntibiotico = "";
		if($numMedCompuesto > 0)
		{
			while($rowsMedCompuesto = mysql_fetch_array($resMedCompuesto))
			{
				if($rowsMedCompuesto['Defcmp']=="on" && $rowsMedCompuesto['Defcpa']!="" && $rowsMedCompuesto['Defcsa']!="")
				{
					// consultar la dosis de antibiotico
					$queryDosisAntibiotico = " SELECT Ekxin1 
												 FROM ".$wbasedato."_000208 
												WHERE Ekxhis='".$historia."' 
												  AND Ekxing='".$ingreso."' 
												  AND Ekxart='".$articulo."' 
												  AND Ekxido='".$ido."' 
												  AND Ekxfec='".date("Y-m-d")."'
												  AND Ekxest='on'

												UNION

											   SELECT Ekxin1 
												 FROM ".$wbasedato."_000209 
												WHERE Ekxhis='".$historia."' 
												  AND Ekxing='".$ingreso."' 
												  AND Ekxart='".$articulo."' 
												  AND Ekxido='".$ido."' 
												  AND Ekxfec='".date("Y-m-d")."'
												  AND Ekxest='on';";
											 
					$resDosisAntibiotico = mysql_query($queryDosisAntibiotico, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryDosisAntibiotico . " - " . mysql_error());
					$numDosisAntibiotico = mysql_num_rows($resDosisAntibiotico);
					
					if($numDosisAntibiotico>0)
					{
						while($rowsDosisAntibiotico = mysql_fetch_array($resDosisAntibiotico))
						{
							$dosisAntibiotico = $rowsDosisAntibiotico['Ekxin1'];
						}
					}
				}
			}
		}

		return $dosisAntibiotico;	
	}
	
	function condicionANecesidad($codCondicion)
	{
		global $wbasedato;
		global $conex;
		
		$qCondicion = " SELECT Contip 
						  FROM ".$wbasedato."_000042
						 WHERE Concod='".$codCondicion."';";
		
		$resCondicion = mysql_query($qCondicion, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qCondicion . " - " . mysql_error());
		$numCondicion = mysql_num_rows($resCondicion);	
		
		$aNecesidad = false;
		if($numCondicion>0)
		{
			$rowsCondicion = mysql_fetch_array($resCondicion);
			
			if($rowsCondicion['Contip']=="AN")
			{
				$aNecesidad = true;
			}
		}
		
		return $aNecesidad;
	}
	
	function consultarEdad($historia,$completa)
	{
		global $conex;
		
		global $wemp_pmla;
		
		
		$q = "SELECT Pacnac 
				FROM root_000037,root_000036 
			   WHERE Orihis='".$historia."' 
				 AND Oriori='".$wemp_pmla."' 
				 AND Oriced=Pacced;";

		$res=mysql_query($q,$conex);
		$num = mysql_num_rows($res);
		
		$edad = "";
		$anos = "";
		if($num>0)
		{
			$row=mysql_fetch_array($res);

			$fechaNacimiento = $row['Pacnac'];
			
			//Edad
			$ann=(integer)substr($fechaNacimiento,0,4)*360 +(integer)substr($fechaNacimiento,5,2)*30 + (integer)substr($fechaNacimiento,8,2);
			$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
			$ann1=($aa - $ann)/360;
			$meses=(($aa - $ann) % 360)/30;
			if ($ann1<1){
				$dias1=(($aa - $ann) % 360) % 30;
				$wedad=(string)(integer)$meses." mes(es) ".(string)$dias1." dia(s)";
				$anos = 0;
			} else {
				$dias1=(($aa - $ann) % 360) % 30;
				$wedad=(string)(integer)$ann1." a&ntilde;o(s) ".(string)(integer)$meses." mes(es) ".(string)$dias1." dia(s)";
				$anos = (integer)$ann1;
			}
			
			$edad = $wedad; 
		
		}
		
		if($completa=="on")
		{
			return $edad;
		}
		else
		{
			return $anos;
		}
		
	}
	
	function consultarConcentracionParaInfusion($articuloCM,$historia)
	{
		global $conex;
		global $wbasedato;
		global $wemp_pmla;
		$wcenmez = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cenmez');
		
		$qDatos = 	" SELECT Edainf,Edadex,Edaemi,Edaema 
						FROM ".$wcenmez."_000021 
					   WHERE Edains='".$articuloCM."' 
						 AND Edaest='on';";
							 
		$resDatos = mysql_query($qDatos, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qDatos . " - " . mysql_error());
		$numDatos = mysql_num_rows($resDatos);
		
		$wedad = consultarEdad($historia,"off");
		
		$arrayDatos = array();
		if($numDatos > 0)
		{
			while($rowsDatos = mysql_fetch_array($resDatos))
			{
				if($wedad>=$rowsDatos['Edaemi'] && $wedad<=$rowsDatos['Edaema'])
				{
					$arrayDatos['concInfusion'] = $rowsDatos['Edainf'];
					break;
				}
			}
		}
		
		return $arrayDatos;
	}
	
	function consultarCodigoNPT($historia,$ingreso,$articulo,$ido)
	{
		global $wbasedato;
		global $conex;
		
		$qExisteNPT = " SELECT Enucnu 
						 FROM ".$wbasedato."_000214
						WHERE Enuhis='".$historia."'
						  AND Enuing='".$ingreso."'
						  AND Enuart='".$articulo."'
						  AND Enuido='".$ido."'
						  AND Enuest='on'
						  AND Enurea='on';";
		
		$resExisteNPT = mysql_query($qExisteNPT, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qExisteNPT . " - " . mysql_error());
		$numExisteNPT = mysql_num_rows($resExisteNPT);	
		
		$codNPT = "";
		if($numExisteNPT>0)
		{
			$rowsExisteNPT = mysql_fetch_array($resExisteNPT);
			
			$codNPT = $rowsExisteNPT['Enucnu'];
		}
		
		return $codNPT;
	}
	
	function consultarSiDAexiste($historia,$ingreso,$articulo,$ido)
	{
		global $wbasedato;
		global $wemp_pmla;
		global $conex;
		$wcenmez = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cenmez');
		
		$qExisteDA = " SELECT Rdacda 
						 FROM ".$wbasedato."_000224,".$wcenmez."_000002
						WHERE Rdahis='".$historia."'
						  AND Rdaing='".$ingreso."'
						  AND Rdaart='".$articulo."'
						  AND Rdaido='".$ido."'
						  AND Rdaest='on'
						  AND Artcod=Rdacda
						  AND Artest='on';";				  
		
		$resExisteDA = mysql_query($qExisteDA, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qExisteDA . " - " . mysql_error());
		$numExisteDA = mysql_num_rows($resExisteDA);	
		
		$codDA = "";
		if($numExisteDA>0)
		{
			$rowsExisteDA = mysql_fetch_array($resExisteDA);
			
			$codDA = $rowsExisteDA['Rdacda'];
			
		}
		
		return $codDA;
	}
	
	function consultarEquivalenteCM($articulo)
	{
		global $wbasedato;
		global $wemp_pmla;
		global $conex;
		
		$arrayCM = array();
		
		$wcenpro = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cenmez');
		
		$qArticuloEquivalenteCM = "SELECT Appcod,Appcnv  
									 FROM ".$wcenpro."_000009 
									 WHERE apppre='".$articulo."' 
									   AND Appest='on';";
		
		$resArticuloEquivalenteCM = mysql_query($qArticuloEquivalenteCM, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qArticuloEquivalenteCM . " - " . mysql_error());
		$numArticuloEquivalenteCM = mysql_num_rows($resArticuloEquivalenteCM);	
		
		$codArtCM = "";
		if($numArticuloEquivalenteCM>0)
		{
			$rowsArticuloEquivalenteCM = mysql_fetch_array($resArticuloEquivalenteCM);
			
			if($rowsArticuloEquivalenteCM['Appcod']!="")
			{
				$codArtCM = $rowsArticuloEquivalenteCM['Appcod'];
				$cantidadEquivalenteCM = $rowsArticuloEquivalenteCM['Appcnv'];
			}
		}
		
		$unidadArtCM = "";
		if($codArtCM!="")
		{
			$qUnidadArticuloEquivalenteCM = "SELECT Artuni 
											   FROM ".$wcenpro."_000002 
											  WHERE Artcod='".$codArtCM."' 
												AND Artest='on';";
			
			$resUnidadArticuloEquivalenteCM = mysql_query($qUnidadArticuloEquivalenteCM, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qUnidadArticuloEquivalenteCM . " - " . mysql_error());
			$numUnidadArticuloEquivalenteCM = mysql_num_rows($resUnidadArticuloEquivalenteCM);	
			
			if($numUnidadArticuloEquivalenteCM>0)
			{
				$rowsUnidadArticuloEquivalenteCM = mysql_fetch_array($resUnidadArticuloEquivalenteCM);
				
				if($rowsUnidadArticuloEquivalenteCM['Artuni']!="")
				{
					$unidadArtCM = $rowsUnidadArticuloEquivalenteCM['Artuni'];
				}
			}
		}
		
		$arrayCM['codigo'] = $codArtCM;
		$arrayCM['unidad'] = $unidadArtCM;
		$arrayCM['conversion'] = $cantidadEquivalenteCM;
		
		return $arrayCM;
	}
	
	function consultarArticulosPorPaciente($historia,$ingreso,$codArticulo,$ronda,$fecharonda)
	{
		global $wbasedato;
		global $wemp_pmla;
		global $conex;
		
		$wfecha = date( "Y-m-d" );
		
		$gruposAntibioticos = consultarAliasPorAplicacion( $conex, $wemp_pmla, "gruposMedicamentosAntibioticos" );
		
		$grupoAntib = explode(",",$gruposAntibioticos);
		
		$cadenaGruposAntibioticos = "";
		for($u=0;$u<count($grupoAntib);$u++)
		{
			$cadenaGruposAntibioticos .= "Artgru LIKE '".$grupoAntib[$u]."%' OR ";
		}
		
		if($cadenaGruposAntibioticos!="")
		{
			$cadenaGruposAntibioticos = " AND (".substr($cadenaGruposAntibioticos, 0, -3).")";
		}
		
		$paciente = consultarUbicacionPaciente( $conex, $wbasedato, $historia, $ingreso);
	
		$tablaHabitaciones = consultarTablaHabitaciones( $conex, $wbasedato, $paciente->servicioActual );
		
		$esCcoDomiciliario = esCcoDomiciliarioMSR( $conex, $wbasedato, $paciente->servicioActual );
		
		$serDom = $esCcoDomiciliario ? '&servicioDomiciliario=on' : '' ;
		
		$qArt = " SELECT Kadhis,Kading,Kadart,Kadido,Kadcfr,Kadufr,Kadfin,Kadhin,Kadobs,Kadcpx,Kadron,Kadcnd,'Dosis adaptada' AS Tipo,Habcco
					FROM ".$wbasedato."_000054,".$tablaHabitaciones."
				   WHERE Kadhis='".$historia."' 
					 AND Kading='".$ingreso."' 
					 AND Kadart='".$codArticulo."' 
					 AND Kadfec='".$wfecha."' 
					 AND Kadsus='off' 
					 AND Kadest='on'
					 AND Kaddoa='on'
					 AND Habhis=Kadhis
					 AND Habing=Kading

				   UNION

				  SELECT Kadhis,Kading,Kadart,Kadido,Kadcfr,Kadufr,Kadfin,Kadhin,Kadobs,Kadcpx,Kadron,Kadcnd,'Dosis adaptada' AS Tipo,Habcco
					FROM ".$wbasedato."_000060,".$tablaHabitaciones." 
				   WHERE Kadhis='".$historia."' 
					 AND Kading='".$ingreso."' 
					 AND Kadart='".$codArticulo."' 
					 AND Kadfec='".$wfecha."' 
					 AND Kadsus='off' 
					 AND Kadest='on'
					 AND Kaddoa='on'
					 AND Habhis=Kadhis
					 AND Habing=Kading

				   UNION

				  SELECT Kadhis,Kading,Kadart,Kadido,Kadcfr,Kadufr,Kadfin,Kadhin,Kadobs,Kadcpx,Kadron,Kadcnd,'Antibiotico' AS Tipo,Habcco
					FROM ".$wbasedato."_000054,".$wbasedato."_000026,".$tablaHabitaciones." 
				   WHERE Kadhis='".$historia."' 
					 AND Kading='".$ingreso."' 
					 AND Kadart='".$codArticulo."' 
					 AND Kadfec='".$wfecha."' 
					 AND Kadsus='off' 
					 AND Kadest='on'
					 AND Kadart=Artcod
					 AND Artest='on'
					 ".$cadenaGruposAntibioticos."
					 AND Habhis=Kadhis
					 AND Habing=Kading

				   UNION

				  SELECT Kadhis,Kading,Kadart,Kadido,Kadcfr,Kadufr,Kadfin,Kadhin,Kadobs,Kadcpx,Kadron,Kadcnd,'Antibiotico' AS Tipo,Habcco
					FROM ".$wbasedato."_000060,".$wbasedato."_000026,".$tablaHabitaciones." 
				   WHERE Kadhis='".$historia."' 
					 AND Kading='".$ingreso."' 
					 AND Kadart='".$codArticulo."' 
					 AND Kadfec='".$wfecha."' 
					 AND Kadsus='off' 
					 AND Kadest='on'
					 AND Kadart=Artcod
					 AND Artest='on'
					 ".$cadenaGruposAntibioticos."
					 AND Habhis=Kadhis
					 AND Habing=Kading

				   UNION

				  SELECT Kadhis,Kading,Kadart,Kadido,Kadcfr,Kadufr,Kadfin,Kadhin,Kadobs,Kadcpx,Kadron,Kadcnd,'Central de mezclas' AS Tipo,Habcco
					FROM ".$wbasedato."_000054,".$tablaHabitaciones."
				   WHERE Kadhis='".$historia."' 
					 AND Kading='".$ingreso."' 
					 AND Kadart='".$codArticulo."' 
					 AND Kadfec='".$wfecha."' 
					 AND Kadsus='off' 
					 AND Kadest='on'
					 AND Kadori='CM'
					 AND Habhis=Kadhis
					 AND Habing=Kading

				   UNION

				  SELECT Kadhis,Kading,Kadart,Kadido,Kadcfr,Kadufr,Kadfin,Kadhin,Kadobs,Kadcpx,Kadron,Kadcnd,'Central de mezclas' AS Tipo,Habcco
					FROM ".$wbasedato."_000060,".$tablaHabitaciones."
				   WHERE Kadhis='".$historia."' 
					 AND Kading='".$ingreso."' 
					 AND Kadart='".$codArticulo."' 
					 AND Kadfec='".$wfecha."' 
					 AND Kadsus='off' 
					 AND Kadest='on'
					 AND Kadori='CM'
					 AND Habhis=Kadhis
					 AND Habing=Kading;";
		
		// echo "<pre>".print_r($qArt,true)."</pre>";
		
		$resArt = mysql_query($qArt,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qArt." - ".mysql_error());
		$numArt = mysql_num_rows($resArt);
		
		$arrayArticulos = array();
		if($numArt > 0)
		{
			while($rows = mysql_fetch_array($resArt))
			{
				$info = "";
				$info = $rows['Tipo'];
				$regleta = $rows['Kadcpx'];
				
				$condicionANecesidad = false;
				if($rows['Kadcnd']!="")
				{
					$condicionANecesidad = condicionANecesidad($rows['Kadcnd']);
				}
				
				
				$rondasRegleta = explode(",",$regleta);
				
				for($i=0;$i<count($rondasRegleta);$i++)
				{
					$rondaReglet = explode("-",$rondasRegleta[$i]);
					
					if($ronda.":00:00"==$rondaReglet[0] && ($rondaReglet[1]>$rondaReglet[2] || $condicionANecesidad))
					{
						if($rows['Tipo']=="Dosis adaptada" || $rows['Tipo']=="Antibiotico")
						{
							$existeDA = consultarSiDAexiste($historia,$ingreso,$rows['Kadart'],$rows['Kadido']);
						
							if($existeDA=="")
							{
								$arrayEquivalenteCM = consultarEquivalenteCM($rows['Kadart']);
								
								$equivalenteCM = $arrayEquivalenteCM['codigo'];
								$unidadArtCM = $arrayEquivalenteCM['unidad'];
								$conversionEquivalenteCM = $arrayEquivalenteCM['conversion'];
								
								$dosis = $rows['Kadcfr'];
								
								// // Valida si el medicamento esta marcado como compuesto en movhos_000059, si es así consultar en movhos_000208 o movhos_000209 la dosis del antibiotico
								// $dosisMedicamentoCompuesto = consultarDosisSiMedicamentoCompuesto($historia,$ingreso,$rows['Kadart'],$rows['Kadido']);
								
								// if($dosisMedicamentoCompuesto!="")
								// {
									// $dosis = $dosisMedicamentoCompuesto;
								// }
								
								//calculo
								if( $rows['Kadufr'] != $unidadArtCM )
								{
									//Se busca la concentración del articulo, para dejar la dosis del médico en las unidades en que se factura
									$concentracionArticuloSF = consultarConcentracionArticuloSF( $conex, $wbasedato, $rows['Kadart'] );
									$dosis 					 = $dosis/$concentracionArticuloSF*$conversionEquivalenteCM;
								}
								
								$dosisConPurga=$dosis;
								// tener en cuenta la purga
								$purgaDA = consultarPurgaDA($rows['Habcco']);
								
								if($purgaDA!="0" && $equivalenteCM!="")
								{
									$concentracionParaInfusion = consultarConcentracionParaInfusion($equivalenteCM,$historia);
									$dosisConPurga = ($purgaDA*$concentracionParaInfusion['concInfusion'])+ $dosis;
								}
								
								$dosis = round($dosis*1,2);
								$dosisConPurga = round($dosisConPurga,2);
								
								// crear producto DA
								if($equivalenteCM=="")
								{
									$urlCM = "Sin equivalente en central de mezclas (cenpro_000009)"; 
									$urlCM .= "<br><A href='perfilFarmacoterapeutico.php?wemp_pmla=".$wemp_pmla."&waccion=a&whistoria=".$historia."&wfecha=".$wfecha.$serDom."' target=_blank> Ir al Perfil </A>"; 
								}
								else
								{
									$urlCM = "<A href='../../cenpro/procesos/cen_mez.php?wemp_pmla=".$wemp_pmla."&DA_historia=".$historia."&DA_ingreso=".$ingreso."&DA_articulo=".$rows['Kadart']."&DA_ido=".$rows['Kadido']."&DA_articuloCM=".$equivalenteCM ."&DA_cantidad=".$dosisConPurga."&DA_cantidadSinPurga=".$dosis."&DA_tipo=".$rows['Tipo']."&tippro=03-Dosis adaptada-NO CODIFICADO&pintarListaDAPendientes=true&wronda=".$ronda."&wfecharonda=".$fecharonda."&DA_cco=".$rows['Habcco']."' target=_blank> Crear producto </A>"; 
									if($rows['Tipo']=="Antibiotico")
									{
										$urlCM .= "<br>-<br><A href='perfilFarmacoterapeutico.php?wemp_pmla=".$wemp_pmla."&waccion=a&whistoria=".$historia."&wfecha=".$wfecha.$serDom."' target=_blank> Ir al Perfil </A>"; 
									}
								}
							}
							else
							{
								$productoEsDA = consultarTipoProductoCM($existeDA);
							
								if($productoEsDA)
								{
									$info = "Dosis adaptada creada: ".$existeDA;
									$codigoDA = $existeDA;
									// crear lote
									$urlCM = "<A href='../../cenpro/procesos/lotes.php?wemp_pmla=".$wemp_pmla."&parcon=".$codigoDA."&forcon=Codigo del Producto&pintar=1&whistoria=".$historia."&wingreso=".$ingreso."&warticuloda=".$rows['Kadart']."&idoda=".$rows['Kadido']."&wronda=".$ronda."&wfecharonda=".$fecharonda."' target=_blank> Crear lote </A>"; 
								}
								else
								{
									// Ir al perfil
									$urlCM = "<A href='perfilFarmacoterapeutico.php?wemp_pmla=".$wemp_pmla."&waccion=a&whistoria=".$historia."&wfecha=".$wfecha.$serDom."' target=_blank> Ir al Perfil </A>"; 
								}
								
							}
						}
						elseif($rows['Tipo']=="Central de mezclas")
						{
							$rows['Kadart'] = strtoupper($rows['Kadart']);
							$codProdCM = substr($rows['Kadart'],0,2);
							
							// validar segun el codigo
							$tipoProtocoloDA = consultarProtocoloDA();
							$tipoProtocoloNPT = consultarProtocoloNPT();
							
							// if(strtoupper($codProdCM)==$tipoProtocoloDA)
							if(in_array(strtoupper($codProdCM),$tipoProtocoloDA))
							{
								$existeDA = consultarSiDAexiste($historia,$ingreso,$rows['Kadart'],$rows['Kadido']);
						
								if($existeDA=="")
								{
									$codigoGenerico = esArticuloGenericoCM( $conex, $wbasedato, "cenpro", $rows['Kadart'] );
								
									if($codigoGenerico)
									{
										$equivalenteCM = "";
										$dosis = $rows['Kadcfr'];
										$dosisConPurga=$dosis;
										
										
										$info = "Dosis adaptada genérica";
										// crear producto DA
										$urlCM = "<A href='../../cenpro/procesos/cen_mez.php?wemp_pmla=".$wemp_pmla."&DA_historia=".$historia."&DA_ingreso=".$ingreso."&DA_articulo=".$rows['Kadart']."&DA_ido=".$rows['Kadido']."&DA_articuloCM=".$equivalenteCM ."&DA_cantidad=".$dosisConPurga."&DA_cantidadSinPurga=".$dosis."&DA_tipo=Generica&tippro=03-Dosis adaptada-NO CODIFICADO&pintarListaDAPendientes=true&wronda=".$ronda."&wfecharonda=".$fecharonda."&DA_cco=".$rows['Habcco']."' target=_blank> Crear producto </A>"; 
									}
									else
									{
										$productoEsDA = consultarTipoProductoCM($rows['Kadart']);
							
										if($productoEsDA)
										{
											// crear lote
											$codigoDA = $rows['Kadart'];
											$info = "Dosis adaptada creada: ".$codigoDA;
											$urlCM = "<A href='../../cenpro/procesos/lotes.php?wemp_pmla=".$wemp_pmla."&parcon=".$codigoDA."&forcon=Codigo del Producto&pintar=1&whistoria=".$historia."&wingreso=".$ingreso."&warticuloda=".$codigoDA."&idoda=".$rows['Kadido']."&sinReemplazo=on&wronda=".$ronda."&wfecharonda=".$fecharonda."' target=_blank> Crear lote </A>"; 
										
											
										}
										else
										{
											// Ir al perfil
											$urlCM = "<A href='perfilFarmacoterapeutico.php?wemp_pmla=".$wemp_pmla."&waccion=a&whistoria=".$historia."&wfecha=".$wfecha.$serDom."' target=_blank> Ir al Perfil </A>"; 
										}
										
									}
									
								}
								else
								{
									$productoEsDA = consultarTipoProductoCM($existeDA);
									
									if($productoEsDA)
									{
										$info = "Dosis adaptada creada: ".$existeDA;
										// crear lote
										$codigoDA = $existeDA;
										$urlCM = "<A href='../../cenpro/procesos/lotes.php?wemp_pmla=".$wemp_pmla."&parcon=".$codigoDA."&forcon=Codigo del Producto&pintar=1&whistoria=".$historia."&wingreso=".$ingreso."&warticuloda=".$rows['Kadart']."&idoda=".$rows['Kadido']."&wronda=".$ronda."&wfecharonda=".$fecharonda."' target=_blank> Crear lote </A>"; 
									}
									else
									{
										// Ir al perfil
										$urlCM = "<A href='perfilFarmacoterapeutico.php?wemp_pmla=".$wemp_pmla."&waccion=a&whistoria=".$historia."&wfecha=".$wfecha.$serDom."' target=_blank> Ir al Perfil </A>"; 
									}
									
								}
							}
							elseif(strtoupper($codProdCM)==$tipoProtocoloNPT)
							{
								$codigoNPT = consultarCodigoNPT($historia,$ingreso,$rows['Kadart'],$rows['Kadido']);
								
								if($codigoNPT=="")
								{
									// consultar datos npt
									$qDatosNPT = "SELECT Enupes,Enutin,Enupur,Enuvol 
													FROM ".$wbasedato."_000214
												   WHERE Enuhis='".$historia."'
													 AND Enuing='".$ingreso."'
													 AND Enuart='".$rows['Kadart']."'
													 AND Enuido='".$rows['Kadido']."'
													 AND Enuest='on'
													 AND Enurea='off';";
											   
									$resDatosNPT = mysql_query($qDatosNPT,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qDatosNPT." - ".mysql_error());
									$numDatosNPT = mysql_num_rows($resDatosNPT);
									
									$tipoProtocoloNPT="";
									if($numDatosNPT > 0)
									{
										$info = "Nutrición parenteral";
										$rowsDatosNPT = mysql_fetch_array($resDatosNPT); 
										$tipoProtocoloNPT=$rowsDatosNPT['Tiptpr'];
										
										// crear NPT
										$urlCM = "<A href='../../cenpro/procesos/cen_mez.php?wemp_pmla=".$wemp_pmla."&historia=".$historia."&NPT_historia=".$historia."&NPT_ingreso=".$ingreso."&NPT_articulo=".$rows['Kadart']."&NPT_ido=".$rows['Kadido']."&peso=".$rowsDatosNPT['Enupes']."&purga=".$rowsDatosNPT['Enupur']."&volumen=".$rowsDatosNPT['Enuvol']."&NPT_tiempoInfusion=".$rowsDatosNPT['Enutin']."&NPT_origen=ordenes&tippro=02-Nutricion Parenteral-NO CODIFICADO&pintarListaNPTPendientes=true' target=_blank> Crear producto </A>"; 
										
									}
									else
									{
										$codigoGenerico = esArticuloGenericoCM( $conex, $wbasedato, "cenpro", $rows['Kadart'] );
								
										// if($rows['Kadart']=="NU0000")
										if($codigoGenerico)
										{
											$info = "Nutrición parenteral genérica";
											// crear NPT generica
											$urlCM = "<A href='../../cenpro/procesos/cen_mez.php?wemp_pmla=".$wemp_pmla."&historia=".$historia."&NPT_historia=".$historia."&NPT_ingreso=".$ingreso."&NPT_articulo=".$rows['Kadart']."&NPT_ido=".$rows['Kadido']."&&NPT_origen=kardex&tippro=02-Nutricion Parenteral-NO CODIFICADO&pintarListaNPTPendientes=true' target=_blank> Crear producto </A>"; 
										}
										else
										{
											$codigoNPT = $rows['Kadart'];
											$info = "Nutrición parenteral creada: ".$codigoNPT;
											$urlCM = "<A href='../../cenpro/procesos/lotes.php?wemp_pmla=".$wemp_pmla."&parcon=".$codigoNPT."&forcon=Codigo del Producto&pintar=1&whistoria=".$historia."&wingreso=".$ingreso."&sinReemplazo=on' target=_blank> Crear lote </A>"; 
										}
										
									}
								}
								else
								{
									$info = "Nutrición parenteral creada: ".$codigoNPT;
									$urlCM = "<A href='../../cenpro/procesos/lotes.php?wemp_pmla=".$wemp_pmla."&parcon=".$codigoNPT."&forcon=Codigo del Producto&pintar=1' target=_blank> Crear lote </A>"; 
								}
							}
							else
							{
								// Ir al perfil
								$urlCM = "<A href='perfilFarmacoterapeutico.php?wemp_pmla=".$wemp_pmla."&waccion=a&whistoria=".$historia."&wfecha=".$wfecha.$serDom."' target=_blank> Ir al Perfil </A>"; 
							}
						}
						else
						{
							// Ir al perfil
							$urlCM = "<A href='perfilFarmacoterapeutico.php?wemp_pmla=".$wemp_pmla."&waccion=a&whistoria=".$historia."&wfecha=".$wfecha.$serDom."' target=_blank> Ir al Perfil </A>"; 
						}
						
						$kadobs1="";
						if(trim($rows['Kadobs'])!="")
						{
							$observaciones=explode("<div",$rows['Kadobs']);
						
							for($s=1;$s<count($observaciones);$s++)
							{
								$observacion="<div".$observaciones[$s];
								
								$obs = nl2br(strip_tags( substr( $observacion, 0, strpos($observacion, "<span" ) ) ));
								
								if(trim($obs)!="")
								{
									$kadobs1 .= "- ".$obs."<br>";
								}
								
							}
						}
						
						$arrayArticulos[$rows['Kadido']]['ido'] = $rows['Kadido'];
						$arrayArticulos[$rows['Kadido']]['tipo'] = $rows['Tipo'];
						$arrayArticulos[$rows['Kadido']]['dosis'] = $rows['Kadcfr'];
						$arrayArticulos[$rows['Kadido']]['unidad'] = $rows['Kadufr'];
						$arrayArticulos[$rows['Kadido']]['FechaInicio'] = $rows['Kadfin'];
						$arrayArticulos[$rows['Kadido']]['HoraInicio'] = $rows['Kadhin'];
						$arrayArticulos[$rows['Kadido']]['observacion'] = $kadobs1;
						$arrayArticulos[$rows['Kadido']]['url'] = $urlCM;
						$arrayArticulos[$rows['Kadido']]['info'] =  $info;
						
					}
				}
			}
		}

		return $arrayArticulos;		
	}
	
	function consultarProtocoloNPT()
	{
		global $wbasedato;
		global $conex;
		global $wemp_pmla;
		$wcenmez = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cenmez');
		
		$qNPT = "SELECT Tiptpr 
				  FROM ".$wcenmez."_000001,".$wcenmez."_000002,".$wbasedato."_000068
				 WHERE Tipnco = 'off' 
				   AND Tipcdo != 'on'
				   AND Tipest = 'on'
				   AND Arttip = Tipcod
				   AND Artcod = Arkcod
				   AND Tiptpr = Arktip;";
				   
		$resNPT = mysql_query($qNPT,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qNPT." - ".mysql_error());
		$numNPT = mysql_num_rows($resNPT);
		
		$tipoProtocoloNPT = "";
		if($numNPT > 0)
		{
			$rowsNPT = mysql_fetch_array($resNPT); 
			$tipoProtocoloNPT = $rowsNPT['Tiptpr'];
		}

		return $tipoProtocoloNPT;		
	}
	
	function consultarProtocoloDA()
	{
		global $wbasedato;
		global $conex;
		global $wemp_pmla;
		$wcenmez = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cenmez');
		
		$qDA = "SELECT Tiptpr 
				  FROM ".$wcenmez."_000001,".$wcenmez."_000002,".$wbasedato."_000068
				 WHERE tippro = 'on' 
				   AND Tipnco = 'on' 
				   AND Tipcdo != 'on'
				   AND Tipest = 'on'
				   AND Arttip = Tipcod
				   AND Artcod = Arkcod
				   AND Artest = 'on'
				   AND Tiptpr = Arktip
				   AND Tiptpr IN ('DA','DD','DS');";
				   
		$resDA = mysql_query($qDA,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qDA." - ".mysql_error());
		$numDA = mysql_num_rows($resDA);
						
		$arrayTipoProtocoloDA = array();
		if($numDA > 0)
		{
			while($rowsDA = mysql_fetch_array($resDA))
			{
				$arrayTipoProtocoloDA[] = $rowsDA['Tiptpr'];
			}
		}
		
		return $arrayTipoProtocoloDA;
	}
	
	
	
  function pintarPaciente2( $pacientes,$codArticulo,$cadenaTooltipDetalle ){
	
	global $wemp_pmla;
	global $wfecha;
	global $wccotim;
	global $wccoo;
	global $wbasedato;
	global $conex;
	
	$ccoOrigen = explode("-",$wccoo);
	
	echo "<table align=center>";
	echo "<tr class=encabezadoTabla>";
	echo "<th>Historia</th>";
	echo "<th>Ingreso</th>";
	echo "<th>Habitación</th>";
	echo "<th>Paciente</th>";
	echo "<th>Dosis</th>";
	echo "<th>Condición</th>";
	echo "<th>Acción</th>";
	echo "</tr>";
	
	//Pinto los pacientes que están asociados a los medicamentos
	foreach( $pacientes as $keyPacientes => $valuePacientes ){
		
		
		$artPaciente = consultarArticulosPorPaciente($valuePacientes['historia'],$valuePacientes['ingreso'],$codArticulo,$valuePacientes['ronda'],$valuePacientes['fecharonda']);
		
		if( $wclass=="fila1" )
			$wclass="fila2";
		else
			$wclass="fila1";
		
		// -----------------------
		$qAlta = " SELECT COUNT(*) 
					 FROM ".$wbasedato."_000018 
					WHERE ubihis = '".$valuePacientes['historia']."'
					  AND ubiing = '".$valuePacientes['ingreso']."'
					  AND ubialp = 'on' ";
				  
		$resAlta = mysql_query($qAlta,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qAlta." - ".mysql_error());
		$rowAlta = mysql_fetch_array($resAlta);
		
		$pacienteAlta = "";
		if ($rowAlta[0] > 0)
		{
		   $wclass = "fondoAmarillo";
			$pacienteAlta = " - Paciente de Alta";
		}
		
		// -----------------------
		
		echo "<tr class='".$wclass."'>";
		echo "<td align=center>".$valuePacientes['historia']."</td>";
		echo "<td align=center>".$valuePacientes['ingreso']."</td>";
		echo "<td align=center>".$valuePacientes['habitacion']."</td>";
		echo "<td>".$valuePacientes['nombre'].$pacienteAlta."</td>";
		echo "<td>".$valuePacientes['dosis']."</td>";
		echo "<td><b>".$valuePacientes['condicion']."</b></td>";
		
		$paciente = consultarUbicacionPaciente( $conex, $wbasedato, $valuePacientes['historia'], $valuePacientes['ingreso'] );
	
		$tablaHabitaciones = consultarTablaHabitaciones( $conex, $wbasedato, $paciente->servicioActual );
		
		$esCcoDomiciliario = esCcoDomiciliarioMSR( $conex, $wbasedato, $paciente->servicioActual );
		
		$serDom = $esCcoDomiciliario ? '&servicioDomiciliario=on' : '' ;
		
		if($ccoOrigen[0]=="1051")
		{
			if(count($artPaciente)==0)
			{
				echo "<td align=center><A href='../procesos/perfilFarmacoterapeutico.php?wemp_pmla=".$wemp_pmla."&waccion=a&whistoria=".$valuePacientes['historia']."&wfecha=".$wfecha.$serDom."' target=_blank> Ir al Perfil </A></td>";
			}
			else
			{
				
				echo "<td>";
				echo "	<table style='width:100%;height:100%;'>";
					foreach($artPaciente as $keyArt => $valueArt)
					{
						if ($fila_lista=='Fila1')
							$fila_lista = "Fila2";
						else
							$fila_lista = "Fila1";
						
						$observacionesCM = "";
						if($valueArt['observacion']!="")
						{
							$observacionesCM = "Observaciones: <br>".trim($valueArt['observacion']);
						}
						// ------------------------------------------
						// Tooltip
						// ------------------------------------------	
							$infoTooltip = "Tipo: ".$valueArt['info']."<br> Fecha y hora de inicio: ".$valueArt['FechaInicio']." ".$valueArt['HoraInicio']."<br>".$observacionesCM;
							$tooltip = "<div id=\"dvTooltip_".$valueArt['ido']."\" style=\"font-family:verdana;font-size:10pt\">".$infoTooltip."</div>";
							$cadenaTooltipDetalle .= "tooltipDetalle_".$valueArt['ido']."|";
						// ------------------------------------------					
					
						$urlCM = $valueArt['url'];
						
						echo "  <tr id='tooltipDetalle_".$valueArt['ido']."' title='".$tooltip."'>
									<td style='font-size:10pt;border-color:#FFFFFF;border: 1px solid white;width:70%;'>".$valueArt['dosis']." ".$valueArt['unidad']."</td>
									<td style='font-size:10pt;border-color:#FFFFFF;border: 1px solid white;width:30%;' align='center'>".$urlCM."</td>
								</tr>";
					}
				echo "	</table>";
				echo "</td>";
				
			}
		}
		else
		{
			echo "<td align=center><A href='../procesos/perfilFarmacoterapeutico.php?wemp_pmla=".$wemp_pmla."&waccion=a&whistoria=".$valuePacientes['historia']."&wfecha=".$wfecha.$serDom."' target=_blank> Ir al Perfil </A></td>";
		}
		
		// echo "<td align=center><A href='../procesos/perfilFarmacoterapeutico.php?wemp_pmla=".$wemp_pmla."&waccion=a&whistoria=".$valuePacientes['historia']."&wfecha=".$wfecha."' target=_blank> Ir al Perfil </A></td>";
		echo "</tr>";
	}
	
	echo "</table>";
	
	
	return $cadenaTooltipDetalle;
}

function pintarAritculos( $articulos ){
							
	global $wccotim;
	global $totalRondas;
	global $whora_par_act;

	echo "<center><table>";
	echo "<tr class=encabezadoTabla>";
	echo "<th colspan=1><font size=4>Ronda</font></th>";
	echo "<th colspan=3><font size=4>Medicamento</font></th>";
	echo "<th colspan=2><font size=4>Dosis<br>según Kardex</font></th>";
	echo "<th colspan=2><font size=4>Cantidad Unidades<br>según Perfil</font></th>";
	echo "<th colspan=1><font size=4>No Pos</font></th>";
	echo "<th colspan=1><font size=4>Es de<br>Control</font></th>";
	echo "</tr>";
	
	$cadenaTooltipDetalle = "";
	
	$i = 0;
	//Reocorro todas las rondas posibles
	for( $j = 0; $j < $totalRondas; $j += 2 ){
		
		$ronda = gmdate( "H", $whora_par_act*3600+$j*3600 );
		
		//Se muestra los datos correspondientes a las rondas correspondientes
		if( !empty( $articulos[ $ronda ] ) ){
			
			$valueDatos = $articulos[ $ronda ];	//Son los datos correspondientes a los articulos
				
			if($wclass=="fila1")
				$wclass="fila2";
			else
				$wclass="fila1";
			
			$mostrarRonda = true;
			
			foreach( $valueDatos as $keyArts => $valueArts ){
				
				$idTablaPacientes = $i.$valueArts['codigoArticulo'];
				
				echo "<tr class='".$wclass."' onclick=\"intercalar('".$idTablaPacientes."','".$ronda."')\">";
				
				if( $mostrarRonda )
					echo "<td id=tdRonda$ronda rowspan='".( count( $valueDatos ) )."' align=center><b style='font-size:12pt'>".$ronda."</b></td>";		//Ronda
				
				echo "<td>".$valueArts['ubicacion']."</td>";                    		//Ubicación del medicamento
				echo "<td>".$valueArts['codigoArticulo']."</td>";                    	//Codigo Medicamento
				echo "<td>".$valueArts['nombreArticulo']."</td>";                    	//Nombre Medicamento
				
				if ($valueArts['aprobado'] == "on")                            			//Indica si esta aprobado en el perfil
				{
					echo "<td align=center>".$valueArts['cantidadDosis']."</td>";   	//Cantidad de Dosis
					echo "<td align=center>".$valueArts['fraccionDosis']."</td>";   	//Fraccion de la dosis
					echo "<td align=center>".$valueArts['cantidadUnidades']."</td>";   	//Can. Unidades
					echo "<td align=center>".$valueArts['presentacion']."</td>";   		//Presentacion
				}
				else{
					if( $wccotim == "SF" )
						echo "<td align=center colspan=4 bgcolor=FFFFCC>Sin Aprobar en el perfil</td>";
					else{
						if( $valueArts['confirmado'] == "off" )               			//No confirmado. Es antibiotico
							echo "<td align=center colspan=4 bgcolor=FFFFCC>Medicamento Sin Confirmar en el Kardex</td>";
						else
							echo "<td align=center colspan=4 bgcolor=FFFFCC>Sin Aprobar en el perfil</td>";
					}
				}

				echo "<td align=center>".$valueArts['noPos']."</td>";     				//Es Pos
				echo "<td align=center>".$valueArts['control']."</td>";   				//Es de Control
				echo "</tr>";

				echo "<tr id='".$idTablaPacientes."' style='display:none'>";
				echo "<td colspan=10 align=center>";
					// pintarPaciente2( $valueArts['pacientes'] );	//Pinta los pacientes que hay por articulo
					$cadenaTooltipDetalle = pintarPaciente2( $valueArts['pacientes'],$valueArts['codigoArticulo'],$cadenaTooltipDetalle );	//Pinta los pacientes que hay por articulo
				echo "</td>";
				echo "</tr>";
				
				$mostrarRonda = false;
				$i++;
			}
		}
	}
	echo "<input type='hidden' id='tooltipDetalle' value='".$cadenaTooltipDetalle."'>";
	echo "</table></center>";
}


  function mostrar_empresa($wemp_pmla)
     {
	  global $user;
	  global $conex;
	  global $wemp_pmla;
	  global $wafinidad;
	  global $wbasedato;
	  global $wtabcco;
	  global $winstitucion;
	  global $wactualiz;
	  $wcenmez = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cenmez');
	  //Traigo TODAS las aplicaciones de la empresa, con su respectivo nombre de Base de Datos
	  $q = " SELECT detapl, detval, empdes "
	      ."   FROM root_000050, root_000051 "
	      ."  WHERE empcod = '".$wemp_pmla."'"
	      ."    AND empest = 'on' "
	      ."    AND empcod = detemp ";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num = mysql_num_rows($res);

	  if ($num > 0 )
	     {
		  for ($i=1;$i<=$num;$i++)
		     {
		      $row = mysql_fetch_array($res);

		      if ($row[0] == "cenmez")
		         $wcenmez=$row[1];
				
		      if ($row[0] == "afinidad")
		         $wafinidad=$row[1];

		      if ($row[0] == "movhos")
		         $wbasedato=$row[1];

		      if ($row[0] == "tabcco")
		         $wtabcco=$row[1];
	         }
	     }
	    else
	       echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";

	  $winstitucion=$row[2];

	  encabezado("Medicamentos por Ronda y C.Costo",$wactualiz, "clinica");
     }


  function hora_par()
    {
	 global $whora_par_actual;
	 global $whora_par_anterior;
	 global $wfecha;
	 global $wfecha_actual;


	 $whora_Actual=date("H");
	 $whora_Act=($whora_Actual/2);

	 $wfecha_actual=date("Y-m-d");

	 if (!is_int($whora_Act))   //Si no es par le resto una hora
	    {
		 $whora_par_actual=$whora_Actual-1;
	     if ($whora_par_actual=="00" or $whora_par_actual=="0")    //Esto se hace porque el Kardex o el metodo que calcula las horas de aplicacion reconoce es las 24 horas y no las 00
	         $whora_par_actual="24";
	    }
	   else
	     {
		  if ($whora_Actual=="00" or $whora_Actual=="0")    //Esto se hace porque el Kardex o el metodo que calcula las horas de aplicacion reconoce es las 24 horas y no las 00
	         $whora_par_actual="24";
		    else
		       $whora_par_actual=$whora_Actual;
	     }

	  if ($whora_Actual=="02" or $whora_Actual=="2")        //Esto se hace porque el Kardex o el metodo que calcula las horas de aplicacion reconoce es las 24 horas y no las 00
	     $whora_par_anterior="24";
	    else
	      {
		   if (($whora_par_actual-2) == "00")               //Abril 12 de 2011
		      $whora_par_anterior="24";
		     else
	            $whora_par_anterior = $whora_par_actual-2;
		  }

	  if (strlen($whora_par_anterior) == 1)
	     $whora_par_anterior="0".$whora_par_anterior;

	  if (strlen($whora_par_actual) == 1)
	     $whora_par_actual="0".$whora_par_actual;
    }


  function pintarPaciente($res, $num, $codigo, $medicamento)
     {
	  global $conex;
	  global $wbasedato;
	  global $wemp_pmla;
	  global $wfecha;

	  global $arPacientes;
	  global $arHabitaciones;


	  if ($num > 0)
	     {
		  $wclass="fila2";

		  echo "<table align=center>";
		  echo "<tr class=encabezadoTabla>";
		  echo "<th>Historia</th>";
		  echo "<th>Ingreso</th>";
		  echo "<th>Habitación</th>";
		  echo "<th>Paciente</th>";
		  echo "<th>Dosis</th>";
		  echo "<th>Condición</th>";
		  echo "<th>Acción</th>";
		  echo "</tr>";
		  for ($i=1; $i<=$num; $i++)
		     {
			  if ($wclass=="fila1")
			     $wclass="fila2";
				else
                  $wclass="fila1";

			  $row = mysql_fetch_array($res);


			   // Creo Array de pacientes para mostrar al terminar el reporte
			  $arPacientes[ $row[2] ]['Historia'] = $row[0];
			  $arPacientes[ $row[2] ]['Ingreso'] = $row[1];
			  $arPacientes[ $row[2] ]['Nombre'] = $row[3]." ".$row[4]." ".$row[5]." ".$row[6];
			  $arPacientes[ $row[2] ]['Habitacion'] = $row[2];
			  $nom_medica= $codigo."-".$medicamento."|";
			  @$ya_existe=strpos($arPacientes[ $row[2] ]['Medicamento'], $nom_medica);
			  $ya_existe;
			  if($ya_existe!== false)
			  {
			  // No lo almaceno porque ya existe dentro del array
			  }
			  else
			{
			  @$arPacientes[ $row[2] ]['Medicamento'] = $arPacientes[ $row[2] ]['Medicamento'].$nom_medica;
			  @$arPacientes[ $row[2] ]['NumMed'] =$arPacientes[ $row[2] ]['NumMed']+1;
			}
			  $arHabitaciones[ $row[2] ] = $row[2];

			  echo "<tr class='".$wclass."'>";
			  echo "<td align=center>".$row[0]."</td>";
			  echo "<td align=center>".$row[1]."</td>";
			  echo "<td align=center>".$row[2]."</td>";
			  echo "<td>".$row[3]." ".$row[4]." ".$row[5]." ".$row[6]."</td>";
			  echo "<td>".$row[7]." ".$row[8]."</td>";

			  $wcond="";
			  if ($row[9] != "")
			     {
				  $q = " SELECT condes, contip "
					  ."   FROM ".$wbasedato."_000042 "
					  ."  WHERE concod = '".$row[9]."'";
				  $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				  $row1 = mysql_fetch_array($res1);

				  $wcond=$row1[0]." - ".$row1[1];
				 }
			  echo "<td><b>".$wcond."</b></td>";
			  echo "<td align=center><A href='../procesos/perfilFarmacoterapeutico.php?wemp_pmla=".$wemp_pmla."&waccion=a&whistoria=".$row[0]."&wfecha=".$wfecha.$serDom."' target=_blank> Ir al Perfil </A></td>";
			  echo "</tr>";
			 }
		  echo "</table>";
		 }
	 }

	
  function elegir_centro_de_costo()
     {
	  global $user;
	  global $conex;
	  global $wemp_pmla;
	  global $wafinidad;
	  global $wbasedato;
	  global $wtabcco;
	  global $winstitucion;
	  global $wactualiz;
	  global $wcco;

	  global $whora_par_actual;
	  global $whora_par_sigte;
	  global $whora_par_anterior;
	  $wcenmez = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cenmez');

	  hora_par();
	  
	  
	  
		$filtro="--";
		
		$cco="ccohos = 'off' AND ccofac = 'on' AND ccotra = 'on'";  // filtros para la consulta
		$centrosCostos = consultaCentrosCostos($cco, $filtro="--");  //tipo 3
		
		$cco="ccocpx = 'on' AND ccohos = 'on' OR ccolac = 'on'";  // filtros para la consulta
		$centrosCostosDestino = consultaCentrosCostos($cco, $filtro);  //tipo 3
		
		$cco="ccotra != 'on' AND ccodom = 'on' AND ccoest = 'on'";  // filtros para la consulta
		$centrosCostosDomiciliario = consultaCentrosCostos($cco, $filtro);  //tipo 3
		
		$serviciosDomiciliarios = [];
		foreach( $centrosCostosDomiciliario as $ccoDoms ){
			$serviciosDomiciliarios[] = $ccoDoms->codigo;
		}
		
		$ccoConCiclos24 = consultarCcoCiclos24();
		
	  echo "<div>
				<table align='center'>
					<tr class='encabezadoTabla'>
						<td colspan='2' align='center'>Seleccione los parámentros de búsqueda</td>
					</tr>
					<tr>
						<td class='fila1'>Ronda: </td>
						<td class='fila2'>
							<select name='whora_par_actual' size='1'>
								<option selected>".$whora_par_actual."</option>
								<option>2</option>
								<option>4</option>
								<option>6</option>
								<option>8</option>
								<option>10</option>
								<option>12</option>
								<option>14</option>
								<option>16</option>
								<option>18</option>
								<option>20</option>
								<option>22</option>
								<option>00</option>
							</select>
							<select name='diaSelecionada' size='1'>
								<option value='1'>Hoy</option>
								<option value='2'>Mañana</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class='fila1'>Unidad de origen: </td>
						<td class='fila2'>
							<select id='wccoo' name='wccoo' size='1'>
								<option value='' selected>&nbsp</option>";
								foreach ($centrosCostos as $centroCostos)
								{
									echo "<option value='".$centroCostos->codigo."-".$centroCostos->nombre."'>".$centroCostos->codigo."-".$centroCostos->nombre."</option>";
								}
		echo				"</select>
						</td>
					</tr>
					<tr style='display: none;' id='encabezadoTipos'>
						<td class='fila1'>Tipo de medicamento:</td>
						<td class='fila2'>
							<select id='selectTipos' name='tipoMedicamento' style='display: none;' size='1' >
								<option value='' selected disabled></option>
								<option value='%-Todos'>%-Todos</option>
								<option value='DA-Dosis Adapatada'>DA-Dosis Adapatada</option>
								<option value='NU-Nutricion Parenteral'>NU-Nutricion Parenteral</option>
								<option value='QT-Quimioterapia'>QT-Quimioterapia</option>
								<option value='NE-No Esteril'>NE-No Esteril</option>
								<option value='OTROS-Otros'>Otros</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class='fila1'>Unidad de destino:</td>
						<td class='fila2'>
							<input type='hidden' id='ccoCiclos24' name='ccoCiclos24' value='".$ccoConCiclos24."'>
							<select id='wcco' name='wcco' size='1' onchange='envioFormulario(this);'>
								<option value='' selected>&nbsp</option>
								<option>%-Todos</option>";
								foreach ($centrosCostosDestino as $centroCostos)
								{
									echo "<option ".( in_array( $centroCostos->codigo, $serviciosDomiciliarios ) ? 'dom="on"' : '' )." value='".$centroCostos->codigo."-".$centroCostos->nombre."'>".$centroCostos->codigo."-".$centroCostos->nombre."</option>";
								}
		echo				"</select>
						</td>
					</tr>
				</table>
			</div>";
	  
	  
	  // //Seleccionar RONDA
	  // echo "<center><table>";
      // echo "<tr class=fila1><td align=center><font size=20>Seleccione Ronda : </font></td></tr>";
	  // echo "</table>";

	  // echo "<center><table>";
	  // echo "<tr><td align=rigth><select name='whora_par_actual' size='1' style=' font-size:50px; font-family:Verdana, Arial, Helvetica, sans-serif; height:50px'>";
	  // echo "<option selected>".$whora_par_actual."</option>";
	  // echo "<option>2</option>";
	  // echo "<option>4</option>";
	  // echo "<option>6</option>";
	  // echo "<option>8</option>";
	  // echo "<option>10</option>";
	  // echo "<option>12</option>";
	  // echo "<option>14</option>";
	  // echo "<option>16</option>";
	  // echo "<option>18</option>";
	  // echo "<option>20</option>";
	  // echo "<option>22</option>";
	  // echo "<option>00</option>";
	  // echo "</select></td>"; 
	  // echo "<td>";
	  // echo "<select name='diaSelecionada' size='1' style=' font-size:50px; font-family:Verdana, Arial, Helvetica, sans-serif; height:50px'>";
	  // echo "<option value='1'>Hoy</option>";
	  // echo "<option value='2'>Mañana</option>";
	  // echo "</select>";
	  // echo "</td>";
	  // echo "</tr>";
	  // echo "</table>";


	  // //=======================================================================================================
	  // //Seleccionar CENTRO DE COSTO Origen
	    // //**************** llamada a la funcion consultaCentrosCostos y dibujarSelect************
	 	// $cco="ccohos = 'off' AND ccofac = 'on' AND ccotra = 'on'";  // filtros para la consulta
		// $sub="off";
		// $tod=" ";
		// $ipod="Origen";
		// //$cco="Todos";
		// $filtro="--";
		// $centrosCostos = consultaCentrosCostos($cco, $filtro="--");  //tipo 3


		// echo "<table align='center' border=0>";
		// $dib=dibujarSelect($centrosCostos, $sub, $tod, $ipod, "wccoo");

		// echo $dib;
		// echo "</table>";

	  // echo "<br><br>";
	  //============================================================================================================

	  // //============================================================================================================
	  // //Seleccionar TIPO DE MEDICAMENTOS a mostrar
	  // echo "<table align='center' border=0>";
	  // echo "<tr class='fila1' style='display: none;' id='encabezadoTipos'><td align=center><font size=20>Seleccione tipo de Medicamento : </font></td></tr></table>";
	  // echo "<table align='center' border=0><tr><td>";
	  // echo "<select id='selectTipos' name='tipoMedicamento' style='display: none; font-size:30px; font-family:Verdana, Arial, Helvetica, sans-serif; height:40px' size='1' >";
	  // echo "<option value='' selected disabled></option>";
	  // echo "<option value='%-Todos'>%-Todos</option>";
	  // echo "<option value='DA-Dosis Adapatada'>DA-Dosis Adapatada</option>";
	  // echo "<option value='NU-Nutricion Parenteral'>NU-Nutricion Parenteral</option>";
	  // echo "<option value='QT-Quimioterapia'>QT-Quimioterapia</option>";
	  // echo "<option value='NE-No Esteril'>NE-No Esteril</option>";
	  // echo "<option value='OTROS-Otros'>Otros</option>";
	  // echo "</select>";
	  // echo "</td>";
	  // echo "</tr>";
	  // echo "</table>";
	  // //=======================================================================================================

	  // //=======================================================================================================
	  // //Seleccionar CENTRO DE COSTO Destino
	    // //**************** llamada a la funcion consultaCentrosCostos y dibujarSelect************
	 	// $cco="ccocpx = 'on' AND ccohos = 'on' OR ccolac = 'on'";  // filtros para la consulta
		// $sub="on";
		// $tod="Todos";
		// $ipod="Destino";  //para que pinte le select mediano
		// //$cco="Todos";
		// $filtro="--";
		// $centrosCostos = consultaCentrosCostos($cco, $filtro);  //tipo 3
		
		// $ccoConCiclos24 = consultarCcoCiclos24();

		// echo "<input type='hidden' id='ccoCiclos24' name='ccoCiclos24' value='".$ccoConCiclos24."'>";
		// echo "<table align='center' border=0>";
		// $dib=dibujarSelect($centrosCostos, $sub, $tod, $ipod);

		// echo $dib;
		// echo "</table>";
	  
		
	  // //=======================================================================================================
	 }
	function consultarTipoMedicamento($codigoArticulo)
	{
		global $wemp_pmla;
		global $conex;
	    $wcenmez = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cenmez');
		$sql  = " SELECT Tiptpr ";
		$sql .= " FROM ".$wcenmez."_000002 A, ".$wcenmez."_000001 B ";
		$sql .= " WHERE Arttip = Tipcod ";
		$sql .= " AND Artcod = '".$codigoArticulo."' ";

		$res = mysql_query($sql,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

		return $res;

	}
  //===========================================================================================================================================
  //*******************************************************************************************************************************************



  //===========================================================================================================================================
  //===========================================================================================================================================
  // P R I N C I P A L
  //===========================================================================================================================================
  //===========================================================================================================================================
  echo "<form name='separacionXRonda' action='MedicamentosXServicioXRonda.php?wemp_pmla=".$wemp_pmla."' method=post>";

  $wfecha = date("Y-m-d");
  
  if( isset( $diaSelecionada ) && $diaSelecionada == '2' ){
	$wfecha = date( "Y-m-d", time()+24*3600 );
	echo "<input type='hidden' name='diaSelecionada' value='".$diaSelecionada."'>";
  }
  $whora  = (string)date("H:i:s");

  
  $unixFechaActual = strtotime( $wfecha." 00:00:00" );


  echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";

  mostrar_empresa($wemp_pmla);

  if (!isset($wcco) or !isset($wccoo))
     {
      elegir_centro_de_costo();
     }
	else
       {
	    echo "<input type='HIDDEN' name='wcco' VALUE='".$wcco."'>";
		echo "<input type='HIDDEN' name='wccoo' VALUE='".$wccoo."'>";
		echo "<input type='HIDDEN' name='whora_par_actual' VALUE='".$whora_par_actual."'>";
		echo "<input type='HIDDEN' name='tipoMedicamento' VALUE='".$tipoMedicamento."'>";

		$arPacientes = Array();	//Array que contiene los pacientes que se muestran
		$arHabitaciones = Array();	//Array que contiene los pacientes que se muestran

	    $wcco1=explode("-",$wcco);
	    if (trim($wcco1[0])=="%")
	       $wcco1[0]="%";
	      else
	         $wcco1[0]=trim($wcco1[0]);

		$wccoo1=explode("-",$wccoo);
	    $wccoo1[0]=trim($wccoo1[0]);

		//Se consulta el centro de costos de Central de mezclas

		$sql = "SELECT Ccocod FROM ".$wbasedato."_000011 
				WHERE Ccotra = 'on'
				AND Ccofac = 'on'
				AND Ccoima = 'on'";
		$respuesta = mysql_query($sql,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$rowcc = mysql_fetch_array($respuesta);
		$ccoCentralMezclas = $rowcc[0];

		if ($whora_par_actual < 13)
           $whora_par_act=$whora_par_actual." AM";
		  else
             $whora_par_act=$whora_par_actual." PM";

		echo '<table align=center>';
		echo "<tr class=seccion1>";
	    echo "<th align=center colspan=2><font size=3>Centro de Costos Origen: </font><b><font size=4>".$wccoo."</b></font></th>";
		echo "</tr>";
	    echo "<tr class=seccion1>";
	    echo "<th align=center colspan=2><font size=3>Centro de Costos Destino: </font><b><font size=4>".$wcco."</b></font></th>";
		echo "</tr><tr class=seccion1>";
		if($wccoo1[0] == $ccoCentralMezclas)
		{
			echo "<th align=center colspan=2><font size=3>Tipo de medicamento: </font><b><font size=4>".$tipoMedicamento."</b></font></th>";
			echo "</tr><tr class=seccion1>";
		}
		$tipMed = explode('-',$tipoMedicamento);
	    // echo "<th align=center><font size=3>Ronda: </font><b><font size=4>".$whora_par_act."</b></font></th>";


		/**************************************************************************************************
	     * Marzo 7 de 2012
	     **************************************************************************************************/
	    $infoTipo = consultarInfoTipoArticulosInc( "N" );


		/************************************************************************************************************
		 * Consulto las rondas a mostrar por si el cco destino seleccionado tiene dispensacion diferente
		 ************************************************************************************************************/
		if( $wccoo1[0] != '%' ){

			$aux = consultarHoraDispensacionPorCcoInc( $conex, $wbasedato, $wcco1[0] );

			if( $aux ){
				$infoTipo[ 'horaCorteDispensacion' ] = $aux/3600;
			}
		}

	    //Esto para sacar cuantas horas de dispensacion son
	    list( $totalRondas ) = explode( ":",$infoTipo[ 'horaCorteDispensacion' ] );


		for( $i = 0; $i < $totalRondas/2; $i++ ){
			if( $i == 0 ){
				$rondasAMostrar = gmdate( "H A", ($whora_par_act+$i*2)*3600 );
			}
			else{
				$rondasAMostrar .= "-".gmdate( "H A", ($whora_par_act+$i*2)*3600 );
			}
		}

		echo "<th align=center><font size=3>Ronda: </font><b><font size=4>".$rondasAMostrar."</b> DE ".( isset( $diaSelecionada ) && $diaSelecionada == '2' ? 'MAÑANA': 'HOY' )."</font></th>";

		// echo "<th align=center><font size=3>Ronda: </font><b><font size=4>".gmdate( "H A", $whora_par_act*3600 )."-".gmdate( "H A",($whora_par_act+2)*3600 )."</b></font></th>";
		/**************************************************************************************************/


		echo "<th align=center><font size=3>Fecha y Hora: <b>".date("Y-m-d H:i:s")."</b></font></th>";
	    echo "</tr>";
	    echo "</table>";
        echo "<br>";
				 

		echo "<center id='txCondiciones'><table class='fondoAmarillo' style='width:720px;'><tr><td colspan=2>";
		echo "<br><center><font size=3><b>Este reporte muestra los medicamentos que cumplan con las siguientes condiciones: </b></font></center><br>";
		echo "<tr><td style='width:50%;'>";
		echo "1. Que tenga algo pendiente por enviar o grabar en las PDA's. <br>"; // para esa ronda. <br>";
		echo "2. Que este aprobado por el regente. <br>";
		echo "3. Que sea para enviar, es decir que la enfermera no haya colocado lo contrario en el Kardex. <br>";
		echo "4. Que el medicamento sea solicitado al servicio origen. <br>";
		echo "5. Que el medicamento sea de un paciente que este hospitalizado en el servicio destino.<br>";
		echo "6. Que el medicamento este programado para la ronda solicitada. <br>";
		echo "7. Se muestran también los articulos de lactario. <br>";
		echo "<tr><td><br><b>Notas:</b><br>";
		echo "                 ** Esta pantalla se actualiza automaticamente cada dos (2) minutos. <br>";
		echo "                 ** Para cambiar de Ronda o Centro de costo debe retornar y seleccionar. <br>";
		echo "                 ** A medida que se va dispensando la ronda, los medicamentos deben ir desapareciendo de este reporte. <br>";
		echo "</td></tr></table></center><br>";

		$gruposControl = consultarAliasPorAplicacion( $conex, $wemp_pmla, "gruposControl" );
		$gruposControl = explode( ",", $gruposControl );
		
		$gruposNoVisibles = consultarAliasPorAplicacion( $conex, $wemp_pmla, "gruposNoVisibles" );
		$gruposNoVisibles = explode( ",", $gruposNoVisibles );
		
		/**************************************************************************
		 * Consulta de articulos generico que no se deben mostrar
		 **************************************************************************/
		 
		$sql = "SELECT Artcod
				  FROM ".$wcenmez."_000001 a, ".$wcenmez."_000002 b, ".$wbasedato."_000068 c
			     WHERE arttip = tipcod
			       AND tiptpr = arktip
				   AND arkcod = artcod 
				   AND tiptpr = 'LQ'
				   ";
		
		$resGen = mysql_query( $sql, $conex ) or die ( "Error: ".mysql_errno()." - en el query: ".$sql." - ".mysql_error() );
		
		$arrGenLQIC = Array();
		while( $rows = mysql_fetch_array( $resGen) ){
			$arrGenLQIC[] = $rows['Artcod'];
		}
		/**************************************************************************/

		
		
		//Traigo el tipo de Movimiento (SF: Servicio Farmaceutico, CM: Central de Mezclas)
		$q = " SELECT ccotim "
			."   FROM ".$wbasedato."_000011 "
			."  WHERE ccocod = '".$wccoo1[0]."'";
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$row = mysql_fetch_array($res);

		$wccotim=$row[0];
		
		// query_articulos_cco($wfecha, &$res, $wcco1[0], $wccoo1[0], &$wccotim);
		// query_articulos_cco($wfecha, &$res, $wcco1[0], $wccoo1[0], &$wccotim,"on");
		$res = query_todos_articulos_cco( $conex, $wbasedato, $wcenmez, $wfecha, $wcco1[0], $wccoo1[0] );
		$num = mysql_num_rows($res);

		$datos = array();		
		
		/**************************************************************************************************
		 * Parte del query que saca el filtro por rondas necesarias
		 **************************************************************************************************/
		//--> centro de costos de urgencias
		$q  = " select Ccocod from {$wbasedato}_000011 where ccourg = 'on' and Ccoest = 'on'";
		$rs = mysql_query( $q, $conex );
		while( $row = mysql_fetch_assoc( $rs ) ){
		  $ccourg = $row['Ccocod'];
		}

		$historiasTrasladoUrgencias = array();
		$query = " SELECT concat( Ubihis,Ubiing ) as hising
				   FROM {$wbasedato}_000018
				  WHERE Ubisan = '{$ccourg}'
					AND Ubiptr = 'on'
					AND Ubiald = 'off'";
		$rs = mysql_query($query);
		while( $row = mysql_fetch_assoc( $rs ) ){
			array_push( $historiasTrasladoUrgencias, $row['hising'] );
		}
		
		
		
		
		$infoTipo = consultarInfoTipoArticulosInc( "N" );

		if( $wcco != '%' ){
			$aux = consultarHoraDispensacionPorCcoInc( $conex, $wbasedato, $wcco1[0] );

			if( $aux ){
				$infoTipo[ 'horaCorteDispensacion' ] = $aux/3600;
			}
		}
		
		$arrTiposAdmitidos = array();
		if($tipMed[0] == 'DA')
		{
			$arrTiposAdmitidos[] = 'DA';
			$arrTiposAdmitidos[] = 'DS';
			$arrTiposAdmitidos[] = 'DD';
		}
		else if($tipMed[0] == 'OTROS')
		{
			$arrTiposAdmitidos[] = 'OTROS';
			$arrTiposAdmitidos[] = 'DA';
			$arrTiposAdmitidos[] = 'DS';
			$arrTiposAdmitidos[] = 'DD';
			$arrTiposAdmitidos[] = 'NU';
			$arrTiposAdmitidos[] = 'NE';
			$arrTiposAdmitidos[] = 'QT';
		}
		else
		{
			$arrTiposAdmitidos[] = $tipMed[0];
		}
		
		$j=1;
		$wclass = "fila2";
		
		for( $i=1; $i<=$num; $i++ )   //Recorre cada uno de los medicamentos
		{
			$row = mysql_fetch_array($res);
			
			if( in_array( $row['kadhis'].$row['kading'], $historiasTrasladoUrgencias ) ){
				continue;
			}
			
			//Si es del grupo no visible no se muestra
			$wgnv=explode("-",$row[12]);
			if( in_array( strtoupper( trim($wgnv[0]) ), $gruposNoVisibles ) ){
				continue;
			}
			
			$rowsPac = informacionPacienteM18( $conex, $wbasedato, $wemp_pmla, $row['kadhis'], $row['kading'] );
			
			//Si es del stock no se muestra
			if( esStock( $conex, $wbasedato, $row[0], $rowsPac['servicioActual'] ) ){
				continue;
			}

			//Si es dosis máxima = 1 no se muestra se ya fue aplicado
			if( !empty( $row['Kaddma'] ) && $row['Kaddma'] == 1 ){
				// consultarTotalAplicacionesEfectivasEnDosisInc( $conex, $wbasedato, $his, $ing, $articulo, $fechorInicial, $fechorFinal, $ido = false, $fechorTraslado = 0 )
				$aplEfectivas = consultarTotalAplicacionesEfectivasEnDosisInc( $conex, $wbasedato, $row['kadhis'], $row['kading'], $row[0], strtotime( $row['kadfin']." ".$row[ 'kadhin' ] ), time()+24*3600, $row['kadido'] );
				if( $aplEfectivas >= $row['Kaddma'] ){
					continue;
				}
			}


			/*************************************************************************
			* Consultar el tipo del medicamento y filtrar si no coincide con los criterios de búsqueda
			*************************************************************************/

			$resTipoMedicamento = consultarTipoMedicamento($row[0]);
			$tipoActual = mysql_fetch_array($resTipoMedicamento);
			
			if($wccoo1[0] == $ccoCentralMezclas)
			{
				if($arrTiposAdmitidos[0] == '%')
				{

				}
				else if($arrTiposAdmitidos[0] == 'OTROS')
				{
					if(in_array($tipoActual[0], $arrTiposAdmitidos))
					{
						continue;
					}
				}
				else
				{
					if(!in_array($tipoActual[0], $arrTiposAdmitidos))
					{
						continue;
					}
				}
			}
			

			/*************************************************************************/

			//Si es articulo genercio de infusion continua o liquido endovenoso no se muestra
			if( !in_array( $row[0], $arrGenLQIC ) ){
				
				$wultRonda  = $row[9];    //Ultima ronda grabada
				$wcondicion = $row[10];   //Condicion de administracion
				$wAN	  	= $row['Contip'] == 'AN' ? 'on': 'off';

				//Esto para sacar cuantas horas de dispensacion son
				list( $totalRondas ) = explode( ":",$infoTipo[ 'horaCorteDispensacion' ] );
				/**************************************************************************************************/

				$unixInicioDispensacion = $unixFechaActual+$whora_par_actual*3600;
				$unixFinDispensacion = $unixFechaActual+($totalRondas+$whora_par_actual)*3600;
				for( $j = $unixInicioDispensacion; $j < $unixFinDispensacion; $j += 2*3600 )
				{
					$fechaRonda = date( "Y-m-d", $j );
					$horaRonda  = date( "H:i:s", $j );
					$perteneceRonda = clRegleta::perteneceRondaADispensarPorArray( $conex, $wbasedato, $fechaRonda, $horaRonda, $row );
					
					if( $perteneceRonda ){
						
						if( !isset( $articulos[ $row['kadart']."-".$row['kadare'] ] ) ){
							
							$wctrl=explode("-",$row[12]);
							$articulos[ $row['kadart']."-".$row['kadare'] ] = array(
								'ubicacion' 		=> trim( $row[5] ),
								'codigoArticulo' 	=> $row[0],
								'nombreArticulo' 	=> $row[1],
								'aprobado' 			=> $row[7],
								'cantidadDosis' 	=> 0,	//$row[2],
								'fraccionDosis' 	=> $row[6],
								'cantidadUnidades' 	=> 0,
								'presentacion' 		=> $row[4],
								'confirmado' 		=> $row[8],
								'noPos' 			=> ( strtoupper( trim($row[11]) ) == "N" ) ? "Si" : "",
								// 'control'			=> ( strtoupper(trim($wctrl[0])) == "CTR" ) ? "Si" : "",
								'control'			=> in_array( strtoupper(trim($wctrl[0]) ), $gruposControl ) ? "Si" : "",
								'pacientes' 		=> array(),
								'rondaMenor' 		=> $j,
							);
						}
						
						$wcond = "";
						if( $row['kadcnd'] != "" ){
							if( $wAN == 'on' ){
								$wcond = $row['condes']." - AN";
							}
						}
						
						// $rowsPac = informacionPacienteM18( $conex, $wbasedato, $wemp_pmla, $row['kadhis'], $row['kading'] );
						
						$habitacion = $rowsPac['habitacionActual'];
						$historia 	= $rowsPac['historia'];
						$ingreso 	= $rowsPac['ingreso'];
						$nombre 	= $rowsPac['primerNombre']." ".$rowsPac['segundoNombre']." ".$rowsPac['primerApellido']." ".$rowsPac['segundoApellido'];
						$rondaMenor = $articulos[ $row['kadart']."-".$row['kadare'] ]['rondaMenor'];
						
						$articulos[ $row['kadart']."-".$row['kadare'] ]['rondaMenor'] = $j < $rondaMenor ? $j : $rondaMenor;
						$articulos[ $row['kadart']."-".$row['kadare'] ]['cantidadDosis'] += $row[2];
						$articulos[ $row['kadart']."-".$row['kadare'] ]['cantidadUnidades'] += ceil( $row['kadcfr']/$row['kadcma'] );
						
						
						
	
						
						$paciente_inf 		= consultarUbicacionPaciente( $conex, $wbasedato, $historia, $ingreso);
						$esCcoDomiciliario 	= esCcoDomiciliarioMSR( $conex, $wbasedato, $paciente_inf->servicioActual );
						$serDom 			= $esCcoDomiciliario ? '&servicioDomiciliario=on' : '' ;
						
						
						
						
						if( !isset( $articulos[ $row['kadart']."-".$row['kadare'] ]['pacientes'][$historia] ) ){
						
							$articulos[ $row['kadart']."-".$row['kadare'] ]['pacientes'][$historia] = array(
								'ronda' 		=> date( "H" ,$articulos[ $row['kadart']."-".$row['kadare'] ]['rondaMenor'] ),
								'historia' 		=> $historia,
								'ingreso' 		=> $ingreso,
								'habitacion' 	=> $habitacion,
								'nombre' 		=> $nombre,
								'dosis' 		=> $row['kadcfr']." ".$row['kadufr'],
								'dosisFraccion' => $row['kadcfr'],
								'condicion' 	=> $wcond,
								'accion' 		=> "'../procesos/perfilFarmacoterapeutico.php?wemp_pmla=".$wemp_pmla."&waccion=a&whistoria=".$rowsPac[0]."&wfecha=".$wfecha.$serDom."'",
								'fecharonda' 	=> date( "Y-m-d" ,$articulos[ $row['kadart']."-".$row['kadare'] ]['rondaMenor'] ),
							);
						}
						else{
							$dFrac = $articulos[ $row['kadart']."-".$row['kadare'] ]['pacientes'][$historia]['dosisFraccion'] += $row['kadcfr'];
							$articulos[ $row['kadart']."-".$row['kadare'] ]['pacientes'][$historia]['dosis'] = $dFrac." ".$row['kadufr'];
						}
						
						
						
						// Creo Array de pacientes para mostrar al terminar el reporte
						$arPacientes[ $habitacion ]['Historia'] 	= $historia;
						$arPacientes[ $habitacion ]['Ingreso'] 		= $ingreso;
						$arPacientes[ $habitacion ]['Nombre'] 		= $nombre;
						$arPacientes[ $habitacion ]['Habitacion'] 	= $habitacion;
						$nom_medica= $row[0]."-".$row[1]."|";
						@$ya_existe=strpos($arPacientes[ $habitacion ]['Medicamento'], $nom_medica);
						if( $ya_existe === false )
						{
							@$arPacientes[ $habitacion ]['Medicamento'] = $arPacientes[ $habitacion ]['Medicamento'].$nom_medica;
							@$arPacientes[ $habitacion ]['ronda'] .= date( "H", $j )."|";
							@$arPacientes[ $habitacion ]['NumMed']		= $arPacientes[ $habitacion ]['NumMed']+1;
						}
						$arHabitaciones[ $habitacion ] = $habitacion;
						
						
						// var_dump( $articulos[ $row['kadart']."-".$row['kadare'] ]['pacientes'] );
					}
					
					//Si es a necesidad no se verifica la ronda siguiente por que ya pertence a esa ronda
					if( $perteneceRonda && $wAN == 'on' ){
						break;
					}
				}
			}
		}

		$datos = array();
		if(count($articulos)>0)
		{
			foreach( $articulos as $key => $articulo ){
				$datos[ date( "H", $articulo['rondaMenor'] ) ][] = $articulo;
			}
		}
		
		
		pintarAritculos( $datos );

		/////////////////////////////////////////////////////////////////////////////////////////////
		//MUESTRO TODOS LOS PACIENTES DE LA RONDA ESPECIFICADA
		/////////////////////////////////////////////////////////////////////////////////////////////
		// query_pacientes_cco($wfecha, &$res, $wcco1[0], $wccoo1[0], &$wccotim);
		// $num = mysql_num_rows($res);

		$num = count( $arPacientes );
		echo "<br><center><input type='button' id='exportar' onclick='Exportar()' value='Exportar a Excel'></center>";
		echo "<br>";
		echo "<center><table id='tablaExcel'>";
		echo "<tr class=fila1>";
		echo "<th colspan=7><font size=4>PACIENTES CON MEDICAMENTOS EN ESTA RONDA</font></th>";
		echo "</tr>";
		echo "<tr class=seccion1 style='display: none;'>";
	    echo "<th align=center colspan=7><font size=3>Centro de Costos Origen: </font><b><font size=4>".$wccoo."</b></font></th>";
		echo "</tr>";
	    echo "<tr class=seccion1 style='display: none;'>";
	    echo "<th align=center colspan=7><font size=3>Centro de Costos Destino: </font><b><font size=4>".$wcco."</b></font></th>";
		echo "</tr>";
		if($wccoo1[0] == $ccoCentralMezclas)
		{
			echo "<tr style='display: none;'><th align=center colspan=7><font size=3>Tipo de medicamento: </font><b><font size=4>".$tipoMedicamento."</b></font></th>";
			echo "</tr>";
		}
		echo "<tr class=encabezadoTabla>";
		echo "<th><font size=4>Habitacion</font></th>";
		echo "<th><font size=4>Historia</font></th>";
		echo "<th><font size=4>Ingreso</font></th>";
		echo "<th colspan=2><font size=4>Paciente</font></th>";
		echo "<th><font size=3>Medicamentos<br>Pendientes</font></th>";
		echo "<th><font size=4>Ronda</font></th>";
		echo "</tr>";

		

		$j=1;
		$wclass="fila2";

		sort( $arHabitaciones );

		foreach( $arHabitaciones as $keyHabitaciones => $valueHabitaciones ){

			if ($wclass=="fila1")
			   $wclass="fila2";
	          else
	             $wclass="fila1";

            $q = " SELECT COUNT(*) "
			    ."   FROM ".$wbasedato."_000018 "
				."  WHERE ubihis = '".$arPacientes[ $valueHabitaciones ]['Historia']."' "
				."    AND ubiing = '".$arPacientes[ $valueHabitaciones ]['Ingreso']."'"
				."    AND ubialp = 'on' ";
			$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$row1 = mysql_fetch_array($res1);
			if ($row1[0] > 0)
               $wclass = "fondoAmarillo";

			
		   
			$NumMedi=$arPacientes[ $valueHabitaciones ]['NumMed'];
			$medicamArray = explode("|",$arPacientes[ $valueHabitaciones ]['Medicamento']);
			// $medicam = explode("|",$arPacientes[ $valueHabitaciones ]['Medicamento']);
			$rond = explode("|",$arPacientes[ $valueHabitaciones ]['ronda']);
			
			asort($rond);
			
			$medicam = array();
			$medicamRonda = array();
			foreach($rond as $key => $value)
			{
				if($value!="")
				{
					$medicam[]= $medicamArray[$key];
					$medicamRonda[]= $rond[$key];
				}
				
			}
			
			if ($NumMedi>1)
			{
				echo "<tr class=".$wclass.">";
				echo "<td align=center rowspan='".$NumMedi."' >".$arPacientes[ $valueHabitaciones ]['Habitacion']."</td>"; 	    //Habitacion
				echo "<td align=center rowspan='".$NumMedi."'>".$arPacientes[ $valueHabitaciones ]['Historia']."</td>";  		//Historia
				echo "<td align=center rowspan='".$NumMedi."'>".$arPacientes[ $valueHabitaciones ]['Ingreso']."</td>";  		//Ingreso
				echo "<td align=left rowspan='".$NumMedi."'>".$arPacientes[ $valueHabitaciones ]['Nombre']."</td>";  			//Paciente
				if ($row1[0] > 0)
					echo "<td align=left rowspan='".$NumMedi."'><blink id=blink>Paciente de Alta</blink></td>";  			//Paciente
				else
					  echo "<td rowspan='".$NumMedi."'> </td>";
				echo "</td>";
				// echo "<td align=left>".$medicam[0]."</td></tr>";      //Medicamentos Pendientes
				echo "<td align=left>".$medicam[0]."</td><td align=center style='font-size:12pt;font-weight:bold;'>".$medicamRonda[0]."</td></tr>";      //Medicamentos Pendientes
					for($i=1; $i<$NumMedi; $i++)
					{
						// echo "<tr class=".$wclass."><td align=left>".$medicam[$i]."</td></tr>"; 	//Medicamentos Pendientes
						echo "<tr class=".$wclass."><td align=left>".$medicam[$i]."</td><td align=center style='font-size:12pt;font-weight:bold;'>".$medicamRonda[$i]."</td></tr>"; 	//Medicamentos Pendientes
					}
				echo "</tr>";
			}

			else
			{
				echo "<tr class=".$wclass.">";
				echo "<td align=center>".$arPacientes[ $valueHabitaciones ]['Habitacion']."</td>"; 	    //Habitacion
				echo "<td align=center>".$arPacientes[ $valueHabitaciones ]['Historia']."</td>";  		//Historia
				echo "<td align=center>".$arPacientes[ $valueHabitaciones ]['Ingreso']."</td>";  		//Ingreso
				echo "<td align=left>".$arPacientes[ $valueHabitaciones ]['Nombre']."</td>";  			//Paciente
				if ($row1[0] > 0)
					echo "<td align=left><blink id=blink>Paciente de Alta</blink></td>";  			//Paciente
				   else
					  echo "<td> </td>";
				echo "</td>";
					for($i=0; $i<$NumMedi; $i++)
					{
						echo "<td align=left>".$medicam[$i]."</td>";      //Medicamentos Pendientes
						echo "<td align=center style='font-size:12pt;font-weight:bold;'>".$medicamRonda[$i]."</td>";      //Medicamentos Pendientes
					}
						
				echo "<tr>";
			}
		}
		echo "</table>";
		/////////////////////////////////////////////////////////////////////////////////////////////

		echo "<br><br>";
		echo "<table>";
		echo "<tr><td><A HREF='MedicamentosXServicioXRonda.php?wemp_pmla=".$wemp_pmla."&user=".$user."' class=tipo4V>Retornar</A></td></tr>";
		echo "</table>";

		echo "<meta http-equiv='refresh' content='120;url=MedicamentosXServicioXRonda.php?wemp_pmla=".$wemp_pmla."&wuser=".$user."&wcco=".$wcco."&wccoo=".$wccoo."&whora_par_actual=".$whora_par_actual."&diaSelecionada=".$diaSelecionada."&tipoMedicamento=".$tipoMedicamento."'>";
		
	   }

  echo "<br><br><br>";
  echo "<table align=center>";
  echo "<tr><td align=center colspan=4><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
  echo "</table>";
} // if de register

?>
</body>
