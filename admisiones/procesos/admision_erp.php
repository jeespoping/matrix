<?php
include_once("conex.php");
define('MYSQL_ASSOC',MYSQLI_ASSOC);
//header("Content-Type: text/html;charset=UTF8");
/****************************************************************************
* accion
2021-12-19 Sebastián Nevado: se cambia la función verificarCcoIngresoAyuda.
2021-04-26 Juan David Rodriguez: Se separa codigo js y funciones de php en archivos por separados para una mejor comprensión del codigo (js_admisiones_erp.js y funciones_admsiones_erp.php)
2021-04-26 Juan David Rodriguez: Se realiza modifición para que los datos demograficos de un paciente sean traidos desde la tabla root_000036 y no de la tabla 100 de cada empresa
2020-09-03 Jessica Madrid Mejía: Para los pacientes con tipo de documento configurado en el parámetro tipoDocumentoPacienteInternacional en root_000051 se envía correo notificando que son pacientes internacionales al hacer 
								 la admisión o si se modifica el tipo de documento, los correos de destino están configurados en el parámetro emailDestinoPacienteInternacional en root_000051
2020-08-12 Camilo Zapata: Manejo del error 12 de unix, nuevo tratamiento para las tablas que quedan sin grabar en una admisión de manera que deshaga todos los cambios y deje el proceso unix para ejecución desde el cronjob.
2020-06-13 Camilo Zapata: Modificaciones necesarias para permitir la actualización de ingresos anteriores. buscar esta fecha en caso de ser necesario
2020-04-29 Camilo Zapata: Modificación requerida para abrir el programa que imprime el sticker de historia inmediatamente se ha realizado la admisión
2020-03-20 Camilo Zapata: Se hacen las modificaciones para que refresque la pantalla cuando la admisión se realizó correctamente pero el parámetro de soportesAutomaticos, está desactivada.
2019-12-03 Camilo Zapata: Se valida para los adultos sin identificación, que puedan ingresar con el mismo número de documento( historia ) asignados la primer vez,
						  implicando esto la continuidad en el consecutivo de ingreso asociado.
2019-11-07 Andres Alvarez: Se adiciona funcionalidad para que realice la busqueda del paciente por el número de cedula en alguna de las otras empresas.
2019-09-18 Camilo Zapata: modificaciones en el programa para que en los centros de costos marcados con "off" en el campo Ccomdi en movhos_000011 no exija
						  diagnóstico ni médico de ingreso.
2019-07-08 Camilo Zapata: modificaciones en el manejo de la conversión a utf8 de variables para optimizar la escritura y la lectura de caracteres especiales.
2019-06-26 Jerson Trujillo: Se adapta el programa para que soporte dos campos del formulario de triage de la hce, para definir si se da de alta alvalidarCamposCedRes() paciente.
2019-01-24 camilo zapata: corrección en la validación del paso de una admisión a unix, si no hay ping, el indicador debe permanecer en off
2019-01-14 camilo zapata: aumento del tamaño permitido para el documento de identificación de 11 a 15 caracteres.
2018-08-31 camilo zapata: corrección de validacion del tope y el saldo a la hora de cambiar el tipo de causa de ingreso de accidente a
						  cualquier otro ya que tenía errores en los tipos de datos, nunca coincidian.
2018-08-30 camilo zapata: corrección del proceso para pacientes sin identificación, de tal manera que siempre coincidan el número de
						  identifcación y la historia asignada.
						  Tambien se adiciona la asignación de la historia asignada a las variables asociadas en unix, para que se haga el guardado correcto en el primer intento.
2018-08-13 camilo zapata: adicion de atributos referentes a las novedades de remisión para unix. buscar "ux_nov" para identificar
                          los campos que se mapearán en la tabla fanovacc, adicionalmente se hace lo necesario para que el "valor remitido" sea requerido u omitido dependiendo de la selección que se haga para el paciente, remitido o no.
2018-07-30 camilo zapata: se realizan las siguientes modificaciones en la gestión de los accidentes de tránsito.
						  - siempre que haya un cambio en la fecha del accidente, se consultará el tope correspondiente a dicha fecha.
						  - se adiciona la función validarFacturacionUnix, para que al intentar cambiar el responsable original de un accidente
						    de transito se asegure que el responsable anterior no tiene facturas activas pendientes en unix, o que tenga las notas créditos correspodientes, esto se hace a partir de una consulta a la tabla fasalacc donde el saldo debe estar
						    en cero para que permita hacer el cambio de responsable.
2018-07-19 camilo zapata: Corrección en la carga inicial de un paciente egresado con admisiones anteriores, para que el menu de tipo de 								  afilicación  traiga las opciones correctas, puesto que no estaba cargando nada inicialmente
2018-07-13 camilo zapata: A la hora de guardar los responsables se verifica la tabla 000313 la cual almacena el cambio de responsable
                           automàtico en cargos para que el segundo responsable se mantenga como principal en las tablas correspondientes 101, 16, etc pero manteniendo el orden de responsabilidad
						   original en la tabla 205.
2018-07-10 camilo zapata: implementación de relacion entre la cobertura en salud - tipo de afiliación y el pago compartido, por medio de la tabla 000316 (buscar dicha tabla y hacer seguimiento en caso de ser requerido) de tal manera que cada opción tenga un vinculo establecido con el menú siguiente, ejemplo cobertura en salud: "subsidiado" - tipo de afiliación: "no aplica".
2018-07-03 camilo zapata: adicion de codigo para que asocie las preanestesias automáticamente cuando un paciente tenga una de estas pendiente a su servicio de ingreso.
2018-06-29 Jessica Madrid: Se modifica la función grabartoperesponsable() y se agrega la función registrarLogTopes() para que cada vez
						   que se inserten o se eliminen topes guarde el registro en la tabla log de topes (cliame_000315) con el fin
						   de conservar un registro de como estaban los topes antes de cada modificación.
2018-06-26 camilo zapata: cambio de la función alerta() de javascript para que utilice una versión basada en dialog, y no en blockui para evitar el conflicto con otras posibles ventanas emergentes llamadas con anterioridad, principalmente en el formulario soat.
2018-06-26 Jessica Madrid: Al admitir un paciente con preadmisión se envía topesPreadmision en true y se agrega la validación en el llamado ajax guardarDatos
						   para evitar que se borren los topes cada vez que actualizan la admisión.
2018-06-22 camilo zapata: se modifica el progama para que muestre los botones de nueva admision y preadmision sin que consulte los datos de los admitidos del dia
2018-06-20 Camilo Zapata: - se adiciona atributo "_ux_ordfec" a la fecha de la autorizaciòn para que esta viaje a unix
                          - se evita que en la carga inicial muestre el listado completo de admitidos.
2018-05-29 Jerson Trujillo: El llamado automático para pintar la lista de pacientes con triage, se hará mientras no se esté haciendo una admisión.
2018-05-11 camilo zapata: se le quita el atributo ux al campo del detalle de la dirección, para que siempre guarde el campo dirección en ux 	                          evitando  que se reemplace este último, cuando ambos están diligenciados
2018-04-11 camilo zapata: adición de asociación de preanestesia en ingresos de ayudas diagnósticas.
2018-03-07 Jonatan Lopez: Se agrega observacion del triage en la descripcion del triage, sera controlado por el parametro ObservacionesFormHCETriage en root_000051 que tendra la tabla y lo consecutivos que tienen la observacion.
2018-01-17 camilo zapata: se modifica la consulta del tope del accidente, para que no traiga el tope según el dia del ingreso sino la fecha del accidente, puesto que puede haber un ingreso el dia de hoy referente a un accidente dado el año anterior y por lo tanto debe traer el tope del año anterior
2017-12-11 camilo zapata: corrección de nombres de tipo de documento al consultar un paciente particular, de manera que al consultar no se dañen los datos que viajan a unix
2017-11-23 camilo zapata: se valida que el campo poliza de los responsables no tenga el valor de la marca de agua al guardar para que no quede en unix.
2017-11-10  camilo zapata: se modifica la validación de caracteres en los campos de nombres y apellidos para que permitan eñes y tildes. adicional se parcha el ingtip de movhos_000016 procurando detectar un punto ocasional
2017-10-17  Camilo Zapata: se permite al campo que recibe el documento del acompañante recibir letras tambien para aquellos que son extranjeros.
2017-09-19  Veronica Arismendy se modifica código para agregar un parametro en la 51 para el barrio de un pais de residencia extranjero, se correigen tildes javascript
2017-08-31  camilo zapata: cambios para ejecución del cambio de consorcio fosyga
2017-06-25  Camilo Zapata: validación de servicio de ingreso para asociación de triage.
2017-06-22  Camilo Zapata: bloqueo de tabla wbasedato_000040 para el consecutivo y evitar daños con la concurrencia.
2017-06-20  Veronica Arismendy se modifica para agregar validaicón si el centro de costo esta usando el nuevo sistema de ayudas diagnosticas con el fin de que modifique los campos de la nueva estructura de la tabla citasen_000023
			pero si no que se conserve la forma en como se hacia antes.
			Se agrega la función getInfoCc para consultar la configuración del centro de costo en la tabla root_0000117 que es nueva para ayudas diagnosticas
2017-06-12: camilo zapata. que no muestre la agenda de preadmision en urgencias.
2017-05-23: camilo zapata. se modifica el programa para que al encontrar una historia en unix de un documento que NO EXISTE EN MATRIX, lo agregue en el ingreso correcto en matrix y en unix.( buscar 2017-05-23 )
2017-05-16: camilo zapata.
			- priorizarPermiso81 la idea de este parametro es que se respete la configuración de permisos realizada en la tabla 81 modificación realizada por solicitud de clinica del sur.
2017-05-16: camilo zapata.
			- se valida la asociación de las preanestesias a aquellos ingresos que sean por admisiones o por cirugia. y se agrega un parametro en la 51 que habilita la solicitud de cambio de documento.
2017-05-05: camilo zapata.
			- adicion de la opción "solicitud de cambio de documento", el cual se encarga de hacer envio de solicitudes de cambio de documentos al personal de registros médicos, para que ellos realicen el procedimiento
			  necesario para que el proceso se realice de manera satisfactoria.
2017-04-28: Camilo Zapata.
			- Se adiciona el manejo de las preanestesias, para que reemplace en las tablas necesarias( movhos_000204, tcx_000011 y hce_000075), la historia temporal asignada por el proceso de preanestesia,
			- se adiciona la opción de que se muestre para los pacientes de urgencias un tooltip con la información del motivo de consulta ( descripción del triage )
2017-04-28:	Edwar Jaramillo.
			- Hacía falta mostrar el botón "Alta definitiva/Egreso" para usuarios en la unidad de urgencias (Se estaba mostrando solo para usuarios de ayudas).
2017-04-24:	Edwar Jaramillo.
			- Se agregó plugin jAlert.
			- Nuevo botón para buscar y dar de alta a historias con ingresos de otras unidades diferentes a urgencias.
			- Modificación en la validación de sesión de usuario al cargar el programa.
			- Inicialización de algunas variables que generaban warnings.
			- Se creó el parámetro "admin_erp_ver_boton_alta_egreso" para permitir mostrar u ocultar el botón de dar alta definitiva y agreso a pacientes desde el programa de admisión.
2017-04-04 camilo zapata: se corrige el valor que toma el tope del soat cuando es por reingreso, debe tomar el tope del año en el que ingresó inicialmente, adicional a eso se agrega el comportamiento debido al valor remitido
						  de tal manera que se reste al saldo cuando corresponda
2017-03-28  camilo zapata: modificacion en la forma que se cambian los responsables, eliminando la restricción del cambio de saldo, y el borrado de topes para nuevos registros.
2017-02-07  camilo zapata: modificación que permite consultar datos de un paciente de manera mas eficiente y sin incurrir en errores de ajax( si se está consultando no haga otras llamados ajax), que impidan el funcionamiento correcto.
2017-01-11  camilo zapata: modificación que permite actualizar los datos del accidente para todos los ingresos que pertenezcan al mismo accidente previo en caso de ser necesario buscar: " while( $accPrevioReal != "") "
2016-27-12  camilo zapata: modificación que permite cambiar cualquier ingreso, activo o inactivo para causa "accidente de transito"
2016-12-05   camilo zapata: se implementa la eliminación del registro en la tabla 32 cuando se requiere anular un ingreso en un servicio de ayuda diangnóstica.
2016-11-30   camilo zapata: inserción en la tabla movhos_33 de pacientes que ingresan en las ayudas diagnósticas.
2016-11-28   camilo zapata: corrección de anulación de ingreso para que tenga en cuenta la empresa en root37
2016-11-18   camilo zapata: se valida el bloqueo de la tabla inpac, para suspender la grabación. en caso de bloqueo de inpac reintenta varias veces con diferencia de 3 segundos.
2016-10-24  camilo zapata: se desarrolla lo necesario para que reintente hacer el ingreso de aquellos pacientes cuya grabación en unix falla.
2016-10-19  camilo zapata: se adicionan campos de acompañante para los pacientes de ayudas diagnósticas. buscar $cco_usuario_ayuda;
2016-09-20  camilo zapata: se corrige para la seleccion de un responsable desde el tab.
2016-09-14  camilo zapata: se adiciona filtro de centro de costos para que muestre los pacientes ingresados en el servicio seleccionado
2016-09-08  camilo zapata: se habilita la opción de que se pueda elegir la fecha que sea, sin restricción (no aplica para las preadmisiones, en ese caso se guardan la fecha y la hora del sistema ). y que el listado de centros de costos pueda ser limitado por la tabla 000081 en el campo Percca
2016-06-19: camilo zapata: se validan los cargos en unix antes de hacer una anulación de ingreso.
2016-06-22: ( camilo zz): se modifica el script para que libere la habitación cuando se anula un ingreso buscar _000020 en caso de ser necesario
2016-05-16: ( camilo zz): modificación del script para que valide que el campo de alta definitiva de la movhos 18 sea distinto de ='off'. de tal manera que ya tenga alta definitiva. bsucar "2016-05-16"
2016-03-28: ( camilo zz): se modifica la consulta de historia e ingresos para que los ordene númericamente y se corrige para que no consulte el consecutivo de historia en unix a menos de que el parámetro de conexión a unix esté activo
2016-03-16: ( camilo zz): se modifica el programa para que al elegir pacientes sin identificación se guarde el número de historia en lugar del documento. y modificaciones de clinica del sur, como impresion de la historia y orden de consulta
2016-03-03: ( camilo zz): se modifica el programa para que filtre los usuarios admitidos por centros de costos siempre y cuando el usuario sea de un cco ayuda diagnostica para cualquier duda buscar "filtrarCcoAyuda"
2016-03-01: ( camilo zz): se modifica el programa para que se pueda abrir desde el programa de "citas" o "agenda" guardando el turno en la tabla de log correspondiente.
2016-02-24: ( camilo zz): se modifica el programa para que se incluya el campo zona geográfica en la admision de clinica del sur.
2016-02-22: ( camilo zz): se modifica el programa que el cco de costo por defecto lo tome de la tabla cliame_81
2016-02-08: ( camilo zz): se modifica el programa que tenga en cuenta los siguientes parámetros en clínica del sur:
							- filtraEspecialidadClinica ( en el médico de ingreso solo utilizará los médicos cuya especialidad se médica )
							- tipoUsuarioDefecto        ( pra clínica del sur el tipo de usuario cargará por defecto en general, no en institucional )
							- pideTipoServicio          ( pide el tipo de servicio asociado al tipo de atención definida por el centro de costos: ejemplo, en clinica del sur
														 el centro de costos medicina domiciliaria tiene los servicios Agudo, cronico y hospitalización )
														 este cambio es el mas profundo puesto que se utiliza la tabla clisur_000174
2015-12-14: ( camilo zz): se modifica el programa para que grabar el ingreso primero en matrix y luego en unix, y se graba la hora de inicio del proceso de admisión
2015-10-15: ( camilo zz): se quita la clase requerido a los campos creados por jerson para el manejo de los cursos, se modifica el programa para que libere la habitación
						  cuando se anula un ingreso y este está en proceso de traslado y sin movimientos hospitalarios anteriores. tambien se anula el turno en caso de que este tenga
2015-09-07: ( camilo zz): se verifica al entrar a editar un ingreso, que si este paciente tiene ingresos posteriores, ya no deje modificar al que el usuario está viendo.
2015-08-24: (Jerson Trujillo): Se implementa una nueva funcionalidad para el manejo del turnero, la cual permite visualizar
			una lista de pacientes que estan en turno y desde ahí iniciar el proceso de la admisión.
2015-08-05: ( Camilo zz): se modifica el programa adicionando los campos de inicio y final de responsabilidad por parte de la entidad, tambien se modifica el programa para que
			permita la selección de un responsable como complementario para la liquidación de cirugías.
2015-07-27: ( Camilo zz): se modifica el programa para que tenga en cuenta si el tipo de documento que se va a ingresar es alfanumérico o no.
2015-07-23: ( Camilo zz): se modifica el programa para que siempre que haya un unico responsable tenga como fecha de inicio de responsabilidad, la fecha del ingreso del paciente
2015-07-14: ( Camilo zz): Se habilita la autorizacion de uso de datos personales por parte de la organización y el guardado de la misma en la tabla de pacientes y en el log
2015-07-14: ( Camilo zz): se restringe el ingreso de los valores del tope y el porcentaje reconocido, con el propósito de evitar errores de diligenciamiento.
2015-07-14: ( Camilo zz): se evita la modificación del campo resfir, en la tabla 205.
2015-06-25: ( Camilo zz): se modificó el programa asignandolese un nuevo valor de ux al campo de pais de nacimiento, para recibirlo y guardarlo en unix si se trata de un extranjero
2015-06-12: ( Camilo zz): se modificó el programa consultaAseguradoras, para que en el autocompletar solo se puedan elegir, entidades activas.
2015-06-02: ( Camilo zz): se adicionó a la función javascript $(document).ready --> que no permita escribir comilla simple en ningún input, para evitar que se rompan los strings en el servidor
2015-05-22: ( camilo zz):  se modificó el programa no permita el guion en la cédula ni lineas de mas de 11 caracteres, para que haya concordancia con los documentos en unix
2015-05-21: ( camilo zz):  se modificó el programa para que permita editar siempre un ingreso, sin importar el ccco de ingreso y el del usuario
2015-05-19: (camilo zz): se modifica el programa para que cuando carga datos demográficos desde la base de datos, y estos no existan en los maestros, se notifique al usuario pero permitan continuar
			en caso de que se requiere elminar dicho cambio buscar "permitir la carga para corrección", poner la variable ['error'] = 1 y borrar dos lineas mas abajo donde se hace vacía la variable buscada
			ejemplo 'pac_muh = ""'
2015-04-23: (camilo zz): se modifica el programa para que no permita anular una admisión si el paciente está inactivo en unix. en caso de necesitar eliminar el cambio buscar "correccion 2015-04-23"
2015-03-13: se nodificó el programa para que no tenga en cuenta los campos que se llengan por defecto en el formulario en caso de que se esté consultando la información de un paciente buscar "correccion consulta de pacientes" --> camilo zapata
2015-03-12: se modificó el programa para que limpie el formulario cuando se busque una cédula que no existe y ya se ha llenado el mismo con datos de un ingreso anterior
2015-03-04: se modificó el programa para que utilice la perfilización, de usuarios de la tabla 000081( usuarios- cajeros ) y tabla 000241( usuarios externos que solo consultan ), adicionalmente se evita el
			ingreso de caracteres especiales en el nombre del paciente
2015-01-13: Se modificó el programa para que guarde los datos de residencia siempre nacionales, y en el extranjero solo el pais, la dirección y el teléfono.
2014-12-29: Se modificó el programa para que permita el ingreso del espacio en blanco en el campo del documento, antes de guardarse, se quitan los espacios al inicio y al final
2014-12-12: Se comentaron las partes que agregan la opcion "NO APLICA" a la busqueda de departamento y municipio
2014-12-11: se modifica el programa para que no deje continuar una admision si el paciente a admitir está en la lista de rechazados. Buscar "consultarSiRechazado"
2014-12-05: se modifica el programa para que no reaccine el valor del campo infdav( dirección del acudiente en la tabla inpacinf )
2014-11-05: Se cambia el query para buscar los medicos, se despliega la lista de medicos encima del input, se busca nombre de la aseguradora si es tipo empresa
2014-10-30: Se realizan cambios cuando el responsable de la cuenta es particular.
2014-10-14: Se actualiza la tabla cliame 000207, solicitado por Juan Carlos.
			Se actualiza movhos_000018 solo si el campo ubisan esta en blanco
2014-10-15: Se verifica la existencia de un primer responsable en caso de SOAT o si el pagador es tipo empresa.
			Se reducen las solicitudes al servidor para llenar la tabla log.
			Se consideran los nuevos campos historia-ingreso de las tablas de CUPS de admision y preadmision.
			Se inhabilita el programa si se accede desde Internet Explorer.
2014-10-02: Si estoy haciendo una admision y el paciente tiene una preadmision programada, permite traer esos datos para modificarlos y admitir.
			Si estoy en una preadmision y el paciente ya tenia una programada, permite modificar dicha preadmision.
2014-08-26: No se permiten guardar caracteres especiales en la tabla de maestros del paciente
2014-08-12: Se crea el campo "Medico de ingreso", no se permite ingresar . en nombres, se consultan parametros de tarifas entre otros para los pacientes particulares y cambia la forma de grabar en movhos 16
2014-07-22: Se guardan, muestran y admiten desde preadmision, los pacientes de preadmision con accidente de transito.
			Se listan los accidentes previos por paciente CONSERVANDO el saldo por cada reingreso.
			Si el cco de ingreso tiene el campo ccocir='on' en movhos 11, se guarda Ubiptr='on' en movhos 18
2014-07-14: Se consulta el estado del paciente en UNIX al momento de guardar para las admisiones desde Preadmision.
2014-07-11: Se modifica la funcion consultarSiActivo para que tambien consulte en UNIX el estado del paciente.
2014-07-03: Se realiza control sobre la respuesta json al consultar,
			al consultar documento responsable molestaba al tratar de escribir el nombre,
			se quita el campoRequerido a los planes cuando se consulta,
			Se muestran todos los ingresos en la clinica aunque el usuario no pertenezca a ese cco,
			Buscar por codigo las empresas SOAT
*****************************************************************************/
include_once("root/comun.php");
include_once("root/magenta.php");  //para saber si el paciente es afin
include_once("ips/funciones_facturacionERP.php");

// Se encuentran todas las funciones de php, para mejor comprensión del codigo y hacer un mejor debug
include_once("funciones_admisiones_erp.php");


if( isset($user) ){
	$user2 = explode("-",$user);
	( isset($user2[1]) )? $key = $user2[1] : $key = $user2[0];
}

define("NOMBRE_BORRAR",'Eliminar');
define("NOMBRE_ADICIONAR",'Adicionar');
define("NOMBRE_ADICIONAR1",'Adicionar Responsable');

if( isset($accion) ){
	if(!array_key_exists("user", $_SESSION)){
		$data = array('error'=>2,'mensaje'=>'La sesion de usuario caduco. Cierre el programa y vuelva a abrirlo');
		echo json_encode($data);
		return;
	}


}
if( isset($debug) ){
	$data = array();
	$data[ "error" ] = 1;

	if( $data[ "error" ] == 0 && false ){
		echo "AAA";
	}else{
		echo "BBB";
	}
	//echo "hola";
}

if (isset($accion) and $accion == 'validarFacturacionUnix'){

	$data = array('error'=>0,'mensaje'=>'','html'=>'', 'respuesta'=>false );

	$sql1 = "SELECT Asecod, Asedes,Emptar,Asecoe
			   FROM {$wbasedato}_000193, {$wbasedato}_000024
			  WHERE Asecod ='{$responsable}'
			    AND Asecoe = Empcod
			    AND Aseest = 'on'";

	$res1= mysql_query( $sql1, $conex );
	if ($res1){

		$num1 = mysql_num_rows($res1);

		if( $num1 > 0 ){
			$rows1       = mysql_fetch_array($res1);
			$responsable = $rows1['Asecoe'];
		}
	}
	else
	{
		$data['error']   = 1;
		$data['mensaje'] = "No se ejecuto la consulta ".mysql_errno()." - Error en el query $sql - ".mysql_error()."";
	}

	$_POST["responsable"] = $responsable;
	$a                    = new admisiones_erp( 'validarFacturacionUnix', $historia, $ingreso );
	$dataU                = $a->data;

	if( $dataU['error'] == 0 ){
		$data['respuesta'] = ( $dataU['saldoEncontrado'] > 0 ) ? false : true ;
		$data['saldo']     = $dataU['saldoEncontrado'];
	}else{
		$data['error']     = 1;
		$data['mensaje']   = $dataU['mensaje'];
		$data['respuesta'] = false;
	}
	unset($a);
	echo json_encode($data);
	return;
}

if (isset($accion) and $accion == 'consultarConsecutivo'){
	global $conex;

	$historia = "000001";
	$sql3= "select Carcon
			  from ".$wbasedato."_000040
			 where Carfue='01'";
	$res3 = mysql_query($sql3,$conex) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando el consecutivo de historia " .mysql_errno()." - Error en el query $sql3 - ".mysql_error() ) );
	if ($res3){
		$num3=mysql_num_rows($res3);
		if ($num3>0 )
		{
			$rows3 = mysql_fetch_array($res3);
			$historia= $rows3['Carcon']+1;
		}
	}
	echo $historia;
	exit;
}

if (isset($accion) and $accion == 'consultarMedico' ){
	global $conex;
	$aplicacion=consultarAplicacion2($conex,$wemp_pmla,"movhos");
	$json = consultarMedicos( $q, $wbasedato, $aplicacion, false );
	echo $json;
	exit;
}

if (isset($accion) and $accion == 'cargarDatosAccidente')
{
	$data = array('error'=>0,'mensaje'=>'','html'=>'','valor'=>'','usu'=>'');
	$data[ 'infoing' ][0] = []; // para que la funcion no falle
	consultarAccidentesAlmacenados( $historia, $ingreso, $data );
	echo json_encode($data);
	exit;
}

if (isset($accion) and $accion == 'consultarPais')
{
	$data = array('error'=>0,'mensaje'=>'','html'=>'','valor'=>'','usu'=>'');
	$json = consultarPaises( $q );
	echo $json;
	exit;
}

if (isset($accion) and $accion == 'consultarDepartamento')
{
	$data = array('error'=>0,'mensaje'=>'','html'=>'','valor'=>'','usu'=>'');
	$json = consultarDepartamentos( $q, @$codigoPais, @$name_objeto);
	echo $json;
	exit;
}

if (isset($accion) and $accion == 'consultarMunicipio')
{

	$data = array('error'=>0,'mensaje'=>'','html'=>'','valor'=>'','usu'=>'');
	$json = consultarMunicipios( $q, $dep );
	echo $json;
	exit;
}

if (isset($accion) and $accion == 'consultarBarrio')
{
	$data = array('error'=>0,'mensaje'=>'','html'=>'','valor'=>'','usu'=>'');
	$json = consultarBarrios( $q, $mun );
	echo $json;
	exit;
}

if (isset($accion) and $accion == 'consultarOcupacion')
{
	$data = array('error'=>0,'mensaje'=>'','html'=>'','valor'=>'','usu'=>'');
	$json = consultarOcupaciones( $q );
	echo $json;
	exit;
}

if (isset($accion) and $accion == 'consultarAseguradora')
{
	$data = array('error'=>0,'mensaje'=>'','html'=>'','valor'=>'','usu'=>'');
	$json = consultarAseguradoras( $q, $wbasedato );
	echo $json;
	exit;
}

if (isset($accion) and $accion == 'consultarCUPS')
{
	$data = array('error'=>0,'mensaje'=>'','html'=>'','valor'=>'','usu'=>'');
	$json = consultarCUPS( $q );
	echo $json;
	exit;
}

if (isset($accion) and $accion == 'consultarImpresionDiagnostica')
{
	$data = array('error'=>0,'mensaje'=>'','html'=>'','valor'=>'','usu'=>'');
	$json = consultarImpresionesDiagnosticas( $q, $edad, @$sexo );
	echo $json;
	exit;
}
if (isset($accion) and $accion == 'buscarTarifaParticular')
{
	$data = array('error'=>0,'mensaje'=>'','html'=>'','valor'=>'','usu'=>'');
	$json = consultarTarifasParticular( $q );
	echo $json;
	exit;
}

if (isset($accion) and $accion == 'llenarSelectPlan')
{
	$data = array('error'=>0,'mensaje'=>'','html'=>'');
	$param="class='reset'";

	$sql = "SELECT Placod,Plades
			from ".$wbasedato."_000153
			where Plaemp='".$valor."'
			and Plaest='on'";


	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	$data['html'] .="<option value=''>Seleccione..</option>";
	for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ )
	{
		$data['html'] .= "<option value='".utf8_encode($rows['Placod'])."'>".utf8_encode($rows['Plades'])."</option>";
	}
	if($i==0)
	{
		$sql = " SELECT Placod,Plades
			from ".$wbasedato."_000153
			where Plaemp='*'
			and Plaest='on'
			";
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ )
		{
			$data['html'] .= "<option value='".utf8_encode($rows['Placod'])."'>".utf8_encode($rows['Plades'])."</option>";
		}
	}
	echo json_encode($data);
	return;
}

if (isset($accion) and $accion == 'consultarTipoServicio' ){

	$data        = array('error'=>0,'mensaje'=>'','html'=>'');
	$codServicio = explode( "-", $codServicio );
	$aplMovhos   = consultarAplicacion2($conex,$wemp_pmla,"movhos");
	$codServicio = $codServicio[1];
	$entro       = false;

	$sql = "SELECT Seides, Seiuni, Seitat
			  FROM {$wbasedato}_000174, {$aplMovhos}_000011
			 WHERE Ccocod = '{$codServicio}'
			   AND Seitat = Ccotat
			";
	//echo $sql;
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	$data['html'] .="<option value=''>Seleccione..</option>";
	for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ )
	{
		$entro = true;
		$selected = ( $tipoAtencionSelected == $rows['Seiuni'] ) ? "SELECTED" : "";
		$data['html'] .= "<option value='".utf8_encode($rows['Seiuni'])."' {$selected}>".utf8_encode($rows['Seides'])."</option>";
	}
	$data['error']   = ( !$entro ) ? "3" : "";
	$data['mensaje'] = ( !$entro ) ? "3->no tiene datos asociados" : "";
	echo json_encode($data);
	return;

}

if (isset($accion) and $accion == 'traertopespreadmision'){

	global $wbasedato;
	global $conex;
	//$data = array('error'=>0,'mensaje'=>'','html'=>'','numRegistrosIng'=>'', 'ultimoIngreso' => '', 'numRegistrosPac'=>'');
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "facturacion" );
	$sqlpro1 = "  select Toptdo 	,Topdoc ,	Topres 	,Toptco ,	Topcla ,	Topcco, 	Toptop, 	Toprec ,	Topdia ,	Topsal 	,Topest ,	Topfec
					from ".$wbasedato."_000215
					where Toptdo = '".$tipodocumento."'
					  and Topdoc = '".$documento."'
					  and Topest = 'on'"; //no sea de soat
	$sqlpro1.=" ORDER BY Topres,Toptco desc";
	//$data[ 'html' ] = $sqlpro1;



	//$data[ 'html' ] = $sqlpro1;
	$respro= mysql_query( $sqlpro1, $conex );
	while($rowspro = mysql_fetch_array($respro))
	{


	  $data[] = array(
					  "top_ccohidCcoTop" => $rowspro["Topcco"],
					  "top_clahidClaTop" => $rowspro["Topcla"],
					  "top_diachkValDia" => $rowspro["Topdia"],
					  "top_rectxtValRec" =>  $rowspro["Toprec"],
					  "top_reshidTopRes" => $rowspro["Topres"],
					  "top_tcoselTipCon" => $rowspro["Toptco"],
					  "top_toptxtValTop" => $rowspro["Toptop"],
					  "top_fectxtValFec" => $rowspro["Topfec"],
				);

	}



	echo json_encode((object)$data);
	return;

}

if (isset($accion) and $accion == 'empresahacedigitalizacion'){

	global $wbasedato;
	global $conex;
	//$data = array('error'=>0,'mensaje'=>'','html'=>'','numRegistrosIng'=>'', 'ultimoIngreso' => '', 'numRegistrosPac'=>'');
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "facturacion" );

	/*
	Consulto las empresas y los centros de costos que hacen digitalizacion
	*/

	$sqldigitalizacion = "  SELECT Empcod, Empccd
							  FROM ".$wbasedato."_000024
							 WHERE Empdso = 'on'
							   AND Empcod = '".$empresa."'
							   ";
	$res= mysql_query( $sqldigitalizacion, $conex );
	$parametros = '';
	if($row = mysql_fetch_array($res))
	{
	  $parametros = $row['Empccd'];
	  if($parametros=='' || $parametros=='*' || $parametros=='No Aplica')
		  $parametros = '*';
	}
	echo  $parametros;
	return;

}
if (isset($accion) and $accion == 'guardarDatos'){


	$data = array('error'=>0,'mensaje'=>'','html'=>'','data'=>'','historia'=>'','ingreso'=>'','documento'=>'');
	global $wbasedato;
	global $conex;
	global $pac_ciuhidMunNac;
	global $ing_histxtNumHis;
	global $ing_seisel_serv_ing;

	$ing_tpaselTipRes="";

	//2014-08-26
	global $pac_no1txtPriNom,$pac_no2txtSegNom,$pac_ap1txtPriApe,$pac_ap2txtSegApe;
	$caracteres = array( "á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","\\","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??","?£", "°");
	$caracteres2 = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","","","a","e","i","o","u","A","E","I","O","U","A","S","", "", "N", "N", "U", "");
	$pac_no1txtPriNom = str_replace( $caracteres, $caracteres2, utf8_decode($pac_no1txtPriNom) );
	$pac_no2txtSegNom = str_replace( $caracteres, $caracteres2, utf8_decode($pac_no2txtSegNom) );
	$pac_ap1txtPriApe = str_replace( $caracteres, $caracteres2, utf8_decode($pac_ap1txtPriApe) );
	$pac_ap2txtSegApe = str_replace( $caracteres, $caracteres2, utf8_decode($pac_ap2txtSegApe) );
	$_POST['pac_no1txtPriNom'] = str_replace( $caracteres, $caracteres2, utf8_decode($_POST['pac_no1txtPriNom']) );
	$_POST['pac_no2txtSegNom'] = str_replace( $caracteres, $caracteres2, utf8_decode($_POST['pac_no2txtSegNom']) );
	$_POST['pac_ap1txtPriApe'] = str_replace( $caracteres, $caracteres2, utf8_decode($_POST['pac_ap1txtPriApe']) );
	$_POST['pac_ap2txtSegApe'] = str_replace( $caracteres, $caracteres2, utf8_decode($_POST['pac_ap2txtSegApe']) );
	$_POST['pac_noatxtNomAco'] = str_replace( $caracteres, $caracteres2, utf8_decode($_POST['pac_noatxtNomAco']) );
	$_POST['pac_nrutxtNomRes'] = str_replace( $caracteres, $caracteres2, utf8_decode($_POST['pac_nrutxtNomRes']) );
	//para unix
	$_POST['_ux_pacap2_ux_midap2'] = str_replace( $caracteres, $caracteres2, $_POST['_ux_pacap2_ux_midap2'] );
	$_POST['_ux_pacap1_ux_midap1'] = str_replace( $caracteres, $caracteres2, $_POST['_ux_pacap1_ux_midap1'] );
	$_POST['_ux_pnom1_ux_midno1'] = str_replace( $caracteres, $caracteres2, $_POST['_ux_pnom1_ux_midno1'] );
	$_POST['_ux_pnom2_ux_midno2'] = str_replace( $caracteres, $caracteres2, $_POST['_ux_pnom2_ux_midno2'] );
	$_POST['omitirCambioReponsable'] = false;

	if( $datosEnc['pac_pahtxtPaiRes'] != CODIGO_COLOMBIA ){
		$datosEnc['pac_trhselTipRes'] = "E";
	}else{
		$datosEnc['pac_trhselTipRes'] = "N";
	}

	$datosEnc['pac_tdaselTipoDocRes'] = trim( $datosEnc['pac_tdaselTipoDocRes'] );
	$datosEnc['pac_tdoselTipoDoc'] = trim( $datosEnc['pac_tdoselTipoDoc'] );

	$preadmisionAnulada = false;

	$consecutivoHistoriaDeUnix = false; //Variable que indica si el CONSECUTIVO de historia se trajo desde UNIX
	$datosEnc="";

	//se consulta si existe esa aplicacion
	$alias="movhos";
	$aplicacion=consultarAplicacion2($conex,$wemp_pmla,$alias);

	$alias1="hce";
	$aplicacionHce=consultarAplicacion2($conex,$wemp_pmla,$alias1);
	$intentosIngreso=consultarAplicacion2($conex,$wemp_pmla,"intentosPermitidos");//--> intentos permitidos para grabar un ingreso en unix
	$tieneConexionUnix = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'conexionUnix' );
	//Consultar la historia e ingreso nuevo
	//1. Consultar por documento
	//	- Si el documento no se encuentra en la BD, significa que la historia no existe para el paciente
	//	- Si no se encuentra el documento, consultar la nueva historia
	//	- Si existe el documento, consulto la historia y el numero de ingreso nuevo (Esto es sumarle uno al último ingreso)

	//2020-07-04
	/*Consulto los datos del eventual accidente de tránsito para revisar en caso de que haya algun cambio al respecto tengamos los datos necesarios*/
	$sqlpro = "SELECT Accres, Accrei FROM {$wbasedato}_000148 
				WHERE Acchis = '{$historia}' AND Accing = '{$ingreso}'";
	$res2 = mysql_query( $sqlpro, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando datos de accidente " .mysql_errno()." - Error en el query $sql2 - ".mysql_error() ) );
	$num2 = mysql_num_rows($res2);
	
	$accResponsableActual = "";
	$accNuevoResponsable  = $_POST['_ux_accres'];
	if( $num2 > 0 ){//si antes de la modificación el ingreso corresponde a un accidente de tránsito.
		$rows2 = mysql_fetch_array($res2);
		$accResponsableActual = $rows2['Accres'];
	}

	$sql = "SELECT ingcai FROM {$wbasedato}_000101
				WHERE inghis = '{$historia}' AND ingnin = '{$ingreso}'";
	$rs  = mysql_query( $sql, $conex ) or die( mysql_error());
	$rows2 = mysql_fetch_array($rs);
	$causaIngresoOriginal =  $rows2['ingcai'];


	if( !empty( $historia ) && !empty( $ingreso ) && $causaIngresoOriginal == "02" ){
		if( ( $accResponsableActual != $accNuevoResponsable ) || $ing_caiselOriAte != '02' ){

			if( $hay_unix && $tieneConexionUnix == 'on' ){
				$_POST['accResponsableActual'] = $accResponsableActual;
				$a    = new admisiones_erp( 'consultarFacturacionAccidente', $historia, $ingreso );
				$data = $a->data;

				if( $data['error'] == 5	 ){ //El paciente esta activo
					$data['mensaje'] = "El accidente tiene cargos *facturados* en Unix, por favor resuelva esto y vuelva a intentarlo";
					$data['error'] = 1;
					echo json_encode($data);
					return;
				}
				unset( $a );
			}else{
				if( $tieneConexionUnix == 'on' ){
					$data[ "error" ] = 1;
					$data[ "mensaje" ] = utf8_encode("Usted desea realizar cambios en el accidente de transito , debido a fallas en la conexion con unix estos no pueden realizarse en este momento");
					echo json_encode($data);
					return;
				}
			}
			
		}
	}

	/***consulta o actualizacion de historia e ingreso***/
	if (!empty ($documento)) //se consulta si ese documento existe
	{
			//
			//SE COMPRUEBA EL ESTADO DEL PACIENTE EN UNIX ANTES DE ADMITIR
			//
			$estadoPaciente = false;//Inactivo
			if( $tieneConexionUnix != "on" ){
				$historiaUx = $_POST[ 'ing_histxtNumHis' ];
				$ingresoUx  = $_POST[ 'ing_nintxtNumIng' ];
			}

			//--> consultar si está llegando el último ingreso
			if( $modoConsulta == "true" ){//-->2016-12-27 -> para mantener la siguiente consulta solo para el último ingreso

				$sql = "SELECT pachis, pacact, max(ingnin*1) ultimoIngreso FROM {$wbasedato}_000101, {$wbasedato}_000100
						WHERE inghis = '{$_POST[ 'ing_histxtNumHis' ]}' AND pachis = inghis GROUP BY 1,2";

				$rs  = mysql_query( $sql, $conex );
				$row = mysql_fetch_assoc( $rs );
				if( $row['ultimoIngreso']*1 == $_POST[ 'ing_nintxtNumIng' ]*1 ){
					$esUltimoIngreso = true;
				}
			}

			if( $hay_unix && $tieneConexionUnix == 'on' && ( ( $modoConsulta != "true" ) || (  $modoConsulta == "true" && $esUltimoIngreso ) )/*2016-12-27*/ )//-->2016-03-28
			{
				$pac_doctxtNumDoc = strtoupper( $pac_doctxtNumDoc );

				$a    = new admisiones_erp( 'estadoPaciente', $pac_tdoselTipoDoc, $pac_doctxtNumDoc );
				$data = $a->data;
				$historiaUx = $_POST[ 'ing_histxtNumHis' ];
				$ingresoUx  = $_POST[ 'ing_nintxtNumIng' ];

				if( $data['mensaje'] != "" && $modoConsulta == "false" ){ //El paciente esta activo
					$data['mensaje'] = "El paciente esta activo en UNIX con la historia ".$data['mensaje']."\nEGRESARLO EN UNIX para continuar...";
					$data['error'] = 1;
					echo json_encode($data);
					return;
				}else if( $data['mensaje'] != "" ){
					$estadoPaciente = true;
				}
				unset( $a );
			}

		$estaUnixNoMatrix = false;
		if( $historia == "" )
		{
			$sql = "";
			if( $aplicacion == "" ){
				$sql = "Select Pacdoc,Pachis,id,Pacact from ".$wbasedato."_000100
					where Pacdoc = '".utf8_decode($documento)."' and Pactdo = '".utf8_decode($tipodoc)."'";
			}else{
				$sql = "Select Pacdoc,Pachis,a.id,Ubiald as Pacact, Ubihis
					from ".$wbasedato."_000100 a LEFT JOIN ".$aplicacion."_000018 b ON (Pachis=Ubihis)
					where Pacdoc = '".utf8_decode($documento)."' and Pactdo = '".utf8_decode($tipodoc)."'
					ORDER BY b.fecha_data desc, b.hora_data desc limit 1";
			}

			$res = mysql_query( $sql, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando el documento ".mysql_errno()." - Error en el query $sql - ".mysql_error() ) );
			$num= mysql_num_rows($res);

			if ($num>0) // si tiene registros se consulta el numero de ingreso
			{
				$rows=mysql_fetch_array($res);

				$historia=$rows['Pachis'];
				$sql2 = "select Inghis,MAX(Ingnin*1) AS Ingnin,id from ".$wbasedato."_000101
							where Inghis = '".utf8_decode($historia)."'";

				// ====================================================
				// --> OJO TEMPORAL JERSON Trujillo
				//$sql2.= " GROUP BY Inghis";
				// ====================================================

				$res2 = mysql_query( $sql2, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando el ingreso " .mysql_errno()." - Error en el query $sql2 - ".mysql_error() ) );
				if( $res2 )
				{
					$num2=mysql_num_rows($res2);
					if ($num2>0)
					{
						$rows2=mysql_fetch_array($res2);

						if( $aplicacion != "" ){ //si hay movhos, consulto Ubiald, alta definitiva, el estado es lo contrario a este valor
							( $rows['Pacact'] == "on" )? $rows['Pacact'] = 'off' : $rows['Pacact'] = 'on';
							if( $rows['Ubihis'] == "" ) $rows['Pacact'] = 'off'; //Si no trajo ubihis, es porque no existe registro en movhos 18, es decir, esta inactivo
						}

						if ($rows['Pacact'] != 'on') //si el paciente esta inactivo
						{
							if( is_null($rows2['Ingnin']) ){
								$rows2['Ingnin'] = 0;
							}
							//se le suma 1 al ingreso
							$ingreso=$rows2['Ingnin']+1;
						}else
						{
							if( is_null($rows2['Ingnin']) ){
								$rows2['Ingnin'] = 1;
							}
							$ingreso = $rows2['Ingnin'];
							$historia = $rows['Pachis'];
						}
					}
					if( $historia == $historiaUx ){
						if( $ingresoUx > $ingreso ){
							$ingreso = $ingresoUx;
						}
					}
				}
				else
				{
					$data[ 'error' ] = 1; //sale el mensaje de error
				}
			}
			else//no tiene registros con ese documento
			{

				//--> si no lo encontró en la 100, pero aún así tiene historia en unix;
				if( isset( $historiaUx ) and $historiaUx != "" ){//-->2017-05-23
					$historia         = $historiaUx;
					$ingreso          = $ingresoUx;
					$estaUnixNoMatrix = true;
					logAdmsiones( 'Historia recuperada de unix no esta en matrix', $historia, $ingreso, $documento);
				}else{
					//es una admision nueva y consulto el consecutivo
					//
					//se bloquea la tabla //2017-06-22--> bloqueo de tablas para el consecutivo y evitar daños con la concurrencia.
					$query = " UNLOCK TABLES ";
					$resquery = mysql_query($query,$conex) or ( $data[ 'mensaje' ] = utf8_encode( "Error desbloqueando la tabla de consecutivo de historias " .mysql_errno()." - Error en el query $sql4 - ".mysql_error() ) );

					$query = " LOCK TABLE ".$wbasedato."_000040 WRITE";
					$resquery = mysql_query($query,$conex) or ( $data[ 'mensaje' ] = utf8_encode( "Error bloqueando la tabla de consecutivo de historia " .mysql_errno()." - Error en el query $query - ".mysql_error() ) );

					$sql3= "select Carcon from ".$wbasedato."_000040 where Carfue='01'";
					$res3 = mysql_query($sql3,$conex) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando el consecutivo de historia " .mysql_errno()." - Error en el query $sql3 - ".mysql_error() ) );
					if ($res3)
					{
						$num3=mysql_num_rows($res3);
						if ($num3>0 )
						{
							$rows3 = mysql_fetch_array($res3);
							$historia  = $rows3['Carcon']+1;
							if( $pacienteNN == "on" ){
								$documento = $historia;
							}
							$ingreso=1;

							/*****se actualiza el consecutivo de las historias***/
							//if( $data[ "error" ] == 0 )
							//{
								$sql4 =  "UPDATE ".$wbasedato."_000040 set Carcon = Carcon + 1 where Carfue='01'";
								$res4 = mysql_query($sql4,$conex) or ( $data[ 'mensaje' ] = utf8_encode( "Error actualizando el consecutivo de historia " .mysql_errno()." - Error en el query $sql4 - ".mysql_error() ) );
							//}
						    /*****fin se actualiza el consecutivo de las historias***/

							logAdmsiones( 'Historia generada por matrix', $historia, $ingreso, $documento);
						}
					}
					else
					{
						$data[ 'error' ] = 1; //sale el mensaje de error
					}
					//se desbloquea la tabla 40
					$query = " UNLOCK TABLES ";
					$resquery = mysql_query($query,$conex) or ( $data[ 'mensaje' ] = utf8_encode( "Error desbloqueando la tabla de consecutivo de historias " .mysql_errno()." - Error en el query $sql4 - ".mysql_error() ) );
				}

			}
		}
	}//documento !empty
	else
	{
		$data[ 'error' ] = 1;
		$data[ 'mensaje' ] = "El documento esta vacio";
	}
	/***fin consulta o actualizacion de historia e ingreso***/

	// validar si el tipo de documento es Adulto sin identificación, para que reasigne la historia encontrada al número de documento
	// y todos los campos asociados a este
	$pac_tdoselTipoDoc  = trim( $pac_tdoselTipoDoc );
	if( trim( $pac_tdoselTipoDoc ) == "AS" ){

		if( $_POST['_ux_paccer_ux_mrecer_ux_accre3'] == $_POST['_ux_pacced_ux_midide'] ){

			$_POST['_ux_paccer_ux_mrecer_ux_accre3'] = $historia;
			$_POST['_ux_pacced_ux_midide']           = $historia;
		}

		if( $_POST['pac_crutxtNumDocRes'] == $_POST[ 'pac_doctxtNumDoc' ] ){

			$_POST['pac_crutxtNumDocRes'] = $historia;
		}


		$_POST[ 'pac_doctxtNumDoc' ] = $historia;
		$pac_doctxtNumDoc            = $historia;
		if( $ingreso < 1 )//2019-12-03 linea adicionada para permitir reingresos a adultos sin identificación.
			$ingreso  = 1;

	}


	//-->2016-05-13
	//mirar  si el ingreso anterior sigue activo en la tabla 18
	// SI YA TENEMOS HISTORIA E INGRESO, VALIDAMOS QUE EL INGRESO INMEDIATAMENTE ANTERIOR TENGA ALTA DEFINITIVA EN LA 18
	if( $ingreso*1 > 1 ){
		$ingresoAnterior = $ingreso - 1;
		$queryAD = " SELECT ubiald FROM {$aplicacion}_000018
					  WHERE ubihis = '{$historia}' AND ubiing = '{$ingresoAnterior}'";
		$rsAD    = mysql_query( $queryAD, $conex ) or die( mysql_error()."  -->  ".$queryAD);
		$row     = mysql_fetch_array($rsAD);
		if($row[0] == "off"){//--> el ingreso anterior no tiene alta definitiva
			$data['mensaje'] = "El paciente no tiene ALTA DEFINITIVA en su ingreso anterior historia:{$historia} - ingreso:{$ingresoAnterior}";
			$data['error'] = 1;
			echo json_encode($data);
			return;
		}
	}

	/***se guardan o se actualizan los datos***/
	$remitidoAnterior = 0;
	$estadoAdmision = "";
	$cambiaTipoDocumento = false;
	if( !empty( $historia ) && !empty( $ingreso ) )
	{
	   /**ingreso**/
		//Consulto si existe el registo
		$sql = "select Inghis,Ingnin,id, Ingunx, ingvre*1 from ".$wbasedato."_000101
				where Inghis = '".utf8_decode($historia)."' and Ingnin = '".utf8_decode($ingreso)."'";

		$res = mysql_query( $sql, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query $sql - ".mysql_error() ) );

		if( $res )
		{
			$tarifa = "";
			$num = mysql_num_rows( $res );

			if ((!empty ($responsables1) && count($responsables1)>0))
			{
				$ing_tpaselTipRes = $responsables1[0]['ing_tpaselTipRes'];
				$ing_cemhidCodAse = $responsables1[0]['ing_cemhidCodAse'];
			}

			$cambiaTarifaParticular = consultarAplicacion2($conex,$wemp_pmla,"cambiaTarifaParticular");
			if( $ing_tpaselTipRes == 'P' )//mirar si le pongo con el nombre del txt si toma la ultima
			{
				//$ing_cemhidCodAse = '01';
				if( $cambiaTarifaParticular == "on" ){
					$tarifa = $ing_tarhid;
				}else{
					$ing_cemhidCodAse = consultarAplicacion2($conex,$wemp_pmla,"codigoempresaparticular");
					$tarifa = consultarAplicacion2($conex,$wemp_pmla,"tarifaparticular");
				}
				//--> validación de cambio de codigo de responsable para la tabla 101 variable $ing_cemhidCodAse.
				$cambiarCodigoEntidad = consultarAplicacion2( $conex, $wemp_pmla, "responsable101particular" );
				if($cambiarCodigoEntidad == "c"){
					$ing_cemhidCodAse = consultarAplicacion2($conex,$wemp_pmla,"codigoempresaparticular");
				}
			}else{
				//se consulta la tarifa para guardarle dependiendo de la empresa
				$sqlt = "select Emptar from ".$wbasedato."_000024
				where Empcod = '".utf8_decode($ing_cemhidCodAse)."' and Empest = 'on'";

				$rest = mysql_query( $sqlt, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query $sqlt - ".mysql_error() ) );

				if( $rest )
				{
					$numt=mysql_num_rows($rest);
					if ($numt>0)
					{
						$rowst=mysql_fetch_array($rest);
						$tar=explode("-",$rowst['Emptar']);
						$tarifa=$tar[0];
					}
				}
			}

			//Si no se encontraron los datos, significa que es un registro nuevo
			if( $num == 0 ) //hace el insert
			{
				$estadoAdmision = "Nueva";
				
				$datosEnc = crearArrayDatos( $wbasedato, "ing", "ing_", 3 );

				//datos adicionales
				$datosEnc[ "ingusu" ] = $key; //usuario
				$datosEnc[ "inghis" ] = $historia; //historia
				$datosEnc[ "ingnin" ] = $ingreso; //ingreso
				$datosEnc[ "ingtin" ] = $datosEnc[ "ingsei" ]; //tipo ingreso
				$datosEnc[ "ingtar" ] = $tarifa; //tarifa

				$cambiarCodigoEntidad = consultarAplicacion2( $conex, $wemp_pmla, "responsable101particular" );
				if($cambiarCodigoEntidad == "c" and $responsables1[0]['ing_tpaselTipRes'] == "P"){
					$datosEnc[ "ingcem" ] = consultarAplicacion2($conex,$wemp_pmla,"codigoempresaparticular");
				}else{
					$datosEnc[ "ingcem" ] = $responsables1[0]['ing_cemhidCodAse'];
				}
				$datosEnc[ "ingtpa" ] = $responsables1[0]['ing_tpaselTipRes'];
				$datosEnc[ "ingpla" ] = $responsables1[0]['ing_plaselPlan'];
				$datosEnc[ "ingpol" ] = $responsables1[0]['ing_poltxtNumPol'];
				$datosEnc[ "ingnco" ] = $responsables1[0]['ing_ncotxtNumCon'];

				if ($aplicacion != "")
				{
					$servicioIng1=explode("-",$ing_seisel_serv_ing);
					$servicioIng=$servicioIng1[1];
					$datosEnc[ "ingsei" ] = $servicioIng; //servicio de ingreso
					$datosEnc[ "ingtin" ] = consultarTipoIngreso( $aplicacion, $servicioIng1[1] ); //tipo ingreso
				}
				if( $_POST[ 'esPreAdmicion' ] == "on" ){
					$datosEnc[ "ingfei" ] = date( "Y-m-d" );
					$datosEnc[ "inghin" ] = date( "H:i:s" );
				}else{
					//$datosEnc[ "ingfei" ] = date( "Y-m-d" );
					$datosEnc[ "ingfei" ] = $_POST[ 'ing_feitxtFecIng' ];
					//$datosEnc[ "inghin" ] = date( "H:i:s" );
					$datosEnc[ "inghin" ] = $_POST[ 'ing_hintxtHorIng' ];
				}


				//2014-03-10 El campo ingunx indica si el ingreso se guardó en unix
				//El campo consecutivoHistoriaDeUnix es true cuando la admision ya se guardo en unix
				$datosEnc[ "ingunx" ] = "off";
				if( $consecutivoHistoriaDeUnix == true )
					$datosEnc[ "ingunx" ] = "on";

				//SI ES ACCIDENTE DE TRANSITO, LOS CAMPOS LIGADOS A UN RESPONSABLE VAN EN BLANCO
				//2014-03-17
				if( !empty( $ing_caiselOriAte ) && $ing_caiselOriAte == '02' ){
					if(!isset($datosEnc) or $datosEnc == "" )
						$datosEnc = [];
					$datosEnc[ "inghoa" ] = "";
					$datosEnc[ "ingpco" ] = "";
					$datosEnc[ "ingcac" ] = "";
					$datosEnc[ "ingnpa" ] = "";
					$datosEnc[ "ingfha" ] = "";
					$datosEnc[ "ingpla" ] = "";
					$datosEnc[ "ingord" ] = "";
				}

				$sqlInsert = crearStringInsert( $wbasedato."_000101", $datosEnc );

				$resEnc = mysql_query( $sqlInsert, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() ) );

				if( $resEnc )
				{	//si inserto la 101
					$data[ "mensaje" ] = utf8_encode( "Se registró correctamente" );
				}
				else
				{
					$data[ "error" ] = 1;
					$data[ "mensaje" ] = utf8_encode( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() );
				}
			}
			else //hace la actualizacion
			{
				$estadoAdmision = "Modificada";
				
				//2017-10-23 seguir esta variable $cambioTemporalSaldoCero para revisar los puntos de cambio y retorno del arreglo de responsable.
				$cambioTemporalSaldoCero = false;
				$responsables205         = $responsables1;
				$queryCA = "SELECT count(*) cantidad FROM {$wbasedato}_000313
								WHERE Crahis = '{$historia}' AND Craing = '{$ingreso}'
				                AND Crarea = '{$responsables1[0]['ing_cemhidCodAse']}' AND Craest = 'on'";
				$rsCA    = mysql_query( $queryCA, $conex );

				while( $rowCA   = mysql_fetch_assoc( $rsCA )){

					if( $rowCA['cantidad']*1 > 0 ){//->2018-07-13
						//---> esto quiere decir que el programa de cargos hizo un cambio automàtico por consumo de saldo y que el principal
						//     responsable ya no es la empresa tipo soat
						$arrayAuxV        = array();
						$arrayAuxV        = $responsables1[0];
						$responsables1[0] = $responsables1[1];
						$responsables1[1] = $arrayAuxV;
						$cambioTemporalSaldoCero = true;
					}
				}

				$rowsEnc = mysql_fetch_array( $res );
				$remitidoAnterior = $rowsEnc[ 'ingvre' ]*1;

				//Si se encontraron datos, significa que es una actualización
				$datosTabla = crearArrayDatos( $wbasedato, "ing", "ing_", 3 );

				/*unset( $datosTabla[ "ingfei" ] );2016-09-08
				unset( $datosTabla[ "inghin" ] );*/

				$datosTabla[ 'id' ] = $rowsEnc[ 'id' ];
				$datosTabla[ "ingtar" ] = $tarifa; //tarifa
				$datosTabla[ "inghis" ] = $historia; //historia
				$datosTabla[ "ingnin" ] = $ingreso; //ingreso

				if ($aplicacion != "")
				{
					$servicioIng1=explode("-",$ing_seisel_serv_ing);
					$servicioIng=$servicioIng1[1];
					$datosTabla[ "ingsei" ] = $servicioIng; //servicio de ingreso
					$datosTabla[ "ingtin" ] = consultarTipoIngreso( $aplicacion, $servicioIng1[1] ); //tipo ingreso
				}
				//datos del primer responsable no accidente

				$cambiarCodigoEntidad = consultarAplicacion2( $conex, $wemp_pmla, "responsable101particular" );
				if($cambiarCodigoEntidad == "c" and $responsables1[0]['ing_tpaselTipRes'] == "P"){
					$datosTabla[ "ingcem" ] = consultarAplicacion2($conex,$wemp_pmla,"codigoempresaparticular");
				}else{
					$datosTabla[ "ingcem" ] = $responsables1[0]['ing_cemhidCodAse'];
				}


				$datosTabla[ "ingtpa" ] = $responsables1[0]['ing_tpaselTipRes'];
				$datosTabla[ "ingpla" ] = $responsables1[0]['ing_plaselPlan'];
				$datosTabla[ "ingpol" ] = $responsables1[0]['ing_poltxtNumPol'];
				$datosTabla[ "ingnco" ] = $responsables1[0]['ing_ncotxtNumCon'];
				$datosTabla[ "ingent" ] = $responsables1[0]['res_nom'];

				//SI ES ACCIDENTE DE TRANSITO, LOS CAMPOS LIGADOS A UN RESPONSABLE VAN EN BLANCO
				//2014-03-17
				if( !empty( $ing_caiselOriAte ) && $ing_caiselOriAte == '02' ){
					if( !isset($datosEnc) or $datosEnc == "")
						$datosEnc = [];

					$datosEnc[ "inghoa" ] = "";
					$datosEnc[ "ingpco" ] = "";
					$datosEnc[ "ingcac" ] = "";
					$datosEnc[ "ingnpa" ] = "";
					$datosEnc[ "ingfha" ] = "";
					$datosEnc[ "ingpla" ] = "";
					$datosEnc[ "ingord" ] = "";
				}
				//CONSULTA PARA VERIFICAR SI EL RESPONSABLE PRINCIPAL HA SIDO CAMBIADO AUTOMÀTICAMENTE POR EL PROGRAMA DE CARGOS, PARA OMITIR ESTE MOVIMIENTO.

				$datosTabla["ingunx"] = $rowsEnc['Ingunx'];//--> 2015-11-11 si ya se había guardado en unix, que cargue el dato para que en erp_unix se hagan actualizaciones y no inserciones.

				$sqlUpdate = crearStringUpdate( $wbasedato."_000101", $datosTabla );

				$res1 = mysql_query( $sqlUpdate, $conex ) or ( $data[ 'error' ] = utf8_encode( mysql_errno()." - Error en el query de ingreso $sqlUpdate - ".mysql_error() ) );

				if( $res1 )
				{
					$data[ "mensaje" ] = utf8_encode( "Se actualizo correctamente" );
				}
				else
				{
					$data[ "error" ] = 1;
					$data[ "mensaje" ] = utf8_encode( mysql_errno()." - Error en el query $sqlUpdate - ".mysql_error() );
				}
			}
		}
		/**fin ingreso**/

		if( $data[ "error" ] == 0 )
		{
			//2014-09-18
			//Si existe un registro con el mismo documento y diferente historia, se debe actualizar la historia por la actual.
			$sqldel = "DELETE FROM ".$wbasedato."_000100 WHERE Pacdoc = '".utf8_decode($documento)."'
							AND Pactdo = '".utf8_decode($tipodoc)."'
							AND Pachis != '".utf8_decode($historia)."' limit 1";

			$resdel = mysql_query( $sqldel, $conex );
			if( $resdel ){
				logAdmsiones( 'Registro borrado por documento duplicado', $historia, $tipodoc, $documento);
			}

			/**admision**/
			//Consulto si existe el registo
			$sql1 = "select Pachis,a.id, ingfei, ingvre, Pacdoc, Pactdo from ".$wbasedato."_000100 a,".$wbasedato."_000101 b
					where b.Inghis = '".utf8_decode($historia)."' and b.Ingnin = '".utf8_decode($ingreso)."' and b.Inghis =a.Pachis";

			$res1 = mysql_query( $sql1, $conex ) or ( $data[ 'error' ] = utf8_encode( mysql_errno()." - Error en el query $sql1 - ".mysql_error() ) );

			if( $res1 )
			{
				$num1 = mysql_num_rows( $res1 );
				
				//Si no se encontraron los datos, significa que es un registro nuevo
				if( $num1 == 0 ) //hace el insert
				{
					$tipoAtencion=$datosEnc[ "ingsei" ];//se llena porque en el siguiente renglon se sobre escribe
					$datosEnc = crearArrayDatos( $wbasedato, "pac", "pac_", 3 );
					unset( $datosEnc[ "pacfec" ] ); //elimina la posicion
					unset( $datosEnc[ "pacciu" ] ); //elimina la posicion
					$datosEnc[ "paciu" ]  = $pac_ciuhidMunNac;
					$datosEnc[ "pachis" ] = $historia;
					$datosEnc[ "pacact" ] = 'on'; //activo
					$datosEnc[ "pactat" ] = $tipoAtencion;//tipo atencion

					if ($aplicacion != "")
					{
						$servicioIng1=explode("-",$ing_seisel_serv_ing);
						$servicioIng=$servicioIng1[0];
						$datosEnc[ "pactat" ] = consultarTipoAtencion( $aplicacion, $servicioIng1[1] ); //tipo ingreso //tipo atencion
					}

					unset( $datosEnc[ "pacfec" ] ); //Se elimina la posicion por si la trajo
					$datosTabla = $datosEnc;
					$datosEnc['pacap1'] = strtoupper($datosEnc['pacap1']);
					$datosEnc['pacap2'] = strtoupper($datosEnc['pacap2']);
					$datosEnc['pacno1'] = strtoupper($datosEnc['pacno1']);
					$datosEnc['pacno2'] = strtoupper($datosEnc['pacno2']);
					
					if( $pacienteNN == "on"){
						$datosEnc['pacdoc'] = $documento;
					}
					if($datosRoot[ "paccac" ] == ''){
						unset( $datosRoot[ "paccac" ] );
					}
					$sqlInsert = crearStringInsert( $wbasedato."_000100", $datosEnc );
					$data1 = logAdmsiones( "Respuesta Autorizacion de datos[ Pacaud={$datosEnc['Pacaud']}]", $historia, $ingreso, $documento );

					$resEnc = mysql_query( $sqlInsert, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query admision $sqlInsert - ".mysql_error() ) );

					if( $resEnc )
					{	//si inserto
						$data[ "mensaje" ] = utf8_encode( "Se registró correctamente" );
						/********************************************************************************
						 * Agosto 15 de 2013
						 *
						 * Si se registra correctamente verifico que halla un registro en preadmisión
						 * Si hay un registro con el mismo documento en preadmisión, cancelo la preadmisión
						 * si la preadmisión es de la fecha actual
						 ********************************************************************************/
						$sql = "UPDATE ".$wbasedato."_000166 SET pacact = 'off'
								WHERE pactdo = '".$pac_tdoselTipoDoc."' AND pacdoc = '".$pac_doctxtNumDoc."'
									AND pacact = 'on' AND pacfec <= '".date( "Y-m-d" )."'";

						$resCancPre = mysql_query( $sql, $conex ) or die(  $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query admision $sql - ".mysql_error() )  );
						if( $resCancPre ){
							$preadmisionAnulada = true;
						}

						/********2014-10-17***********/
						/* Se actualiza la tabla cliame 000207, solicitado por Juan Carlos*/
						$sql = "UPDATE ".$wbasedato."_000207 SET mpahis = '".$historia."', mpaing='".$ingreso."'
									WHERE mpadoc = '".$pac_doctxtNumDoc."' AND mpaest = 'on' AND mpaliq = 'off'
								   		AND mpahis = '' AND mpaing = ''";

						$resup207 = mysql_query( $sql, $conex );
					}
					else
					{
						$data[ "error" ] = 1;
						$data[ "mensaje" ] = utf8_encode( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() );

						logAdmsiones( "Error en la 100 ", $historia, $ingreso, $documento );
					}
				}
				else //hace la actualizacion
				{
					$qaud  = " SELECT Pacaud, Pacact FROM {$wbasedato}_000100 WHERE pachis = '{$historia}'";
					$rsaud  = mysql_query( $qaud, $conex );
					$rowaud = mysql_fetch_assoc( $rsaud );

					$rowsEnc = mysql_fetch_array( $res1 );
					
					if($rowsEnc['Pactdo']!=utf8_decode($tipodoc))
					{
						$cambiaTipoDocumento = true;
					}
					
					$fechaIngreso = $rowsEnc['ingfei'];
					//Si se encontraron datos, significa que es una actualización
					$datosTabla = crearArrayDatos( $wbasedato, "pac", "pac_", 3 );

					unset( $datosTabla[ "pacfec" ] ); //elimina la posicion
					unset( $datosTabla[ "pacciu" ] ); //elimina la posicion
					$datosTabla[ "paciu" ] = $pac_ciuhidMunNac;
					$datosTabla[ 'id' ] = $rowsEnc[ 'id' ];

					if( $modoConsulta == "true" ){//--> 2016-12-27 solo debe poner un activo seguro si y solo si es un insert nuevo
						$datosTabla[ "pacact" ] = $rowaud['Pacact']; //que conserve el estado del paciente
					}else{
						$datosTabla[ "pacact" ] = 'on'; //Como se hace un ingreso siempre se deja activo
					}

					unset( $datosTabla[ "pacfec" ] ); //Se elimina la posicion por si la trajo
					if ($aplicacion != "")
					{
						$servicioIng1=explode("-",$ing_seisel_serv_ing);
						$servicioIng=$servicioIng1[0];
						$datosTabla[ "pactat" ] = consultarTipoAtencion( $aplicacion, $servicioIng1[1] ); //tipo ingreso //tipo atencion
					}
					$datosTabla['pacap1'] = strtoupper($datosTabla['pacap1']);
					$datosTabla['pacap2'] = strtoupper($datosTabla['pacap2']);
					$datosTabla['pacno1'] = strtoupper($datosTabla['pacno1']);
					$datosTabla['pacno2'] = strtoupper($datosTabla['pacno2']);
					if($datosTabla['paccac'] == ''){
						unset( $datosTabla[ "paccac" ] );
					}
					$sqlUpdate = crearStringUpdate( $wbasedato."_000100", $datosTabla );
					$res2 = mysql_query( $sqlUpdate, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query $sqlUpdate - ".mysql_error() ) );

					if( $rowaud['Pacaud'] != $datosTabla['pacaud'] )
						$data1 = logAdmsiones( "Actualizacion Autorizacion de datos[ Pacaud={$datosTabla['pacaud']}]", $historia, $ingreso, $documento );

					if( $res2 )
					{
							/********************************************************************************
							 * Agosto 15 de 2013
							 *
							 * Si se registra correctamente verifico que halla un registro en preadmisión
							 * Si hay un registro con el mismo documento en preadmisión, cancelo la preadmisión
							 * si la preadmisión es de la fecha actual
							 ********************************************************************************/
							$sql = "UPDATE
										".$wbasedato."_000166
									SET
										pacact = 'off'
									WHERE
										pacdoc = '".$pac_doctxtNumDoc."'
										AND pacact = 'on'
										AND pacfec <= '".date( "Y-m-d" )."'
									";

							$resCancPre = mysql_query( $sql, $conex ) or die(  $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query admision $sql - ".mysql_error() )  );

							if( !$resCancPre ){
								$data[ "error" ] = 1;
							}
							else{
								$preadmisionAnulada = true;
							}
							/********************************************************************************/

						/********2014-10-17***********/
						/* Se actualiza la tabla cliame 000207, solicitado por Juan Carlos*/
						$sql = "UPDATE ".$wbasedato."_000207
								   SET mpahis = '".$historia."', mpaing='".$ingreso."'
								 WHERE mpadoc = '".$pac_doctxtNumDoc."'
								   AND mpaest = 'on'
								   AND mpaliq = 'off'
								   AND mpahis = ''
								   AND mpaing = ''";

						$resup207 = mysql_query( $sql, $conex );
					}
					else
					{
						$data[ "error" ] = 1;
					}
				}
			}

			$sqlRoot = "select id from  root_000036
					where Pactid='".utf8_decode($tipodoc)."' and Pacced='".utf8_decode($documento)."'";
			$resRoot = mysql_query( $sqlRoot, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla unica de pacientes ".mysql_errno()." - Error en el query $sql5 - ".mysql_error() ) );
			if( $resRoot ){
				$num1 = mysql_num_rows( $resRoot );
				if( $num1 == 0 ) //hace el insert
				{
					$datosRoot = crearArrayDatos( $wbasedato, "pac", "pac_", 3 ); 
					unset( $datosRoot[ "pacfec" ] ); //elimina la posicion
					unset( $datosRoot[ "pacciu" ] ); //elimina la posicion
					$datosRoot['paciu']  = $pac_ciuhidMunNac;
					$datosRoot['Pacced'] = $datosRoot[ "pacdoc" ];
					unset( $datosRoot[ "pacdoc" ] ); //elimina la posicion
					$datosRoot['Pactid'] = $datosRoot['pactdo'];
					unset( $datosRoot[ "pactdo" ] ); //elimina la posicion
					$datosRoot['Pacnac'] = $datosRoot['pacfna'];
					unset( $datosRoot[ "pacfna" ] ); //elimina la posicion
					$datosRoot['pacap1'] = strtoupper($datosRoot['pacap1']);
					$datosRoot['pacap2'] = strtoupper($datosRoot['pacap2']);
					$datosRoot['pacno1'] = strtoupper($datosRoot['pacno1']);
					$datosRoot['pacno2'] = strtoupper($datosRoot['pacno2']);
					unset( $datosRoot[ "pachis" ] );
					unset( $datosRoot[ "pacing" ] );
					unset( $datosRoot[ "pactat" ] );
					unset( $datosRoot[ "pacact" ] );
					unset( $datosRoot[ "paccea" ] );
					unset( $datosRoot[ "pacnoa" ] );
					unset( $datosRoot[ "pactea" ] );
					unset( $datosRoot[ "pacdia" ] );
					unset( $datosRoot[ "pacpaa" ] );
					unset( $datosRoot[ "paccru" ] );
					unset( $datosRoot[ "pacnru" ] );
					unset( $datosRoot[ "pactru" ] );
					unset( $datosRoot[ "pacdru" ] );
					unset( $datosRoot[ "pacpru" ] );
					unset( $datosRoot[ "pactam" ] );
					unset( $datosRoot[ "pactda" ] );
					unset( $datosRoot[ "pacddr" ] );
					unset( $datosRoot[ "pacdre" ] );
					unset( $datosRoot[ "pacmre" ] );
					unset( $datosRoot[ "pacmor" ] );
					unset( $datosRoot[ "paccre" ] );
					if($datosRoot[ "paccac" ] == ''){
						unset( $datosRoot[ "paccac" ] );
					}

					$sqlInsertRoot = crearStringInsert( 'root_000036', $datosRoot );
					$resEncRoot = mysql_query( $sqlInsertRoot, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query admision $sqlInsertRoot - ".mysql_error() ) );
				}
				else //hace la actualizacion
				{
					$rowsRoot = mysql_fetch_array( $resRoot );
					$datosRoot = crearArrayDatos( $wbasedato, "pac", "pac_", 3 ); 
					unset( $datosRoot[ "pacfec" ] ); //elimina la posicion
					unset( $datosRoot[ "pacciu" ] ); //elimina la posicion
					$datosRoot['paciu']  = $pac_ciuhidMunNac;
					$datosRoot['pacap1'] = strtoupper($datosRoot['pacap1']);
					$datosRoot['pacap2'] = strtoupper($datosRoot['pacap2']);
					$datosRoot['pacno1'] = strtoupper($datosRoot['pacno1']);
					$datosRoot['pacno2'] = strtoupper($datosRoot['pacno2']);
					$datosRoot['Pacced'] = $datosRoot[ "pacdoc" ];
					unset( $datosRoot[ "pacdoc" ] ); //elimina la posicion
					$datosRoot['Pactid'] = $datosRoot['pactdo'];
					unset( $datosRoot[ "pactdo" ] ); //elimina la posicion
					$datosRoot['Pacnac'] = $datosRoot['pacfna'];
					unset( $datosRoot[ "pacfna" ] ); //elimina la posicion
					$datosRoot[ 'id' ] = $rowsRoot[ 'id' ];
					unset( $datosRoot[ "pachis" ] );
					unset( $datosRoot[ "pacing" ] );
					unset( $datosRoot[ "pactat" ] );
					unset( $datosRoot[ "pacact" ] );
					unset( $datosRoot[ "paccea" ] );
					unset( $datosRoot[ "pacnoa" ] );
					unset( $datosRoot[ "pactea" ] );
					unset( $datosRoot[ "pacdia" ] );
					unset( $datosRoot[ "pacpaa" ] );
					unset( $datosRoot[ "paccru" ] );
					unset( $datosRoot[ "pacnru" ] );
					unset( $datosRoot[ "pactru" ] );
					unset( $datosRoot[ "pacdru" ] );
					unset( $datosRoot[ "pacpru" ] );
					unset( $datosRoot[ "pactam" ] );
					unset( $datosRoot[ "pactda" ] );
					unset( $datosRoot[ "pacddr" ] );
					unset( $datosRoot[ "pacdre" ] );
					unset( $datosRoot[ "pacmre" ] );
					unset( $datosRoot[ "pacmor" ] );
					unset( $datosRoot[ "paccre" ] );
					if($datosRoot[ "paccac" ] == ''){
						unset( $datosRoot[ "paccac" ] );
					}
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");

					$datosRoot[ "Fecha_data" ] = $fecha;
					$datosRoot[ "Hora_data" ] = $fecha;

					$prohibidos[ "Medico" ] = true;
					$prohibidos[ "Seguridad" ] = true;
					$prohibidos[ "id" ] = true;

					foreach( $datosRoot as $keyDatos => $valueDatos ){

						if( !isset( $prohibidos[ $keyDatos ] ) ){
							$stPartInsert .= ",$keyDatos = '$valueDatos' ";
						}
					}
					$stPartInsert = "UPDATE root_000036 SET ".substr( $stPartInsert, 1 );
					$stPartValues = " WHERE id = '{$datosRoot[ 'id' ]}'";

					$sqlUpdate = $stPartInsert.$stPartValues;
					$res2 = mysql_query( $sqlUpdate, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query $sqlUpdate - ".mysql_error() ) );
				}
			}
		}
		/**fin admision**/

		//validaciones de topes y responsables
		if( $data[ "error" ] == 0 )
		{
			/******************************************************************************************/
			/* SE CONSULTA LA INFORMACION DE LOS RESPONSABLES Y LOS TOPES CUYO VALOR DEL TOPE ES DIFERENTE AL SALDO */
			/* ES DECIR, LOS TOPES QUE HAN SIDO UTILIZADOS*/
			$arr_responsables_no_tocar = array(); //Codigos de los responsables que no se pueden modificar en la bd
			$arr_orden_res_no_tocar = array(); //El orden de la bd de los responsables que no se pueden modificar

			$arr_topes_responsables = array();

			//Query que me trae responsable-tope cuyo saldo ha sido utilizado DIFERENTE A SOAT
			$sqlpro = "SELECT Resnit as codigo, Resord as orden, Toptco as tipo, Toptop as tope, Topsal as saldo
						 FROM ".$wbasedato."_000205, ".$wbasedato."_000204
						WHERE Reshis = '".$historia."'
						  AND Resing = '".$ingreso."'
						  AND Tophis = Reshis
						  AND Toping = Resing
						  AND Topres = Resnit
						  AND Toptop != Topsal
						  AND Toptco != '*'
						  AND Topcla != '*'
						  AND Toprec != '*'
						  AND Resest = 'on'
						  AND Topest = 'on'
						  AND Resdes = 'off'";

			$respro = mysql_query( $sqlpro, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla ".$wbasedato."000205 ".mysql_errno()." - Error en el query $sqlpro - ".mysql_error() ) );
			if ($respro){
				$numpro=mysql_num_rows($respro);
				if ($numpro>0){
					while( $rowspro = mysql_fetch_assoc($respro) ){
						if( isset( $arr_topes_responsables[$rowspro['codigo']] ) == false )
							$arr_topes_responsables[$rowspro['codigo']] = array();
						array_push( $arr_topes_responsables[$rowspro['codigo']], $rowspro );
					}
				}
			}

			//Se crea un arreglo con los nuevos responsables y nuevos topes
			$arr_topes_nuevos = array();

			if( $cambioTemporalSaldoCero ){//-->2018-07-13
				$arrayAuxV        = $responsables1[0];
				$responsables1[0] = $responsables1[1];
				$responsables1[1] = $arrayAuxV;
				$responsablesReal = $responsables205;
			}
			$responsablesReal = $responsables1;

			if ((!empty ($responsablesReal) && count($responsablesReal)>0) && (!empty ($topes) && count($topes)>0))
			{
				foreach( $responsablesReal as $keRes => $valueRes )
				{
					foreach( $topes as $keRes1 => $valueRes1 )
					{
						if( $valueRes1['top_reshidTopRes'] == $valueRes['ing_cemhidCodAse'] ){
							$row1 = array();
							$row1['codigo'] = $valueRes['ing_cemhidCodAse'];
							$row1['tipo'] = $valueRes1['top_tcoselTipCon'];
							$row1['tope'] = $valueRes1['top_toptxtValTop'];
							$row1['saldo'] = $valueRes1['top_toptxtValTop'];
							$row1['error'] = false; //Se puede guardar sin problemas
							if( $row1['tope'] != '' && $valueRes1['top_toptxtValTop'] != '' ){
								if( !isset($arr_topes_nuevos[ $valueRes['ing_cemhidCodAse'] ]))
									$arr_topes_nuevos[ $valueRes['ing_cemhidCodAse'] ] = array();
								array_push( $arr_topes_nuevos[ $valueRes['ing_cemhidCodAse'] ], $row1 );
							}
						}
					}
				}
			}
			//echo "<br>Topes Nuevo".json_encode($arr_topes_nuevos);

			/*	se comprueba si permite modificar los responsables y los topes	*/
			if( count($arr_topes_responsables) > 0 ){ //Ya existian responsables y topes
				if( count($arr_topes_nuevos) > 0 ){ //hay nuevos responsables con topes
					foreach( $arr_topes_responsables as &$arrTopeViejo ) //por cada responsable-tope existente
					{
						foreach($arrTopeViejo as &$topeViejo){
							//Si el responsable "nuevo" existia, se comprueba el valor del tope
							if( isset( $arr_topes_nuevos[$topeViejo['codigo']] ) == true ){
								foreach( $arr_topes_nuevos[$topeViejo['codigo']] as &$topeNew ){
									if( $topeViejo['tipo'] == $topeNew['tipo'] ){
										//Si el valor del tope nuevo es menor o igual al valor del tope viejo, no se puede modificar
										if( floatval($topeNew['tope']) <= floatval($topeViejo['tope']) ){
											$topeNew['error'] = true;
											$data[ 'mensaje' ] = "Existen topes cuyo saldo ha sido utilizado, por lo que no se pueden modificar.";
											array_push($arr_responsables_no_tocar, $topeViejo['codigo'] );
											array_push($arr_orden_res_no_tocar, $topeViejo['orden'] );
										}else{
											$topeNew['saldo'] = $topeNew['tope'] - $topeViejo['tope'] + $topeViejo['saldo'];
											$data[ 'mensaje' ] = "Se ha actualizado el saldo para el tope del responsable ".$topeViejo['codigo'];
										}
									}
								}
							}else{
								$data[ 'mensaje' ] = "Existen topes de responsables cuyo saldo ha sido utilizado, por lo que no se pueden eliminar.";
								array_push($arr_responsables_no_tocar, $topeViejo['codigo'] );
								array_push($arr_orden_res_no_tocar, $topeViejo['orden'] );
							}
						}
					}
				}else{ //Si no hay responsables hay un error
					$data[ "error" ] = 1;
					$data[ 'mensaje' ] = "Existen topes cuyo saldo ha sido utilizado, por lo que no se pueden eliminar.";
				}
			}
			/* fin se comprueba si permite modificar los responsables y los topes	*/
			/* FIN SE CONSULTA LA INFORMACION DE LOS RESPONSABLES Y LOS TOPES  */
			/******************************************************************************************/
			/******************************************************************************************/
		}

		/****guardar accidentes o eventos catastroficos***/
		if( $data[ "error" ] == 0 )
		{
			/**Se guarda accidentes de transito**/
			if( !empty( $ing_caiselOriAte ) && $ing_caiselOriAte == '02' ){
				$data1 = guardarAccidentes( $historia, $ingreso );

				if( $data1['error'] == 1 )
				{
					$data = $data1;
				}
			}
			else{
				$sql = "UPDATE {$wbasedato}_000148
						   SET Accest='off'
						 WHERE Acchis = '$historia'
						   AND Accing = '$ingreso'";

				$resCancPre = mysql_query( $sql, $conex ) or die(  $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query admision $sql - ".mysql_error() )  );
			}

			/**Se guarda eventos catastróficos**/
			if( !empty( $ing_caiselOriAte ) && $ing_caiselOriAte == '06' ){

				if ($relEvento == 'off')
				{
					$data1 = guardarEventos( $historia, $ingreso );
					$data = $data1;
				}
				else
				{
					$data1 = guardarRelacion($historia, $ingreso, $hidcodEvento);
					$data = $data1;
				}
			}
			else{
				$sqlUptEvc = "UPDATE {$wbasedato}_000150
							     SET Evnest = 'off'
							   WHERE Evnhis = '$historia'
								 AND Evning = '$ingreso'";

				$resUptEvc = mysql_query( $sqlUptEvc, $conex ) or die( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query $sqlUptEvc - ".mysql_error() )  );
			}
		}
		/**** fin guardar accidentes o eventos catastroficos***/

		/******************* Guardar en la tabla de responsables 000205****************/
		if( $data[ "error" ] == 0 )
		{
			//-->nueva funcion para actualización de responsables
			if( true ){

				//if( !empty( $responsables1 ) && count( $responsables1 ) > 0 ){
				if( !empty( $responsablesReal ) && count( $responsablesReal ) > 0 ){

					$responsablesNuevos              = array();
					$responsablesActuales            = array();
					$responsablesActualesOrd         = array();
					$responsablesActualesDescartados = array();
					$responsablesDescartados         = array();
					$responsablesActualizar          = array();

					//foreach( $responsables1 as $keRes => $valueRes ){
					foreach( $responsablesReal as $keRes => $valueRes ){
						array_push( $responsablesNuevos, $valueRes['ing_cemhidCodAse'] );
					}

					$query = " SELECT resnit, id*1 as id, resdes, resord*1 resord
						 		 FROM {$wbasedato}_000205
						 		WHERE Reshis = '$historia'
						 		  AND Resing = '$ingreso'
						 		  AND Resest = 'on'
						 		ORDER BY 3 asc ";

					$rsra  = mysql_query( $query, $conex );
					while( $rowra = mysql_fetch_array( $rsra ) ){
						if( $rowra['resdes'] == "off" ){
							$responsablesActuales[$rowra['resnit']] = array('codigoResponsable'=>$rowra['resnit'], 'id'=>$rowra['id'], 'ordenResponsabilidad'=>$rowra['resord']);
							$responsablesActualesOrd[$rowra['resord']] = $rowra['resnit'];
						}else if( $rowra['resdes'] == "on" ){
							$responsablesActualesDescartados[$rowra['resnit']] = array('codigoResponsable'=>$rowra['resnit'], 'id'=>$rowra['id'], 'ordenResponsabilidad'=>$rowra['resord']);
						}
					}

					foreach( $responsablesActuales as $keyResA => $datosResponsableActual ){
						if(  ( in_array( $datosResponsableActual['codigoResponsable'], $responsablesNuevos ) )  ){
							//--> si entra acá quiere decir que el responsable almacenado actualmente sigue existiendo entre los responsables del paciente
							array_push( $responsablesActualizar, $datosResponsableActual['codigoResponsable'] );

						}else{
							//--> el responsable almacenado ya no se encuentra entre los responsables del paciente, así que debe descartarse.
							array_push( $responsablesDescartados, $datosResponsableActual['codigoResponsable'] );

						}
					}

					foreach( $responsablesDescartados as $keyResD => $datosResDes ){

						$queryDes = " UPDATE {$wbasedato}_000205
							             SET resdes = 'on'
							           WHERE reshis = '$historia'
							             AND resing = '$ingreso'
							             AND resnit = '{$responsablesDescartados[$keyResD]}'";

						$rsDes    = mysql_query( $queryDes, $conex );
					}

					foreach( $responsablesReal as $keRes => $valueRes ){


						unset( $datosEnc ); //se borra el array
						//se guardan todos los responsables
						$datosEnc[ "Medico" ] = $wbasedato;
						$datosEnc[ "Fecha_data" ] = date("Y-m-d");
						$datosEnc[ "Hora_data" ] = date( "H:i:s" );
						$datosEnc[ "reshis" ] = $historia;
						$datosEnc[ "resing" ] = $ingreso;
						$datosEnc[ "restdo" ] = $valueRes['res_tdo'];
						$datosEnc[ "resnit" ] = $valueRes['ing_cemhidCodAse'];
						$datosEnc[ "resnom" ] = $valueRes['res_nom'];
						$datosEnc[ "resord" ] = $keRes*1+1; //orden del responsable el tr en el que esta
						if( $valueRes['res_fir'] == "" ){
							if ($ordenRes == 1){ //solo en el primer responsble se envia la fecha inicial
								$datosEnc[ "resfir" ] = date("Y-m-d");
							}else{
								$datosEnc[ "resfir" ] = '';
							}
						}else{
							$datosEnc[ "resfir" ] = $valueRes['res_fir'];
						}
						$ordenRes++;
						$datosEnc[ "resest" ] =	'on';
						$datosEnc[ "restpa" ] =	$valueRes['ing_tpaselTipRes'];
						$datosEnc[ "respla" ] =	$valueRes['ing_plaselPlan'];
						$datosEnc[ "respol" ] =	$valueRes['ing_poltxtNumPol'];
						$datosEnc[ "resnco" ] =	$valueRes['ing_ncotxtNumCon'];
						$datosEnc[ "resfir" ] =	$valueRes['res_firtxtNumcon'];
						$datosEnc[ "resffr" ] =	$valueRes['res_ffrtxtNumcon'];
						$datosEnc[ "rescom" ] =	$valueRes['res_comtxtNumcon'];
						$datosEnc[ "resaut" ] =	$valueRes['ing_ordtxtNumAut'];
						$datosEnc[ "resfha" ] =	$valueRes['ing_fhatxtFecAut'];
						$datosEnc[ "reshoa" ] =	$valueRes['ing_hoatxtHorAut'];
						$datosEnc[ "resnpa" ] =	$valueRes['ing_npatxtNomPerAut'];
						$datosEnc[ "respco" ] =	$valueRes['ing_pcoselPagCom'];
						$datosEnc[ "Seguridad" ] =	"C-".$wbasedato;

						if( $datosEnc[ "resord" ]*1 == 1 and ($datosEnc[ "resfir" ] == "0000-00-00" or $datosEnc[ "resfir" ] == "") ){
							$datosEnc[ "resfir" ] = $fechaIngreso;
						}

						if( array_key_exists( $valueRes['ing_cemhidCodAse'], $responsablesActualesDescartados ) ){//--> si estaba descartado y se vuelve a activar.

							$datosEnc[ "resdes" ] =	"off";
							$datosEnc[ "id" ] =	$responsablesActualesDescartados[$valueRes['ing_cemhidCodAse']]['id'];
							$sqlDel="DELETE FROM ".$wbasedato."_000209
									  WHERE Cprhis = '".$historia."'
										AND Cpring = '".$ingreso."'
										AND Cprnit = '".$valueRes['ing_cemhidCodAse']."'";
							$resDel = mysql_query( $sqlDel, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query $sqlDel - ".mysql_error() ) );

							$sqlInsert = crearStringUpdate( $wbasedato."_000205", $datosEnc );

						}else if( in_array( $valueRes['ing_cemhidCodAse'], $responsablesActualizar ) ){

							$datosEnc[ "id" ] =	$responsablesActuales[$valueRes['ing_cemhidCodAse']]['id'];
							$sqlInsert = crearStringUpdate( $wbasedato."_000205", $datosEnc );

						}else{

							$sqlInsert = crearStringInsert( $wbasedato."_000205", $datosEnc );

						}

						$resEnc = mysql_query( $sqlInsert, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error grabando en la tabla ".$wbasedato."_000205 - ".mysql_error() ) );

						if (!$resEnc){
							$data['error']=1;
						}else{ //20140221 -------------------> nueva función de cups

							if( $datosEnc[ "resord" ]*1 == 1 ){
								$sqlDel="DELETE FROM ".$wbasedato."_000209
										  WHERE Cprnit = '".$valueRes['ing_cemhidCodAse']."'
											AND Cpraut = '".$valueRes['ing_ordtxtNumAut']."' ";
								$sqlDel="DELETE FROM ".$wbasedato."_000209
										  WHERE Cprhis = '".$historia."'
											AND Cpring = '".$ingreso."' ";
								$resDel = mysql_query( $sqlDel, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query $sqlDel - ".mysql_error() ) );
								}

								//Insertar en la tabla de cups
								if( isset($valueRes['cups']) ){
									foreach( $valueRes['cups'] as $valueCup )
									{
										 unset( $datosEnc ); //se borra el array
										//se guardan todos los responsables

										$datosEnc[ "Cprhis" ] = $historia;
										$datosEnc[ "Cpring" ] = $ingreso;

										$datosEnc[ "Medico" ] = $wbasedato;
										$datosEnc[ "Fecha_data" ] = date("Y-m-d");
										$datosEnc[ "Hora_data" ] = date( "H:i:s" );
										$datosEnc[ "Cprnit" ] =	$valueRes['ing_cemhidCodAse'];
										$datosEnc[ "Cpraut" ] =	$valueRes['ing_ordtxtNumAut'];
										$datosEnc[ "Cprcup" ] =	$valueCup;
										$datosEnc[ "Cprest" ] =	'on';
										$datosEnc[ "Seguridad" ] =	"C-".$wbasedato;
										$sqlInsert = crearStringInsert( $wbasedato."_000209", $datosEnc );
										$resEnc = mysql_query( $sqlInsert, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error grabando en la tabla ".$wbasedato."_000209 - ".mysql_error() ) );
									}
								}
							}

							if( $data[ "error" ] == 0 ){ //2014-07-23
								//DESACTIVANDO LOS RESPONSABLES, TOPES Y  CUPS DE PREADMISION
								$sql = "UPDATE ".$wbasedato."_000215
										   SET	Topest = 'off'
										 WHERE Toptdo = '".$pac_tdoselTipoDoc."'
										   AND Topdoc = '".$pac_doctxtNumDoc."'
											";
								$resCancPre = mysql_query( $sql, $conex ) or die(  $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query admision $sql - ".mysql_error() )  );

								$sql = "UPDATE ".$wbasedato."_000216
										   SET	Resest = 'off'
										 WHERE Restdo = '".$pac_tdoselTipoDoc."'
										   AND Resdoc = '".$pac_doctxtNumDoc."'
											";
								$resCancPre = mysql_query( $sql, $conex ) or die(  $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query admision $sql - ".mysql_error() )  );

								$sql = "UPDATE ".$wbasedato."_000217,".$wbasedato."_000167
										   SET	Cprest = 'off'
										 WHERE Ingtdo = '".$pac_tdoselTipoDoc."'
										   AND Ingdoc = '".$pac_doctxtNumDoc."'
										   AND Cprnit = Ingcem
										   AND Cpraut = Ingord
											";
								$sql = "UPDATE ".$wbasedato."_000217
										   SET Cprest = 'off'
										 WHERE Cprtdo = '".$pac_tdoselTipoDoc."'
										   AND Cprdoc = '".$pac_doctxtNumDoc."'
											";
								$resCancPre = mysql_query( $sql, $conex ) or die(  $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query admision $sql - ".mysql_error() )  );
							}

						}//--> fin foreach responsables

						if( $data[ "error" ] == 0 ){ //2014-07-23
							//DESACTIVANDO LOS RESPONSABLES, TOPES Y  CUPS DE PREADMISION
							$sql = "UPDATE ".$wbasedato."_000215
									   SET	Topest = 'off'
									 WHERE Toptdo = '".$pac_tdoselTipoDoc."'
									   AND Topdoc = '".$pac_doctxtNumDoc."'
										";
							$resCancPre = mysql_query( $sql, $conex ) or die(  $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query admision $sql - ".mysql_error() )  );

							$sql = "UPDATE ".$wbasedato."_000216
									   SET	Resest = 'off'
									 WHERE Restdo = '".$pac_tdoselTipoDoc."'
									   AND Resdoc = '".$pac_doctxtNumDoc."'
										";
							$resCancPre = mysql_query( $sql, $conex ) or die(  $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query admision $sql - ".mysql_error() )  );

							$sql = "UPDATE ".$wbasedato."_000217,".$wbasedato."_000167
									   SET	Cprest = 'off'
									 WHERE Ingtdo = '".$pac_tdoselTipoDoc."'
									   AND Ingdoc = '".$pac_doctxtNumDoc."'
									   AND Cprnit = Ingcem
									   AND Cpraut = Ingord
										";
							$sql = "UPDATE ".$wbasedato."_000217
									   SET Cprest = 'off'
									 WHERE Cprtdo = '".$pac_tdoselTipoDoc."'
									   AND Cprdoc = '".$pac_doctxtNumDoc."'
										";
							$resCancPre = mysql_query( $sql, $conex ) or die(  $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query admision $sql - ".mysql_error() )  );
						}
				}
			}


		}
		/******************* Fin Guardar en la tabla de responsables 000205************/


		/*******************Para guardar el tope de soat*******************/

		if( $data[ "error" ] == 0 )
		{
			if( !empty( $ing_caiselOriAte ) && $ing_caiselOriAte == '02' ){
				//se consulta el tope de accidente de transito para el año en curso
				$idSoat = "";//--> variable que me dirá si hay que hacer insert o update, y si es el segundo caso, me dirá cual registro actualizar
				$fechaAccidente = $_POST['dat_Accfec_ux_accfec'];//-->2018-01-17 si falla cambiar la variable abajo por date('y-m-d');
				$sqlTopeSoat = "  SELECT Toptpr
									FROM ".$wbasedato."_000194
									WHERE Topfin <= '".$fechaAccidente."'
									AND Topffi >= '".$fechaAccidente."'";
				$resTopeSoat = mysql_query( $sqlTopeSoat, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error consultando en la tabla ".$wbasedato."_000194 topes soat- ".mysql_error() ) );

				if (!$resTopeSoat){
					$data['error']=1;
				}else{
					$numTopeSoat=mysql_num_rows($resTopeSoat);
					if ($numTopeSoat > 0){
						$rowsTopeSoat=mysql_fetch_array($resTopeSoat);
						$valorTope=$rowsTopeSoat['Toptpr'];
					}
				}
				//se guarda el tope de accidente de transito
				unset( $datosEnc ); //se borra el array

				$datosEnc[ "Medico" ] = $wbasedato;
				$datosEnc[ "Fecha_data" ] = date("Y-m-d");
				$datosEnc[ "Hora_data" ] = date( "H:i:s" );
				$datosEnc[ "tophis" ] = $historia;
				$datosEnc[ "toping" ] = $ingreso;
				$datosEnc[ "topres" ] = $_POST['dat_AccreshidCodRes24'];
				$datosEnc[ "toptco" ] = '*';
				$datosEnc[ "topcla" ] = '*';
				$datosEnc[ "topcco" ] =	'*';
				$datosEnc[ "toptop" ] =	$valorTope;
				$datosEnc[ "toprec" ] =	'*';
				$datosEnc[ "topdia" ] =	'off';

				//Si el responsable soat existia en el arreglo, no se puede actualizar el saldo por el total, SE DEJA EL QUE ESTABA
				$saldo_g = $valorTope;
				if( isset($arr_topes_nuevos[$_POST['dat_AccreshidCodRes24']]) == true ){
					//Se busca si al tope se le cambio el saldo, para que no lo ponga como total otra vez
					foreach( $arr_topes_nuevos[$_POST['dat_AccreshidCodRes24']] as $topeNewx ){
						if( $topeNewx['tipo'] == '*' ){
							$saldo_g = $topeNewx['saldo'];
							break;
						}
					}
				}

				$datosEnc[ "topsal" ] =	$saldo_g;
				$datosEnc[ "topest" ] =	'on';
				$datosEnc[ "Seguridad" ] =	"C-".$wbasedato;

				//Se consulta el saldo para no cambiarlo
				$sqlpro = "SELECT Topsal, id, topres
							 FROM ".$wbasedato."_000204
							WHERE tophis = '".$historia."'
							  AND toping = '".$ingreso."'
							  AND toprec = '*'
							  AND topres = '{$_POST['dat_AccreshidCodRes24']}'"; //sea de soat
				$res3 = mysql_query( $sqlpro, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando el ingreso " .mysql_errno()." - Error en el query $sql2 - ".mysql_error() ) );
				if( $res3 )
				{
					$num3=mysql_num_rows($res3);
					if ($num3>0){
						$rowto=mysql_fetch_array($res3);
						$datosEnc[ "topsal" ] = $rowto[0];
						$idSoat = $rowto[1];
					}else if( isset($accidente_previo) && $accidente_previo != "" ){ //es un reingreso de accidente
						//Se consulta el saldo del accidente ORIGEN
						$sqlpro = "SELECT Topsal, Toptop
								 FROM ".$wbasedato."_000204
								WHERE tophis = '".$historia."'
								  AND toping = '".$accidente_previo."'
								  AND toprec = '*'"; //sea de soat
						$res39 = mysql_query( $sqlpro, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando el ingreso " .mysql_errno()." - Error en el query $sql2 - ".mysql_error() ) );
						if( $res39 )
						{
							$num39=mysql_num_rows($res39);
							if ($num39>0){
								$rowto9=mysql_fetch_array($res39);
								$datosEnc[ "topsal" ] = $rowto9[0];
								$datosEnc[ "toptop" ] = $rowto9[1];
							}
						}
					}
				}


				$sqlDel="delete from ".$wbasedato."_000204
						 where tophis = '".$historia."'
						 and toping = '".$ingreso."'
						 and toprec = '*'"; //sea de soat

				if( is_nan( $POST['ing_vretxtValRem'] ) ){
					$POST['ing_vretxtValRem'] = 0;

				}
				if(trim($_POST['ing_vretxtValRem'])== "")
						$_POST['ing_vretxtValRem'] = 0;

				if( $idSoat == "" ){
					//--> agregar fragmento de código que tiene en cuenta el valor remitido
					$datosEnc[ "topsal" ] = $datosEnc[ "topsal" ]*1 - $_POST['ing_vretxtValRem']*1;
					$sqlInsertSOAT = crearStringInsert( $wbasedato."_000204", $datosEnc );
				}else{

					//---> sumar el valor remetido guardado anterior y restar el nuevo, para que concuerden los datos.
					$datosEnc['id'] = $idSoat;
					$datosEnc["topsal"] = $datosEnc["topsal"]*1 + $remitidoAnterior - $_POST['ing_vretxtValRem']*1;
					$sqlInsertSOAT = crearStringUpdate( $wbasedato."_000204", $datosEnc );
				}
				$resEncSOAT = mysql_query( $sqlInsertSOAT, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error grabando en la tabla ".$wbasedato."_000204 - ".mysql_error() ) );

				if (!$resEncSOAT){
					$data['error']=1;
				}
			}
		}
		/*******************Fin para guardar el tope de soat***************/

		/******************* Guardar en la tabla de topes 000204****************/
		if(isset($topesPreadmision) && $topesPreadmision)
		{
			global $user;

			$usuario = explode("-", $user);
			$usuario = $usuario[1];

			if( $data[ "error" ] == 0 )
			{
				$empresasConTopes = array();
				$queryEmpTop = " SELECT DISTINCT(topres)
								   FROM {$wbasedato}_000204
								  WHERE tophis = '{$historia}'
									AND toping = '{$ingreso}'
									AND toprec != '*'";
				$rsemptop = mysql_query( $queryEmpTop, $conex );
				while( $rowemptop = mysql_fetch_array( $rsemptop ) ){
					$empresasConTopes[$rowemptop[0]] = "'".$rowemptop[0]."'";
				}
				if( !empty($topes) && count($topes)>0 ){

					//para pasar de tope en tope
					$responsablesTopes = array();

					foreach( $topes as $keRes => $valueRes )
					{
						/*if( in_array($valueRes['top_reshidTopRes'], $arr_responsables_no_tocar )){//2017-01-26
							continue;
						}*/

						$saldo_g = $valueRes['top_toptxtValTop'];
						if( isset($arr_topes_nuevos[$valueRes['top_reshidTopRes']]) == true ){
							//Se busca si al tope se le cambio el saldo, para que no lo ponga como total otra vez
							foreach( $arr_topes_nuevos[$valueRes['top_reshidTopRes']] as &$topeNew ){
								if( $valueRes['top_tcoselTipCon'] == $topeNew['tipo'] ){
									$saldo_g = $topeNew['saldo'];
									break;
								}
							}
						}
						 unset( $datosEnc ); //se borra el array
						//se guardan todos los topes
						$datosEnc[ "Medico" ] = $wbasedato;
						$datosEnc[ "Fecha_data" ] = date("Y-m-d");
						$datosEnc[ "Hora_data" ] = date( "H:i:s" );
						$datosEnc[ "tophis" ] = $historia;
						$datosEnc[ "toping" ] = $ingreso;
						$datosEnc[ "topres" ] = $valueRes['top_reshidTopRes']; //aseguradora responsable relacion con tope
						$datosEnc[ "toptco" ] = $valueRes['top_tcoselTipCon'];
						$datosEnc[ "topcla" ] = $valueRes['top_clahidClaTop'];
						$datosEnc[ "topcco" ] =	$valueRes['top_ccohidCcoTop'];
						$datosEnc[ "toptop" ] =	$valueRes['top_toptxtValTop'];
						$datosEnc[ "toprec" ] =	$valueRes['top_rectxtValRec'];
						$datosEnc[ "topdia" ] =	$valueRes['top_diachkValDia'];
						$datosEnc[ "topfec" ] =	$valueRes['top_fectxtValFec'];
						$datosEnc[ "topsal" ] =	$saldo_g;
						$datosEnc[ "topest" ] =	'on';
						$datosEnc[ "Seguridad" ] =	"C-".$usuario;
						//	print_r($datosEnc);

						unset( $empresasConTopes[ $valueRes['top_reshidTopRes'] ] );// si esta acá es porque tiene minimo un tope
						if (!($valueRes['top_tcoselTipCon'] == '*' && $valueRes['top_clahidClaTop'] == '*' && $valueRes['top_ccohidCcoTop'] == '*'
						&& $valueRes['top_toptxtValTop'] == '' && $valueRes['top_rectxtValRec'] == ''))
						{
							if( isset($valueRes['top_id']) && $valueRes['top_id'] != "" ){
								$datosEnc['id'] = $valueRes['top_id']*1;
								if(!isset($responsablesTopes[$valueRes['top_reshidTopRes']])){
									$responsablesTopes[$valueRes['top_reshidTopRes']] = array();
								}
								array_push( $responsablesTopes[$valueRes['top_reshidTopRes']], $valueRes['top_id']*1 );
								$sqlInsert = crearStringUpdate( $wbasedato."_000204", $datosEnc );
								$resEnc = mysql_query( $sqlInsert, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error grabando en la tabla ".$wbasedato."_000204 - ".mysql_error() ) );

							}else{
								$sqlInsert = crearStringInsert( $wbasedato."_000204", $datosEnc );
								$resEnc    = mysql_query( $sqlInsert, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error grabando en la tabla ".$wbasedato."_000204 - ".mysql_error() ) );
								$idResultante = mysql_insert_id();
								if(!isset($responsablesTopes[$valueRes['top_reshidTopRes']])){
									$responsablesTopes[$valueRes['top_reshidTopRes']] = array();
								}
								array_push( $responsablesTopes[$valueRes['top_reshidTopRes']], $idResultante*1 );

								$cadenaTope = $datosEnc[ "tophis" ].":".$datosEnc[ "toping" ].":".$datosEnc[ "toptco" ].":".$datosEnc[ "topcla" ].":".$datosEnc[ "topcco" ].":".formato($datosEnc[ "toptop" ]).":".$datosEnc[ "toprec" ].":".$datosEnc[ "topdia" ].":".$datosEnc[ "topfec" ];

								$sqlLog = registrarLogTopes($historia,$ingreso,$valueRes['top_reshidTopRes'],$cadenaTope,$idResultante,"activo",$usuario);
							}
							if (!$resEnc){
								$data['error']=1;
							}
						}
					}//foreach
				}
			} //data error = 0

			if( count( $responsablesTopes) ){
				foreach( $responsablesTopes as $keyResponsableTopes => $idsTopes ){
					$topesNoBorrar = $responsablesTopes[$keyResponsableTopes];
					if( count($topesNoBorrar) > 0 ){
						$condicion = implode( ",", $topesNoBorrar );
						$sqlDel="DELETE
								   FROM {$wbasedato}_000204
								  WHERE tophis = '{$historia}'
									AND toping = '{$ingreso}'
									AND topres = '{$keyResponsableTopes}'
									AND toprec != '*'
									AND id NOT IN({$condicion})";
						$resDel = mysql_query( $sqlDel, $conex ) or ( $data[ 'error' ] = utf8_encode( mysql_errno()." - Error en el query $sqlDel - ".mysql_error() ) );
						if (!$resDel){
							$data['error'] = 1;
						}
					}
				}
			}

			if( count( $empresasConTopes) > 0 ){//--> empresas que tenian topes pero ya no mas

				$empresasSinTopes = implode(",",$empresasConTopes);
				$sqlDel="DELETE
						   FROM {$wbasedato}_000204
						  WHERE tophis = '{$historia}'
							AND toping = '{$ingreso}'
							AND topres in ({$empresasSinTopes})
							AND toprec != '*'";
				$resDel = mysql_query( $sqlDel, $conex ) or ( $data[ 'error' ] = utf8_encode( mysql_errno()." - Error en el query $sqlDel - ".mysql_error() ) );
				if (!$resDel){
					$data['error'] = 1;
				}
			}
		}

		/******************* Fin Guardar en la tabla de topes 000204************/

		//--> segmento de código encargado de la regrabación.
		$ordenNuevos = 0;
		$cantidadCambios = 0;
		if( !isset( $responsablesNuevos ) )
			$responsablesNuevos = array();
		foreach( $responsablesNuevos as $codigoNuevoResponsable=> $valueNresponsable ){
			$ordenNuevos++;
			$responsableAnterior = $responsablesActualesOrd[$ordenNuevos];

			if( $valueNresponsable != $responsableAnterior and $valueNresponsable != "" and $responsableAnterior != ""){//--> si hubo cambio de responsable se debe realizar el análisis y la correspondiente regrabación.
				/*if( $cantidadCambios == 0 ){
					include_once("ips/funciones_facturacionERP.php");
				}*/
				$cantidadCambios++;
				$sqlCargos = "	UPDATE {$wbasedato}_000106
								   SET tcarreg = 'pen'
								 WHERE Tcarhis = '{$historia}'
								   AND Tcaring = '{$ingreso}'
								   AND Tcarres = '{$responsableAnterior}'
								   AND Tcarfex = 0
								   AND Tcarfre = 0
								   AND Tcarest = 'on'";
				$resCargos = mysql_query($sqlCargos, $conex ) or die("<b>ERROR EN QUERY MATRIX:</b><br>".mysql_error());
				$cargosAregrabar = mysql_affected_rows();

				$data1 = logAdmsiones( "Cambio de responsable responsable Anterior: {$responsableAnterior} --> nuevo: {$valueNresponsable}", $historia, $ingreso, $documento );
				if( $data1[ 'error' ] == 1 ){
					$data = $data1;
				}
				if($resCargos){
					guardarPendientesRegrabacion( $historia, $ingreso, $responsableAnterior, $valueNresponsable, "admision_erp.php");
				}
			}else{
				//--> no hay cambio de responsable en este punto
			}
		}
		//--> fin de la regrabación
	} //historia e ingreso vacios
	else
	{
		$data[ 'error' ] = 1;
		$data[ 'mensaje' ] = "historia o ingreso vacios";
	}
	/***fin se guardan o se actualizan los datos***/

	//--> se cambia nuevamente el orden de responsables para que tome el indicado para las tablas de matrix como la movhos_16, root_36 y root_37
	if( $cambioTemporalSaldoCero ){//-->2018-07-13
		$arrayAuxV        = $responsables1[0];
		$responsables1[0] = $responsables1[1];
		$responsables1[1] = $arrayAuxV;
		$responsablesReal = $responsables205;
	}
	/***insercion y actualizacion de tablas root_36 y root_37***/
		if( $data[ "error" ] == 0 )
		{
			//Consulto si existe la empresa
			$sql4 = "select Empcod
					from  root_000050
					where Empcod='".utf8_decode($wemp_pmla)."'";

			$res4 = mysql_query( $sql4, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando las empresas ".mysql_errno()." - Error en el query $sql4 - ".mysql_error() ) );

			if( $res4 )
			{
				$num4=mysql_num_rows($res4);
				if ($num4>0)
				{
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");

					//insercion y actualizacion de la tabla root_37
					$sql8 = "select Oriced
						from root_000037
						where Oritid='".utf8_decode($tipodoc)."'
						and Oriced='".utf8_decode($documento)."'
						and Oriori='".utf8_decode($wemp_pmla)."'";

					$res8 = mysql_query( $sql8, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla origen historia del paciente ".mysql_errno()." - Error en el query $sql8 - ".mysql_error() ) );
					if ($res8)
					{
						$num8=mysql_num_rows($res8);
						if ($num8>0)
						{
							//se actualiza el registro tabla de origen historia del paciente
							$sql9 =  " update root_000037 set
							  Orihis='".utf8_decode($historia)."',
							  Oriing='".utf8_decode($ingreso)."',
							  Fecha_data='".$fecha."', Hora_data='".$hora."'
							  where Oritid='".utf8_decode($tipodoc)."'
							  and Oriced='".utf8_decode($documento)."'
							  and Oriori='".utf8_decode($wemp_pmla)."'";

							$res9 = mysql_query( $sql9, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error actualizando la tabla de origen historia del paciente ".mysql_errno()." - Error en el query $sql9 - ".mysql_error() ) );
							if (!$res9){
								$data[ 'error' ] = 1; //sale el mensaje de error
							}
						}
						else //si es registro nuevo en la tabla origen historia del paciente
						{
							$sql10 = "insert into root_000037 (medico,fecha_data,hora_data, Oriced, Oritid, Orihis, Oriing, Oriori, seguridad)
							values ('root','".utf8_decode($fecha)."','".utf8_decode($hora)."','".utf8_decode($documento)."','".utf8_decode($tipodoc)."','".utf8_decode($historia)."','".utf8_decode($ingreso)."','".utf8_decode($wemp_pmla)."','C-root')";

							$res10 = mysql_query( $sql10, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error insertando en la tabla de origen historia del paciente ".mysql_errno()." - Error en el query $sql10 - ".mysql_error() ) );
							if (!$res10){
								$data[ 'error' ] = 1; //sale el mensaje de error
							}
						}
					}
					else{
						$sql10 = "insert into root_000037 (medico,fecha_data,hora_data, Oriced, Oritid, Orihis, Oriing, Oriori, seguridad)
						values ('root','".utf8_decode($fecha)."','".utf8_decode($hora)."','".utf8_decode($documento)."','".utf8_decode($tipodoc)."','".utf8_decode($historia)."','".utf8_decode($ingreso)."','".utf8_decode($wemp_pmla)."','C-root')";

						$res10 = mysql_query( $sql10, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error insertando en la tabla de origen historia del paciente ".mysql_errno()." - Error en el query $sql10 - ".mysql_error() ) );
						if (!$res10){
							$data[ 'error' ] = 1; //sale el mensaje de error
						}
					}
				}//num4>0
				else //no se ejecuto la consulta res4
				{
					$data[ 'error' ] = 1; //sale el mensaje de error
					$data[ 'mensaje' ] = "No se encontro la empresa";
				}
			}
			else //no se ejecuto la consulta res4
			{
				$data[ 'error' ] = 1; //sale el mensaje de error
			}
		}//data['error']==0
	/***fin insercion y actualizacion de tablas root_36 y root_37***/

	/*Insercion en la tabla de log admisiones 164 (movimientos)*/
	if( $data[ "error" ] == 0 )
	{
		$fecha = date("Y-m-d");
		$hora = (string)date("H:i:s");

		if( $preadmisionAnulada ){
			if ($modoConsulta != "false" ){
				$des="Admision actualizada";
			}else{
				$des="Admision";
			}
		}
		else{
			$des="Admision de preadmision";
		}

		$data1 = logAdmsiones( $des, $historia, $ingreso, $documento );

		if( $data1[ 'error' ] == 1 ){
			$data = $data1;
		}

	}
	/*Fin insercion en la tabla de log admisiones 164 (movimientos)*/


	/*Insercion o actualizacion en las tablas movhos 16(ingreso de pacientes) y 18(ubicacion de pacientes)*/
	if( $data[ "error" ] == 0 and $aplicacion != "")
	{
		$empresa        = "";
		$tipo_empresa   = "";
		$nombre_empresa = "";
		$tel_empresa    = "";
		$dir_empresa    = "";

		$empresa = $responsables1[0]['ing_cemhidCodAse'];
		//El tipo de empresa del primer responsable
		if ((!empty ($responsables1) && count($responsables1)>0)){
			$ing_tpaselTipRes = $responsables1[0]['ing_tpaselTipRes'];
		}

		if( $ing_tpaselTipRes == 'P' ){
			global $pac_no1txtPriNom,$pac_no2txtSegNom,$pac_ap1txtPriApe,$pac_ap2txtSegApe,$pac_fnatxtFecNac,$pac_sexradSex;
			$nombre_empresa = strtoupper($pac_no1txtPriNom)." ".strtoupper($pac_no2txtSegNom)." ".strtoupper($pac_ap1txtPriApe)." ".strtoupper($pac_ap2txtSegApe);
			$empresa = $documento;
			$tipo_empresa = consultarAplicacion2($conex,$wemp_pmla,"tipoempresaparticular");
			$tel_empresa = $datosTabla[ 'pactel' ];
			$dir_empresa = $datosTabla[ 'pacdir' ];

			if ((!empty ($responsables1) && count($responsables1)>0)){
				$empresa = $responsables1[0]['ing_cemhidCodAse'];
				$nombre_empresa = strtoupper($responsables1[0]['res_nom']);
			}

		}else{
			//se consultan los datos de la aseguradora para actualizar o insertar
			$sql="select Empcod,Empnom,Emptem,Emptel,Empdir
				    from ".$wbasedato."_000024
				   where Empcod='".utf8_decode($empresa)."'";

			$res = mysql_query( $sql, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla ".$wbasedato."_000024 para guardar en movhos_000016 ".mysql_errno()." - Error en el query $sql - ".mysql_error() ) );

			if ($res)
			{
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
				$num5a=mysql_num_rows($res);
				if( $num5a > 0 ){
					$rows5=mysql_fetch_array( $res );
					$empresa = $rows5['Empcod'];
					$tipo1= explode("-",$rows5['Emptem']);
					$tipo_empresa = $tipo1[0];
					$nombre_empresa = $rows5['Empnom'];
					$tel_empresa = $rows5['Emptel'];
					$dir_empresa = $rows5['Empdir'];
				}
			}
			else
			{
				$data[ 'error' ] = 1; //sale el mensaje de error no se ejecuto la consulta de consulta a la tabla 24
			}
		}

		//se consulta si existe esa historia y ese ingreso en la tabla movhos 16
		$sql5 = "select Inghis,Inging
		from  ".$aplicacion."_000016
		where Inghis='".utf8_decode($historia)."'
		and Inging='".utf8_decode($ingreso)."' ";

		$res5 = mysql_query( $sql5, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla movhos_000016 ".mysql_errno()." - Error en el query $sql5 - ".mysql_error() ) );

		if ($res5)
		{
			$num5=mysql_num_rows($res5);
			//tipo de empresa
			//$tipo1= explode("-",$rows5['Emptem']);
			$tipo=$tipo_empresa;

			if( utf8_decode( $tipo ) == "." ){//2017-11-10
				if( trim($ing_cemhidCodAse) == trim( $documento ) ){
					$tipo = consultarAplicacion2($conex,$wemp_pmla,"tipoempresaparticular");
					logAdmsiones( "Llego punto a la tabla movhos_000016 con datos: responsable: {$ing_cemhidCodAse} | documento: {$documento} | tipoResponsable: {$ing_tpaselTipRes} ", $historia, $ingreso, $documento);
				}else{
					logAdmsiones( "Llego punto a la tabla movhos_000016 con datos: responsable: {$ing_cemhidCodAse} | documento: {$documento} | tipoResponsable: {$ing_tpaselTipRes} ", $historia, $ingreso, $documento);
				}
			}

			if ($num5>0)
			{
				//se actualiza el registro tabla movhos_16
				$sql6 =  " update ".$aplicacion."_000016 set
				  Inghis='".utf8_decode($historia)."',
				  Inging='".utf8_decode($ingreso)."',
				  Ingres='".utf8_decode($empresa)."',
				  Ingnre='".utf8_decode($nombre_empresa)."',
				  Ingtip='".utf8_decode($tipo)."',
				  Ingtel='".utf8_decode($tel_empresa)."',
				  Ingdir='".utf8_decode($dir_empresa)."'
				  where Inghis='".utf8_decode($historia)."'
				  and Inging='".utf8_decode($ingreso)."'";

				$res6 = mysql_query( $sql6, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error actualizando la tabla movhos_000016 ".mysql_errno()." - Error en el query $sql6 - ".mysql_error() ) );
				if (!$res6)
				{
					$data[ 'error' ] = 1; //sale el mensaje de error
				}
			}
			else //se inserta el registro tabla movhos_000016
			{
				$sql7 = "insert into ".$aplicacion."_000016 (medico,fecha_data,hora_data, Inghis, Inging,Ingres,Ingnre,Ingtip,Ingtel,Ingdir, seguridad)
							values ('".$aplicacion."','".utf8_decode($fecha)."','".utf8_decode($hora)."','".utf8_decode($historia)."','".utf8_decode($ingreso)."','".utf8_decode($empresa)."',
							'".utf8_decode($nombre_empresa)."','".utf8_decode($tipo)."','".utf8_decode($tel_empresa)."','".utf8_decode($dir_empresa)."','C-".utf8_decode($key)."')";

				$res7 = mysql_query( $sql7, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error insertando en la tabla movhos_000016 ".mysql_errno()." - Error en el query $sql7 - ".mysql_error() ) );
				if (!$res7)
				{
					$data[ 'error' ] = 1; //sale el mensaje de error
				}
			}
		}
		else
		{
			$data[ 'error' ] = 1; //sale el mensaje de error
		}

		$campo_ccocir = "off";
		//se consulta si el servicio de ingreso tiene ccocir en movhos11, el valor de este va en movhos18->ubiptr
		$sql11 = "select ccocir
				from ".$aplicacion."_000011
				where Ccocod='".utf8_decode($servicioIng1[1])."'";
		$res11 = mysql_query( $sql11, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla ".$aplicacion."_000011 ".mysql_errno()." - Error en el query $sql11 - ".mysql_error() ) );
		if ($res11)
		{
			$num11=mysql_num_rows($res11);
			if( $num11 > 0 ){
				$row11=mysql_fetch_array( $res11 );
				if( $row11[0] == "on" ) $campo_ccocir = "on";
			}
		}

		//insercion y actualizacion de la tabla movhos_000018
		$sql8 = "select Ubihis,Ubiing
			from ".$aplicacion."_000018
			where Ubihis='".utf8_decode($historia)."'
			 and Ubiing='".utf8_decode($ingreso)."' ";

		$res8 = mysql_query( $sql8, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla ".$aplicacion."_000018 ".mysql_errno()." - Error en el query $sql8 - ".mysql_error() ) );
		if ($res8)
		{
			$num8=mysql_num_rows($res8);
			$servicioIng2=$servicioIng1[1];
			if ($num8>0)
			{
				//se actualiza el registro de la tabla movhos_000018
				$sql9 =  " update ".$aplicacion."_000018 set
				  Ubisac='".utf8_decode($servicioIng2)."',
				  Ubiptr='".utf8_decode($campo_ccocir)."'
				  where Ubihis='".utf8_decode($historia)."'
				  and Ubiing='".utf8_decode($ingreso)."'
				  and Ubisan = ''";

				$res9 = mysql_query( $sql9, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error actualizando la tabla de origen historia del paciente ".mysql_errno()." - Error en el query $sql9 - ".mysql_error() ) );
				if (!$res9)
				{
					$data[ 'error' ] = 1; //sale el mensaje de error
				}
			}
			else //si es registro nuevo en la tabla movhos_000018
			{
				$sql10 = "insert into ".$aplicacion."_000018 (medico,fecha_data,hora_data, Ubihis, Ubiing, Ubisac, Ubialp, Ubiald, Ubiptr,seguridad)
							values ('".$aplicacion."','".utf8_decode($fecha)."','".utf8_decode($hora)."','".utf8_decode($historia)."','".utf8_decode($ingreso)."',
							'".utf8_decode($servicioIng2)."', 'off' , 'off' , '".$campo_ccocir."' ,'C-".$aplicacion."')";

				$res10 = mysql_query( $sql10, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error insertando en la tabla movhos_000018 ".mysql_errno()." - Error en el query $sql10 - ".mysql_error() ) );
				if (!$res10)
				{
					$data[ 'error' ] = 1; //sale el mensaje de error
				}
			}
		}
		else
		{
			$data[ 'error' ] = 1; //sale el mensaje de error
		}
	}

	/*Fin insercion o actualizacion en las tablas movhos 16(ingreso de pacientes) y 18(ubicacion de pacientes)*/


	//--> manejo de los datos del triaje 2016-06-15 --> camilo zapata
	$wbasedatoMov 	= consultarAplicacion2($conex,$wemp_pmla,"movhos");
	$wbasedatoHce 	= consultarAplicacion2($conex,$wemp_pmla,"hce");
	$wbasedatotcx	= consultarAplicacion2($conex,$wemp_pmla,"tcx");
	$historiaTemporal = "";

	$campo_ccourg = "off";
	//se consulta si el servicio de ingreso tiene ccocir en movhos11, el valor de este va en movhos18->ubiptr
	$sql11 = "SELECT ccourg
			    FROM {$wbasedatoMov}_000011
			   WHERE Ccocod='".utf8_decode($servicioIng1[1])."'";
	$res11 = mysql_query( $sql11, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla ".$aplicacion."_000011 ".mysql_errno()." - Error en el query $sql11 - ".mysql_error() ) );
	if ($res11)
	{
		$num11=mysql_num_rows($res11);
		if( $num11 > 0 ){
			$row11=mysql_fetch_array( $res11 );
			$campo_ccourg = $row11[0];
		}
	}

	if( $campo_ccourg == "on" ){
		$qtriageUrg = " SELECT ahthte historiaTemporal, id
						  FROM {$wbasedatoMov}_000204
						 WHERE ahttdo = '$tipodoc'
						   AND ahtdoc = '$documento'
						   AND ahtest = 'on'
						   AND ahtahc != 'on'
						  ORDER BY id desc
						  LIMIT 1";
		$rsTrUrg          = mysql_query($qtriageUrg,$conex);
		$rowTrUrg         = mysql_fetch_assoc( $rsTrUrg );
		$historiaTemporal = $rowTrUrg['historiaTemporal'];

		if( $historiaTemporal == "" and $turno != "" ){
			$qtriageUrg = " SELECT ahthte historiaTemporal, id
							  FROM {$wbasedatoMov}_000204
							 WHERE ahttur = '{$turno}'
							   AND ahtest = 'on'
							   AND ahtahc != 'on'
							  ORDER BY id desc
							  LIMIT 1";
			$rsTrUrg          = mysql_query($qtriageUrg,$conex);
			$rowTrUrg         = mysql_fetch_assoc( $rsTrUrg );
			$historiaTemporal = $rowTrUrg['historiaTemporal'];
		}

		if( $historiaTemporal != "" ){
			//--> actualización de tablas con el triaje
			$queryUp36 =  "UPDATE {$wbasedatoHce}_000036
							  SET Firhis = '$historia',
							      Firing = '$ingreso'
							WHERE Firhis = '$historiaTemporal'
							  AND Firing = '1'";
			$rsup36    = mysql_query( $queryUp36,$conex );
			$regAfectados36 = mysql_affected_rows();

			$queryUp152 = "UPDATE {$wbasedatoHce}_000152
							  SET movhis = '$historia',
							      moving = '$ingreso'
							WHERE movhis = '$historiaTemporal'
							  AND moving = '1'";
			$rsup152    = mysql_query( $queryUp152,$conex );
			$regAfectados152 = mysql_affected_rows();
			if($rsup36 and $rsup152){
					$queryUp204 = "UPDATE  {$wbasedatoMov}_000204
									   SET ahtahc = 'on',
											  ahthis = '$historia',
											  ahting = '$ingreso',
											  ahttdo = '$tipodoc',
											  ahtdoc = '$documento'
									 WHERE ahthte = '$historiaTemporal'
									   AND ahtest = 'on'
									   AND ahtahc != 'on'";
					$rsup204    = mysql_query( $queryUp204,$conex );
			}
		}
	}

	$campo_ccocir = "off";
	$campo_ccoadm = "off";
	$campo_ccoayu = "off";
	//se consulta si el servicio de ingreso tiene ccocir en movhos11, el valor de este va en movhos18->ubiptr
	$sql11 = "SELECT ccocir, ccoadm, ccoayu
			    FROM {$wbasedatoMov}_000011
			   WHERE Ccocod='".utf8_decode($servicioIng1[1])."'";
	$res11 = mysql_query( $sql11, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla ".$aplicacion."_000011 ".mysql_errno()." - Error en el query $sql11 - ".mysql_error() ) );
	if ($res11)
	{
		$num11=mysql_num_rows($res11);
		if( $num11 > 0 ){
			$row11=mysql_fetch_array( $res11 );
			$campo_ccocir = $row11[0];
			$campo_ccoadm = $row11[1];
			$campo_ccoayu = $row11[2];
		}
	}

	//--> manejo de preanestesia.
	$qprean = " SELECT ahthte historiaTemporal, ahttur turnoCirugia, id, ahtccd
				  FROM {$wbasedatoMov}_000204
				 WHERE ahttdo = '$tipodoc'
				   AND ahtdoc = '$documento'
				   AND ahtest = 'on'
				   AND ahtahc != 'on'
				   AND ahttur != ''
				   AND ahtori = 'preanestesia'
				   {$condicionCcoPrea}
				 ORDER BY id desc
				 LIMIT 1";
	$rsPreAn          = mysql_query($qprean,$conex);
	while( $rowPreAn = mysql_fetch_assoc( $rsPreAn ) ){

		$historiaTemporal = $rowPreAn['historiaTemporal'];
		$turnoCir204      = $rowPreAn['turnoCirugia'];
		$turnoCirugia     = explode("_", $turnoCir204);
		$turnoCirugia     = $turnoCirugia[1];
	}

	$habilitarPorCirugia = false;
	if( ( ( $campo_ccocir == "on" or $campo_ccoadm =="on" ) and $campo_ccoayu == "off" ) and ( $rowPreAn['ahtccd'] == "" or $rowPreAn['ahtccd'] == "cir") ){
		$habilitarPorCirugia = true;
	}


	$habilitarPorAyuda = false;
	if( $campo_ccoayu == "on" and ( ( $rowPreAn['ahtccd'] == $servicioIng1[1] ) or ( $rowPreAn['ahtccd'] == "" and $habilitarPreanestesiaAD == "on" )  ) ){
		$habilitarPorAyuda = true;
	}

	if( $historiaTemporal != "" and ( ($habilitarPorCirugia ) or ($habilitarPorAyuda) ) ){
		$queryUp36 =  "UPDATE {$wbasedatoHce}_000036
						  SET Firhis = '$historia',
						      Firing = '$ingreso'
						WHERE Firhis = '$historiaTemporal'
						  AND Firing = '1'
						  AND Firpro = '000075'";
		$rsup36    = mysql_query( $queryUp36,$conex );
		$regAfectados3675 = mysql_affected_rows();

		$queryUp75 = "UPDATE {$wbasedatoHce}_000075
						 SET movhis = '$historia',
						     moving = '$ingreso'
					   WHERE movhis = '$historiaTemporal'
						 AND moving = '1'";
		$rsup152    = mysql_query( $queryUp75,$conex );
		$regAfectados75 = mysql_affected_rows();

		$queryTcx11 = " UPDATE {$wbasedatotcx}_000011
						   SET turhis = '{$historia}',
						       turing = '{$ingreso}'
						 WHERE turtdo = '{$tipodoc}'
						   AND turdoc = '{$documento}'
						   AND turtur = '{$turnoCirugia}'";
		$rstrCir    = mysql_query( $queryTcx11, $conex );

		$queryUp204 = "UPDATE {$wbasedatoMov}_000204
						   SET ahtahc = 'on',
						   	   ahthis = '$historia',
						   	   ahting = '$ingreso',
						   	   ahttdo = '$tipodoc',
						   	   ahtdoc = '$documento',
						   	   ahtccd = '{$servicioIng1[1]}'
						 WHERE id = '{$rowPreAn['id']}'";
		$rsup204    = mysql_query( $queryUp204,$conex );
	}


	/*fin modificacion de datos post admision de otras aplicaciones*/

	/*Insercion o actualizacion en la tabla hce_000022(medicos tratantes por historia) */
	if( $data[ "error" ] == 0 and $aplicacionHce != "")
	{
		$servicioIng1=explode("-",$ing_seisel_serv_ing);
		$servicioIng=$servicioIng1[0];

		$sql8 = "select Mtrhis,Mtring
			from ".$aplicacionHce."_000022
			where Mtrhis='".utf8_decode($historia)."'
			and Mtring='".utf8_decode($ingreso)."' ";

		$res8 = mysql_query( $sql8, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla hce_000022 ".mysql_errno()." - Error en el query $sql8 - ".mysql_error() ) );
		if ($res8)
		{
			// --> 	Modificacion jerson trujillo 2015-07-17:
			//		En los dos querys siguientes se agrega el campo turno. Para relacionar el turno del paciente en la hce_000022

			$num8=mysql_num_rows($res8);
			if ($num8>0)
			{
				$servicioIng2=$servicioIng1[1];

				$sql9 =  " update ".$aplicacionHce."_000022 set
				  Mtrhis='".utf8_decode($historia)."',
				  Mtring='".utf8_decode($ingreso)."',
				  Mtrest = 'on',
				  Mtrcur = 'off',
				  Mtrcci = '".$servicioIng2."'
				  where Mtrhis='".utf8_decode($historia)."'
				  and Mtring='".utf8_decode($ingreso)."'";

				$res9 = mysql_query( $sql9, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error actualizando la tabla hce_000022 ".mysql_errno()." - Error en el query $sql9 - ".mysql_error() ) );
				if (!$res9)
				{
					$data[ 'error' ] = 1; //sale el mensaje de error
				}
			}
			else //si es registro nuevo en la tabla movhos_000018
			{
				// --> 	Obtener Homologacion de especialidades tratantes HCE.
				//		Jerson Trujillo 2016-06-17
				$campoConducta		= trim(consultarAliasPorAplicacion($conex, $wemp_pmla, "CampoPlanConductaDeTriageHce"));
				$arrHomoConductas 	= array();
				$sqlHomoCon = "SELECT Hctcch, Hctcom FROM ".$wbasedatoMov."_000205
					WHERE Hctcon = '".$campoConducta."' AND Hctpin = 'on' AND Hctest = 'on'";
				$resHomoCon = mysql_query($sqlHomoCon, $conex) or ( $data[ 'mensaje' ] = utf8_encode( "Error en el query sqlHomoCon:$sqlHomoCon - ".mysql_error() ) );
				while($rowHomoCon = mysql_fetch_array($resHomoCon))
				{
					$arrHomoConductas[$rowHomoCon['Hctcch']] 	= $rowHomoCon['Hctcom'];
				}

				// --> 	Obtener el nivel de triage y la conducta de la HCE
				//		Jerson Trujillo 2016-06-17
				$formularioTriage	= consultarAliasPorAplicacion($conex, $wemp_pmla, "formularioYcampoTriage");
				$formularioTriage	= explode("-", $formularioTriage);

				$respuestaTriage	= obtenerDatoHce($historia, $ingreso, $formularioTriage[0], array($formularioTriage[1]));
				$triage				= $respuestaTriage[$formularioTriage[1]];
				if($triage != '')
				{
					$triage			= explode("-", $triage);
					$nivelTriage	= '0'.trim($triage[0])*1;
				}
				else
					$nivelTriage	= '';

				$especialidadTriage	= '';
				$fechaAsigEspec		= '0000-00-00';
				$horaAsigEspec		= '00:00:00';
				$Mtrtra				= 'off';

				$datosHce 			= obtenerDatoHce($historia, $ingreso, $formularioTriage[0], array($campoConducta));
				$datosHce			= $datosHce[$campoConducta];
				$datosHce 			= explode("-", $datosHce);
				$datosHce 			= $datosHce[0];
				if(trim($datosHce) != '')
				{
					$especialidadTriage	= $arrHomoConductas[$datosHce];
					$fechaAsigEspec		= '0000-00-00';
					$horaAsigEspec		= '00:00:00';
					$Mtrtra				= 'on';
				}


				// --> Guardar registro en la hce_000022
				$servicioIng2=$servicioIng1[1];
				$sql10 = "insert into ".$aplicacionHce."_000022 (medico,fecha_data,hora_data, Mtrhis, Mtring, Mtrest, Mtrfam,Mtrham,Mtrtra,Mtreme,Mtretr,Mtrtri,Mtrcur,Mtrcci,Mtrtur,Mtrfia,Mtrhia,seguridad)
							values ('".$aplicacionHce."' ,'".utf8_decode($fecha)."','".utf8_decode($hora)."','".utf8_decode($historia)."',
							'".utf8_decode($ingreso)."',  'on' , '".$fechaAsigEspec."',	'".$horaAsigEspec."',	'".$Mtrtra."' , '".$especialidadTriage."',	
							'".$especialidadTriage."',	'".$nivelTriage."',	'off' , '".$servicioIng2."',	'".$turno."', '".$wfiniAdm."',    '".$whiniAdm."', 	'C-".$key."')";

				$res10 = mysql_query( $sql10, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error insertando en la tabla hce_000022 ".mysql_errno()." - Error en el query $sql10 - ".mysql_error() ) );
				if (!$res10)
				{
					$data[ 'error' ] = 1; //sale el mensaje de error
				}
				elseif(trim($turno) != '')
				{
					// --> Actualizar el turno del paciente como admitido, jerson trujillo.
					$wbasedatoMov 	= consultarAplicacion2($conex,$wemp_pmla,"movhos");
					$usuario		= explode("-", $user);
					$usuario		= $usuario[1];

					$sqlActTurnoAdm = "UPDATE ".$wbasedatoMov."_000178
					   	SET Atuadm = 'on',
						   Atufad = '".date('Y-m-d')."',
						   Atuhad = '".date("H:i:s")."'
					 	WHERE Atutur = '".$turno."'";
					$resActTurnoAdm = mysql_query($sqlActTurnoAdm, $conex) or ( $data[ 'mensaje' ] = utf8_encode( "Error actualizando el turno - Error en el query sqlActTurnoAdm:$sqlActTurnoAdm - ".mysql_error() ) );
					if(!$resActTurnoAdm)
						$data['error'] = 1;

					// --> Registrar en el log el movimiento
					$sqlRegMov = "INSERT INTO ".$wbasedatoMov."_000179 (Medico,Fecha_data,Hora_data,Logtur,Logacc,Logusu,Seguridad,id)
									VALUES('".$wbasedatoMov."','".date('Y-m-d')."','".date("H:i:s")."','".$turno."','turnoAdmitido','".$usuario."', 'C-".$wbasedatoMov."',	NULL)";
					$resRegMov = mysql_query($sqlRegMov, $conex) or ($data['mensaje'] = utf8_encode( "Error actualizando el turno - Error en el query sqlRegMov:$sqlRegMov - ".mysql_error()));
					if(!$resRegMov)
						$data['error'] = 1;
				}
			}
		}
		else
		{
			$data[ 'error' ] = 1; //sale el mensaje de error
		}
	}

	/* modificacion de datos post admision de otras aplicaciones*/
	// Veronica Arismendy 2017-06-20
	if(isset($solucionCitas) && $solucionCitas != ""){
		$namAplMov  = consultarAplicacion2($conex,$wemp_pmla,"movhos");
		$infoCco    = getInfoCc($solucionCitas, $conex);

		$sqlValidar = "SELECT Cconsa FROM ". $namAplMov ."_000011 WHERE ccocod = '".$infoCco["codCco"]."'";
		$resSqlVal  = mysql_query($sqlValidar, $conex);
		$rowVal     = mysql_fetch_assoc($resSqlVal);

		if(isset($rowVal["Cconsa"]) && $rowVal["Cconsa"] == "on"){
			$prefix   = $infoCco["prefix"];
			$fechaCit = date("Y-m-d");
			$horaCit  = date("H:i:s");
			$usuario  = explode("-", $user);
			$usuario  = $usuario[1];
			$queryCit = "UPDATE {$solucionCitas}_000023
						SET
							".$prefix."tip = '{$tipodoc}',
							".$prefix."doc = '{$documento}',
							".$prefix."his = '{$historia}',
							".$prefix."ing = '{$ingreso}'
						WHERE
							".$prefix."tur = '{$logturCit}'";

			$rscit = mysql_query( $queryCit, $conex );

		} else {
			$fechaCit = date("Y-m-d");
			$horaCit  = date("H:i:s");
			$usuario  = explode("-", $user);
			$usuario  = $usuario[1];
			$queryCit = "UPDATE {$solucionCitas}_000023
						SET
							Logtip = '{$tipodoc}',
							Logdoc = '{$documento}',
							Loghis = '{$historia}',
							Loging = '{$ingreso}',
							Logffa = '{$fechaCit}',
							Loghfa = '{$horaCit}',
							Logefa = 'on',
							Loguad = '{$usuario}'
						WHERE
							Logtur = '{$logturCit}'";
			$rscit = mysql_query( $queryCit, $conex );
		}
	}

	/*Fin insercion o actualizacion en la tabla hce_000022(medicos tratantes por historia)*/
	global $key;
	$wbasedato  = consultarAplicacion2($conex,$wemp_pmla,"cliame");
	if( $data["error"] == 0 ){
		$sql = "delete from ".$wbasedato."_000163
				where Logusu='".utf8_decode($key)."'
				and Logest='on'";

		$res = mysql_query( $sql, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error borrando el registro de la tabla ".$wbasedato."000163 ".mysql_errno()." - Error en el query $sql - ".mysql_error() ) );
		//$data[ 'mensaje' ] = $sql;
	}



	/* Insercion en tabla movhos_000033 para los pacientes que están ingresando a un centro de costos de ayudas diagnósticas*/
	$aplMovhos    = consultarAplicacion2($conex,$wemp_pmla,"movhos");
	$servicioIng1 = explode("-",$ing_seisel_serv_ing);
	$servicioIng  = $servicioIng1[1];
	$ccoIngresoAyuda = verificarCcoIngresoAyudaUnificada( $servicioIng, $aplMovhos );
	if( $ccoIngresoAyuda && $modoConsulta != "true" ){//--> 2016-12-27 inserts de alta automática para ayudas diagnósticas.

		// Si el paciente a estado antes en el servicio para el mismo ingreso, traigo cuantas veces para sumarle una
		$q32 = "SELECT COUNT(*) FROM {$aplMovhos}_000032
		        WHERE Historia_clinica = '{$historia}'
		          AND Num_ingreso      = '{$ingreso}'
		          AND Servicio         = '{$servicioIng}'";
		$err32 = mysql_query($q32, $conex) or die (mysql_errno().$q32." - ".mysql_error());
		$row32 = mysql_fetch_array($err32);

		$wingser = $row32[0] + 1; //Sumo un ingreso a lo que traigo el query

		if( $wingser ==1 ){
			$q32 = "INSERT INTO {$aplMovhos}_000032 (Medico,Fecha_data,Hora_data,Historia_clinica,Num_ingreso,Servicio,Num_ing_Serv,Fecha_ing,Hora_ing,Procedencia,Seguridad)
			        	VALUES ( '{$aplMovhos}','".date('Y-m-d')."','".date("H:i:s")."','".$historia."','".$ingreso."','".$servicioIng. "','" . $wingser . "' ,'".$_POST[ 'ing_feitxtFecIng' ]."','".$_POST[ 'ing_hintxtHorIng' ]."','".$servicioIng. "', 'C-" . $key . "')";
			$err = mysql_query($q32, $conex) or die (mysql_errno() . $q32 . " - " . mysql_error());
		}


		$q33 = "SELECT * FROM {$aplMovhos}_000033
					WHERE Historia_clinica = '{$historia}' AND Num_ingreso = '{$ingreso}'
					AND Tipo_egre_serv = 'ALTA' AND Servicio = '{$servicioIng}'";
		$res33 = mysql_query($q33, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q33 . " - " . mysql_error());
		$num33 = mysql_num_rows($res33);

		if( $num33 == 0 ){
			$q33 = "INSERT INTO {$aplMovhos}_000033
						(Medico, Fecha_data, Hora_data, Historia_clinica, Num_ingreso, Servicio, Num_ing_serv, Fecha_egre_serv, Hora_egr_serv, Tipo_egre_serv, Dias_estan_serv,Seguridad)
				 	VALUES
						('".$aplMovhos."','".date('Y-m-d')."','".date("H:i:s")."','".$historia."','".$ingreso."','".$servicioIng."','1','".$_POST[ 'ing_feitxtFecIng' ]."','".$_POST[ 'ing_hintxtHorIng' ]."','ALTA','1','C-".$key."')";
			$res33 = mysql_query($q33, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q33 . " - " . mysql_error());
		}
	}

	// ACA DEBE DE IR EL RESPONSABLE REAL
	//insercion a unix
	$tieneConexionUnix = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'conexionUnix' );
	//$tieneConexionUnix='off';
	$ping_unix = ping_unix();
	if($hay_unix && $tieneConexionUnix == 'on' && $ping_unix == true )
	{

		if ((!empty ($responsables1) && count($responsables1)>0))
		{

			$respo = $responsables1[0]['ing_cemhidCodAse'];
			$sqlxy = "SELECT Empdir,Emptel
					    FROM ".$wbasedato."_000024
					   WHERE Empcod = '".$respo."'";

			$res2xy = mysql_query( $sqlxy, $conex );
			if( $res2xy )
			{
				$num2xy=mysql_num_rows($res2xy);
				if ($num2xy>0){
					$rows2xy=mysql_fetch_array($res2xy);
					$rows2xy  = mysql_fetch_array($res2xy);
					$valantes = $_POST['_ux_pacdre_ux_mredir_ux_infdav'];
					unset( $_POST['_ux_pacdre_ux_mredir_ux_infdav'] );
					$_POST['_ux_infdav']           = $valantes;
					$_POST['_ux_pacdre_ux_mredir'] = $rows2xy['Empdir'];
					$_POST['_ux_pactre_ux_inftav'] = $rows2xy['Emptel'];
				}
			}
		}
		/*
		FIN SE COMPRUEBA EL ESTADO DEL PACIENTE EN UNIX ANTES DE ADMITIR
		*/

		// se devuelve al estado estandar con orden de responsables original para que unix haga la respectiva validaciòn y guarde los datos correctamente
		if( $cambioTemporalSaldoCero ){//-->2018-07-13
			$arrayAuxV        = $responsables1[0];
			$responsables1[0] = $responsables1[1];
			$responsables1[1] = $arrayAuxV;
			$responsablesReal = $responsables205;
		}
		$errorUnix = false;
		$reintentar = true;
		$intentos   = 1;
		if( $modoConsulta == "true" && $cambiarAaccTran == "on" && !$estadoPaciente ){//--> 2016-12-27 se activa el paciente temporalmente en la historia y el ingreso seleccionado solo si se va a modificar la causa de ingreso a accidente de transito
			$estadoPaciente = true;
			$cambioEstadoTemporal = true;
			$_POST['cambioEstadoTEmporal'] = "true";
		}
		$_POST[ '_ux_pachis' ] = $historia;
		$_POST[ '_ux_pacnum' ] = $ingreso;

		//if( $modoConsulta == "false" || ( $modoConsulta == "true" && $estadoPaciente == true ) ){
		if( true ){//2020-06-13
			//$ping_unix = ping_unix();
			//-->2018-08-28 debido a que el orden de grabación cambió, entonces reasignar los valores de historia e ingreso para unix
			while( $reintentar && $intentos*1 <= $intentosIngreso*1 && $ping_unix && $tieneConexionUnix == 'on'){
					$a = new admisiones_erp();
					if( $a->conex_u && $ping_unix == true){
						$data = $a->data;
						$historia = $_POST[ 'ing_histxtNumHis' ];
						$ingreso = $_POST[ 'ing_nintxtNumIng' ];
						$consecutivoHistoriaDeUnix = true;
						if( $data['error'] == 1 ){
							$errorUnix = true;
							$data['mensaje'] = "Error al grabar la admision en UNIX ".$data['mensaje'];
							$data[ "error" ] = 4;//--> no grabó en unix
							//echo json_encode($data);
							//return;//--2016-10-21
						}
					}else if( $a->conex_u === false && $ping_unix == true ){
						$data[ "error" ] = 4; //--> no grabó en unix unix unix unix unix
						$errorUnix = true;
						$data[ "mensaje" ] = utf8_encode("No se puede grabar en UNIX desde Matrix. Comuniquese con informatica.");
						//echo json_encode($data);
						//return;//--2016-10-21
					}

					//--> validar si se guardó bien :D
					unset( $a );
					if( $errorUnix ){
						$reintentar = true;
						$intentos++;
						sleep( 3 );
					}else{
						$reintentar = false;
					}

				}
			}else{

			}

		if($cambioEstadoTemporal){
			$estadoPaciente = false;
		}
		$_POST[ 'pac_doctxtNumDoc' ] = $_POST[ 'pac_doctxtNumDocOriginal' ];
	}

	// FIN INSERCIÓN EN UNIX.
	//para enviarlos y colocarlos en las cajas de texto correspondientes despues de guardar
	$data[ 'historia' ]  = $historia;
	$data[ 'ingreso' ]   = $ingreso;
	$data[ 'documento' ] = $documento;

	if( $data["error"] == 0 ){
		if( $modoConsulta == "false" && $ping_unix && $tieneConexionUnix == 'on' ){
			//--> si llegó hasta acá es porque guardó en unix, entonces se actualiza el campo ingunx en la tabla 101 de ingresos
			$query = "UPDATE {$wbasedato}_000101
						 SET ingunx = 'on'
					   WHERE inghis = '{$historia}'
					     AND ingnin = '{$ingreso}'";
			$rsunx = mysql_query( $query, $conex );
			$data[ "mensaje" ] = "Se registró correctamente";
		}else{
			$data[ "mensaje" ] = utf8_encode("Se actualizo correctamente" );
		}
	}else{
		if( $data["error"] == 12 ){
			$data["error"] = 0;
			$data[ "mensaje" ] = "Se registró correctamente";
		}
	}

	if( $data[ "error" ] == 0)
	{
		$tipoDocPacInt = consultarAliasPorAplicacion($conex, $wemp_pmla, "tipoDocumentoPacienteInternacional" );
		$tipoDocumentoPacienteInternacional = explode(",",$tipoDocPacInt);
		
		// Si es un paciente internacional
		if(in_array($tipodoc,$tipoDocumentoPacienteInternacional))
		{
			// Si la admisión es nueva o si es modificada y actualizaron el tipo de documento debe notificar el paciente internacional
			if(($estadoAdmision == "Nueva") || ($estadoAdmision == "Modificada" && $cambiaTipoDocumento))
			{
				$nombrePaciente = $datosTabla['pacno1']." ".$datosTabla['pacno2']." ".$datosTabla['pacap1']." ".$datosTabla['pacap2'];
				
				$sqlCco = "SELECT Cconom FROM ".$aplMovhos."_000011 WHERE Ccocod = '".$servicioIng."';";

				$resCco = mysql_query( $sqlCco, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando descripcion de centro de costos ".mysql_errno()." - Error en el query $sqlCco - ".mysql_error() ) );
				$numCco=mysql_num_rows($resCco);
				
				$descCcoIngreso = $servicioIng;
				if($numCco>0)
				{
					$rowCco     = mysql_fetch_assoc($resCco);
					$descCcoIngreso = $rowCco['Cconom'];
				}

				notificarPacienteInternacional($conex, $wemp_pmla, $historia, $ingreso, $nombrePaciente, $descCcoIngreso);
			}
		}
		
	}
	echo json_encode($data);
	return;
}

if (isset($accion) and $accion == 'mostrarDatosAlmacenados')
{
	$data = array('error'=>0,'mensaje'=>'','html'=>'','numRegistrosIng'=>[], 'ultimoIngreso' => [], 'numRegistrosPac'=>'');

	global $wbasedato;
	global $conex;


	///---> para ignorar los datos iniciales siempre que se esté consultando ----> //correccion consulta de pacientes
	unset($_POST['ing_lugselLugAte']);
	unset($_POST['ing_feitxtFecIng']);
	unset($_POST['ing_hintxtHorIng']);
	//--mirar
	unset($_POST['ing_tutradiotutela']);
	unset($_POST['ing_claselClausu']);
	unset($_POST['pac_petselPerEtn']);
	unset($_POST['pac_zonselZonRes']);
	unset($_POST['ing_fhatxtFecAut1_tr_tabla_eps']);
	unset($_POST['ing_hoatxtHorAut1_tr_tabla_eps']);
	unset($_POST['pac_tdaselTipoDocRes']);
	///----> fin
	$datosEnc = crearArrayDatos( $wbasedato, "pac", "pac_", 3 );
	unset( $datosEnc[ 'Medico' ] );
	unset( $datosEnc[ 'Hora_data' ] );
	unset( $datosEnc[ 'Fecha_data' ] );
	unset( $datosEnc[ 'Seguridad' ] );
	$where = crearStringWhere( $datosEnc );


	$datosEnc = crearArrayDatos( $wbasedato, "ing", "ing_", 3 );
	unset( $datosEnc[ 'Medico' ] );
	unset( $datosEnc[ 'Hora_data' ] );
	unset( $datosEnc[ 'Fecha_data' ] );
	unset( $datosEnc[ 'Seguridad' ] );
	$where .= crearStringWhere( $datosEnc );

	//si no tiene ningun parametro para buscar
	if( empty( $where ) )
	{
		$data['mensaje']="Debe ingresar al menos un parametro de busqueda";
		$data['error']=1;
		echo json_encode($data);
		exit;
	}
	else{
		//reemplazo pacciu por paciu
		$where = str_replace( "pacciu", "paciu", $where );
	}

	/***se consulta si la persona ha venido antes en la tabla 100***/
	$sql = "select Pachis,Pactdo,Pacdoc,Pacfed,Pactat,Pacap1,Pacap2,Pacno1,Pacno2,Pacfna,Pacsex,Pacest,Pacdir,Pactel,
            Paciu,Pacbar,Pacdep,Paczon,Pactus,Pacofi,Paccea,Pacnoa,Pactea,Pacdia,Pacpaa,Pacact,Paccru,Pacnru,
            Pactru,Pacdru,Pacpru,Paccor,Pactam,Pacpan,Pacpet,Pacded,Pactrh,Pacpah,Pacdeh,Pacmuh,Pacmov,Pacned,
            Pacemp,Pactem,Paceem,Pactaf,Pacrem,Pacire,Paccac,Pactda,Pacddr,Pacdre,Pacmre,Pacmor,Paccre,a.Fecha_data, Pacdle, Pactle, Pacaud, Paczog
			from ".$wbasedato."_000100 a, ".$wbasedato."_000101 b
			where Pachis !='0' and pachis = inghis";
	$sql .= $where;
	$sql .=" Group by  Pachis  ";
	$sql .=" Order by  pacdoc, inghis*1, ingnin*1 DESC ";

	$res = mysql_query( $sql, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla ".$wbasedato."000100 ".mysql_errno()." - Error en el query $sql - ".mysql_error() ) );
	if ($res)
	{
		$num=mysql_num_rows($res);
		$data['numRegistrosPac']=$num;
		if ($num>0)
		{
			$where = str_replace("pactdo", "pactid", $where);
			$where = str_replace("pacdoc", "Pacced", $where);
			// Se consulta si hay datos demograficos en la table root_000036 y se reemplazan
			$sqlRoot = "select a.Pactid as pactdo,a.Pacced as pacdoc,a.pacfed,a.Pacap1,a.Pacap2,a.Pacno1,a.Pacno2,a.Pacnac as pacfna,
				a.Pacsex,a.Pacest,a.Pacdir,a.Pactel,a.Paciu,a.Pacbar,a.Pacdep,a.Paczon,a.Pactus,a.Pacofi,a.Paccor,a.Pacpan,a.Pacpet,
				a.Pacded,a.Pactrh,a.Pacpah,a.Pacdeh,a.Pacmuh,a.Pacmov,a.Pacned,a.Pacemp,a.Pactem,a.Paceem,a.Pactaf,a.Pacrem,a.Pacire,
				a.Paccac,a.Fecha_data,a.Pacdle,a.Pactle,a.Pacaud,a.Paczog
				from root_000036 a, ".$wbasedato."_000100 b, ".$wbasedato."_000101 c
				where a.pacced != '' and b.pacdoc = a.pacced and c.inghis = b.pachis";
			$sqlRoot .= $where;
			$resRoot = mysql_query($sqlRoot, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla root_000036 ".mysql_errno()." - Error en el query $sqlRoot - ".mysql_error() ) );
			if($resRoot){
				$numRoot = mysql_num_rows($resRoot);
			}
			/*se inicializa la i en el for de la consulta de la 100 pero se incrementa en el for de la
			consulta de la 101
			*/
			for( $i = 0, $j = 0;$rows=mysql_fetch_assoc($res); $j++ )
			{ //solo se puede buscar por el nombre del campo
				if($numRoot>0){
					$rowsRoot=mysql_fetch_assoc($resRoot);

					if($rowsRoot['Pacpan'] != '' && $rowsRoot['Paciu'] != '' && $rowsRoot['Pacest'] != '' && $rowsRoot['Pacpah'] != ''){
						foreach( $rowsRoot as $keyRoot => $valueRoot )
						{
							foreach( $rows as $key => $value )
							{
								if($keyRoot == $key){
									$rows[$key] = $valueRoot;
								}
							}
						}
					}
				}
				//posicion de historia
				$data['numPosicionHistorias'][ $rows['Pachis'] ] = $j;

				foreach( $rows as $key => $value )
				{
					//se guarda en data con el prefijo pac_ y empezando en la posicion 3 hasta el final
					$data[ 'infopac' ][ "pac_".substr( $key, 3 ) ] =  utf8_encode( $value );
				}
				$data[ 'infopac' ][ 'pac_ciu' ] = $rows['Paciu'];

				//se deben buscar los nombres de los campos que tienen guardado el codigo
				//se consulta el nombre del pais nacimiento
				if (!empty( $rows['Pacpan'] ))
				{
					$res1=consultaNombrePais($rows['Pacpan']);
					if ($res1)
					{
						$num1=mysql_num_rows($res1);
						if ($num1>0)
						{
							$rows1=mysql_fetch_array($res1);
							$data[ 'infopac' ][ 'pac_pantxtPaiNac' ] = utf8_encode($rows1['Painom']);
						}
						else
						{
							$data[ 'error' ] = 0;//--> permitir la carga para corrección
							$data['mensaje'].=" - No se encontro el codigo del pais \n";
							$data[ 'infopac' ][ 'pac_pan' ] = "";
						}
					}
					else
					{
						$data['mensaje']="No se ejecuto la consulta de busqueda del pais";
						$data[ 'error' ] = 1;
					}
				}

				//se consulta el nombre del departamento nacimiento
				if (!empty( $rows['Pacdep'] ))
				{
					$res2=consultaNombreDepartamento($rows['Pacdep']);
					if ($res2)
					{
						$num2=mysql_num_rows($res2);
						if ($num2>0)
						{
							$rows2=mysql_fetch_array($res2);
							$data[ 'infopac' ][ 'pac_deptxtDepNac' ] = utf8_encode($rows2['Descripcion']);
						}
						else
						{
							$data[ 'error' ] = 0;//--> permitir la carga para corrección
							$data['mensaje'].=" - No se encontro el codigo del departamento \n";
							$data[ 'infopac' ][ 'pac_dep' ] = "";
						}
					}
					else
					{
						$data[ 'error' ] = 1;
						$data['mensaje']="No se ejecuto la consulta de busqueda del departamento";
					}
				}

				//se consulta el nombre del municipio nacimiento
				if (!empty( $rows['Paciu'] ))
				{
					$res3=consultaNombreMunicipio($rows['Paciu']);
					if ($res3)
					{
						$num3=mysql_num_rows($res3);
						if ($num3>0)
						{
							$rows3=mysql_fetch_array($res3);
							$data[ 'infopac' ][ 'pac_ciutxtMunNac' ] = utf8_encode($rows3['Nombre']);
						}
						else
						{
							$data[ 'error' ] = 0;//--> permitir la carga para corrección
							$data['mensaje'].=" - No se encontro el codigo del municipio \n";
							$data[ 'infopac' ][ 'pac_ciu' ] = "";
						}
					}
					else
					{
						$data[ 'error' ] = 1;
						$data['mensaje']="No se ejecuto la consulta de busqueda del municipio";
					}
				}

				//se consulta el nombre del pais de residencia
				if (!empty( $rows['Pacpah'] ))
				{
					$res4=consultaNombrePais($rows['Pacpah']);
					if ($res4)
					{
						$num4=mysql_num_rows($res4);
						if ($num4>0)
						{
							$rows4=mysql_fetch_array($res4);
							$data[ 'infopac' ][ 'pac_pahtxtPaiRes' ] = utf8_encode($rows4['Painom']);
						}
						else
						{
							$data[ 'error' ] = 0;//--> permitir la carga para corrección
							$data['mensaje'].=" - No se encontro el codigo del pais de residencia \n";
							$data[ 'infopac' ][ 'pac_pah' ] = "";
						}
					}
					else
					{
						$data['mensaje']="No se ejecuto la consulta de busqueda del pais de residencia";
						$data[ 'error' ] = 1;
					}
				}

				//se consulta el nombre del departamento de residencia
				if (!empty( $rows['Pacdeh'] ))
				{
					$res4=consultaNombreDepartamento($rows['Pacdeh']);
					if ($res4)
					{
						$num4=mysql_num_rows($res4);
						if ($num4>0)
						{
							$rows4=mysql_fetch_array($res4);
							$data[ 'infopac' ][ 'pac_dehtxtDepRes' ] = utf8_encode($rows4['Descripcion']);
						}
						else
						{
							$data[ 'error' ] = 0;//--> permitir la carga para corrección
							$data['mensaje'] .=" - No se encontro el codigo del departamento residencia \n";
							$data[ 'infopac' ][ 'pac_deh' ] = "";
						}
					}
					else
					{
						$data[ 'error' ] = 1;
						$data['mensaje']="No se ejecuto la consulta de busqueda del departamento residencia";
					}
				}

				//se consulta el nombre del municipio de residencia
				if (!empty( $rows['Pacmuh'] ))
				{
					$res4=consultaNombreMunicipio($rows['Pacmuh']);
					if ($res4)
					{
						$num4=mysql_num_rows($res4);
						if ($num4>0)
						{
							$rows4=mysql_fetch_array($res4);
							$data[ 'infopac' ][ 'pac_muhtxtMunRes' ] = utf8_encode($rows4['Nombre']);
						}
						else
						{
							$data[ 'error' ]= 0;//--> permitir la carga para corrección
							$data['mensaje']=" - No se encontro el codigo del municipio de residencia \n";
							$data['infopac']['pac_muh'] = "";
						}
					}
					else
					{
						$data[ 'error' ] = 1;
						$data['mensaje']="No se ejecuto la consulta de busqueda del municipio de residencia";
					}
				}

				//se consulta el nombre del barrio de residencia
				if (!empty( $rows['Pacbar'] ) and !empty( $rows['Pacmuh']))
				{
					$res4=consultaNombreBarrio($rows['Pacbar'],$rows['Pacmuh']);
					if ($res4)
					{
						$num4 = mysql_num_rows($res4);
						if ($num4>0)
						{
							$rows4 = mysql_fetch_array($res4);
							$data[ 'infopac' ][ 'pac_bartxtBarRes' ] = utf8_encode($rows4['Bardes']);
						}
						else
						{
							$data[ 'error' ] = 0;//--> permitir la carga para corrección
							$data['mensaje'] .= " - No se encontro el codigo del barrio de residencia \n ";
						}
					}
					else
					{
						$data[ 'error' ] = 1;
						$data['mensaje']="No se ejecuto la consulta de busqueda del barrio de residencia";
					}
				}
				else
				{
					if(!empty( $rows['Pacbar'] ) and empty( $rows['Pacmuh']))
					{
						$res4=consultaNombreBarrio($rows['Pacbar'],'');
						if ($res4)
						{
							$num4=mysql_num_rows($res4);
							if ($num4>0)
							{
								$rows4=mysql_fetch_array($res4);
								$data[ 'infopac' ][ 'pac_bartxtBarRes' ] = utf8_encode($rows4['Bardes']);
							}
							else
							{
								$data[ 'error' ]  = 0;//--> permitir la carga para corrección
								$data['mensaje']=" - No se encontro el codigo del barrio de residencia \n ";
							}
						}
						else
						{
							$data[ 'error' ] = 1;
							$data['mensaje']="No se ejecuto la consulta de busqueda del barrio de residencia";
						}
					}
				}

				//se consulta el nombre de la ocupacion
				if (!empty( $rows['Pacofi'] ))
				{
					$res4=consultaNombreOcupacion($rows['Pacofi']);
					if ($res4)
					{
						$num4=mysql_num_rows($res4);
						if ($num4>0)
						{
							$rows4=mysql_fetch_array($res4);
							$data[ 'infopac' ][ 'pac_ofitxtocu' ] = utf8_encode($rows4['Nombre']);
						}
						else
						{
							 $data[ 'error' ] = 0;
							$data['mensaje']=" - No se encontro el codigo de la ocupacion \n";
						}
					}
					else
					{
						$data[ 'error' ] = 1;
						$data['mensaje']="No se ejecuto la consulta de busqueda de la ocupacion";
					}
				}

				//se consulta el nombre del departamento del responsable del usuario
				if (!empty( $rows['Pacdre'] ))
				{
					$res4=consultaNombreDepartamento($rows['Pacdre']);
					if ($res4)
					{
						$num4=mysql_num_rows($res4);
						if ($num4>0)
						{
							$rows4=mysql_fetch_array($res4);
							$data[ 'infopac' ][ 'pac_dretxtDepResp' ] = utf8_encode($rows4['Descripcion']);
						}
						else
						{
							$data[ 'error' ]= 0;//--> permitir la carga para corrección
							$data['mensaje']=" - No se encontro el codigo del departamento responsable del usuario \n ";
						}
					}
					else
					{
						$data[ 'error' ] = 1;
						$data['mensaje']="No se ejecuto la consulta de busqueda del departamento del responsable del usuario";
					}
				}

				//se consulta el nombre del municipio del responsable del usuario
				if (!empty( $rows['Pacmre'] ))
				{
					$res4=consultaNombreMunicipio($rows['Pacmre']);
					if ($res4)
					{
						$num4=mysql_num_rows($res4);
						if ($num4>0)
						{
							$rows4=mysql_fetch_array($res4);
							$data[ 'infopac' ][ 'pac_mretxtMunResp' ] = utf8_encode($rows4['Nombre']);
						}
						else
						{
							$data[ 'error' ]  = 0;//--> permitir la carga para corrección
							$data['mensaje']="No se encontro el codigo del municipio del responsable del usuario  \n";
						}
					}
					else
					{
						$data[ 'error' ] = 1;
						$data['mensaje']="No se ejecuto la consulta de busqueda del municipio del responsable del usuario";
					}
				}
				/***busqueda del paciente en la tabla de ingreso 101***/

				$sql1 = "select Inghis,Ingnin,Ingfei,Inghin,Ingsei,Ingtin,Ingcai,Ingtpa,Ingcem,Ingent,
						Ingord,Ingpol,Ingnco,Ingdie,Ingtee,Ingtar,Ingusu,Inglug,Ingdig,Ingdes,Ingpla,
						Ingcla,Ingvre,Fecha_data,Ingmei, Ingcai, Ingtut
						from ".$wbasedato."_000101
						where Inghis !='0'";
				if (!empty( $rows['Pachis'] ))
				{
					 $sql1.="and Inghis='".$rows['Pachis']."' ";
				}
				if (!empty( $_POST['ingreso'] ))
				{
					 $sql1.="and Ingnin='".$_POST['ingreso']."' ";
				}
				$sql1 .= " ORDER BY ingnin*1 asc";


				//--->  2015-09-04 --> estado activo del paciente
					if ( !empty( $rows['Pachis'] ) && !empty($_POST['ingreso']) && ( $rows['Pacact'] == "on" ) ){

						$queryAct = " SELECT COUNT(*)
									    FROM {$wbasedato}_000101
									   WHERE Inghis = '{$rows['Pachis']}'
									     AND Ingnin*1 > {$_POST['ingreso']} ";
						$rsAct    = mysql_query( $queryAct, $conex );
						//$rowAct   = mysql_fetch_array( $rsAct );

						if( $rowAct   = mysql_fetch_array( $rsAct ) ){
							if( $rowAct[0] > 0 ){
								$data[ 'infopac' ][ 'pac_act' ] = "off";
							}
						}else{

						}

					}
				//<----

				$res1 = mysql_query( $sql1, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla ".$wbasedato."000101 ".mysql_errno()." - Error en el query $sql1 - ".mysql_error() ) );
				if ($res1)
				{
					$num1=mysql_num_rows($res1);
					$data['numRegistrosIng'][ $rows['Pachis'] ] = $num1;
					$data['ultimoIngreso'][ $rows['Pachis'] ] = 0;
					if ($num1>0)
					{
						for( $i;$rows1=mysql_fetch_array($res1, MYSQL_ASSOC ); $i++ )  //solo se puede buscar por el nombre del campo
						{
							if( $data['ultimoIngreso'][ $rows['Pachis'] ] == 0 ){
								$data['ultimoIngreso'][ $rows['Pachis'] ] = 0;

								$sqll = "SELECT MAX(ingnin*1)
										   FROM ".$wbasedato."_000101
										  WHERE inghis = '".$rows['Pachis']."'";
								$resll = mysql_query( $sqll, $conex );

								if ($resll)
								{
									$numll=mysql_num_rows($resll);
									if ($numll>0)
									{
										$rowll=mysql_fetch_array($resll);
										$data['ultimoIngreso'][ $rows['Pachis'] ] = $rowll[0];
									}
								}
							}

							$data[ 'infoing' ][$i] = $data[ 'infopac' ];

							if( $rows1['Ingcai' ] == '02' ){
								consultarAccidentesAlmacenados( $rows['Pachis'], $rows1['Ingnin'], $data );
							}
							$data[ 'infoing' ][$i]['causaIngreso'] =  $rows1['Ingcai'];
							if(  $rows1['Ingcai' ] == '06'  ){
								consultarEventosCatastroficos( $rows['Pachis'], $rows1['Ingnin'], $data );
								//se llama a la funcion que trae la lista de eventos
								$llamadoEventos = listaEventos();
								//se le agrega al array que devuelve en la posicion htmlEventos para llenarlo en el js
								$data[ 'infoing' ][$i][ 'htmlEventos' ] = $llamadoEventos[ 'html' ];
							}
							$aplicacion=consultarAplicacion2( $conex, $wemp_pmla, "movhos" );
							if( $aplicacion != '' ){
								// $resIngSei = consultarCC( $aplicacion, $where="Ccoing = 'on' and Ccoayu != 'on' and ccoest = 'on' and ccorel='".$rows1[ 'Ingsei' ]."'" );
								$resIngSei = consultarCC( $aplicacion, $where="Ccocod='".$rows1[ 'Ingsei' ]."'" );

								if( $rowsIngSei = mysql_fetch_array($resIngSei) ){
									$rows1[ 'Ingsei' ] = $rowsIngSei[ 'Ccosei' ]."-".$rows1[ 'Ingsei' ];
								}
							}

							foreach( $rows1 as $key => $value )
							{
								//se guarda en data con el prefijo ing_ y empezando en la posicion 3 hasta el final
								$data[ 'infoing' ][$i][ "ing_".substr( $key, 3 ) ] = $value;
							}


							//se consultan los nombres de los datos del ingreso que traen el codigo
								//se consulta el nombre de la aseguradora
							if (!empty( $rows1['Ingcem'] ) && $rows1['Ingtpa'] == "E")
							{
								$res4=consultaNombreAseguradora($rows1['Ingcem']);
								$adicionarTarifaAnombre = consultarAplicacion2( $conex, $wemp_pmla, "adicionarTarifaAnombreEntidad" );
								if ($res4)
								{
									$num4=mysql_num_rows($res4);
									if ($num4>0)
									{
										$rows4=mysql_fetch_array($res4);
										if( $adicionarTarifaAnombre == "on" )
											$data[ 'infoing' ][$i][ 'ing_cemtxtCodAse' ] = utf8_encode($rows4['Empnom'])."-->Tarifa: ".$rows4['Emptar']." ".utf8_encode($rows4['Tardes']);
										else
											$data[ 'infoing' ][$i][ 'ing_cemtxtCodAse' ] = utf8_encode($rows4['Empnom']);
									}
									else
									{
										//$data[ 'error' ] = 1;
										$data['mensaje']="No se encontro el codigo de la aseguradora...";
									}
								}
								else
								{
									$data[ 'error' ] = 1;
									$data['mensaje']="No se ejecuto la consulta de busqueda del codigo de la aseguradora";
								}
							}

							if (!empty( $rows1['Ingtar'] ) )
							{
								$res4=consultaNombreTarifa($rows1['Ingtar']);
									if ($res4)
									{
										$num4=mysql_num_rows($res4);
										if ($num4>0)
										{
											$rows4=mysql_fetch_array($res4);
											$data[ 'infoing' ][$i][ 'ing_tartxt' ] = $rows1['Ingtar']." - ".utf8_encode($rows4['Descripcion']);
										}
										else
										{
											//$data[ 'error' ] = 1;
											$data['mensaje']="No se encontro el codigo de tarifa admision";
										}
									}
									else
									{
										$data[ 'error' ] = 1;
										$data['mensaje']="No se ejecuto la consulta de busqueda del codigo de tarifa admision";
									}
							}
							//se consulta el nombre de la impresion diagnostica
							if (!empty( $rows1['Ingdig'] ))
							{
								$res4=consultaNombreImpDiag($rows1['Ingdig']);
								if ($res4)
								{
									$num4=mysql_num_rows($res4);
									if ($num4>0)
									{
										$rows4=mysql_fetch_array($res4);
										$data[ 'infoing' ][$i][ 'ing_digtxtImpDia' ] = utf8_encode($rows4['Descripcion']);
									}
									else
									{
										//$data[ 'error' ] = 1;
										$data['mensaje']="No se encontro el codigo de impresion diagnostica";
									}
								}
								else
								{
									$data[ 'error' ] = 1;
									$data['mensaje']="No se ejecuto la consulta de busqueda del codigo impresion diagnostica";
								}
							}

							/*2014-08-04 Se consulta el nombre del medico de ingreso*/
							if (!empty( $rows1['Ingmei'] ))
							{
								$med_ing = consultarMedicos($rows1['Ingmei'],$wbasedato, $aplicacion, true);
								if( $med_ing ){
									$data[ 'infoing' ][$i][ 'ing_meitxtMedIng' ] = utf8_encode($med_ing['valor']['des']);
								}
							}

							$hayResponsables = false;
							/*codigo para traer los responsables de la tabla 205*/
							if($data['error']==0)
							{
								//La empresa tipo SOAT se muestre desde otra parte, en esta parte no se debe mandar
								$tipoSOAT = consultarAliasPorAplicacion($conex, $wemp_pmla, "tipoempresasoat" );


								$sqlpro = "select * from ".$wbasedato."_000205
											where Reshis = '".$rows['Pachis']."' and Resest = 'on' and Resdes = 'off'";
								if (!empty( $rows1['Ingnin'] ))
								{
									$sqlpro.="	and Resing = '".$rows1['Ingnin']."'";
								}
								$sqlpro.=" ORDER BY resord*1 ";

								$respro = mysql_query( $sqlpro, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla ".$wbasedato."000205 ".mysql_errno()." - Error en el query $sqlpro - ".mysql_error() ) );
								if ($respro)
								{
									$numpro=mysql_num_rows($respro);
									if ($numpro>0)
									{
										$hayResponsables = true;
										$k=-1;
										while( $rowspro = mysql_fetch_assoc($respro) ){

											$esSoat = false;

											if( $rowspro['Resord'] == "1" && $rows1['Ingcai' ] == '02' ){
												$esSoat = true;
											}
											if( $esSoat == false ){
												$k++;
												$data[ 'infoing' ][$i]['responsables'][$k]['ing_tpaselTipRes'] = $rowspro['Restpa'];
												$data[ 'infoing' ][$i]['responsables'][$k]['ing_cemhidCodAse'] = $rowspro['Resnit']; //el hidden
												// $data[ 'infoing' ][$i]['responsables'][$k]['ing_cemtxtCodAse'] = utf8_encode($rows4['Empnom'])."-->Tarifa: ".$rows4['Emptar']." ".utf8_encode($rows4['Tardes']);
												$data[ 'infoing' ][$i]['responsables'][$k]['ing_plaselPlan'] = $rowspro['Respla'];
												$data[ 'infoing' ][$i]['responsables'][$k]['ing_poltxtNumPol'] = $rowspro['Respol'];
												$data[ 'infoing' ][$i]['responsables'][$k]['ing_ncotxtNumCon'] = $rowspro['Resnco'];
												$data[ 'infoing' ][$i]['responsables'][$k]['res_firtxtNumcon'] = $rowspro['Resfir'];
												$data[ 'infoing' ][$i]['responsables'][$k]['res_comtxtNumcon'] = $rowspro['Rescom'];
												$data[ 'infoing' ][$i]['responsables'][$k]['res_ffrtxtNumcon'] = $rowspro['Resffr'];
												$data[ 'infoing' ][$i]['responsables'][$k]['ing_ordtxtNumAut'] = $rowspro['Resaut'];
												$data[ 'infoing' ][$i]['responsables'][$k]['ing_fhatxtFecAut'] = $rowspro['Resfha'];
												$data[ 'infoing' ][$i]['responsables'][$k]['ing_hoatxtHorAut'] = $rowspro['Reshoa'];
												$data[ 'infoing' ][$i]['responsables'][$k]['ing_npatxtNomPerAut'] = $rowspro['Resnpa'];
												$data[ 'infoing' ][$i]['responsables'][$k]['ing_pcoselPagCom'] = $rowspro['Respco'];
												$data[ 'infoing' ][$i]['responsables'][$k]['res_fir'] = $rowspro['Resfir'];
												$data[ 'infoing' ][$i]['responsables'][$k]['res_ffr'] = $rowspro['Resffr'];

												/*nombres de las aseguradoras*/
												if (!empty( $rowspro['Resnit'] ) && $rowspro['Restpa']=="E")
												{
													$res4=consultaNombreAseguradora($rowspro['Resnit']);
													$adicionarTarifaAnombre = consultarAplicacion2( $conex, $wemp_pmla, "adicionarTarifaAnombreEntidad" );

													if ($res4)
													{
														$num4=mysql_num_rows($res4);
														if ($num4>0)
														{
															$rows4=mysql_fetch_array($res4);
															if( $adicionarTarifaAnombre == "on" )
																$data[ 'infoing' ][$i]['responsables'][$k]['ing_cemtxtCodAse'] = utf8_encode($rows4['Empnom'])."-->Tarifa: ".$rows4['Emptar']." ".utf8_encode($rows4['Tardes']);
															else
																$data[ 'infoing' ][$i]['responsables'][$k]['ing_cemtxtCodAse'] = utf8_encode($rows4['Empnom']);
														}
														else
														{
															//$data[ 'error' ] = 1;
															$data['mensaje']="No se encontro el codigo de la aseguradora.";
														}
													}
													else
													{
														$data[ 'error' ] = 1;
														$data['mensaje']="No se ejecuto la consulta a la tabla de aseguradoras";
													}
												}else if($rowspro['Restpa'] == "P"){
													$data[ 'infoing' ][$i]['responsables'][$k]['res_nom'] = $rowspro['Resnom'];
													$data[ 'infoing' ][$i]['responsables'][$k]['res_tdo'] = $rowspro['Restdo'];
													$data[ 'infoing' ][$i]['responsables'][$k]['res_doc'] = $rowspro['Resnit'];
												}
												/*fin nombres de las aseguradoras*/


												//Se consultan los cups de cada nit-autorizacion
												if (!empty( $rowspro['Resnit'] ))
												{
													$sqlcupc = "SELECT Cprcup as cup, id
																  FROM ".$wbasedato."_000209
																 WHERE Cprhis = '".$rows['Pachis']."'
																   AND Cprnit = '".$rowspro['Resnit']."'
																   AND Cpraut = '".$rowspro['Resaut']."'
																   AND Cprest = 'on' ";

													/*$sqlcupc = "SELECT Cprcup as cup
																  FROM ".$wbasedato."_000209
																 WHERE Cprhis = '".$rows['Pachis']."'";*/
													if (!empty( $rows1['Ingnin'] )){
														$sqlcupc.="	AND Cpring = '".$rows1['Ingnin']."'";
													}
													$sqlcupc.="	AND Cprest = 'on' ";


													$rescup = mysql_query($sqlcupc,$conex) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla ".$wbasedato."000209 ".mysql_errno()." - Error en el query $sqlcupc - ".mysql_error() ) );
													if( $rescup ){
														$numpro=mysql_num_rows($rescup);
														if ($numpro>0){
															$data[ 'infoing' ][$i]['responsables'][$k]['cups'] = array();
															while( $rowcup = mysql_fetch_assoc($rescup) ){
																$cupdato = array();
																$cupdato['nombre'] = "";
																$cupdato['codigo'] = $rowcup['cup'] ;
																$cupdato['id'] = $rowcup['id'];
																$res4=consultaNombreCups($rowcup['cup']);
																if ($res4){
																	$num4=mysql_num_rows($res4);
																	if ($num4>0){
																		$rows4=mysql_fetch_array($res4);
																		$cupdato['nombre'] = utf8_encode($rows4['Nombre']);
																	}
																}
																array_push( $data[ 'infoing' ][$i]['responsables'][$k]['cups'], $cupdato );
															}
														}
													}else{
														$data['mensaje']="No se encontraron codigos cups ";
													}
												}
											}
										}
									}
									else
									{
										//$data[ 'error' ] = 1;
										$data['mensaje']="No se encontraron responsables asociados a la historia ".$rows['Pachis']."";
									}
								}
								else
								{
									// $data[ 'error' ] = 1;
									$data['mensaje']="No se ejecuto la consulta de busqueda de los responsables";
								}
							}
							/*Fin codigo para traer los responsables de la tabla 205*/

							/*codigo para traer los topes de la tabla 204*/
							if($data['error']==0 && $hayResponsables==true)
							{
								$sqlpro1 = "SELECT a.*
											  FROM {$wbasedato}_000204 a, {$wbasedato}_000205
											 WHERE Tophis = '{$rows['Pachis']}'
											   AND Topest = 'on'
											   AND toprec != '*'
											   AND reshis = tophis
											   AND resdes = 'off'
											   AND resing = toping
											   AND resnit = topres"; //no sea de soat
								if (!empty( $rows1['Ingnin'] ))
								{
									$sqlpro1.="	   AND Toping = '".$rows1['Ingnin']."'";
								}
								$sqlpro1.=" ORDER BY Topres,Toptco desc";

								$respro1 = mysql_query( $sqlpro1, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla ".$wbasedato."000204 ".mysql_errno()." - Error en el query $sqlpro1 - ".mysql_error() ) );
								if ($respro1)
								{
									$numpro1=mysql_num_rows($respro1);
									if ($numpro1>0)
									{
										for ($k=0; $rowspro1=mysql_fetch_array($respro1);$k++)
										{
											$data[ 'infoing' ][$i]['topes'][$k]['top_tcoselTipCon'] = $rowspro1['Toptco'];
											$data[ 'infoing' ][$i]['topes'][$k]['top_clahidClaTop'] = $rowspro1['Topcla']; //el hidden
											$data[ 'infoing' ][$i]['topes'][$k]['top_ccohidCcoTop'] = $rowspro1['Topcco']; //el hidden
											$data[ 'infoing' ][$i]['topes'][$k]['top_toptxtValTop'] = $rowspro1['Toptop'];
											$data[ 'infoing' ][$i]['topes'][$k]['top_rectxtValRec'] = $rowspro1['Toprec'];
											$data[ 'infoing' ][$i]['topes'][$k]['top_diachkValDia'] = $rowspro1['Topdia'];
											$data[ 'infoing' ][$i]['topes'][$k]['top_reshidTopRes'] = $rowspro1['Topres']; //el hidden
											$data[ 'infoing' ][$i]['topes'][$k]['sfdsf'] = 'total'; //el hidden
											$data[ 'infoing' ][$i]['topes'][$k]['id'] = $rowspro1['id']; //el hidden
											//el saldo no se muestra

											// para mostrar los totales de tope y reconocido
											if ($rowspro1['Toptco'] == '*' && $rowspro1['Topcla'] == '*' && $rowspro1['Topcco'] == '*')
											{
												$data[ 'infoing' ][$i]['topes'][$k]['total'] = 'on';
											}
											else
											{
												$data[ 'infoing' ][$i]['topes'][$k]['total'] = 'off';
											}
											//--> borrar
											$data[ 'infoing' ][$i]['topes'][$k]['total'] = 'off';

											/*nombres de los conceptos && $rowspro1['Topcla'] != '*'*/
											if (!empty( $rowspro1['Topcla'] ) )
											{
												$res4=consultaNombreConcepto($rowspro1['Topcla']);

												if ($res4)
												{
													$num4=mysql_num_rows($res4);
													if ($num4>0)
													{
														$rows4=mysql_fetch_array($res4);
														$data[ 'infoing' ][$i]['topes'][$k]['top_clatxtClaTop'] = utf8_encode($rows4['Cpgnom']);
													}
													else
													{
														$data[ 'error' ] = 1;
														$data['mensaje']="No se encontro el codigo del concepto";
													}
												}
												else
												{
													$data[ 'error' ] = 1;
													$data['mensaje']="No se ejecuto la consulta a la tabla de conceptos";
												}
											}
											/*fin nombres de los conceptos*/

											/*nombres de los cco && $rowspro1['Topcco'] != '*'*/
											if (!empty( $rowspro1['Topcco'] ) )
											{
												$res4=consultaNombreCco($rowspro1['Topcco']);

												if ($res4)
												{
													$num4=mysql_num_rows($res4);
													if ($num4>0)
													{
														$rows4=mysql_fetch_array($res4);
														$data[ 'infoing' ][$i]['topes'][$k]['top_ccotxtCcoTop'] = utf8_encode($rows4['Ccodes']);
													}
													else
													{
														$data[ 'error' ] = 1;
														$data['mensaje']="No se encontro el codigo del centro de costo";
													}
												}
												else
												{
													$data[ 'error' ] = 1;
													$data['mensaje']="No se ejecuto la consulta a la tabla de centros de costo";
												}
											}
											/*fin nombres de los cco*/
										}
									}
									else
									{
										//2014-02-11, Los topes no son obligatorios
										//$data[ 'error' ] = 1;
										//$data['mensaje']="No se encontraron topes asociados a la historia ".$historia."";
									}
								}
								else
								{
									// $data[ 'error' ] = 1;
									$data['mensaje']="No se ejecuto la consulta de busqueda de los topes";
								}
							}
							/*Fin codigo para traer los topes de la tabla 204*/

							// array_push( $data[ 'infoing' ][$i], $data[ 'infopac' ] );
						}//for

					}//$num1>0
					else
					{
						$data[ 'error' ] = 1;
						$data[ 'mensaje' ] = "No se encontraron registros del ingreso para los datos ingresados";
					}
				}
				else
				{
					$data[ 'error' ] = 1;
				}
					/***fin busqueda en la tabla 101***/
			} //fin for
		} //si trae registros de la 100
		else
		{
			$data[ 'mensaje' ] = "No se encontro informacion para los datos ingresados";
		}
	}
	else
	{
		$data[ 'error' ] = 1;
	}
	/***fin busqueda en la tabla 100***/

	// $data[ 'error' ] = 0;

	echo json_encode($data);
	return;
}

if (isset($accion) and $accion == 'consultarResponsable')
{
	global $wbasedato;
	global $conex;

	$data = array('error'=>0,'mensaje'=>'','html'=>'',
				  'tdoc'=>'','doc'=>'','nom'=>'',
				  'dir'=>'','ddir'=>'','dep'=>'',
				  'mun'=>'','tel'=>'','mov'=>'',
				  'ema'=>'','pare'=>'','no1'=>'',
				  'no2'=>'','ap1'=>'', 'ap2'=>'');
	//se busca como responsable        //cedula,nombre,telefono,dir,paren,email, tipo doc, detalle dir,dep res, mun res,movil res
	 $sql = "select Paccru,Pacnru,Pactru,Pacdru,Pacpru,Paccre,Pactda,Pacddr,Pacdre,Pacmre,Pacmor, Pactdo
				from ".$wbasedato."_000100
				where Paccru='".utf8_decode($cedula2)."'";
	$res = mysql_query( $sql, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla ".$wbasedato."000100 con responsable de usuario ".mysql_errno()." - Error en el query $sql - ".mysql_error() ) );
	if ($res)
	{
		$num=mysql_num_rows($res);
		if ($num==0) //no encontro como responsable
		{
			//se busca como paciente                                                      dep resi,mun resi,movil,detalle dir
			  $sql1 = "select Pactdo,Pacdoc,Pacap1,Pacap2,Pacno1,Pacno2,Pacdir,Pactel,Pacdeh,Pacmuh,Pacmov,Paccor,Pacded
				from ".$wbasedato."_000100
				where Pacdoc='".utf8_decode($cedula2)."'
				";
			$res1 = mysql_query( $sql1, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla ".$wbasedato."000100 con datos paciente ".mysql_errno()." - Error en el query $sql1 - ".mysql_error() ) );
			if ($res1)
			{
				$num1=mysql_num_rows($res1);
				if ($num1==0)
				{
					$data['mensaje']="No se encontraron datos de responsable del usuario como paciente";
				}
				else
				{  //resultados como paciente
					$rows=mysql_fetch_array($res1);
					$data['tdoc']=utf8_encode($rows['Pactdo']);
					$data['doc']=utf8_encode($rows['Pacdoc']);
					$data['nom']=utf8_encode($rows['Pacno1'])." ".utf8_encode($rows['Pacno2'])." ".utf8_encode($rows['Pacap1'])." ".utf8_encode($rows['Pacap2']) ;

					//Consulto el nombre del departamento en donde ocurrio el accidente
					$res = consultaNombreDepartamento( $rows[ 'Pacdeh' ] );
					$num = mysql_num_rows( $res );
					if( $rowdp = mysql_fetch_array( $res ) ){
						$dep = utf8_encode($rowdp[ 'Descripcion' ]);
					}else{
						$dep = '';
					}
					$data[ 'ndep' ] = $dep;

					//Consulto el nombre el municipio en donde ocurrio el accidente
					$res = consultaNombreMunicipio( $rows[ 'Pacmuh' ] );
					$num = mysql_num_rows( $res );
					if( $rowdp = mysql_fetch_array( $res ) ){
						$mun = $rowdp[ 'Nombre' ];
					}else{
						$mun = '';
					}
					$data[ 'nmun' ] = $mun;

					$data['no1']=utf8_encode($rows['Pacno1']);
					$data['no2']=utf8_encode($rows['Pacno2']);
					$data['ap1']=utf8_encode($rows['Pacap1']);
					$data['ap2']=utf8_encode($rows['Pacap2']);

					$data['dir']=utf8_encode($rows['Pacdir']);
					$data['ddir']=utf8_encode($rows['Pacded']);
					$data['dep']=utf8_encode($rows['Pacdeh']);
					$data['mun']=utf8_encode($rows['Pacmuh']);
					$data['tel']=utf8_encode($rows['Pactel']);
					$data['mov']=utf8_encode($rows['Pacmov']);
					$data['ema']=utf8_encode($rows['Paccor']);
					$data['pare']="";
				}
			}
			else
			{
				$data['error']=1;
			}
		}
		else
		{
			//resultados como responsable
			$rows=mysql_fetch_array($res);

			//Consulto el nombre del departamento en donde ocurrio el accidente
			$res = consultaNombreDepartamento( $rows[ 'Pacdre' ] );
			$num = mysql_num_rows( $res );
			if( $rowdp = mysql_fetch_array( $res ) ){
				$dep = utf8_encode($rowdp[ 'Descripcion' ]);
			}else{
				$dep = '';
			}
			$data[ 'ndep' ] = $dep;

			//Consulto el nombre el municipio en donde ocurrio el accidente
			$res = consultaNombreMunicipio( $rows[ 'Pacmre' ] );
			$num = mysql_num_rows( $res );
			if( $rowdp = mysql_fetch_array( $res ) ){
				$mun = utf8_encode($rowdp[ 'Nombre' ]);
			}else{
				$mun = '';
			}
			$data[ 'nmun' ] = $mun;

			$data['tdoc']=utf8_encode($rows['Pactda']);
			$data['doc']=utf8_encode($rows['Paccru']);
			$data['nom']=utf8_encode($rows['Pacnru']);
			$data['no1']=utf8_encode($rows['Pacnru']);
			$data['dir']=utf8_encode($rows['Pacdru']);
			$data['ddir']=utf8_encode($rows['Pacddr']);
			$data['dep']=utf8_encode($rows['Pacdre']);
			$data['mun']=utf8_encode($rows['Pacmre']);
			$data['tel']=utf8_encode($rows['Pactru']);
			$data['mov']=utf8_encode($rows['Pacmor']);
			$data['ema']=utf8_encode($rows['Paccre']);
			$data['pare']=utf8_encode($rows['Pacpru']);
		}
	}
	else
	{
		$data['error']=1;
	}

	echo json_encode($data);
	return;
}

if( isset($accion) and $accion == 'enviarnivelsiguiente' )
{
	$respuesta = "";
	$registroAfectado = "";
	$nuevoNivel = 0;
	$error = 1;
	$nivelSiguiente = trim( $nivelSiguiente );
	$nivelListado   = trim( $nivelListado );


	global $wbasedato;
	global $conex;
	$wfachos      = consultarAliasPorAplicacion($conex, $wemp_pmla, "Facturacion hospitalaria");

	$hoy = date("Y-m-d");
	$hora = date("H:i:s");

    $select ="SELECT lenpro
				FROM ".$wfachos."_000011
			   WHERE lenhis = '{$whis}'
				 AND lening = '{$wing}'";


	$res = mysql_query( $select, $conex );
	$rows=mysql_fetch_array($res);
	$flujoorigen = $rows['lenpro'];

	$select ="SELECT  dprtem ,	dprori 	,dprdes
				FROM  ".$wfachos."_000018
				WHERE  dprcod ='".$flujoorigen."'";
	$res = mysql_query( $select, $conex );
	$rows=mysql_fetch_array($res);


		$query = "UPDATE {$wfachos}_000011
					 SET lenrac = '".$rows['dprdes']."',
						 lenran = '".$rows['dprori']."',
						 lenfeu = '{$hoy}',
						 lenhou = '{$hora}'
				   WHERE lenhis = '{$whis}'
				     AND lening = '{$wing}'";
		//$rs = mysql_query( $query, $conex );
	    $nivelSiguiente= $rows['dprdes'];
		$nivelanterior = $rows['dprori'];
		//$query;
		$rs = mysql_query( $query, $conex );


		/*{
			$registroAfectado = mysql_affected_rows();
			$accion 	    = "UPDATE";
			$descripcion    = "envio de documento a el rol {$nivelSiguiente} ";
			$identificacion = "{$whis}-{$wing}";
			insertLog( $conex, $wfachos, $usuario, $accion, "000011", $err, $descripcion, $identificacion, $sql_error = '', '', '', '', '' );
		}*/
		//----------------------------------------------------------------------------INSERCIÓN INICIAL EN TABLA DE MOVIMIENTO---------------------------------------------------------------------//
		/*$query = "INSERT
				    INTO {$wfachos}_000020 (Medico, Fecha_data, Hora_data, lmohis, lmoing, lmotip, lmoori, lmodes, lmofot, lmoest, seguridad)
				  VALUES ('fachos','{$hoy}','{$hora}','{$whis}','{$wing}', 'UPDATE' ,'{$nivelanterior}','{$nivelSiguiente}', '{$foto}','on','{$usuario}')";
		//$rs = mysql_query( $query, $conex );
		$nivelListado = $nivelSiguiente;*/


		return;
}
if( isset($accion) and $accion == 'naautomatico' )
{
	global $conex;
	global $user;
	$user2 = explode("-",$user);
	$user2 = $user2[1];


	$westado ='na';
	$wfachos      = consultarAliasPorAplicacion($conex, $wemp_pmla, "Facturacion hospitalaria");

	$query = "UPDATE {$wfachos}_000012
				 SET delest = '{$westado}',
				     delfor = ''
			   WHERE delhis = '{$whistoria}'
			     AND deling = '{$wingreso}'
			      ".$opciones." ";

	$rs = mysql_query( $query, $conex );
	//echo $query;
    return;
}
if( isset($accion) and $accion == 'actualizarEstadoSoporte' )
{

	global $conex;
	global $user;
	$user2 = explode("-",$user);
	$user2 = $user2[1];



	$wfachos      = consultarAliasPorAplicacion($conex, $wemp_pmla, "Facturacion hospitalaria");
	$query = "UPDATE {$wfachos}_000012
				 SET delest = '{$westado}',
				     delfor = 'a'
			   WHERE delhis = '{$whistoria}'
				 AND deling = '{$wingreso}'
				 AND delsop = '{$wsoporte}'";
	  //$rs = mysql_query( $query, $conex );

	//echo $query;

	if( $rs = mysql_query( $query, $conex ) )
	{
		 $accion 	     = "UPDATE";
		 $descripcion    = "actualizo soporte para el  valor {$westado} ";
		 $identificacion = "{$whistoria}-{$wingreso}";
		 insertLog( $wemp_pmla, $user2, $accion, "000016", $err, $descripcion, $identificacion, $sql_error = '', $wempPlan, '', $servicio="", $wsoporte );
	}

	return;
}

if( isset($accion) and $accion == 'llamarModalNoFlujo' )
{
	global $conex;
	$respuesta = array();
	$wfachos      = consultarAliasPorAplicacion($conex, $wemp_pmla, "Facturacion hospitalaria");
	$wbasedato    = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
	$wmovhos	  = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");


	$Select = "SELECT Empcod , Empfec , Empest
				 FROM ".$wfachos."_000027
			    WHERE Empest = 'on'";

	$resp 	 = mysql_query( $Select, $conex );
	$vecresp = array();
	while( $rowres = mysql_fetch_array( $resp ) )
	{
		$vecresp[$rowres['Empcod']]=$rowres['Empfec'];
	}

	global $user;
	$user2 = explode("-",$user);
	$user2 = $user2[1];
	$fecha = date("Y-m-d");
	$fechaant = date( "Y-m-d",time()-3600*(24*5));

	$select   = "SELECT Inghis , Ingnin , Pacap1, Pacap2, Pacno1 , Pacno2 , Empnom,Empcod, ".$wbasedato."_000101.Ingfei
				   FROM   ".$wbasedato."_000100, ".$wbasedato."_000024 , ".$wbasedato."_000101 LEFT JOIN ".$wfachos."_000011 ON ( lenhis = Inghis AND lening = Ingnin)
				  WHERE Ingfei > '".$fechaant."'
				    AND Pachis = Inghis
					AND Ingcem = Empcod
					AND Ingusu  ='".$user2."'
					AND lenhis IS NULL";


	$res = mysql_query( $select, $conex );

	// $responsableactual = '';


	$html .= "<table><tr class='encabezadoTabla'><td>Historia</td><td>Nombre</td><td>Responsable</td><td></td></tr>";
	$k =0;
	while ($rowres = mysql_fetch_array( $res ))
	{
		if(!$vecresp[$row['Empcod']] OR ( $vecresp[$row['Empcod']] >= $row['Ingfei'] ) )
		{
			$k++;
			if (is_int ($k/2))
			{
				$wcf="fila1";  // color de fondo de la fila
			}
			else
			{
				$wcf="fila2"; // color de fondo de la fila
			}
			$nombre = '';
			$nombre = $rowres['Pacno1']." ".$rowres['Pacno2']." ".$rowres['Pacap1']." ".$rowres['Pacap2'];

			$html .= "<tr class='".$wcf."'><td>".$rowres['Inghis']."-".$rowres['Ingnin']."</td><td>".$nombre."</td><td>".$rowres['Empnom']."</td><td><input type='button' value='Crear Flujo' onclick='crearflujo(\"".$rowres['Inghis']."\" , \"".$rowres['Ingnin']."\")'></td></tr>";
		}
	}

	$html .= "</table><br><center><input style='width:100px' type='button' value='cerrar' onclick='cerrarsinflujo()'></center>";



	$respuesta['contenidoDiv'] = $html;
	if($k>0)
	{

			$respuesta['html'] = "abrirModal";
	}
	else
	{
			$respuesta['html'] = "";
	}

	echo json_encode($respuesta);

	//$select   = "SELECT ".$wfachos."_000011 ";

	return;
}
if( isset($accion) and $accion == 'crearListaAutomatica' )
{
	//--------------------------------
	// Se pone a grabar en un archivo la historia y el ingreso que  pasa por aqui
	//$guardar = "<br>Registro de grabacion, Historia :".$whistoria."- ".$wingreso."";
	//seguimiento($guardar, false );

	//--------------------------------

	global $conex;
	$respuesta = array();
	$html ='';
	$existeRelacion = false;
	$vector_empresas = array();
	$hoy = date("Y-m-d");
	$hora = date("H:i:s");
	$whis = $whistoria;
	$wing = $wingreso;
	$usuario 		= 'fachos';
	$nivelUsuario   = '01';

	$wfachos      = consultarAliasPorAplicacion($conex, $wemp_pmla, "Facturacion hospitalaria");
	$wbasedato    = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
	$wmovhos	  = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$respuesta['imprimotabla'] ='no';

	//----------------------
	$select  = "SELECT Ingcem
				  FROM ".$wbasedato."_000101
				 WHERE Inghis  = '".$whistoria."'
				   AND Ingnin = '".$wingreso."'  ";


	$res = mysql_query( $select, $conex );
	$responsableactual = '';
	if ($rowres = mysql_fetch_assoc( $res ))
	{
		$responsableactual =  $rowres['Ingcem'];
	}



	// primero busco si la historia tine listado creado

	// busco el listado en la tabla fachos_000011
	$select = "SELECT lenemp
				 FROM ".$wfachos."_000011
				WHERE lenhis = '".$whistoria."'
				  AND lening = '".$wingreso."'";

	$rs    = mysql_query( $select, $conex );
	$relacionEmpresa ='';
	while( $row = mysql_fetch_assoc( $rs ) )
	{
		$relacionEmpresa = $row['lenemp'];
		// Guardo en un vector las empresas con las que tiene un listado creado
		$select = "SELECT pememp
					 FROM ".$wfachos."_000009
					WHERE pemcod = '".$relacionEmpresa."'";

		$res = mysql_query( $select, $conex );
		if ($row1 = mysql_fetch_assoc( $res ))
			$vector_empresas[$row1['pememp']]='';


		$existeRelacion =true;

	}

	// si exite relacion , verifico si ya existe una lista creada para el responsable mandado por el parametro
	if($existeRelacion)
	{
		if(array_key_exists($responsableactual, $vector_empresas))
		{
			$creoautomatico ='no';
			$html =' NO CREO ';
		}
		else
		{
			$creoautomatico ='no';
			$html =' CREO  Ya tiene lista pero no a este responsable';
		}

	}
	else
	{
		$creoautomatico ='si';
		$html ='CREO ';
	}

	if($wflujo !='' and $wplan!='')
	{
			$select = "SELECT pempln ,pemcod, plndes
						 FROM  ".$wfachos."_000009 ,  ".$wfachos."_000007
						WHERE pememp ='".$responsableactual."'
						  AND pemest ='on'
						  AND plncod = pempln
						  AND pempln = '".$wplan."' ";

					$respuesta['html'] =  "abrirModal";
					$respuesta['exito'] = "si";
					$respuesta['mensajeexitoso'] = "Se inserto con exito";


					$res = mysql_query( $select, $conex );

					while ($rowres = mysql_fetch_assoc( $res ))
					{

						$ultimorelacion = $rowres['pemcod'] ;
					}
					$proceso = $wflujo;
					//$ultimorelacion = $wplan;
					$insert = "INSERT
								 INTO {$wfachos}_000011 (Medico	, Fecha_data, Hora_data, lenhis		, lening	, lenemp					, lenpro			, lenrac			, lenfeu	, lenhou	, lenest	, seguridad)
							   VALUES ( 					'fachos', '{$hoy}'	, '{$hora}', '{$whis}'	, '{$wing}' ,	'{$ultimorelacion}'		, '{$proceso}'	, '{$nivelUsuario}'	, '{$hoy}'	, '{$hora}'	, 'on'		,'{$usuario}' )";

					$resinsert = mysql_query($insert, $conex) or die("<b>ERROR EN QUERY(sqlDet):</b><br>".mysql_error());
					$html.= "  inserte";

					//----------------------------------------------------------------------------INSERCIÓN INICIAL EN TABLA DE MOVIMIENTO--------------------------------------------------------------//
					$query = "INSERT
								INTO {$wfachos}_000020 (Medico, Fecha_data, Hora_data, lmohis, lmoing, lmoori, lmodes, lmoest, seguridad)
							  VALUES ('fachos','{$hoy}','{$hora}','{$whis}','{$wing}','','{$nivelUsuario}','on','{$usuario}')";
					$rs = mysql_query( $query, $conex );

					$query = "INSERT
								INTO {$wfachos}_000016 (Medico, Fecha_data, Hora_data, relhis, reling, relres, relest, seguridad)
							  VALUES ('fachos','{$hoy}','{$hora}','{$whis}',{$wing},'{$responsableactual}','on','{$usuario}')";
					$rs    = mysql_query( $query, $conex );



	}
	else
	{

		if($responsableactual !='' and $creoautomatico=='si')
		{


			// lo primero que se hace es saber si para esta empresa hay un plan  definido
			$select = "SELECT pempln ,pemcod, plndes
						 FROM  ".$wfachos."_000009 ,  ".$wfachos."_000007
						WHERE pememp ='".$responsableactual."'
						  AND pemest ='on'
						  AND plncod = pempln ";

			$res = mysql_query( $select, $conex );
			$array_planes_empresa = array();
			$planes =$select.'';
			$arra_planes_flujos   = array();
			$numeroflujos = 0;
			$ultimoflujo ='';
			$ultimorelacion ='';
			while ($rowres = mysql_fetch_assoc( $res ))
			{
				$array_planes_empresa[$rowres['pempln']] = $rowres['plndes'] ;
				$planes .=  "plan : ".$rowres['pempln'] ;
				$ultimorelacion = $rowres['pemcod'] ;
				$selectflujo = "SELECT serpro
								  FROM ".$wfachos."_000019
								 WHERE seremp = '".$responsableactual."'
								   AND serpln = '".$rowres['pempln']."'
								   GROUP BY serpro";

				$resflujo = mysql_query( $selectflujo, $conex );
				while ($rowresflujo = mysql_fetch_assoc( $resflujo ))
				{
					$arra_planes_flujos[$rowres['pempln']][$rowresflujo['serpro']];
					$numeroflujos ++;
					$ultimoflujo =$rowresflujo['serpro'];
				}
			}

			if(count($array_planes_empresa)> 0)
			{
				$html .=  $planes;

					$proceso = '';
					$respuesta['html'] = $html = "abrirModal";

					/*
						Se consulta el tipo de ingreso de este paciente si es por accidente de transito si
						es por accidente de transito se va ir por flujo de SOAT
					*/
					$ingresoAccidenteTransito      = consultarAliasPorAplicacion($conex, $wemp_pmla, "ingresoAccidenteTransito");

					//----------------------



					$select  = "SELECT Ingcai, Ingsei , Ccoflu , Ccoayu,Ccourg
								  FROM ".$wbasedato."_000101  , ".$wmovhos."_000011
								 WHERE Inghis  = '".$whistoria."'
								   AND Ingnin = '".$wingreso."'
								  AND Ingsei =  Ccocod";



					$res = mysql_query( $select, $conex );
					//$responsableactual = '';
					if ($rowres = mysql_fetch_assoc( $res ))
					{
						$causaIngreso 		=  $rowres['Ingcai'];
						$serviciodeingreso 	=  $rowres['Ingsei'];
						$flujocco 			=  $rowres['Ccoflu'];
						$ccoayu 			=  $rowres['Ccoayu'];
						$Ccourg 			=  $rowres['Ccourg'];
					}

					$whereflujo ='';
					// $ccoayu == 'off';
					// $ccoayu == 'off';
					//$ccoayu = 'off';
					if($Ccourg=='on')
					{

							$flujoscco = explode(",", $flujocco);

							if(count($flujoscco)==1)
							{
								$select  = "SELECT procod
										  FROM ".$wfachos."_000014
										 WHERE procod = '".$flujocco."' ";



								$res = mysql_query( $select, $conex );
								if ($rowres = mysql_fetch_assoc( $res ))
								{

									$whereflujo = "AND procod = '".$flujocco."'";
								}

							}
							else
							{
								for($e=0;$e<count($flujoscco) ; $e++)
								{
									$select  = "SELECT procod
												  FROM ".$wfachos."_000014
												 WHERE procod = '".$flujoscco[$e]."' ";



									$res = mysql_query( $select, $conex );
									if ($rowres = mysql_fetch_assoc( $res ))
									{
										if($e==0)
										{
											$whereflujo = " AND ( procod = '".$flujoscco[$e]."' ";
										}
										else
										{
											$whereflujo .= " OR procod = '".$flujoscco[$e]."' ";
										}

										if($e == count($flujoscco)-1)
										{
											$whereflujo .= ")";

										}
									}

								}
							}



					}
					else
					{

						if($ccoayu == 'on')
						{
							$select  = "SELECT procod
										  FROM ".$wfachos."_000014
										 WHERE procod = '".$flujocco."' ";



							$res = mysql_query( $select, $conex );
							if ($rowres = mysql_fetch_assoc( $res ))
							{

								$whereflujo = "AND procod = '".$flujocco."'";
							}



						}
						else
						{
							if($causaIngreso == $ingresoAccidenteTransito )
							{
								$whereflujo = "AND  procod = '".$causaIngreso."'";

							}
						}
					}


					 $selectflujo = "  SELECT DISTINCT procod , pronom, {$wfachos}_000014.id
										 FROM ".$wfachos."_000014,".$wfachos."_000019
										WHERE proest = 'on'
										  AND serpro = procod
										  AND seremp = '".$responsableactual."'
										";


					$selectflujo = $selectflujo." ".$whereflujo;
					//$selectflujo = "SELECT procod , pronom FROM ".$wfachos."_000014 ";


					$resflujo = mysql_query( $selectflujo, $conex );
					$contenidohtml .= "<br><br><div align='center' id='divplanesyflujos' ><input type='hidden' id='historiamodal' value='".$whistoria."'><input type='hidden' value='".$wingreso."' id='ingresomodal'><table><tr class='encabezadotabla' align = 'center'><td  colspan ='6'>Seleccione el flujo </td></tr><tr class='fila1'>";




					$contadorflujo=0;
					while ($rowresflujo = mysql_fetch_assoc( $resflujo ))
					{


						$contadorflujo++;
						$contenidohtml .='<td><input type="radio" class="radioflujo" name="flujo" value="'.$rowresflujo['procod'].'" onclick="crearlista()">'.$rowresflujo['pronom'].'</td>';

					}






					$contenidohtml .= "</tr></table>";

					$contenidohtml .= '<br>';
					$contenidohtml .= '<br>';

					$contenidohtml .= "<table  ><tr class='encabezadotabla' align = 'center'><td colspan ='".count($array_planes_empresa)."'>Seleccione el plan</td></tr><tr class='fila1'>";
					foreach( $array_planes_empresa as  $keyplan => $valueplan){

						$contenidohtml .= '<td><input class="radioplan" type="radio" name="plan" onclick="crearlista()" value="'.$keyplan.'">'.$valueplan.'</td>';

					}

					$contenidohtml .= "</tr></table><br><br></div><div id='divexito'></div>";
					$respuesta['tabla'] = "no entre por donde era";
					$respuesta['arrayplanes']  = $array_planes_empresa;
					$respuesta['contenidoDiv'] = $contenidohtml;

					if($contadorflujo==0)
					{
						$respuesta['contenidoDiv'] = "<br><b>No hay configurado plan ni flujo para esta empresa</b><br><br><br>
													<center><input  style='width:50px;' type='button' value='ok' onclick='onloadDesdeSoportes()'>";
						$respuesta['html'] = $html = "abrirModal";

					}
					echo json_encode($respuesta);
					return;


			}
			else
			{
				$respuesta['contenidoDiv'] = "<br><b>No hay configurado plan ni flujo para esta empresa</b><br><br><br>
													<center><input  style='width:50px;' type='button' value='ok' onclick='onloadDesdeSoportes()'>";
				$respuesta['html'] = $html = "abrirModal";
				echo json_encode($respuesta);
				//$html .= "NO tiene plan";
				return ;
			}

			// debo establecer si se debe meter en un flujo estandar , soat , PAF , no pbs

			// flujo PAF  voy a la tabla de empresas y miro si la empresa esta marcada como PAF  cliame_000024
			$select = "SELECT Emppaf
						 FROM ".$wbasedato."_000024
						WHERE Empcod = '".$responsableactual."'";

			$res = mysql_query( $select, $conex );
			$espaf = '';
			if ($rowres = mysql_fetch_assoc( $res ))
			{
				$espaf =  $rowres['Emppaf'];
			}

		}
	}


	//----------------------

	///------------------------------------------------
	//--> se redefinen los limites.2017-01-16




		$puedeCerrar    = 'off';
		$esAdmin        = 'off';
		//$proceso 	    = '01';



		$caracteres  = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£");
		$caracteres2 = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U");
		$estadoListado = 'SIN';
		$wcliame = 'cliame';

		$query = "SELECT temcod codigo
					FROM {$wfachos}_000017
				   WHERE temnom = 'facturacion'
				     AND temest = 'on'";

		//-- seguimiento
		$html  .= " seguimiento 1".$query;

		$rs    = mysql_query( $query, $conex );
		$row   = mysql_fetch_array( $rs );
		$wtema = $row['codigo'];

		$nivelesLimites = array();
		$query   = " SELECT dprini minimo, dprdes siguiente
					   FROM {$wfachos}_000018
					  WHERE dprori = '{$nivelUsuario}'
						AND dprcod = '{$proceso}'
						AND dprest = 'on'";
		$rs  = mysql_query( $query, $conex );
		$row = mysql_fetch_array( $rs );

		( trim($row['minimo']) == "" ) ? $row['minimo'] = "off" : $row['minimo'] = $row['minimo'] ;

		$nivelesLimites[0] = $row['minimo'];
		$nivelesLimites[2] = $row['siguiente'];

		$query   = " SELECT dprfin maximo
					   FROM {$wfachos}_000018
					  WHERE dprdes = '{$nivelUsuario}'
						AND dprcod = '{$proceso}'";
		$rs  = mysql_query( $query, $conex );

		//-- seguimiento
		$html  .= " seguimiento 2".$query;

		$row = mysql_fetch_array( $rs );
		( trim($row['maximo']) == "" ) ? $row['maximo'] = "off" : $row['maximo'] = $row['maximo'] ;
		$nivelesLimites[1] = $row['maximo'];

		$nivelMinimo    = $nivelesLimites[0];
		$nivelMaximo    = $nivelesLimites[1];
		$nivelSiguiente = $nivelesLimites[2];
		$hayDatos      = false;

		$entidades     = array();
		$planesArray   = array();
		$estadosArray  = array();
		$formatosArray = array();
		$serviciosVisitados     = array(); /*almacena los servicios que a utilizado el paciente según los centros de costos por los que ha pasado*/



		$cco 	   = array();
		$servicios = array();
		$serviciosVisitados = array();
		//-----------------------------------------------------------consulta de servicios prestados por la clinica ---------------------------------------------------------------//

		$query = " SELECT sercod codigo, serdes nombre, server verificacion
					 FROM {$wfachos}_000008
					WHERE serest = 'on'";
		$rs    = mysql_query( $query, $conex );


		//-- seguimiento
		$html  .= " seguimiento 3".$query;

		while( $row = mysql_fetch_array( $rs ) )
		{
			$servicios[$row['codigo']] = $row['verificacion'];
		}

		//-----------------------------------------------------------servicios usados por el paciente------------------------------------------------------------------------------//
		$auxserv = '';
		$query = " SELECT Ingsei codigo
					 FROM {$wbasedato}_000101
					WHERE Inghis  = '{$whis}'
					  AND Ingnin  = '{$wing}'";
		$rs    = mysql_query( $query, $conex );
		while( $row = mysql_fetch_array( $rs ) )
		{
			$cco[$row['codigo']] = "";
			$auxserv = $row['codigo'];

		}

		$querycco  = $query;

		//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

		//---------------------------------------------------------------------Asociación de centro de costos visitados por Servicios--------------------------------------------------------//
		foreach( $cco as $keyCco => $dato)
		{
			foreach( $servicios as $keyServicio=>$campoVerificacion)
			{
				if( !array_key_exists( $keyServicio, $serviciosVisitados ) )
				{
					$query = "SELECT {$campoVerificacion} valor
								FROM {$wmovhos}_000011
							   WHERE Ccocod = '{$keyCco}'
								 AND Ccoest = 'on'";

					//$querycco  .= $querycco."---------a";
					$rs    = mysql_query( $query, $conex );
					$row   = mysql_fetch_array( $rs );
					if( $row['valor'] == 'on' )
						$serviciosVisitados[$keyServicio] = "";
				}
			}
		}




		$responsablesArray 	    = array();
		$serviciosArray    	    = array();
		$empresas_planes        = array();
		$servicios_empresa_plan = array();
		$tabla= "";

		$auxserv = '';


		$auxserv = '';


		//-----------------------------------------------------------------------------SE CONSULTAN LOS PLANES ASOCIADOS A LA HISTORIA----------------------------------------------------------//
		/*El string resultante se transformará para consultar con la propiedad IN de mysql de tal manera que se consulten todo los soportes asociados a estos planes*/
		( $esAdmin == "on" ) ? $condicionNivelUsuario = "" : $condicionNivelUsuario = " AND ( lenrac = '{$nivelUsuario}' OR lencer='on' ) ";
		$query = " SELECT lenemp, lenobs observacion, lencer cerrado, id num, lenran nivelAnterior, lenrac nivelActual, fecha_data fechaCreacion, lenfeu fechaRecibo, lenhou horaRecibo, lenpro proceso
					 FROM {$wfachos}_000011
					WHERE lenhis = '{$whis}'
					  AND lening = '{$wing}'
					 {$condicionNivelUsuario}
					  AND lenest = 'on'";


		//-- seguimiento
		$html  .= " seguimiento 5".$query;

		$rs    = mysql_query( $query, $conex );

		while( $row = mysql_fetch_array( $rs ) )
		{
			$observacion   = $row['observacion'];
			$estadoListado = $row['cerrado'];
			$fechaCreacion = $row['fechaCreacion'];
			$nivelActual   = trim($row['nivelActual']);
			$nivelAnterior = $row['nivelAnterior'];
			$fechaRecibo   = $row['fechaRecibo'];
			$horaRecibo    = $row['horaRecibo'];
			$numeroDoc     = $row['num'];
			$codigoProceso = $row['proceso'];
			$proceso = $row['proceso'];
			$planes_empresa = $row[0];
			$planes_empresa = explode( ",", $planes_empresa );
			$puedeCerrar    = 'off';

			( count($planes_empresa) > 0 and (trim($planes_empresa[0]) != "")) ? $hayPlanesEmpresa = true : $hayPlanesEmpresa = false;

			$query = " SELECT roldes
						 FROM {$wfachos}_000015
						WHERE rolcod = '{$nivelActual}'";
			$rs2  = mysql_query( $query, $conex );
			$row2 = mysql_fetch_array( $rs2 );
			$etapa = $row2[0];
		}
		$respuesta['tabla'] = 'hayplanes';
		//-------------------------------------------------------------CONSULTA QUE ARMA EL LISTADO A PRESENTAR EN PANTALLA---------------------------------------------------------------------/
		if($hayPlanesEmpresa)
		{
			//-----------------------------------------------------------> consulta el nombre del proceso que rije su revisión <----------------------------------------------------------------//

			$query = " SELECT pronom nombre
						 FROM {$wfachos}_000014
						WHERE procod = '{$codigoProceso}'";
			$rs	   = mysql_query( $query, $conex );
			$row   = mysql_fetch_array( $rs );
			$row['nombre'] = str_replace( $caracteres, $caracteres2, $row['nombre'] );
			$nombreProceso = $row['nombre'];

			//----------------------------------------------------------->consulta los soportes que ya están guardados<---------------------------------------------/

			$query = "SELECT sopcod codigo, sopnom nombre, Pememp codigoEmpresa, Pempln codigoPlan, a.id numLista, delsop soporte, delemp empPlan, delfor formato, delobs observacion, delest estado, delres responsable, delser servicio , soptip tipo, soptif formatoDefecto
					FROM {$wfachos}_000011 a, {$wfachos}_000012 ,  {$wfachos}_000006 , {$wfachos}_000009 b , {$wfachos}_000010
				   WHERE lenhis = '{$whis}'
					 AND lening = '{$wing}'
					 {$condicionNivelUsuario}
					 AND delhis = lenhis
					 AND deling = lening
					 AND lenest = 'on'
					 AND delsop  = Sopcod
					 AND b.pemcod = delemp
					 AND b.pemcod = sesepl
			GROUP BY Pememp, Pempln, delser, delsop
			ORDER BY delsop";
			$rs = mysql_query( $query, $conex ) or die( mysql_error() );


			while( $row = mysql_fetch_array($rs) )//ACA SE ESTAN GUARDANDO EN EL ARREGLO AQUELLOS SOPORTES QUE YA TIENEN ALGUN TIPO DE CONFIGURACIÓN EN EL LISTADO
			{

				$hayDatos = true;
				$row['nombre'] = str_replace( $caracteres, $caracteres2, $row['nombre'] );
				$consolidado[$row['codigoEmpresa']][$row['codigoPlan']][$row['servicio']][$row['soporte']]['estado']  = $row['estado'];
				$consolidado[$row['codigoEmpresa']][$row['codigoPlan']][$row['servicio']][$row['soporte']]['nombre']  = $row['nombre'];
				$consolidado[$row['codigoEmpresa']][$row['codigoPlan']][$row['servicio']][$row['soporte']]['existe']  = "s";
				$consolidado[$row['codigoEmpresa']][$row['codigoPlan']][$row['servicio']][$row['soporte']]['empPlan'] = $row['empPlan'];
				$consolidado[$row['codigoEmpresa']][$row['codigoPlan']][$row['servicio']][$row['soporte']]['formato'] = $row['formato'];
				$consolidado[$row['codigoEmpresa']][$row['codigoPlan']][$row['servicio']][$row['soporte']]['formatoDefecto'] = $row['formatoDefecto'];
				$consolidado[$row['codigoEmpresa']][$row['codigoPlan']][$row['servicio']][$row['soporte']]['tipo'] = $row['tipo'];
				$consolidado[$row['codigoEmpresa']][$row['codigoPlan']][$row['servicio']][$row['soporte']]['responsable'] = $row['responsable'];
				$consolidado[$row['codigoEmpresa']][$row['codigoPlan']][$row['servicio']][$row['soporte']]['observacion'] = str_replace( $caracteres, $caracteres2, $row['observacion'] );

				$empresas_planes[$row['codigoEmpresa']][$row['codigoPlan']]['codigo'] = $row['empPlan'];
				$servicios_empresa_plan[$row['empPlan']][$row['servicio']] 			  = '';

				$planEncontrado = array_search( $row['empPlan'], $planes_empresa );

			}

			foreach( $planes_empresa as $i => $codigo )
			{
				$planes_empresa[$i] = "'{$codigo}'";
			}

			$planes_empresa = implode( ",", $planes_empresa );
			//-------------------------------------------------------------->consulta soportes que aun no existen<------------------------------------------------------------------------------/
			if( trim($planes_empresa) != "" )
			{
				$tmp  = "tmpSoportes{$hoy1}_{$hora1}";
				$qaux = "DROP TABLE IF EXISTS $tmp";
				$resdr = mysql_query($qaux,$conex) or die (mysql_errno().":".mysql_error());
				$query = "CREATE TEMPORARY TABLE IF NOT EXISTS {$tmp}
							(INDEX idx(soporte, empPlan))
							SELECT sesepl empPlan, sessop soporte, sopnom nombre, soptif formato, '' nivelAnterior, '' nivelActual, '' observacion, 'na' estado, serres responsable, sesser servicios, pememp codigoEmpresa, Pempln codigoPlan, soptip tipo
							FROM {$wfachos}_000006, {$wfachos}_000009 b, {$wfachos}_000010, {$wfachos}_000019
						   WHERE sesepl IN ({$planes_empresa})
							 AND sopcod = sessop
							 AND b.pemcod = sesepl
							 AND seremp = b.pememp
							 AND serpln = b.pempln
							 AND sersop = sopcod
							 AND serres = '{$nivelUsuario}'
							 AND serpln = '{$wplan}'
						   GROUP BY codigoEmpresa, codigoPlan, soporte, sesser
						   ORDER BY soporte";
				$rs = mysql_query( $query, $conex ) or die( mysql_error() );

				foreach( $serviciosVisitados as $kservicio=>$kdato)
				{
					$auxserv .= "  ".$kservicio;
				}



				$varaux = $query ;
				//echo $query;
				$query = "SELECT *
							FROM {$tmp}";
				$rs = mysql_query( $query, $conex );



				while( $row = mysql_fetch_array($rs) )
				{
					( trim($row['servicios']) == "") ? $aplicaEnTodos = true : $aplicaEnTodos = false;  //variable para controlas si un soporte aplica para todos los servicios o solo para los que está filtrado;

					$servicios = explode( ",", $row['servicios']);// separo los servicios en los que aplica dicho soporte


					foreach( $serviciosVisitados as $keyServicio => $dato )// se recorre el arreglo de servicios utilizados por el paciente
					{

						$hayDatos = true;
						$entro1="1";

						if( in_array( $keyServicio, $servicios) or ($aplicaEnTodos) )//se pregunta si el soporte aplica para cada servicio, sea por especificación o porque el soporte no tiene filtro de servicios
						{
							$entro2="2";
							$keyServicio = 'n';
							if(!isset($consolidado[$row['codigoEmpresa']][$row['codigoPlan']][$keyServicio][$row['soporte']]))//ESTO FILTRA QUE EL SOPORTE NO EXISTA YA EN LA TABLA, DE SER ASÍ SE CONSERVAN LOS DATOS YA GUARDADOS EN {$wfachos}_000012
							{
								$entro3="3";
								$row['nombre'] = str_replace( $caracteres, $caracteres2, $row['nombre'] );
								$consolidado[$row['codigoEmpresa']][$row['codigoPlan']][$keyServicio][$row['soporte']]['estado']  = $row['estado'];
								$consolidado[$row['codigoEmpresa']][$row['codigoPlan']][$keyServicio][$row['soporte']]['existe']  = "n";
								$consolidado[$row['codigoEmpresa']][$row['codigoPlan']][$keyServicio][$row['soporte']]['nombre']  = $row['nombre'];
								$consolidado[$row['codigoEmpresa']][$row['codigoPlan']][$keyServicio][$row['soporte']]['empPlan'] = $row['empPlan'];
								$consolidado[$row['codigoEmpresa']][$row['codigoPlan']][$keyServicio][$row['soporte']]['formato'] = $row['formato'];
								$consolidado[$row['codigoEmpresa']][$row['codigoPlan']][$keyServicio][$row['soporte']]['formatoDefecto'] = $row['formato'];
								$consolidado[$row['codigoEmpresa']][$row['codigoPlan']][$keyServicio][$row['soporte']]['tipo'] 	  = $row['tipo'];
								$consolidado[$row['codigoEmpresa']][$row['codigoPlan']][$keyServicio][$row['soporte']]['responsable'] = $row['responsable'];
								$consolidado[$row['codigoEmpresa']][$row['codigoPlan']][$keyServicio][$row['soporte']]['observacion'] = $row['observacion'];
								//$consolidado[$row['codigoEmpresa']][$row['codigoPlan']][$keyServicio][$row['soporte']]['observacion'] = $row['observacion'];

								$empresas_planes[$row['codigoEmpresa']][$row['codigoPlan']]['codigo'] = $row['empPlan'];
								$servicios_empresa_plan[$row['empPlan']][$keyServicio] = '';

								$query = "INSERT INTO {$wfachos}_000012(Medico, Fecha_data, Hora_data, delhis, deling, delsop, delemp, delser, delfor, delest, delres, delobs, seguridad)
											   VALUES('fachos', '{$hoy}', '{$hora}', '{$whis}', '{$wing}', '{$row['soporte']}', '{$row['empPlan']}', '{$keyServicio}', '', '{$row['estado']}', '{$row['responsable']}', '', '{$usuario}' )";


								//$seguimiento   .= "--".$query;
								$rsAux = mysql_query( $query, $conex );
								$varaux2 = $varaux2."--".$query;
							}
						}
					}

				}
			}
			$respuesta['tabla'] = 'haydatos';
			if($hayDatos)
			{

				//-------------------------------------------------------------------CONSULTO LAS EMPRESAS------------------------------------------------------------------------------//

			}
			//$tabla .= $seguimiento;


		}
	///------------------------------------------------

	$query = "SELECT sopcod, sopnom
				FROM {$wfachos}_000012, {$wfachos}_000006
			   WHERE delsop = sopcod
					 AND delhis = '".$whistoria."'
					 AND deling = '".$wingreso."' ";

	$rs = mysql_query( $query, $conex );

	$htmltabla .="<b>Para el siguiente paciente se pediran los siguientes soportes</b><br><br><table>

					<tr>
					 <td class='encabezadoTabla' >Recibido</td><td  class='encabezadoTabla'>Codigo</td><td  class='encabezadoTabla'>Soportes</td></tr>

					</tr>";
	$rs = mysql_query( $query, $conex ) or die( mysql_error() );
	$imprimotabla='no';
	while( $row = mysql_fetch_array( $rs ) )
	{
		$htmltabla .="<tr>
						<td class='fila1' align='center'><input type='checkbox' class='checkboxlista' soporte='".$row['sopcod']."' id='checkboxsoporte_".$row['sopcod']."' name='opciones_".$row['sopcod']."'  onclick='actualizarEstadoSoporte(\"si\" , \"".$row['sopcod']."\" , \"".$whistoria."\" , \"".$wingreso."\")' title='Si' value='si'></td>
						<td class='fila1'>".$row['sopcod']."</td><td class='fila1'>".$row['sopnom']."</td></tr>";

		/*
		$htmltabla .="<tr>
						<td class='fila1' align='center'><input type='radio' name='opciones_".$row['sopcod']."'  onclick='actualizarEstadoSoporte(\"si\" , \"".$row['sopcod']."\" , \"".$whistoria."\" , \"".$wingreso."\")' title='Si' value='si'></td><td class='fila1' title='No' align='center' ><input type='radio' name='opciones_".$row['sopcod']."' value='no' onclick='actualizarEstadoSoporte(\"no\" , \"".$row['sopcod']."\" , \"".$whistoria."\" , \"".$wingreso."\")'></td><td class='fila1' title='NA'   align='center'><input type='radio' name='opciones_".$row['sopcod']."' value='NA' onclick='actualizarEstadoSoporte(\"na\" , \"".$row['sopcod']."\" , \"".$whistoria."\" , \"".$wingreso."\")'></td>
						<td class='fila1'>".$row['sopcod']."</td><td class='fila1'>".$row['sopnom']."</td></tr>";
	   */
	   $imprimotabla ='si';
	}
	$htmltabla .="</table>";

	//<tr><td colspan='2'></td><td ><input  style='width:100px; align:right'  type='button' Value='Enviar' onclick='enviarnivelsiguiente()'></td></tr>
	$respuesta['tabla'] = utf8_encode($htmltabla);
	$respuesta['query'] = $query;
	$respuesta['imprimotabla'] = $imprimotabla;
	$respuesta['html'] =  "";
	$respuesta['exito'] = "si";
	//$respuesta['tabla'] = $htmltabla;
	$respuesta['mensajeexitoso'].= "Se inserto con exito";
	echo json_encode($respuesta);
	return;
}

if (isset($accion) and $accion == 'llenarTablaDatosLog')
{
	global $wbasedato;
	global $conex;

	$data = array('error'=>0,'mensaje'=>'','html'=>'');

	//se pregunta si existe un registro con ese usuario
	$sql1 = "select *
			from ".$wbasedato."_000163
			where Logusu='".utf8_decode($key)."'
				";
	$res1 = mysql_query( $sql1, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla ".$wbasedato."000163 para el usuario ".$key." ".mysql_errno()." - Error en el query $sql1 - ".mysql_error() ) );
	if ($res1)
	{
		$num1=mysql_num_rows($res1);
		if ($num1>0)
		{
			$sql =  " update ".$wbasedato."_000163
			set Logdes = '".utf8_decode( $cadena )."'
			where Logusu ='".$key."'
			";
			$res = mysql_query($sql,$conex) or ( $data[ 'mensaje' ] = utf8_encode( "Error actualizando la tabla ".$wbasedato."000163 " .mysql_errno()." - Error en el query $sql - ".mysql_error() ) );
			if (!$res)
			{
				$data[ 'error' ] = 1; //sale el mensaje de error
			}
		}
		else
		{
			//se inserta en la tabla 163 de log de almacenamiento
			$sql=" INSERT INTO ".$wbasedato."_000163 (Medico, Fecha_data, Hora_data, Logusu, Logdes, Logest, Seguridad)
					VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$key."', '".utf8_decode( $cadena )."', 'on', 'C-".$wbasedato."' )";

			$res = mysql_query( $sql, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error insertando la tabla ".$wbasedato."000163 ".mysql_errno()." - Error en el query $sql - ".mysql_error() ) );
			if (!$res){
				$data['error']=1;
			}
		}
	}
	else
	{
		$data['error']=1;
	}

	echo json_encode($data);
	return;
}

if (isset($accion) and $accion == 'traerTablaDatosLog')
{
	global $wemp_pmla;
	$wbasedato  = consultarAplicacion2($conex,$wemp_pmla,"cliame");
	global $conex;

	$data = array('error'=>0,'mensaje'=>'','html'=>'');
	//se inserta en la tabla 163 de log de almacenamiento

	$sql = "select Logdes
				from ".$wbasedato."_000163
				where Logusu='".utf8_decode($key)."'
				and Logest='on'
				";

	$res = mysql_query( $sql, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla ".$wbasedato."000163 ".mysql_errno()." - Error en el query $sql - ".mysql_error() ) );
	if ($res)
	{
		$num=mysql_num_rows($res);
		if ($num >0){
			$rows=mysql_fetch_array($res);
			$data['html'] = utf8_encode($rows['Logdes']);
		}
	}
	else
	{
		$data['error']=1;
	}

	echo json_encode($data);
	return;
}

if (isset($accion) and $accion == 'borrarTablaDatosLog')
{
	global $wbasedato;
	global $conex;

	$data = array('error'=>0,'mensaje'=>'','html'=>'');
	//se inserta en la tabla 163 de log de almacenamiento

	$sql = "delete from ".$wbasedato."_000163
				where Logusu='".utf8_decode($key)."'
				and Logest='on'
				";

	$res = mysql_query( $sql, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error borrando el registro de la tabla ".$wbasedato."000163 ".mysql_errno()." - Error en el query $sql - ".mysql_error() ) );
	if (!$res){
		$data['error']=1;
	}
	//$data['mensaje']=$sql;

	echo json_encode($data);
	return;
}

if (isset($accion) and $accion == 'consultarIpsRemite')
{
	$data = array('error'=>0,'mensaje'=>'','html'=>'','valor'=>'','usu'=>'');
	$json = consultarIpsQueRemite( $q, $wbasedato );
	echo $json;
	exit;
}

/************************************************************************
 * Agosto 13 de 2012
 ************************************************************************/
if (isset($accion) and $accion == 'guardarDatosPreadmision')
{
	$data = array('error'=>0,'mensaje'=>'','html'=>'','data'=>'','historia'=>'','ingreso'=>'','documento'=>'');

	global $wbasedato;
	global $conex;
	global $pac_ciuhidMunNac;
	global $ing_histxtNumHis;
	global $ing_cemhidCodAse;
	global $ing_seisel_serv_ing;

	$modoConsulta = false;

	$aplicacion=consultarAplicacion2( $conex, $wemp_pmla, "movhos" );

	//Consultar la historia e ingreso nuevo
	//1. Consultar por documento
	//	- Si el documento no se encuentra en la BD, significa que la historia no existe para el paciente
	//	- Si no se encuentra el documento, consultar la nueva historia
	//	- Si existe el documento, consulto la historia y el numero de ingreso nuevo (Esto es sumarle uno al último ingreso)

	/***consulta o actualizacion de historia e ingreso***/
	if( empty ($documento) ) //se consulta si ese documento existe
	{
		$data[ 'error' ] = 1;
		$data[ 'error' ] = "El documento esta vacio";
	}
	else
	{
	   /**ingreso**/
		$sql = "";
		if( $aplicacion == "" ){
			//verifico que el paciente este activo
			$sql = "select *
					from ".$wbasedato."_000100 a
					where Pactdo = '".utf8_decode($tipodoc)."'
					and Pacdoc = '".utf8_decode($documento)."'
					and pacact = 'on'";
		}else{
			//Si ubiald != 'on' es porque todavia está hospitalizado o activo
			$sql = "select a.*
					from ".$wbasedato."_000100 a, ".$aplicacion."_000018 b
					where Pactdo = '".utf8_decode($tipodoc)."'
					and Pacdoc = '".utf8_decode($documento)."'
					and Pachis = Ubihis
					and Ubiald != 'on'
					ORDER BY b.fecha_data desc, b.hora_data desc limit 1";
		}

		$res = mysql_query( $sql, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query $sql - ".mysql_error() ) );
		$num = mysql_num_rows( $res );

		//Solo se puede hacer preadmisión si el paciente no esta activo
		if( $num == 0 )
		{
			//Consulto si existe el registo
			$sql = "select Ingtdo,Ingdoc,a.id
					from ".$wbasedato."_000167 a, ".$wbasedato."_000166 b
					where Ingdoc = '".utf8_decode($documento)."'
					and Ingtdo = '".$pac_tdoselTipoDoc."'
					and Pacdoc = Ingdoc
					and Pactdo = Ingtdo
					and Pacact = 'on'
					and Pacidi = a.id";

			$res = mysql_query( $sql, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query $sql - ".mysql_error() ) );

			if( $res )
			{
				$tarifa = "";
				$num = mysql_num_rows( $res );
				if( $ing_tpaselTipRes == 'P' ){
					if( $cambiaTarifaParticular == "on" ){
						$tarifa = $ing_tarhid;
					}else{
						$ing_cemhidCodAse = consultarAplicacion2($conex,$wemp_pmla,"codigoempresaparticular");
						$tarifa = consultarAplicacion2($conex,$wemp_pmla,"tarifaparticular");
					}
				}else{
					//se consulta la tarifa para guardarle dependiendo de la empresa
					$sqlt = "select Emptar
					from ".$wbasedato."_000024
					where Empcod = '".utf8_decode($ing_cemhidCodAse)."'
					and Empest = 'on'";

					$rest = mysql_query( $sqlt, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query $sqlt - ".mysql_error() ) );

					if( $rest ){
						$numt=mysql_num_rows($rest);
						if ($numt>0)
						{
							$rowst=mysql_fetch_array($rest);
							$tar=explode("-",$rowst['Emptar']);
							$tarifa=$tar[0];
						}
					}
				}

				//Si no se encontraron los datos, significa que es un registro nuevo
				if( $num == 0 ) //hace el insert
				{
					$datosEnc = crearArrayDatos( $wbasedato, "ing", "ing_", 3 );

					//datos adicionales
					$datosEnc[ "ingusu" ] = $key; //usuario
					//$datosEnc[ "inghis" ] = $historia; //historia
					//$datosEnc[ "ingnin" ] = $ingreso; //ingreso
					$datosEnc[ "ingtin" ] = $datosEnc[ "ingsei" ]; //tipo ingreso
					$datosEnc[ "ingtar" ] = $tarifa; //tarifa

					$cambiarCodigoEntidad = consultarAplicacion2( $conex, $wemp_pmla, "responsable101particular" );
					if($cambiarCodigoEntidad == "c"){
						$datosEnc[ "ingcem" ] = consultarAplicacion2($conex,$wemp_pmla,"codigoempresaparticular");
					}else{
						$datosEnc[ "ingcem" ] = $responsables1[0]['ing_cemhidCodAse'];
					}
					$datosEnc[ "ingtpa" ] = $responsables1[0]['ing_tpaselTipRes'];
					$datosEnc[ "ingpla" ] = $responsables1[0]['ing_plaselPlan'];
					$datosEnc[ "ingpol" ] = $responsables1[0]['ing_poltxtNumPol'];
					$datosEnc[ "ingnco" ] = $responsables1[0]['ing_ncotxtNumCon'];


					if ($aplicacion != "")
					{
						$servicioIng1=explode("-",$ing_seisel_serv_ing);
						$servicioIng=$servicioIng1[0];
						$datosEnc[ "ingsei" ] = $servicioIng1[1]; //servicio de ingreso
						$datosEnc[ "ingtin" ] = $servicioIng; //tipo ingreso
						$datosEnc[ "ingtin" ] = consultarTipoIngreso( $aplicacion, $servicioIng1[1] ); //tipo ingreso
					}
					//No se graba ni historia ni ingreso en la tabla 000167(preadmision datos de ingreso del paciente)
					//En preadmisiones no se crea ni historia ni ingreso
					//Pero tiene documento y tipo de documento
					unset( $datosEnc[ "inghis" ] );
					unset( $datosEnc[ "ingnin" ] );

					$datosEnc[ "ingtdo" ] = $pac_tdoselTipoDoc;
					$datosEnc[ "ingdoc" ] = $documento;

					//SI ES ACCIDENTE DE TRANSITO, LOS CAMPOS LIGADOS A UN RESPONSABLE VAN EN BLANCO
					//2014-03-17
					if( !empty( $ing_caiselOriAte ) && $ing_caiselOriAte == '02' ){
						$datosEnc[ "inghoa" ] = "";
						$datosEnc[ "ingpco" ] = "";
						$datosEnc[ "ingcac" ] = "";
						$datosEnc[ "ingnpa" ] = "";
						$datosEnc[ "ingfha" ] = "";
						$datosEnc[ "ingpla" ] = "";
						$datosEnc[ "ingord" ] = "";
					}

					$sqlInsert = crearStringInsert( $wbasedato."_000167", $datosEnc );

					$resEnc = mysql_query( $sqlInsert, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() ) );
					if($resEnc){
						if( mysql_affected_rows() > 0 )
						{	
						}
						//si inserto la 101
						/******************************************************************************
						 * Agosto 23 de 2013
						 ******************************************************************************/
						//Guardo el id del último insert de datos de ingreso de preadmisión
						$idIngreso = mysql_insert_id();
						/******************************************************************************/
						$data[ "mensaje" ] = utf8_encode( "Se registró la preadmisión correctamente" );
					}
					else
					{
						$data[ "error" ] = 1;
						$data[ "mensaje" ] = utf8_encode( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() );
					}
				}
				else //hace la actualizacion
				{
					$rowsEnc = mysql_fetch_array( $res );
					//Si se encontraron datos, significa que es una actualización
					$datosTabla = crearArrayDatos( $wbasedato, "ing", "ing_", 3 );

					$datosTabla[ 'id' ] = $rowsEnc[ 'id' ];
					$datosTabla[ "ingtar" ] = $tarifa; //tarifa

					//No se graba ni historia ni ingreso en la tabla 000167(preadmision datos de ingreso del paciente)
					//En preadmisiones no se crea ni historia ni ingreso
					//Pero tiene documento y tipo de documento
					unset( $datosTabla[ "inghis" ] );
					unset( $datosTabla[ "ingnin" ] );

					$datosTabla[ "ingtdo" ] = $pac_tdoselTipoDoc;
					$datosTabla[ "ingdoc" ] = $documento;

					if ($aplicacion != "")
					{
						$servicioIng1=explode("-",$ing_seisel_serv_ing);
						$servicioIng=$servicioIng1[0];
						// $datosEnc[ "ingsei" ] = $servicioIng; //servicio de ingreso
						// $datosEnc[ "ingtin" ] = $servicioIng; //tipo ingreso
						$datosTabla[ "ingsei" ] = $servicioIng1[1]; //servicio de ingreso
						$datosTabla[ "ingtin" ] = $servicioIng; //tipo ingreso
						$datosTabla[ "ingtin" ] = consultarTipoIngreso( $aplicacion, $servicioIng1[1] ); //tipo ingreso
					}

					//datos del primer responsable no accidente
					$datosTabla[ "ingcem" ] = $responsables1[0]['ing_cemhidCodAse'];
					$datosTabla[ "ingtpa" ] = $responsables1[0]['ing_tpaselTipRes'];
					$datosTabla[ "ingpla" ] = $responsables1[0]['ing_plaselPlan'];
					$datosTabla[ "ingpol" ] = $responsables1[0]['ing_poltxtNumPol'];
					$datosTabla[ "ingnco" ] = $responsables1[0]['ing_ncotxtNumCon'];

					//SI ES ACCIDENTE DE TRANSITO, LOS CAMPOS LIGADOS A UN RESPONSABLE VAN EN BLANCO
					//2014-03-17
					if( !empty( $ing_caiselOriAte ) && $ing_caiselOriAte == '02' ){
						$datosEnc[ "inghoa" ] = "";
						$datosEnc[ "ingpco" ] = "";
						$datosEnc[ "ingcac" ] = "";
						$datosEnc[ "ingnpa" ] = "";
						$datosEnc[ "ingfha" ] = "";
						$datosEnc[ "ingpla" ] = "";
						$datosEnc[ "ingord" ] = "";
					}

					$sqlUpdate = crearStringUpdate( $wbasedato."_000167", $datosTabla );
					$res1 = mysql_query( $sqlUpdate, $conex ) or ( $data[ 'error' ] = utf8_encode( mysql_errno()." - Error en el query $sqlUpdate - ".mysql_error() ) );

					if( $res1 )
					{
						if( mysql_affected_rows() > 0 ){
						}
						$data[ "mensaje" ] = utf8_encode( "Se actualizo correctamente" );
						$modoConsulta = true;
					}
					else
					{
						$data[ "error" ] = 1;
						$data[ "mensaje" ] = utf8_encode( mysql_errno()." - Error en el query $sqlUpdate - ".mysql_error() );
					}
				}
			}
			/**fin ingreso**/

			if( $data[ "error" ] == 0 )
			{
				/**admision**/
				//Consulto si existe el registo
				$sql1 = "select Pacdoc,a.id
						from ".$wbasedato."_000166 a,".$wbasedato."_000167 b
						where a.Pactdo = '".utf8_decode($pac_tdoselTipoDoc)."'
						and a.Pacdoc = '".utf8_decode($documento)."'
						and b.Ingtdo =a.Pactdo
						and b.Ingdoc =a.Pacdoc
						and a.Pacidi = b.id
						and a.Pacact = 'on'";

				$res1 = mysql_query( $sql1, $conex ) or ( $data[ 'error' ] = utf8_encode( mysql_errno()." - Error en el query $sql1 - ".mysql_error() ) );
				if( $res1 )
				{
					$num1 = mysql_num_rows( $res1 );
					//Si no se encontraron los datos, significa que es un registro nuevo
					if( $num1 == 0 ) //hace el insert
					{
						$tipoAtencion=$datosEnc[ "ingsei" ];//se llena porque en el siguiente renglon se sobre escribe
						$datosEnc = crearArrayDatos( $wbasedato, "pac", "pac_", 3 );
						//datos adicionales
						// $datosEnc[ "ingusu" ] = $key; //usuario
						unset( $datosEnc[ "pacciu" ] ); //elimina la posicion
						$datosEnc[ "paciu" ] = $pac_ciuhidMunNac;
						$datosEnc[ "pacact" ] = 'on'; //activo
						$datosEnc[ "pactat" ] = $tipoAtencion;//tipo atencion

						if($aplicacion != "")
						{
							$servicioIng1=explode("-",$ing_seisel_serv_ing);
							$servicioIng=$servicioIng1[0];
							// $datosEnc[ "pactat" ] = $servicioIng; //tipo atencion
							$datosEnc[ "pactat" ] = consultarTipoAtencion( $aplicacion, $servicioIng1[1] ); //tipo ingreso
						}

						$datosEnc[ "pacidi" ] = $idIngreso;
						unset( $datosEnc[ "pachis" ] );
						$datosEnc['pacap1'] = strtoupper($datosEnc['pacap1']);
						$datosEnc['pacap2'] = strtoupper($datosEnc['pacap2']);
						$datosEnc['pacno1'] = strtoupper($datosEnc['pacno1']);
						$datosEnc['pacno2'] = strtoupper($datosEnc['pacno2']);

						$sqlInsert = crearStringInsert( $wbasedato."_000166", $datosEnc );
						$resEnc = mysql_query( $sqlInsert, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query admision $sqlInsert - ".mysql_error() ) );

						if($resEnc){
							if( mysql_affected_rows() > 0 ){
							}
							$data[ "mensaje" ] = utf8_encode( "Se registró correctamente" );
						}
						else{
							$data[ "error" ] = 1;
							// $data[ "mensaje" ] = utf8_encode( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() );
						}
					}
					else //hace la actualizacion
					{
						$rowsEnc = mysql_fetch_array( $res1 );

						//Si se encontraron datos, significa que es una actualización
						$datosTabla = crearArrayDatos( $wbasedato, "pac", "pac_", 3 );

						unset( $datosTabla[ "pacciu" ] ); //elimina la posicion
						$datosTabla[ "paciu" ] = $pac_ciuhidMunNac;
						$datosTabla[ 'id' ] = $rowsEnc[ 'id' ];

						//En preadmision no hay historia
						unset( $datosTabla[ "pachis" ] );

						$sqlUpdate = crearStringUpdate( $wbasedato."_000166", $datosTabla );
						$res2 = mysql_query( $sqlUpdate, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query $sqlUpdate - ".mysql_error() ) );

						if( $res2 )
						{
							$data[ "mensaje" ] = utf8_encode( "Se actualizo correctamente" );
							$modoConsulta = true;
							if( mysql_affected_rows() > 0 )
							{
							}
							else
							{
								// $data[ "mensaje" ] = utf8_encode( "No se actualizo porque no se registraron cambios" );
							}
						}
						else
						{
							$data[ "error" ] = 1;
							// $data[ "mensaje" ] = utf8_encode( mysql_errno()." - Error en el query $sqlUpdate - ".mysql_error() );
						}
					}
				}
			}


				/******************* Guardar en la tabla de responsables 000216****************/
			if( $data[ "error" ] == 0 )
			{
				if (!empty ($responsables1) && count($responsables1)>0)
				{
					/*se borran los registros para volver a insertarlos*/
					$sqlDel="delete from ".$wbasedato."_000216
							 where restdo = '".$pac_tdoselTipoDoc."'
							 and resdoc = '".$documento."'";

					$resDel = mysql_query( $sqlDel, $conex ) or ( $data[ 'error' ] = utf8_encode( mysql_errno()." - Error en el query $sqlDel - ".mysql_error() ) );
					if (!$resDel)
					{
						$data['error'] = 1;
					}
					/*fin borrado de registros*/

					$ordenRes = 1;
					//para pasar de responsable en responsable
					$cantidadResponsables = count( $responsables1 );
					foreach( $responsables1 as $keRes => $valueRes )
					{
						 unset( $datosEnc ); //se borra el array
						//se guardan todos los responsables
						$datosEnc[ "Medico" ] = $wbasedato;
						$datosEnc[ "Fecha_data" ] = date("Y-m-d");
						$datosEnc[ "Hora_data" ] = date( "H:i:s" );
						$datosEnc[ "restdo" ] = $pac_tdoselTipoDoc;
						$datosEnc[ "resdoc" ] = $documento;
						$datosEnc[ "restid" ] = $valueRes['res_tdo'];
						$datosEnc[ "resnit" ] = $valueRes['ing_cemhidCodAse']; //codigo responsable 11 para particular
						$datosEnc[ "resnom" ] = $valueRes['res_nom'];
						$datosEnc[ "resord" ] = $ordenRes; //orden del responsable el tr en el que esta
						if ($ordenRes == 1){ //solo en el primer responsble se envia la fecha inicial
							$datosEnc[ "resfir" ] = date("Y-m-d");
						}else{
							$datosEnc[ "resfir" ] = '';
						}

						/*if( $datosEnc[ "resord" ]*1 == 1 and $cantidadResponsables == 1 and ( $datosEnc[ "resfir" ] == "0000-00-00" or $datosEnc[ "resfir" ] == "" ) ){
							$datosEnc[ "resfir" ] = $fechaIngreso;
						}*/

						$datosEnc[ "resest" ] =	'on';
						$datosEnc[ "restpa" ] =	$valueRes['ing_tpaselTipRes'];
						$datosEnc[ "respla" ] =	$valueRes['ing_plaselPlan'];
						$datosEnc[ "respol" ] =	$valueRes['ing_poltxtNumPol'];
						$datosEnc[ "resnco" ] =	$valueRes['ing_ncotxtNumCon'];
						$datosEnc[ "resfir" ] =	$valueRes['res_firtxtNumcon'];
						$datosEnc[ "resffr" ] =	$valueRes['res_ffrtxtNumcon'];
						$datosEnc[ "rescom" ] =	$valueRes['res_comtxtNumcon'];
						$datosEnc[ "resaut" ] =	$valueRes['ing_ordtxtNumAut'];
						$datosEnc[ "resfha" ] =	$valueRes['ing_fhatxtFecAut'];
						$datosEnc[ "reshoa" ] =	$valueRes['ing_hoatxtHorAut'];
						$datosEnc[ "resnpa" ] =	$valueRes['ing_npatxtNomPerAut'];
						$datosEnc[ "respco" ] =	$valueRes['ing_pcoselPagCom'];
						$datosEnc[ "Seguridad" ] =	"C-".$wbasedato;

						$sqlInsert = crearStringInsert( $wbasedato."_000216", $datosEnc );
						$resEnc = mysql_query( $sqlInsert, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error grabando en la tabla ".$wbasedato."_000216 - ".mysql_error() ) );

						if (!$resEnc){
							$data['error']=1;
						}else{ //20140221

							if( $ordenRes == 1 ){
							$sqlDel="DELETE FROM ".$wbasedato."_000217
									  WHERE Cprnit = '".$valueRes['ing_cemhidCodAse']."'
										AND Cpraut = '".$valueRes['ing_ordtxtNumAut']."' ";
							$sqlDel="DELETE FROM ".$wbasedato."_000217
									  WHERE Cprtdo = '".$pac_tdoselTipoDoc."'
										AND Cprdoc = '".$documento."' ";
							$resDel = mysql_query( $sqlDel, $conex ) or ( $data[ 'error' ] = utf8_encode( mysql_errno()." - Error en el query $sqlDel - ".mysql_error() ) );
							}

							//Insertar en la tabla de cups
							if( isset($valueRes['cups']) ){
								foreach( $valueRes['cups'] as $valueCup )
								{
									 unset( $datosEnc ); //se borra el array
									//se guardan todos los responsables

									$datosEnc[ "Cprtdo" ] = $pac_tdoselTipoDoc;
									$datosEnc[ "Cprdoc" ] = $documento;

									$datosEnc[ "Medico" ] = $wbasedato;
									$datosEnc[ "Fecha_data" ] = date("Y-m-d");
									$datosEnc[ "Hora_data" ] = date( "H:i:s" );
									$datosEnc[ "Cprnit" ] =	$valueRes['ing_cemhidCodAse'];
									$datosEnc[ "Cpraut" ] =	$valueRes['ing_ordtxtNumAut'];
									$datosEnc[ "Cprcup" ] =	$valueCup;
									$datosEnc[ "Cprest" ] =	'on';
									$datosEnc[ "Seguridad" ] =	"C-".$wbasedato;
									$sqlInsert = crearStringInsert( $wbasedato."_000217", $datosEnc );
									$resEnc = mysql_query( $sqlInsert, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error grabando en la tabla ".$wbasedato."_000217 - ".mysql_error() ) );
								}
							}
						}
						$ordenRes++;
					} //foreach
				}
			}
			/******************* Fin Guardar en la tabla de responsables 000205************/

					/******************* Guardar en la tabla de topes 000204****************/
			if( $data[ "error" ] == 0 )
			{

				if (!empty ($topes) && count($topes)>0)
				{
					/*se borran los registros para volver a insertarlos*/
					$sqlDel="delete from ".$wbasedato."_000215
							 where toptdo = '".$pac_tdoselTipoDoc."'
							 and topdoc = '".$documento."'
							 and toprec != '*'"; //no sea de soat

					$resDel = mysql_query( $sqlDel, $conex ) or ( $data[ 'error' ] = utf8_encode( mysql_errno()." - Error en el query $sqlDel - ".mysql_error() ) );
					if (!$resDel)
					{
						$data['error'] = 1;
					}
					/*fin borrado de registros*/

					//para pasar de tope en tope
					foreach( $topes as $keRes => $valueResT )
					{
						$saldo_g = $valueResT['top_toptxtValTop'];
						if( isset($arr_topes_nuevos[$valueResT['top_reshidTopRes']]) == true ){
							//Se busca si al tope se le cambio el saldo, para que no lo ponga como total otra vez
							foreach( $arr_topes_nuevos[$valueResT['top_reshidTopRes']] as &$topeNew ){
								if( $valueResT['top_tcoselTipCon'] == $topeNew['tipo'] ){
									$saldo_g = $topeNew['saldo'];
									break;
								}
							}
						}
						 unset( $datosEnc ); //se borra el array
						//se guardan todos los topes
						// $datosEnc = crearArrayDatos( $wbasedato, "res", "res_", 3, $valueResT );
						$datosEnc[ "Medico" ] = $wbasedato;
						$datosEnc[ "Fecha_data" ] = date("Y-m-d");
						$datosEnc[ "Hora_data" ] = date( "H:i:s" );
						$datosEnc[ "toptdo" ] = $pac_tdoselTipoDoc;
						$datosEnc[ "topdoc" ] = $documento;
						$datosEnc[ "topres" ] = $valueResT['top_reshidTopRes']; //aseguradora responsable relacion con tope
						$datosEnc[ "toptco" ] = $valueResT['top_tcoselTipCon'];
						$datosEnc[ "topcla" ] = $valueResT['top_clahidClaTop'];
						$datosEnc[ "topcco" ] =	$valueResT['top_ccohidCcoTop'];
						$datosEnc[ "toptop" ] =	$valueResT['top_toptxtValTop'];
						$datosEnc[ "toprec" ] =	$valueResT['top_rectxtValRec'];
						$datosEnc[ "topdia" ] =	$valueResT['top_diachkValDia'];
						$datosEnc[ "topsal" ] =	$saldo_g;
						$datosEnc[ "topest" ] =	'on';
						$datosEnc[ "Seguridad" ] =	"C-".$wbasedato;

						if (!($valueResT['top_tcoselTipCon'] == '*' && $valueResT['top_clahidClaTop'] == '*' && $valueResT['top_ccohidCcoTop'] == '*'
						&& $valueResT['top_toptxtValTop'] == '' && $valueResT['top_rectxtValRec'] == ''))
						{
							$sqlInsert = crearStringInsert( $wbasedato."_000215", $datosEnc );
							$resEnc = mysql_query( $sqlInsert, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error grabando en la tabla ".$wbasedato."_000215 - ".mysql_error() ) );

							if (!$resEnc){
								$data['error']=1;
							}
						}
					}//foreach
				}				//topes diferente de vacio
			}			//data error = 0
			/******************* Fin Guardar en la tabla de topes 000204************/



			/* GUARDAR ACCIDENTE DE TRANSITO */
			if( $data[ "error" ] == 0 )
			{
				/*se borran los registros para volver a insertarlos*/
				$sqlDel="delete from ".$wbasedato."_000227
						 where Acctdo = '".$pac_tdoselTipoDoc."'
						 and Accdoc = '".$documento."'"; //no sea de soat

				$resDel = mysql_query( $sqlDel, $conex ) or ( $data[ 'error' ] = utf8_encode( mysql_errno()." - Error en el query $sqlDel - ".mysql_error() ) );
				if (!$resDel)
				{
					$data['error'] = 1;
				}

				/**Se guarda accidentes de transito**/
				if( !empty( $ing_caiselOriAte ) && $ing_caiselOriAte == '02' ){

					$datosTabla = crearArrayDatos( $wbasedato, "Acc", "dat_Acc", 3 );

					$datosTabla[ 'Acctdo' ] = $pac_tdoselTipoDoc;
					$datosTabla[ 'Accdoc' ] = $documento;
					$datosTabla[ 'Accest' ] = 'on';
					unset( $datosTabla[ 'Acchis' ] );
					unset( $datosTabla[ 'Accing' ] );

					//para el segundo responsable
					if (isset($codAseR2))
					{
						$datosTabla[ 'Accre2' ]= $codAseR2;
					}

					//para el tercer responsable
					if (isset($tipoEmpR3))
					{
						$datosTabla[ 'Accemp' ] = $tipoEmpR3;
						$datosTabla[ 'Accre3' ] = $codAseR3;
						$datosTabla[ 'Accno3' ] = $nomAseR3;
					}

					//ES UN ACCIDENTE DE REINGRESO
					if( isset($accidente_previo) && $accidente_previo != "" ){
						$datosTabla[ 'Accrei' ] = $accidente_previo;
					}

					$sqlInsert = crearStringInsert( $wbasedato."_000227", $datosTabla );

					$res1 = mysql_query( $sqlInsert, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() ) );

					if( $res1 ){
						if( mysql_affected_rows() > 0 ){
							//$data[ "mensaje" ] = utf8_encode( "Se guardo correctamente" );
						}
					}
					else{
						$data[ "error" ] = 1;
					}
				}
			}
			/* FIN GUARDAR ACCIDENTE DE TRANSITO */
		}
		else
		{
			$data[ "error" ] = 1;
			$data[ "mensaje" ] = utf8_encode( "El paciente se encuentra activo en el sistema.\nNo se puede realizar la preadmisión" );
		}
		/**fin admision**/
	} //historia e ingreso vacios
	// else
	// {
		// $data[ 'error' ] = 1;
		// $data[ 'error' ] = "historia o ingreso vacios";
	// }
	/***fin se guardan o se actualizan los datos***/

	/*Insercion en la tabla de log admisiones 164 (movimientos)*/
	if( $data[ "error" ] == 0 )
	{
		$fecha = date("Y-m-d");
		$hora = (string)date("H:i:s");

		if( $modoConsulta == true )
		{
			$des="Preadmision actualizada";
		}
		else
		{
			$des="Preadmision";
		}

		$data1 = logAdmsiones( $des, $historia, $ingreso, $documento );

		if( $data1[ 'error' ] == 1 ){
			$data = $data1;
		}
	}

	/*Fin insercion en la tabla de log admisiones 164 (movimientos)*/

	//para enviarlos y colocarlos en las cajas de texto correspondientes despues de guardar
	$data[ 'documento' ]=$documento;

	echo json_encode($data);
	return;
}

/************************************************************************
 * Agosto 13 de 2013
 ************************************************************************/
if (isset($accion) and $accion == 'mostrarDatosAlmacenadosPreadmision')
 {
	$data = array('error'=>0,'mensaje'=>'','html'=>'','numRegistrosIng'=>array(), 'ultimoIngreso'=>array(),'numRegistrosPac'=>'');

	global $wbasedato;
	global $conex;

	//si no tiene ningun parametro para buscar
	// if (empty( $pac_doctxtNumDoc ) and empty( $ing_histxtNumHis ) and empty( $pac_ap1txtPriApe ) and empty( $pac_ap2txtSegApe )
		// and empty( $pac_no1txtPriNom ) and empty( $pac_no2txtSegNom ))
	if( empty( $pac_doctxtNumDoc ) )
	{
		// $data['mensaje']="Debe ingresar al menos un parametro de busqueda";
		// $data['error']=1;
		echo json_encode($data);
		exit;
	}

	/***se consulta si la persona ha venido antes en la tabla 100***/
	$sql = "select Pacfec, Pactdo,Pacdoc,Pacfed,Pactat,Pacap1,Pacap2,Pacno1,Pacno2,Pacfna,Pacsex,Pacest,Pacdir,Pactel,
            Paciu,Pacbar,Pacdep,Paczon,Pactus,Pacofi,Paccea,Pacnoa,Pactea,Pacdia,Pacpaa,Pacact,Paccru,Pacnru,
            Pactru,Pacdru,Pacpru,Paccor,Pactam,Pacpan,Pacpet,Pacded,Pactrh,Pacpah,Pacdeh,Pacmuh,Pacmov,Pacned,
            Pacemp,Pactem,Paceem,Pactaf,Pacrem,Pacire,Paccac,Pactda,Pacddr,Pacdre,Pacmre,Pacmor,Paccre,Fecha_data, Pacdle, Pactle, Pacaud
			from ".$wbasedato."_000166
			where pacact = 'on'";
			// and pacfec = '".date( "Y-m-d" )."'";	//Agosto 30 de 2013
		if (!empty( $pac_doctxtNumDoc ))
		{
			$sql.="and Pacdoc='".utf8_decode($pac_doctxtNumDoc)."' ";
		}

		$sql .=" Order by  Pacdoc  ";

	$res = mysql_query( $sql, $conex ) or ( $data[ 'error' ] = utf8_encode( "Error consultando la tabla ".$wbasedato."000100 ".mysql_errno()." - Error en el query $sql - ".mysql_error() ) );
	if ($res)
	{
		$num=mysql_num_rows($res);
		$data['numRegistrosPac']=$num;
		if ($num>0)
		{
			/*se inicializa la i en el for de la consulta de la 100 pero se incrementa en el for de la
			consulta de la 101
			*/
			for( $i = 0, $j = 0;$rows=mysql_fetch_assoc($res ); $j++ )
			{ //solo se puede buscar por el nombre del campo
				//posicion de historia
				$data['numPosicionHistorias'][ $rows['Pacdoc'] ] = $j;

				foreach( $rows as $key => $value )
				{
					//se guarda en data con el prefijo pac_ y empezando en la posicion 3 hasta el final
					$data[ 'infopac' ][ "pac_".substr( $key, 3 ) ] = utf8_encode( $value );
				}
				$data[ 'infopac' ][ 'pac_ciu' ] = $rows['Paciu'];

				//se deben buscar los nombres de los campos que tienen guardado el codigo
				//se consulta el nombre del pais nacimiento
				if (!empty( $rows['Pacpan'] ))
				{
					$res1=consultaNombrePais($rows['Pacpan']);
					if ($res1)
					{
						$num1=mysql_num_rows($res1);
						if ($num1>0)
						{
							$rows1=mysql_fetch_array($res1);
							$data[ 'infopac' ][ 'pac_pantxtPaiNac' ] = utf8_encode($rows1['Painom']);
						}
						else
						{
							$data[ 'error' ] = 1;
							$data['mensaje']="No se encontro el codigo del pais";
						}
					}
					else
					{
						$data['mensaje']="No se ejecuto la consulta de busqueda del pais";
						$data[ 'error' ] = 1;
					}
				}

				//se consulta el nombre del departamento nacimiento
				if (!empty( $rows['Pacdep'] ))
				{
					$res2=consultaNombreDepartamento($rows['Pacdep']);
					if ($res2)
					{
						$num2=mysql_num_rows($res2);
						if ($num2>0)
						{
							$rows2=mysql_fetch_array($res2);
							$data[ 'infopac' ][ 'pac_deptxtDepNac' ] = utf8_encode($rows2['Descripcion']);
						}
						else
						{
							$data[ 'error' ] = 1;
							$data['mensaje']="No se encontro el codigo del departamento";
						}
					}
					else
					{
						$data[ 'error' ] = 1;
						$data['mensaje']="No se ejecuto la consulta de busqueda del departamento";
					}
				}

				//se consulta el nombre del municipio nacimiento
				if (!empty( $rows['Paciu'] ))
				{
					$res3=consultaNombreMunicipio($rows['Paciu']);
					if ($res3)
					{
						$num3=mysql_num_rows($res3);
						if ($num3>0)
						{
							$rows3=mysql_fetch_array($res3);
							$data[ 'infopac' ][ 'pac_ciutxtMunNac' ] = utf8_encode($rows3['Nombre']);
						}
						else
						{
							//$data[ 'error' ] = 1;
							$data['mensaje']="No se encontro el codigo del municipio";
						}
					}
					else
					{
						$data[ 'error' ] = 1;
						$data['mensaje']="No se ejecuto la consulta de busqueda del municipio";
					}
				}
				//se consulta el nombre del pais de residencia
				if (!empty( $rows['Pacpah'] ))
				{
					$res4=consultaNombrePais($rows['Pacpah']);
					if ($res4)
					{
						$num4=mysql_num_rows($res4);
						if ($num4>0)
						{
							$rows4=mysql_fetch_array($res4);
							$data[ 'infopac' ][ 'pac_pahtxtPaiRes' ] = utf8_encode($rows4['Painom']);
						}
						else
						{
							$data[ 'error' ] = 1;
							$data['mensaje']="No se encontro el codigo del pais de residencia";
						}
					}
					else
					{
						$data['mensaje']="No se ejecuto la consulta de busqueda del pais de residencia";
						$data[ 'error' ] = 1;
					}
				}

				//se consulta el nombre del departamento de residencia
				if (!empty( $rows['Pacdeh'] ))
				{
					$res4=consultaNombreDepartamento($rows['Pacdeh']);
					if ($res4)
					{
						$num4=mysql_num_rows($res4);
						if ($num4>0)
						{
							$rows4=mysql_fetch_array($res4);
							$data[ 'infopac' ][ 'pac_dehtxtDepRes' ] = utf8_encode($rows4['Descripcion']);
						}
						else
						{
							$data[ 'error' ] = 1;
							$data['mensaje']="No se encontro el codigo del departamento residencia";
						}
					}
					else
					{
						$data[ 'error' ] = 1;
						$data['mensaje']="No se ejecuto la consulta de busqueda del departamento residencia";
					}
				}

				//se consulta el nombre del municipio de residencia
				if (!empty( $rows['Pacmuh'] ))
				{
					$res4=consultaNombreMunicipio($rows['Pacmuh']);
					if ($res4)
					{
						$num4=mysql_num_rows($res4);
						if ($num4>0)
						{
							$rows4=mysql_fetch_array($res4);
							$data[ 'infopac' ][ 'pac_muhtxtMunRes' ] = utf8_encode($rows4['Nombre']);
						}
						else
						{
							$data[ 'error' ] = 1;
							$data['mensaje']="No se encontro el codigo del municipio de residencia";
						}
					}
					else
					{
						$data[ 'error' ] = 1;
						$data['mensaje']="No se ejecuto la consulta de busqueda del municipio de residencia";
					}
				}

				//se consulta el nombre del barrio de residencia
				if (!empty( $rows['Pacbar'] ) and !empty( $rows['Pacmuh']))
				{
					$res4=consultaNombreBarrio($rows['Pacbar'],$rows['Pacmuh']);
					if ($res4)
					{
						$num4=mysql_num_rows($res4);
						if ($num4>0)
						{
							$rows4=mysql_fetch_array($res4);
							$data[ 'infopac' ][ 'pac_bartxtBarRes' ] = utf8_encode($rows4['Bardes']);
						}
						else
						{
							$data[ 'error' ] = 1;
							$data['mensaje']="No se encontro el codigo del barrio de residencia";
						}
					}
					else
					{
						$data[ 'error' ] = 1;
						$data['mensaje']="No se ejecuto la consulta de busqueda del barrio de residencia";
					}
				}
				else
				{
					if(!empty( $rows['Pacbar'] ) and empty( $rows['Pacmuh']))
					{
						$res4=consultaNombreBarrio($rows['Pacbar'],'');
						if ($res4)
						{
							$num4=mysql_num_rows($res4);
							if ($num4>0)
							{
								$rows4=mysql_fetch_array($res4);
								$data[ 'infopac' ][ 'pac_bartxtBarRes' ] = utf8_encode($rows4['Bardes']);
							}
							else
							{
								$data[ 'error' ] = 1;
								$data['mensaje']="No se encontro el codigo del barrio de residencia";
							}
						}
						else
						{
							$data[ 'error' ] = 1;
							$data['mensaje']="No se ejecuto la consulta de busqueda del barrio de residencia";
						}
					}
				}

				//se consulta el nombre de la ocupacion
				if (!empty( $rows['Pacofi'] ))
				{
					$res4=consultaNombreOcupacion($rows['Pacofi']);
					if ($res4)
					{
						$num4=mysql_num_rows($res4);
						if ($num4>0)
						{
							$rows4=mysql_fetch_array($res4);
							$data[ 'infopac' ][ 'pac_ofitxtocu' ] = utf8_encode($rows4['Nombre']);
						}
						else
						{
							 $data[ 'error' ] = 0;
							$data['mensaje']="No se encontro el codigo de la ocupacion";
						}
					}
					else
					{
						$data[ 'error' ] = 1;
						$data['mensaje']="No se ejecuto la consulta de busqueda de la ocupacion";
					}
				}

				//se consulta el nombre del departamento del responsable del usuario
				if (!empty( $rows['Pacdre'] ))
				{
					$res4=consultaNombreDepartamento($rows['Pacdre']);
					if ($res4)
					{
						$num4=mysql_num_rows($res4);
						if ($num4>0)
						{
							$rows4=mysql_fetch_array($res4);
							$data[ 'infopac' ][ 'pac_dretxtDepResp' ] = utf8_encode($rows4['Descripcion']);
						}
						else
						{
							$data[ 'error' ] = 1;
							$data['mensaje']="No se encontro el codigo del departamento responsable del usuario";
						}
					}
					else
					{
						$data[ 'error' ] = 1;
						$data['mensaje']="No se ejecuto la consulta de busqueda del departamento del responsable del usuario";
					}
				}

				//se consulta el nombre del municipio del responsable del usuario
				if (!empty( $rows['Pacmre'] ))
				{
					$res4=consultaNombreMunicipio($rows['Pacmre']);
					if ($res4)
					{
						$num4=mysql_num_rows($res4);
						if ($num4>0)
						{
							$rows4=mysql_fetch_array($res4);
							$data[ 'infopac' ][ 'pac_mretxtMunResp' ] = utf8_encode($rows4['Nombre']);
						}
						else
						{
							$data[ 'error' ] = 1;
							$data['mensaje']="No se encontro el codigo del municipio del responsable del usuario";
						}
					}
					else
					{
						$data[ 'error' ] = 1;
						$data['mensaje']="No se ejecuto la consulta de busqueda del municipio del responsable del usuario";
					}
				}

				/***busqueda del paciente en la tabla de ingreso 101***/

				 $sql1 = "select Ingfei,Inghin,Ingsei,Ingtin,Ingcai,Ingtpa,Ingcem,Ingent,
						Ingord,Ingpol,Ingnco,Ingdie,Ingtee,Ingtar,Ingusu,Inglug,Ingdig,Ingdes,Ingpla,
						Ingfha,Ingnpa,Ingcac,Ingpco,Inghoa,Ingcla,Fecha_data,Ingmei
						from ".$wbasedato."_000167
						where Ingdoc !='0'";
				if (!empty( $rows['Pacdoc'] ))
				{
					 $sql1.="and Ingdoc='".$rows['Pacdoc']."' ";
				}

				$res1 = mysql_query( $sql1, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla ".$wbasedato."000101 ".mysql_errno()." - Error en el query $sql1 - ".mysql_error() ) );
				if ($res1)
				{
					$num1=mysql_num_rows($res1);
					$data['numRegistrosIng'][ $rows['Pacdoc'] ] = $num1;
					$data['ultimoIngreso'][ $rows['Pacdoc'] ] = -1;
					if ($num1>0)
					{
						for( $i;$rows1=mysql_fetch_assoc($res1 ); $i++ )  //solo se puede buscar por el nombre del campo
						{

							$data[ 'infoing' ][$i] = $data[ 'infopac' ];

							$aplicacion=consultarAplicacion2( $conex, $wemp_pmla, "movhos" );
							if( $aplicacion != '' ){
								// $resIngSei = consultarCC( $aplicacion, $where="Ccoing = 'on' and Ccoayu != 'on' and ccoest = 'on' and ccorel='".$rows1[ 'Ingsei' ]."'" );
								$resIngSei = consultarCC( $aplicacion, $where="Ccocod='".$rows1[ 'Ingsei' ]."'" );

								if( $rowsIngSei = mysql_fetch_array($resIngSei) ){
									$rows1[ 'Ingsei' ] = $rowsIngSei[ 'Ccosei' ]."-".$rowsIngSei[ 'Ccocod' ];
								}
							}

							foreach( $rows1 as $key => $value )
							{
								//se guarda en data con el prefijo ing_ y empezando en la posicion 3 hasta el final
								$data[ 'infoing' ][$i][ "ing_".substr( $key, 3 ) ] = utf8_encode($value);
							}

							//se consultan los nombres de los datos del ingreso que traen el codigo
								//se consulta el nombre de la aseguradora
							if (!empty( $rows1['Ingcem'] ) && $rows1['Ingtpa'] == "E" )
							{
								$res4=consultaNombreAseguradora($rows1['Ingcem']);
								$adicionarTarifaAnombre = consultarAplicacion2( $conex, $wemp_pmla, "adicionarTarifaAnombreEntidad" );
								if ($res4)
								{
									$num4=mysql_num_rows($res4);
									if ($num4>0)
									{
										$rows4=mysql_fetch_array($res4);
										if( $adicionarTarifaAnombre == "on")
											$data[ 'infoing' ][$i][ 'ing_cemtxtCodAse' ] = utf8_encode($rows4['Empnom'])."-->Tarifa: ".$rows4['Emptar']." ".utf8_encode($rows4['Tardes']);
										else
											$data[ 'infoing' ][$i][ 'ing_cemtxtCodAse' ] = utf8_encode($rows4['Empnom']);
									}
									else
									{
										//$data[ 'error' ] = 1;
										$data['mensaje']="No se encontro el codigo de la aseguradora";
									}
								}
								else
								{
									$data[ 'error' ] = 1;
									$data['mensaje']="No se ejecuto la consulta de busqueda del codigo de la aseguradora";
								}
							}

							if (!empty( $rows1['Ingtar'] ) )
							{
								$res4=consultaNombreTarifa($rows1['Ingtar']);
									if ($res4)
									{
										$num4=mysql_num_rows($res4);
										if ($num4>0)
										{
											$rows4=mysql_fetch_array($res4);
											$data[ 'infoing' ][$i][ 'ing_tartxt' ] = utf8_encode($rows4['Descripcion']);
										}
										else
										{
											//$data[ 'error' ] = 1;
											$data['mensaje']="No se encontro el codigo de tarifa";
										}
									}
									else
									{
										$data[ 'error' ] = 1;
										$data['mensaje']="No se ejecuto la consulta de busqueda del codigo de tarifa";
									}
							}

							//se consulta el nombre de la impresion diagnostica
							if (!empty( $rows1['Ingdig'] ))
							{
								$res4=consultaNombreImpDiag($rows1['Ingdig']);
								if ($res4)
								{
									$num4=mysql_num_rows($res4);
									if ($num4>0)
									{
										$rows4=mysql_fetch_array($res4);
										$data[ 'infoing' ][$i][ 'ing_digtxtImpDia' ] = utf8_encode($rows4['Descripcion']);
									}
									else
									{
										//$data[ 'error' ] = 1;
										$data['mensaje']="No se encontro el codigo de impresion diagnostica";
									}
								}
								else
								{
									$data[ 'error' ] = 1;
									$data['mensaje']="No se ejecuto la consulta de busqueda del codigo impresion diagnostica";
								}
							}

							/*2014-08-04 Se consulta el nombre del medico de ingreso*/
							if (!empty( $rows1['Ingmei'] ))
							{
								$med_ing = consultarMedicos($rows1['Ingmei'],$wbasedato, $aplicacion, true);
								if( $med_ing ){
									$data[ 'infoing' ][$i][ 'ing_meitxtMedIng' ] = utf8_encode($med_ing['valor']['des']);
								}
							}

							if( $rows1['Ingcai' ] == '02' ){
								$sql = "SELECT Acctdo, Accdoc, Acccon, Accdir, Accdtd, Accfec, Acchor, Accdep, Accmun, Acczon, Accdes, Accase, Accmar, Accpla, Acctse, Acccas,
											   Accpol, Accvfi, Accvff, Accaut, Acccep, Accap1, Accap2, Accno1, Accno2, Accnid, Acctid, Accpdi, Accpdd, Accpdp, Accpmn, Acctel,
											   Accca1, Accca2, Acccn1, Acccn2, Acccni, Acccti, Acccdi, Acccdd, Acccdp, Acccmn, Accctl, Accrei
										  FROM {$wbasedato}_000227
									     WHERE Acctdo = '".$rows['Pactdo']."'
										   AND Accdoc = '".$rows['Pacdoc']."'
										   AND Accest = 'on'
										";

								$res = mysql_query( $sql, $conex ) or ( $data[ 'error' ] = utf8_encode( "Error consultando la tabla ".$wbasedato."000227 ".mysql_errno()." - Error en el query $sql - ".mysql_error() ) );

								if ($res)
								{
									$num=mysql_num_rows($res);

									if ($num>0)
									{
										if( $rowAcc=mysql_fetch_array($res, MYSQL_ASSOC ) )
										{
											foreach( $rowAcc as $key => $value )
											{
												//se guarda en data con el prefijo pac_ y empezando en la posicion 3 hasta el final
												$data[ 'infoing' ][$i][ "dat_Acc".substr( $key, 3 ) ] = utf8_encode( $value );
											}

											//Consulto el nombre del departamento en donde ocurrio el accidente
											$res = consultaNombreDepartamento( $rowAcc[ 'Accdep' ] );
											$num = mysql_num_rows( $res );
											if( $rowdp = mysql_fetch_array( $res ) ){
												$dep = $rowdp[ 'Descripcion' ];
											}
											else{
												$dep = '';
											}
											$data[ 'infoing' ][$i][ "Accdep" ] = $dep;




											//Consulto el nombre del departamento en donde ocurrio el accidente
											$res = consultaNombreDepartamento( $rowAcc[ 'Accpdp' ] );
											$num = mysql_num_rows( $res );
											if( $rowdp = mysql_fetch_array( $res ) ){
												$dep = $rowdp[ 'Descripcion' ];
											}
											else{
												$dep = '';
											}
											$data[ 'infoing' ][$i][ "AccDepPropietario" ] = $dep;




											//Consulto el nombre del departamento en donde ocurrio el accidente
											$res = consultaNombreDepartamento( $rowAcc[ 'Acccdp' ] );
											$num = mysql_num_rows( $res );
											if( $rowdp = mysql_fetch_array( $res ) ){
												$dep = $rowdp[ 'Descripcion' ];
											}
											else{
												$dep = '';
											}
											$data[ 'infoing' ][$i][ "AccConductordp" ] = $dep;




											//Consulto el nombre el municipio en donde ocurrio el accidente
											$res = consultaNombreMunicipio( $rowAcc[ 'Accmun' ] );
											$num = mysql_num_rows( $res );
											if( $rowdp = mysql_fetch_array( $res ) ){
												$mun = $rowdp[ 'Nombre' ];
											}
											else{
												$mun = '';
											}
											$data[ 'infoing' ][$i][ "Accmun" ] = $mun;




											//Consulto el nombre el municipio del propietario
											$res = consultaNombreMunicipio( $rowAcc[ 'Accpmn' ] );
											$num = mysql_num_rows( $res );
											if( $rowdp = mysql_fetch_array( $res ) ){
												$mun = $rowdp[ 'Nombre' ];
											}
											else{
												$mun = '';
											}
											$data[ 'infoing' ][$i][ "AccMunPropietario" ] = $mun;




											//Consulto el nombre el municipio del conductor
											$res = consultaNombreMunicipio( $rowAcc[ 'Acccmn' ] );
											$num = mysql_num_rows( $res );
											if( $rowdp = mysql_fetch_array( $res ) ){
												$mun = $rowdp[ 'Nombre' ];
											}
											else{
												$mun = '';
											}
											$data[ 'infoing' ][$i][ "AccConductorMun" ] = $mun;

											//Consulto el nombre de la aseguradora
											//se modifica para consultar la nueva funcion consultaNombreAseguradoraVehiculo
											$res = consultaNombreAseguradoraVehiculo( $rowAcc[ 'Acccas' ] );
											$num = mysql_num_rows( $res );
											if( $rowAs = mysql_fetch_array( $res ) ){
												$ase = $rowAs[ 'Asedes' ];
											}
											else{
												$ase = '';
											}
											$data[ 'infoing' ][$i][ "_ux_accasn" ] = $ase;
										}
									}
								}
							}

							//Frederick
							$hayResponsables = false;
							/*codigo para traer los responsables de la tabla 205*/
							if($data['error']==0)
							{
								//La empresa tipo SOAT se muestre desde otra parte, en esta parte no se debe mandar
								$tipoSOAT = consultarAliasPorAplicacion($conex, $wemp_pmla, "tipoempresasoat" );


								$sqlpro = "select *
											from ".$wbasedato."_000216
											where Restdo = '".$rows['Pactdo']."'
											  and Resdoc = '".$rows['Pacdoc']."'
											  and Resest = 'on'";
								$sqlpro.=" ORDER BY resord*1 ";

								$respro = mysql_query( $sqlpro, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla ".$wbasedato."000216 ".mysql_errno()." - Error en el query $sqlpro - ".mysql_error() ) );
								if ($respro)
								{
									$numpro=mysql_num_rows($respro);
									if ($numpro>0)
									{
										$hayResponsables = true;
										$k=-1;
										while( $rowspro = mysql_fetch_assoc($respro) ){


											$esSoat = false;
											if( $rowspro['Resord'] == "1" ){ //POR NORMA, si tiene SOAT siempre es el primer responsable
												$sqls = " SELECT Empcod
															FROM ".$wbasedato."_000024
														   WHERE Empcod = '".$rowspro['Resnit']."'
															 AND Emptem = '".$tipoSOAT."'";
												$ress = mysql_query( $sqls, $conex );
												$nums = mysql_num_rows( $ress );

												if( $nums > 0 ){//Quiere decir que SI ES TIPO SOAT
													$esSoat = true;
												}
											}
											if( $esSoat == false ){
												$k++;
												$data[ 'infoing' ][$i]['responsables'][$k]['ing_tpaselTipRes'] = $rowspro['Restpa'];
												$data[ 'infoing' ][$i]['responsables'][$k]['ing_cemhidCodAse'] = $rowspro['Resnit']; //el hidden
												// $data[ 'infoing' ][$i]['responsables'][$k]['ing_cemtxtCodAse'] = utf8_encode($rows4['Empnom'])."-->Tarifa: ".$rows4['Emptar']." ".utf8_encode($rows4['Tardes']);
												$data[ 'infoing' ][$i]['responsables'][$k]['ing_plaselPlan'] = $rowspro['Respla'];
												$data[ 'infoing' ][$i]['responsables'][$k]['ing_poltxtNumPol'] = $rowspro['Respol'];
												$data[ 'infoing' ][$i]['responsables'][$k]['ing_ncotxtNumCon'] = $rowspro['Resnco'];
												$data[ 'infoing' ][$i]['responsables'][$k]['res_firtxtNumcon'] = $rowspro['Resfir'];
												$data[ 'infoing' ][$i]['responsables'][$k]['res_ffrtxtNumcon'] = $rowspro['Resffr'];
												$data[ 'infoing' ][$i]['responsables'][$k]['res_comtxtNumcon'] = $rowspro['Rescom'];
												$data[ 'infoing' ][$i]['responsables'][$k]['ing_ordtxtNumAut'] = $rowspro['Resaut'];
												$data[ 'infoing' ][$i]['responsables'][$k]['ing_fhatxtFecAut'] = $rowspro['Resfha'];
												$data[ 'infoing' ][$i]['responsables'][$k]['ing_hoatxtHorAut'] = $rowspro['Reshoa'];
												$data[ 'infoing' ][$i]['responsables'][$k]['ing_npatxtNomPerAut'] = $rowspro['Resnpa'];
												$data[ 'infoing' ][$i]['responsables'][$k]['ing_pcoselPagCom'] = $rowspro['Respco'];

												/*nombres de las aseguradoras*/
												if (!empty( $rowspro['Resnit'] ) && $rowspro['Restpa'] == "E")
												{
													$res4=consultaNombreAseguradora($rowspro['Resnit']);

													if ($res4)
													{
														$num4=mysql_num_rows($res4);
														if ($num4>0)
														{
															$rows4=mysql_fetch_array($res4);
															$data[ 'infoing' ][$i]['responsables'][$k]['ing_cemtxtCodAse'] = utf8_encode($rows4['Empnom'])."-->Tarifa: ".$rows4['Emptar']." ".utf8_encode($rows4['Tardes']);
														}
														else
														{
															//$data[ 'error' ] = 1;
															$data['mensaje']="No se encontro el codigo de la aseguradora..";
														}
													}
													else
													{
														$data[ 'error' ] = 1;
														$data['mensaje']="No se ejecuto la consulta a la tabla de aseguradoras";
													}
												}else if($rowspro['Restpa'] == "P"){
													$data[ 'infoing' ][$i]['responsables'][$k]['res_nom'] = $rowspro['Resnom'];
													$data[ 'infoing' ][$i]['responsables'][$k]['res_tdo'] = $rowspro['Restid'];
													$data[ 'infoing' ][$i]['responsables'][$k]['res_doc'] = $rowspro['Resnit'];
												}
												/*fin nombres de las aseguradoras*/


												//Se consultan los cups de cada nit-autorizacion
												if (!empty( $rowspro['Resnit'] ))
												{
													$sqlcupc = "SELECT Cprcup as cup
																  FROM ".$wbasedato."_000217
																 WHERE Cprtdo = '".$rows['Pactdo']."'
																   AND Cprdoc = '".$rows['Pacdoc']."'
																   AND Cprnit = '".$rowspro['Resnit']."'
																   AND Cpraut = '".$rowspro['Resaut']."'
																   AND Cprest = 'on' ";
													/*$sqlcupc = "SELECT Cprcup as cup
																  FROM ".$wbasedato."_000217
																 WHERE Cprtdo = '".$rows['Pactdo']."'
																   AND Cprdoc = '".$rows['Pacdoc']."'
																   AND Cprest = 'on' ";*/

													$rescup = mysql_query($sqlcupc,$conex) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla ".$wbasedato."000209 ".mysql_errno()." - Error en el query $sqlcupc - ".mysql_error() ) );
													if( $rescup ){
														$numpro=mysql_num_rows($rescup);
														if ($numpro>0){
															$data[ 'infoing' ][$i]['responsables'][$k]['cups'] = array();
															while( $rowcup = mysql_fetch_assoc($rescup) ){
																$cupdato = array();
																$cupdato['nombre'] = "";
																$cupdato['codigo'] = $rowcup['cup'] ;
																$res4=consultaNombreCups($rowcup['cup']);
																if ($res4){
																	$num4=mysql_num_rows($res4);
																	if ($num4>0){
																		$rows4=mysql_fetch_array($res4);
																		$cupdato['nombre'] = utf8_encode($rows4['Nombre']);
																	}
																}
																array_push( $data[ 'infoing' ][$i]['responsables'][$k]['cups'], $cupdato );
															}
														}
													}else{
														$data['mensaje']="No se encontraron codigos cups ".$sqlcupc;
													}
												}
											}
										}
									}
									else
									{
										//$data[ 'error' ] = 1;
										$data['mensaje']="No se encontraron responsables asociados a la historia ".$rows['Pachis']."";
									}
								}
								else
								{
									// $data[ 'error' ] = 1;
									$data['mensaje']="No se ejecuto la consulta de busqueda de los responsables";
								}
							}
							/*Fin codigo para traer los responsables de la tabla 205*/

							/*codigo para traer los topes de la tabla 204*/
							if($data['error']==0 && $hayResponsables==true)
							{
								$sqlpro1 = "select *
											from ".$wbasedato."_000215
											where Toptdo = '".$rows['Pactdo']."'
											  and Topdoc = '".$rows['Pacdoc']."'
											  and Topest = 'on'
											  and toprec != '*' "; //no sea de soat
								$sqlpro1.=" ORDER BY Topres,Toptco desc";

								$respro1 = mysql_query( $sqlpro1, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla ".$wbasedato."000204 ".mysql_errno()." - Error en el query $sqlpro1 - ".mysql_error() ) );
								if ($respro1)
								{
									$numpro1=mysql_num_rows($respro1);
									if ($numpro1>0)
									{
										for ($k=0; $rowspro1=mysql_fetch_array($respro1);$k++)
										{
											$data[ 'infoing' ][$i]['topes'][$k]['top_tcoselTipCon'] = $rowspro1['Toptco'];
											$data[ 'infoing' ][$i]['topes'][$k]['top_clahidClaTop'] = $rowspro1['Topcla']; //el hidden
											$data[ 'infoing' ][$i]['topes'][$k]['top_ccohidCcoTop'] = $rowspro1['Topcco']; //el hidden
											$data[ 'infoing' ][$i]['topes'][$k]['top_toptxtValTop'] = $rowspro1['Toptop'];
											$data[ 'infoing' ][$i]['topes'][$k]['top_rectxtValRec'] = $rowspro1['Toprec'];
											$data[ 'infoing' ][$i]['topes'][$k]['top_diachkValDia'] = $rowspro1['Topdia'];
											$data[ 'infoing' ][$i]['topes'][$k]['top_reshidTopRes'] = $rowspro1['Topres']; //el hidden
											$data[ 'infoing' ][$i]['topes'][$k]['sfdsf'] = 'total'; //el hidden
											//el saldo no se muestra

											// para mostrar los totales de tope y reconocido
											if ($rowspro1['Toptco'] == '*' && $rowspro1['Topcla'] == '*' && $rowspro1['Topcco'] == '*')
											{
												$data[ 'infoing' ][$i]['topes'][$k]['total'] = 'on';
											}
											else
											{
												$data[ 'infoing' ][$i]['topes'][$k]['total'] = 'off';
											}

											/*nombres de los conceptos && $rowspro1['Topcla'] != '*'*/
											if (!empty( $rowspro1['Topcla'] ) )
											{
												$res4=consultaNombreConcepto($rowspro1['Topcla']);

												if ($res4)
												{
													$num4=mysql_num_rows($res4);
													if ($num4>0)
													{
														$rows4=mysql_fetch_array($res4);
														$data[ 'infoing' ][$i]['topes'][$k]['top_clatxtClaTop'] = utf8_encode($rows4['Cpgnom']);
													}
													else
													{
														$data[ 'error' ] = 1;
														$data['mensaje']="No se encontro el codigo del concepto";
													}
												}
												else
												{
													$data[ 'error' ] = 1;
													$data['mensaje']="No se ejecuto la consulta a la tabla de conceptos";
												}
											}
											/*fin nombres de los conceptos*/

											/*nombres de los cco && $rowspro1['Topcco'] != '*'*/
											if (!empty( $rowspro1['Topcco'] ) )
											{
												$res4=consultaNombreCco($rowspro1['Topcco']);

												if ($res4)
												{
													$num4=mysql_num_rows($res4);
													if ($num4>0)
													{
														$rows4=mysql_fetch_array($res4);
														$data[ 'infoing' ][$i]['topes'][$k]['top_ccotxtCcoTop'] = utf8_encode($rows4['Ccodes']);
													}
													else
													{
														$data[ 'error' ] = 1;
														$data['mensaje']="No se encontro el codigo del centro de costo";
													}
												}
												else
												{
													$data[ 'error' ] = 1;
													$data['mensaje']="No se ejecuto la consulta a la tabla de centros de costo";
												}
											}
											/*fin nombres de los cco*/
										}
									}
									else
									{
										//2014-02-11, Los topes no son obligatorios
										//$data[ 'error' ] = 1;
										//$data['mensaje']="No se encontraron topes asociados a la historia ".$historia."";
									}
								}
								else
								{
									// $data[ 'error' ] = 1;
									$data['mensaje']="No se ejecuto la consulta de busqueda de los topes1";
								}
							}
							/*Fin codigo para traer los topes de la tabla 204*/

							// array_push( $data[ 'infoing' ][$i], $data[ 'infopac' ] );
						}//for
					}//$num1>0
					else
					{
						$data[ 'error' ] = 1;
						$data[ 'mensaje' ] = "No se encontraron registros del ingreso para los datos ingresados";
					}
				}
				else
				{
					$data[ 'error' ] = 1;
				}

				/***fin busqueda en la tabla 101***/
			} //fin for
		} //si trae registros de la 100
		else
		{
			// $data[ 'mensaje' ] = "No se encontro informacion para los datos ingresados";
		}
	}
	else
	{
		$data[ 'error' ] = 1;
	}
	/***fin busqueda en la tabla 100***/

	echo json_encode($data);
	return;
}

if (isset($accion) and $accion == 'consultarPreadmision')
{
	$data = agendaAdmisiones( $fecha, $incremento );
	// $data = agendaAdmitidos( $fecha, $incremento );
	echo json_encode( $data );
	return;
}


if( isset($accion) and $accion == 'cancelarPreadmision' )
{
	$data = array('error'=>0,'mensaje'=>'','html'=>'');

	$sql = "UPDATE
				".$wbasedato."_000166
			SET
				pacact = 'off'
			WHERE
				pactdo = '".utf8_decode( $tdo )."'
				AND pacdoc = '".utf8_decode( $doc )."'
				AND pacact = 'on'";

	$res = mysql_query( $sql, $conex ) or ( $data[ 'mensaje' ] = mysql_errno()." - Error en el query $sql - ".mysql_error() );

	if( $res ){
		if( mysql_affected_rows() > 0 ){
			
			if( $data1[ 'error' ] == 1 ){
				$data = $data1;
			}
		}
		$data[ 'mensaje' ] = 'Se ha cancelado la PREADMISION correctamente';
		//Grabo el log correspondiente
		$data1 = logAdmsiones( "Preadmision anulada", '', '', $doc );
	}
	else{
		$data[ 'error' ] = 1;
	}
	echo json_encode( $data );
	return;
}

if (isset($accion) and $accion == 'consultarAdmitidos')
{
	$data = agendaAdmitidos( $fecha, $incremento );
	echo json_encode( $data );
	return;
}

if(isset($accion) and $accion == 'anularAdmision')
{
	$data = array('error'=>0,'mensaje'=>'','html'=>'');

	$aplMovhos=consultarAplicacion2( $conex, $wemp_pmla, "movhos" );
	$aplHce=consultarAplicacion2( $conex, $wemp_pmla, "hce" );

	//2014-03-18   Comprobar primero si se puede anular la admision
	if( !empty( $aplMovhos ) )
	{
		$query = " SELECT ingsei
					 FROM {$wbasedato}_000101
					WHERE inghis = '{$historia}'
					  AND ingnin = '{$ingreso}'";
		$resMov = mysql_query( $query, $conex ) or ( $data[ 'mensaje' ] = mysql_errno()." - Error en el query $sqlMov - ".mysql_error() );
		$row = mysql_fetch_array($resMov);
		$ccoAyuda = verificarCcoIngresoAyudaUnificada( $row[0], $aplMovhos );

		if( $ccoAyuda ){
			$query = "DELETE
			            FROM {$aplMovhos}_000032
					   WHERE Historia_clinica = '{$historia}'
					     AND Num_ingreso = '{$ingreso}'
					     AND servicio = '{$row[0]}'";
			$res = mysql_query( $query, $conex ) or ( $data[ 'mensaje' ] = mysql_errno()." - Error en el query $sqlMov - ".mysql_error() );
		}

		//Se consulta si ha sido ingresado en algun piso
		$sqlMov = "SELECT Fecha_data
					 FROM ".$aplMovhos."_000032
					WHERE Historia_clinica = '".$historia."'
					  AND Num_ingreso = '".$ingreso."'
					  LIMIT 1";

		$resMov = mysql_query( $sqlMov, $conex ) or ( $data[ 'mensaje' ] = mysql_errno()." - Error en el query $sqlMov - ".mysql_error() );

		if( $resMov )
		{
			$num = mysql_num_rows( $resMov );
			if( $num > 0 ){
				$data[ 'mensaje' ] = utf8_encode( 'No se puede anular, el paciente tiene un traslado.' );
				$data[ 'error' ] = 1;
				echo json_encode( $data );
				return;
			}
		}

		//Se consulta si ha tenido algun cargo
		$sqlMov2 = "SELECT Fecha_data
					 FROM ".$aplMovhos."_000002
					WHERE Fenhis = '".$historia."'
					  AND Fening = '".$ingreso."'
					  AND Fenest = 'on'
					  LIMIT 1";

		$resMov2 = mysql_query( $sqlMov2, $conex ) or ( $data[ 'mensaje' ] = mysql_errno()." - Error en el query $sqlMov2 - ".mysql_error() );

		if( $resMov2 )
		{
			$num2 = mysql_num_rows( $resMov2 );
			if( $num2 > 0 ){
				$data[ 'mensaje' ] = utf8_encode( 'No se puede anular, el paciente tiene un cargo activo.' );
				$data[ 'error' ] = 1;
				echo json_encode( $data );
				return;
			}
		}


		//--> se consulta si tiene habitacion y se retira de ella.
	}


	$tieneConexionUnix = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'conexionUnix' );
	//$tieneConexionUnix='off';
	if($hay_unix && $tieneConexionUnix == 'on' )
	{

		//--> primero validar en unix si está inactivo... correccion 2015-04-23.
		$cedula  = trim ($cedula);
		$tipoDoc = strtoupper( $tipoDoc );
		$cedula  = strtoupper( $cedula );
		$a = new admisiones_erp( 'estadoPaciente', $tipoDoc, $cedula );
		$data = $a->data;

		if( $data['mensaje'] == "" ){ //El paciente esta inactivo
			$data['mensaje'] = "El paciente esta inactivo en UNIX. \n Activarlo EN UNIX para continuar.";
			$data['error'] = 1;
			echo json_encode($data);
			return;
		}
		unset( $a );
		//--> comentar hasta aquí si se necesita deshacer el cambio

		$a = new admisiones_erp( 'anular', $historia, $ingreso );
		$data = $a->data;
		if( $data['mensaje'] == "cargos" ){ //El paciente tiene cargos pendientes en unix 2016-07-19
			$data['mensaje'] = "No puede anular la admision. El paciente tiene cargos activos en UNIX. \n.";
			$data['error'] = 1;
			echo json_encode($data);
			return;
		}

		unset( $a );
	}

	//Borro el último ingreso
	$sql = "SELECT *
			  FROM ".$wbasedato."_000100, ".$wbasedato."_000101
			 WHERE pachis = '".$historia."'
			   AND inghis = pachis
			   AND pacact = 'on'
		  ORDER BY Ingnin*1 DESC";

	$resInfPac = mysql_query( $sql, $conex ) or ( $data[ 'mensaje' ] = mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $resInfPac );
	$rowsInfo = mysql_fetch_array( $resInfPac );

	//Si no encontró paciente no se hace ningún borrado
	if( $num == 0 ){
		$data[ 'mensaje' ] = utf8_encode( 'No se encontró paciente para anular la admisión' );
		return;
	}

	//2015-10-15
	$sqlhab = " SELECT Ubiptr
				  FROM {$aplMovhos}_000020 a, {$aplMovhos}_000018
				 WHERE habhis = '{$historia}'
				   AND habing = '{$ingreso}'
				   AND ubihis = habhis
				   AND ubiing = habing";
	$rshab  = mysql_query( $sqlhab, $conex );
	$rowhab = mysql_fetch_array( $rshab );
	$liberarHabitacion  = false;
	$liberarHabitacionTxt = "no";
	if( $rowhab[0] == "on" ){
		$liberarHabitacion = true;
		$liberarHabitacionTxt = "si";
	}
	/*$data[ 'mensaje' ] = utf8_encode( 'liberar habitación  '.$liberarHabitacionTxt." /n {$sqlhab}" );
	$data[ 'error' ] = 1;
	echo json_encode( $data );
	return;*/


	if( $num == 1 )
	{
		//Inactivo al paciente
		$sqlPac = "DELETE FROM
					".$wbasedato."_000100
				WHERE
					pachis = '".$historia."'
					AND pacact = 'on'
				";
		//Inactivo al paciente
		$sqlRoot = "DELETE root_000037 FROM
						root_000037
					WHERE
						orihis = '{$historia}'
					  and oriori = '{$wemp_pmla}'
					";
	}
	else
	{
		//Inactivo al paciente
		$sqlPac = "UPDATE
					".$wbasedato."_000100
				SET
					pacact = 'off'
				WHERE
					pachis = '".$historia."'
					AND pacact = 'on'
				";
		//Inactivo al paciente
		$sqlRoot = "UPDATE
						root_000037 a
					SET
						oriing = oriing-1
					WHERE
						orihis = '{$historia}'
						and oriori = '{$wemp_pmla}'
					";
	}

	$resPac = mysql_query( $sqlPac, $conex ) or ( $data[ 'mensaje' ] = mysql_errno()." - Error en el query $sqlPac - ".mysql_error() );

	if( $resPac )
	{
		//Borro el último ingreso
		$sql = "DELETE FROM ".$wbasedato."_000101
			WHERE inghis = '".$historia."' AND ingnin = '".$ingreso."'";

		$resIng = mysql_query( $sql, $conex ) or ( $data[ 'mensaje' ] = mysql_errno()." - Error en el query $sql - ".mysql_error() );

		if( $resIng )
		{	
			if( mysql_affected_rows() > 0 )
			{	
			}
			$data[ 'mensaje' ] = utf8_encode( 'Admision anulada correctamente' );

			logAdmsiones( 'Admision anulada', $historia, $ingreso, $rowsInfo[ 'Pacdoc' ] );

			//Busco si el paciente tiene una preadmisión para la misma fecha de ingreso
			//De ser así activo la preadmisión
			$sqlPre = "UPDATE
						{$wbasedato}_000166
					SET
						pacact = 'on'
					WHERE
						pacact = 'off'
						AND pacfec = '".$rowsInfo[ 'Ingfei' ]."'
						AND pacdoc = '".$rowsInfo[ 'Pacdoc' ]."'
						AND pactdo = '".$rowsInfo[ 'Pactdo' ]."'
					";

			$resPread = mysql_query( $sqlPre, $conex ) or ( $data[ 'mensaje' ] = mysql_errno()." - Error en el query $sqlPre - ".mysql_error() );

			if( !$resRoot )
			{
				$data1[ 'error' ] = 1;
			}

			//Borro los datos de informacion del paciente (root_000037)
			$resRoot = mysql_query( $sqlRoot, $conex ) or ( $data[ 'mensaje' ] = mysql_errno()." - Error en el query $sqlRoot - ".mysql_error() );

			if( !$resRoot ){
				$data1[ 'error' ] = 1;
			}

			if( !empty( $aplMovhos ) )
			{
				//Borro los datos de movimiento hospitalario
				$sqlMov = "DELETE ".$aplMovhos."_000016, ".$aplMovhos."_000018
							FROM ".$aplMovhos."_000016, ".$aplMovhos."_000018
							WHERE ubihis = '".$historia."'
							AND ubiing = '".$ingreso."'
							AND inghis = ubihis
							AND inging = ubiing";

				$resMov = mysql_query( $sqlMov, $conex ) or ( $data[ 'mensaje' ] = mysql_errno()." - Error en el query $sqlMov - ".mysql_error() );

				if( !$resMov )
				{
					$data1[ 'error' ] = 1;
				}

				//libero la habitación de ser necesario. 2015-10-15
				$sqlhab = "UPDATE {$aplMovhos}_000020
							SET habhis = '',
								habing = '',
								habdis = 'on'
							WHERE habhis = '{$historia}'
								AND habing = '{$ingreso}'";

				$rshab = mysql_query( $sqlhab, $conex ) or ( $data[ 'mensaje' ] = mysql_errno()." - Error en el query $sqlhab - ".mysql_error() );
			}

			if( !empty( $aplHce ) )
			{

				//modificar el turno solicitado si lo tiene //2015-10-15
				$sqltur = " SELECT Mtrtur
							FROM {$aplHce}_000022
							WHERE mtrhis = '{$historia}'
							AND mtring = '{$ingreso}'";
				$rstur  = mysql_query( $sqltur, $conex );
				$rowtur = mysql_fetch_array( $rstur );
				$turno  = $rowtur[0];
				if( $turno != "" ){
					$sqlCtur = " UPDATE {$aplMovhos}_000178
									SET Atuest = 'off',
										Atullv = 'off'
								WHERE Atutur = '{$turno}'";
					$rsctur  = mysql_query( $sqlCtur, $conex );
				}
				//Borro los datos de movimiento hospitalario
				$sqlHce = "DELETE ".$aplHce."_000022
							FROM ".$aplHce."_000022
							WHERE mtrhis = '".$historia."'
							AND mtring = '".$ingreso."'";

				$resHce = mysql_query( $sqlHce, $conex ) or ( $data[ 'mensaje' ] = mysql_errno()." - Error en el query $sqlHce - ".mysql_error() );

				if( $resHce )
				{
					$user2 = explode("-",$user);
					( isset($user2[1]) )? $user2 = $user2[1] : $user2 = $user2[0];
					//Guardo LOG de borrado en tabla root_000037 por clave duplicada
						$q = "  INSERT INTO log_agenda (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera)
												VALUES ('".date('Y-m-d')."', '".date("H:i:s")."', '".$historia."', '".$ingreso."', 'Borrado tabla hce_000022', '".$user2."', 'anulacion desde admisiones ".$historia."-".$ingreso."')";
						$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
				}
				else{
					$data1[ 'error' ] = 1;
				}
			}

			//Si ya borro el registro de ingreso procedo a eliminar accidentes de tránsito
			//Borro el último ingreso
			$sqlAcc = "DELETE FROM
						".$wbasedato."_000148
					WHERE
						Acchis = '".$historia."'
						AND Accing = '".$ingreso."'
					";

			$resIng = mysql_query( $sqlAcc, $conex ) or ( $data[ 'mensaje' ] = mysql_errno()." - Error en el query $sqlAcc - ".mysql_error() );

			if( !$resIng ){
				$data1[ 'error' ] = 1;
			}

			$sqlpro = "SELECT Resnit as codigo, Resaut as aut
						FROM ".$wbasedato."_000205
						WHERE Reshis = '".$historia."'
						AND Resing = '".$ingreso."'";

			$respro = mysql_query( $sqlpro, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla ".$wbasedato."000205 ".mysql_errno()." - Error en el query $sqlpro - ".mysql_error() ) );
			if ($respro)
			{
				$numpro=mysql_num_rows($respro);
				if ($numpro>0)
				{
					while( $rowspro = mysql_fetch_assoc($respro) ){
						$sqlRes = "DELETE FROM
									".$wbasedato."_000209
									WHERE Cprnit = '".$rowspro['codigo']."'
									AND Cpraut = '".$rowspro['aut']."'";
						$sqlRes = "DELETE FROM
									".$wbasedato."_000209
									WHERE Cprhis = '".$historia."'
									AND Cpring = '".$ingreso."'";
						$rescup = mysql_query( $sqlRes, $conex ) or ( $data[ 'mensaje' ] = mysql_errno()." - Error en el query $sqlRes - ".mysql_error() );
					}

					$sqlResd = "DELETE FROM
								".$wbasedato."_000205
								WHERE Reshis = '".$historia."'
								AND Resing = '".$ingreso."'";
					mysql_query( $sqlResd, $conex );
				}
			}

			//Si ya borro el registro de ingreso procedo a eliminar accidentes de tránsito
			//Borro el último ingreso
			$sqlRes = "DELETE FROM
						".$wbasedato."_000204
					WHERE
						Tophis = '".$historia."'
						AND Toping = '".$ingreso."'
					";

			$resIng = mysql_query( $sqlRes, $conex ) or ( $data[ 'mensaje' ] = mysql_errno()." - Error en el query $sqlRes - ".mysql_error() );

			if( !$resIng )
			{
				$data1[ 'error' ] = 1;
			}

			//Si ya borro el registro de ingreso procedo a eliminar encabezado de eventos catastróficos
			//Borro el último ingreso
			$sqlEv = "DELETE ".$wbasedato."_000149 , ".$wbasedato."_000150  FROM
						".$wbasedato."_000149 , ".$wbasedato."_000150
					WHERE Evnhis = '".$historia."'
						AND Evning = '".$ingreso."'
						AND Devcod 	= Evncod
						AND Evnest  = 'on'";

			$resEv = mysql_query( $sqlEv, $conex ) or ( $data[ 'mensaje' ] = mysql_errno()." - Error en el query $sqlEv - ".mysql_error() );

			if( !$resEv )
			{
				$data1[ 'error' ] = 1;
			}
		}
	}

	echo json_encode( $data );
	return;
}

if (isset($accion) and $accion == 'mostrarDatosDemograficos')
 {
	$data = array('error'=>0,'mensaje'=>'','html'=>'','numRegistrosIng'=>'','numRegistrosPac'=>'');

	global $wbasedato;
	global $conex;
	global $wemp_pmla;

	$datosEnc = crearArrayDatos($wbasedato, "pac", "pac_", 3 );
	unset( $datosEnc[ 'Medico' ] );
	unset( $datosEnc[ 'Hora_data' ] );
	unset( $datosEnc[ 'Fecha_data' ] );
	unset( $datosEnc[ 'Seguridad' ] );
	$where = crearStringWhere( $datosEnc );

	//si no tiene ningun parametro para buscar
	if( empty( $where ) )
	{
		$data['mensaje']="Debe ingresar al menos un parametro de busqueda";
		$data['error']=1;
		echo json_encode($data);
		exit;
	}
	else{
		//reemplazo pacciu por paciu
		$where = str_replace( "pacciu", "paciu", $where );
	}
	
	$where = str_replace("pactdo", "pactid", $where);
	$where = str_replace("pacdoc", "Pacced", $where);
	/* 
		En mayo 2021, se cambió para que los datos demográficos del paciente estén en root_36 y de ahí se consulten.
		Se añadieron los campos con valor por defecto vacio y a medida de que el paciente haga un nuevo ingreso se irán 
		cargando de la tabla 100 de la última empresa que visitó.
	*/
	$sql = "select a.Pactid as pactdo,a.Pacced as pacdoc,a.pacfed,a.Pacap1,a.Pacap2,a.Pacno1,a.Pacno2,a.Pacnac as pacfna,
				a.Pacsex,a.Pacest,a.Pacdir,a.Pactel,a.Paciu,a.Pacbar,a.Pacdep,a.Paczon,a.Pactus,a.Pacofi,a.Paccor,a.Pacpan,a.Pacpet,
				a.Pacded,a.Pactrh,a.Pacpah,a.Pacdeh,a.Pacmuh,a.Pacmov,a.Pacned,a.Pacemp,a.Pactem,a.Paceem,a.Pactaf,a.Pacrem,a.Pacire,
				a.Paccac,a.Fecha_data,a.Pacdle,a.Pactle,a.Pacaud,a.Paczog
				from root_000036 a
				where a.pacced != ''";
	$sql .= $where;
	$sql .=" Order by  a.Pacced  ";

	$res = mysql_query( $sql, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla root_36 ".mysql_errno()." - Error en el query $sql - ".mysql_error() ) );

	if ($res)
	{
		$traerDeEmpresa = false;
		$num=mysql_num_rows($res);

		$rows=mysql_fetch_assoc($res);
		/*
			Valida si en la tabla root_36 estan los datos incompletos, 
			si es así, se pone una bandera para buscar los datos en la última empresa que visitó.
		*/
		if($rows['Pacpan'] == '' && $rows['Paciu'] == '' && $rows['Pacest'] == '' && $rows['Pacpah'] == ''){
			$traerDeEmpresa = true;
		}

		if( $num == 0 || $traerDeEmpresa ){
			/* Se busca documento y tipo de documentos en la root_00037, 
			 	trae la empresa ordenados por fecha de la mas reciente a la mas antigua. */
			$sql5 = "SELECT Oriori FROM root_000037
					  WHERE Oritid='".utf8_decode($datosEnc['pactdo'])."' AND Oriced='".utf8_decode($datosEnc['pacdoc'])."' 
					  ORDER BY Fecha_data DESC";

			$res5 = mysql_query( $sql5, $conex );
			if ($res5)
			{
				$num5= mysql_num_rows($res5);
				/** Si se encuentra alguna información con el documento  */
				if ($num5 > 0)
				{
					/** Se guarda la wemp_pmla actual */
					$wemp_pmla_anterior = $wemp_pmla;

					for($k=0; $k<$num5; $k++){
						$rowsee = mysqli_fetch_array($res5);
						/** Se actualiza la $wemp_pmla  con la primera encontra en el root_000037 */
						$wemp_pmla = $rowsee['Oriori'];
						$validaExistenciaTabla = validarExisteTabla100($conex, $wemp_pmla);
						if($validaExistenciaTabla){
							/** Se guarda la empresa actual del programa */
							$wbasedato_anterior = $wbasedato;
							/** Se busca la empresa a la que pertenece al informacion encontrada en la root_00037 */
							$wbasedato1 = consultarInstitucionPorCodigo($conex, $wemp_pmla);
							$wbasedato = $wbasedato1->baseDeDatos;

							/** Se genera query para traer la información del paciente para el autocomplete de admisiones */
							$where = str_replace("pactid", "pactdo", $where);
							$where = str_replace("Pacced", "pacdoc", $where);
							$sql = "select Pactdo,Pacdoc,pacfed,Pactat,Pacap1,Pacap2,Pacno1,Pacno2,Pacfna,Pacsex,Pacest,Pacdir,Pactel,Paciu,Pacbar,
								Pacdep,Paczon,Pactus,Pacofi,Paccea,Pacnoa,Pactea,Pacdia,Pacpaa,Pacact,Paccru,Pacnru,Pactru,Pacdru,Pacpru,Paccor,
								Pactam,Pacpan,Pacpet,Pacded,Pactrh,Pacpah,Pacdeh,Pacmuh,Pacmov,Pacned,Pacemp,Pactem,Paceem,
								Pactaf,Pacrem,Pacire,Paccac,Pactda,Pacddr,Pacdre,Pacmre,Pacmor,Paccre,a.Fecha_data, Pacdle, Pactle, Pacaud, Paczog 
								from ".$wbasedato."_000100 a
								where Pachis !='0'";
							  $sql .= $where;
							  $sql .=" Group by  Pachis  ";
							  $sql .=" Order by  Pacdoc  ";
		
							$res = mysql_query($sql, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla ".$wbasedato."000100 ".mysql_errno()." - Error en el query $sql - ".mysql_error() ) );
							if($res){
								$num = mysql_num_rows($res);
								$rows=mysql_fetch_assoc($res);
								if($num > 0){
									/** Se reestable la empresa inicial del programa */
									$wbasedato =  $wbasedato_anterior;
									break;
								}
							}
						}
					}
					/** Se reestablece $wemp_pmla con la empresa actual del programa */
					$wemp_pmla = $wemp_pmla_anterior;
				}
			}
		}
		/** Se devuelve la informacion encontrada del paciente */
		$data['numRegistrosPac'] = $num;
		if ($num>0)
		{	
			if( !$traerDeEmpresa ){

				$sql5 = "SELECT Oriori FROM root_000037
					  	WHERE Oritid='".utf8_decode($datosEnc['pactdo'])."' AND Oriced='".utf8_decode($datosEnc['pacdoc'])."' 
					  	ORDER BY Fecha_data DESC";
				$res5 = mysql_query( $sql5, $conex );
				if ($res5)
				{
					$num5= mysql_num_rows($res5);
					/** Si se encuentra alguna información con el documento  */
					if ($num5 > 0)
					{
						/** Se guarda la wemp_pmla actual */
						$wemp_pmla_anterior = $wemp_pmla;

						for($k=0; $k<$num5; $k++){
							$rowsee = mysqli_fetch_array($res5);
							/** Se actualiza la $wemp_pmla  con la primera encontra en el root_000037 */
							$wemp_pmla = $rowsee['Oriori'];
							$validaExistenciaTabla = validarExisteTabla100($conex, $wemp_pmla);
							if($validaExistenciaTabla){
								/** Se guarda la empresa actual del programa */
								$wbasedato_anterior = $wbasedato;
								/** Se busca la empresa a la que pertenece al informacion encontrada en la root_00037 */
								$wbasedato1 = consultarInstitucionPorCodigo($conex, $wemp_pmla);
								$wbasedato = $wbasedato1->baseDeDatos;

								/** Se genera query para traer la información del paciente para el autocomplete de admisiones */
								$where = str_replace("pactid", "pactdo", $where);
								$where = str_replace("Pacced", "pacdoc", $where);
								$sql = "select Pactat,Paccea,Pacnoa,Pactea,Pacdia,Pacpaa,Pacact,Paccru,Pacnru,Pactru,Pacdru,Pacpru,
									Pactam,Pactda,Pacddr,Pacdre,Pacmre,Pacmor,Paccre
									from ".$wbasedato."_000100 a
									where Pachis !='0'";
								$sql .= $where;
								$sql .=" Group by  Pachis  ";
								$sql .=" Order by  Pacdoc  ";
			
								$res2 = mysql_query($sql, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla ".$wbasedato."000100 ".mysql_errno()." - Error en el query $sql - ".mysql_error() ) );
								if($res2){
									$num = mysql_num_rows($res2);
									$rows2=mysql_fetch_assoc($res2);
									$result = array_merge($rows, $rows2);
									$rows = $result;
								}
								/** Se reestable la empresa inicial del programa */
								$wbasedato =  $wbasedato_anterior;
								break;
							}
						}
						/** Se reestablece $wemp_pmla con la empresa actual del programa */
						$wemp_pmla = $wemp_pmla_anterior;
					}
				}
			}
			for( $j = 0;$j<$num; $j++ )
			{ //solo se puede buscar por el nombre del campo
				//posicion de historia
				$data['numPosicionHistorias'][ $rows['Pachis'] ] = $j;

				foreach( $rows as $key => $value )
				{
					//se guarda en data con el prefijo pac_ y empezando en la posicion 3 hasta el final
					$data[ 'infopac' ][ "pac_".substr( $key, 3 ) ] =  utf8_encode( $value );
				}
				$data[ 'infopac' ][ 'pac_ciu' ] = $rows['Paciu'];

				//se deben buscar los nombres de los campos que tienen guardado el codigo

				//se consulta el nombre del pais nacimiento
				if (!empty( $rows['Pacpan'] ))
				{
					$res1=consultaNombrePais($rows['Pacpan']);
					if ($res1)
					{
						$num1=mysql_num_rows($res1);
						if ($num1>0)
						{
							$rows1=mysql_fetch_array($res1);
							$data[ 'infopac' ][ 'pac_pantxtPaiNac' ] = utf8_encode($rows1['Painom']);
						}
						else
						{
							$data[ 'error' ] = 1;
							$data['mensaje']="No se encontro el codigo del pais";
						}

					}
					else
					{
						$data['mensaje']="No se ejecuto la consulta de busqueda del pais";
						$data[ 'error' ] = 1;
					}
				}

				//se consulta el nombre del departamento nacimiento
				if (!empty( $rows['Pacdep'] ))
				{
					$res2=consultaNombreDepartamento($rows['Pacdep']);
					if ($res2)
					{
						$num2=mysql_num_rows($res2);
						if ($num2>0)
						{
							$rows2=mysql_fetch_array($res2);
							$data[ 'infopac' ][ 'pac_deptxtDepNac' ] = utf8_encode($rows2['Descripcion']);
						}
						else
						{
							$data[ 'error' ] = 0;//--> permitir la carga para corrección
							$data['mensaje'] .=" - No se encontro el codigo del departamento \n";
						}

					}
					else
					{
						$data[ 'error' ] = 1;
						$data['mensaje']="No se ejecuto la consulta de busqueda del departamento";
					}
				}

				//se consulta el nombre del municipio nacimiento
				if (!empty( $rows['Paciu'] ))
				{
					$res3=consultaNombreMunicipio($rows['Paciu']);
					if ($res3)
					{
						$num3=mysql_num_rows($res3);
						if ($num3>0)
						{
							$rows3=mysql_fetch_array($res3);
							$data[ 'infopac' ][ 'pac_ciutxtMunNac' ] = utf8_encode($rows3['Nombre']);
						}
						else
						{
							$data[ 'error' ] = 0;//--> permitir la carga para corrección
							$data['mensaje'] .= " - No se encontro el codigo del municipio \n";
							$data[ 'infopac' ][ 'pac_ciu' ] = "";
						}

					}
					else
					{
						$data[ 'error' ] = 1;
						$data['mensaje']="No se ejecuto la consulta de busqueda del municipio";
					}
				}

				//se consulta el nombre del pais de residencia
				if (!empty( $rows['Pacpah'] ))
				{
					$res4=consultaNombrePais($rows['Pacpah']);
					if ($res4)
					{
						$num4=mysql_num_rows($res4);
						if ($num4>0)
						{
							$rows4=mysql_fetch_array($res4);
							$data[ 'infopac' ][ 'pac_pahtxtPaiRes' ] = utf8_encode($rows4['Painom']);
						}
						else
						{
							$data[ 'error' ] = 0;//--> permitir la carga para corrección
							$data['mensaje'] .=" - No se encontro el codigo del pais de residencia \n";
						}

					}
					else
					{
						$data['mensaje']="No se ejecuto la consulta de busqueda del pais de residencia";
						$data[ 'error' ] = 1;
					}
				}

				//se consulta el nombre del departamento de residencia
				if (!empty( $rows['Pacdeh'] ))
				{
					$res4=consultaNombreDepartamento($rows['Pacdeh']);
					if ($res4)
					{
						$num4=mysql_num_rows($res4);
						if ($num4>0)
						{
							$rows4=mysql_fetch_array($res4);
							$data[ 'infopac' ][ 'pac_dehtxtDepRes' ] = utf8_encode($rows4['Descripcion']);
						}
						else
						{
							$data[ 'error' ] = 0;//--> permitir la carga para corrección
							$data['mensaje'] .=" - No se encontro el codigo del departamento residencia \n";
							$data[ 'infopac' ][ 'pac_deh' ] = "";
						}

					}
					else
					{
						$data[ 'error' ] = 1;
						$data['mensaje']="No se ejecuto la consulta de busqueda del departamento residencia";
					}
				}


				//se consulta el nombre del municipio de residencia
				if (!empty( $rows['Pacmuh'] ))
				{
					$res4=consultaNombreMunicipio($rows['Pacmuh']);
					if ($res4)
					{
						$num4=mysql_num_rows($res4);
						if ($num4>0)
						{
							$rows4=mysql_fetch_array($res4);
							$data[ 'infopac' ][ 'pac_muhtxtMunRes' ] = utf8_encode($rows4['Nombre']);
						}
						else
						{
							$data[ 'error' ] = 0;//--> permitir la carga para corrección
							$data['mensaje'] .= " - No se encontro el codigo del municipio de residencia \n";
							$data[ 'infopac' ][ 'pac_muh' ] = "";
						}

					}
					else
					{
						$data[ 'error' ] = 1;
						$data['mensaje']="No se ejecuto la consulta de busqueda del municipio de residencia";
					}
				}

				//se consulta el nombre del barrio de residencia
				if (!empty( $rows['Pacbar'] ) and !empty( $rows['Pacmuh']))
				{
					$res4=consultaNombreBarrio($rows['Pacbar'],$rows['Pacmuh']);
					if ($res4)
					{
						$num4=mysql_num_rows($res4);
						if ($num4>0)
						{
							$rows4=mysql_fetch_array($res4);
							$data[ 'infopac' ][ 'pac_bartxtBarRes' ] = utf8_encode($rows4['Bardes']);
						}
						else
						{
							$data[ 'error' ] = 0;//--> permitir la carga para corrección
							$data['mensaje'] .=" - No se encontro el codigo del barrio de residencia \n";
						}

					}
					else
					{
						$data[ 'error' ] = 1;
						$data['mensaje']="No se ejecuto la consulta de busqueda del barrio de residencia";
					}
				}
				else
				{
					if(!empty( $rows['Pacbar'] ) and empty( $rows['Pacmuh']))
					{
						$res4=consultaNombreBarrio($rows['Pacbar'],'');
						if ($res4)
						{
							$num4=mysql_num_rows($res4);
							if ($num4>0)
							{
								$rows4=mysql_fetch_array($res4);
								$data[ 'infopac' ][ 'pac_bartxtBarRes' ] = utf8_encode($rows4['Bardes']);
							}
							else
							{
								$data[ 'error' ] = 0;//--> permitir la carga para corrección
								$data['mensaje'] .=" - No se encontro el codigo del barrio de residencia \n";
							}

						}
						else
						{
							$data[ 'error' ] = 1;
							$data['mensaje']="No se ejecuto la consulta de busqueda del barrio de residencia";
						}
					}
				}

				//se consulta el nombre de la ocupacion
				if (!empty( $rows['Pacofi'] ))
				{
					$res4=consultaNombreOcupacion($rows['Pacofi']);
					if ($res4)
					{
						$num4=mysql_num_rows($res4);
						if ($num4>0)
						{
							$rows4=mysql_fetch_array($res4);
							$data[ 'infopac' ][ 'pac_ofitxtocu' ] = utf8_encode($rows4['Nombre']);
						}
						else
						{
							 $data[ 'error' ] = 0;
							$data['mensaje']="No se encontro el codigo de la ocupacion";
						}

					}
					else
					{
						$data[ 'error' ] = 1;
						$data['mensaje']="No se ejecuto la consulta de busqueda de la ocupacion";
					}
				}

				//se consulta el nombre del departamento del responsable del usuario
				if (!empty( $rows['Pacdre'] ))
				{
					$res4=consultaNombreDepartamento($rows['Pacdre']);
					if ($res4)
					{
						$num4=mysql_num_rows($res4);
						if ($num4>0)
						{
							$rows4=mysql_fetch_array($res4);
							$data[ 'infopac' ][ 'pac_dretxtDepResp' ] = utf8_encode($rows4['Descripcion']);
						}
						else
						{
							$data[ 'error' ] = 0;//--> permitir la carga para corrección
							$data['mensaje'] .=" - No se encontro el codigo del departamento responsable del usuario \n";
							$data[ 'infopac' ][ 'pac_dre' ] = "";
						}

					}
					else
					{
						$data[ 'error' ] = 1;
						$data['mensaje']="No se ejecuto la consulta de busqueda del departamento del responsable del usuario";
					}
				}

				//se consulta el nombre del municipio del responsable del usuario
				if (!empty( $rows['Pacmre'] ))
				{
					$res4=consultaNombreMunicipio($rows['Pacmre']);
					if ($res4)
					{
						$num4=mysql_num_rows($res4);
						if ($num4>0)
						{
							$rows4=mysql_fetch_array($res4);
							$data[ 'infopac' ][ 'pac_mretxtMunResp' ] = utf8_encode($rows4['Nombre']);
						}
						else
						{
							$data[ 'error' ] = 0;//--> permitir la carga para corrección
							$data['mensaje'] .=" - No se encontro el codigo del municipio del responsable del usuario \n";
							$data[ 'infopac' ][ 'pac_mre' ] = "";
						}

					}
					else
					{
						$data[ 'error' ] = 1;
						$data['mensaje']="No se ejecuto la consulta de busqueda del municipio del responsable del usuario";
					}
				}

				$data[ 'infoing' ][$j] = $data[ 'infopac' ];

			} //fin for


		} //si trae registros de la 100
		else
		{
			// $data[ 'mensaje' ] = "No se encontro informacion para los datos ingresados";
		}

	}
	else
	{
		$data[ 'error' ] = 1;
	}
	/***fin busqueda en la tabla 100***/

	// $data[ 'error' ] = 0;

	echo json_encode($data);
	return;
}

if (isset($accion) and $accion == 'consultarAseguradoraVehiculo')
{
	$data = array('error'=>0,'mensaje'=>'','html'=>'','valor'=>'','usu'=>'','tarifa'=>'');

	$json = consultarAseguradorasVehiculo( $q, $wbasedato );

	echo $json;

	exit;
}

if (isset($accion) and $accion == 'consultarSegundoResp')
{
	$data = array('error'=>0,'mensaje'=>'','html'=>'','valor'=>'','usu'=>'','cod'=>'','nom'=>'','topeS'=>'');


	$sql="select Topfid, Toptse,Empnom
		  from ".$wbasedato."_000194,".$wbasedato."_000024
		  where Topest = 'on'
		  and Empcod = Topfid
		  ";
	$res= mysql_query( $sql, $conex );
	if ($res)
	{
		$num=mysql_num_rows($res);
		if ($num>0)
		{
			$rows=mysql_fetch_array($res);
			$data['cod']=$rows['Topfid'];
			$data['nom']=$rows['Empnom'];
			$data['topeS']=$rows['Toptse'];
		}
	}
	else
	{
		$data['error']=1;
		$data['mensaje']="No se ejecuto la consulta a la tabla de topes ".$wbasedato."_000194";
	}

	echo json_encode($data);

	exit;
}

if (isset($accion) and $accion == 'consultarPrimerResp')
{
	$data = array('error'=>0,'mensaje'=>'','html'=>'','valor'=>'','usu'=>'','cod24'=>'','nom'=>'','tar'=>'','vsm'=>'','tope'=>'','cod193'=>'','vre'=>'',);

	//salario minimo y tope
	$sql="SELECT Topmin, Toptpr
			FROM {$wbasedato}_000194
		   WHERE Topfin <= '{$fechaAccidente}'
			 AND Topffi >= '{$fechaAccidente}'";
	$res= mysql_query( $sql, $conex );
	if ($res)
	{
		$num=mysql_num_rows($res);
		if ($num>0)
		{
			$rows=mysql_fetch_array($res);
			$data['vsm']=$rows['Topmin'];
			$data['tope']=$rows['Toptpr'];
		}
	}
	else
	{
		$data['error']=1;
		$data['mensaje']="No se ejecuto la consulta ".mysql_errno()." - Error en el query $sql - ".mysql_error()."";
	}

	//asegurodora
	if ($asegu != '')
	{
		$sql1 = "SELECT Asecod, Asedes,Emptar,Asecoe
				FROM ".$wbasedato."_000193, ".$wbasedato."_000024
				WHERE Asecod ='".trim($asegu)."'
				and Asecoe = Empcod
				and Aseest = 'on'
				";
		 // $data['mensaje']=$sql1;
		$res1= mysql_query( $sql1, $conex );
		if ($res1)
		{
			$num1=mysql_num_rows($res1);
			if ($num1>0)
			{
				$rows1=mysql_fetch_array($res1);
				$data['cod24']=$rows1['Asecoe'];
				$data['tar']=$rows1['Emptar'];
				$data['nom']=$rows1['Asedes'];
			}
		}
		else
		{
			$data['error']=1;
			$data['mensaje']="No se ejecuto la consulta ".mysql_errno()." - Error en el query $sql - ".mysql_error()."";
		}
	}

	echo json_encode($data);

	exit;
}

if (isset($accion) and $accion == 'listaEventos')
{

	$data=listaEventos();
	echo json_encode($data);

	exit;
}

if (isset($accion) and $accion == 'mostrarDetalleEvento')
{
	$data= array('error'=>0,'mensaje'=>'','html'=>'','dep'=>'','mun'=>'');


	global $wbasedato;
	global $conex;

	 $i = count( $data[ 'infoing' ] );

	$sql = "SELECT
				Devcod, Deveve, Devdir, Devded, Devfac, Devhac, Devdep, Devmun, Devzon, Devdes
			FROM
				".$wbasedato."_000149
			WHERE
				Devcod = ".$codigo."
				AND Devest = 'on'
			";

	$res = mysql_query( $sql, $conex ) or ( $data[ 'error' ] = utf8_encode( "Error consultando la tabla ".$wbasedato."000149 ".mysql_errno()." - Error en el query $sql - ".mysql_error() ) );

	if ($res)
	{

		$num=mysql_num_rows($res);

		if ($num>0)
		{
			if( $rows=mysql_fetch_array($res, MYSQL_ASSOC ) )
			{
				foreach( $rows as $key => $value )
				{

					if( substr( $key, 0, 3 ) == 'Dev' ){
						//se guarda en data con el prefijo pac_ y empezando en la posicion 3 hasta el final
						$data[ 'infoing' ][$i][ "det_Cat".substr( $key, 3 ) ] = utf8_encode( $value );
					}
				}

				$data[ 'infoing' ][$i][ "dat_Catevento" ] = utf8_encode( $rows[ 'Deveve' ] );
				//20140305$data[ 'infoing' ][$i][ "det_ux_evccec" ] = utf8_encode( $rows[ 'Evncla' ] );
				$data[ 'infoing' ][$i][ "det_ux_evccec" ] = "";

				//Consulto el nombre del departamento en donde ocurrio el accidente
				$res = consultaNombreDepartamento( $rows[ 'Devdep' ] );
				$num = mysql_num_rows( $res );
				if( $rowdp = mysql_fetch_array( $res ) ){
					$dep = $rowdp[ 'Descripcion' ];
				}
				else{
					$dep = '';
				}
				$data[ 'infoing' ][$i][ "Catdep" ] = $dep;

				//Consulto el nombre el municipio en donde ocurrio el accidente
				$res = consultaNombreMunicipio( $rows[ 'Devmun' ] );
				$num = mysql_num_rows( $res );
				if( $rowdp = mysql_fetch_array( $res ) ){
					$mun = $rowdp[ 'Nombre' ];
				}
				else{
					$mun = '';
				}
				$data[ 'infoing' ][$i][ "Catmun" ] = $mun;
			}
		}
	}
	echo json_encode($data);

	exit;
}

if (isset($accion) and $accion == 'consultarCcoTopes')
{
	$data = array('error'=>0,'mensaje'=>'','html'=>'','valor'=>'','usu'=>'');

	$json = consultarCcoTopesResp( $q,$wbasedato,$wemp_pmla );

	echo $json;

	exit;
}

if (isset($accion) and $accion == 'mostrarTopes')
{

	$data=mostrarFormTopes($responsable , $historia, $ingreso,$documento , $tipodocumento,$esadmision);
	echo json_encode($data);

	exit;
}

if (isset($accion) and $accion == 'insertarTopes')
{

	$data = grabartoperesponsable($whistoria,$wingreso,$responsable , $insertar,$activo,$esadmision,$documento,$tipodocumento);
	echo json_encode($data);
	exit;
}

if (isset($accion) and $accion == 'recalculartopes2')
{

	$data = calculartopes2($responsable,$whistoria,$wingreso);
	echo ($data);
	exit;
}

if (isset($accion) and $accion == 'regrabarCargo')
{
	global $conex;
	global $wemp_pmla;
	global $wbasedato;
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "facturacion" );
	$data = regrabarCargo($idCargo, $responsble, $tipoIngreso, $tipoPaciente, '', 'REGRABACION DESDE CARGOS', 'on');

	echo json_encode($data);
	exit;
}

if (isset($accion) and $accion == 'adicionar_fila')
{
	global $wbasedato;
	global $conex;

	global $fechaAct;
	global $horaAct;

	$data = array('error'=>0,'html'=>'','mensaje'=>'');

	$data['html']="<tr id=".$id_fila.">";

	$pos = strpos($id_fila, "_");
	$indc = substr($id_fila, 0, $pos);

	$data['html'].= "<td><table>";

	$data['html'].= "<tr>
			<td style='width:45;' class='numeroresponsable corchete' rowspan=4>R{$indc}</td>
			<td class='encabezadotabla titulo_responsable' align='center' colspan='9' nowrap='nowrap'>RESPONSABLE <p>( <input type='radio' style='width:16px; height:16px;' name='res_comtxtNumcon' estadoAnterior='off' onclick=' cambiarEstadoComplementariedad( this ); ' id='res_comtxtNumcon{$id_fila}'> Aplica complementariedad en cirug&iacute;a )</p></td>
			<td style='width:45;' class='corchetei' rowspan=4><img border=0 src='../../images/medical/root/borrar.png' onclick='removerFila(\"".$id_fila."\",\"".$wbasedato."\",\"tabla_eps\");'/></td>
			</tr>";
	$data['html'].= "<tr>";
	$data['html'].= "<td class='fila1'>Tipo de Responsable</td>";

	/*2014-10-20 DATOS PARA RESPONSABLE PARTICULAR*/
	$data['html'].= "<td class='fila1 dato_particulares' style='display:none;'>Tipo Documento</td>";
	$data['html'].= "<td class='fila1 dato_particulares' style='display:none;'>Numero Documento</td>";
	$data['html'].= "<td class='fila1 dato_particulares' style='display:none;'>Nombre Responsable</td>";
	$permiteCambioTarifaParticular = consultarAplicacion2($conex,$wemp_pmla,"cambiaTarifaParticular");
	if( $permiteCambioTarifaParticular ){
		$data['html'].= "<td class='fila1 dato_particulares' style='display:none;'>Tarifa Responsable</td>";
	}
	/*FIN*/


	$data['html'].= "<td class='fila1 dato_esconder_particulares'>Aseguradora</td>";
	$data['html'].= "<td class='fila1 dato_esconder_particulares'>Plan</td>";
	$data['html'].= "<td class='fila1 dato_esconder_particulares'>N&uacute;mero de P&oacute;liza</td>";
	$data['html'].= "<td class='fila1'>N&uacute;mero de Contrato</td>";
	$data['html'].= "<td class='fila1'>Fecha inicio <br> responsabilidad</td>";
	$data['html'].= "<td class='fila1'>Fecha final <br> responsable</td>";
	$data['html'].= "<td class='fila1'>Topes</td>";
	//$data['html'].= "<td class='fila1' align='center'><span onclick=\"addFila('tabla_eps','$wbasedato','$wemp_pmla');\" class='efecto_boton' >".NOMBRE_ADICIONAR1."</span></td>";
	//echo "<td class='fila1' align='center'><span onclick=\"addResponsable();\" class='efecto_boton' >".NOMBRE_ADICIONAR1."</span></td>";
	$data['html'].= "</tr>";

	$data['html'].= "<tr>";
	$data['html'].="<td class='fila2'>";$res1=consultaMaestros('000105','Selcod,Seldes',$where="Seltip = '09' and Selest='on'",'','Selcod','2');
	// $data['html'].=crearSelectHTMLAcc($res1,'ing_tpaselTipRes'.$id_fila.'','ing_tpaselTipRes'.$id_fila.'',$param);
		$data['html'].= "<SELECT msgcampo='Tipo Responsable' id='ing_tpaselTipRes".$id_fila."' name='ing_tpaselTipRes' onchange='validarTipoResp(this);' ux='_ux_mreemp_ux_pacemp_ux_accemp'>";
		$data['html'].= "<option value=''>Seleccione...</option>";

        $num = mysql_num_rows( $res1 );
        if( $num > 0 )
		{
            while( $rows = mysql_fetch_assoc( $res1 ) )
			{
			   $value = "";
			   $des = "";
			   $i = 0;
			   foreach( $rows  as $key => $val ){
					if( $i == 0 ){
						$value = $val;
					}else{
						$des .= "-".$val;
					}
					$i++;
			   }
               $data['html'].= "<option value='{$value}'>".substr( $des, 1 )."</option>";
            }
		}

	$data['html'].= "</SELECT>";
	$data['html'].="<input type='hidden' id=".$id_fila."_bd name=".$id_fila."_bd value='' ></td>";

	/*2014-10-20 DATOS PARA RESPONSABLE PARTICULAR*/
	$res1res=consultaMaestros('root_000007','Codigo,Descripcion,alfanumerico, docigualhis',$where="Estado='on'",'','');
	$data['html'].="<td class='fila2 dato_particulares' style='display:none;'>";
	$data['html'].= "<SELECT msgcampo='Tipo Documento' name='res_tdo' msgError='' disabled>";
		$data['html'].= "<option value=''>Seleccione...</option>";
        $num = mysql_num_rows( $res1res );
        if( $num > 0 )
		{
            while( $rows = mysql_fetch_array( $res1res, MYSQL_ASSOC ) )
			{
				$value        = $rows['Codigo'];
				$des          = $rows['Descripcion'];
				$alfanumerico = $rows['alfanumerico'];
				$docXhis      = $rows['docigualhis'];
               $data['html'].= "<option value='{$value}' alfanumerico='{$alfanumerico}' docXhis='{$docXhis}'>".$des."</option>";
            }
		}
	$data['html'].= "</SELECT>";
	$data['html'].= "</td>";

	$data['html'].= "<td class='fila2 dato_particulares' style='display:none;'>";
	$data['html'].= "<input type='text' name='res_doc' msgError msgcampo='Documento' ondblclick='completarParticular( this )' onblur='completarParticular( this )'  disabled>";
	$data['html'].= "</td>";
	$data['html'].= "<td class='fila2 dato_particulares' style='display:none;'>";
	$data['html'].= "<input type='text' name='res_nom' msgError msgcampo='Nombre' disabled>";
	$data['html'].= "</td>";
	if( $permiteCambioTarifaParticular ){
		$data['html'].= "<td class='fila2 dato_particulares' style='display:none;'>";
		$data['html'].= "<input type='text' name='ing_tartxt' id='ing_tartxt'  msgError msgcampo='Tarifa' disabled>";
		$data['html'].= "<input type='hidden' name='ing_tarhid' id='ing_tarhid'>";
		$data['html'].= "</td>";
	}
	/*FIN*/


	$data['html'].="<td class='fila2 dato_esconder_particulares'>";
	$data['html'].="<input type='text' msgcampo='Aseguradora' name='ing_cemtxtCodAse' id='ing_cemtxtCodAse".$id_fila."' class='reset' ux='_ux_pacres_ux_mreres' msgError='Digite la Aseguradora'>";
	$data['html'].="<input type='hidden' name='ing_cemhidCodAse' id='ing_cemhidCodAse".$id_fila."' ux='_ux_mrecer_ux_paccer_ux_arsars'>";
		$data['html'].="<input type='hidden' name='ing_enthidNomAse' id='ing_enthidNomAse".$id_fila."'>";
	// $data['html'].="<input type='hidden' name='dat_Accno3hidNomAse' id='dat_Accno3hidNomAse' ux=''>";
	$data['html'].="</td>";

	$data['html'].="<td class='fila2 dato_esconder_particulares'><select msgcampo='Plan del responsable' name='ing_plaselPlan' id='ing_plaselPlan".$id_fila."'  class='reset campoRequerido' onChange='' ux='_ux_mrepla' msgError >";
	$data['html'].="<option value=''>Seleccione...</option>";
	$data['html'].="</select>";
	$data['html'].="</td>";
	$data['html'].="<td class='fila2 dato_esconder_particulares'><input type='text' name='ing_poltxtNumPol' id='ing_poltxtNumPol".$id_fila."' class='reset' numerico ux='_ux_pacpol' msgaqua='Digite el numero de la Poliza'></td>";
	$data['html'].="<td class='fila2'><input type='text' name='ing_ncotxtNumCon' id='ing_ncotxtNumCon".$id_fila."' class='reset' numerico ux='' msgaqua='Digite el numero del contrato'></td>";
	$data['html'].= "<td> <input type='text' name='res_firtxtNumcon' fecha id='res_firtxtNumcon{$id_fila}' class='reset' msgaqua='0000-00-00'></td>";
	$data['html'].= "<td> <input type='text' name='res_ffrtxtNumcon' fecha id='res_ffrtxtNumcon{$id_fila}' class='reset' msgaqua='0000-00-00'></td>";
	$data['html'].= "<td align='center'>";
	$data['html'].= "<input type='button' value='Topes' id='btnTopes".$id_fila."' name='btnTopes' style='width:100;height:25' onClick=\"mostrarDivTopes('ing_cemhidCodAse".$id_fila."',this)\">
					 <input type='hidden' name='res_fir' id='res_fir{$id_fila}' value=''>";
	$data['html'].= "</td>";
	//$data['html'].="<td align='center'><span class='efecto_boton' onclick='removerFila(\"".$id_fila."\",\"".$wbasedato."\",\"tabla_eps\");'>".NOMBRE_BORRAR."</span></td>";

	$data['html'].= "</tr><tr><td colspan=9>";

	$botonAgregarCUP = "<input type='button' style='width:20;' onclick='agregarCUPS(\"".$id_fila."\")' value='+' />";

	$data['html'].= "<div id='div_datos_autorizacion' style='width:100%'>";
	$data['html'].= "<center><table>";
	$data['html'].= "<th style='background-color:#6694E3; font-size:10pt;' colspan='6'>Datos Autorizaci&oacute;n</th>";
	$data['html'].= "<tr>";
	$data['html'].= "<td class='fila1'>Numero Autorizacion</td>";
	$data['html'].= "<td class='fila1'>Fecha Autorizacion</td>";
	$data['html'].= "<td class='fila1'>Hora Autorizacion</td>";
	$data['html'].= "<td class='fila1'>Nombre Persona Autorizadora</td>";
	$data['html'].= "<td class='fila1'>Codigo Servicio Autorizado (cups)".$botonAgregarCUP."</td>";
	$data['html'].= "<td class='fila1'>Pago Compartido</td>";
	$data['html'].= "</tr>";
	$data['html'].= "<tr>";
	$data['html'].= "<td class='fila2'><input type='text' name='ing_ordtxtNumAut' id='ing_ordtxtNumAut".$id_fila."' class='reset' ux='_ux_mseaut' placeholder='Digite el Numero'></td>";
	$data['html'].= "<td class='fila2'><input type='text' name='ing_fhatxtFecAut' id='ing_fhatxtFecAut".$id_fila."' fecha value='".$fechaAct."' ux='_ux_ordfec'></td>";
	$data['html'].= "<td class='fila2'><input type='text' name='ing_hoatxtHorAut' id='ing_hoatxtHorAut".$id_fila."' hora value='".$horaAct."' ></td>";
	$data['html'].= "<td class='fila2'><input type='text' name='ing_npatxtNomPerAut' id='ing_npatxtNomPerAut".$id_fila."' class='reset'  placeholder='Digite el Nombre'></td>";
	$data['html'].= "<td class='fila2'>";
	$data['html'].= "<div><input type='text' name='ing_cactxtcups' style='width:200px;' id='ing_cactxtcups".$id_fila."' class='reset'  placeholder='Digite el Codigo o el nombre'>";
	$data['html'].= "<input type='hidden' name='ing_cachidcups' id='ing_cachidcups".$id_fila."' >";
	$data['html'].= "<input type='hidden' name='id_idcups' id='id_idcups".$id_fila."' ></div>";
	$data['html'].= "</td>";
	$data['html'].= "<td class='fila2'>";
	$param="class='reset' ";
	$res1=consultaMaestros('000105','Selcod,Seldes',$where="Seltip = '22' and Selest='on'",'','seldes','2');
	//crearSelectHTMLAcc($res1,'ing_pcoselPagCom','ing_pcoselPagCom',$param);
	$data['html'].= "<SELECT id='ing_pcoselPagCom".$id_fila."' name='ing_pcoselPagCom' class='reset'>";
	$data['html2'] = "<SELECT id='ing_pcoselPagCom_original' name='ing_pcoselPagCom_original' style='display:none' class='reset'>";
	$data['html'].= "<option value=''>Seleccione...</option>";
	$data['html2'].= "<option value=''>Seleccione...</option>";
	$num = mysql_num_rows( $res1 );
	if( $num > 0 ){
		while( $rows = mysql_fetch_assoc( $res1 ) ){
		    $value = "";
		    $des = "";
		    $i = 0;
		    foreach( $rows  as $key => $val ){
			   if( $i == 0 ){
					$value = $val;
			   }else{
					$des .= "-".htmlentities($val);
			   }
			   $i++;
		    }
			$data['html'].= "<option value='{$value}' >".substr( $des, 1 )."</option>";
			$data['html2'].= "<option value='{$value}' codigoRelacion='22-{$value}'>".substr( $des, 1 )."</option>";
		}
	}
	$data['html'].= "</SELECT>";
	$data['html2'].= "</SELECT>";
	$data['html'] .= $data['html2'];
	$data['html'].= "</td>";
	$data['html'].= "</tr>";
	$data['html'].= "</table></center>";
	$data['html'].= "</div>";

	$data['html'].= "</td></tr></table></td>";

	$data['html'].="</tr>";

	$data['html'].= '<script>
					$("#'.$id_fila.' [fecha]").datepicker({
						dateFormat:"yy-mm-dd",
						fontFamily: "verdana",
						dayNames: [ "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado", "Domingo" ],
						monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ],
						dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
						dayNamesShort: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
						monthNamesShort: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic" ],
						changeMonth: true,
						changeYear: true,
						yearRange: "c-100:c+100"
					});
					$( "#'.$id_fila.' [hora]" ).val( $( "#horaAct" ).val() );
					$("#'.$id_fila.' [hora]").on({
						focus: function(){
							//Si es igual a vacio o a la mascara que tenga por defecto
							if( $( this ).val() == "" || $( this ).val() == "__:__:__" ){
								$( this ).val( $( "#horaAct" ).val() );
							}
						}
					});
					$( "#'.$id_fila.' [fecha][name!=\'res_ffrtxtNumcon\']" ).val( $( "#fechaAct" ).val() );
					$( "#'.$id_fila.' [fecha][name=\'res_ffrtxtNumcon\'][value=\'\']" ).removeAttr( \'requerido\');
					$("#'.$id_fila.' [fecha][name!=\'res_ffrtxtNumcon\']" ).on({
						focus: function(){
							if( $( this ).val() == ""){
								$( this ).val( $( "#fechaAct" ).val() );
							}
						}
					});

					$("#'.$id_fila.' [hora]").mask("Hn:Nn:Nn");
					$("#'.$id_fila.' [hora]").keyup(function(){
						if ( $(this).val().substring(0,1) == "2" && $(this).val().substring(0,2)*1 > 23 )
						{
							$(this).val( "2_:__:__" );
							$(this).caret(1);
						}
					});
					buscarCUPS("'.$id_fila.'");
					$("#'.$id_fila.' [fecha]").keyup(function(event){
						if( event.which != 8 ){ //Diferente de back space
							if ( $(this).val().length == 4 ){
								$(this).val( $(this).val()+"-" );
							}else if ( $(this).val().length == 7 ){
								$(this).val( $(this).val()+"-" );
							}
						}
					});
					buscarTarifaParticular();

					</script>';

	echo json_encode($data);

	exit;
}

if (isset($accion) and $accion == 'eliminar_planes')
{
	$data = array('error'=>0,'html'=>'','mensaje'=>'');
	// //se actualiza el estado(activo) de la accion a off para que no lo muestre
						// $sql = "UPDATE ".$wbasedato."_000003
							// SET  Accesa = 'off'
							// WHERE Id='".$id_eliminar."' ";

						// $res = mysql_query( $sql ,$conex) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

						// //$data['html'] = '';
						// //$data['mensaje']=$sql;
	echo json_encode($data);

	exit;
}

if (isset($accion) and $accion == 'consultarClasificacinConceptos')
{
	$data = array('error'=>0,'mensaje'=>'','html'=>'','valor'=>'','usu'=>'');

	$json = consultarClasificacionConceptosFact( $q,$wbasedato,$wemp_pmla );

	echo $json;

	exit;
}

if (isset($accion) and $accion == 'adicionar_fila_tope')
{
	global $wbasedato;
	global $conex;

	$data = array('error'=>0,'html'=>'','mensaje'=>'');

	$data['html'].="<tr class='fila2' id=".$id_fila.">";
			//se agrega un hidden por tr para concatenar los valores de los campos a validar que no sean repetidos
			$data['html'].="<td><input type='hidden' id='hdd_".$id_fila."' name='hdd_".$id_fila."' idtr='".$id_fila."' value='' >";
			$res1=consultaMaestros('000202','Ccfcod,Ccfnom',$where="Ccfest = 'on'",'','','2');
			$data['html'].="<input type='hidden' id='top_reshidTopRes".$id_fila."' name='top_reshidTopRes' value='' >";
			$data['html'].="<input type='hidden' id=".$id_fila."_bd name=".$id_fila."_bd value='' >";
			$data['html'].= "<SELECT id='top_tcoselTipCon".$id_fila."' name='top_tcoselTipCon' onBlur=\"valRepetidosTopes('f.$id_fila');\">";
				//$data['html'].= "<option value=''>Seleccione...</option>";

				$num = mysql_num_rows( $res1 );

				if( $num > 0 )
				{
					while( $rows = mysql_fetch_array( $res1, MYSQL_ASSOC ) )
					{
						$value = "";
						$des = "";
						$i = 0;
							foreach( $rows  as $key => $val )
							{
							   if( $i == 0 )
							   {
									$value = $val;
							   }
							   else
							   {
									$des .= "-".$val;
							   }

								$i++;
							}
						$selected = ( $value == "*" ) ? " selected " : "";
						$data['html'].= "<option value='{$value}' $selected>".substr( $des, 1 )."</option>";
					}
				}

				$data['html'].= "</SELECT>";
			$data['html'].="</td>";
			$data['html'].="<td>
			<input type='text' name='top_clatxtClaTop' id='top_clatxtClaTop".$id_fila."' class='' ux='' value='TODAS LAS  CLASIFICACIONES' placeholder='Ingrese la clasificacion' onBlur=\"valRepetidosTopes('f.$id_fila');\">
			<input type='hidden' name='top_clahidClaTop' id='top_clahidClaTop".$id_fila."' class='' ux='' value='*'>";
			$data['html'].="</td>";
			$data['html'].="<td><input type='text' name='top_ccotxtCcoTop' id='top_ccotxtCcoTop".$id_fila."' value='TODOS LOS CENTROS DE COSTO' class='' ux='' placeholder='Ingrese el centro de costo' onBlur=\"valRepetidosTopes('f.$id_fila');\">
			<input type='hidden' name='top_ccohidCcoTop' id='top_ccohidCcoTop".$id_fila."' class='' value='*' ux='' >";
			$data['html'].="</td>";
			$data['html'].="<td><input type='text' name='top_toptxtValTop' id='top_toptxtValTop".$id_fila."' class='' ux='' placeholder='Ingrese el tope' onfocus=\"valNumero('top_toptxtValTop".$id_fila."');\">";
			$data['html'].="</td>";
			$data['html'].="<td><input type='text' name='top_rectxtValRec' id='top_rectxtValRec".$id_fila."' class='' value='100' ux='' placeholder='Ingrese el valor' onBlur=\"valPorcentaje(this);\">";
			$data['html'].="<input type='hidden' name='top_id' id='top_id".$id_fila."' class='' value=''>";
			$data['html'].="</td>";
			$data['html'].="<td><input type='checkbox' name='top_diachkValDia' id='top_diachkValDia".$id_fila."' class='' ux='' >";
			$data['html'].="</td>";
			$data['html'].="<td align='center'><span class='efecto_boton' onclick='removerFila(\"".$id_fila."\",\"".$wbasedato."\",\"tabla_topes\");'>".NOMBRE_BORRAR."</span></td>";
			$data['html'].="</tr>";

	echo json_encode($data);

	exit;
}

if (isset($accion) and $accion == 'consultaClientes')
 {
	$data = array('error'=>0,'mensaje'=>'','html'=>'');
	$per="";
	$soc="";
	$afin="";

	/*Consulta Personalidades*/
				$sql = "SELECT *
						from root_000101
						where Perced='".$cedula."'
						and Perest='on'
						";
				$res = mysql_query( $sql, $conex );

				if ($res)
				{
					$num=mysql_num_rows($res);
					if ($num>0)
					{
						$rows=mysql_fetch_array($res);
						$data['mensaje']="El paciente tiene el cargo de ".$rows['Percar']." llamar a Comunicaciones";
						$per=1;
					}
				}
				else
				{
					$data['error']=1;
					$data['mensaje']="No se ejecuto la consulta a la tabla de personalidades";
				}

	/*Fin consultar Personalidades*/

	/*Consulta Socios*/
				$sql1 = "SELECT Socap1,Socap2,Socnom
						from socios_000001
						where Socced='".$cedula."'
						and Socact='A'
						";
				$res1 = mysql_query( $sql1, $conex );

				if ($res1)
				{
					$num1=mysql_num_rows($res1);
					if ($num1>0)
					{
						$rows1=mysql_fetch_array($res1);
						$data['mensaje']="El paciente es socio de la PMLA";
						$soc=1;
					}
				}
				else
				{
					$data['error']=1;
					$data['mensaje']="No se ejecuto la consulta a la tabla de socios";
				}

	/*Fin consultar Socios*/


	/**Consulta clientes afin*/
	// En este procedimiento pregunto si el paciente es cliente AFIN o no, y de que tipo

		$wafin = clienteMagenta($cedula, $tipoDoc, $wtpa, $wcolorpac);

		if ($wafin)
		{
			$data['mensaje']="El paciente es afin";
			$afin=1;
		}


	/**Fin consulta clientes afin*/

	if ($per != "" && $soc != "")
	{
		$data['mensaje']="El paciente tiene el cargo de ".$rows['Percar']." y es socio de la PMLA llamar a Comunicaciones";
	}
	else if ($afin != "" && $soc != "")
	{
		$data['mensaje']="El paciente es socio de la PMLA  y es afin";
	}
	else if ($afin != "" && $per != "")
	{
		$data['mensaje']="El paciente tiene el cargo de ".$rows['Percar']." y es afin";
	}

	echo json_encode($data);
	return;
}

if (isset($accion) and $accion == 'consultarSiActivo')
{
	global $wbasedato;
	global $conex;
	global $hay_unix;

    $aplicacion=consultarAplicacion2($conex,$wemp_pmla,"movhos");

	$data = array('error'=>0,'mensaje'=>'','html'=>'','estado'=>"","his"=>"","ing"=>"");

	$tieneConexionUnix = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'conexionUnix' );
	$ping_unix = ping_unix();
	if($hay_unix && $tieneConexionUnix == 'on' && $ping_unix == true )
	{
		$cedula = trim ($cedula);
		$a = new admisiones_erp( 'estadoPaciente', $tipoDoc, $cedula );
		$data = $a->data;

		if( $data['mensaje'] != "" ){ //El paciente esta activo
			$data['mensaje'] = "El paciente esta activo en UNIX con la historia ".$data['mensaje']."\nEGRESARLO EN UNIX para continuar.";
			$data['error'] = 1;
		}
		unset( $a );
		echo json_encode($data);
		return;
	}

	$sql = "";
	if( $aplicacion == "" ){
		$sql = "SELECT pacact as est, Pachis as his, Ingnin as ing
				  FROM ".$wbasedato."_000100 a LEFT JOIN ".$wbasedato."_000101 b ON (Pachis=Inghis)
				 WHERE Pactdo = '".utf8_decode($tipoDoc)."'
				   AND Pacdoc = '".utf8_decode($cedula)."'";
	}else{
		$sql = "  SELECT Ubiald as est, Ubihis as his, Ubiing as ing
					FROM root_000037 a, ".$aplicacion."_000018 b
				   WHERE Oritid = '".utf8_decode($tipoDoc)."'
					 AND Oriced = '".utf8_decode($cedula)."'
					 AND Orihis = Ubihis
					 AND Oriori = '".$wemp_pmla."'
				ORDER BY b.fecha_data desc, b.hora_data desc limit 1";
	}
	$res = mysql_query( $sql, $conex );

	if ($res)
	{
		$num=mysql_num_rows($res);
		if ($num>0)
		{
			$rows=mysql_fetch_array($res);
			$estado = $rows['est'];
			if( $aplicacion != "" ){ //si hay movhos, consulto Ubiald, alta definitiva, el estado es lo contrario a este valor
				( $estado == "on" )? $estado = 'off' : $estado = 'on';
			}
			$data['estado'] = $estado;

			if( $estado == 'on' ){
				$data['error'] = 1;
				$data['mensaje']= "El paciente se encuentra ACTIVO en estos momentos en MATRIX,\nNo se puede crear uno nuevo sin darle de ALTA.";
				$data['his'] = $rows['his'];
				$data['ing'] = $rows['ing'];
			}
		}
	}else{
		$data['error'] = 1;
		$data['mensaje'] = "No se ejecuto la consulta sobre el estado del paciente";
	}

	echo json_encode($data);
	return;
}

if (isset($accion) and $accion == 'consultarSiRechazado'){
	global $wbasedato;
	global $conex;
	global $hay_unix;

	$data             = array('error'=>0,'mensaje'=>'','html'=>'','estado'=>"","his"=>"","ing"=>"");
	$alias            = "movhos";
	$wcco             = explode("-", $wcco);
	$wcco             = $wcco[1];
	$permitirAdmision = false;
    $aplicacion       = consultarAplicacion2($conex,$wemp_pmla,$alias);

	if ($aplicacion == ""){

		$sql = "SELECT count(*) cantidad
				  FROM ".$wbasedato."_000003
				 WHERE Ccoest = 'on'
				   AND Ccocod = '".utf8_decode($wcco)."'
				   AND Ccourg = 'on'";
	}else{

		$sql = "SELECT count(*) cantidad
				  FROM ".$aplicacion."_000011
				 WHERE Ccoest = 'on'
				   AND Ccocod = '".utf8_decode($wcco)."'
				   AND Ccourg = 'on'";
	}
	$res = mysql_query( $sql, $conex );
	$row = mysql_fetch_assoc( $res );
	if( $row['cantidad'] > 0 ){
		$permitirAdmision = true;
	}

	$sql = " SELECT Clrdes descripcion, clrnom nombrePaciente, clrdfu fuenteFactura, clrdfa factura, clrdob observacion
			   FROM ".$wbasedato."_000236
			  INNER JOIN
			  	".$wbasedato."_000237 on ( clrdti = clrtid and clrdce = clrced )
			  WHERE Clrtid = '".utf8_decode($tipoDoc)."'
			    AND Clrced = '".utf8_decode($cedula)."'";
	$res = mysql_query( $sql, $conex );

	$num=mysql_num_rows($res);
	if ($num>0){

		$i = 1;
		while( $row = mysql_fetch_array($res) ){
			if( $i == 1 ){
				/*if(! $permitirAdmision )
					$data['error'] = 1;*/
				$data['mensaje'] = " El paciente ha sido Rechazado:\nCausa: {$row['descripcion']}\nFacturas:\n";
			}else{
			}
			$data['mensaje'] .= "$i. fuente:{$row['fuenteFactura']} - Num:{$row['factura']}  valor:{$row['observacion']}\n";
			$i++;
		}
	}

	echo json_encode($data);
	return;
}

if (isset($accion) and $accion == 'consultarSiPreanestesia'){
	global $wbasedato;
	global $conex;
	global $hay_unix;

	$data             = array('error'=>0,'turno'=>'','html'=>'','estado'=>"","his"=>"","ing"=>"");
	$alias            = "movhos";
	$wcco             = explode("-", $wcco);
	$wcco             = $wcco[1];
	$permitirAdmision = false;
    $aplicacion       = consultarAplicacion2($conex,$wemp_pmla,$alias);
    $wbasedatoMov     = consultarAplicacion2($conex,$wemp_pmla,$alias);

	if ($aplicacion == ""){

		$sql = "SELECT Ccoayu
				  FROM ".$wbasedato."_000003
				 WHERE Ccoest = 'on'
				   AND Ccocod = '".utf8_decode($wcco)."'";
	}else{

		$sql = "SELECT Ccoayu
				  FROM ".$aplicacion."_000011
				 WHERE Ccoest = 'on'
				   AND Ccocod = '".utf8_decode($wcco)."'";
	}
	$res = mysql_query( $sql, $conex );
	$row = mysql_fetch_assoc( $res );

	if( $row['Ccoayu'] == "on" ){

		$qprean = " SELECT ahthte historiaTemporal, ahttur turnoCirugia, id
					  FROM {$wbasedatoMov}_000204
					 WHERE ahttdo = '$tipoDoc'
					   AND ahtdoc = '$cedula'
					   AND ahtest = 'on'
					   AND ahtahc != 'on'
					   AND ahttur != ''
					   AND ahtori = 'preanestesia'
					   AND ahtccd = ''
					 ORDER BY id desc
					 LIMIT 1";
		$rsPreAn          = mysql_query($qprean,$conex);
		$rowPreAn         = mysql_fetch_assoc( $rsPreAn );
		$historiaTemporal = $rowPreAn['historiaTemporal'];
		$turnoCir204      = $rowPreAn['turnoCirugia'];
		$turnoCirugia     = explode("_", $turnoCir204);
		$turnoCirugia     = $turnoCirugia[1];
		$data['turno']    = $turnoCirugia;

		if( $data['turno'] == "" ){
			$data['error'] = 1;
		}
	}else{
		$data['error'] = 1;
	}

	echo json_encode($data);
	return;
}
// --> 	Llamar al paciente que esta en la sala de espera, a la atencion en la ventanilla.
// 		Jerson trujillo, 2015-07-02
if (isset($accion) and $accion == 'llamarPacienteAtencion'){

	global $wbasedato;
	global $conex;
	global $hay_unix;

	$wbasedatoMov 	= consultarAplicacion2($conex,$wemp_pmla,"movhos");
	$usuario		= explode("-", $user);
	$usuario		= $usuario[1];
	$respuesta		= array('Error' => FALSE, 'Mensaje', '');

	// --> Validar que el paciente no haya sido llamado
	$sqlValLla = "
	SELECT Atuusu, Atuurt ,Atullv, Atupad, Atuart, Atuetr
	  FROM ".$wbasedatoMov."_000178
	 WHERE Atutur = '".$turno."'
	   AND (Atullv = 'on' OR Atupad = 'on' OR Atuart = 'on' OR Atuetr = 'on')
	";
	$resValLla = mysql_query($sqlValLla, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlValLla):</b><br>".mysql_error());
	if($rowValLla = mysql_fetch_array($resValLla))
	{
		$respuesta['Error'] 	= TRUE;

		if($rowValLla['Atullv'] == 'on')
		{
			$respuesta['Mensaje'] 	= "El paciente ya está siendo llamado a admisión por ";
			$usuario				= $rowValLla['Atuusu'];
		}
		elseif($rowValLla['Atupad'] == 'on')
			{
				$respuesta['Mensaje'] 	= "El paciente está en proceso de admisión con ";
				$usuario				= $rowValLla['Atuusu'];
			}
			elseif($rowValLla['Atuart'] == 'on')
				{
					$respuesta['Mensaje'] 	= "El paciente está siendo llamado a reclasificación triage por ";
					$usuario				= $rowValLla['Atuurt'];
				}
				elseif($rowValLla['Atuetr'] == 'on')
					{
						$respuesta['Mensaje'] 	= "El paciente está en proceso de reclasificación triage con ";
						$usuario				= $rowValLla['Atuurt'];
					}

		$sqlNomUsu = "
		SELECT Descripcion
		  FROM usuarios
		 WHERE Codigo = '".$usuario."'
		";
		$resNomUsu = mysql_query($sqlNomUsu, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlNomUsu):</b><br>".mysql_error());
		if($rowNomUsu = mysql_fetch_array($resNomUsu))
			$nomUsuario = $rowNomUsu['Descripcion'];
		else
			$nomUsuario = '';

		$respuesta['Mensaje'].= ": ".$nomUsuario;
		$respuesta['Mensaje'] = utf8_encode($respuesta['Mensaje']);
	}
	else
	{
		// --> realizar el llamado
		$sqlLlamar = "
		UPDATE ".$wbasedatoMov."_000178
		   SET Atullv = 'on',
			   Atufll = '".date('Y-m-d')."',
			   Atuhll = '".date("H:i:s")."',
			   Atuusu = '".$usuario."',
			   Atuven = '".$ventanilla."'
		 WHERE Atutur = '".$turno."'
		";
		mysql_query($sqlLlamar, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlLlamar):</b><br>".mysql_error());

		// --> Registrar en el log el llamado
		$sqlRegLLamado = "
		INSERT INTO ".$wbasedatoMov."_000179 (Medico,				Fecha_data,				Hora_data,				Logtur,			Logacc,					Logusu,			Seguridad,				id)
									   VALUES('".$wbasedatoMov."',	'".date('Y-m-d')."',	'".date("H:i:s")."',	'".$turno."', 	'llamadoVentanilla',	'".$usuario."', 'C-".$wbasedatoMov."',	NULL)
		";
		mysql_query($sqlRegLLamado, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlRegLLamado):</b><br>".mysql_error());
	}

	echo json_encode($respuesta);
	return;
}
// --> 	Cancelar el Llamado del paciente que esta en la sala de espera.
// 		Jerson trujillo, 2015-07-02
if (isset($accion) and $accion == 'cancelarLlamarPacienteAtencion'){

	global $wbasedato;
	global $conex;
	global $hay_unix;

	$wbasedatoMov 	= consultarAplicacion2($conex,$wemp_pmla,"movhos");
	$usuario		= explode("-", $user);
	$usuario		= $usuario[1];

	// --> realizar el llamado
	$sqlCancelar = "
	UPDATE ".$wbasedatoMov."_000178
	   SET Atullv = 'off'
	 WHERE Atutur = '".$turno."'
	";
	mysql_query($sqlCancelar, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCancelar):</b><br>".mysql_error());

	// --> Registrar en el log la cancelacion del llamado
	$sqlRegLLamado = "
	INSERT INTO ".$wbasedatoMov."_000179 (Medico,				Fecha_data,				Hora_data,				Logtur,			Logacc,				Logusu,			Seguridad,				id)
								   VALUES('".$wbasedatoMov."',	'".date('Y-m-d')."',	'".date("H:i:s")."',	'".$turno."', 	'cancelaLlamado',	'".$usuario."', 'C-".$wbasedatoMov."',	NULL)
	";
	mysql_query($sqlRegLLamado, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlRegLLamado):</b><br>".mysql_error());

	//echo json_encode($data);
	return;
}
// --> 	Cancelar el turno de un paciente.
// 		Jerson trujillo, 2015-07-13.
if (isset($accion) and $accion == 'cancelarTurno'){

	global $wbasedato;
	global $conex;
	global $hay_unix;

	$wbasedatoMov 	= consultarAplicacion2($conex,$wemp_pmla,"movhos");
	$usuario		= explode("-", $user);
	$usuario		= $usuario[1];
	$respuesta		= array('Error' => FALSE, 'Mensaje' => '');

	// --> Validar que el paciente no haya sido llamado
	$sqlValLla = "
	SELECT Atuusu, Atuurt ,Atullv, Atupad, Atuart, Atuetr
	  FROM ".$wbasedatoMov."_000178
	 WHERE Atutur = '".$turno."'
	   AND (Atullv = 'on' OR Atupad = 'on' OR Atuart = 'on' OR Atuetr = 'on')
	";
	$resValLla = mysql_query($sqlValLla, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlValLla):</b><br>".mysql_error());
	if($rowValLla = mysql_fetch_array($resValLla))
	{
		$respuesta['Error'] 	= TRUE;

		if($rowValLla['Atullv'] == 'on')
		{
			$respuesta['Mensaje'] 	= "No se puede cancelar el turno, el paciente ya está siendo llamado a admisión por ";
			$usuario				= $rowValLla['Atuusu'];
		}
		elseif($rowValLla['Atupad'] == 'on')
			{
				$respuesta['Mensaje'] 	= "No se puede cancelar el turno, el paciente está en proceso de admisión con ";
				$usuario				= $rowValLla['Atuusu'];
			}
			elseif($rowValLla['Atuart'] == 'on')
				{
					$respuesta['Mensaje'] 	= "No se puede cancelar el turno, el paciente está siendo llamado a reclasificación triage por ";
					$usuario				= $rowValLla['Atuurt'];
				}
				elseif($rowValLla['Atuetr'] == 'on')
					{
						$respuesta['Mensaje'] 	= "No se puede cancelar el turno, el paciente está en proceso de reclasificacián triage con ";
						$usuario				= $rowValLla['Atuurt'];
					}

		$sqlNomUsu = "
		SELECT Descripcion
		  FROM usuarios
		 WHERE Codigo = '".$usuario."'
		";
		$resNomUsu = mysql_query($sqlNomUsu, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlNomUsu):</b><br>".mysql_error());
		if($rowNomUsu = mysql_fetch_array($resNomUsu))
			$nomUsuario = $rowNomUsu['Descripcion'];
		else
			$nomUsuario = '';

		$respuesta['Mensaje'].= ": ".$nomUsuario;
		$respuesta['Mensaje'] = utf8_encode($respuesta['Mensaje']);
	}
	else
	{
		// --> Inactivar el turno
		$sqlTur = "
		UPDATE ".$wbasedatoMov."_000178
		   SET Atuest = 'off',
			   Atullv = 'off'
		 WHERE Atutur = '".$turno."'
		";
		mysql_query($sqlTur, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTur):</b><br>".mysql_error());

		// --> Registrar en el log la cancelacion del turno.
		$sqlCancelarTurno = "
		INSERT INTO ".$wbasedatoMov."_000179 (Medico,				Fecha_data,				Hora_data,				Logtur,			Logacc,				Logusu,			Seguridad,				id)
									   VALUES('".$wbasedatoMov."',	'".date('Y-m-d')."',	'".date("H:i:s")."',	'".$turno."', 	'turnoCancelado',	'".$usuario."', 'C-".$wbasedatoMov."',	NULL)
		";
		mysql_query($sqlCancelarTurno, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCancelarTurno):</b><br>".mysql_error());
	}

	echo json_encode($respuesta);
	return;
}

// --> 	Genera el html de los turnos cancelados.
// 		Jerson trujillo, 2015-07-15.
if (isset($accion) and $accion == 'verTurnosCancelados'){
	global $wbasedato;
	global $conex;
	global $hay_unix;
    global $tema;

    $wcliame		= consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
	$wbasedatoMov 	= consultarAplicacion2($conex,$wemp_pmla,"movhos");
	$usuario		= explode("-", $user);
	$usuario		= $usuario[1];

	echo "
	<table id='tablaListaTurnos' style='' align='center' id='tablaPacTurnos'>
		<tr align='center'>
			<td rowspan='2' class='encabezadoTabla'>Turno</td>
			<td rowspan='2' class='encabezadoTabla'>Documento</td>
			<td rowspan='2' class='encabezadoTabla'>Nombre</td>
			<td colspan='2' class='encabezadoTabla'>Asignaci&oacute;n turno</td>
			<td colspan='4' class='encabezadoTabla'>Log Acci&oacute;n</td>
			<td rowspan='2' class='encabezadoTabla'>Habilitar</td>
		</tr>
		<tr align='center'>
			<td class='encabezadoTabla'>Fecha</td>
			<td class='encabezadoTabla'>Hora</td>
			<td class='encabezadoTabla'>Fecha</td>
			<td class='encabezadoTabla'>Hora</td>
			<td class='encabezadoTabla'>Responsable</td>
			<td class='encabezadoTabla'>Estado</td>
		</tr>
		";

	// --> Consultar turnos cancelados desde hace 15 dias
	$dateMenos2 = strtotime(date('Y-m-d'));
    $dateMenos2 = strtotime("-4 day", $dateMenos2);

	$sqlTurCancelados = "
	SELECT A.*, B.Fecha_data AS FechaCan, B.Hora_data AS HoraCan, descripcion, 'Cancelado' estado
	  FROM ".$wbasedatoMov."_000178 AS A, ".$wbasedatoMov."_000179 AS B, usuarios
	 WHERE A.Fecha_data BETWEEN '".date('Y-m-d', $dateMenos2)."' AND '".date('Y-m-d')."'
	   AND Atuest = 'off'
	   AND Atutur = Logtur
	   AND Logacc = 'turnoCancelado'
	   AND Logusu = codigo
	   AND A.Atutem = '".$tema."'
	 GROUP BY Logtur
	 UNION
	SELECT A.*, B.Fecha_data AS FechaCan, B.Hora_data AS HoraCan, descripcion, 'Sin finalizar admisión' estado
	  FROM ".$wbasedatoMov."_000178 AS A, ".$wbasedatoMov."_000179 AS B, usuarios
	 WHERE A.Fecha_data BETWEEN '".date('Y-m-d', $dateMenos2)."' AND '".date('Y-m-d')."'
	   AND Atuest = 'on'
	   AND Atupad = 'on'
	   AND Atuadm != 'on'
	   AND Atutur = Logtur
	   AND Logacc = 'iniciaAdmision'
	   AND Logusu = codigo
	 GROUP BY Logtur
	 ORDER BY REPLACE(Atutur, '-', '')*1 DESC
	";
    
	$resTurCancelados = mysql_query($sqlTurCancelados, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTurCancelados):</b><br>".mysql_error());

	if(mysql_num_rows($resTurCancelados) == 0)
		echo "<tr class='fila1'><td colspan='10' align='center'>No se encontraron registros.</td></tr>";

	$coloFila = 'fila1';
	while($rowTurCancelados = mysql_fetch_array($resTurCancelados))
	{
		$nombrePaciente = obtenerNombrePaciente($rowTurCancelados['Atutdo'], $rowTurCancelados['Atudoc']);
		$coloFila 		= (($coloFila == 'fila2') ? 'fila1' : 'fila2');
		echo "
		<tr class='".$coloFila."'>
			<td align='center'><b>".substr($rowTurCancelados['Atutur'], 4)."</b></td>
			<td>".$rowTurCancelados['Atutdo']."-".$rowTurCancelados['Atudoc']."</td>
			<td>".$nombrePaciente."</td>
			<td align='center'>".$rowTurCancelados['Fecha_data']."</td>
			<td align='center'>".$rowTurCancelados['Hora_data']."</td>
			<td align='center'>".$rowTurCancelados['FechaCan']."</td>
			<td align='center'>".$rowTurCancelados['HoraCan']."</td>
			<td>".$rowTurCancelados['descripcion']."</td>
			<td><b>".$rowTurCancelados['estado']."</b></td>
			<td align='center'><img style='cursor:pointer;' width='15' heigth='15' src='../../images/medical/sgc/Refresh-128.png'	onclick='habilitarTurno(\"".$rowTurCancelados['Atutur']."\", this)'></td>
		</tr>
		";
	}
	echo"</table>";

	return;
}

// --> 	Habilita un turno cancelado.
// 		Jerson trujillo, 2015-07-15.
if (isset($accion) and $accion == 'habilitarTurno'){

	global $wbasedato;
	global $conex;
	global $hay_unix;

	$wbasedatoMov 	= consultarAplicacion2($conex,$wemp_pmla,"movhos");
	$usuario		= explode("-", $user);
	$usuario		= $usuario[1];

	$sqlHabTurno = "
	UPDATE ".$wbasedatoMov."_000178
	   SET Atuest = 'on',
		   Atupad = 'off'
	 WHERE Atutur = '".$turno."'
	";
	$sqlHabTurno = mysql_query($sqlHabTurno, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlHabTurno):</b><br>".mysql_error());

	if($sqlHabTurno)
	{
		// --> Registrar en el log la habilitacion del turno.
		$sqlLogHabTurno = "
		INSERT INTO ".$wbasedatoMov."_000179 (Medico,				Fecha_data,				Hora_data,				Logtur,			Logacc,				Logusu,			Seguridad,				id)
									   VALUES('".$wbasedatoMov."',	'".date('Y-m-d')."',	'".date("H:i:s")."',	'".$turno."', 	'turnoHabilitado',	'".$usuario."', 'C-".$wbasedatoMov."',	NULL)
		";
		mysql_query($sqlLogHabTurno, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlLogHabTurno):</b><br>".mysql_error());
	}

	return;
}

// --> 	Llama la funcion que lista los pacientes con turno
// 		Jerson trujillo, 2015-07-15.
if (isset($accion) and $accion == 'listarPacientesConTurno'){

	global $wbasedato;
	global $conex;
	global $hay_unix;

	listarPacientesConTurno();

	return;
}

// --> 	Apagar la alerta de llamado a la ventanilla y registrar el inicio del proceso de admision
// 		Jerson trujillo, 2015-07-15.
if (isset($accion) and $accion == 'apagarAlertaDeLlamado'){

	global $wbasedato;
	global $conex;
	global $hay_unix;

	$wbasedatoMov 	= consultarAplicacion2($conex,$wemp_pmla,"movhos");
	$usuario		= explode("-", $user);
	$usuario		= $usuario[1];

	$sqlApagAlerta = "
	UPDATE ".$wbasedatoMov."_000178
	   SET Atullv = 'off',
	       Atupad = 'on'
	 WHERE Atutur = '".$turno."'
	";
	mysql_query($sqlApagAlerta, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlApagAlerta):</b><br>".mysql_error());

	// --> Registrar en el log el inicio del proceso de admision
	$sqlRegLLamado = "
	INSERT INTO ".$wbasedatoMov."_000179 (Medico,				Fecha_data,				Hora_data,				Logtur,			Logacc,				Logusu,			Seguridad,				id)
								   VALUES('".$wbasedatoMov."',	'".date('Y-m-d')."',	'".date("H:i:s")."',	'".$turno."', 	'iniciaAdmision',	'".$usuario."', 'C-".$wbasedatoMov."',	NULL)
	";
	mysql_query($sqlRegLLamado, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlRegLLamado):</b><br>".mysql_error());

	return;
}

//---mirar
if (isset($accion) and $accion == 'cambiarOpcionesSelect'){

	global $wbasedato;
	global $conex;
	global $hay_unix;

	$wbasedatoCliame 	= consultarAplicacion2($conex,$wemp_pmla,"facturacion");
	// --> Validar que el puesto de trabajo este disponible
	$query = "SELECT Selcod , Seldes
				FROM ".$wbasedatoCliame."_000105
			   WHERE  Selrel='06'
			   AND Selest ='on'
			   AND  FIND_IN_SET ( '".$valor."' ,selcam )";
	$res = mysql_query($query, $conex) or die("<b>ERROR EN QUERY MATRIX(query):</b><br>".mysql_error());
	$htmlresp ='';
	while($row = mysql_fetch_array($res))
	{
		$htmlresp .=','.$row['Selcod'];
	}

	echo $htmlresp;
	return;
}


// --> 	Actualiza el puesto de trabajo asociado a un usuario
// 		Jerson trujillo, 2015-07-15.
if (isset($accion) and $accion == 'cambiarPuestoTrabajo'){

	global $wbasedato;
	global $conex;
	global $hay_unix;

	$wbasedatoMov 	= consultarAplicacion2($conex,$wemp_pmla,"movhos");
	$usuario		= explode("-", $user);
	$usuario		= $usuario[1];
	$respuesta 		= array("Error" => FALSE, "Mensaje" => "");

	// --> Validar que el puesto de trabajo este disponible
	$sqlValPuesTra = "
	SELECT Descripcion
	  FROM ".$wbasedatoMov."_000180, usuarios
	 WHERE Puecod = '".$puestoTrabajo."'
	   AND Pueusu != ''
	   AND Pueusu = Codigo
	";
	$resValPuesTra = mysql_query($sqlValPuesTra, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlValPuesTra):</b><br>".mysql_error());
	if($respetarOcupacion == 'true' && $rowValPuesTra = mysql_fetch_array($resValPuesTra))
	{
		$respuesta["Error"] 	= TRUE;
		$respuesta["Mensaje"] 	= 'Esta ventanilla ya esta ocupada por '.$rowValPuesTra['Descripcion'];
	}
	else
	{
		// --> Quitar cualquier puesto de trabajo asociado al usuario
		$sqlUpdatePues = "
		UPDATE ".$wbasedatoMov."_000180
		   SET Pueusu = ''
		 WHERE Pueusu = '".$usuario."'
		";
		mysql_query($sqlUpdatePues, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUpdatePues):</b><br>".mysql_error());

		if($puestoTrabajo != '')
		{
			// --> Asignar el nuevo puesto de trabajo
			$sqlUpdatePues = "
			UPDATE ".$wbasedatoMov."_000180
			   SET Pueusu = '".$usuario."'
			 WHERE Puecod = '".$puestoTrabajo."'
			";
			mysql_query($sqlUpdatePues, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUpdatePues):</b><br>".mysql_error());
		}
	}

	echo json_encode($respuesta);
	return;
}

if (isset($accion) and $accion == 'consultarFechaHoraActual'){

	$fechaHoraActual = date("Y-m-d|H:i:s");
	$fechaHoraActual = explode("|", $fechaHoraActual );
	$respuesta = array('fecha'=>$fechaHoraActual[0], 'hora'=>$fechaHoraActual[1]);
	echo json_encode( $respuesta );
	return;
}

// Proceso para consultar historia
if (isset($accion) and $accion == 'consultar_historia_activa'){
	$data = array('error'=>0,'mensaje'=>'','html'=>'','sql'=>'');
	$data["historia_activa"] = 'off';
	$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
	$wbasedatoCliame = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
	$wbasedatoMovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$wbasedatoHce    = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");

	$data["wbasedatoCliame"] = $wbasedatoCliame;
	$data["wbasedatoMovhos"] = $wbasedatoMovhos;
	$data["wbasedatoHce"]    = $wbasedatoHce;

	$wbasedato_HCE   = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");
	$sql = "SELECT 	m11.Cconom, c100.Pacact, c100.Pachis, c100.Pacno1, c100.Pacno2, c100.Pacap1, c100.Pacap2, c101.Ingsei AS serv_ingreso,
					c101.Ingfei AS fec_ingreso, c101.Inghin AS hor_ingreso, c101.Ingnin AS ult_ingreso, c100.Pacdoc AS documento, c100.Pactdo AS tipo_documento,
					m18.Ubiald AS alta_definitiva
			FROM 	{$wbasedatoCliame}_000100 AS c100
					INNER JOIN
					{$wbasedatoCliame}_000101 AS c101 ON (c100.Pachis = c101.Inghis)
					INNER JOIN
					{$wbasedatoMovhos}_000011 AS m11 ON (c101.Ingsei = m11.Ccocod)
					INNER JOIN
					{$wbasedatoMovhos}_000018 AS m18 ON (c100.Pachis = m18.Ubihis AND c101.Ingnin = m18.Ubiing)
			WHERE 	c100.Pachis='{$historia_alta}'
			ORDER BY CONVERT(c101.Ingnin, UNSIGNED INTEGER) DESC
			LIMIT 0,1";
			// AND c100.Pacact = 'on'
	if($result = mysql_query($sql, $conex))
	{
		if(mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_assoc($result);
			$spn_hist_alta_historia = $row["Pachis"];
			$spn_hist_alta_ingreso  = $row["ult_ingreso"];
			$spn_hist_alta_nombres  = (trim($row["Pacno1"].' '.$row["Pacno2"].' '.$row["Pacap1"].' '.$row["Pacap2"]));
			$spn_hist_alta_fecha    = $row["fec_ingreso"].' '.$row["hor_ingreso"];
			$spn_hist_alta_servicio = $row["serv_ingreso"].'-'.($row["Cconom"]);
			$spn_historia_activa    = $row["Pacact"];
			$spn_hist_alta_definitiv= $row["alta_definitiva"];

			$msj_alta = ($spn_hist_alta_definitiv == 'on') ? '<span style="color:green;font-weight:bold;">Con alta definitiva</span>': '<span style="color:red;font-weight:bold;">Sin alta definitiva</span>';

			$excluir_ss = ($row["serv_ingreso"] == '1130' && $spn_historia_activa == 'on') ? 'disabled="disabled"': '';
			$checkbox_alta  = '';
			$estado_ingreso = '<span style="color:red;font-weight:bold;">INACTIVO</span> '.$msj_alta;
			if($spn_historia_activa == 'on')
			{
				$data["historia_activa"] = 'on';
				$url_egreso = '/matrix/admisiones/procesos/egreso_erp.php?wemp_pmla='.$wemp_pmla.'&documento='.$row["documento"].'&wtipodoc='.$row["tipo_documento"].'&historia='.$historia_alta.'&ingreso='.$spn_hist_alta_ingreso.'&ccoEgreso='.$row["serv_ingreso"];
				if($spn_hist_alta_definitiv == 'off')
				{
					$checkbox_alta = ' <br><div id="div_chk_alta" style="">[<span style="font-weight:bold;">Dar alta definitiva</span> <input type="checkbox" id="chk_dar_alta_otro_ss" '.$excluir_ss.' style="padding:0px; width:12px; height:12px;" onclick="altaPacienteOtroServicio(\''.$row["Pachis"].'\',\''.$row["ult_ingreso"].'\',\''.$wuser.'\',\''.$spn_hist_alta_nombres.'\');">]</div>';
				}
				else
				{
					$checkbox_alta .= ' <input type="button" id="btn_egresar" style="display:; width:100px;" value="Egresar" onclick="abrirEgresarPaciente(\''.$url_egreso.'\')">';
				}

				$estado_ingreso = '<span style="color:green;font-weight:bold;">ACTIVO</span> '.$msj_alta;
			}

			$data["html"] = '<br><table id="tbl_info_alta" align="center" style="">
								<tr>
									<td class="encabezadoTabla">Historia</td>
									<td class="fila1"><span id="spn_hist_alta_historia">'.$spn_hist_alta_historia.'</span></td>
								</tr>
								<tr>
									<td class="encabezadoTabla">Ingreso reciente</td>
									<td class="fila2"><span id="spn_hist_alta_ingreso">'.$spn_hist_alta_ingreso.'</span></td>
								</tr>
								<tr>
									<td class="encabezadoTabla">Paciente</td>
									<td class="fila1"><span id="spn_hist_alta_nombres">'.($spn_hist_alta_nombres).'</span></td>
								</tr>
								<tr>
									<td class="encabezadoTabla">Fecha ingreso</td>
									<td class="fila2"><span id="spn_hist_alta_fecha">'.$spn_hist_alta_fecha.'</span></td>
								</tr>
								<tr>
									<td class="encabezadoTabla">Servicio ingreso</td>
									<td class="fila1"><span id="spn_hist_alta_servicio">'.$spn_hist_alta_servicio.'</span></td>
								</tr>
								<tr>
									<td class="encabezadoTabla">Estado ingreso</td>
									<td class="fila2"><span id="spn_hist_alta_servicio">'.$estado_ingreso.$checkbox_alta.'</span></td>
								</tr>
							</table>';
		}
		else
		{
			$data["error"] = 1;
			$data["mensaje"] = ("No se encontraron datos en Matrix para la historia ingresada número [$historia_alta].\n\nEs posible que esté INACTIVA.");
		}
	}
	else
	{
		$data["error"]     = 1;
		$data["mensaje"]   = ("No se pudo consultar información de la historia [$historia_alta].");
		$data["sql_error"] = mysql_error().PHP_EOL.$sql;
	}
	echo json_encode($data);
	return;
}

if(isset($accion) and $accion == 'verificarTriageUrgencias'){

	$wbasedatoMov 	= consultarAplicacion2($conex,$wemp_pmla,"movhos");
	$wbasedatoHce 	= consultarAplicacion2($conex,$wemp_pmla,"hce");
	$forYcampoTriage = consultarAliasPorAplicacion($conex, $wemp_pmla, "ObservacionesFormHCETriage");
	$forYcampoTriage = explode("-", $forYcampoTriage);

	$tabla = $forYcampoTriage[0];
	unset($forYcampoTriage[0]); // Se elimina la posicion de la tabla para luego unir los consecutivos.
	$consecutivos_tabla = implode("','",$forYcampoTriage);

	$qprean = " SELECT ahthte historiaTemporal, ahttur turnoCirugia, id
				  FROM {$wbasedatoMov}_000204
				 WHERE ahttdo = '$tipoDoc'
				   AND ahtdoc = '$documento'
				   AND ahtest = 'on'
				   AND ahtahc != 'on'
				   AND ahttur != ''
				   AND ahtori != 'preanestesia'
				 ORDER BY id desc
				 LIMIT 1";

	$rsPreAn          = mysql_query($qprean,$conex);
	$rowPreAn         = mysql_fetch_assoc( $rsPreAn );
	$historiaTemporal = $rowPreAn['historiaTemporal'];

	if( $historiaTemporal != "" ){
		$query = " SELECT movdat
				  	 FROM {$wbasedatoHce}_".$tabla."
				  	WHERE movhis = '{$historiaTemporal}'
				  	  AND moving = '1'
				  	  AND movcon in ('".$consecutivos_tabla."')";
		$rs    = mysql_query( $query, $conex );
		while( $row = mysql_fetch_array($rs) ){
			$title .= "{$row[0]}<br><br>";
		}
	}else{
		$title = "<div align=center class=fila1><span class=subtituloPagina2 style=font-size:10px;>TRIAGE NO ENCONTRADO </span></div>";
	}
	echo $title;
	return;
}

if(isset($accion) and $accion == 'solicitarCambioDocumento'){

	$fechaHoy      = date('Y-m-d');
	$horaHoy       = date('H:i:s');
	$justificacion = utf8_encode( $justificacion );
	$user2         = explode("-",$user);
	( isset($user2[1]) )? $user2 = $user2[1] : $user2 = $user2[0];

	$query = " SELECT COUNT(*)
				 FROM {$wbasedato}_000288
				WHERE Scdhis = '{$whistoria}'
				  AND Scding = '{$wingreso}'
				  AND Scdest = 'on'";
	$rs = mysql_query( $query, $conex );
	$row = mysql_fetch_array($rs);
	if( $row[0]*1 > 0 ){
		echo " Esta historia ya tiene una solicitud pendiente, <br>comun&iacute;quese con registros m&eacute;dicos ";
		return;
	}

	$query = " INSERT INTO {$wbasedato}_000288 ( Medico,Fecha_data,Hora_data,Scdhis,Scding,Scdtda,Scddoa,Scdtdn,Scddon,Scdjus,Scdest,Seguridad)
				    VALUES ( '{$wbasedato}',
				    	     '{$fechaHoy}',
				    	     '{$horaHoy}',
				    	     '{$whistoria}',
				    	     '{$wingreso}',
				    	     '{$wtdocant}',
				    	     '{$wdocant}',
				    	     '{$wtdocnue}',
				    	     '{$wdocnuev}',
				    	     '{$justificacion}',
				    	     'on',
				    	     'C-{$user2}')";
	$rs = mysql_query( $query, $conex );
	$num = mysql_affected_rows();
	if( $num >= '0' ){
		echo " SOLICITUD REGISTRADA ";
	}else{
		echo " ERROR EN LA SOLICITUD, INT&Eacute;NTELO DE NUEVO MAS TARDE";
	}
	return;
}

?>
<html lang="es-ES">
<!DOCTYPE html>
<head>
<title>Admision de Pacientes</title>
<meta charset="utf-8">

<!--<link type="text/css" href="../../../include/root/jquery.autocomplete.css" rel="stylesheet" /> --><!-- Autocomplete -->
<link type="text/css" href="../../../include/root/ui.all.css" rel="stylesheet" /><!-- Nucleo jquery -->
<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" /> <!-- Tooltip -->
<link type="text/css" href="../../../include/root/jquery.autocomplete.css" rel="stylesheet" /> <!-- Autocomplete -->
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>

<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
<!--<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>-->

<script src="../../../include/root/toJson.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.easyAccordion.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.maskedinput.js" type="text/javascript"></script>

<script type='text/javascript' src='../../../include/root/jquery.autocomplete.js'></script>	<!-- Autocomplete -->

<!--<script src="./accidentesTransito.js" type="text/javascript"></script>-->
<script type="text/javascript" src="accidentesTransito.js?v=<?=md5_file('accidentesTransito.js');?>"></script>
<script src="./eventosCatastroficos.js" type="text/javascript"></script>
<script type="text/javascript" src="./funcionesAdmisiones.js?v=<?=md5_file('./funcionesAdmisiones.js');?>"></script>

<script src="../../../include/root/toJson.js"></script>
<script src="../../../include/root/jquery.validate.js"></script>
<script src='../../../include/root/jquery.quicksearch.js' type='text/javascript'></script>
<script type="text/javascript" src="../../ips/procesos/soportes.js?v=<?=md5_file('../../ips/procesos/soportes.js');?>"></script>

<!-- Estan todas las funciones de jquery, se separan del archivo principal para mejor comprensión del codigo y hacer un mejor debug -->
<script src="js_admisiones_erp.js"></script>

<style>
	.campoObligatorio{
		border-style:solid;
		border-color:red;
		border-width:1px;
	}

	.disabler {
		background-color: #bbb;
		opacity:0.75;
	}
</style>
<style type="text/css">

	#div_doc_change{
		padding-right: 15px;
		padding-left: 15px;
		padding-top: 15px;
		padding-bottom: 15px;
	}

	.td_datos{
		border-radius:      0.4em;
		width:              150px;
		padding:             5px;
		border-style:     solid;
        border-width:     2px;
	}

	.tabla_datos{
		border-style:     solid;
        border-width:     2px;
	}

	.textarea_justi {
		width: 600px;
		height: 120px;
		border: 3px solid #cccccc;
		padding: 5px;
		font-family: Tahoma, sans-serif;
		background-position: bottom right;
		background-repeat: no-repeat;
	}

	.amarilloSuave{
		background-color: #F7D358;
	}

    .efecto_boton
	{
		cursor:pointer;
		border-bottom: 1px solid orange;
		color:orange;
		font-weight:bold;
    }

	.class_div
	{
		overflow-x: scroll
		overflow-y: scroll
	}
	.j2
	{
		background-color:#00CCCC;
	}
	.campoRequerido
	{
            border: 1px orange solid;
            background-color:lightyellow;
			color:gray;
    }
	.sel_enviadas_color
	{
		background-color:#E8EEF7;
		font-size: 10pt;
	}

	.fila1 { border-right:2px #fff solid; }
	.fila2 { border-right:2px #fff solid;}

	.fila1espacio { border-right:2px #fff solid;border-bottom:2px #fff solid; font-size: smaller;  }
	.fila2espacio { border-right:2px #fff solid;border-bottom:2px #fff solid; font-size: smaller;}

	.anchotabla{
		width:90%;
	}
	select{
		width:100%;
	}

	input{
		width:100%;
		height:23px;
	}
	.bordes{
	border-radius: 15px;
	}

	.estadoInactivo
		{
			font-size: larger;
			color:red;
		}

	.estadoActivo
		{
			font-size: larger;
			color:green;
		}

	.errorMensajes
		{
			color:red;
			BACKGROUND-COLOR: lightyellow;
		}

	.mensajeValido
		{
			color:black;
		}

	.inputblank
		{
			color:gray;
		}

	.efecto_boton
		{
			cursor:pointer;
			border-bottom: 1px solid orange;
			color:orange;
			font-weight:bold;
		}

	.div_especiales {
		background:#FFFFCC;
		font-size:16px;
		height:40px;
		width:480px;
		margin:0 auto 0 auto;
		text-align: center;
		border: 1px orange solid;
		background-color:lightyellow;
		color:red;
		font-weight: bold;
		/*para Firefox*/
		-moz-border-radius: 15px 15px 15px 15px;
	/*para Safari y Chrome*/
	-webkit-border-radius: 15px 15px 15px 15px;
	/* para Opera */
	border-radius: 15px 15px 15px 15px;
	}
	.corchete {
		background-image: url("../../images/medical/root/corchete.png");
		background-position: right top;
		background-repeat: no-repeat;
		background-size: 23px 98%;
		margin: 0 auto;
	}
	.corchetei{
		background-image: url("../../images/medical/root/corchete.png");
		background-position: right top;
		background-repeat: no-repeat;
		background-size: 23px 98%;
		margin: 0 auto;
		-webkit-transform:scaleX(-1);
		-moz-transform:scaleX(-1);
		-ms-transform:scaleX(-1);
		-o-transform:scaleX(-1);
		transform:scaleX(-1);
	}

	.ac_results {
		max-height: 400px;
		overflow-y: auto;
		/* prevent horizontal scrollbar */
		overflow-x: hidden;
	}

	.boton_preanestesia{
		padding: 10px 24px;
		border-radius: 8px;
		font-weight:bold;
	}

	.boton_preanestesia:hover {
		background-color: #004A91; /* Green */
		color: white;
	}

</style>

</head>
<body>
<?php


/******************************************************************************
 * INICIO DEL PROGRAMA
 ******************************************************************************/

$conex = obtenerConexionBD("matrix");

$wactualiz="2021-12-19";

if( !isset($wemp_pmla) ){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}
define("CODIGO_COLOMBIA", consultarCodigoColombia() );
define("PEDIRTIPOSERVICIO", consultarAplicacion2($conex,$wemp_pmla,"pideTipoServicio") );
define("INCLUIR_ZONA_GEOGRAFICA", consultarAplicacion2($conex,$wemp_pmla,"incluirZonaAdmision") );
define("AUXFILTRO", consultarAplicacion2($conex,$wemp_pmla,"filtroEspecialidadClinica") );


$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "facturacion" );
$wbasedato1 = strtolower( $institucion->baseDeDatos );
$wentidad = $institucion->nombre;

$cco_usuario = "";
$cco_usuario_ayuda = "";
$where_lista_servicios = "";

include_once( "./accidentesTransito.php" );
include_once( "./eventosCatastroficos.php" );
// include_once("root/magenta.php");  //para saber si el paciente es afin

// if(!isset($_SESSION['user'])){
if(!array_key_exists('user', $_SESSION)){
    echo "<center><font size=5><br><br>La sesi&oacute;n ha caducado, por favor ingrese nuevamente a Matrix.</font></center>";
	exit;
}

//Para inhabilitar el programa si se ingresa desde internet explorer.
if (isset($_SERVER['HTTP_USER_AGENT'])) {
    $agent = $_SERVER['HTTP_USER_AGENT'];
	if (strlen(strstr($agent, 'MSIE')) > 0) {
		echo "<center><font size=5><br><br>El programa de admisiones no está habilitado para el navegador Internet Explorer.<br>
		<a target='_blank' href='http://www.mozilla.org/es-ES/firefox/new/'><img src='../../images/medical/root/boton5.png'></a>
		</font></center>";
		exit;
	}
}

//2014-03-18
$permisos = array();
$permisos = consultarPermisosUsuario( $user2[1] );

if( validarUsuario() == false && $permisos['graba'] != "on" && $permisos['consulta'] != "on" && $permisos['actualiza'] != "on" ){

	echo "<center><font size=5><br><br>El usuario no esta autorizado para utilizar el programa de admisiones,<br>porque no pertenece a un centro de costos de ingreso</font></center>";
	exit;
}

$tieneConexionUnix = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'conexionUnix' );
//$tieneConexionUnix = "on";
//$tieneConexionUnix = "off";
$ping_unix = ping_unix();
if( $hay_unix && $tieneConexionUnix == 'on' && $ping_unix == true )
{
	/*****************************************************************************
	 * Ejecutando crones
	 *
	 * Tablas que se mueven con estos crones
	 *
	 * maestroTarifas						000025
	 * maestroEmpresa						000024
	 * maestroEventosCatastroficos			000155
	 * maestroTiposVehiculos				000162
	 *****************************************************************************/
	//Este archivo contiene los crones que se pueden ejecutar
	include_once("root/kron_maestro_unix.php");

	//Ejecuto los crones correspondientes
	// $cron = Array( 'maestroTarifas', 'maestroEmpresa', 'maestroEventosCatastroficos', 'maestroDepartamentos', 'maestroMunicipios', 'actualizarDatosCie10', 'pacientesInactivosAMatrix', 'kronBarrios' );
	$cron = Array( 'maestroTarifas', 'maestroEventosCatastroficos', 'maestroDepartamentos', 'maestroMunicipios', 'actualizarDatosCie10', 'pacientesInactivosAMatrix', 'kronBarrios' );
	$cron = Array( 'kron_admisionesMatrix_Unix');
	$ejCron = new datosDeUnix();

	if( isset($debugfrederick) ){
		$ejCron->insercionTarifasHomologacionDeFacturacion("P");
	}

	foreach( $cron as $key => $value ){
		$ejCron->$value();
	}
	/*****************************************************************************/
}else{
	//echo "no unix";
}

@session_start();
//el usuario se encuentra registrado
// if(!isset($_SESSION['user']))
if(!array_key_exists("user", $_SESSION))
    echo "error";
else
{
$fechaAct=date("Y-m-d");
$horaAct=date("H:i:s");
if ($wemp_pmla == 01)
{
	encabezado("ADMISI&Oacute;N DE PACIENTES ",$wactualiz, $wbasedato1);
}
else 
{
	encabezado("ADMISI&Oacute;N DE PACIENTES ",$wactualiz, "logo_".$wbasedato1);
}

$soportesautomaticos =  consultarAliasPorAplicacion( $conex, $wemp_pmla, 'soportesautomaticos' );
$TableroDigitalizacionUrgencias = consultarAliasPorAplicacion($conex,$wemp_pmla,"TableroDigitalizacionUrgencias");

$consulta           = (!isset($consulta)) ? '' : $consulta;
$TipoDocumentoPacAm = (!isset($TipoDocumentoPacAm)) ? '' : $TipoDocumentoPacAm;
$DocumentoPacAm     = (!isset($DocumentoPacAm)) ? '' : $DocumentoPacAm;
$TurnoEnAm          = (!isset($TurnoEnAm)) ? '' : $TurnoEnAm;
$AgendaMedica       = (!isset($AgendaMedica)) ? '' : $AgendaMedica;
$solucionCitas      = (!isset($solucionCitas)) ? '' : $solucionCitas;

$search_historia      = (!isset($search_historia)) ? '' : $search_historia;
$search_ingreso      = (!isset($search_ingreso)) ? '' : $search_ingreso;



echo "<input type='hidden' name='wbasedato' id='wbasedato' value='".$wbasedato."'>";
echo "<input type='hidden' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";
echo "<input type='hidden' name='wemp_pmla' id='tema' value='".$tema."'>";
echo "<input type='hidden' name='soportesautomaticos' id='soportesautomaticos' value='".$soportesautomaticos."'>";
echo "<input type='hidden' name='parametroDigitalizacion' id='parametroDigitalizacion' value='".$TableroDigitalizacionUrgencias."'>";
echo "<input type='hidden' name='fechaAct'  id='fechaAct'  value='".$fechaAct."'>";
echo "<input type='hidden' name='horaAct'   id='horaAct'   value='".$horaAct."'>";
echo "<input type='hidden' name='key' id='key' value='".$key."'>";
echo "<input type='hidden' name='cco_usuario' id='cco_usuario' value='".$cco_usuario."'>";
echo "<input type='hidden' name='cco_usuario_ayuda' id='cco_usuario_ayuda' value='".$cco_usuario_ayuda."'>";
echo "<input type='hidden' name='cod_colombia' id='cod_colombia' value='".CODIGO_COLOMBIA."'>";
echo "<input type='hidden' name='perfil_consulta' id='perfil_consulta' value='".$consulta."'>";
echo "<input type='hidden' id='mostrarTipoServicio'                    value='".PEDIRTIPOSERVICIO."' >";
echo "<input type='hidden' id='filtraEspecialidadClinica'              value='".AUXFILTRO."' >";
echo "<input type='hidden' id='TipoDocumentoPacAm'                     value='{$TipoDocumentoPacAm}' >";
echo "<input type='hidden' id='DocumentoPacAm'                         value='{$DocumentoPacAm}' >";
echo "<input type='hidden' id='TurnoEnAm'                              value='{$TurnoEnAm}' >";//2016-02-26
echo "<input type='hidden' id='AgendaMedica'                           value='{$AgendaMedica}' >";
echo "<input type='hidden' id='solucionCitas'                          value='{$solucionCitas}' >";

echo "<input type='hidden' id='search_historia'                        value='{$search_historia}' >";
echo "<input type='hidden' id='search_ingreso'                         value='{$search_ingreso}' >";

$datosNoAplica      = buscarDatosNoAplicaExtranjeros();
echo "<input type='hidden' name='dep_no_aplica' id='dep_no_aplica' value='".$datosNoAplica['departamento']."'>";
echo "<input type='hidden' name='mun_no_aplica' id='mun_no_aplica' value='".$datosNoAplica['municipio']."'>";
echo "<input type='hidden' name='bar_no_aplica' id='bar_no_aplica' value='".$datosNoAplica['barrio']."'>";


echo"<FORM METHOD='POST' ACTION='' id='forAdmisiones'>";

echo "<input type='hidden' id='cargoDatosConsulta' value='off'>";
echo "<div id='div_admisiones'>";
echo "<input type='hidden' value='.' ux='_ux_infcon'>";	//Campo vacio para unix

/**************************************************************************************************
 * Agosto 13 de 2013
 * Navegación superior al consultar
 **************************************************************************************************/
echo "<div id='bot_navegacion1' style='display:none'>";
echo "<center><table style='width:500;' border='0'>";
echo "<th colspan='3' class='encabezadotabla'>Resultados de la busqueda</th>";
echo "<tr class='fila1'>";
echo "<td align='center' colspan='3'>Registro <span id='spRegAct1'></span> de <span id='spTotalReg1'></span>&nbsp;&nbsp;</td>";
echo "</tr>";
/*echo "<tr class='fila1'>";
echo "<td align='center' colspan='3'>Total Resultados:<span id='spTotalReg1'></span>&nbsp;&nbsp;</td>";
echo "</tr>";*/
/*echo "<tr>";
echo "<td align='center' colspan='3' class='fila1'>Resultado:<span id='spRegAct1'></span>&nbsp;con historia: &nbsp;<span id='spHisAct1'></span>&nbsp;Ingreso:&nbsp;<span id='spIngAct1'></span>&nbsp;de&nbsp;<span id='spTotalIng11'></span></td>";
echo "</tr>";*/
echo "<tr id='tr_navegaResultados'>";
echo "<td align='center' colspan='3'><img src='../../images/medical/citas/atras.jpg' height='30' width='30' border=0 onClick=\"navegacionIngresos(-1);\"/>";
echo "&nbsp;<img src='../../images/medical/citas/adelante.jpg' height='30' width='30' border=0 onClick=\"navegacionIngresos(+1);\"/></td>";
echo "</tr>";
echo "</table></center>";
echo "<br><br>";
echo "</div>";//div botones navegacion
/**************************************************************************************************/

/*********************************************************************************************************
 * Agsoto 17 de 2013
 *
 * Div que contiene la información de los pacientes que tienen preadmision
 *********************************************************************************************************/


echo "<div id='div_ext_agendaPreadmision'>";
echo "<h3>AGENDA PREADMISI&Oacute;N</h3>";
echo "<div id='dvAgendaPreadmision' style='display:none'>";

//----------------------------------------------------------------------
// --> Inicio pintar lista de turnos, jerson trujillo
//----------------------------------------------------------------------
// --> Validar que el cco del usuario pueda ver la lista de pacientes con turno, Jerson trujillo.
$verListaTurnos           = FALSE;
$wbasedatoMov             = consultarAplicacion2($conex,$wemp_pmla,"movhos");
$usuario                  = explode("-", $user);
$usuario                  = $usuario[1];
$usuariosExentos          = consultarAplicacion2($conex, $wemp_pmla, "usersexentosadmisiones");
$usuariosExentos          = explode( ",", $usuariosExentos );
$filtrarCcoAyuda          = "off";
$imprimirHistoria         = consultarAplicacion2($conex,$wemp_pmla,"imprimirHistoria");
$responsable101particular = consultarAplicacion2($conex,$wemp_pmla,"responsable101particular");
$imprimirSticker          = consultarAplicacion2($conex,$wemp_pmla,"imprimirStickerAdmision");
$codCausaAccTrans         = consultarAplicacion2($conex,$wemp_pmla,"codCausaAccTransito");

// --> Si el usuario esta entre los exentos
if(in_array($usuario, $usuariosExentos))
	$verListaTurnos = TRUE;
else
{
	// --> Si el cco del usuario es de urgencias
	$sqlCcoUrg = "SELECT Ccocod
					FROM ".$wbasedatoMov."_000011
				   WHERE Ccocod = '".$cco_usuario."'
				     AND Ccourg = 'on'
	";
	$resCcoUrg = mysql_query($sqlCcoUrg, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCcoUrg):</b><br>".mysql_error());
	if(mysql_fetch_array($resCcoUrg))
		$verListaTurnos = TRUE;

	// --> si el cco es de ayuda Diagnóstica
	$sqlCcoAyu =  "SELECT Ccocod
					FROM {$wbasedatoMov}_000011
				   WHERE Ccocod = '{$cco_usuario}'
				     AND Ccoayu = 'on'";
	$resCcoAyu = mysql_query($sqlCcoAyu, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCcoUrg):</b><br>".mysql_error());
	if(mysql_fetch_array($resCcoAyu)){
		$filtrarCcoAyuda = "on";
	}
}

echo "<input type='hidden' id='permitirVerListaTurnos' value='".$verListaTurnos."'>";
echo "<input type='hidden' id='filtrarCcoAyuda' value='".$filtrarCcoAyuda."'>";
echo "<input type='hidden' id='imprimirSticker' value='".$imprimirSticker."'>";
echo "<input type='hidden' id='codCausaAccTrans' value='".$codCausaAccTrans."'>";
echo "<input type='hidden' id='codCausaInicial' value=''>";
echo "<input type='hidden' id='accion_consultar' value=''>";

if($verListaTurnos)
{
	echo "
			<div id='divListaPacientesConTurno' align='center'>";
				listarPacientesConTurno();
	echo "	</div>";
}
//-----------------------------------
// --> Fin pintar lista de turnos
//-----------------------------------
													//2017-06-12
$verPreadmision = ( ( $filtrarCcoAyuda == "on") or ( $verListaTurnos ) ) ? " style='display:none' " : "";

echo "<div id='dvAgendaPreamdisionDatos' $verPreadmision></div>";
echo "<p align='right' style='font-size:10pt;'><a onClick='ocultarMostrarPacientesIngresados(0);'>[+] Pacientes Admitidos</a></p>";
echo "<div id='dvAgendaAmdisionDatos'></div>";
echo "</div>";
echo "</div>";



/**************************************************************************************************
 * Agosto 15 de 2013
 * Radios para elegir opción de admisión o no
 **************************************************************************************************/

echo "<div id='div_datosAdmiPreadmi'>";
 //INGRESO - DATOS PERSONALES - DATOS ACOMPAÑANTE
echo "<input type='hidden' id='wfiniAdm' value=''>";
echo "<input type='hidden' id='whiniAdm' value=''>";
//BOTONES
echo "<div name='div_botones'>";
echo "<center><table class='fondoamarillo' style='width:100%'>";
echo "<tr>";
// echo "<td class='' align='center' ><input type='button' value='Iniciar' style='width:100;height:25' onClick=\"resetear();\">";
echo "<td class='' align='center' >";
 echo "<div style='overflow: hidden;'><div style='width: 70%; float: left;' align='right'>";
    echo "<input type='button' value='Iniciar' style='width:100;height:25' onClick=\"iniciar();\">";
	echo "<input type='button' value='Consultar' style='width:100;height:25' onClick=\"mostrarDatos()\">";

	  $permisos = consultarPermisosUsuario( $user2[1] );
	( $permisos['consulta'] == "on" ) ? $disabledActualizar = "disabled" : $disabledActualizar = "";

	echo "<input type='button' name='btRegistrarActualizar' value='Registrar/Admitir' consulta='{$permisos['consulta']}' actualiza='{$permisos['actualiza']}' graba='{$permisos['graba']}' style='width:120;height:25' onClick=\"enviarDatos();\" $disabledActualizar>";
	// echo "<input type='button' value='Cerrar' style='width:100;height:25'  onclick='javascript: cerrarVentana();'>";
	echo "<input type='button' value='Regresar agenda' style='width:150;height:25'  onclick='javascript: mostrarPreadmision();'></div><div align='right' id='div_des_triage' style='cursor:cursor; width:200px; float:left; padding:8px; display:none;' title=' aca se mustra la descripcion bn bonita'><span class='subtituloPagina2'> Descripcion Triage </span></div></div></td>";
// echo "<input type='button' value='Log' style='width:100;height:25'  onclick='llenarDatosLog();'></td>";
echo "</tr>";
echo "</table></center>";
echo "</div>";
echo "<div id='div_datosIng_Per_Aco'>";
echo "<h3>DATOS INGRESO - DATOS PERSONALES </h3>";

echo "<div id='div_int_datos_ing_per_aco'>";
//datos de ingreso
echo "<div id='datos_ingreso'>";

echo "<center><table class='anchotabla'>";
// echo "<tr style='background-color:#C3D9FF'>";
echo "<tr style='background-color:#E8EEF7'>";

//Se agrega radio button de admisión y preadmisión
echo "<td align='center' style='display:none'>";
echo "<INPUT type='radio' id='radAdmision' name='radPreAdmi' value='admision' onClick='opcionAdmisionPreadmision();' checked style='width:14'>";
echo "</td>";
echo "<td  style='display:none'>Admisión</td>";

echo "<td align='center'  style='display:none'>";
echo "<INPUT type='radio' id='radPreadmision' name='radPreAdmi' value='preadmision' onClick='opcionAdmisionPreadmision();' style='width:14'>";
echo "</td>";
echo "<td  style='display:none'>Preadmisión</td>";

echo "<td id='txtAdmisionPreadmision' style='background-color:lightyellow;color:black;font-size:18pt;' align='center'><b></b></td>";

echo "<td style='width:200' padm>Fecha Posible de Ingreso</td>";
echo "<td align='center'  style='width:100' padm>";
echo "<INPUT type='text' id='pac_fectxtFechaPosibleIngreso' style='width:105' msgerror='YYYY-MM-DD' fecha>";
echo "</td>";
/**************************************************************************************************/

echo "<div id='div_mensaje_PerEspeciales' style='display:none' align='center' class='div_especiales'>
</div>";
echo "<br>";
echo "
	<td align='center'>
		<b>Estado Paciente:  </b><span id='spEstPac' ></span>&nbsp;&nbsp;&nbsp;&nbsp;
		<b>Turno:  </b><span id='numTurnoPaciente' style='color:red;font-size:20px' valor=''>SIN TURNO!!!</span>
	</td></tr>"; //pasa a verde cuando la persona esta activa


// /*********************************************************************************************************
 // * Agsoto 17 de 2013
 // *
 // * Div que contiene la información de los pacientes que tienen preadmision
 // *********************************************************************************************************/
// echo "<div id='dvAgendaPreadmision' style='display:none'>";
// // $d = agendaAdmisiones( date( "Y-m-d" ) );
// // echo $d[ 'html' ];
// echo "</div>";


echo "</table></center>";
echo "<center><table>";
echo "<th class='encabezadotabla' colspan='8'>Datos de Ingreso</th>";
echo "<tr>";
echo "<td class='fila1' style='width:12%'>Servicio de ingreso</td>";
echo "<td class='fila1' style='width:22%'>Lugar de Atenci&oacute;n</td>";
echo "<td class='fila1' style='width:8%'>Historia</td>";
echo "<td class='fila1' style='width:4%'>Ingreso</td>";
echo "<td class='fila1' style='width:12%'>Fecha de Ingreso</td>";
echo "<td class='fila1' style='width:12%'>Hora de Ingreso</td>";
echo "<td class='fila1' style='width:16%'>Clasificaci&oacute;n Usuario</td>";

//Agosto 22 de 2013
//$pedirTipoServicio = consultarAplicacion2($conex,$wemp_pmla,"pideTipoServicio");
//$auxFiltro         = consultarAplicacion2($conex,$wemp_pmla,"filtroEspecialidadClinica");
$pedirTipoServicio = ( PEDIRTIPOSERVICIO == "on" ) ? "" : " style='display:none' ";
echo "<td class='fila1' colspan='1' id='td_tipoServicioTitulo' {$pedirTipoServicio} >Tipo de Servicio</td>";

echo "</tr>";
echo "<tr>";
$alias="movhos";
$aplicacion=consultarAplicacion2($conex,$wemp_pmla,$alias);
if ($aplicacion == "")
{

	$param="class='reset' msgcampo='Tipo de servicio' msgError ux='_ux_pachos_ux_pacser_ux_infate_ux_murser'";
	echo "<td class='fila2'>";$res1=consultaMaestros('000105','Selcod,Seldes',$where="Seltip = '20' and Selest='on'",'','','2');
	crearSelectHTMLAcc($res1,'ing_seisel_serv_ing','ing_seisel_serv_ing',$param);
	echo "</td>";
}
else
{

	// $param="class='reset' msgError ux='_ux_pachos_ux_pacser_ux_infate_ux_murser' onchange='validacionServicioIngreso();'";
	// echo "<td class='fila2'>";$res1=consultarCC($alias,$where="Ccoing = 'on' and Ccoayu != 'on' and ccoest = 'on'");
	$where="( ccoing='on' or (ccoayu='on' and ccohos!='on') or ccourg='on' and ccocir='on' ) and ccoest='on'";

	if( $where_lista_servicios != "" )
		$where = $where_lista_servicios.= " AND ccoest = 'on'";

	$userCcos = explode("-",$user);
	( isset($userCcos[1]) )? $userCcos = $userCcos[1] : $userCcos = $userCcos[0];
	echo "<td class='fila'>";$res1=consultarCC($aplicacion, $where, $userCcos);
	echo "<SELECT id='ing_seisel_serv_ing' msgcampo='Tipo de servicio' name='ing_seisel_serv_ing' msgError ux='_ux_pachos_ux_pacser_ux_infate_ux_murser'>";
    // echo "<option value=''>Seleccione...</option>";

       $num = mysql_num_rows( $res1 );

	   $html_options = "<option value=''>Seleccione...</option>";
	   $first_option = "";
       if( $num > 0 )
	   {
               while( $rows = mysql_fetch_assoc( $res1, MYSQL_ASSOC ) )
			   {
                       $value = "";
                       $des = "";
                       $i = 0;
                       foreach( $rows  as $key => $val ){
                               if( $i == 0 ){
                                    $value = $val;
                               }
                               else if( $i == 1 ){
                                    $des = $val;
                               }else{
									$j = $rows[ 'Ccosei' ];
							   }
                               $i++;
                       }
                       $selected = "";
					   if( $cco_usuario == $value )
							$first_option="<option value='".$j."-".$value."' exigirDiagnosticoInicial='".$rows['Ccomdi']."' selected='selected'>".$des."</option>";
					   else
							$html_options.="<option value='".$j."-".$value."' exigirDiagnosticoInicial='".$rows['Ccomdi']."'>".$des."</option>";
               }
       }

	   echo $first_option;
	   echo $html_options;

       echo "</SELECT>";

	   echo "</td>";

}



$param="class='reset' msgError msgcampo='Lugar de atencion'";
echo "<td class='fila2'>";$res1=consultaMaestros('000105','Selcod,Seldes',$where="Seltip = '21' and Selest='on'",'','Seldes','2'); //msgError='Numero de Historia' msgError='Numero de Ingreso'
crearSelectHTMLAcc($res1,'ing_lugselLugAte','ing_lugselLugAte',$param);
echo "</td>";
echo "<td class='fila2'><input type='text' name='ing_histxtNumHis' id='ing_histxtNumHis' class='reset'  ux='_ux_pachis' onblur=''></td>";
echo "<td class='fila2'><input type='text' name='ing_nintxtNumIng' id='ing_nintxtNumIng' class='reset'  ux='_ux_pacnum' onblur=''></td>";
echo "<td class='fila2'><input type='text' name='ing_feitxtFecIng' id='ing_feitxtFecIng' fecha value='".$fechaAct."' class='reset'></td>";
echo "<td class='fila2'><input type='text' name='ing_hintxtHorIng' id='ing_hintxtHorIng' hora value='".$horaAct."' class='reset'></td>";

$consultarDefecto = consultarAplicacion2($conex,$wemp_pmla,"tipoUsuarioDefecto");
$param="class='reset'";
echo "<td class='fila2' colspan='1'>";$res1=consultaMaestros('root_000099','Clacod,Clades',$where="Claest='on'",'','Clades' );
crearSelectHTMLAcc($res1,'ing_claselClausu','ing_claselClausu',$param, $consultarDefecto);
echo "</td>";

$param="class='reset' {$pedirTipoServicio}";
echo "<td class='fila2espacio' colspan='1' id='td_tipoServicioSelect' {$pedirTipoServicio}>";$res1=consultaMaestros('000105','Selcod,Seldes',$where="Seltip = '14' and Selest='on'",'','Seldes','2');
crearSelectHTMLAcc($res1,'pac_tamselClausu','pac_tamselClausu',$param);
echo "</td>";

echo "</tr>";

echo "</table></center>";
echo "</div>"; //datos ingreso
//datos personales paciente
echo "<div id='div_int_datos_personales'>";
echo "<center><table>";
echo "<th class='encabezadotabla' colspan='8'>Datos Personales Paciente</th>";
echo "<tr>";
echo "<td class='fila1' colspan='1' style='width:8%'>Tipo de Documento</td>";
echo "<td class='fila1' style='width:16%'>N&uacute;mero Documento</td>";
echo"<td class='fila1' colspan='1' style='width:8%'>Fecha de Expedici&oacute;n</td>";
echo "<td class='fila1' style='width:16%'>Primer Apellido</td>";
echo "<td class='fila1' style='width:16%'>Segundo Apellido</td>";
echo "<td class='fila1' style='width:16%'>Primer Nombre</td>";
echo "<td class='fila1' colspan='2' style='width:16%'>Segundo Nombre</td>";
echo "</tr>";
echo "<tr>";
$param="class='reset' msgError msgcampo='Tipo de documento' ux='_ux_pactid_ux_midtii' onChange='cambiarTipoDocumento( this );'";
echo "<td class='fila1espacio' colspan='1'>";$res1=consultaMaestros('root_000007','Codigo,Descripcion,alfanumerico, docigualhis',$where="Estado='on'",'','');
crearSelectHTMLAcc($res1,'pac_tdoselTipoDoc','pac_tdoselTipoDoc',$param);

echo "</td>";
echo "<td class='fila1espacio'><input type='text' msgcampo='Documento' name='pac_doctxtNumDoc' id='pac_doctxtNumDoc' class='reset' msgError='Digite el numero de documento' ux='_ux_pacced_ux_midide' onblur='verificarDocumento();verificarTriageUrgencias();'></td>";
echo "<td class='fila1espacio'><input type='text' name='pac_fedtxtFecExpDoc' id='pac_fedtxtFecExpDoc' class='reset' fecha  placeholder='Seleccione fecha de expedicion' ></td>";
echo "<td class='fila1espacio'><input type='text' msgcampo='Primer Apellido' name='pac_ap1txtPriApe' id='pac_ap1txtPriApe' class='reset' msgError='Digite el primer apellido' alfabetico ux='_ux_pacap1_ux_midap1' onblur=''></td>";
echo "<td class='fila1espacio'><input type='text' name='pac_ap2txtSegApe' id='pac_ap2txtSegApe' class='reset' placeholder='Digite el segundo apellido' alfabetico ux='_ux_pacap2_ux_midap2' onblur=''></td>";
echo "<td class='fila1espacio'><input type='text' msgcampo='Primer nombre' name='pac_no1txtPriNom' id='pac_no1txtPriNom' class='reset' msgError='Digite el primer nombre' alfabetico ux='_ux_pnom1_ux_midno1' onblur=''></td>";
echo "<td class='fila1espacio' colspan='2'><input type='text' name='pac_no2txtSegNom' id='pac_no2txtSegNom' class='reset' placeholder='Digite el segundo nombre' alfabetico ux='_ux_pnom2_ux_midno2' onblur=''></td>";
echo "</tr>";
echo "<tr>";
echo "<td class='fila2' width='150'>Fecha de Nacimiento</td>";
echo "<td class='fila2' width='40'>Edad</td>";
echo "<td class='fila2'>Pais de Nacimiento</td></td>";
echo "<td class='fila2'>Departamento de Nacimiento</td>";
echo "<td class='fila2'>Municipio de Nacimiento</td>";
echo "<td class='fila2'>Sexo</td></td>";
echo "<td class='fila2' colspan='2'>Estado Civil</td></td>";
echo "</tr>";
echo "<tr>";
echo "<td class='fila2espacio' ><input type='text' name='pac_fnatxtFecNac' id='pac_fnatxtFecNac' fecha onChange='calcular_edad(this.value, this )' ux='_ux_pacnac_ux_midnac' value='".$fechaAct."'></td>";
echo "<td class='fila2espacio' ><input type='text' name='txtEdad' id='txtEdad' class='reset'></td>";
echo "<td class='fila2espacio'>";
echo "<input type='text' msgcampo='Pais de nacimiento' name='pac_pantxtPaiNac' id='pac_pantxtPaiNac' class='reset' ux='_ux_infnac' msgError='Digite el Pais'>";
echo "<input type='hidden' name='pac_panhidPaiNac' id='pac_panhidPaiNac' ux='_ux_plug_pais'>";
echo "</td>";
echo "<td class='fila2espacio'>";
echo "<input type='text' msgcampo='Departamento de nacimiento' name='pac_deptxtDepNac' id='pac_deptxtDepNac' class='reset' srcPai='pac_panhidPaiNac' msgError='Digite el Departamento'>";
echo "<input type='hidden' name='pac_dephidDepNac' id='pac_dephidDepNac' ux='_ux_plug'>";
echo "</td>";
echo "<td class='fila2espacio'>";
echo "<input type='text' msgcampo='Ciudad de nacimiento' name='pac_ciutxtMunNac' id='pac_ciutxtMunNac' class='reset' srcDep='pac_dephidDepNac' msgError='Digite el Municipio'>";
echo "<input type='hidden' name='pac_ciuhidMunNac' id='pac_ciuhidMunNac' ux='_ux_paclug'>";
echo "</td>";

echo "<td class='fila2espacio'>";
echo "<table width='100%' border='0'>";
echo "<tr><td width='50%' align='center' style='font-size: 11px;' nowrap>Femenino &nbsp; <input msgcampo='Sexo' type='radio' style='width:14px;height:12px;' name='pac_sexradSex' id='pac_sexradSexf' value='F' onclick='' ux='_ux_pacsex_ux_midsex'>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Masculino &nbsp; <input msgcampo='Sexo' type='radio' style='width:14px;height:12px;' name='pac_sexradSex' id='pac_sexradSexm' value='M' onclick='' ux='_ux_pacsex_ux_midsex'></td></tr>";
echo "</table>";
echo "</td>";

$param="class='reset' ux='_ux_pacest' msgcampo='Estado civil'";
echo "<td class='fila2espacio' colspan='2'>";$res1=consultaMaestros('000105','Selcod,Seldes',$where="Seltip = '25' and Selest='on'",'','','2');
crearSelectHTMLAcc($res1,'pac_estselEstCiv','pac_estselEstCiv',$param);

echo "</td>";
echo "</tr>";
echo "<tr>";
echo "<td class='fila1' colspan='2'>Pertenencia Etnica</td></td>";
echo "<td class='fila1'>Pais de Residencia</td></td>";
//-<nuevo>
echo "<td class='fila1'> Departamento de Residencia</td>";
echo "<td class='fila1'>Municipio de Residencia</td>";
echo "<td class='fila1'>Barrio de Residencia</td></td>";
echo "<td class='fila1'>Zona</td></td>";
if( INCLUIR_ZONA_GEOGRAFICA == 'on' ){
	echo "<td class='fila1'>Zona Geo.</td></td>";
}
echo "</tr>";
echo "<tr>";
$param="class='reset' ux='_ux_msegpo' msgcampo='Pertenencia Etnica'";
echo "<td class='fila1espacio' colspan='2'>";$res1=consultaMaestros('root_000098','Petcod,Petdes',$where="Petest = 'on'",'','Petcod');
crearSelectHTMLAcc($res1,'pac_petselPerEtn','pac_petselPerEtn',$param);
echo "</td>";
echo "<td class='fila1espacio'>";
echo "<input msgcampo='Pais de Residencia' type='text' name='pac_pahtxtPaiRes' id='pac_pahtxtPaiRes' msgError='Digite el Pais'>";
echo "<input type='hidden' name='pac_pahhidPaiRes' id='pac_pahhidPaiRes' ux='_ux_msepai'>";
echo "</td>";
//-<nuevo>
echo "<td class='fila2espacio'>";
echo "<input msgcampo='Departamento de Residencia' type='text' name='pac_dehtxtDepRes' id='pac_dehtxtDepRes' class='reset' srcPai='pac_pahhidPaiRes' msgError='Digite el Departamento'>";
echo "<input type='hidden' name='pac_dehhidDepRes' id='pac_dehhidDepRes'>";
echo "</td>";
echo "<td class='fila2espacio'>";
echo "<input msgcampo='Municipio de Residencia' type='text' name='pac_muhtxtMunRes' id='pac_muhtxtMunRes' class='reset' srcDep='pac_dehhidDepRes' msgError='Digite el Municipio'>";
echo "<input type='hidden' name='pac_muhhidMunRes' id='pac_muhhidMunRes' ux='_ux_pacmun_ux_midmun'>";
echo "</td>";
echo "<td class='fila2espacio'>";
echo "<input type='text' msgcampo='Barrio de Residencia' name='pac_bartxtBarRes' id='pac_bartxtBarRes' class='reset' srcMun='pac_muhhidMunRes' msgError='Digite el Barrio'>";
echo "<input type='hidden' name='pac_barhidBarRes' id='pac_barhidBarRes' ux='_ux_midbar'>";
echo "</td>";
$param="class='reset' ux='_ux_paczon_ux_midzon' msgcampo='Zona de Residencia'";
echo "<td class='fila1espacio'>";$res1=consultaMaestros('000105','Selcod,Seldes',$where="Seltip = '05' and Selest='on'",'','','2');
crearSelectHTMLAcc($res1,'pac_zonselZonRes','pac_zonselZonRes',$param);
echo "</td>";
$param="class='reset' msgcampo='Zona geografica'";
if( INCLUIR_ZONA_GEOGRAFICA == 'on' ){
	echo "<td class='fila1espacio'>";$res1=consultaMaestros('000256','Zogcod,Zogdes',$where="Zogest = 'on'",'','','2');
	crearSelectHTMLAcc($res1,'pac_zogselZongeo','pac_zogselZongeo',$param);
	echo "</td>";
}
echo "</tr>";
echo "<tr>";
echo "<td class='fila2' colspan='3'>Direcci&oacute;n de Residencia</td>";
echo "<td class='fila2'>Detalle de la Direcci&oacute;n</td>";
echo "<td class='fila2' colspan='1'>Tel&eacute;fono Fijo</td>";
echo "<td class='fila2'>Tel&eacute;fono M&oacute;vil</td>";
//-<nuevo>
echo "<td class='fila2' colspan='2'>Correo Electr&oacute;nico</td>";
echo "</tr>";
echo "<tr>";
echo "<td class='fila1espacio' colspan='3'><input type='text' msgcampo='Direccion de Residencia' name='pac_dirtxtDirRes' id='pac_dirtxtDirRes' class='reset' ux='_ux_pacdir_ux_middir' msgError='Digite la Direccion' depend='pac_dedtxtDetDir'></td>";
echo "<td class='fila1espacio'><input type='text' msgcampo='Detalle direccion de Residencia' name='pac_dedtxtDetDir' id='pac_dedtxtDetDir' class='reset' msgError='Digite detalle de la Direccion' depend='pac_dirtxtDirRes'></td>";
echo "<td class='fila2espacio' colspan='1'><input msgcampo='Telefono fijo' type='text' name='pac_teltxtTelFij' id='pac_teltxtTelFij' class='reset' ux='_ux_pactel_ux_midtel' msgError='Digite el telefono' depend='pac_movtxtTelMov' numerico></td>";
echo "<td class='fila2espacio'><input msgcampo='Telefono movil' type='text' name='pac_movtxtTelMov' id='pac_movtxtTelMov' class='reset' msgError='Digite el movil' depend='pac_teltxtTelFij' numerico></td>";
echo "<td class='fila1espacio' colspan='2'><input type='text' name='pac_cortxtCorEle' id='pac_cortxtCorEle' class='reset' ux='_ux_infmai'></td>";
echo "</tr>";
echo "<tr>";
//-<nuevo>echo "<td class='fila1' colspan='2'>Correo Electr&oacute;nico</td>";
echo "<td class='fila1' colspan='2'>Nivel Educativo</td></td>";
echo "<td class='fila1'>Ocupaci&oacute;n</td>";
echo "<td class='fila1' colspan='2'>Empresa donde Labora</td>";
echo "<td class='fila1'>Tel&eacute;fono Trabajo</td>";
echo "<td class='fila1' colspan='2'>Extensi&oacute;n</td>";
//echo "<td class='fila1'>Tipo Residencia Permanente</td>";
echo "</tr>";
echo "<tr>";
//-<>nuevoecho "<td class='fila1espacio' colspan='2'><input type='text' name='pac_cortxtCorEle' id='pac_cortxtCorEle' class='reset' ux='_ux_infmai'></td>";
$param="class='reset' msgcampo='Nivel educativo'";
echo "<td class='fila1espacio' colspan='2'>";$res1=consultaMaestros('root_000066','Scocod,Scodes',$where="Scoest='on' and Scoley='off'",'','');
crearSelectHTMLAcc($res1,'pac_nedselNivEdu','pac_nedselNivEdu',$param);
echo "</td>";
echo "<td class='fila1espacio'>";
echo "<input msgcampo='Ocupacion' type='text' name='pac_ofitxtocu' id='pac_ofitxtocu' class='reset' ux='_ux_infocu' msgError='Digite la ocupacion'>";
echo "<input type='hidden' name='pac_ofihidOcu' id='pac_ofihidOcu' ux='_ux_infcoc_ux_mseocu'>";
echo "</td>";
echo "<td class='fila1espacio' colspan='2'><input type='text' name='pac_emptxtEmpLab' id='pac_emptxtEmpLab' class='reset' ux='_ux_pactra' placeholder='Digite la Empresa'></td>";
echo "<td class='fila1espacio'><input type='text' name='pac_temtxtTelTra' id='pac_temtxtTelTra' class='reset' ux='_ux_inftel' placeholder='Digite el Telefono' numerico></td>";
echo "<td class='fila1espacio' colspan='2'><input type='text' name='pac_eemtxtExt' id='pac_eemtxtExt' class='reset' numerico></td>";
//-<nuevo>
$param="class='reset' msgcampo='Tipo de residencia' onChange='CambiarEstadoDatosExtranjeros( this );'";
/*echo "<td class='fila2' colspan='2'>";
$res1=consultaMaestros('000105','Selcod,Seldes',$where="Seltip = '15' and Selest='on'",'','','2');
crearSelectHTMLAcc($res1,'pac_trhselTipRes','pac_trhselTipRes',$param);
echo "</td>";*/
echo "</tr>";

echo "<tr style='display:none;' class='tr_pacienteExtranjero'>";
//echo "<td class='fila2' colspan='2'>Pais Extranjero</td>";
echo "<td class='fila2' colspan='5'>Direcci&oacute;n Local</td>";
echo "<td class='fila2' colspan='3'>Telefono Local:</td>";
//echo "<td class='fila2' colspan='2'>&nbsp;</td>";
echo "</tr>";

echo "<tr style='display:none;' class='tr_pacienteExtranjero fila2'>";
/*echo "<td colspan='2'><input msgcampo='Pais de Residencia Permanente' type='text' name='pac_prpntxtPaiResPer' id='pac_prpntxtPaiResPer' class='reset' msgcampo='Digite el Pais'>
	  <input type='hidden' name='pac_prpidPaiRes' id='pac_prpidPaiRes' value='".CODIGO_COLOMBIA."'>
	  </td>";*/
echo "<td colspan='5'><input msgcampo='Digite la Direccion' type='text' name='pac_dle' id='pac_dle' class='reset' msgError='Digite la Direccion'></td>";
echo "<td colspan='1'><input msgcampo='Digite El numero Telefonico' type='text' name='pac_tle' id='pac_tle'></td>";
echo "<td colspan='2' align='center'><a href='http://apps.migracioncolombia.gov.co/sire/public/login.jsf'  target='_blank'><img border=0 src='../../images/medical/root/migracioncolombia.png'/></a></td>";
//echo "<td class='fila2' colspan='2'>&nbsp;</td>";
echo "</tr>";

echo "<tr>";
echo "<td class='fila1' colspan='2'>Cobertura en Salud</td></td>";
echo "<td class='fila1'>Tipo de Afiliaci&oacute;n</td></td>";
echo "<td class='fila1'>Origen de la Atenci&oacute;n</td></td>";
echo "<td class='fila1'>Paciente Remitido</td></td>";
echo "<td class='fila1'>IPS que Remite</td>";
echo "<td class='fila1' colspan='2'>C&oacute;digo Aceptaci&oacute;n</td>";
echo "</tr>";
echo "<tr>";
$relaciones = construirArregloRelaciones();
$param="class='reset' ux='_ux_pacase_ux_midtus' msgcampo='Cobertura en salud'";
echo "<td class='fila2espacio' colspan='2'>";$res1=consultaMaestros('000105','Selcod,Seldes',$where="Seltip = '06' and Selest='on'",'','Selcod','2');
crearSelectHTMLAccEspecial($res1,'pac_tusselCobSal','pac_tusselCobSal',$param, '', 'cambioCobertura(this)', '06', $relaciones, true);
echo "</td>";
$param="class='reset' ux='_ux_mseafi_ux_arsafi' msgcampo='Tipo de afiliacion'";
echo "<td class='fila2espacio'>";$res1=consultaMaestros('000105','Selcod,Seldes',$where="Seltip = '16' and Selest='on'",'','Selcod','2');
crearSelectHTMLAccEspecial($res1,'pac_tafselTipAfi','pac_tafselTipAfi',$param, '', 'cambioTipoAfiliacion(this)', '16', $relaciones, false);
echo "</td>";
$param="class='reset' onchange='validarOrigenAte(this);' ux='_ux_paccin_ux_murext' msgcampo='Origen de la atencion'";
echo "<td class='fila2'>";$res1=consultaMaestros('000105','Selcod,Seldes',$where="Seltip = '12' and Selest='on'",'','Selcod','2');
crearSelectHTMLAcc($res1,'ing_caiselOriAte','ing_caiselOriAte',$param);
echo "<input type='hidden' name='causaAnteriorIngreso' value=''>";
echo "</td>";
echo "<td class='fila2'>";
echo "<table>";
echo "<tr><td align=right style='font-size: smaller;'>Si<td><input msgcampo='Paciente Remitido' type='radio' style='width:14px;height:12px;' name='pac_remradPacRem' id='pac_remradPacRems' value='S' onclick='validarPacienteRem();' ux='_ux_infrem_ux_murrem'></td>";
echo "<td align=right style='font-size: smaller;'>No<td><input msgcampo='Paciente Remitido' type='radio' style='width:14px;height:12px;' name='pac_remradPacRem' id='pac_remradPacRemn' value='N' onclick='validarPacienteRem();' ux='_ux_infrem_ux_murrem'></td></tr>";
echo "</table>";
echo "</td>";
echo "<td class='fila2'><input msgcampo='IPS que remite' type='text' name='pac_iretxtIpsRem' id='pac_iretxtIpsRem' class='reset' disabled msgError='Digite la Ips' ux='_ux_murent_ux_novnin'>";
echo "<input type='hidden' name='pac_irehidIpsRem' id='pac_irehidIpsRem' ux='_ux_pacrem_ux_novcin'>";
echo "</td>";  //puede ser select
echo "<td class='fila2' colspan='2'><input msgcampo='Codigo de aceptacion' type='text' name='pac_cactxtCodAce' id='pac_cactxtCodAce' class='reset' numerico disabled msgError='Digite el Codigo'></td>";
echo "</tr>";
// echo "<tr>";
// echo "<td class='fila1' colspan='2'>Clasificaci&oacute;n Usuario</td>";
// echo "</tr>";
// echo "<tr>";
// $param="class='reset'";
// echo "<td class='fila2' colspan='2'>";$res1=consultaMaestros('root_000099','Clacod,Clades',$where="Claest='on'",'','Clades');
// crearSelectHTMLAcc($res1,'pac_claselClausu','pac_claselClausu',$param);
// echo "</tr>";
echo "<tr>";
	echo "<td  class='fila2' nowrap=nowrap colspan='4' align='center'>El paciente autoriza la utilizaci&oacute;n de sus datos para efectos de Comunicaciones ?:&nbsp;&nbsp;&nbsp;
			SI:<input msgcampo='Paciente autoriza uso de datos' type='radio' style='width:14px;height:12px;' name='pac_audradPacAud' id='pac_audradPacAuds' value='on'>&nbsp;
			NO:<input msgcampo='Paciente autoriza uso de datos' type='radio' style='width:14px;height:12px;' name='pac_audradPacAud' id='pac_audradPacAuds' value='off'>
		 </td>";
		 echo "<td  class='fila2' nowrap=nowrap colspan='4' align='right'>Ingresa por tutela
			SI:<input msgcampo='Paciente con tutela' type='radio' name='ing_tutradiotutela'  id='ing_tutradiotutela' style='width:14px;height:12px;' value='on'>&nbsp;
			NO:<input msgcampo='Paciente con tutela' type='radio' name='ing_tutradiotutela'  id='ing_tutradiotutela' style='width:14px;height:12px;' value='off'>
		 </td>";
echo "</tr>";

//--mirar


echo "</table></center>";
echo "</div>"; //datos personales

echo "</div>"; //interno
echo "</div>"; //externo



//DATOS ACOMPAÑANTE
echo "<div id='div_datos_acompañante'>";
echo "<h3>DATOS ACOMPA&Ntilde;ANTE</h3>";
//datos acompañante
echo "<div id='div_int_datos_acompañante'>";
echo "<center><table width='70%'>";
echo "<th class='encabezadotabla' colspan='8'>Datos del Acompa&ntilde;ante</th>";
if( $cco_usuario_ayuda == "on" ){
	echo "<tr>";
	/*echo "<td class='fila1' width='25%'>Tipo Documento</td>";
	echo "<td class='fila2' width='25%'>";
	$res1res=consultaMaestros('root_000007','Codigo,Descripcion,alfanumerico, docigualhis',$where="Estado='on'",'','');
	crearSelectHTMLAcc($res1res,'','Pac_ceaselTipoDocAco',$param);
	echo"</td>";*/
	echo "<td class='fila1' width='10%'>Número Documento</td>";
	echo "<td class='fila2' width='17%'><input msgcampo='Telefono del acompañante' type='text' name='pac_ceatxtCelAco' id='pac_ceatxtCelAco' class='reset' ux='_ux_otrcea' msgError='Digite el Documento' onblur='validarCamposCedAco();'></td>";
	echo "<td class='fila1' width='25%'>Nombres y Apellidos del Acompa&ntilde;ante</td>";
	echo "<td class='fila2' width='25%'><input msgcampo='Nombre del acompañante' type='text' name='pac_noatxtNomAco' id='pac_noatxtNomAco' class='reset' ux='_ux_otrnoa' msgError='Digite los Nombres y Apellidos'></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td class='fila1' width='10%'>Tel&eacute;fono &nbsp;&nbsp;&nbsp;&nbsp; </td>";
	echo "<td class='fila2' width='17%'><input msgcampo='Telefono del acompañante' type='text' name='pac_teatxtTelAco' id='pac_teatxtTelAco' class='reset' ux='_ux_otrtra' msgError='Digite el Telefono' numerico></td>";
	echo "<td class='fila1' width='10%'>Direcci&oacute;n &nbsp;&nbsp;&nbsp;&nbsp; </td>";
	echo "<td class='fila2' width='17%'><input msgcampo='Direccion del acompañante' type='text' name='pac_diatxtDirAco' id='pac_diatxtDirAco' class='reset' ux='_ux_otrdra' msgError='Digite la direccion' numerico></td>";
	echo "</tr>";
}else{
	echo "<tr>";
	echo "<td class='fila1' width='25%'>Nombres y Apellidos del Acompa&ntilde;ante</td>";
	echo "<td class='fila2' width='25%'><input msgcampo='Nombre del acompañante' type='text' name='pac_noatxtNomAco' id='pac_noatxtNomAco' class='reset' ux='_ux_otrnoa' msgError='Digite los Nombres y Apellidos'></td>";
	echo "<td class='fila1' width='10%'>Tel&eacute;fono &nbsp;&nbsp;&nbsp;&nbsp; </td>";
	echo "<td class='fila2' width='17%'><input msgcampo='Telefono del acompañante' type='text' name='pac_teatxtTelAco' id='pac_teatxtTelAco' class='reset' ux='_ux_otrtra' msgError='Digite el Telefono' numerico></td>";
	echo "</tr>";
}
echo "</table></center>";
echo "</div>";
echo "</div>";


//DATOS RESPONSABLE DEL USUARIO
echo "<div id='div_datos_responsable'>";
echo "<h3>DATOS RESPONSABLE U ACUDIENTE DEL USUARIO</h3>";
echo "<div id='div_int_datos_responsable'>";
echo "<center><table>";
echo "<th class='encabezadotabla' colspan='8'>Datos Responsable del Usuario</th>";
echo "<tr>";
echo "<td class='fila1' >Tipo de Documento</td>";
echo "<td class='fila1' >N&uacute;mero Documento</td>";
echo "<td class='fila1' width='22%'>Nombres y Apellidos del Responsable</td>";
echo "<td class='fila1' >Parentesco</td>";
echo "<td class='fila1' colspan='2'>Direcci&oacute;n de Residencia</td>";
echo "</tr>";
echo "<tr>";
$param="class='reset' msgError msgcampo='Tipo documento responsable' onChange='cambiarTipoDocumento( this );'";
echo "<td class='fila1espacio' width='17%'>";$res1res=consultaMaestros('root_000007','Codigo,Descripcion,alfanumerico, docigualhis',$where="Estado='on'",'','');
crearSelectHTMLAcc($res1res,'pac_tdaselTipoDocRes','pac_tdaselTipoDocRes',$param);

echo "</td>";
echo "<td class='fila2' msgcampo='Documento responsable' width='17%'><input type='text' name='pac_crutxtNumDocRes' id='pac_crutxtNumDocRes' class='reset' msgError='Digite el numero de documento' ux='_ux_paccer_ux_mrecer_ux_accre3' onblur='validarCamposCedRes()'></td>";
echo "<td class='fila2'><input type='text' msgcampo='Nombres del responsable' name='pac_nrutxtNomRes' id='pac_nrutxtNomRes' class='reset' msgError='Digite el nombre del responsable' ux='_ux_pacres_ux_infavi_ux_mreres_ux_accno3'></td>";
echo "<td style='font-size: smaller;'><input type='text' msgcampo='Parentezco responsable' name='pac_prutxtParRes' id='pac_prutxtParRes' class='reset' ux='_ux_infpar' msgError='Digite el parentesco'></td>";
echo "<td class='fila2' colspan='2'><input type='text' msgcampo='Direccion responsable' name='pac_drutxtDirRes' id='pac_drutxtDirRes' class='reset' ux='_ux_pacdre_ux_mredir_ux_infdav' msgError='Digite la Direccion' depend='pac_ddrtxtDetDirRes'></td>";
echo "</tr>";
echo "<tr>";
echo "<td class='fila2'>Detalle de la Direcci&oacute;n</td>";
echo "<td class='fila2'>Departamento de Residencia</td></td>";
echo "<td class='fila2'>Municipio de Residencia</td></td>";
echo "<td class='fila2'>Tel&eacute;fono Fijo</td>";
echo "<td class='fila2'>N&uacute;mero M&oacute;vil</td>";
echo "<td class='fila2' width='17%'>Correo Electr&oacute;nico</td>";
echo "</tr>";
echo "<tr>";
echo "<td class='fila2espacio'><input type='text' msgcampo='Detalle direccion responsable' name='pac_ddrtxtDetDirRes' id='pac_ddrtxtDetDirRes' class='reset' ux='_ux_pacdre_ux_infdav' msgError='Digite el detalle de la Direccion' depend='pac_drutxtDirRes'></td>";
echo "<td class='fila2espacio'>";
echo "<input type='text' msgcampo='Departamento residencia del responsable' name='pac_dretxtDepResp' id='pac_dretxtDepResp' class='reset' msgError='Digite el Departamento'>";
echo "<input type='hidden' name='pac_drehidDepResp' id='pac_drehidDepResp'>";
echo "</td>";
echo "<td class='fila2espacio'>";
echo "<input type='text' msgcampo='Municipio residencia del responsable' name='pac_mretxtMunResp' id='pac_mretxtMunResp' class='reset' srcDep='pac_drehidDepResp' msgError='Digite el Municipio'>";
echo "<input type='hidden' name='pac_mrehidMunResp' id='pac_mrehidMunResp'>";
echo "</td>";
echo "<td class='fila2espacio'><input type='text' msgcampo='Telefono fijo del responsable' name='pac_trutxtTelRes' id='pac_trutxtTelRes' class='reset' ux='_ux_pactre_ux_inftav' msgError='Digite el Telefono' depend='pac_mortxtNumResp' numerico></td>";
echo "<td class='fila2espacio'><input type='text' msgcampo='Telefono movil del responsable' name='pac_mortxtNumResp' id='pac_mortxtNumResp' class='reset' msgError='Digite el Movil' depend='pac_trutxtTelRes' numerico></td>";
echo "<td class='fila2espacio'><input type='text' name='pac_cretxtCorResp' id='pac_cretxtCorResp' class='reset' ux='_ux_infmai'></td>";
echo "</tr>";
echo "</table></center>";
echo"</div>"; //interno
echo "</div>"; //externo

//DATOS PAGADOR Y AUTORIZACION
echo "<div id='div_datos_Pag_Aut'>";
echo "<h3>DATOS PAGADOR Y AUTORIZACI&Oacute;N (RESPONSABLE DE LA CUENTA)</h3>";
echo "<div id='div_int_pag_aut'>"; //interno
echo "<div id='div_datos_pagador'>";
echo "<center>
		<table>
			<tr>
				<th style='width:45;'>&nbsp</th>
				<th class='encabezadotabla'>Datos Pagador</th>
				<th style='width:45;'>";
$alias1="UsuarioRecalcularTopes";
$usuariorecalculartopes =consultarAplicacion2($conex,$wemp_pmla,$alias1);
$usuariosrecalculartopes          = explode( ",", $usuariorecalculartopes );


					if(in_array($usuario, $usuariosrecalculartopes))
						echo "<input  type='button' value='Calcular topes'  onclick='recalculartopes2()'>";
echo"</th>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td style='font-size: 11pt;' align='right'><span onclick=\"addFila('tabla_eps','$wbasedato','$wemp_pmla');\" class='efecto_boton' >".NOMBRE_ADICIONAR1."</span></td>
				<td>&nbsp;</td>
			</tr>";
echo "</table></center>";

//primer y segundo responsable cuando es accidente
echo "<center><table id='tabla_responsables_1_2'>";
echo "<tr id='tr_titulo_primer_segundo_resp'>";
echo "<td style='width:45;' class='numeroresponsable corchete' rowspan=3>R1</td>";
echo "<td class='encabezadotabla' colspan='5' align='center'>Datos del Primer Responsable</td>";
//echo "<td class='encabezadotabla' colspan='1' align='center'>Valor Remitido</td>";
echo "<td style='width:45;'>&nbsp;</td>";
echo "</tr>";
echo "<tr>";
echo "<td class='fila1' width='30%'>Empresa Responsable SOAT</td>";
echo "<td class='fila1' width='10%'>Tarifa</td>";
echo "<td class='fila1'>Valor Salario Minimo Dia</td>";
echo "<td class='fila1'>Valor Tope</td>";
echo "<td class='fila1' width='20%'>Valor Remitido</td>";
echo "<td style='width:45;'>&nbsp;</td>";
echo "</tr>";
echo "<tr id='tr_resp_txt'>";
echo "<td class='fila2'><input type='text' name='restxtCodRes' id='restxtCodRes' class='reset' msgaqua=''>";
echo "<input type='hidden' name='dat_AccreshidCodRes24' id='dat_AccreshidCodRes24' ux='_ux_accres'>";
// echo "<input type='hidden' name='dat_Acc_cashidCodRes193' id='dat_Acc_cashidCodRes193'>";
echo "</td>";
echo "<td class='fila2'><input type='text' name='dat_AcctartxtTarRes' id='dat_AcctartxtTarRes' class='reset' ux='_ux_acctar' msgaqua=''></td>";
echo "<td class='fila2'><input type='text' name='dat_AccvsmtxtSalMin' id='dat_AccvsmtxtSalMin' class='reset' ux='_ux_accvsm' msgaqua=''></td>";
echo "<td class='fila2'><input type='text' name='dat_AcctoptxtValTop' id='dat_AcctoptxtValTop' class='reset' ux='_ux_acctop' msgaqua=''></td>";
/*Eran los datos del segundo responsable de accidente que era el fosiga*/
// echo "<td class='fila2'><input type='text' name='re2txtCodRes2' id='re2txtCodRes2' class='reset' msgaqua=''>";
// echo "<input type='hidden' name='dat_Accre2hidCodRes2' id='dat_Accre2hidCodRes2' ux='_ux_accre2'>";
// echo "<input type='hidden' name='re2hidtopRes2' id='re2hidtopRes2' ux=''>"; //DDD
// echo "</td>";
/*ahora el segundo reponsable se toma de la tabla tabla_eps, este campo pasa a ser "valor remitido" se guardara en la 101*/
echo "<td class='fila2'><input msgcampo='Valor Remitido SOAT' type='text' name='ing_vretxtValRem' ux='_ux_novval' disabled id='ing_vretxtValRem' class='reset campoRequerido' msgaqua='' msgError='Valor Remitido SOAT' numerico></td>";
echo "<td style='width:45;'>&nbsp;</td>";
echo "</tr>";
echo "</table></center>";

//primer responsable no accidente o evento catastrofico y tercer responsable en accidente o evento
	echo "<center><table id='tabla_eps'>";
	echo "<tr class='encabezadotabla' id='tr_titulo_tercer_resp'>";
	/*se cambia porque ya el segundo responsable va a ser de la tabla_eps*/
	// echo "<td colspan='9' align='center'>Datos del Tercer Responsable</td>";
	echo "<td colspan='9' align='center'>Otros responsables</td>";

	echo"</tr>";


	$id_fila = "1_tr_tabla_eps";
	echo "<tr id=".$id_fila.">"; //se le envia a la tabla 101 y a la tabla de accidentes manualmente al enviar
	$param="class='reset' msgError onchange='validarTipoResp(this);' ux='_ux_mreemp_ux_pacemp_ux_accemp' msgcampo='Tipo Responsable'";

	echo "<td><table>";
	echo "<tr>
			<td style='width:45;' class='numeroresponsable corchete' rowspan=4>R1</td>
			<td class='encabezadotabla titulo_responsable' colspan='9' align='center' nowrap='nowrap'>RESPONSABLE <p style='display:none;'>( <input type='radio' style='width:16px; height:16px;' name='res_comtxtNumcon' estadoAnterior='off' onclick=' cambiarEstadoComplementariedad( this ); ' id='res_comtxtNumcon{$id_fila}'> Aplica complementariedad en cirugía )</p></td>
			<td style='width:45;' class='corchetei' rowspan=4><img border=0 src='../../images/medical/root/borrar.png' onclick='removerFila(\"".$id_fila."\",\"".$wbasedato."\",\"tabla_eps\");'/></td>
		</tr>";
	echo "<tr>";
	echo "<td class='fila1'>Tipo de Responsable</td>";

	/*2014-10-20 DATOS PARA RESPONSABLE PARTICULAR*/
	echo "<td class='fila1 dato_particulares' style='display:none;'>Tipo Documento</td>";
	echo "<td class='fila1 dato_particulares' style='display:none;'>Número Documento</td>";
	echo "<td class='fila1 dato_particulares' style='display:none;'>Nombre Responsable</td>";
	$permiteCambioTarifaParticular = consultarAplicacion2($conex,$wemp_pmla,"cambiaTarifaParticular");
	if( $permiteCambioTarifaParticular ){
		echo "<td class='fila1 dato_particulares' style='display:none;'>Tarifa Responsable</td>";
	}
	/*FIN*/
	
	//Se cierra conexión de la base de datos :)
	//Se comenta cierre de conexion para la version estable de matrix :)
	//mysql_close($conex);
	
	echo "<td class='fila1 dato_esconder_particulares'>Aseguradora</td>";
	echo "<td class='fila1 dato_esconder_particulares'>Plan</td>";
	echo "<td class='fila1 dato_esconder_particulares'>N&uacute;mero de P&oacute;liza</td>";
	echo "<td class='fila1'>N&uacute;mero de Contrato</td>";
	echo "<td class='fila1'>Fecha inicio <br> responsabilidad</td>";
	echo "<td class='fila1'>Fecha final <br> responsable</td>";
	echo "<td class='fila1' align='center'>Topes</td>";
	//echo "<td class='fila1' align='center'><span onclick=\"addFila('tabla_eps','$wbasedato','$wemp_pmla');\" class='efecto_boton' >".NOMBRE_ADICIONAR1."</span></td>";
	//echo "<td class='fila1' align='center'><span onclick=\"addResponsable();\" class='efecto_boton' >".NOMBRE_ADICIONAR1."</span></td>";
	echo "</tr>";

	echo "<tr>";
	echo "<td class='fila2'>";$res1=consultaMaestros('000105','Selcod,Seldes',$where="Seltip = '09' and Selest='on'",'','Selcod','2');
	crearSelectHTMLAcc($res1,'ing_tpaselTipRes'.$id_fila.'','ing_tpaselTipRes',$param);
	echo "</td>";

	/*2014-10-20 DATOS PARA RESPONSABLE PARTICULAR*/
	$param="class='reset' msgError msgcampo='Tipo documento pagador' disabled";
	echo "<td class='fila2 dato_particulares' style='display:none;'>";
	mysql_data_seek( $res1res , 0 );
	crearSelectHTMLAcc($res1res,'res_tdo','res_tdo',$param);
	echo "</td>";
	echo "<td class='fila2 dato_particulares' style='display:none;'>";
	echo "<input type='text' name='res_doc' ondblclick='completarParticular( this )' onblur='completarParticular( this )' msgError msgcampo='Documento pagador' disabled>";
	echo "</td>";
	echo "<td class='fila2 dato_particulares' style='display:none;'>";
	echo "<input type='text' name='res_nom' msgError msgcampo='Nombre pagador' disabled>";
	echo "</td>";
	if( $permiteCambioTarifaParticular ){
		echo "<td class='fila2 dato_particulares' style='display:none;'>";
		echo "<input type='text' name='ing_tartxt' id='ing_tartxt'  msgError msgcampo='Tarifa' disabled>";
		echo "<input type='hidden' name='ing_tarhid' id='ing_tarhid'>";
		echo "</td>";
	}
	/*FIN*/

	echo "<td class='fila2 dato_esconder_particulares'>";
	echo "<input type='text' msgcampo='Aseguradora' name='ing_cemtxtCodAse' id='ing_cemtxtCodAse".$id_fila."' class='reset' ux='_ux_pacres_ux_mreres' msgError='Digite la Aseguradora'>";
	echo "<input type='hidden' name='ing_cemhidCodAse' id='ing_cemhidCodAse".$id_fila."' ux='_ux_mrecer_ux_paccer_ux_arsars'>";
	echo "<input type='hidden' name='ing_enthidNomAse' id='ing_enthidNomAse".$id_fila."'>";
	// echo "<input type='hidden' name='dat_Accno3hidNomAse' id='dat_Accno3hidNomAse' ux=''>";
	echo "</td>";
	echo "<td class='fila2 dato_esconder_particulares'><select msgcampo='Plan del responsable' name='ing_plaselPlan' id='ing_plaselPlan".$id_fila."'  class='reset' onChange='' ux='_ux_mrepla'>";
	echo "<option value=''>Seleccione...</option>";
	echo "</select>";
	echo "</td>";
	echo "<td class='fila2 dato_esconder_particulares'><input type='text' name='ing_poltxtNumPol' id='ing_poltxtNumPol".$id_fila."' class='reset' ux='_ux_pacpol' msgaqua='Digite la Poliza'>";
	echo "<input type='hidden' id=".$id_fila."_bd name=".$id_fila."_bd value='' ></td>";//lo tenia en el primer select
	echo "<td class='fila2'><input type='text' name='ing_ncotxtNumCon' id='ing_ncotxtNumCon".$id_fila."' class='reset' ux='' msgaqua='Digite el contrato'></td>";
	echo "<td> <input type='text' name='res_firtxtNumcon' fecha id='res_firtxtNumcon{$id_fila}' class='reset' msgerror='0000-00-00' value='".date('Y-m-d')."'></td>";
	echo "<td> <input type='text' name='res_ffrtxtNumcon' fecha id='res_ffrtxtNumcon{$id_fila}' class='reset' msgaqua='0000-00-00' value=''></td>";
	echo "<td align='center'>";
	echo "<input type='button' value='Topes' id='btnTopes".$id_fila."' name='btnTopes' style='width:100;height:25' onClick=\"mostrarDivTopes('ing_cemhidCodAse".$id_fila."',this)\">
		  <input type='hidden' name='res_fir' id='res_fir{$id_fila}' value=''>";
	echo "</td>";
	//echo "<td align='center'><span class='efecto_boton' onclick='removerFila(\"".$id_fila."\",\"".$wbasedato."\",\"tabla_eps\");'>".NOMBRE_BORRAR."</span></td>";
	echo "</tr>";
	echo "<tr><td colspan=9>";
	//div datos autorizacion
	echo "<div id='div_datos_autorizacion' style='width:100%'>";
	echo "<center><table>";
	echo "<th style='background-color:#6694E3; font-size:10pt;' colspan='9'>Datos Autorizaci&oacute;n</th>";
	echo "<tr>";
	$botonAgregarCUP = "<input type='button' style='width:20;' onclick='agregarCUPS(\"".$id_fila."\")' value='+' />";
	echo "<td class='fila1'>N&uacute;mero Autorizaci&oacute;n</td>";
	echo "<td class='fila1'>Fecha Autorizaci&oacute;n</td>";
	echo "<td class='fila1'>Hora Autorizaci&oacute;n</td>";
	echo "<td class='fila1'>Nombre Persona Autorizadora</td>";
	echo "<td class='fila1'>C&oacute;digo Servicio Autorizado (cups)".$botonAgregarCUP."</td>";
	echo "<td class='fila1'>Pago Compartido</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td class='fila2'><input type='text' msgcampo='Numero Autorizacion' name='ing_ordtxtNumAut' id='ing_ordtxtNumAut".$id_fila."' class='reset' ux='_ux_mseaut' placeholder='Digite el Numero'></td>";
	echo "<td class='fila2'><input type='text' name='ing_fhatxtFecAut' id='ing_fhatxtFecAut".$id_fila."' fecha value='".$fechaAct."' ux='_ux_ordfec'></td>";
	echo "<td class='fila2'><input type='text' name='ing_hoatxtHorAut' id='ing_hoatxtHorAut".$id_fila."' hora value='".$horaAct."' ></td>";
	echo "<td class='fila2'><input type='text' msgcampo='Nombre persona que autoriza' name='ing_npatxtNomPerAut' id='ing_npatxtNomPerAut".$id_fila."' class='reset'  placeholder='Digite el Nombre'></td>";
	echo "<td class='fila2'>";
	echo "<div><input type='text' msgcampo='CUPS' name='ing_cactxtcups' style='width:200px;' id='ing_cactxtcups".$id_fila."' class='reset'  placeholder='Digite el Codigo o el nombre'>";
	echo "<input type='hidden' name='ing_cachidcups' id='ing_cachidcups".$id_fila."' >";
	echo "<input type='hidden' name='id_idcups' id='id_idcups".$id_fila."' ></div>";
	echo "</td>";
	echo "<td class='fila2'>";
	$param="class='reset' msgcampo='Pago compartido'";
	$res1=consultaMaestros('000105','Selcod,Seldes',$where="Seltip = '22' and Selest='on'",'','seldes','2');
	crearSelectHTMLAccEspecial($res1,'ing_pcoselPagCom','ing_pcoselPagCom',$param, '', '', '22', $relaciones, false );
	echo "</td>";
	echo "</tr>";
	echo "</table></center>";
	echo "</div>";
	echo "</td></tr></table></td></tr>";

	echo "</tr>";
echo "</table>";

echo "</div>";

// echo "<input type='button' value='Topes' id='btnTopes' name='btnTopes' style='width:100;height:25' onClick=\"mostrarDivTopes()\">";

//div topes
echo "<div id='div_topes' style='display:none'>";
echo "</div>";
echo "<div id='Divresponsablestopes' ></div>";
echo "<div id='div_recalculartopes' style='display:none'></div>";

echo"</div>"; //interno
echo "</div>"; //externo

//OTROS DATOS DEL INGRESO
echo "<div id='div_otros_datos_ingreso'>";
echo "<h3>OTROS DATOS DEL INGRESO</h3>";
echo "<div id='div_int_otros_datos_ing'>";
echo "<center><table width='70%'>";
echo "<th class='encabezadotabla' colspan='8'>Otros Datos del Ingreso</th>";
echo "<tr>";
echo "<td class='fila1' width='22%'>Impresi&oacute;n Diagn&oacute;stica</td></td>";
echo "<td class='fila2' width='20%'>";
echo "<input type='text' msgcampo='Impresion diagnostica' name='ing_digtxtImpDia' id='ing_digtxtImpDia' class='reset' msgError='Digite la Impresion Diagnostica' style='width:400'>";
echo "<input type='hidden' name='ing_dighidImpDia' id='ing_dighidImpDia' ux='_ux_pacdin_ux_murdxi_ux_murdxe_ux_hosdxi'>";
echo "</td>";
echo "<td class='fila1' width='15%'>Destino</td></td>";
echo "<td class='fila2' width='20%'>";$res1=consultaMaestros('000105','Selcod,Seldes',$where="Seltip = '17' and Selest='on'",'','Selcod','2');
$param="class='reset' msgcampo='Destino'";
crearSelectHTMLAcc($res1,'ing_desselDes','ing_desselDes',$param);

echo "</td>";
echo "</tr>";
echo "<tr>";
echo "<td class='fila1' width='22%'>Medico de Ingreso</td></td>";
echo "<td class='fila2' colspan=3 width='78%'>";
echo "<input type='text' msgcampo='Medico de Ingreso' name='ing_meitxtMedIng' id='ing_meitxtMedIng' class='reset' msgError='Digite el medico de ingreso' >";
echo "<input type='hidden' name='ing_meihidMedIng' id='ing_meihidMedIng' ux='_ux_pacmed_ux_infmed'>"; //medico de ingreso y medico tratante en UNIX
echo "</td>";
echo "</tr>";



if($imprimirHistoria == "on"){
	echo "<input type='hidden' id='wbasedatoImp' name='wbasedatoImp' value='{$wbasedato}'>";
	echo "<tr>";
		echo "<td class='fila1' width='22%' colspan='4' align='center'>Imprimir Historia &nbsp; <input type='checkbox' name='chk_imprimirHistoria' id='chk_imprimirHistoria'></td>";
	echo "</tr>";
}

echo "</table></center>";
echo"</div>";
echo "</div>";

//DATOS ACCIDENTE - EVENTO
echo "<div id='div_accidente_evento'>";
echo "<h3>DATOS ACCIDENTE - EVENTO</h3>";
echo "<div id='int_acci_evento'>";
echo "<center><table style='width:700'><tr><td align='center'>";
echo "<INPUT TYPE='button' onclick='mostrarAccidentesTransito();' value='Mostrar accidentes de transito' width='250'>";
echo "</td><td align='center'>";
echo "<INPUT TYPE='button' onclick='mostrarEventosCatastroficos();' value='Mostrar eventos catastroficos' width='250'>";
echo "</td>";
echo "<td align='center'>";
echo "<INPUT TYPE='button' onclick='listarEventosCatastroficos();' value='Mostrar evento catastrofico' width='250'>";
echo "</td>";
echo "</tr></table></center>";
echo"</div>";
echo "</div>";


//BOTONES
echo "<div name='div_botones'>";
echo "<center><table class='fondoamarillo' style='width:100%'>";
echo "<tr>";
// echo "<td class='' align='center' ><input type='button' value='Iniciar' style='width:100;height:25' onClick=\"resetear();\">";
echo "<td class='' align='center' ><input type='button' value='Iniciar' style='width:100;height:25' onClick=\"iniciar();\">";
echo "<input type='button' value='Consultar' style='width:100;height:25' onClick=\"mostrarDatos()\">";

  $permisos = consultarPermisosUsuario( $user2[1] );
( $permisos['consulta'] == "on" ) ? $disabledActualizar = "disabled" : $disabledActualizar = "";

echo "<input type='button' name='btRegistrarActualizar' value='Registrar/Admitir' consulta='{$permisos['consulta']}' actualiza='{$permisos['actualiza']}' graba='{$permisos['graba']}' style='width:120;height:25' onClick=\"enviarDatos();\" $disabledActualizar>";
// echo "<input type='button' value='Cerrar' style='width:100;height:25'  onclick='javascript: cerrarVentana();'>";
echo "<input type='button' value='Regresar agenda' style='width:150;height:25'  onclick='javascript: mostrarPreadmision();'></td>";
// echo "<input type='button' value='Log' style='width:100;height:25'  onclick='llenarDatosLog();'></td>";
echo "</tr>";
echo "</table></center>";
echo "</div>";


//DATOS PREGUNTA SI SE HACE EL RESTO DEL INGRESO CUANDO ES URGENCIAS
echo "<div id='realizarIngresoCompleto' style='display:none'>";
echo "<center><table style='width:400' border='0'>";
echo "<tr>";
echo "<td>¿DESEA REALIZAR EL INGRESO?</td>";
echo "<td align=right>Si<td><input type='radio' name='realizarIng' id='realizarIngS' value='S' onclick='realizarIngreso();' style='display:none'></td>";
echo "<td align=right>&nbsp;&nbsp;No<td><input type='radio' name='realizarIng' id='realizarIngN' value='N' onclick='realizarIngreso();' style='display:none'></td></tr>";
echo "</tr>";
echo "</table></center>";
echo"</div>";

echo "<br>";
echo "<div id='bot_navegacion' style='display:none'>";
echo "<center><table style='width:500;' border='0'>";
echo "<th colspan='3' class='encabezadotabla'>Resultados de la busqueda</th>";
echo "<tr class='fila1'>";
echo "<td align='center' colspan='3'>Registro <span id='spRegAct'></span> de <span id='spTotalReg'></span>&nbsp;&nbsp;</td>";
echo "</tr>";
/*echo "<tr class='fila1'>"; //Total Ingresos:<span id='spTotalIng'></span>
echo "<td align='center' colspan='3'>Total Resultados:<span id='spTotalReg'></span>&nbsp;&nbsp;</td>";
echo "</tr>";
echo "<tr>";
echo "<td align='center' colspan='3' class='fila1'>Resultado:<span id='spRegAct'></span>&nbsp;con historia: &nbsp;<span id='spHisAct'></span>&nbsp;Ingreso:&nbsp;<span id='spIngAct'></span>&nbsp;de&nbsp;<span id='spTotalIng1'></span></td>";
echo "</tr>";*/
echo "<tr>";
echo "<td align='center' colspan='3'><img src='../../images/medical/citas/atras.jpg' height='30' width='30' border=0 onClick=\"navegacionIngresos(-1);\"/>";
echo "&nbsp;<img src='../../images/medical/citas/adelante.jpg' height='30' width='30' border=0 onClick=\"navegacionIngresos(+1);\"/></td>";
echo "</tr>";
echo "</table></center>";
echo "</div>";//div botones navegacion

echo "<div id='div_contenedor_topes' style='display:none'>";
echo "</div>";

echo "</div>"; //div_datosAdmiPreadmi

echo "</div>"; //div_admisiones

echo "<div id='div_contenedor' style='display:none'></div>";
echo "<div id='div_dialog_topes' style='display:none'></div>";

echo"</form>";
echo "<div id='msjAlerta' style='display:none;'>
	        <br>
	        <img src='../../images/medical/root/Advertencia.png'/>
	        <br><br><div id='textoAlerta'></div><br><br>
	    </div>";

echo "<div id='msjAlerta2' style='display:none;'>
	        <br>
	        <center><img src='../../images/medical/root/Advertencia.png'/></center>
	        <br><br><center><div id='textoAlerta2' style='font-size: 12pt;'></div></center><br>
	    </div>";

echo '<div id="divPlanyFlujo" style="display:none"></div><div id="divfaltantesporflujo" ></div>
		<div id="div_loading" style="display:none;text-align: center;"><img width="15" height="15" src="../../images/medical/ajax-loader5.gif" /> Consultando datos, espere un momento por favor...</div>
		<div id="div_iniciar_alta_otro_ss" style="display: none;">
			<table align="center">
				<tr class="encabezadoTabla"><td colspan="2" style="text-align: center;">Verificaci&oacute;n de historia</td></tr>
				<tr>
					<td class="encabezadoTabla">Digite la historia</td>
					<td class="fila2"><input type="text" id="historia_alta" name="historia_alta" onkeypress="return soloNumeros(event);" onkeyup="return validarEnterConsultar(event);" placeholder="Historia"></td>
				</tr>
				<tr>
					<td class="fila2" colspan="2" style="text-align: center;"><input type="button" id="btn_consultar_hitoria_alta" value="Consultar" onclick="consultarHistoriaPendienteDeAlta();" style="width:100px;"></td>
				</tr>
			</table>
			<div id="div_info_alta" style="text-align: center;"></div>
		</div>';
echo "</body>";


//--------------agrego modal para  soportes
echo "<div  id='modaliframe' style='display:none; height:600px'   ></div>";

//-----------------

$param   = "style='border-radius: 4px;border:1px solid #AFAFAF;width:200px;'";
$res1res = consultaMaestros('root_000007','Codigo,Descripcion,alfanumerico, docigualhis',$where="Estado='on'",'','');

echo "<div id='div_doc_change' class='fila2' style='display:none;'><br>

		<div align='center'><span class=subtituloPagina2 style=font-size:12px;>DATOS ACTUALES DEL PACIENTE </span></div><br>
		<div align='center'>
			<table class='tabla_datos'>
				<tbody>
					<tr>
						<td align='left' class='fila1 td_datos'><b>HISTORIA:</b></td><td aling='left' class='td_datos'><span style='font-size:12px;' id='span_his_actual'> historia actual </span></td>
						<td align='left' class='fila1 td_datos'><b>DOCUMENTO:</b></td><td aling='center' class='td_datos'><span style='font-size:12px;' id='span_doc_actual'> documento actual </span></td>
						<td align='left' class='fila1 td_datos'><b>NOMBRE:</b></td><td align='left' class='td_datos'><span style='font-size:12px;' id='span_nom_actual'> nombre actual </span></td>
					</tr>
				</tbody>
			</table>
		</div>
		<br>
		<div align='center'><span class=subtituloPagina2 style=font-size:12px;>DATOS NUEVOS DEL PACIENTE </span></div><br>
		<br>
		<center><div align='center' class='fila1' style='width:80%; padding:5px;'><b>TIPO DOCUMENTO:</b>";
		crearSelectHTMLAcc($res1res,'select_tdoc_new','select_tdoc_new',$param);
 echo " &nbsp;&nbsp; <b>DOCUMENTO:</b> <input id='select_doc_new' value=''  style='border-radius: 4px;border:1px solid #AFAFAF;width:200px;'></div><center><br>
		<div align='center'><span class=subtituloPagina2 style=font-size:12px;>JUSTIFICACI&OacuteN DEL CAMBIO </span></div>
		<div align='center'> <textarea id='txt_jus_change_Doc' class='textarea_justi'></textarea> </div>

		<div id='div_mensaje_solitudCD' align='center' style='display:none;'>
            <br>
                <img src='../../images/medical/root/Advertencia.png'/><span  class='subtituloPagina2' id='span_mensajeCD'>  </span>
            <br>
        </div>
	 </div>";
	 echo "<div id='div_preanestesia' align='center' style='display:none;'>
	 			<input type='hidden' id='asociarPreanestesia' value='off'>
	 			<input type='hidden' id='turnoPreAnestesia' value=''>
				<p> Este paciente tiene una preanestesia asociada al turno de cirug&iacute;a n&uacute;mero: <span  class='subtituloPagina2' id='turno_preanestesia'>  </span> </p>
				 <p> ¿ Desea asociar dicha preanestesia a este ingreso ? </p><br>
				 <button class='boton_preanestesia' type='button' id='btn_si_preanestesia' onkeypress='return false;' onclick='asociarPreanestesia( this )'>SI</button>&nbsp;&nbsp;<button class='boton_preanestesia' type='button' id='btn_no_preanestesia' onkeypress='return false;' onclick='asociarPreanestesia( this )'>NO</button>
				 <input type='text' id='input_aux_prea' value='' style='display:none;'>
	 	   </div>";
echo "</html>";
}


?>