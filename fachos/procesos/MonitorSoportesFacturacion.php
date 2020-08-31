<?php
include_once("conex.php");
/**
PROGRAMA : MonitorSoportesFacturacion.php
AUTOR : Camilo Zapata
FECHA CREACION : 10 ENERO de 2013

DESCRIPCION:
Este programa se usa para monitorear la recolección de soportes( documentos ) necesarios para el envio de facturas a las entidades correspondientes

LISTADO DE TABLAS PERTINENTES Y DESCRIPCIÓN BÁSICA:
fachos_000006:	MAESTRO SOPORTES
fachos_000007:	MAESTRO PLANES SE RELACIONA CON movhos_000049	:MAESTRO DE EMPRESAS
fachos_000008:	MAESTRO SERVICIOS
fachos_000009:	TABLA RELACION ENTRE EMPRESAS Y PLANES, RETORNA UN CÓDIGO DE PLAN_EMPRESA
fachos_000010:	TABLA QUE CONTIENE: LOS SOPORTES QUE APLICAN PARA UNA COMBINACIÓN DE PLAN_EMPRESA Y EN QUE SERVICIOS
fachos_000011:	TABLA ENCABEZADO DE LAS LISTAS DE SOPORTES.
fachos_000012:	TABLA DETALLE DE LISTAS DE SOPORTES
fachos_000013:	MAESTRO CARGOS( NIVEL ) DE LOS USUARIOS DENTRO DEL PROCESO DE VERIFICACION DE SOPORTES
fachos_000014:	MAESTRO DE PROCESOS DE REVISION DE SOPORTES.
fachos_000015:	MAESTRO ROLES
fachos_000016:	TABLA DE RELACION DE LISTAS DE CHEQUEO CON ENTIDADES RESPONSABLES.
fachos_000017:	TABLA MAESTRO DE TEMAS.
fachos_000018:	TABLA DE DEFINICION DE PROCESO.
fachos_000019:	RESPONSABLES DE SOPORTES PARA UNA COMBINACION EMPRESA PLAN.
fachos_000020:	MOVIMIENTOS DE LISTA DE CHEQUEO.
fachos_000021:	TABLA DE LOG DEFINICION Y MONITOREO DE SOPORTES.

//DATOS DE LOS PACIENTES
movhos_000016
movhos_000018
movhos_000017
root_000036
root_000037

//PARÁMETROS COMO LOS TIPOS DE FORMATOS, Y LOS NIVELES POR LOS QUE PUEDE PASAR UNA FACTURA
root_000051

ACTUALIZACIONES:

*
**/
session_start();
if(!isset($_SESSION['user']))
{
	die('error');
}




$hoy = date("Y-m-d");
$hora = date("H:i:s");
$caracteres = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£", "°");
$caracteres2 = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U", "");
DEFINE( "ORIGEN","monitor" );
DEFINE( "LOG","000021" );

function titleCambiarProceso()
{
	$title = "<div align=center class=fila1><span class=subtituloPagina2 style=font-size:10px;>DEBE SER ADMINISTRADOR PARA USAR<br>ESTA FUNCION</span></div>";
	return( $title );
}

function verificarCierre( $codigoProceso, $nivelUsuario, $conex, $wfachos, $wtema )
{
	$query = " SELECT dprfin cierra
				 FROM {$wfachos}_000018
				WHERE dprtem = '{$wtema}'
				  AND dprcod = '{$codigoProceso}'
				  AND dprdes = '{$nivelUsuario}'
				  AND dprest = 'on'";
	$rs  = mysql_query( $query, $conex );
	$row = mysql_fetch_array( $rs );
	( trim( $row['cierra'] ) == "" ) ? $row['cierra'] = "" : $row['cierra'] = $row['cierra'];
	return( $row['cierra'] );
}
/**
**/
function insertLog( $conex, $wbasedato, $user_session, $accion, $tabla, $err, $descripcion, $identificacion, $sql_error = "", $responsable, $plan, $servicio, $soporte )
{
    $descripcion = str_replace("'",'"',$descripcion);
    $sql_error = ereg_replace('([ ]+)',' ',$sql_error);

    $insert = " INSERT INTO ".$wbasedato."_".LOG."
                    (Medico, Fecha_data, Hora_data, logori, Logcdu, Logemp, Logpln, Logser, Logsop, Logacc, Logtab, Logerr, Logsqe, Logdes, Loguse, Logest, Seguridad)
                VALUES
                    ('".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."', '".ORIGEN."', '".utf8_decode($identificacion)."', '".$responsable."', '".$plan."', '".$servicio."', '".$soporte."', '".utf8_decode($accion)."','".$tabla."','".$err."', '".$sql_error."','".$descripcion."','".$user_session."','on','C-".$user_session."')";

    $res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - en el query (Insertar En Log): " . $insert . " - " . mysql_error());
}
/*****************************************************************************************************************************************/

/**
FUNCION QUE LE DA FORMATO A LAS OBSERVACIONES, ESTE FORMATO INCLUYE QUIEN Y CUANDO HIZO LA OBSERVACIÓN.
**/

function formatearObservaciones( $observacion1 )
{
	$textoFormateado = "";
	if( trim($observacion1) != "" )
	{
		$observaciones = explode( "$", $observacion1 );
		foreach( $observaciones as $i=>$observacion )
		{
			if( trim($observacion) != "" )
			{
				( $i*1 > 0 ) ? $saltoInicial = "<br>" : $saltoInicial = "";
				$detalleObservacion = explode( "¬", $observacion );
				$textoFormateado .= $saltoInicial.$detalleObservacion[0]."<br>"."<b style='font-size:6pt'>".$detalleObservacion[1]."</b><br>-----------------------------";
			}
		}
	}
	return( $textoFormateado );
}

function formatos( $keyEmpresa, $keyPlan, $keyServicio, $formatos )
{
	$cambiarTodos = "";
	foreach( $formatos as $keyFormato=>$datos )
	{
		$keyFormato = strtoupper( $keyFormato );
		$cambiarTodos .= "<td>{$datos}</td>";
	}
	return ($cambiarTodos);
}

function tiempoEnLaEtapa( $fechaRecibo, $horaRecibo )
{
	global $hoy, $hora;
	$auxiliar = "";

	if( ( trim( $fechaRecibo ) != "") AND ( trim( $horaRecibo ) != "" ) )
	{
		$fechaInicial = strtotime( $fechaRecibo." ".$horaRecibo );
		$fechaFinal   = strtotime( $hoy." ".$hora );
		$diferencia = $fechaFinal - $fechaInicial;
		$restante   = gmdate( "H:i:s", $diferencia );
		$dias 		= floor( $diferencia/( 24*3600 ) );
		( $dias < 0 ) ? $dias = 0 : $dias = $dias*1 ;
		$auxiliar = "{$dias} DIAS {$restante}";
	}
	return( $auxiliar );
}

function cambiarTodos( $keyEmpresa, $keyPlan, $keyServicio, $estados )
{
	$cambiarTodos = "";
	foreach( $estados as $keyEstado=>$datos )
	{
		$cambiarTodos .= "<td>{$datos}<br><input type='radio' style='cursor:pointer;' name='todos_{$keyEmpresa}_{$keyPlan}_{$keyServicio}' title='{$datos} A TODOS' value='{$keyEstado}' onclick='cambiarTodos( \"{$keyEmpresa}_{$keyPlan}_{$keyServicio}\", this )'></td>";
	}
	return ($cambiarTodos);
}

function titleEliminarResponsable( $usuario )
{
	$titleEliminar .= "<div class=fila1 align=center valign=center>";
	$titleEliminar .= "<span class=subtituloPagina2 style=font-size:10px;>AGREGADO POR: {$usuario}</span>";
	$titleEliminar .= "</div>";
	return( $titleEliminar );
}

/**
	ESTA FUNCION IDENTIFICA LOS CENTROS DE COSTOS, QUE PERTENECEN A ALGÚN TIPO DE SERVICIO( urgencias, ayudas, hospitalización, etc... )
**/
function centrosCostosPertinentes()
{
	global $wfachos, $wbasedato, $conex;
	$filtrar = "";
	$query = "SELECT server
				FROM {$wfachos}_000008
			   WHERE serest = 'on'";
	$rs    = mysql_query( $query, $conex );
	while( $row = mysql_fetch_array( $rs ) )
	{
		( $i == 0 ) ? $filtrar .= " {$row[0]} = 'on'" :  $filtrar .= " OR {$row[0]} = 'on'";
		$i++;
	}
	return($filtrar);
}

/**
	FUERA DE USO
**/
function selectResponsables( $responsableActual, $responsables, $empPlan, $soporte, $servicio )
{
	$select  = "";
	$select .= "<select id='responsable_{$empPlan}_{$soporte}_{$servicio}' onchange='actualizarResponsableSoporte(\"{$empPlan}\", \"{$soporte}\", \"{$servicio}\", this)'>";
	foreach( $responsables as $keyResponsable=>$datos )
	{
		( trim($keyResponsable) == trim($responsableActual) ) ? $seleccionado = "selected" : $seleccionado = "";

		$select .= "<option value='{$keyResponsable}' {$seleccionado}>{$datos}</option>";
	}
	$select .= "</select>";
	return($select);
}

/**
	CONSULTA EL CÓDIGO Y EL NOMBRE DE LOS CENTROS DE COSTOS ACTIVOS EN EL SISTEMA
**/
function ConsultarcentrosCostos()
{
	global $wbasedato, $conex, $centrosCostos, $wfachos;
	$query = "SELECT Ccocod codigo, Cconom nombre
				FROM {$wbasedato}_000011
			   WHERE Ccoest = 'on'";
	$rs    = mysql_query( $query, $conex );
	while( $row = mysql_fetch_array( $rs ) )
	{
		$centrosCostos[$row['codigo']]['nombre'] = $row['nombre'];
	}
}

/**
	CONSULTA LOS PACIENTES QUE ESTÁN HABILITADOS PARA REVISIÓN DE SOPORTES, SEA PORQUE TIENEN LISTADO VIGENTE O PORQUE ESTÁN ACTIVOS EN SU INGRESO ACTUAL
**/
function consultarPacientesActivos()
{
	global $wbasedato, $wfachos, $nivelUsuario, $nivelMaximo, $nivelMinimo, $pacientes, $conex, $whis, $wcco, $usuario, $caracteres, $caracteres2, $esAdmin, $puedeCerrar;
	$consultar2 = false;
	$consultar 	= true;

	$centrosCostosPertinentes = centrosCostosPertinentes();
	$centrosCostos 			  = array();

	( $wcco != "%" ) ? $filtroCco = " AND ubisac = '{$wcco}'" : $filtroCco = "AND ubisac != ''";
	if( trim($whis) == "" )
	{
		if( $nivelMinimo == "on" )
		{
			$query = "SELECT Ubihis historia, Ubiing ingreso, Pacno1, Pacno2, Pacap1, Pacap2, Pacced, Pactid, Ubisac ccoActual, Ubihac habitacionActual, pf.id origen, b.fecha_data fechaIngreso, 'n' verFoto, pf.lenfeu fechaRecibo, pf.lenhou horaRecibo, b.ubialp altaProceso, pf.lencer estado, pf.lenrac nivelActual, TIMESTAMPDIFF( SECOND, CONCAT( '{$hoy}', ' ', '{$hora}' ), CONCAT( pf.lenfeu,' ', pf.lenhou ) ) tiempoAtencion
						FROM {$wbasedato}_000018 b
					   INNER JOIN
							 root_000037 on (ubihis = orihis AND ubiing = oriing AND ubiald != 'on' {$filtroCco})
				        LEFT JOIN
							 {$wfachos}_000011 as pf on ( b.Ubihis = pf.lenhis AND b.Ubiing = pf.lening )
					   INNER JOIN
							 root_000036 on ( Pactid = Oritid AND Pacced = Oriced )
					   INNER JOIN
							 {$wbasedato}_000017 as vis on (Eyrhis = Ubihis AND Eyring = Ubiing  AND Eyrest = 'on')
					   INNER JOIN
							 {$wbasedato}_000011 on ( vis.Eyrsor = ccocod AND ({$centrosCostosPertinentes}) )
					   WHERE ( pf.lenrac = '{$nivelUsuario}' or pf.lenrac is null )
					   GROUP BY historia, ingreso, Pacno1, Pacno2, Pacap1, Pacap2, Pacced, Pactid, ccoActual, habitacionActual, origen, fechaIngreso
					   ORDER BY origen asc, tiempoAtencion desc, Pacap1 asc";
					 //  echo $query;
		}else
			{
				$condicionNivelUsuario = "";
				( $esAdmin == "on" ) ? $condicionNivelUsuario = " " : $condicionNivelUsuario = "AND lis.lenrac = '{$nivelUsuario}'";
				( $puedeCerrar == "on" or $esAdmin == "on" ) ? $condicionListasCerradas = " " : $condicionListasCerradas = "AND lis.lencer = 'off'";
					$query = "SELECT lenhis historia, lening ingreso,  Pacno1, Pacno2, Pacap1, Pacap2, Pacced, Pactid,	 Ubisac ccoActual, Ubihac habitacionActual, lis.id origen, ubi.fecha_data fechaIngreso, 'n' verFoto, lis.lenfeu fechaRecibo, lis.lenhou horaRecibo,  ubi.ubialp altaProceso, lis.lencer estado, lis.lenrac nivelActual, TIMESTAMPDIFF( SECOND, CONCAT( '{$hoy}', ' ', '{$hora}' ), CONCAT( lis.lenfeu,' ', lis.lenhou ) ) tiempoAtencion
								FROM {$wbasedato}_000018 as ubi
							   INNER JOIN
									 {$wfachos}_000011 as lis on ( ubi.ubihis = lis.lenhis AND ubi.ubiing = lis.lening {$condicionNivelUsuario} {$filtroCco} {$condicionListasCerradas})
							   INNER JOIN
									 root_000037 as ori on ( ori.Orihis = ubi.Ubihis AND ori.Oriing = ubi.Ubiing )
							   INNER JOIN
									 root_000036 as pac on ( pac.Pactid = ori.Oritid AND pac.Pacced = ori.Oriced )
							   INNER JOIN
									 {$wbasedato}_000017 as vis on (Eyrhis = Ubihis AND Eyring = Ubiing  AND Eyrest = 'on')
							   INNER JOIN
								     {$wbasedato}_000011 on ( vis.Eyrsor = ccocod AND ({$centrosCostosPertinentes}) )
							   GROUP BY  historia, ingreso, Pacno1, Pacno2, Pacap1, Pacap2, Pacced, Pactid, ccoActual, habitacionActual, origen, fechaIngreso
							   ORDER BY tiempoAtencion asc";
				//echo $query;
			}
	}else
		{
			  // ( $nivelMinimo != "on" ) ? $condicionAlta = "" : $condicionAlta = " AND ubiald != 'on' AND ubisac != '' ";
			   ( $esAdmin == "on" ) ? $condicionNivelUsuario = " " : $condicionNivelUsuario = " pf.lenrac = '{$nivelUsuario}'";
				$query = "SELECT Ubihis historia, Ubiing ingreso, Pacno1, Pacno2, Pacap1, Pacap2, Pacced, Pactid, Ubisac ccoActual, Ubihac habitacionActual, pf.id origen, b.fecha_data fechaIngreso, 'n' verFoto,  pf.lenfeu fechaRecibo, pf.lenhou horaRecibo,  b.ubialp altaProceso, pf.lencer estado, pf.lenrac nivelActual
							FROM {$wbasedato}_000018 b
						   INNER JOIN
								 root_000037 on (ubihis = orihis AND ubiing = oriing AND orihis='{$whis}')
							LEFT JOIN
								 {$wfachos}_000011 as pf on (b.Ubihis = pf.lenhis AND  b.Ubiing = pf.lening )
						   INNER JOIN
								 root_000036 on ( Pactid = Oritid AND Pacced = Oriced)
						   INNER JOIN
								 {$wbasedato}_000017 as vis on (Eyrhis = Ubihis AND Eyring = Ubiing  AND Eyrest = 'on')
						   INNER JOIN
								 {$wbasedato}_000011 on ( vis.Eyrsor = ccocod AND ({$centrosCostosPertinentes}) )

						   GROUP BY  historia, ingreso, Pacno1, Pacno2, Pacap1, Pacap2, Pacced, Pactid, ccoActual, habitacionActual, origen, fechaIngreso";
			//echo $query; 						   WHERE ( ({$condicionNivelUsuario}) or pf.lenrac is null )  EN CASO DE SER NECESARIO ESTO IBA ARRIBA DEL GROUP BY ANTERIOR
		}
	if( $consultar )
	{
		$rs  = mysql_query( $query, $conex ) or die( mysql_error() );
		$num = mysql_num_rows($rs);
		while( $row = mysql_fetch_array( $rs ) )
		{
		/*	echo $nivelUsuario."\n";
			echo $row['nivelActual']."\n";*/
		/*	if( $row['estado'] == 'on' )
				echo "cerrado \n";
			if( trim($row['estado']) == '' or ( trim($row['estado']) == 'off' ) )
				echo "abierto \n and \n";
				echo $row['nivelActual']."-----".$nivelUsuario;
			if(  $esAdmin == 'on' or $row['nivelActual'] == $nivelUsuario )
				echo "POR USUARIO \n";
			if(	$nivelMinimo == "on" AND (!isset($row['nivelActual'])) )
				echo "por inicial \n";*/

			if( ( $row['estado'] == 'on' ) or ( ( trim($row['estado']) == '' or ( trim($row['estado']) == 'off' )) AND ( ( $esAdmin == 'on' or trim( $row['nivelActual']) == $nivelUsuario ) or ( $nivelMinimo == "on" AND (!isset($row['nivelActual'])) ) )  ) )
			{
				$nombre = $row['Pacno1']." ".$row['Pacno2']." ".$row['Pacap1']." ".$row['Pacap2'];
				$nombre = str_replace( $caracteres, $caracteres2, $nombre );
				( trim( $row['origen'] ) == "" or !isset( $row['origen'] )) ? $row['origen']="1" :	$row['origen']="0";
				( trim( $row['estado'] ) == 'on' ) ?  $row['origen'] = "3" : $row['origen'] = $row['origen'];

				$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['ingreso'] = $row['ingreso'];
				$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['nombre'] = $nombre;
				$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['cedula'] = $row['Pacced'];
				$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['tipoDocu'] = $row['Pactid'];
				$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['fechaIngreso'] = $row['fechaIngreso'];
				$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['habitacionActual'] = $row['habitacionActual'];
				$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['verFoto'] = $row['verFoto'];
				$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['fechaRecibo'] = $row['fechaRecibo'];
				$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['horaRecibo'] = $row['horaRecibo'];
				$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['altaProceso'] = $row['altaProceso'];
				$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['estadoListado'] = $row['estado'];
			}
		}
	}

	/*************************************************** LISTADO DE PACIENTES CON LISTAS QUE YA HAN SIDO ENVIADAS PERO QUE EL USUARIO DEJO PENDIENTE ALGUN SOPORTE************************************************/
		( trim($whis) == "" ) ? $filtroHistoria = "" : $filtroHistoria  = "AND orihis='{$whis}'";
		$condicionNivelUsuario = "AND lis.lenrac != '{$nivelUsuario}'";
		$query = "SELECT lmohis historia, lmoing ingreso, Pacno1, Pacno2, Pacap1, Pacap2, Pacced, Pactid, Ubisac ccoActual, Ubihac habitacionActual, 2 origen, ubi.fecha_data fechaIngreso, 'n' verFoto, lis.lenfeu fechaRecibo, lis.lenhou horaRecibo,  ubi.ubialp altaProceso, lis.lencer estado
					FROM {$wbasedato}_000018 as ubi
				   INNER JOIN
						{$wfachos}_000020 lmo on ( ubi.ubihis = lmo.lmohis AND ubi.ubiing = lmo.lmoing {$filtroCco})
				   INNER JOIN
						 {$wfachos}_000012 lde on ( lde.delhis = lmo.lmohis AND lde.deling = lmo.lmoing AND lmoori = '{$nivelUsuario}' AND lmo.seguridad='{$usuario}' AND lde.delres = '{$nivelUsuario}' AND lde.delest = 'n')
				   INNER JOIN
						 root_000037 as ori on ( ori.Orihis = ubi.Ubihis AND ori.Oriing = ubi.Ubiing {$filtroHistoria} )
				   INNER JOIN
						 root_000036 as pac on ( pac.Pactid = ori.Oritid AND pac.Pacced = ori.Oriced )
				   INNER JOIN
						 {$wbasedato}_000017 as vis on (Eyrhis = Ubihis AND Eyring = Ubiing  AND Eyrest = 'on')
				   INNER JOIN
						 {$wbasedato}_000011 on ( vis.Eyrsor = ccocod AND ({$centrosCostosPertinentes}) )
				   INNER JOIN
						 {$wfachos}_000011 as lis on ( lmo.lmohis = lis.lenhis AND lmo.lmoing = lis.lening {$condicionNivelUsuario})
				   GROUP BY  historia, ingreso, Pacno1, Pacno2, Pacap1, Pacap2, Pacced, Pactid, ccoActual, habitacionActual, origen, fechaIngreso
				   ORDER BY fechaIngreso";
	if( $consultar )
	{
		$rs  = mysql_query( $query, $conex ) or die( mysql_error() );
		$num = mysql_num_rows($rs);
		while( $row = mysql_fetch_array( $rs ) )
		{
			$nombre = $row['Pacno1']." ".$row['Pacno2']." ".$row['Pacap1']." ".$row['Pacap2'];
			$nombre = str_replace( $caracteres, $caracteres2, $nombre );

			$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['ingreso'] = $row['ingreso'];
			$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['nombre'] = $nombre;
			$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['cedula'] = $row['Pacced'];
			$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['tipoDocu'] = $row['Pactid'];
			$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['fechaIngreso'] = $row['fechaIngreso'];
			$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['habitacionActual'] = $row['habitacionActual'];
			$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['verFoto'] = $row['verFoto'];
			$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['fechaRecibo'] = $row['fechaRecibo'];
			$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['horaRecibo'] = $row['horaRecibo'];
			$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['altaProceso'] = $row['altaProceso'];
			$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['estadoListado'] = $row['estado'];
		}
	}
	return;
}

/**
	FUNCION QUE VERIFICA CUALES SERVICIOS HA UTILIZADO EL PACIENTE, SEGUN LOS CENTROS DE COSTOS QUE HA VISITADO
**/
function centrosCostosVisitados( $whis, $wing )
{
	global $conex, $wbasedato, $wfachos;
	$cco 	   = array();
	$servicios = array();
	$servicios_utilizados = array();
	//-----------------------------------------------------------consulta de servicios prestados por la clinica ---------------------------------------------------------------//

	$query = " SELECT sercod codigo, serdes nombre, server verificacion
				 FROM {$wfachos}_000008
				WHERE serest = 'on'";
	$rs    = mysql_query( $query, $conex );

	while( $row = mysql_fetch_array( $rs ) )
	{
		$servicios[$row['codigo']] = $row['verificacion'];
	}

	//-----------------------------------------------------------servicios usados por el paciente------------------------------------------------------------------------------//
	$query = " SELECT DISTINCT(Eyrsor) codigo
				 FROM {$wbasedato}_000017
				WHERE Eyrhis = '{$whis}'
				  AND Eyring = '{$wing}'
				  AND Eyrest = 'on'";
	$rs    = mysql_query( $query, $conex );
	while( $row = mysql_fetch_array( $rs ) )
	{
		$cco[$row['codigo']] = "";

	}
	//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	//---------------------------------------------------------------------Asociación de centro de costos visitados por Servicios--------------------------------------------------------//
	foreach( $cco as $keyCco => $dato)
	{
		foreach( $servicios as $keyServicio=>$campoVerificacion)
		{
			if( !array_key_exists( $keyServicio, $servicios_utilizados ) )
			{
				$query = "SELECT {$campoVerificacion} valor
							FROM {$wbasedato}_000011
						   WHERE Ccocod = '{$keyCco}'
						     AND Ccoest = 'on'";
				$rs    = mysql_query( $query, $conex );
				$row   = mysql_fetch_array( $rs );
				if( $row['valor'] == 'on' )
					$servicios_utilizados[$keyServicio] = "";
			}
		}
	}

	return( $servicios_utilizados );
}

/**
	CONSULTA LOS SERVICIOS PARA LOS QUE APLICA UNA COMBINACIÓN EMPRESA_PLAN, ESTO SE ESTABLECE DESDE EL GENERADOR DE SOPORTES
**/
function empresa_planes_servicios( $codigoEmpresaPlan, $serviciosCompletos, $serviciosActivoPorPlan )
{
	global $conex, $wfachos;
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

/**
	CONSULTA Y ARMA LOS SELECTS CON LOS POSIBLES ESTADOS DE RECIBO PARA CADA SOPORTE (SI, NO, NO APLICA)
**/
function selectEstadoSoporte( $estadoActual, $estados, $empPlan, $soporte, $servicio, $clase )
{
	$select  = "";
	foreach( $estados as $keyEstado=>$datos )
	{

		( $keyEstado == $estadoActual ) ? $seleccionado = "checked" : $seleccionado = "";
		if( $keyEstado == $estadoActual )
			$valAnterior = "<input type='hidden' name='valAnterior_{$empPlan}_{$soporte}_{$servicio}' value='{$keyEstado}'>";
		$select .= "<td>{$datos}<br><input type='radio' class='{$clase}' style='cursor:pointer;' name='estado_{$empPlan}_{$soporte}_{$servicio}' value='{$keyEstado}' {$seleccionado} onclick='actualizarEstadoSoporte(\"{$empPlan}\", \"{$soporte}\", this, \"{$servicio}\")'></td>";
	}
	$select .= $valAnterior;

	return($select);
}

/**
	CONSULTA Y ARMA LOS SELECTS CON LOS POSIBLES FORMATOS DE RECIBO DE UN SOPORTE(FISICO, ELECTRÓNICO, AMBOS....)
**/
function selectFormatoSoporte( $formatoActual, $formatos, $empPlan, $soporte, $estado, $servicio )
{

	( $estado != "s" ) ? $disabled = "disabled" : $disabled = "";
	$select  = "";
	$clase = "formato_{$empPlan}_{$soporte}_{$servicio}";
	foreach( $formatos as $keyFormato=>$datos )
	{

		( trim($keyFormato) == trim($formatoActual) ) ? $seleccionado = "checked" : $seleccionado = "";
		if( $keyFormato == $formatoActual )
			$valAnterior = "<input type='hidden' name='valAnterior_{$empPlan}_{$soporte}_{$servicio}' value='{$keyFormato}'>";
		$select .= "<td><input type='radio' {$disabled} class='{$clase}' style='cursor:pointer;' name='formato_{$empPlan}_{$soporte}_{$servicio}' value='{$keyFormato}' {$seleccionado} onclick='actualizarFormatoSoporte(\"{$empPlan}\", \"{$soporte}\", this, \"{$servicio}\")'></td>";
	}
	$select .= $valAnterior;

	return($select);
}

/**
	FUNCION QUE VA A BUSCAR LOS PLANES POR CADA ENTIDAD RESPONSABLE Y VA A ARMAR EL DIV QUE PERMITIRÁ ELEGIR LOS PLANES UTILIZADOS POR EL PACIENTE DURANTE SU ESTADIA BAJO LA RESPONSABILIDAD
	DE LA ENTIDAD
**/
function buscarPlanes( $responsable, $planesActivos )
{
	global $conex;
	global $wbasedato, $wfachos;
	$planes  = array();
	$div     = "";
	$options = "";
	$caracteres  = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£");
	$caracteres2 = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U");
	$i = 0;


	//---------------------------------- consulta de los planes que aplican para dicha empresa---------------------------------------------------//
	$query =  "SELECT plncod codigo, plndes nombre, a.pemcod codigoPlanEmpresa
				 FROM {$wfachos}_000007, {$wfachos}_000009 a
				WHERE pememp = '{$responsable}'
				  AND plncod = pempln
				  AND pemest = 'on'
				  AND plnest = 'on'";
	$rs    =  mysql_query( $query, $conex );

	$div .= "<div id='div_pln_{$responsable}' class='fila2' style='cursor:default; display:none; repeat scroll 0 0; position:relative; width:98%; height:98%; overflow:auto;'>";
	$div .= "<div align='center' ><br><span class='subtituloPagina2'>ELEGIR PLAN(ES)</span><table style='border:1; border-style: inset;	border-color: blue;' id='tbl_planes_{$responsable}'>";
	$div .= "<tr style='height:30px' class='encabezadotabla'><td>&nbsp;</td><td>CODIGO</td><td>NOMBRE</td></tr>";
	while( $row = mysql_fetch_array( $rs ) )
	{
		$i++;
		( is_int($i/2) ) ? $wclass = "fila1" : $wclass = "fila2";

		$row['nombre'] = str_replace( $caracteres, $caracteres2, $row['nombre'] );

		array_push( $planes, trim($row['codigo']).", ".trim($row['nombre']) );

		( array_key_exists( $row['codigoPlanEmpresa'], $planesActivos ) ) ? $checked = "checked nuevo='no' " : $checked = " nuevo='si' ";

		$div .= "<tr class='{$wclass}' style='height:30px'><td style='width:50px' align='center'><input type='checkbox' ".$checked." id='chk_{$row['codigoPlanEmpresa']}'></td><td>{$row['codigo']}</td><td>{$row['nombre']}</td></tr>";
	}
	$div .= "</table>";
	$div .= "<br>";
	$div .= "<input type='button' class='botona' value='ACEPTAR' onclick='agregarPlanEmpresa(\"{$responsable}\")'>";
	$div .= "</div>";
	$div .= "</div>";
	return ( $div );
}

/**
	FUNCION QUE ARMA EL CÓDIGO HTML QUE PRESENTA EN PANTALLA LOS DATOS DEL PACIENTE AL QUE SE LE ESTÁN VERIFICANDO LOS DATOS
**/
function datosPaciente( $whis, $wing, $nombre, $tipoDocu, $cedula, $fechaIngreso, $habitacionActual )
{
	global $conex;
	global $control;

	(trim($habitacionActual) == "") ? $habitacionActual = "NO APLICA" : $habitacionActual = $habitacionActual;
	$datosPaciente  = "";
	$datosPaciente .= "<div class='fila2' valign='middle' style='width:100%; height:180px;'>";
	$datosPaciente .= "<span class='subtituloPagina2'>DATOS DEL PACIENTE</span>";
	$datosPaciente .= "<br><br>";
	$datosPaciente .= "<table>";
	$datosPaciente .= "<tr><td class='encabezadotabla'>NOMBRE:&nbsp;&nbsp;</td><td class='fila1'>{$nombre}</td><td>&nbsp;</td><td class='encabezadotabla'>CEDULA:&nbsp;&nbsp;</td><td class='fila1'>({$tipoDocu}) {$cedula}</td></tr>";
	$datosPaciente .= "<tr><td class='encabezadotabla'>HISTORIA:&nbsp;&nbsp;</td><td class='fila1'>{$whis}</td><td>&nbsp;</td><td class='encabezadotabla'>INGRESO:&nbsp;&nbsp;</td><td class='fila1'>{$wing}</td></tr>";
	$datosPaciente .= "<tr><td class='encabezadotabla'>HABITACI&Oacute;N</td><td class='fila1'>{$habitacionActual}</td><td>&nbsp;</td><td class='encabezadotabla'>FECHA INGRESO:&nbsp;&nbsp;</td><td class='fila1'>{$fechaIngreso}</td></tr>";
	$datosPaciente .= "</table>";
	$datosPaciente .= "</div>";

	return($datosPaciente);
}

/**
	FUNCION QUE ARMA EL CÓDIGO HTML QUE PRESENTA EN PANTALLA LAS ENTIDADES RESPONSABLES DEL PACIENTE ASÍ COMO LO NECESARIO PARA AGREGAR O ELIMINAR UN RESPONSABLE
**/
function listadoResponsables( $whis, $wing, $ajax )
{
	global $hoy, $hora, $conex, $wbasedato, $wfachos, $entidades, $entidadesNoResponsables, $usuario, $esAdmin, $nivelUsuario,
	       $caracteres, $caracteres2, $entidadesResponsablesActuales, $numProceso;

	$numProceso   = trim( $numProceso );
	$respuesta    = array();
	$responsables = "";
	$encontrado   = false;
	$emp_plns     = array(); /*arreglo que contiene los codigos de emp_pln que ya están en el listado*/
	$contenedorNoResponsables = "";
	$yaExisteEncabezado 	  = false;

	//-------------------------------------------------CONSULTA DE CODIGOS EMPRESA-PLAN DE LA LISTA---------------------------------------------//
	$query = "SELECT lenemp
			    FROM {$wfachos}_000011
			   WHERE lenhis = '{$whis}'
			     AND lening = '{$wing}'
				 AND lenest = 'on'";
	$rs = mysql_query( $query, $conex );
	while( $row = mysql_fetch_array( $rs ) )
	{
		$row = explode( ",", $row[0] );
		foreach ( $row as $i => $plan )
		{
			$emp_plns[$plan] = "";
		}
	}

	//-------------------------------------------------CONSULTO TODAS LAS ENTIDADES--------------------------------------------------------------//
	$query = "SELECT Pememp codigo, epsnom nombre
				FROM {$wfachos}_000009, {$wbasedato}_000049
			   WHERE Pememp = epscod
			   GROUP BY Pememp";

	$rs = mysql_query( $query, $conex );
	while( $row = mysql_fetch_array( $rs ) )
	{
		$row['nombre'] = str_replace( $caracteres, $caracteres2, $row['nombre'] );
		$entidades[trim($row['codigo'])]['nombre'] = $row['nombre'];
	}

	//--------------------------------------CONSULTO LAS ENTIDADES RESPONSABLES DEL PACIENTE(PUEDEN SER VARIAS)---------------------------------------//

	$query = "SELECT id numDocumento
			    FROM {$wfachos}_000011
			   WHERE lenhis = '{$whis}'
			     AND lening = '{$wing}'
				 AND lenest = 'on'";
	$rs    = mysql_query( $query, $conex );
	$row   = mysql_fetch_array( $rs );
	( trim( $row['numDocumento'] ) != "" ) ? $yaExisteEncabezado = true : $yaExisteEncabezado = false;

	if( $yaExisteEncabezado )
	{
		$query = "SELECT relres responsable, seguridad usuario, id
					FROM {$wfachos}_000016
				   WHERE relhis = '{$whis}'
				     AND reling = '{$wing}'
					 AND relest = 'on'
				   ORDER BY id desc";
		$rs = mysql_query( $query, $conex );
		$i  = 0;
		while( $row = mysql_fetch_array( $rs ) )
		{
			$entidadesResponsablesActuales[$row['responsable']] = $row['usuario'];
			$encontrado = true;
		}
	}
	if(!$yaExisteEncabezado)
	{
		$query = "SELECT Ingres
					FROM {$wbasedato}_000016
				   WHERE Inghis = '{$whis}'
					 AND Inging = '{$wing}'";
		$rs  = mysql_query( $query, $conex );
		$row = mysql_fetch_array( $rs );
		$entidadesResponsablesActuales[$row[0]] = $usuario;
		$encontrado = true;

		//-------------------------------------------- ----------------------------------CREACIÓN DEL ENCABEZADO --------------------------------------------------------------------------//
		$query = "INSERT
					INTO {$wfachos}_000011 (Medico, Fecha_data, Hora_data, lenhis, lening, lenemp, lenpro, lenrac, lenfeu, lenhou, lenest, seguridad)
				  VALUES ( 'fachos', '{$hoy}', '{$hora}', '{$whis}', '{$wing}' ,'', '{$numProceso}', '{$nivelUsuario}', '{$hoy}', '{$hora}', 'on','{$usuario}' )";
		if( $rs    = mysql_query( $query, $conex ) )
		{
			$accion 	    = "INSERT";
			$descripcion    = "Creacion de encabezado de lista";
			$identificacion = "{$whis}-{$wing}";
			insertLog( $conex, $wfachos, $usuario, $accion, "000011", $err, $descripcion, $identificacion, $sql_error = '', '', '', '', '' );
		}

		//----------------------------------------------------------------------------INSERCIÓN INICIAL EN TABLA DE MOVIMIENTO--------------------------------------------------------------//
		$query = "INSERT
				    INTO {$wfachos}_000020 (Medico, Fecha_data, Hora_data, lmohis, lmoing, lmoori, lmodes, lmoest, seguridad)
				  VALUES ('fachos','{$hoy}','{$hora}','{$whis}','{$wing}','','{$nivelUsuario}','on','{$usuario}')";
		$rs = mysql_query( $query, $conex );


		//YA INSERTARIA EN LA TABLA DE RESPONSABLES POR LISTADO
		$query = "INSERT
					INTO {$wfachos}_000016 (Medico, Fecha_data, Hora_data, relhis, reling, relres, relest, seguridad)
				  VALUES ('fachos','{$hoy}','{$hora}','{$whis}',{$wing},'{$row[0]}','on','{$usuario}')";
		$rs    = mysql_query( $query, $conex );
	}
	//LISTADO DE RESPONSABLES
		$responsables .= "<div class='fila2' style='width:100%; height:100%; vertical-align:top;'>";
		$responsables .= "<span class='subtituloPagina2'>PANEL DE RESPONSABLES</span>";
		$responsables .= "<br><br>";
		$responsables .= "<div align='left'>";
		$responsables .= "<table>";
			$responsables .= "<tr><td align='right'><input type='button' class='botona' value='NUEVO RESPONSABLE' onclick='abrirPanelEntidades()'></font></td></tr>";
		$responsables .= "</table>";
		$responsables .= "</div>";
		$responsables .= "<div aling='left' style='overflow-y:scroll; height:105px'>";
		$responsables .= "<table id='tbl_responsables' width='100%'>";
		$responsables .= "<tr class='encabezadotabla'><td width='60px' align='center'>QUITAR</td><td width='60px' align='center'>CODIGO</td><td width='300px' align='center'>NOMBRE</td><td width='40px'>&nbsp;</td></tr>";

		$arrayAuxiliar = $entidadesResponsablesActuales;
		if( count( $arrayAuxiliar ) > 0 )
		{
			foreach($arrayAuxiliar as $keyCodigo => $datos)
			{
				if ( ($arrayAuxiliar[$keyCodigo] == $usuario) or ( $esAdmin == "on" ) )
				{
					$eliminarResponsable = " <td align='center' style='cursor:pointer;' onclick='quitarResponsable(\"{$keyCodigo}\")'><img src='../../images/medical/eliminar1.png' ></td> ";

				}else
					{
						//si el responsable fue agregado por otro usuario se consulta el nombre de este.
						$queryUsuario  = "SELECT descripcion
										   FROM usuarios
										  WHERE codigo = '{$arrayAuxiliar[$keyCodigo]}'";
						$rsUsuario     = mysql_query( $queryUsuario, $conex );
						$rowUsuario    = mysql_fetch_array( $rsUsuario );
						$titleEliminar = titleEliminarResponsable( $rowUsuario['descripcion'] );

						$eliminarResponsable = "<td align='center' class='msg_tooltip{$idTooltip}' title='{$titleEliminar}' style='cursor:pointer;'>&nbsp;</td> ";
					}
				$responsables .= "<tr class='fila1'>{$eliminarResponsable}<td>{$keyCodigo}</td><td>{$entidades[$keyCodigo]['nombre']}</td><td style='cursor:pointer' onclick='verPlanes(\"{$keyCodigo}\")'><font color='blue'>Plan(es)</font></td><td style='display:none'>".buscarPlanes($keyCodigo, @$emp_plns)."</td></tr>";
			}
		}
		$responsables .= "</table>";
		$responsables .= "</div>";
		$responsables .= "</div>";
	foreach( $entidades as $keyEntidad=>$datos )
	{
		if( @!array_key_exists( $keyEntidad , $entidadesResponsablesActuales ) )
		{
			$datos['nombre'] = str_replace( $caracteres, $caracteres2, $datos['nombre'] );
			array_push($entidadesNoResponsables, trim($keyEntidad).", ".trim($datos['nombre']));
		}
	}
	$entidadesNoResponsables = json_encode( $entidadesNoResponsables );
	$encontrado = !$encontrado;
	$respuesta  = array('tieneDatos' => $encontrado, 'panelResponsables' => $responsables, 'arrayNoResponsables'=>$entidadesNoResponsables );
	return($respuesta);
}

/**
	ES LA FUNCION MAS IMPORTANTE DEL PROGRAMA YA QUE PRESENTA EN PANTALLA TODOS LOS SOPORTES DILIGENCIADOS Y NO DILIGENCIADOS ASOCIADOS A LAS FACTURAS DE UN PACIENTE DETERMINADO
	ALLÍ SE PUEDE INTERACTUAR PARA REGISTRAR COMO  RECIBIDO, OBSERVAVIONES, NO APLICA, NO LO RECIBIÓ Y PORQUÉ; VERIFICA SEGUN LAS ENTIDADES RESPONSABLES Y LOS PLANES UTILIZADOS CUALES SON LOS
	DIFERENTES SOPORTES QUE REQUIEREN LAS ENTIDADES, AGRUPADOS EN EL ORDEN DE: EMPRESA, PLAN, SERVICIO Y POR ÚLTIMO EL LISTADO DE SOPORTES
**/
function generarListadoActual()
{
	$i = 0;
	global $conex, $whis, $wing, $error, $wbasedato, $wemp_pmla, $nivelUsuario, $nivelMinimo,
		   $nivelMaximo, $wfachos, $puedeCerrar, $nivelSiguiente, $usuario, $hoy, $hora,
		   $caracteres, $caracteres2, $nivel, $proceso, $esAdmin, $idTooltip, $estadoListado, $wtema;

	$hayDatos      = false;
	$entidades     = array();
	$planesArray   = array();
	$estadosArray  = array();
	$formatosArray = array();
	$serviciosVisitados     = array(); /*almacena los servicios que a utilizado el paciente según los centros de costos por los que ha pasado*/
	$serviciosVisitados     = centrosCostosVisitados( $whis, $wing );
	$responsablesArray 	    = array();
	$serviciosArray    	    = array();
	$empresas_planes        = array();
	$servicios_empresa_plan = array();

	//-----------------------------------------------------------------------------SE CONSULTAN LOS PLANES ASOCIADOS A LA HISTORIA----------------------------------------------------------//
	/*El string resultante se transformará para consultar con la propiedad IN de mysql de tal manera que se consulten todo los soportes asociados a estos planes*/
	( $esAdmin == "on" ) ? $condicionNivelUsuario = "" : $condicionNivelUsuario = " AND ( lenrac = '{$nivelUsuario}' OR lencer='on' ) ";
	$query = " SELECT lenemp, lenobs observacion, lencer cerrado, id num, lenran nivelAnterior, lenrac nivelActual, fecha_data fechaCreacion, lenfeu fechaRecibo, lenhou horaRecibo, lenpro proceso
				 FROM {$wfachos}_000011
				WHERE lenhis = '{$whis}'
				  AND lening = '{$wing}'
				 {$condicionNivelUsuario}
				  AND lenest = 'on'";
	echo $query;
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
		$planes_empresa = $row[0];
		$planes_empresa = explode( ",", $planes_empresa );
		$puedeCerrar    = verificarCierre( $codigoProceso, $nivelUsuario, $conex, $wfachos, $wtema );

		( count($planes_empresa) > 0 and (trim($planes_empresa[0]) != "")) ? $hayPlanesEmpresa = true : $hayPlanesEmpresa = false;

		$query = " SELECT roldes
					 FROM {$wfachos}_000015
					WHERE rolcod = '{$nivelActual}'";
		$rs2  = mysql_query( $query, $conex );
		$row2 = mysql_fetch_array( $rs2 );
		$etapa = $row2[0];
	}

	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------/

	//-------------------------------------------------------------CONSULTA QUE ARMA EL LISTADO A PRESENTAR EN PANTALLA---------------------------------------------------------------------/
	if($hayPlanesEmpresa)
	{
		//-----------------------------------------------------------> consulta el nombre del proceso que rije su revisión <----------------------------------------------------------------//

		$query = " SELECT pronom nombre
					 FROM {$wfachos}_000014
					WHERE procod = '{$codigoProceso}'";
		$rs	   = mysql_query( $query, $conex );
		$row   = mysql_fetch_array( $rs );
		$nombreProceso = $row['nombre'];

		//----------------------------------------------------------->consulta los soportes que ya están guardados<---------------------------------------------/
		$hora1 = str_replace( ":", "_", $hora);
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
				   GROUP BY codigoEmpresa, codigoPlan, servicio, soporte";
		$rs = mysql_query( $query, $conex ) or die( mysql_error() );
		echo $query;
		while( $row = mysql_fetch_array($rs) )//ACA SE ESTAN GUARDANDO EN EL ARREGLO AQUELLOS SOPORTES QUE YA TIENEN ALGUN TIPO DE CONFIGURACIÓN EN EL LISTADO
		{
			$hayDatos = true;
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
						SELECT sesepl empPlan, sessop soporte, sopnom nombre, soptif formato, '' nivelAnterior, '' nivelActual, '' observacion, 'n' estado, serres responsable, sesser servicios, pememp codigoEmpresa, Pempln codigoPlan, soptip tipo
						FROM {$wfachos}_000006, {$wfachos}_000009 b, {$wfachos}_000010, {$wfachos}_000019
					   WHERE sesepl IN ({$planes_empresa})
						 AND sopcod = sessop
						 AND b.pemcod = sesepl
						 AND seremp = b.pememp
						 AND serpln = b.pempln
						 AND sersop = sopcod
						 AND serres = '{$nivelUsuario}'
						 AND serpro = '{$proceso}'
					   GROUP BY codigoEmpresa, codigoPlan, soporte";
			$rs = mysql_query( $query, $conex ) or die( mysql_error() );
			echo $query;
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
					if( in_array( $keyServicio, $servicios) or ($aplicaEnTodos) )//se pregunta si el soporte aplica para cada servicio, sea por especificación o porque el soporte no tiene filtro de servicios
					{
						if(!isset($consolidado[$row['codigoEmpresa']][$row['codigoPlan']][$keyServicio][$row['soporte']]))//ESTO FILTRA QUE EL SOPORTE NO EXISTA YA EN LA TABLA, DE SER ASÍ SE CONSERVAN LOS DATOS YA GUARDADOS EN {$wfachos}_000012
						{
							$consolidado[$row['codigoEmpresa']][$row['codigoPlan']][$keyServicio][$row['soporte']]['estado']  = $row['estado'];
							$consolidado[$row['codigoEmpresa']][$row['codigoPlan']][$keyServicio][$row['soporte']]['existe']  = "n";
							$consolidado[$row['codigoEmpresa']][$row['codigoPlan']][$keyServicio][$row['soporte']]['nombre']  = $row['nombre'];
							$consolidado[$row['codigoEmpresa']][$row['codigoPlan']][$keyServicio][$row['soporte']]['empPlan'] = $row['empPlan'];
							$consolidado[$row['codigoEmpresa']][$row['codigoPlan']][$keyServicio][$row['soporte']]['formato'] = $row['formato'];
							$consolidado[$row['codigoEmpresa']][$row['codigoPlan']][$keyServicio][$row['soporte']]['formatoDefecto'] = $row['formato'];
							$consolidado[$row['codigoEmpresa']][$row['codigoPlan']][$keyServicio][$row['soporte']]['tipo'] 	  = $row['tipo'];
							$consolidado[$row['codigoEmpresa']][$row['codigoPlan']][$keyServicio][$row['soporte']]['responsable'] = $row['responsable'];
							$consolidado[$row['codigoEmpresa']][$row['codigoPlan']][$keyServicio][$row['soporte']]['observacion'] = $row['observacion'];

							$empresas_planes[$row['codigoEmpresa']][$row['codigoPlan']]['codigo'] = $row['empPlan'];
							$servicios_empresa_plan[$row['empPlan']][$keyServicio] = '';

							$query = "INSERT INTO {$wfachos}_000012(Medico, Fecha_data, Hora_data, delhis, deling, delsop, delemp, delser, delfor, delest, delres, delobs, seguridad)
										   VALUES('fachos', '{$hoy}', '{$hora}', '{$whis}', '{$wing}', '{$row['soporte']}', '{$row['empPlan']}', '{$keyServicio}', '', '{$row['estado']}', '{$row['responsable']}', '', '{$usuario}' )";

							$rsAux = mysql_query( $query, $conex );
						}
					}
				}

			}
		}
		if($hayDatos)
		{
			//-------------------------------------------------------------------CONSULTO LAS EMPRESAS------------------------------------------------------------------------------//
			$query = "SELECT Pememp codigo, epsnom nombre
						FROM {$wfachos}_000009, {$wbasedato}_000049
					   WHERE Pememp = epscod
					   GROUP BY Pememp";
			$rs = mysql_query( $query, $conex );

			while( $row = mysql_fetch_array( $rs ) )
			{
				$row['nombre'] = str_replace( $caracteres, $caracteres2, $row['nombre'] );
				$entidades[trim($row['codigo'])]['nombre'] = $row['nombre'];
			}

			//-------------------------------------------------------------------CONSULTO LOS PLANES---------------------------------------------------------------------------------//

			$query = "SELECT Plncod codigo, plndes nombre
						FROM {$wfachos}_000007
					   WHERE plnest = 'on'";

			$rs = mysql_query( $query, $conex );

			while( $row = mysql_fetch_array( $rs ) )
			{
				$row['nombre'] = str_replace( $caracteres, $caracteres2, $row['nombre'] );
				$planesArray[trim($row['codigo'])]['nombre'] = $row['nombre'];
			}
			//-------------------------------------------------------------------CONSULTO LOS DIFERENTES ESTADOS DE UN SOPORTE-----------------------------------------------------------//
			$query = "SELECT Detval
						FROM root_000051
					   WHERE Detemp = '{$wemp_pmla}'
						 AND Detapl = 'estadosSoportesFacturacion'";

			$rs = mysql_query( $query, $conex );

			while( $row = mysql_fetch_array( $rs ) )
			{
				$estados = explode( ",", $row[0] );
				foreach( $estados as $keyEstado=>$datos )
				{
					$estado = explode( "_", $estados[$keyEstado] );
					$estadosArray[$estado[0]] = $estado[1];
				}
			}

			//------------------------------------------------------------CONSULTO LOS DIFERENTES FORMATOS EN LOS QUE SE PUEDE PRESENTAR UN SOPORTE---------------------------------------//
			$query = "SELECT Detval
						FROM root_000051
					   WHERE Detemp = '{$wemp_pmla}'
						 AND Detapl = 'formatosSoportesFacturacion'";

			$rs = mysql_query( $query, $conex );

			while( $row = mysql_fetch_array( $rs ) )
			{
				$formatos = explode( ",", $row[0] );
				foreach( $formatos as $keyFormato=>$datos )
				{
					$formato = explode( "_", $formatos[$keyFormato] );
					$formatosArray[$formato[0]] = $formato[1];
				}
			}

			//-----------------------------------------------------------CONSULTO LOS DIFERENTES RESPONSABLES DE UN SOPORTE-------------------------------------------------------------------//
			$query = "SELECT rolcod codigo, roldes nombre
						FROM {$wfachos}_000015
					   WHERE rolest = 'on'";

			$rs = mysql_query( $query, $conex );
			$responsablesArray["sin"] = " ";

			while( $row = mysql_fetch_array( $rs ) )
			{
				$row['nombre'] = str_replace( $caracteres, $caracteres2, $row['nombre'] );
				$responsablesArray[trim($row['codigo'])] = $row['nombre'];
			}

			//-----------------------------------------------------------CONSULTO LOS SERVICIOS QUE HAY DISPONIBLES EN LA CLINICA-------------------------------------------------------------//

			$query = "SELECT sercod codigo, serdes nombre
						FROM {$wfachos}_000008
					   WHERE serest = 'on'";
			$rs = mysql_query( $query, $conex );
			while( $row = mysql_fetch_array( $rs ) )
			{
				$row['nombre'] = str_replace( $caracteres, $caracteres2, $row['nombre'] );
				$serviciosArray[trim($row['codigo'])]['nombre'] = $row['nombre'];
			}
			$serviciosArray["sd"]['nombre'] = "Sin Filtrar";

			//----------------------------------------------------------CONSULTO LOS DIFERENTES SERVICIOS QUE APLICAN PARA LA EMPRESA Y SUS PLANES----------------------------------------------//
			$empresa_planes_servicios = empresa_planes_servicios( &$empresas_planes, &$serviciosArray, &$servicios_empresa_plan );


			( $puedeCerrar == "on" ) ? $inhabilitarCompleto = "" : $inhabilitarCompleto = "disabled";

			$titles = titlesEnvioDevolucion( &$nivelAnterior, &$nivelSiguiente, $nivelActual, $nivelMinimo, $nivelMaximo, $codigoProceso);
			$titleDevolucion = $titles[0];
			$titleEnvio = $titles[1];

			(( ( $nivelActual == $nivelUsuario ) or ( $esAdmin == "on" ) ) and ( trim($titleDevolucion) == "" ) ) ? $inhabilitarDevolucion = "disabled" : "";
			(( ( $nivelActual == $nivelUsuario ) or ( $esAdmin == "on") ) and ( trim($titleEnvio) == "" ) ) ? $inhabilitarEnvio = "disabled" : "";

			( $estadoListado == "off" ) ? $estado = "En Proceso " : $estado = " Finalizado ";
			( $estadoListado == "on" ) ? $checked = " checked " : $checked = "";
			( $estadoListado == "on" ) ? $inhabilitarMovimiento = " disabled " : $inhabilitarMovimiento = "";
			( $esAdmin == "off" ) ? $inhabilitarCambioProceso  = " disabled " : $inhabilitarCambioProceso = "";
			( $esAdmin == "off" ) ? $titleCambiarProceso  = " title='".titleCambiarProceso()."' " : $titleCambiarProceso = "";


			$tabla .= "<input type='hidden' id='input_nivelListado' value='{$nivelActual}'>";
			$tabla .= "<input type='hidden' id='input_nivelAnterior' value='{$nivelAnterior}'>";
			$tabla .= "<table width=100%>";
			$tabla .= "<tr><td><input type='button'  {$inhabilitarMovimiento} class='botona msg_tooltip{$idTooltip}' title='{$titleDevolucion}' name='btn_devolver' value='<<- DEVOLVER' {$inhabilitarDevolucion} onclick='devolverEnviarListado(\"d\", \"{$nivelAnterior}\", this )'></td><td align='center'><span class='subtituloPagina2'>LISTADO ACTUAL DE SOPORTES</span></td><td align='right'><input type='button' {$inhabilitarMovimiento} class='botona msg_tooltip{$idTooltip}' title='{$titleEnvio}' name='btn_enviar' value='ENVIAR ->>' {$inhabilitarEnvio} onclick='devolverEnviarListado(\"e\", \"{$nivelSiguiente}\", this )'></td></tr>";
			$tabla .= "</table>";
			$tabla .= "<br><br>";
			$tabla .= "<div style='border: 1px solid; border-color: #2A5DB0;'>";
			$tabla .= "<table width='100%'>";
			$tabla .= "<tr><td   width='15%' class='encabezadotabla' style='border: 1px solid; border-color: #2A5DB0;'>Lista de Chequeo N&uacute;mero: </td><td width='30%' align='center' class='fila1' style='border: 1px solid; border-color: #2A5DB0;'>{$numeroDoc}</td>";
				$tabla .= "<td width='15%' class='encabezadotabla' style='border: 1px solid; border-color: #2A5DB0;'>Fecha Creaci&oacute;n: </td><td width='30%' align='center' class='fila1' style='border: 1px solid; border-color: #2A5DB0;'>{$fechaCreacion}</td><td width='10%' rowspan=3 align='center' style='border: 1px solid; border-color: #2A5DB0;'><span class='subtituloPagina2' style='font-size:12px;'>COMPLETO</span><br><input type='checkbox' {$checked} {$inhabilitarCompleto} value='' onclick='cambiarEstadoDelListado(this)'></td>";
			$tabla .= "<tr><td class='encabezadotabla' style='border: 1px solid; border-color: #2A5DB0;'>Estado: </td><td id='td_estado' align='center' class='fila1' style='border: 1px solid; border-color: #2A5DB0;'><b>{$estado}</b></td>";
			$tabla .= "<td width='15%' class='encabezadotabla' style='border: 1px solid; border-color: #2A5DB0;'>Proceso Actual y Tiempo en Espera: </td><td width='30%' align='center' class='fila1' style='border: 1px solid; border-color: #2A5DB0;'>{$etapa} (".tiempoEnLaEtapa( $fechaRecibo, $horaRecibo ).")</td></tr></tr>";
			$tabla .= "<tr><td class='encabezadotabla' style='border: 1px solid; border-color: #2A5DB0;'>Proceso: </td><td id='td_proceso' align='center' class='fila1' style='border: 1px solid; border-color: #2A5DB0;' colspan='2'><b>{$nombreProceso}</b></td><td align='center' ><input {$inhabilitarCambioProceso} type='button' id='btn_cambiarProceso' class='botona msg_tooltip' value='CAMBIAR PROCESO' {$titleCambiarProceso} onclick='cambiarProceso( \"{$codigoProceso}\" );'></td>";
			$tabla .= "</table>";
			$tabla .= "</div>";
			$tabla .= "<br><br>";
			foreach( $consolidado as $keyEmpresa=>$planes )
			{
				$tabla .= "<div align='center'><span class='subtituloPagina2' mostrando='n' style='cursor:pointer;' onclick='mostrarOcultar(\"div_{$keyEmpresa}\", this)'><font size=5><b>{$entidades[$keyEmpresa]['nombre']}</b></font></span></div><br>";
				$tabla .= "<div id='div_{$keyEmpresa}' style='display:none;'>";
				foreach( $planes as $keyPlan=>$servicios )
				{
					$tabla .= "<span class='subtituloPagina2' mostrando='n' style='cursor:pointer;' onclick='mostrarOcultar(\"div_{$keyEmpresa}_{$keyPlan}\", this)'><font size=3><b>PLAN: {$planesArray[$keyPlan]['nombre']} </b></font></span><br>";
					$tabla .= "<div id='div_{$keyEmpresa}_{$keyPlan}' style='display:none;'>";
					$tabla .= "<table width=100% >";
					$tabla .= "</table>";
					foreach( $servicios as $keyServicio=>$soportes )
					{
						$claseHijos 	= "{$keyEmpresa}_{$keyPlan}_{$keyServicio}";
						$cambiarTodos 	= cambiarTodos( $keyEmpresa, $keyPlan, $keyServicio, &$estadosArray );
						$globalFormatos = formatos( $keyEmpresa, $keyPlan, $keyServicio, &$formatosArray );
						$numeroFormatos = count( $formatosArray );
						$tabla .= "<span class='subtituloPagina2' mostrando='n' style='cursor:pointer;' onclick='mostrarOcultar(\"div_{$keyEmpresa}_{$keyPlan}_{$keyServicio}\", this)'><font size=3><b>&nbsp;&nbsp;SERVICIO: {$serviciosArray[$keyServicio]['nombre']} </b></font></span><br>";
						$tabla .= "<div id='div_{$keyEmpresa}_{$keyPlan}_{$keyServicio}' style='display:none;'>";
						$tabla .= "<table width=100% >";
						$tabla .= "<tr class='botona' align='center'><td width=10% colspan=3> RECIBIDO </td><td width=15% rowspan='2'> NOMBRE </td><td width=5% rowspan='2'>TIPO</td><td rowspan='1' colspan='{$numeroFormatos}' width=5%>FORMATO</td><td width='20%' rowspan='2'>RESPONSABLE</td><td rowspan='2' colspan='2' width='45%'>OBSERVACI&Oacute;N</td>";
						$tabla .= "<tr class='botona' align='center'>{$cambiarTodos}{$globalFormatos}</tr>";
						foreach( $soportes as $keySoporte=>$datos )
						{
							$tabla .= "<input type=hidden id='formato_{$datos['empPlan']}_{$keySoporte}_{$keyServicio}' value='{$datos['formatoDefecto']}'>";
							( $datos['existe'] == "s" ) ? $datos['formato'] = $datos['formato'] : $datos['formato'] = "";
							$tabla .= "<tr class='fila1' align='center'>".selectEstadoSoporte(&$datos['estado'], &$estadosArray, &$datos['empPlan'], $keySoporte, $keyServicio, $claseHijos )."<td align='left' nowrap='nowrap' style='width:390px;'> ".str_replace( $caracteres, $caracteres2, $datos['nombre'] )." </td><td> ".str_replace( $caracteres, $caracteres2, $datos['tipo'] )." </td>".selectFormatoSoporte( &$datos['formato'], &$formatosArray, &$datos['empPlan'], $keySoporte, &$datos['estado'], $keyServicio )." </td><td align='left'>".$responsablesArray[$datos['responsable']]."<td><div style='overflow-y:scroll; height:80px; background-color:white;' align='left' height:100px id='txar_{$datos['empPlan']}_{$keySoporte}_{$keyServicio}'>".formatearObservaciones( $datos['observacion'] )."</div></td><td  width='5%' ><input width='100%' type='button' class='botonAdd' onclick='mostrarDivAgregarComentario(".$keySoporte.", ".$datos['empPlan'].", ".$keyServicio.")'></td></tr>";
							$tabla .= "<input type=hidden id='existe_{$datos['empPlan']}_{$keySoporte}_{$keyServicio}' value='{$datos['existe']}'>";
						}
						$tabla .= "<tr class='fila2' align='center'><td width=10% colspan=6 align='center'>&nbsp;</td></tr>";
						$tabla .= "</table>";
						$tabla .= "</div>";
					}
					$tabla .= "</div>";
				}
				$tabla .= "<br>";
				$tabla .= "<br>";
				$tabla .= "</div>";
			}
			$tabla .= "<div align='center'>";
			$tabla .= "<p class='encabezadotabla'>OBSERVACION GENERAL</p>";
			$tabla .= "<textarea id='observacionListado' style='width:400px; height=100px;' onkeypress='if(validar(event)) guardarObservacionGeneral(this)' onblur='guardarObservacionGeneral(this)'>{$observacion}</textarea>";
			$tabla .= "</div>";
			$tabla .= "<br><br>";
			$tabla .= "<table width=100%>";
			$tabla .= "<tr><td><input type='button' {$inhabilitarMovimiento} class='botona msg_tooltip{$idTooltip}' title='{$titleDevolucion}' name='btn_devolver' value='<<- DEVOLVER' {$inhabilitarDevolucion} onclick='devolverEnviarListado(\"d\", \"{$nivelAnterior}\", this )'></td><td align='center'>&nbsp;</td><td align='right'><input type='button' {$inhabilitarMovimiento} class='botona msg_tooltip{$idTooltip}' title='{$titleEnvio}' name='btn_enviar' value='ENVIAR ->>' {$inhabilitarEnvio} onclick='devolverEnviarListado(\"e\", \"{$nivelSiguiente}\", this )'></td></tr>";
			$tabla .= "</table>";
		}
	}else
		{
			$tabla = "<table width=100%>";
			$tabla .= "<tr><td><span class='subtituloPagina2'>NO EXISTE LISTA DE SOPORTES: FALTAN RESPONSABLES Y/O PLANES</span></td></tr>";
			$tabla .= "<br><br>";
			$tabla .= "</table>";
		}
	if(!$hayDatos)
		{
			$tabla = "<table width=100%>";
			$tabla .= "<tr><td><span class='subtituloPagina2'>EL PACIENTE NO HA UTILIZADO NINGUN TIPO SE SERVICIO O EL LISTADO SE ENCUENTRA EN UN NIVEL DE REVISI&Oacute;N DIFERENTE AL SUYO</span></td></tr>";
			$tabla .= "<br><br>";
			$tabla .= "</table>";
		}
	return($tabla);
}


/**
	ARMA EL TITLE QUE INDICA A QUE PARTE DEL PROCESO LLEGA EL LISTADO EN CASO DE SER DEVUELTO O ENVIADO.
**/
function titlesEnvioDevolucion( $nivelAnterior, $nivelSiguiente, $nivelActual, $nivelMinimo, $nivelMaximo, $codigoProceso )
{
	global $wemp_pmla, $conex, $wfachos, $whis, $wing, $esAdmin, $nivelUsuario;
	$titleDev = "";
	$titleEnv = "";

	if(  $nivelMinimo == "off" )
	{
		if( $esAdmin == "off" )
		{
			$query = "SELECT lmohis, lmoing, lmoori codigo, Fecha_data, Hora_data
						FROM {$wfachos}_000020
					   WHERE lmohis = '{$whis}'
						 AND lmoing = '{$wing}'
						 AND lmodes = '{$nivelUsuario}'
						 AND lmotip = 'e'
					   GROUP BY lmohis, lmoing
					  HAVING MAX(fecha_data) and MAX(hora_data)";
			$rs = mysql_query( $query, $conex );
			while( $row = mysql_fetch_array( $rs ) )
			{
				$nivelAnterior = $row['codigo'];
			}
		}

		$query = " SELECT roldes nombre
					 FROM {$wfachos}_000015
					WHERE rolcod = '{$nivelAnterior}'
					  AND rolest = 'on'";
		$rs = mysql_query( $query, $conex );
		$row = mysql_fetch_array( $rs );
		if( trim( $row['nombre'] ) != "" )
		{
			$titleDev .= "<div class=\"fila1\" name=\"tool\" align=\"center\" valign=\"center\">";
			$titleDev .= "<span class=\"subtituloPagina2\" style=\"font-size:10px;\">{$row['nombre']}</span>";
			$titleDev .= "</div>";
		}
	}

	if( ( $esAdmin == "on") and ( $nivelUsuario != $nivelActual ) )
	{
		$query = " SELECT dprdes siguiente
					 FROM {$wfachos}_000018
					WHERE dprcod = '{$codigoProceso}'
					  AND dprori = '{$nivelActual}'
					  AND dprest = 'on'";
		$rs  = mysql_query( $query, $conex );
		//echo $query;
		$row = mysql_fetch_array( $rs );
		$nivelSiguiente = $row['siguiente'];
	}
	$query = " SELECT roldes nombre
				 FROM {$wfachos}_000015
				WHERE rolcod = '{$nivelSiguiente}'";
	$rs = mysql_query( $query, $conex );
	$row = mysql_fetch_array( $rs );
	if( trim($row['nombre']) != "" )
	{
		$titleEnv .= "<div class=\"fila1\" name=\"tool\" align=\"center\" valign=\"center\">";
		$titleEnv .= "<span class=\"subtituloPagina2\" style=\"font-size:10px;\">{$row['nombre']}</span>";
		$titleEnv .= "</div>";
	}

	$titles = array( 0 => $titleDev, 1=> $titleEnv );
	return($titles);
}

/**
	FUNCION QUE IDENTIFICA LOS SOPORTES PENDIENTES EN UNA LISTA DE CHEQUEO QUE YA HA SIDO ENVIADA.
**/

function generarListadoDePendientes( $historia, $ingreso, $nivelUsuario )
{
	global $wbasedato, $wfachos, $conex, $hoy, $hora, $caracteres, $caracteres2;
	$listadoPendientes = "";

	$query = "SELECT a.Fecha_data, a.Hora_data, descripcion usuario, roldes rol
				FROM {$wfachos}_000020 a, usuarios, {$wfachos}_000015
			   WHERE lmohis = '{$historia}'
				 AND lmoing = '{$ingreso}'
				 AND lmotip = 'e'
				 AND lmoori = '{$nivelUsuario}'
				 AND codigo = a.seguridad
				 AND rolcod = lmoori
			   GROUP BY fecha_data, Hora_data
			  HAVING (MAX(a.Fecha_data) AND MAX(a.Hora_data))";
	//echo $query;
	$rs  = mysql_query( $query, $conex );
	$row = mysql_fetch_array( $rs );

	$fechaEnvio = $row['Fecha_data'];
	$horaEnvio  = $row['Hora_data'];
	$usuario    = $row['usuario'];
	$rol  	    = $row['rol'];

	$listadoPendientes .= "<span class='subtituloPagina2'>DETALLE DEL ENV&Iacute;O</span>";
	$listadoPendientes .= "<table width='100%'>";
	$listadoPendientes .= "</tr><td class='encabezadotabla' align='center'>ENVIADO POR: </td><td align='center' class='fila1'>{$usuario}</td><td class='encabezadotabla' align='center'>ETAPA: </td><td align='center' class='fila1'>{$rol}</td><tr>";
	$listadoPendientes .= "</tr><td class='encabezadotabla' align='center'>FECHA DE ENV&Iacute;O:</td><td align='center' class='fila1'>{$fechaEnvio}</td><td class='encabezadotabla' align='center'>HORA DE ENV&Iacute;O</td><td align='center' class='fila1'>{$horaEnvio}</td><tr>";
	$listadoPendientes .= "</table>";

	$query = "SELECT sopcod codigo, sopnom nombre, delobs observacion, epsnom empresa, serdes servicio
			    FROM {$wfachos}_000012
					 INNER JOIN
					 {$wfachos}_000006 on ( sopcod = delsop AND delhis = '{$historia}' AND deling = '{$ingreso}' AND delres = '{$nivelUsuario}' AND delest = 'n')
					 INNER JOIN
					 {$wfachos}_000009 on ( pemcod = delemp )
					 INNER JOIN
					 {$wbasedato}_000049 on ( epscod = pememp )
					 INNER JOIN
					 {$wfachos}_000008 on ( sercod = delser )";

	$rs = mysql_query( $query, $conex );

	//$listadoPendientes .= "<div align='center' style='width:100%;'>";
	$listadoPendientes .= "<br><br>";
	$listadoPendientes .= "<span class='subtituloPagina2'>SOPORTES PENDIENTES</span>";
	$listadoPendientes .= "<table id='tbl_pendientes' width='100%'>";
	$listadoPendientes .= "<tr class='encabezadotabla'><td align=center>NOMBRE</td><td align=center width='300'>EMPRESA</td><td align=center width='300'>SERVICIO</td></tr>";
	while( $row = mysql_fetch_array( $rs ) )
	{
		$row['nombre'] = str_replace( $caracteres, $caracteres2, $row['nombre'] );
		$listadoPendientes .= "<tr class='fila1'><td align='left' nowrap='nowrap'>{$row['nombre']}</td><td align='center' nowrap='nowrap' width='100'>{$row['empresa']}</td><td nowrap='nowrap' align='center'>{$row['servicio']}</td></tr>";
	}
	$listadoPendientes .= "</table>";
	//$listadoPendientes .= "</div>";
	return($listadoPendientes);
}

/** -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	ACÁ INICIAN EL FRAGMENTO DE CÓDIGO EN EL QUE SE UBICAN LOS DIFERENTES IF QUE RESPONDEN A LAS SOLICITUDES AJAX, HACIENDO USO DE LAS DIFERENTES FUNCIONES ESCRITAS Y DESCRITAS ARRIBA
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- **/

/**
	RECIBE LA PETICIÓN DE BUSCAR PACIENTES, LLAMA A LA FUNCION "consultarPacientesActivos" Y GENERA EL HTML QUE PRESENTA EL LISTADO DE PACIENTES CON LISTADOS ACTIVOS O HABILITADOS PARA
	QUE SE LES GENERE LISTADO DE SOPORTES PARA FACTURAR.
**/
if( $peticionAjax == "buscarPacientes" )
{
	$pacientes 	   = array();
	$centrosCostos = array();
	$error = 0;

	consultarPacientesActivos();
	if( count($pacientes) > 0 )
	{
		ConsultarcentrosCostos();
	}else
		{
			$error = 1;
		}
	if( $error == 0 )
	{
		ksort( $pacientes, true );
		foreach($pacientes as $keyOrigen=> $ccos)
		{
			switch ($keyOrigen)
			{
				case '1':
					$nombreOrigen = "<b>SIN</b> LISTA DE CHEQUEO";
					$colspan = "1";
					$colspanEncabezado = "8";
					break;
				case '0':
					$nombreOrigen = "LISTA DE CHEQUEO <b>CREADAS</b> Y PENDIENTES";
					$colspan = "1";
					$colspanEncabezado = "8";
					break;
				case '2':
					$nombreOrigen = "LISTA DE CHEQUEO <b>ENVIADAS</b> CON PENDIENTES";
					$colspan = "2";
					$colspanEncabezado = "9";
					break;
				case '3':
					$nombreOrigen = "LISTA DE CHEQUEO <b>CERRADAS</b>";
					$colspan = "1";
					$colspanEncabezado = "8";
					break;
			}
			$menu .= "<br><br>";
			$menu .= "<span class='subtituloPagina2' mostrando='s' style='cursor:pointer;' onclick='ocultarMostrarOrigenPacientes(\"div_{$keyOrigen}\", this )'>{$nombreOrigen}</span>";
			$menu .= "<div id='div_{$keyOrigen}'>";
			$menu .= "<table width='80%' style='border:0;'>";
			foreach( $ccos as $keyCco=>$historias )
			{
				$i = 0;
				$menu .= "<tr class='encabezadotabla'><td colspan='{$colspanEncabezado}' style='height:30;'>{$keyCco} - {$centrosCostos[$keyCco]['nombre']}</td><tr>";
				$menu .= "<tr class='botona'><td>HABITACION</td><td>HISTORIA</td><td>INGRESO</td><td>NOMBRE</td><td align='center'>FECHA INGRESO</td><td align='center'>TIEMPO EN ETAPA</td><td align='center'>ESTADO LISTADO</td><td colspan='{$colspan}'>&nbsp;</td><tr>";
				foreach( $historias as $keyHistoria => $datos )
				{
					switch( $keyOrigen )
					{
						case '1':
							$accion = "<td align='center' nowrap='nowrap' style='cursor:pointer;' onclick='verSoportes( \"{$keyHistoria}\", \"{$datos['ingreso']}\", \"{$datos['nombre']}\", \"{$datos['tipoDocu']}\", \"{$datos['cedula']}\", \"{$datos['fechaIngreso']}\", \"{$datos['habitacionActual']}\", \"si\" )'><font color='blue'>CREAR LISTA</font></td>";
							$verFoto = "";
							break;
						case '0':
							$accion = "<td align='center' style='cursor:pointer;' onclick='verSoportes( \"{$keyHistoria}\", \"{$datos['ingreso']}\", \"{$datos['nombre']}\", \"{$datos['tipoDocu']}\", \"{$datos['cedula']}\", \"{$datos['fechaIngreso']}\", \"{$datos['habitacionActual']}\", \"no\" )'><font color='blue'>VER SOPORTES</font></td>";
							$verFoto = "";
							break;
						case '2':
							$accion = "<td align='center' style='cursor:pointer;' onclick='verPendientes( \"{$keyHistoria}\", \"{$datos['ingreso']}\", \"{$datos['nombre']}\", \"{$datos['tipoDocu']}\", \"{$datos['cedula']}\", \"{$datos['fechaIngreso']}\", \"{$datos['habitacionActual']}\", \"no\" )'><font color='blue'>VER SOPORTES PENDIENTES</font></td>";
							$verFoto = "<td align='center' style='cursor:pointer;' onclick='verFoto( \"{$keyHistoria}\", \"{$datos['ingreso']}\" )'><font color='blue'>VER REGISTRO DE ENVIO</font></td>";
							break;
						case '3':
							$accion = "<td align='center' style='cursor:pointer;' onclick='verSoportes( \"{$keyHistoria}\", \"{$datos['ingreso']}\", \"{$datos['nombre']}\", \"{$datos['tipoDocu']}\", \"{$datos['cedula']}\", \"{$datos['fechaIngreso']}\", \"{$datos['habitacionActual']}\", \"no\" )'><font color='blue'>VER SOPORTES</font></td>";
							$verFoto = "";
							break;
					}

					switch( $datos['estadoListado'] )
					{
						case '':
							$estado = "SIN";
							break;
						case 'off':
							$estado = "EN PROCESO";
							break;
						case 'on':
							$estado = "CERRADO";
							break;
					}
					$i++;
					( is_int($i/2) ) ? $wclass = 'fila1' : $wclass = 'fila2';
					if( $datos['altaProceso'] == "on" )
						$wclass = 'fondoamarillo';
					$menu .= "<tr class='{$wclass}' >";
					$menu .= "<td align='center' style='height:30;'>{$datos['habitacionActual']}</td>";
					$menu .= "<td align='center' style='height:30;'>{$keyHistoria}</td>";
					$menu .= "<td align='center'>{$datos['ingreso']}</td>";
					$menu .= "<td>{$datos['nombre']}</td>";
					$menu .= "<td align='center'>{$datos['fechaIngreso']}</td>";
					$tiempoEnEtapa = tiempoEnLaEtapa( $datos['fechaRecibo'], $datos['horaRecibo'] );
					( trim( $tiempoEnEtapa ) == "" ) ? $tiempoEnEtapa = "&nbsp;" : $tiempoEnEtapa;
					$menu .= "<td align='center'>".$tiempoEnEtapa."</td>";
					$menu .= "<td align='center'>".$estado."</td>";
					$menu .= $accion;
					$menu .= $verFoto;
					$menu .= "<tr>";
				}
			}
		$menu .= "</table>";
		$menu .= "</div>";
	  }
	}else
		{
			$query = "SELECT roldes nombre
						FROM {$wfachos}_000015
					   WHERE rolcod = '{$nivelUsuario}'";
			$rs = mysql_query( $query, $conex );
			$row = mysql_fetch_array( $rs );
			$menu = "<span class='subtituloPagina2'>SIN LISTADOS PENDIENTES POR VERIFICAR EN ESTA ETAPA ({$row['nombre']})</span>";
		}
	$data = array('pacientes'=>$menu, 'error'=>$error );
	echo json_encode( $data );
	return;
}

/**
	RECIBE LA PETICIÓN DE VERIFICAR DATOS INICIALES DEL PACIENTE, LLAMA A LA FUNCIONES: "datosPaciente", "listadoResponsables", Y GENERA EL HTML QUE PRESENTA LOS DATOS DEL PACIENTE COMO
	HISTORIA, INGRESO, NOMBRE, HABITACION, ETC...
	ADEMAS CONSULTA EL LISTADO ACTUAL DE RESPONSABLES ASOCIADOS AL PACIENTE, PARA ARMAR EL PANEL QUE PERMITA LA EDICIÓN DE LOS MISMOS.
	ADICIONALMENTE ES EL FRAGMENTO DE CODIGO ENCARGADO DE ARMAR LA MAQUETA DE LA APLICACION.
**/
if( $peticionAjax == "verificarDatosIniciales" )
{
	$html  = "";
	$error = 0;
	$datosPaciente = "";
	$listadoActual = "";
	$control 	   = false; /*variable para el control de errores*/
	$resultados    = array();
	$entidades     = array();
	$entidadesResponsablesActuales = array();
	$entidadesNoResponsables       = array();
	$serviciosUtilizado 		   = array();

	$datosPaciente = datosPaciente( $whis, $wing, $nombre, $tipoDocu, $cedula, $fechaIngreso, $habitacionActual );
	/*SI SE ENCONTRARON DATOS DE LA HISTORIA Y EL INGRESO*/
	if($datosPaciente != "" )
	{
		$resultados = listadoResponsables( $whis, $wing, false ); /*en distintas posiciones almacena responsables y NO responsables;*/
		if( $resultados['tieneDatos'] )
		{
			$listadoActual = "<div class='fila2' style='width=100%; height=100%;'>";
			$listadoActual .= "<span class='subtituloPagina2'>LISTADO ACTUAL DE SOPORTES</span><br><br>";
			$listadoActual .= "<br><br>";
			$listadoActual .= "<span class='subtituloPagina2'>NO SE HA DEFINIDO NINGUN PLAN PARA LOS RESPONSABLES</span><br><br>";
			$listadoActual .= "</div>";
		}

		$html .= "<table id='tb_maqueta' width='1400'>";
			$html .= "<tr><td style='vertical-align:top; border: 1px solid; border-color: #2A5DB0; width:40%;'>{$datosPaciente}</td><td>&nbsp</td><td id='div_responsables' aling='left' style='vertical-align:top; border: 1px solid; border-color:#2A5DB0;'>".$resultados['panelResponsables']."</td>";
			$html .= "<tr>";
			$html .= "<tr><td colspan=3 ><br><br><div class='fila2' id='div_listadoActual' style='width=100%; height=100%; border: 1px solid; border-color:#2A5DB0;'>".$listadoActual."</div><td><tr>";
		$html .= "</table>";
	}

	$data = array( "maqueta"=>$html, "error"=>$error, "entidadesNoResponsables"=>$resultados['arrayNoResponsables'] );
	echo json_encode( $data );
	return;
}

/**
	RECIBE LA PETICIÓN DE GENERAR LISTADO ACTUAL, LLAMA A LA FUNCION "generarListadoActual" LA CUAL ES LA MAS IMPORTANTE
**/
if( $peticionAjax == "generarListadoActual")
{
	$error   = 0;
	$estadoListado = "";
	$listado = generarListadoActual();
	$data    = array( "lista"=>$listado, "error"=>$error, "cerrado"=>$estadoListado );
	echo json_encode($data);
	return;
}

/**
	RECIBE LA PETICIÓN DE AGREGAR UN NUEVO RESPONSABLE RECIBE LOS DATOS( codigo ) DEL NUEVO RESPONSABLE DEL PACIENTE Y SE AGREGA A LA TABLA DEL ENCABEZADO DE LA LISTA
**/
if( $peticionAjax == "agregarResponsable" )
{
	$resultado = array();
	$entidadesResponsable    = array();
	$entidadesNoResponsables = array();
	$numeroResponsables      = 0;

	/*CONSULTO LAS ENTIDADES RESPONSABLES DEL PACIENTE(PUEDEN SER VARIAS)*/
	$query = "SELECT relres
			    FROM {$wfachos}_000016
			   WHERE relhis = '{$whis}'
			     AND reling = '{$wing}'
				 AND relest = 'on'";
	$rs = mysql_query( $query, $conex );

	while( $row = mysql_fetch_array( $rs ) )
	{
		$entidadesResponsable[$row[0]] = "";
	}

	( array_key_exists( $wcodigo, $entidadesResponsable ) ) ? $error = 1 : $error = 0;
	if( $error == 0 )
	{
		$query = "INSERT
					INTO {$wfachos}_000016 (Medico, Fecha_data, Hora_data, relhis, reling, relres, relest, seguridad)
				  VALUES ('fachos','{$hoy}','{$hora}','{$whis}', '{$wing}','{$wcodigo}','on','{$usuario}')";
		if( $rs = mysql_query( $query, $conex ) )
		{
			$accion 	    = "INSERT";
			$descripcion    = "AGREGO responsable ";
			$identificacion = "{$whis}-{$wing}";
			insertLog( $conex, $wfachos, $usuario, $accion, "000016", $err, $descripcion, $identificacion, $sql_error = '', $wcodigo , '', '', '' );
		}
	}
	$result = listadoResponsables( $whis, $wing, true );
	$data   = array ("error"=>$error, "tr"=>$result['panelResponsables'], "noResponsables"=>$result['arrayNoResponsables'] );
	echo json_encode($data);
	return;
}

/**
	RECIBE LA PETICIÓN DE AGREGAR UN NUEVO PLAN(ES) DE UNA EMPRESA, PARA NO PERDER LA REFERENCIA DE LOS PLANES ASOCIADOS A UN PACIENTE, SE ALMACENAN EN EL ENCABEZADO LOS CÓDIGOS DE LOS
	PLANES_EMPRESA CORRESPONDIENTES A LA ESTADIA DEL PACIENTE
**/
if( $peticionAjax == "agregarPlanEmpresa" )
{
	$wempresas_planes;
	$empPlanesAgregar = array();
	$empPlanesRemover = array();
	$empresasPlanesNuevo = "";
	$error = 0;
	 //---------------------------INSERCION DEL ENCABEZADO DE LA LISTA-----------------------------------------//

	$empresasplanesFiltro = explode( ",",$wempresas_planes );
	foreach ($empresasplanesFiltro as $pos => $dato )
	{
		($pos==0) ? $filtro .= "'".$empresasplanesFiltro[$pos]."'" : $filtro .= ",'".$empresasplanesFiltro[$pos]."'";
	}

	//CONSULTO LOS CODIGOS EMPRESA_PLAN DE LA LISTA ASOCIADA AL PACIENTE(PUEDEN SER VARIOS)
	$query = "SELECT lenemp
			    FROM {$wfachos}_000011
			   WHERE lenhis = '{$whis}'
			     AND lening = '{$wing}'
				 AND lenemp != ''
				 AND lenest = 'on'";
	$rs = mysql_query( $query, $conex );
	while( $row = mysql_fetch_array( $rs ) )
	{
		$empresasPlanesAux = $row['lenemp'];
	}
	$empresasPlanesAux = explode( ",", $empresasPlanesAux );

	foreach( $empresasPlanesAux as $key => $dato )
	{
		$empresasPlanesResponsable[$empresasPlanesAux[$key]] = "";
	}

	foreach( $empresasplanesFiltro as $i => $wcodigo)
	{
		$wcodigo = explode ( "_", $wcodigo );
		$accion = $wcodigo[1];
		$wcodigo = $wcodigo[0];

		( $accion == "add" ) ?  $empPlanesAgregar[$wcodigo] = "" : $empPlanesRemover[$wcodigo] = "";
	}
	$i=0;

	foreach( $empPlanesAgregar as $keyCodigo => $dato)
	{
		($i==0) ? $empresasPlanesNuevo = $keyCodigo : $empresasPlanesNuevo .= ",".$keyCodigo;
		$i++;
	}

	foreach( $empresasPlanesResponsable as $keyCodigo => $dato)
	{
		if( !array_key_exists( $keyCodigo, $empPlanesRemover ) and $keyCodigo != "" )
		{
			($i==0) ? $empresasPlanesNuevo = $keyCodigo : $empresasPlanesNuevo .= ",".$keyCodigo;
			$i++;
		}else
			{
				$queryEliminar = " DELETE
									 FROM {$wfachos}_000012
									WHERE delhis = '{$whis}'
									  AND deling = '{$wing}'
									  AND delemp = '{$keyCodigo}'";
				$rsEliminar = mysql_query( $queryEliminar, $conex );
			}
	}

	$query = " UPDATE {$wfachos}_000011
				  SET lenemp = '{$empresasPlanesNuevo}'
				WHERE lenhis = '{$whis}'
				  AND lening = '{$wing}'";
	$rs = mysql_query( $query, $conex ) or die( mysql_error() );

	$data = array( "error"=>$error );
	echo json_encode( $data );
	return;
}

/**
	RECIBE LA PETICIÓN DE ACTUALIZAR EL ESTADO DE RECIBIDO DE UN SOPORTE.
**/
if( $peticionAjax == "actualizarSoporteListado" )
{
	( $westado == "n" OR $westado == "na" ) ? $deshabilitarEstado = ", delfor = ''" : $deshabilitarEstado = "";
	$query = "UPDATE {$wfachos}_000012
				 SET delest = '{$westado}'{$deshabilitarEstado}
			   WHERE delhis = '{$whis}'
				 AND deling = '{$wing}'
				 AND delsop = '{$soporte}'
				 AND delemp = '{$wempPlan}'
				 AND delser = '{$servicio}'";
	  //$rs = mysql_query( $query, $conex );
	  if( $rs = mysql_query( $query, $conex ) )
	  {
		 $accion 	     = "UPDATE";
		 $descripcion    = "actualizo soporte para el codigo de empresa-plan: {$wempPlan} --  valor {$westado} ";
		 $identificacion = "{$whis}-{$wing}";
		 insertLog( $conex, $wfachos, $usuario, $accion, "000016", $err, $descripcion, $identificacion, $sql_error = '', $wempPlan, '', $servicio, $soporte );
	  }
	 // echo $query;
	return;
}

/**
	RECIBE LA PETICIÓN DE ACTUALIZAR FORMATO DE RECIBIDO DE UN SOPORTE.
**/
if( $peticionAjax == "actualizarFormatoSoporteListado" )
{
	$query = "UPDATE {$wfachos}_000012
				 SET delfor = '{$wformato}'
			   WHERE delhis = '{$whis}'
				 AND deling = '{$wing}'
			     AND delsop = '{$soporte}'
				 AND delemp = '{$wempPlan}'
				 AND delser = '{$wservicio}'";
	//$rs = mysql_query( $query, $conex );
	if( $rs = mysql_query( $query, $conex ) )
		{
			$accion 	    = "UPDATE";
			$descripcion    = "cambio de formato del soporte para la empresa_plan {$wempPlan} valor: {$wformato} ";
			$identificacion = "{$whis}-{$wing}";
			insertLog( $conex, $wfachos, $usuario, $accion, "000016", $err, $descripcion, $identificacion, $sql_error = '', $wempPlan, '', $servicio, $soporte );
		}
	return;
}

/**
	RECIBE LA PETICIÓN DE ELIMINAR UN  RESPONSABLE RECIBE LOS DATOS( codigo ) DEL  RESPONSABLE DEL PACIENTE Y SE ELIMINA DE LA TABLA DEL ENCABEZADO DE LA LISTA
**/
if( $peticionAjax == "eliminarResponsable" )
{
	$error = 1;
	$planesAeliminar = array();
	$entidadesNoResponsables = array();
	//------------------------------------------CONSULTO LAS COMBINACIONES DE EMPRESA_PLAN ASOCIADOS AL RESPONSABLE QUE SE VA A ELIMINAR-----------------------------------------------//
	$planes_empresa = "AND delemp IN (";
	$query = " SELECT pemcod codigo
				 FROM {$wfachos}_000009
				WHERE Pememp = '{$wempresa}'
				  AND Pemest = 'on'";

	$rs = mysql_query( $query, $conex ) or die ( mysql_error() ) ;
	$i = 0;
	while( $row = mysql_fetch_array( $rs ) )
	{
		$error = 0;
		$planesAelminar[$row['codigo']] = "";
		( $i == 0) ? $planes_empresa .= "'{$row['codigo']}'" : $planes_empresa .= ",'{$row['codigo']}'";
		$i++;
	}
	$planes_empresa .= ")";

	//------------------------------------------------------------- ELIMINACION DEL RESPONSABLE EN EL DETALLE----------------------------------------------------------------------//

	$query = " DELETE
				 FROM {$wfachos}_000012
				WHERE delhis = '{$whis}'
				  AND deling = '{$wing}'
				  {$planes_empresa}";
	$rs = mysql_query( $query, $conex );



	//---------------------------------------------------------- PROCESO DE ELIMINACION DEL RESPONSABLE EN EL ENCABEZADO---------------------------------------------------------//

	//---QUITA EL RESPONSABLE--------///
	( $esAdmin == "on" ) ? $filtroUsuario = "" : $filtroUsuario = " AND seguridad = '{$usuario}' ";
	$query = " DELETE
				 FROM {$wfachos}_000016
				WHERE relhis = '{$whis}'
				  AND reling = '{$wing}'
				  AND relres = '{$wempresa}'
				  AND relest = 'on'
				  {$filtroUsuario}";

	//$rs = mysql_query( $query, $conex );
	if( $rs = mysql_query( $query, $conex ) )
	{
		$accion 	    = "DELETE";
		$descripcion    = "Elimino responsable {$wempresa}";
		$identificacion = "{$whis}-{$wing}";
		insertLog( $conex, $wfachos, $usuario, $accion, "000016, 000012", $err, $descripcion, $identificacion, $sql_error = '', $wempresa, '', $servicio, $soporte );
	}

	//---QUITA LOS CODIGOS EMPRESA_PLAN ASOCIADOS AL RESPONSABLE EN EL ENCABEZADO------//
	$query = "SELECT lenemp
				FROM {$wfachos}_000011
			   WHERE lenhis = '{$whis}'
				 AND lening = '{$wing}'";
	//echo $query;
	$rs = mysql_query( $query, $conex );

	$row = mysql_fetch_array( $rs );

	$empresa_planes = explode(",",$row[0]);
	if( count($planesAeliminar) > 0 )
	{
		foreach( $planesAelminar as $keyEmpresaPlan => $dato )
		{
			$posicionEntidad = array_search( $keyEmpresaPlan, $empresa_planes );
			if( $posicionEntidad !== false )
				unset($empresa_planes[$posicionEntidad]);
		}
		$empresa_planes = implode( ",", $empresa_planes);
		$query = "UPDATE {$wfachos}_000011
					 SET lenemp = '{$empresa_planes}'
				   WHERE lenhis = '{$whis}'
					 AND lening = '{$wing}'";
		$rs = mysql_query( $query, $conex );
	}
	$result = listadoResponsables( $whis, $wing, true );
	$data = array( "error"=>$error, "tr"=>$result['panelResponsables'], "noResponsables"=>$result['arrayNoResponsables'] );
	echo json_encode($data);
	return;
}

/**
	RECIBE LA PETICIÓN DE GUARDAR OBSERVACION, ESTA FUNCION TIENE CASOS ESPECIALES Y ES QUE PERMITE GUARDAR REGISTROS DE SOPORTES NO RECIBIDOS, SIEMPRE Y CUANDO TENGA UNA OBSERVACIÓN QUE
	HAGA NECESARIO EL REGISTRO EN BD.
**/
if( $peticionAjax == "guardarObservacion" )
{
	//ACÁ LE DOY FORMATO AL TEXTO PARA QUE MANTENGA LA FORMA:

	$pie   		   = $nombreUsuario." ".$hoy." ".$hora;
	$txtFormateado = strtoupper( $wobservacion )."¬".$pie;

	$query = " UPDATE {$wfachos}_000012
				  SET delobs = CONCAT('{$txtFormateado}', '$' ,delobs )
				WHERE delhis = '{$whis}'
				  AND deling = '{$wing}'
				  AND delsop = '{$wsoporte}'
				  AND delemp = '{$wempPlan}'
				  AND delser = '{$wservicio}'";
	//echo $query;
	//$rs = mysql_query( $query, $conex );
	if( $rs = mysql_query( $query, $conex ) )
	{
		$accion 	    = "UPDATE";
		$descripcion    = "adiciono observacion para el soporte.";
		$identificacion = "{$whis}-{$wing}";
		insertLog( $conex, $wfachos, $usuario, $accion, "000012", $err, $descripcion, $identificacion, $sql_error = '', $wempPlan, '', $wservicio, $wsoporte );
	}

	$data = array( "fecha"=>$hoy, "hora"=>$hora );
	echo json_encode( $data );
	return;
}

/**
	RECIBE LA PETICIÓN CAMBIAR EL NIVEL(ENVIAR A LA SIGUIENTE O LA ANTERIOR ETAPA DEL PROCESO ) A UN LISTADO.
**/
if( $peticionAjax == "cambiarNivelListado" )
{
	$respuesta = "";
	$registroAfectado = "";
	$nuevoNivel = 0;
	$error = 1;
	$nivelSiguiente = trim( $nivelSiguiente );
	$nivelListado   = trim( $nivelListado );
	if( $waccion == "e" ) //if( $waccion == "e" and $nivelMaximo == "off" )
	{
		$query = "UPDATE {$wfachos}_000011
					 SET lenrac = '{$nivelSiguiente} ',
						 lenran = '{$nivelListado}',
						 lenfeu = '{$hoy}',
						 lenhou = '{$hora}'
				   WHERE lenhis = {$whis}
				     AND lening = '{$wing}'";
		//$rs = mysql_query( $query, $conex );

		if( $rs = mysql_query( $query, $conex ) )
		{
			$registroAfectado = mysql_affected_rows();
			$accion 	    = "UPDATE";
			$descripcion    = "envio de documento a el rol {$nivelSiguiente} ";
			$identificacion = "{$whis}-{$wing}";
			insertLog( $conex, $wfachos, $usuario, $accion, "000011", $err, $descripcion, $identificacion, $sql_error = '', '', '', '', '' );
		}
		//----------------------------------------------------------------------------INSERCIÓN INICIAL EN TABLA DE MOVIMIENTO---------------------------------------------------------------------//
		$query = "INSERT
				    INTO {$wfachos}_000020 (Medico, Fecha_data, Hora_data, lmohis, lmoing, lmotip, lmoori, lmodes, lmofot, lmoest, seguridad)
				  VALUES ('fachos','{$hoy}','{$hora}','{$whis}','{$wing}', '{$waccion}' ,'{$nivelListado}','{$nivelSiguiente}', '{$foto}','on','{$usuario}')";
		$rs = mysql_query( $query, $conex );
		$nivelListado = $nivelSiguiente;
	}
	if( $waccion == "d" ) //if( $waccion == "d" and $nivelMinimo == "off" )
	{
		$query = "UPDATE {$wfachos}_000011
					 SET lenrac = '{$nivelSiguiente}',
						 lenran = '{$nivelListado}',
						 lenfeu = '{$hoy}',
						 lenhou = '{$hora}'
				   WHERE lenhis = {$whis}
				     AND lening = '{$wing}'";
		//$rs = mysql_query( $query, $conex );

		if( $rs = mysql_query( $query, $conex ) )
		{
			$registroAfectado = mysql_affected_rows();
			$accion 	    = "UPDATE";
			$descripcion    = "devolucion de documento a el rol {$nivelSiguiente } ";
			$identificacion = "{$whis}-{$wing}";
			insertLog( $conex, $wfachos, $usuario, $accion, "000011", $err, $descripcion, $identificacion, $sql_error = '', '', '', '', '' );
		}
		//----------------------------------------------------------------------------INSERCIÓN INICIAL EN TABLA DE MOVIMIENTO---------------------------------------------------------------------//
		$query = "INSERT
				    INTO {$wfachos}_000020 (Medico, Fecha_data, Hora_data, lmohis, lmoing, lmotip, lmoori, lmodes, lmofot, lmoest, seguridad)
				  VALUES ('fachos','{$hoy}','{$hora}','{$whis}','{$wing}', '{$waccion}' ,'{$nivelListado}','{$nivelSiguiente}', '{$foto}', 'on','{$usuario}')";
		$rs = mysql_query( $query, $conex );
		$nivelListado = $nivelAnterior;
	}

	if( $registroAfectado > 0 )
	{
		$error = 0;
		$query = " SELECT roldes
					 FROM {$wfachos}_000015
					WHERE rolcod = {$nivelListado}";
		$rs  = mysql_query( $query, $conex );
		$row = mysql_fetch_array( $rs );

		$tabla  = "<div class='fila1' align='center'><table width=100%>";
		$tabla .= "<tr><td align='center'><span class='subtituloPagina2'>LISTADO ENVIADO A: {$row[0]}</span></td></tr>";
		$tabla .= "<br><br>";
		$tabla .= "</table></div>";
	}
	$data = array( "respuesta"=> $tabla, "error"=>$error, "nuevoNivel"=>$nivelListado );
	echo json_encode($data);

	return;
}

/**
	RECIBE UNA OBSERVACION GENERAL DEL LISTADO Y LA ALMACENA EN LA BASE DE DATOS
**/
if( $peticionAjax == "actualizarObservacionGeneral")
{
	$query = "UPDATE {$wfachos}_000011
				 SET lenobs = '{$observacion}'
			   WHERE lenhis = '{$whis}'
			     AND lening = '{$wing}'";
	//$rs = mysql_query( $query, $conex );
	if( $rs = mysql_query( $query, $conex ) )
		{
			$registroAfectado = mysql_affected_rows();
			$accion 	    = "UPDATE";
			$descripcion    = "Actualizacion de observacion de documento ";
			$identificacion = "{$whis}-{$wing}";
			insertLog( $conex, $wfachos, $usuario, $accion, "000011", $err, $descripcion, $identificacion, $sql_error = '', '', '', '', '' );
		}
	return;
}

/**
	ACTUALIZA EL ESTADO DE COMPLETO O INCOMPLETO DE UN LISTADO, VALIDANDO SIEMPRE QUE SEA EL USUARIO INDICADO
**/
if( $peticionAjax == "cambiarEstadoDelListado" )
{
	$error = 0;

	if( $accion == "on" )
	{
		$query = "SELECT COUNT(*) soportesFaltantes
				    FROM {$wfachos}_000012
				   WHERE delhis = '{$whis}'
					 AND deling = '{$wing}'
					 AND delest = 'n'";
		   $rs  = mysql_query( $query, $conex );
		   $row = mysql_fetch_array( $rs );
		   $soportesFaltantes  = $row['soportesFaltantes']*1;
		$fechaHoraSet = ", lenfec = '{$hoy}'
						 , lenhor = '{$hora}'";
	}else
		{
			$fechaHoraSet = ", lenfec = '0000-00-00'
						     , lenhor = '00:00:00'";
		}
	if( $soportesFaltantes == 0 )
	{
		$query = "UPDATE {$wfachos}_000011
					 SET lencer = '{$accion}'
						{$fechaHoraSet}
				   WHERE lenhis = '{$whis}'
					 AND lening = '{$wing}'";
		//$rs = mysql_query( $query, $conex );
		if( $rs = mysql_query( $query, $conex ) )
		{
			$registroAfectado = mysql_affected_rows();
			$accion 	    = "UPDATE";
			$descripcion    = "El listado cambio su estado a {$accion} en cerrado ";
			$identificacion = "{$whis}-{$wing}";
			insertLog( $conex, $wfachos, $usuario, $accion, "000011", $err, $descripcion, $identificacion, $sql_error = '','', '', '', '' );
		}
	}else
		{
			$error = 1;
		}
	$data = array( "error"=> $error );
	echo json_encode( $data );
	return;
}

/**
**/
if( $peticionAjax == "actualizarResponsableSoporteListado" )
{
	$query = " UPDATE {$wfachos}_000012
				  SET delres = '{$wresponsable}'
				WHERE delhis = '{$whis}'
				  AND deling = '{$wing}'
				  AND delsop = '{$wsoporte}'
				  AND delemp = '{$wempPlan}'
				  AND delser = '{$wservicio}'";
	//$rs = mysql_query( $query, $conex );
	if( $rs = mysql_query( $query, $conex ) )
	{
		$registroAfectado = mysql_affected_rows();
		$accion 	    = "UPDATE";
		$descripcion    = "cambio el responsable del soporte {$wsoporte} a {$wresponsable} ";
		$identificacion = "{$whis}-{$wing}";
		//insertLog( $conex, $wfachos, $usuario, $accion, "000011", $err, $descripcion, $identificacion, $sql_error = '' );
	}
	return;
}

/**
**/
if( $peticionAjax == "verSoportesPendientesEnviados" )
{
	$error 		   = 0;
	$datosPaciente = datosPaciente( $whis, $wing, $wnom, $wtipoDocu, $wcedula, $wfechaIngreso, $whabitacionActual );

	/*SI SE ENCONTRARON DATOS DE LA HISTORIA Y EL INGRESO*/
	$listadoActual = generarListadoDePendientes( $whis, $wing, $nivelUsuario );
	if($datosPaciente != "" )
	{
		$html .= "<table id='tb_maqueta' style='width:70%;'>";
			$html .= "<tr><td style='vertical-align:top; border: 1px solid; border-color: #2A5DB0;'>{$datosPaciente}</td></tr>";
			$html .= "<tr><td><br><br><div class='fila2' align='center' id='div_listadoActual' style='width:100%; height:100%; border: 1px solid; border-color:#2A5DB0;'>".$listadoActual."</div></td></tr>";
		$html .= "</table>";
	}

	$data = array( "maqueta"=>$html, "error"=>$error );
	echo json_encode( $data );
	return;
}

/**
**/
if( $peticionAjax == "verFoto" )
{
	$query = " SELECT lmohis, lmoing, lmofot, fecha_data, hora_data
				 FROM {$wfachos}_000020
				WHERE lmohis = '{$whis}'
				  AND lmoing = '{$wing}'
				  AND lmoori = '{$nivelUsuario}'
				  AND seguridad = '{$usuario}'
				GROUP BY lmohis, lmoing
			   HAVING MAX(fecha_data) AND MAX(hora_data)";
	$rs   = mysql_query( $query, $conex );
	$row  =  mysql_fetch_array( $rs );
	$foto = $row[2];
	$data = array( "foto"=>$foto );
	echo json_encode($data);
	return;
}

/**
**/
if( $peticionAjax == "cambiarProcesoListado" )
{
	$query = " SELECT dprori origen
				 FROM {$wfachos}_000018
				WHERE dprcod = '{$numProceso}'
				  AND dprini = 'on'";
	$rs    = mysql_query( $query, $conex );
	$row   = mysql_fetch_array( $rs );
	$origen = $row['origen'];
	$query = "UPDATE {$wfachos}_000011
			     SET lenpro = '{$numProceso}',
				     lenran = '',
					 lenrac = '{$origen}'
			   WHERE lenhis = '{$whis}'
			     AND lening = '{$wing}'
				 AND lencer = 'off'";
	$rs    = mysql_query( $query, $conex );
	//echo $query."\n";
	return;

}
?>
<html>
<header>
	<title>MONITOR DE SOPORTES PARA FACTURACION</title>
</header>

<style>
	.botona{
			font-size:13px;
			font-family:Verdana,Helvetica;
			font-weight:bold;
			color:white;
			background:#638cb5;
			border:0px;
			width:180px;
			height:30px;
			margin-left: 1%;
			cursor: pointer;
		 }
	.botona2{
			font-size:10px;
			font-family:Verdana,Helvetica;
			font-weight:bold;
			color:white;
			background:#638cb5;
			border:0px;
			width:100px;
			height:30px;
			margin-left: 1%;
		 }
	#tooltip{
			color: #2A5DB0;
			font-family: Arial,Helvetica,sans-serif;
			position:absolute;
			z-index:3000;
			border:1px solid #2A5DB0;
			background-color:#C3D9FF;
			padding:5px;
			opacity:1;}
	#tooltip div{margin:0; width:200px; class:fila1}

   .botonAdd{
		background-image:url(../../images/medical/ips/plus.gif);
		background-repeat:no-repeat;
		height:20px;
		width:20px;
		background-position:center;
    }
</style>
<!-- <script type='text/javascript' src='../../../include/root/jquery-1.3.2.js'></script> -->
<script type='text/javascript' src='../../../include/root/jquery-1.3.2.js'></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
<link rel="stylesheet" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" />
<script type='text/javascript' src='../../../include/root/jquery.blockUI.min.js'></script>
<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>
<script>

	var wemp_pmla;
	var wbd;
	var wfachos;
	var listado;
	var usuario;
	var proceso
	var nivelUsuario;
	var nivelMinimo;
	var nivelMaximo;
	var nivelSiguiente;
	var puedeCerrar;
	var esAdmin;
	var txtNuevaObservacion;
	var nombreUsuario;
	var titulos;
	var wtema;
	var identificadorTooltip = 0;

	$(document).ready(function(){
		wemp_pmla 	   = $("#wemp_pmla").val();
		wbd 	  	   = $("#wbasedato").val();
		proceso   	   = $("#wproceso").val();
		wfachos  	   = $("#wfachos").val();
		listado   	   = $("#div_respuestas");
		usuario   	   = $("#wusuario").val();
		nivelUsuario   = $("#wNivelUsuario").val();
		nombreUsuario  = $("#wNombreUsuario").val();
		nivelMinimo    = $("#nivelMinimo").val();
		nivelMaximo    = $("#nivelMaximo").val();
		nivelSiguiente = $("#nivelSiguiente").val();
		puedeCerrar    = $("#puedeCerrar").val();
		esAdmin 	   = $("#esAdmin").val();
		wtema 	  	   = $("#wtema").val();
		txtNuevaObservacion = $("textarea[name='texarea_nuevaObservacion']");
		$("#btn_consultar").click(buscarPacientes());

		titulos = new Array();
		titulos[0] = "";     //TITULO PARA LA PARTE EN QUE SE PRESENTAN LOS LISTADOS PENDIENTES
		//titulos[0] = "<span class='subtituloPagina2'><b>PACIENTES CON REVISIÓN DE SOPORTES PENDIENTES</b></span>";     //TITULO PARA LA PARTE EN QUE SE PRESENTAN LOS LISTADOS PENDIENTES
		titulos[1] = "<span class='subtituloPagina2'><b>VERIFICACIÓN DE SOPORTES EN LISTA DE CHEQUEO</b></span>";			   //TITULO PARA LA PARTE EN QUE SE MUESTRA EL ESTADO DE UN LISTADO( FUNCIONALIDAD PRINCIPAL )
		titulos[2] = "<span class='subtituloPagina2'><b>HISTORICO DE LISTA DE CHEQUEO ENVIADA</b></span>"; 			           //TITULO PARA LA PARTE EN QUE SE MUESTRA EL REGISTRO DE ENVIO DE UN LISTADO.
		titulos[3] = "<span class='subtituloPagina2'><b>LISTA DE SOPORTES PENDIENTES EN LISTA DE CHEQUEO ENVIADA</b></span>"; //TITULO PARA LA PARTE EN QUE SE MUESTRA EL lISTADO DE SOPORTES QUE SE QUEDARON DEBIENDO EN UN LISTADO.

		$("#div_titulos").html( titulos[0] );
	});

	function validar(e) {
	   var esIE = (document.all);
	   var esNS = (document.layers);
	   var tecla = (esIE) ? event.keyCode : e.which;
	   if ( tecla ==13 ){
		return true;
	   }
	   else return false;
	}

	/*BUSCA LOS PACIENTES ACTIVOS EN CUALQUIER CENTRO DE COSTOS, O EL PACIENTE QUE CORRESPONDA AL BUSCADO*/
	function buscarPacientes()
	{
		centroCostos 	  = $("#input_cco").val();
		historiaIngresada = $("#whis_ingresada").val();
		// $( ".viewport-bottom" ).hide();

		$("#div_menuPacientes").html("<br><br><br><div align=center><img id='img_esperar_plan' src='../../images/medical/ajax-loader7.gif' name='mg_esperar_plan'></div>");
		$.ajax({
			    url: "MonitorSoportesFacturacion.php",
				type: "POST",
				data: {
					 peticionAjax: "buscarPacientes",
						wbasedato: wbd,
						  wfachos: wfachos,
						wemp_pmla: wemp_pmla,
						  proceso: proceso,
							 wcco: centroCostos,
						  usuario: usuario,
						  esAdmin: esAdmin,
					 nivelUsuario: nivelUsuario,
					  nivelMaximo: nivelMaximo,
					  puedeCerrar: puedeCerrar,
					  nivelMinimo: nivelMinimo,
							 whis: historiaIngresada
					  },
				success: function(data)
				{
					$("#div_menuPacientes").html(data.pacientes);
				},
				dataType: "json"
		});
		return;
	}

	function verSoportes( historia, ingreso, nombre, tipoDocu, cedula, fechaIngreso, habitacionActual, nuevo )//recopila los parámetros y los asigna a variables que serán parámetros para generar los soportes
	{
		$("#input_whis").val(historia);
		$("#input_wing").val(ingreso);
		$("#input_nombre").val(nombre);
		$("#input_tipoDocu").val(tipoDocu);
		$("#input_cedula").val(cedula);
		$("#input_fechaIngreso").val(fechaIngreso);
		$("#input_habitacionActual").val(habitacionActual);
		if( nuevo == "si")
		{
			$("#funcionMenuProcesos").val( "create" );
			$.blockUI({
						message: $("#div_menuProcesos"),
						css: { left: '25%',
								top: '20%',
							  width: '30%',
							 height: '40%'
							 }
					  });
		}else
			{
				generarSoportesPaciente();
			}
	}

	function generarListadoActual() // funcion que genera el listado de soportes diligenciados o a diligenciar segun los servicios utilizados por el paciente.
	{
		whistoria = $("#input_whis").val();
		wingreso = $("#input_wing").val();
		$("#div_listadoActual").html( "" );
		$.ajax({
			    url: "MonitorSoportesFacturacion.php",
				type: "POST",
				async: false,
				data: {
					 peticionAjax: "generarListadoActual",
						wbasedato: wbd,
						  wfachos: wfachos,
						wemp_pmla: wemp_pmla,
						  proceso: proceso,
							 whis: whistoria,
					 nivelUsuario: nivelUsuario,
					  nivelMinimo: nivelMinimo,
					  nivelMaximo: nivelMaximo,
				   nivelSiguiente: nivelSiguiente,
						  esAdmin: esAdmin,
					  puedeCerrar: puedeCerrar,
							 wing: wingreso,
						idTooltip: identificadorTooltip,
						    wtema: wtema
					  },
				success: function(data)
				{
					if(data.error == 0)
					{
						$("#div_listadoActual").empty();
						$("#div_listadoActual").html(data.lista);
						$(".msg_tooltip"+identificadorTooltip).tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50});
						$(".msg_tooltip").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50});
						identificadorTooltip ++;
						if( data.cerrado == "on" )
						{
							divClone  = $("#div_listadoActual");
							foto 	  = jQuery(divClone);
							foto.find( "input[type=radio]" ).attr( "disabled", "disabled" );
						}

					}else{
						console.log(data.error+" error");
					}

				},
				dataType: "json"
		});

	}

	function generarSoportesPaciente()// verifica los datos del paciente, arma la maqueta y llama a la función generar listadoActual.
	{
		whistoria = $("#input_whis").val();
		wingreso  = $("#input_wing").val();
		nombre    = $("#input_nombre").val();
		tipoDocu  = $("#input_tipoDocu").val();
		cedula    = $("#input_cedula").val();
		numProceso       = $("#codigoProceso").val();
		habitacionActual = $("#input_habitacionActual").val();
		fechaIngreso 	 = $("#input_fechaIngreso").val();

		if( $.trim(wingreso) == "" || $.trim(whistoria) == "" )
		{
			alert("Parámetros de consulta incompletos");
			return;
		}
		esperar();
		$.ajax({
			    url: "MonitorSoportesFacturacion.php",
			   type: "POST",
			   data: {
						 peticionAjax: "verificarDatosIniciales",
							wbasedato: wbd,
							  wfachos: wfachos,
							wemp_pmla: wemp_pmla,
							  proceso: proceso,
								 whis: whistoria,
								 wing: wingreso,
							   nombre: nombre,
							 tipoDocu: tipoDocu,
							   cedula: cedula,
							  usuario: usuario,
						 nivelUsuario: nivelUsuario,
							  esAdmin: esAdmin,
						 fechaIngreso: fechaIngreso,
					 habitacionActual: habitacionActual,
						   numProceso: numProceso,
						        wtema: wtema
					  },
			success: function(data)
				{
					if(data.error == 0)
					{
						listado.html(data.maqueta);
						activarIngresoNoResponsables(data.entidadesNoResponsables);
						generarListadoActual();
						listado.show();
						$("#div_menuPpal").hide();
						$("#div_menuPacientes").hide();
						$('[name="div_retornar"]').show();
						$("#div_titulos").html( titulos[1] );
						$("#codigoProceso").val( "" );
						$.unblockUI();

					}else{
						console.log(data.error);
					}

				},
				dataType: "json"
		});

	}

	function abrirPanelEntidades() // abre el formulario para adicionar una entidad responsable adicional
	{
		div = "div_noResponsables";
		$.blockUI({
							message: $("#"+div),
							css: { left: '15%',
									top: '15%',
								  width: '50%',
						    	 height: '30%'
								 }
					  });
	}

	function activarIngresoNoResponsables( entidadesNoResponsables ) //permite armar el autocompletar de las entidades a agregar con aquellas que no están matriculadas como responsables
	{
		entidades_noResponsables_array = new Array();
		var datosEntidadesNoResponsables = eval( entidadesNoResponsables );
		for( i in datosEntidadesNoResponsables ){
			entidades_noResponsables_array.push( datosEntidadesNoResponsables[i] );
		}
		$( "#inp_nuevoResponsable" ).autocomplete({
					source: entidades_noResponsables_array, minLength : 3
		 });
		$('.ui-corner-all').css('fontSize', '11px');
	 }

	function agregarResponsable()//agrega una entidad responsable al listado del paciente
	{
		nResponsable = $("#inp_nuevoResponsable").val();
		nResponsable = nResponsable.split(",");
		codigoResponsable = $.trim(nResponsable[0]);
		nombreResponsable = $.trim(nResponsable[1]);
		whistoria = $("#input_whis").val();
		wingreso  = $("#input_wing").val();

		if( codigoResponsable != "" && nombreResponsable != "" )
		{
			$.ajax({
					url: "MonitorSoportesFacturacion.php",
				   type: "POST",
				   data: {
						 peticionAjax: "agregarResponsable",
							wbasedato: wbd,
							  wfachos: wfachos,
							wemp_pmla: wemp_pmla,
							  proceso: proceso,
						   	  wcodigo: codigoResponsable,
							  wnombre: nombreResponsable,
								 whis: whistoria,
								 wing: wingreso,
							  usuario: usuario,
							  esAdmin: esAdmin,
								 wing: wingreso
						  },
				success: function(data)
					{
						if(data.error == 0)
						{
							$("#div_responsables").html( data.tr );
							$("#inp_nuevoResponsable").val("");
							activarIngresoNoResponsables(data.noResponsables);

						}else{
							console.log(data.error);
						}

					},
					dataType: "json"
			});
		}
	}

	function verPlanes( codigo )// abre el formulario donde se eligen los planes asociados a una empresa
	{
		div = "div_pln_"+codigo;
		$.blockUI({
							message: $("#"+div),
							css: { left: '25%',
									top: '20%',
								  width: '30%',
						    	 height: '50%'
								 }
					  });
	}

	function agregarPlanEmpresa( codigo ) // arma un string  que contiene los codigos empresa_plan, que será enviado via ajax para asociarlos a una lista de soportes
	{
		empresas_planes = "";
		j = 0;
		$("#tbl_planes_"+codigo+" input[type=checkbox]").each(function(){
			if($(this).attr("nuevo")=="si")
			{
				if( $(this).is(":checked") )
				{
					id = $(this).attr("id");
					id = id.split("_");
					id = $.trim(id[1]);
					j++;
					if(j==1)
					{
						insertar = true;
						empresas_planes = id+"_add";
					}else
						{
							empresas_planes = empresas_planes+","+id+"_add";
						}
					$(this).attr("nuevo","no");
				}
			 }

			 if( $(this).attr("nuevo")=="no" )
			 {
				if( !$(this).is(":checked") )
				{
					id = $(this).attr("id");
					id = id.split("_");
					id = $.trim(id[1]);
					j++;
					if(j==1)
					{
						insertar = true;
						empresas_planes = id+"_rm";
					}else
						{
							empresas_planes = empresas_planes+","+id+"_rm";
						}
					$(this).attr("nuevo","si");
				}
			 }


		});
		if(empresas_planes != "")
		{
			insertarNuevosSoportesEnLista( empresas_planes );
		}
		$.unblockUI();
	}

	function insertarNuevosSoportesEnLista( empresas_planes ) //agrega los codigos empresa_plan seleccionados en el encabezado de la lista
	{
		$.ajax({

				 url: "MonitorSoportesFacturacion.php",
				type: "POST",
			   async: false,
				data: {
					 peticionAjax: "agregarPlanEmpresa",
						wbasedato: wbd,
						  wfachos: wfachos,
						wemp_pmla: wemp_pmla,
						  proceso: proceso,
							 whis: whistoria,
							 wing: wingreso,
				 wempresas_planes: empresas_planes,
							 wing: wingreso
					  },
			 success: function(data)
				{
					if(data.error == 0)
					{
						generarSoportesPaciente();

					}else{
						console.log(data.error);
					}

				},
				dataType: "json"
		});
	}

	function actualizarEstadoSoporte( empresaPlan, soporte, select, servicio ) //actualiza el estado de un soporte recibido, no recibido, no aplica, y realiza los movimientos en la base datos correspondiente
	{
		valor     = $(select).val();
		whistoria = $("#input_whis").val();
		wingreso  = $("#input_wing").val();
		wformato  = $("#formato_"+empresaPlan+"_"+soporte+"_"+servicio).val();
		$.ajax({
				 url: "MonitorSoportesFacturacion.php",
				type: "POST",
				data: {
					 peticionAjax: "actualizarSoporteListado",
						wbasedato: wbd,
						  wfachos: wfachos,
						wemp_pmla: wemp_pmla,
						  proceso: proceso,
						 wempPlan: empresaPlan,
						  usuario: usuario,
						  soporte: soporte,
						  westado: valor,
						 servicio: servicio,
						     whis: whistoria,
						     wing: wingreso,
					     wformato: wformato
					  },
			 success: function(data)
				{
					if(( valor=="n" || valor=="na"))
					{
						$(".formato_"+empresaPlan+"_"+soporte+"_"+servicio).attr("disabled", true);
						$(".formato_"+empresaPlan+"_"+soporte+"_"+servicio).attr("checked", "");
					}
					if(( valor =="s" || valor=="na" )  )
					{
						if( valor == "s")
						{
							$(".formato_"+empresaPlan+"_"+soporte+"_"+servicio).removeAttr('disabled');
							formatoDefecto = $("#formato_"+empresaPlan+"_"+soporte+"_"+servicio).val();
							setTimeout(function(){
									$(".formato_"+empresaPlan+"_"+soporte+"_"+servicio).each(function(){
									if( $(this).attr( "value" ) == formatoDefecto )
									{
										$(this).click();
									}
								});}, 300);
						}
					}

				}
			});
	}

	function actualizarFormatoSoporte( empresaPlan, soporte, select, servicio ) //actualiza el formato de un soporte recibido y realiza los movimientos en la base datos correspondiente
	{
		valor 	  = $(select).val();
		whistoria = $("#input_whis").val();
		wingreso  = $("#input_wing").val();
		$.ajax({

				 url: "MonitorSoportesFacturacion.php",
				type: "POST",
				data: {
					peticionAjax: "actualizarFormatoSoporteListado",
					   wbasedato: wbd,
					     wfachos: wfachos,
					   wemp_pmla: wemp_pmla,
					     proceso: proceso,
					    wempPlan: empresaPlan,
					     soporte: soporte,
						    whis: whistoria,
							wing: wingreso,
						 usuario: usuario,
					   wservicio: servicio,
						wformato: valor
					  },
		    success: function(data)
				{
					if(data.error == 0)
					{

					}else{
						console.log(data.error);
					}

				},
				dataType: "json"
			});
	}

	function quitarResponsable(responsable) //quita una entidad del listado de responsables asociados a la lista
	{
		if(!(confirm("¿ESTÁ SEGURO QUE DESEA QUITAR ESTE RESPONSABLE?")))
		{
			return;
		}
		whistoria = $("#input_whis").val();
		wingreso = $("#input_wing").val();
		$.ajax({

				 url: "MonitorSoportesFacturacion.php",
				type: "POST",
			   async: false,
				data: {
					   peticionAjax: "eliminarResponsable",
						  wbasedato: wbd,
						    wfachos: wfachos,
						  wemp_pmla: wemp_pmla,
						    proceso: proceso,
						   wempresa: responsable,
						    esAdmin: esAdmin,
						       whis: whistoria,
						       wing: wingreso,
						    usuario: usuario
					  },
				success: function(data)
				{
					$("#div_responsables").html( data.tr );
					activarIngresoNoResponsables( data.noResponsables );

				},
				dataType: "json"
			});
	}

	function guardarObservacion( soporte, empresaPlan, servicio, textArea )
	{
		value = $(textArea).val();
		if( $.trim( value ) != "" )
		{
			wexiste   = $("#existe_"+empresaPlan+"_"+soporte+"_"+servicio).val();
			westado   = $("#estado_"+empresaPlan+"_"+soporte+"_"+servicio).val();
			whistoria = $("#input_whis").val();
			wingreso  = $("#input_wing").val();
			$.ajax({
					 url: "MonitorSoportesFacturacion.php",
					type: "POST",
					data: {
						 peticionAjax: "guardarObservacion",
							wbasedato: wbd,
							  wfachos: wfachos,
							wemp_pmla: wemp_pmla,
							  proceso: proceso,
							 wsoporte: soporte,
							 wempPlan: empresaPlan,
							  wexiste: wexiste,
						 wobservacion: value,
							  usuario: usuario,
					    nombreUsuario: nombreUsuario,
								 whis: whistoria,
								 wing: wingreso,
							  westado: westado,
							wservicio: servicio
						  },
				 success: function( data )
						{
							textoActual 	   = $("#txar_"+empresaPlan+"_"+soporte+"_"+servicio).html();
							datosReferenciales =  value+"<br><b style='font-size:6pt'>"+nombreUsuario+" "+data.fecha+" "+data.hora+"</b><br>-----------------------------<br>";
							$("#txar_"+empresaPlan+"_"+soporte+"_"+servicio).html( datosReferenciales + textoActual )
						},
				 dataType: "json"
				});
		}
	}

	function retornar()
	{
		$("#div_menuPpal").show();
		$("#div_menuPacientes").html("");
		buscarPacientes()
		$("#div_menuPacientes").show();
		listado.hide();
		$('[name|="div_retornar"]').hide();
		$("#div_verFoto").hide();
		$("#div_titulos").html( titulos[0] );
	}

	function elegirServiciosEmpresaPlan( codigo ) //los servicios asociados a un codigo de empresa_plan
	{
		div = "div_servicios_"+codigo;
		$.blockUI({
							message: $("#"+div),
							css: { left: '30%',
									top: '20%',
								  width: '30%',
						    	 height: '50%'
								 }
					  });
	}

	function cambiarEstadoServicio( checkbox )
	{
		estadoAuxiliar = $(checkbox).attr("estadoActual");
		if( estadoAuxiliar == "n" )
			$(checkbox).attr("estadoActual","s");
			else
				$(checkbox).attr("estadoActual","n");
	}

	function mostrarOcultar( id, controlador )
	{
		if( $(controlador).attr("mostrando") == "s" )
		{
			$("#"+id).hide();
			$(controlador).attr("mostrando","n");
		}else
			{
				$("#"+id).show();
				$(controlador).attr("mostrando","s");
			}
	}

	function esperar()
	{
		$.blockUI({ message: $('#msjEspere')});
	}

	function devolverEnviarListado( accion, nivel, boton )
	{
		if( ! $.browser.msie )
		{
			$( ".msg_tooltip"+identificadorTooltip ).tooltip( "close" );
			setTimeout( function(){
				devolverEnviarListado2( accion, nivel, boton );
			},1000);
		}else{
			devolverEnviarListado2( accion, nivel, boton );
		}
	}

	function devolverEnviarListado2( accion, nivel, boton, e )
	{
		whistoria = $("#input_whis").val();
		wingreso  = $("#input_wing").val();

		if( ! $.browser.msie )
		{
			generarListadoActual();
		}

		divClone  = $("#div_listadoActual").clone( true ).attr("id", "clone");
		foto 	  = jQuery(divClone);
		foto.find( "input[type=radio]" ).attr( "disabled", "disabled" );
		foto.find( "input[type=button]" ).attr( "disabled", "disabled" );
		foto.find( "textarea" ).attr( "disabled", "disabled" );
		foto.find( "select" ).attr( "disabled", "disabled" );
		foto.find( "div" ).show();
		fotoListado = foto.html();

		$.ajax({
				 url: "MonitorSoportesFacturacion.php",
				type: "POST",
				data: {
					 peticionAjax: "cambiarNivelListado",
						wbasedato: wbd,
						  wfachos: wfachos,
						wemp_pmla: wemp_pmla,
						  proceso: proceso,
							 whis: whistoria,
							 wing: wingreso,
					  nivelMaximo: nivelMaximo,
				   nivelSiguiente: nivel,
					 nivelListado: $("#input_nivelListado").val(),
					nivelAnterior: $("#input_nivelAnterior").val(),
					  nivelMinimo: nivelMinimo,
					 nivelUsuario: nivelUsuario,
						  usuario: usuario,
							 foto: fotoListado,
						  waccion: accion
					  },
			  success: function(data)
					{
						if(data.error == "0")
						{
							$("#div_respuestas").html( data.respuesta );
							$("#div_nivelListado").val(data.nuevoNivel);
						}else{
								console.log(" fallo insercion ");
							 }
					},
				dataType: "json"

			});
	}

	function guardarObservacionGeneral( textarea )
	{
		observacion = $(textarea).val();
		whistoria   = $("#input_whis").val();
		wingreso    = $("#input_wing").val();
		$.ajax({
				 url: "MonitorSoportesFacturacion.php",
				type: "POST",
				data: {
					 peticionAjax: "actualizarObservacionGeneral",
						wbasedato: wbd,
						  wfachos: wfachos,
							 whis: whistoria,
							 wing: wingreso,
						wemp_pmla: wemp_pmla,
						  proceso: proceso,
					  observacion: observacion
					  }
			});
	}

	function cambiarEstadoDelListado( chk )
	{
		accion = "";
		whistoria = $("#input_whis").val();
		wingreso = $("#input_wing").val();
		if($(chk).is(":checked"))
		{
			accion = "on";
		}else
			{
				accion = "off";
			}

		$.ajax({
				url: "MonitorSoportesFacturacion.php",
				type: "POST",
				data: {
					 peticionAjax: "cambiarEstadoDelListado",
						wbasedato: wbd,
						  wfachos: wfachos,
						wemp_pmla: wemp_pmla,
						  proceso: proceso,
						     whis: whistoria,
						     wing: wingreso,
						   accion: accion
					  },
				success: function(data)
					{
						if($(chk).is(":checked"))
						{
							if( data.error == 0 )
							{
								accion = "on";
								$("#td_estado").html("<b>Completado</b>");
								divClone  = $("#div_listadoActual");
								foto 	  = jQuery(divClone);
								foto.find( "input[type=radio]" ).attr( "disabled", "disabled" );
								foto.find( "input[type=button]" ).attr( "disabled", "disabled" );
								/*foto.find( "input[type=button]" ).each(function(){
									console.log( $(this).value );
								});*/

							}else
								{
									$(chk).removeAttr("checked");
									divClone  = $("#div_listadoActual");
									foto 	  = jQuery(divClone);
									foto.find( "input[type=radio]" ).removeAttr( "disabled" );
									foto.find( "input[type=button]" ).removeAttr( "disabled" );
									/*foto.find( "input[type=button]" ).each(function(){
									console.log( $(this).value );
								});*/
									alert("El listado no puede cerrarse puesto que hay soportes pendientes");
								}

						}else
							{
								accion = "off";
								$("#td_estado").html("<b>En Proceso</b>");
								divClone  = $("#div_listadoActual");
								foto 	  = jQuery(divClone);
								foto.find( "input[type=radio]" ).removeAttr( "disabled" );
								foto.find( "input[type=button]" ).removeAttr( "disabled" );
							}
					},
				dataType: "json"
			});
	}

	function actualizarResponsableSoporte( empresaPlan, soporte, servicio, select )
	{
		valor     = $(select).val();
		wexiste   = $("#existe_"+empresaPlan+"_"+soporte+"_"+servicio).val();
		westado   = $("#estado_"+empresaPlan+"_"+soporte+"_"+servicio).val();
		whistoria = $("#input_whis").val();
		wingreso  = $("#input_wing").val();
		$.ajax({

				 url: "MonitorSoportesFacturacion.php",
				type: "POST",
				data: {
					peticionAjax: "actualizarResponsableSoporteListado",
					   wbasedato: wbd,
					     wfachos: wfachos,
					   wemp_pmla: wemp_pmla,
					     proceso: proceso,
					    wempPlan: empresaPlan,
					    wsoporte: soporte,
					     wexiste: wexiste,
							whis: whistoria,
							wing: wingreso,
					     westado: westado,
					   wservicio: servicio,
					wresponsable: valor,
						 usuario: usuario
					  },
			 success: function(data)
				{
					if(data.error == 0)
					{
						if( wexiste=="s" && $.trim(valor) =="sin" )
						{
							$("#existe_"+empresaPlan+"_"+soporte+"_"+servicio).val("n");
						}
						if( wexiste=="n" && ( $.trim(valor) !="" )  )
						{

							$("#existe_"+empresaPlan+"_"+soporte+"_"+servicio).val("s");
						}

					}else{
						console.log(data.error);
					}

				},
				dataType: "json"
			});
	}

	function ocultarMostrarOrigenPacientes( id, controlador )
	{
	  if( $(controlador).attr("mostrando") == "s" )
		{
			$("#"+id).hide();
			$(controlador).attr("mostrando","n");

		}else
			{
				$("#"+id).show();
				$(controlador).attr("mostrando","s");
			}
	}

	function verPendientes( historia, ingreso, nombre, tipoDocu, cedula, fechaIngreso, habitacionActual )
	{
		$.ajax({

				 url: "MonitorSoportesFacturacion.php",
				type: "POST",
				data: {
					peticionAjax: "verSoportesPendientesEnviados",
					   wbasedato: wbd,
					     wfachos: wfachos,
					   wemp_pmla: wemp_pmla,
					     proceso: proceso,
							whis: historia,
							wing: ingreso,
					        wnom: nombre,
					   wtipoDocu: tipoDocu,
					     wcedula: cedula,
				   wfechaIngreso: fechaIngreso,
			   whabitacionActual: habitacionActual,
			        nivelUsuario: nivelUsuario,
						 usuario: usuario
					  },
			 success: function(data)
				{
					if(data.error == 0)
					{
						$("#div_menuPpal").hide();
						$("#div_menuPacientes").hide();
						$('[name="div_retornar"]').show();
						listado.html(data.maqueta);
						listado.show();
						$("#div_titulos").html( titulos[3] );
					}else{
						console.log(data.error);
					}

				},
				dataType: "json"
			});
	}

	function cambiarTodos( clase, chk )
	{
		value = $(chk).val();
		$("."+clase).each(function(){
			if( $( this ).attr( "value" ) == value )
			{
				$(this).click();
			}
		});
	}

	function mostrarDivAgregarComentario( Soporte, empPlan, Servicio )
	{
		id = Soporte+"_"+empPlan+"_"+Servicio;
		txtNuevaObservacion.val( "" );
		txtNuevaObservacion.attr( "id", id );
		$("#btnAddComentario").attr( 'idtxt', id );

		div = "div_agregarComentario";
		$.blockUI({
							message: $("#"+div),
							css: { left: '25%',
									top: '20%',
								  width: '30%',
						    	 height: '20%'
								 }
					  });
	}

	function agregarComentario( btn )
	{
		id = $( btn ).attr( "idtxt" );
		aux = id.split( "_" );
		soporte	    = aux[0];
		empresaPlan = aux[1];
		servicio    = aux[2];

		guardarObservacion( soporte, empresaPlan, servicio, txtNuevaObservacion );

		txtNuevaObservacion.val( "" );
		txtNuevaObservacion.attr( "id", "-" );
		$("#btnAddComentario").attr( 'idtxt', "" );
		$.unblockUI();
	}

	function verFoto( historia, ingreso )
	{
		$.ajax({

				 url: "MonitorSoportesFacturacion.php",
				type: "POST",
				data: {
					peticionAjax: "verFoto",
					   wbasedato: wbd,
					     wfachos: wfachos,
					   wemp_pmla: wemp_pmla,
					     proceso: proceso,
							whis: historia,
							wing: ingreso,
			        nivelUsuario: nivelUsuario,
						 usuario: usuario
					  },
			 success: function(data)
				{
					$("#div_verFoto").show();
					$("#div_contenedorFoto").html( data.foto );
					$("#div_menuPpal").hide();
					$("#div_menuPacientes").hide();
					$("#div_titulos").html( titulos[2] );
				},
				dataType: "json"
			});
	}

	function elegirProceso( radio )
	{
		accion  = $("#funcionMenuProcesos").val();
		opcion  = jQuery( radio );
		proceso = opcion.val();
		$("#codigoProceso").val( proceso );
		if( accion == "create" )
		{
			setTimeout(function(){
				generarSoportesPaciente();
				}, 300);
		}else <!--es update -->
			{
				cambiarProcesoListado();
			}
		setTimeout(function(){
			opcion.removeAttr( "checked" );
			$("#codigoProceso").val("");
		}, 500);
	}

	function cambiarProceso( codigoProceso )
	{
		$("#funcionMenuProcesos").val( "update" );
		$("#codigoProceso").val( codigoProceso );
		radio = $("input[name='input_procesos'][value='"+codigoProceso+"']");
		radio.attr( "checked", "checked" );
		$.blockUI({
					message: $("#div_menuProcesos"),
					css: { left: '25%',
							top: '20%',
						  width: '30%',
						 height: '40%'
						 }
				  });
	}

	function cambiarProcesoListado()
	{
		whistoria = $("#input_whis").val();
		wingreso  = $("#input_wing").val();
		numProceso= $("#codigoProceso").val();

		if( $.trim(wingreso) == "" || $.trim(whistoria) == "" )
		{
			alert("Parámetros de consulta incompletos");
			return;
		}
		esperar();
		$.ajax({
			    url: "MonitorSoportesFacturacion.php",
			   type: "POST",
			   data: {
						 peticionAjax: "cambiarProcesoListado",
							wbasedato: wbd,
							  wfachos: wfachos,
							wemp_pmla: wemp_pmla,
								 whis: whistoria,
								 wing: wingreso,
							  usuario: usuario,
						 nivelUsuario: nivelUsuario,
							  esAdmin: esAdmin,
						   numProceso: numProceso,
						        wtema: wtema
					  },
			success: function(data)
				{
					$.unblockUI();
					$("#btn_retornar_1").click();
				}
		});
	}

</script>
<body>
<?php
include_once('root/comun.php');
$wactualiz = "2013-04-08";
encabezado("MONITOR DE SOPORTES PARA FACTURACI&OacuteN POR PACIENTE",$wactualiz, "clinica");

function verificarNivelDeUsuario( $codigoUsuario )
{
	global $conex, $wfachos;
	$query = "SELECT a.uscniv rol, b.rolcie, roladm admin, c.dprcod proceso
				FROM {$wfachos}_000013 a
					 INNER JOIN
					 {$wfachos}_000015 b on ( uscniv = rolcod AND a.uscusu = '{$codigoUsuario}' AND a.uscest='on' )
					 INNER JOIN
					 {$wfachos}_000018 c on ( c.dprori = a.uscniv or c.dprdes = a.uscniv )
			   GROUP BY rol, admin, proceso";
	$rs    = mysql_query( $query, $conex );
	$row   = mysql_fetch_array( $rs );
	$datos = array( 0 => $row[0], 1 => $row[1], 2 => $row[2], 3 => $row[3] );
	return( $datos );
}

function selectCentroCostos()
{
	global $wbasedato, $conex;
	$query = "SELECT Ccocod codigo, Cconom nombre
				FROM {$wbasedato}_000011
			   WHERE Ccoest = 'on'
			     AND (Ccohos = 'on' or Ccourg = 'on' or Ccocir = 'on' or Ccoayu = 'on')";
	$rs = mysql_query( $query, $conex );

	$select  = "<select id='input_cco'>";
	$select .= "<option value='%' selected> </option>";

	while( $row = mysql_fetch_array( $rs ) )
	{
		$select .= "<option value='{$row['codigo']}'>{$row['codigo']}, {$row['nombre']}</option>";
	}

	$select .= "</select>";
	return( $select );
}

function nivelesLimites( $nivelUsuario )
{
	global $wemp_pmla, $conex, $wfachos ;
	$niveles = array();
	$query   = " SELECT dprini minimo, dprdes siguiente
				   FROM {$wfachos}_000018
				  WHERE dprori = {$nivelUsuario}
					AND dprest = 'on'";
	$rs  = mysql_query( $query, $conex );
	$row = mysql_fetch_array( $rs );

	( trim($row['minimo']) == "" ) ? $row['minimo'] = "off" : $row['minimo'] = $row['minimo'] ;

	$niveles[0] = $row['minimo'];
	$niveles[2] = $row['siguiente'];

	$query   = " SELECT dprfin maximo
				   FROM {$wfachos}_000018
				  WHERE dprdes = {$nivelUsuario}";
	$rs  = mysql_query( $query, $conex );
	$row = mysql_fetch_array( $rs );
	( trim($row['maximo']) == "" ) ? $row['maximo'] = "off" : $row['maximo'] = $row['maximo'] ;
	$niveles[1] = $row['maximo'];


/*	$query = " SELECT dprdes siguiente
				 FROM {$wfachos}_000018
				WHERE dprori = '{$nivelUsuario}'
				  AND dprest = 'on'";
	$rs  = mysql_query( $query, $conex );
	$row = mysql_fetch_array( $rs );*/


	return( $niveles );

}

//PRESENTACION INICIAL Y DEFINICION DE CONTENEDORES (divs)
function datosUsuario()
{
	global $conex, $user_session, $nivelUsuario, $wemp_pmla, $wfachos, $nombreUsuario;

	$query = "SELECT descripcion
			    FROM usuarios
			   WHERE codigo = '{$user_session}'";
	$rs    = mysql_query( $query, $conex );
	$row   = mysql_fetch_array( $rs );
	$nombreUsuario = $row[0];
	$query = " SELECT roldes
				 FROM {$wfachos}_000015
				WHERE rolcod = '{$nivelUsuario}'";
	$rs = mysql_query( $query, $conex );
	$row = mysql_fetch_array( $rs );

	$cargo = $row[0];
	$tablaUsuario  = "";
	$tablaUsuario .= "<table width='40%'>";
	$tablaUsuario .= "<tr><td class='fila1' width='10%'>EMPLEADO: </td><td class='fila2' width='90%'  align='center' colspan=1>{$nombreUsuario}</td></tr>";
	$tablaUsuario .= "<tr><td class='fila1' width='10%'>PROCESO: </td><td class='fila2' width='60%' align='center'>{$cargo}</td></tr>";
	$tablaUsuario .= "</table>";

	return($tablaUsuario);
}

function menuPpal()
{
	$menu  = "<span class='subtituloPagina2'>Parámetros de consulta</span>";
	$menu .= "<br><br>";
	$menu .= "<table>";
		$menu .= "<tr><td class='fila1'>HISTORIA: </td><td class='fila2'><input type='text' id='whis_ingresada' size=40 value=''></td><tr>";
		$menu .= "<tr><td class='fila1'>CENTRO DE COSTOS: </td><td class='fila2'>".selectCentroCostos()."</td><tr>";
	$menu .= "</table>";
	//$menu .= "<input type='button' id='btn_consultar' class='botona' value='CONSULTAR' onclick='generarSoportesPaciente()'>";
	$menu .= "<input type='button' id='btn_consultar' class='botona' value='BUSCAR' onclick='buscarPacientes()'>";
	$menu .= "<input type='hidden' id='input_whis' value=''>";
	$menu .= "<input type='hidden' id='input_wing' value=''>";
	$menu .= "<input type='hidden' id='input_nombre' value=''>";
	$menu .= "<input type='hidden' id='input_tipoDocu' value=''>";
	$menu .= "<input type='hidden' id='input_cedula' value=''>";
	$menu .= "<input type='hidden' id='input_fechaIngreso' value=''>";
	$menu .= "<input type='hidden' id='input_habitacionActual' value=''>";
	$menu .= "<input type='hidden' name='codigoProceso' id='codigoProceso' value=''>";
	$menu .= "<input type='hidden' name='funcionMenuProcesos' id='funcionMenuProcesos' value=''>";
	return($menu);
}

function armarMenuProcesos()
{
	global $conex, $wfachos;
	$query = " SELECT procod codigo, pronom nombre, id
				 FROM {$wfachos}_000014
				WHERE proest = 'on'
				ORDER BY id";
	$rs	   = mysql_query( $query, $conex );

	$i = 0;
	$error = 1;
	if( mysql_num_rows( $rs ) > 0 )
	{
		$error = 0;
		$tabla = " <table width='80%'>";
			$tabla .= "<tr class='encabezadotabla' ><td colspan='3' align='center'>PROCESOS</td></tr>";
			$tabla .= "<tr class='encabezadotabla'><td>SELECCIONAR</td><td align='center'>CODIGO</td><td align='center'>NOMBRE</td></tr>";
			while( $row = mysql_fetch_array( $rs ) )
			{
				( is_int( $i/2) ) ?  $wclass = "fila1" : $wclass = "fila2";
				$i++;
				$tabla .= "<tr class='fila2'><td align='center'><input type='radio' name='input_procesos' value='{$row['codigo']}' onclick='elegirProceso( this );'></td><td>{$row['codigo']}</td><td>{$row['nombre']}</td></tr>";
			}

		$tabla .= "</table>";
		$tabla .= "<br>";
		$tabla .= "<input type='button' class='botona' value='CERRAR' onclick='$.unblockUI();'>";
	}else
		{
			 $tabla = "<br><span class='subtituloPagina2'>ELIJA EL PROCESO AL QUE PERTENECE EL LISTADO</span><br><br>";
		}
	return( $tabla );
}

function consultarTema( $tema, $conex, $wfachos )
{
	$query = "SELECT temcod codigo
			    FROM {$wfachos}_000017
			   WHERE temnom = '{$tema}'
				 AND temest = 'on'";
	$rs    = mysql_query( $query, $conex );
	$row   = mysql_fetch_array( $rs );
	return( $row['codigo'] );
}

$user_session   = explode('-',$_SESSION['user']);
$user_session   = $user_session[1];
$nombreUsuario  = "";
$proceso 		= "";
$wbasedato      = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
$wfachos        = consultarAliasPorAplicacion($conex, $wemp_pmla, "Facturacion hospitalaria");
$nivelUsuario   = verificarNivelDeUsuario( $user_session );
$puedeCerrar    = $nivelUsuario[1];
$esAdmin        = $nivelUsuario[2];
$proceso 	    = $nivelUsuario[3];
$nivelUsuario   = $nivelUsuario[0];
$nivelesLimites = nivelesLimites( $nivelUsuario );
$nivelMinimo    = $nivelesLimites[0];
$nivelMaximo    = $nivelesLimites[1];
$nivelSiguiente = $nivelesLimites[2];
$wtema 			= consultarTema( "facturacion", $conex, $wfachos );

//VARIABLES INCIALES
echo "<input type='hidden' name='wemp_pmla' id='wemp_pmla' value='{$wemp_pmla}'>";
echo "<input type='hidden' name='wbasedato' id='wbasedato' value='{$wbasedato}'>";
echo "<input type='hidden' name='wfachos' id='wfachos' value='{$wfachos}'>";
echo "<input type='hidden' name='wusuario' id='wusuario' value='{$user_session}'>";
echo "<input type='hidden' name='wNivelUsuario' id='wNivelUsuario' value='{$nivelUsuario}'>";
echo "<input type='hidden' name='nivelMinimo' id='nivelMinimo' value='{$nivelMinimo}'>";
echo "<input type='hidden' name='nivelMaximo' id='nivelMaximo' value='{$nivelMaximo}'>";
echo "<input type='hidden' name='puedeCerrar' id='puedeCerrar' value='{$puedeCerrar}'>";
echo "<input type='hidden' name='nivelSiguiente' id='nivelSiguiente' value='{$nivelSiguiente}'>";
echo "<input type='hidden' name='wproceso' id='wproceso' value='{$proceso}'>";
echo "<input type='hidden' name='wnombreProceso' id='wnombreProceso' value=''>";
echo "<input type='hidden' name='wtema' id='wtema' value='{$wtema}'>";
echo "<input type='hidden' name='esAdmin' id='esAdmin' value='{$esAdmin}'>";
echo "<input type='hidden' name='noResponsables' id='noResponsables' value=''>";
echo "<div id='div_datosUsuario' align='center'>".datosUsuario()."</div>";
echo "<input type='hidden' name='wNombreUsuario' id='wNombreUsuario' value='{$nombreUsuario}'>";
echo "<br><br>";
echo "<div id='div_menuPpal' align='center'>".menuPpal()."</div>";
echo "<br>";
echo "<div id='div_titulos' align='center' valign='center'></div>";
echo "<div id='div_menuPacientes' align='center'></div>";
echo "<div name='div_retornar' align='center' style='display:none;'><br><br><input type=button class='botona' name='btn_retornar' value='RETORNAR' onclick='retornar()'></div>";
echo "<br>";
echo "<div id='div_respuestas' align='center' class='div_contenedor' valing='top' style='display:none'></div>";
echo "<br>";
echo "<div name='div_retornar' align='center' style='display:none;'><br><br><input type=button class='botona' name='btn_retornar' id='btn_retornar_1' value='RETORNAR' onclick='retornar()'></div>";

//LISTADO DE NO RESPONSABLES
echo "<div id='div_noResponsables' class='fila2' align='center' style='cursor:default; display:none; repeat scroll 0 0; position:relative; width:98%; height:98%; overflow:auto;'>";
	echo "<span class='subtituloPagina2'>BUSCADOR DE ENTIDADES</span><br><br>";
	echo "<table>";
		echo "<tr>";
			echo "<td class='encabezadotabla'>ENTIDAD: &nbsp</td><td class='fila1'><input size=70 type='text' id='inp_nuevoResponsable' value=''></td><td align='center' colspan=2><input type='button' class='botona2' id='btn_agregarResponsable' value='AGREGAR' onclick='agregarResponsable()'></td>";
		echo "</tr>";
	echo "</table>";
	echo "<br><br>";
	echo "<td><input type='button' class='botona' id='btn_cerrarAddResponsable' value='CERRAR' onclick='$.unblockUI();'></td>";
echo "</div>";

echo "<div id='msjEspere' name='msjEspere' style='display:none;'>";
	echo "<br /><img src='../../images/medical/ajax-loader5.gif'/><br /><br />Por favor espere un momento ... <br /><br />";
echo "</div>";

echo "<div style='display:none;' id='div_verFoto'>";
	echo "<div align='center' valign='center' ><br><input type=button class='botona' value='RETORNAR' onclick='retornar()'><br></div>";
	echo "<br>";
	echo "<div id='div_contenedorFoto' class='fila2' style='border: 1px solid; border-color:#2A5DB0;''></div>";
echo "</div>";

echo "<div id='div_cerrar' align='center'>";
	echo "<br><br>";
	echo  "<table>";
		echo "<tr><td><input type='button' id='btn_cerrar' name='btn_generar' value='CERRAR' onclick='window.close();'></td></tr>";
	echo "</table>";
echo "</div>";

echo "<div id='div_agregarComentario' align='left' class='fila2' style='display:none;'>";
	echo "<span class='subtituloPagina2'>AGREGAR COMENTARIO</span><br><br>";
	echo "<div align='center'>";
		echo "<table>";
			echo "<tr class='encabezadotabla'><td align='center'>ESCRIBA EL COMENTARIO QUE DESEA AGREGAR</td></tr>";
			echo "<tr><td align='center'><textarea name='texarea_nuevaObservacion' style='width:400px; height=100px;'>asfafadsfaf</textarea></td></tr>";
			echo "<tr class='fila2'><td align='center'><input id='btnAddComentario' type='button' class='botona' idtxt='-' value='AGREGAR COMENTARIO' onclick='agregarComentario( this )'></td></tr>";
		echo "</table>";
	echo "</div>";
echo "</div>";

echo "<div id='div_menuProcesos' align='center' style='cursor:default; display:none; repeat scroll 0 0; position:relative; width:98%; height:98%; overflow:auto;'>";
	echo "<span class='subtituloPagina2'>ELIJA EL PROCESO AL QUE PERTENECE EL LISTADO</span><br><br>";
	echo "<div align='center' style='width:90%;'>";
		echo armarMenuProcesos();
	echo "</div>";
echo "</div>";
?>
</body>
</html>