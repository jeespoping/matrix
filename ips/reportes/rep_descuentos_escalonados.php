<?php
include_once("conex.php");
if (!isset($consultaAjax))
{
    if(isset($accion) && isset($form)) 
    { 
       if(isset($accion) && $accion == 'exportar_excel') // se debe diferenciar por los dos o por otro diferente a $accion puesto que desde talento.php ya esta seteado $accion 
       { 
           header("Content-type: application/vnd.ms-excel; name='excel'"); 
           header("Content-Disposition: filename=reporte_desc_escal_".date("Ymd").".xls"); 
           header("Pragma: no-cache"); 
           header("Expires: 0"); 

           echo $_POST['datos_a_enviar']; 
           return; 
       } 
    }
?>
<head>
  <title>REPORTE DESCUENTOS ESCALONADOS</title>
</head>
<body>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>

<script type="text/javascript">

$.datepicker.regional['esp'] = {
        closeText: 'Cerrar',
        prevText: 'Antes',
        nextText: 'Despues',
        monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
        'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
        monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
        'Jul','Ago','Sep','Oct','Nov','Dic'],
        dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
        dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
        dayNamesMin: ['D','L','M','M','J','V','S'],
        weekHeader: 'Sem.',
        dateFormat: 'yy-mm-dd',
        yearSuffix: '',
        changeYear: true,
        changeMonth: true,
        yearRange: '-20:+0'
        };
$.datepicker.setDefaults($.datepicker.regional['esp']);




function ocultar_tablas()
{
   $("#datos_reporte").hide();
   $("#div_exportar").hide();

}

function mostrar_tipo(tipo)
{
    
    switch(tipo)
        {
        case 'cedula':
            $('#tipoc_lab').hide(500);
            $('#cedula_persona').val('');
            $('#laboratorio').val('');
            $('#tipoc_cedula').show(500);            
          break;
        case 'lab':
            $('#tipoc_cedula').hide(500);
            $('#laboratorio').val('');
            $('#cedula_persona').val('');
            $('#tipoc_lab').show(500);            
          break;
        default:
          break;
        };  
        
}



function mostrar_datos()
 {

    $.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
                    css: 	{
                                width: 	'auto',
                                height: 'auto'
                            }
                });

    var wlaboratorio = $("#laboratorio").val();
    var wcedula = $("#cedula_persona").val();    
    var wbasedatos = $("#wbasedatos").val();
    var wemp_pmla = $("#wemp_pmla").val();
    var wnombre_lab = $('#laboratorio option:selected').html();
    var wfecha_inicial = $("#wfecha_inicial").val();
    var wfecha_final = $("#wfecha_final").val();   

    //Verifica que este seleccoinado un laboratorio, si el radio button de laboratorio esta seleccionado.
    if($('#tipo_consulta_lab').is(':checked') && wlaboratorio == '')
        {
             alert('Debe seleccionar un laboratorio.');
             $.unblockUI();
             return;
        }
    
    //Verifica que ingrese una cedula, si el radio button de cedula esta seleccionado.
    if($('#tipo_consulta_ced').is(':checked') && wcedula == '')
        {
            alert('Debe ingresar una cédula.');
            $.unblockUI();
            return;
        }
     

    $.post("rep_descuentos_escalonados.php",
            {
                consultaAjax:       'mostrar_datos',
                wemp_pmla:          wemp_pmla,
                wbasedato:          wbasedatos,
                wlaboratorio:       wlaboratorio,
                wnombre_lab:        wnombre_lab,
                wfecha_inicial:     wfecha_inicial,
                wfecha_final:       wfecha_final,
                wcedula:            wcedula

            }
            ,function(data_json) {

                if (data_json.error == 1)
                {
                    alert(data_json.mensaje);
                    $.unblockUI();
                    return;
                }
                else
                {
                    $('#div_exportar').show();      
                    $("#datos_desc_escalonados").html(data_json.table);
                    $.unblockUI();
                }

        },
        "json"
    );
}

$(document).ready(function() {
    $("#wfecha_inicial").datepicker({
      showOn: "button",
      buttonImage: "../../images/medical/root/calendar.gif",
      buttonImageOnly: true,
	  maxDate:"+1D"
    });

    $("#wfecha_final").datepicker({
      showOn: "button",
      buttonImage: "../../images/medical/root/calendar.gif",
      buttonImageOnly: true,
	  maxDate:"+1D"
    });
    
    $("#tipoc_lab").show();
    
    /** Inicializa la funcionalidad para generar la exportación a excel. */ 
   $(".botonExcel").click(function(event) {      
       $("#datos_a_enviar").val( $("<div>").append( $("#datos_reporte").eq(0).clone()).html()); 
       $("#FormularioExportacion").submit(); 
   });
    
});
</script>

<?php
}
/* ***********************************************************************************
   * PROGRAMA PARA GENERAR EL REPORTE DE DESCUENTOS ESCALONADOS EN UN LAPSO DE FECHAS
   ***********************************************************************************/

//==================================================================================================================================
//PROGRAMA                   : rep_descuentos_escalonados.php
//AUTOR                      : Jonatan Lopez Aguirre.
//FECHA CREACION             : Junio 27 de 2013
//FECHA ULTIMA ACTUALIZACION :
  $wactualiz="Octubre 22 de 2013";
//DESCRIPCION
//====================================================================================================================================\\
//Este programa entrega un reporte de los descuentos escalonados por laboratorio en un laspo determinado   																							  \\
//====================================================================================================================================\\



if(!isset($_SESSION['user']))
	echo "Error, Usuario NO Registrado";
else
{

include_once("root/comun.php");
include_once("movhos/movhos.inc.php");





$wbasedatos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'farpmla');

// Se incializan variables de fecha hora y usuario
if (strpos($user, "-") > 0)
    $wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
    else
        $wuser=$user;

//Trae el nombre del medico.
function medico($wnroventa)
{

    global $conex;
	global $wbasedatos;

    $query = "SELECT Vmpmed
                FROM ".$wbasedatos."_000050
               WHERE vmpvta = '".$wnroventa."'";
    $res = mysql_query( $query, $conex) or die( mysql_errno()." - Error en el query $query - ".mysql_error() );
    $row = mysql_fetch_array($res);

    return $row['Vmpmed'];

}

//Trae el nombre de la especialidad del medico.
function especialidad($wespecialidad)
{

    global $conex;
	global $wbasedatos;
		
    $query = "SELECT Espnom
                FROM ".$wbasedatos."_000053
               WHERE Espcod = '".$wespecialidad."'";
    $res = mysql_query( $query, $conex) or die( mysql_errno()." - Error en el query $query - ".mysql_error() );
    $row = mysql_fetch_array($res);

    return $row['Espnom'];

}

		
//Trae el nombre del cliente - paciente.
function nombre_cliente($wdoc_pac)
{

    global $conex;
	global $wbasedatos;

    $query = "SELECT Clinom
                FROM ".$wbasedatos."_000041
               WHERE clidoc = '".$wdoc_pac."'";
    $res = mysql_query( $query, $conex) or die( mysql_errno()." - Error en el query $query - ".mysql_error() );
    $row = mysql_fetch_array($res);

    return $row['Clinom'];

}		
		
		
//Trae el codigo de barras del articulo, y el codigo del articulo.
function codigo_ean($warticulo, $wbasedatos)
{

    global $conex;

    $query = "SELECT Axpcpr
                FROM ".$wbasedatos."_000009
               WHERE SUBSTRING_INDEX(Axpart, '-', 1) = '".$warticulo."'";
    $res = mysql_query( $query, $conex) or die( mysql_errno()." - Error en el query $query - ".mysql_error() );
    $row = mysql_fetch_array($res);

    return $row['Axpcpr'];

}

//Trae la sucursal de acuerdo al usuario que grabo
function sucursal($wsucursal, $wbasedatos)
{

    global $conex;

    $query = "SELECT SUBSTRING_INDEX(Cjecco, '-', -1) as sucursal
                FROM ".$wbasedatos."_000030
               WHERE Cjeusu = '".$wsucursal."'";
    $res = mysql_query( $query, $conex) or die( mysql_errno()." - Error en el query $query - ".mysql_error() );
    $row = mysql_fetch_array($res);

    return $row['sucursal'];

}

//Esta función muestra la informacion del laboratorio seleccionado o de la cedula ingresada.
function mostrar_datos($wemp_pmla, $wbasedatos, $wlaboratorio, $wnombre_lab, $wfecha_inicial, $wfecha_final, $wcedula)
    {

    global $conex;
	$wfecha_actual = date ("Y-m-d");
	
    $datamensaje = array('mensaje'=>'', 'error'=>0);
    
    if(trim($wlaboratorio) != '')
    {
    //Se consulta en el maestro de articulos relacionados por laboratorio, cuantos tiene.
    $query_lab = "SELECT Fecha_data, Rdedoc, Rdenve, Rdelab, Rdeart, Rdepde, sum(Rdeval) as valor, sum(Rdecan) as cantidad, Rdeniv, Rdeest, Seguridad as sucursal, Rdeffa, Rdenfa 
                    FROM ".$wbasedatos."_000169
                   WHERE Fecha_data BETWEEN '".$wfecha_inicial."' AND '".$wfecha_final."'                     
                     AND Rdelab LIKE '%".$wlaboratorio."%'
				GROUP BY Rdedoc, Rdeart
                ORDER BY Fecha_data DESC, Hora_data DESC";    
    }
    else
    {
		//Consulta por cedula.
        if(trim($wcedula) != '')
            {
            $query_lab = "SELECT Fecha_data, Rdedoc, Rdenve, Rdelab, Rdeart, Rdepde, sum(Rdeval) as valor, sum(Rdecan) as cantidad, Rdeniv, Rdeest, Seguridad as sucursal, Rdeffa, Rdenfa 
                            FROM ".$wbasedatos."_000169
                           WHERE Rdedoc = '".$wcedula."' 
						GROUP BY Rdedoc, Rdeart						   
                        ORDER BY Fecha_data DESC, Hora_data DESC";
            }
    }
    
    $res_lab = mysql_query( $query_lab, $conex) or die( mysql_errno()." - Error en el query $query_lab - ".mysql_error() );
    $num_lab = mysql_num_rows($res_lab);
   
    
    $texto_html .= "<br>";	
    $texto_html .= "<table id='datos_reporte' style='text-align: center;' border=1 cellspacing=0>";
    
    //Si escogieron cedula muestra una columna adicional para imprimir la factura.
    if(trim($wcedula) != '')
       {
        $wnivel_actual = '<td>Factura</td>';        
       }
    
		$texto_html .= "<tr class=encabezadotabla>
								<td>Mes/Año</td>
								<td>Nit cliente asociado</td>
								<td>Nombre del paciente</td>
								<td>Cédula</td>	
								<td>Número de Factura</td>
								<td>Unidades que recibe</td>
								<td>EAN</td>
								<td>Valor descuento</td>
								<td>Nivel de desc. actual</td>
								<td>Fecha de descuento</td>
								<td>Médico</td>
								<td>Especialidad</td>
								<td>Pto de venta</td>                            
								<td>Ciudad</td>								
								$wnivel_actual
						</tr>";

    $i = 1; //Variable para controlar el estilo de los td

    while($row = mysql_fetch_array($res_lab))
        {

        if (is_integer($i/2))
                   $wclass="fila1";
                else
                   $wclass="fila2";

        $ean = codigo_ean($row['Rdeart'], $wbasedatos); //Codigo EAN

        //Si no hay codigo de barras registrado, utliza el codigo del articulo.
        if(trim($ean) == '')
        {
            $ean = $row['Rdeart'];
        }

        $dato_surcursal = explode("-",$row['sucursal']);
        $wsucursal = sucursal($dato_surcursal[1], $wbasedatos); // Trae el nombre de la sucursal.
		$wnombre_pac = nombre_cliente($row['Rdedoc']); //Nombre del paciente
		$date = date_create($row['Fecha_data']); // Prepacion de fecha para dar formato.
	    $wfecha_reporte = date_format($date, 'm/Y'); // Fecha con formato mes/año
		$wmedico1 = medico($row['Rdenve']);
		$wmedico = explode('-', $wmedico1);
		if($wmedico[1] == '') $wmedico[1] = "&nbsp;";
		
		$wmed_esp = especialidad(trim($wmedico[0]));
		if($wmed_esp == '') $wmed_esp = "&nbsp;";
		
		
        $texto_html .= "<tr class=".$wclass.">
                            <td>".$wfecha_reporte."</td>
							<td>".$row['Rdelab']."</td>
							<td>".$wnombre_pac."</td>
                            <td align='left'>".$row['Rdedoc']."</td>    
						    <td>".$row['Rdenfa']."</td> 							
                            <td>".$row['cantidad']."</td>
                            <td>".$ean."</td>
                            <td>".$row['valor']."</td>
							<td>".$row['Rdeniv']."</td>
                            <td>".$row['Fecha_data']."</td>
							<td>".$wmedico[1]."</td>
							<td>".$wmed_esp."</td>
                            <td>".$wsucursal."</td>							
                            <td>Medellin</td>";
        
                            //Si la busqueda es por cedula, pondrá la columna de imprimir factura y nivel de descuento.
                            if(trim($wcedula) !='')
                                {									
									$texto_html .= "<td><a target='_blank' href='../procesos/copia_factura.php?wnrovta=".$row['Rdenve']."&wfuefac=".$row['Rdeffa']."&wnrofac=".$row['Rdenfa']."&wfecini=$wfecha_inicial&wfecfin=$wfecha_final&wemp_pmla=09&wlotven=$wlotven'>Imprimir Copia</a></td>";
                                }
        
        $texto_html .= "</tr>";
        
        $i++;
        }

     $texto_html .= "</table>";
	
	 
     $datamensaje['table'] = utf8_encode($texto_html);
     
     echo json_encode($datamensaje);

    }

//Este segmento interactua con los llamados ajax

//Si la variable $consultaAjax tiene datos entonces busca la funcion que trae la variable.
if (isset($consultaAjax))
            {
            switch($consultaAjax)
                {

                    case 'mostrar_datos':
                        {
                            echo mostrar_datos($wemp_pmla, $wbasedatos, $wlaboratorio, $wnombre_lab, $wfecha_inicial,$wfecha_final, $wcedula);
                        }
                    break;

                    default : break;
                }
            return;
            }


  //===========================================================================================================================================
  //===========================================================================================================================================
  // P R I N C I P A L
  //===========================================================================================================================================
  //===========================================================================================================================================
    echo "<form name='rep_desc_escalonados' id='rep_desc_escalonados' action=''>";
    echo "<input type='HIDDEN' id='wemp_pmla' value='".$wemp_pmla."'>";
    echo "<input type='HIDDEN' id='wbasedatos' value='".$wbasedatos."'>";
    echo "<input type='HIDDEN' id='wusuario' value='".$wuser."'>";

	encabezado("REPORTE DESCUENTOS ESCALONADOS", $wactualiz, "logo_".$wbasedatos);

    //======================= Consulta los proveedores y construye el select ===================================

    $query_lab = "SELECT pronit, pronom
			        FROM ".$wbasedatos."_000006
			       WHERE proest = 'on'
                ORDER BY pronom";
    $res_lab = mysql_query( $query_lab ) or die( mysql_errno()." - Error en el query $query_lab - ".mysql_error() );

    $arr_lab = array();
    while($row_lab = mysql_fetch_array($res_lab))
    {
        //Se verifica si el producto ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row_lab['pronit'], $arr_lab))
        {
            $arr_lab[$row_lab['pronit']] = array();
        }

        //Aqui se forma el arreglo, con clave nit => nombre entidad
        $arr_lab[$row_lab['pronit']] = $row_lab['pronom'];

    }

    $select_lab .=  "<select id='laboratorio'>";
    $select_lab .=  "<option value=''>Seleccione...</option>";
    $select_lab .=  "<option value='%'>Todos</option>";

    foreach ($arr_lab as $key => $value) {

            $select_lab .=  "<option value='".$key."'>".$value."</option>";
    }

    $select_lab .=  "</select>";

    //============================================================================================================   
   
    
    echo "<br>";
    echo "<center>";
    echo "<input type=hidden id='cod_relacion'>";   
    echo "<table style='text-align: center; width: auto;'>
          <tbody>
            <tr class=fila1 align=center>
            <td colspan=2><b><input type=radio name='tipo_consulta' id='tipo_consulta_ced' onclick='mostrar_tipo(\"cedula\");'>Cédula</b><b><input type=radio name='tipo_consulta' id='tipo_consulta_lab' checked='' onclick='mostrar_tipo(\"lab\");'>Laboratorio</b></td>           
            </tr>
            <tr class=fila1 align=left style='display:none;' id='tipoc_cedula'>
                <td><b>Cédula:</b></td>
                <td>
                <input type=text id='cedula_persona'>
                </td>
            </tr>
            <tr class=fila1 align=left style='display:none;' id='tipoc_lab'>
                <td><b>Laboratorio:</b></td>
                <td>
                $select_lab
                </td>
            </tr>
            <tr class=fila1>
                <td align=left><b>Fecha inicial:</b></td>
                <td align=left><input type=text id=wfecha_inicial value='".date("Y-m-d")."'>
                </td>
            </tr>
            <tr class=fila1>
                <td><b>Fecha final</b></td>
                <td align=left><input type=text id=wfecha_final value='".date("Y-m-d")."'>
                </td>
            </tr>
           </tbody>
           </table>";
    echo " <table>";
    echo "  <tr>";
    echo "      <td>";
    echo "      <input type=reset onclick='ocultar_tablas();' value=Limpiar><input type=button onclick='mostrar_datos();' value=Enviar>";
    echo "      </td>";
    echo "  </tr>";
    echo " </table>";
    echo "</center>";   
    echo "</form>";
    
    echo '<div id="div_exportar" style="display:none;text-align:right;"> 
               <form action="rep_descuentos_escalonados.php?form=&accion=exportar_excel" method="post" target="_blank" id="FormularioExportacion"> 
                   <span style="color:#999999;">Exportar</span>  <img width="28" height="14" border="0" src="../../images/medical/root/export_to_excel.gif" class="botonExcel" style="cursor:pointer;" /> 
                   <input type="hidden" id="datos_a_enviar" name="datos_a_enviar" /> 
               </form> 
           </div>';
    
    echo "<center>
            <div id='datos_desc_escalonados'></div>
          </center>";
    
    
}
?>