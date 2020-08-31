<?php
include_once("conex.php");

/*******************************************************************************************************************************************
*                                             REPORTE DE VERIFICACION DE DEVOLUCION		                                                   *
*******************************************************************************************************************************************

//==========================================================================================================================================
//PROGRAMA				      : Reporte para la verificación de devoluciones.                                                               |
//AUTOR				          : Ing. Gustavo Alberto Avendano Rivera.                                                                       |
//FECHA CREACION			  : AGOSTO 28 DE 2007.                                                                                          |                                                                                    |
//DESCRIPCION			      : Este reporte sirve para verificar las cantidas devueltas por enfermeria ya sea por alta o por devolución    |
//                              parcial.                                                                                                    |
//                            : EL 13 DE DICIEMBRE se cambia la tabla movhos_000002 por la movhos_000035, ahi se encuentra el usuario       |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//root_000051       : Tabla de Aplicaciones por Empresa.                                                                                    |
//usuarios          : Tabla de Usuarios con su codigo y descripcion.                                                                        |
//movhos_000002     : Tabla de Encabezado de Cargos.                                                                                        |
//movhos_000003     : Tabla de Detalle de Cargos.                                                                                           |
//movhos_000018     : Tabla de Ubicación Pacientes.                                                                                         |
//movhos_000026     : Tabla de Maestros de Articulos.                                                                                       |
//movhos_000028     : Tabla de Devoluciones.                                                                                                |
//cenpro_000002     : Tabla de Maestro de Articulos.        
//movhos_000143     : Tabla de Contingencia Detalle de Cargos. 

 Actualizaciones:
 Noviembre 12 de 2013 (Jonatan Lopez)		
			Se agregan "UNION" a las consultas donde se encuentre la tabla movhos_000003 
 			para que traiga los datos de contingencia (tabla movhos_00143) con estado activo.	 
 2012-11-23 (Frederick Aguirre Sanchez).
			Se cambia la estructura del script para hacer peticiones con ajax y bloquear la pantalla mientras se carga la respuesta.
			Se agrego la opcion de consultar para servicio farmaceutico(1050) o central de mezclas(1051) o ambos
			Se agrego la funcion de comun.php consultarAliasPorAplicacion para la consulta de los prefijos de bd
			Se agregaron los estilos del css de matrix
			Se cambiaron los titulos, se agruparon contenidos, y se cambio la forma en que se mostraban 
			los datos en la tabla para que tenga una vista mas agradable
			Se agrego la opcion que cuando el mouse pase sobre el nombre del paciente visualice el numero de documento
			Se cambio el array de resultados de la consulta por uno asociativo
 2012-11-27 (Frederick Aguirre Sanchez).
			Se modificaron los querys debido a que no estaba consultando correctamente las devoluciones para central de mezclas
			
//==========================================================================================================================================*/
$wactualiz="Noviembre 12 de 2013";

if(!isset($_SESSION['user'])){
	echo "error";
	return;
}

//La siguiente condicion se hace porque si existe el parametro action quiere decir que viene una solicitud ajax y no debe
//enviar como respuesta el texto "<html><head><title... " etc.
if(! isset($_REQUEST['action'] )){
	echo "<html>";
	echo "<head>";

	echo "<title>Reporte de verificación de devolución</title>";
	echo '<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />';
	echo '<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>';
	echo '<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>';
	echo '<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>';
}
	




include_once("root/comun.php");

if( isset( $wemp ) ){
	$wemp_pmla = $wemp;
}

$wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
$wcenpro = consultarAliasPorAplicacion($conex, $wemp_pmla, "cenmez");	


//**************************ENCARGADO DE RECIBIR LOS LLAMADOS CON AJAX********************************************//
if( isset($_REQUEST['action'] )){
	$action = $_REQUEST['action'];
	if($action=="consultar"){
		ejecutarConsulta( $_REQUEST['historia'], $_REQUEST['numero_devolucion'],$_REQUEST['fecha_inicio'], $_REQUEST['fecha_final'],  $_REQUEST['origen'] );
		return;
	}else{
		return;
	}
}
//FIN*LLAMADOS*AJAX**************************************************************************************************************//


 function ejecutarConsulta($whistoria, $wnumero_devolucion, $wfecha_inicio, $wfecha_final, $worigen){
	
		global $conex;
		global $wmovhos;
		global $wcenpro;
		global $wemp_pmla;
		
		$titulo_tabla ="Devoluciones ";
		if( $wfecha_inicio == $wfecha_final )
			$titulo_tabla.=" del dia ".$wfecha_inicio;
		else
			$titulo_tabla.=" entre el ".$wfecha_inicio." y el ".$wfecha_final;
		
		if( $whistoria != '' && $whistoria != '*' )
			$titulo_tabla.=" para la historia ".$whistoria;
			
		if( $wnumero_devolucion != '' && $wnumero_devolucion != '*' )
			$titulo_tabla.=" con el numero de devolucion ".$wnumero_devolucion;
		
		if( $worigen == "SF" || $worigen == '*' )
			$titulo_tabla.=" <br>en Servicio Farmaceutico "; 
		else
			$titulo_tabla.=" en ";
		if( $worigen == '*' )
				$titulo_tabla.=" y ";
		
		if( $worigen == "CM" || $worigen == '*' )
			$titulo_tabla.=" Central de Mezclas ";
			
		if( $whistoria == "" )
			$whistoria = '*';
			
		if ( $wnumero_devolucion == '' )
			$wnumero_devolucion = '*';
			
			
		
		echo "<table>";
		echo "<tr class=fila1><td align=center colspan=15><b>".$titulo_tabla."</b></td></tr>";	
			
		echo "<tr class=encabezadoTabla >";
		echo "<td align=center>Usuario que hace <br>la devolucion</td>";
		echo "<td align=center>Fecha</td>";
		echo "<td align=center>Habitacion</td>";
		echo "<td align=center>Historia</td>";
		echo "<td align=center>Ingreso</td>";
		echo "<td align=center>Nombre Paciente</td>";
		echo "<td align=center>Codigo Articulo</td>";
		echo "<td align=center>Nombre articulo</td>";
		echo "<td align=center>Cantidad</td>";
		echo "<td align=center>Causa de devolucion</td>";
		echo "<td align=center>Cantidad faltante</td>";
		echo "<td align=center>Causa del faltante</td>";
		echo "<td align=center>Usuario que verifica</td>";
		echo "<td align=center>Inconsistencia <br> en la verificacion</td>";
		echo "<td align=center>Numero de <br> devolucion </td>";
		echo "</tr>";
			 
		$and_historia ="";
		$and_numero_devolucion="";
		
		
		if ($whistoria != '*')	
			$and_historia = "   AND denhis = '".$whistoria."'";
		
		if( $wnumero_devolucion != '*' )
			$and_numero_devolucion = "   AND dencon = '".$wnumero_devolucion."' ";

			 
		$query1 = "";
		$groupby = "";
		
		if( $worigen == "SF" || $worigen == '*' ){
			 $query1.="SELECT  ED.dencon, ED.seguridad, ED.fecha_data, ED.denhis, ED.dening, DC.fdeari as fdeart, ART.artcom, "
						."  	  DD.devces, DD.devjud, DD.devcfs, DD.devjus, DD.devusu, DD.devcff, PAC.pacno1, PAC.pacno2, "
						."		  PAC.pacap1, PAC.pacap2, PAC.Pactid, ORI.Oriced, UBI.ubihac "
				."  FROM  ".$wmovhos."_000035 ED,".$wmovhos."_000003 DC,".$wmovhos."_000026 ART,".$wmovhos."_000028 DD,".$wmovhos."_000018 UBI,root_000037 ORI ,root_000036 PAC"
				." WHERE (ED.fecha_data between '".$wfecha_inicio."' and '".$wfecha_final."' )" 
				."   AND dencon = devcon"
				.$and_historia
				.$and_numero_devolucion
				."   AND denhis = ubihis"
				."   AND dening = ubiing"
				."   AND devnum = fdenum"
				."   AND devlin = fdelin"
				."   AND fdeart = artcod"
				."   AND denhis = orihis"
				."   AND ORI.oriori = '".$wemp_pmla."'"
				."   AND oriced = pacced"
				."   AND oritid = pactid";
				/*********************************************************************************************************************/
				/* Noviembre 12 de 2013 se agrega este union para traiga los datos de contingencia (tabla movhos_00143). Jonatan Lopez
				/*********************************************************************************************************************/	
				$query1.=" UNION ";
				$query1.="SELECT  ED.dencon, ED.seguridad, ED.fecha_data, ED.denhis, ED.dening, DC.fdeari as fdeart, ART.artcom, "
				."  	  DD.devces, DD.devjud, DD.devcfs, DD.devjus, DD.devusu, DD.devcff, PAC.pacno1, PAC.pacno2, "
				."		  PAC.pacap1, PAC.pacap2, PAC.Pactid, ORI.Oriced, UBI.ubihac "
				."  FROM  ".$wmovhos."_000035 ED,".$wmovhos."_000143 DC,".$wmovhos."_000026 ART,".$wmovhos."_000028 DD,".$wmovhos."_000018 UBI,root_000037 ORI ,root_000036 PAC"
				." WHERE (ED.fecha_data between '".$wfecha_inicio."' and '".$wfecha_final."' )" 
				."   AND dencon = devcon"
				.$and_historia
				.$and_numero_devolucion
				."   AND denhis = ubihis"
				."   AND dening = ubiing"
				."   AND devnum = fdenum"
				."   AND devlin = fdelin"
				."   AND fdeart = artcod"
				."   AND denhis = orihis"
				."   AND ORI.oriori = '".$wemp_pmla."'"
				."   AND oriced = pacced"
				."   AND oritid = pactid"
				."   AND fdeest = 'on'"; //Se agrega este filtro para que solo muestre los registros activos.
		}
		if($worigen == '*' ){
				$query1.=" UNION ";
		}
		if( $worigen == "CM" || $worigen == '*' ){
				$query1.="SELECT  ED.dencon, ED.seguridad, ED.fecha_data, ED.denhis, ED.dening, DC.fdeari as fdeart, ART.artcom, "
						."  	  COUNT(DISTINCT fdenum) as devces, DD.devjud, DD.devcfs, DD.devjus, DD.devusu, DD.devcff, PAC.pacno1, PAC.pacno2, "
						."		  PAC.pacap1, PAC.pacap2, PAC.Pactid, ORI.Oriced, UBI.ubihac "
						."  FROM  ".$wmovhos."_000035 ED,".$wmovhos."_000003 DC,".$wcenpro."_000002 ART,".$wmovhos."_000028 DD,".$wmovhos."_000018 UBI,root_000037 ORI ,root_000036 PAC"
						." WHERE  (ED.fecha_data between '".$wfecha_inicio."' and '".$wfecha_final."' )"
						."   AND  ED.dencon = DD.devcon"
						.$and_historia
						.$and_numero_devolucion
						."   AND DD.devnum = DC.fdenum"
						."   AND DC.fdeari = ART.artcod"
						."   AND DC.fdelot != '' "
						."   AND ED.denhis = UBI.ubihis"
						."   AND DD.devlin = 0 "
						."   AND ED.dening = UBI.ubiing"
						."   AND ED.denhis = ORI.orihis"
						."   AND ORI.oriori = '".$wemp_pmla."'"
						."   AND ORI.oriced = PAC.pacced"
						."   AND ORI.oritid = PAC.pactid"	
						." 	 GROUP BY denhis, dening, fdeari ";
						/*********************************************************************************************************************/
						/* Noviembre 12 de 2013 se agrega este union para traiga los datos de contingencia (tabla movhos_00143). Jonatan Lopez
						/*********************************************************************************************************************/	
						$query1.=" UNION ";
						$query1.="SELECT  ED.dencon, ED.seguridad, ED.fecha_data, ED.denhis, ED.dening, DC.fdeari as fdeart, ART.artcom, "
						."  	  COUNT(DISTINCT fdenum) as devces, DD.devjud, DD.devcfs, DD.devjus, DD.devusu, DD.devcff, PAC.pacno1, PAC.pacno2, "
						."		  PAC.pacap1, PAC.pacap2, PAC.Pactid, ORI.Oriced, UBI.ubihac "
						."  FROM  ".$wmovhos."_000035 ED,".$wmovhos."_000143 DC,".$wcenpro."_000002 ART,".$wmovhos."_000028 DD,".$wmovhos."_000018 UBI,root_000037 ORI ,root_000036 PAC"
						." WHERE  (ED.fecha_data between '".$wfecha_inicio."' and '".$wfecha_final."' )"
						."   AND  ED.dencon = DD.devcon"
						.$and_historia
						.$and_numero_devolucion
						."   AND DD.devnum = DC.fdenum"
						."   AND DC.fdeari = ART.artcod"
						."   AND DC.fdelot != '' "
						."   AND ED.denhis = UBI.ubihis"
						."   AND DD.devlin = 0 "
						."   AND ED.dening = UBI.ubiing"
						."   AND ED.denhis = ORI.orihis"
						."   AND ORI.oriori = '".$wemp_pmla."'"
						."   AND ORI.oriced = PAC.pacced"
						."   AND ORI.oritid = PAC.pactid"
						."   AND fdeest = 'on'" //Se agrega este filtro para que solo muestre los registros activos.						
						." 	 GROUP BY denhis, dening, fdeari ";
						
		}
		
		$query1.=" ORDER BY denhis,dening,fdeart";

		$err1 = mysql_query($query1,$conex);
		$num1 = mysql_num_rows($err1);

		for ($i=1;$i<=$num1;$i++){
			if (is_int ($i/2))
				$wclass="fila1";  
			else
				$wclass="fila2"; 

		$row1 = mysql_fetch_assoc($err1);

		$usua = explode('-',$row1['seguridad']);

		$usudev=$usua[1]; 

		$query2 = " SELECT descripcion "
				 ."   FROM usuarios "
				 ."  WHERE codigo='".$usudev."'";

		$err2 = mysql_query($query2,$conex);
		$num2 = mysql_num_rows($err2);

		$row2 = mysql_fetch_array($err2);

		$nomrdev = $row2[0];

		if ($row1['devusu'] != "" ){
			$query3 = " SELECT descripcion "
				     ."   FROM usuarios "
					 ."  WHERE codigo='".$row1['devusu']."'";

			$err3 = mysql_query($query3,$conex);
			$num3 = mysql_num_rows($err3);

			$row3 = mysql_fetch_array($err3);

			$nombveri = $row3[0];
		}else{
			$nombveri = "SIN VERIFICAR";	
		}

		echo "<tr class=".$wclass.">";
		echo "<td nowrap='nowrap' class='izquierda'>$nomrdev</td>";//usuario que hace la devolucion
		echo "<td nowrap='nowrap' align=center>".$row1['fecha_data']."</td>";//fecha
		echo "<td align=center>".$row1['ubihac']."</td>";//habitacion
		echo "<td align=center>".$row1['denhis']."</td>";//historia
		echo "<td align=center>".$row1['dening']."</td>";//ingreso
		echo "<td nowrap='nowrap' class='msg_tooltip izquierda' title='".$row1['Pactid']." - ".$row1['Oriced']."' align=center>".$row1['pacno1']." ".$row1['pacno2']." ".$row1['pacap1']." ".$row1['pacap2']."</td>";//nombre paciente
		echo "<td align=center>".$row1['fdeart']."</td>";//codigo articulo
		echo "<td align=center>".$row1['artcom']."</td>";//nombre articulo
		echo "<td align=center>".$row1['devces']."</td>";//cantidad
		echo "<td align=center>".$row1['devjud']."</td>";//causa dev   devcfs,devjus,devusu,devcff,
		echo "<td align=center>".$row1['devcfs']."</td>";//cantidad faltante
		echo "<td align=center>".$row1['devjus']."</td>";//causa del faltante
		echo "<td nowrap='nowrap' class='izquierda'>".$nombveri."</td>";//nombre quien verifica serv farm
		echo "<td align=center>".$row1['devcff']."</td>";//inconsis
		echo "<td align=center>".$row1['dencon']."</td>";//numero devolucion
		echo "</tr>";     
	       
	}
		
	echo "</table>"; // cierra la tabla o cuadricula de la impresión
				
}

function vistaInicial(){

	global $wemp_pmla;
	global $wactualiz;
	
	encabezado("Reporte Verificacion de Devoluciones",$wactualiz, 'clinica');
	
	echo "<input type='hidden' id ='wemp_pmla' value='".$wemp_pmla."'/>";
	
	$fecha_hoy = date("Y-m-d");
	$fecha_pddm = date("Y-m");
	$fecha_pddm.="-01";
	
	echo '<center>';
	echo '<span class="subtituloPagina2 rep_parametros">Parámetros de consulta</span>';
	echo '</center>';
	echo '<br><br>';

	echo "<table align='center' class='rep_parametros'>";
	echo "<tr>";
	echo "<td class='fila1'><b>Fecha Inicial</b></td>";
	echo "<td class='fila2'>";
	campoFechaDefecto( "f_inicial", $fecha_pddm );
	echo "</td>";
	echo "</tr>";
	
	echo "<tr>";
	echo "<td class='fila1'><b>Fecha Final</b></td>";
	echo "<td class='fila2'>";
	campoFechaDefecto( "f_final", $fecha_hoy );
	echo "</td>";
	echo "</tr>";
	
	echo "<tr>";
	echo "<td class='fila1'><b>Historia o (*) Todas:</b></td>";
	echo "<td class='fila2'><INPUT TYPE='text' id='historia' /></td>";
	echo "</tr>";
	
	echo "<tr>";
	echo "<td class='fila1'><b>Numero Devolucion o (*) Todas:</b></td>";
	echo "<td class='fila2'><INPUT TYPE='text' id='numero_devolucion' /></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td class='fila1' align='center' colspan=2><b>Origen:</b></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td class='fila2' align='center' colspan=2><INPUT type='radio' name='origen' value='*' checked> Servicio Farmaceutico y<br> Central de mezclas</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td class='fila2' align='center' colspan=2><INPUT type='radio' name='origen' value='SF'> Servicio Farmaceutico</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td class='fila2' align='center' colspan=2><INPUT type='radio' name='origen' value='CM'> Central de mezclas</td>";
	echo "</tr>";
	
	echo "</table>";
   
	echo "<br><br>";
	echo '<center>';
	echo "<input type='button' value='Consultar' id='consultar' class='rep_parametros' style='width:100' />";
	echo "<br><br>"; 
	echo "<div id='resultados'></div>";
	echo "<br><br>"; 
	echo "<a id='enlace_retornar' href='#' >RETORNAR</a>";
	echo "<br><br>";
	echo "<input type=button id='cerrar_ventana' value='Cerrar Ventana' onClick='javascript:cerrarVentana()' />";
	echo "<br><br>"; 
	echo "<br><br>";
	//Mensaje de espera
	echo "<div id='msjEspere' style='display:none;'>";
	echo '<br>';
	echo "<img src='../../images/medical/ajax-loader5.gif'/>";
	echo "<br><br> Por favor espere un momento ... <br><br>";
	echo '</div>';
	echo '</center>';
   
}

?>
<style type="text/css">
	#tooltip{color: #2A5DB0;font-family: Arial,Helvetica,sans-serif;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:5px;opacity:1;}
	#tooltip h3, #tooltip div{margin:0; width:auto}
				
	.centrar{
		text-align:center;
		nowrap: nowrap;
	}
	.izquierda{
		text-align:left;
		nowrap: nowrap;
	}
	.fila1{
			font-size: 9pt;
		}
		.fila2{
			font-size: 9pt;
		}
</style>

<script>

//************cuando la pagina este lista...**********//
			$(document).ready(function() {
				//Cuando cargue completamente la pagina
				
				//agregar eventos a campos de la pagina
				$("#consultar").click(function() {
					realizarConsulta();
				});
				
				$("#enlace_retornar").hide();
				$("#enlace_retornar").click(function() {
					restablecer_pagina();
				});
			});
			
			function restablecer_pagina(){
				//$(".rep_parametros").fadeIn('slow');
				$('#resultados').hide('slow');
				$("#enlace_retornar").hide('slow');
				$("#historia").val('');
				$("#numero_devolucion").val('');
			}
			
			function realizarConsulta(){
			
				var wemp_pmla = $("#wemp_pmla").val();
				var historia = $("#historia").val();
				var num_devolucion = $("#numero_devolucion").val();
				var f_inicio = $("#f_inicial").val();
				var f_final = $("#f_final").val();
				var origen = $('input[name=origen]:checked').val();

				//muestra el mensaje de cargando
				$.blockUI({ message: $('#msjEspere') });
				
				$("#enlace_retornar").fadeIn('slow');
				
				//se crea una variable aleatoria para el llamado ajax evitando que se almacene una respuesta fija en cache
				var rango_superior = 245;
				var rango_inferior = 11;
				var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
						
				//Realiza el llamado ajax con los parametros de busqueda
				$.get('rep_veridevo.php', { wemp_pmla: wemp_pmla, action: "consultar", historia: historia, numero_devolucion: num_devolucion, fecha_inicio: f_inicio, fecha_final: f_final, origen: origen, consultaAjax: aleatorio} ,
					function(data) {
						//oculta el mensaje de cargando
						//$(".rep_parametros").hide();
						$.unblockUI();
						$('#resultados').html(data);	
						$('#resultados').show('slow');		
						$(".msg_tooltip").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });						
					});			
			}
</script>
</head>
    <body>
		<!-- LO QUE SE MUESTRA AL INGRESAR POR PRIMERA VEZ -->
			<?php
					vistaInicial();
			?>
    </body>
</html>
