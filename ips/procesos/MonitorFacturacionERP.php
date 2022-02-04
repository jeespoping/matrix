<?php
include_once("conex.php");

/**
DESCRIPCION:
AUTOR:				Felipe Alvarez
FECHA DE CREACION:

 --------------------------------------------------------------------------------------------------------------------------------------------
 */$wactualiza='Noviembre 1 2021';/*
 ACTUALIZACIONES
Octubre 27 2021: Juan David R, Se hacen modificaciones en parametros quemados 

Enero 21 2020: Jerson, mostrar codigo propio en el tooltip de los procedimientos de la cx
Enero 21 2020: Jerson, En el monitor de auditoria/autorizaciones cuando una cx requiere que se autoricen los procedimientos para el segundo
				responsable por superación de topes, todos estos deben quedar como confirmados para que el usuario de autorizaciones solo le corresponsa ingresar el numero
				de autorizacion.
Agosto 8 de 2019: jerson
	- En el monitor de auditoria: 
		Se corrige error al leer los procedimientos de la D.O, no los mostraba todos porque se inicializaba el indice del array
		No se muestra el nombre del residente sino del medico que confirmo
		Se coloca tooltip del medico que ingreso el cups
		Se agrega seleccionador para la bilateralidad y debe ser obligatorio escogerlo
		Se muestra la especialidad del medico
		El mismo codico cups se puede leer dos veces desde la D.O
Julio 4 de 2019: Jessica
	- En el monitor: Programación de cirugías, se solucionan problemas de tildes.
Abril 16 de 2018: Jerson
	- Se agrega el filtro de estado "AND B.Proest  = 'on'" al query que trae los procedimientos en la opcion "Adicionar procedmiento" 
Mayo 5 de 2018: Jerson Trujillo
	* En el monitor de auditoria de cx se agrega una opcion para poder visualizar la carta de derechos.
Abril 24 de 2018: Jerson trujillo
	* Se corrige incosistencia para la variable valTramite, cuando sea undefined se iguala en vacio.

Abril 4 de 2018 Jonatan Lopez:
	* Se cambia la descripcion de tipo de liquidacion de NO POS a A TARIFA PROPIA.
 Marzo 28 de 2018 Edwar Jaramillo:
 	* Se agrega una nueva columna en los resultados de la pestaña "CX liquidadas en trámite" para permitir diferenciar las cirugías de pacientes ambulatorios u hospitalizados.
 	* Nueva modificación en la pestaña "CX liquidadas en trámite" para complementar el filto de paciente ambulatorio u hospitalizado, verificando en la tabla movhos_67 si existe registro de hospitalización.
 Marzo 27 de 2018 Edwar Jaramillo:
 	* En el monitor de autorizaciones y auditoría se deshabilita le opción de seleccionar trámites para el perfil de facturación para que no modifique
 		los estados de trámite enviados desde autorizaciones.
	* Se devuelve la modificación inmediatamente anterior porque las facturadoras de cirugía si pueden manipular los estados de trámite por ejemplo
		para marcar la cirugía cómo en tramite por MOS, entre otros. Aunque no debería poder modificar o seleccionar los trámites de autorización o pendiente por parametrización.
 Marzo 22 de 2018 Edwar Jaramillo:
 	* Se crean nuevas opciones en los roles para indicar si el rol se debe ver en las convenciones y si el rol permite marcar una cirugía como revisada.
 	* La opción de crear observaciones se habilita para los usuarios que permiten autorizar y está con trámites pendientes.
 	* Para que se permita pasar a liquidar en trámite, se incluye el estado LIQ como destino que permite pasar a liquidar en trámite y validar si el trámite
 		requiere o no autorización.
 Marzo 21 de 2018 Edwar Jaramillo:
 	* En el monitor de autorizaciones/auditoría no se estaba validando para el rol de auditoría que el número de autorización debía ser obligatorio para pasar a facturación.
 Marzo 20 de 2018 Edwar Jaramillo:
 	* Se crean dos nuevos campos en el maestro de trámites 000264 que identifican si un trámite permite enviar un turno de cirugía a facturación o no y además
 		si requiere o no números de autorización. Esos nuevos campos on "Caupli" (Causa permite pasar a facturación), "Caurau" (Causa requiere autorización para liquidar, necesita Caupli=on)
 	* En el maestro de roles se crean dos campos para parametrizar los permisos a dos nuevas pestañas del monitor de auditoría, como son
 		"Rolltr" (Ver pestaña liquidados en trámite), "Rollsr" (Ver pestaña liquidados solucionados sin revisar).
 	* Nueva pensaña "CX Liquidadas en trámite", se muestran las cirugías liquidadas pero que quedaron con un trámite pendiente, se permitirá cambiar el estado del trámite
 		a "Solucionado" y se obliga a ingresar los número de autorización faltantes para procedimientos e insumos.
	* Nueva pestaña "CX Liquidadas sin revisar", se muestran las cirugías que fueron liquidadas y que tienen estado "Solucionado", esta pestaña fue creada para
		los facturadores donde tienen la posibilidad de ver las cirugías que estaban pendiente por autorizaciones pero que ya fueron solucionadas, es posible que al
		solucionar los problemas de autorización se deban actualizar cargos en la cuenta del paciente, pero esos cambios los deberá hacer el facturador directamente
		en unix, cuando el facturador realice las actualizaciones necesarias, en esta nueva pestaña puede marcar el check de "Revisada" y así sacar esa cirugía de la lista.
	* Validación para que solo los roles que tienen permitido diligenciar autorización, tengan permitido modificar el estado de trámite después de liquidar la cirugía en la
		pestaña de "CX liquidadas en trámite" en el monitor de autorizaciones/auditoría.
	* Cuando se dejaba una cirugía pendiente por trámite y se pasaba a facturación, la causa de trámite se estaba reiniciando a vacío, en esta actualización se soluciona.
 Diciembre 13 de 2017 Edwar Jaramillo:
 	* Se agregan y modifican css para resaltar inconsistencias en la devolución de insumos de cirugía que hacen parte del proceso de aplicación.
 Noviembre 14 de 2017 Edwar Jaramillo:
 	* Modificaciones para que mediante parametrización del turno de cirugía o quirófano, se pueda inicar un turno en el nuevo proceso de asignación de responsables
 		en la entrega de mercado, aplicación desde artículos-insumos por parte de los responsables del mercado.
 	* Se desarrollan nuevas características para el programa de mercados de círugía pueda interactuar con el nuevo proceso de aplicación de insumos por parte
 		de los responsables del mercado en el quirófano.
 		- Las opciones de adicionar insumo, editar, borrar insumo, borrar mercado, modifican también las tabla movhos_227 y movhos_228 del programa de aplicación
 			para que se tenga consistencia en los datos entregados en el almacén y aplicados o usados en el quirófano.
 Septiembre 26 de 2017: Edwar Jaramillo
    * En la opción de imprimir listado de insumos en el monitor de mercados no se estaba mostrando correctamente la información de las columnas, debído a que en actualización
        anterior se agregó una nueva columna, la impresión estaba desplazada una columna por tanto no se mostraba la información de antes.
 Agosto 08 de 2017: Edwar Jaramillo
 	* En las cirugías liquidadas se muestra la fecha y hora de liquidación.
 	* En el calendario de mercados del día, se actualiza para permitir seleccionar año y mes más facilmente.
 Julio 06 de 2017: Edwar Jaramillo
 	* Consulta de usuario y foto de empleado, para asignarle la entrega de mercados o insumos durante el acto quirúrgico.
 Abril 25 de 2017: Edwar Jaramillo
 	* En la función guardarAuditoria faltaba asociar el perfil convenios con facturación para que guarde el campo de rechazar procedimiento por convenios y
 		se restringe que solo el perfil convenios puede guardar las modificaciones realizadas en el campo de rechazar procedimiento por convenios.
 Abril 04 de 2017: Jerson Trujillo
 	* Se modifica el query, para que muestre el nombre de la 103 y no el de la 70
 Marzo 27 2017, Camilo Zapata:
 	* se elimina la función mostrar_correos y se reemplaza por mostrarPendientesRegrabacion el cual mostrara las historias que tienen pendiente regrabación de cargos por cambio de responsable desde el programa de
 	  admision.
 Marzo 27 2017, Edwar Jaramillo:
 	* Nueva parametrización de roles para el monitor de auditoría y autorizaciones de una forma más dinámica, se pueden inactivas o crear nuevos roles y parte de
 		permisos o características de cada rol.
	* En esta nueva actualización se configuran tres nuevos roles ("Coordinación de cirugía", "Auditoría IDC", "Convenios").
 Marzo 16 2017, Edwar Jaramillo:
 	* Se coloca entre comentarios los botones para los nuevos roles para que aún no aparezcan en los monitores hasta que se realice la socualización de estos cambios.
 Marzo 15 2017, Edwar Jaramillo:
 	* Se agregan tres nuevos roles para el monitor de Autorizaciones/Auditoría, estos son:
 		CCX:Coordinación de cirugía, a este rol le llegan los turnos desde auditoría que no tienen descripción operatoria.
 		IDC: Auditoría IDC, a este rol le llegan los turnos desde auditoría de clinica, cuyos turnos deben ser auditados por el IDC.
 		CNV: Convenios, a este rol le llegan los turnos desde Autorizaciones, cuyos procedimientos de la cirugía necesitan de la intervención de convenios en la negociación.
 		Hasta el momento del desarrollo de esta nueva funcionalidad el rol AUD puede enviar adicionalmente a "IDC" o "CCX" y estos dos solo pueden enviar a AUD,
 		AUT puede enviar al nuevo rol CNV y este solo puede enviar a AUT.
	* Al perfil IDC no se le permite ver el listado completo de cirugías ni realizar búsquedas, solo se le permite ver la cirugías que se le asignaron al rol IDC.
	* El rol CNV Puede dar un visto bueno para los procedimientos que llegan a su monitor, por defecto todos los procedimientos son aprobados por convenios
		pero este rol los puede rechazar.
	* No se valídan campos cuando se pasa de AUD a IDC o CCX, tampoco cuando se pasa de AUT a CNV.
	* No se permite agregar procedimientos a CCX ni CNV.
 Enero 24 2017, Edwar Jaramillo:
 	* Al borrar un mercado de cirugía se valída si hay una sesión de usuario activa, en caso contrario no permite eliminar, pues se encontro en el log
 		de insumos borrados que no estaba quedando el código del responsable porque posiblemente en esos casos ya se había cerrado la sesión.
 Septiembre 28 2016, jerson trujillo:
		Se modifica el monitor de cx pendientes y cx de dias anteriores, se le agrega imagen con tooltip informando la razon por la que
		la cx paso automaticamente a liquidar. Se da la posiblidad de dar permisos por usuario para acceder a los monitores. Nueva opcion
		para una cx que ya este lista para liquidar poder reversarla al monitor de autorizaciones/auditoria (Rol FAC).
 Septiembre 12 2016 Edwar Jaramillo
 	* Se mejora el rendiemiento del query para el monitor de mercados de días anteriores sin liquidar
 Agosto 24 2016 Jerson trujillo
	* Monitor auditoria: con un solo procedimiento registrado en gestin de cx, que no tenga excepcion, implica que se debe auditar la cx.
 Julio 26 2016 Jerson trujillo
	* Se corrige error que no se estaban actualizando los numeros de autorizacion
 Junio 30 2016 Edwar Jaramillo:
 	* Cuando se abre el mercado despues de haber estado cerrado, ya no se cambia el estado del proceso del turno por a facturación cuando el turno se auditó automáticamente
 Junio 28 2016, jerson Trujillo:
	Las cx que tengan procedimientos con excepcion, deberan pasar automatizamente a listas para liquidar.
 Junio 20 2016 Edwar Jaramillo:
 	* En general, el mercado deja de depender de la historia-ingreso-cedula y depende solo del código del turno.
	* El proceso cerrar_mercado, abrirMercado se modifica para que tenga en cuenta el turno de cirugía y no la historia-ingreso-cedula, porque el mercado esta asociado directamente al turno, las historia e ingreso en agenda de cirugía puede variar y el mercado queda desligado temporalmente hasta ejecutar la opción de actualización.
	* En los monitores de mercados ya no se relacionan con root_36 ni root_37 porque eso genera que se muestren en el monitor los registros solo con la historia e ingreso que esté en root, pero si hay reingresos o reintervenciones no va a ser igual el ingreso de root con el del turno de cirugía, se requiere el del turno de cirugía para permitir liquidar a ingresos inactivos, se empieza a mostrar la historia e ingreso del turno en la agenda de cirugía.
	* Botón para ocultar los turnos de cirugía de días anteriores que no tienen mercado.
 Mayo 31 2016 Edwar Jaramillo:
 	* Cuando se anula el mercado, se marca en el encabezado de la auditoría que fue anulado, al momento de cargar un insumo nuevamente, se desmarca ese estado.
 Mayo 25 2016 Edwar Jaramillo:
	* Modificación a consulta de días anteriores sin mercado para que revice en cliame_252 si el turno aparece sin liquidar.
	* Se mejora la función que simula el efecto blink, el javascript estaba agotando la memoria despues de tener un largo tiempo de inactividad en el monitor.
 Abril 17 2016: Jerson Trujillo
	Para el monitor de Autorizaciones/Auditoria, se cambia para que todo el flujo de las cx quede dependiendo de la configuracion que tengan
	los parametros definidos en la root_51.
 Marzo 29 2016 Edwar Jaramillo:
 	* Para los monitores de mercados de cirugía, se cambia el campo de estado auditado "Aueaud" por "Auelli" listo para liquidar.
 Marzo 08 2016 Edwar Jaramillo:
 	* Nueva columna para que los facturadores puedan elegir liquidar paquete desde el monitor de mercados de cirugía.
 	* Se envían parámetros al programa de liquidación para saber si el programa fue abierto desde el monitor o no.
 Marzo 03 2016 Edwar Jaramillo:
 	* En la columna de liquidación se puede ver si el turno aun no tiene descripción operatoria.
 Enero 04 2016 Edwar Jaramillo:
 	* Las siguientes modificaciones aplican para monitores de mercados de cirugía:
 	* Los procedimientos liquidados ya no se verifican en cliame_199 sino en cliame_252 que es la tabla de encabezado de auditoría donde se marca si el acto quirúrgico esta liquidado o no.
 	* A los estados (mensajes) de los monitores se agregan dos estados, liquidado y auditado según cliame_252 para indicar a los usuarios en que estado esta cada turno de cirugía.
 	* En la descripción de la cirugía se muestran los códigos de los procedimientos que se encuentran en la tabla de detalle de auditoría, si en la tabla aún no hay procedimientos,
 		entonces se muestra la descripción que aparece en tcx_11.
 	* El enlace para liquidar la cirugía solo se habilitará si el turno ya está auditado.

 Noviembre 26 2015 Edwar Jaramillo
 	* [EJ_01] Mejoras al monitor de Cx. sin liquidar de días anteriores, nueva función para consultar los turnos sin liquidar de días anteriores, se guarda en una tabla
 		temporal y se hace JOIN con el query final de mercados-cirugías de días anteriores sin liquidar.
 	* Se crea un parámetro para indicar cuántos meses hacia atras de la fecha actual se puede consultar los turnos sin liquidar.
 Julio 29 2015 Edwar Jaramillo:
	* En la función "anular_mercado", se insertan todos los insumos del mercado en una tabla de log (000245) antes de borrarlos de la tabla de mercados (000207),
		en esa nueva tabla se guardar cada insumo tal como estaban antes de ser borrados y el usuario, feche-hora en que se borró.
	* Para eliminar todo el mercado desde "anular_mercado" ahora se pide confirmar mediante la contraseña matrix para poder borrar todo el mercado.
 Julio 21 2015 Edwar Jaramillo:
	* Nuevos campos en cliame_207 para guardar fecha, hora, usuario que cierra el mercado.
	* A la función "liquidarMercadoUnix" encargada de consultar el mercado desde el monitor y mostrar tarifas, se le envía la fecha de la cirugía para que se puedan calcular
		las tarifas respecto a esa fecha y no con la fecha actual del sistemas como se venía haciendo.
	* A las cirugías pendientes de validar en unix se les coloca un color diferente para identificarlas.
	* Desde funciones_facturacion se crea una nueva función "validarMercadoLiquidadoCerrado" encargada de validar si un mercado ha sido cerrado o liquidado y poder restringir los eventos
		en el monitor de mercados para que permita o no, modificar el mercado (Editar, devolver, grabar, ...).
	* Uso de md5_file para oblicar a cargar cache de funcionInsumosqxERP.js cuando se tengan cambio.

 Abril 17 2015
 Edwar Jaramillo: 	* La consulta del monitor de mercados se modifica en la parte de mercados liquidados para saber si los insumos del mercado ya fueron revisados en unix,
						(Esa revición la hace un cron para dejar los insumos en unix tal como quedaron SI o NO facturables en Matrix).
					* En la columna de Liquidación cirugía se cambia el mensaje del link para que indique si el mercado que ya esta liquidado fue verificado
						en unix o falta por verificar.

 Marzo 31 2015
 Edwar Jaramillo: 	* No estaba abriendo correctamente el mercado desde desde almacen (Para quienes tiene el permiso de abrir)

 Marzo 25 2015
 Edwar Jaramillo: 	* Nuevo campo donde se define un nuevo permiso, el nuevo campo tiene los códigos de los usuarios administradores para ese monitor, si un usuario aparece en ese campo
						entonces tendrá permiso de cargar, devolver, cerrar, ..., además debe tener las columnas habilitadas, si un usuario tiene las columnas habilitadas
						pero no es usuairo administrador entonces no se le mostrarán las columnas que tenga habilitadas (on).
					* No se estaba diferenciando correctamente entre los permisos del monitor Cirugias_sin_mercado y Cirugias_con_mercado_y_no_liquidadas, siempre se estaban
						adignando los permisos solo de Cirugias_sin_mercado así se estuviera consultando el segundo monitor. (Los permisos de segundo monitor no tenían efecto
						porque siempre se tomaban los de Cirugias_sin_mercado)

 Marzo 20 2015
 Edwar Jaramillo: 	* Se crea un nuevo permiso para controlar que algunos roles solo puedan ver el link de "consultar" mercado solo si este ya está cerrado.
					* Este cambio esta asociado a otra mejora realizada en la modal de consultar donde se muestran las tarifas de los insumos.

 Marzo 19 2015
 Edwar Jaramillo:	* Cambio mensaje en la columna mercado facturador, cambia nombre solo por facturación, cuando el mercado ya esta liquidado, en esa mismo columna
						debe decir Liquidado.

 Marzo 16 2015
 Edwar Jaramillo:	* Dos nuevas columnas para mostrar el estado del mercado para almacen y facturación, mostrará si esta abierto o cerrado.
					* La consulta de cirugías no liquidadas de días anteriores se le adiciona cuántos insumos son grabados y cerrados
						por almacén y por facturador, en algunos casos se estaba mostrando en las dos columnas nuevas de estado de mercado
						que no había mercado ni para almacen ni para facturador pero realmente si había mercado, pero no estaba identificando a quien correspondía el mercado
						eso sucedía para el primer union donde solo se consultaban los mercados sin cerrar, no se especificaba las cantidades de insumos y cierres para cada rol.

 Marzo 13 2015
 Edwar Jaramillo:	* Se crea un link que permita consultar las plantillas de mercados creados por procedimientos, para que los grabadores tengan una fuente de consulta
						para crear los mercados de las cirugías.

 Marzo 09 2015
 Edwar Jaramillo: 	* Se inactiva la rutina encarga de ejecutar el cron de cargos desde el monitor.
					* Se coloca un mensaje en el monitor de mercados para indicar cuando la consulta no arrojó resultados, es el caso del monitor de cirugías
						sin liquidar de días anteriores, cuando todo está liquidado muestra el mensaje.

 Marzo 03 2015
 Edwar Jaramillo: 	* Adición de validación en los campos de historia donde se carga o se devuelve mercado, cuando hay más de un turno para una misma história
						entonces el programa muestra un mensaje informando que hay más de un registro para la misma historia, entonces se debe cargar manualmente
						el turno que corresponde abrir.
					* En la ventana de cargar, devolver y consultar se muestra un nuevo campo en el encabezado donde se muestra la descripción de la cirugía.

 Febrero 17 2015
 Edwar Jaramillo: 	* Se modifica la clase para definir que un artículo del mercado ha sido modificado median la nueva clase "insumoModificado", antes se presentaba problema
						si se modificaba una cantidad y seguido a eso se eliminaba un artículo, se quedaba el primer insumos sin ser marcado como editado.
					* Se modifican los querys de actualización del marcado para que solo lo haga mediante el código del turno sin tener en cuenta la historia e ingreso.
					* Al anular mercado se inactivan tambien los registros de lotes que el mercado tenga asociado en la nueva tabla de lotes por artículo 000240

 Diciembre 30 2014
 Edwar Jaramillo: 	* Se crea la funcionalidad "actualizacion_cron_cargos" para que desde el monitor se actualice constantemente los cargos de insumos
						que estan en unix respecto a los cargos guardados en matrix (000106).

 Diciembre 29 2014
 Edwar Jaramillo:	* Al quirófano se le adiciona la hora de inicio de la cirugía en el monitor mercados cirugias.
					* Se usa la funcionalidad del blockUI cuando se cambia de fecha en el monitor de mercados cirugías.

 Diciembre 19 2014
 Edwar Jaramillo:	* Se modifican los permisos para el monitor de cirugías con-sin mercado.
					* Se cambia la opción de liquidar mercado por consultar mercado.
					* El parámetro que ántes era usado para saber si se debía mostrar la opción de liquidar mercado, se usó para determinar si puede o no ver la columna de
						consultar mercado.

 Diciembre 17 2014
 Edwar Jaramillo:	* Se agrupa el query de agenda de cirugía por código de turno, si hay una misma historia e ingreso o documento iguales se puede diferenciar por el código
						del turno (por ejemplo si hay dos cirugías diferentes que están muy seguidas, el mercado va a ser dependiente del turno al que corresponda)

 Diciembre 09 2014
 Edwar Jaramillo:	* Se modifica el query que lee la agenda de cirugías para verificar si el ingreso que aparece en tcx_11 si esta realmente activo o no,
						en cado de estar de alta en movhos_18 entonces en la lista de carga de mercados debe aparecer sin historia e ingreso hasta que se
						haga el nuevo ingreso.

 Diciembre 09 2014
 Edwar Jaramillo:	* Para verificar si el paciente esta de alta de cirugía ya no es necesario revisar en movhos18, solo se tiene que consultar en que estado está según los campos
						de estado en la tabla tcx_11, para saber si esta cancelada se busca el código del turno en tcx_7.
					* Se adiciona una nueva columna para verificar en que estado o proceso va el paciente en cirugía.

 Diciembre 04 2014
 Edwar Jaramillo:	* Nueva funcionalidad para cerrar el ingreso y devolución del mercado desde el almacen, el grabador de MOS puede liquidar todos el mercado solo cuando desde
						el almacen cierren el mercado, los grabadores de almacen no deben tener activa la opción de liquidar mercado, cuando cierran el mercado los funcionarios
						del almacen ya no podrán cargar y devolver para esa historia e ingreso.
					* La opción de cerrar mercado solo debe aparecer cuando el paciente esta en alta definitiva.
					* El query que lista las cirugías por fecha se modifica para que tenga en cuenta la tabla root_36 y la empresa a la que pertenece el ingreso (wemp_pmla),
						porque se estaban mostrando cirugías de otras entidades (por ejemplo de clinica del sur).
					* Nueva columna para mostrar alta definitiva y otra para la nueva opción de cerrar mercado.
					* Se muestra boton de cerrar en la ventanas modales.
					* La lista de cirugías ahora se actualiza cuando cierran las ventanas modales.
					* Al proceso de liquidación se le adiciona un paso para seleccionar el centro de costos de cirugía al que se le va a grabar el mercado (segundo y tercer piso).
						Se muestra una ventana modal con un seleccionador de centro de costos y el onchange se encarga de abrir la ventana modal de liquidación de mercado (seteando
						el campo de centro de costos).

 Diciembre 01 2014
 Edwar Jaramillo:	* En la tabla 000228 se crean nuevos campos para controlar permisos sobre las opciones del monitor de mercados, de igual manera se implementan los permisos en
						el programa monitor para que dependiendo del rol de usuario se le puedan mostrar las diferentes opciones de grabar, devolver, liquidar, entre otros.
					* Se modifica el query que consulta las cirugías sin mercado para que tenga en cuenta si ya hay insumos liquidados, tambie se adicionan otras variables en
						el query para facilitar la manipulación de los permisos sobre las opciones de mercados. Las opciones devolver, grabar, liquidar, entre otras se muestran
						dependiendo de si ya esta liquidado o no el mercado además de los permisos configurados para ver o no las opciones.
					* Se crea la función javascript "liquidarMercadoUnix" para mostrar una ventana tipo dialog donde se muestra lo cargado, lo devuelto y el saldo, además de la tarifa y
						el total a liquidar.
					* En el llamado a algunas funciones se complementan los parámetros porque el encabezado de la función tenían más parámetros con los que se debían llamar.
 --------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
*/

//--------------------------------------------------------------------------------------------------------------------------------------------
// 	EJECUCION DEL SCRIPT
//--------------------------------------------------------------------------------------------------------------------------------------------

if(!isset($_SESSION['user']))
{
    echo '  <div style="color: #676767;font-family: verdana;background-color: #E4E4E4;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}
else
{
	//header('Content-type: text/html;charset=ISO-8859-1');
	$user_session = explode('-',$_SESSION['user']);
	$wuse = $user_session[1];
	include_once("root/comun.php");
	//mysql_select_db("matrix");
	// include "../../gesapl/procesos/gestor_aplicaciones_config.php";
	// include_once("../../gesapl/procesos/gesapl_funciones.php");
	include_once("ips/funciones_facturacionERP.php");
	$conex 			= obtenerConexionBD("matrix");

	$wbasedato 		= consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
	$wfecha			= date("Y-m-d");
    $whora 			= date("H:i:s");
    $RAIZ_MATRIX = $_SERVER['HTTP_HOST'].'/matrix';


//=====================================================================================================================================================================
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================

//-------------------------------------------------------------------------------------

//-------------------------------------------------------------------------------------
/**
 * [seguimiento description: Función para uso solo de desarrollo, en ambiente local, crea un archivo de texto donde se imprimen variables y arrays para su seguimiento]
 * @param  [type] $seguir [Cadena de texto a guardar en el archivo, para guardar array recordar usar print_r($al_array, true), puede usar saltos de línea PHP así PHP_EOL ]
 * @return [type]         [description]
 */
function seguimiento($seguir)
{
    /*if (file_exists("seguimiento.txt")) {
        unlink("seguimiento.txt");
    }*/
    $fp = fopen("seguimiento.txt","a+");
    fwrite($fp, "[".date("Y-m-d H:i:s")."]".PHP_EOL.$seguir);
    fclose($fp);
}

/*function mostrarPendientesRegrabacion()
{
	global $wbasedato;
	global $conex;

	$numfilas = 0;
	// $q = "SELECT Fecha_data, Tophis, Toping, Topmen
			// FROM ".$wbasedato."_000229
		   // WHERE Topest ='on'
		   // ";
	// $res1 = mysql_query($q,$conex) or die ("Query (nombreConcepto): ".$q." - ".mysql_error());

	//$numfilas = $numfilas + mysql_num_rows($res1);

	$q = "SELECT ".$wbasedato."_000106.Fecha_data, Tcarhis,Empnom ,Tcaring,Tcarres,Tcarconcod,Tcarprocod,Tcarpronom, Tcarser, Tcarvun,Tcarvun, Tcartar , Concat('Cobro Segundo responsable sin terminar saldo del primero (cargo:' , ".$wbasedato."_000106.id, ')') as mensaje , Tcarppr, ".$wbasedato."_000106.id
			FROM ".$wbasedato."_000106 , ".$wbasedato."_000024
		   WHERE Tcarppr ='CR'
			 AND Empcod = Tcarres
			 AND Tcarest ='on' ";
	$res2 = mysql_query($q,$conex) or die ("Query (nombreConcepto): ".$q." - ".mysql_error());



	$numfilas =$numfilas + mysql_num_rows($res2);

	$html = "<br><br><table align='center' width='90%' >
						<tr>
							<td colspan='3'></td>
							<td colspan='3' class='encabezadoTabla'>Numero de registros: ".$numfilas."</td>
						</tr>
						<tr class='encabezadoTabla' align='center'>
								<td>Fecha</td>
								<td>Historia</td>
								<td>Ingreso</td>
								<td>Problema</td>
								<td></td>
						</tr>";

	$i=0;

	//while ($row_q = mysql_fetch_array($res1))
	//{

	//	if (($i%2)==0)
	//		$wcf="fila1";  // color de fondo de la fila
	//	else
	//		$wcf="fila2"; // color de fondo de la fila
	//	//------
	//	$i++;
	//	$html .="<tr class='".$wcf."' align='center'>";
	//	$html .="<td >".$row_q['Fecha_data']."</td>";
	//	$html .="<td>".$row_q['Tophis']."</td>";
	//	$html .="<td>".$row_q['Toping']."</td>";
	//	$html .="<td>".$row_q['Topmen']."</td>";
	//	$html .="<td><a style='cursor : pointer' onclick='abrir_programa(\"".$row_q['Tophis']."\", \"".$row_q['Toping']."\" )'>Ir </a></td>";
	//	$html .="</tr>";
	//}


	$i=0;
	while ($row_q = mysql_fetch_array($res2))
	{

		if (($i%2)==0)
			$wcf="fila1";  // color de fondo de la fila
		else
			$wcf="fila2"; // color de fondo de la fila
		//------
		$i++;
		$html .="<tr class='".$wcf."' align='center'>";
		$html .="<td >".$row_q['Fecha_data']."</td>";
		$html .="<td>".$row_q['Tcarhis']."</td>";
		$html .="<td>".$row_q['Tcaring']."</td>";

		$html .="<td>Cobro Segundo responsable sin terminar saldo del primero (cargo:".$row_q['id'].")</td>";


		$html .="<td><a style='cursor:pointer' onclick='abrir_programa(\"".$row_q['Tcarhis']."\", \"".$row_q['Tcaring']."\" )'> Ir </a></td>";
		$html .="</tr>";
	}

	$q = "SELECT ".$wbasedato."_000106.Fecha_data, Tcarhis,Empnom ,Tcaring,Tcarres,Tcarconcod,Tcarprocod,Tcarpronom, Tcarser, Tcarvun,Tcarvun, Tcartar , Concat('Cobro Segundo responsable sin terminar saldo del primero (cargo:' , ".$wbasedato."_000106.id, ')') as mensaje , Tcarppr, ".$wbasedato."_000106.id
			FROM ".$wbasedato."_000106 , ".$wbasedato."_000024
		   WHERE Tcarpar ='on'
		     AND Empcod = Tcarres
			 AND Tcarest ='on' ";
	$res = mysql_query($q,$conex) or die ("Query (nombreConcepto): ".$q." - ".mysql_error());


	$numfilas =$numfilas + mysql_num_rows($res);

	while ($row_q = mysql_fetch_array($res))
	{

		if (($i%2)==0)
			$wcf="fila1";  // color de fondo de la fila
		else
			$wcf="fila2"; // color de fondo de la fila
		//------
		$i++;
		$html .="<tr class='".$wcf."' align='center'>";
		$html .="<td >".$row_q['Fecha_data']."</td>";
		$html .="<td>".$row_q['Tcarhis']."</td>";
		$html .="<td>".$row_q['Tcaring']."</td>";

		$html .="<td>Paralelo (cargo:".$row_q['id'].")</td>";


		$html .="<td><a style='cursor:pointer' onclick='crear_tarifa(\"".$row_q['Tcarhis']."\", \"".$row_q['Tcaring']."\" , \"".$row_q['Tcarres']."\" ,  \"".$row_q['Tcarconcod']."\",\"".$row_q['Tcarprocod']."\", \"".$row_q['Tcarpronom']."\" , \"".$row_q['Tcarser']."\", \"".$row_q['Tcarvun']."\" , \"".$row_q['Tcartar']."\",  \"".$row_q['id']."\" , \"".$row_q['Empnom']."\" , \"correo\" , \"\"  , \"\" , \"106\")'> Ir </a></td>";
		$html .="</tr>";
	}



	$html .= "</table>";

	return $html;

}*/

function mostrarPendientesRegrabacion(){
	global $wbasedato;
	global $conex;
	$html=  "<b>Tipo de paciente:&nbsp;</b>
							<select id='tipoPacienteregrabacion' style='border-radius: 4px;border:1px solid #AFAFAF;' onChange='pintarCargosxCambioResponsable()'>
								<option value='*'>Todos</option>
								<option value='A'>Ambulatorio</option>
								<option value='H'>Hospitalario</option>
							</select>&nbsp;&nbsp;&nbsp;";

	$html .= "<br><div id='divcambioresponsable'>".pintarCargosxCambioResponsable()."</div>";

	return $html;
}

function pintarCargosxCambioResponsable($tipoPaciente='*')
{

	global $wbasedato;
	global $conex;
	global $wemp_pmla;
	$wbasedatoMovhos	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	$conexUnix			= odbc_connect('facturacion','informix','sco');

	$numfilas = 0;
	$responsables = array();

	$query = "SELECT Empcod, Empnom
			    FROM {$wbasedato}_000024
		       WHERE 1";
	$res2 = mysql_query($query,$conex) or die ("Query (Empresas): ".$query." - ".mysql_error());
	while( $rowEmp = mysql_fetch_array( $res2 )){
		$responsables[$rowEmp[0]] = $rowEmp[1];
	}


	$q = "SELECT ".$wbasedato."_000282.Fecha_data, Prehis, Preing, Prerea, Preren
			FROM ".$wbasedato."_000282 , ".$wbasedato."_000101 ,  ".$wbasedatoMovhos."_000018 , ".$wbasedatoMovhos."_000011
						 WHERE Prehis =  Inghis
						   AND Preing =  Ingnin
						   AND Prehis =  Ubihis
						   AND Preing =  Ubiing
						   AND Ubisac =  Ccocod
						   AND Preest =  'on' ";

	if($tipoPaciente != '*' )
					$q.= (($tipoPaciente == 'H') ? "AND Ccohos = 'on' " : "AND Ccohos != 'on' ");

	$res2 = mysql_query($q,$conex) or die ("Query (nombreConcepto): ".$q." - ".mysql_error());

	$numfilas =$numfilas + mysql_num_rows($res2);

	$html = "<br><br><table align='center' width='90%' >
						<tr>
							<td colspan='10' align='right'>Numero de registros: ".$numfilas."</td>
						</tr>
						<tr class='encabezadoTabla' align='center'>
								<td rowspan='2'>Fecha</td>
								<td rowspan='2'>Historia</td>
								<td rowspan='2'>Ingreso</td>
								<td colspan='2'>Responsable Anterior</td>
								<td colspan='2'>Responsable Nuevo</td>
								<td rowspan='2'>Estado</td>
								<td rowspan='2'>Acci&oacute;n</td>
						</tr>
						<tr class='encabezadoTabla' align='center'>
								<td>Codigo</td>
								<td>Nombre</td>
								<td>Codigo</td>
								<td>Nombre</td>
						</tr>";
	$i=0;
	while ($row_q = mysql_fetch_array($res2)){

		// --> Consultar si el ingreso está activo en unix.
		$estado = "";
		$sqlIngAct = "
		SELECT pacnum
		  FROM INPAC
		 WHERE pachis = '".$row_q['Prehis']."'
		";
		$resIngAct = odbc_exec($conexUnix, $sqlIngAct);
		if(odbc_fetch_row($resIngAct))
		{
			if(trim(odbc_result($resIngAct,'pacnum')) != $row_q['Preing'])
				$estado = "<span style='color:red'>Inactivo</span>";
		}
		else
			$estado = "<span style='color:red'>Inactivo</span>";

		// --> Consultar si ya está facturado
		$sqlFact = "
		SELECT count(*) AS CANT
		  FROM FAMOV
		 WHERE movhis = '".$row_q['Prehis']."'
		   AND movnum = '".$row_q['Preing']."'
		   AND movfuo = '01'
		   AND movanu = '0'
		";
		$resFact = odbc_exec($conexUnix, $sqlFact);
		if(odbc_fetch_row($resFact))
		{
			if(odbc_result($resFact,'CANT') > 0)
				$estado = "<span style='color:blue'>Facturado";
		}


		if (($i%2)==0)
			$wcf="fila1";  // color de fondo de la fila
		else
			$wcf="fila2"; // color de fondo de la fila
		//------
		$i++;
		$html .="<tr class='".$wcf."' align='center'>";
		$html .="<td >".$row_q['Fecha_data']."</td>";
		$html .="<td>".$row_q['Prehis']."</td>";
		$html .="<td>".$row_q['Preing']."</td>";

		$html .="<td align='left'>{$row_q['Prerea']}</td><td align='left'>".((array_key_exists($row_q['Prerea'], $responsables)) ? $responsables[$row_q['Prerea']] : '')."</td> <td align='left'> {$row_q['Preren']} </td> <td align='left'> ".((array_key_exists($row_q['Preren'], $responsables)) ? $responsables[$row_q['Preren']] : '')."</td>";

		$html .="<td>".$estado."</td>";

		$html .="
		<td>
		<button style='cursor:pointer' onclick='abrir_programa(\"".$row_q['Prehis']."\", \"".$row_q['Preing']."\" )'> Ir </button>
		&nbsp;|&nbsp;
		<button style='cursor:pointer' onclick='omitirRegrabacion(\"".$row_q['Prehis']."\", \"".$row_q['Preing']."\", this)'> Omitir</button>
		</td>";

		$html .="</tr>";
	}

	$html .= "</table>";

	odbc_close($conexUnix);
	odbc_close_all();

	return $html;

}

function empresaEmpleado($wemp_pmla, $conex, $wbasedato, $cod_use_emp)
{
	$use_emp = '+';

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


function cargos_sin_tarifa ()
{

	global $wbasedato;
	global $conex;
	global $wemp_pmla;


	// Consulta que trae los cargos sin tarifa
	// El primer query los trae desde  la tabla cliame_000106 (tabla principal) tambien se consultan otras para traer
    // informacion basica del paciente
	//
	// El segundo query los trae desde el procedimiento de auditoria tabla principal cliame_000253 (tabla principal) tambien se consultan otras para traer
    // informacion basica del paciente

	// concepto_honorario_erp  trae el concepto de honorarios por defecto
	$concepto_honorario_erp = consultarAliasPorAplicacion($conex, $wemp_pmla, 'concepto_honorario_erp');
	$nombrehonorarios = "SELECT Grudes
						   FROM ".$wbasedato."_000200
						  WHERE Grucod = '".$concepto_honorario_erp."'";

	$res = mysql_query($nombrehonorarios,$conex) or die ("Query (nombreConcepto): ".$nombrehonorarios." - ".mysql_error());
	$desconcepto ='';
	if ($row = mysql_fetch_array($res))
	{

		$desconcepto =$row['Grudes'];
	}


	// --> 	Jerson trujillo, se comenta el query ya que este está considerando la homologación de procedimientos desde el
	//		monitor de autorizaciones de cx. 2016-04-01

	/*$q = "SELECT ".$wbasedato."_000106.Fecha_data AS Fecha, Tcarhis AS Historia, Tcaring AS Ingreso ,
				  Tcarres AS Responsable,Tcarconcod AS Concepto,Tcarprocod AS Procedimiento ,Tcarpronom AS NombrePro,
				  Tcarser AS Servicio,Tcarvun AS Valor,Tcarfec AS FechaCargo , Tcartar AS Tarifa ,
				  Concat('Cobro Segundo responsable sin terminar saldo del primero (cargo:' , ".$wbasedato."_000106.id, ')') AS Mensaje ,
				  ".$wbasedato."_000106.id  AS Id, Empnom  AS Empnom, Grudes AS Descconcepto,
				  '' AS CodigoProEmpresa , '' AS NombreProEmpresa , 'Cargos' AS Origen , 'Cargos' AS 'Programa' ,
				  Descripcion  AS usuario, '' AS Turno
			FROM ".$wbasedato."_000106 , ".$wbasedato."_000024 , ".$wbasedato."_000200 ,  usuarios
	   WHERE  Tcarppr = 'PT'
		 AND  Empcod = Tcarres
		 AND  Tcarconcod = Grucod
			 AND  Tcarest = 'on'
			 AND  Tcarusu = Codigo


		   UNION

		   SELECT ".$wbasedato."_000253.Fecha_data AS Fecha, Auehis AS Historia ,Aueing AS Ingreso,
				  Ingcem AS Responsable , '".$concepto_honorario_erp."' AS Concepto , Audcho  AS Procedimiento , Pronom AS NombrePro  ,
				  '' AS Servicio , '' AS Valor , '' AS FechaCargo , Emptar AS Tarifa ,
				  '' AS Mensaje ,
				  ".$wbasedato."_000253.id  AS Id , Empnom  AS Empnom , '".$desconcepto."' AS  Descconcepto,
				   Audpro  AS CodigoProEmpresa , Cprnom  AS NombreProEmpresa , 'Auditoria' AS Origen , 'Auditoria' As Programa,
				   Descripcion  AS usuario , Auetur AS Turno
		     FROM ".$wbasedato."_000252 , ".$wbasedato."_000253 , ".$wbasedato."_000101 , ".$wbasedato."_000024 , ".$wbasedato."_000103 , ".$wbasedato."_000254,usuarios
			WHERE  Audtur = Auetur
			  AND  Audpct = 'on'
			  AND  Inghis = Auehis
			  AND  Ingnin = Aueing
			  AND  Ingcem = Empcod
			  AND  Audcho = Procod
			  AND  Audpro = Cprcod
			  AND  Empcod = Cprnem
			  AND  Aueuad = Codigo

			ORDER BY Programa , Fecha ";*/

	$q = "SELECT ".$wbasedato."_000106.Fecha_data AS Fecha, Tcarhis AS Historia, Tcaring AS Ingreso ,
				  Tcarres AS Responsable,Tcarconcod AS Concepto,Tcarprocod AS Procedimiento ,Tcarpronom AS NombrePro,
				  Tcarser AS Servicio,Tcarvun AS Valor,Tcarfec AS FechaCargo , Tcartar AS Tarifa ,
				  Concat('Cobro Segundo responsable sin terminar saldo del primero (cargo:' , ".$wbasedato."_000106.id, ')') AS Mensaje ,
				  ".$wbasedato."_000106.id  AS Id, Empnom  AS Empnom, Grudes AS Descconcepto,
				  '' AS CodigoProEmpresa , '' AS NombreProEmpresa , 'Cargos' AS Origen , 'Cargos' AS 'Programa' ,
				  Descripcion  AS usuario, '' AS Turno
			FROM ".$wbasedato."_000106 , ".$wbasedato."_000024 , ".$wbasedato."_000200 ,  usuarios
	   WHERE  Tcarppr = 'PT'
		 AND  Empcod = Tcarres
		 AND  Tcarconcod = Grucod
			 AND  Tcarest = 'on'
			 AND  Tcarusu = Codigo
			ORDER BY Programa , Fecha ";

	$res = mysql_query($q,$conex) or die ("Query (nombreConcepto): ".$q." - ".mysql_error());
	$numfilas = mysql_num_rows($res);

	$i=0;
	$html = "<br><br>
		<table align='center' width='90%' >
			<tr>
			<td colspan='4' ></td>
			<td colspan ='1'class='encabezadotabla' ># de registros: </td><td align='center' class='encabezadotabla'>".$numfilas."</td>
			</tr>
			<tr class='encabezadoTabla' align='center'>
				<td width='5%' align='center' >Origen</td>
				<td width='5%' >Fecha</td>
				<td width='5%' >Historia</td>
				<td >Problema</td>
				<td>Usuario</td>
				<td width='2%'>Crear</td>
			</tr>";

	while ($row_q = mysql_fetch_array($res))
	{

		if (($i%2)==0)
			$wcf="fila1";  // color de fondo de la fila
		else
			$wcf="fila2"; // color de fondo de la fila
		//------
		if ($row_q['Programa']=='Auditoria')
		{
		  $color = '#008000';
		  $mensaje = "Falta crear tarifa del procedimiento:".substr($row_q['Procedimiento']."-".$row_q['NombrePro'],0,35)." para la empresa ".substr($row_q['Responsable']."-".$row_q['Empnom'],0,35);

		}
		else
		{
		  $color = 'blue' ;
		  $mensaje = "Falta crear tarifa del procedimiento:".substr($row_q['Procedimiento']."-".$row_q['NombrePro'],0,35)." para la empresa ".substr($row_q['Responsable']."-".$row_q['Empnom'],0,35);
		}

		$i++;
		$html .="<tr class='".$wcf."' >";
		$html .="<td align='center'><font color='".$color."'><b>".$row_q['Programa']."</b></font></td>";
		$html .="<td align='left'>".$row_q['Fecha']."</td>";
		$html .="<td align='right'>".$row_q['Historia']."-".$row_q['Ingreso']."</td>";
		$html .="<td align='left'><font style='font-size: 8pt'>".$mensaje."</font></td>";
		$html .="<td align='left'><font style='font-size: 7pt'>".$row_q['usuario']."</font></td>";
		$html .="<td align='right' ><input  type='button' onclick='crear_tarifa(\"".$row_q['Historia']."\", \"".$row_q['Ingreso']."\" , \"".$row_q['Responsable']."\" ,  \"".$row_q['Concepto']."\",\"".$row_q['Procedimiento']."\", \"".$row_q['NombrePro']."\" , \"".$row_q['Servicio']."\", \"".$row_q['Valor']."\" , \"".$row_q['Tarifa']."\", \"".$row_q['FechaCargo']."\", \"".$row_q['Id']."\" , \"".$row_q['Empnom']."\" , \"".$row_q['Concepto']."\" , \"".$row_q['Descconcepto']."\" ,\"cargos\"  , \"".$row_q['CodigoProEmpresa']."\" , \"".$row_q['NombreProEmpresa']."\" ,  \"".$row_q['Origen']."\"  ,   \"".$row_q['Turno']."\")' value='crear'></td></tr>";
	}

	$html .= "</table>";

	return $html;



}

function anular_mercado ($conex, $wbasedato, $wemp_pmla, $data, $wcedula, $wcodigoturno)
{
	// global $conex;
	// global $wbasedato;

	$wbotiquin        = consultarAliasAplicacion($conex, $wemp_pmla, 'botiquin_cirugia_ERP');
	$wbasedato_movhos = consultarAliasAplicacion($conex, $wemp_pmla, 'movhos');

	$user_session      = explode('-',$_SESSION['user']);
	$wuse              = $user_session[1];
	$wfecha            = date("Y-m-d");
	$whora             = date("H:i:s");
	$data["query"]     = "";
	$data["Query_Err"] = "";

	// Ver MERCADO_SOLO_AL_TURNO Para que solo actualice lo del turno y no se presente un inconveniente si ha cambiado la historia o ingreso, cambio realizado en otra sección.
	// $query = "DELETE
	// 			FROM  {$wbasedato}_000207
	// 			WHERE  Mpadoc = '{$wcedula}'
	// 			  AND  Mpatur = '{$wcodigoturno}'
	// 			  AND  Mpaest = 'on'
	// 			  AND  Mpaliq = 'off' ";
	$sql = "SELECT  Medico, Fecha_data, Hora_data, Mpahis, Mpaing, Mpapro, Mpaper, Mpaefa, Mpaela, Mpacom, Mpacan, Mpadev,
					Mpaliq, Mpalux, Mpacal, Mpacfa, Mpacrm, Mpafcm, Mpahcm, Mpaucm, Mpaest, Mpadoc, Mpatur, Seguridad
			FROM 	{$wbasedato}_000207
			WHERE 	Mpatur = '{$wcodigoturno}'
					AND  Mpaest = 'on'
					AND  Mpaliq = 'off'";
	if($resultSel = mysql_query($sql,$conex))
	{
		$insertLog = "INSERT INTO {$wbasedato}_000245 (Medico, Fecha_data, Hora_data, Mpahis, Mpaing, Mpapro, Mpaper, Mpaefa, Mpaela, Mpacom, Mpacan, Mpadev,
													  Mpaliq, Mpalux, Mpacal, Mpacfa, Mpacrm, Mpafcm, Mpahcm, Mpaucm, Mpaest, Mpadoc, Mpatur, Seguridad,
													  Mpabrm, Mpafbm, Mpahbm)";
		$arr_insertLog = array();
		while($row = mysql_fetch_array($resultSel))
		{
			$arr_insertLog[] = "('{$row["Medico"]}', '{$row["Fecha_data"]}', '{$row["Hora_data"]}', '{$row["Mpahis"]}', '{$row["Mpaing"]}', '{$row["Mpapro"]}', '{$row["Mpaper"]}', '{$row["Mpaefa"]}', '{$row["Mpaela"]}', '{$row["Mpacom"]}', '{$row["Mpacan"]}', '{$row["Mpadev"]}',
								  '{$row["Mpaliq"]}', '{$row["Mpalux"]}', '{$row["Mpacal"]}', '{$row["Mpacfa"]}', '{$row["Mpacrm"]}', '{$row["Mpafcm"]}', '{$row["Mpahcm"]}', '{$row["Mpaucm"]}', '{$row["Mpaest"]}', '{$row["Mpadoc"]}', '{$row["Mpatur"]}', '{$row["Seguridad"]}',
								  '{$wuse}', '{$wfecha}', '{$whora}')";
		}

		if(count($arr_insertLog) > 0)
		{
			$insertLog .= " VALUES ".implode(",", $arr_insertLog);
			if($resultLog = mysql_query($insertLog,$conex))
			{
				$query = "	DELETE FROM  {$wbasedato}_000207
							WHERE  	Mpatur = '{$wcodigoturno}'
									AND  Mpaest = 'on'
									AND  Mpaliq = 'off'";
				$data["query"] .= $query;
				if($result = mysql_query($query,$conex))
				{
					$delApl = " DELETE
                                FROM    {$wbasedato_movhos}_000227
                                WHERE   Carbot = '{$wbotiquin}'
                                        AND Cartur = '{$wcodigoturno}'";

                    if($result227 = mysql_query($delApl,$conex))
                    {
                        $delAplDlle = " DELETE
                                        FROM    {$wbasedato_movhos}_000228
                                        WHERE   Movbot = '{$wbotiquin}'
                                                AND Movtur = '{$wcodigoturno}'";

                        if($result228 = mysql_query($delAplDlle,$conex)){
                        }
                        else
                        {
                            $data["error"] = 1;
                            $data["mensaje"] = "No se pudo eliminar insumos desde detalle de la aplicacion Qx";
                            $data["resultado"] = "Error: ".mysql_errno()." - en el query  ".$delAplDlle." - ".mysql_error();
                        }
                    }
                    else
                    {
                        $data["error"] = 1;
                        $data["mensaje"] = "No se pudo eliminar insumos desde la aplicacion Qx";
                        $data["resultado"] = "Error: ".mysql_errno()." - en el query  ".$delApl." - ".mysql_error();
                    }

					$sql = "UPDATE  {$wbasedato}_000240
						            SET Lotest='off',
						                Lotfmo='{$wfecha}',
						                Lothmo='{$whora}',
						                Lotusm='{$wuse}'
						    WHERE   Lottur = '{$wcodigoturno}'
						            AND Lotest='on'";
			        $data["query"] .= " | ".$sql;
					// $query .= " | ".$sql;
					if($result240 = mysql_query($sql,$conex))
					{
					}
					else
					{
						$data["error"] = 1;
						// $data["mensaje"] = "No se pudo eliminar los lotes relacionados";
						$data["Query_Err"] = "Error: ".mysql_errno()." - en el query  ".$sql." - ".mysql_error();
					}

					// Marcar en el encabezado de auditoría que el mercado fue anulado.
					$sqlSM = "  UPDATE  {$wbasedato}_000252
						            SET Aueman='on'
						        WHERE   Auetur = '{$wcodigoturno}'";
			        $data["query"] .= " | ".$sqlSM;
					if($result252= mysql_query($sqlSM,$conex))
					{
					}
					else
					{
						$data["error"] = 1;
						// $data["mensaje"] = "No se pudo eliminar los lotes relacionados";
						$data["Query_Err"] = "Error: ".mysql_errno()." - en el query  ".$sqlSM." - ".mysql_error();
					}
				}
				else
				{
					$data["error"] = 1;
					// $data["mensaje"] = "No se pudo eliminar los lotes relacionados";
					$data["Query_Err"] = "Error: ".mysql_errno()." - en el query  ".$sql." - ".mysql_error();
				}
			}
			else
			{
				$data["error"] = 1; // Error al insertar en el log
				$data["Query_Err"] = "Error: ".mysql_errno()." - en el query  ".$insertLog." - ".mysql_error();
			}
		}
		else
		{
			$data["error"] = 1; // No se encontraron insumos asociados al turno
			$data["Query_Err"] = "Error: ".mysql_errno()." - en el query  ".$sql." - ".mysql_error();
		}

	}
	else
	{
		$data["error"] = 1;
		$data["Query_Err"] = "Error: ".mysql_errno()." - en el query  ".$sql." - ".mysql_error();
	}

	return $data;
}

/**
 * [cerrarMercado: Función encargado de marcar todos los insumos activos de una historia e ingreso a estado cerrado, para permitir liquidar el mercado completo o cargar y devolver
 * 					materiales de alto costo.]
 * @param  [type] conex      [description]
 * @param  [type] wemp_pmla  [description]
 * @param  [type] wbasedato  [description]
 * @param  [type] $whistoria [description]
 * @param  [type] $wingreso  [description]
 * @param  [type] $wcedula   [description]
 * @return [type]            [description]
 */
function cerrarMercado($data, $conex, $wemp_pmla, $wbasedato, $wuse, $whistoria, $wingreso, $wcedula, $wcodigoturno,$wperfil)
{
	$data["sql_cerrar_mercado"] = "";
	$fecha_actual = date("Y-m-d");
	$hora_actual  = date("H:i:s");

	if($wperfil=='almacen')
	{
		$sql = "UPDATE 	{$wbasedato}_000207
						SET Mpacrm    = 'on',
							Mpacal    = '1',
							Seguridad = 'C-{$wuse}',
							Mpafcm    = '$fecha_actual',
							Mpahcm    = '$hora_actual',
							Mpaucm    = '$wuse'
				WHERE 	Mpatur = '{$wcodigoturno}'
						AND Mpaest = 'on'
						AND Mpaliq = 'off'
						AND Mpalux = 'off'
						AND Mpaper = '{$wperfil}'";
						// Mpahis = '{$whistoria}'
						// AND Mpaing = '{$wingreso}'
						// AND Mpadoc = '{$wcedula}'
						// AND
	}
	if($wperfil =='facturador')
	{
		$sql = "UPDATE 	{$wbasedato}_000207
						SET Mpacrm    = 'on',
							Mpacfa    = '1',
							Seguridad = 'C-{$wuse}',
							Mpafcm    = '$fecha_actual',
							Mpahcm    = '$hora_actual',
							Mpaucm    = '$wuse'
				WHERE 	Mpatur = '{$wcodigoturno}'
						AND Mpaest = 'on'
						AND Mpaliq = 'off'
						AND Mpalux = 'off'
						AND Mpaper = '{$wperfil}'";
						// Mpahis = '{$whistoria}'
						// AND Mpaing = '{$wingreso}'
						// AND Mpadoc = '{$wcedula}'
						// AND
	}
	if(mysql_query($sql, $conex))
	{
		$data["mensaje"] = utf8_encode("Mercado cerrado");
		$data["sql_cerrar_mercado"] = $sql;
	}
	else
	{
		$data["error"] = 1;
		$data["mensaje"] = utf8_encode("No fue posible cerrar el mercado");
		$data["sql_cerrar_mercado"] = utf8_encode("Error en el query -cerrarMercado-: ".$sql."<br>Tipo Error:".mysql_error());
	}
	// $data["error"] = 1;
	// $data["mensaje"] = $sql;
	return $data;
}

function abrirMercado($data, $conex, $wemp_pmla, $wbasedato, $wuse, $whistoria, $wingreso, $wcedula, $wcodigoturno, $arr_roles_cx)
{
	$data["sql_cerrar_mercado"] = "";
	$data["sql_no_listo_liquidar"] = "";
	$sql = "UPDATE 	{$wbasedato}_000207
					SET Mpacrm = 'off',
						Mpacal = '0',
						Mpacfa = '0',
						Seguridad = 'C-{$wuse}'
			WHERE 	Mpatur = '{$wcodigoturno}'
					AND Mpaest = 'on'
					AND Mpaliq = 'off'
					AND Mpalux = 'off'";
					// Mpahis = '{$whistoria}'
					// AND Mpaing = '{$wingreso}'
					// AND Mpadoc = '{$wcedula}'
					// AND

	if(mysql_query($sql, $conex))
	{
		$data["mensaje"] = utf8_encode("Mercado Abierto.");
		$data["sql_cerrar_mercado"] = $sql;
		$sqlLista = "SELECT  Aueecx
					 FROM    {$wbasedato}_000252
					 WHERE   Auetur = '{$wcodigoturno}'
							 AND Auelli = 'on'
							 AND Auerau = ''";

		$data["sql_estabalistaliquidar"] = $sqlLista;
		if($resultEstaLiq = mysql_query($sqlLista, $conex))
		{
			if(mysql_num_rows($resultEstaLiq) > 0)
			{
				$rolQuePermiteAutorizarCx = consultarAliasPorAplicacion($conex, $wemp_pmla, 'rolQuePermiteAutorizarCx');
				$sqlAud = " UPDATE 	{$wbasedato}_000252
								    SET Auelli = 'off',
								        Aueecx = '{$rolQuePermiteAutorizarCx}'
						    WHERE 	Auetur = '{$wcodigoturno}'";

				if(mysql_query($sqlAud, $conex))
				{
					$data["mensaje"] .= " ".utf8_encode("No esta listo para liquidar.");
					$data["sql_no_listo_liquidar"] .= $sqlAud;

					/*$arr_estados_procesos = array(  ""   => '--',
													"FAC"=> 'Facturacion',
													"AUT"=> 'Autorizaciones',
													"AUD"=> 'Auditoria',
													"CCX"=> 'Coor. cirugia',
													"IDC"=> 'Auditoria IDC',
													"CNV"=> 'Convenios');*/
					$proceso_monitor = (array_key_exists($rolQuePermiteAutorizarCx, $arr_roles_cx)) ? utf8_decode($arr_roles_cx[$rolQuePermiteAutorizarCx]['abreviado']): '';

					// --> Guardar el log de movimientos
					$sqlLogMov = "	INSERT INTO {$wbasedato}_000259
											(Medico,				Fecha_data,			Hora_data,				Movtur,			Movest,	Movdes,					Movusu,			Seguridad)
									VALUES
											('{$wbasedato}', '".date("Y-m-d")."', '".date("H:i:s")."',	'{$wcodigoturno}',		'{$rolQuePermiteAutorizarCx}',	'Se abre mercado, el turno de cirugia queda en {$proceso_monitor}',	'{$wuse}',	'C-{$wuse}')";
					mysql_query($sqlLogMov, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlLogMov):</b><br>".mysql_error());

				}
				else
				{
					// $data["error"] = 1;
					// $data["mensaje"] = utf8_encode("No fue posible abrir el mercado");
					$data["sql_cerrar_mercado"] = utf8_encode("Error en el query -cambiarNoLiquidado-: ".$sql."<br>Tipo Error:".mysql_error());
				}
			}
		}
		else
		{
			$data["sql_estabalistaliquidar_ERROR"] = $sqlLista;
		}
	}
	else
	{
		$data["error"] = 1;
		$data["mensaje"] = utf8_encode("No fue posible abrir el mercado");
		$data["sql_cerrar_mercado"] = utf8_encode("Error en el query -abrirMercado-: ".$sql."<br>Tipo Error:".mysql_error());
	}
	return $data;
}


function traer_mercados($whistoria,$wingreso,$wemp_pmla,$wcedula, $wcodigoturno)
{
	global $conex;
	$wbasedato 		= consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
	$data = array();

	if($wcedula == '')
	{
		$query = "	SELECT 	Mpapro ,Pronom
					FROM  	{$wbasedato}_000207, {$wbasedato}_000103
					WHERE  	Mpahis = '{$whistoria}'
							AND  Mpaing = '{$wingreso}'
							AND  Mpapro = Procod
					GROUP BY Mpapro";

		$res	= mysql_query($query, $conex) or die("Error en el query: ".$query."<br>Tipo Error:".mysql_error());

		$i=0;
		$data = array();
		while($row = mysql_fetch_array($res))
		{
			$i++;
			$data[$i]['codigo'] = $row['Mpapro'];
			$data[$i]['nombre'] = $row['Pronom'];
			$data[$i]['query']  = $query;
		}

		$q = "	SELECT 	Mpapro, Paqnom
				FROM 	{$wbasedato}_000207 , {$wbasedato}_000113
				WHERE  	Mpahis = '{$whistoria}'
						AND  Mpaing = '{$wingreso}'
						AND  Mpapro = Paqcod
				GROUP BY Mpapro";

		$res	= mysql_query($q, $conex) or die("Error en el query: ".$q."<br>Tipo Error:".mysql_error());

		while($row = mysql_fetch_array($res))
		{
			$i++;
			$data[$i]['codigo'] = $row['Mpapro'];
			$data[$i]['nombre'] = $row['Paqnom'];
		}
	}
	else
	{
		$filtro = "Mpahis = '".$whistoria."'
			     	AND Mpaing = '".$wingreso."'";
		if($whistoria == '')
		{
			$filtro = "Mpadoc  = '".$wcedula."'";
		}
		else
		{
			$filtro .= " AND Mpadoc  = '".$wcedula."'";
		}

		$query = "	SELECT 	Mpapro, Pronom
					FROM  	{$wbasedato}_000207, {$wbasedato}_000103
					WHERE  	{$filtro}
							AND  Mpapro = Procod
					GROUP BY Mpapro";

		$res	= mysql_query($query, $conex) or die("Error en el query: ".$query."<br>Tipo Error:".mysql_error());

		$i=0;
		$data = array();
		while($row = mysql_fetch_array($res))
		{
			$i++;
			$data[$i]['codigo'] = $row['Mpapro'];
			$data[$i]['nombre'] = str_replace("(", "", utf8_encode($row['Pronom']));
			$data[$i]['query']  = $query;
		}

		$q = "	SELECT 	Mpapro, Paqnom
				FROM 	{$wbasedato}_000207 , {$wbasedato}_000113
				WHERE  	Mpadoc='{$wcedula}'
						AND  Mpapro = Paqcod
				GROUP BY Mpapro";

		$res	= mysql_query($q, $conex) or die("Error en el query: ".$q."<br>Tipo Error:".mysql_error());

		while($row = mysql_fetch_array($res))
		{
			$i++;
			$data[$i]['codigo'] = $row['Mpapro'];
			$data[$i]['nombre'] = utf8_encode($row['Paqnom']);

		}

	}
	return $data;
}

function actualizar_datos_mercado($whistoria,$wingreso,$wemp_pmla,$wcedula,$wcodigoturno)
{
	global $conex;
	$wbasedato 		= consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');

	$q = "UPDATE {$wbasedato}_000207
			 SET Mpahis ='".$whistoria."',
				 Mpaing ='".$wingreso."'  ,
				 Mpadoc ='".$wcedula."'
		   WHERE Mpatur = '".$wcodigoturno."'";

	$res	= mysql_query($q, $conex) or die("Error en el query: ".$q."<br>Tipo Error:".mysql_error());
}


function traer_mercados_devolucion($whistoria,$wingreso,$wemp_pmla, $opcion_liquidacion_unix='', $wcodigoturno)
{
	global $conex;
	$wbasedato 	 = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
	$haypaquetes = false;
	$hayprocedimientos = false;

	// if($whistoria !='')
	// {}

	$q = "SELECT Mpapro ,Pronom
			FROM  ".$wbasedato."_000207 , ".$wbasedato."_000103
			WHERE  Mpahis = '".$whistoria."'
			  AND  Mpaing = '".$wingreso."'
			  AND  Mpapro = Procod
			GROUP BY Mpapro";

	$res	= mysql_query($q, $conex) or die("Error en el query: ".$q."<br>Tipo Error:".mysql_error());
	//echo $q;
	$i=0;
	$data = array();
	while($row = mysql_fetch_array($res))
	{

			$hayprocedimientos = true;
	}

	$q = "SELECT Mpapro, Paqnom
		    FROM ".$wbasedato."_000207 , ".$wbasedato."_000113
			WHERE  Mpahis = '".$whistoria."'
			  AND  Mpaing = '".$wingreso."'
			  AND  Mpapro = Paqcod
			GROUP BY Mpapro";

	$res	= mysql_query($q, $conex) or die("Error en el query: ".$q."<br>Tipo Error:".mysql_error());



	while($row = mysql_fetch_array($res))
	{
		$haypaquetes = true;
	}

	if( $hayprocedimientos || $haypaquetes )
	{
		$data[1]['codigo'] = "Mercado";
		$data[1]['nombre'] = "Mercado General";
		$data[1]['q'] = $q;
	}

	return $data;
}


/**
 * [limpiarString: quita multiples espacios y espacios al final del string]
 * @param  [type] $string_ [description]
 * @return [type]          [description]
 */
function limpiarString($string_)
{
    return trim(preg_replace('/[ ]+/', ' ', $string_));
}

/**
 * [crearTemporalTurnosSinLiquidar: [EJ_01] función encargada de consultar e insertar en una tabla temporal todos los turnos que tienen mercado
 * 										y que no han sido liquidados]
 * @param  [type] $conex                  [description]
 * @param  [type] $wbasedato              [description]
 * @param  [type] $idx_tablejoin1         [description]
 * @param  [type] $idx_tablejoin2         [description]
 * @param  [type] $fecha_actual_rep_NoLiq [description]
 * @param  [type] $wfecha_ini_proceso     [description]
 * @return [type]                         [description]
 */
function crearTemporalTurnosSinLiquidar($conex, $wbasedato, $idx_tablejoin1, $idx_tablejoin2, $fecha_actual_rep_NoLiq, $wfecha_ini_proceso, $proceso_auditoria_activo_sfi)
{
	if($idx_tablejoin2 == '')
	{
		/*$sqltmp11 = "   CREATE TEMPORARY TABLE {$idx_tablejoin1}
			            (UNIQUE idx_turtur (Turtur_tmp))
                		SELECT Turtur AS Turtur_tmp, Enltur
						FROM 	{$wbasedato}_000207
								INNER JOIN
								tcx_000011 ON Mpatur = Turtur
								LEFT JOIN
								{$wbasedato}_000199 ON Enltur = Turtur AND Enlest = 'on'
						WHERE   Turfec < '{$fecha_actual_rep_NoLiq}' AND Turfec > '{$wfecha_ini_proceso}'
								AND Turest = 'on'
								AND Mpaest = 'on'
						GROUP BY Turtur
						HAVING Enltur IS NULL";*/

		$sqltmp11 = "   CREATE TEMPORARY TABLE {$idx_tablejoin1}
			            (UNIQUE idx_turtur (Turtur_tmp))
                		SELECT  Auetur AS Turtur_tmp, Enltur
						FROM    {$wbasedato}_000252
						        INNER JOIN {$wbasedato}_000207 ON (Mpatur = Auetur AND Mpaest = 'on')
						        LEFT JOIN {$wbasedato}_000199 ON (Enltur = Auetur AND Enlest = 'on')
						WHERE   Aueliq = 'off'
						        AND Aueman = 'off'
						GROUP BY Auetur
						HAVING Enltur IS NULL";
	}
	else
	{
		$sqltmp11       = " CREATE TEMPORARY TABLE {$idx_tablejoin2}
                            (UNIQUE idx_turtur (Turtur_tmp))
			                SELECT * FROM {$idx_tablejoin1}";

        // Lo siguiente es para poder simular la tabla temporal, dejarla como tabla fija y poder verla en la base de datos
        // despues de que termine del proceso de consulta.

		// $sqltmp11       = " CREATE TABLE {$idx_tablejoin2} (Turtur_tmp INT(11), Enltur INT(11))";
		// $resultTmp11 = mysql_query($sqltmp11, $conex) or die("Error en el query: ".$sqltmp11."<br>Tipo Error:".mysql_error());
		// $sqltmp11       = " INSERT INTO {$idx_tablejoin2} (Turtur_tmp, Enltur)
		//            SELECT * FROM {$idx_tablejoin1}";
	}

	$resultTmp11 = mysql_query($sqltmp11, $conex) or die("Error en el query: ".$sqltmp11."<br>Tipo Error:".mysql_error());

}

//---------------------------------------------------------------------------------------------------------------------
//	--> Funcion que valida si un articulo requiere autorizacion
//		Jerson trujillo, 2016-03-15
//---------------------------------------------------------------------------------------------------------------------
function articuloSeDebeAutorizar($infoArtMer, $codEnt, $nitEnt, $tipEnt, $planEmp)
{
	global $conex;
	global $wemp_pmla;
	global $wbasedato;

	// --> Generar query combinado para saber si alguna regla le aplica al articulo, para que este deba ser autorizado
	$variables = array();
	// --> Codigo del articulo
	$variables['Paucom']['combinar']	= true;
	$variables['Paucom']['valor'] 		= $infoArtMer['codigo'];
	// --> Grupo de articulo
	$variables['Paugru']['combinar'] 	= true;
	$variables['Paugru']['valor'] 		= $infoArtMer['grupo'];
	// --> Clasificacion
	$variables['Paucla']['combinar'] 	= true;
	$variables['Paucla']['valor'] 		= (($infoArtMer['clasificacionArt'] != '' && $infoArtMer['clasificacionArt'] != '*' && $infoArtMer['clasificacionArt'] != 'NO APLICA') ? $infoArtMer['clasificacionArt'] : $infoArtMer['clasificacionGru']);
	// --> Plan de empresa
	$variables['Paupla']['combinar'] 	= true;
	$variables['Paupla']['valor'] 		= $planEmp;
	// --> Entidad
	$variables['Paucem']['combinar'] 	= true;
	$variables['Paucem']['valor'] 		= $codEnt;
	// --> Nit Entidad
	$variables['Paunit']['combinar'] 	= true;
	$variables['Paunit']['valor'] 		= $nitEnt;
	// --> Tipo de empresa
	$variables['Pautem']['combinar'] 	= true;
	$variables['Pautem']['valor'] 		= $tipEnt;
	// --> Estado
	$variables['Pauest']['combinar'] 	= false;
	$variables['Pauest']['valor'] 		= 'on';

	// --> Obtener query
	$sqlDebeAuto = generarQueryCombinado($variables, $wbasedato."_000258");
	$resDebeAuto = mysql_query($sqlDebeAuto, $conex) or die("ERROR EN QUERY MATRIX (sqlDebeAuto): ".mysql_error());

	if($rowDebeAuto = mysql_fetch_array($resDebeAuto))
	{
		$sqlAut = "
		SELECT Paupau
		  FROM ".$wbasedato."_000258
		 WHERE id = '".$rowDebeAuto['id']."'
		";
		$resAut = mysql_query($sqlAut, $conex) or die("ERROR EN QUERY MATRIX (sqlAut): ".mysql_error());
		$rowAut = mysql_fetch_array($resAut);
		if($rowAut['Paupau'] == 'on')
			return true;
		else
			return false;
	}
	else
		return false;
}
//---------------------------------------------------------------------------------------------------------------------
//	--> Funcion que lee las cx con descripcion operatoria y las inserta en la tabla de procedimientos para auditar
//		Jerson trujillo, 2015-12-30
//---------------------------------------------------------------------------------------------------------------------
function llenarCirugiasParaAuditar()
{
	global $conex;
	global $wemp_pmla;
	global $wbasedato;

	$procesoAudActivo = consultarAliasPorAplicacion($conex, $wemp_pmla, 'proceso_auditoria_activo_sfi');
	if($procesoAudActivo != 'on')
		return;

	$wbasedatoHce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
	$wbasedatoTcx = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tcx');

	// --> Obtener las empresas que se deben auditar/aautorizar
	$arrayEmpAuditan = array();
	$sqlEmpAudi = "
	SELECT Empcod
	  FROM ".$wbasedato."_000024
	 WHERE Empest = 'on'
	   AND Empaam = 'on'
	";
	$resEmpAudi = mysql_query($sqlEmpAudi, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlEmpAudi):</b><br>".mysql_error());
	while($rowEmpAudi = mysql_fetch_array($resEmpAudi))
		$arrayEmpAuditan[] = $rowEmpAudi['Empcod'];

	// --> Obtener las excepciones de medicos y procedimientos, los cuales se autorizan automaticamente
	$arrayExcep = array();
	$sqlExcep = "
	SELECT Emacod, Ematpr, Ematce
	  FROM ".$wbasedato."_000257
	 WHERE Emaest = 'on'
	";
	$resExcep = mysql_query($sqlExcep, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlExcep):</b><br>".mysql_error());
	while($rowExcep = mysql_fetch_array($resExcep))
	{
		if($rowExcep['Ematpr'] == 'on')
			$tipo = 'Procedimientos';
		elseif($rowExcep['Ematce'] == 'on')
				$tipo = 'Medicos';

		$arrayExcep[$tipo][] = trim($rowExcep['Emacod']);
	}
	// --> Obtener los turnos de cx sin auditar
	$arrTurnosCx		= array();
	$sqlInfoTurnosCx 	= "
	SELECT Turtur, Turhis, Turnin, Turrcu, Turfec, Turhin, Ingcem
	  FROM ".$wbasedatoTcx."_000011 AS A LEFT JOIN ".$wbasedato."_000101 AS B ON (A.Turhis = B.Inghis AND A.Turnin = B.Ingnin)
	 WHERE Turest = 'on'
	   AND Turaud != 'on'
	   AND Turpea = 'on'
	   AND Turhis != ''
	   AND Turnin != ''
	   AND Turhis != '0'
	   AND Turnin != '0' ";

	$resInfoTurnosCx = mysql_query($sqlInfoTurnosCx, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInfoTurnosCx):</b><br>".mysql_error());
	while($rowInfoTurnosCx = mysql_fetch_array($resInfoTurnosCx))
	{
		$arrTurnosCx[$rowInfoTurnosCx['Turtur']]['Historia'] 		= trim($rowInfoTurnosCx['Turhis']);
		$arrTurnosCx[$rowInfoTurnosCx['Turtur']]['Ingreso'] 		= trim($rowInfoTurnosCx['Turnin']);
		$arrTurnosCx[$rowInfoTurnosCx['Turtur']]['cupTurCx'] 		= trim($rowInfoTurnosCx['Turrcu']);
		$arrTurnosCx[$rowInfoTurnosCx['Turtur']]['fechaTurno'] 		= trim($rowInfoTurnosCx['Turfec']);
		$arrTurnosCx[$rowInfoTurnosCx['Turtur']]['horaTurno'] 		= trim($rowInfoTurnosCx['Turhin']).":00";
		$arrTurnosCx[$rowInfoTurnosCx['Turtur']]['codEntidad'] 		= trim($rowInfoTurnosCx['Ingcem']);
		$arrTurnosCx[$rowInfoTurnosCx['Turtur']]['tieneDesOpe'] 	= false;
		$arrTurnosCx[$rowInfoTurnosCx['Turtur']]['tieneNotaOpe'] 	= false;
		$arrTurnosCx[$rowInfoTurnosCx['Turtur']]['codigosIguales'] 	= false;
	}

	// --> Consultar la descripcion operatoria de cada turno
	foreach($arrTurnosCx as $turno => &$infoTurno)
	{
		$infoTurno['procedimientosCups'] 	= array();
		$infoTurno['codigosProTurnoCx'] 	= array();
		$infoTurno['medicosTurnoCx'] 		= array();

		// --> Consultar lo procedimientos que registraron en el turno de cx
		$sqlPorTurCx = "
		SELECT Mcicod
		  FROM ".$wbasedatoTcx."_000008
		  WHERE Mcitur = '".$turno."' ";
		$resPorTurCx = mysql_query($sqlPorTurCx, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlPorTurCx):</b><br>".mysql_error());
		while($rowPorTurCx = mysql_fetch_array($resPorTurCx))
			$infoTurno['codigosProTurnoCx'][$rowPorTurCx['Mcicod']] = '';

		// --> Consultar los medicos que registraron en el turno de cx
		$sqlMedTurCx = "
		SELECT Mmemed
		  FROM ".$wbasedatoTcx."_000010
		  WHERE Mmetur = '".$turno."' ";
		$resMedTurCx = mysql_query($sqlMedTurCx, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlMedTurCx):</b><br>".mysql_error());
		while($rowMedTurCx = mysql_fetch_array($resMedTurCx))
			$infoTurno['medicosTurnoCx'][$rowMedTurCx['Mmemed']] = '';

		// --> 	Validar si para el turno ya existe descripcion operatoria.
		$sqlDesOpe = "
		SELECT Fecha_data, Hora_data
		  FROM ".$wbasedatoHce."_000077
		 WHERE movhis = '".$infoTurno['Historia']."'
		   AND moving = '".$infoTurno['Ingreso']."'
		   AND movpro = '000077'
		   AND movcon = '69'
		   AND movdat = '".$turno."' ";
		$resDesOpe = mysql_query($sqlDesOpe, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlDesOpe):</b><br>".mysql_error());
		while($rowDesOpe = mysql_fetch_array($resDesOpe))
		{
			$infoTurno['tieneDesOpe'] 	= true;

			// --> Obtener el campo 99(Procedimientos realizados cups)
			$sqlProDesOpe = "
			SELECT movdat
			  FROM ".$wbasedatoHce."_000077
			 WHERE Fecha_data 	= '".$rowDesOpe['Fecha_data']."'
			   AND Hora_data 	= '".$rowDesOpe['Hora_data']."'
			   AND movpro 		= '000077'
			   AND movcon 		= '99'
			   AND movhis 		= '".$infoTurno['Historia']."'
			   AND moving 		= '".$infoTurno['Ingreso']."'
			   AND movdat		!= ''
			";
			$resProDesOpe = mysql_query($sqlProDesOpe, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlProDesOpe):</b><br>".mysql_error());
			while($rowProDesOpe = mysql_fetch_array($resProDesOpe))
			{
				// --> Interpretar los codigos de los procedimientos cups
				if(trim($rowProDesOpe['movdat']) != '')
				{
					$str = explode("<option", trim($rowProDesOpe['movdat']));
					foreach($str AS $valor)
					{
						if(trim($valor) == '')
							continue;

						$valor 		= "<option".$valor;
						$valor 		= explode(' ', substr(strip_tags($valor), 4));
						$codigoPro 	= explode('(', trim($valor[0]));
						$codigoPro 	= $codigoPro[0];
						$codigoPro	= str_replace('.', '', $codigoPro);
						$infoTurno['procedimientosCups'][$codigoPro] = '';
					}
				}
			}
		}

		// --> Interpretar los codigos de procedimientos que vienen desde el turno de cx.
		$procedimientosCupsTurCx 	= array();
		$arrayCupsTurCx 			= explode('\r\n', $infoTurno['cupTurCx']);
		foreach($arrayCupsTurCx AS $indice => $codCupTurCx)
		{
			$codCupTurCx = explode('(', $codCupTurCx);
			$codCupTurCx = trim($codCupTurCx[0]);
			if($codCupTurCx != '')
				$procedimientosCupsTurCx[$codCupTurCx] = '';
		}

		$diferencias1 = array_diff_key($procedimientosCupsTurCx, $infoTurno['procedimientosCups']);
		$diferencias2 = array_diff_key($infoTurno['procedimientosCups'], $procedimientosCupsTurCx);

		// --> Si no hay difetencia entre los codigos del turno de la cx y los codigos de la descrpcion operatoria.
		if(count($diferencias1) == 0 && count($diferencias2) == 0)
			$infoTurno['codigosIguales'] = true;

		// --> Si no hay descripcion operatoria
		if(!$infoTurno['tieneDesOpe'])
		{
			// --> Validar si el paciente tiene nota operatoria.
			$sqlNotaOpera = "
			SELECT id
			  FROM ".$wbasedatoHce."_000036
			 WHERE Firhis 		= '".$infoTurno['Historia']."'
			   AND Firing 		= '".$infoTurno['Ingreso']."'
			   AND Firpro 		= '000316'
			   AND Firfir 		= 'on'
			   AND Fecha_data  >= '".$infoTurno['fechaTurno']."'
			";
			//AND Hora_data  >= '".$infoTurno['horaTurno']."'

			$resNotaOpera 	= mysql_query($sqlNotaOpera, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlNotaOpera):</b><br>".mysql_error());
			$arrNotaOpera 	= array();
			if($rowNotaOpera = mysql_fetch_array($resNotaOpera))
			{
				$infoTurno['tieneNotaOpe'] = true;
			}
		}
		// print_r($procedimientosCupsDesOpe);
		// print_r($infoTurno['procedimientosCups']);
		// print_r($diferencias1);
		// print_r($diferencias2);
	}

	// --> Recorrer el array de turnos con sus procedimientos, para insertarlos en la tabla de auditoria.
	foreach($arrTurnosCx as $turno => $infoTurno2)
	{
		$autorizada = '';

		// --> Validar si la cx tiene excepcion por procedimiento.
		foreach($infoTurno2['codigosProTurnoCx'] as $codPro => $val)
		{
			if(in_array(trim($codPro), $arrayExcep['Procedimientos']))
			{
				$autorizada = 'on';
				$tipoAuto	= 'EP'; // --> Excepcion por procedimiento
				break;
			}
		}

		foreach ($infoTurno2['medicosTurnoCx'] as $codMed => $val)
		{
			if(in_array(trim($codMed), $arrayExcep['Medicos']))
			{
				$autorizada = 'on';
				$tipoAuto	= 'EM'; // --> Excepcion por medico
				break;
			}
		}

		// --> Validar otras excepciones para sabe si se autoriza automaticamente
		if($autorizada == '')
		{
			// --> Si tiene nota operatoria y no tiene descripcion operatoria
			if($infoTurno2['tieneNotaOpe'] && !$infoTurno2['tieneDesOpe'])
			{
				$autorizada = 'on';
				$tipoAuto	= 'PA';
			}
			else
			{
				// --> Si no hay diferencias entre los codigos del turno de cx y la descripcion operatoria, se autoriza automaticamente
				if($infoTurno2['codigosIguales'])
				{
					$autorizada = 'on';
					$tipoAuto	= 'CI';
				}
				else
				{
					// --> Si la empresa del paciente tiene marcado que se debe autorizar
					if(in_array($infoTurno2['codEntidad'], $arrayEmpAuditan))
					{
						$autorizada = 'off';
						$tipoAuto	= '';
					}
					else
					{
						$autorizada = 'on';
						$tipoAuto	= 'NA';
					}
				}
			}
		}

		// --> Guardar el detalle de los procedimientos a auditar (Cups)
		if(count($infoTurno2['procedimientosCups']) > 0 && $autorizada == 'on')
		{
			foreach($infoTurno2['procedimientosCups'] as $codProCups => $valor2)
			{
				$sqlDetPro 	= "
				INSERT INTO ".$wbasedato."_000253(Medico,			Fecha_data,				Hora_data,				Audtur,			Audpro,				Audcon,				Audrec,	Audadi,	Audest,	Seguridad)
										  VALUES ('".$wbasedato."', '".date("Y-m-d")."',	'".date("H:i:s")."',	'".$turno."',	'".$codProCups."', '".$autorizada."', 	'off', 	'off', 	'on', 	'C-".$wbasedato."')
				";
				mysql_query($sqlDetPro, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlDetPro):</b><br>".mysql_error());
			}
		}

		// --> Guardar el encabezado del turno a auditar
		if($infoTurno2['tieneDesOpe'] || $infoTurno2['tieneNotaOpe'] || $tipoAuto == 'EP' || $tipoAuto == 'EM')
		{
			$fechaAut 	= ($autorizada == 'on') ? date("Y-m-d")." ".date("H:i:s") : "0000-00-00 00:00:00";

			$sqlEncPro = "
			INSERT INTO ".$wbasedato."_000252(Medico, 			Fecha_data, 			Hora_data, 				Auetur, 		Auehis, 						Aueing, 						Auerau, 			Aueaud, 			Auefau, 		Aueliq, Aueest, Seguridad)
									  VALUES ('".$wbasedato."', '".date("Y-m-d")."',	'".date("H:i:s")."',	'".$turno."',	'".$infoTurno2['Historia']."',	'".$infoTurno2['Ingreso']."', 	'".$tipoAuto."',	'".$autorizada."', 	'".$fechaAut."','off', 	'on', 	'C-".$wbasedato."')
			";
			mysql_query($sqlEncPro, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlEncPro):</b><br>".mysql_error());

			// --> Actualizar el turno de cx como ya revisado por el monitor
			$sqlUpsEst 	= "
			UPDATE ".$wbasedatoTcx."_000011
			   SET Turaud = 'on'
			 WHERE Turtur = '".$turno."' ";
			mysql_query($sqlUpsEst, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUpsEst):</b><br>".mysql_error());
		}
	}
}

function consultarlog($wdocumento, $wlinea, $whistoria, $wingreso, $wfuente,$wreg)
{
	global $conex;
	global $conexUnix;
	global $wemp_pmla;
	$conex = obtenerConexionBD("matrix");
	$wbasedato = consultarAliasAplicacion($conex, $wemp_pmla, 'movhos');



	//echo "<table align=center ><><tr><td>Historia:</td><td>".$whistoria."</td>"


	$findme   	= '|';
	$pos 		= strpos($wreg, $findme);



	conexionOdbc($conex, $wbasedato, $conexUnix, 'facturacion');

	if($pos === false)
	{


		 $sqlu = "SELECT logfec , logusu, logpro,idenom,ideap1
				   FROM Falog , Siide
				  WHERE logva1  = '".$whistoria." - ".$wingreso."'
					AND logva2  = '".$wfuente." - ".$wdocumento."'
					AND logreg  = '".$wreg."'
					AND logtip  = 'M'
					AND logusu = idecod
					GROUP BY 1,2,3,4,5

					UNION

				 SELECT logfec , logusu, logpro,idenom,ideap1
				   FROM ivlog, Siide
			      WHERE logva1  = '".$wfuente."'
			        AND logva2  = '".$wdocumento."'
				    AND logtip  = 'M'
					AND logusu = idecod
				  GROUP BY 1,2,3,4,5
				  ORDER BY 1 DESC";
	}
	else
	{
		$wreg = str_replace('|', ',', $wreg);

		$sqlu = "SELECT logfec , logusu, logpro,idenom,ideap1
				   FROM Falog , Siide
				  WHERE logva1  = '".$whistoria." - ".$wingreso."'
					AND logva2  = '".$wfuente." - ".$wdocumento."'
					AND logreg  IN ( ".$wreg." )
					AND logtip  = 'M'
					AND logusu = idecod
					GROUP BY 1,2,3,4,5

					UNION

				 SELECT logfec , logusu, logpro,idenom,ideap1
				   FROM ivlog, Siide
			      WHERE logva1  = '".$wfuente."'
			        AND logva2  = '".$wdocumento."'
				    AND logtip  = 'M'
					AND logusu = idecod
				  GROUP BY 1,2,3,4,5
				  ORDER BY 1 DESC";


	}


	//echo $sqlu;
	$resu = odbc_do( $conexUnix, $sqlu );

	$i=0;
	$encabezado = "<br><br><br><table align='center'><tr class='encabezadoTabla'><td colspan='4'>Resultado de Analisis de Modificacion en Logs de Unix</td></tr>
	<tr class=encabezadoTabla ><td colspan='4'>Historia : ".$whistoria."-".$wingreso."</td></tr>
	<tr class=encabezadoTabla align='left' class='fila1'>
	<td><b>Tipo de Registro</b></td>
	<td><b>Fecha de Registro</b></td>
	<td><b>Usuario </b></td>
	<td><b>Programa </b></td><tr>";
	if($resu)
	{
		$tr='';
		while( odbc_fetch_row($resu))
		{
			if (($i%2)==0)
			$wcf="fila1";  // color de fondo de la fila
			else
			$wcf="fila2"; // color de fondo de la fila
			$i++;

			$tr .=" <tr align='left' style='background-color: rgb(153, 153, 153); font-size: 10pt;'><td colspan='4'>Registro ".$i."</td></tr>
				   <tr class='".$wcf."'><td>Modificacion</td>
				   <td>".odbc_result($resu,1)."</td>
				   <td>".odbc_result($resu,2)."-".odbc_result($resu,4)." ".odbc_result($resu,5)." </td>
				   <td>".odbc_result($resu,3)."</td></tr>";


		}
	}



	if ($i>0)
	{
		echo $encabezado;
		echo $tr;
		echo "</table>";
		//echo "<br><br><table align='center'><tr><td><input type='button' value='Cerrar' onclick='cerrarmodal()'></td></tr></table>";
	}
	else
	{
		echo "<br><br><br><table align='center'><tr><td>No se encontraron datos</td></tr></table>";
	}


}
//---------------------------------------------------------------------------------------------------------------------
//	--> Verifica que turnos de la cliame_000252 tienen historia o ingreso diferente a la tcx_000011
//		y los actualiza.
//		Jerson trujillo, 2016-04-21
//---------------------------------------------------------------------------------------------------------------------
function actualizarHistoriasIngreso()
{
	global $wbasedato;
	global $conex;
	global $wemp_pmla;

	$wbasedatoTcx = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tcx');

	$sqlHisIng = "
	SELECT A.id, Turhis, Turnin
	  FROM ".$wbasedato."_000252 AS A INNER JOIN ".$wbasedatoTcx."_000011 AS B ON(Auetur = Turtur AND (Turhis != Auehis OR Turnin != Aueing))
	 WHERE Aueliq != 'on'
	   AND Auelli != 'on' ";

	$resHisIng = mysql_query($sqlHisIng, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlHisIng):</b><br>".mysql_error());
	while($rowHisIng = mysql_fetch_array($resHisIng))
	{
		$actulizarHisIng = "
		UPDATE ".$wbasedato."_000252
		   SET Auehis = '".$rowHisIng['Turhis']."',
		       Aueing = '".$rowHisIng['Turnin']."'
		 WHERE id 	  = '".$rowHisIng['id']."'
		";
		mysql_query($actulizarHisIng, $conex) or die("<b>ERROR EN QUERY MATRIX(actulizarHisIng):</b><br>".mysql_error());
	}
}
//---------------------------------------------------------------------------------------------------------------------
//	--> Funcion que lee las cx y las inserta en la tabla de procedimientos para auditar
//		Jerson trujillo, 2016-03-22
//---------------------------------------------------------------------------------------------------------------------
function llenarCirugiasParaAuditar2()
{
	global $conex;
	global $wemp_pmla;
	global $wbasedato;

	$procesoAudActivo 				= consultarAliasPorAplicacion($conex, $wemp_pmla, 'proceso_auditoria_activo_sfi');
	$estadoDeInicioAutorizacionCx 	= trim(consultarAliasPorAplicacion($conex, $wemp_pmla, 'estadoDeInicioAutorizacionCx'));
	$estadoDeInicioAutorizacionCx 	= (($estadoDeInicioAutorizacionCx == '') ? 'FAC' : $estadoDeInicioAutorizacionCx);

	if($procesoAudActivo != 'on')
		return;

	$wbasedatoHce 				= consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
	$wbasedatoTcx 				= consultarAliasPorAplicacion($conex, $wemp_pmla, 'tcx');

	// --> Obtener las empresas que se deben auditar/autorizar
	$arrayEmpAuditan = array();
	$sqlEmpAudi = "
	SELECT Empcod
	  FROM ".$wbasedato."_000024
	 WHERE Empest = 'on'
	   AND Empaam = 'on'
	";
	$resEmpAudi = mysql_query($sqlEmpAudi, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlEmpAudi):</b><br>".mysql_error());
	while($rowEmpAudi = mysql_fetch_array($resEmpAudi))
		$arrayEmpAuditan[] = $rowEmpAudi['Empcod'];

	// --> Obtener las excepciones de procedimientos, los cuales pasaran directamente a listas para liquidar
	$arrayExcep = array();
	$sqlExcep = "
	SELECT Emacod
	  FROM ".$wbasedato."_000257
	 WHERE Emaest = 'on'
	   AND Ematpr = 'on'
	";
	$resExcep = mysql_query($sqlExcep, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlExcep):</b><br>".mysql_error());
	while($rowExcep = mysql_fetch_array($resExcep))
		$arrayExcep[] = trim($rowExcep['Emacod']);

	// --> Obtener los turnos de cx sin auditar
	$arrTurnosCx		= array();
	$sqlInfoTurnosCx 	= "
	SELECT Turtur, Turhis, Turnin, Turfec, Turhin, Ingcem
	  FROM ".$wbasedatoTcx."_000011 AS A LEFT JOIN ".$wbasedato."_000101 AS B ON (A.Turhis = B.Inghis AND A.Turnin = B.Ingnin)
	 WHERE Turest = 'on'
	   AND Turaud != 'on'
	   AND Turfec <= '".date("Y-m-d")."'
	   AND Turpea = 'on'
	   AND Turhis != ''
	   AND Turnin != ''
	   AND Turhis != '0'
	   AND Turnin != '0' ";

	$resInfoTurnosCx = mysql_query($sqlInfoTurnosCx, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInfoTurnosCx):</b><br>".mysql_error());
	while($rowInfoTurnosCx = mysql_fetch_array($resInfoTurnosCx))
	{
		$arrTurnosCx[$rowInfoTurnosCx['Turtur']]['Historia'] 		= trim($rowInfoTurnosCx['Turhis']);
		$arrTurnosCx[$rowInfoTurnosCx['Turtur']]['Ingreso'] 		= trim($rowInfoTurnosCx['Turnin']);
		$arrTurnosCx[$rowInfoTurnosCx['Turtur']]['fechaTurno'] 		= trim($rowInfoTurnosCx['Turfec']);
		$arrTurnosCx[$rowInfoTurnosCx['Turtur']]['horaTurno'] 		= trim($rowInfoTurnosCx['Turhin']).":00";
		$arrTurnosCx[$rowInfoTurnosCx['Turtur']]['codEntidad'] 		= trim($rowInfoTurnosCx['Ingcem']);
	}

	// --> Consultar la descripcion operatoria de cada turno
	foreach($arrTurnosCx as $turno => &$infoTurno)
	{
		// --> Consultar lo procedimientos que registraron en el turno de cx
		$sqlPorTurCx = "
		SELECT Mcicod
		  FROM ".$wbasedatoTcx."_000008
		  WHERE Mcitur = '".$turno."' ";
		$resPorTurCx = mysql_query($sqlPorTurCx, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlPorTurCx):</b><br>".mysql_error());
		while($rowPorTurCx = mysql_fetch_array($resPorTurCx))
			$infoTurno['codigosProTurnoCx'][$rowPorTurCx['Mcicod']] = '';


		$infoTurno['procedimientosCups'] 	= array();

		// --> 	Validar si para el turno ya existe descripcion operatoria.
		$sqlDesOpe = "
		SELECT Fecha_data, Hora_data
		  FROM ".$wbasedatoHce."_000077
		 WHERE movhis = '".$infoTurno['Historia']."'
		   AND moving = '".$infoTurno['Ingreso']."'
		   AND movpro = '000077'
		   AND movcon = '69'
		   AND movdat = '".$turno."' ";
		$resDesOpe = mysql_query($sqlDesOpe, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlDesOpe):</b><br>".mysql_error());
		while($rowDesOpe = mysql_fetch_array($resDesOpe))
		{
			$infoTurno['tieneDesOpe'] 	= true;

			// --> Obtener el campo 99(Procedimientos realizados cups)
			$sqlProDesOpe = "
			SELECT movdat
			  FROM ".$wbasedatoHce."_000077
			 WHERE Fecha_data 	= '".$rowDesOpe['Fecha_data']."'
			   AND Hora_data 	= '".$rowDesOpe['Hora_data']."'
			   AND movpro 		= '000077'
			   AND movcon 		= '99'
			   AND movhis 		= '".$infoTurno['Historia']."'
			   AND moving 		= '".$infoTurno['Ingreso']."'
			   AND movdat		!= ''
			";
			$resProDesOpe = mysql_query($sqlProDesOpe, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlProDesOpe):</b><br>".mysql_error());
			while($rowProDesOpe = mysql_fetch_array($resProDesOpe))
			{
				// --> Interpretar los codigos de los procedimientos cups
				if(trim($rowProDesOpe['movdat']) != '')
				{
					$str = explode("<option", trim($rowProDesOpe['movdat']));
					foreach($str AS $valor)
					{
						if(trim($valor) == '')
							continue;

						$valor 		= "<option".$valor;
						$valor 		= explode(' ', substr(strip_tags($valor), 4));
						$codigoPro 	= explode('(', trim($valor[0]));
						$codigoPro1	= trim(str_replace('.', '', $codigoPro[0]));

						$codigoPro2	= trim(str_replace('.', '', $codigoPro[1]));
						$codigoPro2	= explode(')', trim($codigoPro2));
						$codigoPro2	= trim($codigoPro2[0]);

						$codigoPro 	= ($codigoPro2 != '') ? $codigoPro2 : $codigoPro1;

						if(trim($codigoPro) != '')
							$infoTurno['procedimientosCups'][] = $codigoPro;
					}
				}
			}
		}
	}

	// --> Recorrer el array de turnos con sus procedimientos, para insertarlos en la tabla de auditoria.
	foreach($arrTurnosCx as $turno => $infoTurno2)
	{
		$autorizada = true;

		// --> Si la empresa del paciente tiene marcado que se debe autorizar/auditar
		if(in_array($infoTurno2['codEntidad'], $arrayEmpAuditan))
		{
			$autorizada = false;
			$tipoAuto	= '';
		}
		else
		{
			$autorizada = true;
			$tipoAuto	= 'NA';
		}

		if(!$autorizada)
		{
			// --> 	Validar si la cx tiene excepcion por procedimiento.
			// 		Esto se activa el 2016-06-28, por orden de juan, para que las cx que tengan procedimientos con excepcion
			//		pasen automaticamente a listas para liquidar.
			foreach($infoTurno2['codigosProTurnoCx'] as $codPro => $val)
			{
				if(in_array(trim($codPro), $arrayExcep))
				{
					$autorizada = true;
					$tipoAuto	= 'EP'; // --> Excepcion por procedimiento
				}
				else
				{
					// --> Con un solo procedimiento que no tenga excepcion, se debe auditar la cx
					$autorizada = false;
					$tipoAuto	= '';
					break;
				}
			}
		}

		// --> Guardar el detalle de los procedimientos a auditar (Cups)
		if(count($infoTurno2['procedimientosCups']) > 0 && $autorizada)
		{
			foreach($infoTurno2['procedimientosCups'] as $idxProCups => $codProCups)
			{
				$sqlDetPro 	= "
				INSERT INTO ".$wbasedato."_000253(Medico,			Fecha_data,				Hora_data,				Audtur,			Audpro,				Audcon,	Audrec,	Audadi,	Audest,	Seguridad)
										  VALUES ('".$wbasedato."', '".date("Y-m-d")."',	'".date("H:i:s")."',	'".$turno."',	'".$codProCups."', 'on', 	'off', 	'off', 	'on', 	'C-".$wbasedato."')
				";
				mysql_query($sqlDetPro, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlDetPro):</b><br>".mysql_error());
			}
		}

		// --> Guardar el encabezado del turno a auditar
		$fechaAut 	= ($autorizada) ? date("Y-m-d")." ".date("H:i:s") : "0000-00-00 00:00:00";

		$sqlEncPro = "
		INSERT INTO ".$wbasedato."_000252(Medico, 			Fecha_data, 			Hora_data, 				Auetur, 		Auehis, 						Aueing, 						Auerau, 			Auelli, 								Auefll, 			Aueecx, 														Aueest, Seguridad)
								  VALUES ('".$wbasedato."', '".date("Y-m-d")."',	'".date("H:i:s")."',	'".$turno."',	'".$infoTurno2['Historia']."',	'".$infoTurno2['Ingreso']."', 	'".$tipoAuto."',	'".(($autorizada) ? 'on' : 'off')."', 	'".$fechaAut."',	'".(($autorizada) ? 'LIQ' : $estadoDeInicioAutorizacionCx)."',	'on', 	'C-".$wbasedato."')
		";
		mysql_query($sqlEncPro, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlEncPro):</b><br>".mysql_error());

		// --> Guardar el log de movimientos
		$sqlLogMov = "
		INSERT INTO ".$wbasedato."_000259 (Medico,				Fecha_data,			Hora_data,				Movtur,			Movest,	Movdes,					Movusu,			Seguridad)
								   VALUES ('".$wbasedato."', 	'".date("Y-m-d")."', '".date("H:i:s")."',	'".$turno."',	'LIQ',	'Se registra la cx',	'Automatico',	'C-".$wbasedato."')
		";
		mysql_query($sqlLogMov, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlLogMov):</b><br>".mysql_error());

		// --> Actualizar el turno de cx como ya revisado por el monitor
		$sqlUpsEst 	= "
		UPDATE ".$wbasedatoTcx."_000011
		   SET Turaud = 'on'
		 WHERE Turtur = '".$turno."' ";
		mysql_query($sqlUpsEst, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUpsEst):</b><br>".mysql_error());
	}
}
function pintarMedicamentosUnixvsMatrix($tipoPaciente='*' , $cco='*')
{


			global $conex;
			global $wemp_pmla;
			global $wbasedato;
			$wbasedatoMovhos	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');


			$arr = array();
			$select_res = "SELECT Ccocod,Cconom AS Nombre
							 FROM ".$wbasedatoMovhos."_000011
							WHERE Ccoest='on'
							ORDER BY Cconom";

			$res = 	mysql_query($select_res,$conex) or die ("Error 1: ".mysql_errno()." - en el query: ".$select_res." - ".mysql_error());
			while($row = mysql_fetch_array($res))
			{
				$arr[utf8_encode(trim($row['Ccocod']))] = trim(utf8_encode($row['Nombre']));
			}

			// $select = " SELECT Count(Mvufec) as cuantos
						  // FROM  ".$wbasedato."_000289
						// ";

			// $res = mysql_query($select,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$select." - ".mysql_error());
			// $i=0;
			// $cuantos = 0 ;
			// if($row = mysql_fetch_array($res))
			// {
				// $cuantos = $row['cuantos'];
			// }


			$select = " SELECT Mvufec,Mvuhis,Mvuing,Mvucod,Mvudoc,Mvulin,Mvufue,Mvucco,Mvufac ,Mvureg,Mvudou,Mvuliu,Mvufuu
						  FROM  ".$wbasedato."_000289 , ".$wbasedato."_000101 ,  ".$wbasedatoMovhos."_000018 , ".$wbasedatoMovhos."_000011
						 WHERE Mvuhis =  Inghis
						   AND Mvuing =  Ingnin
						   AND Mvuhis =  Ubihis
						   AND Mvuing =  Ubiing
							AND Ubisac = Ccocod
					  ";

			if($tipoPaciente != '*' )
					$select.= (($tipoPaciente == 'H') ? "AND Ccohos = 'on' " : "AND Ccohos != 'on' ");

			if($cco != '*' )
				    $select.= " AND Mvucco = '".$cco."' ";


			$select .="ORDER BY Mvufec , Mvuhis";




			$res = mysql_query($select,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$select." - ".mysql_error());
			$numfilas =0;
			$numfilas =$numfilas + mysql_num_rows($res);
			$i=0;

			$html ="
					<br><br><table align='center' id='tablePpalEmpresasPaf'>
						<tr><td colspan='5'></td><td nowrap=nowrap align='right' colspan='2' class='encabezadoTabla'>Numero de Registros : ".$numfilas."</td></tr>
						<tr align='center' class='encabezadoTabla'>
							<th style='display: none' ></th>
							<th>Fecha</th>
							<th>Historia</th>
							<th>Material o Medicamento</th>
							<th>Centro de Costos</th>
							<th>Estado En Matrix</th>
							<th>Estado En Unix</th>
							<th width='15%'>Ver</th>
						</tr>";
			while($row = mysql_fetch_array($res))
			{

				if (($i%2)==0)
					$wcf="fila1";  // color de fondo de la fila
				else
					$wcf="fila2"; // color de fondo de la fila
				//------
				$i++;

				$selectnombre ="SELECT Artcom FROM  ".$wbasedatoMovhos."_000026  WHERE Artcod = '".$row['Mvucod']."' ";
				$resselec = mysql_query($selectnombre,$conex) or die ("Error: ".mysql_errno()." - en el query (mostrar varios ): ".$selectnombre." - ".mysql_error());
				$nombre = '';
				if($rowres = mysql_fetch_array($resselec))
				{
				  $nombre = $rowres['Artcom'];
				}
				if($row['Mvufac']=='N')
				{
					$facturaunix = 'S';
				}
				else
				{
					$facturaunix = 'N';
				}
				$html.="<tr class='".$wcf."'><td>".$row['Mvufec']."</td><td>".$row['Mvuhis']."-".$row['Mvuing']."</td><td>".$row['Mvucod']."-".$nombre."</td><td>".$arr[$row['Mvucco']]."</td><td align='center'>".$row['Mvufac']."</td><td align='center'>".$facturaunix."</td>
				<td align='center' style='cursor:pointer' onclick='verlog(".$row['Mvudou']." , ".$row['Mvuliu']." , ".$row['Mvuhis']." , ".$row['Mvuing']." , \"".$row['Mvufuu']."\", ".$row['Mvureg'].")'><a>log</a></td></tr>";
			}
			$html .="</table>";

			$html .="<div id='explicacionlog'  style='display:none'>";
			$html .="</div>";

			return $html;


}
//-----------------------------------------------------------------------------------------------
//	--> Funcion que pinta los procedimientos sin auditar, para el monitor de auditoria interna
//		Jerson trujillo, 2015-12-15
//-----------------------------------------------------------------------------------------------
function pintarProcedimientosSinAuditar($tipoPaciente='*', $selectEstado='%', $arr_roles_cx, $perfilMonitorAud = '')
{
	global $conex;
	global $wuse;
	global $wemp_pmla;
	global $perfilMonitorAud;
	global $filtroEmpresasRevisar;

	$arrayCx 			= array();
	$respuesta			= array('numRegis', 'html'=>'');
	$arrayCxEstadoPerfil= array();
	$arrayCxOtroPerfil	= array();
	$wbasedato 			= consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
	$wbasedatoTcx 		= consultarAliasPorAplicacion($conex, $wemp_pmla, 'tcx');
	$wbasedatoMovhos 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	$wbasedatoHce 		= consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');

	$wbasedatoHce 					= consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
	$rolQuePermiteAutorizarCx 		= consultarAliasPorAplicacion($conex, $wemp_pmla, 'rolQuePermiteAutorizarCx');
	$rolQuePermiteAutorizarCx		= explode(",", $rolQuePermiteAutorizarCx);

	/*$rolQuePermitePonerCxEnTramite 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'rolQuePermitePonerCxEnTramite'); // Ya se usa desde cliame_000283 Rolbet=on
	$rolQuePermitePonerCxEnTramite	= explode(",", $rolQuePermitePonerCxEnTramite);*/

	$rolQueIngresaNumAutorizacionCx 		= consultarAliasPorAplicacion($conex, $wemp_pmla, 'rolQueIngresaNumAutorizacionCx');
	$rolQueIngresaNumAutorizacionCx			= explode(",", $rolQueIngresaNumAutorizacionCx);

	$tipoempresasoat 						= consultarAliasPorAplicacion($conex, $wemp_pmla, 'tipoempresasoat');

	$rolQueIngresaViasDeLaCx 				= consultarAliasPorAplicacion($conex, $wemp_pmla, 'rolQueIngresaViasDeLaCx');
	$rolQueIngresaViasDeLaCx				= explode(",", $rolQueIngresaViasDeLaCx);

	$rolQueRechazaAutorizaProcedimientosCx 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'rolQueRechazaAutorizaProcedimientosCx');
	$rolQueRechazaAutorizaProcedimientosCx	= explode(",", $rolQueRechazaAutorizaProcedimientosCx);

	$rolQueDaVoBoProcedimientosCx 			= consultarAliasPorAplicacion($conex, $wemp_pmla, 'rolQueDaVoBoProcedimientosCx');
	$rolQueDaVoBoProcedimientosCx			= explode(",", $rolQueDaVoBoProcedimientosCx);

	// <input type='hidden' id='rolQuePermitePonerCxEnTramite' 	value='".json_encode($rolQuePermitePonerCxEnTramite)."'>
	$respuesta['html'].= "
	<input type='hidden' id='rolQuePermiteAutorizarCx' 				value='".json_encode($rolQuePermiteAutorizarCx)."'>
	<input type='hidden' id='rolQueIngresaNumAutorizacionCx' 		value='".json_encode($rolQueIngresaNumAutorizacionCx)."'>
	<input type='hidden' id='rolQueIngresaViasDeLaCx' 				value='".json_encode($rolQueIngresaViasDeLaCx)."'>
	<input type='hidden' id='rolQueRechazaAutorizaProcedimientosCx' value='".json_encode($rolQueRechazaAutorizaProcedimientosCx)."'>
	<input type='hidden' id='rolQueDaVoBoProcedimientosCx' 			value='".json_encode($rolQueDaVoBoProcedimientosCx)."'>
	<input type='hidden' id='tipoempresasoat' 						value='".$tipoempresasoat."'>";

	llenarCirugiasParaAuditar2();
	actualizarHistoriasIngreso();

	// --> Obtener maestro de planes
	$arrayPlan = array();
	$sqlPlan = "
	SELECT Placod, Plades
	  FROM ".$wbasedato."_000153
	 WHERE Plaest = 'on'
	";
	$resPlan = mysql_query($sqlPlan, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlPlan):</b><br>".mysql_error());
	while($rowPlan = mysql_fetch_array($resPlan))
		$arrayPlan[$rowPlan['Placod']] = utf8_encode($rowPlan['Plades']);

	// --> Obtener maestro de causas de tramites
	$arrayCausas = array();
	$sqlCausas = "
	SELECT Caucod, Caunom, Caupli, Caurau
	  FROM ".$wbasedato."_000264
	 WHERE Cauest = 'on'
	";
	$resCausas = mysql_query($sqlCausas, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCausas):</b><br>".mysql_error());
	while($rowCausas = mysql_fetch_array($resCausas))
		// Caupli: Causa permite pasar a liquidar
		// Caurau: Causa requiere autorización para liquidar (necesita Caupli=on)
		$arrayCausas[$rowCausas['Caucod']] = array("nombre"=>utf8_encode($rowCausas['Caunom']),"permite_liquidar"=>$rowCausas['Caupli'],"requiere_autorizar"=>$rowCausas['Caurau']);

	$respuesta['html'].= "
	<input type='hidden' id='arrayCausas' value='".json_encode($arrayCausas)."'>";

	// --> Obtener información de la empresa particular
	$codEnt = consultarAliasPorAplicacion($conex, $wemp_pmla, 'codigoempresaparticular');
	$sqlInfoEmpPar = "
	SELECT Empnom, Tardes
	  FROM ".$wbasedato."_000024 AS A INNER JOIN ".$wbasedato."_000025 AS B ON(Emptar = Tarcod)
	 WHERE Empcod = '".$codEnt."'
	";
	$resInfoEmpPar = mysql_query($sqlInfoEmpPar, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInfoEmpPar):</b><br>".mysql_error());
	if($rowInfoEmpPar = mysql_fetch_array($resInfoEmpPar))
	{
		$nomEmpPar = utf8_encode($rowInfoEmpPar['Empnom']);
		$nomTarPar = utf8_encode($rowInfoEmpPar['Tardes']);
	}

	// --> Obtener las excepciones de procedimientos, los cuales pasaran directamente al rol de facturacion
	// $arrayExcep = array();
	// $sqlExcep = "
	// SELECT Emacod
	  // FROM ".$wbasedato."_000257
	 // WHERE Emaest = 'on'
	   // AND Ematpr = 'on'
	// ";
	// $resExcep = mysql_query($sqlExcep, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlExcep):</b><br>".mysql_error());
	// while($rowExcep = mysql_fetch_array($resExcep))
		// $arrayExcep[] = trim($rowExcep['Emacod']);

	$filtroQueryEmp = "";
	if($filtroEmpresasRevisar != '' && $filtroEmpresasRevisar != '*')
	{
		$arrEmpresasRevisar = explode(",", $filtroEmpresasRevisar);
		foreach($arrEmpresasRevisar as $codEmpRev)
			$filtroQueryEmp.= (($filtroQueryEmp == "") ? "'".trim($codEmpRev)."'" : ", '".trim($codEmpRev)."'");

		$filtroQueryEmp = "AND Ingcem IN(".$filtroQueryEmp.")";
	}

	$filtro_rol = "";
	if(array_key_exists($perfilMonitorAud, $arr_roles_cx) && $arr_roles_cx[$perfilMonitorAud]['filtrar_cx_rol'] == 'on')
	{
		$filtro_rol = "AND Aueecx = '{$perfilMonitorAud}'";
	}

	// --> Consultar cirugias pendientes de auditar
	$sqlCxPendientes = "
	SELECT Auetur, Auehis, Aueing, Aueecx, Auetra, Auesra, CONCAT(Pacno1, ' ', Pacno2, ' ', Pacap1, ' ', Pacap2) AS Nombre, Pacdoc, Pactdo, Ingcem, Ingpla, Ingtpa, Empnom, Emptar, Tardes,
		   Turfec, Turrcu, Turpea
	  FROM ".$wbasedato."_000252 AS A INNER JOIN ".$wbasedato."_000100 AS B ON (Auehis = Pachis), ".$wbasedato."_000101 AS E LEFT JOIN ".$wbasedato."_000024 AS F ON (E.Ingcem = F.Empcod) LEFT JOIN ".$wbasedato."_000025 AS G ON (F.Emptar = G.Tarcod),
		   ".$wbasedatoTcx."_000011, ".$wbasedatoMovhos."_000018, ".$wbasedatoMovhos."_000011
	 WHERE (Aueliq != 'on' OR Auesra != '')
	   AND Auelli != 'on'
	   AND Aueman != 'on'
	   AND Aueecx LIKE '{$selectEstado}'
	   "./*(($perfilMonitorAud != 'FAC') ? " AND Aueecx = '".$perfilMonitorAud."' " : " AND Aueecx LIKE '".$selectEstado."'").*/"
	   {$filtro_rol}
	   AND Aueest = 'on'
	   AND Auehis = Inghis
	   AND Aueing = Ingnin
	   ".(($filtroQueryEmp != "") ? $filtroQueryEmp : "")."
	   AND Auetur = Turtur
	   AND Ubihis = Auehis
	   AND Ubiing = Aueing
	   AND Ubisac = Ccocod ";
	if($tipoPaciente != '*')
		$sqlCxPendientes.= (($tipoPaciente == 'H') ? "AND Ccohos = 'on' " : "AND Ccohos != 'on' ");

	$sqlCxPendientes.= "
	 ORDER BY Turfec
	";
	$resCxPendientes 		= mysql_query($sqlCxPendientes, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCxPendientes):</b><br>".mysql_error());
	$respuesta['numRegis'] 	= 0;

	while($rowCxPendientes = mysql_fetch_array($resCxPendientes))
	{
		$arrayCx[$rowCxPendientes['Auetur']]['historia'] 		= $rowCxPendientes['Auehis'];
		$arrayCx[$rowCxPendientes['Auetur']]['ingreso'] 		= $rowCxPendientes['Aueing'];
		$arrayCx[$rowCxPendientes['Auetur']]['documento'] 		= $rowCxPendientes['Pacdoc'];
		$arrayCx[$rowCxPendientes['Auetur']]['tipoDoc'] 		= $rowCxPendientes['Pactdo'];
		$arrayCx[$rowCxPendientes['Auetur']]['paciente'] 		= utf8_encode($rowCxPendientes['Nombre']);
		$arrayCx[$rowCxPendientes['Auetur']]['entidad'] 		= utf8_encode($rowCxPendientes['Empnom']);
		$arrayCx[$rowCxPendientes['Auetur']]['tarifa'] 			= utf8_encode($rowCxPendientes['Tardes']);
		$arrayCx[$rowCxPendientes['Auetur']]['fechCx'] 			= $rowCxPendientes['Turfec'];
		$arrayCx[$rowCxPendientes['Auetur']]['enAlta'] 			= $rowCxPendientes['Turpea'];
		$arrayCx[$rowCxPendientes['Auetur']]['estado'] 			= $rowCxPendientes['Aueecx'];
		$arrayCx[$rowCxPendientes['Auetur']]['plan'] 			= $rowCxPendientes['Ingpla'];
		$arrayCx[$rowCxPendientes['Auetur']]['enTramite'] 		= $rowCxPendientes['Auetra'];
		$arrayCx[$rowCxPendientes['Auetur']]['segundoRespAut']	= trim($rowCxPendientes['Auesra']);

		// --> Si es un paciente particular
		if($rowCxPendientes['Ingtpa'] == 'P')
		{
			$arrayCx[$rowCxPendientes['Auetur']]['entidad'] 	= $nomEmpPar;
			$arrayCx[$rowCxPendientes['Auetur']]['tarifa'] 		= $nomTarPar;
		}

		$arrayCx[$rowCxPendientes['Auetur']]['colorF'] 	= "red";
		$arrayCx[$rowCxPendientes['Auetur']]['Msj'] 	= $rowCxPendientes['Aueecx'];
		if(array_key_exists($rowCxPendientes['Aueecx'], $arr_roles_cx))
		{
			$arrayCx[$rowCxPendientes['Auetur']]['colorF'] 	= "#".$arr_roles_cx[$rowCxPendientes['Aueecx']]["color"];
			$arrayCx[$rowCxPendientes['Auetur']]['Msj'] 	= 'En '.$arr_roles_cx[$rowCxPendientes['Aueecx']]["nombre"];
		}

		// --> Estos dos arrays es para mostrar primero los registros que tengan el estado asociado al perfil del usuario.
		if($rowCxPendientes['Aueecx'] == $perfilMonitorAud)
			$arrayCxEstadoPerfil[] 	= $rowCxPendientes['Auetur'];
		else
			$arrayCxOtroPerfil[] 	= $rowCxPendientes['Auetur'];
	}

	$arrayCxEstadoPerfil = array_merge($arrayCxEstadoPerfil, $arrayCxOtroPerfil);

	$respuesta['html'].= "
	<input type='hidden' id='wbasedatoFacturacion' value='".$wbasedato."'>
	<table width='100%' id='tablaProSinAudi'>
		<tr class='encabezadoTabla' align='center'>
			<td>Estado</td><td>Turno</td><td>Fecha Cx</td><td>Historia</td><td>Documento</td><td>Paciente</td><td>Entidad</td><td>Tarifa</td><td>Plan</td><td>Pendientes</td><td>Acci&oacute;n</td>
		</tr>
	";
	// --> Pintar registros
	$colorFila = 'fila2';
	//foreach($arrayCx as $turno => $cxInfo)
	foreach($arrayCxEstadoPerfil as $turno)
	{
		$cxInfo			= $arrayCx[$turno];

		$colorFila 		= ($colorFila == 'fila1') ? 'fila2' : 'fila1';
		$colorBack 		= ($colorFila == 'fila1') ? '#C3D9FF' : '#E8EEF7';
		$numMovimientos = numMovimientosCx($turno);
		$numMovimientos = ($numMovimientos == 0) ? "&nbsp;" : $numMovimientos;

		// --> Consultar lo procedimientos que registraron en el turno de cx
		$codigosProTurnoCx 	= array();
		$procTurnoCx 		= '';
		$sqlPorTurCx = "
		SELECT Mcicod, Cirdes
		  FROM ".$wbasedatoTcx."_000008 AS A INNER JOIN ".$wbasedatoTcx."_000002 AS B ON (Mcicod = Circod)
		 WHERE Mcitur = '".$turno."' ";
		$resPorTurCx = mysql_query($sqlPorTurCx, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlPorTurCx):</b><br>".mysql_error());
		while($rowPorTurCx = mysql_fetch_array($resPorTurCx))
		{
			$procTurnoCx = ($procTurnoCx == '') ? $rowPorTurCx['Cirdes'] : ", ".$rowPorTurCx['Cirdes'];

			// --> 2016-06-28: 	Se comentan estas lineas, ya que las cx que tengan procedimientos con excepcion se van a pasar directamente
			//					a listas para liquidar.

			// --> 	Si el procedimiento tiene excepcion, se cambia de rol responsable de la cx
			// if(in_array($rowPorTurCx['Mcicod'], $arrayExcep) && $cxInfo['estado'] != $rolQuePermiteAutorizarCx[0])
			// {
				// --> Consultar si ya tiene un movimiento de 'Procedimiento con excepción'
				// $sqlMovExc = "
				// SELECT Movtur
				  // FROM ".$wbasedato."_000259
				 // WHERE Movtur = '".$turno."'
				   // AND Movdes LIKE 'Procedimiento con excepci%'";
				// $resMovExc = mysql_query($sqlMovExc, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlMovExc):</b><br>".mysql_error());
				// $numMovExc = mysql_num_rows($resMovExc);

				// --> Esto solo se hace una vez, si ya tiene un registro de 'Procedimiento con excepción' no se hace.
				// if($numMovExc == 0)
				// {
					// $sqlCxAudi = "
					// UPDATE ".$wbasedato."_000252
					   // SET Aueecx = '".$rolQuePermiteAutorizarCx[0]."'
					 // WHERE Auetur = '".$turno."'
					// ";
					// mysql_query($sqlCxAudi, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCxAudi):</b><br>".mysql_error());

					// --> Guardar el log de movimiento
					// $sqlLogMov = "
					// INSERT INTO ".$wbasedato."_000259 (Medico,			 Fecha_data,		  	Hora_data,				Movtur,			Movest,								Movdes,												Movusu,					Seguridad)
											   // VALUES ('".$wbasedato."', '".date("Y-m-d")."', 	'".date("H:i:s")."',	'".$turno."',	'".$rolQuePermiteAutorizarCx[0]."',	'".utf8_decode('Procedimiento con excepción').", enviado a ".$rolQuePermiteAutorizarCx[0].".',	'Automatico',	'C-".$wuse."')
					// ";
					// mysql_query($sqlLogMov, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlLogMov):</b><br>".mysql_error());

					// --> No pinto el registro
					// continue 2;
				// }
			// }
		}
		//--------------------------------------------
		// --> INICIO: Consultar acciones pendientes
		//--------------------------------------------
		$arrPendientes = array();
		if($cxInfo['segundoRespAut'] != '')
			$arrPendientes[] = '<span style="color:red" blink>Superaci&oacute;n de topes</span>';

		if($cxInfo['enAlta'] != 'on')
			$arrPendientes[] = 'Sin alta';

		if($cxInfo['enTramite'] != '' && $cxInfo['enTramite'] != 'off')
			$arrPendientes[] = 'En tr&aacute;mite: '.$arrayCausas[$cxInfo['enTramite']]['nombre'].".";

		// --> Consultar si tiene mercado cerrado
		$mercadoCerrado = false;
		$sqlMer = "
		SELECT Mpacrm
		  FROM ".$wbasedato."_000207
		 WHERE Mpatur = '".$turno."'
		   AND Mpaest = 'on' ";
		$resMer = mysql_query($sqlMer, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlMer):</b><br>".mysql_error());
		while($rowMer = mysql_fetch_array($resMer))
		{
			$mercadoCerrado = true;
			if($rowMer['Mpacrm'] != 'on')
			{
				$mercadoCerrado = false;
				break;
			}
		}

		if(mysql_num_rows($resMer) == 0)
			$arrPendientes[] 	= 'Sin mercado';
		elseif(!$mercadoCerrado)
			$arrPendientes[] 	= 'Mercado sin cerrar';

		// --> 	Validar si para el turno ya existe descripcion operatoria.
		$sqlDesOpe = "
		SELECT Fecha_data, Hora_data
		  FROM ".$wbasedatoHce."_000077
		 WHERE movhis = '".$cxInfo['historia']."'
		   AND moving = '".$cxInfo['ingreso']."'
		   AND movpro = '000077'
		   AND movcon = '69'
		   AND movdat = '".$turno."' ";
		$resDesOpe = mysql_query($sqlDesOpe, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlDesOpe):</b><br>".mysql_error());

		if(mysql_num_rows($resDesOpe) == 0)
		{
			// --> Consultar los medicos que registraron en el turno de cx
			$medicos = '';
			$sqlMedTurCx = "
			SELECT Meddoc, Medno1, Medno2, Medap1, Medap2
			  FROM ".$wbasedatoTcx."_000010 AS A LEFT JOIN ".$wbasedatoMovhos."_000048 AS B ON (Mmemed = Meddoc)
			  WHERE Mmetur = '".$turno."'
			  GROUP BY Meddoc ";
			$resMedTurCx = mysql_query($sqlMedTurCx, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlMedTurCx):</b><br>".mysql_error());
			while($rowMedTurCx = mysql_fetch_array($resMedTurCx))
			{
				$nombreMed = $rowMedTurCx['Medno1']." ".$rowMedTurCx['Medno2']." ".$rowMedTurCx['Medap1']." ".$rowMedTurCx['Medap2'];
				$nombreMed = ucfirst(strtolower($nombreMed));
				if(trim($rowMedTurCx['Meddoc']) != '')
					$medicos = ($medicos == '') ? $nombreMed : ' ,'.$nombreMed;
			}

			// --> Validar si el paciente tiene nota operatoria.
			$sqlNotaOpera = "
			SELECT id
			  FROM ".$wbasedatoHce."_000036
			 WHERE Firhis 		= '".$cxInfo['historia']."'
			   AND Firing 		= '".$cxInfo['ingreso']."'
			   AND Firpro 		= '000316'
			   AND Firfir 		= 'on'
			   AND Fecha_data  >= '".$cxInfo['fechCx']."'
			";
			$resNotaOpera 	= mysql_query($sqlNotaOpera, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlNotaOpera):</b><br>".mysql_error());
			if($rowNotaOpera = mysql_fetch_array($resNotaOpera))
			{
				$arrPendientes[] = 'Con nota operatoria';
				$respuesta['html'].= " <input type='hidden' id='notaOperatoria-".$turno."' value='si'>";

				if($procTurnoCx != '')
					$arrPendientes[] = "(<span style='font-size:7pt;font-weight:bold'>".$procTurnoCx."</span>)";
			}
			else
			{
				$arrPendientes[] = 'Sin descripci&oacute;n operatoria';
				if($medicos != '')
					$arrPendientes[] = "(<span style='font-size:7pt;font-weight:bold'>".$medicos."</span>)";

				$arrPendientes[] = 'Sin nota operatoria';
			}
		}
		else
		{
			$infoTurnoPro = array();

			while($rowDesOpe = mysql_fetch_array($resDesOpe))
			{
				// --> Obtener el campo 99(Procedimientos realizados cups)
				$sqlProDesOpe = "
				SELECT movdat
				  FROM ".$wbasedatoHce."_000077
				 WHERE Fecha_data 	= '".$rowDesOpe['Fecha_data']."'
				   AND Hora_data 	= '".$rowDesOpe['Hora_data']."'
				   AND movpro 		= '000077'
				   AND movcon 		= '99'
				   AND movhis 		= '".$cxInfo['historia']."'
				   AND moving 		= '".$cxInfo['ingreso']."'
				   AND movdat		!= ''
				";
				$resProDesOpe = mysql_query($sqlProDesOpe, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlProDesOpe):</b><br>".mysql_error());
				while($rowProDesOpe = mysql_fetch_array($resProDesOpe))
				{
					// --> Interpretar los codigos de los procedimientos cups
					if(trim($rowProDesOpe['movdat']) != '')
					{
						$str = explode("<option", trim($rowProDesOpe['movdat']));
						foreach($str AS $valor)
						{
							if(trim($valor) == '')
								continue;

							$valor 		= "<option".$valor;
							$valor 		= explode(' ', substr(strip_tags($valor), 4));
							$codigoPro 	= explode('(', trim($valor[0]));

							$codigoPro1	= trim(str_replace('.', '', $codigoPro[0]));

							$codigoPro2	= (array_key_exists(1, $codigoPro)) ? trim(str_replace('.', '', $codigoPro[1])) : '';
							$codigoPro2	= explode(')', trim($codigoPro2));
							$codigoPro2	= trim($codigoPro2[0]);

							$codigoPro 	= ($codigoPro2 != '') ? $codigoPro2 : $codigoPro1;

							$infoTurnoPro[$codigoPro] = '';
						}
					}
				}
			}

			if(count($infoTurnoPro) == 0)
				$arrPendientes[] = 'Sin procedimientos';
		}
		//--------------------------------------------
		// --> FIN: Consultar acciones pendientes
		//--------------------------------------------

		// --> A los auditores solo mostrarles cx que puedan revisar
		if($perfilMonitorAud == 'AUD' && $cxInfo['estado'] == 'AUD' && ($cxInfo['enAlta'] != 'on' || !$mercadoCerrado))
			continue;
		// --> A los auditores IDC solo mostrarles cx que puedan revisar
		if($perfilMonitorAud == 'IDC' && $cxInfo['estado'] == 'IDC' && ($cxInfo['enAlta'] != 'on' || !$mercadoCerrado))
			continue;

		$plan_desc = (array_key_exists($cxInfo['plan'], $arrayPlan)) ? $arrayPlan[$cxInfo['plan']] : "";
		$respuesta['html'].= "
		<tr class='".$colorFila." find' onmouseover='$(this).css({\"background-color\": \"#FCFFE8\"})' onmouseout='$(this).css({\"background-color\": \"".$colorBack."\"})'>
			<td align='center' ><button title='<span style=\"font-weight:normal;\">".$cxInfo['Msj']."</span>' tooltip='si' style='cursor:help;border:1px solid #999999;border-radius: 20px;background-color:".$cxInfo['colorF'].";'>".$numMovimientos."</button></td>
			<td align='center'>".$turno."</td>
			<td align='center'>".$cxInfo['fechCx']."</td>
			<td>".$cxInfo['historia']."-".$cxInfo['ingreso']."</td>
			<td>".$cxInfo['tipoDoc']."-".$cxInfo['documento']."</td>
			<td>".$cxInfo['paciente']."</td>
			<td>".$cxInfo['entidad']."</td>
			<td>".$cxInfo['tarifa']."</td>
			<td>".$plan_desc."</td>
			<td>".utf8_encode(implode('<br>', $arrPendientes))."</td>
			<td align='center'>";
			if($cxInfo['estado'] == $perfilMonitorAud && $cxInfo['enAlta'] == 'on' && $mercadoCerrado)
				$respuesta['html'].= "<button style='cursor:pointer;font-family: verdana;font-weight:bold;font-size: 8pt;' onclick='autorizarAuditarCx(\"".$turno."\", \"off\", \"".(($cxInfo['segundoRespAut'] != '') ? 'on' : 'off')."\")'>Revisar</button>";
			else
				$respuesta['html'].= "
				<img width='15' height='15' style='cursor:pointer;' src='../../images/medical/sgc/lupa.png' onclick='autorizarAuditarCx(\"".$turno."\", \"on\", \"off\")'>";
			$respuesta['html'].= "
			</td>
		</tr>
		";

		$respuesta['numRegis']++;
	}

	if(count($arrayCx) == 0)
		$respuesta['html'].= "<tr><td colspan='11' align='center' class='fila2'>No se encontraron registros</td></tr>";

	$respuesta['html'].= "</table>";

	return $respuesta;
}
//-----------------------------------------------------------------------------------------------
//	--> Consultar log de movimientos, jerson trujillo 2016-03-29
//-----------------------------------------------------------------------------------------------
function logMovimientosCx($turno)
{
	global $conex;
	global $wemp_pmla;
	global $wbasedato;

	$log	= "
	<table width=100%>
		<tr align=center class=fila1 style=font-size:8pt;font-weight:bold><td>Fecha-Hora</td><td>Usuario</td><td>Movimiento</td></tr>";

	$colorFila1 = "fila2";

	$sqlLog = "
	SELECT Fecha_data, Hora_data, Movdes, Movusu, Descripcion, id
	  FROM ".$wbasedato."_000259 AS A LEFT JOIN usuarios AS B ON(A.Movusu = B.Codigo)
	 WHERE Movtur = '".$turno."'
	";
	$resLog = mysql_query($sqlLog, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlLog):</b><br>".mysql_error());
	while($rowLog = mysql_fetch_array($resLog))
	{
		//$colorFila1 = (($colorFila1 == 'fila2') ? 'fila1' : 'fila2');
		$colorFila1 = 'fila2';

		$log.= "
		<tr class=".$colorFila1." style=font-size:8pt>
			<td align=center>".$rowLog['Fecha_data']." ".$rowLog['Hora_data']."</td><td>".(($rowLog['Descripcion'] == '') ? $rowLog['Movusu'] : utf8_encode(ucfirst(strtolower($rowLog['Descripcion']))))."</td><td>".utf8_encode($rowLog['Movdes'])."</td>
		</tr>
		";
	}
	$log.="</table>";

	return $log;
}
//-----------------------------------------------------------------------------------------------
//	--> Consultar numero de movimientos, jerson trujillo 2016-03-29
//-----------------------------------------------------------------------------------------------
function numMovimientosCx($turno)
{
	global $conex;
	global $wemp_pmla;
	global $wbasedato;

	$sqlLog = "
	SELECT count(*) AS cant
	  FROM ".$wbasedato."_000259
	 WHERE Movtur = '".$turno."'
	   AND Movusu != 'automatico'
	";
	$resLog = mysql_query($sqlLog, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlLog):</b><br>".mysql_error());
	$cant = mysql_fetch_array($resLog);
	return $cant['cant'];
}
//-----------------------------------------------------------------------------------------------
//	--> Consultar el nombre de un usuario, jerson trujillo 2016-04-27
//-----------------------------------------------------------------------------------------------
function nombreDeUsuario(&$hisUsuarios, $codUsuario)
{
	global $conex;
	global $wemp_pmla;
	global $wbasedato;

	if(array_key_exists($codUsuario, $hisUsuarios))
		return $hisUsuarios[$codUsuario];

	$sqlUsu = "
	SELECT Descripcion
	  FROM usuarios
	 WHERE Codigo = '".$codUsuario."' ";

	$resUsu = mysql_query($sqlUsu, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUsu):</b><br>".mysql_error());
	if($rowUsu = mysql_fetch_array($resUsu))
	{
		$hisUsuarios[$codUsuario] = $rowUsu['Descripcion'];
		return $hisUsuarios[$codUsuario];
	}
	else
		return "";
}
//-----------------------------------------------------------------------------------------------
//	--> Funcion que pinta los procedimientos ya auditados
//		Jerson trujillo, 2015-12-22
//-----------------------------------------------------------------------------------------------
function pintarProcedimientosAuditados($fechaAutorizacion, $historia='%', $verTodas='off', $arr_roles_cx)
{
	global $conex;
	global $wemp_pmla;
	global $perfilMonitorAud;
	global $filtroEmpresasRevisar;
	global $wuse;

	$liquidada = (!isset($liquidada)) ? '': $liquidada;
	$arrayCx 			= array();
	$respuesta 			= array('html'=>'', 'numRegis');
	$wbasedato 			= consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
	$wbasedatoTcx 		= consultarAliasPorAplicacion($conex, $wemp_pmla, 'tcx');
	$fechaAutorizacion 	= ((trim($fechaAutorizacion) == '') ? date("Y-m-d") : $fechaAutorizacion);

	// if($verTodas == 'on')
		// if($perfilMonitorAud == 'on')
			// $filtroSql = " (Aueuad != '' OR Auerau != '') ";
		// else
			// $filtroSql = " Aueaud = 'on' ";
	// else
		// if($perfilMonitorAud == 'on')
			// $filtroSql = " Aueuad = '".$wuse."' ";
		// else
			// $filtroSql = " Aueaud = 'on' AND Aueuau = '".$wuse."' ";

	$filtroQueryEmp = "";
	if($filtroEmpresasRevisar != '' && $filtroEmpresasRevisar != '*')
	{
		$arrEmpresasRevisar = explode(",", $filtroEmpresasRevisar);
		foreach($arrEmpresasRevisar as $codEmpRev)
			$filtroQueryEmp.= (($filtroQueryEmp == "") ? "'".trim($codEmpRev)."'" : ", '".trim($codEmpRev)."'");

		$filtroQueryEmp = "AND Ingcem IN(".$filtroQueryEmp.")";
	}

	// --> Consultar cirugias auditadas
	$sqlCxPendientes = "
	SELECT Auetur, Auehis, Aueing, A.Auerau, A.Auefll, CONCAT(Pacno1, ' ', Pacno2, ' ', Pacap1, ' ', Pacap2) AS Nombre, Ingtpa, Empnom, Tardes, Turfec
	  FROM ".$wbasedato."_000252 AS A INNER JOIN ".$wbasedato."_000101 AS B ON(A.Auehis = B.Inghis AND A.Aueing = B.Ingnin ".$filtroQueryEmp.") LEFT JOIN ".$wbasedato."_000024 AS F ON (B.Ingcem = F.Empcod) LEFT JOIN ".$wbasedato."_000025 AS G ON (F.Emptar = G.Tarcod),
		   ".$wbasedato."_000100 AS D, ".$wbasedatoTcx."_000011
	 WHERE Auelli = 'on'
	   AND Aueecx = 'LIQ'
	   AND Aueest = 'on'
	   AND Auehis LIKE '".$historia."'
	    ".(($historia == '%') ? "AND Auefll LIKE '".$fechaAutorizacion."%'" : "")."
	   AND Pachis = Auehis
	   AND Auetur = Turtur
	 ORDER BY Turfec DESC
	";
	$resCxPendientes 		= mysql_query($sqlCxPendientes, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCxPendientes):</b><br>".mysql_error());
	$respuesta['numRegis']	= mysql_num_rows($resCxPendientes);

	$filtrar_rol = false;
	if(array_key_exists($perfilMonitorAud, $arr_roles_cx) && $arr_roles_cx[$perfilMonitorAud]['filtrar_busqueda_rol'] == 'on')
	{
		$filtrar_rol = true;
	}

	while($rowCxPendientes = mysql_fetch_array($resCxPendientes))
	{
		$cor_turno = $rowCxPendientes['Auetur'];
		$permitir_ver_resultado = true;
		// Si se quiere filtrar por el rol que consulta y ocultar todos los demás turnos en los que el rol actual no ha participado
		if($filtrar_rol)
		{
			// Si la consulta arroja por lo menos un resultado, es porque el rol ha participado, por tanto se le permitirá ver ese turno, en caso contrario
			// no se permiten ver los resultados de ese turno.
			$sqlCxInt = "	SELECT 	Movtur
							FROM 	{$wbasedato}_000259
							WHERE 	Movtur = '{$cor_turno}'
									AND Movest = '{$perfilMonitorAud}'
							GROUP BY Movtur";
			$resCxInt 		= mysql_query($sqlCxInt, $conex) or die("<b>ERROR EN QUERY MATRIX({$sqlCxInt}):</b><br>".mysql_error());
			if(mysql_num_rows($resCxInt) == 0 )
			{
				$permitir_ver_resultado = false;
			}
		}

		if($permitir_ver_resultado)
		{
			$arrayCx[$cor_turno]['historia'] 		= $rowCxPendientes['Auehis'];
			$arrayCx[$cor_turno]['ingreso'] 		= $rowCxPendientes['Aueing'];
			$arrayCx[$cor_turno]['paciente'] 		= utf8_encode($rowCxPendientes['Nombre']);
			$arrayCx[$cor_turno]['entidad'] 		= (($rowCxPendientes['Ingtpa'] == 'P') ? 'Particular' : utf8_encode($rowCxPendientes['Empnom']));
			$arrayCx[$cor_turno]['tarifa'] 			= (($rowCxPendientes['Ingtpa'] == 'P') ? 'Particular' : utf8_encode($rowCxPendientes['Tardes']));
			$arrayCx[$cor_turno]['tipoAuto'] 		= trim($rowCxPendientes['Auerau']);
			$arrayCx[$cor_turno]['fechaLisLiq'] 	= trim($rowCxPendientes['Auefll']);
			$arrayCx[$cor_turno]['fechCx'] 			= $rowCxPendientes['Turfec'];
		}
	}

	$respuesta['html'].= "
	<input type='hidden' id='wbasedatoFacturacion' value='".$wbasedato."'>
	<table width='100%' id='tablaProAudi'>
		<tr class='encabezadoTabla' align='center'>
			<td>Turno</td><td>Fecha Cx</td><td>Historia</td><td>Paciente</td><td>Entidad</td><td>Tarifa</td><td>Log</td><td>Lista para liquidar</td><td>Liquidada</td><td>Cx</td>
		</tr>
	";

	// --> Pintar registros
	$colorFila = 'fila2';
	foreach($arrayCx as $turno => $cxInfo)
	{
		$colorFila = ($colorFila == 'fila1') ? 'fila2' : 'fila1';
		$colorBack = ($colorFila == 'fila1') ? '#C3D9FF' : '#E8EEF7';

		switch($cxInfo['tipoAuto'])
		{
			case 'CI':
			{
				$tipoAuto = 'Códigos Iguales';
				break;
			}
			case 'NA':
			{
				$tipoAuto = 'La entidad autoriza automáticamente';
				break;
			}
			case 'PA':
			{
				$tipoAuto = 'Procedimiento no se autoriza/audita';
				break;
			}
			case 'EP':
			{
				$tipoAuto = 'Excepcion por procedimiento';
				break;
			}
			case 'EM':
			{
				$tipoAuto = 'Excepcion por medico';
				break;
			}
		}

		$log = logMovimientosCx($turno);

		$respuesta['html'].= "
		<tr class='".$colorFila." find' onmouseover='$(this).css({\"background-color\": \"#FCFFE8\"})' onmouseout='$(this).css({\"background-color\": \"".$colorBack."\"})'>
			<td align='center'>".$turno."</td>
			<td align='center'>".$cxInfo['fechCx']."</td>
			<td>".$cxInfo['historia']."-".$cxInfo['ingreso']."</td>
			<td>".utf8_encode($cxInfo['paciente'])."</td>
			<td>".utf8_encode($cxInfo['entidad'])."</td>
			<td>".utf8_encode($cxInfo['tarifa'])."</td>
			<td align='center' tooltip='si' title='".$log."' style='cursor:help'><img width='16' height='16' src='../../images/medical/sgc/lupa.png'></td>
			<td align='center'>".$cxInfo['fechaLisLiq']."</td>
			<td align='center'>".$liquidada."</td>
			<td align='center'>
				<button style='cursor:pointer;font-family: verdana;font-weight:bold;font-size: 8pt;' onclick='autorizarAuditarCx(\"".$turno."\", \"on\", \"off\")'>Ver</button>
			</td>
		</tr>
		";
	}
	if(count($arrayCx) == 0)
		$respuesta['html'].= "<tr><td colspan='12' class='".$colorFila."' align='center'>No se encontraron registros</td></tr>";

	$respuesta['html'].= "</table>";

	return $respuesta;
}

/**
 * [pintarTurnosLiquidadosTramite description]
 * @param  [type] $conex         [description]
 * @param  [type] $wemp_pmla     [description]
 * @param  [type] $wbasedato     [description]
 * @param  [type] $arr_roles_cx  [description]
 * @param  [type] $tipo_consulta [description]
 * @return [type]                [description]
 */
function pintarTurnosLiquidadosTramite($conex, $wemp_pmla, $wbasedato, $arr_roles_cx, $tipo_consulta, $sufijo, $perfilMonitorAud)
{
	// $liquidada = (!isset($liquidada)) ? '': $liquidada;
	$arrayCx      = array();
	$respuesta    = array('html'=>'', 'numRegis');
	$wbasedato    = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
	$wbasedatoTcx = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tcx');
	$wbasedatomovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	$liquidada    = 'Si';
	$ver_revisarStd = "estado-revisado";

	$filtros_tramite = "AND c252.Auetra <> '10' AND c252.Auetra <> '' AND c252.Auetra <> 'off'";
	if($tipo_consulta == 'pendiente_revisar'){
		$filtros_tramite = "AND c252.Auetra = '10' AND c252.Auelre <> 'on'";
		$ver_revisarStd = "";
	}

	// --> Consultar cirugias auditadas
	// Aueliq -> Turno liquidado
	// Auelre -> Liquidado revisado
	// Auetra -> Trámite
	$sqlCxLiqTram = "
			SELECT 	c252.Auetur, c252.Auehis, c252.Aueing, c252.Auerau, c252.Auefll, c252.Auetra, c264.Caunom AS nom_tramite,
					CONCAT(c100.Pacno1, ' ', c100.Pacno2, ' ', c100.Pacap1, ' ', c100.Pacap2) AS Nombre, c101.Ingtpa, c24.Empnom, c25.Tardes, tcx11.Turfec,
					mv20.Habhis AS his_hospitalizado, mv67.Habhis AS his_estuvo_hospitalizado
			FROM 	{$wbasedato}_000252 AS c252
					INNER JOIN
					{$wbasedato}_000101 AS c101 ON(c252.Auehis = c101.Inghis AND c252.Aueing = c101.Ingnin)
					INNER JOIN
					{$wbasedato}_000100 AS c100 ON (c100.Pachis = c252.Auehis)
					INNER JOIN
					{$wbasedatoTcx}_000011 AS tcx11 ON (c252.Auetur = tcx11.Turtur)
					LEFT JOIN
					{$wbasedato}_000024 AS c24 ON (c101.Ingcem = c24.Empcod)
					LEFT JOIN
					{$wbasedato}_000025 AS c25 ON (c24.Emptar = c25.Tarcod)
					LEFT JOIN
					{$wbasedato}_000264 AS c264 ON (c264.Caucod = c252.Auetra)
					LEFT JOIN
					{$wbasedatomovhos}_000020 AS mv20 ON (mv20.Habhis = c101.Inghis AND mv20.Habing = c101.Ingnin)
					LEFT JOIN
					{$wbasedatomovhos}_000067 AS mv67 ON (mv67.Habhis = c101.Inghis AND mv67.Habing = c101.Ingnin)
			WHERE 	c252.Auelli = 'on'
					AND c252.Aueecx = 'LIQ'
					AND c252.Aueliq = 'on'
					{$filtros_tramite}
					AND c252.Aueest = 'on'
			GROUP BY c252.Auetur
			ORDER BY tcx11.Turfec DESC";
	$resCxLiqtram 		= mysql_query($sqlCxLiqTram, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCxLiqTram):</b><br>".mysql_error());
	$respuesta['numRegis']	= mysql_num_rows($resCxLiqtram);

	$filtrar_rol = false;
	if(array_key_exists($perfilMonitorAud, $arr_roles_cx) && $arr_roles_cx[$perfilMonitorAud]['filtrar_busqueda_rol'] == 'on')
	{
		$filtrar_rol = true;
	}

	while($rowCxPendientes = mysql_fetch_assoc($resCxLiqtram))
	{
		$cor_turno = $rowCxPendientes['Auetur'];
		$permitir_ver_resultado = true;
		// // Si se quiere filtrar por el rol que consulta y ocultar todos los demás turnos en los que el rol actual no ha participado
		// if($filtrar_rol)
		// {
		// 	// Si la consulta arroja por lo menos un resultado, es porque el rol ha participado, por tanto se le permitirá ver ese turno, en caso contrario
		// 	// no se permiten ver los resultados de ese turno.
		// 	$sqlCxInt = "	SELECT 	Movtur
		// 					FROM 	{$wbasedato}_000259
		// 					WHERE 	Movtur = '{$cor_turno}'
		// 							AND Movest = '{$perfilMonitorAud}'
		// 					GROUP BY Movtur";
		// 	$resCxInt 		= mysql_query($sqlCxInt, $conex) or die("<b>ERROR EN QUERY MATRIX({$sqlCxInt}):</b><br>".mysql_error());
		// 	if(mysql_num_rows($resCxInt) == 0 )
		// 	{
		// 		$permitir_ver_resultado = false;
		// 	}
		// }

		if($permitir_ver_resultado)
		{
			$arrayCx[$cor_turno]['historia'] 		= $rowCxPendientes['Auehis'];
			$arrayCx[$cor_turno]['ingreso'] 		= $rowCxPendientes['Aueing'];
			$arrayCx[$cor_turno]['paciente'] 		= $rowCxPendientes['Nombre'];
			$arrayCx[$cor_turno]['entidad'] 		= (($rowCxPendientes['Ingtpa'] == 'P') ? 'Particular' : $rowCxPendientes['Empnom']);
			$arrayCx[$cor_turno]['tarifa'] 			= (($rowCxPendientes['Ingtpa'] == 'P') ? 'Particular' : $rowCxPendientes['Tardes']);
			$arrayCx[$cor_turno]['tipoAuto'] 		= trim($rowCxPendientes['Auerau']);
			$arrayCx[$cor_turno]['fechaLisLiq'] 	= trim($rowCxPendientes['Auefll']);
			$arrayCx[$cor_turno]['fechCx'] 			= $rowCxPendientes['Turfec'];
			$arrayCx[$cor_turno]['cod_tramite'] 	= $rowCxPendientes['Auetra'];
			$arrayCx[$cor_turno]['nom_tramite'] 	= $rowCxPendientes['nom_tramite'];

			$pac_hosp = "Ambulatorio";
			// Si la historia está en un registro de la 000020 está hospitalizado o si la historia está en un registro
			// de historial de 000067 indica que estuvo hospitalizado
			if($rowCxPendientes['his_hospitalizado'] != '' || $rowCxPendientes['his_estuvo_hospitalizado'] != ''){
				$pac_hosp = "Hospitalizado";
			}
			$arrayCx[$cor_turno]['pac_hosp'] 	= $pac_hosp;
		}
	}

	$respuesta['html'].= "
	<input type='hidden' id='wbasedatoFacturacion".$sufijo."' value='".$wbasedato."'>
	<table width='100%' id='tabla".$sufijo."'>
		<tr class='encabezadoTabla' align='center'>
			<td>Turno</td>
			<td>Fecha Cx</td>
			<td>Historia</td>
			<td>Paciente</td>
			<td>Tipo</td>
			<td>Entidad</td>
			<td>Tarifa</td>
			<td>Log</td>
			<td>Lista para liquidar</td>
			<td>Liquidada</td>
			<td>Trámite</td>
			<td class='".$ver_revisarStd."'>Revisada</td>
			<td>Cx</td>
		</tr>
	";

	// --> Pintar registros
	$colorFila = 'fila2';
	foreach($arrayCx as $turno => $cxInfo)
	{
		$colorFila = ($colorFila == 'fila1') ? 'fila2' : 'fila1';
		$colorBack = ($colorFila == 'fila1') ? '#C3D9FF' : '#E8EEF7';

		switch($cxInfo['tipoAuto'])
		{
			case 'CI':
			{
				$tipoAuto = 'Códigos Iguales';
				break;
			}
			case 'NA':
			{
				$tipoAuto = 'La entidad autoriza automáticamente';
				break;
			}
			case 'PA':
			{
				$tipoAuto = 'Procedimiento no se autoriza/audita';
				break;
			}
			case 'EP':
			{
				$tipoAuto = 'Excepcion por procedimiento';
				break;
			}
			case 'EM':
			{
				$tipoAuto = 'Excepcion por medico';
				break;
			}
		}

		$log = logMovimientosCx($turno);

		$marcar_cx = '';
		if(array_key_exists($perfilMonitorAud, $arr_roles_cx) && $arr_roles_cx[$perfilMonitorAud]['marcar_cx_revisada'] == 'on')
		{
			$marcar_cx = '<input type="checkbox" value="" id="input_rev_'.$turno.'" onclick="marcarTurnoRevisado(\''.$turno.'\', this)">';
		}

		$respuesta['html'].= '
		<tr class="'.$colorFila.' find" onmouseover="$(this).css({\'background-color\': \'#FCFFE8\'})" onmouseout="$(this).css({\'background-color\': \''.$colorBack.'\'})">
			<td align="center">'.$turno.'</td>
			<td align="center">'.$cxInfo['fechCx'].'</td>
			<td>'.$cxInfo['historia'].'-'.$cxInfo['ingreso'].'</td>
			<td>'.utf8_encode($cxInfo['paciente']).'</td>
			<td>'.utf8_encode($cxInfo['pac_hosp']).'</td>
			<td>'.utf8_encode($cxInfo['entidad']).'</td>
			<td>'.utf8_encode($cxInfo['tarifa']).'</td>
			<td align="center" tooltip="si" title="'.$log.'" style="cursor:help"><img width="16" height="16" src="../../images/medical/sgc/lupa.png"></td>
			<td align="center">'.$cxInfo['fechaLisLiq'].'</td>
			<td align="center">'.$liquidada.'</td>
			<td align="center">'.ucfirst(strtolower((utf8_encode($cxInfo['nom_tramite'])))).'</td>
			<td align="center" class="'.$ver_revisarStd.'">
				'.$marcar_cx.'
			</td>
			<td align="center">
				<button style="cursor:pointer;font-family: verdana;font-weight:bold;font-size: 8pt;" onclick="autorizarAuditarCx(\''.$turno.'\', \'on\', \'off\')">Ver</button>
			</td>
		</tr> ';
	}
	if(count($arrayCx) == 0)
		$respuesta['html'].= "<tr><td colspan='".(($ver_revisarStd == '') ? '12': '11')."' class='".$colorFila."' align='center'>No se encontraron registros</td></tr>";

	$respuesta['html'].= "</table>";

	return $respuesta;
}

function cambiarResponsableEstancia($whistoria,$wingreso,$wresponsable,$wresseleccionado,$wresanterior,$wbasedato,$wdatofecha,$wtextareapaf)
{
	global $conex;
	global $wemp_pmla;
	global $wuse;
	$select = "SELECT  Ingcem
						 FROM ".$wbasedato."_000101
						WHERE Inghis  = '".$whistoria."'
						  AND Ingnin  = '".$wingreso."'";

	$res = mysql_query($select,$conex) or die ("Query : ".$select." - ".mysql_error());

	$par101 = 'no'; // se inicializa la variable en no , responsable que esta en la 101

	while ($row = mysql_fetch_array($res))
	{
		if($wresseleccionado == $row['Ingcem'])
		{
			$par101 = 'si';  //--- si el paciente seleccionado es el que esta en la 101 par101 = si
		}
		$responsableactualcodigo = $row['Ingcem'];
		//$responsableactual = $row['Ingcem'];
	}



	$insert ="INSERT  INTO ".$wbasedato."_000265 (	Fecha_data			, 		Hora_data			,	 Medico			, 		Esthis			,		Esting		,  		Estres					, Estran				, Estest  	, 		Seguridad	,Estfec	)
										VALUES 	 ('".date("Y-m-d")."'	, 	'".date("H:i:s")."'		, '".$wbasedato."'  ,	'".$whistoria."'	,  '".$wingreso."'	,	 '".$wresseleccionado."'	, '".$wresanterior."'	,  'on'		, 	'C-".$wuse."'	,'".$wdatofecha."') ";

	mysql_query($insert,$conex) ;


	//-----------
	// se graba en la bitacora
	Grabarpafbitacora($whistoria,$wingreso,$wresponsable,$wresseleccionado , $wtextareapaf ,$wdatofecha);


	//-- Se traen los responsables de la tabla 205

	$select = "SELECT Resnit
				 FROM ".$wbasedato."_000205
				WHERE Reshis='".$whistoria."'
				  AND Resing='".$wingreso."'
				  AND Resest='on'";

	$tieneResponsable = false;
	$res = mysql_query($select,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$select." - ".mysql_error());
	$cuantosresponsable=0;
	while($row = mysql_fetch_array($res))
	{
	   $cuantosresponsable++;
	   if($wresseleccionado == $row['Resnit'])
	   {
		  $tieneResponsable = true;
	   }

	}

	//$enviarcorreo='no';
	//$cambioResponsableAutomatico = "on";
	$cambioResponsableAutomatico	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'PafCambioResponsable');


	//--- Solo se envia el correo, diciendo que el paciente tiene que ser cambiado de responsable
	if($cambioResponsableAutomatico=="off")
	{
		//-- Traigo el nombre del paciente
		$select = "SELECT  CONCAT (Pacno1 ,' ',Pacno2,' ',Pacap1,' ',Pacap2) as Nombre
							 FROM ".$wbasedato."_000100
							WHERE Pachis  = '".$whistoria."' ";

		$res = mysql_query($select,$conex) or die ("Query : ".$select." - ".mysql_error());

		if ($row = mysql_fetch_array($res))
		{
			$nombre = $row['Nombre'];
		}
		//----------------------------------------

		//---Traigo nombre responsable actual
		$select_res = "SELECT Empcod,Empnom AS Nombre, Emptar
							 FROM ".$wbasedato."_000024
							WHERE Empcod = '".$responsableactualcodigo."'";

		$res = 	mysql_query($select_res,$conex) or die ("Error 1: ".mysql_errno()." - en el query: ".$select_res." - ".mysql_error());
		while($row = mysql_fetch_array($res))
		{
			$nombreresactual = $row['Nombre'];
		}
		//---------------------------------------

		//---Traigo nombre responsable al que debe ser cambiado
		$select_res = "SELECT Empcod,Empnom AS Nombre, Emptar , Empema
							 FROM ".$wbasedato."_000024
							WHERE Empcod = '".$wresseleccionado."'";

		$res = 	mysql_query($select_res,$conex) or die ("Error 1: ".mysql_errno()." - en el query: ".$select_res." - ".mysql_error());
		$emailseleccionado='';
		while($row = mysql_fetch_array($res))
		{
			$nombrerescambiado = $row['Nombre'];
			$emailseleccionado = $row['Empema'];
		}

		//----------Se organiza el correo para ser enviado a los interesados
		$wcorreopmla = consultarAliasPorAplicacion( $conex, $wemp_pmla, "emailEnvioCambioResponsable");
		$wcorreopmla = explode("--", $wcorreopmla );
		$wpassword   = $wcorreopmla[1];
		$wremitente  = $wcorreopmla[0];
		$datos_remitente = array();
		$datos_remitente['email']	= $wremitente;
		$datos_remitente['password']= $wpassword;
		$datos_remitente['from'] = $wremitente;
		$datos_remitente['fromName'] = $wremitente;

		$message  = "<html><body>";

		$message .= "<table width='100%' bgcolor='#e0e0e0' cellpadding='0' cellspacing='0' border='0'>";

		$message .= "<tr><td>";

		$message .= "<table align='center' width='100%' border='0' cellpadding='0' cellspacing='0' style='background-color:#fff; font-family:Verdana, Geneva, sans-serif;'>";

		$message .= "<thead>
						<tr height='80' align='left'>
							<th colspan='4' style='background-color:#ffffff; border-bottom:solid 1px #bdbdbd; font-family:Verdana, Geneva, sans-serif; color:#333; font-size:30px;' >
							<img width='110px' height='70px' src='../../images/medical/root/clinica.JPG'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Cambio de Responsable</th>
						</tr>
					 </thead>";

		$message .= "<tbody>
					   <tr>
					   <td colspan='4' style='padding:15px;'>
					   <p style='font-size:18px;'>Buenos dias, el paciente con historia ".$whistoria."-".$wingreso."  ".$nombre." debe cambiarse de responsable. Desde la fecha : ".$wdatofecha." </p>
					   </tr>
					   <tr>
							<td>
							<table align='center'>
							 <tr align='center' style='background-color: #2a5db0; color: #ffffff; font-size: 14pt; padding:1px; font-family: verdana; fond-weight: bold;'><td>Responsable actual</td><td>Responsable al que debe cambiar</td></tr>
							 <tr style='background-color: #C3D9FF; color: #000000; font-size: 10pt; padding:1px; font-family: verdana;'><td>".$responsableactualcodigo." ".$nombreresactual."</td><td>".$wresseleccionado." ".$nombrerescambiado."</td></tr>
							</table>
							</td>
					   <tr>
					   <tr>
					   <td style='font-size: 14pt;' ><br>Texto del Medico: ".$wtextareapaf."</td>
					   </tr>
					   <tr height='80'>
					   <hr />
					   <td colspan='4' align='center' style='background-color:#f5f5f5; border-top:dashed #00a2d1 2px; font-size:24px; '>
					   </td>
					   </tr>
					</tbody>";

		$message .= "</table>";

		$message .= "</td></tr>";
		$message .= "</table>";

		$message .= "</body></html>";
		//$mensaje .= "El paciente con historia :".$whistoria."-".$wingreso."  Se debe de cambiar de responsable  por : ".$wresseleccionado;

		// if (!$tieneResponsable)
			// $mensaje .= "<br> Debe agregarse este responsable en la admision";
		$mensaje = $message;
		//$mensaje = "esto es una prueba";

		$wdestinatarios	= consultarAliasPorAplicacion( $conex, $wemp_pmla, "emailCambioResponsable");
		if($emailseleccionado !='')
		{
		  $wdestinatarios = $wdestinatarios.",".$emailseleccionado;
		}
		$wdestinatarios = explode(",",$wdestinatarios);
		$wasunto 		= "Cambio de Reponsable paciente:".$whistoria;
		$altbody 		= "Cordial saludo,<br> \n\n Cambio de  responsable";
		sendToEmail($wasunto,$mensaje,$altbody, $datos_remitente, $wdestinatarios );
		//--------------------------------
		//-----------------------------------------
	}


	if($cambioResponsableAutomatico=="on")
	{

		$select_res = "SELECT Empcod,Empnom AS Nombre, Emptar , Empema
							 FROM ".$wbasedato."_000024
							WHERE Empcod = '".$wresseleccionado."'";

		$res = 	mysql_query($select_res,$conex) or die ("Error 1: ".mysql_errno()." - en el query: ".$select_res." - ".mysql_error());

		$emailseleccionado='';
		while($row = mysql_fetch_array($res))
		{
			$tarifanueva = $row['Emptar'];
			$emailseleccionado = $row['Empema'];
		}

		if ($tieneResponsable)
		{


				/*$html .= "Tiene Responsalbe";
				$html .= "<br>" ;

				$update = " UPDATE  ".$wbasedato."_000205 SET Resord = '2'
							 WHERE Reshis = '".$whistoria."'
							   AND Resing = '".$wingreso."'
							   AND Resord = '1' ";

				mysql_query($update,$conex);

				$html .= $update ;
				$html .= "<br>" ;
				$update = " UPDATE  ".$wbasedato."_000205 SET Resord = '1'
							 WHERE Reshis = '".$whistoria."'
							   AND Resing = '".$wingreso."'
							   AND Resnit = '".$wresseleccionado."' ";

				$html .= $update ;
				$html .= "<br>" ;
				mysql_query($update,$conex);

				$update = " UPDATE  ".$wbasedato."_000101
							   SET Ingent  = ''  ,
								   Ingcem  = '".$wresseleccionado."',
								   Ingtar  = '".$tarifanueva."'
							 WHERE Inghis  = '".$whistoria."'
							   AND Ingnin  = '".$wingreso."'";

				$html .= $update ;
				mysql_query($update,$conex);*/
				$html ='no';

		}
		else
		{

			/*$html .= "2 Tiene Responsalbe";
			//--- Pongo el primer responsable como el segundo responsable en la 205
			$update = " 	UPDATE  ".$wbasedato."_000205
							   SET Resord = '2' ,
								   Resffr = '".date("Y-m-d")."'
							 WHERE Reshis = '".$whistoria."'
							   AND Resing = '".$wingreso."'
							   AND Resord = '1' ";

			mysql_query($update,$conex);
			$html .= $update ;
			*/
			//--- Inserto el responsable en la 205, al insertarlo lo pongo como primer responsable
			$insert = "INSERT INTO ".$wbasedato."_000205   (Fecha_data		, 		Hora_data		, 	 Medico			, 		Reshis			,	Resing			,	Restdo 	, 		     Resnit         ,		Resnom 		,	Resord  ,		Resfir 		 	,		Resffr		, Resest 	, Restpa 	,	Respla 	, Respol,	Resnco 	,Resaut	, 	Resfha ,Reshoa,Resnpa 	, Respco ,	Rescom ,	Seguridad   )
						    VALUES					 	('".date("Y-m-d")."', 	'".date("H:i:s")."'	, '".$wbasedato."'  ,	'".$whistoria."'	,  '".$wingreso."'	,	'NIT' 	,	'".$wresseleccionado."'	, 		 ''			,  	'".(($cuantosresponsable*1)+1)."'		, 	'".date("Y-m-d")."'	,	'0000-00-00' 	,  'on' 	,	'E'		,	''		, 	''	,	''		,  ''	,    ''    , ''   , '' 		,  ''    ,    ''   , 'C-".$wuse."'	) ";


			mysql_query($insert,$conex);
			/*
			$html .= "<br>" ;
			$html .= $insert ;

			$update = " UPDATE  ".$wbasedato."_000101
						   SET Ingcem  = '".$wresseleccionado."',
						       Ingent  = '',
							   Ingtar  = '".$tarifanueva."'
						 WHERE Inghis  = '".$whistoria."'
						   AND Ingnin  = '".$wingreso."'";

			mysql_query($update,$conex);
			$html .= $update ;
			$html .= "<br>" ;
			*/
			$html ="Se  inserto como ".(($cuantosresponsable*1)+1)." responsable a  ".$wresseleccionado." ";


		}

		$wdestinatarios	= consultarAliasPorAplicacion( $conex, $wemp_pmla, "emailCambioResponsable");
		if($emailseleccionado !='')
		{
		  $wdestinatarios = $wdestinatarios.",".$emailseleccionado;
		}
		$wdestinatarios = explode(",",$wdestinatarios);
	}

	$respuestaarray = array();
	$respuestaarray['responsable'] = $html ;
	$respuestaarray['destinatarios'] =  $wdestinatarios;
	return $respuestaarray;

}

function Grabarpafbitacora($whistoria,$wingreso,$wresponsable,$wresseleccionado, $wtextareapaf , $wdatofecha)
{
	global $conex;
	global $wemp_pmla;
	global $wuse;

	// --> Guardar en la bitacora del paciente, la nueva observacion si la hay.
	$wbasedatoMovhos	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	$mensaje = "El paciente con historia :".$whistoria."-".$wingreso."  Se cambia de responsable por el responsable  : ".$wresseleccionado." ".$wresponsable;

	$arrObservaciones 	= json_decode($arrObservaciones, true);

	$sqlUltId = "
	SELECT MAX(Bitnum) AS id
	  FROM ".$wbasedatoMovhos."_000021 ";
	$resUltId = mysql_query($sqlUltId, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUltId):</b><br>".mysql_error());
	if($rowUltId = mysql_fetch_array($resUltId))
	{
		$nuevoId 				= ($rowUltId['id']*1)+1;
		$temaBitacora = 'AM';

		$sqlInserBit = " INSERT INTO ".$wbasedatoMovhos."_000021
				(Medico,				Fecha_data,			Hora_data,			Bithis,				Biting,				Bitnum,			Bitobs,		 Bitusr,			Bittem,					Seguridad		)
		VALUES 	('".$wbasedatoMovhos."','".date("Y-m-d")."','".date("H:i:s")."','".$whistoria."','".$wingreso."','".$nuevoId."', '".$mensaje." ".$wtextareapaf." , a partir de la fecha: ".$wdatofecha."', 	'".$wuse."',	'".$temaBitacora."', 	'C-".$wuse."' 	)
		";
		mysql_query($sqlInserBit, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInserBit):</b><br>".mysql_error());

		//$arrObservaciones[$clave]['idBitacora'] 	= mysql_insert_id();


		//---------- Se verifica si ya hay una auditoria en  la tabla 136 si hay una se adiciona, si no se crea una nueva
		$q = "SELECT Audhis
				FROM ".$wbasedatoMovhos."_000136
			   WHERE Audhis='".$whistoria."'
			     AND Auding='".$wingreso."'
				 AND Fecha_data = '".date("Y-m-d")."'
				 AND Seguridad ='C-".$wuse."' ";
		$resq = mysql_query($q, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUltId):</b><br>".mysql_error());

		$hay_auditoria = false;
		if($row = mysql_fetch_array($resq))
		{
			$hay_auditoria=true;
		}



		if($hay_auditoria)
		{
			$q = "UPDATE  ".$wbasedatoMovhos."_000136
			         SET   Audobs = CONCAT(Audobs, ' <br> ', '".$mensaje." ".$wtextareapaf." , a partir de la fecha: ".$wdatofecha."' )
				   WHERE Audhis='".$whistoria."'
					 AND Auding='".$wingreso."'
				     AND Fecha_data = '".date("Y-m-d")."'
				     AND Seguridad ='C-".$wuse."' ";

			mysql_query($q, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInserBit):</b><br>".mysql_error());

		}
		else
		{
			$q="	INSERT INTO
						".$wbasedatoMovhos."_000136
							(medico,
							 Fecha_data, Hora_data, Audhis, Auding, Audest, Audmed, Audayd, Audpob, Audvob, Audead, Audrei, Audalt, Audobs, Seguridad)
						 VALUES
						 	('".$wbasedatoMovhos."', '".date("Y-m-d")."', '".(string)date("H:i:s")."', '".$whistoria."', '".$wingreso."', '', '', '', '', '','', '', '', '".$mensaje." ".$wtextareapaf." , a partir de la fecha: ".$wdatofecha."',  'C-".$wuse."');";
				mysql_query($q, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInserBit):</b><br>".mysql_error());
		}

	}

}

function historialPacientesPaf($whistoria,$wingreso,$wbasedato){
	global $conex;
	global $wemp_pmla;
	global $wuse;

	$select = "SELECT Fecha_data, Hora_data, Esthis, Esting, Estres
				 FROM ".$wbasedato."_000265
				WHERE Esthis='".$whistoria."'
				  AND Esting='".$wingreso."'";

	$res = mysql_query($select,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$select." - ".mysql_error());
	$i=0;
	$html.='';
	$html.="<br><br><table align='center'><tr class='encabezadoTabla'><td>Historia</td><td>Responsable</td><td>Fecha de Cambio</td>";
	while($row = mysql_fetch_array($res))
	{

		if (($i%2)==0)
			$wcf="fila1";  // color de fondo de la fila
		else
			$wcf="fila2"; // color de fondo de la fila
		//------
		$i++;

		$html.="<tr class='".$wcf."'><td>".$row['Esthis']."-".$row['Esting']."</td><td>".$row['Estres']."</td><td>".$row['Fecha_data']."</td></tr>";

	}
	$html.="</table>";

	return $html;

}

/**
    Esta función se encarga de buscar la foto o silueta para la persona encontrada, como en linux hay diferencia en encontrar una extensión con
    mayúsculas o minúsculas entonces se valídan las convinaciones entre la extensión .jpg variando entre mayúsculas y minúsculas

	@param $wcedula       : Si existe un número de cedula indica que se debe buscar la foto con este nombre, si no existe se muestra silueta en base al sexo.
	@param $sex           : Indica si es hombre o mujer para poder mostrar las silueta adecuada.
	@param $$permite_foto : Indica si se puede mostrar o no la foto según determinacion del empleado.

    @return string: nombre de la foto que se va a mostrar.
 */
function getFoto($wcedula = 'not_foto',$sex='M')
{
    $extensiones_img = array(   '.jpg','.JPG','.Jpg','.jPg','.jpG','.JPg','.JpG','.jPG',
                                '.png','.Png','.pNg','.pnG','.PNg','.PnG','.PNG','.pNG');
    $wruta_fotos = "../../images/medical/tal_huma/";
    $wfoto = "silueta".$sex.".png";

    $wfoto_em = '';
    $ext_arch = '';

    // comentado para que no aparezca foto a nadie, determinación temporal.
    foreach($extensiones_img as $key => $value)
    {
        $ext_arch = $wruta_fotos.trim($wcedula).$value;
        // echo "<!-- Foto encontrada: $ext_arch -->";
        if (file_exists($ext_arch))
        {
            $wfoto_em = $ext_arch;
            break;
        }
    }

    if ($wfoto_em == '')
    {
        $wfoto_em = $wruta_fotos.$wfoto;
    }

    return $wfoto_em;
}

//=======================================================================================================================================================
//		F I N	 F U N C I O N E S	 P H P
//=======================================================================================================================================================

//=======================================================================================================================================================
//		F I L T R O S  	 D E  	L L A M A D O S  	P O R  	J Q U E R Y  	O  	A J A X
//=======================================================================================================================================================
if(isset($accion))
{
	switch($accion)
	{

		case "pasar_examenes" :
		{
				$conexUnix = odbc_connect('facturacion','informix','sco');
				// odbc_autocommit($conexUnix, FALSE);

				//--------------Arreglar
				// mirar si al agregar la condicion de cardefue='14' se aumenta el tiempo de respuesta
				$mes_actual = date("m");
				$anio_actual = date("Y");

				$query =  "SELECT cardetfue,cardetdoc,cardetreg,cardetite
							 FROM FACARDET
						    WHERE cardetite = '0'
							  AND cardetfue = '14'
							  AND cardetmes = '{$mes_actual}'
							  AND cardetano = '{$anio_actual}' ";

				$err 	= odbc_do($conexUnix,$query);
				$campos	= odbc_num_fields($err);
				$i 		= 0;

				while(odbc_fetch_row($err))
				{
					if (odbc_result($err,1) =='14' && odbc_result($err,4)== '0' )
					{


						// falta el estado
						$sqlinsert = " INSERT INTO Labmatrix (labfue	,		labdoc 					,			labreg			,	labest)
											VALUES 			 ('14'		,'".odbc_result($err,2)."'	,	'".odbc_result($err,3)."'	, 	'P') ";

						// odbc_do($conexUnix,$sqlinsert);



						$sqlupdate = "UPDATE FACARDET
										 SET cardetite='1'
									   WHERE cardetreg= '".odbc_result($err,3)."'";

						// odbc_do($conexUnix,$sqlupdate);


					}


				}

				odbc_close($conexUnix);
				odbc_close_all();

			break;

		}
		case "Examinar_Labmatrix" :
		{


			$conexUnix = odbc_connect('facturacion','informix','sco');
			// odbc_autocommit($conexUnix, FALSE);
			// estado P = pasado
			$query = "SELECT cardethis,cardetnum,cardetcon,cardetcod,cardettar,cardetfec,cardetfue,labreg,cardetvun
						FROM labmatrix , FACARDET
						WHERE labreg = cardetreg
						AND (labest ='P' OR labest ='NT' OR labest ='NP' ) ";

			$err 	= odbc_do($conexUnix,$query);
			$campos	= odbc_num_fields($err);
			$i 		= 0;



			while(odbc_fetch_row($err)  && $i<300)
			{
					$i++;

					$seguir = 'si';
					$activo = '';


					$concepto 		=  	odbc_result($err,3);
					$procedimiento 	=  	odbc_result($err,4);
					$tarifa 		=   odbc_result($err,5);
					$fecha 			=   odbc_result($err,6);
					$reg 			= 	odbc_result($err,8);
					$valor 			=	odbc_result($err,9);

					$info = array('tieneTarifa' => 'NT', 'queryTarifa' => '', 'mensaje' => '');

					if($valor*1 != 0)
					{
						$sqlTipLiq = "SELECT exaliq, exagex,exaact
										FROM INEXA
									   WHERE exacod = '".$procedimiento."'
										 AND exaliq != ''
										 AND exagex IS NOT NULL
									   UNION
									  SELECT exaliq, '',exaact
										FROM INEXA
									   WHERE exacod = '".$procedimiento."'
										 AND exaliq != ''
										 AND exagex IS NULL";


						// --> Obtener el tipo de liquidacion
						$tipoLiquidacion 	= '';
						$grupoQuirurgico 	= '';
						$resTipLiq 			= odbc_exec($conexUnix, $sqlTipLiq);
						if(odbc_fetch_row($resTipLiq))
						{
							$activo 			= trim(odbc_result($resTipLiq, 3));
							$tipoLiquidacion 	= trim(odbc_result($resTipLiq, 1));
							$grupoQuirurgico 	= trim(odbc_result($resTipLiq, 2));
						}
						else
						{
							$seguir = 'no';

						}

						if($seguir == 'si')
						{
							// --> Buscar la tarifa segun el tipo de liquidacion
							switch($tipoLiquidacion)
							{
								// --> Tipo de liquidacion por codigo
								case 'C':
								{
									$sqlTarifa = "SELECT COUNT(*) AS cantidad
														FROM INEXATAR
													   WHERE exatarexa = '".$procedimiento."'
														 AND (exatartar = '".$tarifa."' OR exatartar = '*')
														 AND exatartse = '*'
														 AND exatarcon = '".$concepto."'
														 AND exatartip = '*'";
									break;

								}
								// --> Tipo de liquidacion por grupo GQX
								case 'G':
								{
									$sqlTarifa = "SELECT COUNT(*) AS cantidad
													FROM INQUITAR
												   WHERE quitarqui = '".$grupoQuirurgico."'
													 AND (quitartar = '".$tarifa."' OR exatartar = '*')
													 AND quitartse = '*'
													 AND quitarcon = '".$concepto."'
													 AND quitartip = '*'";
									break;
								}
								// --> Tipo de liquidacion por UVR
								case 'U':
								{
									$sqlTarifa = "SELECT COUNT(*) AS cantidad
													FROM FAUNITAR
												   WHERE (unitartar = '".$tarifa."' OR exatartar = '*')
													 AND unitartse = '*'
													 AND unitarcon = '".$concepto."'";
									break;
								}
							}


							// --> Ejecutar query de la tarifa
							$info['queryTarifa'] = $sqlTarifa;
							$resTarifa = odbc_exec($conexUnix, $sqlTarifa);
							if(odbc_fetch_row($resTarifa))
							{
								if(odbc_result($resTarifa, 'cantidad') > 0)
									$info['tieneTarifa'] = "si";
							}
						}
						else
						{
							// No procedimiento
							$info['tieneTarifa'] = "NP";
							$activo ='no';
						}


						if ($info['tieneTarifa']!='si' )
						{

							$sqlupdate = "UPDATE Labmatrix
											 SET labest='".$info['tieneTarifa']."'
										   WHERE labreg= '".$reg."'";

							odbc_do($conexUnix,$sqlupdate);

						}
						else
						{

								$sqlupdate = "UPDATE Labmatrix
												 SET labest='R'
											   WHERE labreg= '".$reg."'";

								odbc_do($conexUnix,$sqlupdate);
						}
					}
					else
					{
						$sqlupdate = "UPDATE Labmatrix
										 SET labest='R'
									   WHERE labreg= '".$reg."'";

						odbc_do($conexUnix,$sqlupdate);

					}
			}
				odbc_close($conexUnix);
				odbc_close_all();
			break;
		}

		case "Mostrar_monitor" :
		{

			$concepto      = (isset($concepto)) ? $concepto:'';
			$procedimiento = (isset($procedimiento)) ? $procedimiento:'';
			$tarifa        = (isset($tarifa)) ? $tarifa:'';
			$conexUnix = odbc_connect('facturacion','informix','sco');
			// odbc_autocommit($conexUnix, FALSE);


			$array_proc_tar = array();
			$query = "SELECT cardethis,cardetnum,cardetcon,cardetcod,cardettar,cardetfec,cardetfue,labreg,labest
					  FROM labmatrix , FACARDET
					 WHERE labreg = cardetreg
					   AND (labest ='NT' OR labest ='NP' )";

			$err 	= odbc_do($conexUnix,$query);
			$campos	= odbc_num_fields($err);
			$i 		= 0;
			$html ='<br><table align="center" width="90%" >
							<tr><td colspan="5"></td><td colspan="2" class="encabezadoTabla">Numero de registros : codigoareemplazar</td></tr>
							<tr class="encabezadoTabla" style="text-align: center" >
								<td>Historia</td>
								<td>Ingreso</td>
								<td>Concepto</td>
								<td>Procedimiento</td>
								<td>Tarifa</td>
								<td>Problema</td>
								<td></td>
							</tr>';


			while(odbc_fetch_row($err) )
			{
					//-------
				if (($i%2)==0)
					$wcf="fila1";  // color de fondo de la fila
				else
					$wcf="fila2"; // color de fondo de la fila
				//------
				$i++;

				// No tiene tarifa
				$estado = odbc_result($err,9);
				if($estado =='NT')
					$estado = ' No existe tarifa';
				else
					$estado = ' No existe Procedimiento';

				$html .= '<tr class="'.$wcf.'"><td style="font-size: 10px;">'.odbc_result($err,1).'</td><td style="font-size: 10px;">'.odbc_result($err,2).'</td><td style="font-size: 10px;">'.odbc_result($err,3).'</td><td style="font-size: 10px;" >'.odbc_result($err,4).'</td><td style="font-size: 10px;" >'.odbc_result($err,5).'</td><td style="font-size: 10px; text-align: left" ><img width="16" height="16"  src="../../images/medical/sgc/Warning-32.png"> '.$estado.'</td><td><a Style="cursor : pointer" onclick="Crear_tarifa_unix(\''.odbc_result($err,3).'\' , \''.odbc_result($err,4).'\' , \''.odbc_result($err,5).'\' , \''.odbc_result($err,9).'\'  )">Ir</a></td></tr>';
			}
			// Tcarprocod
			// Tcartercod
			 $select = "SELECT Loghis , Loging  ,Logmsj , Logvar , Logreg
						 FROM ".$wbasedato."_000251
						 WHERE  Logerr ='on'";

			$res = mysql_query($select,$conex) or die ("Query (nombreConcepto): ".$q." - ".mysql_error());
			//$html .="<tr><td>".$select."</td></tr>";
			while ($row = mysql_fetch_array($res))
			{



				$i++;

				$conexUnix = odbc_connect('facturacion','informix','sco');
				// odbc_autocommit($conexUnix, FALSE);
				// estado P = pasado
				$query = "SELECT cardethis,cardetnum,cardetcon,cardetcod,cardettar,cardetfec,cardetfue,cardetvun
							FROM  FACARDET
							WHERE cardetreg = '".$row['Logreg']."'";

				$err 	= odbc_do($conexUnix,$query);
				$campos	= odbc_num_fields($err);

				if(odbc_fetch_row($err) )
				{


						$seguir = 'si';
						$activo = '';


						$concepto 		=  	odbc_result($err,3);
						$procedimiento 	=  	odbc_result($err,4);
						$tarifa 		=   odbc_result($err,5);
						$fecha 			=   odbc_result($err,6);
				}
				if (($i%2)==0)
					$wcf="fila1";  // color de fondo de la fila
				else
					$wcf="fila2"; // color de fondo de la fila

				$html .= '<tr class="'.$wcf.'"><td style="font-size: 10px; ">'.$row['Loghis'].'</td><td style="font-size: 10px; ">'.$row['Loging'].'</td><td style="font-size: 10px;">'.$concepto.'</td><td style="font-size: 10px;" >'.$procedimiento.'</td><td style="font-size: 10px;" >'.$tarifa.'</td><td style="font-size: 10px;" ><img width="16" height="16"  src="../../images/medical/sgc/Warning-32.png"> '.utf8_encode($row['Logmsj']).'</td><td></td></tr>';

			}

			$html .='</table>';

			$html = str_replace("codigoareemplazar",$i,$html);

			$data['html'] = $html;
			echo json_encode($data);

			odbc_close($conexUnix);
			odbc_close_all();
			break;
		}

		case "grabacionCargosLaboratorio":
		{
			//grabacionCargosLaboratorio();
			break;
		}

		case "traer_rol":
		{
			$cargo = '';
			$perfilMonitorAud 		= '';
			$filtroEmpresasRevisar 	= '';
			$arr_permisos_opciones 	= array( "Cirugias_sin_mercado" 	=> array(	"cargar"                => "off",
																				"devolver"              => "off",
																				"anular_no_liquidado"   => "off",
																				"anular_liquidado_unix" => "off",
																				"liquidar_en_unix"      => "off",
																				"consultar_tarifa"      => "off",
																				"liquidar_cirugias"     => "off"),
						                    "Cirugias_con_mercado_y_no_liquidadas" 	=>
						                                                array(	"cargar"                => "off",
																				"devolver"              => "off",
																				"anular_no_liquidado"   => "off",
																				"anular_liquidado_unix" => "off",
																				"liquidar_en_unix"      => "off",
																				"consultar_tarifa"      => "off",
																				"liquidar_cirugias"     => "off"));

			$nuser_session = explode('-',$_SESSION['user']);
			$nwuse = $user_session[1];
			$array_rol = array();
			$data = array('array_rol' =>$array_rol , 'cantidad' => 0  );
			$wbasedatotalhuma 		= consultarAliasPorAplicacion($conex, $wemp_pmla, 'talhuma');

			$use_emp = empresaEmpleado($wemp_pmla, $conex, '', $nwuse);

			$q= "SELECT Ideccg
				   FROM ".$wbasedatotalhuma."_000013
				  WHERE Ideuse = '".$use_emp."'";

			$res = mysql_query($q,$conex) or die ("Query (nombreConcepto): ".$q." - ".mysql_error());

			if ($row_q = mysql_fetch_array($res))
			{
				$cargo = $row_q['Ideccg'];
			}

			$q= "   SELECT  Cxmadm, Monnom, Cxmpcm, Cxmpdm, Cxmanl, 'off' AS Cxmaml, Cxmlmu, Cxmctr, Cxmlcm, Cxmptm, Cxmlce,Cxmame,Cxmpca,Cxmlta, Cxmmai,
							1 AS Prioridad, Cxmprc, Cxmepr
					FROM    {$wbasedato}_000228, {$wbasedato}_000232
					WHERE   Cxmusu LIKE '%{$wuse}%'
					        AND Cxmmon = Monide
					        AND Cxmest = 'on'
					UNION
				    SELECT  Cxmadm, Monnom, Cxmpcm, Cxmpdm, Cxmanl, 'off' AS Cxmaml, Cxmlmu, Cxmctr, Cxmlcm, Cxmptm, Cxmlce,Cxmame,Cxmpca,Cxmlta, Cxmmai,
							2 AS Prioridad, Cxmprc, Cxmepr
					FROM    {$wbasedato}_000228, {$wbasedato}_000232
					WHERE   Cxmcar = '{$cargo}'
					        AND Cxmmon = Monide
					        AND Cxmest = 'on'
					ORDER BY Prioridad ASC ";
					//   AND Monide in (1,2)

			$res = mysql_query($q,$conex) or die ("Query (nombreConcepto): ".$q." - ".mysql_error());
			$i = 1;
			while($row_q = mysql_fetch_array($res))
			{
				if(in_array($row_q['Monnom'], $array_rol))
					continue;

				$array_rol[$i] = $row_q['Monnom'];
				$id_monitor_rol = $row_q['Monnom'];
				if($id_monitor_rol == 'Cirugias_sin_mercado' || $id_monitor_rol == 'Cirugias_con_mercado_y_no_liquidadas')
				{
					$arr_permisos_opciones[$id_monitor_rol]["usuarios_admin"]                     = $row_q['Cxmadm'];
					$arr_permisos_opciones[$id_monitor_rol]["cargar_almacen"]                     = $row_q['Cxmpca'];
					$arr_permisos_opciones[$id_monitor_rol]["cargar_facturador"]                  = $row_q['Cxmpcm'];
					$arr_permisos_opciones[$id_monitor_rol]["devolver"]                           = $row_q['Cxmpdm'];
					$arr_permisos_opciones[$id_monitor_rol]["anular_no_liquidado"]                = $row_q['Cxmanl'];
					$arr_permisos_opciones[$id_monitor_rol]["anular_liquidado_unix"]              = $row_q['Cxmaml'];
					$arr_permisos_opciones[$id_monitor_rol]["liquidar_en_unix"]                   = $row_q['Cxmlmu'];
					$arr_permisos_opciones[$id_monitor_rol]["consultar_tarifa"]                   = $row_q['Cxmctr'];
					$arr_permisos_opciones[$id_monitor_rol]["liquidar_cirugias"]                  = $row_q['Cxmlcm'];
					$arr_permisos_opciones[$id_monitor_rol]["cerrar_mercado_facturador"]          = $row_q['Cxmptm'];
					$arr_permisos_opciones[$id_monitor_rol]["cerrar_mercado_almacen"]             = $row_q['Cxmlta'];
					//$arr_permisos_opciones[$id_monitor_rol]["cerrar_mercado_almacen"]  = $row_q['Cxmptm'];
					$arr_permisos_opciones[$id_monitor_rol]["liquidar_cirugias_horario_especial"] = $row_q['Cxmlce'];
					$arr_permisos_opciones[$id_monitor_rol]["abrir_mercado"]                      = $row_q['Cxmame'];
					$arr_permisos_opciones[$id_monitor_rol]["permiteReversarCx"]                  = $row_q['Cxmprc'];
				}

				// --> Jerson Trujillo, 2016-01-21, rol para el monitor de auditoria interna
				if($id_monitor_rol == 'Procedimientos_no_autorizados')
				{
					$arr_permisos_opciones[$id_monitor_rol]["perfilMonitorAudMonitorAuditoria"] = $row_q['Cxmmai'];
					$arr_permisos_opciones[$id_monitor_rol]["filtroEmpresasRevisar"] 			= $row_q['Cxmepr'];
					$perfilMonitorAud 		= $row_q['Cxmmai'];
					$filtroEmpresasRevisar 	= $row_q['Cxmepr'];
				}

				$i++;
			}

			// $array_rol[1] = 'Cirugias_sin_mercado';
			// $array_rol[2] = 'Cirugias_con_mercado_y_no_liquidadas';
			// $array_rol[3] = 'monitor_correos';
			// $array_rol[4] = 'cargos_sin_tarifa';
			// $array_rol[5] = 'Cirugias_Con_Cargos_Pendientes';
			// $array_rol[6] = 'Pacientes_proceso_alta_sin_estancia_liq';
			// $array_rol[7] = 'Procedimientos_no_autorizados';
			// $array_rol[8] = 'monitor_laboratorios';

			$data['wcodigo_cargo_wuse'] = $cargo;
			$data['arr_permisos_opciones'] = base64_encode(serialize($arr_permisos_opciones));
			$data['array_rol'] = $array_rol;
			$data['cantidad'] = count($array_rol);
			$data['perfilMonitorAud'] 		= $perfilMonitorAud;
			$data['filtroEmpresasRevisar'] 	= $filtroEmpresasRevisar;
			$data['q'] = $q;
			echo json_encode($data);
			break;
		}

		case "mostrarPendientesRegrabacion":
		{

			echo mostrarPendientesRegrabacion();
			break;
		}

		case "cargos_sin_tarifa" :
		{

			echo cargos_sin_tarifa();
			break;

		}

		case "grabar_tarifa" :
		{

			$data70  = 0; // se almacena el exito o fallo de la grabacion.
			$data104 = 0; // se almacena el exito o fallo de la grabacion.
			$data192 = 0; // se almacena el exito o fallo de la grabacion.

			//validacion antes de crear una tarifa si es codigo y  viene de auditoria
			if($wtipo_facturacion =='CODIGO' )
			{
				// Valida si esta en la 70
				$select70 = "  SELECT *
								 FROM  ".$wbasedato."_000070
								WHERE  Proempcod = '".$wcodigo_procedimiento."'
								  AND  Proempemp = '".$wcodigo_empresa."'
								  AND  Proemptip = '*'
								  AND  Proempcco = '".$wcco."' ";

				$res70 = mysql_query($select70,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$select70." - ".mysql_error());

				if($row = mysql_fetch_array($res70))
				{
					echo $data['mensaje'] = 'Hay una tarifa creada con estos parametros en la tabla 70';
					return ;
				}

				// Valida si esta en la 104
				$select104 = " SELECT *
								 FROM ".$wbasedato."_000104
								WHERE Tarcod ='".$wcodigo_procedimiento."'
								  AND Tarcon ='".$wconcepto."'
								  AND Tartar ='".$wtar."'
								  AND Tartin ='*'
								  AND Tarcco ='".$wcco."'
								  AND Taresp ='*'
								  AND Tarhon ='*'
								  AND Taruvi =''
								  AND Targme ='*'
								  AND Taruvf =''
								  AND Tartur ='*' ";

				$res104 = mysql_query($select104,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$select104." - ".mysql_error());

				if($row = mysql_fetch_array($res104))
				{
					echo $data['mensaje'] = 'Hay una tarifa creada con estos parametros en la tabla 104';
					return ;
				}

				// valida la homologacion

				$select192 = "SELECT *
								FROM ".$wbasedato."_000192
							   WHERE Homcom = '".$wconcepto."'
								 AND Hompom = '".$wcodigo_procedimiento."'
								 AND Homccm = '".$wcco."'
								 AND Homtem = '*'
								 AND Homtam = '".$wtar."'
								 AND Homenm = '".$wcodigo_empresa."'
								 AND Homtim = '*'
								 AND Homtpm = '*'
								 AND Homtrm = '*'
								 AND Homesm = '*'
								 AND Homthm = '*'
								 AND Homtct = '*'
								 AND Homgme = '*' " ;

				$res192 = mysql_query($select192,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$select192." - ".mysql_error());

				if($row = mysql_fetch_array($res192))
				{
					echo $data['mensaje'] = 'Hay una Homologacion creada con estos parametros en la tabla 192';
					return ;
				}

			}
			//----------------------------------------------------------------------------------



			//-------------------------------------------------------------------------
			// si la tarifa se guarda por codigo
			if($wtipo_facturacion =='CODIGO')
			{

				// Va a la tabla 70 para  insertar el tipo de facturacion particular de la empresa
				$insert = "INSERT INTO ".$wbasedato."_000070 (	Fecha_data			, 		Hora_data			,	 Medico			, 		Proempcod				,		Proempemp			, 	Proemptip	 	, 	Proemptfa				, Proempgqx  	, Proemppun		, 		Proemppro		, 		Proempnom		, 	Proempest	, Proempcco		, 		Seguridad		)
								VALUES 						 ('".date("Y-m-d")."'	, 	'".date("H:i:s")."'		, '".$wbasedato."'  ,'".$wcodigo_procedimiento."'	,  '".$wcodigo_empresa."'	,		'*'	 	 	, '".$wtipo_facturacion."'	, '".$wgqx."'	, '".$wuvr."'	, 	'".$wempresa_pro."'	,	'".$wempresa_nom."'	, 		'on'	,  '".$wcco."'	, 	'C-".$wbasedato."'	) ";


				if (mysql_query($insert,$conex) )
				{

					// Va a la tabla 104 y guarda el valor de la tarifa
					$insert2 = "INSERT INTO ".$wbasedato."_000104 (	Fecha_data			, 		Hora_data			,	 Medico	 		, Tarcod						, 		Tarcon			, 	 Tartar			, 	Tartin	, 	Tarcco		, 	Taresp	, 		Tarvac			, 		Tarfec				,		Tarvan			, Tarest	, Tarhon ,  Taruvi	 , Taruvf	    ,	Targme , Tartur		, 	Seguridad)
									 VALUES 					  ('".date("Y-m-d")."'	, 	'".date("H:i:s")."'		, '".$wbasedato."'	, '".$wcodigo_procedimiento."'	,	'".$wconcepto."'	,	'".$wtar."'    ,	  '*'	,	'".$wcco."'	,	 '*'	,    '".$wvalor."'		,  '".date("Y-m-d")."'		,	 '".$wvalor."'		,  'on'		,  '*' 	 ,		''	  ,  ''		    ,	 '*'   , '*'		,  'C-".$wbasedato."') ";
					if( mysql_query($insert2,$conex) )
					{
						$exitoso = '1';
						//$data104 = 1; // ok insercion tabla cliame_000104

					}else
					{
						$exitoso= 'Error al  insertar en la tabla (104)';
					}

				}
				else
				{
					$exitoso = 'Error al Insertar en la tabla (70)';
					//$data70  = 1; // ok insercion tabla cliame_000070
				}

				// Si todo ha salido bien  sigue con los siguientes pasos
				// - Guardar la homologacion en la tabla 192  y  regrabar todos los registros que tengan el mismo problema
				if($exitoso == '1')
				{
					// Inserta la Homologacion en la tabla 192 Homologando al concepto 0037 en unix
					$insert = "INSERT INTO  ".$wbasedato."_000192  (Fecha_data			, 		Hora_data			,	 Medico			,  Homcom			, 		Hompom						, Homccm		, Homtem, 	Homtam			, 	Homenm					, 	Homtim	, 	Homtpm	,	Homtrm  ,	Homesm	,	Homthm,	Homtct ,	Homgme,	Homcos  ,	Hompos,	Homtis,	Homtrs,	Homest,	Seguridad)
									VALUES						 ('".date("Y-m-d")."'	, 	'".date("H:i:s")."'		, '".$wbasedato."'	,  '".$wconcepto."'	, '".$wcodigo_procedimiento."'		, '".$wcco."'	, '*'  	,	'".$wtar."'		, 	'".$wcodigo_empresa."'	,	'*' 	, 		'*'	,	 '*'    ,	'*'		,  '*'    ,  '*'  ,     '*'  ,  '0037' ,    ''   ,  '*'  ,  '*'  ,  'on'  , 'C-".$wbasedato."')";

					if(mysql_query($insert,$conex))
					{
						//$data192 = 1; //ok insercion tabla cliame_000192
					}
					//---------------------------
					//-- si el cargo viene desde cirugia actualiza el cargo a estado CO
					if($wparametro =='Cirugia')
					{
						$select = "SELECT  id
									 FROM  ".$wbasedato."_000231
								    WHERE  Tcarcon = '".$wconcepto."'
									  AND  Tcarpro = '".$wcodigo_procedimiento."'
									  AND  Tcartar = '".$wtar."'
									  AND  Tcarres = '".$wcodigo_empresa."'
									  AND  Tcarest = 'on' ";
						$i = 0;
						$err = mysql_query($select,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$select." - ".mysql_error());

						while($row = mysql_fetch_array($err))
						{
							$update = "UPDATE  ".$wbasedato."_000231
										  SET   Tcarrcr = 'CO'
										WHERE   id ='".$row['id']."'";

							mysql_query($update,$conex);

						}

					}
					else
					{
						if($worigen !='Auditoria')
						{
						// Regraba el registro particular en la 106
						$data = regrabarCargo ($wid  , $wcodigo_empresa	, 	'*'		  , 	'*'		 , 	$wcco	, '' );
						//function regrabarCargo($idCargo, $responsble, $tipoIngreso, $tipoPaciente, $centroCostos, $accionLog)
						//print_r($data);
						//---------------------------------------

						// Se optienen los registros similares   para ser regrabados
						$select =  "SELECT   Tcarres  , Tcarconcod , Tcarprocod, Tcartar, id, 	Tcarser
									  FROM   ".$wbasedato."_000106
									 WHERE   Tcarppr 	= 'PT'
									   AND   Tcarres 	= '".$wcodigo_empresa."'
									   AND   Tcarconcod	= '".$wconcepto."'
									   AND   Tcarprocod	= '".$wcodigo_procedimiento."'
									   AND   Tcartar	= '".$wtar."'  ";

						if($wcco != '*')
							$select .=  " AND Tcarser  = '".$wcco."' ";


						$err = mysql_query($select,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$select." - ".mysql_error());
						//---------------------------------------------------------------------------


						//$html ="<br><br><table align='center'><tr class='encabezadoTabla'><td>Historia</td><td>Ingreso</td><td>Codigo Procedimiento</td><td>Nombre Procedimiento</td></tr>";
						$i = 0;
						while($row = mysql_fetch_array($err))
						{

							regrabarCargo ($row['id']  , $wcodigo_empresa	, 	'*'		  , 	'*'		 , 	$row['Tcarser']	, '' );
							//regrabarCargo($idCargo	, $responsble		, $tipoIngreso, $tipoPaciente, $centroCostos	, $accionLog)
						}




						}
						else
						{
							$update = "UPDATE  ".$wbasedato."_000253
										  SET   Audpct  = 'off'
										WHERE   id ='".$wid."'";

							mysql_query($update,$conex);

							// select que trae si para el turno hay mas tarifas por homologar, si todavia quedan pendientes
							// no hago nada, si no quedan pendientes actualiza el estado de la tabla cliame_000252 que es el
							// encabezado de la tabla de auditoria
							$Selectmas ="SELECT *
							               FROM ".$wbasedato."_000253
										  WHERE Audtur  = '".$wturno."'
										    AND Audpct ='on'" ;

							$resmas = mysql_query($Selectmas,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$Selectmas." - ".mysql_error());

							if($row = mysql_fetch_array($resmas))
							{

							}
							else
							{
								$update252 = "UPDATE ".$wbasedato."_000252
												 SET  Auepct  = 'off' ,
												      Auefct  = '".date("Y-m-d")." ".date("H:i:s")."'
											   WHERE  Auetur  ='".$wturno."'";

								mysql_query($update252,$conex);
							}

						}
					}

				}

				echo $exitoso;
				//echo $insert;

			}

			if($wtipo_facturacion =='UVR')
			{

				// Se graba la tarifa en la tabla 70
				$insert = "INSERT INTO ".$wbasedato."_000070 (	Fecha_data			, 		Hora_data			,	 Medico			, 		Proempcod				,		Proempemp			, 	Proemptip	 	, 	Proemptfa				  	, Proemppun		, 		Proemppro		, 		Proempnom		, 	Proempest	, Proempcco		, 		Seguridad		)
								VALUES 						 ('".date("Y-m-d")."'	, 	'".date("H:i:s")."'		, '".$wbasedato."'  ,'".$wcodigo_procedimiento."'	,  '".$wcodigo_empresa."'	,		'*'	 	 	, '".$wtipo_facturacion."'		, '".$wuvr."'	, 	'".$wempresa_pro."'	,	'".$wempresa_nom."'	, 		'on'	,  '".$wcco."'	, 	'C-".$wbasedato."'	) ";


				if (mysql_query($insert,$conex) )
				{
					$exitoso = 'Grabacion exitosa';
				}
				else
				{
					$exitoso = 'Error al Insertar en la tabla (70)';
				}

				if($exitoso == 'Grabacion exitosa')
				{

					// Inserta la Homologacion en la tabla 192 Homologando al concepto 0037 en unix
					$insert = "INSERT INTO  ".$wbasedato."_000192  (Fecha_data			, 		Hora_data			,	 Medico			,  Homcom			, 		Hompom						, Homccm		, Homtem, 	Homtam			, 	Homenm					, 	Homtim	, 	Homtpm	,	Homtrm  ,	Homesm	,	Homthm,	Homtct ,	Homgme,	Homcos  ,	Hompos,	Homtis,	Homtrs,	Homest,	Seguridad)
								    VALUES						 ('".date("Y-m-d")."'	, 	'".date("H:i:s")."'		, '".$wbasedato."'	,  '".$wconcepto."'	, '".$wcodigo_procedimiento."'		, '".$wcco."'	, '*'  	,	'".$wtar."'		, 	'".$wcodigo_empresa."'	,	'*' 	, 		'*'	,	 '*'    ,	'*'		,  '*'    ,  '01'  ,     '*'  ,  '0037' ,    ''   ,  '*'  ,  '*'  ,  'on'  , 'C-".$wbasedato."')";

					mysql_query($insert,$conex);

					if( $wparametro =='Cirugia')
					{
						$select = "SELECT  id
									 FROM  ".$wbasedato."_000231
								    WHERE  Tcarcon = '".$wconcepto."'
									  AND  Tcarpro = '".$wcodigo_procedimiento."'
									  AND  Tcartar = '".$wtar."'
									  AND  Tcarres = '".$wcodigo_empresa."'
									  AND  Tcarest = 'on' ";
						$i = 0;
						$err = mysql_query($select,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$select." - ".mysql_error());

						while($row = mysql_fetch_array($err))
						{
							$update = "UPDATE  ".$wbasedato."_000231
										  SET   Tcarrcr = 'CO'
										WHERE   id ='".$row['id']."'";

							mysql_query($update,$conex);

						}


					}
					else
					{
						// Se optienen los registros similares   para ser regrabados
						$select =  "SELECT   Tcarres  , Tcarconcod , Tcarprocod, Tcartar, id, 	Tcarser
									  FROM   ".$wbasedato."_000106
									 WHERE   Tcarppr 	= 'PT'
									   AND   Tcarres 	= '".$wcodigo_empresa."'
									   AND   Tcarconcod	= '".$wconcepto."'
									   AND   Tcarprocod	= '".$wcodigo_procedimiento."'
									   AND   Tcartar	= '".$wtar."'  ";

						if($wcco != '*')
							$select .=  " AND Tcarser  = '".$wcco."' ";

						$err = mysql_query($select,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$select." - ".mysql_error());

						//$html ="<br><br><table align='center'><tr class='encabezadoTabla'><td>Historia</td><td>Ingreso</td><td>Codigo Procedimiento</td><td>Nombre Procedimiento</td></tr>";
						$i = 0;
						while($row = mysql_fetch_array($err))
						{
								regrabarCargo ($row['id']  , $wcodigo_empresa	, 	'*'		  , 	'*'		 , 	$row['Tcarser']	, '' );
								//regrabarCargo($idCargo	, $responsble		, $tipoIngreso, $tipoPaciente, $centroCostos	, $accionLog)

						}



						// Regraba el registro particular en la 106
						$data = regrabarCargo ($wid  , $wcodigo_empresa	, 	'*'		  , 	'*'		 , 	$wcco	, '' );
						//function regrabarCargo($idCargo, $responsble, $tipoIngreso, $tipoPaciente, $centroCostos, $accionLog)
						//print_r($data);
						//---------------------------------------
						//---------------------------------------------------------------------------
						//	$html ="<br><br><table align='center'><tr class='encabezadoTabla'><td>Historia</td><td>Ingreso</td><td>Codigo Procedimiento</td><td>Nombre Procedimiento</td></tr>";
						$i = 0;
						while($row = mysql_fetch_array($err))
						{

							regrabarCargo ($row['id']  , $wcodigo_empresa	, 	'*'		  , 	'*'		 , 	$row['Tcarser']	, '' );
							//regrabarCargo($idCargo	, $responsble		, $tipoIngreso, $tipoPaciente, $centroCostos	, $accionLog)
						}
					}

				}

				echo $exitoso;



			}

			if ($wtipo_facturacion =='GQX')
			{


				///
				// Va a la tabla 70 para  insertar el tipo de facturacion particular de la empresa
				$insert = "INSERT INTO ".$wbasedato."_000070 (	Fecha_data			, 		Hora_data			,	 Medico			, 		Proempcod				,		Proempemp			, 	Proemptip	 	, 	Proemptfa				, Proempgqx  	, Proemppun		, 		Proemppro		, 		Proempnom		, 	Proempest	, Proempcco		, 		Seguridad		)
								VALUES 						 ('".date("Y-m-d")."'	, 	'".date("H:i:s")."'		, '".$wbasedato."'  ,'".$wcodigo_procedimiento."'	,  '".$wcodigo_empresa."'	,		'*'	 	 	, '".$wtipo_facturacion."'	, '".$wgqx."'	, '".$wuvr."'	, 	'".$wempresa_pro."'	,	'".$wempresa_nom."'	, 		'on'	,  '".$wcco."'	, 	'C-".$wbasedato."'	) ";



				if (mysql_query($insert,$conex) )
				{

					$exitoso = 'Grabacion exitosa';

				}
				else
				{
					$exitoso = 'Error al Insertar en la tabla (70)';
				}


				// Si todo ha salido bien  sigue con los siguientes pasos
				// - Guardar la homologacion en la tabla 192  y  regrabar todos los registros que tengan el mismo problema
				if($exitoso == 'Grabacion exitosa')
				{

					// Inserta la Homologacion en la tabla 192 Homologando al concepto 0037 en unix
					$insert = "INSERT INTO  ".$wbasedato."_000192  (Fecha_data			, 		Hora_data			,	 Medico			,  Homcom			, 		Hompom						, Homccm		, Homtem, 	Homtam			, 	Homenm					, 	Homtim	, 	Homtpm	,	Homtrm  ,	Homesm	,	Homthm,	Homtct ,	Homgme,	Homcos  ,	Hompos,	Homtis,	Homtrs,	Homest,	Seguridad)
								    VALUES						 ('".date("Y-m-d")."'	, 	'".date("H:i:s")."'		, '".$wbasedato."'	,  '".$wconcepto."'	, '".$wcodigo_procedimiento."'		, '".$wcco."'	, '*'  	,	'".$wtar."'		, 	'".$wcodigo_empresa."'	,	'*' 	, 		'*'	,	 '*'    ,	'*'		,  '*'    ,  '01'  ,     '*'  ,  '0037' ,    ''   ,  '*'  ,  '*'  ,  'on'  , 'C-".$wbasedato."')";

					mysql_query($insert,$conex);
					//---------------------------

					if( $wparametro =='Cirugia')
					{
						$select = "SELECT  id
									 FROM  ".$wbasedato."_000231
								    WHERE  Tcarcon = '".$wconcepto."'
									  AND  Tcarpro = '".$wcodigo_procedimiento."'
									  AND  Tcartar = '".$wtar."'
									  AND  Tcarres = '".$wcodigo_empresa."'
									  AND  Tcarest = 'on' ";
						$i = 0;
						$err = mysql_query($select,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$select." - ".mysql_error());

						while($row = mysql_fetch_array($err))
						{
							$update = "UPDATE  ".$wbasedato."_000231
										  SET   Tcarrcr = 'CO'
										WHERE   id ='".$row['id']."'";

							mysql_query($update,$conex);

						}


					}
					else
					{

						// Regraba el registro particular en la 106
						$data = regrabarCargo ($wid  , $wcodigo_empresa	, 	'*'		  , 	'*'		 , 	$wcco	, '' );
						//function regrabarCargo($idCargo, $responsble, $tipoIngreso, $tipoPaciente, $centroCostos, $accionLog)
						//print_r($data);
						//---------------------------------------


						// Se optienen los registros similares   para ser regrabados
						$select =  "SELECT   Tcarres  , Tcarconcod , Tcarprocod, Tcartar, id, 	Tcarser
									  FROM   ".$wbasedato."_000106
									 WHERE   Tcarppr 	= 'PT'
									   AND   Tcarres 	= '".$wcodigo_empresa."'
									   AND   Tcarconcod	= '".$wconcepto."'
									   AND   Tcarprocod	= '".$wcodigo_procedimiento."'
									   AND   Tcartar	= '".$wtar."'  ";

						if($wcco != '*')
							$select .=  " AND Tcarser  = '".$wcco."' ";


						$err = mysql_query($select,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$select." - ".mysql_error());
						//---------------------------------------------------------------------------


						//$html ="<br><br><table align='center'><tr class='encabezadoTabla'><td>Historia</td><td>Ingreso</td><td>Codigo Procedimiento</td><td>Nombre Procedimiento</td></tr>";
						$i = 0;
						while($row = mysql_fetch_array($err))
						{

							regrabarCargo ($row['id']  , $wcodigo_empresa	, 	'*'		  , 	'*'		 , 	$row['Tcarser']	, '' );
							//regrabarCargo($idCargo	, $responsble		, $tipoIngreso, $tipoPaciente, $centroCostos	, $accionLog)
						}
					}
				}

				echo $exitoso;
			}



			break;
		}



		case "grabar_tarifa_unix" :
		{


			$conexUnix = odbc_connect('facturacion','informix','sco');
			// odbc_autocommit($conexUnix, FALSE);
			if($wparametro == 'NT')
			{

				$sqlinsert = " INSERT INTO Inexatar (exatarexa			 ,	exatartar		, exatartse , 	exatarcon		,exatartip	,		exatarvaa	,	exatarfec	,	exatarval			,exatarpor					,	exatarcco		,	exataruad,		exatarfad						  		,exatarumo			,exatarfmo)
									VALUES 		 ('".trim($wcod_examen)."' ,  '".$wtarifa."'	,	'*'		, '".$wconcepto."'	,	'*'		,	'".$wvactual."'	,	''	,	'".$wvanterior."'	,'".$wporcentaje."'		,	'".$wcco_exa."'	,	'facmer' ,		'".date("Y-m-d")." ".date("H:i:s")."' 	, 'facmer02' 	  	, '".date("Y-m-d")." ".date("H:i:s")."'	) ";

				odbc_do($conexUnix,$sqlinsert);
			}
			if($wparametro =='NP')
			{

				// exagex , exaniv
				$sqlinsert = " INSERT INTO  Inexa (  		exacod 				,    exanom				, 		exades			,		exaane					,			exaliq				 ,	exaact , exauad 	,			exafad						, 	exaniv		, 	exagex 	  )
									VALUES		  ( '".trim($wcod_examen)."'	, '".$wnombre_examen."' , '".$wnombre_examen."' ,  '".$wcod_anexo_examen."'		,	'".$wliquidacion_examen."'   ,	  'S'  , 'facmer04'	, '".date("Y-m-d")." ".date("H:i:s")."' , '".$wnivel."'	,	'".$wgrupo_examen."' )";


				odbc_do($conexUnix,$sqlinsert);

				/*
				$sqlinsert = " INSERT INTO Inexatar (exatarexa			 ,	exatartar		, exatartse , 	exatarcon		,exatartip	,		exatarvaa	,	exatarfec	,	exatarval			,exatarpor					,	exatarcco		,	exataruad,		exatarfad						  		,exatarumo			,exatarfmo)
									VALUES 		 ('".trim($wcod_examen)."' ,  '".$wtarifa."'	,	'*'		, '".$wconcepto."'	,	'*'		,	'".$wvactual."'	,	'".$wfec."'	,	'".$wvanterior."'	,'".$wporcentaje."'		,	'".$wcco_exa."'	,	'facmer' ,		'".date("Y-m-d")." ".date("H:i:s")."' 	, 'facmer02' 	  	, '".date("Y-m-d")." ".date("H:i:s")."'	) ";


				odbc_do($conexUnix,$sqlinsert);
				*/


			}

			echo $sqlinsert;

			odbc_close($conexUnix);
			odbc_close_all();

			break;
		}


		case "traer_valor_grupo" :
		{


			$variables = array();
			// --> Procedimiento
			$variables["SUBSTRING_INDEX(Tarcod, '-', 1)"]['combinar'] 	= false;
			$variables["SUBSTRING_INDEX(Tarcod, '-', 1)"]['valor'] 		= $wgqx;
			// --> Concepto
			$variables["SUBSTRING_INDEX(Tarcon, '-', 1)"]['combinar'] 	= false;
			$variables["SUBSTRING_INDEX(Tarcon, '-', 1)"]['valor'] 		= $wconcepto;
			// --> Tarifa
			$variables["SUBSTRING_INDEX(Tartar, '-', 1)"]['combinar'] 	= false;
			$variables["SUBSTRING_INDEX(Tartar, '-', 1)"]['valor'] 		= $wtar;
			// --> Tipo de ingreso
			$variables["Tartin"]['combinar'] 							= false;
			$variables["Tartin"]['valor'] 								= '*';
			// --> Centro de costos
			$variables["Tarcco"]['combinar'] 							= true;
			$variables["Tarcco"]['valor'] 								= $wcco;
			// --> Especialidad
			$variables["Taresp"]['combinar'] 							= false;
			$variables["Taresp"]['valor'] 								= '*';
			// --> Cobra Honorarios
			$variables['Tarhon']['combinar'] 							= false;
			$variables['Tarhon']['valor'] 								= '*';
			// --> Grupo de medicos
			$variables['Targme']['combinar'] 							= false;
			$variables['Targme']['valor'] 								= '*';
			// --> Estado
			$variables['Tarest']['combinar'] 							= false;
			$variables['Tarest']['valor'] 								= 'on';

			// --> Obtener query
			$q_tarEsp 	= generarQueryCombinado($variables, $wbasedato."_000104");
			$res_tarEsp	= mysql_query($q_tarEsp, $conex) or die("Error en el query: ".$q_tarEsp."<br>Tipo Error:".mysql_error());

			// --> Si hay tarifa
			if ($row_tarEsp = mysql_fetch_array($res_tarEsp))
			{
				// --> Validar si el procedimiento maneja tarifa de cobro o no cobro de honorarios.
				if($validarManejoTarifaPorHonorarios)
					$data['manejaTarifaConHonorarios'] = manejaTarifaPorHonorarios($row_tarEsp['id'], $cobraHonorarios);

				$q_tarEsp =  "
					SELECT Tarvac, Tarvan, Tarfec
					  FROM ".$wbasedato."_000104
					 WHERE id = '".$row_tarEsp['id']."' ";
				$res_tarEsp	= mysql_query($q_tarEsp, $conex) or die("Error en el query: ".$q_tarEsp."<br>Tipo Error:".mysql_error());
				$row_tarEsp = mysql_fetch_array($res_tarEsp);

				$wprovac 		= $row_tarEsp['Tarvac'];  	// --> Valor Actual
				$wprovan 		= $row_tarEsp['Tarvan'];  	// --> Valor Anterior
				$wprofec 		= $row_tarEsp['Tarfec'];  	// --> Fecha cambio de tarifa

				// --> Se evalua si tomo el valor anterior o el actual
				if ($wfeccar < $wprofec)
					$wvaltar = $wprovan;
				else
					$wvaltar = $wprovac;

				$wvaltar	= $wvaltar;

			}
			else
			{
				$wvaltar = "no hay valor";
			}


			echo $wvaltar;

			break;

		}

		case "traer_valor_uvr" :
		{

			$variables = array();
			// --> Procedimiento
			$variables["SUBSTRING_INDEX(Tarcod, '-', 1)"]['combinar'] 	= false;
			$variables["SUBSTRING_INDEX(Tarcod, '-', 1)"]['valor'] 		= 'UVR';
			// --> Concepto
			$variables["SUBSTRING_INDEX(Tarcon, '-', 1)"]['combinar'] 	= false;
			$variables["SUBSTRING_INDEX(Tarcon, '-', 1)"]['valor'] 		= $wconcepto;
			// --> Tarifa
			$variables["SUBSTRING_INDEX(Tartar, '-', 1)"]['combinar'] 	= false;
			$variables["SUBSTRING_INDEX(Tartar, '-', 1)"]['valor'] 		= $wtar;
			// --> Tipo de ingreso
			$variables["Tartin"]['combinar'] 							= false;
			$variables["Tartin"]['valor'] 								= '*';
			// --> Centro de costos
			$variables["Tarcco"]['combinar'] 							= true;
			$variables["Tarcco"]['valor'] 								= $wcco;
			// --> Especialidad
			$variables["Taresp"]['combinar'] 							= false;
			$variables["Taresp"]['valor'] 								= '*';
			// --> Cobra Honorarios
			$variables['Tarhon']['combinar'] 							= false;
			$variables['Tarhon']['valor'] 								= '*';
			// --> Grupo de medicos
			$variables['Targme']['combinar'] 							= false;
			$variables['Targme']['valor'] 								= '*';
			// --> Estado
			$variables['Tarest']['combinar'] 							= false;
			$variables['Tarest']['valor'] 								= 'on';

			// --> Obtener query
			$q_tarEsp 	= generarQueryCombinado($variables, $wbasedato."_000104");
			$res_tarEsp	= mysql_query($q_tarEsp, $conex) or die("Error en el query: ".$q_tarEsp."<br>Tipo Error:".mysql_error());

			// --> Si hay tarifa
			if ($row_tarEsp = mysql_fetch_array($res_tarEsp))
			{
				// --> Validar si el procedimiento maneja tarifa de cobro o no cobro de honorarios.
				if($validarManejoTarifaPorHonorarios)
					$data['manejaTarifaConHonorarios'] = manejaTarifaPorHonorarios($row_tarEsp['id'], $cobraHonorarios);

				$q_tarEsp =  "
					SELECT Tarvac, Tarvan, Tarfec
					  FROM ".$wbasedato."_000104
					 WHERE id = '".$row_tarEsp['id']."' ";
				$res_tarEsp	= mysql_query($q_tarEsp, $conex) or die("Error en el query: ".$q_tarEsp."<br>Tipo Error:".mysql_error());
				$row_tarEsp = mysql_fetch_array($res_tarEsp);

				$wprovac 		= $row_tarEsp['Tarvac'];  	// --> Valor Actual
				$wprovan 		= $row_tarEsp['Tarvan'];  	// --> Valor Anterior
				$wprofec 		= $row_tarEsp['Tarfec'];  	// --> Fecha cambio de tarifa

				// --> Se evalua si tomo el valor anterior o el actual
				if ($wfeccar < $wprofec)
					$wvaltar = $wprovan;
				else
					$wvaltar = $wprovac;

				$wvaltar	= $wvaltar;

			}
			else
			{
				$wvaltar = "no hay valor";
			}
			echo $wvaltar;

			break;
		}

		case "traer_valor_gqx" :
		{
			$query = 	" SELECT  	subcodigo , descripcion
							FROM det_selecciones
						   WHERE codigo = '010'
							 AND medico='".$wbasedato."'" ;

			$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
			$html = "<select id='select_gqx' onchange='traer_grupo_gqx()'>";
			while($row = mysql_fetch_array($err))
			{
				$html .= "<option value='".$row['subcodigo']."'>".$row['descripcion']."</option>";
			}
			$html .= "</select>";
			echo $html;
			break;
		}

		case "Cirugias_sin_mercado" :
		{
			global $wfecha;
			$arr_roles_cx = json_decode(str_replace('\\', '', $arr_roles_cx), true);
			$font_size = "0.74em";

			$fecha_actual_rep_NoLiq = date("Y-m-d");

			$proceso_auditoria_activo_sfi = consultarAliasPorAplicacion($conex, $wemp_pmla, 'proceso_auditoria_activo_sfi');
			$wbasedatoHce                 = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
			$rangocx_sin_liquidar = consultarAliasPorAplicacion($conex, $wemp_pmla, 'rangocx_sin_liquidar'); // [EJ_01]
			$rangocx_sin_liquidar = ($rangocx_sin_liquidar=='') ? 2: $rangocx_sin_liquidar;
			$rangocx_sin_liquidar = ($rangocx_sin_liquidar < 0) ? ($rangocx_sin_liquidar * (-1)): $rangocx_sin_liquidar;

			// El proceso de merados de cirugía inició en esta fecha, que se tiene en cuenta para consultar cirugías no liquidadas de días anteriores
			// pero el query cada vez se vuelve más lento por consultar tandos datos, por eso se tienen en cuenta los turnos sólo hasta x meses atrás.
			$wfecha_ini_proceso = "2015-02-16";
			$wfecha_ini_proceso = $fecha_actual_rep_NoLiq;
			$nuevafecha         = strtotime('-'.$rangocx_sin_liquidar.' month',strtotime($wfecha_ini_proceso ));
			$wfecha_ini_proceso = date('Y-m-d',$nuevafecha);

			$index_monitor_permisos = ($wmonitor == '') ? "Cirugias_sin_mercado": $wmonitor;
			$arr_permisos_opciones = unserialize(base64_decode($arr_permisos_opciones));

			if($wfechaenviada=='') { $wfecha = $wfecha; }
			else { $wfecha = $wfechaenviada; }

			// Consulta los nombres de los estados de cirugía, según los campos en det_formulario
			$arr_estados_cirugia = array();
			$sql_est = "SELECT 	df.medico, df.codigo, df.descripcion, r30.Dic_Descripcion
						FROM 	det_formulario AS df
								INNER JOIN
								root_000030 AS r30 ON (df.medico = r30.Dic_usuario AND df.codigo = r30.Dic_Formulario AND df.campo = r30.Dic_Campo)
						WHERE 	df.medico = 'tcx'
								AND df.codigo = '000011'
								AND df.descripcion IN ('Turpre','Turpan','Turpes','Turpep','Turpeq','Turper','Turpea')";
			if($result_estCx = mysql_query($sql_est,$conex))
			{
				if(mysql_num_rows($result_estCx) > 0)
				{
					while ($row_estCx = mysql_fetch_array($result_estCx))
					{
						$campoTcx11 = $row_estCx["descripcion"];
						if(!array_key_exists($campoTcx11, $arr_estados_cirugia))
						{
							$arr_estados_cirugia[$campoTcx11] = '';
						}
						$arr_estados_cirugia[$campoTcx11] = $row_estCx["Dic_Descripcion"];
					}
				}
			}

			// Hace parte de un nuevo nivel de permisos, los usuarios especificados en este campo son los que realmente tiene permisos
			// para las acciones más importantes del proceso, por ejemplo editar, devolver, anular, cerrar, abrir, además de tener obviamente activo
			// el permiso de cada columna, si tiene por ejemplo permiso de cargar mercado pero el código del usuario no aparece en el campo de administradores
			// entonces no se le mostrará la columna de cargar mercado.
			$usuario_admin_exp = $arr_permisos_opciones[$index_monitor_permisos]["usuarios_admin"];
			$usuarios_admin    = explode("-", $usuario_admin_exp);
			$user_admin        = (in_array($wuse, $usuarios_admin)) ? true:false;

			// RESTRICCIÓN DE OPCIONES POR PERMISOS DEL PERFIL O CARGO DE LA PERSONA QUE ACCEDE A LOS MONITORES
			$p_cargar                = (($arr_permisos_opciones[$index_monitor_permisos]["cargar_almacen"] == 'on' || $arr_permisos_opciones[$index_monitor_permisos]["cargar_facturador"] == 'on' ) && $user_admin) ? "": "sin_permiso";
			$p_devolver              = ($arr_permisos_opciones[$index_monitor_permisos]["devolver"] == 'on' && $user_admin) 				? "": "sin_permiso";
			$p_anular_no_liquidado   = ($arr_permisos_opciones[$index_monitor_permisos]["anular_no_liquidado"] == 'on' && $user_admin) 		? "": "sin_permiso";
			$p_cerrar_mercado        = (($arr_permisos_opciones[$index_monitor_permisos]["cerrar_mercado_facturador"] == 'on' ||  $arr_permisos_opciones[$index_monitor_permisos]["cerrar_mercado_almacen"] =='on' ) && $user_admin) ? "": "sin_permiso";
			$p_abrir_mercado		 = ($arr_permisos_opciones[$index_monitor_permisos]["abrir_mercado"] == 'on' && $user_admin) 			? "": "sin_permiso";
			$p_liquidar_cirugias     = ($arr_permisos_opciones[$index_monitor_permisos]["liquidar_cirugias"] == 'on' && $user_admin) 		? "": "sin_permiso";
			// $p_anular_liquidado_unix = ($arr_permisos_opciones[$index_monitor_permisos]["anular_liquidado_unix"] == 'on') 	? "": "sin_permiso";
			$p_liquidar_en_unix      = 'sin_permiso'; //($arr_permisos_opciones[$index_monitor_permisos]["liquidar_en_unix"] == 'on') 		? "": "sin_permiso";
			$p_consultar_insumos     = ($arr_permisos_opciones[$index_monitor_permisos]["liquidar_en_unix"] == 'on') 		? "": "sin_permiso";
			$p_consultar_tarifa      = ($arr_permisos_opciones[$index_monitor_permisos]["consultar_tarifa"] == 'on') 		? "": "sin_permiso";
			$consultar_insumos       = ($arr_permisos_opciones[$index_monitor_permisos]["liquidar_en_unix"] == 'on') ? true : false;
			$consultar_tarifa        = ($arr_permisos_opciones[$index_monitor_permisos]["consultar_tarifa"] == 'on') ? true : false;

			// se establece el perfil del que carga el mercado ya que puede ser una persona de almacen o un grabado de cirugia.
			// si los dos estan en on primara el perfil del almacen.
			$perfil_carga_mercado ='';
			if($arr_permisos_opciones[$index_monitor_permisos]["cargar_facturador"] == 'on' )
			{
					$perfil_carga_mercado	= "facturador";
			}

			if($arr_permisos_opciones[$index_monitor_permisos]["cargar_almacen"] == 'on' )
			{
					$perfil_carga_mercado	= "almacen";
			}

			// se establece el perfil del que cierra el mercado ya que puede ser una persona de almacen o un grabado de cirugia.
			// si los dos estan en on primará el perfil del almacen.

			$perfil_cierra_mercado ='';
			if($arr_permisos_opciones[$index_monitor_permisos]["cerrar_mercado_facturador"] == 'on' )
			{
					$perfil_cierra_mercado	= "facturador";
			}

			if($arr_permisos_opciones[$index_monitor_permisos]["cerrar_mercado_almacen"] == 'on' )
			{
					$perfil_cierra_mercado	= "almacen";
			}

			//Permiso especial para los del almacen liquidar  cirugias en horarios especiales
			if($p_liquidar_cirugias == "sin_permiso" && $arr_permisos_opciones[$index_monitor_permisos]["liquidar_cirugias_horario_especial"] == 'on')
			{
				//$p_liquidar_cirugias = "";
				$nombre_dia = date("l");
				$hora_dia 	= date("G");
				$fecha_dia  = date("Y-m-d");

				if(strtolower($nombre_dia) == "sunday")
				{
					$p_liquidar_cirugias = "";
				}
				if($hora_dia  >= 18 || $hora_dia  <= 8)
				{
					$p_liquidar_cirugias = "";
				}

				$query = "SELECT Fecha
							FROM root_000063
						   WHERE Fecha = '".$fecha_dia."'";
				$res = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());

				if($row = mysql_fetch_array($res))
				{
					if($row['Fecha'] == $fecha_dia)
					{
						$p_liquidar_cirugias = "";
					}
				}
			}
			//$p_liquidar_cirugias="";

			$wbasetcx                = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tcx');
			$codigoempresaparticular = consultarAliasPorAplicacion($conex, $wemp_pmla, 'codigoempresaparticular');
			$wbasedatomovhos         = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

			// [EJ_01] Para el monitor de cirugías no liquidadas de días anteriores se crea tabla temporar para filtrar solo los turnos que están pendientes de
			// ser liquidados, se hacen dos tablas temporales porque en la consulta de días anteriores se esta usando un unión y por cada consulta se debe
			// cruzar la tabla temporal y mysql saca error, es por eso que se duplica la tabla temporar y poder usar ambar tablas en el unión
			$idx_tablejoin1 = "";
			$idx_tablejoin2 = "";
			if($wactual !='on')
			{
				// $sqltmp11    = "DROP TABLE IF EXISTS join1_tcx_no_liq_";
				// $resultTmp11 = mysql_query($sqltmp11, $conex) or die("Error en el query: ".$sqltmp11."<br>Tipo Error:".mysql_error());
				// $sqltmp11    = "DROP TABLE IF EXISTS join2_tcx_no_liq_";
				// $resultTmp11 = mysql_query($sqltmp11, $conex) or die("Error en el query: ".$sqltmp11."<br>Tipo Error:".mysql_error());

				$consulta_fecha = " tcx11.Turfec < '{$fecha_actual_rep_NoLiq}' AND tcx11.Turfec > '{$wfecha_ini_proceso}' ";
				$idx_table = str_replace(":", "_",date("H:i:s"));
				$idx_tablejoin1 = "join1_tcx_no_liq_".$idx_table;
				crearTemporalTurnosSinLiquidar($conex, $wbasedato, $idx_tablejoin1, '', $fecha_actual_rep_NoLiq, $wfecha_ini_proceso, $proceso_auditoria_activo_sfi);

				$idx_tablejoin2 = "join2_tcx_no_liq_".$idx_table;
				crearTemporalTurnosSinLiquidar($conex, $wbasedato, $idx_tablejoin1, $idx_tablejoin2, $fecha_actual_rep_NoLiq, $wfecha_ini_proceso, $proceso_auditoria_activo_sfi);
	        }

			// El siguiente query consulta
			// 1. Las historias o documentos que no tienen mercado,
			// 2. Los turnos que ya tienen mercado sin liquidar,
			// 3. Los turnos que ya tienen mercado liquidado,
			// 4. Los insumos que ya estan cerrados y sin liquidar
			//
			// 5. El resultado de los union se cruza con la tabla tcx_7 para saber si hay algún resgistro cancelado, si es así entonces si el código de turno en tcx11 es igual al turno cancelado en tcx7
			// 		la cirugía fue cancelada, esto se puede validar más adelante en el código php.
			// (tambien tiene en cuenta si solo se han liquidado algunos inumos del mercado sin estar el mercado liquidado completamente para poder que aparezca la opción de liquidar)

			// para la doble funcionalidad del monitor se agrega  la variable de consulta_fecha , asi se agrega dualidad al query
			// logrando que el monitor muestre las cirugias sin liquidar de dias anteriores y el estado de las cirugias actuales

	        $select_audit = ", 'off' AS paraLiquidar, 'off' AS liquidado, '' AS estado_cirugia";
	        $select_audit_anteriores = ", 'off' AS paraLiquidar, 'off' AS liquidado, '' AS estado_cirugia";
	        $from_audit = "";
	        $from_audit_anteriores = "";
	        if($proceso_auditoria_activo_sfi == 'on')
	        {
	        	$select_audit = ", clm252.Auelli AS paraLiquidar, clm252.Aueliq AS liquidado, clm252.Aueecx AS estado_cirugia, clm252.Auerau AS revisionAut ";
	        	$select_audit_anteriores = ", clm252.Auelli AS paraLiquidar, clm252.Aueliq AS liquidado, clm252.Aueecx AS estado_cirugia, clm252.Auerau AS revisionAut ";
	        	$from_audit = " LEFT JOIN
										{$wbasedato}_000252 AS clm252 ON (clm252.Auetur = tcx11.Turtur) ";
	        	$from_audit_anteriores = " LEFT JOIN
										{$wbasedato}_000252 AS clm252 ON (clm252.Auetur = tmp_NL.Turtur_tmp AND Aueliq = 'off' AND Aueman = 'off') ";
	        }

	        $ver_boton_ocultar = false;
			//$consulta_fecha = $wactual;
			if($wactual =='on')
			{
				$consulta_fecha = " tcx11.Turfec ='{$wfecha}' ";
				$query = "	SELECT 	quir.Quicco, t.tiene_insumos, t.whistoriaActiva, t.ingresoActivo, t.Turhis, t.Turnin, t.historia_207, t.ingreso_207,
									t.Turfec, t.Turdoc, t.Turnom, t.Mpapro, t.Turhin, t.cod_responsable, t.Empnom, t.Turcir, t.quirofano, t.desc_cirugias, t.egreso_cirugia, t.id_turno,
									t.cod_turno, cnl.Mcatur AS turno_cancelado,
									t.Turpre, t.Turpan, t.Turpes, t.Turpep, t.Turpeq, t.Turper, t.Turpea,
									SUM(t.cantidad_entregada) AS cantidad_entregada,
									SUM(t.cantidad_devoluciones_union) AS cantidad_devoluciones, SUM(t.liquidaciones_unix_union) AS total_liquidados_unix, SUM(t.total_insumos) AS total_codigos_insumos,
									SUM(t.total_insumos_cerrados) AS total_insumos_cerrados, t.fecha_alta_ingreso, t.Ubiald,
									SUM(t.cerrados_almacen) AS cerrados_almacen, SUM(t.cerrados_facturador) AS cerrados_facturador,
									SUM(t.grabados_almacen) AS grabados_almacen, SUM(t.grabados_facturador) AS grabados_facturador, SUM(t.insumos_aplicados) AS insumos_aplicados, SUM(t.revisados_unix) AS revisados_unix,
									MAX(t.fecha_liquidado) AS fecha_liquidado, MAX(t.hora_liquidado) AS hora_liquidado, t.aplicar_insumos_historia,
									t.paraLiquidar, t.liquidado, t.estado_cirugia, t.revisionAut,
									quir.Quiapi AS aplicar_insumos
							FROM(
								SELECT  'aaa_sin_mercado' AS tiene_insumos, tcx11.Turfec, tcx11.Turdoc, tcx11.Turnom, clm207.Mpapro, tcx11.Turhin,
										tcx11.Tureps AS cod_responsable, clm24.Empnom, tcx11.Turcir,
										tcx11.Turqui AS quirofano, tcx11.Turcir AS desc_cirugias, tcx11.Turpea AS egreso_cirugia, tcx11.id AS id_turno, tcx11.Turtur AS cod_turno,
										tcx11.Turpre AS Turpre, tcx11.Turpan AS Turpan, tcx11.Turpes AS Turpes, tcx11.Turpep AS Turpep, tcx11.Turpeq AS Turpeq, tcx11.Turper AS Turper, tcx11.Turpea AS Turpea,
										0 AS cantidad_entregada,
										0 AS cantidad_devoluciones_union, 0 AS liquidaciones_unix_union, 0 AS total_insumos, 0 AS total_insumos_cerrados,
										m18.Ubihis whistoriaActiva, m18.Ubiing AS ingresoActivo, tcx11.Turhis, tcx11.Turnin, clm207.Mpahis AS historia_207, clm207.Mpaing AS ingreso_207,
										m18.Ubifad AS fecha_alta_ingreso, m18.Ubiald,
										0 AS cerrados_almacen, 0 AS cerrados_facturador,
										0 AS grabados_almacen, 0 AS grabados_facturador, 0 AS insumos_aplicados, 0 AS revisados_unix,
										'' AS fecha_liquidado, '' AS hora_liquidado, tcx11.Turapi AS aplicar_insumos_historia,
										'off' AS paraLiquidar, 'off' AS liquidado, '' AS estado_cirugia, '' revisionAut
								FROM 	{$wbasetcx}_000011 AS tcx11
										LEFT JOIN
										{$wbasedato}_000207 AS clm207 ON (tcx11.Turtur=clm207.Mpatur AND clm207.Mpaest='on')
										LEFT JOIN
										{$wbasedatomovhos}_000018 AS m18 ON (m18.Ubihis = tcx11.Turhis AND m18.Ubiing = tcx11.Turnin)
										LEFT JOIN
										{$wbasedato}_000024 AS clm24 ON (tcx11.Tureps = clm24.Empcod)
								WHERE 	{$consulta_fecha}
										AND clm207.Mpatur IS NULL
										AND tcx11.Turest = 'on'
								GROUP BY tcx11.Turdoc, tcx11.Turtur, m18.Ubihis, m18.Ubiing

								UNION

								SELECT  'bbb_con_mercado' AS tiene_insumos, tcx11.Turfec, tcx11.Turdoc, tcx11.Turnom, clm207.Mpapro, tcx11.Turhin,
										tcx11.Tureps AS cod_responsable, clm24.Empnom, tcx11.Turcir,
										tcx11.Turqui AS quirofano, tcx11.Turcir AS desc_cirugias, tcx11.Turpea AS egreso_cirugia, tcx11.id AS id_turno, tcx11.Turtur AS cod_turno,
										tcx11.Turpre AS Turpre, tcx11.Turpan AS Turpan, tcx11.Turpes AS Turpes, tcx11.Turpep AS Turpep, tcx11.Turpeq AS Turpeq, tcx11.Turper AS Turper, tcx11.Turpea AS Turpea,
										SUM(clm207.Mpacan) AS cantidad_entregada,
										SUM(clm207.Mpadev) AS cantidad_devoluciones_union, 0 AS liquidaciones_unix_union, COUNT(clm207.Mpacom) AS total_insumos, 0 AS total_insumos_cerrados,
										m18.Ubihis whistoriaActiva, m18.Ubiing AS ingresoActivo, tcx11.Turhis, tcx11.Turnin, clm207.Mpahis AS historia_207, clm207.Mpaing AS ingreso_207,
										m18.Ubifad AS fecha_alta_ingreso, m18.Ubiald,
										0 AS cerrados_almacen, 0 AS cerrados_facturador,
										SUM(clm207.Mpaela) AS grabados_almacen, SUM(clm207.Mpaefa) AS grabados_facturador, SUM(mv227.Carcap) AS insumos_aplicados, 0 AS revisados_unix,
										'' AS fecha_liquidado, '' AS hora_liquidado, tcx11.Turapi AS aplicar_insumos_historia
										{$select_audit}
								FROM 	{$wbasetcx}_000011 AS tcx11
										LEFT JOIN
										{$wbasedato}_000207 AS clm207 ON (tcx11.Turtur=clm207.Mpatur AND clm207.Mpaliq ='off' AND clm207.Mpaest='on')
										LEFT JOIN
										{$wbasedatomovhos}_000018 AS m18 ON (m18.Ubihis = tcx11.Turhis AND m18.Ubiing = tcx11.Turnin)
										LEFT JOIN
										{$wbasedato}_000024 AS clm24 ON (tcx11.Tureps = clm24.Empcod)
										LEFT JOIN
										{$wbasedatomovhos}_000227 AS mv227 ON (mv227.Carbot = '1016' AND mv227.Cartur = clm207.Mpatur AND clm207.Mpacom = mv227.Carins)
										{$from_audit}
								WHERE 	{$consulta_fecha}
										AND clm207.Mpatur IS NOT NULL
										AND tcx11.Turest = 'on'
								GROUP BY tcx11.Turdoc, tcx11.Turtur, m18.Ubihis, m18.Ubiing

								UNION

								SELECT  'bbb_con_mercado' AS tiene_insumos, tcx11.Turfec, tcx11.Turdoc, tcx11.Turnom, clm207.Mpapro, tcx11.Turhin,
										tcx11.Tureps AS cod_responsable, clm24.Empnom, tcx11.Turcir,
										tcx11.Turqui AS quirofano, tcx11.Turcir AS desc_cirugias, tcx11.Turpea AS egreso_cirugia, tcx11.id AS id_turno, tcx11.Turtur AS cod_turno,
										tcx11.Turpre AS Turpre, tcx11.Turpan AS Turpan, tcx11.Turpes AS Turpes, tcx11.Turpep AS Turpep, tcx11.Turpeq AS Turpeq, tcx11.Turper AS Turper, tcx11.Turpea AS Turpea,
										0 AS cantidad_entregada,
										0 AS cantidad_devoluciones_union, COUNT(clm207.Mpalux) AS liquidaciones_unix_union, COUNT(clm207.Mpacom) AS total_insumos, COUNT(clm207.Mpacrm) AS total_insumos_cerrados,
										m18.Ubihis whistoriaActiva, m18.Ubiing AS ingresoActivo, tcx11.Turhis, tcx11.Turnin, clm207.Mpahis AS historia_207, clm207.Mpaing AS ingreso_207,
										m18.Ubifad AS fecha_alta_ingreso, m18.Ubiald,
										SUM(clm207.Mpacal) AS cerrados_almacen, SUM(clm207.Mpacfa) AS cerrados_facturador,
										SUM(clm207.Mpaela) AS grabados_almacen, SUM(clm207.Mpaefa) AS grabados_facturador, SUM(mv227.Carcap) AS insumos_aplicados, COUNT(c106.id) AS revisados_unix,
										c198.Fecha_data AS fecha_liquidado, c198.Hora_data AS hora_liquidado, tcx11.Turapi AS aplicar_insumos_historia
										{$select_audit}
								FROM 	{$wbasetcx}_000011 AS tcx11
										LEFT JOIN
										{$wbasedato}_000207 AS clm207 ON (tcx11.Turtur=clm207.Mpatur AND clm207.Mpaliq ='on' AND clm207.Mpaest='on')
										LEFT JOIN
										{$wbasedatomovhos}_000018 AS m18 ON (m18.Ubihis = tcx11.Turhis AND m18.Ubiing = tcx11.Turnin)
										LEFT JOIN
										{$wbasedato}_000024 AS clm24 ON (tcx11.Tureps = clm24.Empcod)
										LEFT JOIN
										{$wbasedato}_000198 AS c198 ON (clm207.Mpatur = c198.Liqtur AND clm207.Mpacom = c198.Liqdll AND clm207.Mpaest = 'on' AND c198.Liqest = 'on')
										LEFT JOIN
										{$wbasedato}_000106 AS c106 ON (c198.Liqidc = c106.id AND c106.Tcarest = 'on'  AND c106.Tcaraun = 'on')
										LEFT JOIN
										{$wbasedatomovhos}_000227 AS mv227 ON (mv227.Carbot = '1016' AND mv227.Cartur = clm207.Mpatur AND clm207.Mpacom = mv227.Carins)
										{$from_audit}
								WHERE 	{$consulta_fecha}
										AND clm207.Mpatur IS NOT NULL
										AND tcx11.Turest = 'on'
										AND clm207.Mpalux = 'on'
								GROUP BY tcx11.Turdoc, tcx11.Turtur, m18.Ubihis, m18.Ubiing

								UNION

								SELECT  'bbb_con_mercado' AS tiene_insumos, tcx11.Turfec, tcx11.Turdoc, tcx11.Turnom, clm207.Mpapro, tcx11.Turhin,
										tcx11.Tureps AS cod_responsable, clm24.Empnom, tcx11.Turcir,
										tcx11.Turqui AS quirofano, tcx11.Turcir AS desc_cirugias, tcx11.Turpea AS egreso_cirugia, tcx11.id AS id_turno, tcx11.Turtur AS cod_turno,
										tcx11.Turpre AS Turpre, tcx11.Turpan AS Turpan, tcx11.Turpes AS Turpes, tcx11.Turpep AS Turpep, tcx11.Turpeq AS Turpeq, tcx11.Turper AS Turper, tcx11.Turpea AS Turpea,
										0 AS cantidad_entregada,
										0 AS cantidad_devoluciones_union, 0 AS liquidaciones_unix_union, 0 AS total_insumos, COUNT(clm207.Mpacrm) AS total_insumos_cerrados,
										m18.Ubihis whistoriaActiva, m18.Ubiing AS ingresoActivo, tcx11.Turhis, tcx11.Turnin, clm207.Mpahis AS historia_207, clm207.Mpaing AS ingreso_207,
										m18.Ubifad AS fecha_alta_ingreso, m18.Ubiald,
										SUM(clm207.Mpacal) AS cerrados_almacen, SUM(clm207.Mpacfa) AS cerrados_facturador,
										0 AS grabados_almacen, 0 AS grabados_facturador, SUM(mv227.Carcap) AS insumos_aplicados, 0 AS revisados_unix,
										'' AS fecha_liquidado, '' AS hora_liquidado, tcx11.Turapi AS aplicar_insumos_historia
										{$select_audit}
								FROM 	{$wbasetcx}_000011 AS tcx11
										LEFT JOIN
										{$wbasedato}_000207 AS clm207 ON (tcx11.Turtur=clm207.Mpatur AND clm207.Mpaliq ='off' AND clm207.Mpaest='on')
										LEFT JOIN
										{$wbasedatomovhos}_000018 AS m18 ON (m18.Ubihis = tcx11.Turhis AND m18.Ubiing = tcx11.Turnin)
										LEFT JOIN
										{$wbasedato}_000024 AS clm24 ON (tcx11.Tureps = clm24.Empcod)
										LEFT JOIN
										{$wbasedatomovhos}_000227 AS mv227 ON (mv227.Carbot = '1016' AND mv227.Cartur = clm207.Mpatur AND clm207.Mpacom = mv227.Carins)
										{$from_audit}
								WHERE 	{$consulta_fecha}
										AND clm207.Mpatur IS NOT NULL
										AND tcx11.Turest = 'on'
										AND clm207.Mpacrm = 'on'
								GROUP BY tcx11.Turdoc, tcx11.Turtur, m18.Ubihis, m18.Ubiing
							) AS t
							LEFT JOIN
							{$wbasetcx}_000007 AS cnl ON (cnl.Mcatur = t.cod_turno)
							LEFT JOIN
							{$wbasetcx}_000012 AS quir ON (quir.Quicod = t.quirofano)
							GROUP BY  t.tiene_insumos, t.Turdoc, t.cod_turno, t.whistoriaActiva, t.ingresoActivo, t.quirofano
							ORDER BY quir.Quicco, t.tiene_insumos, t.Turhin, t.Turnom";
			}
			else
			{
				if($perfil_carga_mercado == "facturador")
				{
					$ver_boton_ocultar = true;
				}
				// [EJ_01]Se mejora la respuesta de este query cruzandolo con la tabla temporal de turnos sin liquidar, de esta menara el indice a la tabla tcx_11 funciona mejor
				//adicionalmente se creó un parámetro en root_51 para que no consulte casi todo el historial de turnos para buscar turnos sin liquidar sino que se fija un
				//ranto de busqueda desde el día actual hacia atras, por ejemplo 1, 2, 3 meses o los que se quieran según indique el parámetro "rangocx_sin_liquidar"

				/**
				 * 2. Los turnos que ya tienen mercado sin liquidar,
				 * 4. Los insumos que ya estan cerrados
				 */
				$consulta_fecha = " tcx11.Turfec < '{$fecha_actual_rep_NoLiq}'  AND tcx11.Turfec > '{$wfecha_ini_proceso}' ";
				$query = "	SELECT 	quir.Quicco, t.tiene_insumos, t.whistoriaActiva, t.ingresoActivo, t.Turhis, t.Turnin, t.historia_207, t.ingreso_207,
									t.Turfec, t.Turdoc, t.Turnom, t.Mpapro, t.Turhin, t.cod_responsable, t.Empnom, t.Turcir, t.quirofano, t.desc_cirugias, t.egreso_cirugia, t.id_turno,
									t.cod_turno, cnl.Mcatur AS turno_cancelado,
									t.Turpre, t.Turpan, t.Turpes, t.Turpep, t.Turpeq, t.Turper, t.Turpea,
									SUM(t.cantidad_entregada) AS cantidad_entregada,
									SUM(t.cantidad_devoluciones_union) AS cantidad_devoluciones, SUM(t.liquidaciones_unix_union) AS total_liquidados_unix, SUM(t.total_insumos) AS total_codigos_insumos,
									SUM(t.total_insumos_cerrados) AS total_insumos_cerrados, t.fecha_alta_ingreso, t.Ubiald,
									SUM(t.cerrados_almacen) AS cerrados_almacen, SUM(t.cerrados_facturador) AS cerrados_facturador,
									SUM(t.grabados_almacen) AS grabados_almacen, SUM(t.grabados_facturador) AS grabados_facturador, SUM(t.insumos_aplicados) AS insumos_aplicados, 0 AS revisados_unix,
									'' AS fecha_liquidado, '' AS hora_liquidado, '' AS aplicar_insumos_historia,
									t.paraLiquidar, t.liquidado, t.estado_cirugia, t.revisionAut,
									quir.Quiapi AS aplicar_insumos
							FROM(
								SELECT  'bbb_con_mercado' AS tiene_insumos, tcx11.Turfec, tcx11.Turdoc, tcx11.Turnom, clm207.Mpapro, tcx11.Turhin,
										tcx11.Tureps AS cod_responsable, clm24.Empnom, tcx11.Turcir,
										tcx11.Turqui AS quirofano, tcx11.Turcir AS desc_cirugias, tcx11.Turpea AS egreso_cirugia, tcx11.id AS id_turno, tcx11.Turtur AS cod_turno,
										tcx11.Turpre AS Turpre, tcx11.Turpan AS Turpan, tcx11.Turpes AS Turpes, tcx11.Turpep AS Turpep, tcx11.Turpeq AS Turpeq, tcx11.Turper AS Turper, tcx11.Turpea AS Turpea,
										SUM(clm207.Mpacan) AS cantidad_entregada,
										SUM(clm207.Mpadev) AS cantidad_devoluciones_union, 0 AS liquidaciones_unix_union, COUNT(clm207.Mpacom) AS total_insumos, 0 AS total_insumos_cerrados,
										m18.Ubihis whistoriaActiva, m18.Ubiing AS ingresoActivo, tcx11.Turhis, tcx11.Turnin, clm207.Mpahis AS historia_207, clm207.Mpaing AS ingreso_207,
										m18.Ubifad AS fecha_alta_ingreso, m18.Ubiald,
										SUM(clm207.Mpacal) AS cerrados_almacen, SUM(clm207.Mpacfa) AS cerrados_facturador,
										SUM(clm207.Mpaela) AS grabados_almacen, SUM(clm207.Mpaefa) AS grabados_facturador, SUM(mv227.Carcap) AS insumos_aplicados
										{$select_audit_anteriores}
								FROM 	{$idx_tablejoin1} AS tmp_NL
								        INNER JOIN
										{$wbasetcx}_000011 AS tcx11 on (tcx11.Turtur = tmp_NL.Turtur_tmp)
										LEFT JOIN
										{$wbasedato}_000207 AS clm207 ON (tcx11.Turtur=clm207.Mpatur AND clm207.Mpaliq ='off' AND clm207.Mpaest='on')
										LEFT JOIN
										{$wbasedatomovhos}_000018 AS m18 ON (m18.Ubihis = tcx11.Turhis AND m18.Ubiing = tcx11.Turnin)
										LEFT JOIN
										{$wbasedato}_000024 AS clm24 ON (tcx11.Tureps = clm24.Empcod)
										LEFT JOIN
										{$wbasedatomovhos}_000227 AS mv227 ON (mv227.Carbot = '1016' AND mv227.Cartur = clm207.Mpatur AND clm207.Mpacom = mv227.Carins)
										{$from_audit_anteriores}
								WHERE 	{$consulta_fecha}
										AND clm207.Mpatur IS NOT NULL
										AND tcx11.Turest = 'on'
										AND (clm207.Mpacan - clm207.Mpadev) > 0
								GROUP BY tcx11.Turdoc, tcx11.Turtur, m18.Ubihis, m18.Ubiing

								UNION

								SELECT  'bbb_con_mercado' AS tiene_insumos, tcx11.Turfec, tcx11.Turdoc, tcx11.Turnom, clm207.Mpapro, tcx11.Turhin,
										tcx11.Tureps AS cod_responsable, clm24.Empnom, tcx11.Turcir,
										tcx11.Turqui AS quirofano, tcx11.Turcir AS desc_cirugias, tcx11.Turpea AS egreso_cirugia, tcx11.id AS id_turno, tcx11.Turtur AS cod_turno,
										tcx11.Turpre AS Turpre, tcx11.Turpan AS Turpan, tcx11.Turpes AS Turpes, tcx11.Turpep AS Turpep, tcx11.Turpeq AS Turpeq, tcx11.Turper AS Turper, tcx11.Turpea AS Turpea,
										0 AS cantidad_entregada,
										0 AS cantidad_devoluciones_union, 0 AS liquidaciones_unix_union, 0 AS total_insumos, COUNT(clm207.Mpacrm) AS total_insumos_cerrados,
										m18.Ubihis whistoriaActiva, m18.Ubiing AS ingresoActivo, tcx11.Turhis, tcx11.Turnin, clm207.Mpahis AS historia_207, clm207.Mpaing AS ingreso_207,
										m18.Ubifad AS fecha_alta_ingreso, m18.Ubiald,
										SUM(clm207.Mpacal) AS cerrados_almacen, SUM(clm207.Mpacfa) AS cerrados_facturador,
										SUM(clm207.Mpaela) AS grabados_almacen, SUM(clm207.Mpaefa) AS grabados_facturador, SUM(mv227.Carcap) AS insumos_aplicados
										{$select_audit_anteriores}
								FROM 	{$idx_tablejoin2} AS tmp_NL
								        INNER JOIN
										{$wbasetcx}_000011 AS tcx11 on (tcx11.Turtur = tmp_NL.Turtur_tmp)
										LEFT JOIN
										{$wbasedato}_000207 AS clm207 ON (tcx11.Turtur=clm207.Mpatur AND clm207.Mpaliq ='off' AND clm207.Mpaest='on')
										LEFT JOIN
										{$wbasedatomovhos}_000018 AS m18 ON (m18.Ubihis = tcx11.Turhis AND m18.Ubiing = tcx11.Turnin)
										LEFT JOIN
										{$wbasedato}_000024 AS clm24 ON (tcx11.Tureps = clm24.Empcod)
										LEFT JOIN
										{$wbasedatomovhos}_000227 AS mv227 ON (mv227.Carbot = '1016' AND mv227.Cartur = clm207.Mpatur AND clm207.Mpacom = mv227.Carins)
										{$from_audit_anteriores}
								WHERE 	{$consulta_fecha}
										AND clm207.Mpatur IS NOT NULL
										AND tcx11.Turest = 'on'
										AND clm207.Mpacrm = 'on'
										AND (clm207.Mpacan - clm207.Mpadev) > 0
								GROUP BY tcx11.Turdoc, tcx11.Turtur, m18.Ubihis, m18.Ubiing

								UNION

								SELECT  'ccc_sin_mercado' AS tiene_insumos, tcx11.Turfec, tcx11.Turdoc, tcx11.Turnom, '' AS Mpapro, tcx11.Turhin,
										tcx11.Tureps AS cod_responsable, clm24.Empnom, tcx11.Turcir,
										tcx11.Turqui AS quirofano, tcx11.Turcir AS desc_cirugias, tcx11.Turpea AS egreso_cirugia, tcx11.id AS id_turno, tcx11.Turtur AS cod_turno,
										tcx11.Turpre AS Turpre, tcx11.Turpan AS Turpan, tcx11.Turpes AS Turpes, tcx11.Turpep AS Turpep, tcx11.Turpeq AS Turpeq, tcx11.Turper AS Turper, tcx11.Turpea AS Turpea,
										0 AS cantidad_entregada,
										0 AS cantidad_devoluciones_union, 0 AS liquidaciones_unix_union, 0 AS total_insumos, 0 AS total_insumos_cerrados,
										m18.Ubihis whistoriaActiva, m18.Ubiing AS ingresoActivo, tcx11.Turhis, tcx11.Turnin, '' AS historia_207, '' AS ingreso_207,
										m18.Ubifad AS fecha_alta_ingreso, m18.Ubiald,
										0 AS cerrados_almacen, 0 AS cerrados_facturador,
										0 AS grabados_almacen, 0 AS grabados_facturador, 0 AS insumos_aplicados
										, 'off' AS paraLiquidar, 'off' AS liquidado, '' AS estado_cirugia, '' revisionAut
								FROM 	{$wbasetcx}_000011 AS tcx11
										INNER JOIN
										{$wbasedato}_000252 clm252 ON (clm252.Auetur = tcx11.Turtur AND clm252.Aueliq = 'off' AND clm252.Aueman = 'off')
										LEFT JOIN
										{$wbasedato}_000207 AS clm207 ON (tcx11.Turtur=clm207.Mpatur AND clm207.Mpaest='on')
										LEFT JOIN
										{$wbasedatomovhos}_000018 AS m18 ON (m18.Ubihis = tcx11.Turhis AND m18.Ubiing = tcx11.Turnin)
										LEFT JOIN
										{$wbasedato}_000024 AS clm24 ON (tcx11.Tureps = clm24.Empcod)
								WHERE 	{$consulta_fecha}
										AND clm207.Mpatur IS NULL
										AND tcx11.Turest = 'on'
								GROUP BY tcx11.Turdoc, tcx11.Turtur, m18.Ubihis, m18.Ubiing
							) AS t
							LEFT JOIN
							{$wbasetcx}_000007 AS cnl ON (cnl.Mcatur = t.cod_turno)
							LEFT JOIN
							{$wbasetcx}_000012 AS quir ON (quir.Quicod = t.quirofano)
							GROUP BY  t.tiene_insumos, t.Turdoc, t.cod_turno, t.whistoriaActiva, t.ingresoActivo, t.quirofano
							ORDER BY quir.Quicco, t.Turfec, t.tiene_insumos, t.Turhin, t.Turnom";
			}

			// Estos LEFT trae historia de pacientes que estan en tcx_11 pero que puede no estar activo un ingreso en root_36 o root_37, en ese caso es mejor mostrar el paciente sin historia ni ingreso
			// $query = "			{$wbasetcx}_000011 AS tcx11
			// 						LEFT JOIN
			// 						{$wbasedato}_000207 AS clm207 ON (tcx11.Turdoc = clm207.Mpadoc AND clm207.Mpaliq ='off' AND clm207.Mpaest='on')
			// 						LEFT JOIN
			// 						root_000036 AS r36 ON (tcx11.Turdoc = r36.Pacced)
			// 						LEFT JOIN
			// 						root_000037 AS r37 ON (r37.Oriced = r36.Pacced AND r37.Oritid = r36.Pactid AND r37.Oriori = '{$wemp_pmla}')
			// 						LEFT JOIN
			// 						{$wbasedato}_000024 AS clm24 ON (tcx11.Tureps = clm24.Empcod)";
			// echo "<pre>".print_r($query,true)."</pre>";

			$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
			$numfilas = mysql_num_rows($err);

			// [EJ_01] Si se crearon tablas temporales para el monitor de turnos sin liquidar entonces se hace un DROP a esas tablas.
			if($wactual !='on')
			{
				$sqltmp11 = "DROP TABLE IF EXISTS {$idx_tablejoin1}";
	    		$resultTmp11 = mysql_query($sqltmp11, $conex) or die("Error en el query: ".$sqltmp11."<br>Tipo Error:".mysql_error());

				$sqltmp11 = "DROP TABLE IF EXISTS {$idx_tablejoin2}";
	    		$resultTmp11 = mysql_query($sqltmp11, $conex) or die("Error en el query: ".$sqltmp11."<br>Tipo Error:".mysql_error());
	    	}


			$html = '<div style="display:none;"><pre>'.print_r($query,true).'</pre></div><input type="hidden" id="fechaocultacirugia" value="'.$wfecha.'" >';

			if($wactual =='on')
			{
						$html .='   <table cellpadding="0" cellspacing="0" >
										<tr>
											<td class="encabezadoTabla">Fecha de cirug&iacute;as: </td>
											<td class="fila1" id="td_campo_fecha"> <input type="text" id="fechas_cirugias" value="'.$wfecha.'"> </td>
											<td class="fila1">&nbsp;&nbsp;&nbsp;</td>
											<td class="fila1"><span style="cursor:pointer;font-size:9pt;color:#B45F04;text-decoration: underline;" onclick="abrirVentanaPlatillaMercados();">[Ver plantillas de mercados]<span></td>
										</tr>
								    </table>';
			}
			$html .='<table align="center" id="" style="width:100%">
							<tr>
								<td colspan="8" class="encabezadoTabla">
									Filtrar listado:<input id="id_search_cirugias_sin_mercado'.$wmonitor.'" type="text" value="" name="id_search_cirugias_sin_mercado'.$wmonitor.'"> | <span onmouseover="trOverDel(this);" onmouseout="trOutDel(this);" onclick="actualizarMonitorMercados(this);" style="cursor:pointer;">Actualizar <img width="14px" height="14px" title="Actualizar listado." src="../../images/medical/sgc/Refresh-128.png"></span> | <span class="roundspn">&nbsp;</span> Requieren aplicaci&oacute;n
								</td>
								<td class="encabezadoTabla" colspan="7">N&uacute;mero de registros: '.$numfilas.'</td>
							</tr>
						</table>
						<table align="center" id="Cirugias_sin_mercado_table'.$wmonitor.'">
							<tr class="encabezadoTabla" style="font-size:'.$font_size.';">';

			if($wactual =='off')
			{
				$html.='<td style="text-align:center;">&nbsp;</td><td>Fecha</td>';
			}
			else
			{
				$html.='<td style="text-align:center;" colspan="2">&nbsp;</td>';
			}

			$html.=				'<td style="text-align:center;">
									Historia
								</td>
								<!-- <td style="text-align:center;">Ingreso</td> -->
								<td style="text-align:center;">
									Documento
								</td>';


			$html.=				'<td style="text-align:center;">Nombre</td>
								<td style="text-align:center;">Turno</td>
								<td style="text-align:center;">Quir&oacute;fano</td>
								<td style="text-align:center;">Cirug&iacute;as</td>
								<td style="text-align:center;">Responsable</td>
								<td style="text-align:center;">Estado</td>
								<td style="text-align:center;width:50px;">Egreso</td>
								<td class="'.$p_cargar.'" style="width: 75px;text-align:center;background-color:green;">Cargar<br>mercado<input id="id_search_historia_cargar" type="text" value="" name="id_search_historia_cargar" placeholder="Historia" size="6" onkeypress="validarAccionMercado(event, \'cargar\',this);"></td>
								<td class="'.$p_devolver.'" style="width: 75px;text-align:center;background-color:orange;">Devolver<br>mercado<input id="id_search_historia_devolver" type="text" value="" name="id_search_historia_devolver" placeholder="Historia" size="6" onkeypress="validarAccionMercado(event, \'devolver\',this);"></td>
								<td class="'.$p_anular_no_liquidado.'" style="width: 100px; text-align:center;">Anular mercado no liquidado</td>
								<td class="'.$p_cerrar_mercado.'" style="width: 100px; text-align:center;">Cerrar<br>mercado</td>
								<td class="'.$p_abrir_mercado.'" style="width: 100px; text-align:center;">Abrir<br>mercado</td>
								<td class="" style="width: 58px; text-align:center;">Mercado Almacen</td>
								<td class="" style="width: 75px; text-align:center;">Mercado Facturaci&oacute;n</td>
								<td class="'.$p_consultar_insumos.'" style="width: 100px; text-align:center;">Consultar insumos</td>
								<td class="'.$p_liquidar_cirugias.'" style="width: 100px; text-align:center;">Liquidaci&oacute;n Cirug&iacute;a</td>
								<td class="'.$p_liquidar_cirugias.'" style="width: 100px; text-align:center;">Liquidar como paquete</td>
							</tr>';
			$i = 1;
			$arr_ccos =  array();

			$stilo = 'color:#000000;border-radius: 4px;border:1px solid #999999;padding:2px;';
			/*$arr_estados_procesos = array(  ""   => '<span style="'.$stilo.'">Sin revisar</span>',
											"FAC"=> '<span style="'.$stilo.'background-color:#FFFFCE;">Facturacion</span>',
											"AUT"=> '<span style="'.$stilo.'background-color:#70E57A;">Autorizaciones</span>',
											"AUD"=> '<span style="'.$stilo.'background-color:#FCCCCC;">Auditoria</span>',
											"CCX"=> '<span style="'.$stilo.'background-color:#FF993A;">Coord. cirugía</span>',
											"IDC"=> '<span style="'.$stilo.'background-color:#CEFFFF;">Auditoria IDC</span>',
											"CNV"=> '<span style="'.$stilo.'background-color:#00B9FF;">Convenios</span>');*/
			$arr_estados_procesos = array(  ""   => '<span style="'.$stilo.'">Sin revisar</span>');
			foreach ($arr_roles_cx as $key_rol => $arr_value_rol) {
				$arr_estados_procesos[$key_rol] = '<span style="'.$stilo.'background-color:#'.$arr_value_rol['color'].';">'.utf8_decode($arr_value_rol['abreviado']).'</span>';
			}

			while($row = mysql_fetch_array($err))
			{
				$msj_link              = 'Cargar';
				$wcf                   = (($i%2)==0) ? "fila1": "fila2";
				$hay_mercado           = $row['total_codigos_insumos']*1;
				$paraLiquidar          = $row['paraLiquidar'];
				$hay_mercado_perfil    = 0;
				$total_liquidados_unix = $row['total_liquidados_unix']*1;
				$cerrados_almacen      = $row['cerrados_almacen']*1;
				$cerrados_facturador   = $row['cerrados_facturador']*1;
                $total_insumos_cerrados= $row['total_insumos_cerrados']*1;
				$grabados_almacen      = $row['grabados_almacen']*1;
				$grabados_facturador   = $row['grabados_facturador']*1;
				$revisados_unix        = $row['revisados_unix']*1;
				$fecha_liquidado       = $row['fecha_liquidado'];
				$hora_liquidadox       = explode(":", $row['hora_liquidado']);
				$hora_liquidado  	   = (count($hora_liquidadox) > 1) ? $hora_liquidadox[0].':'.$hora_liquidadox[1] : '';

				$cantidad_entregada    = $row['cantidad_entregada']*1;
				$cantidad_devoluciones = $row['cantidad_devoluciones']*1;
				$insumos_aplicados     = $row['insumos_aplicados']*1;
				$ajuste_valores 	   = "";//"$cantidad_entregada - ($insumos_aplicados+$cantidad_devoluciones) = ".($cantidad_entregada - ($insumos_aplicados+$cantidad_devoluciones));
				$entregado_aplicado_devuelto = $cantidad_entregada - ($insumos_aplicados+$cantidad_devoluciones);

				$wcodigoturno            = $row['cod_turno'];
				$liq_unx_todosLosCodigos = $hay_mercado - $total_liquidados_unix;
				$whistoriaAct            = (limpiarString($row['Turhis']) != '' && limpiarString($row['Turhis']) != '0') ? limpiarString($row['Turhis']) : '';
				$wingresoAct             = ($whistoriaAct != '') ? limpiarString($row['Turnin']) : '';
				$historia_cliame207      = limpiarString($row['historia_207']);
				$ingreso_cliame207       = limpiarString($row['ingreso_207']);
				$historia_r37            = limpiarString($row['Turhis']);
				$ingreso_r37             = limpiarString($row['Turnin']);
				$fecha_turno             = limpiarString($row['Turfec']);
				$Ubiald                  = limpiarString($row['Ubiald']);
				$fecha_alta_ingreso      = limpiarString($row['fecha_alta_ingreso']);
				$fecha_alta_ingreso      = ($fecha_alta_ingreso == "") ? "" : $fecha_alta_ingreso;

				$msj_liquidado           = "-Grabado-";
				$msj_sin_mercado         = "-Sin mercado-";
				$msj_liqUnx              = $msj_liquidado;
				$msj_cerrar              = "-Sin cerrar-";
				$egreso_cirugia          = ($row['egreso_cirugia'] != 'on') ? "off" : 'on';
				$classEnAlta             = ($row['egreso_cirugia'] != 'on') ? "" : 'css_paciente_alta_cx';
				$westado_pac_cirugia     = '--';
				$estado_cancelado        = ($wcodigoturno == $row['turno_cancelado']) ? 'on' : 'off';
				$alerta_liquidar         = '';

				$aplicar_insumos          = $row['aplicar_insumos'];
				$aplicar_insumos_historia = $row['aplicar_insumos_historia'];
				$op_aplicar_insumos        = ($aplicar_insumos == 'on' || $aplicar_insumos_historia == 'on') ? 1: 0;

				$total_insumos_cerrados_prefil = 0;
				$mercadoCerradoAlmacen    = false;
				$mercadoCerradoFacturador = false;

				// --> Jerson Trujillo, 2016-22-09
				$revisionAut      		= ((isset($row['revisionAut'])) ? $row['revisionAut'] : "");
				$msjRevisionAut			= "";
				switch(trim($revisionAut))
				{
					case 'NA':
					{
						$msjRevisionAut = "La entidad autoriza automáticamente";
						break;
					}
					case 'PA':
					{
						$msjRevisionAut = "Procedimiento no se autoriza/audita";
						break;
					}
					case 'EP':
					{
						$msjRevisionAut = "El procedimiento esta excepto de ser auditado";
						break;
					}
					case 'EM':
					{
						$msjRevisionAut = "Las cx del médico tienen excepción de ser auditadas";
						break;
					}
				}

				if($msjRevisionAut != '')
					$imgRevisionAutomatica 	= "<img style='cursor:help' tooltip3='si' title='<span style=font-weight:normal;><b>Motivo por el que no se audit&oacute;:</b><br>".utf8_decode($msjRevisionAut)."</span>' width='15' height='15' src='../../images/medical/sgc/info_black.png'>";
				else
					$imgRevisionAutomatica 	= "";

				if($arr_permisos_opciones[$index_monitor_permisos]["permiteReversarCx"] == "on")
					$imgReversarCx = "<img style='cursor:pointer' tooltip3='si' onclick='reversarCxAlMonitorAud(\"".$wcodigoturno."\", \"".$revisionAut."\")' title='<span style=font-weight:normal;><b>Reversar cx</b></span>' width='15' height='15' src='../../images/medical/sgc/Refresh-128.png'>";
				else
					$imgReversarCx = "";

				$estado_cirugia           = (array_key_exists($row['estado_cirugia'], $arr_estados_procesos)) ? $arr_estados_procesos[$row['estado_cirugia']]: '<span style="'.$stilo.'background-color:red;">'.$row['estado_cirugia'].'</span>';
				if($hay_mercado == 0)
				{
					$estado_cirugia = '<span style="color:#000000;'.$stilo.'background-color:#ffffff;">Sin mercado</span>';
				}

				if($perfil_cierra_mercado == "almacen")
				{
					$total_insumos_cerrados_prefil = $cerrados_almacen;
					$hay_mercado_perfil = $row['grabados_almacen']*1;
				}
				elseif($perfil_cierra_mercado == "facturador")
				{
					$total_insumos_cerrados_prefil = $cerrados_facturador;
					$hay_mercado_perfil = $row['grabados_facturador']*1;
				}


				$msjMercadoAlmacen = '<span class="resalto_rojo">Abierto</span>';
				$msjMercadoFact    = '<span class="resalto_rojo">Abierto</span>';

				if( $cerrados_almacen > 0 && $cerrados_almacen == ($row['grabados_almacen']*1))
				{
					$mercadoCerradoAlmacen    = true;
					$msjMercadoAlmacen = '<span style="color:#FF8000;">Cerrado</span>';
				}
				elseif($cerrados_almacen == 0 && $cerrados_almacen == ($row['grabados_almacen']*1))
				{
					$mercadoCerradoAlmacen    = true;
					$msjMercadoAlmacen = '<span style="color:#FF8000;">Sin mercado</span>';
				}

				if( $cerrados_facturador > 0 && $cerrados_facturador == ($row['grabados_facturador']*1))
				{
					$mercadoCerradoFacturador = true;
					$msjMercadoFact    = '<span style="color:#4B8A08;">Cerrado</span>';
				}
				elseif($cerrados_facturador == 0 && $cerrados_facturador == ($row['grabados_facturador']*1))
				{
					$mercadoCerradoFacturador = true;
					$msjMercadoFact    = '<span style="color:#4B8A08;">Sin mercado</span>';
				}


				/*if(empty($whistoriaAct) && $hay_mercado*1 > 0 && $egreso_cirugia == 'on' && $historia_cliame207 != '' && $historia_cliame207*1 == $historia_r37*1)
				{
					$whistoriaAct = $historia_cliame207;
					$wingresoAct  = $ingreso_cliame207;
				}
				elseif(empty($whistoriaAct) && $hay_mercado*1 > 0 && $egreso_cirugia == 'on' && $historia_cliame207 != '' && $liq_unx_todosLosCodigos > 0)
				{
					// Para algunos casos como por ejemplo, se hizo mercado, no se ha liquidado, se fue el paciente y el paciente hizo un nuevo ingreso
					// entonces el mercado anterior no esta apareciendo con historia porque la historia de root_37 no concuerda con la de cliame_207
					// Verificar si esta modificación no genera otro caso extraño cuando no esté apareciendo una historia e ingreso.
					$whistoriaAct = $historia_cliame207;
					$wingresoAct  = $ingreso_cliame207;
				}*/

				// Hay registros que estan apareciendo con historia e ingreso pero en movhos_18 estan inactivos, al momento de grabar un mercado estan quedando con esa historia e ingreso
				// pero cuando se crea el nuevo ingreso activo el mercado no va a aparecer porque en cliame_207 ya quedó con el ingreso anterior.
				// En esta sección de código se va a validar que si el registro tiene historia e ingreso, ese ingreso tiene fecha de alta definitiva y la fecha del turno es mayor a la fecha
				// del alta definitiva, entonces ese registro se debe asumir como sin historia e ingreso, se hace este condicional tal como se acaba de describir para que no se vea afectado
				// el monitor para las cirugías de días anteriores que obviamente si van a tener fecha de alta definitiva y el ingreso estará inactivo.
				$fecha_1 = ($fecha_alta_ingreso != '') ? str_replace("-", "", $fecha_alta_ingreso) : $fecha_alta_ingreso;
				$fecha_2 = ($fecha_turno != '') ? str_replace("-", "", $fecha_turno) : $fecha_turno;
				if(($whistoriaAct != "" && $wingresoAct != "") && $Ubiald == 'on' && $fecha_alta_ingreso != "" && ($fecha_2 * 1) > ($fecha_1 * 1))
				{
					// $whistoriaAct = "";
					// $wingresoAct  = "";
				}

				// Estados del paciente en cirugía, solo debería estar un estado en ON al tiempo.
				$westado_pac_cirugia = ($row['Turpre'] == 'on' && array_key_exists('Turpre', $arr_estados_cirugia)) ? $arr_estados_cirugia['Turpre']: $westado_pac_cirugia;
				$westado_pac_cirugia = ($row['Turpan'] == 'on' && array_key_exists('Turpan', $arr_estados_cirugia)) ? $arr_estados_cirugia['Turpan']: $westado_pac_cirugia;
				$westado_pac_cirugia = ($row['Turpes'] == 'on' && array_key_exists('Turpes', $arr_estados_cirugia)) ? $arr_estados_cirugia['Turpes']: $westado_pac_cirugia;
				$westado_pac_cirugia = ($row['Turpep'] == 'on' && array_key_exists('Turpep', $arr_estados_cirugia)) ? $arr_estados_cirugia['Turpep']: $westado_pac_cirugia;
				$westado_pac_cirugia = ($row['Turpeq'] == 'on' && array_key_exists('Turpeq', $arr_estados_cirugia)) ? $arr_estados_cirugia['Turpeq']: $westado_pac_cirugia;
				$westado_pac_cirugia = ($row['Turper'] == 'on' && array_key_exists('Turper', $arr_estados_cirugia)) ? $arr_estados_cirugia['Turper']: $westado_pac_cirugia;
				$westado_pac_cirugia = ($row['Turpea'] == 'on' && array_key_exists('Turpea', $arr_estados_cirugia)) ? $arr_estados_cirugia['Turpea']: $westado_pac_cirugia;
				$westado_pac_cirugia = ($estado_cancelado == 'on') ? 'Cancelado' : $westado_pac_cirugia;

				// RESTRICCIÓN DE OPCIONES POR EVENTOS EN EL SISTEMA (mercado ya liquidado, no hay mercado, mercado sin liquidar, entre otras opciones)

				// Si el mercado esta cerrado y no tiene permisos de liquidar entonces no puede cargar, devolver, anular no liquidado.
				$permiso_carga_devol_anulNoLiq = true;
				// esto lo comentaria
				// if($p_liquidar_cirugias == 'sin_permiso')
				// {
					// $permiso_carga_devol_anulNoLiq = false;
				// }
				// elseif($total_insumos_cerrados_prefil > 0 && $p_liquidar_cirugias == '') // porque va a depender de liquidar cirugias
				// {
					// $permiso_carga_devol_anulNoLiq = false;
				// }

				if($total_insumos_cerrados_prefil > 0)
				{
					$permiso_carga_devol_anulNoLiq = false;
				}

				if( $total_liquidados_unix > 0)
				{
					$msj_liquidado = '-Grabado-';
					$msjMercadoFact    = '<span style="color:#4B8A08;font-weight:bold;">Liquidado</span>';
				}
				else if($hay_mercado > 0 )
				{
					$msj_liquidado = '-Sin Liquidar-';
				}

				// ***** OPCION ANULAR NO LIQUIDADO *****
				// El anular solo debe salir si no hay devolución, si ya hay alguna devolución es porque ya se hizo la cirugía,
				// aunque hay que tener en cuenta que si ya se hizo la cirugía y no se devolvió ni un solo insumo, la opción de anular
				// se podrá ver
				$activo_anular_noLiquidado = "sin_permiso";
				if($row['tiene_insumos'] == 'bbb_con_mercado' && $permiso_carga_devol_anulNoLiq)
				{
					$wcf = 'cs_mercado';
					$msj_link = 'Editar';

					if($liq_unx_todosLosCodigos > 0 && $total_liquidados_unix == 0) //($row['cantidad_devoluciones']*1) == 0 &&
					{
						$activo_anular_noLiquidado = "";
					}
				}
				elseif($row['tiene_insumos'] == 'bbb_con_mercado')
				{
					$wcf = 'cs_mercado';
				}

				// ***** OPCION GRABAR *****
				// Si hay mercado y ya se liquidó en unix no se puede cargar mercado
				$activo_grabar = (($hay_mercado > 0 && $liq_unx_todosLosCodigos == 0) || $total_liquidados_unix > 0 || !$permiso_carga_devol_anulNoLiq) ? 'sin_permiso': '';

				// ***** OPCION DEVOLVER *****
				// Si hay mercado y ya se liquidó en unix no se puede devolver mercado
				$activo_devolver = (($hay_mercado > 0 && $liq_unx_todosLosCodigos == 0) || $total_liquidados_unix > 0 || $hay_mercado == 0 || !$permiso_carga_devol_anulNoLiq) ? 'sin_permiso': '';

				// ***** OPCION CERRAR MERCADO EN EL ALMACEN *****
				// No mostrar si el paciente no esta en alta definitiva, no hay mercado, tiene permisos para liquidar, ya esta cerrado el mercado
				$activo_cerrar_mercado = "";
				$mercadoCerrado = false;
				// echo "<br>//".$wcodigoturno."  total_insumos_cerrados_prefil".$total_insumos_cerrados_prefil."-----hay_mercado_perfil".$hay_mercado_perfil;
				if($hay_mercado_perfil == 0 || $liq_unx_todosLosCodigos == 0 || $whistoriaAct == '' || $total_liquidados_unix > 0 || $total_insumos_cerrados_prefil >= 0 || $egreso_cirugia != 'on')
				{
					$activo_cerrar_mercado= "sin_permiso";
					$msj_cerrar = ($whistoriaAct == '') ? 'Sin historia': $msj_cerrar;
					if($total_insumos_cerrados_prefil > 0 && $total_insumos_cerrados_prefil == $hay_mercado_perfil)
					{
						$msj_cerrar = '-Cerrado-';
						$mercadoCerrado = true;
					}
					/*else
					{
						$msj_cerrar = '-Sin insumos por cerrar-';
					}*/
					elseif($total_insumos_cerrados_prefil >= 0 && $total_insumos_cerrados_prefil < $hay_mercado_perfil && $egreso_cirugia == 'on' && $whistoriaAct != '')
					{
						$activo_cerrar_mercado = "";
					}
					else if($hay_mercado_perfil == 0 && $hay_mercado > 0 && $total_insumos_cerrados == $hay_mercado)
					{
						$msj_cerrar = '-Cerrado-';
						$mercadoCerrado = true;
					}

					if($egreso_cirugia != 'on')
					{
						$msj_cerrar = '-Sin alta-';
					}
					else if ( $whistoriaAct == '')
					{
						$msj_cerrar = '-Sin Historia-';
					}
				}

				if ($total_insumos_cerrados == $hay_mercado)
				{
					$msj_cerrar = '-Cerrado-';
					$mercadoCerrado = true;
					$activo_cerrar_mercado= "sin_permiso";
				}

				// No se puede cerrar un mercado que aún no tiene aplicaciones ni devoluciones.
				if($op_aplicar_insumos == 1 && $perfil_carga_mercado == 'almacen' && $activo_cerrar_mercado == '' && $entregado_aplicado_devuelto != 0){
					$msj_cerrar = '<span class="resalto_rojo">-Aplicar|Devolver-</span>';
					$activo_cerrar_mercado = "sin_permiso";
				}

				// activacion de poder liquidar medicamentos
				$liquidar     = false;
				$msj_paraLiquidar = '<span style="'.$stilo.'background-color:#ffffff;width:10px;white-space: nowrap;">Mercado abierto</span>';
				$opcionLiquidarPaquete = '';
				if($hay_mercado > 0 && $total_insumos_cerrados == $hay_mercado && $p_liquidar_cirugias == '')
				{
					// Si aun no esta activo el proceso de auditoría entonces puede liquidar normalmente,
					// Si el proceso de auditoría ya esta activo entonces se debe validar que el turno ya esté paraLiquidar=on
					if($proceso_auditoria_activo_sfi == 'on')
					{
						if($paraLiquidar == 'on')
						{
							$liquidar = true;
							$opcionLiquidarPaquete = '<input type="button" value="Para autorización" onclick="devolverTurnoParaAutorizaciones(\''.$wcodigoturno.'\')" >';
						}
						else
						{
							$msj_paraLiquidar = $estado_cirugia;
							// Consultar si hay descripción operatoria. Si no hay es porque tampoco ha sido auditada.
							/*$sqlDesOpe = "	SELECT 	Fecha_data, Hora_data
											FROM 	{$wbasedatoHce}_000077
											WHERE 	movhis = '{$whistoriaAct}'
													AND moving = '{$wingresoAct}'
													AND movpro = '000077'
													AND movcon = '69'
													AND movdat = '{$wcodigoturno}'";
							$resultDescOp = mysql_query($sqlDesOpe,$conex) or die ("Error: ".mysql_errno()." - en el sqlDesOpe: ".$sqlDesOpe." - ".mysql_error());
							if(mysql_num_rows($resultDescOp) == 0)
							{
								$msj_paraLiquidar = '<span style="color:#505050;font-weight:bold;">Sin descripción operatoria</span>';
							}*/
						}
					}
					else
					{
					    	$liquidar = true;
					}
				}
				elseif($hay_mercado == 0)
				{
					$fecha_actual_ = (str_replace("-", "", $fecha_actual_rep_NoLiq))*1;
					$fecha_turno_  = (str_replace("-", "", $fecha_turno))*1;

					$html_boton_ocultar = ($ver_boton_ocultar) ? '<button onclick="ocultarTurnoMonitor(this,\''.$wcodigoturno.'\');">Ocultar</button>': "";

					$info_cancelar = ($fecha_turno_ < $fecha_actual_) ? '<br><span style="font-style: italic;">Turno sin cancelar</span>'.$html_boton_ocultar : '';
					$msj_paraLiquidar = '<span style="'.$stilo.'background-color:#ffffff;">Sin mercado </span>'.$info_cancelar;
				}

				//echo "|>$total_insumos_cerrados * $activo_cerrar_mercado * $whistoriaAct";

				$activo_abrir = (( $hay_mercado > 0 && $liq_unx_todosLosCodigos == 0) || $total_liquidados_unix > 0 ) ? false : true;
				$abrirmercado = false;
				if($mercadoCerrado && $activo_abrir )
				{

					$abrirmercado=true;
					$msj_abrir = 'Abrir Mercado';
					$alerta_liquidar = '<div style="display: inline-block; width: 15px; height: 13px;"><img class="css_img_liquidar" width="13" height="13"  src="../../images/medical/sgc/Warning-32.png"></div>';
				}
				else if ($hay_mercado == 0 )
					$msj_abrir = '-Sin <br> Mercado-';
				else if(!$mercadoCerrado)
				{
					$msj_abrir = '-Mercado <br> Sin <br>Cerrar-';
				}
				else if(!$activo_abrir)
					$msj_abrir = 'Mercado <br> Liquidado';

				// Si hay mercado y ya esta cerrado entonces no es necesario parpadear el mensaje paciente en alta.
				if($hay_mercado > 0 && $mercadoCerrado)
				{
					$classEnAlta = "";
				}


				// ***** OPCION LIQUIDAR EN UNIX *****
				// Si la cantidad de codigos de insumos es la misma cantidad de codigos con estado 'on' en el campo "Mpalux", significa que todo el mercado para
				// esa historia, ingreso y documento, ya fue liquidado en unix. Entonces no es necesario mostrar la opción de liquidar en unix.
				// Si el mercado completo no esta en estado cerrado tampoco puede dejar liquidar.
				$activo_liquidar_unix = "";
				if($hay_mercado == 0 || $liq_unx_todosLosCodigos == 0 || $whistoriaAct == '' || $total_liquidados_unix > 0 || $egreso_cirugia != 'on' || ($total_insumos_cerrados == 0))
				{
					$activo_liquidar_unix = "sin_permiso";
					if($whistoriaAct == '')
					{
						$msj_liqUnx = 'Sin historia';
					}
					elseif($total_insumos_cerrados_prefil == 0)
					{
						// total_insumos_cerrados_prefil: si por lo menos hay un insumo marcado como cerrado entonces ya debe considerarse que el mercado ha sido cerrado desde el almacen
						// para que el grabador de alto costo pueda continuar cargando, devolviendo o liquidando.
						$msj_liqUnx = '-Sin Cerrar-';
					}
					elseif($egreso_cirugia != 'on')
					{
						$msj_liqUnx = '-Sin alta-';
					}
				}

				$activo_consultar_insumos = "";
				if($hay_mercado == 0)
				{
					$activo_consultar_insumos = "sin_permiso";
				}

				$expl_cirugias_tm = array();
				if($proceso_auditoria_activo_sfi == 'on')
				{
					// Procedimientos que no estén rechazados
					// QUERY QUE TRAE LA DESCRIPCION OPERATORIA, EN ESTE MOMENTO ESTA MOSTRANDO LOS PROCEDIMIENTOS DEL DETALLE DE LA AUDITORIA
					// PERO DEBERIA MODIFICARSE PARA QUE MUESTRE LOS DATOS DESDE LA NUEVA DESCRIPCION DE LA AGENDA DE CIRUGIA REALIZADA POR PEDRO.
					$sqlDescCx = "	SELECT  c253.Audpro, c103.Pronom
									FROM 	{$wbasedato}_000253 AS c253
											INNER JOIN
											{$wbasedato}_000103 AS c103 ON (c103.Procod = c253.Audpro)
									WHERE 	c253.Audtur = '{$wcodigoturno}'
											AND c253.Audrec <> 'on'
									ORDER BY c103.Pronom";
					$resutlDescCx = mysql_query($sqlDescCx,$conex) or die ("Error: ".mysql_errno()." - en el sqlDescCx: ".$sqlDescCx." - ".mysql_error());
					while($row_Desc = mysql_fetch_assoc($resutlDescCx))
					{
						$cod_proDes = $row_Desc["Audpro"];
						$nomprodes  = $row_Desc["Pronom"];
						$expl_cirugias_tm [] = $cod_proDes.'-'.ucfirst(strtolower(utf8_encode($nomprodes)));
					}
				}

				// Si aún no hay detalle de auditoría entonces se lee la descripción de la agenda de cirugía
				if(count($expl_cirugias_tm) == 0)
				{
					$expl_cirugias_tm = explode("-", $row['desc_cirugias']);
				}

				$expl_cirugias = $expl_cirugias_tm;
				foreach ($expl_cirugias_tm as $key_idx => $cxs_noms) {
					$cxs_noms = limpiarString($cxs_noms);
					if($cxs_noms == '') { unset($expl_cirugias[$key_idx]); }
					else
					{
						// $background_colors = array('#0000FF', '#0B6121', '#610B21', '#61380B', '#4C0B5F');
						// $rand_background = $background_colors[array_rand($background_colors,1)];
						$expl_cirugias[$key_idx] = '<li>'.ucfirst(strtolower(($cxs_noms))).'</li>';
					}
				}

				$nombre_responsable = utf8_encode($row['Empnom']);
				$codigo_resposanble_cx = limpiarString($row['cod_responsable']);
				if($codigoempresaparticular == $codigo_resposanble_cx || $codigoempresaparticular == '0'.$codigo_resposanble_cx)
				{
					$nombre_responsable = "PARTICULAR";
				}

				$turnom             = ucwords(strtolower(($row['Turnom'])));
				$nombre_responsable = ucwords(strtolower($nombre_responsable));
				$quirofano          = utf8_encode($row['quirofano']);
				$hora_ini           = utf8_encode($row['Turhin']);


				if(!array_key_exists($row['Quicco'] , $arr_ccos))
				{
					$sqlInfCco = "SELECT Ccocod, Cconom
									FROM {$wbasedatomovhos}_000011
								   WHERE Ccocod = '{$row['Quicco']}'
									 AND Ccoest = 'on'
								   ";
					$resInfCco = mysql_query($sqlInfCco, $conex ) or die("<b>ERROR EN QUERY MATRIX:</b><br>".mysql_error());
					$descCco = "";
					if(mysql_num_rows($resInfCco) > 0)
					{
						$rowInfCco = mysql_fetch_array($resInfCco);
						$descCco = $rowInfCco['Cconom'];
					}

					$arr_ccos[$row['Quicco']] = $row['Quicco'];

					$html .='<tr class="class_toggle_cco" style="font-weight:bold;font-size:'.$font_size.'; background-color: #83D8F7; cursor: pointer; height: 30px;" onclick="verOcultarTrCco(\'class_toggle_'.$row['Quicco'].''.$wmonitor.'\');" >
								<td class="" colspan="11" >'.$row['Quicco'].'-'.utf8_encode($descCco).'</td>
								<td class="'.$p_cargar.'"  >&nbsp;</td>
								<td class="'.$p_devolver.'"  >&nbsp;</td>
								<td class="'.$p_anular_no_liquidado.'" >&nbsp;</td>
								<td class="'.$p_cerrar_mercado.'" >&nbsp;</td>
								<td class="'.$p_abrir_mercado.'" >&nbsp;</td>
								<td class="" >&nbsp;</td>
								<td class="" >&nbsp;</td>
								<td class="'.$p_consultar_insumos.'" >&nbsp;</td>
								<td class="'.$p_liquidar_cirugias.'" >&nbsp;</td>
								<td class="'.$p_liquidar_cirugias.'" >&nbsp;</td>
							</tr>';
				}

				$html .='<tr id="tr_turno_'.$wcodigoturno.'" op_aplicar_insumos="'.$op_aplicar_insumos.'" class="'.$row['tiene_insumos'].' '.$wcf.' find class_toggle_'.$row['Quicco'].''.$wmonitor.'" style="font-size:'.$font_size.';" onmouseover="trOverSp(this);" onmouseout="trOutSp(this);">';


				if($wactual =='off')
				{

					$html.='<td class="" >'.$i.'</td><td>'.$row['Turfec'].'</td>';
				}
				else
				{
					$html.='<td class="" colspan="2" >'.$i.'</td>';
				}

				// Si tiene activo el permiso para consultar el mercado con tarifas entonces se permita consultar si ya está cerrado el mercado
				$link_consultar = "";
				$solLink = '<span class="link_opcion function_exec" onclick="liquidarMercadoUnix(\''.$row['Turdoc'].'\', \''.$turnom.'\', \''.$whistoriaAct.'\' , \''.$wingresoAct.'\', \''.$quirofano.'\', \''.$wcodigoturno.'\', \''.$fecha_turno.'\', \''.$wmonitor.'\', \'on\',\''.$row['Quicco'].'\');" title="Consultar" >Consultar</span>';
				if($consultar_tarifa && $mercadoCerradoAlmacen) // $consultar_insumos
				{
					$link_consultar = $solLink;
				}
				elseif($consultar_tarifa && !$mercadoCerradoAlmacen)
				{
					$link_consultar = '<span class="">Sin cerrar almacen</span>';
				}
				elseif(!$consultar_tarifa)
				{
					$link_consultar = $solLink;
				}

				$msj_linkLiquidacion     = "Liquidación cirugía";
				$fechHor_liquidado       = "";
				$msj_linkLiquidacionPqte = "Liquidar como PAQUETE"; // Liquidar como paquete, si usan esta opción, al abrir el programa de liquidación solo se permitirá liquidar como paquete.
				$btn_actualizar          = "";
				$bg_clr                  = "";
				if($total_liquidados_unix > 0)
				{
					$msj_linkLiquidacionPqte = '';
					if($revisados_unix > 0)
					{
						$msj_linkLiquidacion = 'Liquidado y verificado en unix';
					}
					else
					{
						$bg_clr = "#addd7f";
						$msj_linkLiquidacion = 'Liquidado PENDIENTE de verificar en unix';
						$btn_actualizar = '<input type="button" value="Recargar" onclick="recargarLista();">';
					}

					if($fecha_liquidado != '')
					{
						$fechHor_liquidado = '<span style="font-size:0.9em;background-color:#f2f2f2;">Liquidado: '.$fecha_liquidado.' '.$hora_liquidado.'</span>';
					}
				}

				$para_aplicacion_insumo = '';
				if($op_aplicar_insumos == 1){
					$para_aplicacion_insumo = '<span class="roundspn">&nbsp;</span> ';
				}

				$html.=		'<td class="spn_resaltado" style="fond-weight:bold;">'.$whistoriaAct.'-'.$wingresoAct.'</td>
							<td class="spn_resaltado" >'.$row['Turdoc'].'</td>
							<td>'.$turnom.'</td>
							<td class="spn_resaltado" >'.$wcodigoturno.'</td>
							<td style="text-align:right;" class="" nowrap="nowrap">'.$quirofano.' ['.$hora_ini.']</td>
							<td class="desc_turno_'.$wcodigoturno.'"><ol style="padding: 0px; margin: 0px;margin-left: 16px;">'.implode($expl_cirugias).'</ol></td>
							<td>'.$codigo_resposanble_cx.'-'.$nombre_responsable.'</td>
							<td style="text-align:center;" ><div style="min-height: 52px; min-width: 52px;"><span class="'.$classEnAlta.'" >'.utf8_encode($westado_pac_cirugia).'</span></div></td>
							<td style="text-align:center;" >'.(($egreso_cirugia == 'on') ? "Si": "No").'</td>
							<td style="text-align:center;" class="'.$p_cargar.' td_hitoria_cargar_'.$whistoriaAct.'" wcodigoturno="'.$wcodigoturno.'" historia="'.$whistoriaAct.'" ingreso="'.$wingresoAct.'" documento="'.$row['Turdoc'].'" >
								'.$para_aplicacion_insumo.((empty($activo_grabar)) ? '<span class="link_opcion function_exec" onclick="cargar_mercado(\''.$row['Turdoc'].'\', \''.$row['Turnom'].'\', \''.utf8_encode($row['Turcir']).'\', \''.$whistoriaAct.'\' ,\''.$wingresoAct.'\',\''.$wcodigoturno.'\', \''.$perfil_carga_mercado.'\', \''.$wmonitor.'\' , \''.$row['Quicco'].'\' ); " title="'.$msj_link.'" >'.$msj_link.'</span>': $msj_liquidado).'
							</td>
							<td style="text-align:center;" class="'.$p_devolver.' td_hitoria_devolver_'.$whistoriaAct.'" wcodigoturno="'.$wcodigoturno.'" historia="'.$whistoriaAct.'" ingreso="'.$wingresoAct.'" documento="'.$row['Turdoc'].'" >
								'.((empty($activo_devolver)) ? '<span class="link_opcion function_exec" onclick="devolver_mercado(\''.$row['Turdoc'].'\', \''.$turnom.'\', \''.$whistoriaAct.'\' , \''.$wingresoAct.'\', \''.$wcodigoturno.'\', \''.$wmonitor.'\'); " style="cursor:pointer" title="Devolver">Devolver</span>': (($hay_mercado == 0 ) ? '-Sin Mercado-': $msj_liquidado)).'
							</td>
							<td style="text-align:center;" class="'.$p_anular_no_liquidado.'" >
								'.((empty($activo_anular_noLiquidado)) ? '<span class="link_opcion function_exec" onclick="anular_mercado(\''.$row['Turdoc'].'\', \''.$wcodigoturno.'\')" style="cursor:pointer">Anular mercado no liquidado</span>': (($hay_mercado == 0 ) ? '-Sin Mercado-': $msj_liquidado)).'
							</td>
							<td style="text-align:center;" class="'.$p_cerrar_mercado.'" >
								'.((empty($activo_cerrar_mercado)) ? '<span class="link_opcion function_exec" onclick="cerrarMercado(\''.$whistoriaAct.'\',\''.$wingresoAct.'\',\''.$row['Turdoc'].'\', \''.$wcodigoturno.'\',\''.$perfil_cierra_mercado.'\');" title="Cerrar mercado">Cerrar mercado</span>': (($hay_mercado == 0 ) ? '-Sin Mercado-': $msj_cerrar)).'
							</td>
							<td style="text-align:center;" class="'.$p_abrir_mercado.'" >
								'.$ajuste_valores.(($abrirmercado) ? '<span class="link_opcion function_exec" onclick="abrirMercado(\''.$whistoriaAct.'\',\''.$wingresoAct.'\',\''.$row['Turdoc'].'\', \''.$wcodigoturno.'\');" title="'.$msj_abrir.'" >'.$msj_abrir.' </span>': $msj_abrir).'
							</td>
							<td style="text-align:center;" >'.$msjMercadoAlmacen.'</td>
							<td style="text-align:center;" >'.$msjMercadoFact.'</td>
							<td style="text-align:center;" class="'.$p_consultar_insumos.'" >
								'.((empty($activo_consultar_insumos)) ? $link_consultar: '-Sin Mercado-').'
							</td>
							<td style="text-align:center;'.$bg_clr.'" class="'.$p_liquidar_cirugias.'" >
								'.(($liquidar) ? $alerta_liquidar.'<span class="link_opcion function_exec" onclick="abrir_programaMonitorCirugiasDia(\''.$row['Turdoc'].'\', \''.$whistoriaAct.'\' , \''.$wingresoAct.'\', \''.$wcodigoturno.'\',\''.$wcodigoturno.'\',\'\');" title="'.utf8_decode($msj_linkLiquidacion).'" >'.utf8_decode($msj_linkLiquidacion).'</span>'.$btn_actualizar.'<br>'.$imgRevisionAutomatica."<br>".$imgReversarCx.''.$fechHor_liquidado: $msj_paraLiquidar).'
							</td>
							<td style="text-align:center;'.$bg_clr.'" class="'.$p_liquidar_cirugias.'" >
								'.(($liquidar) ? '<span class="link_opcion function_exec" onclick="abrir_programaMonitorCirugiasDia(\''.$row['Turdoc'].'\', \''.$whistoriaAct.'\' , \''.$wingresoAct.'\', \''.$wcodigoturno.'\',\''.$wcodigoturno.'\',\'monitores\');" title="'.$msj_linkLiquidacionPqte.'" >'.$msj_linkLiquidacionPqte.'</span>': $msj_paraLiquidar).'
							</td>
						</tr>';
						// <td>".$row2['Ubialp']."</td>
				$i++;
			}

			$div_cero = "";
			if($numfilas == 0)
			{
				$div_cero = '<div width="100%" style="text-align:center;font-size:14;font-weight:bold;background-color:#bfbfbf;">NO HAY CIRUGÍAS PENDIENTES</div>';
			}
			$html .="</table>".$div_cero;
			echo $html;
			break;
		}

		case "actualizacion_cron_cargos":
		{
			$data = array("mensaje"=>"","error"=>0,"html"=>"");
			if($hay_unix)
			{
				include_once("root/comun.php");
				include_once("root/kron_maestro_unix.php");
				$ejCron = new datosDeUnix();
				$ejCron->actualizarMedicamentos();
			}
			echo json_encode($data);
			break;
		}

		case "Cirugias_con_mercado_y_no_liquidadas" :
		{
			$wbasetcx				= consultarAliasPorAplicacion($conex, $wemp_pmla, 'tcx');
			$wbasedatomovhos		= consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

			$query2= "	  SELECT  	Turfec  , Turdoc, Turnom, Mpapro, 	Turhin,Pronom , Orihis,Oriing, Ubialp, Empnom
							FROM 	".$wbasetcx."_000011  LEFT JOIN  root_000037 ON (Turdoc = Oriced ) LEFT JOIN  ".$wbasedatomovhos."_000018  ON (Orihis = Ubihis AND Oriing = Ubiing   AND    Ubialp = 'on'   ) , ".$wbasedato."_000207, ".$wbasedato."_000103, ".$wbasedato."_000024
						   WHERE 	Turfec =  '".date("Y-m-d")."'
						     AND    Turtur = Mpatur
							 AND    Tureps = Empcod
							 AND 	Mpaliq = 'off'
							 AND    Mpaest = 'on'
							 AND    Mpapro = Procod
						   GROUP BY Turdoc,Mpapro
						   ORDER BY Turhin, Turnom";



			$err2 = mysql_query($query2,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query2." - ".mysql_error());
			$numfilas = mysql_num_rows($err2);


			$html ="<br><br>
						<table align='center'>
							<tr>
								<td colspan='7'></td><td class='encabezadoTabla' colspan='3'>Numero de registros : ".$numfilas."</td>
							</tr>
							<tr class='encabezadoTabla'>
								<td>Historia</td>
								<td>Ing</td>
								<td>Documento</td>
								<td>Nombre</td>
								<td>Procedimiento</td>
								<td>Responsable</td>
								<!-- <td></td>
								<td></td> -->
								<td>Alta en proceso</td>
								<td>Liquidar</td>
							</tr>";
			$i = 0;
			while($row2 = mysql_fetch_array($err2))
			{

				if (($i%2)==0)
					$wcf="fila1";  // color de fondo de la fila
				else
					$wcf="fila2"; // color de fondo de la fila


				$html .= '	<tr class="'.$wcf.'" style="font-size:12px">
								<td>'.$row2['Orihis'].'</td>
								<td>'.$row2['Oriing'].'</td>
								<td>'.$row2['Turdoc'].'</td>
								<td>'.substr(utf8_encode($row2['Turnom']),0,20).'</td>
								<td>'.substr(utf8_encode($row2['Pronom']),0,30).'</td>
								<td>'.utf8_encode($row2['Empnom']).'</td>
								<!--
								<td><a onclick="anular_mercado(\''.$row2['Turdoc'].'\');" style="cursor:pointer">Anular</a></td>
								<td><a onclick="devolver_mercado(\''.$row2['Turdoc'].'\' , \''.utf8_encode($row2['Turnom']).'\', \''.$row2['Orihis'].'\' , \''.$row2['Oriing'].'\'); " style="cursor:pointer">Devolver mercado</a></td>
								-->
								<td>'.$row2['Ubialp'].'</td>
								<td><span style="cursor:pointer" onclick="abrir_programa(\''.$row2['Orihis'].'\' , \''.$row2['Oriing'].'\' )">Liquidar</span></td>
							</tr>';
			}

			$html .="</table>";

			echo $html;
			break;
		}

		case "cirugias_con_mercado_dias_anteriores" :
		{

			$wbasetcx				= consultarAliasPorAplicacion($conex, $wemp_pmla, 'tcx');
			$wbasedatomovhos		= consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

			$query = "SELECT  	Turfec  , Turdoc, Turnom, Mpapro, 	Turhin, Orihis, Oriing, Pronom, Empnom
						FROM 	".$wbasetcx."_000011 INNER JOIN ".$wbasedato."_000207 ON (Turtur = Mpatur AND Mpaliq ='off' AND Mpaest='on') LEFT JOIN  root_000037 ON (Turdoc = Oriced) , ".$wbasedato."_000103, ".$wbasedato."_000024
					   WHERE 	Turfec < '".date("Y-m-d")."'
					     AND    Mpapro = Procod
						 AND    Tureps = Empcod
					   GROUP BY Turdoc
					   ORDER BY Turhin";


			$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
			$numfilas = mysql_num_rows($err);



			$html = "      <table align='center'>
							<tr><td colspan='5'></td><td class='encabezadoTabla' colspan='3'>Numero de registros: ".$numfilas."</td></tr>
							<tr class='encabezadoTabla'>
							<td>Historia</td>
							<td>Ingreso</td>
							<td>Documento</td>
							<td>Nombre</td>
							<td>Procedimiento</td>
							<td>Responsable</td>
							<!-- <td></td> -->
							<td>Alta en proceso</td>
							</tr>";
			$i = 0;
			while($row = mysql_fetch_array($err))
			{

				if (($i%2)==0)
						$wcf="fila1";  // color de fondo de la fila
				else
						$wcf="fila2"; // color de fondo de la fila

				$html .="<tr class='".$wcf."'>
							<td>".$row['Orihis']."</td>
							<td>".$row['Oriing']."</td>
							<td>".$row['Turdoc']."</td>
							<td>".substr(utf8_encode($row['Turnom']),0,20)."</td>
							<td>".substr(utf8_encode($row['Pronom']),0,20)."</td>
							<td>".substr(utf8_encode($row['Empnom']),0,20)."</td>
							<!-- <td><a onclick='anular_mercado(".$row['Turdoc'].")' style='cursor:pointer'>Anular mercado</a></td> -->
							<td></td>
						</tr>";

				$i++;

			}
			if($i==0)
			{
				$html .="<tr><td colspan='3'>No hay datos para mostrar</td></tr>";
			}

			$html .="</table>";

			echo $html;


			break;


		}

		case "CirugiasConCargosPendientesTarifa" :
		{
			$query2=	"        SELECT  ".$wbasedato."_000231.Fecha_data ,  Grudes ,	Tcarhis,  	Tcarres, Tcarval, 	Tcaring  , 	Tcarcon , Tcartfa,  	Tcartar , ".$wbasedato."_000231.id , Procod, Pronom, Tcarval, Tcartar,Tcarpro,Tcarser,Empnom,CONCAT (Pacno1,' ' , Pacno2, ' ' , Pacap1 ) AS Nombre
								   FROM  ".$wbasedato."_000231 , ".$wbasedato."_000103, ".$wbasedato."_000200 ,  ".$wbasedato."_000024, ".$wbasedato."_000100
								  WHERE Tcarest = 'on'
								    AND Tcarrcr = 'PR'
									AND Tcarpro =  Procod
									AND Tcarcon = Grucod
									AND Tcarres = Empcod
									AND Tcarhis = Pachis";

			$err2 = mysql_query($query2,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query2." - ".mysql_error());

			$numfilas = mysql_num_rows($err2);


			$html ="<br><br>
						<table align='center'>
							<tr><td colspan= '5'></td><td colspan= '2' class='encabezadoTabla'>Numero de registros: ".$numfilas."</td></tr>
							<tr class='encabezadoTabla'>
								<td>Fecha</td>
								<td>Historia</td>
								<td>Ing</td>
								<td>Nombre</td>
								<td>Concepto</td>
								<td>Nombre Procedimiento</td>
								<td></td>
							</tr>";
			$i = 0;
			while($row2 = mysql_fetch_array($err2))
			{

				if (($i%2)==0)
					$wcf="fila1";  // color de fondo de la fila
				else
					$wcf="fila2"; // color de fondo de la fila


				$html .= "<tr class='".$wcf."'><td>".$row2['Fecha_data']."</td><td>".$row2['Tcarhis']."</td><td>".$row2['Tcaring']."</td><td>".$row2['Nombre']."</td><td>".$row2['Tcarcon']."</td><td>".$row2['Tcarpro']."-".substr(utf8_decode($row2['Pronom']),0,40)."</td><td><a style='cursor : pointer' onclick='crear_tarifa(\"".$row2['Tcarhis']."\", \"".$row2['Tcaring']."\" , \"".$row2['Tcarres']."\" ,  \"".$row2['Tcarcon']."\"		,\"".$row2['Tcarpro']."\"		, \"".$row2['Pronom']."\" , \"".$row2['Tcarser']."\", \"".$row2['Tcarval']."\" 		, \"".$row2['Tcartar']."\"	, \"".$row2['Fecha_data']."\", \"".$row2['id']."\" , \"".$row2['Empnom']."\" , \"".$row2['Tcarcon']."\" , \"".$row2['Grudes']."\" ,  \"Cirugia\" , \"\"  , \"\" , \"Cirugia\" , \"\" )'>Ir</a></td></tr>";

			}

			$html .="</table>";

			echo $html;

			break;


		}
		case 'Consulta_datos_unix' :
		{
			$conexUnix = odbc_connect('facturacion','informix','sco');
			// odbc_autocommit($conexUnix, FALSE);

			$query = "  SELECT exanom, exaane, exaliq, exagex, exaniv
						  FROM INEXA
						 WHERE exacod ='".$wcod_examen."' ";

			$err 	= odbc_do($conexUnix,$query);


			if(odbc_fetch_row($err))
			{
				$nombre = utf8_decode(odbc_result($err,1));
				$anexo = odbc_result($err,2);
				$liquidacion = odbc_result($err,3);
				$grupo_examen = odbc_result($err,4);
				$nivel = odbc_result($err,5);
			}

			$data = array();
			$data['nombre'] = $nombre;
			$data['anexo'] =  $anexo;
			$data['liquidacion'] = $liquidacion;
			$data['grupo_examen'] = $grupo_examen;
			$data['nivel'] = $nivel;
			echo json_encode($data);

			odbc_close($conexUnix);
			odbc_close_all();

			break;

		}
		case 'cargar_mercados':
		{
			$data                               = array();
			$data_mercado                       = array();
			$wperfil                            = (!isset($wperfil)) ? "": $wperfil;
			$arr_mercadoLiquidadoCerrado        = validarMercadoLiquidadoCerrado($conex, $wbasedato, $wemp_pmla, $wcodigoturno, $wperfil);
			$mercadoLiquidadoCerrado            = $arr_mercadoLiquidadoCerrado["mercadoLiquidadoCerrado"];
			actualizar_datos_mercado($whistoria ,$wingreso,$wemp_pmla,$wcedula,$wcodigoturno);
			// $data_mercado                       = traer_mercados($whistoria,$wingreso,$wemp_pmla,$wcedula, $wcodigoturno);
			$data["mercadoLiquidadoCerrado"]    = $mercadoLiquidadoCerrado;
			$data["data_mercado"]               = $data_mercado;
			echo json_encode($data);
			break;
			return;
		}

		case 'cargar_mercados_devolucion':
		{
			$data                               = array();
			$data_mercado                       = array();
			$wperfil                            = (!isset($wperfil)) ? "": $wperfil;
			$arr_mercadoLiquidadoCerrado        = validarMercadoLiquidadoCerrado($conex, $wbasedato, $wemp_pmla, $wcodigoturno, $wperfil, $consultar);
			$mercadoLiquidadoCerrado            = $arr_mercadoLiquidadoCerrado["mercadoLiquidadoCerrado"];
			actualizar_datos_mercado($whistoria ,$wingreso,$wemp_pmla,$wcedula,$wcodigoturno);
			$opcion_liquidacion_unix            = '';
			if(isset($liquidar))
			{
				$opcion_liquidacion_unix = $liquidar;
			}
			// $data_mercado = traer_mercados_devolucion($whistoria,$wingreso,$wemp_pmla,$wcedula, $opcion_liquidacion_unix, $wcodigoturno);
			$data["mercadoLiquidadoCerrado"]    = $mercadoLiquidadoCerrado;
			$data["data_mercado"]               = $data_mercado;
			echo json_encode($data);
			break;
			return;
		}

		case 'liquidar_cirugia_actualizar':
		{
			$data = array("mensaje"=>"","error"=>0,"html"=>"");
			actualizar_datos_mercado($whistoria,$wingreso,$wemp_pmla,$wcedula,$wcodigoturno);
			echo json_encode($data);
			break;
			return;
		}

		case 'pintarCupsAutorizados' :
		{
			$data = pintarCupsAutorizados($whistoria,$wingreso,$wcedula);
			echo json_encode($data);
			break;
			return;
		}

		case 'anular_mercado' :
		{
			//actualizar_datos_mercado($whistoria,$wingreso,$wemp_pmla,$wcedula,$wcodigoturno);
			$data                            = array();
			$wperfil                         = (!isset($wperfil)) ? "": $wperfil;
			$arr_mercadoLiquidadoCerrado     = validarMercadoLiquidadoCerrado($conex, $wbasedato, $wemp_pmla, $wcodigoturno, $wperfil);
			$mercadoLiquidadoCerrado         = $arr_mercadoLiquidadoCerrado["mercadoLiquidadoCerrado"];
			$data["mercadoLiquidadoCerrado"] = $mercadoLiquidadoCerrado;
			if($mercadoLiquidadoCerrado == 'off')
			{
				$data = anular_mercado($conex, $wbasedato, $wemp_pmla, $data, $wcedula, $wcodigoturno);
			}
			echo json_encode($data);
			break;
			return;
		}

		case 'validar_pss_borrar' :
		{
			$data = array("error"=>0,"html"=>"","mensaje"=>"");
			if(isset($_SESSION['user']))
			{
				$user_session = explode('-',$_SESSION['user']);
				$wuse = $user_session[1];
				$sql = "SELECT * FROM usuarios WHERE Codigo = '{$wuse}' AND Empresa='{$wemp_pmla}'";
				$result = mysql_query($sql,$conex);
				if(mysql_num_rows($result) > 0)
				{
					$row = mysql_fetch_array($result);
					// $data["Password"] = $row["Password"];
					if($row["Password"] == $validar)
					{
						$data["mensaje"] = "Verificación correcta";
					}
					else
					{
						$data["error"] = 1;
						$data["mensaje"] = "Verificación no es correcta";
					}
				}
				else
				{
					$data["error"] = 1;
					$data["mensaje"] = "Usuario no encontrado";
				}
			}
			else
			{
				$data["error"] = 1;
				$data["mensaje"] = '<div style="color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;" >
						                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
						            </div>';
			}
			echo json_encode($data);
			break;
			return;
		}

		case 'cerrarMercado' :
		{
			$data = array("error"=>0, "html"=>"", "mensaje"=>"", "mercadoLiquidadoCerrado"=>"off");
			$wperfil                         = (!isset($wperfil)) ? "": $wperfil;
			$arr_mercadoLiquidadoCerrado     = validarMercadoLiquidadoCerrado($conex, $wbasedato, $wemp_pmla, $wcodigoturno, $wperfil);
			$mercadoLiquidadoCerrado         = $arr_mercadoLiquidadoCerrado["mercadoLiquidadoCerrado"];
			$data["mercadoLiquidadoCerrado"] = $mercadoLiquidadoCerrado;
			if($mercadoLiquidadoCerrado == 'off')
			{
				$data = cerrarMercado($data, $conex, $wemp_pmla, $wbasedato, $wuse, $whistoria, $wingreso, $wcedula, $wcodigoturno,$wperfil);
			}
			echo json_encode($data);
			break;
			return;
		}

		case 'abrirMercado' :
		{
			$arr_roles_cx = json_decode(str_replace('\\', '', $arr_roles_cx), true);
			$data = array("error"=>0, "html"=>"", "mensaje"=>"", "mercadoLiquidado"=>"off");
			$wperfil                     = (!isset($wperfil)) ? "": $wperfil;
			$arr_mercadoLiquidadoCerrado = validarMercadoLiquidadoCerrado($conex, $wbasedato, $wemp_pmla, $wcodigoturno, $wperfil);
			$mercadoLiquidado            = $arr_mercadoLiquidadoCerrado["mercadoLiquidado"];
			$data["mercadoLiquidado"]    = $mercadoLiquidado;
			if($mercadoLiquidado == 'off')
			{
				$data = abrirMercado($data, $conex, $wemp_pmla, $wbasedato, $wuse, $whistoria, $wingreso, $wcedula, $wcodigoturno, $arr_roles_cx);
			}
			echo json_encode($data);
			break;
			return;
		}

		case 'Pacientes_proceso_alta_sin_estancia_liq':
		{
			$wbasedatomovhos		= consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');


			// $q = "SELECT  Ubihis , Ubiing, Enlhis
					// FROM  ".$wbasedatomovhos."_000018  LEFT JOIN  ".$wbasedato."_000199 ON (Ubihis = Enlhis  AND Ubiing = Enling AND  Enlest = 'on'	) , ".$wbasedatomovhos."_000011
				   // WHERE  Ubiald = 'on'
					 // AND  Ubifad = '".date("Y-m-d")."'
					 // AND  Ubisac = Ccocod
					 // AND  Ccocir = 'on'";

			// $err2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			// $html ="<br><br><table align='center'><tr class='encabezadoTabla'><td>Historia</td><td>Ingreso</td><td>Problema</td></tr>";
			//
			// while($row2 = mysql_fetch_array($err2))
			// {

				// if($row2['penhis'] =='')
				// {
					// if (($i%2)==0)
						// $wcf="fila1";  // color de fondo de la fila
					// else
						// $wcf="fila2"; // color de fondo de la fila


					// $html .= "<tr class='".$wcf."'><td>".$row2['Ubihis']."</td><td>".$row2['Ubiing']."</td><td>Falta Cobrar Cirugia</td></tr>";
				// }
			// }

			$q = "SELECT  Ubihis , Ubiing, penhis, Concat (Pacno1,' ' ,Pacno2, ' ' ,Pacap1,' ', Pacap2) AS Nombre , Pacdoc
					FROM  ".$wbasedatomovhos."_000018  LEFT JOIN  ".$wbasedato."_000173 ON (Ubihis = penhis  AND Ubiing = pening AND  penest = 'on'	) LEFT JOIN ".$wbasedato."_000100 ON (Ubihis=Pachis) ,  ".$wbasedatomovhos."_000011
				   WHERE  Ubialp = 'on'
					 AND  Ubifap = '".date("Y-m-d")."'
					 AND  Ubisac = Ccocod
					 AND  Ccohos = 'on'
					 AND  Ccourg = 'off' ";

			$err2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$numfilas = mysql_num_rows($err2);

			$i = 0;
			$html ="<br><br>
					<table align='center'>
					<tr><td colspan='3'></td><td class='encabezadoTabla' >Numero de registros: ".$numfilas."</td></tr>
						<tr class='encabezadoTabla'>
							<td>Historia</td>
							<td>Ingreso</td>
							<td>Documento</td>
							<td>Nombre</td>
						</tr>";

			while($row2 = mysql_fetch_array($err2))
			{

				if($row2['penhis'] =='')
				{
					if (($i%2)==0)
						$wcf="fila1";  // color de fondo de la fila
					else
						$wcf="fila2"; // color de fondo de la fila


					$html .= "<tr class='".$wcf."'><td>".$row2['Ubihis']."</td><td>".$row2['Ubiing']."</td><td>".$row2['Pacdoc']."</td><td>".$row2['Nombre']."</td></tr>";
				}
			}

			$html .="</table>";

			echo $html;

			break;
			return;

		}
		// --> Monitor de auditoria interna
		// --> 2015-12-15:
		case 'Procedimientos_no_autorizados':
		{
			$arr_roles_cx = json_decode(str_replace('\\', '', $arr_roles_cx), true);
			$arr_permisos_opciones 	= unserialize(base64_decode($arr_permisos_opciones));
			$perfilMonitorAud		= $arr_permisos_opciones['Procedimientos_no_autorizados']['perfilMonitorAudMonitorAuditoria'];
			$filtroEmpresasRevisar	= $arr_permisos_opciones['Procedimientos_no_autorizados']['filtroEmpresasRevisar'];

			echo '
			<style type="text/css">
				fieldset{
					border: 2px solid #e0e0e0;
				}
				legend{
					border: 2px solid #e0e0e0;
					border-top: 0px;
					font-family: Verdana;
					background-color: #e6e6e6;
					font-size: 10pt;
				}
				.fila1{
					background-color: #C3D9FF;
					color: #000000;
					font-size: 8pt;
					padding:1px;
					font-family: verdana;
				}
				.fila2{
					background-color: #E8EEF7;
					color: #000000;
					font-size: 8pt;
					padding:1px;
					font-family: verdana;
				}
				.encabezadoTabla{
					background-color: #2a5db0;
					color: #ffffff;
					font-size: 8pt;
					padding:1px;
					font-family: verdana;
					fond-weight: bold;
				}
				.listaProPendiente{
					border: 1px solid red;
				}
				.ui-autocomplete{
					max-width: 	290px;
					max-height: 150px;
					overflow-y: auto;
					overflow-x: hidden;
					font-size: 	7pt;
				}
			</style>';

			$ver_liquidado_tramite     = (array_key_exists($perfilMonitorAud, $arr_roles_cx)) ? $arr_roles_cx[$perfilMonitorAud]['ver_liquidado_tramite']: 'off';
			$ver_liquidado_solucionado = (array_key_exists($perfilMonitorAud, $arr_roles_cx)) ? $arr_roles_cx[$perfilMonitorAud]['ver_liquidado_solucionado']: 'off';

			$tab_ci_revisadas = '<li width="20%" id="liProAuditados"><a href="#tabProAuditados"	onclick="pintarProcedimientosAuditados()">CX REVISADAS</a></li>';

			if($ver_liquidado_tramite == 'on'){
				$tab_ci_revisadas .= '<li width="20%" id="liLiqTra"><a href="#tabLiqTram" onclick="pintarTurnosLiquidadosTramite(\'LiqTram\',\'divLiqTram\',\'\')">CX Liquidadas en tr&aacute;mite</a></li>';
			}
			if($ver_liquidado_solucionado == 'on'){
				$tab_ci_revisadas .= '<li width="20%" id="liLiqTramRev"><a href="#tabLiqTramRev" onclick="pintarTurnosLiquidadosTramite(\'LiqTramRev\',\'divLiqTramRev\',\'pendiente_revisar\')">CX Liquidadas sin revisar</a></li>';
			}
			// if(array_key_exists($perfilMonitorAud, $arr_roles_cx) && $arr_roles_cx[$perfilMonitorAud]['filtrar_busqueda_rol'] == 'off')
			// {
			// 	$tab_ci_revisadas = '';
			// }

			echo '
			<input type="hidden" id="claveMonitor" value="'.$clave.'">
			<div id="tabsMonitorAuditoria" style="margin:4px">
				<ul>
					<li width="20%" id="liProSinAuditar"><a href="#tabProSinAuditar"	onclick="pintarProcedimientosSinAuditar()">	CX PENDIENTES</a></li>
					'.$tab_ci_revisadas.'
					<li width="60%">
						<table width="100%" style="padding:6px;font-family: verdana;font-size: 10pt;color: #4C4C4C">
							<tr>
								<td style="font-weight:normal;">
									Ultima actualizaci&oacute;n:&nbsp;<span id="relojTemp" cincoMinTem="86400000"></span>&nbsp;<img width="15px" height="15px" src="../../images/medical/sgc/Clock-32.png">
								</td>
								<td style="font-weight:normal;" align="center" id="tdBotonActualizar">
								</td>
							</tr>
						</table>
					</li>
				</ul>
				<div id="tabProSinAuditar">';
			echo "
					<span style='font-family: verdana;font-size: 10pt;color: #4C4C4C'>
						<b>Buscar:</b>&nbsp;&nbsp;</b><input id='buscarProSinAudi' type='text' placeholder='Digite palabra clave' style='border-radius: 4px;border:1px solid #AFAFAF;width:150px'>&nbsp;&nbsp;|&nbsp;
						<b>Tipo de paciente:&nbsp;</b>
							<select id='tipoPaciente' style='border-radius: 4px;border:1px solid #AFAFAF;' onChange='pintarProcedimientosSinAuditar()'>
								<option value='*'>Todos</option>
								<option value='A'>Ambulatorio</option>
								<option value='H'>Hospitalario</option>
							</select>&nbsp;&nbsp;|&nbsp;";
			//if($perfilMonitorAud == 'FAC' || $perfilMonitorAud == 'CON')
			$options_roles = '<option value="%">Todos</option>';
			$td_roles = '';
			foreach ($arr_roles_cx as $key_rol => $arr_value_rol) {
				// marcar_cx_revisada
				// ver_en_convencion
				if($arr_value_rol['ver_en_convencion'] == 'on'){
					$options_roles .= '<option value="'.$key_rol.'">'.utf8_decode($arr_value_rol['nombre']).'</option>';
					$td_roles .= '<td style="background-color:#'.$arr_value_rol['color'].';border-radius: 4px;border:1px solid #999999;padding:2px;cursor:pointer;" onclick="filtrar_rol(\''.$key_rol.'\')">'.utf8_decode($arr_value_rol['abreviado']).'</td>';
				}
			}
				echo "	<b>Estado:&nbsp;</b>
							<select id='selectEstado' style='border-radius: 4px;border:1px solid #AFAFAF;' onChange='pintarProcedimientosSinAuditar()'>
								".$options_roles."
							</select>&nbsp;&nbsp;|&nbsp;";

			echo "		<b><span style='display:inline;font-size: 8pt;font-family: verdana;'>N&uacute;mero de registros:</span>&nbsp;</b><span id='numRegis'></span>&nbsp;&nbsp;|&nbsp;
						<br><b><span style='display:inline;font-size: 8pt;font-family: verdana;'>Convenci&oacute;n:</span></b>
					</span>
					<table style='display:inline;font-size: 7pt;font-family: verdana;font-weight:bold'>
						<tr style='cursor:default'>
							".$td_roles."
						</tr>
					</table>
					<div id='divProSinAuditar'>";
					$respuesta = pintarProcedimientosSinAuditar('*', '%', $arr_roles_cx);
					echo $respuesta['html'];
			echo '	</div><!-- cierra div divProSinAuditar -->
				</div><!-- cierra div tabProSinAuditar -->';
			$respuesta = pintarProcedimientosAuditados(date("Y-m-d"), '%', 'off', $arr_roles_cx);

			// if($perfilMonitorAud != 'IDC')
			// if(array_key_exists($perfilMonitorAud, $arr_roles_cx) && $arr_roles_cx[$perfilMonitorAud]['filtrar_busqueda_rol'] == 'on')
			{
				echo "
				<div id='tabProAuditados'>
					<span style='font-family: verdana;font-size: 10pt;color: #4C4C4C'>
						<b>Buscar:</b>&nbsp;&nbsp;</b><input id='buscarProAudi' type='text' placeholder=' Digite palabra clave' style='border-radius: 4px;border:1px solid #AFAFAF;width:150'>&nbsp;&nbsp;|&nbsp;
						<b>Historia:</b>&nbsp;</b>
							<input id='buscarHis' type='text' placeholder=' N de historia' style='border-radius: 4px;border:1px solid #AFAFAF;width:100'>
							<button style='cursor:pointer;' onclick='pintarProcedimientosAuditados()'><img width='13' height='13' src='../../images/medical/sgc/lupa.png'></button>
						&nbsp;&nbsp;|&nbsp;
						<b>Fecha, lista para liquidar:</b>&nbsp;&nbsp;</b><input id='fechaProAudi' type='text' disabled='disabled' value='".date("Y-m-d")."' style='border-radius: 4px;border:1px solid #AFAFAF;width:100px'>&nbsp;&nbsp;|&nbsp;
						<!--<b>Ver todas:</b>&nbsp;&nbsp;</b><input type='checkbox' id='checkVerTodas' onChange='pintarProcedimientosAuditados()'>&nbsp;&nbsp;|&nbsp;-->
						<b>N&uacute;mero de registros:&nbsp;</b><span id='numRegis2'></span>&nbsp;&nbsp;
					</span>
					<div id='divProAuditados'>
						".$respuesta['html']."
					</div><!-- cierra div divProAuditados -->
				</div><!-- cierra div tabProAuditados -->";
			}

			if($ver_liquidado_tramite == 'on'){
				echo "<div id='tabLiqTram'>
						<span style='font-family: verdana;font-size: 10pt;color: #4C4C4C'>
							<b>Buscar:</b>&nbsp;&nbsp;</b><input id='buscarLiqTram' type='text' placeholder=' Digite palabra clave' style='border-radius: 4px;border:1px solid #AFAFAF;width:150'>&nbsp;&nbsp;|&nbsp;
							<b>N&uacute;mero de registros:&nbsp;</b><span id='numRegisLiqTram'></span>&nbsp;&nbsp;
						</span>
						<div id='divLiqTram'>
						&nbsp;
						</div><!-- cierra div divLiqTram -->
					</div><!-- cierra div tabLiqTram -->";
			}
			if($ver_liquidado_solucionado == 'on'){
				echo "<div id='tabLiqTramRev'>
						<span style='font-family: verdana;font-size: 10pt;color: #4C4C4C'>
							<b>Buscar:</b>&nbsp;&nbsp;</b><input id='buscarLiqTramRev' type='text' placeholder=' Digite palabra clave' style='border-radius: 4px;border:1px solid #AFAFAF;width:150'>&nbsp;&nbsp;|&nbsp;
							<b>N&uacute;mero de registros:&nbsp;</b><span id='numRegisLiqTramRev'></span>&nbsp;&nbsp;
						</span>
						<div id='divLiqTramRev'>
						&nbsp;
						</div><!-- cierra div divLiqTramRev -->
					</div><!-- cierra div tabLiqTramRev -->";
			}

			echo '
			</div><!-- cierra div tabsMonitorAuditoria -->
			<div id="divAutorizarAuditar" style="display:none">
			</div>';
			break;
			return;
		}

		// --> Monitor de auditoria interna
		// --> 2016-09-09 Felipe Alvarez Sanchez
		case 'Empresas_paf':
		{
			echo '
			<style type="text/css">
				fieldset{
					border: 2px solid #e0e0e0;
				}
				legend{
					border: 2px solid #e0e0e0;
					border-top: 0px;
					font-family: Verdana;
					background-color: #e6e6e6;
					font-size: 10pt;
				}
				.fila1{
					background-color: #C3D9FF;
					color: #000000;
					font-size: 8pt;
					padding:1px;
					font-family: verdana;
				}
				.fila2{
					background-color: #E8EEF7;
					color: #000000;
					font-size: 8pt;
					padding:1px;
					font-family: verdana;
				}
				.encabezadoTabla{
					background-color: #2a5db0;
					color: #ffffff;
					font-size: 8pt;
					padding:1px;
					font-family: verdana;
					fond-weight: bold;
				}
				.listaProPendiente{
					border: 1px solid red;
				}
				.ui-autocomplete{
					max-width: 	290px;
					max-height: 150px;
					overflow-y: auto;
					overflow-x: hidden;
					font-size: 	7pt;
				}

				#popup_content.alert {
				background-image: url(../../../include/root/alerta_confirm.gif);
				}
			</style>';

			$wbasedato 			= consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
			$wbasedatoMovhos	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

			$arr = array();
			$select_res = "SELECT Empcod,Empnom AS Nombre
							 FROM ".$wbasedato."_000024
							WHERE Empest='on'
							ORDER BY Empnom";

			$res = 	mysql_query($select_res,$conex) or die ("Error 1: ".mysql_errno()." - en el query: ".$select_res." - ".mysql_error());
			while($row = mysql_fetch_array($res))
			{
				$arr[utf8_encode(trim($row['Empcod']))] = trim(utf8_encode($row['Nombre']));
			}
			$arr['*']	= 'TODOS';
			$arr['']	= '';
			$cuantos 	= count($arr);

			$html ='<input type="hidden" id="hidden_responsables_paf" value=\''.json_encode($arr).'\'>';

			$arr = array();
			$select_res = "SELECT Ccocod,Cconom AS Nombre
							 FROM ".$wbasedatoMovhos."_000011
							WHERE Ccoest='on'
							ORDER BY Cconom";

			$res = 	mysql_query($select_res,$conex) or die ("Error 1: ".mysql_errno()." - en el query: ".$select_res." - ".mysql_error());
			while($row = mysql_fetch_array($res))
			{
				$arr[utf8_encode(trim($row['Ccocod']))] = trim(utf8_encode($row['Nombre']));
			}
			$arr['*']	= 'TODOS';
			$arr['']	= '';
			$cuantos = count($arr);

			$html.='<input type="hidden" id="hidden_cco_paf" value=\''.json_encode($arr).'\'>';
			$html.='<input type="hidden" id="wpafprograma" value="'.$wpafprograma.'">';
			$html.='<input type="hidden" id="wpafclave" value="'.$wpafclave.'">';
			$correos = consultarAliasPorAplicacion( $conex, $wemp_pmla, "emailCambioResponsable");


			if($wcheckbox=='si' || $winicial=='si' )
			  $wchequeado='checked';
			else
			  $wchequeado='';

			// Se consultan las empresar que  pueden sufrir  cambios segun lo estipula el medico, consulta a la tabla de empresas
			$select = "SELECT Empcod, Empnit ,Empnom , Emppaf
						 FROM ".$wbasedato."_000024
						WHERE  Empest='on'";

			$res = mysql_query($select,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$select." - ".mysql_error());
			$num = mysql_num_rows($res);
			$codigosempr = array();
			$vectornits = array();
			$codigos = '';
			while($row = mysql_fetch_array($res))
			{
				if( $row['Emppaf']=='on')
				{
					$codigosempr[$row['Empcod']] = $row['Empcod'];
					$vectornits[$row['Empnit']][$row['Empcod']]=$row['Empcod']."-".$row['Empnom'];
				}
				$nit = $row['Empnit'];
				if( $row['Emppaf']=='on')
					$codigos = $codigos.",'".$row['Empcod']."'";

			}
			$codigos =substr($codigos,1);
			//-----------------------------

			//-- Miro si hay cambio de responsable para ese dia (dia actual)
			//-- Para esto armo un vector, donde voy a guardar historia e ingreso como claves
			$fechaactual =  date("Y-m-d");

			$selectResponsabledia = "SELECT Esthis, Esting, Estres
									   FROM ".$wbasedato."_000265
									  WHERE Fecha_data = '".$fechaactual."'
									  ORDER BY Hora_data ";

			$resResponsable = mysql_query($selectResponsabledia,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$selectResponsabledia." - ".mysql_error());
			$arrayresponsables = array();
			while($rowResponsable = mysql_fetch_array($resResponsable))
			{
				$arrayresponsables[$rowResponsable['Esthis']."-".$rowResponsable['Esting']] = $rowResponsable['Estres'];
			}
			//$html.='<div>'.$selectResponsabledia.'</div>';
			//-----------------------------------------

			// se hace un select para seleccionar los pacientes que  pertenecen al paf y que puede que sean atendidos por eventos , con este monitor
			// lo que se busca es cambiar de evento y paf deacuerdo a lo que estipule el medico
			$select = " SELECT Habhis,Habing,Ingent,CONCAT(Pacno1,' ',Pacno2,' ',Pacap1,' ',Pacap2) AS Nombre ,Pacdoc 	,Empnom ,Empcod,Empnit,Habcod ,Habcco,Cconom,Ccocod,Ingfei,Pactda
						  FROM ".$wbasedatoMovhos."_000020 , ".$wbasedato."_000100	, ".$wbasedato."_000101  , ".$wbasedato."_000024, ".$wbasedatoMovhos."_000011
						 WHERE  Habhis = Pachis
						   AND  Habhis = Inghis
						   AND  Habing = Ingnin
						   AND	Ingcem = Empcod
						   AND  Habcco = Ccocod
						   AND	Habest = 'on' ";

			// Filtros de empresas paf
			if($wcheckbox=='si' || $winicial=='si' )
			{
				$select .="    AND  Empcod IN (".$codigos.")";
			}
			// Filtro de centro de costos
			if ($wcco=='')
			{

			}
			else
			{
				$select .="    AND  Habcco ='".$wcco."'";
			}
			//Filtro de empresas especificas
			if ($wemp=='')
			{

			}
			else
			{
				$select .="    AND  Empcod ='".$wemp."'";
			}

			$select .=" ORDER BY Ccocod, Empnom, Nombre";

			//$html .="<div>".$select."</div>";


			$html .="<br>
					<table align='center' >
						<tr>

						<td class='fila1' nowrap='nowrap'>Buscar:&nbsp;<input type='text' id='buscadorPalabraClaveEmpresasPaf' placeholder='Digite palabra clave'></td>
						<td class='fila1' nowrap=nowrap>Empresa:";
			if($wemp =='')
			{
				$html .="<input class='buscador_autocomplete' id='buscador_responsablesPaf' type='text' placeholder='Digite empresa' value=''  ></td>";
			}
			else
			{
				$html .="<input class='buscador_autocomplete' id='buscador_responsablesPaf' type='text' placeholder='Digite empresa' value='".$wemp."-".$wnombreempresa."' valor='".$wemp."' nombre='".$wnombreempresa."' ></td>";

			}
			$html .="<td class='fila1' nowrap=nowrap>Centro de costos:&nbsp;";

			if($wcco =='')
			{
				$html .="<input type='text' class='buscador_autocomplete' placeholder='Digite centro de costos' id='buscador_ccoPaf'  value='' ></td>";
			}
			else
			{
				$html .="<input type='text' class='buscador_autocomplete' placeholder='Digite centro de costos' id='buscador_ccoPaf'  value='".$wcco."-".$wnomprecco."' valor='".$wcco."' nombre='".$wnomprecco."' ></td>";

			}
			$html .="<td class='fila1' nowrap=nowrap >Empresas Paf:&nbsp;<input id='pafcheckbox' onclick='filtrarListaPaf()' type='checkbox' ".$wchequeado."></td>
						<td style='font-weight:normal;' class='fila1' >
									Ultima actualizaci&oacute;n:&nbsp;<span id='relojTemp2' cincoMinTem='86400000'></span>&nbsp;<img width='15px' height='15px' src='../../images/medical/sgc/Clock-32.png'>
								</td>
								<td  class='fila1' style='font-weight:normal;' align='center' id='tdBotonActualizarpaf'  onclick='recargarPaf()'>
								<img width='14px' height='14px' src='../../images/medical/sgc/Refresh-128.png' title='Actualizar listado.'>
								</td>
						<td colspan='1' width='12%'>&nbsp;</td><td colspan='3' align='center' class='fila1'>Convenciones</td></tr>
						<tr><td colspan='7'></td><td colspan='3' class='fila2'>
							<div>
							<table align='center'>
								<tr>
									<td class='fila2'><img width='13' height='13' src='../../images/medical/sgc/Warning-32.png'>&nbsp;Pendiente de Cambio</td>
									<td>&nbsp;</td>
									<td class='fila2'><img width='13' height='13' src='../../images/medical/root/grabar.png'>&nbsp;Cambiado Hoy</td>

								</tr>
							</table>
							</div></td>
							</tr>
					</table>";
			$html.="
					<table align='center' id='tablePpalEmpresasPaf'>
						<tr align='center' class='encabezadoTabla'>
							<th style='display: none' ></th>
							<th>Historia</th>
							<th>Documento</th>
							<th width='20%'>Nombre</th>
							<th>Responsable</th>
							<th width='15%'>Centro de costo</th>
							<th width='5%'>Habitaci&oacuten</th>
							<th width='20%'>Cambio de Responsable</th>
							<th>Estado</th>
							<th>HCE</th>
							<th style='display : none'>Historial</th>
						</tr>";
			$res = mysql_query($select,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$select." - ".mysql_error());
			$i=0;
			while($row = mysql_fetch_array($res))
			{

				if (($i%2)==0)
					$wcf="fila1";  // color de fondo de la fila
				else
					$wcf="fila2"; // color de fondo de la fila
				//------
				$i++;
				$htmlselect ='';
				if($codigosempr[$row['Empcod']])
				{

					$habilitado = '';
					$htmlselect .="<input type='hidden' id='valAnterior_".$row['Habhis']."' value='".$row['Empcod']."'><select id='select_".$row['Habhis']."' style='width:95%' onchange='Llamarfecha(".$row['Habhis']." , ".$row['Habing'].")'>";
					if(!$arrayresponsables[$row['Habhis']."-".$row['Habing']])
					{
						$htmlselect .="<option value='' checked>Seleccione...</option>";
					}
					foreach( $vectornits[$row['Empnit']]  as $clave=>$value )
					{
						if($clave === $row['Empcod'])
						{
							//$htmlselect .= "<option value='".$clave."'  atributo='".$row['Empcod']."--".$clave."'>aa".$value."</option>";
						}
						else
						{
							if($clave==$arrayresponsables[$row['Habhis']."-".$row['Habing']])
							{
								$htmlselect .= "<option selected value='".$clave."'>".$value."</option>";
							}
							else
							{
								if(trim($clave)!=trim($row['Empcod']))
								{
									$htmlselect .= "<option value='".$clave."' atributo='".$row['Empcod']."--".$clave."'>".$value."</option>";
								}
							}
						}

					}
					$htmlselect .="<select>";
				}
				else
				{
					$habilitado = 'disabled';
				}
				//".$htmlselect."<input type='hidden' id='fechapafingreso_".$row['Habhis']."' value='".$row['Ingfei']."'>
				$html.="<tr class='".$wcf." find'><th style='display:none'>".$row['Habhis']."-".$row['Habing']." ".$row['Pacdoc']." ".$row['Nombre']." ".$row['Empcod']." ".utf8_decode($row['Empnom'])." ".$row['Ccocod']." ".$row['Cconom']." ".$row['Habcod']."</th><td>".$row['Habhis']."-".$row['Habing']."</td><td>".$row['Pacdoc']."</td><td id='tdnombrepaf_".$row['Habhis']."'>".$row['Nombre']."</td><td id='td_nombreEmpresa_".$row['Habhis']."'>".$row['Empcod']." ".utf8_decode($row['Empnom'])."</td><td>".$row['Ccocod']." ".$row['Cconom']."</td><td>".$row['Habcod']."</td><td align='center' id='td_selectpaf_".$row['Habhis']."'>".$htmlselect."<input type='hidden' id='fechapafingreso_".$row['Habhis']."' value='".$row['Ingfei']."'></td>";

				$hayCambioDia = false;
				if ($arrayresponsables[$row['Habhis']."-".$row['Habing']])
				{
						//-- Traigo el responsable de la tabla 265 si existe en el vecto arrayresponsables
						$responsable_265 = $arrayresponsables[$row['Habhis']."-".$row['Habing']];
						$hayCambioDia = true; // hayCambioDia es igual a true ;


				}
				//- Traigo el responsable de la tabla 101
				$responsable_101 = $row['Empcod'];


				//-- Estados : si el paciente se ha cambiado de responsable ese dia y en admisiones no han cambiado de responsables saldra la imagen warning32
				//--         : si el paciente se ha cambiado de responsable ese dia y en admisiones ya han cambiado de responsables saldra la imagen grabar
				if($hayCambioDia && ($responsable_265 != $responsable_101))
				{
					$html .="<td align='center' id='idimg_".$row['Habhis']."'><img width='13' height='13' src='../../images/medical/sgc/Warning-32.png'></td>";
				}
				else if($hayCambioDia && ($responsable_265 == $responsable_101))
				{
					$html .="<td align='center' id='idimg_".$row['Habhis']."'><img width='13' height='13' src='../../images/medical/root/grabar.png'></td>";
				}
				else if(!$hayCambioDia)
				{
					$html .="<td align='center' id='idimg_".$row['Habhis']."'></td>";
				}
				$html .="<td nowrap=nowrap>
							<button style='cursor:pointer;padding:1px;font-family: verdana;font-weight:bold;font-size: 8pt;' onclick='abrirHce(\"".$row['Pacdoc']."\", \"".$row['Pactda']."\", \"".$row['Habhis']."\", \"".$row['Habing']."\", \"paf\")'>Historia Cl&iacute;nica</button>
						</td>";
						//
				$html.="<td align='center' style='display : none' onclick='abrirVentanaPaf(\"".$row['Habhis']."\" , \"".$row['Habing']."\")' >Ver</td></tr>";
			}
			$html .="</table>";
			echo $html;

			break;
			return;
		}

		// --> Monitor que busca los medicamentos que son diferentes en Unix vs Matrix
		// --> 2017-05-18 Felipe Alvarez Sanchez
		case 'monitor_medicamentos':
		{
			echo '
			<style type="text/css">
				fieldset{
					border: 2px solid #e0e0e0;
				}
				legend{
					border: 2px solid #e0e0e0;
					border-top: 0px;
					font-family: Verdana;
					background-color: #e6e6e6;
					font-size: 10pt;
				}
				.fila1{
					background-color: #C3D9FF;
					color: #000000;
					font-size: 8pt;
					padding:1px;
					font-family: verdana;
				}
				.fila2{
					background-color: #E8EEF7;
					color: #000000;
					font-size: 8pt;
					padding:1px;
					font-family: verdana;
				}
				.encabezadoTabla{
					background-color: #2a5db0;
					color: #ffffff;
					font-size: 8pt;
					padding:1px;
					font-family: verdana;
					fond-weight: bold;
				}
				.listaProPendiente{
					border: 1px solid red;
				}
				.ui-autocomplete{
					max-width: 	290px;
					max-height: 150px;
					overflow-y: auto;
					overflow-x: hidden;
					font-size: 	7pt;
				}

				#popup_content.alert {
				background-image: url(../../../include/root/alerta_confirm.gif);
				}
			</style>';

			$arr = (isset($arr)) ? $arr:array();
			$wbasedato 			= consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
			$wbasedatoMovhos	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');


			$html ='<input type="hidden" id="hidden_cco_paf" value=\''.json_encode($arr).'\'>';
			$html.='<input type="hidden" id="wpafprograma" value="'.$wpafprograma.'">';
			$html.='<input type="hidden" id="wpafclave" value="'.$wpafclave.'">';

			$select2 ="SELECT  Mvucco  FROM ".$wbasedato."_000289  GROUP BY Mvucco";

			$res2 = 	mysql_query($select2,$conex) or die ("Error 1: ".mysql_errno()." - en el query: ".$select." - ".mysql_error());
			$option="<option value='*'>Todos</option>";
			while($row2 = mysql_fetch_array($res2))
			{

					$select_res = "SELECT Ccocod,Cconom AS Nombre
									FROM ".$wbasedatoMovhos."_000011
									WHERE Ccoest='on'
								 	  AND Ccocod='".$row2['Mvucco']."'";

					$res1 = 	mysql_query($select_res,$conex) or die ("Error 1: ".mysql_errno()." - en el query: ".$select_res." - ".mysql_error());
					if($row1 = mysql_fetch_array($res1))
					{
						$option.="<option value='".$row1['Ccocod']."'>".$row1['Ccocod']." - ".$row1['Nombre']."</option>";
					}
			}
			echo "<center>Diferencia de Medicamentos y Materiales Unix y Matrix  entre las fechas ".date( "Y-m-d", time()-24*3600*15 )." Y ".date( "Y-m-d", time()-24*3600*1 )."</center><br><br>";
			echo  "<b>Tipo de paciente:&nbsp;</b>
							<select id='tipoPacientemedicamentos' style='border-radius: 4px;border:1px solid #AFAFAF;' onChange='pintarMedicamentosUnixvsMatrix()'>
								<option value='*'>Todos</option>
								<option value='A'>Ambulatorio</option>
								<option value='H'>Hospitalario</option>
							</select>&nbsp;&nbsp;&nbsp;";

			echo  "<b>Centro de costos:&nbsp;</b>
					<select id='centrocostosmedicamentos' style='border-radius: 4px;border:1px solid #AFAFAF;' onChange='pintarMedicamentosUnixvsMatrix()'>".$option."</select>&nbsp;&nbsp;&nbsp;";


			$html = pintarMedicamentosUnixvsMatrix();

			echo "<br><div id='medicamentosdistintos'>".$html."</div>";

			break;
			return;
		}
		// --> Guardar observaciones
		// --> 2015-04-05, Jerson Trujillo.
		case 'enviarObservacion':
		{
			$arr_roles_cx     = json_decode(str_replace('\\', '', $arr_roles_cx), true);
			$arrObservaciones = str_replace('\\n', '<br>', $arrObservaciones);
			$arrObservaciones = str_replace('\\', '', $arrObservaciones);

			$caracter_ok 	  = array("&aacute;","&eacute;","&iacute;","&oacute;","&uacute;","&Aacute;","&Eacute;","&Iacute;","&Oacute;","&Uacute;","&ntilde;","&Ntilde;", "&deg;", "&#39;", "&iquest;", "&iexcl;", "");
			$caracter_ma 	  = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","°", "'", "¿", "¡", "´");
			$arrObservaciones = str_replace($caracter_ma, $caracter_ok, $arrObservaciones);

			// --> Guardar en la bitacora del paciente, la nueva observacion si la hay.
			$wbasedatoMovhos	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

			$arrObservaciones 	= json_decode($arrObservaciones, true);

			foreach($arrObservaciones as $clave => $infoObserv)
			{
				if(trim($infoObserv['idBitacora']) == 'NUEVO')
				{
					$sqlUltId = "
					SELECT MAX(Bitnum) AS id
					  FROM ".$wbasedatoMovhos."_000021 ";
					$resUltId = mysql_query($sqlUltId, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUltId):</b><br>".mysql_error());
					if($rowUltId = mysql_fetch_array($resUltId))
					{
						$nuevoId 				= ($rowUltId['id']*1)+1;
						$numHIsIng				= explode('-', $numHIsIng);

						$temaBitacora = (array_key_exists($perfilMonitorAud, $arr_roles_cx)) ? $arr_roles_cx[$perfilMonitorAud]["tema_bitacora"]: 'FA';

						$infoObserv['mensaje'] = str_replace('<br>', '\n', $infoObserv['mensaje']);

						$sqlInserBit = " INSERT INTO ".$wbasedatoMovhos."_000021
								(Medico,				Fecha_data,			Hora_data,			Bithis,				Biting,				Bitnum,			Bitobs,										Bitusr,			Bittem,					Seguridad		)
						VALUES 	('".$wbasedatoMovhos."','".date("Y-m-d")."','".date("H:i:s")."','".$numHIsIng[0]."','".$numHIsIng[1]."','".$nuevoId."', '".utf8_decode($infoObserv['mensaje'])."', 	'".$wuse."',	'".$temaBitacora."', 	'C-".$wuse."' 	)
						";
						mysql_query($sqlInserBit, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInserBit):</b><br>".mysql_error());

						$arrObservaciones[$clave]['idBitacora'] 	= mysql_insert_id();
					}
				}
			}

			$arrObservaciones 	= json_encode($arrObservaciones);
			// --> Actualizar la observacion en el encabezado de la cx.
			$sqlObs = "
			UPDATE ".$wbasedatoFacturacion."_000252
			   SET Aueobs = '".$arrObservaciones."'
			 WHERE Auetur = '".$idTurno."'
			";
			mysql_query($sqlObs, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlObs):</b><br>".mysql_error());

			echo $arrObservaciones;
			break;
			return;
		}
		// --> Guardar auditoria de una cx
		// --> 2015-12-21, Jerson Trujillo.
		case 'guardarAuditoria':
		{
			$arrOpcBilateral 			= array("NA"=>"No aplica", "off" => "Unilateral", "on" => "Bilateral");
			
			$arr_roles_cx     			= json_decode(str_replace('\\', '', $arr_roles_cx), true);
			$infoAuditoriaNuevos 		= json_decode(str_replace('\\', '', $infoAuditoriaNuevos), true);
			$infoAuditoria 				= json_decode(str_replace('\\', '', $infoAuditoria), true);
			$infoConvenios 				= json_decode(str_replace('\\', '', $infoConvenios), true);
			$infoAuditoriaDesdeDesOpe	= json_decode(str_replace('\\', '', $infoAuditoriaDesdeDesOpe), true);
			$numAutoInsumos 			= json_decode(str_replace('\\', '', $numAutoInsumos), true);

			// --> Actualizar el estado de la cx, en el encabezado.
			$sqlCxAudi = "
			UPDATE ".$wbasedatoFacturacion."_000252
			   SET Aueecx = '".$destino."',
				   Auetra = '".$valTramite."',
				   Aueaus = '".$autorizadaSegundoRes."',
			       Auelli = ".(($destino == 'LIQ') ? "'on'" : "'off'")."
			       ".(($destino == 'LIQ') ? ", Auefll = '".date("Y-m-d")." ".date("H:i:s")."' " : "")."
			 WHERE Auetur = '".$idTurno."'
			";
			mysql_query($sqlCxAudi, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCxAudi):</b><br>".mysql_error());

			$nombre_rol = (array_key_exists($destino, $arr_roles_cx)) ? utf8_decode($arr_roles_cx[$destino]["nombre"]): '';
			$nombre_rol = ($destino == 'LIQ') ? "liquidar": $nombre_rol;
			$descripEst = 'Enviado a '.$nombre_rol;

			if($autorizadaSegundoRes == 'on')
				$descripEst = 'Autorizada, Segundo Responsable';

			// --> Actualizar los procedimientos de la cx
			foreach($infoAuditoria as $idRegis => $infoAud)
			{				
				// --> 2019-08-14: Jerson trujillo, se guarda en log quien modificó la via y la bilateralidad
				$sqlSelectPro = "
				SELECT Audvia, Audbil, Audpro
				  FROM ".$wbasedatoFacturacion."_000253
				 WHERE id = '".$idRegis."'
				";
				$resSelectPro = mysql_query($sqlSelectPro, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlSelectPro):</b><br>".mysql_error());
				if($rowSelectPro = mysql_fetch_array($resSelectPro)){
					
					$rowSelectPro['Audvia'] = ($rowSelectPro['Audvia'] == 0) ? '' : $rowSelectPro['Audvia'];
					
					if($rowSelectPro['Audvia'] != $infoAud['via'])
						$descripEst.= "<br>(".$rowSelectPro['Audpro']."-> Se modifica la via, Valor anterior:".$rowSelectPro['Audvia']." Valor nuevo:".$infoAud['via'].")";
					
					if($rowSelectPro['Audbil'] != $infoAud['checkBilateral'])
						$descripEst.= "<br>(".$rowSelectPro['Audpro']."-> Se modifica la bilateralidad, Valor anterior:".$arrOpcBilateral[$rowSelectPro['Audbil']]." Valor nuevo:".$arrOpcBilateral[$infoAud['checkBilateral']].")";
					
				}			
				
				$infoAud['organo'] = (($infoAud['organo'] != "") ? $infoAud['organo'] : "*");

				$sqlUpdatePro = "
				UPDATE ".$wbasedatoFacturacion."_000253
				   SET Audcon = '".$infoAud['confirmado']."',
					   Audrec = '".(($infoAud['confirmado'] == 'on' || $infoAud['confirmado'] == '') ? "off" : "on")."',
					   Audnau = '".$infoAud['numAuto']."',
					   Audvia = '".$infoAud['via']."',
					   Audorg = '".$infoAud['organo']."',
					   Audbil = '".$infoAud['checkBilateral']."'
				 WHERE id     = '".$idRegis."'
				";
				mysql_query($sqlUpdatePro, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUpdatePro):</b><br>".mysql_error());

				// --> 	Se registra el usuario que ingresa el numero de autorizacion, solo la primera vez, por eso se tiene el
				//		filtro de que Auduia = '', esto con el fin de controlar de que solo el usuario que ingresó la primera vez
				//		el número de autorización, sea el unico que lo pueda modificar.
				//		Y si el numero de autorizacion es diferente al que se ingreso en admisones.
				if($infoAud['numAuto'] != '' && $infoAud['numAuto'] != $infoAud['numAutoAdmi'])
				{
					$sqlUpdateUsuIngAut = "
					UPDATE ".$wbasedatoFacturacion."_000253
					   SET Auduia = '".$wuse."'
					 WHERE id     = '".$idRegis."'
					   AND Auduia = ''
					";
					mysql_query($sqlUpdateUsuIngAut, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUpdateUsuIngAut):</b><br>".mysql_error());
				}
			}
			// --> Actualizar los procedimientos de la cx
			foreach($infoConvenios as $idRegis => $infoConv)
			{
				$sqlUpdatePro = "	UPDATE {$wbasedatoFacturacion}_000253
									   		SET Audvbo = '{$infoConv['negar_vobo']}'
									 WHERE id = '{$idRegis}'";
				mysql_query($sqlUpdatePro, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUpdatePro):</b><br>".mysql_error());
			}

			// --> Guardar los nuevos procedimientos (Agregados en este monitor)
			foreach($infoAuditoriaNuevos as $infoAudNuevos)
			{
				$infoAudNuevos['organo'] = (($infoAudNuevos['organo'] != "") ? $infoAudNuevos['organo'] : "*");
				$sqlNuevoPro = "
				INSERT INTO ".$wbasedatoFacturacion."_000253 (Medico,						Fecha_data,			Hora_data,				Audtur,			Audpro,											Audcon,	Audrec,	Audadi,	Audnau, 								Audest,	Auduia,															Audvia,							Audorg,							Audbil,									Seguridad)
													  VALUES ('".$wbasedatoFacturacion."', '".date("Y-m-d")."', '".date("H:i:s")."',	'".$idTurno."',	'".trim($infoAudNuevos['procedimiento'])."',	'on', 	'off', 	'on', 	'".trim($infoAudNuevos['numAuto'])."', 	'on', 	'".((trim($infoAudNuevos['numAuto']) != '') ? $wuse : '')."',	'".$infoAudNuevos['via']."',	'".$infoAudNuevos['organo']."',	'".$infoAudNuevos['checkBilateral']."',	'C-".$wuse."')
				";
				mysql_query($sqlNuevoPro, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlNuevoPro):</b><br>".mysql_error());
			}

			// --> Guardar los nuevos procedimientos que vienen desde la descripcion operatoria, pero que aun no se habian insertado
			foreach($infoAuditoriaDesdeDesOpe as $codNuevoPro => $infoAudiDesdeDesOpe)
			{
				$infoAudiDesdeDesOpe['organo'] = (($infoAudiDesdeDesOpe['organo'] != "") ? $infoAudiDesdeDesOpe['organo'] : "*");

				$sqlNuevoProDesOp = "
				INSERT INTO ".$wbasedatoFacturacion."_000253 (Medico,						Fecha_data,			Hora_data,				Audtur,			Audpro,						Audcon,										Audrec,																												Audnau, 								Audest,	Auduia, 																																			Audvia,								Audorg,									Audbil,											Audmed, 										Seguridad)
													  VALUES ('".$wbasedatoFacturacion."', '".date("Y-m-d")."', '".date("H:i:s")."',	'".$idTurno."',	'".trim($codNuevoPro)."',	'".$infoAudiDesdeDesOpe['confirmado']."', 	'".(($infoAudiDesdeDesOpe['confirmado'] == 'on' || $infoAudiDesdeDesOpe['confirmado'] == '') ? "off" : "on")."', 	'".$infoAudiDesdeDesOpe['numAuto']."',	'on', 	'".((trim($infoAudiDesdeDesOpe['numAuto']) != '' && trim($infoAudiDesdeDesOpe['numAuto']) != $infoAudiDesdeDesOpe['numAutoAdmi']) ? $wuse : '')."', '".$infoAudiDesdeDesOpe['via']."',	'".$infoAudiDesdeDesOpe['organo']."',	'".$infoAudiDesdeDesOpe['checkBilateral']."',	'".$infoAudiDesdeDesOpe['medicoRealizo']."',	'C-".$wuse."')
				";
				mysql_query($sqlNuevoProDesOp, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlNuevoProDesOp):</b><br>".mysql_error());
			}

			// --> Actualizar los numeros de autorizaciones de los insumos.
			foreach($numAutoInsumos as $idMerc => $numAutoriInsu)
			{
				$sqlUpdateInsu = "
				UPDATE ".$wbasedatoFacturacion."_000207
				   SET Mpanau = '".$numAutoriInsu."'
				 WHERE id     = '".$idMerc."'
				";
				mysql_query($sqlUpdateInsu, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUpdateInsu):</b><br>".mysql_error());

				if($numAutoriInsu != '')
				{
					$sqlUpdateUsu = "
					UPDATE ".$wbasedatoFacturacion."_000207
					   SET Mpauia = '".$wuse."'
					 WHERE id     = '".$idMerc."'
					   AND Mpauia = ''
					";
					mysql_query($sqlUpdateUsu, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUpdateUsu):</b><br>".mysql_error());
				}
			}
			
			// --> Guardar el log de movimientos
			$sqlLogMov = "
			INSERT INTO ".$wbasedatoFacturacion."_000259 (Medico,						Fecha_data,			Hora_data,				Movtur,			Movest,			Movdes,				Movusu,			Seguridad)
												  VALUES ('".$wbasedatoFacturacion."', '".date("Y-m-d")."', '".date("H:i:s")."',	'".$idTurno."',	'".$destino."',	'".$descripEst."',	'".$wuse."',	'C-".$wuse."')
			";
			mysql_query($sqlLogMov, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlLogMov):</b><br>".mysql_error());


			break;
			return;
		}
		case 'guardarAuditoriaActualizaAutorizacion':
				$data = array("error"=>0,"mensaje"=>"","html"=>"");
				$infoAuditoria = json_decode(str_replace('\\', '', $infoAuditoria), true);

				$descripEst = utf8_decode('Números de autorización actualizados');
				$fecha_actual = date("Y-m-d");
				$hora_actual = date("H:i:s");

				// --> Guardar el log de movimientos
				$sqlLogMov = "	INSERT INTO {$wbasedatoFacturacion}_000259
											(Medico, Fecha_data, Hora_data, Movtur, Movest, Movdes, Movusu, Seguridad)
								VALUES 		('{$wbasedatoFacturacion}', '{$fecha_actual}', '{$hora_actual}', '{$turno}', 'LIQ', '{$descripEst}', '{$wuse}', 'C-{$wuse}') ";
				mysql_query($sqlLogMov, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlLogMov):</b><br>".mysql_error());

				// --> Actualizar los procedimientos de la cx
				foreach($infoAuditoria as $idRegis => $infoAud)
				{
					$infoAud['organo'] = (($infoAud['organo'] != "") ? $infoAud['organo'] : "*");

					$sqlUpdatePro = "	UPDATE 	{$wbasedatoFacturacion}_000253
												SET Audnau = '{$infoAud['numAuto']}'
										WHERE 	id = '{$idRegis}'";
					mysql_query($sqlUpdatePro, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUpdatePro):</b><br>".mysql_error());

					// --> 	Se registra el usuario que ingresa el numero de autorizacion, solo la primera vez, por eso se tiene el
					//		filtro de que Auduia = '', esto con el fin de controlar de que solo el usuario que ingresó la primera vez
					//		el número de autorización, sea el unico que lo pueda modificar.
					//		Y si el numero de autorizacion es diferente al que se ingreso en admisones.
					if($infoAud['numAuto'] != '' && $infoAud['numAuto'] != $infoAud['numAutoAdmi'])
					{
						$sqlUpdateUsuIngAut = " UPDATE 	{$wbasedatoFacturacion}_000253
														SET Auduia = '{$wuse}'
												WHERE 	id = '{$idRegis}'
														AND Auduia = ''";
						mysql_query($sqlUpdateUsuIngAut, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUpdateUsuIngAut):</b><br>".mysql_error());
					}
				}
				echo json_encode($data);
			break;
			return;
		case 'pintarProcedimientosSinAuditar':
		{
			$arr_roles_cx = json_decode(str_replace('\\', '', $arr_roles_cx), true);
			$respuesta = pintarProcedimientosSinAuditar($tipoPaciente, $selectEstado, $arr_roles_cx, $perfilMonitorAud);
			echo json_encode($respuesta);
			break;
			return;
		}
		case 'pintarMedicamentosUnixvsMatrix':
		{

			$respuesta = pintarMedicamentosUnixvsMatrix($tipoPaciente,$cco);
			echo $respuesta;
			break;
			return;
		}case 'pintarCargosxCambioResponsable':
		{

			$respuesta = pintarCargosxCambioResponsable($tipoPaciente);
			echo $respuesta;
			break;
			return;
		}
		case 'pintarProcedimientosAuditados':
		{
			$arr_roles_cx = json_decode(str_replace('\\', '', $arr_roles_cx), true);
			$numHis = ($numHis == '') ? '%' : $numHis;
			$respuesta = pintarProcedimientosAuditados($fechaAutorizacion, $numHis, $verTodas, $arr_roles_cx);
			echo json_encode($respuesta);
			break;
			return;
		}
		case 'pintarTurnosLiquidadosTramite':
		{
			$arr_roles_cx = json_decode(str_replace('\\', '', $arr_roles_cx), true);
			// $numHis = ($numHis == '') ? '%' : $numHis;
			$respuesta = pintarTurnosLiquidadosTramite($conex, $wemp_pmla, $wbasedato, $arr_roles_cx, $tipo_consulta, $sufijo, $perfilMonitorAud);
			// $respuesta = array("html"=>"");
			echo json_encode($respuesta);
			break;
			return;
		}
		case "consultarlog" :
		{
			consultarlog($wdocumento, $wlinea, $whistoria, $wingreso, $wfuente ,$wreg);
			break;
			return;
		}
		case 'procedimientoSeDebeAutorizar':
		{
			$respuesta = array('pideAutorizacion', 'htmlTipoLiq');

			// --> Obtener array de organos
			$arrayOrganos = array();
			$sqlOrganos = "
			SELECT Prccod, Prcnom
			  FROM root_000104
			 WHERE Prcest = 'on'
			";
			$resOrganos = mysql_query($sqlOrganos, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlOrganos):</b><br>".mysql_error());
			while($rowOrganos = mysql_fetch_array($resOrganos))
				$arrayOrganos[$rowOrganos['Prccod']] = $rowOrganos['Prcnom'];

			// --> Obtener clasificacion del procedimiento
			$sqlClasPro = "
			SELECT Procpg
			  FROM ".$wbasedato."_000103
			 WHERE Procod = '".$codProcedimiento."'
			";
			$resClasPro = mysql_query($sqlClasPro, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlClasPro):</b><br>".mysql_error());
			if($rowClasPro = mysql_fetch_array($resClasPro))
				$clasifiPro = $rowClasPro['Procpg'];
			else
				$clasifiPro = '*';

			$pideAutorizacion 				= procedimientoSeDebeAutorizar($codEnt, $nitEnt, $tipEnt, $planEmp, $tarifaEnt, $clasifiPro, $codProcedimiento);
			$respuesta['pideAutorizacion']	= ($pideAutorizacion) ? 'on' : 'off';


			// --> 	Obtener tipo de liquidación
			$sqlCodHomo = "
			SELECT Protfa AS tipoFac, Propun AS numUvr, '103' AS tabla, '*' Proemporg
			  FROM ".$wbasedato."_000103
			 WHERE Procod = '".$codProcedimiento."'
			   AND Proest = 'on'
			 UNION
			SELECT Proemptfa AS tipoFac, Proemppun AS numUvr, '70' AS tabla, Proemporg
			  FROM ".$wbasedato."_000070 AS A INNER JOIN ".$wbasedato."_000103 AS B ON(Proempcod = Procod)
			 WHERE Proempcod  = '".$codProcedimiento."'
			   AND (Proempemp = '".$codEnt."' OR Proempemp = '*')
			   AND (Proemptip = '".$tipEnt."' OR Proemptip = '*')
			   AND (Proempcco = '".$ccoCx."'  OR Proempcco = '*')
			   AND Proempest  = 'on'
			";
			// $respuesta['SEGUIMIENTO'] = $sqlCodHomo;
			$resCodHomo = mysql_query($sqlCodHomo, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCodHomo):</b><br>".mysql_error());
			while($rowCodHomo = mysql_fetch_array($resCodHomo))
			{
				$organo 														= $rowCodHomo['Proemporg'];
				$rowCodHomo['tipoFac']											= explode("-", $rowCodHomo['tipoFac']);
				$rowCodHomo['tipoFac']											= $rowCodHomo['tipoFac'][0];
				$datosPro[$rowCodHomo['tabla']][$organo]['tipoFac'] 			= $rowCodHomo['tipoFac'];
				$datosPro[$rowCodHomo['tabla']][$organo]['numUvr']				= $rowCodHomo['numUvr'];
			}

			$selectOrg 		= "<option></option>";
			$mostrarSelect	= false;

			if(isset($datosPro['70']))
			{
				if(count($datosPro['70']) > 1)
				{
					foreach($datosPro['70'] as $codOrg => $infoValPro)
					{
						$infoPro['numUvr']			= (($infoValPro['numUvr'] > 0) ? $infoValPro['numUvr'] : $datosPro['103']["*"]['numUvr']);

						$mostrarSelect = true;

						$tooltipSelect = "
						<span style=font-weight:normal;>
							<b>Tipo de Liquidación:</b> ".$infoValPro['tipoFac'].(($infoValPro['tipoFac'] == "UVR") ? " (".$infoPro['numUvr'].")" : "").((trim($infoValPro['nombreProEntidad']) != '') ? "<br><b>Nombre propio:</b> ".$infoValPro['nombreProEntidad'] : "")."</span>";

						$selectOrg.= "
						<option value='".$codOrg."' tooltSelect='".$tooltipSelect."'>".$arrayOrganos[$codOrg]."</option>
						";

						$respuesta['htmlTipoLiq'] = "<img style='cursor:help' id='imgTipLiq".$consecutivo."' tooltip='si' title='' width='13' height='13' src='../../images/medical/sgc/info_black.png'>";
					}
				}
				else
				{
					foreach($datosPro['70'] as $codOrg => $infoValPro)
					{
						$infoPro['tipoLiq']			= $infoValPro['tipoFac'];
						$infoPro['numUvr']			= (($infoValPro['numUvr'] > 0) ? $infoValPro['numUvr'] : $infoValPro['numUvr']);

						$selectOrg = "
						<option value='".$codOrg."'>".$arrayOrganos[$codOrg]."</option>
						";

						$respuesta['htmlTipoLiq'] = "<img style='cursor:help' id='imgTipLiq".$consecutivo."' tooltip='si' title='<span style=font-weight:normal;>Tipo de Liquidación: ".$infoPro['tipoLiq'].(($infoPro['tipoLiq'] == "UVR") ? " (".$infoPro['numUvr'].")" : "")."</span>' width='13' height='13' src='../../images/medical/sgc/info_black.png'>";
					}
				}
			}
			else
			{
				$infoPro['tipoLiq']			= $datosPro['103']["*"]['tipoFac'];
				$infoPro['numUvr']			= $datosPro['103']["*"]['numUvr'];

				$selectOrg = "
				<option value='*'>NO APLICA</option>
				";

				$respuesta['htmlTipoLiq'] = "<img style='cursor:help' id='imgTipLiq".$consecutivo."' tooltip='si' title='<span style=font-weight:normal;>Tipo de Liquidación: ".$infoPro['tipoLiq'].(($infoPro['tipoLiq'] == "UVR") ? " (".$infoPro['numUvr'].")" : "")."</span>' width='13' height='13' src='../../images/medical/sgc/info_black.png'>";
			}

			$respuesta['selectOrg'] 	= $selectOrg;
			$respuesta['mostrarSelect'] = $mostrarSelect;


			// --> Obtener si el procedimiento es NO POS.
			$sqlNoPos = "
			SELECT id
			  FROM ".$wbasedato."_000249
			 WHERE Pnppro  = '".$codProcedimiento."'
			   AND (Pnpemp = '".$codEnt."' OR Pnpemp = '*')
			   AND Pnppnp = 'on'
			   AND Pnpest = 'on'
			";
			$resNoPos = mysql_query($sqlNoPos, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlNoPos):</b><br>".mysql_error());
			if(mysql_fetch_array($resNoPos))
				$infoPro['tipoLiq'] = 'NO POS';

			echo json_encode($respuesta);
			break;
			return;
		}
		case 'autorizacionCxEnTramite':
		{
			$sqlCxTramite = "
			UPDATE ".$wbasedato."_000252
			   SET Auetra = '".$causa."'
             WHERE Auetur = '".$turno."'
			";
			mysql_query($sqlCxTramite, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCxTramite):</b><br>".mysql_error());
			break;
			return;
		}
		// --> Edwar Jaramillo 2018-03-12
		case 'marcarTurnoRevisado':
		{
			$data = array("error_msj"=> "", "mensaje"=>"", "error"=>0);
			$sqlCxrevisado = "	UPDATE 	{$wbasedato}_000252
										SET Auelre = '{$estadoRevisado}'
					            WHERE 	Auetur = '{$turno}'";
			if(mysql_query($sqlCxrevisado, $conex)){
				$data["mensaje"] = "Cirugía marcada como revisada";
			} else {
				$data["error_msj"] = "<b>ERROR EN QUERY MATRIX(sqlCxrevisado):</b><br>".mysql_error().' #> '.$sqlCxrevisado;
				$data["error"] = 1;
				$data["mensaje"] = "La cirugía no se pudo marcar como revisada";
			}
			echo json_encode($data);
			break;
			return;
		}
		// --> Jerson Trujillo	2016-09-22
		case 'reversarCxAlMonitorAud':
		{
			// --> Cambiar estado de la cx
			$sqlReversarCx = "
			UPDATE ".$wbasedato."_000252
			   SET Auelli = 'off',
			       Aueecx = 'FAC',
				   Auerau = ''
             WHERE Auetur = '".$turnoCx."'
			";
			mysql_query($sqlReversarCx, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlReversarCx):</b><br>".mysql_error());

			// --> Guardar el log de movimientos
			$sqlLogMov = "
			INSERT INTO ".$wbasedato."_000259 (Medico,				Fecha_data,			Hora_data,				Movtur,			Movest,		Movdes,									Movusu,			Seguridad)
									   VALUES ('".$wbasedato."', 	'".date("Y-m-d")."', '".date("H:i:s")."',	'".$turnoCx."',	'FAC',		'Se reversa la cx (".$tipoRevAuto.")',	'".$wuse."',	'C-".$wuse."')
			";
			mysql_query($sqlLogMov, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlLogMov):</b><br>".mysql_error());

			break;
			return;
		}
		case 'autorizarAuditarCx':
		{
			$arr_roles_cx 	= json_decode(str_replace('\\', '', $arr_roles_cx), true);
			$wbasedatoTcx 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'tcx');
			$wbasedatoHce 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
			$wbasedatoMov 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
			$hisUsuarios	= array();
			$hayProcedimientos253 = false;
			$html = "";
			$requiere_autorizar_liquidado = 'off';

			$caracter_ok = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U", "");
			$caracter_ma = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£", "°");

			$data['tram_revisado']   = 'off';
			$data['turno_liquidado'] = 'off';
			$data['tramActual']      = '';

			$ver_liquidado_tramite = (array_key_exists($perfilMonitorAudRef, $arr_roles_cx)) ? $arr_roles_cx[$perfilMonitorAudRef]["ver_liquidado_tramite"]: 'off';

			// --> Consultar la informacion del turno de cx
			$sqlInfoTurCx = "
			SELECT  cli252.Auetur, cli252.Auehis, cli252.Aueing, cli252.Aueobs, cli252.Auetra, cli252.Auesra, cli252.Aueaus, cli252.Auelli, cli252.Auelre, cli252.Aueliq,
					cli253.id AS idPro, cli100.Pacact, CONCAT(cli100.Pacno1, ' ', cli100.Pacno2, ' ', cli100.Pacap1, ' ', cli100.Pacap2) AS Nombre, cli100.Pacdoc, cli100.Pactdo, cli24.Empnom, cli24.Empnit, cli24.Empcod, cli24.Emptem, cli25.Tardes,
				    cli253.Audpro, cli253.Audcon, cli253.Audrec,  cli253.Audvbo AS negar_vobo, cli253.Audnau, cli253.Auduia, cli253.Audadi, cli253.Audsra, cli253.Audvia, cli253.Audorg, cli253.Audbil,
				    cli253.Seguridad AS UsuAdiciono, cli253.Audmed, cli103.Pronom, tcx11.Turfec, tcx11.Turcom, cli101.Ingtpa, cli101.Ingpla, tcx12.Quicco
			  FROM  {$wbasedato}_000252 AS cli252
			  		INNER JOIN
				    {$wbasedato}_000100 AS cli100 ON (cli252.Auehis = cli100.Pachis)
				    INNER JOIN
				    {$wbasedato}_000101 AS cli101 ON (cli252.Auehis = cli101.Inghis AND cli252.Aueing = cli101.Ingnin)
				    INNER JOIN
				    {$wbasedatoTcx}_000011 AS tcx11 ON (cli252.Auetur = tcx11.Turtur)
				    INNER JOIN
				    {$wbasedatoTcx}_000012 AS tcx12 ON (tcx11.Turqui = tcx12.Quicod)
			  		LEFT JOIN
			  		{$wbasedato}_000253 AS cli253 ON (cli252.Auetur = cli253.Audtur)
			  		LEFT JOIN
			  		{$wbasedato}_000103 AS cli103 ON (cli253.Audpro = cli103.Procod AND cli253.Audest = 'on')
				    LEFT JOIN
				    {$wbasedato}_000024 AS cli24 ON (cli101.Ingcem = cli24.Empcod)
				    LEFT JOIN
				    {$wbasedato}_000025 AS cli25 ON (cli24.Emptar = cli25.Tarcod)
			 WHERE  cli252.Auetur = '{$turno}'";

			$resInfoTurCx 	= mysql_query($sqlInfoTurCx, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInfoTurCx):</b><br>".mysql_error());
			while($rowCxPendientes = mysql_fetch_array($resInfoTurCx))
			{
				$arrayCx['historia'] 		= $rowCxPendientes['Auehis'];
				$arrayCx['ingreso'] 		= $rowCxPendientes['Aueing'];
				$arrayCx['paciente'] 		= utf8_encode($rowCxPendientes['Nombre']);
				$arrayCx['documento'] 		= $rowCxPendientes['Pacdoc'];
				$arrayCx['tipoDoc'] 		= $rowCxPendientes['Pactdo'];
				$arrayCx['Ingtpa'] 			= $rowCxPendientes['Ingtpa'];
				$arrayCx['entidad'] 		= utf8_encode($rowCxPendientes['Empnom']);
				$arrayCx['codEntidad'] 		= $rowCxPendientes['Empcod'];
				$arrayCx['nitEntidad'] 		= $rowCxPendientes['Empnit'];
				$arrayCx['tipoEmp'] 		= $rowCxPendientes['Emptem'];
				$arrayCx['tarifa'] 			= utf8_encode($rowCxPendientes['Tardes']);
				$arrayCx['plan'] 			= $rowCxPendientes['Ingpla'];
				$arrayCx['fechCx'] 			= $rowCxPendientes['Turfec'];
				$arrayCx['observaciones']	= $rowCxPendientes['Aueobs'];
				$arrayCx['enTramite']		= $rowCxPendientes['Auetra'];
				$arrayCx['segundoRespAutor']= $rowCxPendientes['Auesra'];
				$arrayCx['autorizadaSegRes']= $rowCxPendientes['Aueaus'];
				$arrayCx['listaParaLiqui']	= $rowCxPendientes['Auelli'];
				$arrayCx['tram_revisado']	= $rowCxPendientes['Auelre'];
				$arrayCx['turno_liquidado']	= $rowCxPendientes['Aueliq'];
				$arrayCx['ccoCx']			= $rowCxPendientes['Quicco'];
				$arrayCx['pacActivo']		= trim($rowCxPendientes['Pacact']);
				$arrayCx['cartaDerechos']	= utf8_encode($rowCxPendientes['Turcom']);

				$data['tram_revisado']   = $arrayCx['tram_revisado'];
				$data['turno_liquidado'] = $arrayCx['turno_liquidado'];
				$data['tramActual']      = $arrayCx['enTramite'];

				if(!array_key_exists("procedimientosCups", $arrayCx))
				{
					$arrayCx["procedimientosCups"] = array();
				}

				// --> Si es un particular
				if($arrayCx['Ingtpa'] == 'P')
				{
					$codEntParticular = consultarAliasPorAplicacion($conex, $wemp_pmla, 'codigoempresaparticular');
					$sqlInfoEmpPar = "
					SELECT Empnit, Emptem, Empnom, Tardes
					  FROM ".$wbasedato."_000024 AS A INNER JOIN ".$wbasedato."_000025 AS B ON(Emptar = Tarcod)
					 WHERE Empcod = '".$codEntParticular."'
					";
					$resInfoEmpPar = mysql_query($sqlInfoEmpPar, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInfoEmpPar):</b><br>".mysql_error());
					if($rowInfoEmpPar = mysql_fetch_array($resInfoEmpPar))
					{
						$arrayCx['codEntidad'] 	= $codEntParticular;
						$arrayCx['entidad']		= utf8_encode($rowInfoEmpPar['Empnom']);
						$arrayCx['nitEntidad'] 	= $rowInfoEmpPar['Empnit'];
						$arrayCx['tarifa'] 		= utf8_encode($rowInfoEmpPar['Tardes']);
						$arrayCx['tipoEmp']		= $rowInfoEmpPar['Emptem'];
						$arrayCx['plan']		= '*';
					}
				}

				// --> Detalle de procedimientos
				if(trim($rowCxPendientes['Audpro']) != '')
				{
					// --> Si existe un segundo responsable, solo muestro los procedimientos que se requieran reautorizar.
					if($rowCxPendientes['Auesra'] != '' && $rowCxPendientes['Audsra'] != 'on' && $arrayCx['autorizadaSegRes'] != 'on')
						continue;

					$arrayCx['procedimientosCups'][$rowCxPendientes['idPro']]['codigoPro'] 		= $rowCxPendientes['Audpro'];
					$arrayCx['procedimientosCups'][$rowCxPendientes['idPro']]['nombrePro'] 		= utf8_encode($rowCxPendientes['Pronom']);
					$arrayCx['procedimientosCups'][$rowCxPendientes['idPro']]['proConfir'] 		= $rowCxPendientes['Audcon'];
					$arrayCx['procedimientosCups'][$rowCxPendientes['idPro']]['proRechaza']		= $rowCxPendientes['Audrec'];
					$arrayCx['procedimientosCups'][$rowCxPendientes['idPro']]['negar_vobo']		= $rowCxPendientes['negar_vobo'];
					$arrayCx['procedimientosCups'][$rowCxPendientes['idPro']]['numAutorizacion']= trim($rowCxPendientes['Audnau']);
					if(trim($rowCxPendientes['Audnau']) != ''){
						$arrayCx['procedimientosCups'][$rowCxPendientes['idPro']]['usuIngresoAut']	= (($rowCxPendientes['Audsra'] == 'on' && $arrayCx['autorizadaSegRes'] != 'on') ? "" : $rowCxPendientes['Auduia']);
					}
					
					$arrayCx['procedimientosCups'][$rowCxPendientes['idPro']]['proAdicionado']		= $rowCxPendientes['Audadi'];
					
					// --> Si es adicionado muestro el nombre del usuario que hizo el registro
					if($rowCxPendientes['Audadi'] == 'on')
						$arrayCx['procedimientosCups'][$rowCxPendientes['idPro']]['UsuAdiciono']	= $rowCxPendientes['UsuAdiciono'];
					else
						$arrayCx['procedimientosCups'][$rowCxPendientes['idPro']]['UsuAdiciono']	= $rowCxPendientes['Audmed'];
					
					$arrayCx['procedimientosCups'][$rowCxPendientes['idPro']]['Via']			= $rowCxPendientes['Audvia'];
					$arrayCx['procedimientosCups'][$rowCxPendientes['idPro']]['organo']			= $rowCxPendientes['Audorg'];
					$arrayCx['procedimientosCups'][$rowCxPendientes['idPro']]['Bilateral']		= $rowCxPendientes['Audbil'];

					$hayProcedimientos253						= true;
				}
			}

			if($ver_liquidado_tramite == 'on' && $data["turno_liquidado"] == 'on' && $data["tramActual"] != '10' && $data["tramActual"] != '')
			{
				$requiere_autorizar_liquidado = 'on';
			}

			// --> Consultar los procedimientos relacionados a la entidad del paciente
			// --> Abril 04 de 2017: Se modifica el query, para que muestre el nombre de la 103 y no el de la 70
			// --> Abril 16 2018: Jerson. Se agrega el filtro "AND B.Proest  = 'on'"
			$sqlProRelPac = "
			SELECT Procod AS CODIGO, Pronom AS NOMBRE
			  FROM ".$wbasedato."_000103
			 WHERE Proest = 'on'
			 UNION
			SELECT Proempcod AS CODIGO, CONCAT('(', Proemppro, ')', B.Pronom) AS NOMBRE
			  FROM ".$wbasedato."_000070 AS A, ".$wbasedato."_000103 AS B
			 WHERE Proempemp = '".(($arrayCx['segundoRespAutor'] != '') ? $arrayCx['segundoRespAutor'] : $arrayCx['codEntidad'])."'
			   AND Proempest = 'on'
			   AND Proempcod = B.Procod
			   AND B.Proest  = 'on'
			";
			$resProRelPac 	= mysql_query($sqlProRelPac, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlProRelPac):</b><br>".mysql_error());
			$arrayProced 	= array();
			while($rowProRelPac = mysql_fetch_array($resProRelPac))
			{
				$rowProRelPac['NOMBRE'] = str_replace($caracter_ma, $caracter_ok, utf8_decode($rowProRelPac['NOMBRE']));
				$arrayProced[trim($rowProRelPac['CODIGO'])] = trim($rowProRelPac['NOMBRE']);
			}

			$html .= "
			<input type='hidden' id='arrayProcedParaAuditoria' value='".json_encode($arrayProced)."'>";

			$hallazgosDesOp	= array();
			$otrosProced	= array();
			$descripciDesOp	= array();
			$viasCx			= array();
			$indice2 		= 'Nuevo1';
			
			// --> Consultar fecha y hora de la descripcion operatoria.
			$sqlDesOpe = "
			SELECT Fecha_data, Hora_data
			  FROM ".$wbasedatoHce."_000077
			 WHERE movhis = '".$arrayCx['historia']."'
			   AND moving = '".$arrayCx['ingreso']."'
			   AND movpro = '000077'
			   AND movcon = '69'
			   AND movdat = '".$turno."' ";
			$resDesOpe = mysql_query($sqlDesOpe, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlDesOpe):</b><br>".mysql_error());
			while($rowDesOpe = mysql_fetch_array($resDesOpe))
			{
				// --> Consultar el medico
				$sqlDesMed = "
				SELECT Codigo, descripcion, Rolmed 
				  FROM ".$wbasedatoHce."_000077 AS A INNER JOIN usuarios AS B ON(A.movusu = Codigo)
					   INNER JOIN ".$wbasedatoHce."_000020 ON (movusu = Usucod)
					   INNER JOIN ".$wbasedatoHce."_000019 ON (Usurol = Rolcod)	
				 WHERE A.Fecha_data = '".$rowDesOpe['Fecha_data']."'
				   AND A.Hora_data  = '".$rowDesOpe['Hora_data']."'
				   AND movpro 		= '000077'
				   AND movcon 		= '1000'
				   AND movhis 		= '".$arrayCx['historia']."'
				   AND moving 		= '".$arrayCx['ingreso']."'
				 ORDER BY A.Fecha_data ASC, A.Hora_data ASC
				";
				$sqlDesMed 	= mysql_query($sqlDesMed, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlDesMed):</b><br>".mysql_error());
				if($rowDesMed = mysql_fetch_array($sqlDesMed)){
					// --> Si es un medico
					if($rowDesMed["Rolmed"] == "on"){
						$medicoCx 		= $rowDesMed["descripcion"];
						$codMedicoCx 	= $rowDesMed["Codigo"];
					}					
					// --> Si no es medico, osea un residente entonces traigo el campo 1001
					else{
						
						$sqlDesMed2 = "
						SELECT Codigo, descripcion 
						  FROM ".$wbasedatoHce."_000077 AS A INNER JOIN usuarios AS B ON(A.movusu = Codigo)
						 WHERE A.Fecha_data = '".$rowDesOpe['Fecha_data']."'
						   AND A.Hora_data  = '".$rowDesOpe['Hora_data']."'
						   AND movpro 		= '000077'
						   AND movcon 		= '1001'
						   AND movhis 		= '".$arrayCx['historia']."'
						   AND moving 		= '".$arrayCx['ingreso']."'
						 ORDER BY A.Fecha_data ASC, A.Hora_data ASC
						";
						$resDesMed2 	= mysql_query($sqlDesMed2, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlDesMed2):</b><br>".mysql_error());
						if($rowDesMed2 = mysql_fetch_array($resDesMed2)){
							$medicoCx 		= $rowDesMed2["descripcion"];
							$codMedicoCx 	= $rowDesMed2["Codigo"];
						}
					}
				}
				else{
					$medicoCx 		= "";
					$codMedicoCx	= "";
				}
				
				//--> Consultar especialidad del medico
				if($codMedicoCx != ""){
					
					$sqlEspMed = "
					SELECT Espnom
					  FROM ".$wbasedatoMov."_000048 INNER JOIN ".$wbasedatoMov."_000044 ON(Medesp = Espcod)
					 WHERE Meduma = '".$codMedicoCx."'
					";
					$resEspMed 	= mysql_query($sqlEspMed, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlEspMed):</b><br>".mysql_error());
					while($rowEspMed = mysql_fetch_array($resEspMed))
						$medicoCx.= "<font style='font-weight:normal'> (".$rowEspMed["Espnom"].")</font>";
				}					

				// --> Consultar la descripción de la cx, los hallazgos y otros procedimientos.
				$sqlDesHalla = "
				SELECT A.Fecha_data, A.Hora_data, movdat, movcon, descripcion
				  FROM ".$wbasedatoHce."_000077 AS A INNER JOIN usuarios AS B ON(A.movusu = Codigo)
				 WHERE A.Fecha_data = '".$rowDesOpe['Fecha_data']."'
				   AND A.Hora_data  = '".$rowDesOpe['Hora_data']."'
				   AND movpro 		= '000077'
				   AND movcon 		IN('97', '105', '64', '99', '31')
				   AND movhis 		= '".$arrayCx['historia']."'
				   AND moving 		= '".$arrayCx['ingreso']."'
				 ORDER BY A.Fecha_data ASC, A.Hora_data ASC
				";
				$resDesHalla 	= mysql_query($sqlDesHalla, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlDesHalla):</b><br>".mysql_error());

				while($rowDesHalla = mysql_fetch_array($resDesHalla))
				{
					$indice = strtotime($rowDesHalla['Fecha_data']." ".$rowDesHalla['Hora_data']);

					switch($rowDesHalla['movcon'])
					{
						// --> Hallazgos
						case '97':
						{
							$hallazgosDesOp[$indice]['valor'] 		= $rowDesHalla['movdat'];
							// $hallazgosDesOp[$indice]['usuario'] 	= $rowDesHalla['descripcion'];
							$hallazgosDesOp[$indice]['usuario'] 	= $medicoCx;
							break;
						}
						// --> Otros procedimientos
						case '64':
						{
							$otrosProced[$indice]['valor'] 		= $rowDesHalla['movdat'];
							// $otrosProced[$indice]['usuario'] = $rowDesHalla['descripcion'];
							$otrosProced[$indice]['usuario'] 	= $medicoCx;
							break;
						}
						// --> Descripción de la cx
						case '105':
						{
							$descripciDesOp[$indice]['valor'] 	= $rowDesHalla['movdat'];
							// $descripciDesOp[$indice]['usuario'] = $rowDesHalla['descripcion'];
							$descripciDesOp[$indice]['usuario'] = $medicoCx;
							break;
						}
						// --> Vías de la cx
						case '31':
						{
							$viasCx[$indice]['valor'] 	= $rowDesHalla['movdat'];
							// $viasCx[$indice]['usuario'] = $rowDesHalla['descripcion'];
							$viasCx[$indice]['usuario'] = $medicoCx;
							break;
						}
						// --> Procedimientos cups nuevos
						case '99':
						{
							// --> 	Si es en modo consulta, solo muestro los procedimientos que hayan en la tabla de auditoria
							//		Lo que haya en la hce no lo muestro
							if(($modoConsulta == 'on' && $arrayCx['listaParaLiqui'] == 'on') || $hayProcedimientos253 || $arrayCx['segundoRespAutor'] != '')
								break;

							// --> Este campo es un html de un <select>, por lo cual se debe interpretar el string
							$str = explode("<option", trim($rowDesHalla['movdat']));
							foreach($str AS $valor)
							{
								if(trim($valor) == '')
									continue;

								$valor 		= "<option".$valor;
								$valor 		= explode(' ', substr(strip_tags($valor), 4));
								$codigoPro 	= explode('(', trim($valor[0]));

								$codigoPro[0] 	= trim(str_replace('.', '', $codigoPro[0]));

								$codigoPro[1] 	= trim(str_replace('.', '', $codigoPro[1]));
								$codigoProTemp 	= explode(')', trim($codigoPro[1]));
								$codigoPro[1]	= trim($codigoProTemp[0]);

								$codigoPro 	= ($codigoPro[1] != '') ? $codigoPro[1] : $codigoPro[0];

								if(trim($codigoPro) != '')
								{
									$arrayCx['procedimientosCups'][$indice2]['codigoPro'] 		= $codigoPro;
									$arrayCx['procedimientosCups'][$indice2]['nombrePro'] 		= '';
									$arrayCx['procedimientosCups'][$indice2]['proConfir'] 		= '';
									$arrayCx['procedimientosCups'][$indice2]['proRechaza']		= '';
									$arrayCx['procedimientosCups'][$indice2]['nuevoDesdeDesOp']	= '';
									$arrayCx['procedimientosCups'][$indice2]['UsuAdiciono']		= $codMedicoCx;

									$indice2 = 'Nuevo'.(((int)str_replace('Nuevo', '', $indice2))+1);
								}
							}
							break;
						}
					}
				}
			}
			
			// --> Consultar la justificacion de la HCE.
			$sqlJustifi = "
			SELECT A.Fecha_data, A.Hora_data, movdat, descripcion
			  FROM ".$wbasedatoHce."_000311 AS A INNER JOIN usuarios AS B ON(A.movusu = Codigo)
			 WHERE Fecha_data 	>= 	'".$arrayCx['fechCx']."'
			   AND movpro 		= '000311'
			   AND movcon 		= '6'
			   AND movhis 		= '".$arrayCx['historia'] ."'
			   AND moving 		= '".$arrayCx['ingreso'] ."'
			 ORDER BY A.Fecha_data ASC, A.Hora_data ASC
			";
			$resDesHalla 	= mysql_query($sqlJustifi, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlJustifi):</b><br>".mysql_error());
			$arrJustificaciones = array();
			while($rowJustifi = mysql_fetch_array($resDesHalla))
			{
				$indice = strtotime($rowJustifi['Fecha_data']." ".$rowJustifi['Hora_data']);
				$arrJustificaciones[$indice]['valor'] 		= $rowJustifi['movdat'];
				$arrJustificaciones[$indice]['usuario'] 	= $rowJustifi['descripcion'];
			}

			// --> Consultar si existe registro de instrumentacion para el mismo turno de cx
			$sqlRegInstru = "
			SELECT Fecha_data, Hora_data
			  FROM ".$wbasedatoHce."_000107
			 WHERE movhis = '".$arrayCx['historia']."'
			   AND moving = '".$arrayCx['ingreso']."'
			   AND movpro = '000107'
			   AND movcon = '280'
			   AND movdat = '".$turno."'
			";
			$arrayMuestras = array();
			$arrayMaterialAlto = array();
			$resRegInstru = mysql_query($sqlRegInstru, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlRegInstru):</b><br>".mysql_error());
			while($rowJustifi = mysql_fetch_array($resRegInstru))
			{
				$arrayMuestras 		= array();
				$arrayMaterialAlto 	= array();
				// --> Obtener el campo muestras para estudio (279) y el campo (298) Material de alto costo
				$sqlMuestras = "
				SELECT movdat, movcon
				  FROM ".$wbasedatoHce."_000107
				 WHERE Fecha_data = '".$rowJustifi['Fecha_data']."'
				   AND Hora_data  = '".$rowJustifi['Hora_data']."'
				   AND movpro 		= '000107'
				   AND movcon 		IN ('279', '298')
				   AND movhis 		= '".$arrayCx['historia']."'
				   AND moving 		= '".$arrayCx['ingreso']."'
				";
				$resMuestras = mysql_query($sqlMuestras, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlMuestras):</b><br>".mysql_error());
				while($rowResMuestras = mysql_fetch_array($resMuestras))
				{
					$rowMuestras = explode('*', trim($rowResMuestras['movdat']));

					// --> La posición cero del array es la cantidad de muestras que ingresaron
					if(trim($rowMuestras[0]) > 0)
					{
						foreach($rowMuestras as $indice => $info)
						{
							if($indice == 0)
								continue;

							if($rowResMuestras['movcon'] == "279")
								$arrayMuestras[] = $info;

							if($rowResMuestras['movcon'] == "298")
								$arrayMaterialAlto[] = $info;
						}
					}
				}
			}

			// --> Consultar las notas operatorias.
			$sqlNotaOpera = "
			SELECT A.Fecha_data, A.Hora_data, movdat, descripcion
			  FROM ".$wbasedatoHce."_000316 AS A INNER JOIN usuarios AS B ON(A.movusu = Codigo)
			 WHERE Fecha_data 	>= 	'".$arrayCx['fechCx']."'
			   AND movpro 		= 	'000316'
			   AND movcon 		= 	'6'
			   AND movhis 		= 	'".$arrayCx['historia']."'
			   AND moving 		= 	'".$arrayCx['ingreso']."'
			 ORDER BY A.Fecha_data ASC, A.Hora_data ASC
			";
			$resNotaOpera 	= mysql_query($sqlNotaOpera, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlNotaOpera):</b><br>".mysql_error());
			$arrNotaOpera 	= array();
			while($rowNotaOpera = mysql_fetch_array($resNotaOpera))
			{
				$indice = strtotime($rowNotaOpera['Fecha_data']." ".$rowNotaOpera['Hora_data']);
				$arrNotaOpera[$indice]['valor'] 	= $rowNotaOpera['movdat'];
				$arrNotaOpera[$indice]['usuario'] 	= $rowNotaOpera['descripcion'];
			}

			// --> Nombre del plan
			$sqlPlan = "
			SELECT Plades
			  FROM ".$wbasedato."_000153
			 WHERE Placod = '".$arrayCx['plan']."'
			";
			$resPlan = mysql_query($sqlPlan, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlPlan):</b><br>".mysql_error());
			if($rowPlan = mysql_fetch_array($resPlan))
				$nombrePlan = $rowPlan['Plades'];
			else
				$nombrePlan = '';

			// --> Obtener array de organos
			$arrayOrganos = array();
			$sqlOrganos = "
			SELECT Prccod, Prcnom
			  FROM root_000104
			 WHERE Prcest = 'on'
			";
			$resOrganos = mysql_query($sqlOrganos, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlOrganos):</b><br>".mysql_error());
			while($rowOrganos = mysql_fetch_array($resOrganos))
				$arrayOrganos[$rowOrganos['Prccod']] = utf8_encode($rowOrganos['Prcnom']);

			$infoSegRes 				= array();
			$infoSegRes['codEntidad'] 	= '';
			$infoSegRes['entidad']		= '';
			$infoSegRes['nitEntidad'] 	= '';
			$infoSegRes['tarifa'] 		= '';
			$infoSegRes['tipoEmp']		= '';
			$infoSegRes['plan']			= '';
			$infoSegRes['descripPlan']	= '';

			$codEnt    = '';
			$nitEnt    = '';
			$tipEnt    = '';
			$planEmp   = '';
			$tarifaEnt = '';
			// --> Obtener informacion del segundo responsable
			if($arrayCx['segundoRespAutor'] != '')
			{
				$sqlInfoSegRes = "
				SELECT Empcod, Empnit, Emptem, Empnom, Tardes, Tarcod, Placod, Plades
				  FROM ".$wbasedato."_000205 AS A INNER JOIN ".$wbasedato."_000024 AS B ON(A.Resnit = B.Empcod)
						INNER JOIN ".$wbasedato."_000025 AS C ON(Emptar = Tarcod) LEFT JOIN ".$wbasedato."_000153 AS D ON (Respla = Placod)
				 WHERE Reshis = '".$arrayCx['historia']."'
				   AND Resing = '".$arrayCx['ingreso']."'
				   AND Resnit = '".$arrayCx['segundoRespAutor']."'
				";
				$resInfoSegRes = mysql_query($sqlInfoSegRes, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInfoSegRes):</b><br>".mysql_error());
				if($rowInfoSegRes = mysql_fetch_array($resInfoSegRes))
				{
					$infoSegRes['codEntidad'] 	= $rowInfoSegRes['Empcod'];
					$infoSegRes['entidad']		= utf8_encode($rowInfoSegRes['Empnom']);
					$infoSegRes['nitEntidad'] 	= $rowInfoSegRes['Empnit'];
					$infoSegRes['tarifa'] 		= utf8_encode($rowInfoSegRes['Tardes']);
					$infoSegRes['tipoEmp']		= $rowInfoSegRes['Emptem'];
					$infoSegRes['plan']			= $rowInfoSegRes['Placod'];
					$infoSegRes['tarifaEnt']	= $rowInfoSegRes['Tarcod'];
					$infoSegRes['descripPlan']	= utf8_encode($rowInfoSegRes['Plades']);

					// --> Variables para obtener si un insumo o procedimiento requiere autorizacion
					$codEnt 	= $infoSegRes['codEntidad'];
					$nitEnt 	= $infoSegRes['nitEntidad'];
					$tipEnt 	= $infoSegRes['tipoEmp'];
					$planEmp 	= $infoSegRes['plan'];
					$tarifaEnt 	= $infoSegRes['tarifaEnt'];
				}
			}
			else
			{
				// --> Variables para obtener si un insumo o procedimiento requiere autorizacion
				$codEnt 	= $arrayCx['codEntidad'];
				$nitEnt 	= $arrayCx['nitEntidad'];
				$tipEnt 	= $arrayCx['tipoEmp'];
				$planEmp 	= $arrayCx['plan'];
				$tarifaEnt 	= $arrayCx['tarifa'];
			}

			$html .= "<input type='hidden' id='variablesParaValidarAutorizacion' codEnt='".$codEnt."' nitEnt='".$nitEnt."' tipEnt='".$tipEnt."' planEmp='".$planEmp."' tarifaEnt='".$tarifaEnt."' ccoCx='".$arrayCx['ccoCx']."'>";

			//-------------------------------------------------------------------------------------------
			// --> INICIO: Validar si en el mercado del paciente hay insumos que requieren autorización.
			//-------------------------------------------------------------------------------------------
			// --> 1ro: Obtener el mercado.
			$arrArtAuto = array();
			$arrMercado = array();
			$sqlMer = "
			SELECT Mpacom, (Mpacan-Mpadev) saldo, A.id, Mpanau, Mpauia, Mpasra, Artcom, Artcla, Grucod, Grucpg
			  FROM ".$wbasedato."_000207 AS A LEFT JOIN ".$wbasedatoMov."_000026 AS B ON(A.Mpacom = B.Artcod)
				   LEFT JOIN ".$wbasedato."_000004 AS C ON(SUBSTRING_INDEX(B.Artgru, '-', 1) = C.Grucod)
			 WHERE Mpatur = '".$turno."'
			   AND Mpaest = 'on'
			   AND Mpacrm = 'on'
			   ".(($arrayCx['segundoRespAutor'] != '' && $arrayCx['autorizadaSegRes'] != 'on') ? "AND Mpasra = 'on' " : "")."
			 ";
			$resMer = mysql_query($sqlMer, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlMer):</b><br>".mysql_error());
			while($rowMer = mysql_fetch_array($resMer))
			{
				if(trim($rowMer['saldo']) > 0)
				{
					$codigo = trim($rowMer['Mpacom']);
					$arrMercado[$codigo]['codigo'] 			= $codigo;
					$arrMercado[$codigo]['nombre'] 			= trim($rowMer['Artcom']);
					$arrMercado[$codigo]['saldo'] 			= trim($rowMer['saldo']);
					$arrMercado[$codigo]['numAuto'] 		= trim($rowMer['Mpanau']);
					$arrMercado[$codigo]['usuAuto'] 		= (($arrayCx['segundoRespAutor'] != '' && $arrayCx['autorizadaSegRes'] != 'on') ? "" : trim($rowMer['Mpauia']));
					$arrMercado[$codigo]['idMercado'] 		= trim($rowMer['id']);
					$arrMercado[$codigo]['grupo']	 		= trim($rowMer['Grucod']);
					$arrMercado[$codigo]['clasificacionGru']= trim($rowMer['Grucpg']);
					$arrMercado[$codigo]['clasificacionArt']= trim($rowMer['Artcla']);
					$arrMercado[$codigo]['reqAutSegResp']	= trim($rowMer['Mpasra']);
				}
			}

			// --> 2do: Por cada articulo del mercado validar si debe ser autorizado
			foreach($arrMercado AS $codArtMer => $infoArtMer)
			{
				if($infoArtMer['reqAutSegResp'] == 'on')
					$seDebeAutorizar = true;
				else
					$seDebeAutorizar = articuloSeDebeAutorizar($infoArtMer, $codEnt, $nitEnt, $tipEnt, $planEmp);

				if($seDebeAutorizar)
					$arrArtAuto[$codArtMer] = $infoArtMer['nombre'];
			}
			//-------------------------------------------------------------------------------------------
			// --> FIN: Validar si en el mercado del paciente hay insumos que requieren autorización.
			//-------------------------------------------------------------------------------------------

			// --> Pintar Datos básicos
			$html .= "
			<input type='hidden' value='".$arrayCx['enTramite']."' id='enTramite'>
			<div id='pro_".$turno."' align='center'>
				<br>
				<fieldset align='center' style='padding:15px;'>
					<legend class='fieldset'>Datos Básicos</legend>
						<table width='100%'>
							<tr>
								<td class='fila1'>Historia:</td><td class='fila2' id='numHIsIng'>".$arrayCx['historia']."-".$arrayCx['ingreso']."</td>
								<td class='fila1'>Paciente:</td><td class='fila2'>".$arrayCx['paciente']."</td>
								<td class='fila1'>Fecha Cx:</td><td class='fila2'>".$arrayCx['fechCx']."</td>
								<td class='fila1'>Turno:</td><td class='fila2'>".$turno."</td>
							</tr>
							<tr>
								<td class='fila1' width='8%'>Entidad:</td><td class='fila2'>".$arrayCx['entidad']."</td>
								<td class='fila1' >Plan:</td><td class='fila2'>".utf8_encode($nombrePlan)."</td>
								<td class='fila1' width='8%'>Tarifa:</td><td class='fila2'>".$arrayCx['tarifa']."</td>
								<td class='fila1' width='8%'>Estado Paciente:</td><td class='fila2'><b>".(($arrayCx['pacActivo'] == 'on') ? "<span style='color:green'>Activo</span>" : "<span style='color:red'>Inactivo</inactivo>")."</b></td>
							</tr>";
						// --> Información del segundo responsable
						if($arrayCx['segundoRespAutor'] != '')
						{
							$html .= "
							<tr>
								<td class='fila1' width='8%'>Segundo responsable:</td><td class='fila2' style='background-color:#F4CDCB'>".$infoSegRes['entidad']."</td>
								<td class='fila1' >Plan:</td><td class='fila2' style='background-color:#F4CDCB'>".$infoSegRes['descripPlan']."</td>
								<td class='fila1' width='8%'>Tarifa:</td><td colspan='3' class='fila2' style='background-color:#F4CDCB'>".$infoSegRes['tarifa']."</td>
							</tr>";
						}
			$html .= "			<tr>
								<td class='fila1'>Descripción cx:</td><td colspan='3' class='fila2' style='vertical-align: text-top;font-size: 8pt;'>";
								// --> Pintar tabs de descripciones de la cx.
								$html .= "<table tabsIdZona=''>
										<tr style='font-size: 8pt;font-family: verdana;'>";
								$numCx = 1;
								foreach($descripciDesOp AS $indice => $valDescripciDesOp)
									$html .= "	<td idTabZona='".$indice."' onclick='verElemento(\"".$indice."\")' style='cursor:pointer;".(($numCx == 1) ? "color:#FFFFFF;background-color:#5DB9E8;" : "color:#2779AA;background-color:#EEF6FC;")."border-radius: 4px;border:1px solid #999999;padding:2px'>&nbsp;Descripción ".$numCx++."&nbsp;</td>";
								$html .= "	</tr>
									</table>";

								$numDiv = 0;
								foreach($descripciDesOp AS $indice => $valDescripciDesOp)
									$html .= "<div scrollAlto='si' idZona='".$indice."' style='".(($numDiv++ == 0) ? "" : "display:none;")."text-align:justify'><b>".utf8_encode($valDescripciDesOp['usuario'])."</b>:<br>".utf8_encode($valDescripciDesOp['valor'])."</div>";
								$html .= "
								</td>
								<td class='fila1'>Hallazgos:</td><td colspan='3' class='fila2' style='font-size: 8pt;vertical-align: text-top;'>";
								// --> Pintar tabs de descripciones de la cx.
								$html .= "<table tabsIdZona=''>
										<tr style='font-size: 8pt;font-family: verdana;'>";
								$numCx = 1;
								foreach($hallazgosDesOp AS $indice => $valHallazgosDesOp)
									$html .= "	<td idTabZona='".$indice."' onclick='verElemento(\"".$indice."\")' style='cursor:pointer;".(($numCx == 1) ? "color:#FFFFFF;background-color:#5DB9E8;" : "color:#2779AA;background-color:#EEF6FC;")."border-radius: 4px;border:1px solid #999999;padding:2px'>&nbsp;Hallazgo ".$numCx++."&nbsp;</td>";
								$html .= "	</tr>
									</table>";

								$numDiv = 0;
								foreach($hallazgosDesOp AS $indice => $valHallazgosDesOp)
									$html .= "<div scrollAlto='si' idZona='".$indice."' style='".(($numDiv++ == 0) ? "" : "display:none;")."text-align:justify'><b style='font-size: 8pt;'>".utf8_encode($valHallazgosDesOp['usuario'])."</b>:<br>".utf8_encode($valHallazgosDesOp['valor'])."</div>";
								$html .= "
								</td>
							</tr>
							<tr>
								<td class='fila1'>Otros procedimientos:</td><td colspan='3' class='fila2' style='vertical-align: text-top;font-size: 8pt;background-color:#F4CDCB'>";
								// --> Pintar tabs de otros procediminetos.
								$html .= "<table tabsIdZona=''>
										<tr style='font-size: 8pt;font-family: verdana;'>";
								$numCx = 1;
								foreach($otrosProced AS $indice => $valOtrosPro)
									$html .= "	<td idTabZona='".$indice."' onclick='verElemento(\"".$indice."\")' style='cursor:pointer;".(($numCx == 1) ? "color:#FFFFFF;background-color:#5DB9E8;" : "color:#2779AA;background-color:#EEF6FC;")."border-radius: 4px;border:1px solid #999999;padding:2px'>&nbsp;Otro procedimiento ".$numCx++."&nbsp;</td>";
								$html .= "	</tr>
									</table>";
								$numDiv = 0;
								foreach($otrosProced AS $indice => $valOtrosPro)
								{
									$html .= "
									<div scrollAlto='si' idZona='".$indice."' style='".(($numDiv++ == 0) ? "" : "display:none;")."text-align:justify'>";
										$primeraVez = true;
										$str 		= explode("<option", trim($valOtrosPro['valor']));
										foreach($str AS $valor)
										{
											if(trim($valor) == '')
												continue;

											$valor 		= "<option".$valor;
											$nomOtroPro = substr(strip_tags($valor), 3);
											$html .= (($primeraVez) ? "<b style='font-size: 8pt;'>".utf8_encode($valOtrosPro['usuario'])."</b>:<br>" : "")."&nbsp;- ".utf8_decode($nomOtroPro)."<br>";
											$primeraVez = (($primeraVez) ? false : false);
										}
									$html .="
									</div>";
								}
							$html .= "
								</td>
								<td class='fila1'>Vías de abordaje:</td><td colspan='3' class='fila2' style='font-size: 8pt;vertical-align: text-top;'>";
								// --> Pintar tabs de vías de abordaje.
								$html .= "<table tabsIdZona=''>
										<tr style='font-size: 8pt;font-family: verdana;'>";
								$numCx = 1;
								foreach($viasCx AS $indice => $valViasDesOp)
									$html .= "	<td idTabZona='".$indice."' onclick='verElemento(\"".$indice."\")' style='cursor:pointer;".(($numCx == 1) ? "color:#FFFFFF;background-color:#5DB9E8;" : "color:#2779AA;background-color:#EEF6FC;")."border-radius: 4px;border:1px solid #999999;padding:2px'>&nbsp;Descripción ".$numCx++."&nbsp;</td>";
								$html .= "	</tr>
									</table>";

								$numDiv = 0;
								foreach($viasCx AS $indice => $valViasDesOp)
									$html .= "<div scrollAlto='si' idZona='".$indice."' style='".(($numDiv++ == 0) ? "" : "display:none;")."text-align:justify'><b style='font-size: 8pt;'>".utf8_encode($valViasDesOp['usuario'])."</b>:<br>".utf8_encode($valViasDesOp['valor'])."</div>";
								$html .= "
								</td>
							</tr>
							<tr>
								<td class='fila1'>Nota operatoria:</td><td colspan='3' class='fila2' style='font-size: 8pt;vertical-align: text-top;'>
									<div scrollAlto='si' style='text-align:justify'>";
								foreach($arrNotaOpera AS $indice => $valNotaOpera)
									$html .= "<b style='font-size: 8pt;'>".utf8_encode($valNotaOpera['usuario'])."</b>:<br>".utf8_encode($valNotaOpera['valor'])."<br>";
								$html .= "
									</div>
								</td>
								<td class='fila1'>Justificaciones:</td><td colspan='3' class='fila2' style='font-size: 8pt;vertical-align: text-top;'>";

								// --> Pintar tabs de justificaciones de la cx.
								$numDiv = 0;
								$html .= "<div scrollAlto='si' style='text-align:justify'>";
								foreach($arrJustificaciones AS $indice => $valJustifi)
									$html .= "<b style='font-size: 8pt;'>".utf8_encode($valJustifi['usuario'])."</b>:<br>".utf8_encode($valJustifi['valor'])."<br>";
								$html .= "
									</div>
								</td>
							</tr>
							<tr>
								<td class='fila1'>Muestras para estudio:</td>
								<td colspan='3' class='fila2' align='center'>";
								if(count($arrayMuestras) > 0)
								{
									// --> Obtener encabezado de la tabla (Campo tipo grid para HCE)
									$htmlEncabezadoTabla = "<table style='margin:5px;font-family: Arial;border:1px solid;border-collapse: collapse;'>";
									$sqlEncTabla = "
									SELECT Detfor
									  FROM ".$wbasedatoHce."_000002
									 WHERE Detpro = '000107'
									   AND Detcon = '279'
									";
									$resEncTabla = mysql_query($sqlEncTabla, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlEncTabla):</b><br>".mysql_error());
									if($rowEncTabla = mysql_fetch_array($resEncTabla))
									{
										$htmlEncabezadoTabla.= "<tr class='encabezadoTablaHce' align='center'>";
										$encabezadoTabla = explode("*", $rowEncTabla["Detfor"]);
										$encabezadoTabla = explode("|", $encabezadoTabla[0]);
										foreach($encabezadoTabla as $titulo)
											$htmlEncabezadoTabla.= "<td style='border:1px solid #AFAFAF'><b>".$titulo."</b></td>";

										$htmlEncabezadoTabla.= "</tr>";
									}


									$html .= $htmlEncabezadoTabla;

									foreach($arrayMuestras as $infoMuestra)
									{
										$infoMuestra 	= explode("|", $infoMuestra);
										$filaHce		= (isset($filaHce) && $filaHce == "hceFila1") ? "hceFila2" : "hceFila1";

										$html .= "<tr class='".$filaHce."'>";
											foreach($infoMuestra as $datoMuestra)
												$html .= "<td style='border:1px solid #AFAFAF' align='center'>".utf8_encode(ucfirst(strtolower($datoMuestra)))."</td>";
										$html .= "</tr>";
									}
									$html .= "
									</table>";
								}

								$rutasProgramaOrdenesPaf 	= consultarAliasPorAplicacion($conex, $wemp_pmla, "rutasProgramaOrdenesPaf");
								$rutasProgramaOrdenesPaf	= explode('|', trim($rutasProgramaOrdenesPaf));
								$arrayRutaProgOrdenesPaf 	= array();
								foreach($rutasProgramaOrdenesPaf as $infoRutasPaf)
								{
									$infoRutasPaf = explode("=", $infoRutasPaf);
									$arrayRutaProgOrdenesPaf[trim($infoRutasPaf[0])] = trim($infoRutasPaf[1]);
								}
				$html .= "			</td>

								<td class='fila1'>Material de alto costo:</td>
								<td colspan='3' class='fila2' align='center'>";
								if(count($arrayMaterialAlto) > 0)
								{
									// --> Obtener encabezado de la tabla (Campo tipo grid para HCE)
									$htmlEncabezadoTabla = "<table style='margin:5px;font-family: Arial;border:1px solid;border-collapse: collapse;'>";
									$sqlEncTabla = "
									SELECT Detfor
									  FROM ".$wbasedatoHce."_000002
									 WHERE Detpro = '000107'
									   AND Detcon = '298'
									";
									$resEncTabla = mysql_query($sqlEncTabla, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlEncTabla):</b><br>".mysql_error());
									if($rowEncTabla = mysql_fetch_array($resEncTabla))
									{
										$htmlEncabezadoTabla.= "<tr class='encabezadoTablaHce' align='center'>";
										$encabezadoTabla = explode("*", $rowEncTabla["Detfor"]);
										$encabezadoTabla = explode("|", $encabezadoTabla[0]);
										foreach($encabezadoTabla as $titulo)
											$htmlEncabezadoTabla.= "<td style='border:1px solid #AFAFAF'><b>".$titulo."</b></td>";

										$htmlEncabezadoTabla.= "</tr>";
									}


									$html .= $htmlEncabezadoTabla;

									foreach($arrayMaterialAlto as $infoMuestra)
									{
										$infoMuestra 	= explode("|", $infoMuestra);
										$filaHce		= (isset($filaHce) && $filaHce == "hceFila1") ? "hceFila2" : "hceFila1";

										$html .= "<tr class='".$filaHce."'>";
											foreach($infoMuestra as $datoMuestra)
												$html .= "<td style='border:1px solid #AFAFAF' align='center'>".utf8_encode(ucfirst(strtolower(str_replace("Seleccione", "", $datoMuestra))))."</td>";
										$html .= "</tr>";
									}
									$html .= "
									</table>";
								}

								$rutasProgramaOrdenesPaf 	= consultarAliasPorAplicacion($conex, $wemp_pmla, "rutasProgramaOrdenesPaf");
								$rutasProgramaOrdenesPaf	= explode('|', trim($rutasProgramaOrdenesPaf));
								$arrayRutaProgOrdenesPaf 	= array();
								foreach($rutasProgramaOrdenesPaf as $infoRutasPaf)
								{
									$infoRutasPaf = explode("=", $infoRutasPaf);
									$arrayRutaProgOrdenesPaf[trim($infoRutasPaf[0])] = trim($infoRutasPaf[1]);
								}
				$html .= "			</td>
							</tr>
							<tr>
								<td class='fila1'>Enlaces:</td>
								<td colspan='3' class='fila2'>
									<button style='cursor:pointer;padding:1px;font-family: verdana;font-weight:bold;font-size: 8pt;' onclick='abrirHce(\"".$arrayCx['documento']."\", \"".$arrayCx['tipoDoc']."\", \"".$arrayCx['historia']."\", \"".$arrayCx['ingreso']."\")'>Historia Cl&iacute;nica</button>
									<button style='cursor:pointer;padding:1px;font-family: verdana;font-weight:bold;font-size: 8pt;' onclick='abrirDigitalizar(\"".$arrayCx['historia']."\", \"".$arrayCx['ingreso']."\")'>Documentos Escaneados</button>
									<button style='cursor:pointer;padding:1px;font-family: verdana;font-weight:bold;font-size: 8pt;' onclick='abrirBitacora(\"".$arrayCx['historia']."\", \"".$arrayCx['ingreso']."\")'>Bitácora</button>
									<button style='cursor:pointer;padding:1px;font-family: verdana;font-weight:bold;font-size: 8pt;' onclick='abrirResolucion()'>Resolución cups</button>";
								if(array_key_exists($nitEnt, $arrayRutaProgOrdenesPaf))
									$html .= "<button style='cursor:pointer;padding:1px;font-family: verdana;font-weight:bold;font-size: 8pt;' onclick='abrirOrdenesPaf(\"".$arrayCx['documento']."\", \"".$arrayRutaProgOrdenesPaf[$nitEnt]."\")'>Ordenes PAF</button>";
				$html .= "			<button style='cursor:pointer;padding:1px;font-family: verdana;font-weight:bold;font-size: 8pt;' onclick='mostrarCartaDerechos(this)' cartaDerechos='".$arrayCx['cartaDerechos']."' >Carta Derechos</button>
								</td>
							</tr>
						</table>
					</legend>
				</fieldset>
				<br>";
				
				// --> Pintar Cups
				$colorFila = 'fila2';
				$html .= "
				<table width='100%'>
					<tr>
						<td width='60%'>
							<fieldset align='center' style='padding:10px;' id='fieldProCups' scrollAlto2='si'>
								<legend class='fieldset'>Procedimientos cups</legend>
								<table width='100%' style='color: #848484;font-size: 8pt;font-family: verdana;'>
									<tr>
										<td align='left'>
											<b>Convenciones:</b>&nbsp;&nbsp;
											Código no existe en los maestros <img width='11' height='11' src='../../images/medical/sgc/Warning-32.png'>
											&nbsp;<b>|</b>&nbsp;
											Procedimiento adicionado <img width='11' height='11' src='../../images/medical/sgc/Mensaje_alerta.png'>
											&nbsp;<b>|</b>&nbsp;
											Usuario que ingresó autorización <img width='11' height='11' src='../../images/medical/root/info.png'>
										</td>
									</tr>
								</table>
								<table width='100%' id='tablaProcedCups'>
									<tr align='center' class='fila1' style='font-weight:bold'>
										<td>Código</td>
										<td>Nombre</td>
										<td>Confirmar</td>
										<td>Rechazar</td>
										<td>Convenios rechaza</td>
										<td>N°autorización</td>
										<td>Vía</td>
										<td>Bilateralidad</td>
										<td>Órgano</td>
									</tr>";

							// --> Recorrer cada procedimiento
							if(count($arrayCx['procedimientosCups']) > 0)
							{
								asort($arrayCx['procedimientosCups']);
								$consec = 1;
								foreach($arrayCx['procedimientosCups'] as $idReg => $infoPro)
								{
									$infoPro['clasifiPro'] = '*';

									// --> 	Consultar si el procedimiento existe en la 103 o en la 70 y obtener tipo de liquidación
									$datosPro		= array();
									$pedirCodHomo 	= false;

									$sqlCodHomo = "
									SELECT '' AS Proemppro, '' AS Proempnom, Pronom, Protfa AS tipoFac, Propun AS numUvr, Procod, Procpg, '103' AS tabla, '*' AS Proemporg
									  FROM ".$wbasedato."_000103
									 WHERE Procod = '".$infoPro['codigoPro']."'
									   AND Proest = 'on'
									 UNION
									SELECT Proemppro, Proempnom, Pronom, Proemptfa AS tipoFac, Proemppun AS numUvr, Procod, Procpg, '70' AS tabla, Proemporg
									  FROM ".$wbasedato."_000070 AS A INNER JOIN ".$wbasedato."_000103 AS B ON(Proempcod = Procod)
									 WHERE (Proempcod  = '".$infoPro['codigoPro']."' OR Proemppro  = '".$infoPro['codigoPro']."')
									   AND (Proempemp = '".(($infoSegRes['codEntidad'] != '') ? $infoSegRes['codEntidad'] : $arrayCx['codEntidad'])."' OR Proempemp = '*')
									   AND (Proemptip = '".(($infoSegRes['tipoEmp'] != '') ? $infoSegRes['tipoEmp'] : $arrayCx['tipoEmp'])."' OR Proemptip = '*')
									   AND (Proempcco = '".$arrayCx['ccoCx']."' OR Proempcco = '*')
									   AND Proempest  = 'on'
									";
									//seguimientoLog($sqlCodHomo);
									$resCodHomo = mysql_query($sqlCodHomo, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCodHomo):</b><br>".mysql_error());
									while($rowCodHomo = mysql_fetch_array($resCodHomo))
									{
										$organo 														= $rowCodHomo['Proemporg'];
										$rowCodHomo['tipoFac']											= explode("-", $rowCodHomo['tipoFac']);
										$rowCodHomo['tipoFac']											= $rowCodHomo['tipoFac'][0];
										$datosPro[$rowCodHomo['tabla']][$organo]['codigoCups'] 			= $rowCodHomo['Procod'];
										$datosPro[$rowCodHomo['tabla']][$organo]['nombrePro'] 			= utf8_encode($rowCodHomo['Pronom']);
										$datosPro[$rowCodHomo['tabla']][$organo]['nombreProEntidad'] 	= utf8_encode($rowCodHomo['Proempnom']);
										$datosPro[$rowCodHomo['tabla']][$organo]['codigoProEntidad'] 	= $rowCodHomo['Proemppro'];
										$datosPro[$rowCodHomo['tabla']][$organo]['clasifiPro']			= $rowCodHomo['Procpg'];
										$datosPro[$rowCodHomo['tabla']][$organo]['tipoFac'] 			= $rowCodHomo['tipoFac'];
										$datosPro[$rowCodHomo['tabla']][$organo]['numUvr']				= $rowCodHomo['numUvr'];
									}

									$selectOrg 		= "<option></option>";
									$mostrarSelect	= false;

									if(mysql_num_rows($resCodHomo) > 0)
									{
										if(isset($datosPro['70']))
										{
											if(count($datosPro['70']) > 1)
											{
												foreach($datosPro['70'] as $codOrg => $infoValPro)
												{
													$infoPro['codigoPro']		= $infoValPro['codigoCups'];
													$infoPro['nombreProCups'] 	= $infoValPro['nombrePro'];
													$infoPro['numUvr']			= (($infoValPro['numUvr'] > 0) ? $infoValPro['numUvr'] : $datosPro['103']["*"]['numUvr']);

													$mostrarSelect = true;

													$tooltipSelect = "
													<span style=font-weight:normal;>
														<b>Tipo de Liquidación:</b> ".$infoValPro['tipoFac'].(($infoValPro['tipoFac'] == "UVR") ? " (".$infoPro['numUvr'].")" : "").((trim($infoValPro['nombreProEntidad']) != '') ? "<br><b>Codigo propio:</b> ".$infoValPro['codigoProEntidad']."<br><b>Nombre propio:</b> ".$infoValPro['nombreProEntidad'] : "")."</span>";

													$selectOrg.= "
													<option value='".$codOrg."' tooltSelect='".$tooltipSelect."' ".(($infoPro['organo'] == $codOrg) ? "SELECTED" : "").">".$arrayOrganos[$codOrg]."</option>
													";

													if($infoPro['organo'] == $codOrg)
													{
														$infoPro['nombreProEntidad']= $infoValPro['nombreProEntidad'];
														$infoPro['codigoProEntidad']= $infoValPro['codigoProEntidad'];
														$infoPro['clasifiPro']		= $infoValPro['clasifiPro'];
														$infoPro['tipoLiq']			= $infoValPro['tipoFac'];
														$infoPro['numUvr']			= (($infoValPro['numUvr'] > 0) ? $infoValPro['numUvr'] : $datosPro['103']["*"]['numUvr']);
													}
												}
											}
											else
											{
												foreach($datosPro['70'] as $codOrg => $infoValPro)
												{
													$infoPro['codigoPro']		= $infoValPro['codigoCups'];
													$infoPro['nombreProCups'] 	= $infoValPro['nombrePro'];
													$infoPro['nombreProEntidad']= $infoValPro['nombreProEntidad'];
													$infoPro['codigoProEntidad']= $infoValPro['codigoProEntidad'];
													$infoPro['clasifiPro']		= $infoValPro['clasifiPro'];
													$infoPro['tipoLiq']			= $infoValPro['tipoFac'];
													$infoPro['numUvr']			= (($infoValPro['numUvr'] > 0) ? $infoValPro['numUvr'] : $infoValPro['numUvr']);

													$selectOrg = "
													<option value='".$codOrg."'>".$arrayOrganos[$codOrg]."</option>
													";
												}
											}
										}
										else
										{
											$infoPro['codigoPro']		= $datosPro['103']["*"]['codigoCups'];
											$infoPro['nombreProCups'] 	= $datosPro['103']["*"]['nombrePro'];
											$infoPro['nombreProEntidad']= "";
											$infoPro['codigoProEntidad']= "";
											$infoPro['clasifiPro']		= $datosPro['103']["*"]['clasifiPro'];
											$infoPro['tipoLiq']			= $datosPro['103']["*"]['tipoFac'];
											$infoPro['numUvr']			= $datosPro['103']["*"]['numUvr'];

											$selectOrg = "
											<option value='*'>NO APLICA</option>
											";
										}
									}

									// echo "<pre>";
									// print_r($infoPro);
									// print_r($datosPro);
									// echo "</pre>";


									// --> Obtener si el procedimiento es NO POS.
									$sqlNoPos = "
									SELECT id
									  FROM ".$wbasedato."_000249
									 WHERE Pnppro  = '".$infoPro['codigoPro']."'
									   AND (Pnpemp = '".(($infoSegRes['codEntidad'] != '') ? $infoSegRes['codEntidad'] : $arrayCx['codEntidad'])."' OR Pnpemp = '*')
									   AND Pnppnp = 'on'
									   AND Pnpest = 'on'
									";
									$resNoPos = mysql_query($sqlNoPos, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlNoPos):</b><br>".mysql_error());
									if(mysql_fetch_array($resNoPos))
										$infoPro['tipoLiq'] = 'A TARIFA PROPIA';

									if(!array_key_exists('tipoLiq', $infoPro)){
										$infoPro['tipoLiq'] = '';
									}

									if(mysql_num_rows($resCodHomo) == 0)
									{
										$pedirCodHomo = true;
										// --> Obtener el nombre del procedimiento en el maestro de codigos propios
										/*$sqlNomProPro = "
										SELECT Cprnom
										  FROM ".$wbasedato."_000254
										 WHERE Cprcod = '".$infoPro['codigoPro']."' ";
										$resNomProPro = mysql_query($sqlNomProPro, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlNomProPro):</b><br>".mysql_error());
										if($rowNomProPro = mysql_fetch_array($resNomProPro))
											$infoPro['nombrePro'] = $rowNomProPro['Cprnom'];*/
									}

									// --> Consultar si el procedimiento requiere autorizacion
									if($arrayCx['segundoRespAutor'] != '')
										$pideAutorizacion = true;
									else
										$pideAutorizacion = procedimientoSeDebeAutorizar($codEnt, $nitEnt, $tipEnt, $planEmp, $tarifaEnt, $infoPro['clasifiPro'], $infoPro['codigoPro']);

									// --> Pintar procedimiento
									$html .= "
									<tr style='font-size: 8pt;padding:1px;' procedimiento='' ".((isset($infoPro['nuevoDesdeDesOp'])) ? "nuevoDesdeDesOp='".$infoPro['codigoPro']."'" : "")." class='".$colorFila."'>";
									if($pedirCodHomo)
									{
										$html .= "
										<td align='center' faltaHomolog='si'><img width='13' height='13' src='../../images/medical/sgc/Warning-32.png'>&nbsp;".$infoPro['codigoPro']."</td>";
									}
									elseif(array_key_exists("UsuAdiciono", $infoPro))
										{
											if(array_key_exists("proAdicionado", $infoPro) && $infoPro['proAdicionado'] == 'on')
												$nomUsuAdi = "Adicionado por: ";
											else
												$nomUsuAdi = "";
											
											// --> Consultar usuario que adicionó el procedimiento
											$sqlUsu = "
											SELECT Descripcion
											  FROM usuarios
											 WHERE Codigo = '".str_replace("C-", "", $infoPro['UsuAdiciono'])."' ";

											$resUsu = mysql_query($sqlUsu, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUsu):</b><br>".mysql_error());
											if($rowUsu = mysql_fetch_array($resUsu))
												$nomUsuAdi.= $rowUsu['Descripcion'];
											else
												$nomUsuAdi.= "?";

											$html .= "
											<td align='center' medicoRealizo='".$infoPro['UsuAdiciono']."' ><img style='cursor:help' tooltip='si' title='<span style=font-weight:normal;>".utf8_encode(ucfirst(strtolower($nomUsuAdi)))."</span>' width='13' height='13' src='../../images/medical/sgc/Mensaje_alerta.png'>&nbsp;".$infoPro['codigoPro']."</td>";
										}
										else
										{
											$html .= "
											<td align='center'>".$infoPro['codigoPro']."</td>";
										}

										if(!isset($infoPro['nombreProCups'])){
											$infoPro['nombreProCups'] = '';
										}

									$html .= "
										<td>
											<span tooltSelect>
												<img style='cursor:help' tooltip='si' title='<span style=font-weight:normal;><b>Tipo de Liquidación:</b> ".$infoPro['tipoLiq'].(($infoPro['tipoLiq'] == "UVR") ? " (".$infoPro['numUvr'].")" : "").((isset($infoPro['nombreProEntidad']) && trim($infoPro['nombreProEntidad']) != '') ? "<br><b>Codigo propio:</b> ".$infoPro['codigoProEntidad']."<br><b>Nombre propio:</b> ".$infoPro['nombreProEntidad'] : "")."</span>' width='13' height='13' src='../../images/medical/sgc/info_black.png'>
											</span>
											".$infoPro['nombreProCups']."
										</td>
										<td align='center'><input type='radio' style='cursor:pointer' name='".$idReg."' confirmar=''  ".(($modoConsulta == 'on') ? "disabled='disabled'" : "")."	".(($infoPro['proConfir'] == 'on') ? "checked='checked'" : "")."></td>
										<td align='center'><input type='radio' style='cursor:pointer' name='".$idReg."' rechazar=''	  ".(($modoConsulta == 'on') ? "disabled='disabled'" : "")." 	".(($infoPro['proRechaza'] == 'on') ? "checked='checked'" : "")."></td>

										<td align='center'><input type='checkbox' style='cursor:pointer' name='".$idReg."' negar_vobo='' disabled='disabled' ".((isset($infoPro['negar_vobo']) && $infoPro['negar_vobo'] == 'off') ? "checked='checked'" : "")."></td>

										";

										$numAutoDesdeAdmision = '';
										if($infoPro['numAutorizacion'] == '' || $infoPro['usuIngresoAut'] == '')
										{
											// --> Consultar si el procedimiento tiene autorizacion en la admision
											$sqlProAutAdm = "
											SELECT Cpraut
											  FROM ".$wbasedato."_000209
											 WHERE Cprhis = '".$arrayCx['historia']."'
											   AND Cpring = '".$arrayCx['ingreso']."'
											   AND Cprnit = '".(($infoSegRes['codEntidad'] != '') ? $infoSegRes['codEntidad'] : $arrayCx['codEntidad'])."'
											   AND Cprcup = '".$infoPro['codigoPro']."'
											   AND Cprest = 'on'
											";
											$resProAutAdm = mysql_query($sqlProAutAdm, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlProAutAdm):</b><br>".mysql_error());
											if($rowProAutAdm = mysql_fetch_array($resProAutAdm))
											{
												$numAutoDesdeAdmision			= $rowProAutAdm['Cpraut'];
												$infoPro['numAutorizacion']		= $rowProAutAdm['Cpraut'];
												$infoPro['nomUsuIngresoAut']	= 'Desde Admisiones';
											}
										}

										// --> Obtengo el nombre del usuario que ingresó el numero de autorización
										if(array_key_exists('usuIngresoAut', $infoPro) && $infoPro['usuIngresoAut'] != '')
										{
											$infoPro['nomUsuIngresoAut'] = nombreDeUsuario($hisUsuarios, $infoPro['usuIngresoAut']);
											if($infoPro['nomUsuIngresoAut'] == "")
												$infoPro['nomUsuIngresoAut'] = "?";
										}

										if($pideAutorizacion || $requiere_autorizar_liquidado == 'on')
										// if(true)
										{
											$nomUsuIngresoAut = (isset($infoPro['nomUsuIngresoAut'])) ? ucfirst(strtolower($infoPro['nomUsuIngresoAut'])):'';
											$html .= "
											<td align='center' nowrap>
												<input type='text' numAutorizacion='".$consec."'
														numAutoDesdeAdmision='".$numAutoDesdeAdmision."' ".((isset($infoPro['usuIngresoAut']) && $infoPro['usuIngresoAut'] != '' && isset($infoPro['usuIngresoAut']) && $infoPro['usuIngresoAut'] != $wuse) ? "readonly" : "")."
														title='Doble click para traer valor anterior' placeholder='Digite o Doble click' value='".$infoPro['numAutorizacion']."'
														style='border-radius: 4px;border:1px solid #AFAFAF;width:130px'
														requiere_autorizar_liquidado='".$requiere_autorizar_liquidado."'
														onkeyup='setEditado(this);'
														ondblclick='copiarNumAuto(\"".$consec."\", \"numAutorizacion\")'>
												".(($nomUsuIngresoAut != "") ? "<img style='cursor:help' tooltip='si' title='<span style=font-weight:normal;>".ucwords(strtolower(utf8_encode($nomUsuIngresoAut)))."</span>' width='13' height='13' src='../../images/medical/root/info.png'>": "")."
											</td>";
											$consec++;
										}
										else{
											$html .= "<td>".$infoPro['numAutorizacion']."</td>";
										}
									$html .= "
										<td>
											<select selectVia='' ".(($modoConsulta == 'on') ? "disabled='disabled'" : "").">
												<option></option>";
											$j = 0;
											while($j<count($arrayCx['procedimientosCups']))
												$html .= "<option ".((++$j == $infoPro['Via']) ? "SELECTED" : "" ).">".$j."</option>";
									$html .= "	</select>
										</td>";
									
									$arrOpcBilateral = array("NA"=>"No aplica", "off" => "Unilateral", "on" => "Bilateral");
									$html .= "
										<td align='center'>
											<select ".(($modoConsulta == 'on') ? "disabled='disabled'" : "")." checkBilateral=''>
												<option></option>";
											foreach($arrOpcBilateral as $codBi => $nomBi)
												$html .= "<option value='".$codBi."' ".(($infoPro['Bilateral'] == $codBi) ? "selected" : "").">".$nomBi."</option>";
									$html .= "</select>
										</td>
										<td align='center'>
											<select selectOrg='' onChange='cargarTooltSelect(this)' ".(($modoConsulta == 'on') ? "disabled='disabled'" : "")." style='".((!$mostrarSelect) ? "display:none" : "" )."'>
												".$selectOrg."
											</select>
										</td>
									</tr>";
								}
							}
				$html .= "				<tr><td align='center' colspan='5'><button id='btn_agregar_procedimiento' ".(($modoConsulta == 'on') ? "disabled='disabled'" : "")." style='cursor:pointer;padding:1px;font-family: verdana;font-weight:bold;font-size: 8pt;' onclick='agregarProcedimiento()'>Adicionar</button></td></tr>
								</table>
							</fieldset>
						</td>
						<td>
							<fieldset align='center' style='padding:10px;font-size: 9pt;' id='fieldMedAuto' scrollAlto2='si'>
								<legend class='fieldset'>Insumos que requieren autorización</legend>
								<table width='100%'>
									<tr align='center' class='fila1' style='font-weight:bold'><td>Código</td><td>Nombre</td><td>Cant</td><td>N°autorización</td></tr>";
								$consec2 = 1;
								foreach($arrArtAuto as $codArt => $nomArt)
								{
									if($arrMercado[$codArt]['usuAuto'] != '')
										$nomUsuAutArt = nombreDeUsuario($hisUsuarios, $arrMercado[$codArt]['usuAuto']);
									else
										$nomUsuAutArt = "";

									$html .= "
									<tr class='fila2'>
										<td align='center'>".$codArt."</td>
										<td>".utf8_encode($nomArt)."</td>
										<td align='center'>".$arrMercado[$codArt]['saldo']."</td>
										<td align='center'>
											<input type='text' numAutorizacionInsu='".$consec2."'
												idMercadoInsumo='".$arrMercado[$codArt]['idMercado']."'
												value='".$arrMercado[$codArt]['numAuto']."'
												".(($arrMercado[$codArt]['usuAuto'] != '' && $arrMercado[$codArt]['usuAuto'] != $wuse) ? "readonly" : "")."
												title='Doble click para traer valor anterior'
												placeholder='Digite o Doble click'
												style='border-radius: 4px;border:1px solid #AFAFAF;width:140px'
												ondblclick='copiarNumAuto(\"".$consec2."\", \"numAutorizacionInsu\")'
												requiere_autorizar_liquidado='".$requiere_autorizar_liquidado."'
												onkeyup='setEditado(this);'>
											".(($nomUsuAutArt != "") ? "<img style='cursor:help' tooltip='si' title='<span style=font-weight:normal;>".ucwords(strtolower(utf8_encode($nomUsuAutArt)))."</span>' width='13' height='13' src='../../images/medical/root/info.png'>": "")."
										</td>
									</tr>
									";
									$consec2++;
								}
								if(count($arrArtAuto) == 0)
									$html .= "<tr class='fila2'><td align='center' colspan='4'>Sin registros</td></tr>";
				$html .= "				</table>
							</fieldset>
						</td>
					<tr>
				</table>";
				
				// --> Pintar seccion de observaciones
				$sqlNomUsu = "
				SELECT Descripcion
				  FROM usuarios
				 WHERE Codigo = '".$wuse."' ";
				$resNomUsu = mysql_query($sqlNomUsu, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlNomUsu):</b><br>".mysql_error());
				if($rowNomUsu = mysql_fetch_array($resNomUsu))
					$nomUsuario = utf8_encode(ucfirst(strtolower($rowNomUsu['Descripcion'])));
				else
					$nomUsuario = "";

				$html .= "
				<br>
				<table width='100%'>
					<tr>
						<td width='60%'>
							<fieldset align='center' style='padding:10px;font-size: 9pt;' id='fieldObservaciones' scrollAlto2='si'>
								<legend class='fieldset'>Observaciones</legend>
								<table width='100%' style='font-size: 9pt;'>
									<tr><td><b>Nueva:</b></td><td></td><td><b>Histórico:</b></td></tr>
									<tr>
										<td width='49%' align='center'>
											<textarea align='left' id='inputNuevaObserv' ".(($modoConsulta == 'on') ? "readonly=''" : "")." usuario='".$nomUsuario."' fechaYhora='".date("Y-m-d")." ".date("H:i:s")."' placeholder='Digite la nueva observacion' style='height:80px;border-radius: 4px;border:1px solid #AFAFAF;width:350px'></textarea>
										</td>
										<td align='center'>
											<button ".(($modoConsulta == 'on') ? "disabled='disabled'" : "")." id='btn_enviar_obs' style='cursor:pointer;padding:1px;font-family: verdana;font-weight:bold;font-size: 8pt;' onclick='enviarObservacion(\"".$turno."\")'>Enviar</button>
										</td>
										<td width='49%' align='center'>";
											//echo $arrayCx['observaciones'];
											$hisObserv 	= $arrayCx['observaciones'];
											$mensajeHis = "";
											if($hisObserv == "")
												$hisObserv = json_encode(array());
											else
											{
												$color = "#2a5db0";
												foreach(json_decode(utf8_encode($hisObserv), true) as $infoObserva)
												{
													$color = (($color == "#2a5db0") ? "#7588A3" : "#2a5db0");
													$mensajeHis.= "<span style='color:".$color."'>&nbsp;".$infoObserva["usuario"]." (".$infoObserva["fechaYhora"]."): </span>".$infoObserva["mensaje"]."<br>";
												}
											}

							$html .= "			<input type='hidden' id='hisObserv' value='".utf8_encode($hisObserv)."'>
											<div id='mensajeHis' align='left' style='resize: both;height:80px;overflow:auto;background: #ffffff none repeat scroll 0 0;font-size: 8pt;border-radius: 4px;border:1px solid #AFAFAF;width:350'>".$mensajeHis."</div>
										</td>
									</tr>
								</table>
							</fieldset>
						</td>
						<td>
							<fieldset align='center' style='padding:10px;font-size: 9pt;' id='fieldMovimientos' scrollAlto='si'>
								<legend class='fieldset'>Movimientos</legend>
								".logMovimientosCx($turno)."
							</fieldset>
						</td>
					</tr>
			</div>
			<div id='divCartaDerechos' style='display:none' align='center'>
			</div>
			";

			$data["html"] = $html;
			echo json_encode($data);
			break;
			return;
		}

		case 'guardar_error_ajax_log':
			$data = array('error'=>0,'mensaje'=>'','html'=>'');
			echo "ok";
            $msg_fail = utf8_decode($msg_fail.PHP_EOL.$_SERVER['SCRIPT_FILENAME'].PHP_EOL.PHP_EOL.$data_parcial);
            $detalle_error = utf8_decode($detalle_error);

            $nombre_script_error = $_SERVER['SCRIPT_NAME'];

            registroLogError($conex, $wbasedato, $msg_fail, "matrix_ajax", $detalle_error, $url_err, $nombre_script_error);
            echo json_encode($data);
            return;
        break;

		case 'ocultar_turno_monitor':
			$data = array('error'=>0,'mensaje'=>'','html'=>'');
			$user_session      = explode('-',$_SESSION['user']);
			$user_session      = $user_session[1];

			$fecha_hora_reg = date("Y-m-d H:i:s");
            $sqlSM = "  UPDATE  {$wbasedato}_000252
				            SET Aueman = 'on',
				                Auefho = '{$fecha_hora_reg}',
				                Aueuso = '{$user_session}'
				        WHERE   Auetur = '{$wcodigoturno}'";
	        $data["query"] = $sqlSM;
			if($result252= mysql_query($sqlSM,$conex))
			{
			}
			else
			{
				$data["error"] = 1;
				$data["mensaje"] = "No se pudo ocultar el turno";
				// $data["mensaje"] = "No se pudo eliminar los lotes relacionados";
				$data["Query_Err"] = "Error: ".mysql_errno()." - en el query  ".$sqlSM." - ".mysql_error();
			}
			echo json_encode($data);
			return;
        break;

		case 'cambiarResponsableEstancia' :
		{
			$data = cambiarResponsableEstancia($whistoria,$wingreso,$wresponsable,$wresseleccionado,$wresanterior,$wbasedato,$wdatofecha,$wtextareapaf);
			echo json_encode($data);
			break;
			return ;
		}

		case 'historialPacientesPaf' :
		{
			$data = historialPacientesPaf($whistoria,$wingreso,$wbasedato);
			echo $data;
			break;
			return;
		}

		case 'verificarCambioResponsable':
		{
			$select = "SELECT  Ingcem
						 FROM ".$wbasedato."_000101
						WHERE Inghis  = '".$whistoria."'
						  AND Ingnin  = '".$wingreso."'";

			$res = mysql_query($select,$conex) or die ("Query : ".$select." - ".mysql_error());
			$html .= $select;
			$html .= "<br>";
			$par101 = 'no';
			while ($row = mysql_fetch_array($res))
			{
				if($wresseleccionado == $row['Ingcem'])
				{
					$par101 = 'si';
				}
				$responsableactualcodigo = $row['Ingcem'];
			}

			$select_res = "SELECT Empcod,Empnom AS Nombre, Emptar
							 FROM ".$wbasedato."_000024
							WHERE Empcod = '".$responsableactualcodigo."'";

			$res = 	mysql_query($select_res,$conex) or die ("Error 1: ".mysql_errno()." - en el query: ".$select_res." - ".mysql_error());
			while($row = mysql_fetch_array($res))
			{
				$nombreresactual = $row['Nombre'];
			}

				//---Traigo nombre responsable al que debe ser cambiado
			$select_res = "SELECT Empcod,Empnom AS Nombre, Emptar ,Empema
								 FROM ".$wbasedato."_000024
								WHERE Empcod = '".$wresseleccionado."'";

			$res = 	mysql_query($select_res,$conex) or die ("Error 1: ".mysql_errno()." - en el query: ".$select_res." - ".mysql_error());
			$emailseleccionado='';
			while($row = mysql_fetch_array($res))
			{
				$nombrerescambiado = $row['Nombre'];
				$emailseleccionado = $row['Empema'];
			}




			$select = "SELECT Resnit
						 FROM ".$wbasedato."_000205
						WHERE Reshis='".$whistoria."'
						  AND Resing='".$wingreso."'
						  AND Resord='1'";

			$res = mysql_query($select,$conex) or die ("Query : ".$select." - ".mysql_error());
			$par205 = 'no';
			$html .= $select;
			$html .= "<br>";
			while ($row = mysql_fetch_array($res))
			{
				if($wresseleccionado == $row['Resnit'])
				{
					$par205 = 'si';
				}
			}

			$html .= $wresseleccionado;
			$html .= "<br>";


			//----------
			$select = "SELECT  CONCAT (Pacno1 ,' ',Pacno2,' ',Pacap1,' ',Pacap2) as Nombre
							 FROM ".$wbasedato."_000100
							WHERE Pachis  = '".$whistoria."' ";

				$res = mysql_query($select,$conex) or die ("Query : ".$select." - ".mysql_error());

				if ($row = mysql_fetch_array($res))
				{
					$nombre = $row['Nombre'];
				}
				//--- envio correo
				$wcorreopmla = consultarAliasPorAplicacion( $conex, $wemp_pmla, "emailEnvioCambioResponsable");
				$wcorreopmla = explode("--", $wcorreopmla );
				$wpassword   = $wcorreopmla[1];
				$wremitente  = $wcorreopmla[0];
				$datos_remitente = array();
				$datos_remitente['email']	= $wremitente;
				$datos_remitente['password']= $wpassword;
				$datos_remitente['from'] = $wremitente;
				$datos_remitente['fromName'] = $wremitente;


				$message  = "<html><body>";

				$message .= "<table width='100%' bgcolor='#e0e0e0' cellpadding='0' cellspacing='0' border='0'>";

				$message .= "<tr><td>";

				$message .= "<table align='center' width='100%' border='0' cellpadding='0' cellspacing='0' style='background-color:#fff; font-family:Verdana, Geneva, sans-serif;'>";

				$message .= "<thead>
								<tr height='80' align='left'>
									<th colspan='4' style='background-color:#ffffff; border-bottom:solid 1px #bdbdbd; font-family:Verdana, Geneva, sans-serif; color:#333; font-size:30px;' >
									<img width='110px' height='70px' src='../../images/medical/root/clinica.JPG'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Cambio de Responsable</th>
								</tr>
							 </thead>";

				$message .= "<tbody>
							   <tr>
							   <td colspan='4' style='padding:15px;'>
							   <p style='font-size:18px;'>Buenos dias, el paciente con historia ".$whistoria."-".$wingreso."  ".$nombre." debe cambiarse de responsable desde la fecha: ".$wdatofecha."</p>
							   </tr>
							   <tr>
									<td>
									<table align='center'>
									 <tr align='center' style='background-color: #2a5db0; color: #ffffff; font-size: 14pt; padding:1px; font-family: verdana; fond-weight: bold;'><td>Responsable actual</td><td>Responsable al que debe cambiar</td></tr>
									 <tr style='background-color: #C3D9FF; color: #000000; font-size: 10pt; padding:1px; font-family: verdana;'><td>".$responsableactualcodigo." ".$nombreresactual."</td><td>".$wresseleccionado." ".$nombrerescambiado."</td></tr>
									</table>
									</td>
							   <tr>
							   <tr>
							   <td style='font-size: 14pt;' ><br>Texto del Medico: ".$wtextareapaf."</td>
							   </tr>

							   <tr height='80'>
							   <hr />
							   <td colspan='4' align='center' style='background-color:#f5f5f5; border-top:dashed #00a2d1 2px; font-size:24px; '>
							   </td>
							   </tr>
							</tbody>";

				$message .= "</table>";

				$message .= "</td></tr>";
				$message .= "</table>";

				$message .= "</body></html>";
				//$mensaje .= "El paciente con historia :".$whistoria."-".$wingreso."  Se debe de cambiar de responsable  por : ".$wresseleccionado;

				// if (!$tieneResponsable)
					// $mensaje .= "<br> Debe agregarse este responsable en la admision";
				$mensaje = $message;
				//$mensaje = "esto es una prueba";
				$wdestinatarios	= consultarAliasPorAplicacion( $conex, $wemp_pmla, "emailCambioResponsable");

				if($emailseleccionado !='')
				{
				  $wdestinatarios = $wdestinatarios.",".$emailseleccionado;
				}


				$wdestinatarios = explode(",",$wdestinatarios);
				$wasunto 		= "Cambio de Reponsable paciente:".$whistoria;
				$altbody 		= "Cordial saludo,<br> \n\n Cambio de  responsable";
				sendToEmail($wasunto,$mensaje,$altbody, $datos_remitente, $wdestinatarios );

			//-------
			// echo $html;
			if( $par205 =='si' && $par101 == 'si' )
			{
				echo 'si';
				//---------------------
			}
			else
				echo 'no';

			break;
			return;
		}
		case 'cambiarselectresponsable':
		{
			$select = "SELECT Empcod, Empnit ,Empnom
						 FROM ".$wbasedato."_000024
						WHERE Emppaf='on'
						  AND Empest='on'
						  AND Empcod='".$wresseleccionado."'";
			$res = mysql_query($select,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$select." - ".mysql_error());
			while($row = mysql_fetch_array($res))
			{
				$nit = $row['Empnit'];
			}

			// Se consultan las empresar que  pueden sufrir  cambios segun lo estipula el medico, consulta a la tabla de empresas
			$select = "SELECT Empcod, Empnit ,Empnom
						 FROM ".$wbasedato."_000024
						WHERE Empnit = '".$nit."'
						  AND Empest='on'
						  AND Emppaf='on'";


			//$htmlselect .=$select;
			$res = mysql_query($select,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$select." - ".mysql_error());
			$num = mysql_num_rows($res);
			$codigosempr = array();
			$vectornits = array();
			$codigos = '';
			while($row = mysql_fetch_array($res))
			{

				$codigosempr[$row['Empcod']] = $row['Empcod'];
				$vectornits[$row['Empnit']][$row['Empcod']]=$row['Empcod']."-".$row['Empnom'];
				//$nit = $row['Empnit'];
				//$htmlselect .= $nit;
				//$codigos = $codigos.",'".$row['Empcod']."'";

			}



			//$codigos =substr($codigos,1);
			//$habilitado = '';
			$htmlselect .="<input type='hidden' id='valAnterior_".$whistoria."' value='".$wresseleccionado."'><select id='select_".$whistoria."' style='width:95%' onchange='Llamarfecha(".$whistoria." , ".$wingreso.")'>";
			$htmlselect .="<option value='' checked>Seleccione...</option>";
			foreach( $vectornits[$nit]  as $clave=>$value )
			{
				if($clave === $wresseleccionado)
				{
					//$htmlselect .= "<option selected value='".$clave."'>".$value."</option>";
				}
				else
				{
					$htmlselect .= "<option value='".$clave."'>".$value."</option>";
				}

			}
			$htmlselect .="<select>";
			echo $htmlselect;
			break;
			return;
		}

		case 'omitirRegrabacion':
		{
			$sqlOmit = "
			UPDATE ".$wbasedato."_000106
			   SET Tcarreg = 'OMM'
			 WHERE Tcarhis = '".$historia."'
			   AND Tcaring = '".$ingreso."'
			   AND Tcarreg = 'pen'
			   AND Tcarest = 'on'
			";
			mysql_query($sqlOmit,$conex) or die("Error en el query: ".$sqlOmit."<br>Tipo Error:".mysql_error());

			$sqlActAlerta = "
			UPDATE ".$wbasedato."_000282
			   SET Preest = 'off'
			 WHERE Prehis = '".$historia."'
			   AND Preing = '".$ingreso."'
			";
			mysql_query($sqlActAlerta,$conex) or die("Error en el query: ".$sqlActAlerta."<br>Tipo Error:".mysql_error());

			break;
			return;
		}

		case 'consultar_empleados_por_rol':
			$data = array("html"=>"","mensaje"=>"","error"=>0,"sql"=>"","arr_ampleados_mercado"=>array());
			$rolesRecibenMercadosCirugia_ERP = consultarAliasPorAplicacion($conex, $wemp_pmla, "rolesRecibenMercadosCirugia_ERP"); //.',034';
			$rolesInstrumentadores_ERP       = consultarAliasPorAplicacion($conex, $wemp_pmla, "rolesInstrumentadores_ERP"); //.',034';
			$wbasedatoHce                    = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');

			if($tipo_usu == "instru_"){
				$rolesRecibenMercadosCirugia_ERP = $rolesInstrumentadores_ERP;
			}

			$rolesRecibenMercadosCirugia_ERP = explode(",", $rolesRecibenMercadosCirugia_ERP);
			$implode_roles = implode("','", $rolesRecibenMercadosCirugia_ERP);

			$arr_ampleados_mercado = array();
			$sql = "SELECT  us.Codigo, us.Descripcion, us.Ccostos
					FROM    usuarios AS us
							INNER JOIN
							{$wbasedatoHce}_000020 as hce20 ON (us.Codigo = hce20.Usucod AND hce20.Usurol IN ('{$implode_roles}'))
					WHERE   us.Activo = 'A'
					        AND us.Empresa = '{$wemp_pmla}'
					        AND us.Descripcion <> ''
					        AND (us.Codigo*1) > 0
					ORDER BY us.Descripcion";
			if($result = mysql_query($sql,$conex))
			{
				while ($row = mysql_fetch_assoc($result))
				{
					$nombre_emp = trim(preg_replace('/[ ]+/', ' ', $row["Descripcion"]));
					$arr_ampleados_mercado[$row["Codigo"]] = utf8_encode($nombre_emp)." - Cco: ".$row["Ccostos"];
				}
			}
			else
			{
				$data["error"] = 1;
				$data["sql"] = "Error en el query: ".$sql."<br>Tipo Error:".mysql_error();
			}

			$data["arr_ampleados_mercado"] = $arr_ampleados_mercado;
			echo json_encode($data);
			break;

		case 'consultar_foto_empleado':
			$data = array("html"=>"","mensaje"=>"","error"=>0,"sql"=>"");
			$foto = '';

			$empleado_talhuma = empresaEmpleado($wemp_pmla, $conex, '', $codigo_empleado);
			$wbasedato_talhuma = consultarAliasPorAplicacion($conex, $wemp_pmla, 'talhuma');

			$q = "  SELECT  Ideuse AS codigo, Ideced AS cedula, Idecco AS ccosto, Ideest AS estado, Idegen AS genero, Ideafb AS autoriza_ver_foto
		            FROM    {$wbasedato_talhuma}_000013
		            WHERE   Ideuse = '{$empleado_talhuma}'
		            ORDER BY Ideno1, Ideno2, Ideap1";
		    $res = mysql_query($q,$conex);

		    while ($row = mysql_fetch_array($res))
		    {
		    	$row['cedula'] = ($row['autoriza_ver_foto'] == 'off') ? '' : $row['cedula'];
				$foto = '<img class="imagen" src="'.getFoto($row['cedula'],$row['genero']).'"/>';
		    }

			$data["foto"] = $foto;
			echo json_encode($data);
			break;

	}
	return;

//=======================================================================================================================================================
//		F I N   F I L T R O S   A J A X
//=======================================================================================================================================================


//=======================================================================================================================================================
//	I N I C I O   E J E C U C I O N   N O R M A L   D E L   P R O G R A M A
//=======================================================================================================================================================
}
else
{
	$wbasedatomovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	$sql_cir = "SELECT  Ccocod, Cconom
				FROM 	{$wbasedatomovhos}_000011
				WHERE   Ccofac = 'on'
					    AND Ccocir = 'on'
					    AND Ccoest = 'on'";
	$result_cir = mysql_query($sql_cir,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$sql_cir." - ".mysql_error());
	$opstions_cco_cir = '<option value="">Seleccione</option>';
	while($row_cir = mysql_fetch_array($result_cir))
	{
		$opstions_cco_cir .= '<option value="'.$row_cir["Ccocod"].'">'.$row_cir["Ccocod"].'-'.utf8_encode($row_cir["Cconom"]).'</option>';
	}

	$sql_rol = "SELECT  Rolcod, Roldes, Roldcn, Rolclr, Rolbtn, Roladp, Roltbi, Rolfbr, Rolbet, Rolfcr, Rolltr, Rollsr, Rolmcr, Rolvec
				FROM 	{$wbasedato}_000283
				WHERE   Rolest = 'on'";
	$result_rol = mysql_query($sql_rol,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$sql_rol." - ".mysql_error());
	$arr_roles_cx = array();
	while($row_rol = mysql_fetch_array($result_rol))
	{
		$arr_roles_cx[$row_rol['Rolcod']] = array(  "codigo"                    => $row_rol['Rolcod'],
													"nombre"                    => utf8_encode($row_rol['Roldes']), // Nombre del rol completo
													"abreviado"                 => utf8_encode($row_rol['Roldcn']), // Nombre del rol abreviado
													"color"                     => $row_rol['Rolclr'], // Color para identificar en convenciones
													"botones_roles"             => $row_rol['Rolbtn'], // Activar botones para enviar a roles diferentes
													"add_procedimiento"         => $row_rol['Roladp'], // Agregar procedimientos auditados
													"tema_bitacora"             => $row_rol['Roltbi'], // Identificador, tema para agregar en la bitácora
													"filtrar_busqueda_rol"      => $row_rol['Rolfbr'], // Filtrar busqueda pentaña revisadar por rol específico
													"btn_enTramite"             => $row_rol['Rolbet'], // Activar botón En tramite
													"ver_liquidado_tramite"     => $row_rol['Rolltr'], // Ver pestaña liquidados y en trámite
													"ver_liquidado_solucionado" => $row_rol['Rollsr'], // Ver pestaña liquidados y solucionados sin revisar
													"filtrar_cx_rol"            => $row_rol['Rolfcr'], // Ver pestaña liquidados y solucionados sin revisar
													"marcar_cx_revisada"        => $row_rol['Rolmcr'], // Marcar cirugía liquidada como revisada-facturada
													"ver_en_convencion"         => $row_rol['Rolvec']); // Ver rol en convenciones
	}

	?>
	<!DOCTYPE html>
	<html lang="es-ES">
	<head>
	  <title>Monitor facturaci&oacute;n ERP</title>
	</head>

		<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
		<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />

		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
		<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
		<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>

		<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
		<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />

		<script src="../../../include/ips/funcionInsumosqxERP.js?v=<?=md5_file('../../../include/ips/funcionInsumosqxERP.js');?>" type="text/javascript"></script>
		<script src="../../../include/root/toJson.js" type="text/javascript"></script>

	    <link rel="stylesheet" href="../../../include/root/jqx.base.css" type="text/css" />
		<!--<script type="text/javascript" src="../../../include/root/scripts/jquery-1.11.1.min.js"></script>-->
		<script type="text/javascript" src="../../../include/root/jqxcore.js"></script>
		<script type="text/javascript" src="../../../include/root/jqxsplitter.js"></script>
		<script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>
		<!--<script type="text/javascript" src="../../../include/root/jqxtabs.js"></script>-->
		<!--<script type="text/javascript" src="../../../include/root/scripts/demos.js"></script>-->


		<link type="text/css" href="../../../include/root/jquery.ui.timepicker.css" rel="stylesheet"/>
		<script type="text/javascript">

//=====================================================================================================================================================================
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T
//=====================================================================================================================================================================


	var globalMonitor;
	var globalnumero;
	var clonadoppal;
	var btn_clave;

	var arr_roles_cx = <?=json_encode($arr_roles_cx)?>;


	$(function(){

		iniciar_requerido();

      });

	function filtrar_rol(cod_rol)
	{
		$("#selectEstado").val(cod_rol);
		$("#selectEstado").trigger("change");
	}

	function activarSeleccionadorFecha(clave,programa)
	{
		$("#fechas_cirugias").datepicker({
			showOn: "button",
			buttonImage: "../../images/medical/root/calendar.gif",
			buttonImageOnly: true,
			changeYear: true,
            changeMonth: true,
            monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio', 'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
            monthNamesShort: ['Enero','Febrero','Marzo','Abril','Mayo','Junio', 'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
            yearRange: '-2:+1',
			onSelect: function(dateText, inst) {
				// globalMonitor = programa;
				// globalnumero =  clave;
				 var date = $(this).val();
				//alert(date);
				actualizarMonitor(programa,clave,date);
			}
		}).attr("disabled", "disabled");
		$("#fechas_cirugias").next().css({"cursor": "pointer"}).attr("title", "Seleccione");
		$("#fechas_cirugias").after("&nbsp;");
	}

	function validarAccionMercado(e, accion, elem)
	{
		var tecla = (document.all) ? e.keyCode : e.which;
        if(tecla == 13)
        {
        	var codigo_busqueda = $(elem).val();
        	// console.log(codigo_busqueda);
			codigo_busqueda=codigo_busqueda.replace(/ /gi, "");
        	if(codigo_busqueda.replace(/ /gi, "") != "")
        	{
        		if(accion == 'cargar')
        		{
        			// Es posible que varios TDs se llamen igual porque no tienen número de historia, entonces se valída que solo se encuentre un TD con ese número de
        			// historia
        			if($(".td_hitoria_cargar_"+codigo_busqueda).length == 1)
        			{
	            		var elemFunction = $(".td_hitoria_cargar_"+codigo_busqueda).find(".function_exec");
	            		$(elemFunction).trigger("onclick");
	            		// console.log(elemFunction);
        				$("#id_search_historia_cargar").val("");
        			}
        			else if($(".td_hitoria_cargar_"+codigo_busqueda).length > 1)
        			{
        				generarSonidoAlerta();
        				jAlert("Hay mas de un registro con el mismo numero de historia ["+codigo_busqueda+"] Elija manualmente el turno correcto","Mensaje");
        				$(elem).val("");
        			}
        			else
        			{
        				$(elem).val("");
        				jAlert("No se encontro el numero de historia ["+codigo_busqueda+"] para la fecha seleccionada","Mensaje");
        			}
        		}
        		else if(accion == 'devolver')
        		{
        			if($(".td_hitoria_devolver_"+codigo_busqueda).length == 1)
        			{
	            		var elemFunction = $(".td_hitoria_devolver_"+codigo_busqueda).find(".function_exec");
	            		$(elemFunction).trigger("onclick");
	            		// console.log(elemFunction);
        				$("#id_search_historia_devolver").val("");
        			}
        			else if($(".td_hitoria_devolver_"+codigo_busqueda).length > 1)
        			{
        				generarSonidoAlerta();
        				jAlert("Hay mas de un registro con el mismo numero de historia ["+codigo_busqueda+"] Elija manualmente el turno correcto", "Mensaje");
        				$(elem).val("");
        			}
        			else
        			{
        				$(elem).val("");
        				jAlert("No se encontro el numero de historia ["+codigo_busqueda+"] para la fecha seleccionada", "Mensaje");
        			}
        		}
        	}
        	else
        	{
        		jAlert("Deve escribir una historia o utilizar el lector con un stiker","Mensaje");
        	}
        }
	}

	function focushistoria(id_search_historia)
	{
		if($("#"+id_search_historia).length > 0)
		{
			$("#"+id_search_historia).val("").focus();
			// $("#"+id_search_historia).val("");
		}
		return true;
	}


	function cargar_mercado(cedula, nombre, cirugias, historia,ingreso, wcodigoturno, perfil_carga_mercado, wmonitor , wcentro_costos)
	{
		var notyStatus = null;
		$("#divmercado").html("");
		$( "#div_ppal_mercado" ).dialog({
			show: {
				effect: "blind",
				duration: 100
			},
			hide: {
				effect: "blind",
				duration: 100
			},
			height: 750, //750,
			width:  1100,
			buttons: {
		        "Cerrar": function() {
		          $( this ).dialog( "close" );
		        }},
			dialogClass: 'fixed-dialog',
			modal: true,
			title: "Cargar Mercado",
			beforeClose: function( event, ui ) {
				var tbl_insumos = $("#div_ppal_mercado").find("[id^=table_medicamentos_insumos_fiqx_]").attr("id");
				// Si por lo menos hay una fila con un insumo entonces pida confirmación para grabar
				var cantidad_fila_insumos = $("#"+tbl_insumos).find("[id^=tr_insumos_fiqx_]").length;
				var cantidad_fila_insumo_sin_guardar = $("#div_ppal_mercado").find('.insumoModificado').length; // debe existir por lo menos un campo con esa clase asi se hayan modificado varios, solo queda marcado el último codigo de barras o código

				if(cantidad_fila_insumos > 0 && cantidad_fila_insumo_sin_guardar > 0)
				{
					if (notyStatus === null) {
			            event.preventDefault();
			            $('<p title="Confirmar" style="background-color:#fffee2;text-align:justify;">Hay insumos sin guardar. Desea guardar cambios realizados en la lista de insumos?</p>').dialog({
			                dialogClass: 'fixed-dialog',
			                modal: true,
			                buttons: {
			                    "No": function () {
			                        notyStatus = true;
			                        $(this).dialog("close");
			                        $("#div_ppal_mercado").dialog("close");
			                    },
			                    "Si guardar": function () {
			                    	var btn_grabar = $("#div_ppal_mercado").find("[id^=botongrabar_fiqx_]").attr("id");
					           		$("#"+btn_grabar).trigger("onclick");
									actualizarMonitor(globalMonitor,globalnumero, $("#fechas_cirugias").val());
									focushistoria("id_search_historia_cargar");

									notyStatus = true;
			                        $(this).dialog("close");
			                        $("#div_ppal_mercado").dialog("close");
			                    }
			                },
			                open: function(event, ui) {
						        // $(".ui-dialog-titlebar-close").hide(); // Hide the [x] button
					            $(":button:contains('Si')").focus(); // Set focus to the [Ok] button
						    }
			            });
			        }
					// if(confirm("Hay insumos sin guardar. Desea guardar cambios realizados en la lista de insumos?"))
					// {
					// 	var btn_grabar = $("#div_ppal_mercado").find("[id^=botongrabar_fiqx_]").attr("id");
					// 	$("#"+btn_grabar).trigger("onclick");

					// 	actualizarMonitor(globalMonitor,globalnumero, $("#fechas_cirugias").val());
					// 	focushistoria("id_search_historia_cargar");
					// }
				}
				else
				{
					actualizarMonitor(globalMonitor,globalnumero, $("#fechas_cirugias").val());
					focushistoria("id_search_historia_cargar");
				}
			},
			create: function() {
		       $(this).closest('.ui-dialog').on('keydown', function(ev) {
		           if (ev.keyCode === $.ui.keyCode.ESCAPE) {
		           		// Antes de cerrar el div con scape guardar los cambios.
		               // if(confirm("Desea guardar cambios en la lista de insumos?"))
		               // {
		               // 		var btn_grabar = $("#div_ppal_mercado").find("[id^=botongrabar_fiqx_]").attr("id");
		               // 		$("#"+btn_grabar).trigger("onclick");
		               // }
		               $( "#div_ppal_mercado" ).dialog('close');
		           }
		       });
		    },
		    closeOnEscape: false
		}).on("dialogopen", function( event, ui ) {
				var fc = $("#div_ppal_mercado").find("[id^=busc_nom_medicamento_fiqx_]").attr("id");
				$("#"+fc).val("").focus();
		});

		if(undefined == wmonitor)
		{
			var wmonitor="";
		}

		if(wmonitor != "" && $("table[id$="+wmonitor+"]").find(".desc_turno_"+wcodigoturno).length == 1)
		{
			$("#desc_cx_cargar").html($("table[id$="+wmonitor+"]").find(".desc_turno_"+wcodigoturno).html());
		}//else, hay más de uno, no se puede determinar cual elegir

		$("#wcedula").val(cedula);
		$("#wnombrepaciente").val(nombre);
		$("#wcedulaoculto").val(cedula);
		$("#whistoriaoculto").val(historia);
		$("#wingresooculto").val(ingreso);
		$("#wcodigoturnooculto").val(wcodigoturno);
		$("#wturno_cargar").html(wcodigoturno);
		$("#wperfiloculto").val(perfil_carga_mercado);
		//alert(perfil_carga_mercado);
		$("#div_ppal_mercado_historia").val(historia);
		$("#div_ppal_mercado_ingreso").val(ingreso);
		$("#procedimiento_autorizado_cirugia").html(cirugias);
		$("#wcco_oculto_div").val(wcentro_costos);
		pintarCupsAutorizados();
		crear_div('grabarpaciente','divmercado','no', '','','','','','','','','','off','',cedula, wcodigoturno, wcentro_costos);
	}

	function devolver_mercado (cedula, nombre, historia, ingreso, wcodigoturno, wmonitor)
	{
		var notyStatus = null;
		$("#divmercado_devolucion").html("");
		$( "#div_ppal_mercado_devolucion" ).dialog({
			show: {
				effect: "blind",
				duration: 100
			},
			hide: {
				effect: "blind",
				duration: 100
			},
			height: 750,
			width:  1100,
			buttons: {
		        "Cerrar": function() {
		          $( this ).dialog( "close" );
		        }},
			dialogClass: 'fixed-dialog',
			modal: true,
			title: "Devolver Mercado",
			beforeClose: function( event, ui ) {
				var tbl_insumos = $("#div_ppal_mercado_devolucion").find("[id^=table_medicamentos_insumos_fiqx_]").attr("id");
				// Si por lo menos hay una fila con un insumo entonces pida confirmación para grabar
				var cantidad_fila_insumos = $("#"+tbl_insumos).find("[id^=tr_insumos_fiqx_]").length;
				var cantidad_fila_insumo_sin_guardar = $("#div_ppal_mercado_devolucion").find('.insumoModificado').length; // debe existir por lo menos un campo con esa clase asi se hayan modificado varios, solo queda marcado el último codigo de barras o código

				if(cantidad_fila_insumos > 0 && cantidad_fila_insumo_sin_guardar > 0)
				{
					if (notyStatus === null) {
			            event.preventDefault();
			            $('<p title="Confirmar" style="background-color:#fffee2;text-align:justify;">Hay insumos devueltos sin guardar. Desea guardar cambios realizados en la lista de insumos?</p>').dialog({
			                dialogClass: 'fixed-dialog',
			                modal: true,
			                buttons: {
			                    "No": function () {
			                        notyStatus = true;
			                        $(this).dialog("close");
			                        $("#div_ppal_mercado_devolucion").dialog("close");
			                    },
			                    "Si guardar": function () {
			                    	var btn_grabar = $("#div_ppal_mercado_devolucion").find("[id^=botongrabar_fiqx_]").attr("id");
					           		$("#"+btn_grabar).trigger("onclick");
									actualizarMonitor(globalMonitor,globalnumero, $("#fechas_cirugias").val());
									focushistoria("id_search_historia_devolver");

									notyStatus = true;
			                        $(this).dialog("close");
			                        $("#div_ppal_mercado_devolucion").dialog("close");
			                    }
			                },
			                open: function(event, ui) {
						        // $(".ui-dialog-titlebar-close").hide(); // Hide the [x] button
					            $(":button:contains('Si')").focus(); // Set focus to the [Ok] button
						    }
			            });
			        }
					// if(confirm("Hay insumos devueltos sin guardar. Desea guardar cambios realizados en la lista de insumos?"))
					// {
					// 	var btn_grabar = $("#div_ppal_mercado_devolucion").find("[id^=botongrabar_fiqx_]").attr("id");
					// 	$("#"+btn_grabar).trigger("onclick");

					// 	actualizarMonitor(globalMonitor,globalnumero, $("#fechas_cirugias").val());
					// 	focushistoria("id_search_historia_devolver");
					// }
				}
				else
				{
					actualizarMonitor(globalMonitor,globalnumero, $("#fechas_cirugias").val());
					focushistoria("id_search_historia_devolver");
				}
			},
			create: function() {
		       $(this).closest('.ui-dialog').on('keydown', function(ev) {
		           if (ev.keyCode === $.ui.keyCode.ESCAPE) {
		           		// Antes de cerrar el div con scape guardar los cambios.
		               // if(confirm("Desea guardar cambios en la lista de insumos?"))
		               // {
		               // 		var btn_grabar = $("#div_ppal_mercado_devolucion").find("[id^=botongrabar_fiqx_]").attr("id");
		               // 		$("#"+btn_grabar).trigger("onclick");
		               // }
		               $( "#div_ppal_mercado_devolucion" ).dialog('close');
		           }
		       });
		    },
		    closeOnEscape: false
		}).on( "dialogopen", function( event, ui ) {
				var fc = $("#div_ppal_mercado_devolucion").find("[id^=busc_nom_medicamento_fiqx_]").attr("id");
				$("#"+fc).val("").focus();
		} );

		if(undefined == wmonitor)
		{
			var wmonitor="";
		}

		if(wmonitor != "" && $("table[id$="+wmonitor+"]").find(".desc_turno_"+wcodigoturno).length == 1)
		{
			$("#desc_cx_devolucion").html($("table[id$="+wmonitor+"]").find(".desc_turno_"+wcodigoturno).html());
		}//else, hay más de uno, no se puede determinar cual elegir

		$("#wcedula_devolucion").val(cedula);
		$("#wnombrepaciente_devolucion").val(nombre);
		$("#wcedulaoculto_devolucion").val(cedula);
		$("#whistoriaoculto_devolucion").val(historia);
		$("#wingresooculto_devolucion").val(ingreso);
		$("#wcodigoturnooculto_devolucion").val(wcodigoturno);
		$("#mercado_devolucion_historia").val(historia);
		$("#mercado_devolucion_ingreso").val(ingreso);
		$("#wcedulaoculto").val(cedula);
		$("#whistoriaoculto").val(historia);
		$("#wingresooculto").val(ingreso);
		$("#wcodigoturnooculto").val(wcodigoturno);
		$("#wturno_devolucion").html(wcodigoturno);
		// $("#procedimiento_autorizado_cirugia").html(cirugias);
		// pintarCupsAutorizados();
		//crear_div_devolucion('grabardevolucion','divmercado','no', '','','','','','','','','','off','',cedula);
		crear_div_devolucion('grabardevolucion','divmercado_devolucion','no', '','','','','','cc',historia,ingreso,'','off','',cedula, '', wcodigoturno,'');
	}

	/**
	 * [liquidarMercadoUnix: Esta función muestra un dialog con un select y el onchange del select es quien finalmente despliega el dialog de liquidarción de mercado.
	 * 							Además crea atributos en el select para luego leerlos en el dialog de liquidación de mercado.]
	 * @param  {[type]} cedula    [description]
	 * @param  {[type]} nombre    [description]
	 * @param  {[type]} historia  [description]
	 * @param  {[type]} ingreso   [description]
	 * @param  {[type]} quirofano [description]
	 * @return {[type]}           [description]
	 */
	function liquidarMercadoUnix(cedula, nombre, historia, ingreso, quirofano, wcodigoturno, wfechacargo, wmonitor, consultar ,cco)
	{
		if(undefined == wmonitor)
		{
			var wmonitor="";
		}

		$("#wcentro_costos").attr("cedula",cedula);
		$("#wcentro_costos").attr("nombre",nombre);
		$("#wcentro_costos").attr("historia",historia);
		$("#wcentro_costos").attr("ingreso",ingreso);
		$("#wcentro_costos").attr("quirofano",quirofano);
		$("#wcentro_costos").attr("wcodigoturno",wcodigoturno);
		$("#wcentro_costos").attr("wfechacargo",wfechacargo);
		$("#wcentro_costos").attr("wmonitor",wmonitor);
		$("#wcentro_costos").attr("consultar",consultar);

		$("#wcentro_costos").val("");
		$("#wcentro_costos").val(cco);

		liquidarMercadoUnixModal();

		// $("#div_wcentro_costos").dialog({
		// 	show: {
		// 		effect: "blind",
		// 		duration: 100
		// 	},
		// 	hide: {
		// 		effect: "blind",
		// 		duration: 100
		// 	},
		// 	// height: 600,
		// 	// width:  1300,
		// 	dialogClass: 'fixed-dialog',
		// 	modal: true,
		// 	title: "Seleccione un centro de costos"
		// });
	}

	/**
	 * [cerrarMercado: Marca todos los insumos con estado cerrado, para permitir liquidar]
	 * @param  {[type]} historia [description]
	 * @param  {[type]} ingreso  [description]
	 * @param  {[type]} cedula   [description]
	 * @return {[type]}          [description]
	 */
	function cerrarMercado(historia, ingreso, cedula, wcodigoturno,wperfil)
	{
		// var r = confirm("Desea cerrar el mercado?\n\nDespues de cerrar no se permite cargar y devolver desde el almacen.");
		jConfirm('Desea cerrar el mercado?\n\nDespues de cerrar no se permite cargar y devolver desde el almacen.', 'Confirmar', function(r) {
			if (r == true) {
				$.post("MonitorFacturacionERP.php",
				{
					consultaAjax : '',
					wemp_pmla    : $('#wemp_pmla').val(),
					accion       : 'cerrarMercado',
					whistoria    : historia,
					wingreso     : ingreso,
					wcedula      : cedula,
					wcodigoturno : wcodigoturno,
					wperfil		 : wperfil

				},function(data){
					if(data.error == 1)
					{
						jAlert(data.mensaje,"Mensaje");
					}
					else
					{
						if(data.mercadoLiquidadoCerrado == 'off')
						{
							jAlert(data.mensaje,"Mensaje");
							actualizarMonitor(globalMonitor,globalnumero, $("#fechas_cirugias").val());
						}
						else
						{
							jAlert("El mercado ya fue cerrado previamente o ya ha sido liquidado.","Mensaje");
						}
					}
				},"json");

			}
		});
	}

	function abrirMercado(historia, ingreso, cedula, wcodigoturno)
	{
		jConfirm('Desea abrir el mercado?.', 'Confirmar', function(r) {
		    if (r == true) {
				$.post("MonitorFacturacionERP.php",
				{
					consultaAjax : '',
					wemp_pmla    : $('#wemp_pmla').val(),
					accion       : 'abrirMercado',
					whistoria    : historia,
					wingreso     : ingreso,
					wcedula      : cedula,
					wcodigoturno : wcodigoturno,
					arr_roles_cx : JSON.stringify(arr_roles_cx)
				},function(data){
					if(data.error == 1)
					{
						// alert(data.mensaje);
						jAlert(data.mensaje,'Mensaje');
					}
					else
					{
						if(data.mercadoLiquidado == 'off')
						{
							// alert(data.mensaje);
							jAlert(data.mensaje,'Mensaje');
							actualizarMonitor(globalMonitor,globalnumero, $("#fechas_cirugias").val());
						}
						else
						{
							var mssj = "El mercado ya fue liquidado y no es posible abrirlo para realizar modificaciones.";
							// alert(mssj);
							jAlert(mssj,'Mensaje');
						}
					}
				},"json");
			}
		});
	}

	///--------------------------------------------------------
	function Empresas_paf(clave, programa,wfecha='',wcco='',wemp='' , winicial='si', nomprecco='' , nombreempresa='', checkbox ='')
	{

		var wcconame ='';
		var wccocode ='';
		var wempname ='';
		var wempcode ='';
		actual ='on';


		if(wcco=='')
		{
			wcconame ='';
			wccocode='';
		}
		else
		{
			wcconame = $("#buscador_ccoPaf").attr('name');
			wccocode = $("#buscador_ccoPaf").attr('valor');
		}
		if(wemp=='')
		{
				wempname ='';
				wempcode ='';
		}
		else
		{
			wempname = $("#buscador_responsablesPaf").attr('name');
			wempcode = $("#buscador_responsablesPaf").attr('valor');
		}


		$.blockUI({ message: $('#msjEspere') });
		if($("#divmonitor_"+clave).attr("programa") == undefined || $("#divmonitor_"+clave).attr("programa")  == '')
		{
			$("#divmonitor_"+clave).attr("programa" , programa);
			$("#divmonitor_"+clave).attr("numero" , clave);
		}
		else
		{
			clave = $("#divmonitor_"+clave).attr("numero");
			programa = $("#divmonitor_"+clave).attr("programa");
			$("#divmonitor_"+clave).attr("programa" , programa);
			$("#divmonitor_"+clave).attr("numero" , clave);
		}

		$.post("MonitorFacturacionERP.php",
		{
			consultaAjax          : '',
			wemp_pmla             : $('#wemp_pmla').val(),
			accion                : 'Empresas_paf',
			wfechaenviada         : wfecha,
			wcodigo_cargo_wuse    : $("#wcodigo_cargo_wuse").val(),
			arr_permisos_opciones : $("#arr_permisos_opciones").val(),
			wactual				  : actual,
			wmonitor			  : programa,
			winicial			  : winicial,
			wpafprograma		  : programa,
			wpafclave			  : clave,
			wcco				  : wcco,
			wemp				  : wemp,
			wnomprecco		      : nomprecco,
			wnombreempresa		  : nombreempresa,
			wcheckbox			  : checkbox
		}, function (data){



			$('#divmonitor_'+clave).html('');
			$('#divmonitor_'+clave).append('<img width="45" id="boton_'+clave+'"  src="../../images/medical/hce/hceB.png" value="Pantalla completa" class="btn_lupa" onclick="agrandar(\''+clave+'\', \''+programa+'\');">');
			$('#divmonitor_'+clave).append("<div id='accordion"+clave+"' class='acordion'><h3 id='divtitulo_"+clave+"' ></h3><div  id='contenido_"+clave+"'></div></div>");

			$('#divtitulo_'+clave).append("Pacientes PAF");

			var accion_vw = (btn_clave == '') ? "Pantalla Completa": btn_clave;
			if(accion_vw == 'Volver')
			{
				$('#divsecundario').html($('#divmonitor_'+clave).html());
				$('#divmonitor_'+clave).html("");
			}

			$('#contenido_'+clave).html(data);

			// $('#contenido_'+clave).css("height","100%");
			$( "#accordion"+clave).accordion({
				collapsible: true,
				heightStyle: "content"
			});

			activarSeleccionadorFecha(clave,programa);
			return data;
		}).done(function(data){
			//console.log($('input#buscadorPalabraClaveEmpresasPaf'));
		/*	$('input#buscadorPalabraClaveEmpresasPaf').quicksearch('table#tablePpalEmpresasPaf tbody tr', {
						'delay': 300,
						'selector': 'th',
						'stripeRows': ['fila1', 'fila2'],
						'loader': 'span.loading',
						'prepareQuery': function (val) {
							return new RegExp(val, "i");
						},
						'testQuery': function (query, txt, _row) {
							return query.test(txt);
						}
					});*/

			// $('input#buscadorPalabraClaveEmpresasPaf').quicksearch('table#tablePpalEmpresasPaf .find', {
				// 'delay': 300,
				// 'selector': 'th',
				// 'loader': 'span.loading',
				// 'bind': 'keyup click input',
				// 'prepareQuery': function (val) {
					// return new RegExp(val, "i");
				// },
				// 'testQuery': function (query, txt, _row) {
					// return query.test(txt);
				// }
			// });

			$('#buscadorPalabraClaveEmpresasPaf').quicksearch('#tablePpalEmpresasPaf .find');
			iniciarbuscadorespaf(wcconame,wccocode , wempname, wempcode);
			initAutocomplete();
			activarRelojTemporizador2();
			$.unblockUI();
		});
		// focushistoria('id_search_historia_cargar');
	}

	function monitor_medicamentos(clave, programa,wfecha='',wcco='',wemp='' , winicial='si', nomprecco='' , nombreempresa='', checkbox ='')
	{

		//alert("entro");
		actual ='on';
		var wcconame = '';
		var wccocode = '';
		var wempname = '';
		var wempcode = '';


		$.blockUI({ message: $('#msjEspere') });
		if($("#divmonitor_"+clave).attr("programa") == undefined || $("#divmonitor_"+clave).attr("programa")  == '')
		{
			$("#divmonitor_"+clave).attr("programa" , programa);
			$("#divmonitor_"+clave).attr("numero" , clave);
		}
		else
		{
			clave = $("#divmonitor_"+clave).attr("numero");
			programa = $("#divmonitor_"+clave).attr("programa");
			$("#divmonitor_"+clave).attr("programa" , programa);
			$("#divmonitor_"+clave).attr("numero" , clave);
		}

		$.post("MonitorFacturacionERP.php",
		{
			consultaAjax          : '',
			wemp_pmla             : $('#wemp_pmla').val(),
			accion                : 'monitor_medicamentos',
			wfechaenviada         : wfecha,
			wcodigo_cargo_wuse    : $("#wcodigo_cargo_wuse").val(),
			arr_permisos_opciones : $("#arr_permisos_opciones").val(),
			wactual				  : actual,
			wmonitor			  : programa,
			winicial			  : winicial,
			wpafprograma		  : programa,
			wpafclave			  : clave,
			wcco				  : wcco,
			wemp				  : wemp,
			wnomprecco		      : nomprecco,
			wnombreempresa		  : nombreempresa,
			wcheckbox			  : checkbox
		}, function (data){



			$('#divmonitor_'+clave).html('');
			$('#divmonitor_'+clave).append('<img width="45" id="boton_'+clave+'"  src="../../images/medical/hce/hceB.png" value="Pantalla completa" class="btn_lupa" onclick="agrandar(\''+clave+'\', \''+programa+'\');">');
			$('#divmonitor_'+clave).append("<div id='accordion"+clave+"' class='acordion'><h3 id='divtitulo_"+clave+"' ></h3><div  id='contenido_"+clave+"'></div></div>");

			$('#divtitulo_'+clave).append("Medicamientos Unix vs Matrix");

			var accion_vw = (btn_clave == '') ? "Pantalla Completa": btn_clave;
			if(accion_vw == 'Volver')
			{
				$('#divsecundario').html($('#divmonitor_'+clave).html());
				$('#divmonitor_'+clave).html("");
			}

			$('#contenido_'+clave).html(data);

			// $('#contenido_'+clave).css("height","100%");
			$( "#accordion"+clave).accordion({
				collapsible: true,
				heightStyle: "content"
			});

			activarSeleccionadorFecha(clave,programa);
			return data;
		}).done(function(data){
			//console.log($('input#buscadorPalabraClaveEmpresasPaf'));
		/*	$('input#buscadorPalabraClaveEmpresasPaf').quicksearch('table#tablePpalEmpresasPaf tbody tr', {
						'delay': 300,
						'selector': 'th',
						'stripeRows': ['fila1', 'fila2'],
						'loader': 'span.loading',
						'prepareQuery': function (val) {
							return new RegExp(val, "i");
						},
						'testQuery': function (query, txt, _row) {
							return query.test(txt);
						}
					});*/

			// $('input#buscadorPalabraClaveEmpresasPaf').quicksearch('table#tablePpalEmpresasPaf .find', {
				// 'delay': 300,
				// 'selector': 'th',
				// 'loader': 'span.loading',
				// 'bind': 'keyup click input',
				// 'prepareQuery': function (val) {
					// return new RegExp(val, "i");
				// },
				// 'testQuery': function (query, txt, _row) {
					// return query.test(txt);
				// }
			// });


			//$('#buscadorPalabraClaveEmpresasPaf').quicksearch('#tablePpalEmpresasPaf .find');

			//iniciarbuscadorespaf(wcconame,wccocode , wempname, wempcode);
			initAutocomplete();
			activarRelojTemporizador2();
			$.unblockUI();
		});
		// focushistoria('id_search_historia_cargar');
	}

	function traerfechaingresoPaf()
	{
		var whistoria = $("#idhistoriapaf").val();
		//alert($("#fechapafingreso_"+whistoria).val());
		$('#datofechacambioresponsable').val($("#fechapafingreso_"+whistoria).val());
	}
	function Llamarfecha(historia , ingreso)
	{

		$('#datofechacambioresponsable').datepicker('destroy');
		$("#idhistoriapaf").val("");
		$("#idingresopaf").val("");
		$('#datofechacambioresponsable').val("");
		$('#textareapaf').val("");
		$("#idhistoriapaf").val(historia);
		$("#idingresopaf").val(ingreso);
		var fecIng = new Date($("#fechapafingreso_"+historia).val());


		$("#datofechacambioresponsable").datepicker({
					showOn: "button",
					buttonImage: "../../images/medical/root/calendar.gif",
					buttonImageOnly: true,
					minDate: fecIng,
					onSelect: function(){
						//cambiarResponsable( historia, ingreso ,$("#datofechacambioresponsable").val());
						//$("#fechacambioresponsable").dialog( "close" )
					}
				});

		$( "#fechacambioresponsable" ).dialog({

				height: 200,
				width:  600,
				modal: true,
				title: "Fecha cambio responsable"


			});



	}
	function grabarresponsablepaf()
	{
		idhistoriapaf = $("#idhistoriapaf").val();
		idingresopaf  = $("#idingresopaf").val();

		if($("#datofechacambioresponsable").val()=='')
		{
			$("#datofechacambioresponsable").addClass("campoRequerido");
			return;
		}

		cambiarResponsable( idhistoriapaf, idingresopaf ,$("#datofechacambioresponsable").val());
		$("#fechacambioresponsable").dialog( "close" );
	}

	function cambiarResponsable( historia, ingreso , fecha)
	{

		$.blockUI({ message: $('#msjEspere') });
		var seleccionado = $("#select_"+historia).val();
		var nomresseleccionado = $("#select_"+historia+" option:selected").text();
		textareapaf = $('#textareapaf').val();
		//alert(textareapaf);
		$.post("MonitorFacturacionERP.php",
		{
			consultaAjax:      		'',
			wemp_pmla:         		$('#wemp_pmla').val(),
			accion:            		'cambiarResponsableEstancia',
			whistoria:				historia,
			wingreso:				ingreso,
			wresseleccionado:		seleccionado,
			wresanterior:			$("#valAnterior_"+historia).val(),
			wdatofecha:				fecha,
			wtextareapaf:			textareapaf


		}, function (data){
			// alert(data.responsable);
			// alert(data.responsable);

			var destinatariosstr = ","+data.destinatarios;
			destinatariosstr= destinatariosstr.replace(/,/g, "\n-");

			if ($.trim(data.responsable)=='no')
			{
				jAlert("Paciente con Historia: "+historia+"-"+ingreso+"  "+$("#tdnombrepaf_"+historia).html()+" \ncambio de responsable a empresa: "+nomresseleccionado+"\napartir del dia: "+$("#datofechacambioresponsable").val()+".\nSe envio un correo de notificacion  a los siguientes correos: "+destinatariosstr+"",  "Cambio responsable");

				//jAlert("Paciente con Historia: "+historia+"-"+ingreso+"  "+$("#tdnombrepaf_"+historia).html()+" \nya tiene como responsable : "+$("#select_"+historia+" option:selected").text()+"" , "Cambio responsable");
				//$("#select_"+historia).val('');
			}
			else
			{
				jAlert("Paciente con Historia: "+historia+"-"+ingreso+"  "+$("#tdnombrepaf_"+historia).html()+" \ncambio de responsable a empresa: "+nomresseleccionado+"\napartir del dia: "+$("#datofechacambioresponsable").val()+". "+data.responsable+".\nSe envio un correo de notificacion  a los siguientes correos: "+destinatariosstr+"", "Cambio responsable");

				//jAlert("Paciente con Historia: "+historia+"-"+ingreso+"  "+$("#tdnombrepaf_"+historia).html()+" \ncambio de responsable a empresa :"+$("#select_"+historia+" option:selected").text()+"" , "Cambio responsable");
				//$("#select_"+historia).val('');
			}
			$.unblockUI();

		},"json").done(function(data){

			verificarCambioResponsable(historia,ingreso,seleccionado,nomresseleccionado,fecha , textareapaf);


		});

	}

	function verificarCambioResponsable(historia,ingreso,seleccionado,nomresseleccionado,fecha, textareapaf)
	{
		$.post("MonitorFacturacionERP.php",
		{
			consultaAjax:      		'',
			wemp_pmla:         		$('#wemp_pmla').val(),
			accion:            		'verificarCambioResponsable',
			whistoria:				historia,
			wingreso:				ingreso,
			wresseleccionado:		seleccionado,
			wdatofecha:				fecha,
			wtextareapaf:			textareapaf

		}, function (data){

			//alert(data);
			if($.trim(data)=='si')
			{
					// alert("entro");
					$("#idimg_"+historia).html("<img width='13' height='13' src='../../images/medical/root/grabar.png'>");
					$("#valAnterior_"+historia).val($("#select_"+historia).val());
					$("#td_nombreEmpresa_"+historia).html(nomresseleccionado);
			}
			else
			{
				$("#idimg_"+historia).html("<img width='13' height='13' src='../../images/medical/sgc/Warning-32.png'>");
			}


		}).done(function(data){

			if($.trim(data)=='si')
			{
				$.post("MonitorFacturacionERP.php",
				{
					consultaAjax:      		'',
					wemp_pmla:         		$('#wemp_pmla').val(),
					accion:            		'cambiarselectresponsable',
					whistoria:				historia,
					wingreso:				ingreso,
					wresseleccionado:		seleccionado


				}, function (data){

					$("#td_selectpaf_"+historia).html(data);
					/*$("#idimg_"+historia).html("<img width='13' height='13' src='../../images/medical/root/grabar.png'>");
					$("#valAnterior_"+historia).val($("#select_"+historia).val());
					$("#td_nombreEmpresa_"+historia).html(nomresseleccionado);*/
				});
			}
		});
	}

	function abrirVentanaPaf(whistoria, wingreso)
	{
		$("#divHistoriaPaf").html("");

		$.post("MonitorFacturacionERP.php",
		{
			consultaAjax          : '',
			wemp_pmla             : $('#wemp_pmla').val(),
			accion                : 'historialPacientesPaf',
			whistoria			  : whistoria,
			wingreso			  : wingreso
		}, function (data){
			$("#divHistoriaPaf").html(data);
			return data;
		}).done(function(data){

			$( "#divHistoriaPaf" ).dialog({

				height: 400,
				width:  600,
				modal: true,
				title: "Historico de Cambios de Responsable",
				buttons: {
							cerrar: function() {
								$( this ).dialog( "close" );
							}
						}

			});

		});


	}


	function recargarLista()
	{
		actualizarMonitor(globalMonitor,globalnumero, $("#fechas_cirugias").val());
	}



	function liquidarMercadoUnixModal()
	{
		$("#div_wcentro_costos").dialog('close');
		var cedula         = $("#wcentro_costos").attr("cedula");
		var nombre         = $("#wcentro_costos").attr("nombre");
		var historia       = $("#wcentro_costos").attr("historia")
		var ingreso        = $("#wcentro_costos").attr("ingreso");
		var quirofano      = $("#wcentro_costos").attr("quirofano");
		var wcodigoturno   = $("#wcentro_costos").attr("wcodigoturno");
		var wfechacargo    = $("#wcentro_costos").attr("wfechacargo");
		var wmonitor       = $("#wcentro_costos").attr("wmonitor");
		var consultar      = $("#wcentro_costos").attr("consultar");
		var wcentro_costos = $("#wcentro_costos").val();

		$("#divmercado_liquidar").html("");
		$( "#div_ppal_mercado_liquidar_unix" ).dialog({
			show: {
				effect: "blind",
				duration: 100
			},
			hide: {
				effect: "blind",
				duration: 100
			},
			height: 600,
			width:  1300,
			buttons: {
				"Imprimir": function() {

				// $("#div_ppal_mercado_liquidar_unix").print();
					var tableimprimir ="";


					tableimprimir +="<table>";
					tableimprimir +="<tr>";
					tableimprimir +="<td>Nombre:</td><td>"+nombre+"</td>";
					tableimprimir +="</tr>";
					tableimprimir +="<tr><td>Documento:</td><td>"+cedula+"</td>";
					tableimprimir +="</tr>";
					tableimprimir +="<tr>";
					tableimprimir +="<td>Historia:</td><td>"+historia +"</td>";
					tableimprimir +="</tr>";
					tableimprimir +="<tr>";
					tableimprimir +="<td>Ingreso:</td><td>"+ingreso+"</td>";
					tableimprimir +="</tr>";
					tableimprimir +="<tr>";
					tableimprimir +="<td>Quirofano:</td><td>"+quirofano+"</td>";
					tableimprimir +="</tr>";
					tableimprimir +="</table><br><br><br>";
					tableimprimir +="<table Style='border: 1px solid #000; border-collapse: collapse;' align='center'>";
					$("#table_medicamentos_insumos_fiqx_div_divmercado_liquidar tbody tr").each(function () {
						tableimprimir +="<tr  style='border: 1px solid #000;'>";
					$(this).children("td").each(function () {

					if($(this).index()== 2 || $(this).index()== 3 || $(this).index()== 8  || $(this).index()== 9  || $(this).index()== 10  )
						tableimprimir += "<td  ><font size='2'>"+$(this).html()+"</font><td>";

					});
						tableimprimir +="</tr>";
					});

					tableimprimir +="</table>";
					var divContents = $("#div_medica_insumos_fiqx_div_divmercado_liquidar").html();
					var printWindow = window.open('', '', 'height=400,width=800');
					printWindow.document.write('<html><head><title></title>');
					printWindow.document.write('</head><body >');
					printWindow.document.write(tableimprimir);
					printWindow.document.write('</body></html>');
					printWindow.document.close();
					printWindow.print();


		          //$( this ).dialog( "close" );
		        },
		        "Cerrar": function() {
		          $( this ).dialog( "close" );
		        }},
			dialogClass: 'fixed-dialog',
			modal: true,
			title: "Liquidar insumos",
			beforeClose: function( event, ui ) {
				var tbl_insumos = $("#div_ppal_mercado_liquidar_unix").find("[id^=table_medicamentos_insumos_fiqx_]").attr("id");
				actualizarMonitor(globalMonitor,globalnumero, $("#fechas_cirugias").val());
			},
			create: function() {
		       $(this).closest('.ui-dialog').on('keydown', function(ev) {
		           if (ev.keyCode === $.ui.keyCode.ESCAPE) {
		           		// Antes de cerrar el div con scape guardar los cambios.
		               // if(confirm("Desea guardar cambios en la lista de insumos?"))
		               // {
		               // 		var btn_grabar = $("#div_ppal_mercado_liquidar_unix").find("[id^=botongrabar_fiqx_]").attr("id");
		               // 		$("#"+btn_grabar).trigger("onclick");
		               // }
		               $("#div_ppal_mercado_liquidar_unix").dialog('close');
		           }
		       });
		    },
		    closeOnEscape: false
		    /*autoOpen: false*/
		}).on( "dialogopen", function( event, ui ) {
				// var fc = $("#div_ppal_mercado_liquidar_unix").find("[id^=busc_nom_medicamento_fiqx_]").attr("id");
				// $("#"+fc).val("").focus();
		} );

		if(undefined == wmonitor)
		{
			var wmonitor="";
		}

		if(wmonitor != "" && $("table[id$="+wmonitor+"]").find(".desc_turno_"+wcodigoturno).length == 1)
		{
			$("#desc_cx_liquidar").html($("table[id$="+wmonitor+"]").find(".desc_turno_"+wcodigoturno).html());
		}//else, hay más de uno, no se puede determinar cual elegir

		$("#wcedula_liquidar").val(cedula);
		$("#wnombrepaciente_liquidar").val(nombre);
		$("#wcedulaoculto_liquidar").val(cedula);
		$("#whistoriaoculto_liquidar").val(historia);
		$("#wingresooculto_liquidar").val(ingreso);
		$("#wcodigoturnooculto_liquidar").val(wcodigoturno);
		$("#wfechacargooculto_liquidar").val(wfechacargo);
		$("#wturno_liquidar").html(wcodigoturno);
		$("#mercado_liquidar_unix_historia").html(historia);
		$("#mercado_liquidar_unix_ingreso").html(ingreso);
		var cco_sp = $("#wcentro_costos :selected").text();
		$("#spn_wcentro_costos").html(cco_sp);
		$("#spn_wquirofano").html(quirofano);
		// $("#wcedulaoculto").val(cedula);
		// $("#whistoriaoculto").val(historia);
		// $("#wingresooculto").val(ingreso);
		// $("#procedimiento_autorizado_cirugia").html(cirugias);
		// pintarCupsAutorizados();
		crear_div_devolucion('grabarliquidacion','divmercado_liquidar','no', '','','','','','cc',historia,ingreso,'','off','',cedula,'on', wcodigoturno, consultar);

	}

	function crear_div(operacion,div,modal, procedimiento,c_responsable,c_tarifa,n_tarifa,n_responsable,ant_procedimiento,whistoria,wingreso,tipoEmpresa,paquete,n_tipo_empresa,cedula,wcodigoturno,wcentro_costos)
	{
		var wperfil = $("#wperfiloculto").val();
		$.ajax(
		{
			url: "MonitorFacturacionERP.php",
			context: document.body,
			type: "POST",
			data:
			{
				consultaAjax : '',
				wemp_pmla    : $('#wemp_pmla').val(),
				accion       : 'cargar_mercados',
				whistoria    : $('#whistoriaoculto').val(),
				wingreso     : $('#wingresooculto').val(),
				wcedula      : $('#wcedula').val(),
				wcodigoturno : wcodigoturno,
				wperfil      : wperfil
			},
				async: false,
				dataType: "json",
				success:function(data_fn)
				{
					if(data_fn.mercadoLiquidadoCerrado == 'off')
					{
						var data = data_fn.data_mercado ; //eval('(' + data_fn.data_mercado + ')');

						var index = 0;
						var c_procedimiento = '-1';
						var n_procedimiento = '';
						// $("#"+div).append("<div   id='acordion_div_"+div+"' class='divacordion1'   ><h3 class='encabezadoTabla'>Insumos de la cirug&iacute;a</h3><div id='div_"+div+"' ></div></div>");
						$("#"+div).append("<div id='div_"+div+"' ></div>");
						ventana_insumo(operacion,"div_"+div,modal, c_procedimiento,c_responsable,c_tarifa,n_tarifa,n_responsable,n_procedimiento,whistoria,wingreso,tipoEmpresa,paquete,n_tipo_empresa,'',cedula, '',wcentro_costos,wcodigoturno);
						/*for (var datos in data)
						{
							index++;
							c_procedimiento = data[index].codigo;
							n_procedimiento = data[index].nombre;

							if( $("#div_"+c_procedimiento).length == 0)
							{
								$("#"+div).append("<br><div   id='acordion_div_"+c_procedimiento+"' class='divacordion1'   ><h3>"+n_procedimiento+"</h3><div id='div_"+c_procedimiento+"'  procedimiento='"+c_procedimiento+"'></div></div>");
								ventana_insumo(operacion,"div_"+c_procedimiento+"",modal, c_procedimiento,c_responsable,c_tarifa,n_tarifa,n_responsable,n_procedimiento,whistoria,wingreso,tipoEmpresa,paquete,n_tipo_empresa,cedula);
							}
						}*/
					}
					else
					{
						jAlert("El mercado ya fue cerrado previamente o ya ha sido liquidado, No es posible cargar o modificar el mercado.","Mensaje");
					}
				}
		});
	}

	function crear_div_devolucion (operacion,div,modal, procedimiento,c_responsable,c_tarifa,n_tarifa,n_responsable,ant_procedimiento,whistoria,wingreso,tipoEmpresa,paquete,n_tipo_empresa,cedula, liquidar,wcodigoturno, consultar)
	{
		//alert(whistoria);
		var wperfil = $("#wperfiloculto").val();
		var wcentro_costos = ($("#wcentro_costos").length > 0) ? $("#wcentro_costos").val() : "";
		$.ajax(
		{
			url: "MonitorFacturacionERP.php",
			context: document.body,
			type: "POST",
			data:
			{
				consultaAjax   : '',
				wemp_pmla      : $('#wemp_pmla').val(),
				accion         : 'cargar_mercados_devolucion',
				whistoria      : whistoria,
				wingreso       : wingreso,
				wcedula        : cedula,
				liquidar       : liquidar,
				wcodigoturno   : wcodigoturno,
				wperfil        : wperfil,
				consultar      : consultar
			},
				async: false,
				dataType: "json",
				success:function(data_fn) {
					if(data_fn.mercadoLiquidadoCerrado == 'off')
					{
						var data = data_fn.data_mercado; //eval('(' + data. + ')');

						var index = 0;
						var c_procedimiento = '-1';
						var n_procedimiento = '';

						$("#"+div).append("<div id='div_"+div+"' ></div>");
						ventana_insumo(operacion,"div_"+div,modal, c_procedimiento,c_responsable,c_tarifa,n_tarifa,n_responsable,n_procedimiento,whistoria,wingreso,tipoEmpresa,paquete,n_tipo_empresa,'',cedula, liquidar,wcentro_costos,wcodigoturno);
					}
					else
					{
						jAlert("El mercado ya fue cerrado previamente o ya ha sido liquidado, No es posible hacer nuevas devoluciones","Mensaje");
					}
				}
		});
	}

	function iniciar_requerido()
	{
		$('.requerido').on({
		focusout: function(e) {
			if($(this).val().replace(/ /gi, "") == '')
			{
				$(this).addClass("campoRequerido");
			}
			else
			{
				$(this).removeClass("campoRequerido");
			}
		}
		});

		$('.requerido_exa').on({
			focusout: function(e) {
				if($(this).val().replace(/ /gi, "") == '')
				{
					$(this).addClass("campoRequerido_exa");
				}
				else
				{
					$(this).removeClass("campoRequerido_exa");
				}
			}
		});


	}

	function borrar_datos()
	{
		$("#wnom_procedimiento_empresa").val('');
		$("#wprocedimiento_empresa").val('');
		$("#wuvr").val('');
		$("#wvalor_uvr").val('');

	}

	function validar_requeridos()
	{

			$(".requerido").each(function () {

				if($(this).val()!='')
				{
					$(this).removeClass("campoRequerido");
				}
				else
					$(this).addClass("campoRequerido");

			})


	}

	function verificar_dato()
	{
		validar_requeridos_exa();

	}
	function validar_requeridos_exa()
	{


			$(".requerido_exa").each(function () {

				if($(this).val()!='')
				{
					$(this).removeClass("campoRequerido_exa");

				}
				else
					$(this).addClass("campoRequerido_exa");

			})


	}

	// Funcion que graba una tarifa nueva

	function grabar_tarifa()
	{
		$("#idcreartarifamensaje").html('');
		$("#idcreartarifamensaje").removeClass('divmensajetarifa');
		$(".requerido").removeClass('faltanDatos');// se quita la clase faltanDatos
		$("#idbuttongrabartarifa").prop('disabled',true); // se desabilita el boton grabar

		//------
		//--------Validaciones-------------------------------------------------
		//--------Validacion campos requeridos
		var requeridos =false;
		$(".requerido").each(function() {
			if($(this).val()=='')
			{
				$(this).addClass('faltanDatos');
				requeridos =true;

			}
		});
		if (requeridos == true)
		{
			jAlert("<span style='color:#4C99F5'>Faltan campos por llenar.</span>", "Mensaje");
			$("#idbuttongrabartarifa").prop('disabled',false);
			return;
		}

		//----Validacion de decimales
		var decimal=false;
		$(".decimal").each(function (){
			if ($(this).val().match(/^[0-9]+$/))
			{

			}
			else
			{
				$(this).val('');
				$(this).addClass('faltanDatos');
				decimal=true;
			}

		});
		if (decimal == true)
		{

			jAlert("<span style='color:#4C99F5'>El campo valor debe ser n\u00famerico.</span>", "Mensaje");
			$("#idbuttongrabartarifa").prop('disabled',false);
			return;
		}
		//-----------------------------------

		//------Validacion UVR
			if($("#wtipo_facturacion").val() == 'UVR')
			{
				if($("#wuvr").val() == '')
				{
					jAlert("debe llenar primero el valor de uvr para esta tarifa","Mensaje");
					return;
				}

			}
		//----------------------------------
		//--------- Validacion grupo quirurgico
			var wgqx ;
			wgqx = '';
			if($("#wtipo_facturacion").val() == 'GQX')
			{
					jAlert($("#select_gqx").val(),"Mensaje");
					wgqx = $("#select_gqx").val();
					if ($("#wvalor").val() =='0')
					{
						jAlert("debe Ingresar tarifa primero para este grupo quirurgico","Mensaje");
						return;
					}
			}
		//----------------------------------
		//-----------------------------------------------
		//-----------------------------------------------
			$.post("MonitorFacturacionERP.php",
			{
				consultaAjax:      		'',
				wemp_pmla:         		$('#wemp_pmla').val(),
				accion:            		'grabar_tarifa',
				wcodigo_procedimiento:  $("#wprocedimiento").val(),
				wcodigo_empresa:		$("#wresponsable_empresa").val(),
				wtipo_facturacion:		$("#wtipo_facturacion").val(),
				wempresa_pro:			$("#wprocedimiento_empresa").val(),
				wempresa_nom:			$("#wnom_procedimiento_empresa").val(),
				wcco:					$("#wcco").val(),
				wconcepto:				$("#codigoconcepto").val(),
				wvalor:					$("#wvalor").val(),
				wtar:					$("#wtar").val(),
				wuvr:					$("#wuvr").val(),
				wvalouvr:				$("#wvalor_uvr").val(),
				wid:					$("#wnumero_id").val(),
				wgqx:					wgqx,
				wparametro:				$("#parametro").val(),
				worigen:				$("#origen").val(),
				wturno:					$("#wturnohidden").val()



			}, function (data){

				data = $.trim(data);
				//alert(data);
				// data = 'Grabacion exitosa';

				if(data =='1')
				{
					actualizarMonitor(globalMonitor,globalnumero, '');
					//alert("entro2");
					//$("#idbuttongrabartarifa").prop('disabled',false);
					$("#idbuttongrabartarifa").prop('disabled',true); // se desabilita el boton grabar
					$(".requerido").prop('disabled',true);
					jAlert("<span style='color:#4C99F5'>Grabacion exitosa</span>", "Mensaje");
					//alert("entro");

				}
				else
				{
					//alert("entro");
					$("#idbuttongrabartarifa").prop('disabled',false);
					actualizarMonitor(globalMonitor,globalnumero, '');
					jAlert("<span style='color:#4C99F5'>"+data+"</span>", "Mensaje");
				}

				// $("#idcreartarifamensaje").addClass('divmensajetarifa');
				// $("#idcreartarifamensaje").html("<br>"+data);


			});


	}

	function grabar_tarifa_unix()
	{

		if($(".campoRequerido_exa").length > 0)
		{

			//alert($(".campoRequerido_exa").length);
			jAlert("debe llenar los campos requeridos","Mensaje");
			return;
		}

		var wporcentaje= 0;

		$.post("MonitorFacturacionERP.php",
			{
				consultaAjax:      		'',
				wemp_pmla:         		$('#wemp_pmla').val(),
				accion:            		'grabar_tarifa_unix',
				wcod_examen:			$("#examen_codigo").val(),
				wtarifa:				$("#examen_tarifa").val(),
				wconcepto:				$("#examen_concepto").val(),
				wvactual:				$("#valor_actual").val(),
				wvanterior:				$("#valor_anterior").val(),
				wfec:					$("#wfecha_tarifa_examen").val(),
				wcco_exa:				$("#wcco_exa").val(),
				wporcentaje:			wporcentaje,
				wparametro:				$("#wparametro_examen").val(),
				wnombre_examen:			$("#examen_nombre").val(),
				wcod_anexo_examen:		$("#codigo_anexo_examen").val(),
				wliquidacion_examen:	$("#tipo_facturacion_examen").val(),
				wnivel:					$("#cod_nivel").val(),
				wgrupo_examen:			$("#cod_grupo_examen").val()



			}, function (data){

				//alert(data);
				//alert(data);
				actualizarMonitor(globalMonitor,globalnumero, $("#fechas_cirugias").val());
				$('#div_tarifas_unix').dialog('close');

			});



	}

	function cerrar_tarifa ()
	{

		$('#div_tarifas').dialog('close');
		$('#div_tarifas_unix').dialog('close');

	}

	function cerrar_mercado ()
	{

		$('#div_ppal_mercado').dialog('close');


	}



	function cambiartipofacturacion ()
	{
		var tipo_facturacion = $('#wtipo_facturacion').val();
		$("#wvalor").prop('disabled', true);
		// alert(tipo_facturacion);
		$("#img_grupo").remove();
		if(tipo_facturacion=='CODIGO')
		{
			$("#wvalor").prop('disabled', false);
			$("#tr_uno").remove();
			$("#tr_dos").remove();
		}
		if(tipo_facturacion=='GQX')
		{

			$("#tr_uno").remove();
			$("#tr_dos").remove();

			$.post("MonitorFacturacionERP.php",
			{
				consultaAjax:      		'',
				wemp_pmla:         		$('#wemp_pmla').val(),
				accion:            		'traer_valor_gqx',
				wtar:					$("#wtar").val(),
				wconcepto:				$("#wconcepto_empresa").val(),
				wfeccar:				$("#wfechadecargo").val()
			}, function (data){



				$('#td_tipo_facturacion').html('Grupo Quirurgico');


				$('#tablevalor').append('<tr id="tr_uno"><td>Grupo : </td><td>'+data+'</td>');




			});

		}
		if(tipo_facturacion=='UVR')
		{

			$.post("MonitorFacturacionERP.php",
			{
				consultaAjax:      		'',
				wemp_pmla:         		$('#wemp_pmla').val(),
				accion:            		'traer_valor_uvr',
				wtar:					$("#wtar").val(),
				wconcepto:				$("#wconcepto_empresa").val(),
				wfeccar:				$("#wfechadecargo").val(),
				wcco:					$("#wcco").val()
			}, function (data){

				var valor_uvr = data;

				$('#td_tipo_facturacion').html('Numero de UVR');

				if($("#tr_uno").length == 0)
				{
					$('#tablevalor').append('<tr id="tr_uno"><td>Numero de Uvrs:</td><td><input type="text" id="wuvr"  class="requerido" value=""  onchange="calcular_uvr()"></td><tr id="tr_dos"><td>Valor de la Uvr:</td><td><input type="text" id="wvalor_uvr" value="'+valor_uvr+'" disabled="disabled" ></td></tr>');

				}
				else
				{
					$("#wvalor_uvr").val(valor_uvr);
				}
				if(data =='no hay valor')
				{
					$('#tr_dos').append('<td><img width="15" height="15" src="../../images/medical/sgc/Warning-32.png"><td/>')
				}
			});


		}

	}


	function traer_grupo_gqx ()
	{

		//$("#select_gqx").val();

		$.post("MonitorFacturacionERP.php",
		{
			consultaAjax:      		'',
			wemp_pmla:         	$('#wemp_pmla').val(),
			accion:            	'traer_valor_grupo',
			wtar:				$("#wtar").val(),
			wconcepto:			$("#wconcepto_empresa").val(),
			wfeccar:			$("#wfechadecargo").val(),
			wgqx:				$("#select_gqx").val(),
			wcco:				$("#wcco").val()
		}, function (data){

			//alert(data)
			$("#img_grupo").remove();
			if(data == "no hay valor")
			{
					//alert("nn");
					$("#wvalor").val("0");
					$('<img id="img_grupo" width="15" height="15" src="../../images/medical/sgc/Warning-32.png">').insertAfter("#wvalor")
			}
			else
				$("#wvalor").val(data);
				//alert(data);

		});


	}

	function calcular_uvr()
	{
		var valor 	  = $("#wvalor").val();
		var numerouvr = $("#wuvr").val();
		var valor_uvr = $("#wvalor_uvr").val();
		$("#wvalor").val( numerouvr * valor_uvr );
		validar_requeridos();

	}

	function traer_examenes_problemas(clave, programa)
	{

		$('#divmonitor_'+clave).html('');
		/*$.post("MonitorFacturacionERP.php",
		{
			consultaAjax:      		'',
			wemp_pmla:         	$('#wemp_pmla').val(),
			accion:            		'pasar_examenes'
		}, function (data){


		});


		$.post("MonitorFacturacionERP.php",
		{
			consultaAjax:      		'',
			wemp_pmla:         	$('#wemp_pmla').val(),
			accion:            		'Examinar_Labmatrix'
		}, function (data){


		});

		*/
		$.post("MonitorFacturacionERP.php",
		{
			consultaAjax:      		'',
			wemp_pmla:         		$('#wemp_pmla').val(),
			accion:            		'Mostrar_monitor',
			fecha_actual:			$("#wfeccar").val(),
			fecha_aviso:			$("#wfeccar2").val()
		}, function (data){

					if($("#divmonitor_"+clave).attr("programa")  != '' )
					{
						$("#divmonitor_"+clave).attr("programa" , ""+programa+"");
						$("#divmonitor_"+clave).attr("numero" , ""+clave+"");
					}


					$('#divmonitor_'+clave).append('<img width="45" id="boton_'+clave+'"  src="../../images/medical/hce/hceB.png" valor="Pantalla completa" class="btn_lupa" onclick="agrandar(\''+clave+'\', \''+programa+'\');" >');


					/*
					<div width='95%' id='accordionPension'>
			<h3>LIQUIDACION DE PENSION</h3>";
	echo	"<div id='detalle_liquidacion_general' style='display : none'>
				<div id='sel_concepto' >";
	echo 		$conceptos = selectconceptospension($whistoria, $wing ,$div_tarifa,$div_responsable);
	echo       "</div>
				<div id='div_liquidacion_pension'></div>";
	echo"	</div>
		</div>

					*/


					$('#divmonitor_'+clave).append("<div id='accordion"+clave+"' class='acordion' ><h3 id='divtitulo_"+clave+"' ></h3><div  id='contenido_"+clave+"'></div></div>");
					$('#divtitulo_'+clave).append("Cargos de Laboratorio con Problemas");
					$('#contenido_'+clave).html(data.html);
					$( "#accordion"+clave+"" ).accordion({
						collapsible: false,
						heightStyle: "content"
					});

					$("#wfeccar").datepicker({
					showOn: "button",
					buttonImage: "../../images/medical/root/calendar.gif",
					buttonImageOnly: true
					});

				// $.post("MonitorFacturacionERP.php",
				// {
					// consultaAjax:      		'',
					// accion:            		'grabacionCargosLaboratorio'

				// }, function (data){
				// });



		}, 'json');


	}


	function traer_correos (clave,programa)
	{


		$.post("MonitorFacturacionERP.php",
		{
			consultaAjax:      		'',
			wemp_pmla:         		$('#wemp_pmla').val(),
			accion:            		'mostrarPendientesRegrabacion'
		}, function (data){
				$("#divmonitor_"+clave).attr("programa" , ""+programa+"");
				$("#divmonitor_"+clave).attr("numero" , ""+clave+"");
				$('#divmonitor_'+clave).append("<img id='boton_"+clave+"'  src='../../images/medical/hce/hceB.png' valor='Pantalla completa' class='btn_lupa' onclick='agrandar(\""+clave+"\", \""+programa+"\")'>");

				$('#divmonitor_'+clave).append("<div id='accordion"+clave+"' class='acordion'><h3 id='divtitulo_"+clave+"' ></h3><div  id='contenido_"+clave+"'></div></div>");
				$('#divtitulo_'+clave).append("Pendientes de regrabaci\u00F3n por cambio de responsable");
				$('#contenido_'+clave).html(data);
					$( "#accordion"+clave+"" ).accordion({
						collapsible: true,
						heightStyle: "content"
					});

		});

	}

	function  cargos_sin_tarifa(clave,programa)
	{
		// alert(programa);
		//$('#divmonitor_'+clave).html("No hay nada");

		if($("#divmonitor_"+clave).attr("programa") == undefined || $("#divmonitor_"+clave).attr("programa")  == '')
		{
			$("#divmonitor_"+clave).attr("programa" , programa);
			$("#divmonitor_"+clave).attr("numero" , clave);
		}
		else
		{
			clave = $("#divmonitor_"+clave).attr("numero");
			programa = $("#divmonitor_"+clave).attr("programa");
			$("#divmonitor_"+clave).attr("programa" , programa);
			$("#divmonitor_"+clave).attr("numero" , clave);
		}


		$.post("MonitorFacturacionERP.php",
		{
			consultaAjax:      		'',
			wemp_pmla:         	$('#wemp_pmla').val(),
			accion:            		'cargos_sin_tarifa'
		}, function (data){

			// if($("#divmonitor_"+clave).attr("programa")  != '' )
			// {
				// $("#divmonitor_"+clave).attr("programa" , ""+programa+"");
				// $("#divmonitor_"+clave).attr("numero" , ""+clave+"");
			// }


			// $('#divmonitor_'+clave).html('');
			// $('#divmonitor_'+clave).append("<img id='boton_"+clave+"'  src='../../images/medical/hce/hceB.png' valor='Pantalla completa' class='btn_lupa' onclick='agrandar(\""+clave+"\", \""+programa+"\")'>");
			// $('#divmonitor_'+clave).append("<div id='accordion"+clave+"' class='acordion'><h3 id='divtitulo_"+clave+"' ></h3><div  id='contenido_"+clave+"'></div></div>");


			//---
			$('#divmonitor_'+clave).html('');
			$('#divmonitor_'+clave).append('<img width="45" id="boton_'+clave+'"  src="../../images/medical/hce/hceB.png" value="Pantalla completa" class="btn_lupa" onclick="agrandar(\''+clave+'\', \''+programa+'\');">');
			$('#divmonitor_'+clave).append("<div id='accordion"+clave+"' class='acordion'><h3 id='divtitulo_"+clave+"' ></h3><div  id='contenido_"+clave+"'></div></div>");

			//---

			/*

			//console.log('#contenido_'+clave+data);
			$('#contenido_'+clave).html(data);
					$( "#accordion"+clave+"" ).accordion({
						collapsible: true,
						heightStyle: "content"
					});
			*/

			var accion_vw = (btn_clave == '') ? "Pantalla Completa": btn_clave;
			if(accion_vw == 'Volver')
			{
				$('#divsecundario').html($('#divmonitor_'+clave).html());
				$('#divmonitor_'+clave).html("");
			}

			$('#contenido_'+clave).html(data);
			$('#divtitulo_'+clave).append("Cargos sin Tarifa");
			// $('#contenido_'+clave).css("height","100%");
			$( "#accordion"+clave).accordion({
				collapsible: true,
				heightStyle: "content"
			});

		});


	}

	function Cirugias_sin_mercado(clave, programa,wfecha='')
	{


		actual ='on';

		$.blockUI({ message: $('#msjEspere') });
		if($("#divmonitor_"+clave).attr("programa") == undefined || $("#divmonitor_"+clave).attr("programa")  == '')
		{
			$("#divmonitor_"+clave).attr("programa" , programa);
			$("#divmonitor_"+clave).attr("numero" , clave);
		}
		else
		{
			clave = $("#divmonitor_"+clave).attr("numero");
			programa = $("#divmonitor_"+clave).attr("programa");
			$("#divmonitor_"+clave).attr("programa" , programa);
			$("#divmonitor_"+clave).attr("numero" , clave);
		}

		$.post("MonitorFacturacionERP.php",
		{
			consultaAjax          : '',
			wemp_pmla             : $('#wemp_pmla').val(),
			accion                : 'Cirugias_sin_mercado',
			wfechaenviada         : wfecha,
			wcodigo_cargo_wuse    : $("#wcodigo_cargo_wuse").val(),
			arr_permisos_opciones : $("#arr_permisos_opciones").val(),
			wactual				  : actual,
			wmonitor			  : programa,
			arr_roles_cx		  : JSON.stringify(arr_roles_cx)
		}, function (data){



			$('#divmonitor_'+clave).html('');
			$('#divmonitor_'+clave).append('<img width="45" id="boton_'+clave+'"  src="../../images/medical/hce/hceB.png" value="Pantalla completa" class="btn_lupa" onclick="agrandar(\''+clave+'\', \''+programa+'\');">');
			$('#divmonitor_'+clave).append("<div id='accordion"+clave+"' class='acordion'><h3 id='divtitulo_"+clave+"' ></h3><div  id='contenido_"+clave+"'></div></div>");

			$('#divtitulo_'+clave).append("&nbsp;&nbsp;&nbsp;Programaci&oacute;n de cirug&iacute;as");

			//$('#divtitulo_'+clave).append("Cirugias sin liquidar de dias anteriores");

			// Como se creó un div secundario, se comprueba si el div que está abierto corresponde a la pantalla principal, si es asi entonces todo lo que se
			// actualizó en el div del monitor se debe copiar completamente al divsecundario y vaciar el divmonitor para evitar problemas de ID repetidos en el html
			var accion_vw = (btn_clave == '') ? "Pantalla Completa": btn_clave;
			if(accion_vw == 'Volver')
			{
				$('#divsecundario').html($('#divmonitor_'+clave).html());
				$('#divmonitor_'+clave).html("");
			}

			$('#contenido_'+clave).html(data);

			// $('#contenido_'+clave).css("height","100%");
			$( "#accordion"+clave).accordion({
				collapsible: true,
				heightStyle: "content"
			});

			activarSeleccionadorFecha(clave,programa);
			return data;
		}).done(function(data){
			// focushistoria("id_search_historia_cargar");
			$("#id_search_historia_cargar").val("").focus();
			//alert(programa);
			$('input#id_search_cirugias_sin_mercado'+programa).quicksearch('#Cirugias_sin_mercado_table'+programa+' .find');
			$('#divmonitor_'+clave).css("height","");
			$.unblockUI();
			blinkJqueryElementClass("css_paciente_alta_cx");
			blinkJqueryElementClass("css_img_liquidar");
			// cronUnixCargos();
		});
		// focushistoria('id_search_historia_cargar');
	}

	function Cirugias_con_mercado_y_no_liquidadas(clave, programa,wfecha='')
	{


		actual ='off';
		$.blockUI({ message: $('#msjEspere') });
		if($("#divmonitor_"+clave).attr("programa") == undefined || $("#divmonitor_"+clave).attr("programa")  == '')
		{
			$("#divmonitor_"+clave).attr("programa" , programa);
			$("#divmonitor_"+clave).attr("numero" , clave);
		}
		else
		{
			clave = $("#divmonitor_"+clave).attr("numero");
			programa = $("#divmonitor_"+clave).attr("programa");
			$("#divmonitor_"+clave).attr("programa" , programa);
			$("#divmonitor_"+clave).attr("numero" , clave);
		}


		$.post("MonitorFacturacionERP.php",
		{
			consultaAjax          : '',
			wemp_pmla             : $('#wemp_pmla').val(),
			accion                : 'Cirugias_sin_mercado',
			wfechaenviada         : wfecha,
			wcodigo_cargo_wuse    : $("#wcodigo_cargo_wuse").val(),
			arr_permisos_opciones : $("#arr_permisos_opciones").val(),
			wactual				  : actual,
			wmonitor			  : programa,
			arr_roles_cx          : JSON.stringify(arr_roles_cx)
		}, function (data){



			$('#divmonitor_'+clave).html('');
			$('#divmonitor_'+clave).append('<img width="45" id="boton_'+clave+'"  src="../../images/medical/hce/hceB.png" value="Pantalla completa" class="btn_lupa" onclick="agrandar(\''+clave+'\', \''+programa+'\');">');
			$('#divmonitor_'+clave).append("<div id='accordion"+clave+"' class='acordion'><h3 id='divtitulo_"+clave+"' ></h3><div  id='contenido_"+clave+"'></div></div>");

			//$('#divtitulo_'+clave).append("Cirugias sin Mercado");

			$('#divtitulo_'+clave).append("&nbsp;&nbsp;&nbsp;Cirug&iacute;as sin liquidar de d&iacute;as anteriores");

			// Como se creó un div secundario, se comprueba si el div que está abierto corresponde a la pantalla principal, si es asi entonces todo lo que se
			// actualizó en el div del monitor se debe copiar completamente al divsecundario y vaciar el divmonitor para evitar problemas de ID repetidos en el html
			var accion_vw = (btn_clave == '') ? "Pantalla Completa": btn_clave;
			if(accion_vw == 'Volver')
			{
				$('#divsecundario').html($('#divmonitor_'+clave).html());
				$('#divmonitor_'+clave).html("");
			}

			$('#contenido_'+clave).html(data);

			// $('#contenido_'+clave).css("height","100%");
			$( "#accordion"+clave).accordion({
				collapsible: true,
				heightStyle: "content"
			});

			// activarSeleccionadorFecha(clave,programa);
			return data;
		}).done(function(data){
			// focushistoria("id_search_historia_cargar");
			$("#id_search_historia_cargar").val("").focus();

			$('input#id_search_cirugias_sin_mercado'+programa).quicksearch('#Cirugias_sin_mercado_table'+programa+' .find');
			$('#divmonitor_'+clave).css("height","");
			$.unblockUI();
			blinkJqueryElementClass("css_paciente_alta_cx");
			blinkJqueryElementClass("css_img_liquidar");
			// cronUnixCargos();

		});
		// focushistoria('id_search_historia_cargar');
	}

	function cronUnixCargos()
	{
		// Se comenta esta rutina para evitar que se haga desde el monitor, a cambio de esto se crea un cron que se ejecute automáticamente
		$.post("MonitorFacturacionERP.php",
		{
			consultaAjax : '',
			wemp_pmla    : $('#wemp_pmla').val(),
			accion       : 'actualizacion_cron_cargos'
		}, function (data){
			if(data.error == 1)
			{
				jAlert(data.mensaje,"Mensaje");
			}
		},"json").done(function(){
			blinkJqueryElementClass("css_paciente_alta_cx");
			blinkJqueryElementClass("css_img_liquidar");
			//
		});
	}

	function reiniciarPlugins(programa)
	{
		blinkJqueryElementClass("css_paciente_alta_cx");
		blinkJqueryElementClass("css_img_liquidar");
		$('input#id_search_cirugias_sin_mercado'+programa).quicksearch('#Cirugias_sin_mercado_table'+programa+' .find');

		// --> Activar tabs jaquery
		$( "#tabsMonitorAuditoria" ).tabs({
			heightStyle: "content"
		});
		$('#buscarProSinAudi').quicksearch('#tablaProSinAudi .find');
		// --> 	Reloj temporizador
		activarRelojTemporizador();
		// --> Activar tooltip
		$("[tooltip=si]").tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
		// --> Blink a algunos elementos
		clearInterval(blinkReautorizar);
		blinkReautorizar = setInterval(function(){
			$("span[blink]").css('visibility' , $("span[blink]").css('visibility') === 'hidden' ? '' : 'hidden');
		}, 400);
		// --> Activar datapicker
		// $("#fechaProAudi").datepicker({
			// showOn: "button",
			// buttonImage: "../../images/medical/root/calendar.gif",
			// buttonImageOnly: true,
			// onSelect: function(){
				// pintarProcedimientosAuditados();
			// }
		// });
		// $("#fechaProAudi").next().css({"cursor": "pointer"}).attr("title", "Seleccione");
		// $("#fechaProAudi").after("&nbsp;");
		ajustarTamanoAcordeon();
		// --> Activar tooltip
		if(programa == "Cirugias_con_mercado_y_no_liquidadas" || programa == "Cirugias_sin_mercado")
		{
			// $("[tooltip3=si]").each(function(){
				// console.log($(this));
			// });
			$("[tooltip3=si]").tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
		}
	}

	function reiniciarPluginsPaf(){

			var wcconame = '';


			// $('input#buscadorPalabraClaveEmpresasPaf').quicksearch('table#tablePpalEmpresasPaf .find', {
				// 'delay': 300,
				// 'selector': 'th',
				// 'loader': 'span.loading',
				// 'bind': 'keyup click input',
				// 'prepareQuery': function (val) {
					// return new RegExp(val, "i");
				// },
				// 'testQuery': function (query, txt, _row) {
					// return query.test(txt);
				// }
			// });

			$('#buscadorPalabraClaveEmpresasPaf').quicksearch('#tablePpalEmpresasPaf .find');


			if($("#buscador_ccoPaf").val()!='')
			{

				wcconame = $("#buscador_ccoPaf").attr('name');
				wccocode = $("#buscador_ccoPaf").attr('valor');
			}
			else
			{
				wcconame ='';
				wccocode ='';
			}
			//wempname = $("#buscador_responsablesPaf").attr('name');
			//wempcode = $("#buscador_responsablesPaf").attr('valor');
			//alert(wcconame);
			//alert(wempname);

		iniciarbuscadorespaf(wcconame,wccocode,wempname ='',wempcode='');
		initAutocomplete();
		activarRelojTemporizador2();
	}

	function iniciarbuscadorespaf(wcconame='',wccocode='' , wempname='', wempcode=''){

		var ArrayValores  = eval('(' + $('#hidden_responsables_paf').val() + ')');
		var ArraySource   = new Array();
		var index		  = -1;
		for (var CodVal in ArrayValores)
		{
			index++;
			ArraySource[index] = {};
			ArraySource[index].value  = CodVal;
			ArraySource[index].label  = CodVal+"-"+ArrayValores[CodVal];
			ArraySource[index].name   = ArrayValores[CodVal];
		}



		$( "#buscador_responsablesPaf" ).autocomplete({
			minLength: 	3,
			source: 	ArraySource,
			select: 	function( event, ui ){
				$( "#buscador_responsablesPaf" ).val(ui.item.label);
				$( "#buscador_responsablesPaf" ).attr('valor', ui.item.value);
				$( "#buscador_responsablesPaf" ).attr('nombre', ui.item.name);
				filtrarListaPaf();
				return false;
			}
		});


		var ArrayValores  = eval('(' + $('#hidden_cco_paf').val() + ')');
		var ArraySource   = new Array();
		var index		  = -1;
		for (var CodVal in ArrayValores)
		{
			index++;
			ArraySource[index] = {};
			ArraySource[index].value  = CodVal;
			ArraySource[index].label  = CodVal+"-"+ArrayValores[CodVal];
			ArraySource[index].name   = ArrayValores[CodVal];
		}



		$( "#buscador_ccoPaf" ).autocomplete({
			minLength: 	1,
			source: 	ArraySource,
			select: 	function( event, ui ){
				$( "#buscador_ccoPaf" ).val(ui.item.label);
				$( "#buscador_ccoPaf" ).attr('valor', ui.item.value);
				$( "#buscador_ccoPaf" ).attr('nombre', ui.item.name);
				//$("#hiddenccopaf").val(ui.item.name);
				filtrarListaPaf();
				return false;
			}
		});

		if(wcconame!='')
		{
			$( "#buscador_ccoPaf" ).val(wccocode+"-"+wcconame);
			$( "#buscador_ccoPaf" ).attr('valor', wccocode);
			$( "#buscador_ccoPaf" ).attr('nombre', wcconame);

		}

		if(wempname !='')
		{
			$( "#buscador_responsablesPaf" ).val(wccocode+"-"+wempname);
			$( "#buscador_responsablesPaf" ).attr('valor', wempcode);
			$( "#buscador_responsablesPaf" ).attr('nombre', wempname);


		}

	}

	function filtrarListaPaf()
	{

		if($('#pafcheckbox').attr('checked')=='checked')
			var checboxpaf = 'si';
		else
			var checboxpaf = 'no';


			Empresas_paf( $("#wpafclave").val() ,$("#wpafprograma").val(),'',$("#buscador_ccoPaf").attr('valor'), $("#buscador_responsablesPaf").attr('valor'),'no' , $("#buscador_ccoPaf").attr('nombre'),$("#buscador_responsablesPaf").attr('nombre'), checboxpaf );




	}


	function recargarPaf()
	{
		if($('#pafcheckbox').attr('checked')=='checked')
			var checboxpaf = 'si';
		else
			var checboxpaf = 'no';
		Empresas_paf( $("#wpafclave").val() ,$("#wpafprograma").val(),'',$("#buscador_ccoPaf").attr('valor'), $("#buscador_responsablesPaf").attr('valor'),'no' , $("#buscador_ccoPaf").attr('nombre'),$("#buscador_responsablesPaf").attr('nombre'), checboxpaf );

	}


	/*function  Cirugias_con_mercado_y_no_liquidadas (clave, programa)
	{

		$('#divmonitor_'+clave).append("<img id='boton_"+clave+"'  src='../../images/medical/hce/hceB.png' valor='Pantalla completa' class='btn_lupa' onclick='agrandar(\""+clave+"\", \""+programa+"\")'>");

		$.post("MonitorFacturacionERP.php",
		{
			consultaAjax:      	'',
			wemp_pmla:         	$('#wemp_pmla').val(),
			accion:            	'Cirugias_con_mercado_y_no_liquidadas'
		}, function (data){


			// $("#divmonitor_"+clave).attr("programa" , ""+programa+"");
			// $("#divmonitor_"+clave).attr("numero" , ""+clave+"");
			// $('#divmonitor_'+clave).append("<div id='accordion"+clave+"' class='acordion'><h3 id='divtitulo_"+clave+"' ></h3><div  id='contenido_"+clave+"'></div></div>");
			// $('#divtitulo_'+clave).append("Cirugias con mercado  no liquidadas");
			// $('#contenido_'+clave).html(data);
					$( "#accordion"+clave+"" ).accordion({
						collapsible: true,
						heightStyle: "content"
					});
			$('#divmonitor_'+clave).append("<div id='accordionextra"+clave+"' class='acordion'><h3 id='divtituloextra_"+clave+"' ></h3><div  id='extra"+clave+"'></div></div>");
			$('#divtituloextra_'+clave).append("Cirugias con mercado no liquidadas de dias anteriores");


		});

		$.post("MonitorFacturacionERP.php",
		{
			consultaAjax:      	'',
			wemp_pmla:         	$('#wemp_pmla').val(),
			accion:            	'cirugias_con_mercado_dias_anteriores'
		}, function (data){



			$('#extra'+clave).html(data);
					$( "#accordionextra"+clave+"" ).accordion({
						collapsible: true,
						heightStyle: "content"
					});

		});




	}*/


	function CirugiasConCargosPendientesTarifa(clave, programa){

		$.post("MonitorFacturacionERP.php",
		{
			consultaAjax:      		'',
			wemp_pmla:         	$('#wemp_pmla').val(),
			accion:            	'CirugiasConCargosPendientesTarifa'
		}, function (data){

			if($("#divmonitor_"+clave).attr("programa")  != '' ||  $("#divmonitor_"+clave).attr("programa")  != 'undefined' )
			{
				$("#divmonitor_"+clave).attr("programa" , ""+programa+"");
				$("#divmonitor_"+clave).attr("numero" , ""+clave+"");
			}
			$('#divmonitor_'+clave).html('');
			$('#divmonitor_'+clave).append("<img id='boton_"+clave+"'  src='../../images/medical/hce/hceB.png' value='Pantalla completa' class='btn_lupa' onclick='agrandar(\""+clave+"\", \""+programa+"\")'>");
			$('#divmonitor_'+clave).append("<div id='accordion"+clave+"' class='acordion'><h3 id='divtitulo_"+clave+"' ></h3><div  id='contenido_"+clave+"'></div></div>");
			$('#divtitulo_'+clave).append("Cirugias Con Cargos Pendientes Por Tarifa");
			$('#contenido_'+clave).html(data);
					$( "#accordion"+clave+"" ).accordion({
						collapsible: true,
						heightStyle: "content"
					});

		});



	}




	function  crear_tarifa (historia, ingreso,responsable,concepto,procedimiento,n_procedimiento,wcco,valor, wtarifa,fecha,id, nombreempresa, codigoconcepto, nombreconcepto,parametro ,codprocedimientoempresa , nombreProEmpresa , origen , turno)
	{
		borrar_datos();
		$("#idcreartarifamensaje").removeClass('divmensajetarifa');
		$("#idbuttongrabartarifa").prop('disabled' , false);
		$("#idcreartarifamensaje").html('');
		$(".requerido").removeClass('faltanDatos');
		//validar_requeridos();
		cambiartipofacturacion();
		//$("#div_tarifas").html("");
		$( "#div_tarifas" ).dialog({
			show: {
				effect: "blind",
				duration: 100
			},
			hide: {
				effect: "blind",
				duration: 100
			},beforeClose: function( event, ui ) {

				actualizarMonitor(globalMonitor,globalnumero);
			},
			height: 400,
			width:  1000,
			dialogClass: 'fixed-dialog',
			modal: true,
			title: "Crear Tarifa"
		});

		$("#wresponsable_empresa").val(responsable);
		$("#wconcepto_empresa").val(concepto);
		$("#wfechadecargo").val(fecha);
		$("#wprocedimiento").val(procedimiento);
		$("#wnombre_procedimiento").val(n_procedimiento);
		$("#wnumero_id").val(id);
		$("#wvalor").val(valor);
		$("#wtar").val(wtarifa);
		$("#wturnohidden").val(turno);

		if (wcco !='')
		{
			$("#td_wcco").html("<select id='wcco' class='requerido' style='width:150px' ><option value='"+wcco+"' >"+wcco+"</option><option value='*' selected>*-TODOS</option></select>");
		}
		else
		{
			$("#td_wcco").html("<select id='wcco' class='requerido'  style='width:150px' ><option value='*' selected>*-TODOS</option></select>");
		}
		$("#wnombreempresa").val(nombreempresa);
		$("#codigoconcepto").val(codigoconcepto);
		$("#nombreconcepto").val(nombreconcepto);

		// si viene desde la tabla de auditoria (cliame_0000253 )llenaria estos campos y el campo concepto funcionaria como un
		// autocompletar
		if(origen == 'Auditoria')
		{

			$("#wprocedimiento_empresa").val(codprocedimientoempresa);
			$("#wnom_procedimiento_empresa").val(nombreProEmpresa);

			if($("#wprocedimiento_empresa").val()!='')
			{
				$("#wprocedimiento_empresa").removeClass("campoRequerido");
				$("#wprocedimiento_empresa").prop('disabled', true);
			}
			if($("#wnom_procedimiento_empresa").val()!='')
			{
				$("#wnom_procedimiento_empresa").removeClass("campoRequerido");
				$("#wnom_procedimiento_empresa").prop('disabled' , true);
			}
			$("#codigoconcepto").prop('disabled', true);

		}
		else
		{
			$("#wnom_procedimiento_empresa").prop('disabled' , false);
			$("#wprocedimiento_empresa").prop('disabled', false);
			$("#codigoconcepto").prop('disabled', true);
		}
		$("#parametro").val(parametro);
		$("#origen").val(origen);

		iniciar_requerido();

	}


	// funcion que llena el campo de nombre de concepto cuando se esta creando tarifas
	function traerNombreConcepto()
	{

		var nombre =  $("#codigoconcepto option:selected").text();

		nombre = nombre.split("-");

		$("#nombreconcepto ").val(nombre[1]);

	}
	//---------------------------------------


	function cancelarCrearTarifa(programa)
	{
		jAlert(programa,"Mensaje");

	}

	function Crear_tarifa_unix(concepto, procedimiento, tarifa,parametro)
	{

		// alert(parametro);
		// alert(procedimiento);
		$("#wcco_exa").val('3081');
		$("#examen_codigo").val(procedimiento);
		$("#examen_concepto").val(concepto);
		$("#examen_tarifa").val(tarifa);
		$("#wparametro_examen").val(parametro);
		$("#tipo_facturacion_examen").val('');
		$("#codigo_anexo_examen").val('');
		$("#examen_nombre").val('');
		$("#valor_actual").val('');
		$("#wfecha_tarifa_examen").val('');
		$("#valor_anterior").val('');
		//$("#porcentaje_exa").val('');

		validar_requeridos_exa();

		if(parametro =='NT')
		{
			// poner atributo disabled
			$("#examen_nombre").attr("disabled" ,"disabled"  );
			$("#codigo_anexo_examen").attr("disabled" ,"disabled"  );
			$("#tipo_facturacion_examen").attr("disabled" ,"disabled" );
			//----------------------------------------------------------

			// Remover clases de requerido
			$("#examen_nombre").removeClass("campoRequerido_exa");
			$("#codigo_anexo_examen").removeClass("campoRequerido_exa");
			$("#tipo_facturacion_examen").removeClass("campoRequerido_exa");

			$("#examen_nombre").removeClass("requerido_exa");
			$("#codigo_anexo_examen").removeClass("requerido_exa");
			$("#tipo_facturacion_examen").removeClass("requerido_exa");
			//-------------------------------------------------------

			// consultar nombre de examen , nombre de concepto
			$.post("MonitorFacturacionERP.php",
			{
				consultaAjax:      	'',
				wemp_pmla:         	$('#wemp_pmla').val(),
				accion:            	'Consulta_datos_unix',
				wcod_examen:		procedimiento
			}, function (data){

					$("#examen_nombre").val(data.nombre);
					$("#codigo_anexo_examen").val(data.anexo);
					$("#tipo_facturacion_examen").val(data.liquidacion);
					$("#cod_grupo_examen").val(data.grupo_examen);
					$("#cod_nivel").val(data.nivel);


			},'json');
		}
		else
		{
			// Elimino el atributo disabled, para que se puedan ingresar los datos
			$("#examen_nombre").removeAttr("disabled");
			$("#codigo_anexo_examen").removeAttr("disabled");
			//$("#tipo_facturacion_examen").removeAttr("disabled");
			//-----------------------------------------------------------------
			$("#tipo_facturacion_examen").val('C');
			$("#cod_grupo_examen").val('62');
			$("#cod_nivel").val('3');



			// Adiciono la Clase requerido y campo requerido para controlar el ingreso de los datos
			$("#examen_nombre").addClass("campoRequerido_exa");
			$("#codigo_anexo_examen").addClass("campoRequerido_exa");
			//$("#tipo_facturacion_examen").addClass("campoRequerido_exa");

			$("#examen_nombre").addClass("requerido_exa");
			$("#codigo_anexo_examen").addClass("requerido_exa");
			//$("#tipo_facturacion_examen").addClass("requerido_exa");
			//------------------------------------------------------------------

			iniciar_requerido();



		}
		//----------------------------------------------------------


		$( "#div_tarifas_unix" ).dialog({
			show: {
				effect: "blind",
				duration: 100
			},
			hide: {
				effect: "blind",
				duration: 100
			},
			height: 400,
			width:  1000,
			dialogClass: 'fixed-dialog',
			modal: true,
			title: "Crear Tarifa UNIX"
		});
	}


	function  traer_rol()
	{
		$.post("MonitorFacturacionERP.php",
		{
			consultaAjax:      		'',
			wemp_pmla:         		$('#wemp_pmla').val(),
			accion:            		'traer_rol'
		}, function (data){

			$("#wcodigo_cargo_wuse").val(data.wcodigo_cargo_wuse);
			$("#arr_permisos_opciones").val(data.arr_permisos_opciones);
			$("#perfilMonitorAud").val(data.perfilMonitorAud);
			$("#filtroEmpresasRevisar").val(data.filtroEmpresasRevisar);

			var j = 1;
			var cant = data.cantidad;
			var auxcantidad = data.cantidad;
			if( cant % 2 ==1)
			{
				cant = cant + 1;
			}

			cant = cant / 2;

			respuesta = data.array_rol;

			cantw='400';
			canth='250';


			$("#divprincipal").html('');

			if(auxcantidad == 1 || auxcantidad == 2)
			{
				$("#divprincipal").html('<div id="splitContainer">'
					+'<div>'
						+'<div id="divmonitor_1" class="click_global">'
						+'</div>'
					+'</div>'
					+'<div>'
						+'<div id="divmonitor_2" class="click_global">'
						+'</div>'
					+'</div>'
				+'</div>');
				if(auxcantidad == 2)
				{
					$('#splitContainer').jqxSplitter({ height: '100%', width: '100%', orientation: 'horizontal', panels: [{ size: '50%' }, { size: '50%' } ] });
				}

			}

			if(auxcantidad == 3 || auxcantidad == 4)
			{
				$("#divprincipal").html('<div id="splitContainer">'
											+'<div>'
												+'<div id="division_1">'
															+'<div id="divmonitor_1" class="click_global" >'
															+'</div>'
															+'<div id="divmonitor_2" class="click_global">'
															+'</div>'
												+'</div>'
											+'</div>'
											+'<div>'
												+'<div id="division_2">'
															+'<div id="divmonitor_3" class="click_global" >'
															+'</div>'
															+'<div id="divmonitor_4" class="click_global">'
															+'</div>'
												+'</div>'
											+'</div>'
										+'</div>');
			    $('#splitContainer').jqxSplitter({ height: '100%', width: '100%', orientation: 'vertical', panels: [{ size: '50%' }, { size: '50%' } ] });
			    $('#division_1').jqxSplitter({ height: '100%', width: '100%', orientation: 'horizontal',  panels: [{ size: '50%' }, { size: '50%'}] });
			    $('#division_2').jqxSplitter({ height: '100%', width: '100%', orientation: 'horizontal',  panels: [{ size: '50%' }, { size: '50%'}] });
			}
			if(auxcantidad == 5 || auxcantidad == 6)
			{
				$("#divprincipal").html('<div id="splitContainer">'
												+'<div>'
													+'<div id="division_1">'
																+'<div id="divmonitor_1" style="width : '+cantw+'; height : '+canth+'px; overflow-y: scroll " class="click_global">'
																+'</div>'
																+'<div id="divmonitor_2" style="width : '+cantw+'; height : '+canth+'px; overflow-y: scroll " class="click_global">'
																+'</div>'
													+'</div>'
												+'</div>'
												+'<div>'
													+'<div id="division_2">'
																+'<div >'
																		+'<div id="division_3">'
																				+'<div id="divmonitor_3" style="width : '+cantw+'; height : '+canth+'px; overflow-y: scroll " class="click_global" >'
																				+'</div>'
																				+'<div id="divmonitor_4" style="width : '+cantw+'; height : '+canth+'px; overflow-y: scroll "  class="click_global" >'
																				+'</div>'
																				+'</div>'
																+'</div>'
																+'<div ">'

																			//
																			+'<div id="division_4">'
																				+'<div id="divmonitor_5" style="width : '+cantw+'; height : '+canth+'px; overflow-y: scroll " class="click_global">'
																				+'</div>'
																				+'<div id="divmonitor_6" style="width : '+cantw+'; height : '+canth+'px; overflow-y: scroll " class="click_global">'
																				+'</div>'
																				+'</div>'

																			//
																+'</div>'
													+'</div>'
												+'</div>'
											+'</div>');
			    $('#splitContainer').jqxSplitter({ height: '100%', width: '100%', orientation: 'horizontal', panels: [{ size: '33%' }, { size: '33%' } ] });
			    $('#division_1').jqxSplitter({ height: '100%', width: '100%', orientation: 'vertical',  panels: [{ size: '50%' }, { size: '50%'}] });
			    $('#division_2').jqxSplitter({ height: '100%', width: '100%', orientation: 'horizontal',  panels: [{ size: '50%' }, { size: '50%'}] });
			    $('#division_3').jqxSplitter({ height: '100%', width: '100%', orientation: 'vertical	',  panels: [{ size: '50%' }, { size: '50%'}] });
			    $('#division_4').jqxSplitter({ height: '100%', width: '100%', orientation: 'vertical',  panels: [{ size: '50%' }, { size: '50%'}] });
			}

			if(auxcantidad == 7 || auxcantidad == 8)
			{
				/*
				$("#divprincipal").html('<div id="splitContainer">'
											+'<div>'
												+'<div id="division_1">'
													+'<div>'
														+'<div id="divmonitor_1" class="click_global">'
														+'</div>'
													+'</div>'
													+'<div>'
														+'<div id="divmonitor_2" class="click_global" >'
														+'</div>'
													+'</div>'
												+'</div>'
											+'</div>'
											+'<div>'
												+'<div id="division_2">'
													+'<div>'
														+'<div id="divmonitor_3" class="click_global">'
														+'</div>'
													+'</div>'
													+'<div>'
														+'<div id="divmonitor_4" class="click_global">'
														+'</div>'
													+'</div>'
												+'</div>'
											+'</div>'
											+'<div>'
												+'<div id="division_3">'
													+'<div>'
														+'<div id="divmonitor_5" class="click_global">'
														+'</div>'
													+'</div>'
													+'<div>'
														+'<div id="divmonitor_6" class="click_global">'
														+'</div>'
													+'</div>'
												+'</div>'
											+'</div>'
											+'<div>'
												+'<div id="division_4">'
													+'<div>'
														+'<div id="divmonitor_7" class="click_global">'
														+'</div>'
													+'</div>'
													+'<div>'
														+'<div id="divmonitor_8" class="click_global">'
														+'</div>'
													+'</div>'
												+'</div>'
											+'</div>'
											+'<div>'
												+'<div id="division_5">'
													+'<div>'
														+'<div id="divmonitor_9" class="click_global">'
														+'</div>'
													+'</div>'
													+'<div>'
														+'<div id="divmonitor_10" class="click_global">'
														+'</div>'
													+'</div>'
												+'</div>'
											+'</div>'
											+'<div>'
												+'<div id="division_6">'
													+'<div>'
														+'<div id="divmonitor_11" class="click_global">'
														+'</div>'
													+'</div>'
													+'<div>'
														+'<div id="divmonitor_12" class="click_global">'
														+'</div>'
													+'</div>'
												+'</div>'
											+'<div>'
										+'</div>');
				 */
				$("#divprincipal").html('<div id="splitContainer">'
												+'<div>'
													+'<div id="division_1">'
																//
																	+'<div >'
																		+'<div id="division_2">'
																				+'<div id="divmonitor_1" class="click_global">'
																				+'</div>'
																				+'<div id="divmonitor_2" class="click_global" >'
																				+'</div>'
																				+'</div>'
																+'</div>'
																+'<div ">'

																			//
																			+'<div id="division_3">'
																				+'<div id="divmonitor_3" class="click_global">'
																				+'</div>'
																				+'<div id="divmonitor_4" class="click_global">'
																				+'</div>'
																				+'</div>'

																			//
																+'</div>'

																//
													+'</div>'
												+'</div>'
												+'<div>'
													+'<div id="division_4">'
																+'<div >'
																		+'<div id="division_5">'
																				+'<div id="divmonitor_5" class="click_global">'
																				+'</div>'
																				+'<div id="divmonitor_6" class="click_global">'
																				+'</div>'
																				+'</div>'
																+'</div>'
																+'<div ">'

																			//
																			+'<div id="division_6">'
																				+'<div id="divmonitor_7" class="click_global">'
																				+'</div>'
																				+'<div id="divmonitor_8" class="click_global">'
																				+'</div>'
																				+'</div>'

																			//
																+'</div>'
													+'</div>'
												+'</div>'
											+'</div>');
			    $('#splitContainer').jqxSplitter({ height: '100%', width: '100%', orientation: 'horizontal', panels: [{ size: '50%' ,min: "5%" , collapsible: false}, { size: '50%' ,min: "5%", collapsible: false} ] });
			    $('#division_1').jqxSplitter({ height: '100%', width: '100%', orientation: 'horizontal',  panels: [{ size: '50%'  ,min: "5%", collapsible: false}, { size: '50%',min: "5%", collapsible: false}] });
			    $('#division_2').jqxSplitter({ height: '100%', width: '100%', orientation: 'vertical',  panels: [{ size: '50%' ,min: "5%", collapsible: false}, { size: '25%',min: "5%", collapsible: false}] });
			    $('#division_3').jqxSplitter({ height: '100%', width: '100%', orientation: 'vertical',  panels: [{ size: '50%',min: "5%" , collapsible: false}, { size: '25%',min: "5%", collapsible: false}] });
			    $('#division_4').jqxSplitter({ height: '100%', width: '100%', orientation: 'horizontal',  panels: [{ size: '50%' ,min: "5%", collapsible: false}, { size: '50%',min: "5%", collapsible: false}] });
			    $('#division_5').jqxSplitter({ height: '100%', width: '100%', orientation: 'vertical',  panels: [{ size: '50%',min: "5%" , collapsible: false}, { size: '25%',min: "5%", collapsible: false}] });
			    $('#division_6').jqxSplitter({ height: '100%', width: '100%', orientation: 'vertical',  panels: [{ size: '50%',min: "5%" , collapsible: false}, { size: '25%',min: "5%", collapsible: false}] });
			}

			for (var clave in respuesta)
			{
				if	(respuesta[clave] =='monitor_laboratorios')
				{

					traer_examenes_problemas(j , "traer_examenes_problemas");
					//setInterval('traer_examenes_problemas()',100000);
					j++;
				}

				if	(respuesta[clave] =='monitor_correos')
				{

					traer_correos(j , "traer_correos");
					//setInterval('traer_correos()',100000);
					j++;
				}

				if	(respuesta[clave] =='cargos_sin_tarifa')
				{


					cargos_sin_tarifa(j , "cargos_sin_tarifa");
					//setInterval('cargos_sin_tarifa()',100000);
					j++;
				}

				if	(respuesta[clave] =='Cirugias_sin_mercado')
				{

					wfecha = '';

					Cirugias_sin_mercado(j , "Cirugias_sin_mercado" , wfecha );
					//setInterval('Cirugias_sin_mercado()',100000);
					j++;
				}

				if	(respuesta[clave] =='Cirugias_con_mercado_y_no_liquidadas')
				{


					wfecha = '';

					// Cirugias_con_mercado_y_no_liquidadas(j,"Cirugias_sin_mercado" , wfecha , actual);
					Cirugias_con_mercado_y_no_liquidadas(j,"Cirugias_con_mercado_y_no_liquidadas" , wfecha);
					//setInterval('Cirugias_con_mercado_y_no_liquidadas()',100000);
					j++;
				}

				if	(respuesta[clave] =='Cirugias_Con_Cargos_Pendientes')
				{

					CirugiasConCargosPendientesTarifa(j , "CirugiasConCargosPendientesTarifa");
					//setInterval('CirugiasConCargosPendientesTarifa()',100000);
					j++;
				}

				if	(respuesta[clave] =='Pacientes_proceso_alta_sin_estancia_liq')
				{

					Pacientes_proceso_alta_sin_estancia_liq(j , "Pacientes_proceso_alta_sin_estancia_liq");
					//setInterval('Pacientes_proceso_alta_sin_estancia_liq()',100000);
					j++;
				}

				if	(respuesta[clave] =='Procedimientos_no_autorizados')
				{

					Procedimientos_no_autorizados(j , "Procedimientos_no_autorizados");
					//setInterval('Procedimientos_no_autorizados()',100000);
					j++;
				}

				if	(respuesta[clave] =='Empresas_paf')
				{

					Empresas_paf(j , "Pacientes PAF");
					//setInterval('Procedimientos_no_autorizados()',100000);
					j++;
				}

				if	(respuesta[clave] =='monitor_medicamentos')
				{

					monitor_medicamentos(j , "Monitor Medicamentos");
					//setInterval('Procedimientos_no_autorizados()',100000);
					j++;
				}
			}

			$(".click_global").click(function(){
				globalMonitor = $(this).attr("programa");
				globalnumero =  $(this).attr("numero");
			});
		}, 'json').done(function(){

		});
	}

	function agrandar(clave,programa)
	{
		var fecha	  = ($("#fechas_cirugias").val() == undefined) ? $("#fechas_cirugias_oculto").val(): $("#fechas_cirugias").val();
		var accion_vw = (btn_clave == '') ? "Pantalla Completa": btn_clave;
		globalMonitor = programa;
		globalnumero =  clave;
		if( accion_vw == 'Volver')
		{
			// $("#divprincipal").html("");
			// $("#divprincipal").html(clonadoppal);
			var html_temp_secundario = $("#divsecundario").html();
			$("#divprincipal").find("#divmonitor_"+clave).html(html_temp_secundario);
			$("#divsecundario").html("");
			$("#divprincipal").show();

			// $("#boton_"+clave).attr('valor' ,'Pantalla Completa');
			btn_clave = "Pantalla Completa";
			// $( ".acordion" ).accordion({
			// 	collapsible: true,
			// 	heightStyle: "content"
			// });
		}
		else
		{
			$("#boton_"+clave).attr('valor' , 'Volver');
			btn_clave = 'Volver';
			// clonadoppal = $("#divprincipal").html();
			// clonadadiv = $("#divmonitor_"+clave).html();

			$("#divprincipal").hide();
			var html_temp = $("#divprincipal").find("#divmonitor_"+clave).html();
			$("#divprincipal").find("#divmonitor_"+clave).html("");
			$("#divsecundario").html(html_temp).show();

			// $("#divprincipal").html("");
			// $("#divprincipal").html("<center><div id='divmonitor_"+clave+"' programa='"+programa+"' numero='"+clave+"'>"+clonadadiv+"</center>");

			// $( ".acordion" ).accordion({
			// 	collapsible: true,
			// 	heightStyle: "content"
			// });
		}


	 	// console.log(accion_vw);
		// console.log(clonadoppal);
		inicializar_fecha_cirugia(clave,programa,fecha);
		reiniciarPlugins(programa);
		reiniciarPluginsPaf();
		// focushistoria("id_search_historia_cargar");
	}

	function inicializar_fecha_cirugia(clave,programa,fecha)
	{


			$("#td_campo_fecha").html("");
			$("#td_campo_fecha").append("<input type='text' id='fechas_cirugias' value='"+fecha+"'>");
			$("#fechas_cirugias_oculto").val(fecha);
			$("#fechas_cirugias").datepicker({
			showOn: "button",
			buttonImage: "../../images/medical/root/calendar.gif",
			buttonImageOnly: true,
			changeYear: true,
            changeMonth: true,
            monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio', 'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
            monthNamesShort: ['Enero','Febrero','Marzo','Abril','Mayo','Junio', 'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
            yearRange: '-2:+1',
			onSelect: function(dateText, inst) {

				 var date = $(this).val();
				 $("#fechas_cirugias_oculto").val(date);
				 actualizarMonitor(programa,clave,date);
			}
			}).attr("disabled", "disabled");
			$("#fechas_cirugias").next().css({"cursor": "pointer"}).attr("title", "Seleccione");
			$("#fechas_cirugias").after("&nbsp;");



	}

	function actualizarMonitor(globalMonitor,globalnumero,date)
	{
		//alert(globalMonitor+"---"+globalnumero);
		// alert("actualizomonitor");
		eval( ""+globalMonitor+"("+globalnumero+" , "+globalMonitor+" , '"+date+"' )" );

		// --> Activar tooltip
		if(globalMonitor == "Cirugias_con_mercado_y_no_liquidadas" || globalMonitor == "Cirugias_sin_mercado"){
			setTimeout( function(){
				$("[tooltip3=si]").tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
			}, 500);
		}
	}

	function procedimiento_autorizado_seleccionado()
	{

		//alert($("#cupsAutorizados").val());
		crear_div_procedimiento('grabarpaciente','divmercado','no', $("#cupsAutorizados").val(),'','','','',$("#cupsAutorizados option:selected").text(),'','','','off','');
		//crear_div_procedimiento('grabarpaciente','divmercado','no', ui.item.valor,'','','','',ui.item.nombre,'','','','off','');


	}


	/**
        Esta funcion es referenciada en funciones.php por la función pintarMenuSeleccion(..) que es la que se encarga de pintar los menús con sus eventos.

        cod_tab     : Código de parametrización de la pestaña, menú o submenú.
        include     : Es el archivo o página que se debe ejecutar al darle clic al menú.
        params      : En este campo solo se está enviando el valor "consultaAjax=".
        cod_emp     : A la fecha, inicialmente se esta creando con el valor "[*WCODIGO*]" que posteriormente se reemplaza por '' y finalmente por el momento este parámetro no tiene uso.
        tab_cod     : Es el código único de cada menú o submenú.
        tab_nombre  : Es la descripción o nombre con el que está guardado el menú en la tabla.
    */
    function recargar(cod_tab,include,params,cod_emp,div,tab_cod,tab_nombre)
    {
        recargable = $('#url_tal').val();
        $('#wcodtab_tal').val(tab_cod);
        if(recargable == '' && include != '#' && include != '' && include != '.')
        {
            $('#url_tal').val(cod_tab);
            document.form_comun.submit();
            //return false;
        }
        else
        {
            getinclude_once(include,params,cod_emp,div,tab_cod, tab_nombre);
        }
        //return true;
    }

    /**
        El programa talento.php tiene una zona (un div con campos hidden) en los que se pueden adicionar otros campos que pueden ser compartidos por todas las demás pestañas o menús
        (por todos los programas que sean abiertos por cada menú).

        Esta función se encarga de adicionar a la url de cada menú todos los campos que estén en esta zona compartida.

        include     : Es el archivo o página que se debe ejecutar al darle clic al menú.
        params      : En este campo solo se está enviando el valor "consultaAjax=".
        cod_emp     : A la fecha, inicialmente se esta ndo con el valor "[*WCODIGO*]" que posteriormente se reemplaza por '' y finalmente por el momento este parámetro no tiene uso.
        tab_cod     : Es el código único de cada menú o submenú.
        tab_nombre  : Es la descripción o nombre con el que está guardado el menú en la tabla.
     */
    function getinclude_once(include,params,cod_emp,div,tab_cod, tab_nombre)  /* incluye otros script a la plantilla actual */
    {
        $('#url_tal').val('');

        url_add_params = addUrlCamposCompartidosTalento();

        if(include != '' && include != '#')
        {
            pos = include.indexOf("?"); // busca si exise el signo ?
            if(pos == -1)
            {
                include = include+'?'; // Si no existe lo pone antes de agregar los parámetros
            }
            else
            {
                include = include+'&'; // si existe adiciona '&' para adicionar más parámetros
            }

            // include = include+params+"&wuse="+cod_emp+"&wuse_listado="+cod_emp_varios+"&contenedorPadre="+div;
            include = '../../../'+$('#wroot_group_tal').val()+include+params+"&contenedorPadre="+div+url_add_params;

            $('#'+div).html('&nbsp;');
            $("#visor_espera").html('<br /><br /><br /><br /><br /><br /><br /><br /><br /><br />'
                            +'<img  width="13" height="13" src="../../images/medical/ajax-loader7.gif" />&nbsp;<font style="font-weight:bold; color:#2A5DB0; font-size:13pt" >Iniciando m&oacute;dulo...</font>'
                            +'<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />');
            $.post(include, function(data) {
                $('#'+div).html(function() {
                                  $("#visor_espera").html('');
                                  return data;
                                }
                );
                $('#visor_programas_titulo').html('&raquo; '+tab_nombre);
            });
        }
        else
        {
            return false;
        }
    }

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


	function evaluar_procedimiento ()
	{
		 var value = $( "input:checked" ).val();



		 if(value =='paquete')
		 {
			//$("#etiquetatitulo").html("Agregar " +value+" :");
			$("#busc_procedimiento").hide();
			$("#busc_paquete").show();
			//$("#procedimiento_autorizado").hide();
		 }
		 if(value =='procedimiento')
		 {

			//$("#etiquetatitulo").html(" <input id='checkeado' onchange='todos_los_procedimientos()' type='checkbox' checked >Px Autorizados (Admisiones):");
			// $("#busc_procedimiento").show();
			$("#busc_paquete").hide();
			//$("#procedimiento_autorizado").show();

		 }


	}

	function todos_los_procedimientos()
	{

		//$("#etiquetatitulo").html("<input onchange='todos_los_procedimientos()' type='checkbox'  > Agregar Procedimientos:");
		// $("#busc_procedimiento").show();
		$("#busc_paquete").hide();


	}

	function cargar_procedimiento()
	{

		var ArrayValores = eval('(' + $("#hidden_procedimiento").val() + ')');
		var procedimientos 	= new Array();
		var index		  	= -1;
		for (var cod_pro in ArrayValores)
		{
			index++;
			procedimientos[index] = {};
			procedimientos[index].value  = cod_pro+'-'+ArrayValores[cod_pro];
			procedimientos[index].label  = cod_pro+'-'+ArrayValores[cod_pro];
			procedimientos[index].nombre = cod_pro+'-'+ArrayValores[cod_pro];
			procedimientos[index].valor  = cod_pro;
		}
		$( "#busc_procedimiento" ).autocomplete({
			minLength: 	3,
			source: 	procedimientos,
			select: 	function( event, ui ){
				$("#busc_procedimiento").val(ui.item.nombre);
				$("#busc_procedimiento").attr('valor', ui.item.valor);
				$("#busc_procedimiento").attr("nombre", ui.item.nombre);
				crear_div_procedimiento('grabarpaciente','divmercado','no', ui.item.valor,'','','','',ui.item.nombre,'','','','off','');
				$("#busc_procedimiento").val('');
				$("#busc_procedimiento").attr('valor', '');
				$("#busc_procedimiento").attr("nombre", '');
				return false;
			}
		});

	}

	function pintarCupsAutorizados()
	{
		$.post("MonitorFacturacionERP.php",
		{
			consultaAjax : '',
			wemp_pmla    : $('#wemp_pmla').val(),
			accion       : 'pintarCupsAutorizados',
			whistoria    : $('#whistoriaoculto').val(),
			wingreso     : $('#wingresooculto').val(),
			wcedula      : $('#wcedulaoculto').val(),
			wcodigoturno : $('#wcodigoturno').val()

		},function(data){
			//alert(data);
			$("#procedimiento_autorizado").html(data.html);
		},'json');
	}

	function crear_div_procedimiento(operacion,div,modal, c_procedimiento,c_responsable,c_tarifa,n_tarifa,n_responsable,n_procedimiento,whistoria,wingreso,tipoEmpresa,paquete,n_tipo_empresa)
	{
		if( $("#div_"+c_procedimiento).length == 0)
		{
			$("#"+div).append("<br><div  id='acordion_div_"+c_procedimiento+"'  ><h3>"+n_procedimiento+"</h3><div id='div_"+c_procedimiento+"'   procedimiento='"+c_procedimiento+"'></div></div>");
			ventana_insumo(operacion,"div_"+c_procedimiento+"",modal, c_procedimiento,c_responsable,c_tarifa,n_tarifa,n_responsable,n_procedimiento,whistoria,wingreso,tipoEmpresa,paquete,n_tipo_empresa,'', '', '','','');
		}
		else
		{
			jAlert("el mercado para este procedimiento ya esta agregado","Mensaje");
		}
	}

	function cambiar_tipo_busqueda (valor)
	{

		if(valor == 'cedula')
		{
			$("#divtipobusqueda_leyenda").html("Digite Cedula  :");
			$("#divtipobusqueda_input").html("<input type='text' size='40' id='wcedula'>");
			$("#wcedula").val($("#wcedulaoculto").val());
		}
		else
		{
			$("#divtipobusqueda_leyenda").html("Digite Historia  :")
			$("#divtipobusqueda_input").html("<input type='text' size='40' id='whistoria'>");
		}

	}
	function anular_mercado(cedula, wcodigoturno)
	{
		// var wperfil = $("#wperfiloculto").val(); Cuando anular es la primer acción que se hace sobre el monitor, esta variable aún no tiene valor, si antes abre editar o devolver,
		// se inicializará esa variable.

		$.when( customConfirmBorrarMercado('Confirmar borrar mercado') ).then(
			function(confirm) {
				if(confirm)
				{
					$.post("MonitorFacturacionERP.php",
					{
						consultaAjax : '',
						wemp_pmla    : $('#wemp_pmla').val(),
						accion       : 'anular_mercado',
						whistoria    : $('#whistoria').val(),
						wingreso     : $('#wing_tal').val(),
						wcedula      : cedula,
						wcodigoturno : wcodigoturno

					},function(data){
						if(data.error == 1)
						{
							jAlert("Se generó un error interno al momento de borrar el mercado.","Mensaje");
						}
						else
						{
							if(data.mercadoLiquidadoCerrado == 'on')
							{
								jAlert("El mercado ya fue cerrado previamente o ya ha sido liquidado, No es posible anular/borrar el mercado","Mensaje");
							}
							actualizarMonitor(globalMonitor,globalnumero, $("#fechas_cirugias").val());
						}
					},"json");
				}
				else
				{
					// alert( "NO OK" );
					console.log("Este console no se puede ejecutar si se esta pidiendo la contraseña, se supone que customConfirmBorrarMercado solo emite una respuesta cuando dfd.resolve(true); o dfd.resolve(false);");
				}
		});

		/*jConfirm('Realmente desea ELIMINAR TODO EL MERCADO de este turno de cirug&iacute;a?', 'Anular mercado completo', function(r) {
			// console.log(r);
            if(r){
            	$.post("MonitorFacturacionERP.php",
				{
					consultaAjax : '',
					wemp_pmla    : $('#wemp_pmla').val(),
					accion       : 'anular_mercado',
					whistoria    : $('#whistoria').val(),
					wingreso     : $('#wing_tal').val(),
					wcedula      : cedula,
					wcodigoturno : wcodigoturno

				},function(data){
					if(data.error == 1)
					{
						alert("Se generó un error interno al momento de borrar el mercado.");
					}
					else
					{
						if(data.mercadoLiquidadoCerrado == 'on')
						{
							alert("El mercado ya fue cerrado previamente o ya ha sido liquidado, No es posible anular/borrar el mercado");
						}
						actualizarMonitor(globalMonitor,globalnumero, $("#fechas_cirugias").val());
					}
				},"json");
			}
        });*/

		/*var txt;
		var r = confirm("Desea Anular el Mercado ?");
		if (r == true) {
			$.post("MonitorFacturacionERP.php",
			{
				consultaAjax : '',
				wemp_pmla    : $('#wemp_pmla').val(),
				accion       : 'anular_mercado',
				whistoria    : $('#whistoria').val(),
				wingreso     : $('#wing_tal').val(),
				wcedula      : cedula,
				wcodigoturno : wcodigoturno

			},function(data){
				if(data.error == 1)
				{
					alert("Se generó un error interno al momento de borrar el mercado.");
				}
				else
				{
					if(data.mercadoLiquidadoCerrado == 'on')
					{
						alert("El mercado ya fue cerrado previamente o ya ha sido liquidado, No es posible anular/borrar el mercado");
					}
					actualizarMonitor(globalMonitor,globalnumero, $("#fechas_cirugias").val());
				}
			},"json");

		} else {

		}*/
	}

    function customConfirmBorrarMercado(customMessage) {
	    var dfd = new jQuery.Deferred();
	    // $("#dialog_password_borrar").html(customMessage);
	    $("#dialog_password_borrar").dialog({
	        resizable: false,
	        height: 160,
	        modal: true,
	        title: customMessage,
	        buttons: {
	            "OK": function () {
	            	var dialogThis = $(this);
	            	$.post("MonitorFacturacionERP.php",
					{
						consultaAjax : '',
						wemp_pmla    : $('#wemp_pmla').val(),
						accion       : 'validar_pss_borrar',
						validar      : $("#pass_borrar_mercado").val()

					},function(data){
						$("#pass_borrar_mercado").val("");
						if(data.error == 1)
						{
							jAlert(data.mensaje, "Mensaje");
							// dfd.resolve(false);
						}
						else
						{
	                		dialogThis.dialog("close");
							dfd.resolve(true);
						}
					},"json");
	                // alert(true);
	            },
	            Cancel: function () {
	                $(this).dialog("close");
	                // alert(false);
	                dfd.resolve(false);
	            }
	        }
	    });
	   return dfd.promise();
	}



	function Pacientes_proceso_alta_sin_estancia_liq(clave, programa){

		$.post("MonitorFacturacionERP.php",
		{
			consultaAjax:      		'',
			wemp_pmla:         	$('#wemp_pmla').val(),
			accion:            	'Pacientes_proceso_alta_sin_estancia_liq'
		}, function (data){

			//alert($("#divmonitor_"+clave).attr("programa") );
			if($("#divmonitor_"+clave).attr("programa")  != '' ||  $("#divmonitor_"+clave).attr("programa")  != 'undefined' )
			{
				$("#divmonitor_"+clave).attr("programa" , ""+programa+"");
				$("#divmonitor_"+clave).attr("numero" , ""+clave+"");
			}
			$('#divmonitor_'+clave).html('');
			$('#divmonitor_'+clave).append("<img id='boton_"+clave+"'  src='../../images/medical/hce/hceB.png' valor='Pantalla completa' class='btn_lupa' onclick='agrandar(\""+clave+"\", \""+programa+"\")'>");

			$('#divmonitor_'+clave).append("<div id='accordion"+clave+"' class='acordion'><h3 id='divtitulo_"+clave+"' ></h3><div  id='contenido_"+clave+"'></div></div>");
			$('#divtitulo_'+clave).append("Pacientes en proceso de alta sin estancia liquidada");
			$('#contenido_'+clave).html(data);
					$( "#accordion"+clave+"" ).accordion({
						collapsible: true,
						heightStyle: "content"
					});


		});



	}

	function Procedimientos_no_autorizados(clave, programa){
		var mensajeMonitor = "";
		$.post("MonitorFacturacionERP.php",
		{
			consultaAjax          : '',
			wemp_pmla             : $('#wemp_pmla').val(),
			accion                : 'Procedimientos_no_autorizados',
			clave                 : clave,
			arr_permisos_opciones : $("#arr_permisos_opciones").val(),
			arr_roles_cx          : JSON.stringify(arr_roles_cx),
			perfilMonitorAud      : $("#perfilMonitorAud").val()
		}, function (data){

			//alert($("#divmonitor_"+clave).attr("programa") );
			if($("#divmonitor_"+clave).attr("programa")  != '' ||  $("#divmonitor_"+clave).attr("programa")  != 'undefined' )
			{
				$("#divmonitor_"+clave).attr("programa" , ""+programa+"");
				$("#divmonitor_"+clave).attr("numero" , ""+clave+"");
			}
			$('#divmonitor_'+clave).html('');
			$('#divmonitor_'+clave).append("<img id='boton_"+clave+"'  src='../../images/medical/hce/hceB.png' valor='Pantalla completa' class='btn_lupa' onclick='agrandar(\""+clave+"\", \""+programa+"\")'>");

			$('#divmonitor_'+clave).append("<div id='accordion"+clave+"' class='acordion'><h3 id='divtitulo_"+clave+"' ></h3><div  id='contenido_"+clave+"'></div></div>");

			var cod_rol = $("#perfilMonitorAud").val();
			var nombre_rol = (typeof arr_roles_cx[cod_rol] !== 'undefined') ? arr_roles_cx[cod_rol].nombre: cod_rol;
			mensajeMonitor = "Monitor autorizaci&oacute;n/auditor&iacute;a cx &nbsp;&nbsp;&nbsp;<b>|</b>&nbsp;&nbsp;&nbsp;&nbsp;<span style='font-weight:normal;'><b>Perfil:</b> "+nombre_rol+"</span>";
			/*switch(cod_rol)
			{
				case 'FAC':
					mensajeMonitor = "Monitor autorizaci&oacute;n/auditor&iacute;a cx &nbsp;&nbsp;&nbsp;<b>|</b>&nbsp;&nbsp;&nbsp;&nbsp;<span style='font-weight:normal;'><b>Perfil:</b> Facturaci&oacute;n</span>";
					break;
				case 'AUD':
					mensajeMonitor = "Monitor autorizaci&oacute;n/auditor&iacute;a cx &nbsp;&nbsp;&nbsp;<b>|</b>&nbsp;&nbsp;&nbsp;&nbsp;<span style='font-weight:normal;'><b>Perfil:</b> Auditoria</span>";
					break;
				case 'AUT':
					mensajeMonitor = "Monitor autorizaci&oacute;n/auditor&iacute;a cx &nbsp;&nbsp;&nbsp;<b>|</b>&nbsp;&nbsp;&nbsp;&nbsp;<span style='font-weight:normal;'><b>Perfil:</b> Autorizaciones</span>";
					break;
				case 'CON':
					mensajeMonitor = "Monitor autorizaci&oacute;n/auditor&iacute;a cx &nbsp;&nbsp;&nbsp;<b>|</b>&nbsp;&nbsp;&nbsp;&nbsp;<span style='font-weight:normal;'><b>Perfil:</b> Consulta</span>";
					break;
				case 'CCX':
					mensajeMonitor = "Monitor autorizaci&oacute;n/auditor&iacute;a cx &nbsp;&nbsp;&nbsp;<b>|</b>&nbsp;&nbsp;&nbsp;&nbsp;<span style='font-weight:normal;'><b>Perfil:</b> Coodinaci&oacute;n de cirug&iacute;a</span>";
					break;
				case 'IDC':
					mensajeMonitor = "Monitor autorizaci&oacute;n/auditor&iacute;a cx &nbsp;&nbsp;&nbsp;<b>|</b>&nbsp;&nbsp;&nbsp;&nbsp;<span style='font-weight:normal;'><b>Perfil:</b> Auditor&iacute;a IDC</span>";
					break;
				case 'CNV':
					mensajeMonitor = "Monitor autorizaci&oacute;n/auditor&iacute;a cx &nbsp;&nbsp;&nbsp;<b>|</b>&nbsp;&nbsp;&nbsp;&nbsp;<span style='font-weight:normal;'><b>Perfil:</b> Convenios</span>";
					break;
			}*/

			$('#divtitulo_'+clave).append(mensajeMonitor);
			$('#contenido_'+clave).html(data);

			$( "#accordion"+clave+"" ).accordion({
				collapsible: true,
				heightStyle: "content"
			});

			// --> Activar tabs jaquery
			$( "#tabsMonitorAuditoria" ).tabs({
				heightStyle: "content"
			});
			reiniciarPlugins(programa);
		});

	}

	function trOver(grupo)
    {
        // $("#"+grupo.id).addClass('classOver');
        $(grupo).addClass('classOver');
    }

    function trOut(grupo)
    {
        // $("#"+grupo.id).removeClass('classOver');
        $(grupo).removeClass('classOver');
    }

	function trOverSp(grupo)
    {
        if($(grupo).hasClass("fila1")) {
        	$(grupo).addClass('fila1x');
        	$(grupo).removeClass('fila1');
        } else if($(grupo).hasClass("fila2")) {
        	$(grupo).addClass('fila2x');
        	$(grupo).removeClass('fila2');
        }
        $(grupo).addClass('classOverSp');
    }

    function trOutSp(grupo)
    {
        if($(grupo).hasClass("fila1x")) {
        	$(grupo).addClass('fila1');
        	$(grupo).removeClass('fila1x');
        } else if($(grupo).hasClass("fila2x")) {
        	$(grupo).addClass('fila2');
        	$(grupo).removeClass('filax');
        }
        $(grupo).removeClass('classOverSp');
    }

	function trOverDel(grupo) { $(grupo).addClass('classOverDel'); }

    function trOutDel(grupo) { $(grupo).removeClass('classOverDel'); }

    function cerrarVentanaPpal()
    {
        window.close();
    }

    /*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> SIMULAR BLINK >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
    /**
     * Se cambio a esta forma porque la anterior lo que hacía era ir copando la memoria hasta que el programa
     * terminaba bloqueandose por dejar tantos objetos setInterval en memoria sin destruir.
     */
    var intervalSet;
    var intervalSetArr = new Array();
    function blinkJqueryElementClass(class_css)
    {
    	if((jQuery.inArray(class_css, intervalSetArr)) != -1) { intervalSetArr[ class_css ] = undefined; }
    	clearInterval(intervalSetArr[ class_css ]);

		initBlnk(class_css);
    }

    function initBlnk(class_css)
	{
	    intervalSetArr[ class_css ] = setInterval(function() { draw(class_css); }, 700);
	}

	function draw(class_css)
	{
		// console.log(class_css);
	    if ($("."+class_css+":visible").length == 0) {
            $("."+class_css).show();
        } else {
            $("."+class_css).hide();
        }
	    clearInterval(intervalSetArr[ class_css ]);
	    initBlnk(class_css);
	}
	/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/



	//-----------------------------------------------------------------------
	//	--> Guardar procedimiento auditado
	//		Jerson Trujillo, 2015-12-17
	//-----------------------------------------------------------------------
	function guardarAuditoria(idTurno, destino, autorizadaSegundoRes)
	{
		infoConvenios 				= new Object();
		infoAuditoria 				= new Object();
		infoAuditoriaDesdeDesOpe	= new Object();
		infoAuditoriaNuevos 		= new Object();
		numAutoInsumos 				= new Object();
		idx							= 0;
		permitirGuardar 			= true;
		hayProcedimientos 			= false;
		perfilMonitorAud 			= $("#perfilMonitorAud").val();
		rolQueIngresaNumAutorizacionCx = JSON.parse($("#rolQueIngresaNumAutorizacionCx").val());

		var arrayCausas = JSON.parse($("#arrayCausas").val());
		var valTramite = $("#select_tramites").val();
		if(valTramite == undefined)
			valTramite = "";

		var tram_permite_liquidar = false;//tramite requiere autorizar.
		var tram_requiere_autorizar = false;//tramite requiere autorizar.
		var nombre_tramite = "";
		if(((destino == 'FAC' || destino == 'LIQ') && typeof arrayCausas[valTramite] !== 'undefined') ){
			if(arrayCausas[valTramite]["permite_liquidar"] == 'on'){
				tram_permite_liquidar = true;
			}

			if(arrayCausas[valTramite]["requiere_autorizar"] == 'on'){
				tram_requiere_autorizar = true;
			}

			nombre_tramite = arrayCausas[valTramite]["nombre"];
		}

		if(valTramite == '' && (destino == 'FAC' || destino == 'LIQ')){
			tram_permite_liquidar = true;
			tram_requiere_autorizar = true;
		}
		// console.log("tram_permite_liquidar: ",tram_permite_liquidar);
		// console.log("tram_requiere_autorizar: ",tram_requiere_autorizar);
		// console.log("nombre_tramite: ",nombre_tramite);

		$("[class=listaProPendiente]").removeClass("listaProPendiente");

		var validar_requeridos = true;
		var origen_destino     = perfilMonitorAud+'_'+destino;

		// Si se cumple este origen_destino, no validar campos obligatorios.
		switch(origen_destino)
		{
			case 'AUD_CCX':
			case 'AUD_IDC':
			case 'CCX_AUD':
			case 'AUT_CNV':
				validar_requeridos = false;
			case 'CNV_FAC':
			case 'CNV_AUT':
				validar_requeridos = false;
				// --> Validacion de los procedimientos, rechazados por convenios.

				// Solo el perfil convenios puede modificar el visto bueno de cada procedimiento.
				if(perfilMonitorAud == 'CNV')
				{
					$("#tablaProcedCups tr[procedimiento]").each(function(){
						var VoBo = '';
						// --> Procedimientos que vienen desde la auditoría
						idReg                           = $(this).find("input[negar_vobo]").attr("name");
						infoConvenios[idReg]            = new Object();
						infoConvenios[idReg].negar_vobo = ($(this).find("input[negar_vobo]").is(':checked')) ? 'off': 'on';
					});
				}
			break;
		}

		if(validar_requeridos)
		{
			// --> 	Si hay codigos que no existen en los maestros y la accion es LIQ (Lista para liquidar)
			mostrarMensaje = false;
			$("td[faltaHomolog=si]").each(function (){
				if($(this).parent().find("input[confirmar]").is(':checked') && destino == 'LIQ')
				{
					$(this).attr("class", "listaProPendiente");
					mostrarMensaje = true;
				}
			});

			if(mostrarMensaje)
			{
				jAlert("<span style='color:red'>La cx aun no se puede enviar a liquidar, porque hay c&oacute;digos inconsistentes.<br>Por favor revisarlos.</span>", "Mensaje");
				return;
			}

			// --> Validar numeros de autorizacion para los insumos
			tipoempresasoat 				= $("#tipoempresasoat").val();
			tipoEmp 						= $("#variablesParaValidarAutorizacion").attr("tipEnt");
			mostrarMsjInsumo 				= false;

			// --> Validar num de autorizacion si el tipo de empresa es diferente a soat
			if(tipoEmp != tipoempresasoat)
			{
				if(jQuery.inArray(perfilMonitorAud, rolQueIngresaNumAutorizacionCx) >= 0 || destino == 'LIQ')
				{
					$("input[idMercadoInsumo]").each(function(){
						numAutoInsumos[$(this).attr("idMercadoInsumo")] = $(this).val();
						if($(this).val() == '')
						{
							mostrarMsjInsumo = true;
							$(this).parent().attr("class", "listaProPendiente");
						}
					});

					// Si hay número de autorización vacíos
					// El tramite permite liquidar
					// El tramite no obliga a autorizar
					if(mostrarMsjInsumo && tram_permite_liquidar && !tram_requiere_autorizar ){
						mostrarMsjInsumo = false;
					}

					if(mostrarMsjInsumo)
					{
						jAlert("<span style='color:red'>Faltan insumos por autorizaci&oacute;n.<br>Por favor revisarlos.</span>", "Mensaje");
						return;
					}
				}
			}

			// --> Validar que hayan seleccionado la via y el organo
			rolQueIngresaViasDeLaCx = JSON.parse($("#rolQueIngresaViasDeLaCx").val());
			mostrarMsjVia 			= false;
			mostrarMsjOrg 			= false;
			mostrarMsjBil 			= false;

			if(jQuery.inArray(perfilMonitorAud, rolQueIngresaViasDeLaCx) >= 0 || destino == 'LIQ')
			{
				$("select[selectVia]").each(function(){
					if($(this).val() == '' && $(this).parent().parent().find("input[confirmar]").is(':checked'))
					{
						mostrarMsjVia = true;
						$(this).parent().attr("class", "listaProPendiente");
					}
				});

				if(mostrarMsjVia)
				{
					jAlert("<span style='color:red'>Faltan procedimientos por definirle la v&iacute;a.<br>Por favor revisarlos.</span>", "Mensaje");
					return;
				}

				$("select[selectOrg]").each(function(){
					if($(this).val() == '' && $(this).parent().parent().find("input[confirmar]").is(':checked'))
					{
						mostrarMsjOrg = true;
						$(this).parent().attr("class", "listaProPendiente");
					}
				});

				if(mostrarMsjOrg)
				{
					jAlert("<span style='color:red'>Faltan procedimientos por definirle el organo.<br>Por favor revisarlos.</span>", "Mensaje");
					return;
				}
				
				$("select[checkBilateral]").each(function(){
					if($(this).val() == '' && $(this).parent().parent().find("input[confirmar]").is(':checked'))
					{
						mostrarMsjBil = true;
						$(this).parent().attr("class", "listaProPendiente");
					}
				});

				if(mostrarMsjBil)
				{
					jAlert("<span style='color:red'>Faltan procedimientos por definirle la bilateralidad.<br>Por favor revisarlos.</span>", "Mensaje");
					return;
				}
			}

			// --> Numeros de autorizacion para los procedimientos
			mostrarMsjAutoPro = false;
			// --> Validar num de autorizacion si el tipo de empresa es diferente a soat
			if(tipoEmp != tipoempresasoat)
			{
				if(jQuery.inArray(perfilMonitorAud, rolQueIngresaNumAutorizacionCx) >= 0 || destino == 'LIQ')
				{
					$("input[numAutorizacion]:visible").each(function(){

						if($(this).val() == '' && $(this).parent().parent().find("input[confirmar]").is(':checked'))
						{
							mostrarMsjAutoPro = true;
							$(this).parent().attr("class", "listaProPendiente");
						}
					});

					// Si hay número de autorización vacíos
					// El tramite permite liquidar
					// El tramite no obliga a autorizar
					if(mostrarMsjAutoPro && tram_permite_liquidar && !tram_requiere_autorizar ){
						mostrarMsjAutoPro = false;
					}

					if(mostrarMsjAutoPro)
					{
						jAlert("<span style='color:red'>Faltan procedimientos por autorizaci&oacute;n.<br>Por favor revisarlos.</span>", "Mensaje");
						return;
					}
				}
			}

			if((destino == 'FAC' || destino == 'LIQ') && !tram_permite_liquidar){
				jAlert("El tr&aacute;mite ["+nombre_tramite+"] no permite pasar a liquidar.","Alerta");
				return;
			} else if(destino != 'FAC' && destino != 'LIQ'){
				valTramite = '';
			}

			// --> Validacion de los procedimientos.
			$("#tablaProcedCups tr[procedimiento]").each(function(){
				confirmar = '';
				analizado = '';

				// --> Procedimientos que vienen desde la descripcion operatoria
				if($(this).attr("nuevoAgregado") == undefined)
				{
					if($(this).find("input[confirmar]").is(':checked'))
						confirmar = 'on';
					else
					{
						if($(this).find("input[rechazar]").is(':checked'))
							confirmar = 'off';
						else
						{
							var rolQueRechazaAutorizaProcedimientosCx = JSON.parse($("#rolQueRechazaAutorizaProcedimientosCx").val());

							// --> Solo valido campos obligatorios para el momento en que se de click en liquidar
							if(destino == 'LIQ' || jQuery.inArray(perfilMonitorAud, rolQueRechazaAutorizaProcedimientosCx) >= 0)
							{
								$(this).find("input[rechazar]").parent().attr("class", "listaProPendiente");
								$(this).find("input[confirmar]").parent().attr("class", "listaProPendiente");
								permitirGuardar = false;
							}
						}
					}

					idReg 				= $(this).find("input[confirmar]").attr("name");
					hayProcedimientos 	= true;

					// --> Procedimiento que aun no se ha guardado en la tabla de auditoria
					if($(this).attr("nuevoDesdeDesOp") != undefined)
					{
						infoAuditoriaDesdeDesOpe[$(this).attr("nuevoDesdeDesOp")] 				= new Object();
						infoAuditoriaDesdeDesOpe[$(this).attr("nuevoDesdeDesOp")].confirmado 	= confirmar;
						infoAuditoriaDesdeDesOpe[$(this).attr("nuevoDesdeDesOp")].numAuto 		= $(this).find("input[numAutorizacion]").val();
						infoAuditoriaDesdeDesOpe[$(this).attr("nuevoDesdeDesOp")].numAutoAdmi	= $(this).find("input[numAutorizacion]").attr("numAutoDesdeAdmision");
						infoAuditoriaDesdeDesOpe[$(this).attr("nuevoDesdeDesOp")].via			= $(this).find("select[selectVia]").val();
						infoAuditoriaDesdeDesOpe[$(this).attr("nuevoDesdeDesOp")].organo		= $(this).find("select[selectOrg]").val();
						infoAuditoriaDesdeDesOpe[$(this).attr("nuevoDesdeDesOp")].checkBilateral= $(this).find("[checkBilateral]").val();
						infoAuditoriaDesdeDesOpe[$(this).attr("nuevoDesdeDesOp")].medicoRealizo	= $(this).find("td[medicoRealizo]").attr("medicoRealizo");
					}
					else
					{
						infoAuditoria[idReg] 				= new Object();
						infoAuditoria[idReg].confirmado 	= confirmar;
						infoAuditoria[idReg].numAuto 		= $(this).find("input[numAutorizacion]").val();
						infoAuditoria[idReg].numAutoAdmi	= $(this).find("input[numAutorizacion]").attr("numAutoDesdeAdmision");
						infoAuditoria[idReg].via			= $(this).find("select[selectVia]").val();
						infoAuditoria[idReg].organo			= $(this).find("select[selectOrg]").val();
						infoAuditoria[idReg].checkBilateral = $(this).find("[checkBilateral]").val();
					}
				}
				// --> Procedimientos nuevos, agregados.
				else
				{
					if($(this).find("input[confirmar]").is(':checked') && $(this).find("input[proNuevo]").attr("valor") != '')
					{
						idx 									= idx+1;
						infoAuditoriaNuevos[idx] 				= new Object();
						infoAuditoriaNuevos[idx].procedimiento 	= $(this).find("input[proNuevo]").attr("valor");
						infoAuditoriaNuevos[idx].numAuto 		= $(this).find("input[numAutorizacion]").val();
						infoAuditoriaNuevos[idx].via 			= $(this).find("select[selectVia]").val();
						infoAuditoriaNuevos[idx].organo			= $(this).find("select[selectOrg]").val();
						infoAuditoriaNuevos[idx].checkBilateral	= $(this).find("[checkBilateral]").val();

						hayProcedimientos 						= true;
					}
					else
					{
						if($(this).find("input[proNuevo]").attr("valor") == '')
							$(this).find("input[proNuevo]").attr("class", "listaProPendiente");
						else
							$(this).find("input[confirmar]").parent().attr("class", "listaProPendiente");

						permitirGuardar = false;
					}
				}
			});
		}

		if(permitirGuardar)
		{
			// --> Validar que exista al menos un procedimiento
			if(!hayProcedimientos && destino == 'LIQ' && $("#notaOperatoria-"+idTurno).val() == undefined && autorizadaSegundoRes != 'on')
			{
				jAlert("<span style='color:red'>Debe existir al menos un procedimiento relacionado.</span>", "Mensaje");
				return;
			}

			// --> Solo valido numeros de autorizacion para el rol de autorizaciones y cuando se vaya a autorizar la cx.
			// if(pasarCxDondeAuditor == 'off' && perfilMonitorAud == 'off' && numerosAutorizacion == '')
			// {
				// jAlert("<span style='color:red'>Debe ingresar los n&uacute;meros de autorizaci&oacute;n.</span>", "Mensaje");
				// return;
			// }

			var nombre_rol = (typeof arr_roles_cx[destino] !== 'undefined') ? arr_roles_cx[destino].nombre: destino;
			// --> Definir mensaje de confirm, dependiendo el destino
			switch(destino)
			{
				case 'LIQ':
				{
					mensaje = '<span style="color:#4C99F5">La cirug&iacute;a quedar&aacute; lista para ser liquidada<br>Est&aacute; seguro?</span>';
					break;
				}
				default:
					mensaje = '<span style="color:#4C99F5">La cirug&iacute;a pasar&aacute; a '+nombre_rol.toLowerCase();+'<br>Est&aacute; seguro?</span>';
				break;
			}

			if(autorizadaSegundoRes == 'on')
				mensaje = '<span style="color:#4C99F5">La cirug&iacute;a quedar&aacute; autorizada para el segundo responsable<br>Est&aacute; seguro?</span>';

			jConfirm(mensaje, 'Confirmar', function(respuesta) {
				if(respuesta)
				{
					// console.log(infoAuditoria);
					// console.log(infoAuditoriaDesdeDesOpe);
					// console.log(infoAuditoriaNuevos);
					// return;
					// --> Guardar auditoria
					$.post("MonitorFacturacionERP.php",
					{
						consultaAjax:   			'',
						accion:         			'guardarAuditoria',
						wemp_pmla:         			$('#wemp_pmla').val(),
						idTurno:					idTurno,
						wbasedatoFacturacion:		$("#wbasedatoFacturacion").val(),
						arr_roles_cx:				JSON.stringify(arr_roles_cx),
						infoAuditoria:				JSON.stringify(infoAuditoria),
						infoConvenios:				JSON.stringify(infoConvenios),
						infoAuditoriaDesdeDesOpe:	JSON.stringify(infoAuditoriaDesdeDesOpe),
						infoAuditoriaNuevos:		JSON.stringify(infoAuditoriaNuevos),
						numAutoInsumos:				JSON.stringify(numAutoInsumos),
						perfilMonitorAud:			perfilMonitorAud,
						destino:					destino,
						numHIsIng:         			$('#numHIsIng').text(),
						autorizadaSegundoRes:		autorizadaSegundoRes,
						valTramite : 				valTramite

					}, function(respuesta){
						pintarProcedimientosSinAuditar();
						$("#divAutorizarAuditar").dialog( "close" );
					});
				}
			});
		}
		else
		{
			jAlert("<span style='color:red'>Faltan procedimientos por revisar.</span>", "Mensaje");
		}
	}
	//-----------------------------------------------------------------------
	//	--> Omite la regrabacion de una historia, para que salga del monitor de pendientes de regrabacion
	//		Jerson Trujillo, 2017-05-06
	//-----------------------------------------------------------------------
	function omitirRegrabacion(historia, ingreso, elemento)
	{
		jConfirm('Est&aacute; seguro en omitir esta regrabaci&oacute;n?', 'Confirmar', function(respuesta){
			if(respuesta){
				$.post("MonitorFacturacionERP.php",
				{
					consultaAjax:   		'',
					accion:         		'omitirRegrabacion',
					wemp_pmla:         		$('#wemp_pmla').val(),
					historia:				historia,
					ingreso:				ingreso
				}, function(respuesta){
					$(elemento).parent().parent().remove();
				});
			}
		});
	}
	//-----------------------------------------------------------------------
	//	--> enviarObservacion
	//		Jerson Trujillo, 2016-01-19
	//-----------------------------------------------------------------------
	function enviarObservacion(idTurno)
	{
		// --> Observaciones
		var arrObservaciones 					= new Object();
		arrObservaciones						= JSON.parse($("#hisObserv").val());
		if($("#inputNuevaObserv").val() != '')
		{
			var nuevoIndex							= arrObservaciones.length;
			arrObservaciones[nuevoIndex] 			= new Object();
			arrObservaciones[nuevoIndex].usuario 	= $("#inputNuevaObserv").attr("usuario");
			arrObservaciones[nuevoIndex].fechaYhora	= $("#inputNuevaObserv").attr("fechaYhora");
			arrObservaciones[nuevoIndex].mensaje	= $("#inputNuevaObserv").val().split('"').join("'");
			//console.log(arrObservaciones[nuevoIndex].mensaje);
			arrObservaciones[nuevoIndex].idBitacora	= 'NUEVO';

			$.post("MonitorFacturacionERP.php",
			{
				consultaAjax:   		'',
				accion:         		'enviarObservacion',
				wemp_pmla:         		$('#wemp_pmla').val(),
				idTurno:				idTurno,
				wbasedatoFacturacion:	$("#wbasedatoFacturacion").val(),
				perfilMonitorAud: 		$("#perfilMonitorAud").val(),
				arrObservaciones:		JSON.stringify(arrObservaciones),
				numHIsIng:         		$('#numHIsIng').text(),
				arr_roles_cx : 			JSON.stringify(arr_roles_cx)
			}, function(arrObservaciones){
				color 		= "#7588A3";
				mensajeHis 	= "";
				$(arrObservaciones).each(function(index, valor){
					color = ((color == "#2a5db0") ? "#7588A3" : "#2a5db0");
					mensajeHis+= "<span style='color:"+color+"'>&nbsp;"+valor.usuario+" ("+valor.fechaYhora+"): </span>"+valor.mensaje+"<br>";
				});

				$("#inputNuevaObserv").val("");
				$("#mensajeHis").html(mensajeHis);
				$("#hisObserv").val(JSON.stringify(arrObservaciones));
			}, 'json');
		}
	}
	//-----------------------------------------------------------------------
	//	--> Copiar numeros de autorizacion
	//		Jerson Trujillo, 2016-01-19
	//-----------------------------------------------------------------------
	function copiarNumAuto(consecutivo, preFijo)
	{
		valorAnt = $("["+preFijo+"="+(consecutivo-1)+"]").val();
		if(valorAnt != undefined)
			$("["+preFijo+"="+consecutivo+"]").val(valorAnt);
	}

	//-----------------------------------------------------------------------
	//	--> Remover fila tr de procedimiento
	//		Jerson Trujillo, 2015-12-17
	//-----------------------------------------------------------------------
	function removerProcedimiento(Elemento)
	{
		$(Elemento).parent().parent().remove();
		$("select[selectVia]").each(function(){
			$(this).find("option").last().remove();
		});
	}
	//-----------------------------------------------------------------------
	//	--> Pintar fila para agregar un procedimiento
	//		Jerson Trujillo, 2015-12-17
	//-----------------------------------------------------------------------
	function agregarProcedimiento()
	{
		consecutivo = $("#tablaProcedCups tr[nuevoAgregado=si]").length+1;
		classTr		= 'fila2';
		consec 		= $("[numAutorizacion]").length+1;
		cantVias	= $("#tablaProcedCups tr[procedimiento]").length;

		html = ""+
		"<tr class='"+classTr+"' nuevoAgregado='si' procedimiento='' style='font-size: 8pt;padding:1px;'>"+
			"<td class='listaPro' align='center' id='codigo-"+consecutivo+"'></td>"+
			"<td class='listaPro' align='center'>"+
				"<input type='text' proNuevo='' id='procedimiento-"+consecutivo+"' valor='' style='width:300px;font-size:8pt;'></td>"+
			"<td class='listaPro' align='center'>"+
				"<input type='radio' style='cursor:pointer' confirmar=''></td>"+
			"<td class='listaPro' align='center'>"+
				"<img src='../../images/medical/eliminar1.png' style='cursor:pointer' title='Quitar nuevo procedimiento.' onclick='removerProcedimiento(this);'"+
			"</td><td>&nbsp;</td>"+
			"<td align='center'>"+
				"<input type='text' numAutorizacion='"+consec+"' title='Doble click para traer valor anterior' placeholder='Digite o Doble click' style='display:none;border-radius: 4px;border:1px solid #AFAFAF;width:140px' ondblclick='copiarNumAuto(\""+consec+"\", \"numAutorizacion\")'>"+
			"</td>"+
			"<td><select selectVia=''>"+
					"<option></option>";
		for(x=1;x<=cantVias;x++)
			html+=	"<option>"+x+"</option>";

		html+=	"</select>"+
			"</td>"+
			"<td align='center'><select checkBilateral=''><option></option><option value='NA'>No aplica</option><option value='off'>Unilateral</option><option value='on'>Bilateral</option></select></td>"+
			"<td align='center'>"+
				"<select selectOrg='"+consecutivo+"' onChange='cargarTooltSelect(this)' style=''></select>"+
			"</td>"+
		"</tr>";

		$("#tablaProcedCups tr").last().before(html);

		// --> Aumentar en 1 la cantidad de vias, para todos los select de vias
		$("select[selectVia]").append("<option>"+(cantVias+1)+"</option>");

		// --> Autocomplete para agregar procedimiento.
		crear_autocomplete('arrayProcedParaAuditoria', 'procedimiento-'+consecutivo, consecutivo, consec);

		$("#fieldProCups").css({"height":($("#fieldProCups").height()+20)+"px","overflow":"auto","background":"none repeat scroll 0 0"});

		if($("#fieldProCups").height() > $("#fieldMedAuto").height())
			$("#fieldMedAuto").height($("#fieldProCups").height());
		else
			$("#fieldProCups").height($("#fieldMedAuto").height());

		if($("#fieldMovimientos").height() > $("#fieldObservaciones").height())
			$("#fieldObservaciones").height($("#fieldMovimientos").height());
		else
			$("#fieldMovimientos").height($("#fieldObservaciones").height());

		// --> Validar permiso para rechazar/autorizar procedimientos
		// perfilMonitorAud = $("#perfilMonitorAud").val();
		// var rolQueRechazaAutorizaProcedimientosCx = JSON.parse($("#rolQueRechazaAutorizaProcedimientosCx").val());
		// if(jQuery.inArray(perfilMonitorAud, rolQueRechazaAutorizaProcedimientosCx) < 0)
		// {
			// $("input[confirmar]").attr("disabled", "disabled");
			// $("input[rechazar]").attr("disabled", "disabled");
		// }
	}
	//-----------------------------------------------------------------------
	//	--> Ajustar alto del acordeon
	//-----------------------------------------------------------------------
	function ajustarTamanoAcordeon()
	{
		// --> Ajustar tamaño del acordeon
		claveMonitor 	= $("#claveMonitor").val();
		nuevoAlto 		= $("#tabsMonitorAuditoria").height();
		$("#contenido_"+claveMonitor).height(nuevoAlto+15);
	}
	//-----------------------------------------------------------------------
	//	--> Mostrar tabs de procedimientos sin auditar
	//		Jerson Trujillo, 2015-12-17
	//-----------------------------------------------------------------------
	function pintarProcedimientosSinAuditar()
	{
		$("#divProSinAuditar").hide();
		$.post("MonitorFacturacionERP.php",
		{
			consultaAjax          : '',
			accion                : 'pintarProcedimientosSinAuditar',
			wemp_pmla             : $('#wemp_pmla').val(),
			perfilMonitorAud      : $("#perfilMonitorAud").val(),
			tipoPaciente          : $("#tipoPaciente").val(),
			filtroEmpresasRevisar : $("#filtroEmpresasRevisar").val(),
			selectEstado          : ($("#selectEstado").val() != undefined) ? $("#selectEstado").val() : '%',
			arr_roles_cx          : JSON.stringify(arr_roles_cx)
		}, function(data){
			$("#divProSinAuditar").html(data.html).show(400, function(){
				ajustarTamanoAcordeon();
				// --> 	Reloj temporizador
				activarRelojTemporizador();
				// --> Activar el buscador de texto
				$('#buscarProSinAudi').quicksearch('#tablaProSinAudi .find');
				// --> Activar tooltip
				$("[tooltip=si]").tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });

				// --> Blink a algunos elementos
				clearInterval(blinkReautorizar);
				blinkReautorizar = setInterval(function(){
					$("span[blink]").css('visibility' , $("span[blink]").css('visibility') === 'hidden' ? '' : 'hidden');
				}, 400);

			});
			$("#numRegis").text(data.numRegis);
		}, 'json');
	}


	function pintarMedicamentosUnixvsMatrix()
	{
		//alert( $("#centrocostosmedicamentos").val());
		$.post("MonitorFacturacionERP.php",
		{
			consultaAjax          : '',
			accion                : 'pintarMedicamentosUnixvsMatrix',
			wemp_pmla             : $('#wemp_pmla').val(),
			tipoPaciente          : $("#tipoPacientemedicamentos").val(),
			cco					  : $("#centrocostosmedicamentos").val()

		}, function(data){
			//alert(data);
			$("#medicamentosdistintos").html(data);
		});
	}


	function pintarCargosxCambioResponsable()
	{
		//alert($("#tipoPacienteregrabacion").val());
		$.post("MonitorFacturacionERP.php",
		{
			consultaAjax          : '',
			accion                : 'pintarCargosxCambioResponsable',
			wemp_pmla             : $('#wemp_pmla').val(),
			tipoPaciente          : $("#tipoPacienteregrabacion").val()

		}, function(data){
			//alert(data);
			$("#divcambioresponsable").html(data);
		});
	}


	//-----------------------------------------------------------------------
	// --> 	Reloj temporizador
	//		Jerson Trujillo, 2016-01-13
	//-----------------------------------------------------------------------
	function activarRelojTemporizador()
	{
		clearInterval(relojTemp);
		$("#relojTemp").text("00:00");
		$("#relojTemp").attr("cincoMinTem", "86400000");
		relojTemp = setInterval(function(){
			var cincoMin 	= new Date(parseInt($("#relojTemp").attr("cincoMinTem")));
			var cincoMinTem	= cincoMin.getTime();
			cincoMinTem += 1000;
			cincoMin.setTime(cincoMinTem);
			minuto	 	= ((String(cincoMin.getMinutes()).length == 1) ? "0"+cincoMin.getMinutes() : cincoMin.getMinutes());
			segundo 	= ((String(cincoMin.getSeconds()).length == 1) ? "0"+cincoMin.getSeconds() : cincoMin.getSeconds());

			var nuevoCincoMin = minuto+":"+segundo;
			$("#relojTemp").text(nuevoCincoMin);
			$("#relojTemp").attr("cincoMinTem", cincoMinTem);

			// --> Actualizar cuando el reloj quede en 00:00
			// if(parseInt($("#relojTemp").attr("cincoMinTem")) == 86400000)
			// {
				// clearInterval(relojTemp);
				// pintarProcedimientosSinAuditar();
			// }
		}, 1000);

		var tab = $("#tabsMonitorAuditoria").find("li[class*=ui-state-active]").attr("id");
		$("#tdBotonActualizar").html("");
		if(tab == "liProSinAuditar"){
			$("#tdBotonActualizar").html('&nbsp;&nbsp;<b>|</b>&nbsp;&nbsp;<span style="cursor:pointer" onclick="pintarProcedimientosSinAuditar()">Actualizar&nbsp;<img width="14px" height="14px" src="../../images/medical/sgc/Refresh-128.png" title="Actualizar listado."></span>');
		}
		else if(tab == 'liProAuditados'){
			$("#tdBotonActualizar").html('&nbsp;&nbsp;<b>|</b>&nbsp;&nbsp;<span style="cursor:pointer" onclick="pintarProcedimientosAuditados()">Actualizar&nbsp;<img width="14px" height="14px" src="../../images/medical/sgc/Refresh-128.png" title="Actualizar listado."></span>');
		} else {
			$("#tdBotonActualizar").html('&nbsp;&nbsp;<b>|</b>&nbsp;&nbsp;<span style="cursor:pointer" onclick="actualizarTabActivo(\''+tab+'\')" id="spn_actualizar_listados">Actualizar&nbsp;<img width="14px" height="14px" src="../../images/medical/sgc/Refresh-128.png" title="Actualizar listado."></span>');
		}
		// console.log(tab);
	}

	function actualizarTabActivo(id_tab_activo){
		$("#"+id_tab_activo).find("a").trigger("click");
	}

	function activarRelojTemporizador2()
	{
		clearInterval(relojTemp2);
		$("#relojTemp2").text("00:00");
		$("#relojTemp2").attr("cincoMinTem", "86400000");
		relojTemp2 = setInterval(function(){
			var cincoMin 	= new Date(parseInt($("#relojTemp2").attr("cincoMinTem")));
			var cincoMinTem	= cincoMin.getTime();
			cincoMinTem += 1000;
			cincoMin.setTime(cincoMinTem);
			minuto	 	= ((String(cincoMin.getMinutes()).length == 1) ? "0"+cincoMin.getMinutes() : cincoMin.getMinutes());
			segundo 	= ((String(cincoMin.getSeconds()).length == 1) ? "0"+cincoMin.getSeconds() : cincoMin.getSeconds());

			var nuevoCincoMin = minuto+":"+segundo;
			$("#relojTemp2").text(nuevoCincoMin);
			$("#relojTemp2").attr("cincoMinTem", cincoMinTem);

			// --> Actualizar cuando el reloj quede en 00:00
			// if(parseInt($("#relojTemp").attr("cincoMinTem")) == 86400000)
			// {
				// clearInterval(relojTemp);
				// pintarProcedimientosSinAuditar();
			// }
		}, 1000);

		// var tab = $("#tabsMonitorAuditoria").find("li[class*=ui-state-active]").attr("id");
		// $("#tdBotonActualizar").html("");
		// if(tab == "liProSinAuditar")
			// $("#tdBotonActualizar").html('&nbsp;&nbsp;<b>|</b>&nbsp;&nbsp;<span style="cursor:pointer" onclick="pintarProcedimientosSinAuditar()">Actualizar&nbsp;<img width="14px" height="14px" src="../../images/medical/sgc/Refresh-128.png" title="Actualizar listado."></span>');
		// else
			// $("#tdBotonActualizar").html('&nbsp;&nbsp;<b>|</b>&nbsp;&nbsp;<span style="cursor:pointer" onclick="pintarProcedimientosAuditados()">Actualizar&nbsp;<img width="14px" height="14px" src="../../images/medical/sgc/Refresh-128.png" title="Actualizar listado."></span>');
	}
	//-----------------------------------------------------------------------
	//	--> Mostrar tabs de procedimientos ya auditados
	//		Jerson Trujillo, 2015-12-17
	//-----------------------------------------------------------------------
	function pintarProcedimientosAuditados()
	{
		$("#divProAuditados").hide();
		$.post("MonitorFacturacionERP.php",
		{
			consultaAjax:   		'',
			accion:         		'pintarProcedimientosAuditados',
			wemp_pmla:         		$('#wemp_pmla').val(),
			fechaAutorizacion:		$("#fechaProAudi").val(),
			numHis:					$("#buscarHis").val(),
			verTodas:				(($("#checkVerTodas").is(':checked')) ? 'on' : 'off'),
			perfilMonitorAud:		$("#perfilMonitorAud").val(),
			filtroEmpresasRevisar:	$("#filtroEmpresasRevisar").val(),
			arr_roles_cx: 			JSON.stringify(arr_roles_cx)
		}, function(data){
			$("#divProAuditados").html(data.html).show(400, function(){
				ajustarTamanoAcordeon();
				// --> Activar el buscador de texto
				$('#buscarProAudi').quicksearch('#tablaProAudi .find');
				// --> 	Reloj temporizador
				activarRelojTemporizador();
				// --> Activar datapicker
				$("#fechaProAudi").datepicker({
					showOn: "button",
					buttonImage: "../../images/medical/root/calendar.gif",
					buttonImageOnly: true,
					onSelect: function(){
						pintarProcedimientosAuditados();
					}
				});
				$("#fechaProAudi").next().css({"cursor": "pointer"}).attr("title", "Seleccione");
				$("#fechaProAudi").after("");
				// --> Activar tooltip
				$("[tooltip=si]").tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
			});
			$("#numRegis2").text(data.numRegis);
		}, 'json');
	}
	/**
	 * [pintarTurnosLiquidadosTramite: Mostrar tabs de turnos ya liquidados pero que están en trámite o el trámite está solucionado pero falta revisar por parte del facturador.]
	 * @author [Edwar Jaramillo, 2015-12-17]
	 * @param  {[type]} tipo_consulta [Parámetro para identificar si se debe mostrar lo liquidado y en trámite o lo liquidado y solucionado pendiente de revisar]
	 * @return {[type]}               [description]
	 */
	function pintarTurnosLiquidadosTramite(sufijo, div_respuesta, tipo_consulta)
	{
		$("#"+div_respuesta).hide();
		$.post("MonitorFacturacionERP.php",
		{
          	// fechaAutorizacion     : $("#fechaProAudi").val(),
          	// numHis                : $("#buscarHis").val(),
          	// verTodas              : (($("#checkVerTodas").is(':checked')) ? 'on' : 'off'),
			consultaAjax          : '',
			accion                : 'pintarTurnosLiquidadosTramite',
			wemp_pmla             : $('#wemp_pmla').val(),
			perfilMonitorAud      : $("#perfilMonitorAud").val(),
			filtroEmpresasRevisar : $("#filtroEmpresasRevisar").val(),
			arr_roles_cx          : JSON.stringify(arr_roles_cx),
			tipo_consulta         : tipo_consulta,
			sufijo                : sufijo
		}, function(data){
			$("#"+div_respuesta).html(data.html).show(400, function(){
				ajustarTamanoAcordeon();
				// --> Activar el buscador de texto
				$('#buscar'+sufijo).quicksearch('#tabla'+sufijo+' .find');
				// --> 	Reloj temporizador
				activarRelojTemporizador();
				// --> Activar datapicker
				// $("#fechaProAudi").datepicker({
				// 	showOn: "button",
				// 	buttonImage: "../../images/medical/root/calendar.gif",
				// 	buttonImageOnly: true,
				// 	onSelect: function(){
				// 		pintarTurnosLiquidadosTramite(div_respuesta, tipo_consulta);
				// 	}
				// });
				// $("#fechaProAudi").next().css({"cursor": "pointer"}).attr("title", "Seleccione");
				// $("#fechaProAudi").after("");
				// --> Activar tooltip
				$("[tooltip=si]").tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
			});
			$("#numRegis"+sufijo).text(data.numRegis);
		}, 'json');
	}
	//-----------------------------------------------------------------------------
	//	--> Abre ventana dialog para relizar la autorizacion o auditoria de la cx
	//		Jerson Trujillo, 2015-12-17
	//-----------------------------------------------------------------------------
	function autorizarAuditarCx(turno, modoConsulta, autoriSegundoResp)
	{
		$.blockUI({
			message: "<div style='background-color: #111111;color:#ffffff;font-size: 15pt;'><img width='19' heigth='19' src='../../images/medical/ajax-loader3.gif'>&nbsp;&nbsp;Consultando...</div>",
			css:{"border": "2pt solid #7F7F7F"}
		});

		var perfilMonitorAudRef = $("#perfilMonitorAud").val(); // Cuando modoConsulta==on la variable perfilMonitorAud cambia a 'CON'
																// Es por eso que se crea esta variable perfilMonitorAudRef como referencia al
																// perfil original.
		var perfilMonitorAud = $("#perfilMonitorAud").val();
		var rolQueIngresaNumAutorizacionCx 	= JSON.parse($("#rolQueIngresaNumAutorizacionCx").val());

		$.post("MonitorFacturacionERP.php",
		{
			consultaAjax        : '',
			accion              : 'autorizarAuditarCx',
			wemp_pmla           : $('#wemp_pmla').val(),
			turno               : turno,
			perfilMonitorAud    : perfilMonitorAud,
			perfilMonitorAudRef : perfilMonitorAudRef,
			modoConsulta        : modoConsulta,
			arr_roles_cx        : JSON.stringify(arr_roles_cx)
		}, function(data){

			$.unblockUI();

			$("#divAutorizarAuditar").dialog( "destroy" );
			perfilMonitorAud = (modoConsulta == 'on') ? "CON" : perfilMonitorAud;

			// --> Ventana dialog para pedir autorizacion para el segundo responsable
			if(autoriSegundoResp == 'on' && perfilMonitorAud != 'CON')
			{
				$("#divAutorizarAuditar").html(data.html).dialog({
					show:{
						effect: "blind",
						duration: 100
					},
					hide:{
						effect: "blind",
						duration: 100
					},
					width:  '98%',
					dialogClass: 'fixed-dialog',
					modal: true,
					title: "Perfil: Facturador",
					close: function( event, ui ) {},
					buttons:{
						'Autorizada, Segundo Responsable': function(){
							guardarAuditoria(turno, 'LIQ', 'on');
						}
					}
				});
				
				//$("input[confirmar]").attr("checked", "checked");
			}
			else
			{
				var nombre_rol            = (typeof arr_roles_cx[perfilMonitorAud] !== 'undefined') ? arr_roles_cx[perfilMonitorAud].nombre: perfilMonitorAud;
				var btns_destino          = (typeof arr_roles_cx[perfilMonitorAud] !== 'undefined') ? arr_roles_cx[perfilMonitorAud].botones_roles: '';
				var add_procedimiento     = (typeof arr_roles_cx[perfilMonitorAud] !== 'undefined') ? arr_roles_cx[perfilMonitorAud].add_procedimiento: 'off';
				var btn_enTramite         = (typeof arr_roles_cx[perfilMonitorAud] !== 'undefined') ? arr_roles_cx[perfilMonitorAud].btn_enTramite: 'off';
				var ver_liquidado_tramite = (typeof arr_roles_cx[perfilMonitorAudRef] !== 'undefined') ? arr_roles_cx[perfilMonitorAudRef].ver_liquidado_tramite: 'off';
				var arr_btns_destino  = new Array();
				var botones_perfiles  = new Object();
				var requiere_autorizar_liquidado = 'off';
				var arr_btns_destino      = btns_destino.split(",");
				var rolQueRechazaAutorizaProcedimientosCx = JSON.parse($("#rolQueRechazaAutorizaProcedimientosCx").val());
				var rol_puede_autorizar = false;

				// console.log(perfilMonitorAudRef,rolQueIngresaNumAutorizacionCx);
				if(jQuery.inArray(perfilMonitorAudRef, rolQueIngresaNumAutorizacionCx) >= 0)
				{
					rol_puede_autorizar = true;
				}

				if(rol_puede_autorizar && ver_liquidado_tramite == 'on' && data.turno_liquidado == 'on' && data.tramActual != '' && data.tramActual != '10')
				{
					requiere_autorizar_liquidado = 'on';
				} else if (!rol_puede_autorizar && ver_liquidado_tramite == 'on' && data.turno_liquidado == 'on' && data.tramActual != '' && data.tramActual != '10'){
					btn_enTramite = 'off';
				}

				// console.log(arr_btns_destino);
				// Creación dinámica de botones para pasar a otros roles, como mínimo cada rol tendrá activo el botón "Cerrar"
				$(arr_btns_destino).each(function(){
					var cod_perf = this.toString();
					// console.log(cod);
					if(typeof arr_roles_cx[cod_perf] !== 'undefined')
					{
						var nombreRolDestino = (typeof arr_roles_cx[cod_perf] !== 'undefined') ? arr_roles_cx[cod_perf].nombre: cod_perf;
						var color_rol = (typeof arr_roles_cx[cod_perf] !== 'undefined') ? arr_roles_cx[cod_perf].color: cod_perf;
						botones_perfiles[cod_perf] = {};
						var arr_Btn = 	{ 	text: 'Pasar a '+nombreRolDestino,
								          	click: function(){
													guardarAuditoria(turno, cod_perf, 'off');
												},
											style:"background:#"+color_rol
										};
						botones_perfiles[cod_perf] = arr_Btn;
					}
					else if(cod_perf == 'LIQ')
					{
						botones_perfiles[cod_perf] = {};
						var arr_Btn = 	{ 	text: 'Lista para liquidar',
								          	click: function(){
													guardarAuditoria(turno, cod_perf, 'off');
												}
										};
						botones_perfiles[cod_perf] = arr_Btn;
					}
				});

				if(requiere_autorizar_liquidado == 'on'){
					botones_perfiles["guardar_autorizacion"] = {};
					var arr_btnAut = 	{ 	text: 'Guardar Autorizaci\u00F3n',
								          	click: function(){
													guardarAutorizacionLiquidados(perfilMonitorAudRef, turno);
												}
										};
					botones_perfiles["guardar_autorizacion"] = arr_btnAut;
				}

				botones_perfiles["cerrar"] = {};
				var arr_Btn = 	{ 	text: 'Cerrar',
						          	click: function(){
											$("#divAutorizarAuditar").dialog('close');
										}
								};
				botones_perfiles["cerrar"] = arr_Btn;
				// console.log(botones_perfiles);

				switch(perfilMonitorAud)
				{
					// --> Ventana dialog para el modo consulta
					case 'CON':
					{
						$("#divAutorizarAuditar").html(data.html).dialog({
							show:{
								effect: "blind",
								duration: 100
							},
							hide:{
								effect: "blind",
								duration: 100
							},
							width:  '98%',
							dialogClass: 'fixed-dialog',
							modal: true,
							title: "Modo consulta",
							close: function( event, ui ) {},
							buttons: botones_perfiles
						});
						break;
					}
					default:
					{
						$("#divAutorizarAuditar").html(data.html).dialog({
							show:{
								effect: "blind",
								duration: 100
							},
							hide:{
								effect: "blind",
								duration: 100
							},
							width:  '98%',
							dialogClass: 'fixed-dialog',
							modal: true,
							title: "Perfil: "+nombre_rol,
							close: function( event, ui ) {},
							buttons:botones_perfiles
						});
						break;
					}
				}
			}

			// --> Checkbox para dejar en tramite la cx
			enTramite = $("#enTramite").val();
			// var rolQuePermitePonerCxEnTramite = JSON.parse($("#rolQuePermitePonerCxEnTramite").val());
			// if(jQuery.inArray(perfilMonitorAud, rolQuePermitePonerCxEnTramite) >= 0)
			// tram_revisado
			// turno_liquidado
			// tramActual
			var tram_inicial = "";
			var html_tramite = '';
			var permitirAutorizar = 'off';
			var arrayCausas = JSON.parse($("#arrayCausas").val());
			if(btn_enTramite == 'on')
			{
				html_tramite += "	<option></option>";
				$.each( arrayCausas, function(index, value){
					html_tramite+= "		<option value='"+index+"' "+((enTramite == index) ? "selected" : "")+">"+value['nombre']+"</option>";
				});
			} else if(requiere_autorizar_liquidado == 'on'){
				permitirAutorizar = 'on';
				for(var index in arrayCausas){
					var value = arrayCausas[index];
					if(data.tramActual == index || index == '10'){
						var selected = "";
						if(data.tramActual == index){
							tram_inicial = index;
							selected = 'selected';
						}
						html_tramite+= "<option value='"+index+"' "+selected+">"+value['nombre']+"</option>";
					}
				}
				$("#inputNuevaObserv").removeAttr("readonly");
				$("#btn_enviar_obs").removeAttr("disabled");
			}

			var select_activo = '';
			// Se quita esta validación para que a las facturadoras de cirugía si les permita manipular los estádos de trámite.
			// Aunque no debería poder modificar o seleccionar los trámites de autorización o pendiente por parametrización.
			// if(perfilMonitorAudRef == 'FAC'){
			// 	select_activo = 'disabled="disabled"';
			// }

			if(html_tramite != ''){
									// "<input type='checkbox' id='checkEnTramite' "+((enTramite == 'on') ? "checked" : "")+" onChange='autorizacionCxEnTramite(\""+turno+"\")'>Autorizaci&oacute;n en tramite&nbsp;"+
				var htmlCheckbox = "<span style='color:#2779AA;font-weight:bold;font-size:1em;border-radius: 4px;border:1px solid #AED0EA;padding:7px;cursor:default'>"
									+"En tramite:&nbsp;"
									+'<select style="border-radius: 4px;border:1px solid #AED0EA;" id="select_tramites" onChange="autorizacionCxEnTramite(\''+turno+'\', \''+requiere_autorizar_liquidado+'\', \''+perfilMonitorAudRef+'\', this)" tram_inicial="'+tram_inicial+'" '+select_activo+' >'
									+	html_tramite
									+"</select>"
									+ "</span>&nbsp;&nbsp;";

				//$("button[class^=ui-button]:eq(2)").after(htmlCheckbox);
				$("button[class^=ui-button]:eq(0)").before(htmlCheckbox);
			}

			// --> 	Deshabilitar el campo de pedir autorizaciones si el rol no es el correspondiente
			//		Jerson Trujillo, 2017-07-19
			// rolQueIngresaNumAutorizacionCx 	= JSON.parse($("#rolQueIngresaNumAutorizacionCx").val());
			
			if(jQuery.inArray(perfilMonitorAud, rolQueIngresaNumAutorizacionCx) < 0  && permitirAutorizar != 'on')
			{
				$("input[numAutorizacion]").attr("disabled", "disabled");
				$("input[numAutorizacionInsu]").attr("disabled", "disabled");
			}

			// --> Validar permiso para rechazar/autorizar procedimientos
			// var rolQueRechazaAutorizaProcedimientosCx = JSON.parse($("#rolQueRechazaAutorizaProcedimientosCx").val());
			if(jQuery.inArray(perfilMonitorAud, rolQueRechazaAutorizaProcedimientosCx) < 0)
			{
				$("input[confirmar]").attr("disabled", "disabled");
				$("input[rechazar]").attr("disabled", "disabled");
				// $("select[checkBilateral]").attr("disabled", "disabled");
				// $("select[selectVia]").attr("disabled", "disabled");
			}

			// --> Validar permiso para rechazar/autorizar procedimientos
			$("#tablaProcedCups").find("input[negar_vobo]").attr("disabled","disabled");
			var rolQueDaVoBoProcedimientosCx = JSON.parse($("#rolQueDaVoBoProcedimientosCx").val());
			if(modoConsulta != 'on' && jQuery.inArray(perfilMonitorAud, rolQueDaVoBoProcedimientosCx) != -1)
			{
				$("#tablaProcedCups").find("input[negar_vobo]").removeAttr("disabled");
			}

			if(add_procedimiento == 'off')
			{
				$("#btn_agregar_procedimiento").attr("disabled","disabled");
			}

			// --> Auto scroll para algunos campos
			$("[scrollAlto=si]").each(function(){
				if($(this).height() > 120)
					$(this).css({"height":"120px","overflow":"auto","background":"none repeat scroll 0 0"});
			});
			$("[scrollAlto2=si]").each(function(){
				if($(this).height() > 150)
					$(this).css({"height":"150px","overflow":"auto","background":"none repeat scroll 0 0"});
			});

			if($("#fieldProCups").height() > $("#fieldMedAuto").height())
				$("#fieldMedAuto").height($("#fieldProCups").height());
			else
				$("#fieldProCups").height($("#fieldMedAuto").height());

			if($("#fieldMovimientos").height() > $("#fieldObservaciones").height())
				$("#fieldObservaciones").height($("#fieldMovimientos").height());
			else
				$("#fieldMovimientos").height($("#fieldObservaciones").height());

			// --> Activar tooltip
			$("[tooltip=si]").tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
		},'json');
	}

	function guardarAutorizacionLiquidados(perfilMonitorAud, turno){
		var infoAuditoria = new Object();

		var numAutorizacionRequerido = validarProcAutorizacionVaciaActualizada(perfilMonitorAud, "off");

		if(numAutorizacionRequerido)
		{
			jAlert("<span style='color:red'>Faltan procedimientos por autorizaci&oacute;n.<br>Por favor revisarlos.</span>", "Mensaje");
		} else {
			var hayProcedimientos = false;
			$("#tablaProcedCups tr[procedimiento]").each(function(){
				var confirmar = 'off';
				var analizado = '';

				if($(this).find("input[confirmar]").is(':checked')){
					confirmar = 'on';
				}
				else
				{
					if($(this).find("input[rechazar]").is(':checked')){
						confirmar = 'off';
					}
				}

				idReg = $(this).find("input[confirmar]").attr("name");

				// --> Procedimiento que aun no se ha guardado en la tabla de auditoria
				if(confirmar == 'on'){
					hayProcedimientos 	= true;
					infoAuditoria[idReg] 				= new Object();
					infoAuditoria[idReg].confirmado 	= confirmar;
					infoAuditoria[idReg].numAuto 		= $(this).find("input[numAutorizacion]").val();
					infoAuditoria[idReg].numAutoAdmi	= $(this).find("input[numAutorizacion]").attr("numAutoDesdeAdmision");
					infoAuditoria[idReg].via			= $(this).find("select[selectVia]").val();
					infoAuditoria[idReg].organo			= $(this).find("select[selectOrg]").val();
					infoAuditoria[idReg].checkBilateral = $(this).find("[checkBilateral]").val();
				}
			});

			if(hayProcedimientos){
				$.post("MonitorFacturacionERP.php",
				{
					consultaAjax         : '',
					accion               : 'guardarAuditoriaActualizaAutorizacion',
					wemp_pmla            : $('#wemp_pmla').val(),
					turno                : turno,
					wbasedatoFacturacion : $("#wbasedatoFacturacion").val(),
					infoAuditoria        : JSON.stringify(infoAuditoria),
					perfilMonitorAud     : perfilMonitorAud

				}, function(data){
					if(data.error == 1){
						jAlert(data.mensaje,"Alerta");
					} else {
						jAlert("N&uacute;meros de autorizaci&oacute;n actualizados","Mensaje");
					}
				},'json').done(function(){
					$(".editado_css").removeClass("editado_css");
				});
			}
		}
	}

	function validarProcAutorizacionVaciaActualizada(perfilMonitorAud, validarEditado){
		var rolQueIngresaNumAutorizacionCx 	= JSON.parse($("#rolQueIngresaNumAutorizacionCx").val());
		var tipoempresasoat = $("#tipoempresasoat").val();
		var tipoEmp         = $("#variablesParaValidarAutorizacion").attr("tipEnt");

		var numAutorizacionRequeridoDebeGuardar = false;
		if(tipoEmp != tipoempresasoat)
		{
			if(jQuery.inArray(perfilMonitorAud, rolQueIngresaNumAutorizacionCx) >= 0)
			{
				// --> Numeros de autorización para los insumos
				$("input[idMercadoInsumo]").each(function(){
					if($(this).val() == '')
					{
						numAutorizacionRequeridoDebeGuardar = true;
						$(this).parent().attr("class", "listaProPendiente");
					}
				});

				if(!numAutorizacionRequeridoDebeGuardar && validarEditado == 'on'){
					$("input[idMercadoInsumo]").each(function(){
						var elem = $(this);
						// if(elem.val() != '' && elem.parent().parent().find("input[confirmar]").is(':checked') && elem.hasClass('editado_css'))
						if(elem.val() != '' && elem.hasClass('editado_css'))
						{
							numAutorizacionRequeridoDebeGuardar = true;
						}
					});
				}
			}

			if(jQuery.inArray(perfilMonitorAud, rolQueIngresaNumAutorizacionCx) >= 0)
			{
				// --> Numeros de autorización para los procedimientos
				$("input[numAutorizacion]:visible").each(function(){
					if($(this).val() == '' && $(this).parent().parent().find("input[confirmar]").is(':checked'))
					{
						numAutorizacionRequeridoDebeGuardar = true;
						$(this).parent().attr("class", "listaProPendiente");
					}
				});

				if(!numAutorizacionRequeridoDebeGuardar && validarEditado == 'on'){
					$("input[numAutorizacion]:visible").each(function(){
						var elem = $(this);
						// if(elem.val() != '' && elem.parent().parent().find("input[confirmar]").is(':checked') && elem.hasClass('editado_css'))
						if(elem.val() != '' && elem.parent().parent().find("input[confirmar]").is(':checked') && elem.hasClass('editado_css'))
						{
							numAutorizacionRequeridoDebeGuardar = true;
						}
					});
				}
			}
		}
		return numAutorizacionRequeridoDebeGuardar;
	}


	function setEditado(elem){
		if($(elem).attr("requiere_autorizar_liquidado") == 'on'){
			$(elem).addClass("editado_css");
		}
	}

	/**
	 * [marcarTurnoRevisado: Función encargada actualizar el turno a revisado cuando se de click en checkbox]
	 * @param  {[type]} turno    [Código del turno de cirugía a modificarle el estado revisado]
	 * @param  {[type]} elemento [Elemento html donde se ejecutó el click]
	 * @return {[type]}          [description]
	 */
	function marcarTurnoRevisado(turno, elemento){
		var estadoRevisado = ($(elemento).is(":checked")) ? 'on' : 'off';

		var msj_alert = 'Est&aacute; seguro de marcar esta cirug&iacute;a como revisada y que no siga apareciendo en esta lista?'
						+'<br><br>'
						+'<span style="font-weight:bold; color:red;">Aceptar se asume que el facturador revis&oacute; la cuenta manualmente en unix y realiz&oacute; los cambios necesarios<span>';
		jConfirm(msj_alert, 'Confirmar', function(respuesta){
			if(respuesta){
				$.post("MonitorFacturacionERP.php",
				{
					consultaAjax   : '',
					accion         : 'marcarTurnoRevisado',
					wemp_pmla      : $('#wemp_pmla').val(),
					turno          : turno,
					estadoRevisado : estadoRevisado
				}, function(data){
					if(data.error == 1){
						jAlert(data.mensaje, "Alerta");
					} else {
						jAlert(data.mensaje, "Mensaje");
						$("#spn_actualizar_listados").trigger("click");
					}
				},'json').done(function(){
					//
				});
			} else {
				if($(elemento).is(":checked")){
					$(elemento).removeAttr("checked");
				}
			}
		});
	}
	//-----------------------------------------------------------
	//	--> Marcar una cx en proceso de tramite
	//-----------------------------------------------------------
	function autorizacionCxEnTramite(turno, requiere_autorizar_liquidado, perfilMonitorAud, elemento)
	{
		var continuar = true;
		var debeGuardarActualizar = false;
		if(requiere_autorizar_liquidado == 'on'){
			debeGuardarActualizar = validarProcAutorizacionVaciaActualizada(perfilMonitorAud, 'on');

			// Si hay algo que fue editado y falta por guardar no continúa
			if(debeGuardarActualizar) {
				continuar = false;
				$(elemento).val($(elemento).attr("tram_inicial"));
			}
		}

		if(continuar){
			$.post("MonitorFacturacionERP.php",
			{
				consultaAjax : '',
				accion       : 'autorizacionCxEnTramite',
				wemp_pmla    : $('#wemp_pmla').val(),
				turno        : turno,
				causa        : $(elemento).val()
			}, function(html){
				if($("#spn_actualizar_listados").length > 0){
					$("#spn_actualizar_listados").trigger("click");
				}
			}).done(function(){
				$(elemento).attr("tram_inicial",$(elemento).val());
				$(".editado_css").removeClass("editado_css");
			});
		} else {
			var msj_alert = 'Hay procedimientos o insumos con n&uacute;meros de autorizaci&oacute;n <span style="font-weight:bold;color:red;">sin diligenciar o sin guardar</span>.';
			jAlert(msj_alert,"Alerta");
		}
	}


	function verlog(documento,linea,historia,ingreso,fuente,cardetreg)
	{

			//alert(fuente);
			//alert("documento:"+documento+"-linea:"+linea+"historia"+historia+"ingreso:"+ingreso+"fuente:"+fuente+"cardetreg:"+cardetreg);


			$("#explicacionlog").html("");
			$.post("MonitorFacturacionERP.php",
			{
				consultaAjax:     '',
				wemp_pmla:        $('#wemp_pmla').val(),
				accion:           'consultarlog',
				wdocumento:		   documento,
				wlinea:			   linea,
				whistoria:		   historia,
				wingreso:		   ingreso,
				wfuente:		   fuente,
				wreg:			   cardetreg



			},function(data) {

				//alert(data);

				$("#explicacionlog").html(data);


			});

			// abrir modal de explicacion del caso
			$( "#explicacionlog" ).dialog({

			height: 400,
			width:  800,
			modal: true,
			title: "Resultados log",
			      buttons: {
					Cerrar: function() {
					  $( this ).dialog( "close" );
					}
				}
			});
	}

	//-----------------------------------------------------------
	//	--> Cargar autocomplete
	//-----------------------------------------------------------
	function crear_autocomplete(HiddenArray, CampoCargar, consecutivo, consecAutorizacion)
	{
		ArrayValores  = JSON.parse($("#"+HiddenArray).val());

		var ArraySource   = new Array();
		var index		  = -1;
		for (var CodVal in ArrayValores)
		{
			index++;
			ArraySource[index] = {};
			ArraySource[index].value  = CodVal;
			ArraySource[index].label  = CodVal+"-"+ArrayValores[CodVal];
			ArraySource[index].nombre = ArrayValores[CodVal];
		}

		// --> Si el autocomplete ya existe, lo destruyo
		if( $("#"+CampoCargar).attr("autocomplete") != undefined )
			$("#"+CampoCargar).removeAttr("autocomplete");

		// --> Creo el autocomplete
		$( "#"+CampoCargar ).autocomplete({
			minLength: 	0,
			source: 	ArraySource,
			select: 	function( event, ui ){
				$( "#"+CampoCargar ).val(ui.item.label);
				$( "#"+CampoCargar ).attr('valor', ui.item.value);
				$( "#"+CampoCargar ).attr('nombre', ui.item.nombre);
				procedimientoSeDebeAutorizar(consecAutorizacion, ui.item.value, consecutivo);

				return false;
			}
		});
		limpiaAutocomplete(CampoCargar, consecutivo);
	}
	//----------------------------------------------------------------------------------
	//	--> Validar si un procedimiento requiere autorizacion
	//----------------------------------------------------------------------------------
	function procedimientoSeDebeAutorizar(consecAutorizacion, codProcedimiento, consecutivo)
	{
		$.post("MonitorFacturacionERP.php",
		{
			consultaAjax:   		'',
			accion:         		'procedimientoSeDebeAutorizar',
			wemp_pmla:         		$('#wemp_pmla').val(),
			codProcedimiento:		codProcedimiento,
			codEnt:					$("#variablesParaValidarAutorizacion").attr("codEnt"),
			nitEnt:					$("#variablesParaValidarAutorizacion").attr("nitEnt"),
			tipEnt:					$("#variablesParaValidarAutorizacion").attr("tipEnt"),
			planEmp:				$("#variablesParaValidarAutorizacion").attr("planEmp"),
			ccoCx:					$("#variablesParaValidarAutorizacion").attr("ccoCx"),
			tarifaEnt:				$("#variablesParaValidarAutorizacion").attr("tarifaEnt"),
			consecutivo:			consecutivo

		}, function(respuesta){
			if($.trim(respuesta.pideAutorizacion) == 'on')
				$("[numAutorizacion="+consecAutorizacion+"]").show();
			else
				$("[numAutorizacion="+consecAutorizacion+"]").hide();

			// --> Pintar imagen que muestra la información del tipo de liquidación
			// $("#imgTipLiq"+consecutivo).parent().remove();
			$("#procedimiento-"+consecutivo).parent().find("[tooltSelect]").remove();
			$("#procedimiento-"+consecutivo).before("<span tooltSelect>"+respuesta.htmlTipoLiq+"</span>");
			// --> Activar tooltip
			$("#imgTipLiq"+consecutivo).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });

			// --> Pintar select de organos
			$("[selectOrg="+consecutivo+"]").html(respuesta.selectOrg);

			if(respuesta.mostrarSelect)
				$("[selectOrg="+consecutivo+"]").show();
			else
				$("[selectOrg="+consecutivo+"]").hide();

		}, 'json');
	}
	//----------------------------------------------------------------------------------
	//	--> Ocultar o mostrar divs
	//----------------------------------------------------------------------------------
	function verElemento(index)
	{
		$("[tabsIdZona] td").css({
			"color"				:	"#2779AA",
			"background-color"	:	"#EEF6FC"
		});

		$("[idTabZona="+index+"]").css({"color":"#FFFFFF", "background-color":"#5DB9E8"});

		$("[idZona]").hide();
		$("[idZona="+index+"]").show();
	}
	//----------------------------------------------------------------------------------
	//	--> Enlace para ir a digitalización
	//----------------------------------------------------------------------------------
	function abrirDigitalizar(historia, ingreso)
	{
		var url 	= "/matrix/movhos/procesos/rep_admdoc.php?wemp_pmla="+$("#wemp_pmla").val()+"&hist="+historia+"-"+ingreso;
		alto		= screen.availHeight;
		ventana 	= window.open('','','fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=0,scrollbars=1,titlebar=0');
		ventana.document.open();
		ventana.document.write("<span><b>CONSULTA DESDE MONITOR DE AUTORIZACIONES/AUDITORIA<b></span><br><input type='button' value='Cerrar Ventana' onclick='window.close();'><br><iframe name='' src='" + url + "' height='" + (parseInt(alto,10) - 150) + "' width='100%' scrolling=yes frameborder='0'></iframe>");
	}
	//----------------------------------------------------------------------------------
	//	--> Enlace para ir a la bitacora
	//----------------------------------------------------------------------------------
	function abrirBitacora(historia, ingreso)
	{
		var url 	= "/matrix/movhos/procesos/rBitacora.php?ok=0&empresa="+$("#wmovhos").val()+"&codemp="+$("#wemp_pmla").val()+"&whis="+historia+"&wnin="+ingreso;
		alto		= screen.availHeight;
		ventana 	= window.open('','','fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=0,scrollbars=1,titlebar=0');
		ventana.document.open();
		ventana.document.write("<span><b>CONSULTA DESDE MONITOR DE AUTORIZACIONES/AUDITORIA<b></span><br><input type='button' value='Cerrar Ventana' onclick='window.close();'><br><iframe name='' src='" + url + "' height='" + (parseInt(alto,10) - 150) + "' width='100%' scrolling=yes frameborder='0'></iframe>");
	}
	//----------------------------------------------------------------------------------
	//	--> Enlace para ir a la historia clinica
	//----------------------------------------------------------------------------------
	function abrirHce(documento, tipoDoc, historia, ingreso, desde)
	{

		var url 	= "/matrix/HCE/procesos/HCE_Impresion.php?empresa="+$("#whce").val()+"&origen="+$("#wemp_pmla").val()+"&wcedula="+documento+"&wtipodoc="+tipoDoc+"&wdbmhos="+$("#wmovhos").val()+"&whis="+historia+"&wing="+ingreso+"&wservicio=*&protocolos=0&CLASE=C&BC=1";
		alto		= screen.availHeight;
		ventana 	= window.open('','','fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=0,scrollbars=1,titlebar=0');
		ventana.document.open();
		if(desde=='auditoria')
		{
			desde = "CONSULTA DESDE MONITOR DE AUTORIZACIONES/AUDITORIA";
		}
		if(desde=='paf')
		{
			desde = "CONSULTA DESDE MONITOR PAF";
		}
		ventana.document.write("<span><b>"+desde+"<b></span><br><iframe name='' src='" + url + "' height='" + (parseInt(alto,10) - 70) + "' width='100%' scrolling=yes frameborder='0'></iframe>");
	}
	//----------------------------------------------------------------------------------
	//	--> Enlace para abrir resolucion cups
	//----------------------------------------------------------------------------------
	function abrirResolucion()
	{
		var url 	= "/matrix/images/medical/ips/resolucionCups.xlsx";
		alto		= screen.availHeight;
		ventana 	= window.open('','','fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=0,scrollbars=1,titlebar=0');
		ventana.document.open();
		ventana.document.write("<span><b>CONSULTA DESDE MONITOR DE AUTORIZACIONES/AUDITORIA<b></span><br><iframe name='' src='" + url + "' height='" + (parseInt(alto,10) - 70) + "' width='100%' scrolling=yes frameborder='0'></iframe>");
	}
	//----------------------------------------------------------------------------------
	//	--> Enlace para abrir programa de ordenes paf
	//----------------------------------------------------------------------------------
	function abrirOrdenesPaf(documento, ruta)
	{
		var url 	= "/matrix/"+ruta+"?criterio="+documento;
		alto		= screen.availHeight;
		ventana 	= window.open('','','fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=0,scrollbars=1,titlebar=0');
		ventana.document.open();
		ventana.document.write("<span><b>CONSULTA DESDE MONITOR DE AUTORIZACIONES/AUDITORIA<b></span><br><iframe name='' src='" + url + "' height='" + (parseInt(alto,10) - 70) + "' width='100%' scrolling=yes frameborder='0'></iframe>");
	}
	//----------------------------------------------------------------------------------
	//	--> Abrir modal mostrando el campo Turcom de la tabla tcx_11
	//----------------------------------------------------------------------------------
	function mostrarCartaDerechos(elemento)
	{
		html = "<br><textarea style='font-family: verdana;width:550px;height:400px;font-size:8pt' >"+$(elemento).attr("cartaDerechos")+"</textarea><br><br>";
		
		$("#divCartaDerechos").show().html(html).dialog({
			dialogClass: 'fixed-dialog',
			modal: true,
			title: "<div align='center' style='font-size:10pt'>Carta de derechos:</div>",
			width: 600
		});
	}
	//----------------------------------------------------------------------------------
	//	--> Revesar cx para que aparezca en el monitor de auditoria/autorizaciones
	//----------------------------------------------------------------------------------
	function reversarCxAlMonitorAud(turnoCx, tipoRevAuto)
	{
		jConfirm('Est&aacute; seguro de reversar la cx?', 'Confirmar', function(respuesta){
			if(respuesta){
				$.post("MonitorFacturacionERP.php",
				{
					consultaAjax:   		'',
					accion:         		'reversarCxAlMonitorAud',
					wemp_pmla:         		$('#wemp_pmla').val(),
					turnoCx:				turnoCx,
					tipoRevAuto:			tipoRevAuto
				}, function(respuesta){

				}, 'json');
			}
		});
	}
	//----------------------------------------------------------------------------------
	//	--> Cargar toltip del procedimiento segun el organo seleccionado
	//----------------------------------------------------------------------------------
	function cargarTooltSelect(elemento)
	{
		valTooltSelect = $(elemento).find("option:selected").attr("tooltselect");
		valTooltSelect = "<img style='cursor:help' tooltip='si' title='"+valTooltSelect+"' width='13' height='13' src='../../images/medical/sgc/info_black.png'>";

		$(elemento).parent().parent().find("span[tooltSelect]").html(valTooltSelect);
		$(elemento).parent().parent().find("span[tooltSelect] [tooltip=si]").tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
	}
	//----------------------------------------------------------------------------------
	//	--> Controlar que el input no quede con basura, sino solo con un valor seleccionado
	//----------------------------------------------------------------------------------
	function limpiaAutocomplete(idInput, consecutivo)
	{
		$( "#"+idInput ).on({
			focusout: function(e) {
				if($(this).val().replace(/ /gi, "") == '')
				{
					$(this).val("");
					$(this).attr("valor","");
					$(this).attr("nombre","");
					if(consecutivo != '')
						$("#codigo-"+consecutivo).text("");
				}
				else
				{
					$(this).val($(this).attr("nombre"));
					if(consecutivo != '')
						$("#codigo-"+consecutivo).text($( "#"+idInput ).attr("valor"));
				}
			}
		});
	}

	//--------------------------------------------------------------------------
	//	Al cargar el programa
	//--------------------------------------------------------------------------
	var relojTemp;
	var relojTemp2;
	var blinkReautorizar;

	$(document).ready(function() {
		console.clear();
		traer_rol();
		$("body").width('100%');
		$("body").height('100%');

		//------ inicio de datepicker
		cargar_elementos_datapicker();


		cargar_procedimiento( );
		todos_los_procedimientos();
		blinkJqueryElementClass("css_paciente_alta_cx");
		blinkJqueryElementClass("css_img_liquidar");
		//pintarCupsAutorizados();
	});

	function verOcultarTrCco(tr_cco)
	{
		if($("."+tr_cco).is(":visible"))
		{
			$("."+tr_cco).hide(300);
		}
		else
		{
			$("."+tr_cco).show(300);
		}
	}

	function actualizarMonitorMercados(elem)
	{
		actualizarMonitor(globalMonitor,globalnumero, $("#fechas_cirugias").val());
	}

	function mensajeFailAlert(this_, mensaje, xhr, textStatus, errorThrown)
    {
        // console.log(xhr);
        var obj_data = [];
        var data_ajax = this_["data"]
        var data_parcial = data_ajax.substr(0, 200)+"...";
        // console.log(data_parcial);
        var url_err = document.location.href;
        // console.log(url_err);

        var responseText = xhr.responseText.split("{\"");
        var detalle_error= "";
        if(responseText.length > 0)
        {
            detalle_error = responseText[0];
            // console.log(detalle_error);
        }

        var msg_fail = "";
        if (xhr.status === 0) {
            msg_fail = 'No hay conexi&oacute;n: verificar la red.';
            // console.log(msg_fail);
        } else if (xhr.status == 404) {
            msg_fail = 'Página no encontrada [404]';
            // console.log(msg_fail);
        } else if (xhr.status == 500) {
            msg_fail = 'Error interno del servidor [500].';
            // console.log(msg_fail);
        } else if (textStatus === 'parsererror') {
            msg_fail = 'Respuesta JSON fall&oacute;.';
            // console.log(msg_fail);
        } else if (textStatus === 'timeout') {
            msg_fail = 'Error tiempo de respuesta agotado.';
            // console.log(msg_fail);
        } else if (textStatus === 'abort') {
            msg_fail = 'Respuesta ajax abortada.';
            // console.log(msg_fail);
        } else {
            msg_fail = 'Error desconocido: ' + xhr.responseText;
            // console.log(msg_fail);
        }

        var msj_extra = '';
        msj_extra = (mensaje != '') ? "<br>"+mensaje: mensaje;
        jAlert($("#failJquery").val()+msj_extra, "Mensaje");
        $("#div_error_interno").html(xhr.responseText);

        obj_data.push(msg_fail);
        obj_data.push(data_parcial);
        obj_data.push(detalle_error);
        obj_data.push(url_err);
        console.log(obj_data);

        $.post("MonitorFacturacionERP.php",
        {
            consultaAjax  : '',
            wemp_pmla     : $('#wemp_pmla').val(),
            accion        : 'guardar_error_ajax_log',
            msg_fail      : msg_fail,
            data_parcial  : data_parcial,
            detalle_error : detalle_error,
            url_err       : url_err
        },function(data){
            if(data.error == 1)
            {
                console.log(data.mensaje);
            }
            else
            {
                console.log("Evento guardado en log de errores..");
            }
        },
        'json'
        ).done(function(){
            //
        }).fail(function(xhrLog, textStatusLog, errorThrownLog) { console.log(xhrLog.responseText) });

        // console.log(xhr);
        // jAlert("error interno: "+xhr.responseText, "Mensaje"); console.log("error");
    }


	function ocultarTurnoMonitor(this_,wcodigoturno)
	{
		var obJson             = {};
		obJson['consultaAjax'] = '';
		obJson['accion']       = 'ocultar_turno_monitor';
		obJson['wcodigoturno'] = wcodigoturno;
		obJson['wemp_pmla']    = $('#wemp_pmla').val();

        $.post("MonitorFacturacionERP.php", obJson,
            function(data){
                if(data.error == 1)
                {
                    jAlert(data.mensaje, "Mensaje");
                }
                else
                {
                	// console.log($(this_).parents("tr"));
                	$(this_).parents("tr").css("background-color","orange");
                	$(this_).parents("tr").hide(500);
                }
                return data;
        },"json").done(function(data){
            //
        }).fail(function(xhr, textStatus, errorThrown) { mensajeFailAlert(this,'', xhr, textStatus, errorThrown); });
	}

	function abrir_programa(historia , ingreso)
	{
		var raiz_matrix = "http://<?=$RAIZ_MATRIX?>";
		// var url = raiz_matrix+"/gesapl/procesos/gestor_aplicaciones.php?wemp_pmla=01&wtema=05";
		var url = "/matrix/gesapl/procesos/gestor_aplicaciones.php?wemp_pmla="+$("#wemp_pmla").val()+"&wtema=IPSERP&whistoria="+historia+"&wing="+ingreso+"";
		// console.log(url);
		referenciaVentana = window.open(url, "nuevo", "directories=no, location=no, menubar=no, scrollbars=yes, statusbar=no, tittlebar=no, width=2000, height=1000");
		//referenciaVentana.close();
		referenciaVentana.recargar("id_href_37","procesos/Cargos_ipsERP.php","consultaAjax=","","visor_programas","37","Cargos");

	}


	function abrirVentanaPlatillaMercados()
	{
		var raiz_matrix = "http://<?=$RAIZ_MATRIX?>";
		// var url = raiz_matrix+"/gesapl/procesos/gestor_aplicaciones.php?wemp_pmla=01&wtema=05";
		var url = "/matrix/ips/procesos/plantillasMercadosCirugia.php?wemp_pmla="+$("#wemp_pmla").val()+"&consultaview=on";
		// console.log(url);
		referenciaVentana = window.open(url, "nuevo", "directories=no, location=no, menubar=no, scrollbars=yes, statusbar=no, tittlebar=no, width=2000, height=1000");
	}

	function abrir_programaMonitorCirugiasDia(cedula, historia, ingreso, wcodigoturno, turnopermitido, pqte)
	{
		$.post("MonitorFacturacionERP.php",
		{
			consultaAjax : '',
			wemp_pmla    : $('#wemp_pmla').val(),
			accion       : 'liquidar_cirugia_actualizar',
			whistoria    : historia,
			wingreso     : ingreso,
			wcedula      : cedula,
			wcodigoturno : wcodigoturno
		}, function (data){
			//
		},"json").done(function(){
			// cronUnixCargos();
		});
		var raiz_matrix = "http://<?=$RAIZ_MATRIX?>";
		var url = "/matrix/gesapl/procesos/gestor_aplicaciones.php?wemp_pmla="+$("#wemp_pmla").val()+"&wtema=IPSERP&whistoria="+historia+"&wing="+ingreso+"&turnopermitido="+turnopermitido+"&pqte_mon="+pqte;
		// console.log(url);
		referenciaVentana = window.open(url, "nuevo", "directories=no, location=no, menubar=no, scrollbars=yes, statusbar=no, tittlebar=no, width=2000, height=1000");
		//referenciaVentana.close();
		referenciaVentana.recargar("id_href_37","procesos/Cargos_ipsERP.php","consultaAjax=","","visor_programas","37","Cargos");
	}

	function initAutocomplete()
        {
            $('.buscador_autocomplete').on({
                focusout: function(e) {
                    if($(this).val().replace(/ /gi, "") == '')
                    {
                        $(this).val("");
                        $(this).attr("valor","");
                        $(this).attr("nombre","");
						filtrarListaPaf();
                    }
                    else
                    {
                        $(this).val($(this).attr("nombre"));

                    }
                }
            });
        }
//=======================================================================================================================================================
//	F I N  F U N C I O N E S  J A V A S C R I P T
//=======================================================================================================================================================
	</script>

<!--=====================================================================================================================================================================
	E S T I L O S
=====================================================================================================================================================================-->
	<style type="text/css">
		.imagen { width: 70px; height: auto;}

		.ui-autocomplete{
			max-width: 	250px;
			max-height: 160px;
			overflow-y: auto;
			overflow-x: hidden;
			font-size: 	9pt;
		}
		#tooltip{font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 8pt;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:3px;opacity:1;border-radius: 4px;}
		#tooltip div{margin:0; width:auto;}

		.pad{
			padding: 	6px;
		}
		.Bordegris{
			border: 1px solid #999999;
		}
		.ui-effects-transfer { border: 2px dotted gray; }

		.faltanDatos{
		   border: 1px solid red;
		}



		.campoRequerido{
			border: 1px solid red;
		}

		.campoRequerido_exa{
		border: 1px orange solid;
		background-color:lightyellow;
		}



		.divfija {
			width:98%;
			}

		.cs_mercado{
			background-color: #cecece;
            font-size: 8pt;
            padding: 1px;
			font-family: verdana;
		}

		.spn_resaltado{
			font-weight:bold;
			font-size:10.5pt;
		}

		.classOver{
            background-color: #f7f7bb;
        }

		.classOverSp{
            background-color: #f7f7bb;
            font-size: 8pt;
            padding: 1px;
			font-family: verdana;
        }

		.classOverDel{
            background-color: #E8AE22;
        }

        .link_opcion{
        	cursor:pointer;
        	color:blue;
        }

        .sin_permiso{
        	display: none;
        }

        .css_paciente_alta_cx
        {
        	color: red;
        	/*font-weight: bold;*/
        	font-family: cursive;
        	font-size: 0.9em;
        }

        .titulopagina2
        {
            border-bottom-width: 1px;
            /*border-color: <?=$bordemenu?>;*/
            border-left-width: 1px;
            border-top-width: 1px;
            font-family: verdana;
            font-size: 18pt;
            font-weight: bold;
            height: 30px;
            margin: 2pt;
            overflow: hidden;
            text-transform: uppercase;
        }

		.divmensajetarifa{
			width:400px;
			height:70px;
			font-size : 16px  ;
			background-color:#FFFFDD;
			border: 1px orange solid;


		}

		.hceFila1 {
			color: #000066;
			background: #E8EEF7;
			font-size: 7pt;
			font-family: Arial;
			font-weight: bold;
			text-align: center;
			height: 1em;
		}

		.hceFila2 {
			color: #000066;
			background: #C3D9FF;
			font-size: 7pt;
			font-family: Arial;
			font-weight: bold;
			text-align: center;
			height: 1em;
		}
		.encabezadoTablaHce {
			color: #000066;
			background: #999999;
			font-size: 8pt;
			font-family: Arial;
			text-align: left;
			height: 1em;
		}

		.resalto_rojo {
			color:#B40404;
		}

		.roundspn {
			background-color: orange;
		    display: inline-block;
		    border-radius: 50%;
		    width: 12px;
		    height: 12px;
		    text-align: center;
		}

		.btn_lupa{
			cursor: pointer;
		}

		.errorDevApl{
			background-color: #FF9981;
			background-image: url("../../images/medical/sgc/Warning-32.png");
			background-repeat: no-repeat;
			background-position: right;
			background-size: 12px 12px;
		}

		.estado-revisado{
			display: none;
		}

		.editado_css{
			background-color: #FFFFCE;
		}

	</style>
<!--=====================================================================================================================================================================
	F I N   E S T I L O S
=====================================================================================================================================================================-->

<!--=====================================================================================================================================================================
	I N I C I O   B O D Y
=====================================================================================================================================================================-->
	<BODY>
	<?php
	    encabezado("<div class='titulopagina2'>Monitores facturaci&oacute;n</div>", $wactualiza, "clinica");
	?>
	<input type="hidden" name="hidden_procedimiento" id="hidden_procedimiento" value='<?=json_encode(obtener_array_procedimientos($conex, $wemp_pmla, $wbasedato))?>'>
	<input type='hidden' name='hidden_paquetes' id='hidden_paquetes' value='<?=json_encode(Obtener_array_paquetes($conex, $wemp_pmla, $wbasedato))?>'>

	
	<input type='hidden' id = 'wemp_pmla' value='<?=$wemp_pmla?>'>
	<input type='hidden' id = 'wmovhos' value='<?=consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos')?>'>
	<input type='hidden' id = 'whce' value='<?=consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce')?>'>
	<input type='hidden' id = 'wemp_pmla_tal' value='<?=$wemp_pmla?>'>
	<input type='hidden' id = 'fechas_cirugias_oculto' value='<?=$wfecha?>'>
	<input type='hidden' id = 'wcodigo_cargo_wuse' value=''>
	<input type='hidden' id = 'perfilMonitorAud' value=''>
	<input type='hidden' id = 'filtroEmpresasRevisar' value=''>
	<input type='hidden' id = 'arr_permisos_opciones' value=''>
	<input type='hidden' name='failJquery' id='failJquery' value='El programa termin&oacute; de ejecutarse pero con algunos inconvenientes <br>(El proceso no se complet&oacute; correctamente)' >
	<!-- <audio id="audio_fb"><source src="../../images/medical/root/alertaMensaje.mp3" type="audio/mp3"></audio> -->

	<div id='divprincipal' class='divfija' ></div>
	<div id='divsecundario' class='divfija' style='display:none;'></div>

	<!-- // Div que crea las tarifas en matrix -->
	<div id='div_tarifas' style='display: none' ><br><br><input type='hidden' id='wnumero_id'  disabled='disabled'><input type='hidden' id='wconcepto_empresa'  disabled='disabled'><input type='hidden' id='wfechadecargo'  disabled='disabled'><input type='hidden' id='wtar'  value=''><input type='hidden' id='parametro'><input type='hidden' id='origen'><input type='hidden' id='wturnohidden'>
				<table align='center'>
					<tr class='fila1'>	<td class='encabezadoTabla'>Tipo De Empresa</td><td><input type='text' style='width:150px'  class='requerido' value ='Todos' disabled='disabled'></td>
										<td class='encabezadoTabla' >Codigo Empresa</td><td><input type='text' style='width:350px' class='requerido' disabled='disabled' id='wresponsable_empresa'></td>
					</tr>
					<tr>
										<td class='encabezadoTabla'>Centro de Costos</td>
										<td id='td_wcco' colspan ='1' class='fila1' ></td>
										<td class='encabezadoTabla'>Nombre Empresa</td><td class='fila1' colspan = '1'><input style='width:350px' type='text' class='requerido' disabled='disabled' id='wnombreempresa'></td>

					</tr>

					<tr class='fila1'>
										<td class='encabezadoTabla' >Codigo Concepto</td><td colspan = '1'><select disabled='disabled' style='width:150px' class='requerido' id='codigoconcepto' onchange ='traerNombreConcepto()'>
										<?php
										$wbasedato 		= consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
										$select = "SELECT  Grucod , Grudes
													 FROM  ".$wbasedato."_000200
													WHERE  Gruest = 'on' ";


										$res = mysql_query($select,$conex) or die ("Query (nombreConcepto): ".$select." - ".mysql_error());
										echo "<option value=''>Seleccione</option>";
										while ($row = mysql_fetch_array($res))
										{
											echo "<option value='".$row['Grucod']."'>".$row['Grucod']."-".$row['Grudes']."</option>";
										}
										
										//Se cierra conexión de la base de datos :)
										//Se comenta cierre de conexion para la version estable de matrix :)
										//mysql_close($conex);
										?>
										</select></td>
										<td class='encabezadoTabla' >Nombre de Concepto</td><td colspan = '1' ><input type='text' disabled='disabled' class='requerido'  style='width:350px' id='nombreconcepto'></td>

					</tr>

					<tr class='fila1'>
										<td class='encabezadoTabla' >Codigo Cups</td><td colspan = '1'><input type='text' disabled='disabled'  class='requerido' style='width:150px' id='wprocedimiento'></td>
										<td class='encabezadoTabla' >Nombre Cups</td><td colspan = '1' ><input type='text' disabled='disabled' class='requerido'  id='wnombre_procedimiento' style='width:350px'></td>

					</tr>
					<tr class='fila1'>
										<td class='encabezadoTabla'>C. Propio Empresa</td><td><input type='text' class='requerido' style='width:150px' id='wprocedimiento_empresa'></td>
										<td colspan ='1' class='encabezadoTabla'>Nombre procediminento empresa</td><td><input class='requerido' type='text'id='wnom_procedimiento_empresa' style='width:350px'></td>
					</tr>

					<tr class='fila1'>
											<td class='encabezadoTabla' >Tipo Facturacion</td>
											<td><select id='wtipo_facturacion' onchange='cambiartipofacturacion()' style='width:150px'  class='requerido'  disabled='disabled'><option value='CODIGO'>Codigo</option><option value='UVR'>UVR</option><option value='GQX'>GQX</option></select></td>
											<!--<td colspan ='3' id='td_input_tipo_facturacion' >
												<table class='fila1' id='tablevalor'>
													<tr>
															<td>Valor: </td>
															<td><input  type='text' id='wvalor' ></td>
													</tr>
												</table>
											</td>-->
											<td class='encabezadoTabla'>Valor </td>
											<td><input  type='text' class='requerido decimal' id='wvalor' ></td>
					</tr>
					<tr>
									<td colspan='5' align='center'>
										<br><br><input type='button'  value='Grabar' onclick='grabar_tarifa()' id='idbuttongrabartarifa' align='center'><input type='button'  value='Cerrar Ventana' onclick='cerrar_tarifa()' align='center'>
									</td>
					</tr>
					<tr>
									<td colspan='5' align='center'>
										<br><div align='center' id='idcreartarifamensaje' ></div>
									</td>
					</tr>
					</table>
				</div>
	<!--
	// Div tarifas Unix -->
	<div id='div_tarifas_unix' style='display: none' >
				<input type='hidden' id='wparametro_examen' ><br><br><br>
				<table align='center'>
					<tr>
						<td class='encabezadoTabla'>Codigo de Examen</td><td class='fila1'><input disabled='disabled' type='text' id='examen_codigo' value=''></td>
						<td class='encabezadoTabla' >Nombre de Examen</td><td class='fila1'><input disabled='disabled' type='text' id='examen_nombre' size='55' value=''></td>
					</tr>
					<tr>
						<td class='encabezadoTabla'>Codigo Anexo</td><td class='fila1'><input type='text' disabled='disabled' id='codigo_anexo_examen'></td>
						<td class='encabezadoTabla' >Tipo de Facturacion</td><td class='fila1' ><input type='text' disabled='disabled'  id='tipo_facturacion_examen'></td>
					</tr>
					<tr>
						<td class='encabezadoTabla'>Grupo de Examen</td><td class='fila1'><input type='text' disabled='disabled' id='cod_grupo_examen'></td>
						<td class='encabezadoTabla' >Nivel</td><td class='fila1' ><input type='text' disabled='disabled'  id='cod_nivel'></td>
					</tr>
					<tr>
						<td class='encabezadoTabla' >Tarifa</td><td class='fila1' ><input type='text' disabled='disabled'  id='examen_tarifa' value=''></td>
						<td class='encabezadoTabla' >Concepto</td><td class='fila1' ><input type='text' disabled='disabled' id='examen_concepto'></td>


					<tr>
						<td class='encabezadoTabla' >Valor Actual</td><td class='fila1' ><input type='text'  class='campoRequerido_exa requerido_exa' id='valor_actual'></td>
						<td class='encabezadoTabla' >Fecha</td><td class='fila1' ><input type='text' class='campoRequerido_exa requerido_exa' id='wfecha_tarifa_examen' onchange='verificar_dato()'></td>
					</tr>
					<tr>
						<td class='encabezadoTabla' >Valor Anterior</td><td class='fila1' ><input type='text' class='campoRequerido_exa requerido_exa' id='valor_anterior'></td>
						<td class='encabezadoTabla' >Centro de Costos</td><td class='fila1' ><input type='text' disabled='disabled'  id='wcco_exa' value='1000'><input type='hidden'  id='porcentaje_exa' value='0'></td>
					</tr>

					<tr>
						<td colspan='5' align='center'>
							<br><br><input type='button'  value='Grabar' onclick='grabar_tarifa_unix()' align='center'><input type='button'  value='Cancelar' onclick='cerrar_tarifa()' align='center'>
						</td>
					</tr>
					</table>
			</div>
			<div  align='center' id='div_ppal_mercado' style='display: none; border:3px solid green;' >
			<div style='text-align:center; font-size:8pt;font-weight: bold;' class='fila1'>Al cerrar esta ventana o presionar UNA SOLA VEZ la tecla "Esc" puede confirmar que grabe los cambios realizados en la lista de insumos sin necesidad de presionar el boton "Grabar". Si presiona dos veces "Esc" no guardar&aacute; los cambios</div>
			<input type='hidden' id='wcedulaoculto'>
			<input type='hidden' id='whistoriaoculto'>
			<input type='hidden' id='wingresooculto'>
			<input type='hidden' id='wcodigoturnooculto'>
			<input type='hidden' id='wcco_oculto_div'>
			<table>
				<tr>
					<td class='encabezadoTabla' style="font-size : 10pt; width:180px;" ><b>Cedula</b></td>
					<td class='encabezadoTabla' style="font-size : 10pt; width:270px;" ><b>Nombre</b></td>
					<td class='encabezadoTabla' style="font-size : 10pt; width:80px;" ><b>Turno</b></td>
					<td class='encabezadoTabla' style="font-size : 10pt; width:225px;" ><b>Descripci&oacute;n</b></td>
				</tr>
				<tr>
					<td class='fila2' style="font-size : 8pt;"><input type='text' disabled='disabled' id='wcedula'></td>
					<td class='fila2' style="font-size : 8pt;"><input type='text' size='45' disabled='disabled' id='wnombrepaciente'></td>
					<td class='fila2' style="font-size : 8pt;"><span style="font-weight:bold;" id="wturno_cargar"></span></td>
					<td class='fila2' rowspan="3"><span style="font-size:8pt;" id="desc_cx_cargar"></span></td>
				</tr>
				<tr>
					<td class='encabezadoTabla' style="font-size : 10pt;"><b>Hitoria</b></td>
					<td class='encabezadoTabla' style="font-size : 10pt;"><b>Ingreso</b></td>
					<td class='encabezadoTabla' style="font-size : 10pt;"><b>&nbsp;</b></td>
				</tr>
				<tr>
					<td class='fila2' style="font-size : 8pt;"><input type='text' disabled='disabled' id='div_ppal_mercado_historia'></td>
					<td class='fila2' style="font-size : 8pt;"><input type='text' size='5' disabled='disabled' id='div_ppal_mercado_ingreso'></td>
					<td class='fila2' style="font-size : 8pt;"><span id="wturno_cargar"></span></td>
				</tr>
					<!-- <tr align='left' class='fila1'>
						<td  colspan='1' id='etiquetatitulo' align='left' colspan='2'>
							<b>PX Autorizados (Admisiones)</b>
						</td>
						<td  colspan='1' id='etiquetatitulo' align='left' colspan='2'>
							<b>PX Autorizados (Cirugia)</b>
						</td>
					<tr class='fila2'>
						<td align='left' colspan='1'>
							<div id='procedimiento_autorizado'></div>
						</td>
						<td align='left' colspan='1'>
							<div id='procedimiento_autorizado_cirugia'></div>
						</td>
					</tr>
					<tr align='left' class='fila1'>
						<td align='left'>
							<b>Procedimiento</b><input type ='radio'  name='procedimiento_paquete' value='procedimiento' checked onchange='evaluar_procedimiento()'>
						</td>
						<td  colspan='2'>
							<b>Paquete</b><input type ='radio'  name='procedimiento_paquete' value='paquete' onchange='evaluar_procedimiento()'>
						</td>
					</tr> -->
					<tr align='left' class='fila2'>
						<td align='left' colspan='4'>
								<input type='text' id='busc_procedimiento' size='60' style='display: none;'>
								<input type='text' id='busc_paquete' size='60' style='display: none'>
						</td>
					</tr>
			</table>

			<div  align='center' id='divmercado' >

			</div>
			</div>
			<div  align='center' id='div_ppal_mercado_devolucion' style='display: none; border:3px solid orange;' >
			<div style='text-align:center; font-size:8pt;font-weight: bold;' class='fila1'>Al cerrar esta ventana o presionar UNA SOLA VEZ la tecla "Esc" puede confirmar que grabe los cambios realizados en la lista de insumos sin necesidad de presionar el boton "Grabar". Si presiona dos veces "Esc" no guardar&aacute; los cambios</div>
			<input type='hidden' id='wcedulaoculto_devolucion'>
			<input type='hidden' id='whistoriaoculto_devolucion'>
			<input type='hidden' id='wingresooculto_devolucion'>
			<input type='hidden' id='wcodigoturnooculto_devolucion'>
			<br>
			<table width=''>
				<tr>
					<td class='encabezadoTabla' style="font-size : 10pt;width:180px;" ><b>Cedula</b></td>
					<td class='encabezadoTabla' style="font-size : 10pt;width:270px;" ><b>Nombre</b></td>
					<td class='encabezadoTabla' style="font-size : 10pt;width:80px;" ><b>Turno</b></td>
					<td class='encabezadoTabla' style="font-size : 10pt;width:225px;" ><b>Descripci&oacute;n</b></td>
				</tr>
				<tr>
					<td class='fila2' style="font-size : 8pt;"><input type='text' disabled='disabled' id='wcedula_devolucion'></td>
					<td class='fila2' style="font-size : 8pt;"><input type='text' size='45' disabled='disabled' id='wnombrepaciente_devolucion'></td>
					<td class='fila2' style="font-size : 8pt;"><span style="font-weight:bold;" id="wturno_devolucion"></td>
					<td class='fila2' style="font-size : 8pt;" rowspan="3"><span id="desc_cx_devolucion"></span></td>
				</tr>
				<tr>
					<td class='encabezadoTabla' style="font-size : 10pt;"><b>Historia</b></td>
					<td class='encabezadoTabla' style="font-size : 10pt;"><b>Ingreso</b></td>
					<td class='encabezadoTabla' style="font-size : 10pt;"><b>&nbsp;</b></td>
				</tr>
				<tr>
					<td class='fila2' style="font-size : 8pt;"><input type='text' disabled='disabled' id='mercado_devolucion_historia'></td>
					<td class='fila2' style="font-size : 8pt;"><input type='text' size='5' disabled='disabled' id='mercado_devolucion_ingreso'></td>
					<td class='fila2' style="font-size : 8pt;"><span style="" id="">&nbsp;</td>
				</tr>
			</table>
			<br>
			<div  align='center' id='divmercado_devolucion' >

			</div>
			</div>

			<div align='center' id='div_ppal_mercado_liquidar_unix' style='display: none; border:3px solid red;' >
				<input type='hidden' id='wcedulaoculto_liquidar'>
				<input type='hidden' id='whistoriaoculto_liquidar'>
				<input type='hidden' id='wingresooculto_liquidar'>
				<input type='hidden' id='wcodigoturnooculto_liquidar'>
				<input type='hidden' id='wfechacargooculto_liquidar'>
				<input type='hidden' id='wperfiloculto'>
				<br>
				<table id='tabledatosBasicos'width=''>
					<tr>
						<td class='encabezadoTabla' style="font-size : 10pt;width:180px;" ><b>Cedula</b></td>
						<td class='encabezadoTabla' style="font-size : 10pt;width:270px;" ><b>Nombre</b></td>
						<td class='encabezadoTabla' style="font-size : 10pt;width:80px;" ><b>Turno</b></td>
						<td class='encabezadoTabla' style="font-size : 10pt;width:225px;" ><b>Descripci&oacute;n</b></td>
					</tr>
					<tr>
						<td class='fila1' style="font-size : 8pt;"><input type='text' disabled='disabled' id='wcedula_liquidar'></td>
						<td class='fila1' style="font-size : 8pt;"><input type='text' size='30' disabled='disabled' id='wnombrepaciente_liquidar'></td>
						<td class='fila1' style="font-size : 8pt;"><span style="display:none;" id='spn_wcentro_costos'></span> <span style="font-weight:bold;" id="wturno_liquidar"></span></td>
						<td class='fila2' style="font-size : 8pt;" rowspan="3"><span id="desc_cx_liquidar"></span></td>
					</tr>
					<tr>
						<td class='encabezadoTabla' style="font-size : 10pt;"><b>Historia</b></td>
						<td class='encabezadoTabla' style="font-size : 10pt;"><b>Ingreso</b></td>
						<td class='encabezadoTabla' style="font-size : 10pt;"><b>Quir&oacute;fano</b></td>
					</tr>
					<tr>
						<td class='fila1' style="font-size : 8pt;"><span style="font-weight:bold;font-size:12pt;" id='mercado_liquidar_unix_historia'></span></td>
						<td class='fila1' style="font-size : 8pt;"><span style="font-weight:bold;font-size:12pt;" id='mercado_liquidar_unix_ingreso'></span></td>
						<td class='fila1' style="font-size : 8pt;"><span style="" id='spn_wquirofano'></span></td>
					</tr>
				</table>
				<br>
				<div  align='center' id='divmercado_liquidar' >
				</div>
			</div>
			<br />
	        <br />
	        <table align='center'>
	            <tr><td align="center" colspan="9"><input type="button" value="Cerrar Ventana" onclick="cerrarVentanaPpal();"></td></tr>
	        </table>
			<br />
	        <br />
	        <div id="div_wcentro_costos" style="display:none;">
	        	<table style="text" align="center">
					<tr>
						<td style="text-align:center;" class="encabezadoTabla" >Seleccione un centro de costos para continuar</td>
					</tr>
					<tr>
						<td style="text-align:center;" class="fila1" >
				        	<select name="wcentro_costos" id="wcentro_costos" onchange="liquidarMercadoUnixModal();">
				        		<?=$opstions_cco_cir?>
				        	</select>
						</td>
					</tr>
	        	</table>
	        </div>
			<div id='divHistoriaPaf'>
			</div>
	        <div id='msjEspere' name='msjEspere' style='display:none;'>
			<br /><img src='../../images/medical/ajax-loader5.gif'/><br /><br />Por favor espere un momento ... <br /><br />
			</div>
	        <!-- <div id="dialog_error_msj" style="display:none;"></div> -->
	        <div id="dialog_password_borrar" style="display:none;text-align:center;">
	        	Escriba su contrase&ntilde;a matrix: <input type="password" id="pass_borrar_mercado" value="">
	        </div>
			<div style='display:none' id='fechacambioresponsable'><center><input type='hidden' id='idhistoriapaf'><input type='hidden' id='idingresopaf'><br><table ><tr ><td class='fila1'>Cambio de responsable desde</td><td class='fila2'><input type='text' id='datofechacambioresponsable'  ></td><td class='fila2'><input type='button' value='Fecha de ingreso' onclick='traerfechaingresoPaf()' ></td></tr><tr ><td class='fila1'>Texto que viajara a la bitacora:</td><td colspan='2' class='fila2'><textarea  cols='40' rows='1' id='textareapaf'></textarea></td></tr></table><br><input type='button' value='Grabar' onclick='grabarresponsablepaf()'></center></div>
	</BODY>
<!--=====================================================================================================================================================================
	F I N   B O D Y
=====================================================================================================================================================================-->
	</HTML>
	<?php
//=======================================================================================================================================================
//	F I N  E J E C U C I O N   N O R M A L
//=======================================================================================================================================================
}
}//Fin de session
?>