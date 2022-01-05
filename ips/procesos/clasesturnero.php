<?php


// CREATE TABLE `cliame_0001305` (
	// `Medico` VARCHAR(8) NULL DEFAULT NULL,
	// `Fecha_data` DATE NULL DEFAULT '0000-00-00',
	// `Hora_data` TIME NULL DEFAULT '00:00:00',
	// `Porcod` VARCHAR(2) NULL DEFAULT '' COMMENT 'Codigo del poertal',
	// `Pornom` VARCHAR(80) NULL DEFAULT '' COMMENT 'Nombre del portal',
	// `Porlog` VARCHAR(80) NULL DEFAULT NULL COMMENT 'Nombre del logo',
	// `Pormbv` VARCHAR(200) NULL DEFAULT '' COMMENT 'Mensaje Bienvenida',
	// `Pormsl` VARCHAR(200) NULL DEFAULT '' COMMENT 'Mensaje Seleccion',
	// `Porest` CHAR(3) NULL DEFAULT 'off' COMMENT 'Estado',	
	// `Seguridad` VARCHAR(10) NULL DEFAULT NULL,
	// `id` BIGINT NOT NULL AUTO_INCREMENT,
	// PRIMARY KEY (`id`),
	// UNIQUE INDEX `Codtem_UNIQUE` (`Porcod`)
// )
// COMMENT='Portales de turneros'
// COLLATE='latin1_swedish_ci'
// ENGINE=InnoDB
// AUTO_INCREMENT=18
// ;


// CREATE TABLE `cliame_0001298` (
	// `Medico` VARCHAR(8) NULL DEFAULT NULL,
	// `Fecha_data` DATE NULL DEFAULT '0000-00-00',
	// `Hora_data` TIME NULL DEFAULT '00:00:00',
	// `Porcod` VARCHAR(2) NULL DEFAULT '' COMMENT 'Codigo del portal',
	// `Portur` VARCHAR(2) NULL DEFAULT '' COMMENT 'Codigo del turnero',
	// `Porord` INT NULL DEFAULT '0' COMMENT 'Orden',	
	// `Porest` CHAR(3) NULL DEFAULT NULL COMMENT 'Estado',
	// `Seguridad` VARCHAR(10) NULL DEFAULT NULL,
	// `id` BIGINT NOT NULL AUTO_INCREMENT,
	// PRIMARY KEY (`id`)
// )
// COMMENT='Turneros por Portal'
// COLLATE='latin1_swedish_ci'
// ENGINE=InnoDB
// AUTO_INCREMENT=18
// ;



class Turnero {
	public $CodigoTurnero;
	public $NombreTurnero;	
	public $IngresoPorPasos;
	public $TieneLectorCedula;
	public $TieneIngresoManual;
	public $TieneCategorias;
	public $TienePrioridad;
	public $TieneTipoDocumento;
	public $TieneSubCategorias;
	public $TieneDocumento;
	public $TieneNombre;
	public $TieneEdad;	
	public $EsUrgencias;
	public $TieneCitas;	
	public $SolucionCitas;		
	public $ValidarExisteTurno;
	public $MensajeBienvenida;
	public $MensajeLector;
	public $MensajeIngresoManual;
	public $MensajeTiposDocumento;
	public $MensajeDatosPersonales;
	public $MensajeCategorias;
	public $MensajeTurnoGenerado;
	public $MensajeSinTipoDocumento;
	public $MensajeSinNumeroDocumento;
	public $MensajeNumeroDocumento;
	public $MensajeSinNombre;
	public $MensajeNombre;
	public $MensajeEdad;
	public $MensajePrioridad;
	public $MensajeSubcategoria;
	public $MensajeSinEdad;
	public $MensajeSinTipoEdad;
	public $MensajeSinCategoria;	
	public $RedirTurnero;	
	public $UrlScriptGenerarTurno;	
	public $TiposDocumento = Array();
	public $Categorias = Array();
	public $Prioridades = Array();	
	public $Error;
	public $wemp_pmla;
	public $MensajeError;
	private $wbasedato;
	private $conex;
	
	public function __construct($codigoTurnero,$wemp_pmla,$conex)
	{

		$this->CodigoTurnero = $codigoTurnero;
		$this->wemp_pmla = $wemp_pmla;
		$this->Error=true;
		$this->MensajeError="";
		$this->conex = $conex;			
		$this->wbasedato = consultarAliasPorAplicacion($this->conex, $wemp_pmla, 'cliame');			
		
	}	
	
	public function CargarConfiguracion()
	{
					
		// --> Obtener maestro de tipos de documento (NU)
		$sqlTipDoc = "SELECT Id, Codigo, Descripcion
					FROM root_000007
				   WHERE Codigo IN('CC', 'TI', 'RC', 'CE', 'PA')
				   order by Id asc
		";
		$resTipDoc = mysql_query($sqlTipDoc, $this->conex) or die("<b>ERROR EN QUERY MATRIX(sqlTipDoc):</b><br>".mysql_error());
		while($rowTipDoc = mysql_fetch_array($resTipDoc))
		{
			$objTipoDocumento = new TipoDocumento();
			$objTipoDocumento->Codigo = $rowTipDoc['Codigo'];
			$objTipoDocumento->Nombre = utf8_encode(rplcSpecialChar($rowTipDoc['Descripcion']));
			$this->TiposDocumento[]= $objTipoDocumento;
			
		}

		
		// --> Obtener categorias asociadas al turnero
		$sqlCategorias = "SELECT Sercod, Sernom, Serest FROM ".$this->wbasedato."_000298 WHERE Sertem = '".$this->CodigoTurnero."' AND Serest= 'on' ORDER BY Serord";
		
		
		$resCategorias = mysql_query($sqlCategorias, $this->conex) or die("<b>ERROR EN QUERY MATRIX(sqlCategorias):</b><br>".mysql_error());
		while($rowCategoria = mysql_fetch_array($resCategorias))
		{
			$objCategoria = new Categoria();
			$objCategoria->Codigo = $rowCategoria['Sercod'];
			$objCategoria->Nombre = utf8_encode(rplcSpecialChar($rowCategoria['Sernom']));
			$this->Categorias[]= $objCategoria;

			

			$sqlSubCategorias = "SELECT Seccod, Secnom FROM ".$this->wbasedato."_000310  left join ".$this->wbasedato."_000309 on (Seccod = Rsssec) WHERE Rssser = '".$objCategoria->Codigo."' AND Rsstem = '".$this->CodigoTurnero."' AND Rssest= 'on' ";			
			$resSubCategorias = mysql_query($sqlSubCategorias, $this->conex) or die("<b>ERROR EN QUERY MATRIX(sqlSubCategorias):</b><br>".mysql_error());
			while($rowSubCategoria = mysql_fetch_array($resSubCategorias))
			{
				$objSubCategoria = new Subcategoria();
				$objSubCategoria->Codigo = $rowSubCategoria['Seccod'];
				$objSubCategoria->Nombre = utf8_encode(rplcSpecialChar($rowSubCategoria['Secnom']));
				$objCategoria->Subcategorias[]= $objSubCategoria;
				
			}			
			
			
		}

		// --> Obtener prioridades asiciadas al turnero
		$sqlPrioridades = "SELECT Concod, Connom, Conest,Conpri,Conico
						FROM ".$this->wbasedato."_000299
							WHERE Conest 		= 'on'
							ORDER BY Conord
		";
		$resPrioridades = mysql_query($sqlPrioridades, $this->conex) or die("<b>ERROR EN QUERY MATRIX(sqlCategorias):</b><br>".mysql_error());

		while($rowPrioridad = mysql_fetch_array($resPrioridades))
		{
			$objPrioridad = new Prioridad();
			$objPrioridad->Codigo = $rowPrioridad['Concod'];
			$objPrioridad->Nombre =  utf8_encode(rplcSpecialChar($rowPrioridad['Connom']));
			$objPrioridad->Icono = $rowPrioridad['Conico'];
			if ($rowPrioridad['Conpri'] == 'on')
				$objPrioridad->EsPrioridad = true;
			else
				$objPrioridad->EsPrioridad = false;
				
			$this->Prioridades[]= $objPrioridad;
		}	

		// --> Obtener la configuraion del turnero
		$sqlConfigTurnero = " SELECT *
						   FROM ".$this->wbasedato."_000305
						  WHERE Codtem 	= '".$this->CodigoTurnero."'
							AND Codest 		= 'on'
						
		";
		$resConfigTurnero = mysql_query($sqlConfigTurnero, $this->conex) or die("<b>ERROR EN QUERY MATRIX(sqlConfigTurnero):</b><br>".mysql_error());
		if($rowConfigTurnero = mysql_fetch_array($resConfigTurnero))
		{

			 $this->NombreTurnero = utf8_encode(rplcSpecialChar($rowConfigTurnero['Codnom']));
			 if ($rowConfigTurnero['Codipp'] == 'on')
				 $this->IngresoPorPasos = true;
			 else
				 $this->IngresoPorPasos = false;

			 if ($rowConfigTurnero['Codlec'] == 'on')
				 $this->TieneLectorCedula = true;
			 else
				 $this->TieneLectorCedula = false;
			
			 if ($rowConfigTurnero['Codman'] == 'on')
				 $this->TieneIngresoManual = true;
			 else
				 $this->TieneIngresoManual = false;		

			 if ($rowConfigTurnero['Codcat'] == 'on')
				 $this->TieneCategorias = true;
			 else
				 $this->TieneCategorias = false;				

			 if ($rowConfigTurnero['Codpri'] == 'on')
				 $this->TienePrioridad = true;
			 else
				 $this->TienePrioridad = false;					

			 if ($rowConfigTurnero['Codvtu'] == 'on')
				 $this->ValidarExisteTurno = true;
			 else
				 $this->ValidarExisteTurno = false;
			 
			 if ($rowConfigTurnero['Codttd'] == 'on')
				 $this->TieneTipoDocumento = true;
			 else
				 $this->TieneTipoDocumento = false;
			 
			 if ($rowConfigTurnero['Codtsc'] == 'on')
				 $this->TieneSubCategorias = true;
			 else
				 $this->TieneSubCategorias = false;
			
			 if ($rowConfigTurnero['Codtdo'] == 'on')
				 $this->TieneDocumento = true;
			 else
				 $this->TieneDocumento = false;

			 if ($rowConfigTurnero['Codtno'] == 'on')
				 $this->TieneNombre = true;
			 else
				 $this->TieneNombre = false;
			 
			 if ($rowConfigTurnero['Codurg'] == 'on')
				 $this->EsUrgencias = true;
			 else
				 $this->EsUrgencias = false;			 

			 if ($rowConfigTurnero['Codted'] == 'on')
				 $this->TieneEdad = true;
			 else
				 $this->TieneEdad = false;
			 
			 if ($rowConfigTurnero['Codtci'] == 'on')
				 $this->TieneCitas = true;
			 else
				 $this->TieneCitas = false;			 

			 $this->SolucionCitas = utf8_encode(rplcSpecialChar($rowConfigTurnero['Codmsl']));			 
			 $this->MensajeBienvenida = utf8_encode(rplcSpecialChar($rowConfigTurnero['Codmbv']));
			 $this->MensajeLector = utf8_encode(rplcSpecialChar($rowConfigTurnero['Codmlc']));
			 $this->MensajeIngresoManual = utf8_encode(rplcSpecialChar($rowConfigTurnero['Codmim']));
			 $this->MensajeTiposDocumento = utf8_encode(rplcSpecialChar($rowConfigTurnero['Codmtd']));
			 $this->MensajeDatosPersonales = utf8_encode(rplcSpecialChar($rowConfigTurnero['Codmdp']));
			 $this->MensajeCategorias = utf8_encode(rplcSpecialChar($rowConfigTurnero['Codmct']));
			 $this->MensajeTurnoGenerado = utf8_encode(rplcSpecialChar($rowConfigTurnero['Codmtg']));
			 $this->MensajeSinTipoDocumento = utf8_encode(rplcSpecialChar($rowConfigTurnero['Codmst']));
			 $this->MensajeSinNumeroDocumento = utf8_encode(rplcSpecialChar($rowConfigTurnero['Codmsn']));
			 $this->MensajeNumeroDocumento = utf8_encode(rplcSpecialChar($rowConfigTurnero['Codmnd']));
			 $this->MensajeSinNombre = utf8_encode(rplcSpecialChar($rowConfigTurnero['Codsno']));
			 $this->MensajeNombre = utf8_encode(rplcSpecialChar($rowConfigTurnero['Codmno']));
			 $this->MensajeEdad = utf8_encode(rplcSpecialChar($rowConfigTurnero['Codmed']));
			 $this->MensajePrioridad = utf8_encode(rplcSpecialChar($rowConfigTurnero['Codmpr']));
			 $this->MensajeSubcategoria = utf8_encode(rplcSpecialChar($rowConfigTurnero['Codmsu']));
			 $this->MensajeSinEdad = utf8_encode(rplcSpecialChar($rowConfigTurnero['Codmse']));
			 $this->MensajeSinTipoEdad = utf8_encode(rplcSpecialChar($rowConfigTurnero['Codste']));
			 $this->MensajeSinCategoria = utf8_encode(rplcSpecialChar($rowConfigTurnero['Codmsc']));			
			 $this->RedirTurnero = utf8_encode(rplcSpecialChar($rowConfigTurnero['Codtrd']));			
			 $this->UrlScriptGenerarTurno = utf8_encode(rplcSpecialChar($rowConfigTurnero['Codsgt']));			
		}		

		$this->Error = false;
		$this->MensajeError = "";

	}

	  
}


function rplcSpecialChar($txt)
{
	$t = str_replace("Ñ", "&Ntilde;", $txt);
	$t = str_replace("ñ", "&ntilde;", $t);
	$t = str_replace("Á", "&Aacute;", $t);
	$t = str_replace("á", "&aacute;", $t);
	$t = str_replace("É", "&Eacute;", $t);
	$t = str_replace("é", "&eacute;", $t);
	$t = str_replace("Í", "&Iacute;", $t);
	$t = str_replace("í", "&iacute;", $t);
	$t = str_replace("Ó", "&Oacute;", $t);
	$t = str_replace("ó", "&oacute;", $t);
	$t = str_replace("Ú", "&Uacute;", $t);
	$t = str_replace("ú", "&uacute;", $t);
	return $t;
}
	
class TipoDocumento 
{
	public $Nombre;
	public $Codigo;
	public $Color='primary';
	public $Clase='m-boton text-h6 headline pt-4';	

}



class Categoria 
{
	public $Nombre;
	public $Codigo;
	public $Color='primary';
	public $Clase='m-boton text-h6 headline pt-4';
	public $Subcategorias = Array();

}

class Subcategoria 
{
	public $Nombre;
	public $Codigo;
	public $Color='primary';
	public $Clase='m-boton text-h6 headline pt-4';

}

class Prioridad 
{
	public $Nombre;
	public $Codigo;
	public $EsPrioridad;
	public $Icono;
	public $Color='white';

}


class Portal {
	public $CodigoPortal;
	public $NombrePortal;	
	public $MensajeBienvenida;
	public $MensajeSeleccion;
	public $Turneros = Array();
	public $MensajeError;
	private $wbasedato;
	private $wemp_pmla;
	private $conex;
	
	public function __construct($codigoPortal,$wemp_pmla,$conex)
	{
		$this->CodigoPortal = $codigoPortal;
		$this->wemp_pmla = $wemp_pmla;
		$this->Error=true;
		$this->MensajeError="";				
		$this->conex = $conex;		
		$this->wbasedato = consultarAliasPorAplicacion($this->conex, $this->wemp_pmla, 'cliame');			
	}	
	
	public function CargarConfiguracion()
	{
					

		
		// --> Obtener turneros asociados al portal
		$sqlTurneros = "SELECT porcod,portur FROM ".$this->wbasedato."_000357 WHERE porcod = '".$this->CodigoPortal."' AND porest= 'on' ORDER BY porord";
		
		
		$resTurneros = mysql_query($sqlTurneros, $this->conex) or die("<b>ERROR EN QUERY MATRIX(sqlTurneros):</b><br>".mysql_error());
		while($resTurnero = mysql_fetch_array($resTurneros))
		{

			$objTurnero = new Turnero($resTurnero['portur'],$this->wemp_pmla,$this->conex);
			$objTurnero->CargarConfiguracion();	
			$this->Turneros[]= $objTurnero;			
			
		}

		// --> Obtener la configuraion del portal
		$sqlConfigPortal = " SELECT pornom,pormbv,pormsl
						   FROM ".$this->wbasedato."_000356
						  WHERE porcod 	= '".$this->CodigoPortal."'
							AND porest 		= 'on'
						
		";
		$resConfigPortal = mysql_query($sqlConfigPortal, $this->conex) or die("<b>ERROR EN QUERY MATRIX(sqlConfigPortal):</b><br>".mysql_error());
		if($rowConfigPortal = mysql_fetch_array($resConfigPortal))
		{

			 $this->NombrePortal = utf8_encode(rplcSpecialChar($rowConfigPortal['pornom']));
			 $this->MensajeBienvenida = utf8_encode(rplcSpecialChar($rowConfigPortal['pormbv']));
			 $this->MensajeSeleccion = utf8_encode(rplcSpecialChar($rowConfigPortal['pormsl']));
	
		}		

		$this->Error = false;
		$this->MensajeError = "";

	}

	  
}

?>