<?php
include_once("conex.php");
/********************************************************************************
 * Septiembre 16 de 2011 (Edwin MG)
 * Desconfirma articulos o desaprueba el perfil segun la parametrizacion por 
 * tipos de articulo
 ********************************************************************************/
 /************************************************************************************************************************
 * Determina si un articulo es generico y devuelve el tipo de articulo al que pertenece
 * 
 * @param $conexion
 * @param $wbasedatoMH
 * @param $wbasedatoCM
 * @param $codArticulo
 * @return unknown_type
 *
 * Modificacon: 
 * Agosto 24 de 2015.	(Edwin MG).	El proceso solo se hace para pacientes que tengan kardex y no ordenes
 * Agosto 8 de 2011.	(Edwin MG).	Si un articulo es de central de mezclas pero no tiene tipo, se hace aparte y se coloca como asignacion
 *			    					de tipo
 ************************************************************************************************************************/
 function esArticuloGenerico( $conex, $wbasedatoMH, $wbasedatoCM, $codArticulo ){
	
	//Consulto de que tipo es el articulo
	$sql = "SELECT
				*
			FROM
				{$wbasedatoMH}_000068,
				{$wbasedatoCM}_000002,
				{$wbasedatoCM}_000001
			WHERE
				artcod = '$codArticulo'
				AND arttip = tipcod
				AND tiptpr = arktip
				AND artest = 'on'
				AND arkest = 'on'
				AND tipest = 'on' 
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query - ".mysql_errno()  );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		
		$rows = mysql_fetch_array( $res );
		return $rows['Arktip'];
	}
	else{
	
		/********************************************************************************
		 * Verifico que sea de la central y tenga tipo
		 * Si es de la central y no tiene tipo se agrega en un campo nuevo
		 ********************************************************************************/
		
		$sql = "SELECT
					*
				FROM
					{$wbasedatoCM}_000002,
					{$wbasedatoCM}_000001
				WHERE
					artcod = '$codArticulo'
					AND arttip = tipcod
					AND artest = 'on'
					AND tipest = 'on' 
				";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query - ".mysql_errno()  );
		$numrows = mysql_num_rows( $res );
		
		if( $numrows > 0 ){	//si es de central
			
			$rows = mysql_fetch_array( $res );
			
			
			if( empty( $rows['Tiptpr'] ) || trim( $rows['Tiptpr'] ) == '' || strtoupper( trim( $rows['Tiptpr'] ) ) == 'NO' ){ //Si no tiene tipo de articulo, se asigna a uno
				return "SADT";	//Sin Asignacion de Tipo
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
	}
}

/****************************************************************************************
  * Consulta si la preparacion de un articulo fue confirmado en el dia actual
  ****************************************************************************************/
function consultarAprobacionHoy( $conex, $wbasedato, $historia, $ingreso, $articulo ){

	$val = false;

	$sql = "SELECT
				Kadart, Kadfin, Kadhin, Kaudes, Kaumen
			FROM
				{$wbasedato}_000054 a, 
				{$wbasedato}_000055 b
			WHERE
				kadfec = '".date( "Y-m-d" )."'
				AND kadhis = '$historia'
				AND kading = '$ingreso'
				AND kadhis = kauhis
				AND kading = kauing
				AND kaufec = kadfec
				AND kaumen = 'Articulo aprobado'
				AND kaudes like '%$articulo%'
			"; //echo "<br>.......<pre>$sql</pre>";
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){

			$datos = explode( ",", $rows['Kaudes'] );

			if( trim( $datos[0] ) == trim( $rows['Kadart'] ) && $rows['Kadfin'] == $datos[ 2 ] && $rows['Kadhin'] == $datos[ 3 ] ){
				$val = true;
			}			
		}
	}
	
	return $val;
}
 

 /****************************************************************************************
  * Consulta si la preparacion de un articulo fue confirmado en el dia actual
  ****************************************************************************************/
function consultarConfirmacionHoy( $conex, $wbasedato, $historia, $ingreso, $articulo ){

	$val = false;

	$sql = "SELECT
				Kadfin, Kadhin, Kaudes, Kaumen
			FROM
				{$wbasedato}_000054 a, 
				{$wbasedato}_000055 b
			WHERE
				kadfec = '".date( "Y-m-d" )."'
				AND kadhis = '$historia'
				AND kading = '$ingreso'
				AND kadhis = kauhis
				AND kading = kauing
				AND kaufec = kadfec
				AND ( kaumen = 'Articulo actualizado'
				 OR kaumen = 'Articulo ha sido reemplazado desde el perfil farmacologico'
				 OR kaumen = 'Articulo creado'
				 OR kaumen = 'Articulo ha sido reemplazado desde cargos de central de mezclas'
				)
				AND kaudes like '%$articulo%'
			"; //echo "<br>.......<pre>$sql</pre>";
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){

			@list( $antes, $nuevo ) = explode( "N:", $rows[ 'Kaudes' ] );
			$antes = trim( $antes );
			
			$explodeNuevo = explode( ",", trim( $nuevo ) );
			
			if( trim( $rows[ 'Kaumen' ] ) == 'Articulo creado' ||  trim( $rows[ 'Kaumen' ] ) == 'Articulo actualizado' ){
				
				//Busco si fue confirmado, al septima posicion lo indica
				if( trim( $explodeNuevo[ 7 ] ) == 'on' ){
					$val = true;
					break;
				}
			}
			else{	//Si el articulo fue reemplazado entonces fue confirmado
				if( trim( strtoupper( $explodeNuevo[0] ) ) == strtoupper( $articulo ) ){
					$val = true;
					break;
				}				
			}
		}
	}
	
	return $val;
}

 /****************************************************************************************
 *	Desconfirma un articulo segun el id del registro que se pasa por parametro
 ****************************************************************************************/ 
function desconfirmarArticulo( $conex, $wbasedato, $idRegistro ){

	$val = false;

	$sql = "UPDATE
				{$wbasedato}_000054
			SET
				kadcon = 'off'
			WHERE
				id='$idRegistro'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows() > 0 ){
		$val = true;
	}
	
	return $val;
}
 
/****************************************************************************************
 *	Desaprueba el perfil segun el id que se le pase por parametro
 ****************************************************************************************/ 
function desaprobarPerfil( $conex, $wbasedato, $idRegistro ){

	$val = false;

	$sql = "UPDATE
				{$wbasedato}_000054
			SET
				kadare = 'off'
			WHERE
				id='$idRegistro'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows() > 0 ){
		$val = true;
	}
	
	return $val;
}
 
/********************************************************************************
 * Consulta los articulos que pueden ser desconfirmado o desaprobados y los pongo en
 * un arrya con la informacion correspondiente
 ********************************************************************************/
function consultarInformacion( $conex, $wbasedato ){

	$val = Array();
	
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000099
			WHERE
				tarest = 'on'
				AND ( tardco = 'on'
				OR tardap = 'on' )
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
		
		$val[ $rows['Tarcod'] ]['desconfirmar'] = ( $rows['Tardco'] == 'on' ) ? true: false;
		$val[ $rows['Tarcod'] ]['horaDesconfimar'] = strtotime( date( "Y-m-d" )." ".$rows[ 'Tarhdc' ] );
		$val[ $rows['Tarcod'] ]['desaprobar'] = ( $rows['Tardap'] == 'on' ) ? true: false;
		$val[ $rows['Tarcod'] ]['horaDesaprobar'] = strtotime( date( "Y-m-d" )." ".$rows['Tarhda'] );
	}
	
	return $val;
}

/********************************************************************************
 * Septiembre 16 de 2011  
 * Busco todos los articulos posibles que puedan ser desconfirmados
 ********************************************************************************/
function desconfirmando( $conex, $wbasedato, $wcenmez, $fecha ){
	
	$info = consultarInformacion( $conex, $wbasedato );
	
	$sql = "SELECT
				d.*
			FROM
				{$wbasedato}_000011 a,
				{$wbasedato}_000020 b,
				{$wbasedato}_000053 c,
				{$wbasedato}_000054 d
			WHERE
				ccocpx		= 'on'
				AND ccoest  = 'on'
				AND habcco  = ccocod
				AND karhis  = habhis
				AND karing  = habing
				AND kadhis  = karhis
				AND kading  = karing
				AND kadsus != 'on'
				AND kadess != 'on'
				AND c.fecha_data = '".date( "Y-m-d" )."'
				AND kadfec = c.fecha_data
				AND kadcco = karcco
				AND karord != 'on'
			UNION
			SELECT
				d.*
			FROM
				{$wbasedato}_000011 a,
				{$wbasedato}_000020 b,
				{$wbasedato}_000053 c,
				{$wbasedato}_000054 d
			WHERE
				ccocpx		= 'on'
				AND ccoest  = 'on'
				AND habcco  = ccocod
				AND karhis  = habhis
				AND karing  = habing
				AND kadhis  = karhis
				AND kading  = karing
				AND kadsus != 'on'
				AND kadess != 'on'
				AND c.fecha_data = '".date( "Y-m-d" )."'
				AND kadfec = c.fecha_data
				AND kadcco = karcco
				AND ccoior != 'on'
			"; //echo "<br>.......<pre>$sql</pre>";
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
		
			$esGenerico = esArticuloGenerico( $conex, $wbasedato, $wcenmez, $rows['Kadart'] );
			
			if( $esGenerico ){
				$tipo = $esGenerico;
			}
			else{
				$tipo = $rows[ 'Kadpro' ];
			}
		
			if( isset( $info[ $tipo ] ) && count( $info[ $tipo ] ) > 1 ){
				//Se verifica si es la hora para desconfirmar o aprobar perfil
				if( ( $info[ $tipo ]['desconfirmar'] && time() >= $info[ $tipo ]['horaDesconfimar'] ) || (@$info[ $tipo ]['desaprobar'] == true && time() >= @$info[ $tipo ]['horaDesaprobar']) ){
				
					if( $rows['Kadori'] == "CM" ){	//Si es de Central de Mezclas
						//Si es de central de mezclas se puede desconfirmar el articulo
						//Si se desconfimra el articula tambien se desaprueba el perfil
						if( $info[ $tipo ]['desconfirmar'] && time() >= $info[ $tipo ]['horaDesconfimar'] ){
							$confirmado = consultarConfirmacionHoy( $conex, $wbasedato, $rows['Kadhis'], $rows['Kading'], $rows['Kadart'] );
						
						    if( !$confirmado && $rows['Kadcon'] == "on"){
								desconfirmarArticulo( $conex, $wbasedato, $rows[ 'id' ] );	//Desconfirmo el articulo
								desaprobarPerfil( $conex, $wbasedato, $rows[ 'id' ] );		//Desapruebo el perfil
							}
						}
						elseif( $info[ $tipo ]['desaprobar'] && time() >= $info[ $tipo ]['horaDesaprobar'] ){	//Si no verifico si solo es desaprobar el perfil
							
							$esAprobadoHoy = consultarAprobacionHoy( $conex, $wbasedato, $rows['Kadhis'], $rows['Kading'], $rows['Kadart'] );
							
							if( $rows['Kadare'] == "on" && !$esAprobadoHoy ){
								desaprobarPerfil( $conex, $wbasedato, $rows[ 'id' ] );	//Desapruebo el perfil
							}
						}
					}
					else{	//Si es de Servicio Farmaceutico

						//El articulo es de SF
						//Si es de SF solo se desaprobueba el perfil
						if( $info[ $tipo ]['desaprobar'] && time() >= $info[ $tipo ]['horaDesaprobar'] ){
						
							$esAprobadoHoy = consultarAprobacionHoy( $conex, $wbasedato, $rows['Kadhis'], $rows['Kading'], $rows['Kadart'] );
						
							if( $rows['Kadare'] == "on" && !$esAprobadoHoy ){
								desaprobarPerfil( $conex, $wbasedato, $rows[ 'id' ] );
							}
						}
					}
				}
			}				
		}
	}
}

include_once("root/comun.php"); 


mysql_select_db("matrix") or die("No se selecciono la base de datos");

$wbasedato = consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );
$wcenmez = consultarAliasPorAplicacion( $conex, $wemp_pmla, "cenmez" );

//$corteKardex = consultarHoraCorteKardex( $conex );
			
if( true ){

	$fecha = date( "Y-m-d" );
	
	if( true ){
		desconfirmando( $conex, $wbasedato, $wcenmez, $fecha );
	}
}
?>