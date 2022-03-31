<?php
include_once("conex.php");
echo "INICIO script: ".date("Y-m-d H:i:s")."<br>";

/*
 ****************** INGRESO Y ALTA DE PACIENTES EN MATRIX DESDE UNIX **************************
 ****************************** DESCRIPCI�N ***************************************************
 * Consulta los pacientes activos en Unix e inactivos en Matrix y registra su ingreso en Matrix
 * Consulta los pacientes inactivos en Unix y ACtivos en Matrix y registra su egreso en matrix
 * Para registrar el ingreso y/o egreso de estos pacientes en Matrix primero se hace una serie de
 * validaciones que permiten verificar la acci�n a ejecutar
 *************************************************************************************************
 * Autor: John M. Cadavid. G.
 * Fecha creacion: 2011-02-16
 *************************************************************************************************
 * MODIFICACIONES
 *************************************************************************************************
 * 2021-12-15 Daniel CB.
 * 			  Se  comentan lineas con el parametro wemp_pmla sobrescrito.
 * 
 *************************************************************************************************
 * 2016-0-25   Jonatan Lopez
 *			   Se agtrega actualizacion de la cedula y tipo de cedula en la tabla cliame_000100, en la misma parte donde se actualiza 
			   la cedula de la tabla root_000037.
 *************************************************************************************************
 * 2016-07-15  Edwar Jaramillo:
 *             Las funciones actualizarMedicamentos, actualizarCargosIngresosInactivosUnix, recuperarTurnosDeUrgencias, se pasan a un nuevo archivo
 *   		   en include/root/kron_procesos_ERP.php, para que queden en un cron independiente al que carga-valida los pacientes unix matrix,
 *   		   se present� un problema con un valor nulo en pacientes unix que fren� todos los dem�s procesos por estar en el mismo cron.
 *************************************************************************************************
 * 2016-06-16  Edwar Jaramillo:
 *             Actualizar ingreso correcto cargos y RIPS en unix de insumos que desde matrix se grabaron a un ingreso inactivo pero
 *             que el integrador grab� a unix con el ingreso activo en ese momento de la grabaci�n a unix., para este procesos se crearon las funciones
 *             fn> queryCargoUnix
 *             fn> actualizarConsecutivosRipsCargos
 *             fn> consultarCargoInsumoPorLinea
 *             fn> consultaCargosUnix
 *             fn> registroLogError
 *             fn> actualizarCargosIngresosInactivosUnix
 *************************************************************************************************
 * 2016-04-11  Jerson Andres Trujillo . Se agrega la funcion recuperarTurnosDeUrgencias().
 *			   Que busca los pacientes de urgencias que se le hayan borrado el turno, para volver a asign�rselo.
 *************************************************************************************************
 * 2016-01-20  Felipe Alvarez Sanchez . Se modifica la funcion  ActualizarMedicamentos en su flujo ppal , con el fin de optimizar
 *             el procedimiento e ir menos a unix
 *
 *************************************************************************************************
 * 2015-12-21 camilo zz se modifica el query que consulta los pacientes activos en mx para que verifique que dichos ingresos ya existan en unix, mediante
 *            la verificaci�n del campo ingunx de la tabla 101
 *
 **************************************************************************************************
 * 2015-05-14 - Se actualiza el responsable del paciente si en unix es diferente al de matrix, ademas se actualiza la tabla cliame_000101 en el responsable y
 * la cliame_000205 en las fechas de terminacion e inicio de responsable. Jonatan Lopez
 *************************************************************************************************
 * 2015-03-25 - Se agrega la funcion ActualizarMedicamentos esto con el fin de actualizar los medicamentos (facturable o no facturable)
 segun lo que se 	grabo en matrix debe quedar en unix
 *************************************************************************************************
 * 2014-12-29 - Se valida la liberacion de la cama si el paciente tiene ocupada una de tipo cubiculo, si es asi, la habitacion debe quedar
				disponible inmediatamente, ya que quedaba bloqueada para y no se podia reasignar. Jonatan
 *************************************************************************************************
 * 2013-12-19 - Se cambia el campo de la tabla pacing por pacnum, ya que pacnum es el ingreso y pacing es la fecha de ingreso, en la funcion altaPacienteUrgencias
 *				Se agrega en el GROUP BY el ingreso, para que tenga en cuenta historias con varios ingresos activos, osea
 * 					con uniald en off, esto permitira trabajar con los dos ingresos en el arreglo $listados, en la funcion agendaPacientesUrgencias.
 *				Se aumenta el tiempo de borrado de datos en la tabla log_agenda a 30 dias, estaba en 15 dias, en la funcion agendaPacientesUrgencias.
 *************************************************************************************************
 * 2013-09-09 - En la funci�n altaPacientesUrgencias, cando se valida la no alta del paciente si �ste tiene conducta,
 *				se agrega la condici�n ($fecha_egreso==$fecha) comparando que la fecha de egreso en unix sea igual a hoy,
 *				de este modo los pacientes con fecha de egreso en Unix del d�a anterior, asi tengan conducta asignada, s� ser�n dados de alta
 *************************************************************************************************
 * 2013-08-20 - En la funci�n altaPacienteUrgencias se cambia la validaci�n para pacientes que est�n en proceso de traslado, as�:
 * 				Si el paciente est� en cirugia y tiene una antiguedad de m�s de 2 d�as. Se puede dar de alta asi est� en proceso de traslado
***********************************************************************************************
 * 2013-05-22 - Se ha modificado el programa agenda_urgencias_por_especialidad.php para crear este script
 * 				asi ya el programa de agenda urgencias solo se encarga de consultar, mostrar y permitir
 *				operaciones manuales sobre los pacientes activos y dados de alta en urgencias
 *				Mientras que este script se encargar� de hacer las consultas en Unix y comparaci�n con Matrix
 *				para dar de alta y/o ingresar los pacientes que est�n activos o no en Unix seguiendo varias
 *				reglas de validaci�n que ya est�n incorporadas en el script.
 *				Este script se ejecutar� por medio de un cron job.
 *************************************************************************************************
*/



 /***********************************************************************************************************************
 * LOS SIGUIENTES CAMBIOS CORRESPONDEN CUANDO ESTE PROCESO SE EJECTABA EN EL PROGRAMA DE ASIGNACI�N DE M�DICO URGENCIAS *
 ************************************************************************************************************************/
/*
 *************************************************************************************************
 * 2013-05-10 - En las funciones muertePacienteUrgencias y altaPacienteUrgencias se agreg� la consulta para saber
 * 				si el cco de ingreso del paciente es hospitalario (se busca en la tabla 000022 de historia clinica electronica (Mtrcci) )
 *				y no tiene cco anterior registrado, (se busca en la tabla 000018 de movimiento hospitalario (Ubisan) )
 * 				Si es as� no se debe registrar el egreso en la tabla 000033 de movimiento hospitalario
 * 				Esto porque hubo un paciente al que le registraron por error cco de ingreso 1180 en vez de poner 1800
 *				lo cual hizo que al dar de alta al paciente se afectara los indicadores de egresos de cco hospitalarios - Mario Cadavid
 *************************************************************************************************
 * 2013-04-25 - Cuando se da de alta o muerte, se cancela la solicitud de dieta con la funcion
 *				cancelar_pedido_alimentacion - Frederick Aguirre
 *************************************************************************************************
 * 2013-04-23 - En la funci�n activarPacienteUrgencias se agreg� la asignaci�n Mtrmed='' en el query de actualizaci�n
 * 				de la tabla 000022 de historia clinica electronica. Esto porque cuando un m�dico ya habia tomado un paciente
 *				y �ste es dado de alta, cuando se reactiva el paciente ningun otro medico lo pod�a tomar pues quedaba inactivo
 *				el bot�n Ir a historia en el programa de sala de espera.
 *************************************************************************************************
 * 2013-03-27 - En la funci�n actualizarPacientesUnix se incluy� la consulta a la tabla insercco de Unix cuando
 * 				no se encuentra la relaci�n de cco - servicio en la tabla 11 de movimiento hospitalario
 *************************************************************************************************
 * 2013-03-21 - En la funci�n activarPacienteUrgencias se agreg� Ubialp='off' en el query de actualizaci�n de la tabla 000018 de movimiento hospitalario
 *              Esto porque se solicit� que cuando se re-active un alta se quite el alta en proceso si la ten�a
 *************************************************************************************************
 * 2013-03-12 - Se modifica la funcion asignarEspecialidadUrgencias para que al momento de seleccionar la especialidad Ingreso Directo el paciente
 *              quede en conducta observacion, especialidad asociada medicina general, nivel de triage 2 y sin medico asociado. - Jonatan Lopez
 *************************************************************************************************
 * 2013-03-06 - En la funci�n consultarPacienteUnix se adicion� los campos direccion y municipio a la clase paciente
 *				En las funciones actualizarResponsablePaciente e insertarResponsablePaciente se adicion� la grabaci�n
 *				en los nuevos campos Ingdir e Ingmun de la tabla 000016 de movimiento hospitalario, grabaci�n que se hace
 *				desde los campos direccion y municipio a la clase paciente. - Mario Cadavid
 * *************************************************************************************************
 * 2013-02-26 - Se agrega la funcion BorrarAltasMuertesAntesDeAgregarNueva que elimina las altas y muertes de un paciente y actualiza el indicador
 *				Esto para garantizar que un paciente solo tenga una alta o una muerte en movhos33
 *				Se agregan condiciones a dos querys que eliminaban todos los egresos del paciente de movhos33 - Frederick Aguirre
 *************************************************************************************************
 * 2013-02-22 - En las funciones reasignarPacienteUrgencias y altaPacienteurgencias se agreg� una consulta para definir
 *				si los registros de historia e ingreso que se van a borrar tiene registros de movimiento hospitalario, si los tiene,
 * 				no se borra. En la funci�n altaPacieteUrgencias esto aplica cuando $tipo_alta == "borrado" - Mario Cadavid
 *************************************************************************************************
 * 2013-02-18 - En las funciones altaPacienteUrgencias y muertePacienteUrgencias ya no se modifican los campos
 *				Mtrtra y seguridad en la tabla hce_000022, tampoco se modifica los campos Ubialp, Ubifap y Ubihap
 *				en la tabla movhos_000018. En la funci�n activarPacienteUrgencias solo se dej� modificando los campos
 *				Ubimue='off', Ubiald='off', Ubifad='0000-00-00', Ubihad='00:00:00', Ubiuad=''; es decir solo lo que tenga
 *				que ver con el alta.  - Mario Cadavid
 *				En la funci�n borraIngresosMayores se agreg� una consulta para definir si los registros de historia e ingreso
 *				que se van a borrar tiene registros de movimiento hospitalario, si los tiene, no se borra. Esto porque se present�
 *				la inactivaco�n en unix de una histor�a cuyo paciente estaba ya hospitalizado, lo que hizo que el sistema tomara
 *				como mayor el ingreso actual y borrara sus registros, volviendolos a ingresar cuando reactivaron la historia en unix
 *				pero en el reingreso se perdieron los datos de movimiento hospitalario - Mario Cadavid
 *************************************************************************************************
 * 2013-02-08 - Se adicion� la funci�n obtenerRegistrosFila que permite almacenar todos los datos de una fila
 *				de la base de datos en una cadena tipo string. Esta funci�n sirve para guardar en la tabla
 * 				log_agenda los datos de una fila de la base de datos antes de �sta ser actualizada o borrada.
 *			 	Estos se debi� hacer porque se borr� el ingreso de 4 historias que estaban activas y en hospitalizaci�n,
 *				es decir, con registros en la tabla movhos_000020. Guardando el dato de la fila antes de ser borrada
 *  			se podr� saber porque se borr� y determinar si alguien le di� de alta o cambi� alg�n dato que hace
 *				que este programa no tome la historia como activa en Matrix - Mario Cadavid
 *************************************************************************************************
 * 2013-02-04 - Al seleccionar especialidad emergencia tambien se registra la fecha y hora de triage - Jonatan Lopez
 *************************************************************************************************
 * 2013-01-31 - En la asignacion de especialidad se asocia al paciente Nivel 1 de triage si selecciona
 *				emergencia, ademas al seleccionar esta especialidad se iniciara la consulta- Jonatan Lopez
 *************************************************************************************************
 * 2012-12-20 - En la funci�n altaPacienteUrgencias se incluyo la actualizaci�n de tabla 000020 de movimiento
 * 				hospitalario, de modo que desocupe la habitaci�n, si la tiene, del paciente dado de alta - Mario Cadavid
 *************************************************************************************************
 * 2012-12-04 - Se agreg� la columna M�dico en la lista de Pacientes Activos - Mario Cadavid
 *************************************************************************************************
 * 2012-07-18 - Se adicion� la grabaci�n del centro de costo de ingreso (Mtrcci) en la tabla hce_000022 - Mario Cadavid
 *************************************************************************************************
 * 2012-06-14 - Se quitaron espacios en codigo html que pudieran afectar la respuesta ajax ya que las funciones javascript
 *				validan si la respuesta es igual a "ok" para definir si muestra un mensaje de advertencia o no y estaba
 *				llegando ok pero con un salto de linea - Mario Cadavid
 *************************************************************************************************
 * 2012-04-11 - Se unieron los dos scripts (agenda_urgencias_por_especialidad.php y auxAgendaUrgencias_por_especialidad.php) en uno solo
 *				Se modifico la consulta principal en la funci�n consultarPacienteUnix para que no se frenara
 *				el script si existe algun registro Nulo, sto por el cambio al nuevo servidor - Mario Cadavid
 *************************************************************************************************
 * 2012-03-16 - Se adicion� una condici�n m�s antes de borrar un ingreso, de modo que primero se consulte
 *				inpaci y si el ingreso a borrar es mayor que el ingreso actual en inpaci se borra (if($ingreso>$ing_act))
 *				sino se da alta normal - Mario Cadavid
 *************************************************************************************************
 * 2012-02-20 - En la funci�n "actualizarDatosPacientes" se adicion� el llamado a la funci�n
 *				"borrarHistoriaDiferenteUnix" ya que cuando en unix se hacia un cambio despu�s de
 *				haber ingresado el paciente y esto implicaba datos duplicidad de datos en un registro ya existente
 *				en root_000037 el sistema no borraba este registro y causaba error de clave duplicada - Mario Cadavid
 *************************************************************************************************
 * 2012-01-24 - En las funciones "actualizarAltaPacientesUnix" y "actualizarPacientesUnix" se modific�
 *				el query de consulta de pacientes activos en Unix de modo que solo consulte inpac
 *				y no las dem�s tablas (insercco,inemp) ya que no se necesitan datos de estas
 *				y al consultarlas se estabn trayendo algunas historias duplicadas - Mario Cadavid
 *************************************************************************************************
 *************************************************************************************************
 * 2012-01-19 - En la funci�n "altaPacienteUrgencias" se agreg� la validaci�n de la fecha de egreso
 *				en Unix. Si fecha de egreso en unix es igual a la fecha actual no se da de alta
 *				Se cre� la funci�n "actualizarDatosPacientes" que actualiza los datos de los pacientes
 *				activos en cl�nica, siempre y cuando se encuentren estos datos diferentes en Unix - Mario Cadavid
 *************************************************************************************************
 * 2011-11-29 - Se modific� la funci�n ingresarPacientesUrgencias para que tenga en cuenta cuando
 *				la historia y/o c�dula en matrix no corresponde con las de Unix. Para esto tambi�n
 *				se creo la funci�n borrarHistoriaDiferenteUnix  - Mario Cadavid
 *************************************************************************************************
 * 2011-11-28 - Se agreg� la condici�n si mysql_affected_rows() antes de grabar en log_agenda para garantizar
 *				que si se ejecuto la acci�n que se graba en la tabla de log_agenda - Mario Cadavid
 *************************************************************************************************
 * 2011-11-27 - Se agreg� grabaci�n en la tabla de log_agenda para todas las acciones que se
 *				ejecuten en el sistema, no solo para las de borrado como estaba
 *				En la funci�n borraIngresosMayores se cambi� Ubiing >= ".$ingreso." por
 *				Ubiing*1 >= ".$ingreso." para que hiciera la comparaci�n correctamente - Mario Cadavid
 *************************************************************************************************
 * 2011-11-25 - Cuando se llama la funci�n borraIngresosMayores, se estaba llevando el ingreso de Matrix
 *				se cambi� para que lleve el ingreso de unix - Mario Cadavid
 *************************************************************************************************
 * 2011-11-23 - En el Query de la funci�n obtenerCcoMatrix se adicion� la condici�n de que el
 *				centro de costo sea de ingreso (ccoing='on') para que no se ingresen pacientes a
 *				centros de costos que no son de ingreso - Mario Cadavid
 *************************************************************************************************
 * 2011-11-11 - Se agreg� la columna de Afinidad del paciente tanto en la lista de pacientes activos
 *				como en la lista de pacientes inactivos - Mario Cadavid
 *************************************************************************************************
 * 2011-10-31 - Se modificaron las funciones insertarIngresoPaciente y actualizarIngresoPaciente de modo que
 *				cuando la adici�n o edici�n en la tabla root_000037 saque error por clave duplicada,
 *				borre los registros duplicados e inserte o actualice los datos del paciente que se traen desde Unix
 *				Se creo la funci�n esCirugia para verificar si el centro de costo del paciente es cirugia de modo que en
 *				proceso de traslado quede en 'on', es decir, poner Ubiptr de la tabla movhos_000018 en 'on' - Mario Cadavid
 *************************************************************************************************
 * 2011-10-27 - La consulta de pacientes de Unix se hizo general para que se consulte e ingresen todos los
 *				pacientes activos desde unix sin importar el servicio pues se decidi� que desde este script
 *				de urgencias se ingresen todos los pacientes activos de Unix e igual para la alta automatica
 *				de los pacientes que ya no esten en Unix y no tengan conducta asociada - Mario Cadavid
 *************************************************************************************************
 * 2011-10-26 - Se adicion� la funci�n borraIngresosMayores y se modific� la funci�n ingresarPacientesUrgencias
 *				esto para preveer la situaci�n en la que una historia o ingreso es reasignado en Unix
 *				de modo que en matrix se borren los ingresos mayores a los de Unix e igual se actualicen
 *				los datos en las tablas root_000036 y root_000037 en caso de un cambio de c�dula para la historia - Mario Cadavid
 *************************************************************************************************
 * 2011-08-25 - Cuando se escanea para ingreso de pacientes desde Unix se adicion� la funci�n
 *				actualizarDatosPacienteTablaUnica para que actualice los datos de la tabla root_000036
 *				con los que se traen de Unix - Mario Cadavid
 *************************************************************************************************
 * 2011-08-17 - En la consulta de m�dicos se incluyeron las condiciones para meduma diferente de '' y 'NO APLICA' - Mario Cadavid
 *************************************************************************************************
 * 2011-08-11 - Se agrego al LOG que guarde tambien el borrado de la tabla  movhos_000016
 *				Se modific� las consultas que agregan pacientes nuevos a la agenda para que traiga de
 *				Unix no solo los pacientes a partir de ayer sino todos los que esten en Unix
 *				asignados a urgencias, ver campos comentados asi: 	//  AND pacfec >= '".$ayer."'"; - Mario Cadavid
 *************************************************************************************************
 * 2011-07-06 - Se creo una tabla para LOG (log_agenda) para guardar las acciones de borrado de la tabla movhos_000018 - Mario Cadavid
 *************************************************************************************************
 * 2011-06-08 - Se cambio la asignaci�n por m�dico a asignaci�n por especialidad - Mario Cadavid
 *************************************************************************************************
 * 2011-05-13 - Se cambio el query para consultar m�dico asignado en la funci�n agendaUrgencias
 *				ya que estaba tomando pacientes con m�dico en blanco "", y mostraba m�dico
 *				con c�digo en blanco ""
 *				Se cambi� la funci�n actualizarAltaPacientesUnix ya que no se estaba recorriendo
 *				el arreglo de forma correcta, se cambi� la funci�n while ($j < count ($altas_unix))
 *				por foreach ($altas_unix as $j => $value) - Mario Cadavid
 *************************************************************************************************
 * 2011-04-28 - Cuando es alta autom�tica, se cambio la funci�n <altaPacienteUrgencias>
 *				para que verifique si en Unix sigue activa la historia, si es asi no le da alta
 *				Se activaron los checbox de actas y muertes para conductas de alta
 *				Se inactiva checbox de muerte si conducta es alta y viceversa - Mario Cadavid
 *************************************************************************************************
 * 2011-04-25 - Modificaci�n en el ingreso de pacientes a urgencias, se quito el campo
 *				de texto donde se ingresaba la historia cl�nica, ya se toma de Unix los
 *				pacientes en urgencias actualiz�ndose la lista autom�ticamente - Mario Cadavid
 *************************************************************************************************
 * 2011-03-04 - Modificaci�n en el proceso de ingreso del paciente a urgencias para validar los siguientes casos:
 * 				Si un usuario ya est� en movhos 18 pero aun no esta en hce 22, registrarlo en hce 22
 * 				antes se asumia como ya ingresado y no se registraba en hce 22
 *				Validar ingreso de paciente no solo por historia sino tambien por n�mero de ingreso, por ejemplo:
 *				si un usuario est� registrado con ingreso 1 y vuelven y lo entran y tiene ya en UNIX ingreso 2
 *				se debe dar de alta automaticamente el ingreso 1 y adicionarlo a la agenda de urgencias con ingreso 2
 *				Validaci�n al activar un paciente dado de alta, si ya est� activo con otro ingreso no lo deja activar
 *				En reasignar si el paciente tiene mas de un ingreso no deja reasiganar su n�mero de historia - Mario Cadavid
 *************************************************************************************************
 * 2011-02-23 - Adici�n de try catch al ejecutar blockUI debido a que en algunas versiones viejas de IE sacaba un error
 * 				y no dejaba ejecutar la p�gina. Tambi�n se adicion� el evento onload al final del javascript - Mario Cadavid
 *************************************************************************************************
 * 2011-02-22 - Adici�n de columnas Activar y Reasignar historia para pacientes dados de alta - Mario Cadavid
 */


 /***************************************************************************************
 * LOS SIGUIENTES CAMBIOS CORRESPONDEN CUANDO EL PROGRAMA ESTABA DIVIDIDO EN 2 SCRIPTS	*
 ****************************************************************************************/
 /*
 **************************** AUXILIAR DE AGENDA URGENCIAS ************************************
 ****************************** DESCRIPCI�N ***************************************************
 * Contiene las funciones principales que usa el script agenda_urgencias_por_especialidad.php
 * Estas funciones se llaman desde AJAX
 *************************************************************************************************
 * Autor: John M. Cadavid. G.
 * Fecha creacion: 2011-02-16
 *************************************************************************************************
 * MODIFICACIONES
 *************************************************************************************************
 * 2012-02-20 - En la funci�n "actualizarDatosPacientes" se adicion� el llamado a la funci�n
 *				"borrarHistoriaDiferenteUnix" ya que cuando en unix se hacia un cambio despu�s de
 *				haber ingresado el paciente y esto implicaba datos duplicidad de datos en un registro
 *				ya existente en root_000037 el sistema no borraba este registro y causaba error de clave duplicada
 *************************************************************************************************
 * 2012-01-24 - En las funciones "actualizarAltaPacientesUnix" y "actualizarPacientesUnix" se modific�
 *				el query de consulta de pacientes activos en Unix de modo que solo consulte inpac
 *				y no las dem�s tablas (insercco,inemp) ya que no se necesitan datos de estas
 *				y al consultarlas se estabn trayendo algunas historias duplicadas
 *************************************************************************************************
 * 2012-01-19 - En la funci�n "altaPacienteUrgencias" se agreg� la validaci�n de la fecha de egreso
 *				en Unix. Si fecha de egreso en unix es igual a la fecha actual no se da de alta
 *				Se cre� la funci�n "actualizarDatosPacientes" que actualiza los datos de los pacientes
 *				activos en cl�nica, siempre y cuando se encuentren estos datos diferentes en Unix
 *************************************************************************************************
 * 2011-11-29 - Se modific� la funci�n ingresarPacientesUrgencias para que tenga en cuenta cuando
 *				la historia y/o c�dula en matrix no corresponde con las de Unix. Para esto tambi�n
 *				se creo la funci�n borrarHistoriaDiferenteUnix
 *************************************************************************************************
 * 2011-11-28 - Se agreg� la condici�n si mysql_affected_rows() antes de grabar en log_agenda
 *				para garantizar que si se ejecuto la acci�n que se graba en la tabla de log_agenda
 *************************************************************************************************
 * 2011-11-27 - Se agreg� grabaci�n en la tabla de log_agenda para todas las acciones que se
 *				ejecuten en el sistema, no solo para las de borrado como estaba
 *				En la funci�n borraIngresosMayores se cambi� Ubiing >= ".$ingreso." por
 *				Ubiing*1 >= ".$ingreso." para que hiciera la comparaci�n correctamente
 *************************************************************************************************
 * 2011-11-25 - Cuando se llama la funci�n borraIngresosMayores, se estaba llevando el ingreso de Matrix
 *				se cambi� para que lleve el ingreso de unix
 *************************************************************************************************
 * 2011-11-23 - En el Query de la funci�n obtenerCcoMatrix se adicion� la condici�n de que el
 *				centro de costo sea de ingreso (ccoing='on') para que no se ingresen pacientes a
 *				centros de costos que no son de ingreso
 *************************************************************************************************
 * 2011-11-11 - Se agreg� la columna de Afinidad del paciente tanto en la lista de pacientes activos
 *				como en la lista de pacientes inactivos
 *************************************************************************************************
 * 2011-10-31 - Se modificaron las funciones insertarIngresoPaciente y actualizarIngresoPaciente de modo que
 *				cuando la adici�n o edici�n en la tabla root_000037 saque error por clave duplicada,
 *				borre los registros duplicados e inserte o actualice los datos del paciente que se traen desde Unix
 *				Se creo la funci�n esCirugia para verificar si el centro de costo del paciente es cirugia
 *				de modo que En proceso de traslado quede en 'on', es decir, poner Ubiptr de la tabla movhos_000018 en 'on'
 *************************************************************************************************
 * 2011-10-27 - La consulta de pacientes de Unix se hizo general para que se consulte e ingresen todos los
 *				pacientes activos desde unix sin importar el servicio pues se decidi� que desde este script
 *				de urgencias se ingresen todos los pacientes activos de Unix e igual para la alta automatica
 *				de los pacientes que ya no esten en Unix y no tengan conducta asociada
 *************************************************************************************************
 * 2011-10-26 - Se adicion� la funci�n borraIngresosMayores y se modific� la funci�n ingresarPacientesUrgencias
 *				esto para preveer la situaci�n en la que una historia o ingreso es reasignado en Unix
 *				de modo que en matrix se borren los ingresos mayores a los de Unix e igual se actualicen
 *				los datos en las tablas root_000036 y root_000037 en caso de un cambio de c�dula para la historia
 *************************************************************************************************
 * 2011-08-25 - Cuando se escanea para ingreso de pacientes desde Unix se adicion� la funci�n
 *				actualizarDatosPacienteTablaUnica para que actualice los datos de la tabla root_000036
 *				con los que se traen de Unix
 *************************************************************************************************
 * 2011-08-17 - En la consulta de m�dicos se incluyeron las condiciones para meduma diferente de '' y 'NO APLICA'
 *************************************************************************************************
 * 2011-08-11 - Se agrego al LOG que guarde tambien el borrado de la tabla  movhos_000016
 *				Se modific� las consultas que agregan pacientes nuevos a la agenda para que traiga de
 *				Unix no solo los pacientes a partir de ayer sino todos los que esten en Unix
 *				asignados a urgencias, ver campos comentados asi: 	//  AND pacfec >= '".$ayer."'";
 *************************************************************************************************
 * 2011-07-06 - Se creo una tabla para LOG (log_agenda) para guardar las acciones de borrado de la tabla movhos_000018
 *************************************************************************************************
 * 2011-06-08 - Se cambio la asignaci�n por m�dico a asignaci�n por especialidad
 *************************************************************************************************
 * 2011-05-13 - Se cambio el query para consultar m�dico asignado en la funci�n agendaUrgencias
 *				ya que estaba tomando pacientes con m�dico en blanco "", y mostraba m�dico
 *				con c�digo en blanco ""
 *				Se cambi� la funci�n actualizarAltaPacientesUnix ya que no se estaba recorriendo
 *				el arreglo de forma correcta, se cambi� la funci�n while ($j < count ($altas_unix))
 *				por foreach ($altas_unix as $j => $value)
 *************************************************************************************************
 * 2011-04-28 - Cuando es alta autom�tica, se cambio la funci�n <altaPacienteUrgencias>
 *				para que verifique si en Unix sigue activa la historia, si es asi no le da alta
 *				Se activaron los checbox de actas y muertes para conductas de alta
 *				Se inactiva checbox de muerte si conducta es alta y viceversa
 *************************************************************************************************
 * 2011-04-25 - Modificaci�n en el ingreso de pacientes a urgencias, se quito el campo
 *				de texto donde se ingresaba la historia cl�nica, ya se toma de Unix los
 *				pacientes en urgencias actualiz�ndose la lista autom�ticamente.
 *************************************************************************************************
 * 2011-03-04 - Modificaci�n en el proceso de ingreso del paciente a urgencias para validar los siguientes casos:
 * 				Si un usuario ya est� en movhos 18 pero aun no esta en hce 22, registrarlo en hce 22
 * 				antes se asumia como ya ingresado y no se registraba en hce 22
 *				Validar ingreso de paciente no solo por historia sino tambien por n�mero de ingreso, por ejemplo:
 *				si un usuario est� registrado con ingreso 1 y vuelven y lo entran y tiene ya en UNIX ingreso 2
 *				se debe dar de alta automaticamente el ingreso 1 y adicionarlo a la agenda de urgencias con ingreso 2
 *				Validaci�n al activar un paciente dado de alta, si ya est� activo con otro ingreso no lo deja activar
 *				En reasignar si el paciente tiene mas de un ingreso no deja reasiganar su n�mero de historia
 *************************************************************************************************
 * 2011-02-23 - Adici�n de try catch al ejecutar blockUI debido a que en algunas versiones viejas de IE
 * 				sacaba un error y no dejaba ejecutar la p�gina. Tambi�n se adicion� el evento onload al final del javascript
 *************************************************************************************************
 * 2011-02-22 - Adici�n de columnas Activar y Reasignar historia para pacientes dados de alta
 *
 */

//Nivel de triage (nombre)
  function niveltriage($wtri, $wbasedatohce)
   {

	global $conex;


	$q =       " SELECT Trinom"
			 . "   FROM ".$wbasedatohce."_000040 "
			 . "  WHERE Tricod = '".$wtri."' "
             ."     AND triest = 'on'";
    $res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);

	return $row[0];

   }

 // Retorna el c�digo y nombre del centro de costos de urgencias
 function consultarCcoUrgencias()
 {
	global $wbasedato;
	global $conex;

	$q = "SELECT
			Ccocod, Cconom
		FROM
			".$wbasedato."_000011
		WHERE
			Ccourg = 'on'; ";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$filas = mysql_num_rows($res);

	$cco = new centroCostosDTO();

	if($filas > 0)
	{
		$fila = mysql_fetch_row($res);

		$cco->codigo = $fila[0];
		$cco->nombre = $fila[1];
	}
	return $cco;
 }

include_once("root/comun.php");
include_once("movhos/movhos.inc.php");
include_once("root/magenta.php");

/********************************************************************************************
****************************** INICIO APLICACI�N ********************************************
********************************************************************************************/
//$wbasedato = "";
$wactualiz = "2021-12-15";
$wemp_pmla = $_REQUEST['wemp_pmla'];

$wuser = "movhos";
$seguridad = "movhos";

//Variable para determinar la empresa
//if(!isset($wemp_pmla))
//{
//	$wemp_pmla = '01';
//}

	//Conexion base de datos Matrix
	$conex = obtenerConexionBD("matrix");

	$wbasedatohce = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$wcliame=consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");

	/*


	//Consulto el codigo y nombre del centro de costo de urgencias
	$ccoUrgencias = consultarCcoUrgencias();

	//Formulario (forma)
	echo "<form name='forma' action='' method='post'>";

	echo "<input type='hidden' name='wemp_pmla' value='".$wemp_pmla."'>";
	echo "<input type='hidden' name='wbasedato' value='".$wbasedato."'>";
	echo "<input type='hidden' name='wbasedatohce' value='".$wbasedatohce."'>";
	echo "<input type='hidden' name='codCco' value='".$ccoUrgencias->codigo."'>";
	echo "<input type='hidden' name='wseguridad' id='wseguridad' value='".$wuser."'>";
	echo "<input type='hidden' name='conex' id='conex' value='".$conex."'>";

	$codCco = $ccoUrgencias->codigo;
	$wseguridad = $wuser;

	echo "</form>";




$conex = obtenerConexionBD("matrix");
conexionOdbc($conex, $wbasedato, &$conexUnix, 'facturacion');

class medicosUrgencias
{
	var $codigo;
	var $nombre;
}

class especialidadesUrgencias
{
	var $codigo;
	var $nombre;
}

function consultar_tarifa($resp_nuevo){

	global $conex;

	// Busco el responsable del paciente en matrix.
	$resp_tar = "  SELECT Emptar
						FROM cliame_000024
					   WHERE Empcod = '".$resp_nuevo."'";
	$res_tar = mysql_query($resp_tar,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $resp_tar . " - " . mysql_error());
	$row_tar = mysql_fetch_array($res_tar);
	$tarifa_responsable = $row_tar['Emptar'];

	return $tarifa_responsable;

}


function consultar_responsable_diferente_unix_matrix($paciente, $datos_pacienteUnix){

	global $conex;

	$diferente = array('diferentes'=>'off', 'nuevo'=>'', 'anterior'=>'' );

	// Busco el responsable del paciente en matrix.
	$resp_matrix = "  SELECT Ingres
						FROM ".$wbasedato."_000016
					   WHERE Inghis = '".$paciente->historiaClinica."'
						 AND Inging = '".$paciente->ingresoClinica."' ";
	$resmatrix = mysql_query($resp_matrix,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $resp_matrix . " - " . mysql_error());
	$row_matrix = mysql_fetch_array($resmatrix);
	$responsable_matrix = $row_matrix['Ingres'];

	if($responsable_matrix != '' and $datos_pacienteUnix->numeroIdentificacionResponsable != ''){

		if(trim($responsable_matrix) != trim($datos_pacienteUnix->numeroIdentificacionResponsable)){

			$diferente['diferentes'] = 'on';
			$diferente['nuevo'] = trim($datos_pacienteUnix->numeroIdentificacionResponsable);
			$diferente['anterior'] = trim($responsable_matrix);

		}
	}



	return $diferente;


}


// Retorna un arreglo con los m�dicos actualmente asiganados a urgencias
function consultarMedicosUrgencias($wbasedato)
{
	global $conex;

	$q1=  "	SELECT Meduma, Medno1, Medno2, Medap1, Medap2 "
		 ."   FROM ".$wbasedato."_000048 "
		 ."  WHERE Medurg = 'on' "
		 ."	   AND Medest = 'on' "
		 ."    AND Meduma != '' "
		 ."    AND Meduma != ' ' "
		 ."    AND Meduma != 'NO APLICA' "
		 ."  ORDER BY Medno1, Medno2, Medap1, Medap2";
	$res1 = mysql_query($q1,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q1 . " - " . mysql_error());
  	$num1 = mysql_num_rows($res1);

  	$coleccion = array();

  	if ($num1 > 0 )
  	{
  		for ($i=1;$i<=$num1;$i++)
  		{
  			$med = new medicosUrgencias();
  			$row1 = mysql_fetch_array($res1);

  			$med->codigo = $row1[0];
  			$med->nombre = $row1[1]." ".$row1[2]." ".$row1[3]." ".$row1[4];

  			$coleccion[] = $med;
  		}
  	}
  	return $coleccion;
}

// Retorna un arreglo con las especialidades actualmente asiganadas a urgencias
function consultarEspecialidadesUrgencias($wbasedato)
{
	global $conex;

	$q1=  "	SELECT Espcod, Espnom "
		 ."   FROM ".$wbasedato."_000048, ".$wbasedato."_000044 "
		 ."  WHERE Medurg = 'on' "
		 ."    AND Medest = 'on' "
		 ."    AND Meduma != '' "
		 ."    AND Meduma != ' ' "
		 ."    AND Meduma != 'NO APLICA' "
		 ."	   AND Medesp = Espcod "
		 ."  GROUP BY Medesp ";
	$res1 = mysql_query($q1,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q1 . " - " . mysql_error());
  	$num1 = mysql_num_rows($res1);

  	$coleccion = array();

  	if ($num1 > 0 )
  	{
  		for ($i=1;$i<=$num1;$i++)
  		{
  			$med = new especialidadesUrgencias();
  			$row1 = mysql_fetch_array($res1);

  			$med->codigo = $row1[0];
  			$med->nombre = $row1[1];

  			$coleccion[] = $med;
  		}
  	}
  	return $coleccion;
}

/********************************************************************************************
* VERIFICA SI LA HISTORIA DEL PACIENTE SE ENCUENTRA REGISTRADA EN URGENCIAS DE DB UNIX		*
********************************************************************************************/
function consultarPacienteUnix($pacienteConsulta)
{
	global $conexUnix;
	$paciente = new pacienteDTO();

	$q = " SELECT pacnom, pacap1, pacap2, pacnum, pacfec, pachor, pachab, paccer, pacres, pactid, pacced, pacnac, pacsex, pachos, serccocco, pactel, pacdir, pacmun, pacemp
		     FROM inpac, insercco
		    WHERE pachis = '".$pacienteConsulta->historiaClinica."'
			  AND serccoser = pacser
			  AND pacap2 is not null
			  AND pachab is not null

			UNION

			SELECT pacnom, pacap1, ' ' AS pacap2, pacnum, pacfec, pachor, pachab, paccer, pacres, pactid, pacced, pacnac, pacsex, pachos, serccocco, pactel, pacdir, pacmun, pacemp
		     FROM inpac, insercco
		    WHERE pachis = '".$pacienteConsulta->historiaClinica."'
			  AND serccoser = pacser
			  AND pacap2 is null
			  AND pachab is not null

			UNION

		   SELECT pacnom, pacap1, pacap2, pacnum, pacfec, pachor, ' ', paccer, pacres, pactid, pacced, pacnac, pacsex, pachos, serccocco, pactel, pacdir, pacmun, pacemp
		     FROM inpac, insercco
		    WHERE pachis = '".$pacienteConsulta->historiaClinica."'
			  AND serccoser = pacser
			  AND pacap2 is not null
			  AND pachab is null

			UNION

			SELECT pacnom, pacap1, ' ' AS pacap2, pacnum, pacfec, pachor, ' ', paccer, pacres, pactid, pacced, pacnac, pacsex, pachos, serccocco, pactel, pacdir, pacmun, pacemp
		     FROM inpac, insercco
		    WHERE pachis = '".$pacienteConsulta->historiaClinica."'
			  AND serccoser = pacser
			  AND pacap2 is null
			  AND pachab is null
			  ";
	//		  AND pacser = '04'";			// 2011-10-27

	$rs = odbc_do($conexUnix,$q) or die (odbc_errormsg());
	//odbc_fetch_row($rs);

	if ($arr_paciente = odbc_fetch_array($rs))
	{

		$municipio = "";
		$tipo_responsable = "";

		//Busco el nombre del municipio
		$muncod = trim($arr_paciente['pacmun']);
		$qmun = "  SELECT munnom
					 FROM inmun
					WHERE muncod = '".trim($muncod)."'
				  ";
		$rsmun = odbc_do($conexUnix,$qmun) or die (odbc_errormsg());
		$arr_municipio = odbc_fetch_array($rsmun);
		$municipio = $arr_municipio['munnom'];

		// $numreg = odbc_num_rows($rsmun);
		// if($numemp>0)
			// $municipio = trim(odbc_result($rsmun,1));
		// else
			// $municipio = "";


		// Busco el tipo de responsable
		$codResponsable = trim($arr_paciente['paccer']);
		$qemp = "  SELECT emptip
					 FROM inemp
					WHERE empcod = '".$codResponsable."'
					  AND emptip is not null
				  ";
		$rsemp = odbc_do($conexUnix,$qemp) or die (odbc_errormsg());
		$arr_responsable = odbc_fetch_array($rsemp);
		$tipo_responsable = $arr_responsable['emptip'];

		// $numreg = odbc_num_rows($rsemp);
		// if($numreg>0)
			// $tipo_responsable = trim(odbc_result($rsemp,1));
		// else
			// $tipo_responsable = "";


		$nombre = explode(" ",trim($arr_paciente['pacnom']));
		$paciente->nombre1 = $nombre[0];

		if(isset($nombre[1]) && !isset($nombre[2]))
		{
			$paciente->nombre2 = $nombre[1];
		}
		elseif(isset($nombre[1]) && isset($nombre[2]))
		{
			$paciente->nombre2 = $nombre[1]." ".$nombre[2];
		}
		elseif(!isset($nombre[1]) && isset($nombre[2]))
		{
			$paciente->nombre2 = $nombre[2];
		}
		else
		{
			$paciente->nombre2 = "";
		}

		$paciente->apellido1 = trim($arr_paciente['pacap1']);
		$paciente->apellido2 = trim($arr_paciente['pacap2']);
		$paciente->historiaClinica = trim($pacienteConsulta->historiaClinica);
		$paciente->ingresoHistoriaClinica = trim($arr_paciente['pacnum']);
		$paciente->fechaIngreso = str_replace("/","-",trim($arr_paciente['pacfec']));
		$paciente->horaIngreso = str_replace(".",":",trim($arr_paciente['pachor'])).":00";
		$paciente->habitacionActual = "";
		$paciente->numeroIdentificacionResponsable = $codResponsable;
		$paciente->nombreResponsable = trim($arr_paciente['pacres']);
		$paciente->tipoDocumentoIdentidad = trim($arr_paciente['pactid']);
		$paciente->documentoIdentidad = trim($arr_paciente['pacced']);
		$paciente->fechaNacimiento = trim($arr_paciente['pacnac']);
		$paciente->genero = trim($arr_paciente['pacsex']);
		$paciente->deHospitalizacion = trim($arr_paciente['pachos']);
		$paciente->servicioActual = trim($arr_paciente['serccocco']);
		$paciente->tipoResponsable = $tipo_responsable;
		$paciente->telefono = trim($arr_paciente['pactel']);
		$paciente->direccion = trim($arr_paciente['pacdir']);
		$paciente->municipio = $municipio;
		$paciente->tipo_empresa = trim($arr_paciente['pacemp']);

		if(!isset($paciente->tipoResponsable))
		{
			$paciente->tipoResponsable = "02";
		}
		else
		{
			if($paciente->tipoResponsable == '' || empty($paciente->tipoResponsable))
			{
				$paciente->tipoResponsable = "02";
			}
		}
	}

	return $paciente;
}

// 2013-02-08
// Consulta los datos de una fila seg�n el query $qlog y convierte esta fila en un String
// separando cada campo por el caracter |
function obtenerRegistrosFila($qlog)
{
	global $conex;
	
	$datosFila = array();
	$reslog = mysql_query($qlog, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qlog . " - " . mysql_error());
	$rowlog = mysql_fetch_row($reslog);
	
	if(is_array($rowlog)){
	$datosFila = implode("|", $rowlog);
	}
	return $datosFila;
}

/********************************************************************************************
* FUNCIONES UTILIZADAS EN EL REGISTRO DEL PACIENTE QUE ENTRA A URGENCIAS					*
********************************************************************************************/

//Existe un registro del paciente en la tabla 36 de root
function existeEnTablaUnicaPacientes($paciente)
{
	global $conex;

	$esta = false;

	$q = " SELECT *
		  	 FROM root_000036
		    WHERE Pacced = '".$paciente->documentoIdentidad."'
			  AND Pactid = '".$paciente->tipoDocumentoIdentidad."'";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . "-en el query: " . $q . "-" . mysql_error());
	$filas = mysql_num_rows($res);

	if($filas > 0)
	{
		$esta = true;
	}
	return $esta;
}

//Ingresa los datos en la tabla 36 de root
function insertarPacienteTablaUnica($paciente,$seguridad)
{
	global $conex;

	$fechaLog = date('Y-m-d');
	$horaLog = date('H:i:s');

	$q = "INSERT INTO
			root_000036
				(medico,fecha_data,hora_data,Pacced,Pacno1,Pacno2,Pacap1,Pacap2,Pacnac,Pacsex,Pactid,Seguridad)
			VALUES
				('root','$paciente->fechaIngreso','$paciente->horaIngreso', '$paciente->documentoIdentidad', '$paciente->nombre1', '".$paciente->nombre2."', '".$paciente->apellido1."', '".$paciente->apellido2."', '".$paciente->fechaNacimiento."', '".$paciente->genero."', '$paciente->tipoDocumentoIdentidad', 'C-".$seguridad."' )";
	$err=mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	$num_affect = mysql_affected_rows();
	if($num_affect>0)
	{
		//Guardo LOG de grabaci�n en tabla root_000036
		$q = "	INSERT INTO log_agenda
								  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera)
						   VALUES
								  ('".$fechaLog."', '".$horaLog."', '".$paciente->documentoIdentidad."', '".$paciente->tipoDocumentoIdentidad."', 'Grabacion tabla root_000036', '".$seguridad."', 'Auto')";
		$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	}
}

//Actualiza documento del paciente en la tabla 36 de root
function actualizarDocumentoPacienteTablaUnica($pacienteAnterior, $pacienteNuevo)
{
	global $conex;

	$fechaLog = date('Y-m-d');
	$horaLog = date('H:i:s');

	// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
	$qlog = " SELECT *
				FROM root_000036
			   WHERE Pacced = '".$pacienteAnterior->documentoIdentidad."'
				 AND Pactid = '".$pacienteAnterior->tipoDocumentoIdentidad."' ";
	$registrosFila = obtenerRegistrosFila($qlog);

	$q = "UPDATE
			root_000036
		SET
			Pacced = '".$pacienteNuevo->documentoIdentidad."',
			Pactid = '".$pacienteNuevo->tipoDocumentoIdentidad."'
		WHERE
			Pacced = '".$pacienteAnterior->documentoIdentidad."'
			AND Pactid = '".$pacienteAnterior->tipoDocumentoIdentidad."' ";
	$err1=mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	$num_affect = mysql_affected_rows();
	if($num_affect>0)
	{
		//Guardo LOG de actualizaci�n en tabla root_000036
		$q = "	INSERT INTO log_agenda
								  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
						   VALUES
								  ('".$fechaLog."', '".$horaLog."', '".$pacienteAnterior->documentoIdentidad."', '".$pacienteAnterior->tipoDocumentoIdentidad."', 'Actualizacion tabla root_000036', 'root', 'Nuevo documento ".$pacienteNuevo->tipoDocumentoIdentidad." ".$pacienteNuevo->documentoIdentidad."', '".$registrosFila."')";
		$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	}
}

//Actualiza datos del paciente en la tabla 36 de root
function actualizarDatosPacienteTablaUnica($pacienteAnterior, $pacienteNuevo)
{
	global $conex;

	$fechaLog = date('Y-m-d');
	$horaLog = date('H:i:s');

	// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
	$qlog = " SELECT *
				FROM root_000036
			   WHERE Pacced = '".$pacienteNuevo->documentoIdentidad."'
				 AND Pactid = '".$pacienteNuevo->tipoDocumentoIdentidad."'";
	$registrosFila = obtenerRegistrosFila($qlog);

	$q = "UPDATE
			root_000036
		SET
			Pacno1 = '".$pacienteNuevo->nombre1."',
			Pacno2 = '".$pacienteNuevo->nombre2."',
			Pacap1 = '".$pacienteNuevo->apellido1."',
			Pacap2 = '".$pacienteNuevo->apellido2."',
			Pacnac = '".$pacienteNuevo->fechaNacimiento."',
			Pacsex = '".$pacienteNuevo->genero."'
		WHERE
			Pacced = '".$pacienteNuevo->documentoIdentidad."'
			AND Pactid = '".$pacienteNuevo->tipoDocumentoIdentidad."'";
	$err1=mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	$num_affect = mysql_affected_rows();
	if($num_affect>0)
	{
		//Guardo LOG de actualizaci�n en tabla root_000036
		$q = "	INSERT INTO log_agenda
								  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
						   VALUES
								  ('".$fechaLog."', '".$horaLog."', '".$pacienteNuevo->documentoIdentidad."', '".$pacienteNuevo->tipoDocumentoIdentidad."', 'Actualizacion tabla root_000036', 'root', 'Nuevos datos ".$pacienteNuevo->nombre1." ".$pacienteNuevo->nombre2." ".$pacienteNuevo->apellido1." ".$pacienteNuevo->apellido2." | ".$pacienteNuevo->fechaNacimiento." | ".$pacienteNuevo->genero." ', '".$registrosFila."')";
		$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	}
}

//Existe un registro del paciente en la tabla 37 de root
function existeEnTablaIngresos($paciente,$origen)
{
	global $conex;

	$esta = false;

	$q = "SELECT
				*
		  	FROM
		  		root_000037
			WHERE
				Orihis = '".$paciente->historiaClinica."'
				AND Oriori = '".$origen."'";


	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . "-" . mysql_error());
	$filas = mysql_num_rows($res);

	if($filas > 0)
	{
		$esta = true;
	}
	return $esta;
}

// En tabla root_000037 borra la historia asociada en matrix a la c�dula de unix
// siempre y cuando esta historia sea diferente a la asociada en Unix
function borrarHistoriaDiferenteUnix($paciente, $wemp_pmla, $seguridad)
{
	global $conex;

	$fechaLog = date('Y-m-d');
	$horaLog = date('H:i:s');

	$q = "SELECT *
		  	FROM root_000037
		   WHERE Oriced = '".$paciente->documentoIdentidad."'
		     AND Oritid = '".$paciente->tipoDocumentoIdentidad."'
			 AND Orihis != '".$paciente->historiaClinica."'
			 AND Oriori = '".$wemp_pmla."'";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . "-" . mysql_error());
	$filas = mysql_num_rows($res);

	if($filas > 0)
	{

		$registrosFila = obtenerRegistrosFila($q);

		$q = " DELETE FROM root_000037
				WHERE Oriced = '".$paciente->documentoIdentidad."'
				  AND Oritid = '".$paciente->tipoDocumentoIdentidad."'
				  AND Orihis != '".$paciente->historiaClinica."'
				  AND Oriori = '".$wemp_pmla."'";
		$err1=mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . "-" . mysql_error());

		$num_affect = mysql_affected_rows();
		if($num_affect>0)
		{
			//Guardo LOG de borrado en tabla root_000037 por clave duplicada
			$q = "	INSERT INTO log_agenda
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
							   VALUES
									  ('".$fechaLog."', '".$horaLog."', '".$paciente->historiaClinica."', '".$paciente->ingresoHistoriaClinica."', 'Borrado tabla root_000037', '".$seguridad."', 'Historia diferente unix ".$paciente->tipoDocumentoIdentidad." ".$paciente->documentoIdentidad."', '".$registrosFila."')";
			$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}
	}
}

//Ingresa los datos en la tabla 37 de root
function insertarIngresoPaciente($paciente, $wemp_pmla, $seguridad)
{
	global $conex;

	$fechaLog = date('Y-m-d');
	$horaLog = date('H:i:s');

	$q = "INSERT INTO root_000037
			( medico,fecha_data,hora_data,Oriced,Orihis,Oriing,Oriori,Oritid,Seguridad)
		VALUES
			('root','".$paciente->fechaIngreso."','".$paciente->horaIngreso."','".$paciente->documentoIdentidad."', '".$paciente->historiaClinica."', '".$paciente->ingresoHistoriaClinica."', '".$wemp_pmla."', '".$paciente->tipoDocumentoIdentidad."', 'C-".$seguridad."' )";
	$err=mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	$num_affect = mysql_affected_rows();
	if($num_affect>0)
	{
		//Guardo LOG de grabaci�n en tabla root_000037
		$q = "	INSERT INTO log_agenda
								  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera)
						   VALUES
								  ('".$fechaLog."', '".$horaLog."', '".$paciente->historiaClinica."', '".$paciente->ingresoHistoriaClinica."', 'Grabacion tabla root_000037', '".$seguridad."', 'Auto ".$paciente->tipoDocumentoIdentidad." ".$paciente->documentoIdentidad."')";
		$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	}

	// Si ocurri� error por clave duplicada
	$error_sql = mysql_errno();
	if(isset($error_sql) && $error_sql=="1062")
	{
		// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
		$qlog = " SELECT *
					FROM root_000037
				   WHERE Oriced = '".$paciente->documentoIdentidad."'
				  AND Oritid = '".$paciente->tipoDocumentoIdentidad."'
				  AND Oriori = '".$wemp_pmla."';";
		$registrosFila = obtenerRegistrosFila($qlog);

		$q = " DELETE FROM root_000037
				WHERE Oriced = '".$paciente->documentoIdentidad."'
				  AND Oritid = '".$paciente->tipoDocumentoIdentidad."'
				  AND Oriori = '".$wemp_pmla."';";
		$err1=mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . "-" . mysql_error());

		$num_affect = mysql_affected_rows();
		if($num_affect>0)
		{
			//Guardo LOG de borrado en tabla root_000037 por clave duplicada
			$q = "	INSERT INTO log_agenda
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
							   VALUES
									  ('".$fechaLog."', '".$horaLog."', '".$paciente->historiaClinica."', '".$paciente->ingresoHistoriaClinica."', 'Borrado tabla root_000037', '".$seguridad."', 'Clave duplicada ".$paciente->tipoDocumentoIdentidad." ".$paciente->documentoIdentidad."', '".$registrosFila."')";
			$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}

		$q = "INSERT INTO root_000037
				( medico,fecha_data,hora_data,Oriced,Orihis,Oriing,Oriori,Oritid,Seguridad)
			VALUES
				('root','".$paciente->fechaIngreso."','".$paciente->horaIngreso."','".$paciente->documentoIdentidad."', '".$paciente->historiaClinica."', '".$paciente->ingresoHistoriaClinica."', '".$wemp_pmla."', '".$paciente->tipoDocumentoIdentidad."', 'C-".$seguridad."' )";
		$err=mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . "-" . mysql_error());

		$num_affect = mysql_affected_rows();
		if($num_affect>0)
		{
			//Guardo LOG de grabaci�n en tabla root_000037 por clave duplicada
			$q = "	INSERT INTO log_agenda
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera)
							   VALUES
									  ('".$fechaLog."', '".$horaLog."', '".$paciente->historiaClinica."', '".$paciente->ingresoHistoriaClinica."', 'Grabacion tabla root_000037', '".$seguridad."', 'Clave duplicada ".$paciente->tipoDocumentoIdentidad." ".$paciente->documentoIdentidad."')";
			$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}
	}
}

//Actualiza los datos en la tabla 37 de root
function actualizarIngresoPaciente($pacienteAnterior, $pacienteNuevo, $origen)
{
	global $conex;
	global $wcliame;

	$fechaLog = date('Y-m-d');
	$horaLog = date('H:i:s');

	// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
	$qlog = " SELECT *
				FROM root_000037
			   WHERE Orihis = '".$pacienteNuevo->historiaClinica."'
				 AND Oriori = '".$origen."';";
	$registrosFila = obtenerRegistrosFila($qlog);
	
	$qlog_cliame100 = " SELECT *
						  FROM ".$wcliame."_000100
					     WHERE Pachis = '".$pacienteNuevo->historiaClinica."'";
	$registrosFila_cliame100 = obtenerRegistrosFila($qlog_cliame100);
	
	
	$qc100 = "UPDATE ".$wcliame."_000100
			     SET Pacdoc = '".$pacienteNuevo->documentoIdentidad."',
			         Pactdo = '".$pacienteNuevo->tipoDocumentoIdentidad."'
		       WHERE Pachis = '".$pacienteNuevo->historiaClinica."'";
	$err1_c100 = mysql_query($qc100,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qc100 . " - " . mysql_error());

	$num_affect_c100 = mysql_affected_rows();
	
	if($num_affect_c100 > 0)
	{
		//Guardo LOG de actualizaci�n en tabla root_000037
		$q = "	INSERT INTO log_agenda
								  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
						   VALUES
								  ('".$fechaLog."', '".$horaLog."', '".$pacienteNuevo->historiaClinica."', '".$pacienteNuevo->ingresoHistoriaClinica."', 'Actualizacion tabla ".$wcliame."_000100', 'root', 'Actualiza ingreso ".$pacienteNuevo->ingresoHistoriaClinica." | ".$pacienteNuevo->tipoDocumentoIdentidad." ".$pacienteNuevo->documentoIdentidad."', '".$registrosFila_cliame100."')";
		$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	}
	
	
	$q = "UPDATE
			root_000037
		SET
			Oriing = '".$pacienteNuevo->ingresoHistoriaClinica."',
			Oriced = '".$pacienteNuevo->documentoIdentidad."',
			Oritid = '".$pacienteNuevo->tipoDocumentoIdentidad."'
		WHERE
			Orihis = '".$pacienteNuevo->historiaClinica."'
			AND Oriori = '".$origen."';";
	$err1=mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	$num_affect = mysql_affected_rows();
	if($num_affect>0)
	{
		//Guardo LOG de actualizaci�n en tabla root_000037
		$q = "	INSERT INTO log_agenda
								  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
						   VALUES
								  ('".$fechaLog."', '".$horaLog."', '".$pacienteNuevo->historiaClinica."', '".$pacienteNuevo->ingresoHistoriaClinica."', 'Actualizacion tabla root_000037', 'root', 'Actualiza ingreso ".$pacienteNuevo->ingresoHistoriaClinica." | ".$pacienteNuevo->tipoDocumentoIdentidad." ".$pacienteNuevo->documentoIdentidad."', '".$registrosFila."')";
		$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	}	
	
	// Si ocurri� error por clave duplicada
	$error_sql = mysql_errno();
	if(isset($error_sql) && $error_sql=="1062")
	{
		// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
		$qlog = " SELECT *
					FROM root_000037
				   WHERE Oriced = '".$pacienteNuevo->documentoIdentidad."'
					 AND Oritid = '".$pacienteNuevo->tipoDocumentoIdentidad."'
					 AND Oriori = '".$origen."';";
		$registrosFila = obtenerRegistrosFila($qlog);

		$q = "DELETE FROM root_000037
					WHERE Oriced = '".$pacienteNuevo->documentoIdentidad."'
					  AND Oritid = '".$pacienteNuevo->tipoDocumentoIdentidad."'
					  AND Oriori = '".$origen."';";
		$err1=mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . "-" . mysql_error());

		$num_affect = mysql_affected_rows();
		if($num_affect>0)
		{
			//Guardo LOG de borrado en tabla root_000037 por clave duplicada
			$q = "	INSERT INTO log_agenda
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
							   VALUES
									  ('".$fechaLog."', '".$horaLog."', '".$pacienteNuevo->historiaClinica."', '".$pacienteNuevo->ingresoHistoriaClinica."', 'Borrado tabla root_000037', 'root', 'Clave duplicada ".$pacienteNuevo->tipoDocumentoIdentidad." ".$pacienteNuevo->documentoIdentidad."', '".$registrosFila."')";
			$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}

		// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
		$qlog = " SELECT *
					FROM root_000037
				   WHERE Orihis = '".$pacienteNuevo->historiaClinica."'
					 AND Oriori = '".$origen."';";
		$registrosFila = obtenerRegistrosFila($qlog);

		$q = "UPDATE
				root_000037
			SET
				Oriing = '".$pacienteNuevo->ingresoHistoriaClinica."',
				Oriced = '".$pacienteNuevo->documentoIdentidad."',
				Oritid = '".$pacienteNuevo->tipoDocumentoIdentidad."'
			WHERE
				Orihis = '".$pacienteNuevo->historiaClinica."'
				AND Oriori = '".$origen."';";
		$err1=mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . "-" . mysql_error());

		$num_affect = mysql_affected_rows();
		if($num_affect > 0)
		{
			//Guardo LOG de actualizacion en tabla root_000037 por clave duplicada
			$q = "	INSERT INTO log_agenda
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
							   VALUES
									  ('".$fechaLog."', '".$horaLog."', '".$pacienteNuevo->historiaClinica."', '".$pacienteNuevo->ingresoHistoriaClinica."', 'Actualizacion tabla root_000037', 'root', 'Clave duplicada ".$pacienteNuevo->ingresoHistoriaClinica." | ".$pacienteNuevo->tipoDocumentoIdentidad." ".$pacienteNuevo->documentoIdentidad."', '".$registrosFila."')";
			$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}
		
		//Actualizacion de la tabla cliame_000100
		$qc100 = "UPDATE ".$wcliame."_000100
			        SET Pacdoc = '".$pacienteNuevo->documentoIdentidad."',
			            Pactdo = '".$pacienteNuevo->tipoDocumentoIdentidad."'
		           WHERE Pachis = '".$pacienteNuevo->historiaClinica."'";
		$err1_c100 = mysql_query($qc100,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qc100 . " - " . mysql_error());

		$num_affect_c100 = mysql_affected_rows();
		
		if($num_affect_c100 > 0)
		{
			//Guardo LOG de actualizaci�n en tabla root_000037
			$q = "	INSERT INTO log_agenda
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
							   VALUES
									  ('".$fechaLog."', '".$horaLog."', '".$pacienteNuevo->historiaClinica."', '".$pacienteNuevo->ingresoHistoriaClinica."', 'Actualizacion tabla ".$wcliame."_000100', 'root', 'Actualiza ingreso ".$pacienteNuevo->ingresoHistoriaClinica." | ".$pacienteNuevo->tipoDocumentoIdentidad." ".$pacienteNuevo->documentoIdentidad."', '".$registrosFila_cliame100."')";
			$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}

	}
}

//Actualiza el documento del paciente en la tabla 37 de root
function actualizarDocumentoPacienteTablaIngresos($pacienteAnterior, $pacienteNuevo,$wemp_pmla)
{
	global $conex;
	global $wcliame;

	$fechaLog = date('Y-m-d');
	$horaLog = date('H:i:s');

	// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
	$qlog = " SELECT *
				FROM root_000037
			   WHERE Orihis = '".$pacienteAnterior->historiaClinica."'
				 AND Oriori = '".$wemp_pmla."' ";
	$registrosFila = obtenerRegistrosFila($qlog);
	
	$qlog_cliame100 = " SELECT *
						  FROM ".$wcliame."_000100
					     WHERE Pachis = '".$pacienteNuevo->historiaClinica."'";
	$registrosFila_cliame100 = obtenerRegistrosFila($qlog_cliame100);
	
	//Actualizacion de la tabla cliame_000100
	$qc100 = "UPDATE ".$wcliame."_000100
				SET Pacdoc = '".$pacienteNuevo->documentoIdentidad."',
					Pactdo = '".$pacienteNuevo->tipoDocumentoIdentidad."'
			   WHERE Pachis = '".$pacienteNuevo->historiaClinica."'";
	$err1_c100 = mysql_query($qc100,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qc100 . " - " . mysql_error());

	$num_affect_c100 = mysql_affected_rows();
	
	if($num_affect_c100 > 0)
	{
		//Guardo LOG de actualizaci�n en tabla root_000037
		$q = "	INSERT INTO log_agenda
								  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
						   VALUES
								  ('".$fechaLog."', '".$horaLog."', '".$pacienteNuevo->historiaClinica."', '".$pacienteNuevo->ingresoHistoriaClinica."', 'Actualizacion tabla ".$wcliame."_000100', 'root', 'Actualiza ingreso ".$pacienteNuevo->ingresoHistoriaClinica." | ".$pacienteNuevo->tipoDocumentoIdentidad." ".$pacienteNuevo->documentoIdentidad."', '".$registrosFila_cliame100."')";
		$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	}
	
	
	$q = "UPDATE root_000037
		     SET Oriced = '".$pacienteNuevo->documentoIdentidad."',
			     Oritid = '".$pacienteNuevo->tipoDocumentoIdentidad."'
		   WHERE Orihis = '".$pacienteAnterior->historiaClinica."'
			 AND Oriori = '".$wemp_pmla."' ";
	//		AND Oriing = '".$pacienteAnterior->ingresoHistoriaClinica."'
	$err1=mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	$num_affect = mysql_affected_rows();
	if($num_affect>0)
	{
		//Guardo LOG de actualizaci�n en tabla root_000037
		$q = "	INSERT INTO log_agenda
								  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
						   VALUES
								  ('".$fechaLog."', '".$horaLog."', '".$pacienteAnterior->historiaClinica."', '".$pacienteAnterior->ingresoHistoriaClinica."', 'Actualizacion tabla root_000037', 'root', 'Actualiza documento paciente ".$pacienteNuevo->tipoDocumentoIdentidad." ".$pacienteNuevo->documentoIdentidad."', '".$registrosFila."')";
		$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	}
	

	// Si ocurri� error por clave duplicada
	$error_sql = mysql_errno();
	if(isset($error_sql) && $error_sql=="1062")
	{
		// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
		$qlog = " SELECT *
					FROM root_000037
				   WHERE Oriced = '".$pacienteNuevo->documentoIdentidad."'
					 AND Oritid = '".$pacienteNuevo->tipoDocumentoIdentidad."'
					 AND Oriori = '".$wemp_pmla."';";
		$registrosFila = obtenerRegistrosFila($qlog);

		$q = "DELETE FROM root_000037
					WHERE Oriced = '".$pacienteNuevo->documentoIdentidad."'
					  AND Oritid = '".$pacienteNuevo->tipoDocumentoIdentidad."'
					  AND Oriori = '".$wemp_pmla."';";
		$err1=mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . "-" . mysql_error());

		$num_affect = mysql_affected_rows();
		if($num_affect>0)
		{
			//Guardo LOG de borrado en tabla root_000037 por clave duplicada
			$q = "	INSERT INTO log_agenda
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
							   VALUES
									  ('".$fechaLog."', '".$horaLog."', '".$pacienteAnterior->historiaClinica."', '".$pacienteAnterior->ingresoHistoriaClinica."', 'Borrado tabla root_000037', 'root', 'Clave duplicada ".$pacienteNuevo->tipoDocumentoIdentidad." ".$pacienteNuevo->documentoIdentidad."', '".$registrosFila."')";
			$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}

		// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
		$qlog = " SELECT *
					FROM root_000037
				   WHERE Orihis = '".$pacienteAnterior->historiaClinica."'
					 AND Oriori = '".$wemp_pmla."' ";
		$registrosFila = obtenerRegistrosFila($qlog);

		$q = "UPDATE
				root_000037
			SET
				Oriced = '".$pacienteNuevo->documentoIdentidad."',
				Oritid = '".$pacienteNuevo->tipoDocumentoIdentidad."'
			WHERE
				Orihis = '".$pacienteAnterior->historiaClinica."'
				AND Oriori = '".$wemp_pmla."' ";
		//		AND Oriing = '".$pacienteAnterior->ingresoHistoriaClinica."'
		$err1=mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

		$num_affect = mysql_affected_rows();
		if($num_affect>0)
		{
			//Guardo LOG de grabacion en tabla root_000037 por clave duplicada
			$q = "	INSERT INTO log_agenda
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
							   VALUES
									  ('".$fechaLog."', '".$horaLog."', '".$pacienteAnterior->historiaClinica."', '".$pacienteAnterior->ingresoHistoriaClinica."', 'Grabacion tabla root_000037', 'root', 'Clave duplicada ".$pacienteNuevo->tipoDocumentoIdentidad." ".$pacienteNuevo->documentoIdentidad."', '".$registrosFila."')";
			$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}
		
		//Actualizacion de la tabla cliame_000100
		$qc100 = "UPDATE ".$wcliame."_000100
					SET Pacdoc = '".$pacienteNuevo->documentoIdentidad."',
						Pactdo = '".$pacienteNuevo->tipoDocumentoIdentidad."'
				   WHERE Pachis = '".$pacienteNuevo->historiaClinica."'";
		$err1_c100 = mysql_query($qc100,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qc100 . " - " . mysql_error());

		$num_affect_c100 = mysql_affected_rows();
		
		if($num_affect_c100 > 0)
		{
			//Guardo LOG de actualizaci�n en tabla root_000037
			$q = "	INSERT INTO log_agenda
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
							   VALUES
									  ('".$fechaLog."', '".$horaLog."', '".$pacienteNuevo->historiaClinica."', '".$pacienteNuevo->ingresoHistoriaClinica."', 'Actualizacion tabla ".$wcliame."_000100', 'root', 'Actualiza ingreso ".$pacienteNuevo->ingresoHistoriaClinica." | ".$pacienteNuevo->tipoDocumentoIdentidad." ".$pacienteNuevo->documentoIdentidad."', '".$registrosFila_cliame100."')";
			$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}
	}
}

//Existe un registro del paciente en la tabla 16 de movhos
function existeEnTablaResponsables($pacienteMatrix, $wemp_pmla)
{
	global $conex;

	$esta = false;

	$q = "SELECT
				*
		  	FROM
			  ".$wbasedato."_000016
			WHERE
				Inghis = '".$pacienteMatrix->historiaClinica."'
				AND Inging = '".$pacienteMatrix->ingresoHistoriaClinica."';";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . "-en el query: " . $q . "-" . mysql_error());
	$filas = mysql_num_rows($res);

	if($filas > 0)
	{
		$esta = true;
	}
	return $esta;
}

//Ingresa los datos en la tabla 22 de hce
function registrarIngresoPaciente($ingreso,$seguridad)
{
	global $conex;

	$fecha = date("Y-m-d");
	$hora = date("H:i:s");

	$q = "	SELECT Mtrhis
			FROM hce_000022
			WHERE Mtrhis = '".$ingreso->historiaClinica."'
			AND Mtring = '".$ingreso->ingresoHistoriaClinica."'";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if($num==0)
	{
		$q = "INSERT INTO
				hce_000022
					(Medico,Fecha_data,Hora_data,Mtrhis,Mtring,Mtrcci,Mtrmed,Mtrest,Mtrtra,Mtretr,Mtrcur,Seguridad)
				VALUES
					('HCE','".$fecha."','".$hora."','".$ingreso->historiaClinica."','".$ingreso->ingresoHistoriaClinica."','".$ingreso->servicioActual."','','on','off','','off','C-".$seguridad."')";

		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

		$num_affect = mysql_affected_rows();
		if($num_affect>0)
		{
			//Guardo LOG de garabaci�n en tabla hce_000022
			$q = "	INSERT INTO log_agenda
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera)
							   VALUES
									  ('".$fecha."', '".$hora."', '".$ingreso->historiaClinica."', '".$ingreso->ingresoHistoriaClinica."', 'Grabacion tabla hce_000022', '".$seguridad."', 'Auto')";
			$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}

		// Si ocurri� error por clave duplicada
		$error_sql = mysql_errno();
		if(isset($error_sql) && $error_sql=="1062")
		{
			// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
			$qlog = " SELECT *
						FROM hce_000022
					   WHERE Mtrhis = '".$ingreso->historiaClinica."'
						 AND Mtring = '".$ingreso->ingresoHistoriaClinica."'; ";
			$registrosFila = obtenerRegistrosFila($qlog);

			$q = "DELETE FROM hce_000022
						WHERE Mtrhis = '".$ingreso->historiaClinica."'
						  AND Mtring = '".$ingreso->ingresoHistoriaClinica."';";
			$err1=mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . "-" . mysql_error());

			$num_affect = mysql_affected_rows();
			if($num_affect>0)
			{
				//Guardo LOG de borrado en tabla root_000037 por clave duplicada
				$q = "	INSERT INTO log_agenda
										  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
								   VALUES
										  ('".$fecha."', '".$hora."', '".$ingreso->historiaClinica."', '".$ingreso->ingresoHistoriaClinica."', 'Borrado tabla hce_000022', '".$seguridad."', 'Clave duplicada ".$ingreso->historiaClinica."-".$ingreso->ingresoHistoriaClinica."', '".$registrosFila."')";
				$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			}

			$q = "INSERT INTO
					hce_000022
						(Medico,Fecha_data,Hora_data,Mtrhis,Mtring,Mtrcci,Mtrmed,Mtrest,Mtrtra,Mtretr,Mtrcur,Seguridad)
					VALUES
						('HCE','".$fecha."','".$hora."','".$ingreso->historiaClinica."','".$ingreso->ingresoHistoriaClinica."','".$ingreso->servicioActual."','','on','off','','off','C-".$seguridad."')";

			$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

			$num_affect = mysql_affected_rows();
			if($num_affect>0)
			{
				//Guardo LOG de garabaci�n en tabla hce_000022
				$q = "	INSERT INTO log_agenda
										  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera)
								   VALUES
										  ('".$fecha."', '".$hora."', '".$ingreso->historiaClinica."', '".$ingreso->ingresoHistoriaClinica."', 'Grabacion tabla hce_000022', '".$seguridad."', 'Auto')";
				$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			}
		}

	}
	else
	{
		// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
		$qlog = " SELECT *
					FROM hce_000022
				   WHERE Mtrhis = '".$ingreso->historiaClinica."'
					 AND Mtring = '".$ingreso->ingresoHistoriaClinica."'";
		$registrosFila = obtenerRegistrosFila($qlog);

		$q = "	UPDATE hce_000022
				SET Fecha_data='".$fecha."', Hora_data='".$hora."', Mtrmed='', Mtrest='on', Mtrtra='off', Mtretr='', Mtrcur='off', Mtrcci='".$ingreso->servicioActual."', Seguridad='C-".$seguridad."'
				WHERE Mtrhis = '".$ingreso->historiaClinica."'
				AND	Mtring = '".$ingreso->ingresoHistoriaClinica."'";

		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

		$num_affect = mysql_affected_rows();
		if($num_affect>0)
		{
			//Guardo LOG de actualizaci�n en tabla hce_000022
			$q = "	INSERT INTO log_agenda
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
							   VALUES
									  ('".$fecha."', '".$hora."', '".$ingreso->historiaClinica."', '".$ingreso->ingresoHistoriaClinica."', 'Actualizacion tabla hce_000022', '".$seguridad."', 'Auto', '".$registrosFila."')";
			$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}
	}
}

//Ingresa los datos en la tabla 16 de movhos
function insertarResponsablePaciente($paciente, $wemp_pmla, $seguridad)
{
	global $conex;

	$fecha = date("Y-m-d");
	$hora = date("H:i:s");

	$q = "INSERT INTO ".$wbasedato."_000016
			(medico,Fecha_data,Hora_data,Inghis,Inging,Ingres,Ingnre,Ingtip,Ingtel,Ingdir,Ingmun,Seguridad)
		VALUES
			('movhos','".$paciente->fechaIngreso."','".$paciente->horaIngreso."','".$paciente->historiaClinica."', '".$paciente->ingresoHistoriaClinica."', '".$paciente->numeroIdentificacionResponsable."', '".$paciente->nombreResponsable."', '".$paciente->tipoResponsable."', '".$paciente->telefono."', '".$paciente->direccion."', '".$paciente->municipio."', 'C-".$seguridad."' )";
	$err=mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	$num_affect = mysql_affected_rows();
	if($num_affect>0)
	{
		//Guardo LOG de garabaci�n en tabla movhos_000016
		$q = "	INSERT INTO log_agenda
								  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera)
						   VALUES
								  ('".$fecha."', '".$hora."', '".$paciente->historiaClinica."', '".$paciente->ingresoHistoriaClinica."', 'Grabacion tabla ".$wbasedato."_000016', '".$seguridad."', 'Auto')";
		$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	}
}

//Actualiza los datos en la tabla 16 de movhos
function actualizarResponsablePaciente($pacienteAnterior, $pacienteNuevo, $resp_anterior, $resp_nuevo, $control_cliame)
{
	global $conex;
	global $wemp_pmla;
	global $wcliame;

	$fecha = date("Y-m-d");
	$hora = date("H:i:s");

	// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
	$qlog = " SELECT *
				FROM ".$wbasedato."_000016
			   WHERE Inghis = '".$pacienteAnterior->historiaClinica."'
				 AND Inging = '".$pacienteAnterior->ingresoHistoriaClinica."' ";
	$registrosFila = obtenerRegistrosFila($qlog);

	$q = "UPDATE ".$wbasedato."_000016
	         SET Ingres = '".$pacienteNuevo->numeroIdentificacionResponsable."',
				 Ingnre = '".$pacienteNuevo->nombreResponsable."',
				 Ingtip = '".$pacienteNuevo->tipoResponsable."',
				 Ingtel = '".$pacienteNuevo->telefono."',
				 Ingdir = '".$pacienteNuevo->direccion."',
				 Ingmun = '".$pacienteNuevo->municipio."'
		   WHERE Inghis = '".$pacienteAnterior->historiaClinica."'
			 AND Inging = '".$pacienteAnterior->ingresoHistoriaClinica."'";
	$err1=mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	$num_affect = mysql_affected_rows();
	if($num_affect>0)
	{
		//Guardo LOG de actualizaci�n en tabla movhos_000016
		$q = "	INSERT INTO log_agenda
								  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
						   VALUES
								  ('".$fecha."', '".$hora."', '".$pacienteAnterior->historiaClinica."', '".$pacienteAnterior->ingresoHistoriaClinica."', 'Actualizacion tabla ".$wbasedato."_000016', 'root', 'Auto ".$pacienteNuevo->numeroIdentificacionResponsable." ".$pacienteNuevo->nombreResponsable."', '".$registrosFila."')";
		$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	}

	//------- Cambios en Cliame --------

	if($control_cliame == "on"){

		$consultar_tarifa = consultar_tarifa($resp_nuevo); //consulto la tarifa del nuevo responsable.
		$resParticular = consultarAliasPorAplicacion($conex, $wemp_pmla, 'codigoempresaparticular');


		// --> Actualizar el nuevo resposable en la tabla 101
		$qlog = " SELECT *
					FROM ".$wcliame."_000101
				   WHERE Inghis = '".$pacienteAnterior->historiaClinica."'
					 AND Ingnin = '".$pacienteAnterior->ingresoHistoriaClinica."' ";
		$registrosFila101 = obtenerRegistrosFila($qlog);

		$qNewRespons= " UPDATE ".$wcliame."_000101
						   SET Ingcem = '".$resp_nuevo."',
							   Ingent = '".$pacienteNuevo->nombreResponsable."',
							   Ingtar = '".$consultar_tarifa."',
							   Ingtpa = '".$pacienteNuevo->tipo_empresa."'
						 WHERE Inghis = '".$pacienteAnterior->historiaClinica."'
						   AND Ingnin = '".$pacienteAnterior->ingresoHistoriaClinica."'
		";
		mysql_query($qNewRespons, $conex) or die("Error en el query: ".$qNewRespons."<br>Tipo Error:".mysql_error());
		$num_affect101 = mysql_affected_rows();

		if($num_affect101 > 0)
		{
			//Guardo LOG de actualizaci�n en tabla movhos_000016
			$q = "	INSERT INTO log_agenda
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
							   VALUES
									  ('".$fecha."', '".$hora."', '".$pacienteAnterior->historiaClinica."', '".$pacienteAnterior->ingresoHistoriaClinica."', 'Actualizacion tabla ".$wbasedato."_000101', 'root', 'Auto ".$pacienteNuevo->numeroIdentificacionResponsable." ".$pacienteNuevo->nombreResponsable."', '".$registrosFila101."')";
			$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}

		// --> Actualizo la fecha en que inicia como responsable
		$qlog = " SELECT *
					FROM ".$wcliame."_000205
				   WHERE Reshis = '".$pacienteAnterior->historiaClinica."'
					 AND Resing = '".$pacienteAnterior->ingresoHistoriaClinica."' ";
		$registrosFila205 = obtenerRegistrosFila($qlog);

		$qFechaIniR = " UPDATE ".$wcliame."_000205
						   SET Resfir = '".date("Y-m-d")."',
							   Resest = 'on'
						 WHERE Reshis = '".$pacienteAnterior->historiaClinica."'
						   AND Resing = '".$pacienteAnterior->ingresoHistoriaClinica."'
						   AND Resnit = '".$resp_nuevo."'";
		mysql_query($qFechaIniR, $conex) or die("Error en el query: ".$qFechaIniR."<br>Tipo Error:".mysql_error());
		$num_affect205 = mysql_affected_rows();

		if($num_affect205 > 0)
		{
			//Guardo LOG de actualizaci�n en tabla movhos_000016
			$q = "	INSERT INTO log_agenda
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
							   VALUES
									  ('".$fecha."', '".$hora."', '".$pacienteAnterior->historiaClinica."', '".$pacienteAnterior->ingresoHistoriaClinica."', 'Actualizacion tabla ".$wbasedato."_000205 Fecha Inicio Resp', 'root', 'Auto ".$pacienteNuevo->numeroIdentificacionResponsable." ".$pacienteNuevo->nombreResponsable." Fecha de inicio como responsable', '".$registrosFila205."')";
			$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}


	// --> Registrar en la tabla de resposables las fechas en que se realiza el cambio.
		$qFechaFinR = "    UPDATE ".$wcliame."_000205
							  SET Resffr = '".date("Y-m-d")."'
							WHERE Reshis = '".$pacienteAnterior->historiaClinica."'
							  AND Resing = '".$pacienteAnterior->ingresoHistoriaClinica."'
							  AND Resnit = '".$resp_anterior."'
							  AND Resest = 'on'";
		mysql_query($qFechaFinR, $conex) or die("Error en el query: ".$qFechaFinR."<br>Tipo Error:".mysql_error());
		$num_affect2051 = mysql_affected_rows();

		if($num_affect205 > 0)
		{
			//Guardo LOG de actualizaci�n en tabla movhos_000016
			$q = "	INSERT INTO log_agenda
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
							   VALUES
									  ('".$fecha."', '".$hora."', '".$pacienteAnterior->historiaClinica."', '".$pacienteAnterior->ingresoHistoriaClinica."', 'Actualizacion tabla ".$wbasedato."_000205 Fecha Terminacion Resp Anterior', 'root', 'Auto ".$pacienteNuevo->numeroIdentificacionResponsable." ".$pacienteNuevo->nombreResponsable." Fecha de inicio como responsable', '".$registrosFila205."')";
			$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}
	}

}

//Ingresa los datos en la tabla 18 de movhos
function grabarIngresoPaciente($ingreso,$seguridad)
{
	global $conex;

	$fecha = date("Y-m-d");
	$hora = date("H:i:s");

	$q = "INSERT INTO
			".$wbasedato."_000018 (Medico,Fecha_data,Hora_data,Ubihis,Ubiing,Ubisac,Ubisan,Ubihac,Ubihan,Ubialp,Ubiald,Ubifap,Ubihap,Ubifad,Ubihad,Ubiptr,Seguridad)
		VALUES
			('movhos','".$ingreso->fechaIngreso."','".$ingreso->horaIngreso."','".$ingreso->historiaClinica."', '".$ingreso->ingresoHistoriaClinica."', '".$ingreso->servicioActual."', '".$ingreso->servicioAnterior."', '".$ingreso->habitacionActual."',  '".$ingreso->habitacionAnterior."','".$ingreso->altaEnProceso."', '".$ingreso->altaDefinitiva."', '".$ingreso->fechaAltaProceso."','".$ingreso->horaAltaProceso."', '".$ingreso->fechaAltaDefinitiva."', '".$ingreso->horaAltaDefinitiva."', '".$ingreso->enProcesoTraslado."', 'C-".$seguridad."' )";
	$err=mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	$num_affect = mysql_affected_rows();
	if($num_affect>0)
	{
		//Guardo LOG de grabaci�n en tabla movhos_000018
		$q = "	INSERT INTO log_agenda
								  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera)
						   VALUES
								  ('".$fecha."', '".$hora."', '".$ingreso->historiaClinica."', '".$ingreso->ingresoHistoriaClinica."', 'Grabacion tabla ".$wbasedato."_000018', '".$seguridad."', 'Auto')";
		$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	}

	// Si ocurri� error por clave duplicada
	$error_sql = mysql_errno();
	if(isset($error_sql) && $error_sql=="1062")
	{
		// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
		$qlog = " SELECT *
					FROM ".$wbasedato."_000018
				   WHERE Ubihis = '".$ingreso->historiaClinica."'
					 AND Ubiing = '".$ingreso->ingresoHistoriaClinica."';";
		$registrosFila = obtenerRegistrosFila($qlog);

		$q = "DELETE FROM ".$wbasedato."_000018
					WHERE Ubihis = '".$ingreso->historiaClinica."'
					  AND Ubiing = '".$ingreso->ingresoHistoriaClinica."';";
		$err1=mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . "-" . mysql_error());

		$num_affect = mysql_affected_rows();
		if($num_affect>0)
		{
			//Guardo LOG de borrado en tabla root_000037 por clave duplicada
			$q = "	INSERT INTO log_agenda
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
							   VALUES
									  ('".$fecha."', '".$hora."', '".$ingreso->historiaClinica."', '".$ingreso->ingresoHistoriaClinica."', 'Borrado tabla ".$wbasedato."_000018', '".$seguridad."', 'Clave duplicada ".$ingreso->historiaClinica."-".$ingreso->ingresoHistoriaClinica."', '".$registrosFila."')";
			$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}

		$q = "INSERT INTO
				".$wbasedato."_000018 (Medico,Fecha_data,Hora_data,Ubihis,Ubiing,Ubisac,Ubisan,Ubihac,Ubihan,Ubialp,Ubiald,Ubifap,Ubihap,Ubifad,Ubihad,Ubiptr,Seguridad)
			VALUES
				('movhos','".$ingreso->fechaIngreso."','".$ingreso->horaIngreso."','".$ingreso->historiaClinica."', '".$ingreso->ingresoHistoriaClinica."', '".$ingreso->servicioActual."', '".$ingreso->servicioAnterior."', '".$ingreso->habitacionActual."',  '".$ingreso->habitacionAnterior."','".$ingreso->altaEnProceso."', '".$ingreso->altaDefinitiva."', '".$ingreso->fechaAltaProceso."','".$ingreso->horaAltaProceso."', '".$ingreso->fechaAltaDefinitiva."', '".$ingreso->horaAltaDefinitiva."', '".$ingreso->enProcesoTraslado."', 'C-".$seguridad."' )";
		$err=mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

		$num_affect = mysql_affected_rows();
		if($num_affect>0)
		{
			//Guardo LOG de grabaci�n en tabla movhos_000018
			$q = "	INSERT INTO log_agenda
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera)
							   VALUES
									  ('".$fecha."', '".$hora."', '".$ingreso->historiaClinica."', '".$ingreso->ingresoHistoriaClinica."', 'Grabacion tabla ".$wbasedato."_000018', '".$seguridad."', 'Auto')";
			$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}

	}
}

// Verifica si el paciente ya ha sido ingresao
function pacienteIngresado($paciente)
{
	global $conex;

	$es = false;

	$q = "SELECT
				*
		 	FROM
			 ".$wbasedato."_000018
			WHERE
				Ubihis = '".$paciente->historiaClinica."'
				AND Ubiing   = '".$paciente->ingresoHistoriaClinica."'
			";

	$err = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($err);

	if($num>0)
	{
		$es = true;
	}

	return $es;
}

// Verifica si el paciente ya ha sido ingresado a la tabla 22 de HCE
function pacienteIngresadoHce($paciente)
{
	global $conex;

	$es = false;

	$q = "SELECT
				*
		 	FROM
		 		hce_000022
			WHERE
				Mtrhis = '".$paciente->historiaClinica."'
				AND Mtring   = '".$paciente->ingresoHistoriaClinica."'
			";

	$err = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($err);

	if($num>0)
	{
		$es = true;
	}

	return $es;
}

// Verifica si el centro de costos del paciente es cirugia
// Para determinar si movhos_000018.Ubiptr = on (En proceso de traslado)
function esCirugia($cco)
{
	global $conex;

	$es = false;

	$q = "SELECT
				Ccocod
		 	FROM
		 		".$wbasedato."_000011
			WHERE
				Ccocod = '".$cco."'
				AND Ccocir   = 'on'
			";

	$err = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($err);

	if($num>0)
	{
		$es = true;
	}

	return $es;
}

/********************************************************************************************/


/*********************************************************************************************
 **************************	FUNCIONES DE LLAMADO AJAX *************************************
 ********************************************************************************************/

// Borra los ingresos de una historia mayores al ingreso actual de Unix
function borraIngresosMayores($wbasedato,$wbasedatohce,$paciente,$ingreso,$wemp_pmla,$seguridad,$bandera,$fechaIngresoUnix,$horaIngresoUnix)
{
	global $conex;
	global $conexUnix;

	$fecha = date("Y-m-d");
	$hora = date("H:i:s");

	//Consulto los ingresos mayores al ingreso actual en unix
	$qmay = "	SELECT Fecha_data, Hora_data, Ubiing, Ubisac
				FROM ".$wbasedato."_000018
				WHERE Ubihis = '".$paciente."'
				AND   Ubiing*1 >= ".$ingreso." ";
	$resmay = mysql_query($qmay, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qmay . " - " . mysql_error());
	$nummay = mysql_num_rows($resmay);

	$ingreso_unix = $ingreso;

	// Si encontr� ingresos comience el borrado
	if($nummay>0)
	{
	  while($rowmay = mysql_fetch_array($resmay))
	  {
		$ingreso = $rowmay['Ubiing'];
		$fechaIngreso = $rowmay['Fecha_data'];
		$horaIngreso = $rowmay['Hora_data'];
		$ccoActual = $rowmay['Ubisac'];

		// 2013-02-18
		// Cosulto si el paciente tiene registros de movimiento hospitalario
		$qeyr = " SELECT Eyrhis
					FROM ".$wbasedato."_000017
				   WHERE Eyrhis = '".$paciente."'
					 AND Eyring = '".$ingreso."'
					 AND Eyrest = 'on'";
		$reseyr = mysql_query($qeyr, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qeyr . " - " . mysql_error());
		$numeyr = mysql_num_rows($reseyr);

		// Si el paciente no ha tenido movimiento hospitalario si se puede hacer el borrado
		if($numeyr==0)
		{
			$numant=0;
			if($ingreso==$ingreso_unix)
			{
				$horaUnix = date( "Y-m-d H:i:s", strtotime( $fechaIngresoUnix." ".$horaIngresoUnix ) - 10*60 );
				$fecha = explode(" ",$horaUnix);
				$fechaIngresoUnix = $fecha[0];
				$horaIngresoUnix = $fecha[1];

				// Valida si Fecha y hora registrados en Matrix son anteriores que los registrados en Unix
				// Si es menor quiere decir que el ingreso fue reasignado en Unix pero no se borr� de Matrix
				// Entonces se debe borrar

				//Consulto si tiene fecha hora de ingreso anterior a la de unix
				$qant = "	SELECT Fecha_data
							FROM ".$wbasedato."_000018
							WHERE Ubihis = '".$paciente."'
							AND   Ubiing = '".$ingreso."'
							AND   (
									(  Fecha_data < '".$fechaIngresoUnix."')
									OR
									(  Fecha_data = '".$fechaIngresoUnix."'
									   AND Hora_data < '".$horaIngresoUnix."'
									)
								   ) ";
				$resant = mysql_query($qant, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qant . " - " . mysql_error());
				$numant = mysql_num_rows($resant);
			}

			if($ingreso!=$ingreso_unix || $numant>0)
			{
				$fechaLog = date('Y-m-d');
				$horaLog = date('H:i:s');

				// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
				$qlog = " SELECT *
							FROM ".$wbasedato."_000016
						   WHERE Inghis = '".$paciente."'
							 AND Inging = '".$ingreso."'";
				$registrosFila = obtenerRegistrosFila($qlog);

				//Borro registro en tabla 16 de Movhos
				$q = "	DELETE
						  FROM ".$wbasedato."_000016
						 WHERE Inghis = '".$paciente."'
						   AND Inging = '".$ingreso."'";
				$res2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

				$num_affect = mysql_affected_rows();
				if($num_affect>0)
				{
					//Guardo LOG de borrado en tabla Movhos 16
					$q = "	INSERT INTO log_agenda
											  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
									   VALUES
											  ('".$fechaLog."', '".$horaLog."', '".$paciente."', '".$ingreso."', 'Borrado tabla ".$wbasedato."_000016', '".$seguridad."', '".$bandera."', '".$registrosFila."')";
					$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
				}

				// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
				$qlog = " SELECT *
							FROM ".$wbasedato."_000018
						   WHERE Ubihis = '".$paciente."'
							 AND Ubiing = '".$ingreso."'";
				$registrosFila = obtenerRegistrosFila($qlog);

				//Borro registro en tabla 18 de Movhos
				$q = "	DELETE
						  FROM ".$wbasedato."_000018
						 WHERE Ubihis = '".$paciente."'
						   AND Ubiing = '".$ingreso."'";
				$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

				$num_affect = mysql_affected_rows();
				if($num_affect>0)
				{
					//Guardo LOG de borrado en tabla Movhos 18
					$q = "	INSERT INTO log_agenda
											  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
									   VALUES
											  ('".$fechaLog."', '".$horaLog."', '".$paciente."', '".$ingreso."', 'Borrado tabla ".$wbasedato."_000018', '".$seguridad."', '".$bandera."', '".$registrosFila."')";
					$resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
				}

				// Cosulto si el paciente ya est� registrado en la tabla 22 de Hce
				$q = "	SELECT *
						FROM ".$wbasedatohce."_000022
						WHERE Mtrhis = '".$paciente."'
						AND Mtring = '".$ingreso."'";
				$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
				$num = mysql_num_rows($res);

				if($num>0)
				{
					$registrosFila = obtenerRegistrosFila($q);

					$q = "	DELETE
							  FROM ".$wbasedatohce."_000022
							 WHERE Mtrhis = '".$paciente."'
							   AND Mtring = '".$ingreso."'";
					$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

					$num_affect = mysql_affected_rows();
					if($num_affect>0)
					{
						//Guardo LOG de borrado en tabla hce 22
						$q = "	INSERT INTO log_agenda
												  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
										   VALUES
												  ('".$fechaLog."', '".$horaLog."', '".$paciente."', '".$ingreso."', 'Borrado tabla hce_000022', '".$seguridad."', '".$bandera."', '".$registrosFila."')";
						$resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					}
				}

				// 2013-02-26
				// Con esta funci�n se borra el registro de egreso por alta que exista en la tabla 000033 de movimiento hospitalario
				BorrarAltasMuertesAntesDeAgregarNueva($conex, $wbasedato, $paciente, $ingreso, $bandera);

				// Como ya se borro el egreso se comentan las siguientes l�neas

				// Cosulto si el paciente ya est� registrado en la tabla 33 de Movhos
				// $q2 = "	SELECT *
						// FROM ".$wbasedato."_000033
						// WHERE Historia_clinica = '".$paciente."'
						// AND Num_ingreso = '".$ingreso."'
						// AND Servicio = '".$ccoActual."' ";
				// $res2 = mysql_query($q2, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
				// $num2 = mysql_num_rows($res2);

				// if($num2>0)
				// {
					// $registrosFila = obtenerRegistrosFila($q2);

					// $q = "	DELETE
							// FROM ".$wbasedato."_000033
							// WHERE Historia_clinica = '".$paciente."'
							// AND Num_ingreso = '".$ingreso."'
							// AND Servicio = '".$ccoActual."' ";
					// $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

					// $num_affect = mysql_affected_rows();
					// if($num_affect>0)
					// {
						// //Guardo LOG de borrado en tabla Movhos 33
						// $q = "	INSERT INTO log_agenda
												  // (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
										   // VALUES
												  // ('".$fechaLog."', '".$horaLog."', '".$paciente."', '".$ingreso."', 'Borrado tabla movhos_000033', '".$seguridad."', '".$bandera."', '".$registrosFila."')";
						// $resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					// }
				// }
			}
		}
	  }
	}
}

// Establece el estado de alta para un paciente en urgencias
function altaPacienteUrgencias($wbasedato,$wbasedatohce,$paciente,$ingreso,$wemp_pmla,$seguridad,$bandera)
{
	global $conex;
	global $conexUnix;
    $fecha = date("Y-m-d");
	$hora = date("H:i:s");

	// Se consulta si el paciente sigue activo en Unix
	$qact = "SELECT COUNT(*)
			   FROM inpac
			  WHERE pachis = '".$paciente."'
				AND pacnum = ".$ingreso."";
	$rs_act = odbc_do($conexUnix,$qact);
	odbc_fetch_row($rs_act);
	$campos = odbc_result($rs_act,1);

	// Si no est� activo en Unix seg�n inpac
	// mira en inpaci si est� inactivo con el mismo ingreso
	// Sino el ingreso fue cancelado y se borra ingreso en Matrix
	if(!$campos || $campos==0)
	{
		// Se consulta si el paciente tiene el mismo ingreso en inpaci (Pacientes inactivos)
		$qin = "SELECT COUNT(*)
				   FROM inpaci
				  WHERE pachis = '".$paciente."'
					AND pacnum = ".$ingreso."";
		$rs_in = odbc_do($conexUnix,$qin);
		odbc_fetch_row($rs_in);
		$fields = odbc_result($rs_in,1);
	}

	// Si se identific� ingreso diferente al que traemos en inpaci
	// El alta debe ser con borrado de ingreso en Matrix
	if(isset($fields) && $fields==0 && $bandera!="Ingreso")
	{
		// Consulto el ingreso actual en inpaci
		// Se cambia el campo de la tabla pacing por pacnum, ya que pacnum es el ingreso y pacing es la fecha de ingreso. Jonatan Lopez 19 Diciembre 2013.
		$qcomp = " SELECT pacnum
				     FROM inpaci
				    WHERE pachis = '".$paciente."'";
		$rs_comp = odbc_do($conexUnix,$qcomp);
		odbc_fetch_row($rs_comp);
		$ing_act = odbc_result($rs_comp,1);

		if($ingreso>$ing_act)
			$tipo_alta = "borrado";
		else
			$tipo_alta = "normal";
	}
	else
	{
		// Si el alta es autom�tica y se encontraron registros en inpac no se da de alta
		// El que este en inpac y no en urgencias quiere decir que fue trasladado a otro centro de costos
		// Notese que si el alta no es automatica deja dar de alta asi este activo en inpac
		// El operador puede volver a activar el paciente si se equivoc� al dar de alta
		if(isset($bandera) && $bandera=="auto" && isset($campos) && $campos>0)
			$tipo_alta = "noalta";
		else
			$tipo_alta = "normal";
	}

	// 2012-01-19
	// Si fecha de egreso en unix es igual a la fecha actual y el paciente a�n aparece en la tabla de
	// Estado de habitaciones (movhos_000020) con cama asignada, entonces la historia no se debe dar de alta
	// Esto porque normalmente el paciente es egresado y el registro de egreso en Unix se hace al siguiente d�a
	// Si se egresa el mismo d�a en matrix puede haber inconsistencias en cuanto a la ocupaci�n de camas
	$qegr = "SELECT egregr
			   FROM inmegr
			  WHERE egrhis = '".$paciente."'
				AND egrnum = ".$ingreso."";
	$rs_egr = odbc_do($conexUnix,$qegr);
	odbc_fetch_row($rs_egr);
	$fecha_egreso = odbc_result($rs_egr,1);

	if($fecha_egreso == $fecha)
	{
		//Consulto si el paciente a�n tiene cama asignada
		$qhab = "	SELECT Habhis, Habing
					FROM ".$wbasedato."_000020
					WHERE Habhis = '".$paciente."'
					AND Habing = '".$ingreso."'";
		$reshab = mysql_query($qhab, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qhab . " - " . mysql_error());
		$numhab = mysql_num_rows($reshab);
		// Si tiene cama asignada y fecha de egreso en unix es igual a la fecha actual
		if($numhab>0)
			$tipo_alta = "noalta";
	}

	// Consulto si el paciente est� en la tabla de Historias No Automaticas
	// Si est� en esta tabla indica que no se puede dar de alta autom�ticamente
	$qhna = "	SELECT Hnahis, Hnaing
				FROM ".$wbasedato."_000140
				WHERE Hnahis = '".$paciente."'
				AND Hnaing = '".$ingreso."'
				AND Hnaest = 'on'";
	$reshna = mysql_query($qhna, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qhna . " - " . mysql_error());
	$numhna = mysql_num_rows($reshna);
	// Si est� en tabla de Historia no autom�tica y el alta es autom�tica
	if($numhna>0 && isset($bandera) && $bandera=="auto")
		$tipo_alta = "noalta";

	// Inicia el proceso de alta despu�s de validar si realmente se va a dar de alta
	if($tipo_alta != "noalta")
	{
		if($tipo_alta == "normal")
		{

			//Consulto si el paciente tiene conducta asignada
			$qcon = "	SELECT Mtrhis, Mtring, Mtrcon
						FROM ".$wbasedatohce."_000022
						WHERE Mtrhis = '".$paciente."'
						AND Mtring = '".$ingreso."'
						AND Mtrcon != ''
						AND Mtrcon != 'NO APLICA'
						AND Mtrest = 'on'";
			$rescon = mysql_query($qcon, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qcon . " - " . mysql_error());
			$rowcon = mysql_fetch_array($rescon);
			$numcon = mysql_num_rows($rescon);

			// 2013-09-09 ( && $fecha_egreso == $fecha )
			// Se agrega la condici�n comparando que la fecha de egreso en unix sea igual a hoy, de este modo los pacientes
			// con conducta asignada y fecha de egreso en Unix del d�a anterior s� ser�n dados de alta

			// Si tiene conducta asiganada, es alta autom�tica y la fecha de egreso en Unix es igual a la actual, no se da de alta
			if($numcon>0 && isset($bandera) && $bandera=="auto" && $fecha_egreso == $fecha)
			{
				return "no-alta";
			}
			else
			{
				// echo "Alta: ".$paciente." - ".$ingreso."<br>";

				//Consulto si el paciente est� en proceso de traslado
				$qptr = "	SELECT Ubihis, Ubiing, Ubiptr, Eyrsor, Eyrsde, Cconom, Ubisac, Ccocir, ".$wbasedato."_000018.Fecha_data AS FechaIngreso
							FROM ".$wbasedato."_000017, ".$wbasedato."_000018, ".$wbasedato."_000011
							WHERE Ubihis = '".$paciente."'
							AND Ubiing = '".$ingreso."'
							AND Ubiptr = 'on'
							AND Ubihis = Eyrhis
							AND Ubiing = Eyring
							AND Eyrest = 'on'
							AND Eyrsde = Ccocod ";
				$resptr = mysql_query($qptr, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qptr . " - " . mysql_error());
				$rowptr = mysql_fetch_array($resptr);
				$numptr = mysql_num_rows($resptr);

				//////////////////////////////////////////////////////////////////////////////////////////////////
				// 2013-08-20
				// Se define si se valida el proceso de traslado o no
				// Si el paciente est� en cirugia y tiene una antiguedad de m�s de 2 d�as
				// Se puede dar de alta asi est� en proceso de traslado

				// Se inicializa la variable como verdadero (S� se valida proceso de traslado)
				$validaProcesoTraslado = true;

				// Se obtiene la fecha del d�a antes de ayer
				$anteayer = date("Y-m-d",time()-172800);

				// Si el paciente est� en cirugia y tiene una antiguedad de m�s de 2 d�as
				if($rowptr['Ccocir']=='on' && $rowptr['FechaIngreso'] <= $anteayer)
				{
					$validaProcesoTraslado = false;
				}
				//////////////////////////////////////////////////////////////////////////////////////////////////


				if($numptr>0 && $validaProcesoTraslado)
				{
					return "El paciente no se puede dar de alta debido a que est� en proceso de traslado para el servicio ".$rowptr['Cconom'];
				}
				else
				{

					// Consulta de pacientes activos en cl�nica
					$q = " SELECT Ubihis, Ubiing
							 FROM ".$wbasedato."_000018 a
							WHERE Ubimue != 'on'
							  AND Ubiald != 'on' ";

					$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					$num = mysql_num_rows($res);
					$row = mysql_fetch_array($res);


					//Consulto datos en tabla 18 de movhos
					$qubi = "	SELECT Fecha_data, Hora_data, Ubisac
								FROM ".$wbasedato."_000018
								WHERE Ubihis = '".$paciente."'
								AND Ubiing = '".$ingreso."'";
					$resubi = mysql_query($qubi, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qubi . " - " . mysql_error());
					$rowubi = mysql_fetch_array($resubi);
					$numubi = mysql_num_rows($resubi);

					//Consulto el centro de costo actual del paciente
					$qcen = "	SELECT *
								FROM ".$wbasedato."_000018
								WHERE Ubihis = '".$paciente."'
								AND	Ubiing = '".$ingreso."'";
					$rescen = mysql_query($qcen, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qcen . " - " . mysql_error());
					$rowcen = mysql_fetch_array($rescen);

					$registrosFila = obtenerRegistrosFila($qcen);

					//Actualizo tabla 18 de Movhos asignandole los parametros del alta
					$q = "	UPDATE ".$wbasedato."_000018
							SET Ubiald='on', Ubifad='".$fecha."', Ubihad='".$hora."', Ubiuad='".$seguridad."'
							WHERE Ubihis = '".$paciente."'
							AND	Ubiing = '".$ingreso."'";
					$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

					$num_affect = mysql_affected_rows();
					if($num_affect>0)
					{
						//Guardo LOG de actualizaci�n alta en tabla movhos_000018
						$q = "	INSERT INTO log_agenda
												  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
										   VALUES
												  ('".$fecha."', '".$hora."', '".$paciente."', '".$ingreso."', 'Actualizacion tabla ".$wbasedato."_000018', '".$seguridad."', 'Alta ".$bandera."', '".$registrosFila."')";
						$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					}

					//Consulto el c�digo de la conducta de alta en la tabla 35 de HCE
					$qalt = "	SELECT Concod
								FROM ".$wbasedatohce."_000035
								WHERE Conalt = 'on'
								AND Conadm = 'on'";
					$resalt = mysql_query($qalt, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qalt . " - " . mysql_error());
					$rowalt = mysql_fetch_array($resalt);
					$conducta = $rowalt['Concod'];

					// Cosulto si el paciente ya est� registrado en la tabla 22 de Hce
					$q = "	SELECT Mtrhis
							FROM ".$wbasedatohce."_000022
							WHERE Mtrhis = '".$paciente."'
							AND Mtring = '".$ingreso."'";
					$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					$num = mysql_num_rows($res);

					if($num==0)
					{
						$q = "INSERT INTO
								".$wbasedatohce."_000022
									(Medico,Fecha_data,Hora_data,Mtrhis,Mtring,Mtrcci,Mtrmed,Mtrest,Mtrtra,Mtretr,Mtrcon,Mtrcur,Seguridad)
								VALUES
									('".$wbasedatohce."','".$fecha."','".$hora."','".$paciente."','".$ingreso."','".$rowcen['Ubisac']."','','on','off','','".$conducta."','off','C-".$seguridad."')";
						$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

						$num_affect = mysql_affected_rows();
						if($num_affect>0)
						{
							//Guardo LOG de grabaci�n alta en tabla hce_000022
							$q = "	INSERT INTO log_agenda
													  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera)
											   VALUES
													  ('".$fecha."', '".$hora."', '".$paciente."', '".$ingreso."', 'Grabacion tabla hce_000022', '".$seguridad."', 'Alta ".$bandera."')";
							$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
						}
					}
					else
					{
						// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
						$qlog = " SELECT *
									FROM ".$wbasedatohce."_000022
								   WHERE Mtrhis = '".$paciente."'
									 AND Mtring = '".$ingreso."'";
						$registrosFila = obtenerRegistrosFila($qlog);

						$q = "	UPDATE ".$wbasedatohce."_000022
								SET Mtrest='on', Mtrcon='".$conducta."', Mtrcur='off'
								WHERE Mtrhis = '".$paciente."'
								AND	Mtring = '".$ingreso."'";
						$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

						$num_affect = mysql_affected_rows();
						if($num_affect>0)
						{
							//Guardo LOG de actualizaci�n alta en tabla hce_000022
							$q = "	INSERT INTO log_agenda
													  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
											   VALUES
													  ('".$fecha."', '".$hora."', '".$paciente."', '".$ingreso."', 'Actualizacion tabla hce_000022', '".$seguridad."', 'Alta ".$bandera."', '".$registrosFila."')";
							$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
						}
					}

					// Cosulto si el paciente est� registrado en la tabla 20 de Movhos
					$q = "	SELECT *
							FROM ".$wbasedato."_000020
							WHERE Habhis = '".$paciente."'
							AND Habing = '".$ingreso."'";
					$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					$num = mysql_num_rows($res);

					if($num>0)
					{
						$registrosFila = obtenerRegistrosFila($q);

						$disponible = 'off';
						$alistamiento = 'on';

						//Se consulta si el paciente esta en una habitacion tipo cubiculo.
						$q_tiphab =    "  SELECT habcub
											FROM ".$wbasedato."_000020
										   WHERE Habhis = '".$paciente."'
											 AND Habing = '".$ingreso."'";
						$res_tiphab = mysql_query($q_tiphab, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q_tiphab . " - " . mysql_error());
						$row_tiphab = mysql_fetch_array($res_tiphab);

						//Si es asi la habitacion al ser liberada debe quedar disponible.
						if($row_tiphab['habcub'] == 'on'){

							$disponible = 'on';
							$alistamiento = 'off';

						}

						$q = "	UPDATE ".$wbasedato."_000020
								   SET Habhis='', Habing='', Habali='".$alistamiento."', Habdis='".$disponible."', Habfal='".$fecha."', Habhal='".$hora."'
								 WHERE Habhis = '".$paciente."'
								   AND Habing = '".$ingreso."'";
						$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

						$num_affect = mysql_affected_rows();
						if($num_affect>0)
						{
							//Guardo LOG de actualizaci�n alta en tabla hce_000022
							$q = "	INSERT INTO log_agenda
													  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
											   VALUES
													  ('".$fecha."', '".$hora."', '".$paciente."', '".$ingreso."', 'Actualizacion tabla ".$wbasedato."_000020', '".$seguridad."', 'Alta ".$bandera."', '".$registrosFila."')";
							$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
						}
					}

					// 2013-02-26
					// Con esta funci�n se borra el registro de egreso por alta que exista en la tabla 000033 de movimiento hospitalario
					BorrarAltasMuertesAntesDeAgregarNueva($conex, $wbasedato, $paciente, $ingreso, "Egreso existente");

					//2013-04-25
					//Cancelar el servicio de dietas
					cancelar_pedido_alimentacion($paciente, $ingreso, $rowubi['Ubisac'], "Cancelar", 'movhos');


					// Como ya se borro el egreso se comentan las siguientes l�neas

					// Cosulto si el paciente ya est� registrado en la tabla 33 de Movhos
					// $q = "	SELECT *
							// FROM ".$wbasedato."_000033
							// WHERE Historia_clinica = '".$paciente."'
							// AND Num_ingreso = '".$ingreso."'
							// AND Servicio = '".$rowcen['Ubisac']."'
							// AND Tipo_egre_serv = 'ALTA'";
					// $resegr = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					// $numegr = mysql_num_rows($resegr);


					// 2013-05-09
					// Cosulto si el cco de ingreso del paciente registrado en la tabla 000022 de historia clinica electronica (Mtrcci) es hospitalario
					// y en la tabla 000018 de movimiento hospitalario no tiene cco anterior (Ubisan)
					// Si es as� no se debe registrar el egreso en la tabla 000033 de movimiento hospitalario
					$q = "	SELECT Ubihis
							FROM ".$wbasedato."_000018, ".$wbasedatohce."_000022, ".$wbasedato."_000011
							WHERE Ubihis = '".$paciente."'
							AND Ubiing = '".$ingreso."'
							AND Ubihis = Mtrhis
							AND Ubiing = Mtring
							AND Mtrcci = Ccocod
							AND Ccohos = 'on'
							AND Ccoing != 'on'
							AND (TRIM(Ubisan) = '' OR TRIM(Ubisan) = 'NO APLICA')";
					$resegr = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					$numegr = mysql_num_rows($resegr);

					$registraEgreso = true;
					if($numegr>0)
						$registraEgreso = false;

					if($registraEgreso)
					{
						//Registro el egreso en la tabla 33 de Movhos
						$q = "	INSERT INTO
								".$wbasedato."_000033
									(Medico, Fecha_data, Hora_data, Historia_clinica, Num_ingreso, Servicio, Num_ing_serv, Fecha_egre_serv, Hora_egr_serv, Tipo_egre_serv, Dias_estan_serv,Seguridad)
								VALUES
									('".$wbasedato."','".$fecha."','".$hora."','".$paciente."','".$ingreso."','".$rowcen['Ubisac']."','1','".$fecha."','".$hora."','ALTA','1','C-".$seguridad."')";
						$res2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

						$num_affect = mysql_affected_rows();
						if($num_affect>0)
						{
							//Guardo LOG de grabaci�n egreso en tabla movhos_000033
							$q = "	INSERT INTO log_agenda
													  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera)
											   VALUES
													  ('".$fecha."', '".$hora."', '".$paciente."', '".$ingreso."', 'Grabacion tabla ".$wbasedato."_000033', '".$seguridad."', 'Alta ".$bandera."')";
							$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
						}

					}
					// else
					// {
						// $registrosFila = obtenerRegistrosFila($q);

						// $q = "	UPDATE ".$wbasedato."_000033
								// SET Fecha_data='".$fecha."', Hora_data='".$hora."', Fecha_egre_serv='".$fecha."', Hora_egr_serv='".$hora."', Seguridad='C-".$seguridad."'
								// WHERE Historia_clinica = '".$paciente."'
								// AND Num_ingreso = '".$ingreso."'
								// AND Servicio = '".$rowcen['Ubisac']."'
								// AND Tipo_egre_serv = 'ALTA'";
						// $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

						// $num_affect = mysql_affected_rows();
						// if($num_affect>0)
						// {
							// //Guardo LOG de actualizaci�n alta en tabla hce_000022
							// $q = "	INSERT INTO log_agenda
													  // (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
											   // VALUES
													  // ('".$fecha."', '".$hora."', '".$paciente."', '".$ingreso."', 'Actualizacion tabla movhos_000033', '".$seguridad."', 'Alta ".$bandera."', '".$registrosFila."')";
							// $resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
						// }
					// }


					if($res1)
						return "ok";
					else
						return "Ocurri� un error en el proceso. \n Error: ".$res1;
				}
			}
		}
		elseif($tipo_alta == "borrado")
		{
			//Consulto si el paciente est� en proceso de traslado
			$qptr = "	SELECT Ubihis, Ubiing, Ubiptr, Eyrsor, Eyrsde, Cconom, Ccocir, ".$wbasedato."_000018.Fecha_data AS FechaIngreso
						FROM ".$wbasedato."_000017, ".$wbasedato."_000018, ".$wbasedato."_000011
						WHERE Ubihis = '".$paciente."'
						AND Ubiing = '".$ingreso."'
						AND Ubiptr = 'on'
						AND Ubihis = Eyrhis
						AND Ubiing = Eyring
						AND Eyrest = 'on'
						AND Eyrsde = Ccocod ";
			$resptr = mysql_query($qptr, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qptr . " - " . mysql_error());
			$rowptr = mysql_fetch_array($resptr);
			$numptr = mysql_num_rows($resptr);

			//////////////////////////////////////////////////////////////////////////////////////////////////
			// 2013-08-20
			// Se define si se valida el proceso de traslado o no
			// Si el paciente est� en cirugia y tiene una antiguedad de m�s de 2 d�as
			// Se puede dar de alta asi est� en proceso de traslado

			// Se inicializa la variable como verdadero (S� se valida proceso de traslado)
			$validaProcesoTraslado = true;

			// Se obtiene la fecha del d�a antes de ayer
			$anteayer = date("Y-m-d",time()-172800);

			// Si el paciente est� en cirugia y tiene una antiguedad de m�s de 2 d�as
			if($rowptr['Ccocir']=='on' && $rowptr['FechaIngreso'] <= $anteayer)
			{
				$validaProcesoTraslado = false;
			}
			//////////////////////////////////////////////////////////////////////////////////////////////////


			if($numptr>0 && $validaProcesoTraslado)
			{
				return "El paciente no se puede dar de alta debido a que est� en proceso de traslado para el servicio ".$rowptr['Cconom'];
			}
			else
			{

				// 2013-02-22
				// Cosulto si el paciente tiene registros de movimiento hospitalario
				$qeyr = " SELECT Eyrhis
							FROM ".$wbasedato."_000017
						   WHERE Eyrhis = '".$paciente."'
							 AND Eyring = '".$ingreso."'
							 AND Eyrest = 'on'";
				$reseyr = mysql_query($qeyr, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qeyr . " - " . mysql_error());
				$numeyr = mysql_num_rows($reseyr);

				// Si el paciente no ha tenido movimiento hospitalario si se puede hacer el borrado
				if($numeyr==0)
				{
					//Consulto datos en tabla 18 de movhos
					$qubi = "	SELECT Fecha_data, Hora_data, Ubisac
								FROM ".$wbasedato."_000018
								WHERE Ubihis = '".$paciente."'
								AND Ubiing = '".$ingreso."'";
					$resubi = mysql_query($qubi, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qubi . " - " . mysql_error());
					$rowubi = mysql_fetch_array($resubi);
					$numubi = mysql_num_rows($resubi);

					if($numubi>0)
					{
						// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
						$qlog = " SELECT *
									FROM root_000037
								   WHERE Orihis = '".$paciente."'
									 AND Oriing = '".$ingreso."'
									 AND Oriori = '".$wemp_pmla."'";
						$registrosFila = obtenerRegistrosFila($qlog);

						//Actualizo tabla 37 de root
						$q = "	UPDATE root_000037
								SET Fecha_data='".$rowubi['Fecha_data']."', Hora_data='".$rowubi['Hora_data']."', Oriing=Oriing-1
								WHERE Orihis = '".$paciente."'
								AND	Oriing = '".$ingreso."'
								AND	Oriori = '".$wemp_pmla."'";
						$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

						$num_affect = mysql_affected_rows();
						if($num_affect>0)
						{
							//Guardo LOG de actualizaci�n en tabla root_000037
							$q = "	INSERT INTO log_agenda
													  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
											   VALUES
													  ('".$fecha."', '".$hora."', '".$paciente."', '".$ingreso."', 'Actualizacion tabla root_000037', '".$seguridad."', 'Alta ".$bandera." Oriing-1', '".$registrosFila."')";
							$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
						}
					}

					$fechaLog = date('Y-m-d');
					$horaLog = date('H:i:s');

					// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
					$qlog = " SELECT *
								FROM ".$wbasedato."_000016
							   WHERE Inghis = '".$paciente."'
								 AND Inging = '".$ingreso."'";
					$registrosFila = obtenerRegistrosFila($qlog);

					//Borro registro en tabla 16 de Movhos
					$q = "	DELETE
							  FROM ".$wbasedato."_000016
							 WHERE Inghis = '".$paciente."'
							   AND Inging = '".$ingreso."'";
					$res2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

					$num_affect = mysql_affected_rows();
					if($num_affect>0)
					{
						//Guardo LOG de borrado en tabla Movhos 16
						$q = "	INSERT INTO log_agenda
												  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
										   VALUES
												  ('".$fechaLog."', '".$horaLog."', '".$paciente."', '".$ingreso."', 'Borrado tabla ".$wbasedato."_000016', '".$seguridad."', 'Alta ".$bandera."', '".$registrosFila."')";
						$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					}

					// Obtengo los datos actuales del registro para grabarlos en la tabla log_agenda
					$qlog = " SELECT *
								FROM ".$wbasedato."_000018
							   WHERE Ubihis = '".$paciente."'
								 AND Ubiing = '".$ingreso."'";
					$registrosFila = obtenerRegistrosFila($qlog);


					//Borro registro en tabla 18 de Movhos
					$q = "	DELETE
							  FROM ".$wbasedato."_000018
							 WHERE Ubihis = '".$paciente."'
							   AND Ubiing = '".$ingreso."'";
					$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());


					$num_affect = mysql_affected_rows();
					if($num_affect>0)
					{
						//Guardo LOG de borrado en tabla Movhos 18
						$q = "	INSERT INTO log_agenda
												  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
										   VALUES
												  ('".$fechaLog."', '".$horaLog."', '".$paciente."', '".$ingreso."', 'Borrado tabla ".$wbasedato."_000018', '".$seguridad."', 'Alta ".$bandera."', '".$registrosFila."')";
						$resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					}

					// Cosulto si el paciente ya est� registrado en la tabla 22 de Hce
					$q = "	SELECT *
							FROM ".$wbasedatohce."_000022
							WHERE Mtrhis = '".$paciente."'
							AND Mtring = '".$ingreso."'";
					$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					$num = mysql_num_rows($res);

					if($num>0)
					{
						$registrosFila = obtenerRegistrosFila($q);

						$q = "	DELETE
								  FROM ".$wbasedatohce."_000022
								 WHERE Mtrhis = '".$paciente."'
								   AND	Mtring = '".$ingreso."'";
						$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

						$num_affect = mysql_affected_rows();
						if($num_affect>0)
						{
							//Guardo LOG de borrado en tabla hce 22
							$q = "	INSERT INTO log_agenda
													  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
											   VALUES
													  ('".$fechaLog."', '".$horaLog."', '".$paciente."', '".$ingreso."', 'Borrado tabla hce_000022', '".$seguridad."', 'Alta ".$bandera."', '".$registrosFila."')";
							$resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
						}
					}

					// 2013-02-26
					// Con esta funci�n se borra el registro de egreso por alta que exista en la tabla 000033 de movimiento hospitalario
					BorrarAltasMuertesAntesDeAgregarNueva($conex, $wbasedato, $paciente, $ingreso, "Alta ".$bandera);

					//2013-04-25
					//Cancelar el servicio de dietas
					cancelar_pedido_alimentacion($paciente, $ingreso, $rowubi['Ubisac'], "Cancelar", 'movhos');

					// Como ya se borro el egreso se comentan las siguientes l�neas

					// // Cosulto si el paciente ya est� registrado en la tabla 33 de Movhos
					// $q = "	SELECT *
							// FROM ".$wbasedato."_000033
							// WHERE Historia_clinica = '".$paciente."'
							// AND Num_ingreso = '".$ingreso."'
							// AND	Tipo_egre_serv = 'ALTA'";
					// $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					// $num = mysql_num_rows($res);

					// if($num>0)
					// {
						// $registrosFila = obtenerRegistrosFila($q);

						// $q = "	DELETE
								  // FROM ".$wbasedato."_000033
								 // WHERE Historia_clinica = '".$paciente."'
								   // AND	Num_ingreso = '".$ingreso."'
								   // AND	Tipo_egre_serv = 'ALTA'";
						// $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

						// $num_affect = mysql_affected_rows();
						// if($num_affect>0)
						// {
							// //Guardo LOG de borrado en tabla hce 22
							// $q = "	INSERT INTO log_agenda
													  // (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
											   // VALUES
													  // ('".$fechaLog."', '".$horaLog."', '".$paciente."', '".$ingreso."', 'Borrado tabla movhos_000033', '".$seguridad."', 'Alta ".$bandera."', '".$registrosFila."')";
							// $resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
						// }
					// }

					if($res1)
						return "ok";
					else
						return "Ocurri� un error en el proceso. \n Error: ".$res1;
				}
			}
		}
	}
	else
	{
		return "El paciente no se puede dar de alta porque a�n est� activo en el sistema";
	}
}



/********************************************************************************
* CONSULTA EN UNIX LOS PACIENTES QUE NO ESTAN EN URGENCIAS Y LOS DA DE ALTA		*
*********************************************************************************/
function actualizarAltaPacientesUnix($wbasedato,$wbasedatohce,$wemp_pmla,$seguridad,$listados)
{
	global $conex;
	global $conexUnix;

	$ayer = date("Y-m-d",time()-86400);

	// Se consultan los pacientes activos en Unix
	$qact = "SELECT pachis, pacnum
			   FROM inpac ";
		//	   , insercco,outer inemp			// 2012-01-24
		//	  WHERE serccoser = pacser			// 2012-01-24
		//		AND paccer = empcod";			// 2013-01-24
		//		AND pacser = '04'";				// 2011-10-27
		//  	AND pacfec >= '".$ayer."'";		// 2011-10-27
	$rs_act = odbc_do($conexUnix,$qact);

	$k = 0;
	$listados_unix = array();
	// Se asigna la lista de pacientes de urgencias en Unix
	while (odbc_fetch_row($rs_act))
	{
		$listados_unix[$k] = odbc_result($rs_act,1)."-".odbc_result($rs_act,2);
		//echo $listados_unix[$k]."<br>";
		$k++;
	}
	//echo "okkkFN 2 ".$qact;exit();
	//Verifica que la tabla inpac si tenga datos.
	if(count($listados_unix) > 0){
		// Se obtiene los registros que estan en lista de pacientes activos en el programa
		// pero ya no est�n como pacientes activos en Unix
		$altas_unix = array_diff($listados,$listados_unix);
		$conting=0;

		// Se da de alta los pacientes que ya no estan activos en Unix
		foreach ($altas_unix as $j => $value)
		{
			if(isset($altas_unix[$j]) && $altas_unix[$j]!="")
			{
				$paciente_alta = explode("-",$altas_unix[$j]);
				$historia_paciente = $paciente_alta[0];
				$ingreso_paciente = $paciente_alta[1];
				//echo "<br>ALTA: ".$altas_unix[$j];
				altaPacienteUrgencias($wbasedato,$wbasedatohce,$historia_paciente,$ingreso_paciente,$wemp_pmla,"Movhos","auto");
				$conting++;
			}
		}

	}else{

		//Guardo LOG de que no registros en la tabla inpac posiblemente porque esta bloqueada.
		$q = "	INSERT INTO log_agenda
								  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
						   VALUES
								  ('".date('Y-m-d')."', '".date('H:i:s')."', '000000', '0', 'Tabla inpac sin datos', '".$seguridad."', 'inpac sin datos', '')";
		$resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	}

}

/********************************************************************************************
* CONSULTA EN UNIX LOS PACIENTES EN URGENCIAS Y ACTUALIZA EL LISTADO DE PACIENTES ACTIVOS 	*
********************************************************************************************/
function actualizarPacientesUnix($wbasedato,$wbasedatohce,$wemp_pmla,$seguridad,$listados)
{
	global $conex;
	global $conexUnix;

	if($conexUnix)
	{
		//echo "actualizarAltaPacientesUnix 1 ".date("Y-m-d H:i:s")."<br>";
		// Se llama a la funcion para dar de alta a los pacientes que no est�n en urgencias de Unix
		actualizarAltaPacientesUnix($wbasedato,$wbasedatohce,$wemp_pmla,$seguridad,$listados);
		//echo "actualizarAltaPacientesUnix 2 ".date("Y-m-d H:i:s")."<br>";
		// Se llama a la funcion para actualizar datos de pacientes en cl�nica
		actualizarDatosPacientes($wbasedato,$wemp_pmla,$seguridad,$listados);
		//echo "actualizarDatosPacientes 3 ".date("Y-m-d H:i:s")."<br>";
		//exit();
		$ayer = date("Y-m-d",time()-86400);

		// Se consultan los pacientes activos en urgencias de Unix
		$qact = "SELECT pachis, pacnum, pacced, pactid, pacser
				   FROM inpac ";
			//	   , insercco,outer inemp			// 2012-01-24
			//	  WHERE serccoser = pacser			// 2012-01-24
			//	    AND paccer = empcod";			// 2012-01-24
			//		AND pacser = '04'";				// 2011-10-27
			//		AND pacfec >= '".$ayer."'";		// 2011-10-27
		$rs_act = odbc_do($conexUnix,$qact);
		$conting = 0;
		//echo "odbc_do inpac 4 ".date("Y-m-d H:i:s")."<br>";
		//echo "okk444";exit();
		// Ciclo para actualizar el listado de pacientes activos en urgencias
		while (odbc_fetch_row($rs_act))
		{
			$codCco="";
			$historia_paciente = odbc_result($rs_act,1);
			$ingreso_paciente = odbc_result($rs_act,2);
			$cco_paciente = odbc_result($rs_act,5);
			$paciente_unix = $historia_paciente."-".$ingreso_paciente;
			//echo "INGRESO: ".$paciente_unix."<br>";
			// Si la historia cl�nica obtenida de Unix no est� en listado de pacientes, ingr�sela.
			if (!in_array($paciente_unix,$listados))
			{
				$codCco = obtenerCcoMatrix($wbasedato,$cco_paciente,$wemp_pmla,$seguridad);
				// Si existe relaci�n de cco - servicio (ccoseu vac�o) en la tabla 11 de movimiento hospitalario
				if($codCco!="" && $codCco!=" ")
				{
					ingresarPacientesUrgencias($wbasedato,$wbasedatohce,$codCco,$historia_paciente,$wemp_pmla,$seguridad);
				}
				else
				{
					// Se busca la relaci�n de cco - servicio en la tabla insercco de Unix
					$qser = " SELECT serccocco
								FROM insercco
							   WHERE serccoser = '".$cco_paciente."'
							     AND serccocco is not null
							   UNION
							  SELECT ' ' AS serccocco
								FROM insercco
							   WHERE serccoser = '".$cco_paciente."'
								 AND serccocco is null
							";
					$rs_ser = odbc_do($conexUnix,$qser);

					if(odbc_fetch_row($rs_ser))
					{
						$codCco = odbc_result($rs_ser,1);
						if($codCco!=' ')
							ingresarPacientesUrgencias($wbasedato,$wbasedatohce,$codCco,$historia_paciente,$wemp_pmla,$seguridad);
					}
				}
				$conting++;
			}else{

				$paciente->historiaClinica = $historia_paciente;
				$paciente->ingresoClinica = $ingreso_paciente;
				$pacienteUnix = consultarPacienteUnix($paciente);  //Informacion del Paciente en Unix.

				//print_r($pacienteUnix);
				//Consulta si el paciente tiene responsable diferente en unix y matrix.
				$responsable_diferente = consultar_responsable_diferente_unix_matrix($paciente, $pacienteUnix);

				$resp_matrix = "  SELECT Ingres
									FROM ".$wbasedato."_000016
								   WHERE Inghis = '".$historia_paciente."'
									 AND Inging = '".$ingreso_paciente."' ";
				$resmatrix = mysql_query($resp_matrix,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $resp_matrix . " - " . mysql_error());
				$row_matrix = mysql_fetch_array($resmatrix);
				$responsable_matrix = $row_matrix['Ingres'];

				$pacienteMatrix->historiaClinica = $historia_paciente;
				$pacienteMatrix->ingresoHistoriaClinica = $ingreso_paciente;

				//Si el responsable es diferente hago la actualizacion de responsable.
				if($responsable_diferente['diferentes'] == 'on')
				{
					actualizarResponsablePaciente($pacienteMatrix, $pacienteUnix, $responsable_diferente['anterior'], $responsable_diferente['nuevo'], 'on');
				}
			}
		}
	}
}

// Borra los registros anteriores a $ndias en la tabla de $tblog
function borrarLogsAntiguos($tblog,$ndias)
{
	global $conex;

	$nseg = 86400*$ndias;
	$antiguos = date("Y-m-d",time()-$nseg);

	// Borra registros antiguos de la tabla de log
	$q = "	DELETE FROM ".$tblog."
			WHERE Fecha < '".$antiguos."'";
	$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
}

/*********************************************************************
************** LISTADO DE PACIENTES INGRESADOS  **********************
*********************************************************************/

function agendaPacientesUrgencias($wbasedato,$wbasedatohce,$wemp_pmla,$seguridad)
{

	$user="";
	$wuser="";

	$usuario = new Usuario();

	$usuario->codigo = $wuser;


	//Variable para determinar la empresa
	if(!isset($wemp_pmla))
	{
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
	}

	global $conex;
	global $wcliame;

	// Consulta de pacientes activos en cl�nica
	// Se agrega en el GROUP BY el ingreso, para que tenga en cuenta historias con varios ingresos activos, osea con uniald en off, esto permitira trabajar con los dos ingresos
	// en el arreglo $listados. Diciembre 19 de 2013.
	$q = "SELECT DISTINCT
		a.Fecha_data fing, a.Hora_data hing, Ubihis, Pactid, Pacced, Pacno1, Pacno2, Pacap1, Pacap2, Ingres, Ingnre, Ubiing
	FROM
		".$wbasedato."_000018 a, ".$wbasedato."_000016 b, root_000036, root_000037, ".$wcliame."_000101 c
	WHERE
			Ubimue != 'on'
		AND Ubiald != 'on'
		AND Ubihis = b.Inghis
		AND Ubiing = b.Inging
		AND Ubihis = Orihis
		AND Oriori = '".$wemp_pmla."'
		AND Oriced = Pacced
		AND Oritid = Pactid
        AND c.inghis = Ubihis
        AND c.ingnin = Ubiing
        AND c.ingunx = 'on'
	GROUP BY Ubihis, Ubiing
	ORDER BY fing DESC, hing DESC";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	$row = mysql_fetch_array($res);
	$listados = array();
	$i=0;
	while ($i<$num)
	{
		// Arreglo para guardar las historias de los pacientes activos
		$listados[$i] = $row['Ubihis']."-".$row['Ubiing'];
		//echo $listados[$i]."<br>";
		// Arreglo para guardar los ingresos de los pacientes activos
		//$listadosing[$i] = $row['Ubiing'];
		$i++;
		$row = mysql_fetch_array($res);
	}

	// Borra los registros antiguos en la tabla de log
	// Se aumenta el tiempo de borrado de datos en la tabla log_agenda a 30 dias, estaba en 15 dias. Jonatan Lopez 19 Diciembre 2013.
	borrarLogsAntiguos("log_agenda",30);

	// Trae desde Unix todos los pacientes activos que deben ser ingresados a Matrix
	actualizarPacientesUnix($wbasedato,$wbasedatohce,$wemp_pmla,$seguridad,$listados);

}

//ANTES DE INSERTAR UNA ALTA O UNA MUERTE PARA UN PACIENTE SE CONSULTA SI YA TUVO ALTA O MUERTE Y SE ELIMINAN
function BorrarAltasMuertesAntesDeAgregarNueva($conex, $wbasedato, $whis, $wing, $bandera)
{
	$user_session = explode('-',$_SESSION['user']);
	$seguridad = $user_session[1];

	$q = "	SELECT *
			FROM ".$wbasedato."_000033
			WHERE Historia_clinica = '".$whis."'
			AND Num_ingreso = '".$wing."'
			AND Tipo_egre_serv REGEXP 'MUERTE MAYOR A 48 HORAS|MUERTE MENOR A 48 HORAS|ALTA' ";

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

			$query_para_log = "	SELECT *
				FROM ".$wbasedato."_000033
				WHERE Historia_clinica = '".$whis."'
				AND Num_ingreso = '".$wing."'
				AND Tipo_egre_serv = '".$wtipoEgresoABorrar."'";
			$registrosFila = obtenerRegistrosFila($query_para_log);

			$q ="	DELETE FROM ".$wbasedato."_000033
					 WHERE Historia_clinica = '".$whis."'
					   AND Num_ingreso = '".$wing."'
					   AND Tipo_egre_serv = '".$wtipoEgresoABorrar."'";
			$res = mysql_query($q,$conex);

			$num_affect = mysql_affected_rows();
			if($num_affect>0)
			{

				$q = " UPDATE ".$wbasedato."_000038 "
					."    SET Ciemmay = '".$muerteMayor."',"
					."  	  Ciemmen = '".$muerteMenor."',"
					."  	  Cieeal = '".$egresosAlta."',"
					."  	  Cieegr = '".$cant_egresos."',"
					."  	  Cieocu = '".$cant_camas_ocupadas."',"
					."  	  Ciedis = '".$cant_camas_disponibles."'"
					."  WHERE Fecha_data = '".$wfecha."'"
					."    AND Cieser = '".$wcco."'"
					."  LIMIT 1 ";

				$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

				//Guardo LOG de borrado en tabla movhos 33 - Activacion paciente
				$q = "	INSERT INTO log_agenda
										  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
								   VALUES
										  ('".date('Y-m-d')."', '".date('H:i:s')."', '".$whis."', '".$wing."', 'Borrado tabla ".$wbasedato."_000033', '".$seguridad."', '".$bandera."', '".$registrosFila."')";
				$resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			}
		}
	}
}

// Retorna el c�digo del centro de costo en Matrix
function obtenerCcoMatrix($wbasedato,$codCco,$wemp_pmla,$seguridad)
{
	global $conex;
	$cco = "";
	$conex = obtenerConexionBD("matrix");

	// Consulto si hay un c�digo asociado en la tabla 11 de movhos
	$qcco = "	SELECT Ccocod
				  FROM ".$wbasedato."_000011
				 WHERE Ccoseu  = '".$codCco."'
				   AND Ccoing = 'on'
				   AND Ccoest = 'on'";
	$rescco = mysql_query($qcco, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qcco . " - " . mysql_error());
	$numcco = mysql_num_rows($rescco);
	if($numcco>0)
	{
		$rowcco = mysql_fetch_array($rescco);
		$cco = $rowcco['Ccocod'];
	}
	return $cco;
}

// Actualiza los datos de los pacientes en cl�nica
function actualizarDatosPacientes($wbasedato,$wemp_pmla,$seguridad,$listados)
{
	global $conex;

	for($i=0;$i<count($listados);$i++)
	{
		$paciente = new pacienteDTO();

		// Obtengo historia cl�nica e ingreso de paciente
		$datos_paciente = explode("-",$listados[$i]);
		$whistoria = $datos_paciente[0];
		$wingreso = $datos_paciente[1];

		$paciente->historiaClinica  = $whistoria;

		$pacienteUnix = consultarPacienteUnix($paciente);  //Paciente Unix
		if(isset($pacienteUnix->nombre1) && isset($pacienteUnix->ingresoHistoriaClinica))
		{
			// Consulta si existe el paciente en las tablas root_000036, root_000037
			// Con base en histora y �ltimo ingreso
			$pacienteMatrix = consultarInfoPacientePorHistoria($conex, $paciente->historiaClinica,$wemp_pmla);

			$pacienteEnTablaUnica = false;	// Indica si tipo identificacion e identificaci�n existen en tabla root_000036
			$pacienteEnTablaIngresos = false;	// Indica si historia y origen existen en tabla root_000037

			// En tabla root_000037 borro historia asociada en matrix a la c�dula de unix
			// siempre y cuando esta historia sea diferente a la asociada en Unix
			borrarHistoriaDiferenteUnix($pacienteUnix, $wemp_pmla, $seguridad);

			if(isset($pacienteMatrix->documentoIdentidad))
			{
				// Se comenta porque no es necesario usar las funciones que usan esta variable
				// 2011-11-29
				//if($pacienteMatrix->documentoIdentidad != $pacienteUnix->documentoIdentidad || $pacienteMatrix->tipoDocumentoIdentidad != //$pacienteUnix->tipoDocumentoIdentidad)
				//{
					//$mismoDocumentoIdentidad = false;
				//}
			}
			else
			{
				$pacienteMatrix->historiaClinica = $whistoria;
				$pacienteMatrix->ingresoHistoriaClinica = $pacienteUnix->ingresoHistoriaClinica;
				$pacienteMatrix->documentoIdentidad = $pacienteUnix->documentoIdentidad;
				$pacienteMatrix->tipoDocumentoIdentidad = $pacienteUnix->tipoDocumentoIdentidad;
			}

			// Consulto por historia y origen si existe en tabla root_000037
			$pacienteEnTablaIngresos = existeEnTablaIngresos($pacienteUnix, $wemp_pmla);
			// Consulto por tipo identificacion e identificaci�n si existe en tabla root_000036
			$pacienteEnTablaUnica = existeEnTablaUnicaPacientes($pacienteUnix);

			//Ingreso de datos en tabla 36 de root
			if(!$pacienteEnTablaUnica)
			{
				insertarPacienteTablaUnica($pacienteUnix,$seguridad);
			}
			else
			{
				actualizarDatosPacienteTablaUnica($pacienteMatrix,$pacienteUnix);
			}

			//Ingreso de datos en tabla 37 de root
			if(!$pacienteEnTablaIngresos)
			{
				insertarIngresoPaciente($pacienteUnix, $wemp_pmla, $seguridad);
			}
			else
			{
				actualizarIngresoPaciente($pacienteMatrix, $pacienteUnix, $wemp_pmla);
			}

		}

	}
}

// Ingresa un paciente a la lista de pacientes en urgencias
function ingresarPacientesUrgencias($wbasedato,$wbasedatohce,$codCco,$whistoria,$wemp_pmla,$seguridad)
{
	global $conex;

	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
	$winstitucion = $institucion->nombre;

	// Grabaci�n de ingreso de paciente

	$conex = obtenerConexionBD("matrix");

	$paciente = new pacienteDTO();

	$paciente->historiaClinica = $whistoria;

	$pacienteUnix = consultarPacienteUnix($paciente);  //Paciente Unix
	if(isset($pacienteUnix->nombre1) && isset($pacienteUnix->ingresoHistoriaClinica))
	{
		// Consulta si existe el paciente en las tablas root_000036, root_000037 y movhos_000018
		// Con base en histora y �ltimo ingreso
		$pacienteMatrix = consultarInfoPacientePorHistoria($conex, $paciente->historiaClinica,$wemp_pmla);

		$ingresoAnterior = "";

		if(!$pacienteMatrix || !isset($pacienteMatrix->historiaClinica))
		{
			$pacienteMatrix = $pacienteUnix;
		}
		else
		{
			if(isset($pacienteMatrix->ingresoHistoriaClinica))
			{
				$ingresoAnterior = $pacienteMatrix->ingresoHistoriaClinica;
			}
			else
			{
				$pacienteMatrix->ingresoHistoriaClinica = $ingresoAnterior;
			}

			if($pacienteUnix)
			{
				// Si el ingreso encontrado en Matrix (movhos_000018) es menor
				if($pacienteMatrix->ingresoHistoriaClinica=="")
				{
					$pacienteMatrix->ingresoHistoriaClinica = $pacienteUnix->ingresoHistoriaClinica;
				}
				elseif((($pacienteMatrix->ingresoHistoriaClinica*1)) < (($pacienteUnix->ingresoHistoriaClinica)*1))
				{
					if(isset($pacienteMatrix->historiaClinica) && !empty($pacienteMatrix->historiaClinica))
					{
						// Consulto si el ingreso no tiene alta definitiva
						$qalt = "	SELECT Ubiald
									FROM ".$wbasedato."_000018
									WHERE Ubihis  = '".$pacienteMatrix->historiaClinica."'
									AND	  Ubiing = '".$pacienteMatrix->ingresoHistoriaClinica."'
									AND	  Ubiald != 'on'";
						$resalt = mysql_query($qalt, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qalt . " - " . mysql_error());
						$numalt = mysql_num_rows($resalt);
						// Define si le doy de alta o no 1=no tiene alta 0=tiene alta
						// Si no tiene alta definitiva le doy de alta
						if($numalt>0)
							@altaPacienteUrgencias($wbasedato,$wbasedatohce,$pacienteMatrix->historiaClinica,$pacienteMatrix->ingresoHistoriaClinica,$wemp_pmla,"Movhos","Ingreso");

						$pacienteMatrix->ingresoHistoriaClinica = $pacienteUnix->ingresoHistoriaClinica;
						//$pacienteMatrix->nombre1 = "";
					}
				}
				elseif((($pacienteMatrix->ingresoHistoriaClinica)*1) >= (($pacienteUnix->ingresoHistoriaClinica)*1))
				{
					if(isset($pacienteMatrix->historiaClinica) && !empty($pacienteMatrix->historiaClinica))
					{
						borraIngresosMayores($wbasedato,$wbasedatohce,$pacienteMatrix->historiaClinica,$pacienteUnix->ingresoHistoriaClinica,$wemp_pmla,"Movhos","BorradoIngresoMayor",$pacienteUnix->fechaIngreso,$pacienteUnix->horaIngreso);

						$pacienteMatrix->ingresoHistoriaClinica = $pacienteUnix->ingresoHistoriaClinica;
						//$pacienteMatrix->nombre1 = "";
					}
				}
			}
		}

		// Si ya se encuentra admitido va a consultar la informaci�n
		// La guia es la tabla 18, si esta ahi, YA SE CONSIDERA INGRESADO
		if(!isset($pacienteMatrix->historiaClinica) || empty($pacienteMatrix->historiaClinica))
		{
			$pacienteMatrix->historiaClinica = $pacienteUnix->historiaClinica;
		}

		$pacienteConResponsablePaciente = false;	// Indica si historia e ingreso existen en tabla movhos_000016
		// Consulto por historia e ingreso si existe en tabla movhos_000016
		$pacienteConResponsablePaciente = existeEnTablaResponsables($pacienteUnix, $wemp_pmla);

		//Ingreso de datos en tabla 16 de movhos
		if(!$pacienteConResponsablePaciente)
		{
			insertarResponsablePaciente($pacienteUnix, $wemp_pmla, $seguridad);
		}
		else
		{
			actualizarResponsablePaciente($pacienteMatrix, $pacienteUnix, "", "", "off");
		}

		// Busca por historia e ingreso en la tabla movhos_000018 si existen registros
		$pacienteIngresado = pacienteIngresado($pacienteMatrix);
		// Busca por historia e ingreso en la tabla hce_000022 si existen registros
		$pacienteIngresadoHce = pacienteIngresadoHce($pacienteMatrix);

		// echo "Ingreso: ".$pacienteUnix->historiaClinica." - ".$pacienteMatrix->ingresoHistoriaClinica."<br>";


		if(!isset($pacienteMatrix->ingresoHistoriaClinica) or !$pacienteIngresado or !$pacienteIngresadoHce)
		{
			if(!isset($pacienteMatrix->ingresoHistoriaClinica) or !$pacienteIngresado)
			{
				$pacienteEnTablaUnica = false;	// Indica si tipo identificacion e identificaci�n existen en tabla root_000036
				$pacienteEnTablaIngresos = false;	// Indica si historia y origen existen en tabla root_000037

				// Se comenta porque no es necesario usar las funciones que usan esta variable - 2011-11-29
				//$mismoDocumentoIdentidad = true;	// Indica si paciente unix y paciente matrix tienen el mismo documento

				// En tabla root_000037 borro historia asociada en matrix a la c�dula de unix
				// siempre y cuando esta historia sea diferente a la asociada en Unix
				borrarHistoriaDiferenteUnix($pacienteUnix, $wemp_pmla, $seguridad);

				// Si exite documento de identidad en matrix verifico que sea el mismo de Unix
				if(isset($pacienteMatrix->documentoIdentidad))
				{
					// Se comenta porque no es necesario usar las funciones que usan esta variable
					/* 2011-11-29
					if($pacienteMatrix->documentoIdentidad != $pacienteUnix->documentoIdentidad || $pacienteMatrix->tipoDocumentoIdentidad != $pacienteUnix->tipoDocumentoIdentidad)
					{
						$mismoDocumentoIdentidad = false;
					}
					*/
				}
				else
				{
					$pacienteMatrix->historiaClinica = $whistoria;
					$pacienteMatrix->ingresoHistoriaClinica = $pacienteUnix->ingresoHistoriaClinica;
					$pacienteMatrix->documentoIdentidad = $pacienteUnix->documentoIdentidad;
					$pacienteMatrix->tipoDocumentoIdentidad = $pacienteUnix->tipoDocumentoIdentidad;
				}

				// Consulto por historia y origen si existe en tabla root_000037
				$pacienteEnTablaIngresos = existeEnTablaIngresos($pacienteUnix, $wemp_pmla);
				// Consulto por tipo identificacion e identificaci�n si existe en tabla root_000036
				$pacienteEnTablaUnica = existeEnTablaUnicaPacientes($pacienteUnix);

				// Actualiza documento de identidad en tabla root_000037
				// Se comenta porque la actuailzaci�n de documento en tabla root_000037 se realiza en las siguientes funciones
				/* 2011-11-29
				if(!$mismoDocumentoIdentidad)
					actualizarDocumentoPacienteTablaIngresos($pacienteMatrix,$pacienteUnix,$wemp_pmla);
				*/

				// Actualiza documento de identidad en tabla root_000036
				// Se comenta porque la actuailzaci�n de documento en tabla root_000036 se realiza en las siguientes funciones
				/* 2011-11-29
				if(!$mismoDocumentoIdentidad)
					actualizarDocumentoPacienteTablaUnica($pacienteMatrix,$pacienteUnix);
				*/

				//Ingreso de datos en tabla 36 de root
				if(!$pacienteEnTablaUnica)
				{
					insertarPacienteTablaUnica($pacienteUnix,$seguridad);
				}
				else
				{
					actualizarDatosPacienteTablaUnica($pacienteMatrix,$pacienteUnix);
				}

				//Ingreso de datos en tabla 37 de root
				if(!$pacienteEnTablaIngresos)
				{
					insertarIngresoPaciente($pacienteUnix, $wemp_pmla, $seguridad);
				}
				else
				{
					//$pacienteMatrix->ingresoHistoriaClinica = $ingresoAnterior;
					actualizarIngresoPaciente($pacienteMatrix, $pacienteUnix, $wemp_pmla);
					$pacienteMatrix->ingresoHistoriaClinica = $pacienteUnix->ingresoHistoriaClinica;
				}
			}

			//Proceso de movimiento hospitalario
			$ingresoPaciente = new ingresoPacientesDTO();

			$ingresoPaciente->historiaClinica = $pacienteUnix->historiaClinica;
			$ingresoPaciente->ingresoHistoriaClinica = $pacienteUnix->ingresoHistoriaClinica;
			$ingresoPaciente->servicioActual = $codCco;
			$ingresoPaciente->habitacionActual = "";

			$ingresoPaciente->fechaIngreso = $pacienteUnix->fechaIngreso;
			$ingresoPaciente->horaIngreso = $pacienteUnix->horaIngreso;

			$ingresoPaciente->fechaAltaProceso = "0000-00-00";
			$ingresoPaciente->horaAltaProceso = "00:00:00";
			$ingresoPaciente->fechaAltaDefinitiva = "0000-00-00";
			$ingresoPaciente->horaAltaDefinitiva = "00:00:00";

			if(esCirugia($codCco))
				$ingresoPaciente->enProcesoTraslado = "on";
			else
				$ingresoPaciente->enProcesoTraslado = "off";
			$ingresoPaciente->altaDefinitiva = "off";
			$ingresoPaciente->altaEnProceso = "off";

			$ingresoPaciente->usuario = "A-".$wbasedato;

			//Grabar ingreso paciente
			$ingresoPaciente->servicioAnterior = "";
			$ingresoPaciente->habitacionAnterior = "";

			if(!$pacienteIngresado or !$pacienteIngresadoHce)
			{
				if(!$pacienteIngresado)
					grabarIngresoPaciente($ingresoPaciente, $seguridad);

				if(!$pacienteIngresadoHce)
					registrarIngresoPaciente($ingresoPaciente, $seguridad);

				return 'ok';
			}
			else
			{
				return 'existente';
			}

		}
		else
		{
			return 'existente';
		}
	}
	else
	{
		return 'no-urg';
	}
}

/**********************************************************************
* Actualizar medicamentos y materiales Unix segun reglas de Matrix ( segun Manuales de cirugia)
---Autor: Felipe Alvarez sanchez
---Descripcion:  Esta funcion actualiza los medicamentos cuando se graban por el sistema de facturacion
--				 inteligente
**********************************************************************/
function actualizarMedicamentos($wemp_pmla)
{ 
	// Trnasferida a include/root/kron_procesos_ERP.php
}
//--------------------------------------------------------------------------------------------
// --> 	Funcion que busca que turnos de urgencias se hayan perdido, para volver a asign�rselo
//		al paciente. Jerson Trujillo 2016-04-11
//--------------------------------------------------------------------------------------------
function recuperarTurnosDeUrgencias()
{
	// Transferida a include/root/kron_procesos_ERP
}


/**
 * [queryCargoUnix: Se crea el query que actualizar� el ingreso en facardet para que quede el ingreso matrix]
 * @param  [type] $tipo_cargo             [description]
 * @param  [type] $arr_FiltroSql          [description]
 * @param  [type] $fuente_insumo          [description]
 * @param  [type] $drodocdoc              [description]
 * @param  [type] $Tcarlin                [description]
 * @param  [type] $historia_rep           [description]
 * @param  [type] $ingreso_rep            [description]
 * @param  [type] $wingreso_unx           [description]
 * @param  [type] $reg_unix               [description]
 * @param  [type] &$proceso_actualizacion [description]
 * @return [type]                         [description]
 */
function queryCargoUnix($tipo_cargo, $arr_FiltroSql, $fuente_insumo, $drodocdoc, $Tcarlin, $historia_rep, $ingreso_rep, $wingreso_unx, $reg_unix, &$proceso_actualizacion)
{
    $selectfacar = "";
    // if($tipo_cargo == 'insumo')
    {
        $selectfacar = "UPDATE  FACARDET
                                SET cardetnum = '{$ingreso_rep}'
                        WHERE   cardetfue = '{$fuente_insumo}'
                                AND cardetdoc = '{$drodocdoc}'
                                AND cardethis = '{$historia_rep}'
                                AND cardetnum = '{$wingreso_unx}'
                                AND cardetlin = '{$Tcarlin}'";
        // echo "<pre>selectfacar:".print_r($selectfacar,true)."</pre><br><br>";
    }
    return $selectfacar;
}

/**
 * [actualizarConsecutivosRipsCargos: Los RIPS debieron quedar con el consecutivo del ingreso activo al momento de grabar el cargo, pero en esta funci�n se actualizan con el consecutivo
 *                                 RIPS del ingreso que se grab� desde matrix.]
 * @param  [type] $conex                       [description]
 * @param  [type] $conexUnix                   [description]
 * @param  [type] $wbasedato                   [description]
 * @param  [type] $arr_parametros              [description]
 * @param  [type] $fuente_insumo               [description]
 * @param  [type] $drodocdoc                   [description]
 * @param  [type] $linea                       [description]
 * @param  [type] $codigo_insumo_mx            [description]
 * @param  [type] $arr_FiltroSql               [description]
 * @param  [type] $historia_rep                [description]
 * @param  [type] $ingreso_rep                 [description]
 * @param  [type] $wingreso_unx                [description]
 * @param  [type] $row                         [description]
 * @param  [type] $arr_DocumentosFuentes       [description]
 * @param  [type] $Tcardoi                     [description]
 * @param  [type] &$data                       [description]
 * @param  [type] &$arr_cargosReporte          [description]
 * @param  [type] &$arr_cargosHisFactNotas     [description]
 * @param  [type] &$arr_consultas_por_historia [description]
 * @param  [type] &$proceso_actualizacion      [description]
 * @param  [type] &$arr_RIPS_msate             [description]
 * @return [type]                              [description]
 */
function actualizarConsecutivosRipsCargos($conex, $conexUnix, $wbasedato, $arr_parametros, $fuente_insumo, $drodocdoc, $linea, $codigo_insumo_mx, $arr_FiltroSql, $historia_rep, $ingreso_rep, $wingreso_unx, $row, $arr_DocumentosFuentes, $Tcardoi , &$data, &$arr_cargosReporte, &$arr_cargosHisFactNotas, &$arr_consultas_por_historia, &$proceso_actualizacion, &$arr_RIPS_msate)
{
    // Consulto los consecutivos de RIPS para los ingresos de la historia en unix
    if(!array_key_exists($historia_rep, $arr_RIPS_msate))
    {
        $arr_RIPS_msate[$historia_rep] = array();

        // Para guardar consecutivo RIPS del ingreso matrix
        if(!array_key_exists($ingreso_rep, $arr_RIPS_msate[$historia_rep]))
        {
            $arr_RIPS_msate[$historia_rep][$ingreso_rep] = array("ateips"=>"", "atedoc"=>"");
        }

        // Para guardar consecutivo RIPS del ingreso unix
        if(!array_key_exists($wingreso_unx, $arr_RIPS_msate[$historia_rep]))
        {
            $arr_RIPS_msate[$historia_rep][$wingreso_unx] = array("ateips"=>"", "atedoc"=>"");
        }

        $sqlMsate= "SELECT  ateips, atedoc, ateing
                    FROM    msate
                    WHERE   atehis = '{$historia_rep}'
                            AND ateing IN ('{$ingreso_rep}', '{$wingreso_unx}')";
        if($resMsate = @odbc_exec($conexUnix, $sqlMsate))
        {
            while (odbc_fetch_row($resMsate))
            {
                $sel_ateips = odbc_result($resMsate,"ateips");
                $sel_atedoc = odbc_result($resMsate,"atedoc");
                $sel_ateing = odbc_result($resMsate,"ateing");

                $arr_RIPS_msate[$historia_rep][$sel_ateing]["ateips"] = $sel_ateips;
                $arr_RIPS_msate[$historia_rep][$sel_ateing]["atedoc"] = $sel_atedoc;
            }
        }
        else
        {
            $proceso_actualizacion = false;
            // echo odbc_errormsg()." > ".$sqlMsate;
            $desc_error = "No se pudo consultar consecutivo RIPS: > ".PHP_EOL.odbc_errormsg();
            registroLogError($conex, $wbasedato, $sqlMsate, 'unix', $desc_error);

            // Se eliminan las posiciones de ingreso de arreglo porque si hubo un error no deben quedar seteados los campos de ips y doc de mstate en el array.
            unset($arr_RIPS_msate[$historia_rep][$ingreso_rep]);
            unset($arr_RIPS_msate[$historia_rep][$wingreso_unx]);
        }
    }

    if(array_key_exists($ingreso_rep, $arr_RIPS_msate[$historia_rep]) && array_key_exists($wingreso_unx, $arr_RIPS_msate[$historia_rep])
        && $arr_RIPS_msate[$historia_rep][$ingreso_rep]["ateips"] != '' && $arr_RIPS_msate[$historia_rep][$ingreso_rep]["atedoc"] != ''
        && $arr_RIPS_msate[$historia_rep][$wingreso_unx]["ateips"] != '' && $arr_RIPS_msate[$historia_rep][$wingreso_unx]["atedoc"] != '')
    {
        $ms_ateips_mx = $arr_RIPS_msate[$historia_rep][$ingreso_rep]["ateips"];
        $ms_atedoc_mx = $arr_RIPS_msate[$historia_rep][$ingreso_rep]["atedoc"];//Documento que reemplazar� atedoc por el consecutivo correspondiente al ingreso matrix.

        $ms_ateips_unx = $arr_RIPS_msate[$historia_rep][$wingreso_unx]["ateips"];
        $ms_atedoc_unx = $arr_RIPS_msate[$historia_rep][$wingreso_unx]["atedoc"];//Documento que se debe reemplazar por $ms_atedoc_mx del ingreso matrix

        // Se modifican los consecutivos de RIPS en el detalle del ingreso unix para que se asigne al detalle el consecutivo RIPS del ingreso matrix.
        // Este query aplica para medicamentos
        $sqlMsdro= "UPDATE  msdro
                            SET drodoc = '{$ms_atedoc_mx}'
                    WHERE   droips = '{$ms_ateips_unx}'
                            AND drodoc = '{$ms_atedoc_unx}'
                            AND drofte = '{$fuente_insumo}'
                            AND drodto = '{$drodocdoc}'
                            AND droite = '{$linea}'";
        if($resivUdt = @odbc_exec($conexUnix, $sqlMsdro))
        {
            //
        }
        else
        {
            $proceso_actualizacion = false;
            // echo odbc_errormsg()." > ".$sqlMsdro;
            $desc_error = "No se pudo actualizar consecutivo RIPS en Msdro: > ".PHP_EOL.odbc_errormsg();
            registroLogError($conex, $wbasedato, $sqlMsdro, 'unix', $desc_error);
        }

        // Se modifican los consecutivos de RIPS en el detalle del ingreso unix para que se asigne al detalle el consecutivo RIPS del ingreso matrix.
        // Este query aplica para materiales
        $sqlMsotr= "UPDATE  msotr
                            SET otrdoc = '{$ms_atedoc_mx}'
                    WHERE   otrips = '{$ms_ateips_unx}'
                            AND otrdoc = '{$ms_atedoc_unx}'
                            AND otrfte = '{$fuente_insumo}'
                            AND otrdto = '{$drodocdoc}'
                            AND otrite = '{$linea}'";
        if($resivUdt = @odbc_exec($conexUnix, $sqlMsotr))
        {
            //
        }
        else
        {
            $proceso_actualizacion = false;
            // echo odbc_errormsg()." > ".$sqlMsotr;
            $desc_error = "No se pudo actualizar consecutivo RIPS en Msotr: > ".PHP_EOL.odbc_errormsg();
            registroLogError($conex, $wbasedato, $sqlMsotr, 'unix', $desc_error);
        }
    }
    else
    {
        $proceso_actualizacion = false;
        // echo "<br>".utf8_decode("Falt� alg�n consecutivo RIPS por leer<br>");
        $desc_error = "Falt� un consecutivo RIPS por leer o se present� alg�n problema no especificado al consultar consecutivo para ingresos [({$ingreso_rep}), ({$wingreso_unx})] en msate historia: [{$historia_rep}] ".PHP_EOL;
        registroLogError($conex, $wbasedato, "", 'unix', $desc_error);
    }
}


/**
 * [consultarCargoInsumoPorLinea: Por cada cargo (n�mero de l�nea) se realiza una actualizaci�n en facardet para cambiar el n�mero de ingreso activo al momento de grabar el cargo en unix
 *                                 por el n�mero de ingreso con el que se gener� el cargo en matrix, si la actualizaci�n fue exitosa entonces tambi�n se procede a actualizar el cargo en RIPS
 *                                 con el consecutivo de RIPS del ingreso matrix reemplazando el consecutivo RIPS del ingreso unix (se modifica en msdro y msotr, el cargo puede estar en una de las dos tablas)]
 * @param  [type] $conex                       [description]
 * @param  [type] $conexUnix                   [description]
 * @param  [type] $wbasedato                   [description]
 * @param  [type] $arr_parametros              [description]
 * @param  [type] $fuente_insumo               [description]
 * @param  [type] $drodocdoc                   [description]
 * @param  [type] $linea                       [description]
 * @param  [type] $codigo_insumo_mx            [description]
 * @param  [type] $arr_FiltroSql               [description]
 * @param  [type] $historia_rep                [description]
 * @param  [type] $ingreso_rep                 [description]
 * @param  [type] $wingreso_unx                [description]
 * @param  [type] $row                         [description]
 * @param  [type] $arr_DocumentosFuentes       [description]
 * @param  [type] $Tcardoi                     [description]
 * @param  [type] &$data                       [description]
 * @param  [type] &$arr_cargosReporte          [description]
 * @param  [type] &$arr_cargosHisFactNotas     [description]
 * @param  [type] &$arr_consultas_por_historia [description]
 * @param  [type] &$proceso_actualizacion      [description]
 * @param  [type] &$arr_RIPS_msate             [description]
 * @return [type]                              [description]
 */
function consultarCargoInsumoPorLinea($conex, $conexUnix, $wbasedato, $arr_parametros, $fuente_insumo, $drodocdoc, $linea, $codigo_insumo_mx, $arr_FiltroSql, $historia_rep, $ingreso_rep, $wingreso_unx, $row, $arr_DocumentosFuentes, $Tcardoi , &$data, &$arr_cargosReporte, &$arr_cargosHisFactNotas, &$arr_consultas_por_historia, &$proceso_actualizacion, &$arr_RIPS_msate)
{
    $historia_ing = $historia_rep.'_'.$ingreso_rep;

    {
        $drodetart = '';
        if(array_key_exists($fuente_insumo, $arr_DocumentosFuentes) && array_key_exists($Tcardoi, $arr_DocumentosFuentes[$fuente_insumo])
        && array_key_exists($drodocdoc, $arr_DocumentosFuentes[$fuente_insumo][$Tcardoi]) && array_key_exists($linea, $arr_DocumentosFuentes[$fuente_insumo][$Tcardoi][$drodocdoc]))
        {
            $drodetart = $arr_DocumentosFuentes[$fuente_insumo][$Tcardoi][$drodocdoc][$linea];
        }
        // $data["evidencia_error"][] = "DEBUG drodetart: ".$drodetart.' > DEBUG codigo_insumo_mx: '.$codigo_insumo_mx.' > '.PHP_EOL;
        /*
        Se agrega esta nueva validacion  , donde se ve si el articulo en Matrix corresponde al de Unix.

        Explicacion: En Matrix se graba un documento y linea  por cada articulo grabado, Esto mismo se hace en Unix  , Existe
        una tabla en Unix donde hay relacion del documento y linea Matrix con documento y linea Unix  generalmente son distintos los documentos
        pero el numero de linea coincide. Para estar seguros de que el articulo en Matrix corresponda al de Unix se  compara tambien el articulo
        si es el articulo se trabaja con la linea  de matrix porque se sabe que es la misma sino se busca en todo el documento unix la linea que corresponde
        a la linea en matrix

        Si s� corresponde   se consulta el estado de facturable o no en Facardet
        */

        if($drodetart == $codigo_insumo_mx)
        {
            $updateFacardet = queryCargoUnix('insumo', $arr_FiltroSql, $fuente_insumo, $drodocdoc, $linea, $historia_rep, $ingreso_rep, $wingreso_unx, '', $proceso_actualizacion);

            if($resivUdt = @odbc_exec($conexUnix, $updateFacardet))
            {
                actualizarConsecutivosRipsCargos($conex, $conexUnix, $wbasedato, $arr_parametros, $fuente_insumo, $drodocdoc, $linea, $codigo_insumo_mx, $arr_FiltroSql, $historia_rep, $ingreso_rep, $wingreso_unx, $row, $arr_DocumentosFuentes, $Tcardoi , $data, $arr_cargosReporte, $arr_cargosHisFactNotas, $arr_consultas_por_historia, $proceso_actualizacion, $arr_RIPS_msate);
            }
            else
            {
                $proceso_actualizacion = false;
                // echo odbc_errormsg()." > ".$updateFacardet;
                $desc_error = "No se pudo actualizar el ingreso en facardet: > ".PHP_EOL.odbc_errormsg();
                registroLogError($conex, $wbasedato, $updateFacardet, 'unix', $desc_error);
            }
        }
        else
        {
            // Entra aqu� si las l�neas de matrix vs Unix no son las mismas
            // Hago una busqueda del articulo y documento y asi hallo la nueva linea

            $selecti2 = "   SELECT  drodetite
                            FROM    IVDRODET
                            WHERE   drodetfue = '{$fuente_insumo}'
                                    AND drodetdoc = '{$drodocdoc}'
                                    AND drodetart = '{$codigo_insumo_mx}'";
            // $data["evidencia_error"][] = "DEBUG selecti2: ".$selecti2.' > '.PHP_EOL;
            // $arr_consultas_por_historia[$historia_ing][] = $selecti2;
            if($resiv = odbc_exec($conexUnix, $selecti2))
            {
                $drodetlinea = odbc_result($resiv,'drodetite');
                $existeprocedimiento = true;
                if($drodetlinea != '')
                {
                    $updateFacardet = queryCargoUnix('insumo', $arr_FiltroSql, $fuente_insumo, $drodocdoc, $drodetlinea, $historia_rep, $ingreso_rep, $wingreso_unx, '', $proceso_actualizacion);

                    if($resivUdt = @odbc_exec($conexUnix, $updateFacardet))
                    {
                        actualizarConsecutivosRipsCargos($conex, $conexUnix, $wbasedato, $arr_parametros, $fuente_insumo, $drodocdoc, $drodetlinea, $codigo_insumo_mx, $arr_FiltroSql, $historia_rep, $ingreso_rep, $wingreso_unx, $row, $arr_DocumentosFuentes, $Tcardoi , $data, $arr_cargosReporte, $arr_cargosHisFactNotas, $arr_consultas_por_historia, $proceso_actualizacion, $arr_RIPS_msate);
                    }
                    else
                    {
                        $proceso_actualizacion = false;
                        // echo odbc_errormsg()." > ".$updateFacardet;
                        $desc_error = "No se pudo actualizar el ingreso en facardet: > ".PHP_EOL.odbc_errormsg();
                        registroLogError($conex, $wbasedato, $updateFacardet, 'unix', $desc_error);
                    }
                    // detalleCargosFacturadosNotasCredito($conex, $conexUnix, $wbasedato, $arr_parametros, $selectfacar, $row, $data, $arr_cargosReporte, $arr_cargosHisFactNotas, $arr_consultas_por_historia);
                }
                else
                {
                    // $data["error"] = 1;
                    // $data["mensaje"] = "Problemas al generar el reporte. No existe insumo en Unix";
                    // $data["evidencia_error"][] = "Linea en blanco (unx $drodetart  mx $codigo_insumo_mx, Tcardoi: {$row['Tcardoi']}, Histo: {$row['Tcarhis']}, drodocdoc: $drodocdoc) selecti2: ".$selecti2.' > ';
                    $desc_error = "Linea en blanco (unx $drodetart  mx $codigo_insumo_mx, Tcardoi: {$row['Tcardoi']}, Histo: {$row['Tcarhis']}, drodocdoc: $drodocdoc) > ".PHP_EOL;
                    registroLogError($conex, $wbasedato, $selecti2, 'unix', $desc_error);
                }
            }
            else
            {
                // $data["error"] = 1;
                // $data["mensaje"] = "Problemas al generar el reporte";
                // $data["evidencia_error"][] = "selectiv2: ".$selectiv2.' > '.mysql_error();
                $proceso_actualizacion = false;
                $desc_error = "No se pudo consultar nueva l�nea del insumo en unix: > ".PHP_EOL.odbc_errormsg();
                registroLogError($conex, $wbasedato, $selecti2, 'unix', $desc_error);
            }
        }
        //$html3.= "<td>".odbc_result($resu,1)."-".$linea."</td>";
    }
}

/**
 * [consultaCargosUnix: Esta funci�n se encarga de consultar la fuente y el documento del cargo (se adiciona a un array para no tener que consultar cada vez que se lee un cargo)
 *                         se modifica el n�mero de ingreso en las tablas ivdro y facar para la fuente y el documento le�do en ITDRODOC, se verifica si el cargo
 *                         cambi� de l�nea o se generaron otros cargos derivados del cargo matrix.]
 * @param  [type] $conex                       [description]
 * @param  [type] $conexUnix                   [description]
 * @param  [type] $wbasedato                   [description]
 * @param  [type] $wbasedato_movhos            [description]
 * @param  [type] $solo_facturas               [description]
 * @param  [type] $wccos_rep                   [description]
 * @param  [type] $row                         [description]
 * @param  [type] &$data                       [description]
 * @param  [type] &$arr_cargosReporte          [description]
 * @param  [type] &$arr_cargosHisFactNotas     [description]
 * @param  [type] &$Tcardoi_ant                [description]
 * @param  [type] &$fuente_insumo              [description]
 * @param  [type] &$drodocdoc                  [description]
 * @param  [type] &$arr_DocumentosFuentes      [description]
 * @param  [type] &$arr_consultas_por_historia [description]
 * @param  [type] &$proceso_actualizacion      [description]
 * @param  [type] &$arr_RIPS_msate             [description]
 * @return [type]                              [description]
 */
function consultaCargosUnix($conex, $conexUnix, $wbasedato, $wbasedato_movhos, $solo_facturas, $wccos_rep, $row, &$data, &$arr_cargosReporte, &$arr_cargosHisFactNotas, &$Tcardoi_ant, &$fuente_insumo, &$drodocdoc, &$arr_DocumentosFuentes, &$arr_consultas_por_historia, &$proceso_actualizacion, &$arr_RIPS_msate)
{
    $Tcarlin      = $row['linea_insumo'];
    $historia_rep = $row['Tcarhis'];
    $ingreso_rep  = $row['Tcaring'];
    $wingreso_unx = $row['wingreso_unx'];
    $reg_unix     = $row['reg_unix'];

    $arr_parametros                         = array();
    $arr_parametros["historia_rep"]         = $historia_rep;
    $arr_parametros["ingreso_rep"]          = $ingreso_rep;
    $arr_parametros["row"]                  = $row;
    $arr_parametros["idx_historia_ing_rep"] = $historia_rep.'_'.$ingreso_rep;
    $arr_parametros["wccos_rep"]            = $wccos_rep;
    $historia_ing                           = $historia_rep.'_'.$ingreso_rep;

    $arr_FiltroSql = array("select"=>"","group"=>"");

    // if($row['invent'] == 'on')
    {
        // Si es de inventario, Tcardoi y fuen_insumo son diferentes a un valor anterior entonces consulte nuevamente en ITDRODOC
        // el n�mero de documento
        $sqlu = '';

        if(!array_key_exists($row['fuen_insumo'], $arr_DocumentosFuentes))
        {
            // echo "<pre>fuen_insumo: ".print_r($row['fuen_insumo'],true)."</pre><br>";
            $arr_DocumentosFuentes[$row['fuen_insumo']] = array();
        }

        // if($row['Tcardoi'] != $Tcardoi_ant || $row['fuen_insumo'] != $fuente_insumo)
        if(!array_key_exists($row['Tcardoi'], $arr_DocumentosFuentes[$row['fuen_insumo']]))
        {
            $Tcardoi_ant   = $row['Tcardoi'];
            $fuente_insumo = $row['fuen_insumo'];

            $sqlu = "   SELECT  drodocdoc, drodetart, drodetite
                        FROM    ITDRODOC, IVDRODET
                        WHERE   drodocfue  = '{$fuente_insumo}'
                                AND drodocnum  = '{$Tcardoi_ant}'
                                AND drodetfue = drodocfue
                                AND drodetdoc = drodocdoc";
            // $data["evidencia_error"][] = "DEBUG sqlu: ".$sqlu.' > '.PHP_EOL;
            // $arr_consultas_por_historia[$historia_ing][] = $sqlu;
            if($resu = @odbc_do($conexUnix, $sqlu))
            {
                while(odbc_fetch_row($resu))
                {
                    $drodocdoc     = odbc_result($resu,"drodocdoc");
                    $drodetite_lin = odbc_result($resu,"drodetite");
                    $drodetart     = odbc_result($resu,"drodetart");
                    if(!array_key_exists($Tcardoi_ant, $arr_DocumentosFuentes[$fuente_insumo]))
                    {
                        $arr_DocumentosFuentes[$fuente_insumo][$Tcardoi_ant] = array();
                    }

                    if(!array_key_exists($drodocdoc, $arr_DocumentosFuentes[$fuente_insumo][$Tcardoi_ant]))
                    {
                        $arr_DocumentosFuentes[$fuente_insumo][$Tcardoi_ant][$drodocdoc] = array();
                    }

                    if($drodetite_lin != '')
                    {
                        if(!array_key_exists($drodetite_lin, $arr_DocumentosFuentes[$fuente_insumo][$Tcardoi_ant][$drodocdoc]))
                        {
                            $arr_DocumentosFuentes[$fuente_insumo][$Tcardoi_ant][$drodocdoc][$drodetite_lin] = ""; //array("drodetart"=>$drodetart);

                            // Actualizar el ingreso en encabezado ivdro
                            $updateIvdro = "UPDATE  ivdro
                                                    SET dronum = '{$ingreso_rep}'
                                            WHERE   drofue = '{$fuente_insumo}'
                                                    AND drodoc = '{$drodocdoc}'
                                                    AND drohis = '{$historia_rep}'
                                                    AND dronum = '{$wingreso_unx}'";

                            if($resivUdt = @odbc_exec($conexUnix, $updateIvdro))
                            {
                                //
                            }
                            else
                            {
                                $proceso_actualizacion = false;
                                // echo odbc_errormsg()." > ".$updateIvdro;
                                $desc_error = "No se pudo actualizar ingreso en ivdro: > ".PHP_EOL.odbc_errormsg();
                                registroLogError($conex, $wbasedato, $updateIvdro, 'unix', $desc_error);
                            }

                            // Actualizar el ingreso en encabezado ivdro
                            $updateFacar = "UPDATE  facar
                                                    SET carnum = '{$ingreso_rep}'
                                            WHERE   carfue = '{$fuente_insumo}'
                                                    AND cardoc = '{$drodocdoc}'
                                                    AND carhis = '{$historia_rep}'
                                                    AND carnum = '{$wingreso_unx}'";

                            if($resivUdt = @odbc_exec($conexUnix, $updateFacar))
                            {
                                //
                            }
                            else
                            {
                                $proceso_actualizacion = false;
                                // echo odbc_errormsg()." > ".$updateFacar;
                                $desc_error = "No se pudo actualizar ingreso en facar: > ".PHP_EOL.odbc_errormsg();
                                registroLogError($conex, $wbasedato, $updateFacar, 'unix', $desc_error);
                            }
                        }
                        $arr_DocumentosFuentes[$fuente_insumo][$Tcardoi_ant][$drodocdoc][$drodetite_lin] = $drodetart;
                    }
                    // echo "<pre>Tcardoi_ant: $Tcardoi_ant, drodocdoc: ".print_r($drodocdoc,true)."</pre><br>";
                }
            }
            else
            {
                $proceso_actualizacion = false;
                $drodocdoc = '';
                // $data["error"] = 1;
                // $data["mensaje"] = "Problemas al generar el reporte";
                // $data["evidencia_error"][] = "sqlu: No se pudo ejecutar el query en unix: ".$sqlu.' > '.PHP_EOL.odbc_errormsg();

                $desc_error = "No se puso consultar fuente y documento, ITDRODOC: > ".PHP_EOL.odbc_errormsg();
                registroLogError($conex, $wbasedato, $sqlu, 'unix', $desc_error);
            }
        }
        else
        {
            $arr_key_doc = array_keys($arr_DocumentosFuentes[$fuente_insumo][$row['Tcardoi']]);
            $drodocdoc = $arr_key_doc[0];
            // $data["evidencia_error"][] = "DEBUG drodocdoc: ".$drodocdoc.' > '.PHP_EOL;
            if(count($arr_key_doc) > 1)
            {
                $data["evidencia_error"][] = 'NO DEBERIA SER MAYOR A 1 > '.print_r($arr_key_doc,true);
                // $desc_error = "No se pudo actualizar ingreso en facar: > ".PHP_EOL.odbc_errormsg();
                // registroLogError($conex, $wbasedato, $updateFacar, 'unix', $desc_error);
            }
        }

        if($drodocdoc != '')
        {
            if($row['Logdoc'] != '')
            {
                $lineasNuevasOReemplazo = array();
                if($row['Logpro'] =='on')
                {
                    $querycenpro = "SELECT  Pdeins
                                    FROM    cenpro_000003
                                    WHERE   Pdepro ='{$row['Tcarprocod']}'";
                    // $data["evidencia_error"][] = "DEBUG querycenpro: ".$querycenpro.' > '.PHP_EOL;
                    // $arr_consultas_por_historia[$historia_ing][] = $querycenpro;
                    if($resquerycenpro =  mysql_query( $querycenpro, $conex  ))
                    {
                        $p = -1;
                        while($rowquerycenpro = mysql_fetch_array($resquerycenpro))
                        {
                            $p++;
                            $lineasNuevasOReemplazo[] = ($row['linea_insumo']*1) + $p;
                        }
                    }
                    else
                    {
                        // $data["error"] = 1;
                        // $data["mensaje"] = "Problemas al generar el reporte";
                        // $data["evidencia_error"][] = "querycenpro: ".$querycenpro.' > '.mysql_error();
                        $proceso_actualizacion = false;
                        $desc_error = "No se pudo consultar c�digo insumo en cenpro_000003: > ".PHP_EOL.mysql_error();
                        registroLogError($conex, $wbasedato, $querycenpro, 'matrix', $desc_error);
                    }
                }
                else
                {
                    $lineasNuevasOReemplazo[] = $row['linea_insumo'];
                }

                $nuevasLineas = implode("','", $lineasNuevasOReemplazo);
                $sqlval       = "   SELECT  Logdoc,Loglin,Logaor,Logare
                                    FROM    {$wbasedato_movhos}_000158
                                    WHERE   Logdoc = '{$row['Tcardoi']}'
                                            AND Loglin IN ('{$nuevasLineas}')
                                            AND Logaor = '{$row['Tcarprocod']}'";
                // $data["evidencia_error"][] = "DEBUG sqlval: ".$sqlval.' > '.PHP_EOL;
                // $arr_consultas_por_historia[$historia_ing][] = $sqlval;
                if($resval = mysql_query( $sqlval, $conex))
                {
                    while($rowval = mysql_fetch_array($resval))
                    {
                        consultarCargoInsumoPorLinea($conex, $conexUnix, $wbasedato, $arr_parametros, $fuente_insumo, $drodocdoc, $rowval['Loglin'], $rowval['Logare'], $arr_FiltroSql, $historia_rep, $ingreso_rep, $wingreso_unx, $row, $arr_DocumentosFuentes, $row['Tcardoi'], $data, $arr_cargosReporte, $arr_cargosHisFactNotas, $arr_consultas_por_historia, $proceso_actualizacion, $arr_RIPS_msate);
                    }
                }
                else
                {
                    $proceso_actualizacion = false;
                    // $data["error"] = 1;
                    // $data["mensaje"] = "Problemas al generar el reporte";
                    // $data["evidencia_error"][] = "sqlval: ".$sqlval.' > '.mysql_error();
                    $desc_error = "No se pudo consultar nueva l�nea del insumo: > ".PHP_EOL.mysql_error();
                    registroLogError($conex, $wbasedato, $sqlval, 'matrix', $desc_error);
                }
            }
            else
            {
                consultarCargoInsumoPorLinea($conex, $conexUnix, $wbasedato, $arr_parametros, $fuente_insumo, $drodocdoc, $Tcarlin, $row['Tcarprocod'], $arr_FiltroSql, $historia_rep, $ingreso_rep, $wingreso_unx, $row, $arr_DocumentosFuentes, $row['Tcardoi'], $data, $arr_cargosReporte, $arr_cargosHisFactNotas, $arr_consultas_por_historia, $proceso_actualizacion, $arr_RIPS_msate);
            }
        }
        else
        {
            $proceso_actualizacion = false;
            // $data["error"] = 1;
            // $data["mensaje"] = "Problemas al generar el reporte";
            // $data["evidencia_error"][] = "drodocdoc: es un valor vac�o, no esta integrado, Tcardoi: {$row['Tcardoi']}, fuen_insumo: {$row['fuen_insumo']}, Tcarprocod: {$row['Tcarprocod']}";
            // $data["evidencia_error"][] = $sqlu;
            $desc_error = "drodocdoc: es un valor vac�o, no esta integrado, Tcardoi: {$row['Tcardoi']}, fuen_insumo: {$row['fuen_insumo']}, Tcarprocod: {$row['Tcarprocod']}, id_106: {$row['id_106']} ".PHP_EOL;
            registroLogError($conex, $wbasedato, "", 'unix', $desc_error);
        }
    }
}

/**
 * [registroLogError: funci�n para registrar errores]
 * @param  [type] $conex               [Conexi�n a base de datos matrix]
 * @param  [type] $wbasedato           [Prefijo base de datos]
 * @param  [type] $nombre_script_error [Nombres del script donde se captur� el error]
 * @param  [type] $usuario_login       [Posible usuario logueado durante el error]
 * @param  [type] $sql_error           [Script sql que est� generando el error]
 * @param  [type] $origen_tipo_error   [Origen donde se gener� el error, matrix - unix]
 * @param  [type] $descripcion_error   [Descripci�n m�s exacta del error]
 * @return [type]                      [null]
 */
function registroLogError($conex, $wbasedato, $sql_error, $origen_tipo_error, $descripcion_error, $nombre_script_error = '')
{
    $user_session = 'auto';
    if(array_key_exists('user',$_SESSION))
    {
        $user_session      = explode('-',$_SESSION['user']);
        $user_session      = $user_session[1];
    }

    $fecha         = date("Y-m-d");
    $hora          = date("H:i:s");

    if($nombre_script_error == '' && (array_key_exists('SCRIPT_NAME',$_SERVER)))
    {
        $nombre_script_error = $_SERVER['SCRIPT_NAME'];
    }

    $sql = "INSERT INTO root_000112
                    (Medico, Fecha_data, Hora_data,
                     Logpro, Loguse, Logsql, Logtip,
                    Logdes, Logrev, Logest, Seguridad)
            VALUES
                    ('{$wbasedato}', '{$fecha}', '{$hora}',
                    '{$nombre_script_error}', '{$user_session}', '".addslashes($sql_error)."', '{$origen_tipo_error}',
                    '".addslashes(utf8_decode($descripcion_error))."', 'off', 'on', 'C-{$user_session}') ";

    if($result = mysql_query($sql, $conex))
    {
        //
    }
    else
    {
        // echo "<pre>".mysql_error()." ".$sql."</pre>";
    }
}

/**
 * [actualizarCargosIngresosInactivosUnix: [APLICA PARA INSUMOS] Cuando se graban cargos a ingresos inactivos en unix, en matrix los cargos quedan con el ingreso inactivo y en unix
 *                                         quedan los cargos con el ingreso activo en el momento de la grabaci�n, esta funci�n se encarga de actualizar en unix
 *                                         el ingreso a los cargos que quedaron con el ingreso activo en unix al momento de la grabaci�n por el ingreso con que realmente qued�
 *                                         el cargo en matrix, es decir, el ingreso que para unix es el inactivo. Tambi�n se actualizan los RIPS con el consecutivo que
 *                                         corresponde al ingreso inactivo.]
 * @param  [type] $conex     [description]
 * @param  [type] $conexUnix [description]
 * @param  [type] $wemp_pmla [description]
 * @param  [type] $hay_unix  [description]
 * @return [type]            [description]
 */
function actualizarCargosIngresosInactivosUnix($conex, $conexUnix, $wemp_pmla, $hay_unix)
{
    // Transferida a include/root/kron_procesos_ERP
}


    // LLAMADO PRINCIPAL A LA FUNCI�N QUE HAR� EL ESCAN DE LOS PACIENTES
    agendaPacientesUrgencias($wbasedato,$wbasedatohce,$wemp_pmla,$seguridad);
    // LLAMADO PARA LA FUNCION DE ACTUALIZAR MEDICAMENTOS DE MATRIX A UNIX
	// Transferida a include/root/kron_procesos_ERP
    //actualizarMedicamentos($wemp_pmla); 

    /**
     * Actualizar ingreso correcto cargos y RIPS en unix de insumos que desde matrix se grabaron a un ingreso inactivo pero que el integrador grab� a unix con el ingreso activo en ese momento
     * de la grabaci�n a unix.
     * >> Edwar Jaramillo 2016-06-16
     */
	// Transferida a include/root/kron_procesos_ERP
    //actualizarCargosIngresosInactivosUnix($conex, $conexUnix, $wemp_pmla, $hay_unix); 

    // -->  Actualizar turnos de urgencias que se hayan borrado.
    //      Jerson Trujillo 2016-04-11
	// Transferida a include/root/kron_procesos_ERP
    //recuperarTurnosDeUrgencias(); 


//Liberacion de conexion Matrix
liberarConexionBD($conex);

//Liberacion de conexion Unix
liberarConexionOdbc($conexUnix);
odbc_close_all();

//echo "ok";
echo "FINAL script: ".date("Y-m-d H:i:s")."<br>";