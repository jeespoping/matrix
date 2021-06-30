<?php 

<<<<<<< HEAD
=======
function validarExisteTabla100($conex, $wemp_pmla){
	$wbasedato1 = consultarInstitucionPorCodigo($conex, $wemp_pmla);
	$wbasedato = $wbasedato1->baseDeDatos;

	$query = "SELECT COUNT(*) AS count FROM information_schema.tables 
				WHERE table_schema = 'matrix' AND table_name = '".$wbasedato."_000100'";
	$res = mysql_query($query, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla ".$wbasedato."_000100 ".mysql_errno()." - Error en el query $query - ".mysql_error() ) );
	$rows = mysqli_fetch_array($res);
	$result = $rows[0] == 1 ? true : false;
	
	return $result;
}

>>>>>>> 723f0db725c5893097b5d27e2fe79714e6f42a44
function notificarPacienteInternacional($conex, $wemp_pmla, $historia, $ingreso, $nombrePaciente, $servicioIng)
{
	$correoDestino = consultarAliasPorAplicacion( $conex, $wemp_pmla, "emailDestinoPacienteInternacional");
	
	$mensajeError = "";
	if($correoDestino!="" && $correoDestino!="NO APLICA" && $correoDestino!=".")
	{
		$asunto = "Atencion...";
		$mensaje = "Paciente Internacional Origen ".$wemp_pmla." Con historia nro: ".$historia."-".$ingreso." ".$nombrePaciente." En la unidad de ".$servicioIng;
		$altbody = "";
		
		$email        		= consultarAliasPorAplicacion( $conex, $wemp_pmla, "emailEnviosTI");
		$email        		= explode("--", $email );
		$wremitente			= array( 'email'	=> $email[0],
									 'password' => $email[1],
									 'from' 	=> $email[0],
									 'fromName' => "",
							 );
		$wdestinatario = explode(";",$correoDestino);
		$respuesta = sendToEmail($asunto, $mensaje, $altbody, $wremitente, $wdestinatario);
		
		if($respuesta['Error'])
		{
			$mensajeError = "Correo enviado";
		}
		else
		{
			$mensajeError = "No se pudo enviar el correo";
		}
	}
	
	return $mensajeError;
}

function insertLog( $wemp_pmla, $user_session, $accion, $tabla, $err, $descripcion, $identificacion, $sql_error = "",  $plan="", $servicio="", $wsoporte )
{
    global $conex;
	$wfachos      = consultarAliasPorAplicacion($conex, $wemp_pmla, "Facturacion hospitalaria");
	$descripcion = str_replace("'",'"',$descripcion);
    //$sql_error = ereg_replace('([ ]+)',' ',$sql_error);
	$sql_error = preg_replace('([ ]+)',' ',$sql_error);

    $insert = " INSERT INTO ".$wfachos."_000021
                    (Medico, Fecha_data, Hora_data, logori, Logcdu, Logemp, Logpln, Logser, Logsop, Logacc, Logtab, Logerr, Logsqe, Logdes, Loguse, Logest, Seguridad)
                VALUES
                    ('".$wfachos."','".date("Y-m-d")."','".date("H:i:s")."', 'Admision', '".utf8_decode($identificacion)."', '', '".$plan."', '".$servicio."', '".$wsoporte."', '".utf8_decode($accion)."','".$tabla."','".$err."', '".$sql_error."','".$descripcion."','".$user_session."','on','C-".$user_session."')";

    $res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - en el query (Insertar En Log): " . $insert . " - " . mysql_error());

	return $insert;
}

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

	//Diagnostico // and (codigoPais = '".$codigoPais."' or codigoPais = '*' )
	$sql = "SELECT Codigo, Descripcion
			FROM root_000002
			WHERE (Descripcion LIKE '%".utf8_decode($dep)."%' OR Codigo LIKE '%".utf8_decode($dep)."%' )
				
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
				$val[ $prefijoBD.substr( $keyPost, $lenHtml, $longitud ) ] =  $valuePost ;
			}
			else{
				$val[ substr( $keyPost, $lenHtml, $longitud ) ] =  $valuePost ;
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

				if( !$res1 ){
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
						$res1 = mysql_query( $sqlUpdate, $conex ) or ( $data[ 'mensaje' ] = mysql_errno()." - Error en el query $sql - ".mysql_error() );
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
				$resCancPre = mysql_query( $sql, $conex );
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
						$rsaccp2 = mysql_query( $qaccp2, $conex );
					}else{
						$accPrevioReal = $accidente_previo;
					}

					$datosTabla[ 'Accrei' ] = $accPrevioReal;
				}

				$sqlUpdate = crearStringUpdate( $wbasedato."_000148", $datosTabla );

				$res1 = mysql_query( $sqlUpdate, $conex ) or ( $data[ 'mensaje' ] = mysql_errno()." - Error en el query $sql - ".mysql_error() );

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
						$res1 = mysql_query( $sqlUpdate, $conex ) or ( $data[ 'mensaje' ] = mysql_errno()." - Error en el query $sql - ".mysql_error() );
						$accPrevioReal          = $rowacc['accrei'];
					}else{
						$accPrevioReal = "";
					}
				}

				if( $res1 ){
					if( mysql_affected_rows() > 0 ){
					}
					$data[ "mensaje" ] = utf8_encode( "Se actualizo correctamente" );
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

				$resEnc = mysql_query( $sqlInsert, $conex ) or ( $data[ 'error' ] = utf8_encode( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() ) );

				// if( mysql_affected_rows() > 0 ){	//si inserto el encabezado
				if( $resEnc ){	//si inserto el encabezado

					$datosTabla = crearArrayDatos( $wbasedato, "Dev", "det_Cat", 3 );

					$datosTabla[ "Devcod" ] = $cod;
					$datosTabla[ "Devest" ] = "on";

					$sqlInsert = crearStringInsert( $wbasedato."_000149", $datosTabla );

					$res1 = mysql_query( $sqlInsert, $conex ) or ( $data[ 'error' ] = utf8_encode( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() ) );

					if( $res1 ){
						$data[ "mensaje" ] = utf8_encode( "Se registró correctamente" );
						if( mysql_affected_rows() > 0 ){
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

					$res1 = mysql_query( $sqlUpdate, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query $sqlUpdate - ".mysql_error() ) );

					if( $res1 ){
						if( mysql_affected_rows() > 0 ){
						}
						$data[ "mensaje" ] = utf8_encode( "Se actualizo correctamente" );
						//revizar
						$datosEnc[ "Evncod" ] = $rowsEnc[ 'Evncod' ];
						$datosEnc[ "id" ] = $rowsEnc[ 'Evncod' ];
						$datosEnc[ "Evnest" ] = "on";
						$sqlUptEnc = crearStringUpdate( $wbasedato."_000150", $datosEnc );

						$resUptEnc = mysql_query( $sqlUptEnc, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query $sqlUptEnc - ".mysql_error() ) );
						if( !$resUptEnc )
						{
							$data['error']=1;
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


	$q = " SELECT Ccocod,Cconom, Ccocod, Ccosei, Ccomdi
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
	$fechaTitulo = nombreMes( date( "m", $fecMostrarUnix ) ).date( " d ", $fecMostrarUnix )." de ".date( " Y", $fecMostrarUnix );
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
	$fechaTitulo = nombreMes( date( "m", $fecMostrarUnix ) ).date( " d ", $fecMostrarUnix )." de ".date( " Y", $fecMostrarUnix );

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
				a.Pachis, c.Pactid as Pactdo, c.pacced as Pacdoc, c.Pacno1, c.Pacno2, c.Pacap1, c.Pacap2,
                b.Ingnin, b.Ingsei,b.Ingcem ,b.fecha_data as fechaCarpeta
			FROM
				".$wbasedato."_000100 a, ".$wbasedato."_000101 b, root_000036 c
			WHERE ingfei = '".$fechaMostrar."'
			  AND pachis = inghis AND pactdo = pactid AND pacced = pacdoc {$filtroCco}";

	if( $cco_usuario != ""  && $filtrarCcoAyuda == "on" && !$filtrarCcoPorUsuario){
		$sql.= " AND Ingsei = '".$cco_usuario."' ";
	}

	$res = mysql_query( $sql, $conex ) or die( $data[ 'mensaje' ] = mysql_errno()." - Error en el query $sql -".mysql_error() );

	if( $res )
	{
		$num = mysql_num_rows( $res );

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

					$resEnc = mysql_query( $sqlInsert, $conex );

					if ($resEnc)
					{
						$data['mensaje'] = utf8_encode( "Se registró correctamente" );
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

					$resUpdate = mysql_query($sqlUpdate, $conex);

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
		while( $rows = mysqli_fetch_array( $res1, MYSQL_ASSOC ) )
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

		$resInsertLog = mysql_query( $insertLog, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error insertando en la tabla de log de topes ".$wbasedato."_000315 ".mysql_errno()." - Error en el query ".$insertLog." - ".mysql_error() ) );

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

			$res = mysql_query( $resultado, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error insertando en la tabla de topes ".$wbasedato." 204 ".mysql_errno()." - Error en el query $sql - ".mysql_error() ) );

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

			$res = mysql_query( $resultado, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error insertando en la tabla de topes ".$wbasedato." 204 ".mysql_errno()." - Error en el query $sql - ".mysql_error() ) );

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
	$fechaTitulo = nombreMes( date( "m", $fecMostrarUnix ) ).date( " d ", $fecMostrarUnix )." de ".date( " Y", $fecMostrarUnix );


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

	global $conex, $filtraEspecialidadClinica, $wemp_pmla;

	$wbasedatoHce = consultarAplicacion2( $conex, $wemp_pmla, "hce" );
	mysql_select_db("matrix");

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
				  FROM {$aplicacion}_000048
				  LEFT JOIN
				       {$aplicacion}_000044 ON (Medesp=Espcod)
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
			$arrHomoConductas 	= array();
			$sqlHomoCon = "
			SELECT Hctcon, Hctcch, Hctcom, Hctpin
			  FROM ".$wbasedato."_000205
			 WHERE Hctest = 'on'
			";
			$resHomoCon = mysql_query($sqlHomoCon, $conex) or ( $data[ 'mensaje' ] = utf8_encode( "Error en el query sqlHomoCon:$sqlHomoCon - ".mysql_error() ) );
			while($rowHomoCon = mysql_fetch_array($resHomoCon))
			{
				$arrHomoConductas[$rowHomoCon['Hctcon']][$rowHomoCon['Hctcch']]['Especialidad']		= $rowHomoCon['Hctcom'];
				$arrHomoConductas[$rowHomoCon['Hctcon']][$rowHomoCon['Hctcch']]['permiteIngreso']	= $rowHomoCon['Hctpin'];
			}

			$campoConducta		= trim(consultarAliasPorAplicacion($conex, $wemp_pmla, "CampoPlanConductaDeTriageHce"));
			$campoPlanManejo	= trim(consultarAliasPorAplicacion($conex, $wemp_pmla, "CampoPlanDeManejoTriageHCE"));

			$datosHce 			= obtenerDatoHce($rowTurnos['Ahthte'], '1', trim($forYcampoTriage[0]), array($campoConducta));
			$datosHce2 			= obtenerDatoHce($rowTurnos['Ahthte'], '1', trim($forYcampoTriage[0]), array($campoPlanManejo));

			$mostrarTr 			= true;
			$datosHce			= $datosHce[$campoConducta];
			$datosHce 			= explode("-", $datosHce);
			$datosHce 			= $datosHce[0];

			$datosHce2			= $datosHce2[$campoPlanManejo];
			$datosHce2 			= explode("-", $datosHce2);
			$datosHce2 			= $datosHce2[0];

			if(trim($datosHce) != '' || trim($datosHce2) != '')
			{
				$especialidadTriage	= $arrHomoConductas[$campoConducta][$datosHce]['Especialidad'];
				// --> 	Si es un campo que no permite hacer ingreso
				//		osea el campo de alta o de direccionamiento del formulario de triage, estan asignados en "si".
				if($arrHomoConductas[$campoConducta][$datosHce]['permiteIngreso'] != 'on' || $arrHomoConductas[$campoPlanManejo][$datosHce2]['permiteIngreso'] != 'on')
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

function camposVacios($datos){
	$acum = 0;
	foreach( $datos as $key => $value )
	{
		if($value == ''){
			$acum++;
		}
	}

	return $acum;
}