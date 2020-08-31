<?php
include_once("conex.php");
/************************************************************************************************************
 * Programa		:	Tarjeta de dispositivo medico implantable
 * Fecha		:	2015-10-04
 * Por			:	Felipe Alvarez Sanchez
 * Descripcion	:
 * Condiciones  :
 *********************************************************************************************************

 Actualizaciones:

 * 2016-08-30	: Se agrega utf8_decode() al insertar y actualizar los registros para que se guarden 
 *				  correctamente las tildes.
 **********************************************************************************************************/

$wactualiz = "2017-06-15";

if(!isset($_SESSION['user'])){
	echo "error";
	return;
}
//Para que las respuestas ajax acepten tildes y caracteres especiales
header('Content-type: text/html;charset=ISO-8859-1');

if( isset($consultaAjax) == false ){

?>
	<html>
	<head>
	<title>Maestro de copagos</title>
	<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />
	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
	<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
	<link type="text/css" href="../../../include/root/smartpaginator.css" rel="stylesheet" /> <!-- Autocomplete -->

	<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
	<script type='text/javascript' src='../../../include/root/jquery.tooltip.js'></script>
	<script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>
	<script src="../../../include/root/jquery.maskedinput.js" type="text/javascript"></script>
	<script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>
	<link type="text/css" href="../../../include/root/jquery.ui.timepicker.css" rel="stylesheet"/>
	<script type='text/javascript' src='../../../include/root/smartpaginator.js'></script>	<!-- Autocomplete -->
	<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
    <link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />

	<style>

		.tborder
		{
			/*border: solid black;*/
		}
		.visibilidad
		{
			display:none;
		}

		.campoObligatorio{
			border-style:solid;
			border-color:red;
			border-width:1px;
		}

		fieldset{
			border: 2px solid #e0e0e0;
		}
		legend{
			border: 2px solid #e0e0e0;
			border-top: 0px;
			font-family: Verdana;
			background-color: #e6e6e6;
			font-size: 16pt;
		}

		.ui-autocomplete{
			max-width: 	250px;
			max-height: 150px;
			overflow-y: auto;
			overflow-x: hidden;
			font-size: 	8pt;
		}
		
		

		// --> Estylo para los placeholder
		/*Chrome*/
		[tipo=obligatorio]::-webkit-input-placeholder {color:gray; background:lightyellow;font-size:8pt}
		/*Firefox*/
		[tipo=obligatorio]::-moz-placeholder {color:#000000; background:lightyellow;font-size:8pt}
		/*Interner E*/
		[tipo=obligatorio]:-ms-input-placeholder {color:gray; background:lightyellow;font-size:8pt}
		[tipo=obligatorio]:-moz-placeholder {color:gray; background:lightyellow;font-size:8pt}




	</style>
	<script>


	</script>
<script>

//************cuando la pagina este lista...**********//
	$(document).ready(function() {
		
		//alert();
		$( "#accordionppal" ).accordion({
				collapsible: false,
				heightStyle: "content"
			});
		$( "#accordionopciones" ).accordion({
				collapsible: false,
				heightStyle: "content"
			});
			
		$('.entero').keyup(function(){
		
			
			if(!isNaN(num)){
			//num = num.toString().split('').reverse().join('').replace(/(?=\d*\.?)(\d{3})/g,'$1.');
			//num = num.split('').reverse().join('').replace(/^[\.]/,'');
			$(this).val(num);
			
			}
			else
			{ 
				var num = $(this).val().replace(/\./g,'');
				//alert('Solo se permiten numeros');
				$(this).val($(this).val().replace(/[^\d\.]*/g,''));
			}
		});	

		$('.entero2').keyup(function(){
			if ($(this).val() !="")
			$(this).val($(this).val().replace(/[^0-9]/g, ""));
		});


		$('.real').focusout(function(){
			if ($(this).val() !=""){
				//$(this).val($(this).val().replace(/[^0-9|\.]/g, ""));
				var regEx = /(^[0]\.{1}[0-9]+$)|(^[1-9]+\.{1}[0-9]+$)|(^[1-9]+[0-9]*$)|(^[0]$)/;
				if ( regEx.test( $(this).val() ) == false )
				{
					$(this).val("");
					var aux = $(this);
					alert("El dato ingresado no es de tipo real");
					setTimeout(function(){ aux.focus();},500);
				}
			}
		});
	
	
			/*$('.entero').keyup(function(){
							if($(this).val()*1 > 100)
								
							
							if ($(this).val() !="")
							$(this).val($(this).val().replace(/[^0-9]/g, ""));
			});*/
		crear_autocomplete();
	});

	function grabar()
	{
		// alert($("#empresa").attr("valor"));
		// return;
		//validaciones
		
		if($("#tipo_empresa").val()=='')
		{
			$("#tipo_empresa").addClass('campoObligatorio');
			alert("Seleccione tipo de empresa");
			return;
		}
		else
		{
			$("#tipo_empresa").removeClass('campoObligatorio');
		}
		
		if($("#empresa").attr("valor")=='' )
		{
			$("#empresa").addClass('campoObligatorio');
			alert("Seleccione empresa");
			return;
		}
		else
		{
			$("#empresa").removeClass('campoObligatorio');
		}
		
		if($("#cobertura").val()=='')
		{
			$("#cobertura").addClass('campoObligatorio');
			alert("Seleccione cobertura");
			return;
		}
		else
		{
			$("#cobertura").removeClass('campoObligatorio');
		}
		
		if($("#afiliacion").val()=='')
		{
			$("#afiliacion").addClass('campoObligatorio');
			alert("Seleccione afilicion");
			return;
		}
		else
		{
			$("#afiliacion").removeClass('campoObligatorio');
		}
		
		if($("#compartido").val()=='')
		{
			$("#compartido").addClass('campoObligatorio');
			alert("Seleccione pago compartido");
			return;
		}
		else
		{
			$("#compartido").removeClass('campoObligatorio');
		}		
		
		if($("#porcentaje").val()=='')
		{
			
			$("#porcentaje").addClass('campoObligatorio');
			alert("El porcentaje no puede ser vacio");
			return;
		}
		else
		{
			if($("#porcentaje").val()*1 > 100)
			{
				$("#porcentaje").addClass('campoObligatorio');
				alert("El porcentaje no puede ser mayor a 100");
				return;
			}
			else
			$("#porcentaje").removeClass('campoObligatorio');
		}
		
		if($("#tope").val()=='')
		{
			$("#tope").addClass('campoObligatorio');
			alert("El tope maximo no puede ser vacio");
			return;
		}
		else
		{
			$("#tope").removeClass('campoObligatorio');
		}
		
		
		
		
		$.post("Maestro_copagos.php",
		{
			consultaAjax:   '',
			wemp_pmla:      $('#wemp_pmla').val(),
			accion:         'grabar',
			tipoEmpresa : 	$("#tipo_empresa").val(),
			empresa : 		$("#empresa").attr("valor"),
			cobertura:		$("#cobertura").val(),
			afiliacion:		$("#afiliacion").val(),
			compartidos:	$("#compartido").val(),
			porcentaje:		$("#porcentaje").val(),
			tope:			$("#tope").val(),
			pagoxhospitalizacion:	$("#pagoxhospitalizacion").val()
			

		},function(data) {
			
			//alert(data);
			//alert("hola");
			$("#divppal").html(data);
			
			limpiar();

		});
		




	}
	
	function editar(id , tipoempresa, empresa , cobertura, afiliacion, compartido, porcentaje, tope,estado,pagoxhospitalizacion)
	{
		
		$("#tid").val(id);
		//alert(tipoempresa);
		$("#botongrabar").val("Editar");
		$("#botongrabar").attr("onclick" ,"grabar_edicion()");
	
		$("#tipo_empresa").val(tipoempresa);
		$("#empresa").attr("valor" , empresa );
		$("#empresa").val(empresa);
		$("#cobertura").val(cobertura);
		$("#afiliacion").val(afiliacion);
		$("#compartido").val(compartido);
		$("#porcentaje").val(porcentaje);
		$("#tope").val(tope);
		$("#pagoxhospitalizacion").val(pagoxhospitalizacion);
		
		if(estado=='on')
		{
			$("#activoinactivo").prop('checked', true);
		}
		else
		{
			$("#activoinactivo").prop('checked', false);
		}
		//alert(estado);
		
		
	}
	

	
	function grabar_edicion()
	{
		
		//validaciones
		
		if($("#tipo_empresa").val()=='')
		{
			$("#tipo_empresa").addClass('campoObligatorio');
			alert("Seleccione tipo de empresa");
			return;
		}
		else
		{
			$("#tipo_empresa").removeClass('campoObligatorio');
		}
		
		if($("#empresa").attr("valor")=='')
		{
			$("#empresa").addClass('campoObligatorio');
			alert("Seleccione empresa");
			return;
		}
		else
		{
			$("#empresa").removeClass('campoObligatorio');
		}
		
		if($("#cobertura").val()=='')
		{
			$("#cobertura").addClass('campoObligatorio');
			alert("Seleccione cobertura");
			return;
		}
		else
		{
			$("#cobertura").removeClass('campoObligatorio');
		}
		
		if($("#afiliacion").val()=='')
		{
			$("#afiliacion").addClass('campoObligatorio');
			alert("Seleccione afilicion");
			return;
		}
		else
		{
			$("#afiliacion").removeClass('campoObligatorio');
		}
		
		if($("#compartido").val()=='')
		{
			$("#compartido").addClass('campoObligatorio');
			alert("Seleccione pago compartido");
			return;
		}
		else
		{
			$("#compartido").removeClass('campoObligatorio');
		}		
		
		if($("#porcentaje").val()=='')
		{
			$("#porcentaje").addClass('campoObligatorio');
			alert("El porcentaje no puede ser vacio");
			return;
		}
		else
		{
			if($("#porcentaje").val()*1 > 100)
			{
				$("#porcentaje").addClass('campoObligatorio');
				alert("El porcentaje no puede ser mayor a 100");
				return;
			}
			else
			$("#porcentaje").removeClass('campoObligatorio');
		}
		
		if($("#tope").val()=='')
		{
			$("#tope").addClass('campoObligatorio');
			alert("El tope maximo no puede ser vacio");
			return;
		}
		else
		{
			$("#tope").removeClass('campoObligatorio');
		}
		
		
		
		
		//$("#activoinactivo").
		if($('#activoinactivo').is(':checked'))
		{
			westado='on';
			
		}
		else
		{
			westado='off';
		}
		//alert($("#compartido").val());
		
		//alert($("#empresa").attr("valor"))
		//return;
		$.post("Maestro_copagos.php",
		{
			consultaAjax:   '',
			wemp_pmla:      $('#wemp_pmla').val(),
			accion:         'grabar_edicion',
			tipoEmpresa : 	$("#tipo_empresa").val(),
			empresa : 		$("#empresa").attr("valor"),
			cobertura:		$("#cobertura").val(),
			tipo:			$("#afiliacion").val(),
			compartido:		$("#compartido").val(),
			porcentaje:		$("#porcentaje").val(),
			tope:			$("#tope").val(),
			wid:			$("#tid").val(),
			westado:		westado,
			pagoxhospitalizacion : $("#pagoxhospitalizacion").val()
			
			

		},function(data) {
			
			$("#divppal").html(data);
			
			limpiar();


		});
		
		
	}
	
	function limpiar()
	{
		$("#botongrabar").val("Grabar");
		$("#botongrabar").attr("onclick" ,"grabar()");
		$("#tipo_empresa").val("");
		$("#empresa").attr("valor"," ");
		$("#empresa").val("");
		//$("#empresa").attr("valor", "");
		$("#cobertura").val("");
		$("#afiliacion").val("");
		$("#compartido").val("");
		$("#porcentaje").val("");
		$("#tope").val("");
		$("#tid").val("");
		$("#pagoxhospitalizacion").val("");
		$("#activoinactivo").prop('checked', true);
	}
	
	
	function crear_autocomplete()
	{
		campo='empresa';
		/*$("#"+campo).val(nomIni);
		$("#"+campo).attr("valor",codIni);
		$("#"+campo).attr("nombre",nomIni);*/

		var ArrayValores  = eval('(' + $('#hidden_empresas').val() + ')');
		
		
		var minimoparabusqueda = 3;
		
		
		
		//--

		var ArraySource   = new Array();
		var index		  = -1;
		for (var CodVal in ArrayValores)
		{
			index++;
			ArraySource[index] = {};
			ArraySource[index].value  = CodVal;
			ArraySource[index].label  = ArrayValores[CodVal];
			ArraySource[index].name   = ArrayValores[CodVal];
		}



			$( "#"+campo ).autocomplete({
				minLength: 	minimoparabusqueda,
				source: 	ArraySource,
				select: 	function( event, ui ){
					$( "#"+campo ).val(ui.item.label);
					$( "#"+campo ).attr('valor', ui.item.value);
					$( "#"+campo ).attr('nombre', ui.item.name);

					return false;
				}
			});



	}
	




</script>
</head>

<?php

}

//********************INCLUDES, BD, VARIABLES GLOBALES, SESSION****************************//


include_once("root/comun.php");



$conex = obtenerConexionBD("matrix");


$user_session = explode('-',$_SESSION['user']);
$user_session = (count($user_session) > 1)? $user_session[1] : $user_session[0];
//$user_session = ( strlen($user_session) > 5) ? substr($user_session,-5): $user_session;


$wusuario = $user_session;

//**************************ENCARGADO DE RECIBIR LOS LLAMADOS CON AJAX********************************************//
if(isset($accion))
{
	switch($accion)
	{

		case "grabar":
		{
			
			
			$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
			$wfecha = date("Y-m-d");
			$whora = date("H:i:s");
			$insert = "INSERT      ".$wbasedato."_000290 (Medico, 	Fecha_data, 	Hora_data , 	Cfctem  			, 	Cfcemp, 		Cfccob				, 	Cfctip		 ,Cfcniv, 	 Cfcpor	, 			Cfctma, 	Cfcest, 	Seguridad ,   	Cfcpxh)
									      VALUES ('".$wbasedato."' , '".$wfecha."', '".$whora."',    '".$tipoEmpresa."' ,	'".$empresa."',  '".$cobertura."'	  , '".$afiliacion."',  '".$compartidos."', '".$porcentaje."', '".$tope."', 'on' ,     'C-cliame'  , '".$pagoxhospitalizacion."'  )" ;
			
			
			
			mysql_query($insert,$conex) or die ("Error 4: ".mysql_errno()." - en el query: ".$insert." - ".mysql_error());
			
			
			
			
			$query  = "		   SELECT Temcod ,	Temdes 
								 FROM ".$wbasedato."_000029 
								WHERE Temest='on'
							    ORDER by Temdes";	

					if($res1 = mysql_query($query,$conex))
					{
						$array_tipo = array();
						$array_tipo['*'] ="Todos";
						
						while($row1 = mysql_fetch_array($res1))
						{
							
							$array_tipo[$row1['Temcod']] = $row1['Temcod']."-".$row1['Temdes'];
							
						}
					}
					
					$query  = "SELECT Empcod ,Empnom
								 FROM ".$wbasedato."_000024 
								WHERE Empest ='on'
							Order by  Empnom";	

					if($res1 = mysql_query($query,$conex))
					{
						$array_empresa = array();
						$array_empresa['*'] = "Todos";
						while($row1 = mysql_fetch_array($res1))
						{
							
							$array_empresa[$row1['Empcod']] = utf8_encode($row1['Empcod']."-".$row1['Empnom']);
						}
					}							
												
					
					$query  = "SELECT Seltip,Selcod,Seldes,Selpri
								 FROM ".$wbasedato."_000105 
								WHERE Selest ='on'
								  AND Seltip ='06'
							Order by  Selpri";	

					if($res1 = mysql_query($query,$conex))
					{
						$array_cobertura = array();
						$array_cobertura['*'] = "Todos";
						while($row1 = mysql_fetch_array($res1))
						{
							
							$array_cobertura[$row1['Selcod']] = $row1['Selcod']."-".$row1['Seldes'];
						}
					}							
										
												
												
				
					$query  = "SELECT Seltip,Selcod,Seldes,Selpri
								 FROM ".$wbasedato."_000105 
								WHERE Selest ='on'
								  AND Seltip ='16'
							Order by  Selpri";	

					if($res1 = mysql_query($query,$conex))
					{
						$array_afiliacion = array();
						$array_afiliacion['*'] = "Todos";
						while($row1 = mysql_fetch_array($res1))
						{
							
							$array_afiliacion[$row1['Selcod']] = $row1['Selcod']."-".$row1['Seldes'];
						}
					}				
												
					
											
					$query  = "SELECT Seltip,Selcod,Seldes,Selpri
								 FROM ".$wbasedato."_000105 
								WHERE Selest ='on'
								  AND Seltip ='22'
							Order by  Selpri";	

					if($res1 = mysql_query($query,$conex))
					{
						$array_compartido = array();
						$array_compartido['*'] = "Todos";
						while($row1 = mysql_fetch_array($res1))
						{
							
							$array_compartido[$row1['Selcod']] = $row1['Selcod']."-".$row1['Seldes'];
						}
					}
			
			//echo $insert;
			$select = "SELECT Cfctem ,Cfcemp,Cfccob,Cfctip,Cfcniv,Cfcpor,Cfctma,Cfcest,Id, 	Cfcpxh
										FROM ".$wbasedato."_000290" ;
			if($res = mysql_query($select,$conex))
			{
				$html .="<table align='center' id='tableppal'>
										<tr class='encabezadoTabla'>
											<td>#</td>
											<td>Tipo de Empresa</td>
											<td>Empresa</td>
											<td>Cobertura en Salud</td>	
											<td>Tipo de Afiliaci&oacute;n</td>
											<td>Pago Compartido</td>
											<td>Porcentaje</td>
											<td>Tope Maximo</td>
											<td>Valor x hospitalizaci&oacute;n</td>
											<td>Activo</td>
											<td>Editar</td>
										</tr>";
						

						$k=1;
						while($row = mysql_fetch_array($res))
						{

							if (is_int ($k/2))
							   {
								$wcf="fila1";  // color de fondo de la fila
							   }
							else
							   {
								$wcf="fila2"; // color de fondo de la fila
							   }
							$html .="<tr class='".$wcf."'>";
							$html .="<td>".$k."</td>";
							
							$html .="<td>".$array_tipo[$row['Cfctem']]."</td>";
							$html .="<td>".$array_tipo[$row['Cfcemp']]."</td>";
							$html .="<td>".$array_cobertura[$row['Cfccob']]."</td>";
							$html .="<td>".$array_afiliacion[$row['Cfctip']]."</td>";
							$html .="<td>".$array_compartido[$row['Cfcniv']]."</td>";
							$html .="<td>".$row['Cfcpor']."</td>";
							$html .="<td>".number_format($row['Cfctma'],0,'.',',')."</td>";
							if($row['Cfcest']=='')
								$estado='on';
							else
								$estado=$row['Cfcest'];
							
							
							$estadovisible='Activo';
							if($estado=='off')
							{
								$estadovisible='Inactivo';
							}
							$html .="<td align='right'>".number_format($row['Cfcpxh'],0,'.',',')."</td>";
							$html .="<td>".$estado."</td>";
							$html .="<td><input type='button' value='Editar' onclick='editar(\"".$row['Id']."\" , \"".$row['Cfctem']."\" , \"".$row['Cfcemp']."\" , \"".$row['Cfccob']."\" , \"".$row['Cfctip']."\", \"".$row['Cfcniv']."\" ,  \"".$row['Cfcpor']."\" ,  \"".$row['Cfctma']."\" , \"".$estado."\" , \"".$row['Cfcpxh']."\" )'></td>";
							$html .="</tr>";
							$k++;
						}
					
						$html .="</table>";
			}
						echo $html;
			break;
		}
		
		case "grabar_edicion":
		{
			$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
			//Cfctem 	Cfcemp 	Cfccob 	Cfctip 	Cfcafi 	Cfcniv 	Cfcpor 	Cfctma 	Cfcest
			$wfecha = date("Y-m-d");
			$whora = date("H:i:s");
			$update = "UPDATE      ".$wbasedato."_000290 
											SET	  Cfctem  	=  '".$tipoEmpresa."' ,
												  Cfcemp    =  '".$empresa."'	,
												  Cfccob	=  '".$cobertura."'	,
												  Cfctip    =  '".$tipo."',
												  Cfcniv    =  '".$compartido."',
												  Cfcpor	=  '".$porcentaje."',
												  Cfctma    =  '".$tope."',
												  Cfcest    =  '".$westado."',
												  Cfcpxh    =  '".$pagoxhospitalizacion."'
												  WHERE Id  =  '".$wid."'";
			
			
			mysql_query($update,$conex) or die ("Error 4: ".mysql_errno()." - en el query: ".$update." - ".mysql_error());
			
			
			
			$query  = "SELECT Temcod ,	Temdes 
								 FROM ".$wbasedato."_000029 
								WHERE Temest='on'
							 Order by Temdes";	

					if($res1 = mysql_query($query,$conex))
					{
						$array_tipo = array();
						$array_tipo['*'] ="Todos";
						
						while($row1 = mysql_fetch_array($res1))
						{
							
							$array_tipo[$row1['Temcod']] = $row1['Temcod']."-".$row1['Temdes'];
							
						}
					}
					
					$query  = "SELECT Empcod ,Empnom
								 FROM 	".$wbasedato."_000024 
								WHERE Empest ='on'
							Order by  Empnom";	

					if($res1 = mysql_query($query,$conex))
					{
						$array_empresa = array();
						$array_empresa['*'] = "Todos";
						while($row1 = mysql_fetch_array($res1))
						{
							
							$array_empresa[$row1['Empcod']] = utf8_encode($row1['Empcod']."-".$row1['Empnom']);
						}
					}							
												
					
					$query  = "SELECT Seltip,Selcod,Seldes,Selpri
								 FROM ".$wbasedato."_000105 
								WHERE Selest ='on'
								  AND Seltip ='06'
							Order by  Selpri";	

					if($res1 = mysql_query($query,$conex))
					{
						$array_cobertura = array();
						$array_cobertura['*'] = "Todos";
						while($row1 = mysql_fetch_array($res1))
						{
							
							$array_cobertura[$row1['Selcod']] = $row1['Selcod']."-".$row1['Seldes'];
						}
					}							
										
												
												
				
					$query  = "SELECT Seltip,Selcod,Seldes,Selpri
								 FROM ".$wbasedato."_000105 
								WHERE Selest ='on'
								  AND Seltip ='16'
							Order by  Selpri";	

					if($res1 = mysql_query($query,$conex))
					{
						$array_afiliacion = array();
						$array_afiliacion['*'] = "Todos";
						while($row1 = mysql_fetch_array($res1))
						{
							
							$array_afiliacion[$row1['Selcod']] = $row1['Selcod']."-".$row1['Seldes'];
						}
					}				
												
					
											
					$query  = "SELECT Seltip,Selcod,Seldes,Selpri
								 FROM ".$wbasedato."_000105 
								WHERE Selest ='on'
								  AND Seltip ='22'
							Order by  Selpri";	

					if($res1 = mysql_query($query,$conex))
					{
						$array_compartido = array();
						$array_compartido['*'] = "Todos";
						while($row1 = mysql_fetch_array($res1))
						{
							
							$array_compartido[$row1['Selcod']] = $row1['Selcod']."-".$row1['Seldes'];
						}
					}
			
			//echo $insert;
			$select = "SELECT Cfctem ,Cfcemp,Cfccob,Cfctip,Cfcniv,Cfcpor,Cfctma,Cfcest,Id,Cfcpxh
										FROM ".$wbasedato."_000290" ;
			if($res = mysql_query($select,$conex))
			{
				$html .="<table align='center' id='tableppal'>
										<tr class='encabezadoTabla'>
											<td>#</td>
											<td>Tipo de Empresa</td>
											<td>Empresa</td>
											<td>Cobertura en Salud</td>	
											<td>Tipo de Afiliaci&oacute;n</td>
											<td>Pago Compartido</td>
											<td>Porcentaje</td>
											<td>Tope Maximo</td>
											<td>Valor x hospitalizaci&oacute;n</td>
											<td>Activo</td>
											<td>Editar</td>
										</tr>";
						

						$k=1;
						while($row = mysql_fetch_array($res))
						{

							if (is_int ($k/2))
							   {
								$wcf="fila1";  // color de fondo de la fila
							   }
							else
							   {
								$wcf="fila2"; // color de fondo de la fila
							   }
							$html .="<tr class='".$wcf."'>";
							$html .="<td>".$k."</td>";
							
							$html .="<td>".$array_tipo[$row['Cfctem']]."</td>";
							$html .="<td>".$array_empresa[$row['Cfcemp']]."</td>";
							$html .="<td>".$array_cobertura[$row['Cfccob']]."</td>";
							$html .="<td>".$array_afiliacion[$row['Cfctip']]."</td>";
							$html .="<td>".$array_compartido[$row['Cfcniv']]."</td>";
							$html .="<td>".$row['Cfcpor']."</td>";
							$html .="<td>".number_format($row['Cfctma'],0,'.',',')."</td>";
							if($row['Cfcest']=='')
								$estado='on';
							else
								$estado=$row['Cfcest'];
							
							$estadovisible='Activo';
							if($estado=='off')
							{
								$estadovisible='Inactivo';
							}
							$html .="<td>".number_format($row['Cfcpxh'],0,'.',',')."</td>";
							$html .="<td>".$estado."</td>";
							$html .="<td><input type='button' value='Editar' onclick='editar(\"".$row['Id']."\" , \"".$row['Cfctem']."\" , \"".$row['Cfcemp']."\" , \"".$row['Cfccob']."\" , \"".$row['Cfctip']."\", \"".$row['Cfcniv']."\" ,  \"".$row['Cfcpor']."\" ,  \"".$row['Cfctma']."\" , \"".$estado."\" ,\"".$row['Cfcpxh']."\" )'></td>";
							$html .="</tr>";
							$k++;
						}
					
						$html .="</table>";
			}
						echo $html;
			//echo $update;
			break;
		}
		
	}
	return;

}





?>
<body>
		<!-- LO QUE SE MUESTRA AL INGRESAR POR PRIMERA VEZ -->
			<?php

					encabezado("Maestro de copagos", $wactualiz, "clinica");

					$user_session = explode('-',$_SESSION['user']);
					$user_session = (count($user_session) > 1)? $user_session[1] : $user_session[0];
					//$user_session = (strlen($user_session) > 5) ? substr($user_session,-5): $user_session;


					$wusuario = $user_session;
					$html .="<div id='primeradiv'><input type='hidden' id='wusuariotabla' value='".$wusuario."'>";
					$html .="<input type='hidden' id='wemp_pmla' name='wemp_pmla' value='".$wemp_pmla."'>";
					$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
					$html .="<input type='hidden' id='wbasedato' name='wbasedato' value='".$wbasedato."'>";
					
					$html .="<center>
					<input type='hidden' id='tid'  value=''>
								<div width='95%' id='accordionppal'>
											<h3 align='left'>Nuevo Registro</h3>
												<div>
												
								<table>
									<tr>
									   <td class='fila1' nowrap='nowrap'>
									   Tipo de Empresa :
									   </td>
									   <td class='fila2'>
											<select id='tipo_empresa'>
												<option value=''>
													Seleccione ...
												</option>
												<option value='*'>
													Todos
												</option>";
												
					$query  = "SELECT Temcod ,	Temdes 
								 FROM ".$wbasedato."_000029 
								WHERE Temest='on'
							 Order by Temdes";	

					if($res1 = mysql_query($query,$conex))
					{
						$array_tipo = array();
						$array_tipo['*'] ="Todos";
						
						while($row1 = mysql_fetch_array($res1))
						{
							$html .="<option value=".$row1['Temcod'].">".$row1['Temcod']."-".$row1['Temdes']."</option>";
							$array_tipo[$row1['Temcod']] = $row1['Temcod']."-".$row1['Temdes'];
							
						}
					}
					
					$cuantos = count($vector[$vector_campos_completos[$j]]);
					$html .="
											</select>
									   </td>
									   <td class='fila1' nowrap='nowrap'>
									   Empresa : 
									   </td>
									   
									   <td class='fila2'>
									   
									   <input type='text' id='empresa'   valor=''  >";
										/*	<select id='empresa'>
												<option value=''>
													Seleccione ...
												</option>
												<option value='*'>
													Todos
												</option>";*/
					$query  = "SELECT Empcod ,Empnom
								 FROM 	".$wbasedato."_000024 
								WHERE Empest ='on'
							Order by  Empnom";	

					if($res1 = mysql_query($query,$conex))
					{
						$array_empresa = array();
						$array_empresa['*'] = "Todos";
						while($row1 = mysql_fetch_array($res1))
						{
							/*$html .="<option value=".$row1['Empcod'].">".$row1['Empcod']."-".$row1['Empnom']."</option>";*/
							$array_empresa[$row1['Empcod']] = utf8_encode($row1['Empcod']."-".$row1['Empnom']);
						}
					}							
												
							/*	</select>*/
					$html .="					   </td></tr><tr>  
									   <td class='fila1' nowrap='nowrap'>
									   Cobertura en Salud :
									   </td>
									   <td class='fila2'>
											<select id='cobertura'>
												<option value=''>
													Seleccione ...
												</option>
												<option value='*'>
													Todos
												</option>";
					$query  = "SELECT Seltip,Selcod,Seldes,Selpri
								 FROM ".$wbasedato."_000105 
								WHERE Selest ='on'
								  AND Seltip ='06'
							Order by  Selpri";	

					if($res1 = mysql_query($query,$conex))
					{
						$array_cobertura = array();
						$array_cobertura['*'] = "Todos";
						while($row1 = mysql_fetch_array($res1))
						{
							$html .="<option value=".$row1['Selcod'].">".$row1['Selcod']."-".$row1['Seldes']."</option>";
							$array_cobertura[$row1['Selcod']] = $row1['Selcod']."-".$row1['Seldes'];
						}
					}							
										
												
												
					$html.="				</select>
									   </td>
									   <td class='fila1' nowrap='nowrap'>
									   Tipo de Afiliaci&oacute;n :
									   </td>
									   <td class='fila2'>
											<select id='afiliacion'>
												<option value=''>
													Seleccione ...
												</option>
												<option value='*'>
													Todos
												</option>";
					$query  = "SELECT Seltip,Selcod,Seldes,Selpri
								 FROM ".$wbasedato."_000105 
								WHERE Selest ='on'
								  AND Seltip ='16'
							Order by  Selpri";	

					if($res1 = mysql_query($query,$conex))
					{
						$array_afiliacion = array();
						$array_afiliacion['*'] = "Todos";
						while($row1 = mysql_fetch_array($res1))
						{
							$html .="<option value=".$row1['Selcod'].">".$row1['Selcod']."-".$row1['Seldes']."</option>";
							$array_afiliacion[$row1['Selcod']] = $row1['Selcod']."-".$row1['Seldes'];
						}
					}				
												
					$html.="	   </select>
									   </td>
									</tr>
									<tr>
									   <td class='fila1' nowrap='nowrap'>
									   Pago Compartido :
									   </td>
									   <td class='fila2'>
											<select id='compartido'>
												<option value=''>
													Seleccione ...
												</option>
												<option value='*'>
													Todos
												</option>";
												
											
					$query  = "SELECT Seltip,Selcod,Seldes,Selpri
								 FROM ".$wbasedato."_000105 
								WHERE Selest ='on'
								  AND Seltip ='22'
							Order by  Selpri";	

					if($res1 = mysql_query($query,$conex))
					{
						$array_compartido = array();
						$array_compartido['*'] = "Todos";
						while($row1 = mysql_fetch_array($res1))
						{
							$html .="<option value=".$row1['Selcod'].">".$row1['Selcod']."-".$row1['Seldes']."</option>";
							$array_compartido[$row1['Selcod']] = $row1['Selcod']."-".$row1['Seldes'];
						}
					}				
											
					$html.="				</select>
									   </td>
									   <td class='fila1' nowrap='nowrap'>
									   Porcentaje :
									   </td>
									   <td class='fila2'>
											<input id='porcentaje' type='text'  class='entero'></input>
									   </td></tr><tr>
									   <td class='fila1' nowrap='nowrap'>
									   Tope Maximo :
									   </td>
									   <td class='fila2' >
											<input id='tope' type='text' class='entero2'></input>
									   </td>
									   <td class='fila1'>
									   Estado :
									   </td>
									   <td class='fila2'>
									   <input type='checkbox' id='activoinactivo' name='activo' value='on'>Activo</input>
									   </td>
									</tr>
									<tr>
									   <td class='fila1' nowrap='nowrap'>
									   Pago x Hospitalizaci&oacute;n :
									   </td>
									   <td class='fila2' >
											<input id='pagoxhospitalizacion' type='text' class='entero2'></input>
									   </td>
									   <td class='fila1'>
									  
									   </td>
									   <td class='fila2'>
									   
									   </td>
									</tr>
									<tr>
									<td colspan='8' align='center'><input type='button' id='botongrabar' value='Grabar' onclick='grabar()'><input type='button'  value='Cancelar' onclick='limpiar()'><td>
									</tr>
								</table></div></div>
							  </center>
							 ";
					$html.= '<input cuantos="'.$cuantos.'" type="hidden" id="hidden_empresas" value=\''.json_encode($array_empresa).'\' >';
				
					// selecciona los maestros que tiene configurado el usuario y son organizados por los nombres de sus tablas , quedando asi agrupados por grupos
					// de tablas matrix 
					$select = "SELECT Cfctem ,Cfcemp,Cfccob,Cfctip,Cfcniv,Cfcpor,Cfctma,Cfcest,Id,Cfcpxh
										FROM ".$wbasedato."_000290" ;

					if($res = mysql_query($select,$conex))
					{
						$html .= "<br><br>
										<div width='95%' id='accordionopciones'>
											<h3>Registros</h3>
												<div id='divppal'><table align='center' id='tableppal'>
										<tr class='encabezadoTabla'>
											<td>#</td>
											<td>Tipo de Empresa</td>
											<td>Empresa</td>
											<td>Cobertura en Salud</td>	
											<td>Tipo de Afiliaci&oacute;n</td>
											<td>Pago Compartido</td>
											<td>Porcentaje</td>
											<td>Tope Maximo</td>
											<td>Valor x hospitalizaci&oacute;n</td>
											<td>Activo</td>
											<td>Editar</td>
										</tr>";


						$k=1;
						while($row = mysql_fetch_array($res))
						{

							if (is_int ($k/2))
							   {
								$wcf="fila1";  // color de fondo de la fila
							   }
							else
							   {
								$wcf="fila2"; // color de fondo de la fila
							   }
							$html .="<tr class='".$wcf."'>";
							$html .="<td>".$k."</td>";
							
							$html .="<td>".$array_tipo[$row['Cfctem']]."</td>";
							$html .="<td>".$array_empresa[$row['Cfcemp']]."</td>";
							$html .="<td>".$array_cobertura[$row['Cfccob']]."</td>";
							$html .="<td>".$array_afiliacion[$row['Cfctip']]."</td>";
							$html .="<td>".$array_compartido[$row['Cfcniv']]."</td>";
							$html .="<td>".$row['Cfcpor']."</td>";
							$html .="<td>".number_format($row['Cfctma'],0,'.',',')."</td>";
							if($row['Cfcest']=='')
								$estado='on';
							else
								$estado=$row['Cfcest'];
							
							$estadovisible='Activo';
							if($estado=='off')
							{
								$estadovisible='Inactivo';
							}
							
							if($row['Cfcpxh']=='')
							{
								$row['Cfcpxh'] = 0;
							}
							$html .="<td align='right'>".number_format($row['Cfcpxh'],0,'.',',')."</td>";
							$html .="<td>".$estadovisible."</td>";
							$html .="<td><input type='button' value='Editar' onclick='editar(\"".$row['Id']."\" , \"".$row['Cfctem']."\" , \"".$row['Cfcemp']."\" , \"".$row['Cfccob']."\" , \"".$row['Cfctip']."\", \"".$row['Cfcniv']."\" ,  \"".$row['Cfcpor']."\" ,  \"".$row['Cfctma']."\" , \"".$estado."\", \"".$row['Cfcpxh']."\" )'></td>";
							$html .="</tr>";
							$k++;
						}
						$html .="</table></div></div>";
					}
					else
					{
						$html .="<div>No tiene asignado ningun permiso para acceder a los maestros de Matrix</div>";
					}

					$html.="<br><br><table align='center'><tr><td><input type='button' value='Cerrar' onclick='cerrar_ventana()'></td></tr></table></div>";

				//	$html .="<br><br><div id='divtabla' style='overflow-x:scroll;overflow-y:hidden;width:90%;height:90%' ></div>";
					$html .="<br><br><div id='divtabla' ></div>";
					$html .="<br><br><div id='oculta'></div>";
					echo $html;



			?>
    </body>
</html>