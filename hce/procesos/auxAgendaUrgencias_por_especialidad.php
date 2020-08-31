<?php
include_once("conex.php");
header("Content-Type: text/html;charset=ISO-8859-1"); 

include_once("root/comun.php");
include_once("root/magenta.php");

$conex = obtenerConexionBD("matrix");
conexionOdbc($conex, $basedatos, &$conexUnix, 'facturacion');
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
	
	$q = " 	SELECT pacnom, pacap1, pacap2, pacnum, pacfec, pachor, pachab, paccer, pacres, pactid, pacced, pacnac, pacsex, pachos, serccocco, emptip
		     FROM inpac, insercco,outer inemp
		    WHERE pachis = '".$pacienteConsulta->historiaClinica."' 
			  AND serccoser = pacser
			  AND paccer = empcod	
			  AND pacap2 is not null
			  AND pachab is not null
			
			UNION
			
			SELECT pacnom, pacap1, ' ' AS pacap2, pacnum, pacfec, pachor, pachab, paccer, pacres, pactid, pacced, pacnac, pacsex, pachos, serccocco, emptip
		     FROM inpac, insercco,outer inemp
		    WHERE pachis = '".$pacienteConsulta->historiaClinica."' 
			  AND serccoser = pacser
			  AND paccer = empcod	
			  AND pacap2 is null
			  AND pachab is not null
			  
			UNION

			SELECT pacnom, pacap1, pacap2, pacnum, pacfec, pachor, ' ', paccer, pacres, pactid, pacced, pacnac, pacsex, pachos, serccocco, emptip
		     FROM inpac, insercco,outer inemp
		    WHERE pachis = '".$pacienteConsulta->historiaClinica."' 
			  AND serccoser = pacser
			  AND paccer = empcod	
			  AND pacap2 is not null
			  AND pachab is null
			
			UNION
			
			SELECT pacnom, pacap1, ' ' AS pacap2, pacnum, pacfec, pachor, ' ', paccer, pacres, pactid, pacced, pacnac, pacsex, pachos, serccocco, emptip
		     FROM inpac, insercco,outer inemp
		    WHERE pachis = '".$pacienteConsulta->historiaClinica."' 
			  AND serccoser = pacser
			  AND paccer = empcod	
			  AND pacap2 is null
			  AND pachab is null";
	//		  AND pacser = '04'";			// 2011-10-27

	$rs = odbc_do($conexUnix,$q);
	$campos = odbc_num_fields($rs);
	
	if (odbc_fetch_row($rs))
	{
		$nombre = explode(" ",trim(odbc_result($rs,1)));
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
		
		$paciente->apellido1 = trim(odbc_result($rs,2));
		$paciente->apellido2 = trim(odbc_result($rs,3));
		$paciente->historiaClinica = trim($pacienteConsulta->historiaClinica);
		$paciente->ingresoHistoriaClinica = trim(odbc_result($rs,4));
		$paciente->fechaIngreso = str_replace("/","-",trim(odbc_result($rs,5)));
		$paciente->horaIngreso = str_replace(".",":",trim(odbc_result($rs,6))).":00";
		$paciente->habitacionActual = trim(odbc_result($rs,7));
		$paciente->numeroIdentificacionResponsable = trim(odbc_result($rs,8));
		$paciente->nombreResponsable = trim(odbc_result($rs,9));
		$paciente->tipoDocumentoIdentidad = trim(odbc_result($rs,10)); 
		$paciente->documentoIdentidad = trim(odbc_result($rs,11));
		$paciente->fechaNacimiento = trim(odbc_result($rs,12));
		$paciente->genero = trim(odbc_result($rs,13));
		$paciente->deHospitalizacion = trim(odbc_result($rs,14));
		$paciente->servicioActual = trim(odbc_result($rs,15));
		$paciente->tipoResponsable = str_replace(" ","",trim(odbc_result($rs,16)));
		
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
								  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera) 
						   VALUES
								  ('".$fechaLog."', '".$horaLog."', '".$pacienteAnterior->documentoIdentidad."', '".$pacienteAnterior->tipoDocumentoIdentidad."', 'Actualizacion tabla root_000036', 'root', 'Nuevo documento ".$pacienteNuevo->tipoDocumentoIdentidad." ".$pacienteNuevo->documentoIdentidad."')";
		$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	}
}

//Actualiza datos del paciente en la tabla 36 de root
function actualizarDatosPacienteTablaUnica($pacienteAnterior, $pacienteNuevo)
{
	global $conex; 
	
	$fechaLog = date('Y-m-d');
	$horaLog = date('H:i:s');

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
								  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera) 
						   VALUES
								  ('".$fechaLog."', '".$horaLog."', '".$pacienteNuevo->documentoIdentidad."', '".$pacienteNuevo->tipoDocumentoIdentidad."', 'Actualizacion tabla root_000036', 'root', 'Nuevos datos ".$pacienteNuevo->nombre1." ".$pacienteNuevo->nombre2." ".$pacienteNuevo->apellido1." ".$pacienteNuevo->apellido2." | ".$pacienteNuevo->fechaNacimiento." | ".$pacienteNuevo->genero." ')";
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
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera) 
							   VALUES
									  ('".$fechaLog."', '".$horaLog."', '".$paciente->historiaClinica."', '".$paciente->ingresoHistoriaClinica."', 'Borrado tabla root_000037', '".$seguridad."', 'Historia diferente unix ".$paciente->tipoDocumentoIdentidad." ".$paciente->documentoIdentidad."')";
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
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera) 
							   VALUES
									  ('".$fechaLog."', '".$horaLog."', '".$paciente->historiaClinica."', '".$paciente->ingresoHistoriaClinica."', 'Borrado tabla root_000037', '".$seguridad."', 'Clave duplicada ".$paciente->tipoDocumentoIdentidad." ".$paciente->documentoIdentidad."')";
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

	$fechaLog = date('Y-m-d');
	$horaLog = date('H:i:s');

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
								  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera) 
						   VALUES
								  ('".$fechaLog."', '".$horaLog."', '".$pacienteNuevo->historiaClinica."', '".$pacienteNuevo->ingresoHistoriaClinica."', 'Actualizacion tabla root_000037', 'root', 'Actualiza ingreso ".$pacienteNuevo->ingresoHistoriaClinica." | ".$pacienteNuevo->tipoDocumentoIdentidad." ".$pacienteNuevo->documentoIdentidad."')";
		$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	}
	
	// Si ocurri� error por clave duplicada
	$error_sql = mysql_errno();
	if(isset($error_sql) && $error_sql=="1062")
	{
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
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera) 
							   VALUES
									  ('".$fechaLog."', '".$horaLog."', '".$pacienteNuevo->historiaClinica."', '".$pacienteNuevo->ingresoHistoriaClinica."', 'Borrado tabla root_000037', 'root', 'Clave duplicada ".$pacienteNuevo->tipoDocumentoIdentidad." ".$pacienteNuevo->documentoIdentidad."')";
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
		$err1=mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . "-" . mysql_error());
		
		$num_affect = mysql_affected_rows();
		if($num_affect>0)
		{
			//Guardo LOG de actualizacion en tabla root_000037 por clave duplicada
			$q = "	INSERT INTO log_agenda 
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera) 
							   VALUES
									  ('".$fechaLog."', '".$horaLog."', '".$pacienteNuevo->historiaClinica."', '".$pacienteNuevo->ingresoHistoriaClinica."', 'Actualizacion tabla root_000037', 'root', 'Clave duplicada ".$pacienteNuevo->ingresoHistoriaClinica." | ".$pacienteNuevo->tipoDocumentoIdentidad." ".$pacienteNuevo->documentoIdentidad."')";
			$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}

	}
}

//Actualiza el documento del paciente en la tabla 37 de root
function actualizarDocumentoPacienteTablaIngresos($pacienteAnterior, $pacienteNuevo,$wemp_pmla)
{
	global $conex; 
	
	$fechaLog = date('Y-m-d');
	$horaLog = date('H:i:s');

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
		//Guardo LOG de actualizaci�n en tabla root_000037
		$q = "	INSERT INTO log_agenda 
								  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera) 
						   VALUES
								  ('".$fechaLog."', '".$horaLog."', '".$pacienteAnterior->historiaClinica."', '".$pacienteAnterior->ingresoHistoriaClinica."', 'Actualizacion tabla root_000037', 'root', 'Actualiza documento paciente ".$pacienteNuevo->tipoDocumentoIdentidad." ".$pacienteNuevo->documentoIdentidad."')";
		$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	}
		
	// Si ocurri� error por clave duplicada
	$error_sql = mysql_errno();
	if(isset($error_sql) && $error_sql=="1062")
	{
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
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera) 
							   VALUES
									  ('".$fechaLog."', '".$horaLog."', '".$pacienteAnterior->historiaClinica."', '".$pacienteAnterior->ingresoHistoriaClinica."', 'Borrado tabla root_000037', 'root', 'Clave duplicada ".$pacienteNuevo->tipoDocumentoIdentidad." ".$pacienteNuevo->documentoIdentidad."')";
			$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}

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
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera) 
							   VALUES
									  ('".$fechaLog."', '".$horaLog."', '".$pacienteAnterior->historiaClinica."', '".$pacienteAnterior->ingresoHistoriaClinica."', 'Grabacion tabla root_000037', 'root', 'Clave duplicada ".$pacienteNuevo->tipoDocumentoIdentidad." ".$pacienteNuevo->documentoIdentidad."')";
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
		  		movhos_000016 
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
					(Medico,Fecha_data,Hora_data,Mtrhis,Mtring,Mtrmed,Mtrest,Mtrtra,Mtretr,Mtrcur,Seguridad)
				VALUES 
					('HCE','".$fecha."','".$hora."','".$ingreso->historiaClinica."','".$ingreso->ingresoHistoriaClinica."','','on','off','','off','C-".$seguridad."')";
		
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
	else 
	{
		$q = "	UPDATE hce_000022 
				SET Fecha_data='".$fecha."', Hora_data='".$hora."', Mtrmed='', Mtrest='on', Mtrtra='off', Mtretr='', Mtrcur='off', Seguridad='C-".$seguridad."'
				WHERE Mtrhis = '".$ingreso->historiaClinica."'
				AND	Mtring = '".$ingreso->ingresoHistoriaClinica."'";
		
		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

		$num_affect = mysql_affected_rows();
		if($num_affect>0)
		{
			//Guardo LOG de actualizaci�n en tabla hce_000022
			$q = "	INSERT INTO log_agenda 
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera) 
							   VALUES
									  ('".$fecha."', '".$hora."', '".$ingreso->historiaClinica."', '".$ingreso->ingresoHistoriaClinica."', 'Actualizacion tabla hce_000022', '".$seguridad."', 'Auto')";
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

	$q = "INSERT INTO movhos_000016 
			(medico,Fecha_data,Hora_data,Inghis,Inging,Ingres,Ingnre,Ingtip,Seguridad)
		VALUES 
			('movhos','".$paciente->fechaIngreso."','".$paciente->horaIngreso."','".$paciente->historiaClinica."', '".$paciente->ingresoHistoriaClinica."', '".$paciente->numeroIdentificacionResponsable."', '".$paciente->nombreResponsable."', '".$paciente->tipoResponsable."', 'C-".$seguridad."' )";
	$err=mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	$num_affect = mysql_affected_rows();
	if($num_affect>0)
	{
		//Guardo LOG de garabaci�n en tabla movhos_000016
		$q = "	INSERT INTO log_agenda 
								  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera) 
						   VALUES
								  ('".$fecha."', '".$hora."', '".$paciente->historiaClinica."', '".$paciente->ingresoHistoriaClinica."', 'Grabacion tabla movhos_000016', '".$seguridad."', 'Auto')";
		$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	}
}

//Actualiza los datos en la tabla 16 de movhos
function actualizarResponsablePaciente($pacienteAnterior, $pacienteNuevo)
{
	global $conex; 
	
	$fecha = date("Y-m-d");
	$hora = date("H:i:s");

	$q = "UPDATE 
			movhos_000016 
		SET 
			Ingres = '".$pacienteNuevo->numeroIdentificacionResponsable."', 
			Ingnre = '".$pacienteNuevo->nombreResponsable."',
			Ingtip = '".$pacienteNuevo->tipoResponsable."'
		WHERE 
			Inghis = '".$pacienteAnterior->historiaClinica."' 
			AND Inging = '".$pacienteAnterior->ingresoHistoriaClinica."' ";
	$err1=mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	$num_affect = mysql_affected_rows();
	if($num_affect>0)
	{
		//Guardo LOG de actualizaci�n en tabla movhos_000016
		$q = "	INSERT INTO log_agenda 
								  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera) 
						   VALUES
								  ('".$fecha."', '".$hora."', '".$pacienteAnterior->historiaClinica."', '".$pacienteAnterior->ingresoHistoriaClinica."', 'Actualizacion tabla movhos_000016', 'root', 'Auto ".$pacienteNuevo->numeroIdentificacionResponsable." ".$pacienteNuevo->nombreResponsable."')";
		$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	}
}

//Ingresa los datos en la tabla 18 de movhos
function grabarIngresoPaciente($ingreso,$seguridad)
{
	global $conex; 
	
	$fecha = date("Y-m-d");
	$hora = date("H:i:s");

	$q = "INSERT INTO 
			movhos_000018 (Medico,Fecha_data,Hora_data,Ubihis,Ubiing,Ubisac,Ubisan,Ubihac,Ubihan,Ubialp,Ubiald,Ubifap,Ubihap,Ubifad,Ubihad,Ubiptr,Seguridad)
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
								  ('".$fecha."', '".$hora."', '".$ingreso->historiaClinica."', '".$ingreso->ingresoHistoriaClinica."', 'Grabacion tabla movhos_000018', '".$seguridad."', 'Auto')";
		$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
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
		 		movhos_000018
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
		 		movhos_000011
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

// Asigna el m�dico tratante a un paciente en urgencias
function asignarEspecialidadUrgencias($basedatos,$basedatoshce,$especialidad,$paciente,$ingreso,$seguridad)
{
	global $conex; 
	
	$q = "	SELECT Mtrhis 
			FROM ".$basedatoshce."_000022 
			WHERE Mtrhis = '".$paciente."' 
			AND Mtring = '".$ingreso."'";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	/*
	// Consulto la especialidad
	$qesp = "	SELECT Medesp  
				FROM ".$basedatos."_000044 
				WHERE Espcod = '".$especialidad."' ";
	$resesp = mysql_query($qesp, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qesp . " - " . mysql_error());
	$rowesp = mysql_fetch_array($resesp);
	$especialidad = explode("-",$rowesp['Medesp']);
	*/
	
	$fecha = date("Y-m-d");
	$hora = date("H:i:s");
	
	if($num==0) 
	{
		$q = "INSERT INTO 
				".$basedatoshce."_000022 
					(Medico,Mtrfam,Mtrham,Mtrhis,Mtring,Mtreme,Mtrest,Mtrtra,Mtretr,Mtrcur,Seguridad)
				VALUES 
					('".$basedatoshce."','".$fecha."','".$hora."','".$paciente."','".$ingreso."','".$especialidad."','on','on','".$especialidad."','off','C-".$seguridad."')";
		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		
		if($res)
			return "ok";
		else
			return "No se pudo realizar la asignaci�n. \n Error: ".$res;
	} 
	else 
	{
		$q = "	UPDATE ".$basedatoshce."_000022 
				SET Mtrfam='".$fecha."', Mtrham='".$hora."', Mtreme='".$especialidad."', Mtrest='on', Mtrtra='on', Mtretr='".$especialidad."', Mtrcur='off'
				WHERE Mtrhis = '".$paciente."'
				AND	Mtring = '".$ingreso."'";
		
		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		
		if($res)
			return "ok";
		else
			return "No se pudo realizar la asignaci�n. \n Error: ".$res;
	}
}

// Borra los ingresos de una historia mayores al ingreso actual de Unix
function borraIngresosMayores($basedatos,$basedatoshce,$paciente,$ingreso,$wemp_pmla,$seguridad,$bandera,$fechaIngresoUnix,$horaIngresoUnix)
{
	global $conex; 
	global $conexUnix; 
	
	$fecha = date("Y-m-d");
	$hora = date("H:i:s");

	//Consulto los ingresos mayores al ingreso actual en unix
	$qmay = "	SELECT Fecha_data, Hora_data, Ubiing, Ubisac 
				FROM ".$basedatos."_000018 
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
						FROM ".$basedatos."_000018 
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
			
			//Borro registro en tabla 16 de Movhos 
			$q = "	DELETE 
					  FROM ".$basedatos."_000016 
					 WHERE Inghis = '".$paciente."'
					   AND Inging = '".$ingreso."'";
			$res2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

			$num_affect = mysql_affected_rows();
			if($num_affect>0)
			{
				//Guardo LOG de borrado en tabla Movhos 16
				$q = "	INSERT INTO log_agenda 
										  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera) 
								   VALUES
										  ('".$fechaLog."', '".$horaLog."', '".$paciente."', '".$ingreso."', 'Borrado tabla movhos_000016', '".$seguridad."', '".$bandera."')";
				$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			}
		
			//Borro registro en tabla 18 de Movhos 
			$q = "	DELETE 
					  FROM ".$basedatos."_000018 
					 WHERE Ubihis = '".$paciente."'
					   AND Ubiing = '".$ingreso."'";
			$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

			$num_affect = mysql_affected_rows();
			if($num_affect>0)
			{
				//Guardo LOG de borrado en tabla Movhos 18 
				$q = "	INSERT INTO log_agenda 
										  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera) 
								   VALUES
										  ('".$fechaLog."', '".$horaLog."', '".$paciente."', '".$ingreso."', 'Borrado tabla movhos_000018', '".$seguridad."', '".$bandera."')";
				$resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			}

			// Cosulto si el paciente ya est� registrado en la tabla 22 de Hce
			$q = "	SELECT Mtrhis 
					FROM ".$basedatoshce."_000022 
					WHERE Mtrhis = '".$paciente."' 
					AND Mtring = '".$ingreso."'";
			$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			$num = mysql_num_rows($res);

			if($num>0) 
			{
				$q = "	DELETE
						  FROM ".$basedatoshce."_000022 
						 WHERE Mtrhis = '".$paciente."'
						   AND	Mtring = '".$ingreso."'";
				$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

				$num_affect = mysql_affected_rows();
				if($num_affect>0)
				{
					//Guardo LOG de borrado en tabla hce 22 
					$q = "	INSERT INTO log_agenda 
											  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera) 
									   VALUES
											  ('".$fechaLog."', '".$horaLog."', '".$paciente."', '".$ingreso."', 'Borrado tabla hce_000022', '".$seguridad."', '".$bandera."')";
					$resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
				}
			}

			// Cosulto si el paciente ya est� registrado en la tabla 33 de Movhos
			$q2 = "	SELECT Historia_clinica 
					FROM ".$basedatos."_000033 
					WHERE Historia_clinica = '".$paciente."' 
					AND Num_ingreso = '".$ingreso."'
					AND Servicio = '".$ccoActual."' ";
			$res2 = mysql_query($q2, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
			$num2 = mysql_num_rows($res2);

			if($num2>0) 
			{
				$q = "	DELETE
						FROM ".$basedatos."_000033 
						WHERE Historia_clinica = '".$paciente."' 
						AND Num_ingreso = '".$ingreso."'
						AND Servicio = '".$ccoActual."' ";
				$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

				$num_affect = mysql_affected_rows();
				if($num_affect>0)
				{
					//Guardo LOG de borrado en tabla Movhos 33 
					$q = "	INSERT INTO log_agenda 
											  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera) 
									   VALUES
											  ('".$fechaLog."', '".$horaLog."', '".$paciente."', '".$ingreso."', 'Borrado tabla movhos_000033', '".$seguridad."', '".$bandera."')";
					$resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
				}
			}
		}
	  }
	}
}

// Establece el estado de alta para un paciente en urgencias
function altaPacienteUrgencias($basedatos,$basedatoshce,$paciente,$ingreso,$wemp_pmla,$seguridad,$bandera)
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
		$qcomp = " SELECT pacing
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
					FROM ".$basedatos."_000020
					WHERE Habhis = '".$paciente."' 
					AND Habing = '".$ingreso."'";
		$reshab = mysql_query($qhab, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qhab . " - " . mysql_error());
		$numhab = mysql_num_rows($reshab);
		// Si tiene cama asiganada y fecha de egreso en unix es igual a la fecha actual
		if($numhab>0)
			$tipo_alta = "noalta";
	}

	// Inicia el proceso de alta despu�s de validar si realmente se va a dar de alta
	if($tipo_alta != "noalta")
	{
		if($tipo_alta == "normal")
		{
			//Consulto si el paciente tiene conducta asignada
			$qcon = "	SELECT Mtrhis, Mtring, Mtrcon
						FROM ".$basedatoshce."_000022
						WHERE Mtrhis = '".$paciente."' 
						AND Mtring = '".$ingreso."'
						AND Mtrcon != '' 
						AND Mtrcon != 'NO APLICA' 
						AND Mtrest = 'on'";
			$rescon = mysql_query($qcon, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qcon . " - " . mysql_error());
			$rowcon = mysql_fetch_array($rescon);
			$numcon = mysql_num_rows($rescon);
			// Si tiene conducta asiganada y es alta autom�tica no se da de alta
			if($numcon>0 && isset($bandera) && $bandera=="auto")
			{
				return "no-alta";
			}
			else
			{

				//Consulto si el paciente est� en proceso de traslado
				$qptr = "	SELECT Ubihis, Ubiing, Ubiptr, Eyrsor, Eyrsde, Cconom, Ubisac
							FROM ".$basedatos."_000017, ".$basedatos."_000018, ".$basedatos."_000011
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
				
				// Si est� en proceso de traslado no se puede dar de alta
				if($numptr>0)
				{
					return "El paciente no se puede dar de alta debido a que est� en proceso de traslado para el servicio ".$rowptr['Cconom'];
				}
				else
				{
					//Consulto el centro de costo actual del paciente
					$qcen = "	SELECT Ubisac
								FROM ".$basedatos."_000018
								WHERE Ubihis = '".$paciente."'
								AND	Ubiing = '".$ingreso."'";
					$rescen = mysql_query($qcen, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qcen . " - " . mysql_error());
					$rowcen = mysql_fetch_array($rescen);

					//Actualizo tabla 18 de Movhos asignandole los parametros del alta
					$q = "	UPDATE ".$basedatos."_000018 
							SET Ubiald='on', Ubifad='".$fecha."', Ubihad='".$hora."', Ubiuad='".$seguridad."' 
							WHERE Ubihis = '".$paciente."'
							AND	Ubiing = '".$ingreso."'";
					$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

					$num_affect = mysql_affected_rows();
					if($num_affect>0)
					{
						//Guardo LOG de actualizaci�n alta en tabla movhos_000018
						$q = "	INSERT INTO log_agenda 
												  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera) 
										   VALUES
												  ('".$fecha."', '".$hora."', '".$paciente."', '".$ingreso."', 'Actualizacion tabla movhos_000018', '".$seguridad."', 'Alta ".$bandera."')";
						$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					}
					
					//Consulto el c�digo de la conducta de alta en la tabla 35 de HCE
					$qalt = "	SELECT Concod  
								FROM ".$basedatoshce."_000035 
								WHERE Conalt = 'on' 
								AND Conadm = 'on'";
					$resalt = mysql_query($qalt, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qalt . " - " . mysql_error());
					$rowalt = mysql_fetch_array($resalt);
					$conducta = $rowalt['Concod'];
					
					// Cosulto si el paciente ya est� registrado en la tabla 22 de Hce
					$q = "	SELECT Mtrhis 
							FROM ".$basedatoshce."_000022 
							WHERE Mtrhis = '".$paciente."' 
							AND Mtring = '".$ingreso."'";
					$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					$num = mysql_num_rows($res);
	
					if($num==0) 
					{
						$q = "INSERT INTO 
								".$basedatoshce."_000022 
									(Medico,Fecha_data,Hora_data,Mtrhis,Mtring,Mtrmed,Mtrest,Mtrtra,Mtretr,Mtrcon,Mtrcur,Seguridad)
								VALUES 
									('".$basedatoshce."','".$fecha."','".$hora."','".$paciente."','".$ingreso."','','on','off','','".$conducta."','off','C-".$seguridad."')";
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
						$q = "	UPDATE ".$basedatoshce."_000022 
								SET Mtrest='on', Mtrtra='off', Mtrcon='".$conducta."', Mtrcur='off', Seguridad='C-".$seguridad."' 
								WHERE Mtrhis = '".$paciente."'
								AND	Mtring = '".$ingreso."'";
						$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
						
						$num_affect = mysql_affected_rows();
						if($num_affect>0)
						{
							//Guardo LOG de actualizaci�n alta en tabla hce_000022
							$q = "	INSERT INTO log_agenda 
													  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera) 
											   VALUES
													  ('".$fecha."', '".$hora."', '".$paciente."', '".$ingreso."', 'Actualizacion tabla hce_000022', '".$seguridad."', 'Alta ".$bandera."')";
							$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
						}
					}
	
					// Cosulto si el paciente ya est� registrado en la tabla 33 de Movhos
					$q = "	SELECT Historia_clinica 
							FROM ".$basedatos."_000033 
							WHERE Historia_clinica = '".$paciente."' 
							AND Num_ingreso = '".$ingreso."'
							AND Servicio = '".$rowcen['Ubisac']."'
							AND Tipo_egre_serv = 'ALTA'";
					$resegr = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					$numegr = mysql_num_rows($resegr);
	
					if($numegr==0) 
					{
						//Registro el egreso en la tabla 33 de Movhos
						$q = "	INSERT INTO 
								".$basedatos."_000033 
									(Medico, Fecha_data, Hora_data, Historia_clinica, Num_ingreso, Servicio, Num_ing_serv, Fecha_egre_serv, Hora_egr_serv, Tipo_egre_serv, Dias_estan_serv,Seguridad)
								VALUES 
									('".$basedatos."','".$fecha."','".$hora."','".$paciente."','".$ingreso."','".$rowcen['Ubisac']."','1','".$fecha."','".$hora."','ALTA','1','C-".$seguridad."')";
						$res2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		
						$num_affect = mysql_affected_rows();
						if($num_affect>0)
						{
							//Guardo LOG de grabaci�n egreso en tabla movhos_000033
							$q = "	INSERT INTO log_agenda 
													  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera) 
											   VALUES
													  ('".$fecha."', '".$hora."', '".$paciente."', '".$ingreso."', 'Grabacion tabla movhos_000033', '".$seguridad."', 'Alta ".$bandera."')";
							$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
						}
					}
					else 
					{
						$q = "	UPDATE ".$basedatos."_000033 
								SET Fecha_data='".$fecha."', Hora_data='".$hora."', Fecha_egre_serv='".$fecha."', Hora_egr_serv='".$hora."', Seguridad='C-".$seguridad."' 
								WHERE Historia_clinica = '".$paciente."' 
								AND Num_ingreso = '".$ingreso."'
								AND Servicio = '".$rowcen['Ubisac']."'
								AND Tipo_egre_serv = 'ALTA'";
						$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
						
						$num_affect = mysql_affected_rows();
						if($num_affect>0)
						{
							//Guardo LOG de actualizaci�n alta en tabla hce_000022
							$q = "	INSERT INTO log_agenda 
													  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera) 
											   VALUES
													  ('".$fecha."', '".$hora."', '".$paciente."', '".$ingreso."', 'Actualizacion tabla movhos_000033', '".$seguridad."', 'Alta ".$bandera."')";
							$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
						}
					}
	

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
			$qptr = "	SELECT Ubihis, Ubiing, Ubiptr, Eyrsor, Eyrsde, Cconom
						FROM ".$basedatos."_000017, ".$basedatos."_000018, ".$basedatos."_000011
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
			
			// Si est� en proceso de traslado no se puede dar de alta
			if($numptr>0)
			{
				return "El paciente no se puede dar de alta debido a que est� en proceso de traslado para el servicio ".$rowptr['Cconom'];
			}
			else
			{

				//Consulto datos en tabla 18 de movhos
				$qubi = "	SELECT Fecha_data, Hora_data
							FROM ".$basedatos."_000018
							WHERE Ubihis = '".$paciente."' 
							AND Ubiing = '".$ingreso."'";
				$resubi = mysql_query($qubi, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qubi . " - " . mysql_error());
				$rowubi = mysql_fetch_array($resubi);
				$numubi = mysql_num_rows($resubi);

				if($numubi>0)
				{
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
												  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera) 
										   VALUES
												  ('".$fecha."', '".$hora."', '".$paciente."', '".$ingreso."', 'Actualizacion tabla root_000037', '".$seguridad."', 'Alta ".$bandera." Oriing-1')";
						$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					}
				}
				
				$fechaLog = date('Y-m-d');
				$horaLog = date('H:i:s');

				//Borro registro en tabla 16 de Movhos 
				$q = "	DELETE 
						  FROM ".$basedatos."_000016 
						 WHERE Inghis = '".$paciente."'
						   AND Inging = '".$ingreso."'";
				$res2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

				$num_affect = mysql_affected_rows();
				if($num_affect>0)
				{
					//Guardo LOG de borrado en tabla Movhos 16
					$q = "	INSERT INTO log_agenda 
											  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera) 
									   VALUES
											  ('".$fechaLog."', '".$horaLog."', '".$paciente."', '".$ingreso."', 'Borrado tabla movhos_000016', '".$seguridad."', 'Alta ".$bandera."')";
					$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
				}
				
				//Borro registro en tabla 18 de Movhos 
				$q = "	DELETE 
						  FROM ".$basedatos."_000018 
						 WHERE Ubihis = '".$paciente."'
						   AND Ubiing = '".$ingreso."'";
				$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

				$num_affect = mysql_affected_rows();
				if($num_affect>0)
				{
					//Guardo LOG de borrado en tabla Movhos 18 
					$q = "	INSERT INTO log_agenda 
											  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera) 
									   VALUES
											  ('".$fechaLog."', '".$horaLog."', '".$paciente."', '".$ingreso."', 'Borrado tabla movhos_000018', '".$seguridad."', 'Alta ".$bandera."')";
					$resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
				}

				// Cosulto si el paciente ya est� registrado en la tabla 22 de Hce
				$q = "	SELECT Mtrhis 
						FROM ".$basedatoshce."_000022 
						WHERE Mtrhis = '".$paciente."' 
						AND Mtring = '".$ingreso."'";
				$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
				$num = mysql_num_rows($res);

				if($num>0) 
				{
					$q = "	DELETE
							  FROM ".$basedatoshce."_000022 
							 WHERE Mtrhis = '".$paciente."'
							   AND	Mtring = '".$ingreso."'";
					$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

					$num_affect = mysql_affected_rows();
					if($num_affect>0)
					{
						//Guardo LOG de borrado en tabla hce 22
						$q = "	INSERT INTO log_agenda 
												  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera) 
										   VALUES
												  ('".$fechaLog."', '".$horaLog."', '".$paciente."', '".$ingreso."', 'Borrado tabla hce_000022', '".$seguridad."', 'Alta ".$bandera."')";
						$resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					}
				}

				if($res1)
					return "ok";
				else
					return "Ocurri� un error en el proceso. \n Error: ".$res1;
			}
		}
	}
	else
	{	
		return "El paciente no se puede dar de alta porque a�n est� activo en el sistema";
	}
}

// Establece el estado de alta por muerte para un paciente en urgencias
function muertePacienteUrgencias($basedatos,$basedatoshce,$codcco,$paciente,$ingreso,$seguridad)
{
	global $conex; 
	
	$fecha = date("Y-m-d");
	$hora = date("H:i:s");

	//Consulto si el paciente est� en proceso de traslado
	$qptr = "	SELECT Ubihis, Ubiing, Ubiptr, Eyrsor, Eyrsde, Cconom
				FROM ".$basedatos."_000017, ".$basedatos."_000018, ".$basedatos."_000011
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
	
	// Si est� en proceso de traslado no se puede dar de alta
	if($numptr>0)
	{
		return "El paciente no se puede dar de alta por muerte debido a que est� en proceso de traslado para el servicio ".$rowptr['Cconom'];
	}
	else
	{
		
		//Actualizo tabla 18 de Movhos asignandole los parametros del alta
		$q = "	UPDATE ".$basedatos."_000018 
				SET Ubimue='on', Ubiald='on', Ubifad='".$fecha."', Ubihad='".$hora."', Ubiuad='".$seguridad."'
				WHERE Ubihis = '".$paciente."'
				AND	Ubiing = '".$ingreso."'";
		$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

		$num_affect = mysql_affected_rows();
		if($num_affect>0)
		{
			//Guardo LOG de actualizacion en tabla movhos 18 - alta por muerte
			$q = "	INSERT INTO log_agenda 
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera) 
							   VALUES
									  ('".$fecha."', '".$hora."', '".$paciente."', '".$ingreso."', 'Actualizacion tabla movhos_000018', '".$seguridad."', 'Alta por muerte')";
			$resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}
		
		//Consulto el c�digo de la conducta de alta en la tabla 35 de HCE
		$qalt = "	SELECT Concod  
					FROM ".$basedatoshce."_000035 
					WHERE Conmue = 'on' 
					AND Conadm = 'on'";
		$resalt = mysql_query($qalt, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qalt . " - " . mysql_error());
		$rowalt = mysql_fetch_array($resalt);
		$conducta = $rowalt['Concod'];
		
		// Cosulto si el paciente ya est� registrado en la tabla 22 de Hce
		$q = "	SELECT Mtrhis 
				FROM ".$basedatoshce."_000022 
				WHERE Mtrhis = '".$paciente."' 
				AND Mtring = '".$ingreso."'";
		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$num = mysql_num_rows($res);

		if($num==0) 
		{
			$q = "INSERT INTO 
					".$basedatoshce."_000022 
						(Medico,Fecha_data,Hora_data,Mtrhis,Mtring,Mtrmed,Mtrest,Mtrtra,Mtretr,Mtrcon,Mtrcur,Seguridad)
					VALUES 
						('".$basedatoshce."','".$fecha."','".$hora."','".$paciente."','".$ingreso."','','on','off','','".$conducta."','off','C-".$seguridad."')";
			$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

			$num_affect = mysql_affected_rows();
			if($num_affect>0)
			{
				//Guardo LOG de grabacion en tabla hce 22 - alta por muerte
				$q = "	INSERT INTO log_agenda 
										  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera) 
								   VALUES
										  ('".$fecha."', '".$hora."', '".$paciente."', '".$ingreso."', 'Grabacion tabla hce_000022', '".$seguridad."', 'Alta por muerte')";
				$resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			}
		} 
		else 
		{
			$q = "	UPDATE ".$basedatoshce."_000022 
					SET Mtrest='on', Mtrtra='off', Mtrcon='".$conducta."', Mtrcur='off', Seguridad='C-".$seguridad."' 
					WHERE Mtrhis = '".$paciente."'
					AND	Mtring = '".$ingreso."'";
			$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

			$num_affect = mysql_affected_rows();
			if($num_affect>0)
			{
				//Guardo LOG de actualizacion en tabla hce 22 - alta por muerte
				$q = "	INSERT INTO log_agenda 
										  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera) 
								   VALUES
										  ('".$fecha."', '".$hora."', '".$paciente."', '".$ingreso."', 'Actualizacion tabla hce_000022', '".$seguridad."', 'Alta por muerte')";
				$resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			}
		}

		// Cosulto si el paciente ya est� registrado en la tabla 33 de Movhos
		$q = "	SELECT Historia_clinica 
				FROM ".$basedatos."_000033 
				WHERE Historia_clinica = '".$paciente."' 
				AND Num_ingreso = '".$ingreso."'
				AND Servicio = '".$codcco."'
				AND Tipo_egre_serv = 'MUERTE'";
		$resegr = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$numegr = mysql_num_rows($resegr);

		if($numegr==0) 
		{
			//Registro el egreso en la tabla 33 de Movhos
			$q = "	INSERT INTO 
					".$basedatos."_000033 
						(Medico, Fecha_data, Hora_data, Historia_clinica, Num_ingreso, Servicio, Num_ing_serv, Fecha_egre_serv, Hora_egr_serv, Tipo_egre_serv, Dias_estan_serv,Seguridad)
					VALUES 
						('".$basedatos."','".$fecha."','".$hora."','".$paciente."','".$ingreso."','".$codcco."','1','".$fecha."','".$hora."','MUERTE','1','C-".$seguridad."')";
			$res2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

			$num_affect = mysql_affected_rows();
			if($num_affect>0)
			{
				//Guardo LOG de grabaci�n egreso en tabla movhos_000033
				$q = "	INSERT INTO log_agenda 
										  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera) 
								   VALUES
										  ('".$fecha."', '".$hora."', '".$paciente."', '".$ingreso."', 'Grabacion tabla movhos_000033', '".$seguridad."', 'Alta por muerte')";
				$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			}
		}
		else 
		{
			$q = "	UPDATE ".$basedatos."_000033 
					SET Fecha_data='".$fecha."', Hora_data='".$hora."', Fecha_egre_serv='".$fecha."', Hora_egr_serv='".$hora."', Seguridad='C-".$seguridad."' 
					WHERE Historia_clinica = '".$paciente."' 
					AND Num_ingreso = '".$ingreso."'
					AND Servicio = '".$codcco."'
					AND Tipo_egre_serv = 'MUERTE'";
			$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			
			$num_affect = mysql_affected_rows();
			if($num_affect>0)
			{
				//Guardo LOG de actualizaci�n alta en tabla movhos_000033
				$q = "	INSERT INTO log_agenda 
										  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera) 
								   VALUES
										  ('".$fecha."', '".$hora."', '".$paciente."', '".$ingreso."', 'Actualizacion tabla movhos_000033', '".$seguridad."', 'Alta por muerte')";
				$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			}
		}

		if($res1)
			return "ok";
		else
			return "Ocurri� un error en el proceso. \n Error: ".$res1;
	}
}

// Vuelve y activa un paciente dado de alta
function activarPacienteUrgencias($basedatos,$basedatoshce,$codcco,$paciente,$ingreso,$seguridad)
{
	global $conex; 
	global $conexUnix;	
		
	
	$fecha = date("Y-m-d");
	$hora = date("H:i:s");
	
	// Cosulto si el paciente est� en agenda urgencias
	$qagn = "	SELECT Ubihis 
				FROM ".$basedatos."_000018, ".$basedatoshce."_000022  
				WHERE Ubihis = '".$paciente."' 
				AND Ubimue != 'on' 
				AND Ubiald != 'on' 
				AND Ubisac = '".$codcco."'
				AND Ubihis = Mtrhis  
				AND Ubiing = Mtring ";
	$resagn = mysql_query($qagn, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qagn . " - " . mysql_error());
	$numagn = mysql_num_rows($resagn);

	if($numagn==0) 
	{
		// Se consulta si el paciente sigue activo en Unix
		$qact = "SELECT COUNT(*)
				   FROM inpac
				  WHERE pachis = '".$paciente."'
					AND pacnum = ".$ingreso."";
		$rs_act = odbc_do($conexUnix,$qact);
		odbc_fetch_row($rs_act);
		$campos = odbc_result($rs_act,1);
			
		// Si est� activo en Unix seg�n inpac
		if(isset($campos) & $campos>0)
		{
			//Actualizo tabla 18 de Movhos de modo que quite los registros de alta
			$q = "	UPDATE ".$basedatos."_000018 
					SET Ubimue='off', Ubiald='off', Ubialp='off', Ubifap='0000-00-00', Ubihap='00:00:00', Ubifad='0000-00-00', Ubihad='00:00:00', Ubiuad='' 
					WHERE Ubihis = '".$paciente."'
					AND	Ubiing = '".$ingreso."'";
			$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

			$num_affect = mysql_affected_rows();
			if($num_affect>0)
			{
				//Guardo LOG de actualizacion en tabla movhos 18 - Activacion paciente
				$q = "	INSERT INTO log_agenda 
										  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera) 
								   VALUES
										  ('".$fecha."', '".$hora."', '".$paciente."', '".$ingreso."', 'Actualizacion tabla movhos_000018', '".$seguridad."', 'Activacion paciente')";
				$resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			}

			// Cosulto si el paciente ya est� registrado en la tabla 22 de Hce
			$q = "	SELECT Mtrhis 
					FROM ".$basedatoshce."_000022 
					WHERE Mtrhis = '".$paciente."' 
					AND Mtring = '".$ingreso."'";
			$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			$num = mysql_num_rows($res);

			// Si est� registrado active de nuevo los regisros del paciente
			if($num>0) 
			{
				$q = "	UPDATE ".$basedatoshce."_000022 
						SET Mtrest='on', Mtrcur='off', Mtrcon=''
						WHERE Mtrhis = '".$paciente."'
						AND	Mtring = '".$ingreso."'";
				$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

				$num_affect = mysql_affected_rows();
				if($num_affect>0)
				{
					//Guardo LOG de actualizacion en tabla hce 22 - Activacion paciente
					$q = "	INSERT INTO log_agenda 
											  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera) 
									   VALUES
											  ('".$fecha."', '".$hora."', '".$paciente."', '".$ingreso."', 'Actualizacion tabla hce_000022', '".$seguridad."', 'Activacion paciente')";
					$resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
				}
			}

			//Borro el registro de egreso en la tabla 33 de Movhos
			$q = "	DELETE FROM ".$basedatos."_000033 
					WHERE Historia_clinica = '".$paciente."' AND Num_ingreso = '".$ingreso."'";
			$res2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

			$num_affect = mysql_affected_rows();
			if($num_affect>0)
			{
				//Guardo LOG de borrado en tabla movhos 33 - Activacion paciente
				$q = "	INSERT INTO log_agenda 
										  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera) 
								   VALUES
										  ('".$fecha."', '".$hora."', '".$paciente."', '".$ingreso."', 'Borrado tabla movhos_000033', '".$seguridad."', 'Activacion paciente')";
				$resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			}

			if($res1)
				return "ok";
			else
				return "Ocurri� un error en el proceso. \n Error: ".$res1;
		}
		else
		{
			return "inactivoEnUnix";
		}
	} 
	else		
	{
		return "activo";
	}
}

// Borra los registros del paciente para que su ingreso pueda ser reasignado
function reasignarPacienteUrgencias($basedatos,$basedatoshce,$codcco,$paciente,$ingreso,$wemp_pmla,$seguridad)
{
	global $conex; 
	
	// Cosulto si el paciente est� en agenda urgencias

	$qtra = "	SELECT Ubihis 
				FROM ".$basedatos."_000018, ".$basedatoshce."_000022  
				WHERE Ubihis = '".$paciente."' 
				AND Ubiing = '".$ingreso."'
				AND Ubisac = '".$codcco."'
				AND Ubihis = Mtrhis  
				AND Ubiing = Mtring 
				AND Mtrfco <> '0000-00-00'
				AND Mtrfco <> '' ";
	$restra = mysql_query($qtra, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qtra . " - " . mysql_error());
	$numtra = mysql_num_rows($restra);

	if($numtra==0) 
	{
		
		$fechaLog = date('Y-m-d');
		$horaLog = date('H:i:s');

		// Borra registro en tabla 16 de Movhos
		$q = "	DELETE FROM ".$basedatos."_000016 
				WHERE Inghis = '".$paciente."'
				AND	Inging = '".$ingreso."'";
		$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

		$num_affect = mysql_affected_rows();
		if($num_affect>0)
		{
			//Guardo LOG de borrado en tabla Movhos 16
			$q = "	INSERT INTO log_agenda 
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera) 
							   VALUES
									  ('".$fechaLog."', '".$horaLog."', '".$paciente."', '".$ingreso."', 'Borrado tabla movhos_000016', '".$seguridad."', 'Reasignacion paciente')";
			$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}

		// Borra registro en tabla 18 de Movhos
		$q = "	DELETE FROM ".$basedatos."_000018 
				WHERE Ubihis = '".$paciente."'
				AND	Ubiing = '".$ingreso."'";
		$res2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

		$num_affect = mysql_affected_rows();
		if($num_affect>0)
		{
			//Guardo LOG de borrado en tabla Movhos 18 
			$q = "	INSERT INTO log_agenda 
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera) 
							   VALUES
									  ('".$fechaLog."', '".$horaLog."', '".$paciente."', '".$ingreso."', 'Borrado tabla movhos_000018', '".$seguridad."', 'Reasignacion paciente')";
			$resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}

		// Borra registro de la tabla 37 de Root
		$q = "	DELETE FROM	root_000037 
				WHERE Orihis = '".$paciente."'
				AND	Oriing = '".$ingreso."' 
				AND	Oriori = '".$wemp_pmla."'";
		$res2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

		$num_affect = mysql_affected_rows();
		if($num_affect>0)
		{
			//Guardo LOG de borrado en tabla Root 37 
			$q = "	INSERT INTO log_agenda 
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera) 
							   VALUES
									  ('".$fechaLog."', '".$horaLog."', '".$paciente."', '".$ingreso."', 'Borrado tabla root_000037', '".$seguridad."', 'Reasignacion paciente')";
			$resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}

		// Borra registro de egreso en la tabla 33 de Movhos
		$q = "	DELETE FROM	".$basedatos."_000033 
				WHERE Historia_clinica = '".$paciente."'
				AND	Num_ingreso = '".$ingreso."'";
		$res2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

		$num_affect = mysql_affected_rows();
		if($num_affect>0)
		{
			//Guardo LOG de borrado en tabla Movhos 33 
			$q = "	INSERT INTO log_agenda 
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera) 
							   VALUES
									  ('".$fechaLog."', '".$horaLog."', '".$paciente."', '".$ingreso."', 'Borrado tabla movhos_000033', '".$seguridad."', 'Reasignacion paciente')";
			$resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}

		// Borra registro en tabla 22 de Hce
		$q = "	DELETE FROM ".$basedatoshce."_000022 
				WHERE Mtrhis = '".$paciente."'
				AND	Mtring = '".$ingreso."'";
		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		
		$num_affect = mysql_affected_rows();
		if($num_affect>0)
		{
			//Guardo LOG de borrado en tabla Hce 22 
			$q = "	INSERT INTO log_agenda 
									  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera) 
							   VALUES
									  ('".$fechaLog."', '".$horaLog."', '".$paciente."', '".$ingreso."', 'Borrado tabla hce_000022', '".$seguridad."', 'Reasignacion paciente')";
			$resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}

		if($res1)
			return "ok";
		else
			return "Ocurri� un error en el proceso. \n Error: ".$res1;
	} 
	else		
	{
		return "no-reasignar";
	}
}

/********************************************************************************
* CONSULTA EN UNIX LOS PACIENTES QUE NO ESTAN EN URGENCIAS Y LOS DA DE ALTA		*
*********************************************************************************/
function actualizarAltaPacientesUnix($basedatos,$basedatoshce,$wemp_pmla,$seguridad,$listados)
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
		$k++;
	}

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
			$ingreso_paciente = $paciente_alta[1];;
			//echo "<br>ALTA: ".$altas_unix[$j];
			altaPacienteUrgencias($basedatos,$basedatoshce,$historia_paciente,$ingreso_paciente,$wemp_pmla,"Movhos","auto");
			$conting++;
		}
	}	
}

/********************************************************************************************
* CONSULTA EN UNIX LOS PACIENTES EN URGENCIAS Y ACTUALIZA EL LISTADO DE PACIENTES ACTIVOS 	*
********************************************************************************************/
function actualizarPacientesUnix($basedatos,$basedatoshce,$wemp_pmla,$seguridad,$listados)
{
	global $conex; 
	global $conexUnix;

	if($conexUnix)
	{		

		// Se llama a la funcion para dar de alta a los pacientes que no est�n en urgencias de Unix
		actualizarAltaPacientesUnix($basedatos,$basedatoshce,$wemp_pmla,$seguridad,$listados);
		
		// Se llama a la funcion para actualizar datos de pacientes en cl�nica
		actualizarDatosPacientes($basedatos,$wemp_pmla,$seguridad,$listados);

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
				$codCco = obtenerCcoMatrix($basedatos,$cco_paciente,$wemp_pmla,$seguridad);
				if($codCco!="")
					ingresarPacientesUrgencias($basedatos,$basedatoshce,$codCco,$historia_paciente,$wemp_pmla,$seguridad);
				$conting++;
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

function agendaPacientesUrgencias($basedatos,$basedatoshce,$ccoUrgencias,$wemp_pmla,$seguridad)
{

// Validaci�n de usuario
if (!isset($user))
{
	if (!isset($_SESSION['user'])) 
	{
		session_register("user");
	}
	$user="";
}

//Codigo de usuario que ingreso al sistema
if (strpos($user, "-") > 0)
	$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
else
	$wuser = "";

$usuario = new Usuario();

$usuario->codigo = $wuser;

//Variable para determinar la empresa
if(!isset($wemp_pmla))
{
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}

//Valida codigo de usuario en sesion si no esta registrado el sistema termina la ejecucion
if (!isset($_SESSION['user']) || !isset($seguridad) || $seguridad=="")
{
	terminarEjecucion("<div align='center'>Usuario no autenticado.  Por favor cierre esta ventana y vuelva a ingresar a Matrix.</div>");
} 
else 
{
	global $conex; 
	
	// Consulta de pacientes activos en cl�nica
	$q = "SELECT DISTINCT
		a.Fecha_data fing, a.Hora_data hing, Ubihis, Pactid, Pacced, Pacno1, Pacno2, Pacap1, Pacap2, Ingres, Ingnre, Ubiing
	FROM 
		".$basedatos."_000018 a, ".$basedatos."_000016, root_000036, root_000037
	WHERE 
			Ubimue != 'on' 
		AND Ubiald != 'on' 
		AND Ubihis = Inghis  
		AND Ubiing = Inging
		AND Ubihis = Orihis
		AND Oriori = '".$wemp_pmla."'
		AND Oriced = Pacced
		AND Oritid = Pactid
	GROUP BY Ubihis
	ORDER BY fing DESC, hing DESC";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	$row = mysql_fetch_array($res);
	$listados = array();
	$i=0;
	while ($i<$num)
	{
		// Arreglo para guardar las historias de los pacientes activos en urgencias
		$listados[$i] = $row['Ubihis']."-".$row['Ubiing']; 
		// Arreglo para guardar los ingresos de los pacientes activos en urgencias
		//$listadosing[$i] = $row['Ubiing']; 
		$i++;
		$row = mysql_fetch_array($res);
	}

	// Borra los registros antiguos en la tabla de log
	borrarLogsAntiguos("log_agenda",15);
	
	// Trae desde Unix todos los pacientes activos que deben ser ingresados a Matrix
	actualizarPacientesUnix($basedatos,$basedatoshce,$wemp_pmla,$seguridad,$listados);
	
	/**********************************************************************************
	 ******* INICIA SECCI�N DEL LISTADO DE PACIENTES ACTIVOS HOY **********************
	 *********************************************************************************/

	// Consulta de pacientes activos en urgencias	
	$q = "SELECT DISTINCT
		a.Fecha_data fing, a.Hora_data hing, Ubihis, Pactid, Pacced, Pacno1, Pacno2, Pacap1, Pacap2, Ingres, Ingnre, Ubiing, b.Fecha_data fingb, b.Hora_data hingb
	FROM 
		".$basedatos."_000018 a, ".$basedatos."_000016, root_000036, root_000037, ".$basedatoshce."_000022 b  
	WHERE 
		Ubisac = '".$ccoUrgencias."' 
		AND Ubimue != 'on' 
		AND Ubiald != 'on' 
		AND Ubihis = Inghis  
		AND Ubiing = Inging
		AND Ubihis = Orihis
		AND Oriori = '".$wemp_pmla."'
		AND Oriced = Pacced
		AND Oritid = Pactid
		AND Ubihis = Mtrhis  
		AND Ubiing = Mtring
	GROUP BY Ubihis
	ORDER BY fing DESC, hing DESC";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$row = mysql_fetch_array($res);
		$i=1;
		
		echo "<table border=0 cellspacing=2 cellpadding=0 align=center>"; 

		//Titulo lista de pacientes
		echo "<tr class='fila1'>";
		echo "<td colspan=8 align='center'><strong> &nbsp; PACIENTES ACTIVOS &nbsp; </strong></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td colspan=8>&nbsp;</td>";
		echo "</tr>";
		if($num>0) {
			echo "<tr>";
			echo "<td colspan='8' class='textoMedio'><strong>N&uacute;mero de pacientes: ".$num."</strong></td>";
			echo "</tr>";
		}

		//Encabezado lista de pacientes
		echo "<tr class='encabezadoTabla'>";
		echo "<td align=center>&nbsp;Fecha Ing.&nbsp;<br>&nbsp;Unix&nbsp;</td>";
		echo "<td align=center>&nbsp;Hora Ing.&nbsp;<br>&nbsp;Unix&nbsp;</td>";
		echo "<td align=center>&nbsp;Historia&nbsp;</td>";
		echo "<td align=center>&nbsp;Paciente&nbsp;</td>";
		echo "<td align=center>&nbsp;M&eacute;dico&nbsp;</td>";
		echo "<td align=center>&nbsp;Conducta&nbsp;</td>";
		echo "<td align=center>&nbsp;Afinidad&nbsp;</td>";
		echo "<td align=center>&nbsp;Alta&nbsp;</td>";
		echo "<td align=center>&nbsp;Muerte&nbsp;</td>";
		echo "</tr>";
			 
		//$medicos = consultarMedicosUrgencias($basedatos);
		//$cantidadMedicos = count($medicos);
		$especialidades = consultarEspecialidadesUrgencias($basedatos);
		$cantidadEspecialidades = count($especialidades);

		//Ciclo para recorrer todos los registros de la consulta
		while ($i<=$num)
		{

			if (is_int ($i/2))
			   $wcf="fila1";  // color de fondo de la fila
			else
			   $wcf="fila2"; // color de fondo de la fila

			// Variable que me define si inactivo o no el select de m�dico y los checkbox de alta y muerte
			$inacselect = '';
			$inacselectalt = '';
			$inacselectmue = '';
			   
			// Consulto si el paciente tiene especialidad asociada
			$qmtr =	 " SELECT Mtrhis, Mtring, Espcod, Mtrcur " 
					." FROM ".$basedatoshce."_000022, ".$basedatos."_000044 "
					." WHERE Mtrhis = '".$row['Ubihis']."' "
					." AND Mtring = '".$row['Ubiing']."' "
					." AND Mtreme != '' "
					." AND Mtreme != 'NO APLICA' "
					." AND Mtreme = Espcod "
					." AND Mtrest = 'on' ";

			$resmtr = mysql_query($qmtr, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qmtr . " - " . mysql_error());
			$rowmtr = mysql_fetch_array($resmtr);

			// Si Mtrcur en on el paciente est� siendo atendido, inactivo fila
			if($rowmtr['Mtrcur'] && $rowmtr['Mtrcur']=='on') 
			{
				$wcf = 'fondoAmarillo';
				$inacselect = ' disabled';
				$inacselectalt = ' disabled';
				$inacselectmue = ' disabled';
			}

			// Consulto si el paciente tiene conducta asociada
			$qcon =	 " SELECT Condes, Conadm, Conalt, Conmue " 
					." FROM ".$basedatoshce."_000022, ".$basedatoshce."_000035 "
					." WHERE Mtrhis = '".$row['Ubihis']."' "
					." AND Mtring = '".$row['Ubiing']."' "
					." AND Mtrcon = Concod ";

			$rescon = mysql_query($qcon, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qcon . " - " . mysql_error());
			$rowcon = mysql_fetch_array($rescon);
			
			// Si tiene condcuta que no sea de admisi�n, inactivo fila
			if($rowcon['Condes'] && $rowcon['Condes']!='' && $rowcon['Conadm']!='on') 
			{
				$wcf = 'fondoAmarillo';
				$inacselect = ' disabled';
				$inacselectalt = ' disabled';
				$inacselectmue = ' disabled';
			}
			else
			{
				if($rowcon['Conalt']=='on')
				{
					$inacselectmue = ' disabled';
					$inacselect = ' disabled';
				}

				if($rowcon['Conmue']=='on')
				{
					$inacselectalt = ' disabled';
					$inacselect = ' disabled';

				}
			}
			
			$auxres = explode('-',$row['Ingres']);
			//Se imprime los valores de cada fila
			echo "<tr class=".$wcf.">";
			echo "<td align=left>&nbsp;".$row['fing']."&nbsp;</td>";
			echo "<td align=left>&nbsp;".$row['hing']."&nbsp;</td>";
			echo "<td align=left>";
			echo "<span id='wide".$i."' title='Identificaci&oacute;n: <br>".$row['Pactid']." ".$row['Pacced']."<br><br>Responsable: <br>".$row['Ingnre']." <br>Cod. ".$row['Ingres']."'>";
			echo "&nbsp;".$row['Ubihis']." - ".$row['Ubiing']."&nbsp;";
			echo "</span></td>";
			echo "<td align=left>&nbsp;".$row['Pacno1']." ".$row['Pacno2']." ".$row['Pacap1']." ".$row['Pacap2']."&nbsp;</td>";

			// Columna de m�dico
			echo "<td align=left>";
			echo "<select name='wespecialidad".$i."' id='wespecialidad".$i."' onchange='javascript:asignarEspecialidad(".$i.")'".$inacselect.">";
			echo "<option value='0'> -- seleccione -- </option>";
			foreach ($especialidades as $especialidad)
			{
				if($rowmtr && $especialidad->codigo==$rowmtr['Espcod'])
					echo "<option value=".$especialidad->codigo." selected>".$especialidad->nombre."</option>";
				else
					echo "<option value=".$especialidad->codigo.">".$especialidad->nombre."</option>";
			}
			"</select>";
			echo "<input type='hidden' name='wpaciente".$i."' id='wpaciente".$i."' value='".$row['Ubihis']."'>";
			echo "<input type='hidden' name='wingreso".$i."' id='wingreso".$i."' value='".$row['Ubiing']."'>";
			echo "</td>";

			// Columna de conducta
			echo "<td align=left>";
			if($rowcon['Condes'] && $rowcon['Condes']!='') 
				echo "<input type='text' name='wconducta".$i."' id='wconducta".$i."' style='text-align: center; font-weight: bold;' size='21' value='".$rowcon['Condes']."' readonly>";
			elseif($rowmtr['Mtrcur'] && $rowmtr['Mtrcur']=='on') 
				echo "<input type='text' name='wconducta".$i."' id='wconducta".$i."' style='text-align: center; font-weight: bold;' size='21' value='En consulta' readonly>";
			else
				echo "<input type='text' name='wconducta".$i."' id='wconducta".$i."' style='text-align: center' size='24' value=' -- sin asignar -- ' readonly>";
			echo "</td>";

			// Columna de afinidad
			// En este procedimiento pregunto si el paciente es cliente AFIN o no, y de que tipo
			$wafin = clienteMagenta($row['Pacced'],$row['Pactid'],&$wtpa,&$wcolorpac);
			if ($wafin)
			 echo "<td align=center><font color=".$wcolorpac."><b>".$wtpa."<b></font></td>";
			else
			  echo "<td> &nbsp; </td>";

			echo "<td align=center>&nbsp;<input type='checkbox' onclick='javascript:altaPaciente(".$i.");' name='alta".$i."' id='alta".$i."' value='".$row['Ubihis']."'".$inacselectalt.">&nbsp;</td>";
			echo "<td align=center>&nbsp;<input type='checkbox' onclick='javascript:muertePaciente(".$i.");' name='muerte".$i."' id='muerte".$i."' value='".$row['Ubihis']."'".$inacselectmue.">&nbsp;</td>";
			echo "</tr>";
			
			//Obtengo la siguiente fila
			$row = mysql_fetch_array($res);
			$i++;
		}

		echo "</table>";
	} 
	else 
	{
		echo "<br /><p align='center'>No se encontraron pacientes activos</p><p>&nbsp;</p>";
	}

	echo "<p>&nbsp;</p>";
		
	/*******************************************************************************************
	 ******* INICIA SECCI�N DE PACIENTES INACTIVOS DADOS DE ALTA EN LOS �LTIMOS 2 D�AS *********
	 *******************************************************************************************/

	$ayer = date("Y-m-d",time()-86400);
	$hoy = date("Y-m-d");
	
	// Consulta de pacientes inactivos dados de alta en los �ltimos 2 d�as
	$q = "SELECT DISTINCT
		a.Fecha_data fing, a.Hora_data hing, Ubihis, Pactid, Pacced, Pacno1, Pacno2, Pacap1, Pacap2, Ingres, Ingnre, Ubiing, Ubimue, Ubifad, Ubihad, Ubiuad 
	FROM 
		".$basedatos."_000018 a, ".$basedatos."_000016, root_000036, root_000037, ".$basedatoshce."_000022 b  
	WHERE 
		Ubisac = '".$ccoUrgencias."' 
		AND Ubifad >= '".$ayer."' 
		AND Ubifad != '0000-00-00' 
		AND Ubiald = 'on' 
		AND Ubihis = Inghis  
		AND Ubiing = Inging
		AND Ubihis = Orihis
		AND Oriori = '".$wemp_pmla."'
		AND Oriced = Pacced
		AND Oritid = Pactid
		AND Ubihis = Mtrhis  
		AND Ubiing = Mtring
	ORDER BY Ubifad DESC, Ubihad DESC";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	echo "<table border=0 cellspacing=2 cellpadding=0 align=center>"; 

	//Titulo lista de pacientes
	echo "<tr class='fila1'>";
	echo "<td colspan=8 align='center'><strong> &nbsp; PACIENTES DADOS DE ALTA EN LOS �LTIMOS 2 D�AS &nbsp; </strong></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td colspan=8>&nbsp;</td>";
	echo "</tr>";
	if($num>0) {
		echo "<tr>";
		echo "<td colspan='8' class='textoMedio'><strong>N&uacute;mero de pacientes: ".$num."</strong></td>";
		echo "</tr>";
	}

	if ($num > 0)
	{
		$row = mysql_fetch_array($res);
		$auxi = $i;
		$i=1;
		
		//Encabezado lista de pacientes
		echo "<tr class='encabezadoTabla'>";
		echo "<td align=center>&nbsp;Fecha Ing.&nbsp;<br>&nbsp;Unix&nbsp;</td>";
		echo "<td align=center>&nbsp;Hora Ing.&nbsp;<br>&nbsp;Unix&nbsp;</td>";
		echo "<td align=center>&nbsp;Historia&nbsp;</td>";
		echo "<td align=center>&nbsp;Paciente&nbsp;</td>";
		echo "<td align=center>&nbsp;M&eacute;dico&nbsp;</td>";
		echo "<td align=center>&nbsp;Conducta&nbsp;</td>";
		echo "<td align=center>&nbsp;Afinidad&nbsp;</td>";
		echo "<td align=center>&nbsp;Activar&nbsp;</td>";
		echo "<td align=center>&nbsp;Reasignar&nbsp;<br>&nbsp;Historia&nbsp;</td>";
		echo "</tr>";
			 
		//Ciclo para recorrer todos los registros de la consulta
		while ($i<=$num)
		{

			if (is_int ($i/2))
			   $wcf="fila1";  // color de fondo de la fila
			else
			   $wcf="fila2"; // color de fondo de la fila

			// Consulto si el paciente tiene m�dico tratante asociado
			$qmtr =	 " SELECT Mtrhis, Mtring, Meduma, Medno1, Medno2, Medap1, Medap2 " 
					." FROM ".$basedatoshce."_000022, ".$basedatos."_000048 "
					." WHERE Mtrhis = '".$row['Ubihis']."' "
					." AND Mtring = '".$row['Ubiing']."' "
					." AND Mtrmed != '' "
					." AND Mtrmed != 'NO APLICA' "
					." AND Mtrmed = Meduma "
					." AND Medurg = 'on' "
					." AND Medest = 'on' ";

			$resmtr = mysql_query($qmtr, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qmtr . " - " . mysql_error());
			$rowmtr = mysql_fetch_array($resmtr);

			// Consulto si el paciente tiene conducta asociada
			$qcon =	 " SELECT Condes " 
					." FROM ".$basedatoshce."_000022, ".$basedatoshce."_000035 "
					." WHERE Mtrhis = '".$row['Ubihis']."' "
					." AND Mtring = '".$row['Ubiing']."' "
					." AND Mtrcon = Concod ";

			$rescon = mysql_query($qcon, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qcon . " - " . mysql_error());
			$rowcon = mysql_fetch_array($rescon);
			
			// Consulto el usuario que di� de alta al paciente
			$quad =	 " SELECT Codigo, Descripcion " 
					." FROM usuarios "
					." WHERE Codigo = '".$row['Ubiuad']."'";

			$resuad = mysql_query($quad, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $quad . " - " . mysql_error());
			$rowuad = mysql_fetch_array($resuad);

			$auxres = explode('-',$row['Ingres']);
			//Se imprime los valores de cada fila
			echo "<tr class=".$wcf.">";
			echo "<td align=left>&nbsp;".$row['fing']."&nbsp;</td>";
			echo "<td align=left>&nbsp;".$row['hing']."&nbsp;</td>";
			echo "<td align=left>";
			echo "<span id='wide".$auxi."' title='Identificaci&oacute;n: <br>".$row['Pactid']." ".$row['Pacced']."<br><br>Responsable: <br>".$row['Ingnre']." <br>Cod. ".$row['Ingres']."<br><br>Fecha alta: ".$row['Ubifad']." <br>Hora alta: ".$row['Ubihad']."<br>Di&oacute; de alta: ".$rowuad['Descripcion']."'>";
			echo "&nbsp;".$row['Ubihis']." - ".$row['Ubiing']."&nbsp;";
			echo "</span></td>";
			echo "<td align=left>&nbsp;".$row['Pacno1']." ".$row['Pacno2']." ".$row['Pacap1']." ".$row['Pacap2']."&nbsp;</td>";
			echo "<input type='hidden' name='wpaciente".$auxi."' id='wpaciente".$auxi."' value='".$row['Ubihis']."'>";
			echo "<input type='hidden' name='wingreso".$auxi."' id='wingreso".$auxi."' value='".$row['Ubiing']."'>";
			// Columna de m�dico
			echo "<td align=left>&nbsp;".$rowmtr['Medno1']." ".$rowmtr['Medno2']." ".$rowmtr['Medap1']." ".$rowmtr['Medap2']."&nbsp;</td>";
			// Columna de conducta
			echo "<td align=center>&nbsp;".$rowcon['Condes']."&nbsp;</td>";
			
			// Columna de afinidad
			// En este procedimiento pregunto si el paciente es cliente AFIN o no, y de que tipo
			$wafin = clienteMagenta($row['Pacced'],$row['Pactid'],&$wtpa,&$wcolorpac);
			if ($wafin)
			 echo "<td align=center><font color=".$wcolorpac."><b>".$wtpa."<b></font></td>";
			else
			  echo "<td> &nbsp; </td>";

			// Columna de reactivaci�n de pacientes
			echo "<td align=center>&nbsp;<input type='checkbox' onclick='javascript:activarPaciente(".$auxi.");' name='activar".$auxi."' id='activar".$auxi."' value='".$row['Ubihis']."'>&nbsp;</td>";
			// Columna de reasignaci�n de pacientes
			$desacReasignar = '';
			if($row['Ubiing']>1)
				$desacReasignar = 'disabled';
			echo "<td align=center>&nbsp;<input type='checkbox' onclick='javascript:reasignarPaciente(".$auxi.");' name='reasignar".$auxi."' id='reasignar".$auxi."' value='".$row['Ubihis']."' ".$desacReasignar.">&nbsp;</td>";
			echo "</tr>";
			
			//Obtengo la siguiente fila
			$row = mysql_fetch_array($res);
			$i++; 
			$auxi++;
		}

	} 
	else 
	{
		echo "<tr>";
		echo "<td colspan=8><p align='center'>No se encontraron pacientes dados de alta en los �ltimos 2 d�as</p></td>";
		echo "</tr>";
	}

	echo "</table>";
	
  }
		
}

// Retorna el c�digo del centro de costo en Matrix
function obtenerCcoMatrix($basedatos,$codCco,$wemp_pmla,$seguridad)
{
	global $conex; 
	$cco = "";
	$conex = obtenerConexionBD("matrix");
	
	// Consulto si hay un c�digo asociado en la tabla 11 de movhos
	$qcco = "	SELECT Ccocod   
				  FROM ".$basedatos."_000011 
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
function actualizarDatosPacientes($basedatos,$wemp_pmla,$seguridad,$listados)
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
function ingresarPacientesUrgencias($basedatos,$basedatoshce,$codCco,$whistoria,$wemp_pmla,$seguridad)
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
				elseif($pacienteMatrix->ingresoHistoriaClinica < $pacienteUnix->ingresoHistoriaClinica)
				{
					if(isset($pacienteMatrix->historiaClinica) && !empty($pacienteMatrix->historiaClinica))
					{
						// Consulto si el ingreso no tiene alta definitiva
						$qalt = "	SELECT Ubiald   
									FROM ".$basedatos."_000018 
									WHERE Ubihis  = '".$pacienteMatrix->historiaClinica."' 
									AND	  Ubiing = '".$pacienteMatrix->ingresoHistoriaClinica."'
									AND	  Ubiald != 'on'";
						$resalt = mysql_query($qalt, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qalt . " - " . mysql_error());
						$numalt = mysql_num_rows($resalt);
						// Define si le doy de alta o no 1=no tiene alta 0=tiene alta
						// Si no tiene alta definitiva le doy de alta
						if($numalt>0)
							@altaPacienteUrgencias($basedatos,$basedatoshce,$pacienteMatrix->historiaClinica,$pacienteMatrix->ingresoHistoriaClinica,$wemp_pmla,"Movhos","Ingreso");
						
						$pacienteMatrix->ingresoHistoriaClinica = $pacienteUnix->ingresoHistoriaClinica;
						//$pacienteMatrix->nombre1 = "";
					}
				}
				elseif((($pacienteMatrix->ingresoHistoriaClinica)*1) >= (($pacienteUnix->ingresoHistoriaClinica)*1))
				{
					if(isset($pacienteMatrix->historiaClinica) && !empty($pacienteMatrix->historiaClinica))
					{
						borraIngresosMayores($basedatos,$basedatoshce,$pacienteMatrix->historiaClinica,$pacienteUnix->ingresoHistoriaClinica,$wemp_pmla,"Movhos","BorradoIngresoMayor",$pacienteUnix->fechaIngreso,$pacienteUnix->horaIngreso);

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
			actualizarResponsablePaciente($pacienteMatrix, $pacienteUnix);
		}
		
		// Busca por historia e ingreso en la tabla movhos_000018 si existen registros
		$pacienteIngresado = pacienteIngresado($pacienteMatrix);
		// Busca por historia e ingreso en la tabla hce_000022 si existen registros
		$pacienteIngresadoHce = pacienteIngresadoHce($pacienteMatrix);
		
		
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
					$pacienteMatrix->ingresoHistoriaClinica = $ingresoAnterior;
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
				
			$ingresoPaciente->usuario = "A-".$basedatos;
				
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

// Llamado a las funciones seg�n el par�metro pasado por medio de ajax
if(isset($consultaAjax))
{
	switch($consultaAjax)
	{
		case 10:
			echo asignarEspecialidadUrgencias($basedatos,$basedatoshce,$especialidad,$paciente,$ingreso,$seguridad);
		break;
		case 11:
			echo altaPacienteUrgencias($basedatos,$basedatoshce,$paciente,$ingreso,$wemp_pmla,$seguridad,"Manual");
		break;
		case 12:
			echo muertePacienteUrgencias($basedatos,$basedatoshce,$codcco,$paciente,$ingreso,$seguridad);
		break;
		case 13:
			echo agendaPacientesUrgencias($basedatos,$basedatoshce,$ccoUrgencias,$wemp_pmla,$seguridad);
		break;
		/* 
		// Se usaba para el ingreso de paciente manualmente por el campo de texto
		// Ver actualizaci�n de Abril 20 de 2011
		case 14:
			ingresarPacientesUrgencias($basedatos,$basedatoshce,$ccoUrgencias,$whistoria,$conexUnix,$wemp_pmla,$seguridad);
		break;
		*/
		case 15:
			echo activarPacienteUrgencias($basedatos,$basedatoshce,$codcco,$paciente,$ingreso,$seguridad);
		break;
		case 16:
			echo reasignarPacienteUrgencias($basedatos,$basedatoshce,$codcco,$paciente,$ingreso,$wemp_pmla,$seguridad);
		break;
		default :
			break;
	}
}

//Liberacion de conexion Matrix
liberarConexionBD($conex);

//Liberacion de conexion Unix
liberarConexionOdbc($conexUnix);
?>