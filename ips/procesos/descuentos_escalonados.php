<?php
include_once("conex.php");
session_start();

if (!isset($consultaAjax))
{
?>
<head>
  <title>DESCUENTOS ESCALONADOS</title>
</head>
<body>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>

<style type="text/css">
    .esError{
        border: 1px solid red;
        background-color: lightyellow;
    }
</style>
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
        changeYear: true,
        changeMonth: true,
        yearRange: '-10:+10'
        };
$.datepicker.setDefaults($.datepicker.regional['esp']);


//En esta funcion se excluye o incluye un valor de la tabla de niveles de descuento.
function validarExcluyente(incluido, excluido, evt)
	{
	
		var charCode = (evt.which) ? evt.which : evt.keyCode;
		
		var datoE = $("#"+excluido).val();
		 
		if(charCode != 9 && datoE.replace(/ /gi, "") != '')
		{
			$("#"+excluido).val("");
			
		}
	}

function soloNumeros(evt) 
	{
		var charCode = (evt.which) ? evt.which : evt.keyCode;
		if (charCode > 31 && (charCode < 48 || charCode > 57))
		{ return false; }
		return true;
	}


  function cerrarVentana()
	 {
      window.close();	
     }

  function mostrar_fecha_inact()
  {
      $('#texto_fecha').css("display", "");
      $('#campo_texto').css("display", "");
      $('#espacio_fecha').css("display", "");
  }

  function limpiarcampos()
    {

        $("#laboratorio").val('Seleccione...');
        $("#pierdedias").val('');
        $("#articulo").val('Seleccione...');
        $("#select_devuelve_html").val('1');
        $("#porcentaje-1").val('0');
        $("#porcentaje-2").val('0'); 
        $("#porcentaje-3").val('0');
		$("#porcentaje-4").val('0');
		$("#porcentaje-5").val('0');
		$("#wcajasxmes").val('');
        
    }


  var regEx = /(^[0]{1}\.{1}[0-9]+$)|(^\d+\.{1}[0-9]+$)|(^\d+$)|(^[0]$)/;
  function validar_cifra(elem)
    {

       var cantidad = $(elem).val();
	   
       if ( regEx.test( cantidad ) && cantidad != '')
            {
				if(parseFloat(cantidad) > 50)
				{
					esok = false;
					$(elem).addClass("esError");
				}
				else
					{
					$(elem).removeClass("esError");
					}
            }
        else
            {
			     esok = false;
                $(elem).addClass("esError");
            }
    }

function ocultar_tablas()
{
	
   $("#niveles").hide();
   $("#art_relacionados").hide();  
   $('#texto_fecha').css("display", "none");
   $('#campo_texto').css("display", "none");
   $('#espacio_fecha').css("display", "none");
   
}


function editar_articulo(warticulo,wcod_relacion)
 {


     $.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
						css: 	{
									width: 	'auto',
									height: 'auto'
								}
				 });

     var wlaboratorio = $("#laboratorio").val();
     var wbasedatos   = $("#wbasedatos").val();
     var wemp_pmla    = $("#wemp_pmla").val();

    $.post("descuentos_escalonados.php",
            {
                    consultaAjax:   	'editar_articulo',
					wemp_pmla:      	wemp_pmla,
                    wbasedato:          wbasedatos,
					wlaboratorio :      wlaboratorio,
                    warticulo :         warticulo,
                    wcod_relacion:      wcod_relacion

            }
            ,function(data_json) {

                if (data_json.error == 1)
                {
                    $('#laboratorio').val('Seleccion...');
                    $("#articulos_relacionados").hide();
                    return;
                }
                else
                {
                     $("#pierdedias").val(data_json.Desdpd);
                     $("#articulo").val(data_json.Descar);
                     $("#devuelve").val(data_json.Desnde);
                     $("#cod_relacion").val(data_json.cod_relacion);
                     $("#niveles_dscto").html(data_json.table);
                     $("#select_devuelve").html(data_json.select_niveles);
                     $("#wfecha_final").val(data_json.Desfin);
                     $("#estado_art").val(data_json.Desest);
					 $("#wcajasxmes").val(data_json.Descpm);
					 $('#valor_articulo').val(data_json.valor_articulo);
                     $('#texto_fecha').css("display", "");
                     $('#campo_texto').css("display", "");
                     $('#espacio_fecha').css("display", "");

                     if(data_json.actualizado == 'ok')
                         {
                             $("#niveles").hide();
                         }

                    $.unblockUI();
                }

        },
        "json"
    );
}



 function guardar()
        {

        var string_detalle = '';
        var separador = '';
        var esok = true;

        var wlaboratorio = $("#laboratorio").val();
        var wpierdedias = $("#pierdedias").val();
        var warticulo = $("#articulo").val();
        var wniveldevuelve = $("#select_devuelve_html").val();
        var wbasedatos = $("#wbasedatos").val();
        var wemp_pmla = $("#wemp_pmla").val(); 
        var wusuario = $("#wusuario").val();
        var westado_art = $("#estado_art").val();
        var wfecha_inactividad = $("#wfecha_final").val();
        var wcod_relacion = $("#cod_relacion").val();
		var wcajasxmes = $("#wcajasxmes").val(); 
		var wvalor_articulo = $("#valor_articulo").val();	  	
		
		//Recorro la tabla que contiene los datos par alos niveles con su porcentaje y valor.
        $('table[id^=niveles]').find('[type=text]').each(function(){

				var id_campo = $(this).attr("id");
				var dato = id_campo.split("-");
				var tipo_valor = dato[0];
				var nivel = dato[1];
				var dato_cantidad = $('#'+tipo_valor+"-"+nivel).val();				
				$(this).removeClass("esError");			   
				
				//Verfico que tipo de dato llega al ciclo, porcentaje o valor.
				if(tipo_valor == 'porcentaje')
					{	
						if($('#valor-'+nivel).val() == "")	
							{
							if(parseFloat(dato_cantidad) <= 0 || dato_cantidad == '')
								{
									alert('El porcentaje en el nivel '+nivel+' debe ser mayor a cero.');
									esok = false;
									$(this).addClass("esError");						
									
								}
								else
									{
									
									if(dato_cantidad > 50)
										{
											alert('El porcentaje en el nivel '+nivel+' es mayor a 50, favor verificar.');
											esok = false;
											$(this).addClass("esError");
										}
										
									
									if ( regEx.test(dato_cantidad) )
										{
											$(this).removeClass("esError");
										}
									else
										{
											esok = false;
											$(this).next("input").addClass("esError");
										}
									}
							
							
							string_detalle = string_detalle+separador+nivel+"-"+dato_cantidad+"_"+tipo_valor;
							separador = '*|*';
						}
					}
				else
					{
					
				  if(tipo_valor == 'valor')
					{
					
						if($('#porcentaje-'+nivel).val() == "")
							{
							if(parseFloat(dato_cantidad) <= 0 || dato_cantidad == '')
								{
									alert('El valor en el nivel '+nivel+' debe ser mayor a cero.');
									esok = false;
									$(this).addClass("esError");						
									
								}
								else
									{
									
									//Aqui se valida que no pase del valor maximo del articulo.
									if(dato_cantidad > parseFloat(wvalor_articulo))
										{
											alert('El valor entregado en el nivel '+nivel+' es mayor a $'+wvalor_articulo+', debe ser inferior a este valor, favor verificar.');
											esok = false;
											$(this).addClass("esError");
										}
										
									
									if ( regEx.test(dato_cantidad) )
										{
											$(this).removeClass("esError");
										}
									else
										{
											esok = false;
											$(this).next("input").addClass("esError");
										}
									}
							
							
							string_detalle = string_detalle+separador+nivel+"-"+dato_cantidad+"_"+tipo_valor;
							separador = '*|*';	
						}
					}		
				}	
				});
				
		
		//Valida que si se ingresen valores en la tabla de niveles.	
		if(string_detalle == '')
			{
			alert('Favor ingrese valores a los niveles.');
			return;
			}
			
        if (wlaboratorio == '')
            {
                alert('Debe seleccionar un laboratorio.');
                return;
            }

        if (warticulo == '')
            {
                alert('Debe seleccionar un articulo.');
                return;
            }

        if (wpierdedias == '')
            {
                alert('Debe ingresar el número de días en que se pierde el descuento.');
                return;
            }
		
		if (wcajasxmes == '')
            {
                alert('Debe ingresar la cantidad de cajas por mes.');
                return;
            }

		if(esok)
			{
			
			 $.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
					css: 	{
								width: 	'auto',
								height: 'auto'
							}
			 });	
			
			
			$.post("descuentos_escalonados.php",
					{
						consultaAjax:   	'guardar',
						wemp_pmla:      	wemp_pmla,
						wbasedato:          wbasedatos,
						wlaboratorio :      wlaboratorio,
						wpierdedias :       wpierdedias,
						warticulo :         warticulo,
						wniveldevuelve :    wniveldevuelve,
						wusuario:           wusuario,
						wdetalle:           string_detalle,
						westado_art:        westado_art,
						wfecha_inactividad: wfecha_inactividad,
						wcod_relacion:      wcod_relacion,
						wcajasxmes:			wcajasxmes

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

							alert(data_json.mensaje);
							$("#niveles").hide();
							$("#art_relacionados").hide();
							limpiarcampos();
							if(data_json.actualizado == 'ok')
								{                                
									$("#estado_art").val('on');
									$('#texto_fecha').css("display", "none");
									$('#campo_texto').css("display", "none");
									$('#espacio_fecha').css("display", "none");
								}
								$.unblockUI();
						}

				},
				"json"
			);
		}
    }

//Con esta funcion se muestran los articulos que tiene relacionado un laboratorio, en caso de no tener articulos limpiará los campos.
function mostrar_art_relacionados()
 {

    var wlaboratorio = $("#laboratorio").val();
    var wbasedatos = $("#wbasedatos").val();
    var wemp_pmla = $("#wemp_pmla").val();
    var wnombre_lab = $('#laboratorio option:selected').html();    
	$("#articulo").val('');
	$("#pierdedias").val('');
	$("#estado_art").val('');
	$("#estado_art").val('');
	$("#texto_fecha").hide();
	$("#campo_texto").hide();
	$("#wcajasxmes").val('');
	$("#niveles_aux").hide();

    $.post("descuentos_escalonados.php",
            {
                consultaAjax:   	'mostrar_art_relacionados',
                wemp_pmla:      	wemp_pmla,
                wbasedato:          wbasedatos,
                wlaboratorio:       wlaboratorio,
                wnombre_lab:        wnombre_lab

            }
            ,function(data_json) {

                if (data_json.error == 1)
                {
                    $('#laboratorio').val('Seleccion...');
                    $("#articulos_relacionados").hide();					
                    return;
                }
                else
                {				
                    $("#articulos_relacionados").html(data_json.table);
                }

        },
        "json"
    );
}

  //Esta funcion pinta lo nivel que entregue el usuario, en caso de que el articulo no tenga niveles relacionados en la tabla farpmla_000001
  function pintar_niveles()
    {

        var warticulo = $("#articulo").val();
        var wbasedatos = $("#wbasedatos").val();
        var wemp_pmla = $("#wemp_pmla").val();
        var wlaboratorio = $('#laboratorio').val();
        var wnombre_art = $('#articulo option:selected').html();
		
		
		//Debe seleccionar un laboratorio antes de seleccionar un articulo, para poder mostrar los datos del articulo relacionado con el laboratorio.
		if(wlaboratorio == '')
			{
			var wnombre_art = $('#articulo option:selected').html('Seleccione...');
			alert('Debe seleccionar un laboratorio.');			
			return false;
			}
		
		
		$.post("descuentos_escalonados.php",
				{
                    consultaAjax:   	'pintar_niveles',
					wemp_pmla:      	wemp_pmla,
                    wbasedato:          wbasedatos,
                    warticulo:          warticulo,
                    laboratorio:        wlaboratorio,
                    wnombre_art:        wnombre_art

				}
                ,function(data_json) {
					
					//Entra a esta validación cuando el articulo no tiene niveles de descuento en la tabla de articulos.
                    if (data_json.error == 1)
                    {
					 
						 
                        var answer = confirm(data_json.mensaje);
                        if (answer){
                               var niveles = prompt("Escriba el numero de niveles:", "");
                               if(!isNaN(niveles)) //Valida si es un numero.
                                   {
									if(parseInt(niveles) > 0 && parseInt(niveles) <= 10) //Valida si es mayor a cero.
										{
											//Aqui se envia de nuevo la informacion para asociarle al articulo niveles de descuento.
											$.post("descuentos_escalonados.php",
													{
														consultaAjax:       'asociar_nivel',
														wemp_pmla:          wemp_pmla,
														wbasedato:          wbasedatos,
														wlaboratorio:       wlaboratorio,
														warticulo:          warticulo,
														wnivel_asociado:    niveles

													}
													,function(data_json1) {

														if (data_json1.error == 1)
														{
															 $("#pierdedias").val('');
															 $("#articulo").val('Seleccion...');
															 $("#devuelve").val('');
															 $("#cod_relacion").val('');
															 $("#niveles_dscto").html('');
															 $("#select_devuelve").html('');													 
															 $("#wfecha_final").val('2050-01-01');
															 $("#estado_art").val('Activo');
															 $("#wcajasxmes").val('');
															 $('#texto_fecha').css("display", "");
															 $('#campo_texto').css("display", "");
															 $('#espacio_fecha').css("display", "");
															 $('#laboratorio').val('Seleccion...');
															 $('#art_relacionados_aux').hide();
															 return;
														}
														else
														{
														   $("#niveles_dscto").html(data_json1.table);
														   $("#select_devuelve").html(data_json1.select_niveles);
														   $('#valor_articulo').val(data_json.valor_articulo);
														}

												},
												"json"
											);
											
											return;
										}
										else
										   {
											  alert("No ha ingresado el niveles, es menor a cero o es mayor a 10, favor seleccionar el articulo de nuevo.");  
											  $('#articulo').val('Seleccion...');
										   }
                                   }
                                   else
                                       {
                                          alert("El nivel que ha ingresado es no es un numero, favor seleccionar el articulo de nuevo.");  
                                          $('#articulo').val('Seleccion...');
                                       }
                        }
                        else{
								//Si no va a asociar niveles a un articulo limpia los campos de todos modos ya que pueden haber datos de un articulo asociado
								//al articulo.
								$("#pierdedias").val('');
								$("#devuelve").val('');
								$("#cod_relacion").val('');
								$("#niveles_dscto").html('');
								$("#select_devuelve").html('');													 
								$("#wfecha_final").val('2050-01-01');
								$("#estado_art").val('Activo');
								$("#wcajasxmes").val('');
								$('#texto_fecha').css("display", "");
								$('#campo_texto').css("display", "");
								$('#espacio_fecha').css("display", "");
								$('#laboratorio').val('Seleccion...');
								$('#art_relacionados_aux').hide();
								$('#articulo').val('Seleccione...');
                                $("#niveles").hide();
                                return;
                        }                        
                    }
                    else
                    {
											 
						$("#articulo").val(''); //Vuelve la variable de articulo a seleccione para que lo seleccione de la lista inferior.
						
						if(data_json.Descar != null)
							{
							alert('El articulo ya se encuentra asociado a este laboratorio, seleccionelo de la lista en la parte inferior si desea editarlo.');
							}
												

                    }

            },
            "json"
        );
    }

$(document).ready(function() {
    $("#wfecha_final").datepicker({
      showOn: "button",
      buttonImage: "../../images/medical/root/calendar.gif",
      buttonImageOnly: true,
	  maxDate:"+2Y"
    });    
    
});

</script>

<?php
}
/* ****************************************************************
   * PROGRAMA PARA ASOCIAR DESCUENTOS ESCALONADOS A ARTICULOS POR LABORATORIO
   ****************************************************************/

//==================================================================================================================================
//PROGRAMA                   : descuentos_escalonados.php
//AUTOR                      : Jonatan Lopez Aguirre.
//FECHA CREACION             : Junio 17 de 2013
//FECHA ULTIMA ACTUALIZACION :
  $wactualiz="Octubre 22 de 2013";
//DESCRIPCION
//====================================================================================================================================\\
//Este programa permite la configuracion de articulos para asociarles descuentos escalonados.
//====================================================================================================================================\\



if(!isset($_SESSION['user']))
	echo "Error, su usuario n esta activo en el sistema, favor ingresar de nuevo.";
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
		

function editar_articulo($wemp_pmla, $wbasedatos, $wlaboratorio, $warticulo, $wcodigo_relacion)
    {

    global $conex;

    $datamensaje = array('mensaje'=>'', 'error'=>0);
    $separador = '';

    //Se consulta en el maestro de articulos relacionados por laboratorio, cuantos tiene.
    $query_lab = "SELECT Desdpd, Desnde, b.id as id_relacion, Desest, Desfin, Descpm
                    FROM ".$wbasedatos."_000001 a, ".$wbasedatos."_000147 b
                   WHERE desnit = '".$wlaboratorio."'
                     AND artcod = descar
                     AND descar = '".$warticulo."'";
    $res_lab = mysql_query( $query_lab, $conex) or die( mysql_errno()." - Error en el query $query_lab - ".mysql_error() );
    $row_lab = mysql_fetch_array($res_lab);

    $datamensaje['Desdpd'] = $row_lab['Desdpd'];    //Dias en que se pierde el descuento
    $datamensaje['Descar'] = $warticulo;            //Articulo
    $datamensaje['Desnde'] = $row_lab['Desnde'];    //Numero de niveles
    $datamensaje['cod_relacion'] = $row_lab['id_relacion'];    //Numero de niveles
    $datamensaje['Desfin'] = $row_lab['Desfin'];    //Fecha final de activo.
    $datamensaje['Desest'] = $row_lab['Desest'];    //Fecha final de activo.
	$datamensaje['Descpm'] = $row_lab['Descpm'];    //Fecha final de activo.
    
	//Busco el valor del articulo para validar el maximo en los valores de la tabla de niveles.
	$query_val = "SELECT Mtavac
					FROM ".$wbasedatos."_000026
				   WHERE Mtaart LIKE '%".$warticulo."%'
					 AND Mtaest = 'on'";
	$res_val = mysql_query( $query_val, $conex) or die( mysql_errno()." - Error en el query $query_val - ".mysql_error() );
	$row_val = mysql_fetch_array($res_val);
	$datamensaje['valor_articulo'] = $row_val['Mtavac'];	

    $query_art = "SELECT nivniv, nivpor, nivval
                    FROM ".$wbasedatos."_000151
                   WHERE nivcre = '".$wcodigo_relacion."'";
    $res_art = mysql_query( $query_art, $conex) or die( mysql_errno()." - Error en el query $query_lab - ".mysql_error() );

    while($row_lab = mysql_fetch_array($res_art))
        {
            $datos_niv_desc .=  $separador.$row_lab['nivniv']."-".$row_lab['nivpor']."-".$row_lab['nivval'];
            $separador = '*|*';
        }

    //Se consulta en el maestro de articulos la cantidad de niveles de descuento que tendrá.
    $query_lab = "SELECT nivniv, nivpor, desnde, nivval
                    FROM ".$wbasedatos."_000147 a, ".$wbasedatos."_000151 b
                   WHERE descar = '".$warticulo."'
                     AND a.id = nivcre                     
                ORDER BY nivniv";
    $res_lab = mysql_query( $query_lab, $conex) or die( mysql_errno()." - Error en el query $query_lab - ".mysql_error() );
    $num_lab = mysql_num_rows($res_lab);
    
 if($num_lab > 0)
    {
    $texto_html .= "<br>";
    $texto_html .= "<table id='niveles' style='text-align: center; width: auto;' border=1 cellspacing=0 >
                    <tbody id='niveles_aux'>";
    $texto_html .= "<tr class=encabezadotabla>
                    <td colspan=3>Niveles de descuento</td></tr>";
    $texto_html .= "<tr class=encabezadotabla>
                            <td>Nivel de descuento</td>
                            <td>% Descuento</td>
							<td>Valor en pesos</td>
                        </tr>";

     //Dependiendo de la cantidad de niveles se pinta el seleccionador desnde
    $select_niveles .= "<input type=text id=select_devuelve_html value='1'>";
   
    $wnivel = 1;
	
    //Dependiendo de la cantidad de niveles se pinta la tabla
    while($row = mysql_fetch_array($res_lab))
        {
			$wvalor = "";
			$wporc = "";
			
			if($row['nivpor'] == 0)
				{
				$wvalor = $row['nivval'];				
				}
			else
				{
				$wporc = $row['nivpor'];
				}
			
			$selected = '';
			$texto_html .= '<tr class=fila1>
							<td>'.$row['nivniv'].'</td>
							<td><input type="text" size="6" onkeypress="validarExcluyente(\'porcentaje-'.$wnivel.'\',\'valor-'.$wnivel.'\', event); return soloNumeros(event);" onFocus="if(this.value==0){ this.value=\'\';}" name="porcentaje-'.$wnivel.'" id="porcentaje-'.$wnivel.'" value="'.$wporc.'"></td>						    
							<td><input type="text" size="6" onkeypress="validarExcluyente(\'valor-'.$wnivel.'\',\'porcentaje-'.$wnivel.'\', event); return soloNumeros(event);" onFocus="if(this.value==0){ this.value=\'\';}" name="valor-'.$wnivel.'" id="valor-'.$wnivel.'" value="'.$wvalor.'"></td>
							</tr>';
			$wnivel++;

		   if($row['desnde'] == '-1' and $row['desnde'] == $row['nivniv'] )
			{
				$selected = 'selected';
			}
			else
			{
			   if ( $row['desnde'] == $row['nivniv'] )
					{
						$selected = 'selected';
					}
					else
					{
						$selected = '';
					}
			}        
        }

    $texto_html .=" </tbody>
                    </table>";   


    }
	
    $datamensaje['table'] = $texto_html;
    $datamensaje['select_niveles'] = $select_niveles;
    $datamensaje['datos_niv'] = $datos_niv_desc; //Datos de los niveles de descuento.


    echo json_encode($datamensaje);

    }


function guardar($wemp_pmla, $wbasedatos, $wlaboratorio, $wpierdedias, $warticulo, $wniveldevuelve, $wusuario, $wdetalle, $westado_art, $wfecha_inactividad, $wcod_relacion, $wcajasxmes)
    {

    global $conex;

    $array_detalle = array();
    $wdatosppaldetalle = explode("*|*", $wdetalle);

    $wfecha = date("Y-m-d");
    $whora  = date("H:i:s");

    $datamensaje = array('mensaje'=>'', 'error'=>0, 'actualizado'=>'');

    //Se consulta en el maestro de articulos relacionados por laboratorio, cuantos tiene.
    $query_lab = "SELECT artcod, artnom
                    FROM ".$wbasedatos."_000001, ".$wbasedatos."_000147
                   WHERE desnit = '".$wlaboratorio."'
                     AND artcod = descar
                     AND descar = '".$warticulo."'";
    $res_lab = mysql_query( $query_lab, $conex) or die( mysql_errno()." - Error en el query $query_lab - ".mysql_error() );
    $num_lab = mysql_num_rows($res_lab);

    //Si el articulo ya existe hace una actualizacion, sino hace una insercion.
    if($num_lab > 0)
        {
		
        $update="UPDATE ".$wbasedatos."_000147
		            SET desdpd = '".$wpierdedias."', desnde = '".$wniveldevuelve."', desest = '".$westado_art."', desfin = '".$wfecha_inactividad."', descpm = '".$wcajasxmes."'
		          WHERE desnit = '".$wlaboratorio."'
                    AND id='".$wcod_relacion."'";
        $res_update = mysql_query( $update ) or die( mysql_errno()." - Error en el query $update - ".mysql_error() );

        foreach($wdatosppaldetalle as $valores)
            {
                $valores = explode('-', $valores);
                $array_detalle[$valores[0]] = $valores[1];
            }	
	
        foreach ($array_detalle as $key => $item) {

			$wtipo_dato = explode("_", $item);
				
				if($wtipo_dato[1] == 'porcentaje')
					{
					$wporcentaje = $wtipo_dato[0];
					$wvalor = 0;
					}
				else
					{
					$wvalor = $wtipo_dato[0];
					$wporcentaje = 0;
					}
			
                $update=" UPDATE ".$wbasedatos."_000151
                            SET nivpor = '".$wporcentaje."',nivval = '".$wvalor."' , nivest = '".$westado_art."'
                          WHERE nivcre = '".$wcod_relacion."'
                            AND nivniv = '".$key."'";
                $res_update = mysql_query( $update ) or die( mysql_errno()." - Error en el query $update - ".mysql_error() );
            }

            $datamensaje['mensaje'] = "El articulo ha sido actualizado.";
            $datamensaje['actualizado'] = 'ok';

        }
    else
        {
            $q = " INSERT INTO ".$wbasedatos."_000147 (   Medico       ,    Fecha_data,   Hora_data,       Desnit     ,      Descar,           Desdpd,           Desnde           ,  Desest, Desfin, Descpm, Seguridad     ) "
                            ."               VALUES ('".$wbasedatos."','".$wfecha."','".$whora."','".$wlaboratorio."','".$warticulo."','".$wpierdedias."','".$wniveldevuelve."', '".$westado_art."','".$wfecha_inactividad."' , '".$wcajasxmes."', 'C-".$wusuario."')";
            $err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
            $id = mysql_insert_id();

            foreach($wdatosppaldetalle as $valores)
            {
                $valores = explode('-', $valores);
                $array_detalle[$valores[0]] = $valores[1];
            }
			
			
            foreach ($array_detalle as $key => $valor) {
				
				
				$wtipo_dato = explode("_", $valor);
				
				if($wtipo_dato[1] == 'porcentaje')
					{
					$wporcentaje = $wtipo_dato[0];
					$wvalor_desc = 0;
					}
				else
					{
					$wvalor_desc = $wtipo_dato[0];
					$wporcentaje = 0;
					}
				
				
				
                $q = " INSERT INTO ".$wbasedatos."_000151 (   Medico  ,    Fecha_data,   Hora_data,   Nivniv     , Nivpor           , Nivval      ,   Nivcre, Nivest, Seguridad     ) "
                            ."               VALUES ('".$wbasedatos."','".$wfecha."','".$whora."','".$key."'     ,'".$wporcentaje."', '".$wvalor_desc."','".$id."',   'on', 'C-".$wusuario."')";
                $err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
            }

            $datamensaje['mensaje'] = "El articulo ha sido guardado.";
    }



     echo json_encode($datamensaje);
     return;

    }

function mostrar_art_relacionados($wemp_pmla, $wbasedatos, $wlaboratorio, $wnombre_lab)
    {

    global $conex;

    $datamensaje = array('mensaje'=>'', 'error'=>0);

    //Se consulta en el maestro de articulos relacionados por laboratorio, cuantos tiene.
    $query_lab = "SELECT artcod, artnom, b.id as id_relacion
                    FROM ".$wbasedatos."_000001 a, ".$wbasedatos."_000147 b
                   WHERE desnit = '".$wlaboratorio."'
                     AND artcod = descar
                     AND artest = 'on'";
    $res_lab = mysql_query( $query_lab, $conex) or die( mysql_errno()." - Error en el query $query_lab - ".mysql_error() );
    $num_lab = mysql_num_rows($res_lab);

    $texto_html .= "<br>";
    $texto_html .= "<table id='art_relacionados' style='text-align: center;' border=1 cellspacing=0 >
                    <tbody id='art_relacionados_aux'>";
    $texto_html .= "<tr class=encabezadotabla>
                    <td colspan=3>Articulos relacionados con el laboratorio $wnombre_lab</td></tr>";
    $texto_html .= "<tr class=encabezadotabla>
                            <td>Codigo</td>
                            <td colspan='2' rowspan='1'>Nombre</td>
                        </tr>";
    $i = 1;
    while($row = mysql_fetch_array($res_lab))
        {

        if (is_integer($i/2))
                   $wclass="fila1";
                else
                   $wclass="fila2";

        $texto_html .= "<tr class=".$wclass.">
                        <td><A HREF=# onclick='editar_articulo(\"".$row['artcod']."\",\"".$row['id_relacion']."\");'>".$row['artcod']."</A></td>
                        <td colspan='2' rowspan='1' align=left>".utf8_encode($row['artnom'])."</td>
                        </tr>";
        $i++;
        }

     if($num_lab > 0)
        {
         $datamensaje['table'] = $texto_html;
        }
     else
        {
         $text_div = '<div id="articulos_relacionados"></div>';
         $datamensaje['table'] = $text_div;
        }
	

    echo json_encode($datamensaje);

    }


//Pinta la tabla de niveles de descuento, segun la cantidad que alla ingresado.
function asociar_nivel($wemp_pmla, $wbasedatos, $wlaboratorio, $warticulo, $wnivel_asociado)
    {

    global $conex;

    $datamensaje = array('mensaje'=>'', 'error'=>0);
    
    //Actualiza el nivel de descuento del articulo.
    $update=" UPDATE ".$wbasedatos."_000001
                 SET Artcde = '".$wnivel_asociado."'
               WHERE Artcod = '".$warticulo."'";
    $res_update = mysql_query( $update, $conex ) or die( mysql_errno()." - Error en el query $update - ".mysql_error() );


    $texto_html .= "<br>";
    $texto_html .= "<table id='niveles' style='text-align: center; width:auto' border=1 cellspacing=0 >
                    <tbody id=niveles_aux>";
    $texto_html .= "<tr class=encabezadotabla>
                    <td colspan=3>Niveles de descuento</td></tr>";
    $texto_html .= "<tr class=encabezadotabla>
                            <td>Nivel de descuento</td>
                            <td>% Descuento</td>
							<td>Valor en pesos</td>
                        </tr>";

    $wnivel = 1;
    //Dependiendo de la cantidad de niveles se pinta la tabla
    for($i=1; $i <= $wnivel_asociado; $i++ )
        {

        $texto_html .= '<tr class=fila1>
                        <td>'.$i.'</td>
                        <td><input type="text" value="" size="6" onkeypress="validarExcluyente(\'porcentaje-'.$wnivel.'\',\'valor-'.$wnivel.'\', event); return soloNumeros(event);" onFocus="if(this.value==0){ this.value=\'\';}" name="porcentaje-'.$wnivel.'" id="porcentaje-'.$wnivel.'" class=""></td>						    
						<td><input type="text" value="" size="6" onkeypress="validarExcluyente(\'valor-'.$wnivel.'\',\'porcentaje-'.$wnivel.'\', event); return soloNumeros(event);" onFocus="if(this.value==0){ this.value=\'\';}" name="valor-'.$wnivel.'" id="valor-'.$wnivel.'" class=""></td>
                        </tr>';
        $wnivel++;
        }

    $texto_html .=" </tbody>
                    </table>";

    //Dependiendo de la cantidad de niveles se pinta el seleccionador
    $select_niveles .= "<input id='select_devuelve_html' size='8' value='1'>";
      
    $datamensaje['table'] = $texto_html;
    $datamensaje['select_niveles'] = $select_niveles; 
    
    echo json_encode($datamensaje);

    }



//Pinta los niveles del seleccionador y la tabla de niveles de venta.
function pintar_niveles($wemp_pmla, $wbasedatos, $warticulo, $wlaboratorio, $wnombre_art)
    {

    global $conex;

    $datamensaje = array('mensaje'=>'', 'error'=>0);

    //Se consulta en el maestro de articulos la cantidad de niveles de descuento que tendrá.
    $query_lab = "SELECT artcde,nivniv, nivpor, nivval, Desdpd, Descar, Desnde, Desfin, b.id as id_relacion, Desfin, Desest, Descpm
                    FROM ".$wbasedatos."_000001, ".$wbasedatos."_000147 as b, ".$wbasedatos."_000151
                   WHERE artcod = '".$warticulo."'
                     AND desnit = '".$wlaboratorio."'
                     AND artcod = descar
                     AND b.id   = nivcre
                     AND artest = 'on'";
    $res_lab = mysql_query( $query_lab, $conex) or die( mysql_errno()." - Error en el query $query_lab - ".mysql_error() );
    $row = mysql_fetch_array($res_lab);

    if($row['artcde'] == '0' or $row['artcde'] == '')
    {
        $query_lab = "SELECT artcde
                        FROM ".$wbasedatos."_000001
                       WHERE artcod = '".$warticulo."'
                         AND artest = 'on'";
        $res_lab = mysql_query( $query_lab, $conex) or die( mysql_errno()." - Error en el query $query_lab - ".mysql_error() );
        $row = mysql_fetch_array($res_lab);
    }
	
	//Busco el valor del articulo para validar el maximo en los valores de la tabla de niveles.
	$query_val = "SELECT Mtavac
					FROM ".$wbasedatos."_000026
				   WHERE Mtaart LIKE '%".$warticulo."%'
					 AND Mtaest = 'on'";
	$res_val = mysql_query( $query_val, $conex) or die( mysql_errno()." - Error en el query $query_val - ".mysql_error() );
	$row_val = mysql_fetch_array($res_val);
	$datamensaje['valor_articulo'] = $row_val['Mtavac'];	
	$datamensaje['Desdpd'] = $row['Desdpd'];    //Dias en que se pierde el descuento
    $datamensaje['Descar'] = $warticulo;            //Articulo
    $datamensaje['Desnde'] = $row['Desnde'];    //Numero de niveles
    $datamensaje['cod_relacion'] = $row['id_relacion'];    //Numero de niveles
    
	if($row['Desfin'] == '')
		{
		$wfecha_final = '2050-01-01';
		}
	else
		{
		$wfecha_final = $row['Desfin'];
		}
		
	$datamensaje['Desfin'] = $wfecha_final;    //Fecha final de activo.	
    $datamensaje['Desest'] = $row['Desest'];    //Estado
	$datamensaje['Descpm'] = $row['Descpm'];    //Cajas por mes.
	
	//Valido si tiene datos en la table de niveles de descuento.
	if($row['nivniv'] != '')
		{
		$texto_html .= "<br>";
		$texto_html .= "<table id='niveles' style='text-align: center; width:auto' border=1 cellspacing=0 >
						<tbody id=niveles_aux>";
		$texto_html .= "<tr class=encabezadotabla>
						<td colspan=3>Niveles de descuento</td></tr>";
		$texto_html .= "<tr class=encabezadotabla>
								<td>Nivel de descuento</td>
								<td>% Descuento</td>
								<td>Valor en pesos</td>
							</tr>";
		}
    $wnivel = 1;
	$res_tabla = mysql_query( $query_lab, $conex) or die( mysql_errno()." - Error en el query $query_lab - ".mysql_error() );	
	
	//Valido si tiene datos en la tabla de niveles de descuento.
	if($row['nivniv'] != '')
		{
		//Dependiendo de la cantidad de niveles se pinta la tabla.
		while($row_tabla = mysql_fetch_array($res_tabla))
			{
			
				$wvalor = "";
				$wporc = "";
				
				if($row_tabla['nivpor'] == 0)
					{
					$wvalor = $row_tabla['nivval'];				
					}
				else
					{
					$wporc = $row_tabla['nivpor'];
					}
				//Dependiendo de los datos se pinta la linea con los campos vacios.
				$selected = '';
				$texto_html .= '<tr class=fila1>
								<td>'.$row_tabla['nivniv'].'</td>
								<td><input type="text" size="6" onkeypress="validarExcluyente(\'porcentaje-'.$wnivel.'\',\'valor-'.$wnivel.'\', event); return soloNumeros(event);" onFocus="if(this.value==0){ this.value=\'\';}" name="porcentaje-'.$wnivel.'" id="porcentaje-'.$wnivel.'" value="'.$wporc.'"></td>						    
								<td><input type="text" size="6" onkeypress="validarExcluyente(\'valor-'.$wnivel.'\',\'porcentaje-'.$wnivel.'\', event); return soloNumeros(event);" onFocus="if(this.value==0){ this.value=\'\';}" name="valor-'.$wnivel.'" id="valor-'.$wnivel.'" value="'.$wvalor.'"></td>
								</tr>';
				$wnivel++;
				
			   //Se pinta el nivel al que se devuelve.
			   if($row_tabla['desnde'] == '-1' and $row_tabla['desnde'] == $row_tabla['nivniv'] )
				{
					$selected = 'selected'; //Si el menos -1, marcara -1.
				}
				else
				{
				   if ( $row_tabla['desnde'] == $row_tabla['nivniv'] )
						{
							$selected = 'selected'; 
						}
						else
						{
							$selected = '';
						}
				}        
			}	
		}
	else
		{
		
			//Si no tiene datos relacionados en las tablas de descuento escalonado, pintara los niveles que tenga en la tabla de articulos.
			if($row['artcde'] != 'NO APLICA' and $row['artcde'] > 0)				
				{
				
				$texto_html .= "<br>";
				$texto_html .= "<table id='niveles' style='text-align: center; width:auto' border=1 cellspacing=0 >
								<tbody id=niveles_aux>";
				$texto_html .= "<tr class=encabezadotabla>
								<td colspan=3>Niveles de descuento</td></tr>";
				$texto_html .= "<tr class=encabezadotabla>
										<td>Nivel de descuento</td>
										<td>% Descuento</td>
										<td>Valor en pesos</td>
									</tr>";
				//Dependiendo de los datos se pinta la linea con los campos vacios.					
				for($i=0; $i<=$row['artcde']; $i++)
					{
					$texto_html .= '<tr class=fila1>
									<td>'.$wnivel.'</td>
									<td><input type="text" size="6" onkeypress="validarExcluyente(\'porcentaje-'.$wnivel.'\',\'valor-'.$wnivel.'\', event); return soloNumeros(event);" onFocus="if(this.value==0){ this.value=\'\';}" name="porcentaje-'.$wnivel.'" id="porcentaje-'.$wnivel.'" value=""></td>						    
									<td><input type="text" size="6" onkeypress="validarExcluyente(\'valor-'.$wnivel.'\',\'porcentaje-'.$wnivel.'\', event); return soloNumeros(event);" onFocus="if(this.value==0){ this.value=\'\';}" name="valor-'.$wnivel.'" id="valor-'.$wnivel.'" value=""></td>
									</tr>';
					$wnivel++;
					}
				
				$wmensaje_datos = 'El articulo no tiene informacion de descuentos escalonados, favor ingresar los datos que solicita el formulario.';
				
				}
		}
		
    $texto_html .=" </tbody>
                    </table>";

    //Dependiendo de la cantidad de niveles se pinta el seleccionador
    $select_niveles .= "<input id='select_devuelve_html' size='8' value='1'>";    

    //Si el articulo no tiene niveles asociados mostrará un mensaje indicando que no tiene niveles asociados
    if($row['artcde'] == '0' or $row['artcde'] == ''or $row['artcde'] == 'NO APLICA' )
        {
            $datamensaje['mensaje'] = "$wnombre_art no tiene niveles de descuento asociados en el maestro de articulos. Desea asociarle niveles?";
            $datamensaje['error'] = 1;		
			
        }
    else
        {
            $datamensaje['table'] = $texto_html;
            $datamensaje['select_niveles'] = $select_niveles;
			$datamensaje['mensaje_sin_datos'] = $wmensaje_datos;
        }


    echo json_encode($datamensaje);

    }



//Este segmento interactua con los llamados ajax

//Si la variable $consultaAjax tiene datos entonces busca la funcion que trae la variable.
if (isset($consultaAjax))
            {
            switch($consultaAjax)
                {

                    case 'guardar':
                        {
                            echo guardar($wemp_pmla, $wbasedatos, $wlaboratorio, $wpierdedias, $warticulo, $wniveldevuelve, $wusuario, $wdetalle, $westado_art, $wfecha_inactividad, $wcod_relacion, $wcajasxmes);
                        }
                    break;

                    case 'pintar_niveles':
                        {
                            echo pintar_niveles($wemp_pmla, $wbasedatos, $warticulo, $laboratorio, $wnombre_art);
                        }
                    break;

                    case 'mostrar_art_relacionados':
                        {
                            echo mostrar_art_relacionados($wemp_pmla, $wbasedatos, $wlaboratorio, $wnombre_lab);
                        }
                    break;

                    case 'editar_articulo':
                        {
                            echo editar_articulo($wemp_pmla, $wbasedatos, $wlaboratorio, $warticulo, $wcod_relacion);
                        }
                    break;

                    case 'asociar_nivel':
                        {
                            echo asociar_nivel($wemp_pmla, $wbasedatos, $wlaboratorio, $warticulo, $wnivel_asociado);
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

	echo "<form name='desc_escalonados' id='desc_escalonados' action=''>";
	echo "<input type='HIDDEN' id='wemp_pmla' value='".$wemp_pmla."'>";
    echo "<input type='HIDDEN' id='wbasedatos' value='".$wbasedatos."'>";
    echo "<input type='HIDDEN' id='wusuario' value='".$wuser."'>";

	encabezado("DESCUENTOS ESCALONADOS", $wactualiz, "logo_".$wbasedatos);

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

    $select_lab .=  "<select id='laboratorio' onchange='mostrar_art_relacionados();'>";
    $select_lab .=  "<option value=''>Seleccione...</option>";

    foreach ($arr_lab as $key => $value) {

            $select_lab .=  "<option value='".$key."'>".$value."</option>";
    }

    $select_lab .=  "</select>";

    //======================= Consulta los articulos y construye el select ===================================
    $query_art = "SELECT artcod, artnom
			        FROM ".$wbasedatos."_000001
			       WHERE artest = 'on'
                ORDER BY artnom asc";
    $res_art = mysql_query( $query_art ) or die( mysql_errno()." - Error en el query $query_art - ".mysql_error() );

    $arr_art = array();
    while($row_art = mysql_fetch_array($res_art))
    {
        //Se verifica si el producto ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row_art['artcod'], $arr_art))
        {
            $arr_art[$row_art['artcod']] = array();
        }

        //Aqui se forma el arreglo, con clave nit => nombre entidad
        $arr_art[$row_art['artcod']] = $row_art['artnom']."-".$row_art['artcod'];

    }

    $select_art .= "<select id='articulo' onchange='pintar_niveles();'>";
    $select_art .=  "<option value=''>Seleccione...</option>";

    foreach ($arr_art as $key => $value) {

            $select_art .=  "<option value='".$key."'>".$value."</option>";
    }

    $select_art .=  "</select>";

    //=========================================================================

    echo "<br>";
    echo "<center>";
    echo "<input type=hidden id='cod_relacion'>";
    echo "<table style='text-align: center; width: auto;'>
          <tbody>
			<tr>
			  <td class=encabezadotabla colspan='2' rowspan='1'>Relación Laboratorio - Artículos Descuentos Escalonados</td>
			</tr>
			<tr class=fila1 align=left>
				<td>Laboratorio:</td>
				<td colspan='1' rowspan='1' >
				$select_lab
				</td>
			</tr>
			<tr class=fila1>
				<td align=left>Articulo:</td>
				<td colspan='1' rowspan='1'>
				$select_art <input type=hidden id='valor_articulo' value=0>
				</td>
			</tr>
			<tr class=fila1>
				<td>Nro de días en que se obtiene el descuento:</td>
				<td align=left><input id='pierdedias' size='8' maxlength='3' onkeyup='this.value = this.value.replace (/[^1?[0-9]$|^[1-2]0$]/, \"\");'></td>
			</tr>
			<tr class=fila1 style='display:none'>
				<td align=left>Nivel de descuento al que se devuelve:</td>
				<td colspan='2' rowspan='1' align=left>
				<div id='select_devuelve'></div>           
				</td>
			</tr>
			<tr class=fila1>
				<td align=left>Estado</td>
				<td colspan='1' rowspan='1' align=left>
				<table border='0'>
					<tbody>
					<tr>
						<td><select id='estado_art' onchange='mostrar_fecha_inact();'>
								<option value='on'>Activo</option>
								<option value='off'>Inactivo</option>
							</select></td>
						<td class=fila1 id='espacio_fecha' style='display:none;'>&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td class=fila1 id='texto_fecha' style='display:none;'>Fecha de inactividad:</td>
						<td id='campo_texto'  style='display:none;'><input id='wfecha_final' size='12' value='2050-01-01'></td>

					</tr>
					</tbody>
				</table>
			</tr>
			<tr class=fila1>
				<td align=left >Cajas por mes:</td>
				<td align=left colspan='1' rowspan='1'><input id='wcajasxmes' size='8' maxlength='3' onkeyup='this.value = this.value.replace (/[^1?[0-9]$|^[1-2]0$]/, \"\");'></td>
			</tr>
			<tr id='div_niveles'>
			<td colspan='5' rowspan='1'>
				<center>
				<div id=niveles_dscto></div>
				</center>
				</td>
			</tr>
				<tr>
				</tr>
			</tbody>
			</table>";
    echo "<table>";
    echo "<tr>";
    echo "<td>";
    echo "<input type=reset onclick='ocultar_tablas()' value=Limpiar><input type=button onclick='guardar();' value=Guardar>";
    echo "</td>";
    echo "</tr>";
	echo "<tr>";   
    echo "</tr>";
    echo "</table>";
    echo "</center>";
    echo "<center>
          <div id='articulos_relacionados'></div>
          </center>";
	echo "<br><br>";
	echo "<center>
          <div><input type=reset onclick='cerrarVentana();' value='Cerrar Ventana'></div>
          </center>";
	echo "</form>";
}
?>