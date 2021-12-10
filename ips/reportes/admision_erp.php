<?php
include_once("conex.php");
header("Content-Type: text/html;charset=ISO-8859-1");
/****************************************************************************
* accion
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

/*if( isset($user) )
	$key = substr( $user, 2 );*/
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
	//para unix
	$_POST['_ux_pacap2_ux_midap2'] = str_replace( $caracteres, $caracteres2, utf8_decode($_POST['_ux_pacap2_ux_midap2']) );
	$_POST['_ux_pacap1_ux_midap1'] = str_replace( $caracteres, $caracteres2, utf8_decode($_POST['_ux_pacap1_ux_midap1']) );
	$_POST['_ux_pnom1_ux_midno1'] = str_replace( $caracteres, $caracteres2, utf8_decode($_POST['_ux_pnom1_ux_midno1']) );
	$_POST['_ux_pnom2_ux_midno2'] = str_replace( $caracteres, $caracteres2, utf8_decode($_POST['_ux_pnom2_ux_midno2']) );
	$_POST['omitirCambioReponsable'] = false;

	if( $datosEnc['pac_pahtxtPaiRes'] != CODIGO_COLOMBIA ){
		$datosEnc['pac_trhselTipRes'] = "E";
	}else{
		$datosEnc['pac_trhselTipRes'] = "N";
	}

	$datosEnc['pac_tdaselTipoDocRes'] = trim( $datosEnc['pac_tdaselTipoDocRes'] );
	$datosEnc['pac_tdoselTipoDoc'] = trim( $datosEnc['pac_tdoselTipoDoc'] );
	/*if( $datosEnc['pac_trhselTipRes'] == "E" ){
		$datosNoAplica      = buscarDatosNoAplicaExtranjeros();
		//$datosEnc['_ux_pacmun_ux_midmun'] = $datosNoAplica['departamento'];
		$datosEnc['_ux_pacmun_ux_midmun'] = $datosNoAplica['municipio'];
		$datosEnc['_ux_midbar'] = $datosNoAplica['barrio'];
	}*/

	$preadmisionAnulada = false;

	$consecutivoHistoriaDeUnix = false; //Variable que indica si el CONSECUTIVO de historia se trajo desde UNIX
	$datosEnc="";
	/************************************************************************************************************
	 * Septiembre 03 de 2013
	 * Si tiene una preadmisión y no es en la misma fecha posible de admisión no se deja hacer la admisión
	 ************************************************************************************************************/
	/*$sql = "select Pacdoc,Pacfec,id,Pacact
			  from ".$wbasedato."_000166
			 where Pacdoc = '".utf8_decode($documento)."'
			   and Pacact = 'on'";

	$resPre = mysql_query( $sql, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando el documento ".mysql_errno()." - Error en el query $sql - ".mysql_error() ) );

	if( $resPre ){
		$numPre = mysql_num_rows( $resPre );
		if( $numPre > 0 )
		{
			if( $rowsPre = mysql_fetch_array( $resPre ) )
			{
				if( $rowsPre[ 'Pacfec' ] != date( "Y-m-d" ) )
				{
					$data[ 'error' ] = 1;
					$data[ 'mensaje' ] = utf8_encode( "No se puede realizar el ingreso,\ntiene una preadmisión para el día ".$rowsPre[ 'Pacfec' ] );
					echo json_encode($data);
					return;
				}
			}
		}
	}
	else{
		//$data[ 'error' ] = 1;
	}*/
	/************************************************************************************************************/
	//se consulta si existe esa aplicacion
	$alias="movhos";
	$aplicacion=consultarAplicacion2($conex,$wemp_pmla,$alias);

	$alias1="hce";
	$aplicacionHce=consultarAplicacion2($conex,$wemp_pmla,$alias1);
	$intentosIngreso=consultarAplicacion2($conex,$wemp_pmla,"intentosPermitidos");//--> intentos permitidos para grabar un ingreso en unix
	//Consultar la historia e ingreso nuevo
	//1. Consultar por documento
	//	- Si el documento no se encuentra en la BD, significa que la historia no existe para el paciente
	//	- Si no se encuentra el documento, consultar la nueva historia
	//	- Si existe el documento, consulto la historia y el numero de ingreso nuevo (Esto es sumarle uno al último ingreso)

	//Si se esta tratando de actualizar el ingreso, y antes era por accidente de transito
	//y lo va a cambiar, se debe validar que el saldo del responsable transito este intacto, en caso contrario no deja cambiarlo
	if( !empty( $historia ) && !empty( $ingreso ) && $ing_caiselOriAte != '02' )
	{
		$sql2 = "SELECT Ingcai, Ingvre
				   FROM ".$wbasedato."_000101
				  WHERE Inghis = '".utf8_decode($historia)."'
					AND Ingnin = '".utf8_decode($ingreso)."'";

		$res2 = mysql_query( $sql2, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando el ingreso " .mysql_errno()." - Error en el query $sql2 - ".mysql_error() ) );
		if( $res2 )
		{
			$num2=mysql_num_rows($res2);
			if ($num2>0)
			{
				$rows2=mysql_fetch_array($res2);
				if( $rows2[0] == '02' ){ //Si el origen de la atencion es accidente de transito

					//Si es acc. transito el primer responsable (resord=1) es el responsable transito y si toptop != topsal es porque ya consumio el saldo
					$sqlpro = "SELECT Resnit as codigo, Resord as orden, Toptco as tipo, Toptop as tope, Topsal as saldo
								 FROM ".$wbasedato."_000205, ".$wbasedato."_000204
								WHERE Reshis = '".$historia."'
								  AND Resing = '".$ingreso."'
								  AND Tophis = Reshis
								  AND Toping = Resing
								  AND Topres = Resnit
								  AND Toptop*1 != (Topsal*1 + {$rows2[1]}*1)
								  AND Resord = '1'
								  AND Resest = 'on'
								  AND Topest = 'on'
								  AND Resdes = 'off'";
					$res3 = mysql_query( $sqlpro, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando el ingreso " .mysql_errno()." - Error en el query $sql2 - ".mysql_error() ) );
					if( $res3 )
					{
						$num3=mysql_num_rows($res3);
						if ($num3>0)
						{
							$data[ "error" ] = 1;
							$data[ "mensaje" ] = utf8_encode("No se puede cambiar el origen de la atencion");
							echo json_encode($data);
							return;
						}
					}
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
			$tieneConexionUnix = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'conexionUnix' );
			if( $tieneConexionUnix != "on" ){
				$historiaUx = $_POST[ 'ing_histxtNumHis' ];
				$ingresoUx  = $_POST[ 'ing_nintxtNumIng' ];
			}

			//--> consultar si está llegando el último ingreso
			if( $modoConsulta == "true" ){//-->2016-12-27 -> para mantener la siguiente consulta solo para el último ingreso

				$sql = " SELECT pachis, pacact, max(ingnin*1) ultimoIngreso
						   FROM {$wbasedato}_000101, {$wbasedato}_000100
						  WHERE inghis = '{$_POST[ 'ing_histxtNumHis' ]}'
						    AND pachis = inghis
						  GROUP BY 1,2";
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
				$sql = "Select Pacdoc,Pachis,id,Pacact
					from ".$wbasedato."_000100
					where Pacdoc = '".utf8_decode($documento)."'
					and Pactdo = '".utf8_decode($tipodoc)."'
					";
			}else{
				$sql = "Select Pacdoc,Pachis,a.id,Ubiald as Pacact, Ubihis
					from ".$wbasedato."_000100 a LEFT JOIN ".$aplicacion."_000018 b ON (Pachis=Ubihis)
					where Pacdoc = '".utf8_decode($documento)."'
					and Pactdo = '".utf8_decode($tipodoc)."'
					ORDER BY b.fecha_data desc, b.hora_data desc limit 1
					";
			}

			$res = mysql_query( $sql, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando el documento ".mysql_errno()." - Error en el query $sql - ".mysql_error() ) );
			$num= mysql_num_rows($res);

			if ($num>0) // si tiene registros se consulta el numero de ingreso
			{
				$rows=mysql_fetch_array($res);

				$historia=$rows['Pachis'];
				$sql2 = "select Inghis,MAX(Ingnin*1) AS Ingnin,id
				from ".$wbasedato."_000101
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
					$sql3= "select Carcon
							  from ".$wbasedato."_000040
							 where Carfue='01'";
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
								$sql4 =  " UPDATE ".$wbasedato."_000040
								set Carcon = Carcon + 1
								where Carfue='01'
								";
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
		$ingreso          = 1;
	}


	//-->2016-05-13
	//mirar  si el ingreso anterior sigue activo en la tabla 18
	// SI YA TENEMOS HISTORIA E INGRESO, VALIDAMOS QUE EL INGRESO INMEDIATAMENTE ANTERIOR TENGA ALTA DEFINITIVA EN LA 18
	if( $ingreso*1 > 1 ){
		$ingresoAnterior = $ingreso - 1;
		$queryAD = " SELECT ubiald
					   FROM {$aplicacion}_000018
					  WHERE ubihis = '{$historia}'
					    AND ubiing = '{$ingresoAnterior}'";
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
	if( !empty( $historia ) && !empty( $ingreso ) )
	{

	   /**ingreso**/

		//Consulto si existe el registo
		$sql = "select Inghis,Ingnin,id, Ingunx, ingvre*1
				from ".$wbasedato."_000101
				where Inghis = '".utf8_decode($historia)."'
				and Ingnin = '".utf8_decode($ingreso)."'
				";

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
				$sqlt = "select Emptar
				from ".$wbasedato."_000024
				where Empcod = '".utf8_decode($ing_cemhidCodAse)."'
				and Empest = 'on'
				";

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
				$datosEnc = crearArrayDatos( $wbasedato, "ing", "ing_", 3 );

				//datos adicionales
				$datosEnc[ "ingusu" ] = $key; //usuario
				$datosEnc[ "inghis" ] = $historia; //historia
				$datosEnc[ "ingnin" ] = $ingreso; //ingreso
				$datosEnc[ "ingtin" ] = $datosEnc[ "ingsei" ]; //tipo ingreso
				$datosEnc[ "ingtar" ] = $tarifa; //tarifa
				//datos del primer responsable no accidente
				/*$datosEnc[ "ingcem" ] = $responsables[0]['ing_cemhidCodAse1_tr_tabla_eps'];
				$datosEnc[ "ingtpa" ] = $responsables[0]['ing_tpaselTipRes1_tr_tabla_eps'];
				$datosEnc[ "ingpla" ] = $responsables[0]['ing_plaselPlan1_tr_tabla_eps'];
				$datosEnc[ "ingpol" ] = $responsables[0]['ing_poltxtNumPol1_tr_tabla_eps'];
				$datosEnc[ "ingnco" ] = $responsables[0]['ing_ncotxtNumCon1_tr_tabla_eps'];*/

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

				/*$resEnc = mysql_query( $sqlInsert, $conex ) or die ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() ) );*/
				$resEnc = mysql_query( $sqlInsert, $conex ) or die ( mysql_error() );

				if( mysql_affected_rows() > 0 )
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
				//2017-10-23 seguir esta variable $cambioTemporalSaldoCero para revisar los puntos de cambio y retorno del arreglo de responsable.
				$cambioTemporalSaldoCero = false;
				$responsables205         = $responsables1;
				$queryCA = " SELECT count(*) cantidad
				               FROM {$wbasedato}_000313
				              WHERE Crahis = '{$historia}'
				                AND Craing = '{$ingreso}'
				                AND Crarea = '{$responsables1[0]['ing_cemhidCodAse']}'
				                AND Craest = 'on'";
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

				//2014-03-10 El campo ingunx indica si el ingreso se guardó en unix
				//El campo consecutivoHistoriaDeUnix es true cuando la admision ya se guardo en unix
				/*$datosTabla[ "ingunx" ] = "off";
				if( $consecutivoHistoriaDeUnix == true )
					$datosTabla[ "ingunx" ] = "on";*/
				$datosTabla["ingunx"] = $rowsEnc['Ingunx'];//--> 2015-11-11 si ya se había guardado en unix, que cargue el dato para que en erp_unix se hagan actualizaciones y no inserciones.

				$sqlUpdate = crearStringUpdate( $wbasedato."_000101", $datosTabla );

				/*$res1 = mysql_query( $sqlUpdate, $conex ) or ( $data[ 'error' ] = utf8_encode( mysql_errno()." - Error en el query de ingreso $sqlUpdate - ".mysql_error() ) );*/
				$res1 = mysql_query( $sqlUpdate, $conex ) or die(  mysql_error() );

				if( $res1 )
				{
					if( mysql_affected_rows() > 0 ){
						$data[ "mensaje" ] = utf8_encode( "Se actualizo correctamente" );
					}
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
				if( mysql_affected_rows() > 0 ){
					logAdmsiones( 'Registro borrado por documento duplicado', $historia, $tipodoc, $documento);
				}
			}

			/**admision**/
			//Consulto si existe el registo
			$sql1 = "select Pachis,a.id, ingfei, ingvre
					from ".$wbasedato."_000100 a,".$wbasedato."_000101 b
					where b.Inghis = '".utf8_decode($historia)."'
					and b.Ingnin = '".utf8_decode($ingreso)."'
					and b.Inghis =a.Pachis
					";

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
					$sqlInsert = crearStringInsert( $wbasedato."_000100", $datosEnc );
					$data1 = logAdmsiones( "Respuesta Autorizacion de datos[ Pacaud={$datosEnc['Pacaud']}]", $historia, $ingreso, $documento );

					$resEnc = mysql_query( $sqlInsert, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query admision $sqlInsert - ".mysql_error() ) );

					if( mysql_affected_rows() > 0 )
					{	//si inserto
						$data[ "mensaje" ] = utf8_encode( "Se registró correctamente " );
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
								        pactdo = '".$pac_tdoselTipoDoc."'
									AND pacdoc = '".$pac_doctxtNumDoc."'
									AND pacact = 'on'
									AND pacfec <= '".date( "Y-m-d" )."'
								";

						$resCancPre = mysql_query( $sql, $conex ) or die(  $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query admision $sql - ".mysql_error() )  );

						if( !$resCancPre ){
							//$data[ "error" ] = 1;
						}
						else{
							// $data1 = logAdmsiones( "Preadmision anulada", $historia, $ingreso, $pac_doctxtNumDoc );
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

						$resup207 = mysql_query( $sql, $conex ) or die( mysql_error() );
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
					$qaud  = " SELECT Pacaud, Pacact
								 FROM {$wbasedato}_000100
								WHERE pachis = '{$historia}'";
					$rsaud  = mysql_query( $qaud, $conex );
					$rowaud = mysql_fetch_assoc( $rsaud );

					$rowsEnc = mysql_fetch_array( $res1 );
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
					$sqlUpdate = crearStringUpdate( $wbasedato."_000100", $datosTabla );
					/*$res2 = mysql_query( $sqlUpdate, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query $sqlUpdate - ".mysql_error() ) );*/
					$res2 = mysql_query( $sqlUpdate, $conex ) or die( mysql_error() );

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

						$resup207 = mysql_query( $sql, $conex ) or die( mysql_error() );
					}
					else
					{
						$data[ "error" ] = 1;
					}
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
			//echo $sqlpro;
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
			//echo "<br>Array Topes viejos".json_encode($arr_topes_responsables);

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
					/*$ord = 1;
					foreach( $arr_topes_responsables as $cod_resx => $arr_topes ) //por cada responsable-tope existente
					{
						array_push($arr_responsables_no_tocar, $cod_resx );
						array_push($arr_orden_res_no_tocar, $ord  );
						$ord++;
					}*/
				}
			}
			/* fin se comprueba si permite modificar los responsables y los topes	*/
			/* FIN SE CONSULTA LA INFORMACION DE LOS RESPONSABLES Y LOS TOPES  */
			/******************************************************************************************/
			/******************************************************************************************/
		}

		//	echo json_encode($arr_responsables_no_tocar);
		//	exit;

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

						$rsDes    = mysql_query( $queryDes, $conex ) or die( mysql_error() );
					}

					//foreach( $responsables1 as $keRes => $valueRes ){2017-10-18 que guarde originalmente, en la 205 aunque haya cambiado el responsable principal
																				//por culpa del saldo( SOAT )
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
										/*$resEnc = mysql_query( $sqlInsert, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error grabando en la tabla ".$wbasedato."_000209 - ".mysql_error() ) );*/
										$resEnc = mysql_query( $sqlInsert, $conex ) or die(mysql_error());
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

				/*$resDel = mysql_query( $sqlDel, $conex ) or ( $data[ 'error' ] = utf8_encode( mysql_errno()." - Error en el query $sqlDel - ".mysql_error() ) );
				if (!$resDel){
					$data['error'] = 1;
				}*/
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
				$resEncSOAT = mysql_query( $sqlInsertSOAT, $conex ) or die ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error grabando en la tabla ".$wbasedato."_000204 - ".mysql_error() ) );

				if (!$resEncSOAT){
					$data['error']=1;
				}
			}
			else
			{
				/*se borran el registro de soat */
					/*$sqlDel="delete from ".$wbasedato."_000204//-->2016-07-31
							 where tophis = ".$historia."
							 and toping = ".$ingreso."
							 and toprec = '*'"; //sea de soat
					$resDel = mysql_query( $sqlDel, $conex ) or ( $data[ 'error' ] = utf8_encode( mysql_errno()." - Error en el query $sqlDel - ".mysql_error() ) );
					if (!$resDel){
						$data['error'] = 1;
					}*/
					//si se ejecuta la consulta no sacaria error si no borra nada
				/*fin se borran el registro de soat*/
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
								$resEnc = mysql_query( $sqlInsert, $conex ) or die( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error grabando en la tabla ".$wbasedato."_000204 - ".mysql_error() ) );

							}else{
								$sqlInsert = crearStringInsert( $wbasedato."_000204", $datosEnc );
								$resEnc    = mysql_query( $sqlInsert, $conex ) or die( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error grabando en la tabla ".$wbasedato."_000204 - ".mysql_error() ) );
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
				if( $cargosAregrabar > 0 ){
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
					//Se crean los datos para root_000036
					global $pac_no1txtPriNom,$pac_no2txtSegNom,$pac_ap1txtPriApe,$pac_ap2txtSegApe,$pac_fnatxtFecNac,$pac_sexradSex;
					$datosEnc = array();
					$datosEnc[ "pacno1" ] = strtoupper($pac_no1txtPriNom);
					$datosEnc[ "pacno2" ] = strtoupper($pac_no2txtSegNom);
					$datosEnc[ "pacap1" ] = strtoupper($pac_ap1txtPriApe);
					$datosEnc[ "pacap2" ] = strtoupper($pac_ap2txtSegApe);
					$datosEnc[ "pacfna" ] = $pac_fnatxtFecNac;
					$datosEnc[ "pacsex" ] = $pac_sexradSex;

					//se consulta si existe ese documento en la tabla unica de pacientes
					$sql5 = "select Pacced
					from  root_000036
					where Pactid='".utf8_decode($tipodoc)."'
					and Pacced='".utf8_decode($documento)."'";
					$res5 = mysql_query( $sql5, $conex ) or die( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla unica de pacientes ".mysql_errno()." - Error en el query $sql5 - ".mysql_error() ) );

					if ($res5)
					{
						$num5=mysql_num_rows($res5);
						if ($num5>0)
						{
							//se actualiza el registro tabla de pacientes
							$sql6 =  " update root_000036 set
							  Pacap1='".utf8_decode(strtoupper($datosTabla[ "pacap1" ]))."',
							  Pacap2='".utf8_decode(strtoupper($datosTabla[ "pacap2" ]))."',
							  Pacno1='".utf8_decode(strtoupper($datosTabla[ "pacno1" ]))."',
							  Pacno2='".utf8_decode(strtoupper($datosTabla[ "pacno2" ]))."',
							  Pacnac='".utf8_decode($datosTabla[ "pacfna" ])."',
							  Pacsex='".utf8_decode($datosTabla[ "pacsex" ])."'
							  where Pactid='".utf8_decode($tipodoc)."'
							  and Pacced='".utf8_decode($documento)."'";

							$res6 = mysql_query( $sql6, $conex ) or die( $data[ 'mensaje' ] = utf8_encode( "Error actualizando la tabla unica de pacientes ".mysql_errno()." - Error en el query $sql6 - ".mysql_error() ) );
							if (!$res6){
								$data[ 'error' ] = 1; //sale el mensaje de error
							}
						}
						else //se inserta el registro tabla de pacientes
						{
							$sql7 = "insert into root_000036 (medico,fecha_data,hora_data, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, seguridad)
							values ('root','".utf8_decode($fecha)."','".utf8_decode($hora)."','".utf8_decode($documento)."','".utf8_decode($tipodoc)."','".utf8_decode($datosEnc[ "pacno1" ])."','".utf8_decode($datosEnc[ "pacno2" ])."','".utf8_decode($datosEnc[ "pacap1" ])."','".utf8_decode($datosEnc[ "pacap2" ])."','".utf8_decode($datosEnc[ "pacfna" ])."','".utf8_decode($datosTabla[ "pacsex" ])."','C-root')";

							$res7 = mysql_query( $sql7, $conex ) or die( $data[ 'mensaje' ] = utf8_encode( "Error insertando en la tabla unica de pacientes ".mysql_errno()." - Error en el query $sql7 - ".mysql_error() ) );
							if (!$res7){
								$data[ 'error' ] = 1; //sale el mensaje de error
							}
						}
					}
					else
					{
						$sql7 = "insert into root_000036 (medico,fecha_data,hora_data, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, seguridad)
						values ('root','".utf8_decode($fecha)."','".utf8_decode($hora)."','".utf8_decode($documento)."','".utf8_decode($tipodoc)."','".utf8_decode($datosEnc[ "pacno1" ])."','".utf8_decode($datosEnc[ "pacno2" ])."','".utf8_decode($datosEnc[ "pacap1" ])."','".utf8_decode($datosEnc[ "pacap2" ])."','".utf8_decode($datosEnc[ "pacfna" ])."','".utf8_decode($datosTabla[ "pacsex" ])."','C-root')";

						$res7 = mysql_query( $sql7, $conex ) or die( $data[ 'mensaje' ] = utf8_encode( "Error insertando en la tabla unica de pacientes ".mysql_errno()." - Error en el query $sql7 - ".mysql_error() ) );
						if (!$res7){
							$data[ 'error' ] = 1; //sale el mensaje de error
						}
					}
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
							  Oriing='".utf8_decode($ingreso)."'
							  where Oritid='".utf8_decode($tipodoc)."'
							  and Oriced='".utf8_decode($documento)."'
							  and Oriori='".utf8_decode($wemp_pmla)."'";

							$res9 = mysql_query( $sql9, $conex ) or die( $data[ 'mensaje' ] = utf8_encode( "Error actualizando la tabla de origen historia del paciente ".mysql_errno()." - Error en el query $sql9 - ".mysql_error() ) );
							if (!$res9){
								$data[ 'error' ] = 1; //sale el mensaje de error
							}
						}
						else //si es registro nuevo en la tabla origen historia del paciente
						{
							$sql10 = "insert into root_000037 (medico,fecha_data,hora_data, Oriced, Oritid, Orihis, Oriing, Oriori, seguridad)
							values ('root','".utf8_decode($fecha)."','".utf8_decode($hora)."','".utf8_decode($documento)."','".utf8_decode($tipodoc)."','".utf8_decode($historia)."','".utf8_decode($ingreso)."','".utf8_decode($wemp_pmla)."','C-root')";

							$res10 = mysql_query( $sql10, $conex ) or die( $data[ 'mensaje' ] = utf8_encode( "Error insertando en la tabla de origen historia del paciente ".mysql_errno()." - Error en el query $sql10 - ".mysql_error() ) );
							if (!$res10){
								$data[ 'error' ] = 1; //sale el mensaje de error
							}
						}
					}
					else{
						$sql10 = "insert into root_000037 (medico,fecha_data,hora_data, Oriced, Oritid, Orihis, Oriing, Oriori, seguridad)
						values ('root','".utf8_decode($fecha)."','".utf8_decode($hora)."','".utf8_decode($documento)."','".utf8_decode($tipodoc)."','".utf8_decode($historia)."','".utf8_decode($ingreso)."','".utf8_decode($wemp_pmla)."','C-root')";

						$res10 = mysql_query( $sql10, $conex ) or die( $data[ 'mensaje' ] = utf8_encode( "Error insertando en la tabla de origen historia del paciente ".mysql_errno()." - Error en el query $sql10 - ".mysql_error() ) );
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
		// $sql = "insert into ".$wbasedato."_000164 (medico,fecha_data,hora_data, Logusu, Logdes, Loghis, Loging, Logdoc, Logest,seguridad)
		// values ('".$wbasedato."','".utf8_decode($fecha)."','".utf8_decode($hora)."','".utf8_decode($key)."','".utf8_decode($des)."','".utf8_decode($historia)."','".utf8_decode($ingreso)."','".utf8_decode($documento)."','on','C-root')";

		// $res = mysql_query( $sql, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error insertando en la tabla de log admisiones ".$wbasedato." 164 ".mysql_errno()." - Error en el query $sql - ".mysql_error() ) );
		// if (!$res)
		// {
			// $data[ 'error' ] = 1; //sale el mensaje de error
		// }
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

			$res = mysql_query( $sql, $conex ) or die( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla ".$wbasedato."_000024 para guardar en movhos_000016 ".mysql_errno()." - Error en el query $sql - ".mysql_error() ) );

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

				$res6 = mysql_query( $sql6, $conex ) or die( $data[ 'mensaje' ] = utf8_encode( "Error actualizando la tabla movhos_000016 ".mysql_errno()." - Error en el query $sql6 - ".mysql_error() ) );
				if (!$res6)
				{
					$data[ 'error' ] = 1; //sale el mensaje de error
				}
			}
			else //se inserta el registro tabla movhos_000016
			{
				$sql7 = "insert into ".$aplicacion."_000016 (medico           ,fecha_data               ,hora_data               , Inghis                     , Inging                    ,               Ingres                ,             Ingnre                  ,           Ingtip       ,           Ingtel, 			Ingdir                     , seguridad)
													 values ('".$aplicacion."','".utf8_decode($fecha)."','".utf8_decode($hora)."','".utf8_decode($historia)."','".utf8_decode($ingreso)."','".utf8_decode($empresa)."','".utf8_decode($nombre_empresa)."','".utf8_decode($tipo)."','".utf8_decode($tel_empresa)."','".utf8_decode($dir_empresa)."','C-".utf8_decode($key)."')";

				$res7 = mysql_query( $sql7, $conex ) or die( $data[ 'mensaje' ] = utf8_encode( "Error insertando en la tabla movhos_000016 ".mysql_errno()." - Error en el query $sql7 - ".mysql_error() ) );
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

				$res9 = mysql_query( $sql9, $conex ) or die( $data[ 'mensaje' ] = utf8_encode( "Error actualizando la tabla de origen historia del paciente ".mysql_errno()." - Error en el query $sql9 - ".mysql_error() ) );
				if (!$res9)
				{
					$data[ 'error' ] = 1; //sale el mensaje de error
				}
			}
			else //si es registro nuevo en la tabla movhos_000018
			{
				$sql10 = "insert into ".$aplicacion."_000018 (       medico    ,    fecha_data           ,hora_data               , Ubihis                     , Ubiing                    , Ubisac                         , Ubialp, Ubiald, Ubiptr			  ,     seguridad     )
													  values ('".$aplicacion."','".utf8_decode($fecha)."','".utf8_decode($hora)."','".utf8_decode($historia)."','".utf8_decode($ingreso)."','".utf8_decode($servicioIng2)."', 'off' , 'off' , '".$campo_ccocir."' ,'C-".$aplicacion."')";

				$res10 = mysql_query( $sql10, $conex ) or die( $data[ 'mensaje' ] = utf8_encode( "Error insertando en la tabla movhos_000018 ".mysql_errno()." - Error en el query $sql10 - ".mysql_error() ) );
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
			$rsup36    = mysql_query( $queryUp36,$conex ) or die( mysql_error() );
			$regAfectados36 = mysql_affected_rows();

			$queryUp152 = "UPDATE {$wbasedatoHce}_000152
							  SET movhis = '$historia',
							      moving = '$ingreso'
							WHERE movhis = '$historiaTemporal'
							  AND moving = '1'";
			$rsup152    = mysql_query( $queryUp152,$conex ) or die( mysql_error() );
			$regAfectados152 = mysql_affected_rows();

			if( $regAfectados36*1 > 0 and $regAfectados152*1 > 0){
				$queryUp204 = "UPDATE  {$wbasedatoMov}_000204
								   SET ahtahc = 'on',
								   	   ahthis = '$historia',
								   	   ahting = '$ingreso',
								   	   ahttdo = '$tipodoc',
								   	   ahtdoc = '$documento'
								 WHERE ahthte = '$historiaTemporal'
								   AND ahtest = 'on'
								   AND ahtahc != 'on'";
				$rsup204    = mysql_query( $queryUp204,$conex ) or die( mysql_error() );
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
		$rsup36    = mysql_query( $queryUp36,$conex ) or die( mysql_error() );
		$regAfectados3675 = mysql_affected_rows();

		$queryUp75 = "UPDATE {$wbasedatoHce}_000075
						 SET movhis = '$historia',
						     moving = '$ingreso'
					   WHERE movhis = '$historiaTemporal'
						 AND moving = '1'";
		$rsup152    = mysql_query( $queryUp75,$conex ) or die( mysql_error() );
		$regAfectados75 = mysql_affected_rows();

		$queryTcx11 = " UPDATE {$wbasedatotcx}_000011
						   SET turhis = '{$historia}',
						       turing = '{$ingreso}'
						 WHERE turtdo = '{$tipodoc}'
						   AND turdoc = '{$documento}'
						   AND turtur = '{$turnoCirugia}'";
		$rstrCir    = mysql_query( $queryTcx11, $conex ) or die( mysql_error() );

		$queryUp204 = "UPDATE {$wbasedatoMov}_000204
						   SET ahtahc = 'on',
						   	   ahthis = '$historia',
						   	   ahting = '$ingreso',
						   	   ahttdo = '$tipodoc',
						   	   ahtdoc = '$documento',
						   	   ahtccd = '{$servicioIng1[1]}'
						 WHERE id = '{$rowPreAn['id']}'";
		$rsup204    = mysql_query( $queryUp204,$conex ) or die( mysql_error() );
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

				//se actualiza el registro de la tabla movhos_000018
				// $sql9 =  " update ".$aplicacionHce."_000022 set
				  // Mtrhis='".utf8_decode($historia)."',
				  // Mtring='".utf8_decode($ingreso)."',
				  // Mtrest = 'on',
				  // Mtrcur = 'off',
				  // Mtrcci = '".$servicioIng2."',
				  // Mtrtur = '".$turno."'
				  // where Mtrhis='".utf8_decode($historia)."'
				  // and Mtring='".utf8_decode($ingreso)."'";

				$sql9 =  " update ".$aplicacionHce."_000022 set
				  Mtrhis='".utf8_decode($historia)."',
				  Mtring='".utf8_decode($ingreso)."',
				  Mtrest = 'on',
				  Mtrcur = 'off',
				  Mtrcci = '".$servicioIng2."'
				  where Mtrhis='".utf8_decode($historia)."'
				  and Mtring='".utf8_decode($ingreso)."'";

				$res9 = mysql_query( $sql9, $conex ) or die( $data[ 'mensaje' ] = utf8_encode( "Error actualizando la tabla hce_000022 ".mysql_errno()." - Error en el query $sql9 - ".mysql_error() ) );
				if (!$res9)
				{
					$data[ 'error' ] = 1; //sale el mensaje de error
				}
			}
			else //si es registro nuevo en la tabla movhos_000018
			{
				// --> 	Obtener Homologacion de especialidades tratantes HCE.
				//		Jerson Trujillo 2016-06-17
				$arrCamposConTriage = array();
				$arrHomoConductas 	= array();
				$sqlHomoCon = "
				SELECT Hctcch, Hctcom
				  FROM ".$wbasedatoMov."_000205
				 WHERE Hctpin = 'on'
				   AND Hctest = 'on'
				";
				$resHomoCon = mysql_query($sqlHomoCon, $conex) or ( $data[ 'mensaje' ] = utf8_encode( "Error en el query sqlHomoCon:$sqlHomoCon - ".mysql_error() ) );
				while($rowHomoCon = mysql_fetch_array($resHomoCon))
				{
					$arrCamposConTriage[] 						= $rowHomoCon['Hctcch'];
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

				$campoConducta		= trim(consultarAliasPorAplicacion($conex, $wemp_pmla, "CampoPlanConductaDeTriageHce"));
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
				$sql10 = "insert into ".$aplicacionHce."_000022 (   medico            ,fecha_data               ,hora_data               , Mtrhis                     , Mtring                    , Mtrest, Mtrfam,					Mtrham,					Mtrtra, 		Mtreme, 					Mtretr,						Mtrtri, 			Mtrcur,        Mtrcci      ,	Mtrtur,        Mtrfia,            Mtrhia,		seguridad)
														 values ('".$aplicacionHce."' ,'".utf8_decode($fecha)."','".utf8_decode($hora)."','".utf8_decode($historia)."','".utf8_decode($ingreso)."',  'on' , '".$fechaAsigEspec."',	'".$horaAsigEspec."',	'".$Mtrtra."' , '".$especialidadTriage."',	'".$especialidadTriage."',	'".$nivelTriage."',	'off' , '".$servicioIng2."',	'".$turno."', '".$wfiniAdm."',    '".$whiniAdm."', 	'C-".$key."')";

				$res10 = mysql_query( $sql10, $conex ) or die( $data[ 'mensaje' ] = utf8_encode( "Error insertando en la tabla hce_000022 ".mysql_errno()." - Error en el query $sql10 - ".mysql_error() ) );
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

					$sqlActTurnoAdm = "
					UPDATE ".$wbasedatoMov."_000178
					   SET Atuadm = 'on',
						   Atufad = '".date('Y-m-d')."',
						   Atuhad = '".date("H:i:s")."'
					 WHERE Atutur = '".$turno."'
					";
					$resActTurnoAdm = mysql_query($sqlActTurnoAdm, $conex) or die( $data[ 'mensaje' ] = utf8_encode( "Error actualizando el turno - Error en el query sqlActTurnoAdm:$sqlActTurnoAdm - ".mysql_error() ) );
					if(!$resActTurnoAdm)
						$data['error'] = 1;

					// --> Registrar en el log el movimiento
					$sqlRegMov = "
					INSERT INTO ".$wbasedatoMov."_000179 (Medico,				Fecha_data,				Hora_data,				Logtur,			Logacc,				Logusu,			Seguridad,				id)
												   VALUES('".$wbasedatoMov."',	'".date('Y-m-d')."',	'".date("H:i:s")."',	'".$turno."', 	'turnoAdmitido',	'".$usuario."', 'C-".$wbasedatoMov."',	NULL)
					";
					$resRegMov = mysql_query($sqlRegMov, $conex) or die($data['mensaje'] = utf8_encode( "Error actualizando el turno - Error en el query sqlRegMov:$sqlRegMov - ".mysql_error()));
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

			$rscit = mysql_query( $queryCit, $conex ) or die( mysql_error() );

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
			$rscit = mysql_query( $queryCit, $conex ) or die( mysql_error() );
		}
	}

	/*Fin insercion o actualizacion en la tabla hce_000022(medicos tratantes por historia)*/
	global $key;
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
	$ccoIngresoAyuda = verificarCcoIngresoAyuda( $servicioIng );
	if( $ccoIngresoAyuda && $modoConsulta != "true" ){//--> 2016-12-27 inserts de alta automática para ayudas diagnósticas.

		// Si el paciente a estado antes en el servicio para el mismo ingreso, traigo cuantas veces para sumarle una
		$q32 = " SELECT COUNT(*)
		         FROM {$aplMovhos}_000032
		        WHERE Historia_clinica = '{$historia}'
		          AND Num_ingreso      = '{$ingreso}'
		          AND Servicio         = '{$servicioIng}'";
		$err32 = mysql_query($q32, $conex) or die (mysql_errno().$q32." - ".mysql_error());
		$row32 = mysql_fetch_array($err32);

		$wingser = $row32[0] + 1; //Sumo un ingreso a lo que traigo el query

		if( $wingser ==1 ){
			$q32 = " INSERT INTO {$aplMovhos}_000032 (   Medico       ,   Fecha_data,   Hora_data,   Historia_clinica ,   Num_ingreso,   Servicio       ,   Num_ing_Serv,   Fecha_ing ,   Hora_ing ,   Procedencia    , Seguridad     )
			           VALUES ( '{$aplMovhos}','".date('Y-m-d')."','".date("H:i:s")."','".$historia."'         ,'".$ingreso."'   ,'".$servicioIng. "','" . $wingser . "' ,'".$_POST[ 'ing_feitxtFecIng' ]."','".$_POST[ 'ing_hintxtHorIng' ]."','".$servicioIng. "', 'C-" . $key . "')";
			$err = mysql_query($q32, $conex) or die (mysql_errno() . $q32 . " - " . mysql_error());
		}


		$q33 = " SELECT *
					FROM {$aplMovhos}_000033
				   WHERE Historia_clinica = '{$historia}'
					 AND Num_ingreso = '{$ingreso}'
					 AND Tipo_egre_serv = 'ALTA'
					 AND Servicio = '{$servicioIng}'";
		$res33 = mysql_query($q33, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q33 . " - " . mysql_error());
		$num33 = mysql_num_rows($res33);

		if( $num33 == 0 ){
			$q33 = "	INSERT INTO {$aplMovhos}_000033
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

		if( $modoConsulta == "false" || ( $modoConsulta == "true" && $estadoPaciente == true ) ){
			//$ping_unix = ping_unix();
			//-->2018-08-28 debido a que el orden de grabación cambió, entonces reasignar los valores de historia e ingreso para unix
			while( $reintentar && $intentos*1 <= $intentosIngreso*1 && $ping_unix){
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
		if( $modoConsulta == "false" && $ping_unix ){
			//--> si llegó hasta acá es porque guardó en unix, entonces se actualiza el campo ingunx en la tabla 101 de ingresos
			$query = "UPDATE {$wbasedato}_000101
						 SET ingunx = 'on'
					   WHERE inghis = '{$historia}'
					     AND ingnin = '{$ingreso}'";
			$rsunx = mysql_query( $query, $conex ) or die( mysql_error() );
			$data[ "mensaje" ] = utf8_encode("Se registró correctamente " );
		}else{
			$data[ "mensaje" ] = utf8_encode("Se actualizo correctamente" );
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
	unset($_POST['pac_tdoselTipoDoc']);
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
	$sql = "select Pachis,Pactdo,Pacdoc,Pactat,Pacap1,Pacap2,Pacno1,Pacno2,Pacfna,Pacsex,Pacest,Pacdir,Pactel,
            Paciu,Pacbar,Pacdep,Paczon,Pactus,Pacofi,Paccea,Pacnoa,Pactea,Pacdia,Pacpaa,Pacact,Paccru,Pacnru,
            Pactru,Pacdru,Pacpru,Paccor,Pactam,Pacpan,Pacpet,Pacded,Pactrh,Pacpah,Pacdeh,Pacmuh,Pacmov,Pacned,
            Pacemp,Pactem,Paceem,Pactaf,Pacrem,Pacire,Paccac,Pactda,Pacddr,Pacdre,Pacmre,Pacmor,Paccre,a.Fecha_data, Pacdle, Pactle, Pacaud, Paczog
			from ".$wbasedato."_000100 a, ".$wbasedato."_000101 b
			where Pachis !='0'
			and pachis = inghis
			";
	$sql .= $where;
	$sql .=" Group by  Pachis  ";
	$sql .=" Order by  pacdoc, inghis*1, ingnin*1 DESC ";

	/*$data['mensaje']=$sql;
	$data['error']=1;
	echo json_encode($data);
	exit;*/

	/*$data['mensaje']= $sql ;
	$data[ 'error' ] = 1;
	echo json_encode($data);
	return;*/

	$res = mysql_query( $sql, $conex ) or die( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla ".$wbasedato."000100 ".mysql_errno()." - Error en el query $sql - ".mysql_error() ) );
	if ($res)
	{
		$num=mysql_num_rows($res);
		$data['numRegistrosPac']=$num;
		if ($num>0)
		{
			/*se inicializa la i en el for de la consulta de la 100 pero se incrementa en el for de la
			consulta de la 101
			*/
			for( $i = 0, $j = 0;$rows=mysql_fetch_array($res, MYSQL_ASSOC ); $j++ )
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


								$sqlpro = "select *
											from ".$wbasedato."_000205
											where Reshis = '".$rows['Pachis']."'
											and Resest = 'on'
											and Resdes = 'off'";
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
											/*if( $rowspro['Resord'] == "1" ){ //POR NORMA, si tiene SOAT siempre es el primer responsable
												$sqls = " SELECT Empcod
															FROM ".$wbasedato."_000024
														   WHERE Empcod = '".$rowspro['Resnit']."'
															 AND Emptem = '".$tipoSOAT."'";
												$ress = mysql_query( $sqls, $conex );
												$nums = mysql_num_rows( $ress );

												if( $nums > 0 ){//Quiere decir que SI ES TIPO SOAT
													$esSoat = true;
												}
											}*/
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
		$rs = mysql_query( $query, $conex ) or die( mysql_error() );


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

	$rs = mysql_query( $query, $conex ) or die( mysql_error() );
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

function insertLog( $wemp_pmla, $user_session, $accion, $tabla, $err, $descripcion, $identificacion, $sql_error = "",  $plan="", $servicio="", $wsoporte )
{
    global $conex;
	$wfachos      = consultarAliasPorAplicacion($conex, $wemp_pmla, "Facturacion hospitalaria");
	$descripcion = str_replace("'",'"',$descripcion);
    $sql_error = ereg_replace('([ ]+)',' ',$sql_error);

    $insert = " INSERT INTO ".$wfachos."_000021
                    (Medico, Fecha_data, Hora_data, logori, Logcdu, Logemp, Logpln, Logser, Logsop, Logacc, Logtab, Logerr, Logsqe, Logdes, Loguse, Logest, Seguridad)
                VALUES
                    ('".$wfachos."','".date("Y-m-d")."','".date("H:i:s")."', 'Admision', '".utf8_decode($identificacion)."', '', '".$plan."', '".$servicio."', '".$wsoporte."', '".utf8_decode($accion)."','".$tabla."','".$err."', '".$sql_error."','".$descripcion."','".$user_session."','on','C-".$user_session."')";

    $res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - en el query (Insertar En Log): " . $insert . " - " . mysql_error());

	return $insert;
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
					$rs = mysql_query( $query, $conex ) or die( mysql_error() );

					$query = "INSERT
								INTO {$wfachos}_000016 (Medico, Fecha_data, Hora_data, relhis, reling, relres, relest, seguridad)
							  VALUES ('fachos','{$hoy}','{$hora}','{$whis}',{$wing},'{$responsableactual}','on','{$usuario}')";
					$rs    = mysql_query( $query, $conex ) or die( mysql_error());



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
				// if(count($array_planes_empresa)== 1 and $numeroflujos ==1)
				// {

					// $proceso = $ultimoflujo;
					// $insert = "INSERT
								 // INTO {$wfachos}_000011 (Medico	, Fecha_data, Hora_data, lenhis		, lening	, lenemp					, lenpro			, lenrac			, lenfeu	, lenhou	, lenest	, seguridad)
							   // VALUES ( 					'fachos', '{$hoy}'	, '{$hora}', '{$whis}'	, '{$wing}' ,	'{$ultimorelacion}'		, '{$proceso}'	, '{$nivelUsuario}'	, '{$hoy}'	, '{$hora}'	, 'on'		,'{$usuario}' )";

					// $resinsert = mysql_query($insert, $conex) or die("<b>ERROR EN QUERY(sqlDet):</b><br>".mysql_error());
					// $html.= "  inserte";

					// ----------------------------------------------------------------------------INSERCIÓN INICIAL EN TABLA DE MOVIMIENTO--------------------------------------------------------------//
					// $query = "INSERT
								// INTO {$wfachos}_000020 (Medico, Fecha_data, Hora_data, lmohis, lmoing, lmoori, lmodes, lmoest, seguridad)
							  // VALUES ('fachos','{$hoy}','{$hora}','{$whis}','{$wing}','','{$nivelUsuario}','on','{$usuario}')";
					// $rs = mysql_query( $query, $conex );

					// $query = "INSERT
								// INTO {$wfachos}_000016 (Medico, Fecha_data, Hora_data, relhis, reling, relres, relest, seguridad)
							  // VALUES ('fachos','{$hoy}','{$hora}','{$whis}',{$wing},'{$responsableactual}','on','{$usuario}')";
					// $rs    = mysql_query( $query, $conex );

					// $respuesta['html'] =  "abrirModal";
					// $respuesta['contenidoDiv'] = "se inserto automaticamente";

				// }


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
			/*$hora1 = str_replace( ":", "_", $hora);
			$hoy1  = str_replace( "-", "_", $hoy);
			$tmp   = "tmpSoportes{$hoy1}_{$hora1}";
			$qaux  = "DROP TABLE IF EXISTS $tmp";
			$resdr = mysql_query($qaux,$conex) or die (mysql_errno().":".mysql_error());
			$query = "CREATE TEMPORARY TABLE IF NOT EXISTS {$tmp}
							(INDEX idx(soporte, empPlan))
					  SELECT a.id numLista, delsop soporte, delemp empPlan, delfor formato, delobs observacion, delest estado, delres responsable, delser servicio
						FROM {$wfachos}_000011 a, {$wfachos}_000012
					   WHERE lenhis = '{$whis}'
						 AND lening = '{$wing}'
						 {$condicionNivelUsuario}
						 AND delhis = lenhis
						 AND deling = lening
						 AND lenest = 'on'";
			$resdr = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());

			$query = "SELECT sopcod codigo, sopnom nombre, Pememp codigoEmpresa, Pempln codigoPlan, a.*, soptip tipo, soptif formatoDefecto
						FROM {$wfachos}_000006, {$wfachos}_000009 b, {$wfachos}_000010, $tmp a
					   WHERE b.pemcod = empPlan
						 AND Sopcod = soporte
					   GROUP BY codigoEmpresa, codigoPlan, servicio, soporte
					   ORDER BY soporte";*/

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
								$rsAux = mysql_query( $query, $conex ) or die( mysql_error() );
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
			$res = mysql_query($sql,$conex) or die( $data[ 'mensaje' ] = utf8_encode( "Error actualizando la tabla ".$wbasedato."000163 " .mysql_errno()." - Error en el query $sql - ".mysql_error() ) );
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

			$res = mysql_query( $sql, $conex ) or die( $data[ 'mensaje' ] = utf8_encode( "Error insertando la tabla ".$wbasedato."000163 ".mysql_errno()." - Error en el query $sql - ".mysql_error() ) );
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
	global $wbasedato;
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

					$resEnc = mysql_query( $sqlInsert, $conex ) or die( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() ) );
					if( mysql_affected_rows() > 0 )
					{	//si inserto la 101
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
					$res1 = mysql_query( $sqlUpdate, $conex ) or die( $data[ 'error' ] = utf8_encode( mysql_errno()." - Error en el query $sqlUpdate - ".mysql_error() ) );

					if( $res1 )
					{
						if( mysql_affected_rows() > 0 ){
							$data[ "mensaje" ] = utf8_encode( "Se actualizo correctamente" );
							$modoConsulta = true;
						}
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
						$resEnc = mysql_query( $sqlInsert, $conex ) or die( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query admision $sqlInsert - ".mysql_error() ) );

						if( mysql_affected_rows() > 0 ){
							$data[ "mensaje" ] = utf8_encode( "Se registró correctamente " );
						}else{
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
						$res2 = mysql_query( $sqlUpdate, $conex ) or die( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query $sqlUpdate - ".mysql_error() ) );

						if( $res2 )
						{
							if( mysql_affected_rows() > 0 )
							{
								$data[ "mensaje" ] = utf8_encode( "Se actualizo correctamente" );
								$modoConsulta = true;
							}
							else
							{
								$data[ "mensaje" ] = utf8_encode( "No se actualizo porque no se registraron cambios" );
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
						$resEnc = mysql_query( $sqlInsert, $conex ) or die( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error grabando en la tabla ".$wbasedato."_000216 - ".mysql_error() ) );

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
							$resDel = mysql_query( $sqlDel, $conex ) or die( $data[ 'error' ] = utf8_encode( mysql_errno()." - Error en el query $sqlDel - ".mysql_error() ) );
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
									$resEnc = mysql_query( $sqlInsert, $conex ) or die( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error grabando en la tabla ".$wbasedato."_000217 - ".mysql_error() ) );
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
							$resEnc = mysql_query( $sqlInsert, $conex ) or die( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error grabando en la tabla ".$wbasedato."_000215 - ".mysql_error() ) );

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

					$res1 = mysql_query( $sqlInsert, $conex ) or die( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() ) );

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
	$data = array('error'=>0,'mensaje'=>'','html'=>'','numRegistrosIng'=>'', 'ultimoIngreso'=>'','numRegistrosPac'=>'');

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
	$sql = "select Pacfec, Pactdo,Pacdoc,Pactat,Pacap1,Pacap2,Pacno1,Pacno2,Pacfna,Pacsex,Pacest,Pacdir,Pactel,
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
				echo "edb->".$sql1;
				$res1 = mysql_query( $sql1, $conex ) or die ( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla ".$wbasedato."000101 ".mysql_errno()." - Error en el query $sql1 - ".mysql_error() ) );
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

	$res = mysql_query( $sql, $conex ) or die( $data[ 'mensaje' ] = mysql_errno()." - Error en el query $sql - ".mysql_error() );

	if( $res ){
		if( mysql_affected_rows() > 0 ){
			$data[ 'mensaje' ] = 'Se ha cancelado la PREADMISION correctamente';
			//Grabo el log correspondiente
			$data1 = logAdmsiones( "Preadmision anulada", '', '', $doc );

			if( $data1[ 'error' ] == 1 ){
				$data = $data1;
			}
		}
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
		$ccoAyuda = verificarCcoIngresoAyuda( $row[0] );

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

	$resPac = mysql_query( $sqlPac, $conex ) or die( $data[ 'mensaje' ] = mysql_errno()." - Error en el query $sqlPac - ".mysql_error() );

	if( $resPac )
	{
		if( mysql_affected_rows() > 0 )
		{
			//Borro el último ingreso
			$sql = "DELETE FROM
						".$wbasedato."_000101
					WHERE
						inghis = '".$historia."'
						AND ingnin = '".$ingreso."'
					";

			$resIng = mysql_query( $sql, $conex ) or ( $data[ 'mensaje' ] = mysql_errno()." - Error en el query $sql - ".mysql_error() );

			if( $resIng )
			{
				if( mysql_affected_rows() > 0 )
				{
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

					$resPread = mysql_query( $sqlPre, $conex ) or die( $data[ 'mensaje' ] = mysql_errno()." - Error en el query $sqlPre - ".mysql_error() );

					if( !$resRoot )
					{
						$data1[ 'error' ] = 1;
					}

					//Borro los datos de informacion del paciente (root_000037)
					$resRoot = mysql_query( $sqlRoot, $conex ) or die( $data[ 'mensaje' ] = mysql_errno()." - Error en el query $sqlRoot - ".mysql_error() );

					if( $resRoot )
					{
						if( mysql_affected_rows() > 0 )
						{
						}
					}
					else{
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

						if( $resMov )
						{
							if( mysql_affected_rows() > 0 )
							{
							}
						}
						else{
							$data1[ 'error' ] = 1;
						}

						//libero la habitación de ser necesario. 2015-10-15
						$sqlhab = "UPDATE {$aplMovhos}_000020
									  SET habhis = '',
										  habing = '',
										  habdis = 'on'
									WHERE habhis = '{$historia}'
					   				  AND habing = '{$ingreso}'";

						$rshab = mysql_query( $sqlhab, $conex ) or die( $data[ 'mensaje' ] = mysql_errno()." - Error en el query $sqlhab - ".mysql_error() );
						/*if( $liberarHabitacion ){
							$sqlhab = "UPDATE {$aplMovhos}_000020
										  SET
											  habhis = '',
											  habing = '',
											  habdis = 'on'
										WHERE
											  habhis = '{$historia}'
					   					  AND habing = '{$ingreso}'";

							$rshab = mysql_query( $sqlhab, $conex ) or ( $data[ 'mensaje' ] = mysql_errno()." - Error en el query $sqlhab - ".mysql_error() );
						}*/
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
							$rsctur  = mysql_query( $sqlCtur, $conex ) or die( mysql_error() );
						}
						//Borro los datos de movimiento hospitalario
						$sqlHce = "DELETE ".$aplHce."_000022
									 FROM ".$aplHce."_000022
								    WHERE mtrhis = '".$historia."'
									  AND mtring = '".$ingreso."'";

						$resHce = mysql_query( $sqlHce, $conex ) or ( $data[ 'mensaje' ] = mysql_errno()." - Error en el query $sqlHce - ".mysql_error() );

						if( $resHce )
						{
							if( mysql_affected_rows() > 0 )
							{
								$user2 = explode("-",$user);
								( isset($user2[1]) )? $user2 = $user2[1] : $user2 = $user2[0];
								//Guardo LOG de borrado en tabla root_000037 por clave duplicada
									$q = "  INSERT INTO log_agenda (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera)
									  			            VALUES ('".date('Y-m-d')."', '".date("H:i:s")."', '".$historia."', '".$ingreso."', 'Borrado tabla hce_000022', '".$user2."', 'anulacion desde admisiones ".$historia."-".$ingreso."')";
									$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
							}
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

					if( $resIng )
					{
						if( mysql_affected_rows() > 0 )
						{
						}
					}
					else{
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

					if( $resIng )
					{
						if( mysql_affected_rows() > 0 )
						{
						}
					}
					else{
						$data1[ 'error' ] = 1;
					}

					//Si ya borro el registro de ingreso procedo a eliminar encabezado de eventos catastróficos
					//Borro el último ingreso
					$sqlEv = "DELETE ".$wbasedato."_000149 , ".$wbasedato."_000150  FROM
								".$wbasedato."_000149 , ".$wbasedato."_000150
							WHERE Evnhis = '".$historia."'
								AND Evning = '".$ingreso."'
								AND Devcod 	= Evncod
								AND Evnest  = 'on'
							";

					$resEv = mysql_query( $sqlEv, $conex ) or ( $data[ 'mensaje' ] = mysql_errno()." - Error en el query $sqlEv - ".mysql_error() );

					if( $resEv )
					{
						if( mysql_affected_rows() > 0 )
						{
						}
					}
					else{
						$data1[ 'error' ] = 1;
					}
				}
			}
		}
	}
	else{

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

	$datosEnc = crearArrayDatos( $wbasedato, "pac", "pac_", 3 );
	// var_dump( $datosEnc );
	unset( $datosEnc[ 'Medico' ] );
	unset( $datosEnc[ 'Hora_data' ] );
	unset( $datosEnc[ 'Fecha_data' ] );
	unset( $datosEnc[ 'Seguridad' ] );
	$where = crearStringWhere( $datosEnc );

	/*$datosEnc = crearArrayDatos( $wbasedato, "ing", "ing_", 3 );
	unset( $datosEnc[ 'Medico' ] );
	unset( $datosEnc[ 'Hora_data' ] );
	unset( $datosEnc[ 'Fecha_data' ] );
	unset( $datosEnc[ 'Seguridad' ] );
	$where .= crearStringWhere( $datosEnc );*/

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
	$sql = "select Pachis,Pactdo,Pacdoc,Pactat,Pacap1,Pacap2,Pacno1,Pacno2,Pacfna,Pacsex,Pacest,Pacdir,Pactel,
            Paciu,Pacbar,Pacdep,Paczon,Pactus,Pacofi,Paccea,Pacnoa,Pactea,Pacdia,Pacpaa,Pacact,Paccru,Pacnru,
            Pactru,Pacdru,Pacpru,Paccor,Pactam,Pacpan,Pacpet,Pacded,Pactrh,Pacpah,Pacdeh,Pacmuh,Pacmov,Pacned,
            Pacemp,Pactem,Paceem,Pactaf,Pacrem,Pacire,Paccac,Pactda,Pacddr,Pacdre,Pacmre,Pacmor,Paccre,a.Fecha_data,Pacdle, Pactle, Pacaud, Paczog
			from ".$wbasedato."_000100 a
			where Pachis !='0'
			";
	$sql .= $where;
	$sql .=" Group by  Pachis  ";
	$sql .=" Order by  Pacdoc  ";

	// echo "\n$sql\n\n";


	$res = mysql_query( $sql, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla ".$wbasedato."000100 ".mysql_errno()." - Error en el query $sql - ".mysql_error() ) );
	if ($res)
	{

		$num=mysql_num_rows($res);

		/*En ocasiones, el tipo y numero de documento de cliame 100 no coincide con root 36,
		Esto debido a que hacen cambio de documento en UNIX y no en MATRIX, y el kron de pacientes refleja ese cambio en root 36 y no en cliame 100
		El objetivo es, que si no existe en cliame 100 que se busque en root 36 con ese tipo y numero de documento, si existe, coger ese numero de historia
		y actualizar en cliame 100 el documento del registro que tenga ese mismo # de historia
		*/
		if( $num == 0 ){
			$sql5 = " SELECT Orihis
						FROM root_000037
					   WHERE Oritid='".utf8_decode($datosEnc['pactdo'])."'
						 AND Oriced='".utf8_decode($datosEnc['pacdoc'])."'
						 AND Oriori='".$wemp_pmla."'";
			$res5 = mysql_query( $sql5, $conex );
			if ($res5)
			{
				$num5=mysql_num_rows($res5);
				if ($num5>0)
				{
					$rowsee=mysql_fetch_array($res5);
					$query =  "UPDATE ".$wbasedato."_000100 SET Pactdo = '".$datosEnc['pactdo']."', Pacdoc = '".$datosEnc['pacdoc']."' WHERE Pachis='".$rowsee[0]."' limit 1";
					$err1 = mysql_query($query,$conex) or die(mysql_error());
				}
				//
				$sql = "select Pachis,Pactdo,Pacdoc,Pactat,Pacap1,Pacap2,Pacno1,Pacno2,Pacfna,Pacsex,Pacest,Pacdir,Pactel,
						Paciu,Pacbar,Pacdep,Paczon,Pactus,Pacofi,Paccea,Pacnoa,Pactea,Pacdia,Pacpaa,Pacact,Paccru,Pacnru,
						Pactru,Pacdru,Pacpru,Paccor,Pactam,Pacpan,Pacpet,Pacded,Pactrh,Pacpah,Pacdeh,Pacmuh,Pacmov,Pacned,
						Pacemp,Pactem,Paceem,Pactaf,Pacrem,Pacire,Paccac,Pactda,Pacddr,Pacdre,Pacmre,Pacmor,Paccre,a.Fecha_data, Pacdle, Pactle, Pacaud, Paczog
						from ".$wbasedato."_000100 a
						where Pachis !='0'
						";
				$sql .= $where;
				$sql .=" Group by  Pachis  ";
				$sql .=" Order by  Pacdoc  ";

				// echo "\n$sql\n\n";


				$res = mysql_query( $sql, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla ".$wbasedato."000100 ".mysql_errno()." - Error en el query $sql - ".mysql_error() ) );
				if ($res)
				{
					$num=mysql_num_rows($res);
				}
			}
		}

		$data['numRegistrosPac']=$num;
		if ($num>0)
		{
			/*se inicializa la i en el for de la consulta de la 100 pero se incrementa en el for de la
			consulta de la 101
			*/
			for( $i = 0, $j = 0;$rows=mysql_fetch_array($res, MYSQL_ASSOC ); $j++ )
			{ //solo se puede buscar por el nombre del campo


				// if ($rows['Fecha_data'] < '2013-07-03')
				// {

					// $rows['Pacest']=consultarCodigoAnteriorMatrix( '25', $rows['Pacest'] );
					// $rows['Pactat']=consultarCodigoAnteriorMatrix( '24', $rows['Pactat'] );
				// }
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

				$data[ 'infoing' ][$i] = $data[ 'infopac' ];

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
	/*$sql="select Topmin, Toptpr
		  from ".$wbasedato."_000194
		  where Topest = 'on'";*/
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
			   FROM cliame_000236
			  INNER JOIN
			        cliame_000237 on ( clrdti = clrtid and clrdce = clrced )
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
	$rs = mysql_query( $query, $conex ) or die(mysql_error());
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

<!--<link type="text/css" href="../../../include/root/jquery.autocomplete.css" rel="stylesheet" /> <!-- Autocomplete -->
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
<script src="./funcionesAdmisiones.js" type="text/javascript"></script>

<script src="../../../include/root/toJson.js"></script>
<script src="../../../include/root/jquery.validate.js"></script>
<script src='../../../include/root/jquery.quicksearch.js' type='text/javascript'></script>
<script type="text/javascript" src="../../ips/procesos/soportes.js?v=<?=md5_file('../../ips/procesos/soportes.js');?>"></script>
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

<script type="text/javascript">

var conFocus = "";	//Indica que elemento tiene focus, esto para saber cuando se esta usando el autocompletar que elemento tiene el foco para completar los elementos de busqueda (paramsExtras)
var hayCambios = false;	//Indica si se hizo cambios al escribir en el formulario, esto para que al dar click cosbre regresar a la agenda o iniciar pregunte si desea continuar
var consultaEvento = false;

var conteoInputs = 0;
var llenadoAutomatico = false;

var numeroTurnoTemporal;

$(document).ready(function() {
	codRespGlobal="";
	idCodResp="";

	//-------------------------------------------------------
	// --> Esto es para las funcionalidades de los turnos
	//-------------------------------------------------------
	setTimeout( function(){
		// --> Activar el buscador de texto, para los turnos
		$('#buscardorTurno').quicksearch('#tablaListaTurnos .find');
		// --> Tooltip en la lista de turnos
		$('[tooltip=si]').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
		// --> Si existe un turno que ya haya sido llamado por este usuario, inhabilito los demas
		if($("#turnoLlamadoPorEsteUsuario").val() != '')
		{
			var idTurno = $("#turnoLlamadoPorEsteUsuario").val();
			$(".botonLlamarPaciente").hide();
			$(".botonColgarPaciente").hide();
			$("#imgLlamar"+idTurno).hide();
			$("#imgLlamar"+idTurno).next().show();
			$("#imgLlamar"+idTurno).next().next().show();
			$("#botonAdmitir"+idTurno).show();
			$("#botonCancelar"+idTurno).show();
			$("#trTurno_"+idTurno).attr("classAnterior", $("#trTurno_"+idTurno).attr("class"));
			$("#trTurno_"+idTurno).attr("class", "fondoAmarillo");
		}
		// --> Solo se puede llamar al primer turno de la lista, para evitar que llamen en desorden
		else
		   activarPrimerTurno();

		// --> Llamado automatico, para que la lista de turnos se este actualizando
		// --> 2018-05-29: El llamado automático se hará mientras no se esté haciendo una admisión, Jerson Trujillo.
		if($("#permitirVerListaTurnos").val())
		{
			setInterval(function(){
				if(!$("#radAdmision" ).is(":checked"))
				{
					listarPacientesConTurno();
				}
			}, 30000);
		}

	}, 500 );
	//-------------------------------------------------------
	// --> Fin funcionalidades de los turnos
	//-------------------------------------------------------

    $('#tabla_responsables_1_2').hide(); //se oculta
	$('#tr_titulo_tercer_resp').hide(); //se oculta
	/************************************************************************************************
	 * Septiembre 02 de 2013
	 * Creo clon de los options de ORIGEN DE LA ATENCIÓN para poder quitar y agregarlos cada vez que
	 * se requiera. Esto se hace al seleccionar el radio button de ADMISION o PREADMISION.
	 ************************************************************************************************/
	optOrigenAtencion = $( "option[value=02],option[value=06]", $( "#ing_caiselOriAte" ) ).clone();
	optServicioIngreso = $( "option[value^=4]", $( "#ing_seisel_serv_ing" ) );
	if( optServicioIngreso.length > 0 )
	{
		// optServicioIngreso.html( optServicioIngreso.html().toUpperCase() );

		optServicioIngreso.each(function(x){
			$( this ).html( $( this ).html().toUpperCase() );
		})
	}
	//$( "option[value^=4]", $( "#ing_seisel_serv_ing" ) ).remove();
	/************************************************************************************************/


	$( "#div_datosIng_Per_Aco,#div_datos_acompañante,#div_datos_responsable,#div_datos_Pag_Aut,#div_otros_datos_ingreso,#div_accidente_evento,#div_ext_agendaPreadmision" )
		.attr( "acordeon", "" );


   $( "table", $( "#div_admisiones" ) ).addClass( "anchotabla" );
   $( "table", $( "#div_int_otros_datos_ing" ) ).removeClass( "anchotabla" );
   $( "table", $( "#div_int_datos_acompañante" ) ).removeClass( "anchotabla" );
    $( "table", $("#tabla_eps" )).removeClass( "anchotabla" );


   /************************************
    * Edwin
	************************************/
	$( "div[acordeon]" ).accordion({
		collapsible: true,
		heightStyle: "content"
	});

	$( "div[acordeon1]" ).accordion({
		collapsible: false,
		heightStyle: "content",
		icons: false
	});
	/************************************/


	$( "option" ).each(function(){

		if( $( this ).html() != 'Seleccione...' ){
			$( this ).html( $( this ).html().toUpperCase() );
		}
	});

	$( "H3", $( "#div_datos_acompañante,#div_datos_responsable,#div_datos_Pag_Aut,#div_otros_datos_ingreso,#div_accidente_evento" ) ).attr( "acclick", "false" );

	//Agregar el atributo msgError a todos los input para que sean obligatorios
	$( "input[type=text],input[type=radio],input[type=checkbox],select,textarea", $( "#div_admisiones" ) ).each(function(x){
		if( !$( this ).attr( "msgError") ){
			$( this ).attr( "msgError", "" );
		}
	});

	 $( "#div_ext_agendaPreadmision" ).accordion( "option", "collapsible", false );

	/************************************************************************************************
	 * Agosto 15 de 2013
	 ************************************************************************************************/
	//Borro los atributos de msgerror para los radios de admisión y preadmisión
	$( "input[type=radio]", document.getElementById( "radAdmision" ).parentNode.parentNode ).each(function(x){
		$( this ).removeAttr( "msgerror" );
	});
	/************************************************************************************************/

	//quitar el atributo msgError a los campos que no son obligatorios
	$( "#ing_histxtNumHis" ).removeAttr( "msgError" );
	$( "#ing_nintxtNumIng" ).removeAttr( "msgError" );
	$( "#pac_cretxtCorResp" ).removeAttr( "msgError" );
	$( "#pac_cortxtCorEle" ).removeAttr( "msgError" );
	$( "#pac_eemtxtExt" ).removeAttr( "msgError" );
	var prefijo_trEps = $("tr[id$=tr_tabla_eps]").eq(0).attr("id");
	$( "#ing_poltxtNumPol"+prefijo_trEps ).removeAttr( "msgError" );
	$( "#txtEdad" ).removeAttr( "msgError" );
	$( "#ing_ncotxtNumCon"+prefijo_trEps ).removeAttr( "msgError" );
	$( "#res_ffrtxtNumcon"+prefijo_trEps ).removeAttr( "msgError" );
	$( "#res_comtxtNumcon"+prefijo_trEps ).removeAttr( "msgError" );
	$( "#pac_ap2txtSegApe" ).removeAttr( "msgError" );	//Agosto 30 de 2013
	$( "#pac_no2txtSegNom" ).removeAttr( "msgError" );	//Agosto 30 de 2013
	$( "#pac_emptxtEmpLab" ).removeAttr( "msgError" );	//Agosto 30 de 2013
	$( "#pac_temtxtTelTra" ).removeAttr( "msgError" );	//Agosto 30 de 2013
	//$( "#pac_pahtxtPaiRes" ).removeAttr( "msgError" );	//Agosto 30 de 2013
	$( "#pac_prpntxtPaiResPer" ).removeAttr( "msgError" );	//Agosto 30 de 2013

	$( "[name=ing_ordtxtNumAut]" ).removeAttr( "msgError" );	//Agosto 30 de 2013
	$( "[name=ing_fhatxtFecAut]" ).removeAttr( "msgError" );	//Agosto 30 de 2013
	$( "[name=ing_hoatxtHorAut]" ).removeAttr( "msgError" );	//Agosto 30 de 2013
	$( "[name=ing_npatxtNomPerAut]" ).removeAttr( "msgError" );	//Agosto 30 de 2013
	$( "[name=ing_cactxtcups]" ).removeAttr( "msgError" );	//Agosto 30 de 2013
	$( "[name=ing_cachidcups]" ).removeAttr( "msgError" );	//Agosto 30 de 2013
	$( "[name=ing_pcoselPagCom]" ).removeAttr( "msgError" );	//Agosto 30 de 2013
	$( "#buscardorTurno" ).removeAttr( "msgError" );	//Agosto 30 de 2013
	$( "#puestoTrabajo" ).removeAttr( "msgError" );	//Agosto 30 de 2013
	$( "#pac_tle, #pac_dle" ).removeAttr( "msgError" );
	$( "#chk_imprimirHistoria" ).removeAttr( "msgError" );

	marcarAqua( '', 'msgError', 'campoRequerido' );
	marcarAqua(  );

	formatoCampos();
	buscarPaises();
	buscarDepartamentos();
	buscarMunicipios();
	buscarBarrios();
	buscarOcupaciones();
	buscarAseguradoras('tabla_eps');
	buscarCUPS();
	buscarImpresionDiagnostica();
	buscarIpsRemite();
	buscarAseguradorasVehiculo();
	buscarMedicos();
	buscarTarifaParticular();
	// buscarSegundoResp();
	// buscarPrimerResp();
	resetear( true );

	// $("#bot_navegacion").css("display", "none");

	$( "textarea,select,input" ).on({
		blur: function()
		{
			if( $(this).is(":button") ){
				return;
			}
			if( this.id != 'pac_doctxtNumDoc' && this.id != 'ing_histxtNumHis' )
			{
				try{
					if( !verificandoLog ){
						llenarDatosLog();
					}
				}catch(e){}
			}
		}
	});


	/************************************************************************************************
	 * Agosto 20 de 2013
	 *
	 * Para la fecha posible de ingreso se permite que sea mayor o igual a la fecha actual
	 * La fecha de nacimiento no puede ser mayor a la fecha actual
	 ************************************************************************************************/
	var dateActual = $( "#fechaAct" ).val().split( "-" );

	/************************************************************************************************/

	/************************************************************************************************
	 * Agosto 30 de 2013
	 *
	 * - Para algunos campos, cuando reciba foco se pone la fecha actual por defecto
	 * - Para algunos campos al recibir un foco se pone la hora actual por defecto
	 ************************************************************************************************/
	$( "#ing_feitxtFecIng,#ing_fhatxtFecAut" ).on({
		focus: function(){
			if( $( this ).val() == '' ){
				$( this ).val( $( "#fechaAct" ).val() );
			}
		}
	});

	$( "#ing_hintxtHorIng,#ing_hoatxtHorAut" ).on({
		focus: function(){
			//Si es igual a vacío o a la mascara que tenga por defecto
			if( $( this ).val() == '' || $( this ).val() == '__:__:__' ){
				$( this ).val( $( "#horaAct" ).val() );
			}
		}
	});


	/*PARA QUE LA LISTA DE RESPONSABLES SEA SORTABLE*/
	var fixHelper = function(e, ui) {
		ui.children().each(function() {
			$(this).width($(this).width());
		});
		return ui;
	};
	$("#tabla_eps > tbody").sortable({
		items: "> tr",
		helper: fixHelper,
		stop: function( event, ui ) {
			reOrdenarResponsables();
			$("input[type='radio'][name='res_comtxtNumcon']").each( function e( i, item){
				if( i == 0 ){
					if( $(item).is(":checked") ){
						alerta(" Debe modificar la complementariedad" );
					}
					$( item ).attr( "checked", false );
					$( item ).attr( "estadoAnterior", "off" );
					$( item ).parent().hide();

				}else{

					$( item ).parent().show();
				}
			});
		},
	});
	/*FIN PARA QUE LA LISTA DE RESPONSABLES SEA SORTABLE*/

	//Al darle doble click, trae la cedula para llenar los demas campos
	$("#pac_crutxtNumDocRes").dblclick(function(){
		$(this).val( $.trim($("#pac_doctxtNumDoc").val())  );
		validarCamposCedRes();
	});

	$("#pac_ceatxtCelAco").dblclick(function(){
		$(this).val( $.trim($("#pac_doctxtNumDoc").val())  );
		validarCamposCedAco();
	});

	//consultarAgendaPreadmision( $( "#fechaAct" ).val(), 0 );
	//consultarAgendaAdmitidos( $( "#fechaAct" ).val(), 0 );

	$( "#div_datosAdmiPreadmi" ).css( {display:"none"} );
	$( "#div_ext_agendaPreadmision" ).css( {display:""} );

	$("#radPreadmision").attr( "checked", true );
	$("#txtAdmisionPreadmision" ).html( "PREADMISION" );


	$("#pac_fectxtFechaPosibleIngreso").attr( "disabled", false );
	$("#pac_fectxtFechaPosibleIngreso").blur();

	//$( "[name=dat_Accvfi_ux_accfin]" ).datepicker( "option", "minDate", new Date( dateActual[0], dateActual[1]-1, dateActual[2] ) );
	//$( "#pac_fectxtFechaPosibleIngreso,#ing_feitxtFecIng" ).datepicker( "option", "minDate", new Date( dateActual[0], dateActual[1]-1, dateActual[2] ) ); -->2016-09-08
	$( "#pac_fectxtFechaPosibleIngreso,#ing_feitxtFecIng" ).datepicker();
	$( "[name='res_firtxtNumcon'][value='']" ).val( $("#fechaAct").val() );
	$( "[name='res_ffrtxtNumcon'][value='']" ).val('0000-00-00');

	//Pongo limite de fecha máxima, la cual es la actual
	$( "#pac_fnatxtFecNac" ).datepicker( "option", "maxDate", "+0d" );
	$( "#dat_Accfec" ).datepicker( "option", "maxDate", "+0d" );
	$( "[name=dat_Accvfi_ux_accfin]" ).datepicker( "option", "maxDate", "+0d" );
	$( "[name=dat_Accvfi_ux_accffi]" ).datepicker( "option", "minDate", new Date( dateActual[0], dateActual[1]-1, dateActual[2] ) );

	consultarAgendaPreadmision( $( "#fechaAct" ).val(), 0 );
	consultarAgendaAdmitidos( "", 0 );

	$( "select,textarea,input" ).change(function(){
		hayCambios = true;
	});
	//funcion si tiene un log guardado con datos de la admision
	verificarLogAdmision();
	$("[padm]").show();
	consultarConsecutivo();

	//Fechas, ingresar numeros y que vaya separando por guiones
	$("[fecha]").keyup(function(event){
		if( event.which != 8 ){ //Diferente de back space
			if ( $(this).val().length == 4 ){
				$(this).val( $(this).val()+"-" );
			}else if ( $(this).val().length == 7 ){
				$(this).val( $(this).val()+"-" );
			}
		}
	});

	$("[fecha]").blur( function(){
		if( isDate( $(this).val() ) == false ){
			$(this).val("0000-00-00");
		}
	});


	//2014-08-12 Para que no permita ingresar un punto en estos campos, el atributo "alfabetico" ya tiene la restriccion de caracteres, excepto el punto
	$("#pac_ap1txtPriApe,#pac_ap2txtSegApe,#pac_no1txtPriNom,#pac_no2txtSegNom").keyup(function(){
		if ($(this).val() !="")
			$(this).val($(this).val().replace(/(\.)|^( )|[^[a-zA-Z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]]/g, ""));//2017-11-10

	});

	$("#pac_ap1txtPriApe,#pac_ap2txtSegApe,#pac_no1txtPriNom,#pac_no2txtSegNom").blur(function(){
		if ($(this).val() == " "){
			$(this).val("");
			resetAqua( $(this).parent() );
		}
	});

	//2014-12-29 Para que no permita ingresar caracteres especiales en los documentos
	$("#pac_doctxtNumDoc, #pac_tdaselTipoDocRes").keyup(function(){
		if ($(this).val() !=""){//2015-05-22
			if( $("#pac_tdoselTipoDoc").find("option:selected").attr("alfanumerico") == "on" ){
				$(this).val($(this).val().replace(/[^\w\d\ ]/g, ""));
			}else if( $("#pac_tdoselTipoDoc").find("option:selected").attr("alfanumerico") == "off" ) {
				$(this).val($(this).val().replace(/[^\d\ ]/g, ""));
			}
			tam =  $(this).val().length;
			if( tam > 15 ){
				$(this).val( $(this).val().substring( 0, tam - 1) );
			}
		}
	});
   //2014-11-06 Para que no permita ingresar caracteres especiales en los documentos
	$("#pac_crutxtNumDocRes").keyup(function(){
		if ($(this).val() !=""){//2015-05-22
			$(this).val($(this).val().replace(/[^\w\d\ ]/g, ""));
			tam =  $(this).val().length;
			if( tam > 15 ){
				$(this).val( $(this).val().substring( 0, tam - 1) );
			}
		}
	});

	/*$("#ing_histxtNumHis,#ing_nintxtNumIng,#pac_dirtxtDirRes,#pac_dedtxtDetDir,#pac_cortxtCorEle,#pac_emptxtEmpLab,#pac_noatxtNomAco,#pac_nrutxtNomRes,#pac_prutxtParRes,#pac_drutxtDirRes,#pac_ddrtxtDetDirRes,#pac_cretxtCorResp").keyup(function(){
		if ($(this).val() !=""){//2015-05-22
			//$(this).val($(this).val().replace(/[^\w\d\ ]/g, ""));
			$(this).val($(this).val().replace("'", ""));
		}
	});*/

	///-->2015-06-02 para no permitir la escritura de la comilla simple( ' ),puesto que puede romper los inserts.
	$("input[type='text'],textarea").keyup(function(){
		if ($(this).val() !=""){//2015-05-22
			//$(this).val($(this).val().replace(/[^\w\d\ ]/g, ""));
			$(this).val($(this).val().replace("'", ""));
		}
	});

	setInterval(function(){
	  var el = $("#tr_navegaResultados");
	  if(el.hasClass('amarilloSuave')){
	      el.removeClass("amarilloSuave");
	  }else{
	      el.addClass("amarilloSuave");
	  }
	},700);

	if($("#AgendaMedica").val() == "on" ){
		mostrarAdmisionDesdeAgendaMedica();
	}

	//para que oculte el tipo de atención inicialmente.
	validacionServicioIngreso( "cargaInicial" );
	$("#ing_seisel_serv_ing > option").click(function(){
		validacionServicioIngreso();
	});

	$("[name=dat_Accvff_ux_accffi]").click(function(){
		$(this).attr("disabled", "");
	});

	$("#select_doc_new").keyup(function(){
		if ($(this).val() !=""){//2015-05-22
			if( $("#select_tdoc_new").find("option:selected").attr("alfanumerico") == "on" ){
				$(this).val($(this).val().replace(/[^\w\d\ ]/g, ""));
			}else if( $("#select_tdoc_new").find("option:selected").attr("alfanumerico") == "off" ) {
				$(this).val($(this).val().replace(/[^\d\ ]/g, ""));
			}
			$(this).val($(this).val().replace(/(\.)|(  )|[^\w\d\ \-]/g, ""));
			tam =  $(this).val().length;
			if( tam > 11 ){
				$(this).val( $(this).val().substring( 0, tam - 1) );
			}
		}
	});

	$("#txt_jus_change_Doc").keyup(function(){
		if ($(this).val() !="")
			$(this).val($(this).val().replace(/(\')|(\")/g, ""));
	});


	// $('#iframeModalTablero').load(function() {
		// RunAfterIFrameLoaded();
	// });

 });

function consultarSiPreanestesia(){

	var wbasedato    = $("#wbasedato").val();
	var cedula       = $.trim($("#pac_doctxtNumDoc").val());
	var tipoDoc      = $("#pac_tdoselTipoDoc").val();
	var centroCostos = $("#ing_seisel_serv_ing").val();
	$.post("admision_erp.php",
	{
		wbasedato: wbasedato,
		consultaAjax:   '',
		accion:         'consultarSiPreanestesia',
		wemp_pmla:      $("#wemp_pmla").val(),
		cedula:         cedula,
		tipoDoc:        tipoDoc,
		wcco:           centroCostos

	},function(data){
		if (data.error == 1){
		}
		else{
			$("#turno_preanestesia").html( data.turno );
			$("#div_preanestesia").dialog({
				modal	: true,
				width	: 'auto',
				title	: "<div align='center'> <img src='../../images/medical/root/Advertencia.png'/> PREANESTESIA.</div>",
				show	: { effect: "slide", duration: 600 },
				hide	: { effect: "fold", duration: 600 },
				closeOnEscape: false,
			    open: function(event, ui) {
			        $(".ui-dialog-titlebar-close", ui.dialog | ui).hide();
			        $("#input_aux_prea").focus();
			    },
				close: function( event, ui ) {
					//listarPacientesConTurno();
				}
			});
		}
	},
	"json"
	);
}

/*function alerta( txt ){
	$("#textoAlerta").text( txt );
    $.blockUI({ message: $('#msjAlerta') });
        setTimeout( function(){
            $.unblockUI();
        }, 4000 );
}*/

function alerta( txt ){

	$("#textoAlerta2").text( txt  );
	$( '#msjAlerta2').dialog({
		width: "auto",
		height: 200,
		modal: true,
		dialogClass: 'noTitleStuff'
	});
	$(".ui-dialog-titlebar").hide();
	setTimeout( function(){
       $( '#msjAlerta2').dialog('destroy');
       $(".ui-dialog-titlebar").show();
    }, 3000 );
}

function CambiarEstadoDatosExtranjeros(obj){
	if( $(obj).val() == "E" ){
		var colombia = $("#cod_colombia").val();
		$("#pac_tle").attr( "msgError", $("#pac_tle").attr( "msgcampo") );
		$("#pac_dle").attr( "msgError", $("#pac_dle").attr( "msgcampo") );
		marcarAqua( '#pac_tle', 'msgError', 'campoRequerido' );
		marcarAqua( '#pac_dle', 'msgError', 'campoRequerido' );
		$("#pac_dle").addClass("campoRequerido");
		$("#pac_tle").addClass("campoRequerido");
		$(".tr_pacienteExtranjero").show();
		$("#pac_tle, #pac_dle").val("");
	}else{
		$("#pac_tle, #pac_dle").removeClass( "campoRequerido" );
		$("#pac_tle, #pac_dle").removeAttr( "msgError" );
		$(".tr_pacienteExtranjero").hide();
	}
}

function buscarPaises()
{
	//Asigno autocompletar para la busqueda de paises
	$( "#pac_pantxtPaiNac, #pac_pahtxtPaiRes" ).autocomplete("admision_erp.php?consultaAjax=&accion=consultarPais", {
		cacheLength:1,
		delay:300,
		max: 100,
		scroll: false,
		scrollHeight: 500,
		matchSubset: false,
		matchContains: true,
		width:250,
		autoFill:false,
		minChars: 2,
		json:"json",
		formatItem: function(data, i, n, value) {

			//convierto el string en json
			eval( "var datos = "+data );

			return datos[0].usu;	//Eso es lo que se muestra al usuario
		},
		formatResult: function(data, value){
			//convierto el string en json
			eval( "var datos = "+data );
			console.log( datos[0] );
			return datos[0].valor.des;
		}
	}).result(
		function(event, item ){

			// //La respuesta es un json
			// //convierto el string en formato json
			eval( "var datos = "+item );

			//Guardo el ultimo valor que selecciona el usuario
			//Esto en una propiedad inventada
			buscabarrio = false;

			if( $(this).attr("id") == "pac_pantxtPaiNac" ){
				idDepTxt = "pac_deptxtDepNac";
				idDepid  = "pac_dephidDepNac";
				idMunTxt = "pac_ciutxtMunNac";
				idMunid  = "pac_ciuhidMunNac";
			}else{
				idDepTxt = "pac_dehtxtDepRes";
				idDepid  = "pac_dehhidDepRes";
				idMunTxt = "pac_muhtxtMunRes";
				idMunid  = "pac_muhhidMunRes";
				idBarTxt = "pac_bartxtBarRes";
				idBarid  = "pac_barhidBarRes";
				buscabarrio = true;
			}

			if( datos[0].valor.cod != $("#cod_colombia").val() ){
				$("#"+idDepTxt).val("NO APLICA");
				$("#"+idDepTxt).attr("disabled", "disabled");
				$("#"+idDepTxt).removeClass("campoRequerido");
				$("#"+idDepid).val( $("#dep_no_aplica").val() );
				$("#"+idMunTxt).val("NO APLICA");
				$("#"+idMunTxt).attr("disabled", "disabled");
				$("#"+idMunTxt).removeClass("campoRequerido");
				$("#"+idMunid).val( $("#mun_no_aplica").val() );
				if(buscabarrio){
					$("#"+idBarTxt).val("Sin Dato");
					$("#"+idBarTxt).attr("disabled", "disabled");
					$("#"+idBarTxt).removeClass("campoRequerido");
					$("#"+idBarid).val( $("#bar_no_aplica").val() );
					//--> solicitud de campos de dirección y teléfono locales para extranjeros
					$("#pac_tle").attr( "msgError", $("#pac_tle").attr( "msgcampo") );
					$("#pac_dle").attr( "msgError", $("#pac_dle").attr( "msgcampo") );
					marcarAqua( '#pac_tle', 'msgError', 'campoRequerido' );
					marcarAqua( '#pac_dle', 'msgError', 'campoRequerido' );
					$("#pac_dle").addClass("campoRequerido");
					$("#pac_tle").addClass("campoRequerido");
					$(".tr_pacienteExtranjero").show();
				}
			}else{
				$("#"+idDepTxt).val("");
				$("#"+idDepTxt).attr("disabled", false);
				$("#"+idDepTxt).addClass("campoRequerido");
				$("#"+idDepid).val( "" );
				$("#"+idMunTxt).val("");
				$("#"+idMunTxt).attr("disabled", false);
				$("#"+idMunTxt).addClass("campoRequerido");
				$("#"+idMunid).val( "" );
				if(buscabarrio){
					$("#"+idBarTxt).val("S");
					$("#"+idBarTxt).attr("disabled", false);
					$("#"+idBarTxt).addClass("campoRequerido");
					$("#"+idBarid).val( "" );
					$("#pac_tle, #pac_dle").removeClass( "campoRequerido" );
					$("#pac_tle, #pac_dle").removeAttr( "msgError" );
					$(".tr_pacienteExtranjero").hide();
				}
			}
			$("#pac_tle, #pac_dle").val("");
			this.value = datos[0].valor.des;
			this._lastValue = datos[0].valor.des;
			this._lastCodigo = datos[0].valor.cod;
			$( this ).removeClass( "campoRequerido" );
			$( "input[type=hidden]", this.parentNode ).val( datos[0].valor.cod );
		}
	).on({
		change: function(){

			var cmp = this;

			setTimeout( function(){

				//Pregunto si la pareja es diferente
				if( ( ( cmp._lastValue && cmp._lastValue != cmp.value ) || ( !cmp._lastValue && cmp.value != $( cmp ).attr( cmp.aqAttr ) ) )
					|| ( cmp._lastCodigo && cmp._lastCodigo != $( "input[type=hidden]", cmp.parentNode ).val() )
				)
				{
					alerta( " Digite un pa\u00EDs v\u00E1lido" );
					$( "input[type=hidden]", cmp.parentNode ).val( '' );
					cmp.value = '';
					cmp.focus();
					//cmp.blur();
				}
			}, 200 );
		},
		keypress: function( event ) {
			if ( event.which == 0 ) {
				console.log("CONSULTAR MAS");
			}
			console.log(event.which);
		}
	})
	;
}

function buscarDepartamentos()
{
	//Asigno autocompletar para la busqueda de departamentos
	$( "#pac_deptxtDepNac,#pac_dehtxtDepRes,#pac_dretxtDepResp,#AccConductordp,#AccDepPropietario,#Catdep,#Accdep" ).autocomplete( "admision_erp.php?consultaAjax=&accion=consultarDepartamento",
	{
		extraParams: {
			codigoPais: function( campo ){
				return $( "#"+ $( conFocus ).attr( "srcPai" ) ).val();
			},
			name_objeto: function( campo ){
				return $(conFocus).attr("name");
			}
		},

		cacheLength:0,
		delay:300,
		max: 100,
		scroll: false,
		scrollHeight: 500,
		matchSubset: false,
		matchContains: true,
		width:250,
		autoFill:false,
		minChars: 2,
		json:"json",
		formatItem: function(data, i, n, value) {

			//convierto el string en json
			eval( "var datos = "+data );

			return datos[0].usu;	//Eso es lo que se muestra al usuario
		},
		formatResult: function(data, value){
			//convierto el string en json
			eval( "var datos = "+data );

			return datos[0].valor.des;
		}
	}).result(
		function(event, item ){

			// //La respuesta es un json
			// //convierto el string en formato json
			eval( "var datos = "+item );

			//Guardo el ultimo valor que selecciona el usuario
			//Esto en una propiedad inventada
			this.value = datos[0].valor.des;
			this._lastValue = this.value;
			$( this ).removeClass( "campoRequerido" );

			$( "input[type=hidden]", this.parentNode ).val( datos[0].valor.cod );
		}
	).focus(function(){
		conFocus = this;
	}).on({
		change: function(){

			var cmp = this;

			setTimeout( function(){

				//Pregunto si la pareja es diferente
				if( ( ( cmp._lastValue && cmp._lastValue != cmp.value ) || ( !cmp._lastValue && cmp.value != $( cmp ).attr( cmp.aqAttr ) ) )
					|| ( cmp._lastCodigo && cmp._lastCodigo != $( "input[type=hidden]", cmp.parentNode ).val() )
				)
				{
					alerta( " Digite un Departamento v\u00E1lido" )
					$( "input[type=hidden]", cmp.parentNode ).val( '' );
					cmp.value = '';
					cmp.focus();
					// cmp.blur();
				}
			}, 200 );
		}
	});
}

function buscarMunicipios()
{
	//Asigno autocompletar para la busqueda de municipios
	$( "#pac_ciutxtMunNac,#pac_muhtxtMunRes,#pac_mretxtMunResp,#AccMunPropietario,#AccConductorMun,#Catmun,#Accmun" ).autocomplete( "admision_erp.php?consultaAjax=&accion=consultarMunicipio",
	{
		extraParams: {
			dep: function( campo ){
				return $( "#"+ $( conFocus ).attr( "srcDep" ) ).val();
			}
		},
		cacheLength:0,
		delay:300,
		max: 100,
		scroll: false,
		scrollHeight: 500,
		matchSubset: false,
		matchContains: true,
		width:250,
		autoFill:false,
		minChars: 2,
		json:"json",
		formatItem: function(data, i, n, value) {

			//convierto el string en json
			eval( "var datos = "+data );

			return datos[0].usu;	//Eso es lo que se muestra al usuario
		},
		formatResult: function(data, value){
			//convierto el string en json
			eval( "var datos = "+data );

			return datos[0].valor.des;
		}
	}).result(
		function(event, item ){

			// //La respuesta es un json
			// //convierto el string en formato json
			eval( "var datos = "+item );

			//Guardo el ultimo valor que selecciona el usuario
			//Esto en una propiedad inventada
			this.value = datos[0].valor.des;
			this._lastValue = this.value;
			$( this ).removeClass( "campoRequerido" );

			$( "input[type=hidden]", this.parentNode ).val( datos[0].valor.cod );
		}
	).focus(function(){
		conFocus = this;
	}).on({
		change: function(){

			var cmp = this;

			setTimeout( function(){

				//Pregunto si la pareja es diferente
				if( ( ( cmp._lastValue && cmp._lastValue != cmp.value ) || ( !cmp._lastValue && cmp.value != $( cmp ).attr( cmp.aqAttr ) ) )
					|| ( cmp._lastCodigo && cmp._lastCodigo != $( "input[type=hidden]", cmp.parentNode ).val() )
				)
				{
					alerta( " Digite un Municipio v\u00E1lido" )
					$( "input[type=hidden]", cmp.parentNode ).val( '' );
					cmp.value = '';
					cmp.focus();
					// cmp.blur();
				}
			}, 200 );

		//Pregunto si la pareja es diferente
			// if( ( this._lastValue && this._lastValue != this.value && this._lastCodigo ) || ( this._lastCodigo != $( "input[type=hidden]", this.parentNode ).val() ) ){
				// alert( "Digite un municipio valido" )
				// $( "input[type=hidden]", this.parentNode ).val( '' );
				// this.value = '';
			// }
			// if( this._lastValue ){
				// this.value = this._lastValue;
			// }
			// else{
				// this.value = "";
			// }
		}
	});
}


function buscarBarrios()
{
	//Asigno autocompletar para la busqueda de barrios
	$( "#pac_bartxtBarRes" ).autocomplete("admision_erp.php?consultaAjax=&accion=consultarBarrio",
	{
		extraParams: {
			mun: function( campo ){
				return $( "#"+ $( conFocus ).attr( "srcMun" ) ).val();
			}
		},
		cacheLength:0,
		delay:300,
		max: 100,
		scroll: false,
		scrollHeight: 500,
		matchSubset: false,
		matchContains: true,
		width:250,
		autoFill:false,
		minChars: 2,
		json:"json",
		formatItem: function(data, i, n, value) {

			//convierto el string en json
			eval( "var datos = "+data );

			return datos[0].usu;	//Eso es lo que se muestra al usuario
		},
		formatResult: function(data, value){
			//convierto el string en json
			eval( "var datos = "+data );

			return datos[0].valor.des;
		}
	}).result(
		function(event, item ){

			// //La respuesta es un json
			// //convierto el string en formato json
			eval( "var datos = "+item );

			//Guardo el ultimo valor que selecciona el usuario
			//Esto en una propiedad inventada
			this.value = datos[0].valor.des;
			this._lastValue = this.value;
			$( this ).removeClass( "campoRequerido" );

			$( "input[type=hidden]", this.parentNode ).val( datos[0].valor.cod );
		}
	).focus(function(){
		conFocus = this;
	}).on({
		change: function(){

			var cmp = this;

			setTimeout( function(){

				//Pregunto si la pareja es diferente
				if( ( ( cmp._lastValue && cmp._lastValue != cmp.value ) || ( !cmp._lastValue && cmp.value != $( cmp ).attr( cmp.aqAttr ) ) )
					|| ( cmp._lastCodigo && cmp._lastCodigo != $( "input[type=hidden]", cmp.parentNode ).val() )
				)
				{
					alerta( " Digite un Barrio v\u00E1lido" )
					$( "input[type=hidden]", cmp.parentNode ).val( '' );
					cmp.value = '';
					cmp.focus();
					// cmp.blur();
				}
			}, 200 );

		//Pregunto si la pareja es diferente
			// if( ( this._lastValue && this._lastValue != this.value && this._lastCodigo ) || ( this._lastCodigo != $( "input[type=hidden]", this.parentNode ).val() ) ){
				// alert( "Digite un barrio valido" )
				// $( "input[type=hidden]", this.parentNode ).val( '' );
				// this.value = '';
			// }
			// if( this._lastValue ){
				// this.value = this._lastValue;
			// }
			// else{
				// this.value = "";
			// }
		}
	});;
}

function buscarOcupaciones()
{
	//Asigno autocompletar para la busqueda de ocuapciones
	$( "#pac_ofitxtocu" ).autocomplete("admision_erp.php?consultaAjax=&accion=consultarOcupacion",
	{
		cacheLength:1,
		delay:300,
		max: 100,
		scroll: false,
		scrollHeight: 500,
		matchSubset: false,
		matchContains: true,
		width:250,
		autoFill:false,
		minChars: 3,
		json:"json",
		formatItem: function(data, i, n, value) {

			//convierto el string en json
			eval( "var datos = "+data );

			return datos[0].usu;	//Eso es lo que se muestra al usuario
		},
		formatResult: function(data, value){
			//convierto el string en json
			eval( "var datos = "+data );

			return datos[0].valor.des;
		}
	}).result(
		function(event, item ){

			// //La respuesta es un json
			// //convierto el string en formato json
			eval( "var datos = "+item );

			//Guardo el ultimo valor que selecciona el usuario
			//Esto en una propiedad inventada
			this.value = datos[0].valor.des;
			this._lastValue = this.value;
			$( this ).removeClass( "campoRequerido" );

			$( "input[type=hidden]", this.parentNode ).val( datos[0].valor.cod );
		}
	).on({
		change: function(){

			var cmp = this;

			setTimeout( function(){

				//Pregunto si la pareja es diferente
				if( ( ( cmp._lastValue && cmp._lastValue != cmp.value ) || ( !cmp._lastValue && cmp.value != $( cmp ).attr( cmp.aqAttr ) ) )
					|| ( cmp._lastCodigo && cmp._lastCodigo != $( "input[type=hidden]", cmp.parentNode ).val() )
				)
				{
					alerta( " Digite una Ocupaci\u00F3n v\u00E1lida" )
					$( "input[type=hidden]", cmp.parentNode ).val( '' );
					cmp.value = '';
					cmp.focus();
					// cmp.blur();
				}
			}, 200 );

		//Pregunto si la pareja es diferente
			// if( ( this._lastValue && this._lastValue != this.value && this._lastCodigo ) || ( this._lastCodigo != $( "input[type=hidden]", this.parentNode ).val() ) ){
				// alert( "Digite una ocupacion valida" )
				// $( "input[type=hidden]", this.parentNode ).val( '' );
				// this.value = '';
			// }
			// if( this._lastValue ){
				// this.value = this._lastValue;
			// }
			// else{
				// this.value = "";
			// }
		}
	});
}

function buscarAseguradoras(tabla_referencia)
{
	var wbasedato = $("#wbasedato").val();

	if (tabla_referencia != "")
	{
	// para saber si la tabla tiene filas o no
				trs = $("#"+tabla_referencia).find('tr[id$=tr_'+tabla_referencia+']').length;
				var value_id = 0;

				//busca consecutivo mayor
				if(trs > 0)
				{
					id_mayor = 0;
					// buscar los tr que terminen en el mismo nombre de su tabla contenedora, recorrerlos y recuperar el valor mayor
					$("#"+tabla_referencia).find('tr[id$=tr_'+tabla_referencia+']').each(function() {
						id_ = $(this).attr('id');
						id_splt = id_.split('_');
						id_this = (id_splt[0])*1;
						if(id_this >= id_mayor)
						{
							id_mayor = id_this;
						}
					});
					// id_mayor++;
					value_id = id_mayor+'_tr_'+tabla_referencia;

				}
				else
				{ value_id = '1_tr_'+tabla_referencia; }

		codEsp="#ing_cemtxtCodAse"+value_id;
	}

	//Asigno autocompletar para la busqueda de aseguradoras
	$( codEsp ).autocomplete("admision_erp.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&accion=consultarAseguradora&wbasedato="+wbasedato+"&origenConsulta=autoCompletar",
	{
		cacheLength:1,
		delay:300,
		max: 100,
		scroll: false,
		scrollHeight: 500,
		matchSubset: false,
		matchContains: true,
		width:250,
		autoFill:false,
		minChars: 3,
		json:"json",
		formatItem: function(data, i, n, value) {

			//convierto el string en json
			eval( "var datos = "+data );

			return datos[0].usu;	//Eso es lo que se muestra al usuario
		},
		formatResult: function(data, value){
			//convierto el string en json
			eval( "var datos = "+data );

			return datos[0].valor.des;
		}
	}).result(
		function(event, item ){

			// //La respuesta es un json
			// //convierto el string en formato json
			eval( "var datos = "+item );

			//Guardo el ultimo valor que selecciona el usuario
			//Esto en una propiedad inventada
			console.log( "value Nuevo: "+datos[0].valor.des);
			console.log( "valor anterior: "+this._lastValue);
			this.value = datos[0].valor.des;
			this._lastValue = this.value;
			$( this ).removeClass( "campoRequerido" );

			$( "input[type=hidden]", this.parentNode ).eq(0).val( datos[0].valor.cod );
			$( "input[type=hidden]", this.parentNode ).eq(1).val( datos[0].valor.des );

			//se manda el value_id porque al id se le concateno el consecutivo de filas

			llenarPlan(datos[0].valor.cod, "ing_plaselPlan"+value_id )
		}
	).on({
		change: function(){
			var cmp = this;
			consolidarResponsableSeleccionado( cmp );
		}
	});
}

function consolidarResponsableSeleccionado( cmp ){

	setTimeout( function(){

				//Pregunto si la pareja es diferente
				cmp.value = $.trim( cmp.value );
				if( ( ( cmp._lastValue && cmp._lastValue != cmp.value ) || ( !cmp._lastValue && cmp.value != $( cmp ).attr( cmp.aqAttr ) ) )
					|| ( cmp._lastCodigo && cmp._lastCodigo != $( "input[type=hidden]", cmp.parentNode ).val() )
				)
				{
					alerta( " Digite una Aseguradora v\u00E1lida" )
					$( "input[type=hidden]", cmp.parentNode ).val( '' );
					cmp.value = '';
					cmp.focus();
					// cmp.blur();
				}
			}, 200 );
}

function buscarCUPS( contenedor, indice )
{
	if( !contenedor){
		contenedor = "div_admisiones";
	}
	if( !indice){
		indice = 0;
	}
	//Asigno autocompletar para la busqueda de paises
	$( "[name=ing_cactxtcups]:eq("+indice+")", $("#"+contenedor )).autocomplete("admision_erp.php?consultaAjax=&accion=consultarCUPS",
	{
		cacheLength:1,
		delay:300,
		max: 100,
		scroll: false,
		scrollHeight: 500,
		matchSubset: false,
		matchContains: true,
		width:250,
		autoFill:false,
		minChars: 3,
		json:"json",
		formatItem: function(data, i, n, value) {

			//convierto el string en json
			eval( "var datos = "+data );

			return datos[0].usu;	//Eso es lo que se muestra al usuario
		},
		formatResult: function(data, value){
			//convierto el string en json
			eval( "var datos = "+data );

			return datos[0].valor.des;
		}
	}).result(
		function(event, item ){

			// //La respuesta es un json
			// //convierto el string en formato json
			eval( "var datos = "+item );

			//Si cup elegido ya esta en la lista, no aceptarlo
			$(this).parent().parent()

			//Guardo el ultimo valor que selecciona el usuario
			//Esto en una propiedad inventada
			this.value = datos[0].valor.cod + "-" + datos[0].valor.des;
			this._lastValue = this.value;
			$( this ).removeClass( "campoRequerido" );

			$(this).next("input[type=hidden]").val( datos[0].valor.cod );
		}
	).on({
		change: function(){

			var cmp = this;
			if( cmp.value == "" ){
				$(cmp).next("input[type='hidden'][name='ing_cachidcups']").val( "" );
				$(cmp).next("input[type='hidden'][name='ing_cachidcups']").next("input[type='hidden'][name='id_idcups']").val( "" );
				return;
			}
			setTimeout( function(){
				//Pregunto si la pareja es diferente
				if( ( ( cmp._lastValue && cmp._lastValue != cmp.value ) || ( !cmp._lastValue && cmp.value != $( cmp ).attr( cmp.aqAttr ) ) )
					|| ( cmp._lastCodigo && cmp._lastCodigo != $(this).next("input[type=hidden]").val() )
				)
				{
					alerta( " Digite un C\u00F3digo CUP v\u00E1lido" )
					$( "input[type=hidden]", cmp.parentNode ).val( '' );
					cmp.value = '';
					cmp.focus();
					// cmp.blur();
				}
			}, 200 );
		}
	});
}

function buscarImpresionDiagnostica()
{
	console.log(" entro a buscar impresion diagnostica");
	//Asigno autocompletar para la busqueda de impresiones diagnosticas
	$( "#ing_digtxtImpDia" ).autocomplete("admision_erp.php?consultaAjax=&accion=consultarImpresionDiagnostica",
	{
		extraParams: {
			edad: function( campo ){

				var objEdad = calcular_edad_detalle( $( "#pac_fnatxtFecNac" ).val() );

				var edadDec = objEdad.age + objEdad.month/12 + objEdad.day/365;

				return edadDec;
			},
			sexo: function( campo ){
				return $( "[name=pac_sexradSex]:checked" ).val();
			}
		},
		cacheLength:0,
		delay:300,
		max: 100,
		scroll: false,
		scrollHeight: 500,
		matchSubset: false,
		matchContains: true,
		width:250,
		autoFill:false,
		minChars: 3,
		json:"json",
		formatItem: function(data, i, n, value) {

			//convierto el string en json
			eval( "var datos = "+data );
			if(datos==0)
				return false;

			return datos[0].usu;	//Eso es lo que se muestra al usuario
		},
		formatResult: function(data, value){

			if( $.trim(data) == 0 ){
				alerta("Por favor ingrese una fecha de nacimiento para consultar la impresion diagnostica");
				return false;
			}
			//convierto el string en json
			eval( "var datos = "+data );

			return datos[0].valor.des;
		}
	}).result(
		function(event, item ){

			// //La respuesta es un json
			// //convierto el string en formato json
			eval( "var datos = "+item );

			//Guardo el ultimo valor que selecciona el usuario
			//Esto en una propiedad inventada
			this.value = datos[0].valor.cod + "-" + datos[0].valor.des;
			this._lastValue = this.value;
			$( this ).removeClass( "campoRequerido" );

			$( "input[type=hidden]", this.parentNode ).val( datos[0].valor.cod );
		}
	).on({
		change: function(){

			var cmp = this;

			setTimeout( function(){

				//Pregunto si la pareja es diferente
				if( ( ( cmp._lastValue && cmp._lastValue != cmp.value ) || ( !cmp._lastValue && cmp.value != $( cmp ).attr( cmp.aqAttr ) ) )
					|| ( cmp._lastCodigo && cmp._lastCodigo != $( "input[type=hidden]", cmp.parentNode ).val() )
				)
				{
					alerta( " Digite un Diagn\u00F3stico v\u00E1lido" )
					$( "input[type=hidden]", cmp.parentNode ).val( '' );
					cmp.value = '';
					cmp.focus();
					// cmp.blur();
				}
			}, 200 );
		}
	});
}

function buscarTarifaParticular()
{
	console.log(" entro a  buscar Tarifa Particular");
	//Asigno autocompletar para la busqueda de impresiones diagnosticas
	$( "#ing_tartxt" ).autocomplete("admision_erp.php?consultaAjax=&accion=buscarTarifaParticular&wbasedato="+$("#wbasedato").val(),
	{
		cacheLength:0,
		delay:300,
		max: 100,
		scroll: false,
		scrollHeight: 500,
		matchSubset: false,
		matchContains: true,
		width:250,
		autoFill:false,
		minChars: 1,
		json:"json",
		formatItem: function(data, i, n, value) {

			//convierto el string en json
			eval( "var datos = "+data );
			if(datos==0)
				return false;

			return datos[0].usu;	//Eso es lo que se muestra al usuario
		},
		formatResult: function(data, value){

			if( $.trim(data) == 0 ){
				alerta("No se han encontrado datos");
				return false;
			}
			//convierto el string en json
			eval( "var datos = "+data );

			return datos[0].valor.des;
		}
	}).result(
		function(event, item ){

			// //La respuesta es un json
			// //convierto el string en formato json
			eval( "var datos = "+item );

			//Guardo el ultimo valor que selecciona el usuario
			//Esto en una propiedad inventada
			this.value = datos[0].valor.cod + "-" + datos[0].valor.des;
			this._lastValue = this.value;
			$( this ).removeClass( "campoRequerido" );

			$( "input[type=hidden]", this.parentNode ).val( datos[0].valor.cod );
		}
	).on({
		change: function(){

			var cmp = this;

			setTimeout( function(){

				//Pregunto si la pareja es diferente
				if( ( ( cmp._lastValue && cmp._lastValue != cmp.value ) || ( !cmp._lastValue && cmp.value != $( cmp ).attr( cmp.aqAttr ) ) )
					|| ( cmp._lastCodigo && cmp._lastCodigo != $( "input[type=hidden]", cmp.parentNode ).val() )
				)
				{
					alerta( " Digite una tarifa v\u00E1lida" )
					$( "input[type=hidden]", cmp.parentNode ).val( '' );
					cmp.value = '';
					cmp.focus();
					// cmp.blur();
				}
			}, 200 );
		}
	});
}

function resetear( inicio )
{
	//Variable para saber si esta en modo consulta o no
	modoConsulta = false;

	//todos los que tengan la clase reset se ponen en blanco
	$("#div_admisiones").find(":input").each(function(){
			if ($(this).hasClass('reset'))
			{
				$(this).val("");
			}

	});

	// iniciarMarcaAqua();
    //para mostrar la fecha actual
	var now = new Date();
	var hora = now.getHours();
	var minutos = now.getMinutes();
	var segundos = now.getSeconds();
	if (hora < 10) {hora='0'+hora}
	if (minutos < 10) {minutos='0'+minutos}
	if (segundos < 10) {segundos='0'+segundos}
	horaActual = hora + ":" + minutos + ":" + segundos;


	//datos por defecto a iniciar
	$( "#pac_zonselZonRes" ).val( 'U' );
	//$( "#pac_trhselTipRes" ).val( 'N' );
	$( "#ing_lugselLugAte" ).val( '1' );
	$( "#pac_tdoselTipoDoc" ).val( 'CC' );
	$( "#pac_tdaselTipoDocRes" ).val( 'CC' );
	$( "#pac_fnatxtFecNac" ).val($( "#fechaAct" ).val() ); //fecha aut
	calcular_edad($( "#pac_fnatxtFecNac" ).val()); //pac_fnatxtFecNac
	$( "#ing_feitxtFecIng" ).val( $( "#fechaAct" ).val() ); //fecha ing
	$( "input[name='res_firtxtNumcon']" ).val( $( "#fechaAct" ).val() ); //fecha ing
    // $( "#ing_hintxtHorIng" ).val($( "#horaAct" ).val() ); //hora ing
	$( "#ing_hintxtHorIng" ).val(horaActual);
	$( "#ing_fhatxtFecAut" ).val($( "#fechaAct" ).val() ); //fecha aut
	$( "#ing_hoatxtHorAut" ).val( horaActual); //hora aut

	valorDefecto = $( "#ing_claselClausu>option[defecto='on']" ).val();
	console.log( "valor defecto 2 "+valorDefecto);

	if(valorDefecto == undefined)
		$( "#ing_claselClausu" ).val( 1 );
	else
		$( "#ing_claselClausu" ).val( valorDefecto );
	$( "#pac_fnatxtFecNac" ).val( "" );
	$( "#pac_petselPerEtn" ).val( 6 );

	resetAqua( );


	$("#bot_navegacion").css("display", "none"); //se oculta el div de navegacion de resultados
	$("#bot_navegacion1").css("display", "none"); //se oculta el div de navegacion de resultados

	//2014-05-06validarTipoResp('');
	var objetoRes = $("[name=ing_tpaselTipRes]").eq(0);
		//console.log("es aqui "+objetoRes[0].value);
		validarTipoResp(objetoRes[0]);

	//se borran todos los label de error que se encuentren
	$( "label" ).remove();
	//se pone el estado del paciente en blanco como es un span se le envia .html
	$("#spEstPac").html("");

	$( "#div_accidente_evento").css( {display: "none"} );
	//se ponen los campos readonly false para que se puedan llenar
	$('#pac_doctxtNumDoc').attr("readonly", false);
	$('#ing_histxtNumHis').attr("readonly", false);
	$('#ing_nintxtNumIng').attr("readonly", false);

	//Al iniciar datos se borra el log
	if( !inicio )
	{
		borrarLog( $( "#key" ) );
	}

	$( "#radAdmision" ).attr( "checked", true );
	$( "#pac_fectxtFechaPosibleIngreso" ).val( $( "#pac_fectxtFechaPosibleIngreso" ).attr( "msgerror" ) );
	$( "[name=radPreAdmi]:checked" ).click();

	ultimaPreadmisionCargada = '';

	//Agosto 23 de 2013
	$( "#ing_seisel_serv_ing" ).change();
	$("#cargoDatosConsulta").val("off");
	//
	//$("#div_topes").html("");

	// --> Limpiar el numero del turno, 2015-11-24: Jerson trujillo.
	$("#numTurnoPaciente").attr("valor", "").html("SIN TURNO!!!");

	$( "#wfiniAdm" ).val("");
	$( "#whiniAdm" ).val("");
}

// -------------------------------------------------------------------
// --> Guarda en varible temporal el numero del turno, jerson trujillo
// -------------------------------------------------------------------
function conservarNumeroDeTurno()
{
	numeroTurnoTemporal = $("#numTurnoPaciente").attr("valor");
}

function validarOrigenAte(obj)
{
	objq = jQuery(obj);
	var origen = objq.val();

	//Para controlar si desea cancelar accidente de transito o evento catastrofico
	if( obj.lastValue == '02' || obj.lastValue == '06' ){

		if( obj.lastValue == '02' ){
			var contenedor = $( "#accidentesTransito" )[0];
			if( contenedor.lastInfo && contenedor.lastInfo != '' ){
				if( confirm( "Desea ignorar los cambios realizados en accidente de tr\u00E1nsito?" ) ){
					resetearAccidentes();
					$( "#div_accidente_evento").css( {display: "none"} );
					reOrdenarResponsables();
				}
				else{
					obj.value = '02';
					return;
				}
			}
		}
		else{
			var contenedor = $( "#eventosCatastroficos" )[0];
			if( contenedor.lastInfo && contenedor.lastInfo != '' ){
				if( confirm( "Desea ignorar los cambios realizados en eventos catastr\u00F3ficos?" ) ){
					resetearEventosCatastroficos();
					$( "#div_accidente_evento").css( {display: "none"} );
				}
				else{
					obj.value = '06';
					return;
				}
			}
		}
	}

	//var origen = $("#ing_caiselOriAte").val();
	if (origen == '02'  ){
	   mostrarAccidentesTransito();
	   //20140225$("#div_datos_autorizacion").css("display", "none");
	   //reOrdenarResponsables();
	}
	else if (origen == '06'){
		listarEventosCatastroficos();
		//20140225$("#div_datos_autorizacion").css("display", "none");
	}
	if (origen != '06' && origen != '02'){
		//20140225$('#div_datos_autorizacion').css("display", "");
	}
	//validarTipoResp('');
	var objetoRes = $("[name=ing_tpaselTipRes]").eq(0);
	validarTipoResp(objetoRes[0]);
}

function calcular_edad(fecha, cmp )
{
    var today = new Date();
    var birthDate = new Date($("#pac_fnatxtFecNac").datepicker("getDate"));
    var age = today.getFullYear() - birthDate.getFullYear();
    var m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    $("#txtEdad").val(age);

	//Si tiene menos de 18 no puede tener documento tipo cedula
	if( cmp ){
		if( age < 18 && $( "#pac_tdoselTipoDoc" ).val() == "CC" ){

			//Se muestra mensaje
			alerta( " El paciente debe ser mayor de edad." );

			//Se borra la fecha de nacimiento
			$( "#pac_fnatxtFecNac" ).val( '' );

			//Se borra la Edad
			$("#txtEdad").val('');

			//Muestro de nuevo el calendario
			setTimeout(function(){
					$( "#pac_fnatxtFecNac" ).focus();
					$( "#pac_fnatxtFecNac" ).click();
				}, 200
			)
		}

		$( "#pac_fnatxtFecNac" ).focus();
		$( "#pac_fnatxtFecNac" ).removeClass( "campoRequerido");
	}
}

function validarPacienteRem()
{

	var pacRem = $("input[name=pac_remradPacRem]:checked").val();
	if (pacRem == 'S')
	{
		$("#pac_iretxtIpsRem").attr("disabled", false);
		$("#pac_cactxtCodAce").attr("disabled", false);
		$("#ing_vretxtValRem").removeAttr("disabled");
	}
	else
	{
		$("#pac_iretxtIpsRem").attr("disabled", true);
		$("#pac_cactxtCodAce").attr("disabled", true);
		$("#ing_vretxtValRem").val("");
		$("#ing_vretxtValRem").attr("disabled", "disabled");
	}

	resetAqua( $( "#div_int_datos_personales" ) );
}

function validarTipoResp(tipoResponsable){

	var tipResp = $(tipoResponsable).val();
	//console.log("Validar respon: "+tipResp);
	var origen = $("#ing_caiselOriAte").val();
	var servicio = $("#ing_seisel_serv_ing").val();

	auxx = jQuery(tipoResponsable);
	var filaResponsable = auxx.parents("tr[id$=tr_tabla_eps]");
	if( !filaResponsable ){
		var prefijo_trEps = $("tr[id$=tr_tabla_eps]").eq(0).attr("id");
		filaResponsable = $("#"+prefijo_trEps);
	}

	//2014-10-20 esconder los datos solicitados para particulares y mostrar los que se ocultan cuando es particular
	filaResponsable.find(".dato_esconder_particulares").show().children().attr("disabled",false);
	filaResponsable.find(".dato_particulares").hide().children().attr("disabled",true);

	/* llamado desde validarOrigenAte()
	   diferente a accidente de transito y a evento catastrofico, no se a seleccionado empresa
	*/
	if ((tipResp == '' || tipResp == 'E') && origen != '02' && origen != '06')
	{
		$('#tabla_responsables_1_2').css('display','none');
		$('#tr_titulo_tercer_resp').css('display','none'); //se oculta
		filaResponsable.find("[id^=ing_ordtxtNumAut],[id^=ing_fhatxtFecAut],[id^=ing_hoatxtHorAut],[id^=ing_npatxtNomPerAut],[id^=ing_cactxtcups],[id^=ing_pcoselPagCom]").attr("disabled", false);
		$("[id^=ing_cemtxtCodAse],[id^=ing_plaselPlan],[id^=ing_poltxtNumPol],[id^=ing_ncotxtNumCon],[id^=res_firtxtNumcon],[id^=res_ffrtxtNumcon]", tipoResponsable.parentNode.parentNode).attr("disabled", false);
		$("[id^=ing_cemtxtCodAse],[id^=ing_plaselPlan]", tipoResponsable.parentNode.parentNode).addClass("campoRequerido");
		//resetAqua( $( "#div_int_pag_aut" ) );
		$("[id^=ing_poltxtNumPol],[id^=ing_ncotxtNumCon],[id^=res_ffrtxtNumcon],[id^=res_comtxtNumcon]", tipoResponsable.parentNode.parentNode).removeClass("campoRequerido"); //si se descomentan agregarlos donde se adiciona la clase
		//se deshabilitan los campos nuevos de primer y segundo responsable
		$("#restxtCodRes,#dat_AccreshidCodRes24,#dat_AcctartxtTarRes,#dat_AccvsmtxtSalMin,#dat_AcctoptxtValTop").attr("disabled", true);
		resetAqua( $( "#div_int_pag_aut" ) );
	}
	/* llamado desde validarOrigenAte()
	   igual a accidente de transito y a evento catastrofico, no se a seleccionado empresa
	*/
	else if ((tipResp == '' || tipResp == 'E') && (origen == '02' )) //empresa y es accidente de transito o evento catastrofico
	{
		$('#tabla_responsables_1_2').css('display','');
		$('#tr_titulo_tercer_resp').css('display','');
		//Los datos del pagador siempre deben estar activas el primer tr de txt de la tabla_eps
		$("[id^=ing_cemtxtCodAse],[id^=ing_plaselPlan],[id^=ing_poltxtNumPol],[id^=ing_ncotxtNumCon]", tipoResponsable.parentNode.parentNode).attr("disabled", false);
		//el primer tr de txt de la tabla_eps
		$("[id^=ing_cemtxtCodAse],[id^=ing_plaselPlan]", tipoResponsable.parentNode.parentNode).addClass("campoRequerido");
		//se deshabilitan los campos nuevos de primer y segundo responsable
		$("#restxtCodRes,#dat_AccreshidCodRes24,#dat_AcctartxtTarRes,#dat_AccvsmtxtSalMin,#dat_AcctoptxtValTop,#ing_vretxtValRem").attr("disabled", false);
		buscarPrimerResp();
		resetAqua( $( "#div_int_pag_aut" ) );
	}
	/*
	   si es particular
	*/
	else if (tipResp == 'P' && origen != '02' && origen != '06')
	{

		//Los datos del pagador siempre deben estar activas se hace al primer tr de txt de la tabla_eps
		$("[id^=ing_cemtxtCodAse],[id^=ing_plaselPlan],[id^=ing_poltxtNumPol],[id^=ing_ncotxtNumCon]", tipoResponsable.parentNode.parentNode).attr("disabled", true);
		filaResponsable.find("[id^=ing_ordtxtNumAut],[id^=ing_fhatxtFecAut],[id^=ing_hoatxtHorAut],[id^=ing_npatxtNomPerAut],[id^=ing_cactxtcups],[id^=ing_pcoselPagCom]").attr("disabled", true);
		$("[id^=ing_cemtxtCodAse]", tipoResponsable.parentNode.parentNode).val( '' );
		$("[id^=ing_cemhidCodAse]", tipoResponsable.parentNode.parentNode).val( '' );
		//se deshabilitan los campos nuevos de primer y segundo responsable
		$("#restxtCodRes,#dat_AccreshidCodRes24,#dat_AcctartxtTarRes,#dat_AccvsmtxtSalMin,#dat_AcctoptxtValTop,#ing_vretxtValRem").attr("disabled", true);

		//se remueve la clase requerida a los que estan deshabilitados al primer tr de txt de la tabla_eps
		//$("[id^=ing_cemtxtCodAse],[id^=ing_plaselPlan],[id^=ing_poltxtNumPol],[id^=ing_ncotxtNumCon]", tipoResponsable.parentNode.parentNode).removeClass("campoRequerido");
		$("[id^=ing_cemtxtCodAse],[id^=ing_plaselPlan]", tipoResponsable.parentNode.parentNode).removeClass("campoRequerido");
		$("[id^=ing_ordtxtNumAut],[id^=ing_fhatxtFecAut],[id^=ing_hoatxtHorAut],[id^=ing_npatxtNomPerAut],[id^=ing_cactxtcups],[id^=ing_pcoselPagCom]",tipoResponsable.parentNode.parentNode).removeClass("campoRequerido");

		$('#tabla_responsables_1_2').css('display','none');
		$('#tr_titulo_tercer_resp').css('display','none');

		filaResponsable.find(".dato_esconder_particulares").hide().children().attr("disabled",true);
		filaResponsable.find(".dato_particulares").show().children().attr("disabled",false);
		resetAqua( $( "#div_int_pag_aut" ) );
	}
	else if (tipResp == 'P' && (origen == '02') )
	{
		$('#tabla_responsables_1_2').css('display',''); //se muestra
		$('#tr_titulo_tercer_resp').css('display',''); //se muestra

		//se remueve clase campo requerido
		filaResponsable.find("[id^=ing_cemtxtCodAse],[id^=ing_plaselPlan],[id^=ing_poltxtNumPol],[id^=ing_ncotxtNumCon],[id^=res_ffrtxtNumcon],[id^=res_comtxtNumcon]").removeClass("campoRequerido").attr("disabled", true);
		filaResponsable.find("[id^=ing_ordtxtNumAut],[id^=ing_fhatxtFecAut],[id^=ing_hoatxtHorAut],[id^=ing_npatxtNomPerAut],[id^=ing_cactxtcups],[id^=ing_pcoselPagCom]").removeClass("campoRequerido").attr("disabled", true);;
		//se deshabilitan
		$("[id^=ing_cemtxtCodAse]", tipoResponsable.parentNode.parentNode).val( '' );
		$("[id^=ing_cemhidCodAse]", tipoResponsable.parentNode.parentNode).val( '' );
		$("[id^=ing_plaselPlan]", tipoResponsable.parentNode.parentNode).val( '' );

		//se habilitan los campos nuevos de primer y segundo responsable
		$("#restxtCodRes,#dat_AccreshidCodRes24,#dat_AcctartxtTarRes,#dat_AccvsmtxtSalMin,#dat_AcctoptxtValTop,#ing_vretxtValRem").attr("disabled", false);
		buscarPrimerResp();

		filaResponsable.find(".dato_esconder_particulares").hide().children().attr("disabled",true);
		filaResponsable.find(".dato_particulares").show().children().attr("disabled",false);
		resetAqua( $( "#div_int_pag_aut" ) );
	}
	else if (tipResp == 'E' &&  origen == '06')
	{
		$('#tabla_responsables_1_2').css('display','none');
		$('#tr_titulo_tercer_resp').css('display','none'); //se oculta

		//se adiciona la clase requerida a los que estan deshabilitados
		filaResponsable.find("[id^=ing_ordtxtNumAut],[id^=ing_fhatxtFecAut],[id^=ing_hoatxtHorAut],[id^=ing_npatxtNomPerAut],[id^=ing_cactxtcups],[id^=ing_pcoselPagCom]").removeClass("campoRequerido").attr("disabled", true);

		//resetAqua( $( "#div_int_pag_aut" ) );
		filaResponsable.find("[id^=ing_poltxtNumPol],[id^=ing_ncotxtNumCon],[id^=res_ffrtxtNumcon],[id^=res_comtxtNumcon]").removeClass("campoRequerido"); //si se descomentan agregarlos donde se adiciona la clase

		//se deshabilitan los campos nuevos de primer y segundo responsable
		$("#dat_AccreshidCodRes24,#dat_AcctartxtTarRes,#dat_AccvsmtxtSalMin,#dat_AcctoptxtValTop,#dat_Accre2hidCodRes2").attr("disabled", true);
		//se quitan porque ya el segundo responsable pasa a ser valor remitido #restxtCodRes, #re2txtCodRes2, #re2hidtopRes2

		$("[id^=ing_cemtxtCodAse],[id^=ing_plaselPlan],[id^=ing_poltxtNumPol],[id^=ing_ncotxtNumCon]", tipoResponsable.parentNode.parentNode).attr("disabled", false);
		$("[id^=ing_cemtxtCodAse],[id^=ing_plaselPlan]", tipoResponsable.parentNode.parentNode).addClass("campoRequerido");
		resetAqua( $( "#div_int_pag_aut" ) );
	}
	else if (tipResp == 'P' &&  origen == '06') //Cambiar
	{
		$('#tabla_responsables_1_2').css('display','none');
		$('#tr_titulo_tercer_resp').css('display','none'); //se oculta
		filaResponsable.find("[id^=ing_ordtxtNumAut],[id^=ing_fhatxtFecAut],[id^=ing_hoatxtHorAut],[id^=ing_npatxtNomPerAut],[id^=ing_cactxtcups],[id^=ing_pcoselPagCom]").attr("disabled", true);
		$("[id^=ing_cemtxtCodAse],[id^=ing_plaselPlan],[id^=ing_poltxtNumPol],[id^=ing_ncotxtNumCon],[id^=res_firtxtNumcon],[id^=res_ffrtxtNumcon]", tipoResponsable.parentNode.parentNode).attr("disabled", true);

		$("[id^=ing_cemtxtCodAse]", tipoResponsable.parentNode.parentNode).val( '' );
		$("[id^=ing_cemhidCodAse]", tipoResponsable.parentNode.parentNode).val( '' );

		//se adiciona la clase requerida a los que estan deshabilitados
		filaResponsable.find("[id^=ing_ordtxtNumAut],[id^=ing_fhatxtFecAut],[id^=ing_hoatxtHorAut],[id^=ing_npatxtNomPerAut],[id^=ing_cactxtcups],[id^=ing_pcoselPagCom]").removeClass("campoRequerido");
		$("[id^=ing_cemtxtCodAse],[id^=ing_plaselPlan],[id^=ing_poltxtNumPol],[id^=ing_ncotxtNumCon],[id^=res_ffrtxtNumcon],[id^=res_comtxtNumcon]", tipoResponsable.parentNode.parentNode).removeClass("campoRequerido");

		//se deshabilitan los campos nuevos de primer y segundo responsable
		$("#restxtCodRes,#dat_AccreshidCodRes24,#dat_AcctartxtTarRes,#dat_AccvsmtxtSalMin,#dat_AcctoptxtValTop,#ing_vretxtValRem").attr("disabled", true);

		filaResponsable.find(".dato_esconder_particulares").hide().children().attr("disabled",true);
		filaResponsable.find(".dato_particulares").show().children().attr("disabled",false);

		resetAqua( $( "#div_int_pag_aut" ) );
	}
}

function llenarPlan(valor, selectHijo)
{
   $.ajax(
	{
				url: "admision_erp.php",
				context: document.body,
				type: "POST",
				data:
				{
					consultaAjax:   '',
					accion:         'llenarSelectPlan',
					wbasedato:		$( "#wbasedato" ).val(),
					valor:          valor
				},
				async: false,
				dataType: "json",
				success:function(data) {

				if(data.error == 1)
				{

				}
				else
				{
					$("#"+selectHijo).html(data.html); // update Ok.
					//$("#div_lista_re")[0].innerHTML = data.html; // update Ok.
				}
		}
	});
}

function getNombresCamposError(){
	var mensajes = new Array();

	if( camposConError != undefined ){
		var campo = "";
		for( var i=0; i < camposConError.length; i++ ){
			campo = camposConError[i];
			campo = jQuery(campo);
			if( campo.attr("msgcampo") != "" ){
				if( mensajes.indexOf( campo.attr("msgcampo") ) == -1 ){
					mensajes.push( campo.attr("msgcampo") );
				}
			}
		}
	}
	var cadena = "";
	if( mensajes.length > 0 ){
		for( var i=0; i < mensajes.length; i++ ){
			cadena = cadena+"-"+mensajes[i]+"\n";
		}
	}
	return cadena;
}

var intentos = 0;

function enviarDatos( automatico = '')
{

	cambioDeCausaAacc = false;
	cambiarAaccTran = "off";
	if( $("#ing_caiselOriAte").val() != $("#codCausaInicial").val() && modoConsulta && $("#ing_caiselOriAte").val() == $("#codCausaAccTrans").val() ){
		cambioDeCausaAacc = true;
		cambiarAaccTran = "on";
	}
	// var filasRes=$('#tabla_eps >tbody >tr').length-2;
	// alert(filasRes);
	/*informacionIngresos == data, numRegistrosIng trae = historia(posicion) e ingreso(valor) traidos de mostrarDatos()
      informacionIngresos en la posicion de la historia que se esta mostrando con su respectivo valor y cuyo valor sea igual ==
      al ingreso que se esta mostrando y && informacionIngresos en la posicion infoIng e infoIng en la posicion actual(posAct) que
      que esta dentro de informacionIngresos todo lo anterior es:(el ingreso que se esta mostrando en pantalla )	y que este on
	  */
	/* 1.Si NO está en modo consulta se guarda la admisón
	   2.Si esta en modoConsulta se pregunta si es el último ingreso, de ser así entonces se actualiza la admisión
	*/
	     // 1.                   2.
	                 // true       &&          -                                                             ==                1                      && on == on
	//console.log( "modoConsulta "+modoConsulta +" && ultimoIngreso:"+informacionIngresos.ultimoIngreso[$( "#ing_histxtNumHis" ).val()] +" == ing_nintxtNumIng"+ $( "#ing_nintxtNumIng" ).val() +" && "+ informacionIngresos.infoing[ informacionIngresos.posAct ].pac_act +" == "+ 'on' );
	if (!modoConsulta || (modoConsulta && informacionIngresos.ultimoIngreso[$( "#ing_histxtNumHis" ).val()] == $( "#ing_nintxtNumIng" ).val() && informacionIngresos.infoing[ informacionIngresos.posAct ].pac_act == 'on' ) || ( modoConsulta && cambioDeCausaAacc ) )
	{

		var datosLlenos = $( '#forAdmisiones' ).valid();
		iniciarMarcaAqua( $( '#forAdmisiones' ) );


		if(datosLlenos )
		{
			var validacion = validarCampos( $( "#div_admisiones" ) );
			//se agrega validacion para estos datos que no son requeridos pero esta enviando el mensaje
			if ($("#ing_poltxtNumPol").val() == $( "#ing_poltxtNumPol" ).attr( "msgAqua" ))
			{ $( "#ing_poltxtNumPol" ).val( '' );}

			if ($("#ing_ncotxtNumCon").val() == $( "#ing_ncotxtNumCon" ).attr( "msgAqua" ))
			{ $( "#ing_ncotxtNumCon" ).val( '' );}

			if ($("#ing_ordtxtNumAut").val() == $( "#ing_ordtxtNumAut" ).attr( "msgAqua" ))
			{ $( "#ing_ordtxtNumAut" ).val( '' );}

			if ($("#ing_npatxtNomPerAut").val() == $( "#ing_npatxtNomPerAut" ).attr( "msgAqua" ))
			{ $( "#ing_npatxtNomPerAut" ).val( '' );}

			$("[name=ing_tpaselTipRes]").each(function(){
				if( $(this).val() == "" ){
					validacion = false;
					return false;
				}
			});

			$("[name='ing_poltxtNumPol']").each(function(){
				//alert(" joder pero porque no funciona: "+$(this).val()+" atributo aqua: "+$(this).attr("msgAqua") );
				if( $(this).val() == $(this).attr( "msgAqua" ) ){
					$(this).val('');
				}
			});
			//alert("222 joder pero porque no funciona: "+$(this).val()+" atributo aqua: "+$(this).attr("msgAqua") );

			if(validacion ){
				//A todos los campos que tengan marca de agua y esten deshabilitado, les borro la marca de agua(msgerror)
				$( "[aqua]:disabled" ).each(function(){
					if( $( this ).val() == $( this ).attr( this.aqAttr ) ){
						$( this ).val( '' );
					}
				});
				$( "[aqua]" ).each(function(){
					if( $( this ).val() == $( this ).attr( this.aqAttr ) ){
						$( this ).val( '' );
					}
				});

				var objJson = cearUrlPorCamposJson( $( "#div_admisiones" ),'id' );
				objJson = cearUrlPorCamposJson( $( "#div_admisiones" ), 'ux', objJson );
				objJson = cearUrlPorCamposJson( $( "#accidentesTransito" ), 'name', objJson );
				objJson = cearUrlPorCamposJson( $( "#eventosCatastroficos" ), 'name', objJson );
				//para guardar la relacion evento - historia -- todo lo que tenga id lo envia
				objJson = cearUrlPorCamposJson( $( "#div_eventos_catastroficos" ), 'id', objJson );
				//se envia si el checkbox esta chequeado para saber si hace la relacion o guarda un evento nuevo

				if ($("#div_eventos_catastroficos [id^=chkagregar]").is(':checked')) {
					objJson.relEvento = 'on';
				}
				else
				{
					objJson.relEvento = 'off';
				}
				//se colocan porque en las validaciones de tarifa solo se hacen para el primer resp
				var prefijo_trEps = $("tr[id$=tr_tabla_eps]").eq(0).attr("id");
				objJson.ing_tpaselTipRes =  $( "#ing_tpaselTipRes"+prefijo_trEps ).val();
				objJson.ing_cemhidCodAse =  $( "#ing_cemhidCodAse"+prefijo_trEps ).val();


				objJson.accion       = "guardarDatos";	//agrego un parametro más
				objJson.intentos     = intentos;	//reintentos
				objJson.wbasedato    = $( "#wbasedato" ).val();
				objJson.consultaAjax = "";
				objJson.historia     = $( "#ing_histxtNumHis" ).val();
				objJson.ingreso      = $( "#ing_nintxtNumIng" ).val();
				objJson.documento    = $.trim($( "#pac_doctxtNumDoc" ).val());
				documentoAnular      = $.trim($( "#pac_doctxtNumDoc" ).val());
				objJson.tipodoc      = $( "#pac_tdoselTipoDoc" ).val();
				objJson.cambioConsorcio = $("#cambioConsorcio").val();
				tipodocAnular        = $( "#pac_tdoselTipoDoc" ).val();
				objJson.habilitarPreanestesiaAD = $("#asociarPreanestesia").val();

				// --> Nuevo parametro, numero del turno. Jerson Trujillo.
				objJson.turno         = $( "#numTurnoPaciente" ).attr("valor");
				objJson.wfiniAdm      = $( "#wfiniAdm" ).val();
				objJson.whiniAdm      = $( "#whiniAdm" ).val();
				objJson.solucionCitas = $("#solucionCitas").val();//->2016-02-26
				objJson.logturCit     = $("#TurnoEnAm").val();
				/*console.log( objJson.solucionCitas );
				return;*/

				objJson.wemp_pmla = $( "#wemp_pmla" ).val();
				objJson.pacienteNN = $( "#pac_tdoselTipoDoc > option:selected" ).attr("docXhis");
				objJson.modoConsulta = modoConsulta;
				objJson.cambiarAaccTran = cambiarAaccTran;

				var esAccTransito = $("#tabla_responsables_1_2").is(":visible");
				if( $("[name=dat_Acccas_ux_acccas]").val() != "" ){
					esAccTransito = true;
				}
				var indice_res = 0;

				/*Responsables para la tabla 000205*/
				//objJson.responsables = {};
				objJson.responsables1 = {};

				//En la primera posicion del arr de responsables siempre va el responsable de transito
				if( esAccTransito ){
					if( $("#dat_AccreshidCodRes24").val() == "" ){
						alerta("No existe aseguradora SOAT, por favor verifique los datos del accidente.");
						return;
					}
					objJson.responsables1[ 0 ] = {};
					objJson.responsables1[ 0 ].res_tdo = "NIT";//2014-10-22
					objJson.responsables1[ 0 ].ing_cemhidCodAse = $("#dat_AccreshidCodRes24").val();
					objJson.responsables1[ 0 ].res_nom = $("#restxtCodRes").val();//2014-10-22
					objJson.responsables1[ 0 ].ing_tpaselTipRes = "E";
					objJson.responsables1[ 0 ].ing_plaselPlan = "00";
					if( $("input[name='dat_Accpol_ux_accpol']").val() == "Número de póliza")
						$("input[name='dat_Accpol_ux_accpol']").val("");
					objJson.responsables1[ 0 ].ing_poltxtNumPol = $("input[name=dat_Accpol_ux_accpol]").val();
					objJson.responsables1[ 0 ].ing_ncotxtNumCon = "";
					objJson.responsables1[ 0 ].ing_ordtxtNumAut = "";
				}

				$( "tr[id$=_tr_tabla_eps]" ).each(function( index )
				{
					if( esAccTransito )
						indice_res = index + 1; //En la posicion 0 esta el responsable transito
					else
						indice_res = index;
					//objJson.responsables[ index ] = cearUrlPorCamposJson(  this , 'id' );
					objJson.responsables1[ indice_res ] = cearUrlPorCamposJson(  this , 'name' );

					//2014-10-22 Si es particular, el codigo del responsable pasa a ser el numero de documento que se ingreso
					if( objJson.responsables1[ indice_res ].ing_tpaselTipRes == "P" ){
						objJson.responsables1[ indice_res ].ing_cemhidCodAse = objJson.responsables1[ indice_res ].res_doc;
					}else{
						//Si no es particular, el tipo de documento sera NIT, y el nombre el que corresponde al campo ing_cemtxtCodAse
						objJson.responsables1[ indice_res ].res_tdo = "NIT";
						objJson.responsables1[ indice_res ].res_nom = objJson.responsables1[ indice_res ].ing_cemtxtCodAse;
					}

					//objJson.responsables[ index ].cups = {};
					objJson.responsables1[ indice_res ].cups = {};
					objJson.responsables1[ indice_res ].cupsids = {};
					console.log( "antes de recopilar cups"+$(this).find("[name=ing_cachidcups]"));
					$(this).find("[name=ing_cachidcups]").each(function(index2){
						if( $(this).val() != "" ){
							objJson.responsables1[ indice_res ].cups[index2]    = $(this).val();
							objJson.responsables1[ indice_res ].cupsids[index2] = $(this).next("input[type='hidden']").val();
						}
					});

					if( index == 0 )
					{
						/*para el responsable 2 de accidente*/
						objJson.codAseR2 = $( "[id^=ing_cemhidCodAse1]", this ).val();
					}

					if (index == 1)
					{
						//para responsable 3 de accidente
						objJson.tipoEmpR3 = $( "[id^=ing_tpaselTipRes]", this ).val();
						//cuando sea empresa
						if ($( "id^=ing_tpaselTipRes", this ).val() == 'P')
						{
							objJson.codAseR3 = $( "#pac_crutxtNumDocRes" ).val();
							objJson.nomAseR3 = $( "#pac_nrutxtNomRes" ).val();
						}
						else
						{
							objJson.codAseR3 = $( "[id^=ing_cemhidCodAse]", this ).val();
							objJson.nomAseR3 = $( "[id^=ing_cemtxtCodAse]", this ).val();
						}
					}
				});
				/*Fin Responsables para la tabla 000205*/

				//DATOS DE LOS RESPONSABLES QUE VIAJAN A UNIX
				 //objJson = cearUrlPorCamposJson( $("#1_tr_tabla_eps"), 'ux', objJson );


				/*Guardar los topes por responsable*/
				objJson.topes = {};
				objJson.topesId = {};

				$( "tr[id$=_tr_tabla_topes]" ).each(function( index )
				{
					//console.log($(this).html()+"\n\n");
					objJson.topes[ index ] = cearUrlPorCamposJson(  this , 'name' );

				});
				//return;
				/*Fin Guardar los topes por responsable*/

				/****************************************************************
				 * Agosto 15 de 2013
				 *
				 * Si está activo preadmisión se guarda el dato como preadmisión
				 ****************************************************************/
				if( $( "[name=radPreAdmi]:checked" ).val() == 'preadmision' || $("input[name='btRegistrarActualizar']").val().toUpperCase() == 'PREADMITIR' )
				{
					objJson.accion = "guardarDatosPreadmision";	//agrego un parametro más
					objJson.modoConsulta = false;
				}
				/****************************************************************/

				/********************************************************
				 * Septiembre 19 de 2013
				 ********************************************************/
				//Busco los campos que son depends y están vacios con propiedad ux
				$( "[depend][ux]" ).each(function()
				{
					if( $( this ).val() == '' || ( $( this ).val() == $( this ).attr( this.aqAttr ) ) )
					{
						objJson[ $( this ).attr( "ux" ) ] = $( "#" + $( this ).attr( "depend" ) ).val();
					}
				});
				/********************************************************/

				//A todos los campos que tengan marca de agua y esten deshabilitado, le pongo la marca de agua
				$( "[aqua]:disabled" ).each(function(){
					if( $( this ).val() == '' ){
						$( this ).val( $( this ).attr( this.aqAttr ) );
					}
				});

				//RESPONSABLE QUE VIAJA A UNIX
				objJson = cearUrlPorCamposJson( $( "tr[id$=_tr_tabla_eps]" ).eq(0), 'ux', objJson );

				if( esAccTransito ){
					objJson._ux_mreemp_ux_pacemp_ux_accemp = "E";
					objJson._ux_pacres_ux_mreres = $("#dat_AccreshidCodRes24").val();
					objJson._ux_mrepla = "00";
					if( $("input[name='dat_Accpol_ux_accpol']").val() == "Número de póliza")
						$("input[name='dat_Accpol_ux_accpol']").val("");
					objJson._ux_pacpol = $("input[name=dat_Accpol_ux_accpol]").val();
				}

				if( objJson._ux_pacpol == "Digite la Poliza" )
					objJson._ux_pacpol = "";

				for( var iii in objJson.responsables1[0] ){
					objJson[ iii ] = objJson.responsables1[0][ iii ];
				}

				//2014-10-22 Siempre tiene que existir el primer responsable
				//2014-10-22if( objJson.responsables1[ 0 ].ing_tpaselTipRes == "E" ){
				if( objJson.responsables1[ 0 ].ing_cemhidCodAse == "" ){
					alerta("NO existe primer responsable, por favor verifique.");
					return;
				}
				//2014-10-22
				$.blockUI({message: "Por favor espere..."});
				$.post("admision_erp.php",
					objJson,
					function(data){
						if( automatico != "on" )
							$.unblockUI();
						if( isJSON(data) == false ){
							alerta("RESPUESTA NO ESPERADA\n"+data);
							return;
						}
						data = $.parseJSON(data);

						if( data.error == 1 )
						{
							alerta( data.mensaje );
						}
						else
						{

							if( data.mensaje != '' )
							{

								//Se oculta todos los acordeones
								//$( "[acordeon]" ).accordion( "option", "active", false );

								//Se muestra el acordeon de DATOS DE INGRESO - DATOS PERSONALES
								//$( "#div_datosIng_Per_Aco" ).accordion( "option", "active", 0 );

								try{
									window.scrollTo(0,0);
								}catch(e){}

								if( data.historia != '' && data.mensaje != "No se actualizo porque no se registraron cambios" )
								{

									if(objJson.accion=='guardarDatosPreadmision')
									{
										var esadmision = 'no';
									}
									else
									{
										var esadmision = 'si';
									}
											//alert("inserto los topes");
											$(".grabarCuandoAdmite").each(function (){


													//grabartoperesponsable($whistoria,$wingreso,$responsable,$insertar,$activo);
													activo ='on';
													$.post("admision_erp.php",
													{
														accion      : "insertarTopes",
														consultaAjax: '',
														whistoria: data.historia,
														wingreso:  data.ingreso,
														responsable: $(this).attr('responsable'),
														wemp_pmla: $('#wemp_pmla').val(),
														insertar: $(this).val(),
														activo: activo,
														esadmision: esadmision,
														documento: $.trim($("#pac_doctxtNumDoc").val()),
														tipodocumento: $("#pac_tdoselTipoDoc").val()

													}, function(data){
														//alert("entro1");
													});


											});





									//location.reload();

									if($("#soportesautomaticos").val()=='on')
									{




											//-- se agregavalidacion de soportes digitalizados
											//-- si la validacion de digitalizacion , si ya la digilitalizacion esta encendida
											//---si ya el centro de costos esta en on para la digitalizacion
											//---si ya la empresa hace la digtalizacion   no se piden soportes
											//-- parametro de digitalizacion apagado , pido soportes(programa viejo)
											if($("#parametroDigitalizacion").val() =='on')
											{

																	// si el parametro esta encendido tengo que mirar igual si la empresa y el centro de costos piden digitalizacion




												var responsable1 = objJson.responsables1[ 0 ].ing_cemhidCodAse;
												var  Empresadigitalizacion;
												var todosdigitalizacion;
												var ccodigitalizacion;
												var data2 ='';
												$.ajax(
												{
													url: "admision_erp.php",
													context: document.body,
													type: "POST",
													data:
													{
														accion      : "empresahacedigitalizacion",
														consultaAjax: '',
														wemp_pmla: $('#wemp_pmla').val(),
														empresa:   responsable1
													},
													async: false,
													success:function(data) {
														if(data=='')
														{
															Empresadigitalizacion='off';
														}
														else
														{
															Empresadigitalizacion='on';
															if(data=='*')
															{

																todosdigitalizacion ='si';
															}
															else
															{

																todosdigitalizacion ='no';
																ccodigitalizacion = data.split(',');
															}

														}
														data2=data;

													}
												});


												var ccoingreso = $("#ing_seisel_serv_ing").val();
												ccoingreso = ccoingreso.split('-');


												if(Empresadigitalizacion=='on')
												{
													if(data2=='*')
													{
														//alert(" empresa en on y * no muestre soportes");
														setTimeout( function()
															{
																//se llenan los campos de historia,ingreso,documento despues de guardar
																$("#ing_histxtNumHis").val(data.historia);
																$("#ing_nintxtNumIng").val(data.ingreso);
																$("#pac_doctxtNumDoc").val(data.documento);
																//se ponen documento,historia,ingreso readonly
																$('#pac_doctxtNumDoc').attr("readonly", true);
																$('#ing_histxtNumHis').attr("readonly", true);
																$('#ing_nintxtNumIng').attr("readonly", true);



																//Si se registró muestro se imprime el sticker
																if( data.historia != '' && data.mensaje != "No se actualizo porque no se registraron cambios" )
																{
																	console.log(" debería imprimir el sticker ");
																	var edad = calcular_edad_detalle( $( "#pac_fnatxtFecNac" ).val() );


																	var wtip = 0;

																	if( edad.age == 0 && edad.month <= 6 )
																	{
																		wtip = 2;
																	}
																	else if( edad.age <= 12  )
																	{
																		wtip = 1;
																	}

																	if( data.mensaje != "Se actualizo correctamente" ){
																		try {

																			imprimirHistoria = $("#chk_imprimirHistoria").is(":checked");
																			wbasedatoImp     = $("#wbasedatoImp").val();
																			if( imprimirHistoria ){
																				winSticker = window.open( "../../ips/reportes/r001-admision.php?wpachi="+data.historia+"&wingni="+data.ingreso+"&empresa="+wbasedatoImp );

																			}

																			if( $("#imprimirSticker").val() == "on" ){
																				winSticker = window.open("../../movhos/reportes/sticker_HC100.php?wtip="+wtip+"&whis="+data.historia ,'','fullscreen=no, status=no, menubar=no, toolbar=no, directories=no, resizable=yes, scrollbars=yes,titlebar=yes,width=600,height=600');
																				winSticker.onload = function()
																				{
																					$( "input:radio[value="+wtip+"]", winSticker.document ).attr( "checked", true );
																				}
																			}
																			//}

																			//Checkeo el radio button correspondiente de la ventana emergente
																		}catch(err){
																			alerta("Para imprimir el sticker, debe habilitar la opcion de abrir ventanas emergentes.");
																		}
																	}

																	hayCambios = false;
																}

																alerta( data.mensaje );


																if( data.mensaje == "Se actualizo correctamente" ){
																	hayCambios = false;
																	mostrarPreadmision();
																}
																else{
																	//Se Inicia el formulario

																	if( $("#AgendaMedica").val() == "on"){

																	}




																}

																hayCambios = false;
																location.reload();

															}, 200

															);
														//location.reload();
													}
													else
													{
														if($.inArray( ccoingreso[1] , ccodigitalizacion ) != -1 )
														{
															//alert("empresa en on y cco "+ccoingreso[1]+" no muestre soportes");
															setTimeout( function()
															{
																//se llenan los campos de historia,ingreso,documento despues de guardar
																$("#ing_histxtNumHis").val(data.historia);
																$("#ing_nintxtNumIng").val(data.ingreso);
																$("#pac_doctxtNumDoc").val(data.documento);
																//se ponen documento,historia,ingreso readonly
																$('#pac_doctxtNumDoc').attr("readonly", true);
																$('#ing_histxtNumHis').attr("readonly", true);
																$('#ing_nintxtNumIng').attr("readonly", true);



																//Si se registró muestro se imprime el sticker
																if( data.historia != '' && data.mensaje != "No se actualizo porque no se registraron cambios" )
																{
																	console.log(" debería imprimir el sticker ");
																	var edad = calcular_edad_detalle( $( "#pac_fnatxtFecNac" ).val() );


																	var wtip = 0;

																	if( edad.age == 0 && edad.month <= 6 )
																	{
																		wtip = 2;
																	}
																	else if( edad.age <= 12  )
																	{
																		wtip = 1;
																	}

																	if( data.mensaje != "Se actualizo correctamente" ){
																		try {

																			imprimirHistoria = $("#chk_imprimirHistoria").is(":checked");
																			wbasedatoImp     = $("#wbasedatoImp").val();
																			if( imprimirHistoria ){
																				winSticker = window.open( "../../ips/reportes/r001-admision.php?wpachi="+data.historia+"&wingni="+data.ingreso+"&empresa="+wbasedatoImp );

																			}

																			if( $("#imprimirSticker").val() == "on" ){
																				winSticker = window.open("../../movhos/reportes/sticker_HC100.php?wtip="+wtip+"&whis="+data.historia ,'','fullscreen=no, status=no, menubar=no, toolbar=no, directories=no, resizable=yes, scrollbars=yes,titlebar=yes,width=600,height=600');
																				winSticker.onload = function()
																				{
																					$( "input:radio[value="+wtip+"]", winSticker.document ).attr( "checked", true );
																				}
																			}
																			//}

																			//Checkeo el radio button correspondiente de la ventana emergente
																		}catch(err){
																			alerta("Para imprimir el sticker, debe habilitar la opcion de abrir ventanas emergentes.");
																		}
																	}

																	hayCambios = false;
																}

																alerta( data.mensaje );


																if( data.mensaje == "Se actualizo correctamente" ){
																	hayCambios = false;
																	mostrarPreadmision();
																}
																else{
																	//Se Inicia el formulario

																	if( $("#AgendaMedica").val() == "on"){

																	}

																	location.reload();


																}

																hayCambios = false;
																location.reload();

															}, 200
															);

														}
														else
														{
														   //alert("empresa en on y muestre");

														   crearListaAutomatica(data.historia , data.ingreso,'','',data.documento,data.mensaje );

														}
													}

												}
												else
												{
													//alert("muestre soportes");
													crearListaAutomatica(data.historia , data.ingreso,'','',data.documento,data.mensaje );
												}




											}
											else
											{
													crearListaAutomatica(data.historia , data.ingreso,'','',data.documento,data.mensaje );

											}


										//--
									}else{
										if( data.mensaje == "Se actualizo correctamente" ){
											hayCambios = false;
											mostrarPreadmision();
										}
									}

								}
								else
								{
									if(objJson.accion=='guardarDatosPreadmision')
									{
										$(".grabarCuandoAdmite").each(function (){


													//grabartoperesponsable($whistoria,$wingreso,$responsable,$insertar,$activo);
													activo ='on';
													$.post("admision_erp.php",
													{
														accion      : "insertarTopes",
														consultaAjax: '',
														whistoria: data.historia,
														wingreso:  data.ingreso,
														responsable: $(this).attr('responsable'),
														wemp_pmla: $('#wemp_pmla').val(),
														insertar: $(this).val(),
														activo: activo,
														esadmision: esadmision,
														documento: $.trim($("#pac_doctxtNumDoc").val()),
														tipodocumento: $("#pac_tdoselTipoDoc").val()

													}, function(data){
														//alert("entro1");
													});


										});
										//alert("grabo preadmision");
										location.reload();
									}
								}
								/*setTimeout( function()
									{
										//se llenan los campos de historia,ingreso,documento despues de guardar
										$("#ing_histxtNumHis").val(data.historia);
										$("#ing_nintxtNumIng").val(data.ingreso);
										$("#pac_doctxtNumDoc").val(data.documento);
										//se ponen documento,historia,ingreso readonly
										$('#pac_doctxtNumDoc').attr("readonly", true);
										$('#ing_histxtNumHis').attr("readonly", true);
										$('#ing_nintxtNumIng').attr("readonly", true);



										//Si se registró muestro se imprime el sticker
										if( data.historia != '' && data.mensaje != "No se actualizo porque no se registraron cambios" )
										{
											console.log(" debería imprimir el sticker ");
											var edad = calcular_edad_detalle( $( "#pac_fnatxtFecNac" ).val() );


											var wtip = 0;

											if( edad.age == 0 && edad.month <= 6 )
											{
												wtip = 2;
											}
											else if( edad.age <= 12  )
											{
												wtip = 1;
											}

											if( data.mensaje != "Se actualizo correctamente" ){
												try {

													imprimirHistoria = $("#chk_imprimirHistoria").is(":checked");
													wbasedatoImp     = $("#wbasedatoImp").val();
													if( imprimirHistoria ){
														winSticker = window.open( "../../ips/reportes/r001-admision.php?wpachi="+data.historia+"&wingni="+data.ingreso+"&empresa="+wbasedatoImp );

													}

													if( $("#imprimirSticker").val() == "on" ){
														winSticker = window.open("../../movhos/reportes/sticker_HC100.php?wtip="+wtip+"&whis="+data.historia ,'','fullscreen=no, status=no, menubar=no, toolbar=no, directories=no, resizable=yes, scrollbars=yes,titlebar=yes,width=600,height=600');
														winSticker.onload = function()
														{
															$( "input:radio[value="+wtip+"]", winSticker.document ).attr( "checked", true );
														}
													}
													//}

													//Checkeo el radio button correspondiente de la ventana emergente
												}catch(err){
													alerta("Para imprimir el sticker, debe habilitar la opcion de abrir ventanas emergentes.");
												}
											}

											hayCambios = false;
										}

										alerta( data.mensaje );


										if( data.mensaje == "Se actualizo correctamente" ){
											hayCambios = false;
											mostrarPreadmision();
										}
										else{
											//Se Inicia el formulario

											if( $("#AgendaMedica").val() == "on"){

											}
											if($("#soportesautomaticos").val()=='on')
											{

											}
											else
											{
												location.reload();
											}

										}

										hayCambios = false;


									}, 200
								);*/



								if( data.error != 4 ){
										//se borran los trs de la tabla_eps menos la primera
										$("#tabla_eps").find("tr[id$='_tr_tabla_eps']").remove();
										var wbasedato = $("#wbasedato").val();
										var wemp_pmla = $("#wemp_pmla").val();
										addFila('tabla_eps',wbasedato,wemp_pmla);
										//se ponen los valores de ese tr en blanco
										$("[id^=ing_tpaselTipRes],[id^=ing_cemtxtCodAse],[id^=ing_plaselPlan]", $('#tabla_eps >tbody >tr').eq(2) ).val("");
										setTimeout( function()
										{
											borrarLog( $( "#key" ) );
										},1100);
								}

							}

							//Al guardar los datos se borra el log
							//borrarLog( $( "#key" ) );
							/*se pone ese div en blanco para que cuando guarde una admsion con
							  evento catastrofico, si guarda otra inmediatamente muestre la lista
							  de eventos en blanco.
							*/
							$("#div_contenedor").html("");
							//$( "#div_contenedor" ).empty();

							//se pone el div que contiene los topes en blanco
							// $("[id^=div_cont_tabla_topes]").html(""); //#div_cont_tabla_topes que empiece
							$("#div_contenedor_topes").html(""); //#div_cont_tabla_topes que empiece
						}
					}
				);
			}
			else{
				var campos = getNombresCamposError();
				alerta( " Hay datos incompletos, por favor verifique los campos de color amarillo \n "+campos );
			}
		}
		else{
			alerta( " Hay datos incompletos, por favor verifique los campos de color amarillo" );
		}

	}
	else
	{
		alerta("Solo se permite actualizar el ultimo ingreso");
	}
}

/**/
$(function(){
        $( '#forAdmisiones' ).validate({
            rules :{
                pac_cortxtCorEle : {
                    required : false, //para validar campo vacio
                    email    : true  //para validar formato email
				},
				pac_cretxtCorResp : {
                    required : false, //para validar campo vacio
                    email    : true  //para validar formato email
				},
				//movil del paciente
				pac_movtxtTelMov : {
						    required : {
								depends : function(element){

								if( $('#pac_teltxtTelFij').val() == $('#pac_teltxtTelFij').attr( "msgerror" ) && $('#pac_movtxtTelMov').val() == $('#pac_movtxtTelMov').attr( "msgerror" )  ){
									$('#pac_movtxtTelMov').val( '' );
								}

								return ($('#pac_teltxtTelFij').val() == $('#pac_teltxtTelFij').attr( "msgerror" ));

						}

					}
				},
				//fijo del paciente
				pac_teltxtTelFij : {
						    required : {
								depends : function(element){
								// return ($('#pac_movtxtTelMov').val() == '');
								// alert( $('#pac_movtxtTelMov').val() == $('#pac_movtxtTelMov').attr( "msgerror" ) && $('#pac_teltxtTelFij').val() == $('#pac_teltxtTelFij').attr( "msgerror" ) );
								if( $('#pac_movtxtTelMov').val() == $('#pac_movtxtTelMov').attr( "msgerror" ) && $('#pac_teltxtTelFij').val() == $('#pac_teltxtTelFij').attr( "msgerror" )  ){
									$('#pac_teltxtTelFij').val( '' );
								}

								return ($('#pac_movtxtTelMov').val() == $('#pac_movtxtTelMov').attr( "msgerror" ));
						}
					}
				},
				//direccion del paciente
				pac_dedtxtDetDir : {
						    required : {
								depends : function(element){

								if( $('#pac_dirtxtDirRes').val() == $('#pac_dirtxtDirRes').attr( "msgerror" ) && $('#pac_dedtxtDetDir').val() == $('#pac_dedtxtDetDir').attr( "msgerror" )  ){
									$('#pac_dedtxtDetDir').val( '' );
								}

								return ($('#pac_dirtxtDirRes').val() == $('#pac_dirtxtDirRes').attr( "msgerror" ));

						}

					}

				},
				//detalle dir del paciente
				pac_dirtxtDirRes : {
						    required : {
								 depends : function(element){
								// return ($('#pac_dedtxtDetDir').val() == '');
								if( $('#pac_dedtxtDetDir').val() == $('#pac_dedtxtDetDir').attr( "msgerror" ) && $('#pac_dirtxtDirRes').val() == $('#pac_dirtxtDirRes').attr( "msgerror" )  ){
									$('#pac_dirtxtDirRes').val( '' );
								}

								return ($('#pac_dedtxtDetDir').val() == $('#pac_dedtxtDetDir').attr( "msgerror" ));

						}

					}

				},
				// direccion del resp pac
				pac_ddrtxtDetDirRes : {
						    required : {
								depends : function(element){

								if( $('#pac_drutxtDirRes').val() == $('#pac_drutxtDirRes').attr( "msgerror" ) && $('#pac_ddrtxtDetDirRes').val() == $('#pac_ddrtxtDetDirRes').attr( "msgerror" )  ){
									$('#pac_ddrtxtDetDirRes').val( '' );
								}
								return ($('#pac_drutxtDirRes').val() == $('#pac_drutxtDirRes').attr( "msgerror" ));

						}

					}

				},
				//detalle dir del resp pac
				pac_drutxtDirRes : {
						    required : {
								 depends : function(element){
								// return ($('#pac_ddrtxtDetDirRes').val() == '');
								if( $('#pac_ddrtxtDetDirRes').val() == $('#pac_ddrtxtDetDirRes').attr( "msgerror" ) && $('#pac_drutxtDirRes').val() == $('#pac_drutxtDirRes').attr( "msgerror" )  ){
									$('#pac_drutxtDirRes').val( '' );
								}
								return ($('#pac_ddrtxtDetDirRes').val() == $('#pac_ddrtxtDetDirRes').attr( "msgerror" ));

						}

					}

				},
				//movil resp pac
				pac_mortxtNumResp : {
						    required : {
								depends : function(element){

								if( $('#pac_trutxtTelRes').val() == $('#pac_trutxtTelRes').attr( "msgerror" ) && $('#pac_mortxtNumResp').val() == $('#pac_mortxtNumResp').attr( "msgerror" )  ){
									$('#pac_mortxtNumResp').val( '' );
								}
								return ($('#pac_trutxtTelRes').val() != $('#pac_trutxtTelRes').attr( "msgerror" ));


						}

					}

				},
				//fijo resp pac
				pac_trutxtTelRes : {
						    required : {
								depends : function(element){

								if( $('#pac_mortxtNumResp').val() == $('#pac_mortxtNumResp').attr( "msgerror" ) && $('#pac_trutxtTelRes').val() == $('#pac_trutxtTelRes').attr( "msgerror" )  ){
									$('#pac_trutxtTelRes').val( '' );
								}
								return ($('#pac_mortxtNumResp').val() != $('#pac_mortxtNumResp').attr( "msgerror" ));
						}

					}

				}

            }, //rules
            messages : {
                pac_cortxtCorEle : {
                    // required : "Debe ingresar el email",
                    email    : "Debe ingresar un email valido"
                },
				pac_cretxtCorResp : {
                    // required : "Debe ingresar el email",
                    email    : "Debe ingresar un email valido"
                },

				pac_movtxtTelMov : "Debe ingresar el telefono fijo o el telefono movil", //movil pac
				pac_teltxtTelFij : "Debe ingresar el telefono fijo o el telefono movil", //fijo pac
				pac_dedtxtDetDir : "Debe ingresar la direccion o el detalle de la direccion", //dir pac
				pac_dirtxtDirRes : "Debe ingresar la direccion o el detalle de la direccion", //detalle dir pac
				pac_ddrtxtDetDirRes : "Debe ingresar la direccion o el detalle de la direccion", //dir resp pac
				pac_drutxtDirRes : "Debe ingresar la direccion o el detalle de la direccion", //detalle dir resp pac
				pac_mortxtNumResp : "Debe ingresar el telefono fijo o el telefono movil", //movil resp SI SE DESCOMENTA MIRAR LA ,
				pac_trutxtTelRes : "Debe ingresar el telefono fijo o el telefono movil" //fijo resp




            }, //messages
			errorClass : "errorMensajes"
			// validClass: "mensajeValido"
        });
    });
/**/

function crearlista(){

	var flujo ='';
	var plan ='';
	$(".radioplan").each(function (){

			if($(this).is(":checked")) {

				plan = $(this).val() ;
			}
	});


	$(".radioflujo").each(function (){

			if($(this).is(":checked")) {

				flujo = $(this).val();

			}
	});

	if(plan!='' && flujo!='' )
	{

		crearListaAutomatica($("#historiamodal").val(), $("#ingresomodal").val(), flujo , plan );
	}


}

function crearListaAutomatica(whistoria , wingreso , wflujo='' , wplan='' , documento, mensaje)
{


	$.post("admision_erp.php",
	{
		accion      : "crearListaAutomatica",
		consultaAjax: '',
		wemp_pmla: $('#wemp_pmla').val(),
		whistoria: whistoria,
		wingreso : wingreso,
		wflujo   : wflujo,
		wplan    : wplan
	}, function(data){
		if(data.html == 'abrirModal' )
		{

			$("#divPlanyFlujo").html(data.contenidoDiv+"<div id='datoppales'><input type='hidden' id='datosppaleshistoria' value='"+whistoria+"'><input type='hidden' id='datosppalesingreso' value='"+wingreso+"'><input type='hidden' id='datosppalesdocumento' value='"+documento+"'><input type='hidden' id='datosppalesmensaje' value='"+mensaje+"'>").show().dialog({
					dialogClass: 'fixed-dialog',
					modal: true,
					title: "<div align='center' style='font-size:10pt'>Seleccion de Flujo y Plan</div>",
					width: "auto",
					height: "300",
					closeOnEscape: false,
					open: function(event, ui) { $(".ui-dialog-titlebar-close", ui.dialog).hide(); }

			});

			$("#divPlanyFlujo").css({
              width:'auto', //probably not needed
              height:'auto'
			});

			$( "#divPlanyFlujo" ).dialog({ dialogClass: 'hide-close' });

		}else{
			// cambio de camilo


			if($("#divPlanyFlujo").is(":visible"))
			{

			}else
			{
				location.reload();
			}

		}

		if(data.exito == 'si')
		{
			$("#divPlanyFlujo").css({
              width:'auto', //probably not needed
              height:'auto'
			});
			$("#divplanesyflujos").html('');
			$("#divexito").html('');
			$("#divexito").html(data.tabla+"<br><br><center><table><tr><td nowrap=nowrap><input   type='button' value='Enviar a siguiente nivel' onclick='enviarnivelsiguiente()'><input   type='button' value='Cerrar' onclick='onloadDesdeSoportes()'></td></tr></table></center>");
		}
	}, 'json');



}

function onloadDesdeSoportes()
{

  historia = $("#datosppaleshistoria").val();
  ingreso = $("#datosppalesingreso").val();
  documento = $("#datosppalesdocumento").val();
  mensaje = $("#datosppalesmensaje").val();
  mensaje = $("#datosppalesmensaje").val();


  var u = 0;
 var variable ='';

 $(".checkboxlista").each(function (){


	if( $(this).attr('checked') ) {

	}
	else
	{
		 u++;
		 if(u==1)
		 {
			variable += ' AND ( delsop="'+$(this).attr('soporte')+'"' ;
		 }
		 else
			variable += ' OR delsop="'+$(this).attr('soporte')+'"';

	}

  });
			if (u != 0)
			{
				variable +=")";
			}

			//alert(variable);

			$.post("admision_erp.php",
			{
				accion      : "naautomatico",
				consultaAjax: '',
				wemp_pmla: $('#wemp_pmla').val(),
				whistoria: historia,
				wingreso : ingreso,
				opciones : variable
			}, function(data){

				//alert(data);

			});

  setTimeout( function()
  {
			//se llenan los campos de historia,ingreso,documento despues de guardar
			$("#ing_histxtNumHis").val(historia);
			$("#ing_nintxtNumIng").val(ingreso);
			$("#pac_doctxtNumDoc").val(documento);
			//se ponen documento,historia,ingreso readonly
			$('#pac_doctxtNumDoc').attr("readonly", true);
			$('#ing_histxtNumHis').attr("readonly", true);
			$('#ing_nintxtNumIng').attr("readonly", true);



			//Si se registró muestro se imprime el sticker
			if( historia != '' && mensaje != "No se actualizo porque no se registraron cambios" )
			{
				console.log(" debería imprimir el sticker ");
				var edad = calcular_edad_detalle( $( "#pac_fnatxtFecNac" ).val() );


				var wtip = 0;

				if( edad.age == 0 && edad.month <= 6 )
				{
					wtip = 2;
				}
				else if( edad.age <= 12  )
				{
					wtip = 1;
				}

				if( mensaje != "Se actualizo correctamente" ){
					try {

						imprimirHistoria = $("#chk_imprimirHistoria").is(":checked");
						wbasedatoImp     = $("#wbasedatoImp").val();
						if( imprimirHistoria ){
							winSticker = window.open( "../../ips/reportes/r001-admision.php?wpachi="+historia+"&wingni="+ingreso+"&empresa="+wbasedatoImp );

						}

						if( $("#imprimirSticker").val() == "on" ){
							winSticker = window.open("../../movhos/reportes/sticker_HC100.php?wtip="+wtip+"&whis="+historia ,'','fullscreen=no, status=no, menubar=no, toolbar=no, directories=no, resizable=yes, scrollbars=yes,titlebar=yes,width=600,height=600');
							winSticker.onload = function()
							{
								$( "input:radio[value="+wtip+"]", winSticker.document ).attr( "checked", true );
							}
						}
						//}

						//Checkeo el radio button correspondiente de la ventana emergente
					}catch(err){
						alerta("Para imprimir el sticker, debe habilitar la opcion de abrir ventanas emergentes.");
					}
				}

				hayCambios = false;
			}

			alerta( mensaje );


			if( mensaje == "Se actualizo correctamente" ){
				hayCambios = false;
				mostrarPreadmision();
			}
			else{
				//Se Inicia el formulario

				if( $("#AgendaMedica").val() == "on"){

				}
				if($("#soportesautomaticos").val()=='on')
				{

				}
				else
				{
					location.reload();
				}

			}

			hayCambios = false;


			  if( $("#AgendaMedica").val() == "on")
			  {
				  $("#divPlanyFlujo").dialog('close');
				  window.close();
				  // return;
			  }
			  location.reload();
			  $("#divPlanyFlujo").dialog('close');



		}, 200
	);


  /*

  */
}

function enviarnivelsiguiente()
{
	historia = $("#datosppaleshistoria").val();
	ingreso = $("#datosppalesingreso").val();

	$.post("admision_erp.php",
			{
				accion      : "enviarnivelsiguiente",
				consultaAjax: '',
				wemp_pmla: $('#wemp_pmla').val(),
				whis: historia,
				wing : ingreso,
			}, function(data){

				alert("los soportes fueron enviados al siguiente nivel");
				onloadDesdeSoportes();

			});

}

function mostrarDatos( documento, ingreso, tipoDocumento )
{

	if( documento )
	{
		iniciar();
		// $( "#radAdmision" ).attr( "checked", true );
		// $( "#radAdmision" ).click();
		mostarOcultarDatosPreadmisiones( false );
		$( "#pac_doctxtNumDoc" ).val( documento );
		$( "#pac_tdoselTipoDoc" ).val( tipoDocumento );
	}

	if( ingreso == undefined || ingreso == "" )
		ingreso = $( "#ing_nintxtNumIng" ).val();

	if( $( "input[name='btRegistrarActualizar']" ).attr( "actualiza" ) == "on" ){
		$( "input[name='btRegistrarActualizar']" ).attr( "disabled", false );
	}else{
		$( "input[name='btRegistrarActualizar']" ).attr( "disabled", true );
	}
	$( "input[name='btRegistrarActualizar']" ).val( "Actualizar" );

	$( "#radAdmision" ).attr( "checked", true );
	$( "#radAdmision" ).click();

	//Variable para saber si esta en modo consulta o no
	modoConsulta = true;

		var objJson = cearUrlPorCamposJson( $( "#div_admisiones" ),'id' );

		objJson.accion = "mostrarDatosAlmacenados";	//agrego un parametro más
		objJson.wbasedato = $( "#wbasedato" ).val();
		objJson.consultaAjax = "";
		objJson.historia = $( "#ing_histxtNumHis" ).val();
		objJson.ingreso = ingreso;
		objJson.documento = $.trim($( "#pac_doctxtNumDoc" ).val());
		objJson.wemp_pmla = $( "#wemp_pmla" ).val();
		objJson.mostrarTipoServicio = $("#mostrarTipoServicio").val();

		/*validacion de todos los input para saber si tienen el mesaje de error
		 y si lo tiene se envia vacio*/
		$('input').each(function(n)
		{
			var id =this.id;
			var valor = $("#"+id).val();
			// var valormsgerror = $("#"+id).attr( "msgerror" );
			if( this.aqAttr )	//Solo si su valor es igual a la marca de agua, ya se mensaje de error(msgerror) o no
			{
				var valormsgerror = $("#"+id).attr( this.aqAttr );	//Se Busca la marca de agua

				if(valor == valormsgerror)
				{
					objJson[ id ] = '';
				}
			}
		});

		//Si el documento esta vació mando el numero de documento vacio
		if( $.trim($( "#pac_doctxtNumDoc" ).val()) == '' || $.trim($( "#pac_doctxtNumDoc" ).val()) == $( "#pac_doctxtNumDoc" ).attr( "msgerror" ) )
		{
			objJson.documento = objJson.pac_doctxtNumDoc;
		}

		$.blockUI({message: "Por favor espere..."});

		$.post("admision_erp.php",
			objJson,
			function(data){
				$.unblockUI();
				if( isJSON(data) == false ){
					alerta("RESPUESTA NO ESPERADA\n"+data);
					return;
				}
				data = $.parseJSON(data);

				if( data.error == 1 )
				{
					alerta( data.mensaje );
				}
				else
				{
					if( data.mensaje != '' )
						alerta( data.mensaje );


					if( data.infoing )
					{
						informacionIngresos = data;
						informacionIngresos.regTotal = data.infoing.length;
						informacionIngresos.posAct = data.infoing.length-1;

						if (informacionIngresos.regTotal>0)
						{
							$("#bot_navegacion").css("display", "");
							$("#bot_navegacion1").css("display", "");
						}
						else
						{
							$("#bot_navegacion").css("display", "none");
							$("#bot_navegacion1").css("display", "none");
						}

						navegacionIngresos( 0 );

						//Despues de que consulte se borra el log
						borrarLog( $( "#key" ) );
						//se colocan los campos cedula,historia,ingreso read only para que no se puedan modificar
						$("#pac_doctxtNumDoc").attr("readonly", true);
						$("#ing_histxtNumHis").attr("readonly", true);
						$("#ing_nintxtNumIng").attr("readonly", true);

						$("[name=ing_cemtxtCodAse]").each(function(){ //Quitar el color amarillo cuando ya tiene valor
							if($(this).val() != "" ){
								resetAqua( $(this).parent() );
							}
						});

						//Agosto 23 de 2013
						$( "#ing_seisel_serv_ing" ).change();
						if( $("#pac_dle").val() != "" ){
							$(".tr_pacienteExtranjero").show();
						}
						validacionServicioIngreso( "cargaDatosAlmacenados" );
						hayCambios = false;
					}
					console.log( "tipo de afiliacion "+data.infopac.pac_taf )
					$("#pac_tafselTipAfi > option[value!='']").remove();
					option      = $("#pac_tafselTipAfi_original > option[value='"+data.infopac.pac_taf+"']").clone();
					$(option).attr("selected", "selected");
					$("#pac_tafselTipAfi").append(option);
					$("#pac_tafselTipAfi").removeAttr('campoRequerido');
				}
			}
		);

}

var informacionIngresos = '';

function navegacionIngresos( incremento )
{

	var data = informacionIngresos;

	if( data.posAct + incremento < informacionIngresos.regTotal && data.posAct + incremento >= 0 )
	{
		data.posAct = data.posAct + incremento;

		$("#tabla_eps").find("tr[id$='_tr_tabla_eps']").remove();
		var wbasedato = $("#wbasedato").val();
		var wemp_pmla = $("#wemp_pmla").val();
		addFila('tabla_eps',wbasedato,wemp_pmla);

		// setDatos( data.infopac, $( "#div_admisiones" ), 'id' )  ;
		setDatos( data.infoing[ data.posAct ], $( "#div_admisiones" ), 'id' )  ;
		setDatos( data.infoing[ data.posAct ], $( "#accidentesTransito" ), 'name' )  ;
		setDatos( data.infoing[ data.posAct ], $( "#eventosCatastroficos" ), 'name' )  ;
		/* se llena el div_contenedor con la lista de eventos que se trae de mostrarDatosAlmacenados que esta en el array
			data.infoing en el resultado actual y la posicion htmlEventos*/
		$("#div_contenedor").html(data.infoing[ data.posAct ][ 'htmlEventos' ] );

		calcular_edad(data.infoing[ data.posAct ].pac_fna);
		validarPacienteRem();


		//Muestra datos para el navegador inferior
		$("#spTotalReg").html(data.numRegistrosPac);// numero de registros encontrados en la busqueda
		$("#spTotalIng").html(data.numRegistrosIng[ data.infoing[ data.posAct ].pac_his ] ); //total ingresos encontrados
		$("#spRegAct").html(data.numPosicionHistorias[ data.infoing[ data.posAct ].pac_his ] +1); //resultado actual

		$("#spHisAct").html( data.infoing[ data.posAct ].pac_his); //historia del registro actual
		$("#spIngAct").html( data.infoing[ data.posAct ].ing_nin );	//ingreso actual del registro actual
		$("#spTotalIng1").html(data.numRegistrosIng[ data.infoing[ data.posAct ].pac_his ] ); //total ingresos por historia

		//Muestra datos para el navegador superior
		$("#spTotalReg1").html(data.numRegistrosPac);// numero de registros encontrados en la busqueda
		$("#spTotalIng1").html(data.numRegistrosIng[ data.infoing[ data.posAct ].pac_his ] ); //total ingresos encontrados
		$("#spRegAct1").html(data.numPosicionHistorias[ data.infoing[ data.posAct ].pac_his ] +1); //resultado actual

		$("#spHisAct1").html( data.infoing[ data.posAct ].pac_his); //historia del registro actual
		$("#spIngAct1").html( data.infoing[ data.posAct ].ing_nin );	//ingreso actual del registro actual
		$("#spTotalIng11").html(data.numRegistrosIng[ data.infoing[ data.posAct ].pac_his ] ); //total ingresos por historia

		$("#spEstPac").removeClass("estadoInactivo estadoActivo");//se le quita antes la clase que tiene para colocarle la nueva
		 var estPac = data.infoing[ data.posAct ].pac_act;//se trae el estado del paciente
		 if (estPac == 'off')
		 {
			estPac= "INACTIVO";
			$("#spEstPac").addClass("estadoInactivo");
		 }
		 else
		 {
			estPac = "ACTIVO";
			$("#spEstPac").addClass("estadoActivo");
		 }
		$("#spEstPac").html( estPac); //se envia Estado del paciente

		if( $( "#ing_caiselOriAte" ).val() == '02' ){
			$( "#div_accidente_evento").css( {display: ""} );
			$( "td", $( "#div_accidente_evento") ).eq(0).css( {display: ""} );	//Mostrar accidentes de transito
			$( "td", $( "#div_accidente_evento") ).eq(1).css( {display: "none"} );	//oculto el boton de eventos catastróficos
			$( "td", $( "#div_accidente_evento") ).eq(2).css( {display: "none"} ); //oculto el boton de listar eventos catastróficos

			var objJson = cearUrlPorCamposJson( $( "#infDatosAcc" ) );
			var contenedor = $( "#accidentesTransito" )[0];


			if( data.infoing[ data.posAct ].dat_Accrei ){
				$("#accidente_previo").val( data.infoing[ data.posAct ].dat_Accrei );
			}

			contenedor.lastInfo = objJson;

			var objetoRes = $("[name=dat_Accase_ux_accase]").eq(0);
			validarEstadoAseguramiento(objetoRes[0]);
		}
		else if( $( "#ing_caiselOriAte" ).val() == '06' ){

			//se crea una variable global para saber si esta consultando eventos
			consultaEvento = true;
			$( "#div_accidente_evento").css( {display: ""} );
			$( "td", $( "#div_accidente_evento") ).eq(0).css( {display: "none"} );	//oculto el boton de accidentes de transito
			$( "td", $( "#div_accidente_evento") ).eq(1).css( {display: ""} );	//mostrar el boton de eventos catastróficos
			$( "td", $( "#div_accidente_evento") ).eq(2).css( {display: ""} ); //se muestra el tercer boton

			var objJson = cearUrlPorCamposJson( $( "#eventosCatastroficos" ) );
			var contenedor = $( "#eventosCatastroficos" )[0];

			contenedor.lastInfo = objJson;
		}
		else{
			$( "#div_accidente_evento").css( {display: "none"} );
		}
		$("#codCausaInicial").val( $( "#ing_caiselOriAte" ).val() );
		//para que cuando sea empresa muestre habilitados los datos de autorizacion
		//---validarTipoResp('');

		resetAqua( );
		//se simula el onblur para quitar el requerido cuando el que depende esta lleno
		 $( "[depend]" ).blur();


		/** para mostrar responsables**/
		if (data.infoing[ data.posAct ]['responsables'] === undefined)
		{

		}
		else
		{  //trae datos de los responsables

			for ( var i=0; i<data.infoing[ data.posAct ]['responsables'].length-1;i++)
			{
				addFila('tabla_eps',$("#wbasedato").val(),$("#wemp_pmla").val());
				//addResponsable();
			}

			for ( var i=0; i<data.infoing[ data.posAct ]['responsables'].length;i++)
			{
				var responsables = data.infoing[ data.posAct ]['responsables'][i];
				var fila = $( "#tabla_eps" )[0].rows[1+i];
				setDatos( responsables, fila, 'name' );

				llenarPlan($( "#ing_cemhidCodAse"+(i*1+1)+"_tr_tabla_eps").val(), 'ing_plaselPlan'+(i*1+1)+"_tr_tabla_eps");
				$( "#ing_plaselPlan"+(i*1+1)+"_tr_tabla_eps" ).val( responsables.ing_plaselPlan ).removeClass("campoRequerido");


				//Construir la cantidad de cups necesarios y llevarles el valor
				if( responsables.cups != undefined ){
					//console.log( "Con cups "+ responsables.ing_cemtxtCodAse  );
					var idUltTr = (i+1)+"_tr_tabla_eps";
					for( var ii=0; ii<responsables.cups.length; ii++){
						//Se crea el input text (nombre del cup) y el input hide (codigo del cup) dentro del tr
						if( ii != 0 )
							agregarCUPS( idUltTr );
						var lastTr = $("#"+idUltTr);
						lastTr.find("input[name=ing_cactxtcups]").eq(ii).val( responsables.cups[ii].codigo+"-"+responsables.cups[ii].nombre ); //nombre del cup
						lastTr.find("input[name=ing_cachidcups]").eq(ii).val( responsables.cups[ii].codigo ); //codigo del cup
						lastTr.find("input[name=id_idcups]").eq(ii).val( responsables.cups[ii].id ); //codigo del cup
					}
				}else{
					//console.log( "sin cups "+ responsables.ing_cemtxtCodAse  );
				}

				var objetoRes = $("[name=ing_tpaselTipRes]").eq(i);
				if( objetoRes != undefined ) validarTipoResp(objetoRes[0]);
			}
			resetAqua( $("#tabla_eps") );
			reOrdenarResponsables();
			$("#tabla_eps > tbody").find("input[type='radio'][name='res_comtxtNumcon']:checked").attr("estadoAnterior", "on");
			$("#tabla_eps > tbody").find("input[type='radio'][name='res_comtxtNumcon']").each( function(){
				console.log( $(this).is(":checked") );
			})
		}
		/**Fin datos responsables**/


		//var objetoRes = $("[name=ing_tpaselTipRes]").eq(0);
		//console.log("es aqui "+objetoRes[0].value);
		//validarTipoResp(objetoRes[0]);

		/** para mostrar topes**/
		if (data.infoing[ data.posAct ]['topes'] === undefined)
		{

		}
		else
		{  //trae datos de los topes
			var topes="";
			var fila="";

						//codigo responsable
						// var resp = data.infoing[ data.posAct ]['topes'][i]['top_reshidTopRes'];
						// //para traer todo el objeto que contiene ese codigo de responsable
						// var objResp= $( "#tabla_eps input[value='"+resp+"']" );
						// //extraer el id
						// var idResp =objResp[0].id;
						// /*aqui se debe llamar una funcion que cree la tabla topes para que
						// no saque error que esta undefined*/
						// mostrarDivTopes1(idResp);

					//se comienza i desde 1 porque la primera fila ya esta, es para que no agregue una demas
					// for ( var i=1; i<data.infoing[ data.posAct ]['topes'].length;i++)
						// {
						// // alert(data.infoing[ data.posAct ]['topes'][i]['total']);
							// if (data.infoing[ data.posAct ]['topes'][i]['total'] == 'off')
							// {
								// addFila('tabla_topes',wbasedato.value,wemp_pmla.value);
							// }
						// }
					//console.log("TOPES------: ");
					//console.log(data.infoing[ data.posAct ]['topes']);
			for ( var k=0; k<data.infoing[ data.posAct ]['responsables'].length;k++)
			{
					var agregarFila = false;
					for ( var i=0,j=0; i<data.infoing[ data.posAct ]['topes'].length;i++)
					{
						//codigo responsable
						var resp = data.infoing[ data.posAct ]['topes'][i]['top_reshidTopRes'];
						//para traer todo el objeto que contiene ese codigo de responsable
						var objResp= $( "#tabla_eps input[value='"+resp+"']" );
						//extraer el id
						var idResp =objResp[0].id;

						if( resp == data.infoing[ data.posAct ]['responsables'][k]['ing_cemhidCodAse'] )
						{
							if(j == 0){
								//mostrarDivTopes1(idResp);
							}


							if( j > 0 ){
								//console.log( "está entrando a agregar fila para tope" );
								if (data.infoing[ data.posAct ]['topes'][i]['total'] == 'off' && agregarFila == true)
								{
									idCodResp = idResp;
									addFila('tabla_topes',wbasedato,wemp_pmla.value);
								}
							}

							if (data.infoing[ data.posAct ]['topes'][i]['total'] == 'off')
							{
								/*//console.log("TOPES TOTAL OFF "+idResp);
								topes = data.infoing[ data.posAct ]['topes'][i];
								//fila = $( "#tabla_topes", $("#div_cont_tabla_topes"+idResp ) )[0].rows[2+j];
								//Fila siempre es la ultima
								var tam = $( "#tabla_topes", $("#div_cont_tabla_topes"+idResp ) )[0].rows.length;
								fila = $( "#tabla_topes", $("#div_cont_tabla_topes"+idResp ) )[0].rows[tam-1];
								setDatos( topes, fila, 'name' ) ;
								agregarFila = true;*/
							}
							else
							{
								//console.log("TOPES TOTAL ON "+idResp);
								/*Para mostrar los datos de total de tope y reconocido*/
								//topes = data.infoing[ data.posAct ]['topes'][i];
								//fila = $( "#_tr_tabla_topes" ,$("#div_cont_tabla_topes"+idResp ));
								//setDatos( topes, fila, 'name' ) ;
								/*Fin Para mostrar los datos de total de tope y reconocido*/
							}
							j++;
						}
						//REVIZAR REVIZAR
						// resetAqua( $("#tabla_topes") );
						$("#div_cont_tabla_topes"+idResp).find("[name='top_rectxtValRec']").on({
							keyup: function(){
								$(this).val( $(this).val().replace(/[^0-9]/, "") );

								if( $(this).val().length > 3 ){
									$(this).val( $(this).val().substring(0, $(this).val().length -1 ) );
								}else if( $(this).val().length == 3 ){
									if( $(this).val()*1 > 100 ){
										$(this).val('100');
									}
								}
							}
						});

						$("#div_cont_tabla_topes"+idResp).find("[name='top_toptxtValTop']").on({
							blur: function(){
								$(this).val( $(this).val().replace(/[^0-9]/, "") );

								if( $(this).val()*1 <= 100 ){
									alert( "El valor ingresado debe ser mayor a 100 puesto que no se refiere a un porcentaje" )
									$(this).val( "" );
								}
							}
						});

					} //for externo

			}


		}
		/**Fin datos topes**/

		$("[name=ing_plaselPlan]").each(function(){
			if( $(this).val() != "" ){
				$(this).removeClass("campoRequerido");
			}
		});

	}
}

//validacion de la cedula del responsable
function validarCamposCedAco(){
	var cedula1 = $.trim($('#pac_doctxtNumDoc').val());
	var cedula2 = $('#pac_ceatxtCelAco').val();

	if (cedula1 == cedula2)
	{
		llenadoAutomatico = true;
		var n1=$('#pac_no1txtPriNom').val();
		var n2=$('#pac_no2txtSegNom').val();
		var a1=$('#pac_ap1txtPriApe').val();
		var a2=$('#pac_ap2txtSegApe').val();

		$('#pac_ceatxtCelAco').val($.trim($('#pac_doctxtNumDoc').val())); //cedula
		$('#pac_noatxtNomAco').val(n1+" "+n2+" "+a1+" "+a2); //nombre
		//$('#pac_prutxtParRes').val('Ninguno'); //parentesco
		$('#pac_diatxtDirAco').val($('#pac_dirtxtDirRes').val()); //direccion

		$('#pac_mretxtMunResp').val($('#pac_muhtxtMunRes').val());//municipio de residencia
		$('#pac_mrehidMunResp').val($('#pac_muhhidMunRes').val());//hidden municipio residencia

		$('#pac_teatxtTelAco').val($('#pac_teltxtTelFij').val()); //telefono


		resetAqua( $( "#div_int_datos_acompañante" ) );
		//Simular onblur para todos los campos con atributo depend en el div de datos del responsable
		$( "[depend]", $( "#div_int_datos_acompañante" ) ).blur();
		llenadoAutomatico = false;
		llenarDatosLog( true );
	}
}

function validarCamposCedRes()
{

		var cedula1 = $.trim($('#pac_doctxtNumDoc').val());
		var cedula2 = $('#pac_crutxtNumDocRes').val();

		if (cedula1 == cedula2)
		{
			llenadoAutomatico = true;
			var n1=$('#pac_no1txtPriNom').val();
			var n2=$('#pac_no2txtSegNom').val();
			var a1=$('#pac_ap1txtPriApe').val();
			var a2=$('#pac_ap2txtSegApe').val();

			$('#pac_tdaselTipoDocRes').val($('#pac_tdoselTipoDoc').val()); //tipo documento
			$('#pac_crutxtNumDocRes').val($.trim($('#pac_doctxtNumDoc').val())); //cedula
			$('#pac_nrutxtNomRes').val(n1+" "+n2+" "+a1+" "+a2); //nombre
			$('#pac_prutxtParRes').val('Ninguno'); //parentesco
			$('#pac_drutxtDirRes').val($('#pac_dirtxtDirRes').val()); //direccion
			$('#pac_ddrtxtDetDirRes').val($('#pac_dedtxtDetDir').val()); //detalle de la direcion
			$('#pac_dretxtDepResp').val($('#pac_dehtxtDepRes').val());//departamento residencia
			$('#pac_drehidDepResp').val($('#pac_dehhidDepRes').val()); //hidden departamento residencia

			$('#pac_mretxtMunResp').val($('#pac_muhtxtMunRes').val());//municipio de residencia
			$('#pac_mrehidMunResp').val($('#pac_muhhidMunRes').val());//hidden municipio residencia

			$('#pac_trutxtTelRes').val($('#pac_teltxtTelFij').val()); //telefono
			$('#pac_mortxtNumResp').val($('#pac_movtxtTelMov').val());//movil
			$('#pac_cretxtCorResp').val($('#pac_cortxtCorEle').val()); //correo


			resetAqua( $( "#div_int_datos_responsable" ) );
			//Simular onblur para todos los campos con atributo depend en el div de datos del responsable
			$( "[depend]", $( "#div_int_datos_responsable" ) ).blur();
			llenadoAutomatico = false;
			llenarDatosLog( true );
		}
		else //se realiza la busqueda de la cedula como responsable del usuario o como paciente
		{
				$.post("admision_erp.php",
				{
					accion      : 'consultarResponsable',
					consultaAjax: '',
					cedula2     : cedula2,
					wbasedato   :$('#wbasedato').val(),
					wemp_pmla   :$('#wemp_pmla').val()

				},
				function(data){
					if(data.error == 1)
					{
						alerta(data.mensaje);
					}
					else
					{
						if( data.doc != '' )
						{

							//$('#pac_tdaselTipoDocRes').val(data.tdoc);
							$('#pac_tdaselTipoDocRes>option[value='+data.tdoc+']').attr("selected", true);

							$('#pac_crutxtNumDocRes').val(data.doc);
							$('#pac_nrutxtNomRes').val(data.nom);
							$('#pac_drutxtDirRes').val(data.dir);
							$('#pac_ddrtxtDetDirRes').val(data.ddir);
							$('#pac_drehidDepResp').val(data.dep);
							$('#pac_mrehidMunResp').val(data.mun);
							$('#pac_trutxtTelRes').val(data.tel);
							$('#pac_mortxtNumResp').val(data.mov);
							if( data.ema != "NULL" ) $('#pac_cretxtCorResp').val(data.ema);
							$('#pac_prutxtParRes').val(data.pare);

							$('#pac_dretxtDepResp').val(data.ndep);
							$('#pac_mretxtMunResp').val(data.nmun);

							resetAqua( $( "#div_int_datos_responsable" ) );
							//Simular onblur para todos los campos con atributo depend en el div de datos del responsable
							$( "[depend]", $( "#div_int_datos_responsable" ) ).blur();
						}

					}



				},
				"json"
			);
		}
}

function validacionServicioIngreso( origen="" )
{
	//parte de configuracion
	var servicio             = $('#ing_seisel_serv_ing').val();
	var mostrarTipoServicio  = $('#mostrarTipoServicio').val();
	var tipoAtencionSelected = $('#pac_tamselClausu > option:selected').val();
	//|| ( mostrarTipoServicio == "on" && origen != "" && (tipoAtencionSelected != "" && tipoAtencionSelected != undefined ) )
	if ( (servicio == '5' || ( mostrarTipoServicio == "on" && origen == "" ) || ( mostrarTipoServicio == "on" && origen != "" && (tipoAtencionSelected != "" && tipoAtencionSelected != undefined )  ) ) )
	{
		if( mostrarTipoServicio =="on" && origen == "cargaDatosAlmacenados" && (tipoAtencionSelected != "" && tipoAtencionSelected != undefined ) ){
			entro = true;
		}
		if( mostrarTipoServicio == "on" && origen == ""  ){
			entro = false;
			$.ajax({
            url: 'admision_erp.php',
            type: "POST",
            data: {
		               consultaAjax: "on",
		                  wemp_pmla: $("#wemp_pmla").val(),
		                     accion: "consultarTipoServicio",
		                  wbasedato: $('#wbasedato').val(),
		                codServicio: servicio,
		       tipoAtencionSelected: $("#pac_tamselClausu > option:selected").val()
                  },
            success: function(data) {
            	if( data.error*1 == 3 ){
            		entro = false;
            		$("#td_tipoServicioTitulo").css("display", "none");
					$("#td_tipoServicioSelect").css("display", "none");
					$("#pac_tamselClausu").css("display", "none");
	            }else{
	            	entro = true;
	            	$("#pac_tamselClausu>td").remove();
	            }
	            $("#pac_tamselClausu").html(data.html);
            },
            async:false,
            dataType: "json"
          });
		}
		if( ( mostrarTipoServicio == "on" && entro ) ||  servicio == '5'  ){
			$("#td_tipoServicioTitulo").css("display", "");
			$("#td_tipoServicioSelect").css("display", "");
			$("#pac_tamselClausu").css("display", "");
		}else{
			$("#td_tipoServicioTitulo").css("display", "none");
			$("#td_tipoServicioSelect").css("display", "none");
			$("#pac_tamselClausu").css("display", "none");
		}

	}
	else
	{
		$("#td_tipoServicioTitulo").css("display", "none");
		$("#td_tipoServicioSelect").css("display", "none");
		$("#pac_tamselClausu").css("display", "none");
	}

}

function realizarIngreso()
{
	var llenarIng = $("input[name=realizarIng]:checked").val();
	if (llenarIng == 'S')
	{
		$('#div_datos_responsable,#div_datos_Pag_Aut,#div_otros_datos_ingreso').slideDown('fast');
	}
	else
	{
		$("#div_datos_responsable,#div_datos_Pag_Aut,#div_otros_datos_ingreso").slideUp('fast');
	}
}


objsCadenas = '';

//llenar datos constantemente por se se presenta un fallo de energia
function llenarDatosLog( obligar )
{

	if( obligar == undefined ){
		if( llenarTablaLog == false )
			return;

		//para que no llene el log si se cargan datos del formulario automaticamente
		if( llenadoAutomatico == true ){
			return;
		}
		//Solo llena el log cada que llene 5 campos
		if( conteoInputs < 4 ){
			conteoInputs++;
			return;
		}
	}
	conteoInputs=0;

	if( modoConsulta == false )
	{
		var objJson = cearUrlPorCamposJson( $( "#div_admisiones" ),'id' );
		objJson = cearUrlPorCamposJson( $( "#accidentesTransito" ), 'name', objJson );
		objJson = cearUrlPorCamposJson( $( "#eventosCatastroficos" ), 'name', objJson );
		// var contenedor = $( "#div_admisiones" )[0];

		// contenedor.infoDigitada = objJson;

		objJson.ing_plaselPlan = {};
		//para el campo de planes creo todos los options
		$( "option", $( "#ing_plaselPlan" ) ).each(function(){

			if( !objJson.ing_plaselPlan.options ){
				objJson.ing_plaselPlan.options = new Array();
			}

			var index = objJson.ing_plaselPlan.options.length;
			objJson.ing_plaselPlan.options[index] = {};

			objJson.ing_plaselPlan.options[index].des = $( this ).html();

			if( $( this ).attr( "value" ) != 'Seleccione...' ){
				objJson.ing_plaselPlan.options[index].val = $( this ).attr( "value" );
			}
			else{
				objJson.ing_plaselPlan.options[index].val = "";
			}

		});

		objJson.ing_plaselPlan.value = $( "#ing_plaselPlan" ).val();

		// var cadena =JSON.stringify(objJson); //para pasar json a string NO SIRVIO EN IE
		var cadena =$.toJSON(objJson); //para pasar json a string
			// JSON.parse(string); //para pasar de string a json
		// alert(cadena);

		if( objsCadenas != cadena ){

			$.post("admision_erp.php",
				{
					accion      : 'llenarTablaDatosLog',
					consultaAjax: '',
					cadena     : cadena,
					wbasedato   :$('#wbasedato').val(),
					wemp_pmla   :$('#wemp_pmla').val()

				},
				function(data){
					if(data.error == 1)
					{
						alerta(data.mensaje);
					}
					else
					{
						// alert(data.mensaje);
						objsCadenas = cadena;
					}

				},
				"json"
			);
		}
	}
}

function verificarLogAdmision()
{
	//para traer la informacion si tiene guardado un log

	$.post("admision_erp.php",
		{
			accion      : 'traerTablaDatosLog',
			consultaAjax: '',
			key         : $('#key').val(),
			wbasedato   :$('#wbasedato').val(),
			wemp_pmla   :$('#wemp_pmla').val()

		},
		function(data){
			if(data.error == 1)
			{
				alerta(data.mensaje);
			}
			else
			{
				if (data.html != '')
				{
					confirmacion = 'Tiene una admision guardada parcialmente, desea recuperarla?';
					if(confirm(confirmacion))
					{
						verificandoLog = true;
						// $.blockUI({message: "Cargando datos..." });
						// var objSon=JSON.parse(data.html); //para pasar de string a json
						eval( "var objSon="+data.html );
						setDatos( objSon, $( "#div_admisiones" ), 'id' );
						setDatos( objSon, $( "#accidentesTransito" ), 'name' );
						setDatos( objSon, $( "#eventosCatastroficos" ), 'name' );

						if( $( "#ing_caiselOriAte" ).val() == '02' ){
							$( "#div_accidente_evento").css( {display: ""} );
							$( "td", $( "#div_accidente_evento") ).eq(1).css( {display: "none"} );	//oculto el boton de eventos catastróficos
							$( "td", $( "#div_accidente_evento") ).eq(0).css( {display: ""} );	//oculto el boton de eventos catastróficos

							var objJson = cearUrlPorCamposJson( $( "#infDatosAcc" ) );
							var contenedor = $( "#accidentesTransito" )[0];

							contenedor.lastInfo = objJson;
						}
						else if( $( "#ing_caiselOriAte" ).val() == '06' ){
							$( "#div_accidente_evento").css( {display: ""} );
							$( "td", $( "#div_accidente_evento") ).eq(0).css( {display: "none"} );	//oculto el boton de eventos catastróficos
							$( "td", $( "#div_accidente_evento") ).eq(1).css( {display: ""} );	//oculto el boton de eventos catastróficos

							var objJson = cearUrlPorCamposJson( $( "#eventosCatastroficos" ) );
							var contenedor = $( "#eventosCatastroficos" )[0];

							contenedor.lastInfo = objJson;
						}
						else{
							$( "#div_accidente_evento").css( {display: "none"} );
						}
						//para que se ejecuten los blur de los elementos del formulario porque cuando se trae el log los vacios y no son requeridos los deja amarillos
						$( "textarea,select,input" ).each(function(){
							if( true || $( this ).attr( "depend" ) == '' ){
								$( this ).blur();
							}
						});

						//para que cuando sea empresa muestre habilitados los datos de autorizacion
						//2014-05-06 validarTipoResp('');
						var objetoRes = $("[name=ing_tpaselTipRes]").eq(0);
						//console.log("es aqui "+objetoRes[0].value);
						validarTipoResp(objetoRes[0]);

						// $("#ing_poltxtNumPol").removeClass("campoRequerido"); //si se descomentan agregarlos donde se adiciona la clase
						// $("#ing_ncotxtNumCon").removeClass("campoRequerido"); //si se descomentan agregarlos donde se adiciona la clase

						//validar si el paciente es remitido
						validarPacienteRem();



						borrarLog(key);
						resetAqua( $( "#div_admisiones,#accidentesTransito,#eventosCatastroficos" ) );

						// mostrarAdmision();
						// $( "#radAdmision" ).attr( "checked", true );
						// opcionAdmisionPreadmision();

						if( $( "[name=radPreAdmi]:checked" ).val().toUpperCase() == "ADMISION" )
						{
							// resetear();
							$( "#radAdmision" ).attr( "checked", true );
							opcionAdmisionPreadmision();

							if( $( "input[name='btRegistrarActualizar']" ).attr( "graba" ) == "on" ){
								$( "input[name='btRegistrarActualizar']" ).attr( "disabled", false );
							}else{
								$( "input[name='btRegistrarActualizar']" ).attr( "disabled", true );
							}

							$( "input[name='btRegistrarActualizar']" ).val( "Admitir" );
						}
						else
						{
							$( "#radPreadmision" ).attr( "checked", true );
							mostarOcultarDatosPreadmisiones( false );
							$( "#txtAdmisionPreadmision" ).html( "PREADMISION" );

							if( $( "input[name='btRegistrarActualizar']" ).attr( "graba" ) == "on" ){
								$( "input[name='btRegistrarActualizar']" ).removeAttr( "disabled" );
								console.log("removiendo")
							}else{
								$( "input[name='btRegistrarActualizar']" ).attr( "disabled", true );
							}

							$( "input[name='btRegistrarActualizar']" ).val( "Preadmitir" );
						}

						// $.unblockUI();
					}
					else
					{
						borrarLog(key);
					}
				}
			}

			verificandoLog = false;
		},
		"json"
	);
}

function borrarLog(key)
{
	$.ajax(
	{
		url: "admision_erp.php",
		context: document.body,
		type: "POST",
		data:
		{
			accion      : 'borrarTablaDatosLog',
			consultaAjax: '',
			key         : $('#key').val(),
			wbasedato   :$('#wbasedato').val(),
			wemp_pmla   :$('#wemp_pmla').val()
		},
		async: false,
		dataType: "json",
		success:function(data) {
			if(data.error == 1)
			{
				alerta(data.mensaje);
			}
			else
			{
				// $("#"+selectHijo).html(data.html); // update Ok.
				//$("#div_lista_re")[0].innerHTML = data.html; // update Ok.
			}
		}
	});
}

function buscarIpsRemite()
{
	var wbasedato = $("#wbasedato").val();
	//Asigno autocompletar para la busqueda de ips que remite
	$( "#pac_iretxtIpsRem" ).autocomplete("admision_erp.php?consultaAjax=&accion=consultarIpsRemite&wbasedato="+wbasedato,
	{
		cacheLength:1,
		delay:300,
		max: 100,
		scroll: false,
		scrollHeight: 500,
		matchSubset: false,
		matchContains: true,
		width:250,
		autoFill:false,
		minChars: 3,
		json:"json",
		formatItem: function(data, i, n, value) {

			//convierto el string en json
			eval( "var datos = "+data );

			return datos[0].usu;	//Eso es lo que se muestra al usuario
		},
		formatResult: function(data, value){
			//convierto el string en json
			eval( "var datos = "+data );

			return datos[0].valor.des;
		}
	}).result(
		function(event, item ){

			// //La respuesta es un json
			// //convierto el string en formato json
			eval( "var datos = "+item );

			//Guardo el ultimo valor que selecciona el usuario
			//Esto en una propiedad inventada
			this.value = datos[0].valor.des;
			this._lastValue = this.value;
			$( this ).removeClass( "campoRequerido" );

			$( "input[type=hidden]", this.parentNode ).val( datos[0].valor.cod );

		}
	).on({
		change: function(){


			var cmp = this;

			setTimeout( function(){

				//Pregunto si la pareja es diferente
				if( ( ( cmp._lastValue && cmp._lastValue != cmp.value ) || ( !cmp._lastValue && cmp.value != $( cmp ).attr( cmp.aqAttr ) ) )
					|| ( cmp._lastCodigo && cmp._lastCodigo != $( "input[type=hidden]", cmp.parentNode ).val() )
				)
				{
					alerta( " Digite una IPS v\u00E1lida" )
					$( "input[type=hidden]", cmp.parentNode ).val( '' );
					cmp.value = '';
					cmp.focus();
					// cmp.blur();
				}
			}, 200 );

			//Pregunto si la pareja es diferente
			// if( ( this._lastValue && this._lastValue != this.value && this._lastCodigo ) || ( this._lastCodigo != $( "input[type=hidden]", this.parentNode ).val() ) ){
				// alert( "Digite una ips que remite valida" )
				// $( "input[type=hidden]", this.parentNode ).val( '' );
				// this.value = '';
			// }


			// if( this._lastValue ){
				// this.value = this._lastValue;
			// }
			// else{
				// this.value = "";
			// }
		}
	});
}

function opcionAdmisionPreadmision()
{
	$("[name=radPreAdmi]:checked").each(function(x)
	{
		if( this.value == 'admision' )
		{
			$( "#pac_fectxtFechaPosibleIngreso" ).attr( "disabled", true );
			$( "#pac_fectxtFechaPosibleIngreso" ).removeClass( "campoRequerido" );
			$( "#txtAdmisionPreadmision" ).html( "ADMISION" );

			mostarOcultarDatosPreadmisiones( false );

			// $( "option[value=02]", $( "#ing_caiselOriAte" ) ).css( { display: "" } );
			// $( "option[value=06]", $( "#ing_caiselOriAte" ) ).css( { display: "" } );
			// $( "option[value=4]", $( "#ing_seisel_serv_ing" ) ).css( { display: "" } );

			//Agrego los elementos al select ORIGEN DE LA ATENCION
			if( optOrigenAtencion && optOrigenAtencion != '' )
			{
				var auxClone = optOrigenAtencion.clone();

				// $( "#ing_caiselOriAte" )[0].options.add( aux[0], 2 );
				// $( "#ing_caiselOriAte" )[0].options.add( aux[1], 6 );

				if( $( "option[value=02]", $( "#ing_caiselOriAte" ) ).length == 0  )
				{
					var aux = document.createElement( "option" );

					var a = document.getElementById( "ing_caiselOriAte" );
					a.options.add( aux, 2 );

					aux.innerHTML = auxClone[0].innerHTML;
					aux.value = auxClone[0].value;
				}

				if( $( "option[value=06]", $( "#ing_caiselOriAte" ) ).length == 0 )
				{
					var aux = document.createElement( "option" );

					var a = document.getElementById( "ing_caiselOriAte" );
					a.options.add( aux, 6 );

					aux.innerHTML = auxClone[1].innerHTML;
					aux.value = auxClone[1].value;
				}
			}

			if( optServicioIngreso && optServicioIngreso != '' )
			{
				var auxClone = optServicioIngreso.clone();

				if( $( "option[value^=4]", $( "#ing_seisel_serv_ing" ) ).length == 0 )
				{
					for( var iii = 0; iii < auxClone.length; iii++ ){
						var aux = document.createElement( "option" );
						var a = document.getElementById( "ing_seisel_serv_ing" );
						a.options.add( aux, 3 );
						aux.innerHTML = auxClone[iii].innerHTML;
						aux.value = auxClone[iii].value;
					}
				}
			}
			$( "[padm]" ).css({ display: "none" });
		}
		else
		{
			$( "#pac_fectxtFechaPosibleIngreso" ).attr( "disabled", false );
			$( "#txtAdmisionPreadmision" ).html( "PREADMISION" );

			if( $( "#pac_fectxtFechaPosibleIngreso" ).val() == '' || $( "#pac_fectxtFechaPosibleIngreso" ).val() == $( "#pac_fectxtFechaPosibleIngreso" ).attr( "msgerror" ) )
			{
				$( "#pac_fectxtFechaPosibleIngreso" ).addClass( "campoRequerido" );
			}

			mostarOcultarDatosPreadmisiones( true );

			// $( "option[value=02]", $( "#ing_caiselOriAte" ) ).css( { display: "none" } );
			// $( "option[value=06]", $( "#ing_caiselOriAte" ) ).css( { display: "none" } );
			// $( "option[value=4]", $( "#ing_seisel_serv_ing" ) ).css( { display: "none" } );

			//$( "option[value=02],option[value=06]", $( "#ing_caiselOriAte" ) ).remove();

			$( "option[value^=4]", $( "#ing_seisel_serv_ing" ) ).remove();

			//Si no hay nada en agenda de preadmision traigo los datos del día en la agenda de preadmisión
			if( $( "#dvAgendaPreadmision" ).html() == '' )
			{
				consultarAgendaPreadmision( $( "#fechaAct" ).val(), 0 );
			}

			$( "[padm]" ).css({ display: "" });
		}
	});
}

/****************************************************************************************************
 * Agosto 20 de 2013
 *
 * Oculta o muestra los datos necesarios al dar click sobre admision o preadmisión
 * También se usa al dar click sobre el botón ingresar en la agenda
 ****************************************************************************************************/
function mostarOcultarDatosPreadmisiones( ocultar )
{
	if( ocultar )
	{

		/************************************************************************************************
		 * Agosto 20 de 2013
		 ************************************************************************************************/

		//Muestro la agenda de preadmisiones
		// $( "#dvAgendaPreadmision" ).css( { display: "" } );
		$( "#div_datosAdmiPreadmi" ).css( {display:"none"} );

		//oculto todo los divs y solo dejo el div de agenda de preadmisiones
		$( "#div_int_datos_personales,[id!=div_datosIng_Per_Aco][acordeon]", $( "#div_admisiones" ) ).css( { display: "none" } );
		$( "#div_ext_agendaPreadmision" ).css( { display: "" } );
		// $( "center",$( "#datos_ingreso") ).eq(2).css( { display: "none" } );
		$( "#datos_ingreso center" ).last().css( { display: "none" } );
		$( "div[name='div_botones']" ).css( { display: "none" } );
		/************************************************************************************************/
	}
	else
	{
		/********************************************************************************
		 * Agsoto 20 de 2013
		 ********************************************************************************/

		//Oculto la agenda de preadmisiones
		// $( "#dvAgendaPreadmision" ).css( {display:"none"} );
		$( "#div_datosAdmiPreadmi" ).css( {display:""} );

		//oculto todo los divs y solo dejo el div de agenda de preadmisiones
		$( "#div_int_datos_personales,[id!=div_datosIng_Per_Aco][acordeon]", $( "#div_admisiones" ) ).css( { display: "" } );
		$( "#div_ext_agendaPreadmision" ).css( {display:"none"} );
		// $( "center",$( "#datos_ingreso") ).eq(2).css( { display: "" } );
		$( "#datos_ingreso center" ).last().css( { display: "" } );
		$( "div[name='div_botones']" ).css( { display: "" } );
		/********************************************************************************/

		/************************************************************************************************
		 * Agosto 21 de 2013
		 ************************************************************************************************/
		if( $( "#ing_caiselOriAte" ).val() == '02' || $( "#ing_caiselOriAte" ).val() == '06' )
		{
			$( "#div_accidente_evento" ).css( {display: "" } );
		}
		else{
			$( "#div_accidente_evento" ).css( {display: "none" } );
		}
		/************************************************************************************************/
	}

	hayCambios = false;
}

/************************************************************************************************
 * Agosto 20 de 2013
 ************************************************************************************************/
//Indica cual fue la último paciente que fue cargado con preadmisión
//Esto para validar que no se vuelva a preguntar si desea cargar los datos
var ultimaPreadmisionCargada = '';
/************************************************************************************************/

/************************************************************************************************
 * Agosto 15 de 2013
 ************************************************************************************************/
function mostrarDatosPreadmision( documento )
{
	if( documento ){
		$( "#pac_doctxtNumDoc" ).val( documento );
		// $( "input[name='btRegistrarActualizar']" ).val( "Actualizar" );
	}

	//No haga el llamado si el documento está vacio
	if( $.trim($( "#pac_doctxtNumDoc" ).val()) == '' )
		return;


	$.blockUI({message: "Por favor espere..."});

	//Variable para saber si esta en modo consulta o no
	modoConsulta = true;


		var objJson = cearUrlPorCamposJson( $( "#div_admisiones" ),'id' );

		objJson.accion = "mostrarDatosAlmacenadosPreadmision";	//agrego un parametro más
		objJson.wbasedato = $( "#wbasedato" ).val();
		objJson.consultaAjax = "";
		objJson.historia = $( "#ing_histxtNumHis" ).val();
		objJson.ingreso = $( "#ing_nintxtNumIng" ).val();
		objJson.documento = $.trim($( "#pac_doctxtNumDoc" ).val());
		objJson.wemp_pmla = $( "#wemp_pmla" ).val();

		/*validacion de todos los input para saber si tienen el mesaje de error
		 y si lo tiene se envia vacio*/
		$('input').each(function(n)
		{
			var id =this.id;
			var valor = $("#"+id).val();
			var valormsgerror = $("#"+id).attr( "msgerror" );

			if(valor == valormsgerror)
			{
				objJson[id] = '';
			}
		});

		$.post("admision_erp.php",
			objJson,
			function(data){

				$.unblockUI();

				if( data.error == 1 )
				{
					alerta( data.mensaje );
				}
				else
				{
					if( data.mensaje != '' )
						alerta( data.mensaje );


					if( data.infoing )
					{
						if( $( "#radPreadmision" ).is(":checked") ){
							if( ultimaPreadmisionCargada != data.infoing[0].pac_doc && ( documento || confirm( "El paciente tiene preadmisión. Desea traer los datos?" ) ) )
							{
								informacionIngresos = data;
								informacionIngresos.regTotal = data.infoing.length;
								informacionIngresos.posAct = data.infoing.length-1;

								navegacionIngresosPreadmision( 0 );

								$( "#radPreadmision" ).attr( "checked", true );
								$( "#radPreadmision" ).click();

								mostarOcultarDatosPreadmisiones( false );

								//Despues de que consulte se borra el log
								borrarLog( $( "#key" ) );
								//se colocan los campos cedula,historia,ingreso read only para que no se puedan modificar
								$("#pac_doctxtNumDoc").attr("readonly", true);
								$("#ing_histxtNumHis").attr("readonly", true);
								$("#ing_nintxtNumIng").attr("readonly", true);

								ultimaPreadmisionCargada = $.trim($("#pac_doctxtNumDoc").val());
								if( $( "input[name='btRegistrarActualizar']" ).attr( "actualiza" ) == "on" ){
									$( "input[name='btRegistrarActualizar']" ).attr( "disabled", false );
								}else{
									$( "input[name='btRegistrarActualizar']" ).attr( "disabled", true );
								}
								$( "input[name='btRegistrarActualizar']" ).val( "Actualizar" );
							}
						}else{
							if( confirm( "El paciente tiene preadmisión. Desea traer los datos para admitir?" )  )
							{
								informacionIngresos = data;
								informacionIngresos.regTotal = data.infoing.length;
								informacionIngresos.posAct = data.infoing.length-1;

								delete informacionIngresos.infoing[ 0 ].pac_fec; //Para que no cambie la fecha de ingreso
								delete informacionIngresos.infoing[ 0 ].ing_fei; //Para que no cambie la fecha de ingreso
								delete informacionIngresos.infoing[ 0 ].ing_sei; //Para que no cambie el servicio de ingreso
								delete informacionIngresos.infoing[ 0 ].ing_des; //Para que no cambie el destino
								delete informacionIngresos.infoing[ 0 ].ing_hin; //Para que no cambie la hora de ingreso

								navegacionIngresosPreadmision( 0 );
							}else
							{
								mostrarDatosDemograficos();
							}
						}
					}
					else
					{
						mostrarDatosDemograficos();
					}
					/*if( $("#pac_trhselTipRes > option:selected").val() == "E" ){
						$(".tr_pacienteExtranjero").show();
					}*/
					modoConsulta = false;
				}
			},
			"json"
		);
}

function navegacionIngresosPreadmision( incremento )
{
	var data = informacionIngresos;

	if( data.posAct + incremento < informacionIngresos.regTotal && data.posAct + incremento >= 0 )
	{
		data.posAct = data.posAct + incremento;
		setDatos( data.infoing[ data.posAct ], $( "#div_admisiones" ), 'id' )  ;
		calcular_edad(data.infoing[ data.posAct ].pac_fna);
		validarPacienteRem();
		var prefijo_trEps = $("tr[id$=tr_tabla_eps]").eq(0).attr("id");
		llenarPlan($( "#ing_cemhidCodAse"+prefijo_trEps ).val(), 'ing_plaselPlan'+prefijo_trEps);
		$( "#ing_plaselPlan"+prefijo_trEps ).val( data.infoing[ data.posAct ].ing_pla );
		$("#spEstPac").removeClass("estadoInactivo estadoActivo");//se le quita antes la clase que tiene para colocarle la nueva

		var estPac = data.infoing[ data.posAct ].pac_act;//se trae el estado del paciente
		$("#spEstPac").html('');
		//var objetoRes = $("[name=ing_tpaselTipRes]").eq(0);
		//validarTipoResp(objetoRes[0]);

		setDatos( data.infoing[ data.posAct ], $( "#accidentesTransito" ), 'name' );
		if( $("#ing_caiselOriAte").val() == '02' ){
			$("#div_accidente_evento").css( {display: ""} );
			$("td", $( "#div_accidente_evento") ).eq(0).css( {display: ""} );	//Mostrar accidentes de transito
			$("td", $( "#div_accidente_evento") ).eq(1).css( {display: "none"} );	//oculto el boton de eventos catastróficos
			$("td", $( "#div_accidente_evento") ).eq(2).css( {display: "none"} ); //oculto el boton de listar eventos catastróficos

			if( data.infoing[ data.posAct ].dat_Accrei ){
				$("#accidente_previo").val( data.infoing[ data.posAct ].dat_Accrei );
			}

			var objJson = cearUrlPorCamposJson( $( "#infDatosAcc" ) );
			var contenedor = $( "#accidentesTransito" )[0];

			contenedor.lastInfo = objJson;

			/*if( $("#ing_vretxtValRem").val() == "" )
				$("#ing_vretxtValRem").val(0);//campo obligatorio, para que deje pasar*/
		}

		 /** para mostrar responsables**/
		if (data.infoing[ data.posAct ]['responsables'] === undefined)
		{

		}
		else
		{  //trae datos de los responsables
			for ( var i=0; i<data.infoing[ data.posAct ]['responsables'].length-1;i++)
			{
				addFila('tabla_eps',$("#wbasedato").val(),$("#wemp_pmla").val());
			}

			for ( var i=0; i<data.infoing[ data.posAct ]['responsables'].length;i++)
			{
				var responsables = data.infoing[ data.posAct ]['responsables'][i];
				var fila = $( "#tabla_eps" )[0].rows[1+i];
				setDatos( responsables, fila, 'name' );
				llenarPlan($( "#ing_cemhidCodAse"+(i*1+1)+"_tr_tabla_eps").val(), 'ing_plaselPlan'+(i*1+1)+"_tr_tabla_eps");
				$( "#ing_plaselPlan"+(i*1+1)+"_tr_tabla_eps" ).val( responsables.ing_plaselPlan );

				//Construir la cantidad de cups necesarios y llevarles el valor
				if( responsables.cups != undefined ){
					var idUltTr = (i+1)+"_tr_tabla_eps";
					for( var ii=0; ii<responsables.cups.length; ii++){
						//Se crea el input text (nombre del cup) y el input hide (codigo del cup) dentro del tr
						if( ii != 0 )
							agregarCUPS( idUltTr );
						var lastTr = $("#"+idUltTr);
						lastTr.find("input[name=ing_cactxtcups]").eq(ii).val( responsables.cups[ii].codigo+"-"+responsables.cups[ii].nombre ); //nombre del cup
						lastTr.find("input[name=ing_cachidcups]").eq(ii).val( responsables.cups[ii].codigo ); //codigo del cup
					}
				}else{
					console.log( "sin cups "+ responsables.ing_cemtxtCodAse  );
				}
				resetAqua( $("#tabla_eps") );
				var objetoRes = $("[name=ing_tpaselTipRes]").eq(i);
				if( objetoRes != undefined ) validarTipoResp(objetoRes[0]);
			}
			reOrdenarResponsables();
		}
		/**Fin datos responsables**/


		/*var objetoRes = $("[name=ing_tpaselTipRes]").eq(0);
		validarTipoResp(objetoRes[0]);*/

		/** para mostrar topes**/
		if (data.infoing[ data.posAct ]['topes'] === undefined)
		{

		}
		else
		{  //trae datos de los topes
			var topes="";
			var fila="";


			for ( var k=0; k<data.infoing[ data.posAct ]['responsables'].length;k++)
			{
				var agregarFila = false;
				for ( var i=0,j=0; i<data.infoing[ data.posAct ]['topes'].length;i++)
				{
					//codigo responsable
					var resp = data.infoing[ data.posAct ]['topes'][i]['top_reshidTopRes'];
					//para traer todo el objeto que contiene ese codigo de responsable
					var objResp= $( "#tabla_eps input[value='"+resp+"']" );
					//extraer el id
					var idResp =objResp[0].id;

					if( resp == data.infoing[ data.posAct ]['responsables'][k]['ing_cemhidCodAse'] )
					{
						if(j == 0){
							//mostrarDivTopes1(idResp);
						}

						if( j > 0 ){
							if (data.infoing[ data.posAct ]['topes'][i]['total'] == 'off' && agregarFila == true)
							{
								idCodResp = idResp;
								addFila('tabla_topes',wbasedato.value,wemp_pmla.value);
							}
						}

						if (data.infoing[ data.posAct ]['topes'][i]['total'] == 'off')
						{
							/*console.log("TOPES TOTAL OFF "+idResp);
							topes = data.infoing[ data.posAct ]['topes'][i];
							//Fila siempre es la ultima
							var tam = $( "#tabla_topes", $("#div_cont_tabla_topes"+idResp ) )[0].rows.length;
							fila = $( "#tabla_topes", $("#div_cont_tabla_topes"+idResp ) )[0].rows[tam-1];
							setDatos( topes, fila, 'name' ) ;
							agregarFila = true;*/
						}
						else
						{
							//console.log("TOPES TOTAL ON "+idResp);
							/*Para mostrar los datos de total de tope y reconocido*/
							/*topes = data.infoing[ data.posAct ]['topes'][i];
							fila = $( "#_tr_tabla_topes" ,$("#div_cont_tabla_topes"+idResp ));
							setDatos( topes, fila, 'name' ) ;*/
							/*Fin Para mostrar los datos de total de tope y reconocido*/
						}
						j++;
					}
				} //for externo
			}
		}
		/**Fin datos topes**/


		resetAqua( );
		//se simula el onblur para quitar el requerido cuando el que depende esta lleno
		 $( "[depend]" ).blur();


	}
}

/**************************************************************************************************
 * Agosto 16 de 2013
 *
 * Realiza el ajax para consultar los datos de preadmisión
 **************************************************************************************************/
function consultarAgendaPreadmision( fecha, incremento )
{
	var objJson = {};	//Creo el objeto
	objJson.accion = "consultarPreadmision";	//agrego un parametro más
	objJson.wbasedato = $( "#wbasedato" ).val();
	objJson.consultaAjax = "";
	objJson.wemp_pmla = $( "#wemp_pmla" ).val();
	objJson.fecha = fecha;
	objJson.incremento = incremento;
	objJson.consulta = $("#perfil_consulta").val();

	$.blockUI({message: "Espere un momento por favor..."});

	$.post("admision_erp.php",
		objJson,
		function(data){
			$.unblockUI();
			if( data.error == 1 )
			{
				alerta( data.mensaje );
			}
			else
			{
				if( data.mensaje != '' )
					alerta( data.mensaje );

				if( data.html )
				{
					$( "#dvAgendaPreamdisionDatos" ).html( data.html );

					$( "#dvAgendaPreamdisionDatos [fecha]" ).datepicker({
						dateFormat:"yy-mm-dd",
						fontFamily: "verdana",
						dayNames: [ "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo" ],
						monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ],
						dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
						dayNamesShort: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
						monthNamesShort: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic" ],
						changeMonth: true,
						changeYear: true,
						yearRange: "c-100:c+100"
					});
					if( $( "input[name='btRegistrarActualizar']" ).attr( "graba" ) == "on" ){
						$( "input[name='btRegistrarActualizar']" ).removeAttr( "disabled" );

					}else{
						$( "input[name='btRegistrarActualizar']" ).attr( "disabled", true );
					}
				}
			}
		},
		"json"
	);
}

/**************************************************************************************************
 * Agosto 16 de 2013
 *
 * Cancela un registro de preadmision
 **************************************************************************************************/
function cancelarPreadmision( campo, fecha, incremento, tdo, doc )
{
	if( confirm( "Desea cancelar la preadmisión?" ) )
	{
		var objJson = {};	//Creo el objeto
		objJson.accion = "cancelarPreadmision";	//agrego un parametro más
		objJson.wbasedato = $( "#wbasedato" ).val();
		objJson.consultaAjax = "";
		objJson.wemp_pmla = $( "#wemp_pmla" ).val();
		objJson.fecha = fecha;
		objJson.incremento = incremento;
		objJson.tdo = tdo;
		objJson.doc = doc;

		$.post("admision_erp.php",
			objJson,
			function(data){

				if( data.error == 1 )
				{
					alerta( data.mensaje );
				}
				else
				{
					if( data.mensaje != '' )
						alerta( data.mensaje );

					consultarAgendaPreadmision( fecha, incremento );
				}
			},
			"json"
		);
	}
	else{
		campo.checked = false;
	}
}


/************************************************************************
 * Agosto 16 de 2013
 ************************************************************************/
function ingresarPreadmision( campo, tdoc, doc )
{



	$.blockUI({message: "Por favor espere" });

	$( "#pac_doctxtNumDoc" ).val( doc );

	//Variable para saber si esta en modo consulta o no
	modoConsulta = true;

	var objJson = cearUrlPorCamposJson( $( "#div_admisiones" ),'id' );

	objJson.accion = "mostrarDatosAlmacenadosPreadmision";	//agrego un parametro más
	objJson.wbasedato = $( "#wbasedato" ).val();
	objJson.consultaAjax = "";
	objJson.historia = $( "#ing_histxtNumHis" ).val();
	objJson.ingreso = $( "#ing_nintxtNumIng" ).val();
	objJson.documento = $.trim($( "#pac_doctxtNumDoc" ).val());
	objJson.wemp_pmla = $( "#wemp_pmla" ).val();

	$.post("admision_erp.php",
		objJson,
		function(data){
			if( isJSON(data) == false ){
				alerta("RESPUESTA NO ESPERADA\n"+data);
				return;
			}
			data = $.parseJSON(data);

			if( data.error == 1 )
			{
				alerta( data.mensaje );
			}
			else
			{
				if( data.mensaje != '' )
					alerta( data.mensaje );

				if( data.infoing )
				{

					informacionIngresos = data;
					informacionIngresos.regTotal = data.infoing.length;
					informacionIngresos.posAct = data.infoing.length-1;

					navegacionIngresosPreadmision( 0 );

					//Despues de que consulte se borra el log
					borrarLog( $( "#key" ) );
					//se colocan los campos cedula,historia,ingreso read only para que no se puedan modificar
					$("#pac_doctxtNumDoc").attr("readonly", true);
					$("#ing_histxtNumHis").attr("readonly", true);
					$("#ing_nintxtNumIng").attr("readonly", true);

					modoConsulta = false;

					$( "#radAdmision" ).attr( "checked", true );
					$( "#pac_fectxtFechaPosibleIngreso" ).attr( "disabled", true );
					// $( "#radAdmision" ).click();


					/*informacionIngresos == data, numRegistrosIng trae = historia(posicion) e ingreso(valor) traidos de 	)
					  informacionIngresos en la posicion de la historia que se esta mostrando con su respectivo valor y cuyo valor sea igual ==
					  al ingreso que se esta mostrando y && informacionIngresos en la posicion infoIng e infoIng en la posicion actual(posAct) que
					  que esta dentro de informacionIngresos todo lo anterior es:(el ingreso que se esta mostrando en pantalla )	y que este on
					  */
					/* 1.Si NO está en modo consulta se guarda la admisón
					   2.Si esta en modoConsulta se pregunta si es el último ingreso, de ser así entonces se actualiza la admisión
					*/
					//      1.                   2.
					//Procedo a guardar los datos
					if( true )
					{

						$( "#ing_seisel_serv_ing" ).change();
						console.log(" fecha preAdmision "+$( "#ing_feitxtFecIng" ).val()+" fecha admision: "+$("#fechaAct").val());
						//--> se cambia la fecha y hora de la preadmision por la fecha de hoy
						$( "#ing_feitxtFecIng" ).val($("#fechaAct").val());
						$( "#ing_hintxtHorIng" ).val($("#horaAct").val());

						var datosLlenos = $( '#forAdmisiones' ).valid();
						iniciarMarcaAqua( $( '#forAdmisiones' ) );
						//if( datosLlenos ) //2014-08-12, esta validacion se hizo al guardar la preadmision, en este punto sobra
						if( true )
						{
							var validacion = validarCampos( $( "#div_admisiones" ) );

							//if( validacion )
							if( true )
							{
								//A todos los campos que tengan marca de agua y esten deshabilitado, les borro la marca de agua(msgerror)
								$( "[aqua]:disabled" ).each(function(){
									if( $( this ).val() == $( this ).attr( this.aqAttr ) ){
										$( this ).val( '' );
									}
								});

								//A todos los campos que tengan marca de agua no obligatorios
								$( "[aqua]" ).each(function(){
									if( $( this ).val() == $( this ).attr( "msgaqua" ) ){
										$( this ).val( '' );
									}
								});
								var objJson = cearUrlPorCamposJson( $( "#div_admisiones" ),'id' );
								objJson = cearUrlPorCamposJson( $( "#div_admisiones" ), 'ux', objJson );

								objJson = cearUrlPorCamposJson( $( "#accidentesTransito" ), 'name', objJson );//2014-07-22

								//se colocan porque en las validaciones de tarifa solo se hacen para el primer resp
								var prefijo_trEps = $("tr[id$=tr_tabla_eps]").eq(0).attr("id");
								objJson.ing_tpaselTipRes =  $( "#ing_tpaselTipRes"+prefijo_trEps ).val();
								objJson.ing_cemhidCodAse =  $( "#ing_cemhidCodAse"+prefijo_trEps ).val();

								objJson.accion = "guardarDatos";	//agrego un parametro más
								objJson.wbasedato = $( "#wbasedato" ).val();
								objJson.consultaAjax = "";
								objJson.historia = $( "#ing_histxtNumHis" ).val();
								objJson.ingreso = $( "#ing_nintxtNumIng" ).val();
								objJson.documento = $.trim($( "#pac_doctxtNumDoc" ).val());
								objJson.tipodoc = $( "#pac_tdoselTipoDoc" ).val();
								objJson.wemp_pmla = $( "#wemp_pmla" ).val();
								objJson.modoConsulta = modoConsulta;

								/*Guardar los responsables*/
								var esAccTransito = $("#tabla_responsables_1_2").is(":visible");
								if( $("[name=dat_Acccas_ux_acccas]").val() != "" ){
									esAccTransito = true;
								}
								var indice_res = 0;
								objJson.responsables1 = {};

								//En la primera posicion del arr de responsables siempre va el responsable de transito
								if( esAccTransito ){
									if( $("#dat_AccreshidCodRes24").val() == "" ){
										alerta("No existe aseguradora SOAT, por favor verifique los datos del accidente.");
										return;
									}
									objJson.responsables1[ 0 ] = {};
									objJson.responsables1[ 0 ].res_tdo = "NIT";//2014-10-22
									objJson.responsables1[ 0 ].ing_cemhidCodAse = $("#dat_AccreshidCodRes24").val();
									objJson.responsables1[ 0 ].res_nom = $("#restxtCodRes").val();//2014-10-22
									objJson.responsables1[ 0 ].ing_tpaselTipRes = "E";
									objJson.responsables1[ 0 ].ing_plaselPlan = "00";
									if( $("input[name='dat_Accpol_ux_accpol']").val() == "Número de póliza")
										$("input[name='dat_Accpol_ux_accpol']").val("");
									objJson.responsables1[ 0 ].ing_poltxtNumPol = $("input[name=dat_Accpol_ux_accpol]").val();
									objJson.responsables1[ 0 ].ing_ncotxtNumCon = "";
									objJson.responsables1[ 0 ].ing_ordtxtNumAut = "";
								}

								$( "tr[id$=_tr_tabla_eps]" ).each(function( index )
								{
									if( esAccTransito )
											indice_res = index + 1; //En la posicion 0 esta el responsable transito
										else
											indice_res = index;

									objJson.responsables1[ indice_res ] = cearUrlPorCamposJson(  this , 'name' );

									//2014-10-22 Si es particular, el codigo del responsable pasa a ser el numero de documento que se ingreso
									if( objJson.responsables1[ indice_res ].ing_tpaselTipRes == "P" ){
										objJson.responsables1[ indice_res ].ing_cemhidCodAse = objJson.responsables1[ indice_res ].res_doc;
									}else{
										//Si no es particular, el tipo de documento sera NIT, y el nombre el que corresponde al campo ing_cemtxtCodAse
										objJson.responsables1[ indice_res ].res_tdo = "NIT";
										objJson.responsables1[ indice_res ].res_nom = objJson.responsables1[ indice_res ].ing_cemtxtCodAse;
									}

									objJson.responsables1[ indice_res ].cups = {};
									$(this).find("[name=ing_cachidcups]").each(function(index2){
										if( $(this).val() != "" ){
											objJson.responsables1[ indice_res ].cups[index2] = $(this).val();
										}
									});
									if( index == 0 )
									{
										/*para el responsable 2 de accidente*/
										objJson.codAseR2 = $( "[id^=ing_cemhidCodAse1]", this ).val();
									}
									if (index == 1)
									{
										//para responsable 3 de accidente
										objJson.tipoEmpR3 = $( "[id^=ing_tpaselTipRes]", this ).val();
										//cuando sea empresa
										if ($( "id^=ing_tpaselTipRes", this ).val() == 'P')
										{
											objJson.codAseR3 = $( "#pac_crutxtNumDocRes" ).val();
											objJson.nomAseR3 = $( "#pac_nrutxtNomRes" ).val();
										}
										else
										{
											objJson.codAseR3 = $( "[id^=ing_cemhidCodAse]", this ).val();
											objJson.nomAseR3 = $( "[id^=ing_cemtxtCodAse]", this ).val();
										}
									}
								});

								//DATOS DE LOS RESPONSABLES QUE VIAJAN A UNIX
								//objJson = cearUrlPorCamposJson( $("#1_tr_tabla_eps"), 'ux', objJson );

								/*Guardar los topes por responsable*/
								objJson.topes = {};

								/*$( "tr[id$=_tr_tabla_topes]" ).each(function( index )
								{
									//

									objJson.topes[ index ] = cearUrlPorCamposJson(  this , 'name' );
								});*/

								objJson.topes = {};
								//---aqui iria nueva funcion para guardar los topes
								for ( var x in objJson.responsables1)
								{


									$.ajax(
									{
										url: "admision_erp.php",
										context: document.body,
										type: "POST",
										data:
										{
											accion      : "traertopespreadmision",
											consultaAjax: '',
											wemp_pmla: $('#wemp_pmla').val(),
											documento: $.trim($( "#pac_doctxtNumDoc" ).val()),
											tipodocumento:  $( "#pac_tdoselTipoDoc" ).val()
										},
										async: false,
										dataType: "json",
										success:function(data) {

											objJson.topes = data;
											objJson.topesPreadmision = true;
										}
									});

								}


								/*Fin Guardar los topes por responsable*/

								//A todos los campos que tengan marca de agua y esten deshabilitado, le pongo la marca de agua
								$( "[aqua]:disabled" ).each(function(){
									if( $( this ).val() == '' ){
										$( this ).val( $( this ).attr( this.aqAttr ) );
									}
								});

								/********************************************************
								 * Septiembre 19 de 2013
								 ********************************************************/
								//Busco los campos que son depends y están vacios con propiedad ux
								$( "[depend][ux]" ).each(function()
								{
									if( $( this ).val() == '' || ( $( this ).val() == $( this ).attr( this.aqAttr ) ) )
									{
										objJson[ $( this ).attr( "ux" ) ] = $( "#" + $( this ).attr( "depend" ) ).val();
									}
								});
								/********************************************************/

								//RESPONSABLE QUE VIAJA A UNIX
								objJson = cearUrlPorCamposJson( $( "tr[id$=_tr_tabla_eps]" ).eq(0), 'ux', objJson );
								//objJson = cearUrlPorCamposJson( $( "tr[id$=_tr_tabla_eps]" ).eq(0), 'name', objJson );

								if( esAccTransito ){
									objJson._ux_mreemp_ux_pacemp_ux_accemp = "E";
									objJson._ux_pacres_ux_mreres = $("#dat_AccreshidCodRes24").val();
									objJson._ux_mrepla = "00";
									if( $("input[name='dat_Accpol_ux_accpol']").val() == "Número de póliza")
										$("input[name='dat_Accpol_ux_accpol']").val("");
									objJson._ux_pacpol = $("input[name=dat_Accpol_ux_accpol]").val();
								}




								for( var iii in objJson.responsables1[0] ){
									objJson[ iii ] = objJson.responsables1[0][ iii ];
								}

								//2014-10-22 Siempre tiene que existir el primer responsable
								//2014-10-22if( objJson.responsables1[ 0 ].ing_tpaselTipRes == "E" ){
								if( objJson.responsables1[ 0 ].ing_cemhidCodAse == "" ){
									alerta("NO existe primer responsable, por favor verifique.");
									return;
								}

								//2014-10-22}
								objJson.esPreAdmicion = "on";


								// console.log(objJson);

								//objJson.accion ='grabardatoprueba';
								// alert("hola");


								$.unblockUI();



								$.post("admision_erp.php",


									objJson,
									function(data){

										if( isJSON(data) == false ){
											alerta("RESPUESTA NO ESPERADA\n"+data);
											return;
										}
										data = $.parseJSON(data);

										if( data.error == 1 )
										{
											alerta( data.mensaje );
										}
										else
										{
											//Al guardar los datos se borra el log
											//borrarLog( $( "#key" ) );
											if( data.mensaje != '' )
											{
												$( "#radAdmision" ).attr( "checked", true );
												$( "#pac_fectxtFechaPosibleIngreso" ).attr( "disabled", true );
												// $( "#pac_fectxtFechaPosibleIngreso" ).addClass( "campoRequerido" );
												$( "#radAdmision" ).click();

												//Se oculta todos los acordeones
												//$( "[acordeon]" ).accordion( "option", "active", false );

												//Se muestra el acordeon de DATOS DE INGRESO - DATOS PERSONALES
												//$( "#div_datosIng_Per_Aco" ).accordion( "option", "active", 0 );

												try{
													window.scrollTo(0,0);
												}catch(e){}


													if( data.historia != '' && data.mensaje != "No se actualizo porque no se registraron cambios" )
													{

														if($("#soportesautomaticos").val()=='on')
														{

															//-- se agregavalidacion de soportes digitalizados
															//-- si la validacion de digitalizacion , si ya la digilitalizacion esta encendida
															//---si ya el centro de costos esta en on para la digitalizacion
															//---si ya la empresa hace la digtalizacion   no se piden soportes
															//-- parametro de digitalizacion apagado , pido soportes(programa viejo)
															if($("#parametroDigitalizacion").val() =='on')
															{

																	// si el parametro esta encendido tengo que mirar igual si la empresa y el centro de costos piden digitalizacion

																	var responsable1 = objJson.responsables1[ 0 ].ing_cemhidCodAse;
																	var  Empresadigitalizacion;
																	var todosdigitalizacion;
																	var ccodigitalizacion;
																	var data2;
																	$.ajax(
																	{
																		url: "admision_erp.php",
																		context: document.body,
																		type: "POST",
																		data:
																		{
																			accion      : "empresahacedigitalizacion",
																			consultaAjax: '',
																			wemp_pmla: $('#wemp_pmla').val(),
																			empresa:   responsable1
																		},
																		async: false,
																		success:function(data) {
																			if(data=='')
																			{
																				Empresadigitalizacion='off';
																			}
																			else
																			{
																				Empresadigitalizacion='on';
																				if(data=='*')
																				{

																					todosdigitalizacion ='si';
																				}
																				else
																				{

																					todosdigitalizacion ='no';
																					ccodigitalizacion = data.split(',');
																				}

																			}
																			data2=data;

																		}
																	});


																	var ccoingreso = $("#ing_seisel_serv_ing").val();
																	ccoingreso = ccoingreso.split('-');


																	if(Empresadigitalizacion=='on')
																	{
																		if(data2=='*')
																		{
																			//alert(" empresa en on y * no muestre soportes");
																			setTimeout( function()
																				{

																					//se llenan los campos de historia,ingreso,documento despues de guardar
																					$("#ing_histxtNumHis").val(data.historia);
																					$("#ing_nintxtNumIng").val(data.ingreso);
																					$("#pac_doctxtNumDoc").val(data.documento);
																					//se ponen documento,historia,ingreso readonly
																					$('#pac_doctxtNumDoc').attr("readonly", true);
																					$('#ing_histxtNumHis').attr("readonly", true);
																					$('#ing_nintxtNumIng').attr("readonly", true);

																					if( data.error == 4 )
																					{
																						alerta("Debe egresar y volver a ingresar");
																					}

																					alerta( data.mensaje+".\nCon historia "+data.historia+"-"+data.ingreso );

																					//Si se registró muestro se imprime el sticker
																					alert(data.historia);
																					if( data.historia != '' ){
																						alert("entro*"+data.historia);
																						var edad = calcular_edad_detalle( $( "#pac_fnatxtFecNac" ).val() );

																						// var edad = $( "#txtEdad" ).val();
																						var wtip = 0;

																						if( edad.age == 0 && edad.month <= 6 )
																						{
																							wtip = 2;
																						}
																						else if( edad.age <= 12  )
																						{
																							wtip = 1;
																						}
																						try{
																							//Abro el programa de sticker
																							//crearListaAutomatica(data.historia , data.ingreso);
																							//winSticker = window.open( "../../movhos/reportes/sticker_HC100.php?wtip="+wtip+"&whis="+data.historia );
																							winSticker = window.open("../../movhos/reportes/sticker_HC100.php?wtip="+wtip+"&whis="+data.historia ,'','fullscreen=no, status=no, menubar=no, toolbar=no, directories=no, resizable=yes, scrollbars=yes,titlebar=yes,width=600,height=600');

																							//Checkeo el radio button correspondiente de la ventana emergente
																							winSticker.onload = function(){
																								$( "input:radio[value="+wtip+"]", winSticker.document ).attr( "checked", true );
																							}
																						}catch(err){
																							alerta("Para imprimir el sticker, debe habilitar la opcion de abrir ventanas emergentes.");
																						}
																					}

																					//Se Inicia el formulario
																					resetear2();

																					// $( "#radPreadmision" ).attr( "checked", true );
																					// $( "#pac_fectxtFechaPosibleIngreso" ).val( $( "#pac_fectxtFechaPosibleIngreso" ).attr( "msgerror" ) );
																					// $( "[name=radPreAdmi]:checked" ).click();

																					$( "#radPreadmision" ).attr( "checked", true );
																					$( "#pac_fectxtFechaPosibleIngreso" ).attr( "disabled", false );
																					$( "#pac_fectxtFechaPosibleIngreso" ).addClass( "campoRequerido", false );

																					campo.parentNode.parentNode.style.display = 'none';

																					// consultarAgendaPreadmision( fecha, incremento );
																					if($("#soportesautomaticos").val()=='on')
																					{
																						//mostrarPreadmision();
																						//consultarAgendaPreadmision( $( "#fechaAct" ).val(), 0 );
																					}
																					else
																					{
																						mostrarPreadmision();
																						consultarAgendaPreadmision( $( "#fechaAct" ).val(), 0 );
																					}
																				location.reload();
																				}, 200
																			);

																		}
																		else
																		{
																			if($.inArray( ccoingreso[1] , ccodigitalizacion ) != -1 )
																			{
																				//alert("empresa en on y cco "+ccoingreso[1]+" no muestre soportes");
																				setTimeout( function()
																				{

																						//se llenan los campos de historia,ingreso,documento despues de guardar
																						$("#ing_histxtNumHis").val(data.historia);
																						$("#ing_nintxtNumIng").val(data.ingreso);
																						$("#pac_doctxtNumDoc").val(data.documento);
																						//se ponen documento,historia,ingreso readonly
																						$('#pac_doctxtNumDoc').attr("readonly", true);
																						$('#ing_histxtNumHis').attr("readonly", true);
																						$('#ing_nintxtNumIng').attr("readonly", true);

																						if( data.error == 4 )
																						{
																							alerta("Debe egresar y volver a ingresar");
																						}

																						alerta( data.mensaje+".\nCon historia "+data.historia+"-"+data.ingreso );

																						//Si se registró muestro se imprime el sticker

																						if( data.historia != '' ){

																							var edad = calcular_edad_detalle( $( "#pac_fnatxtFecNac" ).val() );

																							// var edad = $( "#txtEdad" ).val();
																							var wtip = 0;

																							if( edad.age == 0 && edad.month <= 6 )
																							{
																								wtip = 2;
																							}
																							else if( edad.age <= 12  )
																							{
																								wtip = 1;
																							}
																							try{
																								//Abro el programa de sticker
																								//crearListaAutomatica(data.historia , data.ingreso);
																								//winSticker = window.open( "../../movhos/reportes/sticker_HC100.php?wtip="+wtip+"&whis="+data.historia );
																								winSticker = window.open("../../movhos/reportes/sticker_HC100.php?wtip="+wtip+"&whis="+data.historia ,'','fullscreen=no, status=no, menubar=no, toolbar=no, directories=no, resizable=yes, scrollbars=yes,titlebar=yes,width=600,height=600');

																								//Checkeo el radio button correspondiente de la ventana emergente
																								winSticker.onload = function(){
																									$( "input:radio[value="+wtip+"]", winSticker.document ).attr( "checked", true );
																								}
																							}catch(err){
																								alerta("Para imprimir el sticker, debe habilitar la opcion de abrir ventanas emergentes.");
																							}
																						}

																						//Se Inicia el formulario
																						resetear2();

																						// $( "#radPreadmision" ).attr( "checked", true );
																						// $( "#pac_fectxtFechaPosibleIngreso" ).val( $( "#pac_fectxtFechaPosibleIngreso" ).attr( "msgerror" ) );
																						// $( "[name=radPreAdmi]:checked" ).click();

																						$( "#radPreadmision" ).attr( "checked", true );
																						$( "#pac_fectxtFechaPosibleIngreso" ).attr( "disabled", false );
																						$( "#pac_fectxtFechaPosibleIngreso" ).addClass( "campoRequerido", false );

																						campo.parentNode.parentNode.style.display = 'none';

																						// consultarAgendaPreadmision( fecha, incremento );
																						if($("#soportesautomaticos").val()=='on')
																						{
																							//mostrarPreadmision();
																							//consultarAgendaPreadmision( $( "#fechaAct" ).val(), 0 );
																						}
																						else
																						{
																							mostrarPreadmision();
																							consultarAgendaPreadmision( $( "#fechaAct" ).val(), 0 );
																						}
																				location.reload();
																				}, 200
																				);

																			}
																			else
																			{
																			   //alert("empresa en on y muestre");
																			   crearListaAutomatica(data.historia , data.ingreso,'','',data.documento,data.mensaje );

																			}
																		}

																	}
																	else
																	{
																		//alert("muestre soportes");
																		crearListaAutomatica(data.historia , data.ingreso,'','',data.documento,data.mensaje );
																	}

																// DIGITALIZACION DE SOPORTES
																// debe mover la carpeta de preadmision a admision
																moverSoportesPreadmision(data.historia,data.ingreso,$("#pac_tdoselTipoDoc").val(),$.trim($("#pac_doctxtNumDoc").val()),$( "#ing_feitxtFecIng" ).val());



															}
															else
															{
																	crearListaAutomatica(data.historia , data.ingreso,'','',data.documento,data.mensaje );

															}


														}




													}
													/*setTimeout( function()
													{

														//se llenan los campos de historia,ingreso,documento despues de guardar
														$("#ing_histxtNumHis").val(data.historia);
														$("#ing_nintxtNumIng").val(data.ingreso);
														$("#pac_doctxtNumDoc").val(data.documento);
														//se ponen documento,historia,ingreso readonly
														$('#pac_doctxtNumDoc').attr("readonly", true);
														$('#ing_histxtNumHis').attr("readonly", true);
														$('#ing_nintxtNumIng').attr("readonly", true);

														if( data.error == 4 )
														{
															alerta("Debe egresar y volver a ingresar");
														}

														alerta( data.mensaje+".\nCon historia "+data.historia+"-"+data.ingreso );

														//Si se registró muestro se imprime el sticker
														if( data.historia != '' ){

															var edad = calcular_edad_detalle( $( "#pac_fnatxtFecNac" ).val() );

															// var edad = $( "#txtEdad" ).val();
															var wtip = 0;

															if( edad.age == 0 && edad.month <= 6 )
															{
																wtip = 2;
															}
															else if( edad.age <= 12  )
															{
																wtip = 1;
															}
															try{
																//Abro el programa de sticker
																//crearListaAutomatica(data.historia , data.ingreso);
																//winSticker = window.open( "../../movhos/reportes/sticker_HC100.php?wtip="+wtip+"&whis="+data.historia );
																winSticker = window.open("../../movhos/reportes/sticker_HC100.php?wtip="+wtip+"&whis="+data.historia ,'','fullscreen=no, status=no, menubar=no, toolbar=no, directories=no, resizable=yes, scrollbars=yes,titlebar=yes,width=600,height=600');

																//Checkeo el radio button correspondiente de la ventana emergente
																winSticker.onload = function(){
																	$( "input:radio[value="+wtip+"]", winSticker.document ).attr( "checked", true );
																}
															}catch(err){
																alerta("Para imprimir el sticker, debe habilitar la opcion de abrir ventanas emergentes.");
															}
														}

														//Se Inicia el formulario
														resetear2();

														// $( "#radPreadmision" ).attr( "checked", true );
														// $( "#pac_fectxtFechaPosibleIngreso" ).val( $( "#pac_fectxtFechaPosibleIngreso" ).attr( "msgerror" ) );
														// $( "[name=radPreAdmi]:checked" ).click();

														$( "#radPreadmision" ).attr( "checked", true );
														$( "#pac_fectxtFechaPosibleIngreso" ).attr( "disabled", false );
														$( "#pac_fectxtFechaPosibleIngreso" ).addClass( "campoRequerido", false );

														campo.parentNode.parentNode.style.display = 'none';

														// consultarAgendaPreadmision( fecha, incremento );
														if($("#soportesautomaticos").val()=='on')
														{
															//mostrarPreadmision();
															//consultarAgendaPreadmision( $( "#fechaAct" ).val(), 0 );
														}
														else
														{
															mostrarPreadmision();
															consultarAgendaPreadmision( $( "#fechaAct" ).val(), 0 );
														}
													}, 200
												);*/
											}
										}
									}
								);
							}
							else{
								var campos = getNombresCamposError();
								alerta( " Hay datos incompletos, por favor verifique los campos de color amarillo \n"+campos );
							}
						}
						else{
							alerta( " Hay datos incompletos, por favor verifique los campos de color amarillo" );
						}
					}
					else
					{
						alerta("Solo se permite actualizar el ultimo ingreso");
					}
					//Fin de guardar datos

				}
			}
		}
	);
}


function resetear2( inicio )
{
	//Variable para saber si esta en modo consulta o no
	modoConsulta = false;

	//todos los que tengan la clase reset se ponen en blanco
	// $("#div_admisiones").find(":input:visible").each(function(){
			// if ($(this).hasClass('reset'))
			// {
				// $(this).val("");
			// }

	// });

	$( "select,textarea,input[type=text],input[type=hidden]", $("#div_admisiones") ).val( '' );
	$( "input[type=radio],input[type=checkbox]", $("#div_admisiones") ).attr( 'checked', false );

	// iniciarMarcaAqua();
    //para mostrar la fecha actual
	var now = new Date();
	var hora = now.getHours();
	var minutos = now.getMinutes();
	var segundos = now.getSeconds();
	if (hora < 10) {hora='0'+hora}
	if (minutos < 10) {minutos='0'+minutos}
	if (segundos < 10) {segundos='0'+segundos}
	horaActual = hora + ":" + minutos + ":" + segundos;


	//datos por defecto a iniciar
	$( "#pac_zonselZonRes" ).val( 'U' );
	//$( "#pac_trhselTipRes" ).val( 'N' );
	$( "#ing_lugselLugAte" ).val( '1' );
	$( "#pac_tdoselTipoDoc" ).val( 'CC' );
	$( "#pac_tdaselTipoDocRes" ).val( 'CC' );
	$( "#pac_fnatxtFecNac" ).val($( "#fechaAct" ).val() ); //fecha aut
	calcular_edad($( "#pac_fnatxtFecNac" ).val()); //pac_fnatxtFecNac
	$( "#ing_feitxtFecIng" ).val( $( "#fechaAct" ).val() ); //fecha ing
	$( "input[type='res_firtxtNumcon']" ).val( $( "#fechaAct" ).val() ); //fecha ing
    // $( "#ing_hintxtHorIng" ).val($( "#horaAct" ).val() ); //hora ing
	$( "#ing_hintxtHorIng" ).val(horaActual);
	$( "#ing_fhatxtFecAut" ).val($( "#fechaAct" ).val() ); //fecha aut
	$( "#ing_hoatxtHorAut" ).val( horaActual); //hora aut

	valorDefecto = $( "#ing_claselClausu>option[defecto='on']" ).val();
	console.log( "valor defecto 1 "+valorDefecto);
	if(valorDefecto == undefined)
		$( "#ing_claselClausu" ).val( 1 );
	else
		$( "#ing_claselClausu" ).val( valorDefecto );
	$( "#pac_petselPerEtn" ).val( 6 );
	$( "#pac_fnatxtFecNac" ).val("");

	resetAqua( );

	$("#bot_navegacion").css("display", "none"); //se oculta el div de navegacion de resultados
	$("#bot_navegacion1").css("display", "none"); //se oculta el div de navegacion de resultados

	//2014-05-06 validarTipoResp('');
	var objetoRes = $("[name=ing_tpaselTipRes]").eq(0);
	//console.log("es aqui "+objetoRes[0].value);
	validarTipoResp(objetoRes[0]);
	//se borran todos los label de error que se encuentren
	$( "label" ).remove();
	//se pone el estado del paciente en blanco como es un span se le envia .html
	$("#spEstPac").html("");

	$( "#div_accidente_evento").css( {display: "none"} );
	//se ponen los campos readonly false para que se puedan llenar
	$('#pac_doctxtNumDoc').attr("readonly", false);
	$('#ing_histxtNumHis').attr("readonly", false);
	$('#ing_nintxtNumIng').attr("readonly", false);

	//Al iniciar datos se borra el log
	if( !inicio )
	{
		borrarLog( $( "#key" ) );
	}

	// $( "#radAdmision" ).attr( "checked", true );
	// $( "#pac_fectxtFechaPosibleIngreso" ).val( $( "#pac_fectxtFechaPosibleIngreso" ).attr( "msgerror" ) );
	// $( "[name=radPreAdmi]:checked" ).click();

	ultimaPreadmisionCargada = '';
}


/************************************************************************
 * Agosto 21 de 2013
 * Calcula la edad de un paciente con años, meses y días
 ************************************************************************/
function calcular_edad_detalle( fecha )
{
	var objEdad = {};

	var fecha = fecha.split( "-" );

    var today = new Date();
    var birthDate = new Date( fecha[0], fecha[1]-1, fecha[2], 0, 0, 0, 0 );
    objEdad.age = today.getFullYear() - birthDate.getFullYear();
    objEdad.month = today.getMonth() - birthDate.getMonth();
	objEdad.day = today.getDate() - birthDate.getDate();

    if( objEdad.month < 0 || ( objEdad.month === 0 && today.getDate() < birthDate.getDate() ) )
	{
		if( objEdad.age > 0 )
			objEdad.age--;
    }

	if( objEdad.month < 0 ){
		objEdad.month = 12 + objEdad.month;
	}

	if( objEdad.day < 0 ){

		if( objEdad.month > 0 )
			objEdad.month--;

		objEdad.day = 30 + objEdad.day;
	}

	return objEdad;
}

/****************************************************************************************************************
 * Agosto 26 de 2013
 * Deja todos los campos del formulario en blanco
 ****************************************************************************************************************/
function iniciar()
{
	if( hayCambios )
	{
		if( !confirm( "Perder&aacute; la informaci&oacute;n digitada. Desea continuar?" ) )
		{
			return;
		}
	}
	$("#tabla_eps").find("tr[id$='_tr_tabla_eps']").remove();
	var wbasedato = $("#wbasedato").val();
	var wemp_pmla = $("#wemp_pmla").val();
	var accion_consultar = $("#accion_consultar").val();

	addFila('tabla_eps',wbasedato,wemp_pmla);

	// $( "input:hidden,input:text,select,textarea", $( "#div_admisiones" ) ).val('');
	$( "input[type=hidden][id!='codCausaAccTrans'],input[type=text],select,textarea", $( "#div_admisiones" ) ).val('');
	$( "input:radio,input:checkbox,select,textarea", $( "#div_admisiones" ) ).attr( "checked", false );

	$( "#pac_doctxtNumDoc,#ing_histxtNumHis,#ing_nintxtNumIng" ).attr( "readonly", false );

	$( "#bot_navegacion1,#bot_navegacion" ).css({display:"none"});

	resetAqua( $( "#div_admisiones" ) );

	$( "#spEstPac" ).html( '' );

	ultimaPreadmisionCargada = '';

	$( "#txtAdmisionPreadmision" ).html( '' );

	$("#tabla_responsables_1_2,#tr_titulo_tercer_resp").hide();
	$("#div_datos_autorizacion,tabla_eps").show();
	// $( "#ing_ordtxtNumAut,#ing_fhatxtFecAut,#ing_hoatxtHorAut,#ing_npatxtNomPerAut,#ing_cactxtcups,#ing_pcoselPagCom" ).attr( "disabled", false );
	// $( "#ing_fhatxtFecAut" ).val($( "#fechaAct" ).val() ); //fecha aut
	// $( "#ing_hoatxtHorAut" ).val( horaActual); //hora aut
	$("#ing_fhatxtFecAut,#ing_hoatxtHorAut").removeClass("campoRequerido");
	$("#div_accidente_evento").hide();

	//se borra todo lo del div para que cuanse se abra la lista este en blanco
	$("#div_contenedor").html("");

	if( $( "input[name='btRegistrarActualizar']" ).attr( "graba" ) == "on" ){
		$( "input[name='btRegistrarActualizar']" ).attr( "disabled", false );
	}else{
		$( "input[name='btRegistrarActualizar']" ).attr( "disabled", true );
	}
	$( "input[name='btRegistrarActualizar']" ).val( "Admitir" );

	var wbasedato = $("#wbasedato").val();
	var wemp_pmla = $("#wemp_pmla").val();

	//se borran los trs de la tabla_eps menos la primera fila


	//se borran los trs de la tabla_topes menos la primera fila
	$("#tabla_topes").find("tr[id$='_tr_tabla_topes']:not([id='1_tr_tabla_topes'])").remove();

	//se ponen los valores de ese tr en blanco
	$("[id^=ing_tpaselTipRes],[id^=ing_cemtxtCodAse],[id^=ing_plaselPlan]", $('#tabla_eps >tbody >tr').eq(2) ).val("");


	//se pone el div de especialidades oculto
	$("#div_mensaje_PerEspeciales").css("display", "none");

	//Consultar el numero de historia
	consultarConsecutivo();
	nuevoAccidenteTransito();

	$("#numTurnoPaciente").attr("valor", "").html("SIN TURNO!!!");
	if(accion_consultar == "on"){
		$("#accion_consultar").val(accion_consultar)
		$("input[type='button'][name='btRegistrarActualizar']").attr("disabled","disabled");
		$("input[type='button'][name='btRegistrarActualizar']").hide();
	}
}

function consultarConsecutivo(){
	var objJson = {};	//Creo el objeto
	objJson.accion = "consultarConsecutivo";	//agrego un parametro más
	objJson.wbasedato = $( "#wbasedato" ).val();
	objJson.consultaAjax = "";
	objJson.wemp_pmla = $( "#wemp_pmla" ).val();

	$.blockUI({message: "Espere un momento por favor..."});

	$.post("admision_erp.php",
		objJson,
		function(data){
			$.unblockUI();
			if( !isNaN(data) ){
				$("#ing_histxtNumHis").attr("placeholder", data );
			}
		}
	);

}

window.onbeforeunload = function()
{
	borrarLog( $( "#key" ) );
}

function ocultarMostrarPacientesIngresados(num)
{
	$( "#dvAgendaAmdisionDatos" ).toggle();
}


function mostrarAdmision()
{
	resetear();
	$( "#radAdmision" ).attr( "checked", true );
	$( "#radPreadmision" ).attr( "checked", false );
	opcionAdmisionPreadmision();

	hayCambios = false;
	if( $( "input[name='btRegistrarActualizar']" ).attr( "graba" ) == "on" ){
		$( "input[name='btRegistrarActualizar']" ).attr( "disabled", false );
		var fechaHora;
		fechaHora = consultarFechaHoraActual();
		$("#wfiniAdm").val(fechaHora.fecha);
		$("#whiniAdm").val(fechaHora.hora);
	}else{
		$( "input[name='btRegistrarActualizar']" ).attr( "disabled", true );
	}
	$( "input[name='btRegistrarActualizar']" ).val( "Admitir" );

	//llamarModalNoFlujo();

}
// funcion que consulta en las tablas de flujos que pacientes no tienen asignado flujo en los ultimos 3 dias , este parametro se puede modificar
function llamarModalNoFlujo()
{

	$.post("admision_erp.php",
	{
		accion      : "llamarModalNoFlujo",
		consultaAjax: '',
		wemp_pmla: $('#wemp_pmla').val()

	}, function(data){
		//alert (data.contenidoDiv);
		if(data.html == 'abrirModal' )
		{

			$("#divfaltantesporflujo").html(data.contenidoDiv).show().dialog({
					dialogClass: 'fixed-dialog',
					modal: true,
					title: "<div align='center' style='font-size:10pt'>Lista de Pacientes sin flujo</div>",
					width: "auto",
					height: "400"

			});

			$("#divfaltantesporflujo").css({
              width:'auto', //probably not needed
              height:'auto'
			});

			//$( "#divfaltantesporflujo" ).dialog({ dialogClass: 'hide-close' });

		}


	}, 'json');



}

function actualizarEstadoSoporte( valor, soporte, whistoria, wingreso)
{


	if($("#checkboxsoporte_"+soporte).is(':checked'))
	{
		valor ='s';
	}
	else
	{
		valor='na';
	}
	$.post("admision_erp.php",
	{
		accion      : "actualizarEstadoSoporte",
		consultaAjax: '',
		wsoporte:  soporte,
		whistoria: whistoria,
		wingreso:  wingreso,
		westado:   valor,
		wemp_pmla: $('#wemp_pmla').val()

	}, function(data){

		//alert(data);

	});

}

function cerrarsinflujo(){

		$("#divfaltantesporflujo" ).dialog('close');

}

//
function crearflujo(historia, ingreso)
{
	//alert(historia+"---"+ingreso);
	crearListaAutomatica(historia, ingreso, '' , '' );

}

function prepararParaConsulta(){
	mostrarAdmision();
	iniciar();
	$("#accion_consultar").val("on");
	$("input[type='button'][name='btRegistrarActualizar']").attr("disabled","disabled");
	$("input[type='button'][name='btRegistrarActualizar']").hide();
}

function verificarDocumento(){
	if( $("#accion_consultar").val() != "on" ){
		conservarNumeroDeTurno();
		mostrarDatosPreadmision();
		consultarClientesEspeciales();
		consultarSiActivo();
		consultarSiRechazado();
		consultarSiPreanestesia();
	}
}

function verificarTriageUrgencias(){

	var wbasedato    = $("#wbasedato").val();
	var cedula       = $.trim($("#pac_doctxtNumDoc").val());
	var tipoDoc      = $("#pac_tdoselTipoDoc").val();
	var centroCostos = $("#ing_seisel_serv_ing").val();
	if( tipoDoc ==  "" && cedula == "")
		return;
	$("#div_des_triage").show();
	if( $("#permitirVerListaTurnos") ){
		//---> consultar triage urgencias del paciente

		$.ajax({
			url:   "admision_erp.php",
			type:  "post",
			async: false,
			data:  {

				     consultaAjax: "",
				     accion:       "verificarTriageUrgencias",
				     tipoDoc:      tipoDoc,
				     documento:    cedula,
				     wbasedato:    wbasedato,
				     wemp_pmla:    $("#wemp_pmla").val()
			},
			success: function(respuesta){
	           $("#div_des_triage").attr("title", respuesta);
	           $("#div_des_triage").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50});
	        }
		});

	}else{
		return;
	}
}

function consultarFechaHoraActual(){
	var datos;
	$.ajax({
		url: "admision_erp.php",
        type: 'post',
        async: false,
        data: {
            consultaAjax         : '',
            accion               : 'consultarFechaHoraActual'
        },
        success: function(data){
           datos = data;
        },
        dataType:"json"
    });
    return(datos);
}

var llenarTablaLog = true;
function mostrarPreadmision()
{
	if( hayCambios )
	{
		if( !confirm( "Perder\u00E1 la informaci\u00F3n digitada. Desea continuar?" ) )
		{
			return;
		}
	}

	//alert("hola");
	//2013-03-25
	llenarTablaLog = false;
	location.reload();$("#accion_consultar").val("on");
	return;
	//alert("sale");

	resetear();
	resetearAccidentes();
	resetearEventosCatastroficos();

	$( "#radPreadmision" ).attr( "checked", true );
	$( "#radAdmision" ).attr( "checked", false );
	opcionAdmisionPreadmision();
	$( "#bot_navegacion1" ).css( "display", "none" );
	$( "#bot_navegacion" ).css( "display", "none" );

	consultarAgendaPreadmision( $( "#fecActAgenda" ).html(), 0 );
	consultarAgendaAdmitidos( $( "#fecActAdmitidos" ).html(), 0 );
}

/**************************************************************************************************
 * Septiembre 10 de 2013
 *
 * Realiza el ajax para consultar los datos de admitidos
 **************************************************************************************************/
function consultarAgendaAdmitidos( fecha, incremento )
{
	var objJson = {};	//Creo el objeto
	objJson.accion          = "consultarAdmitidos";	//agrego un parametro más
	objJson.wbasedato       = $( "#wbasedato" ).val();
	objJson.consultaAjax    = "";
	objJson.wemp_pmla       = $( "#wemp_pmla" ).val();
	objJson.cco_usuario     = $( "#cco_usuario" ).val();
	objJson.fecha           = fecha;
	objJson.incremento      = incremento;
	objJson.consulta        = $("#perfil_consulta").val();
	objJson.filtrarCcoAyuda = $("#filtrarCcoAyuda").val();

	$.blockUI({message: "Espere un momento por favor..."});

	$.post("admision_erp.php",
		objJson,
		function(data){
			$.unblockUI();
			if( data.error == 1 )
			{
				console.log("error = 1");
				alerta( data.mensaje );
			}else if(data.error == 2 ){
				console.log("error = 2");
				alerta( data.mensaje);
				return;
			}
			else
			{
				if( data.mensaje != '' )
					alerta( data.mensaje );

				if( data.html )
				{
					$( "#dvAgendaAmdisionDatos" ).html( data.html );

					$( "#dvAgendaAmdisionDatos [fecha]" ).datepicker({
						dateFormat:"yy-mm-dd",
						fontFamily: "verdana",
						dayNames: [ "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo" ],
						monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ],
						dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
						dayNamesShort: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
						monthNamesShort: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic" ],
						changeMonth: true,
						changeYear: true,
						yearRange: "c-100:c+100"
					});
				}
			}


		},
		"json"
	);
}

/*****************************************************************************************
 * Septiembre 02 de 2013
 * Creo clones de los options de ORIGEN E LA ATENCIÓN, solo los de accidentes de transito
 * y evento catastróficos
 *****************************************************************************************/
optOrigenAtencion = '';
/*****************************************************************************************/

/********************************************************************************************************************************
 * Anula una admisión
 ********************************************************************************************************************************/
function anularAdmision( historia, ingreso, tipoDoc, cedula, automatico = '' ){

	continuar = false;
	if( automatico == "on" ){
		continuar = true;
	}else{
		if( confirm( "Desea anular la admisión?" ) ){
			continuar = true;
		}else{
			continuar = false;
			return;
		}
	}
	if( continuar ){

		var objJson = {};	//Creo el objeto
		objJson.accion = "anularAdmision";	//agrego un parametro más
		objJson.wbasedato = $( "#wbasedato" ).val();
		objJson.consultaAjax = "";
		objJson.wemp_pmla = $( "#wemp_pmla" ).val();
		objJson.historia = historia;
		objJson.ingreso = ingreso;
		objJson.tipoDoc = tipoDoc;
		objJson.cedula = cedula;

		if(automatico != "on")
			$.blockUI({message: "Espere un momento por favor..."});

		$.ajax({
		  type: "POST",
		  url: "admision_erp.php",
		  data: objJson,
		  async: false,
		  success : function(data){
		  	if(automatico != "on")
		  		$.unblockUI();
		  	if( data.mensaje != '' && automatico != "on")
					alert( data.mensaje );
			if( automatico != "on" ){
				consultarAgendaPreadmision( $( "#fecActAgenda" ).html(), 0 );
				consultarAgendaAdmitidos( $( "#fecActAdmitidos" ).html(), 0 );
			}

		  },
		  dataType: "json"
		});
		/*$.post("admision_erp.php",
			objJson,
			function(data){
				if( data.mensaje != '' )
					alerta( data.mensaje );
				consultarAgendaPreadmision( $( "#fecActAgenda" ).html(), 0 );
				consultarAgendaAdmitidos( $( "#fecActAdmitidos" ).html(), 0 );

				$.unblockUI();
			},
			"json"
		);*/
	}
}

function imprimirHistoria( historia, ingreso ){
	wbasedatoImp     = $("#wbasedatoImp").val();
	winSticker		 = window.open( "../../ips/reportes/r001-admision.php?wpachi="+historia+"&wingni="+ingreso+"&empresa="+wbasedatoImp );
	consultarAgendaPreadmision( $( "#fecActAgenda" ).html(), 0 );
	consultarAgendaAdmitidos( $( "#fecActAdmitidos" ).html(), 0 );
}

function mostrarDatosDemograficos()
{
	//No haga el llamado si el documento está vacio
	if( $.trim($( "#pac_doctxtNumDoc" ).val()) == '' )
		return;

	// $.blockUI({message: "Por favor espere..."});

	//Variable para saber si esta en modo consulta o no
	modoConsulta = true;
	llenadoAutomatico = true;

		var objJson = {};

		objJson.accion = "mostrarDatosDemograficos";	//agrego un parametro más
		objJson.wbasedato = $( "#wbasedato" ).val();
		objJson.consultaAjax = "";
		objJson.pac_tdo = $( "#pac_tdoselTipoDoc" ).val();
		objJson.pac_doc = $.trim($( "#pac_doctxtNumDoc" ).val());
		objJson.wemp_pmla = $( "#wemp_pmla" ).val();

		$.post("admision_erp.php",
			objJson,
			function(data){

				$.unblockUI();

				if( isJSON(data) == false ){
					alerta("RESPUESTA NO ESPERADA\n"+data);
					modoConsulta = false;
					llenadoAutomatico = false;
					return;
				}
				data = $.parseJSON(data);

				if( data.error == 1 )
				{
					alerta( data.mensaje );
				}
				else
				{
					if( data.mensaje != '' )
						alerta( data.mensaje );

					if( data.infoing )
					{
						if( true )
						{
							informacionIngresos = data;
							informacionIngresos.regTotal = data.infoing.length;
							informacionIngresos.posAct = data.infoing.length-1;
							$("#cargoDatosConsulta").val("on");

							navegacionIngresosPreadmision( 0 );
							cambioCobertura( $("#pac_tusselCobSal") );
						}
					}else{
						if( $("#cargoDatosConsulta").val() == "on"){
							console.log( "Restaurar" );
							resetear();

							// --> Limpiar el numero del turno, 2015-11-24: Jerson trujillo.
							if(numeroTurnoTemporal != "")
							{
								$("#numTurnoPaciente").attr("valor", numeroTurnoTemporal).html(numeroTurnoTemporal);
								numeroTurnoTemporal = "";
							}
							else
								$("#numTurnoPaciente").attr("valor", "").html("SIN TURNO!!!");

						}else{
							console.log("Dejar datos tal cual");
						}
					}

					modoConsulta = false;
					llenadoAutomatico = false;
					llenarDatosLog(true);
				}
			}
		);
}

function buscarAseguradorasVehiculo()
{
	var wbasedato = $("#wbasedato").val();
	//Asigno autocompletar para la busqueda de aseguradoras
	$( "[name=_ux_accasn]" ).autocomplete("admision_erp.php?consultaAjax=&accion=consultarAseguradoraVehiculo&wbasedato="+wbasedato,
	{
		cacheLength:1,
		delay:300,
		max: 100,
		scroll: false,
		scrollHeight: 500,
		matchSubset: false,
		matchContains: true,
		width:250,
		autoFill:false,
		minChars: 3,
		json:"json",
		formatItem: function(data, i, n, value) {

			//convierto el string en json
			eval( "var datos = "+data );

			return datos[0].usu;	//Eso es lo que se muestra al usuario
		},
		formatResult: function(data, value){
			//convierto el string en json
			eval( "var datos = "+data );

			return datos[0].valor.des;
		}
	}).result(
		function(event, item ){

			// //La respuesta es un json
			// //convierto el string en formato json
			eval( "var datos = "+item );

			//Guardo el ultimo valor que selecciona el usuario
			//Esto en una propiedad inventada
			this.value = datos[0].valor.des;
			this._lastValue = this.value;
			$( this ).removeClass( "campoRequerido" );

			$( "input[type=hidden]", this.parentNode ).val( datos[0].valor.cod );
		}
	).on({
		change: function(){

			var cmp                    = this;
			var buscarDatosResponsable = true;

			setTimeout( function(){

				//Pregunto si la pareja es diferente
				if( ( ( cmp._lastValue && cmp._lastValue != cmp.value ) || ( !cmp._lastValue && cmp.value != $( cmp ).attr( cmp.aqAttr ) ) )
					|| ( cmp._lastCodigo && cmp._lastCodigo != $( "input[type=hidden]", cmp.parentNode ).val() )
				)
				{
					alerta( " Digite una Aseguradora v\u00E1lida" )
					$( "input[type=hidden]", cmp.parentNode ).val( '' );
					cmp.value = '';
					cmp.focus();
					// cmp.blur();
				}else{

					if( $("[name='dat_Acccas_ux_acccas']").val() != $("[name='dat_Acccas_ux_acccas']").attr("codigoOriginal") ){
						if( $.trim($("[name='dat_Acccas_ux_acccas']").attr("codigoOriginal")) != "" ){//--> si original = vacio, no verifica saldo unix
							var habilitadoParaCambiar = validarFacturacionUnix( $("[name='dat_Acccas_ux_acccas']").attr("codigoOriginal") );
							if( !habilitadoParaCambiar ){
								alerta( " El responsable original tiene facturas activas en unix, no se puede realizar el cambio" );
								$("[name='dat_Acccas_ux_acccas']").val( $("[name='dat_Acccas_ux_acccas']").attr("codigoOriginal") );
								$("[name='_ux_accasn']").val( $("[name='_ux_accasn']").attr("empresaOriginal") );
								buscarDatosResponsable = false;
							}
						}
					}
					if( buscarDatosResponsable )//--> para que no repita llamados si en realidad continua el original
							buscarPrimerResp();
				}
			}, 200 );

		}
	});

}

function validarFacturacionUnix( codigoResponsableOriginal ){

	var historia  = $("#ing_histxtNumHis").val();
	var ingreso   = $("#ing_nintxtNumIng").val();
	var respuesta = false;

	$.ajax({
			url: "admision_erp.php",
			type: "POST",
			data:
			{
				consultaAjax    : '',
				accion          : 'validarFacturacionUnix',
				historia        : historia,
				ingreso         : ingreso,
				responsable     : codigoResponsableOriginal,
				wemp_pmla       : $("#wemp_pmla").val(),
				wbasedato       : $("#wbasedato").val()
			},
			async: false,
			success:function(data) {
				//return( data.respuesta );
				if(data.error == 1){

					if (data.mensaje != ''){
						alerta(data.mensaje);
					}
					respuesta = false;
				}
				respuesta = data.respuesta;

			},
		dataType: "json"
	});
	return( respuesta );
}

function buscarSegundoResp()
{
	var wbasedato = $("#wbasedato").val();  //el medcid que une la tabla 10 con la 51 en citascs faltan la otras

		   $.post("admision_erp.php",
					{
						wbasedato:      wbasedato,
						consultaAjax:   '',
						accion:         'consultarSegundoResp'

					}
					,function(data) {
					if(data.error == 1)
					{
						if (data.mensaje != '')
						{
							alerta(data.mensaje);
						}
					}
					else
					{
						// if (data.mensaje != '')
						// {
							// alert(data.mensaje);  // update Ok.
						// }

						$("#re2txtCodRes2").val(data.nom);
						$("#dat_Accre2hidCodRes2").val(data.cod);
						$("#re2hidtopRes2").val(data.topeS); //valor tope
						$("#re2txtCodRes2").removeAttr( "msgerror" );
						$("#re2txtCodRes2").removeClass( "campoRequerido" );
						$("#re2txtCodRes2").attr("readonly", true);


					}
				},
				"json"
			);
}
//busca primer responsable de accidente de transito
function buscarPrimerResp()
{

	var wbasedato = $("#wbasedato").val();
	var asegu = $("[name=dat_Acccas_ux_acccas]").val(); //campo aseguradora del formulario de accidentes [name=_ux_accasn]
	if( asegu == "" ) return;

	$.post("admision_erp.php",
	{
		wbasedato      : wbasedato,
		consultaAjax   :   '',
		accion         : 'consultarPrimerResp',
		asegu          : asegu,
		fechaAccidente : $("#dat_Accfec").val()

	},function(data){
		if (data.error == 1)
		{
			if (data.mensaje != '')
			{
				alerta(data.mensaje);
			}
		}
		else
		{

			if (data.mensaje != '')
			{
				alerta(data.mensaje);  // update Ok.
			}
			$("#restxtCodRes").val(data.nom);
			$("#dat_AccreshidCodRes24").val(data.cod24); //codigo de la tabla 24
			// $("#dat_Acc_cashidCodRes193").val(data.cod193); //codigo de la tabla 193
			$("#dat_AcctartxtTarRes").val(data.tar);
			$("#dat_AccvsmtxtSalMin").val(data.vsm);
			$("#dat_AcctoptxtValTop").val(data.tope);

			$("#restxtCodRes,#dat_AccreshidCodRes24,#dat_AcctartxtTarRes,#dat_AccvsmtxtSalMin,#dat_AcctoptxtValTop").removeAttr( "msgerror" );
			$("#restxtCodRes,#dat_AccreshidCodRes24,#dat_AcctartxtTarRes,#dat_AccvsmtxtSalMin,#dat_AcctoptxtValTop").removeClass( "campoRequerido" );
			$("#restxtCodRes,#dat_AccreshidCodRes24,#dat_AcctartxtTarRes,#dat_AccvsmtxtSalMin,#dat_AcctoptxtValTop").attr("readonly", true);

			//2014-02-25 COMO VIENE POR SOAT, SE DEBE MOSTRAR COMO PRIMER RESPONSABLE
			$('#tabla_responsables_1_2').show();
			$('#tr_titulo_tercer_resp').show();
			$("#restxtCodRes,#dat_AccreshidCodRes24,#dat_AcctartxtTarRes,#dat_AccvsmtxtSalMin,#dat_AcctoptxtValTop,ing_vretxtValRem").attr("disabled", false);
			reOrdenarResponsables(); //Para poner R1,R2,R3...
		}
	},
	"json"

	);

}

function listarEventosCatastroficos()
{
	//si no viene vacio el div_contenedor que muestre lo que ya tiene
	// if ($("#div_contenedor").html() != '' && consultaEvento == true)
	//if ( $("#ing_caiselOriAte").val() == 06 && $('#div_eventos_catastroficos').find('input[type=checkbox]').is(':checked') == true)
	if ($("#div_contenedor").html() != '')
	{
		$.blockUI(
				{
					message: $("#div_contenedor"),
					css: { 	top: ($(window).height()*0.5) /5 + 'px',
					left: ($(window).width()*0.5) /5 + 'px',
					width: "50%",
					heightStyle: "content",
					textAlign: "left",
					cursor: ""
					}
				}
			);
		if(informacionIngresos != "")
				{
					//codigo del evento despues de consultar la información del paciente ya registrada en BD
					var codEven = informacionIngresos.infoing[ informacionIngresos.posAct ].det_Catcod
					if ( codEven )
					{
						//$("#chkagregar_"+codEven )[0] = document.getElementById( 'chkagregar_"+codEven' )
						//sin el [0] es todo el objeto, con el [0] es el elemento (this)
						seleccionarCheckbox( $("#chkagregar_"+codEven )[0] ,codEven);
					}
				}
	}
	else
	{
		$.post("admision_erp.php",
		{
			consultaAjax:   '',
			accion:         'listaEventos',
			wbasedato:      $("#wbasedato").val()

		}
		,function(data)
		{
			if (data.error == 1 && data.mensaje != '')
			{
				alerta(data.mensaje);
			}
			else
			{
				$("#div_contenedor").html(data.html);
				//lo que retorna el data.html se le pone al blockui
				 $.blockUI(
				 {
					message: $("#div_contenedor"),
					css: { 	top: ($(window).height()*0.5) /5 + 'px',
					left: ($(window).width()*0.5) /5 + 'px',
					width: "50%",
					heightStyle: "content",
					textAlign: "left",
					cursor: ""
				}
				 }
				 );

				// if(informacionIngresos != "")
				// {
					// //codigo del evento despues de consultar la información del paciente ya registrada en BD
					// var codEven = informacionIngresos.infoing[ informacionIngresos.posAct ].det_Catcod
					// if ( codEven )
					// {
						// //$("#chkagregar_"+codEven )[0] = document.getElementById( 'chkagregar_"+codEven' )
						// //sin el [0] es todo el objeto, con el [0] es el elemento (this)
						// seleccionarCheckbox( $("#chkagregar_"+codEven )[0] ,codEven);
					// }
				// }

			}
		},
		"json"
		);
	}

	 consultaEvento = false;

}

function mostrarDetalleEventosCatastroficos(codigo)
{
	var modoConsultaEvento = true;

	$.post("admision_erp.php",
	{
		consultaAjax:   '',
		accion:         'mostrarDetalleEvento',
		wbasedato:      $("#wbasedato").val(),
		codigo:			codigo

	}
	,function(data)
	{
		if (data.error == 1 && data.mensaje != '')
		{
			alerta(data.mensaje);
		}
		else
		{

			//se llena el formulario antes
			setDatos( data.infoing[ 0 ], $( "#eventosCatastroficos" ), 'name' )  ;

			//muestra el formulario
			mostrarEventosCatastroficos();
			//se le quita la clase campo requerido
			$("textarea,input,select", $("#eventosCatastroficos") ).removeClass( "campoRequerido" );
			if (modoConsultaEvento = true)
			{
				//se oculta el boton de guardar
				$("#btnGuardarEventosCatastroficos").hide();
				//se cambia el value del boton de que cierra los eventos.
				$("#btnCerrarEventosCatastroficos" ).val( "Salir" );
			}
			if (consultaEvento == true)
			{
				//se oculta el boton de guardar por si va a modificar el evento del ultimo ingreso
				$("#btnGuardarEventosCatastroficos").show();
			}
		}
	},
	"json"
	);
	modoConsultaEvento = false;
}

function cancelarEvento(evt) {

	evt.stopPropagation(); //detener la propagacion del evento

}

function seleccionarCheckbox(check,cod)
{
	var aux= $(check).is(":checked"); //se pregunta el estado viene apenas para hacer la relacion
	$('#div_eventos_catastroficos').find('input[type=checkbox]').attr('checked', false); // se deschequean todos

	if (aux) //si viene chequeado lo chequea porque antes se deschequearon todos
	{ //alert("entro 2")
		$(check).attr('checked', true);
		$( "[id=hidcodEvento]" ).val(cod); //se llena el hidden con el codigo del evento chequeado
	}
	else //esta mostrando una relacion ya hecha, se esta consultando
	{
		if (consultaEvento == true)
		{
			//alert("entro 3"+check)
			$(check).attr('checked', true);
		}
	}

}

function guardarRelacionHistoriaEvento()
{
	var validar = false;

	// ($('#div_eventos_catastroficos input[type=checkbox]').is(':checked'))
	if ($('#div_eventos_catastroficos').find('input[type=checkbox]').is(':checked'))
	{
		validar = true;
	}

	if (validar)
	{
		$.unblockUI();
	}
	else
	{
		alerta("Debe seleccionar un Evento Catastrofico");
	}

	//Si se guarda la información, muestro el div correspondiente con el botón para mostrar
			//el formulario de eventos catastroficos
			$( "#div_accidente_evento").css( {display: ""} );	//muestro el div que tiene los botones para abrir los formularios
			$( "td", $( "#div_accidente_evento") ).eq(0).css( {display: "none"} );	//oculto el boton de accidentes de transito
			$( "td", $( "#div_accidente_evento") ).eq(1).css( {display: "none"} );	//oculto el boton de eventos catastróficos
			$( "td", $( "#div_accidente_evento") ).eq(2).css( {display: ""} );  //muestro el nuevo boton que me lleva a la lista de eventos

			//para poner el foco en el select de origen de la atencion cuando se cierre el blockui
			$("#ing_caiselOriAte").focus();
}

function quitarRelacion()
{
	$('#div_eventos_catastroficos').find('input[type=checkbox]').attr('checked', false); // se deschequean todos
}

function buscarCcoTopes(tabla_referencia)
{
	var wbasedato= $("#wbasedato").val();
	var wemp_pmla= $("#wemp_pmla").val();

	//$( "#div_cont_tabla_topes"+idCodResp )


	if (tabla_referencia != "")
	{
	// para saber si la tabla tiene filas o no
				trs = $("#"+tabla_referencia,$("#div_cont_tabla_topes"+idCodResp)).find('tr[id$=tr_'+tabla_referencia+']').length;
				var value_id = 0;

				//busca consecutivo mayor
				if(trs > 0)
				{
					id_mayor = 0;
					// buscar los tr que terminen en el mismo nombre de su tabla contenedora, recorrerlos y recuperar el valor mayor
					$("#"+tabla_referencia,$("#div_cont_tabla_topes"+idCodResp)).find('tr[id$=tr_'+tabla_referencia+']').each(function() {
						id_ = $(this).attr('id');
						id_splt = id_.split('_');
						id_this = (id_splt[0])*1;
						if(id_this >= id_mayor)
						{
							id_mayor = id_this;
						}
					});
					// id_mayor++;
					value_id = id_mayor+'_tr_'+tabla_referencia;

				}
				else
				{ value_id = '1_tr_'+tabla_referencia; }

		codEsp="#top_ccotxtCcoTop"+value_id;
	}

	//Asigno autocompletar para la busqueda de paises
	$("[id^=top_ccotxtCcoTop]",$("#div_cont_tabla_topes"+idCodResp) ).autocomplete("admision_erp.php?consultaAjax=&accion=consultarCcoTopes&wbasedato="+wbasedato+"&wemp_pmla="+wemp_pmla,
	{
		cacheLength:1,
		delay:300,
		max: 100,
		scroll: false,
		scrollHeight: 500,
		matchSubset: false,
		matchContains: true,
		width:250,
		autoFill:false,
		minChars: 1,
		json:"json",
		formatItem: function(data, i, n, value) {

			//convierto el string en json
			eval( "var datos = "+data );

			return datos[0].usu;	//Eso es lo que se muestra al usuario
		},
		formatResult: function(data, value){
			//convierto el string en json
			eval( "var datos = "+data );

			return datos[0].valor.des;
		}
	}).result(
		function(event, item ){

			// //La respuesta es un json
			// //convierto el string en formato json
			eval( "var datos = "+item );

			//Guardo el ultimo valor que selecciona el usuario
			//Esto en una propiedad inventada
			this.value = datos[0].valor.des;
			this._lastValue = datos[0].valor.des;
			this._lastCodigo = datos[0].valor.cod;

			$( this ).removeClass( "campoRequerido" );

			$( "input[type=hidden]", this.parentNode ).val( datos[0].valor.cod );
		}
	).on({
		change: function(){

			var cmp = this;

			setTimeout( function(){

				//Pregunto si la pareja es diferente
				if( ( ( cmp._lastValue && cmp._lastValue != cmp.value ) || ( !cmp._lastValue && cmp.value != $( cmp ).attr( cmp.aqAttr ) ) )
					|| ( cmp._lastCodigo && cmp._lastCodigo != $( "input[type=hidden]", cmp.parentNode ).val() )
				)
				{
					alerta( " Digite un Centro de costo válida" )
					$( "input[type=hidden]", cmp.parentNode ).val( '' );
					cmp.value = '';
					cmp.focus();
					// cmp.blur();
				}
			}, 200 );


			// if( this._lastValue ){
				// this.value = this._lastValue;
			// }
			// else{
				// this.value = "";
			// }
		}
	})
	;
}
objglobal = {};
function guardartopes(codRespGlobal ,DivTopes)
{


	objglobal[codRespGlobal] = {};
	//actualiza
	//btRegistrarActualizar
	// btRegistrarActualizar
	//graba="on"

	//alert($("input[name='btRegistrarActualizar']").attr('graba'));


	// armar vector para grabar
	//--recorro todos los topes principales.

	// envio un texto con _ para saber que es un nuevo registro
	var historia = ($( "#ing_histxtNumHis" ).val());

    var ingreso  = ($( "#ing_nintxtNumIng" ).val());


	$(".campoObligatorio").removeClass('campoObligatorio');

	var insertar = '';
	cumplevalidacion ='si';
	var mensaje ='';
	$(".trtopeppal").each(function(){
		codigotope=$(this).attr("codigotope");
		//alert(codigotope);
		var haydetalle='no';
		var haydetalle2='no';


		$(".detalletope_"+codigotope).each(function(){
			var clasificacion = $(this).attr("clasificacion");

			// detalles
			console.log(codigotope+"--"+clasificacion+"----"+$("#valortopedetalle_"+codigotope+"_"+clasificacion).val())
			//--valortopedetalle_

			if($("#valortopedetalle_"+codigotope+"_"+clasificacion).val().replace(/,/g, "")>0 ||  $("#porcentajetopedetalle_"+codigotope+"_"+clasificacion).val()>0)
				{
					//alert($("#porcentajetopedetalle_"+codigotope+"_"+clasificacion).val());
					haydetalle ='si';
					var ccodetalle    = $("#selectccotopes_"+codigotope+"_"+clasificacion).val();
					var valordetalle  = $("#valortopedetalle_"+codigotope+"_"+clasificacion).val();
					var porcentajedetalle = $("#porcentajetopedetalle_"+codigotope+"_"+clasificacion).val();


					$("#tipo_empresa").addClass('campoObligatorio');

					if($("#diariotopedetalle_"+codigotope+"_"+clasificacion).is(":checked"))
					{
						var diario 	      = 'on';
					}
					else
					{
						var diario 	      = 'off';
					}
					$("#fechatopedetalle_"+codigotope+"_"+clasificacion).removeClass('campoObligatorio');
					if($("#fechatopedetalle_"+codigotope+"_"+clasificacion).val() =='')
					{
						$("#fechatopedetalle_"+codigotope+"_"+clasificacion).addClass('campoObligatorio');
						cumplevalidacion ='no';

					}
					$("#porcentajetopedetalle_"+codigotope+"_"+clasificacion).removeClass('campoObligatorio');
					if($("#porcentajetopedetalle_"+codigotope+"_"+clasificacion).val()=='')
					{
						$("#porcentajetopedetalle_"+codigotope+"_"+clasificacion).addClass('campoObligatorio');
						cumplevalidacion ='no';
					}

					// if( $("#porcentajetopedetalle_"+codigotope+"_"+clasificacion).val()*1 > 100 ){
						// mensaje =  "El valor ingresado debe ser mayor a 100 puesto que no se refiere a un porcentaje" ;
						// cumplevalidacion ='no';
					// }
					var fechatopedetalle= $("#fechatopedetalle_"+codigotope+"_"+clasificacion).val();

					insertar = insertar+"_"+historia+":"+ingreso+":"+codigotope+":"+clasificacion+":"+ccodetalle+":"+valordetalle+":"+porcentajedetalle+":"+diario+":"+fechatopedetalle; // construyo el detalle de los topes alert("grabo");
					// historia ingreso responsable tconcepto Topcla  cco valor  porcentaje diario saldo estado y fecha

				}


		});
		//---medios
		console.log("medios"+codigotope+"------"+$("#valortopeppal_"+codigotope).val())

		if(($("#valortopeppal_"+codigotope).val().replace(/,/g, "") > 0 || $("#porcentajetopeppal_"+codigotope).val() > 0) && ( haydetalle =='no') )
		{
			var clasificaciong = $(this).attr("clasificacion");
			var ccog		   = $("#selectccotopesppal_"+codigotope).val();
			var valorg		   = $("#valortopeppal_"+codigotope).val();
			var porcentajeg	   = $("#porcentajetopeppal_"+codigotope).val();

			if($("#diariotopeppal_"+codigotope).is(":checked"))
			{
				var diariog		   = 'on';
			}
			else
			{
				var diariog		   = 'off';
			}
			//alert("grabo un principal");
			$("#fechatopeppal_"+codigotope).removeClass('campoObligatorio');
			if($("#fechatopeppal_"+codigotope).val() =='')
			{
				$("#fechatopeppal_"+codigotope).addClass('campoObligatorio');
				cumplevalidacion ='no';

			}
			$("#porcentajetopeppal_"+codigotope).removeClass('campoObligatorio');
			if( $("#porcentajetopeppal_"+codigotope).val()=='')
			{
				$("#porcentajetopeppal_"+codigotope).addClass('campoObligatorio');


			}

			// if( $("#porcentajetopeppal_"+codigotope).val()*1 > 100 ){
					// mensaje =  "El valor ingresado debe ser mayor a 100 puesto que no se refiere a un porcentaje" ;
					// cumplevalidacion ='no';
			// }

			var fechag		   = $("#fechatopeppal_"+codigotope).val();
			insertar = insertar+"_"+historia+":"+ingreso+":"+codigotope+":"+clasificaciong+":"+ccog+":"+valorg+":"+porcentajeg+":"+diariog+":"+fechag; // construyo el detalle de los topes alert("grabo");

		}


	});



	if($("#spEstPac").text()=='ACTIVO')
	{
		var activo ='on';
	}
	else
		var activo = 'off';

	//------ general
	if(insertar=='')
	{
		console.log("general"+$("#valortopegeneral").val());

		if($("#valortopegeneral").val().replace(/,/g, "") > 0 || $("#porcentajegeneral").val() > 0)
		{
			// veo el tope general
			if($("#diariotopegeneral").is(":checked"))
				{
					var diariog		   = 'on';
				}
				else
				{
					var diariog		   = 'off';
				}

			$("#fechatopegeneral").removeClass('campoObligatorio');
			if($("#fechatopegeneral").val() =='')
			{
				$("#fechatopegeneral").addClass('campoObligatorio');
				cumplevalidacion ='no';

			}
			$("#porcentajegeneral").removeClass('campoObligatorio');
			console.log("poorcenteaje general----"+$("#porcentajegeneral").val())
			if($("#porcentajegeneral").val()=='')
			{
				$("#porcentajegeneral").addClass('campoObligatorio');
				cumplevalidacion ='no';
			}

			$("#valortopegeneral").removeClass('campoObligatorio');
			if ($("#valortopegeneral").val()=='')
			{
				$("#valortopegeneral").addClass('campoObligatorio');
				cumplevalidacion ='no';
			}


			insertar = insertar+"_"+historia+":"+ingreso+":*:*:"+$("#selectccogeneral").val()+":"+$("#valortopegeneral").val()+":"+$("#porcentajegeneral").val()+":"+diariog+":"+$("#fechatopegeneral").val(); // construyo el detalle de los topes alert("grabo");
		}
	}


	var esadmision ='si';
	if( $( "[name=radPreAdmi]:checked" ).val() == 'preadmision' || $("input[name='btRegistrarActualizar']").val().toUpperCase() == 'PREADMITIR' )
	{
		esadmision ='no';
	}




	if( $( "[name=radPreAdmi]:checked" ).val() == 'preadmision' || $("input[name='btRegistrarActualizar']").val().toUpperCase() == 'PREADMITIR' )
    {
		//alert("entro");
		if($("input[name='btRegistrarActualizar']").val()=='Preadmitir')
		{
				if(cumplevalidacion=='si')
				{
					$("#divtabletopes_"+codRespGlobal+"  input ").each(function(){

					 if($( this ).attr( 'type' ) == "checkbox")
					 {

						 if($(this).attr('checked'))
						 {
							 if( !objglobal[codRespGlobal]  )
										objglobal[codRespGlobal]  = {};

							 objglobal[ codRespGlobal ][ $(this).attr('id') ] = 'on';
						 }
					 }
					 else
					 {
						 if($( this ).attr( 'type' ) != "button")
						 {
							 if($(this).val() !='')
							 {
								if( !objglobal[codRespGlobal]  )
									objglobal[codRespGlobal]  = {};

								objglobal[ codRespGlobal ][ $(this).attr('id') ] = $(this).val();
								//alert($(this).attr('id')+"----"+$(this).val());
							 }
						 }
					 }
					});


					$("#divtabletopes_"+codRespGlobal+"  select ").each(function(){
						if( !objglobal[codRespGlobal]  )
							objglobal[codRespGlobal]  = {};

						objglobal[ codRespGlobal ][ $(this).attr('id') ] = $(this).val();
					});



					if($("#topeGrabar_"+codRespGlobal).length==0)
					{
						$("#Divresponsablestopes").append("<input class='grabarCuandoAdmite'  responsable='"+codRespGlobal+"' id='topeGrabar_"+codRespGlobal+"'  type='hidden' value='"+insertar+"'>");
						$("#Divresponsablestopes").append("<input id='htmlresponsable"+codRespGlobal+"' type='hidden' value='"+$("#divtabletopes_"+codRespGlobal).html()+"'>");
						$("#Divresponsablestopes").append("<input id='htmlresponsablevector"+codRespGlobal+"' type='hidden' >");

					}
					else
					{
						$("#topeGrabar_"+codRespGlobal).remove()
						$("#htmlresponsable"+codRespGlobal).remove();
						$("#htmlresponsablevector"+codRespGlobal).remove();
						$("#Divresponsablestopes").append("<input class='grabarCuandoAdmite'  responsable='"+codRespGlobal+"' id='topeGrabar_"+codRespGlobal+"'  type='hidden' value='"+insertar+"'>");
						$("#Divresponsablestopes").append("<input id='htmlresponsable"+codRespGlobal+"' type='hidden' value='"+$("#divtabletopes_"+codRespGlobal).html()+"'>");
						$("#Divresponsablestopes").append("<input id='htmlresponsablevector"+codRespGlobal+"' type='hidden' >");

					}
					DivTopes.dialog('destroy');
				}
				else
				{
					alert("faltan campos por llenar");
				}



		}
		else
		{
			if(cumplevalidacion=='si')
				{
					//alert("es preadmision 1");
					//alert($.trim($("#pac_doctxtNumDoc").val()));
					//alert($("#pac_tdoselTipoDoc").val());

					/****************************************************************
					 * Agosto 15 de 2013
					 *
					 * Si está activo preadmisión se guarda el dato como preadmisión
					 ****************************************************************/



					$.post("admision_erp.php",
					{
						accion      : "insertarTopes",
						consultaAjax: '',
						whistoria: historia,
						wingreso:  ingreso,
						responsable: codRespGlobal,
						wemp_pmla: $('#wemp_pmla').val(),
						insertar: insertar,
						activo: activo,
						esadmision: esadmision,
						documento: $.trim($("#pac_doctxtNumDoc").val()),
						tipodocumento: $("#pac_tdoselTipoDoc").val()

					}, function(data){
						//alert("grabacion exitosa");
						//alert(data);
						//alert(data.sql);
						DivTopes.dialog('destroy');
					});
				}
				else
				{

					alert("Faltan campos por llenar");

				}

		}





	}
	else
	{
			// historia vacia y es admision  ( osea que no hay una admision en la tabla 101)


			if(historia=='')
			{

				if(cumplevalidacion=='si')
				{


						$("#divtabletopes_"+codRespGlobal+"  input ").each(function(){

						 if($( this ).attr( 'type' ) == "checkbox")
						 {

							 if($(this).attr('checked'))
							 {
								 if( !objglobal[codRespGlobal]  )
											objglobal[codRespGlobal]  = {};

								 objglobal[ codRespGlobal ][ $(this).attr('id') ] = 'on';
							 }
						 }
						 else
						 {
							 if($( this ).attr( 'type' ) != "button")
							 {
								 if($(this).val() !='')
								 {
									if( !objglobal[codRespGlobal]  )
										objglobal[codRespGlobal]  = {};

									objglobal[ codRespGlobal ][ $(this).attr('id') ] = $(this).val();
									//alert($(this).attr('id')+"----"+$(this).val());
								 }
							 }
						 }
						});


						$("#divtabletopes_"+codRespGlobal+"  select ").each(function(){
							if( !objglobal[codRespGlobal]  )
								objglobal[codRespGlobal]  = {};

							objglobal[ codRespGlobal ][ $(this).attr('id') ] = $(this).val();
						});



						if($("#topeGrabar_"+codRespGlobal).length==0)
						{

							$("#Divresponsablestopes").append("<input class='grabarCuandoAdmite'  responsable='"+codRespGlobal+"' id='topeGrabar_"+codRespGlobal+"'  type='hidden' value='"+insertar+"'>");
							$("#Divresponsablestopes").append("<input id='htmlresponsable"+codRespGlobal+"' type='hidden' value='"+$("#divtabletopes_"+codRespGlobal).html()+"'>");
							$("#Divresponsablestopes").append("<input id='htmlresponsablevector"+codRespGlobal+"' type='hidden' >");

						}
						else
						{

							$("#topeGrabar_"+codRespGlobal).remove()
							$("#htmlresponsable"+codRespGlobal).remove();
							$("#htmlresponsablevector"+codRespGlobal).remove();
							$("#Divresponsablestopes").append("<input class='grabarCuandoAdmite'  responsable='"+codRespGlobal+"' id='topeGrabar_"+codRespGlobal+"'  type='hidden' value='"+insertar+"'>");
							$("#Divresponsablestopes").append("<input id='htmlresponsable"+codRespGlobal+"' type='hidden' value='"+$("#divtabletopes_"+codRespGlobal).html()+"'>");
							$("#Divresponsablestopes").append("<input id='htmlresponsablevector"+codRespGlobal+"' type='hidden' >");


						}
						DivTopes.dialog('destroy');
				}
				else
				{
					alert("Faltan campos por llenar");
				}






			}
			else
			{


				if(cumplevalidacion=='si')
				{
					//alert("entrooo por aquiiiii");

					/****************************************************************
					 * Agosto 15 de 2013
					 *
					 * Si está activo preadmisión se guarda el dato como preadmisión
					 ****************************************************************/



					$.post("admision_erp.php",
					{
						accion      : "insertarTopes",
						consultaAjax: '',
						whistoria: historia,
						wingreso:  ingreso,
						responsable: codRespGlobal,
						wemp_pmla: $('#wemp_pmla').val(),
						insertar: insertar,
						activo: activo,
						esadmision: esadmision,
						documento: $.trim($("#pac_doctxtNumDoc").val()),
						tipodocumento: $("#pac_tdoselTipoDoc").val()

					}, function(data){
						//alert("grabacion exitosa");
						DivTopes.dialog('destroy');
					});
				}
				else
				{

					alert("Faltan campos por llenar");

				}

			}

	}



	//alert("hola");
}
function recalculartopes2()
{
	var historia = ($( "#ing_histxtNumHis" ).val());
    var ingreso  = ($( "#ing_nintxtNumIng" ).val());

	if($("#spEstPac").text()=='ACTIVO')
	{
		var activo ='on';
	}
	else
		var activo = 'off';

	if(activo=='on')
	{




		$.post("admision_erp.php",
			{
				accion      : "recalculartopes2",
				consultaAjax: '',
				whistoria: historia,
				wingreso:  ingreso,
				responsable: $("#responsabletope").val(),
				wemp_pmla: $('#wemp_pmla').val(),
				activo: activo

			}, function(data){


				$("#div_recalculartopes").html("");
				$("#div_recalculartopes").html(data);
				//$("#div_recalculartopes").show(data);
				//alert(data);


			}).done(function(){
				$.blockUI(
				 {
					message: $("#div_contenedor"),
					css: { 	top: ($(window).height()*0.5) /5 + 'px',
					left: ($(window).width()*0.5) /5 + 'px',
					width: "50%",
					heightStyle: "content",
					textAlign: "left",
					cursor: ""
					}
				}
						 );
				var i =0 ;
				//alert(i);

				$(".tdrecalculartope").each(function(i){
					//alert($(".tdrecalculartope").length);
					i++;
					var idcargo 	= $(this).attr('idcargo');
					var responsable = $(this).attr('responsable');
					var tipoingreso	= $(this).attr('tipoingreso');
					var tipopaciente= $(this).attr('tipopaciente');
					var centrocostos= $(this).attr('centrocostos');
					var log			= $(this).attr('log');
					var permitir	= $(this).attr('pergrabarcargo');

					//alert(idcargo+'---'+responsable+'---'+tipoingreso+'---'+tipopaciente+'---'+centrocostos+'---'+log+'---'+permitir);

					// alert("entro");
					$.post("admision_erp.php",
						{
							consultaAjax:   '',
							wemp_pmla:      $('#wemp_pmla').val(),
							accion:         'regrabarCargo',
							idCargo:		idcargo,
							responsble:		responsable,
							tipoIngreso:	tipoingreso,
							tipoPaciente:	tipopaciente
						}, function(data){

							if(data.Regrabado)
							{
								//alert("idCargo:"+idcargo+" "+data.MsjGrabado+" "+data.MsjAnulado);
							}
							else
							{
								alert(" Error en el cargo "+idcargo+" no se pueden actualizar los topes");
							}

							if(i== $(".tdrecalculartope").length)
							{
								$.unblockUI();
							}

						}, 'json');


				});

			});
	}
	else
	{
		alert ("paciente inactivo no puedo re calcular topes");
	}


}


function mostrarDivTopes(codResp, obj)
{
	// recibe el id del campo que tiene el cod del responsable
	//alert($("#"+codResp).val());

	/*obj = jQuery(obj);
	var titulo = "";
	if( obj ){
		obj=obj.parent().parent(); //Rerencia al tr
		//Buscar dentro del tr, un input name=ing_cemtxtCodAse que es el nombre de la aseguradora
		titulo = obj.find("[name=ing_cemtxtCodAse]").val();

		$( "#div_cont_tabla_topes"+codResp ).find("#tabla_topes tr:first").find("td:first").text( "TOPES "+titulo );
		$( "#div_cont_tabla_topes"+codResp ).find("#tabla_topes tr:first").find("th:first").text( "TOPES "+titulo );
	}*/

	codRespGlobal = $( "#"+codResp ).val();
	idCodResp = codResp;
	//si ya se habia abierto antes el div de topes y habian digitado algo
	//if( $( "#div_cont_tabla_topes"+codResp ).length > 0 )
	//{

		/*$.blockUI(
			{
				message: $( "#div_cont_tabla_topes"+codResp ),
				css: { 	top: ($(window).height()*0.5) /5 + 'px',
				left: ($(window).width()*0.5) /5 + 'px',
				width: "80%",
				heightStyle: "content",
				textAlign: "left",
				cursor: ""
				}
			}
		);

		$("div[id^='div_cont_tabla_topes']").find("[name='top_rectxtValRec']").on({
			keyup: function(){
				$(this).val( $(this).val().replace(/[^0-9]/, "") );

				if( $(this).val().length > 3 ){
					$(this).val( $(this).val().substring(0, $(this).val().length -1 ) );
				}else if( $(this).val().length == 3 ){
					if( $(this).val()*1 > 100 ){
						$(this).val('100');
					}
				}
			}
		});

		tablaTopes

		$("div[id^='div_cont_tabla_topes']").find("[name='top_toptxtValTop']").on({
			blur: function(){
				$(this).val( $(this).val().replace(/[^0-9]/, "") );

				if( $(this).val()*1 <= 100 ){
					alert( "El valor ingresado debe ser mayor a 100 puesto que no se refiere a un porcentaje" )
					$(this).val( "" );
				}
			}
		});*/
	//}
	//else
	//{
		if ($("#"+codResp).val() != '')
		{
			var esadmision ='si';
			if( $( "[name=radPreAdmi]:checked" ).val() == 'preadmision' || $("input[name='btRegistrarActualizar']").val().toUpperCase() == 'PREADMITIR' )
			{
				esadmision ='no';
			}

			$.post("admision_erp.php",
			{
				consultaAjax:   '',
				accion:         'mostrarTopes',
				wbasedato:      $("#wbasedato").val(),
				id:				codResp,
				responsable:	codRespGlobal,
				wemp_pmla: $("#wemp_pmla").val(),
				historia : $( "#ing_histxtNumHis" ).val(),
				ingreso  : $( "#ing_nintxtNumIng" ).val(),
				documento: $.trim($("#pac_doctxtNumDoc").val()),
				tipodocumento: $("#pac_tdoselTipoDoc").val(),
				esadmision:    esadmision


			}
			,function(data)
			{
				//alert("nada");
				if (data.error == 1 && data.mensaje != '')
				{
					alert(data.mensaje);
				}
				else
				{

					if( $( "[name=radPreAdmi]:checked" ).val() == 'preadmision' || $("input[name='btRegistrarActualizar']").val().toUpperCase() == 'PREADMITIR' )
					{
						if($("input[name='btRegistrarActualizar']").val()=='Preadmitir')
						{

							if($("#htmlresponsable"+codRespGlobal).length==0)
								{
									//alert("abro nuevo");
									$("#div_topes").html("");
									$("#div_topes").append(data.html);
								}
								else
								{
									//alert("abro existente");
									$("#div_topes").html("");
									$("#div_topes").append("<div id='divtabletopes_"+codRespGlobal+"'>"+$("#htmlresponsable"+codRespGlobal).val()+"</div>");
									$("#divtabletopes_"+codRespGlobal+"  input ").each(function(){




										 if($( this ).attr( 'type' ) == "checkbox")
										 {
											 //alert($( this ).val( 'type' ))
											 if(objglobal[codRespGlobal][ $(this).attr('id') ])
											 {
												$(this).attr('checked', true);
												// $(this).val(objglobal[codRespGlobal][ $(this).attr('id') ]);
											 }
										 }
										 else
										 {

											if(objglobal[codRespGlobal][ $(this).attr('id') ])
											{
												 //alert("entro pr aqui");
												 $(this).val(objglobal[codRespGlobal][ $(this).attr('id') ]);
											}
										 }
										});

										$("#divtabletopes_"+codRespGlobal+"  select ").each(function(){

											if(objglobal[codRespGlobal][ $(this).attr('id') ])
											{
												$( this ).val( objglobal[codRespGlobal][ $(this).attr('id') ] );
											}

										});
								}
						}
						else
						{

							$("#div_topes").html("");
							$("#div_topes").append(data.html);


						}

					}
					else
					{
							if($( "#ing_histxtNumHis" ).val()==''  )
							{

								if($("#htmlresponsable"+codRespGlobal).length==0)
								{
									//alert("abro nuevo");
									$("#div_topes").html("");
									$("#div_topes").append(data.html);
								}
								else
								{
									//alert("abro existente");
									$("#div_topes").html("");
									$("#div_topes").append("<div id='divtabletopes_"+codRespGlobal+"'>"+$("#htmlresponsable"+codRespGlobal).val()+"</div>");
									$("#divtabletopes_"+codRespGlobal+"  input ").each(function(){




										 if($( this ).attr( 'type' ) == "checkbox")
										 {
											 //alert($( this ).val( 'type' ))
											 if(objglobal[codRespGlobal][ $(this).attr('id') ])
											 {
												$(this).attr('checked', true);
												// $(this).val(objglobal[codRespGlobal][ $(this).attr('id') ]);
											 }
										 }
										 else
										 {

											if(objglobal[codRespGlobal][ $(this).attr('id') ])
											{
												 //alert("entro pr aqui");
												 $(this).val(objglobal[codRespGlobal][ $(this).attr('id') ]);
											}
										 }
										});

										$("#divtabletopes_"+codRespGlobal+"  select ").each(function(){

											if(objglobal[codRespGlobal][ $(this).attr('id') ])
											{
												$( this ).val( objglobal[codRespGlobal][ $(this).attr('id') ] );
											}

										});
								}
							}
							else
							{

								$("#div_topes").html("");
								$("#div_topes").append(data.html);
							}
					}


					//buscarCcoTopes('tabla_topes');
					//buscarClasificacionConceptosFac('tabla_topes');

					//-----
					var w = $(window).width();
					var h = $(window).height();
					$('html, body').animate({scrollTop:0});
					//-----------------
					sleep(500).then(() => {
					$( '#div_topes').dialog({
								width: w,
								height: h,
								dialogClass: 'fixed-dialog',
								modal: true,
								title: '<img width="15" height="15" src="../../images/medical/root/info.png" /> &nbsp;&nbsp;INGRESO DE TOPES Y RECONOCIDOS',
								close: function( event, ui ) {
										//limpiarPantalla();
								},
								buttons:{
									"Guardar": function() {
										guardartopes(codRespGlobal , $(this));


									},
									"Salir sin guardar": function() {

										$(this).dialog("destroy");
									}
								 }
							});


						$(".datepickertopes").removeClass('hasDatepicker');
						$(".datepickertopes").datepicker({
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
							yearSuffix: ''
						});


						$(".tablaTopes").find(".valortope").on({
							keyup: function(){
								$(this).val( $(this).val().replace(/[^0-9]/, "") );
									num = $(this).val().replace(/\,/g,'');
									num = num.replace(/\./g,'');
									num = num.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1,');
									num = num.split('').reverse().join('').replace(/^[\,]/,'');
									$(this).val(num);
							}
						});

						$(".tablaTopes").find(".portope").on({
							keyup: function(){
								$(this).val( $(this).val().replace(/[^0-9]/, "") );
								if( $(this).val().length > 3 ){
									$(this).val( $(this).val().substring(0, $(this).val().length -1 ) );
								}
								else if( $(this).val().length == 3 ){
									if( $(this).val()*1 > 100 ){
										$(this).val('100');
									}
								}
							}
						});

								//---------------------
								$(".validaciontope").keyup(function() {
									validacionTopeGeneral();
								});
								$(".validaciontope").click(function() {
									if($(this).attr('Type') == 'checkbox')
									 validacionTopeGeneral();
								});

								//--------------------
								$(".validaciontope2").keyup(function() {
									validacionTopexConcepto ($(this));
								});
								$(".validaciontope2").click(function() {
									if($(this).attr('Type') == 'checkbox')
									 validacionTopexConcepto();
								});


								//-----------
								$(".validaciontope3").keyup(function() {
									validacionTopexClasificacion($(this));
								});
								$(".validaciontope3").click(function() {
									if($(this).attr('Type') == 'checkbox')
									 validacionTopexClasificacion();
								});


						//
					});

					$(".trtopeppal").each(function(){
						var tope = $(this).attr("codigotope");
						if($(".detalletope_"+tope).length ==0)
						{
							//alert("entro"+tope);
							$("#detallartope_"+tope).hide();
						}
					});


					/*$.blockUI(
						{
							message: $("#div_topes"),





							css: { 	top: ($(window).height()*0.5) /5 + 'px',
							left: ($(window).width()*0.5) /5 + 'px',
							width: "50%",
							heightStyle: "content",
							textAlign: "left",
							cursor: ""

							}
						}
					);*/

					/*$("#div_cont_tabla_topes"+codResp).find("[name='top_rectxtValRec']").on({
						keyup: function(){
							$(this).val( $(this).val().replace(/[^0-9]/, "") );

							if( $(this).val().length > 3 ){
								$(this).val( $(this).val().substring(0, $(this).val().length -1 ) );
							}else if( $(this).val().length == 3 ){
								if( $(this).val()*1 > 100 ){
									$(this).val('100');
								}
							}
						}
					});*/

					/*$("#div_cont_tabla_topes"+codResp).find("[name='top_toptxtValTop']").on({
						blur: function(){
							$(this).val( $(this).val().replace(/[^0-9]/, "") );

							if( $(this).val()*1 <= 100 ){
								alert( "El valor ingresado debe ser mayor a 100 puesto que no se refiere a un porcentaje" )
								$(this).val( "" );
							}
						}
					});*/

					//se llena el hidden de responsable para mandarlo con el array
					//$( "#div_cont_tabla_topes"+codResp ).find("[name=top_reshidTopRes]").val($("#"+codResp).val());
					// $("#div_topes").html(data);
					// buscarCcoTopes();
					// $("#div_contenedor_topes").html(data.html);

					//PONER EL TITULO DEL RESPONSABLE EN EL DIV FLOTANTE
					/*obj = jQuery(obj);
					 var titulo = "";
					 if( obj ){
						obj=obj.parent().parent(); //Rerencia al tr
						//Buscar dentro del tr, un input name=ing_cemtxtCodAse que es el nombre de la aseguradora
						titulo = obj.find("[name=ing_cemtxtCodAse]").val();

						$( "#div_cont_tabla_topes"+codResp ).find("#tabla_topes tr:first").find("td:first").text( "TOPES "+titulo );
						$( "#div_cont_tabla_topes"+codResp ).find("#tabla_topes tr:first").find("th:first").text( "TOPES "+titulo );
					 }*/


				}
			},
			"json"
			).done(function(){
				//--recorrer los inputs si tienen un dato realizar  validacion para bloquear los inputs donde no se puede grabar
				var validaciongeneral = false;
				$(".validaciontope").each(function (){
					if($(this).val() !='')
					{
						if($(this).attr('type') == 'checkbox')
						{
							if($(this).attr('checked'))
							{
								validaciongeneral = true;
								return false;
							}

						}
						else
						{
							validaciongeneral = true;
							return false;
						}

					}
				});


				if(validaciongeneral)
				{
					validacionTopeGeneral();
				}
				else
				{

					var validacionesTopexClasificacion = false;
					$(".validaciontope3").each(function (){
						if($(this).val() !='')
						{
							if($(this).attr('type') == 'checkbox')
							{
								if($(this).attr('checked'))
								{
									validacionesTopexClasificacion = true;
									return false;
								}

							}
							else
							{
								validacionesTopexClasificacion = true;
								return false;
							}

						}
					});

					if(validacionesTopexClasificacion)
					{

						$(".validaciontope3").each(function (){
							if( $(this).val()!='')
							{
								validacionTopexClasificacion ($(this));
							}
						});
					}
					else
					{

						$(".validaciontope2").each(function (){
							if( $(this).val()!='')
							{
								validacionTopexConcepto ($(this));
							}
						});
					}

					// $(".validaciontope3").each(function (){
						// if( $(this).val()!='')
						// {
							// validacionTopexClasificacion ($(this));
						// }
					// });

					// $(".validaciontope2").each(function (){
						// if( $(this).val()!='')
						// {
							// validacionTopexConcepto ($(this));
						// }
					// });





				}


				// elementoxclasificacion=  $(".validaciontope3").eq(0);
				// validacionTopexClasificacion (elementoxclasificacion);



			});
		}
		else
		{
			alert(" ** Debe ingresar el nombre de la Aseguradora");
		}
	//}
}

//--- Valida los inputs del tope principal (tipo concepto '*-todas' ,  clasificacion '*-Todas' )
function validacionTopeGeneral()
{

	var quito = 'no';
	$(".validaciontope").each(function (){
		//alert($(this).val());
		if($(this).val()=='')
		{


		}
		else
		{

			if($( this ).attr( 'type' ) == "checkbox")
			{
				if($(this).attr('checked'))
				{
				   //alert($(this).val());
				   // desabilito los otros detalles
				   $(".tdminimo").addClass('disabler');
				   $(".tdmedio").addClass('disabler');
				   $(".validaciontope2").attr('disabled','disabled');
				   $(".validaciontope3").attr('disabled','disabled');

				   quito='si';
				   return;

				}

			}
			else
			{
				 $(".tdminimo").addClass('disabler');
				 $(".tdmedio").addClass('disabler');
				 $(".validaciontope2").attr('disabled','disabled');
				 $(".validaciontope3").attr('disabled','disabled');

				 quito='si';
				 return;
			}
		}
	})
	//alert(quito)
	if(quito =='no')
	{
		 $(".tdminimo").removeClass('disabler');
		 $(".tdmedio").removeClass('disabler');
		 $(".validaciontope2").removeAttr('disabled');
		 $(".validaciontope3").removeAttr('disabled');

	}

}
//--- Valida los inputs del tope(tipo concepto 'xxxxxx-especifica' ,  clasificacion '*-Todas' )
function validacionTopexConcepto (elementoinput)
{


	var codigo = elementoinput.attr('attrvalor');
	var quito= 'no';
	$(".validaciontope2_"+codigo).each(function (){
		if($(this).val()=='')
		{


		}
		else
		{
			if($( this ).attr( 'type' ) == "checkbox")
			{
			   if($(this).attr('checked'))
			   {
				   $(".tdminimo_"+codigo).addClass('disabler');
				   $(".tdgeneral").addClass('disabler');
				   $(".validaciontope3_"+codigo).attr('disabled','disabled');
				   $(".validaciontope").attr('disabled','disabled');
				   //validaciontope3_
				   quito='si';
				   return;
			   }

			}
			else
			{
				$(".tdminimo_"+codigo).addClass('disabler');
				$(".tdgeneral").addClass('disabler');
				$(".validaciontope3_"+codigo).attr('disabled','disabled');
				$(".validaciontope").attr('disabled','disabled');

				quito='si';
				return;

			}
		}

	});

	if(quito =='no')
	{

		 $(".tdminimo_"+codigo).removeClass('disabler');
		 $(".validaciontope3_"+codigo).removeAttr('disabled');
		 // para ver si no hay ningun campo lleno en toda la tabla
		 var quitodos ='no';
		 $(".validaciontope2").each(function(){
			if($(this).val()=='')
			{


			}
			else
			{
				if($( this ).attr( 'type' ) == "checkbox")
				{
				   if($(this).attr('checked'))
				   {
					   quitodos = 'si' ;
				   }
				}
				else
				{
					quitodos = 'si' ;
				}
			}

		 });
		 if(quitodos=='no')
		 {
			$(".tdgeneral").removeClass('disabler');
			$(".validaciontope").removeAttr('disabled');
		 }


	}



}

//--- Valida los inputs del tope (tipo concepto 'xxxx' ,  clasificacion 'xxxxx' )
function validacionTopexClasificacion (elementoxclasificacion)
{
	var codigo = elementoxclasificacion.attr('attrvalor');
	var quito = 'no';
	$(".validaciontope3_"+codigo).each(function (){
		//alert($(this).val());
		if($(this).val()=='')
		{


		}
		else
		{

			if($( this ).attr( 'type' ) == "checkbox")
			{
				if($(this).attr('checked'))
				{
				   //alert($(this).val());
				   // desabilito los otros detalles
				   $(".tdgeneral").addClass('disabler');
				   $(".tdmedio_"+codigo).addClass('disabler');
				   $(".validaciontope2_"+codigo).attr('disabled','disabled');
				   $(".validaciontope").attr('disabled','disabled');

				   quito='si';
				   return;

				}

			}
			else
			{
				 $(".tdgeneral").addClass('disabler');
				 $(".tdmedio_"+codigo).addClass('disabler');
				 $(".validaciontope2_"+codigo).attr('disabled','disabled');
				 $(".validaciontope").attr('disabled','disabled');

				 quito='si';
				 return;
			}
		}
	})
	//alert(quito)
	if(quito =='no')
	{




		 $(".tdmedio_"+codigo).removeClass('disabler');
		 $(".validaciontope2_"+codigo).removeAttr('disabled');

		 var quitodos ='no';
		 $(".validaciontope3").each(function(){
			if($(this).val()=='')
			{


			}
			else
			{
				if($( this ).attr( 'type' ) == "checkbox")
				{
				   if($(this).attr('checked'))
				   {
					   quitodos = 'si' ;
				   }
				}
				else
				{
					quitodos = 'si' ;
				}
			}

		 });

		 if(quitodos=='no')
		 {
			$(".tdgeneral").removeClass('disabler');
			$(".validaciontope").removeAttr('disabled');
		 }
		 /*$(".tdgeneral").removeClass('disabler');
		 $(".validaciontope").removeAttr('disabled');*/

	}



}


/*function mostrarDivTopes(codResp, obj)
{
	// recibe el id del campo que tiene el cod del responsable
	// alert($("#"+codResp).val());

	obj = jQuery(obj);
	var titulo = "";
	if( obj ){
		obj=obj.parent().parent(); //Rerencia al tr
		//Buscar dentro del tr, un input name=ing_cemtxtCodAse que es el nombre de la aseguradora
		titulo = obj.find("[name=ing_cemtxtCodAse]").val();

		$( "#div_cont_tabla_topes"+codResp ).find("#tabla_topes tr:first").find("td:first").text( "TOPES "+titulo );
		$( "#div_cont_tabla_topes"+codResp ).find("#tabla_topes tr:first").find("th:first").text( "TOPES "+titulo );
	}

	codRespGlobal = $( "#"+codResp ).val();
	idCodResp = codResp;
	//si ya se habia abierto antes el div de topes y habian digitado algo
	if( $( "#div_cont_tabla_topes"+codResp ).length > 0 )
	{

		$.blockUI(
			{
				message: $( "#div_cont_tabla_topes"+codResp ),
				css: { 	top: ($(window).height()*0.5) /5 + 'px',
				left: ($(window).width()*0.5) /5 + 'px',
				width: "50%",
				heightStyle: "content",
				textAlign: "left",
				cursor: ""
				}
			}
		);

		$("div[id^='div_cont_tabla_topes']").find("[name='top_rectxtValRec']").on({
			keyup: function(){
				$(this).val( $(this).val().replace(/[^0-9]/, "") );

				if( $(this).val().length > 3 ){
					$(this).val( $(this).val().substring(0, $(this).val().length -1 ) );
				}else if( $(this).val().length == 3 ){
					if( $(this).val()*1 > 100 ){
						$(this).val('100');
					}
				}
			}
		});

		$("div[id^='div_cont_tabla_topes']").find("[name='top_toptxtValTop']").on({
			blur: function(){
				$(this).val( $(this).val().replace(/[^0-9]/, "") );

				if( $(this).val()*1 <= 100 ){
					alert( "El valor ingresado debe ser mayor a 100 puesto que no se refiere a un porcentaje" )
					$(this).val( "" );
				}
			}
		});
	}
	else
	{
		if ($("#"+codResp).val() != '')
		{
			$.post("admision_erp.php",
			{
				consultaAjax:   '',
				accion:         'mostrarTopes',
				wbasedato:      $("#wbasedato").val(),
				id:				codResp,
				wemp_pmla: $("#wemp_pmla").val()


			}
			,function(data)
			{
				if (data.error == 1 && data.mensaje != '')
				{
					alerta(data.mensaje);
				}
				else
				{
					$("#div_topes").append(data.html);
					buscarCcoTopes('tabla_topes');
					buscarClasificacionConceptosFac('tabla_topes');
					$.blockUI(
						{
							message: $("#div_topes"),
							css: { 	top: ($(window).height()*0.5) /5 + 'px',
							left: ($(window).width()*0.5) /5 + 'px',
							width: "50%",
							heightStyle: "content",
							textAlign: "left",
							cursor: ""
							}
						}
					);

					$("#div_cont_tabla_topes"+codResp).find("[name='top_rectxtValRec']").on({
						keyup: function(){
							$(this).val( $(this).val().replace(/[^0-9]/, "") );

							if( $(this).val().length > 3 ){
								$(this).val( $(this).val().substring(0, $(this).val().length -1 ) );
							}else if( $(this).val().length == 3 ){
								if( $(this).val()*1 > 100 ){
									$(this).val('100');
								}
							}
						}
					});

					$("#div_cont_tabla_topes"+codResp).find("[name='top_toptxtValTop']").on({
						blur: function(){
							$(this).val( $(this).val().replace(/[^0-9]/, "") );

							if( $(this).val()*1 <= 100 ){
								alert( "El valor ingresado debe ser mayor a 100 puesto que no se refiere a un porcentaje" )
								$(this).val( "" );
							}
						}
					});

					//se llena el hidden de responsable para mandarlo con el array
					$( "#div_cont_tabla_topes"+codResp ).find("[name=top_reshidTopRes]").val($("#"+codResp).val());
					// $("#div_topes").html(data);
					// buscarCcoTopes();
					// $("#div_contenedor_topes").html(data.html);

					//PONER EL TITULO DEL RESPONSABLE EN EL DIV FLOTANTE
					obj = jQuery(obj);
					 var titulo = "";
					 if( obj ){
						obj=obj.parent().parent(); //Rerencia al tr
						//Buscar dentro del tr, un input name=ing_cemtxtCodAse que es el nombre de la aseguradora
						titulo = obj.find("[name=ing_cemtxtCodAse]").val();

						$( "#div_cont_tabla_topes"+codResp ).find("#tabla_topes tr:first").find("td:first").text( "TOPES "+titulo );
						$( "#div_cont_tabla_topes"+codResp ).find("#tabla_topes tr:first").find("th:first").text( "TOPES "+titulo );
					 }


				}
			},
			"json"
			);
		}
		else
		{
			alert(" ** Debe ingresar el nombre de la Aseguradora");
		}
	}
}*/


/*
function mostrarDivTopes1(codResp) //para el mostrar
{ // recibe el id del campo que tiene el cod del responsable
	// alert(codResp);
	codRespGlobal = $( "#"+codResp ).val();
	idCodResp = codResp;

		if ($("#"+codResp).val() != '')
		{
			$.ajax(
			{
				url:"admision_erp.php",
				context: document.body,
				type: "POST",
				data:
				{
					consultaAjax:   '',
					accion:         'mostrarTopes',
					wbasedato:      $("#wbasedato").val(),
					id:				codResp,
					wemp_pmla:   $("#wemp_pmla").val()
				},
				async: false,
				dataType: "json",
				success:function(data){

					if (data.error == 1 && data.mensaje != '')
					{
						alerta(data.mensaje);
					}
					else
					{
						var auxDiv = document.createElement( "div" );
						$( auxDiv ).html( data.html );

						// $("#div_contenedor_topes").html($("#div_contenedor_topes").html()+data.html);
						$("#div_contenedor_topes").append( $( auxDiv ).first() );
						buscarCcoTopes('tabla_topes');
						buscarClasificacionConceptosFac('tabla_topes');

						//se llena el hidden de responsable para mandarlo con el array
						$( "#div_cont_tabla_topes"+codResp ).find("[name=top_reshidTopRes]").val($("#"+codResp).val());
						// $("#div_topes").html(data);
						// buscarCcoTopes();
						// $("#div_contenedor_topes").html(data.html);
					}
				}
			});
		}
}
*/
function addFila(tabla_referencia,wbasedato,wemp_pmla)
{
	if( tabla_referencia == "tabla_topes" )
	// alert("entro"+tabla_referencia);
	var ubicacion="";

	if (tabla_referencia == "tabla_eps")
	{
		accion='adicionar_fila';
		// para saber si la tabla tiene filas o no
		trs = $("#"+tabla_referencia).find('tr[id$=tr_'+tabla_referencia+']').length;
	}
	else
	{
		accion='adicionar_fila_tope';
		// para saber si la tabla tiene filas o no, toca decirle a que div-contatenado con la asiguradora pertenece
		trs = $("#"+tabla_referencia,$("#div_cont_tabla_topes"+idCodResp)).find('tr[id$=tr_'+tabla_referencia+']').length;
	}
	// para saber si la tabla tiene filas o no
	// trs = $("#"+tabla_referencia).find('tr[id$=tr_'+tabla_referencia+']').length;
	var value_id = 0;

	//busca consecutivo mayor
	if(trs > 0)
	{
		id_mayor = 0;
		// buscar los tr que terminen en el mismo nombre de su tabla contenedora, recorrerlos y recuperar el valor mayor
		if (tabla_referencia == "tabla_eps")
		{
			ubicacion = "#"+tabla_referencia;
		}
		else
		{
			// ubicacion = "#"+tabla_referencia,$("#div_cont_tabla_topes"+idCodResp);
			ubicacion = "#div_cont_tabla_topes"+idCodResp+" #"+tabla_referencia;
		}
		$(ubicacion).find('tr[id$=tr_'+tabla_referencia+']').each(function() {
				id_ = $(this).attr('id');
				id_splt = id_.split('_');
				id_this = (id_splt[0])*1;
				if(id_this >= id_mayor)
				{
					id_mayor = id_this;
				}
			});
		id_mayor++;
		value_id = id_mayor+'_tr_'+tabla_referencia;

	}
	else
	{ value_id = '1_tr_'+tabla_referencia; }

	$.ajax(
		{
			url: "admision_erp.php",
			context: document.body,
			type: "POST",
			data: {
				accion      :accion,
				consultaAjax:'',
				id_fila     :value_id,
				tabla_referencia : tabla_referencia,
				wbasedato   : wbasedato,
				wemp_pmla   :wemp_pmla

		},
		async: false,
		dataType: "json",
		success: function(data){
			if(data.error == 1)
				{
					if (data.mensaje != '')
					{
						alerta(data.mensaje);
					}
				}
				else
				{	 // alert(data.html);
					if (tabla_referencia == "tabla_eps")
					{
						$("#"+tabla_referencia+" > tbody").append(data.html);
						buscarAseguradoras('tabla_eps');

						marcarAqua( "#"+value_id, 'msgError', 'campoRequerido' );
						resetAqua( $("#"+value_id) );
						reOrdenarResponsables();
						$("#tabla_eps > tbody").sortable( "refresh" );
						//console.log("adicionando fila")
						$("#tabla_eps > tbody").find("input[type='radio'][name='res_comtxtNumcon']").eq(0).attr("checked", false);
						$("#tabla_eps > tbody").find("input[type='radio'][name='res_comtxtNumcon']").eq(0).parent().hide();
					}
					else
					{
						// alert("div_cont_tabla_topes"+idCodResp);
						$("#"+tabla_referencia+" > tbody", $("#div_cont_tabla_topes"+idCodResp)).append(data.html);
						buscarClasificacionConceptosFac('tabla_topes');
						buscarCcoTopes('tabla_topes');
						//se llena el hidden de responsable para mandarlo con el array
						// $("[name=top_reshidTopRes]", $( "#"+value_id ) ).val($("#"+codResp).val());

						$("[name=top_reshidTopRes]", $( "#"+value_id, $("#div_cont_tabla_topes"+idCodResp) ) ).val( codRespGlobal );
					}

				}
			}
		});
}

function removerFila(id_fila,wbasedato,tabla_referencia)
{
	var wemp_pmla = $("#wemp_pmla").val();
	var id_eliminar = $( "#"+id_fila+"_bd" ).val();
	// alert(id_eliminar+" entro");
	/*if (id_fila == "1_tr_"+tabla_referencia && tabla_referencia == "tabla_eps")
	{
		alert("No se puede eliminar, debe existir al menos un responsable");
	}
	else
	{*/
	if ( tabla_referencia == "tabla_eps" && $("tr[id$=tr_tabla_eps]").length == 1){
		alert(" ** Debe existir al menos un responsable");
		return;
	}


		acc_confirm = 'Confirma que desea eliminar?';
		if(confirm(acc_confirm))
		{
			if(id_eliminar != '')
			{
				$.post("admision_erp.php",
					{
						accion      : 'eliminar_planes',
						consultaAjax: '',
						id_eliminar : id_eliminar,
						wbasedato 	: wbasedato
					},
					function(data){
						if(data.error == 1)
						{
							if (data.mensaje != '')
							{
								alerta(data.mensaje);
							}
						}
						else
						{
							if (data.mensaje != '')
							{
									alerta(data.mensaje);
									$("#"+id_fila).empty();
									$("#"+id_fila).remove();

							}
						}
					},
					"json"
				);
			}
			else
			{
				if( tabla_referencia == 'tabla_topes' ){
					$("#div_cont_tabla_topes"+idCodResp).find("#"+id_fila).remove();
				}else{
					$("#"+tabla_referencia).find("#"+id_fila).remove();
				}

				if( tabla_referencia == "tabla_eps" ){
					reOrdenarResponsables(); //Para ordenar R1,R2,R3
				}
			}
		}
	//}

	if(id_fila == "1_tr_"+tabla_referencia && tabla_referencia == "tabla_topes")
	{
		addFila(tabla_referencia,wbasedato,wemp_pmla)
	}
}

function buscarClasificacionConceptosFac(tabla_referencia)
{


	// var tr =$(this).parent().prev().parent();
	// var valor = tr.$("td:first");

	// var tr = this.parentNode.parentNode;
	// var valor = tr.$( "td:eq( 0 )" )

	var wbasedato= $("#wbasedato").val();
	var wemp_pmla= $("#wemp_pmla").val();

	if (tabla_referencia != "")
	{
	// para saber si la tabla tiene filas o no
				trs = $("#"+tabla_referencia,$("#div_cont_tabla_topes"+idCodResp)).find('tr[id$=tr_'+tabla_referencia+']').length;
				var value_id = 0;

				//busca consecutivo mayor
				if(trs > 0)
				{
					id_mayor = 0;
					// buscar los tr que terminen en el mismo nombre de su tabla contenedora, recorrerlos y recuperar el valor mayor
					$("#"+tabla_referencia,$("#div_cont_tabla_topes"+idCodResp)).find('tr[id$=tr_'+tabla_referencia+']').each(function() {
						id_ = $(this).attr('id');
						id_splt = id_.split('_');
						id_this = (id_splt[0])*1;
						if(id_this >= id_mayor)
						{
							id_mayor = id_this;
						}
					});
					// id_mayor++;
					value_id = id_mayor+'_tr_'+tabla_referencia;

				}
				else
				{ value_id = '1_tr_'+tabla_referencia; }

		codEsp="#top_clatxtClaTop"+value_id;
	}
	//Asigno autocompletar para la busqueda de paises
	$( "[id^=top_clatxtClaTop]",$("#div_cont_tabla_topes"+idCodResp) ).autocomplete("admision_erp.php?consultaAjax=&accion=consultarClasificacinConceptos&tipo=02&wbasedato="+wbasedato+"&wemp_pmla="+wemp_pmla,
	{
		extraParams: {
			tipo: function( campo ){
				//para buscar el valor del select de la fila
				var tr = conFocus.parentNode.parentNode;
				// var tdd= $( "#"+ $( conFocus ).prop("tagName"));
				var valor = $( "select", tr ).val();
				return valor;
			}
		},
		delay:300,
		max: 100,
		scroll: false,
		scrollHeight: 500,
		matchSubset: false,
		matchContains: true,
		width:250,
		autoFill:false,
		minChars: 1,
		cacheLength: 0,
		json:"json",
		formatItem: function(data, i, n, value) {

			//convierto el string en json
			eval( "var datos = "+data );

			return datos[0].usu;	//Eso es lo que se muestra al usuario
		},
		formatResult: function(data, value){
			//convierto el string en json
			eval( "var datos = "+data );

			return datos[0].valor.des;
		}
	}).result(
		function(event, item ){
			// //La respuesta es un json
			// //convierto el string en formato json
			eval( "var datos = "+item );

			//Guardo el ultimo valor que selecciona el usuario
			//Esto en una propiedad inventada
			this.value = datos[0].valor.des;
			this._lastValue = datos[0].valor.des;
			this._lastCodigo = datos[0].valor.cod;

			$( this ).removeClass( "campoRequerido" );

			$( "input[type=hidden]", this.parentNode ).val( datos[0].valor.cod );
		}
	).focus(function(){
		conFocus = this;
	}).on({

		change: function(){
			var cmp = this;

			if( cmp._lastValue && cmp._lastCodigo ){
				setTimeout( function(){
					//Pregunto si la pareja es diferente
					if( ( ( cmp._lastValue && cmp._lastValue != cmp.value ) || ( !cmp._lastValue && cmp.value != $( cmp ).attr( cmp.aqAttr ) ) )
						|| ( cmp._lastCodigo && cmp._lastCodigo != $( "input[type=hidden]", cmp.parentNode ).val() )
					)
					{
						alerta( " Digite una Clasificación válida" )
						$( "input[type=hidden]", cmp.parentNode ).val('');
						cmp.value = '';
						cmp.focus();
						// cmp.blur();
					}
				}, 500 );
			}else{
				alerta( " Digite una Clasificación válida" )
				$( "input[type=hidden]", cmp.parentNode ).val('');
				cmp.value = '';
			}


			// if( this._lastValue ){
				// this.value = this._lastValue;
			// }
			// else{
				// this.value = "";
			// }
		}
	})
	;
}

function salir(div, noesblockui)
{
	if( noesblockui == undefined )
		noesblockui = false;
	// //div que contiene la tabla de topes ya existia, miro que tiene
	// if( $( "#div_cont_tabla_topes"+idCodResp ).length > 0)
	// {
		// var infoDivTablaTopes = $( "#div_cont_tabla_topes"+idCodResp ).find($("#"+div).html());
	// }

	var alMenosUno = false;
	if( $("#"+div).find(":input").length > 0 )
	{
		$("#"+div).find(":input:not(:hidden,:button)").each(function()
		{
			if( $(this).is(':checkbox') )
			{
				if( $(this).is(':checked') )
				{
					alMenosUno = true;
					return false;
				}
			}
			else if( $(this).prop("tagName") == "SELECT" )
			{
				if( $(this).val() != '')
				{
					alMenosUno = true;
					return false;
				}
			}
			else
			{
				var valorx  = $(this).val();
				if( isEmpty(valorx) == true )
				{
					valorx  = $(this).text();
				}
				if( isEmpty(valorx) == false )
				{
					alMenosUno = true;
					return false;
				}
			}
		});
	}

	if( alMenosUno == false )
	{
		// alert("no hay nada!");
		//---$.unblockUI();
	}
	else
	{
		if( confirm( 'La información nueva ingresada no se tomará en cuenta' ) )
		{
			// var nuevaInfoDivTablaTopes =$( "#div_cont_tabla_topes"+idCodResp ).find( $("#"+div).html() );
			// var nuevaInfoDivTablaTopes = $("#"+div).html();
			var nuevaInfoDivTablaTopes = cearUrlPorCamposJson( $("#"+div)[0], 'id' );
			// if ($( "#div_cont_tabla_topes"+idCodResp )[0].anteriorHTML != nuevaInfoDivTablaTopes)
			if ($("#"+div)[0].anteriorJSON && $("#"+div)[0].anteriorJSON != nuevaInfoDivTablaTopes)
			{
				//console.log("TOPES DIFERENTES A:"+$("#"+div)[0].anteriorJSON);
				//console.log("B: "+nuevaInfoDivTablaTopes);
				// $( "#div_cont_tabla_topes"+idCodResp ).html($("#"+div).html());
				// $( "#div_cont_tabla_topes"+idCodResp ).html($( "#div_cont_tabla_topes"+idCodResp )[0].anteriorHTML);
				$("#"+div)[0].innerHTML = $("#"+div)[0].anteriorHTML;
				setDatos( $("#"+div)[0].anteriorJSON, $("#"+div), 'id');
			}
			else{
				if( div.match(/div_cont_tabla_topes/g) ){
					//console.log("A TOPES NO SE LE QUITA NADA");
				}else{
					//console.log("SE LE QUITA TODO");
					$( "input:not([type=button],[name=top_reshidTopRes]),select", $("#"+div) ).val('');
				}
			}
			//---$.unblockUI();
		}
	}
	// // se borran los trs de la tabla_eps
	// $("#tabla_eps").find("tr[id$='_tr_tabla_eps']").remove();
	// // se llama a addFila para que muestre la primera fila
	// addFila('tabla_eps',wbasedato,wemp_pmla);
	//if( noesblockui == false )
	$.unblockUI();
	//else
	//	$("#"+div).dialog( "close" );
	/*Guardar los topes por responsable temporal*/
	// objJson.topes = {};
	// $( "tr[id$=_tr_tabla_topes]" ).each(function( index )
	// {
		// objJson.topes[ index ] = cearUrlPorCamposJson(  this , 'name' );
	// });
	// var contenedor = objJson;
	// if( contenedor.lastInfo && contenedor.lastInfo != '' ){
		// if( confirm( 'Desea cerrar el formulario\nLa información nueva ingresada no se tomará en cuenta' ) ){
			// $.unblockUI();
			// setDatos( contenedor.lastInfo, $( "#tabla_topes" ) );
		// }
	// }
}

function isEmpty(obj) {
	if (typeof obj == 'undefined' || obj === null || obj === '') return true;
	if (typeof obj == 'number' && isNaN(obj)) return true;
	if (obj instanceof Date && isNaN(Number(obj))) return true;
return false;
}

function guardarTopePorResp()
{
	// se le manda al div_contenedor_topes lo que hay en div_topes y dentro de eso el div que empieza en div_cont_tabla_topes
	try{
		// div que contiene la tabla de topes ya existia, miro que tiene
		if( $( "#div_cont_tabla_topes"+idCodResp ).length > 0)
		{
			//Guardo la informacion en el mismo div
			// var auxDiv = $( "[id^=div_cont_tabla_topes]", $( "#div_topes" ) );
			var auxDiv = $( "#div_cont_tabla_topes"+idCodResp );
			auxDiv[0].anteriorHTML = auxDiv[0].innerHTML;
			auxDiv[0].anteriorJSON = cearUrlPorCamposJson( auxDiv[0], 'id' );
		}

		$( "#div_contenedor_topes" )[0].appendChild( $( "[id^=div_cont_tabla_topes]", $( "#div_topes" ) )[0] )


	}
	catch(e){}


	$.unblockUI();

}

function consultarClientesEspeciales()
{

	var wbasedato = $("#wbasedato").val();
	var cedula = $.trim($("#pac_doctxtNumDoc").val());
	var tipoDoc = $("#pac_tdoselTipoDoc").val();

	if( cedula == "" || tipoDoc == "" )
		return;

	$.post("admision_erp.php",
	{
		wbasedato: wbasedato,
		consultaAjax:   '',
		accion: 'consultaClientes',
		cedula: cedula,
		tipoDoc: tipoDoc

	},function(data){
		if (data.error == 1)
		{
			if (data.mensaje != '')
			{
				alerta(data.mensaje);
			}
		}
		else
		{
			if (data.mensaje != '')
			{
				$("#div_mensaje_PerEspeciales").css("display", "");
				$("#div_mensaje_PerEspeciales").html(data.mensaje)  // update Ok.
				$("#div_mensaje_PerEspeciales").parent().show('blind', {}, 500);
				$("#div_mensaje_PerEspeciales").effect("pulsate", {}, 10000);
			}
		}
	},
	"json"

	);
}

function consultarSiActivo()
{

	var wbasedato = $("#wbasedato").val();
	var cedula = $.trim($("#pac_doctxtNumDoc").val());
	var tipoDoc = $("#pac_tdoselTipoDoc").val();

	if( tipoDoc == "" || cedula == "" )
		return;

	$.post("admision_erp.php",
	{
		wbasedato: wbasedato,
		consultaAjax:   '',
		accion: 'consultarSiActivo',
		wemp_pmla: $("#wemp_pmla").val(),
		cedula: cedula,
		tipoDoc: tipoDoc

	},function(data){
		if (data.error == 1){
			if (data.mensaje != '')
				alert(data.mensaje);
			$("#pac_doctxtNumDoc").val("");
			resetAqua($("#pac_doctxtNumDoc").parent());
		}
		else{
			if (data.mensaje != ''){
				alerta(data.mensaje);
			}
			$("#ing_histxtNumHis").val( data.his );
			$("#ing_nintxtNumIng").val( data.ing );

			var estPac = data.estado;
			if (estPac == 'off'){
				estPac= "INACTIVO";
				$("#spEstPac").addClass("estadoInactivo");
			}
			else{
				estPac = "ACTIVO";
				$("#spEstPac").addClass("estadoActivo");
			}
			$("#spEstPac").html( estPac); //se envia Estado del paciente
		}
	},
	"json"
	);
}

function consultarSiRechazado()
{

	var wbasedato    = $("#wbasedato").val();
	var cedula       = $.trim($("#pac_doctxtNumDoc").val());
	var tipoDoc      = $("#pac_tdoselTipoDoc").val();
	var centroCostos = $("#ing_seisel_serv_ing").val();

	if( tipoDoc == "" || cedula == "" )
		return;

	$.post("admision_erp.php",
	{
		wbasedato: wbasedato,
		consultaAjax:   '',
		accion:         'consultarSiRechazado',
		wemp_pmla:      $("#wemp_pmla").val(),
		cedula:         cedula,
		tipoDoc:        tipoDoc,
		wcco:           centroCostos

	},function(data){
		if (data.error == 1){
			if (data.mensaje != '')
				alerta(data.mensaje);
			$("#pac_doctxtNumDoc").val("");
			resetear();
			resetAqua($("#pac_doctxtNumDoc").parent());
		}
		else{
			if (data.mensaje != ''){
				alerta(data.mensaje);
			}
			$("#ing_histxtNumHis").val( data.his );
			$("#ing_nintxtNumIng").val( data.ing );
		}
	},
	"json"
	);
}

function valNumero(id)
{
	$( "#"+id ).on({
		keypress: function(e){
			var key = e.keyCode || e.which;

			if( key != 9 && key != 8 ){
				if( String.fromCharCode(key).search( /[0-9]/g ) == -1 ){
					e.preventDefault();
				}
			}
		}
	});
}

function valPorcentaje( obj )
{
	var valor = $( obj ).val();
	console.log( $( obj ));
	if( $.trim(valor) == "" ){
		$( obj ).val("100");
	}
	if (valor < 0 || valor > 100)
	{
		alert(" ** Debe ingresar un valor de entre 0 y 100");
		$( obj ).val("");
	}
}

function valRepetidosTopes(id_tr)
{

	id_tr_splt = id_tr.split('.');
	id_tr = (id_tr_splt[1]);

	//se pone el fondo de la fila en el color normal
	$("#"+id_tr,$("#div_cont_tabla_topes"+idCodResp)).css("background-color","");

	var campo1=$("#top_tcoselTipCon"+id_tr,$("#div_cont_tabla_topes"+idCodResp)).val();
	var campo2=$("#top_clahidClaTop"+id_tr,$("#div_cont_tabla_topes"+idCodResp)).val();
	var campo3=$("#top_ccohidCcoTop"+id_tr,$("#div_cont_tabla_topes"+idCodResp)).val();

	if (campo2 == '*') {campo2 = 'k'}
	if (campo3 == '*') {campo3 = 'k'}

	// alert(campo1+campo2+campo3);
	var buscar = campo1+campo2+campo3;

	$("#hdd_"+id_tr,$("#div_cont_tabla_topes"+idCodResp)).val(buscar);

	if ($("input:hidden[id^=hdd_][value = '"+buscar+"']",$("#div_cont_tabla_topes"+idCodResp)).length > 1)
	{
		alert(" ** Se encuentran valores repetidos de Tipo de Concepto, Clasificacion y Centro de Costo");
		$("#"+id_tr,$("#div_cont_tabla_topes"+idCodResp)).css("background-color","yellow");
		$("#btnGuardarTopResp",$("#div_cont_tabla_topes"+idCodResp)).attr("disabled", "disabled");
	}
	else
	{
		$("#btnGuardarTopResp",$("#div_cont_tabla_topes"+idCodResp)).removeAttr("disabled");
	}
}

function agregarCUPS( tr_contenedor ){

	//console.log("agregar cups a "+tr_contenedor);
	var nomid = tr_contenedor;
	var nomtr = tr_contenedor;
	tr_contenedor = $("#"+tr_contenedor);

	var count = tr_contenedor.find("[name=ing_cactxtcups]").length;
	nomid+=count;
	var input = "<div><input type='text' style='width:200px;' name='ing_cactxtcups' id='ing_cactxtcups"+nomid+"' class='reset'  msgError='Digite el Codigo o el nombre'>";
	input+="<input type='hidden' name='ing_cachidcups' id='ing_cachidcups"+nomid+"' >";
	input+="<input type='hidden' name='id_idcups' id='id_idcups"+nomid+"' >";
	input+="<img border='0' style='width:15;' src='../../images/medical/root/borrar.png' onClick='eliminarCup(this, \"ing_cactxtcups"+nomid+"\",\"ing_cachidcups"+nomid+"\" );'></div>";

	//console.log("addcups");
	//Se busca el td_contenedor de los cups
	var td_contenedor = tr_contenedor.find("[name=ing_cactxtcups]:eq(0)").parent();
	td_contenedor.append( input );

	buscarCUPS(nomtr, count);

}

function eliminarCup( obj, id_cup_auto, id_cup_hide ){
	obj = jQuery(obj);
	obj.parent().remove(); //Elimina el div que contiene todo lo relacionado al cup
}

function reOrdenarResponsables(){
	var ind = 1;
	$(".numeroresponsable:visible").each(function(){
		$(this).text( "R"+ind );
		ind++;
		//var texto = $(this).text();
		//texto = texto.substring(1); //Para quitar la R
	});
}

function isJSON(data) {
	var isJson = false
	try {
		// this works with JSON string AND JSON object, not sure about others
	   var json = $.parseJSON(data);
	   isJson = typeof json === 'object' ;
	} catch (ex) {
		//console.error('data is not JSON');
	}
	return isJson;
}

var global_flag_medicos = true;

function buscarMedicos(){

	var wbasedato = $("#wbasedato").val();
	var wemp_pmla = $("#wemp_pmla").val();
	var filtraEspecialidadClinica = $("#filtraEspecialidadClinica").val();
	$("#ing_meitxtMedIng").autocomplete("admision_erp.php?consultaAjax=&accion=consultarMedico&wbasedato="+wbasedato+"&filtraEspecialidadClinica="+filtraEspecialidadClinica+"&wemp_pmla="+wemp_pmla,
	{
		cacheLength:1,
		delay:300,
		max: 100,
		scroll: false,
		scrollHeight: 500,
		matchSubset: false,
		matchContains: true,
		width:250,
		autoFill:false,
		minChars: 3,
		json:"json",
		formatItem: function(data, i, n, value) {
			//convierto el string en json
			eval( "var datos = "+data );
			return datos[0].usu;	//Eso es lo que se muestra al usuario
		},
		formatResult: function(data, value){
			if( global_flag_medicos == true  ){
				//Para ubicar la lista con los resultados encima del input y no debajo, si en 500milisegundos no hay lista, no funciona
				//porque con esta version de autocompletar no hay posibilidades de detectar cuando se despliega la lista
				global_flag_medicos = false;
				setTimeout( function(){
					if( $(".ac_results").length > 0 ){
						var oldTop = $(".ac_results").offset().top;
						var newTop = oldTop - $(".ac_results").height() - 25;
						$(".ac_results").css("top", newTop);
					}
				}, 500 );
			}
			//convierto el string en json
			eval( "var datos = "+data );
			return datos[0].valor.des;
		}
	}).result(
		function(event, item ){
			eval( "var datos = "+item );
			//Guardo el ultimo valor que selecciona el usuario
			//this.parentNode.parentNode El tr que contiene el input
			$( "input[type=text]", this.parentNode.parentNode ).eq(0).val( datos[0].valor.des ).removeClass("inputblank");;
			this._lastValue = this.value;
			global_flag_medicos = true;
			$( "input[type=hidden]", this.parentNode.parentNode ).eq(0).val( datos[0].valor.cod );
			$( "input[type=text]", this.parentNode.parentNode ).removeClass( "campoRequerido" );
			//se remueve la clase campoRequerido cuando selecciona el elemento del autocompletar
		}
	).on({
		change: function(){
			var cmp = this;
			global_flag_medicos = true;
			setTimeout( function(){
				//Pregunto si la pareja es diferente
				if( ( ( cmp._lastValue && cmp._lastValue != cmp.value ) || ( !cmp._lastValue && cmp.value != $( cmp ).attr( cmp.aqAttr ) ) )
					|| ( cmp._lastCodigo && cmp._lastCodigo != $( "input[type=hidden]", cmp.parentNode ).val() )
				)
				{
					alerta( " Digite un m\u00e9dico v\u00e1lido" )
					$( "input[type=hidden]", cmp.parentNode ).val( '' );
					cmp.value = '';
					cmp.focus();
				}
			}, 200 );
		},
		keyup: function(){
			global_flag_medicos = true;
		}
	});
}


function isDate(controlName ){
    var isValid = true;
	var format = "yy-mm-dd";
    try{
        jQuery.datepicker.parseDate(format, controlName, null);
    }
    catch(error){
        isValid = false;
    }

    return isValid;
}

function completarParticular( obj ){
	obj = jQuery(obj);

	if( obj.val() == "" || obj.val() ==  $.trim($("#pac_doctxtNumDoc").val()) ){
		obj.val( $.trim($("#pac_doctxtNumDoc").val()) );
		var fila = obj.parent().parent();
		fila.find("[name=res_tdo]").val( $("#pac_tdoselTipoDoc").val());
		var n1=$("#pac_no1txtPriNom").val();
		var n2=$("#pac_no2txtSegNom").val();
		var a1=$("#pac_ap1txtPriApe").val();
		var a2=$("#pac_ap2txtSegApe").val();
		fila.find("[name=res_nom]").val(n1+" "+n2+" "+a1+" "+a2);
		resetAqua( fila );
	}
}

function ponerPreadmitirEnBotones(){
	mostarOcultarDatosPreadmisiones( false );
	$("input[name='btRegistrarActualizar']").val('Preadmitir');
}
//-----------------------------------------------------------------------------------------------
// --> 	Funcion que reliza el llamado para una nueva admision a un paciente que llego con turno
//-----------------------------------------------------------------------------------------------
function mostrarAdmisionDesdeTurno(turno, tipoDocumento, documento)
{
	if($("#puestoTrabajo").val() == "")
	{
		alert("Primero debe seleccionar su puesto de trabajo actual.");
		$("#puestoTrabajo").css("border-color", "red");
		return;
	}
	// --> Primero apago la alerta del llamado en el monitor
	$.post("admision_erp.php",
	{
		consultaAjax:   		'',
		accion:         		'apagarAlertaDeLlamado',
		wemp_pmla:        		$('#wemp_pmla').val(),
		turno:					turno
	}, function(respuesta){

		// --> Abro la admision, con el tipo y documento del paciente.
		mostrarAdmision();
		setTimeout(function(){
			$("#pac_doctxtNumDoc").val(documento);
			$("#pac_tdoselTipoDoc").val(tipoDocumento);
			$("#numTurnoPaciente").html(turno);
			$("#numTurnoPaciente").attr("valor", turno);
			mostrarDatosPreadmision();
			consultarClientesEspeciales();
			consultarSiActivo();
			consultarSiRechazado();
			verificarTriageUrgencias();
		}, 500);
	});
}

function mostrarAdmisionDesdeAgendaMedica(){//2016-02-26

	var tipoDocumento = $("#TipoDocumentoPacAm").val();
	var documento     = $("#DocumentoPacAm").val();
	var turno         = $("#TurnoEnAm").val();

	mostrarAdmision();
	setTimeout(function(){
		$("#pac_doctxtNumDoc").val(documento);
		$("#pac_tdoselTipoDoc").val(tipoDocumento);
		$("#numTurnoPaciente").html(turno);
		$("#numTurnoPaciente").attr("valor", turno);
		mostrarDatosPreadmision();
		consultarClientesEspeciales();
		consultarSiActivo();
		consultarSiRechazado();
	}, 500);
}
//-----------------------------------------------------------------------
// --> Funcion que genera el llamado del paciente para que sea atendido
//-----------------------------------------------------------------------
function llamarPacienteAtencion(turno, elemento)
{
	if($("#puestoTrabajo").val() == "")
	{
		alert("Primero debe seleccionar su puesto de trabajo actual.");
		$("#puestoTrabajo").css("border-color", "red");
		return;
	}

	$.post("admision_erp.php",
	{
		consultaAjax:   		'',
		accion:         		'llamarPacienteAtencion',
		wemp_pmla:        		$('#wemp_pmla').val(),
		turno:					turno,
		ventanilla:				$("#puestoTrabajo").val()
	}, function(respuesta){
		if(respuesta.Error)
		{
			alert(respuesta.Mensaje);
			$(".botonLlamarPaciente").show();
			$(".botonColgarPaciente").hide();
			$("#botonAdmitir"+turno).hide();
			//$("#imgLlamar"+turno).hide();
			$("#trTurno_"+turno).attr("class", $("#trTurno_"+turno).attr("classAnterior"));

			activarPrimerTurno();
		}
		else
		{
			$(".botonLlamarPaciente").hide();
			$(".botonColgarPaciente").hide();
			$("#"+elemento).hide();
			$("#"+elemento).next().show();
			$("#"+elemento).next().next().show();
			$("#botonAdmitir"+turno).show();
			$("#trTurno_"+turno).attr("classAnterior", $("#trTurno_"+turno).attr("class"));
			$("#trTurno_"+turno).attr("class", "fondoAmarillo");
		}
	}, 'json');
}
//-----------------------------------------------------------------------
// --> Funcion que cancela el llamado del paciente para que sea atendido
//-----------------------------------------------------------------------
function cancelarLlamarPacienteAtencion(turno)
{
	$.post("admision_erp.php",
	{
		consultaAjax:   		'',
		accion:         		'cancelarLlamarPacienteAtencion',
		wemp_pmla:        		$('#wemp_pmla').val(),
		turno:					turno
	}, function(respuesta){

		$(".botonLlamarPaciente").show();
		$(".botonColgarPaciente").hide();
		$("#botonAdmitir"+turno).hide();
		// $("#botonCancelar"+idTurno).show();
		$("#trTurno_"+turno).attr("class", $("#trTurno_"+turno).attr("classAnterior"));

		activarPrimerTurno();
	});
}
//----------------------------------------------------
// --> Funcion que cancela el turno de un paciente
//----------------------------------------------------
function cancelarTurno(turno)
{
	if($("#puestoTrabajo").val() == "")
	{
		alert("Primero debe seleccionar su puesto de trabajo actual.");
		$("#puestoTrabajo").css("border-color", "red");
		return;
	}

	if(confirm("¿ Esta seguro que desea cancelar el turno "+turno+" ?"))
	{
		$.post("admision_erp.php",
		{
			consultaAjax:   		'',
			accion:         		'cancelarTurno',
			wemp_pmla:        		$('#wemp_pmla').val(),
			turno:					turno
		}, function(respuesta){
			if(respuesta.Error)
				alert(respuesta.Mensaje);
			else
			{
				$("#trTurno_"+turno).hide(500, function(){
					listarPacientesConTurno();
				});
			}
		}, 'json');
	}
}
//----------------------------------------------------
// --> Mostrar en ventana modal los turno cancelados
//----------------------------------------------------
function verTurnosCancelados()
{
	$.post("admision_erp.php",
	{
		consultaAjax:   		'',
		accion:         		'verTurnosCancelados',
		wemp_pmla:        		$('#wemp_pmla').val()
	}, function(respuesta){
		$("#divTurnosCancelados").html(respuesta).dialog({
			modal	: true,
			width	: 'auto',
			//title	: "<div align='left'>Fecha ingreso: <input type='text' id='fechaTurnosCancel' style='width:110px' />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Turnos cancelados y sin finalizar admision.</div>",
			title	: "<div align='center'>Turnos cancelados y sin finalizar admision.</div>",
			show	: { effect: "slide", duration: 600 },
			hide	: { effect: "fold", duration: 600 },
			close: function( event, ui ) {
				listarPacientesConTurno();
			}
		});
		cargar_elementos_datapicker();
		$("#fechaTurnosCancel").datepicker({
			showOn: "button",
			buttonImage: "../../images/medical/root/calendar.gif",
			buttonImageOnly: true,
			maxDate:"+0D"
		});
		$("#fechaTurnosCancel").next().css({"cursor": "pointer"}).attr("title", "Seleccione");
		$("#fechaTurnosCancel").after("&nbsp;");
	});
}
//--------------------------------------------------------
//	--> Activar datapicker
//---------------------------------------------------------
function cargar_elementos_datapicker()
{
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
		yearSuffix: ''
	};
	$.datepicker.setDefaults($.datepicker.regional['esp']);
}
//----------------------------------------------------
// --> Mostrar en ventana modal los turno cancelados
//----------------------------------------------------
function habilitarTurno(turno, elemento)
{
	if(confirm("¿ Esta seguro que desea habilitar el turno "+turno+" ?"))
	{
		$.post("admision_erp.php",
		{
			consultaAjax:   		'',
			accion:         		'habilitarTurno',
			wemp_pmla:        		$('#wemp_pmla').val(),
			turno:					turno
		}, function(respuesta){
			$(elemento).parent().parent().hide(500, function(){
				$(elemento).parent().parent().remove();
			});
		});
	}
}
//----------------------------------------------------
// --> Recargar lista de pacientes con turnos
//----------------------------------------------------
function listarPacientesConTurno()
{
	$.post("admision_erp.php",
	{
		consultaAjax:   		'',
		accion:         		'listarPacientesConTurno',
		wemp_pmla:        		$('#wemp_pmla').val()
	}, function(html){
		//puestoTrabaSelec = $("#puestoTrabajo").val();
		$("#divListaPacientesConTurno").html(html);
		//$("#puestoTrabajo").val(puestoTrabaSelec);
		(($("#puestoTrabajo").val() == '' ) ? $("#puestoTrabajo").css("border-color", "red") : '');


		// --> Activar el buscador de texto, para los turnos
		$('#buscardorTurno').quicksearch('#tablaListaTurnos .find');
		// --> Tooltip en la lista de turnos
		$('[tooltip=si]').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
		// --> Si existe un turno que ya haya sido llamado por este usuario, inhabilito los demas
		if($("#turnoLlamadoPorEsteUsuario").val() != '')
		{
			var idTurno = $("#turnoLlamadoPorEsteUsuario").val();
			$(".botonLlamarPaciente").hide();
			$(".botonColgarPaciente").hide();
			$("#imgLlamar"+idTurno).hide();
			$("#imgLlamar"+idTurno).next().show();
			$("#imgLlamar"+idTurno).next().next().show();
			$("#botonAdmitir"+idTurno).show();
			$("#trTurno_"+idTurno).attr("classAnterior", $("#trTurno_"+idTurno).attr("class"));
			$("#trTurno_"+idTurno).attr("class", "fondoAmarillo");
		}
		// --> Solo se puede llamar al primer turno de la lista, para evitar que llamen en desorden
		else
			activarPrimerTurno();
	});
}
//--------------------------------------------------------------------------------------------------
// --> Solo se puede llamar al primer turno de la lista, para evitar que llamen en desorden
//--------------------------------------------------------------------------------------------------
function activarPrimerTurno()
{
	return;
	var primero = true;
	$(".botonLlamarPaciente:visible").each(function(){
		if(primero)
			primero = false;
		else
			$(this).hide();
	});
}

//-------------------------------------------------------------
// --> Actualiza el usuario asociado a un puesto de trabajo
//-------------------------------------------------------------
function cambiarPuestoTrabajo(respetarOcupacion)
{
	if($("#puestoTrabajo").val() == '' )
		$("#puestoTrabajo").css("border-color", "red");
	else
		$("#puestoTrabajo").css("border-color", "#AFAFAF");

	$.post("admision_erp.php",
	{
		consultaAjax:   		'',
		accion:         		'cambiarPuestoTrabajo',
		wemp_pmla:        		$('#wemp_pmla').val(),
		puestoTrabajo:			$("#puestoTrabajo").val(),
		respetarOcupacion:		respetarOcupacion
	}, function(respuesta){
		if(respuesta.Error)
		{
			if(confirm(respuesta.Mensaje+"\nDesea liberarla?"))
				cambiarPuestoTrabajo(false);
			else
				$("#puestoTrabajo").val($("#puestoTrabajo").attr("ventanillaActUsu"));
		}
	}, 'json');
}

function cambiarTipoDocumento( obj ){

	$(obj).parent().next("td").find("input").val("");
	if( $( "option:selected", obj ).attr("docxhis") == "on" ){
		console.log( "este debe reemplazar " );
		$(obj).parent().next("td").find("input").val($("#ing_histxtNumHis").attr("placeholder") );
	}
	$(obj).parent().next("td").find("input").removeClass("campoRequerido");
	$(obj).parent().next("td").find("input").focus();
}

function cambiarEstadoComplementariedad( obj ){
   if( $(obj).attr("estadoAnterior") == "on" ){
   		$( obj ).attr("checked", false);
   		$(obj).attr("estadoAnterior", "off" );
   }else{
   		$(obj).attr("estadoAnterior", "on" );
   }
}

function filtrarPorCco( select ){
	var seleccionado = $( select ).find("option:selected").val();
	if( seleccionado == "" ){
		$("tr[tipo='tr_admitidos']").show();
	}else{
		$("tr[tipo='tr_admitidos'][ccoingresopaciente='"+seleccionado+"']").show();
		$("tr[tipo='tr_admitidos'][ccoingresopaciente!='"+seleccionado+"']").hide();
	}
}

/*************** INICIO FUNCIONES PARA EGRESO DE PACIENTE ********************/
$(document).ready(function(){ $("#historia_alta").val(''); });

function modal_alta_paciente_otro_servicio()
{
	$("#historia_alta").val('');
	$("#div_info_alta").hide();
	$("#div_info_alta").html("");
	// fnModalLoading();
	fnModalSeleccionarHistoria();
}

function fnModalSeleccionarHistoria()
{
    $("#div_iniciar_alta_otro_ss" ).dialog({
        "closeOnEscape": false,
        show: {
            effect: "blind",
            duration: 100
        },
        hide: {
            effect: "blind",
            duration: 100
        },
        height: 500,
        // maxHeight: 400,
        width: 400,//'auto',
        buttons: {
            "Cerrar": function() {
              $( this ).dialog( "close" );
              // fnModalLoading_Cerrar();
            }},
        dialogClass: 'fixed-dialog',
        modal: true,
        title: "Consultar historia activa para iniciar el alta",
        beforeClose: function( event, ui ) {
            $(".bloquear_todo").removeAttr("disabled");
        },
        create: function() {
           $(this).closest('.ui-dialog').on('keydown', function(ev) {
               if (ev.keyCode === $.ui.keyCode.ESCAPE) {
                   $("#div_iniciar_alta_otro_ss" ).dialog('close');
                   // fnModalLoading_Cerrar();
               }
           });
        }
    }).on("dialogopen", function( event, ui ) {
        //
    });
}

var wbasedatoCliame_alta ='';
var wbasedatoMovhos_alta ='';
var wbasedatoHce_alta    ='';
function consultarHistoriaPendienteDeAlta()
{
	$("#div_info_alta").html("");
	$("#div_info_alta").hide();
	var historia_alta = $("#historia_alta").val();
	if(historia_alta.replace(/ /gi, "") != '')
	{
		var obJson              = parametrosComunes();
		obJson['consultaAjax']  = '';
		obJson['accion'] 	    = 'consultar_historia_activa';
		obJson['historia_alta'] = historia_alta;
		// obJson['ccoUrgencias']  = document.forms.forma.codCco.value;

        fnModalLoading();

        $.post("admision_erp.php", obJson,
            function(data){
                if(data.error == 1)
                {
                    fnModalLoading_Cerrar();
                    jAlert(data.mensaje, "Mensaje");
                }
                else
                {
					$("#div_info_alta").html(data.html);
					$("#div_info_alta").show();
                }
                return data;
        },"json").done(function(data){
				wbasedatoCliame_alta = data.wbasedatoCliame;
				wbasedatoMovhos_alta = data.wbasedatoMovhos;
				wbasedatoHce_alta    = data.wbasedatoHce;
                fnModalLoading_Cerrar();
        }).fail(function(xhr, textStatus, errorThrown) { mensajeFailAlert('', xhr, textStatus, errorThrown); });
	}
	else
	{
		jAlert("Debe escribir una historia","Mensaje");
	}
}

function altaPacienteOtroServicio(historia_alta, wing, usuario, nombre)
{
	console.log("wbasedatoMovhos_alta: "+wbasedatoMovhos_alta);
	jConfirm('Dar&aacute; de <span style="font-weight:bold;color:red;">ALTA DEFINITIVA</span> a '+nombre+', Esta seguro?', 'Alta definitiva', function(r) {
		if(r){
			fnModalLoading();
			var obJson                  = parametrosComunes();
			obJson['consultaAjax']      = '11';
			obJson['operacion']         = 'marcaraltadefinitiva';
			obJson['basedatos']         = wbasedatoMovhos_alta;
			obJson['basedatoshce']      = wbasedatoHce_alta;
			obJson['paciente']          = historia_alta;
			obJson['ingreso']           = wing;
			obJson['seguridad']         = usuario;
			obJson['wcubiculo_ocupado'] = '';
			obJson['turno']             = '';
			obJson['desde']             = 'altaPacienteOtroServicio';

			$.post("../../hce/procesos/agenda_urgencias_por_especialidad.php", obJson,
		        function(data){
		        	console.log(data);
		            if (data.replace(/ /gi,"") != 'ok')
					{
						jAlert( data, 'ALERTA' );
						$('#chk_dar_alta_otro_ss').removeAttr('checked');
					}
					else{
						jAlert( "El paciente "+nombre+' ha sido dado de alta definitiva. <br><br><span style="font-weight:bold;color:red;">RECUERDE REALIZAR EL EGRESO</span>', 'ALERTA' );
						// $('#div_chk_alta').remove();
						// $('#btn_egresar').show();
					}
					$("#popup_container").find(":input[type=button]").css({"width":"100px"});
		            return data;
		    }).done(function(data){
		            fnModalLoading_Cerrar();
					consultarHistoriaPendienteDeAlta();
		    }).fail(function(xhr, textStatus, errorThrown) { mensajeFailAlert('', xhr, textStatus, errorThrown); });
		}
		else
		{
			$('#chk_dar_alta_otro_ss').removeAttr('checked');
		}
	});
	$("#popup_container").find(":input[type=button]").css({"width":"100px"});
}

function abrirEgresarPaciente(path)
{
	$("#btn_egresar").hide(50);
	window.open(path,'','fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=1,scrollbars=1,titlebar=0');
}

/**
 * [parametrosComunes: Genera un json con las variables más comunes que se deben enviar en los llamados ajax, evitando tener que crear los mismos parámetros de envío
 *                     en cada llamado ajax de forma manual.]
 * @return {[type]} [description]
 */
function parametrosComunes()
{
	var obJson              = {};
	obJson['wemp_pmla']     = $("#wemp_pmla").val();
	// obJson['wbasedato_HCE'] = $("#wbasedatohce_alta").val();
	// obJson['wbasedato']     = $("#wbasedato_alta").val();
	obJson['consultaAjax']  = '';
    return obJson;
}

// sleep time expects milliseconds
function sleep (time)
{

	return new Promise((resolve) => setTimeout(resolve, time));
}

function detallartope(tope)
{
	var tope = $(tope).attr('attrtope');
	//alert(tope);
	if($(".detalletope_"+tope).length == 0 )
	{
		alert("No hay detalle para este tipo de concepto");
	}
	else
		$(".detalletope_"+tope).toggle();
}

function abrirTableroDigitalizacion(historia,ingreso,tipoDocumento,documento,empresa,fecha)
{
	var w = $(window).width();
	var h = $(window).height();


	$('html, body').animate({scrollTop:0});


	// Usage! 3-

	sleep(500).then(() => {
	var html =  '<iframe id="iframeModalTablero" src="../../ips/procesos/tableroDigitalizacion.php?AbiertoDesdeAdmision=si&DesdeAdmisionHistoria='+historia+'&DesdeAdmisionIngreso='+ingreso+'&DesdeAdmisionTipoDocumento='+tipoDocumento+'&DesdeAdmisionDocumento='+documento+'&DesdeAdmisionResponsable='+empresa+'&DesdeAdmisionFecha='+fecha+'" width="100%" height="100%" frameborder="0" allowtransparency="true"></iframe>';

		$("#modaliframe").html(html).show().dialog({
			// dialogClass: 'fixed-dialog',
			modal: true,
			title: "<div align='center' style='font-size:10pt'>Soportes Facturacion </div>",
			 height: h,
			 width: w,
			 position: {
				my: "left top",
				at: "right top",
				of: window
			}
		});
	});
}

function mensajeFailAlert(mensaje, xhr, textStatus, errorThrown)
{
    var msj_extra = '';
    msj_extra = (mensaje != '') ? "<br>"+mensaje: mensaje;
    jAlert($("#failJquery").val()+msj_extra, "Mensaje");
    $("#div_error_interno").html(xhr.responseText);
    // console.log(xhr);
    // jAlert("error interno: "+xhr.responseText, "Mensaje"); console.log("error");
    fnModalLoading_Cerrar();
    $(".bloquear_todo").removeAttr("disabled");
}


/**
 * [fnModalLoading: Es función se encarga de mostrar una ventana modal cada vez que se hace un llamado ajax con el fin de bloquear la página web hasta que se
 *                    se genere una respuesta y evitar que el usuario genere más eventos (click) sin terminar la petición anterior y evitar problemas
 *                    en la veracidad de datos]
 * @return {[type]} [description]
 */
function fnModalLoading()
{
    $( "#div_loading" ).dialog({
        show: {
            effect: "blind",
            duration: 100
        },
        hide: {
            effect: "blind",
            duration: 100
        },
        height: 'auto',
        // maxHeight: 600,
        width:  'auto',//800,
        // buttons: {
        //     "Cerrar": function() {
        //       $( this ).dialog( "close" );
        //     }},
        dialogClass: 'fixed-dialog',
        modal: true,
        title: "Consultando ...",
        beforeClose: function( event, ui ) {
            //
        },
        create: function() {
           $(this).closest('.ui-dialog').on('keydown', function(ev) {
               if (ev.keyCode === $.ui.keyCode.ESCAPE) {
                   $( "#div_loading" ).dialog('close');
               }
           });
        },
        "closeOnEscape": false,
        "closeX": false
    }).on("dialogopen", function( event, ui ) {
        //
    });
}

/**
 * [fnModalLoading_Cerrar: complemento a la función fnModalLoading, esta se encarga de cerrar la ventana modal]
 * @return {[type]} [description]
 */
function fnModalLoading_Cerrar()
{
    if($("#div_loading").is(":visible"))
    {
        $("#div_loading").dialog('close');
    }
}

function soloNumeros(evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    // alert(charCode);
    if(charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 37 && charCode != 39 && charCode != 35 && charCode != 36 && charCode != 46) //37:teclaizquierda 39:tecladerecha 36:teclainicio 38:teclafin 46:suprimir
    {
    	return false;
    }
    return true;
}

function validarEnterConsultar(evt)
{
	var charCode = (evt.which) ? evt.which : evt.keyCode;
    if(charCode == 13)
    {
		$("#btn_consultar_hitoria_alta").trigger("onclick");
    }
}
// mirar
function cambioCobertura(e)
{

	var hijos = $(e).find("option:selected").attr("hijos");
	var arrayHijos  = hijos.split(",")
	console.log("funcionando hasta aca");
    $("#pac_tafselTipAfi > option[value!='']").remove();

	arrayHijos.forEach(function (key) {
		console.log( key );
		auxArbol    = key.split("|");
		hijoDirecto = auxArbol[0];
		nietos      = auxArbol[1];
		console.log( "hijoDirecto: "+hijoDirecto+", nietos"+nietos );
		option      = $("#pac_tafselTipAfi_original > option[codigoRelacion='"+hijoDirecto+"']").clone();
		hijos       = $(option).attr("hijos", nietos);
	    $("#pac_tafselTipAfi").append(option);
	});
	return;
}

function cambioTipoAfiliacion(e){

	var hijos = $(e).find("option:selected").attr("hijos");
	console.log( hijos )
	var arrayHijos  = hijos.split("_")
	$("[name='ing_pcoselPagCom']").eq(0).find("option[value!='']").remove();

	arrayHijos.forEach(function (key) {
	    option = $("#ing_pcoselPagCom_original").eq(0).find("option[codigoRelacion='"+key+"']").clone();
	    $("[name='ing_pcoselPagCom']").eq(0).append(option);
	});
	return;

}


function solicitarCambioDocumento( obj, historia, ingreso, tipoDocumentoAnterior, documentoAnterior, nombre ){

	tipoDocumento = tipoDocumentoAnterior;
	documento     = documentoAnterior;

	$("#select_tdoc_new").find("option[value='"+tipoDocumento+"']").attr("selected", true);
	$("#select_doc_new").val(documentoAnterior);
	$("#div_mensaje_solitudCD").hide();

	$("#span_his_actual").html(historia+" - "+ingreso);
	$("#span_doc_actual").html(tipoDocumento+" - "+documento);
	$("#span_nom_actual").html(nombre);
	$("#div_doc_change" ).dialog({
        "closeOnEscape": false,
        show: {
            effect: "blind",
            duration: 100
        },
        hide: {
            effect: "blind",
            duration: 100
        },
        height: 650,
        // maxHeight: 400,
        width: 1000,//'auto',
        buttons: {
            "Enviar solicitud": function() {
            	enviarSolicitudCambioDocumento( historia, ingreso, tipoDocumentoAnterior, documentoAnterior );
            	parpadear( 1 );
            },
            "Cerrar Sin enviar": function(){
            	 $( obj ).removeAttr("checked");
            	 $("#span_mensajeCD").html( "" );
            	 $("#txt_jus_change_Doc").val( "" );
            	 $("#div_mensaje_solitudCD").hide();
            	 $( this ).dialog( "close" );
            }
        },
        dialogClass: 'fixed-dialog',
        modal: true,
        title: "SOLICITAR CAMBIO DE DOCUMENTO"
    });
}
function parpadear( cantidad ){

	if( cantidad <= 15 ){
		cantidad++;
		$('#div_mensaje_solitudCD').fadeIn(500).delay(250).fadeOut(500, parpadear(cantidad) );
	}else{
		return;
	}
}

function enviarSolicitudCambioDocumento( historia, ingreso, tipoDocumentoAnterior, documentoAnterior ){

	var nuevoTipoDoc   = $("#select_tdoc_new option:selected").val();
	var documentoNuevo = $("#select_doc_new").val();
	var justificacion  = $("#txt_jus_change_Doc").val();

	$.ajax({
				url: "admision_erp.php",
				context: document.body,
				type: "POST",
				data:
				{
					consultaAjax:   '',
					accion:         'solicitarCambioDocumento',
					wbasedato:		$( "#wbasedato" ).val(),
					wemp_pmla:		$( "#wemp_pmla" ).val(),
					wtdocant:       tipoDocumentoAnterior,
					wdocant:        documentoAnterior,
					whistoria:      historia,
					wingreso:       ingreso,
					wtdocnue:       nuevoTipoDoc,
					wdocnuev:       documentoNuevo,
					justificacion:  justificacion
				},
				async: false,
				success:function(respuesta) {
					console.log( respuesta );
					$("#span_mensajeCD").html( respuesta );
				}
	});
}

function asociarPreanestesia( obj ){

	console.log( $(obj).html() )
	if( $(obj).html() == "SI" ){
		$("#asociarPreanestesia").val("on");
	}else{
		$("#asociarPreanestesia").val("off");
	}
	$("#div_preanestesia").dialog("close");
	$("#div_preanestesia").dialog("destroy");
}

function inhabilitarkeypress(){
	console.log("y entonces porque no funciona el keypress?");
    if(e == 13){
    	console.log("entro por aca")
      return false;
    }
}
/*************** FIN FUNCIONES PARA EGRESO DE PACIENTE ********************/

</script>
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
/*======================================================DOCUMENTACION APLICACION==========================================================================

APLICACION LA ADMISION DE PACIENTES

1. DESCRIPCION:
Este software se desarrolla para la admision y el ingreso de pacientes en la clinica las americas, clinica del sur y el IDC, la aplicacion
se realiza con las especificaciones necesarias de acuerdo con las normas que son exigidas por el ministerio de salud, este debe validar automanticamente
a que empresa se le esta haciendo la admision, ademas el resto de validaciones que se necesitan.

Este formulario permite el ingreso de los datos del ingreso, personales, del acompañante, del responsable, del pagador, de la autorizacion y otros
datos del ingreso.
==================================================================================================================================================*/

/****************************************************************************
* Funciones
*****************************************************************************/
function seguimiento($seguir , $validacion)
{
	if($validacion ==true)
	{
		if (file_exists("seguimientoadmision.txt")) {
			unlink("seguimientoadmision.txt");
		}
	}
	$fp = fopen("seguimientoadmision.txt","a+");
	fwrite($fp, "[".date("Y-m-d H:i:s")."]".PHP_EOL.$seguir);
	fclose($fp);
}


function empresa_planes_servicios( $codigoEmpresaPlan, $serviciosCompletos, $serviciosActivoPorPlan )
{
	global $conex, $wemp_pmla;
	$wfachos      = consultarAliasPorAplicacion($conex, $wemp_pmla, "Facturacion hospitalaria");
	$condicion = "(";
	$serviciosEmpresaplan    = array();
	$divServiciosEmpresaPlan = array();
	$codigoAnt = "";
	$codigoNue = "";
	$i = 0;
	foreach( $codigoEmpresaPlan as $keyEmpresa => $planes)
	{
		foreach( $planes as $keyPlan=>$datos)
		{
			$i++;
			($i==1) ? $condicion .= "'{$datos['codigo']}'" : $condicion .= ",'{$datos['codigo']}'";
		}
	}
	$condicion .= ")";

	$query = "SELECT sesepl codigo, sesser servicios
			    FROM {$wfachos}_000010
			   WHERE sesepl IN {$condicion}
			     AND sesest = 'on'
			   GROUP BY codigo, servicios
			   ORDER BY codigo";
	$rs    = mysql_query( $query, $conex );
	while( $row = mysql_fetch_array( $rs ) )
	{
		$serviciosAuxiliares = explode(",", $row['servicios']);

		foreach( $serviciosAuxiliares as $j => $datos)
		{
			$serviciosEmpresaplan[$row['codigo']][$serviciosAuxiliares[$j]]='';
		}
		$serviciosEmpresaplan[$row['codigo']]['sd']='';
	}

	foreach( $serviciosEmpresaplan as $keyEmpresaPlan => $servicios)
	{
		$divServiciosEmpresaPlan[$keyEmpresaPlan]  = "<div align='center' class='fila2' id='div_servicios_{$keyEmpresaPlan}' style='cursor:default; display:none; repeat scroll 0 0; position:relative; width:98%; height:98%; overflow:auto;'>";
		$divServiciosEmpresaPlan[$keyEmpresaPlan] .= "<table>";
		$divServiciosEmpresaPlan[$keyEmpresaPlan] .= "<span  class='subtituloPagina2'> SELECCI&Oacute;N DE SERVICIOS </span><br>";
		( count($servicios) > 2) ? $divServiciosEmpresaPlan[$keyEmpresaPlan] .= "<br><tr class='encabezadotabla'><td>ELEGIR</td><td>SERVICIO</td></tr>" :$divServiciosEmpresaPlan[$keyEmpresaPlan] .= "<tr><td><span  class='subtituloPagina2'> SIN SERVICIOS ASOCIADOS </span><br></td></tr>";
		$i = 0;
			foreach( $servicios as $keyServicio => $datos)
			{
				$i++;
				if(trim($keyServicio != "") and trim($keyServicio != "sd"))
				{
					if( array_key_exists( $keyServicio, $serviciosActivoPorPlan[$keyEmpresaPlan] ) )
					{
						$checked        = "checked";
						$estadoActual   = "s";
						$estadoAnterior = "s";
					}else
						{
							$checked        = "";
							$estadoActual   = "n";
							$estadoAnterior = "n";
						}
					$wclass='fila1';
					$divServiciosEmpresaPlan[$keyEmpresaPlan]     .= "<tr class='{$wclass}'>";
						$divServiciosEmpresaPlan[$keyEmpresaPlan] .= "<td align='center'><input type='checkbox' ".$checked." estadoActual='{$estadoActual}' estadoAnterior='{$estadoAnterior}' empresaPlan='{$keyEmpresaPlan}' servicio='{$keyServicio}' onchange='cambiarEstadoServicio(this)'></td>";
						$divServiciosEmpresaPlan[$keyEmpresaPlan] .= "<td>".$serviciosCompletos[$keyServicio]['nombre']."</td>";
					$divServiciosEmpresaPlan[$keyEmpresaPlan]     .= "</tr>";
				}
			}
		$divServiciosEmpresaPlan[$keyEmpresaPlan] .= "</table>";
		$divServiciosEmpresaPlan[$keyEmpresaPlan] .= "<br>";
		$divServiciosEmpresaPlan[$keyEmpresaPlan] .= "<div align='center'><input type='button' value='CERRAR' class='botona' onclick='cerrarDivServicios({$keyEmpresaPlan})'></div>";
		$divServiciosEmpresaPlan[$keyEmpresaPlan] .= "</div>";
	}

	return($divServiciosEmpresaPlan);
}

function empresaEmpleado($wemp_pmla, $conex, $wbasedato, $cod_use_emp)
{
    $use_emp = '';

    echo $cod_use_emp."<br>";
    $user_session = explode('-',$cod_use_emp);
    $user_session = (count($user_session) > 1) ? $user_session[1] : $user_session[0];

    $q = "  SELECT  Codigo, Empresa
            FROM    usuarios
            WHERE   codigo = '".$user_session."'
                    AND Activo = 'A'";
    $res = mysql_query($q,$conex);
    if(mysql_num_rows($res) > 0)
    {
        $row = mysql_fetch_array($res);
        $user_session = ( strlen($user_session) > 5) ? substr($user_session,-5): $user_session;

        $use_emp = $user_session.'-'.$row['Empresa']; // concatena los últimos 5 digitos del código del usuario con el código de la empresa a la que pertenece.
    }
    return $use_emp;
}

function consultarPermisosUsuario( $wcodigoUsuario ){

	global $conex, $wemp_pmla, $wbasedato;
	//--> consulta del usuario en talhuma
	$consultarOtrosUSuarios = "off";

	$q51   = " SELECT Detval
				 FROM root_000051
				WHERE Detapl = 'usu_ext_hce'
				  AND Detemp = '$wemp_pmla'";
	$rs    = mysql_query( $q51, $conex );
	$row   = mysql_fetch_assoc( $rs );
	if( $row['Detval'] != "" ){
		$consultarOtrosUSuarios = $row['Detval'];
	}


	//$cod_use_Talhuma  = empresaEmpleado($wemp_pmla, $conex, $wbasedato, $wcodigoUsuario );--> los permisos están asociados a los usuarios de cajas( cliame_000030 )
	$q51   = " SELECT Detval
				 FROM root_000051
				WHERE Detapl = 'fuente_hce'
				  AND Detemp = '$wemp_pmla'";
	$rs    = mysql_query( $q51, $conex );
	$row   = mysql_fetch_assoc( $rs );

	$query = " SELECT Pergra graba, Percon consulta, Permod actualiza, peranu anula
				 FROM {$wbasedato}_000081
				WHERE Perfue = '{$row['Detval']}'
				  AND Perusu = '$wcodigoUsuario' ";

	if( $consultarOtrosUSuarios == "on" ){
		$query .= " UNION ALL ";
		$query .= " SELECT Pergra graba, Percon consulta, Permod actualiza, peranu anula
				      FROM {$wbasedato}_000241
				     WHERE Perfue = '{$row['Detval']}'
				       AND Perusu = '$wcodigoUsuario' ";
	}

	$rsper = mysql_query($query, $conex) or die( mysql_error()." - ".$query );
	$row   = mysql_fetch_assoc( $rsper );
	return($row);
}

function consultarCodigoColombia(){
	global $conex, $wemp_pmla;
	$query = " SELECT Detval
				 FROM root_000051
				WHERE Detemp = '$wemp_pmla'
				  AND Detapl = 'codigoColombia'";
	$rs    = mysql_query( $query, $conex ) or die( mysql_error() );
	$row   = mysql_fetch_array( $rs );
	return( $row[0] );
}

function consultaMaestros($tabla, $campos, $where, $group, $order, $cant=1){
	global $conex;
	global $wbasedato;


		if ($cant==1)
		{
			$q = " SELECT ".$campos."
					FROM ".$tabla."";
			if ($where != "")
			{
				$q.= " WHERE ".$where."";
			}

		}
		else
		{

			$q = " SELECT ".$campos."
				FROM ".$wbasedato."_".$tabla."";
			if ($where != "")
			{
				$q.=" WHERE ".$where."";
			}


		}

			if ($group != "")
			{
				$q.="	GROUP BY ".$group." ";
			}
			if ($order != "")
			{
				  $q.="	ORDER BY ".$order." ";
			}


		$res1 = mysql_query($q,$conex) or die (" Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		// $num1 = mysql_num_rows($res1);

	 return $res1;
}

function consultarPaises( $pais ){

		global $conex;




		$val = "";

		//pais
		$sql = "SELECT Paicod, Painom
				FROM root_000077
				WHERE Paiest = 'on'
					AND ( Painom LIKE '%".utf8_decode($pais)."%' OR Paicod LIKE '%".utf8_decode($pais)."%' )
				ORDER BY Painom
				LIMIT 25
				";

		$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );

		if( $num > 0 ){

			for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){

					$rows[ 'Paicod' ] = trim( utf8_encode($rows[ 'Paicod' ]) );
					$rows[ 'Painom' ] = trim( utf8_encode($rows[ 'Painom' ]) );

					//Creo el resultado como un json
					//Primero creo un array con los valores necesarios
					$data[ 'valor' ] = Array( "cod"=> $rows[ 'Paicod' ], "des"=> $rows[ 'Painom' ] );	//Este es el dato a procesar en javascript
					$data[ 'usu' ] = "{$rows[ 'Paicod' ]}-{$rows[ 'Painom' ]}";	//Este es el que ve el usuario
					$dat = Array();
					$dat[] = $data;

					$val .= json_encode( $dat )."\n";

			}
		}

		return $val;
	}

function consultarDepartamentos( $dep, $codigoPais = '', $name_objeto=''){

		global $conex;




		$val = "";

		$arr_names_excluidos = array('pac_dretxtDepResp','AccConductordp','AccDepPropietario','Catdep','Accdep');

		if( $codigoPais == "" && in_array($name_objeto, $arr_names_excluidos) == false ){
			return $val;
		}

		/**
		 * Si codigoPais es diferente a %(busqueda por todos los departamentos) y diferente a
		 * 169 (Colombia), entonces busca el departamento por otro pais, que es codigo 01
		 */
		if( $codigoPais == '' ){
			$codigoPais = '169';
		}
		if( $codigoPais != '' && $codigoPais != '169' ){
			$codigoPais = '01';
		}

		//Diagnostico
		$sql = "SELECT Codigo, Descripcion
				FROM root_000002
				WHERE (Descripcion LIKE '%".utf8_decode($dep)."%' OR Codigo LIKE '%".utf8_decode($dep)."%' )
				  and (codigoPais = '".$codigoPais."' or codigoPais = '*' )
				ORDER BY Descripcion
				";
		$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );

		$hayNoAplica = false;
		if( $num > 0 ){

			for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){

					$rows[ 'Codigo' ] = trim( utf8_encode($rows[ 'Codigo' ]) );
					$rows[ 'Descripcion' ] = trim( utf8_encode($rows[ 'Descripcion' ] ) );
					if($rows['Codigo'] == "00" )
						$hayNoAplica = true;
					//Creo el resultado como un json
					//Primero creo un array con los valores necesarios
					$data[ 'valor' ] = Array( "cod"=> $rows[ 'Codigo' ], "des"=> $rows[ 'Descripcion' ] );	//Este es el dato a procesar en javascript
					$data[ 'usu' ] = "{$rows[ 'Codigo' ]}-{$rows[ 'Descripcion' ]}";	//Este es el que ve el usuario
					$dat = Array();
					$dat[] = $data;

					$val .= json_encode( $dat )."\n";

			}
		}
		/*if( $hayNoAplica == false ){
			$data[ 'valor' ] = Array( "cod"=>'00', "des"=> 'NO APLICA' );	//Este es el dato a procesar en javascript
			$data[ 'usu' ] = "00-NO APLICA";	//Este es el que ve el usuario
			$dat = Array(); $dat[] = $data;
			$val .= json_encode( $dat )."\n";
		}*/

		return $val;
	}

function consultarMunicipios( $mun, $dep ){

		global $conex;




		$val = "";

		if( $dep == "" ){
			return $val;
		}

		$sql = "SELECT Codigo, Nombre
				FROM root_000006
				WHERE ( Nombre LIKE '%".utf8_decode($mun)."%' OR Codigo LIKE '%".utf8_decode($mun)."%' )
					AND codigo LIKE '".utf8_decode($dep)."%'
				ORDER BY Nombre
				LIMIT 25
				";

		$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );
		$hayNoAplica=false;
		if( $num > 0 ){

			for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){

					$rows[ 'Codigo' ] = trim( utf8_encode($rows[ 'Codigo' ]) );
					$rows[ 'Nombre' ] = trim( utf8_encode($rows[ 'Nombre' ] ) );
					if( $rows['Codigo'] == "00" || $rows['Codigo'] == "00999" )
						$hayNoAplica = true;
					//Creo el resultado como un json
					//Primero creo un array con los valores necesarios
					$data[ 'valor' ] = Array( "cod"=> $rows[ 'Codigo' ], "des"=> $rows[ 'Nombre' ] );	//Este es el dato a procesar en javascript
					$data[ 'usu' ] = "{$rows[ 'Codigo' ]}-{$rows[ 'Nombre' ]}";	//Este es el que ve el usuario
					$dat = Array();
					$dat[] = $data;

					$val .= json_encode( $dat )."\n";

			}
		}

		/*if( $hayNoAplica == false ){
			$data[ 'valor' ] = Array( "cod"=>'00999', "des"=> 'NO APLICA' );	//Este es el dato a procesar en javascript
			$data[ 'usu' ] = "00999-NO APLICA";	//Este es el que ve el usuario
			$dat = Array(); $dat[] = $data;
			$val .= json_encode( $dat )."\n";
		}*/

		return $val;
	}

function consultarBarrios( $bar, $mun ){

	global $conex;




	$val = "";

	if( $mun == "" )
		return $val;

	//Diagnostico
	$sql = "SELECT Barcod, Barmun,Bardes
			FROM root_000034
			WHERE (Bardes LIKE '%".utf8_encode($bar)."%' OR Barcod LIKE '%".utf8_encode($bar)."%' )
			AND ( barmun LIKE '".utf8_encode($mun)."%' OR barmun = '*' )
			ORDER BY Bardes
			LIMIT 30
			";

	$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	$hayNoAplica=false;
	if( $num > 0 ){

		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){

				$rows[ 'Barcod' ] = trim( utf8_encode($rows[ 'Barcod' ]) );
				$rows[ 'Bardes' ] = trim( utf8_encode($rows[ 'Bardes' ] ) );
				if( $rows[ 'Barcod' ] == "00" || $rows[ 'Barcod' ] == "00000" )
					$hayNoAplica= true;
				//Creo el resultado como un json
				//Primero creo un array con los valores necesarios
				$data[ 'valor' ] = Array( "cod"=> $rows[ 'Barcod' ], "des"=> $rows[ 'Bardes' ] );	//Este es el dato a procesar en javascript
				$data[ 'usu' ] = "{$rows[ 'Barcod' ]}-{$rows[ 'Bardes' ]}";	//Este es el que ve el usuario
				$dat = Array();
				$dat[] = $data;

				$val .= json_encode( $dat )."\n";
		}
	}

	// 2017-09-19 Se anexa siempre la opción sin dato
	$sqlGeneral = "SELECT Detval FROM root_000051 WHERE Detapl = 'barrioGenerico'";

	$res = mysql_query($sqlGeneral, $conex);
	if($res){
		$rowGeneral = mysql_fetch_assoc($res);
		$data[ 'valor' ] = Array( "cod"=> $rowGeneral["Detval"], "des"=> "SIN DATO" );
		$data[ 'usu' ] = $rowGeneral["Detval"] . "-" . "SIN DATO";
		$dat = Array(); $dat[] = $data;
		$val .= json_encode( $dat )."\n";
	}
	/////////////////////////////////////////////////////////////

	/*if( $hayNoAplica == false ){
		$data[ 'valor' ] = Array( "cod"=>'00000', "des"=> 'NO APLICA' );	//Este es el dato a procesar en javascript
		$data[ 'usu' ] = "00-NO APLICA";	//Este es el que ve el usuario
		$dat = Array(); $dat[] = $data;
		$val .= json_encode( $dat )."\n";
	}*/

	return $val;
}

function consultarOcupaciones( $ocu )
{

	global $conex;




	$val = "";
	$hayNoAplica = false;
	//Diagnostico
	$sql = "SELECT Codigo, Nombre
			FROM root_000008
			WHERE (Nombre LIKE '%".utf8_decode($ocu)."%' OR Codigo LIKE '%".utf8_decode($ocu)."%')
			and CIUO ='on'
			ORDER BY Nombre
			LIMIT 25
			";

	$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );

	if( $num > 0 ){
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
				$rows[ 'Codigo' ] = trim( utf8_encode($rows[ 'Codigo' ]) );
				$rows[ 'Nombre' ] = trim( utf8_encode($rows[ 'Nombre' ]) );
				if($rows['Codigo'] == "9999" )
					$hayNoAplica = true;
				//Creo el resultado como un json
				//Primero creo un array con los valores necesarios
				$data[ 'valor' ] = Array( "cod"=> $rows[ 'Codigo' ], "des"=> $rows[ 'Nombre' ] );	//Este es el dato a procesar en javascript
				$data[ 'usu' ] = "{$rows[ 'Codigo' ]}-{$rows[ 'Nombre' ]}";	//Este es el que ve el usuario
				$dat = Array();
				$dat[] = $data;

				$val .= json_encode( $dat )."\n";
		}
	}
	if( $hayNoAplica == false ){
		$data[ 'valor' ] = Array( "cod"=>'9999', "des"=> 'NO APLICA' );	//Este es el dato a procesar en javascript
		$data[ 'usu' ] = "9999-NO APLICA";	//Este es el que ve el usuario
		$dat = Array(); $dat[] = $data;
		$val .= json_encode( $dat )."\n";
	}
	return $val;
}

// $where="Empest = 'on' and Emptem != '09-EMPLEADOS CLINICA DEL SUR' and Emptem != '04-EMPLEADOS CLINICA' and Emptem != 'NO APLICA'";
// echo "<td class='fila2'>";$res1=consultaMaestros('000024','DISTINCT Empcod,Empnom',$where,'','Empnom','2');
function consultarAseguradoras( $aseg, $wbasedato ){

		global $conex;
		global $wemp_pmla;
		global $origenConsulta;




		$val = "";

		//2014-02-27 Las aseguradoras no pueden ser tipo soat
		$tipoSOAT = consultarAliasPorAplicacion($conex, $wemp_pmla, "tipoempresasoat" );
		$estadoEmpresa = ( empty($origenConsulta ) or $origenConsulta== "" ) ? "" : " AND Empest = 'on' ";
		$adicionarTarifaAnombre = consultarAplicacion2( $conex, $wemp_pmla, "adicionarTarifaAnombreEntidad" );


		//Diagnostico
	 	$sql = "SELECT Empcod, Empnom, Emptar, Tardes
				FROM ".$wbasedato."_000024, ".$wbasedato."_000025
				WHERE Empnom LIKE '%".utf8_decode($aseg)."%'
				  AND Emptem != '".$tipoSOAT."'
				  AND SUBSTRING_INDEX(Emptar,'-',1) = Tarcod
				  {$estadoEmpresa}
				ORDER BY Empnom
				LIMIT 25
				";
		$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );

		if( $num > 0 ){

			for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){

					$rows[ 'Empcod' ] = trim( utf8_encode($rows[ 'Empcod' ]) );
					$rows[ 'Empnom' ] = trim( utf8_encode($rows[ 'Empnom' ] ) );

					//Creo el resultado como un json
					//Primero creo un array con los valores necesarios
					if( $adicionarTarifaAnombre == "on" ){
						$descripcion  = $rows[ 'Empnom' ]."-->Tarifa:{$rows[ 'Emptar' ]}-{$rows[ 'Tardes' ]}";
						$descripcion2 = $rows[ 'Empcod' ]."-".$rows[ 'Empnom' ]." --> Tarifa:{$rows[ 'Emptar' ]}-{$rows[ 'Tardes' ]}";
					}else{
						$descripcion  = $rows[ 'Empnom' ];
						$descripcion2 = $rows[ 'Empcod' ]."-".$rows[ 'Empnom' ];
					}
					$data[ 'valor' ] = Array( "cod"=> $rows[ 'Empcod' ], "des"=> $descripcion );	//Este es el dato a procesar en javascript
					$data[ 'usu' ] = "{$descripcion2}";	//Este es el que ve el usuario

					$data[ 'tarifa' ] = "{$rows[ 'Emptar' ]}-{$rows[ 'Tardes' ]}";	//Este es el que ve el usuario
					$dat = Array();
					$dat[] = $data;

					$val .= json_encode( $dat )."\n";

			}
		}

		return $val;
	}


function consultarCUPS( $cups ){

		global $conex;




		$val = "";

		//Diagnostico
		$sql = "SELECT Codigo, Nombre
				FROM root_000012
				WHERE (Nombre LIKE '%".utf8_decode($cups)."%' or Codigo like '%".utf8_decode($cups)."%')
				ORDER BY Nombre
				LIMIT 25
				";

		$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );

		if( $num > 0 ){

			for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){

					$rows[ 'Codigo' ] = trim( utf8_encode($rows[ 'Codigo' ]) );
					$rows[ 'Nombre' ] = trim( utf8_encode($rows[ 'Nombre' ] ) );

					//Creo el resultado como un json
					//Primero creo un array con los valores necesarios
					$data[ 'valor' ] = Array( "cod"=> $rows[ 'Codigo' ], "des"=> $rows[ 'Nombre' ] );	//Este es el dato a procesar en javascript
					$data[ 'usu' ] = "{$rows[ 'Codigo' ]}-{$rows[ 'Nombre' ]}";	//Este es el que ve el usuario
					$dat = Array();
					$dat[] = $data;

					$val .= json_encode( $dat )."\n";

			}
		}

		return $val;
	}

function consultarImpresionesDiagnosticas( $imp, $edad, $sexo ){

		global $conex;




		$val = "";

		if( $edad == "NaN" )
			return "0";
		//Diagnostico
		// $sql = "SELECT Codigo, Descripcion
				// FROM root_000011
				// WHERE (Descripcion LIKE '%".utf8_decode($imp)."%' or Codigo like '%".utf8_decode($imp)."%')
				// ORDER BY Descripcion
				// ";

		$sql = "SELECT Codigo, Descripcion
				FROM root_000011
				WHERE (Descripcion LIKE '%".utf8_decode($imp)."%' or Codigo like '%".utf8_decode($imp)."%')
				AND Edad_i*1 <= $edad
				AND ( Edad_s*1 >= $edad OR Edad_s*1 = 0 )
				AND sexo IN ( '$sexo', 'A' )
				ORDER BY Descripcion
				LIMIT 25
				";

		$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );

		if( $num > 0 ){
			for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){

					$rows[ 'Codigo' ] = trim( utf8_encode($rows[ 'Codigo' ]) );
					$rows[ 'Descripcion' ] = trim( htmlentities($rows[ 'Descripcion' ] ) );

					//Creo el resultado como un json
					//Primero creo un array con los valores necesarios
					$data[ 'valor' ] = Array( "cod"=> $rows[ 'Codigo' ], "des"=> $rows[ 'Descripcion' ] );	//Este es el dato a procesar en javascript
					$data[ 'usu' ] = "{$rows[ 'Codigo' ]}-{$rows[ 'Descripcion' ]}";	//Este es el que ve el usuario
					$dat = Array();
					$dat[] = $data;

					$val .= json_encode( $dat )."\n";
			}
		}

		return $val;
}

function consultarTarifasParticular( $wtar ){
	global $conex, $wbasedato;
	$val = "";
	$query = "SELECT Tarcod Codigo, Tardes Descripcion
				FROM {$wbasedato}_000025
			   WHERE (Tarcod LIKE '%".utf8_decode($wtar)."%' or Tardes LIKE '%".utf8_decode($wtar)."%')
			     AND Tarest='on'
			   ORDER BY Tardes";
	$res = mysql_query($query,$conex) or die(mysql_errno()."error en linea 2663".mysql_error()." --------> ".$query);
	$num = mysql_num_rows($res);
	if( $num > 0 ){
			for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){

					$rows[ 'Codigo' ] = trim( utf8_encode($rows[ 'Codigo' ]) );
					$rows[ 'Descripcion' ] = trim( htmlentities($rows[ 'Descripcion' ] ) );

					//Creo el resultado como un json
					//Primero creo un array con los valores necesarios
					$data[ 'valor' ] = Array( "cod"=> $rows[ 'Codigo' ], "des"=> $rows[ 'Descripcion' ] );	//Este es el dato a procesar en javascript
					$data[ 'usu' ] = "{$rows[ 'Codigo' ]}-{$rows[ 'Descripcion' ]}";	//Este es el que ve el usuario
					$dat = Array();
					$dat[] = $data;

					$val .= json_encode( $dat )."\n";
			}
		}
	return( $val );
}

 /**********************************************************************************
* Crea un select con el id y name
**********************************************************************************/
function crearSelectHTMLAcc( $res, $id, $name, $style = "", $opcionDefecto = "" ){

       echo "<SELECT id='$id' name='$name' $style>";
       echo "<option value=''>Seleccione...</option>";

       $num = mysql_num_rows( $res );

       if( $num > 0 ){

               while( $rows = mysql_fetch_assoc( $res, MYSQL_ASSOC ) ){

                       $value = "";
                       $des = "";

                       $i = 0;
                       foreach( $rows  as $key => $val ){

                               if( $i == 0 ){
                                       $value = $val;
                               }
                               else{
                                       $des .= "-".$val;
                               }

                               $i++;
                       }

					  $alfanumerico = "";
					  $docXhis      = "";
					  $selected     = ( $value == $opcionDefecto ) ? " selected " : "";
					  $defecto      = ( $value == $opcionDefecto ) ? " defecto='on' " : "";


                       if( trim($rows['docigualhis'] != "" ) ){
                       		$docXhis  = " docXhis='{$rows['docigualhis']}' ";
                       }

                       if( trim( $rows['alfanumerico'] ) != "" ){
                       		$alfanumerico = " alfanumerico='{$rows['alfanumerico']}' ";
                       		echo "<option $selected $defecto value='{$value}' {$alfanumerico} {$docXhis}>".$rows['Descripcion']."</option>";
                       }else{
                       		echo "<option $selected $defecto value='{$value}' {$alfanumerico}>".substr( $des, 1 )."</option>";
                       }
               }
       }

       echo "</SELECT>";
}

//-- mirar
function crearSelectHTMLAccEspecial( $res, $id, $name, $style = "", $opcionDefecto = "" , $cambio, $actual, $arrayRelaciones, $mostrarOriginal ){

       $selectBase  = "<SELECT id='$id' name='$name' $style  onchange='".$cambio."'>";
       $selectBase2  = "<SELECT id='{$id}_original' style='display:none'>";
       $selectBase .= "<option value='' >Seleccione...</option>";
       $selectBase2 .= "<option value='' >Seleccione...</option>";

       $num = mysql_num_rows( $res );

       if( $num > 0 ){

               while( $rows = mysql_fetch_assoc( $res, MYSQL_ASSOC ) ){
                       $value = "";
                       $des = "";

                       $i = 0;
                       foreach( $rows  as $key => $val ){

                               if( $i == 0 ){
                                       $value = $val;
                               }
                               else{
                                       $des .= "-".$val;
                               }

                               $i++;
                       }

					  $alfanumerico = "";
					  $docXhis      = "";
					  $selected     = ( $value == $opcionDefecto ) ? " selected " : "";
					  $defecto      = ( $value == $opcionDefecto ) ? " defecto='on' " : "";


                       if( trim($rows['docigualhis'] != "" ) ){
                       		$docXhis  = " docXhis='{$rows['docigualhis']}' ";
                       }
                       $aux = array();
               		   if( count($arrayRelaciones['padre'][$actual."-".$value]['hijos']) > 0 ){
	               		   foreach( $arrayRelaciones['padre'][$actual."-".$value]['hijos'] as $key => $data){
	               		   		$auxNietos = array();
	               		   		if( count( $arrayRelaciones['padre'][$actual."-".$value]['hijos'][$key]['nietos'] ) > 0 ){
		               		   		foreach( $arrayRelaciones['padre'][$actual."-".$value]['hijos'][$key]['nietos'] as $keyN => $dataN ){
		               		   			array_push( $auxNietos, $keyN );
		               		   		}
		               		   		$nietos = implode("_", $auxNietos );
		               		   	}
		               		   	array_push( $aux, $key."|".$nietos );
	               		   }
	               		}
               		   $hijos = implode(",", $aux);
                       if( trim( $rows['alfanumerico'] ) != "" ){
                       		$alfanumerico = " alfanumerico='{$rows['alfanumerico']}' ";
                       		$optionSelectBase .= "<option $selected $defecto codigoRelacion='{$actual}-{$value}' hijos='{$hijos}' value='{$value}' {$alfanumerico} {$docXhis}>".$rows['Descripcion']."</option>";
                       		$selectBase2 .= "<option $selected $defecto codigoRelacion='{$actual}-{$value}' hijos='{$hijos}' value='{$value}' {$alfanumerico} {$docXhis}>".$rows['Descripcion']."</option>";
                       }else{
                       		$optionSelectBase .= "<option $selected $defecto codigoRelacion='{$actual}-{$value}' hijos='{$hijos}' value='{$value}' {$alfanumerico}>".substr( $des, 1 )."</option>";
                       		$selectBase2 .= "<option $selected $defecto codigoRelacion='{$actual}-{$value}' hijos='{$hijos}' value='{$value}' {$alfanumerico}>".substr( $des, 1 )."</option>";
                       }
               }
       }
       if( $mostrarOriginal )
       		$selectBase .= $optionSelectBase;
       $selectBase .= "</SELECT>";
       $selectBase2 .= "</SELECT>";

       echo $selectBase2.$selectBase;
}

function construirArregloRelaciones(){

	global $wbasedato, $conex;
	$relacionPadresHijos = array();
	$query = " SELECT Rctcst tipoCobertura, Rctcsc codigoCobertura, Rcttat tipoAfiliacion, Rcttac codigoAfiliacion,
	                  Rctpct tipoPagoCompartido, Rctpcc codigoPagoCompartido
	             FROM {$wbasedato}_000316
	            WHERE Rctest = 'on'";
	$rs    = mysql_query( $query, $conex );

	while( $row = mysql_fetch_assoc( $rs ) ){
		$relacionPadresHijos['padre'][$row['tipoCobertura']."-".$row['codigoCobertura']]['hijos'][$row['tipoAfiliacion']."-".$row['codigoAfiliacion']]['nietos'][$row['tipoPagoCompartido']."-".$row['codigoPagoCompartido']] = "" ;
	}
	return( $relacionPadresHijos );

}

function crearSelectHTMLAcc777( $res, $id, $name, $style = "", $resp='' ){

       $cadenas= "<SELECT id='$id' name='$name' $style>";
       $cadenas.=  "<option value=''>Seleccione...</option>";

       $num = mysql_num_rows( $res );

       if( $num > 0 ){

               while( $rows = mysql_fetch_array( $res, MYSQL_ASSOC ) ){

                       $value = "";
                       $des = "";

                       $i = 0;
                       foreach( $rows  as $key => $val ){

                               if( $i == 0 ){
                                       $value = $val;
                               }
                               else{
                                       $des .= "-".$val;
                               }

                               $i++;
                       }

                       $cadenas.= "<option value='{$value}'>".substr( $des, 1 )."</option>";
               }
       }

       $cadenas.= "</SELECT>";

	   if( !empty($resp) )
		{
			echo $cadenas;
		}
		else
		{
			return $cadenas;
		}

}

function consultaNombrePais($codPais)
{
	global $conex;
	//pais de nacimiento
    $sql1="select *
		from root_000077
		where Paicod = '".$codPais."'
		and Paiest='on'";
	$res1 = mysql_query( $sql1, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando el pais de nacimiento ".mysql_errno()." - Error en el query $sql1 - ".mysql_error() ) );

	return $res1;
}


function buscarDatosNoAplicaExtranjeros(){

	global $conex, $wemp_pmla;
	$noAplica = array();
	//2017-09-19 El valor para el barrio sin dato se tomará de la configuración de la tabla root_000051 Detapl = BarrioGenerico
	$query = "SELECT Detval as codigo
			  FROM root_000051
			  WHERE Detapl = 'BarrioGenerico'
			  AND Detemp = '".$wemp_pmla."' 	";

	/*$query = "SELECT Barcod codigo
			    FROM root_000034
			   WHERE Bardes = 'SIN DATO'
			     AND Barcod = '999'";*/
	$rs    = mysql_query( $query, $conex );
	$row   = mysql_fetch_assoc( $rs );
	$noAplica['barrio'] = $row['codigo'];

	$query = "SELECT codigo
			    FROM root_000002
			   WHERE Descripcion = 'NO APLICA'";
	$rs    = mysql_query( $query, $conex );
	$row   = mysql_fetch_assoc( $rs );
	$noAplica['departamento'] = $row['codigo'];

	$query = "SELECT codigo
				FROM root_000006
			   WHERE Nombre = 'NO APLICA'";
	$rs    = mysql_query( $query, $conex );
	$row   = mysql_fetch_assoc( $rs );
	$noAplica['municipio'] = $row['codigo'];

	return($noAplica);
}

function consultaNombreDepartamento($codDep)
{
	global $conex;
	//departamento de nacimiento
			  $sql1="select *
				from root_000002
				where Codigo = '".$codDep."'
				";
			$res1 = mysql_query( $sql1, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando el departamento de nacimiento ".mysql_errno()." - Error en el query $sql1 - ".mysql_error() ) );

	return $res1;
}

function consultaNombreMunicipio($codMun)
{
	global $conex;
	//departamento de nacimiento
			 $sql1="select *
				from root_000006
				where Codigo = '".$codMun."'
				";
			$res1 = mysql_query( $sql1, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando el departamento de nacimiento ".mysql_errno()." - Error en el query $sql1 - ".mysql_error() ) );

	return $res1;
}

function consultaNombreBarrio($codBar,$codMun)
{
	global $conex, $wemp_pmla;
	$barGenerico = "";

	//barrio donde vive
	$sql1="select *
			from root_000034
			where Barcod = '".$codBar."'";
	//if (!empty($codMun) && $codBar != "00" || $codBar != "00000")
	//if (!empty($codMun) && $codBar != "999") 2014-09-19
	if (!empty($codMun))
	{
		$sql1.="and Barmun = '".$codMun."'";
	}

	$res1 = mysql_query( $sql1, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando el departamento de nacimiento ".mysql_errno()." - Error en el query $sql1 - ".mysql_error() ) );

	if(!$res){
		//2017-09-19 Validamos si tiene el valor generico
		$sql = "SELECT Detval as Barcod, 'SIN DATO' as Bardes FROM root_000051 WHERE Detapl = 'barrioGenerico' AND Detemp = '" . $wemp_pmla . "'";
		$res2 = mysql_query($sql, $conex);
		$res3 = mysql_query($sql, $conex);
		$row = mysql_fetch_array($res2);

		if(isset($row["Barcod"]) && $row["Barcod"] == $codBar){
			$barGenerico = $res3;
		}
	}

	return $barGenerico != "" ? $barGenerico : $res1;
}

function consultaNombreOcupacion($codOcu)
{
	global $conex;
	//departamento de nacimiento
			 $sql1="select *
				from root_000008
				where Codigo = '".$codOcu."'
				and CIUO ='on'
				";
			$res1 = mysql_query( $sql1, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando el departamento de nacimiento ".mysql_errno()." - Error en el query $sql1 - ".mysql_error() ) );

	return $res1;
}

function consultaNombreAseguradora($codAse)
{
	global $conex;
	global $wbasedato;
	//consultar codigo aseguradora
			 $sql1="SELECT Empcod,Empnit,Empnom, Emptar, Tardes
				      FROM ".$wbasedato."_000024, ".$wbasedato."_000025
				     WHERE Empcod = '".$codAse."'
				       AND Tarcod = SUBSTRING_INDEX(Emptar,'-',1)
				";
			$res1 = mysql_query( $sql1, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando el departamento de nacimiento ".mysql_errno()." - Error en el query $sql1 - ".mysql_error() ) );

	return $res1;
}


function consultaNombreCups($codCups)
{
	global $conex;

	//consultar codigo cups
			 $sql1="select Codigo, Nombre
				FROM root_000012
				where Codigo = '".$codCups."'
				";
			$res1 = mysql_query( $sql1, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando el departamento de nacimiento ".mysql_errno()." - Error en el query $sql1 - ".mysql_error() ) );

	return $res1;
}

function consultaNombreImpDiag($codImpDiag)
{
	global $conex;

	//consultar codigo impresion diagnostica
			 $sql1="select Codigo, Descripcion
				FROM root_000011
				where Codigo = '".$codImpDiag."'
				";
			$res1 = mysql_query( $sql1, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando el departamento de nacimiento ".mysql_errno()." - Error en el query $sql1 - ".mysql_error() ) );

	return $res1;
}

function consultaNombreTarifa($codTarifa)
{
	global $conex, $wbasedato;

	//consultar codigo impresion diagnostica
			 $sql1="SELECT Tarcod Codigo, Tardes Descripcion
					  FROM {$wbasedato}_000025
					 WHERE Tarcod = '".$codTarifa."'";
			$res1 = mysql_query( $sql1, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando NOMBRE DE TARIFA ".mysql_errno()." - Error en el query $sql1 - ".mysql_error() ) );

	return $res1;
}




/************************************************************************************************
 * Crea un array de datos que hace los siguiente.
 *
 * Toma todas las variables enviadas por Post, y las convierte en un array. Este array puede ser
 * procesado por las funciones crearStringInsert y crearStringInsert
 *
 * Explicacion:
 * Toma todas las variables enviadas por Post que comiencen con $prefijoHtml, creando un array
 * donde su clave o posicion comiencen con $prefijoBD concatenado con $longitud de caracteres
 * despues del $prefijoHtml y dandole como valor el valor de la variable enviada por Post
 *
 * Ejemplo:
 *
 * La variable Post es: indpersonas = 'Armando Calle'
 * Ejecutando la funcion: $a = crearArrayDatos( 'movhos', 'Per', 'ind', 3 );
 *
 * El array que retorna la función es:
 *						$a[ 'Perper' ] = 'Armando Calle'
 *						$a[ 'Medico' ] = 'movhos'
 *						$a[ 'Fecha_data' ] = '2013-05-22'
 *						$a[ 'Hora_data' ] = '05:30:24'
 *						$a[ 'Seguridad' ] = 'C-movhos'
 ************************************************************************************************/
function crearArrayDatos( $wbasedato, $prefijoBD, $prefijoHtml, $longitud ){

	$val = Array();

	$crearDatosExtras = false;

	$lenHtml = strlen( $prefijoHtml );

	foreach( $_POST as $keyPost => $valuePost ){

		if( substr( $keyPost, 0, $lenHtml ) == $prefijoHtml ){

			if( substr( $keyPost, $lenHtml, $longitud ) != 'id' ){
				$val[ $prefijoBD.substr( $keyPost, $lenHtml, $longitud ) ] = utf8_decode( $valuePost );
			}
			else{
				$val[ substr( $keyPost, $lenHtml, $longitud ) ] = utf8_decode( $valuePost );
			}
			$crearDatosExtras = true;
		}
	}

	//Estos campos se llenan automáticamente y toda tabla debe tener esots campos
	if( $crearDatosExtras ){
		global $user;
		$user2 = explode("-",$user);
		( isset($user2[1]) )? $user2 = $user2[1] : $user2 = $user2[0];
		if( $user2 == "" )
			$user2=$wbasedato;

		$val[ 'Medico' ] = $wbasedato;
		$val[ 'Fecha_data' ] = date( "Y-m-d" );
		$val[ 'Hora_data' ] = date( "H:i:s" );
		$val[ 'Seguridad' ] = "C-$user2";
	}

	return $val;
}

/***************************************************************************************
 * inserta los datos a la tabla
 *
 * $datos	Array que tiene como clave el nombre del campo y valor el valor a insertar
 * $tabla 	Nombre de la tabla a la que se va a insertar los datos
 ***************************************************************************************/
function crearStringInsert( $tabla, $datos ){

	$stPartInsert = "";
	$stPartValues = "";

	foreach( $datos as $keyDatos => $valueDatos ){
		if( trim( $valueDatos ) == "" )
			continue;
		$stPartInsert .= ",$keyDatos";
		$stPartValues .= ",'$valueDatos'";
	}

	$stPartInsert = "INSERT INTO $tabla(".substr( $stPartInsert, 1 ).")";	//quito la coma inicial
	$stPartValues = " VALUES (".substr( $stPartValues, 1 ).")";

	return $stPartInsert.$stPartValues;
}

/***************************************************************************************
 * Crea un string que corresponde a un UPDATE valido
 *
 * $datos	Array que tiene como clave el nombre del campo y valor el valor a insertar
 * $tabla 	Nombre de la tabla a la que se va a insertar los datos
 ***************************************************************************************/
function crearStringUpdate( $tabla, $datos ){

	$stPartInsert = "";
	$stPartValues = "";

	//campos que no se actualizan
	$prohibidos[ "Medico" ] = true;
	$prohibidos[ "Fecha_data" ] = true;
	$prohibidos[ "Hora_data" ] = true;
	$prohibidos[ "Seguridad" ] = true;
	$prohibidos[ "id" ] = true;

	foreach( $datos as $keyDatos => $valueDatos ){

		if( !isset( $prohibidos[ $keyDatos ] ) ){
			$stPartInsert .= ",$keyDatos = '$valueDatos' ";
		}
	}

	$stPartInsert = "UPDATE $tabla SET ".substr( $stPartInsert, 1 );	//quito la coma inicial
	$stPartValues = " WHERE id = '{$datos[ 'id' ]}'";

	return $stPartInsert.$stPartValues;

	//UPDATE  `matrix`.`movhos_000138` SET  `Dprest` =  'off' WHERE  `movhos_000138`.`id` =82;
}

/**************************************************************************************************
 * Crea o actualiza los registros de un accidente de tránsito
 **************************************************************************************************/
function guardarAccidentes( $his, $ing ){

	global $wbasedato;
	global $conex;
	global $codAseR2;
	global $tipoEmpR3;
	global $codAseR3;
	global $nomAseR3;
	global $accidente_previo;




	$data = Array(
			"error" => 0,
			"html" => "",
			"data" => "",
			"mensaje" => ""
		);

	if( !empty( $his ) && !empty( $ing ) ){

		//Consulto si no existe el accidente de transito para proceder a guardar los datos
		$sql = "SELECT
					*
				FROM
					{$wbasedato}_000148
				WHERE
					acchis = '$his'
					AND accing = '$ing'
				";

		$res = mysql_query( $sql, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query es este el que genera error $sql  - ".mysql_error() ) );

		if( $res ){

			$num = mysql_num_rows( $res );

			//Si no se encontraron los datos, significa que es un registro nuevo de accidentes de transito
			if( $num == 0 ){

				$datosTabla = crearArrayDatos( $wbasedato, "Acc", "dat_Acc", 3 );

				$datosTabla[ 'Acchis' ] = $his;
				$datosTabla[ 'Accing' ] = $ing;
				$datosTabla[ 'Accest' ] = 'on';


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
				$accPrevioReal = "";
				if( isset($accidente_previo) && $accidente_previo != "" ){//2016-27-12

					$ingaux = $ing*1;
					$qaccp = " SELECT  Accing, accrei
								 FROM {$wbasedato}_000148
								WHERE acchis = '{$his}'
								  AND accing*1 > {$ingaux}
							   HAVING ( MIN( Accing*1 ) ) ";

					$rsaccp = mysql_query( $qaccp, $conex );
					$numacc = mysql_num_rows($rsaccp);
					if( $numacc > 0 ){//hay un reingreso mayor al que se está registrando

						$rowacc = mysql_fetch_assoc( $rsaccp );
						$accPrevioReal =  $rowacc['accrei'];
						$qaccp2 = " UPDATE {$wbasedato}_000148
									  SET accrei = '{$ing}'
									WHERE acchis = '{$his}'
									  AND accing = '{$rowacc['Accing']}'";
						$rsaccp2 = mysql_query( $qaccp2, $conex ) or die(mysql_error());
					}else{
						$accPrevioReal = $accidente_previo;
					}
					$datosTabla[ 'Accrei' ] = $accPrevioReal;
				}

				$sqlInsert = crearStringInsert( $wbasedato."_000148", $datosTabla );

				$res1 = mysql_query( $sqlInsert, $conex ) or die( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() ) );

				if( $res1 ){
					if( mysql_affected_rows() > 0 ){
						//$data[ "mensaje" ] = utf8_encode( "Se guardo correctamente" );
					}
				}
				else{
					$data[ "error" ] = 1;
				}

				//--> aca se actualizan los otros accidentes asociados. 2017-01-04
				while( $accPrevioReal != "" ){
					//--> se mueve por los ingresos anteriores que están asociados al mismo accidente de tránsito 2017-01-04
					$qaccp = " SELECT  Accing, accrei, id
								 FROM {$wbasedato}_000148
								WHERE acchis = '{$his}'
								  AND accing = '{$accPrevioReal}'";
					$rsaccp = mysql_query( $qaccp, $conex );
					$numacc = mysql_num_rows($rsaccp);
					if( $numacc > 0 ){//hay un reingreso mayor al que se está registrando
						$rowacc = mysql_fetch_assoc( $rsaccp );
						/*$datosTabla[ 'Acchis' ] = $his;
						$datosTabla[ 'Accing' ] = $accPrevioReal;*/
						$datosTabla[ 'Accrei' ] = $rowacc['accrei'];
						$datosTabla[ 'id' ]     = $rowacc['id'];
						unset( $datosTabla[ 'Accres' ] );
						unset( $datosTabla[ 'Acctar' ] );
						unset( $datosTabla[ 'Accre2' ] );
						unset( $datosTabla[ 'Acctop' ] );
						unset( $datosTabla[ 'Accvsm' ] );
						unset( $datosTabla[ 'Accemp' ] );
						unset( $datosTabla[ 'Accre3' ] );
						unset( $datosTabla[ 'Accno3' ] );
						unset( $datosTabla[ 'Acchis' ] );
						unset( $datosTabla[ 'Accing' ] );

						$sqlUpdate = crearStringUpdate( $wbasedato."_000148", $datosTabla );
						$res1 = mysql_query( $sqlUpdate, $conex ) or die( $data[ 'mensaje' ] = mysql_errno()." - Error en el query 3333 $sqlUpdate - ".mysql_error() );
						$accPrevioReal          = $rowacc['accrei'];
					}else{
						$accPrevioReal = "";
					}
				}

				/*SE PONE ESTADO OFF EN EL ACCIDENTE DE PREADMISION 2014-07-22*/
				global $pac_tdoselTipoDoc;
				global $pac_doctxtNumDoc;
				$sql = "UPDATE ".$wbasedato."_000227
						   SET Accest = 'off'
						 WHERE Acctdo = '".$pac_tdoselTipoDoc."'
						   AND Accdoc = '".$pac_doctxtNumDoc."' ";
				$resCancPre = mysql_query( $sql, $conex )or die(mysql_error());
				if( !$resCancPre ){
					//$data[ "error" ] = 1;
				}
			}
			else{
				$rows = mysql_fetch_array( $res );

				//Si se encontraron datos, significa que es una actualización de registro de accidentes de tránsito
				$datosTabla = crearArrayDatos( $wbasedato, "Acc", "dat_Acc", 3 );

				unset( $datosTabla['Acchis'] );
				unset( $datosTabla['Accing'] );

				$datosTabla[ 'Accest' ] = 'on';
				$datosTabla[ 'id' ] = $rows[ 'id' ];

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
				$accPrevioReal = "";
				if( isset($accidente_previo) && $accidente_previo != "" && $ing != $accidente_previo){//2016-27-12

					$ingaux = $ing*1;
					$qaccp = " SELECT  Accing, accrei
								 FROM {$wbasedato}_000148
								WHERE acchis = '{$his}'
								  AND accing*1 > {$ingaux}
							   HAVING ( MIN( Accing*1 ) ) ";
					$rsaccp = mysql_query( $qaccp, $conex );
					$numacc = mysql_num_rows($rsaccp);
					if( $numacc > 0 ){//hay un reingreso mayor al que se está registrando
						$rowacc = mysql_fetch_assoc( $rsaccp );
						$accPrevioReal =  $rowacc['accrei'];
						$qaccp2 = " UPDATE {$wbasedato}_000148
									  SET accrei = '{$ing}'
									WHERE acchis = '{$his}'
									  AND accing = '{$rowacc['Accing']}'";
						$rsaccp2 = mysql_query( $qaccp2, $conex ) or die(mysql_error());
					}else{
						$accPrevioReal = $accidente_previo;
					}

					$datosTabla[ 'Accrei' ] = $accPrevioReal;
				}

				$sqlUpdate = crearStringUpdate( $wbasedato."_000148", $datosTabla );

				$res1 = mysql_query( $sqlUpdate, $conex ) or die( $data[ 'mensaje' ] = mysql_errno()." - Error en el query 22222 $sqlUpdate - ".mysql_error() );

				//--> aca se actualizan los otros accidentes asociados. 2017-01-04
				while( $accPrevioReal != "" ){
					//--> se mueve por los ingresos anteriores que están asociados al mismo accidente de tránsito 2017-01-04
					$qaccp = " SELECT  Accing, accrei, id
								 FROM {$wbasedato}_000148
								WHERE acchis = '{$his}'
								  AND accing = '{$accPrevioReal}'";
					$rsaccp = mysql_query( $qaccp, $conex );
					$numacc = mysql_num_rows($rsaccp);
					if( $numacc > 0 ){//hay un reingreso mayor al que se está registrando
						$rowacc = mysql_fetch_assoc( $rsaccp );
						/*$datosTabla[ 'Acchis' ] = $his;
						$datosTabla[ 'Accing' ] = $accPrevioReal;*/
						$datosTabla[ 'Accrei' ] = $rowacc['accrei'];
						$datosTabla[ 'id' ]     = $rowacc['id'];
						unset( $datosTabla[ 'Accres' ] );
						unset( $datosTabla[ 'Acctar' ] );
						unset( $datosTabla[ 'Accre2' ] );
						unset( $datosTabla[ 'Acctop' ] );
						unset( $datosTabla[ 'Accvsm' ] );
						unset( $datosTabla[ 'Accemp' ] );
						unset( $datosTabla[ 'Accre3' ] );
						unset( $datosTabla[ 'Accno3' ] );
						unset( $datosTabla[ 'Acchis' ] );
						unset( $datosTabla[ 'Accing' ] );

						$sqlUpdate = crearStringUpdate( $wbasedato."_000148", $datosTabla );
						$res1 = mysql_query( $sqlUpdate, $conex ) or die( $data[ 'mensaje' ] = mysql_errno()." - Error en el query 111 $sqlUpdate - ".mysql_error() );
						$accPrevioReal          = $rowacc['accrei'];
					}else{
						$accPrevioReal = "";
					}
				}

				if( $res1 ){
					if( mysql_affected_rows() > 0 ){
						$data[ "mensaje" ] = utf8_encode( "Se actualizo correctamente" );
					}
				}
				else{
					$data[ "error" ] = 1;
				}
			}
		}
		else{
			$data[ 'error' ] = 1;
		}
	}
	else{
		$data[ 'mensaje' ] = utf8_encode( "No se digito historia o ingreso" );
		$data[ 'error' ] = 1;
	}

	return $data;
}

/******************************************************************************************
 * Crea o actualiza un registro de eventos catastroficos a la base de datos
 ******************************************************************************************/
function guardarEventos( $his, $ing ){

	global $wbasedato;
	global $conex;




	$data = Array(
			"error" => 0,
			"html" => "",
			"data" => "",
			"mensaje" => ""
		);

	if( !empty( $his ) && !empty( $ing ) ){

		//Consulto si no existe el evento catastrofico para proceder a guardar los datos
		$sql = "SELECT
					*
				FROM
					{$wbasedato}_000150
				WHERE
					evnhis = '$his'
					AND evning = '$ing'
				";

		$res = mysql_query( $sql, $conex ) or ( $data[ 'error' ] = utf8_encode( mysql_errno()." - Error en el query $sql - ".mysql_error() ) );

		if( $res ){

			$num = mysql_num_rows( $res );

			//Si no se encontraron los datos, significa que es un registro nuevo de eventos catastroficos
			if( $num == 0 )
			{
				global $cod;
				if (empty($cod))
				{
					//Consulto el código del evento, es un consecutivo
					$sqlCon="select max(Devcod) as Devcod
							from ".$wbasedato."_000149
							where Devest = 'on'";
					$resCon=mysql_query($sqlCon,$conex);
					if ($resCon)
					{
						$numCon= mysql_num_rows($resCon);
						if ($numCon > 0)
						{
							$rowsCon=mysql_fetch_array($resCon);
							$cod=$rowsCon['Devcod']+1;

						}
						else
						{
							$cod = 1;
						}
					}
					else
					{
						$data['error']=1;
						$data['mensaje']="Error consultando el consecutivo de eventos catastroficos";
					}
				}


				//Creo el encabezado
				$datosEnc[ "Evnhis" ] = $his;
				$datosEnc[ "Evning" ] = $ing;
				$datosEnc[ "Evncod" ] = $cod;			//Consulto el código del evento, es un consecutivo
				$datosEnc[ "Evnest" ] = "on";
				$datosEnc[ "Medico" ] = $wbasedato;
				$datosEnc[ "Fecha_data" ] = date( "Y-m-d" );
				$datosEnc[ "Hora_data" ] = date( "H:i:s" );
				$datosEnc[ "Seguridad" ] = "C-".$wbasedato;

				$sqlInsert = crearStringInsert( $wbasedato."_000150", $datosEnc );

				$resEnc = mysql_query( $sqlInsert, $conex ) or die( $data[ 'error' ] = utf8_encode( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() ) );

				if( mysql_affected_rows() > 0 ){	//si inserto el encabezado

					// $cod = mysql_insert_id();

					$datosTabla = crearArrayDatos( $wbasedato, "Dev", "det_Cat", 3 );

					$datosTabla[ "Devcod" ] = $cod;
					$datosTabla[ "Devest" ] = "on";

					$sqlInsert = crearStringInsert( $wbasedato."_000149", $datosTabla );

					$res1 = mysql_query( $sqlInsert, $conex ) or die( $data[ 'error' ] = utf8_encode( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() ) );

					if( $res1 ){
						if( mysql_affected_rows() > 0 ){
							$data[ "mensaje" ] = utf8_encode( "Se registró correctamente " );

							// $datosEnc[ "Evncod" ] = $cod;
							// $datosEnc[ "id" ] = $cod;
							// $sqlUptEnc = crearStringUpdate( $wbasedato."_000150", $datosEnc );

							// $resUptEnc = mysql_query( $sqlUptEnc, $conex ) or ( $data[ 'error' ] = utf8_encode( mysql_errno()." - Error en el query $sqlUptEnc - ".mysql_error() ) );
						}
					}
				}
				else{
					$data[ "error" ] = 1;
					$data[ "mensaje" ] = utf8_encode( "No se creo correctamente el encabezado de los eventos catastroficos" );
				}


			}
			else{ //para actualizar el evento

				$rowsEnc = mysql_fetch_array( $res );

				//se mira si ese codigo esta en la 149
				$sql = "SELECT
							*
						FROM
							{$wbasedato}_000149
						WHERE
							devcod = '".$rowsEnc[ 'Evncod' ]."'
						";

				$resDet = mysql_query( $sql, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query $sql - ".mysql_error() ) );

				if( $resDet ){

					$rowsDet = mysql_fetch_array( $resDet );

					//Si se encontraron datos, significa que es una actualización de registro de evento catastrofico
					$datosTabla = crearArrayDatos( $wbasedato, "Dev", "det_Cat", 3 );

					$datosTabla[ 'id' ] = $rowsDet[ 'id' ];

					$sqlUpdate = crearStringUpdate( $wbasedato."_000149", $datosTabla );

					$res1 = mysql_query( $sqlUpdate, $conex ) or die( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query $sqlUpdate - ".mysql_error() ) );

					if( $res1 ){
						if( mysql_affected_rows() > 0 ){
							$data[ "mensaje" ] = utf8_encode( "Se actualizo correctamente" );
							//revizar
							$datosEnc[ "Evncod" ] = $rowsEnc[ 'Evncod' ];
							$datosEnc[ "id" ] = $rowsEnc[ 'Evncod' ];
							$datosEnc[ "Evnest" ] = "on";
							$sqlUptEnc = crearStringUpdate( $wbasedato."_000150", $datosEnc );

							$resUptEnc = mysql_query( $sqlUptEnc, $conex ) or die( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query $sqlUptEnc - ".mysql_error() ) );
							if( !$resUptEnc )
							{
								$data['error']=1;
							}
						}
					}
					else
					{
						$data['error']=1;
					}
				}
				else
				{
					$data['error']=1;
				}
			}
		}
	}
	else{
		$data[ 'mensaje' ] = "No se digito historia o ingreso";
		$data['error']=1;
	}

	return $data;
}

/***********************************************************************************************
	 * Consulta del codigo de matrix viejo en el maestro de selecciones de la solución correspondiente.
	 * El maestro de selecciones es la tabla 000105
	 ***********************************************************************************************/
function consultarCodigoAnteriorMatrix( $tip, $cod )
{

	global $conex;
	global $wbasedato;

		$val = false;

		$sql = "SELECT *
				FROM ".$wbasedato."_000105
				WHERE seltip = '".$tip."'
				AND FIND_IN_SET( '".$cod."',selmat ) > 0
				AND selest = 'on'
				";
				//AND selmat like '%".$cod."%'
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );

		if( $num > 0 ){
			if( $rows = mysql_fetch_array( $res ) ){
				$val = $rows[ 'Selcod' ];
			}
		}

		return $val;
	}

/**************************************************************************************************
 * Consulta los accidentes de tránsito para un paciente según la historia e ingreso
 **************************************************************************************************/
function consultarAccidentesAlmacenados( $his, $ing, &$data )
{
	global $wbasedato;
	global $conex;

	$i = count( $data[ 'infoing' ] ) - 1;

	$sql = "SELECT
				Acchis, Accing, Acccon, Accdir, Accdtd, Accfec, Acchor, Accdep, Accmun, Acczon, Accdes, Accase, Accmar, Accpla, Acctse, Acccas,
				Accpol, Accvfi, Accvff, Accaut, Acccep, Accap1, Accap2, Accno1, Accno2, Accnid, Acctid, Accpdi, Accpdd, Accpdp, Accpmn, Acctel,
				Accca1, Accca2, Acccn1, Acccn2, Acccni, Acccti, Acccdi, Acccdd, Acccdp, Acccmn, Accctl, Accrei
			FROM
				{$wbasedato}_000148
			WHERE
				acchis = '$his'
				AND accing = '$ing'
			";

	$res = mysql_query( $sql, $conex ) or ( $data[ 'error' ] = utf8_encode( "Error consultando la tabla ".$wbasedato."000100 ".mysql_errno()." - Error en el query $sql - ".mysql_error() ) );

	if ($res)
	{

		$num=mysql_num_rows($res);

		if ($num>0)
		{
			if( $rows=mysql_fetch_array($res, MYSQL_ASSOC ) )
			{
				$codigoReponsable = $rows['Acccas'];

				foreach( $rows as $key => $value )
				{
					//se guarda en data con el prefijo pac_ y empezando en la posicion 3 hasta el final
					$data[ 'infoing' ][$i][ "dat_Acc".substr( $key, 3 ) ] = utf8_encode( $value );
				}

				//Consulto el nombre del departamento en donde ocurrio el accidente
				$res = consultaNombreDepartamento( $rows[ 'Accdep' ] );
				$num = mysql_num_rows( $res );
				if( $rowdp = mysql_fetch_array( $res ) ){
					$dep = $rowdp[ 'Descripcion' ];
				}
				else{
					$dep = '';
				}
				$data[ 'infoing' ][$i][ "Accdep" ] = $dep;




				//Consulto el nombre del departamento en donde ocurrio el accidente
				$res = consultaNombreDepartamento( $rows[ 'Accpdp' ] );
				$num = mysql_num_rows( $res );
				if( $rowdp = mysql_fetch_array( $res ) ){
					$dep = $rowdp[ 'Descripcion' ];
				}
				else{
					$dep = '';
				}
				$data[ 'infoing' ][$i][ "AccDepPropietario" ] = $dep;




				//Consulto el nombre del departamento en donde ocurrio el accidente
				$res = consultaNombreDepartamento( $rows[ 'Acccdp' ] );
				$num = mysql_num_rows( $res );
				if( $rowdp = mysql_fetch_array( $res ) ){
					$dep = $rowdp[ 'Descripcion' ];
				}
				else{
					$dep = '';
				}
				$data[ 'infoing' ][$i][ "AccConductordp" ] = $dep;

				//-->2017-08-14
				$queryCc = " SELECT COUNT(*)
						       FROM {$wbasedato}_000293
						      WHERE Cccant = '{$codigoReponsable}'
						        AND Cccest = 'on'
						        AND Cccfei <= '".date('Y-m-d')."'";
				$rsCc    = mysql_query( $queryCc, $conex );
				$rowCc   = mysql_fetch_array( $rsCc );
				if( $rowCc[0]*1 > 0 ){
					$data[ 'infoing' ][$i]["cambioConsorcio"] = "on";
				}else{
					$data[ 'infoing' ][$i]["cambioConsorcio"] = "off";
				}


				//Consulto el nombre el municipio en donde ocurrio el accidente
				$res = consultaNombreMunicipio( $rows[ 'Accmun' ] );
				$num = mysql_num_rows( $res );
				if( $rowdp = mysql_fetch_array( $res ) ){
					$mun = $rowdp[ 'Nombre' ];
				}
				else{
					$mun = '';
				}
				$data[ 'infoing' ][$i][ "Accmun" ] = $mun;




				//Consulto el nombre el municipio del propietario
				$res = consultaNombreMunicipio( $rows[ 'Accpmn' ] );
				$num = mysql_num_rows( $res );
				if( $rowdp = mysql_fetch_array( $res ) ){
					$mun = $rowdp[ 'Nombre' ];
				}
				else{
					$mun = '';
				}
				$data[ 'infoing' ][$i][ "AccMunPropietario" ] = $mun;




				//Consulto el nombre el municipio del conductor
				$res = consultaNombreMunicipio( $rows[ 'Acccmn' ] );
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
				$res = consultaNombreAseguradoraVehiculo( $rows[ 'Acccas' ] );
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

	return $data[ 'infoing' ][$i];
}


/**************************************************************************************************
 * Consulta los eventos catastróficos para un paciente según la historia e ingreso
 **************************************************************************************************/
function consultarEventosCatastroficos( $his, $ing, &$data )
{
	global $wbasedato;
	global $conex;

	$i = count( $data[ 'infoing' ] ) - 1;

	$sql = "SELECT
				Devcod, Deveve, Devdir, Devded, Devfac, Devhac, Devdep, Devmun, Devzon, Devdes, Evncla
			FROM
				{$wbasedato}_000149 a, {$wbasedato}_000150 b, {$wbasedato}_000154 c
			WHERE
				Evnhis = '$his'
				AND Evning = '$ing'
				AND b.Evncod = Devcod
				AND b.Evnest = 'on'
				AND c.Evncod = Deveve
				AND a.Devest = 'on'
			";

	$res = mysql_query( $sql, $conex ) or ( $data[ 'error' ] = utf8_encode( "Error consultando la tabla ".$wbasedato."000100 ".mysql_errno()." - Error en el query $sql - ".mysql_error() ) );

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
				$data[ 'infoing' ][$i][ "det_ux_evccec" ] = utf8_encode( $rows[ 'Evncla' ] );


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

	return $data[ 'infoing' ][$i];
}


function consultarIpsQueRemite( $aseg, $wbasedato )
{

		global $conex;




		$val = "";

		//Diagnostico
	 	$sql = "SELECT Empnit, Empnom
				FROM ".$wbasedato."_000024
				WHERE Empnom LIKE '%".utf8_decode($aseg)."%'
				ORDER BY Empnom
				";

		$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );

		if( $num > 0 ){

			for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){

					$rows[ 'Empnit' ] = trim( utf8_encode($rows[ 'Empnit' ]) );
					$rows[ 'Empnom' ] = trim( utf8_encode($rows[ 'Empnom' ] ) );

					//Creo el resultado como un json
					//Primero creo un array con los valores necesarios
					$data[ 'valor' ] = Array( "cod"=> $rows[ 'Empnit' ], "des"=> $rows[ 'Empnom' ] );	//Este es el dato a procesar en javascript
					$data[ 'usu' ] = "{$rows[ 'Empnit' ]}-{$rows[ 'Empnom' ]}";	//Este es el que ve el usuario
					$dat = Array();
					$dat[] = $data;

					$val .= json_encode( $dat )."\n";

			}
		}

		return $val;
}

function consultarAplicacion2($conexion, $codigoInstitucion, $nombreAplicacion){
	$q = " SELECT
				Detval
			FROM
				root_000051
			WHERE
				Detemp = '".$codigoInstitucion."'
				AND Detapl = '".$nombreAplicacion."'";

	//	echo $q;
	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$alias = "";
	if ($num > 0)
	{
		$rs = mysql_fetch_array($res);

		$alias = $rs['Detval'];
	}

	return $alias;
}

function guardarPendientesRegrabacion( $historia, $ingreso, $responsableAnterior, $responsableNuevo, $origenModificacion ){
	global $conex;
	global $wbasedato;
	global $key;
	$sql = " INSERT INTO {$wbasedato}_000282( Medico, Fecha_data, Hora_data, Prehis, Preing, Prerea, Preren, Prepoc, Preest, seguridad)
		 	 		                  VALUES( '{$wbasedato}', '".date('Y-m-d')."', '".date('H:i:s')."', '{$historia}', '{$ingreso}', '{$responsableAnterior}', '{$responsableNuevo}', '{$origenModificacion}', 'on', '".utf8_decode($key)."')";
    $rs  = mysql_query( $sql, $conex);
}

function consultarCC($alias,$where, $usuario = ""){

	global $conex;
	global $wbasedato;
	$condicionCcoPermitidos = "";

	if( $usuario != "" ){
		$query = " SELECT Percca, Perccd
					 FROM {$wbasedato}_000081
					WHERE Perfue = '01'
					  AND Perusu ='{$usuario}'";
		$rsAux = mysql_query( $query, $conex );
		$numRs = mysql_num_rows( $rsAux );
		while( $rowRs = mysql_fetch_assoc( $rsAux ) ){

			$ccoPermitidos = $rowRs['Percca'];

			if( $ccoPermitidos != "" ){

				$ccoPermitidos = explode(",",$ccoPermitidos);
				foreach ($ccoPermitidos as $i => $value) {
					$ccoPermitidos[$i] = "'$value'";
				}
				$ccoPermitidos          = implode( ",", $ccoPermitidos );
				$ccoPerccd              = $rowRs['Perccd'];
				$ccoPermitidos          .= ",'{$ccoPerccd}'";
				$condicionCcoPermitidos = " AND Ccocod in ($ccoPermitidos) ";
			}

		}
	}


	$q = " SELECT Ccocod,Cconom, Ccocod, Ccosei
			FROM ".$alias."_000011
			WHERE ".$where." {$condicionCcoPermitidos}
			order by Cconom";


	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	return $res;
}

/********************************************************************************************************
 * Agosto 16 de 2013
 *
 * Consulta los registros que hay para preadmision
 ********************************************************************************************************/
function agendaAdmisiones( $fecha, $incremento = 0 )
{
	global $conex;
	global $wbasedato;
	global $wemp_pmla;
	global $consulta;
	global $user2;

	$admin_erp_ver_boton_alta_egreso = consultarAplicacion2($conex,$wemp_pmla,"admin_erp_ver_boton_alta_egreso");

	$data = array('error'=>0,'mensaje'=>'','html'=>'');

	$fecMostrarUnix = strtotime( $fecha ) + $incremento*3600*24;
	$fechaMostrar = date( "Y-m-d", $fecMostrarUnix );
	$fechaTitulo = nombreMes( date( "m", $fecMostrarUnix ) ).date( " d \d\e Y", $fecMostrarUnix );
	$permisos = consultarPermisosUsuario( $user2[1] );
	$disabled2 = '';

	/****************************************************************************************************
	 * Agosto 30 de 2013
	 * Solo se puede hacer ingreso si la preadmisión es del día actual
	 ****************************************************************************************************/
	$disabled = '';
	if( date( "Y-m-d" ) != $fechaMostrar ){
		$disabled = 'disabled';
	}

	if( $permisos['consulta'] == "on" and $permisos['graba'] == "off" and $disabled != 'disabled' ){
		$disabled2 = "disabled";
	}

	$permiso_alta_egreso = ($permisos['graba'] == "on") ? '': 'disabled="disabled"';
	/****************************************************************************************************/

	/****************************************************************************************************
	 * Agosto 15 de 2013
	 ****************************************************************************************************/
	$data[ 'html' ] = "<br>";
	$data[ 'html' ] .= "<table class='anchotabla' align='center'>";
	$data[ 'html' ] .= "<tr>";
	$data[ 'html' ] .= "<td class='encabezadotabla' align='center' style='font-size:18pt'>";
	$data[ 'html' ] .= "AGENDA DE PREADMISIONES";
	$data[ 'html' ] .= "</td>";
	$data[ 'html' ] .= "</tr>";
	$data[ 'html' ] .= "</table>";


	$data[ 'html' ] .= "<a id='fecActAgenda' style='display:none'>$fechaMostrar</a>";

	$data[ 'html' ] .= "<br>";
	$data[ 'html' ] .= "<div>";
	$data[ 'html' ] .= "<center><table border='0'>";
	$data[ 'html' ] .= "<tr>";
	$data[ 'html' ] .= "<td colspan='3'></td><td class='encabezadotabla' align='center'>Seleccione la fecha</td>";
	$data[ 'html' ] .= "</tr>";
	$data[ 'html' ] .= "<tr>";
	$data[ 'html' ] .= "<td colspan='3'></td><td><INPUT TYPE='text' value='$fechaMostrar' onChange='consultarAgendaPreadmision( this.value, 0 )'  style='width:200;text-align:center;' fecha></td>";
	$data[ 'html' ] .= "</tr>";
	$data[ 'html' ] .= "<tr>";
	$data[ 'html' ] .= "<td align='center' colspan='3'>";
	$data[ 'html' ] .= "<img src='../../images/medical/citas/atras.jpg' height='30' width='30' border=0 onClick='consultarAgendaPreadmision( \"$fechaMostrar\", -1 );'/>";
	$data[ 'html' ] .= "</td>";
	$data[ 'html' ] .= "<td align='center'>";
	$data[ 'html' ] .= "<b>".$fechaTitulo."</b>";
	$data[ 'html' ] .= "</td>";
	$data[ 'html' ] .= "<td align='center' colspan='3'>";
	$data[ 'html' ] .= "<img src='../../images/medical/citas/adelante.jpg' height='30' width='30' border=0 onClick='consultarAgendaPreadmision( \"$fechaMostrar\", 1 )'/>";
	$data[ 'html' ] .= "</td>";

	$data[ 'html' ] .= "</tr>";
	$data[ 'html' ] .= "</table></center>";
	$data[ 'html' ] .= "</div>";//div botones navegacion

	$sql = "SELECT
				*
			FROM
				".$wbasedato."_000166
			WHERE
				pacact = 'on'
				AND pacfec LIKE '".$fechaMostrar."'
			";

	$res = mysql_query( $sql, $conex ) or die( $data[ 'mensaje' ] = mysql_errno()." - Error en el query $sql -".mysql_error() );

	if( $res )
	{
		$num = mysql_num_rows( $res );

		if( $num > 0 )
		{

			$data[ 'html' ] .= "<br><table align='center'>";

			$data[ 'html' ] .= "<tr class='encabezadotabla'>";
			$data[ 'html' ] .= "<td>Documento</td>";
			$data[ 'html' ] .= "<td>Nombre del paciente</td>";
			$data[ 'html' ] .= "<td>Responsable</td>";
			$data[ 'html' ] .= "<td>Admitir</td>";
			$data[ 'html' ] .= "<td>Cancelar</td>";
			$data[ 'html' ] .= "<td>Editar</td>";


			//---------------------------------------------------
			// mirar el codigo de la empresa

			$sqlempresascondigitalizacion = "SELECT Empcod ,Empccd
											   FROM ".$wbasedato."_000024
											  WHERE Empdso ='on'";
			$resempresascondigitalizacion = mysql_query( $sqlempresascondigitalizacion, $conex ) or die( $data[ 'mensaje' ] = mysql_errno()." - Error en el query $sqlCar -".mysql_error() );
			$array_datosempresadigitalizacion = array();
			while($rowsempresascondigitalizacion = mysql_fetch_array( $resempresascondigitalizacion ))
			{
				if($rowsempresascondigitalizacion['Empccd']=='*' || $rowsempresascondigitalizacion['Empccd']=='' ||  $rowsempresascondigitalizacion['Empccd'] =='No aplica')
					$rowsempresascondigitalizacion['Empccd']='*';

				$array_datosempresadigitalizacion[$rowsempresascondigitalizacion['Empcod']] =  $rowsempresascondigitalizacion['Empccd'];
			}


			//------------------------



			$TableroDigitalizacionUrgencias = consultarAplicacion2($conex,$wemp_pmla,"TableroDigitalizacionUrgencias");

			if($TableroDigitalizacionUrgencias =='on')
			{
					$data[ 'html' ] .= "<td >Tablero de digitalizaci&oacute;n </td>";
			}


			$data[ 'html' ] .= "</tr>";

			for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ )
			{
				$class = "class='fila".($i%2+1)."'";

				$data[ 'html' ] .= "<tr $class>";

				//Documento
				$data[ 'html' ] .= "<td>";
				$data[ 'html' ] .= $rows[ 'Pactdo' ]."-".$rows[ 'Pacdoc' ];
				$data[ 'html' ] .= "</td>";

				//Nombres
				$data[ 'html' ] .= "<td>";
				$data[ 'html' ] .= utf8_encode($rows[ 'Pacno1' ])." ".utf8_encode($rows[ 'Pacno2' ])." ".utf8_encode($rows[ 'Pacap1' ])." ".utf8_encode($rows[ 'Pacap2' ]);
				$data[ 'html' ] .= "</td>";


				//--------------
				$sqlresponsable = "  select Ingtdo,Ingdoc,a.id ,Ingcem , Empnom , Ingsei
										from ".$wbasedato."_000167 a, ".$wbasedato."_000166 b , ".$wbasedato."_000024
										where Ingdoc = '".$rows[ 'Pacdoc' ]."'
										and Ingtdo = '".$rows[ 'Pactdo' ]."'
										and Pacdoc = Ingdoc
										and Pactdo = Ingtdo
										and Pacact = 'on'
										and Pacidi = a.id
										and Ingcem = Empcod ";
				$resresponsable = mysql_query( $sqlresponsable, $conex ) or die( $data[ 'mensaje' ] = mysql_errno()." - Error en el query $sql -".mysql_error() );
				$nombreresponsable = "";
				if($rowresponsable  = mysql_fetch_array( $resresponsable ))
				{
				   $nombreresponsable = $rowresponsable['Ingcem']." - ".utf8_encode($rowresponsable['Empnom']);
				}
				else
				{
					$nombreresponsable ="PARTICULAR";
				}

				//----

				///$Ingcem
				$data[ 'html' ] .= "<td>".$nombreresponsable."</td>";


				//Ingresar
				$data[ 'html' ] .= "<td>";
				$data[ 'html' ] .= "<INPUT type='radio' onClick='ingresarPreadmision( this, \"".$rows[ 'Pactdo' ]."\", \"".$rows[ 'Pacdoc' ]."\" );' $disabled $disabled2>";
				$data[ 'html' ] .= "</td>";

				//Cancelar
				$data[ 'html' ] .= "<td>";
				$data[ 'html' ] .= "<INPUT type='radio' onClick='cancelarPreadmision( this, \"$fechaMostrar\", 0, \"".$rows[ 'Pactdo' ]."\", \"".$rows[ 'Pacdoc' ]."\" )'>";
				$data[ 'html' ] .= "</td>";

				//Editar
				$data[ 'html' ] .= "<td>";
				$data[ 'html' ] .= "<INPUT type='radio' onClick='mostrarDatosPreadmision( \"".$rows[ 'Pacdoc' ]."\" )'>";
				$data[ 'html' ] .= "</td>";


				//---------------------------------------------------
			// mirar el codigo de la empresa




			//------------------------



			$TableroDigitalizacionUrgencias = consultarAplicacion2($conex,$wemp_pmla,"TableroDigitalizacionUrgencias");

			if($TableroDigitalizacionUrgencias =='on')
			{

					////*--
					if (array_key_exists($rowresponsable[ 'Ingcem' ], $array_datosempresadigitalizacion))
					{

						$ccopermitidos = $array_datosempresadigitalizacion[$rowresponsable[ 'Ingcem' ]];
						if($ccopermitidos=='*' || $ccopermitidos==''  || $ccopermitidos=='No aplica' )
						{
							$data[ 'html' ] .= "<td >";
							// $data[ 'html' ] .= "<input onclick='abrirTableroDigitalizacion(\"\", \"\", \"".$rows[ 'Pactdo' ]."\", \"".$rows[ 'Pacdoc' ]."\" , \"".$rowresponsable['Ingcem']."\" , \"\")' type = 'button' value='Digitalizar' >";
							$data[ 'html' ] .= "<input onclick='abrirTableroDigitalizacion(\"\", \"\", \"".$rows[ 'Pactdo' ]."\", \"".$rows[ 'Pacdoc' ]."\" , \"".$rowresponsable['Ingcem']."\" , \"\")' type = 'button' value='Digitalizar' >";
							$data[ 'html' ] .= "</td>";
						}
						else
						{
							$ccopermitidosvec =  explode(",",$ccopermitidos);

							if(in_array($rowresponsable['Ingsei'], $ccopermitidosvec))
							{
								$data[ 'html' ] .= "<td>";
								// $data[ 'html' ] .= "<input onclick='abrirTableroDigitalizacion(\"\", \"\", \"".$rows[ 'Pactdo' ]."\", \"".$rows[ 'Pacdoc' ]."\" , \"".$rowresponsable['Ingcem']."\" , \"\")' type = 'button' value='Digitalizar' >";
								$data[ 'html' ] .= "<input onclick='abrirTableroDigitalizacion(\"\", \"\", \"".$rows[ 'Pactdo' ]."\", \"".$rows[ 'Pacdoc' ]."\" , \"".$rowresponsable['Ingcem']."\" , \"\")' type = 'button' value='Digitalizar' >";
								//$data[ 'html' ] .= "<input onclick='abrirTableroDigitalizacion(\"".$rows[ 'Pachis' ]."\", \"".$rows[ 'Ingnin' ]."\" , \"".$rows[ 'Ingcem' ]."\" , \"".$rows[ 'fechaCarpeta' ]."\")' type = 'button' value='Digitalizar' >";
								$data[ 'html' ] .= "</td>";
							}
							else
							{
								 $data[ 'html' ] .= "<td>";
								 $data[ 'html' ] .= "</td>";
							}
						}
						//$data[ 'html' ] .= "<td >Tablero de digitalizaci&oacute;n </td>";


					}
					else
					{
						$data[ 'html' ] .= "<td>";
								 $data[ 'html' ] .= "</td>";
					}

					////*--



			}



				$data[ 'html' ] .= "</tr>";
			}

			$data[ 'html' ] .= "</table>";
		}
		else
		{
			$data[ 'html' ] .= "<br><br><center><b>NO SE ENCONTRARON REGISTROS</b></center>";
		}

		$data[ 'html' ] .= "<br>";
		$data[ 'html' ] .= "<table align='center'>";
		$data[ 'html' ] .= "<tr>";
		$data[ 'html' ] .= "<td>";
		$data[ 'html' ] .= "<INPUT type='button' value='Consultar' style='width:150;font-size:10pt' onClick='prepararParaConsulta()'>";
		$data[ 'html' ] .= "</td>";
		$data[ 'html' ] .= "<td>";
		$data[ 'html' ] .= "<INPUT type='button' value='Nueva admision' style='width:150;font-size:10pt' onClick='mostrarAdmision()' $disabled2>";
		$data[ 'html' ] .= "</td>";
		$data[ 'html' ] .= "<td>";
		$data[ 'html' ] .= "<INPUT type='button' value='Nueva preadmision' style='width:150;font-size:10pt' onClick='ponerPreadmitirEnBotones();' consulta='{$permisos['consulta']}' graba='{$permisos['graba']}' usuario='$user2' $disabled2>";
		$data[ 'html' ] .= "</td>";
		if($admin_erp_ver_boton_alta_egreso == 'on'){
			$data[ 'html' ] .= '<td><INPUT type="button" value="Alta definitiva/Egreso" id="btn_alta_historia" value="Dar alta y egreso" style="width:160;font-size:10pt" onClick="modal_alta_paciente_otro_servicio();" '.$permiso_alta_egreso.'></td>';
		}
		$data[ 'html' ] .= "<td>";
		$data[ 'html' ] .= "<INPUT type='button' value='Cerrar' style='width:100;font-size:10pt' onClick='cerrarVentana();'>";
		$data[ 'html' ] .= "</td>";
		$data[ 'html' ] .= "</tr>";
		$data[ 'html' ] .= "</table>";
	}
	else{
		$data[ 'error' ] = 1;
	}

	$data[ 'html' ] .= "<table>";
	$data[ 'html' ] .= "</table>";
	/****************************************************************************************************/

	return $data;
}



/****************************************************************************************
 * Agosto 16 de 2013
 *
 * funcion para el log del programa de admisiones
 ****************************************************************************************/
function logAdmsiones( $des, $historia, $ingreso, $documento )
{
	global $key;
	global $conex;
	global $wbasedato;

	$data = array('error'=>0,'mensaje'=>'','html'=>'');

	$fecha = date("Y-m-d");
	$hora = (string)date("H:i:s");

	$sql = "INSERT INTO ".$wbasedato."_000164 (     medico     ,      fecha_data         ,       hora_data        ,        Logusu         ,         Logdes        ,            Loghis          ,           Loging          ,            Logdoc           , Logest, seguridad )
									   VALUES ('".$wbasedato."','".utf8_decode($fecha)."','".utf8_decode($hora)."','".utf8_decode($key)."','".utf8_decode($des)."','".utf8_decode($historia)."','".utf8_decode($ingreso)."','".utf8_decode($documento)."',  'on' , 'C-root'  )";

	$res = mysql_query( $sql, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error insertando en la tabla de log admisiones ".$wbasedato." 164 ".mysql_errno()." - Error en el query $sql - ".mysql_error() ) );
	if (!$res)
	{
		$data[ 'error' ] = 1; //sale el mensaje de error
	}

	return $data;
}


/************************************************************************************************
 * Crea un string valido pra consulta where en sql
 ************************************************************************************************/
function crearStringWhere( $campos )
{
	$val = '';

	foreach( $campos as $key => $value )
	{
		if( !empty( $value ) )
		{
			if( true || isset( $campos[ substr( $key, 0, 4 ) ] ) )
			{
				$val .= " AND ".$key." = '".utf8_decode( $value )."' ";
			}
		}
	}

	return $val;
}

/************************************************************
 * Devuelve el nombre del mes
 *
 * El mes debe ir entre 1 - 12
 ************************************************************/
function nombreMes( $mes )
{
	$nombreMes = '';

	switch( $mes )
	{

		case 1:{
			$nombreMes = "Enero";
		}
		break;

		case 2:{
			$nombreMes = "Febrero";
		}
		break;

		case 3:{
			$nombreMes = "Marzo";
		}
		break;

		case 4:{
			$nombreMes = "Abril";
		}
		break;

		case 5:{
			$nombreMes = "Mayo";
		}
		break;

		case 6:{
			$nombreMes = "Junio";
		}
		break;

		case 7:{
			$nombreMes = "Julio";
		}
		break;

		case 8:{
			$nombreMes = "Agosto";
		}
		break;

		case 9:{
			$nombreMes = "Septiembre";
		}
		break;

		case 10:{
			$nombreMes = "Octubre";
		}
		break;

		case 11:{
			$nombreMes = "Noviembre";
		}
		break;

		case 12:{
			$nombreMes = "Diciembre";
		}
		break;
	}

	return $nombreMes;
}


/********************************************************************************************************
 * Septiembre 10 de 2013
 *
 * Consulta los registros que hay para pacientes admitidos
 ********************************************************************************************************/
function agendaAdmitidos( $fecha, $incremento = 0 )
{

	global $conex;
	global $wbasedato;
	global $user;
	global $wemp_pmla;
	global $cco_usuario;
	global $consulta;
	global $filtrarCcoAyuda;
	global $user2;
	global $imprimirHistoria;

	$data = array('error'=>0,'mensaje'=>'','html'=>'');

	$aplMovhos=consultarAplicacion2($conex,$wemp_pmla,"movhos");
	$wbasedatoHce = consultarAplicacion2( $conex, $wemp_pmla, "hce" );
	$imprimirHistoria = consultarAplicacion2($conex,$wemp_pmla,"imprimirHistoria");
	$habilitarSolicitarCambioDocumento = consultarAplicacion2($conex,$wemp_pmla,"habilitarSolicitarCambioDocumento");
	$admin_erp_ver_boton_alta_egreso = consultarAplicacion2($conex,$wemp_pmla,"admin_erp_ver_boton_alta_egreso");
	$priorizarPermiso81 = consultarAplicacion2($conex,$wemp_pmla,"priorizarPermiso81");

	if( $fecha == "" ){
		$fechaBase = date("Y-m-d");
	}else{
		$fechaBase = $fecha;
	}

	$fecMostrarUnix = strtotime( $fechaBase ) + $incremento*3600*24;
	$fechaMostrar = date( "Y-m-d", $fecMostrarUnix );
	$fechaTitulo = nombreMes( date( "m", $fecMostrarUnix ) ).date( " d \d\e Y", $fecMostrarUnix );

	/****************************************************************************************************
	 * Agosto 30 de 2013
	 * Solo se puede hacer ingreso si la preadmisión es del día actual
	 ****************************************************************************************************/
	$disabled  = '';
	$disabled2 = '';
	if( date( "Y-m-d" ) != $fechaMostrar ){
		$disabled = 'disabled';
	}
	/****************************************************************************************************/

	$verListaTurnos = false;
	// --> Si el cco del usuario es de urgencias
	$sqlCcoUrg = "SELECT Ccocod
					FROM ".$aplMovhos."_000011
				   WHERE Ccocod = '".$cco_usuario."'
				     AND Ccourg = 'on'
	";
	$resCcoUrg = mysql_query($sqlCcoUrg, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCcoUrg):</b><br>".mysql_error());
	if(mysql_fetch_array($resCcoUrg))
		$verListaTurnos = TRUE;

	$filtrarCcoPorUsuario = false;
	//--> se consultan los centros de costos que el usuario puede ver
	$querycco = " SELECT Percca, Perccd
				 FROM {$wbasedato}_000081
				WHERE Perfue = '01'
				  AND Perusu ='{$user2[1]}'";
	$rsAux = mysql_query( $querycco, $conex );
	$numRs = mysql_num_rows( $rsAux );
	while( $rowRs = mysql_fetch_assoc( $rsAux ) ){

		$ccoPermitidos = $rowRs['Percca'];

		if( $ccoPermitidos != "" ){
			$filtrarCcoPorUsuario = true;

			$ccoPermitidos = explode(",",$ccoPermitidos);
			foreach ($ccoPermitidos as $i => $value) {
				$ccoPermitidos[$i] = "'$value'";
			}
			$ccoPermitidos          = implode( ",", $ccoPermitidos );
			$ccoPerccd              = $rowRs['Perccd'];
			$ccoPermitidos          .= ",'{$ccoPerccd}'";
			$condicionCcoPermitidos = "Ccocod in ($ccoPermitidos) ";

			$qcco = " SELECT Ccocod,Cconom, Ccocod, Ccosei
					 FROM ".$aplMovhos."_000011
				    WHERE {$condicionCcoPermitidos}
					ORDER by Cconom";
			$rscco = mysql_query( $qcco, $conex );
			while( $rowcco = mysql_fetch_assoc( $rscco ) ){
				$htmlcco .= "<option value='{$rowcco['Ccocod']}'> {$rowcco['Ccocod']}-{$rowcco['Cconom']} </option>";
			}
		}
	}

	if( $fecha == "" ){
		$data[ 'html' ] .= "<center class='encabezadotabla' style='font-size:18pt'>PACIENTES ADMITIDOS</center>";
		$data[ 'html' ] .= "<a id='fecActAdmitidos' style='display:none'>$fechaMostrar</a>";

		$data[ 'html' ] .= "<br>";
		$data[ 'html' ] .= "<div>";
		$data[ 'html' ] .= "<center><table border='0'>";
		$data[ 'html' ] .= "<tr>";
		$data[ 'html' ] .= "<td colspan='3'></td><td class='encabezadotabla' align='center'>Seleccione la fecha</td>";
		$data[ 'html' ] .= "</tr>";
		$data[ 'html' ] .= "<tr>";
		$data[ 'html' ] .= "<td colspan='3'></td><td><INPUT TYPE='text' value='$fechaMostrar' onChange='consultarAgendaAdmitidos( this.value, 0 )'  style='width:200;text-align:center;' fecha></td>";
		$data[ 'html' ] .= "</tr>";
		$data[ 'html' ] .= "<tr>";
		$data[ 'html' ] .= "<td align='center' colspan='3'>";
		$data[ 'html' ] .= "<img src='../../images/medical/citas/atras.jpg' height='30' width='30' border=0 onClick='consultarAgendaAdmitidos( \"$fechaMostrar\", -1 );'/>";
		$data[ 'html' ] .= "</td>";
		$data[ 'html' ] .= "<td align='center'>";
		$data[ 'html' ] .= "<b>".$fechaTitulo."</b>";
		$data[ 'html' ] .= "</td>";
		$data[ 'html' ] .= "<td align='center' colspan='3'>";
		$data[ 'html' ] .= "<img src='../../images/medical/citas/adelante.jpg' height='30' width='30' border=0 onClick='consultarAgendaAdmitidos( \"$fechaMostrar\", 1 )'/>";
		$data[ 'html' ] .= "</td>";
		$data[ 'html' ] .= "</tr>";
		$data[ 'html' ] .= "</table><br>";

		if( $filtrarCcoAyuda == "on" or ( $verListaTurnos ) ){
			$permisos = array();
			$permisos = consultarPermisosUsuario( $user2[1] );

			/****************************************************************************************************
			 * Agosto 30 de 2013
			 * Solo se puede hacer ingreso si la preadmisión es del día actual
			 ****************************************************************************************************/
			$disabled = '';
			if( date( "Y-m-d" ) != $fechaMostrar ){
				$disabled = 'disabled';
			}

			if( $permisos['consulta'] == "on" and $permisos['graba'] == "off" and $disabled != 'disabled' ){
				$disabled2 = "disabled";
			}

			$permiso_alta_egreso = ($permisos['graba'] == "on") ? '': 'disabled="disabled"';

			$data[ 'html' ] .= "<table align='center'>";
			$data[ 'html' ] .= "<tr>";
			$data[ 'html' ] .= "<td>";
			$data[ 'html' ] .= "<INPUT type='button' value='Consultar' style='width:150;font-size:10pt' onClick='prepararParaConsulta()'>";
			$data[ 'html' ] .= "</td>";
			$data[ 'html' ] .= "<td>";
			$data[ 'html' ] .= "<INPUT type='button' value='Nueva admision' style='width:150;font-size:10pt' onClick='mostrarAdmision()' $disabled2>";
			$data[ 'html' ] .= "</td>";
			$data[ 'html' ] .= "<td>";
			$data[ 'html' ] .= "<INPUT type='button' value='Nueva preadmision' style='width:150;font-size:10pt' onClick='ponerPreadmitirEnBotones();' consulta='{$permisos['consulta']}' graba='{$permisos['graba']}' usuario='{$user2[1]}' $disabled2>";
			$data[ 'html' ] .= "</td>";
			if($admin_erp_ver_boton_alta_egreso == 'on'){
				$data[ 'html' ] .= '<td><INPUT type="button" value="Alta definitiva/Egreso" id="btn_alta_historia" value="Dar alta y egreso" style="width:160;font-size:10pt" onClick="modal_alta_paciente_otro_servicio();" '.$permiso_alta_egreso.'></td>';
			}
			$data[ 'html' ] .= "<td>";
			$data[ 'html' ] .= "<INPUT type='button' value='Cerrar' style='width:100;font-size:10pt' onClick='cerrarVentana();'>";
			$data[ 'html' ] .= "</td>";
			$data[ 'html' ] .= "</tr>";
			$data[ 'html' ] .= "</table>";
			$data[ 'html' ] .= "<br>";
		}
		$data[ 'html' ] .= "<br><center><div style='cursor:pointer;' onClick='consultarAgendaAdmitidos( \"$fechaBase\", 0 );'><span class='subtituloPagina2'> Ver Admisiones Hoy </span></div></center>";

		return ($data);
	}

	$verListaTurnos = false;
	// --> Si el cco del usuario es de urgencias
	$sqlCcoUrg = "SELECT Ccocod
					FROM ".$aplMovhos."_000011
				   WHERE Ccocod = '".$cco_usuario."'
				     AND Ccourg = 'on'
	";
	$resCcoUrg = mysql_query($sqlCcoUrg, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCcoUrg):</b><br>".mysql_error());
	if(mysql_fetch_array($resCcoUrg))
		$verListaTurnos = TRUE;

	$filtrarCcoPorUsuario = false;
	//--> se consultan los centros de costos que el usuario puede ver
	$querycco = " SELECT Percca, Perccd
				 FROM {$wbasedato}_000081
				WHERE Perfue = '01'
				  AND Perusu ='{$user2[1]}'";
	$rsAux = mysql_query( $querycco, $conex );
	$numRs = mysql_num_rows( $rsAux );
	while( $rowRs = mysql_fetch_assoc( $rsAux ) ){

		$ccoPermitidos = $rowRs['Percca'];

		if( $ccoPermitidos != "" ){
			$filtrarCcoPorUsuario = true;

			$ccoPermitidos = explode(",",$ccoPermitidos);
			foreach ($ccoPermitidos as $i => $value) {
				$ccoPermitidos[$i] = "'$value'";
			}
			$ccoPermitidos          = implode( ",", $ccoPermitidos );
			$ccoPerccd              = $rowRs['Perccd'];
			$ccoPermitidos          .= ",'{$ccoPerccd}'";
			$condicionCcoPermitidos = "Ccocod in ($ccoPermitidos) ";

			$qcco = " SELECT Ccocod,Cconom, Ccocod, Ccosei
					 FROM ".$aplMovhos."_000011
				    WHERE {$condicionCcoPermitidos}
					ORDER by Cconom";
			$rscco = mysql_query( $qcco, $conex );
			while( $rowcco = mysql_fetch_assoc( $rscco ) ){
				$htmlcco .= "<option value='{$rowcco['Ccocod']}'> {$rowcco['Ccocod']}-{$rowcco['Cconom']} </option>";
			}
		}
	}


	$filtroCco = ($filtrarCcoPorUsuario) ? " AND ingsei in ($ccoPermitidos) " : "";
	//Busco los pacientes que tienen admisión el día actual
	$sql = "SELECT
				a.*, b.Ingnin, b.Ingsei,b.Ingcem ,b.fecha_data as fechaCarpeta
			FROM
				".$wbasedato."_000100 a, ".$wbasedato."_000101 b
			WHERE ingfei = '".$fechaMostrar."'
			  AND pachis = inghis {$filtroCco}";
			// AND ingnin = ( SELECT MAX(ingnin*1) FROM ".$wbasedato."_000101 c WHERE c.inghis = a.pachis )

	if( $cco_usuario != ""  && $filtrarCcoAyuda == "on" && !$filtrarCcoPorUsuario){
		$sql.= " AND Ingsei = '".$cco_usuario."' ";
	}

	$res = mysql_query( $sql, $conex ) or die( $data[ 'mensaje' ] = mysql_errno()." - Error en el query $sql -".mysql_error() );

	if( $res )
	{
		$num = mysql_num_rows( $res );

		// $data[ 'html' ] .= "<p align='right' style='font-size:10pt;'><a onClick='ocultarMostrarPacientesIngresados(\"$num\");'>[+] Pacientes Admitidos hoy</a></p>";

		// $data[ 'html' ] .= "<div id='dvPacIngresados'>";
		$data[ 'html' ] .= "<center class='encabezadotabla' style='font-size:18pt'>PACIENTES ADMITIDOS</center>";
		$data[ 'html' ] .= "<a id='fecActAdmitidos' style='display:none'>$fechaMostrar</a>";

		$data[ 'html' ] .= "<br>";
		$data[ 'html' ] .= "<div>";
		$data[ 'html' ] .= "<center><table border='0'>";
		$data[ 'html' ] .= "<tr>";
		$data[ 'html' ] .= "<td colspan='3'></td><td class='encabezadotabla' align='center'>Seleccione la fecha</td>";
		$data[ 'html' ] .= "</tr>";
		$data[ 'html' ] .= "<tr>";
		$data[ 'html' ] .= "<td colspan='3'></td><td><INPUT TYPE='text' value='$fechaMostrar' onChange='consultarAgendaAdmitidos( this.value, 0 )'  style='width:200;text-align:center;' fecha></td>";
		$data[ 'html' ] .= "</tr>";
		$data[ 'html' ] .= "<tr>";
		$data[ 'html' ] .= "<td align='center' colspan='3'>";
		$data[ 'html' ] .= "<img src='../../images/medical/citas/atras.jpg' height='30' width='30' border=0 onClick='consultarAgendaAdmitidos( \"$fechaMostrar\", -1 );'/>";
		$data[ 'html' ] .= "</td>";
		$data[ 'html' ] .= "<td align='center'>";
		$data[ 'html' ] .= "<b>".$fechaTitulo."</b>";
		$data[ 'html' ] .= "</td>";
		$data[ 'html' ] .= "<td align='center' colspan='3'>";
		$data[ 'html' ] .= "<img src='../../images/medical/citas/adelante.jpg' height='30' width='30' border=0 onClick='consultarAgendaAdmitidos( \"$fechaMostrar\", 1 )'/>";
		$data[ 'html' ] .= "</td>";
		$data[ 'html' ] .= "</tr>";

		if( $filtrarCcoPorUsuario){
			$data[ 'html' ] .= "<tr>";
			$data[ 'html' ] .= "<td>&nbsp;</td><td colspan='3' align='center' class='encabezadoTabla'> CENTRO DE COSTOS DE INGRESO: </td><td>&nbsp;</td>";
			$data[ 'html' ] .= "</tr>";

			$data[ 'html' ] .= "<tr>";
			$data[ 'html' ] .= "<td>&nbsp;</td><td colspan='3' align='center'>
									<SELECT onchange='filtrarPorCco(this);'>
										<option value='' selected>TODOS</option>
										{$htmlcco}
									</SELECT>
								 </td><td>&nbsp;</td>";
			$data[ 'html' ] .= "</tr>";
		}
		$data[ 'html' ] .= "</table></center>";
		$data[ 'html' ] .= "</div>";//div botones navegacion

		if( $filtrarCcoAyuda == "on" or ( $verListaTurnos ) ){
			$permisos = array();
			$permisos = consultarPermisosUsuario( $user2[1] );

			/****************************************************************************************************
			 * Agosto 30 de 2013
			 * Solo se puede hacer ingreso si la preadmisión es del día actual
			 ****************************************************************************************************/
			$disabled = '';
			if( date( "Y-m-d" ) != $fechaMostrar ){
				$disabled = 'disabled';
			}

			if( $permisos['consulta'] == "on" and $permisos['graba'] == "off" and $disabled != 'disabled' ){
				$disabled2 = "disabled";
			}

			$permiso_alta_egreso = ($permisos['graba'] == "on") ? '': 'disabled="disabled"';

			$data[ 'html' ] .= "<table align='center'>";
			$data[ 'html' ] .= "<tr>";
			$data[ 'html' ] .= "<td>";
			$data[ 'html' ] .= "<INPUT type='button' value='Consultar' style='width:150;font-size:10pt' onClick='prepararParaConsulta()'>";
			$data[ 'html' ] .= "</td>";
			$data[ 'html' ] .= "<td>";
			$data[ 'html' ] .= "<INPUT type='button' value='Nueva admision' style='width:150;font-size:10pt' onClick='mostrarAdmision()' $disabled2>";
			$data[ 'html' ] .= "</td>";
			$data[ 'html' ] .= "<td>";
			$data[ 'html' ] .= "<INPUT type='button' value='Nueva preadmision' style='width:150;font-size:10pt' onClick='ponerPreadmitirEnBotones();' consulta='{$permisos['consulta']}' graba='{$permisos['graba']}' usuario='{$user2[1]}' $disabled2>";
			$data[ 'html' ] .= "</td>";
			if($admin_erp_ver_boton_alta_egreso == 'on'){
				$data[ 'html' ] .= '<td><INPUT type="button" value="Alta definitiva/Egreso" id="btn_alta_historia" value="Dar alta y egreso" style="width:160;font-size:10pt" onClick="modal_alta_paciente_otro_servicio();" '.$permiso_alta_egreso.'></td>';
			}
			$data[ 'html' ] .= "<td>";
			$data[ 'html' ] .= "<INPUT type='button' value='Cerrar' style='width:100;font-size:10pt' onClick='cerrarVentana();'>";
			$data[ 'html' ] .= "</td>";
			$data[ 'html' ] .= "</tr>";
			$data[ 'html' ] .= "</table>";
			$data[ 'html' ] .= "<br>";
		}

		if( $num > 0 )
		{
			$permisos = consultarPermisosUsuario( $user2[1] );
			$data[ 'html' ] .= "<br><table align='center'>";
			$data[ 'html' ] .= "<tr class='encabezadotabla' align='center'>";
			$data[ 'html' ] .= "<td>Turno</td>";
			$data[ 'html' ] .= "<td>Historia</td>";
			$data[ 'html' ] .= "<td>Documento</td>";
			$data[ 'html' ] .= "<td>Nombre del paciente</td>";
			$data[ 'html' ] .= "<td >Responsable</td>";
			$data[ 'html' ] .= "<td>Editar</td>";
			$data[ 'html' ] .= "<td>Anular</td>";

			if( $imprimirHistoria == "on" ){
				$data[ 'html' ] .= "<td>Imprimir</td>";
			}

			if( $habilitarSolicitarCambioDocumento == "on" ){
				$data[ 'html' ] .= "<td>Solicitud<br>Cambio<br>documento</td>";
			}


			//---------------------------------------------------
			// mirar el codigo de la empresa

			$sqlempresascondigitalizacion = "SELECT Empcod ,Empccd
											   FROM ".$wbasedato."_000024
											  WHERE Empdso ='on'";
			$resempresascondigitalizacion = mysql_query( $sqlempresascondigitalizacion, $conex ) or die( $data[ 'mensaje' ] = mysql_errno()." - Error en el query $sqlCar -".mysql_error() );
			$array_datosempresadigitalizacion = array();
			while($rowsempresascondigitalizacion = mysql_fetch_array( $resempresascondigitalizacion ))
			{
				if($rowsempresascondigitalizacion['Empccd']=='*' || $rowsempresascondigitalizacion['Empccd']=='' ||  $rowsempresascondigitalizacion['Empccd'] =='No aplica')
					$rowsempresascondigitalizacion['Empccd']='*';

				$array_datosempresadigitalizacion[$rowsempresascondigitalizacion['Empcod']] =  $rowsempresascondigitalizacion['Empccd'];
			}


			//------------------------



			$TableroDigitalizacionUrgencias = consultarAplicacion2($conex,$wemp_pmla,"TableroDigitalizacionUrgencias");

			if($TableroDigitalizacionUrgencias =='on')
			{
					$data[ 'html' ] .= "<td >Tablero de digitalizaci&oacute;n </td>";
			}

			//--------------------------------------------------


			$data[ 'html' ] .= "</tr>";

			for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ )
			{

				if( !empty( $aplMovhos ) ){

					//Busco si el paciente tiene cargos
					$sqlCar = "SELECT
								*
							FROM
								".$aplMovhos."_000002 a
							WHERE
								Fenhis = '".$rows[ 'Pachis' ]."'
								AND Fening = '".$rows[ 'Ingnin' ]."'
								AND Fenest = 'on'
							";
				}
				else{
					//Busco si el paciente tiene cargos
					$sqlCar = "SELECT
								*
							FROM
								".$wbasedato."_000106 a
							WHERE
								Tcarhis = '".$rows[ 'Pachis' ]."'
								AND Tcaring = '".$rows[ 'Ingnin' ]."'
								AND Tcarest = 'on'
							";
				}

				$resCar = mysql_query( $sqlCar, $conex ) or die( $data[ 'mensaje' ] = mysql_errno()." - Error en el query $sqlCar -".mysql_error() );

				$disabledAnulacion = '';
				$numCar = mysql_num_rows( $resCar );
				if( $numCar > 0 ){
					$disabledAnulacion = "disabled";
				}

				$disabledPorCco = "";
				//Si el centro de costos del usuario es diferente al del ingreso, NO LO DEJA EDITAR
				if( $cco_usuario != "" && $cco_usuario != $rows[ 'Ingsei' ] and $consulta != "on"){
					//$disabledPorCco = "disabled";
					$disabledPorCco = ""; //--> 2015-05-21 que deje editar siempre, sin importar el centro de costos de ingreso
				}

				$class = "class='fila".($i%2+1)."'";

				$data[ 'html' ] .= "<tr $class ccoIngresoPaciente='".$rows['Ingsei']."' tipo='tr_admitidos'>";

				// --> 	Obtener el turno del paciente
				//		Jerson Trujillo
				$sqlObtTurno = "
				SELECT Mtrtur
				  FROM ".$wbasedatoHce."_000022
				 WHERE Mtrhis = '".$rows['Pachis']."'
				   AND Mtring = '".$rows['Ingnin']."'
				";
				$resObtTurno = mysql_query($sqlObtTurno, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlObtTurno):</b><br>".mysql_error());
				if(@$rowObtTurno = mysql_fetch_array($resObtTurno))
					$data[ 'html' ] .= "<td align='center'><b>".substr($rowObtTurno['Mtrtur'], 4)."</b></td>";
				else
					$data[ 'html' ] .= "<td></td>";

				//Historia - ingreso
				$data[ 'html' ] .= "<td>";
				$data[ 'html' ] .= $rows[ 'Pachis' ]."-".$rows[ 'Ingnin' ];
				$data[ 'html' ] .= "</td>";

				//Documento
				$data[ 'html' ] .= "<td>";
				$data[ 'html' ] .= $rows[ 'Pactdo' ]."-".$rows[ 'Pacdoc' ];
				$data[ 'html' ] .= "</td>";

				//Nombres
				$data[ 'html' ] .= "<td>";
				$data[ 'html' ] .= htmlentities($rows[ 'Pacno1' ])." ".htmlentities($rows[ 'Pacno2' ])." ".htmlentities($rows[ 'Pacap1' ])." ".htmlentities($rows[ 'Pacap2' ]);
				$data[ 'html' ] .= "</td>";
				$nombre = htmlentities($rows[ 'Pacno1' ])." ".htmlentities($rows[ 'Pacno2' ])." ".htmlentities($rows[ 'Pacap1' ])." ".htmlentities($rows[ 'Pacap2' ]);


				// voy por el nombre del responsable
				$sqlnombre = "SELECT Empcod ,Empnom
							   FROM ".$wbasedato."_000024
							  WHERE  Empcod = '".$rows[ 'Ingcem' ]."'";


				$resnombreempresa = mysql_query( $sqlnombre, $conex ) or die( $data[ 'mensaje' ] = mysql_errno()." - Error en el query $sqlCar -".mysql_error() );
				$nombreempresa = '';
				if($rownombreempresa = mysql_fetch_array( $resnombreempresa ))
				{
					$nombreempresa =  utf8_encode($rownombreempresa['Empnom']);
				}
				else
				{
					$nombreempresa = "PARTICULAR";
				}



				//responsable
				$data[ 'html' ] .= "<td>";
				$data[ 'html' ] .=  $rows[ 'Ingcem' ]." - ".$nombreempresa;
				$data[ 'html' ] .= "</td>";

				//

				//Editar
				$data[ 'html' ] .= "<td>";
				$data[ 'html' ] .= "<INPUT type='radio' onClick='mostrarDatos( \"".$rows[ 'Pacdoc' ]."\", \"".$rows[ 'Ingnin' ]."\", \"".$rows[ 'Pactdo' ]."\" )' $disabledPorCco>";
				$data[ 'html' ] .= "</td>";

				//Anular
				if($disabledAnulacion=="disabled" && $disabledPorCco == "disabled"){
					$disabledAnulacion = "";
				}
				if( $permisos['anula'] == "off" and $priorizarPermiso81 == "on" ){
					$disabledAnulacion = "disabled";
				}
				$data[ 'html' ] .= "<td>";
				$data[ 'html' ] .= "<INPUT type='radio' onClick='anularAdmision( \"".$rows[ 'Pachis' ]."\", ".$rows[ 'Ingnin' ].", \"".$rows[ 'Pactdo' ]."\", \"".$rows[ 'Pacdoc' ]."\" )' $disabledAnulacion $disabledPorCco>";
				$data[ 'html' ] .= "</td>";

				if( $imprimirHistoria == "on" ){
					$data[ 'html' ] .= "<td>";
						$data[ 'html' ] .= "<INPUT type='radio' onClick='imprimirHistoria( \"".$rows[ 'Pachis' ]."\", ".$rows[ 'Ingnin' ]." )'>";
					$data[ 'html' ] .= "</td>";
				}
				if( $habilitarSolicitarCambioDocumento == "on" ){
					$data[ 'html' ] .= "<td>";
					$data[ 'html' ] .= "<INPUT type='radio' onClick='solicitarCambioDocumento( this, \"".$rows[ 'Pachis' ]."\", ".$rows[ 'Ingnin' ].",  \"".$rows[ 'Pactdo' ]."\", \"".$rows[ 'Pacdoc' ]."\", \"".$nombre."\" )'>";
					$data[ 'html' ] .= "</td>";
				}

				//------tener en cuenta el campo
				if($TableroDigitalizacionUrgencias =='on')
				{

					if (array_key_exists($rows[ 'Ingcem' ], $array_datosempresadigitalizacion))
					{

						$ccopermitidos = $array_datosempresadigitalizacion[$rows[ 'Ingcem' ]];
						if($ccopermitidos=='*' || $ccopermitidos==''  || $ccopermitidos=='No aplica' )
						{
							$data[ 'html' ] .= "<td >";
							$data[ 'html' ] .= "<input onclick='abrirTableroDigitalizacion(\"".$rows[ 'Pachis' ]."\", \"".$rows[ 'Ingnin' ]."\", \"".$rows[ 'Pactdo' ]."\", \"".$rows[ 'Pacdoc' ]."\" , \"".$rows[ 'Ingcem' ]."\" , \"".$rows[ 'fechaCarpeta' ]."\")' type = 'button' value='Digitalizar' >";
							// $data[ 'html' ] .= "<input onclick='abrirTableroDigitalizacion(\"".$rows[ 'Pachis' ]."\", \"".$rows[ 'Ingnin' ]."\" , \"".$rows[ 'Ingcem' ]."\" , \"".$rows[ 'fechaCarpeta' ]."\")' type = 'button' value='Digitalizar' >";
							$data[ 'html' ] .= "</td>";
						}
						else
						{
							$ccopermitidosvec =  explode(",",$ccopermitidos);

							if(in_array($rows['Ingsei'], $ccopermitidosvec))
							{
								$data[ 'html' ] .= "<td >";
								$data[ 'html' ] .= "<input onclick='abrirTableroDigitalizacion(\"".$rows[ 'Pachis' ]."\", \"".$rows[ 'Ingnin' ]."\", \"".$rows[ 'Pactdo' ]."\", \"".$rows[ 'Pacdoc' ]."\" , \"".$rows[ 'Ingcem' ]."\" , \"".$rows[ 'fechaCarpeta' ]."\")' type = 'button' value='Digitalizar' >";
								// $data[ 'html' ] .= "<input onclick='abrirTableroDigitalizacion(\"".$rows[ 'Pachis' ]."\", \"".$rows[ 'Ingnin' ]."\" , \"".$rows[ 'Ingcem' ]."\" , \"".$rows[ 'fechaCarpeta' ]."\")' type = 'button' value='Digitalizar' >";
								$data[ 'html' ] .= "</td>";
							}
							else
							{
								 $data[ 'html' ] .= "<td>";
								 $data[ 'html' ] .= "</td>";
							}
						}
						//$data[ 'html' ] .= "<td >Tablero de digitalizaci&oacute;n </td>";


					}
					else
					{
						$data[ 'html' ] .= "<td>";
						$data[ 'html' ] .= "</td>";
					}



				}
				//---------
				$data[ 'html' ] .= "</tr>";
			}

			$data[ 'html' ] .= "</table>";
			// $data[ 'html' ] .= "</div>";
		}
		else
		{
			// $data[ 'html' ] .= "<div id='dvPacIngresados' style='display:'>";
			$data[ 'html' ] .= "<br><br><table align='center'>";
			$data[ 'html' ] .= "<center><b>NO SE ENCONTRARON PACIENTES ADMITIDOS</b></center>";
			$data[ 'html' ] .= "</table>";
			// $data[ 'html' ] .= "</div>";
		}

		$data[ 'html' ] .= "</table>";
	}
	else{
		$data[ 'error' ] = 1;
	}


	return $data;
}


/****************************************************************************************************
 * Consulto el tipo de atención según el cco
 ****************************************************************************************************/
function consultarTipoAtencion( $wbasedato, $cco ){

	global $key;
	global $conex;

	$data = array('error'=>0,'mensaje'=>'','html'=>'');

	$sql = "SELECT
				Ccotat
			FROM
				".$wbasedato."_000011
			WHERE
				Ccocod = '$cco'
				AND ccoest = 'on'
			";

	$res = mysql_query( $sql, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query $sql - ".mysql_error() ) );

	if( $rows = mysql_fetch_array( $res ) )
	{
		$val = $rows[ 'Ccotat' ]; //sale el mensaje de error
	}

	return $val;
}


/****************************************************************************************************
 * Consulto el tipo de atención según el cco
 ****************************************************************************************************/
function consultarTipoIngreso( $wbasedato, $cco ){

	global $key;
	global $conex;

	$val = '';

	$sql = "SELECT
				Ccotin
			FROM
				".$wbasedato."_000011
			WHERE
				Ccocod = '$cco'
				AND Ccoest = 'on'
			";

	$res = mysql_query( $sql, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query $sql - ".mysql_error() ) );

	if( $rows = mysql_fetch_array( $res ) )
	{
		$val = $rows[ 'Ccotin' ]; //sale el mensaje de error
	}

	return $val;
}

/****************************************************************************************************
 * Consulto el tipo de atención según el cco
 ****************************************************************************************************/
function consultarTipoServicio( $wbasedato, $cco ){

	global $key;
	global $conex;

	$val = '';

	$sql = "SELECT
				Ccosei
			FROM
				".$wbasedato."_000011
			WHERE
				Ccocod = '$cco'
				AND Ccoest = 'on'
			";

	$res = mysql_query( $sql, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query $sql - ".mysql_error() ) );

	if( $rows = mysql_fetch_array( $res ) )
	{
		$val = $rows[ 'Ccosei' ]; //sale el mensaje de error
	}

	return $val;
}

function consultarAseguradorasVehiculo( $aseg, $wbasedato ){

		global $conex;




		$val = "";
		//Aseguradora de vehiculos
	 	$sql = "SELECT Asecod, Asedes
				FROM ".$wbasedato."_000193
				WHERE ( Asedes LIKE '%".utf8_decode($aseg)."%' OR Asecod LIKE '%".utf8_decode($aseg)."%' )
				and Aseest = 'on'
				and Asecoe != ''
				ORDER BY Asedes
				";

		$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );

		if( $num > 0 ){

			for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){

					$rows[ 'Asecod' ] = trim( utf8_encode($rows[ 'Asecod' ]) );
					$rows[ 'Asedes' ] = trim( utf8_encode($rows[ 'Asedes' ] ) );

					//Creo el resultado como un json
					//Primero creo un array con los valores necesarios
					$data[ 'valor' ] = Array( "cod"=> $rows[ 'Asecod' ], "des"=> $rows[ 'Asedes' ] );	//Este es el dato a procesar en javascript
					$data[ 'usu' ] = "{$rows[ 'Asecod' ]}-{$rows[ 'Asedes' ]}";	//Este es el que ve el usuario
					$dat = Array();
					$dat[] = $data;

					$val .= json_encode( $dat )."\n";

			}
		}

		return $val;
}

function consultaNombreAseguradoraVehiculo($codAse)
{
	global $conex;
	global $wbasedato;
	//consultar codigo aseguradora
			 $sql1="select Asecod, Asedes
				FROM ".$wbasedato."_000193
				where Asecod = '".$codAse."'
				";
			$res1 = mysql_query( $sql1, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando las aseguradoras de vehiculos ".mysql_errno()." - Error en el query $sql1 - ".mysql_error() ) );

	return $res1;
}

function consultarTipoEvento($tipo)
{
	global $conex;
	global $wbasedato;

	$sql = "SELECT
				*
			FROM
				".$wbasedato."_000154
			WHERE
				Evncod = '".$tipo."'
				AND Evnest = 'on'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	if ($num > 0)
	{
		$rows=mysql_fetch_array($res);
		$evento = $rows['Evndes'];
	}
	return $evento;
}

function guardarRelacion($historia, $ingreso, $hidcodEvento)
{
	global $wbasedato;
	global $conex;



	$data = Array("error" => 0,"html" => "","data" => "", "mensaje" => "");

	if( !empty( $historia ) && !empty( $ingreso ) && !empty( $hidcodEvento ) )
	{
		$sql = "SELECT Evnhis,Evning,Evncod,Evnest
				FROM ".$wbasedato."_000150
				WHERE Evnhis = '".$historia."'
				AND Evning = '".$ingreso."'

				";
	$res = mysql_query( $sql, $conex ) or ( $data[ 'error' ] = utf8_encode( mysql_errno()." - Error en el query $sql - ".mysql_error() ) );

		if( $res )
		{
			$num = mysql_num_rows( $res );

			//Si no se encontraron los datos, significa que es un registro nuevo evento catastrofico
				if( $num == 0 )
				{

					$datosEnc[ "Evnhis" ] = $historia;
					$datosEnc[ "Evning" ] = $ingreso;
					$datosEnc[ "Evncod" ] = $hidcodEvento;
					$datosEnc[ "Evnest" ] = "on";
					$datosEnc[ "Medico" ] = $wbasedato;
					$datosEnc[ "Fecha_data" ] = date( "Y-m-d" );
					$datosEnc[ "Hora_data" ] = date( "H:i:s" );
					$datosEnc[ "Seguridad" ] = "C-".$wbasedato;

					$sqlInsert = crearStringInsert( $wbasedato."_000150", $datosEnc );

					$resEnc = mysql_query( $sqlInsert, $conex ) or die( mysql_error() );

					if ($resEnc)
					{
						$data['mensaje'] = utf8_encode( "Se registró correctamente " );
					}
					else
					{
						$data['error']=1;
						$data['mensaje']=utf8_encode( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() );
					}
				}
				else //ya se tienen registros se hace la actualizacion
				{
					$sqlUpdate = "update ".$wbasedato."_000150
								  set Evncod = '".$hidcodEvento."',
								  Fecha_data = '".date( "Y-m-d" )."',
								  Hora_data = '".date( "H:i:s" )."'
								  where Evnhis = '".$historia."'
								  and Evning = '".$ingreso."'
								  ";

					$resUpdate = mysql_query($sqlUpdate, $conex) or die(mysql_error());

					if ($resUpdate)
					{
						$data['mensaje']="Se actualizo correctamente";
					}
					else
					{
						$data['error']=1;
						$data['mensaje']=utf8_encode( mysql_errno()." - Error en el query $sqlUpdate - ".mysql_error() );
					}
				}
		}
		else
		{
			$data['error']=1;
			$data['mensaje']=utf8_encode( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		}



	}
   return $data;
}


// if (isset($accion) and $accion == 'listaEventos')
function listaEventos()
{
	global $wbasedato;
	global $conex;

	$data= array('error'=>0,'mensaje'=>'','html'=>'');
	//se hace la consulta de las tablas de eventos catastroficos 000149 - 000150
	$sqlEv="select Devcod,	Deveve,	Devdir,	Devded,	Devfac,	Devhac,	Devdep,	Devmun,	Devzon,	Devdes, Devest
			from ".$wbasedato."_000149
			where Devest = 'on'";
	$resEv= mysql_query( $sqlEv, $conex );

	if ($resEv)
	{
		$data['html']="<div id='div_eventos_catastroficos'>";
		$data['html'].="<center><table border=0>";
		$data['html'].="<th class='encabezadotabla' align=center colspan='5'>LISTA EVENTOS CATASTR&Oacute;FICOS </th>";
		$data['html'] .= "<tr class='encabezadotabla' align=center>";
						$data['html'] .= "<td align='center' style=''>Relacionar</td>";
						$data['html'] .= "<td align='center' style=''>C&oacute;digo</td>";
						$data['html'] .= "<td align='center' style=''>Tipo de evento</td>";
						$data['html'] .= "<td align='center' style=''>Fecha que ocurrio el evento</td>";
						$data['html'] .= "<td align='center' style=''>Hora que ocurrio el evento</td>";
					$data['html'] .= "</tr>";
		for( $i = 0; $rows = mysql_fetch_array($resEv,MYSQL_ASSOC); $i++ ) //MYSQL_ASSOC se coloca para que devuelva solo los nombres de los campos
		{
			$evento=consultarTipoEvento($rows['Deveve']);

			( $class == "class='fila1'" ) ? $class = "class='fila2'" : $class = "class='fila1'";

			$data['html'].="<tr style='cursor:pointer' $class align=center onclick='mostrarDetalleEventosCatastroficos(\"".$rows['Devcod']."\")'>";
				$data['html'] .= "<td align='center' style=''><input type='checkbox' name='chkagregar_".$rows['Devcod']."' id='chkagregar_".$rows['Devcod']."' onclick='seleccionarCheckbox(this,".$rows['Devcod']."); cancelarEvento(event)'></td>";
				$data['html'] .= "<td align='center' style=''>".$rows['Devcod']."</td>";
				$data['html'] .= "<td align='center' style=''>".utf8_encode($evento)."</td>";
				$data['html'] .= "<td align='center' style=''>".$rows['Devfac']."</td>";
				$data['html'] .= "<td align='center' style=''>".$rows['Devhac']."</td>";
			$data['html'] .= "</tr>";
		}

		$data['html'].="<tr align='center'><td colspan='5'>";
		$data['html'].="<input type='button' id='btnGuardarEvento' value='Guardar' style='width:80;height:25' onClick='guardarRelacionHistoriaEvento();'>";
		$data['html'].="<input type='button' id='btnNuevoEvento' value='Nuevo Evento Catastr&oacute;fico' style='width:180;height:25' onClick='resetearEventosCatastroficos(); mostrarEventosCatastroficos();'>";
		$data['html'].="<input type='button' id='btnSalirEvento' value='Salir sin guardar' style='width:120;height:25' onClick='cerrarEventosCatastroficos();'>";
		$data['html'].="</td></tr>";
		$data['html'].="</table></center>";
		$data['html'].="<input type='hidden' id='hidcodEvento' id='hidcodEvento' value=''>";
		$data['html'].="</div>";
	}
	else
	{
		$data['mensaje']="No se ejecuto la consulta a la tabla ".$wbasedato."-000149 $sqlEv".mysql_error()."";
	}
	//echo json_encode($data);

	return $data;
}

function formato($numero)
{
	if($numero!='')
		return number_format((double)$numero,0,'.',',');
	else
		return $numero;
}
//se abre el formulario cuando se oprime el boton de topes
function mostrarFormTopes($responsable,$historia,$ingreso,$documento , $tipodocumento,$esadmision)
{
	global $wbasedato;
	global $conex;
	global $id;
	global $wemp_pmla;

	if($esadmision=='si')
	{
			//--------- se debe buscar que topes ya tiene el paciente.
			$selecttopes = "SELECT Tophis, Toping, Topres, Toptco, Topcla, Topcco, Toptop, Toprec, Topdia, Topsal, Topest, Topfec
							  FROM ".$wbasedato."_000204
							 WHERE  Tophis = '".$historia."'
							   AND  Toping = '".$ingreso."'
							   AND  Topres = '".$responsable."'";
			$restopes = mysql_query( $selecttopes, $conex )  or die( mysql_errno()." - Error en el query $select - ".mysql_error() );
			$vectortopesgrabados = array();
			$vectortopesgrabados = array();
			while( $rowstopes = mysql_fetch_array( $restopes) )
			{
				$vectortopesgrabados2[$rowstopes['Toptco']]['tip']='s';
				$vectortopesgrabados[$rowstopes['Toptco']][$rowstopes['Topcla']]['top'] = $rowstopes['Toptop'];
				$vectortopesgrabados[$rowstopes['Toptco']][$rowstopes['Topcla']]['rec'] = $rowstopes['Toprec'];
				$vectortopesgrabados[$rowstopes['Toptco']][$rowstopes['Topcla']]['dia'] = $rowstopes['Topdia'];
				$vectortopesgrabados[$rowstopes['Toptco']][$rowstopes['Topcla']]['sal'] = $rowstopes['Topsal'];
				$vectortopesgrabados[$rowstopes['Toptco']][$rowstopes['Topcla']]['fec'] = $rowstopes['Topfec'];
				$vectortopesgrabados[$rowstopes['Toptco']][$rowstopes['Topcla']]['cco'] = $rowstopes['Topcco'];

			}
	}
	else
	{
			//--------- se debe buscar que topes ya tiene el paciente.
			$selecttopes = "SELECT  Topres, Toptco, Topcla, Topcco, Toptop, Toprec, Topdia, Topsal, Topest, Topfec
							  FROM ".$wbasedato."_000215
							 WHERE  Toptdo = '".$tipodocumento."'
							   AND  Topdoc = '".$documento."'
							   AND  Topres = '".$responsable."'";
			$restopes = mysql_query( $selecttopes, $conex )  or die( mysql_errno()." - Error en el query $select - ".mysql_error() );
			$vectortopesgrabados = array();
			$vectortopesgrabados = array();
			while( $rowstopes = mysql_fetch_array( $restopes) )
			{
				$vectortopesgrabados2[$rowstopes['Toptco']]['tip']='s';
				$vectortopesgrabados[$rowstopes['Toptco']][$rowstopes['Topcla']]['top'] = $rowstopes['Toptop'];
				$vectortopesgrabados[$rowstopes['Toptco']][$rowstopes['Topcla']]['rec'] = $rowstopes['Toprec'];
				$vectortopesgrabados[$rowstopes['Toptco']][$rowstopes['Topcla']]['dia'] = $rowstopes['Topdia'];
				$vectortopesgrabados[$rowstopes['Toptco']][$rowstopes['Topcla']]['sal'] = $rowstopes['Topsal'];
				$vectortopesgrabados[$rowstopes['Toptco']][$rowstopes['Topcla']]['fec'] = $rowstopes['Topfec'];
				$vectortopesgrabados[$rowstopes['Toptco']][$rowstopes['Topcla']]['cco'] = $rowstopes['Topcco'];

			}

	}


	// creo vector de tipos de concepto y  topes que estan relacionados
	$select  = "SELECT Ccfcod ,Cpgcod, Cpgnom
				  FROM ".$wbasedato."_000202 , ".$wbasedato."_000203
				 WHERE Ccfcod = Cpgccf
				   AND Cpgest = 'on'
				   AND Cpgtda != 'off'";
	$res = mysql_query( $select, $conex )  or die( mysql_errno()." - Error en el query $select - ".mysql_error() );
	$arraytiposdeConcepto = array ();
	while( $rows = mysql_fetch_array( $res) )
	{
		$arraytiposdeConcepto[$rows['Ccfcod']][$rows['Cpgcod']] = $rows['Cpgnom'];
	}


	$json_vector = json_encode($arraytiposdeConcepto);
	//$json_vector = str_replace("\"","'",$arraytiposdeConcepto);



	$alias="movhos";
	$aplicacion=consultarAplicacion2($conex,$wemp_pmla,$alias);


	// Array ccos
	$selectcco = "SELECT Ccocod ,	Cconom
				 FROM ".$aplicacion."_000011
				WHERE Ccoest ='on'
				ORDER BY Cconom";
	$res = mysql_query( $selectcco, $conex )  or die( mysql_errno()." - Error en el query $selectcco - ".mysql_error() );
	$arrayccos = array ();
	$selectcco2 .= "<option value='*' selected>Todos</option>";
	while( $rows = mysql_fetch_array( $res) )
	{
		$arrayccos[$rows['Ccocod']] = $rows['Cconom'];
		$selectcco2 .= "<option value='".$rows['Ccocod']."'>".$rows['Ccocod']."-".utf8_decode($rows['Cconom'])."</option>";

	}


	$json_vectorcco = json_encode($arrayccos);

	//---------------------------------------------

	$data= array('error'=>0,'mensaje'=>'','html'=>'');


	//tabla de datos topes
	//$data['html'].="<div  id='div_cont_tabla_topes".$id."'>";

	//$data['html'].="<input type='hidden' id='vectorClasificacionConceptos' valor='".$json_vector."'><input type='hidden' id='vectorccostopes' valor='".$json_vectorcco."'>";
	// nuevo
	//$data['html'].="<br><br><input type='button' value='Agregar tipo de Concepto'   onclick='agregarConceptoTope()'><br>
	//<input type='hidden' id='responsabletope' value='".$responsable."'>
	$data['html'].="<br><div id='divtabletopes_".$responsable."' style='text-align: center;  width:100%;' ><table class='tablaTopes'  id='tablaTopes' width='100%'>
								<tr style='font-size:10pt' >
											 <td width='5%'>&nbsp;</td>
											 <td   class='encabezadoTabla' align='center' width='20%'><b>Tipo de Concepto</b></td>
											 <td   class='encabezadoTabla' align='center' width='20%' ><b>Centro de Costos</b></td>
											 <td   class='encabezadoTabla' align='center' width='15%'><b>($)Tope</b></td>
											 <td   class='encabezadoTabla' align='center' width='10%'><b>(%)Porcentaje</b></td>
											 <td   class='encabezadoTabla' align='center' width='10%'><b>Diario</b></td>
											 <td   class='encabezadoTabla' align='center' width='10%' ><b>Fecha Inicial</b></td>
								</tr>";



	$data['html'].="<tr class='trtopeppalgeneral' >
											 <td  >&nbsp;</td>
											 <td style='font-size:10pt;' class='encabezadoTabla' align='left' colspan='6'><b>Todos los conceptos</b></td></tr>
											 <tr>
												<td >&nbsp;</td><td class='tdgeneral' style='font-size:8pt;  background-color: #83D8F7;' align='center' ><b>Todas las clasificaciones</b></td>";



	if(isset($vectortopesgrabados['*']['*']['cco']))
	{
		$data['html'] .="<td class='fila1 tdgeneral' style='font-size:8pt;  background-color: #83D8F7;' width='15%'><select id='selectccogeneral'>";
		$rescco = mysql_query( $selectcco, $conex )  or die( mysql_errno()." - Error en el query $selectcco - ".mysql_error() );
		$selectcco4 ="<option value='*'>Todos</option>";
		while( $rowscco = mysql_fetch_array( $rescco) )
		{


			if($vectortopesgrabados['*']['*']['cco'] == $rowscco['Ccocod'])
				$selectcco4 .= "<option  selected value='".$rowscco['Ccocod']."'>".$rowscco['Ccocod']."-".utf8_decode($rowscco['Cconom'])."</option>";
			else
				$selectcco4 .= "<option   value='".$rowscco['Ccocod']."'>".$rowscco['Ccocod']."-".utf8_decode($rowscco['Cconom'])."</option>";


		}
		$data['html'] .= $selectcco4."</select></td>";
	}
	else
	{
		$data['html'].="<td  class='tdgeneral' style='font-size:8pt;  background-color: #83D8F7;' width='15%'><select id='selectccogeneral'  >".$selectcco2."</select></td>";
	}


											// <td class='fila1' style='font-size:8pt' width='15%'><select id='selectccogeneral'  >".$selectcco2."</select></td>
	$data['html'].=" <td   class='tdgeneral' style='font-size:8pt;  background-color: #83D8F7;' width='8%'><input type='text' style='text-align:right;' class='valortope validaciontope' id='valortopegeneral'  ".( ( isset($vectortopesgrabados['*']['*']['top']) ) ? "       value='".formato($vectortopesgrabados['*']['*']['top'])."'" : '' )."></td>
											 <td  class='tdgeneral' style='font-size:8pt;  background-color: #83D8F7;' width='4%'><input type='text' class='portope validaciontope' style='text-align:right;' id='porcentajegeneral'   ".( ( isset($vectortopesgrabados['*']['*']['rec']) ) ? "       value='".$vectortopesgrabados['*']['*']['rec']."'" : '' )."></td>
											 <td  class='tdgeneral' style='font-size:8pt;  background-color: #83D8F7;' width='4%'><input type='checkbox' value='off' class='validaciontope' id='diariotopegeneral' ".( ( ($vectortopesgrabados['*']['*']['dia']=='on') ) ?     "checked" : '' )." ></td>
											 <td  class='tdgeneral' style='font-size:8pt;  background-color: #83D8F7;' width='5%' align='center'><input style='width:100%' type='text' id='fechatopegeneral' class='datepickertopes validaciontope'  ".( ( isset($vectortopesgrabados['*']['*']['fec']) ) ?    " value='".$vectortopesgrabados['*']['*']['fec']."'" : '' )."></td>
											 <td  width='4%'></td>
								</tr>";

	$data['html'].="<tr>
						<td  >&nbsp;</td>
						<td>&nbsp;&nbsp;&nbsp;<br></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>";

	$res1=consultaMaestros('000202','Ccfcod,Ccfnom',$where="Ccfest = 'on'",'','','2');
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

			$data['html'] .="<tr   codigotope='".$value."' clasificacion='*' >
								<td  >&nbsp;</td><td align='left' class='encabezadoTabla' style='font-size:10pt' colspan='6'>".$value."-".substr( $des, 1 )."</td></tr>";
			$data['html'] .= "<tr  align='center' class='trtopeppal'  codigotope='".$value."' clasificacion='*' >
												 <td  >&nbsp;</td>
												 <td align='left' class='fila1 tdmedio tdmedio_".$value."' style='font-size:8pt' id='tdClasificacionTope' width='22%' nowrap='nowrap' >
													<div style='display:inline-block;width:80%' align='left' >Todas las clasificaciones</div><div id='detallartope_".$value."' style='cursor:pointer;display:inline-block;align:right;display:inline-block;color:#444;border:1px solid #CCC;background:#DDD;box-shadow: 0 0 5px -1px rgba(0,0,0,0.2);cursor:pointer;vertical-align:middle;max-width: 100px;padding: 5px;text-align: center;'  attrtope = '".$value."' onclick='detallartope(this)' >Detallar</div>
												 </td>

												";

			if(isset($vectortopesgrabados[$value]['*']['cco']))
			{
				$data['html'] .="<td class='fila1 tdmedio tdmedio_".$value."' style='font-size:8pt' width='15%'><select id='selectccotopesppal_".$value."'  >";

				$rescco = mysql_query( $selectcco, $conex )  or die( mysql_errno()." - Error en el query $selectcco - ".mysql_error() );
				$selectcco3 ="<option value='*'>Todos</option>";
				while( $rowscco = mysql_fetch_array( $rescco) )
				{
					if($vectortopesgrabados[$value]['*']['cco'] == $rowscco['Ccocod'])
						$selectcco3 .= "<option  selected value='".$rowscco['Ccocod']."'>".$rowscco['Ccocod']."-".utf8_decode($rowscco['Cconom'])."</option>";
					else
						$selectcco3 .= "<option   value='".$rowscco['Ccocod']."'>".$rowscco['Ccocod']."-".utf8_decode($rowscco['Cconom'])."</option>";

				}



				$data['html'] .="".$selectcco3."</select></td>";
			}
			else
			{
				$data['html'] .="<td class='fila1 tdmedio tdmedio_".$value."' style='font-size:8pt' width='15%'><select id='selectccotopesppal_".$value."'  >".$selectcco2."</select></td>";
			}

			$data['html'] .="<td class='fila1 tdmedio tdmedio_".$value."' style='font-size:8pt' width='8%'><input type='text' style='text-align:right;'  attrvalor ='".$value."' class='valortope validaciontope2 validaciontope2_".$value."' id='valortopeppal_".$value."'  ".( ( isset($vectortopesgrabados[$value]['*']['top']) ) ? "       value='".formato($vectortopesgrabados[$value]['*']['top'])."'" : '' )."></td>
												 <td class='fila1 tdmedio tdmedio_".$value."' style='font-size:8pt' width='4%'><input type='text' class='portope validaciontope2 validaciontope2_".$value."'  attrvalor ='".$value."' style='text-align:right;' id='porcentajetopeppal_".$value."'   ".( ( isset($vectortopesgrabados[$value]['*']['rec']) ) ? "       value='".$vectortopesgrabados[$value]['*']['rec']."'" : '' )."></td>
												 <td class='fila1 tdmedio tdmedio_".$value."' style='font-size:8pt' width='4%'><input type='checkbox' value='off' class='validaciontope2 validaciontope2_".$value."' attrvalor ='".$value."' id='diariotopeppal_".$value."' ".( ( ($vectortopesgrabados[$value]['*']['dia']=='on') ) ?     "checked" : '' )." ></td>
												 <td class='fila1 tdmedio tdmedio_".$value."' style='font-size:8pt' width='5%'><input style='width:100%' type='text' id='fechatopeppal_".$value."'  attrvalor ='".$value."'  class='datepickertopes validaciontope2 validaciontope2_".$value."'  ".( ( isset($vectortopesgrabados[$value]['*']['fec']) ) ?    " value='".$vectortopesgrabados[$value]['*']['fec']."'" : '' )."></td>
												 </tr>";

			$select  = "SELECT Ccfcod ,Cpgcod, Cpgnom
						  FROM ".$wbasedato."_000202 , ".$wbasedato."_000203
						 WHERE Ccfcod = Cpgccf
						   AND Cpgest = 'on'
						   AND Ccfcod ='".$value."'
						   AND Cpgtda !='off'";
			$res = mysql_query( $select, $conex )  or die( mysql_errno()." - Error en el query $select - ".mysql_error() );
			$condicion = "";
			if(isset($vectortopesgrabados2[$value]['tip']))
			{
				$condicion = "";
			}
			else
			{
				$condicion ="style='display:none'";
			}
			while( $rows = mysql_fetch_array( $res) )
			{

				/*$vectortopesgrabados[$rowstopes['Toptco']][$rowstopes['Topcla']]['top'] = $rowstopes['Toptop'];
				$vectortopesgrabados[$rowstopes['Toptco']][$rowstopes['Topcla']]['rec'] = $rowstopes['Toprec'];
				$vectortopesgrabados[$rowstopes['Toptco']][$rowstopes['Topcla']]['dia'] = $rowstopes['Topdia'];
				$vectortopesgrabados[$rowstopes['Toptco']][$rowstopes['Topcla']]['sal'] = $rowstopes['Topsal'];
				$vectortopesgrabados[$rowstopes['Toptco']][$rowstopes['Topcla']]['fec'] = $rowstopes['Topest'];
				$vectortopesgrabados[$rowstopes['Toptco']][$rowstopes['Topcla']]['cco'] = $rowstopes['Topcco'];*/


				$data['html'] .= "<tr class='detalletope_".$value."'  clasificacion='".$rows['Cpgcod']."' ".$condicion." >
													 <td  >&nbsp;</td>
													 <td class='fila2 tdminimo tdminimo_".$value."' style='font-size:8pt' id='tdClasificacionTope_".$value."_".$rows['Cpgcod']."'  valor='".$rows['Cpgcod']."'  width='22%' >".$rows['Cpgcod']."-".utf8_decode($rows['Cpgnom'])."</td>
								                     <td class='fila2 tdminimo tdminimo_".$value."' style='font-size:8pt' width='15%'>";
				if(isset($vectortopesgrabados[$value][$rows['Cpgcod']]['cco']))
				{
					$rescco = mysql_query( $selectcco, $conex )  or die( mysql_errno()." - Error en el query $selectcco - ".mysql_error() );
					$selectcco3 ="<option value='*'>Todos</option>";
					while( $rowscco = mysql_fetch_array( $rescco) )
					{
						if($vectortopesgrabados[$value][$rows['Cpgcod']]['cco'] == $rowscco['Ccocod'])
							$selectcco3 .= "<option  selected value='".$rowscco['Ccocod']."'>".$rowscco['Ccocod']."-".utf8_decode($rowscco['Cconom'])."</option>";
						else
							$selectcco3 .= "<option   value='".$rowscco['Ccocod']."'>".$rowscco['Ccocod']."-".utf8_decode($rowscco['Cconom'])."</option>";

					}


					$data['html'] .= "<select id='selectccotopes_".$value."_".$rows['Cpgcod']."' clasificacion='*'>".$selectcco3."</select>";
				}
				else
				{
					$data['html'] .= "<select id='selectccotopes_".$value."_".$rows['Cpgcod']."' clasificacion='*'>".$selectcco2."</select>";

				}
				$data['html'] .= "					</td>
													 <td class='fila2 tdminimo tdminimo_".$value."' style='font-size:8pt' width='8%' ><input style='text-align:right;'  class='valortope validaciontope3 validaciontope3_".$value."' attrvalor ='".$value."' type='text' id='valortopedetalle_".$value."_".$rows['Cpgcod']."'  ".( ( isset($vectortopesgrabados[$value][$rows['Cpgcod']]['top']) ) ? "       value='".formato($vectortopesgrabados[$value][$rows['Cpgcod']]['top'])."'" : '' )."></td>
													 <td class='fila2 tdminimo tdminimo_".$value."' style='font-size:8pt' width='4%'  ><input  style='text-align:right;'  class='portope validaciontope3 validaciontope3_".$value."' attrvalor ='".$value."' type='text' id='porcentajetopedetalle_".$value."_".$rows['Cpgcod']."'  ".( ( isset($vectortopesgrabados[$value][$rows['Cpgcod']]['rec']) ) ? " value='".$vectortopesgrabados[$value][$rows['Cpgcod']]['rec']."'" : '' )."></td>
													 <td class='fila2 tdminimo tdminimo_".$value."' style='font-size:8pt' width='4%'><input type='checkbox' class='validaciontope3 validaciontope3_".$value."'  attrvalor ='".$value."' id='diariotopedetalle_".$value."_".$rows['Cpgcod']."' ".( ( ($vectortopesgrabados[$value][$rows['Cpgcod']]['dia']=='on') ) ?     "checked" : '' )." ></td>
													 <td class='fila2 tdminimo tdminimo_".$value."' style='font-size:8pt' width='5%' align='center'><input style='width:100%' type='text' class='datepickertopes validaciontope3 validaciontope3_".$value."'  attrvalor ='".$value."' id='fechatopedetalle_".$value."_".$rows['Cpgcod']."' ".( ( isset($vectortopesgrabados[$value][$rows['Cpgcod']]['fec']) ) ?    " value='".$vectortopesgrabados[$value][$rows['Cpgcod']]['fec']."'" : '' )."></td>
													 <td  style='font-size:8pt' width='4%'></td></tr>";
				//$data['html'].="<td><input type='text' name='top_ccotxtCcoTop' id='top_ccotxtCcoTop".$id_fila."' value='TODOS LOS CENTROS DE COSTO' class='' ux='' placeholder='Ingrese el centro de costo' onBlur=\"valRepetidosTopes('f.$id_fila');\">
			//<input type='hidden' name='top_ccohidCcoTop' id='top_ccohidCcoTop".$id_fila."' value='*' class='' ux='' >";
			}

			//


		}
	}
	$data['html'] .="</table></div><br><br>";
	/*
	$data['html'] .= "<tr class='fila2' ><td id='tdTipoConceptoTope' ><select onChange='seleccionarClasificacionTopes(this)'>";
	$num = mysql_num_rows( $res1 );
	if( $num > 0 )
	{
		$data['html'].= "<option value=''>Seleccione...</option>";
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

			$data['html'].= "<option value='{$value}' >".substr( $des, 1 )."</option>";
		}
	}




	$data['html'].="</select></td><td id='tdClasificacionTope' ></td><td ></td><td></td><td  ></td><td ></td><td></td></tr>";
	$data['html'].="</table><br><br>";
	*/

	/*$data['html'].="<table id='tabla_topes'>";
	$data['html'].="<th colspan='7' class='encabezadotabla'>TOPES </th>";
	$data['html'].="<tr class='encabezadotabla'>";
	$data['html'].="<td>Tipos de Conceptos</td><td>Clasificaci&oacute;n</td><td>Centro de Costo</td><td>($)Tope</td><td>(%)Reconocido</td><td>Diario</td>";
	$data['html'].="<td align='center'><span onclick=\"addFila('tabla_topes','$wbasedato','$wemp_pmla');\" class='efecto_boton' >".NOMBRE_ADICIONAR."</span></td>";
	$data['html'].="</tr>";

	$id_fila = "1_tr_tabla_topes";
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
			<input type='hidden' name='top_clahidClaTop' id='top_clahidClaTop".$id_fila."' class='' ux='' value='*' >";
			$data['html'].="</td>";
			$data['html'].="<td><input type='text' name='top_ccotxtCcoTop' id='top_ccotxtCcoTop".$id_fila."' value='TODOS LOS CENTROS DE COSTO' class='' ux='' placeholder='Ingrese el centro de costo' onBlur=\"valRepetidosTopes('f.$id_fila');\">
			<input type='hidden' name='top_ccohidCcoTop' id='top_ccohidCcoTop".$id_fila."' value='*' class='' ux='' >";
			$data['html'].="</td>";
			$data['html'].="<td><input type='text' name='top_toptxtValTop' id='top_toptxtValTop".$id_fila."' class='' ux='' placeholder='Ingrese el tope' onfocus=\"valNumero('top_toptxtValTop".$id_fila."');\">";
			$data['html'].="</td>";
			$data['html'].="<td><input type='text' name='top_rectxtValRec' id='top_rectxtValRec".$id_fila."' class='' ux='' placeholder='Ingrese el valor( % 0 a 100)' value='100' onBlur=\"valPorcentaje(this);\">";
			$data['html'].="<input type='hidden' name='top_id' id='top_id".$id_fila."' class='' value=''>";
			$data['html'].="</td>";
			$data['html'].="<td><input type='checkbox' name='top_diachkValDia' id='top_diachkValDia".$id_fila."' class='' ux='' >";
			$data['html'].="</td>";
			$data['html'].="<td align='center'><span class='efecto_boton' onclick='removerFila(\"".$id_fila."\",\"".$wbasedato."\",\"tabla_topes\");'>".NOMBRE_BORRAR."</span></td>";
			$data['html'].="</tr>";
			$data['html'].="</table>";



			//tabla de botones topes
			$data['html'].="<table style='width:100%' border='0'>";
			$data['html'].="<tr class='fondoamarillo'>";
			$data['html'].="<td align='center' class='fondoamarillo'>";
			$data['html'].="<input type='button' id='btnGuardarTopResp' name='btnGuardarTopResp' value='Guardar' style='width:100;height:25' onClick=\"guardarTopePorResp();\">
							<input type='button' id='btnGuardarSalirTopResp' name='btnGuardarSalirTopResp' value='Salir sin Guardar' style='width:110;height:25' onClick=\"salir('div_cont_tabla_topes".$id."', true);\">";
			$data['html'].="</td>";
			$data['html'].="</tr>";
			$data['html'].="</table>";*/




		$data['html'].="</div>";





	return $data;


}

function registrarLogTopes($whistoria,$wingreso,$responsable,$cadenaTope,$idTope,$accionLog,$usuario)
{
	global $conex;
	global $wemp_pmla;
	global $wbasedato;

	$data = array();
	if($accionLog=="activo")
	{
		$insertLog = "INSERT INTO ".$wbasedato."_000315 (Medico,Fecha_data,Hora_data,Loghis,Loging,Logres,Loglog,Loguin,Logfin,Loghin,Logidt,Logest,Seguridad)
												 VALUES ('".$wbasedato."','".date("Y-m-d")."','".date( "H:i:s" )."','".$whistoria."','".$wingreso."','".$responsable."','".$cadenaTope."','','','','".$idTope."','on','C-".$usuario."');";

		$resInsertLog = mysql_query( $insertLog, $conex ) or die( $data[ 'mensaje' ] = utf8_encode( "Error insertando en la tabla de log de topes ".$wbasedato."_000315 ".mysql_errno()." - Error en el query ".$insertLog." - ".mysql_error() ) );

		$data['sqlLog'] = $insertLog;
	}
	else
	{
		$updateLog = " UPDATE ".$wbasedato."_000315
						  SET Logest='off',
							  Loguin='".$usuario."',
							  Logfin='".date("Y-m-d")."',
							  Loghin='".date( "H:i:s" )."'
						WHERE Loghis='".$whistoria."'
						  AND Loging='".$wingreso."'
						  AND Logres='".$responsable."'
						  AND Logidt='".$idTope."'
						  AND Logest='on';";

		$resUpdateDetalle = mysql_query($updateLog,$conex) or ($data['mensaje'] = utf8_encode( "Error actualizando en la tabla de log de topes ".$wbasedato."_000315 ".mysql_errno()." - Error en el query ".$updateLog." - ".mysql_error() ) );
		$numInactivarTope = mysql_affected_rows();

		$data['sqlLog'] = $updateLog;
	}

	return $data;
}

function grabartoperesponsable($whistoria,$wingreso,$responsable,$insertar,$activo,$esadmision,$documento,$tipodocumento)
{
	global $conex;
	global $wemp_pmla;
	global $wbasedato;
	global $user;

	$usuario = explode("-", $user);
	$usuario = $usuario[1];

	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "facturacion" );
	$texto = $insertar;
	$fechatope = date("Y-m-d");
	$horatope  = date( "H:i:s" );
    $vectortexto = explode("_",$texto);

	$arrayTopesActivos = array();

	// consultar los topes activos
	if($esadmision =='si')
	{
		$queryTopesActivos = "SELECT Toptco,Topcla,Topcco,Toptop,Toprec,Topdia,Topsal,Topfec,id
								FROM ".$wbasedato."_000204
							   WHERE Tophis='".$whistoria."'
								 AND Toping='".$wingreso."'
								 AND Topres='".$responsable."'
								 AND Topest='on';";

		$resTopesActivos = mysql_query($queryTopesActivos, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryTopesActivos . " - " . mysql_error());
		$numTopesActivos = mysql_num_rows($resTopesActivos);


		if($numTopesActivos>0)
		{
			while($rowTopesActivos = mysql_fetch_array($resTopesActivos))
			{
				$arrayTopesActivos[$rowTopesActivos['id']]['tipoConcepto'] = $rowTopesActivos['Toptco'];
				$arrayTopesActivos[$rowTopesActivos['id']]['clasificacion'] = $rowTopesActivos['Topcla'];
				$arrayTopesActivos[$rowTopesActivos['id']]['cco'] = $rowTopesActivos['Topcco'];
				$arrayTopesActivos[$rowTopesActivos['id']]['valorTope'] = $rowTopesActivos['Toptop'];
				$arrayTopesActivos[$rowTopesActivos['id']]['porcentaje'] = $rowTopesActivos['Toprec'];
				$arrayTopesActivos[$rowTopesActivos['id']]['esDiario'] = $rowTopesActivos['Topdia'];
				$arrayTopesActivos[$rowTopesActivos['id']]['saldoTope'] = $rowTopesActivos['Topsal'];
				$arrayTopesActivos[$rowTopesActivos['id']]['fecha'] = $rowTopesActivos['Topfec'];
			}
		}

		// ------------

		$sqlDel="    DELETE
					   FROM {$wbasedato}_000204
					  WHERE tophis = '{$whistoria}'
						AND toping = '{$wingreso}'
						AND topres = '{$responsable}'";
		$resDel = mysql_query( $sqlDel, $conex ) or ( $data[ 'error' ] = utf8_encode( mysql_errno()." - Error en el query $sqlDel - ".mysql_error() ) );

	}
	else
	{
		$sqlDel="    DELETE
					   FROM {$wbasedato}_000215
					  WHERE Toptdo = '{$tipodocumento}'
						AND Topdoc = '{$documento}'
						AND Topres = '{$responsable}'";
		$resDel = mysql_query( $sqlDel, $conex ) or ( $data[ 'error' ] = utf8_encode( mysql_errno()." - Error en el query $sqlDel - ".mysql_error() ) );

	}
	$respuesta['borrado'] = $sqlDel;

	$respuesta = array();
	for($i=1; $i<count($vectortexto) ; $i++)
	{
		$nuevostring = $vectortexto[$i];

		$vectordetalle = explode(":",$nuevostring);

		// si el tope no existe debe insertarlo
		if($esadmision =='si')
		{
			$resultado ="INSERT INTO ".$wbasedato."_000204 ( Medico, Fecha_data, Hora_data, Tophis ,	Toping ,	Topres ,Toptco, 	Topcla, 	Topcco 	,Toptop ,	Toprec ,	Topdia 	,Topsal ,	Topest 	,Topfec ,Seguridad)
							VALUES ( '".$wbasedato."'  , '".$fechatope."' , '".$horatope."' ,'".$whistoria."', '".$wingreso."' , '".$responsable."' , '".$vectordetalle[2]."' , '".$vectordetalle[3]."','".$vectordetalle[4]."' , '".str_replace(',', '', $vectordetalle[5])."' , '".$vectordetalle[6]."', '".$vectordetalle[7]."'  , '".str_replace(',', '', $vectordetalle[5])."' , 'on' , '".$vectordetalle[8]."','C-".$usuario."' )";

			$res = mysql_query( $resultado, $conex ) or die( $data[ 'mensaje' ] = utf8_encode( "Error insertando en la tabla de topes ".$wbasedato." 204 ".mysql_errno()." - Error en el query $sql - ".mysql_error() ) );

			$numInsertTope = mysql_affected_rows();



			$existeTope = false;
			if(count($arrayTopesActivos)>0)
			{
				foreach($arrayTopesActivos as $keyTopes => $valueTopes)
				{
					$arrayTopesTemporal = array();
					$arrayTopesTemporal['tipoConcepto'] = $vectordetalle[2];
					$arrayTopesTemporal['clasificacion'] = $vectordetalle[3];
					$arrayTopesTemporal['cco'] = $vectordetalle[4];
					$arrayTopesTemporal['valorTope'] = str_replace(',', '', $vectordetalle[5]);
					$arrayTopesTemporal['porcentaje'] = $vectordetalle[6];
					$arrayTopesTemporal['esDiario'] = $vectordetalle[7];
					$arrayTopesTemporal['saldoTope'] = $valueTopes['saldoTope'];
					$arrayTopesTemporal['fecha'] = $vectordetalle[8];


					// var_dump(array_diff($valueTopes, $arrayTopesTemporal));

					// if(count(array_diff($valueTopes, $arrayTopesTemporal))==0)
					if(count(array_diff_assoc($valueTopes, $arrayTopesTemporal))==0)
					{
						unset($arrayTopesActivos[$keyTopes]);
						$existeTope = true;
						break;
					}
				}
			}

			if(!$existeTope && $numInsertTope>0)
			{
				// registrar el log de tope nuevo
				$idTope = mysql_insert_id();
				$sqlLog = registrarLogTopes($whistoria,$wingreso,$responsable,$nuevostring,$idTope,"activo",$usuario);
				$respuesta['logTopes'] .= $sqlLog['sqlLog']."\n";
			}
		}
		else
		{
			$resultado ="INSERT INTO ".$wbasedato."_000215 ( Medico, Fecha_data, Hora_data, Toptdo ,	Topdoc ,	Topres ,Toptco, 	Topcla, 	Topcco 	,Toptop ,	Toprec ,	Topdia 	,Topsal ,	Topest 	,Topfec ,Seguridad)
							VALUES ( '".$wbasedato."'  , '".$fechatope."' , '".$horatope."' ,'".$tipodocumento."', '".$documento."' , '".$responsable."' , '".$vectordetalle[2]."' , '".$vectordetalle[3]."','".$vectordetalle[4]."' , '".str_replace(',', '', $vectordetalle[5])."' , '".$vectordetalle[6]."', '".$vectordetalle[7]."'  , '".str_replace(',', '', $vectordetalle[5])."' , 'on' , '".$vectordetalle[8]."','C-".$usuario."' )";

			$res = mysql_query( $resultado, $conex ) or die( $data[ 'mensaje' ] = utf8_encode( "Error insertando en la tabla de topes ".$wbasedato." 204 ".mysql_errno()." - Error en el query $sql - ".mysql_error() ) );

		}
		$respuesta['insert'] .= $resultado."\n";


	}


	if($esadmision =='si')
	{
		// debe cambiar el estado a off de los topes que hayan quedado en el array
		if(count($arrayTopesActivos)>0)
		{
			foreach($arrayTopesActivos as $keyTopes => $valueTopes)
			{
				// cambiar el estado del log del tope a off
				$sqlLog = registrarLogTopes($whistoria,$wingreso,$responsable,"",$keyTopes,"inactivo",$usuario);
				$respuesta['logTopes'] .= $sqlLog['sqlLog']."\n";
			}
		}
	}


	return $respuesta;

}

function calculartopes2($responsable, $whistoria, $wingreso)
{
	global $conex;
	global $wemp_pmla;
	global $wbasedato;
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "facturacion" );
	$entro='no';

	// buscol las condiciones del paciente
	$select  = "SELECT Ingtin as tipoingreso,Ingtpa as tipopaciente ,  	Resord , Resnit
				  FROM ".$wbasedato."_000101 , ".$wbasedato."_000205
				 WHERE Inghis ='".$whistoria."'
				   AND Ingnin='".$wingreso."'
				   AND Inghis = Reshis
				   AND Ingnin = Resing
				   AND Resord = '1' ";


	$res = mysql_query( $select, $conex )  or die( mysql_errno()." - Error en el query $select - ".mysql_error() );

	//$retornar .=$select;
	$encuentrodatos ='no';
	if( $row = mysql_fetch_array( $res) )
	{

		$tipoingreso = $row['tipoingreso'];
		$tipopaciente = $row['tipopaciente'];
		$centroCostos = '';
		$accionLog = '';
		$responsable = $row['Resnit'];
		$permGrabarCargoCcoDifPda = 'on';
		$encuentrodatos='si';
	}


	if( $encuentrodatos == 'si')
	{


		// listo todos los cargos del paciente
		 $select = " SELECT  id
					  FROM ".$wbasedato."_000106
					 WHERE Tcarhis  = '".$whistoria."'
					   AND Tcaring  = '".$wingreso."'
					   AND Tcarest  = 'on'
					   AND Tcardoi  = '' ";

		//$retornar .=$select;
		$res = mysql_query( $select, $conex )  or die( mysql_errno()." - Error en el query $select - ".mysql_error() );
		//$retornar .="hola";
		$html="<table id='tablarecalculartopes'>";
		while( $row = mysql_fetch_array( $res) )
		{

			//$retornar['mensaje']= "<br>".$responsable."-".$tipoingreso."-".$tipopaciente."-".$centroCostos."-".$accionLog."-".$permGrabarCargoCcoDifPda."";

			//$retornar = regrabarCargo($row['id'],$responsable,$tipoingreso,$tipopaciente,$centroCostos, 'REGRABACION DESDE CARGOS', 'on');
			$html .="<tr><td class='tdrecalculartope'  idcargo='".$row['id']."' responsable='".$responsable."' tipoingreso='".$tipoingreso."' tipopaciente='".$tipopaciente."' centrocostos='".$centroCostos."'  log='REGRABACION DESDE CARGOS' pergrabarcargo='on' >id : ".$row['id']." responsable: ".$responsable." tipo ingreso: ".$tipoingreso." tipo paciente : ".$tipopaciente." centro costos: ".$centroCostos." log : REGRABACION DESDE CARGOS  pergrabarcargo: 'on'</td></tr>";
			$retornar['exito'] ='exito';
			$retornar['cargos']='si';
			$entro ='si';
		}
		$html.="</table>";
		if($entro =='no')
		{
			$html='no';//$retornar['cargos']='no';

		}
		//$idCargo, $responsble, $tipoIngreso, $tipoPaciente, $centroCostos, $accionLog, $permGrabarCargoCcoDifPda='off'


	}

	return $html;
}




/*function mostrarFormTopes()
{
	global $wbasedato;
	global $conex;
	global $id;
	global $wemp_pmla;

	$data= array('error'=>0,'mensaje'=>'','html'=>'');


			//tabla de datos topes
			$data['html']="<div id='div_cont_tabla_topes".$id."'>";
			$data['html'].="<table id='tabla_topes'>";
			$data['html'].="<th colspan='7' class='encabezadotabla'>TOPES</th>";
			$data['html'].="<tr class='encabezadotabla'>";
			$data['html'].="<td>Tipos de Conceptos</td><td>Clasificaci&oacute;n</td><td>Centro de Costo</td><td>($)Tope</td><td>(%)Reconocido</td><td>Diario</td>";
			$data['html'].="<td align='center'><span onclick=\"addFila('tabla_topes','$wbasedato','$wemp_pmla');\" class='efecto_boton' >".NOMBRE_ADICIONAR."</span></td>";
			$data['html'].="</tr>";

				$id_fila = "1_tr_tabla_topes";
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
				<input type='hidden' name='top_clahidClaTop' id='top_clahidClaTop".$id_fila."' class='' ux='' value='*' >";
				$data['html'].="</td>";
				$data['html'].="<td><input type='text' name='top_ccotxtCcoTop' id='top_ccotxtCcoTop".$id_fila."' value='TODOS LOS CENTROS DE COSTO' class='' ux='' placeholder='Ingrese el centro de costo' onBlur=\"valRepetidosTopes('f.$id_fila');\">
				<input type='hidden' name='top_ccohidCcoTop' id='top_ccohidCcoTop".$id_fila."' value='*' class='' ux='' >";
				$data['html'].="</td>";
				$data['html'].="<td><input type='text' name='top_toptxtValTop' id='top_toptxtValTop".$id_fila."' class='' ux='' placeholder='Ingrese el tope' onfocus=\"valNumero('top_toptxtValTop".$id_fila."');\">";
				$data['html'].="</td>";
				$data['html'].="<td><input type='text' name='top_rectxtValRec' id='top_rectxtValRec".$id_fila."' class='' ux='' placeholder='Ingrese el valor( % 0 a 100)' value='100' onBlur=\"valPorcentaje(this);\">";
				$data['html'].="<input type='hidden' name='top_id' id='top_id".$id_fila."' class='' value=''>";
				$data['html'].="</td>";
				$data['html'].="<td><input type='checkbox' name='top_diachkValDia' id='top_diachkValDia".$id_fila."' class='' ux='' >";
				$data['html'].="</td>";
				$data['html'].="<td align='center'><span class='efecto_boton' onclick='removerFila(\"".$id_fila."\",\"".$wbasedato."\",\"tabla_topes\");'>".NOMBRE_BORRAR."</span></td>";
				$data['html'].="</tr>";
				$data['html'].="</table>";


					//tabla de botones topes
					$data['html'].="<table style='width:100%' border='0'>";
					$data['html'].="<tr class='fondoamarillo'>";
					$data['html'].="<td align='center' class='fondoamarillo'>";
					$data['html'].="<input type='button' id='btnGuardarTopResp' name='btnGuardarTopResp' value='Guardar' style='width:100;height:25' onClick=\"guardarTopePorResp();\">
									<input type='button' id='btnGuardarSalirTopResp' name='btnGuardarSalirTopResp' value='Salir sin Guardar' style='width:110;height:25' onClick=\"salir('div_cont_tabla_topes".$id."', true);\">";
					$data['html'].="</td>";
					$data['html'].="</tr>";
					$data['html'].="</table>";




			$data['html'].="</div>";





	return $data;


}*/

function consultarCcoTopesResp( $cco,$wbasedato,$wemp_pmla )
{
		global $conex;




		$alias="movhos";
        $aplicacion=consultarAplicacion2($conex,$wemp_pmla,$alias);

		if ($aplicacion == "")
		{

			$sql = "SELECT Ccocod,Ccodes
					FROM ".$wbasedato."_000003
					WHERE Ccoest = 'on'
					AND Ccocod LIKE '%".utf8_decode($cco)."%' or Ccodes LIKE '%".utf8_decode($cco)."%'
				ORDER BY Ccodes LIMIT 25";
		}
		else
		{

			$sql = "SELECT Ccocod,Cconom as Ccodes
					FROM ".$aplicacion."_000011
					WHERE Ccoest = 'on'
					AND Ccocod LIKE '%".utf8_decode($cco)."%' or Cconom LIKE '%".utf8_decode($cco)."%'
				ORDER BY Cconom LIMIT 25";
		}
		$val = "";

		$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );

		if( $num > 0 ){

			for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){

					$rows[ 'Ccocod' ] = trim( utf8_encode($rows[ 'Ccocod' ]) );
					$rows[ 'Ccodes' ] = trim( utf8_encode($rows[ 'Ccodes' ]) );

					//Creo el resultado como un json
					//Primero creo un array con los valores necesarios
					$data[ 'valor' ] = Array( "cod"=> $rows[ 'Ccocod' ], "des"=> $rows[ 'Ccodes' ] );	//Este es el dato a procesar en javascript
					$data[ 'usu' ] = "{$rows[ 'Ccocod' ]}-{$rows[ 'Ccodes' ]}";	//Este es el que ve el usuario
					$dat = Array();
					$dat[] = $data;

					$val .= json_encode( $dat )."\n";

			}
		}

		return $val;
}

function consultarClasificacionConceptosFact( $cla,$wbasedato,$wemp_pmla )
{
		global $conex;
		global $tipo;




		 $sql = "SELECT Cpgcod,Cpgnom
					FROM ".$wbasedato."_000200 ,".$wbasedato."_000202, ".$wbasedato."_000203
					WHERE Gruest = 'on'
					AND Ccfest = 'on'
					AND Gruccf = '".$tipo."'
					AND Cpginv = Gruinv
					AND (Cpgcod LIKE '%".utf8_decode($cla)."%' or Cpgnom LIKE '%".utf8_decode($cla)."%')
					AND Cpgest = 'on'
					GROUP BY Cpgcod
					ORDER BY Cpgnom";

		$val = "";

		$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );

		if( $num > 0 ){

			for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){

					$rows[ 'Cpgcod' ] = trim( utf8_encode($rows[ 'Cpgcod' ]) );
					$rows[ 'Cpgnom' ] = trim( utf8_encode($rows[ 'Cpgnom' ]) );

					//Creo el resultado como un json
					//Primero creo un array con los valores necesarios
					$data[ 'valor' ] = Array( "cod"=> $rows[ 'Cpgcod' ], "des"=> $rows[ 'Cpgnom' ] );	//Este es el dato a procesar en javascript
					$data[ 'usu' ] = "{$rows[ 'Cpgcod' ]}-{$rows[ 'Cpgnom' ]}";	//Este es el que ve el usuario
					$dat = Array();
					$dat[] = $data;

					$val .= json_encode( $dat )."\n";

			}
		}


		return $val;
}

function consultaNombreConcepto($codCon)
{
	global $conex;
	global $wbasedato;
	//consultar codigo clasificacion procedimiento
			 $sql1="select Cpgcod,Cpgnom
				FROM ".$wbasedato."_000203
				where Cpgcod = '".$codCon."'
				and Cpgest = 'on'";
			$res1 = mysql_query( $sql1, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla de clasificacion de procedimientos ".mysql_errno()." - Error en el query $sql1 - ".mysql_error() ) );

	return $res1;
}

function consultaNombreCco($codCco)
{
	global $conex;
	global $wbasedato;
	global $wemp_pmla;

	$alias="movhos";
    $aplicacion=consultarAplicacion2($conex,$wemp_pmla,$alias);

		if ($aplicacion == "")
		{

			$sql = "SELECT Ccocod,Ccodes
					FROM ".$wbasedato."_000003
					WHERE Ccoest = 'on'
					AND Ccocod = '".utf8_decode($codCco)."'
				ORDER BY Ccodes";
		}
		else
		{

			$sql = "SELECT Ccocod,Cconom as Ccodes
					FROM ".$aplicacion."_000011
					WHERE Ccoest = 'on'
					AND Ccocod = '".utf8_decode($codCco)."'
				ORDER BY Cconom";
		}
			$res1 = mysql_query( $sql, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla de centros de costo ".mysql_errno()." - Error en el query $sql - ".mysql_error() ) );

	return $res1;
}


function validarUsuario(){
	global $conex;
	global $wbasedato;
	global $user;
	global $wemp_pmla;
	global $cco_usuario;
	global $cco_usuario_ayuda;
	global $where_lista_servicios;
	global $fecha;
	global $incremento;

	$user2 = explode("-",$user);
	( isset($user2[1]) )? $user2 = $user2[1] : $user2 = $user2[0];


	$cco_usuario = "";
	$cco_usuario_ayuda = "";

	$data = array('error'=>0,'mensaje'=>'','html'=>'');

	$aplMovhos=consultarAplicacion2($conex,$wemp_pmla,"movhos");
	$exentos_cco=consultarAplicacion2($conex,$wemp_pmla,"usersexentosadmisiones");
	$exentos_cco = explode( ",", $exentos_cco );

	$fecMostrarUnix = strtotime( $fecha ) + $incremento*3600*24;
	$fechaMostrar = date( "Y-m-d", $fecMostrarUnix );
	$fechaTitulo = nombreMes( date( "m", $fecMostrarUnix ) ).date( " d \d\e Y", $fecMostrarUnix );


	$where_lista_servicios = "";
	if( in_array($user2, $exentos_cco) == false ){
		//2014-03-19 VALIDAR CCO DEL USUARIO, Y MOSTRAR LAS ADMISIONES DE ESE CCO
		$sqlusu = "SELECT perccd as cco,Ccoing as ing,Ccoayu as ayu,Ccourg as urg,Ccocir as cir
					 FROM {$wbasedato}_000081, ".$aplMovhos."_000011
					WHERE Perusu = '".$user2."'
					  AND perccd = Ccocod
					  AND ( ccoing='on' or (ccoayu='on' and ccohos!='on') or ccourg='on' or ccocir='on' )
					  AND ccoest='on'";
		$resusu = mysql_query( $sqlusu, $conex ) or die( $data[ 'mensaje' ] = mysql_errno()." - Error en el query $sqlusu -".mysql_error() );

		$disabledAnulacion = '';
		$numusu = mysql_num_rows( $resusu );
		if( $numusu > 0 ){
			$rowusu = mysql_fetch_assoc( $resusu );
			$cco_usuario = $rowusu['cco'];
			$aux = 0;
			if( $rowusu['cir'] == 'on' ){
				$where_lista_servicios.= " ccocir='on' ";
				$aux=1;
			}
			if( $rowusu['urg'] == 'on' ){
				if( $aux == 1 ) $where_lista_servicios.= " or ";
				$where_lista_servicios.= " ccourg='on' ";
				$aux=1;
			}
			if( $rowusu['ayu'] == 'on' ){
				$condicionHibridos = "";
				$cco_usuario_ayuda = "on";

				if( $aux == 1 ) $where_lista_servicios.= " or ";
				$where_lista_servicios.= " (ccoayu='on' and ccohos!='on') or ( ccohos = 'on' and ccoing = 'on' and ccohib = 'on' ) ";
				$aux=1;
			}
			if( $rowusu['ing'] == 'on' ){
				if( $aux == 1 ) $where_lista_servicios.= " or ";
				$where_lista_servicios.= " ccoing='on' ";
				$aux=1;
			}
			if( $aux == 1 ) $where_lista_servicios = "(".$where_lista_servicios.")";
		}else{
			return false;
		}
	}

	return true;

}

function consultarMedicos( $med, $wbasedato, $aplicacion, $sinJson=false ){

	global $conex, $filtraEspecialidadClinica;

	$val = "";
	$data = array();

	if ($aplicacion == "")
	{
		$sql = "SELECT Medcod, Mednom,Medesp,Espnom
				FROM ".$wbasedato."_000051 LEFT JOIN ".$wbasedato."_000053 ON (Medesp=Espcod)
				WHERE (Medcod LIKE '%".utf8_decode($med)."%' or Mednom like '%".utf8_decode($med)."%')
				AND Medest ='on'
				ORDER BY Mednom
				LIMIT 30 ";

		$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );

		if( $num > 0 ){
			for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
				$rows[ 'Medcod' ] = trim( utf8_encode($rows[ 'Medcod' ]) );
				$rows[ 'Mednom' ] = trim( utf8_encode($rows[ 'Mednom' ]) );
				$rows[ 'Medesp' ] = trim( utf8_encode($rows[ 'Medesp' ]) );
				$rows[ 'Espnom' ] = trim( utf8_encode($rows[ 'Espnom' ]) );

				$pos = strpos($rows[ 'Medesp' ], "-");
				if ($pos !== false) {
					$aux = explode("-",$rows[ 'Medesp' ]);
					$rows[ 'Medesp' ] = $aux[0];
					$rows[ 'Espnom' ] = $aux[1];
				}

				if( $rows[ 'Medesp' ] == "" ) $rows[ 'Espnom' ] = "00000";
				if( $rows[ 'Espnom' ] == "" ) $rows[ 'Espnom' ] = "SIN DATOS";

				//Creo el resultado como un json
				//Primero creo un array con los valores necesarios
				$data[ 'valor' ] = Array( "cod"=> $rows[ 'Medcod' ], "des"=> $rows[ 'Mednom' ],"codesp"=> $rows[ 'Medesp' ], "desesp"=> $rows[ 'Espnom' ] );	//Este es el dato a procesar en javascript
				$data[ 'usu' ] = "{$rows[ 'Medcod' ]}-{$rows[ 'Mednom' ]}";	//Este es el que ve el usuario
				$dat = Array();
				$dat[] = $data;

				$val .= json_encode( $dat )."\n";
			}
		}
	}
	else
	{
		$med = str_replace( " ", ".*", $med );
		$filtroEc  = ( $filtraEspecialidadClinica == "on" ) ? " AND Espcli = 'on' " : "";
		$sql = "SELECT Meddoc,Medno1,Medno2,Medap1,Medap2,Medesp,Espnom
					FROM ".$aplicacion."_000048 LEFT JOIN ".$aplicacion."_000044 ON (Medesp=Espcod)
					WHERE ( concat(Medno1,' ',Medno2,' ',Medap1,' ',Medap2) regexp '".utf8_decode($med)."' or Meddoc LIKE '%".utf8_decode($med)."%' )
					AND Medest ='on'
					{$filtroEc}
					ORDER BY Medno1,Medno2,Medap1,Medap2
					LIMIT 30
					";
		$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );

		if( $num > 0 ){

			for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){

				$rows[ 'Meddoc' ] = trim( utf8_encode($rows[ 'Meddoc' ]) );
				$rows[ 'Medno1' ] = trim( utf8_encode($rows[ 'Medno1' ]) );
				$rows[ 'Medno2' ] = trim( utf8_encode($rows[ 'Medno2' ]) );
				$rows[ 'Medap1' ] = trim( utf8_encode($rows[ 'Medap1' ]) );
				$rows[ 'Medap2' ] = trim( utf8_encode($rows[ 'Medap2' ]) );
				$rows[ 'Medesp' ] = trim( utf8_encode($rows[ 'Medesp' ]) );
				$rows[ 'Espnom' ] = trim( utf8_encode($rows[ 'Espnom' ]) );

				$pos = strpos($rows[ 'Medesp' ], "-");
				if ($pos !== false) {
					$aux = explode("-",$rows[ 'Medesp' ]);
					$rows[ 'Medesp' ] = $aux[0];
					$rows[ 'Espnom' ] = $aux[1];
				}

				if( $rows[ 'Medesp' ] == "" ) $rows[ 'Espnom' ] = "00000";
				if( $rows[ 'Espnom' ] == "" ) $rows[ 'Espnom' ] = "SIN DATOS";

				//Creo el resultado como un json
				//Primero creo un array con los valores necesarios
				$data[ 'valor' ] = Array(	"cod"=> $rows[ 'Meddoc' ],
											"des"=> $rows[ 'Medno1' ]." ".$rows[ 'Medno2' ]." ".$rows[ 'Medap1' ]." ".$rows[ 'Medap2' ],
											"codesp"=> $rows[ 'Medesp' ],
											"desesp"=> $rows[ 'Espnom' ]);	//Este es el dato a procesar en javascript
				$data[ 'usu' ] = $rows[ 'Medno1' ]." ".$rows[ 'Medno2' ]." ".$rows[ 'Medap1' ]." ".$rows[ 'Medap2' ];	//Este es el que ve el usuario
				$dat = Array();
				$dat[] = $data;
				$val .= json_encode( $dat )."\n";
			}
		}
	}
	if( $sinJson == true) return $data;
	return $val;
}

function ping_unix(){
	global $conex;
	global $wemp_pmla;

	$ret = false;

	$direccion_ipunix = consultarAliasPorAplicacion($conex, $wemp_pmla, "ipdbunix" );
	if( $direccion_ipunix != "" ){
		$cmd_result = shell_exec("ping -c 1 -w 1 ". $direccion_ipunix);
		$result = explode(",",$cmd_result);
		// la función "eregi" ya está en desuso por eso se cambia a preg_match que es soportada en versiones posteriores de PHP
		// if(eregi("1 received", $result[1])){
		if(preg_match('/(1 received)/', $result[1])){
			$ret = true;
		}
	}
	return $ret;
}
//-----------------------------------------------------------------------------
// --> 	Funcion que obtiene el valor del triage en la HCE
//		2016-06-16, Jerson Trujillo.
//-----------------------------------------------------------------------------
function obtenerDatoHce($historia, $ingreso, $formulario, $arrCampos)
{
	global $conex;
	global $wemp_pmla;

	$respuesta			= array();
	$wbasedatoHce 		= consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");

	$campos 			= "";
	foreach($arrCampos as $valorC)
	{
		if(trim($valorC) != '')
			$campos.= (($campos == "") ? "'".$valorC."'" : ", '".$valorC."'");
	}

	// --> Consultar fecha y hora del formulario.
	$sqlForTri = "
	SELECT Fecha_data, Hora_data
	  FROM ".$wbasedatoHce."_000036
	 WHERE Firhis = '".$historia."'
	   AND Firing = '".$ingreso."'
	   AND Firpro = '".$formulario."'
     ORDER BY Fecha_data DESC, Hora_data DESC
	";
	$resForTri = mysql_query($sqlForTri, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlForTri):</b><br>".mysql_error());
	if($rowForTri = mysql_fetch_array($resForTri))
	{
		// --> Consultar datos del formulario
		$sqlDatosHce = "
		SELECT movcon, movdat
		  FROM ".$wbasedatoHce."_".$formulario."
		 WHERE Fecha_data = '".$rowForTri['Fecha_data']."'
		   AND Hora_data  = '".$rowForTri['Hora_data']."'
		   AND movpro 		= '".$formulario."'
		   AND movcon 		IN(".$campos.")
		   AND movhis 		= '".$historia."'
		   AND moving 		= '".$ingreso."'
		";
		$resDatosHce = mysql_query($sqlDatosHce, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlDatosHce):".$sqlDatosHce."</b><br>".mysql_error());
		while($rowDatosHce = mysql_fetch_array($resDatosHce))
			$respuesta[$rowDatosHce['movcon']] = trim($rowDatosHce['movdat']);
	}

	return $respuesta;
}
//-----------------------------------------------------------------------------
// --> 	Funcion que pinta la lista de pacientes con turno en la sala de espera
//		2015-06-26, Jerson Trujillo.
//-----------------------------------------------------------------------------
function listarPacientesConTurno()
{
	global $conex;
	global $wemp_pmla;
	global $user;

	$usuario		= explode("-", $user);
	$usuario		= $usuario[1];

	$wbasedato 		= consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$sqlVentanillas	= "
	SELECT Puecod, Puenom, Pueusu
	  FROM ".$wbasedato."_000180
	 WHERE Puetve = 'on'
	   AND Pueest = 'on'
	";
	$resVentanillas = mysql_query($sqlVentanillas, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlVentanillas):</b><br>".mysql_error());
	while($rowVentanillas = mysql_fetch_array($resVentanillas))
	{
		$arrayVentanillas[$rowVentanillas['Puecod']] = $rowVentanillas['Puenom'];

		if($rowVentanillas['Pueusu'] == $usuario)
			$ventanillaActUsu = $rowVentanillas['Puecod'];
	}

	echo "
	<br>
	<table class='anchotabla' align='center'>
		<tr>
			<td colspan='3' class='encabezadoTabla' align='center' style='font-size:18pt'>
				PACIENTES CON TRIAGE
			</td>
		</tr>
		<tr>
			<td style='padding:5px;' width='30%' align='left'>
				<span style='font-family: verdana;font-weight:bold;font-size: 10pt;'>
					Buscar:&nbsp;&nbsp;</b><input id='buscardorTurno' type='text' placeholder='Digite palabra clave' style='border-radius: 4px;border:1px solid #AFAFAF;width:200px'>
				</span>
			</td>
			<td style='padding:5px;' width='40%' align='center'>
				<span style='font-family: verdana;font-weight:bold;font-size: 10pt;'>
					Puesto de trabajo:&nbsp;&nbsp;</b>
					<select id='puestoTrabajo' type='text' style='border-radius: 4px;border:1px solid #AFAFAF;width:200px' ventanillaActUsu='".$ventanillaActUsu."' onChange='cambiarPuestoTrabajo(true)'>
						<option value='' ".((trim($codVentanilla) == "") ? "SELECTED='SELECTED'" : "" ).">Seleccione..</option>
					";
				foreach($arrayVentanillas as $codVentanilla => $nomVentanilla)
					echo "<option value='".$codVentanilla."' ".(($codVentanilla == $ventanillaActUsu) ? "SELECTED='SELECTED'" : "" ).">".$nomVentanilla."</option>";
	echo "			</select>
				</span>
			</td>
			<td style='padding:4px;' width='30%' align='right'>
				<input type='button' style='cursor:pointer;font-family: verdana;font-weight:bold;font-size: 10pt;width:200px' onclick='verTurnosCancelados()' value='Ver Turnos Cancelados'>
			</td>
		</tr>
	</table>
	<div style='height:280px;overflow:auto;background:none repeat scroll 0 0;'>
		<table id='tablaListaTurnos' style='width:900px' align='center' id='tablaPacTurnos'>
			<tr align='center' class='encabezadoTabla' style='font-family: verdana;font-size: 8pt;'>

				<td>
					En espera de admisi&oacute;n
					<br><span style='font-family: verdana;font-weight:normal;font-size: 7pt;'>H:m:s</span>
				</td>
				<td>Turno</td>
				<td>Documento</td>
				<td>Nombre</td>
				<td>Triage</td>
				<td>Especialidad</td>
				<td>Prioridad</td>
				<td colspan='3' align='center'>Opciones</td>
			</tr>";

		// --> 2016-06-16: Se consultan los pacientes que ya tengan triage y esten pendientes de la admision
		$fechaTur 	= date('Y-m-d');
		$fechaTur 	= strtotime ('-1 day', strtotime($fechaTur)) ;
		$fechaTur 	= date ('Y-m-d' , $fechaTur );

		$sqlTurnos = "
		SELECT Atufat, Atutur, Atudoc, Atutdo, Atullv, Atuusu, Atunom, A.id, Ahthte, Prinom
		  FROM ".$wbasedato."_000178 AS A INNER JOIN ".$wbasedato."_000204 AS B ON (Atutur = Ahttur)
		       LEFT JOIN ".$wbasedato."_000206 AS C ON (Atupri = Pricod)
		 WHERE A.Fecha_data >= '".$fechaTur."'
		   AND Atuest  = 'on'
		   AND Atucta  = 'on'
		   AND Atupad != 'on'
		   AND Atuadm != 'on'
		   AND Ahtest = 'on'
		 ORDER BY REPLACE(Atutur, '-', '')*1 ASC
		";
		$resTurnos 	= mysql_query($sqlTurnos, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTurnos):</b><br>".mysql_error());
		$coloFila	= 'fila2';
		$turnoConLlamadoEnVentanilla = '';
		while($rowTurnos = mysql_fetch_array($resTurnos))
		{
			$coloFila 			= (($coloFila == 'fila2') ? 'fila1' : 'fila2');
			$tiempoEspera		= strtotime(date("Y-m-d H:i:s"))-strtotime($rowTurnos['Atufat']);

			// --> Obtener el valor del triage
			$forYcampoTriage	= consultarAliasPorAplicacion($conex, $wemp_pmla, "formularioYcampoTriage");
			$forYcampoTriage	= explode("-", $forYcampoTriage);
			$respuestaHce		= obtenerDatoHce($rowTurnos['Ahthte'], '1', trim($forYcampoTriage[0]), array($forYcampoTriage[1]));
			$triage				= $respuestaHce[$forYcampoTriage[1]];
			if($triage != '')
			{
				$triage	= explode("-", $triage);
				$triage	= "Nivel ".trim($triage[0])*1;
			}
			else
				$triage = "";

			// --> Obtener la especialidad
			$arrCamposConTriage = array();
			$arrHomoConductas 	= array();
			$sqlHomoCon = "
			SELECT Hctcch, Hctcom, Hctpin
			  FROM ".$wbasedato."_000205
			 WHERE Hctest = 'on'
			";
			$resHomoCon = mysql_query($sqlHomoCon, $conex) or ( $data[ 'mensaje' ] = utf8_encode( "Error en el query sqlHomoCon:$sqlHomoCon - ".mysql_error() ) );
			while($rowHomoCon = mysql_fetch_array($resHomoCon))
			{
				$arrCamposConTriage[] 										= $rowHomoCon['Hctcch'];
				$arrHomoConductas[$rowHomoCon['Hctcch']]['Especialidad']	= $rowHomoCon['Hctcom'];
				$arrHomoConductas[$rowHomoCon['Hctcch']]['permiteIngreso']	= $rowHomoCon['Hctpin'];
			}

			$campoConducta		= trim(consultarAliasPorAplicacion($conex, $wemp_pmla, "CampoPlanConductaDeTriageHce"));
			$datosHce 			= obtenerDatoHce($rowTurnos['Ahthte'], '1', trim($forYcampoTriage[0]), array($campoConducta));
			$mostrarTr 			= true;
			$datosHce			= $datosHce[$campoConducta];
			$datosHce 			= explode("-", $datosHce);
			$datosHce 			= $datosHce[0];
			if(trim($datosHce) != '')
			{
				$especialidadTriage	= $arrHomoConductas[$datosHce]['Especialidad'];
				// --> 	Si es un campo que no permite hacer ingreso
				//		osea el campo de alta o de direccionamiento del formulario de triage, estan asignados en "si".
				if($arrHomoConductas[$datosHce]['permiteIngreso'] != 'on')
				{
					// --> Actualizar el turno en alta o redireccionado en 'on'
					$sqlALtRed = "
					UPDATE ".$wbasedato."_000178
					   SET Atuaor = 'on'
					 WHERE Atutur = '".$rowTurnos['Atutur']."'
					";
					mysql_query($sqlALtRed, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlALtRed):".$sqlALtRed."</b><br>".mysql_error());

					$mostrarTr 	= false;
				}
				else
				{
					// --> Actualizar el turno en alta o redireccionado en 'off'
					$sqlALtRed = "
					UPDATE ".$wbasedato."_000178
					   SET Atuaor = 'off'
					 WHERE Atutur = '".$rowTurnos['Atutur']."'
					";
					mysql_query($sqlALtRed, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlALtRed):".$sqlALtRed."</b><br>".mysql_error());

					// --> Obtener nombre de la especialidad
					$sqlNomEsp = "
					SELECT Espnom
					  FROM ".$wbasedato."_000044
					 WHERE Espcod = '".$especialidadTriage."'
					";
					$resNomEsp 	= mysql_query($sqlNomEsp, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlNomEsp):".$sqlNomEsp."</b><br>".mysql_error());
					if($rowNomEsp = mysql_fetch_array($resNomEsp))
						$nomEspecialidad = $rowNomEsp['Espnom'];
					else
						$nomEspecialidad = '';
				}
			}

			if(trim($rowTurnos['Atunom']) == '')
				$nomPaciente = obtenerNombrePaciente($rowTurnos['Atutdo'], $rowTurnos['Atudoc']);
			else
				$nomPaciente = trim($rowTurnos['Atunom']);

			// --> El turno ya tiene llamado a la ventanilla.
			$tieneLlamado = (($rowTurnos['Atullv'] == 'on') ? TRUE : FALSE);
			if($tieneLlamado && $rowTurnos['Atuusu'] == $usuario)
				$turnoConLlamadoEnVentanilla = $rowTurnos['Atutur'];

			if($mostrarTr)
			{
				echo "
				<tr class='".$coloFila." find' id='trTurno_".$rowTurnos['Atutur']."'>
					<td style='padding:2px;' align='center'>".gmdate("H:i:s", $tiempoEspera)."</td>
					<td style='padding:2px;' align='center'><b>".substr($rowTurnos['Atutur'], 4)."</b></td>
					<td style='padding:2px;' >".$rowTurnos['Atutdo']."-".$rowTurnos['Atudoc']."</td>
					<td style='padding:2px;' >".$nomPaciente."</td>
					<td style='padding:2px;' align='center'>".$triage."</td>
					<td style='padding:2px;' align='center'>".ucfirst(strtolower($nomEspecialidad))."</td>
					<td style='padding:2px;' align='center'>".$rowTurnos['Prinom']."</td>
					<td style='padding:2px;' align='center' >
						<img id='imgLlamar".$rowTurnos['Atutur']."' style='cursor:pointer;".(($tieneLlamado) ? "display:none" : "")."' class='botonLlamarPaciente' width='20' heigth='20' tooltip='si' title='Llamar' src='../../images/medical/root/Call2.png'	onclick='llamarPacienteAtencion(\"".$rowTurnos['Atutur']."\", \"imgLlamar".$rowTurnos['Atutur']."\")'>
						<img style='cursor:pointer;display:none' 	class='botonColgarPaciente' width='20' heigth='20' tooltip='si' title='Cancelar llamado'  	src='../../images/medical/root/call3.png'	onclick='cancelarLlamarPacienteAtencion(\"".$rowTurnos['Atutur']."\")'>
						<img style='display:none' 					class='botonColgarPaciente' src='../../images/medical/ajax-loader1.gif'>
					</td>
					<td style='padding:2px;' align='center' >
						<img style='cursor:pointer;display:none' id='botonAdmitir".$rowTurnos['Atutur']."' width='18' height='18' tooltip='si' title='Admitir' src='../../images/medical/root/grabar.png' onclick='mostrarAdmisionDesdeTurno(\"".$rowTurnos['Atutur']."\", \"".$rowTurnos['Atutdo']."\", \"".$rowTurnos['Atudoc']."\");'>
					</td>
					<td style='padding:2px;' align='center' >
						<img id='botonCancelar".$rowTurnos['Atutur']."' style='cursor:pointer;".(($tieneLlamado) ? "display:none" : "")."' tooltip='si' title='Cancelar turno' src='../../images/medical/eliminar1.png' onclick='cancelarTurno(\"".$rowTurnos['Atutur']."\")'>
					</td>
				</tr>
				";
			}
		}
		if(mysql_num_rows($resTurnos) == 0)
		{
			echo "<tr><td colspan='8' class='fila2' align='center'>Sin registros</td></tr>";
		}

	echo "
		</table>
		<input type='hidden' id='turnoLlamadoPorEsteUsuario' value='".$turnoConLlamadoEnVentanilla."'>
	</div>
	<div id='divTurnosCancelados' style='display:none'></div>
	";
}
//-----------------------------------------------------------------------------
// --> 	Funcion que dado un documento y tipo, obtiene el nombre de un paciente
//		2015-06-26, Jerson Trujillo.
//-----------------------------------------------------------------------------
function obtenerNombrePaciente($tipoDocumento, $documento)
{
	global $conex;

	$sqlNomPac = "
	SELECT CONCAT(Pacno1, ' ', Pacno2, ' ', Pacap1, ' ', Pacap2, ' ') AS nombrePac
	  FROM root_000036
	 WHERE Pacced = '".$documento."'
	   AND Pactid = '".$tipoDocumento."'
	";
	$resNomPac = mysql_query($sqlNomPac, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlNomPac):</b><br>".mysql_error());
	if($rowNomPac = mysql_fetch_array($resNomPac))
		$nomPaciente = $rowNomPac['nombrePac'];
	else
		$nomPaciente = "";

	return $nomPaciente;
}

function verificarCcoIngresoAyuda( $ccoIngreso ){
	global $conex, $wemp_pmla, $aplMovhos;

	$query = "SELECT ccoayu
			    FROM {$aplMovhos}_000011
			   WHERE ccocod = '{$ccoIngreso}'";
	$rs = mysql_query($query,$conex);
	$row = mysql_fetch_assoc($rs);
	$ccoAyu = ( $row['ccoayu'] == "on" ) ? true : false;
	return($ccoAyu);

}

// Veronica Arismendy 2017-06-20
// Función para consultar la información del centro de costo configurada en root_000117 para el nuevo programa de ayudas diagnosticas bajo esquema de procesos
function getInfoCc($nameCco, $conex){
	$sql = "SELECT descripcion, centroCosto
			FROM root_000117
			WHERE nombreCc = '".$nameCco."'";

	$res = mysql_query($sql, $conex);
	$row = mysql_fetch_assoc($res);

	$newPrefix = substr($row["descripcion"],0,3);
	$arrResult = array("prefix" => strtolower($newPrefix), "codCco" => $row["centroCosto"] );
	return $arrResult;
}
/******************************************************************************
 * INICIO DEL PROGRAMA
 ******************************************************************************/


//




$conex = obtenerConexionBD("matrix");

$wactualiz="2018-08-31";

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
else if($wemp_pmla == 02)
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



echo "<input type='hidden' name='wbasedato' id='wbasedato' value='".$wbasedato."'>";
echo "<input type='hidden' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";
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
							$first_option="<option value='".$j."-".$value."' selected='selected'>".$des."</option>";
					   else
							$html_options.="<option value='".$j."-".$value."'>".$des."</option>";
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
echo "<td class='fila1' colspan='2' style='width:16%'>Tipo de Documento</td>";
echo "<td class='fila1' style='width:16%'>N&uacute;mero Documento</td>";
echo "<td class='fila1' style='width:16%'>Primer Apellido</td>";
echo "<td class='fila1' style='width:16%'>Segundo Apellido</td>";
echo "<td class='fila1' style='width:16%'>Primer Nombre</td>";
echo "<td class='fila1' colspan='2' style='width:16%'>Segundo Nombre</td>";
echo "</tr>";
echo "<tr>";
$param="class='reset' msgError msgcampo='Tipo de documento' ux='_ux_pactid_ux_midtii' onChange='cambiarTipoDocumento( this );'";
echo "<td class='fila1espacio' colspan='2'>";$res1=consultaMaestros('root_000007','Codigo,Descripcion,alfanumerico, docigualhis',$where="Estado='on'",'','');
crearSelectHTMLAcc($res1,'pac_tdoselTipoDoc','pac_tdoselTipoDoc',$param);

echo "</td>";
echo "<td class='fila1espacio'><input type='text' msgcampo='Documento' name='pac_doctxtNumDoc' id='pac_doctxtNumDoc' class='reset' msgError='Digite el numero de documento' ux='_ux_pacced_ux_midide' onblur='verificarDocumento();verificarTriageUrgencias();'></td>";
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