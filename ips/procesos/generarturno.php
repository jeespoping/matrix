<?php

	//$user_session[1];
include_once("conex.php");
include("root/comun.php");
ob_end_clean();

$wfecha = date("Y-m-d");
$whora = date("H:i:s");
$wuse	= 'Turnero';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{

/*
tipoDocumento: CC,numeroIdentificacion: 98576923,nombrePaciente: LUIS MORA,edad: 50,tipoEdad: A,categoria: 01,tema: 01,prioridad: ,tipoTurnero: 01,codigoTurnero: ,validarExisteTurno: false,wemp_pmla: 01
*/
	txtLog("", true);
	$tipoDocumento = $_POST['tipoDocumento'];
	$identificacion = $_POST['numeroIdentificacion'];
	$nombrePaciente = $_POST['nombrePaciente'];	
	$edad = $_POST['edad'];	
	$tipoEdad = $_POST['tipoEdad'];		
	$categoria = $_POST['categoria'];
	$subcategoria = $_POST['subcategoria'];	
	$tema = $_POST['tema'];	
	$prioridad = $_POST['prioridad'];	
	$tipoTurnero = $_POST['tipoTurnero'];		
	$wemp_pmla = $_POST['wemp_pmla'];
	$codigoTurnero = $_POST['codigoTurnero'];
	$validarExisteTurno = $_POST['validarExisteTurno'];
	$turneroRedireccion = $_POST['turneroRedireccion'];	
	$solucionCitas = $_POST['solucionCitas'];	

	
	
	txtLog("new turno");
	$objTurno = new Turno($identificacion,$tipoDocumento,$nombrePaciente,$edad,$tipoEdad,$categoria,$subcategoria,$prioridad,$wemp_pmla,$codigoTurnero,$validarExisteTurno,$tema,$tipoTurnero,$turneroRedireccion,$solucionCitas);

	//echo "     1.";
	//echo json_encode(" Turno ini:" . $objTurno->Turno);
	
	$objTurno->GenerarTurno();
	//echo json_encode(",objTurno:" . isset($objTurno)?"set":"nulo");	
	//echo json_encode(",Turno asig:" . $objTurno->Turno);
	txtLog("objTurno: " . json_encode($objTurno));

	$obj = (object)[
		'Turno' => $objTurno->Turno,
		'TipoIdentificacion' =>  $objTurno->TipoIdentificacion,
		'NumeroIdentificacion' =>  $objTurno->NumeroIdentificacion,
		'Nombre' =>  $objTurno->Nombre,
		'Categoria' =>  $objTurno->Categoria,
		'CategoriaSecundaria' =>  $objTurno->CategoriaSecundaria,
		'NombreCategoria' =>  $objTurno->NombreCategoria,
		'NombreCategoriaSecundaria' =>  $objTurno->NombreCategoriaSecundaria,
		'Prioridad' =>  $objTurno->Prioridad,
		'Edad' =>  $objTurno->Edad,
		'TipoEdad' =>  $objTurno->TipoEdad,
		'ValidarExisteTurno' =>  $objTurno->ValidarExisteTurno,
		'YaExisteTurnoHoy' =>  $objTurno->YaExisteTurnoHoy,
		'Piso' =>  $objTurno->Piso,
		'Tema' =>  $objTurno->Tema,
		'NombreTema' => utf8_encode(strip_tags($objTurno->NombreTema)),
		'FichoTurno' =>  $objTurno->FichoTurno,
		'Error' =>  strip_tags($objTurno->Error),
		'MensajeError' =>  strip_tags($objTurno->MensajeError),
		'codigoTurnero' =>  $objTurno->codigoTurnero,
	];
	
	echo json_encode($obj);	
	
	exit();

}

//En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");

class Turno 
{
	public $TipoIdentificacion;
	public $NumeroIdentificacion;
	public $Nombre;
	public $Categoria;
	public $CategoriaSecundaria;	
	public $NombreCategoria;
	public $NombreCategoriaSecundaria;		
	public $Prioridad;	
	public $Edad;
	public $TipoEdad;
	public $ValidarExisteTurno;
	public $YaExisteTurnoHoy;
	public $Turno;	
	public $Piso;		
	public $Tema;	
	public $NombreTema;
	public $TipoTurnero;	
	public $FichoTurno;
	public $Error;
	public $MensajeError;
	public $TurneroRedireccion;
	public $wbasedato;
	public $codigoTurnero;	
	public $SolucionCitas; 
	public $conex;	
	public $wemp_pmla;	

	public function __construct($numeroIdentificacion,$tipoIdentificacion,$nombrePaciente,$edad,$tipoEdad,$categoria,$subcategoria,$prioridad,$wemp_pmla,$codigoTurnero,$validarExisteTurno,$tema,$tipoTurnero,$turneroRedireccion,$solucionCitas)
	{
		$this->TipoIdentificacion = $tipoIdentificacion;
		$this->NumeroIdentificacion = $numeroIdentificacion;
		$this->Nombre = $nombrePaciente;
		$this->Edad = $edad;		
		$this->TipoEdad = $tipoEdad;	
		$this->Categoria = $categoria;		
		$this->CategoriaSecundaria = $subcategoria;				
		$this->Prioridad = $prioridad;			
		$this->TipoTurnero = $tipoTurnero;			
		$this->Tema = $tema;	
		$this->SolucionCitas = $solucionCitas;			
		$this->ValidarExisteTurno=$validarExisteTurno;
		$this->YaExisteTurnoHoy=false;
		$this->Turno="";	
		$this->FichoTurno="";
		$this->Error=true;
		$this->MensajeError="";
		$this->codigoTurnero = $codigoTurnero;
		$this->TurneroRedireccion = $turneroRedireccion;
		$this->conex = obtenerConexionBD("matrix");	
		$this->wemp_pmla = $wemp_pmla;


	}	
	
	public function getPrefixTables($prefix)
	{
		
		$sql = "SELECT descripcion FROM 
					root_000117
				WHERE nombreCc = '".$prefix."'";
		$res = mysql_query($sql, $this->conex);
		$row = mysql_fetch_assoc($res);
				
		$newPrefix = substr($row["descripcion"],0,3);
		
		return strtolower($newPrefix);
	}
	
	public function GenerarTurno()
	{
			
			if ($this->TipoTurnero == "URGENCIAS")
			{
				$this->wbasedato = utf8_encode(consultarAliasPorAplicacion($this->conex, $this->wemp_pmla, 'movhos'));
				//Genera el turno en la tabla movhos_000178 para conservar compatibilidad con los turneros de urgencias
				$this->GenerarTurnoUrgencias();
			}		

			if ($this->TipoTurnero == "ENDOSCOPIA")
			{
			
				$this->wbasedato = utf8_encode(consultarAliasPorAplicacion($this->conex, $this->wemp_pmla, $this->SolucionCitas));
				//Genera el turno en la tabla citasen_000023 para conservar compatibilidad con los turneros de urgencias
				$this->GenerarTurnoEndoscopia();
			}				
			
			$this->wbasedato = utf8_encode(consultarAliasPorAplicacion($this->conex, $this->wemp_pmla, 'cliame'));
			//Genera el turno en la tabla cliame_000304
			$this->GenerarTurnoLobby();	
	
			
	
	}	
	
	public function GenerarTurnoUrgencias()
	{

	
		$sqlValTurno = " SELECT Atutur
						   FROM ".$this->wbasedato."_000178
						  WHERE Fecha_data 	= '".date("Y-m-d")."'
							AND Atudoc 		= '".$this->NumeroIdentificacion."'
							AND Atutdo 		= '".$this->TipoIdentificacion."'
							AND Atutem		= '".$this->Tema."'
							AND Atuest 		= 'on'
						  ORDER BY Atutur DESC
		";
		//echo ($sqlValTurno);

		$resValTurno = mysql_query($sqlValTurno, $this->conex) or die("<b>ERROR EN QUERY MATRIX(sqlValTurno):</b><br>".mysql_error());
		if($this->ValidarExisteTurno == 'true' && $rowValTurno = mysql_fetch_array($resValTurno))
		{
			$this->Error=false;
			$this->MensajeError="";			
			$this->YaExisteTurnoHoy = true;
			$this->Turno = $rowValTurno['Atutur'];
			//$this->FichoTurno = htmlTurno($rowValTurno['Atutur'], $tipDocumento, $numDocumento, $nombrePaciente, true);
			return;
		}

		$wbasedatoCliame = utf8_encode(consultarAliasPorAplicacion($this->conex, $this->wemp_pmla, 'cliame'));
		// --> Obtener centro de costos del turnero
		$ccoTurnero = '';
		$sqlCco = "
		SELECT codcco 
		  FROM ".$wbasedatoCliame."_000305
		 WHERE codtem = '".$this->Tema."'
		";
		
		$resCco = mysql_query($sqlCco, $this->conex) or die("<b>ERROR EN QUERY MATRIX(sqlSala):</b><br>".mysql_error());
		if($rowCco = mysql_fetch_array($resCco))
		{
			if(trim($rowCco['codcco']) != '')
				$ccoTurnero = $rowCco['codcco'];
		}			
		
		
		// --> Obtener la sala de espera del turnero
		$salaEspera = '';
		$sqlSala = "
		SELECT Tursal 
		  FROM ".$this->wbasedato."_000216
		 WHERE Turcod = '".$this->codigoTurnero."'
		   AND Turest = 'on'
		";
		$resSala = mysql_query($sqlSala, $this->conex) or die("<b>ERROR EN QUERY MATRIX(sqlSala):</b><br>".mysql_error());
		if($rowSala = mysql_fetch_array($resSala))
		{
			if(trim($rowSala['Tursal']) != '')
				$salaEspera = $rowSala['Tursal'];
		}	
		
		if($salaEspera == '')
		{
			// --> Obtener la sala de espera por defecto
			$sqlSalaDefecto = "
			SELECT Salcod
			  FROM ".$this->wbasedato."_000182
			 WHERE Salaps = 'on'
			   AND Salest = 'on'		
				AND Salcco = '".$ccoTurnero."'
			";
			//echo ($sqlSalaDefecto);
			$resSalaDefecto = mysql_query($sqlSalaDefecto, $this->conex) or die("<b>ERROR EN QUERY MATRIX(sqlSalaDefecto):</b><br>".mysql_error());
			if($rowSalaDefecto = mysql_fetch_array($resSalaDefecto))
				$salaEspera = $rowSalaDefecto['Salcod'];
			else
				$salaEspera = '*';
		}
		
		// --> Validar si la categoria si puede ser seleccionada en este turnero
		if($salaEspera != '' && $salaEspera != '*')
		{
			// --> Obtener la sala en la que se puede seleccionar la categoria
			$sqlCatSal = "
			SELECT Catsal, Catnom, Salnom 
			  FROM ".$this->wbasedato."_000207 AS A INNER JOIN ".$this->wbasedato."_000182 AS B ON(Catsal = Salcod)
			 WHERE Catcod = '".$this->Categoria."'	
			   AND Catsal != ''					 
			";
			$resCatSal = mysql_query($sqlCatSal, $this->conex) or die("<b>ERROR EN QUERY MATRIX(sqlCatSal):</b><br>".mysql_error());
			if($rowCatSal = mysql_fetch_array($resCatSal))
			{
				if($salaEspera != $rowCatSal['Catsal'])
				{
					$sqlHorario = "
					SELECT Salhif, Salhff  
					  FROM ".$this->wbasedato."_000182
					 WHERE Salcod = '".$salaEspera."'		   
					";
					$resHorario = mysql_query($sqlHorario, $this->conex) or die("<b>ERROR EN QUERY MATRIX(sqlHorario):</b><br>".mysql_error());
					if($rowHorario = mysql_fetch_array($resHorario))
					{
						$horaActual 	= strtotime(date("Y-m-d H:i"));
						// --> Si está dentro del horario de funcionamiento de aplicacion de filtro de la sala
						if(strtotime(date("Y-m-d")." ".$rowHorario['Salhif']) <= $horaActual && $horaActual <= strtotime(date("Y-m-d")." ".$rowHorario['Salhff']))
						{
							// --> No se puede generar el turno
							$this->Error=true;
							$this->MensajeError="Para la categor&iacute;a ".$rowCatSal['Catnom'].", por favor genere su turno en<br>la sala de atenci&oacute;n ".$rowCatSal['Salnom'];								
							return;
						}
					}
				}
			}	
		}	
							

		// --> Bloquear tabla de turnos
		$sqlBloque = "
		LOCK TABLES ".$this->wbasedato."_000178 WRITE;
		";
		mysql_query($sqlBloque, $this->conex) or die("<b>ERROR EN QUERY MATRIX(sqlBloque):</b><br>".mysql_error());
		
		// --> Cancelar turno existente
		// if($validarExisteTurno == 'false' && $turnoACancelar != '')
		// {
			// $sqlCancelarTur = "
			// UPDATE ".$wbasedato."_000178
			   // SET Atuest = 'off'
			 // WHERE Atutur = '".$turnoACancelar."'
			// ";
			// mysql_query($sqlCancelarTur, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCancelarTur):</b><br>".mysql_error());
		// }
		
		// --> Obtener el ultimo consecutivo
		$sqlObtConsec = " SELECT MAX(REPLACE(Atutur, '-', '')*1) AS turno
							FROM ".$this->wbasedato."_000178
						   WHERE Atutur LIKE '".date('ymd')."%' and Atutem = '".$this->Tema."'
		";
		$resObtConsec = mysql_query($sqlObtConsec, $this->conex) or die("<b>ERROR EN QUERY MATRIX(sqlObtConsec):</b><br>".mysql_error());
		
		$rowObtConsec = mysql_fetch_array($resObtConsec);
		
		
		if(!$rowObtConsec)
		{
			$this->Error=true;
			$this->MensajeError="Error: El turno no se ha podido asignar.";		
			// --> Desbloquear tabla
			$sqlBloque = "
			UNLOCK TABLES
			";
			mysql_query($sqlBloque, $this->conex) or die("<b>ERROR EN QUERY MATRIX(sqlBloque):</b><br>".mysql_error());				
				
			return;			
		}

		$fechaUltiConse = substr($rowObtConsec['turno'], 0, 6);
		$ultConsecutivo = (($fechaUltiConse == date('ymd')) ? substr($rowObtConsec['turno'], 6) : 0);
		$ultConsecutivo	= ($ultConsecutivo*1)+1;
		if ($ultConsecutivo == 1)
			$ultConsecutivo = $this->Tema."001";
		// --> Asignar ceros a la izquierda hasta completar 5 digitos
		while(strlen($ultConsecutivo) < 5)
			$ultConsecutivo = '0'.$ultConsecutivo;

		$nuevoTurno = date('ymd').'-'.$ultConsecutivo;					
		
		// --> Asignarle el turno al paciente
		 
		$sqlAsigTur = "INSERT INTO ".$this->wbasedato."_000178 (Medico,Fecha_data,Hora_data,Atutur,Atudoc,Atutdo,Atuest, Atusea,Atunom,Atueda,Atuted,Atuten,Atuprd,Atutem,Seguridad,id)
		VALUES ('".$this->wbasedato."','".date('Y-m-d')."','".date('H:i:s')."','".$nuevoTurno."', 	'".$this->NumeroIdentificacion."','".$this->TipoIdentificacion."','on','".$salaEspera."','".$this->Nombre."','".$this->Edad."','".$this->TipoEdad."','".$this->Categoria."','".$this->Prioridad."','".$this->Tema."','C-Turnero','')
		";
			// $this->Error = true;
			// $this->MensajeError = $sqlAsigTur;
			// return;
		
		$resObtConsec = mysql_query($sqlAsigTur, $this->conex);

		// --> Si ha ocurrido un error guardando el turno
		if(!$resObtConsec)
		{
			$this->Error = true;
			$this->MensajeError = "Discúlpenos, a ocurrido un error asignando el turno. Por favor contacte al personal de soporte.";

		}
		// --> Genero el ficho del turno
		else
		{
			$this->Error = false;
			$this->Turno = $nuevoTurno;
			//$this->FichoTurno 	= htmlTurno($nuevoTurno, $tipDocumento, $numDocumento, $nombrePaciente, false);
		}

		
		// --> Desbloquear tabla
		$sqlBloque = "
		UNLOCK TABLES
		";
		mysql_query($sqlBloque, $this->conex) or die("<b>ERROR EN QUERY MATRIX(sqlBloque):</b><br>".mysql_error());																

	}
	
 	public function GenerarTurnoEndoscopia()
	{

	
	
		$prefix = $this->getPrefixTables($this->wbasedato);
		

		
		$sqlValTurno = " SELECT ".$prefix."tur
						   FROM " . $this->wbasedato . "_000023
						  WHERE Fecha_data  = '" . date("Y-m-d")."'
							AND ".$prefix."doc      = '" . $this->NumeroIdentificacion."'
							AND ".$prefix."tip      = '" . $this->TipoIdentificacion."'						
							AND ".$prefix."est      = 'on'
						  ORDER BY ".$prefix."tur DESC";		
		

		$resValTurno = mysql_query($sqlValTurno, $this->conex) or die("<b>ERROR EN QUERY MATRIX(sqlValTurno):</b><br>".mysql_error());
		if($this->ValidarExisteTurno == 'true' && $rowValTurno = mysql_fetch_array($resValTurno))
		{
			$this->Error=false;
			$this->MensajeError="";			
			$this->YaExisteTurnoHoy = true;
			$this->Turno = $rowValTurno[$prefix.'tur'];
			//$this->FichoTurno = htmlTurno($rowValTurno['Atutur'], $tipDocumento, $numDocumento, $nombrePaciente, true);
			return;
		}

		$wbasedatoCliame = utf8_encode(consultarAliasPorAplicacion($this->conex, $this->wemp_pmla, 'cliame'));
		// --> Obtener centro de costos del turnero
		$ccoTurnero = '';
		$sqlCco = "
		SELECT codcco 
		  FROM ".$wbasedatoCliame."_000305
		 WHERE codtem = '".$this->Tema."'";
		
		$resCco = mysql_query($sqlCco, $this->conex) or die("<b>ERROR EN QUERY MATRIX(sqlSala):</b><br>".mysql_error());
		if($rowCco = mysql_fetch_array($resCco))
		{
			if(trim($rowCco['codcco']) != '')
				$ccoTurnero = $rowCco['codcco'];
		}			
		
		// --> Obtener el ultimo consecutivo
		$sqlObtConsec = " SELECT MAX(REPLACE(".$prefix."tur, '-E', '')*1) AS turno
							FROM ".$this->wbasedato."_000023
						   WHERE ".$prefix."tur LIKE '".date('ymd')."%'";
		$resObtConsec = mysql_query($sqlObtConsec, $this->conex) or die("<b>ERROR EN QUERY MATRIX(sqlObtConsec):</b><br>".mysql_error());
		
		$rowObtConsec = mysql_fetch_array($resObtConsec);
		
		
		if(!$rowObtConsec)
		{
			$this->Error=true;
			$this->MensajeError="Error: El turno no se ha podido asignar.";		
	
			return;			
		}

		$fechaUltiConse = substr($rowObtConsec['turno'], 0, 6);
		$ultConsecutivo = (($fechaUltiConse == date('ymd')) ? substr($rowObtConsec['turno'], 6) : 0);
		$ultConsecutivo	= ($ultConsecutivo*1)+1;
		if ($ultConsecutivo == 1)
			$ultConsecutivo = $this->Tema."001";
		// --> Asignar ceros a la izquierda hasta completar 5 digitos
		while(strlen($ultConsecutivo) < 5)
			$ultConsecutivo = '0'.$ultConsecutivo;

		$nuevoTurno = date('ymd').'-E'.$ultConsecutivo;					
		
		
		$sqlInactivarTurnosViejos = "UPDATE
										" . $this->wbasedato . "_000023
									SET 
										  ".$prefix."est = 'off'
									WHERE
										Fecha_data  = '" . date("Y-m-d")."'
										AND ".$prefix."doc      = '" . $this->NumeroIdentificacion."'
										AND ".$prefix."tip      = '" . $this->TipoIdentificacion."'
		";		
		$resCancelarTurno = mysql_query($sqlInactivarTurnosViejos, $this->conex);		
		
		// --> Asignarle el turno al paciente
		 
		$sqlAsigTur = "INSERT INTO ".$this->wbasedato."_000023 (Medico,Fecha_data,Hora_data,".$prefix."tur,".$prefix."doc,".$prefix."tip,".$prefix."est,".$prefix."hau, ".$prefix."eau,Seguridad,id)
		VALUES ('".$this->wbasedato."','".date('Y-m-d')."','".date('H:i:s')."','".$nuevoTurno."', 	'".$this->NumeroIdentificacion."','".$this->TipoIdentificacion."','on','".date('H:i:s')."','on','C-Turnero','')
		";
	
			
		$resObtConsec = mysql_query($sqlAsigTur, $this->conex);

		// --> Si ha ocurrido un error guardando el turno
		if(!$resObtConsec)
		{
			$this->Error = true;
			$this->MensajeError = "Discúlpenos, a ocurrido un error asignando el turno. Por favor contacte al personal de soporte.";

		}
		// --> Genero el ficho del turno
		else
		{
			$this->Error = false;
			$this->Turno = $nuevoTurno;
			//$this->FichoTurno 	= htmlTurno($nuevoTurno, $tipDocumento, $numDocumento, $nombrePaciente, false);
		}

	}
	 
  
 	public function GenerarTurnoLobby()
	{


			// --> Obtener el prefijo del tipo de servicio
			$prefijo = "";
			$desPiso = "";
			$sqlPrefijo = " 
			SELECT Serpre, Sernom, Serpis
			  FROM ".$this->wbasedato."_000298
			 WHERE Sertem = '".$this->Tema."' 
			   AND Sercod = '".$this->Categoria."'
			";
			$resPrefijo = mysql_query($sqlPrefijo, $this->conex) or die("<b>ERROR EN QUERY MATRIX(sqlPrefijo):</b><br>".mysql_error());
			txtLog("SQL obtener prefijo turno: ".$sqlPrefijo."; ");

			//echo ("SQL obtener prefijo turno: ".$sqlPrefijo."; ");
			if($rowPrefijo = mysql_fetch_array($resPrefijo))
			{
				$prefijo 		= $rowPrefijo['Serpre'];
				$nomServicio 	= utf8_encode($rowPrefijo['Sernom']);
				$desPiso        = $rowPrefijo['Serpis'];
			}
		
			
			$nomTema = "";
			$sqlTema = "
			SELECT Codnom,Codlog
			  FROM ".$this->wbasedato."_000305
			 WHERE Codtem = '".$this->Tema."'
			   AND Codest = 'on'
			";
			$resTema = mysql_query($sqlTema, $this->conex) or die("<b>ERROR EN QUERY MATRIX(sqlTema):</b><br>".mysql_error());
			if( $rowTema = mysql_fetch_array($resTema) )
			{
				$nomTema = $rowTema['Codnom'];
				$nomlogo = $rowTema['Codlog'];
			}

			// --> Bloquear tabla de turnos
			//$sqlBloque = "
			//LOCK TABLES ".$this->wbasedato."_000304 WRITE;
			//";
			//mysql_query($sqlBloque, $this->conex) or die("<b>ERROR EN QUERY MATRIX(sqlBloque):</b><br>".mysql_error());
					
			// --> Obtener el ultimo consecutivo
			$sqlObtConsec = " 
			SELECT MAX(REPLACE(Turtur, '-".$prefijo."', '')*1) AS turno
			  FROM ".$this->wbasedato."_000304
			 WHERE Turtem = '".$this->Tema."' 
			   AND Turtur LIKE '".date('ymd')."%'
			   AND Turtse = '".$this->Categoria."'
			";
			//echo ("SQL obtener consecutivo turno: ".$sqlObtConsec."; ");
			$resObtConsec = mysql_query($sqlObtConsec, $this->conex) or die("<b>ERROR EN QUERY MATRIX(sqlObtConsec):</b><br>".mysql_error());
			if($rowObtConsec = mysql_fetch_array($resObtConsec))
			{
				$fechaUltiConse = substr($rowObtConsec['turno'], 0, 6);
				$ultConsecutivo = (($fechaUltiConse == date('ymd')) ? substr($rowObtConsec['turno'], 6) : 0);
				$ultConsecutivo	= ($ultConsecutivo*1)+1;
				if ($ultConsecutivo == 1)
					$ultConsecutivo = $this->Tema."001";
				// --> Asignar ceros a la izquierda hasta completar 5 digitos
				while(strlen($ultConsecutivo) < 5)
					$ultConsecutivo = '0'.$ultConsecutivo;

				$nuevoTurno = date('ymd').'-'.$prefijo.$ultConsecutivo;
				
				
				if (($this->Turno+'') != '' )
					$nuevoTurno = $this->Turno;
				
                if (isset($turnoACancelar) && $turnoACancelar != '')
					// --> Asignarle el turno al paciente con redireccionamiento
					$sqlAsigTur = "INSERT INTO ".$this->wbasedato."_000304 (Medico, 			Fecha_data, 			Hora_data, 				Turtem,			Turtur, 			Turdoc, 				Turtdo, 				Turest, Turnom,					Turtse,					Tursec,						Turupr, 		Turred,	Turtrd,		Seguridad, 		id)
															  VALUES ('".$this->wbasedato."', '".date('Y-m-d')."', 	'".date('H:i:s')."',	'".$this->Tema."',	'".$nuevoTurno."', 	'".$this->NumeroIdentificacion."',	'".$this->TipoIdentificacion."', 	'on', 	'".$this->Nombre."',	'".$this->Categoria."',	'".$this->CategoriaSecundaria."',	'".$this->Prioridad."',	'".$turnoACancelar."','".$this->TurneroRedireccion."','C-Turnero',	'')
					";

                else
					// --> Asignarle el turno al paciente
					$sqlAsigTur = "INSERT INTO ".$this->wbasedato."_000304 (Medico, 			Fecha_data, 			Hora_data, 				Turtem,			Turtur, 			Turdoc, 				Turtdo, 				Turest, Turnom,					Turtse,					Tursec,						Turupr, Turtrd,					Seguridad, 		id)
															  VALUES ('".$this->wbasedato."', '".date('Y-m-d')."', 	'".date('H:i:s')."',	'".$this->Tema."',	'".$nuevoTurno."', 	'".$this->NumeroIdentificacion."',	'".$this->TipoIdentificacion."', 	'on', 	'".$this->Nombre."',	'".$this->Categoria."',	'".$this->CategoriaSecundaria."',	'".$this->Prioridad."','".$this->TurneroRedireccion."','C-Turnero',	'')
					";				
				
				
				txtLog("SQL Insert turno: ".$sqlAsigTur."; ");
				//echo ("SQL Insert turno: ".$sqlAsigTur."; ");
				
				$resObtConsec = mysql_query($sqlAsigTur, $conex);

				
				$this->Turno		= $nuevoTurno;
				$this->Piso		= $desPiso;
				$this->NombreTema		= $nomTema;					
				$this->NombreCategoria		= nomServicio;
		
				
				
				// --> Si ha ocurrido un error guardando el turno
				// if(!$resObtConsec)
				// {
					// $this->Error 	= true;
					// $this->MensajeError 	= "	<span style='font-size:20px'>Disc&uacute;lpenos, a ocurrido un error asignando el turno.<br>Por favor contacte al personal de soporte.</span><br>
												// <span style='font-size:10px'>(sqlAsigTur: ".mysql_error().')</span>';
				// }
				// // --> Genero el ficho del turno
				// else
				// {
					// $this->Turno		= $nuevoTurno;
					// $this->Piso		= $desPiso;
					// $this->NombreTema		= $nomTema;					
					// $this->NombreCategoria		= nomServicio;
					// $this->FichoTurno 	= $this->htmlTurno(false,$nomlogo);
				// }
			}
			else
			{
				$this->Error 	= true;
				$this->MensajeError 	= 'Error: El turno no se ha podido asignar.';
			}


	} 
	
	function htmlTurno($reimpresion,$nomlogo)
	{
		$turno = substr($this->Turno, 7);
		$turno = substr($this->Turno, 0, 2)." ".substr($this->Turno, 2, 5);  

		if ($desPiso !== '')
		{
			$nomPiso = "<tr><td align='right' style='font-size:2rem;'><b>".$this->Piso."</b></td></tr>";
		}
		else
		{
			$nomPiso = "";
		}
			
		$html = "
		<table style='font-family: verdana;font-size:1rem;'>
			<tr>
				<td colspan='2' align='center'>
					<img width='118' heigth='58' src='../../images/medical/root/".$nomlogo."' style='background-color: rgb(50,50,50);'>
					<br>
					".utf8_encode($this->NombreTema)."
				</td>
			</tr>
			<tr>
				<td colspan='2' align='center'>
					Es un placer servirle
					<br><br>
				</td>
			</tr>
			<tr>
				<td >Turno:&nbsp;&nbsp;</td>
				<td align='right' style='font-size:2rem;'><b>".$turno."</b></td></tr>
			<tr>
				<td style='padding-bottom:3px;' colspan='2'>".$this->TipoIdentificacion."&nbsp;&nbsp;&nbsp;".$this->NumeroIdentificacion."</td>
			</tr>
			<tr>
				<td style='padding-bottom:3px;' colspan='2'>".ucwords(strtolower($this->Nombre))."</td>
			</tr>
			<tr>
				<td style='padding-bottom:3px;' colspan='2'>Servicio: ".ucwords(strtolower($this->NombreCategoria))." ".ucwords(strtolower($this->NombreCategoriaSecundaria))."</td>
			</tr>
			".$this->Piso."
			<tr>
				<td colspan='2' align='center' style='font-size:0.8rem'>
					<br><b>Por favor conserve este tiquete hasta que sea atendido.</b>
				</td>
			</tr>
			<tr>
				<td colspan='2' align='center' style='font-size:0.7rem'>
					".(($reimpresion) ? "<b>(Reimpresi&oacute;n)</b>" : "")." Fecha: ".date('Y-m-d')." &nbsp;Hora: ".date('g:i:s a')."
				</td>
			</tr>
		</table>";
		
		return $html;
	}

}


	function txtLog($txt, $inicializar=false)
	{
			try {
					$l = date('H:i:s', time()) . ' ' . $txt . "\n";
					if ($inicializar)
						file_put_contents('log_la_obt.txt', $l, LOCK_EX);
					else
						file_put_contents('log_la_obt.txt', $l, FILE_APPEND | LOCK_EX);
			} catch (\Exception $e) {
			}
	}
  
?>
