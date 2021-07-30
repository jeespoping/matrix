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
fachos_000007:	MAESTRO PLANES SE RELACIONA CON movhos_000024	:MAESTRO DE EMPRESAS
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

if(isset ($peticionAjax))
{
	$consultaAjax ='';
	include_once('root/comun.php');
}
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

function formatos( $keyEmpresa, $keyPlan, $keyServicio, &$formatos )
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

function cambiarTodos( $keyEmpresa, $keyPlan, $keyServicio, &$estados )
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
		( $i == 0 ) ? $filtrar .= " {$row[0]} = 'on'" :  $filtrar .= " OR {$row[0]} = 'on' or Ccocod ='1800'";
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
	global $wcliame;
	global $wbasedato, $wfachos, $nivelUsuario, $nivelMaximo, $nivelMinimo, $pacientes, $conex, $whis, $wcco, $usuario, $caracteres, $caracteres2, $esAdmin, $puedeCerrar, $responsables;
	global $wempresaseleccionada;
	$consultar2 = false;
	$consultar 	= true;

	$centrosCostosPertinentes = centrosCostosPertinentes();
	$centrosCostos 			  = array();


	( $wcco != "" ) ? $filtroCco = " AND ubisac = '{$wcco}'" : $filtroCco = "AND ubisac != ''";

	//echo $filtroCco;

	if( trim($whis) == "" )
	{
		if( $nivelMinimo == "on"  )
		{


		$query = "SELECT Ubihis historia, Ubiing ingreso, Pacno1, Pacno2, Pacap1, Pacap2, Pacced, Pactid, Ubisac ccoActual, Ubihac habitacionActual, pf.id origen, b.fecha_data fechaIngreso, 'n' verFoto, pf.lenfeu fechaRecibo, pf.lenhou horaRecibo, b.ubialp altaProceso, pf.lencer estado, pf.lenrac nivelActual, TIMESTAMPDIFF( SECOND, CONCAT( '{$hoy}', ' ', '{$hora}' ), CONCAT( pf.lenfeu,' ', pf.lenhou ) ) tiempoAtencion
						FROM {$wbasedato}_000018 b
					   INNER JOIN
							 root_000037 on (ubihis = orihis AND ubiing = oriing  {$filtroCco})
				        LEFT JOIN
							 {$wfachos}_000011 as pf on ( b.Ubihis = pf.lenhis AND b.Ubiing = pf.lening )
					   INNER JOIN
							 root_000036 on ( Pactid = Oritid AND Pacced = Oriced )
					   INNER JOIN
							 {$wbasedato}_000011 on ( b.Ubisac = ccocod AND ({$centrosCostosPertinentes}) )
					   INNER JOIN
							 ".$wcliame."_000101 as pac on (ubihis = Inghis AND ubiing = Ingnin AND  Ingtpa ='E' ".(($wempresaseleccionada !='') ?  " AND Ingcem ='".$wempresaseleccionada."'" : "" )." )

					   WHERE ( pf.lenrac = '{$nivelUsuario}' or pf.lenrac is null )


						AND b.Fecha_data > '2017-03-13'



					   GROUP BY historia, ingreso, Pacno1, Pacno2, Pacap1, Pacap2, Pacced, Pactid, ccoActual, habitacionActual, origen, fechaIngreso
					   ORDER BY origen asc, tiempoAtencion desc, Pacap1 asc ";



				/*
				echo 	$query = "SELECT Ubihis historia, Ubiing ingreso, Pacno1, Pacno2, Pacap1, Pacap2,  Pacdoc as Pacced, Pactdo as Pactid, Ubisac ccoActual, Ubihac habitacionActual, pf.id origen, b.fecha_data fechaIngreso, 'n' verFoto, pf.lenfeu fechaRecibo, pf.lenhou horaRecibo, b.ubialp altaProceso, pf.lencer estado, pf.lenrac nivelActual, TIMESTAMPDIFF( SECOND, CONCAT( '{$hoy}', ' ', '{$hora}' ), CONCAT( pf.lenfeu,' ', pf.lenhou ) ) tiempoAtencion
								FROM {$wbasedato}_000018 b
						    INNER JOIN
						    cliame_000101 as pac on (ubihis = Inghis AND ubiing = Ingnin AND Ingtpa ='E' ".(($wempresaseleccionada !='') ?  " AND Ingcem ='".$wempresaseleccionada."'" : "" )."     {$filtroCco}  )
							INNER JOIN
							cliame_000100 on ( Pachis =  Inghis	)
							LEFT JOIN
								 {$wfachos}_000011 as pf on (b.Ubihis = pf.lenhis AND  b.Ubiing = pf.lening )
						    INNER JOIN
								 {$wbasedato}_000011 on ( b.Ubisac = ccocod AND ({$centrosCostosPertinentes}) )
						    WHERE  ( pf.lenrac = '{$nivelUsuario}' or pf.lenrac is null )
							AND b.Fecha_data > '2017-03-13'

							GROUP BY historia, ingreso, Pacno1, Pacno2, Pacap1, Pacap2, Pacced, Pactid, ccoActual, habitacionActual, origen, fechaIngreso
							ORDER BY origen asc, tiempoAtencion desc, Pacap1 asc ";*/



		}else
			{
				$condicionNivelUsuario = "";
				( $esAdmin == "on" ) ? $condicionNivelUsuario = " " : $condicionNivelUsuario = "AND lis.lenrac = '{$nivelUsuario}'";
				( $puedeCerrar == "on" or $esAdmin == "on" ) ? $condicionListasCerradas = " " : $condicionListasCerradas = "AND lis.lencer = 'off'";

					// $query = "SELECT lenhis historia, lening ingreso,  Pacno1, Pacno2, Pacap1, Pacap2, Pacced, Pactid,	 Ubisac ccoActual, Ubihac habitacionActual, lis.id origen, ubi.fecha_data fechaIngreso, 'n' verFoto, lis.lenfeu fechaRecibo, lis.lenhou horaRecibo,  ubi.ubialp altaProceso, lis.lencer estado, lis.lenrac nivelActual, TIMESTAMPDIFF( SECOND, CONCAT( '{$hoy}', ' ', '{$hora}' ), CONCAT( lis.lenfeu,' ', lis.lenhou ) ) tiempoAtencion
								// FROM {$wbasedato}_000018 as ubi
							   // INNER JOIN
									 // {$wfachos}_000011 as lis on ( ubi.ubihis = lis.lenhis AND ubi.ubiing = lis.lening {$condicionNivelUsuario} {$filtroCco} {$condicionListasCerradas})
							   // INNER JOIN
									 // root_000037 as ori on ( ori.Orihis = ubi.Ubihis AND ori.Oriing = ubi.Ubiing )
							   // INNER JOIN
									 // root_000036 as pac on ( pac.Pactid = ori.Oritid AND pac.Pacced = ori.Oriced )
							   // INNER JOIN
									 // {$wbasedato}_000017 as vis on (Eyrhis = Ubihis AND Eyring = Ubiing  AND Eyrest = 'on')
							   // INNER JOIN
								     // {$wbasedato}_000011 on ( vis.Eyrsor = ccocod AND ({$centrosCostosPertinentes}) )
							   // GROUP BY  historia, ingreso, Pacno1, Pacno2, Pacap1, Pacap2, Pacced, Pactid, ccoActual, habitacionActual, origen, fechaIngreso
							   // ORDER BY tiempoAtencion asc";

					$query = "SELECT lenhis historia, lening ingreso,  Pacno1, Pacno2, Pacap1, Pacap2, Pacced, Pactid,	 Ubisac ccoActual, Ubihac habitacionActual, lis.id origen, ubi.fecha_data fechaIngreso, 'n' verFoto, lis.lenfeu fechaRecibo, lis.lenhou horaRecibo,  ubi.ubialp altaProceso, lis.lencer estado, lis.lenrac nivelActual, TIMESTAMPDIFF( SECOND, CONCAT( '{$hoy}', ' ', '{$hora}' ), CONCAT( lis.lenfeu,' ', lis.lenhou ) ) tiempoAtencion
								FROM {$wbasedato}_000018 as ubi
							   INNER JOIN
									 {$wfachos}_000011 as lis on ( ubi.ubihis = lis.lenhis AND ubi.ubiing = lis.lening {$condicionNivelUsuario} {$filtroCco} {$condicionListasCerradas})
							   INNER JOIN
									 root_000037 as ori on ( ori.Orihis = ubi.Ubihis AND ori.Oriing = ubi.Ubiing )
							   INNER JOIN
									 root_000036 as pac on ( pac.Pactid = ori.Oritid AND pac.Pacced = ori.Oriced )
							   INNER JOIN
								     {$wbasedato}_000011 on ( ubi.Ubisac = ccocod AND ({$centrosCostosPertinentes}) )
							  INNER JOIN
									 ".$wcliame."_000101 on (ubihis = Inghis AND ubiing = Ingnin ".(($wempresaseleccionada !='') ?  " AND Ingcem ='".$wempresaseleccionada."'" : "" )." )
							  GROUP BY  historia, ingreso, Pacno1, Pacno2, Pacap1, Pacap2, Pacced, Pactid, ccoActual, habitacionActual, origen, fechaIngreso
							  ORDER BY tiempoAtencion asc";
			}
	}else
		{
			  // ( $nivelMinimo != "on" ) ? $condicionAlta = "" : $condicionAlta = " AND ubiald != 'on' AND ubisac != '' ";
			   ( $esAdmin == "on" ) ? $condicionNivelUsuario = " " : $condicionNivelUsuario = " pf.lenrac = '{$nivelUsuario}'";

				// $query = "SELECT Ubihis historia, Ubiing ingreso, Pacno1, Pacno2, Pacap1, Pacap2, Pacced, Pactid, Ubisac ccoActual, Ubihac habitacionActual, pf.id origen, b.fecha_data fechaIngreso, 'n' verFoto,  pf.lenfeu fechaRecibo, pf.lenhou horaRecibo,  b.ubialp altaProceso, pf.lencer estado, pf.lenrac nivelActual
							// FROM {$wbasedato}_000018 b
						   // INNER JOIN
								 // root_000037 on (ubihis = orihis AND ubiing = oriing AND orihis='{$whis}')
							// LEFT JOIN
								 // {$wfachos}_000011 as pf on (b.Ubihis = pf.lenhis AND  b.Ubiing = pf.lening )
						   // INNER JOIN
								 // root_000036 on ( Pactid = Oritid AND Pacced = Oriced)
						   // INNER JOIN
								 // {$wbasedato}_000017 as vis on (Eyrhis = Ubihis AND Eyring = Ubiing  AND Eyrest = 'on')
						   // INNER JOIN
								 // {$wbasedato}_000011 on ( vis.Eyrsor = ccocod AND ({$centrosCostosPertinentes}) )

						   // GROUP BY  historia, ingreso, Pacno1, Pacno2, Pacap1, Pacap2, Pacced, Pactid, ccoActual, habitacionActual, origen, fechaIngreso";


				$query = "SELECT Ubihis historia, Ubiing ingreso, Pacno1, Pacno2, Pacap1, Pacap2, Pacdoc as Pacced, Pactdo as Pactid, Ubisac ccoActual, Ubihac habitacionActual, pf.id origen, b.fecha_data fechaIngreso, 'n' verFoto, pf.lenfeu fechaRecibo, pf.lenhou horaRecibo, b.ubialp altaProceso, pf.lencer estado, pf.lenrac nivelActual
						    FROM {$wbasedato}_000018 b
						    INNER JOIN
						    ".$wcliame."_000101 as pac on (ubihis = Inghis AND ubiing = Ingnin AND Ingtpa ='E' AND Inghis ='{$whis}' )
							INNER JOIN
							".$wcliame."_000100 on ( Pachis =  Inghis	)
							LEFT JOIN
								 {$wfachos}_000011 as pf on (b.Ubihis = pf.lenhis AND  b.Ubiing = pf.lening )
						    INNER JOIN
								 {$wbasedato}_000011 on ( b.Ubisac = ccocod AND ({$centrosCostosPertinentes}) )
						    WHERE  b.Fecha_data > '2017-03-13'
							GROUP BY  historia, ingreso, Pacno1, Pacno2, Pacap1, Pacap2, Pacced, Pactid, ccoActual, habitacionActual, origen, fechaIngreso";


			//echo $query; 						   WHERE ( ({$condicionNivelUsuario}) or pf.lenrac is null )  EN CASO DE SER NECESARIO ESTO IBA ARRIBA DEL GROUP BY ANTERIOR
			//echo $query;
		}


	if( $consultar )
	{
		//echo $query;
		$rs  = mysql_query( $query, $conex ) or die( $query );
		$num = mysql_num_rows($rs);
		while( $row = mysql_fetch_array( $rs ) )
		{
			$qres = " SELECT Ingres
						FROM {$wbasedato}_000016
					   WHERE Inghis = '{$row['historia']}'
					     AND Inging = '{$row['ingreso']}'";
			$rsres   = mysql_query( $qres, $conex );
			$rowres  = mysql_fetch_array( $rsres );
			$responsable = $responsables[$rowres['Ingres']];
			$responsable = str_replace( $caracteres, $caracteres2, $responsable );
			$codigoresponsable = $rowres['Ingres'];


			$qusuario =  "SELECT Seguridad
						FROM ".$wcliame."_000101
					   WHERE Inghis = '{$row['historia']}'
					     AND Ingnin = '{$row['ingreso']}'";

			$rsres   = mysql_query( $qusuario, $conex );
			$rowres  = mysql_fetch_array( $rsres );
			$Usuarioadmision = $rowres['Seguridad'];

			$Usuarioadmision = explode("-", $Usuarioadmision);

			$qnomusu = "SELECT Descripcion FROM usuarios WHERE Codigo='".$Usuarioadmision[1]."' ";
			$rsres   = mysql_query( $qnomusu, $conex );
			$rowres  = mysql_fetch_array( $rsres );
			$nombreusuario = $rowres['Descripcion'];
			$nombreusuario = str_replace( $caracteres, $caracteres2, $nombreusuario );
			$nombreusuario =  substr($nombreusuario, 0, 15);
			// $nombreusuario = $qusuario;
			//$nombreusuario = "nada";
			 //$nombreusuario = $qnomusu;


			//$Usuarioadmision = str_replace( $caracteres, $caracteres2, $responsable );


			$nivelactual='';
			$qres = " SELECT roldes
						FROM {$wfachos}_000015
					   WHERE rolcod ='{$row['nivelActual']}'";
			$rsres   = mysql_query( $qres, $conex );
			$rowres  = mysql_fetch_assoc( $rsres );
			$nivelactual = $rowres['roldes'];

			if( ( $row['estado'] == 'on' ) or ( ( trim($row['estado']) == '' or ( trim($row['estado']) == 'off' )) AND ( ( $esAdmin == 'on' or trim( $row['nivelActual']) == $nivelUsuario ) or ( $nivelMinimo == "on" AND (!isset($row['nivelActual'])) ) )  ) )
			{
				$nombre = $row['Pacno1']." ".$row['Pacno2']." ".$row['Pacap1']." ".$row['Pacap2'];
				$nombre = str_replace( $caracteres, $caracteres2, $nombre );
				( trim( $row['origen'] ) == "" or !isset( $row['origen'] )) ? $row['origen']="1" :	$row['origen']="0";
				( trim( $row['estado'] ) == 'on' ) ?  $row['origen'] = "3" : $row['origen'] = $row['origen'];

				$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['ingreso']          = $row['ingreso'];
				$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['nombre']           = $nombre;
				$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['cedula']           = $row['Pacced'];
				$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['tipoDocu']         = $row['Pactid'];
				$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['fechaIngreso']     = $row['fechaIngreso'];
				$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['habitacionActual'] = $row['habitacionActual'];
				$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['verFoto']          = $row['verFoto'];
				$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['fechaRecibo']      = $row['fechaRecibo'];
				$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['horaRecibo']       = $row['horaRecibo'];
				$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['altaProceso']      = $row['altaProceso'];
				$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['estadoListado']    = $row['estado'];
				$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['responsable']      = $responsable;
				$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['codigoresponsable']      = $codigoresponsable;
				$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['usuarioadmision']      = $nombreusuario;
				$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['nivelActual']      = $nivelactual;
			}
		}
	}

	// LISTADO DE PACIENTES CON LISTAS QUE YA HAN SIDO ENVIADAS PERO QUE EL USUARIO DEJO PENDIENTE ALGUN SOPORTE
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
			$qres = " SELECT Ingres
						FROM {$wbasedato}_000016
					   WHERE Inghis = '{$row['historia']}'
					     AND Inging = '{$row['ingreso']}'";
			$rsres   = mysql_query( $qres, $conex );
			$rowres  = mysql_fetch_assoc( $rsres );
			$responsable = $responsables[$rowres['Ingres']];
			$nombre = $row['Pacno1']." ".$row['Pacno2']." ".$row['Pacap1']." ".$row['Pacap2'];
			$nombre = str_replace( $caracteres, $caracteres2, $nombre );

			$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['ingreso']          = $row['ingreso'];
			$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['nombre']           = $nombre;
			$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['cedula']           = $row['Pacced'];
			$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['tipoDocu']         = $row['Pactid'];
			$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['fechaIngreso']     = $row['fechaIngreso'];
			$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['habitacionActual'] = $row['habitacionActual'];
			$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['verFoto']          = $row['verFoto'];
			$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['fechaRecibo']      = $row['fechaRecibo'];
			$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['horaRecibo']       = $row['horaRecibo'];
			$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['altaProceso']      = $row['altaProceso'];
			$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['estadoListado']    = $row['estado'];
			$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['responsable']      = $responsable;
			$pacientes[$row['origen']][$row['ccoActual']][$row['historia']]['codigoresponsable']      = $rowres['Ingres'];
		}
	}


	return;
}

/*
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

	//LISTADO DE PACIENTES CON LISTAS QUE YA HAN SIDO ENVIADAS PERO QUE EL USUARIO DEJO PENDIENTE ALGUN SOPORTE
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
}*/

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

	$query = " SELECT DISTINCT(Ubisac) codigo
				 FROM {$wbasedato}_000018
				WHERE Ubihis = '{$whis}'
				  AND Ubiing = '{$wing}'";
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
				if($keyCco=='1800')
				{
					$servicios_utilizados[$keyServicio] = "";
				}
			}
		}
	}

	// $servicios_utilizados = array();
	// $servicios_utilizados[1] = "";
	// $servicios_utilizados[4] = "";
	// $servicios_utilizados[2] = "";

	return( $servicios_utilizados );
}

/**
	CONSULTA LOS SERVICIOS PARA LOS QUE APLICA UNA COMBINACIÓN EMPRESA_PLAN, ESTO SE ESTABLECE DESDE EL GENERADOR DE SOPORTES
**/
function empresa_planes_servicios( &$codigoEmpresaPlan, &$serviciosCompletos, &$serviciosActivoPorPlan )
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
function selectEstadoSoporte( $estadoActual, $estados, $empPlan, $soporte, $servicio, $clase ,$especialidad='')
{
	$select  = "";
	foreach( $estados as $keyEstado=>$datos )
	{

		( $keyEstado == $estadoActual ) ? $seleccionado = "checked" : $seleccionado = "";
		if( $keyEstado == $estadoActual )
			$valAnterior = "<input type='hidden' name='valAnterior_{$empPlan}_{$soporte}_{$servicio}' value='{$keyEstado}'>";
		if($especialidad =='')
		{
			$select .= "<td><input type='radio' title='".$datos."' class='{$clase} verificarcheck ' style='cursor:pointer;' name='estado_{$empPlan}_{$soporte}_{$servicio}' value='{$keyEstado}' {$seleccionado} onclick='actualizarEstadoSoporte(\"{$empPlan}\", \"{$soporte}\", this, \"{$servicio}\"  , \"n\" )'></td>";

		}
		else
		{
			$select .= "<td><input type='radio' title='".$datos."'  class='{$clase} verificarcheck class_{$soporte}' soporte='{$soporte}' style='cursor:pointer;' name='estado_{$empPlan}_{$soporte}_{$servicio}_{$especialidad}' value='{$keyEstado}' {$seleccionado} onclick='actualizarEstadoSoporte(\"{$empPlan}\", \"{$soporte}_{$especialidad}\", this, \"{$servicio}\" , \"{$especialidad}\")'></td>";
		}
	}
	$select .= $valAnterior;

	return($select);
}

/**
	CONSULTA Y ARMA LOS SELECTS CON LOS POSIBLES FORMATOS DE RECIBO DE UN SOPORTE(FISICO, ELECTRÓNICO, AMBOS....)
**/
function selectFormatoSoporte( $formatoActual, $formatos, $empPlan, $soporte, $estado, $servicio, $rowspan='')
{

	( $estado != "s" ) ? $disabled = "disabled" : $disabled = "";
	$select  = "";
	$clase = "formato_{$empPlan}_{$soporte}_{$servicio}";
	foreach( $formatos as $keyFormato=>$datos )
	{

		( trim($keyFormato) == trim($formatoActual) ) ? $seleccionado = "checked" : $seleccionado = "";
		if( $keyFormato == $formatoActual )
			$valAnterior = "<input type='hidden' name='valAnterior_{$empPlan}_{$soporte}_{$servicio}' value='{$keyFormato}'>";
		$select .= "<td rowspan='".$rowspan."'><input type='radio' {$disabled} class='{$clase}' style='cursor:pointer;' name='formato_{$empPlan}_{$soporte}_{$servicio}' value='{$keyFormato}' {$seleccionado} onclick='actualizarFormatoSoporte(\"{$empPlan}\", \"{$soporte}\", this, \"{$servicio}\")'></td>";
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
	//$datosPaciente .= "<div class='fila2' valign='middle' style='width:100%;'>";
	$datosPaciente .= "<span>DATOS DEL PACIENTE</span>";
	//$datosPaciente .= "<br><br>";
	$datosPaciente .= "<table>";
	$datosPaciente .= "<tr><td class='encabezadotabla'>NOMBRE:&nbsp;&nbsp;</td><td class='fila1'>{$nombre}</td><td>&nbsp;</td><td class='encabezadotabla'>CEDULA:&nbsp;&nbsp;</td><td class='fila1'>({$tipoDocu}) {$cedula}</td></tr>";
	$datosPaciente .= "<tr><td class='encabezadotabla'>HISTORIA:&nbsp;&nbsp;</td><td class='fila1'>{$whis}</td><td>&nbsp;</td><td class='encabezadotabla'>INGRESO:&nbsp;&nbsp;</td><td class='fila1'>{$wing}</td></tr>";
	$datosPaciente .= "<tr><td class='encabezadotabla'>HABITACI&Oacute;N</td><td class='fila1'>{$habitacionActual}</td><td>&nbsp;</td><td class='encabezadotabla'>FECHA INGRESO:&nbsp;&nbsp;</td><td class='fila1'>{$fechaIngreso}</td></tr>";
	$datosPaciente .= "</table>";
	//$datosPaciente .= "</div>";

	return($datosPaciente);
}

/**
	FUNCION QUE ARMA EL CÓDIGO HTML QUE PRESENTA EN PANTALLA LAS ENTIDADES RESPONSABLES DEL PACIENTE ASÍ COMO LO NECESARIO PARA AGREGAR O ELIMINAR UN RESPONSABLE
**/
function listadoResponsables( $whis, $wing, $ajax )
{
	global $hoy, $hora, $conex, $wbasedato, $wfachos, $entidades, $entidadesNoResponsables, $usuario, $esAdmin, $nivelUsuario,
	       $caracteres, $caracteres2, $entidadesResponsablesActuales, $numProceso, $wcliame;

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
	$query = "SELECT Pememp codigo, empnom nombre
				FROM {$wfachos}_000009, {$wcliame}_000024
			   WHERE Pememp = empcod
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
		//$responsables .= "<div class='fila2' style='width:100%; height:50%; vertical-align:top;'>";
		//$responsables .= "<span>PANEL DE RESPONSABLES</span>";
		//$responsables .= "<br><br>";
		//$responsables .= "<div align='left'>";
		// $responsables .= "<table>";
	    // $responsables .= "<tr><td align='right'><input type='button'  value='NUEVO RESPONSABLE' onclick='abrirPanelEntidades()'></font></td></tr>";
		// $responsables .= "</table>";
		//$responsables .= "</div>";
		//$responsables .= "<div aling='left' style='overflow-y:scroll; height:50px'>";
		$responsables .= "<span style='cursor:pointer; color:blue;font-family: verdana; font-size: 8pt' onclick='ocultarinfopaciente()'><a>Ocultar o ver Informaci&oacute;n del Paciente</a></span><table id='tbl_responsables' width='100%' class='fila1'>";

		$select  = "SELECT Ingcem ,Pacap1 ,	Pacap2 	,Pacno1 ,	Pacno2 ,Ubihac
						  FROM {$wcliame}_000101  , {$wcliame}_000100 , ".$wbasedato."_000018
						 WHERE Inghis  = '{$whis}'
						   AND Ingnin = '{$wing}'
						   AND Inghis = Pachis
						   AND Inghis =  Ubihis
						   AND Ingnin =  Ubiing ";


			$res = mysql_query( $select, $conex );
			$responsableactual = '';
			$nombrepaciente = '';
			$habitacionactual ='';
			if ($rowres = mysql_fetch_assoc( $res ))
			{
				$responsableactual =  $rowres['Ingcem'] ;
				$nombrepaciente    =  $rowres['Pacno1']." ".$rowres['Pacno2']." ".$rowres['Pacap1']." ".$rowres['Pacap2'];
				$habitacionactual  =  $rowres['Ubihac'];
			}
	    $responsables .= "<tr class='encabezadoTabla' ><td colspan='5' align='center'>Datos Responsable</td><td class='encabezadotabla' align='center' colspan='3'>Datos del Paciente</td></tr>";


		$responsables .= "<tr class='encabezadotabla'><td width='60px' align='center'>QUITAR</td><td width='60px' align='center'>CODIGO</td><td width='300px' align='center'>NOMBRE</td><td width='40px'>&nbsp;</td><td width='5px'></td><td class='encabezadotabla' align='center'>Historia</td>
					   <td class='encabezadotabla' align='center'>Nombre</td><td>Habitacion</td></tr>";

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
						//$titleEliminar = titleEliminarResponsable( $rowUsuario['descripcion'] );
						$titleEliminar = str_replace( $caracteres, $caracteres2, $titleEliminar );

						$eliminarResponsable = "<td align='center' class='msg_tooltip{$idTooltip}' title='{$titleEliminar}' style='cursor:pointer;'>&nbsp;</td> ";
					}
				//$responsables .= "<tr class='fila1'>{$eliminarResponsable}<td>{$keyCodigo}</td><td>{$entidades[$keyCodigo]['nombre']}</td><td style='cursor:pointer' onclick='verPlanes(\"{$keyCodigo}\")'><font color='blue'>Plan(es)</font></td><td style='display:none'>".buscarPlanes($keyCodigo, @$emp_plns)."</td></tr>";
				$responsables .= "<tr class='fila1'>{$eliminarResponsable}<td>{$keyCodigo}</td><td>{$entidades[$keyCodigo]['nombre']}</td><td style='cursor:pointer' onclick='verPlanes(\"{$keyCodigo}\")'><font color='blue'>Plan(es)</font></td><td style='display:none'>".buscarPlanes($keyCodigo, @$emp_plns)."</td><td><input type='button' class='botona'  value='NUEVO RESPONSABLE' onclick='abrirPanelEntidades()'></td><td>".$whis."-".$wing."</td><td>".$nombrepaciente."</td><td>".$habitacionactual."</td></tr>";
			}
		}
		$responsables .= "</table>";
		//$responsables .= "</div>";
		//$responsables .= "</div>";
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
		   $caracteres, $caracteres2, $nivel, $proceso, $esAdmin, $idTooltip, $estadoListado, $wtema, $wcliame;

	$query = "SELECT  *
				 FROM {$wfachos}_000011
				WHERE lenhis = '{$whis}'
				  AND lening = '{$wing}'";

	$rs    = mysql_query( $query, $conex );

	if( $row = mysql_fetch_array( $rs ) )
	{
		$proceso = $row["lenpro"];
	}


	//--> se redefinen los limites.2017-01-16
	$nivelesLimites = nivelesLimitesRedefinido( $nivelUsuario, $proceso );
	$nivelMinimo    = $nivelesLimites[0];
	$nivelMaximo    = $nivelesLimites[1];
	$nivelSiguiente = $nivelesLimites[2];
	$hayDatos      = false;


	//echo "hollaaa".$proceso;
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
	$tabla= "";

	$auxserv = '';

	foreach( $serviciosVisitados as $kservicio=>$kdato)
	{
		$auxserv .= "  ".$kservicio;
	}

//	echo $seguimiento   .= "1.   ".$auxserv;
	//-----------------------------------------------------------------------------SE CONSULTAN LOS PLANES ASOCIADOS A LA HISTORIA----------------------------------------------------------//
	/*El string resultante se transformará para consultar con la propiedad IN de mysql de tal manera que se consulten todo los soportes asociados a estos planes*/
	( $esAdmin == "on" ) ? $condicionNivelUsuario = "" : $condicionNivelUsuario = " AND ( lenrac = '{$nivelUsuario}' OR lencer='on' ) ";
	$query = " SELECT lenemp, lenobs observacion, lencer cerrado, id num, lenran nivelAnterior, lenrac nivelActual, fecha_data fechaCreacion, lenfeu fechaRecibo, lenhou horaRecibo, lenpro proceso
				 FROM {$wfachos}_000011
				WHERE lenhis = '{$whis}'
				  AND lening = '{$wing}'
				 {$condicionNivelUsuario}
				  AND lenest = 'on'";

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
		$row['nombre'] = str_replace( $caracteres, $caracteres2, $row['nombre'] );
		$nombreProceso = $row['nombre'];

		//----------------------------------------------------------->consulta los soportes que ya están guardados<---------------------------------------------/
		/*
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
		*/
		//$querydos .= "aaaaas".$query;


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
		ORDER BY nombre";

		/*
		$query = "SELECT sopcod codigo, sopnom nombre, Pememp codigoEmpresa, Pempln codigoPlan, a.*, soptip tipo, soptif formatoDefecto
					FROM {$wfachos}_000006, {$wfachos}_000009 b, {$wfachos}_000010, $tmp a
				   WHERE b.pemcod = empPlan
					 AND Sopcod = soporte
				   GROUP BY codigoEmpresa, codigoPlan, servicio, soporte
				   ORDER BY soporte";
		*/
		$rs = mysql_query( $query, $conex ) or die( mysql_error() );

		//$querydos .= "aaaaas".$query;
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
					   GROUP BY codigoEmpresa, codigoPlan, soporte, sesser
					   ORDER BY nombre";
			$rs = mysql_query( $query, $conex ) or die( mysql_error() );

			$auxquery = $query;
			//echo $query;
			$query = "SELECT *
						FROM {$tmp}";
			$rs = mysql_query( $query, $conex );

			while( $row = mysql_fetch_array($rs) )
			{
				( trim($row['servicios']) == "") ? $aplicaEnTodos = true : $aplicaEnTodos = false;  //variable para controlas si un soporte aplica para todos los servicios o solo para los que está filtrado;



				$servicios = explode( ",", $row['servicios']);// separo los servicios en los que aplica dicho soporte
				$auxquery .= "--".$row['servicios']."++";
				foreach( $serviciosVisitados as $kservicio=>$kdato)
				{
					$auxquery .= "visitados".$kservicio;
				}

				foreach( $serviciosVisitados as $keyServicio => $dato )// se recorre el arreglo de servicios utilizados por el paciente
				{

					$hayDatos = true;

					if( in_array( $keyServicio, $servicios) or ($aplicaEnTodos) )//se pregunta si el soporte aplica para cada servicio, sea por especificación o porque el soporte no tiene filtro de servicios
					{
						$keyServicio = 'n';
						if(!isset($consolidado[$row['codigoEmpresa']][$row['codigoPlan']][$keyServicio][$row['soporte']]))//ESTO FILTRA QUE EL SOPORTE NO EXISTA YA EN LA TABLA, DE SER ASÍ SE CONSERVAN LOS DATOS YA GUARDADOS EN {$wfachos}_000012
						{
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
						}
					}
				}

			}
		}
		if($hayDatos)
		{

			//-------------------------------------------------------------------CONSULTO LAS EMPRESAS------------------------------------------------------------------------------//
			 $query = "SELECT Pememp codigo, empnom nombre
						FROM {$wfachos}_000009, {$wcliame}_000024
					   WHERE Pememp = empcod
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
			$empresa_planes_servicios = empresa_planes_servicios( $empresas_planes, $serviciosArray, $servicios_empresa_plan );


			( $puedeCerrar == "on" ) ? $inhabilitarCompleto = "" : $inhabilitarCompleto = "disabled";

			$titles = titlesEnvioDevolucion( $nivelAnterior, $nivelSiguiente, $nivelActual, $nivelMinimo, $nivelMaximo, $codigoProceso);
			$titleDevolucion = $titles[0];
			$titleEnvio = $titles[1];

			(( ( $nivelActual == $nivelUsuario ) or ( $esAdmin == "on" ) ) and ( trim($titleDevolucion) == "" ) ) ? $inhabilitarDevolucion = "disabled" : "";
			(( ( $nivelActual == $nivelUsuario ) or ( $esAdmin == "on") ) and ( trim($titleEnvio) == "" ) ) ? $inhabilitarEnvio = "disabled" : "";

			( $estadoListado == "off" ) ? $estado = "En Proceso " : $estado = " Finalizado ";
			( $estadoListado == "on" ) ? $checked = " checked " : $checked = "";
			( $estadoListado == "on" ) ? $inhabilitarMovimiento = " disabled " : $inhabilitarMovimiento = "";
			( $esAdmin == "off" ) ? $inhabilitarCambioProceso  = " disabled " : $inhabilitarCambioProceso = "";
			( $esAdmin == "off" ) ? $titleCambiarProceso  = " title='".titleCambiarProceso()."' " : $titleCambiarProceso = "";
			$etapa = str_replace( $caracteres, $caracteres2, $etapa );


			$select  = "SELECT Ingcem ,Pacap1 ,	Pacap2 	,Pacno1 ,	Pacno2
						  FROM {$wcliame}_000101  , {$wcliame}_000100
						 WHERE Inghis  = '{$whis}'
						   AND Ingnin = '{$wing}'
						   AND Inghis = Pachis ";


			$res = mysql_query( $select, $conex );
			$responsableactual = '';
			$nombrepaciente = '';
			if ($rowres = mysql_fetch_assoc( $res ))
			{
				$responsableactual =  $rowres['Ingcem'] ;
				$nombrepaciente    =  $rowres['Pacno1']." ".$rowres['Pacno2']." ".$rowres['Pacap1']." ".$rowres['Pacap2'];
			}

			//echo " edb--> antes de crear la tabla\n";
			$tabla .= "<input type='hidden' id='input_nivelListado' value='{$nivelActual}'>";
			$tabla .= "<input type='hidden' id='input_nivelAnterior' value='{$nivelAnterior}'>";

		//	$tabla .= "<br><br>";
			//$tabla .= "<div >";
			$tabla .= "<span style='cursor:pointer; color:blue;font-family: verdana; font-size: 8pt' onclick='ocultarinfoproceso()'><a>Ocultar o ver Informaci&oacute;n del Proceso</a></span><table id='tableinfoproceso' width='100%' class='fila1' >";
			// $tabla .= "<tr><td   width='15%' class='encabezadotabla' style='border: 1px solid; border-color: #2A5DB0;'>Lista de Chequeo N&uacute;mero: </td><td width='30%' align='center' class='fila1' style='border: 1px solid; border-color: #2A5DB0;'>{$numeroDoc}</td>";
			// $tabla .= "<tr></tr>";
			$tabla .= "<tr ><td class='encabezadotabla' align='center' colspan='4'>Informaci&oacute;n del Proceso</td></tr>";
			$tabla .= "
					   <td class='encabezadotabla' align='center'>Fecha Creaci&oacute;n</td>
					   <td class='encabezadotabla' align='center'>Proceso y Tiempo en Espera</td>
					   <td class='encabezadotabla' align='center'>Estado</td>
					   <td class='encabezadotabla' align='center'>Proceso</td>
					   ";

			// $html  = "";
			// $error = 0;
			// $datosPaciente = "";
			// $listadoActual = "";
			// $control 	   = false; /*variable para el control de errores*/
			// $resultados    = array();
			// $entidades     = array();
			// $entidadesResponsablesActuales = array();
			// $entidadesNoResponsables       = array();
			// $serviciosUtilizado 		   = array();
			$nombrepaciente = str_replace( $caracteres, $caracteres2, $nombrepaciente );
			//$resultados = listadoResponsables( $whis, $wing, false ); /*en distintas posiciones almacena responsables y NO responsables;*/

			//$tabla .= "<tr><td>".$resultados['panelResponsables']."</td></tr>";


			$tabla .= "<tr>
					   <td align='center' class='fila1' >{$fechaCreacion}</td>
					   <td align='center' class='fila1' >{$etapa} (".tiempoEnLaEtapa( $fechaRecibo, $horaRecibo ).")</td>
					   <td id='td_estado' align='center' class='fila1' >{$estado}</td>
					   <td id='td_proceso' align='center' class='fila1'  >{$nombreProceso}</td>
					  ";
			$tabla .= "</tr>";
			$tabla .= "</table>";
			$tabla .= "<table align='center'>";
			$tabla .= "<tr>
							<td><input type='button'  {$inhabilitarMovimiento} class='botona msg_tooltip{$idTooltip}' title='{$titleDevolucion}' name='btn_devolver' value='<<- DEVOLVER' {$inhabilitarDevolucion} onclick='devolverEnviarListado(\"d\", \"{$nivelAnterior}\", this )'></td>
							<td ><input type='button' {$inhabilitarMovimiento} class='botona msg_tooltip{$idTooltip}' title='{$titleEnvio}' name='btn_enviar' value='ENVIAR ->>' {$inhabilitarEnvio} onclick='devolverEnviarListado(\"e\", \"{$nivelSiguiente}\", this )'></td>
							<td align='center' ><input {$inhabilitarCambioProceso} type='button' id='btn_cambiarProceso' class='botona msg_tooltip' value='CAMBIAR FLUJO' {$titleCambiarProceso} onclick='cambiarProceso( \"{$codigoProceso}\", \"{$responsableactual}\" );'></td>
							<td  align='center' >COMPLETO<input type='checkbox' {$checked} {$inhabilitarCompleto} value='' onclick='cambiarEstadoDelListado(this)'></td>
							</tr>";
			$tabla .= "</table>";
			//$tabla .= "</div>";
			$tabla .= "<br>";


			foreach( $consolidado as $keyEmpresa=>$planes )
			{
				$tabla .= "<div align='center'><span class='subtituloPagina2' mostrando='n' style='cursor:pointer;' ><font size=5><b>{$entidades[$keyEmpresa]['nombre']}</b></font></span></div><br>";
				$tabla .= "<div id='div_{$keyEmpresa}'>";
				foreach( $planes as $keyPlan=>$servicios )
				{
					//$tabla .= "<span class='subtituloPagina2' mostrando='n' style='cursor:pointer;' onclick='mostrarOcultar(\"div_{$keyEmpresa}_{$keyPlan}\", this)'><font size=3><b>PLAN:{$planesArray[$keyPlan]['nombre']} </b></font></span>";
					$tabla .= "<div id='div_{$keyEmpresa}_{$keyPlan}' >";
					$tabla .= "<table width=100% >";
					$tabla .= "</table>";
					foreach( $servicios as $keyServicio=>$soportes )
					{
						$claseHijos 	= "{$keyEmpresa}_{$keyPlan}_{$keyServicio}";
						$cambiarTodos 	= cambiarTodos( $keyEmpresa, $keyPlan, $keyServicio, $estadosArray );
						$globalFormatos = formatos( $keyEmpresa, $keyPlan, $keyServicio, $formatosArray );
						$numeroFormatos = count( $formatosArray );
					//	$tabla .= "<span class='subtituloPagina2' mostrando='n' style='cursor:pointer;' onclick='mostrarOcultar(\"div_{$keyEmpresa}_{$keyPlan}_{$keyServicio}\", this)'><font size=3><b>&nbsp;&nbsp;SERVICIO:";
						foreach ( $serviciosVisitados as $kservicio=>$kdato)
						{
							//$tabla .= "  ".$serviciosArray[$kservicio]['nombre'];
						}
						//$tabla .= "</b></font></span>";

						$tabla .= "<div id='div_{$keyEmpresa}_{$keyPlan}_{$keyServicio}' >";
						$tabla .= "<table width=100% >";
						$tabla .= "<tr class='botona' align='center'><td width=10% colspan=3> RECIBIDO  </td><td width=15% rowspan='2' colspan='3'> NOMBRE </td><td width=5% rowspan='2'>TIPO</td><td rowspan='1' colspan='{$numeroFormatos}' width=5%>FORMATO</td><td width='20%' rowspan='2'>RESPONSABLE</td><td rowspan='2' colspan='2' width='45%'>OBSERVACI&Oacute;N</td>";
						$tabla .= "<tr class='botona' align='center'>{$cambiarTodos}{$globalFormatos}</tr>";

						foreach( $soportes as $keySoporte=>$datos )
						{
							$tabla .= "<input type=hidden id='formato_{$datos['empPlan']}_{$keySoporte}_{$keyServicio}' value='{$datos['formatoDefecto']}'>";
							( $datos['existe'] == "s" ) ? $datos['formato'] = $datos['formato'] : $datos['formato'] = "";

							$selectfachos = "SELECT Sopevo
											   FROM {$wfachos}_000006
											  WHERE  Sopcod='".$keySoporte."' ";

							$resevolucion= mysql_query($selectfachos, $conex) or die("Error: ".mysql_errno()." - en el query: ".$selectfachos." - ".mysql_error());

							$evolucion = '';
							while($rowevolucion = mysql_fetch_array($resevolucion))
							{
								$evolucion = $rowevolucion['Sopevo'];
							}
							if($evolucion=='si')
							{
								$wevoluciones = 0;
								$wmovhos='movhos';
								$array_evoluciones = traer_evoluciones($wmovhos, $whis, $wing, $wemp_pmla, $wevoluciones);
								$warreglo_evoluciones = base64_encode(serialize($array_evoluciones));
								$respuestaarray =  mostrar_detalle_especialista($wemp_pmla, $whis, $wing, $wfecha, $whora, $warreglo_evoluciones, $evoluciones, $tipo);

								$soporteEspecialidad ='';
								$soporteEspecialidad = $keySoporte."_".$respuestaarray['especialidad'][1];

								if($respuestaarray['especialidad'][1])
								{

									$estado = crear_soportes_especialidad($soporteEspecialidad , $datos['empPlan'], $keyServicio, 'n');


									$tabla .= "<tr class='fila1' align='center'>".selectEstadoSoporte($estado, $estadosArray, $datos['empPlan'], $keySoporte, $keyServicio, $claseHijos , $respuestaarray['especialidad'][1] );


									$tabla .="<td align='left' nowrap='nowrap'  rowspan='".$respuestaarray['cuantos']."' style='width:195px;'>";
									$tabla .= str_replace( $caracteres, $caracteres2, $datos['nombre'] )."</td>";


									// $tabla .="<td align='left' nowrap='nowrap' style='width:195px;'  rowspan='".$respuestaarray['cuantos']."' >";
									// $tabla .= $respuestaarray['html'];
									// $tabla .=  "</td>";

									//$tabla .="<td align='left' nowrap='nowrap' style='width:195px;'   >";
									$tabla .= $respuestaarray['separado'][1];
									//$tabla .=  "</td>";


									// verifico el estado de todos
									if($estado == 's')
									{
											$estadoaux = $estado;
											for ($r=1 ; $r<$respuestaarray['cuantos'] ;$r++)
											{
												$soporteEspecialidad ='';
												$soporteEspecialidad = $keySoporte."_".$respuestaarray['especialidad'][($r+1)];
												$estado = crear_soportes_especialidad($soporteEspecialidad , $datos['empPlan'], $keyServicio, 'n');

												if($estado !='s')
												{
													$estadoaux = $estado;
												}
											}
											$tabla .="<td   rowspan='".$respuestaarray['cuantos']."'> ".str_replace( $caracteres, $caracteres2, $datos['tipo'] )." </td>".selectFormatoSoporte( $datos['formato'], $formatosArray, $datos['empPlan'], $keySoporte, $estadoaux, $keyServicio ,$respuestaarray['cuantos'] )." </td><td align='left' rowspan='".$respuestaarray['cuantos']."'>".$responsablesArray[$datos['responsable']]."<td rowspan='".$respuestaarray['cuantos']."'><div style='overflow-y:scroll; height:80px; background-color:white;' align='left' height:100px id='txar_{$datos['empPlan']}_{$keySoporte}_{$keyServicio}'>".formatearObservaciones( $datos['observacion'] )."</div></td><td  width='5%' rowspan='".$respuestaarray['cuantos']."' ><input width='100%' type='button' class='botonAdd' onclick='mostrarDivAgregarComentario(\"".$keySoporte."\", ".$datos['empPlan'].", \"".$keyServicio."\")'></td></tr>";

									}
									else
									{
										$tabla .="<td   rowspan='".$respuestaarray['cuantos']."'> ".str_replace( $caracteres, $caracteres2, $datos['tipo'] )." </td>".selectFormatoSoporte( $datos['formato'], $formatosArray, $datos['empPlan'], $keySoporte, $estado, $keyServicio ,$respuestaarray['cuantos'] )." </td><td align='left' rowspan='".$respuestaarray['cuantos']."'>".$responsablesArray[$datos['responsable']]."<td rowspan='".$respuestaarray['cuantos']."'><div style='overflow-y:scroll; height:80px; background-color:white;' align='left' height:100px id='txar_{$datos['empPlan']}_{$keySoporte}_{$keyServicio}'>".formatearObservaciones( $datos['observacion'] )."</div></td><td  width='5%' rowspan='".$respuestaarray['cuantos']."' ><input width='100%' type='button' class='botonAdd' onclick='mostrarDivAgregarComentario(\"".$keySoporte."\", ".$datos['empPlan'].", \"".$keyServicio."\")'></td></tr>";

									}


										//--traer_evoluciones($wmovhos, $whis, $wing, $wemp_pmla, &$wevoluciones)
									for ($r=1 ; $r<$respuestaarray['cuantos'] ;$r++)
									{
										$soporteEspecialidad ='';
										$soporteEspecialidad = $keySoporte."_".$respuestaarray['especialidad'][($r+1)];
										$estado = crear_soportes_especialidad($soporteEspecialidad , $datos['empPlan'], $keyServicio, 'n');


										$tabla .= "<tr class='fila1' align='center'>".selectEstadoSoporte($estado, $estadosArray, $datos['empPlan'], $keySoporte, $keyServicio, $claseHijos, $respuestaarray['especialidad'][($r+1)] );
										//$tabla .="<td align='left' nowrap='nowrap' style='width:195px;'   >";
										$tabla .= $respuestaarray['separado'][($r+1)];
										//$tabla .=  "</td>";
										$tabla .="</tr>";
									}
								}
								else
								{
									$tabla .= "<tr class='fila1' align='center'>".selectEstadoSoporte($datos['estado'], $estadosArray, $datos['empPlan'], $keySoporte, $keyServicio, $claseHijos )."<td align='left' colspan='3' nowrap='nowrap' style='width:390px;'>".str_replace( $caracteres, $caracteres2, $datos['nombre'] )." </td><td> ".str_replace( $caracteres, $caracteres2, $datos['tipo'] )." </td>".selectFormatoSoporte( $datos['formato'], $formatosArray, $datos['empPlan'], $keySoporte, $datos['estado'], $keyServicio )." </td><td align='left'>".$responsablesArray[$datos['responsable']]."<td><div style='overflow-y:scroll; height:40px; background-color:white;' align='left' height:40px id='txar_{$datos['empPlan']}_{$keySoporte}_{$keyServicio}'>".formatearObservaciones( $datos['observacion'] )."</div></td><td  width='5%' ><input width='100%' type='button' class='botonAdd' onclick='mostrarDivAgregarComentario(\"".$keySoporte."\", ".$datos['empPlan'].", \"".$keyServicio."\")'></td></tr>";

								}
							}
							else
							{
								$tabla .= "<tr class='fila1' align='center'>".selectEstadoSoporte($datos['estado'], $estadosArray, $datos['empPlan'], $keySoporte, $keyServicio, $claseHijos )."<td align='left' colspan='3' nowrap='nowrap' style='width:390px;'>".str_replace( $caracteres, $caracteres2, $datos['nombre'] )." </td><td> ".str_replace( $caracteres, $caracteres2, $datos['tipo'] )." </td>".selectFormatoSoporte( $datos['formato'], $formatosArray, $datos['empPlan'], $keySoporte, $datos['estado'], $keyServicio )." </td><td align='left'>".$responsablesArray[$datos['responsable']]."<td><div style='overflow-y:scroll; height:40px; background-color:white;' align='left' height:40px id='txar_{$datos['empPlan']}_{$keySoporte}_{$keyServicio}'>".formatearObservaciones( $datos['observacion'] )."</div></td><td  width='5%' ><input width='100%' type='button' class='botonAdd' onclick='mostrarDivAgregarComentario(\"".$keySoporte."\", ".$datos['empPlan'].", \"".$keyServicio."\")'></td></tr>";


							}

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
			$tabla .= "<div align='center' class='fila1'>";
			$tabla .= "<p class='encabezadotabla'>OBSERVACION GENERAL</p>";
			$tabla .= "<textarea id='observacionListado' style='width:400px; height=100px;' onkeypress='if(validar(event)) guardarObservacionGeneral(this)' onblur='guardarObservacionGeneral(this)'>{$observacion}</textarea>";
			$tabla .= "</div>";
			$tabla .= "<br><br>";
			$tabla .= "<table width=100%>";
			$tabla .= "<tr><td><input type='button' {$inhabilitarMovimiento} class='botona msg_tooltip{$idTooltip}' title='{$titleDevolucion}' name='btn_devolver' value='<<- DEVOLVER' {$inhabilitarDevolucion} onclick='devolverEnviarListado(\"d\", \"{$nivelAnterior}\", this )'></td><td align='center'>&nbsp;</td><td align='right'><input type='button' {$inhabilitarMovimiento} class='botona msg_tooltip{$idTooltip}' title='{$titleEnvio}' name='btn_enviar' value='ENVIAR ->>' {$inhabilitarEnvio} onclick='devolverEnviarListado(\"e\", \"{$nivelSiguiente}\", this )'></td></tr>";
			$tabla .= "</table>";
		}
		//$tabla .= $seguimiento;
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
			$tabla .= "<tr><td><span class='subtituloPagina2'>hay datos ".$auxquery."---".$querydos." </span></td></tr>";
			$tabla .= "<tr><td><span class='subtituloPagina2'>EL PACIENTE NO HA UTILIZADO NINGUN TIPO SE SERVICIO O EL LISTADO SE ENCUENTRA EN UN NIVEL DE REVISI&Oacute;N DIFERENTE AL SUYO</span></td></tr>";
			$tabla .= "<br><br>";
			$tabla .= "</table>";
		}
	return($tabla);
}

function crear_soportes_especialidad ($soporte, $empPlan, $keyServicio, $estado)
{
	global $wemp_pmla, $conex, $wfachos, $whis, $wing;

	$select = "SELECT delest
				 FROM {$wfachos}_000012
				 WHERE delhis ='".$whis."'
				  AND  deling ='".$wing."'
				  AND  delsop ='".$soporte."'
				  AND  delemp ='".$empPlan."'
				  AND  delser ='".$keyServicio."' ";
	$res = mysql_query($select, $conex) or die("Error: ".mysql_errno()." - en el query: ". $select ." - ".mysql_error());

	$estado = 'n';
	if($row = mysql_fetch_array($res))
	{
		$estado = $row['delest'];
	}
	else
	{

		$query = "INSERT INTO {$wfachos}_000012(Medico, Fecha_data, Hora_data, delhis, deling, delsop, delemp, delser, delfor, delest, delres, delobs, seguridad)
										   VALUES('fachos', '{$hoy}', '{$hora}', '{$whis}', '{$wing}', '{$soporte}', '{$empPlan}', '{$keyServicio}', '', '{$estado}', '{$responsable}', '', '{$usuario}' )";


		$rsAux = mysql_query( $query, $conex );

		$estado = 'n';
	}

	return $estado ;


}

//---------
// FUNCION PARA MOSTRAR LOS PENDIENTES POR GRABAR PARA LAS EVOLUCIONES //Enero 12/2012 Jonatan Lopez
function traer_evoluciones($wmovhos, $whis, $wing, $wemp_pmla, &$wevoluciones)
{

	global $conex;
	global $whce;

	//Extrae el nombre del formulario donde se registran las evoluciones.
	$wform_evoluciones = consultarAliasPorAplicacion($conex, $wemp_pmla, 'FormularioEvoluciones');
	$wform_posicion_evo = explode("-", $wform_evoluciones); // Se explota el registro que esta separado por comas, para luego usarlo en el ciclo siguiente.
	$wformulario = $wform_posicion_evo[0];
	$wposicion = $wform_posicion_evo[1];
	// CONSULTA PARA EXTRAER LA FECHA MAXIMA DE LAS EVOLUCIONES GUARD PARA UNA HIST E INGRESO
	$query =     "  SELECT MAX( CONCAT( Fecha_data, ' ', Hora_data ) ) AS FechaHora "
					."FROM ".$wmovhos."_000119 "
				   ."WHERE Glnhis = '".$whis."'
					   AND Glning = '".$wing."'
					   AND Glnind = 'E'
					   AND Glnest = 'on'";
	$res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ". $query ." - ".mysql_error());
	$rows = mysql_fetch_array($res);
	$fechamax_evolucion = $rows['FechaHora'];
	$whce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'HCE');

	//Consulta todos los especialistas que tienen el campo usures diferente de on, quiere decir los que son profesores,
	//hago la relacion de los codigos para extraer la especialidad, el nombre y el codigo de la especialidad.
	$query =    " SELECT usucod, usualu, u.descripcion, espmed.Medesp, nomesp.Espnom, Meddoc"
				."  FROM ".$whce."_000020 as usuhce
					INNER JOIN
					usuarios as u on (u.codigo = usuhce.Usucod )
					INNER JOIN
					".$wmovhos."_000048 as espmed on (espmed.Meduma = usuhce.Usucod)
					INNER JOIN
					".$wmovhos."_000044 as nomesp on (nomesp.Espcod = SUBSTRING_INDEX(espmed.Medesp, '-', 1))"
				." WHERE usures != 'on'";
	$res= mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());

	//Se crea por defecto la posicion medico de turno, para asociarlo a los alumnos que tienen mas de un profesor.
	$array_profesores = array(  'medico_turno'=> array(
														   'cuantos'=>array(),
											   'nombre_especialista'=>'MEDICO DE TURNO',
														   'cod_esp'=>'medico_turno',
											   'nombre_especialidad'=>'MEDICO DE TURNO',
															'alumnos'=>array()
												   )
								);

	// echo "<div align=left>";
	// echo "<pre>";
	// print_r($array_profesores);
	// echo "<pre>";
	// echo "</div>";

	$array_alumnos = array();
	//Al recorrer el resultado de la consulta se crea un arreglo $array_profesores[$row['usucod']][dato] y se agrega al arreglo $array_profesores[$row['usucod']]['alumnos'][],
	//todos los alumnnos asignados a el, solo se agregaran si la posicion $alumno del foreach es diferente de vacio y diferente de punto.
	while($row = mysql_fetch_array($res))
	{
		if(!array_key_exists($row['usucod'], $array_profesores))
		{
			$array_profesores[$row['usucod']] = array();
		}

		$array_profesores[$row['usucod']]['cuantos'] = array();
		$array_profesores[$row['usucod']]['nombre_especialista'] = $row['descripcion'];
		$array_profesores[$row['usucod']]['cod_esp'] = $row['Medesp'];
		$array_profesores[$row['usucod']]['nombre_especialidad'] = $row['Espnom'];
		$array_profesores[$row['usucod']]['cedula_medico'] = $row['Meddoc'];
		$explo_alum = explode(",", $row['usualu']);

		foreach ($explo_alum as $key => $alumno)
			{
				$array_profesores[$row['usucod']]['alumnos'][] = $alumno;

				//Solo se agregan los que tengan datos en la posicion $alumno and diferente de punto.
				if(!empty($alumno) and $alumno != '.')
					{
					$array_alumnos[$alumno]['profesor'][] = $row['usucod'];
					}
			}
	}

	//Consulta todas las  evoluciones que no se han registrado a partir de la ultima fecha y hora de registro
	//en la tabla 119 de movhos para la historia e ingreso y el parametro Glnind = 'E', se trae tambien el nombre, la especialidad y el codigo de la especialidad.
	$query =    " SELECT firusu, usuhce.usualu, u.descripcion, usuhce.usures, ".$whce."_000036.Fecha_data as fechafir, ".$whce."_000036.Hora_data as horafir, "
				." ".$whce."_000036.firhis, ".$whce."_000036.firing, ".$whce."_000036.firrol "
				."  FROM ".$whce."_000036, ".$whce."_000020 as usuhce
					INNER JOIN
					usuarios as u on (u.codigo = usuhce.Usucod )"
				." WHERE Firhis = '".$whis."'"
				."   AND Firing = '".$wing."'"
				."   AND Firpro = '".$wformulario."'"
				."   AND Firfir = 'on'"
				."   AND firusu = usucod "
				."   AND u.Activo = 'A' "
				."   AND CONCAT( ".$whce."_000036.Fecha_data, ' ', ".$whce."_000036.Hora_data ) > '".$fechamax_evolucion."'";
	$res= mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());


	while($row1 = mysql_fetch_array($res))
	{
		//Aqui solo permite ingresar si el usuario es residente, osea alumno.
		if($row1['usures'] == 'on')
		{

		  //Verifica que en el array_alumnos se encuentre el codigo del alumno.
		  if(array_key_exists($row1['firusu'], $array_alumnos))
			{

			  //Si un alumno tiene varios profesores, pondra como especialista la palabra medico de turno.
			  if (count($array_alumnos[$row1['firusu']]['profesor'])>1)
				{

						//Si el especialista que firma es residente entonces traera el profesor que le confirmo el formulario.
						$wprofe_confirma = consultar_profe_confirma($row1['firhis'],$row1['firing'],$row1['fechafir'], $row1['horafir'], $wformulario, $wposicion);
						$el_profe = $wprofe_confirma['codigo_profesor'];

						//Si ese profesor no esta en el arreglo de profesores entonces lo agregara.
						if (!array_key_exists($el_profe, $array_profesores))
						{
							$array_profesores[$el_profe]['cuantos'] = array();
							$array_profesores[$el_profe]['nombre_especialista'] = $wprofe_confirma['nombre'];
							$array_profesores[$el_profe]['cod_esp'] = $wprofe_confirma['codigo_especialidad'];
							$array_profesores[$el_profe]['nombre_especialidad'] = $wprofe_confirma['descrip_especialidad'];
							$array_profesores[$el_profe]['cedula_medico'] = $wprofe_confirma['cedula_medico'];

						}

						$array_profesores[$el_profe]['cuantos'][] = $row1['fechafir'];


						//Se declara esta variable para que se le asigne al medico de turno la especialidad de uno de los profesores que tiene asignado, la especialidad
						//del profesor siempre sera la misma en donde aparezca el alumno.

						//Se comenta esta variable ya que debe traer exactamente la especialidad del medico que le confirmo el formulario al alumno.
						//$cod_profesor = $array_alumnos[$row1['firusu']]['profesor'][0];

						//Al array profesores en la posicion cod_esp se le asigna la primera aparicion de codigo de su profesor.
						$array_profesores[$el_profe]['cod_esp'] = $array_profesores[$el_profe]['cod_esp'];

						//Al array profesores en la posicion nombre_especialidad se le asigna la primera aparicion de especialidad de su profesor.
						$array_profesores[$el_profe]['nombre_especialidad'] = $array_profesores[$el_profe]['nombre_especialidad'];
				}
			 else
				{
						//Si el especialista solo aparece una vez y no es alumno de nadie entonces deja los datos como vienen en el arreglo.
						$el_profe = $array_alumnos[$row1['firusu']]['profesor'][0];
						//Codigo del profesor y cuantas apariciones tiene.
						$array_profesores[$el_profe]['cuantos'][] = $row1['fechafir'];

				}

			}

		}
		//Si el usuario no es residente, entonces la informacion se mantendra como viene en el arreglo de profesores.
		else
		{

			if(!array_key_exists($row1['firusu'], $array_profesores)){

			   $array_profesores[$row1['firusu']]['cuantos'] = array();
			}

		   $array_profesores[$row1['firusu']]['cuantos'][] = $row1['fechafir'];

		}

	}

	$array_aux = $array_profesores; // Auxiliar para recorrer array_profesores y eliminar los que tengan cero.

	//Elimino los elementos del arreglo de profesores que tienen la posicion cuantos en cero.
	foreach ($array_aux as $key => $value)
		{
			if(count($value['cuantos']) == '0')
			{
				unset($array_profesores[$key]);
			}else{

			//Cuento las evoluciones por medico, el arreglo en la posicion $array_profesores[$key]['cuantos'] contiene las fechas de las evoluciones firmadas.
			$wevoluciones += count($array_profesores[$key]['cuantos']);

			}

		}

   // echo "<div align=left>";
   // echo "<pre>";
   // print_r($array_profesores);
   // echo "<pre>";
   // echo "</div>";

  return $array_profesores;


}

function mostrar_detalle_especialista($wemp_pmla, $whis, $wing, $wfecha, $whora, $array_profesores, $evoluciones, $tipo)
    {

		global $conex;
		global $wemp_pmla;
        global $whce;
        global $wmovhos;
		$wmovhos='movhos';
		$whce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'HCE');
        $array_profesores = unserialize(base64_decode($array_profesores));
		$contadortr=0;
		if($tipo == 'E'){
			$texto_tipo = "Evoluci&oacute;n";
			$texto_tipo1 = "Evoluciones";
		}

		if($tipo == 'INT'){
			$texto_tipo = "Interconsulta";
			$texto_tipo1 = "Interconsultas";
		}
		$htmlresp='';

		$htmlresp.="<table style='text-align: left; width: auto;' border='0' rowspan='2' colspan='1'>
                <tbody>";


        $arr_resp = array();
        $winfo = '';
        $winfo2 =array();
        $winfo3 =array();
        $winfo4 =array();

        foreach($array_profesores as $firusu => $value)
        {

            $wnombre_esp = $value['nombre_especialidad']; //Nombre de la especialidad
            $wcod_esp = $value['cod_esp']; //Codigo de la especialidad
            $wespecialista = $value['nombre_especialista']; //Trae el nombre del especialista
            $wcedula_medico = $value['cedula_medico']; //Trae la cedula del especialista

            //Si el usuario no tiene especialidad, se le asigna SIN_ESPECALIDAD ya que esa es la clave primaria del arreglo, y asi evitar claves vacias.
            if(trim($wcod_esp) == '')
                {
                    $wcod_esp = 'SIN_ESPECIALIDAD';
                    $wnombre_esp = 'SIN ESPECIALIDAD';
                    $wespecialista = traer_nombre_especialista($firusu);
                }

            if(!array_key_exists($wcod_esp, $arr_resp))
                {
                    $arr_resp[$wcod_esp] = array("nombre_esp"=>$wnombre_esp,"especialistas"=>array());
                }

            //Se lee el arreglo y crea un modelo de texto con el nombre de la especialidad, los especialistas y las evoluciones
            $arr_resp[$wcod_esp]["especialistas"][] = array('especialidad'=> $wcod_esp,'especialista'=> $wespecialista, 'cedula_medico'=> $wcedula_medico,'evoluciones'=> $value['cuantos'], 'cantidad'=> count($value['cuantos']));
            $arr_resp[$wcod_esp]["evoluciones"] += count($value['cuantos']);

        }

        $i = 2;
        $wcuantos = 0;

		// echo "<div align=left>";
	    // echo "<pre>";
	    // print_r($arr_resp);
	    // echo "<pre>";
	    // echo "</div>";
        //Se lee el arreglo y crea un modelo de texto con el nombre de la especialidad y los especialistas
        foreach($arr_resp as $key => $value)
        {

            if (is_integer($i / 2))
                $wclass = "fila1";
            else
                $wclass = "fila2";


            //Si hay respuesta en la posicion especialista imprime la linea html.
            if(count($value['especialistas']) > 0)
                {
                    $contadortr++;
					$winfo .= "<tr class=$wclass><td nowrap=nowrap >";//Especialidad
					$winfo .= utf8_encode($value['nombre_esp']);
					$winfo2[$contadortr].="<td nowrap=nowrap align='left'>".utf8_encode($value['nombre_esp'])."</td>";
					$winfo3[$contadortr]=$key;
					$winfo .="</td>";
                }

            $esps = 0;
			$a = array();
            foreach($value['especialistas'] as $keyP => $valueP)
                {
                    if($esps != 0) { $winfo .= "<tr>"; } //Se declara un tr para el inicio de la columan que acompaña a la especialidad




					//Recorro el array de fechas de evoluciones que esta dentro de este arreglo.
					foreach($valueP['evoluciones'] as $clave => $valor){

						if(!array_key_exists($keyP, $a)){
						//$winfo .="<td class=$wclass rowspan='".$valueP['cantidad']."' >".utf8_encode($valueP['especialista'])." <br>(Doc. ".$valueP['cedula_medico'].")</td>";
						}


						//$winfo .= "<td class=$wclass align=center>".$valor."</td>";

						if(!array_key_exists($keyP, $a)){
						$winfo .="<td class=$wclass  >".$valueP['cantidad']."</td>";
						$winfo2[$contadortr].="<td>".$valueP['cantidad']."</td>";
						$a[$keyP] = $keyP;
						}

						$winfo .= "</tr>";
					}

                    //Especialistas y cantidad de evoluciones de cada uno

                    $esps++;

                    //Cuenta cuantas evoluciones hay para luego imprimirlas en el total.
                    $wcuantos = $valueP['evoluciones'];
                }

                $i++;
            }

            //Cuanto esta listo el arreglo se imprime la informacion.
            $htmlresp.= $winfo;

        $htmlresp.= "</table>	";

		$arrayresp = array();
		$arrayresp['html']= $htmlresp;
		$arrayresp['cuantos']= $contadortr;
		for($t=1 ; $t<= count($winfo2) ;$t++)
		{
			$arrayresp['separado'][$t]= $winfo2[$t];
			$arrayresp['especialidad'][$t]= $winfo3[$t];
		}
		return $arrayresp;

    }

 //Devuelve el nonmbre de un usuario en Matrix
    function traer_nombre_especialista($wcodigo)
    {

        global $conex;

        //Nombre del usuario
        $q_usuario = " SELECT descripcion "
                    ."   FROM usuarios "
                    ."  WHERE codigo = '".$wcodigo."'";
        $res_usuario = mysql_query($q_usuario,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_usuario." - ".mysql_error());
        $row_usuario = mysql_fetch_array($res_usuario);
        $wnombre = $row_usuario['descripcion'];

        return $wnombre;

    }

//---------


/**
	ARMA EL TITLE QUE INDICA A QUE PARTE DEL PROCESO LLEGA EL LISTADO EN CASO DE SER DEVUELTO O ENVIADO.
**/
function titlesEnvioDevolucion( &$nivelAnterior, &$nivelSiguiente, $nivelActual, $nivelMinimo, $nivelMaximo, $codigoProceso )
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
	global $wbasedato, $wfachos, $conex, $hoy, $hora, $caracteres, $caracteres2, $wcliame;
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

	$query = "SELECT sopcod codigo, sopnom nombre, delobs observacion, empnom empresa, serdes servicio
			    FROM {$wfachos}_000012
					 INNER JOIN
					 {$wfachos}_000006 on ( sopcod = delsop AND delhis = '{$historia}' AND deling = '{$ingreso}' AND delres = '{$nivelUsuario}' AND delest = 'n')
					 INNER JOIN
					 {$wfachos}_000009 on ( pemcod = delemp )
					 INNER JOIN
					 {$wcliame}_000024 on ( empcod = pememp )
					 INNER JOIN
					 {$wfachos}_000008 on ( sercod = delser )";

	//echo $query;
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

function consultarEntidadesResponsables(){
	global $conex, $wcliame;
	$responsables = array();
	$query = " SELECT Empcod codigo, Empnom nombre
				 FROM {$wcliame}_000024
				WHERE Empest = 'on'";

	$rs    = mysql_query( $query, $conex );
	while( $row = mysql_fetch_assoc( $rs ) ){
		$responsables[$row['codigo']] = $row['nombre'];
	}
	return( $responsables );
}

//Aqui se consulta quien es el profesor que confirma el formulario para un medico residente.
 function consultar_profe_confirma($whis, $wing, $wfecha_registro, $whora_registro, $wformulario, $wposicion)
 {

	global $conex;

	global $wemp_pmla;
	global $wmovhos;

	$wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$whce    = consultarAliasPorAplicacion($conex, $wemp_pmla, 'HCE');

	$wdatos_profesor = array();

	//Consulto en la tabla hce_000068 (".$whce."_".$wformulario.") con la fecha, hora, historia, ingreso y posicion (movcon = '".$wposicion."'"),
	//para extraer el codigo del profesor que firmo el formulario.
	$query =         "SELECT movusu, u.descripcion, espmed.Medesp, nomesp.Espnom, Meddoc "
					."  FROM ".$whce."_".$wformulario." as formulario "
					."INNER JOIN
						usuarios as u on (u.codigo = formulario.movusu )
					  INNER JOIN
					  ".$wmovhos."_000048 as espmed on (espmed.Meduma = formulario.movusu)
					  INNER JOIN
					  ".$wmovhos."_000044 as nomesp on (nomesp.Espcod = SUBSTRING_INDEX(espmed.Medesp, '-', 1))"
					." WHERE movhis = '".$whis."'"
					."  AND moving = '".$wing."'"
					."  AND movpro = '".$wformulario."'"
					."  AND formulario.Fecha_data = '".$wfecha_registro."'"
					."  AND formulario.Hora_data = '".$whora_registro."'"
					."  AND movcon = '".$wposicion."'"; //Esta posicion se refiere a al especialista que confirmo el formulario;
	$res= mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
	$rows = mysql_fetch_array($res);


	$wprofe_firma = $rows['movusu']; //Codigo del profesor que confirma el formulario.
	$wnombre_profe = $rows['descripcion']; //Nombre del profesor.
	$wcodigo_especialidad = $rows['Medesp']; // Codigo de la especialidad.
	$wnombre_especialidad = $rows['Espnom']; // Nombre de la especialidad.
	$wcedula_medico = $rows['Meddoc']; // Nombre de la especialidad.

	$wdatos_profesor = array('codigo_profesor'=>$wprofe_firma,'nombre'=>$wnombre_profe, 'codigo_especialidad'=> $wcodigo_especialidad, 'descrip_especialidad'=> $wnombre_especialidad, 'fecha_firma'=> $wfecha_registro, 'cedula_medico'=> $wcedula_medico );

	return $wdatos_profesor;
 }

/**
	RECIBE LA PETICIÓN DE BUSCAR PACIENTES, LLAMA A LA FUNCION "consultarPacientesActivos" Y GENERA EL HTML QUE PRESENTA EL LISTADO DE PACIENTES CON LISTADOS ACTIVOS O HABILITADOS PARA
	QUE SE LES GENERE LISTADO DE SOPORTES PARA FACTURAR.
**/
if( $peticionAjax == "buscarPacientes" )
{
	$creadas ='no';
	$cerradas='no';
	$sinchequeo='no';
	$pacientes     = array();
	$centrosCostos = array();
	$error         = 0;
	$responsables  = consultarEntidadesResponsables();

	//consultarPacientesActivos();

	//echo print_r($pacientes);
	// if( count($pacientes) > 0 )
	// {
		// ConsultarcentrosCostos();
	// }else
		// {
			// $error = 1;
		// }

	$menu ="<style>
	.fila1
	{
		background-color: #C3D9FF;
		color: #000000;
		font-size: 9pt;
		padding:2px;
	}
	.fila2
	{
		background-color: #E8EEF7;
		color: #000000;
		font-size: 9pt;
		padding:3px;
	}
</style>";

	$menu .="<br><br><div id='operaciones' >
						<ul>
						<li><a href='#tabCreadas' onclick='traerenlista()' >Creadas y Pendientes</a></li>
						<li><a href='#tabSinChequeo' onclick='sinchequeo()'>Sin Chequeo</a></li>
						<li><a href='#tabCerradas' onclick='traerCerradas()'>Cerradas</a></li>
					</ul>";


	$menu .="<div id='tabCreadas'></div>";
	$menu .="<div id='tabSinChequeo'></div>";
	$menu .="<div id='tabCerradas'></div>";

	/*
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
					$colspanEncabezado = "10";
					break;
				case '0':
					$nombreOrigen = "LISTA DE CHEQUEO <b>CREADAS</b> Y PENDIENTES";
					$colspan = "1";
					$colspanEncabezado = "10";
					break;
				case '2':
					$nombreOrigen = "LISTA DE CHEQUEO <b>ENVIADAS</b> CON PENDIENTES";
					$colspan = "2";
					$colspanEncabezado = "11";
					break;
				case '3':
					$nombreOrigen = "LISTA DE CHEQUEO <b>CERRADAS</b>";
					$colspan = "1";
					$colspanEncabezado = "10";
					break;
			}

			$query = "SELECT rolcod codigo, roldes nombre
						FROM {$wfachos}_000015
					   WHERE rolest = 'on'";

			$rs = mysql_query( $query, $conex );

			$option = "<option value='todos'>Todos</option>";
			while( $row = mysql_fetch_array( $rs ) )
			{
				$option.= "<option value='".$row['nombre']."'>".$row['nombre']."</option>";
			}

			if($esAdmin == "on" )
			{
				$menu .= "<br><br><table><tr><td width=65%>&nbsp;</td><td >Rol Responsable</td><td><select id='selectrol' onchange='versolorol()'>".$option."</select></td></tr></table>";
			}
			else
			{

			}

			if($keyOrigen=='0')
			{
				$creadas ='si';
				$menu .="<div id='tabCreadas'>";
			}
			if($keyOrigen=='1')
			{
				$sinchequeo='si';
				$menu .="<div id='tabSinChequeo'>";
			}
			if($keyOrigen=='3')
			{
				$cerradas='si';
				$menu .="<div id='tabCerradas'>";
			}

			$menu .= "<br>";
			//$menu .= "<span class='subtituloPagina2' mostrando='s' style='cursor:pointer;' onclick='ocultarMostrarOrigenPacientes(\"div_{$keyOrigen}\", this )'>{$nombreOrigen}</span>";
			$menu .= "<div id='div_{$keyOrigen}'>";
			$menu .= "<table width='60%' style='border:0;' id='tableresultado'>";

			foreach( $ccos as $keyCco=>$historias )
			{

				$i = 0;
				$menu .= "<tr class='encabezadotabla NoEncuenta' ><td colspan='{$colspanEncabezado}' style='height:30;'>{$keyCco} - {$centrosCostos[$keyCco]['nombre']}</td><tr>";
				$menu .= "<tr class='botona NoEncuenta'><td nowrap='nowrap'>Hab</td><td nowrap='nowrap'>Historia</td><td >Nombre</td><td >Responsable del Ingreso</td><td align='center' nowrap='nowrap'>F. Ingreso</td><td align='center' nowrap='nowrap'>Responsable</td><td align='center' >Tiempo en Etapa</td><td align='center' nowrap='nowrap'>Estado Listado</td><td nowrap='nowrap'>Responsable</td><td colspan='{$colspan}'>&nbsp;</td><tr>";
				foreach( $historias as $keyHistoria => $datos )
				{
					switch( $keyOrigen )
					{
						case '1':
							$accion = "<td align='center' nowrap='nowrap' style='cursor:pointer;' onclick='verSoportes( \"{$keyHistoria}\", \"{$datos['ingreso']}\", \"{$datos['nombre']}\", \"{$datos['tipoDocu']}\", \"{$datos['cedula']}\", \"{$datos['fechaIngreso']}\", \"{$datos['habitacionActual']}\", \"si\"  , \"{$datos['codigoresponsable']}\")'><font color='blue'>CREAR LISTA </font></td>";
							$verFoto = "";
							break;
						case '0':
							$accion = "<td align='center' style='cursor:pointer;' onclick='verSoportes( \"{$keyHistoria}\", \"{$datos['ingreso']}\", \"{$datos['nombre']}\", \"{$datos['tipoDocu']}\", \"{$datos['cedula']}\", \"{$datos['fechaIngreso']}\", \"{$datos['habitacionActual']}\", \"no\" , \"{$datos['codigoresponsable']}\")'><font color='blue'>VER SOPORTES </font></td>";
							$verFoto = "";
							break;
						case '2':
							$accion = "<td align='center' style='cursor:pointer;' onclick='verPendientes( \"{$keyHistoria}\", \"{$datos['ingreso']}\", \"{$datos['nombre']}\", \"{$datos['tipoDocu']}\", \"{$datos['cedula']}\", \"{$datos['fechaIngreso']}\", \"{$datos['habitacionActual']}\", \"no\" , \"{$datos['codigoresponsable']}\" )'><font color='blue'>VER SOPORTES PENDIENTES </font></td>";
							$verFoto = "<td align='center' style='cursor:pointer;' onclick='verFoto( \"{$keyHistoria}\", \"{$datos['ingreso']}\" )'><font color='blue'>VER REGISTRO DE ENVIO</font></td>";
							break;
						case '3':
							$accion = "<td align='center' style='cursor:pointer;' onclick='verSoportes( \"{$keyHistoria}\", \"{$datos['ingreso']}\", \"{$datos['nombre']}\", \"{$datos['tipoDocu']}\", \"{$datos['cedula']}\", \"{$datos['fechaIngreso']}\", \"{$datos['habitacionActual']}\", \"no\" , \"{$datos['codigoresponsable']}\")'><font color='blue'>VER SOPORTES </font></td>";
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
					//$lista ='0';
					// if($keyHistoria=='629810')
					// {
						// $lista = crearListaAutomatica($keyHistoria,$datos['ingreso'], $datos['codigoresponsable'] );
					// }
					$i++;
					( is_int($i/2) ) ? $wclass = 'fila1' : $wclass = 'fila2';
					if( $datos['altaProceso'] == "on" )
						$wclass = 'fondoamarillo';
					$menu .= "<tr class='{$wclass} ".$datos['nivelActual']."' >";
					$menu .= "<td nowrap='nowrap' align='center' style='height:30;'>{$datos['habitacionActual']}</td>";
					$menu .= "<td nowrap='nowrap' align='center' style='height:30;'>{$keyHistoria}-{$datos['ingreso']}</td>";
					$menu .= "<td nowrap='nowrap' >".substr($datos['nombre'], 0, 30)."</td>";
					$menu .= "<td nowrap='nowrap' >".$datos['usuarioadmision']."</td>";
					$menu .= "<td nowrap='nowrap' align='center'>{$datos['fechaIngreso']}</td>";
					$menu .= "<td nowrap='nowrap' >{$datos['responsable']}</td>";
					$tiempoEnEtapa = tiempoEnLaEtapa( $datos['fechaRecibo'], $datos['horaRecibo'] );
					( trim( $tiempoEnEtapa ) == "" ) ? $tiempoEnEtapa = "&nbsp;" : $tiempoEnEtapa;
					$menu .= "<td nowrap='nowrap' align='center'>".$tiempoEnEtapa."</td>";

					$menu .= "<td nowrap='nowrap' align='center'>".$estado."</td>";
					$menu .= "<td nowrap='nowrap' align='center'>".$datos['nivelActual'];
					//--- crear lista a paciente
					$menu .= $lista;
					$menu .= "</td>";
					$menu .= $accion."";
					$menu .= $verFoto;
					$menu .= "<tr>";

				}

				//---


			}
		$menu .= "</table>";

		$menu .= "</div>";


		if($keyOrigen=='0')
		{
			//$creadas ='si';
			$menu .="</div >";
		}
		if($keyOrigen=='1')
		{
			//$sinchequeo='si';
			$menu .="</div >";
		}
		if($keyOrigen=='3')
		{
			//$cerradas='si';
			$menu .="</div>";
		}


	 }
	  if($creadas =='no')
		{
				$menu .="<div id='tabCreadas'>No hay datos</div>";
		}
		if($cerradas =='no')
		{
				$menu .="<div id='tabCerradas'>No hay datos</div>";
		}
		if($sinchequeo =='no')
		{
				$menu .="<div id='tabSinChequeo'>No hay datos</div>";
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
	$menu.="</div>";*/
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
			$listadoActual = "<div class='fila2' >";
			$listadoActual .= "<span >LISTADO ACTUAL DE SOPORTES</span><br><br>";
			$listadoActual .= "<br><br>";
			$listadoActual .= "<span >NO SE HA DEFINIDO NINGUN PLAN PARA LOS RESPONSABLES</span><br><br>";
			$listadoActual .= "</div>";
		}

		$html .= "<table id='tb_maqueta' width='1400'>";
			$html .= "<tr><td id='div_responsables' aling='left' >".$resultados['panelResponsables']."</td>";
			//$html .= "<tr>";
			$html .= "<tr><td colspan=3 ><div  id='div_listadoActual' >".$listadoActual."</div><td><tr>";
		$html .= "</table>";




		$html .="<div  id='div_listadoActual' ></div>";
	}

	$data = array( "maqueta"=>$html, "error"=>$error, "entidadesNoResponsables"=>$resultados['arrayNoResponsables'] );
	echo json_encode( $data );
	return;
}


if( $peticionAjax == "traerCerradas")
{

	global $conex, $wfachos,$wcliame;
	global $hoy, $hora;
	global $movhos;

	$movhos      = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$wcliame        = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
	$fechainiciosoportes        = consultarAliasPorAplicacion($conex, $wemp_pmla, "fechainiciosoportes");

	//vector de roles
	$queryrol = "SELECT  rolcod ,roldes
				   FROM {$wfachos}_000015
				  WHERE rolest='on'";

	$rs = mysql_query( $queryrol, $conex );
	$vecrol = array();
	while( $row = mysql_fetch_array( $rs ) )
	{
		$vecrol[$row['rolcod']]=$row['roldes'];
	}
	/*
	whistoria: 	historiaIngresada,
	wempresa:	empresaSeleccionada,
	wcco: 		centroCostos
	*/
	$query = " SELECT lenhis,lening,lenemp,lenpro,lenran,lenrac,lenobs,lencer ,Ubihac,b.fecha_data, c.Seguridad ,Ingcem , Empnom ,Ubisac, lenfeu fechaRecibo,lenhou horaRecibo , Pacap1 ,Pacap2 ,	Pacno1 ,Pacno2,  	Pactdo, Pacdoc,Empcod,b.ubialp altaProceso
				 FROM {$wfachos}_000011, {$wcliame}_000101 c, {$movhos}_000018 b , {$wcliame}_000024, {$wcliame}_000100
				WHERE lencer = 'on'
				  AND Lenhis = Inghis
				  AND Lening = Ingnin
				  AND Lenhis = Ubihis
				  AND Lening = Ubiing
				  AND Ingcem = Empcod
				  AND Lenhis = Pachis
				  AND Lensal !='on'
				  AND b.fecha_data > '".$fechainiciosoportes."'";


	if($whistoria != '')
	{
		$query .= " AND Lenhis ='".$whistoria."'";
	}

	if($wempresa !='')
	{
		$query .= " AND Empcod ='".$wempresa."'";

	}

	if($wcco !='')
	{
			$query .= " AND  Ubisac='".$wcco."' ";

	}

	if($esAdmin=='on')
	{


	}
	else
	{
		$query .= " AND  lenrac='".$rolusuario."' ";
	}

	$query .=	" ORDER BY Ubisac ";


	$data['query'] = $query;
	$html ="<style>
	.fila1
	{
		background-color: #C3D9FF;
		color: #000000;
		font-size: 9pt;
		padding:2px;
	}
	.fila2
	{
		background-color: #E8EEF7;
		color: #000000;
		font-size: 9pt;
		padding:3px;
	}
	</style>";
	$rs = mysql_query( $query, $conex );
	$html .="<table id='tabla2'><tr class='encabezadoTabla' >
					<td>Hab</td>
					<td>Historia</td>
					<td>Nombre</td>
					<td>Responsable del Ingreso</td>
					<td>F. Ingreso</td>
					<td>Responsable</td>
					<td>Tiempo en Etapa</td>
					<td>Rol</td>
					<td>Pacientes: ReemValorTotal</td>
					</tr>";
	$ColorFila 		= 'fila1';
	$ccoaux ='';
	$auxiliar ='';
	$entro='no';
	$contador = 0;
	$contadortotal = 0;
	while( $row = mysql_fetch_array( $rs ) )
	{
		$entro ='si';
		$auxiliar = tiempoEnLaEtapa ( $row['fechaRecibo'] , $row['horaRecibo']);

		if($ColorFila == 'fila1')
			$ColorFila = 'fila2';
		else
			$ColorFila = 'fila1';

		if($ccoaux != $row['Ubisac'])
		{

			$html = str_replace("ReemplazarValor", $contador, $html);
			$contador = 0;
			$selectcco ="SELECT Cconom FROM {$movhos}_000011 WHERE Ccocod = '".$row['Ubisac']."'";
			$rsrescco   = mysql_query( $selectcco, $conex );
			$rowrescco  = mysql_fetch_array( $rsrescco );
			$html.= "<td style='background-color : #83D8F7;border: 1px solid #999999;font-family: verdana; font-size: 9pt; cursor:pointer;' colspan='8' onclick='AbrirCerrarCerradas( \"{$row['Ubisac']}\")'>".$row['Ubisac']."-".$rowrescco['Cconom']."</td><td style='background-color : #83D8F7;border: 1px solid #999999;font-family: verdana; font-size: 9pt; cursor:pointer;' colspan='1'>Pacientes: ReemplazarValor</td>";
		}
		$contador++;
		$contadortotal++;
		$ccoaux = $row['Ubisac'];
		$Usuarioadmision = $row['Seguridad'];

		$Usuarioadmision = explode("-", $Usuarioadmision);

		$qnomusu 		= "SELECT Descripcion FROM usuarios WHERE Codigo='".$Usuarioadmision[1]."' ";
		$rsres   		= mysql_query( $qnomusu, $conex );
		$rowres  		= mysql_fetch_array( $rsres );
		$nombreusuario 	= $rowres['Descripcion'];
		$nombreusuario 	= str_replace( $caracteres, $caracteres2, $nombreusuario );



		$nombre ='';
		$nombre = $row['Pacno1']." ".$row['Pacno2']." ".$row['Pacap1']." ".$row['Pacap2'];
		$nombre = str_replace( $caracteres, $caracteres2, $nombre );

		$responsable='';
		$responsable=$row['Empnom'];
		$responsable= str_replace( $caracteres, $caracteres2, $responsable );

		$rolresponsable = $vecrol[trim($row['lenrac'])] ;
		$rolresponsable= str_replace( $caracteres, $caracteres2, $rolresponsable );

		if ($row['altaProceso'] == "on" )
		{

			$ColorFila = 'fondoamarillo';
		}
		$html .="<tr class='".$ColorFila." ccoCerradas_".$row['Ubisac']."'   ccoCerradas_".$row['Ubisac']." >
					<td>".$row['Ubihac']."</td>
					<td>".$row['lenhis']."-".$row['lening']."</td>
					<td>".($nombre)."</td>
					<td>".($nombreusuario)."</td>
					<td>".$row['fecha_data']."</td>
					<td>".$responsable."</td>
					<td>".($auxiliar)."</td>
					<td>".$rolresponsable."</td>
					<td align='center' style='cursor:pointer;'  onclick='verSoportes( \"{$row['lenhis']}\", \"{$row['lening']}\", \"{$nombre}\", \"{$row['Pactdo']}\", \"{$row['Pacdoc']}\", \"{$row['fecha_data']}\", \"{$row['Ubihac']}\", \"no\" , \"{$row['Empcod']}\")' ><font color='blue'>VER SOPORTES </font></td>
					</tr>";
	}
	$html = str_replace("ReemValorTotal", $contadortotal, $html);
	$html = str_replace("ReemplazarValor", $contador, $html);

	$html.="</table>";

	if($entro=='no')
	{
		$data["html"] ="No hay datos";
	}
	else
	{
		$data["html"] = $html;
	}

	// if($wcco !='')
	// {
			// $query .= " AND  Ubisac='".$wcco."' ";

	// }


	echo json_encode($data);
	return $data;

}


if( $peticionAjax == "traerenlista")
{

	/*INNER JOIN
							 cliame_000101 as pac on (ubihis = Inghis AND ubiing = Ingnin AND  Ingtpa ='E' ".(($wempresaseleccionada !='') ?  " AND Ingcem ='".$wempresaseleccionada."'" : "" )." )
		*/
	global $conex, $wfachos, $wcliame;
	global $hoy, $hora ,$movhos;


	$movhos      = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$wcliame        = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
	$fechainiciosoportes        = consultarAliasPorAplicacion($conex, $wemp_pmla, "fechainiciosoportes");
	//vector de roles
	$queryrol = "SELECT  rolcod ,roldes
				   FROM {$wfachos}_000015
				  WHERE rolest='on'";

	$rs = mysql_query( $queryrol, $conex );
	$vecrol = array();
	while( $row = mysql_fetch_array( $rs ) )
	{
		$vecrol[$row['rolcod']]=$row['roldes'];
	}
	$query = " SELECT lenhis,lening,lenemp,lenpro,lenran,lenrac,lenobs,lencer ,Ubihac,b.fecha_data, c.Seguridad ,Ingcem , Empnom ,Ubisac, lenfeu fechaRecibo,lenhou horaRecibo , Pacap1 ,Pacap2 ,	Pacno1 ,Pacno2,  	Pactdo, Pacdoc,Empcod,b.ubialp altaProceso ,lmotip
				 FROM {$wcliame}_000101 c, {$movhos}_000018 b , {$wcliame}_000024, {$wcliame}_000100 ,  {$wfachos}_000011 LEFT JOIN {$wfachos}_000020 ON (lmohis=lenhis AND 	lmoing = lening AND lmotip='d')
				WHERE lencer = 'off'
				  AND Lenhis = Inghis
				  AND Lening = Ingnin
				  AND Lenhis = Ubihis
				  AND Lening = Ubiing
				  AND Ingcem = Empcod
				  AND Lenhis = Pachis
				  AND Lensal !='on'
				  AND b.fecha_data > '".$fechainiciosoportes."'";

	if($whistoria != '')
	{
		$query .= " AND Lenhis ='".$whistoria."'";
	}

	if($wempresa !='')
	{
		$query .= " AND Empcod ='".$wempresa."'";

	}

	if($wcco !='')
	{
			$query .= " AND  Ubisac='".$wcco."' ";

	}

	if($esAdmin=='on')
	{
		$puedeliminar 	= consultarAliasPorAplicacion($conex, $wemp_pmla, "administradorsoportesfacturacion");
		// echo $usuario;
		if($puedeliminar == $usuario )
		{
				$condicion ='';
		}
		else
		{

				$condicion ="style='display:none'";
		}

	}
	else
	{
		$condicion ="style='display:none'";
		$query .= " AND  lenrac='".$rolusuario."' ";
	}

	$query .=	" ORDER BY Ubisac  ";

	//echo $query;
	$html ="<style>
	.fila1
	{
		background-color: #C3D9FF;
		color: #000000;
		font-size: 8pt;
		padding:2px;
	}
	.fila2
	{
		background-color: #E8EEF7;
		color: #000000;
		font-size: 8pt;
		padding:3px;
	}
	.fondoAmarillo
	{
		 background-color: #FFFFCC;
		 color: #000000;
		 font-size: 8pt;
	}

	.fondoRosa
	{
		 background-color: #E5D5DF;
		 color: #000000;
		 font-size: 8pt;
	}
	</style>";
	$rs = mysql_query( $query, $conex );
	// $html .="<table id='tabla1'><tr class='encabezadoTabla' >
					// <td>Hab</td>
					// <td>Historia</td>
					// <td>Nombre</td>
					// <td>Responsable del Ingreso</td>
					// <td>F. Ingreso</td>
					// <td>Responsable</td>
					// <td>Tiempo en Etapa</td>
					// <td>Rol</td>
					// <td ".$condicion."></td>
					// <td>Pacientes: ReemValorTotal</td>
					// </tr>";
	$ColorFila 		= 'fila1';
	$ccoaux ='';
	$auxiliar ='';
	$entro='no';
	$i==0;
	$contador = 0;
	$contadortotal = 0;

	$html .= "<table align='right'><tr class='encabezadoTabla'><td>Leyenda</td></tr><tr class='fondoAmarillo'><td>Paciente de Alta</td></tr><tr class='fondoRosa' style='background-color: #E5D5DF; color: #000000; font-size: 8pt'><td>Listado devuelto</td></tr></table><br><br>";
	$html.= "<br><br><table  style='table-layout:fixed'  width='100%' ><tr><td class='fila2'  colspan='9'></td><td class='encabezadoTabla' colspan='1'>Pacientes: ReemValorTotal</td></tr></table>";
	$data_array = array();
	while( $row = mysql_fetch_array( $rs ) )
	{

		if (!array_key_exists($row['lenhis']."-".$row['lening'], $data_array))
		{
			$entro='si';
			$auxiliar = tiempoEnLaEtapa ( $row['fechaRecibo'] , $row['horaRecibo']);

			if($ColorFila == 'fila1')
				$ColorFila = 'fila2';
			else
				$ColorFila = 'fila1';

			if($ccoaux != $row['Ubisac'])
			{
				if($row['Ubisac'] =='')
				{

				}
				else
				{
					$html .="</table>";
				}
				$html = str_replace("ReemplazarValor", $contador, $html);
				$contador = 0;
				$selectcco ="SELECT Cconom FROM {$movhos}_000011 WHERE Ccocod = '".$row['Ubisac']."'";
				$rsrescco   = mysql_query( $selectcco, $conex );
				$rowrescco  = mysql_fetch_array( $rsrescco );
				$html.= "<table  style='table-layout:fixed'  width='100%' ><tr><td style='background-color : #83D8F7;border: 1px solid #999999;font-family: verdana; font-size: 9pt; cursor:pointer;' onclick='AbrirCerrarEnLista( \"{$row['Ubisac']}\")' colspan='9'>".$row['Ubisac']."-".$rowrescco['Cconom']."</td><td style='background-color : #83D8F7;border: 1px solid #999999;font-family: verdana; font-size: 9pt; cursor:pointer;' colspan='1'  onclick='AbrirCerrarEnLista( \"{$row['Ubisac']}\")'>Pacientes: ReemplazarValor</td></tr></table>";

				$html .="<table style='table-layout:fixed' id='tablaenlista_".$row['Ubisac']."' width='90%'><tr class='encabezadoTabla' >
						<td width='60pt'>Hab</td>
						<td width='75pt'>Historia</td>
						<td width='200pt'>Nombre</td>
						<td width='200pt'>Responsable del Ingreso</td>
						<td width='70pt'>F. Ingreso</td>
						<td width='200pt'>Responsable</td>
						<td width='120pt'>Tiempo en Etapa</td>
						<td width='90pt'>Rol</td>
						<td ".$condicion." width='80pt'></td>
						<td width='80pt'></td>
						</tr>";
				}
			$contador++;
			$contadortotal++;
			$ccoaux = $row['Ubisac'];
			$Usuarioadmision = $row['Seguridad'];

			$Usuarioadmision = explode("-", $Usuarioadmision);

			$qnomusu = "SELECT Descripcion FROM usuarios WHERE Codigo='".$Usuarioadmision[1]."' ";
			$rsres   = mysql_query( $qnomusu, $conex );
			$rowres  = mysql_fetch_array( $rsres );
			$nombreusuario = $rowres['Descripcion'];
			$nombreusuario = str_replace( $caracteres, $caracteres2, $nombreusuario );
			//$nombreusuario =  substr($nombreusuario, 0, 15);
			/*
			$accion = "<td align='center' style='cursor:pointer;' onclick='verSoportes( \"{$keyHistoria}\", \"{$datos['ingreso']}\", \"{$datos['nombre']}\", \"{$datos['tipoDocu']}\", \"{$datos['cedula']}\", \"{$datos['fechaIngreso']}\", \"{$datos['habitacionActual']}\", \"no\" , \"{$datos['codigoresponsable']}\")'><font color='blue'>VER SOPORTES </font></td>";
								$verFoto = "";
								break;
			*/
			$nombre ='';
			$nombre = $row['Pacno1']." ".$row['Pacno2']." ".$row['Pacap1']." ".$row['Pacap2'];
			$nombre = str_replace( $caracteres, $caracteres2, $nombre );

			$responsable='';
			$responsable=$row['Empnom'];
			$responsable= str_replace( $caracteres, $caracteres2, $responsable );

			$rolresponsable = $vecrol[trim($row['lenrac'])] ;
			$rolresponsable = str_replace( $caracteres, $caracteres2, $rolresponsable );
			$i++;



			$existenivelsuperior= 0;
			$nivelhastadondellego ="ninguno";
			$devuelto ='';
			$selectdevolucion ='';
			$roldevuelto='';
			if($row['lmotip']=='d')
			{

				$selectdevolucion = "SELECT lmoori FROM {$wfachos}_000020
									  WHERE lmohis='".$row['lenhis']."'
										AND lmoing = '".$row['lening']."'
										AND lmotip ='d' ";

				$resdevolucion = mysql_query( $selectdevolucion, $conex );

				while( $rowdevolucion = mysql_fetch_array( $resdevolucion ) )
				{
					$roldevuelto = $rowdevolucion['lmoori'] *1;
				}

				/*$selectdevolucion = "SELECT lmodes FROM {$wfachos}_000020
									  WHERE lmohis='".$row['lenhis']."'
										AND lmohis = '".$row['lening']."'
										AND lmotip ='e'
										AND lmodes*1 >= '".($rolusuario*1)."'";

				$resdevolucion = mysql_query( $selectdevolucion, $conex );

				while( $rowdevolucion = mysql_fetch_array( $resdevolucion ) )
				{
				   $existenivelsuperior = 1;
				   $nivelhastadondellego = $rowdevolucion['lmodes'];
				   $ColorFila = 'fondoRosa';
				}*/

				if(($roldevuelto*1) > ($rolusuario*1))
				{
					$ColorFila = 'fondoRosa';

				}

				$devuelto ='si';

			}

			if ($row['altaProceso'] == "on"  AND $ColorFila!='fondoRosa')
			{

				$ColorFila = 'fondoamarillo';
			}
			$html .="<tr  class='".$ColorFila." ccoEnLista_".$row['Ubisac']."' id='trcreadas_".$i."'  ccoEnLista_".$row['Ubisac']." >
						<td>    ".$row['Ubihac']." </td>
						<td>".$row['lenhis']."-".$row['lening']."</td>
						<td>".$nombre."</td>
						<td>".$nombreusuario."</td>
						<td>".$row['fecha_data']."</td>
						<td>".$responsable."</td>
						<td>".$auxiliar."</td>
						<td>".$rolresponsable."</td>


						<td align='center' style='cursor:pointer;'  onclick='verSoportes( \"{$row['lenhis']}\", \"{$row['lening']}\", \"{$nombre}\", \"{$row['Pactdo']}\", \"{$row['Pacdoc']}\", \"{$row['fecha_data']}\", \"{$row['Ubihac']}\", \"no\" , \"{$row['Empcod']}\")' ><font color='blue'>VER SOPORTES </font></td>
						<td ".$condicion." ><input type='button' style='cursor:pointer;' value='Eliminar' onclick='SacardeLista( \"{$row['lenhis']}\", \"{$row['lening']}\" ,\"{$i}\")'></td>

						</tr>";
		  }
		  $data_array[$row['lenhis']."-".$row['lening']] ='s';
	}
	$html = str_replace("ReemValorTotal", $contadortotal, $html);
	$html = str_replace("ReemplazarValor", $contador, $html);

	$html.="</table>";

	if($entro=='no')
	{
		$data["html"] ="No hay datos";
	}
	else
	{
		$data["html"] = $html;
	}

	echo json_encode($data);
	return $data;
}


if( $peticionAjax == "sinchequeo")
{


	global $conex, $wfachos,$wcliame;
	global $hoy, $hora;

	$movhos      = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$wcliame        = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
	$fechainiciosoportes        = consultarAliasPorAplicacion($conex, $wemp_pmla, "fechainiciosoportes");
	$sigo='no';

	$Select = "SELECT Empcod , Empfec , Empest
				 FROM ".$wfachos."_000027
			    WHERE Empest = 'on'";

	$resp 	 = mysql_query( $Select, $conex );
	$vecresp = array();
	while( $rowres = mysql_fetch_array( $resp ) )
	{
		$vecresp[$rowres['Empcod']]=$rowres['Empfec'];
	}




	if($esAdmin =='on' or $rolusuario=='01' )
	{
		$sigo='si';
	}
	else
	{
		$data["html"] ="No hay datos";
		echo json_encode($data);
		return $data;
	}
	//vector de roles
	$queryrol = "SELECT  rolcod ,roldes
				   FROM {$wfachos}_000015
				  WHERE rolest='on'";



	$rs = mysql_query( $queryrol, $conex );
	$vecrol = array();
	while( $row = mysql_fetch_array( $rs ) )
	{
		$vecrol[$row['rolcod']]=$row['roldes'];
	}




	$query = " SELECT Inghis,Ubiing, Ubihac,b.fecha_data, c.Seguridad ,Ingcem , Empnom ,Ubisac, Pacap1 ,Pacap2 ,	Pacno1 ,Pacno2,  	Pactdo, Pacdoc,Empcod,b.ubialp altaProceso
				 FROM {$wcliame}_000101 c  LEFT JOIN {$wfachos}_000011  ON (Inghis=Lenhis AND Ingnin=Lening ), {$movhos}_000018 b , {$wcliame}_000024, {$wcliame}_000100
				WHERE Inghis = Ubihis
				  AND Ingnin = Ubiing
				  AND Ingcem = Empcod
				  AND Inghis = Pachis
				  AND b.fecha_data > '".$fechainiciosoportes."'
				  AND lenhis IS NULL";

	if($whistoria != '')
	{
		$query .= " AND Inghis ='".$whistoria."'";
	}

	if($wempresa !='')
	{
		$query .= " AND Empcod ='".$wempresa."'";

	}

	if($wcco !='')
	{
		$query .= " AND  Ubisac='".$wcco."' ";

	}

	$query .=	" ORDER BY Ubisac ";

	$rs = mysql_query( $query, $conex );
	$html ="<style>
	.fila1
	{
		background-color: #C3D9FF;
		color: #000000;
		font-size: 9pt;
		padding:2px;
	}
	.fila2
	{
		background-color: #E8EEF7;
		color: #000000;
		font-size: 9pt;
		padding:3px;
	}
	</style>";
	$html .="<table id='tabla3'><tr class='encabezadoTabla' >
					<td>Hab</td>
					<td>Historia</td>
					<td>Nombre</td>
					<td>Responsable del Ingreso</td>
					<td>F. Ingreso</td>
					<td>Responsable</td>
					<td>Pacientes: ReemValorTotal</td>
					</tr>";
	$ColorFila 		= 'fila1';
	$ccoaux ='';
	$auxiliar ='';
	$entro='no';
	$contador = 0;
	$contadortotal = 0;
	while( $row = mysql_fetch_array( $rs ) )
	{

		$entro='si';
		if($ColorFila == 'fila1')
			$ColorFila = 'fila2';
		else
			$ColorFila = 'fila1';



		if($ccoaux != $row['Ubisac'])
		{
			if($contador==0)
			{
				$html = str_replace("nuevapropiedad", "display:none", $html);
			}
			else
			{
				//$html = str_replace("nuevapropiedad", "display:block", $html);
			}

			$html = str_replace("ReemplazarValor", $contador, $html);

			$contador = 0;
			$selectcco ="SELECT Cconom FROM {$movhos}_000011 WHERE Ccocod = '".$row['Ubisac']."'";
			$rsrescco   = mysql_query( $selectcco, $conex );
			$rowrescco  = mysql_fetch_array( $rsrescco );
			$html.= "<td  style='background-color : #83D8F7;border: 1px solid #999999;font-family: verdana; font-size: 9pt; cursor:pointer; nuevapropiedad' colspan='6' onclick='AbrirCerrarSinChequeo( \"{$row['Ubisac']}\")' colspan='7'>".$row['Ubisac']."-".$rowrescco['Cconom']."</td><td style='background-color : #83D8F7;border: 1px solid #999999;font-family: verdana; font-size: 9pt; cursor:pointer; nuevapropiedad'>Pacientes: ReemplazarValor</td>";
		}


		$ccoaux = $row['Ubisac'];
		$Usuarioadmision = $row['Seguridad'];

		$Usuarioadmision = explode("-", $Usuarioadmision);

		$qnomusu = "SELECT Descripcion FROM usuarios WHERE Codigo='".$Usuarioadmision[1]."' ";
		$rsres   = mysql_query( $qnomusu, $conex );
		$rowres  = mysql_fetch_array( $rsres );
		$nombreusuario = $rowres['Descripcion'];
		$nombreusuario = str_replace( $caracteres, $caracteres2, $nombreusuario );


		$nombre ='';
		$nombre = $row['Pacno1']." ".$row['Pacno2']." ".$row['Pacap1']." ".$row['Pacap2'];
		$nombre = str_replace( $caracteres, $caracteres2, $nombre );

		$responsable='';
		$responsable=$row['Empnom'];
		$responsable= str_replace( $caracteres, $caracteres2, $responsable );


		if ($row['altaProceso'] == "on" )
		{

			$ColorFila = 'fondoamarillo';
		}


		if(!$vecresp[$row['Empcod']] OR ( $vecresp[$row['Empcod']] >= $row['fecha_data'] ) )
		{
			$contador++;
			$contadortotal++;
			$html .="<tr class='".$ColorFila." ccoSinChequeo_".$row['Ubisac']."' ccoSinChequeo_".$row['Ubisac']." >
						<td>".$row['Ubihac']."</td>
						<td>".$row['Inghis']."-".$row['Ubiing']."</td>
						<td>".($nombre)."</td>
						<td>".($nombreusuario)."</td>
						<td>".$row['fecha_data']."</td>
						<td>".$responsable."</td>
						<td align='center' style='cursor:pointer;'  onclick='verSoportes( \"{$row['Inghis']}\", \"{$row['Ubiing']}\", \"{$nombre}\", \"{$row['Pactdo']}\", \"{$row['Pacdoc']}\", \"{$row['fecha_data']}\", \"{$row['Ubihac']}\", \"si\" , \"{$row['Empcod']}\")' ><font color='blue'>CREAR LISTA</font></td>
						</tr>";
		}
	}
	$html = str_replace("ReemValorTotal", $contadortotal, $html);

	if($contador==0)
	{
		$html = str_replace("nuevapropiedad", "display:none", $html);
	}
	else
	{
		//$html = str_replace("nuevapropiedad", "display:block", $html);
	}


	$html = str_replace("ReemplazarValor", $contador, $html);

	$html.="</table>";
	if($entro=='no')
	{
		$data["html"] ="No hay datos";
	}
	else
	{
		$data["html"] = $html;
	}

	echo json_encode($data);
	return $data;
}

/**
	RECIBE LA PETICIÓN DE GENERAR LISTADO ACTUAL, LLAMA A LA FUNCION "generarListadoActual" LA CUAL ES LA MAS IMPORTANTE
**/
if( $peticionAjax == "generarListadoActual")
{
	$error   = 0;
	$estadoListado = "";
	$listado = generarListadoActual();
	//echo "edb-88888888888->".$listado;
	$data    = array( "lista"=>$listado, "error"=>$error, "cerrado"=>$estadoListado );
	echo json_encode($data);
	return;
}

if( $peticionAjax == "procesosxempresa")
{
	$error   = 0;

	global $conex, $wfachos;
	 $query = " SELECT DISTINCT procod codigo, pronom nombre, {$wfachos}_000014.id
				 FROM {$wfachos}_000014,{$wfachos}_000019
				WHERE proest = 'on'
				  AND serpro = procod
				  AND seremp  = '".$wempresa."'
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
	$listado = $tabla;
	//$listado = $query;
	$data    = array( "lista"=>$listado, "error"=>$error );
	echo json_encode($data);
	return;
}


if( $peticionAjax == "SacardeLista")
{


	global $conex, $wfachos;
	/*$query = " SELECT DISTINCT procod codigo, pronom nombre, {$wfachos}_000014.id
				 FROM {$wfachos}_000014,{$wfachos}_000019
				WHERE proest = 'on'
				  AND serpro = procod
				  AND seremp  = '".$wempresa."'
				ORDER BY id";
	$rs	   = mysql_query( $query, $conex );*/

	$query = " UPDATE {$wfachos}_000011
				  SET lensal = 'on'
				WHERE lenhis = '{$whis}'
				  AND lening = '{$wing}'";

	$rs = mysql_query( $query, $conex ) or die( mysql_error() );

	$data ['sql']=$query;
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


	  $soporte = explode('_' , $soporte);
	  if(count($soporte)> 1)
	  {

		  $query = "UPDATE {$wfachos}_000012
				 SET delest = '{$westado}'{$deshabilitarEstado}
			   WHERE delhis = '{$whis}'
				 AND deling = '{$wing}'
				 AND delsop = '".$soporte[0]."'
				 AND delemp = '{$wempPlan}'
				 AND delser = '{$servicio}'";
		  $rs = mysql_query( $query, $conex );
	  }
	//echo $query;
	return ;
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


if($peticionAjax == "Traerroles")
{
	$query = " SELECT rolcod 	, roldes
				 FROM {$wfachos}_000015";

	$rs = mysql_query( $query, $conex ) or die ( mysql_error() ) ;
	$i = 0;
	$html .="<center><div><select><option value=''>... Seleccione</option>";
	while( $row = mysql_fetch_array( $rs ) )
	{
		$html .="<option value='".$row['rolcod']."'>".$row['roldes']."</option>";
	}
	$html .="</select></div></center>";
	$error ='no';
	$data = array( "error"=>$error, "html"=>$html );

	echo json_encode($data);

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
	$txtFormateado = strtoupper( $wobservacion )."-->".$pie;

	$query = " UPDATE {$wfachos}_000012
				  SET delobs = CONCAT('{$txtFormateado}', '$' ,delobs )
				WHERE delhis = '{$whis}'
				  AND deling = '{$wing}'
				  AND delsop = '{$wsoporte}'
				  AND delemp = '{$wempPlan}'
				  AND delser = '{$wservicio}'";
	//$rs = mysql_query( $query, $conex );
	if( $rs = mysql_query( $query, $conex ) or die( mysql_error() ) )
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
			height:20px;
			margin-left: 1%;
			cursor: pointer;
		 }

		 .botonab{
			font-size:13px;
			font-family:Verdana,Helvetica;
			font-weight:bold;
			color:white;
			background:#638cb5;
			border:0px;
			width:180px;
			height:20px;
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
<!-- <script type='text/javascript' src='../../../include/root/jquery-1.3.2.js'></script>
<script type='text/javascript' src='../../../include/root/jquery-1.3.2.js'></script>-->
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
<link rel="stylesheet" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" />
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
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
	var relojTemp;


	$(document).ready(function(){
		wemp_pmla 	   = $("#wemp_pmla").val();
		wbd 	  	   = $("#wbasedato").val();
		wcliame  	   = $("#wcliame").val();
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
		//$("#btn_consultar").click(buscarPacientes());

		titulos = new Array();
		titulos[0] = "";     //TITULO PARA LA PARTE EN QUE SE PRESENTAN LOS LISTADOS PENDIENTES
		//titulos[0] = "<span class='subtituloPagina2'><b>PACIENTES CON REVISIÓN DE SOPORTES PENDIENTES</b></span>";     //TITULO PARA LA PARTE EN QUE SE PRESENTAN LOS LISTADOS PENDIENTES
		titulos[1] = "<span class='subtituloPagina2'><b>VERIFICACIÓN DE SOPORTES EN LISTA DE CHEQUEO</b></span>";			   //TITULO PARA LA PARTE EN QUE SE MUESTRA EL ESTADO DE UN LISTADO( FUNCIONALIDAD PRINCIPAL )
		titulos[2] = "<span class='subtituloPagina2'><b>HISTORICO DE LISTA DE CHEQUEO ENVIADA</b></span>"; 			           //TITULO PARA LA PARTE EN QUE SE MUESTRA EL REGISTRO DE ENVIO DE UN LISTADO.
		titulos[3] = "<span class='subtituloPagina2'><b>LISTA DE SOPORTES PENDIENTES EN LISTA DE CHEQUEO ENVIADA</b></span>"; //TITULO PARA LA PARTE EN QUE SE MUESTRA EL lISTADO DE SOPORTES QUE SE QUEDARON DEBIENDO EN UN LISTADO.

		//$("#div_titulos").html( titulos[0] );

		// --> Activar tabs jaquery
		$( "#operaciones" ).tabs({
			heightStyle: "content"
		});


		crear_autocomplete('hidden_entidades', 'SI', 'busc_empresa', 'CambiarResponsable');
		crear_autocomplete('hidden_cco', 'SI', 'busc_cco', 'CambiarResponsable');
		init();

	});
	function ocultarinfopaciente(){
		$("#tbl_responsables").toggle();

	}

	function ocultarinfoproceso(){
		$("#tableinfoproceso").toggle();

	}

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

		/*var tab = $("#tabsMonitorAuditoria").find("li[class*=ui-state-active]").attr("id");
		$("#tdBotonActualizar").html("");
		if(tab == "liProSinAuditar")
			$("#tdBotonActualizar").html('&nbsp;&nbsp;<b>|</b>&nbsp;&nbsp;<span style="cursor:pointer" onclick="pintarProcedimientosSinAuditar()">Actualizar&nbsp;<img width="14px" height="14px" src="../../images/medical/sgc/Refresh-128.png" title="Actualizar listado."></span>');
		else
		*/
		$("#tdBotonActualizar").html('&nbsp;&nbsp;<b>|</b>&nbsp;&nbsp;<span style="cursor:pointer" onclick="buscarPacientes()">Actualizar&nbsp;<img width="14px" height="14px" src="../../images/medical/sgc/Refresh-128.png" title="Actualizar listado."></span>');


		}

	function AbrirCerrarCerradas(cco)
	{
		//$("#tabla2").find(".ccoCerradas_"+cco).toggle();
		$("#tabla2 tr[ccoCerradas_"+cco+"]").hide();
		if($("#tabla2 tr[ccoCerradas_"+cco+"]").find(':visible').length >0)
		{
			$("#tabla2 tr[ccoCerradas_"+cco+"]").hide();
		}
		else
		{
			$("#tabla2 tr[ccoCerradas_"+cco+"]").hide();

		}

	}

	function AbrirCerrarEnLista(cco)
	{
		//$("#tabla1").find(".ccoEnLista_"+cco).toggle();
		//$("#tabla1 tr[ccoEnLista_"+cco+"]").hide();
		$("#tablaenlista_"+cco).toggle();

	}

	function AbrirCerrarSinChequeo(cco)
	{
		//$("#tabla3 tr[ccoSinChequeo_"+cco+"]").hide();
		if($("#tabla3 tr[ccoSinChequeo_"+cco+"]").find(':visible').length >0)
		{
			$("#tabla3 tr[ccoSinChequeo_"+cco+"]").hide();
		}
		else
		{
			$("#tabla3 tr[ccoSinChequeo_"+cco+"]").show();
		}


	}

	function traerCerradas()
	{
		historiaIngresada   = $("#whis_ingresada").val();
		empresaSeleccionada = $("#busc_empresa").attr("valor");
		centroCostos = $("#busc_cco").attr("valor");
		esAdmin		 = $("#esAdmin").val();
		rolusuario   = $("#wNivelUsuario").val()

		//alert(rolusuario);
		$("#tabCerradas").html("<div align=center><img id='img_esperar_plan' src='../../images/medical/ajax-loader7.gif' name='mg_esperar_plan'></div>");

		//alert("historia"+historiaIngresada+"empresaSeleccionada"+empresaSeleccionada+"centrocostos"+centroCostos);
		$.ajax({
		url: "MonitorSoportesFacturacion.php",
		type: "POST",
		data: {
				peticionAjax: "traerCerradas",
				wemp_pmla: $('#wemp_pmla').val(),
				wfachos: wfachos,
				whistoria: 	historiaIngresada,
				wempresa:	empresaSeleccionada,
				wcco: 		centroCostos,
				esAdmin:    esAdmin,
				rolusuario: $("#wNivelUsuario").val()

			  },
		success: function(data)
		{
			activarRelojTemporizador();
			$("#tabCerradas").html("<b>"+data.html+"</b>");
			//alert(data.query);
		},
		dataType: "json"
		});
	}


	function traerenlista()
	{
		historiaIngresada   = $("#whis_ingresada").val();
		empresaSeleccionada = $("#busc_empresa").attr("valor");
		centroCostos = $("#busc_cco").attr("valor");
		esAdmin		 = $("#esAdmin").val();
		rolusuario   = $("#wNivelUsuario").val()

		//alert(rolusuario);
		//alert("historia"+historiaIngresada+"empresaSeleccionada"+empresaSeleccionada+"centrocostos"+centroCostos);
		$("#tabCreadas").html("<div align=center><img id='img_esperar_plan' src='../../images/medical/ajax-loader7.gif' name='mg_esperar_plan'></div>");

		$.ajax({
		url: "MonitorSoportesFacturacion.php",
		type: "POST",
		data: {
				peticionAjax: "traerenlista",
				wemp_pmla: 	$('#wemp_pmla').val(),
				wfachos: 	wfachos,
				whistoria: 	historiaIngresada,
				wempresa:	empresaSeleccionada,
				wcco: 		centroCostos,
				esAdmin:    esAdmin,
				rolusuario: $("#wNivelUsuario").val(),
				usuario   : $("#wusuario").val()

			  },
		success: function(data)
		{
			activarRelojTemporizador();
			$("#tabCreadas").html("<b>"+data.html+"</b>");
		},
		dataType: "json"
		});
	}



	function sinchequeo()
	{
		historiaIngresada   = $("#whis_ingresada").val();
		empresaSeleccionada = $("#busc_empresa").attr("valor");
		centroCostos = $("#busc_cco").attr("valor");
		esAdmin		 = $("#esAdmin").val();
		rolusuario   = $("#wNivelUsuario").val()

		//alert(rolusuario);
		//alert("historia"+historiaIngresada+"empresaSeleccionada"+empresaSeleccionada+"centrocostos"+centroCostos);
		$("#tabSinChequeo").html("<div align=center><img id='img_esperar_plan' src='../../images/medical/ajax-loader7.gif' name='mg_esperar_plan'></div>");

		$.ajax({
		url: "MonitorSoportesFacturacion.php",
		type: "POST",
		data: {
				peticionAjax: "sinchequeo",
				wemp_pmla: 	$('#wemp_pmla').val(),
				wfachos: 	wfachos,
				whistoria: 	historiaIngresada,
				wempresa:	empresaSeleccionada,
				wcco: 		centroCostos,
				esAdmin:    esAdmin,
				rolusuario: $("#wNivelUsuario").val()


			  },
		success: function(data)
		{
			activarRelojTemporizador();
			$("#tabSinChequeo").html("<b>"+data.html+"</b>");
		},
		dataType: "json"
		});
	}

	function cambiarRol()
	{

		$.ajax({
		url: "MonitorSoportesFacturacion.php",
		type: "POST",
		data: {
				peticionAjax: "Traerroles",
				wemp_pmla: $('#wemp_pmla').val(),
				wfachos: wfachos

			  },
		success: function(data)
		{
			//alert("hola");
			$( "#divCambioRol" ).html(data.html);
		},
		dataType: "json"
		});
		//$("#ocultoCodigoUsuario").val();


		$( "#divCambioRol" ).dialog({

		height: 200,
		width:  400,
		modal: true,
		title: "Cambio de Rol",
		buttons: {

					cerrar: function() { //cancel
					$( this ).dialog( "close" );
				}
			}

		});






	}






	function crear_autocomplete(HiddenArray, TipoHidden, CampoCargar, AccionSelect, CampoProcedimiento)
	{
		if(TipoHidden == 'SI')
			var ArrayValores  = eval('(' + $('#'+HiddenArray).val() + ')');
		else
			var ArrayValores  = eval('(' + HiddenArray + ')');

		var ArraySource   = new Array();
		var index		  = -1;
		for (var CodVal in ArrayValores)
		{
			index++;
			ArraySource[index] = {};
			ArraySource[index].value  = CodVal;
			ArraySource[index].label  = CodVal+'-'+ArrayValores[CodVal];
			ArraySource[index].name   = ArrayValores[CodVal];
		}

		CampoCargar = CampoCargar.split('|');
		$.each( CampoCargar, function(key, value){
			$( "#"+value ).autocomplete({
				minLength: 	0,
				source: 	ArraySource,
				select: 	function( event, ui ){
					$( "#"+value ).val(ui.item.label);
					$( "#"+value ).attr('valor', ui.item.value);
					$( "#"+value ).attr('nombre', ui.item.name);
					switch(AccionSelect)
					{


					}
					return false;
				}
			});


		});
	}


	function init(){
            $('#busc_empresa').on({
                focusout: function(e) {
					// alert("entro");
                    if($(this).val().replace(/ /gi, "") == '')
                    {
                        $(this).val("");
                        $(this).attr("valor","");
                        $(this).attr("nombre","");
                    }
                    else
                    {
                        $(this).val($(this).attr("nombre"));
                    }
                }
            });

			$('#busc_cco').on({
                focusout: function(e) {
					// alert("entro");
                    if($(this).val().replace(/ /gi, "") == '')
                    {
                        $(this).val("");
                        $(this).attr("valor","");
                        $(this).attr("nombre","");
                    }
                    else
                    {
                        $(this).val($(this).attr("nombre"));
                    }
                }
            });


    }

	function versolorol (){

		//alert($("#selectrol").val());

		var rol = $("#selectrol").val();

		if(rol=='todos')
		{
			$("#tableresultado tr").each(function() {
				$(this).show();
			});
		}
		else
		{

			$("#tableresultado tr").each(function() {
				//alert("entro1");
				if($(this).hasClass(rol)) {
					$(this).show();
				} else {
					if($(this).hasClass('NoEncuenta'))
					{

					}
					else
					{
						$(this).hide();
					}
					//alert("nopentro");
					//bunch of other things
				}
			});

		}

	}

	function Limpiar()
	{

		$("#input_cco").val('');
		$("#busc_empresa").val('');
		$("#busc_empresa").attr('valor','');
		$("#busc_empresa").attr('nombre','');
		$("#busc_cco").val('');
		$("#busc_cco").attr('valor','');
		$("#busc_cco").attr('nombre','');
		$("#whis_ingresada").val('');


	}

	function crearListaAutomatica(whistoria , wingreso , wflujo='' , wplan='')
	{

		$.ajax({
			url: "MonitorSoportesFacturacion.php",
			type: "POST",
			data: {
				 peticionAjax: "crearListaAutomatica",
					wemp_pmla: $('#wemp_pmla').val(),
					whistoria: whistoria,
					wingreso : wingreso,
					wflujo   : wflujo,
					wplan    : wplan
				  },
			success: function(data)
			{
				if(data.html == 'abrirModal' )
				{
					$("#divPlanyFlujo").html(data.contenidoDiv).show().dialog({
							dialogClass: 'fixed-dialog',
							modal: true,
							title: "<div align='center' style='font-size:10pt'>Seleccion de Flujo y Plan</div>",
							width: "auto",
							height: "300"
						});

				}

				if(data.exito == 'si')
				{
					$("#divexito").html('');
					$("#divexito").html(data.mensajeexitoso);
				}
			},
			dataType: "json"
		});


	}

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
			//alert("entro");
			crearListaAutomatica($("#historiamodal").val(), $("#ingresomodal").val(), flujo , plan );
		}


	}

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


		historiaIngresada   = $("#whis_ingresada").val();
		empresaSeleccionada = $("#busc_empresa").attr("valor");
		centroCostos = $("#busc_cco").attr("valor");
		esAdmin		 = $("#esAdmin").val();

		rolusuario   = $("#wNivelUsuario").val();

		//alert(data.html);
		var menu ="<br><br><div id='operaciones' ><ul><li><a href='#tabCreadas' onclick='traerenlista()' >Creadas y Pendientes</a></li><li><a href='#tabSinChequeo' onclick='sinchequeo()'>Sin Chequeo</a></li><li><a href='#tabCerradas' onclick='traerCerradas()'>Cerradas</a></li><li width='60%'>";
			menu =menu+"<table width='100%' style='padding:6px;font-family: verdana;font-size: 10pt;color: #4C4C4C'>";
			menu =menu+"<tr><td style='font-weight:normal;'>Ultima actualización:&nbsp;<span id='relojTemp' cincoMinTem='86400000'></span>&nbsp;<img width='15px' height='15px' src='../../images/medical/sgc/Clock-32.png'>";
			menu =menu+"</td><td style='font-weight:normal;' align='center' id='tdBotonActualizar'></td></tr></table></li></ul>";


		menu =menu+"<div id='tabCreadas'></div>";
		menu =menu+"<div id='tabSinChequeo'></div>";
		menu =menu+"<div id='tabCerradas'></div>";
		$("#div_menuPacientes").html(menu);
		// --> Activar tabs jaquery
		$( "#operaciones" ).tabs({
			heightStyle: "content"
		});
		$("#tabCreadas").html("<div align=center><img id='img_esperar_plan' src='../../images/medical/ajax-loader7.gif' name='mg_esperar_plan'></div>");

		if (typeof empresaSeleccionada === "undefined") {
			empresaSeleccionada = '';
		}

		//alert(centroCostos);

		//alert("historia"+historiaIngresada+"empresaSeleccionada"+empresaSeleccionada+"centrocostos"+centroCostos);
		$.ajax({
		url: "MonitorSoportesFacturacion.php",
		type: "POST",
		data: {
				peticionAjax: "traerenlista",
				wemp_pmla: $('#wemp_pmla').val(),
				wfachos: wfachos,
				whistoria: 	historiaIngresada,
				wempresa:	empresaSeleccionada,
				wcco: 		centroCostos,
				esAdmin:    esAdmin,
				rolusuario: rolusuario

			  },
		success: function(data)
		{


			activarRelojTemporizador();
			if(data.html !='No hay datos' )
			{

				$("#tabCreadas").html("<b>"+data.html+"</b>");
				var $tabs = $('#operaciones').tabs();
				$tabs.tabs('option', 'selected',0);


			}
			else
			{
					//
					historiaIngresada   = $("#whis_ingresada").val();
					empresaSeleccionada = $("#busc_empresa").attr("valor");
					centroCostos = $("#busc_cco").attr("valor");
					esAdmin		 = $("#esAdmin").val();

					if (typeof empresaSeleccionada === "undefined") {
						empresaSeleccionada = '';
					}
					//alert("historia"+historiaIngresada+"empresaSeleccionada"+empresaSeleccionada+"centrocostos"+centroCostos);

					$.ajax({
					url: "MonitorSoportesFacturacion.php",
					type: "POST",
					data: {
							peticionAjax: "traerCerradas",
							wemp_pmla: 	$('#wemp_pmla').val(),
							wfachos: 	wfachos,
							whistoria: 	historiaIngresada,
							wempresa:	empresaSeleccionada,
							wcco: 		centroCostos,
							esAdmin:    esAdmin,
							rolusuario: $("#wNivelUsuario").val()

						  },
					success: function(data)
					{
						if(data.html !='No hay datos' )
						{
							$("#tabCerradas").html("<b>"+data.html+"</b>");
							var $tabs = $('#operaciones').tabs();
							$tabs.tabs('option', 'selected',2);
						}
						else
						{
							historiaIngresada   = $("#whis_ingresada").val();
							empresaSeleccionada = $("#busc_empresa").attr("valor");
							centroCostos = $("#busc_cco").attr("valor");
							esAdmin		 = $("#esAdmin").val();
							if (typeof empresaSeleccionada === "undefined") {
								empresaSeleccionada = '';
							}
							//alert("historia"+historiaIngresada+"empresaSeleccionada"+empresaSeleccionada+"centrocostos"+centroCostos);

							$.ajax({
							url: "MonitorSoportesFacturacion.php",
							type: "POST",
							data: {
									peticionAjax: "sinchequeo",
									wemp_pmla: 	$('#wemp_pmla').val(),
									wfachos: 	wfachos,
									whistoria: 	historiaIngresada,
									wempresa:	empresaSeleccionada,
									wcco: 		centroCostos,
									esAdmin:    esAdmin,
									rolusuario: $("#wNivelUsuario").val()


								  },
							success: function(data)
							{

								$("#tabSinChequeo").html("<b>"+data.html+"</b>");
								var $tabs = $('#operaciones').tabs();
								$tabs.tabs('option', 'selected',1);
							},
							dataType: "json"
							});
						}

					},
					dataType: "json"
					});


					//
			}
		},
		dataType: "json"
		});



		/*



		// $( ".viewport-bottom" ).hide();

		$("#div_menuPacientes").html("<br><br><br>");
		$.ajax({
			    url: "MonitorSoportesFacturacion.php",
				type: "POST",
				data: {
					 peticionAjax: "buscarPacientes",
						wbasedato: wbd,
						  wcliame: wcliame,
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
							 whis: historiaIngresada,
			 wempresaseleccionada: empresaSeleccionada
					  },
				success: function(data)
				{
					$("#div_menuPacientes").html(data.pacientes);
						// --> Activar tabs jaquery
						$( "#operaciones" ).tabs({
							heightStyle: "content"
						});

						alert($("#tabCreadas").html());
						//alert($("#tabCreadas").html());




				},
				dataType: "json"
		});
		return;

		*/
	}
	function SacardeLista (historia,ingreso,i)
	{
		$.ajax({

				 url: "MonitorSoportesFacturacion.php",
				type: "POST",
			   async: false,
				data: {
					   peticionAjax: "SacardeLista",
					   wemp_pmla : $('#wemp_pmla').val(),
						  wfachos: wfachos,
						  whis: historia,
						  wing:	ingreso

					  },
				success: function(data)
				{
					$("#trcreadas_"+i).remove();//alert(data.sql);

				},
				dataType: "json"
			});



	}
	function verSoportes( historia, ingreso, nombre, tipoDocu, cedula, fechaIngreso, habitacionActual, nuevo ,responsable)//recopila los parámetros y los asigna a variables que serán parámetros para generar los soportes
	{

		//alert("respnsable"+responsable+"proceso"+proceso);
		$("#input_whis").val(historia);
		$("#input_wing").val(ingreso);
		$("#input_nombre").val(nombre);
		$("#input_tipoDocu").val(tipoDocu);
		$("#input_cedula").val(cedula);
		$("#input_fechaIngreso").val(fechaIngreso);
		$("#input_habitacionActual").val(habitacionActual);
	//	alert(nuevo);
		if( nuevo == "si")
		{


				$.ajax({

				 url: "MonitorSoportesFacturacion.php",
				type: "POST",
			   async: false,
				data: {
					   peticionAjax: "procesosxempresa",
						  wbasedato: wbd,
						    wcliame: wcliame,
						    wfachos: wfachos,
						  wemp_pmla: wemp_pmla,
						    proceso: proceso,
						   wempresa: responsable

					  },
				success: function(data)
				{
					$("#procesosxempresa").html('');
					$("#procesosxempresa").html( data.lista );

				},
				dataType: "json"
			});



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
						  wcliame: wcliame,
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
					//alert(data.lista)
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
							  wcliame: wcliame,
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
						//alert()
						//alert($("#div_listadoActual").length)
						activarIngresoNoResponsables(data.entidadesNoResponsables);
						generarListadoActual();
						listado.show();
						$("#div_menuPpal").hide();
						$("#div_menuPacientes").hide();
						$('[name="div_retornar"]').show();
						//$("#div_titulos").html( titulos[1] );
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
							  wcliame: wcliame,
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
						  wcliame: wcliame,
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

	function actualizarEstadoSoporte( empresaPlan, soporte, select, servicio ,especialidad ) //actualiza el estado de un soporte recibido, no recibido, no aplica, y realiza los movimientos en la base datos correspondiente
	{
		valor     = $(select).val();
		var  estadoglobal ='s';
		if(especialidad=='n')
		{


		}
		else
		{
			nuevosoporte = $(select).attr('soporte');

		}
		whistoria = $("#input_whis").val();
		wingreso  = $("#input_wing").val();
		wformato  = $("#formato_"+empresaPlan+"_"+soporte+"_"+servicio).val();
		$.ajax({
				 url: "MonitorSoportesFacturacion.php",
				type: "POST",
				data: {
					 peticionAjax: "actualizarSoporteListado",
						wbasedato: wbd,
						  wcliame: wcliame,
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

					if(especialidad !='n')
					{
						$(".class_"+nuevosoporte).each(function ()
						{


							if ($(this).is(':checked'))
							{

								if($(this).val()!='s')
								{
									estadoglobal = 'n';
								}


							}



						});

						/*alert("soporte"+nuevosoporte);
						alert("empresaPlan"+nuevosoporte);
						alert("servicio"+servicio);
						alert("estado global"+estadoglobal);*/

						if( estadoglobal == 'n')
						{
							$(".formato_"+empresaPlan+"_"+nuevosoporte+"_"+servicio).attr("disabled", true);
							$(".formato_"+empresaPlan+"_"+nuevosoporte+"_"+servicio).attr("checked", "");
						}

						if( estadoglobal  == "s")
							{
								$(".formato_"+empresaPlan+"_"+nuevosoporte+"_"+servicio).removeAttr('disabled');
								formatoDefecto = $("#formato_"+empresaPlan+"_"+nuevosoporte+"_"+servicio).val();
								setTimeout(function(){
										$(".formato_"+empresaPlan+"_"+nuevosoporte+"_"+servicio).each(function(){
										if( $(this).attr( "value" ) == formatoDefecto )
										{
											$(this).click();
										}
									});}, 300);
							}

					}
					else
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
					     wcliame: wcliame,
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
						    wcliame: wcliame,
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
							  wcliame: wcliame,
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
		//$("#div_menuPacientes").html("");
		//buscarPacientes()
		$("#div_menuPacientes").show();
		listado.hide();
		$('[name|="div_retornar"]').hide();
		$("#div_verFoto").hide();
		//$("#div_titulos").html( titulos[0] );
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

		if(accion=='e')
		{
			var conteo = 0;
			$(".verificarcheck").each(function(){
				if($(this).is(':checked'))
				{
					if($(this).val() =='n'){
						//alert($(this).val());
						conteo++;
					}
				}


			});

			if(conteo > 0)
			{
				alert("Solo se pueden enviar soportes si estan completos");
				return;
			}
			else
			{


			}
		}


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
						  wcliame: wcliame,
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
						  wcliame: wcliame,
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
						  wcliame: wcliame,
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
					     wcliame: wcliame,
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
					     wcliame: wcliame,
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
						//$("#div_titulos").html( titulos[3] );
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
		// alert("entro");
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
					     wcliame: wcliame,
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
					//$("#div_titulos").html( titulos[2] );
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

	function cambiarProceso( codigoProceso , responsable)
	{
		//alert(responsable);
		$("#funcionMenuProcesos").val( "update" );
		$("#codigoProceso").val( codigoProceso );
		radio = $("input[name='input_procesos'][value='"+codigoProceso+"']");
		radio.attr( "checked", "checked" );
		$.ajax({

				 url: "MonitorSoportesFacturacion.php",
				type: "POST",
			   async: false,
				data: {
					   peticionAjax: "procesosxempresa",
						  wbasedato: wbd,
						    wcliame: wcliame,
						    wfachos: wfachos,
						  wemp_pmla: wemp_pmla,
						    proceso: proceso,
						   wempresa: responsable

					  },
				success: function(data)
				{
					$("#procesosxempresa").html('');
					$("#procesosxempresa").html( data.lista );

				},
				dataType: "json"
			});


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
							  wcliame: wcliame,
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
$wactualiz = "2017-04-21";
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
				  WHERE dprori = '{$nivelUsuario}'
					AND dprest = 'on'";
	$rs  = mysql_query( $query, $conex );
	$row = mysql_fetch_array( $rs );

	( trim($row['minimo']) == "" ) ? $row['minimo'] = "off" : $row['minimo'] = $row['minimo'] ;

	$niveles[0] = $row['minimo'];
	$niveles[2] = $row['siguiente'];

	$query   = " SELECT dprfin maximo
				   FROM {$wfachos}_000018
				  WHERE dprdes = '{$nivelUsuario}'";
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

function nivelesLimitesRedefinido( $nivelUsuario, $proceso )
{
	global $wemp_pmla, $conex, $wfachos ;
	$niveles = array();
	$query   = " SELECT dprini minimo, dprdes siguiente
				   FROM {$wfachos}_000018
				  WHERE dprori = '{$nivelUsuario}'
				    AND dprcod = '{$proceso}'
					AND dprest = 'on'";
	$rs  = mysql_query( $query, $conex );
	$row = mysql_fetch_array( $rs );

	( trim($row['minimo']) == "" ) ? $row['minimo'] = "off" : $row['minimo'] = $row['minimo'] ;

	$niveles[0] = $row['minimo'];
	$niveles[2] = $row['siguiente'];

	$query   = " SELECT dprfin maximo
				   FROM {$wfachos}_000018
				  WHERE dprdes = '{$nivelUsuario}'
				    AND dprcod = '{$proceso}'";
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
	$tablaUsuario .= "<table width='50%'><tr align='center' class='encabezadoTabla'><td  >Rol</td><td>Usuario</td></tr>";
	$tablaUsuario .= "<tr><td class='fila1'  align='center'>{$cargo}</td>";
	$tablaUsuario .= "<td class='fila1'  align='center' >{$nombreUsuario}</td></tr>";
	$tablaUsuario .= "</table>";

	return($tablaUsuario);
}
function obtener_array_entidades($codNitEnt='', $codTarifa='', $conex = '',$wemp_pmla = '',$wbasedato = '')
{
	if(empty($wbasedato))
	{
		global $conex;
		global $wemp_pmla;
		global $wbasedato;
		global $wcliame;
	}

	$caracter_ok = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U", "");
	$caracter_ma = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£", "°");


	$q_entidades = "SELECT Empcod, Empnom
	 				  FROM ".$wcliame."_000024
					 WHERE Empest = 'on'
				  ORDER BY Empnom ";
	$res_entidades = mysql_query($q_entidades,$conex) or die("Error en el query: ".$q_entidades."<br>Tipo Error:".mysql_error());
	$arr_entidades = array();
	if(mysql_num_rows($res_entidades)>0)
	{
		$arr_entidades['*']	= 'TODOS';
	}
	while($row_entidades = mysql_fetch_array($res_entidades))
	{
		$row_entidades['Empnom'] = str_replace($caracter_ma, $caracter_ok, $row_entidades['Empnom']);
		$arr_entidades[trim($row_entidades['Empcod'])] = trim($row_entidades['Empnom']);
	}
	return $arr_entidades;
}

function obtener_array_cco($codNitEnt='', $codTarifa='', $conex = '',$wemp_pmla = '',$wbasedato = '')
{
	if(empty($wbasedato))
	{
		global $conex;
		global $wemp_pmla;
		global $wbasedato;
		global $wcliame;
	}

	$caracter_ok = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U", "");
	$caracter_ma = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£", "°");


	$q_entidades  = "SELECT Ccocod, Cconom
					   FROM {$wbasedato}_000011
			          WHERE Ccoest = 'on'
			            AND (Ccohos = 'on' or Ccourg = 'on' or Ccocir = 'on' or Ccoayu = 'on' or Ccocod ='1800')
						 ";
	$res_entidades = mysql_query($q_entidades,$conex) or die("Error en el query: ".$q_entidades."<br>Tipo Error:".mysql_error());
	$arr_entidades = array();
	if(mysql_num_rows($res_entidades)>0)
	{
		$arr_entidades['*']	= 'Todos';
	}
	while($row_entidades = mysql_fetch_array($res_entidades))
	{
		$row_entidades['Cconom'] = str_replace($caracter_ma, $caracter_ok, $row_entidades['Cconom']);
		$arr_entidades[trim($row_entidades['Ccocod'])] = trim($row_entidades['Cconom']);
	}
	return $arr_entidades;
}

function menuPpal()
{
	global $user_session;
	global $wfachos;
	global $conex;


	// $selectuser ="SELECT  uscniv , uscpcr
					// FROM ".$wfachos ."_000013
				   // WHERE uscusu='".$user_session."'  ";

	// $res_user = mysql_query($selectuser,$conex) or die("Error en el query: ".$selectuser."<br>Tipo Error:".mysql_error());
	$cambiadeRol='off';
	// while($row_user = mysql_fetch_array($res_user))
	// {
		// $cambiadeRol = $row_user['uscpcr'];
	// }


	if($cambiadeRol == 'on')
	{
		$botondecambiorol = "<input type='hidden' id='ocultoCodigoUsuario' value='".$user_session."'><input type='button' value='Cambiar de Rol' onclick='cambiarRol()'>";

	}
	else
	{
		$botondecambiorol = '';
	}

	$menu .= "<input type='hidden'  id='hidden_entidades' value='".json_encode(obtener_array_entidades())."'>";
	$menu .= "<input type='hidden'    id='hidden_cco' value='".json_encode(obtener_array_cco())."'>";
	$menu .= "<table>";
	$menu .= "<tr class='encabezadoTabla' align='center'><td colspan='3'>Parametros</td></tr>";

	$menu .= "<tr class='fila1'><td  align='center' >Unidad</td><td  align='center'>Entidad</td><td  align='center'>Historia</td></tr>";
	$menu .=" <tr class='fila2'><td ><input type='text'  class='campo_autocomplete'  id='busc_cco' size=40 value=''></td><td><input type='text'  class='campo_autocomplete'  id='busc_empresa' size=40 value=''></td><td class='fila2'><input type='text' id='whis_ingresada' size=40 value=''></td><tr>";
	$menu .= "<tr align='center'><td colspan='3'>".$botondecambiorol."<input type='button' id='btn_consultar'  value='Consultar' onclick='buscarPacientes()'><input type='button' id='btn_limpiar'  value='Limpiar' onclick='Limpiar()'><input type='button' id='btn_borrar'  value='Cerrar' onclick='window.close();'></td></tr>";
	$menu .= "</table>";

	/*$menu .= "<table>";
		$menu .= "<tr><td class='fila1'>HISTORIA: </td><td class='fila2'><input type='text' id='whis_ingresada' size=40 value=''></td><tr>";
		$menu .= "<tr><td class='fila1'>CENTRO DE COSTOS: </td><td class='fila2'>".selectCentroCostos()."</td><tr>";
	$menu .= "</table>";*/
	//$menu .= "<input type='button' id='btn_consultar' class='botona' value='CONSULTAR' onclick='generarSoportesPaciente()'>";

	$menu .= "<input type='hidden' id='input_whis' value=''>";
	$menu .= "<input type='hidden' id='input_wing' value=''>";
	$menu .= "<input type='hidden' id='input_nombre' value=''>";
	$menu .= "<input type='hidden' id='input_tipoDocu' value=''>";
	$menu .= "<input type='hidden' id='input_cedula' value=''>";
	$menu .= "<input type='hidden' id='input_fechaIngreso' value=''>";
	$menu .= "<input type='hidden' id='input_habitacionActual' value=''>";
	$menu .= "<input type='hidden' name='codigoProceso' id='codigoProceso' value=''>";
	$menu .= "<input type='hidden' name='funcionMenuProcesos' id='funcionMenuProcesos' value=''>";
	//-------------


	//---------
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
$wcliame        = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
$wfachos        = consultarAliasPorAplicacion($conex, $wemp_pmla, "Facturacion hospitalaria");
$nivelUsuario   = verificarNivelDeUsuario( $user_session );
$puedeCerrar    = $nivelUsuario[1];
$esAdmin        = $nivelUsuario[2];
$proceso 	    = $nivelUsuario[3];
$nivelUsuario   = $nivelUsuario[0];
$nivelesLimites = nivelesLimitesRedefinido( $nivelUsuario ,$proceso );
$nivelMinimo    = $nivelesLimites[0];
$nivelMaximo    = $nivelesLimites[1];
$nivelSiguiente = $nivelesLimites[2];
$wtema 			= consultarTema( "facturacion", $conex, $wfachos );

//VARIABLES INCIALES
echo "<input type='hidden' name='wemp_pmla' id='wemp_pmla' value='{$wemp_pmla}'>";
echo "<input type='hidden' name='wbasedato' id='wbasedato' value='{$wbasedato}'>";
echo "<input type='hidden' name='wcliame'   id='wcliame' value='{$wcliame}'>";
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
//echo "<br>";
echo "<div id='div_menuPpal' align='center'>".menuPpal()."</div>";
//echo "<br>";
//echo "<div id='div_titulos' align='center' valign='center'></div>";
echo "<div id='div_menuPacientes' align='center'></div>";
echo "<div name='div_retornar' align='center' style='display:none;'><input type=button  class='botona' name='btn_retornar' value='RETORNAR' onclick='retornar()'></div>";
echo "<br>";
echo "<div id='div_respuestas' align='center' class='div_contenedor' valing='top' style='display:none'></div>";
echo "<br>";
echo "<div name='div_retornar' align='center' style='display:none;'><br><input type=button class='botona' name='btn_retornar' id='btn_retornar_1' value='RETORNAR' onclick='retornar()'></div>";

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
	echo "<div align='center' style='width:90%;' id='procesosxempresa'>";
		//echo armarMenuProcesos();
	// echo "</div>";
		// echo "<div align='center' style='width:90%;' id='procesosxempresados'>";
		// echo "aaaaaaaaa";
	// echo "</div>";
echo "</div>";

echo "<div id='divPlanyFlujo' ></div>";
echo "<div id='divCambioRol' ></div>";
?>
</body>
</html>