<?php
include_once("conex.php");
/******************************************************************************************************************************
 * Actualizaciones
 *
 * Febrero 20 de 2017	Edwin MG:			- Para la funcion maestro_medicamentos_empresas se valida que el registro no exista en matrix
 * Mayo 27 de 2016		Edwin MG:			- Para el método maestroExamenes se agrega el la función de php mysql_escape_string
 *											  Para que escape los carecteres especiales y no se dañe las consultas de inserción
 * 											  y acutalización de las tablas root_000012 y hce_000017
 ******************************************************************************************************************************/
// include_once("root/comun.php");

//Se llamará datosDeUnix en un archivo nuevo kron_maestro_unix.php
class datosDeUnix extends conex_unix
{

	/******************************************************************************************
	 * Metodo constructor de clase.
	 * Crea una conexión con Matrix y ejecuta el constructor de su clase padre conex_unix
	 ******************************************************************************************/
	function datosDeUnix( $fn = '' ){

		global $conex;

		parent::__construct();

		$this->conex = $conex;

		if( $conex ){
			

		}

		//Si se ingreso parametro es un proceso(metodo) a ejecutar
		if( !empty( $fn ) ){
			$this->$fn();
		}
	}


	/**********************************************************************
	 * Maestro de tarifas
	 **********************************************************************/
	function maestroTarifas(){

		$wbasedato = $this->consultarAliasPorAplicacion( 'facturacion' );

		$this->conexionOdbc( 'admisiones' );

		$sql = "SELECT
					*
				FROM
					{$wbasedato}_000025
				WHERE
					fecha_data = '".date( "Y-m-d" )."'
					AND id = 1
				";

		$res = mysql_query( $sql, $this->conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );

		if( $num == 0 ){

			if( $this->conex_u ){

				$sql = "SELECT
							tarcod, tarnom
						FROM
							INTAR
						WHERE
							taract = 'S'
						";

				$res = odbc_do( $this->conex_u, $sql );

				if( $res ){

					$i = 0;
					while( odbc_fetch_row($res) )
					{
						if( $i == 0 ){
							$sqlTruncate = "TRUNCATE TABLE  {$wbasedato}_000025";

							$resTruncate = mysql_query( $sqlTruncate, $this->conex ) or die( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() );
						}

						$sqlInsert = "INSERT INTO {$wbasedato}_000025(    Medico   ,   Fecha_data         ,      Hora_data       ,           Tarcod                 ,             Tardes                , Tarest,    Seguridad     ) "
									."			      		  VALUES ( '$wbasedato', '".date( "Y-m-d" )."', '".date( "H:i:s" )."', '".trim( odbc_result($res,1) )."', '".trim( odbc_result($res,2) )."' ,  'on' , 'C-{$wbasedato}' ) ";

						$resInsert = mysql_query( $sqlInsert, $this->conex ) or die( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() );

						if( mysql_affected_rows() > 0 ){
							// echo "<br>Inserto: ".trim( odbc_result($res,1) )." - ".trim( odbc_result($res,2) );
						}
						else{
							// echo "<br>No inserto: ".trim( odbc_result($res,1) )." - ".trim( odbc_result($res,2) );
						}

						$i++;
					}
				}
				else{
					echo "Error en el query $sql";
				}
			}
			else{
				echo "NO SE CONECTO A UNIX";
			}
		}
		//2014-12-29
		liberarConexionOdbc( $this->conex_u );
		odbc_close_all();
	}

	 // Función que conculta las empresas responsables de unix y las registra en la tabla 000024 de la empresa actual
	function maestroEmpresa()
	{
		global $wemp_pmla;
		$wbasedato 			= $this->consultarAliasPorAplicacion('facturacion');
		$conex_o 			= $this->conexionOdbc('facturacion');
		$conex 				= $this->conex;

		// Consulto si ya se ha actualizado la tabla de empresas en matrix hoy
		$sql = "SELECT *
				  FROM {$wbasedato}_000024
				 WHERE fecha_data = '".date( "Y-m-d" )."'
				   AND id = 1";
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );

		// Si no se ha actualizado la tabla de empresas en matrix hoy
		if( $num == 0 )
		{
			// Si existe conexión a Unix
			if( $conex_o )
			{
				// --> 2014-07-31, jerson Andres Trujillo
				$arrayEmpresas = array();
				$SQLemp = "SELECT Empcod
							 FROM ".$wbasedato."_000024
				";
				$RESemp = mysql_query($SQLemp, $conex) or die("ERROR EN QUERY MATRIX (SQLemp): ".mysql_errno());
				while($rowEmp = mysql_fetch_array($RESemp))
					$arrayEmpresas[$rowEmp['Empcod']] = '';

				// consulto los datos de las empresas responsables en Unix
				$sql = "SELECT empcod, empnit, empnom, '' as  emptel, '' as  empdir, empmun, '' as empres, emppla, emppor, empact, empcop, empnca,
							   empcla, emptip, emptar, empbmp, empaev, empsme, empcde, empcas, empuad, empfad, empumo, empfmo
						  FROM INEMP
						 WHERE empres is null
						   AND empdir is null
						   AND emptel is null
						 UNION
						SELECT empcod, empnit, empnom, emptel, '' as  empdir, empmun, '' as empres, emppla, emppor, empact, empcop, empnca,
							   empcla, emptip, emptar, empbmp, empaev, empsme, empcde, empcas, empuad, empfad, empumo, empfmo
						  FROM INEMP
						 WHERE empres is null
						   AND empdir is null
						   AND emptel is not null
						 UNION
						SELECT empcod, empnit, empnom, '' as  emptel, empdir, empmun, '' as empres, emppla, emppor, empact, empcop, empnca,
							   empcla, emptip, emptar, empbmp, empaev, empsme, empcde, empcas, empuad, empfad, empumo, empfmo
						  FROM INEMP
						 WHERE empres is null
						   AND empdir is not null
						   AND emptel is null
						 UNION
					    SELECT empcod, empnit, empnom, emptel, empdir, empmun, '' as empres, emppla, emppor, empact, empcop, empnca,
							   empcla, emptip, emptar, empbmp, empaev, empsme, empcde, empcas, empuad, empfad, empumo, empfmo
						  FROM INEMP
						 WHERE empres is null
						   AND empdir is not null
						   AND emptel is not null
						 UNION
						SELECT empcod, empnit, empnom, '' as  emptel, '' as empdir, empmun, empres, emppla, emppor, empact, empcop, empnca,
							   empcla, emptip, emptar, empbmp, empaev, empsme, empcde, empcas, empuad, empfad, empumo, empfmo
						  FROM INEMP
						 WHERE empres is not null
						   AND empdir is null
						   AND emptel is null
						 UNION
						SELECT empcod, empnit, empnom, emptel, '' as  empdir, empmun, empres, emppla, emppor, empact, empcop, empnca,
							   empcla, emptip, emptar, empbmp, empaev, empsme, empcde, empcas, empuad, empfad, empumo, empfmo
						  FROM INEMP
						 WHERE empres is not null
						   AND empdir is null
						   AND emptel is not null
						 UNION
						SELECT empcod, empnit, empnom, '' as  emptel, empdir, empmun, empres, emppla, emppor, empact, empcop, empnca,
							   empcla, emptip, emptar, empbmp, empaev, empsme, empcde, empcas, empuad, empfad, empumo, empfmo
						  FROM INEMP
						 WHERE empres is not null
						   AND empdir is not null
						   AND emptel is null
						 UNION
						SELECT empcod, empnit, empnom, emptel, empdir, empmun, empres, emppla, emppor, empact, empcop, empnca,
							   empcla, emptip, emptar, empbmp, empaev, empsme, empcde, empcas, empuad, empfad, empumo, empfmo
						  FROM INEMP
						 WHERE empres is not null
						   AND empdir is not null
						   AND emptel is not null";

						   //	    1     2        3        4     5          6     7       8         9   10        11     12      13      14      15      16      17      18      19      20      21      22       23    24
				$campos       = "empcod, empnit, empnom, emptel, empdir, empmun, empres, emppla, emppor, empact, empcop, empnca,empcla, emptip, emptar, empbmp, empaev, empsme, empcde, empcas, empuad, empfad, empumo, empfmo";
				$campos       = "empcod, empnit, empnom, emptel, empdir, empmun, empres, emppla, emppor, empact, empcop, empnca,empcla, emptip, emptar, empdetadm";
				$tablas       = "INEMP, INEMPDET";
				$where        = "empcod = empdetcod";

							//		7      5       6        16    17       18      19      20    21
				$campos_nulos = "empres, empdir, emptel, empbmp, empaev, empsme, empcde, empcas, empuad, empfad, empumo, empfmo";
				$campos_nulos = "empres, empdir, emptel";
				$sql          = $this->construirQueryUnix( $tablas,$campos_nulos,$campos,$where );


				$res = odbc_do( $conex_o, $sql );

				// Si se encontraron empresas en Unix se recorren para hacer las actualizaciones requeridas
				if( $res )
				{
					while( odbc_fetch_row($res) )
					{
						// --> 2014-07-31, Jerson Andres Trujillo
						// --> Si ya existe el registro de la empresa entonces lo actualizo
						$estadoEmpresa = "on";
						if( odbc_result($res,10) != "S"  ){
							$estadoEmpresa = "off";
						}
						if(array_key_exists(trim(odbc_result($res,1)), $arrayEmpresas))
						{
							$arrayEmpresas[trim(odbc_result($res,1))] = TRUE;
							$sqlUpdate = "
							UPDATE ".$wbasedato."_000024
							   SET Fecha_data 	= '".date( "Y-m-d" )."',
								   Hora_data  	= '".date( "H:i:s" )."',
								   Empnit 		= '".trim( odbc_result($res,2) )."',
								   Empres 		= '".trim( odbc_result($res,2) )."',
								   Empnom 		= '".trim( odbc_result($res,3) )."',
								   Emptar 		= '".trim( odbc_result($res,15) )."',
								   Empcon 		= '".trim( odbc_result($res,7) )."',
								   Empdir 		= '".trim( odbc_result($res,5) )."',
								   Emptel 		= '".trim( odbc_result($res,4) )."',
								   Emptem 		= '".trim( odbc_result($res,14) )."',
								   Empcop 		= '".trim( odbc_result($res,11) )."',
								   Empest       = '{$estadoEmpresa}',
								   Empcmi 		= '".trim( odbc_result($res, "empdetadm") )."'
							 WHERE Empcod 		= '".trim( odbc_result($res,1) )."'
							";

							$resInsert = mysql_query( $sqlUpdate, $conex ) or die( mysql_errno()." - Error en el query $sqlUpdate - ".mysql_error() );
						}else{// --> Si no existe lo ingreso como uno nuevo
							// Se registra la fila actual de Unix en la tabla de empresas responsables de Matrix
							$sqlInsert = "	INSERT INTO {$wbasedato}_000024
														(Medico,		Fecha_data,				Hora_data,				Empcod,								Empnit,								Empres,								Empnom,								Emptar,								Empcon,								Empdir,								Emptel,								Empmai,	Empcmi,											Empnma,Empdfi,Empdia,Emppdt,Empprt,Emptem,Empfac,Empest,Empmed,Emppro,Emprem,Emprip,Empftr,Empdiv,Empraz,Empnra,Empdgn,Empran,Emptde, Empcop,Seguridad) "
										."		VALUES  ('$wbasedato', 	'".date( "Y-m-d" )."', 	'".date( "H:i:s" )."', 	'".trim( odbc_result($res,1) )."', 	'".trim( odbc_result($res,2) )."' , '".trim( odbc_result($res,2) )."', 	'".trim( odbc_result($res,3) )."', 	'".trim( odbc_result($res,15) )."', '".trim( odbc_result($res,7) )."', 	'".trim( odbc_result($res,5) )."', 	'".trim( odbc_result($res,4) )."',  '0' ,  	'".trim( odbc_result($res, "empdetadm") )."',  	'0' ,  '0' ,  '0' ,  '0' ,  '0' , '".trim( odbc_result($res,14) )."',   '' , '{$estadoEmpresa}' ,  ''    ,  ''   , 'off' , 'off' , '0.0' ,   ''  ,  ''   ,   ''  ,   ''  ,   ''  , '' , '".trim( odbc_result($res,11) )."'  , 'C-{$wbasedato}' ) ";

							$resInsert = mysql_query( $sqlInsert, $conex ) or die( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() );
						}
					}

					$codEmpParticular = $this->consultarAliasPorAplicacion('codigoempresaparticular');
					$arrayEmpresas[$codEmpParticular] = TRUE;

					// --> Inactivar las empresas que no fueron actualizadas desde unix
					foreach($arrayEmpresas as $codEmp => $actualizada)
					{
						if(!$actualizada)
						{
							$sqlInact = "
							UPDATE ".$wbasedato."_000024
							   SET Empest = 'off'
							 WHERE Empcod = '".$codEmp."'
							";
							mysql_query( $sqlInact, $conex ) or die( mysql_errno()." - Error en el query $sqlInact - ".mysql_error() );
						}
					}
				}
				else
				{
					echo "<br>Error en el query $sql<br>";
				}
			}
			else
			{
				echo "NO HAY CONEXIÓN CON UNIX";
			}
		}
	}

	/**********************************************************************
	 * Maestro de municipios desde unix
	 **********************************************************************/
	function maestroDepartamentos(){

		$wbasedato = "root";

		$this->conexionOdbc( 'admisiones' );

		$sql = "SELECT
					*
				FROM
					root_000002
				WHERE
					fecha_data = '".date( "Y-m-d" )."'
					AND id = 1
				";

		$res = mysql_query( $sql, $this->conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );

		if( $num == 0 ){

			if( $this->conex_u ){

				$sql = "SELECT
							depcod, depnom
						FROM
							INDEP
						WHERE
							depact = 'S'
						";

				$res = odbc_do( $this->conex_u, $sql );

				if( $res ){

					$i = 0;
					while( odbc_fetch_row($res) )
					{
						if( $i == 0 ){
							$sqlTruncate = "TRUNCATE TABLE  root_000002";

							$resTruncate = mysql_query( $sqlTruncate, $this->conex ) or die( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() );
						}

						$codigoPais = "169";

						if( trim( odbc_result($res,1) ) == '01' ){
							$codigoPais = "01";
						}

						$sqlInsert = "INSERT INTO root_000002(    Medico   ,   Fecha_data         ,      Hora_data       ,           Codigo                 ,             Descripcion          ,    codigoPais     ,     Seguridad    ) "
									."			      VALUES ( '$wbasedato', '".date( "Y-m-d" )."', '".date( "H:i:s" )."', '".trim( odbc_result($res,1) )."', '".trim( odbc_result($res,2) )."',  '".$codigoPais."', 'C-root' ) ";

						$resInsert = mysql_query( $sqlInsert, $this->conex ) or die( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() );

						if( mysql_affected_rows() > 0 ){
							// echo "<br>Inserto: ".trim( odbc_result($res,1) )." - ".trim( odbc_result($res,2) );
						}
						else{
							// echo "<br>No inserto: ".trim( odbc_result($res,1) )." - ".trim( odbc_result($res,2) );
						}

						$i++;
					}
				}
				else{
					echo "Error en el query $sql";
				}
			}
			else{
				echo "NO SE CONECTO A UNIX";
			}
		}
		//2014-12-29
		liberarConexionOdbc( $this->conex_u );
		odbc_close_all();
	}

	/**********************************************************************
	 * Maestro de municipios desde unix
	 **********************************************************************/
	function maestroMunicipios(){

		$wbasedato = "root";

		$this->conexionOdbc( 'admisiones' );

		$sql = "SELECT
					*
				FROM
					root_000006
				WHERE
					fecha_data = '".date( "Y-m-d" )."'
					AND id = 1
				";

		$res = mysql_query( $sql, $this->conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );

		if( $num == 0 ){

			if( $this->conex_u ){

				$sql = "SELECT
							muncod, munnom
						FROM
							INMUN
						WHERE
							munact = 'S'
						";

				$res = odbc_do( $this->conex_u, $sql );

				if( $res ){

					$i = 0;
					while( odbc_fetch_row($res) )
					{
						if( $i == 0 ){
							$sqlTruncate = "TRUNCATE TABLE  root_000006";

							$resTruncate = mysql_query( $sqlTruncate, $this->conex ) or die( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() );
						}

						$sqlInsert = "INSERT INTO root_000006(    Medico   ,   Fecha_data         ,      Hora_data       ,           Codigo                 ,             Nombre               ,     Seguridad    ) "
									."			      VALUES ( '$wbasedato', '".date( "Y-m-d" )."', '".date( "H:i:s" )."', '".trim( odbc_result($res,1) )."', '".trim( odbc_result($res,2) )."', 'C-root' ) ";

						$resInsert = mysql_query( $sqlInsert, $this->conex ) or die( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() );

						if( mysql_affected_rows() > 0 ){
							// echo "<br>Inserto: ".trim( odbc_result($res,1) )." - ".trim( odbc_result($res,2) );
						}
						else{
							// echo "<br>No inserto: ".trim( odbc_result($res,1) )." - ".trim( odbc_result($res,2) );
						}

						$i++;
					}
				}
				else{
					echo "Error en el query $sql";
				}
			}
			else{
				echo "NO SE CONECTO A UNIX";
			}
		}
		//2014-12-29
		liberarConexionOdbc( $this->conex_u );
		odbc_close_all();
	}

	function maestroEventosCatastroficos(){

		$wbasedato = $this->consultarAliasPorAplicacion( 'facturacion' );

		$this->conexionOdbc( 'admisiones' );

		if( $this->conex_u ){

			$sql = "SELECT *
					FROM INCEV
					";

			$res = odbc_do( $this->conex_u, $sql );

			if( $res ){

				if( true ){

					$est = 'on';

					$i = 0;
					while( odbc_fetch_row($res) )
					{
						if( $i == 0 ){
							$sqlTruncate = "TRUNCATE TABLE  {$wbasedato}_000165";

							$resTruncate = mysql_query( $sqlTruncate, $this->conex ) or die( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() );
						}

						$sqlInsert = "INSERT INTO {$wbasedato}_000165(    Medico   ,   Fecha_data         ,      Hora_data  ,           Cevcod         ,           Cevdes         ,  Cevest   ,   Seguridad    ) "
									."			              VALUES ( '$wbasedato', '".date( "Y-m-d" )."', '".date( "H:i:s" )."', '".odbc_result($res,1)."', '".odbc_result($res,2)."', '".$est."', 'C-$wbasedato' ) ";

						$resInsert = mysql_query( $sqlInsert, $this->conex ) or die( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() );

						if( mysql_affected_rows() > 0 ){
							// echo "<br>Inserto...";
						}
						else{
							// echo "<br>No inserto....";
						}

						$i++;
					}
				}
			}
			else{
				echo "Error en el query $sql";
			}
		}
		else{
			echo "NO SE CONECTO A UNIX";
		}
		//2014-12-29
		liberarConexionOdbc( $this->conex_u );
		odbc_close_all();
	}

	function maestroTiposVehiculos(){

		$wbasedato = $this->consultarAliasPorAplicacion( 'facturacion' );

		$this->conexionOdbc( 'admisiones' );

		$sql = "SELECT
					*
				FROM
					{$wbasedato}_000162
				WHERE
					fecha_data = '".date( "Y-m-d" )."'
					AND id = 1
				";

		$res = mysql_query( $sql, $this->conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );

		if( $num == 0 ){

			if( $this->conex_u ){

				$sql = "SELECT
							ctedetpar, ctedetdes
						FROM
							sictedet
						where
							ctedetcod = 'CLAVEH'
						";

				$res = odbc_do( $this->conex_u, $sql );

				if( $res ){

					$est = 'on';

					$i = 0;
					while( odbc_fetch_row($res) )
					{
						if( $i == 0 ){
							$sqlTruncate = "TRUNCATE TABLE  {$wbasedato}_000162";

							$resTruncate = mysql_query( $sqlTruncate, $this->conex ) or die( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() );
						}

						$sqlInsert = "INSERT INTO {$wbasedato}_000162(    Medico   ,   Fecha_data         ,      Hora_data  ,           Tipcod         ,           Tipdes         ,  Tipest   ,   Seguridad    ) "
									."			              VALUES ( '$wbasedato', '".date( "Y-m-d" )."', '".date( "H:i:s" )."', '".trim( odbc_result($res,1) )."', '".trim( odbc_result($res,2) )."', '".$est."', 'C-$wbasedato' ) ";

						$resInsert = mysql_query( $sqlInsert, $this->conex ) or die( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() );

						if( mysql_affected_rows() > 0 ){
							// echo "<br>Inserto...";
						}
						else{
							// echo "<br>No inserto....";
						}

						$i++;
					}
				}
				else{
					echo "Error en el query $sql";
				}
			}
			else{
				echo "NO SE CONECTO A UNIX";
			}
		}
		//2014-12-29
		liberarConexionOdbc( $this->conex_u );
		odbc_close_all();
	}

	function kron_procedimientos_y_examenes(){

		// 

		// 

		// $conex_o = odbc_connect('facturacion','','');
		global $conex;
		global $wemp_pmla;

		$conex_o = $this->conexionOdbc( 'facturacion' ) or die( "No se realizó Conexión" );

		echo "<form action='kron_camas.php' method=post>";
		echo "<table border=0 align=center>";
		echo "<tr><td align=center colspan=2><IMG SRC='/matrix/images/medical/movhos/logo_movhos.png'></td></tr>";
		echo "<tr><td align=center bgcolor=#999999 colspan=2><font size=6 face='tahoma'><b>ACTUALIZACION DE PROCEDIMIENTOS Y EXAMENES PARA HCE</font></b></font></td></tr>";


		$query = " SELECT cupcod, cupdes, cupact, cupria "
				."   FROM incup ";
		$err_o = odbc_do($conex_o,$query);
		$campos= odbc_num_fields($err_o);

		$winsert=0;
		$wupdate=0;
		while (odbc_fetch_row($err_o))
		  {
			$row=array();
			for($i=1;$i<=$campos;$i++)
			  {
			   $row[$i-1]=odbc_result($err_o,$i);
			  }

			$wdesc = $row[1];                                //Descripcion

			//===========================================================================================================
			//NoPos
			//===========================================================================================================
			$wnopos="off";
			if (substr_count($row[1], '(NP)') > 0)           //Verifico si el registro es NoPos con NP  = No Pos
			   {
				$wnopos="on";
				$wdesc = str_replace('(NP)', '', $row[1]);   //Si en la descripcion tiene la sigla 'NP', la quito
			   }
			if (substr_count($row[1], '(NPC)') > 0)          //Verifico si el registro es NoPos con NPC = No Pos, No Cups
			   {
				$wnopos="on";
				$wdesc = str_replace('(NPC)', '', $row[1]);  //Si en la descripcion tiene la sigla 'NPC', la quito
			   }
			//===========================================================================================================

			//Estado
			if ($row[2]=="S")
			   $westado = "on";
			  else
				 $westado = "off";

			//Busco si el codigo existe en HCE
			$query = "SELECT Codigo "
					."  FROM hce_000017 "
					." WHERE Codigo='".$row[0]."'";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);

			if ($num == 0)                                     //Solo inserta los codigos que no existan en la tabla hce_000017
			  {
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
				$q = " INSERT hce_000017 (medico,   fecha_data,   hora_data,   Codigo    ,   Descripcion, Servicio, Tipoestudio, Anatomia,    Codcups   , Protocolo,    Estado     ,    Clase     ,    NoPos     , Seguridad ) "
					."            VALUES ('hce' ,'".$fecha."' ,'".$hora."' ,'".$row[0]."','".$wdesc."' , ''      , ''         , ''      , '".$row[0]."', ''       , '".$westado."', '".$row[3]."', '".$wnopos."', 'C-movhos') ";
				$err1 = mysql_query($q,$conex) or die("ERROR INSERTANDO EL CODIGO ".$row[0]." EN LA TABLA hce_000017 :  ".mysql_errno().":".mysql_error());

				$winsert++;
			  }
			 else
			   {
				$q = " UPDATE hce_000017 "                     //Actualizo el maestro de procedimientos y examenes
					."    SET Descripcion = '".$wdesc."',  "
					."        Estado      = '".$westado."', "
					."        Clase       = '".$row[3]."',  "
					."        NoPos       = '".$wnopos."'   "
					."  WHERE Codigo = '".$row[0]."'";
				$err1 = mysql_query($q,$conex) or die("ERROR ACTUALIZANDO EL CODIGO ".$row[0]." DE LA TABLA hce_000017 : ".mysql_errno().":".mysql_error());

				$wupdate++;
			   }
		  }
		echo "<tr><td align=center bgcolor=#dddddd><font face='tahoma' size=4>Se insertaron   : ".$winsert." códigos en HCE_000017</font></td></tr>";
		echo "<tr><td align=center bgcolor=#dddddd><font face='tahoma' size=4>Se actualizaron : ".$wupdate." códigos en HCE_000017</font></td></tr>";

		echo "</table>";




		//*****************************************************SERVICIOS DE INGRESO*****************************************************
		//******************************************************************************************************************************





		echo "<table border=0 align=center>";
		//echo "<tr><td align=center colspan=2><IMG SRC='/matrix/images/medical/movhos/logo_movhos.png'></td></tr>";
		echo "<tr><td align=center bgcolor=#999999 colspan=2><font size=6 face='tahoma'><b>ACTUALIZACION DE SERVICIOS DE INGRESO PARA MOVHOS</font></b></font></td></tr>";

		$insertados = 0;
		$actualizados = 0;
		//Busca los servicios en servinte
		$query="SELECT 	sercod, sernom, seract
				  FROM 	inser";

		$codigos_unix = array();
		$err_o = odbc_do($conex_o,$query) or die( odbc_error()." $query - ".odbc_errormsg() );
		while (odbc_fetch_row($err_o))
		{
			$wcodigo = odbc_result($err_o, 'sercod');
			$wdescripcion = odbc_result($err_o, 'sernom');
			$westado = odbc_result($err_o, 'seract');
			( $westado == 'S' ) ? $westado = 'on' : $westado = 'off';
			array_push( $codigos_unix, $wcodigo );

			//echo "<br>".$wcodigo."-".$wdescripcion;

			$q = "SELECT * "
				."  FROM cliame_000001 "
				." WHERE seicod = '".$wcodigo."'";
			$res = mysql_query($q, $conex);
			@$num = mysql_num_rows($res);
			if( $num > 0 ){
				$q = " UPDATE cliame_000001 SET seides = '".$wdescripcion."', seiest = '".$westado."' "
				  ."    WHERE seicod = '".$wcodigo."'";
				$res = mysql_query($q, $conex);
				$actualizados++;
			}else{
				$q = "INSERT INTO  cliame_000001 "
									."	(Medico, 	Fecha_data, 			Hora_data, 			Seicod, 			Seides, 		Seiest, 		Seguridad) "
						."    VALUES    ('cliame', '".date("Y-m-d")."', '".date("H:i:s")."', '".$wcodigo."', '".$wdescripcion."', '".$westado."', 'C-cliame')";
				$res = mysql_query($q,$conex);
				$insertados++;
			}
		}

		//Busco todos los codigos que tengo en matrix y que el query de unix no trajo y les pongo el estado en off
		/*if( count( $codigos_unix ) > 0 ){
			$cods = implode(",", $codigos_unix);
			$q = "SELECT Seicod as codigo"
				."  FROM cliame_000001 "
				." WHERE Seicod NOT IN (".$cods.") ";
			$res = mysql_query($q, $conex);
			@$num = mysql_num_rows($res);

			if ($num > 0){
				while($row = mysql_fetch_assoc($res)) {
					$q = " UPDATE cliame_000001 SET seiest = 'off' "
					  ."    WHERE seicod = '".$row['codigo']."'";
					$res = mysql_query($q, $conex);
					$actualizados++;
				}
			}
		}*/
		echo "<tr><td align=center bgcolor=#dddddd><font face='tahoma' size=4>Se insertaron   : ".$insertados." códigos en cliame_000001</font></td></tr>";
		echo "<tr><td align=center bgcolor=#dddddd><font face='tahoma' size=4>Se actualizaron : ".$actualizados." códigos en cliame_000001</font></td></tr>";

		echo "</table>";

		//2014-12-29
		liberarConexionOdbc( $conex_o );
		odbc_close_all();
	}

	function actualizarDatosCie10(){
		return;
		// $wbasedato = $this->consultarAliasPorAplicacion( 'facturacion' );

		$this->conexionOdbc( 'admisiones' );

		// $sql = "SELECT
					// *
				// FROM
					// root_000011
				// WHERE
					// edad_i = ''
					// OR edad_s = ''
					// OR sexo = ''
				// ";

		// $res = mysql_query( $sql, $this->conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		// $num = mysql_num_rows( $res );

		// if( $num > 0 )
		// {
			// for( $i = 0;$rows = mysql_fetch_array( $res ); $i++ )
			// {
				// $sql = "SELECT
							// diaedi, diaeds, diasex
						// FROM
							// india
						// WHERE
							// diacod = '".$rows[ 'Codigo' ]."'
							// AND diaact = 'S'
						// ";

				$sql = "SELECT
							diaedi, diaeds, diasex, diacod, diaact, dianom, diades
						FROM
							india
						WHERE
							rowid>0 AND rowid<=20000
						";

				$err_o = odbc_do( $this->conex_u, $sql ) or die( "Error: ????" );

				while( odbc_fetch_row($err_o) )
				{
					if( strtoupper( odbc_result($err_o,5) ) == 'S' )
					{

						$edad_i = odbc_result($err_o,1);
						$edad_s = odbc_result($err_o,2);
						$sexo = odbc_result($err_o,3);
						$cod =  odbc_result($err_o,4);

						//Traduzco la edad inferior en años
						switch($edad_i[0]){

							case 0: //Edad minima sin restricción
							case 1: //horas
								$edad_i = 0;
							break;

							case 2: //Edad en días
								$edad_i = substr( $edad_i, 1, 2 )/365;
							break;

							case 3:
								$edad_i = substr( $edad_i, 1, 2 )/12;
							break;

							case 4:
								$edad_i = substr( $edad_i, 1, 2 );
							break;

							default: break;
						}

						//Traduzco la edad superior en años
						switch($edad_s[0]){

							case 0: //Edad maxima sin restricción
							case 1: //horas
								$edad_s = 0;
							break;

							case 2: //Edad en días
								$edad_s = substr( $edad_s, 1, 2 )/365;
							break;

							case 3:
								$edad_s = substr( $edad_s, 1, 2 )/12;
							break;

							case 4:
								$edad_s = substr( $edad_s, 1, 2 );
							break;

							case 5:
								$edad_s = 120;
							break;

							default: break;
						}

						$sqlUpt = "UPDATE
										root_000011
									SET
										Edad_i = '".$edad_i."',
										Edad_s = '".$edad_s."',
										sexo = '".$sexo."'
									WHERE
										Codigo = '".$cod."'
									";

						$sqlUpt = "SELECT * FROM
										root_000011
									WHERE
										Codigo = '".$cod."'
									";

						$resUpt = mysql_query( $sqlUpt, $this->conex ) or die( mysql_errno()." - Error en el query $sqlUpt - ".mysql_error() );

						IF( mysql_num_rows($resUpt) == 0 ){
							echo "<br>".$cod." - ".odbc_result($err_o,6)." - ".odbc_result($err_o,7);
						}

						if( !$resUpt ){
							//echo "lasfñsdfs";
						}
					}
				}

				if( $i == 50 )
					return;
			// }
		// }

		//2014-12-29
		liberarConexionOdbc( $this->conex_u );
		odbc_close_all();
	}

	function escapar( $cadena ){

		$val = str_replace( "'", "''''", $cadena );
		$val = str_replace( "\\", "\\\\\\\\", $val );
		// $val = str_replace( "\"", "\\\"", $val );

		return $val;
	}

	function pacientesInactivosAMatrix(){
		return;
		$wbasedato = $this->consultarAliasPorAplicacion( 'facturacion' );

		$this->conexionOdbc( 'admisiones' );

		$fecha = date( "Y-m-d" );
		$hora = date( "H:i:s" );

		$arrPaisNac = Array();
		$arrEstCivil = Array();

		if( $this->conex_u ){

			//				1		2		3		4		5		6		7		8		9		10		11		12		13		14		15		16		17	 18		19		20
			$sql = "SELECT
						pachis, pacced, pactid, pacnui, pacap1, pacap2, pacnom, pacsex, pacnac, paclug, pacest, pacdir, pactel, paczon, pacmun, pactra, pacniv, pacpad, pacing, pacnum
					FROM
						inpaci
					WHERE
						pactra IS NULL
						AND pacap2 IS NULL
					UNION
					SELECT
						pachis, pacced, pactid, pacnui, pacap1, pacap2, pacnom, pacsex, pacnac, paclug, pacest, pacdir, pactel, paczon, pacmun, pactra, pacniv, pacpad, pacing, pacnum
					FROM
						inpaci
					WHERE
						pactra IS NULL
						AND pacap2 IS NOT NULL
					UNION
					SELECT
						pachis, pacced, pactid, pacnui, pacap1, pacap2, pacnom, pacsex, pacnac, paclug, pacest, pacdir, pactel, paczon, pacmun, pactra, pacniv, pacpad, pacing, pacnum
					FROM
						inpaci
					WHERE
						pactra IS NOT NULL
						AND pacap2 IS NULL
					UNION
					SELECT
						pachis, pacced, pactid, pacnui, pacap1, pacap2, pacnom, pacsex, pacnac, paclug, pacest, pacdir, pactel, paczon, pacmun, pactra, pacniv, pacpad, pacing, pacnum
					FROM
						inpaci
					WHERE
						pactra IS NOT NULL
						AND pacap2 IS NOT NULL
					";

			$sql = "SELECT
						pachis, pacced, pactid, pacnui, pacap1, pacap2, pacnom, pacsex, pacnac, paclug, pacest, pacdir, pactel, paczon, pacmun, pactra, pacniv, pacpad, pacing, pacnum
					FROM
						inpaci
					WHERE
						rowid>400000 and rowid<=600000
					";

			$err_o = odbc_do( $this->conex_u, $sql );

			if( $err_o ){

				$est = 'on';

				$i = 0;
				while( odbc_fetch_row($err_o) )
				{
					$i++;

					if( trim( odbc_result($err_o,2) ) == '' )
						continue;

					//Busco que la historia no este en la tabla
					$sql = "SELECT
								*
							FROM
								".$wbasedato."_000100
							WHERE
								pachis = '".trim( odbc_result($err_o,1) )."'
								OR pacdoc = '".$this->escapar( trim( odbc_result($err_o,2) ) )."'
							";

					$resPac = mysql_query( $sql, $this->conex );
					$numPac = mysql_num_rows( $resPac );

					if( $numPac == 0 ){

						$paisNac = '';
						if( !isset( $arrPaisNac[ trim( substr( odbc_result($err_o,10), 0, 2 ) ) ] ) ){

							//Consulto el codigo del pais de nacimiento
							$sqlPais = "SELECT codigoPais
									FROM
										root_000002
									WHERE
										Codigo = '".trim( substr( odbc_result($err_o,10), 0, 2 ) )."'
									ORDER BY Descripcion
									";

							$resPais = mysql_query( $sqlPais, $this->conex ) or die( mysql_errno()." - Error en el query $$sqlPais - ".mysql_error() );

							if( $rows = mysql_fetch_array( $resPais ) ){
								$paisNac = $rows[ 'codigoPais' ];

								$arrPaisNac[ trim( substr( odbc_result($err_o,10), 0, 2 ) ) ] = $paisNac;
							}
						}
						else{
							$paisNac = $arrPaisNac[ trim( substr( odbc_result($err_o,10), 0, 2 ) ) ];
						}


						$paisRes = '';
						if( !isset( $arrPaisNac[ trim( substr( odbc_result($err_o,15), 0, 2 ) ) ] ) ){
							//Consulto el codigo del pais de residencia
							$sqlPais = "SELECT codigoPais
									FROM
										root_000002
									WHERE
										Codigo = '".trim( substr( trim( odbc_result($err_o,15) ), 0, 2 ) )."'
									ORDER BY Descripcion
									";

							$resPais = mysql_query( $sqlPais, $this->conex ) or die( mysql_errno()." - Error en el query $$sqlPais - ".mysql_error() );

							if( $rows = mysql_fetch_array( $resPais ) ){
								$paisRes = $rows[ 'codigoPais' ];

								$arrPaisNac[ trim( substr( odbc_result($err_o,15), 0, 2 ) ) ] = $paisRes;
							}
						}
						else{
							$paisRes = $arrPaisNac[ trim( substr( odbc_result($err_o,15), 0, 2 ) ) ];
						}


						$estCivil = '';
						if( !isset( $arrEstCivil[ trim( odbc_result($err_o,11) ) ] ) ){
							//Consulto estado civil
							$sqlEstadoCivil = "SELECT
													Selcod
												FROM
													".$wbasedato."_000105
												WHERE
													Seltip = '25'
													AND selmat = '".trim( odbc_result($err_o,11) )."'
												";

							$resPais = mysql_query( $sqlEstadoCivil, $this->conex ) or die( mysql_errno()." - Error en el query $sqlEstadoCivil - ".mysql_error() );

							if( $rows = mysql_fetch_array( $resPais ) ){
								$estCivil = $rows[ 'Selcod' ];
								$arrEstCivil[ trim( odbc_result($err_o,11) ) ] = $estCivil;
							}
						}
						else{
							$estCivil = $arrEstCivil[ trim( odbc_result($err_o,11) ) ];
						}

						//Avriguo el primer y segundo nombre
						$nombreUnix = trim( odbc_result($err_o,7) );
						$posSegundoNombre = strpos( $nombreUnix, ' ' );
						if( $posSegundoNombre > 0 ){
							$pacNom1 = $this->escapar( substr(  $nombreUnix , 0, $posSegundoNombre ) );
							$pacNom2 = $this->escapar( trim( substr( $nombreUnix, $posSegundoNombre ) ) );
						}
						else{
							$pacNom1 =  $this->escapar( $nombreUnix );
							$pacNom2 = '.';
						}

						//Consulto tipo de residencia
						$tipoResidencia = 'E';	//Extranjera
						if( $paisRes == '169' ){
							$tipoResidencia = 'N';	//Nacional
						}

						//Inserto los datos demograficos del paciente
						$sql = "INSERT INTO ".$wbasedato."_000100(    Medico   , Fecha_data, Hora_data,               Pachis               ,             Pactdo                 ,                      Pacdoc                          , Pactat,            Pacap1                                    ,                      Pacap2                          ,    Pacno1     ,     Pacno2    ,                 Pacfna               ,               Pacsex               ,    Pacest      ,                          Pacdir                       ,                     Pactel                            ,               Paciu                 , Pacbar,                   Pacdep                            ,   Pacpan  ,                Paczon              ,              Pacmuh                ,                         Pacdeh                       ,  Pacpah   ,                       Pacemp                          ,          Pactrh     ,   Seguridad    )
														   VALUES( '$wbasedato',  '$fecha' ,  '$hora' , '".trim( odbc_result($err_o,1) )."', '".trim( odbc_result($err_o,3) )."', '".$this->escapar( trim( odbc_result($err_o,2) ) )."',  ''   , '".$this->escapar( trim( odbc_result($err_o,5) ) )."', '".$this->escapar( trim( odbc_result($err_o,6) ) )."', '".$pacNom1."', '".$pacNom2."',   '".trim( odbc_result($err_o,9) )."', '".trim( odbc_result($err_o,8) )."', '".$estCivil."', '".$this->escapar( trim( odbc_result($err_o,12) ) )."', '".$this->escapar( trim( odbc_result($err_o,13) ) )."', '".trim( odbc_result($err_o,10) )."',   ''  , '".trim( substr( odbc_result($err_o,10), 0, 2 ) )."', '$paisNac','".trim( odbc_result($err_o,14) )."', '".trim( odbc_result($err_o,15) )."', '".substr( trim( odbc_result($err_o,15) ), 0, 2 )."', '$paisRes', '".$this->escapar( trim( odbc_result($err_o,16) ) )."','".$tipoResidencia."', 'C-$wbasedato' );";

						$resDem = mysql_query( $sql, $this->conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

						if( mysql_affected_rows() > 0 ){

							//Inserto datos de ingreso
							$sql = "INSERT INTO ".$wbasedato."_000101(    Medico   , Fecha_data, Hora_data,               Inghis               ,                 Ingnin              ,               Ingfei                ,   Seguridad    )
															   VALUES( '$wbasedato',  '$fecha' ,  '$hora' , '".trim( odbc_result($err_o,1) )."', '".trim( odbc_result($err_o,20) )."', '".trim( odbc_result($err_o,19) )."', 'C-$wbasedato' );";

							$resIng = mysql_query( $sql, $this->conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

							if( mysql_affected_rows() > 0 ){
							}
						}
					}

					if( $i ==200000 ){ return;};
				}
			}
			else{
				echo "Error en el query $sql";
			}
		}
		else{
			echo "NO SE CONECTO A UNIX";
		}

		//2014-12-29
		liberarConexionOdbc( $this->conex_u );
		odbc_close_all();
	}


	function kronBarrios(){
		return;
		// $wbasedato = $this->consultarAliasPorAplicacion( 'facturacion' );

		$this->conexionOdbc( 'admisiones' );

		$sql = "SELECT
					Barcod, Barmun, Bardes
				FROM
					root_000034
				";

		$res = mysql_query( $sql, $this->conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );

		if( $num > 0 )
		{
			for( $i = 0;$rows = mysql_fetch_array( $res ); $i++ )
			{
				//Inserto datos de ingreso
				echo "<br>".$sql = "INSERT INTO inbar1(                   barcod                 ,         barnom         ,                      barloc                              , baract )
						            VALUES( '".$rows[ 'Barmun' ].$rows[ 'Barcod' ]."', '".$rows[ 'Bardes' ]."', '".$rows[ 'Barmun' ].substr( $rows[ 'Barcod' ], 0, 2 )."',  'S'   );";

				// $err_o = odbc_do( $this->conex_u, $sql );
			}
		}
		//2014-12-29
		liberarConexionOdbc( $this->conex_u );
		odbc_close_all();
	}


function construirQueryUnix( $tablas, $campos_nulos, $campos_todos='', $condicionesWhere='',$defecto_campos_nulos=''){

	$condicionesWhere = trim($condicionesWhere);

	if( $campos_nulos == NULL || $campos_nulos == "" ){
		$campos_nulos = array("");
	}

	if( $tablas == "" ){ //Debe existir al menos una tabla
		return false;
	}

	if(gettype($tablas) == "array"){
		$tablas = implode(",",$tablas);
	}

	$pos = strpos($tablas, ",");
	if( $pos !== false && $condicionesWhere == ""){ //Si hay mas de una tabla, debe mandar condicioneswhere
		return false;
	}

	//Si recibe un string, convertirlo a un array
	if( gettype($campos_nulos) == "string" )
		$campos_nulos = explode(",",$campos_nulos);

	$campos_todos_arr = array();

	//Por cual string se reemplazan los campos nulos en el query
	if( $defecto_campos_nulos == "" ){
		$defecto_campos_nulos = array();
		foreach( $campos_nulos as $posxy=>$valorxy ){
			array_push($defecto_campos_nulos, "''");
		}
	}else{
		if(gettype($defecto_campos_nulos) == "string"){
			$defecto_campos_nulos = explode(",",$defecto_campos_nulos);
		}
		if(  count( $defecto_campos_nulos ) == 1 ){ //Significa que todos los campos nulos van a ser reemplazados con el mismo valor
			$defecto_campos_nulos_aux = array();
			foreach( $campos_nulos as $posxyc=>$valorxyc ){
				array_push($defecto_campos_nulos_aux, $defecto_campos_nulos[0]);
			}
			$defecto_campos_nulos = $defecto_campos_nulos_aux;
		}else if(  count( $defecto_campos_nulos ) != count( $campos_nulos ) ){
			return false;
		}
	}

	if( gettype($campos_todos) == "string" ){
		$campos_todos_arr = explode(",",trim($campos_todos));
	}else if(gettype($campos_todos) == "array"){
		$campos_todos_arr = $campos_todos;
		$campos_todos = implode(",",$campos_todos);
	}
	foreach( $campos_todos_arr as $pos22=>$valor ){ //quitar espacios a cada valor
		$campos_todos_arr[$pos22] = trim($valor);
	}
	foreach( $campos_nulos as $pos221=>$valor1 ){ //quitar espacios a cada valor
		$campos_nulos[$pos221] = trim($valor1);

		//Si el campo nulo no existe en el arreglo de todos los campos, agregarlo al final
		$clavex = array_search(trim($valor1), $campos_todos_arr);
		if( $clavex === false ){
			array_push($campos_todos_arr,trim($valor1));
		}
	}
	//Quitar la palabra and, si las condiciones empiezan asi.
	if( substr($condicionesWhere, 0, 3)  == "AND" || substr($condicionesWhere, 0, 3) == "and" ){
		$condicionesWhere = substr($condicionesWhere, 3);
	}
	$condicionesWhere = str_replace("WHERE", "", $condicionesWhere); //Que no tenga la palabra WHERE
	$condicionesWhere = str_replace("where", "", $condicionesWhere); //Que no tenga la palabra WHERE

	$query = "";

	$bits = count( $campos_nulos );
	if( $bits >= 10 ){ //No pueden haber más de 10 campos nulos
		return false;
	}

	if( $bits == 1 && $campos_nulos[0] == "" ){ //retornar el query normal
		$query = "SELECT ".$campos_todos ." FROM ".$tablas;
		if( $condicionesWhere != "" )
			$query.= " WHERE ".$condicionesWhere;
		return $query;
	}

	$max = (1 << $bits);
	$fila_bits = array();
	for ($i = 0; $i < $max; $i++){
		/*-->decbin Entrega el valor binario del decimal $i,
		  -->str_pad Rellena el string hasta una longitud $bits con el caracter 0 por la izquierda:
			 EJEMPLO $input = "Alien" str_pad($input, 10, "-=", STR_PAD_LEFT);  // produce "-=-=-Alien", rellena por la izquierda hasta juntar 10 caracteres
		  -->str_split Convierte un string (el entregado por str_pad) en un array, asi tengo el arreglo con el codigo binario generado
		*/
		$campos_todos_arr_copia = array();
		$campos_todos_arr_copia = $campos_todos_arr;

		$fila_bits = str_split( str_pad(decbin($i), $bits, '0', STR_PAD_LEFT) );
		$select = "SELECT ";
		$where = " WHERE ";
		if( $condicionesWhere != "" )
			$where.= $condicionesWhere." AND ";

		for($pos = 0; $pos < count($fila_bits); $pos++ ){
			if($pos!=0) $where.= " AND ";
			if( $fila_bits[$pos] == 0 ){
				$clave = array_search($campos_nulos[$pos], $campos_todos_arr_copia);
				//if( $clave !== false ) $campos_todos_arr_copia[ $clave ] = "'.' as ".$campos_nulos[$pos];
				if( $clave !== false ) $campos_todos_arr_copia[ $clave ] = $defecto_campos_nulos[$pos]." as ".$campos_nulos[$pos];
				$where.= $campos_nulos[$pos]." IS NULL ";
			}else{
				$where.= $campos_nulos[$pos]." IS NOT NULL ";
			}
		}

		$select.= implode(",",$campos_todos_arr_copia);
		$query.= $select." FROM ".$tablas.$where;
		if( ($i+1) < $max ) $query.= " UNION ";
	}
	return $query;
}

/*
* Esta funcion llena la tabla cliame_000100 y cliame_000101 con los pacientes de inpaci,
  empezando en el ultimo rowid que se consulto y se trajo a dichas tablas, asi para no
  consultar toda la tabla, cuando se traen los datos se guarda la informacion en un array,
  la comparacion se hace con la tabla root_000037 que tambien se guarda en un array, los que
  esten en inpaci que no esten en root_000037 se pasan a la tabla 100 en estado off, y tambien
  a la tabla 101.
  La segunda parte llena la tabla cliame_000100 y cliame_000101 con los pacientes de inpac, si
  no existe la historia en la 100 se inserta con estado on y tambien en la 101, pero si ya existe
  el registro se actualiza en la 100 y se inserta el ingreso en la 101.
  Utiliza estas funciones:
	insertarTemporal()
	actualizaMatrix()
	consultarAplicacion()
	actualizarAplicacion()
*/

function llenarTablaPacientesIngreso( $seccion="" )
{
	global $conex;
	global $wemp_pmla;
	global $bd;
	global $parte1, $parte2, $parte3,$parte4;
	global $cantidadp1, $cantidadp2, $cantidadp3;

	if( $seccion != "" ){
		if( $seccion == "A" ){
			$parte1=true;
		}else if( $seccion == "B" ){
			$parte2=true;
		}else if( $seccion == "C" ){
			$parte3=true;
		}
	}
	//$VER_DETALLE = true;

	if( isset($parte4) ){
		$this->corregirResponsables();
		return;
	}

	$bd="cliame";
	$arrPaisNac = Array();
	$arrEstCivil = Array();

	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
    $wbasedato = $institucion->baseDeDatos;
    $winstitucion = $institucion->nombre;

    $wbasedato_mov = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
    conexionOdbc($conex, $wbasedato_mov, &$conex_o, 'facturacion');

	$rowid=$this->consultarAplicacion( $conex, $wemp_pmla, "Rowid" );
	$rr=1;

	/*******************************************************************************************************************
	 * codigo para comparar la tabla inpaci con la root_37
	 *******************************************************************************************************************/
	$rowidMenorQue = 0;
	$limite_queryp1 = 10000;
	if( isset( $cantidadp1) && $cantidadp1 > 0 ){
		$limite_queryp1 = $cantidadp1;
	}
	$rowidMenorQue = $rowid + $limite_queryp1;


	//no justifican,  pacniv,paczon,pacsex,pacnac,pacmun
	//$campos_nulos = "pactra, pacap2, pactid, pacpad,pacnui,paclug,pacest,pacdir,pactel,pacdir";
	echo "<br>time: ".date("H:i:s");
	/*$campos_nulos = "pactra, pacap2, pacpad,pacnui,paclug,pactel";
	$campos_nulos = "pactra, pacap2, pacpad,paclug,pactel,pacdir";
	//			1		2		3		4		5		6		7		8		9		10		11		12		13		14		15		16		17		18		19		20		21
	$campos = "pachis, pacced, pactid, pacnui, pacap1, pacap2, pacnom, pacsex, pacnac, paclug, pacest, pacdir, pactel, paczon, pacmun, pactra, pacniv, pacpad, pacing, pacnum, rowid";
	$tablas = "inpaci";
	$where = "rowid >= ".$rowid." AND rowid < ".$rowidMenorQue." AND pacnui IS NOT NULL AND pacniv IS NOT NULL AND paczon IS NOT NULL AND pacsex IS NOT NULL AND pacnac IS NOT NULL AND pacmun IS NOT NULL AND pacest IS NOT NULL AND pactid IS NOT NULL";
	$query = $this->construirQueryUnix( $tablas,$campos_nulos,$campos,$where );
	$query.=" ORDER BY 21 ";*/
	//20140926 traer tarifa de inmegr
	$campos_nulos = "pactra, pacap2, pacpad,pacnui,paclug,pactel";
	$campos_nulos = "pactra, pacap2, pacpad,paclug,pactel,pacdir";
	//			1		2		3		4		5		6		7		8		9		10		11		12		13		14		15		16		17		18		19		20		21		22
	$campos = "pachis, pacced, pactid, pacnui, pacap1, pacap2, pacnom, pacsex, pacnac, paclug, pacest, pacdir, pactel, paczon, pacmun, pactra, pacniv, pacpad, pacing, pacnum, inpaci.rowid, egrtar";
	$tablas = "inpaci,inmegr";
	$where = "inpaci.rowid >= ".$rowid." AND inpaci.rowid < ".$rowidMenorQue." AND egrhis=pachis AND egrnum=pacnum AND egrtar IS NOT NULL AND pacnui IS NOT NULL AND pacniv IS NOT NULL AND paczon IS NOT NULL AND pacsex IS NOT NULL AND pacnac IS NOT NULL AND pacmun IS NOT NULL AND pacest IS NOT NULL AND pactid IS NOT NULL AND pacced IS NOT NULL";
	$query = $this->construirQueryUnix( $tablas,$campos_nulos,$campos,$where );
	$query.=" ORDER BY 21 ";

	echo "<br>time antes de ejecutar: ".date("H:i:s");
			/**********************************************************************************************************
				Se inserta en una tabla temporal en matrix, los pacientes leídos desde unix.
			***********************************************************************************************************/
			$user_session = explode('-',$_SESSION['user']);
			$user_session = $user_session[1];

			$nmtemp = $bd."_".date("his");
			$filas_unix = "";
			$coma = '';
			$ctl_lote = 100;
			$lote = $ctl_lote;
			$total_lotes = 0;

			$total_registros = -1;
			echo "<br>PACIENTES INPACI:<BR>";
			$arr_his = array();
			$arr_ing = array();
		if( isset( $parte1 ) ){
			$err_o= odbc_do($conex_o,$query);
			$num_o = odbc_num_fields($err_o);
			echo "Comienza {$rowid}<br>";


			echo "<br>time despues de ejecutar: ".date("H:i:s");
			while(odbc_fetch_row($err_o) && $total_registros < $limite_queryp1 )
			{
				echo "<br>A. Historia".odbc_result($err_o,1)." --- rowid ".odbc_result($err_o,21);
				//echo "<br>a.time: ".date("H:i:s");
				/* Valicaciones necesarias antes de guardar datos en la tabla */
				if( trim( odbc_result($err_o,2) ) == '' )
									continue;
				$total_registros++;
				//Busco que la historia no este en la tabla
				$sql = "SELECT *
						FROM ".$bd."_000100
						WHERE pachis = '".trim( odbc_result($err_o,1) )."'
						   OR pacdoc = '".$this->escapar( trim( odbc_result($err_o,2) ) )."'
						";
				$resPac = mysql_query( $sql, $conex );
				$numPac = mysql_num_rows( $resPac );

				if( $numPac == 0 ){
					//$this->guardarResponsables( $conex_o, trim( odbc_result($err_o,1) ),trim( odbc_result($err_o,20) ) );
					array_push($arr_his, trim( odbc_result($err_o,1) ) );
					array_push($arr_ing, trim( odbc_result($err_o,20) ) );

					/*PAIS DE NACIMIENTO*/
					$paisNac = '';
					if( !isset( $arrPaisNac[ trim( substr( odbc_result($err_o,10), 0, 2 ) ) ] ) ){

						//Consulto el codigo del pais de nacimiento
						$sqlPais = "SELECT codigoPais
									  FROM root_000002
								     WHERE Codigo = '".trim( substr( odbc_result($err_o,10), 0, 2 ) )."'
								  ORDER BY Descripcion
								";
						$resPais = mysql_query( $sqlPais, $conex ) or die( mysql_errno()." - Error en el query $$sqlPais - ".mysql_error() );

						if( $rows = mysql_fetch_array( $resPais ) ){
							$paisNac = $rows[ 'codigoPais' ];
							$arrPaisNac[ trim( substr( odbc_result($err_o,10), 0, 2 ) ) ] = $paisNac;
						}
					}
					else{
						$paisNac = $arrPaisNac[ trim( substr( odbc_result($err_o,10), 0, 2 ) ) ];
					}
					/*FIN PAIS DE NACIMIENTO*/

					/*PAIS DE RESIDENCIA*/
					$paisRes = '';
					if( !isset( $arrPaisNac[ trim( substr( odbc_result($err_o,15), 0, 2 ) ) ] ) ){
						//Consulto el codigo del pais de residencia
						$sqlPais = "SELECT codigoPais
									  FROM root_000002
									 WHERE Codigo = '".trim( substr( trim( odbc_result($err_o,15) ), 0, 2 ) )."'
								  ORDER BY Descripcion
								";
						$resPais = mysql_query( $sqlPais, $conex ) or die( mysql_errno()." - Error en el query $$sqlPais - ".mysql_error() );

						if( $rows = mysql_fetch_array( $resPais ) ){
							$paisRes = $rows[ 'codigoPais' ];
							$arrPaisNac[ trim( substr( odbc_result($err_o,15), 0, 2 ) ) ] = $paisRes;
						}
					}
					else{
						$paisRes = $arrPaisNac[ trim( substr( odbc_result($err_o,15), 0, 2 ) ) ];
					}
					/*FIN PAIS DE RESIDENCIA*/

					/*ESTADO CIVIL*/
					$estCivil = '';
					if( !isset( $arrEstCivil[ trim( odbc_result($err_o,11) ) ] ) ){
						//Consulto estado civil
						$sqlEstadoCivil = "SELECT Selcod
											 FROM ".$bd."_000105
											WHERE Seltip = '25'
											  AND selmat = '".trim( odbc_result($err_o,11) )."'
											";

						$resPais = mysql_query( $sqlEstadoCivil, $conex ) or die( mysql_errno()." - Error en el query $sqlEstadoCivil - ".mysql_error() );

						if( $rows = mysql_fetch_array( $resPais ) ){
							$estCivil = $rows[ 'Selcod' ];
							$arrEstCivil[ trim( odbc_result($err_o,11) ) ] = $estCivil;
						}
					}
					else{
						$estCivil = $arrEstCivil[ trim( odbc_result($err_o,11) ) ];
					}
					/*FIN ESTADO CIVIL*/

					//Avriguo el primer y segundo nombre
					$nombreUnix = trim( odbc_result($err_o,7) );
					$posSegundoNombre = strpos( $nombreUnix, ' ' );
					if( $posSegundoNombre > 0 ){
						$pacNom1 = $this->escapar( substr(  $nombreUnix , 0, $posSegundoNombre ) );
						$pacNom2 = $this->escapar( trim( substr( $nombreUnix, $posSegundoNombre ) ) );
					}
					else{
						$pacNom1 =  $this->escapar( $nombreUnix );
						$pacNom2 = '';
					}

					//Consulto tipo de residencia
					$tipoResidencia = 'E';	//Extranjera
					if( $paisRes == '169' ){
						$tipoResidencia = 'N';	//Nacional
					}

					//ACA CONTINUARIAN LOS INSERT
				}
				else
				{
					$queryIngx = "SELECT id FROM cliame_000101
									WHERE Inghis='".trim( odbc_result($err_o,1) )."'
									AND Ingnin = '".trim( odbc_result($err_o,20) )."'";

					$resingx = mysql_query( $queryIngx, $conex );
					$numingx = mysql_num_rows( $resingx );

					if( $numingx == 0 ){
						$filas_unix_aux =" (
									'".$bd."',
									'".date('Y-m-d')."',
									'".date('H:i:s')."',
									'".trim( odbc_result($err_o,1) )."',
									'".trim( odbc_result($err_o,20) )."',
									'".trim( odbc_result($err_o,19) )."',
									'',
									'',
									'',
									'',
									'',
									'".trim( odbc_result($err_o,22) )."',
									'',
								'C-".$bd."' )";
						$q = "	INSERT INTO ".$bd."_000101(
								Medico,
								Fecha_data,
								Hora_data,
								Inghis,
								Ingnin,
								Ingfei,
								Ingpol,
								Ingcai,
								Ingdig,
								Ingsei,
								Inghin,
								Ingtar,
								Ingcem,
								Seguridad
							) VALUES ".$filas_unix_aux.';';
							if($err = mysql_query($q,$conex))
							{
								echo "<br>Insert&oacute; correctamente en '000101'".trim( odbc_result($err_o,1) )." ) <br>";
								//$act1=$this->actualizarAplicacion( $conex, '01', 'Rowid', trim( odbc_result($err_o,21) ) );
							}
					}else{
						$qq="UPDATE cliame_000101 set Ingtar='".trim( odbc_result($err_o,22) )."' WHERE inghis='".trim( odbc_result($err_o,1) )."' AND ingnin='".trim( odbc_result($err_o,20) )."' limit 1";
						$res = mysql_query( $qq, $conex );
					}
					$this->guardarResponsables($conex_o, trim( odbc_result($err_o,1) ),trim( odbc_result($err_o,20) ));
					$act1=$this->actualizarAplicacion( $conex, $wemp_pmla, 'Rowid', trim( odbc_result($err_o,21) ) );
					continue;
				}
				/* fin valicaciones antes de guardar datos en la tabla*/

				if($lote == 0)
				{
					// Inserta lote en temporal
					if($this->insertarTemporal($conex,$nmtemp,$filas_unix,$total_lotes,'temporal'))
					{ }
					$filas_unix = "";
					$lote = $ctl_lote;
					$coma="";
				}
			//	1		2		3		4		5		6		7		8		9     10mun		11		12		13		14		15		16		17	   18		19		20
			//pachis, pacced, pactid, pacnui, pacap1, pacap2, pacnom, pacsex, pacnac, paclug, pacest, pacdir, pactel, paczon, pacmun, pactra, pacniv, pacpad, pacing, pacnum
				$filas_unix .= $coma." (
									'".$bd."',
									'".date('Y-m-d')."',
									'".date('H:i:s')."',
									'".trim( odbc_result($err_o,1) )."',
									'".$this->escapar(trim( odbc_result($err_o,2) ))."',
									'".trim( odbc_result($err_o,3) )."',
									'".trim( odbc_result($err_o,4) )."',
									'".$this->escapar(trim( odbc_result($err_o,5) ))."',
									'".$this->escapar(trim( odbc_result($err_o,6) ))."',
									'".$pacNom1."',
									'".$pacNom2."',
									'".trim( odbc_result($err_o,8) )."',
									'".trim( odbc_result($err_o,9) )."',
									'".trim( odbc_result($err_o,10) )."',
									'".$estCivil."',
									'".$this->escapar(trim( odbc_result($err_o,12) ))."',
									'".trim( odbc_result($err_o,13) )."',
									'".trim( odbc_result($err_o,14) )."',
									'".trim( odbc_result($err_o,15) )."',
									'".trim( odbc_result($err_o,16) )."',
									'".trim( odbc_result($err_o,17) )."',
									'".trim( odbc_result($err_o,18) )."',
									'".trim( odbc_result($err_o,19) )."',
									'".trim( odbc_result($err_o,20) )."',
									'".trim( odbc_result($err_o,22) )."',
								'C-".$bd."' )";
				$coma = ',';
				$rr++;
				$total_lotes++;
				$lote--;

				 /* se actualiza el valor para enviar a la tabla root_000051*/
				if ($num_o > 0 )
				{
					$ultimoRowid=odbc_result($err_o,21);
					$ultimaFecha=odbc_result($err_o,19);
				}

			} //while de la consulta a impaci

			// Puede quedar un resultante de lote menor al valor configurado en el controlador de lote, pero que debe ser insertado, por eso debe inserta el lote faltante.
			if($lote >= 0 && $filas_unix != "")
			{
				// Inserta lote en temporal
				if($this->insertarTemporal($conex,$nmtemp,$filas_unix,$total_lotes,'temporal'))
				{ }
				$filas_unix = "";
				echo (!$VER_DETALLE) ? "" : "<br><font color='black' face='Arial'>Le&iacute;dos de unix: ".$total_lotes."</font><br>";
			}

			/* Si se insertaron datos desde Unix, continúa para realizar la comparación contra la tabla root_37 en Matrix */
			if($total_lotes > 0)
			{
				// consultar los pacientes en matrix
				$consulta_ok_mx = true;
				$num_row_mx = 0;
				$qmx = "	SELECT Oriced,	Oritid,	Orihis,	Oriing,	Oriori
							  FROM root_000037 AS t1
							  where Oriori = '".$wemp_pmla."' limit 1
							";
							//and Orihis*1 > ".$Historia."
				if($resmx = mysql_query($qmx,$conex))
				{
					$consulta_ok_mx = true;
					$num_row_mx = mysql_num_rows($resmx);
				}else{
					$consulta_ok_mx = false;
				}
				// consulta los datos que fueron guardados desde unix
				$q_un = "
							SELECT  pachis,pacced,pactid,pacnui,pacap1,pacap2,pacnom1,pacnom2,
									pacsex,pacnac,paclug,pacest,pacdir,pactel,paczon,pacmun,pactra,
									pacniv,pacpad,pacing,pacnum,pactar
							FROM    ".$nmtemp."_inac_unix
							GROUP BY pachis";
				$arr_arts_unix = array(); // Array para guardar articulo de unix
				if($res = mysql_query($q_un,$conex))
				{
					while($row = mysql_fetch_array($res))
					{
						$arr_arts_unix[$row['pachis']] = array(
															'pachis' => $row['pachis'],
															'pacced' => $row['pacced'],
															'pactid' => $row['pactid'],
															'pacnui' => $row['pacnui'],
															'pacap1' => $row['pacap1'],
															'pacap2' => $row['pacap2'],
															'pacnom1' => $row['pacnom1'],
															'pacnom2' => $row['pacnom2'],
															'pacsex' => $row['pacsex'],
															'pacnac' => $row['pacnac'],
															'paclug' => $row['paclug'],
															'pacest' => $row['pacest'],
															'pacdir' => $row['pacdir'],
															'pactel' => $row['pactel'],
															'paczon' => $row['paczon'],
															'pacmun' => $row['pacmun'],
															'pactra' => $row['pactra'],
															'pacniv' => $row['pacniv'],
															'pacpad' => $row['pacpad'],
															'pacing' => $row['pacing'],
															'pacnum' => $row['pacnum'],
															'pactar' => $row['pactar']
															);
					}

					//consultar los datos en matrix
					$arr_arts_matrix = array(); // Array para guardar articulo de matrix
					if($consulta_ok_mx)
					{
						while($row = mysql_fetch_array($resmx))
						{
							$arr_arts_matrix[$row['Orihis']] = array(
																'Orihis' => trim($row['Orihis']),
																'Oriced' => $this->escapar(trim($row['Oriced'])),
																'Oritid' => trim($row['Oritid']),
																'Oriing' => trim($row['Oriing']),
																'Oriori' => trim($row['Oriori']),
																);
						}

						// Si hay articulos en unix, continúa con el proceso.
						if(count($arr_arts_unix)>0)
						{
							$filas_unix = "";
							$filas_unix1 = "";
							$coma = '';
							$lote = $ctl_lote;
							$total_lotes = 0;
							$total_updt = 0;
							foreach($arr_arts_unix as $key => $arr_art)
							{
								if(!array_key_exists($key, $arr_arts_matrix))
								{
									if($lote == 0)
									{
										echo "A. TRATA DE INSERTAR 100_1: ".$filas_unix."<br>";
										echo "A. TRATA DE INSERTAR 101_1: ".$filas_unix1."<br>";
										// Inserta lote en la 100
										if($this->insertarTemporal($conex,$bd,$filas_unix,$total_lotes,'000100'))
										{
											// registrarLog($conex, $bd, $user_session, "insert", "inserta_articulo", "carga_articulos", "", "", "Inserta articulos que estan en unix y no en matrix. Secuencia lote: $total_lotes");
																					 // Inserta lote en la 101
											if($this->insertarTemporal($conex,$bd,$filas_unix1,$total_lotes,'000101'))
											{
												// registrarLog($conex, $bd, $user_session, "insert", "inserta_articulo", "carga_articulos", "", "", "Inserta articulos que estan en unix y no en matrix. Secuencia lote: $total_lotes");
											}
										}


										$filas_unix = "";
										$filas_unix1 = "";
										$lote = $ctl_lote;
										$coma="";
									}

									$filas_unix .= $coma." (
													'".$bd."',
													'".date('Y-m-d')."',
													'".date('H:i:s')."',
													'".$arr_arts_unix[$key]['pachis']."',
													'".$arr_arts_unix[$key]['pactid']."',
													'".$arr_arts_unix[$key]['pacced']."',
													'',
													'".$this->escapar($arr_arts_unix[$key]['pacap1'])."',
													'".$this->escapar($arr_arts_unix[$key]['pacap2'])."',
													'".$this->escapar($arr_arts_unix[$key]['pacnom1'])."',
													'".$this->escapar($arr_arts_unix[$key]['pacnom2'])."',
													'".$arr_arts_unix[$key]['pacnac']."',
													'".$arr_arts_unix[$key]['pacsex']."',
													'".$arr_arts_unix[$key]['pacest']."',
													'".$arr_arts_unix[$key]['pacdir']."',
													'".$arr_arts_unix[$key]['pactel']."',
													'".$arr_arts_unix[$key]['paclug']."',
													'',
													'".trim( substr( $arr_arts_unix[$key]['paclug'], 0, 2 ) )."',
													'".$paisNac."',
													'".$arr_arts_unix[$key]['paczon']."',
													'".$arr_arts_unix[$key]['pacmun']."',
													'".trim( substr( $arr_arts_unix[$key]['pacmun'], 0, 2 ) )."',
													'".$paisRes."',
													'".$arr_arts_unix[$key]['pactra']."',
													'".$tipoResidencia."',
													'off',
													'',
													'',
													'',
													'',
												'C-".$bd."' )";

									$queryIngx = "SELECT id FROM cliame_000101
													WHERE Inghis='".$arr_arts_unix[$key]['pachis']."'
													AND Ingnin = '".$arr_arts_unix[$key]['pacnum']."'";

									$resingx = mysql_query( $queryIngx, $conex );
									$numingx = mysql_num_rows( $resingx );

									if( $numingx == 0 ){
										if( $filas_unix1 == "" )
											$comau = "";
										else
											$comau = ",";

										$filas_unix1 .= $comau." (
													'".$bd."',
													'".date('Y-m-d')."',
													'".date('H:i:s')."',
													'".$arr_arts_unix[$key]['pachis']."',
													'".$arr_arts_unix[$key]['pacnum']."',
													'".$arr_arts_unix[$key]['pacing']."',
													'',
													'',
													'',
													'',
													'',
													'".$arr_arts_unix[$key]['pactar']."',
													'',
												'C-".$bd."' )";

									}else{
										$qq="UPDATE cliame_000101 set Ingtar='".$arr_arts_unix[$key]['pactar']."' WHERE inghis='".$arr_arts_unix[$key]['pachis']."' AND ingnin='".$arr_arts_unix[$key]['pacnum']."' limit 1";
										$res = mysql_query( $qq, $conex );
									}
									$coma = ',';
									$total_lotes++;
									$lote--;
									if( isset( $VER_DETALLE )){
										echo (!$VER_DETALLE) ? "" : "<br>Nuevo en mx: ".$key;
										echo "<br>Nuevo en mx: ".$key;
									}
								}
								else
								{
									echo "<br>Ya existe la historia en la 100: ".$key."";
								}

								if(array_key_exists($key, $arr_arts_matrix))
								{
									unset($arr_arts_matrix[$key]); // Los articulos que queden en este array luego de terminar el ciclo, deben cambiar a estado inactivo en matrix.
								}
							} //foreach

							if($lote >= 0 && $filas_unix != "")
							{
								echo "A. TRATA DE INSERTAR 100_2: ".$filas_unix."<br>";
								echo "A. TRATA DE INSERTAR 101_2: ".$filas_unix1."<br>";
								if($this->insertarTemporal($conex,$bd,$filas_unix,$total_lotes,'000100'))
								{
									// registrarLog($conex, $bd, $user_session, "insert", "inserta_articulo", "carga_articulos", "", "", "Inserta articulos que estas en unix y no en matrix. Secuencia lote: $total_lotes");
									if($this->insertarTemporal($conex,$bd,$filas_unix1,$total_lotes,'000101'))
									{
										// registrarLog($conex, $bd, $user_session, "insert", "inserta_articulo", "carga_articulos", "", "", "Inserta articulos que estas en unix y no en matrix. Secuencia lote: $total_lotes");
									}
								}

								$filas_unix1 = "";
							}

							if($total_lotes>0)
							{
								echo (!$VER_DETALLE) ? "" : "<br>Historias nuevas: ".$total_lotes;
							}
						}
					}
					else
					{
						echo (!$VER_DETALLE) ? "" : "<br><font color='red' face='Arial'>No pudo consulta root_000037<br>".$qmx."</font>";
					}
				} //if de res de la consulta a la temporal
				else
				{
					echo (!$VER_DETALLE) ? "" : "<br><font color='black' face='Arial'>No pudo consultar temporal de pacientes le&iacute;dos desde unix.<br>".$q_un."-".mysql_errno().":".mysql_error()."</font>";
				}
			}  //total_lotes > 0

				if ($num_o > 0)
				{
					$rowidFinal=$ultimoRowid;
					$fechaFinal=$ultimafecha;
				}
				else
				{
					$rowidFinal=$rowid;
					$fechaFinal=$fecha;
				}

			$act1=$this->actualizarAplicacion( $conex, $wemp_pmla, 'Rowid', $ultimoRowid );
			// $act2=actualizarAplicacion( $conex, '01', 'ultimaFecha', $fechaFinal );
			if ($act1 == true ) //&& $act2 == true
			{
				echo "<br>Parametro en la tabla root_000051 actualizado con exito: ".$rowidFinal."";
			}
			else
			{
				echo "<br>No se actualizo la tabla root_000051";
			}

			//guardar los responsables del paciente
			for( $indic=1; $indic<=count($arr_his); $indic++ ){
				$this->guardarResponsables( $conex_o, $arr_his[ $indic ], $arr_ing[ $indic ] );
			}
		}
			/*********************AQUI SEGUNDA PARTE***********************/
			/*******************************************************************************************************************
			 * codigo para comparar la tabla inpac con la root_37 e inpac con la 100 y la 101
			 *******************************************************************************************************************/
						/*   		1		2		3		4		5		6		7		8		9		10		11		12		13		14		15		16		17	   18
									19      20       21      22      23      24      25      26      27      28      29    */
		 	//NULOS: pacdin,pacpol
			$query= "   		SELECT
									pachis, pacced, pactid, pacnui, pacap1, '.' as pacap2, pacnom, pacsex, paclug, pacnac, pacest, pacdir, pactel, paczon, pacmun, '.' as pactra, pacemp, paccer,
									pacres, pacdre, pactre, pactar, pacnum, pacfec, pachor, pacser, pacdin, paccin, pacpol
								FROM
									inpac
								WHERE
									pactra IS NULL
									AND pacap2 IS NULL
								UNION
								SELECT
									pachis, pacced, pactid, pacnui, pacap1, pacap2, pacnom, pacsex, paclug, pacnac, pacest, pacdir, pactel, paczon, pacmun, '.' as pactra, pacemp, paccer,
									pacres, pacdre, pactre, pactar, pacnum, pacfec, pachor, pacser, pacdin, paccin, pacpol
								FROM
									inpac
								WHERE
									pactra IS NULL
									AND pacap2 IS NOT NULL

								UNION
								SELECT
									pachis, pacced, pactid, pacnui, pacap1, '.' as pacap2, pacnom, pacsex, paclug, pacnac, pacest, pacdir, pactel, paczon, pacmun, pactra, pacemp, paccer,
									pacres, pacdre, pactre, pactar, pacnum, pacfec, pachor, pacser, pacdin, paccin, pacpol
								FROM
									inpac
								WHERE
									pactra IS NOT NULL
									AND pacap2 IS NULL

								UNION
								SELECT
									pachis, pacced, pactid, pacnui, pacap1, pacap2, pacnom, pacsex, paclug, pacnac, pacest, pacdir, pactel, paczon, pacmun, pactra, pacemp, paccer,
									pacres, pacdre, pactre, pactar, pacnum, pacfec, pachor, pacser, pacdin, paccin, pacpol
								FROM
									inpac
								WHERE
									pactra IS NOT NULL
									AND pacap2 IS NOT NULL
							";
	//------------------********************-----------------------------------*************
			$query= "
								SELECT
									pachis, pacced, pactid, pacnui, pacap1, '.' as pacap2, pacnom, pacsex, paclug, pacnac, pacest, pacdir, pactel, paczon, pacmun, '.' as pactra, pacemp, paccer,
									pacres, pacdre, pactre, pactar, pacnum, pacfec, pachor, pacser, '.' as pacdin, paccin, '.' as pacpol
								FROM
									inpac
								WHERE
									pactra IS NULL
									AND pacap2 IS NULL
									AND pacdin IS NULL
									AND pacpol IS NULL

								UNION

								SELECT
									pachis, pacced, pactid, pacnui, pacap1, '.' as pacap2, pacnom, pacsex, paclug, pacnac, pacest, pacdir, pactel, paczon, pacmun, '.' as pactra, pacemp, paccer,
									pacres, pacdre, pactre, pactar, pacnum, pacfec, pachor, pacser, '.' as pacdin, paccin, pacpol
								FROM
									inpac
								WHERE
									pactra IS NULL
									AND pacap2 IS NULL
									AND pacdin IS NULL
									AND pacpol IS NOT NULL

								UNION

								SELECT
									pachis, pacced, pactid, pacnui, pacap1, '.' as pacap2, pacnom, pacsex, paclug, pacnac, pacest, pacdir, pactel, paczon, pacmun, '.' as pactra, pacemp, paccer,
									pacres, pacdre, pactre, pactar, pacnum, pacfec, pachor, pacser, pacdin, paccin, '.' as pacpol
								FROM
									inpac
								WHERE
									pactra IS NULL
									AND pacap2 IS NULL
									AND pacdin IS NOT NULL
									AND pacpol IS NULL

								UNION

								SELECT
									pachis, pacced, pactid, pacnui, pacap1, '.' pacap2, pacnom, pacsex, paclug, pacnac, pacest, pacdir, pactel, paczon, pacmun, '.' as pactra, pacemp, paccer,
									pacres, pacdre, pactre, pactar, pacnum, pacfec, pachor, pacser, pacdin, paccin, pacpol
								FROM
									inpac
								WHERE
									pactra IS NULL
									AND pacap2 IS NULL
									AND pacdin IS NOT NULL
									AND pacpol IS NOT NULL

								UNION

								SELECT
									pachis, pacced, pactid, pacnui, pacap1, pacap2, pacnom, pacsex, paclug, pacnac, pacest, pacdir, pactel, paczon, pacmun, '.' as pactra, pacemp, paccer,
									pacres, pacdre, pactre, pactar, pacnum, pacfec, pachor, pacser, '.' as pacdin, paccin, '.' as pacpol
								FROM
									inpac
								WHERE
									pactra IS NULL
									AND pacap2 IS NOT NULL
									AND pacdin IS NULL
									AND pacpol IS NULL

								UNION

								SELECT
									pachis, pacced, pactid, pacnui, pacap1, pacap2, pacnom, pacsex, paclug, pacnac, pacest, pacdir, pactel, paczon, pacmun, '.' as pactra, pacemp, paccer,
									pacres, pacdre, pactre, pactar, pacnum, pacfec, pachor, pacser, '.' as pacdin, paccin, pacpol
								FROM
									inpac
								WHERE
									pactra IS NULL
									AND pacap2 IS NOT NULL
									AND pacdin IS NULL
									AND pacpol IS NOT NULL

								UNION

								SELECT
									pachis, pacced, pactid, pacnui, pacap1, pacap2, pacnom, pacsex, paclug, pacnac, pacest, pacdir, pactel, paczon, pacmun, '.' as pactra, pacemp, paccer,
									pacres, pacdre, pactre, pactar, pacnum, pacfec, pachor, pacser, pacdin, paccin, '.' as pacpol
								FROM
									inpac
								WHERE
									pactra IS NULL
									AND pacap2 IS NOT NULL
									AND pacdin IS NOT NULL
									AND pacpol IS NULL

								UNION

								SELECT
									pachis, pacced, pactid, pacnui, pacap1, pacap2, pacnom, pacsex, paclug, pacnac, pacest, pacdir, pactel, paczon, pacmun, '.' as pactra, pacemp, paccer,
									pacres, pacdre, pactre, pactar, pacnum, pacfec, pachor, pacser, pacdin, paccin, pacpol
								FROM
									inpac
								WHERE
									pactra IS NULL
									AND pacap2 IS NOT NULL
									AND pacdin IS NOT NULL
									AND pacpol IS NOT NULL

								UNION

								SELECT
									pachis, pacced, pactid, pacnui, pacap1, '.' as pacap2, pacnom, pacsex, paclug, pacnac, pacest, pacdir, pactel, paczon, pacmun, pactra, pacemp, paccer,
									pacres, pacdre, pactre, pactar, pacnum, pacfec, pachor, pacser, '.' as pacdin, paccin, '.' as pacpol
								FROM
									inpac
								WHERE
									pactra IS NOT NULL
									AND pacap2 IS NULL
									AND pacdin IS NULL
									AND pacpol IS NULL

								UNION

								SELECT
									pachis, pacced, pactid, pacnui, pacap1, '.' as  pacap2, pacnom, pacsex, paclug, pacnac, pacest, pacdir, pactel, paczon, pacmun, pactra, pacemp, paccer,
									pacres, pacdre, pactre, pactar, pacnum, pacfec, pachor, pacser, '.' as pacdin, paccin, pacpol
								FROM
									inpac
								WHERE
									pactra IS NOT NULL
									AND pacap2 IS NULL
									AND pacdin IS NULL
									AND pacpol IS NOT NULL

								UNION

								SELECT
									pachis, pacced, pactid, pacnui, pacap1, '.' as pacap2, pacnom, pacsex, paclug, pacnac, pacest, pacdir, pactel, paczon, pacmun, pactra, pacemp, paccer,
									pacres, pacdre, pactre, pactar, pacnum, pacfec, pachor, pacser, pacdin, paccin, '.' as pacpol
								FROM
									inpac
								WHERE
									pactra IS NOT NULL
									AND pacap2 IS NULL
									AND pacdin IS NOT NULL
									AND pacpol IS NULL

								UNION

								SELECT
									pachis, pacced, pactid, pacnui, pacap1, '.' as pacap2, pacnom, pacsex, paclug, pacnac, pacest, pacdir, pactel, paczon, pacmun, pactra, pacemp, paccer,
									pacres, pacdre, pactre, pactar, pacnum, pacfec, pachor, pacser, pacdin, paccin, pacpol
								FROM
									inpac
								WHERE
									pactra IS NOT NULL
									AND pacap2 IS NULL
									AND pacdin IS NOT NULL
									AND pacpol IS NOT NULL

								UNION

								SELECT
									pachis, pacced, pactid, pacnui, pacap1, pacap2, pacnom, pacsex, paclug, pacnac, pacest, pacdir, pactel, paczon, pacmun, pactra, pacemp, paccer,
									pacres, pacdre, pactre, pactar, pacnum, pacfec, pachor, pacser, '.' as pacdin, paccin, '.' as pacpol
								FROM
									inpac
								WHERE
									pactra IS NOT NULL
									AND pacap2 IS NOT NULL
									AND pacdin IS NULL
									AND pacpol IS NULL

								UNION

								SELECT
									pachis, pacced, pactid, pacnui, pacap1, pacap2, pacnom, pacsex, paclug, pacnac, pacest, pacdir, pactel, paczon, pacmun, pactra, pacemp, paccer,
									pacres, pacdre, pactre, pactar, pacnum, pacfec, pachor, pacser, '.' as pacdin, paccin, pacpol
								FROM
									inpac
								WHERE
									pactra IS NOT NULL
									AND pacap2 IS NOT NULL
									AND pacdin IS NULL
									AND pacpol IS NOT NULL

								UNION

								SELECT
									pachis, pacced, pactid, pacnui, pacap1, pacap2, pacnom, pacsex, paclug, pacnac, pacest, pacdir, pactel, paczon, pacmun, pactra, pacemp, paccer,
									pacres, pacdre, pactre, pactar, pacnum, pacfec, pachor, pacser, pacdin, paccin, '.' as pacpol
								FROM
									inpac
								WHERE
									pactra IS NOT NULL
									AND pacap2 IS NOT NULL
									AND pacdin IS NOT NULL
									AND pacpol IS NULL

								UNION

								SELECT
									pachis, pacced, pactid, pacnui, pacap1, pacap2, pacnom, pacsex, paclug, pacnac, pacest, pacdir, pactel, paczon, pacmun, pactra, pacemp, paccer,
									pacres, pacdre, pactre, pactar, pacnum, pacfec, pachor, pacser, pacdin, paccin, pacpol
								FROM
									inpac
								WHERE
									pactra IS NOT NULL
									AND pacap2 IS NOT NULL
									AND pacdin IS NOT NULL
									AND pacpol IS NOT NULL
							";

			if( isset( $parte2 ) ){


			$err_o= odbc_do($conex_o,$query);
			$num_oxxx = odbc_num_fields($err_o);
			echo "NUMERO PACIENTES ACTIVOS: ".$num_oxxx."<br>";

			/**********************************************************************************************************
				Se inserta en una tabla temporal en matrix, los pacientes leídos desde unix.
			***********************************************************************************************************/

			$horas_total="";
			// $nmtemp = $bd."_".date("his");
			$filas_unix = "";
			$coma = '';
			$ctl_lote = 1000;
			$lote = $ctl_lote;
			$total_lotes = 0;
			echo "PACIENTES ACTIVOS:<BR>";
			/*liberarConexionOdbc( $conex_o );
			odbc_close_all();
			exit;*/

			$arr_his = array();
			$arr_ing = array();
			$arr_res = array();
			while(odbc_fetch_row($err_o) && $total_lotes < 5000) // && $total_lotes < 5000
			{
				/* Valicaciones necesarias antes de guardar datos en la tabla */
				if( trim( odbc_result($err_o,2) ) == '' )
						continue;
					echo "<BR><BR>HISTORIA: ".odbc_result($err_o,1);

								$servIng="";
								$tipoIng="";
								$CausaIng="";
								//Busco que la historia no este en la tabla
								$sql = "SELECT *
										FROM ".$bd."_000100
										WHERE pachis = '".trim( odbc_result($err_o,1) )."'
											OR pacdoc = '".$this->escapar( trim( odbc_result($err_o,2) ) )."'
										";

								$resPac = mysql_query( $sql, $conex );
								$numPac = mysql_num_rows( $resPac ); //AQUI
								echo "<br>numrows: ".$numPac."- Para la his: ".( odbc_result($err_o,1) )."<br>";
								if( $numPac == 0 ){
									//$this->guardarResponsables($conex_o, trim( odbc_result($err_o,1) ),trim( odbc_result($err_o,23) ));
									array_push($arr_his, trim( odbc_result($err_o,1) ) );
									array_push($arr_ing, trim( odbc_result($err_o,23) ) );
									array_push($arr_res, trim( odbc_result($err_o,18) ) );
									$paisNac = '';
									if( !isset( $arrPaisNac[ trim( substr( odbc_result($err_o,10), 0, 2 ) ) ] ) ){

										//Consulto el codigo del pais de nacimiento
										$sqlPais = "SELECT codigoPais
												FROM root_000002
												WHERE Codigo = '".trim( substr( odbc_result($err_o,10), 0, 2 ) )."'
												ORDER BY Descripcion
												";

										$resPais = mysql_query( $sqlPais, $conex ) or die( mysql_errno()." - Error en el query $$sqlPais - ".mysql_error() );

										if( $rows = mysql_fetch_array( $resPais ) ){
											$paisNac = $rows[ 'codigoPais' ];

											$arrPaisNac[ trim( substr( odbc_result($err_o,10), 0, 2 ) ) ] = $paisNac;
										}
									}
									else{
										$paisNac = $arrPaisNac[ trim( substr( odbc_result($err_o,10), 0, 2 ) ) ];
									}


									$paisRes = '';
									if( !isset( $arrPaisNac[ trim( substr( odbc_result($err_o,15), 0, 2 ) ) ] ) ){
										//Consulto el codigo del pais de residencia
										$sqlPais = "SELECT codigoPais
												FROM root_000002
												WHERE Codigo = '".trim( substr( trim( odbc_result($err_o,15) ), 0, 2 ) )."'
												ORDER BY Descripcion
												";

										$resPais = mysql_query( $sqlPais, $conex ) or die( mysql_errno()." - Error en el query $$sqlPais - ".mysql_error() );

										if( $rows = mysql_fetch_array( $resPais ) ){
											$paisRes = $rows[ 'codigoPais' ];

											$arrPaisNac[ trim( substr( odbc_result($err_o,15), 0, 2 ) ) ] = $paisRes;
										}
									}
									else{
										$paisRes = $arrPaisNac[ trim( substr( odbc_result($err_o,15), 0, 2 ) ) ];
									}


									$estCivil = '';
									if( !isset( $arrEstCivil[ trim( odbc_result($err_o,11) ) ] ) ){
										//Consulto estado civil
										$sqlEstadoCivil = "SELECT Selcod
															FROM ".$bd."_000105
															WHERE Seltip = '25'
															  AND selmat = '".trim( odbc_result($err_o,11) )."'
															";

										$resPais = mysql_query( $sqlEstadoCivil, $conex ) or die( mysql_errno()." - Error en el query $sqlEstadoCivil - ".mysql_error() );

										if( $rows = mysql_fetch_array( $resPais ) ){
											$estCivil = $rows[ 'Selcod' ];
											$arrEstCivil[ trim( odbc_result($err_o,11) ) ] = $estCivil;
										}
									}
									else{
										$estCivil = $arrEstCivil[ trim( odbc_result($err_o,11) ) ];
									}

									//Averiguo el primer y segundo nombre
									$nombreUnix = trim( odbc_result($err_o,7) );
									$posSegundoNombre = strpos( $nombreUnix, ' ' );
									if( $posSegundoNombre > 0 ){
										$pacNom1 = $this->escapar( substr(  $nombreUnix , 0, $posSegundoNombre ) );
										$pacNom2 = $this->escapar( trim( substr( $nombreUnix, $posSegundoNombre ) ) );
									}
									else{
										$pacNom1 =  $this->escapar( $nombreUnix );
										$pacNom2 = '';
									}

									//Consulto tipo de residencia
									$tipoResidencia = 'E';	//Extranjera
									if( $paisRes == '169' ){
										$tipoResidencia = 'N';	//Nacional
									}

									//conversion de hora de ingreso
									$hora=odbc_result($err_o,25);
									if( !isset($hora))
									{
										$horaIng=trim( odbc_result($err_o,25) );
										$horaIng=explode(".",$horaIng);
										$horas = $horaIng[0];
										$minutos = $horaIng[1];
										$horas_total = $horas.":".$minutos.":00";
									}

									//conversion servicio de ingreso
									$ser= odbc_result($err_o,26);
									if( !isset($ser))
									{
										//Consulto el cc
										$sqlSer = "SELECT Ccocod,Ccotin
													FROM ".$wbasedato_mov."_000011
													WHERE Ccoseu = '".trim( odbc_result($err_o,26) )."'
															";
										$resSer = mysql_query( $sqlSer, $conex ) or die( mysql_errno()." - Error en el query $sqlSer - ".mysql_error() );

										if( $rowsSer = mysql_fetch_array( $resPais ) )
										{
											$servIng=$rowsSer['Ccocod'];
											$tipoIng=$rowsSer['Ccotin'];
										}
										else
										{
											$servIng="";
											$tipoIng="";
										}
									}

									//conversion causa de ingreso
									$cau=odbc_result($err_o,28);
									if( !isset( $cau ) )
									{
										//consulta la causa de ingreso
										$sqlCau = "SELECT
																Selcod
															FROM
																".$bd."_000105
															WHERE
																Seltip = '12'
																AND Selcux= '".trim( odbc_result($err_o,28) )."'
															and Selest = 'on'
															";

										$resCau = mysql_query( $sqlCau, $conex ) or die( mysql_errno()." - Error en el query $sqlCau - ".mysql_error() );

										if( $rowsCau = mysql_fetch_array( $resCau ) )
										{
											$CausaIng = $rowsCau[ 'Selcod' ];
										}
										else
										{
											$CausaIng = "";
										}
									}


									//ACA CONTINUARIAN LOS INSERT
								 }
								 else
								 {

									$queryIngx = "SELECT id FROM cliame_000101
													WHERE Inghis='".trim( odbc_result($err_o,1) )."'
													AND Ingnin = '".trim( odbc_result($err_o,23) )."'";

									$resingx = mysql_query( $queryIngx, $conex );
									$numingx = mysql_num_rows( $resingx );

									if( $numingx == 0 ){
										$filas_unix_aux =" (
													'".$bd."',
													'".date('Y-m-d')."',
													'".date('H:i:s')."',
													'".trim( odbc_result($err_o,1) )."',
													'".trim( odbc_result($err_o,23) )."',
													'".trim( odbc_result($err_o,24) )."',
													'',
													'',
													'',
													'',
													'',
													'".trim( odbc_result($err_o,22) )."',
													'',
												'C-".$bd."' )";
										$q = "	INSERT INTO ".$bd."_000101(
												Medico,
												Fecha_data,
												Hora_data,
												Inghis,
												Ingnin,
												Ingfei,
												Ingpol,
												Ingcai,
												Ingdig,
												Ingsei,
												Inghin,
												Ingtar,
												Ingcem,
												Seguridad
											) VALUES ".$filas_unix_aux.';';
											if($err = mysql_query($q,$conex))
											{
												echo "<br>Insert&oacute; correctamente en '000101'".trim( odbc_result($err_o,1) )." ) <br>";
												//$act1=$this->actualizarAplicacion( $conex, '01', 'Rowid', trim( odbc_result($err_o,21) ) );
											}
									}else{
										/*$qq="UPDATE cliame_000101 set Ingtar='".trim( odbc_result($err_o,22) )."' WHERE inghis='".trim( odbc_result($err_o,1) )."' AND ingnin='".trim( odbc_result($err_o,23) )."' limit 1";
										$res = mysql_query( $qq, $conex );*/
									}
									$this->guardarResponsables($conex_o, trim( odbc_result($err_o,1) ),trim( odbc_result($err_o,23) ),trim( odbc_result($err_o,18) ));
									continue;
								 }
				/* fin valicaciones antes de guardar datos en la tabla*/
				if($lote == 0)
				{
					echo "INSERTA TEMPORAL1 ".$nmtemp."<BR>";
					// Inserta lote en temporal
					if($this->insertarTemporal($conex,$nmtemp,$filas_unix,$total_lotes,'temporal1'))
					{ }
					$filas_unix = "";
					$lote = $ctl_lote;
					$coma="";
				}

			/*   		1		2		3		4		5		6		7		8		9		10		11		12		13		14		15		16		17	   18
						19      20       21      22      23      24      25      26      27      28      29

						pachis, pacced, pactid, pacnui, pacap1, pacap2, pacnom, pacsex, paclug, pacnac, pacest, pacdir, pactel, paczon, pacmun, pactra, pacemp, paccer,
						pacres, pacdre, pactre, pactar, pacnum, pacfec, pachor, pacser, pacdin, paccin, pacpol */

				$filas_unix .= $coma." (
									'".$bd."',
									'".date('Y-m-d')."',
									'".date('H:i:s')."',
									'".trim( odbc_result($err_o,1))."',
									'".$this->escapar(trim( odbc_result($err_o,2)))."',
									'".trim( odbc_result($err_o,3))."',
									'".trim( odbc_result($err_o,4))."',
									'".$this->escapar(trim( odbc_result($err_o,5)))."',
									'".$this->escapar(trim( odbc_result($err_o,6)))."',
									'".$pacNom1."',
									'".$pacNom2."',
									'".trim( odbc_result($err_o,8))."',
									'".trim( odbc_result($err_o,9))."',
									'".trim( odbc_result($err_o,10))."',
									'".$estCivil."',
									'".$this->escapar(trim( odbc_result($err_o,12)))."',
									'".trim( odbc_result($err_o,13))."',
									'".trim( odbc_result($err_o,14))."',
									'".trim( odbc_result($err_o,15))."',
									'".trim( odbc_result($err_o,16))."',
									'".trim( odbc_result($err_o,17))."',
									'".trim( odbc_result($err_o,18))."',
									'".trim( odbc_result($err_o,19))."',
									'".trim( odbc_result($err_o,20))."',
									'".trim( odbc_result($err_o,21))."',
									'".trim( odbc_result($err_o,22))."',
									'".trim( odbc_result($err_o,23))."',
									'".trim( odbc_result($err_o,24))."',
									'".$horas_total."',
									'".$servIng."',
									'".trim( odbc_result($err_o,27))."',
									'".$CausaIng."',
									'".trim( odbc_result($err_o,29))."',
								'C-".$bd."' )";
				$coma = ',';
				$rr++;
				$total_lotes++;
				$lote--;

			} //while de la consulta a impac
			echo "FIN WHILE<BR>";
			// Puede quedar un resultante de lote menor al valor configurado en el controlador de lote, pero que debe ser insertado, por eso debe inserta el lote faltante.
			if($lote >= 0 && $filas_unix != "")
			{
				 //echo '<br>FALTANTES EN TEMPORAL1!!..<br>'.$filas_unix;
				// Inserta lote en temporal
				if($this->insertarTemporal($conex,$nmtemp,$filas_unix,$total_lotes,'temporal1'))
				{ }
				$filas_unix = "";

				echo "<br><font color='black' face='Arial'>Le&iacute;dos de unix: ".$total_lotes."</font><br>";
			}

			/* Si se insertaron datos desde Unix, continúa para realizar la comparación contra la tabla 100 y la 101 en Matrix */
			if($total_lotes > 0)
			{

				// consulta los datos que fueron guardados desde unix
				$q_un = "
							SELECT  pachis,
							pacced,
							pactid,
							pacnui,
							pacap1,
							pacap2,
							pacnom1,
							pacnom2,
							pacsex,
							paclug,
							pacnac,
							pacest,
							pacdir,
							pactel,
							paczon,
							pacmun,
							pactra,
							pacemp,
							paccer,
							pacres,
							pacdre,
							pactre,
							pactar,
							pacnum,
							pacfec,
							pachor,
							pacser,
							pacdin,
							paccin,
							pacpol
							FROM    ".$nmtemp."_act_unix
							GROUP BY pachis";

				// unset($arr_arts_unix); 	//PARA CUANDO SE UTILICE TODO

				 $arr_arts_unix = array(); // Array para guardar articulo de unix

				if($res = mysql_query($q_un,$conex))
				{
					while($row = mysql_fetch_array($res))
					{
						$arr_arts_unix[$row['pachis']] = array(
															'pachis' => $row['pachis'],
															'pacced' => $row['pacced'],
															'pactid' => $row['pactid'],
															'pacnui' => $row['pacnui'],
															'pacap1' => $row['pacap1'],
															'pacap2' => $row['pacap2'],
															'pacnom1' => $row['pacnom1'],
															'pacnom2' => $row['pacnom2'],
															'pacsex' => $row['pacsex'],
															'paclug' => $row['paclug'],
															'pacnac' => $row['pacnac'],
															'pacest' => $row['pacest'],
															'pacdir' => $row['pacdir'],
															'pactel' => $row['pactel'],
															'paczon' => $row['paczon'],
															'pacmun' => $row['pacmun'],
															'pactra' => $row['pactra'],
															'pacemp' => $row['pacemp'],
															'paccer' => $row['paccer'],
															'pacres' => $row['pacres'],
															'pacdre' => $row['pacdre'],
															'pactre' => $row['pactre'],
															'pactar' => $row['pactar'],
															'pacnum' => $row['pacnum'],
															'pacfec' => $row['pacfec'],
															'pachor' => $row['pachor'],
															'pacser' => $row['pacser'],
															'pacdin' => $row['pacdin'],
															'paccin' => $row['paccin'],
															'pacpol' => $row['pacpol']
															);
					}
						// Si hay articulos en unix, continúa con el proceso.
					if(count($arr_arts_unix)>0)
					{
							$filas_unix = "";
							$filas_unix1 = "";
							$coma = '';
							$lote = $ctl_lote;
							$total_lotes = 0;
							$total_updt = 0;
							foreach($arr_arts_unix as $key => $arr_art)
							{
								//if( $key != "49732") continue;

								/* Se hace toda la parte de la consulta a matrix y la parte de guardar los datos en el array dentro del foreach
								*/
								// consultar los pacientes en matrix
								$consulta_ok_mx = true;
								$num_row_mx = 0;

								$qmx = "
											SELECT  Pachis*1 as Pachis ,Inghis*1 as Inghis,Ingnin*1 as Ingnin
											FROM    ".$bd."_000100, ".$bd."_000101
											WHERE Pachis = '".$key."'
											  AND Pachis=Inghis
											order by Pachis, Ingnin asc";
								echo $qmx."<br>";
								if($resmx = mysql_query($qmx,$conex))
								{
									$consulta_ok_mx = true;
									$num_row_mx = mysql_num_rows($resmx);
									echo "consulta_ok_mx=true<br>";
								}
								else
								{
									$consulta_ok_mx = false;
									echo "consulta_ok_mx=false<br>";
								}

							//consultar los datos en matrix
							// unset($arr_arts_matrix);
							$arr_arts_matrix = array(); // Array para guardar articulo de matrix
							if($consulta_ok_mx)
							{
								while($row1 = mysql_fetch_array($resmx))
								{
									// if(!array_key_exists($row['Artcod'], $arr_arts_matrix))
									// {
									//     $arr_arts_matrix[$row['Artcod']] = array();
									// }
									$arr_arts_matrix[$row1['Pachis']] = array(
																		'Pachis' => trim($row1['Pachis']),
																		'Inghis' => trim($row1['Inghis']),
																		'Ingnin' => trim($row1['Ingnin'])
																	);
								}

								//Se comparan los arrays
								if(!array_key_exists($key, $arr_arts_matrix))
								{
									//echo "AAAAAAAAAAAAAAAAA";
									if($lote == 0)
									{
										// Inserta lote en la 100
										echo "TRATA DE INSERTAR 100_1: ".$filas_unix."<br>";
										echo "TRATA DE INSERTAR 101: ".$filas_unix1."<br>";
										//2014-07-01
										if($this->insertarTemporal($conex,$bd,$filas_unix,$total_lotes,'000100'))
										{
											// registrarLog($conex, $bd, $user_session, "insert", "inserta_articulo", "carga_articulos", "", "", "Inserta articulos que estan en unix y no en matrix. Secuencia lote: $total_lotes");
											 // Inserta lote en la 101
											if($this->insertarTemporal($conex,$bd,$filas_unix1,$total_lotes,'000101'))
											{
												// registrarLog($conex, $bd, $user_session, "insert", "inserta_articulo", "carga_articulos", "", "", "Inserta articulos que estan en unix y no en matrix. Secuencia lote: $total_lotes");
											}
										}



										$filas_unix = "";
										$filas_unix1 = "";
										$lote = $ctl_lote;
										$coma="";
									}

									//validacion del tipo de empresa
									if($arr_arts_unix[$key]['pacemp'] == 'P')
									{
										$cedRes=$arr_arts_unix[$key]['paccer'];
										$nomRes=$arr_arts_unix[$key]['pacres'];
										$dirRes=$arr_arts_unix[$key]['pacdre'];
										$telRes=$arr_arts_unix[$key]['pactre'];
										$codRes="";
									}
									else
									{
										$cedRes="";
										$nomRes="";
										$dirRes="";
										$telRes="";
										$codRes=$arr_arts_unix[$key]['paccer'];
									}

									$filas_unix = $coma." (
													'".$bd."',
													'".date('Y-m-d')."',
													'".date('H:i:s')."',
													'".$arr_arts_unix[$key]['pachis']."',
													'".$arr_arts_unix[$key]['pactid']."',
													'".$arr_arts_unix[$key]['pacced']."',
													'',
													'".$this->escapar($arr_arts_unix[$key]['pacap1'])."',
													'".$this->escapar($arr_arts_unix[$key]['pacap2'])."',
													'".$this->escapar($arr_arts_unix[$key]['pacnom1'])."',
													'".$this->escapar($arr_arts_unix[$key]['pacnom2'])."',
													'".$arr_arts_unix[$key]['pacnac']."',
													'".$arr_arts_unix[$key]['pacsex']."',
													'".$arr_arts_unix[$key]['pacest']."',
													'".$arr_arts_unix[$key]['pacdir']."',
													'".$arr_arts_unix[$key]['pactel']."',
													'".$arr_arts_unix[$key]['paclug']."',
													'',
													'".trim( substr( $arr_arts_unix[$key]['paclug'], 0, 2 ) )."',
													'".$paisNac."',
													'".$arr_arts_unix[$key]['paczon']."',
													'".$arr_arts_unix[$key]['pacmun']."',
													'".trim( substr( $arr_arts_unix[$key]['pacmun'], 0, 2 ) )."',
													'".$paisRes."',
													'".$arr_arts_unix[$key]['pactra']."',
													'".$tipoResidencia."',
													'on',
													'".$cedRes."',
													'".$nomRes."',
													'".$dirRes."',
													'".$telRes."',
												'C-".$bd."' )";

									$filas_unix1 = $coma." (
													'".$bd."',
													'".date('Y-m-d')."',
													'".date('H:i:s')."',
													'".$arr_arts_unix[$key]['pachis']."',
													'".$arr_arts_unix[$key]['pacnum']."',
													'".$arr_arts_unix[$key]['pacfec']."',
													'".$arr_arts_unix[$key]['pacpol']."',
													'".$arr_arts_unix[$key]['paccin']."',
													'".$arr_arts_unix[$key]['pacdin']."',
													'".$arr_arts_unix[$key]['pacser']."',
													'".$arr_arts_unix[$key]['pachor']."',
													'".$arr_arts_unix[$key]['pactar']."',
													'".$codRes."',
												'C-".$bd."' )";

									$coma = '';
									$total_lotes++;
									$lote--;

									echo "<br>Nuevo en mx: ".$key;
								}
								elseif(array_key_exists($key, $arr_arts_matrix) && $arr_arts_matrix[$key] != $arr_arts_unix[$key])
								{
									///echo "BBBBBBBBBBBBBB";
									if($arr_arts_unix[$key]['pacemp'] == 'P')
									{
										$codRes="";
									}
									else
									{
										$codRes=$arr_arts_unix[$key]['paccer'];

									}

									//echo json_encode( $arr_arts_unix[$key] )."------------<br>";
									//echo json_encode( $arr_arts_matrix[$key] );

									//validacion para solo actualizar el estado
											//ingreso unix
									if ($arr_arts_unix[$key]['pacnum'] == $arr_arts_matrix[$key]['Ingnin'])
									{
										///echo "XXXXXXXXXXXXXXXXXX";
										$query = "
										UPDATE  ".$bd."_000100
												SET Pacact = 'on'
										WHERE   Pachis = '".$arr_arts_unix[$key]['pachis']."'";

										//echo "TRATA DE ACTUALIZAR: ".$query."<br>";
										//2014-07-01
										/*if($this->actualizaMatrix($conex,$bd,$query))
										{
											$total_updt++;
											echo (!$VER_DETALLE) ? "" : "<br>actualizó el estado a on en la historia en matrix: ".$arr_arts_unix[$key]['pachis']."";
										}
										else
										{
											echo (!$VER_DETALLE) ? "" : "<br><span style='color:red;'>NO</span> actualizó el estado a on en la historia matrix: ".$arr_arts_unix[$key]['pachis']."";
										}*/
									}
									else
									{
										///echo "CCCCCCCCC";

										/*  Si al actualizar muchos articulos, el script tiende a tardar, es más por la cantidad de veces que se debe hacer update
											y no por la cantidad de articulos cargados en los arrays de comparación*/
										$query = "
											UPDATE  ".$bd."_000100
													SET Pacdir = '".$arr_arts_unix[$key]['pacdir']."',
														Pactel = '".$arr_arts_unix[$key]['pactel']."',
														Pacemp = '".$arr_arts_unix[$key]['pactra']."',
														Pacest = '".$arr_arts_unix[$key]['pacest']."',
														Paczon = '".$arr_arts_unix[$key]['paczon']."',
														Pacmuh =  '".$arr_arts_unix[$key]['pacmun']."',
														Pacdeh = '".trim( substr( $arr_arts_unix[$key]['pacmun'], 0, 2 ) )."',
														Pacact = 'on',
														Pacpah = '".$paisRes."'
											WHERE   Pachis = '".$arr_arts_unix[$key]['pachis']."'";

										//echo "TRATA DE ACTUALIZAR: ".$query."<br>";
										//2014-07-01
										/*if($this->actualizaMatrix($conex,$bd,$query))
										{
											$total_updt++;
											echo (!$VER_DETALLE) ? "" : "<br>actualizó historia en matrix: ".$arr_arts_unix[$key]['pachis']."";
										}
										else
										{
											echo (!$VER_DETALLE) ? "" : "<br><span style='color:red;'>NO</span> actualizó historia matrix: ".$arr_arts_unix[$key]['pachis']."";
										}*/

										// if(empty($filas_unix1))
											// $coma = '';
										// else
											// $coma = ',';

										//se inserta en la tabla 101
										$filas_unix2 = $coma." (
													'".$bd."',
													'".date('Y-m-d')."',
													'".date('H:i:s')."',
													'".$arr_arts_unix[$key]['pachis']."',
													'".$arr_arts_unix[$key]['pacnum']."',
													'".$arr_arts_unix[$key]['pacfec']."',
													'".$arr_arts_unix[$key]['pacpol']."',
													'".$arr_arts_unix[$key]['paccin']."',
													'".$arr_arts_unix[$key]['pacdin']."',
													'".$arr_arts_unix[$key]['pacser']."',
													'".$arr_arts_unix[$key]['pachor']."',
													'".$arr_arts_unix[$key]['pactar']."',
													'".$codRes."',
												'C-".$bd."' )";

										echo "TRATA DE INSERTAR: 101".$filas_unix2."<br>";
										//2014-07-01
										if($this->insertarTemporal($conex,$bd,$filas_unix2,$total_lotes,'000101'))
										{
											// registrarLog($conex, $bd, $user_session, "insert", "inserta_articulo", "carga_articulos", "", "", "Inserta articulos que estan en unix y no en matrix. Secuencia lote: $total_lotes");
										}
										 echo "<br>Nuevo en la tabla 101 mx: ".$arr_arts_unix[$key]['pachis']." ingreso ".$arr_arts_unix[$key]['pacnum']."";

									}
								}
							}

							if($lote >= 0 && $filas_unix != "")
							{
								echo '<br>FALTANTES!!..<br>';
								// Inserta lote en la 26
								echo "TRATA DE INSERTAR 100_2: ".$filas_unix."<br>";
								echo "TRATA DE INSERTAR 101: ".$filas_unix1."<br>";
								//2014-07-01
								if($this->insertarTemporal($conex,$bd,$filas_unix,$total_lotes,'000100'))
								{
									// registrarLog($conex, $bd, $user_session, "insert", "inserta_articulo", "carga_articulos", "", "", "Inserta articulos que estas en unix y no en matrix. Secuencia lote: $total_lotes");
									if($this->insertarTemporal($conex,$bd,$filas_unix1,$total_lotes,'000101'))
									{
										// registrarLog($conex, $bd, $user_session, "insert", "inserta_articulo", "carga_articulos", "", "", "Inserta articulos que estas en unix y no en matrix. Secuencia lote: $total_lotes");
									}
								}

								$filas_unix1 = "";
							}

							if($total_lotes>0)
							{
								"<br>Historias nuevas: ".$total_lotes;
							}
							// if($total_updt>0)
							// {
								// echo (!$VER_DETALLE) ? "" : "<br><font color='black' face='Arial'>Articulos actualizados: ".$total_updt."</font>";
								// // registrarLog($conex, $bd, $user_session, "update", "actualiza_articulos", "carga_articulos", "", "", "Actualiza articulos en Matrix que esta modificados en unix. Modificados: $total_updt");
							// }
						}
					}
					else
					{
						echo "<br><font color='red' face='Arial'>No pudo consulta root_000037<br>".$qmx."</font>";
					}
				} //if de res de la consulta a la temporal
				else
				{
					echo "<br><font color='black' face='Arial'>No pudo consultar temporal de pacientes leídos desde unix.<br>".$q_un."</font>";
				}

				for( $indic=1; $indic<=count($arr_his); $indic++ ){
					$this->guardarResponsables( $conex_o, $arr_his[ $indic ], $arr_ing[ $indic ], $arr_res[ $indic ] );
				}

			}  //total_lotes > 0
			}


			if( isset($parte3) ){

				echo "parte 3";

				$rowIdAccidentes = consultarAliasPorAplicacion($conex, $wemp_pmla, "rowIdAccidentes");

				$campos_nulos = "accdetpol, accdetfin, accdetffi, accdetmar,accdetpla,accdettip";
				$defecto_nulo = "'',date(0000-00-00),date(0000-00-00),'','',''";
				//				1		2			3			4		5			6		7		8			9			10		11		12			13			14		15			16			17		18		19		20			21			22		23
				$select = "accdethis, accdetnum, accdetacc,accdetfec,accdethor,accdetmun,accdetzon,accdetlug,accdetocu,accdetase,accdetpol,accdetfin,accdetffi,accdetmar,accdetpla,accdettip,accdetres,accdettar,accdettop,accdetvsm,accdetre2,accdetaut,rowid";
				$tablas = "inaccdet";
				//accdetfec >= '2010-01-01
				$condiciones_where = " rowid > ".$rowIdAccidentes." AND accdetfec >= '2010-01-01' AND accdethor IS NOT NULL AND accdetmun IS NOT NULL AND accdetzon IS NOT NULL AND	accdetlug IS NOT NULL
										AND accdetocu IS NOT NULL
										AND accdetase IS NOT NULL
										AND accdettop IS NOT NULL
										AND accdetvsm IS NOT NULL
										AND accdetaut IS NOT NULL
										AND accdettar IS NOT NULL
										";
				$query = $this->construirQueryUnix( $tablas,$campos_nulos,$select,$condiciones_where,$defecto_nulo);
				$query.=" ORDER BY 23"; //ordernar por rowid

				//echo $query;
				echo "<br><br>----------------------------------------------------------------------------";

				$total_registros = 0;
				$err_o= odbc_do($conex_o,$query);
				$num_o = odbc_num_fields($err_o);
				$rowid="";
				$limite_query = 100;
				if( isset( $cantidadp3) && $cantidadp3 > 0 ){
					$limite_query = $cantidadp3;
				}
				//return;
				while(odbc_fetch_row($err_o) && $total_registros < $limite_query )
				{
					$total_registros++;

					$rowid = odbc_result($err_o,23);
					echo "<br>rowid: ".$rowid;

					$registro_accidente = array('Acchis'=>odbc_result($err_o,1),
							'Accing'=>odbc_result($err_o,2),
							'Acccon'=>odbc_result($err_o,9),
							'Accdir'=>odbc_result($err_o,8),
							'Accfec'=>odbc_result($err_o,4),
							'Acchor'=>odbc_result($err_o,5),
							'Accdep'=>'05',
							'Accmun'=>odbc_result($err_o,6),
							'Acczon'=>odbc_result($err_o,7),
							'Accdes'=> '',
							'Accase'=>odbc_result($err_o,10),
							'Accmar'=>odbc_result($err_o,14),
							'Accpla'=>odbc_result($err_o,15),
							'Acctse'=>odbc_result($err_o,16),
							'Acccas'=>'',
							'Accpol'=>odbc_result($err_o,11),
							'Accvfi'=>odbc_result($err_o,12),
							'Accvff'=>odbc_result($err_o,13),
							'Accaut'=>odbc_result($err_o,22),
							'Acctop'=>odbc_result($err_o,19),
							'Accres'=>odbc_result($err_o,17),
							'Acctar'=>odbc_result($err_o,18),
							'Accre2'=>odbc_result($err_o,21),
							'Accvsm'=>odbc_result($err_o,20),
							'Acccep'=>'off','Accap1'=>'','Accap2'=>'','Accno1'=>'','Accno2'=>'','Accnid'=>'','Acctid'=>'','Accpdi'=>'','Accpdd'=>'','Accpdp'=>'',
							'Accpmn'=>'','Acctel'=>'','Accca1'=>'','Accca2'=>'','Acccn1'=>'','Acccn2'=>'','Acccni'=>'','Acccti'=>'','Acccdi'=>'','Acccdd'=>'',
							'Acccdp'=>'','Acccmn'=>'','Accctl'=>'','Accest'=>'on','Accemp'=>'','Accre3'=>'','Accno3'=>'','Accrei'=>'','Accdtd'=>'');

					switch( $registro_accidente['Acctse'] )
					{
						case 'PARTICULAR':{
							$registro_accidente['Acctse'] = 1;
						}
						 break;
						 case 'PUBLICO':{
							$registro_accidente['Acctse'] = 2;
						}
						 break;
						 case 'OFICIAL':{
							$registro_accidente['Acctse'] = 3;
						}
						 break;
						 case 'DE EMERGENCIA':{
							$registro_accidente['Acctse'] = 4;
						}
						 break;
						 case 'DIPLOMATICO O CONSULAR':{
							$registro_accidente['Acctse'] = 5;
						}
						 break;
						 case 'TRANSPORTE MASIVO':{
							$registro_accidente['Acctse'] = 6;
						}
						 break;
						 case 'ESCOLAR':{
							$registro_accidente['Acctse'] = 7;
						}
						 break;
					}
					switch( $registro_accidente['Accaut'] )
					{
						case 'S':{
							$registro_accidente['Acctse'] = 'on';
						}
						 break;
						 case 'N':{
							$registro_accidente['Acctse'] = 'off';
						}
					}
					$numero_accidente = odbc_result($err_o,3);

					$historia = $registro_accidente['Acchis'];
					$ingreso = $registro_accidente['Accing'];


					$registro_accidente['Accfec'] = str_replace("/","-",$registro_accidente['Accfec']);
					$registro_accidente['Accvfi'] = str_replace("/","-",$registro_accidente['Accvfi']);
					$registro_accidente['Accvff'] = str_replace("/","-",$registro_accidente['Accvff']);
					$registro_accidente['Acchor'] = str_replace(".",":",$registro_accidente['Acchor']);



					$campos_nulos = "accdetob1,accdetob2,accdetcas";
					$select = "accdetob1,accdetob2,accdetcas";
					$tablas = "inaccdet";
					$condiciones_where = "accdethis='".$historia."' AND accdetnum='".$ingreso."' AND accdetacc='".$numero_accidente."'";
					$query_pro1 = $this->construirQueryUnix( $tablas,$campos_nulos,$select,$condiciones_where);

					$err_o_pro1= odbc_do($conex_o,$query_pro1);
					$num_o_pro1 = odbc_num_fields($err_o_pro1);

					while( odbc_fetch_row($err_o_pro1) )
					{
						$registro_accidente['Acccas'] = odbc_result($err_o_pro1,3);
						$registro_accidente['Accdes'] = ( odbc_result($err_o_pro1,1)." ".odbc_result($err_o_pro1,2) );
					}




					$campos_nulos = "accproap1,accproap2,accprono1,accprono2,accprotid";
					$select = "accproap1,accproap2,accprono1,accprono2,accprotid";
					$tablas = "inaccpro";
					$condiciones_where = "accprohis='".$historia."' AND accpronum='".$ingreso."' AND accproacc='".$numero_accidente."'";
					$query_pro1 = $this->construirQueryUnix( $tablas,$campos_nulos,$select,$condiciones_where);

					$err_o_pro1= odbc_do($conex_o,$query_pro1);
					$num_o_pro1 = odbc_num_fields($err_o_pro1);

					while( odbc_fetch_row($err_o_pro1) )
					{
						$registro_accidente['Accap1'] = odbc_result($err_o_pro1,1);
						$registro_accidente['Accap2'] = odbc_result($err_o_pro1,2);
						$registro_accidente['Accno1'] = odbc_result($err_o_pro1,3);
						$registro_accidente['Accno2'] = odbc_result($err_o_pro1,4);
						$registro_accidente['Acctid'] = odbc_result($err_o_pro1,5);
					}

					$campos_nulos = "accproide,accprodep,accpromun,accprodir,accprotel";
					$select = "accproide,accprodep,accpromun,accprodir,accprotel";
					$tablas = "inaccpro";
					$condiciones_where = "accprohis='".$historia."' AND accpronum='".$ingreso."' AND accproacc='".$numero_accidente."'";
					$query_pro12 = $this->construirQueryUnix( $tablas,$campos_nulos,$select,$condiciones_where);

					$err_o_pro12= odbc_do($conex_o,$query_pro12);
					$num_o_pro1 = odbc_num_fields($err_o_pro12);

					while( odbc_fetch_row($err_o_pro12) )
					{
						$registro_accidente['Accnid'] = odbc_result($err_o_pro12,1);
						$registro_accidente['Accpdp'] = odbc_result($err_o_pro12,2);
						$registro_accidente['Accpmn'] = odbc_result($err_o_pro12,3);
						$registro_accidente['Accpdi'] = odbc_result($err_o_pro12,4);
						$registro_accidente['Acctel'] = odbc_result($err_o_pro12,5);
					}


					$campos_nulos = "accproac1,accproac2,accpronc1,accpronc2,accprotic,accproid2";
					$select = "accproac1,accproac2,accpronc1,accpronc2,accprotic,accproid2";
					$tablas = "inaccpro";
					$condiciones_where = "AND accprohis='".$historia."' AND accpronum='".$ingreso."' AND accproacc='".$numero_accidente."'";
					$query_pro2 = $this->construirQueryUnix( $tablas,$campos_nulos,$select,$condiciones_where);

					$err_o_pro2= odbc_do($conex_o,$query_pro2);
					$num_o_pro2 = odbc_num_fields($err_o_pro2);

					while(odbc_fetch_row($err_o_pro2) )
					{
						$registro_accidente['Accac1'] = odbc_result($err_o_pro2,1);
						$registro_accidente['Accac2'] = odbc_result($err_o_pro2,2);
						$registro_accidente['Accnc1'] = odbc_result($err_o_pro2,3);
						$registro_accidente['Accnc2'] = odbc_result($err_o_pro2,4);
						$registro_accidente['Acctic'] = odbc_result($err_o_pro2,5);
						$registro_accidente['Accid2'] = odbc_result($err_o_pro2,6);
					}

					$select = "accind,accnum";
					$tablas = "inacc";
					$condiciones_where = "acchis='".$historia."' AND accacc='".$numero_accidente."' AND accnum IS NOT NULL ORDER BY accind desc";
					$query_acc = $this->construirQueryUnix( $tablas,'',$select,$condiciones_where);

					$err_o_acc= odbc_do($conex_o,$query_acc);
					$num_o_acc = odbc_num_fields($err_o_acc);

					while(odbc_fetch_row($err_o_acc) )
					{
						$accind = odbc_result($err_o_acc,1);
						$ingreso_acc = odbc_result($err_o_acc,2);
						$datoac = array();
						$datoac = $registro_accidente;
						if( $accind != "P" ){
							$datoac['Accrei'] = $ingreso;
							$datoac['Accing'] = $ingreso_acc;
						}
						//Busco que la historia no este en la tabla
						$sql = "SELECT *
								  FROM cliame_000148
								 WHERE acchis = '".$historia."'
								   AND accing = '".$datoac['Accing']."'
								";
						$resPac = mysql_query( $sql, $conex );
						$numPac = mysql_num_rows( $resPac );
						if( $numPac == 0 ){
							//Inserto datos de ingreso
							$sql_148 = "INSERT INTO cliame_000148(    Medico,	Fecha_data,				Hora_data			,	Acchis,						Accing,						Acccon,				Accdir,					Accdtd,					Accfec,					Acchor,					Accdep,					Accmun,						Acczon,					Accdes,				Accase,					Accmar,						Accpla,					Acctse,					Acccas,					Accpol,					Accvfi,					Accvff,					Accaut,						Acccep,				Accap1,					Accap2,					Accno1,					Accno2,					Accnid,					Acctid,					Accpdi,					Accpdd,					Accpdp,					Accpmn,						Acctel,					Accca1,					Accca2,					Acccn1,				Acccn2,					Acccni,					Acccti,					Acccdi,					Acccdd,					Acccdp,					Acccmn,					Accctl,						Accest,					Accres,				Acctar,					Accre2,					Acctop,					Accvsm,					Accemp,					Accre3,					Accno3,					Accrei,			Seguridad  )
														   VALUES( 'cliame',  '".date("Y-m-d")."' ,  '".date("H:i:s")."' , '".$datoac['Acchis']."', '".$datoac['Accing']."', '".$datoac['Acccon']."', '".$datoac['Accdir']."','".$datoac['Accdtd']."','".$datoac['Accfec']."','".$datoac['Acchor']."','".$datoac['Accdep']."','".$datoac['Accmun']."','".$datoac['Acczon']."','".$datoac['Accdes']."','".$datoac['Accase']."','".$datoac['Accmar']."','".$datoac['Accpla']."','".$datoac['Acctse']."','".$datoac['Acccas']."','".$datoac['Accpol']."','".$datoac['Accvfi']."','".$datoac['Accvff']."','".$datoac['Accaut']."','".$datoac['Acccep']."','".$datoac['Accap1']."','".$datoac['Accap2']."','".$datoac['Accno1']."','".$datoac['Accno2']."','".$datoac['Accnid']."','".$datoac['Acctid']."','".$datoac['Accpdi']."','".$datoac['Accpdd']."','".$datoac['Accpdp']."','".$datoac['Accpmn']."','".$datoac['Acctel']."','".$datoac['Accca1']."','".$datoac['Accca2']."','".$datoac['Acccn1']."','".$datoac['Acccn2']."','".$datoac['Acccni']."','".$datoac['Acccti']."','".$datoac['Acccdi']."','".$datoac['Acccdd']."','".$datoac['Acccdp']."','".$datoac['Acccmn']."','".$datoac['Accctl']."','".$datoac['Accest']."','".$datoac['Accres']."','".$datoac['Acctar']."','".$datoac['Accre2']."','".$datoac['Acctop']."','".$datoac['Accvsm']."','".$datoac['Accemp']."','".$datoac['Accre3']."','".$datoac['Accno3']."','".$datoac['Accrei']."','C-$wbasedato' );";
							 echo $sql_148."<BR><BR>";
							 $resIng = mysql_query( $sql_148, $this->conex );

							/* if( mysql_affected_rows() > 0 ){
								 $sql_100 = "UPDATE cliame_000101 SET Ingcai='02' WHERE Inghis='".$historia."' AND Ingnin='".$registro_accidente_aux['Accing']."'";
								 $resIng = mysql_query( $sql_100, $this->conex );
							 }*/

						}else{
							$sql_148 = "UPDATE cliame_000148 SET Acccon = '".$datoac['Acccon']."', Accdir = '".$datoac['Accdir']."', Accdtd='".$datoac['Accdtd']."', Accfec='".$datoac['Accfec']."', Acchor='".$datoac['Acchor']."', Accdes='".$datoac['Accdes']."', Accase='".$datoac['Accase']."', Accmar='".$datoac['Accmar']."', Accpla='".$datoac['Accpla']."', Acccas='".$datoac['Acccas']."', Accpol='".$datoac['Accpol']."', Acctar='".$datoac['Acctar']."', Acctop='".$datoac['Acctop']."', Accvsm='".$datoac['Accvsm']."', Accrei='".$datoac['Accrei']."'
														   WHERE Acchis = '".$datoac['Acchis']."' AND Accing='".$datoac['Accing']."' limit 1";
							// echo $sql_148."<BR><BR>";
							 //$resIng = mysql_query( $sql_148, $this->conex );
						}
					}
				}

				$act1=$this->actualizarAplicacion( $conex, $wemp_pmla, 'rowIdAccidentes', $rowid );
				if ($act1 == true ){
					echo "<br>Parametro en la tabla root_000051 actualizado con exito: ".$rowid."";
				}else{
					echo "<br>No se actualizo la tabla root_000051";
				}

			}

			//2014-12-29
		liberarConexionOdbc( $conex_o );
		odbc_close_all();

	}

function corregirResponsables(){

	global $conex;
	global $bd;

	$q="SELECT reshis,resing,count(*) as cant
		  FROM cliame_000205
	  GROUP BY Reshis,resing,resnit
		HAVING count(*) > 1";
		//echo $q;
	$res = mysql_query( $q, $conex );
	$num = mysql_num_rows( $res );

	if( $num > 0 ){
		while($row = mysql_fetch_assoc($res))
		{
			$q2="DELETE FROM cliame_000205 WHERE reshis='".$row['reshis']."' AND resing='".$row['resing']."'";
			//echo $q2."<br><br>";
			$res2 = mysql_query( $q2, $conex );
		}
	}
}

//Si viene responsable, quiere decir que el paciente está activo, no se puede consultar en inmegr
function guardarResponsables( $conex_o, $historia, $ingreso,$responsable='' ){
	global $conex;
	global $bd;
	$tiposEmpresa = array();

	$fecha = date("Y-m-d");
	$hora = (string)date("H:i:s");

	if( $historia == "" || $ingreso == "" )
		return;

	$query = "SELECT Reshis
			    FROM ".$bd."_000101, ".$bd."_000205
			   WHERE Inghis='".$historia."'
			     AND Ingnin='".$ingreso."'
				 AND Reshis=Inghis
				 AND Resing=Ingnin";

	$res = mysql_query( $query, $conex );
	$num = mysql_num_rows( $res );

	if( $num == 0 ){

		$arr_responsables = array();

		if( $responsable == "" ){
			//Traer responsable
			$qunix = "SELECT egrcer,egring, egremp
						FROM inmegr
					   WHERE egrhis='".$historia."'
						 AND egrnum='".$ingreso."'
						 AND egrcer IS NOT NULL
						 AND egring IS NOT NULL
						";

			$err_o= odbc_do($conex_o,$qunix);
			$num_o = odbc_num_fields($err_o);

			$fecha_ingreso = "";
			while( odbc_fetch_row($err_o) )
			{
				if( trim( odbc_result($err_o,1) ) == '' )
					continue;
				$fecha_ingreso = str_replace("/","-",odbc_result($err_o,2));
				//array_push($arr_responsables, odbc_result($err_o,1));
				if( in_array( trim( odbc_result($err_o,1) ), $arr_responsables ) == false ){
					array_push($arr_responsables, odbc_result($err_o,1));
					$tiposEmpresa[odbc_result($err_o,1)] = odbc_result($err_o,3);
				}
			}
		}else{
			array_push($arr_responsables, $responsable );
		}

		//tiene accidente?
		$qunix = "SELECT salacccer,salacctop,salaccsal,salaccnre
					FROM inacc,fasalacc
				   WHERE acchis='".$historia."'
				     AND accnum='".$ingreso."'
					 AND acchis=salacchis
					 AND salaccacc=accacc
					 AND salacccer IS NOT NULL
					 AND salacctop IS NOT NULL
					 AND salaccsal IS NOT NULL
					 ORDER BY salaccnre";

		$err_o2= odbc_do($conex_o,$qunix);
		$num_o2 = odbc_num_fields($err_o2);

		$i=1;
		while( odbc_fetch_row($err_o2) )
		{
			if( trim( odbc_result($err_o2,1) ) == '' )
				continue;

			if( in_array( trim( odbc_result($err_o2,1) ), $arr_responsables ) == false ){
				array_push($arr_responsables, odbc_result($err_o2,1));
			}
			if( $i == 1 ){
				$saldo_matrix = odbc_result($err_o2,2) - odbc_result($err_o2,3);

				$query2 = "SELECT Tophis
							FROM  ".$bd."_000204
						   WHERE Tophis='".$historia."'
							 AND Toping='".$ingreso."'
							 limit 1";
				$res2 = mysql_query( $query2, $conex );
				$num2 = mysql_num_rows( $res2 );

				if( $num2 == 0 ){
					$sql = "INSERT INTO ".$bd."_000204 (    Medico, 		Fecha_data,				 Hora_data,			 Tophis, 			Toping ,		Topres, 				Toptco, 	Topcla, 	Topcco, 	Toptop, 						Toprec, 	Topdia, 		Topsal, 		Topest,	 	 Seguridad )
												VALUES ('".$bd."','".utf8_decode($fecha)."','".utf8_decode($hora)."', '".$historia."',	'".$ingreso."',	'".odbc_result($err_o2,1)."',  '*',		'*',		'*',		'".odbc_result($err_o2,2)."', 	'*',		'off',		'".$saldo_matrix."', 'on',		'C-cliame'  )";
					$res = mysql_query( $sql, $conex );
				}
			}
			$i++;
		}
		$i=1;
		foreach($arr_responsables as $cod_res){
			$ffi = "";
			if( $i == 1 ){
				$ffi=$fecha_ingreso;

				/*$tpa="";
				if( $cod_res != "" )
					$tpa="E";*/
				$tpa = $tiposEmpresa[$cod_res];

				$query = "UPDATE ".$bd."_000101 SET Ingcem = '".$cod_res."', Ingtpa='".$tpa."' WHERE  Inghis='".$historia."' AND ingnin='".$ingreso."' limit 1";
				$rescat = mysql_query( $query, $conex );

			}
			$sql = "INSERT INTO ".$bd."_000205 (    Medico, 		Fecha_data,				 Hora_data,			 Reshis, 			Resing,		 Resnit, 		Resord, 	Resfir,		Resest, 	Restpa, 	Seguridad )
										VALUES ('".$bd."','".utf8_decode($fecha)."','".utf8_decode($hora)."', '".$historia."',	'".$ingreso."',	'".$cod_res."',  '".$i."',	'".$ffi."',		'on',		'E', 	'C-cliame'  )";
			$res = mysql_query( $sql, $conex );
			$i++;
		}
	}
}
	/************************************************************************************************************************

/**
    insertarTemporal(), esta función se encarga de insertar lotes en la tabla temporal de los datos leidos desde unix, o de insertar los lotes
    de articulos nuevos para insertar en matrix.

    @param link     conex       : link de conexión a la base de datos matrix.
    @param string   bd          : prefijo de la tabla donde se debe insertar.
    @param string   query       : sql del lote de datos que se van a insertar.
    @param int      total_lotes : la cantidad acumulada de articulos que se ha insertado por todos los lotes.
    @param string   tabla       : indica donde se debe insertar el lote, si en la tabla de articulos de matrix o en la tabla temporal de articulos leídos desde unix.
    @return unknown
*/
function insertarTemporal($conex,$bd,$query,$total_lotes,$tabla)
{
    if($tabla == 'temporal')
    {
        // Inserta en la tabla temporal impaci
        $q = "
            CREATE TEMPORARY TABLE IF NOT EXISTS ".$bd."_inac_unix
                 (
                  Medico VARCHAR(8) NOT NULL DEFAULT '',
                  Fecha_data DATE NOT NULL DEFAULT '0000-00-00',
                  Hora_data TIME NOT NULL DEFAULT '00:00:00',
                  pachis VARCHAR(80) NOT NULL DEFAULT '',
                  pacced VARCHAR(80) NOT NULL DEFAULT '',
                  pactid VARCHAR(6) NOT NULL DEFAULT '',
                  pacnui VARCHAR(80) NOT NULL DEFAULT '',
                  pacap1 VARCHAR(80) NOT NULL DEFAULT '',
                  pacap2 VARCHAR(80) NOT NULL DEFAULT '',
                  pacnom1 VARCHAR(80) NOT NULL DEFAULT '',
				  pacnom2 VARCHAR(80) NOT NULL DEFAULT '',
                  pacsex VARCHAR(4) NOT NULL DEFAULT '',
                  pacnac DATE NOT NULL DEFAULT '0000-00-00',
                  paclug VARCHAR(10) NOT NULL DEFAULT '',
                  pacest VARCHAR(4) DEFAULT '',
				  pacdir VARCHAR(80) DEFAULT '',
				  pactel VARCHAR(20) DEFAULT '',
				  paczon VARCHAR(4) DEFAULT '',
				  pacmun VARCHAR(10) DEFAULT '',
				  pactra VARCHAR(80) DEFAULT '',
				  pacniv VARCHAR(4) DEFAULT '',
				  pacpad VARCHAR(80) DEFAULT '',
				  pacing VARCHAR(80) DEFAULT '',
				  pacnum VARCHAR(80) DEFAULT '',
				  pactar VARCHAR(10) DEFAULT '',
				  Seguridad VARCHAR(10) NOT NULL DEFAULT '',
                  id BIGINT(20) NOT NULL AUTO_INCREMENT,
                  PRIMARY KEY (id),
                  UNIQUE KEY pachis_idx (pachis)
                )";
                  //
        if($err = mysql_query($q,$conex))
        {
            //echo "Insertó correctamente en remporal 'arts_unix' (registros: ".($rr).") ";
            $q = "
                INSERT INTO ".$bd."_inac_unix(
				Medico,
				Fecha_data,
				Hora_data,
				pachis,
				pacced,
				pactid,
				pacnui,
				pacap1,
				pacap2,
				pacnom1,
				pacnom2,
				pacsex,
				pacnac,
				paclug,
				pacest,
				pacdir,
				pactel,
				paczon,
				pacmun,
				pactra,
				pacniv,
				pacpad,
				pacing,
				pacnum,
				pactar,
                Seguridad
                ) VALUES ".$query.';';
            if($err = mysql_query($q,$conex))
            {
                // echo "<br>Insertó correctamente en temporal 'arts_unix' (registros: $total_lotes)";
                return true;
            }
            else
            {
                // echo "<br>Error al insertar en tabla temporal (registros: $total_lotes).<br><pre>$q</pre>".mysql_errno().'--'.mysql_error();
                echo "<font color='#FF0000' face='Arial'><b>COMUNICAR A SISTEMAS: ".$total_lotes.") ".mysql_error()."</b><br>".$q."</font><br>";
                return false;
            }
        }
        else
        {
            // echo "<br>Error creando tabla temporal.<br><pre>$q</pre>".mysql_errno().'--'.mysql_error();
            echo "<font color='#FF0000' face='Arial'><b>COMUNICAR A SISTEMAS: ".$total_lotes.") ".mysql_error()."</b><br>".$q."</font><br>";
            return false;
        }
    }
	elseif($tabla == 'temporal1')
    {
        // Inserta en la tabla temporal impaci

        $q = "
            CREATE TEMPORARY TABLE IF NOT EXISTS ".$bd."_act_unix
                 (
                  Medico VARCHAR(8) NOT NULL DEFAULT '',
                  Fecha_data DATE NOT NULL DEFAULT '0000-00-00',
                  Hora_data TIME NOT NULL DEFAULT '00:00:00',
                  pachis VARCHAR(80) NOT NULL DEFAULT '',
                  pacced VARCHAR(80) NOT NULL DEFAULT '',
                  pactid VARCHAR(6) NOT NULL DEFAULT '',
                  pacnui VARCHAR(80) NOT NULL DEFAULT '',
                  pacap1 VARCHAR(80) NOT NULL DEFAULT '',
                  pacap2 VARCHAR(80) NOT NULL DEFAULT '',
                  pacnom1 VARCHAR(80) NOT NULL DEFAULT '',
				  pacnom2 VARCHAR(80) NOT NULL DEFAULT '',
                  pacsex VARCHAR(4) NOT NULL DEFAULT '',
                  paclug VARCHAR(10) NOT NULL DEFAULT '',
				  pacnac DATE NOT NULL DEFAULT '0000-00-00',
                  pacest VARCHAR(4) DEFAULT '',
				  pacdir VARCHAR(80) DEFAULT '',
				  pactel VARCHAR(20) DEFAULT '',
				  paczon VARCHAR(4) DEFAULT '',
				  pacmun VARCHAR(10) DEFAULT '',
				  pactra VARCHAR(80) DEFAULT '',
				  pacemp VARCHAR(80) DEFAULT '',
				  paccer VARCHAR(80) DEFAULT '',
				  pacres VARCHAR(80) DEFAULT '',
				  pacdre VARCHAR(80) DEFAULT '',
				  pactre VARCHAR(80) DEFAULT '',
				  pactar VARCHAR(80) DEFAULT '',
				  pacnum VARCHAR(80) DEFAULT '',
				  pacfec DATE NOT NULL DEFAULT '0000-00-00',
				  pachor TIME NOT NULL DEFAULT '00:00:00',
				  pacser VARCHAR(80) DEFAULT '',
				  pacdin VARCHAR(80) DEFAULT '',
				  paccin VARCHAR(10) DEFAULT '',
				  pacpol VARCHAR(80) DEFAULT '',
				  Seguridad VARCHAR(10) NOT NULL DEFAULT '',
                  id BIGINT(20) NOT NULL AUTO_INCREMENT,
                  PRIMARY KEY (id),
                  UNIQUE KEY pachis_idx (pachis)
                )";
                  //
        if($err = mysql_query($q,$conex))
        {
            //echo "Insertó correctamente en remporal 'arts_unix' (registros: ".($rr).") ";
            $q = "
                INSERT INTO ".$bd."_act_unix(
				Medico,
				Fecha_data,
				Hora_data,
				pachis,
				pacced,
				pactid,
				pacnui,
				pacap1,
				pacap2,
				pacnom1,
				pacnom2,
				pacsex,
				paclug,
				pacnac,
				pacest,
				pacdir,
				pactel,
				paczon,
				pacmun,
				pactra,
				pacemp,
				paccer,
			    pacres,
				pacdre,
				pactre,
				pactar,
				pacnum,
				pacfec,
				pachor,
				pacser,
				pacdin,
				paccin,
				pacpol,
                Seguridad
                ) VALUES ".$query.';';
            if($err = mysql_query($q,$conex))
            {
                // echo "<br>Insertó correctamente en temporal 'arts_unix' (registros: $total_lotes)";
                return true;
            }
            else
            {
                // echo "<br>Error al insertar en tabla temporal (registros: $total_lotes).<br><pre>$q</pre>".mysql_errno().'--'.mysql_error();
                echo "<font color='#FF0000' face='Arial'><b>COMUNICAR A SISTEMAS: ".$total_lotes.") ".mysql_error()."</b><br>".$q."</font><br>";
                return false;
            }
        }
        else
        {
            // echo "<br>Error creando tabla temporal.<br><pre>$q</pre>".mysql_errno().'--'.mysql_error();
            echo "<font color='#FF0000' face='Arial'><b>COMUNICAR A SISTEMAS: ".$total_lotes.") ".mysql_error()."</b><br>".$q."</font><br>";
            return false;
        }
    }
    elseif($tabla=='000100')
    {

        //Inserta en la 000100
        $q = "
            INSERT INTO ".$bd."_000100(
            Medico,
			Fecha_data,
			Hora_data,
			Pachis,
			Pactdo,
			Pacdoc,
			Pactat,
			Pacap1,
			Pacap2,
			Pacno1,
			Pacno2,
			Pacfna,
			Pacsex,
			Pacest,
			Pacdir,
			Pactel,
			Paciu,
			Pacbar,
			Pacdep,
			Pacpan,
			Paczon,
			Pacmuh,
			Pacdeh,
			Pacpah,
			Pacemp,
			Pactrh,
			Pacact,
			Paccru,
			Pacnru,
			Pacdru,
			Pactru,
            Seguridad
        ) VALUES ".$query.';';
        if($err = mysql_query($q,$conex))
        {
            // echo "<br>Insertó correctamente en '000100' (registros: $total_lotes) <br>";
            return true;
        }
        else
        {
            //echo "<br>Error al insertar en tabla '000100 (registros: $total_lotes)'.<br><pre>$q</pre>".mysql_errno().'--'.mysql_error();
            echo "<font color='#FF0000' face='Arial'><b>COMUNICAR A SISTEMAS: ".$total_lotes.") ".mysql_error()."</b><br>".$q."</font><br>";
            return false;
        }
    }
	elseif($tabla=='000101')
	{
		//Inserta en la 000101
        $q = "
            INSERT INTO ".$bd."_000101(
            Medico,
			Fecha_data,
			Hora_data,
			Inghis,
			Ingnin,
			Ingfei,
			Ingpol,
			Ingcai,
			Ingdig,
			Ingsei,
			Inghin,
			Ingtar,
			Ingcem,
            Seguridad
        ) VALUES ".$query.';';
        if($err = mysql_query($q,$conex))
        {
            // echo "<br>Insertó correctamente en '000101' (registros: $total_lotes) <br>";
            return true;
        }
        else
        {
            //echo "<br>Error al insertar en tabla '000101 (registros: $total_lotes)'.<br><pre>$q</pre>".mysql_errno().'--'.mysql_error();
            echo "<font color='#FF0000' face='Arial'><b>COMUNICAR A SISTEMAS: ".$total_lotes.") ".mysql_error()."</b><br>".$q."</font><br>";
            return false;
        }
	}
}

/*Esta funcion actualiza la tabla que se le envie*/
function actualizaMatrix($conex,$bd,$query)
{
    //Actualiza en la tabla enviada
    $q = $query;
    if($err = mysql_query($q,$conex))
    {
        // echo "<br>Insertó correctamente en '000026' (registros: $total_lotes) <br>";
        return true;
    }
    else
    {
        //echo "<br>Error al insertar en tabla '000026 (registros: $total_lotes)'.<br><pre>$q</pre>".mysql_errno().'--'.mysql_error();
        echo "<font color='#FF0000' face='Arial'><b>COMUNICAR A SISTEMAS: inconveniente al actualizar los datos.) ".mysql_error()."</b><br>".$q."</font><br>";
        return false;
    }
}

/*Esta funcion consulta el parametro en la tabla root_000051
 para saber en que registro se debe iniciar la consulta de inpaci*/
function consultarAplicacion($conexion, $codigoInstitucion, $nombreAplicacion){
	$q = " SELECT
				Detval
			FROM
				root_000051
			WHERE
				Detemp = '".$codigoInstitucion."'
				AND Detapl = '".$nombreAplicacion."'";

	// echo $q;
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


function kron_admisionesMatrix_Unix(){
	//Los siguientes arreglos tienen el nombre del campo en MATRIX y como se debe mandar a UNIX
	//dado que el programa erp_unix.php los espera de esa manera

	$wbasedato = $this->consultarAliasPorAplicacion( 'facturacion' );
	global $conex;

	$arr_camposMatrixAUnix = array(
		"Ingsei" => "_ux_pachos_ux_pacser_ux_infate_ux_murser",
		"Inghis" => "_ux_pachis",
		"Ingnin" => "_ux_pacnum",
		"Pactdo" => "_ux_pactid_ux_midtii",
		"Pacdoc" => "_ux_pacced_ux_midide",
		"Pacap1" => "_ux_pacap1_ux_midap1",
		"Pacap2" => "_ux_pacap2_ux_midap2",
		"Pacno1" => "_ux_pnom1_ux_midno1",
		"Pacno2" => "_ux_pnom2_ux_midno2",
		"Pacfna" => "_ux_pacnac_ux_midnac",
		"Pacpan_n" => "_ux_infnac", //root_000077 join con pacpan Aqui no va el codigo del pais, va la descripcion del pais :D
		//"Paciu_n" => "_ux_paclug", //root_000006 join Paciu Aqui no va el codigo de la ciudad, va la descripcion de la ciudad :D
		"Paciu" => "_ux_paclug",
		"Pacsex" => "_ux_pacsex_midsex",
		"Pacest" => "_ux_pacest",
		"Pacdir" => "_ux_pacdir_ux_middir", //Si hay Direccion éste,
		"Pacded" => "_ux_pacdir_ux_middir",//si no el detalle
		"Pacpah" => "_ux_msepai",
		"Pacmuh" => "_ux_pacmun_ux_midmun",
		"Pacbar" => "_ux_midbar",
		"Paczon" => "_ux_paczon_ux_midzon",
		"Pactel" => "_ux_pactel_ux_midtel",
		"Paccor" => "_ux_infmai",
		"Pacofi_n" => "_ux_infocu", //root_000008 join pacofi Aqui no va el codigo del oficio, va la descripcion del oficio :D
		"Pacofi" => "_ux_infcoc_ux_mseocu",
		"Pacemp" => "_ux_pactra",
		"Pactem" => "_ux_inftel",
		"Pactus" => "_ux_pacase_ux_midtus",
		"Pactaf" => "_ux_mseafi_ux_arsafi",
		"Ingcai" => "_ux_paccin_ux_murext",
		"Pacrem" => "_ux_infrem_ux_murrem",
		"Pacire_n" => "_ux_murent",// cliame_000024 join pacire Aqui no va el codigo de la ips que remite, va la descripcion de la ips que remite oficio :D
		"Pacire" => "_ux_pacrem",
		"Pacnoa" => "_ux_otrnoa",
		"Paccru" => "_ux_paccer_ux_mrecer_ux_accre3",
		"Pacnru" => "_ux_pacres_ux_infavi_ux_mreres_ux_accno3",
		"Pacpru" => "_ux_infpar",
		"Pacdru" => "_ux_pacdre_ux_mredir_ux_infdav",
		"Pacddr" => "_ux_pacdre_ux_infdav",
		"Pactru" => "_ux_pactre_ux_inftav",
		"Paccre" => "_ux_infmai",
		"Accres" => "_ux_accres",
		"Acctar" => "_ux_acctar",
		"Accvsm" => "_ux_accvsm",
		"Acctop" => "_ux_acctop",
		"Ingtpa" => "_ux_mreemp_ux_pacemp_ux_accemp",
		"Ingcem_n" => "_ux_pacres_ux_mreres", //Descripcion del responsable
		"Ingcem" => "_ux_mrecer_ux_paccer_ux_arsars",
		"Ingpla" => "_ux_mrepla",
		"Ingpol" => "_ux_pacpol",
		"Ingord" => "_ux_mseaut",
		"Ingdig" => "_ux_pacdin_ux_murdxi_ux_murdxe_ux_hosdxi",
		"Acccon" => "_ux_accocu",
		"Accfec" => "_ux_accfec",
		"Acchor" => "_ux_acchor",
		"Accmun" => "_ux_accmun",
		"Accdir" => "_ux_acclug_ux_urglug", //Si hay Direccion éste,
		"Accdtd" => "_ux_acclug_ux_urglug", //si no el detalle
		"Acczon" => "_ux_acczon",
		"Accase" => "_ux_accase",
		"Accmar" => "_ux_accmar",
		"Accpla" => "_ux_accpla",
		"Acctse" => "_ux_acctip",
		"Acccas_n" => "_ux_accasn",//cliame_000193 join acccas Aqui no va el codigo , va la descripcion de la aseguradora
		"Acccas" => "_ux_acccas",
		"Accpol" => "_ux_accpol",
		"Accvfi" => "_ux_accfin",
		"Accvff" => "_ux_accffi",
		"Accaut" => "_ux_accaut",
		"Acctid" => "_ux_acptid",
		"Accnid" => "_ux_acpide",
		"Accap1" => "_ux_acpap1",
		"Accap2" => "_ux_acpap2",
		"Accno1" => "_ux_acpno1",
		"Accno2" => "_ux_acpno2",
		"Accpdi" => "_ux_acpdir", //Si hay Direccion éste,
		"Accpdd" => "_ux_acpdir", //si no el detalle
		"Accpdp" => "_ux_acpdep",
		"Accpmn" => "_ux_acpmun",
		"Acctel" => "_ux_acptel",
		"Acccti" => "_ux_acptic",
		"Acccni" => "_ux_acpid2_ux_accced",
		"Accca1" => "_ux_acpac1",
		"Accca2" => "_ux_acpac2",
		"Acccn1" => "_ux_acpnc1",
		"Acccn2" => "_ux_acpnc2",
		"Acccdi" => "_ux_accdir", //Si hay Direccion éste,
		"Acccdd" => "_ux_accdir", //si no el detalle
		"Acccmn" => "_ux_accmuc",
		"Accctl" => "_ux_acctel",
		"Devfac" => "_ux_evcfec",
		"Devhac" => "_ux_evchor",
		"Devdir" => "_ux_evcdir", //Si hay Direccion éste,
		"Devded" => "_ux_evcdir", //si no el detalle
		"Devdep" => "_ux_evcdep",
		"Devmun" => "_ux_evcmun",
		"Devzon" => "_ux_evczon",
		"Devdes" => "_ux_evcdes",
		"Evncla" => "det_ux_evccec",
		"Ingmei" => "_ux_pacmed_ux_infmed"
	);

	$arr_varAdicionales = array(
		"Ingcai" => "ing_caiselOriAte",
		"Ingtpa" => "ing_tpaselTipRes",
		"Ingcem" => "ing_cemhidCodAse",
		"Ingcem_n" => "ing_cemtxtCodAse",
		"Inghis" => "ing_histxtNumHis",
		"Ingnin" => "ing_nintxtNumIng",
		"Evncod" => "hidcodEvento",
		"Paccru" => "pac_crutxtNumDocRes",
		"Accdes" => "dat_Accdes",
		"Acctop" => "dat_AcctoptxtValTop",
		"relEvento" => "",
		"Pacdoc" => "pac_doctxtNumDoc"
	);

	//La fecha actual - menos 7 dias
	$fecha_act = date('Y-m-d', strtotime(date('Y-m-d'). ' - 7 days'));
	/*	_ux_mrecer_ux_paccer_ux_arsars
		_ux_mrecer_ux_paccer_ux_arsars_ux_accre3	*/

	/***se consulta si la persona ha venido antes en la tabla 100***/

	$sql ="SELECT Pachis,Pactdo,Pacdoc,Pactat,Pacap1,Pacap2,Pacno1,Pacno2,Pacfna,Pacsex,Pacest,Pacdir,Pactel,
				  Paciu,Pacbar,Pacdep,Paczon,Pactus,Pacofi,Paccea,Pacnoa,Pactea,Pacdia,Pacpaa,Pacact,Paccru,Pacnru,
				  Pactru,Pacdru,Pacpru,Paccor,Pactam,Pacpan,Pacpet,Pacded,Pactrh,Pacpah,Pacdeh,Pacmuh,Pacmov,Pacned,
				  Pacemp,Pactem,Paceem,Pactaf,Pacrem,Pacire,Paccac,Pactda,Pacddr,Pacdre,Pacmre,Pacmor,Paccre,a.Fecha_data,
				  Inghis,Ingnin,Ingfei,Inghin,Ingsei,Ingtin,Ingcai,Ingtpa,Ingcem,Ingent,
				  Ingord,Ingpol,Ingnco,Ingdie,Ingtee,Ingtar,Ingusu,Inglug,Ingdig,Ingdes,Ingpla,
				  Ingcla,Ingvre, b.id as id_ingreso,Ingmei, (  UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP( CONCAT(b.fecha_data,' ',b.hora_data) )) diferencia
			 FROM ".$wbasedato."_000100 a, ".$wbasedato."_000101 b
			WHERE Ingfei > '".$fecha_act."'
			  AND Pachis = Inghis
			  AND Ingunx = 'off'
		   HAVING( diferencia > 300)
		 ORDER BY Pacdoc ";


	$res = mysql_query( $sql, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla ".$wbasedato."000100 ".mysql_errno()." - Error en el query $sql - ".mysql_error() ) );
	if ($res)
	{
		$num=mysql_num_rows($res);

		if ($num>0)
		{
			while( $rows=mysql_fetch_array($res) )
			{
				//Se limpian las claves del arreglo POST
				foreach( $arr_camposMatrixAUnix as $keyx => $valx ){
						unset($_POST[$valx]);
				}
				foreach( $arr_varAdicionales as $keyy => $valy ){
						unset($_POST[$valy]);
				}

				foreach( $rows as $key => $value )
				{
					if( array_key_exists($key, $arr_camposMatrixAUnix) ){
						$_POST[ $arr_camposMatrixAUnix[$key] ] = utf8_encode( $value );
					}
					if( array_key_exists($key, $arr_varAdicionales) ){
						$_POST[ $arr_varAdicionales[$key] ] = utf8_encode( $value );
					}
				}

				//se consulta el nombre del pais nacimiento
				if (!empty( $rows['Pacpan'] ) && array_key_exists("Pacpan_n", $arr_camposMatrixAUnix) )
				{
					$res1=consultaNombrePais($rows['Pacpan']);
					if ($res1)
					{
						$num1=mysql_num_rows($res1);
						if ($num1>0)
						{
							$rows1=mysql_fetch_array($res1);
							$_POST[ $arr_camposMatrixAUnix["Pacpan_n"] ] = utf8_encode($rows1['Painom']);
						}
					}
				}

				//se consulta el nombre del municipio nacimiento
				/*if (!empty( $rows['Paciu'] )  && array_key_exists("Paciu_n", $arr_camposMatrixAUnix))
				{
					$res3=consultaNombreMunicipio($rows['Paciu']);
					if ($res3)
					{
						$num3=mysql_num_rows($res3);
						if ($num3>0)
						{
							$rows3=mysql_fetch_array($res3);
							$_POST[ $arr_camposMatrixAUnix["Paciu_n"] ] = utf8_encode($rows3['Nombre']);
						}
					}
				}*/

				//se consulta el nombre de la ocupacion
				if (!empty( $rows['Pacofi'] ) && array_key_exists("Pacofi_n", $arr_camposMatrixAUnix))
				{
					$res4=consultaNombreOcupacion($rows['Pacofi']);
					if ($res4)
					{
						$num4=mysql_num_rows($res4);
						if ($num4>0)
						{
							$rows4=mysql_fetch_array($res4);
							$_POST[ $arr_camposMatrixAUnix["Pacofi_n"] ] = utf8_encode($rows4['Nombre']);
						}
					}
				}

				//se consulta el nombre de la aseguradora
				if (!empty( $rows['Ingcem'] ) && array_key_exists("Ingcem_n", $arr_camposMatrixAUnix))
				{
					$res4=consultaNombreAseguradora($rows['Ingcem']);
					if ($res4)
					{
						$num4=mysql_num_rows($res4);
						if ($num4>0)
						{
							$rowCat4=mysql_fetch_array($res4);
							$_POST[ $arr_camposMatrixAUnix["Ingcem_n"] ] = utf8_encode($rowCat4['Empnom']);
							if(array_key_exists("Ingcem_n", $arr_varAdicionales)){
								$_POST[ $arr_varAdicionales["Ingcem_n"] ] = utf8_encode($rowCat4['Empnom']);
							}
						}
					}
				}

				if( $rows['Ingcai' ] == '02' ){
					//Si es SOAT, estas dos variables se deben enviar
					//Y son las mismas que ingcem_n e ingcem,
					//puesto que en la tabla 000101 si es SOAT se guarda dicho responsable
					$_POST['restxtCodRes'] =  $_POST[ $arr_varAdicionales["Ingcem_n"] ]; //DESCRIPCION RESPONSABLE SOAT
					$_POST['dat_AccreshidCodRes24'] =  $_POST[ $arr_varAdicionales["Ingcem"] ]; //CODIGO RESPONSABLE SOAT

					//consultarAccidentesAlmacenados( $rows['Pachis'], $rows1['Ingnin'], &$data );
					$sqlacc = " SELECT Acctop, Acchis, Accing, Acccon, Accdir, Accdtd, Accfec, Acchor, Accdep, Accmun, Acczon, Accdes, Accase, Accmar, Accpla, Acctse, Acccas,
									   Accpol, Accvfi, Accvff, Accaut, Acccep, Accap1, Accap2, Accno1, Accno2, Accnid, Acctid, Accpdi, Accpdd, Accpdp, Accpmn, Acctel,
									   Accca1, Accca2, Acccn1, Acccn2, Acccni, Acccti, Acccdi, Acccdd, Acccdp, Acccmn, Accctl
								  FROM {$wbasedato}_000148
								 WHERE Acchis = '".$rows['Pachis']."'
								   AND Accing = '".$rows['Ingnin']."' ";

					$resacc = mysql_query( $sqlacc, $conex ) or ( $data[ 'error' ] = utf8_encode( "Error consultando la tabla ".$wbasedato."000100 ".mysql_errno()." - Error en el query $sql - ".mysql_error() ) );

					if ($resacc)
					{
						$numacc=mysql_num_rows($resacc);
						if ($numacc>0)
						{
							if( $rowAcc=mysql_fetch_array($resacc, MYSQL_ASSOC ) )
							{
								foreach( $rowAcc as $keyacc => $valueacc )
								{
									if( array_key_exists($keyacc, $arr_camposMatrixAUnix) ){
										$_POST[ $arr_camposMatrixAUnix[$keyacc] ] = utf8_encode( $valueacc );
									}
									if( array_key_exists($keyacc, $arr_varAdicionales) ){
										$_POST[ $arr_varAdicionales[$keyacc] ] = utf8_encode( $valueacc );
									}
								}

								$resas = consultaNombreAseguradoraVehiculo( $rowAcc[ 'Acccas' ] );
								if( $rowAs = mysql_fetch_array( $resas ) ){
									$_POST[ $arr_camposMatrixAUnix["Acccas_n"] ] = $rowAs['Asedes'];
								}
								if( ($rowAcc["Accdir"]) != "" ){
									$_POST["_ux_acclug_ux_urglug"] = utf8_encode( $rowAcc["Accdir"] );
								}
								if( ($rowAcc["Acccdi"]) != "" ){
									$_POST["_ux_accdir"] = utf8_encode( $rowAcc["Acccdi"] );
								}
								if( ($rowAcc["Accpdi"]) != "" ){
									$_POST["_ux_acpdir"] = utf8_encode( $rowAcc["Accpdi"] );
								}

								//2014-09-19
								$_POST["_ux_mreemp_ux_pacemp_ux_accemp"] = "E";
								$_POST["_ux_pacres_ux_mreres"] = $_POST['dat_AccreshidCodRes24'];
								$_POST["_ux_mrepla"] = "00";
								$_POST["_ux_pacpol"] = $_POST["_ux_accpol"];
							}
						}
					}
				}

				if(  $rows['Ingcai' ] == '06'  ){
					$_POST['relEvento'] = "off";
					//consultarEventosCatastroficos( $rows['Pachis'], $rows1['Ingnin'], &$data );
					$sqlcat = " SELECT Devcod, Deveve, Devdir, Devded, Devfac, Devhac, Devdep, Devmun, Devzon, Devdes, Evncla
							   FROM {$wbasedato}_000149 a, {$wbasedato}_000150 b, {$wbasedato}_000154 c
							  WHERE Evnhis = '".$rows['Pachis']."'
									AND Evning = '".$rows['Ingnin']."'
									AND b.Evncod = Devcod
									AND b.Evnest = 'on'
									AND c.Evncod = Deveve
									AND a.Devest = 'on'";

					$rescat = mysql_query( $sqlcat, $conex ) or ( $data[ 'error' ] = utf8_encode( "Error consultando la tabla ".$wbasedato."000100 ".mysql_errno()." - Error en el query $sql - ".mysql_error() ) );

					if ($rescat)
					{
						$numcat=mysql_num_rows($rescat);
						if ($numcat>0)
						{
							if( $rowCat=mysql_fetch_array($rescat, MYSQL_ASSOC ) )
							{
								foreach( $rowCat as $keycat => $valuecat )
								{
									if( array_key_exists($keycat, $arr_camposMatrixAUnix) ){
										$_POST[ $arr_camposMatrixAUnix[$keycat] ] = utf8_encode( $valuecat );
									}
									if( array_key_exists($keycat, $arr_varAdicionales) ){
										$_POST[ $arr_varAdicionales[$keycat] ] = utf8_encode( $valuecat );
									}
								}
								if( ($rowCat["Devdir"]) != "" ){
									$_POST["_ux_evcdir"] = utf8_encode( $rowCat["Devdir"] );
								}
							}
						}
					}
				}

				//Si hay texto en el campo direccion, se envia esta y no el detalle
				if( ($rows["Pacdir"]) != "" ){
					$_POST["_ux_pacdir_ux_middir"] = utf8_encode( $rows["Pacdir"] );
				}

				$responsables1 = array();
				//COMO ES UN ACCIDENTE DE TRANSITO, se tiene que mandar segundo y tercer responsable
				//Se consultan los responsables del paciente
				$sqlpro = "SELECT Resnit as codigo, Resnom as nombre, Resord as orden,Restpa as tpa
								 FROM ".$wbasedato."_000205
								WHERE Reshis = '".$rows['Pachis']."'
								  AND Resing = '".$rows['Ingnin']."'
								  AND Resest = 'on'
								  ORDER BY Resord*1";
								  //Resord > 1 porque el 1 es soat y ese ya se tiene

				$respro = mysql_query( $sqlpro, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla ".$wbasedato."000205 ".mysql_errno()." - Error en el query $sqlpro - ".mysql_error() ) );
				if ($respro)
				{
					$numpro=mysql_num_rows($respro);
					if ($numpro>0)
					{
						//$incx = 0;
						while( $rowspro = mysql_fetch_assoc($respro) ){
							$roww['ing_cemhidCodAse'] = $rowspro['codigo'];
							$roww['ing_tpaselTipRes'] = $rowspro['tpa'];
							array_push($responsables1, $roww );

							if( $rowspro['orden'] == 2 ){
								$_POST['codAseR2'] = $rowspro['codigo'];
							}else if( $rowspro['orden'] == 3 ){
								$_POST['codAseR3'] = $rowspro['codigo'];
								$_POST['nomAseR3'] = "";
							}

							if( $rowspro['orden'] == 1 ){
								$_POST['res_nom'] = $rowspro['nombre'];
							}
							/*if( $rowspro['orden'] == 2 ){
								//SEGUNDO RESPONSABLE
								$_POST['dat_Accre2hidCodRes2'] = $rowspro['codigo'];
								$_POST['re2hidtopRes2'] = 0.0;
							}else if( $rowspro['orden'] == 3 ){
								//TERCER RESPONSABLE
								$_POST['ing_cemhidCodAse'] = $rowspro['codigo'];
							}
							$incx++;*/
						}
					}
				}
				$_POST['responsables1'] = $responsables1;


				//adicionales
				//La siguiente variable es par de ingsei, en el html se envia codigo-codigocco, pero en la bd
				//no se guarda el primer codigo, en unix no se usa, por eso se envia 00, pero en egreso_erp.php hace un explode list por lo que es necesario mandarlo
				$_POST['_ux_pachos_ux_pacser_ux_infate_ux_murser'] = "00-".$_POST['_ux_pachos_ux_pacser_ux_infate_ux_murser'];


				//La direccion y el telefono del responsable son los del primer responsable.
				$respo = $responsables1[0]['ing_cemhidCodAse'];
				$_POST['ing_tpaselTipRes'] = $responsables1[0]['ing_tpaselTipRes'];
				$_POST['ing_cemhidCodAse'] = $responsables1[0]['ing_cemhidCodAse'];

				$sqlxy = "SELECT Empdir,Emptel
						   FROM ".$wbasedato."_000024
						  WHERE Empcod = '".$respo."'";

				$res2xy = mysql_query( $sqlxy, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando el ingreso " .mysql_errno()." - Error en el query $sql2 - ".mysql_error() ) );
				if( $res2xy )
				{
					$num2=mysql_num_rows($res2xy);
					if ($num2>0)
					{
						$rows2xy=mysql_fetch_array($res2xy);
						$_POST['_ux_pacdre_ux_mredir_ux_infdav'] = $rows2xy['Empdir'];
						$_POST['_ux_pactre_ux_inftav'] = $rows2xy['Emptel'];
					}
				}

				$esdelkron=true;
				$a = new admisiones_erp( '', $rows['Pachis'], $rows['Ingnin'], $esdelkron);
				$data = $a->data;
				if( $data['error'] != 1 ){
					$query = "UPDATE ".$wbasedato."_000101 SET Ingunx = 'on' WHERE id = ".$rows['id_ingreso']." LIMIT 1";
					$rescat = mysql_query( $query, $conex );
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$sql = "INSERT INTO ".$wbasedato."_000164 (     medico     ,      fecha_data         ,       hora_data        ,        Logusu         ,         Logdes        ,            Loghis          ,           Loging          ,            Logdoc           , Logest, seguridad )
													   VALUES ('".$wbasedato."','".utf8_decode($fecha)."','".utf8_decode($hora)."','kronmatrixunix','Kron Matrix a Unix','".utf8_decode($rows['Pachis'])."','".utf8_decode($rows['Ingnin'])."','".utf8_decode($rows['Pacdoc'])."',  'on' , 'C-root'  )";
					$res2 = mysql_query( $sql, $conex );

				}else{
					//Guardar registro en el log de admisiones
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$sql = "INSERT INTO ".$wbasedato."_000164 (     medico     ,      fecha_data         ,       hora_data        ,        Logusu         ,         Logdes        ,            Loghis          ,           Loging          ,            Logdoc           , Logest, seguridad )
													   VALUES ('".$wbasedato."','".utf8_decode($fecha)."','".utf8_decode($hora)."','kronmatrixunix','fallo el guardado en unix tabla bloqueada','".utf8_decode($rows['Pachis'])."','".utf8_decode($rows['Ingnin'])."','".utf8_decode($rows['Pacdoc'])."',  'on' , 'C-root'  )";
					$res2 = mysql_query( $sql, $conex );
				}
				unset( $a );


			}
		}
	}
}

function kron_egresoMatrix_Unix(){

	global $conex;
	global $wemp_pmla;

	$this->conexionOdbc( 'admisiones' );
	$wbasedato = $this->consultarAliasPorAplicacion( 'facturacion' );

	$fecha = date( "Y-m-d" );
	$hora = date( "H:i:s" );

	//Los siguientes arreglos tienen el nombre del campo en MATRIX y como se debe mandar a UNIX
	//dado que el programa erp_unix_egreso.php los espera de esa manera
	$arr_camposMatrixAUnix = array(
		"Egrhis" => "_ux_egrhis",
		"Egring" => "_ux_egrnum",
		"Egrmei" => "_ux_egrmei",
		"Egrdxi" => "_ux_egrdin_ux_hosdxi",
		"Egrmee" => "_ux_egrmed",
		"Egrest" => "_ux_egrdfa_ux_hosdes",
		"Egrcae" => "_ux_egrcau",
		"Egrfee" => "_ux_pacnac_ux_midnac",
		"Egrfta" => "_ux_pacnac_ux_midnac",
		"Egrtdp" => "_ux_pacest",
		"Egrcom" => "_ux_pacsex_ux_midsex",
		"Egrcex" => "_ux_hoscex",
		"Pactdo" => "_ux_pactid_ux_midtii",
		"Pacdoc" => "_ux_pacced_ux_midide",
		"Pacap1" => "_ux_pacap1_ux_midap1",
		"Pacap2" => "_ux_pacap2_ux_midap2",
		"Pacno1" => "_ux_pnom1_ux_midno1",
		"Pacno2" => "_ux_pnom2_ux_midno2",
		"Diacod" => "_ux_diadia_ux_plug",
		"Diatip" => "_ux_diatip",
		"Diainf" => "_ux_diainf",
		"Diacom" => "_ux_diacom",
		"Diaegr" => "_ux_dxegr",
		"Procod" => "_ux_propro",
		"Protip" => "_ux_protip",
		"Espcod" => "_ux_espesp",
		"Esptip" => "_ux_esptip",
		"Egrmet" => "_ux_infmed",
		"Egrobg" => "_ux_mepides"
	);

	$alias="movhos";
	$aplicacion=consultarAplicacion($conex,$wemp_pmla,$alias);

	$alias1="hce";
	$aplicacionHce=consultarAplicacion($conex,$wemp_pmla,$alias1);


	$fecha_act = date('Y-m-d', strtotime(date('Y-m-d'). ' - 7 days'));
	//Se consultan los egresos de los ultimos 7 dias
	$sql = "SELECT Pacdoc, Pactdo, Pacap1, Pacap2, Pacno1, Pacno2,Pachis, Pacact,
				   Egrhis,Egring,Egrmei,Egrdxi,Egrfee,Egrest,Egrcae,Egrmee,Egrcex,Egrtdp,Egrcom,Egrfta,A.id as id_egreso,Egrmet
			  FROM ".$wbasedato."_000108 A, ".$wbasedato."_000100
			 WHERE Egrfee > '".$fecha_act."'
			   AND Egrhis = Pachis
			   AND Egract = 'on'
			   AND Egrunx != 'on'";

	$res = mysql_query( $sql, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla ".$wbasedato."_000108 con la ".$wbasedato."_000100  ".mysql_errno()." - Error en el query $sql - ".mysql_error() ) );
	if ($res)
	{
		$num=mysql_num_rows($res);
		$data['numRegistrosPac']=$num;
		if ($num>0)
		{
			while( $rows1=mysql_fetch_array($res) )
			{
				//Se limpian las claves del arreglo POST
				foreach( $arr_camposMatrixAUnix as $keyx => $valx ){
						unset($_POST[$valx]);
				}
				foreach( $rows1 as $key => $value )
				{
					if( array_key_exists($key, $arr_camposMatrixAUnix) ){
						$_POST[ $arr_camposMatrixAUnix[$key] ] = utf8_encode( $value );
					}
				}

				$_POST['diagnosticosux'] = array();
				$_POST['procedimientosux'] = array();
				$_POST['especialidadesux'] = array();

				/**Busqueda de diagnosticos**/
				if (!empty( $rows1['Egrhis'] ) && !empty( $rows1['Egring'] ))
				{
					$sqldia = "SELECT Diacod,Diatip,Diainf,Diacom,Diaegr
								 FROM ".$wbasedato."_000109
								WHERE Diahis = '".$rows1['Egrhis']."'
								  AND Diaing = '".$rows1['Egring']."'";

					$resdia = mysql_query( $sqldia, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla ".$wbasedato."0000109 ".mysql_errno()." - Error en el query $sql1 - ".mysql_error() ) );
					if ($resdia)
					{
						$numdia=mysql_num_rows($resdia);
						if ($numdia>0)
						{
							while( $rowsdia=mysql_fetch_assoc($resdia) )
							{
								$arr_aux = array();
								foreach( $rowsdia as $keyd => $valued )
								{
									if( array_key_exists($keyd, $arr_camposMatrixAUnix) ){
										$arr_aux[ $arr_camposMatrixAUnix[$keyd] ] = utf8_encode( $valued );
									}
								}
								array_push( $_POST['diagnosticosux'], $arr_aux );
							}
						}
					}
				}
				/**Fin busqueda diagnosticos**/

				/**Busqueda de procedimientos**/
				if (!empty( $rows1['Egrhis'] ) && !empty( $rows1['Egring'] ))
				{
					$sqlpro = "SELECT Procod, Protip
								 FROM ".$wbasedato."_000110
								WHERE Prohis = '".$rows1['Egrhis']."'
								  AND Proing = '".$rows1['Egring']."'";

					$respro = mysql_query( $sqlpro, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla ".$wbasedato."000110 ".mysql_errno()." - Error en el query $sql1 - ".mysql_error() ) );
					if ($respro)
					{
						$numpro=mysql_num_rows($respro);
						if ($numpro>0)
						{
							while( $rowspro=mysql_fetch_assoc($respro) )
							{
								$arr_aux = array();
								foreach( $rowspro as $keyp => $valuep )
								{
									if( array_key_exists($keyp, $arr_camposMatrixAUnix) ){
										$arr_aux[ $arr_camposMatrixAUnix[$keyp] ] = utf8_encode( $valuep );
									}
								}
								array_push( $_POST['procedimientosux'], $arr_aux );
							}
						}
					}
				}
				/**Fin busqueda procedimientos**/

				/**Busqueda de especialidades**/
				if (!empty( $rows1['Egrhis'] ) && !empty( $rows1['Egring'] ) && $data['error'] == 0)
				{
					$sqlesp = "SELECT Espcod,Esptip
								 FROM ".$wbasedato."_000111
								WHERE Esphis = '".$rows1['Egrhis']."'
								  AND Esping = '".$rows1['Egring']."'";

					$resesp = mysql_query( $sqlesp, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla ".$wbasedato."000111 ".mysql_errno()." - Error en el query $sql1 - ".mysql_error() ) );
					if ($resesp)
					{
						$numesp=mysql_num_rows($resesp);
						if ($numesp>0)
						{
							while( $rowsesp=mysql_fetch_assoc($resesp) )
							{
								$arr_aux = array();
								foreach( $rowsesp as $keye => $valuee )
								{
									if( array_key_exists($keye, $arr_camposMatrixAUnix) ){
										$arr_aux[ $arr_camposMatrixAUnix[$keye] ] = utf8_encode( $valuee );
									}
								}
								array_push( $_POST['especialidadesux'], $arr_aux );
							}
						}
					}
				}
				/**Fin busqueda especialidades**/

				/*foreach ($_POST as $key => $value) {
					if( $key == 'diagnosticosux' || $key == 'procedimientosux' || $key == 'especialidadesux' ){
						echo "<br>*****".$key;
						foreach ($value as $key1 => $value1) {
							echo "<br>---->";
							echo $key1;
							echo "  ===> ";
							echo json_encode($value1);
						}
						continue;
					}

					echo "<br>";
					echo $key;
					echo "  ===> ";
					echo $value;
				}*/
				//echo "<BR><BR><BR>";



				$a = new egreso_erp();

				if( $a->conex_u ){
					$a->realizarEgreso( $rows1['Egrhis'], $rows1['Egring'] );
					if( $a->data[ 'error' ] == 0 ) //si hay errores guardando en unix
					{
						$query = "UPDATE ".$wbasedato."_000108 SET Egrunx = 'on' WHERE id = ".$rows1['id_egreso']." LIMIT 1";
						$rescat = mysql_query( $query, $conex );
					}
				}
				unset( $a );
			}//while row
		}//$num1>0
	}//if $res

	//2014-12-29
	liberarConexionOdbc( $this->conex_u );
	odbc_close_all();
}


function kron_egresosUnix_Matrix( $comprobarExistencia = false ){
	global $conex;
	global $wemp_pmla;
	$wbasedato = $this->consultarAliasPorAplicacion( 'facturacion' );
	$wbasedato_mov = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$rowIdEgresos = consultarAliasPorAplicacion($conex, $wemp_pmla, "rowIdEgresosUnix");

	$this->conexionOdbc( 'admisiones' );

	$fecha = date( "Y-m-d" );
	$hora = date( "H:i:s" );

	if( $this->conex_u ){

		$maximoRowIdEgresos = $rowIdEgresos + 30000;


		$sql = "SELECT egrhis, egrnum, egrmei, egrmed, egrdin, egrdfa, egrcau, date('0001-01-01') as egregr , egrhoe, egrdia,rowid
				  FROM inmegr
				 WHERE rowid>=".$rowIdEgresos." and rowid<".$maximoRowIdEgresos."
				   AND egregr IS NULL
				UNION
				SELECT egrhis, egrnum, egrmei, egrmed, egrdin, egrdfa, egrcau, egregr, egrhoe, egrdia,rowid
				  FROM inmegr
				 WHERE rowid>=".$rowIdEgresos." and rowid<".$maximoRowIdEgresos."
				   AND egregr IS NOT NULL ORDER BY 11";

		$err_o = odbc_do( $this->conex_u, $sql );

		if( $err_o ){
			$rowidFinal = $rowIdEgresos-1;
			while( odbc_fetch_row($err_o) )
			{
				$rowidFinal++;
				if( trim( odbc_result($err_o,1) ) == '' )
					continue;

				$his = trim( odbc_result($err_o,1) ); //egrhis
				$ing = trim( odbc_result($err_o,2) ); //egrnum


				if( $comprobarExistencia == true ){
					//Consulto si existe el registo
					 $sql = "SELECT Egrhis,Egring,id,Egract
							   FROM ".$wbasedato."_000108
							  WHERE Egrhis = '".$his."'
							    AND Egring = '".$ing."'
							    AND Egract = 'on'";

					$res = mysql_query( $sql, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error consultando la tabla de egresos - ".mysql_error() ) );

					if( $res ){
						$num = mysql_num_rows( $res );
						if( $num > 0 ){
							continue; //El egreso ya existe
						}
					}
				}

				$Egrdia = ""; //Para identificar el diagnostico principal

				$camposEgreso = array(
					"Egrhis" => $his,
					"Egring" => $ing,
					"Egrmei" => "",
					"Egrdxi" => "",
					"Egrmee" => "",
					"Egrest" => "",
					"Egrcae" => "",
					"Egrfee" => "",
					"Egrfta" => "",
					"Egrcex" => "",
					"Egrhoe" => "",
					"Egrtdp" => "",//se desconoce en unix
					"Egrcom" => "",//se desconoce en unix
					"Egrfia" => "",//se desconoce en unix
					"Egruex" => ""//se desconoce en unix
				 );

				$camposEgreso['Egrdxi'] = trim( odbc_result($err_o,5) ); //egrdin
				$camposEgreso['Egrest'] = trim( odbc_result($err_o,6) ); //egrdfa
				$camposEgreso['Egrcae'] = trim( odbc_result($err_o,7) ); //egrcau
				$camposEgreso['Egrfee'] = trim( odbc_result($err_o,8) ); //egregr
				$camposEgreso['Egrfta'] = trim( odbc_result($err_o,8) ); //egregr
				$camposEgreso['Egrhoe'] = trim( odbc_result($err_o,9) ); //egrhoe
				$Egrdia = trim( odbc_result($err_o,10) ); //egrdia


				/*PARA CONSULTAR LA CAUSA EXTERNA*/
				$sqlmsate="SELECT atedoc,ateips
							 FROM msate
							WHERE atehis = '".$his."'
							  AND ateing = '".$ing."'";

				$resmsate = odbc_do( $this->conex_u, $sqlmsate );
				$rowsAte = odbc_fetch_row($resmsate);

				if( $rowsAte ){
					$doc = odbc_result( $resmsate, 1 );
					$ips = odbc_result( $resmsate, 2 );

					$sqlHos =  "SELECT hoscex
								  FROM mshos
								 WHERE hosips = '".$ips."'
								   AND hosdoc = ".$doc."";
					$resHos = odbc_do( $this->conex_u, $sqlHos );
					$rowHos = odbc_fetch_row($resHos);
					if( $rowHos ){
						$camposEgreso['Egrcex'] = odbc_result( $resHos, 1 );
					}
				}
				/*FIN PARA CONSULTAR LA CAUSA EXTERNA*/


				/*PARA CONSULTAR EL CODIGO DE MATRIX DE LOS MEDICOS*/
				$egrmei = trim( odbc_result($err_o,3) ); //egrmei
				$egrmed = trim( odbc_result($err_o,4) ); //egrmed

				$sqlmei="SELECT medced
							  FROM inmed
							 WHERE medcod = '".$egrmei."'";
				$resMei = odbc_do( $this->conex_u, $sqlmei );
				$rowMei = odbc_fetch_row( $resMei );
				if( $rowMei ){
					$camposEgreso['Egrmei'] = odbc_result( $resMei, 1 );
				}

				$sqlmee="SELECT medced
							  FROM inmed
							 WHERE medcod = '".$egrmed."'";
				$resMee = odbc_do( $this->conex_u, $sqlmee );
				$rowMee = odbc_fetch_row( $resMee );
				if( $rowMee ){
					$camposEgreso['Egrmee'] = odbc_result( $resMee, 1 );
				}
				/*FIN PARA CONSULTAR EL CODIGO DE MATRIX DE LOS MEDICOS*/

				$inserto = true;
				$query = "INSERT ".$wbasedato."_000108 (medico          ,fecha_data  ,hora_data  , 			Egrhis				,		Egring				 ,			Egrmei				,		Egrdxi				   ,			Egrfee			  ,			Egrhoe			     ,				Egrest			,			Egrcae			   ,			Egrmee			  ,				Egrcex			 ,				Egrtdp			,				Egrcom		   ,				Egrfia		  ,				Egrfta			 ,				Egruex			,Egract, Egrunx, 	Seguridad )
												VALUES ('".$wbasedato."','".$fecha."','".$hora."','".$camposEgreso['Egrhis']."','".$camposEgreso['Egring']."', '".$camposEgreso['Egrmei']."', '".$camposEgreso['Egrdxi']."', '".$camposEgreso['Egrfee']."', '".$camposEgreso['Egrhoe']."', '".$camposEgreso['Egrest']."', '".$camposEgreso['Egrcae']."', '".$camposEgreso['Egrmee']."', '".$camposEgreso['Egrcex']."', '".$camposEgreso['Egrtdp']."', '".$camposEgreso['Egrcom']."', '".$camposEgreso['Egrfia']."', '".$camposEgreso['Egrfta']."', '".$camposEgreso['Egruex']."',  'on', 'on'  ,  'C-".$wbasedato."')";
				$err1 = mysql_query($query,$conex) or ($inserto = false);

				//Comprobar si inserto
				if( $inserto == false ){
					echo "<br>HISTORIA ".$his." INGRESO ".$ing." NO FUE GUARDADO EN MATRIX";
				}else{

					/*PARA LOS DIAGNOSTICOS*/
					$sqlDiag = "SELECT mdiadia, mdiatip, mdiainf, mdiacom
								  FROM inmdia
								 WHERE mdiahis='".$his."'
								   AND mdianum='".$ing."'";

					$resDiag = odbc_do( $this->conex_u, $sqlDiag );

					if( $resDiag ){
						while( odbc_fetch_row($resDiag) )
						{
							if( trim( odbc_result($resDiag,1) ) == '' )
								continue;

							$camposDiag = array(
								"Diahis" => $his,
								"Diaing" => $ing,
								"Diacod" => "",
								"Diatip" => "",
								"Diainf" => "",
								"Diacom" => "",
								"Dianue" => "",	//Se desconoce en unix
								"Diaegr" => "off"
							);


							$camposDiag['Diacod'] = trim( odbc_result($resDiag,1) ); //mdiadia
							$camposDiag['Diatip'] = trim( odbc_result($resDiag,2) ); //mdiatip
							$camposDiag['Diainf'] = trim( odbc_result($resDiag,3) ); //mdiainf
							$camposDiag['Diacom'] = trim( odbc_result($resDiag,4) ); //mdiacom
							if( $camposDiag['Diacod'] == $Egrdia )
								$camposDiag['Diaegr'] = 'on'; //mdiadia

							$query = "INSERT ".$wbasedato."_000109 (medico  ,fecha_data  ,hora_data  , 			Diahis			 ,		Diaing				 ,			Diacod			  ,		Diatip				   ,			Diainf			,			Diacom			 ,				Dianue		  ,			Diaegr			   ,	Seguridad )
													VALUES ('".$wbasedato."','".$fecha."','".$hora."','".$camposDiag['Diahis']."','".$camposDiag['Diaing']."', '".$camposDiag['Diacod']."', '".$camposDiag['Diatip']."', '".$camposDiag['Diainf']."', '".$camposDiag['Diacom']."', '".$camposDiag['Dianue']."', '".$camposDiag['Diaegr']."',   'C-".$wbasedato."')";
							$err1 = mysql_query($query,$conex);
						}
					}
					/*FIN PARA LOS DIAGNOSTICOS*/


					/*PARA LOS PROCEDIMIENTOS*/
					$sqlProc = "SELECT mpropro, mprotip
								  FROM inmpro
								 WHERE mprohis='".$his."'
								   AND mpronum='".$ing."'";

					$resProc = odbc_do( $this->conex_u, $sqlProc );

					if( $resProc ){
						while( odbc_fetch_row($resProc) )
						{
							if( trim( odbc_result($resProc,1) ) == '' )
								continue;

							$camposProc = array(
								"Prohis" => $his,
								"Proing" => $ing,
								"Procod" => "",
								"Protip" => ""
							);

							$camposProc['Procod'] = trim( odbc_result($resProc,1) ); //mpropro
							$camposProc['Protip'] = trim( odbc_result($resProc,2) ); //mprotip


							$query = "INSERT ".$wbasedato."_000110 (medico  ,fecha_data  ,hora_data  , 			Prohis			 ,		Proing				 ,			Procod			  ,		Protip				   ,	Seguridad )
													VALUES ('".$wbasedato."','".$fecha."','".$hora."','".$camposProc['Prohis']."','".$camposProc['Proing']."', '".$camposProc['Procod']."', '".$camposProc['Protip']."',  'C-".$wbasedato."')";
							$err1 = mysql_query($query,$conex);
						}
					}
					/*FIN PARA LOS PROCEDIMIENTOS*/

					/*PARA LAS ESPECIALIDADES*/
					$sqlEsp = "SELECT mespesp, mesptip
								 FROM inmesp
								WHERE mesphis='".$his."'
								  AND mespnum='".$ing."'";

					$resEsp = odbc_do( $this->conex_u, $sqlEsp );

					if( $resEsp ){
						while( odbc_fetch_row($resEsp) )
						{
							if( trim( odbc_result($resEsp,1) ) == '' )
								continue;

							$camposEsp = array(
								"Esphis" => $his,
								"Esping" => $ing,
								"Espcod" => "",
								"Esptip" => ""
							);

							$camposEsp['Espcod'] = trim( odbc_result($resEsp,1) ); //mespesp
							$camposEsp['Esptip'] = trim( odbc_result($resEsp,2) ); //mesptip

							//Se busca el codigo de MATRIX de la especialidad
							$queryEsp =	"SELECT Espcod
								   FROM ".$wbasedato_mov."_000044
								  WHERE Espunx= '".$camposEsp['Espcod']."'";

							$resEspq = mysql_query( $queryEsp, $this->conex );
							if( $resEspq ){
								$numEsp=mysql_num_rows($resEspq);
								if( $numEsp > 0 ){
									$rowEsp = mysql_fetch_array( $resEspq );
									$camposEsp['Espcod'] = $rowEsp[0];
								}
							}
							$query = "INSERT ".$wbasedato."_000111 (medico  ,fecha_data  ,hora_data  , 			Esphis			 ,		Esping				 ,			Espcod			  ,		Esptip				   ,	Seguridad )
													VALUES ('".$wbasedato."','".$fecha."','".$hora."','".$camposEsp['Esphis']."','".$camposEsp['Esping']."', '".$camposEsp['Espcod']."', '".$camposEsp['Esptip']."',  'C-".$wbasedato."')";
							$err1 = mysql_query($query,$conex);
						}
					}
					/*FIN PARA LOS ESPECIALIDADES*/

					//$rowidFinal++;
				}//fin while

				$act1=$this->actualizarAplicacion( $conex, $wemp_pmla, 'rowIdEgresosUnix', $rowidFinal );
				if ($act1 == true ){
					echo "<br>Parametro en la tabla root_000051 actualizado con exito: ".$rowidFinal."";
				}else{
					echo "<br>No se actualizo la tabla root_000051";
				}
			}
		}
	}
	else{
		echo "NO SE CONECTO A UNIX";
	}
	//2014-12-29
	liberarConexionOdbc( $this->conex_u );
	odbc_close_all();
}
/*Esta funcion actualiza el parametro en la tabla root_000051 con el ultimo registro leido
  de inpaci*/

function kron_actualizarIngresos(){
	global $conex;
	global $wemp_pmla;
	$wbasedato = $this->consultarAliasPorAplicacion( 'facturacion' );
	$wbasedato_mov = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");

	$this->conexionOdbc( 'admisiones' );

	$fecha = date( "Y-m-d" );
	$hora = date( "H:i:s" );

	$id = 1363213;

	if( $this->conex_u ){
		$query = "SELECT Egrhis, Egring, Egrdxi,id
					FROM ".$wbasedato."_000108
					where id> ".$id." limit 30000
				";
		$res = mysql_query( $query, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error consultando la tabla de egresos - ".mysql_error() ) );
		if( $res ){
			$num = mysql_num_rows( $res );
			if( $num > 0 ){
				while( $row = mysql_fetch_assoc($res) ){
					//echo $row['id']."<br>";
					//Buscar si está en la 101
					$query2 = "SELECT *
								FROM ".$wbasedato."_000101
							   WHERE Inghis = '".$row['Egrhis']."'
							     AND Ingnin = '".$row['Egring']."'";
					$res2 = mysql_query( $query2, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error consultando la tabla de egresos - ".mysql_error() ) );
					if( $res2 ){
						$num2 = mysql_num_rows( $res2 );
						if( $num2 == 0 ){
							//Traer la fecha y hora de UNIX

							$sql = "SELECT egring,egrhoi,egrdin
									  FROM inmegr
									 WHERE egrhis = '".$row['Egrhis']."'
									   AND egrnum = '".$row['Egring']."'";

							$err_o = odbc_do( $this->conex_u, $sql );
							if( $err_o ){
								while( odbc_fetch_row($err_o) )
								{
									if( trim( odbc_result($err_o,1) ) == '' )
										continue;

									$fecha_ing = trim( odbc_result($err_o,1) ); //egring
									$hora_ing = trim( odbc_result($err_o,2) ); //egrhoi
									$diagnostico = trim( odbc_result($err_o,3) ); //egrdin

									$hora_ing = str_replace(".",":",$hora_ing);
									$hora_ing = $hora_ing.":00";
									$query3 = "INSERT ".$wbasedato."_000101 (medico  ,fecha_data  ,hora_data  , 			Inghis			 ,		Ingnin				 ,		Ingfei,					Inghin						,	Ingdig			  ,		Ingunx				   ,	Seguridad )
															VALUES ('".$wbasedato."','".$fecha."','".$hora."','".$row['Egrhis']."',       '".$row['Egring']."'				, '".$fecha_ing."',			'".$hora_ing."',			'".$diagnostico."'		, 'on'					,  'C-".$wbasedato."')";
									$err1 = mysql_query($query3,$conex);
									echo "Inserto: ".$row['id']."<br>";
								}
							}
						}else{
							//Ya existe
						}
					}
				}
			}
		}
	}
	//2014-12-29
	liberarConexionOdbc( $this->conex_u );
	odbc_close_all();
}

function actualizarAplicacion($conexion, $codigoInstitucion, $nombreAplicacion, $dato)
{
	$consulta = "";
	if ($dato == "")
	{
		$consulta=false;
	}
	else
	{
		$q = " UPDATE
				root_000051
				SET Detval = '".$dato."'
				WHERE
					Detemp = '".$codigoInstitucion."'
					AND Detapl = '".$nombreAplicacion."'";

		// echo $q;
		$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

		if( $res )
		{
			if(mysql_affected_rows() > 0)
			{
				$consulta=true;
			}
		}
		else
		{
			$consulta=false;
		}
	}

	return $consulta;
}

//---------------------------------------------------------------
//	--> INICIO, KRONS PERTENECIENTES AL PROYECTO DE FACTURACIÓN
//---------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	IMPOTAR MAESTRO DE CENTROS DE COSTOS *  CONCEPTOS DESDE UNIX (FACONCUE y FACONCCO) A MATRIX (cliame_000077)
	// 	Responsable : 			Felipe Alvarez.
	//	Fecha de creacion:		2013-12-02.
	//----------------------------------------------------------------------------------------------
	function cco_conceptos()
	{
		global $conex;

		$wbasedato = $this->consultarAliasPorAplicacion( 'facturacion' );
		$this->conexionOdbc( 'facturacion' );
		if( $this->conex_u )
		{
			// --> Obtener todos los conceptos con su correspondiente homologo matrix.
			$sqlConHomo = "SELECT Congen, Consim
							 FROM ".$wbasedato."_000197
							WHERE Conest = 'on' ";
			$resSqlConHomo 	= mysql_query($sqlConHomo, $conex ) or die("<b>ERROR EN QUERY MATRIX:</b><br>".mysql_error());
			$arrayConHomo 	= array();
			while($rowSqlConHomo = mysql_fetch_array($resSqlConHomo))
				$arrayConHomo[$rowSqlConHomo['Congen']] = $rowSqlConHomo['Consim'];

			// --> 	Se Consulta  la cuenta y el cco  del procedimiento, debido a que en matrix se guarda tambien la cuenta
			//  	relacionada al concepto se hace  una relacion con la tabla FACONCUE

			// --> Query original
			$sql = "SELECT	concuecon,	concuetem,	concueing,	concuedes,	concuente,	concuedte,	conccocco
					  FROM  FACONCUE, FACONCCO
					 WHERE  concconum = concuenum ";

			// --> Aplicarle validacion nulos al query
			$camposNulos 	= "concuetem, concueing, concuedes, concuente, concuedte";
			$select			= "concuecon, concuetem, concueing, concuedes, concuente, concuedte, conccocco";
			$from 			= "FACONCUE, FACONCCO";

			$sql = $this->construirQueryUnix($from, $camposNulos, $select, "concconum = concuenum");
			if(!$sql)
			{
				echo "error en funcion cco_conceptos --> construirQueryUnix";
				return;
			}
			$res = odbc_do( $this->conex_u, $sql );

			if($res)
			{
				while(odbc_fetch_row($res))
				{
					// --> Concepto homologado
					if(array_key_exists(trim(odbc_result($res,1)), $arrayConHomo))
						$conceptoHom = $arrayConHomo[trim(odbc_result($res,1))];
					else
						continue;

					// --> Consultar si ya existe el registro
					$sqlReg77 = "
					SELECT id
					  FROM ".$wbasedato."_000077
					 WHERE Relconcon = '".$conceptoHom."'
					   AND Relconcco = '".trim( odbc_result($res,7) )."'
					   AND Relcontem = '".trim(odbc_result($res,2))."'
					   AND Relconuni = '".trim(odbc_result($res,1))."'
					";
					$resReg77 = mysql_query($sqlReg77, $this->conex ) or die("<b>ERROR EN QUERY MATRIX (sqlReg77):</b><br>".mysql_error());
					if($rowReg77 = mysql_fetch_array($resReg77))
					{
						// --> Actualizar el registro
						$sqlInsert77 = "
						UPDATE ".$wbasedato."_000077
						   SET Fecha_data = '".date( "Y-m-d" )."',
						       Hora_data  = '".date( "H:i:s" )."',
							   Relconcon  = '".$conceptoHom."',
							   Relcontem  = '".trim(odbc_result($res,2))."',
							   Relconcin  = '".trim( odbc_result($res,3))."',
							   Relconcdi  = '".trim( odbc_result($res,4))."',
							   Relconcte  = '".trim( odbc_result($res,5))."',
							   Relconcdt  = '".trim( odbc_result($res,6))."',
							   Relconcco  = '".trim( odbc_result($res,7))."',
							   Relconuni  = '".trim(odbc_result($res,1))."'
						 WHERE id 		  = '".$rowReg77['id']."'
						";
						mysql_query($sqlInsert77, $this->conex ) or die("<b>ERROR EN QUERY MATRIX (sqlInsert77):</b><br>".mysql_error());
					}
					else
					{
						// -->  Insertar el registro
						$sqlInsert77 = "
						INSERT INTO ".$wbasedato."_000077( Medico, 			Fecha_data,      		Hora_data,           	Relconcon,          Relcontem,			 				Relconcin,							Relconcdi,							Relconcte,							Relconcdt,							Relconcco,							Relconest, 	Relconuni, 							Seguridad       )
												   VALUES( '$wbasedato', 	'".date( "Y-m-d" )."', 	'".date( "H:i:s" )."', 	'".$conceptoHom."',	'".trim(odbc_result($res,2))."',  '".trim( odbc_result($res,3) )."',	'".trim( odbc_result($res,4) )."',	'".trim( odbc_result($res,5) )."',  '".trim( odbc_result($res,6))."',	'".trim( odbc_result($res,7) )."',	'on',		'".trim( odbc_result($res,1) )."', 	'C-$wbasedato'	 ) ";
						mysql_query($sqlInsert77, $this->conex );// or die("<b>ERROR EN QUERY MATRIX (sqlInsert77):</b><br>".mysql_error());
					}
				}
			}
			else{
				echo "Error en el query $sql";
			}
		}
		liberarConexionOdbc( $this->conex_u );
		odbc_close_all();
	}

	//----------------------------------------------------------------------------------------------
	//	IMPOTAR MAESTRO DE NIT'S DESDE UNIX (CONIT) A MATRIX (cliame_000189)
	// 	Responsable : 			Jerson trujillo.
	//	Fecha de creacion:		2013-11-25.
	//----------------------------------------------------------------------------------------------
	function maestro_nits(){

		$wbasedato = $this->consultarAliasPorAplicacion( 'facturacion' );
		$this->conexionOdbc( 'facturacion' );

		// --> Verificar si para hoy ya se hizo la actualizacion
		$sql = "SELECT id
				  FROM ".$wbasedato."_000189
				 WHERE Fecha_data = '".date( "Y-m-d" )."'
				   AND id = 1 ";

		$res = mysql_query( $sql, $this->conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );

		if( $num == 0 ){

			if( $this->conex_u ){

				// --> Obtener los nit's de el maestro de unix, Query original
				$sql = "SELECT nitnit, nitdig, nitnom, nitraz, nittip, nitciu, nitret
							   nitcla, nitext, nitdir, nitmun, nitint, nittel
						  FROM CONIT
						 WHERE nitact = 'S'
						";

				// --> Aplicarle validacion nulos al query
				$camposNulos 	= "nitdig, 	nitraz, nittip, nitdir, nitmun, nitint, nittel";
				$valresDefecto 	= "'', 		'', 	'', 	'', 	'',		'',		''";
				$select			= "nitnit, nitdig, nitnom, nitraz, nittip, nitciu, nitret, nitcla, nitext, nitdir, nitmun, nitint, nittel";
				$from 			= "CONIT";

				$sql = $this->construirQueryUnix($from, $camposNulos, $select, "nitact = 'S'", $valresDefecto);
				if(!$sql)
				{
					echo "error en funcion maestro_nits --> construirQueryUnix";
					return;
				}
				$res = odbc_do( $this->conex_u, $sql );

				if( $res ){
					$i = 0;
					while( odbc_fetch_row($res) )
					{
						$nombre			= str_replace("'", "", trim(odbc_result($res,'nitnom')));
						$razonSocial	= str_replace("'", "", trim(odbc_result($res,'nitraz')));
						$retenedor 		= ((trim(odbc_result($res,'nitret')) == 'S') ? 'on' : 'off');
						$extranjero 	= ((trim(odbc_result($res,'nitext')) == 'S') ? 'on' : 'off');
						$personeria 	= ((trim(odbc_result($res,'nitint')) == 'S') ? 'on' : 'off');

						// --> Vaciar la tabla
						if($i == 0){
							$sqlTruncate = "TRUNCATE TABLE  ".$wbasedato."_000189 ";
							$resTruncate = mysql_query( $sqlTruncate, $this->conex ) or die( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() );
						}

						// --> Insertar el nit en matrix
						$sqlInsert = "INSERT INTO ".$wbasedato."_000189
									( Medico   ,	Fecha_data,      		Hora_data,           	Nitnit,									Nitdig,									Nittip,									Nitnom,			Nitraz,				Nitdir,									Nittel,									Nitmun,									Nitnre,	Nitcla,									Nitfre,			Nitret,				Nitext,				Nitper,				Nitcon,	Nitest,	Seguridad       )
							 VALUES ( '$wbasedato', '".date( "Y-m-d" )."', 	'".date( "H:i:s" )."', 	'".trim(odbc_result($res,'nitnit'))."',	'".trim(odbc_result($res,'nitdig'))."',	'".trim(odbc_result($res,'nittip'))."',	'".$nombre."',	'".$razonSocial."',	'".trim(odbc_result($res,'nitdir'))."',	'".trim(odbc_result($res,'nittel'))."',	'".trim(odbc_result($res,'nitmun'))."',	'',		'".trim(odbc_result($res,'nitcla'))."',	'0000-00-00',	'".$retenedor."',	'".$extranjero."',	'".$personeria."',	'', 	'on',	'C-$wbasedato'  ) ";

						$resInsert = mysql_query( $sqlInsert, $this->conex );

						$i++;
					}
				}
				else{
					echo "Error en el query $sql";
				}
			}
			else{
				echo "NO SE CONECTO A UNIX";
			}
		}
		liberarConexionOdbc( $this->conex_u );
		odbc_close_all();
	}

	//----------------------------------------------------------------------------------------------
	//	IMPOTAR MAESTRO DE TIPOS DE EMPRESA DESDE UNIX (INTEM) A MATRIX (cliame_000029)
	// 	Responsable : 			Jerson trujillo.
	//	Fecha de creacion:		2013-12-02.
	//----------------------------------------------------------------------------------------------
	function maestro_tipos_empresa(){

		$wbasedato = $this->consultarAliasPorAplicacion( 'facturacion' );
		$this->conexionOdbc( 'facturacion' );

		$arrayhabitaciones = array();
		$sql = "SELECT Temcod, Temhpd
				  FROM ".$wbasedato."_000029";

		$res = mysql_query($sql, $this->conex) or die("<b>ERROR EN QUERY MATRIX(sql):</b><br>".mysql_error());
		while($row = mysql_fetch_array($res))
		{
			$arrayhabitaciones[$row['Temcod']] = utf8_encode(trim($row['Temhpd']));
		}


		// --> Verificar si para hoy ya se hizo la actualizacion
		$sql = "SELECT id
				  FROM ".$wbasedato."_000029
				 WHERE Fecha_data = '".date( "Y-m-d" )."'
				   AND id = 1 ";

		$res = mysql_query( $sql, $this->conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );

		if( $num == 0 ){

			if( $this->conex_u ){

				// --> Obtener los tipos de empresa desde unix de la tabla INTEM
				$sql = "SELECT temcod, temdes
						  FROM INTEM
						 WHERE temact = 'S'
						";
				$res = odbc_do( $this->conex_u, $sql );

				if( $res ){
					$i = 0;
					while( odbc_fetch_row($res) )
					{
						$nombre	= str_replace("'", "", trim(odbc_result($res,'temdes')));

						// --> Vaciar la tabla
						if($i == 0){
							$sqlTruncate = "TRUNCATE TABLE  ".$wbasedato."_000029 ";
							$resTruncate = mysql_query( $sqlTruncate, $this->conex ) or die( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() );
						}

						// --> Insertar el tipo de empresa en matrix (_000029)
						$sqlInsert = "INSERT INTO ".$wbasedato."_000029
									( Medico   ,	Fecha_data,      		Hora_data,           	Temcod,									Temdes,			Temche,	Temvau,	Temest, Temhpd,	Seguridad       )
							 VALUES ( '$wbasedato', '".date( "Y-m-d" )."', 	'".date( "H:i:s" )."', 	'".trim(odbc_result($res,'temcod'))."',	'".$nombre."',	'off',	'off',	'on'  ,	'".$arrayhabitaciones[trim(odbc_result($res,'temcod'))]."'	  ,	'C-$wbasedato'  ) ";

						$resInsert = mysql_query( $sqlInsert, $this->conex ) or die( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() );

						$i++;
					}
				}
				else{
					echo "Error en el query $sql";
				}
			}
			else{
				echo "NO SE CONECTO A UNIX";
			}
		}
		liberarConexionOdbc( $this->conex_u );
		odbc_close_all();
	}
	//----------------------------------------------------------------------------------------------
	//	IMPOTAR MAESTRO DE TIPOS DE BANCOS DESDE UNIX (CBBAN) A MATRIX (cliame_000069)
	// 	Responsable : 			Felipe alvarez.
	//	Fecha de creacion:		2013-12-02.
	//----------------------------------------------------------------------------------------------
	function maestro_bancos(){

		$wbasedato = $this->consultarAliasPorAplicacion( 'facturacion' );
		$this->conexionOdbc( 'facturacion' );

		// --> Verificar si para hoy ya se hizo la actualizacion
		$sql = "SELECT id
				  FROM ".$wbasedato."_000069
				 WHERE Fecha_data = '".date( "Y-m-d" )."'
				   AND id = 1 ";

		$res = mysql_query( $sql, $this->conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );

		if( $num == 0 ){

			if( $this->conex_u ){

				// --> Obtener bancos  desde unix de la tabla CBBAN
				$sql = "SELECT bancod , bannom, bancue
						  FROM CBBAN
						";

				// --> Aplicarle validacion nulos al query
				$camposNulos 	= "bannom, bancue";
				$valresDefecto 	= "''";
				$select			= "bancod, bannom, bancue";
				$from 			= "CBBAN";

				$sql = $this->construirQueryUnix($from, $camposNulos, $select, "banact = 'S'", $valresDefecto);
				if(!$sql)
				{
					echo "error en funcion maestro_nits --> construirQueryUnix";
					return;
				}

				$res = odbc_do( $this->conex_u, $sql );

				if( $res ){
					$i = 0;
					while( odbc_fetch_row($res) )
					{
						// --> Vaciar la tabla
						if($i == 0){
							$sqlTruncate = "TRUNCATE TABLE  ".$wbasedato."_000069 ";
							$resTruncate = mysql_query( $sqlTruncate, $this->conex ) or die( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() );
						}

						// --> Insertar el tipo de empresa en matrix (_000069)
						$sqlInsert = "INSERT INTO ".$wbasedato."_000069
									( Medico   		,	Fecha_data				, 		Hora_data			,  				 Bancod 					,				 Bannom					,  			Bancue							,  	Banest		,	Bancag	,	Banrec	,	Seguridad       )
							 VALUES ( '$wbasedato'	, '".date( "Y-m-d" )."'		, 	'".date( "H:i:s" )."'	,  '".trim(odbc_result($res,'bancod'))."'	, '".trim(odbc_result($res,'bannom'))."',  '".trim(odbc_result($res,'bancue'))."'	, 	'on'	,	'off'	, 	'off'	,'C-$wbasedato'  ) ";

						$resInsert = mysql_query( $sqlInsert, $this->conex ) or die( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() );

						$i++;
					}
				}
				else{
					echo "Error en el query $sql";
				}
			}
			else{
				echo "NO SE CONECTO A UNIX";
			}
		}
		liberarConexionOdbc( $this->conex_u );
		odbc_close_all();
	}
	//----------------------------------------------------------------------------------------------
	//	IMPOTAR MAESTRO DE TERCEROS DESDE UNIX (TETERTIP) A MATRIX (cliame_000196)
	// 	Responsable : 			Jerson trujillo.
	//	Fecha de creacion:		2013-12-11.
	//----------------------------------------------------------------------------------------------
	function maestro_terceros_no_oficial(){

		$wbasedato = $this->consultarAliasPorAplicacion( 'facturacion' );
		$this->conexionOdbc( 'facturacion' );

		// --> Verificar si para hoy ya se hizo la actualizacion
		$sql = "SELECT id
				  FROM ".$wbasedato."_000196
				 WHERE Fecha_data = '".date( "Y-m-d" )."'
				   AND id = 1 ";

		$res = mysql_query( $sql, $this->conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );

		if( $num == 0 ){

			if( $this->conex_u ){

				// --> Obtener los terceros desde unix de la tabla TETERTIP
				$sql = "SELECT tertipter, tertipnit, tertipnom
						  FROM TETERTIP
						 WHERE tertipact = 'S'
						";
				$res = odbc_do( $this->conex_u, $sql );

				if( $res ){
					$i = 0;
					while( odbc_fetch_row($res) )
					{
						$nombre	= str_replace("'", "", trim(odbc_result($res,'tertipnom')));

						// --> Vaciar la tabla
						if($i == 0){
							$sqlTruncate = "TRUNCATE TABLE  ".$wbasedato."_000196 ";
							$resTruncate = mysql_query( $sqlTruncate, $this->conex ) or die( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() );
						}

						// --> Insertar los terceros en matrix (_000196)
						$sqlInsert = "INSERT INTO ".$wbasedato."_000196
									( Medico   ,	Fecha_data,      		Hora_data,           	Tercod, 									Ternit,										Ternom,			Terest,	Seguridad       )
							 VALUES ( '$wbasedato', '".date( "Y-m-d" )."', 	'".date( "H:i:s" )."', 	'".trim(odbc_result($res,'tertipter'))."',	'".trim(odbc_result($res,'tertipnit'))."',	'".$nombre."',	'on',	'C-$wbasedato'  ) ";

						$resInsert = mysql_query( $sqlInsert, $this->conex ) or die( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() );

						$i++;
					}
				}
				else{
					echo "Error en el query $sql";
				}
			}
			else{
				echo "NO SE CONECTO A UNIX";
			}
		}
		liberarConexionOdbc( $this->conex_u );
		odbc_close_all();
	}
	//----------------------------------------------------------------------------------------------
	//	Maestro de grupos de articulos (Medicamentos y material)
	// 	Responsable : 			Jerson trujillo.
	//	Fecha de creacion:		2014-05-19.
	//----------------------------------------------------------------------------------------------
	function maestroGruposArticulos(){

		$wbasedato = $this->consultarAliasPorAplicacion( 'facturacion' );
		$this->conexionOdbc( 'facturacion' );
		$arrayGruposMatrix = array();

		// --> Obtener los grupos ya creados en matrix
		$sqlGruMatrix = "  SELECT Grucod, Grudes
							 FROM ".$wbasedato."_000004
							WHERE Gruest = 'on' ";

		$resGruMatrix = mysql_query($sqlGruMatrix, $this->conex )or die ("<b>ERROR EN QUERY MATRIX<b><br>:".mysql_error());
		while($rowGruMatrix = mysql_fetch_array($resGruMatrix))
			$arrayGruposMatrix[trim($rowGruMatrix['Grucod'])] = $rowGruMatrix['Grudes'];

		// --> Obtener los grupos en Unix
		$sqlGruUnix = "SELECT grucod, grunom
						 FROM ivgru ";
		$resGruUnix = odbc_do($this->conex_u, $sqlGruUnix);

		if($resGruUnix)
		{
			// --> Recorrer cada grupo
			while(odbc_fetch_row($resGruUnix))
			{
				$codigo	= trim(odbc_result($resGruUnix,'grucod'));
				$nombre	= trim(odbc_result($resGruUnix,'grunom'));

				// --> Si el codigo del grupo ya existe en matrix
				if(array_key_exists($codigo, $arrayGruposMatrix))
				{
					// --> Si el nombre ha cambiado
					if($nombre != $arrayGruposMatrix[$codigo])
					{
						// --> Actualizo el nombre
						$sqlUpdateNom = "UPDATE ".$wbasedato."_000004
											SET Grudes = '".$nombre."'
										  WHERE Grucod = '".$codigo."'
						";
						mysql_query($sqlUpdateNom, $this->conex )or die ("<b>ERROR EN QUERY MATRIX<b><br>:".mysql_error());;
					}
				}
				// --> Si no existe, lo inserto.
				else
				{
					$sqlInsertGru = "INSERT INTO ".$wbasedato."_000004
												 (Medico,		Fecha_data,      		Hora_data,           	Grucod, 		Grudes,			Gruest, Gruinv, Gruarc,		Gruser,	Grutip, Seguridad       )
										  VALUES ('$wbasedato', '".date( "Y-m-d" )."', 	'".date( "H:i:s" )."', 	'".$codigo."', 	'".$nombre."',	'on', 	'on',	'000026',	'A',	'P',	'C-$wbasedato'  ) ";
					mysql_query($sqlInsertGru, $this->conex )or die ("<b>ERROR EN QUERY MATRIX<b><br>:".mysql_error());;
				}
			}
		}
		else
			echo "Error en el query $resGruUnix";

		liberarConexionOdbc( $this->conex_u );
		odbc_close_all();
	}
	//----------------------------------------------------------------------------------------------
	//	IMPOTAR maestro_medicamentos_empresas
	// 	Responsable : 			Felipe Alvarez.
	//	Fecha de creacion:		2013-12-11.
	//----------------------------------------------------------------------------------------------
	function maestro_medicamentos_empresas(){

		$wbasedato = $this->consultarAliasPorAplicacion( 'facturacion' );
		$this->conexionOdbc( 'inventarios' );

		// --> Verificar si para hoy ya se hizo la actualizacion
		$sql = "SELECT id
				  FROM ".$wbasedato."_000214
				 WHERE Fecha_data = '".date( "Y-m-d" )."'
				   AND id = 1 ";

		$res = mysql_query( $sql, $this->conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );

		if( $num == 0 ){

			if( $this->conex_u ){

				// --> Obtener el maestro de unix
				$sql = "SELECT empartart, empartemp, empartcco, empartind, empartmod
						  FROM faempart
						";
				// --> Aplicarle validacion nulos al query
				$camposNulos 	= "empartcco, empartind, empartmod";
				$valresDefecto 	= "''";
				$select			= "empartart, empartemp, empartcco, empartind, empartmod";
				$from 			= "faempart";

				$sql = $this->construirQueryUnix($from, $camposNulos, $select, "", $valresDefecto);
				if(!$sql)
				{
					echo "error en funcion maestro_medicamentos_empresas --> construirQueryUnix";
					return;
				}

				$res = odbc_do( $this->conex_u, $sql );

				if( $res ){
					$i = 0;
					while( odbc_fetch_row($res) )
					{
						// --> Vaciar la tabla
						if($i == 0){
							$sqlTruncate = "TRUNCATE TABLE  ".$wbasedato."_000214 ";
							$resTruncate = mysql_query( $sqlTruncate, $this->conex ) or die( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() );
						}

						$sqlExis = "SELECT id
									  FROM ".$wbasedato."_000214
									 WHERE Aemart = '".trim(odbc_result($res,'empartart'))."'
									   AND Aemtem = '*'
									   AND Aememp = '".trim(odbc_result($res,'empartemp'))."'
									   AND Aemcco = '".trim(odbc_result($res,'empartcco'))."' ";
									   
						$resExist = mysql_query( $sqlExis, $this->conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
						$numExist = mysql_num_rows( $resExist );
						
						if( $numExist == 0 ){
							// --> Insertar los registros en matrix
							$sqlInsert = "INSERT INTO ".$wbasedato."_000214
											(Medico,		Fecha_data,     		Hora_data,           	Aemart,										Aemtem,		Aememp,										Aemcco,										Aemind,										Aemmod,										Aemest,		Seguridad       )
									 VALUES ('$wbasedato',	'".date( "Y-m-d" )."', 	'".date( "H:i:s" )."',	'".trim(odbc_result($res,'empartart'))."',	'*',		'".trim(odbc_result($res,'empartemp'))."', 	'".trim(odbc_result($res,'empartcco'))."', 	'".trim(odbc_result($res,'empartind'))."', '".trim(odbc_result($res,'empartmod'))."',	'on',		'C-$wbasedato'  ) ";

							$resInsert = mysql_query( $sqlInsert, $this->conex ) or die( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() );
						}

						$i++;
					}
				}
				else{
					echo "Error en el query $sql";
				}
			}
			else{
				echo "NO SE CONECTO A UNIX";
			}
		}
		liberarConexionOdbc( $this->conex_u );
		odbc_close_all();
	}
	//----------------------------------------------------------------------------------------------
	//	Borra los registros de las tablas 70-104-192 que hayan sido importados desde unix
	//----------------------------------------------------------------------------------------------
	function borrarRegistrosImportadosDesdeUnix()
	{
		$wbasedato 			= $this->consultarAliasPorAplicacion( 'facturacion' );
		$conexUnix 			= $this->conexionOdbc( 'facturacion' );
		$conex 				= $this->conex;

		$sqlPro = " SELECT count(*)
					  FROM INPRO
				     WHERE proact = 'S'
					   AND procla = '9903' ";
		$resPro = odbc_exec($conexUnix, $sqlPro) or die("<b>ERROR EN QUERY UNIX: (sqlPro)</b><br>".odbc_errormsg());

		// --> 	Si hay almenos un registro en la tabla, procedo a borrar los registros importados desde unix a matrix
		//		2015-03-31, Jerson trujillo
		if(odbc_fetch_row($resPro) && odbc_result($resPro,1) > 0)
		{
			$sqlBorrar70 = "DELETE
			                  FROM ".$wbasedato."_000070
							 WHERE Proempmdu = 'on'
							";
			mysql_query($sqlBorrar70, $conex) or die("<b>ERROR EN QUERY MATRIX (sqlBorrar70):</b><br>".mysql_error());

			$sqlBorrar104 = "DELETE
			                   FROM ".$wbasedato."_000104
							  WHERE Tarmdu = 'on'
							";
			mysql_query($sqlBorrar104, $conex) or die("<b>ERROR EN QUERY MATRIX (sqlBorrar104):</b><br>".mysql_error());

			$sqlBorrar192 = "DELETE
			                   FROM ".$wbasedato."_000192
							  WHERE Hommdu = 'on'
							";
			mysql_query($sqlBorrar192, $conex) or die("<b>ERROR EN QUERY MATRIX (sqlBorrar192):</b><br>".mysql_error());

			// NOTA: Los registros de la 103 no se borran, ya que tiene campos que son actualizados desde matrix (Procpg)
		}
	}

	//----------------------------------------------------------------------------------------------
	//	IMPOTAR MAESTRO DE MEDICAMENTOS EMPRESAS
	// 	Responsable : 			Felipe Alvarez.
	//	Fecha de creacion:		2013-12-11.
	//----------------------------------------------------------------------------------------------
	function tarifasmedicamentos(){

		$wbasedato = $this->consultarAliasPorAplicacion( 'facturacion' );
		$this->conexionOdbc( 'facturacion' );

		// Esta instrucción estaba haciendo que solo se ejecutara la actualización de tarifas de medicamentos una vez al día aunque se quisiera
		// ejecutar manualmente y no de forma automática. Se inactiva esta consultar para permitir que cada vez que ejecuten la opción
		// actualizar tarifas materiales-medicamentos, se puede ejecutar esa acción tantas veces se desee en un mismo día.
		/*$sql = "SELECT *
				  FROM ".$wbasedato."_000026
				 WHERE Fecha_data = '".date( "Y-m-d" )."'
				   AND id = 1 ";

		$res = mysql_query( $sql, $this->conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );

		if( $num == 0 )*/
		{

			if( $this->conex_u ){

				$sql = " SELECT arttarcod, arttartar, arttarvaa, arttarfec, arttarval
						   FROM IVARTTAR
				";

				$res = odbc_do( $this->conex_u, $sql );

				if( $res ){
					$i = 0;
					while( odbc_fetch_row($res) )
					{
						if( $i == 0 ){
							$sqlTruncate = "TRUNCATE TABLE  ".$wbasedato."_000026";

							$resTruncate = mysql_query( $sqlTruncate, $this->conex ) or die( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() );
						}

						$sqlInsert = "INSERT INTO ".$wbasedato."_000026	(Medico,    	Fecha_data,      		Hora_data,           	Mtatar,             				Mtacco,		Mtaart, 							Mtavac,								Mtafec, 							Mtavan,								Mtaest,	Seguridad    		) "
									."			      VALUES   			('$wbasedato', 	'".date( "Y-m-d" )."',	'".date( "H:i:s" )."', 	'".trim( odbc_result($res,2) )."',	'*',		'".trim( odbc_result($res,1) )."', 	'".trim( odbc_result($res,5) )."',	'".trim( odbc_result($res,4) )."',	'".trim( odbc_result($res,3) )."', 	'on', 	'C-$wbasedato'	 	) ";

						$resInsert = mysql_query( $sqlInsert, $this->conex ) or die( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() );
						$i++;
					}
				}
				else{
					echo "Error en el query $sql";
				}
			}
			else{
				echo "NO SE CONECTO A UNIX";
			}
		}
		liberarConexionOdbc( $this->conex_u );
		odbc_close_all();
	}

	//---------------------------------------------------------------------------------------------------------
	//	Funcion que realiza la importacion de los examanes o procedimientos desde unix a matrix, asi como
	//	sus correspondientes tarifas y homologacion, los maestros que se llenan son los siguientes:
	//	cliame_000103 --> Examenes, cliame_000104 --> Tarifas, cliame_000192 --> Homologacion
	// 	La variable de entrada $tipo, indica que tipo de insercion se va a relizar si de procedimientos (P)
	//	o de exmanes (E), ya que dependiendo del tipo cambian algunas consultas
	// 	Responsable : 			Jerson Trujillo.
	//	Fecha de creacion:		2014-08-05.
	//---------------------------------------------------------------------------------------------------------
	function insercionTarifasHomologacionDeFacturacion($tipo)
	{
		$horaIniEjec		= date( "H:i:s" );
		$wbasedato 			= $this->consultarAliasPorAplicacion( 'facturacion' );
		$conexUnix 			= $this->conexionOdbc( 'facturacion' );
		$conex 				= $this->conex;
		$contador 			= 0;
		$exaProImportados	= array();

		// --> 	Obtener todos los conceptos con su correspondiente homologo matrix y
		//		las variables que lo diferencian.
		$sqlConHomo = "SELECT Congen, Consim, Conesp, Congru, Contur, Concco
						 FROM ".$wbasedato."_000197
						WHERE Conest = 'on' ";
		$resSqlConHomo 	= mysql_query($sqlConHomo, $conex ) or die("<b>ERROR EN QUERY MATRIX:</b><br>".mysql_error());
		$arrayConHomo 	= array();
		while($rowSqlConHomo = mysql_fetch_array($resSqlConHomo))
		{
			$arrayConHomo[$rowSqlConHomo['Congen']]['conceptoMatrix'] 		= ((trim($rowSqlConHomo['Consim']) == '' || trim($rowSqlConHomo['Consim']) == 'NO APLICA') ? '*' : $rowSqlConHomo['Consim']);
			$arrayConHomo[$rowSqlConHomo['Congen']]['especialidadMatrix'] 	= ((trim($rowSqlConHomo['Conesp']) == '' || trim($rowSqlConHomo['Conesp']) == 'NO APLICA') ? '*' : $rowSqlConHomo['Conesp']);
			$arrayConHomo[$rowSqlConHomo['Congen']]['grupoMedicoMatrix'] 	= ((trim($rowSqlConHomo['Congru']) == '' || trim($rowSqlConHomo['Congru']) == 'NO APLICA') ? '*' : $rowSqlConHomo['Congru']);
			$arrayConHomo[$rowSqlConHomo['Congen']]['turnoMatrix'] 			= ((trim($rowSqlConHomo['Contur']) == '' || trim($rowSqlConHomo['Contur']) == 'NO APLICA') ? '*' : $rowSqlConHomo['Contur']);
			$arrayConHomo[$rowSqlConHomo['Congen']]['ccoMatrix']			= ((trim($rowSqlConHomo['Concco']) == '' || trim($rowSqlConHomo['Concco']) == 'NO APLICA') ? '*' : $rowSqlConHomo['Concco']);
		}

		// --> Obtener las tarifas y las empresa depuradas desde matrix y guardarlos en un array
		$sqlTarifas = "  SELECT Empcod, Emptem, Emptar, Tardes
						   FROM ".$wbasedato."_000024, ".$wbasedato."_000025
						  WHERE Empest = 'on'
							AND Emptar = Tarcod ";
		$resSqlTarifas 	= mysql_query($sqlTarifas, $conex ) or die("<b>ERROR EN QUERY MATRIX:</b><br>".mysql_error());
		$arrayTarifas   = array();
		while($rowSqlTarifas = mysql_fetch_array($resSqlTarifas))
		{
			// --> Array de empresas con su correspondiente tipo
			$arrayEmpresas[$rowSqlTarifas['Empcod']] 	= $rowSqlTarifas['Emptem'];
			// --> Array de tarifas
			$arrayTarifas[$rowSqlTarifas['Emptar']] 	= $rowSqlTarifas['Tardes'];
		}

		//-----------------------------------------------------------------
		// --> Inicio de insertar examenes y sus correspondientes tarifas
		//-----------------------------------------------------------------

		// --> Crear array con los examenes o procedimientos existentes en matrix
		$SQLexaMatrix = " SELECT Procod
							FROM ".$wbasedato."_000103
						   WHERE Protip = '".$tipo."'
		";
		$RESexaMatrix 	= mysql_query($SQLexaMatrix, $conex) or die("<b>ERROR EN QUERY MATRIX (SQLexaMatrix):</b><br>".mysql_error());
		$examenesMatrix = array();
		while($rowExaMatrix = mysql_fetch_array($RESexaMatrix))
			$examenesMatrix[$rowExaMatrix['Procod']] = '';

		// --> Crear array con los codigos de examenes y procedimientos que estan duplicados
		$sqlCodDup = " SELECT procod
						 FROM inpro, inexa
						WHERE proact = 'S'
						  AND procla = '9903'
						  AND procod = exacod
						  AND exaact = 'S'
		";
		$resCodDup = odbc_exec($conexUnix, $sqlCodDup) or die("<b>ERROR EN QUERY UNIX: (sqlCodDup)</b><br>".odbc_errormsg());
		$arrayCodDup = array();
		while(odbc_fetch_row($resCodDup))
			$arrayCodDup[trim(odbc_result($resCodDup, 'procod'))] = '';

		if($tipo == 'E')
		{
			// --> QUERY ORIGINAL: Obtener los examenes desde UNIX
			$sqlExa = " SELECT exacod, exanom, exaliq, exagex, exauni, 'E', exaane, 'off', 'N'
						  FROM INEXA
						 WHERE exaact = 'S' ";

			// --> Aplicarle validacion nulos al query
			$camposNulos 	= "exauni, exaane";
			$valresDefecto 	= "0, ''";
			$select			= "exacod, exanom, exaliq, exagex, exauni, 'E', exaane, 'off', 'N', exades";
			$from 			= "INEXA";

			$sqlExa = $this->construirQueryUnix($from, $camposNulos, $select, "exaact = 'S' ", $valresDefecto);
			if(!$sqlExa)
			{
				echo "error en funcion insercionTarifasHomologacionDeFacturacion (sqlExa) --> construirQueryUnix";
				return;
			}

			$resSqlExa = odbc_exec($conexUnix, $sqlExa) or die("<b>ERROR EN QUERY UNIX: (sqlExa)</b><br>".odbc_errormsg());
		}
		elseif($tipo == 'P')
		{
			// --> Obtener los procedimientos desde UNIX
			$sqlExa = " SELECT procod, pronom, proliq, proqui, prouni, 'P', proane
						  FROM INPRO
						 WHERE proact = 'S'
						   AND procla = '9903'
						   						   ";

			// --> Aplicarle validacion nulos al query
			$camposNulos 	= "prouni, proane";
			$valresDefecto 	= "0, ''";
			$select			= "procod, pronom, proliq, proqui, prouni, 'P', proane, protip, promay, prodes ";
			$from 			= "INPRO";

			$sqlExa = $this->construirQueryUnix($from, $camposNulos, $select, "proact = 'S' AND procla = '9903'", $valresDefecto);
			if(!$sqlExa)
			{
				echo "error en funcion insercionTarifasHomologacionDeFacturacion (sqlExa) --> construirQueryUnix";
				return;
			}

			$resSqlExa = odbc_exec($conexUnix, $sqlExa) or die("<b>ERROR EN QUERY UNIX: (sqlExa)</b><br>".odbc_errormsg());
		}

		$arrayQuerysTarifa	= array();

		// --> Recorrer cada examen o procedimiento que hay en unix
		while(odbc_fetch_row($resSqlExa))
		{
			$codExamen		= trim(odbc_result($resSqlExa,1));

			// --> 	Excepciones para neonatos, 2014-10-08
			//		Cuando el procedimiento sea iguala S12201 o S12101, se debe cambiar por 'O' , 'L' ya que
			//		estos codigos son para liquidacion de estancia.
			$codExamenUnix = '';
			$ccoPredefinido= '';
			if($codExamen == 'S12201')
			{
				$ccoPredefinido	= '1190';
				$codExamenUnix 	= $codExamen;
				$codExamen 		= 'O';
			}

			if($codExamen == 'S12101')
			{
				$ccoPredefinido	= '1190';
				$codExamenUnix 	= $codExamen;
				$codExamen 		= 'L';
			}

			$guardarEnLa103 = TRUE;

			// --> Homologar codificacion de tipo de facturacion
			$tipFacturacion = trim(odbc_result($resSqlExa,3));
			switch($tipFacturacion)
			{
				case 'C':
				{
					$tipFacturacion = 'CODIGO';
					break;
				}
				case 'U':
				{
					$tipFacturacion = 'UVR';
					break;
				}
				case 'G':
				{
					$tipFacturacion = 'GQX';
					break;
				}
			}

			// --> 	Si el codigo pertenece a los duplicados y es un examen, no se guarda en la 103.
			//		ya que para los codigos duplicados en la 103 se van a guardar los que son procedimientos
			//		y el mismo codigo pero ya como examen, se guarda en la 70 como una excepcion.
			if(array_key_exists($codExamen, $arrayCodDup) && $tipo == 'E')
				$guardarEnLa103 = FALSE;

			$esQuirurgico = ( odbc_result($resSqlExa,8) == "Q"  ) ? "on" : "off";

			if($guardarEnLa103)
			{
				$exaProImportados[$codExamen] = '';

				// --> Si el examen no existe en matrix entonces lo inserto
				if(!array_key_exists($codExamen, $examenesMatrix))
				{
					$sqlInsertExa = "
					INSERT INTO ".$wbasedato."_000103( Medico,				Fecha_data,				Hora_data,				Procod,				Pronom,															Protfa,					Progqx,									Propun,									Protip,									Proest,	Procup,									Promdu, Proqui, 			Protmm, 								Pronoc, 														Seguridad		)
											VALUES   ( '".$wbasedato."',	'".date( "Y-m-d" )."',	'".date( "H:i:s" )."',	'".$codExamen."',	'".str_replace('\'', '', trim(odbc_result($resSqlExa,10)))."',	'".$tipFacturacion."',	'".trim(odbc_result($resSqlExa,4))."',	'".trim(odbc_result($resSqlExa,5))."',	'".trim(odbc_result($resSqlExa,6))."',	'on',	'".trim(odbc_result($resSqlExa,7))."',	'on', 	'{$esQuirurgico}', 	'".trim(odbc_result($resSqlExa,9))."', 	'".str_replace('\'', '', trim(odbc_result($resSqlExa,2)))."',	'C-$wbasedato'  ) ";
					mysql_query( $sqlInsertExa, $conex ) or die("<b>ERROR EN QUERY MATRIX (sqlInsertExa):</b><br>".mysql_error());
				}
				// --> Como el examen ya existe entonces lo actualizo
				else
				{
					$sqlActualizarExa = "
					UPDATE ".$wbasedato."_000103
					   SET Fecha_data = '".date( "Y-m-d" )."',
							Hora_data = '".date( "H:i:s" )."',
							   Pronom = '".str_replace('\'', '', trim(odbc_result($resSqlExa,10)))."',
							   Protfa = '".$tipFacturacion."',
							   Progqx = '".trim(odbc_result($resSqlExa,4))."',
							   Propun = '".trim(odbc_result($resSqlExa,5))."',
							   Proest = 'on',
							   Procup = '".trim(odbc_result($resSqlExa,7))."',
							   Proqui = '".$esQuirurgico."',
							   Protmm = '".trim(odbc_result($resSqlExa,9))."',
							   Pronoc = '".str_replace('\'', '', trim(odbc_result($resSqlExa,2)))."'
					 WHERE 	   Procod = '".$codExamen."'
					   AND	   Protip = '".$tipo."'
					";
					mysql_query( $sqlActualizarExa, $conex ) or die("<b>ERROR EN QUERY MATRIX (sqlActualizarExa):</b><br>".mysql_error());
				}

				// --> Guardar relacion del procedimiento por empresa
				//if($tipo == 'P')
					//$this->insercionRelacionProExaPorEmpresa($codExamen, $conexUnix, $conex, $wbasedato, $tipo);
			}
			// --> Guardar como excepcion en la 70, para cada cco con el que tiene tarifa definida
			else
			{
				// --> 	Obtener todos los cco con los que tiene tarifa el examen, para crearle excepcion
				//		en la 70 por cada uno.
				$sqlCcoRelTar = "SELECT exatarcco
								   FROM INEXATAR
								  WHERE exatarexa = '".(($codExamenUnix == '') ? $codExamen : $codExamenUnix)."'
							   GROUP BY exatarcco ";
				$resCcoRelTar = odbc_exec($conexUnix, $sqlCcoRelTar) or die("<b>ERROR EN QUERY UNIX (sqlCcoRelTar):</b><br>".odbc_errormsg());

				while(odbc_fetch_row($resCcoRelTar))
				{
					// --> Consultar si ya existe la excepcion
					// $sqlExisteExc = "SELECT id
									   // FROM ".$wbasedato."_000070
									  // WHERE Proempcod = '".$codExamen."'
									    // AND Proempemp = '*'
										// AND Proemptip = '*'
										// AND Proempcco = '".trim(odbc_result($resCcoRelTar, 'exatarcco'))."'
					// ";
					// $resExisteExc = mysql_query($sqlExisteExc, $conex) or die("<b>ERROR EN QUERY MATRIX (sqlExisteExc):</b><br>".mysql_error());
					// if($rowExisteExc = mysql_fetch_array($resExisteExc))
					// {
						// --> Actualizo el registro
						// $sqlInsertExa70 = "
						// UPDATE ".$wbasedato."_000070
						   // SET Fecha_data = '".date( "Y-m-d" )."',
							   // Hora_data  = '".date( "H:i:s" )."',
							   // Proempcod  = '".$codExamen."',
							   // Proempemp  = '*',
							   // Proemptip  = '*',
							   // Proemptfa  = '".$tipFacturacion."',
							   // Proempgqx  = '".trim(odbc_result($resSqlExa,4))."',
							   // Proemppun  = '".trim(odbc_result($resSqlExa,5))."',
							   // Proempnom  = '".str_replace('\'', '', trim(odbc_result($resSqlExa,2)))."',
							   // Proempcco  = '".trim(odbc_result($resCcoRelTar, 'exatarcco'))."'
						 // WHERE id 		  = '".$rowExisteExc['id']."'
						// ";
						// mysql_query( $sqlInsertExa70, $conex ) or die("<b>ERROR EN QUERY MATRIX (sqlInsertExa70):</b><br>".mysql_error());
					// }
					// else
					// {
						// --> Inserto el registro
						$sqlInsertExa70 = "
						INSERT INTO ".$wbasedato."_000070( Medico,				Fecha_data,				Hora_data,				Proempcod,			Proempemp,	Proemptip,	Proemptfa,				Proempgqx,								Proemppun,								Proemppro,			Proempnom,														Proempest,	Proempcco,												Proempmdu,	Seguridad			)
												VALUES   ( '".$wbasedato."',	'".date( "Y-m-d" )."',	'".date( "H:i:s" )."',	'".$codExamen."',	'*',		'*',		'".$tipFacturacion."',	'".trim(odbc_result($resSqlExa,4))."',	'".trim(odbc_result($resSqlExa,5))."',	'".$codExamen."',	'".str_replace('\'', '', trim(odbc_result($resSqlExa,2)))."',	'on',		'".trim(odbc_result($resCcoRelTar, 'exatarcco'))."',	'on',		'C-$wbasedato'  	) ";
						mysql_query( $sqlInsertExa70, $conex );
						// if(mysql_error())
							// echo "<br><b>ERROR EN QUERY MATRIX (sqlInsertExa70):</b><br>".mysql_error()."<br><b>QUERY</b>:".$sqlInsertExa70;
					//}
				}
			}

			// --> Guardar relacion del procedimiento por empresa en la cliame_000070
			$this->insercionRelacionProExaPorEmpresa($codExamen, $conexUnix, $conex, $wbasedato, $tipo, $codExamenUnix);

			if($tipo == 'E')
			{
				// --> Obtener las tarifas relacionadas al examen en unix.
				$sqlTarExa = "SELECT exatarcon,	exatartar, exatarcco, exatarval, exatarfec, exatarvaa
								FROM INEXATAR
								WHERE exatarexa = '".(($codExamenUnix == '') ? $codExamen : $codExamenUnix)."'
							ORDER BY exatartar";

				// --> Aplicarle validacion nulos al query
				$camposNulos 	= "exatarval, exatarvaa";
				$valresDefecto 	= "0, 0";
				$select			= "exatarcon, exatartar, exatarcco, exatarval, exatarfec, exatarvaa";
				$from 			= "INEXATAR";

				$sqlTarExa = $this->construirQueryUnix($from, $camposNulos, $select, " exatarexa = '".(($codExamenUnix == '') ? $codExamen : $codExamenUnix)."' ", $valresDefecto);
				if(!$sqlTarExa)
				{
					echo "error en funcion insercionTarifasHomologacionDeFacturacion (sqlExa) --> construirQueryUnix";
					return;
				}
				$resSqlTarExa = odbc_exec($conexUnix, $sqlTarExa) or die("<b>ERROR EN QUERY UNIX (sqlTarExa):</b><br>".odbc_errormsg());
			}
			elseif($tipo == 'P')
			{
				// --> Obtener las tarifas relacionadas al procedimiento en unix.
				$sqlTarExa = "SELECT protarcon,	protartar, protarcco, protarval, protarfec, protarvaa
								FROM INPROTAR
							   WHERE protarpro = '".(($codExamenUnix == '') ? $codExamen : $codExamenUnix)."'
							ORDER BY protartar";

				// --> Aplicarle validacion nulos al query
				$camposNulos 	= "protarval, protarvaa";
				$valresDefecto 	= "0, 0";
				$select			= "protarcon, protartar, protarcco, protarval, protarfec, protarvaa";
				$from 			= "INPROTAR";

				$sqlTarExa = $this->construirQueryUnix($from, $camposNulos, $select, " protarpro = '".(($codExamenUnix == '') ? $codExamen : $codExamenUnix)."' ", $valresDefecto);
				if(!$sqlTarExa)
				{
					echo "error en funcion insercionTarifasHomologacionDeFacturacion (sqlExa) --> construirQueryUnix";
					return;
				}
				$resSqlTarExa = odbc_exec($conexUnix, $sqlTarExa) or die("<b>ERROR EN QUERY UNIX (sqlTarExa):</b><br>".odbc_errormsg());
			}

			// --> Recorrer cada tarifa que tenga el examen o el procedimiento
			while(odbc_fetch_row($resSqlTarExa))
			{
				$conceptoUnix 	= trim(odbc_result($resSqlTarExa, 1));
				$tarifaUnix 	= trim(odbc_result($resSqlTarExa, 2));
				$ccoUnix		= trim(odbc_result($resSqlTarExa, 3));

				if(array_key_exists($conceptoUnix, $arrayConHomo))
				{
					$conceptoMatrix 	= $arrayConHomo[$conceptoUnix]['conceptoMatrix'];
					$especialidadMatrix = $arrayConHomo[$conceptoUnix]['especialidadMatrix'];
					$grupoMedicoMatrix 	= $arrayConHomo[$conceptoUnix]['grupoMedicoMatrix'];
					$turnoMatrix 		= $arrayConHomo[$conceptoUnix]['turnoMatrix'];
					$ccoMatrix 			= $arrayConHomo[$conceptoUnix]['ccoMatrix'];

					$arrayVariables							= array();
					$arrayVariables['conceptoUnix'] 		= $conceptoUnix;
					$arrayVariables['tarifaUnix'] 			= $tarifaUnix;
					$arrayVariables['ccoUnix'] 				= $ccoUnix;
					$arrayVariables['conceptoMatrix'] 		= $conceptoMatrix;
					$arrayVariables['valorActual'] 			= trim(odbc_result($resSqlTarExa, 4));
					$arrayVariables['fechaCambio'] 			= trim(odbc_result($resSqlTarExa, 5));
					$arrayVariables['valorAnterior']		= trim(odbc_result($resSqlTarExa, 6));
					$arrayVariables['especialidadMatrix']	= $especialidadMatrix;
					$arrayVariables['grupoMedicoMatrix']	= $grupoMedicoMatrix;
					$arrayVariables['turnoMatrix']			= $turnoMatrix;
					$arrayVariables['ccoMatrix']			= $ccoMatrix;
					$arrayVariables['ccoPredefinido']		= $ccoPredefinido;
					$arrayVariables['codExamenUnix']		= $codExamenUnix;

					$ccoUnix = (($ccoUnix != '*') ? $ccoUnix : $ccoMatrix);
					
					// --> Regla especifica
					if($conceptoUnix == '0511')
					{
						$ccoUnix = '*';
					}
					

					// --> Si la tarifa es de las oficiales (Depuradas).
					if(array_key_exists($tarifaUnix, $arrayTarifas))
					{
						$indice = count($arrayQuerysTarifa[$codExamen][$conceptoMatrix][$tarifaUnix][$ccoUnix][$especialidadMatrix][$grupoMedicoMatrix][$turnoMatrix]);
						// --> 	Este array es para poder obtener las tarifas duplicadas, ya que como en matrix se simplicaron el numero
						//		de conceptos, entonces se generan muchos duplicados en la tabla matrix; ya mas abajo se analiza como
						//		corregirlo para dejarlo con clave unica.
						$arrayQuerysTarifa[$codExamen][$conceptoMatrix][$tarifaUnix][$ccoUnix][$especialidadMatrix][$grupoMedicoMatrix][$turnoMatrix][$indice]['valores'] = $arrayVariables;
						$arrayQuerysTarifa[$codExamen][$conceptoMatrix][$tarifaUnix][$ccoUnix][$especialidadMatrix][$grupoMedicoMatrix][$turnoMatrix][$indice]['Grabado'] = false;
					}
				}
			}
		}
		// echo "<pre>";
		// print_r($arrayQuerysTarifa);
		// echo "</pre>";
		// return;
		// --> Fin recorrer cada examen o procedimiento que hay en unix

		// --> Recorrer el array que tiene las tarifas y guardarlo en matrix, corrigiendo los duplicados
		foreach($arrayQuerysTarifa as $examen => $array1)
		{
			foreach($array1 as $conMatrix => $array2)
			{
				foreach($array2 as $tarUnix => $array3)
				{
					$ccoAsterisco = true;
					foreach($array3 as $cco => $array4)
					{
						foreach($array4 as $especialidad => $array5)
						{
							foreach($array5 as $grupoMedicoMatrix => $array6)
							{
								foreach($array6 as $turno => $array7)
								{
									// --> Tarifas duplicadas
									if(count($array7) > 1)
									{
										$primerCco = true;
										// --> Recorrer cada una de las duplicadas y aplicarle alguna regla para diferenciarlas
										foreach($array7 as $indice => $variables)
										{
											$variables = $variables['valores'];

											//----------------------------------------------------------
											// --> Aplicar algunas reglas definidas para los duplicados
											//----------------------------------------------------------

											// --> Reglas para los examenes
											if($tipo == 'E')
											{
												// --> 	Si es de alguno de estos dos conceptos entonces se permite grabar la tarifa pero
												//		con el indicador que NO cobra honorarios
												if($variables['conceptoUnix'] == '0605' || $variables['conceptoUnix'] == '0603')
												{
													$variablesG						= array();
													$variablesG['wbasedato'] 		= $wbasedato;
													$variablesG['conexUnix'] 		= $conexUnix;
													$variablesG['conex'] 			= $conex;

													$variablesG['examen'] 			= $examen;
													$variablesG['tipo'] 			= $tipo;
													$variablesG['conMatrix'] 		= $conMatrix;
													$variablesG['tarUnix'] 			= $tarUnix;
													$variablesG['cco'] 				= "*";
													$variablesG['especialidad'] 	= '*';
													$variablesG['aplicaHono'] 		= 'off';
													$variablesG['grupoMedico'] 		= $grupoMedicoMatrix;
													$variablesG['turnoMatrix'] 		= $turno;
													$variablesG['valorActual'] 		= $variables['valorActual'];
													$variablesG['fechaCambio'] 		= $variables['fechaCambio'];
													$variablesG['valorAnterior']	= $variables['valorAnterior'];
													$variablesG['conceptoUnix']		= $variables['conceptoUnix'];

													$errorGrabTar = $this->queryTarifaHomologacionExamen($variablesG, TRUE, TRUE);

													if(!$errorGrabTar)
														$arrayQuerysTarifa[$examen][$conMatrix][$tarUnix][$cco][$especialidad][$grupoMedicoMatrix][$turno][$indice]['Grabado'] = true;
												}
												// --> 	Si es de alguno de estos dos conceptos entonces se permite grabar la tarifa pero
												//		con el indicador que SI cobra honorarios
												if($variables['conceptoUnix'] == '1522' || $variables['conceptoUnix'] == '0601')
												{
													$variablesG						= array();
													$variablesG['wbasedato'] 		= $wbasedato;
													$variablesG['conexUnix'] 		= $conexUnix;
													$variablesG['conex'] 			= $conex;

													$variablesG['examen'] 			= $examen;
													$variablesG['tipo'] 			= $tipo;
													$variablesG['conMatrix'] 		= $conMatrix;
													$variablesG['tarUnix'] 			= $tarUnix;
													$variablesG['cco'] 				= "*";
													$variablesG['especialidad'] 	= '*';
													$variablesG['grupoMedico'] 		= $grupoMedicoMatrix;
													$variablesG['turnoMatrix'] 		= $turno;
													$variablesG['aplicaHono'] 		= 'on';
													$variablesG['valorActual'] 		= $variables['valorActual'];
													$variablesG['fechaCambio'] 		= $variables['fechaCambio'];
													$variablesG['valorAnterior']	= $variables['valorAnterior'];
													$variablesG['conceptoUnix']		= $variables['conceptoUnix'];

													$errorGrabTar = $this->queryTarifaHomologacionExamen($variablesG, TRUE, TRUE);

													if(!$errorGrabTar)
														$arrayQuerysTarifa[$examen][$conMatrix][$tarUnix][$cco][$especialidad][$grupoMedicoMatrix][$turno][$indice]['Grabado'] = true;
												}
												// --> Cuando sea de alguno de estos conceptos, solo se permite crear una sola tarifa, la otra no es necesaria
												if($variables['conceptoUnix'] == '0511' || $variables['conceptoUnix'] == '0506')
												{
													if($variables['conceptoUnix'] == '0511')
													{
														$variablesG						= array();
														$variablesG['wbasedato'] 		= $wbasedato;
														$variablesG['conexUnix'] 		= $conexUnix;
														$variablesG['conex'] 			= $conex;

														$variablesG['examen'] 			= $examen;
														$variablesG['tipo'] 			= $tipo;
														$variablesG['conMatrix'] 		= $conMatrix;
														$variablesG['tarUnix'] 			= $tarUnix;
														$variablesG['cco'] 				= '*';
														$variablesG['especialidad'] 	= '*';
														$variablesG['aplicaHono'] 		= '*';
														$variablesG['grupoMedico'] 		= $grupoMedicoMatrix;
														$variablesG['turnoMatrix'] 		= $turno;
														$variablesG['valorActual'] 		= $variables['valorActual'];
														$variablesG['fechaCambio'] 		= $variables['fechaCambio'];
														$variablesG['valorAnterior']	= $variables['valorAnterior'];
														$variablesG['conceptoUnix']		= $variables['conceptoUnix'];

														$this->queryTarifaHomologacionExamen($variablesG, TRUE, FALSE);
													}

													// -->	Hay que crear homologacion ya que va a quedar una sola tarifa, pero dependiendo de la especialidad del
													//		tercero cambia el concepto en UNIX, este caso es el de los ginecologos que bajan a radiologia.

													$especialidad	= ($variables['conceptoUnix'] == '0511') ? '*' : '100130';
													$variablesG						= array();
													$variablesG['wbasedato'] 		= $wbasedato;
													$variablesG['conexUnix'] 		= $conexUnix;
													$variablesG['conex'] 			= $conex;

													$variablesG['examen'] 			= $examen;
													$variablesG['tipo'] 			= $tipo;
													$variablesG['conMatrix'] 		= $conMatrix;
													$variablesG['tarUnix'] 			= $tarUnix;
													$variablesG['cco'] 				= "*";
													$variablesG['especialidad'] 	= $especialidad;
													$variablesG['aplicaHono'] 		= '*';
													$variablesG['grupoMedico'] 		= $grupoMedicoMatrix;
													$variablesG['turnoMatrix'] 		= $turno;
													$variablesG['valorActual'] 		= $variables['valorActual'];
													$variablesG['fechaCambio'] 		= $variables['fechaCambio'];
													$variablesG['valorAnterior']	= $variables['valorAnterior'];
													$variablesG['conceptoUnix']		= $variables['conceptoUnix'];

													$this->queryTarifaHomologacionExamen($variablesG, FALSE, TRUE);

													$arrayQuerysTarifa[$examen][$conMatrix][$tarUnix][$cco][$especialidad][$grupoMedicoMatrix][$turno][$indice]['Grabado'] = true;
												}

												// --> 	Regla especifica para este examen si es para el concepto unix 0521 se graba la tarifa con especialidad
												//		RADIOLOGIA E IMAGENOLOGIA (100130), si es del concepto 0501 la especialidad es ENDOCRINOLOGIA (100219)
												if($examen == '890202' && $tarUnix == '01' && $cco == '1030')
												{
													$especialidad = (($variables['conceptoUnix'] == '0521') ? '100130' : '100219');
													$variablesG						= array();
													$variablesG['wbasedato'] 		= $wbasedato;
													$variablesG['conexUnix'] 		= $conexUnix;
													$variablesG['conex'] 			= $conex;

													$variablesG['examen'] 			= $examen;
													$variablesG['tipo'] 			= $tipo;
													$variablesG['conMatrix'] 		= $conMatrix;
													$variablesG['tarUnix'] 			= $tarUnix;
													$variablesG['cco'] 				= $cco;
													$variablesG['especialidad'] 	= $especialidad;
													$variablesG['aplicaHono'] 		= '*';
													$variablesG['grupoMedico'] 		= $grupoMedicoMatrix;
													$variablesG['turnoMatrix'] 		= $turno;
													$variablesG['valorActual'] 		= $variables['valorActual'];
													$variablesG['fechaCambio'] 		= $variables['fechaCambio'];
													$variablesG['valorAnterior']	= $variables['valorAnterior'];
													$variablesG['conceptoUnix']		= $variables['conceptoUnix'];

													$errorGrabTar = $this->queryTarifaHomologacionExamen($variablesG, TRUE, TRUE);

													if(!$errorGrabTar)
														$arrayQuerysTarifa[$examen][$conMatrix][$tarUnix][$cco][$especialidad][$grupoMedicoMatrix][$turno][$indice]['Grabado'] = true;
												}
											}

											// --> Reglas para los procedimientos
											if($tipo == 'P')
											{
												// --> 	Array con los codigos de las consultas de urgencias, en estos codigos se guarda
												//		la tarifa con el cco que viene de unix
												$arrayConsulUrgencias = array();
												$arrayConsulUrgencias['890202'] = '';
												$arrayConsulUrgencias['890302'] = '';
												$arrayConsulUrgencias['890402'] = '';
												$arrayConsulUrgencias['890408'] = '';
												$arrayConsulUrgencias['890702'] = '';
												$arrayConsulUrgencias['890602'] = '';

												// --> Array de relacion conceptos unix, con grupos de medicos
												$arrayConUniGruMed 			= array();
												$arrayConUniGruMed['2090'] 	= '01';
												$arrayConUniGruMed['0072'] 	= '*';
												$arrayConUniGruMed['2091'] 	= '01';
												$arrayConUniGruMed['0075'] 	= '*';
												$arrayConUniGruMed['2082'] 	= '02';
												$arrayConUniGruMed['0026'] 	= '*';
												$arrayConUniGruMed['0136'] 	= '03';
												$arrayConUniGruMed['2048'] 	= '*';
												$arrayConUniGruMed['2066'] 	= '04';
												$arrayConUniGruMed['0151'] 	= '05';
												$arrayConUniGruMed['3041'] 	= '06';
												$arrayConUniGruMed['0610'] 	= '07';
												$arrayConUniGruMed['2086'] 	= '*';
												$arrayConUniGruMed['3020'] 	= '08';
												$arrayConUniGruMed['0101'] 	= '*';
												$arrayConUniGruMed['0086'] 	= '08';
												$arrayConUniGruMed['3031'] 	= '09';
												$arrayConUniGruMed['2067'] 	= '10';
												$arrayConUniGruMed['3033'] 	= '09';
												$arrayConUniGruMed['3030'] 	= '09';
												$arrayConUniGruMed['2084'] 	= '02';
												$arrayConUniGruMed['2058'] 	= '06';
												$arrayConUniGruMed['0159'] 	= '11';
												$arrayConUniGruMed['0039'] 	= '*';

												// --> Si el concepto unix tiene relacionado un grupo de medicos
												if(array_key_exists($variables['conceptoUnix'], $arrayConUniGruMed))
												{
													$variablesG						= array();
													$variablesG['wbasedato'] 		= $wbasedato;
													$variablesG['conexUnix'] 		= $conexUnix;
													$variablesG['conex'] 			= $conex;

													$variablesG['examen'] 			= $examen;
													$variablesG['tipo'] 			= $tipo;
													$variablesG['conMatrix'] 		= $conMatrix;
													$variablesG['tarUnix'] 			= $tarUnix;
													$variablesG['cco'] 				= ((array_key_exists($examen, $arrayConsulUrgencias)) ? $cco : '*');
													$variablesG['especialidad'] 	= '*';
													$variablesG['aplicaHono'] 		= '*';
													$variablesG['grupoMedico'] 		= (($grupoMedicoMatrix != '*') ? $grupoMedicoMatrix : $arrayConUniGruMed[$variables['conceptoUnix']]);
													$variablesG['turnoMatrix'] 		= $turno;
													$variablesG['valorActual'] 		= $variables['valorActual'];
													$variablesG['fechaCambio'] 		= $variables['fechaCambio'];
													$variablesG['valorAnterior']	= $variables['valorAnterior'];
													$variablesG['conceptoUnix']		= $variables['conceptoUnix'];

													// --> 	De las tarifas duplicadas se intenta crear una con cco = *, para la mas general osea que tenga
													//		especialidad = '*' y grupoMedico = '*'.
													//		Este cambio se hace el 2015-11-09, para poder tener mas tarifas con cco * de las consultas en urgencias.
													if($primerCco && $variablesG['especialidad'] == '*' && $variablesG['grupoMedico'] == '*')
													{
														$variablesG['cco'] 	= '*';
														$primerCco 			= false;
													}
													$errorGrabTar = $this->queryTarifaHomologacionExamen($variablesG, TRUE, TRUE);
													if(!$errorGrabTar)
													{
														$arrayQuerysTarifa[$examen][$conMatrix][$tarUnix][$cco][$especialidad][$grupoMedicoMatrix][$turno][$indice]['Grabado'] = true;
														continue;
													}
												}
											}
											//----------------------------------------------------------
											// --> Fin aplicar reglas para los duplicados
											//----------------------------------------------------------
										}
									}
									// --> Tarifa unica para grabar
									else
									{
										$variables = $array7[0]['valores'];

										// --> 	Cuando existe una sola tarifa por cco, entonces la creo con cco *
										//if($ccoAsterisco && $tipo == 'P')

										if($ccoAsterisco && $variables['ccoMatrix'] != '*')
											$nuevoCco = $variables['ccoMatrix'];
										else
											if($ccoAsterisco)
											{
												$nuevoCco 		= '*';
												$ccoAsterisco 	= false;
											}
											else
												$nuevoCco 		= $cco;

										$variablesG						= array();
										$variablesG['wbasedato'] 		= $wbasedato;
										$variablesG['conexUnix'] 		= $conexUnix;
										$variablesG['conex'] 			= $conex;

										$variablesG['examen'] 			= $examen;
										$variablesG['tipo'] 			= $tipo;
										$variablesG['conMatrix'] 		= $conMatrix;
										$variablesG['tarUnix'] 			= $tarUnix;
										$variablesG['cco'] 				= (($variables['ccoPredefinido'] != '') ? $variables['ccoPredefinido'] : $nuevoCco);
										$variablesG['especialidad'] 	= $especialidad;
										$variablesG['aplicaHono'] 		= '*';
										$variablesG['grupoMedico'] 		= $grupoMedicoMatrix;
										$variablesG['turnoMatrix'] 		= $turno;
										$variablesG['valorActual'] 		= $variables['valorActual'];
										$variablesG['fechaCambio'] 		= $variables['fechaCambio'];
										$variablesG['valorAnterior']	= $variables['valorAnterior'];
										$variablesG['conceptoUnix']		= $variables['conceptoUnix'];

										if($variables['codExamenUnix'] == 'S12201' || $variables['codExamenUnix'] == 'S12101')
											$variablesG['codExamenUnix']	= $variables['codExamenUnix'];

										$errorGrabTar = $this->queryTarifaHomologacionExamen($variablesG, TRUE, TRUE);
										if(!$errorGrabTar)
											$arrayQuerysTarifa[$examen][$conMatrix][$tarUnix][$cco][$especialidad][$grupoMedicoMatrix][$turno][0]['Grabado'] = true;
										else
										{
											// --> 	Como hubo error en la insercion, se intenta guardar nuevamente el registro pero con el cco especifico.
											//		Este cambio se hace el 2015-11-09, para poder tener mas tarifas con cco * de las consultas en urgencias.
											$variablesG['cco'] 	= $cco;
											$errorGrabTar 		= $this->queryTarifaHomologacionExamen($variablesG, TRUE, TRUE);
											if(!$errorGrabTar)
												$arrayQuerysTarifa[$examen][$conMatrix][$tarUnix][$cco][$especialidad][$grupoMedicoMatrix][$turno][0]['Grabado'] = true;
										}

										// --> 	Regla para el concepto de instrumentadora (0036): 2015-06-12 Jerson trujillo
										// 		Cuando el concepto desde unix sea el 0036, se creara doble homologacion en matrix una en tuno = * para que
										//		grabe en unix con el concepto 0036 y otra que es esta que se esta duplicando, que se guardara en tuno = off para que
										//		grabe en unix con el concepto 0631, que es el concepto de instrumentadora externa, para cuando se grabe por matrix
										//		una instrumentadora con participacion propia (No disponible), La tarifa no se duplica porque las tarifas que tiene
										//		el concepto 0036 tambien sirven para el 0631, por eso en tarifas en la 104 se guarda con Turno = *
										if($variables['conceptoUnix'] == '0036')
										{
											$variablesG['turnoMatrix'] 		= "off";
											$variablesG['conceptoUnix']		= $conMatrix; // --> 0631
											$errorGrabTar = $this->queryTarifaHomologacionExamen($variablesG, FALSE, TRUE);
										}
									}
								}
							}
						}
					}
				}
			}
		}

		// --> 	Borrar los procedimientos o examenes basura que alguna vez hayan sido migrados desde unix
		//		pero que actualmente no se esten migrando
		$sqlExaPro = "    SELECT Procod, id
							FROM ".$wbasedato."_000103
						   WHERE Protip = '".$tipo."'
						     AND Promdu = 'on'
		";
		$resExaPro 		= mysql_query($sqlExaPro, $conex) or die("<b>ERROR EN QUERY MATRIX (sqlExaPro):</b><br>".mysql_error());
		$arrayExaProAct = array();
		while($rowExaPro = mysql_fetch_array($resExaPro))
		{
			if(!array_key_exists($rowExaPro['Procod'], $exaProImportados))
			{
				$sqlDelete = "DELETE
							    FROM ".$wbasedato."_000103
							   WHERE id = '".$rowExaPro['id']."'
				";
				mysql_query($sqlDelete, $conex) or die("<b>ERROR EN QUERY MATRIX (sqlDelete):</b><br>".mysql_error());
			}
		}

		// --> Recorrer nuevamente el array para saber cuales tarifas no se grabaron
		foreach($arrayQuerysTarifa as $examen => $array1)
		{
			foreach($array1 as $conMatrix => $array2)
			{
				foreach($array2 as $tarUnix => $array3)
				{
					foreach($array3 as $cco => $array4)
					{
						foreach($array4 as $especialidad => $array5)
						{
							foreach($array5 as $grupoMedicoMatrix => $array6)
							{
								foreach($array6 as $turno => $array7)
								{
									foreach($array7 as $indice => $variables)
									{
										if(!$variables['Grabado'])
										{
											$variables = $variables['valores'];
											// --> Guardar log de incosistencias
											$log.= PHP_EOL.''.(($tipo=='E') ? 'EXAMEN: ' : 'PROCEDIMIENTO: ' ).$examen.' --> Concepto Matrix:'.$conMatrix.' --> Concepto Unix:'.$variables['conceptoUnix'].' --> Tarifa:'.$tarUnix.' --> Centro Costos:'.$cco.' --> Valor:'.$variables['valorActual'].' [HOMOLOG MATRIX: Especiali:'.$variables['especialidadMatrix'].' -> GrupoMed:'.$variables['grupoMedicoMatrix'].' -> Turno:'.$variables['turnoMatrix'].' -> ccoMatrix:'.$variables['ccoMatrix'].']';
										}
									}
								}
							}
						}
					}
				}
			}
		}

		// --> Guardar log de incosistencias
		$tipoEscritura = (($tipo == 'P') ? 'w+' : 'a+');
		//$tipoEscritura = ((date('d') == '01') ? 'w+' : 'a+');
		$archivo	= fopen("logIncosistenciasCreacionTarifas.txt", $tipoEscritura) or die("Problemas en la creacion del archivo logIncosistenciasCreacionTarifas");
		$log		= PHP_EOL.PHP_EOL.'INICIO MIGRACION DE TARIFAS '.(($tipo == 'P') ? 'PROCEDIMIENTOS' : 'EXAMENES').':'.date( "Y-m-d" )."  ".$horaIniEjec.' FIN MIGRACION:'.date( "Y-m-d" )."  ".date( "H:i:s" ).PHP_EOL.$log;
		fputs($archivo, $log);
		fclose($archivo);

		if( $tipo == 'P' ){
			//--> subir procedimientos inactivos de unix a matrix. 2016-04-06
			// --> Aplicarle validacion nulos al query
			$camposNulos 	= "prouni, proane";
			$valresDefecto 	= "0, ''";
			$select			= "procod, pronom, proliq, proqui, prouni, 'P', proane, protip, promay ";
			$from 			= "INPRO";

			$sqlExa = $this->construirQueryUnix($from, $camposNulos, $select, "proact = 'N' AND procla = '9903' ", $valresDefecto);
			//echo $sqlExa;
			if(!$sqlExa)
			{
				echo "error en funcion carga de procedimientos inactivos (sqlExa) --> construirQueryUnix";
				return;
			}
			$resSqlExa = odbc_exec($conexUnix, $sqlExa) or die("<b>ERROR EN QUERY UNIX (sqlTarExa):</b><br>".odbc_errormsg());
			while(odbc_fetch_row($resSqlExa))
			{
				//echo " guardando ".trim(odbc_result($resSqlExa,1));
				$esQuirurgico = ( odbc_result($resSqlExa,8) == "Q"  ) ? "on" : "off";
				$sqlInsertExa = "
					INSERT INTO ".$wbasedato."_000103( Medico,				Fecha_data,				Hora_data,				Procod,				Pronom,															Protfa,					Progqx,									Propun,									Protip,									Proest,    	Procup,									Promdu, Proqui, Protmm, Seguridad	)
											VALUES   ( '".$wbasedato."',	'".date( "Y-m-d" )."',	'".date( "H:i:s" )."',	'".trim(odbc_result($resSqlExa,1))."',	'".str_replace('\'', '', trim(odbc_result($resSqlExa,2)))."',	'',                  	'',	                                 '',	      '".trim(odbc_result($resSqlExa,6))."',	               'off',	'".trim(odbc_result($resSqlExa,7))."',	'on', '{$esQuirurgico}', '".trim(odbc_result($resSqlExa,9))."', 'C-$wbasedato'  	) ";
					mysql_query( $sqlInsertExa, $conex ) or die("<b>ERROR EN QUERY MATRIX (sqlInsertExa):</b><br>".mysql_error());
			}
		}


		liberarConexionOdbc( $conexUnix );
		odbc_close_all();
	}
	//---------------------------------------------------------------------------------------------
	//	Funcion que actualiza o graba un nuevo registro, de tarifa y homologacion de examenes
	// 	Responsable : 			Jerson Trujillo.
	//	Fecha de creacion:		2014-08-05.
	//----------------------------------------------------------------------------------------------
	function queryTarifaHomologacionExamen($variables, $grabarTarifa, $grabarHomologacion)
	{
		global $wbasedato;
		global $conexUnix;
		global $conex;

		// -- Creacion de variables
		foreach($variables as $nombre => $valor)
			$$nombre = $valor;

		if($grabarTarifa)
		{
			// --> Consultar si el registro para la tarifa ya existe
			// $sqlExisteTar = " SELECT id
								// FROM ".$wbasedato."_000104
							   // WHERE Tarcod = '".$examen."'
								 // AND Tarcon = '".$conMatrix."'
								 // AND Tartar = '".$tarUnix."'
								 // AND Tartin = '*'
								 // AND Tarcco = '".$cco."'
								 // AND Taresp = '".$especialidad."'
								 // AND Tarhon = '".$aplicaHono."'
								 // AND Taruvi = '0'
								 // AND Targme = '".$grupoMedico."'
			// ";
			// $resExisteTar = mysql_query($sqlExisteTar, $conex) or die("<b>ERROR EN QUERY MATRIX (sqlExisteTar):</b><br>".mysql_error());

			// --> Si existe entonces actualizo
			// if($rowExisteTar = mysql_fetch_array($resExisteTar))
			// {
				// $sqlUpdateTar = " UPDATE ".$wbasedato."_000104
									 // SET Fecha_data = '".date( "Y-m-d" )."',
										 // Hora_data  = '".date( "H:i:s" )."',
										 // Tarcod = '".$examen."',
										 // Tarcon = '".$conMatrix."',
										 // Tartar = '".$tarUnix."',
										 // Tartin = '*',
										 // Tarcco = '".$cco."',
										 // Taresp = '".$especialidad."',
										 // Tarhon = '".$aplicaHono."',
										 // Tarvac = '".$valorActual."',
										 // Tarfec = '".$fechaCambio."',
										 // Tarvan = '".$valorAnterior."',
										 // Taruvi = '0',
										 // Taruvf = '0',
										 // Targme = '".$grupoMedico."'
								   // WHERE     id = '".$rowExisteTar['id']."'
				// ";
				// mysql_query($sqlUpdateTar, $conex);
				// if(mysql_error())
					// echo "<br><b>ERROR EN QUERY MATRIX (sqlUpdateTar):</b><br>".mysql_error()."<br><b>QUERY</b>:".$sqlUpdateTar;
			// }
			// --> Si no existe, inserto un nuevo registro
			// else
			// {
				$sqlInsertTar = "INSERT INTO ".$wbasedato."_000104 (Medico,    			Fecha_data,      		Hora_data,				Tarcod,			Tarcon,	 			Tartar, 			Tartin,	Tarcco,			Taresp,				Tarhon,				Tarvac,				Tarfec,				Tarvan,					Tarest,	Taruvi,	Taruvf,	Targme, 			Tarmdu,	Tartur, 			Seguridad    	)
															VALUES ('".$wbasedato."', 	'".date( "Y-m-d" )."',	'".date( "H:i:s" )."',	'".$examen."',	'".$conMatrix."',	'".$tarUnix."', 	'*',	'".$cco."', 	'".$especialidad."','".$aplicaHono."',	'".$valorActual."',	'".$fechaCambio."',	'".$valorAnterior."',	'on',	'0',	'0',	'".$grupoMedico."', 'on',	'".$turnoMatrix."', 'C-$wbasedato'	) ";

				mysql_query($sqlInsertTar, $conex );
				if(mysql_error())
				{
					$error = true;
					// echo "<br><br><b>ERROR EN QUERY MATRIX (sqlInsertTar):</b><br>".mysql_error()."<br><b>QUERY</b>:".$sqlInsertTar;
					// echo "<br>";
					// foreach($variables as $nameVar => $valorVar)
					// {
						// if($nameVar != 'conexUnix' && $nameVar != 'conex' && $nameVar != 'wbasedato')
							// echo '-->'.$nameVar."=".$valorVar;
					// }
				}
				else
					$error = false;
			// }
		}

		if($grabarHomologacion && !$error)
		{
			// --> Consultar si el registro para la homologacion ya existe
			// $sqlExisteHom = " SELECT id
								// FROM ".$wbasedato."_000192
							   // WHERE Homcom = '".$conMatrix."'
								 // AND Hompom = '".$examen."'
								 // AND Homccm = '".$cco."'
								 // AND Homtem = '*'
								 // AND Homtam = '".$tarUnix."'
								 // AND Homenm = '*'
								 // AND Homtim = '*'
								 // AND Homtpm = '*'
								 // AND Homtrm = '*'
								 // AND Homesm = '".$especialidad."'
								 // AND Homthm = '".$aplicaHono."'
								 // AND Homtct = '01'
								 // AND Homgme = '".$grupoMedico."'
			// ";
			// $resExisteHom = mysql_query($sqlExisteHom, $conex) or die("<b>ERROR EN QUERY MATRIX (sqlExisteHom):</b><br>".mysql_error());

			// --> Si existe entonces actualizo
			// if($rowExisteHom = mysql_fetch_array($resExisteHom))
			// {
				// $sqlUpdateHom = " UPDATE ".$wbasedato."_000192
									 // SET Fecha_data = '".date( "Y-m-d" )."',
										 // Hora_data  = '".date( "H:i:s" )."',
										 // Homcom = '".$conMatrix."',
										 // Hompom = '".$examen."',
										 // Homccm = '".$cco."',
										 // Homtem = '*',
										 // Homtam = '".$tarUnix."',
										 // Homenm = '*',
										 // Homtim = '*',
										 // Homtpm = '*',
										 // Homtrm = '*',
										 // Homesm = '".$especialidad."',
										 // Homthm = '".$aplicaHono."',
										 // Homtct = '01',
										 // Homgme = '".$grupoMedico."',
										 // Homcos = '".$conceptoUnix."',
										 // Hompos = '".$examen."'
								   // WHERE     id = '".$rowExisteHom['id']."'
				// ";
				// mysql_query($sqlUpdateHom, $conex);
				// if(mysql_error())
					// echo "<br><b>ERROR EN QUERY MATRIX (sqlUpdateHom):</b><br>".mysql_error()."<br><b>QUERY</b>:".$sqlUpdateHom;
			// }
			// --> Si no existe, inserto un nuevo registro
			// else
			// {

				if(isset($codExamenUnix) && $codExamenUnix != '')
					$examenHomo = $codExamenUnix;
				else
					$examenHomo = $examen;

				$sqlHomolog   = "INSERT INTO ".$wbasedato."_000192 (Medico,    			Fecha_data,      		Hora_data,				Homcom,				Hompom,	 		Homccm,		Homtem,	Homtam, 		Homenm,	Homtim,	Homtpm,	Homtrm, Homesm, 			Homthm,				Homtct,				Homgme,				Homcos,				Hompos,				Homtis,	Homtrs,	Homest,	Hommdu, Seguridad    	)
															VALUES ('".$wbasedato."', 	'".date( "Y-m-d" )."',	'".date( "H:i:s" )."',	'".$conMatrix."',	'".$examen."',	'".$cco."',	'*',	'".$tarUnix."', '*',	'*',	'*',	'*',	'".$especialidad."','".$aplicaHono."',	'".$turnoMatrix."',	'".$grupoMedico."', '".$conceptoUnix."','".$examenHomo."',	'*',	'*',	'on',	'on',	'C-$wbasedato'	) ";
				mysql_query($sqlHomolog, $conex );
				//if(mysql_error())
					//echo "<br><b>ERROR EN QUERY MATRIX (sqlHomolog):</b><br>".mysql_error()."<br><b>QUERY</b>:".$sqlHomolog;
			//}
		}

		return $error;
	}
	//---------------------------------------------------------------------------------------------
	//	Funcion que inserta (cliame_000070) las excepciones de las modalidades de cobro de los
	//	procedimientos y examenes.
	// 	Responsable :		Jerson Trujillo
	//	Fecha de creacion:	2014-08-28
	//----------------------------------------------------------------------------------------------
	function insercionRelacionProExaPorEmpresa($examen, $conexUnix, $conex, $wbasedato, $tipo, $codExamenUnix)
	{
		// --> Consultar las excepciones en unix para los procedimientos
		if($tipo == 'P')
		{
			// --> Query original
			$sqlExpExa = "SELECT proempemp, proempane, proempnom, proempliq, proempuni
							FROM INPROEMP
						   WHERE proemppro = '".$examen."' ";

			// --> Aplicarle validacion nulos al query
			$camposNulos 	= "proempane, proempnom, proempuni";
			$valresDefecto 	= "'', '', 0";
			$select			= "proempemp, proempane, proempnom, proempliq, proempuni";
			$from 			= "INPROEMP";

			$sqlExpExa = $this->construirQueryUnix($from, $camposNulos, $select, "proemppro = '".(($codExamenUnix == '') ? $examen : $codExamenUnix)."' ", $valresDefecto);
			if(!$sqlExpExa)
			{
				echo "error en funcion insercionTarifasHomologacionDeFacturacion (sqlExpExa) --> construirQueryUnix";
				return;
			}
		}

		// --> Consultar las excepciones en unix para los examenes
		if($tipo == 'E')
		{
			// --> Query original
			$sqlExpExa = "SELECT exaempemp, exaempane, exaempnom, exaempliq, exaempuni
							FROM INEXAEMP
						   WHERE exaempexa = '".$examen."' ";

			// --> Aplicarle validacion nulos al query
			$camposNulos 	= "exaempuni";
			$valresDefecto 	= "0";
			$select			= "exaempemp, exaempane, exaempnom, exaempliq, exaempuni";
			$from 			= "INEXAEMP";

			$sqlExpExa = $this->construirQueryUnix($from, $camposNulos, $select, "exaempexa = '".(($codExamenUnix == '') ? $examen : $codExamenUnix)."' ", $valresDefecto);
			if(!$sqlExpExa)
			{
				echo "error en funcion insercionTarifasHomologacionDeFacturacion (sqlExpExa) --> construirQueryUnix";
				return;
			}
		}

		$resSqlExeExa = odbc_exec($conexUnix, $sqlExpExa) or die("<b>ERROR EN QUERY UNIX (sqlExpExa):</b><br>".odbc_errormsg());
		// --> Recorrer cada excepcion
		while(odbc_fetch_row($resSqlExeExa))
		{
			$expEmpresa 	= trim(odbc_result($resSqlExeExa,1));
			$expAnexo 		= trim(odbc_result($resSqlExeExa,2));
			$expNombre 		= str_replace("'", '', trim(odbc_result($resSqlExeExa,3)));
			$expTipLiq		= trim(odbc_result($resSqlExeExa,4));
			$expNumUvr		= trim(odbc_result($resSqlExeExa,5));

			switch($expTipLiq)
			{
				case 'C':
				{
					$expTipLiq = 'CODIGO';
					break;
				}
				case 'U':
				{
					$expTipLiq = 'UVR';
					break;
				}
				case 'G':
				{
					$expTipLiq = 'GQX';
					break;
				}
			}

			// --> Consultar si ya existe la excepcion
			// $sqlExisteExc = "SELECT id
							   // FROM ".$wbasedato."_000070
							  // WHERE Proempcod = '".$examen."'
								// AND Proempemp = '".$expEmpresa."'
								// AND Proemptip = '*'
								// AND Proempcco = '*'
			// ";
			// $resExisteExc = mysql_query($sqlExisteExc, $conex) or die("<b>ERROR EN QUERY MATRIX (sqlExisteExc):</b><br>".mysql_error());
			// if($rowExisteExc = mysql_fetch_array($resExisteExc))
			// {
				// --> Actualizo el registro de la excepcion
				// $sqlInsertExa70 = "
				// UPDATE ".$wbasedato."_000070
				   // SET Fecha_data = '".date( "Y-m-d" )."',
					   // Hora_data  = '".date( "H:i:s" )."',
					   // Proempcod  = '".$examen."',
					   // Proempemp  = '".$expEmpresa."',
					   // Proemptip  = '*',
					   // Proemptfa  = '".$expTipLiq."',
					   // Proemppun  = '".$expNumUvr."',
					   // Proemppro  = '".$expAnexo."',
					   // Proempnom  = '".$expNombre."',
					   // Proempcco  = '*'
				 // WHERE id 		  = '".$rowExisteExc['id']."'
				// ";
				// mysql_query( $sqlInsertExa70, $conex );
				// if(mysql_error())
					// echo "<br><b>ERROR EN QUERY DE INSERCION EN LA 70:</b><br>".mysql_error()."<br><b>QUERY</b>:".$sqlInsertExa70;
			// }
			// else
			// {
				// --> Insertar la excepcion en matrix (000070)
				$sqlInsert70 = "
				INSERT INTO ".$wbasedato."_000070( Medico,				Fecha_data,				Hora_data,				Proempcod,		Proempemp,			Proemptip,		Proemptfa,			Proemppun,			Proemppro,			Proempnom,			Proempest,	Proempcco,	Proempmdu,	Seguridad		)
										   VALUES( '".$wbasedato."',	'".date( "Y-m-d" )."',	'".date( "H:i:s" )."',	'".$examen."',	'".$expEmpresa."',	'*',			'".$expTipLiq."',	'".$expNumUvr."',	'".$expAnexo."',	'".$expNombre."',	'on',		'*',		'on',		'C-$wbasedato'  ) ";
				mysql_query($sqlInsert70, $conex );
				/*if(mysql_error())
					echo "<br><b>ERROR EN QUERY DE INSERCION EN LA 70:</b><br>".mysql_error()."<br><b>QUERY</b>:".$sqlInsert70;*/
			//}
		}
	}
	//---------------------------------------------------------------------------------------------------------
	//	Funcion que realiza la importacion de las tarifas de UVR, Grupos quirurjicos, habitaciones y conceptos
	//	desde unix y los inserta en la cliame_000104
	// 	Responsable : 			Jerson Trujillo.
	//	Fecha de creacion:		2014-08-05.
	//---------------------------------------------------------------------------------------------------------
	function insercionTarifasUvrGqxHabitacionesConceptos()
	{
		$horaIniEjec= date( "H:i:s" );
		$wbasedato 	= $this->consultarAliasPorAplicacion( 'facturacion' );
		$conexUnix 	= $this->conexionOdbc( 'facturacion' );
		$conex 		= $this->conex;

		// --> 	Obtener todos los conceptos con su correspondiente homologo matrix y
		//		las variables que lo diferencian.
		$sqlConHomo = "SELECT Congen, Consim, Conesp, Congru, Contur, Concco
						 FROM ".$wbasedato."_000197
						WHERE Conest = 'on' ";
		$resSqlConHomo 	= mysql_query($sqlConHomo, $conex ) or die("<b>ERROR EN QUERY MATRIX:</b><br>".mysql_error());
		$arrayConHomo 	= array();
		while($rowSqlConHomo = mysql_fetch_array($resSqlConHomo))
		{
			$arrayConHomo[$rowSqlConHomo['Congen']]['conceptoMatrix'] 		= ((trim($rowSqlConHomo['Consim']) == '' || trim($rowSqlConHomo['Consim']) == 'NO APLICA') ? '*' : $rowSqlConHomo['Consim']);
			$arrayConHomo[$rowSqlConHomo['Congen']]['especialidadMatrix'] 	= ((trim($rowSqlConHomo['Conesp']) == '' || trim($rowSqlConHomo['Conesp']) == 'NO APLICA') ? '*' : $rowSqlConHomo['Conesp']);
			$arrayConHomo[$rowSqlConHomo['Congen']]['grupoMedicoMatrix'] 	= ((trim($rowSqlConHomo['Congru']) == '' || trim($rowSqlConHomo['Congru']) == 'NO APLICA') ? '*' : $rowSqlConHomo['Congru']);
			$arrayConHomo[$rowSqlConHomo['Congen']]['turnoMatrix'] 			= ((trim($rowSqlConHomo['Contur']) == '' || trim($rowSqlConHomo['Contur']) == 'NO APLICA') ? '*' : $rowSqlConHomo['Contur']);
			$arrayConHomo[$rowSqlConHomo['Congen']]['ccoMatrix']			= ((trim($rowSqlConHomo['Concco']) == '' || trim($rowSqlConHomo['Concco']) == 'NO APLICA') ? '*' : $rowSqlConHomo['Concco']);
		}

		// --> Insertar los codigos de las habitaciones, en la tabla de procedimientos
		$sqlHab = " SELECT tipcod, tipdes, 'CODIGO', 'H'
					  FROM INTIP
					 WHERE tipact = 'S'
					 GROUP BY tipcod, tipdes";
		$resSqlHab = odbc_exec($conexUnix, $sqlHab) or die("<b>ERROR EN QUERY UNIX:</b><br>".odbc_errormsg());

		// --> Recorrer cada habitacion e insertarla en matrix
		while(odbc_fetch_row($resSqlHab))
		{
			$codHab			= trim(odbc_result($resSqlHab,1));
			$tipFacturacion = trim(odbc_result($resSqlHab,3));

			// --> Consultar si la habitacion ya existe en el maestro
			$sqlExistesHab = "SELECT id
							    FROM ".$wbasedato."_000103
							   WHERE Procod = '".$codHab."'
			";
			$resExistesHab = mysql_query($sqlExistesHab, $conex)or die("<b>ERROR EN QUERY MATRIX (sqlExistesHab):</b><br>".mysql_error());

			if($rowExistesHab = mysql_fetch_array($resExistesHab))
			{
				// --> Como ya existe entonces actualizo los campos que dependan de unix
				$updateHab = "
				UPDATE ".$wbasedato."_000103
				   SET Fecha_data = '".date( "Y-m-d" )."',
					   Hora_data  = '".date( "H:i:s" )."',
					   Procod	  = '".$codHab."',
					   Pronom	  = '".str_replace('\'', '', trim(odbc_result($resSqlHab,2)))."',
					   Protfa	  = '".$tipFacturacion."',
					   Protip	  = '".trim(odbc_result($resSqlHab,4))."'
				 WHERE id 		  = '".$rowExistesHab['id']."'
				";
				mysql_query($updateHab, $conex ) or die("<b>ERROR EN QUERY MATRIX (updateHab):</b><br>".mysql_error());
			}
			else
			{
				// --> Guardar la habitacion en la 103
				$sqlInsert = "
				INSERT INTO ".$wbasedato."_000103( Medico,				Fecha_data,				Hora_data,				Procod,			Pronom,															Protfa,					Progqx,	Propun,	Protip,									Proest,	Procup,	Promdu, Seguridad	)
										VALUES   ( '".$wbasedato."',	'".date( "Y-m-d" )."',	'".date( "H:i:s" )."',	'".$codHab."',	'".str_replace('\'', '', trim(odbc_result($resSqlHab,2)))."',	'".$tipFacturacion."',	'',		'0',	'".trim(odbc_result($resSqlHab,4))."',	'on',	'',		'on', 	'C-root'  	) ";
				mysql_query( $sqlInsert, $conex ); //or die("<b>ERROR EN QUERY MATRIX:</b><br>".mysql_error());
			}
		}

		// --> Obtener las tarifas y las empresa depuradas desde matrix y guardarlos en un array
		$sqlTarifas = "  SELECT Emptar, Tardes
						   FROM ".$wbasedato."_000024, ".$wbasedato."_000025
						  WHERE Empest = 'on'
							AND Emptar = Tarcod ";
		$resSqlTarifas 	= mysql_query($sqlTarifas, $conex ) or die("<b>ERROR EN QUERY MATRIX:</b><br>".mysql_error());
		$arrayTarifas   = array();
		while($rowSqlTarifas = mysql_fetch_array($resSqlTarifas))
			$arrayTarifas[$rowSqlTarifas['Emptar']] 	= $rowSqlTarifas['Tardes'];

		// --> Consultar las tarifas de UVR, grupos quirurjicos y habitaciones.
		$sql = " SELECT 'UVR',		unitarcon,	unitartar,	unitarval,	unitarfec,	unitarvaa, 	'UVR' AS tipo, 			'*'
				   FROM FAUNITAR
				  UNION
				 SELECT	tipcod,		tipcon, 	tiptar,		tipval,  	tipfec, 	tipvaa,		'HABITACION' AS tipo,	'*'
				   FROM INTIP
				  UNION
				 SELECT quitarqui, 	quitarcon, 	quitartar,	quitarval,	quitarfec, 	quitarvaa,	'GRUPO GQX' AS tipo,	quitarcco
				   FROM INQUITAR
				  UNION
				 SELECT '*', 		contarcon, 	contartar,	contarval,	contarfec, 	contarvaa,	'CONCEPTO' AS tipo, 	'*'
				   FROM FACONTAR
				  WHERE contarcon = '0023'
			";

		$resSqlTarExa = odbc_exec($conexUnix, $sql) or die("<b>ERROR EN QUERY UNIX:</b><br>".odbc_errormsg());

		// --> Recorrer cada tarifa
		while(odbc_fetch_row($resSqlTarExa))
		{
			$codigo			= trim(odbc_result($resSqlTarExa, 1));
			$conceptoUnix 	= trim(odbc_result($resSqlTarExa, 2));
			$tarifaUnix 	= trim(odbc_result($resSqlTarExa, 3));
			$valorActual	= trim(odbc_result($resSqlTarExa, 4));
			$ccoUnix		= trim(odbc_result($resSqlTarExa, 8));

			if(array_key_exists($conceptoUnix, $arrayConHomo))
			{
				$conceptoMatrix 	= $arrayConHomo[$conceptoUnix]['conceptoMatrix'];
				$especialidadMatrix = $arrayConHomo[$conceptoUnix]['especialidadMatrix'];
				$grupoMedicoMatrix 	= $arrayConHomo[$conceptoUnix]['grupoMedicoMatrix'];
				$turnoMatrix 		= $arrayConHomo[$conceptoUnix]['turnoMatrix'];
				$ccoMatrix 			= $arrayConHomo[$conceptoUnix]['ccoMatrix'];

				$arrayVariables							= array();
				$arrayVariables['conceptoUnix'] 		= $conceptoUnix;
				$arrayVariables['tarifaUnix'] 			= $tarifaUnix;
				$arrayVariables['ccoUnix'] 				= $ccoUnix;
				$arrayVariables['conceptoMatrix'] 		= $conceptoMatrix;
				$arrayVariables['valorActual'] 			= $valorActual;
				$arrayVariables['fechaCambio'] 			= trim(odbc_result($resSqlTarExa, 5));
				$arrayVariables['valorAnterior']		= trim(odbc_result($resSqlTarExa, 6));
				$arrayVariables['especialidadMatrix']	= $especialidadMatrix;
				$arrayVariables['grupoMedicoMatrix']	= $grupoMedicoMatrix;
				$arrayVariables['turnoMatrix']			= $turnoMatrix;
				$arrayVariables['ccoMatrix']			= $ccoMatrix;

				$ccoUnix = (($ccoUnix != '*') ? $ccoUnix : $ccoMatrix);

				// --> Si la tarifa es de las oficiales (Depuradas).
				if(array_key_exists($tarifaUnix, $arrayTarifas))
				{
					$indice = count($arrayQuerysTarifa[$codigo][$conceptoMatrix][$tarifaUnix][$ccoUnix][$especialidadMatrix][$grupoMedicoMatrix][$turnoMatrix]);
					// --> 	Para estas tarifas de uvr y grupos, si el valor actual es el mismo a la tarifa que ya existe
					//		entonces solo la creo una sola vez.
					/*if($indice > 0)
					{
						if($arrayQuerysTarifa[$codigo][$conceptoMatrix][$tarifaUnix][$ccoUnix][$indice-1]['valores']['valorActual'] == $valorActual)
							continue;
					}*/

					// --> 	Este array es para poder obtener las tarifas duplicadas, ya que como en matrix se simplicaron el numero
					//		de conceptos, entonces se generan muchos duplicados en la tabla matrix; ya mas abajo se analiza como
					//		corregirlo para dejarlo con clave unica.
					$arrayQuerysTarifa[$codigo][$conceptoMatrix][$tarifaUnix][$ccoUnix][$especialidadMatrix][$grupoMedicoMatrix][$turnoMatrix][$indice]['valores'] = $arrayVariables;
					$arrayQuerysTarifa[$codigo][$conceptoMatrix][$tarifaUnix][$ccoUnix][$especialidadMatrix][$grupoMedicoMatrix][$turnoMatrix][$indice]['Grabado'] = false;
				}
			}
		}
		// echo "<pre>";
		// print_r($arrayQuerysTarifa);
		// echo "</pre>";

		// --> Recorrer el array que tiene las tarifas y guardarlo en matrix, corrigiendo los duplicados
		foreach($arrayQuerysTarifa as $examen => $array1)
		{
			foreach($array1 as $conMatrix => $array2)
			{
				foreach($array2 as $tarUnix => $array3)
				{
					$ccoAsterisco = true;
					foreach($array3 as $cco => $array4)
					{
						foreach($array4 as $especialidad => $array5)
						{
							foreach($array5 as $grupoMedicoMatrix => $array6)
							{
								foreach($array6 as $turno => $array7)
								{
									// --> Tarifas duplicadas
									if(count($array7) > 1)
									{
										$primerCco = true;

										// --> Recorrer cada una de las duplicadas y aplicarle alguna regla para diferenciarlas
										foreach($array7 as $indice => $variables)
										{
											$variables = $variables['valores'];

											//----------------------------------------------------------
											// --> Aplicar algunas reglas definidas para los duplicados
											//----------------------------------------------------------

											// --> Duplicados en tarifas de uvr's
											if($examen == 'UVR')
											{
												// --> 	Conceptos relacionados con la especialidad cardiologia vascular
												//		entonces diferencio la tarifa por esta especialidad (100131)
												if($variables['conceptoUnix'] == '3041' || $variables['conceptoUnix'] == '0072' || $variables['conceptoUnix'] == '3042' || $variables['conceptoUnix'] == '0075')
												{
													$variablesG						= array();
													$variablesG['wbasedato'] 		= $wbasedato;
													$variablesG['conexUnix'] 		= $conexUnix;
													$variablesG['conex'] 			= $conex;

													$variablesG['examen'] 			= $examen;
													$variablesG['tipo'] 			= $tipo;
													$variablesG['conMatrix'] 		= $conMatrix;
													$variablesG['tarUnix'] 			= $tarUnix;
													$variablesG['cco'] 				= $cco;
													$variablesG['especialidad'] 	= (($variables['conceptoUnix'] == '3041' || $variables['conceptoUnix'] == '3042') ? '100131' : '*');
													$variablesG['aplicaHono'] 		= '*';
													$variablesG['grupoMedico'] 		= $grupoMedicoMatrix;
													$variablesG['turnoMatrix'] 		= $turno;
													$variablesG['valorActual'] 		= $variables['valorActual'];
													$variablesG['fechaCambio'] 		= $variables['fechaCambio'];
													$variablesG['valorAnterior']	= $variables['valorAnterior'];
													$variablesG['conceptoUnix']		= $variables['conceptoUnix'];

													// --> 	Se intenta crear una con cco = *, para la mas general osea que tenga
													//		especialidad = '*' y grupoMedico = '*'.
													if($primerCco && $variablesG['especialidad'] == '*' && $variablesG['grupoMedico'] == '*')
													{
														$variablesG['cco'] 	= '*';
														$primerCco 		= false;
													}

													$errorGrabTar = $this->queryTarifaHomologacionExamen($variablesG, TRUE, TRUE);
													if(!$errorGrabTar)
													{
														$arrayQuerysTarifa[$examen][$conMatrix][$tarUnix][$cco][$especialidad][$grupoMedicoMatrix][$turno][$indice]['Grabado'] = true;
														continue;
													}
													else
													{
														// --> 	Como hubo error en la insercion, se intenta guardar nuevamente el registro pero con el cco especifico.
														$variablesG['cco'] 	= $cco;
														$errorGrabTar 		= $this->queryTarifaHomologacionExamen($variablesG, TRUE, TRUE);
														if(!$errorGrabTar)
														{
															$arrayQuerysTarifa[$examen][$conMatrix][$tarUnix][$cco][$especialidad][$grupoMedicoMatrix][$turno][0]['Grabado'] = true;
															continue;
														}
													}
												}
											}

											// --> 	Regla para grupos quirurgicos
											// --> 	Conceptos relacionados con la especialidad cardiologia vascular
											//		entonces diferencio la tarifa por esta especialidad (100131)
											if($variables['conceptoUnix'] == '2059' || $variables['conceptoUnix'] == '0079' || $variables['conceptoUnix'] == '2061' || $variables['conceptoUnix'] == '0102')
											{
												$variablesG						= array();
												$variablesG['wbasedato'] 		= $wbasedato;
												$variablesG['conexUnix'] 		= $conexUnix;
												$variablesG['conex'] 			= $conex;

												$variablesG['examen'] 			= $examen;
												$variablesG['tipo'] 			= $tipo;
												$variablesG['conMatrix'] 		= $conMatrix;
												$variablesG['tarUnix'] 			= $tarUnix;
												$variablesG['cco'] 				= $cco;
												$variablesG['especialidad'] 	= (($variables['conceptoUnix'] == '2059' || $variables['conceptoUnix'] == '2061') ? '100131' : '*');
												$variablesG['aplicaHono'] 		= '*';
												$variablesG['grupoMedico'] 		= $grupoMedicoMatrix;
												$variablesG['turnoMatrix'] 		= $turno;
												$variablesG['valorActual'] 		= $variables['valorActual'];
												$variablesG['fechaCambio'] 		= $variables['fechaCambio'];
												$variablesG['valorAnterior']	= $variables['valorAnterior'];
												$variablesG['conceptoUnix']		= $variables['conceptoUnix'];

												// --> 	Se intenta crear una con cco = *, para la mas general osea que tenga
												//		especialidad = '*' y grupoMedico = '*'.
												if($primerCco && $variablesG['especialidad'] == '*' && $variablesG['grupoMedico'] == '*')
												{
													$variablesG['cco'] 	= '*';
													$primerCco 		= false;
												}

												$errorGrabTar = $this->queryTarifaHomologacionExamen($variablesG, TRUE, TRUE);
												if(!$errorGrabTar)
												{
													$arrayQuerysTarifa[$examen][$conMatrix][$tarUnix][$cco][$especialidad][$grupoMedicoMatrix][$turno][$indice]['Grabado'] = true;
													continue;
												}
												else
												{
													// --> 	Como hubo error en la insercion, se intenta guardar nuevamente el registro pero con el cco especifico.
													$variablesG['cco'] 	= $cco;
													$errorGrabTar 		= $this->queryTarifaHomologacionExamen($variablesG, TRUE, TRUE);
													if(!$errorGrabTar)
													{
														$arrayQuerysTarifa[$examen][$conMatrix][$tarUnix][$cco][$especialidad][$grupoMedicoMatrix][$turno][0]['Grabado'] = true;
														continue;
													}
												}
											}
											//----------------------------------------------------------
											// --> Fin aplicar reglas para los duplicados
											//----------------------------------------------------------
										}
									}
									// --> Tarifa unica para grabar
									else
									{
										if($ccoAsterisco)
										{
											$nuevoCco 		= '*';
											$ccoAsterisco 	= false;
										}
										else
											$nuevoCco 		= $cco;

										$variables 	= $array7[0]['valores'];

										$variablesG						= array();
										$variablesG['wbasedato'] 		= $wbasedato;
										$variablesG['conexUnix'] 		= $conexUnix;
										$variablesG['conex'] 			= $conex;

										$variablesG['examen'] 			= $examen;
										$variablesG['conMatrix'] 		= $conMatrix;
										$variablesG['tarUnix'] 			= $tarUnix;
										$variablesG['cco'] 				= $nuevoCco;
										$variablesG['especialidad'] 	= $especialidad;
										$variablesG['aplicaHono'] 		= '*';
										$variablesG['grupoMedico'] 		= $grupoMedicoMatrix;
										$variablesG['turnoMatrix'] 		= $turno;
										$variablesG['valorActual'] 		= $variables['valorActual'];
										$variablesG['fechaCambio'] 		= $variables['fechaCambio'];
										$variablesG['valorAnterior']	= $variables['valorAnterior'];
										$variablesG['conceptoUnix']		= $variables['conceptoUnix'];

										$errorGrabTar = $this->queryTarifaHomologacionExamen($variablesG, TRUE, TRUE);
										if(!$errorGrabTar)
											$arrayQuerysTarifa[$examen][$conMatrix][$tarUnix][$cco][$especialidad][$grupoMedicoMatrix][$turno][0]['Grabado'] = true;
									}
								}
							}
						}
					}
				}
			}
		}

		// --> Recorrer nuevamente el array para saber cuales tarifas no se grabaron
		foreach($arrayQuerysTarifa as $examen => $array1)
		{
			foreach($array1 as $conMatrix => $array2)
			{
				foreach($array2 as $tarUnix => $array3)
				{
					foreach($array3 as $cco => $array4)
					{
						foreach($array4 as $especialidad => $array5)
						{
							foreach($array5 as $grupoMedicoMatrix => $array6)
							{
								foreach($array6 as $turno => $array7)
								{
									foreach($array7 as $indice => $variables)
									{
										if(!$variables['Grabado'])
										{
											$variables = $variables['valores'];
											// --> Guardar log de incosistencias
											$log.= PHP_EOL.''.$examen.' --> Concepto Matrix:'.$conMatrix.' --> Concepto Unix:'.$variables['conceptoUnix'].' --> Tarifa:'.$tarUnix.' --> Centro Costos:'.$cco.' --> Valor:'.$variables['valorActual'].' --> [ HOMOLOG MATRIX -> Especial:'.$variables['especialidadMatrix'].' -> GrupoMed:'.$variables['grupoMedicoMatrix'].' -> Turno:'.$variables['turnoMatrix'].' -> ccoMatrix:'.$variables['ccoMatrix'].']';
										}
									}
								}
							}
						}
					}
				}
			}
		}

		// --> Guardar log de incosistencias
		//$tipoEscritura = 'a+';
		$tipoEscritura = ((date('d') == '01') ? 'w+' : 'a+');

		$archivo	= fopen("logIncosistenciasCreacionTarifas.txt", $tipoEscritura) or die("Problemas en la creacion del archivo logIncosistenciasCreacionTarifas");
		$log		= PHP_EOL.PHP_EOL.'INICIO MIGRACION DE TARIFAS UVR-GRUPOS-HABITACIONES:'.date( "Y-m-d" )."  ".$horaIniEjec.' FIN MIGRACION:'.date( "Y-m-d" )."  ".date( "H:i:s" ).PHP_EOL.$log;
		fputs($archivo, $log);
		fclose($archivo);
		liberarConexionOdbc( $conexUnix );
		odbc_close_all();
	}
//---------------------------------------------------------------
//	--> FIN, KRONS PERTENECIENTES AL PROYECTO DE FACTURACIÓN
//---------------------------------------------------------------

	/**********************************************************************
	 * Maestro de municipios desde unix
	 **********************************************************************/
	function maestroEntidadesSoat(){

		$wbasedato = "cliame";

		$this->conexionOdbc( 'admisiones' );


		if( $this->conex_u ){

			$sql = "SELECT ctedetpar, ctedetdes,empcod
					  FROM SICTEDET,INEMP
					 WHERE ctedetcod='ASEGUR'
					   AND ctedetdes=empnom
					";

			$res = odbc_do( $this->conex_u, $sql );

			if( $res ){
				$arr_datos_unix = array(); //Arreglo con la informacion del query de unix
				while( odbc_fetch_row($res) )
				{
					$dato_aux = "";
					$dato_aux['Asecod'] = trim( odbc_result($res,1) );
					$dato_aux['Asedes'] = trim( odbc_result($res,2) );
					$dato_aux['Asecoe'] = trim( odbc_result($res,3) );
					array_push($arr_datos_unix, $dato_aux);
				}

				$cadena_codigos = ""; //cadena separada con comas con los codigos de las aseguradoras
				foreach( $arr_datos_unix as $poss=>$datoss ){
					$cadena_codigos.= $datoss['Asecod'].",";
				}
				if( $cadena_codigos != "" ){
					$cadena_codigos = substr($cadena_codigos, 0, -1);

					$codigos_matrix = array(); //Codigos de las aseguradoras que tambien estan en matrix
					$sql_193 = "SELECT Asecod
							  FROM ".$wbasedato."_000193
							 WHERE Asecod IN (".$cadena_codigos.")
							";
					$res_193 = mysql_query( $sql_193, $conex );
					$num = mysql_num_rows( $res_193 );
					if( $num > 0 ){
						while( $rows = mysql_fetch_array( $res_193 ) ){
							array_push( $codigos_matrix, $rows[0] );
						}
					}

					foreach( $arr_datos_unix as $pos=>$datos ){
						if( in_array( $datos['Asecod'], $codigos_matrix ) == false ){ //Si la aseguradora de UNIX no esta en las de matrix, se inserta
							$sqlInsert = "INSERT INTO ".$wbasedato."_000193(    Medico   ,   Fecha_data         ,      Hora_data       ,           Asecod      ,        Asedes         ,    Aseest  ,    		Asecoe   		,  Seguridad    ) "
										."			      VALUES ( '".$wbasedato."' 	 , '".date( "Y-m-d" )."', '".date( "H:i:s" )."', '".$datos['Asecod']."', '".$datos['Asedes']."', 	'on'	, 	'".$datos['Asecoe']."'  ,	'C-root' ) ";

							$resInsert = mysql_query( $sqlInsert, $this->conex ) or die( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() );
						}
					}
				}
			}
			else{
				echo "Error en el query $sql";
			}
		}
		else{
			//echo "NO SE CONECTO A UNIX";
		}
		//2014-12-29
		liberarConexionOdbc( $this->conex_u );
		odbc_close_all();

	}

	function queries(){

		$this->conexionOdbc( 'admisiones' );
		$this->actualizarMedicamentos();


	}

	/**********************************************************************
	 * Maestro de diagnósticos del CIE10
	 **********************************************************************/
	function maestroDiagnosticosCie10(){
        $wbasedato = $this->consultarAliasPorAplicacion( 'facturacion' );
        $hoy       = date( "Y-m-d" );
        $hora      = date( "H:i:s" );
        $this->conexionOdbc( 'admisiones' );

        $sql = "SELECT
                    *
                FROM
                    root_000011
                WHERE
                    fecha_data = '".date( "Y-m-d" )."'
                    AND id = 1
                ";

        $res = mysql_query( $sql, $this->conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
        $num = mysql_num_rows( $res );

        if( $num == 0 ){

            if( $this->conex_u ){

                $sql = "SELECT diacod, dianom, diagdx, gdxnom, diasec, secdes, diasub, subnom, diaedi, diaeds, diasex, diaact
                          FROM india, ingdx, insec, OUTER insub
						 WHERE diacie = 'C10'
						   AND gdxcod = diagdx
						   AND seccod = diasec
						   AND subcod = diasub
						   into temp tmpsub ";

				$res = odbc_do( $this->conex_u, $sql );

                $sql = "SELECT diacod as Codigo, dianom as descripcion, diagdx as cod_cap, gdxnom as Capitulo, diasec as Cod_cat, secdes as Categoria, diasub as Cod_subcat,
                               subnom as Subcategoria, diaedi as Edad_i, diaeds as Edad_s, diasex as Sexo, diaact as activo
                          FROM india, ingdx, insec, insub
                         WHERE diacie = 'C10'
                           AND gdxcod = diagdx
                           AND seccod = diasec
                           AND subcod = diasub
                      	 UNION ALL
                      	 SELECT diacod as Codigo, dianom as descripcion, diagdx as cod_cap, gdxnom as Capitulo, diasec as Cod_cat, secdes as Categoria, diasub as Cod_subcat,
                                '.' as Subcategoria, diaedi as Edad_i, diaeds as Edad_s, diasex as Sexo, diaact as activo
						   FROM tmpsub
						  WHERE subnom is null";

                $res = odbc_do( $this->conex_u, $sql );

                if( $res ){

                    $i = 0;
                    while( odbc_fetch_row($res) )
                    {
                        if( $i == 0 ){
                            $sqlTruncate = "TRUNCATE TABLE  root_000011";

                            $resTruncate = mysql_query( $sqlTruncate, $this->conex ) or die( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() );
                        }
						$edad_i = odbc_result($res,9);
						$edad_s = odbc_result($res,10);

						//Traduzco la edad inferior en años
						switch($edad_i[0]){

							case 0: //Edad minima sin restricción
							case 1: //horas
								$edad_i = 0;
							break;

							case 2: //Edad en días
								$edad_i = substr( $edad_i, 1, 2 )/365;
							break;

							case 3:
								$edad_i = substr( $edad_i, 1, 2 )/12;
							break;

							case 4:
								$edad_i = substr( $edad_i, 1, 2 );
							break;

							default: break;
						}

						//Traduzco la edad superior en años
						switch($edad_s[0]){

							case 0: //Edad maxima sin restricción
							case 1: //horas
								$edad_s = 0;
							break;

							case 2: //Edad en días
								$edad_s = substr( $edad_s, 1, 2 )/365;
							break;

							case 3:
								$edad_s = substr( $edad_s, 1, 2 )/12;
							break;

							case 4:
								$edad_s = substr( $edad_s, 1, 2 );
							break;

							case 5:
								$edad_s = 120;
							break;

							default: break;
						}

						$estado = odbc_result($res,12);
						$subcat = odbc_result($res,8);
						( $estado == "S" ) ? $estado = "on" : $estado  = "off";
						( trim($subcat) == "." ) ? $subcat = ""   : $subcat  = $subcat;

                        $sqlInsert = "INSERT INTO root_000011(    Medico   ,   Fecha_data,   Hora_data,              Codigo,                                descripcion,                   cod_cap,                          Capitulo,                             Cod_cat,                           Categoria,                 Cod_subcat,          Subcategoria,          Edad_i,       Edad_s,                        Sexo,         estado,     Seguridad ) "
                                    ."                  VALUES ( 'root',       '".$hoy."',  '".$hora."', '".trim( odbc_result($res,1) )."', '".trim( odbc_result($res,2) )."' , '".trim( odbc_result($res,3) )."', '".trim( odbc_result($res,4) )."', '".trim( odbc_result($res,5) )."', '".trim( odbc_result($res,6) )."', '".trim( odbc_result($res,7) )."', '".$subcat."', '".$edad_i."', '".$edad_s."', '".trim( odbc_result($res,11) )."', '".$estado."', 'C-root' ) ";

                        $resInsert = mysql_query( $sqlInsert, $this->conex ) or die( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() );

                        if( mysql_affected_rows() > 0 ){
                            // echo "<br>Inserto: ".trim( odbc_result($res,1) )." - ".trim( odbc_result($res,2) );
                        }
                        else{
                            // echo "<br>No inserto: ".trim( odbc_result($res,1) )." - ".trim( odbc_result($res,2) );
                        }

                        $i++;
                    }
                }
                else{
                    echo "Error en el query $sql";
                }
            }
            else{
                echo "NO SE CONECTO A UNIX";
            }
        }
		//2014-12-29
		liberarConexionOdbc( $this->conex_u );
		odbc_close_all();
    }


    /**********************************************************************
	 * Pacientes rechazados por mora
	 **********************************************************************/
	function migrarPacientesRechazados(){

        $wbasedato = $this->consultarAliasPorAplicacion( 'facturacion' );
        $hoy       = date( "Y-m-d" );
        $hora      = date( "H:i:s" );
        $wbasedato = "cliame";
        $this->conexionOdbc( 'admisiones' );

        $sql = "SELECT
                    *
                FROM
                    {$wbasedato}_000236
                WHERE
                    fecha_data = '".date( "Y-m-d" )."'
                    AND id = 1
                ";

        $res = mysql_query( $sql, $this->conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
        $num = mysql_num_rows( $res );

        if( $num == 0 ){

            if( $this->conex_u ){
				$campos_nulos = "Clrdir, Clrtel ";
				//$defecto_nulo = " '','','',datetime('0000-00-00 00:00:00') year to second  ";
				//			1		2		3	  4		  5		6	   7		8	 9		10	   11	  12
				$campos = "Clrtid,Clrced,Clrnom,Clrdir,Clrtel,Clrdes,Clrfec,Clrest,Clruad,Clrfad";
				$tablas = "inclr";
				$where = "";
				$sql = $this->construirQueryUnix( $tablas,$campos_nulos,$campos,$where );
                $res = odbc_do( $this->conex_u, $sql );

                if( $res ){

                    $i = 0;
                    while( odbc_fetch_row($res) )
                    {
                        if( $i == 0 ){
                            $sqlTruncate = "TRUNCATE TABLE  {$wbasedato}_000236";

                            $resTruncate = mysql_query( $sqlTruncate, $this->conex ) or die( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() );
                        }
						$estado = odbc_result($res,8);
						( $estado == "S" ) ? $estado = "on" : $estado  = "off";

                        $sqlInsert = "INSERT INTO {$wbasedato}_000236(    Medico   ,   Fecha_data,   Hora_data,                   Clrtid,                       Clrced,                        Clrnom,                             Clrdir,                                  Clrtel,                             Clrdes,                               Clrfec,              Clrest,                  Clruad,                               Clrfad,             Seguridad ) "
                                    ."                  VALUES ( '{$wbasedato}',       '".$hoy."',  '".$hora."', '".trim( odbc_result($res,1) )."', '".trim( odbc_result($res,2) )."' , '".trim( odbc_result($res,3) )."', '".trim( odbc_result($res,4) )."', '".trim( odbc_result($res,5) )."', '".trim( odbc_result($res,6) )."', '".trim( odbc_result($res,7) )."', '".$estado."', '".trim( odbc_result($res,9) )."', '".trim( odbc_result($res,10) )."',  'C-{$wbasedato}' ) ";

                        $resInsert = mysql_query( $sqlInsert, $this->conex ) or die( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() );

                        if( mysql_affected_rows() > 0 ){
                            // echo "<br>Inserto: ".trim( odbc_result($res,1) )." - ".trim( odbc_result($res,2) );
                        }
                        else{
                            // echo "<br>No inserto: ".trim( odbc_result($res,1) )." - ".trim( odbc_result($res,2) );
                        }

                        $i++;
                    }
                }
                else{
                    echo "Error en el query $sql";
                }

                $campos_nulos = " Clrdetobs ";
				//$defecto_nulo = " '','','',datetime('0000-00-00 00:00:00') year to second  ";
				//			1		2		3	  4		  5		6	   7		8	 9		10	   11	  12
				$campos = "Clrdettid,Clrdetced,Clrdetfue,Clrdetfac,Clrdetffa,Clrdetobs";
				$tablas = "inclrdet";
				$where = "";
				$sql = $this->construirQueryUnix( $tablas,$campos_nulos,$campos,$where );
                $res = odbc_do( $this->conex_u, $sql );

                if( $res ){

                    $i = 0;
                    while( odbc_fetch_row($res) )
                    {
                        if( $i == 0 ){
                            $sqlTruncate = "TRUNCATE TABLE  {$wbasedato}_000237";

                            $resTruncate = mysql_query( $sqlTruncate, $this->conex ) or die( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() );
                        }

                        $sqlInsert = "INSERT INTO {$wbasedato}_000237(    Medico   ,   Fecha_data,   Hora_data,               Clrdti,                           Clrdce,                       Clrdfu,                                         Clrdfa,                         Clrdff,                        Clrdob,                    Seguridad ) "
                                    ."                  VALUES ( '{$wbasedato}',       '".$hoy."',  '".$hora."', '".trim( odbc_result($res,1) )."', '".trim( odbc_result($res,2) )."' , '".trim( odbc_result($res,3) )."', '".trim( odbc_result($res,4) )."', '".trim( odbc_result($res,5) )."', '".trim( odbc_result($res,6) )."',  'C-{$wbasedato}' ) ";

                        $resInsert = mysql_query( $sqlInsert, $this->conex ) or die( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() );

                        if( mysql_affected_rows() > 0 ){
                            // echo "<br>Inserto: ".trim( odbc_result($res,1) )." - ".trim( odbc_result($res,2) );
                        }
                        else{
                            // echo "<br>No inserto: ".trim( odbc_result($res,1) )." - ".trim( odbc_result($res,2) );
                        }

                        $i++;
                    }
                }
                else{
                    echo "Error en el query $sql";
                }
            }
            else{
                echo "NO SE CONECTO A UNIX";
            }
        }
		//2014-12-29
		liberarConexionOdbc( $this->conex_u );
		odbc_close_all();
    }

	/**********************************************************************
	* Actualizar medicamentos y materiales Unix segun reglas de Matrix ( segun Manuales de cirugia)
	**********************************************************************/
	function actualizarMedicamentos()
	{
		global $wemp_pmla;
		$wbasedato 	= $this->consultarAliasPorAplicacion( 'facturacion' );
		$conexUnix 	= $this->conexionOdbc( 'facturacion' );
		$conex 		= $this->conex;



		$sql = "SELECT Tcardoi,Tcarlin,Tcarfac , id
				  FROM ".$wbasedato."_000106
				 WHERE  Tcardoi IS NOT NULL
				   AND  Tcaraun !='on' ";


        $res = mysql_query( $sql, $conex  ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
        $num = mysql_num_rows( $res );

		while($row = mysql_fetch_array($res))
		{


			$wbasedato_mov = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");


			$sql1 = "SELECT  Fdeubi
					  FROM ".$wbasedato_mov."_000003
					 WHERE  Fdenum = '".$row['Tcardoi']."'
				 	   AND  Fdelin = '".$row['Tcarlin']."' ";


			$res1 = mysql_query( $sql1, $conex  ) or die( mysql_errno()." - Error en el query $sql1 - ".mysql_error() );

			$estado ='';
			if($row1 = mysql_fetch_array($res1))
			{
				$estado = $row1['Fdeubi'];
			}

			if ($estado =='UP' || $estado =='US')
			{


				$sql2 = "   SELECT  Fenfue
							  FROM  ".$wbasedato_mov."_000002
							 WHERE  Fennum  = '".$row['Tcardoi']."'";


				$res2 = mysql_query( $sql2, $conex  ) or die( mysql_errno()." - Error en el query $sql1 - ".mysql_error() );


				$fuente ='';
				if($row2 = mysql_fetch_array($res2))
				{
					$fuente = $row2['Fenfue'];
				}


				if( $this->conex_u ){

					// --> Obtener los terceros desde unix de la tabla TETERTIP
					$sqlu = "SELECT drodocdoc
							  FROM ITDRODOC
							 WHERE drodocnum  = '".$row['Tcardoi']."'
							   AND drodocfue  = '".$fuente."'
							";
					$resu = odbc_do( $this->conex_u, $sqlu );


					if( $resu )
					{
						$i = 0;
						while( odbc_fetch_row($resu) )
						{
							$sqlupdate = " UPDATE FACARDET
											  SET cardetfac = '".$row['Tcarfac']."'
											WHERE cardetfue = '".$fuente."'
											  AND cardetdoc = '".odbc_result($resu,1)."'
											  AND cardetlin = '".$row['Tcarlin']."'
							";


							odbc_do( $this->conex_u, $sqlupdate );


							$sqlupdate2 = " UPDATE IVDRODET
											  SET drodetfac = '".$row['Tcarfac']."'
											WHERE drodetfue = '".$fuente."'
											  AND drodetdoc = '".odbc_result($resu,1)."'
											  AND drodetite = '".$row['Tcarlin']."'
							";


							odbc_do( $this->conex_u, $sqlupdate2 );




							$sql3 = "   UPDATE ".$wbasedato."_000106
										   SET Tcaraun = 'on'
										 WHERE  id = '".$row['id']."'";


							mysql_query( $sql3, $conex  ) or die( mysql_errno()." - Error en el query $sql3 - ".mysql_error() );

						}

					}

				}
			}

		}

		//2014-12-29
		liberarConexionOdbc( $conexUnix );
		odbc_close_all();
	}
	//----------------------------------------------------------------------------------------------
	//	CREAR PAQUETES EN UNIX
	// 	Responsable : 			Jerson trujillo.
	//	Fecha de creacion:		2015-04-10.
	//----------------------------------------------------------------------------------------------
	function crearPaquetesEnUnix()
	{
		$wbasedato 			= $this->consultarAliasPorAplicacion( 'facturacion' );
		$conexUnix 			= $this->conexionOdbc( 'facturacion' );
		$conex 				= $this->conex;
		$paqUnix			= array();
		$paqMatrix			= array();
		$conceptoPaqUnix	= $this->consultarAliasPorAplicacion('conceptoPaqueteUnix');

		// --> Obtener los paquetes que hay en unix que hayan sido insertados desde matrix
		$sqlPaqUnix  = "SELECT paqcod
						  FROM FAPAQ
						 WHERE paquad = 'MATRIX'
		";
		$resPaqUnix = odbc_do($conexUnix, $sqlPaqUnix);
		while(odbc_fetch_row($resPaqUnix))
			$paqUnix[trim(odbc_result($resPaqUnix,1))] = false;

		// --> Obtener los paquetes que hay en matrix
		$sqlPaqMatrix = "SELECT Paqcod, Paqnom, Paqdetpro
						   FROM ".$wbasedato."_000113, ".$wbasedato."_000114
						  WHERE Paqest 		= 'on'
						    AND Paqcod 		= Paqdetcod
							AND Paqdetcon	IN('0072', '0076', '0024', '0023')
							AND Paqdetgen 	= 'on'
							AND Paqdetpai	!= 'on'
							AND Paqdetest 	= 'on'
					   GROUP BY Paqcod, Paqdetpro
		";
		$resPaqMatrix = mysql_query($sqlPaqMatrix, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlPaqMatrix):</b><br>".mysql_error());
		while($rowPaqMatrix = mysql_fetch_array($resPaqMatrix))
		{
			$rowPaqMatrix['Paqcod'] = trim($rowPaqMatrix['Paqcod']);

			// --> Actualizar el paquete en unix
			if(array_key_exists($rowPaqMatrix['Paqcod'], $paqUnix))
			{
				$paqUnix[$rowPaqMatrix['Paqcod']] = true;

				$updatePaq = "UPDATE FAPAQ
								 SET paqoms = '".$rowPaqMatrix['Paqdetpro']."',
									 paqnom = '".$rowPaqMatrix['Paqnom']."',
									 paqcon = '".$conceptoPaqUnix."',
									 paqcpa = '".$conceptoPaqUnix."',
									 paqpro = '".$rowPaqMatrix['Paqdetpro']."',
								     paqact = 'S',
									 paqumo = 'MATRIX',
									 paqfmo = '".date("Y-m-d H:i:s")."'
							   WHERE paqcod = '".$rowPaqMatrix['Paqcod']."'
				";
				@odbc_do($conexUnix, $updatePaq);
			}
			// --> Insertar el paquete en unix
			else
			{
				$insertPaq = "INSERT INTO FAPAQ (paqcod, 						paqoms, 							paqnom, 						paqcon, 				paqcpa, 				paqpro, 							paqact, paquad, 	paqfad)
										  VALUES('".$rowPaqMatrix['Paqcod']."',	'".$rowPaqMatrix['Paqdetpro']."',	'".$rowPaqMatrix['Paqnom']."',	'".$conceptoPaqUnix."',	'".$conceptoPaqUnix."', '".$rowPaqMatrix['Paqdetpro']."',	'S',	'MATRIX',	'".date("Y-m-d H:i:s")."')";
				@odbc_do($conexUnix, $insertPaq);
			}
		}

		// --> Inactivar los paquetes de unix que no estan matrix
		foreach($paqUnix as $codPaq => $actualizado)
		{
			if(!$actualizado)
			{
				$updateInact = "UPDATE FAPAQ
								   SET paqact = 'N',
									   paqumo = 'MATRIX',
									   paqfmo = '".date("Y-m-d H:i:s")."'
							     WHERE paqcod = '".$codPaq."'
				";
				@odbc_do($conexUnix, $updateInact);
			}
		}
	}

	// Función que conculta los  procedimientos y examenes existentes en unix y los registra en la tabla 000017
	// de historia clínica de la empresa actual y en la tabla root_000012 que es el maestro de códigos CUPS
	function maestroExamenes()
	{
		global $wemp_pmla;
		$wbasedatohce 		= $this->consultarAliasPorAplicacion('hce');
		$conex_o 			= $this->conexionOdbc('facturacion');
		$conex 				= $this->conex;

		// Se consultan los datos de  procedimientos y exámenes en Unix
		$query = " SELECT cupcod, cupdes, cupact, cupria "
				."   FROM incup ";
		$err_o = odbc_do($conex_o,$query);
		$campos= odbc_num_fields($err_o);

		// Se inicializan los contadores de operaciones sql
		$winsert=0;
		$wupdate=0;
		$winsertcups=0;
		$wupdatecups=0;

		// Se incializan fecha y hora
		$fecha = date("Y-m-d");
		$hora = (string)date("H:i:s");

		// Se recorre los registros encontrados de  procedimientos y exámenes en unix
		while (odbc_fetch_row($err_o))
		{
			$row=array();
			for($i=1;$i<=$campos;$i++)
			{
			   $row[$i-1]=odbc_result($err_o,$i);
			}

			//Descripción del procedimiento o examen
			$wdesc = $row[1];


			//////////////////////////////////////////////////////////////////////////
			// Se da formato al estado NoPos para la grabación en Matrix
			$wnopos="off";

			//Verifico si el registro es NoPos con NP  = No Pos
			if (substr_count($row[1], '(NP)') > 0)
			{
				$wnopos="on";
				//Si en la descripcion tiene la sigla 'NP', la quito
				$wdesc = str_replace('(NP)', '', $row[1]);
			}

			//Verifico si el registro es NoPos con NPC = No Pos, No Cups
			if (substr_count($row[1], '(NPC)') > 0)
			{
				$wnopos="on";
				//Si en la descripcion tiene la sigla 'NPC', la quito
				$wdesc = str_replace('(NPC)', '', $row[1]);
			}
			//////////////////////////////////////////////////////////////////////////

			//Estado
			if ($row[2]=="S")
				$westado = "on";
			else
				$westado = "off";


			//////////////////////////////////////////////////////////////////////////
			// Actualización del maestro de procedimientos y exámenes en historia clínica

			//Busco si el codigo existe en el maestro de  procedimientos y exámenes de historia clínica
			$query = "SELECT Codigo, Descripcion, Estado, Clase, NoPos "
					."  FROM {$wbasedatohce}_000017 "
					." WHERE Codigo='".$row[0]."'";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);

			//Solo inserta los codigos que no existan en la tabla 000017
			if ($num == 0)
			{
				// Actualizo el maestro de procedimientos y examenes
				$q = " INSERT {$wbasedatohce}_000017 (medico,fecha_data,hora_data,Codigo,Descripcion,Servicio,Tipoestudio,Anatomia,Codcups,Protocolo,Estado,Clase,NoPos,Seguridad ) "
					." VALUES ('{$wbasedatohce}' ,'".$fecha."' ,'".$hora."' ,'".$row[0]."','". mysql_escape_string( trim($wdesc))."' , '', '', '', '".$row[0]."', '', '".$westado."', '".$row[3]."', '".$wnopos."', 'C-{$wbasedatohce}') ";
				$err1 = mysql_query($q,$conex) or die("ERROR INSERTANDO EL CODIGO ".$row[0]." EN LA TABLA {$wbasedatohce}_000017 :  ".mysql_errno()." : - $q - : ".mysql_error());

				//echo $q."<br>";

				$winsert++;
			}
			else
			{
				$rowexa = mysql_fetch_array($err);
				if($rowexa['Descripcion'] != trim($wdesc) || $rowexa['Estado'] != $westado || $rowexa['Clase'] != $row[3] || $rowexa['NoPos'] != $wnopos)
				{
					// Actualizo el maestro de procedimientos y examenes
					$q = " UPDATE {$wbasedatohce}_000017 "
						."    SET Descripcion = '".mysql_escape_string( trim($wdesc) )."',  "
						."        Estado      = '".$westado."', "
						."        Clase       = '".$row[3]."',  "
						."        NoPos       = '".$wnopos."',   "
						."        Fecha_data  = '".$fecha."',   "
						."        Hora_data   = '".$hora."'  "
						."  WHERE Codigo = '".$row[0]."'";
					$err1 = mysql_query($q,$conex) or die("ERROR ACTUALIZANDO EL CODIGO ".$row[0]." DE LA TABLA {$wbasedatohce}_000017 : ".mysql_errno().":".mysql_error());

					//echo $q."<br>";

					$wupdate++;
				}
			}
			//////////////////////////////////////////////////////////////////////////



			//////////////////////////////////////////////////////////////////////////
			// Actualización del maestro CUPS de root					// 2013-09-04

			//Busco si el codigo existe en el maestro de CUPS
			$query = "SELECT Codigo, Nombre "
					."  FROM root_000012 "
					." WHERE Codigo='".$row[0]."'";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);

			//Solo inserta los codigos que no existan en root_000012
			if ($num == 0)
			{
				// Actualizo el maestro de CUPS
				$q = " INSERT root_000012 (medico,fecha_data,hora_data,Codigo,Nombre,Seguridad ) "
					." VALUES ('root' ,'".$fecha."' ,'".$hora."' ,'".$row[0]."','".mysql_escape_string(trim($wdesc))."', 'C-root') ";
				$err1 = mysql_query($q,$conex) or die("ERROR INSERTANDO EL CODIGO ".$row[0]." EN LA TABLA {$wbasedatohce}_000017 :  ".mysql_errno().":".mysql_error());

				//echo $q."<br>";

				$winsertcups++;
			}
			else
			{
				$rowmtx = mysql_fetch_array($err);
				if($rowmtx['Nombre'] != trim($wdesc))
				{
					// Actualizo el maestro de procedimientos y examenes
					$q = " UPDATE root_000012 "
						."    SET Nombre = '".mysql_escape_string(trim($wdesc))."',  "
						."        Fecha_data  = '".$fecha."',   "
						."        Hora_data   = '".$hora."'  "
						."  WHERE Codigo = '".$row[0]."'";
					$err1 = mysql_query($q,$conex) or die("ERROR ACTUALIZANDO EL CODIGO ".$row[0]." DE LA TABLA {$wbasedatohce}_000017 : ".mysql_errno().":".mysql_error());

					//echo $q."<br>";

					$wupdatecups++;
				}
			}
			//////////////////////////////////////////////////////////////////////////
		}
	}

	//----------------------------------------------------------------------------------------------
	//	IMPOTAR MAESTRO DE PROVEEDORES
	// 	Responsable				:		Eimer Castro.
	//	Fecha de creacion		:		2015-04-10.
	//----------------------------------------------------------------------------------------------
	function maestroProveedores(){

		$wbasedato = 'cliame';
		$conexUnix 	= $this->conexionOdbc( 'inventarios' );
		$conexUnixCuentas = $this->conexionOdbc( 'cuepag' );
		$conex = $this->conex;

		// echo "<br>ok!<br>";
		$sqlProMx  = "truncate table {$wbasedato}_000006";
		$resProMx = mysql_query($sqlProMx, $conex);

		if( $conexUnix ){
			//migrar proveedores 000006
			{
				// --> Obtener los proveedores de matrix
				$sqlProMx  = "SELECT Procod AS codigo
								  FROM {$wbasedato}_000006";
				$resProMx = mysql_query($sqlProMx, $conex);
				$arr_provMx = array();
				while($row = mysql_fetch_array($resProMx))
				{
					$arr_provMx[$row["codigo"]] = false;
				}

				/*$sql = "SELECT 	procod, pronom, pronit, prodir, protel,
									profax, promai, procon, proret, protip,
									proiva, progra, protim, proact, profad,
									prorut
							FROM 	cppro";*/

				$campos       = "procod, pronom, pronit, protel, promai, proret,
									protip, proiva, progra, protim, proact, prorut";
				$tablas       = "cppro";
				$where        = "";

				$campos_nulos = "pronit, protel, promai, prorut, proret, proiva";
				$defecto_nulo = "'','','','','',''";
				$sql          = $this->construirQueryUnix( $tablas,$campos_nulos,$campos,$where,$defecto_nulo);

				/*NOTAS:
					El query no permitió traer mas de 6 campos que pueden ser nulos, se quitaron de la migración
					prodir, procon, profax

					Ademas se quitó la columna profad porque tambien contiene nulos y no se permitió asignarle un valor por defecto
				*/

				$resUx = odbc_do( $conexUnix, $sql );

				if( $resUx ){
					$i = 0;
					while( odbc_fetch_row($resUx) )
					{
						$profyh = "0000-00-00 00:00:00";//odbc_result($resUx, "profad");
						$Fecha_data = date("Y-m-d"); //substr ($profyh, 0, 10);
						$Hora_data = date("H:i:s"); //substr ($profyh, -8);
						$pronit = trim(odbc_result($resUx, "pronit"));
						$codigo_provUx = trim(odbc_result($resUx, "procod"));

						$pronom = odbc_result($resUx, "pronom");
						$pronom = str_replace('"',"*",str_replace("'","*",$pronom));
						$protel = trim(odbc_result($resUx, "protel"));
						$prodir = '';//odbc_result($resUx, "prodir");
						$procon = '';//odbc_result($resUx, "procon");
						$profax = '';//odbc_result($resUx, "profax");
						$proeml = odbc_result($resUx, "promai");
						$protip = odbc_result($resUx, "protip");
						$profcr = $Fecha_data;
						$prorut = odbc_result($resUx, "prorut");
						$prorut = ($prorut == 'S') ? 'on': 'off';
						$protim = odbc_result($resUx, "protim");
						$protim = ($protim == 'S') ? 'on': 'off';
						$proatr = odbc_result($resUx, "proret");
						$proatr = ($proatr == 'S') ? 'on': 'off';
						$proriv = odbc_result($resUx, "proiva");
						if($proriv = 'S') {
							$proriv = 'S-REGIMEN SIMPLIFICADO';
						} elseif($proriv = 'N') {
							$proriv = 'N-NO APLICA';
						}elseif($proriv = 'C') {
							$proriv = 'C-REGIMEN DE IVA';
						}
						$proeir = odbc_result($resUx, "progra");
						$proeir = ($proeir == 'S') ? 'on': 'off';
						$proest = odbc_result($resUx, "proact");
						$proest = ($proest == 'S') ? 'on': 'off';

						if(array_key_exists($codigo_provUx, $arr_provMx))
						{
							$arr_provMx[$codigo_provUx] = true;
							$sqlProMxUpdt  = "	UPDATE {$wbasedato}_000006
													SET Medico = '{$wbasedato}',
														Fecha_data = '{$Fecha_data}',
														Hora_data = '{$Hora_data}',
														Pronit = '{$pronit}',
														Pronom = '{$pronom}',
														Protel = '{$protel}',
														Prodir = '{$prodir}',
														Procon = '{$procon}',
														Profax = '{$profax}',
														Proeml = '{$proeml}',
														Profcr = '{$profcr}',
														Protip = '{$protip}',
														Prorut = '{$prorut}',
														Protim = '{$protim}',
														Proatr = '{$proatr}',
														Proriv = '{$proriv}',
														Proeir = '{$proeir}',
														Proest = '{$proest}',
														Procod = '{$codigo_provUx}'
											WHERE 	Procod='{$codigo_provUx}'";
							$resProMxUpdt = mysql_query($sqlProMxUpdt, $conex);
						}
						else
						{
							$sqlProMxIns  = "	INSERT INTO {$wbasedato}_000006
														(Medico, Fecha_data, Hora_data, Pronit, Pronom, Protel,
															Prodir, Procon, Profax, Proeml, Profcr, Protip, Prorut, Protim,
															Proatr, Proriv, Proeir, Proest, Procod, Seguridad)
												VALUES	('{$wbasedato}', '{$Fecha_data}', '{$Hora_data}', '{$pronit}', '{$pronom}', '{$protel}', '{$prodir}',
															'{$procon}', '{$profax}', '{$proeml}', '{$profcr}', '{$protip}', '{$prorut}', '{$protim}',
															'{$proatr}', '{$proriv}', '{$proeir}', '{$proest}', '{$codigo_provUx}', 'C-{$wbasedato}')";
							if(mysql_query($sqlProMxIns, $conex))
							{
								$i++;
								//echo "-$i insert-";
							}
							else
							{
								//echo "-$i NO- ".mysql_errno().'-'.mysql_error().' - '.$sqlProMxIns;
							}
						}
					}
				}
				else{
					echo "Error en el query $sql";
				}
			}

			// migrar tipos 000031
			{
				// --> Obtener los proveedores de matrix
				$sqlTprMx  = "SELECT Tprcod AS codigo
								  FROM {$wbasedato}_000031";
				$resTprMx = mysql_query($sqlTprMx, $conex);
				$arr_tprMx = array();
				while($row = mysql_fetch_array($resTprMx))
				{
					$arr_tprMx[$row["codigo"]] = false;
				}

				/*$sql = "SELECT 	procod, pronom, pronit, prodir, protel,
									profax, promai, procon, proret, protip,
									proiva, progra, protim, proact, profad,
									prorut
							FROM 	cppro";*/

				$campos       = "tipcod, tipnom, tipcue, tiptmo, tipcua, tiptma,
									tiprub, tipact, tipuad, tipumo";
				$tablas       = "cptip";
				$where        = "";

				$campos_nulos = "tiprub, tipuad, tipumo";
				$defecto_nulo = "'','',''";
				$sqltpr       = $this->construirQueryUnix( $tablas,$campos_nulos,$campos,$where,$defecto_nulo);

				/*NOTAS:
					El query no permitió traer mas de 6 campos que pueden ser nulos, se quitaron de la migración
					prodir, procon, profax

					Ademas se quitó la columna profad porque tambien contiene nulos y no se permitió asignarle un valor por defecto
				*/

				$resUx = odbc_do( $conexUnix, $sqltpr );

				if( $resUx ){
					$i = 0;
					while( odbc_fetch_row($resUx) )
					{
						$Fecha_data = date("Y-m-d");
						$Hora_data = date("H:i:s");
						$codigo_tprUx = trim(odbc_result($resUx, "tipcod"));

						$tprdes = odbc_result($resUx, "tipnom");
						$tprdes = str_replace('"',"*",str_replace("'","*",$tprdes));
						$tprcue = trim(odbc_result($resUx, "tipcue"));
						$tprtmo = trim(odbc_result($resUx, "tiptmo"));
						$tprcua = trim(odbc_result($resUx, "tipcua"));
						$tprtma = trim(odbc_result($resUx, "tiptma"));
						$tprest = odbc_result($resUx, "tipact");
						$tprest = ($tprest == 'S') ? 'on': 'off';

						if(array_key_exists($codigo_tprUx, $arr_tprMx))
						{
							$arr_tprMx[$codigo_tprUx] = true;
							$sqlTprMxUpdt  = "	UPDATE {$wbasedato}_000031
													SET Medico = '{$wbasedato}',
														Fecha_data = '{$Fecha_data}',
														Hora_data = '{$Hora_data}',
														Tprdes = '{$tprdes}',
														Tprcue = '{$tprcue}',
														Tprtmo = '{$tprtmo}',
														Tprcua = '{$tprcua}',
														Tprtma = '{$tprtma}',
														Tprest = '{$tprest}',
														Tprcod = '{$codigo_tprUx}'
											WHERE 	Tprcod='{$codigo_tprUx}'";
							$resTprMxUpdt = mysql_query($sqlTprMxUpdt, $conex);
						}
						else
						{
							$sqlTprMxIns  = "	INSERT INTO {$wbasedato}_000031
														(Medico, Fecha_data, Hora_data, Tprcod, Tprdes, Tprcue,
															Tprtmo, Tprcua, Tprtma, Tprest, Seguridad)
												VALUES	('{$wbasedato}', '{$Fecha_data}', '{$Hora_data}', '{$codigo_tprUx}', '{$tprdes}', '{$tprcue}',
															'{$tprtmo}', '{$tprcua}', '{$tprtma}', '{$tprest}', 'C-{$wbasedato}')";

							if(mysql_query($sqlTprMxIns, $conex))
							{
								$i++;
								//echo "-$i insert-";
							}
							else
							{
								echo "-$i NO- ".mysql_errno().'-'.mysql_error().' - '.$sqlTprMxIns;
							}
						}
					}
				}
				else{
					echo "Error en el query $sqltpr";
				}
			}

		}
		else{
			echo "NO SE CONECTO A UNIX";
		}

		if( $conexUnixCuentas ){
			// migrar actividades económicas 000032
			{
				// --> Obtener los proveedores de matrix
				$sqlAceMx  = "SELECT Acecod AS codigo
								  FROM {$wbasedato}_000032";
				$resAceMx = mysql_query($sqlAceMx, $conex);
				$arr_aceMx = array();
				while($row = mysql_fetch_array($resAceMx))
				{
					$arr_aceMx[$row["codigo"]] = false;
				}

				$campos       = "actcod, actnom, actact";
				$tablas       = "cpact";
				$where        = "";

				$campos_nulos = "";
				$defecto_nulo = "";
				$sqlace       = $this->construirQueryUnix( $tablas,$campos_nulos,$campos,$where,$defecto_nulo);

				/*NOTAS:
					El query no permitió traer mas de 6 campos que pueden ser nulos, se quitaron de la migración
					prodir, procon, profax

					Ademas se quitó la columna profad porque tambien contiene nulos y no se permitió asignarle un valor por defecto
				*/

				$resUx = odbc_do( $conexUnixCuentas, $sqlace );

				if( $resUx ){
					$i = 0;
					while( odbc_fetch_row($resUx) )
					{
						$Fecha_data = date("Y-m-d");
						$Hora_data = date("H:i:s");
						$codigo_aceUx = trim(odbc_result($resUx, "actcod"));

						$acedes = odbc_result($resUx, "actnom");
						$acedes = str_replace('"',"*",str_replace("'","*",$acedes));
						$aceest = odbc_result($resUx, "actact");
						$aceest = ($aceest == 'S') ? 'on': 'off';

						if(array_key_exists($codigo_aceUx, $arr_aceMx))
						{
							$arr_aceMx[$codigo_aceUx] = true;
							$sqlAceMxUpdt  = "	UPDATE {$wbasedato}_000032
													SET Medico = '{$wbasedato}',
														Fecha_data = '{$Fecha_data}',
														Hora_data = '{$Hora_data}',
														Acedes = '{$acedes}',
														Aceest = '{$aceest}',
														Acecod = '{$codigo_aceUx}'
											WHERE 	Acecod='{$codigo_aceUx}'";
							$resAceMxUpdt = mysql_query($sqlAceMxUpdt, $conex);
						}
						else
						{
							$sqlAceMxIns  = "	INSERT INTO {$wbasedato}_000032
														(Medico, Fecha_data, Hora_data, Acecod, Acedes, Aceest, Seguridad)
												VALUES	('{$wbasedato}', '{$Fecha_data}', '{$Hora_data}', '{$codigo_aceUx}', '{$acedes}', '{$aceest}', 'C-{$wbasedato}')";

							if(mysql_query($sqlAceMxIns, $conex))
							{
								$i++;
								//echo "-$i insert-";
							}
							else
							{
								echo "-$i NO- ".mysql_errno().'-'.mysql_error().' - '.$sqlTprMxIns;
							}
						}
					}
				}
				else{
					echo "Error en el query $sqltpr";
				}
			}
		}
		else{
			echo "NO SE CONECTO A UNIX Cuentas por pagar";
		}

		liberarConexionOdbc( $conexUnix );
		liberarConexionOdbc( $conexUnixCuentas );
		odbc_close_all();
	}

	//Funcion que actualiza cargos grabados por el laboratorio en facardet  y los pasa a la tabla Labmatrix y consulta
	// 	Responsable : 			Felipe Alvarez.
	//	Fecha de creacion:		2015-10-10.
	function pasar_examenes_lab()
	{
		return;
		global $wemp_pmla;
		$conexUnix = odbc_connect('facturacion','informix','sco');
		// odbc_autocommit($conexUnix, FALSE);

		//--------------Arreglar
		// mirar si al agregar la condicion de cardefue='14' se aumenta el tiempo de respuesta
		$mes_actual = date("m");
		$anio_actual = date("Y");

		$fechaant = date( "Y-m-d",time()-3600*24 );
		$fechaactual = date( "Y-m-d");



		$wbasedato 			= $this->consultarAliasPorAplicacion( 'facturacion' );
		$conex 				= $this->conex;
		$wbasedato_mov 		= consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
		$wbasedatohce 		= $this->consultarAliasPorAplicacion('hce');


		// voy por los centros de costos de donde se van a pasar los examenes
		$querycco = "SELECT Ccocod
					   FROM ".$wbasedato_mov."_000011
					  WHERE ccoayu ='off'
					    AND ccoing ='on' ";

		$resval =  mysql_query( $querycco, $conex  ) or die( mysql_errno()." - Error en el query $querycco - ".mysql_error() );
		$cco = '';
		while($rowval = mysql_fetch_array($resval))
		{
			$cco = $cco.','.$rowval['Ccocod'];

		}
		$cco = substr($cco,1);


		//---------------------------------------------------------




		// $query =  "SELECT cardetfue,cardetdoc,cardetreg,cardetite,cardetcco,cardethis,cardetnum
					 // FROM FACARDET
					// WHERE cardetite = '0'
					  // AND cardetfue = '14'
					  // AND cardetori = 'LA'
					  // AND cardetanu = '0'
					  // AND cardetfec >= '".$fechaant."'";


		$query =  "SELECT cardetfue,cardetdoc,cardetreg,cardetite,cardetcco,cardethis,cardetnum
					 FROM FACARDET
					WHERE cardetfec >= '2016-02-02'
					  AND cardetfue = '14'
					  AND cardetite = '0'
					  AND cardetori = 'LA'
					  AND cardetanu = '0'
					  AND cardetvfa = 0
					  AND cardetfac = 'S' ";

					  // (UNIX_TIMESTAMP()-UNIX_TIMESTAMP(CONCAT(A.Fecha_data, ' ', A.Hora_data))) < 86400

					  // and fecha_data

					  // and ((a.fecha_data >= '2015-11-04')
					   // OR (a.fecha_data = '2015-11-04'
					  // AND A.HORA_DATA >= '10:00:00'
					  // )
					  // )
					   // AND cardetmes = '{$mes_actual}'
					  // AND cardetano = '{$anio_actual}'

		$err 	= odbc_do($conexUnix,$query);
		$campos	= odbc_num_fields($err);
		$i 		= 0;

		while(odbc_fetch_row($err))
		{
			if (odbc_result($err,1) =='14' && odbc_result($err,4)== '0' )
			{

				// $cco = odbc_result($err,5);
				$whis = odbc_result($err,6);
				$wing = odbc_result($err,7);


				//
				$sqlingreso = "SELECT *
								 FROM ".$wbasedatohce."_000022
								WHERE Mtrhis = '".$whis."'
								  AND Mtring = '".$wing."'
								  AND Mtrcci IN (".$cco.") ";

				$resval =  mysql_query( $sqlingreso, $conex  ) or die( mysql_errno()." - Error en el query $sqlingreso - ".mysql_error() );


				if($rowval = mysql_fetch_array($resval))
				{

					$sqlinsert = " INSERT INTO Labmatrix (labfue	,		labdoc 					,			labreg			,	labest)
										VALUES 			 ('14'		,'".odbc_result($err,2)."'	,	'".odbc_result($err,3)."'	, 	'P') ";

					odbc_do($conexUnix,$sqlinsert);
				}


				$sqlupdate = "UPDATE FACARDET
								 SET cardetite='1'
							   WHERE cardetreg= '".odbc_result($err,3)."'";

				odbc_do($conexUnix,$sqlupdate);


			}


		}


		// odbc_autocommit($conexUnix, FALSE);
		// estado P = pasado
		$query = "SELECT cardethis,cardetnum,cardetcon,cardetcod,cardettar,cardetfec,cardetfue,labreg,cardetvun
					FROM labmatrix , FACARDET
					WHERE labreg = cardetreg
					AND (labest ='P' OR labest ='NT' OR labest ='NP' ) ";

		$err 	= odbc_do($conexUnix,$query);
		$campos	= odbc_num_fields($err);
		$i 		= 0;



		while(odbc_fetch_row($err) )
		{
				$i++;

				$seguir = 'si';
				$activo = '';


				$concepto 		=  	odbc_result($err,3);
				$procedimiento 	=  	odbc_result($err,4);
				$tarifa 		=   odbc_result($err,5);
				$fecha 			=   odbc_result($err,6);
				$reg 			= 	odbc_result($err,8);
				$valor 			=	odbc_result($err,9);

				$info = array('tieneTarifa' => 'NT', 'queryTarifa' => '', 'mensaje' => '');

				if($valor*1 != 0)
				{
					$sqlTipLiq = "SELECT exaliq, exagex,exaact
									FROM INEXA
								   WHERE exacod = '".$procedimiento."'
									 AND exaliq != ''
									 AND exagex IS NOT NULL
								   UNION
								  SELECT exaliq, '',exaact
									FROM INEXA
								   WHERE exacod = '".$procedimiento."'
									 AND exaliq != ''
									 AND exagex IS NULL";


					// --> Obtener el tipo de liquidacion
					$tipoLiquidacion 	= '';
					$grupoQuirurgico 	= '';
					$resTipLiq 			= odbc_exec($conexUnix, $sqlTipLiq);
					if(odbc_fetch_row($resTipLiq))
					{
						$activo 			= trim(odbc_result($resTipLiq, 3));
						$tipoLiquidacion 	= trim(odbc_result($resTipLiq, 1));
						$grupoQuirurgico 	= trim(odbc_result($resTipLiq, 2));
					}
					else
					{
						$seguir = 'no';

					}

					if($seguir == 'si')
					{
						// --> Buscar la tarifa segun el tipo de liquidacion
						switch($tipoLiquidacion)
						{
							// --> Tipo de liquidacion por codigo
							case 'C':
							{
								$sqlTarifa = "SELECT COUNT(*) AS cantidad
													FROM INEXATAR
												   WHERE exatarexa = '".$procedimiento."'
													 AND (exatartar = '".$tarifa."' OR exatartar = '*')
													 AND exatartse = '*'
													 AND exatarcon = '".$concepto."'
													 AND exatartip = '*'";
								break;

							}
							// --> Tipo de liquidacion por grupo GQX
							case 'G':
							{
								$sqlTarifa = "SELECT COUNT(*) AS cantidad
												FROM INQUITAR
											   WHERE quitarqui = '".$grupoQuirurgico."'
												 AND (quitartar = '".$tarifa."' OR exatartar = '*')
												 AND quitartse = '*'
												 AND quitarcon = '".$concepto."'
												 AND quitartip = '*'";
								break;
							}
							// --> Tipo de liquidacion por UVR
							case 'U':
							{
								$sqlTarifa = "SELECT COUNT(*) AS cantidad
												FROM FAUNITAR
											   WHERE (unitartar = '".$tarifa."' OR exatartar = '*')
												 AND unitartse = '*'
												 AND unitarcon = '".$concepto."'";
								break;
							}
						}


						// --> Ejecutar query de la tarifa
						$info['queryTarifa'] = $sqlTarifa;
						$resTarifa = odbc_exec($conexUnix, $sqlTarifa);
						if(odbc_fetch_row($resTarifa))
						{
							if(odbc_result($resTarifa, 'cantidad') > 0)
								$info['tieneTarifa'] = "si";
						}
					}
					else
					{
						// No procedimiento
						$info['tieneTarifa'] = "NP";
						$activo ='no';
					}


					if ($info['tieneTarifa']!='si' )
					{

						$sqlupdate = "UPDATE Labmatrix
										 SET labest='".$info['tieneTarifa']."'
									   WHERE labreg= '".$reg."'";

						odbc_do($conexUnix,$sqlupdate);

					}
					else
					{

							$sqlupdate = "UPDATE Labmatrix
											 SET labest='R'
										   WHERE labreg= '".$reg."'";

							odbc_do($conexUnix,$sqlupdate);
					}
				}
				else
				{
					$sqlupdate = "UPDATE Labmatrix
									 SET labest='R'
								   WHERE labreg= '".$reg."'";

					odbc_do($conexUnix,$sqlupdate);

				}
		}
		odbc_close($conexUnix);
		odbc_close_all();

	}
	//----------------------------------------------------------------------------------------------
	//	IMPOTAR MAESTRO DE TOPES PARA EL SOAT
	// 	Responsable : 			Jerson trujillo.
	//	Fecha de creacion:		2016-01-06.
	//----------------------------------------------------------------------------------------------
	function maestroTopesSoat(){

		$wbasedato = $this->consultarAliasPorAplicacion( 'facturacion' );
		$this->conexionOdbc( 'facturacion' );

		// --> Verificar si para hoy ya se hizo la actualizacion
		$sql = "SELECT id
				  FROM ".$wbasedato."_000194
				 WHERE Fecha_data = '".date( "Y-m-d" )."'
				   AND id = 1 ";

		$res = mysql_query( $sql, $this->conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );

		if( $num == 0 ){

			if( $this->conex_u ){

				// --> Obtener registros de unix
				$sqlUn = "
				SELECT topfin, topffi, topmin, topfid, toptpr, toptse
				  FROM fatop ";

				$res = odbc_do( $this->conex_u, $sqlUn);

				if( $res ){
					$i = 0;
					while( odbc_fetch_row($res) )
					{
						// --> Vaciar la tabla
						if($i == 0){
							$sqlTruncate = "TRUNCATE TABLE  ".$wbasedato."_000194 ";
							$resTruncate = mysql_query( $sqlTruncate, $this->conex ) or die( mysql_errno()." - Error en el query $sqlTruncate - ".mysql_error() );
						}

						// --> Estado
						$estado = ((trim(odbc_result($res,'topfin')) <= date("Y-m-d") && trim(odbc_result($res,'topffi')) >= date("Y-m-d")) ? "on" : "off");

						// --> Insertar el registro en matrix
						$sqlInsert = "INSERT INTO ".$wbasedato."_000194
									( Medico   ,	Fecha_data,      		Hora_data,           	Topfin,									Topffi,									Topmin,									Topfid, 								Toptpr,									Toptse,									Topest,			Seguridad       )
							 VALUES ( '$wbasedato', '".date( "Y-m-d" )."', 	'".date( "H:i:s" )."', 	'".trim(odbc_result($res,'topfin'))."',	'".trim(odbc_result($res,'topffi'))."',	'".trim(odbc_result($res,'topmin'))."',	'".trim(odbc_result($res,'topfid'))."',	'".trim(odbc_result($res,'toptpr'))."',	'".trim(odbc_result($res,'toptse'))."',	'".$estado."',	'C-$wbasedato'  ) ";

						$resInsert = mysql_query( $sqlInsert, $this->conex );

						$i++;
					}
				}
				else{
					echo "Error en el query $sqlUn";
				}
			}
			else{
				echo "NO SE CONECTO A UNIX";
			}
		}
		liberarConexionOdbc( $this->conex_u );
		odbc_close_all();
	}

    function nomina(){

 		global $conex;
		global $wemp_pmla;

        $wbasedato = consultarAliasPorAplicacion ($conex, $wemp_pmla, 'talhuma');

        // ***************   Consultar Datos Correo electronico nomina

        $q = ' SELECT * From root_000051 where Detapl="Emailnomina" ';
        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $row = mysql_fetch_assoc($res);

        list($correoori, $password, $nomcorreo) = split('[|]', $row['Detval']);

        // ***************   Consultar Textos del Formato 1 (asunto y parrafo final)

        $q = " SELECT * From root_000051 where Detapl='formatonomina1' ";
        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $row = mysql_fetch_assoc($res);


        list($asunto1, $asunto2, $asunto3, $asunto4, $nomorigen, $final) = split('[|]', $row['Detval']);
        $parrafo1 = $row['Detdes'];


        // ***************   Consultar Textos del Formato 2 (titulos y contenido)

        $q = " SELECT * From root_000051 where Detapl='formatonomina2' ";
        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $row = mysql_fetch_assoc($res);

        list($titulo1, $titulo2, $titulo3, $titulo4) = split('[|]', $row['Detval']);
        $parrafo2 = $row['Detdes'];


        // ***************   Consultar Los empleados con Evaluaciones pendientes

        $fechaactual  =  getdate();
        $wmes         =  $fechaactual[mon];
        $wano         =  $fechaactual[year];
        $diasem       =  $fechaactual[wday];
        $wmes2        =  $wmes+1;
        $fecha        =  date("Y-m-d");
        $hora         =  date("H:i:s");
        $Nomdestino   = '';

        // ************  Consultar tipos de contratos a excluir, ejm practicantes que no aplican al proceso

        $contratos = '';

        $q   = " SELECT Detval From root_000051 where Detapl='contratosorganigrama' ";
        $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);

        if ($num > 0)
        {
            $rowper    = mysql_fetch_assoc($res);
            $contratos = $rowper['Detval'];
        }

        // **************    Consultar Empleados programados para la evaluación y en la fecha de realización
        //                   no ha sido ejecutado.  El correo debe llegar al Jefe inmediato.

        $q = " SELECT A.Arecdr, A.Arecdo, A.Aretem, A.Arefor, A.Areper, A.Areano, A.Areest,
               B.Idecco, B.Ideuse, B.Idefin, C.Ideeml, C.Idetco, D.Mcaano, D.Mcaper, E.Cardes,
               concat(B.Ideno1,' ', B.Ideno2, ' ', B.Ideap1, ' ', B.Ideap2) as 'nomemp',
               concat(C.Ideno1,' ', C.Ideno2, ' ', C.Ideap1, ' ', C.Ideap2) as 'nomjefe'
               FROM ".$wbasedato."_000058 A Inner Join ".$wbasedato."_000013 B on A.Arecdo = B.Ideuse
               INNER JOIN ".$wbasedato."_000013 C on A.Arecdr = C.Ideuse
               INNER JOIN root_000079 E on trim(B.Ideccg) = trim(E.Carcod)
               LEFT JOIN ".$wbasedato."_000032 D on A.Arecdr = D.Mcauco
               AND A.Areano = D.Mcaano AND  A.Areper = D.Mcaper
               WHERE (A.Areper = ".$wmes.") AND A.Aretem='2' AND A.Areano = ".$wano."
                     AND B.Ideest ='on' AND D.Mcaano is null  AND D.Mcaper is null
                     AND B.Ideuse !='' Order by A.Arecdr ";


        // Construyo el Array con los datos de los Correos a Enviar
        $cont1 = 0;
        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);

        // Enviar los Correos con los datos de la consulta
        if ($num > 0 && $diasem != 5 && $diasem != 6)
        {
              $row2    = mysql_fetch_assoc($res) ;
              $conta   =0;
              $conema  =0;
              $conlistado = '';

              while($row = mysql_fetch_assoc($res)){

                      if ($conta == 0)
                         {$codant  = $row['Arecdr'];}

                          if  ($codant == $row['Arecdr']){

                               if ($row['nomemp'] != ''){

	                               $nuevafecha  = strtotime ( '+3 month' , strtotime ($row['Idefin']) ) ;

	                               $nuevafecha  = date ( 'Y-m-j' , $nuevafecha );

	                               $conlistado .= '<tr><td>'.$row['Ideuse'].'</td><td>'.$row['nomemp'].
	                                               '</td><td>'.$row['Cardes'].'</td><td>'.$row['Idefin'].
	                                               '</td><td>'.$nuevafecha.'</td></tr>';
                               }

                          }
                          else{

                              $conlistado    .= '</table></br></br>';

                              $Emaildesti     =  'arleyda@hotmail.com';  //$row['Ideeml'];


                              $ArrayOrigen    =  array('email'    => $correoori,
                                                    'password' => $password,
                                                    'from'     => '',
                                                    'fromName' => $nomorigen);

                              $ArrayDestino   =  array($Emaildesti);

                              $Contenido      =  $parrafo1;

                              $Contenido     .=  $parrafo2;

                              $Contenido     .= '<table border=1><tr><td>Codigo</td><td>Nombre empleado</td><td>Cargo</td><td>Fecha Ingreso</td><td>Fecha de Vencimiento</td></tr>';

                              $Contenido     .=  $conlistado;

                              $Contenido     .=  $titulo1;

                              $Contenido     .=  $final;

                              $nuevafecha     =  strtotime ( '+3 month' , strtotime ($row['Idefin']) ) ;

                              $nuevafecha     =  date ( 'Y-m-j' , $nuevafecha );

                              // Luego de Enviar el Email del Jefe anterior, Inicializo la variable $conlistado para crear
                              // un nuevo detalle del Email.

                              $conlistado = '<tr><td>'.$row['Ideuse'].'</td><td>'.$row['nomemp'].'</td><td>'.$row['Cardes'].'</td><td>'.$row['Idefin'].'</td><td>'.$nuevafecha.'</td></tr>';

                              sendToEmail($asunto1,$Contenido,$Contenido,$ArrayOrigen,$ArrayDestino);
                          }

                          $codant  = $row['Arecdr'] ;
                          $conta++;

                      }
        }

         // *****************    Consultar Empleados sin Ninguna Evaluacion programada     **************
         //                      El Email debe llegar al correo de Nomina

         $q = " SELECT A.Ideuse, A.Idefin, A.Ideced, A.Ideest,A.Idecco,
                A.Idetco, A.Ideeml, B.Arecdo, B.Areper, B.Areano,C.Cardes,
                concat(A.Ideno1,' ', A.Ideno2, ' ', A.Ideap1, ' ', A.Ideap2) as 'nomemp'
                FROM ".$wbasedato."_000013 A
                LEFT JOIN ".$wbasedato."_000058 B on B.Arecdo = A.Ideuse
                INNER JOIN root_000079 C on trim(A.Ideccg) = trim(C.Carcod)
                WHERE ((CURDATE()-DATE(A.Idefin))>75) AND (YEAR(CURDATE())<=YEAR(A.Idefin))
                  AND (B.Arecdo is null) AND (A.Ideap1 is not null) AND (A.Ideest = 'on')
                  AND (A.Ideced != '') AND (A.Ideap1 != '')
                  AND (A.Idetco not in ('".$contratos."'))
                GROUP By A.Ideuse
                Order by A.Ideap1, A.Ideap2 ";

        $cont1 = 0;
        $res   = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num   = mysql_num_rows($res);

        // Enviar los Correos con los datos de la consulta
        if ($num > 0 && $diasem == 2)
        {
              $numemp=0;
              $conlistado = '';

              while($row = mysql_fetch_assoc($res)){

                  if ($row['Ideeml'] != '' && $row['nomemp'] != '')
                  {
                     $numemp=1;
                     $nuevafecha = strtotime ( '+3 month' , strtotime ($row['Idefin']) ) ;
                     $nuevafecha = date ( 'Y-m-j' , $nuevafecha );

                     $conlistado .= '<tr><td>'.$row['Ideuse'].'</td><td>'.$row['nomemp'].
                                     '</td><td>'.$row['Cardes'].'</td><td>'.$row['Idefin'].
                                     '</td><td>'.$nuevafecha.'</td></tr>';
                  }
             }

             if ($numemp==1)
             {
                  $conlistado  .= '</table></br></br>';

                  $Emaildesti  =  $row['Ideeml'];

                  $Contenido   =  $titulo2;

                  $Contenido  .=  '<table border=1><tr><td>Codigo</td><td>Nombre empleado</td>
                                  <td>Cargo</td><td>Fecha Ingreso</td><td>Fecha de Vencimiento</td></tr>';

                  $Contenido  .=  $conlistado;

                  $ArrayOrigen  = array('email'    => $correoori,
                                        'password' => $password,
                                        'from'     => '',
                                        'fromName' => $nomorigen);

                  $ArrayDestino = array($correoori);

                  sendToEmail($asunto2,utf8_decode($Contenido),utf8_decode($Contenido),$ArrayOrigen,$ArrayDestino);
             }

        }

       // ************    Consultar antiguedad Empleados y verificar concordancia con el numero de evaluacion programadas
       //                                  El Email debe llegar al correo de Nomina

         $q = " SELECT count(B.Arecdo) as totaleva, DATEDIFF(curdate(),A.Idefin) as 'totaldias', A.Ideeml,
                A.Ideuse, A.Idefin, A.Ideced, A.Idecco, A.Ideest, A.Idecco, A.Idetco, B.Areper, B.Areano,
                C.Cardes, concat(A.Ideno1,' ', A.Ideno2, ' ', A.Ideap1, ' ', A.Ideap2) as 'nomemp'
                FROM ".$wbasedato."_000013 A
                INNER JOIN ".$wbasedato."_000058 B on B.Arecdo = A.Ideuse
                INNER JOIN root_000079 C on trim(A.Ideccg) = trim(C.Carcod)
                WHERE (DATEDIFF(CURDATE(),A.Idefin)>75) AND (YEAR(CURDATE())<=YEAR(A.Idefin))
                  AND (A.Ideap1 is not null) AND (A.Ideest = 'on')
                  AND (A.Ideced != '') AND (A.Ideap1 != '')
                  AND (A.Ideeml != '')
                  AND (A.Idetco not in ('".$contratos."'))
                GROUP BY B.Arecdo
                ORDER BY A.Ideap1, A.Ideap2";

        $cont1 = 0;
        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);

        // Enviar los Correos con los datos de la consulta
        if ($num > 0 && $diasem == 2)
        {
              $numemp=0;
              $conlistado='';
              $conlistado = '';
              $arrayeval = array();
              while($row = mysql_fetch_assoc($res)){
                     //Programacion Segun antiguedad
                     $fecha       = $row['Idefin'];
                     $dias        = $row['totaldias'];
                     $contador    = $row['totaleva'];
                     $resultado   = intval($row['totaldias']/90);
                     $identi      = $row['Ideuse'];
                     $mesfec      = substr($fecha,5,2);
                     $anofec      = substr($fecha,0,4);

                     // Si un empleado tiene mas de un año.
                     if ($contador < $resultado && $dias <=365 && $mesfec <= $wmes && $anofec <= $wano)
                     {
                          if ( ($wmes >= $row['Areper']) && ($wano == $row['Areano']) && ($row['nomemp'] != '') ){
                                 $numemp=1;
                                 $nuevafecha = strtotime ( '+3 month' , strtotime ($row['Idefin']) ) ;
                                 $nuevafecha = date ( 'Y-m-j' , $nuevafecha );

                                 $conlistado .= '<tr><td>'.$row['Ideuse'].'</td><td>'.$row['nomemp'].
                                     '</td><td>'.$row['Cardes'].'</td><td>'.$row['Idefin'].
                                     '</td><td>'.$nuevafecha.'</td></tr>';

                          }
                     }
              }

              if ($numemp==1)
              {
                      $conlistado   .= '</table></br></br>';

                      $Contenido    = $titulo3;

                      $Contenido    = '<table border=1><tr><td>Codigo</td><td>Nombre empleado</td><td>Cargo</td><td>Fecha Ingreso</td><td>Fecha de Vencimiento</td></tr>';

                      $Contenido   .=  $conlistado;

                      $ArrayOrigen  =  array('email'    => $correoori,
                                            'password' => $password,
                                            'from'     => '',
                                            'fromName' => $nomorigen);

                      $ArrayDestino =  array($correoori);

                      sendToEmail($asunto3,utf8_decode($Contenido),utf8_decode($Contenido),$ArrayOrigen,$ArrayDestino);
              }
        }

    // *****************    Consultar Empleados sin Centro de Costos en Organigrama para enviar por Correo   **************
    //                                     El Email debe llegar al correo de Nomina

         $q = " SELECT A.Ideuse, A.Idefin, A.Ideced, A.Idecco, A.Ideest, A.Idecco, B.Ajeuco, A.Id, A.Idetco,
               A.Ideeml, concat(A.Ideno1,' ', A.Ideno2, ' ', A.Ideap1, ' ', A.Ideap2) as 'nomemp'
               FROM ".$wbasedato."_000013 A
               Left Join ".$wbasedato."_000008 B on B.Ajeuco = A.Ideuse
               WHERE ((CURDATE()-DATE(A.Idefin))>15) AND (YEAR(CURDATE())<=YEAR(A.Idefin))
                  AND (B.Ajeuco is null) AND (A.Ideap1 is not null) AND (A.Ideest = 'on')
                  AND (A.Ideced != '') AND (A.Ideap1 != '')
                  AND (A.Idetco not in ('".$contratos."'))
                  AND (A.Ideeml != '')
               GROUP BY A.Ideuse
               ORDER BY A.Ideap1, A.Ideap2";

        // Construyo el Array con los datos de los Correos a Enviar
        $cont1 = 0;
        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);

        // Enviar los Correos con los datos de la consulta
        if ($num > 0 && $diasem == 2)
        {
              $numemp=0;
              $conlistado = '';

              while($row = mysql_fetch_assoc($res)){

                         if ($row['nomemp'] != ''){

	                         $numemp=1;
	                         $nuevafecha = strtotime ( '+3 month' , strtotime ($row['Idefin']) ) ;
	                         $nuevafecha = date ( 'Y-m-j' , $nuevafecha );

	                         $conlistado .= '<tr><td>'.$row['Ideuse'].'</td><td>'.$row['nomemp'].
	                                         '</td><td>'.$row['Cardes'].'</td><td>'.$row['Idefin'].
	                                         '</td><td>'.$nuevafecha.'</td></tr>';
                         }

              }
              if ($numemp==1)
              {
                  $conlistado  .=  '</table></br></br>';

                  $Contenido    =  $titulo4;

                  $Contenido   .= '<table border=1><tr><td>Codigo</td><td>Nombre empleado</td><td>Cargo</td><td>Fecha Ingreso</td><td>Fecha de Vencimiento</td></tr>';

                  $Contenido   .=  $conlistado;

                  $ArrayOrigen  = array('email'    => $correoori,
                                        'password' => $password,
                                        'from'     => '',
                                        'fromName' => $nomorigen);

                  $ArrayDestino = array($correoori);

                  sendToEmail($asunto4,utf8_decode($Contenido),utf8_decode($Contenido),$ArrayOrigen,$ArrayDestino);
              }

        }

    }
	//----------------------------------------------------------------------------------------------
	//	IMPOTAR MAESTRO DE CAUSAS
	// 	Creado por 			: 		Jerson trujillo.
	//	Fecha de creacion	:		2016-12-06.
	//----------------------------------------------------------------------------------------------
	function maestroCausas(){

		$wbasedato = $this->consultarAliasPorAplicacion( 'facturacion' );
		$this->conexionOdbc( 'facturacion' );

		// --> Verificar si para hoy ya se hizo la actualizacion
		$sql = "SELECT id
				  FROM ".$wbasedato."_000276
				 WHERE Fecha_data = '".date( "Y-m-d" )."'
				   AND id = 1 ";

		$res = mysql_query( $sql, $this->conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );

		if( $num == 0 ){

			if( $this->conex_u ){
				
				// --> Obtener causas que se deben actualizar
				$arrayCauAct = array();
				$sqlCausas = "
				SELECT Caucod
				  FROM ".$wbasedato."_000276
				 WHERE Caumig != 'on'
				";
				$resCausas = mysql_query( $sqlCausas, $this->conex );
				while($rowCausas = mysql_fetch_array($resCausas))
				{
					$arrayCauAct[] = $rowCausas['Caucod'];
				}

				// --> Obtener registros de unix
				$sqlUn = "
				SELECT caucod, caunom, cauact
				  FROM cacau ";

				$res = odbc_do( $this->conex_u, $sqlUn);

				if( $res ){
					$i = 0;
					while( odbc_fetch_row($res) )
					{
						// --> Borrar registros migrados desde unix
						if($i == 0){
							$sqlTruncate = "DELETE FROM ".$wbasedato."_000276 WHERE Caumig = 'on' ";
							$resTruncate = mysql_query( $sqlTruncate, $this->conex ) or die( mysql_errno()." - Error en el query $sqlTruncate - ".mysql_error() );
						}
						
						$estado = ((trim(odbc_result($res,'cauact')) == 'S') ? 'on' : 'off');
						$codigo = trim(odbc_result($res,'caucod'));
						$nombre = trim(odbc_result($res,'caunom'));

						if(in_array($codigo, $arrayCauAct))
						{
							// --> Actualizar registro
							$sqlActu = "
							UPDATE ".$wbasedato."_000276
							   SET Cauest = '".$estado."'
							 WHERE Caucod = '".$codigo."'
							";
							mysql_query( $sqlActu, $this->conex );
						}
						else
						{
							// --> Insertar el registro en matrix
							$sqlInsert = "INSERT INTO ".$wbasedato."_000276
										( Medico   ,	Fecha_data,      		Hora_data,           	Caucod,			Caunom,			Cauest,			Caumig,	Caucon, Seguridad       )
								 VALUES ( '$wbasedato', '".date( "Y-m-d" )."', 	'".date( "H:i:s" )."', 	'".$codigo."',	'".$nombre."',	'".$estado."',	'on',	'*',		'C-$wbasedato'  ) ";

							$resInsert = mysql_query( $sqlInsert, $this->conex );
						}

						$i++;
					}
				}
				else{
					echo "Error en el query $sqlUn";
				}
			}
			else{
				echo "NO SE CONECTO A UNIX";
			}
		}
		liberarConexionOdbc( $this->conex_u );
		odbc_close_all();
	}

	/**
	 * [maestroEstadosCartera: ]
	 * @return [type] [description]
	 * autor 	: Edwar Jaramillo
	 * Creado 	: 2017-01-03
	 */
	function maestroEstadosCartera()
	{
		// echo "Inicio Proceso maestroEstadosCartera: ".date("Y-m-d H:i:s")."<br>";
		$wbasedato = $this->consultarAliasPorAplicacion('facturacion');
		$this->conexionOdbc( 'facturacion' );
		$fecha_actual = date("Y-m-d");
		$hora_actual = date("H:i:s");

		$arr_estados_matrix = array();
		$sql = "SELECT 	Esccod, Escnom, Esccau, Escest
				FROM 	{$wbasedato}_000279";

		if($res_mx = mysql_query( $sql, $this->conex ))
		{
			while ($row_mx = mysql_fetch_assoc($res_mx))
			{
				$Esccod = $row_mx["Esccod"];
				$Escnom = $row_mx["Escnom"];
				$Esccau = $row_mx["Esccau"];
				$Escest = $row_mx["Escest"];
				if(!array_key_exists($Esccod, $arr_estados_matrix))
				{
					$arr_estados_matrix[$Esccod] = array();
				}

				$arr_estados_matrix[$Esccod]["Esccod"] = $Esccod;
				$arr_estados_matrix[$Esccod]["Escnom"] = $Escnom;
				$arr_estados_matrix[$Esccod]["Esccau"] = $Esccau;
				$arr_estados_matrix[$Esccod]["Escest"] = $Escest;
			}
		}
		else
		{
			echo ( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		}

		if($this->conex_u)
		{
			$sqlEstUnx = "	SELECT 	estcod, estnom, estcau, estact
							FROM 	caest";

			$resEst = odbc_do( $this->conex_u, $sqlEstUnx);
			if($resEst)
			{
				$arr_estados_unix = array();
				while(odbc_fetch_row($resEst))
				{
					// echo "."."<br>";
					// --> Vaciar la tabla estados en matrix para actualizarla con unix
					// if($i == 0){
					// 	$sqlTruncate = "TRUNCATE TABLE  {$wbasedato}_000279 ";
					// 	$resTruncate = mysql_query( $sqlTruncate, $this->conex ) or die( mysql_errno()." - Error en el query $sqlTruncate - ".mysql_error() );
					// }
					$Esccod = trim(odbc_result($resEst,'estcod'));
					$Escnom = trim(odbc_result($resEst,'estnom'));
					$Esccau = trim(odbc_result($resEst,'estcau'));
					$Escest = trim(odbc_result($resEst,'estact'));
					$Esccau = ($Esccau == 'S') ? 'on': 'off';
					$Escest = ($Escest == 'S') ? 'on': 'off';

					if(!array_key_exists($Esccod, $arr_estados_unix))
					{
						$arr_estados_unix[$Esccod] = array();
					}

					$arr_estados_unix[$Esccod]["Esccod"] = $Esccod;
					$arr_estados_unix[$Esccod]["Escnom"] = $Escnom;
					$arr_estados_unix[$Esccod]["Esccau"] = $Esccau;
					$arr_estados_unix[$Esccod]["Escest"] = $Escest;

					if(!array_key_exists($Esccod, $arr_estados_matrix))
					{
						// echo "."."<br>";
						// --> Insertar el registro en matrix
						$sqlInsert = "	INSERT INTO {$wbasedato}_000279
											   ( Medico        ,Fecha_data 		 ,Hora_data 	  ,Esccod 	  ,Escnom 	  ,Esccau 	  ,Escest 	  ,Seguridad     )
										VALUES ( '{$wbasedato}','{$fecha_actual}','{$hora_actual}','{$Esccod}','{$Escnom}','{$Esccau}','{$Escest}','C-$wbasedato')";

						if($resInsert = mysql_query($sqlInsert, $this->conex))
						{
							//
						}
						else
						{
							echo mysql_errno()." - Error en el query $sqlInsert - ".mysql_error();
						}
					}
					elseif($arr_estados_unix[$Esccod] != $arr_estados_matrix[$Esccod])
					{
						// echo ".."."<br>";
						$sqlUpdate = "	UPDATE {$wbasedato}_000279
											   SET 	Escnom = '{$Escnom}',
													Esccau = '{$Esccau}',
													Escest = '{$Escest}'
										WHERE 	Esccod = '{$Esccod}'";

						if($resInsert = mysql_query($sqlUpdate, $this->conex))
						{
							//
						}
						else
						{
							echo mysql_errno()." - Error en el query $sqlUpdate - ".mysql_error();
						}
						unset($arr_estados_matrix[$Esccod]);
					}
				}

			}
		}
		liberarConexionOdbc( $this->conex_u );
		odbc_close_all();
		// echo "FIN Proceso maestroEstadosCartera: ".date("Y-m-d H:i:s")."<br>";
	}
	
	//----------------------------------------------------------------------------------------------
	//	Medicamentos distintos los ultimos 15 dias
	// 	Creado por 			: 		Felipe Alvarez.
	//	Fecha de creacion	:		2017-05-15.
	//----------------------------------------------------------------------------------------------
	function diferenciaMaterialesUnixMatrix(){

		$wbasedato = $this->consultarAliasPorAplicacion( 'facturacion' );
		$this->conexionOdbc( 'facturacion' );
		$fecha_actual = date("Y-m-d");
		$hora_actual = date("H:i:s");
	
		$sqlTruncate = "DELETE FROM ".$wbasedato."_000289";
	    mysql_query( $sqlTruncate, $this->conex ) or die( mysql_errno()." - Error en el query $sqlTruncate - ".mysql_error() );
				

		// --> Verificar si para hoy ya se hizo la actualizacion
		$sql = "SELECT id
				  FROM ".$wbasedato."_000289
				 WHERE Fecha_data = '".date( "Y-m-d" )."'
				   AND id = 1 ";

		$res = mysql_query( $sql, $this->conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );

		if( $num == 0 ){

			if( $this->conex_u ){
				
				
				$sql = "SELECT Tcardoi,Tcarlin,Tcarfac,Tcardun,Tcarlun,Tcarfun,Tcarhis,Tcaring,Tcarprocod,Tcardoi,Tcarlin,Tcarfec, Tcarser,id
						  FROM ".$wbasedato."_000106 
						 WHERE Tcarfec BETWEEN '2017-05-19' AND '2017-05-23' 
						 AND Tcardun != ''  ";

				
				//echo "<br>".$sql;
				$res = mysql_query( $sql, $this->conex   ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
				$num = mysql_num_rows( $res );


				while($row = mysql_fetch_array($res))
				{
					
					$sqlEstUnx = "	SELECT cardetite, cardetdoc, cardetfue,cardetfac,cardetreg
									  FROM Facardet 
									 WHERE cardetdoc='".$row['Tcardun']."'
									   AND cardetite='".$row['Tcarlun']."'
									   AND cardetfue='".$row['Tcarfun']."'";

					$resEst = odbc_do( $this->conex_u, $sqlEstUnx);
					
					//echo "<br>".$sqlEstUnx;
					if($resEst)
					{
						$array_detalle = array();
						while(odbc_fetch_row($resEst))
						{
							if ($row['Tcarfac'] == trim(odbc_result($resEst,'cardetfac')))
							{
								//echo "<br>no entro".$row['Tcarfac']."---".trim(odbc_result($resEst,'cardetfac'));
							}
							else
							{
								
									//echo "<br>Entro".$row['Tcarfac']."---".trim(odbc_result($resEst,'cardetfac'));
									//
									$sqlInsert = "	INSERT INTO {$wbasedato}_000289
												   ( Medico        ,Fecha_data 		 ,Hora_data 	  , Seguridad		, 		Mvuhis			, Mvuing 				,Mvucod						,  	Mvudoc				,  	Mvulin				, Mvufue 									   ,Mvufec  				   , Mvureg 			   						 , Mvuidc 			, 		Mvufac 				, Mvucco)
											VALUES ('{$wbasedato}' ,'{$fecha_actual}','{$hora_actual}','C-$wbasedato'	, '".$row['Tcarhis']."' , '".$row['Tcaring']."' , '".$row['Tcarprocod']."'	,'".$row['Tcardoi']."'	,'".$row['Tcarlin']."'	, '".trim(odbc_result($resEst,'cardetfue'))."' , '".$row['Tcarfec']."' 	   ,'".trim(odbc_result($resEst,'cardetreg'))."' , '".$row['id']."' , '".$row['Tcarfac']."' , '".$row['Tcarser']."')";
									//echo $sqlInsert;
									$resInsert = mysql_query($sqlInsert, $this->conex);
									//	echo $sqlInsert;
							}
								//
							
						}
					}
				

				}
				
			}
		}
		liberarConexionOdbc( $this->conex_u );
		odbc_close_all();
	}
}
?>