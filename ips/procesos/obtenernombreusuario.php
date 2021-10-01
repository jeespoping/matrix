<?php

//script con los tipos de datos
include_once("conex.php");


if ($_SERVER['REQUEST_METHOD'] == 'GET')
{

	$identificacion = $_GET['numeroIdentificacion'];
	$tipoDocumento = $_GET['tipoDocumento'];
	
	$objUsuario = new Usuario2($identificacion,$tipoDocumento);
	$objUsuario->ObtenerNombre();
	header("HTTP/1.1 200 OK");
	echo json_encode($objUsuario);

	exit();

}

//En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");



class Usuario2 {
	public $TipoIdentificacion;
	public $NumeroIdentificacion;
	public $Nombre;
	public $Edad;
	public $Error;


	public function __construct($numeroIdentificacion,$tipoIdentificacion)
	{
		$this->TipoIdentificacion = $tipoIdentificacion;
		$this->NumeroIdentificacion = $numeroIdentificacion;
		
	}	
	
	public function ObtenerNombre()
	{
	
	
		$sqlNomPac = "
		SELECT CONCAT(Pacno1, ' ', Pacno2, ' ', Pacap1, ' ', Pacap2, ' ') AS nombrePaciente, pacnac
		  FROM root_000036
		 WHERE Pacced = '".$this->NumeroIdentificacion."'
		   AND Pactid = '".$this->TipoIdentificacion."'
		";

		$resNomPac = mysql_query($sqlNomPac, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlNomPac):</b><br>".mysql_error());
		if($rowNomPac = mysql_fetch_array($resNomPac))
		{
			$diff = abs(strtotime(date("Y-m-d")) - strtotime($rowNomPac['pacnac']));

			$years = floor($diff / (365*60*60*24));
			
			//$diff = date_diff($rowNomPac['pacnac'], date(), true);
			$this->Nombre = $rowNomPac['nombrePaciente'];
			$this->Edad = $years;
			
			
		}


	}
	
  
}

?>