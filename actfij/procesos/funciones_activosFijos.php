<?php
include_once("conex.php");
/*
 Este archivo es una libreria de funciones utilizadas en activos fijos
*/
$cantidad_decimales =  consultarAliasPorAplicacion($conex, $wemp_pmla, 'cantidad_decimales_actfij');

function formato_numero( $number ){
	global $cantidad_decimales;

	$number = number_format((double)$number,intval($cantidad_decimales),'.',',');
	return $number;
}

//-------------------------------------------------------------
//	Funcion que Replica las tablas cuando se corren los procesos  de activos fijos, de esta manera se guarda
//  la informacion de como quedo el periodo y asi  poder  llevar un historico de  la informacion de activos
//-------------------------------------------------------------
function replicarTablas($anoActual='', $mesActual='')
{
	global $conex;
	global $wemp_pmla;
	global $wbasedato;
	global $wuse;

	if($anoActual=='' && $mesActual=='')
	{
		$vectorauxiliar 	= traerPeriodoActual();
		$mesActual  		= $vectorauxiliar['mes'];
		$anoActual  		= $vectorauxiliar['ano'];
	}

	$vectorauxiliar 	= traerperiodoSiguiente($anoActual, $mesActual);
	$mesSiguiente  		= $vectorauxiliar['mes'];
	$anoSiguiente	 	= $vectorauxiliar['ano'];


	// actfij_000001
	$delete1 = "DELETE
				  FROM ".$wbasedato."_000001
				 WHERE Actano  = '".$anoSiguiente."'
				   AND Actmes  = '".$mesSiguiente."'";

	mysql_query($delete1, $conex ) or die("<b>ERROR EN QUERY MATRIX:</b><br>".mysql_error());


	$replicatabla1 = "INSERT INTO ".$wbasedato."_000001 (Medico, 	Fecha_data, 			Hora_data, 				Actano				,		Actmes			,	Actreg,	Actnom,	Actpla,	Actemp,	Actpro,	Actfoc,	Actmar,	Actser,	Actmod,	Actdes,	Actnue, Actfad,	Actfps,	Actest, Actubi, Actase,	Actpol,	Actvas,	Actdep,	Actmoa,	Actgru,			Actsub,	Actact,	Seguridad)
												 (SELECT Medico, 	'".date("Y-m-d")."', 	'".date("H:i:s")."', 	'".$anoSiguiente."'		,	'".$mesSiguiente."'	,	Actreg,	Actnom,	Actpla,	Actemp,	Actpro,	Actfoc,	Actmar,	Actser,	Actmod,	Actdes,	Actnue, Actfad,	Actfps,	Actest, Actubi, Actase,	Actpol,	Actvas,	Actdep,	Actmoa,	Actgru,		Actsub,	Actact,	Seguridad
													FROM ".$wbasedato."_000001
												   WHERE Actano  = '".$anoActual."'
													 AND Actmes  = '".$mesActual."' )";

	mysql_query($replicatabla1, $conex ) or die("<b>ERROR EN QUERY MATRIX:</b><br>".mysql_error());



	// actfij_000002
	$delete2 = "DELETE
			  FROM ".$wbasedato."_000002
			 WHERE Aifano  = '".$anoSiguiente."'
			   AND Aifmes  = '".$mesSiguiente."'";

	mysql_query($delete2, $conex ) or die("<b>ERROR EN QUERY MATRIX:</b><br>".mysql_error());


	$replicatabla2 = "INSERT INTO ".$wbasedato."_000002 (Medico,	Fecha_data,				Hora_data,				Aifano		,		Aifmes		,Aifreg,Aifcad,Aifica,Aifaxi,Aifmej,Aifdac,Aifada,Aifipv,Aifvsa,Aifraf,Aifpaf,Aifsir,Aifmar,Aifsfr,Aifaca,Aifmde,Aifvut,Aifpde,Aifppd,Aifsin,Aifdme,Aifdaf,Aifsiv,Aifmmv,Aifsfv,Aifvpa,Seguridad)
												 (SELECT Medico,	'".date("Y-m-d")."', 	'".date("H:i:s")."',	'".$anoSiguiente."','".$mesSiguiente."',Aifreg,Aifcad,Aifica,Aifaxi,Aifmej,Aifdac,Aifada,Aifipv,Aifvsa,Aifraf,Aifpaf,Aifsir,Aifmar,Aifsfr,Aifaca,Aifmde,Aifvut,Aifpde,Aifppd,Aifsin,Aifdme,Aifdaf,Aifsiv,Aifmmv,Aifsfv,Aifvpa,Seguridad
													FROM ".$wbasedato."_000002
												   WHERE Aifano  = '".$anoActual."'
													 AND Aifmes  = '".$mesActual."' )";

	mysql_query($replicatabla2, $conex ) or die("<b>ERROR EN QUERY MATRIX:</b><br>".mysql_error());


	// actfij_000003
	$delete3 = "DELETE
			  FROM ".$wbasedato."_000003
			 WHERE Ainano  = '".$anoSiguiente."'
			   AND Ainmes  = '".$mesSiguiente."'";

	mysql_query($delete3, $conex ) or die("<b>ERROR EN QUERY MATRIX:</b><br>".mysql_error());


	$replicatabla3 = "INSERT INTO ".$wbasedato."_000003 (Medico,Fecha_data,Hora_data,		Ainano		,		Ainmes			,Ainreg,Aincom,Aindpc,Ainnco,Ainuge,Aincad,Ainrsi,Ainrmm,Ainrsf,Ainmsi,Ainmmm,Ainmsf,Ainrai,Ainram,Ainraf,Ainvsa,Aintcf,Aincsi,Aincmm,Aincsf,Aintes,Ainfre,Aintaj,Aintap,Ainpci,Ainpcm,Ainpcf,Ainmva,Ainvrv,Ainfva,Ainrei,Ainrem,Ainref,Aindsi,Aindmm,Aindsf,Ainmdn,Aintde,Ainode,Ainvup,Ainpdn,Ainppd,Aindci,Aindcm,Aindcf,Ainrdi,Ainrdm,Ainrdf,Aindfi,Aindfm,Aindff,Aindri,Aindrm,Aindrf,Aindai,Aindam,Aindaf,Ainddi,Ainddm,Ainddf,Aindei,Aindem,Aindea,Ainvli,Ainvlm,Ainvlf,Ainsri,Ainsrm,Ainsrf,Ainest,Seguridad)
												 (SELECT Medico,'".date("Y-m-d")."', 	'".date("H:i:s")."','".$anoSiguiente."', '".$mesSiguiente."'	,Ainreg,Aincom,Aindpc,Ainnco,Ainuge,Aincad,Ainrsi,Ainrmm,Ainrsf,Ainmsi,Ainmmm,Ainmsf,Ainrai,Ainram,Ainraf,Ainvsa,Aintcf,Aincsi,Aincmm,Aincsf,Aintes,Ainfre,Aintaj,Aintap,Ainpci,Ainpcm,Ainpcf,Ainmva,Ainvrv,Ainfva,Ainrei,Ainrem,Ainref,Aindsi,Aindmm,Aindsf,Ainmdn,Aintde,Ainode,Ainvup,Ainpdn,Ainppd,Aindci,Aindcm,Aindcf,Ainrdi,Ainrdm,Ainrdf,Aindfi,Aindfm,Aindff,Aindri,Aindrm,Aindrf,Aindai,Aindam,Aindaf,Ainddi,Ainddm,Ainddf,Aindei,Aindem,Aindea,Ainvli,Ainvlm,Ainvlf,Ainsri,Ainsrm,Ainsrf,Ainest,Seguridad
													FROM ".$wbasedato."_000003
												   WHERE Ainano  = '".$anoActual."'
													 AND Ainmes  = '".$mesActual."' )";

	mysql_query($replicatabla3, $conex ) or die("<b>ERROR EN QUERY MATRIX:</b><br>".mysql_error());


	// tabla actfij_000017
	$delete1 = "DELETE
				  FROM ".$wbasedato."_000017
				 WHERE Ccaano  = '".$anoSiguiente."'
				   AND Ccames  = '".$mesSiguiente."'";


	mysql_query($delete1, $conex ) or die("<b>ERROR EN QUERY MATRIX:</b><br>".mysql_error());

	$replicatabla1 = "INSERT INTO ".$wbasedato."_000017 ( Medico,	Fecha_data,	Hora_data,	Ccareg,		Ccaano			,		Ccames			,	Ccacco,	Ccapor,	Ccaest,	Seguridad )
					 (SELECT 							  Medico,   Fecha_data, Hora_data,	Ccareg,	'".$anoSiguiente."' ,	'".$mesSiguiente."'	,	Ccacco,	Ccapor,	Ccaest,	Seguridad
						FROM ".$wbasedato."_000017
					   WHERE Ccaano  = '".$anoActual."'
						 AND Ccames  = '".$mesActual."' )";

	mysql_query($replicatabla1, $conex ) or die("<b>ERROR EN QUERY MATRIX:</b><br>".mysql_error());



	// tabla actfij_000005
	$delete4 = "DELETE
				  FROM ".$wbasedato."_000005
				 WHERE	Forano  = '".$anoSiguiente."'
				   AND  Formes	= '".$mesSiguiente."' ";

	mysql_query($delete4, $conex ) or die("<b>ERROR EN QUERY MATRIX:</b><br>".mysql_error());


	$replicatabla4 = " INSERT INTO ".$wbasedato."_000005 (Medico,	Fecha_data,				Hora_data,				Forano			,		Formes			, 	Forcod, Fornom,	Fortab,	Forcam,	Forfor,	Forest,		Formde,	Seguridad)
					  (SELECT							  Medico,	'".date("Y-m-d")."', 	'".date("H:i:s")."',	'".$anoSiguiente."'	,	'".$mesSiguiente."'	,	Forcod, Fornom,	Fortab,	Forcam,	Forfor,	Forest,	Formde,	Seguridad
						 FROM	".$wbasedato."_000005
						WHERE	Forano  = '".$anoActual."'
						  AND   Formes	= '".$mesActual."' )";

	mysql_query($replicatabla4, $conex ) or die("<b>ERROR EN QUERY MATRIX:</b><br>".mysql_error());

	//-------------------------

	// tabla actfij_000034
	$delete5 = "DELETE
				  FROM ".$wbasedato."_000034
				 WHERE	Ejeano  = '".$anoSiguiente."'
				   AND  Ejemes	= '".$mesSiguiente."' ";

	mysql_query($delete5, $conex ) or die("<b>ERROR EN QUERY MATRIX:</b><br>".mysql_error());


	$replicatabla5 = " INSERT INTO ".$wbasedato."_000034( Medico,	Fecha_data,				Hora_data,		 	Ejeano			,		Ejemes			,	Ejetip 	,	Ejesub	,	Ejecfo,	Ejeord,	Ejeest,	Seguridad)
					  (SELECT							  Medico,	'".date("Y-m-d")."', 	'".date("H:i:s")."',		'".$anoSiguiente."'	,	'".$mesSiguiente."'	,	Ejetip	,	Ejesub	,	Ejecfo,	Ejeord,	Ejeest,	Seguridad
						 FROM	".$wbasedato."_000034
						WHERE	Ejeano  = '".$anoActual."'
						  AND   Ejemes	= '".$mesActual."' )";

	mysql_query($replicatabla5, $conex ) or die("<b>ERROR EN QUERY MATRIX:</b><br>".mysql_error());


	// tabla actfij_000027
	$delete5 = "DELETE
				  FROM ".$wbasedato."_000027
				 WHERE	Tccano  = '".$anoSiguiente."'
				   AND  Tccmes	= '".$mesSiguiente."' ";

	mysql_query($delete5, $conex ) or die("<b>ERROR EN QUERY MATRIX:</b><br>".mysql_error());


	$replicatabla5 = " INSERT INTO ".$wbasedato."_000027( Medico , 		Fecha_data		, 		Hora_data		,	 Tccano	 				,	 Tccmes	 			,	 Tcccod	 , 	 Tccnom	, 		Tcctab	,		Tcccam	, 		Tccfij	, 		Tccest	, 	Seguridad			)
					  (SELECT							  Medico,	'".date("Y-m-d")."', 	'".date("H:i:s")."',		'".$anoSiguiente."'	,	'".$mesSiguiente."'	,	Tcccod	,	Tccnom	,		Tcctab	,		Tcccam	,		Tccfij	,		Tccest	, 	Seguridad
						 FROM	".$wbasedato."_000027
						WHERE	Tccano  = '".$anoActual."'
						  AND   Tccmes	= '".$mesActual."' )";

	mysql_query($replicatabla5, $conex ) or die("<b>ERROR EN QUERY MATRIX:</b><br>".mysql_error());

	// tabla actfij_000028
	$delete5 = "DELETE
				  FROM ".$wbasedato."_000028
				 WHERE	Cueano  = '".$anoSiguiente."'
				   AND  Cuemes	= '".$mesSiguiente."' ";

	mysql_query($delete5, $conex ) or die("<b>ERROR EN QUERY MATRIX:</b><br>".mysql_error());


	$replicatabla5 = " INSERT INTO ".$wbasedato."_000028( Medico 	, 	Fecha_data			, 		Hora_data		,			 Cueano	 		,	 	Cuemes	 		, 	Cuetcc	,	Cuegra	, 	 Cuegrc	,   Cuenum	,	Cueest	, 	Seguridad			)
					  (SELECT							  Medico	,	'".date("Y-m-d")."'	, 	'".date("H:i:s")."'	,		'".$anoSiguiente."'	,	'".$mesSiguiente."'	,	Cuetcc	,	Cuegra	,	Cuegrc	,	Cuenum	,	Cueest	,	Seguridad
						 FROM	".$wbasedato."_000028
						WHERE	Cueano  = '".$anoActual."'
						  AND   Cuemes	= '".$mesActual."' )";

	mysql_query($replicatabla5, $conex ) or die("<b>ERROR EN QUERY MATRIX:</b><br>".mysql_error());


	// tabla actfij_000032
	$delete5 = "DELETE
				  FROM ".$wbasedato."_000032
				 WHERE	Comano  = '".$anoSiguiente."'
				   AND  Commes	= '".$mesSiguiente."' ";

	mysql_query($delete5, $conex ) or die("<b>ERROR EN QUERY MATRIX:</b><br>".mysql_error());


	$replicatabla5 = " INSERT INTO ".$wbasedato."_000032( Medico 	, 	Fecha_data			, 		Hora_data		, 		Comano			 	, 		Commes			, 		Comnco	, 	Comtdc	,	 Comtip	 , 	 Comsub	,   Comcau	,   Comreg	,   Comtcc	,   Comfor	,   Comest	, 	Seguridad			)
					  (SELECT							  Medico	,	'".date("Y-m-d")."'	, 	'".date("H:i:s")."'	,		'".$anoSiguiente."'	,	'".$mesSiguiente."'	,		Comnco	, 	Comtdc	,	 Comtip	 , 	 Comsub	,   Comcau	,   Comreg	,   Comtcc	,   Comfor	,   Comest	, 	Seguridad
						 FROM	".$wbasedato."_000032
						WHERE	Comano  = '".$anoActual."'
						  AND   Commes	= '".$mesActual."' )";

	mysql_query($replicatabla5, $conex ) or die("<b>ERROR EN QUERY MATRIX:</b><br>".mysql_error());


	//---------------------

	// tabla actfij_000027
	/*$delete3 = "DELETE
				  FROM ".$wbasedato."_000027
				 WHERE	Ejeano  = '".$anoActual."'
				   AND  Ejemes	= '".$mesActual."' ";

	echo "<br>".$delete3;


	$replicatabla3 = " INSERT INTO ".$wbasedato."_000027( Medico,	Fecha_data,	Hora_data,		 	Ejeano			,		Ejemes			,	Ejetip 	,	Ejesub	,	Ejetab,	Ejecam,	Ejeord,	Ejeest,	Seguridad)
					  (SELECT							  Medico,	Fecha_data,	Hora_data,		'".$anoSiguiente."'	,	'".$mesSiguiente."'	,	Ejetip	,	Ejesub	,	Ejetab,	Ejecam,	Ejeord,	Ejeest,	Seguridad)
						 FROM	".$wbasedato."_000027
						WHERE	Forano  = '".$anoActual."'
						  AND   Formes	= '".$mesActual."' )";

	echo "<br>".$replicatabla3;


	//---------------------

*/
}

// funcion que trae el periodo actual
function traerPeriodoActual()
{
	global $conex;
	global $wemp_pmla;
	global $wbasedato;
	global $wuse;

	$data = array();

	$select = "SELECT  	Cieano, Ciemes
				 FROM  	".$wbasedato."_000035
				WHERE  	Cieest = 'off'
			 ORDER BY	Cieano, Ciemes";


	$data['ano']='';
	$data['mes']='';

	if($res = mysql_query($select,$conex) )
	{
		$row 			= mysql_fetch_array($res);
		$data['ano']	=$row['Cieano'];
		$data['mes']	=$row['Ciemes'];
	}

	return $data;


}
// Funcion que  calcula el periodo siguiente

function traerperiodoSiguiente($anoActual, $mesActual)
{
	$data = array();
	$data['mes']	='';
	$data['ano']	='';
	//list( $anoActual, $mesActual ) = explode( "-", date( "Y-m", strtotime( date( $anoActual."-".$mesActual."-t" ) )+24*3600 ) );
	list( $anoActual, $mesActual ) = explode( "-", date("Y-m", strtotime("$anoActual-$mesActual +1 month")));

	$data['mes']	=$mesActual;
	$data['ano']	=$anoActual;

	return $data;
}

// Funcion que  calcula el periodo siguiente

function traerperiodoAnterior($anoActual, $mesActual)
{
	$data = array();
	$data['mes']	='';
	$data['ano']	='';
	//list( $anoAnterior, $mesAnterior ) = explode( "-", date( "Y-m", strtotime( date( $anoActual."-".$mesActual."-01" ) )-24*3600 ) );
	list( $anoAnterior, $mesAnterior ) = explode( "-", date("Y-m", strtotime("$anoActual-$mesActual -1 month")));

	$data['mes']	=$mesAnterior;
	$data['ano']	=$anoAnterior;

	return $data;
}

//---------------------------------------------------------------------------
//	--> Funcion que obtiene la formulacion de un activo o todos los activos
//		Jerson Trujillo, 2015-01-26
//---------------------------------------------------------------------------
function obtenerArrayActivosEjecutar($periodo, $regActivo='')
{
	global $conex;
	global $wbasedato;
	global $wuse;
	global $wemp_pmla;

	$respuesta = array("mensaje" => "", "error" => FALSE, "ListaActivos" => array(), "totalElementos" => 0, "arrayFormulas" => array());

	$arrPeriodo	= explode('-', $periodo);
	$perAno		= $arrPeriodo[0];
	$perMes 	= $arrPeriodo[1];

	$vectorauxiliar = traerPeriodoActual();
	$mesActual		= $vectorauxiliar['mes'];
	$anoActual		= $vectorauxiliar['ano'];

	// --> validar que exista un periodo activo en el sistema
	if($mesActual == '' || $anoActual == '')
	{
		$respuesta["error"] 	= TRUE;
		$respuesta["mensaje"] 	= '> No existe un periodo activo en el sistema.';
		return $respuesta;
	}

	// --> validar  si el periodo a ejecutar es el activo actualmente
	if($mesActual != $perMes || $anoActual != $perAno)
	{
		$respuesta["error"] 	= TRUE;
		$respuesta["mensaje"] = "> Se debe cerrar primero el periodo ".$anoActual."-".$mesActual.".";
		return $respuesta;
	}
	else
	{
		// --> Obtener array de todos los activos
		$arrayActivos 	= array();
		$sqlActivos 	= " SELECT A.Actreg, A.Actnom, B.Aincom, B.Ainnco
							  FROM ".$wbasedato."_000001 AS A LEFT JOIN ".$wbasedato."_000003 AS B
							       ON A.Actano = B.Ainano AND A.Actmes = B.Ainmes AND A.Actreg = B.Ainreg AND B.Ainest = 'on',
								   ".$wbasedato."_000004
						     WHERE A.Actano = '".$perAno."'
						       AND A.Actmes = '".$perMes."' "
		.(($regActivo!='') ? " AND A.Actreg = '".$regActivo."' "
						   : " AND A.Actact = 'on' ")."
							   AND A.Actest = Estcod
							   AND Estret  != 'on'
		";

		$resActivos = mysql_query($sqlActivos, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlActivos):</b><br>".mysql_error());
		while($rowActivos = mysql_fetch_array($resActivos))
		{
			$clave 					= $rowActivos['Actreg'].(($rowActivos['Aincom'] != '') ? '-'.$rowActivos['Aincom'] : '-*');
			$arrayActivos[$clave] 	= ($rowActivos['Ainnco'] != '' ) ?  utf8_encode($rowActivos['Ainnco']) : utf8_encode($rowActivos['Actnom']);
		}

		if(count($arrayActivos) == 0)
		{
			$respuesta["error"] 	= TRUE;
			$respuesta["mensaje"] 	= "> No se encontraron activos.";
		}
		else
		{
			$respuesta["ListaActivos"] 		= $arrayActivos;
			$respuesta["totalElementos"] 	= count($arrayActivos);

			// --> Obtener los procesos que se deben ejecutar mensualmente
			$arrayFormulas = array();
			$sqlProMensual = "SELECT Subtip, Subcod, Subdes
								FROM ".$wbasedato."_000030
							   WHERE Subeme = 'on'
								 AND Subest = 'on'
							ORDER BY Subord " ;

			$resProMensual = mysql_query($sqlProMensual, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlProMensual):</b><br>".mysql_error());
			if(mysql_num_rows($resProMensual) > 0)
			{
				// --> Obtenerle a cada proceso su respectivas formulas a ejecutar
				while($rowProMensual = mysql_fetch_array($resProMensual))
				{
					$respueFormulas = obtenerFormulasProceso($perAno, $perMes, $rowProMensual['Subtip'], $rowProMensual['Subcod']);
					if(count($respueFormulas) > 0)
					{
						$arrayFormulas[$rowProMensual['Subtip']."-".$rowProMensual['Subcod']]['formulas'] 	= $respueFormulas;
						$arrayFormulas[$rowProMensual['Subtip']."-".$rowProMensual['Subcod']]['nombre']   	= utf8_encode($rowProMensual['Subdes']);
					}
				}
				$respuesta["arrayFormulas"] = $arrayFormulas;
			}
			else
			{
				$respuesta["error"] 	= TRUE;
				$respuesta["mensaje"] 	= "> No se encontraron procesos a ejecutar.";
			}
		}
		return $respuesta;
	}
}

//------------------------------------------------------------------------
//	--> Funcion que obtiene las formulas de un proceso
//		Jerson Trujillo, 2015-01-26
//------------------------------------------------------------------------
function obtenerFormulasProceso($perAno, $perMes, $tipo, $proceso)
{
	global $conex;
	global $wbasedato;
	global $wuse;
	global $wemp_pmla;

	$arrayFormulas = array();

	$sqlFormuEje = " SELECT Forcod, Fornom, Fortab, F.nombre, Forcam, Forfor, Formde
					   FROM ".$wbasedato."_000034, ".$wbasedato."_000005, formulario AS F
					  WHERE Ejeano = '".$perAno."'
						AND Ejemes = '".$perMes."'
						AND Ejetip = '".$tipo."'
						AND Ejesub = '".$proceso."'
						AND Forano = Ejeano
						AND Formes = Ejemes
						AND Forcod = Ejecfo
						AND Forest = 'on'
						AND F.medico = '".$wbasedato."'
						AND F.codigo = Fortab
				   ORDER BY Ejeord ";

	$resFormuEje = mysql_query($sqlFormuEje, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlFormuEje):</b><br>".mysql_error());
	while($rowFormuEje = mysql_fetch_array($resFormuEje, MYSQL_ASSOC))
	{
		$arrayFormula 			= json_decode(utf8_encode($rowFormuEje["Forfor"]), TRUE);
		str_replace($caracter_ma, $caracter_ok, $arrayFormula["nombre"]);
		$rowFormuEje["Forfor"] 	= $arrayFormula;
		$rowFormuEje["Fornom"]	= utf8_encode($rowFormuEje["Fornom"]);
		$rowFormuEje["nombre"]	= utf8_encode($rowFormuEje["nombre"]);

		$arrayFormulas[]	= $rowFormuEje;
	}
	return $arrayFormulas;
}

//------------------------------------------------------------------------
//	--> Funcion que realiza la ejecucion de los procesos por activo
//		Jerson Trujillo, 2015-01-26
//------------------------------------------------------------------------
function ejecutarProcesoPorActivo($activo, $arrayFormulas, $periodo)
{
	global $conex;
	global $wbasedato;
	global $wuse;
	global $wemp_pmla;

	$respuesta = array('Errores' => array(), 'Querys' => array(), 'Formulas' => array());

	// sleep(0.5);
	// echo "<pre>";
	// print_r($arrayFormulas);
	// echo "</pre>";

	// --> Definicion de variables
	$arrPeriodo 		= explode('-', $periodo);
	$periodoAno 		= $arrPeriodo[0];
	$periodoMes 		= $arrPeriodo[1];
	$arrActivo 			= explode('-', $activo);
	$regisActivo   		= trim($arrActivo[0]);
	$compoActivo		= trim($arrActivo[1]);
	$tablaAplicaCompo 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'aplicacomponente');
	$codProcesoReglasDe	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'codProcesoAplicaReglasDepreciacion');
	$codProReglasRevDet	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'codProcesoAplicaReglasRevaluacionDeterioro');
	$codProReglasDet	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'codProcesoAplicaReglasDeterioro');
	$arrValoresTemp		= array();
	$campoSensPorDep 	= array();
	$campoSensDiasRet 	= array();

	// --> Consultar los campos que son sensibles al % de depreciacion
	$sqlCamposPor = "SELECT CONCAT(Percod, '-', Percam)
					   FROM ".$wbasedato."_000019
					  WHERE Perspd = 'on'
						AND Perest = 'on'
	";
	$resCamposPor = mysql_query($sqlCamposPor, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCamposPor):</b><br>".mysql_error());
	while($rowCamposPor = mysql_fetch_array($resCamposPor))
		$campoSensPorDep[] = $rowCamposPor[0];

	// --> Consultar los campos que son sensibles al numero de dias antes del retiro
	$sqlCamposRet = "SELECT CONCAT(Percod, '-', Percam)
					   FROM ".$wbasedato."_000019
					  WHERE Persdr = 'on'
						AND Perest = 'on'
	";
	$resCamposRet = mysql_query($sqlCamposRet, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCamposRet):</b><br>".mysql_error());
	while($rowCamposRet = mysql_fetch_array($resCamposRet))
		$campoSensDiasRet[] = $rowCamposRet[0];

	// --> Obtener los metodos de depreciacion niff y fiscal del activo
	$metodosDepAct = array('NIFF' => '', 'FISCAL' => '');
	$sqlMet = "SELECT Aifmde
				 FROM ".$wbasedato."_000002
				WHERE Aifano = '".$periodoAno."'
				  AND Aifmes = '".$periodoMes."'
				  AND Aifreg = '".$regisActivo."'
	";
	$resMet = mysql_query($sqlMet, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlMet):</b><br>".mysql_error());
	if($rowMet = mysql_fetch_array($resMet))
		$metodosDepAct['FISCAL'] 	= $rowMet['Aifmde'];

	$sqlMetN = "SELECT Ainmdn
				  FROM ".$wbasedato."_000003
				 WHERE Ainano = '".$periodoAno."'
				   AND Ainmes = '".$periodoMes."'
				   AND Ainreg = '".$regisActivo."'
				   AND Aincom = '".$compoActivo."'
	";
	$resMetN = mysql_query($sqlMetN, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlMetN):</b><br>".mysql_error());
	if($rowMetN = mysql_fetch_array($resMetN))
		$metodosDepAct['NIFF'] 		= $rowMetN['Ainmdn'];

	// --> Inicio: Recorrer los procesos
	foreach($arrayFormulas as $codProceso => $infoProceso)
	{
		$continuar 				= true;
		$validarPorcenDistri 	= false;
		$validarNumDiasRetiro 	= false;

		// --> Obtener que validaciones le aplican al proceso
		$infoProc  = explode("-", $codProceso);
		$sqlValApl = "SELECT Subvpd, Subvdr, Subdes
						FROM ".$wbasedato."_000030
					   WHERE Subtip = '".$infoProc[0]."'
					     AND Subcod = '".$infoProc[1]."'
						 AND Subest = 'on'
		";
		$resValApl = mysql_query($sqlValApl, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlValApl):</b><br>".mysql_error());
		if($rowValApl = mysql_fetch_array($resValApl))
		{
			$validarPorcenDistri 	= (($rowValApl['Subvpd'] == 'on') ? true : false);
			$validarNumDiasRetiro 	= (($rowValApl['Subvdr'] == 'on') ? true : false);
			$nombreProceso			= $rowValApl['Subdes'];
		}

		// --> Validar que el activo no este retirado, o que haya sido retirado solo para el periodo actual.
		if($validarNumDiasRetiro)
		{
			$sqlEstRet = "SELECT Estret
							FROM ".$wbasedato."_000001, ".$wbasedato."_000004
						   WHERE Actano = '".$periodoAno."'
							 AND Actmes = '".$periodoMes."'
							 AND Actreg = '".$regisActivo."'
							 AND Actest = Estcod
			";
			$resEstRet = mysql_query($sqlEstRet, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlEstRet):</b><br>".mysql_error());
			if($rowEstRet = mysql_fetch_array($resEstRet))
			{
				// --> Si el estado actual es de retiro.
				if($rowEstRet['Estret'] == 'on')
				{
					// --> Consultar si el retiro fue en el periodo actual
					$sqlRet = "SELECT Movdre
								 FROM ".$wbasedato."_000016
								WHERE Movano = '".$periodoAno."'
								  AND Movmes = '".$periodoMes."'
								  AND Movreg = '".$regisActivo."'
								  AND Movtip = 'Retiro'
								  AND Movest = 'on'
					";
					$resRet = mysql_query($sqlRet, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlRet):</b><br>".mysql_error());
					// --> 	Si el estado general del activo es retirado no se deprecia, a excepción que el retiro
					//		haya sido en el mes actual de ejecución.
					if($rowRet = mysql_fetch_array($resRet))
					{
						$numDias = $rowRet['Movdre'];
						// --> El activo se depreciara proporcionalmente al número de días del mes que estuvo activo
						$numDiasDisponible = (($numDias == 31) ? 30 : $numDias);
					}
					else
						$continuar = false;
				}
			}
			else
				$continuar = false;

			if(!$continuar)
				break;
		}

		// -->
		//if($validarPorcenDistri)

		// --> Inicio: Reglas ya fijas para algunos procesos
		switch($codProceso)
		{
			// --> 1: Reglas para el proceso de depreciacion:
			case $codProcesoReglasDe:
			{
				// --> Regla 1.1
				// --> Obtener variables de validacion, (Modalidad deprecia, Grupo deprecia)
				$sqlModDep = "SELECT Moddep, Grudep
								FROM ".$wbasedato."_000001, ".$wbasedato."_000007, ".$wbasedato."_000008
							   WHERE Actano = '".$periodoAno."'
								 AND Actmes = '".$periodoMes."'
								 AND Actreg = '".$regisActivo."'
								 AND Actmoa = Modcod
								 AND Actgru = Grucod
				";
				$resModDep = mysql_query($sqlModDep, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlModDep):</b><br>".mysql_error());
				if($rowModDep = mysql_fetch_array($resModDep))
				{
					// --> Si la moalidad y el grupo del activo son depreciables
					if($rowModDep['Moddep'] == 'on' && $rowModDep['Grudep'] == 'on')
					{
						$porcenDepre = 100;
						// --> 	Se asigna esta variable en false para obligar a que al menos debe existir un estado
						//		que si sea depreciable; y sí lo es, la variable se asignara en true, para continuar con el proceso
						$continuar = false;

						// --> Consultar los estados que no son depreciables.
						$estNoDepre = array();
						$sqlEstDep = "SELECT Estcod
										FROM ".$wbasedato."_000004
									   WHERE Estdep != 'on'
									     AND Estest = 'on'
										 AND Estret != 'on'
						";
						$resEstDep = mysql_query($sqlEstDep, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlEstDep):</b><br>".mysql_error());
						while($rowEstados = mysql_fetch_array($resEstDep))
							$estNoDepre[] = $rowEstados['Estcod'];

						// --> Consultar los estados de cada cco relacionado con el activo
						$sqlEstados = " SELECT Ccaest, Ccapor
										  FROM ".$wbasedato."_000017
										 WHERE Ccaano = '".$periodoAno."'
										   AND Ccames = '".$periodoMes."'
										   AND Ccareg = '".$regisActivo."'
						";
						$resEstados = mysql_query($sqlEstados, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlEstados):</b><br>".mysql_error());
						while($rowEstados = mysql_fetch_array($resEstados))
						{
							$arrEstados = $rowEstados['Ccaest'];
							$arrEstados = explode(',', $arrEstados);

							foreach($arrEstados as $codEstAct)
							{
								// --> 	Si el estado es no depreciable, el % asignado al cco ya no se deprecia.
								//		Para depreciar solo el porcentaje asignado a los cco que si son depreciables
								if(in_array($codEstAct, $estNoDepre))
								{
									$porcenDepre = $porcenDepre-$rowEstados['Ccapor'];
								}
								else
									$continuar = true;
							}
						}
					}
					// --> El activo no se puede depreciar, se continua con el otro proceso
					else
						$continuar = false;
				}
				// --> Si no hay informacion, termino este proceso y continuo con el siguiente
				else
					$continuar = false;

				// --> Regla 1.2
				// --> Validar que todavía hayan periodos pendientes por depreciar.
				$sqlVal = "SELECT Aifppd, Ainppd
							 FROM ".$wbasedato."_000002, ".$wbasedato."_000003
							WHERE Aifano = '".$periodoAno."'
							  AND Aifmes = '".$periodoMes."'
							  AND Aifreg = '".$regisActivo."'
							  AND Ainano = Aifano
							  AND Ainmes = Aifmes
							  AND Ainreg = Aifreg
							  AND Aincom = '".$compoActivo."'
				";
				$resVal = mysql_query($sqlVal, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlVal):</b><br>".mysql_error());
				if($rowVal = mysql_fetch_array($resVal))
				{
					// --> Si periodos pendientes por depreciar fiscal o nif es igual o menor a cero, ya no se deprecia.
					if(($rowVal['Aifppd'] != '' && $rowVal['Aifppd'] <= 0) || ($rowVal['Ainppd'] != '' && $rowVal['Ainppd'] <= 0))
						$continuar = false;
				}

				// --> Regla 1.3
				if($continuar)
				{
					// --> 	Validar que el estado general del activo sea depreciable, y que el activo tenga activa
					//		la opcion de si depreciable.
					$sqlVal = "SELECT Estdep
								 FROM ".$wbasedato."_000001, ".$wbasedato."_000004
								WHERE Actano = '".$periodoAno."'
								  AND Actmes = '".$periodoMes."'
								  AND Actreg = '".$regisActivo."'
								  AND Actdep = 'on'
								  AND Actest = Estcod
								  AND Estdep = 'on'
					";
					$resVal = mysql_query($sqlVal, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlVal):</b><br>".mysql_error());
					if(!mysql_fetch_array($resVal))
						$continuar = false;
				}
				break;
			}
			// --> 2: Reglas para el proceso de revaluación o deterioro:
			case $codProReglasRevDet:
			{
				// --> 2.1 Validar si para el grupo al que pertenece el activo si se le aplica este proceso (Gruard=on).
				$sqlGruApl = "SELECT Grucod
								FROM ".$wbasedato."_000001, ".$wbasedato."_000008
							   WHERE Actano = '".$periodoAno."'
								 AND Actmes = '".$periodoMes."'
								 AND Actreg = '".$regisActivo."'
								 AND Actgru = Grucod
								 AND Gruard = 'on'
								 AND Gruest = 'on'
				";
				$resGruApl = mysql_query($sqlGruApl, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlGruApl):</b><br>".mysql_error());
				if(!mysql_fetch_array($resGruApl))
					$continuar = false;
				// --> 2.2 Validar que se haya realizado valuación dentro del periodo
				else
				{
					// --> Obtener fecha de valuación
					$sqlFecha = "SELECT Ainfva
								   FROM ".$wbasedato."_000003
								  WHERE Ainano = '".$periodoAno."'
									AND Ainmes = '".$periodoMes."'
									AND Ainreg = '".$regisActivo."'
									AND Aincom = '".$compoActivo."'
					";
					$resFecha = mysql_query($sqlFecha, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlFecha):</b><br>".mysql_error());
					if($rowFecha = mysql_fetch_array($resFecha))
					{
						$fechaValuacion = strtotime($rowFecha['Ainfva']);
						$fechaIniPer 	= strtotime($periodoAno."-".$periodoMes."-"."01");
						$fechaFinPer 	= strtotime($periodoAno."-".$periodoMes."-"."31");

						// --> Si la fecha de valuacion no esta dentro del periodo actual
						if($rowFecha['Ainfva'] == '0000-00-00'|| $fechaValuacion < $fechaIniPer || $fechaValuacion > $fechaFinPer)
							$continuar = false;
					}
					else
						$continuar = false;
				}

				break;
			}
			// --> 3: Reglas para el proceso de deterioro:
			case $codProReglasDet:
			{
				// --> 	3.1 Validar si para el grupo al que pertenece el activo, sea un grupo al que no se le aplique
				//		el proceso de revaluacion y deterioro (Gruard != on).
				$sqlGruApl = "SELECT Grucod
								FROM ".$wbasedato."_000001, ".$wbasedato."_000008
							   WHERE Actano = '".$periodoAno."'
								 AND Actmes = '".$periodoMes."'
								 AND Actreg = '".$regisActivo."'
								 AND Actgru = Grucod
								 AND (Gruard != 'on' OR Gruard IS NULL)
								 AND Gruest = 'on'
				";
				$resGruApl = mysql_query($sqlGruApl, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlGruApl):</b><br>".mysql_error());
				if(!mysql_fetch_array($resGruApl))
					$continuar = false;

				break;
			}
		}
		// --> Fin: Reglas ya fijas para algunos procesos

		if($continuar)
		{
			// --> Inicio: Recorrer cada formula del proceso
			foreach($infoProceso['formulas'] as $infoFormula)
			{
				// --> Hay que aplicar validacion por metodo de depreciacion
				if($infoFormula['Formde'] != 'NO_APLICA' && $infoFormula['Formde'] != '')
				{
					// --> Consultar de que tipo es el metodo de depreciacion asigando en la formula
					$sqlMetFor = "SELECT Mdftip
								    FROM ".$wbasedato."_000010
								   WHERE Mdfcod = '".$infoFormula['Formde']."'
								     AND Mdfest = 'on'
					";
					$resMetFor = mysql_query($sqlMetFor, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlMetFor):</b><br>".mysql_error());
					if($rowMetFor = mysql_fetch_array($resMetFor))
					{
						if($rowMetFor['Mdftip'] == 'N')
							$tipoMetodo = "NIFF";

						if($rowMetFor['Mdftip'] == 'F')
							$tipoMetodo = "FISCAL";

						// --> 	Si el metodo de depreciacion asignado en la formula es diferente al del activo, no le
						//		aplico la formula.
						if($metodosDepAct[$tipoMetodo] != $infoFormula['Formde'])
							break;
					}
					else
					{
						$respuesta['Errores'][] = "Activo: ".$regisActivo."-".$compoActivo.", el metodo de depreciacion ".$infoFormula['Formde']." no existe en el maestro, en la formula: ".$infoFormula['Fornom'].".";
						break;
					}
				}

				// --> 	Validacion: Si se va aplicar la formula a un componente y la tabla en la cual se va a
				// 		guardar el resultado no maneja registros por componente, entonces me salto a la siguiente
				//		formula; la unica tabla que maneja registros por coponente es la actfij_3, osea solo se aplicaran
				//		formulas a las tabla actfij_1 y actfij_2 para los activos que no sean compnentes

				if($compoActivo != '*' && $tablaAplicaCompo != $wbasedato."_".$infoFormula['Fortab'])
					break;

				$formula = '';
				// --> Inicio: Recorrer cada variable de la formula
				foreach($infoFormula['Forfor'] as $infoVariable)
				{
					// --> Realizar las respectivas operaciones, segun el tipo de variable
					switch(strtoupper($infoVariable['tipo']))
					{
						// --> Simplemente se concatena el valor de la variable
						case 'OPERADOR':
						{
							$formula.= $infoVariable['valor'];
							break;
						}
						// --> Se debe consultar el valor de un campo en la base de datos
						case 'CAMPO':
						{
							// --> Definicion del periodo a consultar
							if($infoVariable['periodo'] == "Actual")
							{
								$periodoConsultaAno = $periodoAno;
								$periodoConsultaMes = $periodoMes;
							}
							else
							{
								$arrPerAnterior 		= traerperiodoAnterior($periodoAno, $periodoMes);
								$periodoConsultaAno  	= $arrPerAnterior['ano'];
								$periodoConsultaMes	 	= $arrPerAnterior['mes'];
							}

							// --> Obtener prefijo de los campos de la tabla
							$selectPrefijo = substr($infoVariable['valor'], 0, 3);

							// --> Validar si la tabla a consultar maneja detalle por componentes
							if($tablaAplicaCompo == $infoVariable['tabla'])
								$sqlComponente = "AND ".$selectPrefijo."com = '".$compoActivo."' ";
							else
								$sqlComponente = "";

							//--> Obtener valor del campo

							$clave = $infoVariable['tabla']."-".$infoVariable['valor']."-".$periodoConsultaAno."-".$periodoConsultaMes;

							if(array_key_exists($clave, $arrValoresTemp))
								$formula.= $arrValoresTemp[$clave];
							else
							{
								$sqlValorCampo = "SELECT ".$infoVariable['valor']."
													FROM ".$infoVariable['tabla']."
												   WHERE ".$selectPrefijo."ano = '".$periodoConsultaAno."'
													 AND ".$selectPrefijo."mes = '".$periodoConsultaMes."'
													 AND ".$selectPrefijo."reg = '".$regisActivo."'
														 ".$sqlComponente."
								";
								$resValorCampo = mysql_query($sqlValorCampo, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlValorCampo):</b><br>".mysql_error());
								if($rowValorCampo = mysql_fetch_array($resValorCampo))
								{
									if($rowValorCampo[0] != '')
										$formula.= $rowValorCampo[0];
									else
									{
										$respuesta['Errores'][] = "Activo: ".$regisActivo."-".$compoActivo.", La variable: ".$infoVariable['nombre']." (".$infoFormula['nombre']."), no tiene valor.";
										$formula 				= "";
										break 2;
									}
								}
								else
								{
									if($infoVariable['periodo'] == "Actual")
									{
										$respuesta['Errores'][] = "Activo: ".$regisActivo."-".$compoActivo.", no se encontraron registros para la variable: ".$infoVariable['nombre'].". Formulario: ".$infoFormula['nombre'].".";
										$formula 				= "";
										break 2;
									}
									else
										$formula.= '0';
								}
							}
							break;
						}
						// --> Consultar estadistica en el sistema de indicadores
						case 'ESTADISTICA':
						{
							// --> Validar si la tabla a consultar maneja detalle por componentes
							if($tablaAplicaCompo == $infoVariable['tabla'])
								$sqlComponente = "AND ".$selectPrefijo."com = '".$compoActivo."' ";
							else
								$sqlComponente = "";

							// --> Obtener si el metodo de depreciacion niif actual del activo, maneja un origen de datos externo (Estadistica)
							$sqlMetDep = "SELECT Mdfrod
											FROM ".$wbasedato."_000003, ".$wbasedato."_000010
										   WHERE Ainano = '".$periodoAno."'
											 AND Ainmes = '".$periodoMes."'
											 AND Ainreg = '".$regisActivo."'
												 ".$sqlComponente."
											 AND Ainmdn = Mdfcod
											 AND Mdfrod = 'on'
							";
							$resMetDep = mysql_query($sqlMetDep, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlMetDep):</b><br>".mysql_error());
							// --> 	Si no hay resultados es porque el metodo de depreciacion actual no requiere un origen de datos externo
							//		ejemplo, el metodo de depreciacion linea recta no requiere un dato externo, el metodo de depreciacion
							//		unidades de produccion si requiere un dato externo, ya que su valor es dado por una estadistica o un
							//		valor ingresado en la ficha del activo; entonces si el metodo no requiere dato externo no se continua
							//		con la consulta de este dato, sino que simplemente este valor se concatena en la fomula como un valor
							//		vacio y termino el proceso.
							//
							if(!mysql_fetch_array($resMetDep))
							{
								$formula.= '""';
								break;
							}

							// --> Definicion del periodo a consultar
							if($infoVariable['periodo'] == "Actual")
							{
								$periodoConsultaAno = $periodoAno;
								$periodoConsultaMes = $periodoMes;
							}
							else
							{
								$arrPerAnterior 		= traerperiodoAnterior($periodoAno, $periodoMes);
								$periodoConsultaAno  	= $arrPerAnterior['ano'];
								$periodoConsultaMes	 	= $arrPerAnterior['mes'];
							}

							// --> Obtener prefijo de los campos de la tabla
							$selectPrefijo = substr($infoVariable['valor'], 0, 3);

							// --> Obtener codigo de la estadistica
							$sqlCodEst = "SELECT ".$infoVariable['valor'].", Aintde
											FROM ".$infoVariable['tabla']."
										   WHERE ".$selectPrefijo."ano = '".$periodoConsultaAno."'
											 AND ".$selectPrefijo."mes = '".$periodoConsultaMes."'
											 AND ".$selectPrefijo."reg = '".$regisActivo."'
												 ".$sqlComponente."
							";
							$resCodEst = mysql_query($sqlCodEst, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCodEst):</b><br>".mysql_error());
							if($rowCodEst = mysql_fetch_array($resCodEst))
							{
								$codTde = trim($rowCodEst[0]);
								// --> Es una estadistica
								if($rowCodEst['Aintde'] == 'on')
								{
									$wbasedatoSgc 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'sgc');
									$fecha_i 		= $periodoConsultaAno."-".$periodoConsultaMes."-01";
									$fecha_f 		= $periodoConsultaAno."-".$periodoConsultaMes."-30";

									// --> Obtener valor de la estadistica en el sistema de indicadores
									$sqlValEst = "SELECT Movres
													FROM ".$wbasedatoSgc."_000009
												   WHERE Movind = '".$codTde."'
													 AND ( '".$fecha_i."' >= Movfip AND '".$fecha_f."' <= Movffp )
												";
									$resValEst = mysql_query($sqlValEst, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlValEst):</b><br>".mysql_error());
									if($rowValEst = mysql_fetch_array($resValEst))
										$formula.= $rowValEst['Movres'];
									else
									{
										$respuesta['Errores'][] = "Activo: ".$regisActivo."-".$compoActivo.", No se ha calculado la estadistica (".$codTde.") en el sistema de indicadores";
										$formula 				= "";
										break 2;
									}
								}
								// --> Es un valor ya ingresado
								else
								{
									if($codTde != '')
										$formula.= $codTde;
									else
									{
										$respuesta['Errores'][] = "Activo: ".$regisActivo."-".$compoActivo.", No se ha ingresado el valor del dato estadistico (Manual)";
										$formula 				= "";
										break 2;
									}
								}
							}
							else
							{
								if($infoVariable['periodo'] == "Actual")
								{
									$respuesta['Errores'][] = "Activo: ".$regisActivo."-".$compoActivo.", no se encontraron registros para la variable: ".$infoVariable['nombre'].". Formulario: ".$infoFormula['nombre'].".";
									$formula 				= "";
									break 2;
								}
								else
									$formula.= '0';
							}
							break;
						}
					}
				}
				// --> Fin: Recorrer cada variable de la formula

				// --> Si existe una formula a ejecutar
				if($formula != '')
				{
					@eval('$formulaEval='.$formula.';');

					$resTemp = '';
					// --> Validar si se debe aplicar valor proporcional al % de depreciacion
					if(in_array($infoFormula['Fortab']."-".$infoFormula['Forcam'], $campoSensPorDep) && isset($porcenDepre))
					{
						$formulaEval 	= $formulaEval*($porcenDepre/100);
						$resTemp		= ' ('.$porcenDepre.'%)';
					}

					// --> 	Validar si se debe aplicar valor proporcional dependiendo del numero de dias
					//		antes de ser retirado el activo (Si ocurrió un retiro en el mes, solo se tomaran valores proporcionalmente a lo días
					//		en los que estuvo disponible el activo.)
					if(in_array($infoFormula['Fortab']."-".$infoFormula['Forcam'], $campoSensDiasRet) && isset($numDiasDisponible) && $validarPorcenDistri)
					{
						$formulaEval 	= ($formulaEval/30)*$numDiasDisponible;
						$resTemp		= ' ('.$numDiasDisponible.' dias.)';
					}

					$respuesta['Formulas'][$codProceso][] = $formula.' = '.formato_numero($formulaEval).$resTemp;
					$respuesta['nomProceso'][$codProceso] = utf8_encode($nombreProceso);

					$prefijoUpdate 		 = substr($infoFormula['Forcam'], 0, 3);

					// --> Validar si la tabla a actualizar maneja detalle por componentes
					if($tablaAplicaCompo == $wbasedato."_".$infoFormula['Fortab'])
						$sqlComponenteUpdate = "AND ".$prefijoUpdate."com = '".$compoActivo."' ";
					else
						$sqlComponenteUpdate = "";

					// --> 	Crear query para actualizar campo, con el respectivo resultado de la formula
					// 		Nota: Si se identa el query se genera un error al reenviarlo por el $.post que
					//		realiza el llamado para ejecutar los querys.
					$sqlGuardarRes = " UPDATE ".$wbasedato."_".$infoFormula['Fortab']." SET ".$infoFormula['Forcam']." = '".$formulaEval."' WHERE ".$prefijoUpdate."ano = '".$periodoAno."' AND ".$prefijoUpdate."mes = '".$periodoMes."' AND ".$prefijoUpdate."reg = '".$regisActivo."' ".$sqlComponenteUpdate." ";

					$respuesta['Querys'][$codProceso][] = $sqlGuardarRes;

					// --> Guardar temporalmente el resultado, porque depronto otra formula mas adelante la necesita
					$arrValoresTemp[$wbasedato."_".$infoFormula['Fortab']."-".$infoFormula['Forcam']."-".$periodoAno."-".$periodoMes] = $formulaEval;
				}
			}
		}
	}
	return $respuesta;
}

//------------------------------------------------------------------------
//	--> Funcion que realiza la ejecucion de las transacciones por activo
//		Jerson Trujillo, 2015-01-26
//------------------------------------------------------------------------
function ejecutarTransaccion($activo, $periodo, $transaccion)
{
	global $conex;
	global $wbasedato;
	global $wuse;
	global $wemp_pmla;

	$arrRepuesta	= array("Errores" => array());

	$infoPeriodo	= explode('-', $periodo);
	$ano			= $infoPeriodo[0];
	$mes			= $infoPeriodo[1];

	$infoActivo		= explode('-', $activo);
	$registroActivo	= $infoActivo[0];
	$componente		= ((trim($infoActivo[1]) == '') ? '*' : trim($infoActivo[1]));

	$infoPro 		= consultarAliasPorAplicacion($conex, $wemp_pmla, $transaccion);
	$infoPro		= explode("-", $infoPro);

	// --> Obtener formulas
	$respueFormulas[$infoPro[0]."-".$infoPro[1]]['formulas'] = obtenerFormulasProceso($ano, $mes, $infoPro[0], $infoPro[1]);

	// --> Si hay formulas por ejecutar
	if(count($respueFormulas[$infoPro[0]."-".$infoPro[1]]) > 0)
	{
		$respuestaEjec = ejecutarProcesoPorActivo($registroActivo."-".$componente, $respueFormulas, $ano."-".$mes);
		if(count($respuestaEjec['Errores']) > 0)
		{
			$arrRepuesta['Errores'] = $respuestaEjec['Errores'];
			return $arrRepuesta;
		}
		elseif(count($respuestaEjec['Querys']) > 0)
		{
			// --> Recorrer el array de querys y ejecutarlos
			foreach($respuestaEjec['Querys'] as $arrQuerysPorProceso)
			{
				foreach($arrQuerysPorProceso as $codProceso => $sqlQuery)
				{
					$resQuery = mysql_query($sqlQuery, $conex);
					if(!$resQuery)
						$arrRepuesta['Errores'][] = "Error: guardando datos, ".mysql_error().", en el query ".$sqlQuery."";
				}
			}
		}
	}

	return $arrRepuesta;
}

?>