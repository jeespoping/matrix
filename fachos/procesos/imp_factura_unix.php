<?php
include_once("conex.php");
session_start();

/*if (!isset($user))
{
    if(!isset($_SESSION['user']))
        session_register("user");
}
*/
if(!isset($_SESSION['user']) )
{
     echo "<br /><br /><br /><br />
              <div style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;' id='div_sesion_muerta'>
                  [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
             </div>";
      return;
}



include_once("root/comun.php");
include_once("root/montoescrito.php");
$wemp_pmla=$_REQUEST['wemp_pmla'];
$whce = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");

//@$conexunix = odbc_pconnect('informix','informix','sco') or die("No se ralizo Conexion con el Unix"); //2012-02-29
@$conexunix = odbc_connect('facturacion','informix','sco') or die("No se ralizo Conexion con el Unix");

$wfacturacion = "";
mostrar_empresa($wemp_pmla);
$array_meses = array( "1"=>"ENERO","2"=>"FEBRERO","3"=>"MARZO","4"=>"ABRIL","5"=>"MAYO","6"=>"JUNIO"
                      ,"7"=>"JULIO","8"=>"AGOSTO","9"=>"SEPTIEMPRE","10"=>"OCTUBRE","11"=>"NOVIEMBRE","12"=>"DICIEMBRE");

if( $consultaAjax == "on" ){

	//Verónica Arismendy
	if(isset($accion) & $accion == "obtenerFechasFactura" ){

		// Se limpia la fuente de factura de caracteres especiales que puedan romper el query por este campo
        $wffa = str_replace('"','',str_replace("'","",trim($valWffa)));
        $wffa = str_replace('\\','',str_replace("/","",$valWffa));

		$q = " SELECT  carfec, carfev, carhis, carnum, carcco "
        ."   FROM cacar, inemp "
        ."  WHERE carfue = '".$wffa."' "
        ."  AND cardoc = '".$numFactura."' "
        ."  AND carced = empcod "
        ."  AND caranu = '0' ";

		$res = odbc_do($conexunix,$q);

		while( odbc_fetch_row($res)) {

			$wfec = odbc_result($res,1);    //Fecha
			$wfev = odbc_result($res,2);    //Fecha de vencimiento
			$whis = odbc_result($res,3);    //Historia clinica
			$wing = odbc_result($res,4);    //Ingreso
			$wcco = odbc_result($res,5);    //centro de costos
		}

		//2016-04-05
		//Se consulta si para el centro de costo de la factura actual se tiene configurado que tome la fecha de ingreso y salida de la última evolución, si es así
		//se listan las posibles fechas para que el usuario que vaya a realizar la impresión seleccione cual fecha debe aparecer.
		$arrFechaIngreso = validarFechasCentroCosto($wcco, $whis, $wing, $wfec);
		$htmlReturn = "";
		if(count($arrFechaIngreso) >= 1){
			$htmlReturn = "<table align='center'>";
			$htmlReturn .= "<tr class='titulo'>";
			$htmlReturn .= "<td colspan='4' style='font-size:12px'> Seleccione la fecha a mostrar en la factura para Fecha de ingreso y Fecha de salida";
			$htmlReturn .= "</tr>";

			$htmlReturn .= "<tr class='encabezadoTabla'>";
			$htmlReturn .= "<td></td>";
			$htmlReturn .= "<td>Fecha evolucion</td>";
			$htmlReturn .= "<td>Formulario</td>";
			$htmlReturn .= "<td>Nombre</td>";
			$htmlReturn .= "</tr>";
			$fila = "fila1";
			foreach($arrFechaIngreso as $key => $value){

				$htmlReturn .= "<tr class='".$fila."'>";
				$htmlReturn .= "<td><input type='radio' value='".$value["fecha"]."' name='fecha_ingreso_evolucion' onclick='habilitarBotonImprimir()'></td>";
				$htmlReturn .= "<td>".$value["fecha"]."</td>";
				$htmlReturn .= "<td>".$value["formulario"]."</td>";
				$htmlReturn .= "<td>".utf8_encode($value["nombre"])."</td>";
				$htmlReturn .= "</tr>";

				$fila = $fila === "fila1" ? "fila2" : "fila1";
			}
			$htmlReturn .= "</table>";

			echo json_encode(array("tipo" => "ok", "respuesta" => $htmlReturn));
			exit();
		}else{
			echo json_encode(array("tipo" => "error", "respuesta" =>""));
			exit();
		}

	}

    if(isset($accion) & $accion == "consultarPlanes" ){
		$wcodigoEmpresa = "";
		$menuPlanes     = "";
		$q = " SELECT carano, carmes, carfec, carfev, carcep, carpac, carced, carres, carval, carhis, carnum "
		  ."   FROM cacar "
		  ."  WHERE carfue = '$wfue' "
		  ."    AND cardoc = '$wfactura'"
		  ."    AND caranu = '0' ";
		$res = odbc_do($conexunix,$q);

		while( odbc_fetch_row($res) ){
			$wcodigoEmpresa = odbc_result($res,7); //Nro de documento
		}

		if( $wcodigoEmpresa != "" ){
			$query = " SELECT Placod, Plades, Pladet
					 FROM {$wfacturacion}_000023
					WHERE Plaent = '{$wcodigoEmpresa}'
					  AND Plaest = 'on'";
			$rs    = mysql_query( $query, $conex ) or die( mysql_error()."    ----    <br>".$query);
			$rsnum = mysql_num_rows( $rs );
			if( $rsnum ){
				$menuPlanes .= "<table id='tbl_planes_responsable_factura'>";
				$menuPlanes .= "<tr class='encabezadoTabla'>";
				$menuPlanes .= "<td>&nbsp;Sel.&nbsp;</td>";
				$menuPlanes .= "<td>Descripci&oacute;n</td>";
				$menuPlanes .= "</tr>";
				while ( $rowpl = mysql_fetch_assoc($rs) ) {
					$menuPlanes .= "<tr class='fila1'>";
					$menuPlanes .= "<td align='center'><input type='radio' name='radio_plan' value='{$rowpl['Placod']}' onclick='seleccionarPlan( this );'></td>";
					$menuPlanes .= "<td imprimirDetallePaquete='{$rowpl['Pladet']}'>{$rowpl['Placod']}-{$rowpl['Plades']}</td>";
					$menuPlanes .= "</tr>";
				}
				$menuPlanes .= "</table><br>";
				$menuPlanes .= "<table><tr><td><span class='subtituloPagina2'>Mes correspondiente</span></td><td><span class='subtituloPagina2'>A&ntilde;o correspondiente</span></td></tr><br>";
				$menuPlanes .= "<tr><td align='center'><select id='select_mes_plan' onchange='seleccionarMes( this )'>";
				$mes_hoy = date('m');
				// for ( $i = 1; $i<= $mes_hoy; $i++) {
				for ( $i = 1; $i<= 12; $i++) {
					$selected = ( $i == $mes_hoy ) ? "selected" : "";
					$menuPlanes .= "<option value='$i' $selected>&nbsp;{$array_meses[$i]}&nbsp;</option>";
				}
				$menuPlanes .= "</select><br></td>";
				$ano_hoy = date('Y');
				$menuPlanes .= "<td align='center'>
								<select id='select_ano_plan' onchange='seleccionarAno( this )'>";


				for ($k=(($ano_hoy*1) - 3) ; $k <= $ano_hoy ; $k++)
				{
					$selected = ( $k == $ano_hoy) ? "selected" : "";
					$menuPlanes .=	"<option value='$k' $selected>".$k."</option>";
				}
				$menuPlanes .=	"</select></td></tr></table>";
			}
		}
		echo $menuPlanes;
		return;
	}

}


?>
<html>
<head>
  <title>IMPRIMIR FACTURA UNIX</title>
  <style type="text/css">
  .monoespaciado{
    font-family: 'Courier New';
    font-weight: 600;
    letter-spacing:2px
     }
   .monoespaciado td{
    overflow: hidden;;
  }
</style>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script type="text/javascript">
    function enter(e)
    {
        var esIE=(document.all);
        var esNS=(document.layers);
        var tecla=(esIE) ? event.keyCode : e.which;
        if (tecla==13) return true;
        else return false;
    }

    function submit_form()
    {
        document.forms.impfacunix.submit();
    }

    // Se crea esta función javascript para controlar los radio button
    // muchas veces se desea imprimir la factura sin seleccionar ninguna opción
    // si antes se seleccionó una opción se puede desmarcar nuevamente dandole clic.
    var era;
    var previo=null;
    function desSeleccionar(rbutton)
    {
        if(previo && previo != rbutton)
            { previo.era = false; }
        if(rbutton.checked == true && rbutton.era == true)
            { rbutton.checked = false;}
        rbutton.era=rbutton.checked;
        previo=rbutton;
    }

    function cambiarTamaCss3(){
      document.body.style.width = '190mm';
      document.body.style.height = '185mm';
    }
    function regresar(){
       var wemp_pmla = $("#wemp_pmla").val();
       window.location = "imp_factura_unix.php?wemp_pmla="+wemp_pmla+"&wparam=1";
    }

    function cerrarPagina(){
      window.close();
    }

    function validarEmpresaPlan( obj ){

       if( $.trim( $(obj).val() ) != "" ){

          $.ajax({
            url: 'imp_factura_unix.php',
            type: "POST",
            data: {
               consultaAjax: "on",
                  wemp_pmla: $("#wemp_pmla").val(),
                     accion: "consultarPlanes",
                   wfactura: $(obj).val(),
                       wfue: $("#wffa").val()

                  },
            success: function(data) {
                       if( data != "" ){
                          $("#div_planes").html(data);
                          $("#mes_plan").val( $("#select_mes_plan").val() );
                          $("#ano_plan").val( $("#select_ano_plan").val() );
                       }else{
                          $("#imprimir").show();
                          $("#div_planes").html("");
                          $("#mes_plan").val( "" );
                          $("#ano_plan").val( "" );
                          $("#nombre_plan").val( "" );
                          $("#imprimirDetallePaquete").val( "" );
                       }
                    },
            async:false
          });
    }else{
        return;
       }
    }

    function seleccionarPlan( obj ){
      $("#nombre_plan").val( $(obj).parent().next("td").html() )
      $("#imprimirDetallePaquete").val( $(obj).parent().next("td").attr("imprimirDetallePaquete") )
      $("#imprimir").show();
    }

    function seleccionarMes( obj ){
      $("#mes_plan").val( $(obj).find("option:selected").val() );
    }

	function seleccionarAno( obj ){
      $("#ano_plan").val( $(obj).find("option:selected").val() );
    }

    function generarFactura(){

      //console.log( $("#select_mes_plan") );
      var validoEmpresa = false;
      if( $("#div_planes").html() == "" ){
        validoEmpresa = true;
        validarEmpresaPlan( $("#wfactura") );
      }
      if( validoEmpresa &&  $("#div_planes").html() != "" ){
        alert( "Esta entidad tiene paquetes de facturación especiales" );
      }else{
        document.getElementById("impfacunix").submit();
      }
    }

    function limpiarDivPlanes(isChequeo){
      $("#div_planes").html("");
      $("#mes_plan").val( "" );
      $("#ano_plan").val( "" );
      $("#nombre_plan").val( "" );
      $("#imprimirDetallePaquete").val( "" );

	  //2016-04-05 Verónica Arismendy
	  //Limpia el radio de tipo de factura y limpiar el contenido de la tabla para seleccionar la fecha a imprimir en caso de que aplique
	  if(isChequeo != 1){
		$("input:radio[name=wnopos]").attr("checked", false)
		$("#fechasEvolucion").html("");
		$("#imprimir").attr("disabled", true);
	  }
    }


	//Verónica Arismendy
	function habilitarBotonImprimir(){
		$("#imprimir").attr("disabled", false);
	}

	$(document).ready(function(){
		$("input:radio[name=wnopos]").click(function() {
			var valWparam = $('#wparam').val();
			var wnopos = $(this).val();

			//wparam debe ser igual a uno de lo contrario la factura no seria la detallada y wnopos debe ser diferente de factura pav
			if(valWparam == 1 && wnopos != 3){
				$("#imprimir").attr("disabled", true);

				//Se consulta por ajax si el centro de costos de la factura está configurado para seleccinoar la fecha a imprimir en fecha de ingreso y fecha salida
				$.post("imp_factura_unix.php",
				{
					consultaAjax:   		'on',
					accion:         		'obtenerFechasFactura',
					numFactura:          	$('#wfactura').val(),
					valWnopos:          	wnopos,
					valWffa:          		$('#wffa').val(),
					wemp_pmla:          	$('#wemp_pmla').val(),
					}, function(respuesta){
					var objRespuesta = $.parseJSON(respuesta);

					if(objRespuesta.tipo != "error"){
						//Se añade al formulario la parte de seleccionar la fecha a imprimir
						if(objRespuesta.respuesta != "" && objRespuesta.respuesta != null){
							$("#fechasEvolucion").html(objRespuesta.respuesta);
						}else{
							$("#imprimir").attr("disabled", false);
						}
					}else{
						$("#imprimir").attr("disabled", false);
					}
				});
			}else{
				$("#fechasEvolucion").html("");
				$("#imprimir").attr("disabled", false);
			}
		})
	});
</script>
</head>
<body>
<?php
/***************************************************
*            IMPRIMIR FACTURA DE UNIX             *
*            CONEX, FREE => OK                    *
***************************************************/

//==================================================================================================================================
//PROGRAMA                   : imp_factura_unix.php
//AUTOR                      : Juan Carlos Hernández M.
//FECHA CREACION             : Agosto 23 de 2011
//FECHA ULTIMA ACTUALIZACION :

 //DESCRIPCION
//==================================================================================================================================
//Este programa se hace para imprimir las facturas de Unix que se requieran imprimir con aspecto diferente al que sale desde Unix
//==================================================================================================================================

//==================================================================================================================================
//MODIFICACIONES ===================================================================================================================
//  2016-09-22 ( Camilo Zapata) Modificacion que agrega la  obersarvacion en las impresiones paf de sura, y salud total--> funcion imprimir_factura()
//  2016-08-23 ( Camilo Zapata) Se modifica para que el nombre de la empresa responsable tenga mas caracteres, para aquellas que tienen nombres muy largos
//==================================================================================================================================
//	2016-04-20 ( Verónica Arismendy) Se modifica para que el nombre de la empresa responsable de la factura se tome de inempdet(unix)
//==================================================================================================================================
//	2016-03-14 ( Verónica Arismendy) Se modifica para que en el parametro ccoFechaIngresoPorEvolucion se puedan incluir uno o varios
//									 formularios a tener en cuenta para la fecha de ingreso de los centros de costos configurados.
//==================================================================================================================================
/* 2016-02-26( Verónica Arismendy ): Se crea parametro en la 51 y se verifica el centro de costo de la factura comparado con los que estén guardados en el parámetro
									   si se encuentra ahí se debe tomar la fecha de ingreso desde el formulario de evolución tomando la máxima fecha.
									   Se valida que la fecha de evolución no sea mayor que la fecha de la factura de ser así se toma la fecha de ingreso que tenía en unix.
==================================================================================================================================
    2015-12-04( Camilo Zapata ):  se implementa la impresión del detalle del concepto 2105 en la impresión por paquetes, buscar en caso de ser necesario $imprimirDetallePaquete
====================================================================================================================================================================================================================================================================
    2015-09-04( Camilo Zapata ):  se implementó la función construir queryunix en la función "imprimir_factura", para que se puedan imprimir
                                  de manera satisfactoria las facturas multiusuarios con paquetes de sura.
==================================================================================================================================
    2014-06-05( Edwar Jaramillo ):  Se crea la variable entidadNoDiscriminaTerceros asociada a un parámetro en root_51, esto es para que a la
                                    entidad configurada en ese parámetro no le discrimine los valores por clinica y por tercero sino que en
                                    clínica sume ambos valores de una vez.
==================================================================================================================================
    2014-04-22( Camilo Zapata ):    se modificó el script para que las facturas de pacientes pertenecientes a fisiatría tengan, la
                                    fecha de salida del último registro en la tabla de formularios firmados( hce_000036 )
                                    de hce, en lugar de la fecha de
                                    egreso registrada en unix.
==================================================================================================================================
==================================================================================================================================
    2013-09-11( Camilo Zapata ):    se rehabilitaron las observaciones y se retiró el mensaje de la resolución de la Dian
==================================================================================================================================
==================================================================================================================================
    2013-09-10( Camilo Zapata ):    Se agregó el tipo de documento de identidad del paciente en el area de información del paciente."
==================================================================================================================================
==================================================================================================================================
    2013-04-10( Edwar Jaramillo ):  Se modifió el área de Observaciones temporalmente para mostrar en esa área el mensaje
                                    "RESOLUCION DIAN N. 110000525435 FECHA 2013/04/02 NUMERACION DEL 3825687 AL 5000000."
==================================================================================================================================
==================================================================================================================================
    2013-02-04( Mario Cadavid ):    Se adicionó la validación de la variable $whis en la función imprimir_factura_detalle de modo que si viene la historia
									en cero o vacío no haga las consultas que usan esta variable ya que se arrojaría como resultado campos nulos
==================================================================================================================================
    2012-11-19(Edwar Jaramillo):    Se realizan correcciones a los calculos cuando son facturas PAF, se estaba restando dos veces el valor del concepto 2105
                                    Adicionalmente de actualiza la función montoescrito() puesto que al tratar de imprimir un número mayor a nueve cifras se mostraba un
                                    mensaje informando que no se podía el texto para esa cifra.
==================================================================================================================================
    2012-11-16(Edwar Jaramillo):    Se adiciona una nueva opción de impresión de factura, se denomina facturas PAF,
                                    Para esto se insertó una nueva opción en fachos_00001 con el código "PAF-2105" donde "2105" en este caso corresponde
                                    al código del concepto que no debe ser sumado junto con el resto de conceptos de la factura pero que se debe mostrar
                                    en el subtotal en una fila adicional, también se crea un campo en el formulario para pedir la fuente de la factura.

                                    En la función imprimir_factura_detalle se cambia el primer sql que aparece, solo cuanso es el tipo PAF, esto se hace porque
                                    al seleccionar este tipo se encontró con que la consulta de unix retornaba valores nulos que dañaban el programa al ejecutarlo.
==================================================================================================================================
    2012-08-29(Viviana Rodas):      Se modifico para cuando el paciente sea de ayudas diagnosticas no muestre fecha de salida.
==================================================================================================================================
    2012-08-28(Viviana Rodas):      Se modifico el valor que imprime el montoescrito por el total neto.
                                    El limite de conceptos se cambio de 13 a 10 para evitar que se bajen los otros valores cuando la factura tenga
                                       muchos conceptos.
                                    Se agrego sum(antfacval) en la consulta a la tabla anantfac para que sume los abonos.
                                    Se agrego para las observaciones un count para saber cuantas lineas trae y asi hacer las consultas correspondientes.
==================================================================================================================================
    2012-06-23   :   Se creó la variable $wfecegr que permite mostrar la fecha de salida el paciente
==================================================================================================================================
    2012-05-15   :  Se crea función javascript para desmarcar las opciones de 'NO POS', esto porque varias veces se puede elegir imprimir sin
                    marcar ninguna opción.
==================================================================================================================================
    2012-05-09  :   Se adicionó una nueva opción al momento de imprimir la factura, ahora se puede seleccionar entre generar la factura
                    con conceptos NO POS, o generar la factura NO POS para cirugía con el concepto 'PROCEDIMIENTOS NO POS'.
                    esto permite que al seleccionar la opción 'NO POS (Cirugía)' se mostrará un solo concepto en la factura con las cifras totalizadas
==================================================================================================================================
    2012-04-03 -    Se adicionó la opción de impresion de facturas NO POS y la sleccion de impresora, de modo que según la impresora se
                    definen los margenes superior e izquierdo de impresion, para esto se crearon las tablas del grupo de facturacion hospitalaria:
                    fachos_000001 -  Maestro de conceptos NO POS, si en el formulario se seleccionó NO POS y se encuentra el código del concepto en esta
                                     tabla se toma la descripcion de esta tabla y no la UNIX
                    fachos_000002 -  Movimiento impresion de facturas, para grabar la auditoria de las impresiones de facturación
                    fachos_000003 -  Configuracion impresoras facturacion, determina que margen superior e izquierda se debe dejar según la impresora seleccionada
==================================================================================================================================
    2012-03-14 -    Se creo la función 'imprimir_factura_detalle' que permite imprimir la factura con todos los conceptos de ésta,
                    con el valor cargado a la clínica y el valor cargado a terceros, además de las observaciones y el log del pie de página
==================================================================================================================================
*/

  //Se realiza la configuracion para el archivo pdf
  function crearPDF(){
    global $pdf;

    require_once('root/tcpdf/config/lang/spa.php');
    require_once('root/tcpdf/tcpdf.php');

    $nombre_logo = "clinica";
    $datos_paciente = "datos";

    define("PDF_HEADER_LOGO_MX", "medical/root/".$nombre_logo.".jpg"); // Imagen del logo.
    define("PDF_HEADER_LOGO_WIDTH_MX",3); // tamaño ancho en mm de la imagen del logo.
    define("PDF_HEADER_TITLE_MX",$datos_paciente); // Título 1 en encabezado.
    define("PDF_HEADER_STRING_MX","Laboratorio Tel: 3421010 ext. 1132"); // Texto 2 en encabezado.

    define("PDF_MARGIN_TOP_MX",16);
    define("PDF_MARGIN_LEFT_MX",3);
    define("PDF_MARGIN_RIGHT_MX",2);
    define("PDF_MARGIN_BOTTOM_MX",0);

    define("PDF_PAGE_ORIENTATIONMX", "L");
    define("PDF_UNITMX", "mm");
    $format = array(190, 185);
    // create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATIONMX, PDF_UNITMX, $format, true, 'UTF-8', false);

    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('PMLA');
    $pdf->SetTitle('Resultado');
    $pdf->SetSubject('Tarjeta Dietas');
    $pdf->SetKeywords('PMLA, PDF, resultado, clínica, dietas');


    // set default header data
    $pdf->SetHeaderData(PDF_HEADER_LOGO_MX, PDF_HEADER_LOGO_WIDTH_MX, PDF_HEADER_TITLE_MX, PDF_HEADER_STRING_MX);

    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    //set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT_MX, PDF_MARGIN_TOP_MX, PDF_MARGIN_RIGHT_MX);
    $pdf->SetHeaderMargin(0);
    $pdf->SetFooterMargin(0);

    //set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM_MX);

    //set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    //set some language-dependent strings
    $pdf->setLanguageArray($l);

    // set font
    $pdf->SetFont('CourierB', '', 8);
  }

  //Funcion que agrega una nueva pagina al PDF
  function agregarPaginaPDF(){
    global $pdf;

    $pdf->SetAutoPageBreak(false, 0);
    // add a page
    $pdf->AddPage();

    $pdf->lastPage();
  }

  //Funcion que determina la posicion donde debe ir ubicada la tarjeta
  function agregarFacturaPdf( $contenido_pdf ){
    global $pdf;

    $contenido_pdf = str_replace("'",'"',$contenido_pdf);
    $html = str_replace("\\", "", $contenido_pdf);
    $pdf->writeHTML($html, true, false, true, false, '');
  }

  //Se crea el PDF en la carpeta resultados
  function imprimirPDF( $nombrePdf ){
    global $pdf;

      $dir = 'facturas';

      if(is_dir($dir)){ }
      else { mkdir($dir,0777); }
        $archivo_dir = $dir."/".$nombrePdf.".pdf";
      if(file_exists($archivo_dir)){
        unlink($archivo_dir);
    }

    $pdf->Output($archivo_dir, 'F');
  }

  function auditoria( $wfac, $wdid,$whis,$wing, $wfec, $wval, $wpla, $wparam )
    {

      global $conex;
      global $wchequeo;
      global $wfacturacion;
      global $user;

      list( $a, $usuario ) = explode( "-", $user );

      $fecha = date( "Y-m-d" );
      $hora = date( "H:i:s" );

      if($wparam!="1")
        $q = "INSERT INTO ".$wchequeo."_000002 (      Medico   , Fecha_data , Hora_data ,   Impfac  ,  Impdid   ,   Imphis  ,  Imping   ,  Impfec   ,    Impusu    ,  Impval   ,  Imppla   ,   Seguridad    ) "
          ."                           VALUES('".$wchequeo."','".$fecha."','".$hora."','".$wfac."','".$wdid."','".$whis."','".$wing."','".$wfec."','".$usuario."','".$wval."','".$wpla."','C-".$usuario."') ";
      else
        $q = "INSERT INTO ".$wfacturacion."_000002 (      Medico   , Fecha_data , Hora_data ,   Impfac  ,  Impdid   ,   Imphis  ,  Imping   ,  Impfec   ,    Impusu    ,  Impval   ,  Imppla   ,   Seguridad    ) "
          ."                           VALUES('".$wfacturacion."','".$fecha."','".$hora."','".$wfac."','".$wdid."','".$whis."','".$wing."','".$wfec."','".$usuario."','".$wval."','".$wpla."','C-".$usuario."') ";

      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

    }

  function mostrar_empresa($wemp_pmla)
    {
      global $conex;
      global $wcenmez;
      global $wafinidad;
      global $wbasedato;
      global $wtabcco;
      global $winstitucion;
      global $wactualiz;
      global $wchequeo;
      global $wfacturacion;

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

              if ($row[0] == "Chequeo Ejecutivo")
                 $wchequeo=$row[1];

              if ($row[0] == "Facturacion hospitalaria")
                 $wfacturacion=$row[1];
             }
         }
        else
           echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";

      $winstitucion=$row[2];
    }


  function seleccionarPaquete(&$wpaquete)
    {
     global $conex;
     global $wchequeo;


     //Seleccionar PAQUETE
      echo "<center><table>";
      $q = " SELECT placod, planom "
          ."   FROM ".$wchequeo."_000001 "
          ."  WHERE plaest = 'on' ";
      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $num = mysql_num_rows($res);

      echo "<tr class=fila1><td align=center><font size=30>Seleccione el Paquete: </font></td></tr>";
      echo "</table>";
      echo "<br>";
      echo "<center><table>";
      echo "<tr><td align=center><select name='wpaquete' size='1' style=' font-size:20px; font-family:Verdana, Arial, Helvetica, sans-serif; height:40px'>";
      echo "<option>&nbsp</option>";
      for ($i=1;$i<=$num;$i++)
         {
          $row = mysql_fetch_array($res);
          echo "<option>".$row[0]." - ".$row[1]."</option>";
         }
      echo "</select></td></tr>";
      echo "</table>";
    }
  function construirQueryUnix( $tablas, $campos_nulos, $campos_todos='', $condicionesWhere='',$defecto_campos_nulos=''){

    $condicionesWhere = trim($condicionesWhere);

    if( $campos_nulos == NULL || $campos_nulos == "" ){
      $campos_nulos = array("");
    }

    if( $tablas == "" ){ //Debe existir al menos una tabla
      return false;
    }

    if(gettype($tablas) == "array"){
      $tablas = implode(",",$tablas);
    }

    $pos = strpos($tablas, ",");
    if( $pos !== false && $condicionesWhere == ""){ //Si hay mas de una tabla, debe mandar condicioneswhere
      return false;
    }

    //Si recibe un string, convertirlo a un array
    if( gettype($campos_nulos) == "string" )
      $campos_nulos = explode(",",$campos_nulos);

    $campos_todos_arr = array();

    //Por cual string se reemplazan los campos nulos en el query
    if( $defecto_campos_nulos == "" ){
      $defecto_campos_nulos = array();
      foreach( $campos_nulos as $posxy=>$valorxy ){
        array_push($defecto_campos_nulos, "''");
      }
    }else{
      if(gettype($defecto_campos_nulos) == "string"){
        $defecto_campos_nulos = explode(",",$defecto_campos_nulos);
      }
      if(  count( $defecto_campos_nulos ) == 1 ){ //Significa que todos los campos nulos van a ser reemplazados con el mismo valor
        $defecto_campos_nulos_aux = array();
        foreach( $campos_nulos as $posxyc=>$valorxyc ){
          array_push($defecto_campos_nulos_aux, $defecto_campos_nulos[0]);
        }
        $defecto_campos_nulos = $defecto_campos_nulos_aux;
      }else if(  count( $defecto_campos_nulos ) != count( $campos_nulos ) ){
        return false;
      }
    }

    if( gettype($campos_todos) == "string" ){
      $campos_todos_arr = explode(",",trim($campos_todos));
    }else if(gettype($campos_todos) == "array"){
      $campos_todos_arr = $campos_todos;
      $campos_todos = implode(",",$campos_todos);
    }
    foreach( $campos_todos_arr as $pos22=>$valor ){ //quitar espacios a cada valor
      $campos_todos_arr[$pos22] = trim($valor);
    }
    foreach( $campos_nulos as $pos221=>$valor1 ){ //quitar espacios a cada valor
      $campos_nulos[$pos221] = trim($valor1);

      //Si el campo nulo no existe en el arreglo de todos los campos, agregarlo al final
      $clavex = array_search(trim($valor1), $campos_todos_arr);
      if( $clavex === false ){
        array_push($campos_todos_arr,trim($valor1));
      }
    }
    //Quitar la palabra and, si las condiciones empiezan asi.
    if( substr($condicionesWhere, 0, 3)  == "AND" || substr($condicionesWhere, 0, 3) == "and" ){
      $condicionesWhere = substr($condicionesWhere, 3);
    }
    $condicionesWhere = str_replace("WHERE", "", $condicionesWhere); //Que no tenga la palabra WHERE
    $condicionesWhere = str_replace("where", "", $condicionesWhere); //Que no tenga la palabra WHERE

    $query = "";

    $bits = count( $campos_nulos );
    if( $bits >= 10 ){ //No pueden haber más de 10 campos nulos
      return false;
    }

    if( $bits == 1 && $campos_nulos[0] == "" ){ //retornar el query normal
      $query = "SELECT ".$campos_todos ." FROM ".$tablas;
      if( $condicionesWhere != "" )
        $query.= " WHERE ".$condicionesWhere;
      return $query;
    }

    $max = (1 << $bits);
    $fila_bits = array();
    for ($i = 0; $i < $max; $i++){
      /*-->decbin Entrega el valor binario del decimal $i,
        -->str_pad Rellena el string hasta una longitud $bits con el caracter 0 por la izquierda:
         EJEMPLO $input = "Alien" str_pad($input, 10, "-=", STR_PAD_LEFT);  // produce "-=-=-Alien", rellena por la izquierda hasta juntar 10 caracteres
        -->str_split Convierte un string (el entregado por str_pad) en un array, asi tengo el arreglo con el codigo binario generado
      */
      $campos_todos_arr_copia = array();
      $campos_todos_arr_copia = $campos_todos_arr;

      $fila_bits = str_split( str_pad(decbin($i), $bits, '0', STR_PAD_LEFT) );
      $select = "SELECT ";
      $where = " WHERE ";
      if( $condicionesWhere != "" )
        $where.= $condicionesWhere." AND ";

      for($pos = 0; $pos < count($fila_bits); $pos++ ){
        if($pos!=0) $where.= " AND ";
        if( $fila_bits[$pos] == 0 ){
          $clave = array_search($campos_nulos[$pos], $campos_todos_arr_copia);
          //if( $clave !== false ) $campos_todos_arr_copia[ $clave ] = "'.' as ".$campos_nulos[$pos];
          if( $clave !== false ) $campos_todos_arr_copia[ $clave ] = $defecto_campos_nulos[$pos]." as ".$campos_nulos[$pos];
          $where.= $campos_nulos[$pos]." IS NULL ";
        }else{
          $where.= $campos_nulos[$pos]." IS NOT NULL ";
        }
      }

      $select.= implode(",",$campos_todos_arr_copia);
      $query.= $select." FROM ".$tablas.$where;
      if( ($i+1) < $max ) $query.= " UNION ";
    }
    return $query;
  }
  function imprimir_factura($wfactura, $wparam){

      global $wpaquete;
      global $imprimirDetallePaquete;
      global $conexunix;
      global $servicioFisiatria;
      global $whce;
      global $wactualiz;
      global $wffa;
      global $nombre_plan;
      global $conex;
      global $wfacturacion;
      global $wemp_pmla;

      /*$q = " SELECT carano, carmes, carfec, carfev, carcep, carpac, carced, carres, carval, carhis, carnum "
          ."   FROM cacar "
          ."  WHERE carfue = '20' "
          ."    AND cardoc = ".$wfactura
          ."    AND caranu = '0' ";*/

      $campos_nulos = " carcep, carhis, carnum";
      $campos = "carano, carmes, carfec, carfev, carcep, carpac, carced, carres, carval, carhis, carnum, carfue";
      $tablas = "cacar";
      $where  = " carfue = '$wffa' "
               ." AND cardoc = '".$wfactura."'"
               ." AND caranu = '0' ";
      $defectoCampos = " '',0,0";
      $q      = construirQueryUnix( $tablas,$campos_nulos,$campos,$where, $defectoCampos );
      //echo "<pre>".print_r( $q, true )."</pre>";

      $res = odbc_do($conexunix,$q);

      while( odbc_fetch_row($res) )
        {
         $wano = odbc_result($res,1);
         $wmes = odbc_result($res,2);
         $wfec = odbc_result($res,3);	//Fecha
         $wfev = odbc_result($res,4);	//Fecha de vencimiento
         $wcep = odbc_result($res,5);
         $wpac = odbc_result($res,6);	//paciente
         $wced = odbc_result($res,7);	//Nro de documento
         $wres = odbc_result($res,8);	//Responsable
         $wval = odbc_result($res,9);	//Valor factura
         $whis = odbc_result($res,10);	//Historia clínica
         $wing = odbc_result($res,11);	//Ingreso historia

         $queryUnix = " SELECT egrsin
                          FROM inmegr
                         WHERE egrhis = '{$whis}'
                           AND egrnum = '{$wing}'";
          $res2 = odbc_exec($conexunix,$queryUnix);
          while( odbc_fetch_row($res2) ){
            $servicioIngreso =  odbc_result($res2,1 );
          }

          if( trim( $servicioIngreso ) == trim( $servicioFisiatria ) ){
            $querySalida = " SELECT max( Fecha_data )
                               FROM ".$whce."_000036
                              WHERE Firhis = '{$whis}'
                                AND Firing = '{$wing}'";
            $rsSalida  = mysql_query( $querySalida, $conex );
            while( $rowSalida = mysql_fetch_array( $rsSalida ) ){
               $wfec = $rowSalida[0];
            }
          }

          if( isset( $nombre_plan ) and $nombre_plan != "" ){
            $queryUnix = " SELECT empnit, empnom
                            FROM inemp
                           WHERE empcod = '{$wced}'";
            $res2 = odbc_exec($conexunix,$queryUnix);
            while( odbc_fetch_row($res2) ){
              $wres =  odbc_result($res2,2 );
              $wced  =  odbc_result($res2,1 );
              $queryUnix2 = " SELECT empdetnit, empdetraz
                                FROM inempdet
                               WHERE empdetcod = '{$wced}'";
              $res22 = odbc_exec($conexunix,$queryUnix2);
              while( odbc_fetch_row($res22) ){
                $wres =  odbc_result($res22,2 );
              }
            }
          }

         // $wano = "2011";
         // $wmes = "09";
         // $wfec = "2011-09-19";	//Fecha
         // $wfev = "2011-09-20";	//Fecha de vencimiento
         // $wcep = "Hmmmm....";
         // $wpac = "Ediwn Molina Grisales";	//paciente
         // $wced = "98703683";	//Nro de documento
         // $wres = "Edwin Molina Grisales";	//Responsable
         // $wval = "100000";	//Valor factura
         // $whis = "154862";
         // $wing = "5";


         // echo "<br><br><br><br><br><br>";
         // echo $wres."             ".$wced."                                                                     ".substr($wfec,8,2)."  ".substr($wfec,5,2)."  ".substr($wfec,0,4)."<br><br>";
         // echo $wpac."                                                                                           ".substr($wfev,8,2)."  ".substr($wfev,5,2)."  ".substr($wfev,0,4)."<br><br>";

         $htmlFactura   = "<style>
                            .monoespaciado{
                              font-family: 'Courier New';
                              font-weight: bold;
                            }
                             .monoespaciado td{
                              overflow: hidden;
                            }
                          </style>";
        $htmlFactura  .= " <div class='nobreak' align='center'>";
         echo "<br><br>";

         //1ra fila: Fecha y fecha de venciemiento
         /*
             echo "<table style='width:18.5cm;font-size:8pt;font-family:Courier New'>";
             echo "<tr style='height:0.8cm'>";
             echo "<td style='width:12.5cm'></td>";
             echo "<td style='width:0.8cm' align='right'>".substr($wfec,8,2)."</td>";   //Fecha factura
             echo "<td style='width:0.8cm' align='right'>".substr($wfec,5,2)."</td>";   //Fecha factura
             echo "<td style='width:1.4cm' align='right'>".substr($wfec,0,4)."</td>";   //Fecha factura
             echo "<td style='width:0.8cm' align='right'>".substr($wfev,8,2)."</td>";   //Fecha de vecimiento
             echo "<td style='width:0.8cm' align='right'>".substr($wfev,5,2)."</td>";   //Fecha de vecimiento
             echo "<td style='width:1.4cm' align='right'>".substr($wfev,0,4)."</td>";   //Fecha de vecimiento
             echo "</tr>";
             echo "</table>";
         */
         //1ra fila: Fecha y fecha de venciemiento
        $htmlFactura .= "<table cellspacing='0' cellpadding='0' style='width:185mm; height:8mm; font-size:10pt;'  class='monoespaciado'>";
        //$htmlFactura .= "<tr style='height:1.2cm'>";
        $htmlFactura .= "<tr>";
        $htmlFactura .= "<td style='width:124mm;' align='center' valign='top'>No. ".$wfactura."</td>";
        $htmlFactura .= "<td style='margin-top:4mm; width:8mm;' align='center' valign='top'>".substr($wfec,8,2)."</td>";            //Fecha
        $htmlFactura .= "<td style='margin-top:4mm; width:8mm;' align='center' valign='top'>".substr($wfec,5,2)."</td>";            //Fecha
        $htmlFactura .= "<td style='margin-top:4mm; width:15mm;' align='center' valign='top'>".substr($wfec,0,4)."</td>";            //Fecha
        $htmlFactura .= "<td style='margin-top:4mm; width:8mm;' align='center' valign='top'>".substr($wfev,8,2)."</td>";            //Fecha de vencimiento
        $htmlFactura .= "<td style='margin-top:4mm; width:8mm;' align='center' valign='top'>".substr($wfev,5,2)."</td>";            //Fecha de vencimiento
        $htmlFactura .= "<td style='margin-top:4mm; width:15mm;' align='center' valign='top'>".substr($wfev,0,4)."</td>";            //Fecha de vencimiento
        //$htmlFactura .= "<td style='width:0.77cm'></td>";                                                               //Espacio vacio
        $htmlFactura .= "</tr>";
        $htmlFactura .= "</table>";


         //No se neceista bordes para la tabla al imprimir
         //2da fila: Responsable, nit, domicilio, telefono

         ///---> pendiente de reemplazar
         /*echo "<table style='width:18.5cm;font-size:8pt;font-family:Courier New'>";
         echo "<tr style='height:0.8cm'>";
         echo "<td style='width:8cm'>".$wres."</td>";
         echo "<td style='width:3cm'>".$wced."</td>";
         echo "<td style='width:6cm'></td>";
         echo "<td style='width:1.5cm'></td>";
         echo "</tr>";
         echo "</table>";*/
         //--> pendiente de reemplazar(fin)

        $htmlFactura .= "<table cellspacing='0' cellpadding='0' style='width:185mm;font-size:11pt;height:8mm;'  class='monoespaciado'>";
        //$htmlFactura .= "<tr style='height:1cm'>";
        $htmlFactura .= "<tr>";
        $htmlFactura .= "<td style='width:76mm; margin-top:2mm;' align='left'>".$wres."</td>"; // Nombre responsable
        $htmlFactura .= "<td align='center' style='width:30mm; margin-top:2mm;'>".$wced."</td>";  // NIT responsable
        $htmlFactura .= "<td align='center' nowrap='nowrap' style='width:58mm; margin-top:1mm;'>&nbsp;</td>";    // Domicilio responsable
        $htmlFactura .= "<td align='center' style='width:20mm; margin-top:2mm;'>&nbsp;</td>";   // Telefono responsable
        $htmlFactura .= "</tr>";
        $htmlFactura .= "</table>";


         /*
         //Fila del paciente
         echo "<table style='width:18.5cm;font-size:8pt;font-family:Courier New'>";
         echo "<tr style='height:0.8cm'>";
         echo "<td style='width:9.7cm'>".$wpac."</td>";                             //Paciente
         echo "<td style='width:0.8cm' align='right'>".substr($wfec,8,2)."</td>";   //Fecha de ingreso
         echo "<td style='width:0.8cm' align='right'>".substr($wfec,5,2)."</td>";   //Fecha de ingreso
         echo "<td style='width:1.4cm' align='right'>".substr($wfec,0,4)."</td>";   //Fecha de ingreso
         echo "<td style='width:0.8cm' align='right'>".substr($wfec,8,2)."</td>";   //Fecha de salida
         echo "<td style='width:0.8cm' align='right'>".substr($wfec,5,2)."</td>";   //Fecha de salida
         echo "<td style='width:1.4cm' align='right'>".substr($wfec,0,4)."</td>";   //Fecha de salida
         echo "<td style='width:2.8cm' align='center'>".$whis."-".$wing."</td>";    //Estadia
         echo "</tr>";

         echo "<tr  style='height:0.8cm'><td></td></tr>";   //Este espacio es el usado por documento de identidad y tipo de atencion
         echo "<tr  style='height:0.3cm'><td></td></tr>";   //Espacio muerto, aqui va donde dice RESOLUCION DIAN No. 1100....
         echo "</table>";*/

         //Fila del paciente
        $htmlFactura .= "<table cellspacing='0' cellpadding='0' style='width:185mm;font-size:10pt;height:8mm;'  class='monoespaciado'>";
        //$htmlFactura .= "<tr style='height:0.7cm'>";
        $htmlFactura .= "<tr>";
        $htmlFactura .= "<td style=' margin-top:4mm; width:96mm;' align='left' valign='middle'>".$wpac."</td>";                             //Paciente
        $htmlFactura .= "<td style=' margin-top:3mm; width:8mm;' align='center' valign='middle'>".substr($wfec,8,2)."</td>";//Fecha de ingreso
        $htmlFactura .= "<td style=' margin-top:3mm; width:8mm;' align='center' valign='middle'>".substr($wfec,5,2)."</td>";//Fecha de ingreso
        $htmlFactura .= "<td style=' margin-top:3mm; width:15mm' align='center' valign='middle'>".substr($wfec,0,4)."</td>";//Fecha de ingreso
        $htmlFactura .= "<td style=' margin-top:3mm; width:8mm;' align='center' valign='middle'>".substr($wfec,8,2)."</td>";   //Fecha de salida
        $htmlFactura .= "<td style=' margin-top:3mm; width:8mm;' align='center' valign='middle'>".substr($wfec,5,2)."</td>";   //Fecha de salida
        $htmlFactura .= "<td style=' margin-top:3mm; width:15mm;' align='center' valign='middle'>".substr($wfec,0,4)."</td>";   //Fecha de salida
        $htmlFactura .= "<td style=' margin-top:3mm; width:28mm' align='center' valign='middle'>".$whis."-".$wing."</td>";    //Estadia
        $htmlFactura .= "</tr>";
        $htmlFactura .= "</table>";

         //Descripcion de pago
         list( $codigo, $descripcion ) = explode( "-", $wpaquete );
         /*echo "<table style='width:18.5cm;font-size:8pt;font-family:Courier New'>";
         echo "<tr style='height:7.0cm'>";
         echo "<td style='width:1.5cm' align='center'>".$codigo."</td>";
         echo "<td style='width:12.5cm'>".$descripcion."</td>";
         echo "<td style='width:4.5cm' align='right'>".number_format( $wval, 0,'.', ',' )."</td>";
         echo "</tr>";
         echo "</table>";*/

         $htmlFactura .= "<table cellspacing='0' cellpadding='0' style='width:185mm;height:70mm; margin-top:2mm;'  class='monoespaciado' ><tr><td valign='top'>";
         $htmlFactura .= "<table cellspacing='0' cellpadding='0' style='width:185mm;font-size:9pt; valign:top;' class='monoespaciado'>";
         $htmlFactura .= "<tr style='height:35mm'>";
         $htmlFactura .= "<td style='width:14mm;'>&nbsp;</td>";  //Espacio muerto
         $htmlFactura .= "<td style='width:50mm;'>&nbsp;</td>";  //Espacio muerto
         $htmlFactura .= "<td style='width:39mm;'>&nbsp;</td>";  //Espacio muerto
         $htmlFactura .= "<td style='width:22mm;' align='right' valign='bottom'>&nbsp;</td>";  //Encabezado CLINICA
         $htmlFactura .= "<td style='width:22mm;' align='right' valign='bottom'>&nbsp;</td>"; //Encabezado TERCEROS
         $htmlFactura .= "<td style='width:35mm;' align='right' valign='bottom'>&nbsp;</td>"; //Encabezado TOTAL
         $htmlFactura .= "</tr>";


         //echo "<br> edb->".$imprimirDetallePaquete;
         $wval2105 = 0;
         if( $imprimirDetallePaquete == "on" ){
            $q = " SELECT movdetcon, SUM(movdetval*conmul), movdetnit, movdetfue, movdetdoc, connom, movdetvde "
            ."   FROM cacar, famovdet, facon "
            ."  WHERE carfue = '".$wffa."' "
            ."    AND cardoc = '".$wfactura."'"
            ."    AND carfue = movdetfue "
            ."    AND cardoc = movdetdoc "
            ."    AND movdetcon = '2105' "
            ."    AND movdetcon = concod "
            ."    AND caranu = '0' "
            ."    AND movdetanu = '0'
               GROUP BY 1,3,4,5,6,7";
            $resdes = odbc_do($conexunix,$q);
            while( odbc_fetch_row($resdes) )
            {
                $wcon2105 = odbc_result($resdes,1); //Codigo concepto
                $wval2105 = odbc_result($resdes,2); //Valor del concepto
                $wnit2105 = odbc_result($resdes,3); //NIT
                $wfue2105 = odbc_result($resdes,4); //Fuente
                $wdoc2105 = odbc_result($resdes,5); //Documento
                $descuento2105 = odbc_result($resdes,7);    //Valor de descuento

                // // Para empresa en especial que pide no discriminar por tercero y por clinica sino simplemente todo sumado a clinica.
                // $entidadNoDiscriminaTerceros = $entidadNoDiscriminaTerceros*1;
                // $wcod = $wcod*1;
                // $sumar_clinica_tercero = false;
                // /*if($entidadNoDiscriminaTerceros == $wcod)
                // {
                //     $sumar_clinica_tercero = true;
                // }*/

                // /*if (!$cx_no_pos) // Si no se selecciona ver concepto cirugía NO POS - 2012-05-09
                // {
                //     if($wnopos=='1')
                //     {
                //         $q = " SELECT concod, condes "
                //         ."   FROM ".$wfacturacion."_000001 "
                //         ."  WHERE concod = '".$wcon."'"
                //         ."    AND conest = 'on' ";
                //         $resnopos = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                //         $numnopos = mysql_num_rows($resnopos);
                //         if($numnopos>0)
                //         {
                //             $rownopos = mysql_fetch_array($resnopos);
                //             $wcde = trim($rownopos['condes']);  //Descripcion concepto
                //         }
                //         else
                //         {
                //             $wcde = trim(odbc_result($resdes,6));   //Descripcion concepto
                //         }
                //     }
                //     else
                //     {
                //         $wcde = trim(odbc_result($resdes,6));   //Descripcion concepto
                //     }
                // }*/
                $q = " SELECT concod, condes "
                    ."   FROM ".$wfacturacion."_000001 "
                    ."  WHERE concod = '".$wcon2105."'"
                    ."    AND conest = 'on' ";
                $resnopos2015 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                $numnopos = mysql_num_rows($resnopos2015);
                if($numnopos>0)
                {
                    $rownopos = mysql_fetch_array($resnopos2015);
                    $wcde2105 = trim($rownopos['condes']);  //Descripcion concepto
                }
                else
                {
                    $wcde2105 = trim(odbc_result($resdes,6));   //Descripcion concepto
                }

                // //Consulto datos del tercero (Porcentaje y Nombre)
                // $q =   " SELECT connitpor, nitnom "
                // ."   FROM faconnit, conit "
                // ."  WHERE connitcon = '$wcon'"
                // ."    AND connitnit = '$wnit'"
                // ."    AND connitnit = nitnit";
                // $rescon = odbc_do($conexunix,$q);
                // odbc_fetch_row($rescon);
                // $wpor = odbc_result($rescon,1); //Porcentaje tercero
                // $wnom = odbc_result($rescon,2); //Nombre tercero
                // $wtde = $wnit." ".substr($wnom,0,11);
                // if($wpor && $wpor>0)
                // {

                //     // Se comenta porque en teter el valor del tercero no está discriminado, es igual al total del concepto
                //     // entonces para obtener el valor del tercero se hace calculo por medio del porcentaje en faconnit
                //     // consulto el valor asignado al tercero
                //     $q =   " SELECT terval "
                //     ."   FROM facarfac, facardet, teter "
                //     ."  WHERE carfacfue = '$wfue'"
                //     ."    AND carfacdoc = '$wdoc'"
                //     ."    AND carfacanu = '0'"
                //     ."    AND carfacreg = cardetreg "
                //     ."    AND cardetanu = '0'"
                //     ."    AND terfue = cardetfue"
                //     ."    AND terdoc = cardetdoc "
                //     ."    AND tercon = '$wcon' "
                //     ."    AND teranu = '0'";
                //     $rester = odbc_do($conexunix,$q);
                //     odbc_fetch_row($rester);

                //     $valor_tercero = odbc_result($rester,1);
                //     $valor_clinica = $wval - $valor_tercero;


                //     // Se obtiene porcentaje asociado a la clínica
                //     $porcentaje_tercero = $wpor;
                //     $porcentaje_clinica = 100 - $porcentaje_tercero;

                //     // Se obtiene el valor del tercero y se redondea
                //     $valor_tercero = $wval * ($porcentaje_tercero / 100);
                //     $valor_tercero = round($valor_tercero,5);

                //     // Se obtiene el valor de la clinica y se redondea
                //     $valor_clinica = $wval * ($porcentaje_clinica / 100);
                //     $valor_clinica = round($valor_clinica,5);

                //     // Para empresa en especial que pide no discriminar por tercero y por clinica sino simplemente todo sumado a clinica.
                //     if($sumar_clinica_tercero)
                //     {
                //       $valor_clinica = $valor_clinica + $valor_tercero;
                //       $valor_tercero = 0;
                //     }

                //     if($cont>=$limite_conceptos)
                //     {
                //         $total_otros_terceros += $valor_tercero;
                //         $total_otros_clinica += $valor_clinica;
                //         $total_otros += $valor_tercero+$valor_clinica;
                //     }
                // }
                // else
                // {
                //     $wtde = "";
                //     $valor_tercero = 0;
                //     $valor_clinica = $wval;
                //     if($cont>=$limite_conceptos)
                //     {
                //         $total_otros_terceros += $valor_tercero;
                //         $total_otros_clinica += $valor_clinica;
                //         $total_otros += $valor_tercero+$valor_clinica;
                //     }
                // }

                // if($cont<$limite_conceptos && !$cx_no_pos) //********************************************************************
                // {
                    //$htmlFactura .= "<tr style='height:0.4cm'>";
                    $htmlFacturaDetalle .= "<tr style='height:4mm'>";
                    $htmlFacturaDetalle .= "<td style='width:14mm;font-size:9pt;' align='center'>".$wcon2105."</td>";               //Codigo concepto
                    $htmlFacturaDetalle .= "<td style='width:50mm;font-size:9pt;' align='left'>".substr($wcde2105,0,28)."</td>";    //Descripcion concepto
                    $htmlFacturaDetalle .= "<td style='width:39mm;font-size:9pt;' align='left'>".$wtde2105."</td>";                 //Descripción tercero
                    $htmlFacturaDetalle .= "<td style='width:22mm;font-size:10pt;' align='right'>&nbsp;</td>";    //Valor clínica
                    $htmlFacturaDetalle .= "<td style='width:22mm;font-size:10pt;' align='right'>&nbsp;</td>";    //Valor tercero
                    $htmlFacturaDetalle .= "<td style='width:38mm;font-size:10pt;' align='right'>".number_format( $wval2105, 0,'.', ',' )."</td>";
                    $htmlFacturaDetalle .= "</tr>";
                    $wval = $wval + ($wval2105*-1) ;
                //}

                // // if($wcon != $cod_paf_con)
                // {
                //     $total_clinica += $valor_clinica;
                //     $total_terceros += $valor_tercero;
                //     $total_descuento += $descuento;
                // }
                // $total += $wval;

                // if($wcon == $cod_paf_con) // 2012-11-16 para sumar todo lo que es del concepto tipo PAF
                // {
                //     $total_clinica_paf += $valor_clinica;
                //     $total_terceros_paf += $valor_tercero;
                //     $total_descuento_paf += $descuento;
                //     $total_desc_paf += ($wval < 0) ? ($wval*(-1)) : $wval;
                //     $hay_paf = true;

                //     if($wval < 0)           { $total +=  $wval*(-1); }
                //     if($valor_clinica < 0)  { $total_clinica +=  $valor_clinica*(-1); }
                //     if($valor_tercero < 0)  { $total_terceros +=  $valor_tercero*(-1); }
                //     if($descuento < 0)      { $total_descuento +=  $descuento*(-1); }
                // }
                $cont++;
            }
         }
         $htmlFactura .= "<tr style='height:4mm'>";
         $htmlFactura .= "<td style='width:14mm;font-size:9pt;' align='center'>".$codigo."</td>";               //Codigo concepto
         $htmlFactura .= "<td style='width:50mm;font-size:9pt;' align='left'>".$descripcion."</td>";    //Descripcion concepto
         $htmlFactura .= "<td style='width:39mm;font-size:9pt;' align='left'>&nbsp;</td>";                 //Descripción tercero
         $htmlFactura .= "<td style='width:22mm;font-size:10pt;' align='right'>&nbsp</td>";    //Valor clínica
         $htmlFactura .= "<td style='width:22mm;font-size:10pt;' align='right'>&nbsp;</td>";    //Valor tercero
         $htmlFactura .= "<td style='width:35mm;font-size:10pt;' align='right'>".number_format( $wval, 0,'.', ',' )."</td>";
         $htmlFactura .= "</tr>";
         $wval += $wval2105;
         if( $imprimirDetallePaquete == "on" ){
          $htmlFactura .= $htmlFacturaDetalle;
         }
         $htmlFactura .= "</table></td></tr></table>";

         ///echo "<br>";

         //Forma de pago
         /*echo "<table style='width:18.5cm;font-size:8pt;font-family:Courier New'>";
         echo "<tr style='height:1.3cm'>";
         echo "<td style='width:11.5cm'><br>".montoescrito( $wval )."</td>";
         echo "<td style='width:2.5cm' rowspan='3'></td>";
         echo "<td style='width:4.5cm' rowspan='3' align='right'><br>".number_format( $wval, 0,'.', ',' )."<br><br>".number_format( $wval, 0,'.', ',' )."<br><br><br><br>".number_format( $wval, 0,'.', ',' )."</td>";
         echo "</tr>";

         echo "<tr style='height:1.4cm'>";
         echo "<td></td>";
         echo "</tr>";

         echo "<tr style='height:0.4cm'>";
         echo "<td></td>";
         echo "</tr>";

         echo "</table>";*/

         //se hace count para saber si tiene resultados los copagos o cuota moderadora
        $q =    " SELECT count(*) "
            ."   FROM anantfac "
            ."  WHERE antfacffa = '".$wffa."'"
            ."    AND antfacdfa = '".$wfactura."'
                  AND antfacanu = '0'";
        $resant1 = odbc_do($conexunix,$q);
        $antfacval1 = odbc_result($resant1,1);

        if ($antfacval1>0)
        {
            //Consulta de copagos o cuota moderadora
            $q =    " SELECT SUM(antfacval) "
                ."   FROM anantfac "
                ."  WHERE antfacffa = '".$wffa."'"
                ."    AND antfacdfa = '".$wfactura."'
                      AND antfacanu = '0'";
            $resant = odbc_do($conexunix,$q);
            odbc_fetch_row($resant);
            $antfacval = odbc_result($resant,1);  //Copago o cuota moderadora
        }
        else
        {
            $antfacval=0;
        }

        if(!isset($antfacval) || !$antfacval)
        $antfacval = 0;

        if($wind=='P')
        {
        $ant_exc = $antfacval;
        $cop_cmo_frq = 0;
        }
        else
        {
        $ant_exc = 0;
        $cop_cmo_frq = $antfacval;
        }

        $parcial = $wval;
        $subtotal = $wval-$total_descuento;
        $iva = 0;   // IVA siempre es cero ya que en hospitalización no hay cargos que impliquen IVA
        $total_neto = $subtotal+$iva-$cop_cmo_frq-$ant_exc;

          //Forma de pago
         $htmlFactura .= "<table cellspacing='0' cellpadding='0' style='width:185mm;font-size:9pt;height:30mm;' class='monoespaciado'>";
         $htmlFactura .= "<tr style='height:15mm;' valign='top'>";
         $htmlFactura .= "<td>&nbsp;</td>";
         $htmlFactura .= "</tr>";
         $htmlFactura .= "<tr style='height:27mm;'>";

         $htmlFactura .= "<td style='width:124mm; margin-top:4mm;' valign='top'><br>".montoescrito( $total_neto )."</td>";  //***********************************************
         $htmlFactura .= "<td style='width:23mm;'></td>";
         $htmlFactura .= "<td style='width 38mm;' align='right' valign='top'><br>".number_format( $wval, 0,'.', ',' )."<br><br>".number_format( $wval, 0,'.', ',' )."<br><br><br>".number_format( $cop_cmo_frq, 0,'.', ',' )."<br>".number_format( $total_neto, 0,'.', ',' )."</td>";
         $htmlFactura .= "</tr>";
         $htmlFactura .= "</table>";


         //---> agregar observaciones a lo resumido.

         $observacionFinal = "";
        //Consulta para saber cuantas lineas de observaciones tiene la factura

        $query =" SELECT COUNT(*) "
               ." FROM cacarobs "
               ." WHERE carobsfue = '".$wffa."'"
               ." AND carobsdoc = '".$wfactura."' ";

        $res = odbc_do($conexunix,$query);
        $lineas= odbc_result($res,1);
        //$htmlFactura .= $lineas;

        if($lineas==0)
        {
            $observacionFinal = "";
        }
        else if ($lineas==1)
        {
            //Consulta de observaciones
             $q =" SELECT carobsdes "
                ." FROM cacarobs "
                ." WHERE carobsfue = '".$wffa."'"
                ." AND carobsdoc = '".$wfactura."' "
                ." AND carobsnum='1'";
            $resobs = odbc_do($conexunix,$q);
            odbc_fetch_row($resobs);

            $observacion = odbc_result($resobs,1);
        }
        else if ($lineas==2)
        {
            //Consulta de observaciones
             $q =" SELECT carobsdes "
                ." FROM cacarobs "
                ." WHERE carobsfue = '".$wffa."'"
                ." AND carobsdoc = '".$wfactura."' "
                ." AND carobsnum='1'";
            $resobs = odbc_do($conexunix,$q);
            odbc_fetch_row($resobs);

            //Consulta de observaciones
             $q1 =" SELECT carobsdes "
                ." FROM cacarobs "
                ." WHERE carobsfue = '".$wffa."'"
                ." AND carobsdoc = '".$wfactura."' "
                ." AND carobsnum='2'";
            $resobs1 = odbc_do($conexunix,$q1);
            odbc_fetch_row($resobs1);

            $observacion = odbc_result($resobs,1)." ".odbc_result($resobs1,1);
        }
        else
        {
            //Consulta de observaciones
             $q =" SELECT carobsdes "
                ." FROM cacarobs "
                ." WHERE carobsfue = '".$wffa."'"
                ." AND carobsdoc = '".$wfactura."' "
                ." AND carobsnum='1'";
            $resobs = odbc_do($conexunix,$q);
            odbc_fetch_row($resobs);

            //Consulta de observaciones
             $q1 =" SELECT carobsdes "
                ." FROM cacarobs "
                ." WHERE carobsfue = '".$wffa."'"
                ." AND carobsdoc = '".$wfactura."' "
                ." AND carobsnum='2'";
            $resobs1 = odbc_do($conexunix,$q1);
            odbc_fetch_row($resobs1);

            //Consulta de observaciones
             $q2 =" SELECT carobsdes "
                ." FROM cacarobs "
                ." WHERE carobsfue = '".$wffa."'"
                ." AND carobsdoc = '".$wfactura."' "
                ." AND carobsnum='3'";
            $resobs2 = odbc_do($conexunix,$q2);
            odbc_fetch_row($resobs2);

            $observacion = odbc_result($resobs,1)." ".odbc_result($resobs1,1)." ".odbc_result($resobs2,1);
        }


        $numCaracteres = strlen($observacion);

        if ($numCaracteres > 150)
        {
            $observacionFinal = substr( $observacion, 0, 150 );
        }
        else
        {
            $observacionFinal=$observacion;
        }

        //*************************************************************************************

        // if(!isset($observacion) || !$observacion)
        // $observacionFinal = "";

        $htmlFactura .= "<table cellspacing='0' cellpadding='0' style='width:185mm;height:18mm; font-size:9pt;' class='monoespaciado'>";
        $htmlFactura .= "<tr>";
        $htmlFactura .= "<td style='width:109mm;'>&nbsp;</td>"; //Espacio muerto, aqui va donde dice  ELABORADO POR      RECIBI CONFORME
        //$htmlFactura .= "<td style='width:7.3cm' align='left' valign='top'><font size='1'>".$observacion."</font></td>";      //Observaciones

        /* TEMPORALMENTE SE COMENTA LA OBSERVACIÓN FINAL PARA MOSTRAR LA RESOLUCIÓN DE LA DIAN */
        //$htmlFactura .= "<td style='width:76mm;margin-top:10mm;' align='left' valign='top'  ><br>".$observacionFinal."</td>"; // COMENTADO TEMPORALMENTE, al activarlo se deberá comentar el td siguiente

       if( isset( $nombre_plan ) ){
        //$datos_plan = $nombre_plan." MES ".$mes_plan." DEL ".$anioHoy;
        $datos_plan = $nombre_plan." MES DE".$mes_plan." DEL ".$ano_plan;
       }
       $htmlFactura .= "<td style='width:76mm;margin-top:10mm;' align='left' valign='top'>";

        $conResolucionDian = consultarAliasPorAplicacion($conex, $wemp_pmla, 'imprimirFacturaUnixConResolucionDian');
        if($conResolucionDian == 'on')
        {
            $htmlFactura .= "
                <font size='2'>
                  RRESOLUCION DIAN NO. 110000619627<br>
                  FECHA 2015/03/06<br>
                  NUMERACION DEL 4164001 AL 5000000 HABILITA.
                </font>";
        }
        else
        {
            $htmlFactura .= "<br>".$observacionFinal."</td>";
        }

        $htmlFactura .= "
              </td>"; // Poner entre comentarios este TD si se activa el TD de $observacionFinal */

        $htmlFactura .= "</tr>";
        $htmlFactura .= "</table>";

         $existen_facturas = "on";
         auditoria( $wfactura, $wced, $whis,$wing, date("Y-m-d"), $wval, $wpaquete, $wparam );
         // return;
        }
        if($existen_facturas=='off')
          $htmlFactura .= "<div align='center'><br>No se encontraron datos para la factura</div>";
          else{
              //$htmlFactura .= "</html>";
              $wnombrePDF = $wemp_pmla."_".trim($wusuario)."_".$wfactura."_ejec";
              //CREAR UN ARCHIVO .HTML CON EL CONTENIDO CREADO
              $dir = 'facturas';
              if(is_dir($dir)){ }
              else { mkdir($dir,0777); }
              $archivo_dir = $dir."/".$wnombrePDF.".html";
              if(file_exists($archivo_dir)){
                unlink($archivo_dir);
              }
              $f           = fopen( $archivo_dir, "w+" );
              fwrite( $f, $htmlFactura);
              fclose( $f );

              $respuesta = shell_exec( "./generarPdf_facturas_unix.sh ".$wnombrePDF );

              /*crearPDF();
              agregarPaginaPDF();
              agregarFacturaPdf( $htmlFactura );
              imprimirPDF( $wnombrePDF."_2" );*/

              $htmlFactura = "<br><br><br><font size='5' color='#2A5DB0'>Factura nro: ".$wfactura."</font>"
                                ."<br><br>"
                              ."<object type='application/pdf' data='facturas/".$wnombrePDF.".pdf' pdf#toolbar=1&amp;navpanes=0&amp;scrollbar=1 width='900' height='700'>"
                                ."<param name='src' value='facturas/".$wnombrePDF."' pdf#toolbar=1&amp;navpanes=0&amp;scrollbar=1 />"
                                ."<p style='text-align:center; width: 60%;'>"
                                  ."Adobe Reader no se encuentra o la versión no es compatible, utiliza el icono para ir a la página de descarga <br />"
                                  ."<a href='http://get.adobe.com/es/reader/' onclick='this.target=\"_blank\">"
                                    ."<img src='../../images/medical/root/prohibido.gif' alt='Descargar Adobe Reader' width='32' height='32' style='border: none;' />"
                                  ."</a>"
                                ."</p>"
                              ."</object>";
          }
          echo "<div align='center'>";
          encabezado("Imprimir Factura Unix",$wactualiz, "clinica");
          echo "<br>";
          echo $htmlFactura;
          echo "<br><br><input type='button' value='retornar' onclick='regresar();' ><br>";
          echo "<br><input type='button' value='Cerrar Ventana' onclick='cerrarPagina();' ><br>";
          echo "</div>";
    }


function imprimir_factura_detalle($wfactura, $wparam, $wnopos, $wimpresora, $wffa, $fechaIngresoEvolucion)
{
    global $conex;
    global $conexunix;
    global $wusuario;
    global $wfacturacion;
    global $wemp_pmla;
    global $servicioFisiatria;
    global $whce;
    global $entidadNoDiscriminaTerceros;
    global $mes_plan;
    global $ano_plan;
    global $nombre_plan;
    global $entidadNoDiscriminaTerceros;

    //$wffa = "20";       // Fuente de facturas    // Se pone entre comentarios porque antes estaba quemado pero ahora se pide desde el formulario - 2012-11-16
    $existen_facturas = 'off';      // Indicador que determinado
    $wpaquete = "";	// Paquete seleccionado. Usado solo para chequeo ejecutivo

    $q = " SELECT carano, carmes, carfec, carfev, carcep, carpac, carced, empdetraz, carval, carhis, carnum, empdir, emptel, empnit, carind, carcco "
          ."   FROM cacar, inemp, inempdet "
          ."  WHERE carfue = '".$wffa."' "
          ."    AND cardoc = '".$wfactura."' "
          ."    AND carced = empcod "
          ."    AND caranu = '0' "
          ."    AND empcod = empdetcod "
		  ;

    // 2012-11-16
    // Si es del tipo PAF entonces no se requiere carcep ni carhis, porque se va a mostrar es una sumatoria, se debe entonces devolver '0' en esos dos campos
    // para evitar valores nulos que dañan el programa.
    // El siguiente query se crea porque para las facturas tipo PAF se estaba detectando valores nulos que hacían fallar el programa
    if (isset($wnopos) && $wnopos == '3')
    {
        $q = "
                SELECT  carano, carmes, carfec, carfev, carcep, carpac, carced, empdetraz, carval, carhis, carnum, empdir, emptel, empnit, carind, carcco
                FROM    cacar, inemp, inempdet
                WHERE   carfue = '".$wffa."'
                        AND cardoc = '".$wfactura."'
                        AND carced = empcod
						AND empcod = empdetcod
                        AND caranu = '0'
                        AND carcep IS NOT NULL
                        AND carhis IS NOT NULL
                        AND carnum IS NOT NULL

                UNION

                SELECT  carano, carmes, carfec, carfev, carcep, carpac, carced, empdetraz, carval, carhis, 0 AS carnum, empdir, emptel, empnit, carind, carcco
                FROM    cacar, inemp, inempdet
                WHERE   carfue = '".$wffa."'
                        AND cardoc = '".$wfactura."'
                        AND carced = empcod
						AND empcod = empdetcod
                        AND caranu = '0'
                        AND carcep IS NOT NULL
                        AND carhis IS NOT NULL
                        AND carnum IS NULL
                UNION

                SELECT  carano, carmes, carfec, carfev, carcep, carpac, carced, empdetraz, carval, 0 AS carhis, carnum, empdir, emptel, empnit, carind, carcco
                FROM    cacar, inemp, inempdet
                WHERE   carfue = '".$wffa."'
                        AND cardoc = '".$wfactura."'
                        AND carced = empcod
						AND empcod = empdetcod
                        AND caranu = '0'
                        AND carcep IS NOT NULL
                        AND carhis IS NULL
                        AND carnum IS NOT NULL
                UNION

                SELECT  carano, carmes, carfec, carfev, carcep, carpac, carced, empdetraz, carval, 0 AS carhis, 0 AS carnum, empdir, emptel, empnit, carind, carcco
                FROM    cacar, inemp, inempdet
                WHERE   carfue = '".$wffa."'
                        AND cardoc = '".$wfactura."'
                        AND carced = empcod
						AND empcod = empdetcod
                        AND caranu = '0'
                        AND carcep IS NOT NULL
                        AND carhis IS NULL
                        AND carnum IS NULL
                UNION

                SELECT  carano, carmes, carfec, carfev, '.' AS carcep, carpac, carced, empdetraz, carval, carhis, carnum, empdir, emptel, empnit, carind, carcco
                FROM    cacar, inemp, inempdet
                WHERE   carfue = '".$wffa."'
                        AND cardoc = '".$wfactura."'
                        AND carced = empcod
						AND empcod = empdetcod
                        AND caranu = '0'
                        AND carcep IS NULL
                        AND carhis IS NOT NULL
                        AND carnum IS NOT NULL
                UNION

                SELECT  carano, carmes, carfec, carfev, '.' AS carcep, carpac, carced, empdetraz, carval, carhis, 0 AS carnum, empdir, emptel, empnit, carind, carcco
                FROM    cacar, inemp, inempdet
                WHERE   carfue = '".$wffa."'
                        AND cardoc = '".$wfactura."'
                        AND carced = empcod
						AND empcod = empdetcod
                        AND caranu = '0'
                        AND carcep IS NULL
                        AND carhis IS NOT NULL
                        AND carnum IS NULL
                UNION

                SELECT  carano, carmes, carfec, carfev, '.' AS carcep, carpac, carced, empdetraz, carval, 0 AS carhis, carnum, empdir, emptel, empnit, carind, carcco
                FROM    cacar, inemp, inempdet
                WHERE   carfue = '".$wffa."'
                        AND cardoc = '".$wfactura."'
                        AND carced = empcod
						AND empcod = empdetcod
                        AND caranu = '0'
                        AND carcep IS NULL
                        AND carhis IS NULL
                        AND carnum IS NOT NULL
                UNION

                SELECT  carano, carmes, carfec, carfev, '.' AS carcep, carpac, carced, empdetraz, carval, 0 AS carhis, 0 AS carnum, empdir, emptel, empnit, carind, carcco
                FROM    cacar, inemp, inempdet
                WHERE   carfue = '".$wffa."'
                        AND cardoc = '".$wfactura."'
                        AND carced = empcod
						AND empcod = empdetcod
                        AND caranu = '0'
                        AND carcep IS NULL
                        AND carhis IS NULL
                        AND carnum IS NULL
                ";
    }
    // echo '<pre>';print_r($q);echo '</pre>';
    $res = odbc_do($conexunix,$q);

    //echo $q."<br>";

    while( odbc_fetch_row($res) )
    {
        $wano = odbc_result($res,1);
        $wmes = odbc_result($res,2);
        $wfec = odbc_result($res,3);    //Fecha
        $wfev = odbc_result($res,4);    //Fecha de vencimiento
        $wcep = odbc_result($res,5);    //Documento paciente
        $wpac = odbc_result($res,6);    //Paciente
        $wced = odbc_result($res,7);    //Nro de documento
        $wres = odbc_result($res,8);    //Responsable
        $wval = odbc_result($res,9);    //Valor factura
        $whis = odbc_result($res,10);   //Historia clinica
        $wing = odbc_result($res,11);   //Ingreso
        $wdir = odbc_result($res,12);   //Direccion responsable
        $wtel = odbc_result($res,13);   //Telefono responsable
        $wcod = odbc_result($res,14);   //Nit responsable
        $wind = odbc_result($res,15);   //Indicador empresa o particular
        $wcco = odbc_result($res,16);   //Indicador empresa o particular

        $qdiv =  " SELECT nitdig "
             ."   FROM conit "
             ."  WHERE nitnit = '".$wcod."' ";
        $resdiv = odbc_do($conexunix,$qdiv);
        $wdiv = odbc_result($resdiv,1); //Digito de verificacion

        // Si no se encuentra digito de verificación por defecto es cero
        if(!isset($wdiv) || $wdiv=="")
        $wdiv = '0';

        // Busco los espacios a dejar en el encabezado y la izquierda según la impresora seleccionada
        $q = " SELECT cimtop, cimlef "
          ."   FROM ".$wfacturacion."_000003 "
          ."  WHERE cimnom = '".$wimpresora."'"
          ."    AND cimusu = '".$wusuario."'"
          ."    AND cimest = 'on' ";
        $rescim = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $numcim = mysql_num_rows($rescim);
        $rowcim = mysql_fetch_array($rescim);
        $wtop = $rowcim['cimtop'];
        $wleft = $rowcim['cimlef'];

		$tipoDocumento = "";
		// Busco el tipo de documento siempre y cuando este exista
		if( trim( $wcep ) != "." and trim( $wcep ) != "" ){
		    $qTipd = "SELECT pactid
			            FROM inpac
					   WHERE pachis = '{$whis}'
					     AND pacnum = '{$wing}'";
			$restipd = odbc_do($conexunix,$qTipd);
			$tipoDocumento = odbc_result($restipd,1); //tipo  de identificación

			if( $tipoDocumento == "" ){
				$qTipd = "SELECT pactid
				            FROM inpaci
						   WHERE pachis = '{$whis}'
						     AND pacnum = '{$wing}'";
				$restipd = odbc_do($conexunix,$qTipd);
				$tipoDocumento = odbc_result($restipd,1); //tipo  de identificación
			}
		}
       $htmlFactura   = "<style>
                            .monoespaciado{
                              font-family: 'Courier New';
                              font-weight: bold;
                            }
                             .monoespaciado td{
                              overflow: hidden;
                            }
                          </style>";
       $htmlFactura  .= " <div class='nobreak' align='center'>";
        //echo "<br>";
       // $htmlFactura .= "<div align='center' style='position: absolute;top:".$wtop."px;left:".$wleft."px'>";

        //1ra fila: Fecha y fecha de venciemiento
        $htmlFactura .= "<table cellspacing='0' cellpadding='0' style='width:185mm; height:8mm; font-size:10pt;'  class='monoespaciado'>";
        //$htmlFactura .= "<tr style='height:1.2cm'>";
        $htmlFactura .= "<tr>";
        $htmlFactura .= "<td style='width:124mm;' align='center' valign='top'>No. ".$wfactura."</td>";
        $htmlFactura .= "<td style='margin-top:4mm; width:8mm;' align='center' valign='top'>".substr($wfec,8,2)."</td>";            //Fecha
        $htmlFactura .= "<td style='margin-top:4mm; width:8mm;' align='center' valign='top'>".substr($wfec,5,2)."</td>";            //Fecha
        $htmlFactura .= "<td style='margin-top:4mm; width:15mm;' align='center' valign='top'>".substr($wfec,0,4)."</td>";            //Fecha
        $htmlFactura .= "<td style='margin-top:4mm; width:8mm;' align='center' valign='top'>".substr($wfev,8,2)."</td>";            //Fecha de vencimiento
        $htmlFactura .= "<td style='margin-top:4mm; width:8mm;' align='center' valign='top'>".substr($wfev,5,2)."</td>";            //Fecha de vencimiento
        $htmlFactura .= "<td style='margin-top:4mm; width:15mm;' align='center' valign='top'>".substr($wfev,0,4)."</td>";            //Fecha de vencimiento
        //$htmlFactura .= "<td style='width:0.77cm'></td>";                                                               //Espacio vacio
        $htmlFactura .= "</tr>";
        $htmlFactura .= "</table>";

        if ($wnopos == '3')// 2012-11-16 se busca el nombre real y no el nombre del contrato al que pertenece el nit
        {
            $qNIT = "   SELECT  nitnit, nitnom
                        FROM    conit
                        WHERE   nitnit = '".trim($wcod)."'";
            $resNIT = odbc_do($conexunix,$qNIT);
            odbc_fetch_row($resNIT);
            $wres = odbc_result($resNIT,2);
        }


        //No se neceista bordes para la tabla al imprimir
        //2da fila: Responsable, nit, domicilio, telefono
        $domicilioResponsable = substr($wdir,0,24);
        $htmlFactura .= "<table cellspacing='0' cellpadding='0' style='width:185mm;font-size:11pt;height:8mm;'  class='monoespaciado'>";
        //$htmlFactura .= "<tr style='height:1cm'>";
        $htmlFactura .= "<tr>";
        $htmlFactura .= "<td style='width:76mm; margin-top:2mm;' align='left'>".substr($wres,0,50)."</td>"; // Nombre responsable
        $htmlFactura .= "<td align='center' style='width:30mm; margin-top:2mm;'>".trim($wcod)."-".$wdiv."</td>";  // NIT responsable
        $htmlFactura .= "<td align='center' nowrap='nowrap' style='width:58mm; margin-top:1mm;'>".$domicilioResponsable."</td>";    // Domicilio responsable
        $htmlFactura .= "<td align='center' style='width:20mm; margin-top:2mm;'>".substr($wtel,0,8)."</td>";   // Telefono responsable
        $htmlFactura .= "</tr>";
        $htmlFactura .= "</table>";

        $wfecing='0000-00-00';
        $wfecegr='0000-00-00';
        // 2012-11-16 en muchas ocaciones se hacen consultas para historias con tipo 0, pero generan datos nulos y dañan el programa, por defecto se pone la fecha en 0000-00-00
        //
        if($whis && trim($whis)!='' && $whis!='0')
		{
			if ($wnopos != '3')
			{
				// Encuentro la fecha de ingreso del paciente
				$query=" SELECT pacfec
						   FROM inpac
						  WHERE pachis = '".$whis."'
							AND pacnum = '".$wing."' ";
				$err_o = odbc_exec($conexunix,$query);
				if(odbc_fetch_row($err_o))
				{
					$wfecing=odbc_result($err_o,1);
					$wfecegr="          ";
				}
				else
				{
					$query="SELECT egring, egregr
							  FROM inmegr
							 WHERE egrhis = '".$whis."'
							   AND egrnum = '".$wing."' ";
					$err_1 = odbc_exec($conexunix,$query);
					//$htmlFactura .= $query."<br>";

					if (odbc_fetch_row($err_1))
					{
						$wfecing=odbc_result($err_1,1);
						$wfecegr=odbc_result($err_1,2);
					}
					else
					{
						$wfecing=$wfec;
						$wfecegr=$wfec;
					}
				}
			}
		}
		else
		{
			$wfecing="";
			$wfecegr="";
			$whis="";
			$wing="";
		}

        // Si es ayuda diagnóstica encuentro la fecha en aymov
        // 2012-11-16 antes estaba quemado a.movfue = '20' y se cambió por a.movfue = '".$wffa."'
        $query="SELECT b.movfec
               FROM famov a, aymov b
              WHERE a.movfue = '".$wffa."'
                AND a.movdoc = '".$wfactura."'
                AND a.movfuo = b.movfue
                AND a.movhis = b.movdoc
                AND a.movanu = '0'
                AND b.movanu = '0' ";
        $err_ay = odbc_exec($conexunix,$query);
        //$htmlFactura .= $query."<br>";

        if(odbc_fetch_row($err_ay))
        {
            $wfecing=odbc_result($err_ay,1);
            $wfecegr="";
        }

		//2016-04-05 Verónica Arismendy
		//La fecha a mostrar en fecha ingreso y fecha salida puede venir determinada desde el formulario antes de imprimir si el centro de costo de la factura está configurado así
		if($fechaIngresoEvolucion != ""){
			$wfecing = $fechaIngresoEvolucion;
			$wfecegr = $fechaIngresoEvolucion;
		}

        //Fila del paciente
        $htmlFactura .= "<table cellspacing='0' cellpadding='0' style='width:185mm;font-size:10pt;height:8mm;'  class='monoespaciado'>";
        //$htmlFactura .= "<tr style='height:0.7cm'>";
        $htmlFactura .= "<tr>";
        $htmlFactura .= "<td style=' margin-top:4mm; width:96mm;' align='left' valign='middle'>".substr($wpac,0,42)."</td>";                             //Paciente
        $htmlFactura .= "<td style=' margin-top:3mm; width:8mm;' align='center' valign='middle'>".substr($wfecing,8,2)."</td>";//Fecha de ingreso
        $htmlFactura .= "<td style=' margin-top:3mm; width:8mm;' align='center' valign='middle'>".substr($wfecing,5,2)."</td>";//Fecha de ingreso
        $htmlFactura .= "<td style=' margin-top:3mm; width:15mm' align='center' valign='middle'>".substr($wfecing,0,4)."</td>";//Fecha de ingreso
        //si es de ayudas diagnosticas no muestra fecha de salida
        if(odbc_fetch_row($err_ay))
            {
                $htmlFactura .= "<td style=' margin-top:3mm; width:8mm;' align='center' valign='middle'>".$wfecegr."</td>";   //Fecha de salida
                $htmlFactura .= "<td style=' margin-top:3mm; width:8mm;' align='center' valign='middle'>".$wfecegr."</td>";   //Fecha de salida
                $htmlFactura .= "<td style=' margin-top:3mm; width:15mm;' align='center' valign='middle'>".$wfecegr."</td>";   //Fecha de salida
            }
        $htmlFactura .= "<td style=' margin-top:3mm; width:8mm;' align='center' valign='middle'>".substr($wfecegr,8,2)."</td>";   //Fecha de salida
        $htmlFactura .= "<td style=' margin-top:3mm; width:8mm;' align='center' valign='middle'>".substr($wfecegr,5,2)."</td>";   //Fecha de salida
        $htmlFactura .= "<td style=' margin-top:3mm; width:15mm;' align='center' valign='middle'>".substr($wfecegr,0,4)."</td>";   //Fecha de salida
        $htmlFactura .= "<td style=' margin-top:3mm; width:28mm' align='center' valign='middle'>".$whis."-".$wing."</td>";    //Estadia
        $htmlFactura .= "</tr>";
        $htmlFactura .= "</table>";

        $htmlFactura .= "<table cellspacing='0' cellpadding='0' style='width:185mm;font-size:10pt;height:8mm;'  class='monoespaciado'>";
        //$htmlFactura .= "<tr style='height:0.7cm'>";
        $htmlFactura .= "<tr>";
        $htmlFactura .= "<td style='width:69mm; margin-top:3mm;' align='left' valign='bottom'> ".$tipoDocumento." - ".$wcep."</td>";                    //Documento de identidad del paciente
        $htmlFactura .= "<td style='width:116mm; margin-top:3mm;' valign='bottom'>HOSPITALIZADO - PENSIONADO</td>";//Tipo de atencion
        $htmlFactura .= "</tr>";
        $htmlFactura .= "</table>";

         $htmlFactura .= "<table cellspacing='0' cellpadding='0' style='width:185mm; height:6mm;'  class='monoespaciado' ><tr><td></td></tr></table>"; //Linea de Resolucion de la DIAN y cpto, descricion y total


        /*$htmlFactura .= "<table cellspacing='0' cellpadding='0' style='width:185mm;font-size:10pt; height:4mm;' class='monoespaciado'>";
        //$htmlFactura .= "<tr style='height:0.8cm'>";
        $htmlFactura .= "<tr>";
        $htmlFactura .= "<td style='width:107mm;'></td>";  //Espacio muerto, aqui va donde dice CPTO.    DESCRIPCION
        $htmlFactura .= "<td style='width:20mm;' align='right' valign='bottom'>CLINICA</td>";  //Encabezado CLINICA
        $htmlFactura .= "<td style='width:20mm;' align='right' valign='bottom'>TERCEROS</td>"; //Encabezado TERCEROS
        $htmlFactura .= "<td style='width:38mm;padding-right:18px' align='right' valign='bottom'>TOTAL</td>"; //Encabezado TOTAL
        $htmlFactura .= "</tr>";
        $htmlFactura .= "</table>";*/

        //Descripcion de pago
        $q = " SELECT movdetcon, movdetval*conmul, movdetnit, movdetfue, movdetdoc, connom, movdetvde "
            ."   FROM cacar, famovdet, facon "
            ."  WHERE carfue = '".$wffa."' "
            ."    AND cardoc = '".$wfactura."'"
            ."    AND carfue = movdetfue "
            ."    AND cardoc = movdetdoc "
            ."    AND movdetcon = concod "
            ."    AND caranu = '0' "
            ."    AND movdetanu = '0'  ";
        $resdes = odbc_do($conexunix,$q);

        // Inicialización de variables para usar en el ciclo
        $limite_conceptos = 10;
        $total_otros_clinica = 0;
        $total_otros_terceros = 0;
        $total_otros = 0;
        $cont = 0;
        $total_clinica = 0;
        $total_terceros = 0;
        $total_descuento = 0;
        $total_clinica_paf = 0;
        $total_terceros_paf = 0;
        $total_descuento_paf = 0;
        $total_desc_paf = 0;
        $cod_paf_con = 0;
        $total = 0;
        $cx_no_pos = false;
        $hay_paf = false;
        $wcxnopos = 'cxnopos';
        $wf_paf = 'PAF-%';

        /** 2012-05-09
         * Este bloque de código se adiciona para validar y consultar la descripción del concepto cuando se va a imprimir una factura
         * y se seleccionó la opción 'NO POS CIRUGÍA'
         */
        if (isset($wnopos) && $wnopos == '2')
        {
            $qcx = " SELECT concod, condes "
                ."   FROM ".$wfacturacion."_000001 "
                ."  WHERE concod = '".$wcxnopos."'"
                ."    AND conest = 'on' ";
                $rescx = mysql_query($qcx,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qcx." - ".mysql_error());
                $numcx = mysql_num_rows($rescx);
                if($numcx>0)
                {
                    $rowcx = mysql_fetch_array($rescx);
                    $wcde = trim($rowcx['condes']);  //Descripcion concepto
                    $cx_no_pos = true;
                }
                else
                {
                    $wcde = 'NO POS';  //Descripcion concepto
                }
        }

        /** 2012-11-16
         * Este bloque de código se adiciona para validar y consultar la descripción del concepto COPAGO
         *
         */
        if (isset($wnopos) && $wnopos == '3')
        {
            $qcx = " SELECT concod, condes "
                ."   FROM ".$wfacturacion."_000001 "
                ."  WHERE concod LIKE '".$wf_paf."'"
                ."    AND conest = 'on' ";
                $rescx = mysql_query($qcx,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qcx." - ".mysql_error());
                $numcx = mysql_num_rows($rescx);
                if($numcx>0)
                {
                    $rowcx = mysql_fetch_array($rescx);
                    $wcde = trim($rowcx['condes']);  //Descripcion concepto
                    $cod_paf_exp = trim($rowcx['concod']);  //Código de concepto tipo PAF
                    $cod_paf_exp = explode('-',$cod_paf_exp);
                    $cod_paf_con = $cod_paf_exp[1];
                    $cx_no_pos = true;
                }
                else
                {
                    $wcde = 'PAF';  //Descripcion concepto
                }
        }

        //$htmlFactura .= "<table cellspacing='0' cellpadding='0' style='width:18.5cm;height:6.6cm;font-size:10pt;' class='monoespaciado'>";
        //$htmlFactura .= "<table cellspacing='0' cellpadding='0' style='width:100%;height:6.6cm;font-size:7pt;' class='monoespaciado'>";
        $htmlFactura .= "<table cellspacing='0' cellpadding='0' style='width:185mm;height:70mm; margin-top:2mm;'  class='monoespaciado' ><tr><td valign='top'>";
        $htmlFactura .= "<table cellspacing='0' cellpadding='0' style='width:185mm;font-size:9pt; valign:top;' class='monoespaciado'>";
        $htmlFactura .= "<tr style='height:4mm'>";
        $htmlFactura .= "<td style='width:14mm;'>&nbsp;</td>";  //Espacio muerto
        $htmlFactura .= "<td style='width:50mm;'>&nbsp;</td>";  //Espacio muerto
        $htmlFactura .= "<td style='width:39mm;'>&nbsp;</td>";  //Espacio muerto
        $htmlFactura .= "<td style='width:22mm;' align='right' valign='bottom'>CLINICA</td>";  //Encabezado CLINICA
        $htmlFactura .= "<td style='width:22mm;' align='right' valign='bottom'>TERCEROS</td>"; //Encabezado TERCEROS
        $htmlFactura .= "<td style='width:38mm;' align='right' valign='bottom'>TOTAL</td>"; //Encabezado TOTAL
        $htmlFactura .= "</tr>";

        while( odbc_fetch_row($resdes) )
        {
            $wcon = odbc_result($resdes,1); //Codigo concepto
            $wval = odbc_result($resdes,2); //Valor del concepto
            $wnit = odbc_result($resdes,3); //NIT
            $wfue = odbc_result($resdes,4); //Fuente
            $wdoc = odbc_result($resdes,5); //Documento
            $descuento = odbc_result($resdes,7);    //Valor de descuento

            // Para empresa en especial que pide no discriminar por tercero y por clinica sino simplemente todo sumado a clinica.
            $entidadNoDiscriminaTerceros = $entidadNoDiscriminaTerceros*1;
            $wcod = $wcod*1;
            $sumar_clinica_tercero = false;
            if($entidadNoDiscriminaTerceros == $wcod)
            {
                $sumar_clinica_tercero = true;
            }

            if (!$cx_no_pos) // Si no se selecciona ver concepto cirugía NO POS - 2012-05-09
            {
                if($wnopos=='1')
                {
                    $q = " SELECT concod, condes "
                    ."   FROM ".$wfacturacion."_000001 "
                    ."  WHERE concod = '".$wcon."'"
                    ."    AND conest = 'on' ";
                    $resnopos = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                    $numnopos = mysql_num_rows($resnopos);
                    if($numnopos>0)
                    {
                        $rownopos = mysql_fetch_array($resnopos);
                        $wcde = trim($rownopos['condes']);  //Descripcion concepto
                    }
                    else
                    {
                        $wcde = trim(odbc_result($resdes,6));   //Descripcion concepto
                    }
                }
                else
                {
                    $wcde = trim(odbc_result($resdes,6));   //Descripcion concepto
                }
            }

            //Consulto datos del tercero (Porcentaje y Nombre)
            $q =   " SELECT connitpor, nitnom "
            ."   FROM faconnit, conit "
            ."  WHERE connitcon = '$wcon'"
            ."    AND connitnit = '$wnit'"
            ."    AND connitnit = nitnit";
            $rescon = odbc_do($conexunix,$q);
            odbc_fetch_row($rescon);
            $wpor = odbc_result($rescon,1);	//Porcentaje tercero
            $wnom = odbc_result($rescon,2);	//Nombre tercero
            $wtde = $wnit." ".substr($wnom,0,11);
            if($wpor && $wpor>0)
            {
                /*
                // Se comenta porque en teter el valor del tercero no está discriminado, es igual al total del concepto
                // entonces para obtener el valor del tercero se hace calculo por medio del porcentaje en faconnit
                // consulto el valor asignado al tercero
                $q =   " SELECT terval "
                ."   FROM facarfac, facardet, teter "
                ."  WHERE carfacfue = '$wfue'"
                ."    AND carfacdoc = '$wdoc'"
                ."    AND carfacanu = '0'"
                ." 	  AND carfacreg = cardetreg "
                ."    AND cardetanu = '0'"
                ."    AND terfue = cardetfue"
                ." 	  AND terdoc = cardetdoc "
                ."	  AND tercon = '$wcon' "
                ."    AND teranu = '0'";
                $rester = odbc_do($conexunix,$q);
                odbc_fetch_row($rester);

                $valor_tercero = odbc_result($rester,1);
                $valor_clinica = $wval - $valor_tercero;
                */

                // Se obtiene porcentaje asociado a la clínica
                $porcentaje_tercero = $wpor;
                $porcentaje_clinica = 100 - $porcentaje_tercero;

                // Se obtiene el valor del tercero y se redondea
                $valor_tercero = $wval * ($porcentaje_tercero / 100);
                $valor_tercero = round($valor_tercero,5);

                // Se obtiene el valor de la clinica y se redondea
                $valor_clinica = $wval * ($porcentaje_clinica / 100);
                $valor_clinica = round($valor_clinica,5);

                // Para empresa en especial que pide no discriminar por tercero y por clinica sino simplemente todo sumado a clinica.
                if($sumar_clinica_tercero)
                {
                  $valor_clinica = $valor_clinica + $valor_tercero;
                  $valor_tercero = 0;
                }

                if($cont>=$limite_conceptos)
                {
                    $total_otros_terceros += $valor_tercero;
                    $total_otros_clinica += $valor_clinica;
                    $total_otros += $valor_tercero+$valor_clinica;
                }
            }
            else
            {
                $wtde = "";
                $valor_tercero = 0;
                $valor_clinica = $wval;
                if($cont>=$limite_conceptos)
                {
                    $total_otros_terceros += $valor_tercero;
                    $total_otros_clinica += $valor_clinica;
                    $total_otros += $valor_tercero+$valor_clinica;
                }
            }

            if($cont<$limite_conceptos && !$cx_no_pos) //********************************************************************
            {
                //$htmlFactura .= "<tr style='height:0.4cm'>";
                $htmlFactura .= "<tr style='height:4mm'>";
                $htmlFactura .= "<td style='width:14mm;font-size:9pt;' align='center'>".$wcon."</td>";               //Codigo concepto
                $htmlFactura .= "<td style='width:50mm;font-size:9pt;' align='left'>".substr($wcde,0,28)."</td>";    //Descripcion concepto
                $htmlFactura .= "<td style='width:39mm;font-size:9pt;' align='left'>".$wtde."</td>";                 //Descripción tercero
                $htmlFactura .= "<td style='width:22mm;font-size:10pt;' align='right'>".number_format( $valor_clinica, 0,'.', ',' )."</td>";    //Valor clínica
                $htmlFactura .= "<td style='width:22mm;font-size:10pt;' align='right'>".(($sumar_clinica_tercero) ? '&nbsp;' : number_format( $valor_tercero, 0,'.', ',' ))."</td>";    //Valor tercero
                $htmlFactura .= "<td style='width:38mm;font-size:10pt;' align='right'>".number_format( $wval, 0,'.', ',' )."</td>";
                $htmlFactura .= "</tr>";
            }

            // if($wcon != $cod_paf_con)
            {
                $total_clinica += $valor_clinica;
                $total_terceros += $valor_tercero;
                $total_descuento += $descuento;
            }
            $total += $wval;

            if($wcon == $cod_paf_con) // 2012-11-16 para sumar todo lo que es del concepto tipo PAF
            {
                $total_clinica_paf += $valor_clinica;
                $total_terceros_paf += $valor_tercero;
                $total_descuento_paf += $descuento;
                $total_desc_paf += ($wval < 0) ? ($wval*(-1)) : $wval;
                $hay_paf = true;

                if($wval < 0)           { $total +=  $wval*(-1); }
                if($valor_clinica < 0)  { $total_clinica +=  $valor_clinica*(-1); }
                if($valor_tercero < 0)  { $total_terceros +=  $valor_tercero*(-1); }
                if($descuento < 0)      { $total_descuento +=  $descuento*(-1); }
            }
            $cont++;
        }

        // Si se seleccionó ver cirugía NO POS, en este bloque de código se muestra un solo concepto y las cifras totalizadas - 2012-05-09
        if ($cx_no_pos)
        {
            // if (isset($wnopos) && $wnopos == '2')
            {
                //$htmlFactura .= "<tr style='height:0.4cm'>";
                $htmlFactura .= "<tr style='height:4mm'>";
                $htmlFactura .= "<td style='width:14mm;font-size:9pt;' align='center'>&nbsp;</td>";               //Codigo concepto
                $htmlFactura .= "<td style='width: 50mm;font-size:9pt;' align='left'>".substr($wcde,0,28)."</td>";    //Descripcion concepto
                $htmlFactura .= "<td style='width: 39mm;font-size:9pt;' align='left'>&nbsp;</td>";                 //Descripción tercero
                $htmlFactura .= "<td style='width: 22mm;font-size:10pt;' align='right'>".number_format( $total_clinica, 0,'.', ',' )."</td>";    //Valor clínica
                $htmlFactura .= "<td style='width: 22mm;font-size:10pt;' align='right'>".number_format( $total_terceros, 0,'.', ',' )."</td>";    //Valor tercero
                $htmlFactura .= "<td style='width 38mm;font-size:10pt;' align='right'>".number_format( $total, 0,'.', ',' )."</td>";
                $htmlFactura .= "</tr>";
            }

            if(isset($wnopos) && $wnopos == '3' && $hay_paf)  // 2012-11-16 para mostrar todo lo que es del concepto tipo PAF
            {
                $q = "  SELECT  concod,connom
                        FROM    facon
                        WHERE   concod = '".$cod_paf_con."'";
                $resDescPaf = odbc_do($conexunix,$q);

                $desconPaf = odbc_result($resDescPaf,2);

                $total_desc_paf = ($total_desc_paf < 0) ? $total_desc_paf: ($total_desc_paf*(-1));

                //$htmlFactura .= "<tr style='height:0.4cm'>";
                $htmlFactura .= "<tr style='height:4mm;'>";
                $htmlFactura .= "<td style='width:14mm;font-size:9pt;' align='center'>".$cod_paf_con."</td>";               //Codigo concepto
                $htmlFactura .= "<td style='width: 50mm;font-size:9pt;' align='left'>".substr($desconPaf,0,28)."</td>";    //Descripcion concepto
                $htmlFactura .= "<td style='width: 39mm;font-size:9pt;' align='left'>&nbsp;</td>";                 //Descripción tercero
                $htmlFactura .= "<td style='width: 22mm;font-size:10pt;' align='right'>".number_format( $total_clinica_paf, 0,'.', ',' )."</td>";    //Valor clínica
                $htmlFactura .= "<td style='width: 22mm;font-size:10pt;' align='right'>".number_format( $total_terceros_paf, 0,'.', ',' )."</td>";    //Valor tercero
                $htmlFactura .= "<td style='width 38mm;font-size:10pt;' align='right'>".number_format( $total_desc_paf, 0,'.', ',' )."</td>";
                $htmlFactura .= "</tr>";

                // Como lo que es tipo PAF se va a relacionar en una fila aparte entonces lo que se sumó en rotales paf se le resta a la sumatoria de todos los conceptos
                $total_clinica = ($total_clinica_paf < 0) ? $total_clinica + $total_clinica_paf : $total_clinica - $total_clinica_paf;
                $total_terceros = ($total_terceros_paf < 0) ? $total_terceros + $total_terceros_paf : $total_terceros - $total_terceros_paf;
                $total = ($total_desc_paf < 0) ? $total + $total_desc_paf : $total - $total_desc_paf; // para el primer caso debe ser (+) porque $total_desc_paf tiene un valor negativo.
            }
            $cont = 0; // Se reinicia el contador para que no muestre 'OTROS SERVICIOS, Ver Anexo' en la factura. El valor de anexos ya está incluido en el total
        }

        if($cont>=$limite_conceptos)
        {
            //$htmlFactura .= "<tr style='height:0.4cm'>";
            $htmlFactura .= "<tr style='height:4mm;'>";
            $htmlFactura .= "<td align='center'></td>";
            $htmlFactura .= "<td align='left' colspan='2' style='font-size:9pt;'>OTROS SERVICIOS, Ver Anexo</td>";    //Descripcion concepto
            $htmlFactura .= "<td align='right' style='font-size:10pt;'>".number_format( $total_otros_clinica, 0,'.', ',' )."</td>";
            $htmlFactura .= "<td align='right' style='font-size:10pt;'>".number_format( $total_otros_terceros, 0,'.', ',' )."</td>";
            $htmlFactura .= "<td align='right' style='font-size:10pt;'>".number_format( $total_otros, 0,'.', ',' )."</td>";
            $htmlFactura .= "</tr>";
        }

        //$htmlFactura .= "<tr style='height:0.7cm'>";
        $htmlFactura .= "<tr style='height:4mm;'>";
        $htmlFactura .= "<td align='center'></td>";
        $htmlFactura .= "<td align='left' colspan='2' valign='middle'>TOTAL GENERAL DE LOS SERVICIOS:</td>";	//Descripcion concepto
        $htmlFactura .= "<td align='right' valign='middle' style='border-top: 1px solid #000000; font-size:10pt;'>".number_format( $total_clinica, 0,'.', ',' )."</td>";	//Valor clínica
        $htmlFactura .= "<td align='right' valign='middle' style='border-top: 1px solid #000000; font-size:10pt;' >".number_format( $total_terceros, 0,'.', ',' )."</td>";	//Valor tercero
        $htmlFactura .= "<td align='right' valign='middle' style='border-top: 1px solid #000000; font-size:10pt;'>".number_format( $total, 0,'.', ',' )."</td>";
        $htmlFactura .= "</tr>";
       /* $htmlFactura .= "<tr>";
        $htmlFactura .= "<td colspan='6' align='center'>&nbsp;</td>";
        $htmlFactura .= "</tr>";*/

        $htmlFactura .= "</table></td></tr></table>";

        //se hace count para saber si tiene resultados los copagos o cuota moderadora
        $q =    " SELECT count(*) "
            ."   FROM anantfac "
            ."  WHERE antfacffa = '".$wffa."'"
            ."    AND antfacdfa = '".$wfactura."'
                  AND antfacanu = '0'";
        $resant1 = odbc_do($conexunix,$q);
        $antfacval1 = odbc_result($resant1,1);

        if ($antfacval1>0)
        {
            //Consulta de copagos o cuota moderadora
            $q =    " SELECT SUM(antfacval) "
                ."   FROM anantfac "
                ."  WHERE antfacffa = '".$wffa."'"
                ."    AND antfacdfa = '".$wfactura."'
                      AND antfacanu = '0'";
            $resant = odbc_do($conexunix,$q);
            odbc_fetch_row($resant);
            $antfacval = odbc_result($resant,1);	//Copago o cuota moderadora
        }
        else
        {
            $antfacval=0;
        }

        if(!isset($antfacval) || !$antfacval)
        $antfacval = 0;

        if($wind=='P')
        {
        $ant_exc = $antfacval;
        $cop_cmo_frq = 0;
        }
        else
        {
        $ant_exc = 0;
        $cop_cmo_frq = $antfacval;
        }

        $parcial = $total;
        $subtotal = $total-$total_descuento;
        $iva = 0;   // IVA siempre es cero ya que en hospitalización no hay cargos que impliquen IVA
        $total_neto = $subtotal+$iva-$cop_cmo_frq-$ant_exc;

        //Forma de pago
        $htmlFactura .= "<table cellspacing='0' cellpadding='0' style='width:185mm;font-size:9pt;height:30mm;' class='monoespaciado'>";
        $htmlFactura .= "<tr style='height:3mm;' valign='top'>";  //***********************************************
        $htmlFactura .= "<td>&nbsp;</td>";
        $htmlFactura .= "</tr>";
        //$htmlFactura .= "<tr style='height:4.40cm'>";
        $htmlFactura .= "<tr style='height:27mm;'>";
        $htmlFactura .= "<td style='width:124mm; margin-top:4mm;' valign='top'><br>".montoescrito( $total_neto )."</td>";  //***********************************************
        //$htmlFactura .= "<td style='width:12.4%;' rowspan='3'></td>";
        $htmlFactura .= "<td style='width:23mm;'></td>";
        //$htmlFactura .= "<td style='width 38mm;' rowspan='3' align='right' valign='top'><br>".number_format( $total, 0,'.', ',' )."<br>".number_format( $total_descuento, 0,'.', ',' )."<br>".number_format( $subtotal, 0,'.', ',' )."<br>".number_format( $iva, 0,'.', ',' )."<br>".number_format( $ant_exc, 0,'.', ',' )."<br>".number_format( $cop_cmo_frq, 0,'.', ',' )."<br>".number_format( $total_neto, 0,'.', ',' )."</td>";
        $htmlFactura .= "<td style='width 38mm;' align='right' valign='top'><br>".number_format( $total, 0,'.', ',' )."<br>".number_format( $total_descuento, 0,'.', ',' )."<br>".number_format( $subtotal, 0,'.', ',' )."<br>".number_format( $iva, 0,'.', ',' )."<br>".number_format( $ant_exc, 0,'.', ',' )."<br>".number_format( $cop_cmo_frq, 0,'.', ',' )."<br>".number_format( $total_neto, 0,'.', ',' )."</td>";
        $htmlFactura .= "</tr>";
        $htmlFactura .= "</table>";

        $observacionFinal = "";
        //Consulta para saber cuantas lineas de observaciones tiene la factura

        $query =" SELECT COUNT(*) "
               ." FROM cacarobs "
               ." WHERE carobsfue = '".$wffa."'"
               ." AND carobsdoc = '".$wfactura."' ";

        $res = odbc_do($conexunix,$query);
        $lineas= odbc_result($res,1);
        //$htmlFactura .= $lineas;

        if($lineas==0)
        {
            $observacionFinal = "";
        }
        else if ($lineas==1)
        {
            //Consulta de observaciones
             $q =" SELECT carobsdes "
                ." FROM cacarobs "
                ." WHERE carobsfue = '".$wffa."'"
                ." AND carobsdoc = '".$wfactura."' "
                ." AND carobsnum='1'";
            $resobs = odbc_do($conexunix,$q);
            odbc_fetch_row($resobs);

            $observacion = odbc_result($resobs,1);
        }
        else if ($lineas==2)
        {
            //Consulta de observaciones
             $q =" SELECT carobsdes "
                ." FROM cacarobs "
                ." WHERE carobsfue = '".$wffa."'"
                ." AND carobsdoc = '".$wfactura."' "
                ." AND carobsnum='1'";
            $resobs = odbc_do($conexunix,$q);
            odbc_fetch_row($resobs);

            //Consulta de observaciones
             $q1 =" SELECT carobsdes "
                ." FROM cacarobs "
                ." WHERE carobsfue = '".$wffa."'"
                ." AND carobsdoc = '".$wfactura."' "
                ." AND carobsnum='2'";
            $resobs1 = odbc_do($conexunix,$q1);
            odbc_fetch_row($resobs1);

            $observacion = odbc_result($resobs,1)." ".odbc_result($resobs1,1);
        }
        else
        {
            //Consulta de observaciones
             $q =" SELECT carobsdes "
                ." FROM cacarobs "
                ." WHERE carobsfue = '".$wffa."'"
                ." AND carobsdoc = '".$wfactura."' "
                ." AND carobsnum='1'";
            $resobs = odbc_do($conexunix,$q);
            odbc_fetch_row($resobs);

            //Consulta de observaciones
             $q1 =" SELECT carobsdes "
                ." FROM cacarobs "
                ." WHERE carobsfue = '".$wffa."'"
                ." AND carobsdoc = '".$wfactura."' "
                ." AND carobsnum='2'";
            $resobs1 = odbc_do($conexunix,$q1);
            odbc_fetch_row($resobs1);

            //Consulta de observaciones
             $q2 =" SELECT carobsdes "
                ." FROM cacarobs "
                ." WHERE carobsfue = '".$wffa."'"
                ." AND carobsdoc = '".$wfactura."' "
                ." AND carobsnum='3'";
            $resobs2 = odbc_do($conexunix,$q2);
            odbc_fetch_row($resobs2);

            $observacion = odbc_result($resobs,1)." ".odbc_result($resobs1,1)." ".odbc_result($resobs2,1);
        }


        $numCaracteres = strlen($observacion);

        if ($numCaracteres > 150)
        {
            $observacionFinal = substr( $observacion, 0, 150 );
        }
        else
        {
            $observacionFinal=$observacion;
        }

        //*************************************************************************************

        // if(!isset($observacion) || !$observacion)
        // $observacionFinal = "";

        $htmlFactura .= "<table cellspacing='0' cellpadding='0' style='width:185mm;height:18mm; font-size:9pt;' class='monoespaciado'>";
        $htmlFactura .= "<tr>";
        $htmlFactura .= "<td style='width:109mm;'>&nbsp;</td>";	//Espacio muerto, aqui va donde dice  ELABORADO POR      RECIBI CONFORME
        //$htmlFactura .= "<td style='width:7.3cm' align='left' valign='top'><font size='1'>".$observacion."</font></td>";		//Observaciones

        /* TEMPORALMENTE SE COMENTA LA OBSERVACIÓN FINAL PARA MOSTRAR LA RESOLUCIÓN DE LA DIAN */
        //$htmlFactura .= "<td style='width:76mm;margin-top:10mm;' align='left' valign='top'  ><br>".$observacionFinal."</td>"; // COMENTADO TEMPORALMENTE, al activarlo se deberá comentar el td siguiente

       if( isset( $nombre_plan ) ){
        //$datos_plan = $nombre_plan." MES ".$mes_plan." DEL ".$anioHoy;
        $datos_plan = $nombre_plan." MES DE".$mes_plan." DEL ".$ano_plan;
       }
       $htmlFactura .= "<td style='width:76mm;margin-top:10mm;' align='left' valign='top'>";

		$conResolucionDian = consultarAliasPorAplicacion($conex, $wemp_pmla, 'imprimirFacturaUnixConResolucionDian');
		if($conResolucionDian == 'on')
		{
			$htmlFactura .= "
                <font size='2'>
                  RRESOLUCION DIAN NO. 110000619627<br>
                  FECHA 2015/03/06<br>
                  NUMERACION DEL 4164001 AL 5000000 HABILITA.
                </font>";
		}
		else
		{
			$htmlFactura .= "<br>".$observacionFinal."</td>";
		}

		$htmlFactura .= "
              </td>"; // Poner entre comentarios este TD si se activa el TD de $observacionFinal */

        $htmlFactura .= "</tr>";
        $htmlFactura .= "</table>";

        $q =    " SELECT logusu, logter, logfec "
            ."   FROM falog "
            ."  WHERE logva1 = '".$wffa."'"
            ."    AND logva2 = '".$wdoc."'";
        $reslog = odbc_do($conexunix,$q);
        if(odbc_fetch_row($reslog))
        {
            $logusu = odbc_result($reslog,1);   //Usuario
            $logter = odbc_result($reslog,2);   //
            $logfec = odbc_result($reslog,3);   //Fecha
            $logstrfec = explode(" ",$logfec);
            $logfec = $logstrfec[0];
            $loghor = $logstrfec[1];    //Hora
            $logstrper = explode("-",$logfec);
            $logper = $logstrper[0]."-".$logstrper[1];  //Periodo
        }
        else
        {
            $q =    " SELECT logusu, logter, logfec "
                ."   FROM aylog "
                ."  WHERE logva1 = '".$wffa."'"
                ."    AND logva2 = '".$wdoc."'";
            $reslog = odbc_do($conexunix,$q);
            if(odbc_fetch_row($reslog))
            {
                $logusu = odbc_result($reslog,1);   //Usuario
                $logter = odbc_result($reslog,2);   //
                $logfec = odbc_result($reslog,3);   //Fecha
                $logstrfec = explode(" ",$logfec);
                $logfec = $logstrfec[0];
                $loghor = $logstrfec[1];    //Hora
                $logstrper = explode("-",$logfec);
                $logper = $logstrper[0]."-".$logstrper[1];  //Periodo
            }
            else
            {
                $logusu = "";   //Usuario
                $logter = "";   //
                $logfec = "";   //Fecha
                $loghor = "";   //Hora
                $logper = "";   //Periodo
            }
        }

       // $htmlFactura .= "<table cellspacing='0' cellpadding='0' style='width:18.5cm;font-size:10pt;' class='monoespaciado'>";
        $htmlFactura .= "<table cellspacing='0' cellpadding='0' style='width:185mm; height:3mm; font-size:8pt;' class='monoespaciado'>";
        //$htmlFactura .= "<tr style='height:0.8cm'>";
        $htmlFactura .= "<tr>";
        $htmlFactura .= "<td align='left' valign='bottom' style='margin-top:1mm;'><br>Fecha: ".$logfec." &nbsp;  &nbsp; Hora: ".$loghor." &nbsp;  &nbsp; Usu.: ".$logusu." &nbsp; &nbsp; Term.: ".$logter." &nbsp;  &nbsp;  Per.: ".$logper." </td>";		//Pie de pagina de la factura
        $htmlFactura .= "</tr>";
        $htmlFactura .= "</table>";
        $htmlFactura .= "</div>";

        $existen_facturas = 'on';

        auditoria( $wfactura, $wced, $whis,$wing, date("Y-m-d"), $wval, $wpaquete, $wparam );

        // return;
    }
   // echo $htmlFactura;
    if($existen_facturas=='off')
        $htmlFactura .= "<div align='center'><br>No se encontraron datos para la factura</div>";
      else{
            //$htmlFactura .= "</html>";
            $wnombrePDF = $wemp_pmla."_".trim($wusuario)."_".$wfactura;
            //CREAR UN ARCHIVO .HTML CON EL CONTENIDO CREADO
            $dir = 'facturas';
            if(is_dir($dir)){ }
            else { mkdir($dir,0777); }
            $archivo_dir = $dir."/".$wnombrePDF.".html";
            if(file_exists($archivo_dir)){
              unlink($archivo_dir);
            }
            $f           = fopen( $archivo_dir, "w+" );
            fwrite( $f, $htmlFactura);
            fclose( $f );

            $respuesta = shell_exec( "./generarPdf_facturas_unix.sh ".$wnombrePDF );

            /*crearPDF();
            agregarPaginaPDF();
            agregarFacturaPdf( $htmlFactura );
            imprimirPDF( $wnombrePDF."_2" );*/


            $htmlFactura = "<br><br><br><font size='5' color='#2A5DB0'>Factura nro: ".$wfactura."</font>"
                              ."<br><br>"
                            ."<object type='application/pdf' data='facturas/".$wnombrePDF.".pdf' pdf#toolbar=1&amp;navpanes=0&amp;scrollbar=1 width='900' height='700'>"
                              ."<param name='src' value='facturas/".$wnombrePDF."' pdf#toolbar=1&amp;navpanes=0&amp;scrollbar=1 />"
                              ."<p style='text-align:center; width: 60%;'>"
                                ."Adobe Reader no se encuentra o la versión no es compatible, utiliza el icono para ir a la página de descarga <br />"
                                ."<a href='http://get.adobe.com/es/reader/' onclick='this.target=\"_blank\">"
                                  ."<img src='../../images/medical/root/prohibido.gif' alt='Descargar Adobe Reader' width='32' height='32' style='border: none;' />"
                                ."</a>"
                              ."</p>"
                            ."</object>";
      }

    //$htmlFactura .= "</div>";
   // $htmlFactura .= "</div>";
    $wactualiz=" 2016-09-22 ";
    echo "<div align='center'>";
    encabezado("Imprimir Factura Unix",$wactualiz, "clinica");
    echo "<br>";
    echo $htmlFactura;
    echo "<br><br><input type='button' value='retornar' onclick='regresar();' ><br>";
    echo "<br><input type='button' value='Cerrar Ventana' onclick='cerrarPagina();' ><br>";
    echo "</div>";
    odbc_close($conexunix);
}


//Función que valida si el centro de costo de la factura se encuentra configurado para que tome la fecha de ingreso desde evolución
function validarFechasCentroCosto($cco, $historia, $ingreso, $fechaFactura){

	global $conex;
	global $wemp_pmla;

	$arrFechasEvolucion = array();
	//Se consultan los centros de costos configurados para que impriman la fecha de ingreso desde evolución
		$todosCcoConfig = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ccoFechaIngresoPorEvolucion');

		//Se consulta si el centro de costos de la factura actual se encuentra en los centros de costos que están congigurados para tomar fecha de ingreso desde la evolución
		$existeCcoConfigurado = strpos($todosCcoConfig, $cco."-");

		//Si existe ese centro de costo en la configuración se consulta el valor para el formulario y se saca la fecha maxima de la tabla 000036
		if($existeCcoConfigurado !== false){
			$numeroTablaFormulario = "";
			$arrCco = explode(";", $todosCcoConfig);

			foreach($arrCco as $value){
				$newArr = explode("-", $value);

				if(isset($newArr[0]) && $newArr[0] === $cco){

					//2016-03-14 se cambia para que pueda tomar de uno o varios formularios según este configurado en el parámetro ccoFechaIngresoPorEvolucion
					$arrAllForm = explode("_",$newArr[1]);
					$arrForm = implode("','",$arrAllForm);

					$numeroTablaFormulario = $arrForm ;
					break;
				}
			}

			if($numeroTablaFormulario != ""){
				$prefHce  = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
				$q = "SELECT
						  hc.fecha_data as fecha_evolucion
						, hc.Firpro as formulario
						, Encdes nombre
					  FROM ".$prefHce."_000036 hc
					  INNER JOIN ".$prefHce."_000001 h1 ON Encpro = Firpro
					  WHERE Firhis = '".$historia."'
					  AND Firing = '".$ingreso."'
					  AND Firpro IN('".$numeroTablaFormulario."')
					  AND hc.fecha_data <= '".$fechaFactura."'
				  ";
				$result = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

				while($row = mysql_fetch_assoc($result)) {
					$arrFechasEvolucion[] = array("fecha" => $row["fecha_evolucion"], "formulario" => $row["formulario"], "nombre" => $row["nombre"]);
				}
			}
		}

	return $arrFechasEvolucion;
}

session_start();

if (!isset($user))
{
    if(!isset($_SESSION['user']))
        session_register("user");
}

if(!isset($_SESSION['user']))
{
    echo "error";
}
else
{
    /*

    include_once("root/comun.php");
    include_once("root/montoescrito.php");

    


    //@$conexunix = odbc_pconnect('informix','informix','sco') or die("No se ralizo Conexion con el Unix");	//2012-02-29
    @$conexunix = odbc_connect('facturacion','informix','sco') or die("No se ralizo Conexion con el Unix");*/

    $pos = strpos($user,"-");
    $wusuario = substr($user,$pos+1,strlen($user));
    $pdf = "";

    $entidadNoDiscriminaTerceros    = consultarAliasPorAplicacion($conex, $wemp_pmla, 'imp_fact_unix_empresa_no_tercero');
    global $entidadNoDiscriminaTerceros;

                                                        // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
    $wactualiz=" 2016-09-22 ";                          // Aca se coloca la ultima fecha de actualizacion de este programa //
                                                        // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //


    echo "<form name='impfacunix' id='impfacunix' action='imp_factura_unix.php' method=post>";

    echo "<input type='HIDDEN' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";

    if(!isset($wparam))
    { $wparam = "0"; }

    mostrar_empresa($wemp_pmla);

    if ( !isset($wenvia))
    {
        encabezado("Imprimir Factura Unix",$wactualiz, "clinica");

        if($wparam!="1")
        seleccionarPaquete($wpaq);

        echo "<br>";
        echo "<center><table cellspacing='0' cellpadding='0' border='0'>";

        if($wparam=="1")
        {
            // Consulta de impresora asociada al usuario actual
            $q =   " SELECT cimnom "
            ."   FROM ".$wfacturacion."_000003 "
            ."  WHERE cimusu = '".$wusuario."' "
            ."	  AND cimest = 'on' ";
            //echo $q."<br>";
            $res_impusu = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
            $num_impusu = mysql_num_rows($res_impusu);
            $row_impusu = mysql_fetch_array($res_impusu);
            $impusu = $row_impusu[0];

            // Consulta de impresoras
            $q =   " SELECT cimnom "
            ."   FROM ".$wfacturacion."_000003 "
            ."  WHERE cimest = 'on' "
            ."	GROUP BY cimnom ";
            $res_imps = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
            $num_imps = mysql_num_rows($res_imps);

            // Selección de impresora
            echo "<tr><td height='31'><b> Impresora </b></td>";

            // Campo select de impresoras
            echo "<td>";
            echo "<select name='wimpresora' id='wimpresora'>";
            for ($i=1;$i<=$num_imps;$i++)
            {
                $row_imps = mysql_fetch_array($res_imps);
                if(isset($impusu) && $impusu != $row_imps[0])
                {
                    echo "<option value='".$row_imps[0]."'>".$row_imps[0]."</option>";
                }
                else
                {
                    echo "<option value='".$row_imps[0]."' selected>".$row_imps[0]."</option>";
                }
            }
            echo "</select></td></tr>";
        }

		if($wparam != "1"){
			$isChequeo = 1;
		}else{
			$isChequeo = 0;
		}

        echo "  <tr>
                    <td height='31'><b>Fuente de Factura:</b></td>
                    <td><input type='text' name='wffa' id='wffa' size='3' maxlength='4' value='20'></td>
                </tr>
                <tr>
                    <td height='31'><b>Nro de Factura:</b></td>
                    <td><input type='text' name='wfactura' id='wfactura' size='15' onkeyup='limpiarDivPlanes(".$isChequeo.");'></td>
                </tr>";
        if($wparam=="1")
        {
        echo "<tr>
                <td colspan='3'>
                  <div style='color: #676767;font-family: verdana;font-size:9; background-color: #E4E4E4; text-align:center;' >
                     <img width='15' height='15' src='../../images/medical/root/Advertencia.png'/>
                     [?] Imprimir una factura PAF sin seleccionar la opción correspondiente puede generar errores en el programa.
                  </div>
                 </td>
            </tr>";
			$conResolucionDian = consultarAliasPorAplicacion($conex, $wemp_pmla, 'imprimirFacturaUnixConResolucionDian');
            echo "
            <tr>
                <td align='center' colspan='2'>
                    <table>
                        <tr style='font-size:8pt;'>
                            <td height='31' align='center'><input type='radio' name='wnopos' value='1'> NO POS (Otros)</td>
                            <td height='31' align='center'><input type='radio' name='wnopos' value='2'> NO POS (Cirug&iacute;a)</td>
                            <td height='31' align='center'><input type='radio' name='wnopos' value='3'> Factura (PAF)</td>
                            <td height='31' align='center'><input type='radio' name='wnopos' value='0'> Normal</td>
                        </tr>
						<tr style='font-size:8pt;'>
                            <td colspan='3' height='31' align='center'>Mostrar resolución DIAN:&nbsp;<b>".$conResolucionDian."</b></td>
                        </tr>
                    </table>
                </td>
            </tr>";
        }

		//Verónica Arismendy
		//Se agrega la parte en caso de que aplique para que el usuario seleccione la fecha de ingreso y de salida con la que dese imprimr la factura
		echo "<tr><td colspan='2'><br><div id='fechasEvolucion'></div></td><tr>	";

        echo "<tr><td colspan='2'><br><div id='div_planes' align='center'></div></td></tr>";

		if($wparam != "1"){
			 echo "<tr><td height='37' valign='bottom' colspan='2' align='center'><input type='button' id='imprimir' name='imprimir' value='Imprimir' onclick='generarFactura();'></td></tr>";
		}else{
			 echo "<tr><td height='37' valign='bottom' colspan='2' align='center'><input type='button' id='imprimir' name='imprimir' value='Imprimir' onclick='generarFactura();' disabled></td></tr>";
		}

        echo "<tr><td colspan='2'>&nbsp;</td></tr><tr><td align='center' colspan='2'><input type='button' value='Cerrar Ventana' onclick='cerrarPagina();' ></td></tr>";
        echo "<input type='HIDDEN' name='wparam' id='wparam' value='".$wparam."'>";
        echo "<input type='HIDDEN' name='wenvia' value='1'>";
        echo "<input type='HIDDEN' name='nombre_plan' id='nombre_plan' value=''>";
        echo "<input type='HIDDEN' name='imprimirDetallePaquete' id='imprimirDetallePaquete' value=''>";
        echo "<input type='HIDDEN' name='mes_plan' id='mes_plan' value=''>";
        echo "<input type='HIDDEN' name='ano_plan' id='ano_plan' value=''>";

    }
    else
    {
        if(isset($wnopos)) {
            $wnopos = $wnopos;
        } else {
            $wnopos = '0';
        }

        if(isset($wffa)) {
            $wffa = $wffa;
        } else {
            $wffa = '20';
        }

		//2016-04-05 Verónica Arismendy
		//Se valida si aplicaba la opción de selecciona fecha para mostrar en fecha ingreso y fecha salida
		if(isset($fecha_ingreso_evolucion)) {
            $fechaIngresoEvolucion = $fecha_ingreso_evolucion;
        } else {
            $fechaIngresoEvolucion = '';
        }

        echo "<input type='HIDDEN' name='wfactura' value='".$wfactura."'>";
        if(isset($wimpresora)){
			echo "<input type='HIDDEN' name='wimpresora' value='".$wimpresora."'>";
		}

        echo "<input type='HIDDEN' name='wnopos' value='".$wnopos."'>";
        echo "<input type='HIDDEN' name='wffa' value='".$wffa."'>";


        //On
        // echo $wfactura."<br>";

        ///** 2014-04-21 **///
        $query = "SELECT detval
                    FROM root_000051
                  WHERE detapl = 'servicioFisiatria'";
        $rs    = mysql_query( $query, $conex );
        $row   = mysql_fetch_array( $rs );
        $servicioFisiatria = $row[0];

        if( isset( $nombre_plan ) and $nombre_plan != "" ){
          unset($wparam);
          $anioHoy = date('Y');
		  if(!isset($ano_plan))
		  {
			$ano_plan =  $anioHoy;
		  }
          // $wpaquete  = $nombre_plan." MES ".$array_meses[$mes_plan]." DEL ".$anioHoy;
          $wpaquete  = $nombre_plan." MES DE ".$array_meses[$mes_plan]." DEL ".$ano_plan;
        }
        if($wparam!="1")
        {
            imprimir_factura($wfactura, $wparam);
        }
        else
        {
            // Se limpia la fuente de factura de caracteres especiales que puedan romper el query por este campo
            $wffa = str_replace('"','',str_replace("'","",trim($wffa)));
            $wffa = str_replace('\\','',str_replace("/","",$wffa));

            imprimir_factura_detalle($wfactura, $wparam, $wnopos, $wimpresora, $wffa, $fechaIngresoEvolucion);
        }
        echo "</div>";
    }

    echo "</form>";

	odbc_close($conexunix);
	odbc_close_all();
} // if de register
?>
</html>