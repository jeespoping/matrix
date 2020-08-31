<?php
include_once("conex.php");
header("Content-Type: text/html;charset=ISO-8859-1"); 

include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");
conexionOdbc($conex, $basedatos, &$conexUnix, 'facturacion');
/*
 **************************** AUXILIAR DE AGENDA URGENCIAS ************************************
 ****************************** DESCRIPCIÓN ***************************************************
 * Contiene las funciones principales que usa el script agenda_urgencias.php 
 * Estas funciones se llaman desde AJAX
 *************************************************************************************************
 * Autor: John M. Cadavid. G.
 * Fecha creacion: 2011-02-16
 *************************************************************************************************
 * MODIFICACIONES
 *************************************************************************************************
 * 2011-05-13 - Se cambio el query para consultar médico asignado en la función agendaUrgencias
 *				ya que estaba tomando pacientes con médico en blanco "", y mostraba médico 
 *				con código en blanco ""
 *				Se cambió la función actualizarAltaPacientesUnix ya que no se estaba recorriendo 
 *				el arreglo de forma correcta, se cambió la función while ($j < count ($altas_unix))
 *				por foreach ($altas_unix as $j => $value)
 *************************************************************************************************
 * 2011-04-28 - Cuando es alta automática, se cambio la función <altaPacienteUrgencias> 
 *				para que verifique si en Unix sigue activa la historia, si es asi no le da alta
 *				Se activaron los checbox de actas y muertes para conductas de alta
 *				Se inactiva checbox de muerte si conducta es alta y viceversa
 *************************************************************************************************
 * 2011-04-25 - Modificación en el ingreso de pacientes a urgencias, se quito el campo 
 *				de texto donde se ingresaba la historia clínica, ya se toma de Unix los 
 *				pacientes en urgencias actualizándose la lista automáticamente.
 *************************************************************************************************
 * 2011-03-04 - Modificación en el proceso de ingreso del paciente a urgencias para validar los siguientes casos:
 * 				Si un usuario ya está en movhos 18 pero aun no esta en hce 22, registrarlo en hce 22
 * 				antes se asumia como ya ingresado y no se registraba en hce 22
 *				Validar ingreso de paciente no solo por historia sino tambien por número de ingreso, por ejemplo:
 *				si un usuario está registrado con ingreso 1 y vuelven y lo entran y tiene ya en UNIX ingreso 2
 *				se debe dar de alta automaticamente el ingreso 1 y adicionarlo a la agenda de urgencias con ingreso 2
 *				Validación al activar un paciente dado de alta, si ya está activo con otro ingreso no lo deja activar
 *				En reasignar si el paciente tiene mas de un ingreso no deja reasiganar su número de historia
 *************************************************************************************************
 * 2011-02-23 - Adición de try catch al ejecutar blockUI debido a que en algunas versiones viejas de IE
 * 				sacaba un error y no dejaba ejecutar la página. También se adicionó el evento onload al final del javascript
 *************************************************************************************************
 * 2011-02-22 - Adición de columnas Activar y Reasignar historia para pacientes dados de alta
 * 			
 */

class medicosUrgencias 
{
	var $codigo;
	var $nombre;
} 

// Retorna un arreglo con los médicos actualmente asiganados a urgencias
function consultarMedicosUrgencias($wbasedato)
{
	global $conex; 
	
	$q1=  "	SELECT Meduma, Medno1, Medno2, Medap1, Medap2 "
		."  FROM ".$wbasedato."_000048 "
		."  WHERE Medurg = 'on' "
		."  ORDER by Medno1, Medno2, Medap1, Medap2";
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

/********************************************************************************************
*VERIFICA SI LA HISTORIA DEL PACIENTE SE ENCUENTRA REGISTRADA EN URGENCIAS DE DB UNIX		*
********************************************************************************************/
function consultarPacienteUnix($pacienteConsulta)
{
	global $conexUnix;
	$paciente = new pacienteDTO();
	
	$q = "SELECT
			pacnom, pacap1, pacap2, pacnum, pacfec, pachor, pachab, paccer, pacres, pactid, pacced, pacnac, pacsex, pachos, serccocco, emptip
		FROM 
			inpac, insercco,outer inemp
		WHERE	
			pachis = '".$pacienteConsulta->historiaClinica."' 
			AND serccoser = pacser
			AND paccer = empcod
			AND pacser = '04'";

	$rs = odbc_do($conexUnix,$q);
	$campos = odbc_num_fields($rs);
	
	if (odbc_fetch_row($rs))
	{
		$nombre = explode(" ",trim(odbc_result($rs,1)));
		$paciente->nombre1 = $nombre[0];
		
		if(isset($nombre[1]) && !isset($nombre[2])){
			$paciente->nombre2 = $nombre[1];
		} elseif(isset($nombre[1]) && isset($nombre[2])) {
			$paciente->nombre2 = $nombre[1]." ".$nombre[2];
		} elseif(!isset($nombre[1]) && isset($nombre[2]))  {
			$paciente->nombre2 = $nombre[2];
		} else {
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
		
		if(!isset($paciente->tipoResponsable)){
			$paciente->tipoResponsable = "02";
		} else {
			if($paciente->tipoResponsable == '' || empty($paciente->tipoResponsable)){
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
	
	$q = "SELECT 
				* 
		  	FROM	
		  		root_000036 
			WHERE	
				Pacced = '".$paciente->documentoIdentidad."' 
				AND Pactid = '".$paciente->tipoDocumentoIdentidad."'";
		
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . "-en el query: " . $q . "-" . mysql_error());
	$filas = mysql_num_rows($res);
	
	if($filas > 0){
		$esta = true;	
	}
	return $esta;
}

//Ingresa los datos en la tabla 36 de root
function insertarPacienteTablaUnica($paciente,$seguridad)
{
	global $conex; 
	
	$q = "INSERT INTO 
			root_000036 
				(medico,fecha_data,hora_data,Pacced,Pacno1,Pacno2,Pacap1,Pacap2,Pacnac,Pacsex,Pactid,Seguridad)
			VALUES 
				('root','$paciente->fechaIngreso','$paciente->horaIngreso', '$paciente->documentoIdentidad', '$paciente->nombre1', '".$paciente->nombre2."', '".$paciente->apellido1."', '".$paciente->apellido2."', '".$paciente->fechaNacimiento."', '".$paciente->genero."', '$paciente->tipoDocumentoIdentidad', 'C-".$seguridad."' )";
	
	$err=mysql_query($q,$conex);
}

//Actualiza documento del paciente en la tabla 36 de root
function actualizarDocumentoPacienteTablaUnica($pacienteAnterior, $pacienteNuevo)
{
	global $conex; 
	
	$q = "UPDATE 
			root_000036 
		SET 
			Pacced = '".$pacienteNuevo->documentoIdentidad."',
			Pactid = '".$pacienteNuevo->tipoDocumentoIdentidad."'
		WHERE 
			Pacced = '".$pacienteAnterior->documentoIdentidad."' 
			AND Pactid = '".$pacienteAnterior->tipoDocumentoIdentidad."' ";
		
	$err1=mysql_query($q,$conex);
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

//Ingresa los datos en la tabla 37 de root
function insertarIngresoPaciente($paciente, $wemp_pmla, $seguridad)
{	
	global $conex; 
	
	$q = "INSERT INTO root_000037 
			( medico,fecha_data,hora_data,Oriced,Orihis,Oriing,Oriori,Oritid,Seguridad)
		VALUES 
			('root','".$paciente->fechaIngreso."','".$paciente->horaIngreso."','".$paciente->documentoIdentidad."', '".$paciente->historiaClinica."', '".$paciente->ingresoHistoriaClinica."', '".$wemp_pmla."', '".$paciente->tipoDocumentoIdentidad."', 'C-".$seguridad."' )";
	$err=mysql_query($q,$conex);
}

//Actualiza los datos en la tabla 37 de root
function actualizarIngresoPaciente($pacienteAnterior, $pacienteNuevo, $origen)
{
	global $conex; 
	
	$q = "UPDATE 
			root_000037 
		SET 
			Oriing = '".$pacienteNuevo->ingresoHistoriaClinica."',
			Oriced = '".$pacienteNuevo->documentoIdentidad."',
			Oritid = '".$pacienteNuevo->tipoDocumentoIdentidad."' 
		WHERE 
			Orihis = '".$pacienteNuevo->historiaClinica."'
			AND Oriori = '$origen';";
		
	$err1=mysql_query($q,$conex);
}

//Actualiza el documento del paciente en la tabla 37 de root
function actualizarDocumentoPacienteTablaIngresos($pacienteAnterior, $pacienteNuevo)
{
	global $conex; 
	
	$q = "UPDATE 
			root_000037 
		SET 
			Oriced = '".$pacienteNuevo->documentoIdentidad."',
			Oritid = '".$pacienteNuevo->tipoDocumentoIdentidad."'
		WHERE 
			Orihis = '".$pacienteAnterior->historiaClinica."' 
			AND Oriing = '".$pacienteAnterior->ingresoHistoriaClinica."' ";
		
	$err1=mysql_query($q,$conex);
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
	} 
	else 
	{
		$q = "	UPDATE hce_000022 
				SET Fecha_data='".$fecha."', Hora_data='".$hora."', Mtrmed='', Mtrest='on', Mtrtra='off', Mtretr='', Mtrcur='off', Seguridad='C-".$seguridad."'
				WHERE Mtrhis = '".$ingreso->historiaClinica."'
				AND	Mtring = '".$ingreso->ingresoHistoriaClinica."'";
		
		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	}
}

//Ingresa los datos en la tabla 16 de movhos
function insertarResponsablePaciente($paciente, $wemp_pmla, $seguridad)
{	
	global $conex; 
	
	$q = "INSERT INTO movhos_000016 
			(medico,Fecha_data,Hora_data,Inghis,Inging,Ingres,Ingnre,Ingtip,Seguridad)
		VALUES 
			('movhos','".$paciente->fechaIngreso."','".$paciente->horaIngreso."','".$paciente->historiaClinica."', '".$paciente->ingresoHistoriaClinica."', '".$paciente->numeroIdentificacionResponsable."', '".$paciente->nombreResponsable."', '".$paciente->tipoResponsable."', 'C-".$seguridad."' )";
	
	$err=mysql_query($q,$conex);
}

//Actualiza los datos en la tabla 16 de movhos
function actualizarResponsablePaciente($pacienteAnterior, $pacienteNuevo)
{
	global $conex; 
	
	$q = "UPDATE 
			movhos_000016 
		SET 
			Ingres = '".$pacienteNuevo->numeroIdentificacionResponsable."', 
			Ingnre = '".$pacienteNuevo->nombreResponsable."',
			Ingtip = '".$pacienteNuevo->tipoResponsable."'
		WHERE 
			Inghis = '".$pacienteAnterior->historiaClinica."' 
			AND Inging = '".$pacienteAnterior->ingresoHistoriaClinica."' ";
	$err1=mysql_query($q,$conex);
}

//Ingresa los datos en la tabla 18 de movhos
function grabarIngresoPaciente($ingreso,$seguridad)
{
	global $conex; 
	
	$q = "INSERT INTO 
			movhos_000018 (Medico,Fecha_data,Hora_data,Ubihis,Ubiing,Ubisac,Ubisan,Ubihac,Ubihan,Ubialp,Ubiald,Ubifap,Ubihap,Ubifad,Ubihad,Ubiptr,Seguridad)
		VALUES 
			('movhos','".$ingreso->fechaIngreso."','".$ingreso->horaIngreso."','".$ingreso->historiaClinica."', '".$ingreso->ingresoHistoriaClinica."', '".$ingreso->servicioActual."', '".$ingreso->servicioAnterior."', '".$ingreso->habitacionActual."',  '".$ingreso->habitacionAnterior."','".$ingreso->altaEnProceso."', '".$ingreso->altaDefinitiva."', '".$ingreso->fechaAltaProceso."','".$ingreso->horaAltaProceso."', '".$ingreso->fechaAltaDefinitiva."', '".$ingreso->horaAltaDefinitiva."', '".$ingreso->enProcesoTraslado."', 'C-".$seguridad."' )";
	
	$err=mysql_query($q,$conex);
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
	
	$err = mysql_query($q,$conex);
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
	
	$err = mysql_query($q,$conex);
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

// Asigna el médico tratante a un paciente en urgencias
function asignarMedicoUrgencias($basedatos,$basedatoshce,$medico,$paciente,$ingreso,$seguridad)
{
	global $conex; 
	
	$q = "	SELECT Mtrhis 
			FROM ".$basedatoshce."_000022 
			WHERE Mtrhis = '".$paciente."' 
			AND Mtring = '".$ingreso."'";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	// Consulto la especialidad del médico
	$qesp = "	SELECT Medesp  
				FROM ".$basedatos."_000048 
				WHERE Meduma = '".$medico."' ";
	$resesp = mysql_query($qesp, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qesp . " - " . mysql_error());
	$rowesp = mysql_fetch_array($resesp);
	$especialidad = explode("-",$rowesp['Medesp']);
	
	$fecha = date("Y-m-d");
	$hora = date("H:i:s");
	
	if($num==0) 
	{
		$q = "INSERT INTO 
				".$basedatoshce."_000022 
					(Medico,Mtrfam,Mtrham,Mtrhis,Mtring,Mtrmed,Mtrest,Mtrtra,Mtretr,Mtreme,Mtrcur,Seguridad)
				VALUES 
					('".$basedatoshce."','".$fecha."','".$hora."','".$paciente."','".$ingreso."','".$medico."','on','on','".$especialidad[0]."','".$especialidad[0]."','off','C-".$seguridad."')";
		
		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		
		if($res)
			return "ok";
		else
			return "No se pudo realizar la asignación. \n Error: ".$res;
	} 
	else 
	{
		$q = "	UPDATE ".$basedatoshce."_000022 
				SET Mtrfam='".$fecha."', Mtrham='".$hora."', Mtrmed='".$medico."', Mtrest='on', Mtrtra='on', Mtretr='".$especialidad[0]."', Mtreme='".$especialidad[0]."', Mtrcur='off'
				WHERE Mtrhis = '".$paciente."'
				AND	Mtring = '".$ingreso."'";
		
		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		
		if($res)
			return "ok";
		else
			return "No se pudo realizar la asignación. \n Error: ".$res;
	}
}

// Establece el estado de alta para un paciente en urgencias
function altaPacienteUrgencias($basedatos,$basedatoshce,$codcco,$paciente,$ingreso,$seguridad,$bandera)
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
		
	// Si no está activo en Unix según inpac
	// mira en inpaci si está inactivo con el mismo ingreso
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

	// Si se identificó ingreso diferente al que traemos en inpaci
	// El alta debe ser con borrado de ingreso en Matrix
	if(isset($fields) && $fields==0)
		$tipo_alta = "borrado";
	else
	{
		// Si el alta es automática y se encontraron registros en inpac no se da de alta
		// El que este en inpac y no en urgencias quiere decir que fue trasladado a otro centro de costos
		// Notese que si el alta no es automatica deja dar de alta asi este activo en inpac
		// El operador puede volver a activar el paciente si se equivocó al dar de alta
		if(isset($bandera) && $bandera=="auto" && isset($campos) && $campos>0)
			$tipo_alta = "noalta";
		else
			$tipo_alta = "normal";
	}
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
			// Si tiene conducta asiganada y es alta automática no se da de alta
			if($numcon>0 && isset($bandera) && $bandera=="auto")
			{
				return "no-alta";
			}
			else
			{

				//Consulto si el paciente está en proceso de traslado
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
				
				// Si está en proceso de traslado no se puede dar de alta
				if($numptr>0)
				{
					return "El paciente no se puede dar de alta debido a que está en proceso de traslado para el servicio ".$rowptr['Cconom'];
				}
				else
				{
					//Actualizo tabla 18 de Movhos asignandole los parametros del alta
					$q = "	UPDATE ".$basedatos."_000018 
							SET Ubiald='on', Ubifad='".$fecha."', Ubihad='".$hora."', Ubiuad='".$seguridad."' 
							WHERE Ubihis = '".$paciente."'
							AND	Ubiing = '".$ingreso."'";
					$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	
	
					//Consulto el código de la conducta de alta en la tabla 35 de HCE
					$qalt = "	SELECT Concod  
								FROM ".$basedatoshce."_000035 
								WHERE Conalt = 'on' 
								AND Conadm = 'on'";
					$resalt = mysql_query($qalt, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qalt . " - " . mysql_error());
					$rowalt = mysql_fetch_array($resalt);
					$conducta = $rowalt['Concod'];
					
					// Cosulto si el paciente ya está registrado en la tabla 22 de Hce
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
					} 
					else 
					{
						$q = "	UPDATE ".$basedatoshce."_000022 
								SET Mtrest='on', Mtrtra='off', Mtrcon='".$conducta."', Mtrcur='off', Seguridad='C-".$seguridad."' 
								WHERE Mtrhis = '".$paciente."'
								AND	Mtring = '".$ingreso."'";
						$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					}
	
					//Registro el egreso en la tabla 33 de Movhos
					$q = "	INSERT INTO 
							".$basedatos."_000033 
								(Medico, Fecha_data, Hora_data, Historia_clinica, Num_ingreso, Servicio, Num_ing_serv, Fecha_egre_serv, Hora_egr_serv, Tipo_egre_serv, Dias_estan_serv,Seguridad)
							VALUES 
								('".$basedatos."','".$fecha."','".$hora."','".$paciente."','".$ingreso."','".$codcco."','1','".$fecha."','".$hora."','ALTA','1','C-".$seguridad."')";
					$res2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	
					if($res1)
						return "ok";
					else
						return "Ocurrió un error en el proceso. \n Error: ".$res1;
				}
			}
		}
		elseif($tipo_alta == "borrado")
		{
			//Consulto si el paciente está en proceso de traslado
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
			
			// Si está en proceso de traslado no se puede dar de alta
			if($numptr>0)
			{
				return "El paciente no se puede dar de alta debido a que está en proceso de traslado para el servicio ".$rowptr['Cconom'];
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
							AND	Oriing = '".$ingreso."'";
					$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
				}
				
				//Borro registro en tabla 18 de Movhos 
				$q = "	DELETE 
						  FROM ".$basedatos."_000018 
						 WHERE Ubihis = '".$paciente."'
						   AND Ubiing = '".$ingreso."'";
				$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

				//Borro registro en tabla 16 de Movhos 
				$q = "	DELETE 
						  FROM ".$basedatos."_000016 
						 WHERE Inghis = '".$paciente."'
						   AND Inging = '".$ingreso."'";
				$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

				// Cosulto si el paciente ya está registrado en la tabla 22 de Hce
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
				}

				if($res1)
					return "ok";
				else
					return "Ocurrió un error en el proceso. \n Error: ".$res1;
			}
		}
	}
	else
	{	
		return "El paciente no se puede dar de alta porque aún está activo en el sistema";
	}
}

// Establece el estado de alta por muerte para un paciente en urgencias
function muertePacienteUrgencias($basedatos,$basedatoshce,$codcco,$paciente,$ingreso,$seguridad)
{
	global $conex; 
	
	$fecha = date("Y-m-d");
	$hora = date("H:i:s");

	//Consulto si el paciente está en proceso de traslado
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
	
	// Si está en proceso de traslado no se puede dar de alta
	if($numptr>0)
	{
		return "El paciente no se puede dar de alta por muerte debido a que está en proceso de traslado para el servicio ".$rowptr['Cconom'];
	}
	else
	{
		
		//Actualizo tabla 18 de Movhos asignandole los parametros del alta
		$q = "	UPDATE ".$basedatos."_000018 
				SET Ubimue='on', Ubiald='on', Ubifad='".$fecha."', Ubihad='".$hora."', Ubiuad='".$seguridad."'
				WHERE Ubihis = '".$paciente."'
				AND	Ubiing = '".$ingreso."'";
		$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

		//Consulto el código de la conducta de alta en la tabla 35 de HCE
		$qalt = "	SELECT Concod  
					FROM ".$basedatoshce."_000035 
					WHERE Conmue = 'on' 
					AND Conadm = 'on'";
		$resalt = mysql_query($qalt, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qalt . " - " . mysql_error());
		$rowalt = mysql_fetch_array($resalt);
		$conducta = $rowalt['Concod'];
		
		// Cosulto si el paciente ya está registrado en la tabla 22 de Hce
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
		} 
		else 
		{
			$q = "	UPDATE ".$basedatoshce."_000022 
					SET Mtrest='on', Mtrtra='off', Mtrcon='".$conducta."', Mtrcur='off', Seguridad='C-".$seguridad."' 
					WHERE Mtrhis = '".$paciente."'
					AND	Mtring = '".$ingreso."'";
			$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}

		//Registro el egreso en la tabla 33 de Movhos
		$q = "	INSERT INTO 
				".$basedatos."_000033 
					(Medico, Fecha_data, Hora_data, Historia_clinica, Num_ingreso, Servicio, Num_ing_serv, Fecha_egre_serv, Hora_egr_serv, Tipo_egre_serv, Dias_estan_serv,Seguridad)
				VALUES 
					('".$basedatos."','".$fecha."','".$hora."','".$paciente."','".$ingreso."','".$codcco."','1','".$fecha."','".$hora."','MUERTE','1','C-".$seguridad."')";
		$res2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

		if($res1)
			return "ok";
		else
			return "Ocurrió un error en el proceso. \n Error: ".$res1;
	}
}

// Vuelve y activa un paciente dado de alta
function activarPacienteUrgencias($basedatos,$basedatoshce,$codcco,$paciente,$ingreso,$seguridad)
{
	global $conex; 
	global $conexUnix;	
		
	
	$fecha = date("Y-m-d");
	$hora = date("H:i:s");
	
	// Cosulto si el paciente está en agenda urgencias
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
			
		// Si está activo en Unix según inpac
		if(isset($campos) & $campos>0)
		{
			//Actualizo tabla 18 de Movhos de modo que quite los registros de alta
			$q = "	UPDATE ".$basedatos."_000018 
					SET Ubimue='off', Ubiald='off', Ubialp='off', Ubifap='0000-00-00', Ubihap='00:00:00', Ubifad='0000-00-00', Ubihad='00:00:00', Ubiuad='' 
					WHERE Ubihis = '".$paciente."'
					AND	Ubiing = '".$ingreso."'";
			$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());


			// Cosulto si el paciente ya está registrado en la tabla 22 de Hce
			$q = "	SELECT Mtrhis 
					FROM ".$basedatoshce."_000022 
					WHERE Mtrhis = '".$paciente."' 
					AND Mtring = '".$ingreso."'";
			$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			$num = mysql_num_rows($res);

			// Si está registrado active de nuevo los regisros del paciente
			if($num>0) 
			{
				$q = "	UPDATE ".$basedatoshce."_000022 
						SET Mtrest='on', Mtrcur='off', Mtrcon=''
						WHERE Mtrhis = '".$paciente."'
						AND	Mtring = '".$ingreso."'";
				$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			}

			//Borro el registro de egreso en la tabla 33 de Movhos
			$q = "	DELETE FROM ".$basedatos."_000033 
					WHERE Historia_clinica = '".$paciente."' AND Num_ingreso = '".$ingreso."'";
			$res2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

			if($res1)
				return "ok";
			else
				return "Ocurrió un error en el proceso. \n Error: ".$res1;
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
function reasignarPacienteUrgencias($basedatos,$basedatoshce,$codcco,$paciente,$ingreso,$seguridad)
{
	global $conex; 
	
	// Cosulto si el paciente está en agenda urgencias

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

	/*$qagn = "	SELECT Ubihis 
				FROM ".$basedatos."_000018, ".$basedatoshce."_000022  
				WHERE Ubihis = '".$paciente."' 
				AND Ubimue != 'on' 
				AND Ubiald != 'on' 
				AND Ubisac = '".$codcco."'
				AND Ubihis = Mtrhis  
				AND Ubiing = Mtring ";

	$resagn = mysql_query($qagn, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qagn . " - " . mysql_error());
	$numagn = mysql_num_rows($resagn);*/

	if($numagn==0 && $numtra==0) 
	{
		
		// Borra registro en tabla 16 de Movhos
		$q = "	DELETE FROM ".$basedatos."_000016 
				WHERE Inghis = '".$paciente."'
				AND	Inging = '".$ingreso."'";
		$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

		// Borra registro en tabla 18 de Movhos
		$q = "	DELETE FROM ".$basedatos."_000018 
				WHERE Ubihis = '".$paciente."'
				AND	Ubiing = '".$ingreso."'";
		$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

		// Borra registro de la tabla 37 de Root
		$q = "	DELETE FROM	root_000037 
				WHERE Orihis = '".$paciente."'
				AND	Oriing = '".$ingreso."'";
		$res2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

		// Borra registro de egreso en la tabla 33 de Movhos
		$q = "	DELETE FROM	".$basedatos."_000033 
				WHERE Historia_clinica = '".$paciente."'
				AND	Num_ingreso = '".$ingreso."'";
		$res2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

		// Borra registro en tabla 22 de Hce
		$q = "	DELETE FROM ".$basedatoshce."_000022 
				WHERE Mtrhis = '".$paciente."'
				AND	Mtring = '".$ingreso."'";
		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		
		if($res1)
			return "ok";
		else
			return "Ocurrió un error en el proceso. \n Error: ".$res1;
	} 
	else		
	{
		return "no-reasignar";
	}
}

/********************************************************************************
* CONSULTA EN UNIX LOS PACIENTES QUE NO ESTAN EN URGENCIAS Y LOS DA DE ALTA		*
*********************************************************************************/
function actualizarAltaPacientesUnix($basedatos,$basedatoshce,$ccoUrgencias,$wemp_pmla,$seguridad,$listados)
{
	global $conex;
	global $conexUnix;	
	
	$ayer = date("Y-m-d",time()-86400);

	// Se consultan los pacientes activos en urgencias de Unix
	$qact = "SELECT pachis, pacnum
			   FROM inpac, insercco,outer inemp
			  WHERE serccoser = pacser
				AND paccer = empcod
				AND pacser = '04'
				AND pacfec >= '".$ayer."'";
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
	// pero ya no están como pacientes de urgencias en Unix
	$altas_unix = array_diff($listados,$listados_unix);
	$conting=0;
	// Se da de alta los pacientes que no estan en urgencias de Unix
	foreach ($altas_unix as $j => $value)
	{
		if(isset($altas_unix[$j]) && $altas_unix[$j]!="")
		{
			$paciente_alta = explode("-",$altas_unix[$j]);
			$historia_paciente = $paciente_alta[0];
			$ingreso_paciente = $paciente_alta[1];;
			//echo "<br>ALTA: ".$altas_unix[$j];
			altaPacienteUrgencias($basedatos,$basedatoshce,$ccoUrgencias,$historia_paciente,$ingreso_paciente,"Movhos","auto");
			$conting++;
		}
	}	
}

/********************************************************************************************
* CONSULTA EN UNIX LOS PACIENTES EN URGENCIAS Y ACTUALIZA EL LISTADO DE PACIENTES ACTIVOS 	*
********************************************************************************************/
function actualizarPacientesUnix($basedatos,$basedatoshce,$ccoUrgencias,$wemp_pmla,$seguridad,$listados)
{
	global $conex; 
	global $conexUnix;
	
	if($conexUnix)
	{		

		// Se llama a la funcion para dar de alta a los pacientes que no están en urgencias de Unix
		actualizarAltaPacientesUnix($basedatos,$basedatoshce,$ccoUrgencias,$wemp_pmla,$seguridad,$listados);
		
		$ayer = date("Y-m-d",time()-86400);

		// Se consultan los pacientes activos en urgencias de Unix
		$qact = "SELECT pachis, pacnum
				   FROM inpac, insercco,outer inemp
				  WHERE serccoser = pacser
				    AND paccer = empcod
				    AND pacser = '04'
					AND pacfec >= '".$ayer."'";
		$rs_act = odbc_do($conexUnix,$qact);
		$conting = 0;
		// Ciclo para actualizar el listado de pacientes activos en urgencias
		while (odbc_fetch_row($rs_act))
		{
			$historia_paciente = odbc_result($rs_act,1);
			$ingreso_paciente = odbc_result($rs_act,2);
			$paciente_unix = $historia_paciente."-".$ingreso_paciente;
			// Si la historia clínica obtenida de Unix no está en listado de pacientes, ingrésala.
			if (!in_array($paciente_unix,$listados))
			{
				//echo "<br>INGRESO: ".$paciente_unix;
				ingresarPacientesUrgencias($basedatos,$basedatoshce,$ccoUrgencias,$historia_paciente,$wemp_pmla,$seguridad);
				$conting++;
			}
		}
	}
}


/*********************************************************************
************** LISTADO DE PACIENTES INGRESADOS  **********************
*********************************************************************/				

function agendaPacientesUrgencias($basedatos,$basedatoshce,$ccoUrgencias,$wemp_pmla,$seguridad)
{

// Validación de usuario
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

	actualizarPacientesUnix($basedatos,$basedatoshce,$ccoUrgencias,$wemp_pmla,$seguridad,$listados);
	
	/**********************************************************************************
	 ******* INICIA SECCIÓN DEL LISTADO DE PACIENTES ACTIVOS HOY **********************
	 *********************************************************************************/

	//echo $ccoUrgencias->codigo."<br />";	
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
		echo "<td align=center>&nbsp;Alta&nbsp;</td>";
		echo "<td align=center>&nbsp;Muerte&nbsp;</td>";
		echo "</tr>";
			 
		$medicos = consultarMedicosUrgencias($basedatos);
		$cantidadMedicos = count($medicos);

		//Ciclo para recorrer todos los registros de la consulta
		while ($i<=$num)
		{

			if (is_int ($i/2))
			   $wcf="fila1";  // color de fondo de la fila
			else
			   $wcf="fila2"; // color de fondo de la fila

			// Variable que me define si inactivo o no el select de médico y los checkbox de alta y muerte
			$inacselect = '';
			$inacselectalt = '';
			$inacselectmue = '';
			   
			// Consulto si el paciente tiene médico tratante asociado
			$qmtr =	 " SELECT Mtrhis, Mtring, Meduma, Mtrcur " 
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

			// Si Mtrcur en on el paciente está siendo atendido, inactivo fila
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
			
			// Si tiene condcuta que no sea de admisión, inactivo fila
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

			// Columna de médico
			echo "<td align=left>";
			echo "<select name='wmedico".$i."' id='wmedico".$i."' onchange='javascript:asignarMedico(".$i.")'".$inacselect.">";
			echo "<option value='0'> -- seleccione -- </option>";
			foreach ($medicos as $medico)
			{
				if($rowmtr && $medico->codigo==$rowmtr['Meduma'])
					echo "<option value=".$medico->codigo." selected>".$medico->nombre."</option>";
				else
					echo "<option value=".$medico->codigo.">".$medico->nombre."</option>";
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
		
	/************************************************************************************************
	 ******* INICIA SECCIÓN DE PACIENTES DADOS DE ALTA QUE INGRESARON EN LOS ÚLTIMOS 2 DÍAS *********
	 ************************************************************************************************/

	$ayer = date("Y-m-d",time()-86400);
	//echo $ayer;
	$hoy = date("Y-m-d");
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
		AND Oriori = ".$wemp_pmla."
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
	echo "<td colspan=8 align='center'><strong> &nbsp; PACIENTES DADOS DE ALTA EN LOS ÚLTIMOS 2 DÍAS &nbsp; </strong></td>";
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

			// Consulto si el paciente tiene médico tratante asociado
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
			
			// Consulto el usuario que dió de alta al paciente
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
			// Columna de médico
			echo "<td align=left>&nbsp;".$rowmtr['Medno1']." ".$rowmtr['Medno2']." ".$rowmtr['Medap1']." ".$rowmtr['Medap2']."&nbsp;</td>";
			// Columna de conducta
			echo "<td align=center>&nbsp;".$rowcon['Condes']."&nbsp;</td>";
			// Columna de reactivación de pacientes
			echo "<td align=center>&nbsp;<input type='checkbox' onclick='javascript:activarPaciente(".$auxi.");' name='activar".$auxi."' id='activar".$auxi."' value='".$row['Ubihis']."'>&nbsp;</td>";
			// Columna de reasignación de pacientes
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
		echo "<td colspan=8><p align='center'>No se encontraron pacientes dados de alta en los últimos 2 días</p></td>";
		echo "</tr>";
	}

	echo "</table>";
	
  }
		
}

// Ingresa un paciente a la lista de pacientes en urgencias
function ingresarPacientesUrgencias($basedatos,$basedatoshce,$ccoUrgencias,$whistoria,$wemp_pmla,$seguridad)
{
	global $conex; 
	
	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
	$winstitucion = $institucion->nombre;

	// Grabación de ingreso de paciente

	$conex = obtenerConexionBD("matrix");

	$paciente = new pacienteDTO();

	$paciente->historiaClinica = $whistoria;
	
	$pacienteUnix = consultarPacienteUnix($paciente);  //Paciente Unix				
	if(isset($pacienteUnix->nombre1) && isset($pacienteUnix->ingresoHistoriaClinica))
	{
		$pacienteMatrix = consultarInfoPacientePorHistoria($conex, $paciente->historiaClinica);
		
		$ingresoAnterior = "";
		
		if(!$pacienteMatrix)
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
				if($pacienteMatrix->ingresoHistoriaClinica != $pacienteUnix->ingresoHistoriaClinica)
				{
					if(isset($pacienteMatrix->historiaClinica) && !empty($pacienteMatrix->historiaClinica))
					{

						@altaPacienteUrgencias($basedatos,$basedatoshce,$ccoUrgencias,$pacienteMatrix->historiaClinica,$pacienteMatrix->ingresoHistoriaClinica,"Movhos","Manual");
						$pacienteMatrix->ingresoHistoriaClinica = $pacienteUnix->ingresoHistoriaClinica;
						$pacienteMatrix->nombre1 = "";
					}
				}
			}					
		}
		
		// Si ya se encuentra admitido va a consultar la información
		// La guia es la tabla 18, si esta ahi, YA SE CONSIDERA INGRESADO
		if(!isset($pacienteMatrix->historiaClinica) || empty($pacienteMatrix->historiaClinica))
		{
			$pacienteMatrix->historiaClinica = $pacienteUnix->historiaClinica;
		}
		
		$pacienteIngresado = pacienteIngresado($pacienteMatrix);
		$pacienteIngresadoHce = pacienteIngresadoHce($pacienteMatrix);
		
		
		if(!isset($pacienteMatrix->ingresoHistoriaClinica) or !$pacienteIngresado or !$pacienteIngresadoHce)
		{
			$pacienteEnTablaUnica = false;
			$pacienteEnTablaIngresos = false;
			$pacienteConResponsablePaciente = false;
			$mismoDocumentoIdentidad = false;
				
			//Proceso de ingreso en tabla unica y de ingresos pacientes root_000037, root_000036
			$pacienteEnTablaIngresos = existeEnTablaIngresos($pacienteUnix, $wemp_pmla);
			if(isset($pacienteMatrix->documentoIdentidad))
			{
				$pacienteEnTablaUnica = existeEnTablaUnicaPacientes($pacienteMatrix);							
				$pacienteConResponsablePaciente = existeEnTablaResponsables($pacienteUnix, $wemp_pmla);	

				//DEBUG							 
				$estadoIngreso = $pacienteEnTablaIngresos ? "tiene registro en 37" : "no tiene registro en 37";
				$estadoIngreso = $pacienteEnTablaUnica ? "tiene registro en 36" : "no tiene registro en 36"; 
				$estadoIngreso = $pacienteConResponsablePaciente ? "tiene responsable" : "no tiene responsable";

				if($pacienteMatrix->documentoIdentidad == $pacienteUnix->documentoIdentidad && $pacienteMatrix->tipoDocumentoIdentidad == $pacienteUnix->tipoDocumentoIdentidad)
				{
					if(!$pacienteEnTablaUnica && !$pacienteEnTablaIngresos)
					{
						$mismoDocumentoIdentidad = false;
					}
					else 
					{
						$mismoDocumentoIdentidad = true;
					}
				}
				
			} 
			else 
			{
				$pacienteMatrix->historiaClinica = $whistoria;
				$pacienteMatrix->ingresoHistoriaClinica = $pacienteUnix->ingresoHistoriaClinica;
				$pacienteMatrix->documentoIdentidad = "";
				$pacienteMatrix->tipoDocumentoIdentidad = "";  
			}
				
			//Ingreso de datos en tabla 36 de root
			if(!$pacienteEnTablaUnica)
			{
				insertarPacienteTablaUnica($pacienteUnix,$seguridad);
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
			
			//Ingreso de datos en tabla 16 de movhos
			if(!$pacienteConResponsablePaciente)
			{
				insertarResponsablePaciente($pacienteUnix, $wemp_pmla, $seguridad);
			} 
			else 
			{
				actualizarResponsablePaciente($pacienteMatrix, $pacienteUnix);
			}
				
			if(!$mismoDocumentoIdentidad)
			{
				actualizarDocumentoPacienteTablaIngresos($pacienteMatrix,$pacienteUnix);
				actualizarDocumentoPacienteTablaUnica($pacienteMatrix,$pacienteUnix);
			}
				
			//Proceso de movimiento hospitalario
			$ingresoPaciente = new ingresoPacientesDTO();
				
			$ingresoPaciente->historiaClinica = $pacienteUnix->historiaClinica;
			$ingresoPaciente->ingresoHistoriaClinica = $pacienteUnix->ingresoHistoriaClinica;
			$ingresoPaciente->servicioActual = $ccoUrgencias;
			$ingresoPaciente->habitacionActual = "";

			$ingresoPaciente->fechaIngreso = $pacienteUnix->fechaIngreso;
			$ingresoPaciente->horaIngreso = $pacienteUnix->horaIngreso;
			
			$ingresoPaciente->fechaAltaProceso = "0000-00-00";
			$ingresoPaciente->horaAltaProceso = "00:00:00";
			$ingresoPaciente->fechaAltaDefinitiva = "0000-00-00";
			$ingresoPaciente->horaAltaDefinitiva = "00:00:00";
				
			$ingresoPaciente->enProcesoTraslado = "off";
			$ingresoPaciente->altaDefinitiva = "off";
			$ingresoPaciente->altaEnProceso = "off";
				
			$ingresoPaciente->usuario = "A-".$basedatos;
				
			//Si hay un registro en urgencias
			$pacienteIngresado = pacienteIngresado($pacienteMatrix);
			
			//Grabar ingreso paciente
			$ingresoPaciente->servicioAnterior = "";
			$ingresoPaciente->habitacionAnterior = "";
			
			if(!$pacienteIngresado or !$pacienteIngresadoHce)
			{
				if(!$pacienteIngresado)
					grabarIngresoPaciente($ingresoPaciente, $seguridad);
				
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

// Llamado a las funciones según el parámetro pasado por medio de ajax
if(isset($consultaAjax))
{
	switch($consultaAjax)
	{
		case 10:
			echo asignarMedicoUrgencias($basedatos,$basedatoshce,$medico,$paciente,$ingreso,$seguridad);
		break;
		case 11:
			echo altaPacienteUrgencias($basedatos,$basedatoshce,$codcco,$paciente,$ingreso,$seguridad,"Manual");
		break;
		case 12:
			echo muertePacienteUrgencias($basedatos,$basedatoshce,$codcco,$paciente,$ingreso,$seguridad);
		break;
		case 13:
			echo agendaPacientesUrgencias($basedatos,$basedatoshce,$ccoUrgencias,$wemp_pmla,$seguridad);
		break;
		/* 
		// Se usaba para el ingreso de paciente manualmente por el campo de texto
		// Ver actualización de Abril 20 de 2011
		case 14:
			ingresarPacientesUrgencias($basedatos,$basedatoshce,$ccoUrgencias,$whistoria,$conexUnix,$wemp_pmla,$seguridad);
		break;
		*/
		case 15:
			echo activarPacienteUrgencias($basedatos,$basedatoshce,$codcco,$paciente,$ingreso,$seguridad);
		break;
		case 16:
			echo reasignarPacienteUrgencias($basedatos,$basedatoshce,$codcco,$paciente,$ingreso,$seguridad);
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