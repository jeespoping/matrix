<html>
<head>
  <title>REGISTRO DE ALTAS</title>
</head>
<body>	
<script type="text/javascript" src="../../../include/root/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.core.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.tabs.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.draggable.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>

<script type="text/javascript" src="../../../include/root/jquery.dimensions.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.simple.tree.js"></script>

<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
<script type="text/javascript">
	
	function enter()
	{
	 document.forms.altas.submit();
	}
	
	function enter3(){
		$.unblockUI();
		
		var clave = document.getElementById( "pwClaveCancelarAlta" ).value;
		document.getElementById( "inClaveCancelarAlta" ).value = clave;

		enter();
	}
	
	function enter2()
	{
		$.blockUI({ message: $('#divClaveCancelarAlta') });
	}
	
	function agregarJusti()
	{
		var obj = document.getElementById('mjust');
		a = obj.options[obj.selectedIndex].value;
		if(a=="--")
		{
			alert("Debe seleccionar una justificaci\xf3n");
		}else
			{
				$.unblockUI();
				b = document.createElement("input");
				b.type ="text";				
				b.name = "wjust";		
				b.id = "wjust";
				b.value = a;
				document.altas.appendChild(b);
				var f = b.name;
				document.forms.altas.submit();
				
			}
			document.forms.altas.submit();
	}
		
	
	function fnMostrar(){
	
			$.blockUI({ message: $("#menuJusti"), 
							css: { left: ( $(window).width() - 800 )/2 +'px', 
								    top: ( $(window).height() - $("#menuJusti").height() )/2 +'px',
								  width: '800px'
								 } 
					  });
	}
	
	function cerrarVentana()
	 {
      window.close();	
     }
	

	$(document).ready( function ()
	{	
	 
		$(".msg").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: 0 });
		
	});
</script>

<?php
include_once("conex.php");
  /***********************************************
   *              REGISTRO DE ALTAS              *
   *     		  CONEX, FREE => OK		         *
   ***********************************************/
@session_start();

if (!isset($user))
	if(!isset($_SESSION['user']))
	  session_register("user");
	
if(!isset($_SESSION['user']))
	echo "error";
else
{	            
 	
  
  
  include_once("root/magenta.php");
  include_once("root/comun.php");
  include_once("movhos/movhos.inc.php");
  
  $conex = obtenerConexionBD("matrix");
	                                                           // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
  $wactualiz="Noviembre 7 de 2018";                                     // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                                           // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
	

            
/*	                                                           
//=========================================================================================================================================\\
//=========================================================================================================================================\\
//ACTUALIZACIONES
//=========================================================================================================================================\\
//DESCRIPCION Agosto 11 de 2020 Edwin MG
//Se cambia el calculo de los dias de estancia desde la fecha y hora de ingreso la servicio hasta la fecha y hora actual (egreso del servicio),
//antes el calculo se realizaba desde la fecha de ingreso del servicio a media noche hasta la fecha y hora actual (fecha y hora de egreso del servicio)
//=========================================================================================================================================\\
//DESCRIPCION Noviembre 7 de 2017 Jonatan
//Se agrega la funcion cancelarPedidoInsumos para que al momento de dar alta definitiva al paciente se cancele el pedido de Insumos.
//=========================================================================================================================================\\
//DESCRIPCION  Junio 12 de 2017 Jonatan
//Se valida si el paciente tiene saldo en insumos, si es asi no le permite marcar el alta definitva.
//=========================================================================================================================================\\
//DESCRIPCION  Enero 18 de 2017 Jonatan
//=========================================================================================================================================\\
//Se valida que los pacientes listados no tengan egreso en la tabla cliame_000108, si es asi no se muestra en la lista.
//=========================================================================================================================================\\
//DESCRIPCION  Agosto 29 de 2013 Jonatan
//=========================================================================================================================================\\
//Se actualiza la tabla movhos_000067 para poner el paciente en la habitacion donde estaba cuando se cancela una muerte o un alta definitiva
//en las funciones que actualizan los indicadores, asi como actualizaban movhos_000038
//=========================================================================================================================================\\
//DESCRIPCION  Agosto 21 de 2013 Jonatan
//=========================================================================================================================================\\
//Cuando se marca la muerte, tambien se marca el alta en proceso, con su fecha y hora.
//=========================================================================================================================================\\
//DESCRIPCION  Agosto 1 de 2013
//=========================================================================================================================================\\
//Se comenta el update que da alta definitiva (Ubiald='on') en la tabla 18 de movimiento hospitalario cuando un paciente tiene más de 10 días
//de antiguedad en la clínica, esto porque este proceso de alta ya lo hace el cron hce/procesos/pacientes_unix_matrix.php
//=========================================================================================================================================\\
//DESCRIPCION  Julio 22 de 2013
//=========================================================================================================================================\\
//Se limita la cancelacion de la muerte del paciente a las mismas horas de cancelacion de alta definitiva, si se pasa de esas horas mostrará
//un mensaje diciendo que ya no es posible cancelar la muerte.
//=========================================================================================================================================\\
//DESCRIPCION  Febrero 25 de 2013
//=========================================================================================================================================\\
//Cuando se cancela una muerte o se cancela un alta definitiva, el programa restaura los valores en la tabla de indicadores.
//=========================================================================================================================================\\
//DESCRIPCION  Enero 23 de 2013
//=========================================================================================================================================\\
//Cuando se registra una muerte, se cambio el calculo para determinar si es mayor o menor a 48 horas.
//El calculo se hace contando las horas desde que ingreso a la clinica, se hacia desde que ingreso al servicio.
//=========================================================================================================================================\\
//DESCRIPCION  Noviembre 07 de 2012
//=========================================================================================================================================\\
//Cuando se cancela un alta definitiva, se busca si se esta haciendo aseo para la habitación, de ser así se borra el registro de aseo
//=========================================================================================================================================\\
//DESCRIPCION  Octubre 09 de 2012
//=========================================================================================================================================\\
//Al digitar la clave requerida para cancelar un alta definitiva se muestra como password, es decir que el usuario no puede ver lo que escribe
//=========================================================================================================================================\\
//DESCRIPCION  Octubre 08 de 2012
//=========================================================================================================================================\\
//Se cancelan las aplicaciones que hallan posteriores al momento de dar alta o muerte al paciente.
//Para cancelar un alta definitiva, el usuario debe tener permiso para cancelarla e ingresar una clave para dicho fin.
//=========================================================================================================================================\\
//DESCRIPCION  Julio 10 de 2012
//=========================================================================================================================================\\
//Se da la opcion de cancelacion de alta definitiva para los pacientes que han salido en las ultimas horas( segun parametro
//tiempoReactivacionAltas en root 000051)
//=========================================================================================================================================\\
//DESCRIPCION  Junio 03 de 2012                                                                                                           \\
//=========================================================================================================================================\\
// Se incluye el archivo movhos.inc.php para poder utilizar la funcion  cancelar_pedido_alimentacion() 										\\                                                                                                                           \\
//=========================================================================================================================================\\
//DESCRIPCION  Mayo 17 de 2012                                                                                                           \\
//=========================================================================================================================================\\
//Se agrega la validacion de que a aquellos centros de costos que no requieran promediar las altas, no se les pida la justificacion cuando  \\
// superan el tiempo de alta meta. este cambio se hizo en la funcion, require_justificacion().
//=========================================================================================================================================\\
//=========================================================================================================================================\\
//DESCRIPCION  Mayo 02 de 2012                                                                                                           \\
//=========================================================================================================================================\\
//Se cambió la lógica de la validación de necesitar o no justificación, lo que se hizo fue que en el momento de listar los pacientes, el sistema \\
//verifica si este necesita justificación de retraso, en el caso de que se le quiera dar alta definitiva, cuando esto es verdadero el campo       \\
//se va a presentar con fondo rojo para que el encargado sepa que se le va a pedir justificación.                                                 \\
//Cuando el encargado seleccione la opcion alta definitiva estando esta en color rojo se desplegara el menú de selección de justificación.         \\
//=========================================================================================================================================\\
//DESCRIPCION  Abril 26 de 2012                                                                                                           \\
//=========================================================================================================================================\\
//Se agregó la validación de si se necesita o no la justificación antes de guardar el alta definitiva, en caso de que sea necesaria, el programa
//pide la justificación, desplegando en un menú todas las justificaciones que están habilitadas para el retraso.
//Básicamente lo que se hace es preguntar si se requiere justificación utilizando la función "requiere_justificacion()", en caso de que el 
//resultado sea falso, permite realizar el guardado como se venia haciendo anteriormente, en caso contrario se despliegua un iframe que contiene 
//un dropdown el que se cargan las opciones de justificación, este enviá a la página principal la justificación y continua el proceso.
//=========================================================================================================================================\\
//DESCRIPCION  Marzo 14 de 2012                                                                                                            \\
//=========================================================================================================================================\\
//Se adiciona la validacion para traer la Central correspondiente al motivo de la solicitud, según el centro de costo y el tipo de central 
//asociado al motivo de solicitud configurados en la tabla _000004 y 000009 de cencam. 
//=========================================================================================================================================\\
//AGOSTO 13 de 2010                                                                                                                        \\
//=========================================================================================================================================\\
//Se adiciona en el campo de la habitacion el nombre del paciente                                                                          \\
//=========================================================================================================================================\\
//MARZO 1 de 2010                                                                                                                          \\
//=========================================================================================================================================\\
//La cancelacion se coloca dentro de un for porque puede ser que hagan los pedidos por adelantado y se de alta al paciente con uno o varios\\
//servicios de alimentacion pedidos.                                                                                                       \\
//========================================================================================================================================\\
//FEBRERO 10  DE 2010:                                                                                                                    \\
//Se modifica el programa para que cuando se de clic en el radio boton de ALTA DEFINITIVA, Cancele el pedido de alimentacion del paciente \\
//si es que lo tiene, la funcion busca si hay algun servicio que se pueda cancelar en ese instante y si el paciente tiene algun pedido de \\
//ese Servivio y si es asi lo CANCELA, dejando tambien un registro en el LOG o Auditoria en la tabla _000078.                             \\
//========================================================================================================================================\\
//OCTUBRE   6   DE 2009:                                                                                                                  \\
//Se modifica para que cuando un alta se demora mas del tiempo promedio fijado como meta, pida la causa de la demora parametrizadamenete  \\
//========================================================================================================================================\\
//A B R I L   16   DE 2009:                                                                                                               \\
//Se modifica la hoja de estilo por la institucional                                                                                      \\
//========================================================================================================================================\\
//M A Y O   15   DE 2008:                                                                                                                 \\
//Se adiciona la columna de CANCELAR MUERTE, para que cuando se equivoquen al marcar una Muerte, este error lo puedan corregir desde el   \\
//el servicio que se cometio y no halla necesidad de llamar a Sistemas, esta opción reversa todos los movimientos que se hallan realizado \\
//con la marcacion de la muerte, como son liberar la cama, egresar el paciente, modificar la ubicacion del paciente (0000189, todo esto se\\
//reversa.                                                                                                                                \\
//========================================================================================================================================\\
//M A Y O   15   DE 2008:                                                                                                                 \\
//Se actualiza para que cuando se marque la muerte inmediatamente se haga el egreso en el sistema de altas y no se espere a que realicen  \\
//el 'alta definitiva'.                                                                                                                   \\
//========================================================================================================================================\\
*/
//Consulta la fecha y hora de muerte de un paciente.
function consultar_datos_muerte($conex, $wbasedato, $whis, $wing)
	{
	
	$array_datos_mue = array();
	
	$sql = "SELECT Fecha_egre_serv, Hora_egr_serv
			  FROM {$wbasedato}_000033
		  	 WHERE Historia_clinica = '$whis'
			   AND Num_ingreso 	    = '$wing'
			   AND Tipo_egre_serv LIKE '%muerte%'";					
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$row = mysql_fetch_array( $res );
	
	$array_datos_mue = array('fecha_muerte'=>$row['Fecha_egre_serv'],'hora_muerte'=>$row['Hora_egr_serv']);
	
	return $array_datos_mue;
	
	}


function validarUsuarioCancelarAlta( $conex, $wemp_pmla, $usuario, $clave ){

	$val = false;
	
	//Primero busco si el usuario tiene permiso para cancelar el alta
	
	//Consulto los usuario con permiso
	$usuariosPermiso = consultarAliasPorAplicacion($conex, $wemp_pmla, "cancelarAltasDefinitiva" );	//Esto devuelve los usuarios con permiso
	
	$expUsuarios = explode( "-", $usuariosPermiso );
	
	//verifico que el usuario si se tenga el permiso
	foreach( $expUsuarios as $keyUsuario => $valueUsuario ){
		
		//si tiene permiso verifico que la clave si sea correcta
		if( $valueUsuario == $usuario ){
			
			$sql = "SELECT *
					FROM usuarios
					WHERE
						codigo = '$usuario'
						AND Password = SHA1('$clave')
						AND activo = 'A'
					";
					
			$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$num = mysql_num_rows( $res );
			
			if( $num > 0 ){
				$val = true;
			}
			break;
		}
	}
	
	return $val;
}

/************************************************************************************
 * Determina si un paciente se encuentra en un cco que maneja altas
 ************************************************************************************/
function tieneCpxPorHistoriaAltas( $conex, $bd, $his, $ing ){
	
	$val = false;
	
	$sql = "SELECT
				*
			FROM
				{$bd}_000011 a, {$bd}_000018 b
			WHERE
				ccocpx = 'on'
				AND ccoest = 'on'
				AND ccourg != 'on'
				AND ubihis = '$his'
				AND ubiing = '$ing'
				AND ubisac = ccocod
			";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en elquery $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		$val = true;
	}
	
	return $val;
}




/************************************************************************************************************************************
 * Las fechas estan dadas en formato unix
 *
 * $fechaActual				YYYY-MM-DD
 * $fechaInicio				formato Unix (fecha y hora)
 * $frecuencia				en segundos
 * $terminacionDia			Horas maximas de terminacion del medicamento
 * $dosis					Cantidad de dosis por unidad de articulo (kadcfr/kadcma)
 * $fechaCancelacionAlta	formato Unix (fecha y hora)
 ************************************************************************************************************************************/
function crearRegleta( $fechaActual, $terminacionDia, $fechaInicio, $frecuencia, $dosis, $fechaCancelacionAlta, &$fechorUltimaRonda ){

	$val = "";
	
	$dosis = round( $dosis, 3 );

	$fechorActual = strtotime( $fechaActual." 00:00:00" );
	$terminacionDia = $fechorActual+($terminacionDia+24)*3600;
	
	for( $i = $fechorActual; $i < $terminacionDia; $i += 2*3600 ){
	
		//Si es mayor a la fecha y hora de inicio
		if( $i >= $fechaInicio  ){
			
			if( ( $i - $fechaInicio )%$frecuencia == 0 ){
				
				if( $i < $fechaCancelacionAlta ){
					$val .= ",".date( "H:i:s", $i )."-$dosis-$dosis";
					$fechorUltimaRonda = $i;
				}
				else{
					if( $i == $fechorActual ){
						$val .= ",Ant-$dosis-0";
					}
					else{
						$val .= ",".date( "H:i:s", $i )."-$dosis-0";
					}
				}
			}
			else{
				$val .= ",".date( "H:i:s", $i )."-0-0";
			}
		}
		else{
			$val .= ",".date( "H:i:s", $i )."-0-0";
		}
	}
	
	return substr( $val, 1 );
}

/************************************************************************
 * Pinta la tabla de pacientes que fueron dados de alta
 ************************************************************************/
function pintarPacientesAltaDefinitiva( $res, $fecha ){

	$num = mysql_num_rows( $res );

	if( $num > 0 ){
		
		echo "<br><br>";
		
		echo "<center><b>Pacientes dados de alta a partir de las $fecha</b></center>";
		
		echo "<br>";
		
		echo "<table align='center'>";
		
		echo "<tr class='encabezadotabla' align='center'>";
		
		echo "<td>Historia</td>";
		echo "<td>Paciente</td>";
		echo "<td>Cancelar Alta Definitiva</td>";
		
		echo "</tr>";
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
		
			$class = "class='fila".($i%2 + 1 )."'";
		
			echo "<tr $class>";
			
			//Historia
			echo "<td align='center'>";
			echo $rows[ 'Ubihis' ]."-".$rows['Ubiing'];
			echo "</td>";
			
			//Paciente
			echo "<td align='center'>";
			echo $rows[ 'Pacno1' ]." ".$rows['Pacno2']." ".$rows['Pacap1']." ".$rows['Pacap2'];
			echo "</td>";
			
			//Cancelar Alta
			echo "<td align='center'><INPUT type='radio' name='inCancelarAltaDef' value='{$rows[ 'Ubihis' ]}-{$rows[ 'Ubiing' ]}' onClick='enter2()'></td>";
			
			echo "</tr>";
		}
		
		echo "</table>";
		
		echo "<INPUT type='hidden' name='inClaveCancelarAlta' id='inClaveCancelarAlta'>";
		
		echo "<br>";
	}
}


/******************************************************************************************
 * Consulto los pacientes que se le han dado de alta despues de una fecha y hora determinada
 *
 * @param	$fecha:	Formato de fecha en formato Unix
 ******************************************************************************************/
function consultarUltimasAltas( $conex, $wbasedato, $fecha, $origen, $cco )
{
	$sql = "SELECT * 
			FROM 
				{$wbasedato}_000018, root_000036, root_000037
			WHERE
				ubihis = orihis
				AND ubiing =  oriing
				AND oriori = '$origen'
				AND oritid = pactid
				AND oriced = pacced
				AND ubisac = '$cco'
				AND ( ubifad > '".date( "Y-m-d", $fecha )."'
				 OR ( ubifad = '".date( "Y-m-d", $fecha )."'
				AND ubihad >= '".date( "H:i:s", $fecha )."' )
				)
				";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	return $res;
}



/************************************************************************************
 * Consulta la frecuncia en horas pra un articulo
 ************************************************************************************/
function consultarFrecuencia( $conex, $wbasedato, $codigo ){

	$val = false;

	$sql = "SELECT 
				*
			FROM 
				{$wbasedato}_000043
			WHERE 
				Percod = '{$codigo}'
			;";
				 
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array($res) ){
		$val = $rows[ 'Perequ' ];
	}
	
	return $val;
}

// separando cada campo por el caracter |
function obtenerRegistrosFila($qlog)
{
	global $conex; 

	$reslog = mysql_query($qlog, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qlog . " - " . mysql_error());
	$rowlog = mysql_fetch_row($reslog);
	$datosFila = implode("|", $rowlog);
	return $datosFila;
}


/****************************************************************************************
	* Agregada en 2013-02-22, 
	ANTES DE INSERTAR UNA ALTA O UNA MUERTE PARA UN PACIENTE SE CONSULTA SI YA TUVO ALTA O MUERTE Y SE ELIMINAN, Y ACTUALIZA EN INDICADOR
 ****************************************************************************************/
function BorrarAltasMuertesAntesDeAgregarNueva($conex, $wbasedato, $whis, $wing, $bandera)
{
	$user_session = explode('-',$_SESSION['user']);
	$seguridad = $user_session[1];
	
	if( !isset( $bandera ) ){
		$bandera = "";
	}	
 
	$q = "    SELECT * 
			FROM ".$wbasedato."_000033 
			WHERE Historia_clinica = '".$whis."' 
			AND Num_ingreso = '".$wing."'
			AND Tipo_egre_serv REGEXP 'MUERTE MAYOR A 48 HORAS|MUERTE MENOR A 48 HORAS|ALTA|MUERTE' ";
			
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);

	$arregloDatos = array();

	if ($num > 0)
	{
		while($row = mysql_fetch_assoc($res))
		{
			$result = array();
			$result['fecha'] = $row['Fecha_data'];
			$result['cco'] = $row['Servicio'];
			$result['egreso'] = $row['Tipo_egre_serv'];
			array_push( $arregloDatos, $result );                
		}
	}

	if( count( $arregloDatos )  > 0 )
	{

		foreach( $arregloDatos as $dato )
		{
		
			$wfecha = $dato['fecha'];
			$wcco = $dato['cco'];
			$wtipoEgresoABorrar = $dato['egreso'];
			
			$q = " SELECT * "
				."   FROM ".$wbasedato."_000038 "
				."  WHERE Fecha_data = '".$wfecha."'"
				."    AND Cieser = '".$wcco."'";
			
			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num = mysql_num_rows($res);
			$row = mysql_fetch_assoc($res);    


			$existe_en_la_67 = false;
			$q67 = " SELECT * "
				."   FROM ".$wbasedato."_000067 "
				."  WHERE Fecha_data = '".$wfecha."'"
				."    AND Habhis = '".$whis."'"
				."    AND Habing = '".$wing."'";
				
			$res67 = mysql_query($q67,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num67 = mysql_num_rows($res67);
			if( $num67 > 0 ){
				$existe_en_la_67 = true;
			}
			
			$cant_egresos = $row['Cieegr'];
			$cant_camas_ocupadas = $row['Cieocu'];
			$cant_camas_disponibles = $row['Ciedis'];
			$muerteMayor = $row['Ciemmay'];
			$muerteMenor = $row['Ciemmen'];
			$egresosAlta = $row['Cieeal'];
			//Restamos uno al motivo de egreso que tenia el paciente
			
			if(preg_match('/ALTA/i',$wtipoEgresoABorrar))
			{
				$egresosAlta--;
				$cant_egresos--;
				if( $existe_en_la_67 === false )
				{
					$cant_camas_ocupadas++;
					$cant_camas_disponibles--;
				}
			}
			else if(preg_match('/MAYOR/i',$wtipoEgresoABorrar)) //Muerte mayor
			{
				$muerteMayor--;
				$cant_egresos--;
				if( $existe_en_la_67 === false )
				{
					$cant_camas_ocupadas++;
					$cant_camas_disponibles--;
				}
			}
			else if(preg_match('/MENOR/i',$wtipoEgresoABorrar))
			{ // Muerte menor
				$muerteMenor--;    
				$cant_egresos--;
				if( $existe_en_la_67 === false )
				{
					$cant_camas_ocupadas++;
					$cant_camas_disponibles--;
				}
			}
			
			$query_para_log = "    SELECT * 
				FROM ".$wbasedato."_000033 
				WHERE Historia_clinica = '".$whis."' 
				AND Num_ingreso = '".$wing."'
				AND Tipo_egre_serv = '".$wtipoEgresoABorrar."'";
			$registrosFila = obtenerRegistrosFila($query_para_log);

			$q ="    DELETE FROM ".$wbasedato."_000033 
					 WHERE Historia_clinica = '".$whis."' 
					   AND Num_ingreso = '".$wing."'
					   AND Tipo_egre_serv = '".$wtipoEgresoABorrar."'";
			$res = mysql_query($q,$conex); 

			$num_affect = mysql_affected_rows();
			if($num_affect>0)
			{
			
			$q = "   UPDATE ".$wbasedato."_000038 "
					."  SET Ciemmay = '".$muerteMayor."',"
					."      Ciemmen = '".$muerteMenor."',"
					."      Cieeal = '".$egresosAlta."',"
					."      Cieegr = '".$cant_egresos."',"
					."      Cieocu = '".$cant_camas_ocupadas."',"
					."      Ciedis = '".$cant_camas_disponibles."'"
					." WHERE Fecha_data = '".$wfecha."'"
					."  AND Cieser = '".$wcco."'"
					." LIMIT 1 ";
			
				$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());            
				
				//Guardo LOG de borrado en tabla movhos 33 - Activacion paciente
				$q = "    INSERT INTO log_agenda 
										  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros) 
								   VALUES
										  ('".date('Y-m-d')."', '".date('H:i:s')."', '".$whis."', '".$wing."', 'Borrado tabla movhos_000033', '".$seguridad."', '".$bandera."', '".$registrosFila."')";
				$resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			}                
		}    
	}
}

/****************************************************************************************
 * Agregada en 2013-02-25, Al cancelar un alta, se organizan los indicadores como estaban
 *2013-08-23 Se agregan 3 parametros: historia-ingreso-habitacion para actualizar movhos_000067
 ****************************************************************************************/
function cancelar_alta_indicadores( $conex, $wbasedato, $wcco, $wfecha, $whis, $wing, $whabcod ){
	
	
	$q = " SELECT * "
		    ."   FROM ".$wbasedato."_000038 "
			."  WHERE Fecha_data = '".$wfecha."'"
			."    AND Cieser = '".$wcco."'";
		
		$res = mysql_query($q,$conex);
        $num = mysql_num_rows($res);
		if( $num == 0 ){
			return;
		}
		$row = mysql_fetch_assoc($res);
		
		$egresosAlta = $row['Cieeal'];
		$camasDesocupadas = $row['Ciedis'];
		$camasOcupadas = $row['Cieocu'];
		$egresos = $row['Cieegr'];
		
		$egresosAlta--;
		$camasDesocupadas--;
		$camasOcupadas++;
		$egresos--;
		
		$q = " UPDATE ".$wbasedato."_000038 "
		    ."    SET Cieeal = '".$egresosAlta."',"
			."        Cieocu = '".$camasOcupadas."',"
			."        Ciedis = '".$camasDesocupadas."',"
			."        Cieegr = '".$egresos."'"
			."  WHERE Fecha_data = '".$wfecha."'"
			."    AND Cieser = '".$wcco."'"
			."  LIMIT 1 ";
		
		$res = mysql_query($q,$conex);
		
		 //2013-08-23: se actualiza la tabla movhos_000067 para poner el paciente en la habitacion donde estaba
		$qupdate = "UPDATE ".$wbasedato."_000067 "
				." 	   SET Habhis = '".$whis."', "
				."	  	   Habing = '".$wing."', "
				."		   Habdis = 'off', "
				."         Habali = 'off' "
				."   WHERE Fecha_data = '".$wfecha."'"
				."     AND Habcco = '".$wcco."'"
				."     AND Habcod = '".$whabcod."'"
				."  LIMIT 1 ";
		$res = mysql_query($qupdate,$conex);
	
}

/****************************************************************************************
 * Agregada en 2013-02-12, Al cancelar una muerte, se organizan los indicadores como estaban
 *2013-08-23: se adiciona el parametro whabcod para modificar la tabla movhos_000067
 ****************************************************************************************/
function cancelar_muerte_indicadores( $conex, $wbasedato, $wcco, $wfecha, $whis, $wing, $whabcod='' ){
	
	$q = " SELECT * "
		    ."   FROM ".$wbasedato."_000038 "
			."  WHERE Fecha_data = '".$wfecha."'"
			."    AND Cieser = '".$wcco."'";
		
		$res = mysql_query($q,$conex);
        $num = mysql_num_rows($res);
		if( $num == 0 ){
			return;
		}
		$row = mysql_fetch_assoc($res);
		
		$muertesMayor = $row['Ciemmay'];
		$muertesMenor = $row['Ciemmen'];
		$camasDesocupadas = $row['Ciedis'];
		$camasOcupadas = $row['Cieocu'];
		$egresos = $row['Cieegr'];
		
		//CALCULAR SI LA MUERTE ES MAYOR O MENOR DE 48 DESDE LA FECHA DE INGRESO A LA CLINICA
		$query = "	SELECT 	ROUND(TIMESTAMPDIFF(MINUTE,(CONCAT ( Fecha_data, ' ', Hora_data )), '{$wfecha}' )/(24*60),2) AS diferencia  "
				 ."	  FROM 	".$wbasedato."_000016  "
				 ."  WHERE	Inghis = '".$whis."'"
				 ."    AND	Inging = '".$wing."'";								 
		$reso = mysql_query($query, $conex);
		$numo = mysql_num_rows($reso);
		$diferencia = 0;
		if($numo > 0){
			$rowo = mysql_fetch_assoc($reso);
			$diferencia = $rowo['diferencia'];
		}
		if ($diferencia>=2)
			( $muertesMayor > 0 ) ? $muertesMayor-- : $muertesMenor--;			
		else 
			( $muertesMenor > 0 ) ? $muertesMenor-- : $muertesMayor--;			

		$camasDesocupadas--;
		$camasOcupadas++;
		$egresos--;
		
		$q = " UPDATE ".$wbasedato."_000038 "
		    ."    SET Ciemmay = '".$muertesMayor."',"
			."        Ciemmen = '".$muertesMenor."',"
			."        Cieocu = '".$camasOcupadas."',"
			."        Ciedis = '".$camasDesocupadas."',"
			."        Cieegr = '".$egresos."'"
			."  WHERE Fecha_data = '".$wfecha."'"
			."    AND Cieser = '".$wcco."'"
			."  LIMIT 1 ";
		$res = mysql_query($q,$conex);
		
		
		 //2013-08-23: se actualiza la tabla movhos_000067 para poner el paciente en la habitacion donde estaba
		$qupdate = "UPDATE ".$wbasedato."_000067 "
				." 	   SET Habhis = '".$whis."', "
				."	  	   Habing = '".$wing."', "
				."		   Habdis = 'off', "
				."         Habali = 'off' "
				."   WHERE Fecha_data = '".$wfecha."'"
				."     AND Habcco = '".$wcco."'"
				."     AND Habcod = '".$whabcod."'"
				."  LIMIT 1 ";
		$res = mysql_query($qupdate,$conex);
		
}


/********************************************************************************************************************************************
 * PROCESO CANCELACION DE ALTA
 ********************************************************************************************************************************************

 - Activar el paciente en movhos_000018, cancelando el alta en proceso y alta definitiva
 - Cancelar el egreso en movhos_000033
 - Activar la habitacion y dejar el paciente que esta de alta en ella
 - Si hay alguien asignado en la cama que ocupaba el paciente no dejar cancelar el alta
 - Solo se puede cancelar el alta hasta dos horas maximo de haber egresado
********************************************************************************************************************************************/
function cancelarAltaDefinitiva( $conex, $wbasedato, $wemp_pmla, $historia, $ingreso ){

	global $wusuario;
	global $wcco;

	//Si entra a esta funcion es por que estaba en la lista de pacientes egresados en menos de 2 horas
	//Se verifica que el paciente tenga la ultima habitacion disponible
	
	
	//Busco los datos de ubicacion del paciente
	$sql = "SELECT *
			FROM
				{$wbasedato}_000018
			WHERE
				ubihis = '$historia'
				AND ubiing = '$ingreso'
				AND ubiald = 'on'
			";
			
	$resUbicacion = mysql_query( $sql , $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numUbicacion = mysql_num_rows( $resUbicacion );
	
	if( $numUbicacion > 0 ){
	
		$rowsUbicacion = mysql_fetch_array( $resUbicacion );
	
		//Consulto si la habitacion este disponible
		//Si esta de alta la habitacion no puede ser ocupada por el paciente
		if( $rowsUbicacion[ 'Ubimue' ] == 'on' ){	//Si tiene alta por muerte
			$sql = "SELECT *
					FROM
						{$wbasedato}_000020
					WHERE
						habcco = '{$rowsUbicacion[ 'Ubisac' ]}'
						AND habcod = '{$rowsUbicacion[ 'Ubihan' ]}'
						AND habhis = ''
						AND habest = 'on'
					";
		}
		else{
			$sql = "SELECT *
					FROM
						{$wbasedato}_000020
					WHERE
						habcco = '{$rowsUbicacion[ 'Ubisac' ]}'
						AND habcod = '{$rowsUbicacion[ 'Ubihac' ]}'
						AND habhis = ''
						AND habest = 'on'
					";
		}
				
		$resHabitacion = mysql_query( $sql , $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$numHabitacion = mysql_num_rows( $resHabitacion );
		
		if( $numHabitacion > 0 ){	//Si la habitacion esta disponbile
		
			$rowsHabitacion = mysql_fetch_array( $resHabitacion );
			
			//Procedo a borrar el registro de cancelacion de caja
			$sql = "DELETE FROM {$wbasedato}_000022 
					 WHERE Cuehis  = '$historia'
					   AND Cueing  = '$ingreso'
					 LIMIT 1
					";
				
			$resCuentasCaja = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			
			if( mysql_affected_rows() > 0 ){
			
				//Se procede a cancelar el alta
				//quito los parametros de alta en la tabla ubicacion de pacientes (movhos_000018)
				if( $rowsUbicacion[ 'Ubimue' ] == 'on' ){
					$sql = "UPDATE {$wbasedato}_000018
							SET
								ubialp = 'off',
								ubifap = '0000-00-00',
								ubihap = '00:00:00',
								ubiald = 'off',
								ubifad = '0000-00-00',
								ubihad = '00:00:00',
								ubimue = 'off',
								ubihac = ubihan,
								ubisan = ''
							WHERE
								id = '{$rowsUbicacion[ 'id' ]}';
							";
				}else{
					$sql = "UPDATE {$wbasedato}_000018
							SET
								ubialp = 'off',
								ubifap = '0000-00-00',
								ubihap = '00:00:00',
								ubiald = 'off',
								ubifad = '0000-00-00',
								ubihad = '00:00:00'
							WHERE
								id = '{$rowsUbicacion[ 'id' ]}';
							";
				}			
				$resActUbi = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );	//Actualizando habitación
				
				if( mysql_affected_rows() > 0 ){
				
					//Procedo a cancelar el egreso
					//Eso es eliminando el registro
					$sql = "DELETE FROM
								{$wbasedato}_000033
							WHERE
								Historia_clinica = '$historia'
								AND Num_ingreso = '$ingreso'
								AND Tipo_egre_serv = 'ALTA'
							LIMIT 10
							";
					
					$resDelAlta = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
				
					if( mysql_affected_rows() > 0 ){
					
						//Se restaura la tabla de indicadores al cancelar un alta
						cancelar_alta_indicadores( $conex, $wbasedato, $wcco, $rowsUbicacion[ 'Ubifap' ], $historia, $ingreso, $rowsUbicacion[ 'Ubihac' ] );  //2013-02-12
						//2013-08-23 se agregan los parametros: historia,ingreso,habitacion para actualizar movhos_000067 si es necesario
					
						//Asigno la habitación nuavemente al paciente
						$sql = "UPDATE
									{$wbasedato}_000020
								SET
									habhis = '$historia',
									habing = '$ingreso',
									habdis = 'off',
									habali = 'off'
								WHERE
									id = '{$rowsHabitacion['id']}'
								";
								
						$resActHab = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );					
						
						if( mysql_affected_rows() > 0 ){
						
							/************************************************************************************************
							 * Noviembre 07 de 2012
							 *
							 * Intento eliminar orden de habitación si la tiene, debe estar solo una vez
							 ************************************************************************************************/
							$sqlDelelete = "DELETE FROM {$wbasedato}_000025
											 WHERE movhdi = '00:00:00' 
											   AND movhab = '{$rowsHabitacion['Habcod']}' 
											 LIMIT 1";
									   
							$resDelete = mysql_query( $sqlDelelete, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );	
							/************************************************************************************************/
						
							//Cancelo pedido de dietas
							cancelar_pedido_alimentacion( $historia, $ingreso,$wcco,"Habilitar", $wusuario );
						
							//Si se elimino el alta procedo a dejar el kardex con la cantidad necesaria a aplicar
							
							//Consulto los medicamentos del kardex para el día actual
							$sql = "SELECT *
									FROM
										{$wbasedato}_000054
									WHERE
										kadhis = '$historia'
										AND kading = '$ingreso'
										AND kadfec = '{$rowsUbicacion[ 'Ubifap' ]}'
										AND kadest != 'off'
										AND kadess != 'on'
									";
									
							$sql = "SELECT *
									FROM
										{$wbasedato}_000054
									WHERE
										kadhis = '$historia'
										AND kading = '$ingreso'
										AND kadfec = '".date( "Y-m-d" )."'
										AND kadest != 'off'
										AND kadess != 'on'
									";
									
							$resKardex = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
							$numKardex = mysql_num_rows( $resKardex );
							
							if( $numKardex == 0 ){
							
								$sql = "SELECT *
										FROM
											{$wbasedato}_000054
										WHERE
											kadhis = '$historia'
											AND kading = '$ingreso'
											AND kadfec = '".date( "Y-m-d", time() - 24*3600 )."'
											AND kadest != 'off'
											AND kadess != 'on'
										";
									
								$resKardex = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
								$numKardex = mysql_num_rows( $resKardex );
							}
							
							if( $numKardex > 0 ){
							
								//Busco la hora par de alta
								$horaParAlta = floor( date( "H" )/2 )*2*3600;
								$horaAlta = gmdate( "H:i:s", $horaParAlta );
								
								$fechorCancelacionAlta = strtotime( date( "Y-m-d 00:00:00" ) )+$horaParAlta;
								
								$horaCorteDispensacion = consultarAliasPorAplicacion( $conex, $wemp_pmla, "horaCorteDispensacion" );
								
								for( ; $rowsKardex = mysql_fetch_array( $resKardex ); ){
									
									$rowsKardex[ 'Kadcpx' ] = trim( $rowsKardex[ 'Kadcpx' ] );
									
									if( true ){
										
										// Hallar la de terminacion del medicamento
										// Hay tres posibilidades
										// - Hasta las 18 del día siguiente
										// - Por dosis maximas
										// - Por días de tratamiento
										//
										// Buscar la hora de inicio del medicamento para el día actual
										// Teniendo la hora de terminación y la cantidad de aplicaciones (Kadcdi) se puede hallar la hora de inicio para el día actual
										// teniendo en cuenta la fecha y hora de inicio del medicamento (kadfin y kadhin)
										// Contando la cantidad de aplicaciones que hay desde la hora de alta hasta hasta la hora de terminacion del medicamento se puede saber cuantos articulos 
										// se requiere para cubrir el suministro del articulo
										// Por ultimo, se debe restar el total de articulos a la cantidad dispensada, si dicha resta es negativa, se debe aumentar el saldo de dispensacion
										// del día anterior y dejar cantidad dispensada en 0
										// Hay que recalcular la cantidad de saldo del articulo en caso de ser requerido
										//
										// Esto se hace teniendo en cuenta de que siempre se dispensa hasta el día siguiente a la hora de corte
										
										
										/****************************************************************************************
										 * Buscando hora de terminacion del articulo
										 * Consulto cuantas aplicaciones hay hasta el día siguiente desde la fecha de inicio del 
										 * medicamento hasta la hora de corte del día siguiente
										 ****************************************************************************************/
										
										//frecuencia en segundos
										$frecuencia = consultarFrecuencia( $conex, $wbasedato, $rowsKardex[ 'Kadper' ] )*3600;
										
										//Fecha y hora de inicio del medicamento en formato Unix
										$fechorInicio = strtotime( $rowsKardex[ 'Kadfin' ]." ".$rowsKardex[ 'Kadhin' ] );
										
										//Fecha y hora de corte para el medicamento
										$fechorFin = strtotime( $rowsKardex[ 'Kadfec' ]." $horaCorteDispensacion:00:00" ) + 24*3600;
										
										//Si tiene dosis maximas
										if( trim( $rowsKardex[ 'Kaddma'] ) != '' ){
											$fechorFin = min( $fechorFin, $fechorInicio+( trim( $rowsKardex[ 'Kaddma'] ) - 1 )*$frecuencia );
										}
										
										//Si tiene días maximos
										if( trim( $rowsKardex[ 'Kaddia'] ) != '' ){
																		  //									Calculo de fecha final del medicamento por dias de tratamiento
											$fechorFin = min( $fechorFin, $fechorInicio + floor( ( strtotime( $rowsKardex[ 'Kadfin' ] )+trim( $rowsKardex[ 'Kaddia'] )*24*3600 - $fechorInicio)/$frecuencia )*$frecuencia );
										}
										
										//la fecha final debe ser mayor a la hora de inicio
										if( $fechorInicio < $fechorFin ){
										
											//Esto calculo el total de aplicaciones que hay desde la fecha de inicio del medicamento
											//hasta la hora de finalizacion del medicamento
											$auxCan = floor( ( $fechorFin - $fechorInicio )/$frecuencia );
											
											//Por tanto, la fecha y hora final de terminacion del medicamento es
											//total de aplicaciones por frecuencia mas la fecha de inicio
											$fechorFin = $fechorInicio + $auxCan*$frecuencia;	//Hora de terminacion del medicamento
										}
										/****************************************************************************************/
										
										/*********************************************************************************************
										 * Busco la hora de inicio del medicamento despues de la fecha y hora de cancelacion del alta
										 *********************************************************************************************/
										
										//$fechorCancelacionAlta = strtotime( date( "Y-m-d 00:00:00" ) ) + floor( date( "H" )/2 )*2*3600;
										
										//Si la fecha y hora de inicio del medicamento es menor a la fecha y hora de alta
										//Se puede hallar la hora de inicio del medicamento despues de la cancelacion del alta
										if( $fechorInicio < $fechorCancelacionAlta ){
											
											//Cuento el total de aplicaciones que hay antes de la fecha y hora de alta + 1 y lo multiplico por la frecuencia
											//ESto para hallar la fecha y hora de inicio del medicamento despues del alta
											$fechorInicioAlta = $fechorInicio + ( ceil( ( $fechorCancelacionAlta - $fechorInicio )/$frecuencia ) )*$frecuencia;
										}
										else{
											$fechorInicioAlta = $fechorInicio;
										}
										/******************************************************************************************/
										
										/******************************************************************************************
										 * Cuento el total de aplicaciones que se requiere hasta la hora de terminación del medicamento
										 ******************************************************************************************/
										 
										//Si la fehca de inicio de alta es mayor a la fecha de terminacion del medicamento
										if( $fechorFin < $fechorInicioAlta ){
											$fechorInicioAlta = $fechorFin;
										}
										
										$aplicacionesNecesarias = 0;
										
										if( $fechorFin >= $fechorCancelacionAlta ){
											$aplicacionesNecesarias = ( $fechorFin - $fechorInicioAlta )/$frecuencia+1;
										}
										/******************************************************************************************/
										
										//Calculo total de articulos que se requieren dispensar hasta la hora de terminación del medicamento
										$totalADispensar = $rowsKardex[ 'Kadcfr' ]/$rowsKardex[ 'Kadcma' ]*$aplicacionesNecesarias;
										
										//Calculo el saldo del articulo
										$saldoArticulo = $totalADispensar - ceil( $totalADispensar );
										
										//Calculo el saldo de dispensacion del dia anterior para el articulo
										$saldoDispensacion = 0; //$rowsKardex[ 'Kadsad' ];	//Debe ser el mismo
										
										//Calculo en cuanto debe quedar el saldo de dispensación, asumiendo como si ya todo estuviera dispensado sin contar saldo del día anterior
										$dis = $rowsKardex[ 'Kadcdi' ] - $rowsKardex[ 'Kadsad' ] - ceil( $totalADispensar );
										
										//Si el saldo es negativo, significa que hay que aumentar el saldo de dispensación
										//y la cantidad dispensada es 0
										if( $dis < 0 ){
											$saldoDispensacion = $dis*(-1);
											$dis = 0;
										}
										
										/************************************************************************************************************
										 * Creando la regleta
										 ************************************************************************************************************/
										$horasAplicacion = "";
										
										$tieneCpx = tieneCpxPorHistoriaAltas( $conex, $wbasedato, $historia, $ingreso );
										
										$fechorUltimaRonda = 0;
										
										if( $tieneCpx ){
											$horasAplicacion = crearRegleta( $rowsKardex[ 'Kadfec' ], 4, $fechorInicio, $frecuencia, $rowsKardex[ 'Kadcfr' ]/$rowsKardex[ 'Kadcma' ], $fechorCancelacionAlta, $fechorUltimaRonda );
										}
										/************************************************************************************************************/
										
										if( $fechorUltimaRonda > 0 ){
											$fecUltFrond = date( "Y-m-d", $fechorUltimaRonda );
											$fecUltRon = date( "H:i:s", $fechorUltimaRonda );
										}
										else{
											$fecUltFrond = "0000-00-00";
											$fecUltRon = "00:00:00";
										}
										
										//Por ultimo actualizo los campos del kardex
										//Dejando la regleta vacia
										$sql = "UPDATE
													{$wbasedato}_000054
												SET	
													kadcdi = kadcdi - kadsad + $saldoDispensacion,
													kaddis = '$dis',
													kadsal = '$saldoArticulo',
													kadsad = '$saldoDispensacion',
													kadcpx = '$horasAplicacion',
													kadfro = '$fecUltFrond',
													kadron = '$fecUltRon'
												WHERE
													id = '{$rowsKardex[ 'id' ]}'
												";
										
										$ressActKardex = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
									}
								}
							}
						}
						else{
							// echo "<b>No se puedo asignar la habitación</b>";
						}
					}
					else{
						// echo "<b>No se pudo anular el alta</b>";
					}
				}
				else{
					// echo "<b>No cancelar el alta en ubicacion del paciente</b>";
				}	
			}
			else{				
				//echo "<b>No se pudo borrar la cancelacion de caja</b>";
			}
		}
		else{
			// echo "<b>No se puede cancelar alta hasta que este disponible la habiación</b>";
			?>
			<script>
				alert( "No se puede cancelar alta hasta que este disponible la habiación" );
			</script>
			<?php
		}
	}
	else{	//El paciente no esta de alta
		echo "<b>El paciente no se encuentra de alta</b>";
	}
}


	               
  $wfecha=date("Y-m-d");   
  $whora = (string)date("H:i:s");	                                                           
	                                                                                                       
  $q = " SELECT Empdes "
      ."   FROM root_000050 "
      ."  WHERE Empcod = '".$wemp_pmla."'"
      ."    AND Empest = 'on' ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $row = mysql_fetch_array($res); 
  
  $wnominst=$row[0];
  
  //Traigo TODAS las aplicaciones de la empresa, con su respectivo nombre de Base de Datos    
    $wtabcco = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tabcco');
    $wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
    $wcencam = consultarAliasPorAplicacion($conex, $wemp_pmla, 'camilleros');
	$wafinidad = consultarAliasPorAplicacion($conex, $wemp_pmla, 'afinidad');
	$wcenmez = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cenmez');   
  
  encabezado("REGISTRO DE ALTAS",$wactualiz, "clinica");
  
  	 //Mensaje de espera
	echo "<div id='msjEspere' style='display:none;'>";
	echo '<br>';
	echo "<img src='../../images/medical/ajax-loader5.gif'/>";
	echo "<br><br> Por favor espere un momento ... <br><br>";
	echo '</div>';
	
    echo "<script>";
  echo "$.blockUI({ message: $('#msjEspere') });";
  echo "</script>";
       
  //FORMA ================================================================
  echo "<form name='altas' action='Registro_de_Altas.php' method=post>";
  
  echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
  echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
  echo "<input type='HIDDEN' name='wcencam' value='".$wcencam."'>";
  
  
  echo "<center><table>";
  
        
  if (strpos($user,"-") > 0)
     $wusuario = substr($user,(strpos($user,"-")+1),strlen($user)); 

  $q = " SELECT Empdes, Empmsa "
      ."   FROM root_000050 "
      ."  WHERE Empcod = '".$wemp_pmla."'"
      ."    AND Empest = 'on' ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $row = mysql_fetch_array($res); 
  
  $wnominst=$row[0];
  $wmeta_sist_altas=$row[1];  //Esta es la meta en tiempo promedio para las altas   
     
     
  function calcular_tiempo_del_alta($whis,$wing)
    {
	 global $wmeta_sist_altas;
	 
	 $q = " SELECT TIMEDIFF(ubihad,ubihap) "
         ."   FROM ".$wbasedato."_000018 "
         ."  WHERE ubihis  = '".$whis."'"
         ."    AND ubiing  = '".$wing."'"
         ."    AND ubiald != 'on' ";     
	 $row = mysql_fetch_array($res);
	 
	 if ($row[0] > ($wmeta_sist_altas+1))
	    return true; 
	    
    }       
     
  //=====================================================================================================================================================================
  
  function requiere_justificacion($widreg)
    {
		global $wbasedato;
		global $conex;
		global $wemp_pmla;
		
	    //acá traemos la hora de alta en proceso.
		$q = "SELECT Ubifap, Ubihap, Ubisac"
			."  FROM ".$wbasedato."_000018"
			." WHERE id = '".$widreg."'";
			
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$row = mysql_fetch_array($res);
		
		//verificamos si el cco necesita promediación de alta
		$q2 = "SELECT Ccopal"
			."  FROM ".$wbasedato."_000011"
			." WHERE Ccocod = '".$row[2]."'";
		
		$res2 = mysql_query($q2,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q2." - ".mysql_error());
		$row2 = mysql_fetch_array($res2);
		
		if($row2[0]=="off")
			return false;
		
		if(($row[0]=="0000-00-00")or($row[1]=="00:00:00"))
			return false;
			
		$htiap = strtotime($row[0]." ".$row[1]); //tiempo alta en proceso en segundos por UNIX
		
		//acá tengo que consultar la meta de la empresa.
		
		$q = "SELECT Empmsa "
			."  FROM root_000050 "
			." WHERE Empcod = '".$wemp_pmla."'";
		
		$res = $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$row = mysql_fetch_array($res);
		
		
		$segholgura = explode(":", $row[0]);
		$segholgura = ((integer)$segholgura[0]*60*60) + ((integer)$segholgura[1]*60) + ((integer)$segholgura[2]);
		
		
		//acá se consulta el porcentaje de holgura en la meta de la empresa
		$q = "SELECT Detval"
			."  FROM root_000051 "
			." WHERE Detemp = '".$wemp_pmla."'"
			."   AND Detapl = 'HolguraMetaAltas'";
			
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$row = mysql_fetch_array($res);
		
		
		//convertimos a un valor decimal la holgura en la meta de la empresa.
		$aux = explode("%",$row[0]);
		$wholgura = (integer)$aux[0];
		$wholgura = $wholgura/100; //ya está como decimal.
		$holgtotal = $segholgura + ($segholgura*$wholgura); // tiempo máximo esperado para dar el alta definitiva
		
		
		$horaADE = $htiap + $holgtotal; //hora alta definitiva esperada
		
		
		
		
		//aca vamos a hacer la resta con la hora actual.
		//$rs = $horaADE - time(); //calculamos la diferencia entre la hora esperada de salida y la hora en la que se efectua la salida Definitiva
		
		if(time()<$horaADE)
		{
			return false;
		}
		return true;
		

    }
 
  function generar_menu_justificaciones()
	{
		global $conex;
		$wmovhos = consultarAliasPorAplicacion($conex, "01", "movhos");
		$query = "SELECT Juscod, Jusdes
					FROM `".$wmovhos."_000023`
				   WHERE Justip = 'R'"
                   ."AND Jusest = 'on'";
		$rs = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
		echo "<center><table border=0>";
		echo "<tr><td class='fila2' align=center colspan=4><b>CAUSA DEMORA EN EL ALTA</b></td></tr>";
		echo "<tr>";
		echo "<td class='fila2' align=center>";
		echo "<select name='mjust' id='mjust'>";
		 echo"<option value = '--' selected>--</option>";
		 $num = mysql_num_rows($rs);
		 for($i = 0; $i <$num; $i++)
			{
			$row = mysql_fetch_array($rs);
			echo "<option value ='".$row[0]."'>".$row[0]." - ".$row[1]."</option>";
			}
		echo "</select></td></tr>";
		echo "</table>";
	} 
	

	
	
	
	
	
  //===============================================================================================================================================
  //ACA COMIENZA EL MAIN DEL PROGRAMA   
  //===============================================================================================================================================
  
  //Esto solo existe si se va a cancelar un alta
  if( isset( $inCancelarAltaDef ) && trim( $inCancelarAltaDef ) != '' ){

	$puedeCancelarAltaDef = validarUsuarioCancelarAlta( $conex, $wemp_pmla, $wusuario, $inClaveCancelarAlta );
  
	if( $puedeCancelarAltaDef ){
		list( $his, $ing ) =  explode( "-", $inCancelarAltaDef );
		cancelarAltaDefinitiva( $conex, $wbasedato, $wemp_pmla, $his, $ing );
	}
	else{
		?>
		<script>
			alert( "No tiene permiso para cancelar alta definitiva." );
		</script>
		<?php
	}
  }
  
  
   if (!isset($wcco) or trim($wcco) == "" )
     {
	  echo '<span class="subtituloPagina2">INTRODUZCA EL CENTRO DE COSTO:</span>';
	     
      ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      //ACA TRAIGO LOS DESTINOS DIGITADOS EN LA TABLA DE MATRIX
      $q = " SELECT ".$wtabcco.".Ccocod, ".$wtabcco.".Cconom "
          ."   FROM ".$wtabcco.", ".$wbasedato."_000011"
          ."  WHERE ".$wtabcco.".Ccocod = ".$wbasedato."_000011.Ccocod"
		  ."	AND ".$wbasedato."_000011.Ccohos = 'on'";
      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $num = mysql_num_rows($res);
			 	 
	    
	  if ($wusuario == "03150")
	     {
		  echo "<tr><td align=center><select name='wcco'>";
		  echo "<option>&nbsp</option>";    
		  for ($i=1;$i<=$num;$i++)
		     {
		      $row = mysql_fetch_array($res); 
		      echo "<option>".$row[0]." - ".$row[1]."</option>";
	         }
	      echo "</select></td></tr>";
         }
        else
          {
	       ?>	    
			 <script>
			     function ira(){document.altas.wcco.focus();}
			 </script>
		   <?php    
           echo "<td bgcolor=00CCFF align=center><INPUT TYPE='password' NAME='wcco' SIZE=7 id=section></td>"; 
          } 
	    
      echo "</table>";
      
      echo "<table>";    
	  echo "<center><tr class=boton><td align=center></b><input type='submit' value='ENTRAR'></b></td></tr></center>";
	  echo "</table>";
     }
    else 
      { 
	   if (strpos($wcco,"-") > 0)
	      { 
	       $wccosto=explode("-",$wcco);
	       $wcco=$wccosto[0];
          }
         else
           {
            if (strpos($wcco,".") > 0)  
	   	       { 
		        $wccosto=explode(".",$wcco);
		        $wcco=$wccosto[1];
	           }
	       }	      
	       
	       
	   $q = " SELECT Cconom "
	       ."   FROM ".$wtabcco
	       ."  WHERE Ccocod = '".$wcco."'";
	   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	   $row = mysql_fetch_array($res);
	   $wnomcco=$row[0];   
	      
	   if (trim($wnomcco)=="")
	      {
           ?>	    
	         <script>
		       alert ("EL CENTRO DE COSTO NO FUE INGRESADO POR CODIGO DE BARRAS");     
		     </script>
		   <?php 
		  }        
          
	      
	   if (!isset($wccohos))
		 {	  
		  //Traigo el INDICADOR de si el centro de costo es hospitalario o No   
		  $q = " SELECT Ccohos, Ccoapl "
		      ."   FROM ".$wbasedato."_000011 "
		      ."  WHERE Ccocod = '".$wcco."'";
		  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		  $num = mysql_num_rows($res); 
		  if ($num > 0)
		     { 
		      $row = mysql_fetch_array($res);
		      if ($row[0]=="on")
		         {
		          $wccohos="on";
		          $wccoapl=$row[1];
	             } 
		        else
		           {
		            $wccohos="off";
		            $wccoapl=$row[1];
	               } 
		     }
		    else
		      {
		       $wccohos="off";        
		       $wccoapl="off";
	          } 
	          
	          //================================================================================================================================
	          //Doy de alta a todas las historias que esten en URGENCIAS desde hace 10 días para atras, 
	          //y que no esten de paso (osea que no vengan de otro servicio ubisan='' o ubisan='NO APLICA'
	          //================================================================================================================================
			  // Agosto 1 de 2013
			  // Se comenta porque este proceso de alta ya lo hace el cron hce/procesos/pacientes_unix_matrix.php
			  // $q = " UPDATE ".$wbasedato."_000018 A, ".$wbasedato."_000011 B"
		          // ."    SET Ubiald        = 'on' "
		          // ."  WHERE Ubisac        = Ccocod "
		          // ."    AND Ccourg        = 'on' "
		          // ."    AND A.Fecha_data <= str_to_date(ADDDATE('".$wfecha."',-10),'%Y-%m-%d') "  //Fecha_data inferior a hace 10 días para atras
		          // ."    AND Ubiald       != 'on' "
		          // ."    AND Ubiptr       != 'on' "
		          // ."    AND (Ubisan       = '' "
		          // ."     OR  Ubisan       = 'NO APLICA') ";  //Que actualice los que hallan estado en otro servicio, porque se supone que estan de paso en Urgencias
		      // $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		      //================================================================================================================================
		      
		      
		      
		      $q = " SELECT Ubihac, Ubihis, Ubiing, Pacno1, Pacno2, Pacap1, Pacap2, Ubialp, Ubisan, ".$wbasedato."_000018.id, Ubiprg "
			      ."   FROM root_000036, root_000037, ".$wbasedato."_000018 "
			      ."  WHERE Ubisac  = '".$wcco."'"       //Servicio Actual
			      ."    AND Ubihis  = Orihis "
			      ."    AND Ubiing  = Oriing "
			      ."    AND Oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia, 
			      ."    AND Oriced  = Pacced "
			      ."    AND Oritid  = Pactid "
			      ."    AND Ubiptr != 'on' "             //Solo los pacientes que no esten siendo trasladados
			      ."    AND Ubiald != 'on' "             //Que no este en Alta Definitiva
			      ."  GROUP BY 1,2,3,4,5,6,7,8,9,10 ";
			  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			  $num = mysql_num_rows($res);   
			     
			  if ($num >= 1)
			     {
				  for ($i=1;$i<=$num;$i++)
			         {
				      $row = mysql_fetch_array($res);   
				      
				      if ((isset($whabprg[$i]) and $wid[$i] == $row[9] and trim($whabprg[$i])!="" and strlen(trim($whabprg[$i]))>0) or (isset($whabprg[$i]) and $row[10]!="" and trim($whabprg[$i])!=""))           
			            {
				         //=======================================================================================================   
				         //Si seleccionaron una habitacion o una posible alta   
				         //=======================================================================================================
				         $q = " UPDATE ".$wbasedato."_000018 "
				             ."    SET Ubiprg  = '".$whabprg[$i]."'"
				             ."  WHERE Ubihis  = '".$row[1]."'"
				             ."    AND Ubiing  = '".$row[2]."'"
				             ."    AND id      = ".$row[9]
				             ."    AND Ubialp != 'on' ";
				         $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error()); 
				         
				         
				         //=======================================================================================================   
				         //Luego actualizo la habitación destino como programada si no esta disponible   
				         //=======================================================================================================
				         if (strlen(trim($whabprg[$i])) > 0)
				            {
					         $q = " UPDATE ".$wbasedato."_000020 "
					             ."    SET Habprg  = 'on'"
					             ."  WHERE Habcod  = '".$whabprg[$i]."'"
					             ."    AND Habdis != 'on' ";
					         $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				            } 
				        }
				      
				      //==========================================================================================================================================================    
				      //==========================================================================================================================================================    
			          //Si seleccionaron *** ALTA EN PROCESO ***
			          //==========================================================================================================================================================
			          if (isset($wproceso[$i]) and $wid[$i] == $row[9])           
			            {
						
				         //Primera vez que colocan esta historia en proceso de alta   
				         $q = " UPDATE ".$wbasedato."_000018 "
				             ."    SET Ubialp  = 'on', "
				             ."        Ubifap  = '".$wfecha."',"
				             ."        Ubihap  = '".$whora."'"
				             ."  WHERE Ubihis  = '".$row[1]."'"
				             ."    AND Ubiing  = '".$row[2]."'"
				             ."    AND Ubiald != 'on' " 
				             ."    AND Ubiptr != 'on' "
				             ."    AND Ubialp  = 'off' "
				             ."    AND id      = ".$row[9];
				         $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());  
				         
				         unset($wproceso[$i]);
						 
						 /************************************************************************************************************************
						  * Octubre 08 de 2012
						  *
						  * Si se da muerte procedo a cancelar todas las aplicaciones posteriores a la ronda en que se da la muerte
						  ************************************************************************************************************************/
						 //Consulto la hora de corte de dispensacion
						 $horaCorteDispensacion = consultarAliasPorAplicacion( $conex, $wemp_pmla, "horaCorteDispensacion" );
						  
						 //consulta la ronda actual
						 $rondaSiguiente = gmdate( "H:i:s", ( floor( date( "H" )/2 )*2 )*3600 );
						 $fechorRondaSiguiente = strtotime( date( "Y-m-d" )." ".$rondaSiguiente ) + 2*3600;	//Suma dos horas a la ronda actual
						 
						 //Consulto la ronda de corte de dispensacion
						 $fechorCorte = strtotime( date( "Y-m-d" )." $horaCorteDispensacion:00:00" ) + 24*3600;
						 
						 //Cancelo las aplicaciones realizadas entre la siguiente ronda en que se le da muerte al paciente hasta la hora de corte de dispensacion
						 cancelarAplicacionesPorRangoInc( $conex, $wbasedato, $row[1], $row[2], $fechorRondaSiguiente, $fechorCorte );
						 /************************************************************************************************************************/
				        }
				        
				        
				      //==========================================================================================================================================================    
					  $activarAplicacionesCanceladas = false;	//banderaque indica si se puede activar las aplicaciones canceladas, solo se hace si se cancela muerte o alta en proceso
					  
					  
				      //==========================================================================================================================================================    
			          //Si seleccionaron *** CANCELAR ALTA ***
			          //==========================================================================================================================================================  
					  $fechorAltaEnProceso = 0;	//Indica la fecha y hora en formato unix en que se hizo el proceso de alta
					  $fechorMuerte = 0;		//Indica la fehca y hora en formato unix en que se dió muerte al paciente
					  $fechaMuerte = "";        //INdica la fecha en que se dió muerte al paciente  2013-02-12
					  
				      if (isset($wcanalta[$i]) and $wid[$i] == $row[9])           
						{
						 /********************************************************************************
						  * Octubre 08 de 2012
						  *
						  * Consulto la hora de alta en proceso
						  ********************************************************************************/
						  $q = "SELECT * "
				             ."   FROM {$wbasedato}_000018 "
				             ."  WHERE Ubihis  = '".$row[1]."'"
				             ."    AND Ubiing  = '".$row[2]."'"
				             ."    AND Ubiptr != 'on' "
				             ."    AND id      = ".$row[9];
						 
						 $resFecHorAltProc = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());  
						 
						 if( $rowsFecHorAltProc = mysql_fetch_array( $resFecHorAltProc ) ){
							$fechorAltaEnProceso = strtotime( $rowsFecHorAltProc['Ubifap']." ".$rowsFecHorAltProc['Ubihap'] );	//fecha y hora de alta en proceso
						 }
						 /********************************************************************************/
						 
						 /********************************************************************************
						  * Octubre 08 de 2012
						  *
						  * Consulto la hora en que dieron muerte al paciente
						  ********************************************************************************/
						 $sql = "SELECT
									*
								 FROM
									{$wbasedato}_000033 
								 WHERE
									Historia_clinica  = '".$row[1]."'
									AND Num_ingreso		  = '".$row[2]."'
									AND Servicio         = '$wcco'
								 ORDER BY
									fecha_data desc, hora_data desc
								 ";
						 
						 $resUltMuerte = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
						 
						 if( $rowUltMuerte = mysql_fetch_array( $resUltMuerte ) ){
							$fechorMuerte = strtotime( $rowUltMuerte[ 'Fecha_data' ]." ".$rowUltMuerte[ 'Hora_data' ] );
							$fechaMuerte = $rowUltMuerte['Fecha_data']; // 2013-02-12
						 }
						 /********************************************************************************/
						
				         $q = " UPDATE ".$wbasedato."_000018 "
				             ."    SET Ubialp  = 'off', "
				             ."        Ubifap  = '0000-00-00',"
				             ."        Ubihap  = '00:00:00', "
				             ."        Ubifho  = '0000-00-00',"
				             ."        Ubihho  = '00:00:00', "
				             ."        Ubihot  = '', "
				             ."        Ubiuad  = '' "
				             ."  WHERE Ubihis  = '".$row[1]."'"
				             ."    AND Ubiing  = '".$row[2]."'"
				             ."    AND Ubiptr != 'on' "
				             ."    AND id      = ".$row[9];
						 $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());  
						 
						 
						 //Borro el registro de facturacion si ya habia chequeado que no tuviera devolucion pendiente
						 $q = " DELETE FROM ".$wbasedato."_000022 "
				             ."  WHERE Cuehis  = '".$row[1]."'"
				             ."    AND Cueing  = '".$row[2]."'";
				         $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error()); 
						 
						 unset($wcanalta[$i]);
						 
						 $activarAplicacionesCanceladas = true;
				        }  
				        
				        
				      //==========================================================================================================================================================    
				      //==========================================================================================================================================================    
			          //Si seleccionaron *** CANCELAR MUERTE ***
			          //==========================================================================================================================================================  
			          if (isset($wcanmuerte[$i]) and $wid[$i] == $row[9])           
			            {
						
						 /********************************************************************************
						  * Consulto la hora de alta en proceso por si se lo dieron antes de que la muerte
						  ********************************************************************************/
						  $q = "SELECT * "
				             ."   FROM {$wbasedato}_000018 "
				             ."  WHERE Ubihis  = '".$row[1]."'"
				             ."    AND Ubiing  = '".$row[2]."'"
				             ."    AND Ubiptr != 'on' "
				             ."    AND id      = ".$row[9];
						 
						 $resFecHorAltProc = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());  
						 
						 if( $rowsFecHorAltProc = mysql_fetch_array( $resFecHorAltProc ) ){
							$fechorAltaEnProceso = strtotime( $rowsFecHorAltProc['Ubifap']." ".$rowsFecHorAltProc['Ubihap'] );	//fecha y hora de alta en proceso
						 }
						 /********************************************************************************/
						 
						 /********************************************************************************
						  * Octubre 08 de 2012
						  *
						  * Consulto la hora en que dieron muerte al paciente
						  ********************************************************************************/
						 $sql = "SELECT
									*
								 FROM
									{$wbasedato}_000033 
								 WHERE
									Historia_clinica  = '".$row[1]."'
									AND Num_ingreso		  = '".$row[2]."'
									AND Servicio         = '$wcco'
								 ORDER BY
									fecha_data desc, hora_data desc
								 ";
						 
						 $resUltMuerte = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
						 
						 if( $rowUltMuerte = mysql_fetch_array( $resUltMuerte ) ){
							$fechorMuerte = strtotime( $rowUltMuerte[ 'Fecha_data' ]." ".$rowUltMuerte[ 'Hora_data' ] );
							$fechaMuerte = $rowUltMuerte['Fecha_data']; // 2013-02-12
						 }
						 /********************************************************************************/
						
				         //=======================================================================================================   
				         //Traigo la habitacion en la que estaba antes de marcar la muerte 
				         //=======================================================================================================
				         $q = " SELECT Ubihan "
				             ."   FROM ".$wbasedato."_000018 "
				             ."  WHERE Ubihis = '".$row[1]."'"
				             ."    AND Ubiing = '".$row[2]."'";
				         $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error()); 
				         $whabact = mysql_fetch_array($err);   
				         //=================================================================================================================================
				            
				         //=================================================================================================================================
				         // 
				         //=================================================================================================================================
				         $q = " SELECT MAX(Fecha_data), MAX(Hora_data), Eyrhor "
				             ."   FROM ".$wbasedato."_000017 "
				             ."  WHERE Eyrhis = '".$row[1]."'"
				             ."    AND Eyring = '".$row[2]."'"
				             ."    AND Eyrtip = 'Entrega' "
				             ."    AND Eyrsde = '".$wcco."'"
				             ."  GROUP BY 3 ";  
				         $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error()); 
				         $num = mysql_num_rows($err);
				         if ($num > 0)   //Si tiene un recibo anterior en el servicio 
				            {
					         $rowmue = mysql_fetch_array($err);    
					         $whabant=$rowmue[2];   
				            }       
				           else
				              $whabant=""; 
				         //=================================================================================================================================
				         
				             
				         //=================================================================================================================================   
				         //Actualizo en la 000018, para que queda en la habitacion en que estaba y coloque la habitacion anterior antes que fuera trasladado
				         //=================================================================================================================================     
				         $q = " UPDATE ".$wbasedato."_000018 "
				             ."    SET ubimue  = 'off', "
				             ."        ubihac  = ubihan, "
				             ."        ubihan  = '".$whabant."'"
				             ."  WHERE ubihis  = '".$row[1]."'"
				             ."    AND ubiing  = '".$row[2]."'"
				             //."    AND ubialp != 'on' "          //Que no este en proceso de alta, no se tiene en cuenta que el paciente este en proceso de traslado.
				             ."    AND ubiptr != 'on' "          //Que no este en proceso traslado
				             ."    AND id      = ".$row[9];
						 $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());  
						 //=================================================================================================================================
						 
						 
						 //================================================================================================================================= 
						 //Actualizo en la 000020, para que queda en la habitacion en que estaba     
						 //=================================================================================================================================
				         $q = " UPDATE ".$wbasedato."_000020 "
				             ."    SET habhis = '".$row[1]."', "
				             ."        habing = '".$row[2]."', "
				             ."        habdis = 'off', "
				             ."        habali = 'off' "
				             ."  WHERE habcod = '".$whabact[0]."'"
				             ."    AND habhis = '' "
				             ."    AND habing = '' ";
				         $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error()); 
						 //=================================================================================================================================
						 
						 
						 
						 //================================================================================================================================= 
						 //Borro el egreso de la 000033     
						 //=================================================================================================================================
				         $q = " DELETE FROM ".$wbasedato."_000033 "
				             ."  WHERE Historia_clinica = '".$row[1]."'"
				             ."    AND Num_ingreso      = '".$row[2]."'"
				             ."    AND Servicio         = '".$wcco."'";
				         $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error()); 
						 //=================================================================================================================================
						 
						 //Se restaura la tabla de indicadores al cancelar una muerte
						 cancelar_muerte_indicadores( $conex, $wbasedato, $wcco, $fechaMuerte, $row[1], $row[2], $whabact[0] );  //2013-02-12
						 //2013-08-23 se adiciona el parametro habcod para modificar la tabla movhos_000067
						 
						 
						 //habilito de nuevo el servicio de alimentacion si se puede todavia
						 cancelar_pedido_alimentacion($row[1],$row[2], $wcco, "Habilitar", $wusuario);            //Febrero 10 2010
						 
						 unset($wcanmuerte[$i]);
						 
						 $activarAplicacionesCanceladas = true;
				        }
						
						if( $activarAplicacionesCanceladas )
						{
						
							if( !($fechorAltaEnProceso > 0 && $fechorAltaEnProceso > 0 ) )
							{
							 /*********************************************************************************************
							  * Octubre 08 de 2012
							  *
							  * Procedo a activar las aplicaciones que hallan sido canceladas al momento de dar la muerte
							  *********************************************************************************************/
							 //Consulto la hora de corte de dispensacion
							 $horaCorteDispensacion = consultarAliasPorAplicacion( $conex, $wemp_pmla, "horaCorteDispensacion" );
							  
							 //consulta la ronda en que se realizó el alta en proceso o la muerte
							 //Puede que se de que den alta en proceso y muerte, por tanto hay que verificar cual de los dos se dió primero
							 $rondaSiguiente = gmdate( "H:i:s", ( floor( date( "H", max( $fechorAltaEnProceso, $fechorMuerte ) )/2 )*2 )*3600 );
							 
							 $fechorRondaSiguiente = strtotime( date( "Y-m-d" )." ".$rondaSiguiente ) + 2*3600;	//Suma dos horas a la ronda actual
							 
							 //Consulto la ronda de corte de dispensacion
							 $fechorCorte = strtotime( date( "Y-m-d" )." $horaCorteDispensacion:00:00" ) + 24*3600;
							  
							 activarAplicacionesPorRangoInc( $conex, $wbasedato, $row[1], $row[2], $fechorRondaSiguiente, $fechorCorte );
							 /*********************************************************************************************/
							}
						}
				        
				          
				      //==========================================================================================================================================================      
			          //==========================================================================================================================================================    
			          //Si seleccionaron *** ALTA DEFINITIVA ***
			          //==========================================================================================================================================================
			          if (isset($wdefinitiva[$i]) and $wid[$i] == $row[9])           
			            {
				          $continuar = false; //esta variable me va a controlar el proceso de cancelado de almimentación, limpieza de habitación etc...
					      $wreqjust = requiere_justificacion($wid[$i]);
				          
										if($wreqjust == false)//sino requiere justificación ejecuta el query sin la justificación
											{
												$q = " UPDATE ".$wbasedato."_000018 "
													."    SET Ubiald  = 'on', "
													."        Ubifad  = '".$wfecha."',"
													."        Ubihad  = '".$whora."', "
													."        Ubiuad  = '".$wusuario."' "
													."  WHERE Ubihis  = '".$row[1]."'"
													."    AND Ubiing  = '".$row[2]."'"
													."    AND Ubialp  = 'on' "
													."    AND Ubiald != 'on' " 
													."    AND Ubiptr != 'on' "
													."    AND id      = ".$row[9];
												$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
												
												$continuar = true;
											}else{
													if(isset($wjust))
													{
														$q = " UPDATE ".$wbasedato."_000018 "
															."    SET Ubiald  = 'on', "
															."        Ubifad  = '".$wfecha."',"
															."        Ubihad  = '".$whora."', "
															."        Ubiuad  = '".$wusuario."', "
															."		  Ubijus  = '".$wjust."'"
															."  WHERE Ubihis  = '".$row[1]."'"
															."    AND Ubiing  = '".$row[2]."'"
															."    AND Ubialp  = 'on' "
															."    AND Ubiald != 'on' " 
															."    AND Ubiptr != 'on' "
															."    AND id      = ".$row[9];
														$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
														$continuar = true;
													}
									
												 }
											
								if($continuar == true)
								{
									cancelar_pedido_alimentacion($row[1], $row[2], $wcco, "Cancelar", $wusuario);          //Febrero 10 2010									
									cancelarPedidoInsumos($conex, $wbasedato, $row[1], $row[2]); //Noviembre 1 de 2017 Jonatan
				         
									//Con este proceso si al desaparecer una linea la que sigue no estaba setiada, hago que siga dessetiada
									for ($j=$i+1;$j<=$num;$j++)
										{  
										if (!isset($wproceso[$j]))
											{
											if (isset($wproceso[$j-1])) unset($wproceso[$j-1]);
											}
										}       
									//=======================================================================================================================================================     
				         
					     
									$wfecha = date("Y-m-d");
									$whora = (string)date("H:i:s");
						 
									//==========================================================================================  
									//Ago 13 de 2010
									//Si se coloco habitacion, traigo el nombre del paciente
									if ($row[0] != "")
									{
										$whabpac=$row[0];
						       
										$q = " SELECT Pacno1, Pacno2, Pacap1, Pacap2 "
											."   FROM root_000036, root_000037, ".$wbasedato."_000020 "
											."  WHERE Habcod = '".$whabpac."'"
											."    AND Habhis = Orihis "
											."    AND Habing = Oriing "
											."    AND Oriori = '".$wemp_pmla."'"
											."    AND Oriced = Pacced "
											."    AND Oritid = Pactid ";
										$reshab = mysql_query($q,$conex);
										$rowhab = mysql_fetch_array($reshab);
						    
										$numhab = mysql_num_rows($reshab);
						    
										if ($numhab > 0)
										$whabpac="<b>".$whabpac."</b><br>Pac: ".$rowhab[0]." ".$rowhab[1]." ".$rowhab[2]." ".$rowhab[3];
									}    
									//==========================================================================================
						 
									//=======================================================================================================================================================
									//Actualizo o pongo en modo de limpieza la habitación en la que estaba el paciente  
									$q = " UPDATE ".$wbasedato."_000020 "
										."    SET Habali = 'on', "
										."        Habdis = 'off', "
										."        Habhis = '', "
										."        Habing = '', "
										."        Habfal = '".$wfecha."', "
										."        Habhal = '".$whora."', "
										."        Habprg = '".$row[10]."'"    //Aca va la misma habitacion, si fue programada
										."  WHERE Habcod = '".$row[0]."'"
										."    AND Habhis = '".$row[1]."'"
										."    AND Habing = '".$row[2]."'";
									$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
									//=======================================================================================================================================================   
							
				         
									//=======================================================================================================================================================
									//Calculo los días de estancia en el servicio actual
									$q=" SELECT ROUND(TIMESTAMPDIFF(MINUTE,CONCAT( Fecha_ing, ' ', Hora_data ),now())/(24*60),2), Num_ing_Serv "
									."   FROM ".$wbasedato."_000032 "
									."  WHERE Historia_clinica = '".$row[1]."'"
									."    AND Num_ingreso      = '".$row[2]."'"
									."    AND Servicio         = '".$wcco."'"
									."  GROUP BY 2 ";
									$err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
									$rowdia = mysql_fetch_array($err);
									$wdiastan=$rowdia[0];
									$wnuming=$rowdia[1];
					     
									if ($wdiastan=="" or $wdiastan==0)
										$wdiastan=0;
					        
									if ($wnuming=="" or $wnuming==0)
										$wnuming=1;   
					        
					     
									//BUSCO SI EL ALTA ES POR MUERTE O NO
									$q = " SELECT Ubimue "
										."   FROM ".$wbasedato."_000018 "
										."  WHERE Ubihis = '".$row[1]."'"
										."    AND Ubiing = '".$row[2]."'";
									$err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
									$rowmue = mysql_fetch_array($err);    
					        
									if ($rowmue[0]!="on"){
									
										BorrarAltasMuertesAntesDeAgregarNueva($conex, $wbasedato, $row[1], $row[2], 'Nueva alta');
										
										$wmotivo="ALTA"; 
										//Grabo el registro de egreso del paciente del servicio
										$q = " INSERT INTO ".$wbasedato."_000033 (   Medico       ,   Fecha_data,   Hora_data,   Historia_clinica,   Num_ingreso,   Servicio ,  Num_ing_Serv,   Fecha_Egre_Serv ,   Hora_egr_Serv ,    Tipo_Egre_Serv,  Dias_estan_Serv, Seguridad        ) "
													."                    VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$row[1]."'      ,'".$row[2]."' ,'".$wcco."' ,".$wnuming."  ,'".$wfecha."'      ,'".$whora."'     , '".$wmotivo."'   ,".$wdiastan."    , 'C-".$wusuario."')";   
										$err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
										
										
									}
									//=======================================================================================================================================================
						
			             
									//=======================================================================================================================================================
									//Pido el servicio de Camillero
									//Traigo el nombre del Origen de la tabla 000004 de la base de datos de camilleros con el centro de costos actual
									$q = " SELECT Nombre "
										."   FROM ".$wcencam."_000004 "
										."  WHERE mid(Cco,1,instr(Cco,'-')-1) = '".$wcco."'"
										."  GROUP BY 1 ";
									$err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
									$rowori = mysql_fetch_array($err);
									$worigen=$rowori[0];
						 
									//=======================================
									//Traigo el Tipo de Central
									$q = " SELECT Tip_central "
										."   FROM ".$wcencam."_000001 "
										."  WHERE Descripcion = 'PACIENTE DE ALTA'"
										."    AND Estado = 'on' ";
									$restce = mysql_query($q,$conex);
									$rowcen = mysql_fetch_array($restce);
									$wtipcen = $rowcen[0];   				 
					 
									//=======================================
					
									//=============================================================================
									//Traigo la Central asignada para el Centro de Costos según el Tipo de Central
									$q = " SELECT Rcccen "
										."   FROM ".$wcencam."_000009 "
										."  WHERE Rcccco = '".$wcco."'"
										."    AND Rcctic = '".$wtipcen."'";
									$rescen = mysql_query($q,$conex);
									$rowcen = mysql_fetch_array($rescen);
									$wcentral=$rowcen[0]; 
						
									if ($wcentral == FALSE)
										{
							
										$q = " SELECT Rcccen "
											."   FROM ".$wcencam."_000009 "
											."  WHERE Rcccco = '*'"
											."    AND Rcctic = '".$wtipcen."'";
										$rescen = mysql_query($q,$conex);
										$rowcen = mysql_fetch_array($rescen);
										$wcentral=$rowcen[0]; 
										}
									else
										{
										$wcentral=$wcentral;
										}
					 
								//$wcentral="CAMILLEROS";
									//=======================================================================================================================================================
						     
									if ($rowmue[0]!="on")  //No pide el camillero si el paciente Murio, porque se pidio cuando marco la muerte
										{
										//=======================================================================================================================================================
										//Grabo el registro solicitud del camillero
										$q = " INSERT INTO ".$wcencam."_000003 (   Medico     ,   Fecha_data,   Hora_data,   Origen     , Motivo           ,   Habitacion  , Observacion                                                                                                , Destino ,    Solicito    ,    Ccosto  , Camillero, Hora_respuesta, Hora_llegada, Hora_Cumplimiento, Anulada, Observ_central,    Central     , Seguridad        ) "
													."                  VALUES ('".$wcencam."','".$wfecha."','".$whora."','".$worigen."','PACIENTE DE ALTA','".$whabpac."' , 'Se dio alta definitiva desde el sistema de altas a la Historia: ".$row[1]."-".$row[2]." a las ".$whora."' , 'ALTA'  , '".$wusuario."', '".$wcco."', ''       , '00:00:00'    , '00:00:00'  , '00:00:00'       , 'No'   , ''            , '".$wcentral."', 'C-".$wusuario."')";  
										$err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
										//=======================================================================================================================================================
										} 
								}
				        }
				        
				      //==========================================================================================================================================================      
			          //==========================================================================================================================================================    
			          //Si seleccionaron *** MUERTE ***
			          //==========================================================================================================================================================
			          if (isset($wmuerte[$i]) and $wid[$i] == $row[9])           
			            {
						 
						 /************************************************************************************************************************
						  * Octubre 08 de 2012
						  *
						  * Si se da muerte procedo a cancelar todas las aplicaciones posteriores a la ronda en que se da la muerte
						  ************************************************************************************************************************/
						 //Consulto la hora de corte de dispensacion
						 $horaCorteDispensacion = consultarAliasPorAplicacion( $conex, $wemp_pmla, "horaCorteDispensacion" );
						  
						 //consulta la ronda actual
						 $rondaSiguiente = gmdate( "H:i:s", ( floor( date( "H" )/2 )*2 )*3600 );
						 $fechorRondaSiguiente = strtotime( date( "Y-m-d" )." ".$rondaSiguiente ) + 2*3600;	//Suma dos horas a la ronda actual
						 
						 //Consulto la ronda de corte de dispensacion
						 $fechorCorte = strtotime( date( "Y-m-d" )." $horaCorteDispensacion:00:00" ) + 24*3600;
						 
						 //Cancelo las aplicaciones realizadas entre la siguiente ronda en que se le da muerte al paciente hasta la hora de corte de dispensacion
						 cancelarAplicacionesPorRangoInc( $conex, $wbasedato, $row[1], $row[2], $fechorRondaSiguiente, $fechorCorte );
						 /************************************************************************************************************************/
						
				         //==========================================================================================  
					     //Ago 13 de 2010
					     //Si se coloco habitacion, traigo el nombre del paciente
					     if ($row[0] != "")
					       {
						    $whabpac=$row[0];
						       
						    $q = " SELECT Pacno1, Pacno2, Pacap1, Pacap2 "
						        ."   FROM root_000036, root_000037, ".$wbasedato."_000020 "
						        ."  WHERE Habcod = '".$whabpac."'"
						        ."    AND Habhis = Orihis "
						        ."    AND Habing = Oriing "
						        ."    AND Oriori = '".$wemp_pmla."'"
						        ."    AND Oriced = Pacced "
						        ."    AND Oritid = Pactid ";
						    $reshab = mysql_query($q,$conex);
						    $rowhab = mysql_fetch_array($reshab);
						    
						    $numhab = mysql_num_rows($reshab);
						    
						    if ($numhab > 0)
						       $whabpac="<b>".$whabpac."</b><br>Pac: ".$rowhab[0]." ".$rowhab[1]." ".$rowhab[2]." ".$rowhab[3];
						   }    
					     //==========================================================================================
					     
					     //=======================================================================================================================================================   
				         //Aca libero la habitación, porque el cadaver lo trasladan a transición y puedeque se halla o pueda facturar, pero hay que liberar la habitación
				         $q = " UPDATE ".$wbasedato."_000020 "
				             ."    SET Habali = 'on', "
				             ."        Habdis = 'off', "
				             ."        Habhis = '', "
				             ."        Habing = '', "
				             ."        Habfal = '".$wfecha."', "
				             ."        Habhal = '".$whora."'"
				             ."  WHERE Habhis = '".$row[1]."'"
				             ."    AND Habing = '".$row[2]."'"
				             ."    AND Habali = 'off' ";
				         $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());  
				         
				         //=======================================================================================================================================================   
				         //Aca saco el paciente de la habitación pero lo dejo en el servicio para puedan hacer la factura si es que no la han hecho y ponerlo en proceso
				         //de alta si es que no la han colocado.
				         if (isset($wmuerte[$i]) and $wid[$i] and $row[9])
				            {
					         $q = " UPDATE ".$wbasedato."_000018 "
					             ."    SET Ubihan  = ubihac, "
					             ."        Ubihac  = '', "
					             ."        Ubimue  = 'on',"
								 ."		   Ubialp  = 'on', " //(Jonatan 2013-08-16)
								 ."        Ubifap  = '".$wfecha."'," //(Jonatan 2013-08-16)
								 ."        Ubihap  = '".$whora."'" //(Jonatan 2013-08-16)
					             ."  WHERE Ubihis  = '".$row[1]."'"
					             ."    AND Ubiing  = '".$row[2]."'"
					             ."    AND Ubihac != '' "
					             ."    AND id      = ".$row[9];
				            }     
				         $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());   
				         
				         
				         //=======================================================================================================================================================
						 //Pido el servicio de Camillero con Camilla 
						 //Traigo el nombre del Origen de la tabla 000004 de la base de datos de camilleros con el centro de costos actual
						 $q = " SELECT Nombre "
						     ."   FROM ".$wcencam."_000004 "
						     ."  WHERE mid(Cco,1,instr(Cco,'-')-1) = '".$wcco."'"
						     ."  GROUP BY 1 ";
						 $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
						 $rowori = mysql_fetch_array($err);
					     $worigen=$rowori[0];
					     //$wcentral="CAMILLEROS";
					     //=======================================================================================================================================================
						 
						 //Traigo el Tipo de Central
						 $q = " SELECT Tip_central "
							 ."   FROM ".$wcencam."_000001 "
							 ."  WHERE Descripcion = 'PACIENTE DE ALTA'"
							 ."    AND Estado = 'on' ";
						 $restce = mysql_query($q,$conex);
						 $rowcen = mysql_fetch_array($restce);
						 $wtipcen = $rowcen[0];   	
						 
						 //Traigo la Central asignada para el Centro de Costos según el Tipo de Central //13 marzo
					    $q = " SELECT Rcccen "
					        ."   FROM ".$wcencam."_000009 "
					        ."  WHERE Rcccco = '".$wcco."'"
							."    AND Rcctic = '".$wtipcen."'";
					    $rescen = mysql_query($q,$conex);
					    $rowcen = mysql_fetch_array($rescen);
					    $wcentral=$rowcen[0]; 
						
						if ($wcentral == FALSE)
							{							
							$q = " SELECT Rcccen "
								."   FROM ".$wcencam."_000009 "
								."  WHERE Rcccco = '*'"
								."    AND Rcctic = '".$wtipcen."'";
							$rescen = mysql_query($q,$conex);
							$rowcen = mysql_fetch_array($rescen);
							$wcentral=$rowcen[0]; 
							}
						else
							{
							$wcentral=$wcentral;
						}
						 
						 
					         
					     //=======================================================================================================================================================
				         //Grabo el registro solicitud del camillero
				         $q = " INSERT INTO ".$wcencam."_000003 (   Medico     ,   Fecha_data,   Hora_data,   Origen     , Motivo                         ,   Habitacion  , Observacion                                                                                          , Destino             ,    Solicito    ,    Ccosto  , Camillero, Hora_respuesta, Hora_llegada, Hora_Cumplimiento, Anulada, Observ_central,    Central     , Seguridad        ) "
						             ."                  VALUES ('".$wcencam."','".$wfecha."','".$whora."','".$worigen."','TRASLADO SALA TRANSICION (680)','".$whabpac."' , 'Se registro muerte en el sistema de altas a la Historia: ".$row[1]."-".$row[2]." de las ".$whora."' , 'SALA DE TRANSICION', '".$wusuario."', '".$wcco."', ''       , '00:00:00'    , '00:00:00'  , '00:00:00'       , 'No'   , ''            , '".$wcentral."', 'C-".$wusuario."')";  
						 $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
						 //======================================================================================================================================================= 
						 
						 
						 
						 //=======================================================================================================================================================
						 //Cancelo el servicio de Alimentacion
						 //=======================================================================================================================================================
						 cancelar_pedido_alimentacion($row[1], $row[2], $wcco,"Muerte", $wusuario);           
						 cancelarPedidoInsumos($conex, $wbasedato, $row[1], $row[2]); //Noviembre 1 de 2017 Jonatan
						 
						 //Grabo el egreso en el servicio
						 //=======================================================================================================================================================
				         //Calculo los días de estancia en el servicio actual
		         	     $q=" SELECT ROUND(TIMESTAMPDIFF(MINUTE,CONCAT( Fecha_ing, ' ', Hora_ing ),now())/(24*60),2), Num_ing_Serv "
					       ."   FROM ".$wbasedato."_000032 "
					       ."  WHERE Historia_clinica = '".$row[1]."'"
					       ."    AND Num_ingreso      = '".$row[2]."'"
					       ."    AND Servicio         = '".$wcco."'"
					       ."  GROUP BY 2 ";
					     $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
					     $rowdia = mysql_fetch_array($err);
					     $wdiastan=$rowdia[0];
					     $wnuming=$rowdia[1];
					     
					     if ($wdiastan=="" or $wdiastan==0)
					        $wdiastan=0;
					        
					     if ($wnuming=="" or $wnuming==0)
					        $wnuming=1;
							
						//CALCULAR SI LA MUERTE ES MAYOR O MENOR DE 48 DESDE LA FECHA DE INGRESO A LA CLINICA    2013-01-23
						$query = "	SELECT 	ROUND(TIMESTAMPDIFF(MINUTE,(CONCAT ( Fecha_data, ' ', Hora_data )), now() )/(24*60),2) AS diferencia  "
								 ."	  FROM 	".$wbasedato."_000016  "
								 ."  WHERE	Inghis = '".$row[1]."'"
								 ."    AND	Inging = '".$row[2]."'";								 
						$reso = mysql_query($query, $conex);
						$numo = mysql_num_rows($reso);
						$diferencia = 0;
						if($numo > 0){
							$rowo = mysql_fetch_assoc($reso);
							$diferencia = $rowo['diferencia'];
						}
					     $wmotivo='';
				         if ($diferencia>=2)
					        $wmotivo="MUERTE MAYOR A 48 HORAS";
					     else 
					        $wmotivo="MUERTE MENOR A 48 HORAS"; 
							
						BorrarAltasMuertesAntesDeAgregarNueva($conex, $wbasedato, $row[1], $row[2], 'Nueva muerte');	
					     
					     //Grabo el registro de egreso del paciente por Muerte
				         $q = " INSERT INTO ".$wbasedato."_000033 (   Medico       ,   Fecha_data,   Hora_data,   Historia_clinica,   Num_ingreso,   Servicio ,  Num_ing_Serv,   Fecha_Egre_Serv ,   Hora_egr_Serv ,    Tipo_Egre_Serv,  Dias_estan_Serv, Seguridad        ) "
						             ."                    VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$row[1]."'      ,'".$row[2]."' ,'".$wcco."' ,".$wnuming."  ,'".$wfecha."'      ,'".$whora."'     , '".$wmotivo."'   ,".$wdiastan."    , 'C-".$wusuario."')";   
						 $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
						 //echo $q;
						 //=======================================================================================================================================================   
					        
				        }  
				        
				      //==========================================================================================================================================================    
				      //==========================================================================================================================================================      
			         }
	            }            
			     
			     
	      //Aca trae los pacientes que estan hospitalizados en el servicio (ubisac) del usuario matrix y que no esten en proceso de traslado,
	      //y que no esten ni en proceso ni en alta
	      $q = " SELECT Ubihac, Ubihis, Ubiing, Pacno1, Pacno2, Pacap1, Pacap2, Ubialp, Ubisan, ".$wbasedato."_000018.id, Ubimue, Pactid, Pacced, ".$wbasedato."_000018.id, Ubiprg "
		      ."   FROM root_000036, root_000037, ".$wbasedato."_000018 "
		      ."  WHERE Ubihis  = Orihis "
		      ."    AND Ubiing  = Oriing "
		      ."    AND Oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia, 
		      ."    AND Oriced  = Pacced "
		      ."    AND Oritid  = Pactid "
		      ."    AND Ubiptr != 'on' "             //Solo los pacientes que no esten siendo trasladados
		      ."    AND Ubiald != 'on' "             //Que no este en Alta Definitiva
		      ."    AND Ubisac  = '".$wcco."'"       //Servicio Actual
		      ."  GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14 ";
		  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		  $num = mysql_num_rows($res);
		  
		  $array_pacientes = array();
		  
		  while($row1 = mysql_fetch_array($res)){
			  
			  $array_pacientes[$row1['Ubihis']."-".$row1['Ubiing']] = $row1;
			  
		  }
//		  print_r($array_pacientes);
		  echo "<table align='center'>";
		  echo "<tr class=subtituloPagina>";
		  echo "<td colspan=10 align=center>Servicio o Unidad: ".$wnomcco."</td>";
		  echo "</tr>";
		  echo "<tr>";
		  echo "<td> </td>";
		  echo "<td> </td>";
		  echo "<td> </td>";
		  echo "<td> </td>";
		  echo "<td> </td>";
		  echo "<td class=fondorojo colspan=5> <b>NOTA:</b> Debe justificar si se supera el tiempo promedio</td></tr>";
		  echo "</table>";
		 
		  
		  /************************************************************************************************
		   * Julio 10 de 2012
		   ************************************************************************************************/
		  //consulto cuanto tiempo hay para cancelar el alta
		  $tiempoReactivacionAlta = consultarAliasPorAplicacion( $conex, $wemp_pmla, "tiempoReactivacionAltas" );
		   
		  $resCancelarAltas = consultarUltimasAltas( $conex, $wbasedato, time()-$tiempoReactivacionAlta*3600, $wemp_pmla, $wcco );
		  $numCancelarAltas = mysql_num_rows( $resCancelarAltas );
		  /************************************************************************************************/
		  
		  if ($num > 0 || $numCancelarAltas > 0 )
		     {
			  echo "<INPUT type='hidden' name='inClaveCancelarAlta' id='inClaveCancelarAlta'>";
			  
			  echo "<div id='divClaveCancelarAlta' style='display:none'><b>Ingresa la clave</b><br><br><INPUT type='password' name='pwClaveCancelarAlta' id='pwClaveCancelarAlta' value=''><br><br><INPUT type='button' value='Aceptar' onClick='enter3();'></div>";
			  
			  echo "<table align='center'>";			 		  
			  echo "<tr class=encabezadoTabla>";
			  echo "<th style='display:none;'>Posible Traslado<br> o Alta</th>";
			  echo "<th>Habitacion</th>";
			  echo "<th>Historia</th>";
			  echo "<th>Paciente</th>";
			  echo "<th>Alta en Proceso</th>";
			  echo "<th> Dar Alta<br>Definitiva</th>";
			  echo "<th>Cancelar Alta<br>en proceso</th>";
			  echo "<th>Muerte</th>";
			  echo "<th>Cancelar Muerte</th>";
			  echo "<th>Afinidad</th>";
			  echo "<th>Cancelar Alta<br>Definitiva</th>";
			  echo "</tr>";
			   
			  $wbasedatocliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
			  
			  //Elimino del arreglo de pacientes el que ya tiene egreso (cliame_000108). 
			  foreach($array_pacientes as $key => $row){
				  
				  $q_108 = " SELECT * "
								."   FROM ".$wbasedatocliame."_000108 "
								."  WHERE Egrhis = '".$row['Ubihis']."'"
								."    AND Egring = '".$row['Ubiing']."'";
					  $res_108 = mysql_query($q_108,$conex) or die (mysql_errno().$q_108." - ".mysql_error());
					  $num_108 = mysql_num_rows($res_108);
					   
					  //Valida si el paciente ya tiene un egreso registrado para la historia e ingreso, si no es asi muestra el paciente para darle de alta definitiva.
					  if($num_108 > 0){
						  
						  unset($array_pacientes[$row['Ubihis']."-".$row['Ubiing']]);
						  
					  }  
				  
			  }
			  
			  $i = 1;
			  
			  //Recorreo el arreglo sin los pacientes que ya tienen egreso.
			  foreach($array_pacientes as $key => $row)
				 {				 
					 	 	  
					  $wnota = "";  
					  if (is_integer($i/2))
						 $wclass="fila1";
						else
						   $wclass="fila2";
					  
					  $whab = $row[0];
					  $whis = $row[1];
					  $wing = $row[2];
					  $wpac = $row[3]." ".$row[4]." ".$row[5]." ".$row[6];
					  $wdpa = $row[12];     
					  $wtid = $row[11];
					  $wreg = $row[13];
					  $wpos = $row[14];   //Habitacion de Posible traslado
					  $title_saldo = "";
					  $wcolor = "";
					  
					  echo "<tr class=".$wclass.">";					  
					  
					  if ($wccoapl=="on" and $wccohos=="on")
						 $whabilita="Enabled";
						else
						   $whabilita="Disabled"; 
					  
					  //============================================================================================================
					  //Aca coloco todas las habitaciones ocupadas y desocupadas, porque este campo es para un posible traslado
					  //============================================================================================================
					  $q = " SELECT Habcod "
						 . "   FROM ".$wbasedato."_000020 "
						 . "  WHERE Habest = 'on' ";
						 $res1 = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						 $num1 = mysql_num_rows($res1);

						 echo "<td style='display:none;' align=center><select name='whabprg[".$i."]' onchange='enter()' ".$whabilita.">";

						 if (isset($whabprg[$i]) and $wpos!="")                  //Si se selecciono una opcion del dropdown
							echo "<option>".$whabprg[$i]."</option>";
						   else
							  if ($wpos!="")                                     //Si el campo posible traslado tiene informacion la muestro
								 echo "<option>".$wpos."</option>"; 
						 
						 echo "<option>&nbsp</option>";
						 echo "<option>Alta</option>";

						 for ($j=1;$j<=$num1;$j++)
						   {
							$row1 = mysql_fetch_array($res1);

							if ($whabprg[$i] == $row1[0])
							   echo "<option selected>".$row1[0]."</option>";
							  else
								 echo "<option>".$row1[0]."</option>";
						   }
						 echo "</select></td>";
					  //============================================================================================================	 
									
					  
					  echo "<INPUT TYPE='HIDDEN' NAME='wid[".$i."]' VALUE='".$wreg."'>";
					  
					  $q = " SELECT count(*) "
						  ."   FROM ".$wbasedato."_000020 "
						  ."  WHERE Habhis = '".$whis."'"
						  ."    AND Habing = '".$wing."'"
						  ."    AND Habcod = '".$whab."'";
					  $reshab = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());  
					  $rowhab = mysql_fetch_array($reshab); 
					  
					  if ($rowhab[0] > 0) 
						 echo "<td align=center><b>".$whab."</b></td>";
						else
						   echo "<td align=center>&nbsp</td>"; 
						   
					  echo "<td align=center<b>".$whis." - ".$wing."</b></td>";
					  echo "<td align=left  <b>".$wpac."</b></td>";
					  
						if ($row[7] == "on")
						{
							echo "<td align=center bgcolor=000099></td>";   //Coloco la celda de color azul oscuro
							$habili_cancela_alta="ENABLED";
						}
						else
						{
							echo "<td align=center><INPUT TYPE='radio' NAME=wproceso[".$i."] onclick='enter()'></td>";
							$habili_cancela_alta="DISABLED";
						}
						   
					  //Verifico si se puede colocar el cajon de alta definitiva activo
					  $q = " SELECT COUNT(*) "
						  ."   FROM ".$wbasedato."_000018, ".$wbasedato."_000022 "
						  ."  WHERE Ubihis = '".$whis."'"
						  ."    AND Ubiing = '".$wing."'"
						  ."    AND Ubihis = Cuehis "
						  ."    AND Ubiing = Cueing "
						  ."    AND Ubialp = 'on' "
						  ."    AND Cuegen = 'on' "
						  ."    AND Cuepag = 'on' "
						  ."    AND Cuecok = 'on' ";
					  $resalt = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					  $rowalt = mysql_fetch_array($resalt);  
					  
					  $query_ins = "  SELECT SUM(Carcca - Carcap - Carcde) as saldo_insumos "
									  ."FROM ".$wbasedato."_000227 "
									 ."WHERE Carhis = '".$whis."'
										 AND Caring = '".$wing."'
										 AND Carcca - Carcap - Carcde > 0
										 AND Carest = 'on'";
					 $res_ins = mysql_query($query_ins, $conex) or die("Error: ".mysql_errno()." - en el query: ". $query_ins ." - ".mysql_error());
					 $row_ins = mysql_fetch_array($res_ins);
					 $saldo_insumos = $row_ins['saldo_insumos'];
					  
					  if($saldo_insumos == 0){
						  
						  if ($rowalt[0] > 0)                   //Si es mayor a cero es porque el paciente puede ser dado de alta
							 {
							  $whabilitar = "ENABLED";
							  $wcolor="ffff00";
							 } 
							else
							   {
								$whabilitar = 'DISABLED';
								$wcolor="";
							   } 
					  }else{
							
							if ($row[7] == "on"){
								$whabilitar = 'DISABLED';
								$wcolor = 'FFFFCC';
								$title_saldo = 'Paciente con saldo en insumos, comuníquese <br> con la jefe de enfermería en ese servicio.';
							}
					  }
					  
					  
					  $wreqjust = requiere_justificacion($wreg);
					  if($wreqjust == true)
						{
								 echo "<td align=center class='fondorojo msg' title='".$title_saldo."'><INPUT TYPE='radio' NAME=wdefinitiva[".$i."] ".$whabilitar." onclick='fnMostrar()' ></td>";
						}else
							{
								 echo "<td align=center class='msg' bgcolor='".$wcolor."' title='".$title_saldo."'><INPUT TYPE='radio' NAME=wdefinitiva[".$i."] ".$whabilitar." onclick='enter()' ></td>";	 
							}
					  echo "<td align=center><INPUT TYPE='radio' NAME=wcanalta[".$i."] ".$habili_cancela_alta." onclick='enter()' </td>";			
					
					//Si el paciente tiene muerte activa validara cuanto tiempo a pasado despues de marcada la muerte, si ha pasado el tiempo estimado no permite la cancelacion de la muerte.
					if ($row[10]=='on')
					{

						$wdatos_muerte = consultar_datos_muerte($conex, $wbasedato,$whis, $wing); //Consulta fecha y hora de muerte para el paciente.
						$wfecha_mue = $wdatos_muerte['fecha_muerte'];
						$whora_mue = $wdatos_muerte['hora_muerte'];
						
						$tiempo_max_can_muerte = time()-$tiempoReactivacionAlta*3600; //Resta el tiempo en horas configurado en la tabla 51 de root con el parametro 'tiempoReactivacionAltas'
						$wfecha_ini_canc_mue = date( "Y-m-d", $tiempo_max_can_muerte ); //Fecha inicial de cancelacion de la muerte.
						$whora_ini_canc_mue = date( "H:i:s", $tiempo_max_can_muerte ); //Hora inicial de cancelacion de la muerte.
						
						$wfecha_hora_ini = $wfecha_ini_canc_mue." ".$whora_ini_canc_mue; //Formato fecha y hora de inicio de cancelacion de muerte.
						$wfecha_hora_mue = $wfecha_mue." ".$whora_mue; // Formato fecha y hora para la muerte del paciente.
						
						$wfecha_hora_ini_unix = strtotime($wfecha_hora_ini); //Formato unix de la fecha y hora inicial de muerte.
						$wfecha_hora_mue_unix = strtotime($wfecha_hora_mue); //Formato unix de la fecha y hora de muerte.
						
						//Si la fecha de muerte del paciente es mayor o igual a la fecha incial configurada, el rabiobutton estara activo.
						if($wfecha_hora_mue_unix >= $wfecha_hora_ini_unix)
							{
							echo "<td align=center bgcolor=FF3333></B></td>";
							$cancelar_muerte = "ENABLED";
							$wnota = "";						
							}
						else
							{
							echo "<td align=center bgcolor=FF3333></B></td>";						
							$cancelar_muerte = "DISABLED"; 
							$wnota = "<p><font size=1 COLOR=red>Se marcó la muerte hace más de ".$tiempoReactivacionAlta." horas, ya no es posible cancelar la muerte.</p>";
							}					
						
					}
					else
					{
						echo "<td align=center><INPUT TYPE='radio' NAME=wmuerte[".$i."] onclick='enter()' ></td>";
						$cancelar_muerte = "DISABLED"; 
					}  
							
					  echo "<td align=center><INPUT TYPE='radio' NAME=wcanmuerte[".$i."] ".$cancelar_muerte." onclick='enter()' >".$wnota."</td>";
							
					  //======================================================================================================
					  //En este procedimiento pregunto si el paciente es cliente AFIN o no, y de que tipo
					  $wafin=clienteMagenta($wdpa,$wtid,$wtpa,$wcolorpac);
					  if ($wafin)
						 echo "<td align=center><font color=".$wcolorpac."><b>".$wtpa."<b></font></td>";
						else
						  echo "<td>&nbsp</td>";
					  //======================================================================================================     
					  
					  echo "<td></td>";
					  
					  echo "</tr>";
					  
					  $i++;
					  
					 }
				 	
				 
				 for( $i = 0; $rows = mysql_fetch_array( $resCancelarAltas ); $i++ ){
				 
				 if (is_integer($i/2))
	                 $wclass="fila1";
	                else
	                   $wclass="fila2";
					   
					echo "<tr class='$wclass'>";
					echo "<td></td>";
					//echo "<td></td>";
					
					//Historia
					echo "<td align='center'>";
					echo $rows[ 'Ubihis' ]."-".$rows['Ubiing'];
					echo "</td>";
					
					//Paciente
					echo "<td align='center'>";
					echo $rows[ 'Pacno1' ]." ".$rows['Pacno2']." ".$rows['Pacap1']." ".$rows['Pacap2'];
					echo "</td>";
					
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					
					//Cancelar Alta
					echo "<td align='center'><INPUT type='radio' name='inCancelarAltaDef' value='{$rows[ 'Ubihis' ]}-{$rows[ 'Ubiing' ]}' onClick='enter2()'></td>";
					
					echo "</tr>";
				 }
			  }
			 else
			    echo "NO HAY HABITACIONES OCUPADAS"; 
				
				
		  echo "</table>";
		  
		  echo "<div id='menuJusti' style='display:none;width:100%' title='JUSTIFICACIONES'>";
			generar_menu_justificaciones();
		  echo "<br><INPUT TYPE='button' value='Aceptar' onClick='agregarJusti()' style='width:100'>";
		  echo "</div>";
		  
		  unset($wccohos);   //La destruyo para que el vuelva a entrar al if inicial, donde esta el if de los 'or'
		         
		  $wpro="";
		  $wmuerte="";
		  for ($i=1;$i<=$num;$i++)
	   	     {
		   	  if (isset($wproceso[$i])) 
	             {
		          $wpro=$wpro."&wproceso[".$i."]=".$wproceso[$i]; 
		          $wini="N";            //Esta variable me sirve para poder determinar cuando no esta setiada la variable de alta o en proceso porque el usuario la desmarco, 
	                                    //de cuando se esta iniciando el programa y porque no esta setiada.
	             }
		    
		      if (isset($wmuerte[$i]))
		         $wmuerte=$wmuerte."&wmuerte[".$i."]=".$wmuerte[$i];   
		     } 
	      //echo "<br>";
	            
	      $wini="N";
	         
	      echo "<meta http-equiv='refresh' content='60;url=Registro_de_Altas.php?wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."&wcencam=".$wcencam.$wpro."&wini=".$wini.$wmuerte."&wcco=".$wcco."'>";
		                 
		  echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
	      echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
	      echo "<input type='HIDDEN' name='wini' value='".$wini."'>";
	      echo "<input type='HIDDEN' name='wcencam' value='".$wcencam."'>";
	    
	      echo "<input type='HIDDEN' name='wcco' value='".$wcco."'>";
         }
     }
	 
	 

		
		
    echo "</form>";
	  
    echo "<table>"; 
    echo "<tr class=boton><td align=center><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
    echo "</table>";
    
} // if de register

  echo "<script>";
  echo "$.unblockUI();";
  echo "</script>";

include_once("free.php");

?>