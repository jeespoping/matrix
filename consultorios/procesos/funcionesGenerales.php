<?php
include_once("conex.php");
/*********************************************************************************************************
 * 											CLASES
 *********************************************************************************************************/

/**
 * Consulta el grupo de un usuario
 * 
 * @param $codigo
 * @return unknown_type
 */
function consultarGrupo( $codigo ){
	
	global $conex;
	global $wbasedato;
	
	$val = '';
	
	$sql = "SELECT
				grupo
			FROM
				usuarios
			WHERE
				codigo = '$codigo'
				AND Activo = 'A'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		
		$val = $rows['grupo'];
	}
	
	return $val;
}

/**
 * Calcula la edad de la persona de acuerdo a la fecha de nacimiento
 * 
 * @param date $fnac		Fecha de nacimientos				
 * @return entero			Edad de la persona
 */
function calculoEdad( $fnac ){
	
	$edad = 0;
	
	$nac = explode( "-", $fnac );				//fecha de nacimiento
	$fact = date( "Y-m-d" );					//fecha actual

	if( count($nac) == 3 ){
		$edad = date("Y") - $nac[0];
		
		if( date("Y-m-d") < date( "Y-".$nac[1]."-".$nac[2] ) ){
			$edad--;
		}
	}
		
	return $edad;
}

/**
 * Codigo Cie10
 * 
 * @param $cod
 * @return unknown_type
 */
function consultarDescripcionCie10( $cod ){

	global $conex;
	global $wbasedato;
	
	$cie10 = '';
	
	$sql = "SELECT
				descripcion
			FROM
				root_000011
			WHERE
				codigo = '$cod'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$cie10 = $rows['descripcion'];
	}
	
	return $cie10;
	
}

/**
 * 
 * @param $his
 * @param $ing
 * @param $fecha
 * @return unknown_type
 */
function consultarDx( $his, $ing, $fecha ){
	
	global $conex;
	global $wbasedato;
	
	$dx = '';
	
	$sql = "SELECT
				hcldxp
			FROM
				{$wbasedato}_000001
			WHERE
				hclhis = '$his'
				AND hcling like '%$ing'
				AND hclfec = '$fecha'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		
		$dx = $rows['hcldxp'];
		$dx .= "-".consultarDescripcionCie10( $rows['hcldxp'] );
	}
	
	return $dx;
}

/**
 * Busca la información Demografica de un paiciente de un paciente
 * 
 * @param $his			Historia
 * @param $info
 * @return unknown_type
 */
function infoPaciente( $his, &$info ){
	
	global $conex;
	global $wbasedato;
	
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000002
			WHERE
				pachis = '$his'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$info = $rows;
	}
	
}

class classEspecialidad{
	
	var $codigo;
	var $descripcion;
	
	// function classEspecialidad( $codigo ){
	function __construct( $codigo ){
		
		global $conex;
		
		$sql = "SELECT
					*
				FROM
					root_000056 a
				WHERE
					espcod = '$codigo'
					AND espest = 'on'
				";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$numrows = mysql_num_rows( $res );
		
		if( $rows = mysql_fetch_array( $res ) ){
			$this->codigo =  $rows['Espcod'];
			$this->descripcion =  $rows['Espdes'];
		}
		
	}
};

class classConsultorio{

	var $codigo;
	var $descripcion;
	var $telefono;
	var $fax;
	var $direccion;
	
	// function classConsultorio( $codigo ){
	function __construct( $codigo ){
		
		global $conex;
		
		$sql = "SELECT
					*
				FROM
					root_000057 a
				WHERE
					Concod = '$codigo'
					AND Conest = 'on'
				";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$numrows = mysql_num_rows( $res );
			
		if( $rows = mysql_fetch_array( $res ) ){
			$this->codigo = $rows['Concod'];
			$this->descripcion = $rows['Condes'];
			$this->telefono = $rows['Contel'];
			$this->fax = $rows['Confax'];
			$this->direccion = $rows['Condir'];
		}
	}
	
};

class classMedico{
	
	var $nombre;
	var $nroIdentificacion;
	var $direccion;
	var $consultorio;
	var $celular;
	var $fax;
	var $secretaria;
	var $telefono;
	var $bdHC;
	var $bdCitas;
	var $registro;
	var $permiteCitas;
	var $especialidad;
	var $codigo;
	var $bdEmpresas;
	var $grupoSolucion;
	
	// function classMedico( $user ){
	function __construct( $user ){
		
		global $conex;
		
		//Buscando los datos del doctor
		$sql = "SELECT
					*
				FROM
					root_000055 a
				WHERE
					medusu = '$user'
					AND medest = 'on'
				";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$numrows = mysql_num_rows( $res );
		
		if( $rows = mysql_fetch_array( $res ) ){
		
			$this->nombre = $rows['Mednom'];
			$this->nroIdentificacion = $rows['Medced'];
			$this->direccion = $rows['Meddir'];
			$this->celular = $rows['Medcel'];
			$this->fax = $rows['Medfax'];
			$this->secretaria = $rows['Medsec'];
			$this->telefono = $rows['Medtel'];
			$this->bdHC = $rows['Medbhc'];
			$this->bdCitas = $rows['Medbci'];
			$this->registro = $rows['Medreg'];
			$this->permiteCitas = $rows['Medcit'];
			$this->codigo = $user;
			$this->bdEmpresas = $rows[ 'Medemp' ];

			$this->consultorio = new classConsultorio( $rows['Medcon'] );
			$this->especialidad = new classEspecialidad(  $rows['Medesp'] );
			
			$this->grupoSolucion = consultarGrupo( $this->bdHC );
		}
	}
	
};

/*********************************************************************************************************
 * 											FIN DE CLASES
 *********************************************************************************************************/

/*********************************************************************************************************
 * 											  FUNCIONES
 *********************************************************************************************************/

/**
 * Devuelve el nombre del mes comenzando desde 1 para Enero
 * 
 * @param $mes		Nro de Mes
 * @return unknown_type
 */
function nombreMes( $mes ){
	
	switch( $mes ){
		
		case 1:
			return "Enero"; 
			break;
		case 2: 
			return "Febrero"; 
			break;
		case 3: 
			return "Marzo"; 
			break;
		case 4:
			return "Abril"; 
			break; 

		case 5:
			return "Mayo"; 
			break;
			
		case 6:
			return "Junio"; 
			break;

		case 7:
			return "Julio"; 
			break;
		case 8:
			return "Agosto"; 
			break;
		
		case 9: 
			return "Septiembre";
			break;
			
		case 10:
			return "Octubre";
			break;
		
		case 11:
			return "Noviembre";
			break;
			
		case 12:
			return "Diciembre";
			break;
	}
	
}

/*********************************************************************************************************
 * 											  FIN DE FUNCIONES
 *********************************************************************************************************/
?>