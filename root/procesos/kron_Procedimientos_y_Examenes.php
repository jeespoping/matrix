<?php
include_once("conex.php");
/*
 ************************* KRON DE PROCEDIMIENTOS Y EXAMENES **********************************
 ****************************** DESCRIPCIÓN ***************************************************
 * Consulta los prcedimientos y exámenes registrados en Unix y si no existen en Matrix
 * los ingresa a la tabla de procedimientos y exámenes de Matrix y si existen los actualiza
 * También se consulta las empresas responsables en Unix y se actualizan 1 vez al día
 * en la tabla empresas responsables de Matrix
 *************************************************************************************************
 * MODIFICACIONES
 *************************************************************************************************
 * // --> 	2014-07-31, Jerson Andres Trujillo
 * 			El maestro de empresas si el registro existe se actualiza
 *
 *************************************************************************************************
 * 2013-09-04 - Se crea la función maestroExamenes donde se agrupa la actualización de procedimientos
 * y exámenes. Se agrega la actualización del maestro de CUPS de root con base en el maestro de procedimientos
 * y exámenes de Unix - Mario Cadavid
 *************************************************************************************************
 * 2013-08-21 - Se modifica la funcion maestroEmpresas para que llene el maestro de responsables de
 * matrix con los datos de unix - Jonatan López
 *************************************************************************************************
*/

	include_once("root/comun.php");
    include_once("movhos/otros.php");

	/**************************************************************************************************
    * DECLARACION DE VARIABLES GLOBALES
    **************************************************************************************************/

	//Conexion base de datos Matrix
	$conex = obtenerConexionBD("matrix");

	//Declaración de variable para determinar la empresa
	if(!isset($wemp_pmla))
	{
		$wemp_pmla = '01';
	}

	// Variables para determinar prefijos de base de datos
	$wbasedatohce = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");
	$wbasedato = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'cliame' );
	$wbasedatomv = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'movhos' );

    // Sirve para la función connectOdbc
	$bd = $wbasedatomv;

	//Conexion base de datos Unix
	//$conex_o = odbc_connect('facturacion','','') or die("No se realizó Conexión");
	connectOdbc(&$conex_o, 'facturacion');
	sleep(5);

	/**************************************************************************************************/


	/**************************************************************************************************
    * FUNCIONES
    **************************************************************************************************/

    // Función que conculta las empresas responsables de unix y las registra en la tabla 000024 de la empresa actual
	function maestroEmpresa()
	{

		global $conex;
		global $wemp_pmla;
		global $conex_o;
		global $wbasedato;

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
						 WHERE empact = 'S'
						   AND empres is null
						   AND empdir is null
						   AND emptel is null
						 UNION
						SELECT empcod, empnit, empnom, emptel, '' as  empdir, empmun, '' as empres, emppla, emppor, empact, empcop, empnca,
							   empcla, emptip, emptar, empbmp, empaev, empsme, empcde, empcas, empuad, empfad, empumo, empfmo
						  FROM INEMP
						 WHERE empact = 'S'
						   AND empres is null
						   AND empdir is null
						   AND emptel is not null
						 UNION
						SELECT empcod, empnit, empnom, '' as  emptel, empdir, empmun, '' as empres, emppla, emppor, empact, empcop, empnca,
							   empcla, emptip, emptar, empbmp, empaev, empsme, empcde, empcas, empuad, empfad, empumo, empfmo
						  FROM INEMP
						 WHERE empact = 'S'
						   AND empres is null
						   AND empdir is not null
						   AND emptel is null
						 UNION
					    SELECT empcod, empnit, empnom, emptel, empdir, empmun, '' as empres, emppla, emppor, empact, empcop, empnca,
							   empcla, emptip, emptar, empbmp, empaev, empsme, empcde, empcas, empuad, empfad, empumo, empfmo
						  FROM INEMP
						 WHERE empact = 'S'
						   AND empres is null
						   AND empdir is not null
						   AND emptel is not null
						 UNION
						SELECT empcod, empnit, empnom, '' as  emptel, '' as empdir, empmun, empres, emppla, emppor, empact, empcop, empnca,
							   empcla, emptip, emptar, empbmp, empaev, empsme, empcde, empcas, empuad, empfad, empumo, empfmo
						  FROM INEMP
						 WHERE empact = 'S'
						   AND empres is not null
						   AND empdir is null
						   AND emptel is null
						 UNION
						SELECT empcod, empnit, empnom, emptel, '' as  empdir, empmun, empres, emppla, emppor, empact, empcop, empnca,
							   empcla, emptip, emptar, empbmp, empaev, empsme, empcde, empcas, empuad, empfad, empumo, empfmo
						  FROM INEMP
						 WHERE empact = 'S'
						   AND empres is not null
						   AND empdir is null
						   AND emptel is not null
						 UNION
						SELECT empcod, empnit, empnom, '' as  emptel, empdir, empmun, empres, emppla, emppor, empact, empcop, empnca,
							   empcla, emptip, emptar, empbmp, empaev, empsme, empcde, empcas, empuad, empfad, empumo, empfmo
						  FROM INEMP
						 WHERE empact = 'S'
						   AND empres is not null
						   AND empdir is not null
						   AND emptel is null
						 UNION
						SELECT empcod, empnit, empnom, emptel, empdir, empmun, empres, emppla, emppor, empact, empcop, empnca,
							   empcla, emptip, emptar, empbmp, empaev, empsme, empcde, empcas, empuad, empfad, empumo, empfmo
						  FROM INEMP
						 WHERE empact = 'S'
						   AND empres is not null
						   AND empdir is not null
						   AND emptel is not null";

				$res = odbc_do( $conex_o, $sql );

				// Si se encontraron empresas en Unix se recorren para hacer las actualizaciones requeridas
				if( $res )
				{
					while( odbc_fetch_row($res) )
					{
						// --> 2014-07-31, Jerson Andres Trujillo
						// --> Si ya existe el registro de la empresa entonces lo actualizo
						if(array_key_exists(trim(odbc_result($res,1)), $arrayEmpresas))
						{
							$arrayEmpresas[trim(odbc_result($res,1)] = TRUE;

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
								   Emptem 		= '".trim( odbc_result($res,14) )."'
							 WHERE Empcod 		= '".trim( odbc_result($res,1) )."'
							";

							$resInsert = mysql_query( $sqlUpdate, $conex ) or die( mysql_errno()." - Error en el query $sqlUpdate - ".mysql_error() );
						}
						// --> Si no existe lo ingreso como uno nuevo
						else
						{
							// Se registra la fila actual de Unix en la tabla de empresas responsables de Matrix
							$sqlInsert = "	INSERT INTO {$wbasedato}_000024
														(Medico,Fecha_data,Hora_data,Empcod,Empnit,Empres,Empnom,Emptar,Empcon,Empdir,Emptel,Empmai,Empcmi,Empnma,Empdfi,Empdia,Emppdt,Empprt,Emptem,Empfac,Empest,Empmed,Emppro,Emprem,Emprip,Empftr,Empdiv,Empraz,Empnra,Empdgn,Empran,Emptde,Seguridad) "
										."		VALUES  ('$wbasedato', '".date( "Y-m-d" )."', '".date( "H:i:s" )."', '".trim( odbc_result($res,1) )."', '".trim( odbc_result($res,2) )."' , '".trim( odbc_result($res,2) )."', '".trim( odbc_result($res,3) )."', '".trim( odbc_result($res,15) )."', '".trim( odbc_result($res,7) )."', '".trim( odbc_result($res,5) )."', '".trim( odbc_result($res,4) )."',  '0' ,  '0' ,  '0' ,  '0' ,  '0' ,  '0' ,  '0' , '".trim( odbc_result($res,14) )."',   '' , 'on' ,  ''    ,  ''   , 'off' , 'off' , '0.0' ,   ''  ,  ''   ,   ''  ,   ''  ,   ''  , ''    , 'C-{$wbasedato}' ) ";

							$resInsert = mysql_query( $sqlInsert, $conex ) or die( mysql_errno()." - Error en el query $sqlInsert - ".mysql_error() );
						}
					}

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

    // Función que conculta los  procedimientos y examenes existentes en unix y los registra en la tabla 000017
	// de historia clínica de la empresa actual y en la tabla root_000012 que es el maestro de códigos CUPS
	function maestroExamenes()
	{

		global $conex;
		global $wemp_pmla;
		global $conex_o;
		global $wbasedatohce;

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
					." VALUES ('{$wbasedatohce}' ,'".$fecha."' ,'".$hora."' ,'".$row[0]."','".trim($wdesc)."' , '', '', '', '".$row[0]."', '', '".$westado."', '".$row[3]."', '".$wnopos."', 'C-{$wbasedatohce}') ";
				$err1 = mysql_query($q,$conex) or die("ERROR INSERTANDO EL CODIGO ".$row[0]." EN LA TABLA {$wbasedatohce}_000017 :  ".mysql_errno().":".mysql_error());

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
						."    SET Descripcion = '".trim($wdesc)."',  "
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
					." VALUES ('root' ,'".$fecha."' ,'".$hora."' ,'".$row[0]."','".trim($wdesc)."', 'C-root') ";
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
						."    SET Nombre = '".trim($wdesc)."',  "
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

		echo "Se insertaron   : ".$winsert." códigos en {$wbasedatohce}_000017";
		echo "<br>";
		echo "Se actualizaron : ".$wupdate." códigos en {$wbasedatohce}_000017";

		echo "<br><br>";
		echo "Se insertaron   : ".$winsertcups." códigos en root_000012";
		echo "<br>";
		echo "Se actualizaron : ".$wupdatecups." códigos en root_000012";
	}

	/**************************************************************************************************/


    if($conex_o != 0)
	{
        maestroEmpresa();
		maestroExamenes();

		liberarConexionOdbc($conex_o);
		odbc_close_all();
		
		liberarConexionOdbc($conex_o);
		odbc_close_all();
	}

?>
