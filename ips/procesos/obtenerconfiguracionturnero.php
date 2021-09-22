<?php

//script con los tipos de datos
include_once("conex.php");
include("root/comun.php");
ob_end_clean();


if ($_SERVER['REQUEST_METHOD'] == 'GET')
{

	$codigoTurnero = $_GET['codigoTurnero'];
	$wemp_pmla = $_GET['wemp_pmla'];
	
	$objTurnero = new Turnero($codigoTurnero,$wemp_pmla);
	$objTurnero->CargarConfiguracion();

	header("HTTP/1.1 200 OK");
	echo json_encode($objTurnero);
	exit();

}

//En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");


class Turnero {
	public $CodigoTurnero;
	public $NombreTurnero;	
	public $TieneLectorCedula;
	public $TieneIngresoManual;
	public $TieneCategorias;
	public $TienePrioridad;
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
	public $MensajeSinNombre;
	public $MensajeSinEdad;
	public $MensajeSinTipoEdad;
	public $MensajeSinCategoria;	
	public $TiposDocumento = Array();
	public $Categorias = Array();
	public $Prioridades = Array();	
	public $Error;
	public $MensajeError;
	private $wbasedato;
	private $conex;	

	public function __construct($codigoTurnero,$wemp_pmla)
	{

		$this->CodigoTurnero = $codigoTurnero;
		$this->Error=true;
		$this->MensajeError="";
		$this->conex = obtenerConexionBD("matrix");		
		$this->wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');			
		
	}	
	
	public function CargarConfiguracion()
	{
		
		
		
		// --> Obtener maestro de tipos de documento
		$sqlTipDoc = "SELECT Codigo, Descripcion
					FROM root_000007
				   WHERE Codigo IN('CC', 'TI', 'RC', 'NU', 'CE', 'PA')
		";
		$resTipDoc = mysql_query($sqlTipDoc, $this->conex) or die("<b>ERROR EN QUERY MATRIX(sqlTipDoc):</b><br>".mysql_error());
		while($rowTipDoc = mysql_fetch_array($resTipDoc))
		{
			$objTipoDocumento = new TipoDocumento();
			$objTipoDocumento->Codigo = $rowTipDoc['Codigo'];
			$objTipoDocumento->Nombre = $rowTipDoc['Descripcion'];
			$this->TiposDocumento[]= $objTipoDocumento;
			
		}

		
		// --> Obtener categorias asiciadas al turnero
		$sqlCategorias = "SELECT Sercod, Sernom, Serest FROM ".$this->wbasedato."_000298 WHERE Sertem = '".$this->CodigoTurnero."' AND Serest= 'on' ORDER BY Serord";
		
		
		$resCategorias = mysql_query($sqlCategorias, $this->conex) or die("<b>ERROR EN QUERY MATRIX(sqlCategorias):</b><br>".mysql_error());
		while($rowCategoria = mysql_fetch_array($resCategorias))
		{
			$objCategoria = new Categoria();
			$objCategoria->Codigo = $rowCategoria['Sercod'];
			$objCategoria->Nombre = $rowCategoria['Sernom'];
			$this->Categorias[]= $objCategoria;
			
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
			$objPrioridad->Nombre = $rowPrioridad['Connom'];
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

			 $this->NombreTurnero = $rowConfigTurnero['Codnom'];
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
			
			 $this->MensajeBienvenida = $rowConfigTurnero['Codmbv'];
			 $this->MensajeLector = $rowConfigTurnero['Codmlc'];
			 $this->MensajeIngresoManual = $rowConfigTurnero['Codmim'];
			 $this->MensajeTiposDocumento = $rowConfigTurnero['Codmtd'];
			 $this->MensajeDatosPersonales = $rowConfigTurnero['Codmdp'];
			 $this->MensajeCategorias = $rowConfigTurnero['Codmct'];
			 $this->MensajeTurnoGenerado = $rowConfigTurnero['Codmtg'];
			 $this->MensajeSinTipoDocumento = $rowConfigTurnero['Codmst'];
			 $this->MensajeSinNumeroDocumento = $rowConfigTurnero['Codmsn'];
			 $this->MensajeSinNombre = $rowConfigTurnero['Codsno'];
			 $this->MensajeSinEdad = $rowConfigTurnero['Codmse'];
			 $this->MensajeSinTipoEdad = $rowConfigTurnero['Codste'];
			 $this->MensajeSinCategoria = $rowConfigTurnero['Codmsc'];			
		}		

		$this->Error = false;
		$this->MensajeError = "";


	}



	  
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

}


class Prioridad 
{
	public $Nombre;
	public $Codigo;
	public $EsPrioridad;
	public $Icono;
	public $Color='white';

}

?>