<?php
include_once("conex.php");

	header("Content-Type: text/html;charset=ISO-8859-1");
	

	include_once("root/comun.php");
	

	include_once("../procesos/funciones_talhuma.php");
	global $wemp_pmla;


	$wbasedato    = consultarPrefijo($conex, $wemp_pmla, $wtema);
	$wbasedatocli = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
	$wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	$wcostosyp = consultarAliasPorAplicacion($conex, $wemp_pmla, 'COSTOS');
	$fecha= date("Y-m-d");
	$hora = date("H:i:s");

	$vectorcolor = array();
	$vectormaximo = array();
	$vectorminimo = array();



	$qsemaforo =  "SELECT Grusem "
				."   FROM ".$wbasedato."_000051"
				."  WHERE Grutem = '".$wtemareportes."' "
				."    AND Grucod = '".$numerodeagrupaciones[0]."' ";

	$ressemaforo = mysql_query($qsemaforo,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qsemaforo." - ".mysql_error());
	$rowsemaforo = mysql_fetch_array($ressemaforo);
	$semaforo = $rowsemaforo['Grusem'];

	$semaforo = '1';

	$anosemaforo=explode('-', $fechainicial1);

	$entidades = consultarCodigosEntidad($conex, $wbasedatocli);

	$qcolor =    "  SELECT Msecod, Msenom ,Dsecol,Dsemax,Dsemin"
				."    FROM ".$wbasedato."_000052, ".$wbasedato."_000053 "
				."   WHERE Msecod = Dsecod "
				."     AND Msecod = '".$t_semaforo."' "
				."     AND Dseano = '".$anosemaforo[0]."' "
				."ORDER BY Dseord";

	$rescolor = mysql_query($qcolor,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qcolor." - ".mysql_error());

	while($rowcolor = mysql_fetch_array($rescolor))
	{

		$vectormaximo[] = $rowcolor['Dsemax'];
		$vectorminimo[] = $rowcolor['Dsemin'];
		$vectorcolor[] = $rowcolor['Dsecol'];

	}
	//----------------------------------------------------------
	//----------------------------------------------------------



	if ($operacion=='inicioprogramacco')
	{

		echo json_encode(cargar_hiddens_para_autocomplete_cco());
		return;
	}

	if ($operacion=='inicioprogramausuario')
	{

		echo json_encode(cargar_hiddens_para_autocomplete_usuario());
		return;
	}

function AnalizarFormula( $rowccoxagrupacion,$resvalorcco)
{
	global $conex;
	global $wbasedato;
	global $wtema;

	$resultado = array();
	//---formula
	$select_formula = "SELECT Gruetb
						 FROM  ".$wbasedato."_000051
						WHERE  Grucod='".$rowccoxagrupacion."'";

	$res_formula = 	mysql_query($select_formula,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$select_formula." - ".mysql_error());

	while($row_formula = mysql_fetch_array($res_formula))
	{
		$arrayFormula = json_decode(utf8_encode($row_formula['Gruetb']), TRUE);
	}

	$formula = '';
	if($arrayFormula!='')
	{
		$aplicar_formula =true;
		$simbolo = '%';
	}
	else
	{
		$aplicar_formula =false;
	}
	if($aplicar_formula)
	{


		//$infoFormula['Forfor'] = '[{"tipo":"Operador","nombre":"9","valor":"9"},{"tipo":"Operador","nombre":"9","valor":"9"},{"tipo":"Operador","nombre":"9","valor":"9"}]';
		foreach($arrayFormula as $infoVariable)
		{
			// --> Realizar las respectivas operaciones, segun el tipo de variable
			switch(strtoupper($infoVariable['tipo']))
			{
				// --> Simplemente se concatena el valor de la variable
				case 'OPERADOR':
				{

					if($infoVariable['valor']=='Agrupacion')
					{
						$formula.=$resvalorcco;
					}
					else
					{
						$formula.= $infoVariable['valor'];
					}

					break;
				}


			}
		}
		if($formula != '')
		{
			@eval('$formula='.$formula.';');
		}

		$resvalorcco  = $formula;
		$resultado['valor'] = $resvalorcco ;
		$resultado['simbolo'] = $simbolo ;
		$resultado['entre'] = 'si';
	}
	else
	{
		$resvalorcco = $resvalorcco;
		$resultado['valor'] = $resvalorcco ;
		$resultado['simbolo'] = '' ;
		$resultado['entre'] = $select_formula;
	}

	return $resultado;

}

function consultarCodigosEntidad($conex, $wbasedatocli)
{   
	//Esta función consulta los codigos y nombres de las entidades

	$respuesta = array();
	$sql = " SELECT Empcod, Empnom ";
	$sql .= " FROM ".$wbasedatocli."_000024 ";
	$sql .= " WHERE Empest = 'on'; ";    
	$res = mysql_query($sql, $conex) or die(mysql_errno()." - en el query: ".$sql." - ".mysql_error());

	while($row = mysql_fetch_assoc($res))
	{
		$respuesta[$row['Empnom']] = utf8_encode(strtoupper($row['Empcod']));
	}
	return $respuesta;
}

function cargar_hiddens_para_autocomplete_cco()
{
	// --> CCOS
	global $conex;
	global $wbasedato;
	global $wtema;
	global $wmovhos;
	global $wcostosyp;
	$caracter_ok = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U", "");
	$caracter_ma = array("�","�","�","�","�","�","�","�","�","�","�","�","�","�",",","/","�","�","�","�","�","�","�","�","�","�","�","�","�","'","?�","??", "?�", "�");


	$arr_cco = array();
	$tipocco='tabcco';

	$wbasedatocyp = consultarAplicacion($conex, $wtema, $tipocco);

	if ($wbasedatocyp=='')
	{
		$wbasedatocyp =$wmovhos.'_000011';
	}
	
	
	


	$q_cco = " SELECT Ccocod AS codigo, Cconom AS nombre
				 FROM ".$wbasedatocyp."
				WHERE Ccoest = 'on' ";
	
	//---se puso quemado para luego ser borrado
	if($wbasedatocyp==$wcostosyp."_000005")
	{
		 $q_cco = $q_cco." AND  Ccoemp ='01'";
	}	
				
	$q_cco =	$q_cco." ORDER BY nombre ";

	$r_cco = mysql_query($q_cco,$conex) or die("Error en el query: ".$q_cco."<br>Tipo Error:".mysql_error());

	if(mysql_num_rows($r_cco)>0)
	{
		$arr_cco['*']	= 'Todos';
	}

	while($row_cco = mysql_fetch_array($r_cco))
	{
		$row_cco['nombre'] = str_replace($caracter_ma, $caracter_ok, $row_cco['nombre']);
		$arr_cco[trim($row_cco['codigo'])] = trim($row_cco['nombre']);
	}

	return ($arr_cco);

}

function cargar_hiddens_para_autocomplete_usuario()
{
	// --> CCOS
	global $conex;
	global $wbasedato;
	global $wtema;
	global $wemp_pmla;
	$arr_usu = array();

	$caracter_ok = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U", "");
	$caracter_ma = array("�","�","�","�","�","�","�","�","�","�","�","�","�","�",",","/","�","�","�","�","�","�","�","�","�","�","�","�","�","'","?�","??", "?�", "�");

	$wbasedato_talhuma = consultarAliasPorAplicacion($conex, $wemp_pmla, 'talhuma');


	$q_usu = 	" 		SELECT Ideuse as codigo, CONCAT_WS(' ', Ideno1, Ideno2, Ideap1 ,Ideap2) AS nombre
					      FROM ".$wbasedato_talhuma."_000013
						 WHERE Ideest != 'off'
				      ORDER BY nombre ";

	$r_usu = mysql_query($q_usu,$conex) or die("Error en el query: ".$q_usu."<br>Tipo Error:".mysql_error());

	if(mysql_num_rows($r_usu)>0)
	{
		$arr_usu['*']	= 'Todos';
	}

	while($row_usu = mysql_fetch_array($r_usu))
	{
		$row_usu['nombre'] = str_replace($caracter_ma, $caracter_ok, $row_usu['nombre']);
		$arr_usu[trim($row_usu['codigo'])] = trim(utf8_encode($row_usu['nombre']));
	}

	return ($arr_usu);

}

function consultarAplicacion($conexion, $codigoInstitucion, $nombreAplicacion){

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


if($inicial2 =='no' AND $operacion=='cambiaresultadoseps')
{
global $wmovhos;
$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);
	$q 	= "  SELECT DISTINCT(Encent),Epsnom "
		."     FROM ".$wbasedato."_000049  , ".$wmovhos."_000049"
		."    WHERE encfce  BETWEEN '".$wfechainicialeps."' AND  '".$wfechafinaleps."' "
		."      AND Encese= 'cerrado' "
		."      AND Encent LIKE Epsnom"
		." ORDER BY Encent ";
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());



	echo "<option value='seleccione'>Todas</option>";
	while($row  = mysql_fetch_array($res))
	{
		echo "<option value='".$row['Epsnom']."'>".$row['Encent']."</option>";
	}

	return;
}
if ($operacion =='traersemaforo' AND $inicial=='no')
{
	$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);
	$q = " SELECT  	Grusem "
		."   FROM  ".$wbasedato."_000051 "
		."  WHERE Grutem = ".$wtemareportes." ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);
	$tipodeformato = $row['Grusem'];

	echo $tipodeformato;
	return;

}


//--------------------------------------------------------------------------
//-------------------------------------------------------------------------
//-traeresultadov3 == traer resultado ventana 3
// obedece a la muestra de resultados de las preguntas especificamente.

if ($inicial=='no' AND $operacion=='traeresultadov3')
{
	global $wbasedatocli;
	// consulta el tema interno de los reportes  para consultar su tipo
	$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);
	$q = " SELECT  	Fortip "
		."   FROM  ".$wbasedato."_000042 "
		."  WHERE Forcod = ".$wtemareportes." ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);
	$tipodeformato = $row['Fortip'];


	// deacuerdo al tipo de evaluacion salen los resultados
	// tipo 01 	Evaluacion Interna.
	// tipo 04 	Evaluacion de Conocimientos.
	if($tipodeformato == '01' or $tipodeformato=='04')
	{
		//--------------------
		//--Consulta que trae  lo que se contesto a cada una de las preguntas , deacuerdo a la clasificacion de agrupaciones.
		//--Las tablas que tienen que ver en esta consulta son _000005 tabla de descriptores , _000007 tabla con el resultado de cada una de las preguntas
		//--000032 calificadores y calificados en un periodo, 000013 tabla de usuarios , _000051 que es de agrupaciones
		$qpreguntas = 	" 	  SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos,Grunom ,Grucod"
						."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 ,  ".$wbasedato."_000032 , talhuma_000013,".$wbasedato."_000051 "
						." 	   WHERE Desagr = '".$wcagrupacion."' "
						."   	 AND Evades = Descod"
						."   	 AND Mcauco = Evaevo"
						."   	 AND Ideuse = Mcauco "
						."		 AND Grucod = Desagr";

		//--- se a�aden filtros si hay campos para hacer la consulta mas especifica
		// si se manda centro de costos especifico entra
		if($wcentrocostos != 'todos')
		{
			$qpreguntas = $qpreguntas ."  AND Idecco = '".$wcentrocostos."' ";
		}
		// si se manda un usuario especifico
		if($wusuario_consultado!='')
		{
			$qpreguntas = $qpreguntas." AND Ideuse='".$wusuario_consultado."' ";
		}

		// ---- Se le agrega al where algo general  que es el periodo y el a�o  para buscar
		$qpreguntas = $qpreguntas ."    AND Mcaano = '".$wano."' AND Mcaper = '".$wperiodo."' "
								  ."	GROUP BY Descod";
	}
	//-------------- Si es del tipo
	// 	03 	Encuesta Usuario  Registrado
	if($tipodeformato == '03' )
	{

			// -- Hay dos tipos de encuestas que se manejan aqui ,
			// -- las que se debe seleccionar un periodo y un a�o
			// -- y las que se buscan por una fecha inicial y otra final

				// busqueda con periodo y a�o
				if ($wfechainicial=='')
				{
					//--Consulta que trae  lo que se contesto a cada una de las preguntas , deacuerdo a la clasificacion de agrupaciones.
					//--Las tablas que tienen que ver en esta consulta son _000005 tabla de descriptores , _000007 tabla con el resultado de cada una de las preguntas
					//--000049 encuestados en un periodo , _000051 que es de agrupaciones

					$qpreguntas = 	" 	  SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos ,Grucod,Grunom "
									."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 ,  ".$wbasedato."_000049   ,".$wbasedato."_000051  "
									." 	   WHERE  Desagr = '".$wcagrupacion."' "
									."   	 AND Evades = Descod"
									."   	 AND Evafco = Encenc "
									."   	 AND Enchis = Evaevo "
									."		 AND Grucod = Desagr";

					if($wcentrocostos != 'todos')
					{
						$qpreguntas = $qpreguntas ."  AND Enccco = '".$wcentrocostos."' ";
					}

					$qpreguntas = $qpreguntas ."  	 AND ".$wbasedato."_000049.Encano ='".$wano."'"
											  ."     AND ".$wbasedato."_000049.Encper ='".$wperiodo."'"
											  ."     AND Encese= 'cerrado'"
											  ."	 AND Evacal !=0  ";
				}
				else // busqueda con fechas determinadas
				{

					//--Consulta que trae  lo que se contesto a cada una de las preguntas , deacuerdo a la clasificacion de agrupaciones.
					//--Las tablas que tienen que ver en esta consulta son _000005 tabla de descriptores , _000007 tabla con el resultado de cada una de las preguntas
					//--000049 encuestados en un periodo , _000051 que es de agrupaciones
					$qpreguntas = 	" 			  SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos ,Grucod,Grunom "
											."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 , ".$wbasedato."_000049 ".$wbasedato."_000049  LEFT JOIN ".$wbasedatocli."_000024 ON Encdia = Empcod ,".$wbasedato."_000051  "
											." 	   WHERE  Desagr = '".$wcagrupacion."' "
											."   	 AND Evades = Descod"
											."   	 AND Evafco = Encenc "
											."   	 AND Enchis = Evaevo "
											."		 AND Grucod = Desagr";

					if($wcentrocostos != 'todos')
					{
						$qpreguntas = $qpreguntas ."  AND Enccco = '".$wcentrocostos."' ";
					}

					$qpreguntas = $qpreguntas ."  	 AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' "
											  ."     AND Encese= 'cerrado'"
											  ."	 AND Evacal !=0  ";

				}
			// se agrega esto a la consulta para que se busque por afinidad .
			if($wafinidad!='seleccione')
			{
				$qpreguntas = $qpreguntas . " AND Encafi LIKE  '%".$wafinidad."%' ";
			}

			// se agrega esto a la consulta para que se busque por entidad .
			if($wentidad!='seleccione')
			{
				$qpreguntas = $qpreguntas . " AND Encdia =  '".$entidades[$wentidad]."' ";
			}

			if($wtipodeempresa !='')
			{
				if ($wtipodeempresa =='PAP')
				{
					$wtipoempresa_buscar = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Empresas_PAP_servicio_usuario');
					$qpreguntas = $qpreguntas . " AND Emptem IN (".$wtipoempresa_buscar.")  ";

				}
				if ($wtipodeempresa =='particular')
				{
					$wtipoempresa_buscar = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Empresas_particular_servicio_usuario');
					$qpreguntas = $qpreguntas . " AND Emptem IN (".$wtipoempresa_buscar.")  ";


				}
				if ($wtipodeempresa =='pos')
				{

					$wtipoempresa_buscar_pap = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Empresas_PAP_servicio_usuario');
					$wtipoempresa_buscar_particular = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Empresas_particular_servicio_usuario');
					$qpreguntas = $qpreguntas . " AND Emptem NOT IN (".$wtipoempresa_buscar_pap.",".$wtipoempresa_buscar_particular.")  ";

				}


			}

			$qpreguntas = $qpreguntas ."	GROUP BY Descod";

	}
	//  05 	Evaluacion de invelca
	if($tipodeformato == '05' )
	{

			// -- Hay dos tipos de encuestas que se manejan aqui ,
			// -- las que se debe seleccionar un periodo y un a�o
			// -- y las que se buscan por una fecha inicial y otra final

				// busqueda con periodo y a�o
				if ($wfechainicial=='')
				{
					//--Consulta que trae  lo que se contesto a cada una de las preguntas , deacuerdo a la clasificacion de agrupaciones.
					//--Las tablas que tienen que ver en esta consulta son _000005 tabla de descriptores , _000007 tabla con el resultado de cada una de las preguntas
					//--000049 encuestados en un periodo , _000051 que es de agrupaciones

					$qpreguntas = 	" 	  SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos ,Grucod,Grunom "
									."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 ,  ".$wbasedato."_000049   ,".$wbasedato."_000051  "
									." 	   WHERE  Desagr = '".$wcagrupacion."' "
									."   	 AND Evades = Descod"
									."   	 AND Evafco = Encenc "
									."   	 AND Enchis = Evaevo "
									."		 AND Grucod = Desagr";

					if($wcentrocostos != 'todos')
					{
						$qpreguntas = $qpreguntas ."  AND Enccco = '".$wcentrocostos."' ";
					}

					$qpreguntas = $qpreguntas ."  	 AND ".$wbasedato."_000049.Encano ='".$wano."'"
											  ."     AND ".$wbasedato."_000049.Encper ='".$wperiodo."'"
											  ."     AND Encese= 'cerrado'"
											  ."	 AND Evacal !=0  ";
				}
				else // busqueda con fechas determinadas
				{

					//--Consulta que trae  lo que se contesto a cada una de las preguntas , deacuerdo a la clasificacion de agrupaciones.
					//--Las tablas que tienen que ver en esta consulta son _000005 tabla de descriptores , _000007 tabla con el resultado de cada una de las preguntas
					//--000049 encuestados en un periodo , _000051 que es de agrupaciones
					$qpreguntas = 	" 			  SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos ,Grucod,Grunom "
											."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 ,  ".$wbasedato."_000049  LEFT JOIN ".$wbasedatocli."_000024 ON Encdia = Empcod ,".$wbasedato."_000051  "
											." 	   WHERE  Desagr = '".$wcagrupacion."' "
											."   	 AND Evades = Descod"
											."   	 AND Evafco = Encenc "
											."   	 AND Enchis = Evaevo "
											."  	 AND Evaper = encper "
											."  	 AND Evaano = encano "
											."		 AND Grucod = Desagr";

					if($wcentrocostos != 'todos')
					{
						$qpreguntas = $qpreguntas ."  AND Enccco = '".$wcentrocostos."' ";
					}

					$qpreguntas = $qpreguntas ."  	 AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' "
											  ."     AND Encese= 'cerrado'"
											  ."	 AND Evacal !=0  ";

				}
			// se agrega esto a la consulta para que se busque por afinidad .
			if($wafinidad!='seleccione')
			{
				$qpreguntas = $qpreguntas . " AND Encafi LIKE  '%".$wafinidad."%' ";
			}

			// se agrega esto a la consulta para que se busque por entidad .
			if($wentidad!='seleccione')
			{
				$qpreguntas = $qpreguntas . " AND Encdia =  '".$entidades[$wentidad]."' ";
			}

			if($wtipodeempresa !='')
			{
				if ($wtipodeempresa =='PAP')
				{
					$wtipoempresa_buscar = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Empresas_PAP_servicio_usuario');
					$qpreguntas = $qpreguntas . " AND Emptem IN (".$wtipoempresa_buscar.")  ";

				}
				if ($wtipodeempresa =='particular')
				{
					$wtipoempresa_buscar = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Empresas_particular_servicio_usuario');
					$qpreguntas = $qpreguntas . " AND Emptem IN (".$wtipoempresa_buscar.")  ";


				}
				if ($wtipodeempresa =='pos')
				{

					$wtipoempresa_buscar_pap = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Empresas_PAP_servicio_usuario');
					$wtipoempresa_buscar_particular = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Empresas_particular_servicio_usuario');
					$qpreguntas = $qpreguntas . " AND Emptem NOT IN (".$wtipoempresa_buscar_pap.",".$wtipoempresa_buscar_particular.")  ";

				}


			}

			$qpreguntas = $qpreguntas ."	GROUP BY Descod";

	}

	$respreguntas = mysql_query($qpreguntas,$conex) or die ("Error 4: ".mysql_errno()." - en el query: ".$qpreguntas." - ".mysql_error());

	//--- tabla principal que muestra las respuestas
	echo"<table id='tabletresx-".$wcagrupacion."-".$wcentrocostos."'   class='tablasresize'>";
	echo"<tr><td><div><img width='20' height='19' src='../../images/medical/root/chart.png' onclick='pintarGrafica3(\"".$wcagrupacion."\",\"".$wcentrocostos."\")' ></div></td>";
	echo"<td>";
	echo"<table class='tablasresize' align='left' id='tabletres-".$wcagrupacion."-".$wcentrocostos."'><tr class='encabezadoTabla'><td>Preguntas</td><td>Nota</td></tr>";

	$auxclass=0;
	while($rowpreguntas = mysql_fetch_array($respreguntas))
	{
		$auxclass++;
		if (($auxclass%2)==0)
		{
			$wcf="fila1";  // color de fondo de la fila
		}
		else
		{
			$wcf="fila2"; // color de fondo de la fila
		}

		if($tipodeformato == '05')
		{
			$valorround = round($rowpreguntas['Suma'] / $rowpreguntas['Cuantos'] , 2);
		}
		else
		{
			if ($tipodeformato =='04')
			{
				// nuevo jc
				$valorround = round($rowpreguntas['Suma'] / $rowpreguntas['Cuantos'] , 2) * 5;
			}
			else
			{

				$valorround = round($rowpreguntas['Suma'] / $rowpreguntas['Cuantos'] , 2);
				$resultadoformula 	= array();
				$resultadoformula 	= AnalizarFormula( $wcagrupacion,$valorround);
				$valorround			= $resultadoformula['valor'];
				$simbolo			= $resultadoformula['simbolo'];
			}
			$color = traecolor($valorround ,$vectormaximo,$vectorminimo,$vectorcolor) ;

		}

		echo "<tr><td class='".$wcf."' class='tablasresize' align='left'>".$rowpreguntas['Desdes']."</td>
							<td align='right' bgcolor='".$color."' nowrap='nowrap' >";
		// table se hizo para que quedara alineado el valor con el boton mas
		echo "<table>
					<tr>
						<td>
							<div style='display: inline-block;'   class='msg_tooltipx-".$wcagrupacion."-".$wcentrocostos."' title='Respuestas para calculo ".$rowpreguntas['Cuantos']."' >".$valorround."  ".$simbolo."
						</td>
						<td>
							<img onclick='traeDetallePregunta(\"".$rowpreguntas['Descod']."\",\"".$wcagrupacion."\",\"".$wcentrocostos."\",\"".$wfechainicial."\",\"".$wfechafinal."\",\"".$elemento."\",\"".$tipodeformato."\",\"".$rowpreguntas['Cuantos']."\")' style='float: right; display: inline-block;' id='img_mas-".$wcagrupacion."-".$wcentrocostos."-".$rowpreguntas['Descod']."' src='../../images/medical/hce/mas.PNG'></div>
						</td>
					</tr>
			   </table>";
		//------------
		echo "	<div id='cuartaventana-".$wcagrupacion."-".$wcentrocostos."-".$rowpreguntas['Descod']."'></div>";
		echo "</td></tr>";

	}
	echo "</table>";
	echo "</td>";
	echo "</tr>";
	echo"<tr><td align='center' colspan=2>";
	echo"<div id='areatercera-".$wcagrupacion."-".$wcentrocostos."' ></div>";
	echo "</td></tr>";
	echo "</table>";
	return;

}
if ($inicial=='no' AND $operacion=='CargarSelectPreguntas')
{
	if($wagrupacion == 'todos')
	{
	}
	else
	{
			$q= 	 "    SELECT Descod,Desdes "
					."  	FROM ".$wbasedato."_000005 "
					."	   WHERE Desagr=".$wagrupacion." ";
					// ."     AND Desest !='off' ";
			$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	}
	echo "<select id='select_pregunta' style='width: 40em' >";
	echo "<option value='seleccione'>Todos</option>";

	while($row = mysql_fetch_array($res))
	{
		echo "<option value='".$row['Descod']."'>".htmlentities( $row['Desdes'] )."</option>";
	}
	echo "</select>";
	return;
}




//-- Carga el select de agrupaciones asociado a la clasificacion de agrupaciones seleccionada
if ($inicial=='no' AND $operacion=='CargarSelectAgrupacion')
{

	// Query que trae todas las agrupaciones segun la clasificacion seleccionada
	$q = "SELECT Grucod,Grunom "
		."  FROM ".$wbasedato."_000051"
		." WHERE Grutem = '".$wtemareportes."' "
		."   AND Grucag = '".$wclasificacionagrupacion."'
		ORDER BY Grunom";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	echo "<select id='select_agrupacion' onchange='CargarSelectPreguntas()'>";
	echo "<option value='todos||todos'>Todos</option>";

	while($row = mysql_fetch_array($res))
	{
		echo "<option value='".$row['Grunom']."||".$row['Grucod']."'>".$row['Grunom']."</option>";
	}
	echo "</select>";

	return;
}

//-- 19 mayo de 2016 se adiciona este proceso para  que  se escoja el semaforo deacuerdo al tema
//-- Carga el select de agrupaciones asociado a la clasificacion de agrupaciones seleccionada
if ($inicial=='no' AND $operacion=='cambiar_semaforo')
{

	// Query que trae todas las agrupaciones segun la clasificacion seleccionada
	$q = "SELECT Grucod,Grunom,Grusem"
		."  FROM ".$wbasedato."_000051"
		." WHERE Grutem = '".$wtemareportes."' "
		."   AND Grucag = '".$wclasificacionagrupacion."'
		ORDER BY Grunom";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	

	if($row = mysql_fetch_array($res))
	{
		 echo $row['Grusem'];
		 //echo $q;
	}
	else
	{
		//echo "1";
		echo "1";
		//echo $q;
		
	}

	return;
}

//--- aqui se trae los detalles en el mas bajo nivel de las preguntas contestadas.
//--- se puede mirar quien fue el que contesto y que valor contesto.
if ($inicial=='no' AND $operacion=='traeDetallePregunta')
{
	global $wbasedatocli;
    $q = " SELECT  	Fortip "
		."   FROM  ".$wbasedato."_000042 "
		."  WHERE Forcod = ".$wtemareportes." ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);
	$tipodeformato = $row['Fortip'];

	//Se establece la condicion-----------
	if ( strpos($wagrupacion, "|")!== false)
	{
		$numerodeagrupaciones = explode("|",$wagrupacion);
		$condicion = "(";
		$separador = '|**|';
		$o=0;
		while ($o < count($numerodeagrupaciones))
		{
			$condicion .= $separador. "Desagr = '".$numerodeagrupaciones[$o]."'";
			$separador = "||";
			$o++;
		}
		$condicion .= ")";

		$condicion = str_replace("|**|","",$condicion);
		$condicion = str_replace("||"," OR ",$condicion);

	}
	else
	{
		$condicion = " Desagr='".$wagrupacion."'";

	}
	//----------------------------------

	if (($tipodeformato == '01' || $tipodeformato == '04' ) && ($wcentrocostos!='todos')  )
	{
		$q= 	 "    SELECT Evaevo, Evaevr, Evacal,Ideno1,Ideno2,Ideap1,Ideap2,Evadat "
				."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 ,  ".$wbasedato."_000032 , talhuma_000013 "
				." 	   WHERE ".$condicion." "
				."   	 AND Evades = Descod"
				// ."       AND Desest !='off' "
				."       AND Evades = '".$wcodpregunta."' "
				."   	 AND Mcauco = Evaevo"
				."   	 AND Ideuse = Mcauco "
				."    	 AND Idecco = '".$wcentrocostos."' "
				."       AND Mcaano = '".$wano."' AND Mcaper = '".$wperiodo."' ";

		if($wusuario_consultado!='')
		{
			$q = $q." AND Ideuse='".$wusuario_consultado."' ";
		}

		$q=$q."  ORDER BY Evacal " ;


	}
	if (($tipodeformato == '01' || $tipodeformato == '04' ) && ($wcentrocostos=='todos')  )
	{
		$q= 	 "    SELECT Evaevo, Evaevr, Evacal,Ideno1,Ideno2,Ideap1,Ideap2,Evadat "
				."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 ,  ".$wbasedato."_000032 , talhuma_000013 "
				." 	   WHERE ".$condicion." "
				."   	 AND Evades = Descod"
				."       AND Evades = '".$wcodpregunta."' "
				."   	 AND Mcauco = Evaevo"
				."   	 AND Ideuse = Mcauco "
				." 		 AND Mcaano = '".$wano."' AND Mcaper = '".$wperiodo."' "
				."  ORDER BY Evacal " ;

	}
	else if ( ($tipodeformato == '03' || $tipodeformato == '05') && ($wcentrocostos!='todos'))
	{
		// para el tipo de evaluacion
		/*
		03- Encuesta Usuario Registrado
		05- Evaluacion de invelca
		*/
		if ($tipodeformato == '03')
		{
			if($wfechainicial =='')
			{
				$q = " 	  SELECT Encno1,Encno2, Encap1, Encap2, Evaevo, Evaevr, Evacal,Evadat,Comstr  "
					."  	FROM ".$wbasedato."_000005,  ".$wbasedato."_000049, ".$wbasedato."_000007  LEFT JOIN  ".$wbasedato."_000036  ON  Comdes = Evades AND Comucm = Evaevo "
					." 	   WHERE ".$condicion." "
					."   	 AND Evades = Descod"
					."   	 AND Evafco = Encenc "
					."       AND Evades = '".$wcodpregunta."' "
					."   	 AND Enchis = Evaevo "
					."       AND Evacal != 0 "
					."       AND Encese= 'cerrado'"
					."    	 AND Enccco = '".$wcentrocostos."' "
					."  	 AND ".$wbasedato."_000049.Encper ='".$wperiodo."'"
					."  	 AND ".$wbasedato."_000049.Encano ='".$wano."'
							 AND Evaper =".$wbasedato."_000049.Encper
							 AND Evaano = ".$wbasedato."_000049.Encano	";
			}
			else
			{
				 $q = " 	  SELECT Encno1,Encno2, Encap1, Encap2, Evaevo, Evaevr, Evacal,Evadat,Comstr  "
					."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000049 LEFT JOIN ".$wbasedatocli."_000024 ON Encdia = Empcod , ".$wbasedato."_000007  LEFT JOIN  ".$wbasedato."_000036  ON  Comdes = Evades AND Comucm = Evaevo "
					." 	   WHERE ".$condicion." "
					."   	 AND Evades = Descod"
					."   	 AND Evafco = Encenc "
					."       AND Evades = '".$wcodpregunta."' "
					."   	 AND Enchis = Evaevo "
					."       AND Evacal != 0 "
					."       AND Encese= 'cerrado'"
					."    	 AND Enccco = '".$wcentrocostos."' "
					."  	 AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' ";
			
			}
		}
		else
		{
			if($wfechainicial =='')
			{
				$q = " 	  SELECT Encno1,Encno2, Encap1, Encap2, Evaevo, Evaevr, Evacal,Evadat,Comstr  "
					."  	FROM ".$wbasedato."_000005,  ".$wbasedato."_000049, ".$wbasedato."_000007  LEFT JOIN  ".$wbasedato."_000036  ON  Comdes = Evades AND Comucm = Evaevo  "
					." 	   WHERE ".$condicion." "
					."   	 AND Evades = Descod"
					."   	 AND Evafco = Encenc "
					."       AND Evades = '".$wcodpregunta."' "
					."   	 AND Enchis = Evaevo "
					."  	 AND Evaper = encper "
					."  	 AND Evaano = encano "
					."       AND Evacal != 0 "
					."       AND Encese= 'cerrado'"
					." 		 AND Comper = encper"
					."    	 AND Enccco = '".$wcentrocostos."' "
					."  	 AND ".$wbasedato."_000049.Encper ='".$wperiodo."'"
					."  	 AND ".$wbasedato."_000049.Encano ='".$wano."'
							 AND Evaper =".$wbasedato."_000049.Encper
							 AND Evaano = ".$wbasedato."_000049.Encano	";
			}
			else
			{
				$q = " 	  SELECT Encno1,Encno2, Encap1, Encap2, Evaevo, Evaevr, Evacal,Evadat,Comstr  "
					."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000049 LEFT JOIN ".$wbasedatocli."_000024 ON Encdia = Empcod , ".$wbasedato."_000007  LEFT JOIN  ".$wbasedato."_000036  ON  Comdes = Evades AND Comucm = Evaevo"
					." 	   WHERE ".$condicion." "
					."   	 AND Evades = Descod"
					."   	 AND Evafco = Encenc "
					."       AND Evades = '".$wcodpregunta."' "
					."   	 AND Enchis = Evaevo "
					."  	 AND Evaper = encper "
					."  	 AND Evaano = encano "
					."       AND Evacal != 0 "
					." 		 AND Comper = encper"
					."       AND Encese= 'cerrado'"
					."    	 AND Enccco = '".$wcentrocostos."' "
					."  	 AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' ";
			}


		}

		// se agrega esto a la consulta para que se busque por afinidad .
		if($wafinidad!='seleccione')
		{
			$q = $q . " AND Encafi LIKE  '%".$wafinidad."%' ";
		}

		// se agrega esto a la consulta para que se busque por entidad .
		if($wentidad!='seleccione')
		{
			$q = $q . " AND Encdia =  '".$entidades[$wentidad]."' ";
		}

		if($wtipodeempresa !='')
		{
			if ($wtipodeempresa =='PAP')
			{
				$wtipoempresa_buscar = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Empresas_PAP_servicio_usuario');
				$q = $q . " AND Emptem IN (".$wtipoempresa_buscar.")  ";

			}
			if ($wtipodeempresa =='particular')
			{
				$wtipoempresa_buscar = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Empresas_particular_servicio_usuario');
				$q = $q . " AND Emptem IN (".$wtipoempresa_buscar.")  ";


			}
			if ($wtipodeempresa =='pos')
			{

				$wtipoempresa_buscar_pap = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Empresas_PAP_servicio_usuario');
				$wtipoempresa_buscar_particular = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Empresas_particular_servicio_usuario');
				$q = $q . " AND Emptem NOT IN (".$wtipoempresa_buscar_pap.",".$wtipoempresa_buscar_particular.")  ";

			}


		}

		$q = $q ."  ORDER BY Evacal ,Encno1,Encno2, Encap1, Encap2 " ;



	}
	else if ( ($tipodeformato == '03' || $tipodeformato == '05') && ($wcentrocostos=='todos'))
	{

		// para el tipo de evaluacion
		/*
		03- Encuesta Usuario Registrado
		05- Evaluacion de invelca
		*/
		if ( $tipodeformato == '03')
		{
			$q = " 	  SELECT Encno1,Encno2, Encap1, Encap2, Evaevo, Evaevr, Evacal,Evadat,Comstr "
				."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000049 LEFT JOIN ".$wbasedatocli."_000024 ON Encdia = Empcod  , ".$wbasedato."_000007  LEFT JOIN  ".$wbasedato."_000036  ON ( Comdes = Evades  AND Comucm = Evaevo ) "
				." 	   WHERE ".$condicion."  "
				."   	 AND Evades = Descod"
				."   	 AND Evafco = Encenc "
				."       AND Evades = '".$wcodpregunta."' "
				."   	 AND Enchis = Evaevo "
				."       AND Encese= 'cerrado'"
				."       AND Evacal != 0 "
				."  	 AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' ";
		}
		else
		{
			$q = " 	  SELECT Encno1,Encno2, Encap1, Encap2, Evaevo, Evaevr, Evacal,Evadat,Comstr "
				."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000049 LEFT JOIN ".$wbasedatocli."_000024 ON Encdia = Empcod  , ".$wbasedato."_000007  LEFT JOIN  ".$wbasedato."_000036  ON  Comdes = Evades AND Comucm = Evaevo "
				." 	   WHERE ".$condicion."  "
				."   	 AND Evades = Descod"
				."   	 AND Evafco = Encenc "
				."       AND Evades = '".$wcodpregunta."' "
				."   	 AND Enchis = Evaevo "
				."  	 AND Evaper = encper "
				."  	 AND Evaano = encano "
				."       AND Encese= 'cerrado'"
				."       AND Evacal != 0 "
				." 		 AND Comper = encper"
				."  	 AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' ";


		}
		// se agrega esto a la consulta para que se busque por afinidad .
		if($wafinidad!='seleccione')
		{
			$q = $q . " AND Encafi LIKE  '%".$wafinidad."%' ";
		}

		// se agrega esto a la consulta para que se busque por entidad .
		if($wentidad!='seleccione')
		{
			$q = $q . " AND Encdia =  '".$entidades[$wentidad]."' ";
		}

		if($wtipodeempresa !='')
		{
			if ($wtipodeempresa =='PAP')
			{
				$wtipoempresa_buscar = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Empresas_PAP_servicio_usuario');
				$q = $q . " AND Emptem IN (".$wtipoempresa_buscar.")  ";

			}
			if ($wtipodeempresa =='particular')
			{
				$wtipoempresa_buscar = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Empresas_particular_servicio_usuario');
				$q = $q . " AND Emptem IN (".$wtipoempresa_buscar.")  ";


			}
			if ($wtipodeempresa =='pos')
			{

				$wtipoempresa_buscar_pap = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Empresas_PAP_servicio_usuario');
				$wtipoempresa_buscar_particular = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Empresas_particular_servicio_usuario');
				$q = $q . " AND Emptem NOT IN (".$wtipoempresa_buscar_pap.",".$wtipoempresa_buscar_particular.")  ";

			}


		}

		$q = $q ."  ORDER BY Evacal ,Encno1,Encno2, Encap1, Encap2" ;


	}

	
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	//tabla respuesta detalle de preguntas ( persona que contesto y que contesto)
	// para el tipo de evaluacion
	/*
	03- Encuesta Usuario Registrado
	05- Evaluacion de invelca
	*/
	if($tipodeformato=='03' || $tipodeformato == '05')
	{


		$tablarespuestas ;
		$tablarespuestas  = "<table align='center' id='tablex-".$wcodpregunta."-".$wagrupacion."-".$wcentrocostos."'>";
		$tablarespuestas .= "<tr>";
		$tablarespuestas .="<td>";


		$tablarespuestas .= "<table align='center' cellspacing=1 cellpadding=0 id='table".$wcodpregunta."' style='padding:4px; background-color: white;'>" ;

		$tablarespuestas .= "<tr>";
		$tablarespuestas .= "<td class='encabezadoTabla'>Paciente </td><td class='encabezadoTabla'>Que contesto</td><td class='encabezadoTabla'>Valor</td><td class='encabezadoTabla' >Porcentaje</td>";
		$tablarespuestas .= "</tr>";

		$k=0;
		$l=0;
		$contador=1;
		$auxiliar ='';
		while($row = mysql_fetch_array($res))
		{
			if (($k%2)==0)
			{
				$wcf="fila1";  // color de fondo de la fila
			}
			else
			{
				$wcf="fila2"; // color de fondo de la fila
			}
			if ($auxiliar != $row['Evacal'])
			{
				$l=0;
				 $tablarespuestas  = str_replace ( 'cambiarrowspan', "".$contador."" , $tablarespuestas );
				 @$tablarespuestas  = str_replace ( 'resultado', "Numero de respuestas: ".$contador." <br> ".Round((($contador / $wnumeropreguntas) * 100),2)."% " , $tablarespuestas );
				 $tablarespuestas  = str_replace ( 'estilo', "".$wcf."" , $tablarespuestas );
				 $contador = 1;
			}
			else
			{
				$l++;
				$contador++;
			}

			$auxiliar = $row['Evacal'];

			$tablarespuestas .= "<tr align='left'>";
			if($row['Comstr']=='')
			{
				$colorcomentario='black';
			}
			else
			{
				$colorcomentario='gray';
			}
			$tablarespuestas .= "<td class='estilo' >".$row['Encno1']." ".$row['Encno2']." ".$row['Encap1']." ".$row['Encap2']."</td><td class='estilo'>".$row['Evadat']."</td><td align='center' class='estilo'  title='".$row['Comstr']."'  style='cursor: pointer; color: ".$colorcomentario."' ><div>".$row['Evacal']."</div>";
			if($row['Comstr']!='')
			{
				$tablarespuestas .="<div title='".$row['Comstr']."'>Comentario</div>";
			}
			$tablarespuestas .= "</td>";

			if($l==0)
			{
				$tablarespuestas .="<td rowspan='cambiarrowspan'  class='estilo' align='center' >resultado</td>";
				$k++;
			}

			$tablarespuestas .= "</tr>";
		}
		$tablarespuestas  = str_replace ( 'cambiarrowspan', "".$contador."" , $tablarespuestas );
		@$tablarespuestas  = str_replace ( 'resultado', "Numero de respuestas: ".$contador." <br>".Round((($contador / $wnumeropreguntas) *100),2)."%" , $tablarespuestas );
		$tablarespuestas  = str_replace ( 'estilo', "".$wcf."" , $tablarespuestas );
		$tablarespuestas .= "</table>";
		$tablarespuestas .= "</td></tr></table>";

		echo $tablarespuestas;
	}
	else if($tipodeformato=='01' )
	{
		echo "<table align='center' id='tablex-".$wcodpregunta."-".$wagrupacion."-".$wcentrocostos."'>";
		echo "<tr>";
		echo "<td><img width='20' height='19' src='../../images/medical/root/chart.png' onclick='pintarGrafica4(\"".$wagrupacion."\",\"".$wcentrocostos."\",\"".$wcodpregunta."\")' ></div></td>";
		echo "<td>";
		echo "<table align='center' id='table-".$wcodpregunta."-".$wagrupacion."-".$wcentrocostos."'>" ;

		echo "<tr>";
		echo "<td class='encabezadoTabla'>Empleado</td><td class='encabezadoTabla'>Puntaje</td>";
		echo "</tr>";

		while($row = mysql_fetch_array($res))
		{
			echo "<tr align='left'>";
			echo "<td class='fila1' nowrap='nowrap'>".$row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2']."</td><td class='fila1' align='center'>".$row['Evacal']."</td>";
			echo "</tr>";
		}
		echo "</table>";
		echo "</td>";
		echo "</tr>";
		echo "<tr><td colspan='2'  align='center' ><div id='areacuatro-".$wcodpregunta."-".$wagrupacion."-".$wcentrocostos."'></div></td></tr>";
		echo "</table>";

	}
	else if($tipodeformato=='04' )
	{

	    $q2 = "SELECT  Calmax "
			."  FROM  ".$wbasedato."_000034 "
			." WHERE  Calfor = '".$wtemareportes."' "
			."   AND  Calest = 'on' ";

		$res2 = mysql_query($q2,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q2." - ".mysql_error());

		$row2 = mysql_fetch_array($res2);

		$multiplicador = 5;

		echo "<table align='center' id='tablex-".$wcodpregunta."-".$wagrupacion."-".$wcentrocostos."'>";
		echo "<tr>";
		echo "<td class='encabezadoTabla'>Empleado</td><td class='encabezadoTabla'>Que contesto</td><td class='encabezadoTabla'>Valor</td>";
		echo "</tr>";



		while($row = mysql_fetch_array($res))
		{
			echo "<tr align='left'>";
			echo "<td class='fila1' >".$row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2']."</td><td class='fila1'>".$row['Evadat']."</td><td class='fila1' align='center'>".(($row['Evacal']) * ($multiplicador))."</td>";
			echo "</tr>";
		}
		echo "</table>";

	}

	return;
}
// llena la ventana modal con el detalle de cada pregunta contestada con su respectivo promedio
if ($inicial=='no' AND $operacion=='traeResultadosPorPregunta')
{
	global $wbasedatocli;
	$q = " SELECT  	Fortip "
		."   FROM  ".$wbasedato."_000042 "
		."  WHERE Forcod = ".$wtemareportes." ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);
	$tipodeformato = $row['Fortip'];



	if($wnombrecco=='')
	{
		$wnombrecco = "Todos";
	}

	echo "<br>";
	//Se establece la condicion-----------
	if ( strpos($wagrupacion, "|") !== false)
	{
		$numerodeagrupaciones = explode("|",$wagrupacion);
		$condicion = "(";
		$separador = '|**|';
		$o=0;
		while ($o < count($numerodeagrupaciones))
		{
			$condicion .= $separador. "Desagr = '".$numerodeagrupaciones[$o]."'";
			$separador = "||";
			$o++;
		}
		$condicion .= ")";

		$condicion = str_replace("|**|","",$condicion);
		$condicion = str_replace("||"," OR ",$condicion);

	}
	else
	{
		$condicion = " Desagr='".$wagrupacion."'";
		$numerodeagrupaciones[0]=$wagrupacion;

	}
	//----------------------------------

	$qsemaforo =  "SELECT Grusem "
				."   FROM ".$wbasedato."_000051"
				."  WHERE Grutem = '".$wtemareportes."' "
				."    AND Grucod = '".$numerodeagrupaciones[0]."' ";

	$ressemaforo = mysql_query($qsemaforo,$conex) or die ("Error 1: ".mysql_errno()." - en el query: ".$qsemaforo." - ".mysql_error());
	$rowsemaforo = mysql_fetch_array($ressemaforo);
	$semaforo = $rowsemaforo['Grusem'];
	$semaforo = '1';


	$qcolor =    " SELECT Msecod, Msenom ,Dsecol,Dsemax,Dsemin"
				."   FROM ".$wbasedato."_000052, ".$wbasedato."_000053 "
				."  WHERE Msecod = Dsecod "
				."    AND Msecod = '".$t_semaforo."' ";



	$rescolor = mysql_query($qcolor,$conex) or die ("Error 2: ".mysql_errno()." - en el query: ".$qcolor." - ".mysql_error());

	while($rowcolor = mysql_fetch_array($rescolor))
	{
		$vectormaximo[] = $rowcolor['Dsemax'];
		$vectorminimo[] = $rowcolor['Dsemin'];
		$vectorcolor[] = $rowcolor['Dsecol'];
	}
	if($tipodeformato !='01' &&  $tipodeformato !='03' &&  $tipodeformato !='04' &&  $tipodeformato !='05')
	{
		echo "<br>";
		echo "<table align='center'>";
		echo "<tr>";
		echo "<td colspan='2' class='encabezadoTabla'>Semaforo</td>";
		echo "</tr>";
		$i=0;
		while($i<count($vectormaximo))
		{
			echo "<tr>";
			echo "<td class='encabezadoTabla' >Nota entre  ".$vectorminimo[$i]." y ".$vectormaximo[$i]."</td>";
			echo "<td bgcolor='".$vectorcolor[$i]."'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";
			echo "</tr>";
			$i++;
		}
		echo "</table>";
	}

	$vectorpreguntas=array();
	$vectorvalores = array();
	$vectorvaloresagrupacion = array();
	$vectorcuantos = array();
	$vectorcuantosnoaplica = array();



	switch($tipodeformato)
    {
        //
		case '01':

		if($wcentrocostos=='todos')
		{

			$qpreguntas = 	" SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos,Desagr"
						."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 ,  ".$wbasedato."_000032 , talhuma_000013 "
						." 	   WHERE Desagr =  "
						."   	 AND Evades = Descod"
						."   	 AND Mcauco = Evaevo"
						."   	 AND Ideuse = Mcauco "
						."       AND Mcaano = '".$wano."' AND Mcaper = '".$wperiodo."' ";

			if($wusuario_consultado!='')
			{
				$qpreguntas = $qpreguntas." AND Ideuse='".$wusuario_consultado."' ";
			}

			$qpreguntas =		$qpreguntas."	GROUP BY Descod"
											   ."   ORDER BY Desagr, Descod";

			$qpreguntastotal = 	     " 	  SELECT SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos "
									."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 ,  ".$wbasedato."_000032 , talhuma_000013 "
									." 	   WHERE  ".$condicion." "
									."       AND Evades = Descod"
									."   	 AND Mcauco = Evaevo"
									."   	 AND Ideuse = Mcauco "
									."  	 AND Mcaano = '".$wano."' AND Mcaper = '".$wperiodo."' ";

			if($wusuario_consultado!='')
			{
				$qpreguntastotal = $qpreguntastotal." AND Ideuse='".$wusuario_consultado."' ";
			}


		}
		else
		{
			$qpreguntas = 	" SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos,Grunom ,Grucod"
						."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 ,  ".$wbasedato."_000032 , talhuma_000013,".$wbasedato."_000051 "
						." 	   WHERE Desagr = valoracambiar "
						."   	 AND Evades = Descod"
						."   	 AND Mcauco = Evaevo"
						."   	 AND Ideuse = Mcauco "
						."		 AND Grucod = Desagr"
						."    	 AND Idecco = '".$wcentrocostos."' "
						." 		 AND Mcaano = '".$wano."' AND Mcaper = '".$wperiodo."' ";
			if($wusuario_consultado!='')
			{
				$qpreguntas = $qpreguntas." AND Ideuse='".$wusuario_consultado."' ";
			}

			$qpreguntas = $qpreguntas."	GROUP BY Descod";

			$qpreguntastotal = 	" SELECT SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos "
							."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 ,  ".$wbasedato."_000032 , talhuma_000013 "
							." 	   WHERE ".$condicion." "
							." 		 AND Evades = Descod"
							."   	 AND Mcauco = Evaevo"
							."   	 AND Ideuse = Mcauco "
							."    	 AND Idecco = '".$wcentrocostos."' "
							."		 AND Mcaano = '".$wano."' AND Mcaper = '".$wperiodo."' ";

			if($wusuario_consultado!='')
			{
				$qpreguntastotal = $qpreguntastotal." AND Ideuse='".$wusuario_consultado."' ";
			}

		}

		break;
		case '03' :
		if($wcentrocostos=='todos')
		{

			if ($wfechainicial=='')
			{

				$qpreguntas = 	" SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos ,Grucod,Grunom"
							."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 , ".$wbasedato."_000049 ,".$wbasedato."_000051 "
							." 	   WHERE Desagr = valoracambiar "
							."   	 AND Evades = Descod"
							."   	 AND Evafco = Encenc "
							."   	 AND Enchis = Evaevo "
							."		 AND Grucod = Desagr"
							."  	 AND ".$wbasedato."_000049.Encper ='".$wperiodo."'"
							."  	 AND ".$wbasedato."_000049.Encano ='".$wano."'"
							."       AND Encese= 'cerrado' ";



			}
			else
			{

				$qpreguntas = 	" SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos ,Grucod,Grunom"
							."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 ,  ".$wbasedato."_000049 LEFT JOIN ".$wbasedatocli."_000024 ON Encdia = Empcod  ,".$wbasedato."_000051 "
							." 	   WHERE Desagr = valoracambiar "
							."   	 AND Evades = Descod"
							."   	 AND Evafco = Encenc "
							."   	 AND Enchis = Evaevo "
							."		 AND Grucod = Desagr"
							."  	 AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' "
							."       AND Encese= 'cerrado' ";
			}




			$qpreguntas2 =  " SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos,Grucod,Grunom "
						."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 , ".$wbasedato."_000049  LEFT JOIN ".$wbasedatocli."_000024 ON Encdia = Empcod  ,".$wbasedato."_000051  "
						." 	   WHERE Desagr = valoracambiar "
						."   	 AND Evades = Descod"
						."   	 AND Evafco = Encenc "
						."		 AND Grucod = Desagr"
						."   	 AND Enchis = Evaevo "
						."  	 AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' "
						."       AND Encese= 'cerrado'";

			$qpreguntasconnoaplica = $qpreguntas ;
			$qpreguntas =  $qpreguntas." AND Evacal != 0 ";
			// se agrega esto a la consulta para que se busque por afinidad .
			if($wafinidad!='seleccione')
			{
				$qpreguntas = $qpreguntas . " AND Encafi LIKE  '%".$wafinidad."%' ";
				$qpreguntas2 = $qpreguntas2 . "      AND Encafi LIKE  '%".$wafinidad."%' ";
				$qpreguntasconnoaplica = $qpreguntasconnoaplica ." AND Encafi LIKE  '%".$wafinidad."%' ";
			}

			// se agrega esto a la consulta para que se busque por entidad .
			if($wentidad!='seleccione')
			{
				$qpreguntas = $qpreguntas . " AND Encdia =  '".$entidades[$wentidad]."' ";
				$qpreguntas2 = $qpreguntas2 . " AND Encdia =  '".$entidades[$wentidad]."' ";
				$qpreguntasconnoaplica = $qpreguntasconnoaplica . " AND Encdia =  '".$entidades[$wentidad]."' ";
			}

			if($wspregunta != 'seleccione')
			{
				$qpreguntas = $qpreguntas . " AND Descod = '".$wspregunta."' ";
				$qpreguntas2 = $qpreguntas2 . " AND Descod = '".$wspregunta."' ";
				$qpreguntasconnoaplica = $qpreguntasconnoaplica ." AND Descod = '".$wspregunta."' ";
			}

			if($wtipodeempresa !='')
			{
				if ($wtipodeempresa =='PAP')
				{
					$wtipoempresa_buscar = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Empresas_PAP_servicio_usuario');
					$qpreguntas = $qpreguntas . " AND Emptem IN (".$wtipoempresa_buscar.")  ";
					$qpreguntas2 = $qpreguntas2 . " AND Emptem IN (".$wtipoempresa_buscar.")  ";
				}
				if ($wtipodeempresa =='particular')
				{
					$wtipoempresa_buscar = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Empresas_particular_servicio_usuario');
					$qpreguntas = $qpreguntas . " AND Emptem IN (".$wtipoempresa_buscar.")  ";
					$qpreguntas2 = $qpreguntas2 . " AND Emptem IN (".$wtipoempresa_buscar.")  ";

				}
				if ($wtipodeempresa =='pos')
				{

					$wtipoempresa_buscar_pap = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Empresas_PAP_servicio_usuario');
					$wtipoempresa_buscar_particular = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Empresas_particular_servicio_usuario');
					$qpreguntas = $qpreguntas . " AND Emptem NOT IN (".$wtipoempresa_buscar_pap.",".$wtipoempresa_buscar_particular.")  ";
					$qpreguntas2 = $qpreguntas2 . " AND Emptem NOT IN (".$wtipoempresa_buscar_pap.",".$wtipoempresa_buscar_particular.")  ";

				}


			}

			$qpreguntas = $qpreguntas ."	GROUP BY Descod";
			$qpreguntas2 = $qpreguntas2 ."	GROUP BY Descod HAVING Suma = 0"
										." ORDER  BY Descod";
			$qpreguntasconnoaplica = $qpreguntasconnoaplica ."	GROUP BY Descod";

			$qpreguntas = $qpreguntas." UNION ". $qpreguntas2;

			$qpreguntastotal = 	     " 	  SELECT SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos ,Grucod,Grunom "
									."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 , ".$wbasedato."_000049  "
									." 	   WHERE ".$condicion." "
									."		 AND Evades = Descod"
									."   	 AND Evafco = Encenc "
									."		 AND Grucod = Desagr"
									."   	 AND Enchis = Evaevo "
									// ."       AND Desest !='off' "
									."  	 AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' "
									."       AND Encese= 'cerrado'";

			$qpreguntastotalnoaplica = $qpreguntastotal ;
			$qpreguntastotal = $qpreguntastotal ."       AND Evacal != 0 ";

			// se agrega esto a la consulta para que se busque por afinidad .
			if($wafinidad!='seleccione')
			{
				$qpreguntastotal = $qpreguntastotal . " AND Encafi LIKE  '%".$wafinidad."%' ";
				$qpreguntastotalnoaplica = $qpreguntastotalnoaplica ." AND Encafi LIKE  '%".$wafinidad."%' ";
			}

			// se agrega esto a la consulta para que se busque por entidad .
			if($wentidad!='seleccione')
			{
				$qpreguntastotal = $qpreguntastotal . " AND Encdia =  '".$entidades[$wentidad]."' ";
				$qpreguntastotalnoaplica = $qpreguntastotalnoaplica ." AND Encdia =  '".$entidades[$wentidad]."' ";
			}

			// se agrega esto a la consulta para que se busque por pregunta .
			if($wspregunta != 'seleccione')
			{
				$qpreguntastotal = $qpreguntastotal . " AND Descod = '".$wspregunta."' ";
				$qpreguntastotalnoaplica = $qpreguntastotalnoaplica . " AND Descod = '".$wspregunta."' ";
			}

			$qtotalxagrupacion = " 	  SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos "
								."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 , ".$wbasedato."_000049  "
								." 	   WHERE ".$condicion." "
								."		 AND Evades = Descod"
								."   	 AND Evafco = Encenc "
								."   	 AND Enchis = Evaevo "
								."  	 AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' "
								."       AND Encese= 'cerrado'";

			$qtotalxagrupacionnoaplica = $qtotalxagrupacion;
			$qtotalxagrupacion = $qtotalxagrupacion . "    AND Evacal != 0 ";


			if($wspregunta != 'seleccione')
			{
				$qtotalxagrupacion = $qtotalxagrupacion . " AND Descod = '".$wspregunta."' ";
				$qtotalxagrupacionnoaplica = $qtotalxagrupacionnoaplica . " AND Descod = '".$wspregunta."' ";
			}

			$qtotalxagrupacion = $qtotalxagrupacion ."	GROUP BY Descod";
			$qtotalxagrupacionnoaplica = $qtotalxagrupacionnoaplica ."	GROUP BY Descod";

			$qtotal = 	     " 	  SELECT SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos "
									."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 , ".$wbasedato."_000049  "
									." 	   WHERE ".$condicion." "
									."       AND Evades = Descod"
									."   	 AND Evafco = Encenc "
									."   	 AND Enchis = Evaevo "
									// ."       AND Desest !='off' "
									."       AND Evacal != 0 "
									."  	 AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' "
									."       AND Encese= 'cerrado'";

			if($wspregunta != 'seleccione')
			{
				$qtotal = $qtotal . " AND Descod = '".$wspregunta."' ";
			}

		}
		else
		{
			if ($wfechainicial=='')
			{

				$qpreguntas = 	" 		  SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos ,Grucod,Grunom"
									."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 , ".$wbasedato."_000049 ,".$wbasedato."_000051  "
									." 	   WHERE Desagr = valoracambiar "
									."   	 AND Evades = Descod"
									."   	 AND Evafco = Encenc "
									."   	 AND Enchis = Evaevo "
									."		 AND Grucod = Desagr"
									// ."       AND Desest !='off' "
									."    	 AND Enccco = '".$wcentrocostos."' "
									."  	 AND ".$wbasedato."_000049.Encper ='".$wperiodo."'"
									."  	 AND ".$wbasedato."_000049.Encano ='".$wano."'"
									."       AND Encese= 'cerrado'";


			}
			else
			{

				$qpreguntas = 	" 		  SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos ,Grucod,Grunom"
									."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 , ".$wbasedato."_000049  LEFT JOIN ".$wbasedatocli."_000024 ON Encdia = Empcod ,".$wbasedato."_000051  "
									." 	   WHERE Desagr = valoracambiar "
									."   	 AND Evades = Descod"
									."   	 AND Evafco = Encenc "
									."   	 AND Enchis = Evaevo "
									."		 AND Grucod = Desagr"
									// ."       AND Desest !='off' "
									."    	 AND Enccco = '".$wcentrocostos."' "
									."  	 AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' "
									."       AND Encese= 'cerrado'";
			}

			$qpreguntas2 =  " 			  SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos ,Grucod,Grunom"
									."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 , ".$wbasedato."_000049  LEFT JOIN ".$wbasedatocli."_000024 ON Encdia = Empcod ,".$wbasedato."_000051   "
									." 	   WHERE ".$condicion." "
									."   	 AND Evades = Descod"
									."   	 AND Evafco = Encenc "
									."   	 AND Enchis = Evaevo "
									."		 AND Grucod = Desagr"
									// ."       AND Desest !='off' "
									."    	 AND Enccco = '".$wcentrocostos."' "
									."  	 AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' "
									."       AND Encese= 'cerrado'";

			$qpreguntasconnoaplica = $qpreguntas ;
			$qpreguntas =  $qpreguntas ."       AND Evacal != 0 ";

			// se agrega esto a la consulta para que se busque por afinidad .
			if($wafinidad!='seleccione')
			{
				$qpreguntas = $qpreguntas . " AND Encafi LIKE  '%".$wafinidad."%' ";
				$qpreguntas2 = $qpreguntas2 . " AND Encafi LIKE  '%".$wafinidad."%' ";
				$qpreguntasconnoaplica = $qpreguntasconnoaplica .  " AND Encafi LIKE  '%".$wafinidad."%' ";
			}

			// se agrega esto a la consulta para que se busque por entidad .
			if($wentidad!='seleccione')
			{
				$qpreguntas = $qpreguntas . " AND Encdia =  '".$entidades[$wentidad]."' ";
				$qpreguntas2 = $qpreguntas2 . " AND Encdia =  '".$entidades[$wentidad]."' ";
				$qpreguntasconnoaplica = $qpreguntasconnoaplica ." AND Encdia =  '".$entidades[$wentidad]."' ";
			}

			// se agrega esto a la consulta para que se busque por pregunta .
			if($wspregunta != 'seleccione')
			{
				$qpreguntas = $qpreguntas . " AND Descod = '".$wspregunta."' ";
				$qpreguntas2 = $qpreguntas2 . " AND Descod = '".$wspregunta."' ";
				$qpreguntasconnoaplica = $qpreguntasconnoaplica ." AND Descod = '".$wspregunta."' ";
			}

			if($wtipodeempresa !='')
			{
				if ($wtipodeempresa =='PAP')
				{
					$wtipoempresa_buscar = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Empresas_PAP_servicio_usuario');
					$qpreguntas = $qpreguntas . " AND Emptem IN (".$wtipoempresa_buscar.")  ";
					$qpreguntas2 = $qpreguntas2 . " AND Emptem IN (".$wtipoempresa_buscar.")  ";
					$qpreguntasconnoaplica = $qpreguntasconnoaplica . " AND Emptem IN (".$wtipoempresa_buscar.")  ";
				}
				if ($wtipodeempresa =='particular')
				{
					$wtipoempresa_buscar = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Empresas_particular_servicio_usuario');
					$qpreguntas = $qpreguntas . " AND Emptem IN (".$wtipoempresa_buscar.")  ";
					$qpreguntas2 = $qpreguntas2 . " AND Emptem IN (".$wtipoempresa_buscar.")  ";
					$qpreguntasconnoaplica = $qpreguntasconnoaplica . " AND Emptem IN (".$wtipoempresa_buscar.")  ";

				}
				if ($wtipodeempresa =='pos')
				{

					$wtipoempresa_buscar_pap = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Empresas_PAP_servicio_usuario');
					$wtipoempresa_buscar_particular = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Empresas_particular_servicio_usuario');
					$qpreguntas = $qpreguntas . " AND Emptem NOT IN (".$wtipoempresa_buscar_pap.",".$wtipoempresa_buscar_particular.")  ";
					$qpreguntas2 = $qpreguntas2 . " AND Emptem NOT IN (".$wtipoempresa_buscar_pap.",".$wtipoempresa_buscar_particular.")  ";
					$qpreguntasconnoaplica = $qpreguntasconnoaplica . " AND Emptem NOT IN (".$wtipoempresa_buscar_pap.",".$wtipoempresa_buscar_particular.")  ";

				}


			}

		}
		break;

		case '04':

		if($wcentrocostos=='todos')
		{

			$qpreguntas = 	" 			  SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos,Desagr"
									."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 ,  ".$wbasedato."_000032 , talhuma_000013 "
									." 	   WHERE Desagr =  "
									."   	 AND Evades = Descod"
									."   	 AND Mcauco = Evaevo"
									."   	 AND Ideuse = Mcauco "
									// ."       AND Desest !='off' "
									."       AND Mcaano = '".$wano."' AND Mcaper = '".$wperiodo."' ";


			if($wusuario_consultado!='')
			{
				$qpreguntas = $qpreguntas." AND Ideuse='".$wusuario_consultado."' ";
			}

			$qpreguntas=	$qpreguntas."	 GROUP BY Descod"
									."   ORDER BY Desagr, Descod";

			$qpreguntastotal = 	     " 	  SELECT SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos "
									."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 ,  ".$wbasedato."_000032 , talhuma_000013 "
									." 	   WHERE  ".$condicion." "
									."       AND Evades = Descod"
									."   	 AND Mcauco = Evaevo"
									."   	 AND Ideuse = Mcauco "
									."  	 AND Mcaano = '".$wano."' AND Mcaper = '".$wperiodo."' ";

			if($wusuario_consultado!='')
			{
				$qpreguntastotal = $qpreguntastotal." AND Ideuse='".$wusuario_consultado."' ";
			}



		}
		else
		{
			$qpreguntas = 	" 			  SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos,Grunom ,Grucod"
									."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 ,  ".$wbasedato."_000032 , talhuma_000013,".$wbasedato."_000051 "
									." 	   WHERE Desagr = valoracambiar "
									."   	 AND Evades = Descod"
									."   	 AND Mcauco = Evaevo"
									."   	 AND Ideuse = Mcauco "
									// ."       AND Desest !='off' "
									."		 AND Grucod = Desagr"
									."    	 AND Idecco = '".$wcentrocostos."' "
								//	."  	 AND ".$wbasedato."_000032.fecha_data  BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' "
									." 		 AND Mcaano = '".$wano."' AND Mcaper = '".$wperiodo."' ";
			if($wusuario_consultado!='')
			{
				$qpreguntas = $qpreguntas." AND Ideuse='".$wusuario_consultado."' ";
			}

			$qpreguntas	= $qpreguntas."	GROUP BY Descod";

			$qpreguntastotal = 	" 		  SELECT SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos "
									."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 ,  ".$wbasedato."_000032 , talhuma_000013 "
									." 	   WHERE ".$condicion." "
									." 		 AND Evades = Descod"
									."   	 AND Mcauco = Evaevo"
									."   	 AND Ideuse = Mcauco "
									// ."       AND Desest !='off' "
									."    	 AND Idecco = '".$wcentrocostos."' "
									."		 AND Mcaano = '".$wano."' AND Mcaper = '".$wperiodo."' ";

			if($wusuario_consultado!='')
			{
				$qpreguntastotal = $qpreguntastotal." AND Ideuse='".$wusuario_consultado."' ";
			}
		}

		break;
		// tipo 05- supervicion tecnica invecla
		case '05' :
		if($wcentrocostos=='todos')
		{

			if ($wfechainicial=='')
			{
				$qpreguntas = 	" 			  SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos ,Grucod,Grunom"
										."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 , ".$wbasedato."_000049 ,".$wbasedato."_000051 "
										." 	   WHERE Desagr = valoracambiar "
										."   	 AND Evades = Descod"
										."   	 AND Evafco = Encenc "
										."   	 AND Enchis = Evaevo "
										."  	 AND Evaper = encper "
										."  	 AND Evaano = encano "
										."		 AND Grucod = Desagr"
										// ."       AND Desest !='off' "
										."  	 AND ".$wbasedato."_000049.Encper ='".$wperiodo."'"
										."  	 AND ".$wbasedato."_000049.Encano ='".$wano."'"
										."       AND Encese= 'cerrado' ";

			}
			else
			{

			$qpreguntas = 	" 			  SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos ,Grucod,Grunom"
										."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 ,  ".$wbasedato."_000049 LEFT JOIN ".$wbasedatocli."_000024 ON Encdia = Empcod  ,".$wbasedato."_000051 "
										." 	   WHERE Desagr = valoracambiar "
										."   	 AND Evades = Descod"
										."   	 AND Evafco = Encenc "
										."   	 AND Enchis = Evaevo "
										."  	 AND Evaper = encper "
										."  	 AND Evaano = encano "
										."		 AND Grucod = Desagr"
										."  	 AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' "
										."       AND Encese= 'cerrado' ";
			}

			$qpreguntas2 =  " 			  SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos,Grucod,Grunom "
									."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 , ".$wbasedato."_000049  LEFT JOIN ".$wbasedatocli."_000024 ON Encdia = Empcod  ,".$wbasedato."_000051  "
									." 	   WHERE Desagr = valoracambiar "
									."   	 AND Evades = Descod"
									."   	 AND Evafco = Encenc "
									."		 AND Grucod = Desagr"
									."   	 AND Enchis = Evaevo "
									."   	 AND Evaper = encper "
									."   	 AND Evaano = encano "
									."  	 AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' "
									."       AND Encese= 'cerrado'";


			$qpreguntasconnoaplica = $qpreguntas ;
			$qpreguntas =  $qpreguntas." AND Evacal != 0 ";
			// se agrega esto a la consulta para que se busque por afinidad .
			if($wafinidad!='seleccione')
			{
				$qpreguntas = $qpreguntas . " AND Encafi LIKE  '%".$wafinidad."%' ";

				$qpreguntas2 = $qpreguntas2 . "      AND Encafi LIKE  '%".$wafinidad."%' ";
				$qpreguntasconnoaplica = $qpreguntasconnoaplica ." AND Encafi LIKE  '%".$wafinidad."%' ";
			}

			// se agrega esto a la consulta para que se busque por entidad .
			if($wentidad!='seleccione')
			{
				$qpreguntas = $qpreguntas . " AND Encdia =  '".$entidades[$wentidad]."' ";
				$qpreguntas2 = $qpreguntas2 . " AND Encdia =  '".$entidades[$wentidad]."' ";
				$qpreguntasconnoaplica = $qpreguntasconnoaplica . " AND Encdia =  '".$entidades[$wentidad]."' ";
			}

			if($wspregunta != 'seleccione')
			{
				$qpreguntas = $qpreguntas . " AND Descod = '".$wspregunta."' ";
				$qpreguntas2 = $qpreguntas2 . " AND Descod = '".$wspregunta."' ";
				$qpreguntasconnoaplica = $qpreguntasconnoaplica ." AND Descod = '".$wspregunta."' ";
			}

			if($wtipodeempresa !='')
			{
				if ($wtipodeempresa =='PAP')
				{
					$wtipoempresa_buscar = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Empresas_PAP_servicio_usuario');
					$qpreguntas = $qpreguntas . " AND Emptem IN (".$wtipoempresa_buscar.")  ";
					$qpreguntas2 = $qpreguntas2 . " AND Emptem IN (".$wtipoempresa_buscar.")  ";
				}
				if ($wtipodeempresa =='particular')
				{
					$wtipoempresa_buscar = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Empresas_particular_servicio_usuario');
					$qpreguntas = $qpreguntas . " AND Emptem IN (".$wtipoempresa_buscar.")  ";
					$qpreguntas2 = $qpreguntas2 . " AND Emptem IN (".$wtipoempresa_buscar.")  ";

				}
				if ($wtipodeempresa =='pos')
				{

					$wtipoempresa_buscar_pap = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Empresas_PAP_servicio_usuario');
					$wtipoempresa_buscar_particular = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Empresas_particular_servicio_usuario');
					$qpreguntas = $qpreguntas . " AND Emptem NOT IN (".$wtipoempresa_buscar_pap.",".$wtipoempresa_buscar_particular.")  ";
					$qpreguntas2 = $qpreguntas2 . " AND Emptem NOT IN (".$wtipoempresa_buscar_pap.",".$wtipoempresa_buscar_particular.")  ";

				}


			}

			$qpreguntas = $qpreguntas ."	GROUP BY Descod";
			$qpreguntas2 = $qpreguntas2 ."	GROUP BY Descod HAVING Suma = 0"
										." ORDER  BY Descod";
			$qpreguntasconnoaplica = $qpreguntasconnoaplica ."	GROUP BY Descod";

			$qpreguntas = $qpreguntas." UNION ". $qpreguntas2;


			$qpreguntastotal = 	     " 	  SELECT SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos ,Grucod,Grunom "
									."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 , ".$wbasedato."_000049  "
									." 	   WHERE ".$condicion." "
									."		 AND Evades = Descod"
									."   	 AND Evafco = Encenc "
									."		 AND Grucod = Desagr"
									."   	 AND Evaper = encper "
									."       AND Evaano = encano "
									."   	 AND Enchis = Evaevo "
									// ."       AND Desest !='off' "
									."  	 AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' "
									."       AND Encese= 'cerrado'";

			$qpreguntastotalnoaplica = $qpreguntastotal ;
			$qpreguntastotal = $qpreguntastotal ."       AND Evacal != 0 ";

			// se agrega esto a la consulta para que se busque por afinidad .
			if($wafinidad!='seleccione')
			{
				$qpreguntastotal = $qpreguntastotal . " AND Encafi LIKE  '%".$wafinidad."%' ";
				$qpreguntastotalnoaplica = $qpreguntastotalnoaplica ." AND Encafi LIKE  '%".$wafinidad."%' ";
			}

			// se agrega esto a la consulta para que se busque por entidad .
			if($wentidad!='seleccione')
			{
				$qpreguntastotal = $qpreguntastotal . " AND Encdia =  '".$entidades[$wentidad]."' ";
				$qpreguntastotalnoaplica = $qpreguntastotalnoaplica ." AND Encdia =  '".$entidades[$wentidad]."' ";
			}

			// se agrega esto a la consulta para que se busque por pregunta .
			if($wspregunta != 'seleccione')
			{
				$qpreguntastotal = $qpreguntastotal . " AND Descod = '".$wspregunta."' ";
				$qpreguntastotalnoaplica = $qpreguntastotalnoaplica . " AND Descod = '".$wspregunta."' ";
			}

			$qtotalxagrupacion = " 	  SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos "
								."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 , ".$wbasedato."_000049  "
								." 	   WHERE ".$condicion." "
								."		 AND Evades = Descod"
								."   	 AND Evafco = Encenc "
								."       AND Evaper = encper "
								."       AND Evaano = encano "
								// ."       AND Desest !='off' "
								."   	 AND Enchis = Evaevo "
								."  	 AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' "
								."       AND Encese= 'cerrado'";

			$qtotalxagrupacionnoaplica = $qtotalxagrupacion;
			$qtotalxagrupacion = $qtotalxagrupacion . "    AND Evacal != 0 ";


			if($wspregunta != 'seleccione')
			{
				$qtotalxagrupacion = $qtotalxagrupacion . " AND Descod = '".$wspregunta."' ";
				$qtotalxagrupacionnoaplica = $qtotalxagrupacionnoaplica . " AND Descod = '".$wspregunta."' ";
			}

			$qtotalxagrupacion = $qtotalxagrupacion ."	GROUP BY Descod";
			$qtotalxagrupacionnoaplica = $qtotalxagrupacionnoaplica ."	GROUP BY Descod";

			$qtotal = 	     " 	  SELECT SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos "
									."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 , ".$wbasedato."_000049  "
									." 	   WHERE ".$condicion." "
									."       AND Evades = Descod"
									."   	 AND Evafco = Encenc "
									."   	 AND Enchis = Evaevo "
									."       AND Evaper = encper "
									."       AND Evaano = encano "
									."       AND Evacal != 0 "
									."  	 AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' "
									."       AND Encese= 'cerrado'";

			if($wspregunta != 'seleccione')
			{
				$qtotal = $qtotal . " AND Descod = '".$wspregunta."' ";
			}

		}
		else
		{
			if ($wfechainicial=='')
			{

				$qpreguntas = 	" 		  SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos ,Grucod,Grunom"
									."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 , ".$wbasedato."_000049 ,".$wbasedato."_000051  "
									." 	   WHERE Desagr = valoracambiar "
									."   	 AND Evades = Descod"
									."   	 AND Evafco = Encenc "
									."   	 AND Enchis = Evaevo "
									."       AND Evaper = encper "
									."       AND Evaano = encano "
									."		 AND Grucod = Desagr"
									."    	 AND Enccco = '".$wcentrocostos."' "
									."  	 AND ".$wbasedato."_000049.Encper ='".$wperiodo."'"
									."  	 AND ".$wbasedato."_000049.Encano ='".$wano."'"
									."       AND Encese= 'cerrado'";


			}
			else
			{

				$qpreguntas = 	" 		  SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos ,Grucod,Grunom"
									."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 , ".$wbasedato."_000049  LEFT JOIN ".$wbasedatocli."_000024 ON Encdia = Empcod ,".$wbasedato."_000051  "
									." 	   WHERE Desagr = valoracambiar "
									."   	 AND Evades = Descod"
									."   	 AND Evafco = Encenc "
									."   	 AND Enchis = Evaevo "
									."       AND Evaper = encper "
									."       AND Evaano = encano "
									."		 AND Grucod = Desagr"
									."    	 AND Enccco = '".$wcentrocostos."' "
									."  	 AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' "
									."       AND Encese= 'cerrado'";
			}

			$qpreguntas2 =  " 			  SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos ,Grucod,Grunom"
									."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 , ".$wbasedato."_000049  LEFT JOIN ".$wbasedatocli."_000024 ON Encdia = Empcod ,".$wbasedato."_000051   "
									." 	   WHERE ".$condicion." "
									."   	 AND Evades = Descod"
									."   	 AND Evafco = Encenc "
									."   	 AND Enchis = Evaevo "
									."       AND Evaper = encper "
									."       AND Evaano = encano "
									."		 AND Grucod = Desagr"
									."    	 AND Enccco = '".$wcentrocostos."' "
									."  	 AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' "
									."       AND Encese= 'cerrado'";

			$qpreguntasconnoaplica = $qpreguntas ;
			$qpreguntas =  $qpreguntas ."       AND Evacal != 0 ";

			// se agrega esto a la consulta para que se busque por afinidad .
			if($wafinidad!='seleccione')
			{
				$qpreguntas = $qpreguntas . " AND Encafi LIKE  '%".$wafinidad."%' ";
				$qpreguntas2 = $qpreguntas2 . " AND Encafi LIKE  '%".$wafinidad."%' ";
				$qpreguntasconnoaplica = $qpreguntasconnoaplica .  " AND Encafi LIKE  '%".$wafinidad."%' ";
			}

			// se agrega esto a la consulta para que se busque por entidad .
			if($wentidad!='seleccione')
			{
				$qpreguntas = $qpreguntas . " AND Encdia =  '".$entidades[$wentidad]."' ";
				$qpreguntas2 = $qpreguntas2 . " AND Encdia =  '".$entidades[$wentidad]."' ";
				$qpreguntasconnoaplica = $qpreguntasconnoaplica ." AND Encdia =  '".$entidades[$wentidad]."' ";
			}

			// se agrega esto a la consulta para que se busque por pregunta .
			if($wspregunta != 'seleccione')
			{
				$qpreguntas = $qpreguntas . " AND Descod = '".$wspregunta."' ";
				$qpreguntas2 = $qpreguntas2 . " AND Descod = '".$wspregunta."' ";
				$qpreguntasconnoaplica = $qpreguntasconnoaplica ." AND Descod = '".$wspregunta."' ";
			}

			if($wtipodeempresa !='')
			{
				if ($wtipodeempresa =='PAP')
				{
					$wtipoempresa_buscar = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Empresas_PAP_servicio_usuario');
					$qpreguntas = $qpreguntas . " AND Emptem IN (".$wtipoempresa_buscar.")  ";
					$qpreguntas2 = $qpreguntas2 . " AND Emptem IN (".$wtipoempresa_buscar.")  ";
					$qpreguntasconnoaplica = $qpreguntasconnoaplica . " AND Emptem IN (".$wtipoempresa_buscar.")  ";
				}
				if ($wtipodeempresa =='particular')
				{
					$wtipoempresa_buscar = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Empresas_particular_servicio_usuario');
					$qpreguntas = $qpreguntas . " AND Emptem IN (".$wtipoempresa_buscar.")  ";
					$qpreguntas2 = $qpreguntas2 . " AND Emptem IN (".$wtipoempresa_buscar.")  ";
					$qpreguntasconnoaplica = $qpreguntasconnoaplica . " AND Emptem IN (".$wtipoempresa_buscar.")  ";

				}
				if ($wtipodeempresa =='pos')
				{

					$wtipoempresa_buscar_pap = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Empresas_PAP_servicio_usuario');
					$wtipoempresa_buscar_particular = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Empresas_particular_servicio_usuario');
					$qpreguntas = $qpreguntas . " AND Emptem NOT IN (".$wtipoempresa_buscar_pap.",".$wtipoempresa_buscar_particular.")  ";
					$qpreguntas2 = $qpreguntas2 . " AND Emptem NOT IN (".$wtipoempresa_buscar_pap.",".$wtipoempresa_buscar_particular.")  ";
					$qpreguntasconnoaplica = $qpreguntasconnoaplica . " AND Emptem NOT IN (".$wtipoempresa_buscar_pap.",".$wtipoempresa_buscar_particular.")  ";

				}


			}

		}
		break;
		default :
        break;
	}




		$auxqpreguntas = $qpreguntas ;
		$auxqpreguntasconnoaplica = $qpreguntasconnoaplica;
		echo "<table id='tablesegundax-".$windicador."'>";
		echo "<tr><td><img width='20' height='19' src='../../images/medical/root/chart.png' onclick='pintarGrafica2(\"".$windicador."\")' ></div></td>";
		echo "<td>";
		echo "<div align='right' ><input type='button' onclick='abrirtodos()' value='Abrir todos'></div><br>";
		echo "<table align = 'center' id='tablesegunda-".$windicador."'>";
		echo "<tr><td  class='encabezadoTabla' >Agrupacion </td><td  class='encabezadoTabla' >Valor</td></tr>";
		$auxclass=0;
		foreach($numerodeagrupaciones as $claveagrupacion => $valoragrupacion	)
		{
			$auxclass++;
			if (($auxclass%2)==0)
			{
				$wcf="fila1";  // color de fondo de la fila
			}
			else
			{
				$wcf="fila2"; // color de fondo de la fila
			}

			$qpreguntas =  $auxqpreguntas;
			$qpreguntas =str_replace("valoracambiar",$valoragrupacion ,$qpreguntas) ;
			$respreguntas = mysql_query($qpreguntas,$conex) or die ("Error 4: ".mysql_errno()." - en el query: ".$qpreguntas." - ".mysql_error());
			$valortotalagru = 0;
			$cuantostotalagru = 0;
			while($rowpreguntas = mysql_fetch_array($respreguntas))
			{
				$vectorpreguntas[$rowpreguntas['Descod']] = $rowpreguntas['Desdes'];
				$vectorcodpreguntas[] = $rowpreguntas['Descod'];

				$valor = $rowpreguntas['Suma'] ;
				$valorround =  round($valor,2);


				$valor = $valorround;
				$valortotalagru = $valortotalagru + $valorround;




				$cuantostotalagru = $cuantostotalagru + $rowpreguntas['Cuantos'];
				$nombreagrupacion = $rowpreguntas['Grunom'];
				$codigoagrupacion = $rowpreguntas['Grucod'];
			}
			if($tipodeformato=='03')
			{
				$cuantostotalagrunoaplica = 0;
				$qpreguntasconnoaplica = $auxqpreguntasconnoaplica;
				$qpreguntasconnoaplica =str_replace("valoracambiar",$valoragrupacion ,$qpreguntasconnoaplica) ;
				$respreguntasnoaplica =  mysql_query($qpreguntasconnoaplica,$conex) or die ("Error 4: ".mysql_errno()." - en el query: ".$qpreguntasconnoaplica." - ".mysql_error());
				while($rowpreguntasnoaplica = mysql_fetch_array($respreguntasnoaplica))
				{
					$cuantostotalagrunoaplica = $cuantostotalagrunoaplica + $rowpreguntasnoaplica['Cuantos'];
				}
			}

			@$totalvalorsobrecuantos= $valortotalagru/$cuantostotalagru;



			$resultadoformula 				= array();
			$resultadoformula 				= AnalizarFormula( $codigoagrupacion,$totalvalorsobrecuantos);
			$totalvalorsobrecuantos			= $resultadoformula['valor'];
			$simbolo						= $resultadoformula['simbolo'];
			$entre						= $resultadoformula['entre'];
			// if($aplicar_formula)
			// {
				// $totalvalorsobrecuantos  = (($totalvalorsobrecuantos/5) * 100);
			// }


			$totalvalorsobrecuantos =   round($totalvalorsobrecuantos,2);
			if($tipodeformato=='04')
			{
				// nuevo -jc
				$totalvalorsobrecuantos = $totalvalorsobrecuantos *5;
			}

			$color = traecolor($totalvalorsobrecuantos ,$vectormaximo,$vectorminimo,$vectorcolor);

			if($cuantostotalagru != '0')
			{
				echo "<tr>";
				echo "<td class='".$wcf."' align='left' >".$nombreagrupacion."</td>";
				echo "<td bgcolor='".$color."' align='center' >";
				//------
				// para el tipo de evaluacion
				/*
				03- Encuesta Usuario Registrado
				05- Evaluacion de invelca
				*/
				if($tipodeformato=='03' or $tipodeformato=='05'  )
				{
					echo "<div class='msg_tooltip1".$windicador." clasventan3'  agrupacion='".$codigoagrupacion."'  centrocostos='".$wcentrocostos."'  fechaini='".$wfechainicial."' fechafin='".$wfechafinal."' title='Respuestas para c&aacute;lculo ".$cuantostotalagru."<br> Respuestas Totales ".$cuantostotalagrunoaplica." '  onclick='abrir3ventana(\"".$codigoagrupacion."\",\"".$wcentrocostos."\" ,\"".$wfechainicial."\",\"".$wfechafinal."\")'>".$totalvalorsobrecuantos." ".$simbolo." <img style='float: right; valign' id='img_mas-".$codigoagrupacion."-".$wcentrocostos."' src='../../images/medical/hce/mas.PNG'> </div>";
				}else
				{
					echo "<div class='msg_tooltip1".$windicador." clasventan3'   agrupacion='".$codigoagrupacion."'  centrocostos='".$wcentrocostos."'  fechaini='".$wfechainicial."' fechafin='".$wfechafinal."' title='Respuestas para c&aacute;lculo ".$cuantostotalagru."'  onclick='abrir3ventana(\"".$codigoagrupacion."\",\"".$wcentrocostos."\" ,\"".$wfechainicial."\",\"".$wfechafinal."\")'>".$totalvalorsobrecuantos." <img id='img_mas-".$codigoagrupacion."-".$wcentrocostos."' style='float: right; valign' src='../../images/medical/hce/mas.PNG'></div>";
				}
				//---------------------
				echo "<div id='tercerventana-".$codigoagrupacion."-".$wcentrocostos."'></div>";
				echo "</td>";
				echo "</tr>";
			}

		}
		echo "</table >";
		echo "</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td colspan='2' align='center'>";
		echo"<div id='areasegunda-".$windicador."' ></div>";
		echo "</td>";
		echo "</tr>";
		echo "</table >";


	return;
}

// trae la clasificacion de agrupaciones
// y otros campos como el a�o y el periodo , para ser llenado por el usuario
if ($inicial=='no' AND $operacion=='mostrarAgrupaciones' )
{


	echo "<table align = 'center'>";
	//-----------------------------
	echo "<tr align='center' class='fila1'>";
	echo "<td align='Left'>Centro de costos</td>";
	echo "<td align='left' colspan='2'>";

	echo "<input type='text'  id='buscador_cco' size='40' >
		  <img width='12'  border='0' height='12' title='Busque el nombre o parte del nombre del centro de costo' src='../../images/medical/HCE/lupa.PNG'>";
	echo "</td>";

	echo "</tr>";

	//-- Trae todas la  Clasificacion de agrupaciones
	$q = "SELECT DISTINCT(Cagcod),Cagdes "
		."  FROM ".$wbasedato."_000057"
		." WHERE Cagest = 'on'
		ORDER BY cagdes ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	echo "<tr align='center' class='fila1'>";
	echo "<td align='Left'>Clasificacion Agrupaciones</td>";
	echo "<td align='left' colspan='2'>";
	echo "<select id='select_clasificacionagrupacion' onchange='CargarSelectAgrupacion()'>";
	echo "<option value='seleccione'>Seleccione</option>";
	echo "<option value='todos'>Todos</option>";

	while($row =mysql_fetch_array($res))
	{
		echo "<option value='".$row['Cagcod']."'>".htmlentities( $row['Cagdes'] )."</option>";
	}
	echo "<select>";
	echo "</td>";

	echo "</tr>";

	echo "<tr align='left' >";
	echo "<td class='fila1'>Agrupaci&oacute;n</td>";
	echo "<td class='fila1' align='left'  colspan='2'><div id='div_select_agrupacion'><select id='select_agrupacion' style='width: 40em'>";
	echo "<option value='seleccione'>Seleccione</option>";
	echo "</select></div></td>";
	echo "</tr>";

	echo "<tr align='left' >";
	echo "<td class='fila1'>Pregunta</td>";
	echo "<td class='fila1' align='left'  colspan='2'><div id='div_select_pregunta'><select id='select_pregunta' style='width: 40em'>";
	echo "<option value='seleccione'>Seleccione</option>";
	echo "</select></div></td>";
	echo "</tr>";

	$fechaactual= date('Y-m-d');
	$wfecha_i = $fechaactual;
	$wfecha_f = $fechaactual;

	$porperiodo = consultarAliasPorAplicacion($conex, $wemp_pmla, 'buscarporperiodo');

	//-- Si la base de datos es especificamente  encumage
	if($porperiodo == $wbasedato)
	{
		$q=  "SELECT Perano, Perper "
		    ."  FROM ".$wbasedato."_000009 "
		    ." WHERE Perfor = '".$wtemareportes."' ";

		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

		echo "<tr align='left'>";
		echo "<td class='fila1'>Periodo</td>";
		echo "<td class='fila1'  colspan='2'<div id='selectperiodo' ><Select id='selectperiodos'>";
		echo "<option value='nada||nada'> Seleccione </option>";
		while($row = mysql_fetch_array($res))
		{
			echo "<option value='".$row['Perano']."||".$row['Perper']."'>".$row['Perano']."-".$row['Perper']."</option>";
		}
		echo "</select></div></td>";
		echo "</tr>";

		echo "	<tr class='fila1'>
					<td>
						Codigo usuario
					</td>
					<td>
						<input type='text'  id='buscador_usuario'>
						<img width='12'  border='0' height='12' title='Busque el nombre o parte del nombre del centro de costo' src='../../images/medical/HCE/lupa.PNG'>
					</td>
				</tr>";

	}
	else
	{
		echo "<tr align='left'>";
		echo "<td align='left' class='fila1' >Periodo 1<input type='checkbox' id='periodo1' value='si' checked></td><td class='fila1' align='center'>Fecha Inicial: ";
		campofechaDefecto("wfecha_i",$wfecha_i);
		echo "</td>";
		echo "<td align='center' class='fila1'>Fecha final: ";
		campofechaDefecto("wfecha_f",$wfecha_f);
		echo "</td>";
		echo "</tr>";
		echo "<tr align='left'>";
		echo "<td  class='fila1' align='left'>Periodo 2<input type='checkbox' id='periodo2' value='si' ></td><td class='fila1' align='center'>Fecha Inicial: ";
		campofechaDefecto("wfecha_i1",$wfecha_i);
		echo "</td>";
		echo "<td class='fila1' align='center'>Fecha final: ";
		campofechaDefecto("wfecha_f1",$wfecha_f);
		echo "</td>";
		echo "</tr>";

	}

	//-- Si la base de datos es especificamente  encumage
	//-- trae campos asociados a esta. Afinidad, empresa , es VIP o no
	if($wbasedato=='encumage')
	{


		echo"<tr align='left'>";
		echo"<td class='fila1' colspan='1'>Tipo de Servicio</td>";
		echo"<td class='fila1' colspan='2'>Todos&nbsp;<input type='radio' name='servicio' id='servicio_todos' checked='checked'  value='si'>Hospitalario&nbsp;<input type='radio' name='servicio' id='servicio_hospitalario' value='si'>&nbsp;Terapeuticos&nbsp;<input type='radio' name='servicio' id='servicio_terapeuticos' value='si'>&nbsp;Urgencias&nbsp;<input type='radio' name='servicio' id='servicio_urgencias' value='si' >&nbsp;Cirugia&nbsp;<input type='radio' name='servicio' id='servicio_cirugia' value='si'>&nbsp;Ayudas diagnosticas&nbsp;<input type='radio' name='servicio'  id='servicio_ayudas' value='si'></td>";
		echo"</tr>";

		echo"<tr>";
		echo"<td class='fila1' colspan='1'>Tipo de Empresa</td>";
		echo"<td class='fila1' colspan='2'>Todos&nbsp;<input type='radio' name='tempresa' id='tempresa_todos' checked='checked'  value='si'>Particular&nbsp;<input type='radio' name='tempresa' id='tempresa_particular' value='si'>&nbsp;P.A.P&nbsp;<input type='radio' name='tempresa' id='tempresa_pap' value='si'>&nbsp;POS&nbsp;<input type='radio' name='tempresa' id='tempresa_pos' value='si'></td>";
		echo"</tr>";

		echo "<tr align='Left' >";
		echo "<td class='fila1'>Entidad</td>";

		$q =   "  SELECT Empnom "
			  ."  FROM ".$wbasedatocli."_000024 "
			  ."  WHERE Empnom != '' "
			  ."  ORDER BY Empnom";

		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		echo "<td colspan='1' class='fila1'>";

		echo "<div id='epsselect'><select id='select_entidad' name='select_entidad' multiple='multiple' style='width:400px'>";
		echo "<option value='seleccione' selected>Todas</option>";
		while($row =mysql_fetch_array($res))
		{
			echo "<option value='".($row['Empnom'])."'>".($row['Empnom'])."</option>";
		}
		echo "</select>";
		echo "</div></td>";
		echo "<td class='fila1'></td>";
		echo "</tr>";


		echo "<tr align='Left' >";
		echo "<td class='fila1'>Afinidad</td>";
		echo "<td class='fila1' align='left'  colspan='2'><select id='select_afinidad'>";
		echo "<option value='seleccione'>Todos</option>";
		echo "<option value='AFI'>Afinidad</option>";
		echo "<option value='VIP'>VIP</option>";
		echo "</select></td>";
		echo "</tr>";
	}
	echo"<tr align='left'>";
	echo"<td class='fila1' colspan='3' align='center'>";
	echo"<input type='button' value='Consultar' onclick='verReporte()' >";
	echo"</td>";
	echo"</tr>";
	echo "</table>";

	return;

}

if ($inicial=='no' AND $operacion=='traeResultadosReporte')
{
	if($wclasificacionagrupacion != '')
	{
		//vector donde se van almacenar las agrupaciones
		$vectoragrupaciones = array();
		// vector donde se guarda los nombres de las agrupaciones
		$vectornomagrupaciones = array();
		$vectorcondiciones     = array(); 

		$q1 ="  SELECT Cagcod,Cagdes "
			."     FROM ".$wbasedato."_000057 ";

		if($wclasificacionagrupacion != 'todos')
			 $q1= $q1." WHERE Cagcod = '".$wclasificacionagrupacion."' ";

		$q1= $q1." ORDER BY Cagcod" ;

		$res1 = mysql_query($q1,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q1." - ".mysql_error());

		$s=0;
		while($row1 = mysql_fetch_array($res1))
		{
			$q2 =" SELECT Grucod,Grunom,Grucag "
				."   FROM ".$wbasedato."_000051"
				."  WHERE Grutem = '".$wtemareportes."' "
				."    AND Grucag = '".$row1['Cagcod']."' ";

			if($wcagrupacion !='todos')
				$q2= $q2." AND Grucod='".$wcagrupacion."'" ;

			$res2 = mysql_query($q2,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q2." - ".mysql_error());
			$separador = "*|"; // se utiliza para separar
			while($row2 = mysql_fetch_array($res2))
			{
				//$auxvector=$row2['Grucod'];
				$vectoragrupaciones[$row2['Grucag']][] = $row2['Grucod'];
				if($wcagrupacion !='todos')
					$vectornomagrupaciones[$row2['Grucag']]=$row2['Grunom'];
				else
					$vectornomagrupaciones[$row2['Grucag']]=$row1['Cagdes'];

				$separador = "|";
			}
			//$vectoragrupaciones[$s] = str_replace("*|","",$vectoragrupaciones[$s]);
		    $s++;
		}

		$s=0;
		// se crea el vector de condiciones
		$r=0;
		foreach($vectoragrupaciones as $clasificacion => $arry_agrupacion )
		{
			$vectorcondiciones[$clasificacion] = (count($arry_agrupacion) > 1) ? "(Desagr = '".implode("' OR Desagr = '", $arry_agrupacion)."')" : "Desagr = '".implode(" OR ", $arry_agrupacion)."'";

			if($r==0)
			{

				if(count($arry_agrupacion) > 1)
				{

					$numerodeagrupaciones[0] = implode($arry_agrupacion);

				}
				else
				{

					$numerodeagrupaciones = implode($arry_agrupacion);
				}
				$r=1;
			}

		}

	}


	// colores de tds
	//---------------------------------------------------------
	//- se crean los vectores que contienen los rangos maximo minimo y el color de este rango

	// Tabla de convenciones del semaforo

	echo "<table align='left'>";
	echo "<tr>";
	echo "<td colspan='2' class='encabezadoTabla'>Semaforo</td>";

	echo "</tr>";
	$i=0;
	while($i<count($vectormaximo))
	{
		echo "<tr>";
		echo "<td class='encabezadoTabla' >Nota entre  ".$vectorminimo[$i]." y ".$vectormaximo[$i]."</td>";
		echo "<td bgcolor='".$vectorcolor[$i]."'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";
		echo "</tr>";
		$i++;
	}

	echo "</table>";
	//--------------------------------------------------------------

	echo "<br>";
	echo "<br>";
	echo "<br>";
	echo "<br>";

	//---------------------------------------------------------------
	// Se consulta el tipo de formato par luego utilizar el switch
	$q = " SELECT  	Fortip "
		."   FROM  ".$wbasedato."_000042 "
		."  WHERE Forcod = ".$wtemareportes." ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);
	$tipodeformato = $row['Fortip'];
	//-------------------------------------------------------
	//imprime-prueba
	//echo $tipodeformato;
	$wbasedatocentrocostos= consultarAliasPorAplicacion($conex, $wbasedato, 'centrocostos');
	global $wcostosyp;
	global $wbasedatocli;
	switch($tipodeformato)
    {
               case '01':
                if ($wcco!='')
				{

					//echo "sies1";
					//venctor de cco que se llena con los nombres de los cco que tienen respuestas
					$vectorccocontestado = array();
					//venctor de cco que se llena con los codigos de los cco que tienen respuestas
					$vectorcodccocontestado  = array();
					$wbasedatocentrocostos= consultarAliasPorAplicacion($conex, $wbasedato, 'centrocostos');

					if ($wcco=='todos')
					{

						$querycco =	 "SELECT DISTINCT(Cconom),Ccocod"
									."  FROM ".$wbasedato."_000032 ,".$wbasedatocentrocostos.", talhuma_000013,  ".$wbasedato."_000002"
									." WHERE Ideuse = Mcauco "
									."   AND Idecco = Ccocod"
									."   AND Forcod = Mcafor"
									."   AND ".$wbasedato."_000002.Fortip = '".$wtemareportes."' ";
						
						//---se puso quemado para luego ser borrado
						if($wbasedatocentrocostos==$wcostosyp."_000005")
						{
							 $querycco = $querycco." AND  Ccoemp ='01'";
						}
						
						if($wusuario_consultado!='')
						{
							$querycco = $querycco." AND Ideuse='".$wusuario_consultado."' ";
						}

						$resquerycco = mysql_query($querycco,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$querycco." - ".mysql_error());
						$i=0;
						while($rowquerycco = mysql_fetch_array($resquerycco))
						{
							$vectorccocontestado[$i] = $rowquerycco['Cconom'];
							$vectorcodccocontestado[$i] = $rowquerycco['Ccocod'];
							$i++;
						}

					}
					else
					{
						$vectorccocontestado[]  = $wnomcco;
						$vectorcodccocontestado[] = $wcodcco;


					}

					$arr_querys_aplican = array();
					$arr_querys_NOaplican = array();
					$vectorresporcco = array();
					$vectorresporccocuantos = array();
					$n=0;

					foreach($vectorcondiciones as $cod_clasificacion => $arr_agrupacion)
					{

							$qccoxagrupacion = 	" SELECT SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos,".$wbasedato."_000051.Grucag,Idecco  "
												."  FROM ".$wbasedato."_000005 left join ".$wbasedato."_000051 on (Desagr = Grucod), ".$wbasedato."_000007 ,  ".$wbasedato."_000032 , talhuma_000013 "
												." WHERE ".$arr_agrupacion." "
												."   AND Evades = Descod"
											//	."   AND Evafco = Encenc "
												."   AND Mcauco = Evaevo"
												// ."   AND Desest !='off' "
												."   AND Ideuse = Mcauco "
												."   AND Idecco IN ('".implode("','",$vectorcodccocontestado)."') "
												//."   AND ".$wbasedato."_000032.fecha_data BETWEEN '".$fechainicial1."' AND '".$fechafinal1."' ";
												." AND Mcaano = '".$wano1."' AND Mcaper = '".$wperiodo1."' ";
												

							if($wusuario_consultado!='')
							{
								$qccoxagrupacion = $qccoxagrupacion." AND Ideuse='".$wusuario_consultado."' ";
							}

							$qccoxagrupacion .= " GROUP BY ".$wbasedato."_000013.Idecco";
							$arr_querys_aplican[] 	= $qccoxagrupacion;

					}
					$union=" UNION  ";
					$querys_aplican = implode($union, $arr_querys_aplican);

					if ($qccoxagrupacion != '')
					{
					    $resccoxagrupacion = mysql_query($querys_aplican,$conex) or die ("Error 0: ".mysql_errno()." - en el query: ".$qccoxagrupacion." - ".mysql_error());
					
                    

						while($rowccoxagrupacion  = mysql_fetch_array($resccoxagrupacion))
						{
							$cuantos = $rowccoxagrupacion['Cuantos'];

							@$resvalorcco = $rowccoxagrupacion['Suma'] / $cuantos ;
							$roundresvalorcco = round($resvalorcco ,2);
							$vectorresporcco[$rowccoxagrupacion['Grucag']][$rowccoxagrupacion['Idecco']]= $roundresvalorcco;
							$vectorresporccocuantos[$rowccoxagrupacion['Grucag']][$rowccoxagrupacion['Idecco']]= $cuantos;
						}
				    }

					if( $unperiodo!='1')
					{

						$vectorresporcco1 = array();
						$vectorresporccocuantos1=array();

						$n=0;
						while($n < count($vectoragrupaciones))
						{

							$j=0;
							while($j < count($vectorccocontestado))
							{


								$qccoxagrupacion = 	" SELECT SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos  "
													."  FROM ".$wbasedato."_000005, ".$wbasedato."_000007 ,  ".$wbasedato."_000032 , talhuma_000013 "
													." WHERE Desagr='".$vectoragrupaciones[$n]."' "
													."   AND Evades = Descod"
														//."   AND Evafco = Encenc "
														."   AND Enchis = Evaevo"
														// ."   AND Desest !='off' "
														."   AND Ideuse = Mcauco "
														."   AND Idecco = '".$vectorcodccocontestado[$j]."' "
														//."   AND  ".$wbasedato."_000032.fecha_data  BETWEEN '".$fechainicial2."' AND '".$fechafinal2."' ";
														." 	 AND Mcaano = '".$wano2."' AND Mcaper = '".$wperiodo2."' ";

								if($wusuario_consultado!='')
								{
									$qccoxagrupacion = $qccoxagrupacion." AND Ideuse='".$wusuario_consultado."' ";
								}

                                
								$resccoxagrupacion = mysql_query($qccoxagrupacion,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qccoxagrupacion." - ".mysql_error());
								$rowccoxagrupacion = mysql_fetch_array($resccoxagrupacion);

								$cuantos1=$rowccoxagrupacion['Cuantos'];

								@$resvalorcco1 = $rowccoxagrupacion['Suma'] / $cuantos1;
								$roundresvalorcco1 = round($resvalorcco1 ,2);
								$vectorresporcco1[$vectoragrupaciones[$n]][$vectorcodccocontestado[$j]]= $roundresvalorcco1;
								$vectorresporccocuantos1[$vectoragrupaciones[$n]][$vectorcodccocontestado[$j]]=$cuantos1;
								$j++;
							}
							$n++;
						}
					}
				}
				$m=0;

				// si se va a ver por todos las agrupaciones sin ningun detalle

				echo"<br><br>";

				echo"<table style='border:#2A5DB0 1px solid'  id='tablareporte'  align='center'>";

				echo"<tr><td class='encabezadoTabla' rowspan='2' >Unidad</td>";

				echo"<td align='center' class='encabezadoTabla' colspan='".count($vectorcondiciones)." '>Nombre del indicador</td>";
				//funciona si es porcco
				//------------------------------------

				if ($wcco!='' )
				{
					$j=0;
					echo"<tr>";
					foreach($vectorcondiciones as $clasificacion => $codigo)
					{
						echo"<td class='encabezadoTabla' align='center'>".$vectornomagrupaciones[$clasificacion]."</td>";
						$j++;
					}
				}
				//echo"<td class='encabezadoTabla' >TOTAL CLINICA</td>";
				echo"</tr>";
				//-------------------
				if($wcco!='')
				{

					$auxind=0;
					foreach($vectorccocontestado as $clavecco => $codigocco)
					{



						$color = traecolor ($vectorresporcco[$clasificacion][$vectorcodccocontestado[$clavecco]] ,$vectormaximo,$vectorminimo,$vectorcolor) ;
						$color1 = traecolor ($vectorresporcco1[$clasificacion][$vectorcodccocontestado[$clavecco]] ,$vectormaximo,$vectorminimo,$vectorcolor) ;



						if($vectorresporcco[$clasificacion][$vectorcodccocontestado[$clavecco]] =='' )
							$vectorresporcco[$clasificacion][$vectorcodccocontestado[$clavecco]]= 0;

						if($vectorresporccocuantos[$clasificacion][$vectorcodccocontestado[$clavecco]] == '')
							$vectorresporccocuantos[$clasificacion][$vectorcodccocontestado[$clavecco]]= 0;

						if($vectorresporccocuantosnoaplica[$clasificacion][$vectorcodccocontestado[$clavecco]]=='')
						  $vectorresporccocuantosnoaplica[$clasificacion][$vectorcodccocontestado[$clavecco]] = 0;

                        if(count($vectoragrupaciones) > 0)
						   $condicion = (count($vectoragrupaciones[$clasificacion]) > 1) ? "".implode("|", $vectoragrupaciones[$clasificacion])."" : "".implode("|", $vectoragrupaciones[$clasificacion])."";
						else
						   $condicion ='';
						
						if (($auxind%2)==0)
						{
							$wcf="fila1";  // color de fondo de la fila
						}
						else
						{
							$wcf="fila2"; // color de fondo de la fila
						}
						echo"<tr >";
						echo"<td class='".$wcf."' colspan='1' align='Left'  ><div id='sss'>".$codigocco."</div></td><td align='center'  class='msg_tooltip'    style='cursor: pointer;' bgcolor='".$color."' ><div title='Respuestas para c&aacute;lculo  ".$vectorresporccocuantos[$clasificacion][$vectorcodccocontestado[$clavecco]]."' onclick='abrirporpreguntas(this, \"".$condicion."\",\"".$vectorcodccocontestado[$clavecco]."\",\"".$fechainicial1."\",\"".$fechafinal1."\",\"".$vectornomagrupaciones[$clasificacion]."\",\"".$vectorccocontestado[$clavecco]."\",\"".$vectorresporcco[$clasificacion][$vectorcodccocontestado[$clavecco]]."\",\"".$auxind."\",\"".$tipodeformato."\")'>".$vectorresporcco[$clasificacion][$vectorcodccocontestado[$clavecco]]." <img style='float: right; display: inline-block;' id='img_mas-".$auxind."' src='../../images/medical/hce/mas.PNG'></div><div id='ddiv-".$auxind."'></div></td>";
						echo"</tr>";
						$auxind++;
					}
				}

			echo"</table>";

               break;

			   case '03':
				if ($wcco!='')
				{
					//venctor de cco que se llena con los nombres de los cco que tienen respuestas
					$vectorccocontestado = array();
					//venctor de cco que se llena con los codigos de los cco que tienen respuestas
					$vectorcodccocontestado  = array();

					if ($wcco=='todos')
					{

						// Consulta que se arroja los centros de costo en que se hara la busqueda
						// hay ciertos parametros que se van agregando  si son seleccionados

						$wbasedatocentrocostos= consultarAliasPorAplicacion($conex, $wbasedato, 'centrocostos');

						if ($fechainicial1=='')
						{
						 	$querycco =	 "SELECT distinct(Cconom) AS Nombre, Enccco"
									."  FROM ".$wbasedato."_000049 , ".$wbasedatocentrocostos." "
									." WHERE Enccco = Ccocod "
									."   AND ".$wbasedato."_000049.Encper ='".$wperiodo1."'"
									."   AND ".$wbasedato."_000049.Encano ='".$wano1."'"
									."   AND Encese= 'cerrado'";
						}
						else
						{
						$querycco =	 "SELECT distinct(Cconom) AS Nombre, Enccco"
									."  FROM ".$wbasedato."_000049 , ".$wbasedatocentrocostos." "
									." WHERE Enccco = Ccocod "
									."   AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$fechainicial1."' AND '".$fechafinal1."'"
									."   AND Encese= 'cerrado'";
						}
						
						//---se puso quemado para luego ser borrado
						if($wbasedatocentrocostos==$wcostosyp."_000005")
						{
							 $querycco = $querycco." AND  Ccoemp ='01'";
						}
						
						
						// se agrega esto a la consulta para que se busque por afinidad .
						if($wafinidad!='seleccione')
						{
							$querycco = $querycco . " AND Encafi LIKE  '%".$wafinidad."%' ";
						}

						// se agrega esto a la consulta para que se busque por entidad .
						if($wentidad!='seleccione')
						{
							$querycco = $querycco . " AND Encdia =  '".$entidades[$wentidad]."' ";
						}

						// se agrega esto al query para que busque por centro de costos hospitalarios
						if($whospitalario=='si')
						{
							$querycco = $querycco . " AND Ccohos = 'on' ";
						}

						// se agrega esto al query para que busque por centro de costos urgencias
						if($wurgencias=='si')
						{
							$querycco = $querycco . " AND Ccourg = 'on' ";
						}

						// se agrega esto al query para que busque por centro de costos cirugia
						if($wcirugia=='si')
						{
							$querycco = $querycco . " AND Ccocir = 'on' ";
						}

						// se agrega esto al query para que busque por centro de costos terapeutico
						if($wterapeutico=='si')
						{
							$querycco = $querycco . " AND Ccoter = 'on' ";
						}

						// se agrega esto al query para que busque por centro de costos de ayudas diagnosticas
						if($wayudas=='si')
						{
							$querycco = $querycco . " AND Ccoayu = 'on' ";
						}



						//------------------------------------------------------------

						// si se se maneja mas de un periodo se debe hacer un Union que arroja los centros de costos
						// donde hay respuestas tando del periodo 1 como del period 2
						if($unperiodo!='1')
						{
							// se concatena a la consulta un union que saca los centros de costos que tienen respuesta en un periodo
							// con los del otro periodo

							$wbasedatocentrocostos= consultarAliasPorAplicacion($conex, $wbasedato, 'centrocostos');

							$querycco =	$querycco."    UNION DISTINCT "
													."SELECT distinct(Cconom) AS Nombre, Enccco"
													."  FROM ".$wbasedato."_000049 , ".$wbasedatocentrocostos." "
													." WHERE Enccco = Ccocod "
													."   AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$fechainicial2."' AND '".$fechafinal2."'"
													."   AND Encese= 'cerrado'";


							// se agrega esto a la consulta para que se busque por afinidad .


							if($wafinidad!='seleccione'  )
							{
								$querycco = $querycco . "AND Encafi LIKE  '%".$wafinidad."%' ";
							}

							// se agrega esto a la consulta para que se busque por entidad .
							if($wentidad!='seleccione')
							{
								$querycco = $querycco . "AND Encdia =  '".$entidades[$wentidad]."' ";
							}

							// se agrega esto al query para que busque por centro de costos hospitalarios
							if($whospitalario=='si')
							{
								$querycco = $querycco . " AND Ccohos = 'on' ";
							}

							// se agrega esto al query para que busque por centro de costos urgencias
							if($wurgencias=='si')
							{
								$querycco = $querycco . " AND Ccourg = 'on' ";
							}

							// se agrega esto al query para que busque por centro de costos cirugia
							if($wcirugia=='si')
							{
								$querycco = $querycco . " AND Ccocir = 'on' ";
							}

							// se agrega esto al query para que busque por centro de costos terapeutico
							if($wterapeutico=='si')
							{
								$querycco = $querycco . " AND Ccoter = 'on' ";
							}
							// se agrega esto al query para que busque por centro de costos de ayudas diagnosticas
							if($wayudas=='si')
							{
								$querycco = $querycco . " AND Ccoayu = 'on' ";
							}
							
							//---se puso quemado para luego ser borrado
							if($wbasedatocentrocostos==$wcostosyp."_000005")
							{
								$querycco = $querycco." AND  Ccoemp ='01'";
							}

						}

						// se imprime la consulta para ver si esta buena.

						$resquerycco = mysql_query($querycco,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$querycco." - ".mysql_error());

						//echo 'consulta final '.$querycco;

						$i=0;
						// por medio de este while se llenan los vectores  vectorccocontestado con el nombre y vectorcodccocontestado con el codigo
						while($rowquerycco = mysql_fetch_array($resquerycco))
						{
							$vectorccocontestado[$i] = $rowquerycco['Nombre'];
							$vectorcodccocontestado[$i] = $rowquerycco['Enccco'];
							$i++;
						}

					}
					else
					{
						$vectorccocontestado[]  = $wnomcco;
						$vectorcodccocontestado[] = $wcodcco;
					}

					// vector donde se almacena el promedio de las notas por centro de costos
					$vectorresporcco = array();
					// vector donde se almacena la cantidad de preguntas contestadas por agrupacion en centro de costos
					$vectorresporccocuantos = array();
					$vectorresporccocuantosnoaplica= array();
					$n=0;
					$xx=0;

					$arr_querys_aplican = array();
					$arr_querys_NOaplican = array();
					foreach($vectorcondiciones as $cod_clasificacion => $arr_agrupacion)
					{
						$xx= $xx +1;
						if ($fechainicial1=='')
						{
						 	$qccoxagrupacion = 	"SELECT ".$wbasedato."_000049.Enccco, ".$wbasedato."_000051.Grucag, SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos, Empcod   "
												."  FROM ".$wbasedato."_000005 left join ".$wbasedato."_000051 on (Desagr = Grucod), ".$wbasedato."_000007 , ".$wbasedato."_000049 LEFT JOIN ".$wbasedatocli."_000024 ON Encdia = Empcod "
												." WHERE ".$arr_agrupacion." "
												."   AND Evades = Descod"
												."   AND Evafco = Encenc "
												."   AND Enchis = Evaevo "
												."   AND Enccco IN ('".implode("','",$vectorcodccocontestado)."') "
												."   AND ".$wbasedato."_000049.Encper ='".$wperiodo1."'"
												."   AND ".$wbasedato."_000049.Encano ='".$wano1."'"
												."   AND Encese= 'cerrado'";
												// ."   AND Desest !='off' ";


						}
						else
						{
							$qccoxagrupacion = 	" SELECT ".$wbasedato."_000049.Enccco, ".$wbasedato."_000051.Grucag, SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos,Empcod   "
												."  FROM ".$wbasedato."_000005 left join ".$wbasedato."_000051 on (Desagr = Grucod), ".$wbasedato."_000007 , ".$wbasedato."_000049 LEFT JOIN ".$wbasedatocli."_000024 ON Encdia = Empcod  "
												." WHERE ".$arr_agrupacion." "
												."   AND Evades = Descod"
												."   AND Evafco = Encenc "
												."   AND Enchis = Evaevo "
												."   AND Enccco IN ('".implode("','",$vectorcodccocontestado)."') "
												."   AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$fechainicial1."' AND '".$fechafinal1."' "
												."   AND Encese= 'cerrado'";
												// ."   AND Desest !='off' ";

						}


							$qccoxagrupacionconnoaplica = $qccoxagrupacion;

							$qccoxagrupacion = $qccoxagrupacion."   AND Evacal != 0 ";
							// se agrega esto a la consulta para que se busque por afinidad .
							if($wafinidad!='seleccione')
							{
								$qccoxagrupacion = $qccoxagrupacion . " AND Encafi LIKE  '%".$wafinidad."%' ";
								$qccoxagrupacionconnoaplica =$qccoxagrupacionconnoaplica . " AND Encafi LIKE  '%".$wafinidad."%' ";
							}

							// se agrega esto a la consulta para que se busque por entidad .
							if($wentidad!='seleccione')
							{
								$qccoxagrupacion = $qccoxagrupacion . " AND Encdia =  '".$entidades[$wentidad]."' ";
								$qccoxagrupacionconnoaplica = $qccoxagrupacionconnoaplica . " AND Encdia =  '".$entidades[$wentidad]."' ";
							}

							if($wspregunta!='seleccione')
							{
								$qccoxagrupacion = $qccoxagrupacion . " AND Descod  = '".$wspregunta."' ";
								$qccoxagrupacionconnoaplica = $qccoxagrupacionconnoaplica . " AND Descod  = '".$wspregunta."' ";
							}

							// se agrega esto al query para que busque solo las empresas que sean del tipo establecido por el usuario
							if($wtipodeempresa !='')
							{
								if ($wtipodeempresa =='PAP')
								{
									$wtipoempresa_buscar = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Empresas_PAP_servicio_usuario');
									$qccoxagrupacion = $qccoxagrupacion . " AND Emptem IN (".$wtipoempresa_buscar.")  ";
									$qccoxagrupacionconnoaplica = $qccoxagrupacionconnoaplica . " AND Emptem IN (".$wtipoempresa_buscar.")  ";
								}
								if ($wtipodeempresa =='particular')
								{
									$wtipoempresa_buscar = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Empresas_particular_servicio_usuario');
									$qccoxagrupacion = $qccoxagrupacion . " AND Emptem IN (".$wtipoempresa_buscar.")  ";
									$qccoxagrupacionconnoaplica = $qccoxagrupacionconnoaplica . " AND Emptem IN (".$wtipoempresa_buscar.")  ";

								}
								if ($wtipodeempresa =='pos')
								{

									$wtipoempresa_buscar_pap = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Empresas_PAP_servicio_usuario');
									$wtipoempresa_buscar_particular = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Empresas_particular_servicio_usuario');
									$qccoxagrupacion = $qccoxagrupacion . " AND Emptem NOT IN (".$wtipoempresa_buscar_pap.",".$wtipoempresa_buscar_particular.")  ";
									$qccoxagrupacionconnoaplica = $qccoxagrupacionconnoaplica . " AND Emptem NOT IN (".$wtipoempresa_buscar_pap.",".$wtipoempresa_buscar_particular.")  ";

								}


							}



							$qccoxagrupacion .= " GROUP BY ".$wbasedato."_000049.Enccco";
							$qccoxagrupacionconnoaplica .= " GROUP BY ".$wbasedato."_000049.Enccco";

							$arr_querys_aplican[] 	= $qccoxagrupacion;
							$arr_querys_NOaplican[]	= $qccoxagrupacionconnoaplica;

					}
					$union=" UNION  ";
					$querys_aplican = implode($union, $arr_querys_aplican);
					$querys_NOaplican = implode($union, $arr_querys_NOaplican);

					//echo '</pre>'.$querys_aplican.'</pre><br><br>';
					$resccoxagrupacion = mysql_query($querys_aplican,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qccoxagrupacion." - ".mysql_error());
					//$rowccoxagrupacion = mysql_fetch_array($resccoxagrupacion);
					$resccoxagrupacionconnoaplica = mysql_query($querys_NOaplican,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$querys_NOaplican." - ".mysql_error());

					while($rowccoxagrupacion  = mysql_fetch_array($resccoxagrupacion))
					{
						$cuantos = $rowccoxagrupacion['Cuantos'];


						@$resvalorcco = $rowccoxagrupacion['Suma'] / $cuantos ;
						$roundresvalorcco = round($resvalorcco ,2);
						$vectorresporcco[$rowccoxagrupacion['Grucag']][$rowccoxagrupacion['Enccco']]= $roundresvalorcco;
						$vectorresporccocuantos[$rowccoxagrupacion['Grucag']][$rowccoxagrupacion['Enccco']]= $cuantos;
					}

					while( $rowccoxagrupacionconnoaplica = mysql_fetch_array($resccoxagrupacionconnoaplica) )
					{
						$cuantosnoaplica = $rowccoxagrupacionconnoaplica['Cuantos'];
						$vectorresporccocuantosnoaplica[$rowccoxagrupacionconnoaplica['Grucag']][$rowccoxagrupacionconnoaplica['Enccco']]= $cuantosnoaplica;
					}


					// si tiene mas de  uno , se debe hacer para el otro periodo
					if( $unperiodo!='1')
					{
						$arr_querys_aplican = array();
						$arr_querys_NOaplican = array();
						$vectorresporcco1 = array();
						$n=0;
						foreach($vectorcondiciones as $cod_clasificacion => $arr_agrupacion)
						{

								$qccoxagrupacion = 	" SELECT ".$wbasedato."_000049.Enccco, ".$wbasedato."_000051.Grucag, SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos  "
												."  FROM ".$wbasedato."_000005 left join ".$wbasedato."_000051 on (Desagr = Grucod), ".$wbasedato."_000007 , ".$wbasedato."_000049  "
												." WHERE ".$arr_agrupacion." "
												."   AND Evades = Descod"
												."   AND Evafco = Encenc "
												."   AND Enchis = Evaevo "
												."   AND Enccco IN ('".implode("','",$vectorcodccocontestado)."') "
												."   AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$fechainicial2."' AND '".$fechafinal2."' "
												."   AND Encese= 'cerrado'";
												// ."   AND Desest !='off' ";


								$qccoxagrupacionconnoaplica = $qccoxagrupacion;

								$qccoxagrupacion = $qccoxagrupacion."   AND Evacal != 0 ";

								// se agrega esto a la consulta para que se busque por afinidad .
								if($wafinidad!='seleccione')
								{
									$qccoxagrupacion = $qccoxagrupacion . "AND Encafi LIKE  '%".$wafinidad."%' ";
									$qccoxagrupacionconnoaplica = $qccoxagrupacionconnoaplica . "AND Encafi LIKE  '%".$wafinidad."%' ";
								}

								// se agrega esto a la consulta para que se busque por entidad .
								if($wentidad!='seleccione')
								{
									$qccoxagrupacion = $qccoxagrupacion . "AND Encdia =  '".$entidades[$wentidad]."' ";
									$qccoxagrupacionconnoaplica = $qccoxagrupacionconnoaplica . "AND Encdia =  '".$entidades[$wentidad]."' ";
								}

								if($wspregunta!='seleccione')
								{
									$qccoxagrupacion = $qccoxagrupacion . " AND Descod  = '".$wspregunta."' ";
									$qccoxagrupacionconnoaplica = $qccoxagrupacionconnoaplica . " AND Descod  = '".$wspregunta."' ";
								}

								$qccoxagrupacion .= " GROUP BY ".$wbasedato."_000049.Enccco";
								$qccoxagrupacionconnoaplica .= " GROUP BY ".$wbasedato."_000049.Enccco";

								$arr_querys_aplican[] 	= $qccoxagrupacion;
								$arr_querys_NOaplican[]	= $qccoxagrupacionconnoaplica;

						}

							$union=" UNION  ";
							$querys_aplican = implode($union, $arr_querys_aplican);
							$querys_NOaplican = implode($union, $arr_querys_NOaplican);

							$resccoxagrupacion = mysql_query($querys_aplican,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$querys_aplican." - ".mysql_error());
							//$rowccoxagrupacion = mysql_fetch_array($resccoxagrupacion);
							$resccoxagrupacionconnoaplica = mysql_query($querys_NOaplican,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$querys_NOaplican." - ".mysql_error());


							while($rowccoxagrupacion  = mysql_fetch_array($resccoxagrupacion))
							{
								$cuantos1 = $rowccoxagrupacion['Cuantos'];


								@$resvalorcco1 = $rowccoxagrupacion['Suma'] / $cuantos1 ;
								$roundresvalorcco1 = round($resvalorcco1 ,2);
								$vectorresporcco1[$rowccoxagrupacion['Grucag']][$rowccoxagrupacion['Enccco']]= $roundresvalorcco1;
								$vectorresporccocuantos1[$rowccoxagrupacion['Grucag']][$rowccoxagrupacion['Enccco']]= $cuantos1;
							}

							while( $rowccoxagrupacionconnoaplica = mysql_fetch_array($resccoxagrupacionconnoaplica) )
							{
								$cuantosnoaplica1 = $rowccoxagrupacionconnoaplica['Cuantos'];
								$vectorresporccocuantosnoaplica1[$rowccoxagrupacionconnoaplica['Grucag']][$rowccoxagrupacionconnoaplica['Enccco']]= $cuantosnoaplica1;
							}
					}

				}
				echo"<br><br>";
				echo"<table style='border:#2A5DB0 1px solid' id='tablareporte'>";
				if( $unperiodo=='1')
				{
					echo"<tr><td class='encabezadoTabla' rowspan='2' >Nombre del Indicador</td>";
					echo"<td align='center' class='encabezadoTabla' colspan='".(count($vectorccocontestado) + 1)." '>Notas Promedio</td>";
				}
				else
				{
				  echo"<tr><td class='encabezadoTabla' rowspan='3' >Nombre del Indicador</td>";
				  echo"<td align='center' class='encabezadoTabla' colspan='".((count($vectorccocontestado) + 2) * 2)." '>Notas Promedio</td>";
				}

				//funciona si es porcco
				//------------------------------------

				if ($wcco!='' )
				{
					$j=0;
					echo"<tr class='pisos'>";

						if( $unperiodo=='1')
						{
							$j=0;
							while($j < count($vectorccocontestado))
							{
								echo"<td class='encabezadoTabla' align='center'>".$vectorccocontestado[$j]."</td>";
								$j++;
							}
							echo"<td class='encabezadoTabla' >TOTAL CLINICA</td>";
							echo"</tr>";
						}
						else
						{
							$j=0;
							while($j < count($vectorccocontestado))
							{
								echo"<td class='encabezadoTabla' colspan='2' align='center'>".$vectorccocontestado[$j]."</td>";
								$j++;
							}
							echo"<td class='encabezadoTabla' colspan='2'>TOTAL CLINICA</td>";
							echo"</tr>";
							echo"<tr>";
							$j=0;
							while($j < count($vectorccocontestado))
							{
								echo"<td class='encabezadoTabla Periodo'  align='center'>Periodo1</td><td class='encabezadoTabla Periodo' align='center'>Periodo2</td>";
								$j++;
							}
							echo"<td class='encabezadoTabla Periodo' align='center'>Periodo1</td><td class='encabezadoTabla Periodo' align='center'>Periodo2</td>";
							echo"</tr>";

						}

				}

				if($wcco!='')
				{

					$auxind = 0;
					foreach ( $vectorcondiciones  as $clasificacion => $codigo )
					{

						//--------nombre de agrupacion
						echo"<tr class='ocultar' >";
						echo"<td width='200' class='encabezadoTabla' colspan='1' align='Left'  ><div id=td_nombreagrupacion>".$vectornomagrupaciones[$clasificacion]."</div></td>";
						//---------------------------

						if($wcco!='')
						{
					//03
							$j=0;

							while($j < count($vectorccocontestado))
							{
								// trae los datos de cada uno de los centro de costos por agrupacion


								$color = traecolor ($vectorresporcco[$clasificacion][$vectorcodccocontestado[$j]] ,$vectormaximo,$vectorminimo,$vectorcolor) ;
								$color1 = traecolor ($vectorresporcco1[$clasificacion][$vectorcodccocontestado[$j]] ,$vectormaximo,$vectorminimo,$vectorcolor) ;


								if( $unperiodo=='1')
								{
									if($vectorresporcco[$clasificacion][$vectorcodccocontestado[$j]] =='' )
										$vectorresporcco[$clasificacion][$vectorcodccocontestado[$j]]= 0;

									if($vectorresporccocuantos[$clasificacion][$vectorcodccocontestado[$j]] == '')
										$vectorresporccocuantos[$clasificacion][$vectorcodccocontestado[$j]]= 0;

									if($vectorresporccocuantosnoaplica[$clasificacion][$vectorcodccocontestado[$j]]=='')
									  $vectorresporccocuantosnoaplica[$clasificacion][$vectorcodccocontestado[$j]] = 0;


									$condicion = (count($vectoragrupaciones[$clasificacion]) > 1) ? "".implode("|", $vectoragrupaciones[$clasificacion])."" : "".implode("|", $vectoragrupaciones[$clasificacion])."";
									echo"<td align='center'   style='cursor: pointer;' bgcolor='".$color."'><div  class='msg_tooltip'  title='Respuestas para c&aacute;lculo  ".$vectorresporccocuantos[$clasificacion][$vectorcodccocontestado[$j]]."<br>  Respuestas Totales ".$vectorresporccocuantosnoaplica[$clasificacion][$vectorcodccocontestado[$j]]."'  onclick='abrirporpreguntas(this, \"".$condicion."\",\"".$vectorcodccocontestado[$j]."\",\"".$fechainicial1."\",\"".$fechafinal1."\",\"".$vectornomagrupaciones[$clasificacion]."\",\"".$vectorccocontestado[$j]."\",\"".$vectorresporcco[$clasificacion][$vectorcodccocontestado[$j]]."\", \"".$auxind."\")'><img style='float: right; display: inline-block;' id='img_mas-".$auxind."' src='../../images/medical/hce/mas.PNG'>".$vectorresporcco[$clasificacion][$vectorcodccocontestado[$j]]."</div><div id='ddiv-".$auxind."'></div></td>";

									$auxind++;
								}
								else
								{
									if($vectorresporcco[$clasificacion][$vectorcodccocontestado[$j]] =='' )
										$vectorresporcco[$clasificacion][$vectorcodccocontestado[$j]]= 0;

									if($vectorresporccocuantos[$clasificacion][$vectorcodccocontestado[$j]] == '')
										$vectorresporccocuantos[$clasificacion][$vectorcodccocontestado[$j]]= 0;

									if($vectorresporccocuantosnoaplica[$clasificacion][$vectorcodccocontestado[$j]]=='')
									  $vectorresporccocuantosnoaplica[$clasificacion][$vectorcodccocontestado[$j]] = 0;


									if($vectorresporcco1[$clasificacion][$vectorcodccocontestado[$j]] =='' )
										$vectorresporcco1[$clasificacion][$vectorcodccocontestado[$j]]= 0;

									if($vectorresporccocuantos1[$clasificacion][$vectorcodccocontestado[$j]] == '')
										$vectorresporccocuantos1[$clasificacion][$vectorcodccocontestado[$j]]= 0;

									if($vectorresporccocuantosnoaplica1[$clasificacion][$vectorcodccocontestado[$j]]=='')
									  $vectorresporccocuantosnoaplica1[$clasificacion][$vectorcodccocontestado[$j]] = 0;

									$condicion = (count($vectoragrupaciones[$clasificacion]) > 1) ? "".implode("|", $vectoragrupaciones[$clasificacion])."" : "".implode("|", $vectoragrupaciones[$clasificacion])."";
									echo"<td bgcolor='".$color."'   align='center' style='cursor: pointer;' ><div class='msg_tooltip'  title='Respuestas para c&aacute;lculo ".$vectorresporccocuantos[$clasificacion][$vectorcodccocontestado[$j]]."<br> Respuestas Totales ".$vectorresporccocuantosnoaplica[$clasificacion][$vectorcodccocontestado[$j]]."' onclick='abrirporpreguntas(this,\"".$condicion."\",\"".$vectorcodccocontestado[$j]."\",\"".$fechainicial1."\",\"".$fechafinal1."\",\"".$vectornomagrupaciones[$clasificacion]."\",\"".$vectorccocontestado[$j]."\",\"".$vectorresporcco[$clasificacion][$vectorcodccocontestado[$j]]."\", \"".$auxind."\")' ><img style='float: right; display: inline-block;' id='img_mas-".$auxind."' src='../../images/medical/hce/mas.PNG'>".$vectorresporcco[$clasificacion][$vectorcodccocontestado[$j]]."</div><div id='ddiv-".$auxind."'></div></td >";

									$auxind++;
									echo"<td  bgcolor='".$color1."' style='cursor: pointer;' align='center' ><div class='msg_tooltip'  title='Respuestas para c&aacute;lculo ".$vectorresporccocuantos1[$clasificacion][$vectorcodccocontestado[$j]]."<br> Respuetas Totales ".$vectorresporccocuantosnoaplica1[$clasificacion][$vectorcodccocontestado[$j]]."'    onclick='abrirporpreguntas(this,\"".$condicion."\",\"".$vectorcodccocontestado[$j]."\",\"".$fechainicial2."\",\"".$fechafinal2."\",\"".$vectornomagrupaciones[$clasificacion]."\",\"".$vectorccocontestado[$j]."\",\"".$vectorresporcco1[$clasificacion][$vectorcodccocontestado[$j]]."\", \"".$auxind."\")' ><img style='float: right; display: inline-block;' id='img_mas-".$auxind."' src='../../images/medical/hce/mas.PNG'>".$vectorresporcco1[$clasificacion][$vectorcodccocontestado[$j]]."</div><div id='ddiv-".$auxind."'></div></td>";


									$auxind++;

								}

								$j++;


							}



						}
						//-------------total de la agrupacion
						if ($fechainicial1=='')
						{
						 		$qtagrupacion = " SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos  "
												."  FROM ".$wbasedato."_000005, ".$wbasedato."_000007 , ".$wbasedato."_000049 "
												." WHERE ".$vectorcondiciones[$clasificacion]." "
												."   AND Evades = Descod"
												."   AND Evafco = Encenc "
												."   AND Enchis = Evaevo "
												."   AND ".$wbasedato."_000049.Encper ='".$wperiodo1."'"
												."   AND ".$wbasedato."_000049.Encano ='".$wano1."'"
												."   AND Encese= 'cerrado'";
												// ."   AND Desest !='off' ";



						}
						else
						{
							$qtagrupacion = 	" SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos  "
												."  FROM ".$wbasedato."_000005, ".$wbasedato."_000007 , ".$wbasedato."_000049 "
												." WHERE ".$vectorcondiciones[$clasificacion]." "
												."   AND Evades = Descod"
												."   AND Evafco = Encenc "
												."   AND Enchis = Evaevo "
												."   AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$fechainicial1."' AND '".$fechafinal1."' "
												."   AND Encese= 'cerrado'";
												// ."   AND Desest !='off' ";

						}


						$qtagrupacionnoaplica = $qtagrupacion;
						$qtagrupacion = $qtagrupacion ."   AND Evacal !=0  ";


						// si la busqueda es por afinidad
						if($wafinidad!='seleccione')
						{
							$qtagrupacion = $qtagrupacion . "AND Encafi LIKE  '%".$wafinidad."%' ";
							$qtagrupacionnoaplica = $qtagrupacionnoaplica . "AND Encafi LIKE  '%".$wafinidad."%' ";
						}

						// se agrega esto a la columna para que se busque por entidad .
						if($wentidad!='seleccione')
						{
							$qtagrupacion =$qtagrupacion . "AND Encdia =  '".$entidades[$wentidad]."' ";
							$qtagrupacionnoaplica = $qtagrupacionnoaplica . "AND Encdia =  '".$entidades[$wentidad]."' ";
						}

						if($wspregunta!='seleccione')
						{
							$qtagrupacion = $qtagrupacion . " AND Descod  = '".$wspregunta."' ";
							$qtagrupacionnoaplica = $qtagrupacionnoaplica . " AND Descod  = '".$wspregunta."' ";
						}


						$restagrupacion = mysql_query($qtagrupacion,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qtagrupacion." - ".mysql_error());
						$rowtagrupacion = mysql_fetch_array($restagrupacion);

						$restagrupacionnoaplica = mysql_query($qtagrupacionnoaplica,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qtagrupacionnoaplica." - ".mysql_error());
						$rowtagrupacionnoaplica = mysql_fetch_array($restagrupacionnoaplica);

						$qtagrupacion1 = 	" SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos  "
											."  FROM ".$wbasedato."_000005, ".$wbasedato."_000007 , ".$wbasedato."_000049 "
											." WHERE ".$vectorcondiciones[$clasificacion]." "
											."   AND Evades = Descod"
											."   AND Evafco = Encenc "
											."   AND Enchis = Evaevo "
											."   AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$fechainicial2."' AND '".$fechafinal2."' "
											."   AND Encese= 'cerrado'";
											// ."   AND Desest !='off' ";

						$qtagrupacionnoaplica1 = $qtagrupacion1;
						$qtagrupacion1= $qtagrupacion1 ."   AND Evacal !=0  ";

						// si la busqueda es por afinidad
						if($wafinidad!='seleccione')
						{
							$qtagrupacion1 = $qtagrupacion1 . "AND Encafi LIKE  '%".$wafinidad."%' ";
							$qtagrupacionnoaplica1 = $qtagrupacionnoaplica1 . "AND Encafi LIKE  '%".$wafinidad."%' ";
						}

						if($wentidad !='seleccione')
						{
							$qtagrupacion1 = $qtagrupacion1 . "AND Encdia =  '".$entidades[$wentidad]."' ";
							$qtagrupacionnoaplica1 = $qtagrupacionnoaplica1. "AND Encdia =  '".$entidades[$wentidad]."' ";
						}

						if($wspregunta!='seleccione')
						{
							$qtagrupacion1 = $qtagrupacion1 . " AND Descod  = '".$wspregunta."' ";
							$qtagrupacionnoaplica1 = $qtagrupacionnoaplica1 . " AND Descod  = '".$wspregunta."' ";
						}


						$restagrupacion1 = mysql_query($qtagrupacion1,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qtagrupacion1." - ".mysql_error());
						$rowtagrupacion1 = mysql_fetch_array($restagrupacion1);

						$restagrupacion1noaplica = mysql_query($qtagrupacionnoaplica1,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qtagrupacionnoaplica1." - ".mysql_error());
						$rowtagrupacion1noaplica = mysql_fetch_array($restagrupacion1noaplica);


							$cuantost= $rowtagrupacion['Cuantos'];
							$cuantostnoaplica = $rowtagrupacionnoaplica['Cuantos'];
							@$valor = $rowtagrupacion['Suma']  / $cuantost;
							$total = $valor + $total;
							$float_redondeado=round($valor * 100) / 100;

							$cuantost1= $rowtagrupacion1['Cuantos'];
							$cuantostnoaplica1 = $rowtagrupacion1noaplica['Cuantos'];
							@$valor1 = $rowtagrupacion1['Suma']  / $cuantost1;
							$total1 = $valor1 + $total1;
							$float_redondeado1=round($valor1 * 100) / 100;
							$color = traecolor ($float_redondeado ,$vectormaximo,$vectorminimo,$vectorcolor) ;
							$color1 = traecolor ($float_redondeado1 ,$vectormaximo,$vectorminimo,$vectorcolor) ;
							$auxind = $auxind + 1;
							
							$float_redondeado = is_nan($float_redondeado) ? 0 : $float_redondeado;
							
							if( $unperiodo=='1')
							{

								echo"<td align='center'  style='cursor: pointer;' bgcolor='".$color."' ><div class='msg_tooltip'  title='Respuestas para c&aacute;lculo ".$cuantost." <br> Respuestas totales ".$cuantostnoaplica."'    onclick='abrirporpreguntas(this,\"".$condicion."\",\"todos\",\"".$fechainicial1."\",\"".$fechafinal1."\",\"".$vectornomagrupaciones[$clasificacion]."\",\"".$vectorccocontestado[$j]."\",\"".$float_redondeado."\",\"".$auxind."\")' ><img style='float: right; display: inline-block;' id='img_mas-".$auxind."'' src='../../images/medical/hce/mas.PNG'>".$float_redondeado."</div><div id='ddiv-".$auxind."'></div></td>";																																																													// abrirporpreguntas(this, \"".$condicion."\",\"".$vectorcodccocontestado[$j]."\",\"".$fechainicial1."\",\"".$fechafinal1."\",\"".$vectornomagrupaciones[$clasificacion]."\",\"".$vectorccocontestado[$j]."\",\"".$vectorresporcco[$clasificacion][$vectorcodccocontestado[$j]]."\", \"".$auxind."\")
							}
							else
							{
								$auxind = $auxind + 1;
								echo"<td align='center'  style='cursor: pointer;' bgcolor='".$color."' ><div class='msg_tooltip'  title='Respuestas para c&aacute;lculo ".$cuantost."  <br> Respuestas totales ".$cuantostnoaplica." '  onclick='abrirporpreguntas(this,\"".$condicion."\",\"todos\",\"".$fechainicial1."\",\"".$fechafinal1."\",\"".$vectornomagrupaciones[$clasificacion]."\",\"".$vectorccocontestado[$j]."\",\"".$float_redondeado."\",\"".$auxind."\")'><img style='float: right; display: inline-block;' id='img_mas-".$auxind."'' src='../../images/medical/hce/mas.PNG'>".$float_redondeado."</div><div id='ddiv-".$auxind."'></div></td>";

								$auxind = $auxind + 1;
								echo"<td align='center'  style='cursor: pointer;' bgcolor='".$color1."'><div  class='msg_tooltip'  title='Respuestas para c&aacute;lculo ".$cuantost1." <br> Respuestas Totales ".$cuantostnoaplica1."' onclick='abrirporpreguntas(this,\"".$condicion."\",\"todos\",\"".$fechainicial2."\",\"".$fechafinal2."\",\"".$vectornomagrupaciones[$clasificacion]."\",\"".$vectorccocontestado[$j]."\",\"".$float_redondeado1."\",\"".$auxind."\")'><img style='float: right; display: inline-block;' id='img_mas-".$auxind."'' src='../../images/medical/hce/mas.PNG'>".$float_redondeado1."</div><div id='ddiv-".$auxind."'></div></td>";
								//echo"<td align='center'  style='cursor: pointer;' bgcolor='".$color."' ><div class='msg_tooltip'  title='Respuestas para c&aacute;lculo ".$cuantost." <br> Respuestas totales ".$cuantostnoaplica."'    onclick='abrirporpreguntas(this,\"".$condicion."\",\"todos\",\"".$fechainicial1."\",\"".$fechafinal1."\",\"".$vectornomagrupaciones[$clasificacion]."\",\"".$vectorccocontestado[$j]."\",\"".$float_redondeado."\",\"".$auxind."\")' ><img style='float: right; display: inline-block;' id='img_mas-".$auxind."'' src='../../images/medical/hce/mas.PNG'>".$float_redondeado."</div><div id='ddiv-".$auxind."'></div></td>";

								$auxind = $auxind + 1;
							}
							$auxind = $auxind + 1;
							$i++;

						//---------------------------------------

						echo"</tr>";
						$m++;
					}
				}
				else if($poragrupacion=='si' )
				{
					while($m < count($vectoragrupaciones))
					{
						//--------nombre de agrupacion
						echo"<tr >";
						echo"<td class='encabezadoTabla' colspan='1' align='Left'  ><div id=td_nombreagrupacion>".$vectornomagrupaciones[$m]."</div></td>";
						//---------------------------

						if($porcco=='si')
						{

							$j=0;
							while($j < count($vectorccocontestado))
							{



								if( $unperiodo=='1')
								{
									echo"<td align='center'>".$vectorresporcco[$vectoragrupaciones[$m]][$vectorcodccocontestado[$j]]."</td>";
								}
								else
								{

									echo"<td class='encabezadoTabla'>".$vectorresporcco[$vectoragrupaciones[$m]][$vectorcodccocontestado[$j]]."</td ><td align='center' class='encabezadoTabla' class='msg_tooltip'  title='Cantidad de Respuestas ".$vectorresporccocuantos[$vectoragrupaciones[$m]][$vectorcodccocontestado[$j]]."' >".$vectorresporcco[$vectoragrupaciones[$m]][$vectorcodccocontestado[$j]]."</td>";


								}

								$j++;
							}

						}
						//-------------total de la agrupacion

						$qtagrupacion = 	" SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos  "
											."  FROM ".$wbasedato."_000005, ".$wbasedato."_000007  "
											." WHERE ".$vectorcondiciones[$n]." "
											."   AND Evades = Descod";
											// ."   AND Desest !='off' ";



						$restagrupacion = mysql_query($qtagrupacion,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qtagrupacion." - ".mysql_error());
						$rowtagrupacion = mysql_fetch_array($restagrupacion);


							$valor = $rowtagrupacion['Suma']  / $rowtagrupacion['Cuantos'];
							$total = $valor + $total;
							$float_redondeado=round($valor * 100) / 100;

							$color = traecolor($float_redondeado ,$vectormaximo,$vectorminimo,$vectorcolor) ;

								if( $unperiodo=='1')
								{
										echo"<td align='center' style='cursor: pointer;' bgcolor='".$color ."' onclick='abrirporpreguntas(this,\"".$vectoragrupaciones[$m]."\",\"todos\",\"".$fechainicial1."\",\"".$fechafinal1."\",\"".$vectornomagrupaciones[$m]."\",\"".$vectorccocontestado[$j]."\",\"".$float_redondeado."\")'>".$float_redondeado."<div id='ddiv-".$auxind."'></div></td>";
								}
								else
								{

									echo"<td align='center'   align='center'  style='cursor: pointer;' bgcolor='".$color ."' onclick='abrirporpreguntas(this,\"".$vectoragrupaciones[$m]."\",\"todos\",\"".$fechainicial1."\",\"".$fechafinal1."\",\"".$vectornomagrupaciones[$m]."\",\"".$vectorccocontestado[$j]."\",\"".$float_redondeado."\")' >".$float_redondeado."</td><td  style='cursor: pointer;' align='center' bgcolor='".$color ."' onclick='abrirporpreguntas(this,\"".$vectoragrupaciones[$m]."\",\"todos\",\"".$fechainicial2."\",\"".$fechafinal2."\",\"".$vectornomagrupaciones[$m]."\",\"".$vectorccocontestado[$j]."\",\"".$float_redondeado."\")'>".$float_redondeado."<div id='ddiv-".$auxind."'></div></td>";

								}

							$i++;

						echo"</tr>";
						$m++;
					}
				}
				echo"</table>";

				break;

			   Case '04':

			   // nuevo
			   if ($wcco!='')
				{

					//echo "sies1";
					//venctor de cco que se llena con los nombres de los cco que tienen respuestas
					$vectorccocontestado = array();
					//venctor de cco que se llena con los codigos de los cco que tienen respuestas
					$vectorcodccocontestado  = array();
					$wbasedatocentrocostos= consultarAliasPorAplicacion($conex, $wbasedato, 'centrocostos');

					if ($wcco=='todos')
					{

						$querycco =	 "SELECT DISTINCT(Cconom),Ccocod"
									."  FROM ".$wbasedato."_000032 ,".$wbasedatocentrocostos.", talhuma_000013,  ".$wbasedato."_000002"
									." WHERE Ideuse = Mcauco "
									."   AND Idecco = Ccocod"
									."   AND Forcod = Mcafor"
									."   AND ".$wbasedato."_000002.Fortip = '".$wtemareportes."' ";

						if($wusuario_consultado!='')
						{
							$querycco = $querycco." AND Ideuse='".$wusuario_consultado."' ";
						}
						
						//---se puso quemado para luego ser borrado
						if($wbasedatocentrocostos==$wcostosyp."_000005")
						{
							 $querycco = $querycco." AND  Ccoemp ='01'";
						}

						$resquerycco = mysql_query($querycco,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$querycco." - ".mysql_error());
						$i=0;
						while($rowquerycco = mysql_fetch_array($resquerycco))
						{
							$vectorccocontestado[$i] = $rowquerycco['Cconom'];
							$vectorcodccocontestado[$i] = $rowquerycco['Ccocod'];
							$i++;
						}

					}
					else
					{
						$vectorccocontestado[]  = $wnomcco;
						$vectorcodccocontestado[] = $wcodcco;


					}

					$arr_querys_aplican = array();
					$arr_querys_NOaplican = array();
					$vectorresporcco = array();
					$vectorresporccocuantos = array();
					$n=0;
					foreach($vectorcondiciones as $cod_clasificacion => $arr_agrupacion)
					{

							$qccoxagrupacion = 	" SELECT SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos,".$wbasedato."_000051.Grucag,Idecco  "
												."  FROM ".$wbasedato."_000005 left join ".$wbasedato."_000051 on (Desagr = Grucod), ".$wbasedato."_000007 ,  ".$wbasedato."_000032 , talhuma_000013 "
												." WHERE ".$arr_agrupacion." "
												."   AND Evades = Descod"
												."   AND Mcauco = Evaevo"
												// ."   AND Desest !='off' "
												."   AND Ideuse = Mcauco "
												."   AND Idecco IN ('".implode("','",$vectorcodccocontestado)."') "
												//."   AND ".$wbasedato."_000032.fecha_data BETWEEN '".$fechainicial1."' AND '".$fechafinal1."' ";
												." AND Mcaano = '".$wano1."' AND Mcaper = '".$wperiodo1."' ";
							if($wusuario_consultado!='')
							{
								$qccoxagrupacion = $qccoxagrupacion." AND Ideuse='".$wusuario_consultado."' ";
							}

							$qccoxagrupacion .= " GROUP BY ".$wbasedato."_000013.Idecco";
							$arr_querys_aplican[] 	= $qccoxagrupacion;


					}
					$union=" UNION  ";
					$querys_aplican = implode($union, $arr_querys_aplican);



					$resccoxagrupacion = mysql_query($querys_aplican,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qccoxagrupacion." - ".mysql_error());

					while($rowccoxagrupacion  = mysql_fetch_array($resccoxagrupacion))
					{
						$cuantos = $rowccoxagrupacion['Cuantos'];


						@$resvalorcco = $rowccoxagrupacion['Suma'] / $cuantos ;
						$roundresvalorcco = round($resvalorcco ,2);
						$vectorresporcco[$rowccoxagrupacion['Grucag']][$rowccoxagrupacion['Idecco']]= $roundresvalorcco;
						$vectorresporccocuantos[$rowccoxagrupacion['Grucag']][$rowccoxagrupacion['Idecco']]= $cuantos;
					}

					if( $unperiodo!='1')
					{

						$vectorresporcco1 = array();
						$vectorresporccocuantos1=array();

						$n=0;
						while($n < count($vectoragrupaciones))
						{

							$j=0;
							while($j < count($vectorccocontestado))
							{


								$qccoxagrupacion = 	" SELECT SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos  "
													."  FROM ".$wbasedato."_000005, ".$wbasedato."_000007 ,  ".$wbasedato."_000032 , talhuma_000013 "
													." WHERE Desagr='".$vectoragrupaciones[$n]."' "
													."   AND Evades = Descod"
													."   AND Mcauco = Evaevo"
													// ."   AND Desest !='off' "
													."   AND Ideuse = Mcauco "
													."   AND Idecco = '".$vectorcodccocontestado[$j]."' "
													//."   AND  ".$wbasedato."_000032.fecha_data  BETWEEN '".$fechainicial2."' AND '".$fechafinal2."' ";
													." 	 AND Mcaano = '".$wano2."' AND Mcaper = '".$wperiodo2."' ";

								if($wusuario_consultado!='')
								{
									$qccoxagrupacion = $qccoxagrupacion." AND Ideuse='".$wusuario_consultado."' ";
								}

								$resccoxagrupacion = mysql_query($qccoxagrupacion,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qccoxagrupacion." - ".mysql_error());
								$rowccoxagrupacion = mysql_fetch_array($resccoxagrupacion);

								$cuantos1=$rowccoxagrupacion['Cuantos'];

								@$resvalorcco1 = $rowccoxagrupacion['Suma'] / $cuantos1;
								// nuevo
								@$resvalorcco1 =  @$resvalorcco1 *5;
								$roundresvalorcco1 = round($resvalorcco1 ,2);
								$vectorresporcco1[$vectoragrupaciones[$n]][$vectorcodccocontestado[$j]]= $roundresvalorcco1;
								$vectorresporccocuantos1[$vectoragrupaciones[$n]][$vectorcodccocontestado[$j]]=$cuantos1;
								$j++;
							}
							$n++;
						}
					}
				}
				$m=0;

				// si se va a ver por todos las agrupaciones sin ningun detalle


				echo"<br><br>";

				echo"<table style='border:#2A5DB0 1px solid'  id='tablareporte'  align='center'>";

				echo"<tr><td class='encabezadoTabla' rowspan='2' >Unidad</td>";

				echo"<td align='center' class='encabezadoTabla' colspan='".count($vectorcondiciones)." '>Nombre del indicador</td>";
				//funciona si es porcco
				//------------------------------------

				if ($wcco!='' )
				{
					$j=0;
					echo"<tr>";
					foreach($vectorcondiciones as $clasificacion => $codigo)
					{
						echo"<td class='encabezadoTabla' align='center'>".$vectornomagrupaciones[$clasificacion]."</td>";
						$j++;
					}
				}
				//echo"<td class='encabezadoTabla' >TOTAL CLINICA</td>";
				echo"</tr>";
				//-------------------
				if($wcco!='')
				{

					$auxind=0;
					foreach($vectorccocontestado as $clavecco => $codigocco)
					{

						//nuevo jc
						$vectorresporcco[$clasificacion][$vectorcodccocontestado[$clavecco]] = $vectorresporcco[$clasificacion][$vectorcodccocontestado[$clavecco]]*5;
						$vectorresporcco1[$clasificacion][$vectorcodccocontestado[$clavecco]] = $vectorresporcco1[$clasificacion][$vectorcodccocontestado[$clavecco]]*5;

						$color = traecolor ($vectorresporcco[$clasificacion][$vectorcodccocontestado[$clavecco]] ,$vectormaximo,$vectorminimo,$vectorcolor) ;
						$color1 = traecolor ($vectorresporcco1[$clasificacion][$vectorcodccocontestado[$clavecco]] ,$vectormaximo,$vectorminimo,$vectorcolor) ;



						if($vectorresporcco[$clasificacion][$vectorcodccocontestado[$clavecco]] =='' )
							$vectorresporcco[$clasificacion][$vectorcodccocontestado[$clavecco]]= 0;

						if($vectorresporccocuantos[$clasificacion][$vectorcodccocontestado[$clavecco]] == '')
							$vectorresporccocuantos[$clasificacion][$vectorcodccocontestado[$clavecco]]= 0;

						if($vectorresporccocuantosnoaplica[$clasificacion][$vectorcodccocontestado[$clavecco]]=='')
						  $vectorresporccocuantosnoaplica[$clasificacion][$vectorcodccocontestado[$clavecco]] = 0;


						$condicion = (count($vectoragrupaciones[$clasificacion]) > 1) ? "".implode("|", $vectoragrupaciones[$clasificacion])."" : "".implode("|", $vectoragrupaciones[$clasificacion])."";
						if (($auxind%2)==0)
						{
							$wcf="fila1";  // color de fondo de la fila
						}
						else
						{
							$wcf="fila2"; // color de fondo de la fila
						}
						echo"<tr >";
						echo"<td class='".$wcf."' colspan='1' align='Left'  ><div id='sss'>".$codigocco."</div></td><td align='center'  class='msg_tooltip'    style='cursor: pointer;' bgcolor='".$color."' ><div title='Respuestas para c&aacute;lculo  ".$vectorresporccocuantos[$clasificacion][$vectorcodccocontestado[$clavecco]]."' onclick='abrirporpreguntas(this, \"".$condicion."\",\"".$vectorcodccocontestado[$clavecco]."\",\"".$fechainicial1."\",\"".$fechafinal1."\",\"".$vectornomagrupaciones[$clasificacion]."\",\"".$vectorccocontestado[$clavecco]."\",\"".$vectorresporcco[$clasificacion][$vectorcodccocontestado[$clavecco]]."\",\"".$auxind."\",\"".$tipodeformato."\")'>".$vectorresporcco[$clasificacion][$vectorcodccocontestado[$clavecco]] ." <img style='float: right; display: inline-block;' id='img_mas-".$auxind."' src='../../images/medical/hce/mas.PNG'></div><div id='ddiv-".$auxind."'></div></td>";
						echo"</tr>";
						$auxind++;
					}
				}

			echo"</table>";
			break;

			case '05': // tipo 05- supervicion tecnica invecla
			if ($wcco!='')
			{
				//venctor de cco que se llena con los nombres de los cco que tienen respuestas
				$vectorccocontestado = array();
				//venctor de cco que se llena con los codigos de los cco que tienen respuestas
				$vectorcodccocontestado  = array();

				if ($wcco=='todos')
				{

					// Consulta que se arroja los centros de costo en que se hara la busqueda
					// hay ciertos parametros que se van agregando  si son seleccionados

					$wbasedatocentrocostos= consultarAliasPorAplicacion($conex, $wbasedato, 'centrocostos');

					if ($fechainicial1=='')
					{
						$querycco =	 "SELECT distinct(Cconom) AS Nombre, Enccco"
									."  FROM ".$wbasedato."_000049 , ".$wbasedatocentrocostos." "
									." WHERE Enccco = Ccocod "
									."   AND ".$wbasedato."_000049.Encper ='".$wperiodo1."'"
									."   AND ".$wbasedato."_000049.Encano ='".$wano1."'"
									."   AND Encese= 'cerrado'";
					}
					else
					{
						$querycco =	 "SELECT distinct(Cconom) AS Nombre, Enccco"
									."  FROM ".$wbasedato."_000049 , ".$wbasedatocentrocostos." "
									." WHERE Enccco = Ccocod "
									."   AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$fechainicial1."' AND '".$fechafinal1."'"
									."   AND Encese= 'cerrado'";


					}
					// se agrega esto a la consulta para que se busque por afinidad .
					if($wafinidad!='seleccione')
					{
						$querycco = $querycco . " AND Encafi LIKE  '%".$wafinidad."%' ";
					}

					// se agrega esto a la consulta para que se busque por entidad .
					if($wentidad!='seleccione')
					{
						$querycco = $querycco . " AND Encdia =  '".$entidades[$wentidad]."' ";
					}

					// se agrega esto al query para que busque por centro de costos hospitalarios
					if($whospitalario=='si')
					{
						$querycco = $querycco . " AND Ccohos = 'on' ";
					}

					// se agrega esto al query para que busque por centro de costos urgencias
					if($wurgencias=='si')
					{
						$querycco = $querycco . " AND Ccourg = 'on' ";
					}

					// se agrega esto al query para que busque por centro de costos cirugia
					if($wcirugia=='si')
					{
						$querycco = $querycco . " AND Ccocir = 'on' ";
					}

					// se agrega esto al query para que busque por centro de costos terapeutico
					if($wterapeutico=='si')
					{
						$querycco = $querycco . " AND Ccoter = 'on' ";
					}

					// se agrega esto al query para que busque por centro de costos de ayudas diagnosticas
					if($wayudas=='si')
					{
						$querycco = $querycco . " AND Ccoayu = 'on' ";
					}
					
					
					//---se puso quemado para luego ser borrado
					if($wbasedatocentrocostos==$wcostosyp."_000005")
					{
						 $querycco = $querycco." AND  Ccoemp ='01'";
					}


					// echo $querycco;
					//------------------------------------------------------------

					// si se se maneja mas de un periodo se debe hacer un Union que arroja los centros de costos
					// donde hay respuestas tando del periodo 1 como del period 2
					if($unperiodo!='1')
					{
						// se concatena a la consulta un union que saca los centros de costos que tienen respuesta en un periodo
						// con los del otro periodo

						$wbasedatocentrocostos= consultarAliasPorAplicacion($conex, $wbasedato, 'centrocostos');

						$querycco =	$querycco."    UNION DISTINCT "
												."SELECT distinct(Cconom) AS Nombre, Enccco"
												."  FROM ".$wbasedato."_000049 , ".$wbasedatocentrocostos." "
												." WHERE Enccco = Ccocod "
												."   AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$fechainicial2."' AND '".$fechafinal2."'"
												."   AND Encese= 'cerrado'";


						// se agrega esto a la consulta para que se busque por afinidad .


						if($wafinidad!='seleccione'  )
						{
							$querycco = $querycco . "AND Encafi LIKE  '%".$wafinidad."%' ";
						}

						// se agrega esto a la consulta para que se busque por entidad .
						if($wentidad!='seleccione')
						{
							$querycco = $querycco . "AND Encdia =  '".$entidades[$wentidad]."' ";
						}

						// se agrega esto al query para que busque por centro de costos hospitalarios
						if($whospitalario=='si')
						{
							$querycco = $querycco . " AND Ccohos = 'on' ";
						}

						// se agrega esto al query para que busque por centro de costos urgencias
						if($wurgencias=='si')
						{
							$querycco = $querycco . " AND Ccourg = 'on' ";
						}

						// se agrega esto al query para que busque por centro de costos cirugia
						if($wcirugia=='si')
						{
							$querycco = $querycco . " AND Ccocir = 'on' ";
						}

						// se agrega esto al query para que busque por centro de costos terapeutico
						if($wterapeutico=='si')
						{
							$querycco = $querycco . " AND Ccoter = 'on' ";
						}
						// se agrega esto al query para que busque por centro de costos de ayudas diagnosticas
						if($wayudas=='si')
						{
							$querycco = $querycco . " AND Ccoayu = 'on' ";
						}
						
						
						//---se puso quemado para luego ser borrado
						if($wbasedatocentrocostos==$wcostosyp."_000005")
						{
							 $querycco = $querycco." AND  Ccoemp ='01'";
						}

					}

					// se imprime la consulta para ver si esta buena.

					$resquerycco = mysql_query($querycco,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$querycco." - ".mysql_error());

					//echo $querycco;


					$i=0;
					// por medio de este while se llenan los vectores  vectorccocontestado con el nombre y vectorcodccocontestado con el codigo
					while($rowquerycco = mysql_fetch_array($resquerycco))
					{
						$vectorccocontestado[$i] = $rowquerycco['Nombre'];
						$vectorcodccocontestado[$i] = $rowquerycco['Enccco'];
						$i++;
					}

				}
				else
				{
					$vectorccocontestado[]  = $wnomcco;
					$vectorcodccocontestado[] = $wcodcco;
				}

				// vector donde se almacena el promedio de las notas por centro de costos
				$vectorresporcco = array();
				// vector donde se almacena la cantidad de preguntas contestadas por agrupacion en centro de costos
				$vectorresporccocuantos = array();
				$vectorresporccocuantosnoaplica= array();
				$n=0;
				$xx=0;

				$arr_querys_aplican = array();
				$arr_querys_NOaplican = array();
				foreach($vectorcondiciones as $cod_clasificacion => $arr_agrupacion)
				{
					$xx= $xx +1;
					if ($fechainicial1=='')
					{
						$qccoxagrupacion = 	"SELECT ".$wbasedato."_000049.Enccco, ".$wbasedato."_000051.Grucag, SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos, Empcod   "
											."  FROM ".$wbasedato."_000005 left join ".$wbasedato."_000051 on (Desagr = Grucod), ".$wbasedato."_000007 , ".$wbasedato."_000049 LEFT JOIN ".$wbasedatocli."_000024 ON Encdia = Empcod "
											." WHERE ".$arr_agrupacion." "
											."   AND Evades = Descod"
											."   AND Evafco = Encenc "
											."   AND Enchis = Evaevo "
											."   AND Enccco IN ('".implode("','",$vectorcodccocontestado)."') "
											."   AND ".$wbasedato."_000049.Encper ='".$wperiodo1."'"
											."   AND ".$wbasedato."_000049.Encano ='".$wano1."'"
											."   AND Encese= 'cerrado'";
											// ."   AND Desest !='off' ";


					}
					else
					{
						$qccoxagrupacion = 	" SELECT ".$wbasedato."_000049.Enccco, ".$wbasedato."_000051.Grucag, SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos,Empcod   "
											."  FROM ".$wbasedato."_000005 left join ".$wbasedato."_000051 on (Desagr = Grucod), ".$wbasedato."_000007 , ".$wbasedato."_000049 LEFT JOIN ".$wbasedatocli."_000024 ON Encdia = Empcod  "
											." WHERE ".$arr_agrupacion." "
											."   AND Evades = Descod"
											."   AND Evafco = Encenc "
											."   AND Enchis = Evaevo "
											."   AND Evaper = encper "
											."   AND Evaano = encano "
											."   AND Enccco IN ('".implode("','",$vectorcodccocontestado)."') "
											."   AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$fechainicial1."' AND '".$fechafinal1."' "
											."   AND Encese= 'cerrado'";
											// ."   AND Desest !='off' ";

					}


						$qccoxagrupacionconnoaplica = $qccoxagrupacion;

						$qccoxagrupacion = $qccoxagrupacion."   AND Evacal != 0 ";
						// se agrega esto a la consulta para que se busque por afinidad .
						if($wafinidad!='seleccione')
						{
							$qccoxagrupacion = $qccoxagrupacion . " AND Encafi LIKE  '%".$wafinidad."%' ";
							$qccoxagrupacionconnoaplica =$qccoxagrupacionconnoaplica . " AND Encafi LIKE  '%".$wafinidad."%' ";
						}

						// se agrega esto a la consulta para que se busque por entidad .
						if($wentidad!='seleccione')
						{
							$qccoxagrupacion = $qccoxagrupacion . " AND Encdia =  '".$entidades[$wentidad]."' ";
							$qccoxagrupacionconnoaplica = $qccoxagrupacionconnoaplica . " AND Encdia =  '".$entidades[$wentidad]."' ";
						}

						if($wspregunta!='seleccione')
						{
							$qccoxagrupacion = $qccoxagrupacion . " AND Descod  = '".$wspregunta."' ";
							$qccoxagrupacionconnoaplica = $qccoxagrupacionconnoaplica . " AND Descod  = '".$wspregunta."' ";
						}

						// se agrega esto al query para que busque solo las empresas que sean del tipo establecido por el usuario
						if($wtipodeempresa !='')
						{
							if ($wtipodeempresa =='PAP')
							{
								$wtipoempresa_buscar = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Empresas_PAP_servicio_usuario');
								$qccoxagrupacion = $qccoxagrupacion . " AND Emptem IN (".$wtipoempresa_buscar.")  ";
								$qccoxagrupacionconnoaplica = $qccoxagrupacionconnoaplica . " AND Emptem IN (".$wtipoempresa_buscar.")  ";
							}
							if ($wtipodeempresa =='particular')
							{
								$wtipoempresa_buscar = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Empresas_particular_servicio_usuario');
								$qccoxagrupacion = $qccoxagrupacion . " AND Emptem IN (".$wtipoempresa_buscar.")  ";
								$qccoxagrupacionconnoaplica = $qccoxagrupacionconnoaplica . " AND Emptem IN (".$wtipoempresa_buscar.")  ";

							}
							if ($wtipodeempresa =='pos')
							{

								$wtipoempresa_buscar_pap = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Empresas_PAP_servicio_usuario');
								$wtipoempresa_buscar_particular = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Empresas_particular_servicio_usuario');
								$qccoxagrupacion = $qccoxagrupacion . " AND Emptem NOT IN (".$wtipoempresa_buscar_pap.",".$wtipoempresa_buscar_particular.")  ";
								$qccoxagrupacionconnoaplica = $qccoxagrupacionconnoaplica . " AND Emptem NOT IN (".$wtipoempresa_buscar_pap.",".$wtipoempresa_buscar_particular.")  ";

							}


						}



						$qccoxagrupacion .= " GROUP BY ".$wbasedato."_000049.Enccco";
						$qccoxagrupacionconnoaplica .= " GROUP BY ".$wbasedato."_000049.Enccco";

						$arr_querys_aplican[] 	= $qccoxagrupacion;
						$arr_querys_NOaplican[]	= $qccoxagrupacionconnoaplica;

				}
				$union=" UNION  ";
				$querys_aplican = implode($union, $arr_querys_aplican);
				$querys_NOaplican = implode($union, $arr_querys_NOaplican);

				 // echo '</pre>'.$querys_aplican.'</pre><br><br>';
				$resccoxagrupacion = mysql_query($querys_aplican,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qccoxagrupacion." - ".mysql_error());
				//$rowccoxagrupacion = mysql_fetch_array($resccoxagrupacion);
				$resccoxagrupacionconnoaplica = mysql_query($querys_NOaplican,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$querys_NOaplican." - ".mysql_error());

				while($rowccoxagrupacion  = mysql_fetch_array($resccoxagrupacion))
				{
					$cuantos = $rowccoxagrupacion['Cuantos'];

					// modificacion
					@$resvalorcco = $rowccoxagrupacion['Suma'] / $cuantos ;

					$resultadoformula = array();
					$resultadoformula = AnalizarFormula ( $rowccoxagrupacion['Grucag'],$resvalorcco);
					$resvalorcco	= $resultadoformula['valor'];
					$simbolo		= $resultadoformula['simbolo'];


					$roundresvalorcco = round($resvalorcco ,2);
					$vectorresporcco[$rowccoxagrupacion['Grucag']][$rowccoxagrupacion['Enccco']]= $roundresvalorcco;
					$vectorresporccocuantos[$rowccoxagrupacion['Grucag']][$rowccoxagrupacion['Enccco']]= $cuantos;
				}

				while( $rowccoxagrupacionconnoaplica = mysql_fetch_array($resccoxagrupacionconnoaplica) )
				{
					$cuantosnoaplica = $rowccoxagrupacionconnoaplica['Cuantos'];
					$vectorresporccocuantosnoaplica[$rowccoxagrupacionconnoaplica['Grucag']][$rowccoxagrupacionconnoaplica['Enccco']]= $cuantosnoaplica;
				}


				// si tiene mas de  uno , se debe hacer para el otro periodo
				if( $unperiodo!='1')
				{
					$arr_querys_aplican = array();
					$arr_querys_NOaplican = array();
					$vectorresporcco1 = array();
					$n=0;
					foreach($vectorcondiciones as $cod_clasificacion => $arr_agrupacion)
					{

							$qccoxagrupacion = 	" SELECT ".$wbasedato."_000049.Enccco, ".$wbasedato."_000051.Grucag, SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos  "
												."  FROM ".$wbasedato."_000005 left join ".$wbasedato."_000051 on (Desagr = Grucod), ".$wbasedato."_000007 , ".$wbasedato."_000049  "
												." WHERE ".$arr_agrupacion." "
												."   AND Evades = Descod"
												."   AND Evafco = Encenc "
												."   AND Enchis = Evaevo "
												."   AND Evaper = encper "
												."   AND Evaano = encano "
												."   AND Enccco IN ('".implode("','",$vectorcodccocontestado)."') "
												."   AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$fechainicial2."' AND '".$fechafinal2."' "
												."   AND Encese= 'cerrado'";
											// ."   AND Desest !='off' ";


							$qccoxagrupacionconnoaplica = $qccoxagrupacion;

							$qccoxagrupacion = $qccoxagrupacion."   AND Evacal != 0 ";

							// se agrega esto a la consulta para que se busque por afinidad .
							if($wafinidad!='seleccione')
							{
								$qccoxagrupacion = $qccoxagrupacion . "AND Encafi LIKE  '%".$wafinidad."%' ";
								$qccoxagrupacionconnoaplica = $qccoxagrupacionconnoaplica . "AND Encafi LIKE  '%".$wafinidad."%' ";
							}

							// se agrega esto a la consulta para que se busque por entidad .
							if($wentidad!='seleccione')
							{
								$qccoxagrupacion = $qccoxagrupacion . "AND Encdia =  '".$entidades[$wentidad]."' ";
								$qccoxagrupacionconnoaplica = $qccoxagrupacionconnoaplica . "AND Encdia =  '".$entidades[$wentidad]."' ";
							}

							if($wspregunta!='seleccione')
							{
								$qccoxagrupacion = $qccoxagrupacion . " AND Descod  = '".$wspregunta."' ";
								$qccoxagrupacionconnoaplica = $qccoxagrupacionconnoaplica . " AND Descod  = '".$wspregunta."' ";
							}

							$qccoxagrupacion .= " GROUP BY ".$wbasedato."_000049.Enccco";
							$qccoxagrupacionconnoaplica .= " GROUP BY ".$wbasedato."_000049.Enccco";

							$arr_querys_aplican[] 	= $qccoxagrupacion;
							$arr_querys_NOaplican[]	= $qccoxagrupacionconnoaplica;

					}

						$union=" UNION  ";
						$querys_aplican = implode($union, $arr_querys_aplican);
						$querys_NOaplican = implode($union, $arr_querys_NOaplican);

						//echo $querys_aplican;

						$resccoxagrupacion = mysql_query($querys_aplican,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$querys_aplican." - ".mysql_error());
						//$rowccoxagrupacion = mysql_fetch_array($resccoxagrupacion);
						$resccoxagrupacionconnoaplica = mysql_query($querys_NOaplican,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$querys_NOaplican." - ".mysql_error());


						while($rowccoxagrupacion  = mysql_fetch_array($resccoxagrupacion))
						{
							$cuantos1 = $rowccoxagrupacion['Cuantos'];


							@$resvalorcco1 = $rowccoxagrupacion['Suma'] / $cuantos1 ;
							$resultadoformula = array();
							$resultadoformula = AnalizarFormula ( $rowccoxagrupacion['Grucag'],$resvalorcco1);
							$resvalorcco1	= $resultadoformula['valor'];
							$simbolo1		= $resultadoformula['simbolo'];

							$roundresvalorcco1 = round($resvalorcco1 ,2);

							$vectorresporcco1[$rowccoxagrupacion['Grucag']][$rowccoxagrupacion['Enccco']]= $roundresvalorcco1;
							$vectorresporccocuantos1[$rowccoxagrupacion['Grucag']][$rowccoxagrupacion['Enccco']]= $cuantos1;
						}

						while( $rowccoxagrupacionconnoaplica = mysql_fetch_array($resccoxagrupacionconnoaplica) )
						{
							$cuantosnoaplica1 = $rowccoxagrupacionconnoaplica['Cuantos'];
							$vectorresporccocuantosnoaplica1[$rowccoxagrupacionconnoaplica['Grucag']][$rowccoxagrupacionconnoaplica['Enccco']]= $cuantosnoaplica1;
						}
				}

			}
			echo"<br><br>";
				echo"<table style='border:#2A5DB0 1px solid' id='tablareporte'>";
				if( $unperiodo=='1')
				{
					echo"<tr><td class='encabezadoTabla' rowspan='2' >Nombre del Indicador</td>";
					echo"<td align='center' class='encabezadoTabla' colspan='".(count($vectorccocontestado) + 1)." '>Notas Promedio</td>";
				}
				else
				{
				  echo"<tr><td class='encabezadoTabla' rowspan='3' >Nombre del Indicador</td>";
				  echo"<td align='center' class='encabezadoTabla' colspan='".((count($vectorccocontestado) + 2) * 2)." '>Notas Promedio</td>";
				}

				//funciona si es porcco
				//------------------------------------

				if ($wcco!='' )
				{
					$j=0;
					echo"<tr class='pisos'>";

						if( $unperiodo=='1')
						{
							$j=0;
							while($j < count($vectorccocontestado))
							{
								if ($tipodeformato =='05')
								{
									$qfechaevaluacion = "SELECT ".$wbasedato."_000049.Fecha_data , Encenc , Fordes
														   FROM ".$wbasedato."_000049, ".$wbasedato."_000002
														   WHERE  Enccco='".$vectorcodccocontestado[$j]."'
															AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$fechainicial1."' AND '".$fechafinal1."'
															AND Encese = 'cerrado'
															AND Encenc = Forcod ";
									$resqfechaevaluacion = mysql_query($qfechaevaluacion,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qfechaevaluacion." - ".mysql_error());

									$htmtable = "<br><table style='background-color: white' ><tr class='fila1' ><td>Nombre</td><td>Fecha</td></tr>";
									while($rowqfechaevaluacion  = mysql_fetch_array($resqfechaevaluacion))
									{
										$htmtable .= "<tr class='fila2'><td>".$rowqfechaevaluacion['Fordes']."</td><td>".$rowqfechaevaluacion['Fecha_data']."</td></tr>";
									}
									$htmtable .= "</table>";

									echo"<td class='encabezadoTabla' align='center'>".$vectorccocontestado[$j]." <input type='button' style='cursor : pointer' onclick='verocultacco(\"".$vectorcodccocontestado[$j]."\")' value='ver info'><div id='oculta_".$vectorcodccocontestado[$j]."' style='display:none'>".$htmtable."</div></td>";
								}
								else
								{
									echo"<td class='encabezadoTabla' align='center'>".$vectorccocontestado[$j]." </td>";
								}
								$j++;
							}
							echo"<td class='encabezadoTabla' >TOTAL CLINICA</td>";
							echo"</tr>";
						}
						else
						{
							$j=0;
							while($j < count($vectorccocontestado))
							{
								echo"<td class='encabezadoTabla' colspan='2' align='center'>".$vectorccocontestado[$j]."</td>";
								$j++;
							}
							echo"<td class='encabezadoTabla' colspan='2'>TOTAL CLINICA</td>";
							echo"</tr>";
							echo"<tr>";
							$j=0;
							while($j < count($vectorccocontestado))
							{
								echo"<td class='encabezadoTabla Periodo'  align='center'>Periodo1</td><td class='encabezadoTabla Periodo' align='center'>Periodo2</td>";
								$j++;
							}
							echo"<td class='encabezadoTabla Periodo' align='center'>Periodo1</td><td class='encabezadoTabla Periodo' align='center'>Periodo2</td>";
							echo"</tr>";

						}

				}

				if($wcco!='')
				{

					$auxind = 0;
					foreach ( $vectorcondiciones  as $clasificacion => $codigo )
					{

						//--------nombre de agrupacion
						echo"<tr class='ocultar' >";
						echo"<td width='200' class='encabezadoTabla' colspan='1' align='Left'  ><div id=td_nombreagrupacion>".$vectornomagrupaciones[$clasificacion]."</div></td>";
						//---------------------------

						if($wcco!='')
						{
							$j=0;
							while($j < count($vectorccocontestado))
							{
								// trae los datos de cada uno de los centro de costos por agrupacion
								$color = traecolor ($vectorresporcco[$clasificacion][$vectorcodccocontestado[$j]] ,$vectormaximo,$vectorminimo,$vectorcolor) ;
								$color1 = traecolor ($vectorresporcco1[$clasificacion][$vectorcodccocontestado[$j]] ,$vectormaximo,$vectorminimo,$vectorcolor) ;

								if( $unperiodo=='1')
								{
									if($vectorresporcco[$clasificacion][$vectorcodccocontestado[$j]] =='' )
										$vectorresporcco[$clasificacion][$vectorcodccocontestado[$j]]= 0;

									if($vectorresporccocuantos[$clasificacion][$vectorcodccocontestado[$j]] == '')
										$vectorresporccocuantos[$clasificacion][$vectorcodccocontestado[$j]]= 0;

									if($vectorresporccocuantosnoaplica[$clasificacion][$vectorcodccocontestado[$j]]=='')
									  $vectorresporccocuantosnoaplica[$clasificacion][$vectorcodccocontestado[$j]] = 0;


									$condicion = (count($vectoragrupaciones[$clasificacion]) > 1) ? "".implode("|", $vectoragrupaciones[$clasificacion])."" : "".implode("|", $vectoragrupaciones[$clasificacion])."";
									echo"<td align='center'   style='cursor: pointer;' bgcolor='".$color."'><div  class='msg_tooltip'  title='Respuestas para c&aacute;lculo  ".$vectorresporccocuantos[$clasificacion][$vectorcodccocontestado[$j]]."<br>  Respuestas Totales ".$vectorresporccocuantosnoaplica[$clasificacion][$vectorcodccocontestado[$j]]."'  onclick='abrirporpreguntas(this, \"".$condicion."\",\"".$vectorcodccocontestado[$j]."\",\"".$fechainicial1."\",\"".$fechafinal1."\",\"".$vectornomagrupaciones[$clasificacion]."\",\"".$vectorccocontestado[$j]."\",\"".$vectorresporcco[$clasificacion][$vectorcodccocontestado[$j]]."\", \"".$auxind."\")'><img style='float: right; display: inline-block;' id='img_mas-".$auxind."' src='../../images/medical/hce/mas.PNG'>".$vectorresporcco[$clasificacion][$vectorcodccocontestado[$j]]."".$simbolo." </div><div id='ddiv-".$auxind."'></div></td>";

									$auxind++;
								}
								else
								{
									if($vectorresporcco[$clasificacion][$vectorcodccocontestado[$j]] =='' )
										$vectorresporcco[$clasificacion][$vectorcodccocontestado[$j]]= 0;

									if($vectorresporccocuantos[$clasificacion][$vectorcodccocontestado[$j]] == '')
										$vectorresporccocuantos[$clasificacion][$vectorcodccocontestado[$j]]= 0;

									if($vectorresporccocuantosnoaplica[$clasificacion][$vectorcodccocontestado[$j]]=='')
									  $vectorresporccocuantosnoaplica[$clasificacion][$vectorcodccocontestado[$j]] = 0;


									if($vectorresporcco1[$clasificacion][$vectorcodccocontestado[$j]] =='' )
										$vectorresporcco1[$clasificacion][$vectorcodccocontestado[$j]]= 0;

									if($vectorresporccocuantos1[$clasificacion][$vectorcodccocontestado[$j]] == '')
										$vectorresporccocuantos1[$clasificacion][$vectorcodccocontestado[$j]]= 0;

									if($vectorresporccocuantosnoaplica1[$clasificacion][$vectorcodccocontestado[$j]]=='')
									  $vectorresporccocuantosnoaplica1[$clasificacion][$vectorcodccocontestado[$j]] = 0;

									$condicion = (count($vectoragrupaciones[$clasificacion]) > 1) ? "".implode("|", $vectoragrupaciones[$clasificacion])."" : "".implode("|", $vectoragrupaciones[$clasificacion])."";
									echo"<td bgcolor='".$color."'   align='center' style='cursor: pointer;' ><div class='msg_tooltip'  title='Respuestas para c&aacute;lculo ".$vectorresporccocuantos[$clasificacion][$vectorcodccocontestado[$j]]."<br> Respuestas Totales ".$vectorresporccocuantosnoaplica[$clasificacion][$vectorcodccocontestado[$j]]."' onclick='abrirporpreguntas(this,\"".$condicion."\",\"".$vectorcodccocontestado[$j]."\",\"".$fechainicial1."\",\"".$fechafinal1."\",\"".$vectornomagrupaciones[$clasificacion]."\",\"".$vectorccocontestado[$j]."\",\"".$vectorresporcco[$clasificacion][$vectorcodccocontestado[$j]]."\", \"".$auxind."\")' ><img style='float: right; display: inline-block;' id='img_mas-".$auxind."' src='../../images/medical/hce/mas.PNG'>".$vectorresporcco[$clasificacion][$vectorcodccocontestado[$j]]." ".$simbolo1."</div><div id='ddiv-".$auxind."'></div></td >";

									$auxind++;
									echo"<td  bgcolor='".$color1."' style='cursor: pointer;' align='center' ><div class='msg_tooltip'  title='Respuestas para c&aacute;lculo ".$vectorresporccocuantos1[$clasificacion][$vectorcodccocontestado[$j]]."<br> Respuetas Totales ".$vectorresporccocuantosnoaplica1[$clasificacion][$vectorcodccocontestado[$j]]."'    onclick='abrirporpreguntas(this,\"".$condicion."\",\"".$vectorcodccocontestado[$j]."\",\"".$fechainicial2."\",\"".$fechafinal2."\",\"".$vectornomagrupaciones[$clasificacion]."\",\"".$vectorccocontestado[$j]."\",\"".$vectorresporcco1[$clasificacion][$vectorcodccocontestado[$j]]."\", \"".$auxind."\")' ><img style='float: right; display: inline-block;' id='img_mas-".$auxind."' src='../../images/medical/hce/mas.PNG'>".$vectorresporcco1[$clasificacion][$vectorcodccocontestado[$j]]." ".$simbolo1."</div><div id='ddiv-".$auxind."'></div></td>";
									$auxind++;

								}

								$j++;


							}



						}
						//-------------total de la agrupacion
						if ($fechainicial1=='')
						{
						 		$qtagrupacion = " SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos  "
												."  FROM ".$wbasedato."_000005, ".$wbasedato."_000007 , ".$wbasedato."_000049 "
												." WHERE ".$vectorcondiciones[$clasificacion]." "
												."   AND Evades = Descod"
												."   AND Evafco = Encenc "
												."   AND Enchis = Evaevo "
												."   AND ".$wbasedato."_000049.Encper ='".$wperiodo1."'"
												."   AND ".$wbasedato."_000049.Encano ='".$wano1."'"
												."   AND Encese= 'cerrado'";
												// ."   AND Desest !='off' ";



						}
						else
						{
							$qtagrupacion = 	" SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos  "
												."  FROM ".$wbasedato."_000005, ".$wbasedato."_000007 , ".$wbasedato."_000049 "
												." WHERE ".$vectorcondiciones[$clasificacion]." "
												."   AND Evades = Descod"
												."   AND Evafco = Encenc "
												."   AND Enchis = Evaevo "
												."   AND Evaper = encper "
												."   AND Evaano = encano "
												."   AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$fechainicial1."' AND '".$fechafinal1."' "
												."   AND Encese= 'cerrado'";
												// ."   AND Desest !='off' ";

						}


						$qtagrupacionnoaplica = $qtagrupacion;
						$qtagrupacion = $qtagrupacion ."   AND Evacal !=0  ";


						// si la busqueda es por afinidad
						if($wafinidad!='seleccione')
						{
							$qtagrupacion = $qtagrupacion . "AND Encafi LIKE  '%".$wafinidad."%' ";
							$qtagrupacionnoaplica = $qtagrupacionnoaplica . "AND Encafi LIKE  '%".$wafinidad."%' ";
						}

						// se agrega esto a la columna para que se busque por entidad .
						if($wentidad!='seleccione')
						{
							$qtagrupacion =$qtagrupacion . "AND Encdia =  '".$entidades[$wentidad]."' ";
							$qtagrupacionnoaplica = $qtagrupacionnoaplica . "AND Encdia =  '".$entidades[$wentidad]."' ";
						}

						if($wspregunta!='seleccione')
						{
							$qtagrupacion = $qtagrupacion . " AND Descod  = '".$wspregunta."' ";
							$qtagrupacionnoaplica = $qtagrupacionnoaplica . " AND Descod  = '".$wspregunta."' ";
						}

						// echo "<br>hola".$qtagrupacion;
						$restagrupacion = mysql_query($qtagrupacion,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qtagrupacion." - ".mysql_error());
						$rowtagrupacion = mysql_fetch_array($restagrupacion);

						$restagrupacionnoaplica = mysql_query($qtagrupacionnoaplica,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qtagrupacionnoaplica." - ".mysql_error());
						$rowtagrupacionnoaplica = mysql_fetch_array($restagrupacionnoaplica);

						$qtagrupacion1 = 	" SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos  "
											."  FROM ".$wbasedato."_000005, ".$wbasedato."_000007 , ".$wbasedato."_000049 "
											." WHERE ".$vectorcondiciones[$clasificacion]." "
											."   AND Evades = Descod"
											."   AND Evafco = Encenc "
											."   AND Evaevo = Enchis "
											."   AND Evaper = encper "
											."   AND Evaano = encano "
											."   AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$fechainicial2."' AND '".$fechafinal2."' "
											."   AND Encese= 'cerrado'";


						$qtagrupacionnoaplica1 = $qtagrupacion1;
						$qtagrupacion1= $qtagrupacion1 ."   AND Evacal !=0  ";

						// si la busqueda es por afinidad
						if($wafinidad!='seleccione')
						{
							$qtagrupacion1 = $qtagrupacion1 . "AND Encafi LIKE  '%".$wafinidad."%' ";
							$qtagrupacionnoaplica1 = $qtagrupacionnoaplica1 . "AND Encafi LIKE  '%".$wafinidad."%' ";
						}

						if($wentidad !='seleccione')
						{
							$qtagrupacion1 = $qtagrupacion1 . "AND Encdia =  '".$entidades[$wentidad]."' ";
							$qtagrupacionnoaplica1 = $qtagrupacionnoaplica1. "AND Encdia =  '".$entidades[$wentidad]."' ";
						}

						if($wspregunta!='seleccione')
						{
							$qtagrupacion1 = $qtagrupacion1 . " AND Descod  = '".$wspregunta."' ";
							$qtagrupacionnoaplica1 = $qtagrupacionnoaplica1 . " AND Descod  = '".$wspregunta."' ";
						}


						$restagrupacion1 = mysql_query($qtagrupacion1,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qtagrupacion1." - ".mysql_error());
						$rowtagrupacion1 = mysql_fetch_array($restagrupacion1);


						$restagrupacion1noaplica = mysql_query($qtagrupacionnoaplica1,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qtagrupacionnoaplica1." - ".mysql_error());
						$rowtagrupacion1noaplica = mysql_fetch_array($restagrupacion1noaplica);


							$cuantost= $rowtagrupacion['Cuantos'];
							$cuantostnoaplica = $rowtagrupacionnoaplica['Cuantos'];
							@$valor = $rowtagrupacion['Suma']  / $cuantost;

							// $aplicar_formula =true;
							// $simbolo = '%';
							// if($aplicar_formula)
							// {
								// $valor  = (($valor/5) * 100);
							// }


							$resultadoformula 	= array();
							$resultadoformula 	= AnalizarFormula( $clasificacion,$valor);
							$valor				= $resultadoformula['valor'];
							$simbolo			= $resultadoformula['simbolo'];


							//$total = $valor + $total;
							$float_redondeado=round($valor * 100) / 100;
							$float_redondeado1=round($valor1 * 100) / 100;

							$cuantost1= $rowtagrupacion1['Cuantos'];
							$cuantostnoaplica1 = $rowtagrupacion1noaplica['Cuantos'];
							@$valor1 = $rowtagrupacion1['Suma']  / $cuantost1;
							
							$resultadoformula1 	= array();
							$resultadoformula1 	= AnalizarFormula( $clasificacion,$valor1);
							$valor1				= $resultadoformula1['valor'];
							$simbolo			= $resultadoformula1['simbolo'];
							$total1 = $valor1 + $total1;
							
							
							$float_redondeado1=round($valor1 * 100) / 100;
							$color = traecolor ($float_redondeado ,$vectormaximo,$vectorminimo,$vectorcolor) ;
							$color1 = traecolor ($float_redondeado1 ,$vectormaximo,$vectorminimo,$vectorcolor) ;
							$auxind = $auxind + 1;
							if( $unperiodo=='1')
							{

								echo"<td align='center'  style='cursor: pointer;' bgcolor='".$color."' ><div class='msg_tooltip'  title='Respuestas para c&aacute;lculo ".$cuantost." <br> Respuestas totales ".$cuantostnoaplica."'    onclick='abrirporpreguntas(this,\"".$condicion."\",\"todos\",\"".$fechainicial1."\",\"".$fechafinal1."\",\"".$vectornomagrupaciones[$clasificacion]."\",\"".$vectorccocontestado[$j]."\",\"".$float_redondeado."\",\"".$auxind."\")' ><img style='float: right; display: inline-block;' id='img_mas-".$auxind."'' src='../../images/medical/hce/mas.PNG'>".$float_redondeado." ".$simbolo."</div><div id='ddiv-".$auxind."'></div></td>";

																																																													// abrirporpreguntas(this, \"".$condicion."\",\"".$vectorcodccocontestado[$j]."\",\"".$fechainicial1."\",\"".$fechafinal1."\",\"".$vectornomagrupaciones[$clasificacion]."\",\"".$vectorccocontestado[$j]."\",\"".$vectorresporcco[$clasificacion][$vectorcodccocontestado[$j]]."\", \"".$auxind."\")
							}
							else
							{
								$auxind = $auxind + 1;
								echo"<td align='center'  style='cursor: pointer;' bgcolor='".$color."' ><div class='msg_tooltip'  title='Respuestas para c&aacute;lculo ".$cuantost."  <br> Respuestas totales ".$cuantostnoaplica." '  onclick='abrirporpreguntas(this,\"".$condicion."\",\"todos\",\"".$fechainicial1."\",\"".$fechafinal1."\",\"".$vectornomagrupaciones[$clasificacion]."\",\"".$vectorccocontestado[$j]."\",\"".$float_redondeado."\",\"".$auxind."\")'><img style='float: right; display: inline-block;' id='img_mas-".$auxind."'' src='../../images/medical/hce/mas.PNG'>
										".$float_redondeado." ".$simbolo."
									</div><div id='ddiv-".$auxind."'></div></td>";

								$auxind = $auxind + 1;
								echo"<td align='center'  style='cursor: pointer;' bgcolor='".$color1."'><div  class='msg_tooltip'  title='Respuestas para c&aacute;lculo ".$cuantost1." <br> Respuestas Totales ".$cuantostnoaplica1."' onclick='abrirporpreguntas(this,\"".$condicion."\",\"todos\",\"".$fechainicial2."\",\"".$fechafinal2."\",\"".$vectornomagrupaciones[$clasificacion]."\",\"".$vectorccocontestado[$j]."\",\"".$float_redondeado1."\",\"".$auxind."\")'><img style='float: right; display: inline-block;' id='img_mas-".$auxind."'' src='../../images/medical/hce/mas.PNG'>
										".$float_redondeado1." ".$simbolo."
									 </div><div id='ddiv-".$auxind."'></div></td>";
								//echo"<td align='center'  style='cursor: pointer;' bgcolor='".$color."' ><div class='msg_tooltip'  title='Respuestas para c&aacute;lculo ".$cuantost." <br> Respuestas totales ".$cuantostnoaplica."'    onclick='abrirporpreguntas(this,\"".$condicion."\",\"todos\",\"".$fechainicial1."\",\"".$fechafinal1."\",\"".$vectornomagrupaciones[$clasificacion]."\",\"".$vectorccocontestado[$j]."\",\"".$float_redondeado."\",\"".$auxind."\")' ><img style='float: right; display: inline-block;' id='img_mas-".$auxind."'' src='../../images/medical/hce/mas.PNG'>".$float_redondeado."</div><div id='ddiv-".$auxind."'></div></td>";

								$auxind = $auxind + 1;
							}
							$auxind = $auxind + 1;
							$i++;

						//---------------------------------------

						echo"</tr>";
						$m++;
					}
				}
				else if($poragrupacion=='si' )
				{
					while($m < count($vectoragrupaciones))
					{
						//--------nombre de agrupacion
						echo"<tr >";
						echo"<td class='encabezadoTabla' colspan='1' align='Left'  ><div id=td_nombreagrupacion>".$vectornomagrupaciones[$m]."</div></td>";
						//---------------------------

						if($porcco=='si')
						{

							$j=0;
							while($j < count($vectorccocontestado))
							{



								if( $unperiodo=='1')
								{
									echo"<td align='center'>".$vectorresporcco[$vectoragrupaciones[$m]][$vectorcodccocontestado[$j]]."</td>";
								}
								else
								{

									echo"<td class='encabezadoTabla'>".$vectorresporcco[$vectoragrupaciones[$m]][$vectorcodccocontestado[$j]]."</td ><td align='center' class='encabezadoTabla' class='msg_tooltip'  title='Cantidad de Respuestas ".$vectorresporccocuantos[$vectoragrupaciones[$m]][$vectorcodccocontestado[$j]]."' >".$vectorresporcco[$vectoragrupaciones[$m]][$vectorcodccocontestado[$j]]."</td>";


								}

								$j++;
							}

						}
						//-------------total de la agrupacion

						$qtagrupacion = 	" SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos  "
											."  FROM ".$wbasedato."_000005, ".$wbasedato."_000007  "
											." WHERE ".$vectorcondiciones[$n]." "
											."   AND Evades = Descod";
											// ."   AND Desest !='off' ";



						$restagrupacion = mysql_query($qtagrupacion,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qtagrupacion." - ".mysql_error());
						$rowtagrupacion = mysql_fetch_array($restagrupacion);


							$valor = $rowtagrupacion['Suma']  / $rowtagrupacion['Cuantos'];
							$total = $valor + $total;
							$float_redondeado=round($valor * 100) / 100;

							$color = traecolor($float_redondeado ,$vectormaximo,$vectorminimo,$vectorcolor) ;

								if( $unperiodo=='1')
								{
										echo"<td align='center' style='cursor: pointer;' bgcolor='".$color ."' onclick='abrirporpreguntas(this,\"".$vectoragrupaciones[$m]."\",\"todos\",\"".$fechainicial1."\",\"".$fechafinal1."\",\"".$vectornomagrupaciones[$m]."\",\"".$vectorccocontestado[$j]."\",\"".$float_redondeado."\")'>".$float_redondeado."<div id='ddiv-".$auxind."'></div></td>";
								}
								else
								{

									echo"<td align='center'   align='center'  style='cursor: pointer;' bgcolor='".$color ."' onclick='abrirporpreguntas(this,\"".$vectoragrupaciones[$m]."\",\"todos\",\"".$fechainicial1."\",\"".$fechafinal1."\",\"".$vectornomagrupaciones[$m]."\",\"".$vectorccocontestado[$j]."\",\"".$float_redondeado."\")' >".$float_redondeado."</td><td  style='cursor: pointer;' align='center' bgcolor='".$color ."' onclick='abrirporpreguntas(this,\"".$vectoragrupaciones[$m]."\",\"todos\",\"".$fechainicial2."\",\"".$fechafinal2."\",\"".$vectornomagrupaciones[$m]."\",\"".$vectorccocontestado[$j]."\",\"".$float_redondeado."\")'>".$float_redondeado."<div id='ddiv-".$auxind."'></div></td>";

								}

							$i++;

						echo"</tr>";
						$m++;
					}
				}
				echo"</table>";

				break;
				/////////------------------------------------
				//nuevo
               default :
                       break;
       }
	   //echo"</table>";
	   if( $unperiodo=='1')
	   {
		   echo "<br>";
		   echo "<br>";
		   echo "<br>";
		   echo "<table>";
		   /*echo "<tr><td><input type='button' value='Graficar' onclick='pintarGrafica()'>q1-".$q1."
																							q2-".$q2."
																							q-".$q."
																							querycco-".$querycco."
																							querys_aplican-".$querys_aplican."
																							qccoxagrupacion-".$qccoxagrupacion."
																							xx-".$xx."
																							qccoxagrupacionconnoaplica-".$qccoxagrupacionconnoaplica."
																							qtagrupacion-".$qtagrupacion."
																							qtagrupacionnoaplica-".$qtagrupacionnoaplica."
																							qtagrupacion1-".$qtagrupacion1."
																							qtagrupacionnoaplica1-".$qtagrupacionnoaplica1."
																							qtagrupacion-".$qtagrupacion.";</td></tr>";*/
		   echo "<tr><td><input type='button' value='Graficar' onclick='pintarGrafica()'>";
		   echo "</table>";
		   echo "<br>";
		   echo "<br>";
		   echo "<table>";
		   echo "<tr><td><div id='amchart1' style='width:1500px; height:600px;'></div></td></tr>";
		   echo "</table>";
	   }



	   return;
}





?>
<html>

<head>
<title>Reportes por Agrupacion</title>
<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>
<link type="text/css" href="../../../include/root/jquery.ui.timepicker.css" rel="stylesheet"/>
<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>
<script src="../../../include/root/LeerTablaAmericas.js" type="text/javascript"></script>
<script src="../../../include/root/amcharts/amcharts.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.css" />
<link rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.filter.css" />
<script type="text/javascript" src="../../../include/root/jquery.multiselect.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.multiselect.filter.js"></script>
<script type='text/javascript'>

$(document).ready(function(){

      $('#select_entidad').multiselect({
              numberDisplayed: 1,
              selectedList:1,
              multiple:false
      }).multiselectfilter();

}); // Finalizar Ready()

var fechainicialeps;
var fechafinaleps;
var fechainicialeps1;
var fechafinaleps1;
var gtipoformato;


function traesemaforo(){


$.post("../reportes/reportes_por_agrupacion.php",
	{
		consultaAjax 	: '',
		inicial			: 'no',
		operacion		: 'traersemaforo',
		wemp_pmla		: $('#wemp_pmla').val(),
		wtemareportes	: $('#select_tema').val(),
		wtema           : $('#wtema').val()

	}
	, function(data) {

		$("#semaforogeneral").val(data);

	});

}

function cambiarfechaseps(){

	if(fechainicialeps != $('#wfecha_i').val() || fechafinaleps != $('#wfecha_f').val())
	{
		if($('#periodo2:checked').val()=='si' )
		{
			fechainicialeps = $('#wfecha_i').val().replace(/-/gi,"");
			fechafinaleps = $('#wfecha_f').val().replace(/-/gi,"");
			fechainicialeps1 = $('#wfecha_i1').val().replace(/-/gi,"");
			fechafinaleps1 = $('#wfecha_f1').val().replace(/-/gi,"");

			if (fechainicialeps1 < fechainicialeps)
				fechainicialeps = $('#wfecha_i1').val();
			else
				fechainicialeps = $('#wfecha_i').val();

			if(fechafinaleps1 > fechafinaleps )
				fechafinaleps = $('#wfecha_f1').val();
			else
				fechafinaleps = $('#wfecha_f').val();

		}
		else
		{
			fechainicialeps = $('#wfecha_i').val();
			fechafinaleps = $('#wfecha_f').val();

		}

        // Se desactiva consulta
/*			$.get("../reportes/reportes_por_agrupacion.php",
				{
					consultaAjax 	: '',
					inicial2			: 'no',
					operacion		: 'cambiaresultadoseps',
					wfechainicialeps : fechainicialeps,
					wfechafinaleps	 : fechafinaleps,
					wtema           : $('#wtema').val(),
					wuse			: $('#wuse').val(),
					wemp_pmla		: $('#wemp_pmla').val()

				}
				, function(data) {


				$('#select_entidad').html(data);
				});*/
	}

}

function abrirtodos()
{
	// se tiene que verificar que hay abierto primero .
	// si estan todas abiertas debe cambiar el boton por cerrar .
	// si almenos falta una por abrir se deben abrir todas

	$(".clasventan3").each(function(){

		if($('#tabletresx-'+$(this).attr('agrupacion')+'-'+$(this).attr('centrocostos')).length > 0)
		{


		}
		else
		{
			//calert("agrupacioncentrocostosfechainicialfechafinal");
			abrir3ventana($(this).attr('agrupacion'),$(this).attr('centrocostos'),$(this).attr('fechaini'),$(this).attr('fechafin'),'si');
		}
	});

}

// abre el segundo nivel del informe principal.
function abrir3ventana(wagrupacion,centrocostos,fechainicial,fechafinal,abrirtodos='no')
{
	if ($('#selectperiodos').val()==undefined)
	{
		 ano = 'nada';
		 periodo = 'nada';
	}
	else
	{
		var wanoperiodo = $('#selectperiodos').val().split("||");
		ano = wanoperiodo[0];
		periodo = wanoperiodo[1];
	}

	//cambia imagen de + a -
	if($("#img_mas-"+wagrupacion+'-'+centrocostos).attr('src') == '../../images/medical/hce/mas.PNG')
	{
		$("#img_mas-"+wagrupacion+'-'+centrocostos).attr('src', '../../images/medical/hce/menos.PNG');
	}else
	{
		$("#img_mas-"+wagrupacion+'-'+centrocostos).attr('src', '../../images/medical/hce/mas.PNG');
	}
	//-----------

	if($('#tabletresx-'+wagrupacion+'-'+centrocostos).length > 0)
	{
		if (abrirtodos=='no')
		{
			$('#tabletresx-'+wagrupacion+'-'+centrocostos).remove();
			$('#tercerventana-'+wagrupacion+'-'+centrocostos).css("background-color","");
		}
	}
	else
	{

		var entidad  = $('#select_entidad > option:selected').val();
		var afinidad = $('#select_afinidad').val();

		if($('#select_afinidad').length == 0)
				afinidad = 'seleccione';

		if($('#select_entidad').val() == '' || $('#select_entidad').val() == null)
				entidad = 'seleccione';

		var 	usuario_consultado ='';
		if ($('#buscador_usuario').length)
			usuario_consultado = $('#buscador_usuario').attr('valor');

		//-------------
		var tempresa;
		if($("#tempresa_todos:checked").val())
		{
			tempresa = '';

		}
		if($("#tempresa_particular:checked").val())
		{
			tempresa = 'particular';

		}
		if($("#tempresa_pap:checked").val())
		{
			tempresa = 'PAP';

		}

		if($("#tempresa_pos:checked").val())
		{
			tempresa = 'pos';

		}

		$.post("../reportes/reportes_por_agrupacion.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'traeresultadov3',
				wtema           : $('#wtema').val(),
				wuse			: $('#wuse').val(),
				wemp_pmla		: $('#wemp_pmla').val(),
				wperiodo		: periodo,
				wano			: ano,
				wcagrupacion	: wagrupacion,
				wcentrocostos	: centrocostos,
				wtemareportes	: $('#select_tema').val(),
				wtipoformato	: gtipoformato,
				wfechainicial	: fechainicial,
				wfechafinal		: fechafinal,
				fechainicial1	: fechafinal,
				wentidad 		: entidad,
				wafinidad		: afinidad,
				wusuario_consultado: usuario_consultado,
				t_semaforo		: $("#semaforogeneral").val(),
				wtipodeempresa	: tempresa

			}
			, function(data) {

					$('#tercerventana-'+wagrupacion+'-'+centrocostos).html(data);
					$('#tercerventana-'+wagrupacion+'-'+centrocostos).css("background-color","white");
					$('#tabletres-'+wagrupacion+'-'+centrocostos).css("bgcolor","white");
					$(".msg_tooltipx-"+wagrupacion+'-'+centrocostos).tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
					$(".tablasresize").css( "width", "100%" );
			});

	}

}

function mostrarSoloTd( obj ){
	obj = jQuery(obj);
	obj = obj.parent();
	//console.log( obj.html() );
	var indice_fila = obj.parent().index();//Numero de tr en la tabla
	var indice_columna = obj.index();//Numero de td en el tr
	//console.log("indice_fila->"+indice_fila+" indice_columna->"+indice_columna );

	obj.parent().addClass('noocultar'); //Le agrega la clase noocultar al tr
	obj.parent().parent().find('tr.ocultar').not('.noocultar').hide(); //Oculto todos los tr con la clase ocultar que no tengan la clase noocultar

	obj.addClass('noocultar'); //Le agrega la clase noocultar al td
	obj.parent().find('td').eq(0).addClass('noocultar'); //Le agrega la clase noocultar al td(0) asi se muestran los titulos
	obj.parent().find('td').not('.noocultar').hide(); //Oculto todos los td que no tengan la clase noocultar


	if( $(".pisos").find('td').eq(0).attr("colspan") != undefined ){
		var colspan_pisos = $(".pisos").find('td').eq(0).attr("colspan");
		indice_columna = Math.ceil(indice_columna/colspan_pisos);
	}

	//console.log("indice cambiado: "+indice_columna );
	$(".pisos").find('td').eq( indice_columna-1 ).addClass('noocultar'); //Le agrega la clase noocultar al td
	$(".pisos").find('td').not('.noocultar').hide(); //Oculto todos los td que no tengan la clase noocultar


    obj.parent().parent().find('td.Periodo').hide();

}

// abre al primer nivel el informe.

function abrirporpreguntas(ele, agrupacion, centrocco , fechainicial, fechafinal,nombreagrupacion,nombrecco,puntaje,indicador,tipoformato )
{

		gtipoformato=tipoformato;
		if ($('#selectperiodos').val()==undefined)
		{
			ano = 'nada';
			periodo = 'nada';
		}
		else
		{
			var wanoperiodo = $('#selectperiodos').val().split("||");
			ano = wanoperiodo[0];
			periodo = wanoperiodo[1];
		}


	if(puntaje=='0')
	{
	}
	else
	{
	if( $('#tablesegundax-'+indicador).length > 0)
	{
		$('#tablesegundax-'+indicador).remove();
		$('#ddiv-'+indicador).css("background-color","");

		$(".noocultar").removeClass('noocultar');
		$("#tablareporte tr, #tablareporte td").show();

		if($("#img_mas-"+indicador).attr('src') == '../../images/medical/hce/mas.PNG')
		{
			$("#img_mas-"+indicador).attr('src', '../../images/medical/hce/menos.PNG');
		}else
		{
			$("#img_mas-"+indicador).attr('src', '../../images/medical/hce/mas.PNG');
		}
	}
	else
	{

		//if( ele != null ){
			mostrarSoloTd( ele );
		//}


		var afinidad = $('#select_afinidad').val();
		var entidad  = $('#select_entidad > option:selected').val();

		if($('#select_afinidad').length == 0)
			afinidad = 'seleccione';

		if($('#select_entidad').val() == '' || $('#select_entidad').val() == null)
			entidad = 'seleccione';

		var	usuario_consultado = '';

		if ($('#buscador_usuario').length)
			usuario_consultado = $('#buscador_usuario').attr('valor');


		if($("#img_mas-"+indicador).attr('src') == '../../images/medical/hce/mas.PNG')
		{
			$("#img_mas-"+indicador).attr('src', '../../images/medical/hce/menos.PNG');
		}else
		{
			$("#img_mas-"+indicador).attr('src', '../../images/medical/hce/mas.PNG');
		}

		//-------------
		tempresa = '';
		if($("#tempresa_todos:checked").val())
		{
			tempresa = '';

		}
		if($("#tempresa_particular:checked").val())
		{
			tempresa = 'particular';

		}
		if($("#tempresa_pap:checked").val())
		{
			tempresa = 'PAP';

		}

		if($("#tempresa_pos:checked").val())
		{
			tempresa = 'pos';

		}

		$.post("../reportes/reportes_por_agrupacion.php",
				{
					consultaAjax 	: '',
					inicial			: 'no',
					operacion		: 'traeResultadosPorPregunta',
					wemp_pmla		: $('#wemp_pmla').val(),
					wtema           : $('#wtema').val(),
					wuse			: $('#wuse').val(),
					wtemareportes	: $('#select_tema').val(),
					wagrupacion		: agrupacion,
					wcentrocostos	: centrocco,
					wfechainicial	: fechainicial,
					wfechafinal		: fechafinal,
					wnombreagr		: nombreagrupacion,
					wnombrecco		: nombrecco,
					wseleccco		: $('#buscador_cco').attr('valor'),
					wentidad 		: entidad,
					wafinidad		: afinidad,
					wspregunta 		: $('#select_pregunta').val(),
					wperiodo		:periodo,
					wano			:ano,
					windicador		:indicador,
					wusuario_consultado : usuario_consultado,
					t_semaforo		: $("#semaforogeneral").val(),
					wtipodeempresa	: tempresa


				}
				, function(data) {
				$('#ddiv-'+indicador).html(data);
				$('#ddiv-'+indicador).css("background-color","white");
				$(".msg_tooltip1"+indicador).tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
				//var div ='div_reporte';
				//fnMostrar2(div);
				});

		}
	}
}

//************************************************************************************************************************

function pintarGrafica ()
{

$("[id^=tablesegundax-]").remove();
$("[id^=ddiv-]").css("background-color","");
$('#tablareporte tr, #tablareporte td').show();
$(".noocultar").removeClass('noocultar');
//
$('#tablareporte').LeerTablaAmericas({

			empezardesdefila: 2,
			titulo : 'Notas Promedio por Centro de Costos ' ,
			tituloy: 'cantidad',
			filaencabezado : [1,0],
			datosadicionales : 'todo'

		});


}



function pintarGrafica2 (ind)
{
$("[id^=tabletresx]").remove();
$("[id^=tercerventana]").css("background-color","");
$("#areasegunda-"+ind).css("width","400px");
$("#areasegunda-"+ind).css("height","300px");
$('#tablesegunda-'+ind).LeerTablaAmericas({

			empezardesdefila: 1,
			titulo : 'Notas Promedio por Agrupacion ' ,
			tituloy: 'cantidad',
			filaencabezado : [0,1],
			datosadicionales : 'todo',
			divgrafica: "areasegunda-"+ind

		});
}

function pintarGrafica3 (agrupacion, centrodecostos)
{
$("[id^=tablex]").remove();
$("[id^=cuartaventana]").css("background-color","");
$("#areatercera-"+agrupacion+"-"+centrodecostos).css("width","500px");
$("#areatercera-"+agrupacion+"-"+centrodecostos).css("height","500px");
$('#tabletres-'+agrupacion+'-'+centrodecostos).LeerTablaAmericas({

			empezardesdefila: 1,
			titulo : ' ' ,
			tituloy: ' ',
			filaencabezado : [0,1],
			datosadicionales : 'todo',
			divgrafica: "areatercera-"+agrupacion+"-"+centrodecostos,
			columnadatos: 1

		});


}


function pintarGrafica4 (agrupacion, centrodecostos,cpregunta)
{
$("#areacuatro-"+cpregunta+"-"+agrupacion+"-"+centrodecostos).css("width","400px");
$("#areacuatro-"+cpregunta+"-"+agrupacion+"-"+centrodecostos).css("height","300px");

$('#table-'+cpregunta+'-'+agrupacion+'-'+centrodecostos).LeerTablaAmericas({

			empezardesdefila: 1,
			titulo : ' ' ,
			tituloy: ' ',
			filaencabezado : [0,1],
			datosadicionales : 'todo',
			divgrafica: "areacuatro-"+cpregunta+"-"+agrupacion+"-"+centrodecostos,
			columnadatos: 1

		});


}

//********************************************************************************************************************************

//------------------------------
// Funcion que pinta las agrupaciones segun el tema interno que fue escogido.
function verAgrupaciones()
{
	traesemaforo();
	$('#div_contenido_reporte').html('');
	if($('#select_tema').val()=='seleccione')
	{
		$('#div_resultados').html('');
	}
	else
	{


		$.get("../reportes/reportes_por_agrupacion.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'mostrarAgrupaciones',
				wemp_pmla		: $('#wemp_pmla').val(),
				wtema           : $('#wtema').val(),
				wuse			: $('#wuse').val(),
				wtemareportes	: $('#select_tema').val()

			}
			, function(data) {
				$('#div_resultados').html(data);
				traer_datos_buscador_cco();
				traer_datos_buscador_usuario();

				$('#select_entidad').multiselect({
		              numberDisplayed: 1,
		              selectedList:1,
		              multiple:false
		        }).multiselectfilter();
			});
	}

}


function traer_datos_buscador_cco()
{

  $.post("../reportes/reportes_por_agrupacion.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'inicioprogramacco',
				wemp_pmla		: $('#wemp_pmla').val(),
				wtema           : $('#wtema').val(),
				wuse			: $('#wuse').val()
			}
			, function(data) {

				cargar_cco (eval('(' + data + ')'));
			});
}


function traer_datos_buscador_usuario()
{

  $.post("../reportes/reportes_por_agrupacion.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'inicioprogramausuario',
				wemp_pmla		: $('#wemp_pmla').val(),
				wtema           : $('#wtema').val(),
				wuse			: $('#wuse').val()
			}
			, function(data) {
				//alert(data);
				cargar_usuario (eval('(' + data + ')'));
			});
}


function CargarSelectPreguntas()
{
	var agrupacion;
	agrupacion = $('#select_agrupacion').val().split('||');


	$.get("../reportes/reportes_por_agrupacion.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'CargarSelectPreguntas',
				wemp_pmla		: $('#wemp_pmla').val(),
				wtema           : $('#wtema').val(),
				wuse			: $('#wuse').val(),
				wtemareportes	: $('#select_tema').val(),
				wagrupacion		: agrupacion[1]

			}
			, function(data) {
			$('#div_select_pregunta').html(data);

			});

}

function CargarSelectAgrupacion()
{
	var clasificacionagrupacion;
	clasificacionagrupacion = $('#select_clasificacionagrupacion').val();


	$.get("../reportes/reportes_por_agrupacion.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'CargarSelectAgrupacion',
				wemp_pmla		: $('#wemp_pmla').val(),
				wtema           : $('#wtema').val(),
				wuse			: $('#wuse').val(),
				wtemareportes	: $('#select_tema').val(),
				wclasificacionagrupacion	: clasificacionagrupacion

			}
			, function(data) {
			$('#div_select_agrupacion').html(data);

			});
			
	cambiar_semaforo();

}

function cambiar_semaforo()
{
	var clasificacionagrupacion;
	clasificacionagrupacion = $('#select_clasificacionagrupacion').val();
	$.get("../reportes/reportes_por_agrupacion.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'cambiar_semaforo',
				wemp_pmla		: $('#wemp_pmla').val(),
				wtema           : $('#wtema').val(),
				wuse			: $('#wuse').val(),
				wtemareportes	: $('#select_tema').val(),
				wclasificacionagrupacion	: clasificacionagrupacion

			}
			, function(data) {
			 $("#semaforogeneral").val(data);

			});
			 
}



function borrarpreguntas(){
 $('#div_contenido_porpreguntas').html('');
}

function fnMostrar2( celda )
{

	if( $('#'+celda ) )
	{
		$.blockUI({ message: $('#'+celda ),
						css: { left: ( $(window).width() - 1000 )/2 +'px',
							  top: '100px',
							  width: '1000px',
							  height: 'auto',
							  overflow:	'scroll'
							 }
				  });

	}

}

// Mensaje de espera
function esperar (  )
{
$.blockUI({ message:  '<img src="../../images/medical/ajax-loader.gif" >',
            css: {
				   width:         'auto',
				   height: 'auto'
                 }
          });
}

//- abre el informe principal en su tercer nivel.
function traeDetallePregunta(pregunta,agrupacion,centrocco,fechainicial,fechafinal,elemento,tipoformato,numeropreguntas)
{

	if( $("#"+elemento).next('tr').hasClass('tumama'))
	{

		if( $("#"+elemento).next('tr').css('display') == 'none')
		{
			$("#"+elemento).next('tr').slideDown('slow');
		}
		else
		{
			$("#"+elemento).next('tr').hide('slow');
		}
		return;
	}

	if(fechainicial=='' &&  fechafinal =='')
	{
		if ($('#selectperiodos').val()==undefined)
		{
		 ano = 'nada';
		 periodo = 'nada';
		}
		else
		{
			var wanoperiodo = $('#selectperiodos').val().split("||");
			ano = wanoperiodo[0];
			periodo = wanoperiodo[1];
		}
	}
	//cambia imagen de + a -
	if($("#img_mas-"+agrupacion+"-"+centrocco+"-"+pregunta).attr('src') == '../../images/medical/hce/mas.PNG')
	{
		$("#img_mas-"+agrupacion+"-"+centrocco+"-"+pregunta).attr('src', '../../images/medical/hce/menos.PNG');
	}else
	{
		$("#img_mas-"+agrupacion+"-"+centrocco+"-"+pregunta).attr('src', '../../images/medical/hce/mas.PNG');
	}
	//-----------

	if( $("#tablex-"+pregunta+"-"+agrupacion+"-"+centrocco).length > 0)
	{
		$("#tablex-"+pregunta+"-"+agrupacion+"-"+centrocco).remove();
		$("#cuartaventana-"+agrupacion+"-"+centrocco+"-"+pregunta).css("background-color","");

	}
	else
	{
		var entidad  = $('#select_entidad > option:selected').val();
		var afinidad = $('#select_afinidad').val();

		if($('#select_afinidad').length == 0)
				afinidad = 'seleccione';

		if($('#select_entidad').val() == '' || $('#select_entidad').val() == null)
				entidad = 'seleccione';

		//-------------
		var tempresa;
		if($("#tempresa_todos:checked").val())
		{
			tempresa = '';
		}
		if($("#tempresa_particular:checked").val())
		{
			tempresa = 'particular';

		}
		if($("#tempresa_pap:checked").val())
		{
			tempresa = 'PAP';

		}

		if($("#tempresa_pos:checked").val())
		{
			tempresa = 'pos';
		}

		var	usuario_consultado = '';

		if ($('#buscador_usuario').length)
			usuario_consultado = $('#buscador_usuario').attr('valor');


		$.post("../reportes/reportes_por_agrupacion.php",
		{
			consultaAjax 	: '',
			inicial			: 'no',
			operacion		: 'traeDetallePregunta',
			wemp_pmla		: $('#wemp_pmla').val(),
			wtema           : $('#wtema').val(),
			wuse			: $('#wuse').val(),
			wtemareportes	: $('#select_tema').val(),
			wagrupacion		: agrupacion,
			wcentrocostos	: centrocco,
			wfechainicial	: fechainicial,
			wfechafinal		: fechafinal,
			wseleccco		: $('#buscador_cco').attr('valor'),
			wcodpregunta	: pregunta,
			wtipoformato	: gtipoformato,
			wentidad 		: entidad,
			wafinidad		: afinidad,
			wnumeropreguntas : numeropreguntas,
			wperiodo		: periodo,
			wano			: ano,
			wusuario_consultado : usuario_consultado,
			t_semaforo		: $("#semaforogeneral").val(),
			wtipodeempresa	: tempresa


		}
		, function(data) {
		//$('#detallepreguntas').html(data);


			$("#cuartaventana-"+agrupacion+"-"+centrocco+"-"+pregunta).css("background-color","white");
			$("#cuartaventana-"+agrupacion+"-"+centrocco+"-"+pregunta).html(data);
			// $("#"+elemento).after("<tr class='tumama fila1'><td colspan=2>"+data+"</td></tr>");
			// $(".msg_tooltip3").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
		});

	}

}

function verReporte()
{

	var agrupacion;
	var centrocostos;
	var codcco;
	var nombrecco;
	var hospitalario;
	var urgencias;
	var terapeutico;
	var cirugia;
	var clasificacionagrupacion;
	var ayudas;
	var ano;
	var periodo;
	var tempresa;

	if ($('#selectperiodos').val()==undefined)
	{
	    ano = 'nada';
	    periodo = 'nada';
	}
	else
	{
		var wanoperiodo = $('#selectperiodos').val().split("||");
		ano = wanoperiodo[0];
		periodo = wanoperiodo[1];
	}

	agrupacion   = $('#select_agrupacion').val();
	centrocostos = $('#buscador_cco').attr('valor');
	afinidad     = $('#select_afinidad').val();
	entidad      = $('#select_entidad > option:selected').val();
	pregunta     = $('#select_pregunta').val();
	clasificacionagrupacion = $('#select_clasificacionagrupacion').val();
	var tipocco = $('#tipocco').val();

	if( $('#servicio_hospitalario:checked').val()=='si')
	{
		hospitalario = $('#servicio_hospitalario').val();
	}

	if( $('#servicio_urgencias:checked').val()=='si')
	{
		urgencias = $('#servicio_urgencias').val();
	}

	if( $('#servicio_terapeuticos:checked').val()=='si')
	{
		terapeutico = $('#servicio_terapeuticos').val();
	}

	if( $('#servicio_cirugia:checked').val()=='si')
	{
		cirugia = $('#servicio_cirugia').val();
	}

	if( $('#servicio_ayudas:checked').val()=='si')
	{
		ayudas = $('#servicio_ayudas').val();
	}

	//-------------
	if($("#tempresa_todos:checked").val())
	{
		tempresa = '';

	}
	if($("#tempresa_particular:checked").val())
	{
		tempresa = 'particular';

	}
	if($("#tempresa_pap:checked").val())
	{
		tempresa = 'PAP';

	}

	if($("#tempresa_pos:checked").val())
	{
		tempresa = 'pos';

	}


	if(centrocostos=='seleccione' || agrupacion=='seleccione')
	{
		var mensaje = unescape('Debe seleccionar un centro de costos o una agrupaci�n');
		alert(mensaje);
		return;
	}

	if(centrocostos=='*')
	{
		centrocostos='todos';

	}
	else
	{
		centrocostos=centrocostos.split('||');

		nombrecco=$("#buscador_cco").val();
		codcco=$("#buscador_cco").attr('valor');
	}

	var fechainicial2;
	var fechafinal2;
	var porcco;
	var porpregunta;
	var poragrupacion ='si';

	var splitagrupacion = agrupacion.split('||');

	var codigoagrupacion = splitagrupacion[1];
	var nombreagrupacion = splitagrupacion[0];



	if($('#periodo2:checked').val()=='si' )
	{


		fechainicial2=$('#wfecha_i1').val();
		fechafinal2=$('#wfecha_f1').val();
		unperiodo=2;

	}
	else
	{
		unperiodo=1;
	}
	//selectperiodo
	if($('#wfecha_i').val()==undefined)
	{
		if($('#selectperiodos').val()=='nada||nada')
		{
			alert ("debe seleccionar una periodo");
			return;
		}

	}

	if($('#select_afinidad').length == 0)
		afinidad = 'seleccione';

	if ( ($('#select_entidad').val() == '') || ($('#select_entidad').val() == null) )
		entidad = 'seleccione';

	var	usuario_consultado = '';

	if ($('#buscador_usuario').length)
		var	usuario_consultado = $('#buscador_usuario').attr('valor');
    

	esperar();
	$.post("../reportes/reportes_por_agrupacion.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'traeResultadosReporte',
				wemp_pmla		: $('#wemp_pmla').val(),
				wtema           : $('#wtema').val(),
				wuse			: $('#wuse').val(),
				wtemareportes	: $('#select_tema').val(),
				wcagrupacion	: codigoagrupacion,
				wnagrupacion	: nombreagrupacion,
				porcco			: porcco,
				porpregunta		: porpregunta,
				poragrupacion	: poragrupacion,
				fechainicial1 	:$('#wfecha_i').val(),
				fechafinal1		:$('#wfecha_f').val(),
				fechafinal2		: fechafinal2,
				fechainicial2	: fechainicial2,
				unperiodo		: unperiodo,
				wcco			: centrocostos,
				wcodcco			: codcco,
				wnomcco			: nombrecco,
				wafinidad		: afinidad,
				wentidad		: entidad,
				whospitalario	: hospitalario,
				wurgencias 		: urgencias,
				wterapeutico 	: terapeutico,
				wayudas			: ayudas,
				wcirugia 		: cirugia,
				wspregunta		: pregunta,
				wclasificacionagrupacion : clasificacionagrupacion,
				tipocco 		: tipocco ,
				wperiodo1		: periodo,
				wano1			: ano,
				wusuario_consultado : usuario_consultado,
				t_semaforo		: $("#semaforogeneral").val(),
				wtipodeempresa	: tempresa


			}
			, function(data) {
				$('#div_contenido_reporte').html(data);
				$(".msg_tooltip").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
				$.unblockUI();
			});

}






function cargar_cco (ArrayValores)
{


	var ccos	= new Array();
	var index		= -1;
	var tr ;
	var n = 1;
	tr ="";
	var wfc ;
	for (var cod_ccos in ArrayValores)
	{
		index++;
		ccos[index] = {};
		ccos[index].value  = cod_ccos+'-'+ArrayValores[cod_ccos];
		ccos[index].label  = cod_ccos+'-'+ArrayValores[cod_ccos];
		ccos[index].valor  = cod_ccos;
		ccos[index].nombre = ArrayValores[cod_ccos];
	}

	$( "#buscador_cco" ).autocomplete({

		minLength: 	0,
		source: 	ccos,
		select: 	function( event, ui ){
			$( "#buscador_cco" ).val(ui.item.label);
			$( "#buscador_cco" ).attr('valor', ui.item.valor);
			return false;
		}



	});

	$("#buscador_cco").val("Todos");
	$("#buscador_cco").attr("valor","*");


}
function verocultacco(cco)
{
	//alert(cco);
	$("#oculta_"+cco).toggle();

}
function cargar_usuario (ArrayValores)
{


	var usuarios	= new Array();
	var index		= -1;
	var tr ;
	var n = 1;
	tr ="";
	var wfc ;
	var aux='';
	for (var cod_usuarios in ArrayValores)
	{
		index++;
		usuarios[index] = {};
		usuarios[index].value  = cod_usuarios+'-'+ArrayValores[cod_usuarios];
		aux =  cod_usuarios.split('-');
		aux = aux[0];
		usuarios[index].label  = aux+' '+ArrayValores[cod_usuarios];
		usuarios[index].valor  = cod_usuarios;
		usuarios[index].nombre = ArrayValores[cod_usuarios];
	}

	$( "#buscador_usuario" ).autocomplete({

		minLength: 	0,
		source: 	usuarios,
		select: 	function( event, ui ){
			$( "#buscador_usuario" ).val(ui.item.label);
			$( "#buscador_usuario" ).attr('valor', ui.item.valor);
			return false;
		}



	});

	$("#buscador_cco").val("Todos");
	$("#buscador_cco").attr("valor","*");


}

</script>

<style type="text/css">
	.ui-autocomplete{
		max-width: 	300px;
		max-height: 150px;
		overflow-y: auto;
		overflow-x: hidden;
		font-size: 	9pt;
	}
    .displ{
        display:block;
    }
    .borderDiv {
        border: 2px solid #2A5DB0;
        padding: 5px;
    }
    .resalto{
        font-weight:bold;
    }
    .parrafo1{
        color: #676767;
        font-family: verdana;
    }
    .ui-multiselect { background:white; color: black; font-weight: normal; border-color: black; border: 2px; height:20px; width:450px; overflow-x:hidden; text-align:left;font-size: 10pt;border-radius: 1px;} 
	#tooltip
	{color: #2A5DB0;font-family: Arial,Helvetica,sans-serif;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:5px;opacity:1;}
	#tooltip h3, #tooltip div{margin:0; width:auto}
</style>

</head>
<body >

<?php

// NOMBRE: reporte_por_agrupacion
// AUTOR:  Felipe Alvarez Sanchez
// FECHA CREACION :  2012-08-23
// Programa principal para crear reportes desde los datos tomados de encuentas y evaluaciones en cada uno de los temas
// temas : (talhuma, magenta, invecla ... etc)
// El reporte grafica por cada tema interno , agrupacion los datos  de las evaluaciones y encuestas en los periodos seleccionados
// la escala de valoracion depende de cada uno de estos temas y del tipo de escala que tengan las preguntas asociadas en  el informe
// que se quiera ver
// Actualizaciones
// 2013-02-02 Felipe Alvarez
// Se a�aden funcionalidades para poder ver resultados de encuestas relacionadas con encuestas magenta
// --------------------------------------------------------------------------------------------------------
// 2014-05-23 Felipe Alvarez
// Se a�ade funcionalidad para graficar los resultados de los informes de encuestas
// ---------------------------------------------------------------------------------------------------------
// 2015-11-11 Felipe Alvarez
// Se a�aden funcionalidades para poder ver los resultados del tema invecla , pues se tiene que interpretar formula y
// calcular valores de acuerdo a esta.
// 2016-05-19 Felipe Alvarez.
// se adiciona se adiciona en las funcionalidades seleccionar el tema y que el semaforo cambie  para  que  se escoja el semaforo deacuerdo al tema
// 2017-11-08 Arleyda Insignares C.
// Se cambia campo Entidad por un multiselect que permita buscar por el nombre de la Entidad.
// 2018-06-29 Juan Felipe Balcero L.
// Se modifica la consulta para que busque las entidades de acuerdo a su código y no a su nombre, ya que un cambio en el nombre del maestro estaba causando 
// pérdida de información



$wactualiz = "2018-06-29";

echo '<input type="hidden" id="wemp_pmla" name="wemp_pmla" value="'.$wemp_pmla.'" />';

/****************************************************************************************************************
*  FUNCIONES PHP >>
****************************************************************************************************************/

//------- campos ocultos

echo "<input type='hidden' name='wtema' id='wtema' value='".$wtema."'>";
echo "<input type='hidden' name='wuse' id='wuse' value='".$wuse."'>";
echo "<input type='hidden' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";
echo "<input type='hidden' name='wemp_pmla' id='tipocco' value='".$tipocco."'>";
echo "<input type='hidden'  id='semaforogeneral' value=''>";

//----------------------------

//tabla principal
echo"<table id='tabla_principal' width='900' align='center'>";
//primer tr :  Select tema.
echo"<tr><td>";

//----------------------------------
/*
   Los tipos de evaluaciones que se pueden sacar por medio de este reporte son los siguientes
   01- Evaluacion Interna.
   02- Encuesta Anonima.
   03- Encuesta Usuario Registrado
   04- Evaluacion de Conocimientos
   05- Evaluacion de invecla

*/
//----------------------------------


// Se traen los temas de cada uno de las dependencias en que estan instaladas este programa de encuestas y evaluaciones
// la tabla _000042 contiene los diferentes temas

$q = "	SELECT  Forcod, Fordes "
	."	  FROM  ".$wbasedato."_000042 "
	."   WHERE Fortip='01' "
	."      OR Fortip='03' "
	."      OR Fortip='04'
			OR Fortip='05'
	  ORDER BY Fordes ";

$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

echo"<div id='div_tema' align='center'>";
echo"<table width='500' align='center'  border='0' cellspacing='0' cellpadding='0'>";
echo"<tr>";
echo"<td width='200' class='encabezadoTabla' align='Center' >Seleccione Tema</td>";
echo"<td  width='300' class='encabezadoTabla' align='Left'>";
echo"<select id='select_tema' onchange='verAgrupaciones()'>";

echo"<option value='seleccione' selected >Seleccione</option>";

while($row = mysql_fetch_array($res))
{
	echo"<option value='".$row['Forcod']."'>".$row['Fordes']."</option>";
}
echo"</select>";
echo"</td>";
echo"</tr>";
echo"</table>";
echo"</div>";
echo"</td></tr>";
echo"</table>";
echo"<br><br>";
echo"<div id='div_resultados' >";
echo"</div>";

//-----div Muestra de resultados-------------------------

echo "<table align='center'>";
echo "<tr>";
echo "<td>";
echo "<div id='div_contenido_reporte'></div>";
echo "<td>";
echo "</tr>";
echo "</table>";

//-------------------------------------------------------------
echo "<div id='div_reporte' class='fila2' align='middle'  style='display:none;cursor:default;background:none repeat scroll 0 0;height: 400px' >";
echo "<input id='nombreAgrupacionAgregar' type='hidden'>";
echo "<br><br>";

// div para mostrar las preguntas
echo "<div id='div_contenido_porpreguntas'>";

echo "</div>";
// div de detalle de preguntas
echo "<div id='detallepreguntas'>";
echo "</div>";

echo"<br><br>";
echo"<input type='button' value='Cancelar'  onClick='$.unblockUI(); borrarpreguntas();' style='width:100'>";
echo"</div>";

echo "<div id='div_esperar' class='fila2' align='middle'  style='display:none;cursor:default;background:none repeat scroll 0 0;height: 400px' >";
echo "</div>";
//------------------------------------------------------
//------------------------------------------------------
function traecolor ($puntaje ,$vectormaximo,$vectorminimo,$vectorcolor)
{
 $i=0;
 $color = '#F2F2F2';
 while($i<count($vectormaximo))
 {
	if($puntaje>$vectorminimo[$i] AND $puntaje<=$vectormaximo[$i])
	$color = $vectorcolor[$i];
	$i++;
 }
 return $color;
}

?>

</body>
</html>