<?php
include_once("conex.php");
//Modificaciones: 2019-06-11 Edwin MG, Se guardan los datos consultados en unix como un arreglo y se registran en matrix en lotes de 1000
//Modificaciones: 2017-07-13 Edwin MG, No se borran de la tabla 141 aqullos cco que sean un botiquin, ya que estos se mueven constantemente en los programas de Gestion de insumos
//Modificaciones: 2013-04-05 Edwin MG, se valida para que se ejecute solo en caso de que alla conexion con UNIX

function insertarRegistros( $conex, $bd, $arts ){

	$fecha	= date( "Y-m-d" );
	$hora 	= date( "H:i:s" );

	$num = count( $arts );
	$ins = 1000;

	if( $num > 0 )
	{
		$i 		= 0;
		$insert = "INSERT INTO {$bd}_000141( Medico, Fecha_data, Hora_data, Salant, Salent, Salsal, Salart, Salser, Seguridad ) VALUES";
		$values = "";

		foreach( $arts as $art )
		{
			$i++;

			// echo "<br>.....sal: ".odbc_result($err_o,1)." ".odbc_result($err_o,2)." ".odbc_result($err_o,3);
			$values .= "( '$bd' ,  '$fecha' ,  '$hora' ,'".$art['Salant']."', '".$art['Salent']."','".$art['Salsal']."','".$art['Salart']."','".$art['Salser']."', 'C-$bd' )";

			//inserto 1000($ins) registros a la vez
			if( $i%$ins == 0 || $i == $num )
			{
				//si entra por aquí es que se va a insertar un grupo de conjuntos
				$sql = $insert.$values.";";

				$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

				$values = "";
			}
			else
			{
				//Si entra por aquí es que NO SE VA A INSERTAR un grupo de registros
				$values .= ",";
			}
		}
	}
}

/**************************************************************************************************
 * Trae los saldos de Unix y solo aquellos que su saldo sea diferente de 0
 **************************************************************************************************/
function saldosPorCco( $cco ){

	global $conex_o;
	global $conex;
	global $bd;

	// $q= "SELECT salant, salent, salsal, salart, salser "
	// ."     FROM ivsal,ivarttar "
	// ."    WHERE arttarcod = salart "
	// ."      and salano = '".date('Y')."' "
	// ."      and salmes = '".date('m')."' "
	// ." GROUP BY salant, salent, salsal, salart, salser ";

	$q= "SELECT salant, salent, salsal, salart, salser "
	."     FROM ivsal "
	."    WHERE salano = '".date('Y')."' "
	."      and salmes = '".date('m')."' "
	."      and salser = '".$cco."' "
	;

	$err_o= odbc_do($conex_o,$q);

	$arts = [];

	while(odbc_fetch_row($err_o))
	{
		$arts[] = [
					'Salant' => odbc_result($err_o,1),
					'Salent' => odbc_result($err_o,2),
					'Salsal' => odbc_result($err_o,3),
					'Salart' => odbc_result($err_o,4),
					'Salser' => odbc_result($err_o,5),
				];
	}

	liberarConexionOdbc($conex_o);
	odbc_close_all();

	$sql = "DELETE FROM {$bd}_000141 WHERE Salser = '".$cco."'";
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	insertarRegistros( $conex, $bd, $arts );
}

/**************************************************************************************************
 * Trae los saldos de Unix y solo aquellos que su saldo sea diferente de 0
 **************************************************************************************************/
function saldos(){

	global $conex_o;
	global $conex;
	global $bd;


	// $sql = "TRUNCATE TABLE {$bd}_000141";
	$sql = "SELECT Ccoori
			  FROM {$bd}_000058
			 WHERE Ccoest = 'on'
		  GROUP BY 1
			";
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$ccos = "('')";
	for( $i = 0; $rows = mysql_fetch_array($res); $i++ ){
		if( $i == 0 )
			$ccos = "('".$rows['Ccoori']."'";
		else
			$ccos .= ",'".$rows['Ccoori']."'";
	}
	$ccos .= ")";

	// $q= "SELECT salant, salent, salsal, salart, salser "
	// ."     FROM ivsal,ivarttar "
	// ."    WHERE arttarcod = salart "
	// ."      and salano = '".date('Y')."' "
	// ."      and salmes = '".date('m')."' "
	// ." GROUP BY salant, salent, salsal, salart, salser ";

	$q= "SELECT salant, salent, salsal, salart, salser "
	."     FROM ivsal "
	."    WHERE salano = '".date('Y')."' "
	."      and salmes = '".date('m')."' "
	."      and salser NOT IN ".$ccos;
	;

	$err_o= odbc_do($conex_o,$q);

	$arts = [];

	while(odbc_fetch_row($err_o))
	{
		$arts[] = [
					'Salant' => odbc_result($err_o,1),
					'Salent' => odbc_result($err_o,2),
					'Salsal' => odbc_result($err_o,3),
					'Salart' => odbc_result($err_o,4),
					'Salser' => odbc_result($err_o,5),
				];
	}

	liberarConexionOdbc($conex_o);
	odbc_close_all();

	$fecha = date( "Y-m-d" );
	$hora = date( "H:i:s" );

	// $sql = "TRUNCATE TABLE {$bd}_000141";
	$sql = "DELETE FROM {$bd}_000141
				  WHERE Salser NOT IN(
					SELECT Ccoori
					  FROM {$bd}_000058
					 WHERE Ccoest = 'on'
				  GROUP BY 1
				  )
			";
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	insertarRegistros( $conex, $bd, $arts );
}

function saldosConsolidados(){
	global $conex;
	global $bd;
	global $bdCliame;

	$fecha = date( "Y-m-d" );
	$hora  = date( "H:i:s" );

	$query = "TRUNCATE TABLE {$bdCliame}_000322 ";
	$rs    = mysql_query( $query, $conex );

	$query = "  INSERT INTO {$bdCliame}_000322 ( `Medico`, `Fecha_data`, `Hora_data`, `Salart`, `Salsal`, `Salest`, `Seguridad`)
				SELECT 'cliame', '$fecha', '$hora',  Salart, sum(salsal), 'on', 'C-cliame'
	              FROM  {$bd}_000141
	             GROUP BY 1,2,3,4,6,7";
	$rs    = mysql_query( $query, $conex );

}


// include_once("movhos/validacion_hist.php");
// include_once("movhos/fxValidacionArticulo.php");
// include_once("movhos/registro_tablas.php");
include_once("movhos/otros.php");
include_once("root/comun.php");




$bd = "movhos";
$bdCliame = "cliame";

connectOdbc($conex_o, 'inventarios');

if($conex_o != 0){

	if( empty( $cco ) ){
		saldos();
		saldosConsolidados();
	}else{
		saldosPorCco( $cco );
	}

	// liberarConexionOdbc($conex_o);
	// odbc_close_all();
}
?>