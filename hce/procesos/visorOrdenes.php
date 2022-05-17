<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA          
//=========================================================================================================================================\\
//DESCRIPCION:			Programa que permite visualizar los resultados de los examenes prescritos desde ordenes, el tipo de 
// 						orden debe tener el campo Tipres activo en hce_000015
//AUTOR:				Jessica Madrid Mejía
//FECHA DE CREACION: 	2019-12-17
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES   
//--------------------------------------------------------------------------------------------------------------------------------------------  
//  2022-05-16  -   Sebastian Alvarez Barona: Se realiza modificación para que se puedan consultar los resultados de patologia.                                                                                                                     \\
//	2020-05-12	-	Edwin Molina Grisales:	- Si las ordenes están en proceso no se muestra el boton de Ver pdf
//	2020-03-17	-	Jessica Madrid Mejía:	- Si las ordenes de laboratorio están en proceso no se muestran los botones Ver resultado
// 											  y Ver PDF.
//	2020-01-16	-	Jessica Madrid Mejía:	- Modificaciones varias, aún en desarrollo.
//	2019-12-17	-	Jessica Madrid Mejía:	- Se modifican algunos estilos en la información del paciente
//--------------------------------------------------------------------------------------------------------------------------------------------


//--------------------------------------------------------------------------------------------------------------------------------------------                                        
// 	EJECUCION DEL SCRIPT
//--------------------------------------------------------------------------------------------------------------------------------------------

if(!isset($_SESSION['user']))
{
    echo '  <div style="color: #676767;font-family: verdana;background-color: #E4E4E4;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}
else
{
	$user_session = explode('-',$_SESSION['user']);
	$wuse = $user_session[1];
	include_once("root/comun.php");
	include_once("hce/funcionesHCE.php");
	
	if(!isset($wemp_pmla))
	{
		$wemp_pmla = $origen;	
	}
	
	if(!isset($wbasedatoHCE))
	{
		$wbasedatoHCE = $empresa;
	}
	
	if(!isset($wbasedatoMovhos))
	{
		$wbasedatoMovhos = $wdbmhos;	
	}
	
	if(!isset($historia))
	{
		$historia = $whis;	
	}
	
	if(!isset($ingreso))
	{
		$ingreso = $wing;	
	}
	
	$wfecha=date("Y-m-d");   
    $whora = date("H:i:s");

//=====================================================================================================================================================================     
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================
	
	function consultarInformacionPersonal($conex, $tipoDocumento, $documento)
	{
		$queryInfoPersonal = "SELECT CONCAT_WS(' ',Pacno1,Pacno2,Pacap1,Pacap2) AS Nombre, Pacnac, Pacsex
								FROM root_000036 
							   WHERE Pacced='".$documento."'
								 AND Pactid='".$tipoDocumento."';";
		
		$resInfoPersonal = mysql_query($queryInfoPersonal, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryInfoPersonal . " - " . mysql_error());
		$numInfoPersonal = mysql_num_rows($resInfoPersonal);
		
		$arrayInfoPersonal = array( 'nombre' => '',
									'edad'   => '',
									'sexo'   => '');
		if($numInfoPersonal>0)
		{
			$rowInfoPersonal = mysql_fetch_array($resInfoPersonal);
			
			$sexo="MASCULINO";
			if($rowInfoPersonal['Pacsex'] == "F")
			{
				$sexo="FEMENINO";
			}
			
			$edad = calcularEdadPaciente($rowInfoPersonal['Pacnac']);
			$arrayEdad = explode(" ", $edad);
			
			$edadResumida = "";
			for($i=0; $i<count($arrayEdad);$i+=2)
			{
				$edadResumida .= $arrayEdad[$i].substr($arrayEdad[$i+1],0,1)." ";
			}
			
			$arrayInfoPersonal['nombre'] = $rowInfoPersonal['Nombre'];
			$arrayInfoPersonal['edad'] = $edadResumida;
			$arrayInfoPersonal['sexo'] = $sexo;
		}
		
		return $arrayInfoPersonal;
	}
	
	function consultarHabitacion($conex, $wbasedatoMovhos, $wbasedatoHCE, $historia, $ingreso)
	{
		$queryHabitacion  = " SELECT Habcpa
								FROM ".$wbasedatoHCE."_000022
						  INNER JOIN ".$wbasedatoMovhos."_000020 
								  ON Habcod=Mtrccu
							   WHERE Mtrhis='".$historia."' 
								 AND Mtring='".$ingreso."';";
		
		$resHabitacion = mysql_query($queryHabitacion, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryHabitacion . " - " . mysql_error());
		$numHabitacion = mysql_num_rows($resHabitacion);
		
		$habitacion = "";
		if($numHabitacion>0)
		{
			$rowHabitacion = mysql_fetch_array($resHabitacion);
			
			$habitacion = $rowHabitacion['Habcpa'];
		}
		
		return $habitacion;
	}
	
	function consultarInformacionPaciente($conex, $wbasedatoMovhos, $wbasedatoHCE, $historia, $ingreso)
	{
		$queryInfoPaciente = "SELECT Ubisac, Ubihac, Ubiald, Cconom, Ccourg, Ingnre, a.Fecha_data, Ubifad
								FROM ".$wbasedatoMovhos."_000018 a
						  INNER JOIN ".$wbasedatoMovhos."_000011
								  ON Ccocod=Ubisac
						  INNER JOIN ".$wbasedatoMovhos."_000016
								  ON Inghis=Ubihis
								 AND Inging=Ubiing
							   WHERE Ubihis='".$historia."' 
								 AND Ubiing='".$ingreso."';";
		
		$resInfoPaciente = mysql_query($queryInfoPaciente, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryInfoPaciente . " - " . mysql_error());
		$numInfoPaciente = mysql_num_rows($resInfoPaciente);
		
		$arrayInfoPaciente = array( 'cco' 		  => '',
									'habitacion'  => '',
									'responsable' => '');
		if($numInfoPaciente>0)
		{
			$rowInfoPaciente = mysql_fetch_array($resInfoPaciente);
			
			$arrayInfoPaciente['cco'] = $rowInfoPaciente['Cconom'];
			$arrayInfoPaciente['habitacion'] = $rowInfoPaciente['Ubihac'];
			if($rowInfoPaciente['Ubihac']=="" && $rowInfoPaciente['Ccourg']=="on")
			{
				$arrayInfoPaciente['habitacion'] = consultarHabitacion($conex, $wbasedatoMovhos, $wbasedatoHCE, $historia, $ingreso);
			}
			$arrayInfoPaciente['altaDefinitiva'] = $rowInfoPaciente['Ubiald'];
			$arrayInfoPaciente['responsable'] = $rowInfoPaciente['Ingnre'];
			$arrayInfoPaciente['ingreso'] = $rowInfoPaciente['Fecha_data'];
			$arrayInfoPaciente['egreso'] = $rowInfoPaciente['Ubifad'];
		}
		return $arrayInfoPaciente;
	}
	
	function consultarTipoOrdenesPaciente($conex, $wbasedatoHCE, $historia, $ingreso)
	{
		$queryTipoOrden = "SELECT Codigo, Descripcion, Tipbro  
							 FROM ".$wbasedatoHCE."_000015 
							WHERE Estado='on'
							  AND Tipres='on'
						 ORDER BY Descripcion;";
						 
		$resTipoOrden = mysql_query($queryTipoOrden, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryTipoOrden . " - " . mysql_error());
		$numTipoOrden = mysql_num_rows($resTipoOrden);
		
		$arrayTipoOrdenes = array();
		if($numTipoOrden>0)
		{
			while($rowTipoOrden = mysql_fetch_array($resTipoOrden))
			{
				$arrayTipoOrdenes[$rowTipoOrden['Codigo']]['descripcion'] = htmlentities(ucfirst(strtolower($rowTipoOrden['Descripcion'])));
				$arrayTipoOrdenes[$rowTipoOrden['Codigo']]['consultaOrdenes'] = htmlentities(ucfirst(strtolower($rowTipoOrden['Tipbro'])));
			}
		}
		
		return $arrayTipoOrdenes;
	}
	
	function consultarOrdenesPaciente($conex, $wbasedatoHCE, $historia, $ingreso, $tipoOrdenes)
	{
		$condicionTipoOrden = "";
		if($tipoOrdenes!="")
		{
			$condicionTipoOrden = "AND Ordtor IN ('".implode("','",$tipoOrdenes)."')";
		}
		
		$queryOrdenes = " SELECT hce47.Codigo, hce47.Descripcion
							FROM ".$wbasedatoHCE."_000027 AS hce27
					  INNER JOIN ".$wbasedatoHCE."_000015 AS hce15
							  ON hce15.Codigo=hce27.Ordtor
							 AND hce15.Tipres='on'
					  INNER JOIN ".$wbasedatoHCE."_000028 AS hce28
							  ON hce28.Dettor=hce27.Ordtor 
							 AND hce28.Detnro=hce27.Ordnro
							 AND hce28.Detest='on'
					  INNER JOIN ".$wbasedatoHCE."_000047 AS hce47
							  ON  hce47.Codigo=hce28.Detcod
						   WHERE hce27.Ordhis='".$historia."' 
							 AND hce27.Ording='".$ingreso."'
							 ".$condicionTipoOrden."
							 AND hce27.Ordest='on'
						GROUP BY Codigo

						   UNION						

						  SELECT hce17.Codigo, hce17.Descripcion
							FROM ".$wbasedatoHCE."_000027 AS hce27
					  INNER JOIN ".$wbasedatoHCE."_000015 AS hce15
							  ON hce15.Codigo=hce27.Ordtor
							 AND hce15.Tipres='on'
					  INNER JOIN ".$wbasedatoHCE."_000028 AS hce28
							  ON hce28.Dettor=hce27.Ordtor 
							 AND hce28.Detnro=hce27.Ordnro
							 AND hce28.Detest='on'
					  INNER JOIN ".$wbasedatoHCE."_000017 AS hce17
							  ON  hce17.Codigo=hce28.Detcod
							 AND hce17.Nuevo='on'
						   WHERE hce27.Ordhis='".$historia."' 
							 AND hce27.Ording='".$ingreso."'
							 ".$condicionTipoOrden."
							 AND hce27.Ordest='on'
						GROUP BY Codigo
						 
						ORDER BY Descripcion;";
							
		$resOrdenes = mysql_query($queryOrdenes, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryOrdenes . " - " . mysql_error());
		$numOrdenes = mysql_num_rows($resOrdenes);
		
		$arrayOrdenes = array();
		if($numOrdenes>0)
		{
			while($rowOrdenes = mysql_fetch_array($resOrdenes))
			{
				$rowOrdenes['Descripcion'] = utf8_decode($rowOrdenes['Descripcion']);
				$arrayOrdenes[$rowOrdenes['Codigo']] = htmlentities(ucfirst(strtolower($rowOrdenes['Descripcion'])));
			}
		}
		
		return $arrayOrdenes;					
	}
	
	function consultarEstadosOrdenes($conex, $wbasedatoMovhos)
	{
		$queryEstados = " SELECT Eexcod, Eexdes
							FROM ".$wbasedatoMovhos."_000045 
						   WHERE Eexest='on'
						ORDER BY Eexdes;";
							
		$resEstados = mysql_query($queryEstados, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryEstados . " - " . mysql_error());
		$numEstados = mysql_num_rows($resEstados);
		
		$arrayEstados = array();
		if($numEstados>0)
		{
			while($rowEstados = mysql_fetch_array($resEstados))
			{
				$arrayEstados[$rowEstados['Eexcod']] = htmlentities(ucfirst(strtolower($rowEstados['Eexdes'])));
			}
		}
		
		return $arrayEstados;					
	}
	function consultarEstadosGenerales($conex, $wbasedatoMovhos)
	{
		$queryEstados = " SELECT Eexcod,Eexpnd,Eexrea,Eexcan 
							FROM ".$wbasedatoMovhos."_000045 
						   WHERE (Eexpnd='on' OR Eexrea='on' OR Eexcan='on') 
							 AND Eexest='on';";
							
		$resEstados = mysql_query($queryEstados, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryEstados . " - " . mysql_error());
		$numEstados = mysql_num_rows($resEstados);
		
		$arrayEstados = array();
		if($numEstados>0)
		{
			while($rowEstados = mysql_fetch_array($resEstados))
			{
				$estado = "";
				if($rowEstados['Eexpnd']=="on")
				{
					$estado = "Pendiente";
				}
				else if($rowEstados['Eexrea']=="on")
				{
					$estado = "Realizado";
				}
				else if($rowEstados['Eexcan']=="on")
				{
					$estado = "Cancelado";
				}
				$arrayEstados[$rowEstados['Eexcod']] = $estado;
			}
		}
		
		return $arrayEstados;					
	}
	
	function consultarExamenes($wemp_pmla,$wbasedatoHCE,$wbasedatoMovhos,$historia,$ingreso,$fechaInicial,$fechaFinal,$tipoOrdenes,$ordenes,$estados)
	{
		global $conex;
		
		$condicionTipoOrden = "";
		if($tipoOrdenes!="")
		{
			$condicionTipoOrden = "AND hce_27.Ordtor IN ('".implode("','",$tipoOrdenes)."')";
		}
		
		$condicionOrdenes = "";
		if($ordenes!="")
		{
			$condicionOrdenes = "AND hce_28.Detcod IN ('".implode("','",$ordenes)."')";
		}
		
		$condicionEstados = "";
		if($estados!="")
		{
			$condicionEstados = "AND hce_28.Detesi IN ('".implode("','",$estados)."')";
		}
		
		$condicionIngreso = "";
		if($ingreso!="")
		{
			$condicionIngreso = "AND hce_27.Ording='".$ingreso."'";
		}
		
		$queryExamenes = "SELECT hce_27.Fecha_data, hce_27.Hora_data, hce_28.Dettor, hce_28.Detnro, hce_28.Detite, hce_28.Detcod, hce_28.Detesi, hce_28.Detfec, hce_28.Detjus, hce_28.Detusu, hce_27.Ordurl, hce_28.Deturl, hce_28.Deturp, hce_47.Descripcion, hce_47.Codcups, usuarios.Descripcion AS nombreUsuario, hce_15.Descripcion As tipoOrden
							FROM ".$wbasedatoHCE."_000027 AS hce_27
					  INNER JOIN ".$wbasedatoHCE."_000028 AS hce_28
							  ON hce_28.Dettor=hce_27.Ordtor
							 AND hce_28.Detnro=hce_27.Ordnro
							 ".$condicionOrdenes."
							 ".$condicionEstados."
							 AND hce_28.Detest='on'
					  INNER JOIN ".$wbasedatoHCE."_000047 AS hce_47
							  ON hce_47.Codigo=hce_28.Detcod
					  INNER JOIN ".$wbasedatoHCE."_000015 AS hce_15
							  ON hce_15.Codigo=hce_28.Dettor
							 AND hce_15.Tipres='on'
					  INNER JOIN usuarios
							  ON usuarios.Codigo=hce_28.Detusu
						   WHERE hce_27.Ordhis='".$historia."' 
							 ".$condicionIngreso."
							 ".$condicionTipoOrden."
							 AND hce_27.Ordest='on'
							 AND hce_27.Fecha_data BETWEEN '".$fechaInicial."' AND '".$fechaFinal."'

						   UNION

						  SELECT hce_27.Fecha_data, hce_27.Hora_data, hce_28.Dettor, hce_28.Detnro, hce_28.Detite, hce_28.Detcod, hce_28.Detesi, hce_28.Detfec, hce_28.Detjus, hce_28.Detusu, hce_27.Ordurl, hce_28.Deturl, hce_28.Deturp, hce_17.Descripcion, hce_17.Codcups, usuarios.Descripcion AS nombreUsuario, hce_15.Descripcion As tipoOrden
							FROM ".$wbasedatoHCE."_000027 AS hce_27
					  INNER JOIN ".$wbasedatoHCE."_000028 AS hce_28
							  ON hce_28.Dettor=hce_27.Ordtor
							 AND hce_28.Detnro=hce_27.Ordnro
							 ".$condicionOrdenes."
							 ".$condicionEstados."
							 AND hce_28.Detest='on'
					  INNER JOIN ".$wbasedatoHCE."_000017 AS hce_17
							  ON hce_17.Codigo=hce_28.Detcod
							 AND hce_17.Nuevo='on'
					  INNER JOIN ".$wbasedatoHCE."_000015 AS hce_15
							  ON hce_15.Codigo=hce_28.Dettor
							 AND hce_15.Tipres='on'
					  INNER JOIN usuarios
							  ON usuarios.Codigo=hce_28.Detusu
							WHERE hce_27.Ordhis='".$historia."' 
							 ".$condicionIngreso."
							 ".$condicionTipoOrden."
							 AND hce_27.Ordest='on'
							 AND hce_27.Fecha_data BETWEEN '".$fechaInicial."' AND '".$fechaFinal."' 
							 
						ORDER BY Fecha_data DESC, Hora_data DESC, tipoOrden, Descripcion;";
		// echo "<pre>".print_r($queryExamenes,true)."</pre>";					 
		$resExamenes = mysql_query($queryExamenes, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryExamenes . " - " . mysql_error());
		$numExamenes = mysql_num_rows($resExamenes);
		
		$arrayExamenes = array();
		if($numExamenes>0)
		{
			$arrayEstados = consultarEstadosOrdenes($conex, $wbasedatoMovhos);
			$arrayEstadosGenerales = consultarEstadosGenerales($conex, $wbasedatoMovhos);
			while($rowExamenes = mysql_fetch_array($resExamenes))
			{
				$urlResultado = "";
				if($rowExamenes['Deturl']!="" && $rowExamenes['Deturl']!="NO APLICA" && $rowExamenes['Deturl']!=".")
				{
					$urlResultado = $rowExamenes['Deturl'];
				}
				
				$urlLectura = "";
				if($rowExamenes['Deturp']!="" && $rowExamenes['Deturp']!="NO APLICA" && $rowExamenes['Deturp']!=".")
				{
					$urlLectura = $rowExamenes['Deturp'];
				}
				elseif($rowExamenes['Ordurl']!="" && $rowExamenes['Ordurl']!="NO APLICA" && $rowExamenes['Ordurl']!=".")
				{
					$urlLectura = $rowExamenes['Ordurl'];
				}	
				
				$rowExamenes['Descripcion'] = utf8_decode($rowExamenes['Descripcion']);
				
				$idExamen = $rowExamenes['Dettor']."-".$rowExamenes['Detnro']."-".$rowExamenes['Detite'];
				$arrayExamenes[$idExamen]['fechaRegistro'] = $rowExamenes['Fecha_data'];
				$arrayExamenes[$idExamen]['horaRegistro'] = $rowExamenes['Hora_data'];
				$arrayExamenes[$idExamen]['FechaHoraRegistro'] = strtotime($rowExamenes['Fecha_data']." ".$rowExamenes['Hora_data']);
				$arrayExamenes[$idExamen]['FechaHoraRango'] = strtotime($rowExamenes['Fecha_data']." ".$rowExamenes['Hora_data']." +1 day");
				$arrayExamenes[$idExamen]['tipoOrden'] = $rowExamenes['Dettor'];
				$arrayExamenes[$idExamen]['nroOrden'] = $rowExamenes['Detnro'];
				$arrayExamenes[$idExamen]['item'] = $rowExamenes['Detite'];
				$arrayExamenes[$idExamen]['codEstado'] = $rowExamenes['Detesi'];
				$arrayExamenes[$idExamen]['estado'] = htmlentities($arrayEstados[$rowExamenes['Detesi']]);
				$arrayExamenes[$idExamen]['estadoGeneral'] = $arrayEstadosGenerales[$rowExamenes['Detesi']];
				$arrayExamenes[$idExamen]['fechaProcedimiento'] = $rowExamenes['Detfec'];
				$arrayExamenes[$idExamen]['justificacion'] = htmlentities($rowExamenes['Detjus']);
				$arrayExamenes[$idExamen]['codUsuario'] = $rowExamenes['Detusu'];
				$arrayExamenes[$idExamen]['urlResultado'] = trim($urlResultado);
				$arrayExamenes[$idExamen]['urlLectura'] = trim($urlLectura);
				$arrayExamenes[$idExamen]['nombreUsuario'] = htmlentities($rowExamenes['nombreUsuario']);
				$arrayExamenes[$idExamen]['codProcedimiento'] = $rowExamenes['Detcod'];
				$arrayExamenes[$idExamen]['codCups'] = $rowExamenes['Codcups'];
				$arrayExamenes[$idExamen]['descProcedimiento'] = htmlentities($rowExamenes['Descripcion']);
				$arrayExamenes[$idExamen]['descTipoOrden'] = htmlentities($rowExamenes['tipoOrden']);
			}
		}
		// echo "<pre>".print_r($arrayExamenes,true)."</pre>";
		return $arrayExamenes;						 
	}
	
	function consultarDatosOrdenMatrix($wemp_pmla,$wbasedatoHCE,$historia,$ingreso,$numeroOrdenes)
	{
		global $conex;
		
		foreach($numeroOrdenes as $tipoOrden => $ordenes)
		{
			$listaNroOrdenes = implode("','",$ordenes);
			
			$queryExamenes = "SELECT Fecha_data, Hora_data, Ordtor, Ordnro 
								FROM ".$wbasedatoHCE."_000027 
							   WHERE Ordtor='".$tipoOrden."' 
								 AND Ordnro IN ('".$listaNroOrdenes."') 
								 AND Ordhis='".$historia."';";
								 
			// echo "<pre>".print_r($queryExamenes,true)."</pre>";					 
			$resExamenes = mysql_query($queryExamenes, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryExamenes . " - " . mysql_error());
			$numExamenes = mysql_num_rows($resExamenes);
			
			$arrayExamenes = array();
			if($numExamenes>0)
			{
				while($rowExamenes = mysql_fetch_array($resExamenes))
				{
					$arrayExamenes[$rowExamenes['Ordtor']][$rowExamenes['Ordnro']]['fechaRegistro'] = $rowExamenes['Fecha_data'];
					$arrayExamenes[$rowExamenes['Ordtor']][$rowExamenes['Ordnro']]['horaRegistro'] = $rowExamenes['Hora_data'];
				}
			}
		}
		
		return $arrayExamenes;						 
	}
//=======================================================================================================================================================	
//		F I N	 F U N C I O N E S	 P H P
//=======================================================================================================================================================	

//=======================================================================================================================================================
//		F I L T R O S  	 D E  	L L A M A D O S  	P O R  	J Q U E R Y  	O  	A J A X
//=======================================================================================================================================================
if(isset($accion)) 
{
	switch($accion)
	{
		case 'consultarInformacionPaciente':
		{	
			$data = consultarInformacionPaciente($conex, $wbasedatoMovhos, $wbasedatoHCE, $historia, $ingreso);
			echo json_encode($data);
			break;
			return;
		}
		case 'consultarInformacionPersonal':
		{	
			$data = consultarInformacionPersonal($conex, $wtipodoc, $wcedula);
			echo json_encode($data);
			break;
			return;
		}
		case 'consultarTipoOrdenesPaciente':
		{	
			$data = consultarTipoOrdenesPaciente($conex, $wbasedatoHCE, $historia, $ingreso);
			echo json_encode($data);
			break;
			return;
		}
		case 'consultarOrdenesPaciente':
		{	
			$data = consultarOrdenesPaciente($conex, $wbasedatoHCE, $historia, $ingreso, $tipoOrdenes);
			echo json_encode($data);
			break;
			return;
		}
		case 'consultarEstadosOrdenes':
		{	
			$data = consultarEstadosOrdenes($conex, $wbasedatoMovhos);
			echo json_encode($data);
			break;
			return;
		}
		case 'consultarExamenes':
		{	
			$data = consultarExamenes($wemp_pmla,$wbasedatoHCE,$wbasedatoMovhos,$historia,$ingreso,$fechaInicial,$fechaFinal,$tipoOrdenes,$ordenes,$estados);
			echo json_encode($data);
			break;
			return;
		}
		case 'consultarDatosOrdenMatrix':
		{	
			$data = consultarDatosOrdenMatrix($wemp_pmla,$wbasedatoHCE,$historia,$ingreso,$numeroOrdenes);
			echo json_encode($data);
			break;
			return;
		}
	}
}
//=======================================================================================================================================================
//		F I N   F I L T R O S   A J A X 
//=======================================================================================================================================================	


//=======================================================================================================================================================
//	I N I C I O   E J E C U C I O N   N O R M A L   D E L   P R O G R A M A
//=======================================================================================================================================================
else 	
{
	?>
	<html>
	<head>
	  <title>Visor de ordenes</title>
	  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	</head>
	
	<script src="../../../include/root/jquery.min.js"></script>
	<script src="../../../include/root/jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script>
	<link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.min.css" rel="stylesheet"/>
	<link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.theme.min.css" rel="stylesheet"/>
	<link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.structure.min.css" rel="stylesheet"/>
	
	
	<link rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.css" />
	<link rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.filter.css" />
	<link rel='stylesheet' href='../../../include/gentelella/vendors/font-awesome/css/font-awesome.min.css'>
	<script type="text/javascript" src="../../../include/root/jquery.multiselect.js"></script>
	<script type="text/javascript" src="../../../include/root/jquery.multiselect.filter.js"></script>
		

	<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" >
    <link rel="stylesheet" href="../../../include/gentelella/vendors/bootstrap/dist/css/bootstrap.min.css">

    <link rel="stylesheet" href="../../../include/gentelella/vendors/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../../include/gentelella/build/css/custom.css">
    
    <script   src="../../../include/gentelella/vendors/bootstrap/dist/js/bootstrap.min.js" ></script>
	

		
		
		<script src='../../../include/root/jquery.quicksearch.js' type='text/javascript'></script>
		
		
		<!-- Bootstrap -->
		<link href="../../../include/gentelella/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
		<!-- Font Awesome -->
		<link href="../../../include/gentelella/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
		
		<!-- Custom Theme Style -->
		<link href="../../../include/gentelella/build/css/custom.min.css" rel="stylesheet">
		
		<!-- Datatables -->
		<link href="../../../include/gentelella/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
		<link href="../../../include/gentelella/vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
		<link href="../../../include/gentelella/vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
		<link href="../../../include/gentelella/vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
		<link href="../../../include/gentelella/vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">

		<link rel="stylesheet" href="../../../include/root/bootstrap.min.css">

		<script src="../../../include/root/bootstrap.min.js"></script>
		
		<link type="text/css" href="../../../include/root/select2/select2.min.css" rel="stylesheet"/>
		<script type='text/javascript' src='../../../include/root/select2/select2.min.js'></script>
		
		
		
		
		<!-- jQuery
		<script src="../../../include/gentelella/vendors/jquery/dist/jquery.min.js"></script> -->
		<!-- Bootstrap -->
		<script src="../../../include/gentelella/vendors/bootstrap/dist/js/bootstrap.min.js"></script>

		<!-- Custom Theme Scripts -->
		<script src="../../../include/gentelella/build/js/custom.min.js"></script>
		
		<!-- Datatables -->
		<script src="../../../include/gentelella/vendors/datatables.net/js/jquery.dataTables.min.js"></script>
		<script src="../../../include/gentelella/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
		<script src="../../../include/gentelella/vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
		<script src="../../../include/gentelella/vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
		<script src="../../../include/gentelella/vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
		<script src="../../../include/gentelella/vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
		<script src="../../../include/gentelella/vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
		<script src="../../../include/gentelella/vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
		<script src="../../../include/gentelella/vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
		<script src="../../../include/gentelella/vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
		<script src="../../../include/gentelella/vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
		<script src="../../../include/gentelella/vendors/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
		<script src="../../../include/gentelella/vendors/jszip/dist/jszip.min.js"></script>
		<script src="../../../include/gentelella/vendors/pdfmake/build/pdfmake.min.js"></script>
		<script src="../../../include/gentelella/vendors/pdfmake/build/vfs_fonts.js"></script>
		
	<script type="text/javascript">
//=====================================================================================================================================================================     
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T 
//=====================================================================================================================================================================
		
	$.datepicker.regional['esp'] = {
		closeText: 'Cerrar',
		prevText: 'Antes',
		nextText: 'Despues',
		monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
		'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
		monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
		'Jul','Ago','Sep','Oct','Nov','Dic'],
		dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
		dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
		dayNamesMin: ['D','L','M','M','J','V','S'],
		weekHeader: 'Sem.',
		dateFormat: 'yy-mm-dd',
		yearSuffix: ''
	};
	$.datepicker.setDefaults($.datepicker.regional['esp']);	
	
	$(document).ready(function() {
		
		pintarInformacionPersonal();
		pintarFiltros();
		
		
		widthDiv = $("#divDatosDemograficos").width();
		$("#divFiltros").width(widthDiv);
	});
	
	function pintarInformacionPersonal()
	{
		$.ajax({
			url: "visorOrdenes.php",
			type: "POST",
			dataType: "json",
			data:{
				consultaAjax 	: '',
				accion			: 'consultarInformacionPersonal',
				wemp_pmla		: $('#wemp_pmla').val(),
				wtipodoc		: $('#wtipodoc').val(),
				wcedula			: $('#wcedula').val()
				},
				async: false,
				success:function(informacionPaciente) {
					
					$("#nombrePaciente").html(informacionPaciente.nombre);
					$("#documento").html($('#wtipodoc').val()+" "+$('#wcedula').val());
					$("#edadActual").html(informacionPaciente.edad);
					$("#sexo").html(informacionPaciente.sexo);
				}
		});
	}
	
	function pintarInformacionIngresoPaciente()
	{
		$.ajax({
			url: "visorOrdenes.php",
			type: "POST",
			dataType: "json",
			data:{
				consultaAjax 	: '',
				accion			: 'consultarInformacionPaciente',
				wemp_pmla		: $('#wemp_pmla').val(),
				wbasedatoMovhos	: $('#wbasedatoMovhos').val(),
				wbasedatoHCE	: $('#wbasedatoHCE').val(),
				historia		: $('#historia').val(),
				ingreso			: $('#ingreso').val()
				},
				async: false,
				success:function(informacionIngreso) {
					
					var labelFechas = "<span id='labelFechaIngreso'></span> <span id='labelFechaEgreso'></span>";
					
					var estadoPaciente = labelFechas+
										 "&nbsp;&nbsp;<span class='label label-danger' style='font-size:10pt;vertical-align:middle;'>EGRESADO</span>";
										
					
					if(informacionIngreso.altaDefinitiva!="on")
					{
						var estadoPaciente = labelFechas+
											 "&nbsp;&nbsp;<span class='label label-success' style='font-size:10pt;vertical-align:middle;'>ACTIVO</span>";
					}
					
					$("#habitacion").html("");
					if(informacionIngreso.habitacion!="")
					{
						$("#habitacion").html("Habitaci&oacute;n: <span id='labelHabitacion' class='label label-warning' >"+informacionIngreso.habitacion+"</span>");
					}

					
					$("#estadoPaciente").html(estadoPaciente);
					
					
					$("#labelFechaIngreso").html("<b>Ingres&oacute;:</b> "+informacionIngreso.ingreso);
					$("#labelFechaEgreso").html(" - <b>Egres&oacute;:</b> "+informacionIngreso.egreso);
					if(informacionIngreso.egreso=="0000-00-00")
					{
						$("#labelFechaEgreso").html("");
					}
					
					
					$("#infoPaciente").attr("data-content","<b>Servicio: </b>"+informacionIngreso.cco+"</span><br><b>Entidad: </b>"+informacionIngreso.responsable+"<br>");
					$('[data-toggle="popover"]').popover();
				}
		});
		
	}
	
	function inicializarDatepicker()
	{
		fechaActual = $("#fechaActual").val();
		$("#txtFechaInicial").val(fechaActual);
		$("#txtFechaFinal").val(fechaActual);
	
		$("#txtFechaInicial").datepicker({
		
			dateFormat: 'yy-mm-dd',
			buttonImageOnly: true,
			changeMonth: true,
			changeYear: true,
			// minDate: fechaIngreso,
			maxDate: fechaActual,
			onSelect: function(fechaInicial){
				
				$("#txtFechaFinal").datepicker("option", "minDate", fechaInicial);//bloquear que no esconjan en la fecha final una fecha antes de la del inicio
			}
		});
		
		$("#txtFechaFinal").datepicker({
		
			dateFormat: 'yy-mm-dd',
			buttonImageOnly: true,
			changeMonth: true,
			changeYear: true,
			// minDate: fechaIngreso,
			maxDate: fechaActual,
			onSelect: function(fechaFinal){
				
				$("#txtFechaInicial").datepicker("option", "maxDate", fechaFinal);//bloquear que no esconjan en la fecha final una fecha antes de la del inicio
			}
		});
	}
	
	function inicializarMultiselect()
	{
		$('#tipoOrdenes').multiselect({
			checkAllText : 'Todos',
			uncheckAllText : 'Ninguno',
			selectedText: "# de # seleccionados",
			beforeclose: function(event, ui) { 
			},
			menuHeight: 600,
        }).multiselectfilter();
		$("#tipoOrdenes").multiselect("refresh");
	}
	
	function pintarFiltros()
	{
		$("#divExamenes").hide();
		
		pintarInformacionIngresoPaciente();
		inicializarDatepicker();
		
		pintarTipoOrdenesPaciente();
		inicializarMultiselect();
		
		$("#tipoOrdenes").multiselect("uncheckAll");
		
		if($('#tipoOrdenes option').length==1)
		{
			$("#tipoOrdenes").multiselect("checkAll");
			consultarExamenes();
		}
	}
	
	function pintarTipoOrdenesPaciente()
	{
		$("#tipoOrdenes").html("");
		
		$.ajax({
			url: "visorOrdenes.php",
			type: "POST",
			dataType: "json",
			data:{
				consultaAjax 	: '',
				accion			: 'consultarTipoOrdenesPaciente',
				wemp_pmla		: $('#wemp_pmla').val(),
				wbasedatoHCE	: $('#wbasedatoHCE').val(),
				historia		: $('#historia').val(),
				ingreso			: $('#ingreso').val()
				},
				async: false,
				success:function(tiposOrdenes) {
					
					// si tiposOrdenes no tiene nada debe ocultar el div de filtros y mostrar el div de pacientes sin ordenes
					if(tiposOrdenes.length==0)
					{
						$("#divFiltros").hide();
						$("#divSinOrdenes").show();
					}
					else
					{
						$("#divSinOrdenes").hide();
						$("#divFiltros").show();
						
						for(tipoOrden in tiposOrdenes)
						{
							html = "<option value='"+tipoOrden+"' consultaOrdenes='"+tiposOrdenes[tipoOrden].consultaOrdenes+"'> "+tiposOrdenes[tipoOrden].descripcion+"</option>";
							
							$("#tipoOrdenes").append(html);
						}
						
					}
				}
		});
	}
	
	function pintarExamenes(tipoOrdenes, datosOrdenMatrix)
	{
		var listaTipoOrdenes = [];
		$("#tipoOrdenes option").each(function()
		{
			listaTipoOrdenes[$(this).val()] = $(this).html();
		});
		
		var tituloResultados = "";
		// fecha de inicio y fin son iguales a la actual debe mostrar el siguiente titulo
		if($("#txtFechaInicial").val()==$("#fechaActual").val() && $("#txtFechaFinal").val()==$("#fechaActual").val())
		{
			tituloResultados = "<h3><span class='label label-primary' style='text-align:center;'>ORDENADO HOY</span></h3>";
		}
		
		
		var estilosFila = "style='text-align:center;vertical-align:middle;font-size: 9pt;'";
		// "<br>"+
		var html = 	
					"<div class='col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>"+
						tituloResultados+
						"<table id='tablaResultados' class='table table-striped table-bordered'>"+
							"<thead>"+
								"<tr class='encabezadoTabla'>"+
									"<td "+estilosFila+">N&uacute;mero<br> de orden</td>"+
									"<td "+estilosFila+">Ordenado</td>"+
									"<td "+estilosFila+">Realizaci&oacute;n</td>"+
									"<td "+estilosFila+">Tipo de &oacute;rden</td>"+
									"<td "+estilosFila+">Estudio o procedimiento</td>"+
									"<td "+estilosFila+">Estado</td>"+
									"<td "+estilosFila+">Resultado</td>"+
								"</tr>"+
							"</thead>"+
							"<tbody>";
							
							fila_lista = "Fila1";
							for(tipoOrden in tipoOrdenes)
							{
								for(resultado in tipoOrdenes[tipoOrden])
								{
									if (fila_lista=='Fila1')
										fila_lista = "Fila2";
									else
										fila_lista = "Fila1";
									
									var claseEstado = "label-warning";
									if(tipoOrdenes[tipoOrden][resultado].estadoGeneral=="Realizado")
									{
										claseEstado = "label-success";
									}
									else if(tipoOrdenes[tipoOrden][resultado].estadoGeneral=="Cancelado")
									{
										claseEstado = "label-danger";
									}
									
									
									var verResultado = "";
									var verLectura = "";
									var verImpresion = "";
									if(tipoOrdenes[tipoOrden][resultado].estadoGeneral=="Realizado")
									{
										if(tipoOrdenes[tipoOrden][resultado].urlResultado!="")
										{
											verResultado = "<div class='btn-group'><button type='button' id='btnResultado' class='btn btnMatrix btn-xs' descripcionProcedimiento='"+tipoOrdenes[tipoOrden][resultado].descProcedimiento+"' onclick=verResultado(this,'"+tipoOrdenes[tipoOrden][resultado].urlResultado+"');><span class='fa fa-file-image-o' data-toggle='tooltip' data-html='true' data-placement='bottom' title='Ver imagen' style='cursor:pointer; font-size:10pt;'></span></button></div>";
										}
										
										if(tipoOrdenes[tipoOrden][resultado].urlLectura!="")
										{
											verLectura = "<div class='btn-group'><button type='button' id='btnLectura' class='btn btnMatrix btn-xs' descripcionProcedimiento='"+tipoOrdenes[tipoOrden][resultado].descProcedimiento+"' onclick=verResultado(this,'"+tipoOrdenes[tipoOrden][resultado].urlLectura+"');><span class='fa fa-file-text-o' data-toggle='tooltip' data-html='true' data-placement='bottom' title='Ver resultado' style='cursor:pointer; font-size:10pt;'></span></button></div>";
										}
										else if(tipoOrdenes[tipoOrden][resultado].url!="")
										{
											verLectura = "<div class='btn-group'><button type='button' id='btnLectura' class='btn btnMatrix btn-xs' descripcionProcedimiento='"+tipoOrdenes[tipoOrden][resultado].descProcedimiento+"' onclick=verGraficos(this,'"+tipoOrdenes[tipoOrden][resultado].url+"');><span class='fa fa-file-text-o' data-toggle='tooltip' data-html='true' data-placement='bottom' title='Ver resultado' style='cursor:pointer; font-size:10pt;'></span></button></div>";
										}
										
										if(tipoOrdenes[tipoOrden][resultado].urlImpresion!="")
										{
											verImpresion = "<div class='btn-group'><button type='button' id='btnImpresion' class='btn btnMatrix btn-xs' descripcionProcedimiento='"+tipoOrdenes[tipoOrden][resultado].descProcedimiento+"' onclick=verResultado(this,'"+tipoOrdenes[tipoOrden][resultado].urlImpresion+"');><span class='fa fa-print' data-toggle='tooltip' data-html='true' data-placement='bottom' title='Ver PDF' style='cursor:pointer; font-size:10pt;'></span></button></div>";
										}
									}
									else{
										if(tipoOrdenes[tipoOrden][resultado].urlLectura!="")
										{
											verLectura = "<div class='btn-group'><button type='button' id='btnLectura' class='btn btnMatrix btn-xs' descripcionProcedimiento='"+tipoOrdenes[tipoOrden][resultado].descProcedimiento+"' onclick=verResultado(this,'"+tipoOrdenes[tipoOrden][resultado].urlLectura+"');><span class='fa fa-file-text-o' data-toggle='tooltip' data-html='true' data-placement='bottom' title='Ver resultado' style='cursor:pointer; font-size:10pt;'></span></button></div>";
										}
										else if(tipoOrdenes[tipoOrden][resultado].url!="")
										{
											verLectura = "<div class='btn-group'><button type='button' id='btnLectura' class='btn btnMatrix btn-xs' descripcionProcedimiento='"+tipoOrdenes[tipoOrden][resultado].descProcedimiento+"' onclick=verGraficos(this,'"+tipoOrdenes[tipoOrden][resultado].url+"');><span class='fa fa-file-text-o' data-toggle='tooltip' data-html='true' data-placement='bottom' title='Ver resultado' style='cursor:pointer; font-size:10pt;'></span></button></div>";
										}
									}
									
									var textoTooltip = "";
									if(tipoOrdenes[tipoOrden][resultado].justificacion!="")
									{
										textoTooltip += "<b>Justificaci&oacute;n: </b>"+tipoOrdenes[tipoOrden][resultado].justificacion+"<br><br>";
										
									}
									textoTooltip += "<b>Ordenado por: </b>"+tipoOrdenes[tipoOrden][resultado].nombreUsuario;
									var info = "<span class='fa fa-info-circle pull-right' data-toggle='popover' data-placement='right' data-html='true' data-placement='bottom' data-content='"+textoTooltip+"' style='cursor:pointer; font-size:12pt;text-align:right;color:#828181;'></span>";
										
									if (datosOrdenMatrix[tipoOrden] !== undefined)
									{
										if (datosOrdenMatrix[tipoOrden][tipoOrdenes[tipoOrden][resultado].nroOrden] !== undefined)
										{
											tipoOrdenes[tipoOrden][resultado].fechaRegistro = datosOrdenMatrix[tipoOrden][tipoOrdenes[tipoOrden][resultado].nroOrden]['fechaRegistro'];
											tipoOrdenes[tipoOrden][resultado].horaRegistro = datosOrdenMatrix[tipoOrden][tipoOrdenes[tipoOrden][resultado].nroOrden]['horaRegistro'];
										}
									}
								
				html += 			"<tr class='find'>"+
										"<td class='"+fila_lista+"' style='text-align:center;vertical-align:middle;'>"+tipoOrdenes[tipoOrden][resultado].nroOrden+info+"</td>"+
										"<td class='"+fila_lista+"' style='text-align:center;vertical-align:middle;'>"+tipoOrdenes[tipoOrden][resultado].fechaRegistro+" "+tipoOrdenes[tipoOrden][resultado].horaRegistro+"</td>"+
										"<td class='"+fila_lista+"' style='text-align:center;vertical-align:middle;'>"+tipoOrdenes[tipoOrden][resultado].fechaProcedimiento+"</td>"+
										"<td class='"+fila_lista+"' style='vertical-align:middle;'>"+tipoOrdenes[tipoOrden][resultado].descTipoOrden+"</td>"+
										"<td class='"+fila_lista+"' style='vertical-align:middle;'>"+tipoOrdenes[tipoOrden][resultado].descProcedimiento+"</td>"+
										"<td class='"+fila_lista+"' style='text-align:center;vertical-align:middle;font-size:8.5pt;'><span class='label "+claseEstado+"'>"+tipoOrdenes[tipoOrden][resultado].estado+"</span></td>"+
										"<td class='"+fila_lista+"' style='text-align:center;vertical-align:middle;'>"+verResultado+"&nbsp;"+verLectura+"&nbsp;"+verImpresion+"</td>"+
									"</tr>";		
								}
							}
			html +=			"</tbody>"
						"</table>"+
					"</div>"+
					"<br>"+
					"<br>";
		
		return html;			  
		
	}
	
	async function consultarExamenes()
	{
		$("#divExamenes").html("");
		
		var filtrosSeleccionados = false;
		// si tiene los filtros seleccionados
		if($('#tipoOrdenes').val() != null)
		{
			filtrosSeleccionados = true;
		}
		
		if(filtrosSeleccionados)
		{
			$("#divMensajeEspere").modal("show");
			$("#divExamenes").show();
		
			var tipoOrdenes = $('#tipoOrdenes').val()
			
			var listaTipoOrdenes = {};
			$("#tipoOrdenes option").each(function()
			{
				listaTipoOrdenes[$(this).val()] = {
					'descripcion': $(this).html(),
					'consultaOrdenes': $(this).attr('consultaOrdenes')
				};
			});
			
			var examenes = {};
			var numeroOrdenes = {};
			for (tipoOrden in tipoOrdenes)
			{
				// Consultar examenes de acuerdo al tipo de orden
				if(listaTipoOrdenes[tipoOrdenes[tipoOrden]].consultaOrdenes=="On")
				{
					// Busca los procedimientos que fueron prescritos desde ordenes en hce_000027 y hce_000028
					objExamenes = consultarExamenesOrdenes(tipoOrdenes[tipoOrden]);
					
					if(Object.keys(objExamenes).length>0)
					{
						examenes[tipoOrdenes[tipoOrden]] = [];
						
						for(examen in objExamenes)
						{
							var detalleExamen = {};
							
							detalleExamen.tipoOrden = objExamenes[examen].tipoOrden;
							detalleExamen.nroOrden = objExamenes[examen].nroOrden;
							detalleExamen.fechaRegistro = objExamenes[examen].fechaRegistro;
							detalleExamen.horaRegistro = objExamenes[examen].horaRegistro;
							detalleExamen.fechaProcedimiento = objExamenes[examen].fechaProcedimiento;
							detalleExamen.descTipoOrden = listaTipoOrdenes[tipoOrdenes[tipoOrden]].descripcion;
							detalleExamen.descProcedimiento = objExamenes[examen].descProcedimiento;
							detalleExamen.estado = objExamenes[examen].estado;
							detalleExamen.urlResultado = objExamenes[examen].urlResultado;
							detalleExamen.urlLectura = objExamenes[examen].urlLectura;
							detalleExamen.url = "";
							detalleExamen.urlImpresion = "";
							detalleExamen.justificacion = objExamenes[examen].justificacion;
							detalleExamen.nombreUsuario = objExamenes[examen].nombreUsuario;
							detalleExamen.codCups = objExamenes[examen].codCups;
							detalleExamen.estadoGeneral = objExamenes[examen].estadoGeneral;
							
							examenes[tipoOrdenes[tipoOrden]].push(detalleExamen);
						}
					}
				}
				else
				{
					// Laboratorio
					if(tipoOrdenes[tipoOrden]=="A20" || tipoOrdenes[tipoOrden]=="A16")
					{
						// Debe consultar el API de laboratorio para obtener los exámenes realizados para el paciente en el rango de fechas
						
						var ipLaboratorio = $("#ipLaboratorio").val();
			
						let headers = {
						  Authorization: "fab383b9-9e42-4758-a91d-10ebbf6ed68f",
						  "Content-Type": "application/json"
						};
						
						// Se envía el documento, fecha inicial y ficha final. No se envía el tipo de documento ya que en el laboratorio
						// no se garantiza que el paciente tiene el tipo de documento correcto y por tal motivo no genera una consulta 
						// con todos los resultados
						var urlApi = "http://"+ipLaboratorio+"/api/lmla/v2/"+$('#wcedula').val()+"/"+$('#txtFechaInicial').val()+"/"+$('#txtFechaFinal').val();
						async function fetchURL() {
							const res = await fetch(
							  urlApi,
							  { headers }
							);
							
							return await res.json();
						}
						
						let resp = await fetchURL();
						
						if(resp.data!=false)
						{
							examenes[tipoOrdenes[tipoOrden]] = [];
							numeroOrdenes[tipoOrdenes[tipoOrden]] = [];
							objExamenes = resp.data;
							
							for(examen in objExamenes)
							{
								var detalleExamen = {};
								
								var estadoGeneral = "";
								if(objExamenes[examen].estado=="Finalizado")
								{
									estadoGeneral = "Realizado";
								}
								else if(objExamenes[examen].estado=="En proceso")
								{
									estadoGeneral = "Pendiente";
								}
								
								var nroOrden = objExamenes[examen].ordenCli;
								if(nroOrden=="")
								{
									nroOrden = objExamenes[examen].ordenLab;
								}
								
								detalleExamen.tipoOrden = tipoOrden;
								detalleExamen.nroOrden = nroOrden;
								detalleExamen.fechaRegistro = objExamenes[examen].fechaOrden; // Dato asignado temporalmente, se reemplaza con la fecha de la orden desde que se tenga el número de orden de la clínica
								detalleExamen.horaRegistro = objExamenes[examen].horaOrden; // Dato asignado temporalmente, se reemplaza con la hora de la orden desde que se tenga el número de orden de la clínica
								detalleExamen.fechaProcedimiento = objExamenes[examen].fechaOrden;
								detalleExamen.descTipoOrden = listaTipoOrdenes[tipoOrdenes[tipoOrden]].descripcion;
								detalleExamen.descProcedimiento = objExamenes[examen].descripcion;
								detalleExamen.estado = objExamenes[examen].estado;
								detalleExamen.urlResultado = "";
								detalleExamen.urlLectura = "";
								detalleExamen.url = objExamenes[examen].url;
								detalleExamen.urlImpresion = objExamenes[examen].url_pdf;
								detalleExamen.justificacion = objExamenes[examen].comentario;
								detalleExamen.nombreUsuario = objExamenes[examen].medico;
								detalleExamen.codCups = objExamenes[examen].cups;
								detalleExamen.estadoGeneral = estadoGeneral;
								
								// guardar en un array todos los numeros de orden para ir a buscar la fecha y hora en que se ordenó
								if ( numeroOrdenes[tipoOrdenes[tipoOrden]].indexOf(detalleExamen.nroOrden) == -1 ) {
									numeroOrdenes[tipoOrdenes[tipoOrden]].push(detalleExamen.nroOrden);
								}
								
								examenes[tipoOrdenes[tipoOrden]].push(detalleExamen);
							}
						}
					}
				}
			}
			
			$("#divMensajeEspere").modal("hide");
			
			if(Object.keys(examenes).length==0)
			{
				var mensajeResultados = "El paciente no tiene &oacute;rdenes con los par&aacute;metros de b&uacute;squeda seleccionados.";
				
				// fecha de inicio y fin son iguales a la actual debe mostrar el siguiente mensaje
				if($("#txtFechaInicial").val()==$("#fechaActual").val() && $("#txtFechaFinal").val()==$("#fechaActual").val())
				{
					mensajeResultados = "El paciente no tiene &oacute;rdenes prescritas el d&iacute;a de HOY.";
				}
				
				var html = "<span class='col-md-12 col-sm-12 col-xs-12 alert alert-danger' style='font-weight:bold;text-align:center;'>"+mensajeResultados+"</span>"
				$("#divExamenes").append(html);
			}
			else
			{
				datosOrdenMatrix = consultarDatosOrdenMatrix(numeroOrdenes);
				
				var html = pintarExamenes(examenes, datosOrdenMatrix);
				
				$("#divExamenes").append(html);
				$('#tablaResultados').DataTable({
					"paging":   false,
					"ordering": true,
					"info":     false,
					"language": {
						"sProcessing":     "Procesando...",
						"sLengthMenu":     "Mostrar _MENU_ registros",
						"sZeroRecords":    "No se encontraron resultados",
						"sEmptyTable":     "Ningún dato disponible en esta tabla =(",
						"sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
						"sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
						"sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
						"sInfoPostFix":    "",
						"sSearch":         "Buscar:",
						"sUrl":            "",
						"sInfoThousands":  ",",
						"sLoadingRecords": "Cargando...",
						"oPaginate": {
							"sFirst":    "Primero",
							"sLast":     "Último",
							"sNext":     "Siguiente",
							"sPrevious": "Anterior"
						},
						"oAria": {
							"sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
							"sSortDescending": ": Activar para ordenar la columna de manera descendente"
						},
						"buttons": {
							"copy": "Copiar",
							"colvis": "Visibilidad"
						}
					},
					"order": [] 
					// "order": [[ 1, 'desc' ],[ 2, 'desc' ]]
				});
				$('[data-toggle="tooltip"]').tooltip({container: 'body'});
				$('[data-toggle="popover"]').popover();
			}
		}
		else
		{
			// Mostrar alerta
			$("#mensajeAlerta").html("Debe seleccionar al menos un tipo de orden");
			$("#divAlerta").modal("show");
		}
	}
	
	function consultarExamenesOrdenes(tipoOrdenes)
	{
		var arrayTipoOrdenes = [tipoOrdenes];
		var examenesOrdenes = {};
		$.ajax({
			url: "visorOrdenes.php",
			type: "POST",
			dataType: "json",
			data:{
				consultaAjax 	: '',
				accion			: 'consultarExamenes',
				wemp_pmla		: $('#wemp_pmla').val(),
				wbasedatoHCE	: $('#wbasedatoHCE').val(),
				wbasedatoMovhos	: $('#wbasedatoMovhos').val(),
				historia		: $('#historia').val(),
				ingreso			: '',
				fechaInicial	: $('#txtFechaInicial').val(),
				fechaFinal		: $('#txtFechaFinal').val(),
				tipoOrdenes		: arrayTipoOrdenes,
				ordenes			: '',
				estados			: ''
				},
				async: false,
				success:function(examenes) {
					examenesOrdenes = examenes;
				}
		});
		
		return examenesOrdenes;		
	}
	
	function consultarDatosOrdenMatrix(numeroOrdenes)
	{
		var datosOrdenesMatrix = {};
		
		if(Object.keys(numeroOrdenes).length>0)
		{
			$.ajax({
				url: "visorOrdenes.php",
				type: "POST",
				dataType: "json",
				data:{
					consultaAjax 	: '',
					accion			: 'consultarDatosOrdenMatrix',
					wemp_pmla		: $('#wemp_pmla').val(),
					wbasedatoHCE	: $('#wbasedatoHCE').val(),
					historia		: $('#historia').val(),
					ingreso			: $('#ingreso').val(),
					numeroOrdenes	: numeroOrdenes
					},
					async: false,
					success:function(resp) {
						
						datosOrdenesMatrix = resp;
					}
			});
		}
		
		return datosOrdenesMatrix;
	}
	
	function iniciarCampos()
	{
		$("#divExamenes").hide();
		
		fechaActual = $("#fechaActual").val();
		$("#txtFechaInicial").val(fechaActual);
		$("#txtFechaFinal").val(fechaActual);
		
		$("#tipoOrdenes").multiselect("uncheckAll");
	}
	
	function verResultado(elmnt,urlResultado)
	{
		var ancho = "98%";
		var alto = "100%";
		html = "<object type='application/pdf' data='"+urlResultado+"' pdf#toolbar=1&amp;navpanes=0&amp;scrollbar=1  style='display: block;margin: 10px;width:"+ancho+";height:"+alto+";'>"+
					"<param name='src' value='"+urlResultado+"' pdf#toolbar=1&amp;navpanes=0&amp;scrollbar=1 />"+
					"<p style='text-align:center; width: 60%;'>"+
					"Adobe Reader no se encuentra o la versi&oacute;n no es compatible, utiliza el icono para ir a la p&aacute;gina de descarga <br />"+
						"<a href='http://get.adobe.com/es/reader/' onclick='this.target=\"_blank\">"+
						"<img src='../../images/medical/root/prohibido.gif' alt='Descargar Adobe Reader' width='32' height='32' style='border: none;' />"+
						"</a>"+
					"</p>"+
				"</object>";
		
		$("#divVerResultado").html(html);
		$("#tituloModalResultado").html($(elmnt).attr('descripcionProcedimiento'));
		$("#modalVistaPrevia").modal("show");
	}
	
	function verGraficos(elmnt,url)
	{
		var ancho = "98%";
		var alto = "100%";
		
		html = 	"<iframe src='"+url+"/"+$("#txtFechaFinal").val()+"' style='display: block;margin: 10px;width:"+ancho+";height:"+alto+";'>"+
				"</iframe>";
			
		$("#divVerGrafica").html(html);
		$("#tituloModalGraficas").html($(elmnt).attr('descripcionProcedimiento'));
		$("#modalGraficas").modal("show");
	}
	
	function cerrarVentana()
	{
		if($("#esIframe").val())
		{
			parent.$.unblockUI();  
		}
		else
		{
			top.close();		
		}
    }
	
//=======================================================================================================================================================	
//	F I N  F U N C I O N E S  J A V A S C R I P T 
//=======================================================================================================================================================	
	</script>
	

	
	
<!--=====================================================================================================================================================================     
	E S T I L O S 
=====================================================================================================================================================================-->
	<style type="text/css">
		body
		{
			width: auto;
			height: auto;
			background-color: #FFFFFF;
			color: #000000;
		}
		
		.ui-multiselect { 
            background:white; 
            color: gray; 
            font-weight: normal; 
            width:100%; 
			max-width: 100%;
			overflow-x:hidden; 
            border-radius: 1px;
        }
		.ui-state-hover, .ui-widget-content .ui-state-hover, .ui-widget-header .ui-state-hover, .ui-state-focus, .ui-widget-content .ui-state-focus, .ui-widget-header
		{
			font-weight: normal; 
		}
		.ui-corner-all
		{
			font-weight: normal; 
			// font-size: 9pt;
		}
		
		.accordion .panel-heading {
			background: #2A5DB0;
			padding: 13px;
			width: 100%;
			display: block;
			color: #FFFFFF;
		}
		
		.encabezadoTablaCentrado {
			text-align:center;
			vertical-align:middle;
			font-size: 8pt;
		}
		.fila1 {
			font-size: 8pt;
		}
		.fila2 {
			font-size: 8pt;
		}
		
		th
		{
			text-align:center;
			vertical-align:middle;
		}
		
		.panel-primary {
			border-color: #2A5DB0;
		}
		
		.panel-primary > .panel-heading {
			color: #fff;
			background-color: #2A5DB0;
			border-color: #2A5DB0;
		}
		
		.label-primary{
			background-color: #2A5DB0;
		}
		.btnMatrix{
			background-color: #2A5DB0;
			color: #FFFFFF;
		}
		
		.btnMatrix:hover {
			background-color: #234d90;
			color: #FFFFFF;
		}
		
		.modal-header {
			background-color: #2A5DB0;
			padding:1px;
			color:#FFF;
			border-bottom:2px dashed #2A5DB0;
			font-weight: bold;
		}
		
		.modal-Alerta {
			background-color: #2A5DB0;
			padding:16px 16px;
			color:#FFF;
			border-bottom:2px dashed #2A5DB0;
			font-weight: bold;
			font-size: 10pt;
		}
		.modalGraficas {
			// width: 95%;
			width: 75%;
			height: 70%;
		}
		#labelHabitacion {
			font-weight: bold;
			font-size: 15pt;
		}
	</style>
<!--=====================================================================================================================================================================     
	F I N   E S T I L O S 
=====================================================================================================================================================================-->



<!--=====================================================================================================================================================================     
	I N I C I O   B O D Y
=====================================================================================================================================================================-->	
	<body class="nav-md">
		<?php
		
		// -->	ENCABEZADO
		$wactualiz='17 de mayo de 2022';
		encabezado("VISOR DE RESULTADOS", $wactualiz, "HCE".$wemp_pmla);
		
		$historiaPantalla = consultarAliasPorAplicacion($conex, $wemp_pmla, 'historiaPantalla');
		$documentoPantalla = consultarAliasPorAplicacion($conex, $wemp_pmla, 'documentoPantalla');
		$ipLaboratorio = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ipLaboratorio');
		
		$esIframe = true;
		if(isset($ventanaNueva) && $ventanaNueva=="on")
		{
			$esIframe = false;
		}
		?>
		
		<input type='hidden' id='wemp_pmla' name='wemp_pmla' value='<?php echo $wemp_pmla ?>'>
		<input type='hidden' id='wbasedatoHCE' name='wbasedatoHCE' value='<?php echo $wbasedatoHCE ?>'>
		<input type='hidden' id='wbasedatoMovhos' name='wbasedatoMovhos' value='<?php echo $wbasedatoMovhos ?>'>
		<input type='hidden' id='ingresoActual' name='ingresoActual' value='<?php echo $ingreso ?>'>
		<input type='hidden' id='wcedula' name='wcedula' value='<?php echo $wcedula ?>'>
		<input type='hidden' id='wtipodoc' name='wtipodoc' value='<?php echo $wtipodoc ?>'>
		<input type='hidden' id='wservicio' name='wservicio' value='<?php echo $wservicio ?>'>
		<input type='hidden' id='fechaActual' name='fechaActual' value='<?php echo $wfecha; ?>'>
		<input type='hidden' id='ipLaboratorio' name='ipLaboratorio' value='<?php echo $ipLaboratorio; ?>'>
		<input type='hidden' id='esIframe' name='esIframe' value='<?php echo $esIframe; ?>'>
		
		<div class="container-fluid">
			
			<div class="col-lg-3 col-md-8 col-sm-8 col-xs-8 ">
				<div id="divDatosDemograficos" style="text-align:center;cursor:pointer;">
				
					<div class="panel panel-default" id="infoPaciente" data-toggle="popover" data-placement="top" data-html='true' data-content="">
						<div class="panel-body">
							<h4><span id='nombrePaciente'></span></h4>
							<h4><span id="habitacion"></span></h4>
							<h5>
								<form class="form-inline">
									<b><?php echo $historiaPantalla; ?>:</b>  
									<input id='historia' class='form-control' type='text' value='<?php echo $historia; ?>' style='height:32px;width:80px;' readOnly='readOnly'>
									-
									<input id='ingreso' class='form-control' type='text' value='<?php echo $ingreso; ?>' style='height:32px;width:50px;' readOnly='readOnly'>
								</form>
							</h5>
							<div class="form-group col-lg-5" class="control-label">
								<h6><b><?php echo $documentoPantalla; ?>: </b><br><span id='documento'></span></h6>
							</div>
							<div class="form-group col-lg-4" class="control-label">
								<h6><b>Edad actual: </b><br><span id='edadActual'></span></h6>
							</div>
							<div class="form-group col-lg-3" class="control-label">
								<h6><b>Sexo: </b><br><span id='sexo'></span></h6>
							</div>
							<h6 id="estadoPaciente" class="inline-block center-block" style='text-align:center;'></h6>
						</div>
						
					</div>
				</div>
				<div id="divFiltros" style="text-align:center;">
					<div class="panel panel-primary">
						
						<div class="panel-heading">Consultar Ordenes</div>
						<div class="panel-body">
							<form>
								<div class="form-group">
									<label for="labelTipoOrdenes" class="control-label">Tipo de orden</label>
									<select id="tipoOrdenes" class="form-control" multiple="multiple">
									</select>
								</div>
								<div class="form-group col-lg-6" class="control-label">
									<label for="labelFechaInicial" class="control-label">Fecha inicial</label>
									<input id='txtFechaInicial' class='form-control' type='text' value='<?php echo date("Y-m-d") ?>' fechaActual='<?php echo date("Y-m-d") ?>' style='height:32px' readOnly='readOnly'>
								</div>
								<div class="form-group col-lg-6">
									<label for="labelFechaFinal" class="control-label">Fecha final</label>
									<input id='txtFechaFinal' class='form-control' type='text' value='<?php echo date("Y-m-d") ?>' fechaActual='<?php echo date("Y-m-d") ?>'  style='height:32px' readOnly='readOnly'>
								</div>
							</form>
							
							<button type="button" id="btnConsultar" class="btn btnMatrix" onclick="consultarExamenes();">Consultar</button>
							<button type="button" id="btnIniciar" class="btn btn-default" onclick="iniciarCampos();">Iniciar</button>
							
						</div>
					
					</div>
				</div>
				
				<div id="divSinOrdenes" class="alert alert-danger" style="display:none;text-align:center;">
					<span style="font-weight:bold;">El paciente no tiene &oacute;rdenes prescritas.</span>
				</div>
				
				<div id="divFiltros" style="text-align:center;">
					<button type="button" id="btnCerrar" class="btn btnMatrix" onclick="cerrarVentana();">Cerrar</button>
				</div>
				
			</div>
			   
			<div id="divExamenes" class="col-md-9 col-sm-12 col-xs-12" style="display:none;">
				
				
			</div>

			
			<div id='divAlerta' class='modal fade bs-example-modal-sm' role='dialog'>
				<div class='modal-dialog modal-sm'>
					<div class='modal-content'>
						<div class='modal-Alerta'>ALERTA</div>
						<div class='modal-body' id='mensajeAlerta'></div>
						<div class='modal-footer'>
							<button type='button' class='btn btnMatrix' data-dismiss='modal'>Cerrar</button>
						</div>
					</div>
				</div>
			</div>
			
			
			<div id='modalVistaPrevia' class='modal fade bs-example-modal-lg' role='dialog'>
				<div class='modal-dialog modal-lg' style='display: block;'>
					<div class='modal-content'>
						<div class='modal-Alerta'>
							RESULTADOS - <span id='tituloModalResultado'></span> 
							<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#FFFFFF; font-weight:bold;">
							 &times;
							</button>
						</div>
						
						<div class='modal-body' id='divVerResultado' style='overflow: auto;'></div>
						<div class='modal-footer'>
							<button type='button' class='btn btnMatrix' data-dismiss='modal'>Cerrar</button>
						</div>
					</div>
				</div>
			</div>
			<div id='modalGraficas' class='modal fade bs-example-modal-lg' role='dialog'>
				<div class='modal-dialog modal-lg modalGraficas' style='display: block;'>
					<div class='modal-content'>
						<div class='modal-Alerta'>
							GRAFICAS DE RESULTADOS - <span id='tituloModalGraficas'></span>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#FFFFFF; font-weight:bold;">
							 &times;
							</button>
						</div>
						
						<div class='modal-body' id='divVerGrafica' style='overflow: auto;'></div>
						<div class='modal-footer'>
							<button type='button' class='btn btnMatrix' data-dismiss='modal'>Cerrar</button>
						</div>
					</div>
				</div>
			</div>

			<div id='divMensajeEspere' class='modal fade bs-example-modal-sm' role='dialog'>
			<div class='modal-dialog modal-sm' style='display: block;'>
				<div class='modal-content'>
					<div class='modal-body' id='mensajeEspere'><br><p align='center'><img src='../../images/medical/ajax-loader5.gif'/>&nbsp;&nbsp;&nbsp;Por favor espere un momento...</p><br></div>
				</div>
			</div>
		</div>
		</div>
		<!-- /page content -->

	</body>
<!--=====================================================================================================================================================================     
	F I N   B O D Y
=====================================================================================================================================================================-->	
	</HTML>
	<?php
//=======================================================================================================================================================
//	F I N  E J E C U C I O N   N O R M A L   
//=======================================================================================================================================================
}

}//Fin de session
?>
