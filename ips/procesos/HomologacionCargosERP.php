<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA
//=========================================================================================================================================\\
//DESCRIPCION:
//AUTOR:				Jerson Andres Trujillo
//FECHA DE CREACION:
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz='En Desarrollo, jerson';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
//
//
//--------------------------------------------------------------------------------------------------------------------------------------------


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
	

	include_once("ips/funciones_facturacionERP.php");
	include "../../gesapl/procesos/gestor_aplicaciones_config.php";
	include_once("../../gesapl/procesos/gesapl_funciones.php");
	$conex 			= obtenerConexionBD("matrix");
	$wbasedato 		= consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
	$wfecha			= date("Y-m-d");
    $whora 			= date("H:i:s");


//=====================================================================================================================================================================
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================

//-------------------------------------------------------------------------------------
//	Funcion que obtiene el nombre de un concepto
//	Responsable:	Jerson Trujillo.
//-------------------------------------------------------------------------------------
function nombreConcepto($concepto)
{
	global $wbasedato;
	global $conex;

	if($concepto != '*')
	{
		$q = "SELECT Grudes
				FROM ".$wbasedato."_000200
			   WHERE Grucod = '".$concepto."'
		";
		$res = mysql_query($q,$conex) or die ("Query (nombreConcepto): ".$q." - ".mysql_error());
		if($row_q = mysql_fetch_array($res))
			return $row_q['Grudes'];
		else
			return '';
	}
	else
		return 'TODOS';
}
//-------------------------------------------------------------------------------------
//	Funcion que obtiene el nombre de un procedimiento
//	Responsable:	Jerson Trujillo.
//-------------------------------------------------------------------------------------
function nombreProcedimiento($procedimiento)
{
	global $wbasedato;
	global $conex;

	if($procedimiento != '*')
	{
		$q = "SELECT Pronom
				FROM ".$wbasedato."_000103
			   WHERE Procod = '".$procedimiento."'
		";
		$res = mysql_query($q,$conex) or die ("Query (nombreProcedimiento): ".$q." - ".mysql_error());
		if($row_q = mysql_fetch_array($res))
			return $row_q['Pronom'];
		else
			return '';
	}
	else
		return 'TODOS';
}
//-------------------------------------------------------------------------------------
//	Funcion que obtiene el nombre de un tipo de empresa
//	Responsable:	Jerson Trujillo.
//-------------------------------------------------------------------------------------
function nombreTipoEmpresa($tipoEmpresa)
{
	global $wbasedato;
	global $conex;

	if($tipoEmpresa != '*')
	{
		$q = "SELECT Temdes
				FROM ".$wbasedato."_000029
			   WHERE Temcod = '".$tipoEmpresa."'
		";
		$res = mysql_query($q,$conex) or die ("Query (nombreTipoEmpresa): ".$q." - ".mysql_error());
		if($row_q = mysql_fetch_array($res))
			return $row_q['Temdes'];
		else
			return '';
	}
	else
		return 'TODOS';
}
//-------------------------------------------------------------------------------------
//	Funcion que obtiene el nombre de una tarifa
//	Responsable:	Jerson Trujillo.
//-------------------------------------------------------------------------------------
function nombreTarifa($tarifa)
{
	global $wbasedato;
	global $conex;

	if($tarifa != '*')
	{
		$q = "SELECT Tardes
				FROM ".$wbasedato."_000025
			   WHERE Tarcod = '".$tarifa."'
		";
		$res = mysql_query($q,$conex) or die ("Query (nombreTarifa): ".$q." - ".mysql_error());
		if($row_q = mysql_fetch_array($res))
			return $row_q['Tardes'];
		else
			return '';
	}
	else
		return 'TODOS';
}
//-------------------------------------------------------------------------------------
//	Funcion que obtiene el nombre de un entidad
//	Responsable:	Jerson Trujillo.
//-------------------------------------------------------------------------------------
function nombreEntidad($entidad)
{
	global $wbasedato;
	global $conex;

	if($entidad != '*')
	{
		$q = "SELECT Empnom
				FROM ".$wbasedato."_000024
			   WHERE Empcod = '".$entidad."'
		";
		$res = mysql_query($q,$conex) or die ("Query (nombreEntidad): ".$q." - ".mysql_error());
		if($row_q = mysql_fetch_array($res))
			return $row_q['Empnom'];
		else
			return '';
	}
	else
		return 'TODOS';
}
//-------------------------------------------------------------------------------------
//	Funcion que obtiene el nombre de un tercero
//	Responsable:	Jerson Trujillo.
//-------------------------------------------------------------------------------------
function nombreTercero($tercero)
{
	global $wbasedato;
	global $conex;
	global $wemp_pmla;

	// --> Consultar la base de datos de movimiento hospitalario correspondiente a la empresa
	$wbasedato_mov = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

	if($tercero != '*')
	{
		$q = "SELECT CONCAT(Medno1, ' ', Medno2, ' ', Medap1, ' ', Medap2) as nombre
				FROM ".$wbasedato_mov."_000048
			   WHERE Meddoc = '".$tercero."'
		";
		$res = mysql_query($q,$conex) or die ("Query (nombreEntidad): ".$q." - ".mysql_error());
		if($row_q = mysql_fetch_array($res))
			return $row_q['nombre'];
		else
			return '';
	}
	else
		return 'TODOS';
}
//-------------------------------------------------------------------------------------
//	Funcion que obtiene el nombre de una especialidad
//	Responsable:	Jerson Trujillo.
//-------------------------------------------------------------------------------------
function nombreEspecialidad($especialidad)
{
	global $wbasedato;
	global $conex;
	global $wemp_pmla;

	// --> Consultar la base de datos de movimiento hospitalario correspondiente a la empresa
	$wbasedato_mov = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

	if($especialidad != '*')
	{
		$q = "SELECT Espnom
				FROM ".$wbasedato_mov."_000044
			   WHERE Espcod = '".$especialidad."'
		";
		$res = mysql_query($q,$conex) or die ("Query (nombreEspecialidad): ".$q." - ".mysql_error());
		if($row_q = mysql_fetch_array($res))
			return $row_q['Espnom'];
		else
			return '';
	}
	else
		return 'TODOS';
}
//-------------------------------------------------------------------------------------
//	Funcion que obtiene el maestro de tipos de ingreso
//	Responsable:	Jerson Trujillo.
//-------------------------------------------------------------------------------------
function maestroTiposIngreso()
{
	global $wbasedato;
	global $conex;

	$arr_TipIng = array();

	$q_TipIng = " SELECT Tiicod, Tiides, Tiiuni
				    FROM ".$wbasedato."_000175
				   WHERE Tiiest = 'on' ";
	$res_TipIng = mysql_query($q_TipIng,$conex) or die ("Query (Tipos de ingreso): ".$q_TipIng." - ".mysql_error());

	$arr_TipIng['*']['nombre']  = 'TODOS';
	$arr_TipIng['*']['codUnix'] = '';
	while($row_TipIng = mysql_fetch_array($res_TipIng))
	{
		$arr_TipIng[$row_TipIng['Tiicod']]['nombre'] = utf8_encode($row_TipIng['Tiides']);
		$arr_TipIng[$row_TipIng['Tiicod']]['codUnix']= $row_TipIng['Tiiuni'];
	}

	return $arr_TipIng;
}
//-------------------------------------------------------------------------------------
//	Funcion que obtiene el maestro de tipos de paciente
//	Responsable:	Jerson Trujillo.
//-------------------------------------------------------------------------------------
function maestroTiposPaciente()
{
	global $wbasedato;
	global $conex;

	$arr_TipPac = array();

	$q_TipPac = " SELECT Clacod, Clades
				    FROM root_000099
				   WHERE Claest = 'on' ";
	$res_TipPac = mysql_query($q_TipPac,$conex) or die ("Query (Tipos de paciente): ".$q_TipPac." - ".mysql_error());

	$arr_TipPac['*'] = 'TODOS';
	while($row_TipPac = mysql_fetch_array($res_TipPac))
	{
		$arr_TipPac[$row_TipPac['Clacod']] = utf8_encode($row_TipPac['Clades']);
	}

	return $arr_TipPac;
}
//-------------------------------------------------------------------------------------
//	Funcion que obtiene el maestro de grupos de medicos
//	Responsable:	Jerson Trujillo.
//-------------------------------------------------------------------------------------
function maestroGruposMedicos()
{
	global $wbasedato;
	global $conex;
	global $wemp_pmla;

	// --> Consultar la base de datos de movimiento hospitalario correspondiente a la empresa
	$wbasedato_mov = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

	$arr_gruMed = array();

	$q_GruMed = " SELECT Gmecod, Gmenom
				    FROM ".$wbasedato_mov."_000166
				   WHERE Gmeest = 'on' ";
	$res_GruMed = mysql_query($q_GruMed,$conex) or die ("Query (grupo medicos): ".$q_GruMed." - ".mysql_error());

	while($row_GruMed = mysql_fetch_array($res_GruMed))
	{
		$arr_gruMed[$row_GruMed['Gmecod']] = utf8_encode($row_GruMed['Gmenom']);
	}

	return $arr_gruMed;
}
//-------------------------------------------------------------------------------------
//	Funcion que obtiene el maestro de tipos de cuadro de turno y retorna un array
//	Responsable:	Jerson Trujillo.
//-------------------------------------------------------------------------------------
function maestroTiposCuadrosTurno()
{
	global $wbasedato;
	global $conex;
	global $wemp_pmla;

	// --> Consultar la base de datos de movimiento hospitalario correspondiente a la empresa
	$wbasedato_mov = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

	$arr_TipCuaTur = array();

	$q_TipCua = " SELECT Ccucod, Ccunom
				    FROM ".$wbasedato_mov."_000160
				   WHERE Ccuest = 'on' ";
	$res_TipCua = mysql_query($q_TipCua,$conex) or die ("Query (Tipos de cuadros de turno): ".$q_TipCua." - ".mysql_error());

	while($row_TipCua = mysql_fetch_array($res_TipCua))
	{
		$arr_TipCuaTur[$row_TipCua['Ccucod']] = utf8_encode($row_TipCua['Ccunom']);
	}

	return $arr_TipCuaTur;
}
//-------------------------------------------------------------------------------------
//	Funcion que obtiene el maestro de terceros que maneja unix
//	Responsable:	Jerson Trujillo.
//-------------------------------------------------------------------------------------
function maestroTercerosSegunUnix($codTercero='')
{
	global $wbasedato;
	global $conex;
	// --> Array con caracteres especiales para escaparlos en el nombre del tercero
	$caracter_ok = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U", "");
	$caracter_ma = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£", "°");
	$arr_TerUnix = array();

	$q_TerUnix = " SELECT Tercod, Ternom
				     FROM ".$wbasedato."_000196
				    WHERE Terest = 'on'
	".(($codTercero != '') ? "AND Tercod = '".$codTercero."'" : "" )."";

	$res_TerUnix = mysql_query($q_TerUnix,$conex) or die ("Query (Terceros segun unix): ".$q_TerUnix." - ".mysql_error());

	$arr_TerUnix['*'] = 'TODOS';
	while($row_TerUnix = mysql_fetch_array($res_TerUnix))
	{
		$row_TerUnix['Ternom'] = str_replace($caracter_ma, $caracter_ok, $row_TerUnix['Ternom']);
		$arr_TerUnix[trim($row_TerUnix['Tercod'])] = utf8_encode($row_TerUnix['Ternom']);
	}

	return $arr_TerUnix;
}
//-------------------------------------------------------------------------------------
//	Funcion que obtiene los conceptos que maneja unix
//	Responsable:	Jerson Trujillo.
//-------------------------------------------------------------------------------------
function obtener_array_conceptos_unix($codConcepto='')
{
	global $wbasedato;
	global $conex;
	// --> Array con caracteres especiales para escaparlos en el nombre del tercero
	$caracter_ok = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U", "");
	$caracter_ma = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£", "°");
	$arr_TerUnix = array();

	$q_ConUnix = " SELECT Congen, Connom
				     FROM ".$wbasedato."_000197
				    WHERE Conest = 'on'
	".(($codConcepto != '') ? "AND Congen = '".$codConcepto."'" : "" )."";

	$res_ConUnix = mysql_query($q_ConUnix,$conex) or die ("Query (Conceptos segun unix): ".$q_ConUnix." - ".mysql_error());

	$arr_ConUnix['*'] = 'TODOS';
	while($row_ConUnix = mysql_fetch_array($res_ConUnix))
	{
		$row_ConUnix['Connom'] = str_replace($caracter_ma, $caracter_ok, $row_ConUnix['Connom']);
		$arr_ConUnix[trim($row_ConUnix['Congen'])] = utf8_encode($row_ConUnix['Connom']);
	}

	return $arr_ConUnix;
}
//-------------------------------------------------------------------------------------
//	Funcion que pinta el formulario para ingresar un nuevo de registro de homologacion
//	Responsable:	Jerson Trujillo.
//-------------------------------------------------------------------------------------
function FormularioRegistro($idRegistro)
{
	global $wbasedato;
	global $conex;

	// --> Obtener el maestro de tipos de ingreso
	$arr_TiposIngreso  	= maestroTiposIngreso();
	// --> Obtener el maestro de tipos de paciente
	$arr_TiposPaciente 	= maestroTiposPaciente();
	// --> Obtener el maestro de tipos de cuadro de turno
	$tiposCuadroTurno 	= maestroTiposCuadrosTurno();
	// --> Obtener el maestro de grupos medicos
	$gruposMedicos 		= maestroGruposMedicos();

	// --> Consultar los datos del registro de homologacion
	$editar = false;
	if($idRegistro != 'nuevo')
	{
		$q_valores = "
		SELECT *
		  FROM ".$wbasedato."_000192
		 WHERE id = '".$idRegistro."'
		";
		$res_valores 	= mysql_query($q_valores,$conex) or die ("Query (Valores de registro de homologacion): ".$q_valores." - ".mysql_error());
		$row_valores 	= mysql_fetch_array($res_valores);
		$editar 		= true;

		$nomConcepto 	= nombreConcepto($row_valores['Homcom']);
		$nomProcedim 	= nombreProcedimiento($row_valores['Hompom']);
		$nom_cco 		= Obtener_array_cco($row_valores['Homccm']);
		$nom_cco 		= $nom_cco[$row_valores['Homccm']];
		$nomTipoEmp 	= nombreTipoEmpresa($row_valores['Homtem']);
		$nomTarifa 		= nombreTarifa($row_valores['Homtam']);
		$nomEntidad 	= nombreEntidad($row_valores['Homenm']);
		$nomTercero 	= nombreTercero($row_valores['Homtrm']);
		$nomEspecial	= nombreEspecialidad($row_valores['Homesm']);
		$nomConceptoSe 	= obtener_array_conceptos_unix($row_valores['Homcos']);
		$nomConceptoSe 	= $nomConceptoSe[$row_valores['Homcos']];
		$nomProcedimSe 	= nombreProcedimiento($row_valores['Hompos']);
		$nomTerceroSe 	= maestroTercerosSegunUnix($row_valores['Homtrs']);
		$nomTerceroSe	= $nomTerceroSe[$row_valores['Homtrs']];
	}

	// --> Pintar formulario
	echo"
	<table width='97%' id='TablaForm' idReg='".$idRegistro."'>
		<tr>
			<td>
			</td>
			<td colspan='4' class='fondoAmarillo' align='center'><b>MATRIX</b></td>
		</tr>
		<tr>
			<td class='encabezadoTabla pad' width='18%'>Concepto:</td>
			<td class='fila1' align='center'><input type='text' class='campoReq' style='width:250px' id='conceptoMa' ".(($editar) ? " valor='".$row_valores['Homcom']."' value='".$row_valores['Homcom']."-".$nomConcepto."' nombre='".$nomConcepto."' " : " valor='' value='' nombre='' ")."></td>
			<td class='encabezadoTabla pad' width='18%'>Procedimiento:</td>
			<td class='fila1' align='center'><input type='text' class='campoReq' style='width:250px' id='procedimientoMa' ".(($editar) ? " valor='".$row_valores['Hompom']."' value='".$row_valores['Hompom']."-".$nomProcedim."' nombre='".$nomProcedim."' " : " valor='' value='' nombre='' ")."></td>
		</tr>
		<tr>
			<td class='encabezadoTabla pad'>Centro de costos:</td>
			<td class='fila2' align='center'><input type='text' class='campoReq' style='width:250px' id='centroCostosMa' ".(($editar) ? "  valor='".$row_valores['Homccm']."' value='".$row_valores['Homccm']."-".$nom_cco."' nombre='".$nom_cco."' " : " valor='' value='' nombre='' ")."></td>
			<td class='encabezadoTabla pad'>Tipo de empresa:</td>
			<td class='fila2' align='center'><input type='text' class='campoReq' style='width:250px' id='tipoEmpresaMa' ".(($editar) ? " valor='".$row_valores['Homtem']."' value='".$row_valores['Homtem']."-".$nomTipoEmp."' nombre='".$nomTipoEmp."' " : " valor='' value='' nombre='' ")."></td>
		</tr>
		<tr>
			<td class='encabezadoTabla pad'>Tarifa:</td>
			<td class='fila1' align='center'><input type='text' class='campoReq' style='width:250px' id='tarifaMa' ".(($editar) ? " valor='".$row_valores['Homtam']."' value='".$row_valores['Homtam']."-".$nomTarifa."' nombre='".$nomTarifa."' " : " valor='' value='' nombre='' ")."></td>
			<td class='encabezadoTabla pad'>Entidad:</td>
			<td class='fila1' align='center'><input type='text' class='campoReq' style='width:250px' id='entidadMa' ".(($editar) ? " valor='".$row_valores['Homenm']."' value='".$row_valores['Homenm']."-".$nomEntidad."' nombre='".$nomEntidad."' " : " valor='' value='' nombre='' ")."></td>
		</tr>
		<tr>
			<td class='encabezadoTabla pad'>Tipo de ingreso:</td>
			<td class='fila2' align='center'>
				<select class='campoReq' style='width:250px' id='tipoIngresoMa'>
					<option value=''>Seleccione...</option>";
				foreach($arr_TiposIngreso as $codigo => $arrValores)
				{
					echo "<option value='".$codigo."' codUnix='".$arrValores['codUnix']."' ".((isset($editar) && $codigo == $row_valores['Homtim']) ? "SELECTED" : "").">".$arrValores['nombre']."</option>";
				}
	echo"		</select>
			</td>
			<td class='encabezadoTabla pad'>Tipo de paciente:</td>
			<td class='fila2' align='center'>
				<select class='campoReq' style='width:250px' id='tipoPacienteMa'>
					<option value=''>Seleccione...</option>";
				foreach($arr_TiposPaciente as $codigo => $nombre)
				{
					echo "<option value='".$codigo."' ".((isset($editar) && $codigo == $row_valores['Homtpm']) ? "SELECTED" : "").">".$nombre."</option>";
				}
	echo"		</select>
			</td>
		</tr>
		<tr>
			<td class='encabezadoTabla pad'>Tercero:</td>
			<td class='fila1' align='center'><input type='text' class='campoReq' style='width:250px' id='terceroMa' ".(($editar) ? " valor='".$row_valores['Homtrm']."' value='".$row_valores['Homtrm']."-".$nomTercero."' nombre='".$nomTercero."' " : " valor='' value='' nombre='' ")."></td>
			<td class='encabezadoTabla pad'>Especialidad:</td>
			<td class='fila1' align='center'><input type='text' class='campoReq' style='width:250px' id='especialidadMa' ".(($editar) ? " valor='".$row_valores['Homesm']."' value='".$row_valores['Homesm']."-".$nomEspecial."' nombre='".$nomEspecial."' " : " valor='' value='' nombre='' ")."></td>
		</tr>";
		$msj = "<font style=\"font-weight:normal;text-align:justify\">&nbsp;<img width=\"12\" height=\"12\" src=\"../../images/medical/root/info.png\">&nbsp;
					Aplica solo cuando existe doble tarifa,<br>diferenciadas por el cobro o no de honorarios.&nbsp;
				</font>";

	$radio1 = "";
	$radio2 = "";
	$radio3 = "";

	if($editar)
	{
		if($row_valores['Homthm'] == 'on')
			$radio1 = "CHECKED='CHECKED' ";
		if($row_valores['Homthm'] == 'off')
			$radio2 = "CHECKED='CHECKED' ";
		if($row_valores['Homthm'] == '*')
			$radio3 = "CHECKED='CHECKED' ";
	}
	else
		$radio3 = "CHECKED='CHECKED' ";

	echo"<tr>
			<td class='encabezadoTabla pad'>
				<img tooltip='si' title='".$msj."' style='cursor:help' width='12' height='12' src='../../images/medical/root/info.png'>
				Cobra Honorarios:
			</td>
			<td class='fila2' align='center' style='font-weight:bold'>
				Si <input type='radio' 			name='tarifaHonorarios' value='on'			".$radio1.">
				No <input type='radio' 			name='tarifaHonorarios' value='off'			".$radio2.">
				No aplica <input type='radio' 	name='tarifaHonorarios' value='noAplica'	".$radio3.">
			</td>
			<td class='encabezadoTabla pad'>Cuadro de turno:</td>
			<td class='fila2' align='center'>
				<select class='campoReq' style='width:250px' id='tipoCuadroTurno'>";
				foreach($tiposCuadroTurno as $codigo => $nombre)
				{
					echo "<option value='".$codigo."' ".((isset($editar) && $codigo == $row_valores['Homtct']) ? "SELECTED" : "").">".$nombre."</option>";
				}
	echo"		</select>
			</td>
		</tr>
		<tr>
			<td class='encabezadoTabla pad'>Grupo medico:</td>
			<td class='fila1' align='center'>
				<select class='campoReq' style='width:250px' id='grupoMedico'>";
				foreach($gruposMedicos as $codigo => $nombre)
				{
					echo "<option value='".$codigo."' ".((isset($editar) && $codigo == $row_valores['Homgme']) ? "SELECTED" : "").">".$nombre."</option>";
				}
	echo"		</select>
			</td>
			<td></td>
			<td></td>
		</tr>
		<tr><td><br></td></tr>
		<tr>
			<td></td>
			<td class='encabezadoTabla' style=' background-color: #C0E5F7;color: #000000;font-size: 10pt;' align='center' colspan='4'>UNIX</td>
		</tr>
		<tr>
			<td class='encabezadoTabla pad'>Concepto:</td>
			<td class='fila2' align='center'><input type='text' class='campoReq' style='width:250px' id='conceptoSe' ".(($editar) ? " valor='".$row_valores['Homcos']."' value='".$row_valores['Homcos']."-".$nomConceptoSe."' nombre='".$nomConceptoSe."' " : " valor='' value='' nombre='' ")."></td>
			<td class='encabezadoTabla pad'>Procedimiento:</td>
			<td class='fila2' align='center'><input type='text' class='campoReq' style='width:250px' id='procedimientoSe' ".(($editar) ? " valor='".$row_valores['Hompos']."' value='".$row_valores['Hompos']."-".$nomProcedimSe."' nombre='".$nomProcedimSe."' " : " valor='' value='' nombre='' ")."></td>
		</tr>
		<tr>
			<td class='encabezadoTabla pad'>Tercero:</td>
			<td class='fila2' align='center'><input type='text' class='campoReq' style='width:250px' id='terceroSe' ".(($editar) ? " valor='".$row_valores['Homtrs']."' value='".$row_valores['Homtrs']."-".$nomTerceroSe."' nombre='".$nomTerceroSe."' " : " valor='' value='' nombre='' ")."></td>
			<td class=' pad'></td>
			<td class='' align='center'></td>
		</tr>
		<tr>
			<td colspan='4' align='center'>
				<br>
				<input type='button' value='Homologar' id='botonGuardar' 	onClick='guardarHomologacion()'>&nbsp;
				<input type='button' value='Cancelar'  id='botonCancelar' 	onClick='limpiarFormulario();$(\"#botonCancelar\").hide();$(\"#botonGuardar\").val(\"Homologar\");' 	style='display:none'>
			</td>
		</tr>
		<tr>
			<td colspan='4' align='right'>
				<div id='div_mensajes' class='fondoAmarillo' style='display:none;border: 1px solid #2A5DB0;padding: 5px;' align='center'>
				</div>
			</td>
		</tr>
	</table>
	";
}

//-----------------------------------------------------------------------------
//	Funcion que pinta la lista de homologaciones ya configuradas
//	Responsable:	Jerson Trujillo.
//-----------------------------------------------------------------------------
function ListaHomologaciones($buscConceptoMa='', $buscProcedimientoMa='', $buscConceptoSe='', $buscProcedimientoSe='', $rangoIdIni='0', $rangoIdFin='300')
{
	global $wbasedato;
	global $conex;
	global $wemp_pmla;

	// --> Consultar la base de datos de movimiento hospitalario correspondiente a la empresa
	$wbasedato_mov = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

	// --> Obtener el total de registros
	$q_total = " SELECT count(*) AS total
				  FROM ".$wbasedato."_000192
				 WHERE Homest = 'on' ";
	$res_total 		= mysql_query($q_total,$conex) or die ("Query (Total registros): ".$q_total." - ".mysql_error());
	$totalRegistros = mysql_fetch_array($res_total);
	$totalRegistros = $totalRegistros['total'];
	echo "<input type='hidden' id='totalRegistros' value='".$totalRegistros."'>";

	// --> Consultar las homologaciones ya configuradas
	// --> Query para obtener todos los registros
	if($buscConceptoMa == '' && $buscProcedimientoMa == '' && $buscConceptoSe == '' && $buscProcedimientoSe == '')
	{
		$q_homo = " SELECT *, id AS idRegistro
					  FROM ".$wbasedato."_000192
					 WHERE Homest = 'on'
				  ORDER BY idRegistro
					 LIMIT ".$rangoIdIni.", ".$rangoIdFin."
				";
	}
	// --> Query para obtener registros pero aplicando los filtros de busqueda
	else
	{
		$q_homo = " SELECT A.*, A.id AS idRegistro
					  FROM ".$wbasedato."_000192 as A ".(($buscConceptoMa!='') ? ", ".$wbasedato."_000200" : "").(($buscConceptoSe!='') ? ", ".$wbasedato."_000197" : "").(($buscProcedimientoMa!='' || $buscProcedimientoSe!='') ? ", ".$wbasedato."_000103" : "")."
					 WHERE Homest = 'on' ";
		// --> Filtro para el concepto matrix
		if($buscConceptoMa != '')
		{
			$q_homo.=" AND Homcom = Grucod
					   AND (Grucod LIKE '%".$buscConceptoMa."%' OR Grudes LIKE '%".$buscConceptoMa."%')";
		}
		// --> Filtro para el concepto servinte
		if($buscConceptoSe != '')
		{
			$q_homo.=" AND Homcos = Congen
					   AND (Congen LIKE '%".$buscConceptoSe."%' OR Connom LIKE '%".$buscConceptoSe."%')";
		}
		// --> Filtro para el procedimiento matrix
		if($buscProcedimientoMa != '')
		{
			$q_homo.=" AND Hompom = Procod
					   AND (Procod LIKE '%".$buscProcedimientoMa."%' OR Pronom LIKE '%".$buscProcedimientoMa."%')";
		}
		// --> Filtro para el procedimiento revinte
		if($buscProcedimientoSe != '')
		{
			$q_homo.=" AND Hompos = Procod
					   AND (Procod LIKE '%".$buscProcedimientoSe."%' OR Pronom LIKE '%".$buscProcedimientoSe."%')";
		}
		$q_homo.="	 ORDER BY idRegistro
					 LIMIT ".$rangoIdIni.", ".$rangoIdFin."";
	}
	$res_homo = mysql_query($q_homo,$conex) or die ("Query (Lista de homologaciones): ".$q_homo." - ".mysql_error());

	// --> Pintar encabezado de la lista
	echo"
	<table width='100%'>
		<tr>
			<td align='left' width='83%'><b>Total Registros: </b>".mysql_num_rows($res_homo)."/".$totalRegistros."<b> | Rango: </b>".$rangoIdIni."-".$rangoIdFin."</td>
			<td align='right' width='7%' tipo='atras' style='cursor:pointer' onClick='navegarRegistros(this)'>
				<img width='15' height='15' src='../../images/medical/sgc/anterior.png'>
				Anterior
			</td>
			<td align='center' width='1%'><b>|</b></td>
			<td align='left' width='7%' tipo='adelante' style='cursor:pointer' onClick='navegarRegistros(this)'>
				Siguiente
				<img width='15' height='15' src='../../images/medical/sgc/siguiente.png'>
			</td>
			<td width='1%'></td>
		</tr>
	</table>
	<table id='TableLista'>
		<tr>
			<td colspan='14'  class='fondoAmarillo' align='center'><b>MATRIX</b></td>
			<td>&nbsp;</td>
			<td colspan='3'  style=' background-color: #C0E5F7;color: #000000;font-size: 10pt;' align='center'><b>UNIX</b></td>
		</tr>
		<tr align='center' class='encabezadoTabla' style='font-size: 8pt;'>
			<td>Id</td>
			<td>Concepto</td>
			<td>Procedimiento</td>
			<td>Centro de costos</td>
			<td>Tipo de empresa</td>
			<td>Tarifa</td>
			<td>Entidad</td>
			<td>Tipo de ingreso:</td>
			<td>Tipo de paciente:</td>
			<td>Tercero:</td>
			<td>Especialidad:</td>
			<td>Cobra Honorarios:</td>
			<td>Cuadro de turno:</td>
			<td>Grupo medico:</td>
			<td style='background-color: #ffffff'></td>
			<td>Concepto</td>
			<td>Procedimiento</td>
			<td>Tercero</td>
			<td colspan='2' style='background-color: #ffffff;'></td>
		</tr>";

		echo "<input type='hidden' id='rangoIdIni' value='".$rangoIdIni."'>";
		echo "<input type='hidden' id='rangoIdFin' value='".$rangoIdFin."'>";

	if(mysql_num_rows($res_homo) == 0)
	{
		echo "
		<tr class='fila2'><td colspan='18' align='center'>No existen registros...</td></tr>";
	}
	else
	{

		// --> Array Conceptos
		$array_conceptos 	= obtener_array_conceptos();
		// --> Array Conceptos Unix
		$array_conceptosUnix= obtener_array_conceptos_unix();
		// --> Array procedimientos
		$array_proced		= obtener_array_procedimientos($conex, $wemp_pmla, $wbasedato);
		// --> Centro de costos
		$array_cco 			= Obtener_array_cco();
		// --> Tipos de empresa
		$array_tipos_empres	= Obtener_array_tipos_empresa();
		// --> Array tarifas
		$array_tarifas		= Obtener_array_tarifas();
		// --> Array entidades
		$array_entidades	= obtener_array_entidades('', '');
		// --> Terceros matrix (movhos_000048)
		$array_terceros 	= obtener_array_terceros();
		$array_terceros['*']= 'TODOS';
		// --> Array especialidades
		$array_especialidad	= obtener_array_especialidades();
		// --> Terceros segun unix (cliame_000196)
		$array_tercerosUnix	= maestroTercerosSegunUnix();
		// --> Nombre del tipo de ingreso
		$array_nom_tipoIng  = maestroTiposIngreso();
		// --> Nombre del tipo de paciente
		$array_nom_tipoPaci = maestroTiposPaciente();
		// --> Obtener el maestro de tipos de cuadro de turno
		$tiposCuadroTurno 	= maestroTiposCuadrosTurno();
		// --> Obtener el maestro de grupos medicos
		$gruposMedicos 		= maestroGruposMedicos();

		$colorF 		 	= 'fila2';
		// --> Pintar los registros
		while($row_homo = mysql_fetch_array($res_homo))
		{
			if($colorF == 'fila1')
				$colorF = 'fila2';
			else
				$colorF = 'fila1';

			$nom_tipoIngreso  = $array_nom_tipoIng[$row_homo['Homtim']]['nombre'];
			$nom_tipoPaciente = $array_nom_tipoPaci[$row_homo['Homtpm']];

			// --> Cobra honorarios
			if($row_homo['Homthm'] != '*')
				$cobraHonorarios = ($row_homo['Homthm'] == 'on') ? 'Sí' : 'No';
			else
				$cobraHonorarios = 'N/A';

			echo"
			<tr class='".$colorF."' id='".$row_homo['id']."' style='font-size: 6.4pt;'>
				<td>".$row_homo['idRegistro']."</td>
				<td>".$row_homo['Homcom']."-".$array_conceptos[$row_homo['Homcom']]."</td>
				<td>".$row_homo['Hompom']."-".$array_proced[$row_homo['Hompom']]."</td>
				<td>".$row_homo['Homccm']."-".$array_cco[$row_homo['Homccm']]."</td>
				<td>".$row_homo['Homtem']."-".$array_tipos_empres[$row_homo['Homtem']]."</td>
				<td>".$row_homo['Homtam']."-".$array_tarifas[$row_homo['Homtam']]."</td>
				<td>".$row_homo['Homenm']."-".$array_entidades[$row_homo['Homenm']]."</td>
				<td>".$row_homo['Homtim']."-".$nom_tipoIngreso."</td>
				<td>".$row_homo['Homtpm']."-".$nom_tipoPaciente."</td>
				<td>".$row_homo['Homtrm']."-".$array_terceros[$row_homo['Homtrm']]."</td>
				<td>".$row_homo['Homesm']."-".$array_especialidad[$row_homo['Homesm']]."</td>
				<td align='center'>".$cobraHonorarios."</td>
				<td>".$row_homo['Homtct']."-".$tiposCuadroTurno[$row_homo['Homtct']]."</td>
				<td>".$row_homo['Homgme']."-".$gruposMedicos[$row_homo['Homgme']]."</td>
				<td style='background-color: #ffffff'></td>
				<td>".$row_homo['Homcos']."-".$array_conceptosUnix[$row_homo['Homcos']]."</td>
				<td>".$row_homo['Hompos']."-".$array_proced[$row_homo['Hompos']]."</td>
				<td>".$row_homo['Homtrs']."-".$array_tercerosUnix[$row_homo['Homtrs']]."</td>
				<td style='background-color: #ffffff'><img src='../../images/medical/hce/mod.PNG' 	title='Editar' 	 style='cursor: pointer;' onclick='editarHomologacion(\"".$row_homo['idRegistro']."\")'></td>
				<td style='background-color: #ffffff'><img src='../../images/medical/eliminar1.png' title='Eliminar' style='cursor: pointer;' onclick='eliminarHomologacion(\"".$row_homo['idRegistro']."\")'></td>
			</tr>
			";
		}
	}
	echo"
	</table>
	";
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
		case 'ObtenerTarifas':
		{
			$arr_tar = array();
			// --> Array con caracteres especiales para escaparlos en el nombre de las tarifas
			$caracter_ok = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U", "");
			$caracter_ma = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£", "°");

			// --> Obtener las tarifas segun el tipo de empresa
			$q_tar = "
			SELECT Tarcod, Tardes
			  FROM ".$wbasedato."_000024, ".$wbasedato."_000025
			 WHERE Empest = 'on'
			   ".(($codTipoEmp != '*') ? "AND Emptem = '".$codTipoEmp."'" : "")."
			   AND Emptar = Tarcod
			   AND Tarest = 'on'
			 ";

			$res_tar = mysql_query($q_tar,$conex) or die ("Query (Consultar tarifas segun el tipo de empresa): ".$q_tar." - ".mysql_error());
			while($row_tar = mysql_fetch_array($res_tar))
			{
				$row_tar['Tardes'] = str_replace($caracter_ma, $caracter_ok, $row_tar['Tardes']);
				$arr_tar[trim($row_tar['Tarcod'])] = $row_tar['Tardes'];
			}
			echo json_encode($arr_tar);
			break;
			return;
		}
		case 'ObtenerProcedimientos':
		{
			// --> Primero debo obtenr cual es el codigo con el que quedo homologado en matrix
			$sqlConHom = "SELECT Consim
							FROM ".$wbasedato."_000197
						   WHERE Congen = '".$CodConcepto."'
			";
			$resSqlConHom = mysql_query($sqlConHom,$conex) or die ("Query (Consultar homologacion del concepto): ".$sqlConHom." - ".mysql_error());
			if($row_SqlConHom = mysql_fetch_array($resSqlConHom))
				$CodConcepto = 	$row_SqlConHom['Consim'];

			$data = Obtener_array_procedimientos_x_concepto($CodConcepto, '');
			echo json_encode($data);
			break;
			return;
		}
		case 'ObtenerEspecialidades':
		{
			// --> Array con caracteres especiales para escaparlos en el nombre de las especialidades
			$caracter_ok = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U", "");
			$caracter_ma = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£", "°");

			// --> Consultar la base de datos de movimiento hospitalario correspondiente a la empresa
			$wbasedato_mov = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

			// --> Obtener las especialidades del medico
			$q_esp = "
			SELECT Espcod, Espnom
			  FROM ".$wbasedato_mov."_000044, ".$wbasedato_mov."_000048
			 WHERE Meddoc LIKE '".(($codTercero == '*') ? '%' : $codTercero)."'
			   AND SUBSTRING_INDEX(Medesp, '-', 1) = Espcod
			   AND Medest = 'on'
			 ";

			$res_esp = mysql_query($q_esp,$conex) or die ("Query (Consultar especialidades de un medico): ".$q_esp." - ".mysql_error());
			while($row_esp = mysql_fetch_array($res_esp))
			{
				$row_esp['Espnom'] = str_replace($caracter_ma, $caracter_ok, $row_esp['Espnom']);
				$arr_especialidades[trim($row_esp['Espcod'])] = $row_esp['Espnom'];
			}
			echo json_encode($arr_especialidades);
			break;
			return;
		}
		case 'ObtenerEntidades':
		{
			// --> Array con caracteres especiales para escaparlos en el nombre de las tarifas
			$caracter_ok = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U", "");
			$caracter_ma = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£", "°");

			$q_entidades = "SELECT Empcod, Empnom
							  FROM ".$wbasedato."_000024
							 WHERE Empest = 'on'
			".(($codTipoEmp != '*') ? " AND Emptem = '".$codTipoEmp."' " : " ")."
			".(($codTarifa  != '*') ? " AND Emptar = '".$codTarifa."' " : " ")."
						  ORDER BY Empnom ";
			$res_entidades = mysql_query($q_entidades,$conex) or die("Error en el query: ".$q_entidades."<br>Tipo Error:".mysql_error());
			$arr_entidades = array();
			while($row_entidades = mysql_fetch_array($res_entidades))
			{
				$row_entidades['Empnom'] = str_replace($caracter_ma, $caracter_ok, $row_entidades['Empnom']);
				$arr_entidades[trim($row_entidades['Empcod'])] = trim($row_entidades['Empnom']);
			}

			echo json_encode($arr_entidades);
			break;
			return;
		}
		case 'PintarListaHomologaciones':
		{
			echo ListaHomologaciones('', '', '', '', $rangoIni, $rangoFin);
			break;
		}
		case 'FiltrarListaHomologaciones':
		{
			echo ListaHomologaciones($buscConceptoMa, $buscProcedimientoMa, $buscConceptoSe, $buscProcedimientoSe, $rangoIni, $rangoFin);
			break;
			return;
		}
		case 'editarHomologacion':
		{
			echo FormularioRegistro($idRegistro);
			break;
			return;
		}
		case 'eliminarHomologacion':
		{
			$q_eliminar = "
			DELETE FROM ".$wbasedato."_000192
			      WHERE id = '".$idReg."'
			";
			mysql_query($q_eliminar,$conex) or die ("Query (Eliminar homologacion): ".$q_eliminar." - ".mysql_error());
			$respuesta['mensaje'] 	= 'Registro eliminado';
			echo json_encode($respuesta);
			break;
			return;
		}
		case 'guardarHomologacion':
		{
			$respuesta 			= array();
			$tarifaHonorariosMa = ($tarifaHonorariosMa == 'noAplica') ? '*' : $tarifaHonorariosMa;

			// --> Validar que el registro a guardar o a actualizar, no exista.
			$q_validar = " SELECT id
							 FROM ".$wbasedato."_000192
							WHERE Homcom = '".$conceptoMa."'
							  AND Hompom = '".$procedimientoMa."'
							  AND Homccm = '".$centroCostosMa."'
							  AND Homtem = '".$tipoEmpresaMa."'
							  AND Homtam = '".$tarifaMa."'
							  AND Homenm = '".$entidadMa."'
							  AND Homtim = '".$tipoIngresoMa."'
							  AND Homtpm = '".$tipoPacienteMa."'
							  AND Homtrm = '".$terceroMa."'
							  AND Homesm = '".$especialidadMa."'
							  AND Homthm = '".$tarifaHonorariosMa."'
							  AND Homtct = '".$tipoCuadroTurno."'
							  AND Homgme = '".$grupoMedico."'
							  AND Homest = 'on'
							  AND id 	!= '".$idReg."'
			";
			$res_validar = mysql_query($q_validar,$conex) or die ("Query (Validar homologacion): ".$q_validar." - ".mysql_error());

			if(mysql_num_rows($res_validar) == 0)
			{
				// --> Insertar un nuevo registro de homologacion
				if($idReg == 'nuevo')
				{
					$q_guardar = "
					INSERT INTO ".$wbasedato."_000192
						  ( Medico,				Fecha_data,    	Hora_data,		Homcom,				Hompom,					Homccm,					Homtem,					Homtam,				Homenm,				Homtim,					Homtpm,					Homtrm,				Homesm,					Homthm,						Homtct,					Homgme,				Homcos,				Hompos,					Homtis,					Homtrs,				Homest,	Seguridad		)
					VALUES(	'".$wbasedato."',	'".$wfecha."',	'".$whora."',	'".$conceptoMa."',	'".$procedimientoMa."',	'".$centroCostosMa."',	'".$tipoEmpresaMa."',	'".$tarifaMa."',	'".$entidadMa."',	'".$tipoIngresoMa."',	'".$tipoPacienteMa."',	'".$terceroMa."',	'".$especialidadMa."',	'".$tarifaHonorariosMa."',	'".$tipoCuadroTurno."',	'".$grupoMedico."',	'".$conceptoSe."',	'".$procedimientoSe."',	'".$tipoIngresoSe."',	'".$terceroSe."',	'on',	'C-".$wuse."'	)
					";
					mysql_query($q_guardar,$conex) or die ("Query (Insertar homologacion): ".$q_guardar." - ".mysql_error());
					$respuesta['mensaje'] 	= 'Homologación guardada';
					$respuesta['id_reg'] 	= mysql_insert_id();
				}
				// --> Actualizar un registro de homologacion
				else
				{
					$q_actulizar = "
					UPDATE ".$wbasedato."_000192
					   SET Homcom = '".$conceptoMa."',
						   Hompom = '".$procedimientoMa."',
						   Homccm = '".$centroCostosMa."',
						   Homtem = '".$tipoEmpresaMa."',
						   Homtam = '".$tarifaMa."',
						   Homenm = '".$entidadMa."',
						   Homtim = '".$tipoIngresoMa."',
						   Homtpm = '".$tipoPacienteMa."',
						   Homtrm = '".$terceroMa."',
						   Homesm = '".$especialidadMa."',
						   Homthm = '".$tarifaHonorariosMa."',
						   Homtct = '".$tipoCuadroTurno."',
						   Homgme = '".$grupoMedico."',
						   Homcos = '".$conceptoSe."',
						   Hompos = '".$procedimientoSe."',
						   Homtis = '".$tipoIngresoSe."',
						   Homtrs = '".$terceroSe."'
					 WHERE 	   id = '".$idReg."'
					";
					mysql_query($q_actulizar,$conex) or die ("Query (Actualizar homologacion): ".$q_actulizar." - ".mysql_error());
					$respuesta['mensaje'] 	= 'Homologación actualizada';
					$respuesta['id_reg'] 	= $idReg;
					$respuesta['error'] 	= false;
				}
			}
			else
			{
				$respuesta['mensaje'] 	= 'Esta homologación ya existe.';
				$respuesta['error'] 	= true;
				$row_validar 			=  mysql_fetch_array($res_validar);
				$respuesta['id_reg'] 	= $row_validar['id'];
			}
			echo json_encode($respuesta);
			break;
			return;
		}
	}
}
//=======================================================================================================================================================
//		F I N   F I L T R O S   A J A X
//=======================================================================================================================================================


//=======================================================================================================================================================
//	I N I C I O   E J E C U C I O N   N O R M A L   D E L   P R O G R A M A
//=======================================================================================================================================================
else
{
	?>
	<html>
	<head>
	  <title>...</title>
	</head>

		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
		<link type="text/css" href="../../../include/root/jquery.ui.timepicker.css" rel="stylesheet"/>
		<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
		<link rel="stylesheet" href="../../../include/ips/facturacionERP.css" />

		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
		<script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>
		<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>

	<script type="text/javascript">
//=====================================================================================================================================================================
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T
//=====================================================================================================================================================================

	//------------------------------------------------------------------------------------------------------
	//	Funcion general que recive un array o el id de un hidden Json y carga un autocomplete en un iput
	// 	Jerson Trujillo.
	//------------------------------------------------------------------------------------------------------
	function crear_autocomplete(HiddenArray, TipoHidden, AgregarOpcTodos, CampoCargar, AccionSelect)
	{
		if(TipoHidden)
			var ArrayValores  = eval('(' + $('#'+HiddenArray).val() + ')');
		else
			var ArrayValores  = eval('(' + HiddenArray + ')');

		if(AgregarOpcTodos)
			ArrayValores['*'] = 'TODOS';

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
						case 'CargarProcedimientos':
						{
							if( value == 'conceptoMa')
								traerArrayProcedimientos(ui.item.value, 'procedimientoMa');
							else
								traerArrayProcedimientos(ui.item.value, 'procedimientoSe');
							return false;
						}
						case 'CargarTarifas':
						{
							traerArrayTarifas(ui.item.value);
							return false;
						}
						case 'CargarEntidades':
						{
							traerArrayEntidades(ui.item.value);
							return false;
						}
						case 'CargarEspecialidades':
						{
							traerArrayEspecialidades(ui.item.value);
							return false;
						}
					}
					return false;
				}
			});
			limpiaAutocomplete(value);
		});
	}

	//----------------------------------------------------------------------------------
	//	Controlar que el input no quede con basura, sino solo con un valor seleccionado
	//----------------------------------------------------------------------------------
	function limpiaAutocomplete(idInput)
	{
		$( "#"+idInput ).on({
		focusout: function(e) {
				if($(this).val().replace(/ /gi, "") == '')
				{
					$(this).val("");
					$(this).attr("valor","");
					$(this).attr("nombre","");
				}
				else
				{
					$(this).val($(this).attr("valor")+"-"+$(this).attr("nombre"));
				}
			}
		});
	}

	//--------------------------------------------------------------------------
	//	Ajustar tamaño de la lista de homologaciones ya configuradas
	//--------------------------------------------------------------------------
	function ajustarTamano()
	{
		//--> Ajustar tamaño del div
		var altura_div = $("#TableLista").height();
		if(altura_div > 400)
		{
			$('#DivListaHomologaciones').css(
				{
					'height': 410,
					'overflow': 'auto',
					'background': 'none repeat scroll 0 0'
				}
			);
		}
		else
		{
			$('#DivListaHomologaciones').css(
				{
					'height': altura_div+10
				}
			);
		}
	}
	// ----------------------------------------------------------------------
	//	Funcion que navega en los registros de homologacion (Adelante-Atras)
	//-----------------------------------------------------------------------
	function navegarRegistros(Elemento)
	{
		var rangoIni = parseInt($("#rangoIdIni").val());
		var rangoFin = parseInt($("#rangoIdFin").val());

		if($(Elemento).attr('tipo') == 'adelante')
		{
			rangoIni = rangoFin;
			rangoFin = rangoFin+300;
			if(rangoFin > parseInt($("#totalRegistros").val()))
				rangoFin = parseInt($("#totalRegistros").val());
		}
		else
		{
			if((rangoIni-300 < 0))
			{
				rangoFin = 300;
				rangoIni = 0;
			}
			else
			{
				rangoFin = rangoIni;
				rangoIni = rangoIni-300;
			}
		}
		FiltrarListaHomologaciones(rangoIni, rangoFin);
	}
	//--------------------------------------------------------------------------
	//	Pintar la lista de homologaciones existentes
	//--------------------------------------------------------------------------
	function PintarListaHomologaciones(rangoIni, rangoFin)
	{
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   		'',
			accion:         		'PintarListaHomologaciones',
			rangoIni:				rangoIni,
			rangoFin:				rangoFin,
			wemp_pmla:				$('#wemp_pmla').val()
		}
		,function(html){
			$("#DivListaHomologaciones").html(html);
			ajustarTamano();

			/*var rangoFinV = parseInt($("#rangoIdFin").val());

			// --> Controlar vista de flechas de navegacion
			if(rangoIni == 0)
				$("td[tipo=atras]").hide();
			else
				$("td[tipo=atras]").show();
			console.log(rangoFinV+'-'+rangoFin);
			if(rangoFinV < (rangoFin-1))
				$("td[tipo=adelante]").hide();
			else
				$("td[tipo=adelante]").show();*/

		});
	}
	//--------------------------------------------------------------------------
	//	Filtrar la lista de homologaciones existentes
	//--------------------------------------------------------------------------
	function FiltrarListaHomologaciones(rangoIni, rangoFin)
	{
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   		'',
			accion:         		'FiltrarListaHomologaciones',
			wemp_pmla:				$('#wemp_pmla').val(),
			buscConceptoMa:			$("#buscConceptoMa").val(),
			buscConceptoSe:			$("#buscConceptoSe").val(),
			buscProcedimientoSe:	$("#buscProcedimientoSe").val(),
			buscProcedimientoMa:	$("#buscProcedimientoMa").val(),
			rangoIni:				rangoIni,
			rangoFin:				rangoFin
		}
		,function(html){
			$("#DivListaHomologaciones").html(html);
			ajustarTamano();


			// --> Controlar vista de flechas de navegacion
			/*var rangoFinV = parseInt($("#rangoIdFin").val());
			if(rangoIni == 0)
				$("td[tipo=atras]").hide();
			else
				$("td[tipo=atras]").show();
			console.log(rangoFinV+'-'+rangoFin);
			if(rangoFinV < (rangoFin-1))
				$("td[tipo=adelante]").hide();
			else
				$("td[tipo=adelante]").show();*/
		});
	}

	//--------------------------------------------------------------------------
	//	Guardar un nuevo registro de homologacion
	//--------------------------------------------------------------------------
	function guardarHomologacion()
	{
		var guardar = true;
		$('[bordeObligatorio=si]').css("border","").removeAttr('bordeObligatorio');

		// --> Validacion de campos
		$("#TablaForm .campoReq").each(function(){
			var campo = $(this);
			if(campo.attr("valor") != undefined )
			{
				if(campo.attr("valor") == "")
				{
					CampoObligatorio(campo);
					guardar = false;
				}
			}
			else
			{
				if(campo.val() == "")
				{
					CampoObligatorio(campo);
					guardar = false;
				}
			}
		});

		if(guardar)
		{
			$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
			{
				consultaAjax:   		'',
				accion:         		'guardarHomologacion',
				wemp_pmla:				$('#wemp_pmla').val(),
				conceptoMa:				$('#conceptoMa').attr('valor'),
				procedimientoMa:		$('#procedimientoMa').attr('valor'),
				centroCostosMa:			$('#centroCostosMa').attr('valor'),
				tipoEmpresaMa:			$('#tipoEmpresaMa').attr('valor'),
				tarifaMa:				$('#tarifaMa').attr('valor'),
				entidadMa:				$('#entidadMa').attr('valor'),
				tipoIngresoMa:			$('#tipoIngresoMa').val(),
				tipoPacienteMa:			$('#tipoPacienteMa').val(),
				terceroMa:				$('#terceroMa').attr('valor'),
				especialidadMa:			$('#especialidadMa').attr('valor'),
				tarifaHonorariosMa:		$('[name=tarifaHonorarios]:CHECKED').val(),
				tipoCuadroTurno:		$('#tipoCuadroTurno').val(),
				grupoMedico:			$('#grupoMedico').val(),
				conceptoSe:				$('#conceptoSe').attr('valor'),
				procedimientoSe:		$('#procedimientoSe').attr('valor'),
				terceroSe:				$('#terceroSe').attr('valor'),
				tipoIngresoSe:			$( "#tipoIngresoMa option:selected" ).attr("codUnix"),
				idReg:					$('#TablaForm').attr('idReg')
			}
			,function(respuesta){
				mostrar_mensaje(respuesta.mensaje);
				if(!respuesta.error)
				{
					limpiarFormulario();
					$("#botonGuardar").val('Homologar');
					$("#botonCancelar").hide();
					var rangoIni = parseInt($("#rangoIdIni").val());
					var rangoFin = parseInt($("#rangoIdFin").val());
					PintarListaHomologaciones(rangoIni, rangoFin);
				}
				else
				{
					$("#"+respuesta.id_reg).css("color", "#FF0000");

					setTimeout(function(){
						$("#"+respuesta.id_reg).css("color", "#000000");
					},15000);
				}
			}, 'json');
		}
		else
		{
			mostrar_mensaje("Faltan campos obligatorios");
		}

	}

	function CampoObligatorio(Elemento)
	{
		Elemento.css("border","1px dotted #FF0000").attr('bordeObligatorio','si');
	}
	//----------------------------------------------------------------------------------------------------
	//	Funcion que permite la edicion de una homologacion, colocando sus respectivos
	//	valores en el formulario de ingreso
	//----------------------------------------------------------------------------------------------------
	function editarHomologacion(idRegistro)
	{
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   		'',
			accion:         		'editarHomologacion',
			wemp_pmla:				$('#wemp_pmla').val(),
			idRegistro:				idRegistro
		}
		,function(html){
			$("#DivFormularioRegistro").html(html);
			$("#botonGuardar").val('Actualizar');
			$("#botonCancelar").show();

			// --> Cargar autocomplete de conceptos
			crear_autocomplete('hidden_conceptos', true, false, 'conceptoMa', 'CargarProcedimientos');
			crear_autocomplete('hidden_conceptos_unix', true, false, 'conceptoSe', 'CargarProcedimientos');

			// --> Cargar autocomplete de los procedimientos
			traerArrayProcedimientos($('#conceptoMa').attr('valor'), 'procedimientoMa');
			traerArrayProcedimientos($('#conceptoSe').attr('valor'), 'procedimientoSe');

			// --> Cargar autocomplete de los centros de costos
			crear_autocomplete('hidden_centroCostos', true, true, 'centroCostosMa');

			// --> Cargar autocomplete de los tipos de empresa
			crear_autocomplete('hidden_tipos_empresa', true, true, 'tipoEmpresaMa', 'CargarTarifas');

			// --> Cargar autocomplete de las tarifas
			traerArrayTarifas($('#tipoEmpresaMa').attr('valor'));

			// --> Cargar autocomplete de las entidades
			traerArrayEntidades($('#tarifaMa').attr('valor'));

			// --> Cargar autocomplete de los terceros
			crear_autocomplete('hidden_terceros', true, true, 'terceroMa', 'CargarEspecialidades');
			crear_autocomplete('hidden_terceros_unix', true, true, 'terceroSe');

			// --> Cargar autocomplete de las especialidades
			traerArrayEspecialidades($('#terceroMa').attr('valor'));

			// --> Activar tooltip
			$("[tooltip=si]").tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
		});
	}
	//-----------------------------------------------------------------------
	//	Funcion que obtiene un array de procedimientos dado un concepto
	//-----------------------------------------------------------------------
	function traerArrayProcedimientos(concepto, campo)
	{
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   		'',
			accion:         		'ObtenerProcedimientos',
			wemp_pmla:				$('#wemp_pmla').val(),
			CodConcepto:			concepto
		}
		,function(ArrProcedimientos){
			crear_autocomplete(ArrProcedimientos, false, true, campo);
		});
	}
	//-----------------------------------------------------------------------
	//	Funcion que obtiene un array de tarifas dado un tipo de empresa
	//-----------------------------------------------------------------------
	function traerArrayTarifas(tipoEmpresa)
	{
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   		'',
			accion:         		'ObtenerTarifas',
			wemp_pmla:				$('#wemp_pmla').val(),
			codTipoEmp:				tipoEmpresa
		}
		,function(arrayTarifas){
			crear_autocomplete(arrayTarifas, false, true, 'tarifaMa', 'CargarEntidades');
		});
	}
	//---------------------------------------------------------------------------------
	//	Funcion que obtiene un array de entidades dado un tipo de empresa y una tarifa
	//---------------------------------------------------------------------------------
	function traerArrayEntidades(tarifa)
	{
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   		'',
			accion:         		'ObtenerEntidades',
			wemp_pmla:				$('#wemp_pmla').val(),
			codTarifa:				tarifa,
			codTipoEmp:				$('#tipoEmpresaMa').attr('valor')
		}
		,function(arrayEntidades){
			crear_autocomplete(arrayEntidades, false, true, 'entidadMa');
		});
	}
	//---------------------------------------------------------------------------------
	//	Funcion que obtiene un array de especialidades dado un tercero
	//---------------------------------------------------------------------------------
	function traerArrayEspecialidades(tercero)
	{
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   		'',
			accion:         		'ObtenerEspecialidades',
			wemp_pmla:				$('#wemp_pmla').val(),
			codTercero:				tercero
		}
		,function(arrayEspecialidades){
			crear_autocomplete(arrayEspecialidades, false, true, 'especialidadMa');
		});
	}
	//-----------------------------------------------------------------------
	//	Pinta un mensaje en el div correspondiente para los mensajes
	//-----------------------------------------------------------------------
	function mostrar_mensaje(mensaje)
	{
		$("#div_mensajes").html("<img width='15' height='15' src='../../images/medical/root/info.png' />&nbsp;"+mensaje);
		$("#div_mensajes").css({"width":"250","opacity":" 0.6","fontSize":"11px"});
		$("#div_mensajes").hide();

		$("#div_mensajes").effect("pulsate", {}, 2000);

		setTimeout(function() {
			$("#div_mensajes").hide(500);
		}, 15000);
	}
	//-----------------------------------------------------------------------
	//	Elimina un registro de homologacion
	//-----------------------------------------------------------------------
	function eliminarHomologacion(idResgistro)
	{
		if (confirm("Desea eliminar este registro de homologación"))
		{
			$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
			{
				consultaAjax:   		'',
				accion:         		'eliminarHomologacion',
				wemp_pmla:				$('#wemp_pmla').val(),
				idReg:					idResgistro
			}
			,function(respuesta){
				mostrar_mensaje(respuesta.mensaje);

				$("#"+idResgistro).hide(500, function(){
					$("#"+idResgistro).remove();
				});

			}, 'json');
		}
	}
	//-----------------------------------
	//	Limpia los campos del formulario
	//-----------------------------------
	function limpiarFormulario()
	{
		$('#TablaForm').attr('idReg', 'nuevo');
		$("#TablaForm .campoReq").each(function(){
			var campo = $(this);
			if(campo.attr("valor") != undefined )
			{
				campo.attr("valor", "");
				campo.attr("nombre", "");
				campo.val("");
			}
			else
			{
				campo.val("Seleccione...");
			}
		});

		$("[name=tarifaHonorarios], [value=noAplica]").attr("CHECKED", "CHECKED");
	}
	//--------------------------------------------------------------------------
	//	Al cargar el programa
	//--------------------------------------------------------------------------
	$(document).ready(function() {

		// --> Cargar autocomplete de conceptos
		crear_autocomplete('hidden_conceptos', true, false, 'conceptoMa', 'CargarProcedimientos');
		crear_autocomplete('hidden_conceptos_unix', true, false, 'conceptoSe', 'CargarProcedimientos');

		// --> Cargar autocomplete de los centros de costos
		crear_autocomplete('hidden_centroCostos', true, true, 'centroCostosMa');

		// --> Cargar autocomplete de los tipos de empresa
		crear_autocomplete('hidden_tipos_empresa', true, true, 'tipoEmpresaMa', 'CargarTarifas');

		// --> Cargar autocomplete de los terceros
		crear_autocomplete('hidden_terceros', true, true, 'terceroMa', 'CargarEspecialidades');
		crear_autocomplete('hidden_terceros_unix', true, true, 'terceroSe');

		ajustarTamano();
		// --> Activar tooltip
		$("[tooltip=si]").tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });

	});
//=======================================================================================================================================================
//	F I N  F U N C I O N E S  J A V A S C R I P T
//=======================================================================================================================================================
	</script>

<!--=====================================================================================================================================================================
	E S T I L O S
=====================================================================================================================================================================-->
	<style type="text/css">
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
	</style>
<!--=====================================================================================================================================================================
	F I N   E S T I L O S
=====================================================================================================================================================================-->



<!--=====================================================================================================================================================================
	I N I C I O   B O D Y
=====================================================================================================================================================================-->
	<BODY>
	<?php
	// --> Crear hiddens para autocompletes
	// --> Conceptos
	$array_conceptos 	= obtener_array_conceptos();
	echo "<input type='hidden' id='hidden_conceptos' 	value='".json_encode($array_conceptos)."'>";
	// --> Conceptos Unix
	$array_conceptosUnix= obtener_array_conceptos_unix();
	echo "<input type='hidden' id='hidden_conceptos_unix' 	value='".json_encode($array_conceptosUnix)."'>";
	// --> Centro de costos
	$array_cco 			= Obtener_array_cco();
	echo "<input type='hidden' id='hidden_centroCostos' 	value='".json_encode($array_cco)."'>";
	// --> Tarifas
	$array_tipos_empres	= Obtener_array_tipos_empresa();
	echo "<input type='hidden' id='hidden_tipos_empresa'  	value='".json_encode($array_tipos_empres)."'>";
	// --> Terceros matrix (movhos_000048)
	$array_terceros 	= obtener_array_terceros();
	echo "<input type='hidden' id='hidden_terceros' 	value='".json_encode($array_terceros)."'>";

	// --> Terceros segun unix (cliame_000196)
	$array_tercerosUnix	= maestroTercerosSegunUnix();
	echo "<input type='hidden' id='hidden_terceros_unix' 	value='".json_encode($array_tercerosUnix)."'>";

	echo "
	<div align='center'>
		<table width='94%' border='0' cellpadding='3' cellspacing='3'>
			<tr>
				<td align='center'>
					<fieldset align='center' style='padding:15px;width:933px;'>
						<legend class='fieldset'>Nuevo registro</legend>
						<div id='DivFormularioRegistro'>";
						FormularioRegistro('nuevo');
	echo"				</div>
					</fieldset>
					<br>
					<fieldset align='center' id='' style='padding:15px;width:100%'>
						<legend class='fieldset'>Lista de homologaciones configuradas</legend>
						<div>";
							// --> Filtros de busqueda
		echo"				<table width='100%' class='Bordegris fila2' style='padding:2px;' id='tablaFiltros'>
								<tr>
									<td>
										<img width='18' height='18' src='../../images/medical/HCE/lupa.PNG' title='Buscar'>
									</td>
									<td>Concepto matrix: 		<input type='text' size='15' id='buscConceptoMa'></td>
									<td>Procedimiento matrix: 	<input type='text' size='15' id='buscProcedimientoMa'></td>
									<td>Concepto servinte: 		<input type='text' size='15' id='buscConceptoSe'></td>
									<td>Procedimiento servinte: <input type='text' size='15' id='buscProcedimientoSe'></td>
									<td><input type='button' value='Buscar...' onClick='FiltrarListaHomologaciones(0, 300)'></td>
								</tr>
							</table>
							<br>
							<div id='DivListaHomologaciones'>";
							ListaHomologaciones();
	echo "					</div>
						</div>
					</fieldset>
				</td>
			</tr>
		</table>
	</div>";
	?>
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
