<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA
//=========================================================================================================================================\\
//DESCRIPCION:
//AUTOR:				Felipe Alvarez
//FECHA DE CREACION:
//FUNCIONAMIENTO:  consulta principal que trae la estancia de un paciente. movimientos en la  tabla 17 con el campo Eyrtipo ='Recibo'
/*

//--------------------------------------------------------------------------------------------------------------------------------------------
//                  CAMBIOS PARA MIGRACION
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
	CODIGO	|	FECHA		|	AUTOR 	|	DESCRIPCION	
----------------------------------------------------------------------------------------------------------------------------------------------
	MIGRA_1	|	2019-02-14	|	Jerson	|	Se quita el odbc_close ya que cerraba la conexion y no dejaba ejecutar los querys de  mas abajo

	
	
*/
//--------------------------------------------------------------------------------------------------------------------------------------------
// ACTUALIZACIONES
// 20 de octubre 2016  Se adiciona la posibilidad de editar el campo excedente 
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
$wactualiz='';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
//
//--------------------------------------------------------------------------------------------------------------------------------------
// 	EJECUCION DEL SCRIPT
//--------------------------------------------------------------------------------------------------------------------------------------------

if(!isset($_SESSION['user']))
{
    echo ' <div style="color: #676767;font-family: verdana;background-color: #E4E4E4;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}
else
{
	$user_session = explode('-',$_SESSION['user']);
	$wuse = $user_session[1];
	

	

	include "../../gesapl/procesos/gestor_aplicaciones_config.php";
	include_once("../../gesapl/procesos/gesapl_funciones.php");
	include_once("root/comun.php");
	$conex = obtenerConexionBD("matrix");
	include_once("ips/funciones_facturacionERP.php");
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
	$wfecha=date("Y-m-d");
    $whora = date("H:i:s");

//=====================================================================================================================================================================
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================
	function seguimiento($seguir , $validacion)
	{
		if($validacion ==true)
		{	
			if (file_exists("seguimientopension.txt")) {
				unlink("seguimientopension.txt");
			}
		}
		$fp = fopen("seguimientopension.txt","a+");
		fwrite($fp, "[".date("Y-m-d H:i:s")."]".PHP_EOL.$seguir);
		fclose($fp);
	}
	
	function traer_detalle_estancia($whistoria, $wing, $datos_ingreso, $datos_egreso ,$vec_politicas,$wconcepto,$wtarifa,$wempresa,$wtipo_ingreso,$nejemplo,$wcambiodetipos,$wtipo_paciente,$fechaingreso_liquidacion_parcial,$horaingreso_liquidacion_parcial)
	{

		global $conex;
		global $wemp_pmla;
		global $wbasedato;
		global $wuse;
		$array_valores = array();

		//----------------------------------------------
		//--CARGA DE VARIBLES INICIALES
		//
		//-- se buscan parametro en la root_000051
		$wbasedato_movhos  = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
		$wconceptoestancia = consultarAliasPorAplicacion($conex, $wemp_pmla, 'concepto_estancia');

		// consulto el nombre del concepto y su clasificacion
		$q 	= "SELECT Grudes,Gruccf
				 FROM ".$wbasedato."_000200
				WHERE Grucod = '".$wconceptoestancia."' ";

		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$row = mysql_fetch_array($res);

		//------ wnconceptoestancia  variable  que contiene el nombre del concepto
		$wnconceptoestancia = $row['Grudes'];

		//----------------------------------------------------
		//----------------------------------------------------
		
		$movimientospaf = false; // esta variable nos dira si tiene movimientos paf
		$selectmovimientospaf = "SELECT Estfec , Estres
							   FROM ".$wbasedato."_000265 
							  WHERE Esthis ='".$whistoria."'
							    AND Esting ='".$wing."'
								AND Estest ='on'";
	
		$res_selectmovimientospaf	= mysql_query($selectmovimientospaf, $conex) or die("Error en el query: ".$selectmovimientospaf."<br>Tipo Error:".mysql_error());
		
		while($row_selectmovimientospaf = mysql_fetch_array($res_selectmovimientospaf))
		{
		
			$movimientospaf = true;
		}
		
		if (!$movimientospaf)
		{
			// consulta principal que trae la estancia de un paciente.
			// movimientos en la  tabla 17 con el campo Eyrtipo ='Recibo'
			$q=	" SELECT  Cconom,Ccocod, Eyrhde , ".$wbasedato_movhos."_000017.Fecha_data,".$wbasedato_movhos."_000017.Hora_data, Eyrthr , '' as responsable
					FROM ".$wbasedato_movhos."_000017, ".$wbasedato_movhos."_000011
				   WHERE eyrhis = '".$whistoria."'
					 AND eyring = '".$wing."'
					 AND eyrtip = 'Recibo'
					 AND eyrest = 'on'
					 AND Ccocod = Eyrsde
					 AND Ccocir != 'on'
				ORDER BY fecha_data ASC , hora_data ASC";

			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num = mysql_num_rows($res);
		
			$vector_pension = array();
			// recorro los datos  arrojados de la consulta y los organizo en un array
			$i=1;
			while($row = mysql_fetch_array($res))
			{
				
				$vector_pension[$i] = $row;
				$i++;
			}
		}
		else
		{	
		
			$q=	" SELECT  Cconom,Ccocod, Eyrhde , ".$wbasedato_movhos."_000017.Fecha_data,".$wbasedato_movhos."_000017.Hora_data, Eyrthr , '1' as prioridad , '' as responsable
					FROM ".$wbasedato_movhos."_000017, ".$wbasedato_movhos."_000011
				   WHERE eyrhis = '".$whistoria."'
					 AND eyring = '".$wing."'
					 AND eyrtip = 'Recibo'
					 AND eyrest = 'on'
					 AND Ccocod = Eyrsde
					 AND Ccocir != 'on'
				  UNION 
				  SELECT '' as Cconom, '' as Ccocod, '' as  Eyrhde, Estfec AS fecha_data, '12:00:00' AS hora_data, '' as Eyrthr, '2' AS prioridad ,  Estres as responsable
					FROM ".$wbasedato."_000265
					WHERE Esthis = '".$whistoria."' 
					  AND Esting = '".$wing."' 
					  AND Estest = 'on'
				 ORDER BY fecha_data ASC, prioridad , hora_data ASC";
				  
				$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$num = mysql_num_rows($res);
				
				$vector_pension = array();
				// recorro los datos  arrojados de la consulta y los organizo en un array
				$i=1;
				while($row = mysql_fetch_assoc($res))
				{
					if ($row['prioridad']==1)
						$vector_pension[$i] = $row;
					else
					{
						 $vector_pension[$i] = $row;
						 $vector_pension[$i]['Cconom'] =  $vector_pension[$i-1]['Cconom'];
						 $vector_pension[$i]['Ccocod'] =  $vector_pension[$i-1]['Ccocod'];
						 $vector_pension[$i]['Eyrhde'] =  $vector_pension[$i-1]['Eyrhde'];
						 $vector_pension[$i]['Eyrthr'] =  $vector_pension[$i-1]['Eyrthr'];
						 //$vector_pension[$i]['responsable'] = $vector_pension[$i-1]['responsable'];
					}
					$i++;
				}
				
		}
	
		//----------------------------------------------------------------------------

		//organizo el vector en una manera intermedia  para luego volver a organizarlo de la manera que se quiere
		$nuevo_vector_pension = array();
		foreach ($vector_pension as $clave => $valor)
		{
			$nuevo_vector_pension [$clave]['viejo']  = $valor;
			if( isset( $vector_pension[$clave+1])){
				$nuevo_vector_pension [$clave]['nuevo'] = $vector_pension[($clave + 1)];

			}

		}

		//-----------------------------------------------------------------------------------------
		// reorganizo el vector por motivos de  eficiencia a la hora de hacer el calculo de los dias
		$vec_pension  = array();
		$o=1;
		$vector_valhabitaciones = array();
		foreach ($nuevo_vector_pension as $clave => $valor)
		{

			$vec_tip_hab = array (); // vector para consultar el tipo de habitacion en todos los dias que el paciente estuvo en ella
			$aux_cambio_tipo = '';
			$cambio_tipo ='no';

			$vec_pension[$clave]['cco_ing'] 	= $nuevo_vector_pension[$clave]['viejo']['Cconom'];
			$vec_pension[$clave]['cod_cco_ing'] = $nuevo_vector_pension[$clave]['viejo']['Ccocod'];
			$vec_pension[$clave]['hab_ing'] 	= $nuevo_vector_pension[$clave]['viejo']['Eyrhde'];
			$vec_pension[$clave]['fec_ing'] 	= $nuevo_vector_pension[$clave]['viejo']['Fecha_data'];
			$vec_pension[$clave]['hor_ing'] 	= $nuevo_vector_pension[$clave]['viejo']['Hora_data'];
			$vec_pension[$clave]['cco_egr'] 	= $nuevo_vector_pension[$clave]['nuevo']['Cconom'];
			$vec_pension[$clave]['cod_cco_egr'] = $nuevo_vector_pension[$clave]['nuevo']['Ccocod'];
			$vec_pension[$clave]['hab_egr'] 	= $nuevo_vector_pension[$clave]['nuevo']['Eyrhde'];
			$vec_pension[$clave]['hab_tipo']	= $nuevo_vector_pension[$clave]['viejo']['Eyrthr'] ;
			$vec_pension[$clave]['responsable']	= $nuevo_vector_pension[$clave]['viejo']['responsable'] ;
			
	
			if($fechaingreso_liquidacion_parcial > $vec_pension[$clave]['fec_ing'])
			{
					$vec_pension[$clave]['fec_ing'] 	= $fechaingreso_liquidacion_parcial;
					$vec_pension[$clave]['hor_ing']  	= $horaingreso_liquidacion_parcial;
			}
			

			$aux_cambio_tipo= $nuevo_vector_pension[$clave]['viejo']['Eyrthr'] ;


			// En el ultimo servicio no se encuetra hora de salida, cuando pasa esto la hora de salida de el
			// servicio se asocia a la fecha de alta.
			if($nuevo_vector_pension[$clave]['nuevo']['Fecha_data'] == '' || $nuevo_vector_pension[$clave]['nuevo']['Fecha_data'] == '0000-00-00')
			{
				$vec_pension[$clave]['fec_egr'] = $datos_egreso['dia'];
				$vec_pension[$clave]['hor_egr'] = $datos_egreso['hora'];
			}
			else
			{
				$vec_pension[$clave]['fec_egr'] = $nuevo_vector_pension[$clave]['nuevo']['Fecha_data'];
				$vec_pension[$clave]['hor_egr'] = $nuevo_vector_pension[$clave]['nuevo']['Hora_data'];
			}


				if($aux_cambio_tipo != $row['Eyrthr'])
					$cambio_tipo ="si";
					
					
					
					//echo "aaaaaaaaaaaa".$vec_pension[$clave]['fec_ing_original']."--".$vec_pension[$clave]['fec_ing'];
					$nuevovalor = datos_desde_procedimiento($vec_pension[$clave]['hab_tipo'], $wconcepto	, $vec_pension[$clave]['cod_cco_ing']	, $vec_pension[$clave]['cod_cco_ing'] 	,$wempresa	,$vec_pension[$clave]['fec_ing']								,$wtipo_ingreso, '*', 'on', false,'', $vec_pension[$clave]['fec_ing_original']	, date("H:i:s"));
					$valor_habitacion = $nuevovalor['wvaltar'];


					if(!$nuevovalor['error'])
					{
						$valor_habitacion = $nuevovalor['wvaltar'];
					}
					else
					{
						$valor_habitacion = 0 ;
						// $mensaje_error= "<img width='15' height='15' src='../../images/medical/root/info.png' title='".$nuevovalor['mensaje']."'>";
					}

				$vec_pension[$clave]['tipo_unico'] =  $vec_pension[$clave]['hab_tipo'];
				$vec_pension[$clave]['valor_habitacion_final'] = $valor_habitacion;

		}
		
		$j=1;
		$vec_pension_auxiliar  = array();
		foreach( $vec_pension  as $clave => $valor)
		{
			
			if($vec_pension[$clave]['fec_ing'] > $fechaingreso_liquidacion_parcial )
			{
				$vec_pension_auxiliar[$j]['cco_ing'] = 			$vec_pension[$clave]['cco_ing'];
				$vec_pension_auxiliar[$j]['cod_cco_ing'] =		$vec_pension[$clave]['cod_cco_ing']; 
				$vec_pension_auxiliar[$j]['hab_ing'] = 			$vec_pension[$clave]['hab_ing']; 
				$vec_pension_auxiliar[$j]['fec_ing'] = 			$vec_pension[$clave]['fec_ing']; 
				$vec_pension_auxiliar[$j]['hor_ing'] = 			$vec_pension[$clave]['hor_ing']; 
				$vec_pension_auxiliar[$j]['cco_egr'] = 			$vec_pension[$clave]['cco_egr'];
				$vec_pension_auxiliar[$j]['cod_cco_egr']= 		$vec_pension[$clave]['cod_cco_egr']; 
				$vec_pension_auxiliar[$j]['hab_egr'] = 			$vec_pension[$clave]['hab_egr'];
				$vec_pension_auxiliar[$j]['hab_tipo'] = 		$vec_pension[$clave]['hab_tipo']; 
				$vec_pension_auxiliar[$j]['fec_egr'] = 			$vec_pension[$clave]['fec_egr']; 
				$vec_pension_auxiliar[$j]['hor_egr'] = 			$vec_pension[$clave]['hor_egr']; 
				$vec_pension_auxiliar[$j]['tipo_unico'] = 		$vec_pension[$clave]['tipo_unico']; 
				$vec_pension_auxiliar[$j]['valor_habitacion_final'] = $vec_pension[$clave]['valor_habitacion_final']; 
				$vec_pension_auxiliar[$j]['responsable'] = $vec_pension[$clave]['responsable']; 
				$j++;
				
			}
			else
			{
				if($fechaingreso_liquidacion_parcial > $vec_pension[$clave]['fec_egr'])
				{
					
				}
				else
				{
					$vec_pension_auxiliar[$j]['cco_ing'] = 			$vec_pension[$clave]['cco_ing'];
					$vec_pension_auxiliar[$j]['cod_cco_ing'] =		$vec_pension[$clave]['cod_cco_ing']; 
					$vec_pension_auxiliar[$j]['hab_ing'] = 			$vec_pension[$clave]['hab_ing']; 
					$vec_pension_auxiliar[$j]['fec_ing'] = 			$fechaingreso_liquidacion_parcial; 
					$vec_pension_auxiliar[$j]['hor_ing'] = 			$vec_pension[$clave]['hor_ing']; 
					$vec_pension_auxiliar[$j]['cco_egr'] = 			$vec_pension[$clave]['cco_egr'];
					$vec_pension_auxiliar[$j]['cod_cco_egr']= 		$vec_pension[$clave]['cod_cco_egr']; 
					$vec_pension_auxiliar[$j]['hab_egr'] = 			$vec_pension[$clave]['hab_egr'];
					$vec_pension_auxiliar[$j]['hab_tipo'] = 		$vec_pension[$clave]['hab_tipo']; 
					$vec_pension_auxiliar[$j]['fec_egr'] = 			$vec_pension[$clave]['fec_egr']; 
					$vec_pension_auxiliar[$j]['hor_egr'] = 			$vec_pension[$clave]['hor_egr']; 
					$vec_pension_auxiliar[$j]['tipo_unico'] = 		$vec_pension[$clave]['tipo_unico']; 
					$vec_pension_auxiliar[$j]['valor_habitacion_final'] = $vec_pension[$clave]['valor_habitacion_final']; 
					$vec_pension_auxiliar[$j]['responsable'] = $vec_pension[$clave]['responsable']; 
					$j++;
				}
				
				
			}

		}
		
		$vec_pension = array();
		foreach( $vec_pension_auxiliar  as $clave => $valor)
		{
			
				$vec_pension[$clave]['cco_ing'] = 		$vec_pension_auxiliar[$clave]['cco_ing'];
				$vec_pension[$clave]['cod_cco_ing']= 	$vec_pension_auxiliar[$clave]['cod_cco_ing']; 
				$vec_pension[$clave]['hab_ing']= 		$vec_pension_auxiliar[$clave]['hab_ing']; 
				$vec_pension[$clave]['fec_ing']= 		$vec_pension_auxiliar[$clave]['fec_ing']; 
				$vec_pension[$clave]['hor_ing']= 		$vec_pension_auxiliar[$clave]['hor_ing']; 
				$vec_pension[$clave]['cco_egr']= 		$vec_pension_auxiliar[$clave]['cco_egr'];
				$vec_pension[$clave]['cod_cco_egr']= 	$vec_pension_auxiliar[$clave]['cod_cco_egr']; 
				$vec_pension[$clave]['hab_egr']= 		$vec_pension_auxiliar[$clave]['hab_egr'];
				$vec_pension[$clave]['hab_tipo']= 		$vec_pension_auxiliar[$clave]['hab_tipo']; 
				$vec_pension[$clave]['fec_egr']= 		$vec_pension_auxiliar[$clave]['fec_egr']; 
				$vec_pension[$clave]['hor_egr']= 		$vec_pension_auxiliar[$clave]['hor_egr']; 
				$vec_pension[$clave]['tipo_unico']= 	$vec_pension_auxiliar[$clave]['tipo_unico']; 
				$vec_pension[$clave]['valor_habitacion_final']= $vec_pension_auxiliar[$clave]['valor_habitacion_final']; 
				$vec_pension[$clave]['responsable']= $vec_pension_auxiliar[$clave]['responsable']; 
			
		}
		
		// Se aplica politica de tiempo minimo de estancia de estancia por dia
		foreach( $vec_pension  as $clave => $valor)
		{

			//----------------Se guarda las fechas originales
			$vec_pension[$clave]['fec_ing_original']=$vec_pension[$clave]['fec_ing'];
			$vec_pension[$clave]['hor_ing_original']=$vec_pension[$clave]['hor_ing'];
			$vec_pension[$clave]['fec_egr_original']=$vec_pension[$clave]['fec_egr'];
			$vec_pension[$clave]['hor_egr_original']=$vec_pension[$clave]['hor_egr'];


			if($vec_pension[$clave-1]['fec_egr'] == $vec_pension[$clave]['fec_ing'] )
			{

				$horas_transcurridas_ingreso = (strtotime($vec_pension[$clave]['fec_ing']." 23:59:59") - strtotime($vec_pension[$clave]['fec_ing']." ".$vec_pension[$clave]['hor_ing'])) / 3600 ;

				if( $vec_politicas ['tiempo_minimo'] > $horas_transcurridas_ingreso)
				{
					$vec_pension[$clave]['fec_ing'] =	date("Y-m-d", strtotime( $vec_pension[$clave]['fec_ing']." 00:00:00") + 24 * 3600);
					$vec_pension[$clave]['hor_ing'] =   "00:00:00";
				}

			}

		}

		$vec_pension_final = array(); // array que sera utilizado oficialmente para calcular los dias y para
									  // ser mostrado en pantalla en una tabla

		$vector_resp = array();
		$vector_resp['pension'] = $html;
		$vector_resp['html'] 	= $html;
		$vec_pension_final = calculardias($vec_pension,$whistoria, $wing,$vec_politicas, $wconcepto, $wtarifa,$wempresa ,$wtipo_ingreso);
		
		
		///----------
		if($movimientospaf == true)
		{
			foreach( $vec_pension_final  as $clave => $valor)
			{	
				if($vec_pension_final[$clave]['responsable'] !='')
					$array_vector_responsables_paf[$vec_pension_final[$clave]['dia_empieza_cobro']] = $vec_pension_final[$clave]['responsable'] ;
			}
		
		}
		
		//--El vector topes sera el encargado de tener los responsables y topes del paciente
		$vector_topes = array();
		
		// -->  Obtener el numero de orden del responsable actual, es decir si es el primero o el segunto... responsable.
		$q 	=  "SELECT  Emptar, Emptem, Empnom, Empnit,Empttp 
				  FROM ".$wbasedato."_000024
				 WHERE Empcod = '".$wempresa."' ";
		$res = mysql_query($q, $conex) or die("Error en el query: ".$q."<br>Tipo Error:".mysql_error());
	
		if($row = mysql_fetch_array($res))
		{
				$vector_topes[1]['codigo_responsable'] 	= $wempresa;
				if ($row['Emppar'] == '')
				{
					$row['Emppar'] ='off';
				}
				$vector_topes[1]['paralelo']		   	= $row['Emppar'];
				$vector_topes[1]['tarifa'] 			   	= $row['Emptar'];
				$vector_topes[1]['tipo_empresa']	   	= $row['Emptem'];
				$vector_topes[1]['nombre_responsable']	= $row['Empnom'];
				$vector_topes[1]['nit_responsable']		= $row['Empnit'];
				$vector_topes[1]['topeXempresa']		= $row['Empttp'];
				
		}
		
		//***************************************************************************************
		//-----------------------------------------------------------
		//---------------------------------llenado del vector topes
		$data 		= array();
		$valorCargo	= $wvaltarReco;
		// -->  Obtener el numero de orden del responsable actual, es decir si es el primero o el segunto... responsable.
		$qOrdRes = "SELECT Resord
					  FROM ".$wbasedato."_000205
					 WHERE Reshis = '".$whistoria."'
					   AND Resing = '".$wing."'
					   AND Resnit = '".$wempresa."'
					   AND Resest = 'on'
					   AND Resdes !='on'
		";
		$rOrdRes = mysql_query($qOrdRes, $conex) or die("Error en el query: ".$qOrdRes."<br>Tipo Error:".mysql_error());
		if($arr_OrdRes = mysql_fetch_array($rOrdRes))
		{

			$numOrdenRes = $arr_OrdRes['Resord'];
			// --> Obtener cual es el siguiente responsable y si este maneja paralelos.
			$qSigRes = "SELECT Resnit, Emppar, Emptar, Emptem, Empnom, Empnit,Empttp
						  FROM ".$wbasedato."_000205, ".$wbasedato."_000024
						 WHERE Reshis = '".$whistoria."'
						   AND Resing = '".$wing."'
						   AND Resord > ".$numOrdenRes."
						   AND Resest = 'on'
						   AND Resnit = Empcod
						   AND Empest = 'on'
						   AND Resdes !='on'
			";
			$rSigRes = mysql_query($qSigRes, $conex) or die("Error en el query: ".$qSigRes."<br>Tipo Error:".mysql_error());
			if($arr_SigRes = mysql_fetch_array($rSigRes))
			{
				$vector_topes[2]['codigo_responsable'] 	= $arr_SigRes['Resnit'];

				if ($arr_SigRes['Emppar'] == '')
				{
					$arr_SigRes['Emppar'] ='off';
				}
				$vector_topes[2]['paralelo']		   	= $arr_SigRes['Emppar'];
				$vector_topes[2]['tarifa'] 			   	= $arr_SigRes['Emptar'];
				$vector_topes[2]['tipo_empresa']	   	= $arr_SigRes['Emptem'];
				$vector_topes[2]['nombre_responsable']	= $arr_SigRes['Empnom'];
				$vector_topes[2]['nit_responsable']		= $arr_SigRes['Empnit'];
				$vector_topes[2]['topeXempresa']		= $arr_SigRes['Empttp'];

				// --> Si no hay un siguiente responsable, entonces el responsable sera el mismo paciente (Particular).
			}
			else
			{

			}
		}
		else
		{
		
		}
		
		//---------------------------------------
		//---------------------------------------
		//*******************************************************************************************

		//vector con tipo de  habitacion y sus respectivos nombres
		$vectorhabitacion = array();
		$q_tip_hab  = "SELECT Procod,Pronom "
					 ."  FROM  ".$wbasedato."_000103"
					 ." WHERE  Protip='H' ";
		$res_tip_hab = mysql_query($q_tip_hab,$conex) or die("Error en el query: ".$q_tip_hab."<br>Tipo Error:".mysql_error());
		$vectorhabitacion[''] =  'NO HAY HABITACION';
		while($row_tip_hab = mysql_fetch_array($res_tip_hab))
		{
			$vectorhabitacion[$row_tip_hab['Procod']] = $row_tip_hab['Pronom'];
		}
		
		//--> Cambio de tipos
		// si hay un cambio de tipo distinto al original se hace esto
		// Ejemplo el paciente entra por soat  y tiene poliza o prepagada , entonces pide una habitacion unipersonal
		// fisicamente esta en una habitacion unipersonal pero el soat no cubre esta   y  su tope soat puede ser alterado
		// por estar  en una habitacion de este tipo , debido a esto se le cambia el tipo de habitacion para soat por una bipersonal
		$tiposcambiados = array();
		if( $wcambiodetipos !=0)
		{

			$cambiartipos = explode("___", $wcambiodetipos);// se hace un explode al cambiodetipos

			for($r=0;$r<count($cambiartipos);$r++)
			{
				$auxtipos = explode(":", $cambiartipos[$r]);
				$tiposcambiados[$auxtipos[0]][$auxtipos[1]]=$auxtipos[2];
			}
		}
		//-----------------------------------------------------------

		// --> Obtener el codigo de la empresa particular
		// el codigo de la empresa particular se halla debido a su trato especial en  calculos de excedentes
		$qCodPart = " SELECT Detval
						FROM root_000051
					   WHERE Detemp = '".$wemp_pmla."'
						 AND Detapl = 'codigoempresaparticular' ";
		$rCodPart = mysql_query($qCodPart, $conex) or die("Error en el query: ".$qCodPart."<br>Tipo Error:".mysql_error());
		if($arr_CodPart = mysql_fetch_array($rCodPart))
			$empParticular = $arr_CodPart['Detval'];
		//-----------------------------------------------------
		//--- Se consulta si tiene liquidacion de estancia hecha por liquidacion de paquetes en cirugia
		$qEstanciaPaquete = " SELECT Tcarprocod , Tcarcan 
										FROM ".$wbasedato."_000106
									   WHERE Tcarhis ='".$whistoria."' 
									     AND Tcaring ='".$wing."' 
										 AND Tcartfa ='PAQUETE'
										 AND Tcarest ='on'
										 AND Tcarconcod = '".$wconceptoestancia."'";
										 
				
		$ResEstanciaPaquete = mysql_query($qEstanciaPaquete,$conex) or die("Error en el query: ".$qEstanciaPaquete."<br>Tipo Error:".mysql_error());
		$array_estancia_paquete = array();
		while($RowEstanciaPaquete = mysql_fetch_array($ResEstanciaPaquete))
		{
			$array_estancia_paquete[$RowEstanciaPaquete['Tcarprocod']] = $RowEstanciaPaquete['Tcarcan'];
		}
		//--------------
		
			
		
		//$html .= '<TEXTAREA COLS=20 ROWS=10 NAME="Texto">'.$ejemplo.'</TEXTAREA>';
		$html .= '<input type="hidden" id="numero_responsables" value="'.count($vector_topes).'">';

		// tabla principal, que contiene la info de la pension
		$html .= '<table id="tabla_pension">';
		$html .= '<tr class ="encabezadoTabla">
					<td align="center" rowspan="2">Hab</td>
					<td nowrap="nowrap" align="center">Fecha de Ingreso</td>
					<td nowrap="nowrap" align="center">Fecha Ini Cobro</td>
					<td nowrap="nowrap" style="display : none" align="Center" rowspan="2">Tercero</td>
					<td nowrap="nowrap"  align="center" rowspan="2">Dias</td>';
		// se imprime los encabezados para los responsables
		for($u=1; $u<=count($vector_topes);$u++)
		{
			$html.='<td align="center" colspan="2">'.$vector_topes[$u]['nombre_responsable'].'</td>';
		}

		//----------------------------------------
		$html.='<td rowspan="2" align ="center" >Excedente</td>';
		$html.=	'	<td align="center" rowspan="2">Total</td>
					<td style="display : none" align="center" rowspan="2">info</td>
					<td style="display : none" align="center" rowspan="2"></td>
				</tr>';
		$html .= '<tr class ="encabezadoTabla">
					<td align="center"> Fecha de Egreso</td>
					<td align="center"> Fecha Fin Cobro</td>';
		$vec_pol = array();
	
		for($u=1; $u<=count($vector_topes);$u++)
		{
			$auxl ='';
			// arreglo que contiene las politicas
			//----------------------------------------------------------------------------
			//--> Datos para traer politica
			$arr_var['wcodcon'] 			=$wconcepto;
			$arr_var['wprocod'] 			= "*";
			$arr_var['wtar'] 				= $vector_topes[$u]['tarifa'];
			$arr_var['wcodemp'] 			=$vector_topes[$u]['codigo_responsable'];
			$arr_var['wespecialdiad'] 		= "*";
			$arr_var['wccogra'] 			= "*";
			$arr_var['wdia_ingreso']		= $dia_ingreso;
			$arr_var['wdia_egreso'] 		= $dia_egreso;
			$arr_var['dia_inicio_cobro'] 	= "";
			$arr_var['dia_final_cobro'] 	= "";
			$arr_var['tipo_facturacion'] 	= "";
			$arr_var['horaespecifica'] 		= "";
			$arr_var['tipo_hab_esp']		= "";
			$arr_variables['tiempo_minimo']	= "";
			$arr_var['concepto_a']			="";
			$arr_var['proceso_a']			="";
			$arr_var['tipoEmpresa']			=$vector_topes[$u]['tipo_empresa'];
			$arr_var['esdepension']			="Es de pension";
			//----------------------------------------------------

			//trae las politicas - con los parametros que estan en $arr_variables
			ValidarGrabacion($arr_var, $CargosAnexos);

			$auxl =explode( '!!' , $arr_var['tipo_hab_esp'])  ;
			$auxe='';

			for($y=0;$y <(count($auxl)-1);$y++)
			{
				 $auxe = explode(':',$auxl[$y]);
				 $vec_pol [$vector_topes[$u]['codigo_responsable']][$auxe[0]]['tipo_hab_esp'] = $auxe[0];
				 $vec_pol [$vector_topes[$u]['codigo_responsable']][$auxe[0]]['minhoras_hab_esp'] = $auxe[1];
			}
			
			$vec_pol [$vector_topes[$u]['codigo_responsable']]['concepto_a'] = $arr_var['concepto_a'];
			$vec_pol [$vector_topes[$u]['codigo_responsable']]['proceso_a']  = $arr_var['proceso_a'];
			$vec_pol [$vector_topes[$u]['codigo_responsable']]['nproceso_a'] = $arr_var['nproceso_a'];
			$vec_pol [$vector_topes[$u]['codigo_responsable']]['nconcepto_a'] = $arr_var['nconcepto_a'];

			$html.='<td align="center">Habitacion</td><td >Total</td>';
		}
		
		$vec_pol_2 = array();
		for($uu=1; $uu<=count($vec_pension_final);$uu++)
		{
			for($u=1; $u<=count($vector_topes);$u++)
			{
				$auxl ='';
				// arreglo que contiene las politicas
				//----------------------------------------------------------------------------
				//--> Datos para traer politica
				$arr_var['wcodcon'] 			=$wconcepto;
				$arr_var['wprocod'] 			= $vec_pension_final[$uu]['hab_tipo'];
				$arr_var['wtar'] 				= $vector_topes[$u]['tarifa'];
				$arr_var['wcodemp'] 			= $vector_topes[$u]['codigo_responsable'];
				$arr_var['wespecialdiad'] 		= "*";
				$arr_var['wccogra'] 			= $vec_pension_final[$uu]['cod_cco_ing'];
				$arr_var['wdia_ingreso']		= $dia_ingreso;
				$arr_var['wdia_egreso'] 		= $dia_egreso;
				$arr_var['dia_inicio_cobro'] 	= "";
				$arr_var['dia_final_cobro'] 	= "";
				$arr_var['tipo_facturacion'] 	= "";
				$arr_var['horaespecifica'] 		= "";
				$arr_var['tipo_hab_esp']		= "";
				$arr_variables['tiempo_minimo']	= "";
				$arr_var['concepto_a']			="";
				$arr_var['proceso_a']			="";
				$arr_var['tipoEmpresa']			=$vector_topes[$u]['tipo_empresa'];
				$arr_var['esdepension']			="Es de pension";
				//----------------------------------------------------

				//trae las politicas - con los parametros que estan en $arr_variables
				ValidarGrabacion($arr_var, $CargosAnexos);

				$auxl =explode( '!!' , $arr_var['tipo_hab_esp'])  ;
				$auxe='';

				for($y=0;$y <(count($auxl)-1);$y++)
				{
					 $auxe = explode(':',$auxl[$y]);
					 $vec_pol_2 [$vector_topes[$u]['codigo_responsable']][$vec_pension_final[$uu]['cod_cco_ing']][$auxe[0]]['tipo_hab_esp'] = $auxe[0];
					 $vec_pol_2 [$vector_topes[$u]['codigo_responsable']][$vec_pension_final[$uu]['cod_cco_ing']][$auxe[0]]['minhoras_hab_esp'] = $auxe[1];
				}
				
				$vec_pol_2 [$vector_topes[$u]['codigo_responsable']][$vec_pension_final[$uu]['cod_cco_ing']]['concepto_a'] = $arr_var['concepto_a'];
				$vec_pol_2 [$vector_topes[$u]['codigo_responsable']][$vec_pension_final[$uu]['cod_cco_ing']]['proceso_a']  = $arr_var['proceso_a'];
				$vec_pol_2 [$vector_topes[$u]['codigo_responsable']][$vec_pension_final[$uu]['cod_cco_ing']]['nproceso_a'] = $arr_var['nproceso_a'];
				$vec_pol_2 [$vector_topes[$u]['codigo_responsable']][$vec_pension_final[$uu]['cod_cco_ing']]['nconcepto_a'] = $arr_var['nconcepto_a'];

			}
			
		}
		
		$html.='</tr>';
		
		//--------------------------------
		//------Varible que se habilita cuando un usuario tiene permiso para poner excedente
		$qExcedenteUse = " SELECT Cjegre
							 FROM ".$wbasedato."_000030
							WHERE Cjeusu = '".$wuse."'
							  AND Cjeest = 'on'";
		$ResExcedenteUse = mysql_query($qExcedenteUse,$conex) or die("Error en el query: ".$qExcedenteUse."<br>Tipo Error:".mysql_error());
		if($RowqExcedenteUse = mysql_fetch_array($ResExcedenteUse))
			$puedePonerExcedente = $RowqExcedenteUse['Cjegre'];
		else
			$puedePonerExcedente = 'off';
		
		if($puedePonerExcedente =='')
			$puedePonerExcedente = 'off';
		//------------------------------------------
		//------------------------------------------

		$i=0;
		$total_dias=0;
		$total_valor=0;
		$vector_saldos = array();
		$codigoResponsablepaf='';
	
		// IMPRESION
		// Impresion principal de los detalles
		// se empieza a imprimir los detalles principales de la liquidacion de pension
		foreach( $vec_pension_final  as $clave => $valor)
		{
			$ver_excedente = false;

			//-------
			if (($i%2)==0)
				$wcf="fila1";  // color de fondo de la fila
			else
				$wcf="fila2"; // color de fondo de la fila
			//------
			$i++;

			$tipo_unico = $vec_pension_final[$clave]['tipo_unico'];
			//----------------------------------------

			$html .='<tr id="tr_detalle_pension_inicio'.$clave.'"  class="trppal"  clave="'.$clave.'" >
						<td align="center" class="'.$wcf.'" rowspan="2" id="habitacion_'.$clave.'"  numero="'.$vec_pension_final[$clave]['hab_ing'].'"> '.$vec_pension_final[$clave]['hab_ing'].'</td>
						<td nowrap="nowrap" class="'.$wcf.'" id="tdfechainicialppal_'.$clave.'">'.$vec_pension_final[$clave]['fec_ing_original'].' / '.$vec_pension_final[$clave]['hor_ing_original'].'</td>
						<td class="'.$wcf.'" align="center" id="tdfechainiciocobroppal_'.$clave.'">'.$vec_pension_final[$clave]['dia_empieza_cobro'].'</td>
						<td style="display : none" id="tercero_'.$clave.'" class="'.$wcf.'  tienetercero"  clave="'.$clave.'" tipo_habitacion="'.$tipo_unico.'"  cod_cco="'.$vec_pension_final[$clave]['cod_cco_ing'].'" fecha_ini_cobro="'.$vec_pension_final[$clave]['dia_empieza_cobro'].'" fecha_fin_cobro="'.$vec_pension_final[$clave]['dia_fin_cobro'].'" rowspan="2"></td>
						<td class="'.$wcf.'" nowrap="nowrap" rowspan="2" id="tdnumerodiasppal_'.$clave.'">
						<input  id="input_dias_'.$clave.'"  style="text-align: center"  type="text" value="'.$vec_pension_final[$clave]['dias_cobro'].'"  size="3" onchange="recalcular_estancia('.$clave.')">
					</td>';

			// se saca el select de las habitaciones
			$q_tip_hab  = "SELECT Procod,Pronom  "
						 ."  FROM  ".$wbasedato."_000103"
						 ." WHERE  Protip='H' 
						 ORDER BY Procod ";
			$res_tip_hab = mysql_query($q_tip_hab,$conex) or die("Error en el query: ".$q_tip_hab."<br>Tipo Error:".mysql_error());
			//---------------------------------------

			// se construye el select para cambiar la habitacion
			// la variable $wcambiodetipos !=0  es la que determina si se establecio un cambio de tipo de habitacion
			// al recargar y hacer el recalculo de la pension se busca  $wcambiodetipos si existe  un cambio para ese responsable
			// en ese registro se cambia
			
			//-- El for funciona para los diferentes responsables donde u es el numero del responsable
			for($u=1; $u<=count($vector_topes);$u++)
			{
				$html.='<td class="'.$wcf.' tdselecthabitacion_'.$clave.'" id="resumen_responsable_'.$clave.'_'.$u.'" rowspan="2" id_responsable="'.$vector_topes[$u]['codigo_responsable'].'" n_responsable="'.$vector_topes[$u]['nombre_responsable'].'"  c_tarifa="'.$vector_topes[$u]['tarifa'].'"  c_tarifa="'.$vector_topes[$u]['tarifa'].'"  c_nitresponsable="'.$vector_topes[$u]['nit_responsable'].'"  t_responsable ="'.$vector_topes[$u]['tipo_empresa'].'">';
				if($u==1)
				{
					$htmloption	= $vec_pension_final[$clave]['faltantes']."<select   id='tipo_hab_facturacion_".$clave."_".$u."' style='font-size:8pt; width:120pt'  onchange='cambiar_tipo_habitacion_facturacion(\"".$clave."\",\"".$u."\")'  >";
				}
				else
				{
					$htmloption	="<select   id='tipo_hab_facturacion_".$clave."_".$u."'  style='font-size:8pt; width:120pt'   onchange='cambiar_tipo_habitacion_facturacion(\"".$clave."\",\"".$u."\")'  >";

				}
				$x=0;//-- Contador que sirve para controlar la primera vez que recorre el ciclo y no trae habitacion en el vector principal.
				while($row_tip_hab = mysql_fetch_array($res_tip_hab))
				{
					// cuando no hay cambio de tipo de habitacion seleccionado por el usuario grabador
					if($wcambiodetipos ==0 )
					{
						// si el tipo unico viene vacio y es la primera iteracion del ciclo
						if ($tipo_unico =='' && $x==0)
						{
							$htmloption   .="<option   selected value='' >NO HAY TIPO HABITACION</option>";
						}
						if($row_tip_hab['Procod'] == $tipo_unico)
							$htmloption   .="<option   selected value='".$row_tip_hab['Procod']."' >".$row_tip_hab['Procod']."-".$row_tip_hab['Pronom']."</option>";
						else
							$htmloption   .="<option   value='".$row_tip_hab['Procod']."' >".$row_tip_hab['Procod']."-".$row_tip_hab['Pronom']."</option>";
					}
					else
					{
						if(isset($tiposcambiados[$clave][$u]))
						{
							if($row_tip_hab['Procod'] == $tiposcambiados[$clave][$u])
								$htmloption   .="<option selected value='".$row_tip_hab['Procod']."' >".$row_tip_hab['Procod']."-".$row_tip_hab['Pronom']."</option>";
							else
								$htmloption   .="<option  value='".$row_tip_hab['Procod']."' >".$row_tip_hab['Procod']."-".$row_tip_hab['Pronom']."</option>";
						}
						else
						{
							if($row_tip_hab['Procod'] == $tipo_unico)
								$htmloption   .="<option   selected value='".$row_tip_hab['Procod']."' >".$row_tip_hab['Procod']."-".$row_tip_hab['Pronom']."</option>";
							else
								$htmloption   .="<option  value='".$row_tip_hab['Procod']."' >".$row_tip_hab['Procod']."-".$row_tip_hab['Pronom']."</option>";

						}
					}
					$x++;
				}
				$htmloption   .="</select>";
				$html.=$htmloption;
				mysql_data_seek($res_tip_hab, 0);
				//$res_tip_hab = mysql_query($q_tip_hab,$conex) or die("Error en el query: ".$q_tip_hab."<br>Tipo Error:".mysql_error());
				$html.='</td><td class="'.$wcf.' td_valorPagoResponsable" rowspan="2" align="center" id="td_reconocido_'.$clave.'" ><input style="text-align: right" class="ppalreconocido" numero_de_responsable="'.$u.'" id="input_reconocido_'.$clave.'_'.$u.'" clave="'.$clave.'" disabled="true" size="9"  type="text" value="0" onchange="recalcular_reconocido('.$clave.')"></td>';
			}
			
			// si el usuario tiene permiso de poner excedentes y si solo hay un responsable.
			if($puedePonerExcedente=='on' && count($vector_topes)==1)
			{
				$html.='<td class="'.$wcf.'" rowspan="2" id="td_excedente_'.$clave.'"><input type="text" id="input_excedente_'.$clave.'" style="text-align: right" size="9" disabled="true" value="0" class="entero"><input type="checkbox" id="checkbox_excedente_'.$clave.'"  onclick="habilitar_excedente('.$clave.')"></td>';
			}
			else
			{
				$html.='<td class="'.$wcf.'" rowspan="2" id="td_excedente_'.$clave.'"><input type="text" id="input_excedente_'.$clave.'" style="text-align: right" size="9" disabled="true" value="0"></td>';
			}
			$html .='<td class="'.$wcf.' classtotales"  id="input_total_'.$clave.'"  align="right" nowrap=nowrap  rowspan="2"></td>';

			if($vec_pension_final[$clave]['info']=='')
				$html .='<td style="display : block" rowspan="2">'.$vec_pension_final[$clave]['info'].'</td>';
			else
				$html .='<td style="font-weight:bold;background-color:#D7EBF9;color:#2779AA;cursor:pointer; border-radius:4px; border: 1px solid #2779AA;" rowspan="2">'.$vec_pension_final[$clave]['info'].'</td>';

			$html	.='<td  style="vertical-align:middle"   rowspan="2" nowrap=nowrap>
							<div id="divbutton_detalle_'.$clave.'" style="display: inline-block"></div>
					   </td>';
			$html	.='<td  style="vertical-align:middle"   rowspan="2" nowrap=nowrap>
							<input type="button" style="cursor : pointer;  height:25px; width:90px "   value="Ver Detalle" onclick="ver_detalle('.$clave.')" style="display: inline-block" >
					   </td>';
			$html	.='</tr>';
			//---------------------------------------

			// dos td por el rowspan
			$html	.='<tr id="tr_detalle_pension_final_'.$clave.'" >
							<td  align="center" class="'.$wcf.' fecha_movimiento" hora="'.$vec_pension_final[$clave]['hor_egr'].'" fecha="'.$vec_pension_final[$clave]['fec_egr'].'" id="tdfechafinalppal_'.$clave.'" >
								'.$vec_pension_final[$clave]['fec_egr'].' / '.$vec_pension_final[$clave]['hor_egr'].'
							</td >
							<td  align="center" class="'.$wcf.'" id="tdfechafinalcobroppal_'.$clave.'">
								'.$vec_pension_final[$clave]['dia_fin_cobro'].'
							</td>
						</tr>';

			// resumen por dia  de cuanto tiene que pagar cada responsable
			$html	.='<tr class="detalle" id="detalle_'.$clave.'" style="display:none;"  >
						<td colspan="2"></td>
						<td colspan="10">
							<table align="Left">
								<tr class="encabezadoTabla" ><td rowspan="2">Fecha</td>
								<td rowspan="2" align="center" class="tercero_'.$clave.'_encabezado" >Tercero</td>';

			$auxi =1;
			$controlador =2;
			$p=1;
			//Se trae el archivo del concepto
			//---------------------
			$qcon=	" SELECT Gruarc "
					."  FROM ".$wbasedato."_000200 "
					." WHERE  Grucod = '".$wconcepto."'";
			$res = mysql_query($qcon,$conex) or die("Error en el query: ".$qcon."<br>Tipo Error:".mysql_error());
			$row = mysql_fetch_array($res);
			$warctar = $row['Gruarc'];
			//------------------
			$htmlaux="";

			while($auxi != $controlador)
			{
				if(isset($vector_topes[$p]['nombre_responsable']))
				{
					$html   .='<td  colspan ="2" verresponsable_'.$p.' align="center">'.$vector_topes[$p]['nombre_responsable'].'</td>';
					$htmlaux =$htmlaux.'<td verresponsable_'.$p.' align="center">Tarifa</td><td verresponsable_'.$p.' >Reconocido</td>';
				}
				else
					$auxi =  $controlador;

				$p++;
			}
			$html 	.='<td Rowspan="2" verexcedente >Excedente</td>';
			$html	.='</tr>';
			$html	.='<tr class="encabezadoTabla">'.$htmlaux.'</tr>';

			$vector_ver_responsable = array();
			for($z=1; $z<=count($vector_topes);$z++)
			{

					if(isset($tiposcambiados[$clave][$z]))
					{
						
						$nuevo_valor = datos_desde_procedimiento($tiposcambiados[$clave][$z], $wconceptoestancia,$vec_pension_final[$clave]['cod_cco_ing']	, $vec_pension_final[$clave]['cod_cco_ing'],$vector_topes[$z]['codigo_responsable'],$vec_pension_final[$clave]['dia_empieza_cobro'],$wtipo_ingreso, '*', 'on', false, '', $row['Fecha_data'] , date("H:i:s"));
						$valor_habitacion_original[$z] = $nuevo_valor['wvaltar'];
						$thabitacion_encuenta_original[$z] = $tiposcambiados[$clave][$z]; // se guarda la tarifa a tener en cuenta

					}
					else
					{
	
						/*Se va por la tarifa segun la politica , si tiene una politica que cambia el  procedimiento y el concepto se reemplaza para que vaya por esta nueva tarifa*/
						$tipo_unico_aux = $tipo_unico ;
						$wconceptoestancia_aux = $wconceptoestancia;
						// si existe el tipo de habitacion en politicas
						
						if(isset($vec_pol [$vector_topes[$z]['codigo_responsable']][$tipo_unico]['tipo_hab_esp']) )
						{
							if($vec_pol [$vector_topes[$z]['codigo_responsable']]['concepto_a'] !="" )
							{
								$wconceptoestancia_aux =  $vec_pol [$vector_topes[$z]['codigo_responsable']]['concepto_a'];
							}
							if ($vec_pol [$vector_topes[$z]['codigo_responsable']]['proceso_a']   !="")
							{
								$tipo_unico_aux =$vec_pol [$vector_topes[$z]['codigo_responsable']]['proceso_a'] ;
							}
						}
					
						$nuevo_valor = datos_desde_procedimiento($tipo_unico_aux, $wconceptoestancia_aux,$vec_pension_final[$clave]['cod_cco_ing']	, $vec_pension_final[$clave]['cod_cco_ing'],$vector_topes[$z]['codigo_responsable'],$vec_pension_final[$clave]['dia_empieza_cobro'],$wtipo_ingreso, '*', 'on', false, '', $row['Fecha_data'] , date("H:i:s"));
						$valor_habitacion_original[$z] = $nuevo_valor['wvaltar']  ;
						$thabitacion_encuenta_original[$z] = $tipo_unico ; // se guarda la tarifa a tener en cuenta

					}

					$vector_ver_responsable[$z]= false;
			}

			$vector_resp = array();
			$tipo_empresa_soat = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tipoempresasoat');
			$tipo_empresa_mpa  = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tipoempresampa');
			$tipo_empresa_soat = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tipoempresasoats');
			$tipo_empresa_soat = explode(",",$tipo_empresa_soat);
			$tipo_empresa_mpa  = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tipoempresapaps');
			$tipo_empresa_mpa  = explode(",",$tipo_empresa_mpa);
			$tipo_empresa_particular = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tipoempresaparticular');
			
			for($t=0;$t<$vec_pension_final[$clave]['dias_cobro'];$t++)
			{
						
				$haypaquete = 0;
				// se va a la funcion topes y paralelos para aplicarle la inteligencia de este proceso
				$vector_resp =  topesyparalelos_estancia($vec_pension_final,$clave,$t,$tipo_empresa_soat,$tipo_empresa_mpa ,$tipo_empresa_particular,$vector_topes,$vec_pol,$tiposcambiados,$vector_valhabitaciones,$wconceptoestancia,$tipo_unico,$vector_ver_responsable,$vectorhabitacion,$wnconceptoestancia,$wbasedato,$wemp_pmla,$whistoria, $wing,$wtipo_paciente,$vector_saldos,$valor_habitacion_original,$thabitacion_encuenta_original,$vec_pol_2,$vec_pension_final[$clave]['cod_cco_ing'],$array_estancia_paquete,$haypaquete,$codigoResponsablepaf,$array_vector_responsables_paf,$array_valores);

				$html .= $vector_resp['html'];
				$vector_topes = $vector_resp['tope'];
			}
			
			

			$html .= '</table>
						</td>
					  </tr>';
			//------------------------------------------------------------

			// totaliza dias
			$total_dias= $total_dias + $vec_pension_final[$clave]['dias_cobro'];

			// totaliza valores
			$total_valor= $total_valor + ( $vec_pension_final[$clave]['val_hab'] * $vec_pension_final[$clave]['dias_cobro']);

			// totaliza habitacion
			$total_valor_hab = $total_valor_hab +   $vec_pension_final[$clave]['val_hab'];

		}
		$html.="<tr>";
		$html.= "<td></td>
				 <td></td>
				 <td></td>
				 <td></td>";
		for($u=1; $u<=count($vector_topes);$u++)
		{
			$html .="<td></td><td></td>";
		}
		$html.="<td><b>Total</b></td>";
		$html.="<td id='grantotal' align='left'></td>";
		$html.="</tr>";
		
		$html .= '</table>';

		$jon_vector_saldos = json_encode($vector_saldos);
		$jon_vector_saldos = str_replace("\"","'",$jon_vector_saldos);
		$html .='<input type="hidden" id="vector_saldos" valor="'.$jon_vector_saldos.'">';



		$vector_resp = array();
		$vector_resp['pension'] = $vec_pension_final;
		$vector_resp['html'] = $html;
		$vector_resp['topes'] = $vector_topes;
		$vector_resp['valorTotalEstancia'] = $array_valores['valorTotal'];
		$vector_resp['valorTotalReconocido'] = $array_valores['valorTotalReconocido'];
		$vector_resp['valorTotalExcedente'] = $array_valores['valorTotalExcedente'];
		$vector_resp['valorReconocido'] = $array_valores['valorReconocido'];
		
		return $vector_resp ;


	}

	function calculardias($vec_pension,$whistoria, $wing,$vec_politicas, $wconcepto, $wtarifa ,$wempresa ,$wtipo_ingreso)
	{

		global $conex;
		global $wemp_pmla;
		global $wbasedato;

		$wbasedato_movhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

		// se carga las restricciones en variables
		$vectiphab= array();
		$dia_hasta 			= $vec_politicas['dia_inicio_cobro'];
		$tipo_hab_esp 		= $vec_politicas['tipo_hab_esp'];
		$auxvectiphab		= explode("!!",$tipo_hab_esp);


		for($s=0; $s<(count($auxvectiphab) -1 ); $s++)
		{

			$auxvectiphab2= explode(":",$auxvectiphab[$s]);
			$vectiphab[$auxvectiphab2[0]]['habitacion']= $auxvectiphab2[0];
			$vectiphab[$auxvectiphab2[0]]['horas']= $auxvectiphab2[1];

		}

		$tipo_hab_esp 		= str_replace("!!", "," ,$tipo_hab_esp);
		$minhoras_hab_esp	= $vec_politicas['minhoras_hab_esp'];
		$concepto_a			= $vec_politicas ['concepto_a'];

		$qcon				=	" SELECT Gruarc "
								."  FROM ".$wbasedato."_000200 "
								." WHERE  Grucod = '".$concepto_a."'";
	
		$res = mysql_query($qcon,$conex) or die("Error en el query: ".$qcon."<br>Tipo Error:".mysql_error());
		$row = mysql_fetch_array($res);

		$warctar 			= $row['Gruarc'];
		$proceso_a			= $vec_politicas ['proceso_a'];

		$qpro				= 	" SELECT Pronom "
								."  FROM ".$wbasedato."_000103 "
								." WHERE  Procod = '".$proceso_a."'";

		$res = mysql_query($qpro,$conex) or die("Error en el query: ".$qpro."<br>Tipo Error:".mysql_error());
		$row = mysql_fetch_array($res);
		$proceso_an= $row['Pronom'];
		//--------------------------------------------------------
		$aux_cantidad_iteraciones = 1;

		$calculo_segun_cco = $vec_politicas['tipo_facturacion'];

		if	(count($vec_pension)==1)
		{
			$calculo_segun_cco ='';
		}

		$horaespecifica = $vec_politicas['horaespecifica']; 
		foreach ($vec_pension as $clave => $valor)
		{

			switch($calculo_segun_cco)
			{

				// tipo de cobro en dias de traslado
				case 'ccomayor':
				{

					$vec_pension[$clave]['dia_empieza_cobro'] = $dia_hasta;
					$fecha_inicio = $dia_hasta ;

					// $vec_pension[$clave]['valor_habitacion_final']
					//		$imprimir .= "egreso".$vec_pension[$clave]['fec_egr']." valor ".$vec_pension[$clave]['val_hab_fin']." -----ingreso".$vec_pension[$clave + 1]['fec_ing']." valor ".intval($vec_pension[$clave + 1]['val_hab_ini'])."  <br>";
					if($vec_pension[$clave]['fec_egr'] == $vec_pension[$clave + 1]['fec_ing'])
					{
						if ( intval($vec_pension[$clave]['valor_habitacion_final']) < intval($vec_pension[$clave + 1]['valor_habitacion_final']))
						{
							$fecha_fin = date('Y-m-d', strtotime( $vec_pension[$clave]['fec_egr'] ) - (1*24*3600) );
							$dia_hasta = $vec_pension[$clave]['fec_egr'];
							$vec_pension[$clave]['dia_fin_cobro'] = $fecha_fin;

						}
						else
						{
							$fecha_fin = $vec_pension[$clave]['fec_egr'];
							$dia_hasta = date('Y-m-d', strtotime( $vec_pension[$clave]['fec_egr'] ) + (1*24*3600) );
							$vec_pension[$clave]['dia_fin_cobro'] =$fecha_fin;
						}

					}
					else
					{

						$dia_hasta = date('Y-m-d', strtotime( $vec_pension[$clave]['fec_egr'] ) + (1*24*3600) );
						$vec_pension[$clave]['dia_empieza_cobro'];
						$fecha_fin = $vec_pension[$clave]['fec_egr'];
						$vec_pension[$clave]['dia_fin_cobro'] = $fecha_fin;
					}


					break;
				}
				case 'cconoche':
				{
					/*
					-- Se agrega otra forma de liquidar la pension que es segun la hora en que el paciente pasa la noche 
					-- la hora especifica trae esta hora y el calculo se hace siempre y cuando la variable calculo_segun_cco
					*/
					$vec_pension[$clave]['dia_empieza_cobro'] = $dia_hasta;
					$fecha_inicio = $dia_hasta ;

					// $vec_pension[$clave]['valor_habitacion_final']
					//		$imprimir .= "egreso".$vec_pension[$clave]['fec_egr']." valor ".$vec_pension[$clave]['val_hab_fin']." -----ingreso".$vec_pension[$clave + 1]['fec_ing']." valor ".intval($vec_pension[$clave + 1]['val_hab_ini'])."  <br>";
					if($vec_pension[$clave]['fec_egr'] == $vec_pension[$clave + 1]['fec_ing'])
					{
						if ( $vec_pension[$clave]['hor_egr'] > $horaespecifica )
						{
							//-- si la hora de egreso es mayor a la hora especifica se tiene en cuenta este dia en la estancia
							$fecha_fin = $vec_pension[$clave]['fec_egr'];
							$dia_hasta = date('Y-m-d', strtotime( $vec_pension[$clave]['fec_egr'] ) + (1*24*3600) );
							$vec_pension[$clave]['dia_fin_cobro'] =$fecha_fin;
							

						}
						else
						{
							$fecha_fin = date('Y-m-d', strtotime( $vec_pension[$clave]['fec_egr'] ) - (1*24*3600) );
							$dia_hasta = $vec_pension[$clave]['fec_egr'];
							$vec_pension[$clave]['dia_fin_cobro'] = $fecha_fin;
							
						}

					}
					else
					{

						$dia_hasta = date('Y-m-d', strtotime( $vec_pension[$clave]['fec_egr'] ) + (1*24*3600) );
						$vec_pension[$clave]['dia_empieza_cobro'];
						$fecha_fin = $vec_pension[$clave]['fec_egr'];
						$vec_pension[$clave]['dia_fin_cobro'] = $fecha_fin;
					}
					break;
				}
				// el tipo de cobro por traslado tipo = centro de costo de ingreso o default
				default :
				{
					if($vec_pension[$clave]['fec_ing'] <= $dia_hasta )
					{
						if ($aux_cantidad_iteraciones == 1)
						{
							$vec_pension[$clave]['dia_empieza_cobro'] = $dia_hasta;
							$fecha_inicio = $dia_hasta ;
							$fecha_fin = $vec_pension[$clave]['fec_egr'];
							$vec_pension[$clave]['dia_fin_cobro'] = $fecha_fin;
						}
						else
						{
							$vec_pension[$clave]['dia_empieza_cobro'] = date('Y-m-d', strtotime( $dia_hasta ) + 1*24*3600 );
							$fecha_inicio = date('Y-m-d', strtotime( $dia_hasta ) + 1*24*3600 );
							$fecha_fin = $vec_pension[$clave]['fec_egr'];
							$vec_pension[$clave]['dia_fin_cobro'] = $fecha_fin;
						}
					}
					else
					{
							$vec_pension[$clave]['dia_empieza_cobro'] = date('Y-m-d', strtotime( $dia_hasta ) + 1*24*3600 );
							$fecha_inicio = date('Y-m-d', strtotime( $dia_hasta ) + 1*24*3600 );
							$fecha_fin = $vec_pension[$clave]['fec_egr'];
							$vec_pension[$clave]['dia_fin_cobro'] = $fecha_fin;

					}
					//-datos que se actualizan para el cobro por cco de ingreso
					$dia_hasta = $vec_pension[$clave]['fec_egr'];
					$aux_cantidad_iteraciones++;
					//---------------------------------------------------------
					break;
				}
			}
			
			
			
			if($vec_pension[$clave]['fec_ing'] == $vec_pension[$clave]['dia_empieza_cobro'])
			{
				$horas_primer_dia = ( 24*3600 - strtotime( "1970-01-01 ".$vec_pension[$clave]['hor_ing']." UTC" ) )/3600;
				$vec_pension[$clave]['horas_ingreso']= $horas_primer_dia;
			}
			else
			{
				$horas_primer_dia =24;
				$vec_pension[$clave]['horas_ingreso']= $horas_primer_dia;
			}

			if ($vec_pension[$clave]['fec_egr'] == $vec_pension[$clave]['dia_fin_cobro'])
			{
				$horas_ultimo_dia = (strtotime( "1970-01-01 ".$vec_pension[$clave]['hor_egr']." UTC" ))/3600;
				$vec_pension[$clave]['horas_egreso']= $horas_ultimo_dia;
			}
			else
			{
				$horas_ultimo_dia = 24;
				$vec_pension[$clave]['horas_egreso']= $horas_ultimo_dia;
			}

			//------------------------
			if($fecha_fin >  $vec_politicas['dia_final_cobro'])
				$fecha_fin  =  $vec_politicas['dia_final_cobro'];

			//se calcula la pension deacuerdo a la manera de pagar si es cco de ingreso en el dia que el paciente
			// esta en dos centros de costos  o cco de egreso  o si es cco de mayor valor


			$datos = ( strtotime( $fecha_fin." UTC" ) - strtotime( $fecha_inicio." UTC" ) )/(24*3600);

			//se le suma 1 a $datos por el dia numero 1 de pension
			$datos = $datos + 1 ;
			if($datos < 0)
				$datos = 0;

			$vec_pension[$clave]['dias_cobro'] = $datos;


			$mensaje_error='';
			

		}
	

		return $vec_pension;
	}
	
	function resumen_pension($whistoria, $wing,$wtar,$wempresa,$wconcepto,$wtipo_ingreso,$wcambiodetipos,$wtipo_paciente, $wfechaparcial, $wcedula)
	{
		global $wbasedato;
		global $conex;
		global $wemp_pmla;
		global $wuse;
		$array_conceptos = array();
		
		$wbasedato_movhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
		$wconcepto = consultarAliasPorAplicacion($conex, $wemp_pmla, 'concepto_estancia');
		// se verifica si la pension ya esta cargada
		$q = 	 "SELECT penhis , pening "
				."  FROM ".$wbasedato."_000173 "
				." WHERE penhis ='".$whistoria."' "
				."   AND pening ='".$wing."' "
				."   AND penest ='on' ";
		
		$html ="<style>	
			.fila1
			{
				background-color: #C3D9FF;
				color: #000000;
				font-size: 8pt;
				padding:2px;
			}
			.fila2
			{
				background-color: #E8EEF7;
				color: #000000;
				font-size: 8pt;
				padding:3px;
			}
			
			</style>";			  
		
		
		
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);
		if ($num == 0 || $num =='0')
			$existe_pension =false;
		else
			$existe_pension = true;
		
		// comprobacion si tiene liquidacion de estancia parcial
		$fechaingreso_liquidacion_parcial ='';
		if($existe_pension)
		{
			$qconsultafechaparcial = "SELECT Fepfec ,Fephor  
										FROM ".$wbasedato."_000263 
									   WHERE Fephis = '".$whistoria."' 
										 AND Feping = '".$wing."' 
									ORDER BY Fepfec DESC, Fephor DESC";
					 
			$res = mysql_query($qconsultafechaparcial,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qconsultafechaparcial." - ".mysql_error());
			$num = mysql_num_rows($res);
			
			
			if ($num == 0 || $num =='0')
			{
				$existe_pension =true;
				
				
			}
			else
			{
				$existe_pension =false;
				$row = mysql_fetch_array($res);
				$fechaingreso_liquidacion_parcial = $row['Fepfec'];
				$horaingreso_liquidacion_parcial  = $row['Fephor'];
			}
		}
		
		//----------
		$string ='';
				//------------
				$selectcargos =" SELECT Audrcu
								   FROM ".$wbasedato."_000106 as A INNER JOIN ".$wbasedato."_000107 AS C ON (A.id = C.Audreg)
								  WHERE Tcarhis 	= '".$whistoria."'
								    AND Tcaring 	= '".$wing."'
									AND Tcarconcod != '".$wconceptoestancia."'
									AND Tcarest 	= 'on'
									AND Tcarfac     = 'S'
									AND Tcardoi     = ''";
				
				$res_get_cargos = mysql_query($selectcargos,$conex) or die("Error en el query: ".$selectcargos."<br>Tipo Error:".mysql_error());
				while($row_get_cargos = mysql_fetch_array($res_get_cargos))
				{
					$vectorcargosenUnix[$row_get_cargos['Audrcu']] = $row_get_cargos['Audrcu'];
				}
				
				//-------------------
				$conexUnix = odbc_connect('facturacion','informix','sco');
				$sqlppal = "SELECT Tcardoi,Tcarlin,Tcarfac , ".$wbasedato."_000106.id, Tcarprocod ,Fdeubi,Logdoc , Logpro , Fenfue
							  FROM ".$wbasedato."_000106
							  LEFT JOIN  ".$wbasedato_movhos."_000158 ON ( Tcardoi = Logdoc AND Tcarlin = Loglin) ,
										 ".$wbasedato_movhos."_000003 , ".$wbasedato_movhos."_000002
							 WHERE  Tcardoi !=''
							   AND  Tcarhis 	= '".$whistoria."'
							   AND  Tcaring 	= '".$wing."'
							   AND  Tcardoi = Fdenum
							   AND  Tcarlin = Fdelin
							   AND  Tcardoi = Fennum" ;

				$res = mysql_query( $sqlppal, $conex  ) or die( mysql_errno()." - Error en el query $sqlppal - ".mysql_error() );
				$num = mysql_num_rows( $res );

				//Se construye  un array  ($rows)  con el resultado de la consulta anterior, con el fin de que el proceso, continue
				//recorriendose el array y no tener una conexion abierta.
				// Al array se le añade una posicion llamada clave y otra documentounix en este documentounix se guardara luego el ducumento
				// correspondiente en unix
				$clave = 0; // se utiliza para ponerle una clave al vector
				while($row = mysql_fetch_array($res))
				{
					
					$clave ++;
					$row['clave'] = $clave;
					$row['documentounix'] = ''; // se crea la posicion documentounix que posteriormente sera llenada
					$rows[] = $row;
				}
				
				
								
					$contador = 0; // este contador es para llevar un control de las idas a unix. no influye en el programa


					// En este proceso se va a unix por la informacion del documento con matrix.
					// Se optimizo yendo pocas veces a unix utilizando un vector (arr_itdrodoc) , si el documento con la fuente de matrix ya fue consultado en unix
					// se graba en un vector la respuesta. Asi antes de ir a Unix se mira si ya se tiene en el vector, ahorrando idas a unix
					// Ademas de esto la posicion del vector rows[documentounix] se llena con el documento consultado.
					$arr_itdrodoc   = array(); // vector documentos matrix , en su valor trae el documento unix

					// se hace siempre y cuando clave sea diferente de cero
					if($clave !=0)
					{
						// se recorre todo el vector resultado de la consulta ppal (sqlppal)
						foreach($rows as &$row)
						{
							
							$documentoppal1 =''; // variable auxiliar para ir llenando el vector  arr_itdrodoc

							// se pregunta si la posicion de del array no se encuentra y si cumple esta condicion hace la consulta en unix
							// si no la cumple (que quiere decir que el elemento ya se encuentra no consulta en unix
							if( !isset($arr_itdrodoc[$row['Fenfue']][$row['Tcardoi']]) )
							{
								$arr_itdrodoc[$row['Fenfue']][$row['Tcardoi']] = "";
							
								// se consulta en drodocdoc el documento con que se grabo a Unix , la tabla ITDRODOC
								// es una tabla puente entre Matrix y Unix  averiguo con los datos de Matrix con que documento y fuente
								// quedo en unix y sigo trabajando con este.
								$sqlu = "SELECT drodocdoc
										   FROM ITDRODOC
										  WHERE drodocnum  = '".$row['Tcardoi']."'
											AND drodocfue  = '".$row['Fenfue']."'";
								$resu = odbc_do( $conexUnix, $sqlu );


								if( odbc_fetch_row($resu))
								{
									
									$documentoppal1 = odbc_result($resu,1); // documentoppal1 se iguala al resultado encontrado
									$arr_itdrodoc[$row['Fenfue']][$row['Tcardoi']] = $documentoppal1; // se llena la posicion con el documentoppal11
									$row['documentounix'] = $documentoppal1; // la posicion del vector rows se llena con el documentounix

								}


							}
							else
							{
								
								// Si no se consulta  se  busca la posicion y se asigna a documentoppal  y  se le asigna a la posicion del vector rows documentounix
								$documentoppal1 = $arr_itdrodoc[$row['Fenfue']][$row['Tcardoi']];
								$row['documentounix'] = $documentoppal1;
							}

						}

						//-----------------------------------------------------------------------------

						// Este proceso se utiliza para crear una consulta  unix por fuente de documentos que haya .
						// Utilizando el vector antes construido arr_itdrodoc
						// Este vector tiene  por cada Fuente , todos los documentos correspondientes a esta, es decir:
						//          hay articulos para actualizar  y entre todos estos articulos hay 2 Fuentes , la 11 y la AP
						//          se hacen dos consultas a unix una donde estan todos los documentos correspondientes a la fuente 11 y en otra
						//          todos los documentos correspondiente a la fuente AP
						// Nota : todo esto se crea para optimizar el proceso.

						$strin_in = array(); // array que se utiliza para construir el IN de la consulta, aqui se guardaran todos los documentos
											 // separados por coma por cada una de las fuentes.

						// se recorre el array arr_itdrodoc por las fuentes que existan
						foreach ( $arr_itdrodoc as $keyfuente => $fuente  )
						{
							// Se recorre el array con su fuente correspondiente por cada uno de los documentos
							foreach($fuente as $key => $valor)
							{
								// En el vector  strin_in  queda como clave la fuente y en su valor todos los documentos
								// separados por coma.
								$strin_in[$keyfuente] = $strin_in[$keyfuente].",'".$valor."'";
							}

						}


						// Acontinuacion se hara un proceso para  hacer las consultas a unix utilizando el vector $strin_in para su debida
						// construccion y los resultados se almacenaran en el vector  arrayresultadosunix .
						//
						$r=0;
						$arrayresultadosunix = array(); // Este vector se crea para almacenar los resultados de las consulta en unix
						// se recorre el vector $strin_in
						foreach($strin_in as $key => $valor)
						{
							$valor=substr($valor,1);
							// consulta donde se agrupan todos los documentos de una fuente determinada. Es decir
							// por la fuente que seria key , se pone el valor del strin_in que son todos los documentos de esa fuente
							// separados por coma
							$selectiv   = " SELECT drodetfac,drodetart,drodetfue,drodetdoc,drodetite
														  FROM IVDRODET
														 WHERE drodetfue = '".$key."'
														   AND drodetdoc IN ( ".$valor.")";

							$resiv = odbc_do( $conexUnix, $selectiv );


							while (odbc_fetch_row($resiv))
							{

								$drodetfac = odbc_result($resiv,1); // se guarda el resultado de si es facturable o no (drodetfac)
								$drodetart = odbc_result($resiv,2); // se guarda el resultado del articulo (drodetart)
								$drodetfue = odbc_result($resiv,3); // se guarda el resultado de la fuente (drodetfue)
								$drodetdoc = odbc_result($resiv,4); // se guarda el resultado del documento (drodetdoc)
								$drodetite = odbc_result($resiv,5); // se guarda el resultado de la linea o ite llamado en unix (drodetite)
								$arrayresultadosunix[$drodetfue][$drodetdoc][$drodetite]['articulo']  = $drodetart; // se llena la posicion del vector arrayresultadosunix[fuente][documento][linea][articulo] con el articulo
								$arrayresultadosunix[$drodetfue][$drodetdoc][$drodetite]['facturable']= $drodetfac; // se llena la posicion del vector arrayresultadosunix[fuente][documento][linea][facturable] con la condicion si o no facturable
							}

						}
					}
					//---------------------------------------------------------------------
					//--------------------------------------------------------------------


					//-----
					//Recorrido ppal , aqui se realizan las operaciones de  actualizar la condicion de facturable o no  de los medicamentos,
					//en las tablas  IVDRODET y FACARDET.
					//Tiene varios flujos
					//1- Medicamento no se parte en varios medicamentos ni se reemplaza por otro , ademas de esto la linea en matrix es igual a la de unix
					//   corresponde a la mayoria de los casos
					//2- Medicamento no se parte en varios medicamentos ni se reemplaza por otro , las lineas en matrix no corresponden a las de unix
					//3- Medicamento se parte en varios medicamentos o se reemplaza por otro , Las lineas corresponden tanto en matrix como en unix
					//4- Medicamento se parte en varios medicamentos o se reemplaza por otro , Las lineas no corresponde
					
					if($clave !=0)
					{
						
						foreach($rows as $row)
						{
							
							//--Se trae el estado, del vector ppal $row['Fdeubi']
							$estado ='';
							$estado =  $row['Fdeubi'];
							//--------------------------

							// el proceso solo sigue si se encuentra en estado UP = procesado a unix , US = Unix sin procesar (esto es porque quedan muchos
							if ($estado =='UP' || $estado =='US')
							{
								
								//--------------------------------------------------------------
								// valido si tiene regla que divide un articulo entre varios en la tabla movhos_000158
								// a veces algunos medicamentos se parten en varios componentes y pasan a unix divididos ,
								// entonces hay que hacer un analisis particular para estos
								if ($row['Logdoc'] !='')
								{
									$validacion ='si'; // se llena la variable validacion si validacion vale si , es porque tiene una regla (de reemplazo o divide en componentes)
									if($row['Logpro'] =='on')
										$esdereemplazo = 'si'; // esdereemplazo  se define si reemplaza articulo por otro
									else
									   $esdereemplazo = 'no';

								}
								else
								{
								   $validacion ='no';
								   $esdereemplazo ='';
								}
								//
								//-------------------------------------------
								//--Se trae el estado, del vector ppal $row['Fenfue']
								$fuente ='';
								$fuente = $row['Fenfue'];
								//------------------
								
								// si hay conexion a unix haga
								if( $conexUnix ){
									
									$documentoppal = $row['documentounix'];//Se trae el documentoppal, del vector ppal $row['documentounix']
									if( $documentoppal !='' )
									{
											
											$i = 0;
											//1- Medicamento no se parte en varios medicamentos ni se reemplaza por otro , ademas de esto la linea en matrix es igual a la de unix
											//   corresponde a la mayoria de los casos
											if($validacion!='si')
											{

												$articulo =''; // variable auxiliar donde estara el articulo
												// Si el arrayresultadosunix[fuente][documentoppal][linea]['articulo'] existe no hace la consulta a unix
												// por lo general todos estos articulos ya se encuentran en este vector pero por salvedad  se tiene el flujo
												// del else y se hace la consulta , por si llega a no estar en este array se busque en unix
												if(isset($arrayresultadosunix[$fuente][$documentoppal][$row['Tcarlin']]['articulo']))
												{
													 $articulo = $arrayresultadosunix[$fuente][$documentoppal][$row['Tcarlin']]['articulo'];
													 $drodetart = $articulo; // la variable drodetart se llena con el articulo
													 $drodetfac = $arrayresultadosunix[$fuente][$documentoppal][$row['Tcarlin']]['facturable']; // La variable drodetfac se llena con la condicion facturable o no
												}
												else
												{
													// hago Consulta para encontrar el articulo y si es facturable o no, esta consulta no se ejecutara si en el vector arrayresultadosunix ya se encuentra
													$selectiv   = " SELECT drodetfac , drodetart
																	  FROM IVDRODET
																	 WHERE drodetfue = '".$fuente."'
																	   AND drodetdoc = '".$documentoppal."'
																	   AND drodetite = '".$row['Tcarlin']."'";

													$resiv = odbc_do( $conexUnix, $selectiv );
													$drodetfac = odbc_result($resiv,1); // se llena la condicion de facturable si o no
													$drodetart = odbc_result($resiv,2); // se llena el articulo.
													$contador++;// este contador es utilizado para contar las veces que se va a unix
												}

												//--Se comparan los articulo de matrix con los de unix , aveces no coinciden entonces se tiene que buscar cual es el articulo que corresponde
												//--Las lineas ya no coincidirian en unix y matrix y se iria por el flujo 2(2- Medicamento no se parte en varios medicamentos ni se reemplaza por otro , las lineas en matrix no corresponden a las de unix)
												$nodiferenteprocedimiento = true;
												if( $drodetart != $row['Tcarprocod']  )
												{
													$nodiferenteprocedimiento = false;
												}
												//-- si los procedimientos coresponden (articulos )
												if($nodiferenteprocedimiento)
												{

													if($drodetfac != $row['Tcarfac'])
													{
														
														$contador++;
														
													}

													$selectfacar   = "  SELECT cardetreg
																		  FROM FACARDET
																		 WHERE cardetfue = '".$fuente."'
																		   AND cardetdoc = '".$documentoppal."'
																	       AND cardetite = '".$row['Tcarlin']."'";
													$resfacar = odbc_do( $conexUnix, $selectfacar );
													$cardetreg = odbc_result($resfacar,1);
													$vectorcargosenUnix[$cardetreg] = $cardetreg;
												



													// si los registros en facardet e ivdrodet y cliame_000106 son iguales
													// actualizo en la tabla cliame_000106  el campo Tcaraun igual a on y asi
													// queda por terminada la transaccion

													//* Enero 20 de 2016 se quita este if
													// if($row['Tcarfac'] == $cardetfac && $row['Tcarfac']== $drodetfac)
													// {
														/*$sql3 = "   UPDATE ".$wbasedato."_000106
																	  SET Tcaraun = 'on'
																	WHERE  id = '".$row['id']."'";
														mysql_query( $sql3, $conex  ) or die( mysql_errno()." - Error en el query $sql3 - ".mysql_error() );
														*/
													// }

												}
												else
												{
													// hago Consulta para luego validar si los articulos son iguales y si  puedo actualizar o no
													$selectiv   = " SELECT drodetite
																	  FROM IVDRODET
																	 WHERE drodetfue = '".$fuente."'
																	   AND drodetdoc = '".$documentoppal."'
																	   AND drodetart = '".$row['Tcarprocod']."' ";

													$resiv = odbc_do( $conexUnix, $selectiv );
													//$drodetfac = odbc_result($resiv,1);
													//$drodetart = odbc_result($resiv,2);
													$drodetlinea = odbc_result($resiv,1);


													if($drodetlinea=='')
													{

													}
													else
													{
															

															$selectfacar   = "  SELECT cardetreg
																				  FROM  FACARDET
																				WHERE cardetfue = '".$fuente."'
																				  AND cardetdoc = '".$documentoppal."'
																				  AND cardetite = '".$drodetlinea."'";

															$resfacar = odbc_do( $conexUnix, $selectfacar );
															$cardetreg = odbc_result($resfacar,1);
															$vectorcargosenUnix[$cardetreg] = $cardetreg;
															




													}

												}
											}
											else
											{


												//2- Medicamento no se parte en varios medicamentos ni se reemplaza por otro , las lineas en matrix no corresponden a las de unix
												if ($esdereemplazo =='si')
												{
													$querycenpro = "SELECT  Pdeins
																	  FROM  cenpro_000003
																	 WHERE  Pdepro ='".$row['Tcarprocod']."'";

													$resquerycenpro=  mysql_query( $querycenpro, $conex  ) or die( mysql_errno()." - Error en el query $querycenpro - ".mysql_error() );

													$p=-1;
													$variablereemplazo    = '';
													$auxvariablereemplazo = '';
													while($rowquerycenpro = mysql_fetch_array($resquerycenpro))
													{
														$p++;
														$auxvariablereemplazo = $auxvariablereemplazo.",".(($row['Tcarlin']*1) + $p);

													}

													$variablereemplazo = substr($auxvariablereemplazo,1);
													 //$variablereemplazo = $auxvariablereemplazo;

												}
												else
												{
													$variablereemplazo = $row['Tcarlin'];
												}

												$wbasedato_movhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
												// Consulto los medicamentos y veo cuales son su reglas y en cuantos
												// articulos se parte el medicamento
												$sqlval          = "  SELECT Logdoc,Loglin,Logaor,Logare
																		FROM ".$wbasedato_movhos."_000158
																	   WHERE Logdoc = '".$row['Tcardoi']."'
																		 AND Loglin IN ( ".$variablereemplazo." ) ";

												$resval =  mysql_query( $sqlval, $conex  ) or die( mysql_errno()." - Error en el query $sqlval - ".mysql_error() );


												$bandera = true;
												while($rowval = mysql_fetch_array($resval))
												{



													// hago Consulta para luego validar si los articulos son iguales y si  puedo actualizar o no
													$selectiv   = " SELECT drodetfac , drodetart
																	  FROM IVDRODET
																	 WHERE drodetfue = '".$fuente."'
																	   AND drodetdoc = '".$documentoppal."'
																	   AND drodetite = '".$rowval['Loglin']."'";

													$resiv = odbc_do( $conexUnix, $selectiv );
													$drodetfac = odbc_result($resiv,1);
													$drodetart = odbc_result($resiv,2);

													$diferenteprocedimiento = true;
													if( $drodetart != $rowval['Logare']  )
													{
														$diferenteprocedimiento = false;
													}


													if($diferenteprocedimiento)
													{

													

														// se selecciona el estado de facturable o no facturable en facardet
													 	$selectfacar = "   SELECT cardetreg
																			FROM FACARDET
																			WHERE cardetfue = '".$fuente."'
																			  AND cardetdoc = '".$documentoppal."'
																			  AND cardetite = '".$rowval['Loglin']."'";

														$resfacar = odbc_do( $conexUnix, $selectfacar );
														$cardetreg = odbc_result($resfacar,1);
														$vectorcargosenUnix[$cardetreg] = $cardetreg;

														



													}
													else
													{


															// hago Consulta para luego validar si los articulos son iguales y si  puedo actualizar o no
															$selectiv   = " SELECT drodetite
																			  FROM IVDRODET
																			 WHERE drodetfue = '".$fuente."'
																			   AND drodetdoc = '".$documentoppal."'
																			   AND drodetart = '".$rowval['Logare']."' ";

															$resiv = odbc_do( $conexUnix, $selectiv );
															$drodetlinea = odbc_result($resiv,1);


															if($drodetlinea=='')
															{

															}
															else
															{
																

																// se selecciona el estado de facturable o no facturable en facardet
																$selectfacar = "   SELECT cardetreg
																					FROM FACARDET
																					WHERE cardetfue = '".$fuente."'
																					AND cardetdoc = '".$documentoppal."'
																					AND cardetite = '".$drodetlinea."'";

																$resfacar = odbc_do( $conexUnix, $selectfacar );
																$cardetreg = odbc_result($resfacar,1);
																$vectorcargosenUnix[$cardetreg] = $cardetreg;

																
															}



													}
												}
												


											}

									}

								}
							}

						}
					}
				
				$conexUnix = odbc_connect('facturacion','informix','sco');
				
				$sqlimpacmre = "SELECT pacmretar , pacmrecer ,pacmreres
								  FROM INPACMRE
							     WHERE pacmrehis   = '".$whistoria."' 
							       AND pacmrenum   = '".$wing."'";
			
				$arrayempresasunix	=array();	   
				if($resimpacmre = odbc_exec($conexUnix, $sqlimpacmre))
				{
					while(odbc_fetch_row($resimpacmre))
					{
						$arrayempresasunix[odbc_result($resimpacmre,'pacmretar')]['codigo'] = odbc_result($resimpacmre,'pacmrecer');
						$arrayempresasunix[odbc_result($resimpacmre,'pacmretar')]['nombre'] = odbc_result($resimpacmre,'pacmreres');
					}
					
				}
				
				
				$sqlfacardet = "SELECT *
								  FROM FACARDET
								 WHERE cardethis   = '".$whistoria."' 
						           AND cardetnum   = '".$wing."'
								   AND cardetanu   != '1'
								   AND cardetfue   != '02'
								   AND cardetfac   != 'N'";
							   
				
					   
				
				$y=0;
				$t=0;
				$cargos_unix_mostrar =array();
				$arrayArticulo 		 =array();
				$arraynomconcepto 	 =array();
				$arraynomprocedimiento 	 =array();
				if($resfacardet = odbc_exec($conexUnix, $sqlfacardet))
				{
					while(odbc_fetch_row($resfacardet))
					{
						$y++;
						// $esta='no';
						//echo "nooo".$row_get_cargos['Audrcu'];
						// if(count($vectorcargosenUnix)>0)
						// {
							
							
							// if(isset($row_get_cargos['Audrcu']) && array_key_exists ( $row_get_cargos['Audrcu'] , $vectorcargosenUnix ) && array_key_exists (odbc_result($resfacardet,'cardetreg'), $vectorcargosenUnix[$row_get_cargos['Audrcu']] ))
							// {
								// $esta ='si';
							// }
						// }	

						//if($esta=='no')
						{
							
							if(odbc_result($resfacardet,'cardetfue') == '11' || odbc_result($resfacardet,'cardetfue') == '12' || odbc_result($resfacardet,'cardetfue') == 'GD' || odbc_result($resfacardet,'cardetfue') == 'DD'  || odbc_result($resfacardet,'cardetfue') == 'AP')
							{
								// --> Consultar detalle de los articulos
								$sqlDetArticulos = "
								SELECT drodetart, drodetcan
								  FROM IVDRODET
								 WHERE drodetfue = '".odbc_result($resfacardet,'cardetfue')."'
								   AND drodetdoc = '".odbc_result($resfacardet,'cardetdoc')."'
								   AND drodetite = '".odbc_result($resfacardet,'cardetite')."'
								";
								$resDetArticulos = odbc_exec($conexUnix, $sqlDetArticulos);
								if(odbc_fetch_row($resDetArticulos))
								{
									$codigo = trim(odbc_result($resDetArticulos,'drodetart'));
									$cantidad = trim(odbc_result($resDetArticulos,'drodetcan'));
									
									// --> Obtener maestro de articulos
									if(!$arrayArticulo[$codigo])
									{
										$sqlArt = "
														SELECT Artcod, Artgen
														  FROM movhos_000026
														 WHERE Artest = 'on'
														   AND Artcod ='".$codigo."' 
														";
										$resArt = mysql_query($sqlArt, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlArt):</b><br>".mysql_error());
										while($rowArt = mysql_fetch_array($resArt))
										{
											$arrayArticulo[$rowArt['Artcod']] = utf8_encode(trim($rowArt['Artgen']));
										}
									}
									$nombreCod = $arrayArticulo[$codigo];
									$cargos_unix_mostrar[$t]['CodProcedi'] = $codigo;
									$cargos_unix_mostrar[$t]['NomProcedi'] = $nombreCod;
								}
							}
							else
							{
								$cargos_unix_mostrar[$t]['CodProcedi'] = odbc_result($resfacardet,'cardetcod');
								$cargos_unix_mostrar[$t]['NomProcedi'] = 's';
							}
							
							$cargos_unix_mostrar[$t]['Fecha'] = odbc_result($resfacardet,'cardetfec');
							
							
							$cargos_unix_mostrar[$t]['Servicio'] = odbc_result($resfacardet,'cardetcco');
							$cargos_unix_mostrar[$t]['NomServicio'] = '';
							$cargos_unix_mostrar[$t]['CodTercero'] = '';
							$cargos_unix_mostrar[$t]['NomTercero'] = '';
							$cargos_unix_mostrar[$t]['TipoFact'] ='';
							$cargos_unix_mostrar[$t]['Devolucion'] = '';
							$cargos_unix_mostrar[$t]['yaRegrabado'] ='';
							$cargos_unix_mostrar[$t]['Cantidad'] = odbc_result($resfacardet,'cardetcan');
							$cargos_unix_mostrar[$t]['ValorUn'] = odbc_result($resfacardet,'cardetvun');
							$cargos_unix_mostrar[$t]['ValorTo'] = odbc_result($resfacardet,'cardettot');
							 if( odbc_result($resfacardet,'cardetvre') == 0 &&  odbc_result($resfacardet,'cardetvex')==0)
							{
								$cargos_unix_mostrar[$t]['ValorRe'] = odbc_result($resfacardet,'cardettot');
							}
							else
							{
								$cargos_unix_mostrar[$t]['ValorRe'] = odbc_result($resfacardet,'cardetvre');
							}
							$cargos_unix_mostrar[$t]['ValorEx'] = odbc_result($resfacardet,'cardetvex');
							$cargos_unix_mostrar[$t]['FacturadoReconoci'] = odbc_result($resfacardet,'cardetvfa');
							$cargos_unix_mostrar[$t]['ReconExced'] = '';
							$cargos_unix_mostrar[$t]['Facturable'] = odbc_result($resfacardet,'cardetfac');
							$cargos_unix_mostrar[$t]['CodUsuario'] = '';
							$cargos_unix_mostrar[$t]['codEntidad'] = $arrayempresasunix[odbc_result($resfacardet,'cardettar')]['codigo'];
							$cargos_unix_mostrar[$t]['Entidad'] = $arrayempresasunix[odbc_result($resfacardet,'cardettar')]['nombre'];
							$cargos_unix_mostrar[$t]['FactuExcede'] = '';
							$cargos_unix_mostrar[$t]['FacturadoReconoci'] = '';
							$cargos_unix_mostrar[$t]['ConceptoInventar'] = '';
							$cargos_unix_mostrar[$t]['Registro'] = '';
							$cargos_unix_mostrar[$t]['GraboParalelo'] = '';
							$cargos_unix_mostrar[$t]['idParalelo'] = '';
							$cargos_unix_mostrar[$t]['pendienteRevicion'] = '';
							$cargos_unix_mostrar[$t]['politicaAplico'] = '';
							$cargos_unix_mostrar[$t]['nomEspecialidad'] = '';
							$cargos_unix_mostrar[$t]['Usuario'] = '';
							$facturado							=  'no_facturado' ;
							$cargos_unix_mostrar[$t]['Codconcepto'] =odbc_result($resfacardet,'cardetcon'); 
							$t++;
						}
						//$html.="<tr><td>".$y."</td><td>".$esta."----".odbc_result($resfacardet,'cardetcon')."</td><td>".odbc_result($resfacardet,'cardettot')."</td><td>".odbc_result($resfacardet,'cardetreg')."</td></tr>";
					
					}
				}
				//"</table>";
				
				//MIGRA_1
				// odbc_close($conexUnix);
				// odbc_close_all();
				
				//$html .= "segimdp".count($cargos_unix_mostrar);
				
		//----------
		
		//----------------------------------------------------

		// como se forma la pension
		// 1. primero va a la cliame_101 a buscar el dia de ingreso , fecha de ingreso y el servicio de ingreso
		// 2.busca en la tabla movhos_118 el dia de egreso , fecha de egreso, hora de egreso
		// 3. se va a la funcion traer_detalle_estancia  y primero se consulta los movimientos que tuvo un paciente
		//    consultado los recibos esto da   las fechas y horas de ingreso

	

			$datos_ingreso =array();

			//--Datos de ingreso del paciente
			//----------------------------------------------
			$q_diaingreso =  "SELECT Fecha_data, Hora_data "
							."  FROM ".$wbasedato_movhos."_000017 "
							." WHERE Eyrhis = '".$whistoria."' "
							."   AND Eyring = '".$wing."'"
							."   AND Eyrtip = 'recibo'"
							."   AND Eyrest = 'on'";


			$res_diaingreso = mysql_query($q_diaingreso,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_diaingreso." - ".mysql_error());
			$row_diaingreso = mysql_fetch_array($res_diaingreso);

			$res = mysql_query($q_diaingreso,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_diaingreso." - ".mysql_error());
			$num = mysql_num_rows($res);
			$row = mysql_fetch_array($res);
			
			if($fechaingreso_liquidacion_parcial == '')
			{
				$datos_ingreso ['dia'] = $row['Fecha_data'];
				$dia_ingreso = $row['Fecha_data'];
				$datos_ingreso ['hora'] = $row['Hora_data'];
			}
			else
			{
				
				$datos_ingreso ['dia'] 	= 	$fechaingreso_liquidacion_parcial;
				$dia_ingreso 			=	$fechaingreso_liquidacion_parcial;
				$datos_ingreso ['hora'] = 	$horaingreso_liquidacion_parcial;
			}
			//------------------------------------------------------------------

			//-----------------------------------------------------------
			//Datos de Egreso del paciente
			//-----------------------------------------------------
			$datos_egreso = array();


			$q_diaegreso =  " SELECT Ubifap, Ubihap "
							."  FROM ".$wbasedato_movhos."_000018 "
							." WHERE  Ubihis = '".$whistoria."' "
							."   AND  Ubiing = '".$wing."' "
							."   AND  Ubialp ='on' ";

			$res = mysql_query($q_diaegreso,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_diaingreso." - ".mysql_error());
			$num = mysql_num_rows($res);
			$row = mysql_fetch_array($res);
			$datos_egreso['dia'] = $row['Ubifap'];
			$dia_egreso = $row['Ubifap'];
			$datos_egreso['hora'] = $row['Ubihap'];
			
			//------
			/*
			Se adiciona la opcion de que cuando no hay alta en proceso se pone como alta en proceso la fecha y hora que se consulta
			y asi hacer una simulacion, esto no se deja grabar ni tampoco se congela la cuenta para luego ser recuperada pues eso solo es un
			estimando.
			*/
			//------
			if($num == 0)
			{
				if($wfechaparcial!='no')
				{
					$datos_egreso['dia'] 	= $wfechaparcial;
					$dia_egreso 			= $wfechaparcial;
					$datos_egreso['hora'] 	= date("H:i:s");
				}
				else
				{
					$datos_egreso['dia'] 	= date("Y-m-d");
					$dia_egreso 			= date("Y-m-d");
					$datos_egreso['hora'] 	= date("H:i:s");
				}
				
				$liquidarparcial="si";
			}
			else
			{
				
				$liquidarparcial="no";
			}
			
			$vec_politicas = array(); // arreglo que contiene las politicas
			//----------------------------------------------------------------------------
			//--> Datos para traer politica
			$arr_variables['wcodcon'] 			= $wconcepto;
			$arr_variables['wprocod'] 			= "*";
			$arr_variables['wtar'] 				= $wtar;
			$arr_variables['wcodemp'] 			= $wempresa;
			$arr_variables['wespecialdiad'] 	= "*";
			$arr_variables['wccogra'] 			= "*";
			$arr_variables['wdia_ingreso']		= $datos_ingreso ['dia'];
			$arr_variables['wdia_egreso'] 		= $datos_egreso  ['dia'];
			$arr_variables['dia_inicio_cobro'] 	= "";
			$arr_variables['dia_final_cobro'] 	= "";
			$arr_variables['tipo_facturacion'] 	= "";
			$arr_variables['horaespecifica'] 	= "";
			$arr_variables['tipo_hab_esp']		= "";
			$arr_variables['tiempo_minimo']		= "";
			$arr_variables['concepto_a']		= "";
			$arr_variables['proceso_a']			= "";
			$arr_variables['proceso_a']			= "";
			$arr_variables['esdepension']		="Es de pension";


			$qtip_res = " SELECT Emptem  "
						."  FROM ".$wbasedato."_000024 "
						." WHERE Empcod = '".$wempresa."'";

			$res_tip_res 	= mysql_query($qtip_res,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qtip_res." - ".mysql_error());
			$rows_tip_res 	= mysql_fetch_array($res_tip_res);
			$arr_variables['tipoEmpresa'] = $rows_tip_res['Emptem'];
			//----------------------------------------------------

			//-------------------------------------------------------------------------------------------------------------------
			// trae las politicas - con los parametros que estan en $arr_variables
			// politicas generales al primer responsable
			ValidarGrabacion($arr_variables, $CargosAnexos);

			$vec_politicas ['tiempo_minimo'] = $arr_variables['tiempo_minimo'];// fecha minima de estancia

			//-------------------------------------------------------------------------------
			//-------------------------------------------------------------------------------
			//-------------------------------------------------------------------------------
			//1 POLITICA DE MINIMO DE HORAS QUE SE PUEDE LIQUIDAR POR  ESTANCIA
			//
			//--Se calcula dia de ingreso  segun el tiempo minimo de estancia

			$horas_primer_dia = ( strtotime($datos_ingreso ['dia']." 23:59:59") - strtotime( $datos_ingreso ['dia']." ".$datos_ingreso ['hora'] ) ) / 3600;

			if( $vec_politicas ['tiempo_minimo'] > $horas_primer_dia)
			{
				$datos_ingreso ['dia'] 			= date("Y-m-d", strtotime( $datos_ingreso ['dia']." 00:00:00") + 24 * 3600); // se le suma un dia
				$datos_ingreso ['hora']			= '00:00:00';	// Se inicia las horas del nuevo dia a las 00:00:00
				$arr_variables ['wdia_ingreso']	= $datos_ingreso ['dia'];
				
			}


			//---------------------------------------------------------------

			//--Se calcula el dia de egreso sengun tiempo minimo de estancia

			$horas_ultimo_dia = (strtotime($datos_egreso['dia']." ".$datos_egreso['hora'] ) - strtotime($datos_egreso['dia']." 00:00:00 ") ) / 3600 ;

			if( $vec_politicas ['tiempo_minimo'] > $horas_ultimo_dia )
			{
				$datos_egreso  ['dia'] 			=	date("Y-m-d", strtotime( $datos_egreso['dia']." 00:00:00") - 24 * 3600);
				$datos_egreso  ['hora']			=  	'23:59:59';
				$arr_variables ['wdia_egreso']	=   $datos_egreso['dia'];
			}

			//------------------------------------------------------------------------------------
			//------------------------------------------------------------------------------------
			//------------------------------------------------------------------------------------

			//------------------------------------------------------------------------------------
			//2 POLITICA DE  NO COBRO DIA INGRESO O NO COBRO DIA EGRESO
			//------------------------------------------------------------------------------------
			/*Se excluye politica de dia de no pago dia de ingreso o no pago dia de egreso si la estacia es de un dia */
			$no_aplica_politica_dia_ing = false;

			if($datos_ingreso ['dia'] == $datos_egreso  ['dia'] )
			{
				$no_aplica_politica_dia_ing = true;
				$arr_variables['dia_inicio_cobro']='';
				$arr_variables['dia_final_cobro']='';
			}


				if($arr_variables['dia_inicio_cobro']== '')
					$vec_politicas ['dia_inicio_cobro'] =	$arr_variables['wdia_ingreso'];
				else
					$vec_politicas ['dia_inicio_cobro'] = date('Y-m-d', strtotime( $arr_variables['wdia_ingreso']." 00:00:00" ) + $arr_variables['dia_inicio_cobro']*24*3600 );

				if($arr_variables['dia_final_cobro'] == '')
					$vec_politicas ['dia_final_cobro'] = 	$arr_variables['wdia_egreso'];
				else
					$vec_politicas ['dia_final_cobro']  = date('Y-m-d', strtotime( $arr_variables['wdia_egreso']." 00:00:00"  ) - $arr_variables['dia_final_cobro']*24*3600 );

		
			//------------------------------------------------------------------------------------
			//POLITICA DE  TIPO DE COBRO EN TRASLADOS
			//------------------------------------------------------------------------------------
			if ($arr_variables['tipo_facturacion'] =='')
				$vec_politicas ['tipo_facturacion']="ingreso"; // Por defecto si no hay nada  se cobra los traslados por habitacion ingreso
			else
				$vec_politicas ['tipo_facturacion'] = $arr_variables['tipo_facturacion'] ;

			//------------------------------------------------------------------------------------


			$vec_politicas ['tipo_hab_esp'] 	= $arr_variables['tipo_hab_esp']; 	 	// Tipos de habitacion especial
			$vec_politicas ['minhoras_hab_esp'] = $arr_variables['minhoras_hab_esp']; 	// minimo de horas habitacion especial
			$vec_politicas ['concepto_a'] 		= $arr_variables['concepto_a']; 		// cambio de concepto
			$vec_politicas ['proceso_a'] 		= $arr_variables['proceso_a']; 			// cambio de procedimiento
			$vec_politicas ['horaespecifica'] 	= $arr_variables['horaespecifica']; 			// cambio de procedimiento

			//-------------------------------------------------------------------------------------------------------
			//-------------------------------------------------------------------------------------------------------

			//--Impresion de la estancia
			$vector_resp = array();
			//---------------Vector respuesta trae el html de la politica
			$vector_resp = traer_detalle_estancia($whistoria, $wing, $datos_ingreso, $datos_egreso ,$vec_politicas,$wconcepto,$wtar,$wempresa,$wtipo_ingreso,$nejemplo,$wcambiodetipos,$wtipo_paciente,$fechaingreso_liquidacion_parcial,$horaingreso_liquidacion_parcial);
			//----------------------------------------------------------------
			
			if (count($vector_resp['pension'])*1 ==0)
			{
				//$html .= "<tr><td><table><tr><td align='center'><div class='fondoAmarillo' style='font-size:12pt; width=300px'>La Estancia asociada al numero de Historia: <b>".$whistoria." - ".$wing."</b> <br> No tiene movimiento hospitalario</div></td></tr></table></td></tr>";
				
			}
			else
			{
			
				//$html .="<tr><td></td></tr>";
				//$html .="<table><tr><td>Parametros</td><td>historia ".$whistoria." Ingreso ".$wing." Tarifa  ".$wtar." empresa ".$wempresa." Concepto ".$wconcepto."   tipo ingreso ".$wtipo_ingreso." cambio de tipo ".$wcambiodetipos." tipo paciente ".$wtipo_paciente."  fecha parcial ".$wfechaparcial." </td></tr></table></td></tr>";
				
			}
				$UseEstparcial='on';
				$vectorhabitacion = array();
				$vectorcargosenUnix = array();
				$q_tip_hab  = "SELECT Procod,Pronom "
							 ."  FROM  ".$wbasedato."_000103"
							 ." WHERE  Protip='H' ";
				$res_tip_hab = mysql_query($q_tip_hab,$conex) or die("Error en el query: ".$q_tip_hab."<br>Tipo Error:".mysql_error());
				$vectorhabitacion[''] =  'NO HAY HABITACION';
				while($row_tip_hab = mysql_fetch_array($res_tip_hab))
				{
					$vectorhabitacion[$row_tip_hab['Procod']] = $row_tip_hab['Pronom'];
				}
				$wconceptoestancia = consultarAliasPorAplicacion($conex, $wemp_pmla, 'concepto_estancia');
				$forma_estado ='';
				$ColorFila 	  = 'fila1';
				$Total_r_e 	  = 0;
				$Total_exe    = 0;
				$Total_rec    = 0;
				$Total_exe    = 0;
				
				
				
				// $html .="<br><br><table width='100%'  style='border: 2px solid #999999;background-color: #ffffff;  padding:10px;'>
									// <tr class='encabezadoTabla' >
										// <td width='15%' align='center'>Concepto</td>
										// <td width='25%' align='center'>Descripcion del cargo</td>
										// <td width='10%' align='center'>Reconocido</td>
										// <td width='10%' align='center'>Excedente</td>
										// <td width='10%' align='center'>Total</td>
										// <td width='30%' align='center'>Entidad</td>
									// </tr>";
				
				if($ColorFila == 'fila1')
					$ColorFila = 'fila2';
				else
					$ColorFila = 'fila1';
				
				//-----Estancia
				if($forma_estado=='' || $forma_estado=='resumido' )
				{
					// consulto el nombre del concepto y su clasificacion
					$q 	= "SELECT Grudes,Gruccf
							 FROM ".$wbasedato."_000200
							WHERE Grucod = '".$wconceptoestancia."' ";

					$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$row = mysql_fetch_array($res);

					//------ wnconceptoestancia  variable  que contiene el nombre del concepto
					$wnconceptoestancia = $row['Grudes'];
					$num_responsables = count($vector_resp['topes']);
					$array_responsables_valor = array();
					/*
					for($u=1; $u<=$num_responsables;$u++)
					{
						foreach ($vector_resp['valorReconocido'][$vector_resp['topes'][$u]['codigo_responsable']] as  $key => $valor)
						{
							$array_responsables_valor[$vector_resp['topes'][$u]['codigo_responsable']] = $valor;
						}
					}
					
					for($u=1; $u<=$num_responsables;$u++)
					{
						// $html.="<tr class='".$ColorFila."'>";
						// $html.="<td >".$wconceptoestancia." ".$wnconceptoestancia." </td>";
						// $html.="<td></td>";
						// $html.="<td align='right'>".number_format($array_responsables_valor[$vector_resp['topes'][$u]['codigo_responsable']],0,',','.' )."</td>";
						// $html.="<td align='right'>".number_format($vector_resp['valorTotalExcedente'],0,',','.' )."</td>";
						// $html.="<td align='right'>".number_format($array_responsables_valor[$vector_resp['topes'][$u]['codigo_responsable']],0,',','.' )."</td>";
						// $html.="<td>".$vector_resp['topes'][$u]['codigo_responsable']."-".$vector_resp['topes'][$u]['nombre_responsable']."</td>";
						// $html.="</tr>";
						$Total_r_e = $Total_r_e + $array_responsables_valor[$vector_resp['topes'][$u]['codigo_responsable']];
						$Total_rec = $Total_rec + $array_responsables_valor[$vector_resp['topes'][$u]['codigo_responsable']];
						$Total_exe = $Total_exe + $vector_resp['valorTotalExcedente'];
					}*/
					
				}
				
				//--------------------------------
				//-----Cargos en general
				
				$array_paq_cargados 	= array();
				$array_cuenta_cargos	= array();
				$array_cargParalelos	= array();
				$arrayccoNom            = array();
				$arrayentidades 		= array();

				$wbasedato_movhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

				// --> Obtener si el usuario es administrador
				$qAdminUse = " SELECT Cjeadm
								 FROM ".$wbasedato."_000030
								WHERE Cjeusu = '".$wuse."'
								  AND Cjeest = 'on'
				";
				$ResAdminUse = mysql_query($qAdminUse,$conex) or die("Error en el query: ".$qAdminUse."<br>Tipo Error:".mysql_error());
				if($RowAdminUse = mysql_fetch_array($ResAdminUse))
					$useAdministrador = $RowAdminUse['Cjeadm'];
				else
					$useAdministrador = 'off';

				// --> Array de centros de costos por empresa
				$arrayCco = Obtener_array_cco();
				
				$wconceptoestancia = consultarAliasPorAplicacion($conex, $wemp_pmla, 'concepto_estancia');
				// --> Obtener los cargos grabados al paciente
				$q_get_cargos = "
				   SELECT A.id as Registro, Tcarfec, Tcarser, Tcarconcod, Tcarconnom, Tcarprocod, Tcarpronom, Tcartercod, Tcarternom, Empcod, Empnom,
						  Tcartfa, Tcarcan, Tcarvun, Tcarvto, Tcarrec, Tcarfac, Tcarfex, Tcarvre, Tcarvex, Tcarfre, Tcaridp, Tcarpar, Tcarppr,
						  Tcardev, Tcarreg, Tcarusu, Gruinv, Audpol, Audrcu
					 FROM ".$wbasedato."_000106 as A INNER JOIN ".$wbasedato."_000024 AS B ON (A.Tcarres = B.Empcod) INNER JOIN ".$wbasedato."_000107 AS C ON (A.id = C.Audreg), ".$wbasedato."_000200
					WHERE Tcarhis 	= '".$whistoria."'
					  AND Tcaring 	= '".$wing."'
					  AND Tcarconcod   != '".$wconceptoestancia."'
					  AND Tcarest 	= 'on'
					  AND Tcarconcod= Grucod
					  AND Tcarfac ='S'
				 ORDER BY Tcarconnom, Tcarfec DESC, Registro DESC
				";
				/*
				$res_get_cargos = mysql_query($q_get_cargos,$conex) or die("Error en el query: ".$q_get_cargos."<br>Tipo Error:".mysql_error());
				while($row_get_cargos = mysql_fetch_array($res_get_cargos))
				{
					
					
					$inf_cargo['Fecha'] 			= $row_get_cargos['Tcarfec'];
					
					// --> Crear un array con la informacion organizada
					$inf_cargo['CodProcedi']		= $row_get_cargos['Tcarprocod'];
					$inf_cargo['NomProcedi']		= $row_get_cargos['Tcarpronom'];
					
					$inf_cargo['Servicio'] 			= $row_get_cargos['Tcarser'];
					$inf_cargo['NomServicio'] 		= ((array_key_exists($row_get_cargos['Tcarser'], $arrayCco)) ? $arrayCco[$row_get_cargos['Tcarser']] : '?' );
					$inf_cargo['CodTercero']		= $row_get_cargos['Tcartercod'];
					$inf_cargo['NomTercero']		= $row_get_cargos['Tcarternom'];
					$inf_cargo['TipoFact']			= $row_get_cargos['Tcartfa'];
					$inf_cargo['Devolucion']		= $row_get_cargos['Tcardev'];
					$inf_cargo['yaRegrabado']		= $row_get_cargos['Tcarreg'];
					$inf_cargo['Cantidad']			= $row_get_cargos['Tcarcan'];
					$inf_cargo['ValorUn']			= $row_get_cargos['Tcarvun'];
					$inf_cargo['ValorTo']			= $row_get_cargos['Tcarvto'];
					$inf_cargo['ValorRe']			= $row_get_cargos['Tcarvre'];
					$inf_cargo['ValorEx']			= $row_get_cargos['Tcarvex'];
					$inf_cargo['FacturadoReconoci']	= $row_get_cargos['Tcarfre'];
					$inf_cargo['ReconExced']		= $row_get_cargos['Tcarrec'];
					$inf_cargo['Facturable']		= $row_get_cargos['Tcarfac'];
					$inf_cargo['CodUsuario']		= $row_get_cargos['Tcarusu'];
					$inf_cargo['codEntidad']		= $row_get_cargos['Empcod'];
					$inf_cargo['Entidad']			= utf8_encode($row_get_cargos['Empnom']);
					$inf_cargo['FactuExcede']		= $row_get_cargos['Tcarfex'];
					$inf_cargo['FacturadoReconoci']	= $row_get_cargos['Tcarfre'];
					$inf_cargo['ConceptoInventar']	= $row_get_cargos['Gruinv'];
					$inf_cargo['Registro']			= $row_get_cargos['Registro'];
					$inf_cargo['GraboParalelo']		= $row_get_cargos['Tcarpar'];
					$inf_cargo['idParalelo']		= $row_get_cargos['Tcaridp'];
					$inf_cargo['pendienteRevicion']	= $row_get_cargos['Tcarppr'];
					$inf_cargo['politicaAplico']	= $row_get_cargos['Audpol'];
					$facturado						=  'no_facturado' ;

					// --> Obtener la especialidad del tercero
					$inf_cargo['nomEspecialidad'] = '';
					if(trim($inf_cargo['CodTercero']) != '')
					{
						$sqlEspe = " SELECT Espnom
									   FROM ".$wbasedato."_000159, ".$wbasedato_movhos."_000044
									  WHERE Terrel = '".$inf_cargo['Registro']."'
										AND Teresp = Espcod
						";
						$resEspe = mysql_query($sqlEspe, $conex) or die("<b>ERROR EN QUERY MATRIX (sqlEspe):</b><br>".mysql_error());
						if($rowEspe = mysql_fetch_array($resEspe))
							$inf_cargo['nomEspecialidad'] = $rowEspe['Espnom'];
					}

					// --> Obtener el nombre del usuario que grabo el cargo
					$inf_cargo['Usuario'] = '';
					if(trim($inf_cargo['CodUsuario']) != '')
					{
						$sqlNomUsu = "SELECT Descripcion
										FROM usuarios
									   WHERE Codigo = '".$inf_cargo['CodUsuario']."'
						";
						$resNomUsu = mysql_query($sqlNomUsu, $conex) or die("<b>ERROR EN QUERY MATRIX (sqlNomUsu):</b><br>".mysql_error());
						if($rowNomUsu = mysql_fetch_array($resNomUsu))
							$inf_cargo['Usuario'] = $rowNomUsu['Descripcion'];
					}

					$array_cuenta_cargos[$facturado][$row_get_cargos['Tcarconcod']]['NomConcepto'] = $row_get_cargos['Tcarconnom'];
					$array_cuenta_cargos[$facturado][$row_get_cargos['Tcarconcod']]['InfConcepto'][$row_get_cargos['Registro']] = $inf_cargo;

					// --> Array para saber cuales son los cargos grabados en paralelo
					if($row_get_cargos['Tcarpar'] == 'on' && $row_get_cargos['Tcaridp'] > 0)
						$array_cargParalelos[$row_get_cargos['Tcaridp']] = '';
				}*/
				//----------------------------------------------------------------------------
				//-Cargos de Unix
				
				while($row_get_cargos = mysql_fetch_array($res_get_cargos))
				{
					
					
					$inf_cargo['Fecha'] 			= $row_get_cargos['Tcarfec'];
					
					// --> Crear un array con la informacion organizada
					$inf_cargo['CodProcedi']		= $row_get_cargos['Tcarprocod'];
					$inf_cargo['NomProcedi']		= $row_get_cargos['Tcarpronom'];
					
					$inf_cargo['Servicio'] 			= $row_get_cargos['Tcarser'];
					$inf_cargo['NomServicio'] 		= ((array_key_exists($row_get_cargos['Tcarser'], $arrayCco)) ? $arrayCco[$row_get_cargos['Tcarser']] : '?' );
					$inf_cargo['CodTercero']		= $row_get_cargos['Tcartercod'];
					$inf_cargo['NomTercero']		= $row_get_cargos['Tcarternom'];
					$inf_cargo['TipoFact']			= $row_get_cargos['Tcartfa'];
					$inf_cargo['Devolucion']		= $row_get_cargos['Tcardev'];
					$inf_cargo['yaRegrabado']		= $row_get_cargos['Tcarreg'];
					$inf_cargo['Cantidad']			= $row_get_cargos['Tcarcan'];
					$inf_cargo['ValorUn']			= $row_get_cargos['Tcarvun'];
					$inf_cargo['ValorTo']			= $row_get_cargos['Tcarvto'];
					$inf_cargo['ValorRe']			= $row_get_cargos['Tcarvre'];
					$inf_cargo['ValorEx']			= $row_get_cargos['Tcarvex'];
					$inf_cargo['FacturadoReconoci']	= $row_get_cargos['Tcarfre'];
					$inf_cargo['ReconExced']		= $row_get_cargos['Tcarrec'];
					$inf_cargo['Facturable']		= $row_get_cargos['Tcarfac'];
					$inf_cargo['CodUsuario']		= $row_get_cargos['Tcarusu'];
					$inf_cargo['codEntidad']		= $row_get_cargos['Empcod'];
					// if($inf_cargo['codEntidad'] == "")
					// {
						// $inf_cargo['codEntidad'] ='voy vacio';
					// }
					$inf_cargo['Entidad']			= utf8_encode($row_get_cargos['Empnom']);
					$inf_cargo['FactuExcede']		= $row_get_cargos['Tcarfex'];
					$inf_cargo['FacturadoReconoci']	= $row_get_cargos['Tcarfre'];
					$inf_cargo['ConceptoInventar']	= $row_get_cargos['Gruinv'];
					$inf_cargo['Registro']			= $row_get_cargos['Registro'];
					$inf_cargo['GraboParalelo']		= $row_get_cargos['Tcarpar'];
					$inf_cargo['idParalelo']		= $row_get_cargos['Tcaridp'];
					$inf_cargo['pendienteRevicion']	= $row_get_cargos['Tcarppr'];
					$inf_cargo['politicaAplico']	= $row_get_cargos['Audpol'];
					$facturado						=  'no_facturado' ;

					// --> Obtener la especialidad del tercero
					$inf_cargo['nomEspecialidad'] = '';
					if(trim($inf_cargo['CodTercero']) != '')
					{
						$sqlEspe = " SELECT Espnom
									   FROM ".$wbasedato."_000159, ".$wbasedato_movhos."_000044
									  WHERE Terrel = '".$inf_cargo['Registro']."'
										AND Teresp = Espcod
						";
						$resEspe = mysql_query($sqlEspe, $conex) or die("<b>ERROR EN QUERY MATRIX (sqlEspe):</b><br>".mysql_error());
						if($rowEspe = mysql_fetch_array($resEspe))
							$inf_cargo['nomEspecialidad'] = $rowEspe['Espnom'];
					}

					// --> Obtener el nombre del usuario que grabo el cargo
					$inf_cargo['Usuario'] = '';
					if(trim($inf_cargo['CodUsuario']) != '')
					{
						$sqlNomUsu = "SELECT Descripcion
										FROM usuarios
									   WHERE Codigo = '".$inf_cargo['CodUsuario']."'
						";
						$resNomUsu = mysql_query($sqlNomUsu, $conex) or die("<b>ERROR EN QUERY MATRIX (sqlNomUsu):</b><br>".mysql_error());
						if($rowNomUsu = mysql_fetch_array($resNomUsu))
							$inf_cargo['Usuario'] = $rowNomUsu['Descripcion'];
					}

					$array_cuenta_cargos[$facturado][$row_get_cargos['Tcarconcod']]['NomConcepto'] = $row_get_cargos['Tcarconnom'];
					$array_cuenta_cargos[$facturado][$row_get_cargos['Tcarconcod']]['InfConcepto'][$row_get_cargos['Registro']] = $inf_cargo;

					// --> Array para saber cuales son los cargos grabados en paralelo
					if($row_get_cargos['Tcarpar'] == 'on' && $row_get_cargos['Tcaridp'] > 0)
						$array_cargParalelos[$row_get_cargos['Tcaridp']] = '';
				}
				
				///-
				// cargos en unix
				 
				foreach ($cargos_unix_mostrar as $t => $value)
				{
						$inf_cargo['CodProcedi']		=  $cargos_unix_mostrar[$t]['CodProcedi'];
						$inf_cargo['NomProcedi']		=  $cargos_unix_mostrar[$t]['NomProcedi'];
						$inf_cargo['Fecha'] 			=  $cargos_unix_mostrar[$t]['Fecha'];
						$inf_cargo['Servicio'] 			=  $cargos_unix_mostrar[$t]['Servicio'] ;
						$inf_cargo['NomServicio'] 		=  $cargos_unix_mostrar[$t]['NomServicio']; 	
						$inf_cargo['CodTercero']		=  $cargos_unix_mostrar[$t]['CodTercero'];
						$inf_cargo['NomTercero']		=  $cargos_unix_mostrar[$t]['NomTercero'];
						$inf_cargo['TipoFact']			=  $cargos_unix_mostrar[$t]['TipoFact'];	
						$inf_cargo['Devolucion']		=  $cargos_unix_mostrar[$t]['Devolucion'];
						$inf_cargo['yaRegrabado']		=  $cargos_unix_mostrar[$t]['yaRegrabado'];
						$inf_cargo['Cantidad']			=  $cargos_unix_mostrar[$t]['Cantidad'];
						$inf_cargo['ValorUn']			=  $cargos_unix_mostrar[$t]['ValorUn'];	
						
						$inf_cargo['ValorRe']			=  $cargos_unix_mostrar[$t]['ValorRe'];
						$inf_cargo['ValorEx']			=  $cargos_unix_mostrar[$t]['ValorEx'];
						$inf_cargo['ValorTo']			=  $cargos_unix_mostrar[$t]['ValorTo'];
						$inf_cargo['FacturadoReconoci']	=  $cargos_unix_mostrar[$t]['FacturadoReconoci'];	
						$inf_cargo['ReconExced']		=  $cargos_unix_mostrar[$t]['ReconExced'];
						$inf_cargo['Facturable']		=  $cargos_unix_mostrar[$t]['Facturable'];
						$inf_cargo['CodUsuario']		=  $cargos_unix_mostrar[$t]['CodUsuario'];
						$inf_cargo['codEntidad']		=  $cargos_unix_mostrar[$t]['codEntidad'];
						$inf_cargo['Entidad']			=  $cargos_unix_mostrar[$t]['Entidad'];
						$inf_cargo['FactuExcede']		=  $cargos_unix_mostrar[$t]['FactuExcede'];
						$inf_cargo['FacturadoReconoci']	=  $cargos_unix_mostrar[$t]['FacturadoReconoci'];
						$inf_cargo['ConceptoInventar']	=  $cargos_unix_mostrar[$t]['ConceptoInventar'];
						$inf_cargo['Registro']			=  $cargos_unix_mostrar[$t]['Registro'];	
						$inf_cargo['GraboParalelo']		=  $cargos_unix_mostrar[$t]['GraboParalelo'];
						$inf_cargo['idParalelo']		=  $cargos_unix_mostrar[$t]['idParalelo'];
						$inf_cargo['pendienteRevicion']	=  $cargos_unix_mostrar[$t]['pendienteRevicion'];	
						$inf_cargo['politicaAplico']	=  $cargos_unix_mostrar[$t]['politicaAplico'];
						$facturado		              	= 'no_facturado';
							
						if(!$arraynomconcepto[$cargos_unix_mostrar[$t]['Codconcepto']])
						{
							$sqlArt = "
											SELECT Consim, Connom
											  FROM ".$wbasedato."_000197
											 WHERE Conest = 'on'
											   AND Congen ='".$cargos_unix_mostrar[$t]['Codconcepto']."' 
											";
							$resArt = mysql_query($sqlArt, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlArt):</b><br>".mysql_error());
							while($rowArt = mysql_fetch_array($resArt))
							{
								$arraynomconcepto[$cargos_unix_mostrar[$t]['Codconcepto']]['nombre'] = utf8_encode(trim($rowArt['Connom']));
								$arraynomconcepto[$cargos_unix_mostrar[$t]['Codconcepto']]['codigo'] = utf8_encode(trim($rowArt['Consim']));
							}
						}	 	
						
						if($cargos_unix_mostrar[$t]['NomProcedi']=='s')
						{
							if(!$arraynomprocedimiento[$cargos_unix_mostrar[$t]['CodProcedi']])
							{
								$sqlArt = "
												SELECT Pronom , Procod
												  FROM ".$wbasedato."_000103
												 WHERE Procod ='".$cargos_unix_mostrar[$t]['CodProcedi']."' ";
												   
								$resArt = mysql_query($sqlArt, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlArt):</b><br>".mysql_error());
								while($rowArt = mysql_fetch_array($resArt))
								{
									$arraynomprocedimiento[$cargos_unix_mostrar[$t]['CodProcedi']] = utf8_encode(trim($rowArt['Pronom']));;
								}
							}	 	
							$inf_cargo['NomProcedi']		=  $arraynomprocedimiento[$cargos_unix_mostrar[$t]['CodProcedi']];
						}
						else
						{
							$inf_cargo['NomProcedi']		=  $cargos_unix_mostrar[$t]['NomProcedi'];
						}
						
						

							$array_cuenta_cargos[$facturado][$arraynomconcepto[$cargos_unix_mostrar[$t]['Codconcepto']]['codigo']]['NomConcepto'] = $arraynomconcepto[$cargos_unix_mostrar[$t]['Codconcepto']]['nombre'];
							$array_cuenta_cargos[$facturado][$arraynomconcepto[$cargos_unix_mostrar[$t]['Codconcepto']]['codigo']]['InfConcepto'][] = $inf_cargo;							
					
				}
				
				//-------------
				//entro y guardo en el vector el de pension
				for($u=1; $u<=$num_responsables;$u++)
				{
					
					//echo $vector_resp['topes'][$u]['codigo_responsable'];
					if(isset($vector_resp['topes'][$u]) && isset($vector_resp['valorReconocido'][$vector_resp['topes'][$u]['codigo_responsable']]) )
					{
						foreach ($vector_resp['valorReconocido'][$vector_resp['topes'][$u]['codigo_responsable']] as $k => $value )
						{
							// foreach($k)
							$inf_cargo['CodProcedi']		= $k;
							$inf_cargo['NomProcedi']		= $vectorhabitacion[$k];
							$inf_cargo['Fecha'] 			= $wfecha=date("Y-m-d");
							$inf_cargo['Fecha'] 			= $wfecha=$arr_variables['wdia_ingreso'];
							$inf_cargo['Servicio'] 			= '';
							$inf_cargo['NomServicio'] 		= '';
							$inf_cargo['CodTercero']		= '';
							$inf_cargo['NomTercero']		= '';
							$inf_cargo['TipoFact']			= '';
							$inf_cargo['Devolucion']		='';
							$inf_cargo['yaRegrabado']		= '';
							$inf_cargo['Cantidad']			= $vector_resp['valorReconocido'][$vector_resp['topes'][$u]['codigo_responsable']][$k]['cantidad'];
							
							
							$inf_cargo['ValorRe']			= $vector_resp['valorReconocido'][$vector_resp['topes'][$u]['codigo_responsable']][$k]['valor'];
							//if($u==$num_responsables)
							//{
								$inf_cargo['ValorEx']			= $vector_resp['valorTotalExcedente'][$k];
								
							//}
							//else
							//{
								//$inf_cargo['ValorEx']			= 0;
						//	}
							
							$valtarifa2 = datos_desde_procedimiento($k, $wconceptoestancia,'*'	, '*',$vector_resp['topes'][$u]['codigo_responsable'],$inf_cargo['Fecha'],'', '*', 'on', false, '', $inf_cargo['Fecha'] , date("H:i:s"));
							$inf_cargo['ValorUn'] = $valtarifa2['wvaltar'];
							// $inf_cargo['ValorUn']			= ((($inf_cargo['ValorEx'] + $inf_cargo['ValorRe'])*1)/(($inf_cargo['Cantidad']) *1));
							// $inf_cargo['ValorUn']			= $vector_resp['valorTotalEstancia'][$k];
							// $inf_cargo['ValorUn']			= $vector_resp['pension'][$u]['tarifa']."-".$vector_resp['topes'][$u]['tipo_unico']."--".$inf_cargo['Fecha'];
							// $inf_cargo['ValorUn']			= $vector_resp['pension'][$u]['valor_habitacion_final'];
							$inf_cargo['ValorTo']			= $inf_cargo['ValorRe'] + $inf_cargo['ValorEx'];
							$inf_cargo['FacturadoReconoci']	= '';
							$inf_cargo['ReconExced']		= '';
							$inf_cargo['Facturable']		= 'S';
							$inf_cargo['CodUsuario']		= '';
							//$inf_cargo['codEntidad']		= $vector_resp['topes'][$u]['codigo_responsable']."--".$vector_resp['pension'][$u]['tipo_unico']."--".$inf_cargo['Fecha']."--".print_r($vector_resp['pension'])."---".print_r($vector_resp['topes']);
							$inf_cargo['codEntidad']		= $vector_resp['topes'][$u]['codigo_responsable'];
							$inf_cargo['Entidad']			= utf8_encode($vector_resp['topes'][$u]['nombre_responsable']);
							$inf_cargo['FactuExcede']		= $row_get_cargos['Tcarfex'];
							$inf_cargo['FacturadoReconoci']	= $row_get_cargos['Tcarfre'];
							$inf_cargo['ConceptoInventar']	= $row_get_cargos['Gruinv'];
							$inf_cargo['Registro']			= $row_get_cargos['Registro'];
							$inf_cargo['GraboParalelo']		= $row_get_cargos['Tcarpar'];
							$inf_cargo['idParalelo']		= '';
							$inf_cargo['pendienteRevicion']	= '';
							$inf_cargo['politicaAplico']	= '';
							$facturado						=  'no_facturado' ;
							
							$array_cuenta_cargos[$facturado][$wconceptoestancia]['NomConcepto'] = $wnconceptoestancia;
							$array_cuenta_cargos[$facturado][$wconceptoestancia]['InfConcepto'][] = $inf_cargo;
						
						}
					}
					
				}
				//----------
				
				// --> Pintar informacion
				
				
				$respbotones.="<input type='hidden' id='estadoCuenta' value='1'><table width='100%'><tr >
				<td width='5%' align='right'>
				<button style='cursor:pointer;font-family: verdana;font-weight:bold;font-size: 8pt;' onClick='resumido()' >
							RESUMIDO
					</button>
				</td>
				<td width='5%' align='right'>
				<button style='cursor:pointer;font-family: verdana;font-weight:bold;font-size: 8pt;' onClick='detallado()' >
							DETALLADO
					</button>
				</td>
				<td width='8%' align='right' nowrap=nowrap>
					<button style='cursor:pointer;font-family: verdana;font-weight:bold;font-size: 8pt;' onClick='imprimirSoporte( \"".$whistoria."\", \"".$wing."\")' >
						<img src='../../images/medical/sgc/Printer.png' width='12px' height='12px'>
							IMPRIMIR
					</button>
				</td>
				</tr>
				</table>";
				
				
				
				
				$resp.= "<br>
				<table width='100%' id='detalleCargoppal' align='center'>
				";
				
				$respresumido.= "<br><br>
				<table width='100%' id='detalleCargo' class='doted' align='center' style='border:1px;border-style:dotted none dotted none;'>
				";
				
				$respdetallado.= "<br><br>
				<table width='100%' id='detalleCargo' class='doted' align='center' style='border:1px;border-style:dotted none dotted none;'>
				";
				
				$toltGra = "<font style=\"font-weight:normal\">&nbsp;<img width=\"12\" height=\"12\" src=\"../../images/medical/root/info.png\">&nbsp;Cargo grabado&nbsp;</font>";
				$toltAnu = "<font style=\"font-weight:normal\">&nbsp;<img width=\"12\" height=\"12\" src=\"../../images/medical/root/info.png\">&nbsp;Anular&nbsp;</font>";
				$toltReg = "<font style=\"font-weight:normal\">&nbsp;<img width=\"12\" height=\"12\" src=\"../../images/medical/root/info.png\">&nbsp;Regrabar&nbsp;</font>";
				
				// --> No existen cargos en la cuenta del paciente
				if(count($array_cuenta_cargos) == 0)
				{
					$resp.= "
					<tr>
						<td colspan='17' align='center' style='font-size: 9pt;font-family: verdana;font-weight:bold'>
							<img width='15' height='15' src='../../images/medical/sgc/info.png'>
							No existen cargos grabados al paciente.
						</td>
					</tr>";
				}
				
				foreach($array_cuenta_cargos as $tipoFact => $arrCoceptos)
				{
					// --> Barra de cargos facturados o no facturados
					
					
					
					$resp.="
					<tr align='center'  style='display:none' class='".$tipoFact." conceptos'  width='100%' onclick='detalladoporconcepto(\"nose\")'>
						
						<td class='procedimientoEncabezado '  width='10%' ><div  style='display:none'>Fecha </div></td>
						<td class='procedimientoEncabezado'  width='40%'><div  style='display:none' >Procedimiento</div></td>
						<td class='procedimientoEncabezado'  width='10%'><div  style='display:none'  >C.Costos</div></td>
						<td class='procedimientoEncabezado'  width='20%'><div  style='display:none'   >Entidad</div></td>
						<td class='procedimientoEncabezado'  width='5%'><div  style='display:none'  >Cantidad</div></td>
						<td class='procedimientoEncabezado'  width='10%'><div  style='display:none' >Valor. Uni</div></td>
						<td class='procedimientoEncabezado valorextra'  width='10%'><div  style='display:none'  >Valor extra</div></td>
						<td class='encabezadoTabla'  width='10%' nowrap='nowrap'>Valor. Rec</td>
						
						<td class='encabezadoTabla'  width='10%'  nowrap='nowrap'>Valor. Exc</td>
						<td class='encabezadoTabla'  width='10%'  nowrap='nowrap'>Valor. Tot</td>
						<td  nowrap = nowrap ></td>
						
					</tr>";
					
					$respresumido.="
					<tr align='center' class='doted2'   width='100%'   >
						<td  width='10%'>&nbsp;</td>
						<td  width='40%'>&nbsp;</td>
						<td  width='10%'>&nbsp;</td>
						<td  width='5%'>&nbsp;</td>
						<td  width='5%'>&nbsp;</td>
						<td  width='5%'>&nbsp;</td>
						<td  width='10%'>&nbsp;</td>
						<td  width='10%'>&nbsp;</td>
						<td  width='10%'>&nbsp;<td>
					
						
					</tr>";
					$respresumido.="
					<tr align='center' class='doted2'   width='100%'   >
						<td  width='10%'>&nbsp;</td>
						<td  width='40%'>&nbsp;</td>
						<td  width='10%'>&nbsp;</td>
						<td  width='5%'>&nbsp;</td>
						<td  width='5%'>&nbsp;</td>
						<td  width='5%'>&nbsp;</td>
						<td  width='10%'>&nbsp;</td>
						<td  width='10%'>&nbsp;</td>
						<td  width='10%'>&nbsp;</td>
						<td class='encabezadoTabla'  nowrap='nowrap' width='40%' colspan='4'><b>Valores</b></td>	
					</tr>";
					
					
					$respresumido.="
					<tr align='center' class='doted2'   width='100%'   >
						<td  width='10%'>&nbsp;</td>
						<td  width='40%'>&nbsp;</td>
						<td  width='10%'>&nbsp;</td>
						<td  width='5%'>&nbsp;</td>
						<td  width='5%'>&nbsp;</td>
						<td  width='5%'>&nbsp;</td>
						<td  width='10%'>&nbsp;</td>
						<td  width='10%'>&nbsp;</td>
						<td  width='10%'>&nbsp;</td>
						<td class='encabezadoTabla'  nowrap='nowrap' width='10%'><b>Reconocido</b></td>
						<td class='encabezadoTabla valorextra'   nowrap='nowrap' width='10%' style='display:none'><b>Extra</b></td>
						<td class='encabezadoTabla'  nowrap=nowrap width='10%'><b>Excedente</b></td>
						<td class='encabezadoTabla'  nowrap=nowrap width='10%'><b>Total</b></td>
						
					</tr>";
					
					$respdetallado.="
					<tr align='center' class='doted2'   width='100%'   >
						<td  width='10%'>&nbsp;</td>
						<td  width='40%'>&nbsp;</td>
						<td  width='10%'>&nbsp;</td>
						<td  width='5%'>&nbsp;</td>
						<td  width='5%'>&nbsp;</td>
						<td  width='5%'>&nbsp;</td>
						<td  width='10%'>&nbsp;</td>
						<td  width='10%'>&nbsp;</td>
						<td  width='10%'>&nbsp;<td>
					
						
					</tr>";
					$respdetallado.="
					<tr align='center' class='doted2'   width='100%'   >
						<td class='procedimientoEncabezado'  width='10%' ><b>Fecha</b></td>
						<td class='procedimientoEncabezado'  width='40%'><b>Procedimiento</b></td>
						<td class='procedimientoEncabezado'  width='10%'><b>C.Costos</b></td>
						<td class='procedimientoEncabezado'  width='5%'><b>Entidad</b></td>
						<td class='procedimientoEncabezado'  width='5%'><b>Cantidad</b></td>
						<td class='procedimientoEncabezado'  width='10%'><b>Valor Unitario</b></td>
						<td class='encabezadoTabla'  nowrap=nowrap width='10%'><b>Reconocido</b></td>
						<td class='encabezadoTabla'  nowrap=nowrap width='10%'><b>Excedente</b></td>
						<td class='encabezadoTabla'  nowrap=nowrap width='10%'><b>Total</b></td>
						
					</tr>";
					
				 

					$Total_rec_cuenta = 0;
					$Total_exe_cuenta = 0;
					$Total_r_e_cuenta = 0;
					$contadorid		= 0;
					foreach($arrCoceptos as $codConcepto => $arrInfoConceptos)
					{
						$Total_rec = 0;
						$Total_exe = 0;
						
						
						$queryconcepto 	= "SELECT Grudes  FROM ".$wbasedato."_000200  WHERE  Grucod = '".$codConcepto."' ";
						
						$resconcepto 	= mysql_query($queryconcepto, $conex) or die("<b>ERROR EN QUERY MATRIX(queryconcepto):</b><br>".mysql_error());
						$auxnombreconcepto ='';
						if($rowconcepto = mysql_fetch_array($resconcepto))
						{
							$auxnombreconcepto = $rowconcepto['Grudes'];
						}
				
					
						// --> Barra del nombre del concepto
						$resp.="
						<tr  class='".$tipoFact." conceptos'  width='100%'    >
							
							<td  onClick='detalladoporconcepto(\"".$codConcepto."\")'  width='70%' colspan='6' style='font-weight:bold;background-color:#D7EBF9;color:#2779AA;cursor:pointer; border-radius:4px; border: 1px solid #2779AA;'>
							".$codConcepto."-".(($auxnombreconcepto=='') ? $arrInfoConceptos['NomConcepto'] : $auxnombreconcepto)." 
							</td>
							";
							
					
						//------------
						/*$respdetallado.="
						<tr align='left' class='".$tipoFact." conceptos'  width='100%'  >
							
							<td   width='70%' colspan='6' >
							".$codConcepto."-".$arrInfoConceptos['NomConcepto']."
							</td>
							<td  align='right'>
							remplazarValorReconocido
							</td>";*/
						//-------------------	
							
							if($codConcepto == $wconceptoestancia)
							{
								$resp.="<td id='valorextra_".$codConcepto."' class='valorextra' style='font-weight:bold;background-color:#D7EBF9;color:#2779AA; border-radius:4px; border: 1px solid #2779AA; display:none' align='right'>
										<input type='text' id='extraestancia' size='10' class='miles' onchange='cambiarvalorextra(\"".$codConcepto."\")' style = 'text-align: right;'  value=''>
								
										</td>
										<td id='idreconocidoestancia'  valor='aux2hospitalizacion' style='font-weight:bold;background-color:#D7EBF9;color:#2779AA; border-radius:4px; border: 1px solid #2779AA;' align='right'>
										<div id='divValorReconocido_".$codConcepto."'>remplazarValorReconocido</div>
										</td>";
								
							
								$resp.="
								<td style='font-weight:bold;background-color:#D7EBF9;color:#2779AA; border-radius:4px; border: 1px solid #2779AA;' align='right'>
								<input type='text' id='excedenteHospitalizacion' size='10' class='miles' onchange='cambiarvalorexcedente(\"".$codConcepto."\")' style = 'text-align: right;' disabled  value='remplazarValorExcedente'>
								</td>";
								$resp.="
								<td style='font-weight:bold;background-color:#D7EBF9;color:#2779AA; border-radius:4px; border: 1px solid #2779AA;' id='TotalHospitalizacion'  valor='remplazarValoraux' align='right'>
								remplazarValorTotal
								</td>";
								//-------------
								/*$respdetallado .="
												<td  align='right'>
												<input type='text' id='excedenteHospitalizacion'  style = 'text-align: right;'  value='remplazarValorExcedente'>
												</td>";*/
								//----------------
							}
							else
							{
								
								$resp.="<td id='valorextra_".$codConcepto."' class='valorextra' style='font-weight:bold;background-color:#D7EBF9;color:#2779AA; border-radius:4px; border: 1px solid #2779AA; display:none' align='right'>
										
										</td>
										<td style='font-weight:bold;background-color:#D7EBF9;color:#2779AA; border-radius:4px; border: 1px solid #2779AA;' align='right'>
										<div id='divValorReconocido_".$codConcepto."'>remplazarValorReconocido</div>
										</td>";
										
								
								
								
								$resp.="
								<td style='font-weight:bold;background-color:#D7EBF9;color:#2779AA; border-radius:4px; border: 1px solid #2779AA;' align='right'>
								<div id='divValorExcedente_".$codConcepto."'>remplazarValorExcedente</div>
								</td>";
								
								$resp.="
								<td style='font-weight:bold;background-color:#D7EBF9;color:#2779AA; border-radius:4px; border: 1px solid #2779AA;'   align='right'>
								remplazarValorTotal
								</td>";
								
								//-----------------
								/*$respdetallado .="
								<td align='right'>
								remplazarValorExcedente
								</td>";*/
								//-------------------
							}
							
							// linea del centro
						
						if($codConcepto != $wconceptoestancia)
						{
							$resp.="
							<td align='left'  nowrap = nowrap>
							<input type='hidden' id='ValorRCambio_".$codConcepto."' value='remplazarValorRCambio'><input type='hidden' id='ValorECambio_".$codConcepto."' value='remplazarValorECambio'>
							<img class='recorrerporcentaje'  concepto='".$codConcepto."' id='imgexcedente_".$codConcepto."' tooltip='si'  valor ='si' onclick='enviarExedente( \"".$codConcepto."\")' title ='enviar valor al excedente'  width='15' height='15' src='../../images/medical/sgc/atras.png '  style='cursor:pointer' />&nbsp;&nbsp<img  tooltip='si'  onclick='reestablecerValores( \"".$codConcepto."\")' title ='Reestablecer valores' width='15' height='15' src='../../images/medical/sgc/Refresh-128.png' style='cursor:pointer'>&nbsp;&nbsp<img tooltip='si'  id='imgreconocido_".$codConcepto."'  valor ='no' onclick='enviarReconocido( \"".$codConcepto."\")' title ='enviar valor al reconocido' width='15' height='15' src='../../images/medical/sgc/adelante.png'  style='cursor:pointer' />
							</td></tr>";
						
						}
						else
						{
							$resp.="
							<td align='left'  nowrap = nowrap >
							<input type='hidden' id='ValorRCambio_".$codConcepto."' value='remplazarValorRCambio'><input type='hidden' id='ValorECambio_".$codConcepto."' value='remplazarValorECambio'>
							<img class='recorrerporcentaje'  concepto='".$codConcepto."' id='imgexcedente_".$codConcepto."' tooltip='si'  valor ='si' onclick='enviarExedente( \"".$codConcepto."\")' title ='enviar valor al excedente'  width='15' height='15' src='../../images/medical/sgc/atras.png '/>&nbsp;&nbsp<img  tooltip='si'  onclick='reestablecerValores( \"".$codConcepto."\")' title ='Reestablecer valores' width='15' height='15' src='../../images/medical/sgc/Refresh-128.png'>&nbsp;&nbsp<img tooltip='si'  id='imgreconocido_".$codConcepto."'  valor ='no' onclick='enviarReconocido( \"".$codConcepto."\")' title ='enviar valor al reconocido' width='15' height='15' src='../../images/medical/sgc/adelante.png'/>
							<input type='checkbox'  style='cursor:pointer' id='habiliatarpagoextra' title='Pago extra' onclick='vercajonextra(\"".$codConcepto."\")'>Pago extra</td></tr>";
						}
						//----------------						
							
							$resp.="
								<tr style='display : none' align='center' class='".$tipoFact." conceptos procedimiento procedimiento_".$codConcepto."'  width='100%' onclick='detalladoporconcepto(\"nose\")'>
							
							<td class='encabezadoTabla '  width='10%' ><div>Fecha </div></td>
							<td class='encabezadoTabla'  width='40%'><div>Procedimiento</div></td>
							<td class='encabezadoTabla'  width='10%'><div>C.Costos</div></td>
							<td class='encabezadoTabla'  width='20%'><div>Entidad</div></td>
							<td class='encabezadoTabla'  width='5%'><div>Cantidad</div></td>
							<td class='encabezadoTabla'  width='10%'><div>Valor. Uni</div></td>
							<td class='encabezadoTabla valorextra'  width='10%' style='display:none'><div >Valor extra</div></td>
							<td class='encabezadoTabla'  width='10%' nowrap='nowrap'>Valor. Rec</td>
							
							<td class='encabezadoTabla'  width='10%'  nowrap='nowrap'>Valor. Exc</td>
							<td class='encabezadoTabla'  width='10%'  nowrap='nowrap'>Valor. Tot</td>
							<td  nowrap = nowrap ></td>
							
						</tr>";	
						
						//--------------------
						/*$respdetallado.="
							<td align='right'>
							remplazarValorTotal
							</td>
						</tr>";*/
						//-------------------------
						
							
							
							// $respdetallado .="<tr align='left'  width='100%'  class='doted2' >
							// <td   width='70%' colspan='9'>
							// ".$codConcepto."-".$arrInfoConceptos['NomConcepto']."
							// </td>
							// <td align='right'>
							// remplazarValorReconocido
							// </td>
							// <td  align='right'>";
							
							
							if($codConcepto == $wconceptoestancia)
							{
								$respresumido .= "<tr align='left'  width='100%'  class='doted2' >
									<td   width='70%' colspan='9'>
									".$codConcepto."-".$arrInfoConceptos['NomConcepto']."
									</td>
									<td align='right'><div id='div2ValorReconocido_".$codConcepto."'>
									remplazarValorReconocido</div>
									</td>
									<td align='right' class='valorextra' style='display:none'><div id='valorextraresumido'>
									</div>
									</td>
									<td  align='right'>";
								
								$respresumido.="<div id='divValorexcedenteestancia'>
										remplazarValorExcedente</div>";
								$respresumido.="
												</td>
												<td align='right' id='resumidovalortotalestancia'>
												remplazarValorTotal
												</td>";
							}
							else
							{
								$respresumido .= "<tr align='left'  width='100%'  class='doted2' >
									<td   width='70%' colspan='9'>
									".$codConcepto."-".$arrInfoConceptos['NomConcepto']."
									</td>
									<td align='right'><div id='div2ValorReconocido_".$codConcepto."'>
									remplazarValorReconocido</div>
									</td>
									<td align='right' class='valorextra' style='display:none'><div >
									</div>
									</td>
									<td  align='right'>";
								
								$respresumido.="<div id='div2ValorExcedente_".$codConcepto."'>remplazarValorExcedente</div>";
								$respresumido.="
												</td>
												<td align='right'>
												remplazarValorTotal
												</td>";
							}
							
						$respresumido.="	
						</tr>";
						
						

						$ColorFila 		= 'fila1';
						
						foreach($arrInfoConceptos['InfConcepto'] as $idRegistro => $variables)
						{
							
							$contadorid ++;
							if(!array_key_exists($idRegistro, $array_cargParalelos))
							{
								if($ColorFila == 'fila1')
									$ColorFila = 'fila2';
								else
									$ColorFila = 'fila1';

								

								$resp.="
								<tr  class='".$tipoFact." ".$tipoFact.'-'.$codConcepto." procedimiento procedimiento_".$codConcepto."' style='display:none' >";
								//----------
								$respdetallado.="
								<tr class='".$tipoFact." ".$tipoFact.'-'.$codConcepto." procedimiento procedimiento_".$codConcepto."' style='display:none' >";
								//----------------------------
								// --> Si el cargo tiene un paralelo relacionado
								if($variables['GraboParalelo'] == 'on' && array_key_exists($variables['idParalelo'], $array_cargParalelos))
								{
									$pintarParalelo = true;
								}
								else
								{
									

									$pintarParalelo = false;
								}
								// --> Tooltip para la especialidad
								$toolEspe = "";
								if($variables['nomEspecialidad'] != '')
								{
									
								}

							

								if(!$arrayccoNom[$variables['Servicio']])
								{
									$sql_ccoNom = "SELECT Ccocod , Cconom 
													 FROM ".$wbasedato_movhos."_000011 
													WHERE Ccocod = '".$variables['Servicio']."'";
													
									$resccoNom = mysql_query($sql_ccoNom, $conex) or die("<b>ERROR EN QUERY MATRIX(sql_ccoNom):</b><br>".mysql_error());
									while($rowccoNom = mysql_fetch_array($resccoNom))
									{
										$arrayccoNom[$rowccoNom['Ccocod']] = utf8_encode(trim($rowccoNom['Cconom']));
									}
								}
								
								
								if(!$arrayentidades[$variables['codEntidad']])
								{
									$sqlnomentidad = "SELECT  Empnom 
														FROM ".$wbasedato."_000024  
													   WHERE  Empcod = '".$variables['codEntidad']."'";
									$resempresa = mysql_query($sqlnomentidad, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlnomentidad):</b><br>".mysql_error());
					
									while($rowempresa = mysql_fetch_array($resempresa))
									{
										$arrayentidades[$variables['codEntidad']] = utf8_encode(trim($rowempresa['Empnom']));
									}
								
								}
								
								
								
								if($arrayentidades[$variables['codEntidad']]=='')
								{
									$arrayentidades[$variables['codEntidad']] = 'particular'.$variables['codEntidad']."aa";
								}
								if($codConcepto == $wconceptoestancia)
								{
									
									
									//--------Busco si tiene un 70 - 30 
									
									$selectTopeEstancia = "SELECT * FROM cliame_000204 WHERE Tophis='".$whistoria."'  AND Toping='".$wing."' AND Toptco ='04' ";
									
									
									$resTope = mysql_query($selectTopeEstancia,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$selectTopeEstancia." - ".mysql_error());
									$tope = 0;
									if($rowTope = mysql_fetch_array($resTope))
									{
										$tope = $rowTope['Toptop'];
										
									}
									
									$resp.="
										<td class='".$ColorFila."' align='center'>".$variables['Fecha']."</td>
										<td class='".$ColorFila."' style='font-size: 8pt;font-family: verdana;' >".$variables['CodProcedi']."-".$variables['NomProcedi']."</td>
										<td class='".$ColorFila."'   >".$variables['Servicio']."-".$arrayccoNom[$variables['Servicio']]."</td>
										<td class='".$ColorFila."' align='center'>".$variables['codEntidad']."-".$arrayentidades[$variables['codEntidad']]."</td>
										<td class='".$ColorFila."' align='center'>".$variables['Cantidad']."</td>
										<td class='".$ColorFila." tdvalorunitarioestancia' valororiginal ='".$variables['ValorUn']."' align='right'>".@number_format($variables['ValorUn'],0,'.',',' )."</td>
										<td class='".$ColorFila." valorextra valorestra_".$codConcepto."'   id='valorextrad_".$codConcepto."' style='display:none' align='right'></td>
										<td class='".$ColorFila." tdvalorreconocidoestancia' valor='".$variables['ValorRe']."' valororiginal ='".$variables['ValorRe']."' align='right' id='tdreconocidodetalle".$contadorid."' >".number_format($variables['ValorRe'],0,'.',',' )."</td>";
										
										
										//aqui
										
										//---------------------	

										$respdetallado.="
										<td class='".$ColorFila."' align='center'>".$variables['Fecha']."</td>
										<td class='".$ColorFila."' >".$variables['CodProcedi']."-".$variables['NomProcedi']."</td>
										<td class='".$ColorFila."'   >".$variables['Servicio']."-".$arrayccoNom[$variables['Servicio']]."</td>
										<td class='".$ColorFila."' align='center'>".$variables['codEntidad']."</td>
										<td class='".$ColorFila."' align='center'>".$variables['Cantidad']."</td>
										<td class='".$ColorFila."' align='right'>".@number_format($variables['ValorUn'],0,'.',',' )."</td>
										<td class='".$ColorFila." tdvalorreconocidoestancia2' id='tdreconocidodetalle2_".$contadorid."' align='right'>".number_format($variables['ValorRe'],0,'.',',' )."</td>";										
						
								}
								else
								{
									$resp.="
										<td class='".$ColorFila."' align='center'>".$variables['Fecha']."</td>
										<td class='".$ColorFila."' style='font-size: 8pt;font-family: verdana;' >".$variables['CodProcedi']."-".$variables['NomProcedi']."</td>
										<td class='".$ColorFila."'   >".$variables['Servicio']."-".$arrayccoNom[$variables['Servicio']]."</td>
										<td class='".$ColorFila."' align='center'>".$variables['codEntidad']."-".$arrayentidades[$variables['codEntidad']]."</td>
										<td class='".$ColorFila."' align='center'>".$variables['Cantidad']."</td>
										
										<td class='".$ColorFila."' align='right'>".@number_format($variables['ValorUn'],0,'.',',' )."</td>
										<td class='".$ColorFila." valorextra' id='valorextrad_".$codConcepto."' style='display:none' align='right'></td>
										<td class='".$ColorFila." classtdreconocidodetalle_".$codConcepto."' align='right'  id='tdreconocidodetalle".$contadorid."' >".number_format($variables['ValorRe'],0,'.',',' )."</td>";
										
										//---------------------		
										$respdetallado.="
										<td class='".$ColorFila."' nowrap='nowrap' align='center'>".$variables['Fecha']."</td>
										<td class='".$ColorFila."' >".$variables['CodProcedi']."-".$variables['NomProcedi']."</td>
										<td class='".$ColorFila."'   >".$variables['Servicio']."-".$arrayccoNom[$variables['Servicio']]."</td>
										<td class='".$ColorFila."' align='center'>".$variables['codEntidad']."</td>
										<td class='".$ColorFila."' align='center'>".$variables['Cantidad']."</td>
										<td class='".$ColorFila."' align='right'>".@number_format($variables['ValorUn'],0,'.',',' )."</td>
										<td class='".$ColorFila."' align='right'  id='tdreconocidodetalle2_".$contadorid."' >".number_format($variables['ValorRe'],0,'.',',' )."</td>";
								}
								
								
										
								//---------------------		
										// $wconceptoestancia = consultarAliasPorAplicacion($conex, $wemp_pmla, 'concepto_estancia');
										// if($codConcepto == $wconceptoestancia)
										// {
											// $resp.="<td class='".$ColorFila."' align='right'><input type='text' value='".number_format($variables['ValorEx'],0,',','.' )."'></td>";
										// }
										// else
										// {
								
								if($codConcepto == $wconceptoestancia)
								{
									$resp.="
									<td class='".$ColorFila." tdexcedenteestancia' valor='".$variables['ValorEx']."' align='right'  id='tdexcendentedetalle".$contadorid."'>".number_format($variables['ValorEx'],0,'.',',' )."</td>";
									$respdetallado.="<td class='".$ColorFila." tdexcedenteestancia2' id='tdexcendentedetalle2_".$contadorid."' align='right'>2525".number_format($variables['ValorEx'],0,'.',',' )."</td>";
								
								
								}
								else
								{
									$resp.="
									<td class='".$ColorFila." classtdexcendentedetalle_".$codConcepto."' align='right' id='tdexcendentedetalle".$contadorid."'>".number_format($variables['ValorEx'],0,'.',',' )."</td>";
									$respdetallado.="<td class='".$ColorFila."' id='tdexcendentedetalle2_".$contadorid."' align='right' >".number_format($variables['ValorEx'],0,'.',',' )."</td>";
								
								}
								
								//-----------------------
								//-----------------------
								// }
								
								// Cosulto los conceptos permitidos para cambiar el estado de cuenta detalladamente
								
								$sqlconceptos = "Select ";
								
								$conceptoPermitido 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'conceptoMueveEstadoCuenta'); 
								$varaux = explode(',' , $conceptoPermitido); 
								if(in_array($codConcepto, $varaux))
								{
									$condicion ='';
								}
								else
								{
									$condicion = "style='display:none'";
								}
								
								if($codConcepto == $wconceptoestancia)
								{
									$resp.="<td class='".$ColorFila." tdtotalhospitalizacion'  valororiginal='".$variables['ValorTo']."' align='right' >".number_format($variables['ValorTo'],0,'.',',' )."</td>
									
									<td nowrap='nowrap'><img  ".$condicion." id='imgexcedente_".$codConcepto."' tooltip='si'  valor ='si' onclick='enviarExedentedetalle( \"".$codConcepto."\" , \"".$contadorid."\")' title ='enviar valor al excedente'  width='15' height='15' src='../../images/medical/sgc/atras.png '  style='cursor:pointer' />&nbsp;&nbsp<img   ".$condicion."  tooltip='si'  onclick='reestablecerValoresdetalle( \"".$codConcepto."\" ,  \"".$contadorid."\")' title ='Reestablecer valores' width='15' height='15' src='../../images/medical/sgc/Refresh-128.png' style='cursor:pointer'>&nbsp;&nbsp<img  ".$condicion." tooltip='si'  id='imgreconocido_".$codConcepto."'  valor ='no' onclick='enviarReconocidodetalle( \"".$codConcepto."\" , \"".$contadorid."\")' title ='enviar valor al reconocido' width='15' height='15' src='../../images/medical/sgc/adelante.png'  style='cursor:pointer' /></td>
									<td align='center'><input type='hidden'  class='valorOcultoReconocidoExcedente_".$codConcepto."' reconocido='".$variables['ValorRe']."' fecha='".$variables['Fecha']."'  excedente='".$variables['ValorEx']."' contador='".$contadorid."' id='IDvalorOcultoReconocidoExcedente_".$contadorid."'>
									";
								}
								else
								{
									$resp.="<td class='".$ColorFila."' align='right'>".number_format($variables['ValorTo'],0,'.',',' )."</td>
								
									<td nowrap='nowrap'><img  ".$condicion." id='imgexcedente_".$codConcepto."' tooltip='si'  valor ='si' onclick='enviarExedentedetalle( \"".$codConcepto."\" , \"".$contadorid."\")' title ='enviar valor al excedente'  width='15' height='15' src='../../images/medical/sgc/atras.png '  style='cursor:pointer' />&nbsp;&nbsp<img   ".$condicion."  tooltip='si'  onclick='reestablecerValoresdetalle( \"".$codConcepto."\" ,  \"".$contadorid."\")' title ='Reestablecer valores' width='15' height='15' src='../../images/medical/sgc/Refresh-128.png' style='cursor:pointer'>&nbsp;&nbsp<img  ".$condicion." tooltip='si'  id='imgreconocido_".$codConcepto."'  valor ='no' onclick='enviarReconocidodetalle( \"".$codConcepto."\" , \"".$contadorid."\")' title ='enviar valor al reconocido' width='15' height='15' src='../../images/medical/sgc/adelante.png'  style='cursor:pointer' /></td>
									<td align='center'><input type='hidden'  class='valorOcultoReconocidoExcedente_".$codConcepto."' reconocido='".$variables['ValorRe']."' fecha='".$variables['Fecha']."'  excedente='".$variables['ValorEx']."' contador='".$contadorid."' id='IDvalorOcultoReconocidoExcedente_".$contadorid."'>
									";
								}
								
								
								//--------------------------
								$respdetallado.="<td class='".$ColorFila."' align='right'>".number_format($variables['ValorTo'],0,'.',',' )."</td>
								<td align='center'>";
								//-----------------------------

								if($tipoFact == 'facturado')
								{
									$resp.= "<img tooltip='si'  width='15' height='15' src='../../images/medical/root/grabar.png'/>";
									//----------------
									$respdetallado.= "<img tooltip='si'  width='15' height='15' src='../../images/medical/root/grabar.png'/>";
									//---------------------
								}
								else
								{
									$mesCargo 		= explode('-', $variables['Fecha']);
									$cargoMesAct 	= ((date("Y-m") == $mesCargo[0]."-".$mesCargo[1]) ? true : false);
								}
								$resp.="</td>
										  <td align='center'>";
								$resp.="</td>

								</tr>
								";
								//---------------------
								$respdetallado.="</td>
										  <td align='center'>";
								$respdetallado.="</td>

								</tr>
								";
								//--------------------
								// --> Aqui se pinta el cargo paralelo si lo hay.
								if($pintarParalelo)
								{
									$varParalelo = $arrInfoConceptos['InfConcepto'][$variables['idParalelo']];
									$resp.="
									<tr onClick='detalladoporconcepto(\"".$codConcepto."\")' id='".$variables['idParalelo']."' class='".$tipoFact." ".$tipoFact.'-'.$codConcepto." procedimiento-paralelo' >
										<td colspan='2'></td>
										<td class='".$ColorFila."' style='border-left: 2px dotted #72A3F3;' align='center'>".$varParalelo['Fecha']." ".$variables['Facturable']." </td>
										<td class='".$ColorFila."' style='font-size: 8pt;font-family: verdana;' >".$varParalelo['CodProcedi']."-".$varParalelo['NomProcedi']."</td>
										<td class='".$ColorFila."'>".$varParalelo['Servicio']."</td>
										<td class='".$ColorFila."' style='font-size: 8pt;font-family: verdana;' >".$varParalelo['NomTercero']."</td>
										<td class='".$ColorFila."' style='font-size: 8pt;font-family: verdana;' >".$varParalelo['TipoFact']."</td>
										<td class='".$ColorFila."' align='center'>".$varParalelo['ReconExced']."</td>
										<td class='".$ColorFila."' align='center'>".$varParalelo['Facturable']."</td>
										<td class='".$ColorFila."' align='center'>".$varParalelo['Cantidad']."</td>
										<td class='".$ColorFila."' align='right'>".number_format($varParalelo['ValorUn'],0,'.',',' )."</td>
										<td class='".$ColorFila."' align='right'>".number_format($varParalelo['ValorRe'],0,'.',',' )."</td>
										<td class='".$ColorFila."' align='right'>".number_format($varParalelo['ValorEx'],0,'.',',' )."</td>
										<td class='".$ColorFila."' align='right'>".number_format($varParalelo['ValorTo'],0,'.',',' )."</td>
										<td class='".$ColorFila."' style='font-size: 8pt;font-family: verdana;' >".$varParalelo['Usuario']."</td>
										<td class='".$ColorFila."' style='border-right: 2px dotted #72A3F3;' align='center'>".$varParalelo['Registro']."</td>
									</tr>
									";
									//------------------------
									$respdetallado.="
									<tr id='".$variables['idParalelo']."' class='".$tipoFact." ".$tipoFact.'-'.$codConcepto." procedimiento-paralelo' >
										<td colspan='2'></td>
										<td class='".$ColorFila."'  align='center'>".$varParalelo['Fecha']." ".$variables['Facturable']." </td>
										<td class='".$ColorFila."'  >".$varParalelo['CodProcedi']."-".$varParalelo['NomProcedi']."</td>
										<td class='".$ColorFila."'>".$varParalelo['Servicio']."</td>
										<td class='".$ColorFila."' >".$varParalelo['NomTercero']."</td>
										<td class='".$ColorFila."' >".$varParalelo['TipoFact']."</td>
										<td class='".$ColorFila."' align='center'>".$varParalelo['ReconExced']."</td>
										<td class='".$ColorFila."' align='center'>".$varParalelo['Facturable']."</td>
										<td class='".$ColorFila."' align='center'>".$varParalelo['Cantidad']."</td>
										<td class='".$ColorFila."' align='right'>".number_format($varParalelo['ValorUn'],0,'.',',' )."</td>
										<td class='".$ColorFila."' align='right'>".number_format($varParalelo['ValorRe'],0,'.',',' )."</td>
										<td class='".$ColorFila."' align='right'>".number_format($varParalelo['ValorEx'],0,'.',',' )."</td>
										<td class='".$ColorFila."' align='right'>".number_format($varParalelo['ValorTo'],0,'.',',' )."</td>
										<td class='".$ColorFila."' >".$varParalelo['Usuario']."</td>
										<td class='".$ColorFila."' align='center'>".$varParalelo['Registro']."</td>
									</tr>
									";
									//-------------------------
									
								}
							}

							$Total_rec+= $variables['ValorRe'] ;
							$Total_exe+= $variables['ValorEx'];
						}
						$Total_r_e = $Total_rec+$Total_exe;
						$html_barra= '<b>$'.number_format($Total_r_e,0,'.',',' ).'</b>';

						$resp.="
						<input type='hidden' class='HiddenTotales' id_barra='".$tipoFact."-".$codConcepto."' value='".$html_barra."'>
						";	
						//-------------
						$respdetallado.="
						<input type='hidden' class='HiddenTotales' id_barra='".$tipoFact."-".$codConcepto."' value='".$html_barra."'>
						";
						//---------------
						
						

						
						//----------------
						
						//-----------------
						
						$array_conceptos[$codConcepto] = $codConcepto;
						if($codConcepto == $wconceptoestancia)
						{
						 $resp.="
							<tr  class='concepto_".$codConcepto." procedimiento procedimiento_".$codConcepto."' style='display:none'>
								<td colspan='4'></td>
								<td colspan='2' align='right' class='encabezadoTabla' style='font-size: 8pt;font-family: verdana;'>&nbsp;<div><b>TOTALES:&nbsp;</b></div></td>
								<td class='encabezadoTabla valorextra' align='right'  style='display:none' >&nbsp;<div id='totalgeneralestanciaextra'></div></td>
								<td class='encabezadoTabla' align='right'>&nbsp;<div id='divreconocidosubtotal' valor='".$Total_rec."'>$".number_format($Total_rec,0,'.',',' )."</div></td>";
								
						 // $respdetallado.="
							// <tr class='".$tipoFact." ".$tipoFact."-".$codConcepto." procedimiento' style='display:none'>
								// <td colspan='4'></td>
								// <td colspan='2' align='right' class='encabezadoTabla' >&nbsp;<div><b>TOTALES:</b></div></td>
								// <td class='encabezadoTabla' align='right'>&nbsp;<div id='divsubtotaltotalrec'>$".number_format($Total_rec,0,'.',',' )."</div></td>";
								
								
						 $resp.="
								<td class='encabezadoTabla' align='right'>&nbsp;<div id='divexcedentetotalhospitalizacion' >$".number_format($Total_exe,0,'.',',' )."</div><input type='hidden' id='valorexcedenteH' value='".$Total_exe."'></td>";
							//------------------
							// $respdetallado.="
									// <td class='encabezadoTabla' align='right' nowrap='nowrap'>&nbsp;<div   id='divexcedentetotalhospitalizacion2' >$".number_format($Total_exe,0,'.',',' )."</div><input type='hidden' id='valorexcedenteH' value='".$Total_exe."'></td>";
							//----------------------
						
						 $resp.="
								<td class='encabezadoTabla' align='right'>&nbsp;<div id='totalgeneralestancia' valororiginal='".$Total_r_e."'>$".number_format($Total_r_e,0,'.',',' )."</div></td>
								";
						}
						else
						{
							$resp.="
							<tr  class='concepto_".$codConcepto." procedimiento procedimiento_".$codConcepto."' style='display:none'>
								<td colspan='4'></td>
								<td colspan='2' align='right' class='encabezadoTabla' style='font-size: 8pt;font-family: verdana;'>&nbsp;<div><b>TOTALES:&nbsp;</b></div></td>
								<td class='encabezadoTabla valorextra' style='display:none'></td>
								<td class='encabezadoTabla' align='right'>&nbsp;<div id='divreconocidoTotal_".$codConcepto."' >$".number_format($Total_rec,0,'.',',' )."</div></td>";
								
							
							// $respdetallado.="
							// <tr class='".$tipoFact." ".$tipoFact."-".$codConcepto." procedimiento' style='display:none'>
								// <td colspan='4'></td>
								// <td colspan='2' align='right' class='encabezadoTabla' >&nbsp;<div><b>TOTALES:</b></div></td>
								// <td class='encabezadoTabla' align='right'>&nbsp;<div>$".number_format($Total_rec,0,'.',',' )."</div></td>";
							
							$resp.="
								<td class='encabezadoTabla' align='right'>&nbsp;<div id='divexcenteTotal_".$codConcepto."'>$".number_format($Total_exe,0,'.',',' )."</div></td>";
							//--------------
							// $respdetallado.="
								// <td class='encabezadoTabla' align='right'>&nbsp;<div>$".number_format($Total_exe,0,'.',',' )."</div></td>";
							// ---------------------
						
							$resp.="
								<td class='encabezadoTabla' align='right'>&nbsp;<div >$".number_format($Total_r_e,0,'.',',' )."</div></td>
								";
						
						}
						
						
						//----------------
					
						//---------------------
						 // $respdetallado.="
								// <td class='encabezadoTabla' align='right'>&nbsp;<div>$".number_format($Total_r_e,0,'.',',' )."</div></td>
							// </tr><tr><td colspan='9'>&nbsp;</td></tr>";
						//----------------------------
						
						$Total_rec_cuenta+=$Total_rec;
						$Total_exe_cuenta+=$Total_exe;
						$Total_r_e_cuenta+=$Total_r_e;
						
						$resp = str_replace("remplazarValorReconocido", "$".number_format($Total_rec,0,'.',',' ) , $resp);
						$resp = str_replace("remplazarValorRCambio", "$".number_format($Total_rec,0,'.',',' ) , $resp);
						$resp = str_replace("aux2hospitalizacion", $Total_rec , $resp);
						//-----
						$respdetallado = str_replace("remplazarValorReconocido", "$".number_format($Total_rec,0,'.',',' ) , $respdetallado);
						//-----------
						
						if($codConcepto == $wconceptoestancia)
						{
							$resp = str_replace("remplazarValorExcedente", 	$Total_exe , $resp);
							$resp = str_replace("remplazarValorECambio", 	$Total_exe , $resp);
							//----------
							$respdetallado = str_replace("remplazarValorExcedente", 	$Total_exe , $respdetallado);
							//-------
						}
						else
						{
						   
							$resp = str_replace("remplazarValorExcedente", 	"$".number_format($Total_exe,0,'.',',' ) , $resp);
							$resp = str_replace("remplazarValorECambio", 	"$".number_format($Total_exe,0,'.',',' ) , $resp);
						    //------------
							$respdetallado = str_replace("remplazarValorExcedente", 	"$".number_format($Total_exe,0,'.',',' ) , $respdetallado);
							//------------
						}
						$resp = str_replace("remplazarValorTotal", 	"$".number_format($Total_r_e,0,'.',',' ), $resp);
						$resp = str_replace("remplazarValoraux", $Total_r_e, $resp);
						//---------
						$respdetallado = str_replace("remplazarValorTotal", 	"$".number_format($Total_r_e,0,'.',',' ), $respdetallado);
						//--------------
						$respresumido =  str_replace("remplazarValorReconocido", "$".number_format($Total_rec,0,'.',',' ) , $respresumido);
						$respresumido =  str_replace("remplazarValorExcedente", 	"$".number_format($Total_exe,0,'.',',' ) , $respresumido);
						$respresumido =  str_replace("remplazarValorTotal", 	"$".number_format($Total_r_e,0,'.',',' ), $respresumido);
						
					}
					
					//----
						$sqlInfoPac = "
										 SELECT Emptem,Empcod,Pactus,Pactaf,Ingpco,Ingtpa
										  FROM ".$wbasedato."_000100 AS A INNER JOIN ".$wbasedato."_000101 AS B ON(A.Pachis = B.Inghis AND B.Ingnin = '".$wing."')
											   LEFT  JOIN ".$wbasedato."_000024 	AS C ON(B.Ingcem = C.Empcod)
											   LEFT  JOIN ".$wbasedato."_000025 	AS D ON(C.Emptar = D.Tarcod)
											   LEFT  JOIN root_000011 				AS E ON(B.Ingdig = E.Codigo)
											   LEFT  JOIN ".$wbasedato_movhos."_000048	AS F ON(B.Ingmei = F.Meddoc)
										 WHERE A.Pachis = '".$whistoria."'
										";
						$resInfoPac = mysql_query($sqlInfoPac, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInfoPac):</b><br>".mysql_error());
						$tipopaciente='';
						if($rowInfoPac = mysql_fetch_array($resInfoPac))
						{
							$tipoempresa = $rowInfoPac['Emptem'];
							$empresa = $rowInfoPac['Empcod'];
							$cobertura = $rowInfoPac['Pactus'];
							$tipo = $rowInfoPac['Pactaf'];
							$afiliacion = '*';
							$nivel = $rowInfoPac['Ingpco'];
							$tipopaciente =  $rowInfoPac['Ingtpa'];	
						}
						
					
												
						$querycob  = "SELECT Seltip,Selcod,Seldes,Selpri
									 FROM cliame_000105 
									WHERE Selest ='on'
									  AND Seltip ='06'
									  AND Selcod ='".$cobertura."'
								Order by  Selpri";	

						$rescob = mysql_query($querycob,$conex);
						
							$array_cobertura = array();
							$array_cobertura['*'] = "Todos";
							while($rowcob = mysql_fetch_array($rescob))
							{
								$descobertura = $rowcob['Seldes'];
							}	
						
						
						
						$querytipo  = "SELECT Seltip,Selcod,Seldes,Selpri
									 FROM cliame_000105 
									 WHERE Selest ='on'
									   AND Seltip ='16'
									   AND Selcod ='".$tipo."'
									 Order by  Selpri";	

						$restipo = mysql_query($querytipo,$conex);
					
						
							while($rowtipo = mysql_fetch_array($restipo))
							{
								$destipo = $rowtipo['Seldes'];
							}
								
						
						

						$nivelquery  = "SELECT Seltip,Selcod,Seldes,Selpri
									 FROM cliame_000105 
									WHERE Selest ='on'
									  AND Seltip ='22'
									  AND Selcod ='".$nivel."'
								Order by  Selpri";	

						$resnivel = mysql_query($nivelquery,$conex);
						
						
						while($rownivel = mysql_fetch_array($resnivel))
						{
								$desnivel=$rownivel['Seldes'];
						}
										
						
						
						$sqlTopesdesdeAdmision = "SELECT Toptop , Toptco,Toprec,Topdia,Ccfnom , ".$wbasedato."_000204.Fecha_data
													FROM ".$wbasedato."_000204 LEFT JOIN   ".$wbasedato."_000202 ON (Ccfcod = Toptco)
												   WHERE Tophis ='".$whistoria."' 
												     AND Toping = '".$wing."' ";
													 
						$restopes = mysql_query($sqlTopesdesdeAdmision,$conex)or die ("Error: ".mysql_errno()." - en el query: ".$sqlTopesdesdeAdmision." - ".mysql_error());;
						$entrotopes = 0;
						while($rowtopes = mysql_fetch_array($restopes))
						{
								$entrotopes ++;
								if($entrotopes==1)
								{
									$tablatopes .= "<table align='center'  ><tr ><td colspan='5' class='fila1' align='center'><p style='font-size: 12px; margin: 0'>Topes desde la Admisión</p></td></tr>
									<tr><td class='fila1' style='HEIGHT:10px'><p style='font-size: 12px; margin:0'>Concepto</p></td><td class='fila1'><p style='font-size: 12px;margin:0'>Valor</p></td><td class='fila1'><p style='font-size: 12px;margin:0'>Diario</p></td><td class='fila1'><p style='font-size: 12px;margin:0'>Porcentaje</p></td><td class='fila1'><p style='font-size: 12px;margin:0'>F.Creacion</p></td></tr>";
								}
								if($rowtopes['Toptop'] !='')
								{
									$valortope = "$".number_format($rowtopes['Toptop'],0,'.',',' );
								}
								else
								{
									$valortope = '';
								}
								if ($rowtopes['Topdia'] =='on')
								$topediario = 'si';
								else
								$topediario ='no';
								$tablatopes .= "<tr class='fila2'><td nowrap=nowrap align=left><p style='font-size: 12px; margin:0'>".$rowtopes['Toptco']."-".$rowtopes['Ccfnom']."</p></td><td nowrap=nowrap align='right'><p style='font-size: 12px; margin:0'>".$valortope."</p></td><td  nowrap=nowrap align='right' ><p style='font-size: 12px; ; margin:0'>".$topediario."</p></b></td><td align=right><p style='font-size: 12px;  margin: 0'>".$rowtopes['Toprec']."</p></td><td align=right><p style='font-size: 12px;  margin: 0'>".$rowtopes['Fecha_data']."</p></td></tr>";
						}
						
						if($entrotopes!=0)
						{
							$tablatopes .= "</table>";
						}
						
						$sqlhabitacion = "SELECT Habcpa 	
											FROM ".$wbasedato_movhos."_000020 
										   WHERE Habhis ='".$whistoria."' 
											 AND Habing = '".$wing."' ";
						
						$whabitacion ='';
						$wfondo ='fila2';
						$reshab = mysql_query($sqlhabitacion,$conex)or die ("Error: ".mysql_errno()." - en el query: ".$sqlhabitacion." - ".mysql_error());;
					
						while($rowhab = mysql_fetch_array($reshab))
						{
							$whabitacion = $rowhab['Habcpa'];
						}
						
						if($whabitacion == '')
						{
								
							$sqldealta = "SELECT Ubihis ,Ubialp ,Ubiald	
												FROM ".$wbasedato_movhos."_000018 
											   WHERE Ubihis ='".$whistoria."' 
											     AND Ubiing = '".$wing."' ";
						
							$resalta= mysql_query($sqldealta,$conex)or die ("Error: ".mysql_errno()." - en el query: ".$sqldealta." - ".mysql_error());;
							$altadefinitiva ='';
							$altaprogramada ='';
							if($rowalta = mysql_fetch_array($resalta))
							{
								$altaprogramada = $rowalta['Ubialp']; // alta programada
								$altadefinitiva = $rowalta['Ubiald']; // alta definitiva
								
								if($altadefinitiva=='on')
								{
									$whabitacion='De Alta';
									$wfondo ='fondoRojo';
								}
								if( $altadefinitiva=='off' && $altaprogramada =='on')
								{
									$whabitacion='Alta en proceso';
								}
							}
						
						}
						
						$respencabezado.="<table><tr>
						<td><div align='left'><table><tr class='fila1'><td class='fila1' ><b>Cobertura en Salud:</b></td><td class='fila2'>".$descobertura."</td></tr><tr><td class='fila1'><b>Tipo de Afiliación :</b></td><td class='fila2'>".$destipo."</td></tr><tr><td class='fila1' ><b>Pago Compartido :</b></td><td class='fila2'>".$desnivel."</td></tr><tr><td class='fila1' ><b>Habitacion:</b></td><td class='".$wfondo."'>".$whabitacion."</td></tr></table></div>
						</td>
						
						<td width='600px' align='center'>
						<div id='topes' align='center'>".$tablatopes."</div>
						</td>
						<td>".$respbotones."</td>
						</tr>
						<tr>
						<td></td>
						<td width='600px'></td>
						<td>
							<table align='right'>
								<tr>
									<td colspan='4' style='cursor:pointer' onclick ='traertope()'  >
										<b>Agregar Tope&nbsp;</b>
										<img width='15' height='15'   src='../../images/medical/root/adicionar2.png'>
									</td>
								</tr>
								<tr>
									<td class='tope' style='display:none' align='right'>
										<b><p style='font-size: 10px'>Valor tope:</p></b>
									</td>
									<td >
										<div class='tope' style='display:none'>
											<input style='width: 100px; height: 15px; text-align: right;'  class='tope miles' type='text' id='tope' style='display:none'>
										</div>
									</td>
									<td class='tope'  style='display:none'>
										<p style='font-size: 10px'>
											<b>Fecha inicial tope:</b>
										</p>
									</td>
									<td class='tope' style='display:none' nowrap='nowrap' >
										<input style='width: 70px; height: 12px; font-size:12px' id='fechatope'  type='text'>
									</td>
								</tr>
								<tr>
									<td  width='10%'><div  style='display:none'  class='tope' align='right'><b><p style='font-size: 10px'>porcentaje:</p></b></div></td>
									<td   width='10%' nowrap='nowrap' align='right'><input size='3'   class='tope' style='width: 50px; height: 15px; text-align:right; display:none'  type='text' id='porcentaje' value='100' ></td>
									<td></td>
									<td   width='10%'  nowrap='nowrap'><input type='button' class='tope'  style='display:none'  value='calcular' onclick='calcularvalores()'></td>
								</tr>
							<table>
						</td>
						</tr>
					</table>
					</td>";
					
					/*
					$resp.="
					<tr align='center'   width='100%'>
						<td   width='10%' ><div  style='display:none' >Fecha </div></td>
						<td   width='40%'><div  style='display:none'  ></div></td>
						<td   width='10%'><div  style='display:none'  ></div></td>
						<td   width='20%'><div  style='display:none'  ></div></td>
						<td   width='5%'><div  style='display:none'   ></div></td>
						<td   width='10%'><div  style='display:none'></div></td>
						
						<td style='cursor:pointer' onclick ='traertope()' colspan='4' align='right'><b>Agregar Tope&nbsp;</b><img width='15' height='15'   src='../../images/medical/root/adicionar2.png'</td>
						
					</tr>
					<tr align='center'   width='100%' >
						<td   width='10%' ><div  style='display:none' ></div></td>
						<td  width='40%'><div  style='display:none'  ></div></td>
						<td   width='10%'><div  style='display:none'  ></div></td>
						<td  width='20%'><div  style='display:none'  ></div></td>
						<td   width='5%'><div  style='display:none'   ></div></td>
						
						<td colspan='1'  class='tope' style='display:none' align='right'><b><p style='font-size: 10px'>Valor tope:</p></b></td><td colspan='1' ><div class='tope' style='display:none'><input style='width: 100px; height: 15px; text-align: right;'  class='tope miles' type='text' id='tope' style='display:none'></div></td><td class='tope'  style='display:none'><p style='font-size: 10px'><b>Fecha inicial tope:</b></p></td><td class='tope' style='display:none' nowrap='nowrap' ><input style='width: 70px; height: 12px; font-size:12px' id='fechatope'  type='text'></td>
						
					</tr>
					";
				
				
				
				$resp.="
					<tr align='center'   width='100%'>
						<td   width='10%' ><div  style='display:none' >Fecha </div></td>
						<td   width='40%'><div  style='display:none'  >Procedimiento</div></td>
						<td  width='10%'><div  style='display:none'  >C.Costos</div></td>
						<td  width='20%'><div  style='display:none'  >Entidad</div></td>
						<td  width='5%'><div  style='display:none'   >Cantidad</div></td>
						<td  width='10%'><div  style='display:none'  class='tope' align='right'><b><p style='font-size: 10px'>porcentaje:</p></b></div></td>
						
						<td   width='10%' nowrap='nowrap' align='right'><input size='3'   class='tope' style='width: 50px; height: 15px; text-align:right; display:none'  type='text' id='porcentaje' value='100' ></td>
						<td   width='10%'  nowrap='nowrap'><input type='button' class='tope'  style='display:none'  value='calcular' onclick='calcularvalores()'></td>
						<td   width='10%'  nowrap='nowrap'></td>
						<td  nowrap = nowrap ></td>
						
					</tr>";*/
					
						$sqltopes = " SELECT   Cfcpor ,Cfctma ,Cfcpxh
									   FROM ".$wbasedato."_000290
									  WHERE (Cfctem = '".$tipoempresa."'  OR Cfctem ='*')
										AND (Cfcemp = '".$empresa."'      OR Cfcemp ='*')
										AND (Cfccob = '".$cobertura."' OR Cfccob='*'	)
										AND (Cfctip = '".$tipo."' OR Cfctip='*')
										AND (Cfcniv = '".$nivel."' OR Cfcniv='*' )
							";
						$restope = mysql_query($sqltopes, $conex) or die("<b>ERROR EN QUERY MATRIX (sqltopes):</b><br>".mysql_error());
						$valor_luego_copagos = 0;
						if($rowtope = mysql_fetch_array($restope))
						{
							$porcentaje = $rowtope['Cfcpor']*1;
							$topemaximo = $rowtope['Cfctma']*1;
							$porcentaje = $porcentaje /100;
							
							//$valor_luego_copagos = ($Total_rec_cuenta*1 ) * ($porcentaje*1);
							$valor_luego_copagos = ($Total_exe_cuenta + $Total_rec_cuenta*1 ) * ($porcentaje*1);
							//$valor_luego_copagos = $valor_luego_copagos."--".$topemaximo;
							
							
							// valor de la franquicia por hospitalizacion.
							$concepto_estancia 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'concepto_estancia');
							//print_r($array_conceptos);
							
							
							
							if(array_key_exists($concepto_estancia , $array_conceptos))
							{
								
								$valor_estancia_franquicia =  $rowtope['Cfcpxh']*1;
								$valor_luego_copagos = $valor_luego_copagos + $valor_estancia_franquicia;
							}
							
							if($topemaximo*1 < $valor_luego_copagos*1)
							{
								$valor_luego_copagos = $topemaximo;
							}
						}
						
						
						//------------- Abonos en unix 
				
						//----Primero se buscan abonos a pacientes particulares
						//----
						//se busca por cedula en la tabla anant con la fuente 35  antind = p   y que el saldo sea diferente de cero
						$saldototal=0;
						$queryAnticipoUnix = "SELECT Antsal 
												FROM Anant WHERE antfue ='35'
												AND  Antind = 'P'
												AND  Antced = '".$wcedula."'
												AND  Antsal <> 0
												AND  Antanu = 0";
						
						$resunix = odbc_do( $conexUnix, $queryAnticipoUnix );
						while (odbc_fetch_row($resunix))
						{
							$saldo = odbc_result($resunix,1);
							$saldototal = $saldototal +  $saldo;
						}
						
						if($saldototal!=0)
						{
							$codConcepto='anticipos';
							//$Total_exe_cuenta+=-$saldototal;
							$Total_r_e_cuenta+=-$saldototal;
							$Total_rec_cuenta+=-$saldototal;
							$resp .= "<tr  onClick='detalladoporconcepto(\"anticipos\")' align='left' class='".$tipoFact." conceptos'  c width='100%' ><td  width='70%' colspan='6' style='font-weight:bold;background-color:#D7EBF9;color:#2779AA;cursor:pointer; border-radius:4px; border: 1px solid #2779AA;'>ANTICIPOS</td>
							<td style='font-weight:bold;background-color:#D7EBF9;color:#2779AA; border-radius:4px; border: 1px solid #2779AA;' align='right'>&nbsp;-$".number_format($saldototal,0,'.',',' )."</td>
							<td style='font-weight:bold;background-color:#D7EBF9;color:#2779AA; border-radius:4px; border: 1px solid #2779AA;' align='right'><div id='divValorExcedente_".$codConcepto."'>$".number_format(0,0,'.',',' )."</div></td>
							<td style='font-weight:bold;background-color:#D7EBF9;color:#2779AA; border-radius:4px; border: 1px solid #2779AA;' align='right'><div id='divValorReconocido_".$codConcepto."'>$-".number_format($saldototal,0,'.',',' )."</div></td></tr>";
							
							//----------------
							$respdetallado .= "<tr align='left' class='".$tipoFact." conceptos'   width='100%' ><td  width='70%' colspan='6' >ANTICIPOS</td>
							<td align='right'>&nbsp;-$".number_format($saldototal,0,'.',',' )."</td>
							<td align='right'>&nbsp;$".number_format(0,0,'.',',' )."</td>
							<td align='right'>&nbsp;-$".number_format($saldototal,0,'.',',' )."</td></tr>";
							//----------------
							
							//----------------
							// $respresumido .= "<tr align='left' class='".$tipoFact." conceptos'   width='100%' ><td  width='70%' colspan='6' >ANTICIPOS</td>
							// <td align='right'>&nbsp;-$".number_format($saldototal,0,'.',',' )."</td>
							// <td align='right'>&nbsp;-$".number_format(0,0,'.',',' )."</td>
							// <td align='right'>&nbsp;-$".number_format($saldototal,0,'.',',' )."</td></tr>";
							//----------------
							$respresumido .="<tr align='left'  width='100%'  class='doted2' >
												<td   width='70%' colspan='9'>ANTICIPOS</td>
												<td align='right' nowrap=nowrap class='block'>&nbsp;-$".number_format($saldototal,0,'.',',' )."</td>
												<td align='right' nowrap=nowrap class='block'>&nbsp;$".number_format(0,0,'.',',' )."</td>
												<td align='right' nowrap=nowrap class='block'>&nbsp;-$".number_format($saldototal,0,'.',',' )."</td>
											 </tr>";
							
						
						}
						
						//--------------------------------
						$saldototal=0;
						//--Abonos unix a empresas 
						$queryAnticipoUnix = "SELECT Antsal , Antdoc, Antfec
												FROM Anant, Anantpac 
												WHERE Antfue ='35'
												AND  Antind = 'E'
												AND  Antfue = Antpacfue
												AND  Antdoc = Antpacdoc
												AND  Antpacced = '".$wcedula."'
												AND  Antsal <> 0
												AND  Antanu = 0";
						
						$resunix = odbc_do( $conexUnix, $queryAnticipoUnix );
						while (odbc_fetch_row($resunix))
						{
							$saldo = odbc_result($resunix,1);
							$saldototal = $saldototal +  $saldo;
							$codConcepto='anticipos';
							$respaux.="
								<tr   class='".$tipoFact." ".$tipoFact.'-'.$codConcepto." procedimiento procedimiento_".$codConcepto."' style='display:none' >
										<td class='".$ColorFila."' align='center'>".odbc_result($resunix,3)."</td>
										<td class='".$ColorFila."' style='font-size: 8pt;font-family: verdana;' >Anticipo Numero : ".odbc_result($resunix,2)." </td>
										<td class='".$ColorFila."'   ></td>
										<td class='".$ColorFila."' align='center'></td>
										<td class='".$ColorFila."' align='center'>1</td>
										<td class='".$ColorFila."' align='right'>".@number_format(odbc_result($resunix,1),0,'.',',' )."</td>
										<td  class='".$ColorFila."' align='right'>".@number_format(0,0,'.',',' )."</td>
										<td class='".$ColorFila."' valor='".odbc_result($resunix,1)."' align='right'>-".number_format(odbc_result($resunix,1),0,'.',',' )."</td>
										<td  class='".$ColorFila."' align='right'>".@number_format(0,0,'.',',' )."</td>
								</tr>";
								
								/*
								$resp.="
										<td class='".$ColorFila."' align='center'>".$variables['Fecha']."</td>
										<td class='".$ColorFila."' style='font-size: 8pt;font-family: verdana;' >".$variables['CodProcedi']."-".$variables['NomProcedi']."</td>
										<td class='".$ColorFila."'   >".$variables['Servicio']."-".$arrayccoNom[$variables['Servicio']]."</td>
										<td class='".$ColorFila."' align='center'>".$variables['codEntidad']."-".$arrayentidades[$variables['codEntidad']]."</td>
										<td class='".$ColorFila."' align='center'>".$variables['Cantidad']."</td>
										<td class='".$ColorFila."' align='right'>".@number_format($variables['ValorUn'],0,'.',',' )."</td>
										<td class='".$ColorFila." tdvalorreconocidoestancia' valor='".$variables['ValorRe']."' align='right'>".number_format($variables['ValorRe'],0,'.',',' )."</td>";
										//---------------------		
						
								*/
								
								
						}
						
						if($saldototal!=0)
						{
							$Total_exe_cuenta+=-$saldototal;
							//$Total_r_e_cuenta+=-$saldototal;
							$codConcepto='anticipos';
							$resp .= "<tr align='left' class='".$tipoFact." conceptos'  width='100%' ><td  width='70%' colspan='6' style='font-weight:bold;background-color:#D7EBF9;color:#2779AA;cursor:pointer; border-radius:4px; border: 1px solid #2779AA;'>ANTICIPOS</td>
							<td style='font-weight:bold;background-color:#D7EBF9;color:#2779AA; border-radius:4px; border: 1px solid #2779AA;' align='right'><div id='divValorReconocido_".$codConcepto."'>$".number_format(0,0,'.',',' )."</div></td>
							<td style='font-weight:bold;background-color:#D7EBF9;color:#2779AA; border-radius:4px; border: 1px solid #2779AA;' align='right'><div id='divValorExcedente_".$codConcepto."'>-$".number_format($saldototal,0,'.',',' )."</div></td>
							<td style='font-weight:bold;background-color:#D7EBF9;color:#2779AA; border-radius:4px; border: 1px solid #2779AA;' align='right'>-$".number_format($saldototal,0,'.',',' )."</td></tr>";
							
							/*
							<tr align='left'  width='100%'  class='doted2' >
							<td   width='70%' colspan='9'>
							".$codConcepto."-".$arrInfoConceptos['NomConcepto']."
							</td>
							
							*/
							
							$respresumido .="<tr align='left'  width='100%'  class='doted2' >
												<td   width='70%' colspan='9'>ANTICIPOS</td>
												<td align='right' nowrap=nowrap class='block'>&nbsp;$".number_format(0,0,'.',',' )."</td>
												<td align='right' nowrap=nowrap class='block'>&nbsp;-$".number_format($saldototal,0,'.',',' )."</td>
												<td align='right' nowrap=nowrap class='block'>&nbsp;-$".number_format($saldototal,0,'.',',' )."</td>
											 </tr>";
							
										/*$respresumido.="
								<tr><td colspan='9'>&nbsp;</td></tr>
								<tr  class='doted2'>
									<td colspan='6'></td>
									<td colspan='3'  nowrap=nowrap >&nbsp;<b><div>TOTALES CUENTA:</div></b></td>
									<td align='right' nowrap=nowrap class='block'>&nbsp;<b><div id='divreconocidoresumido'>$".number_format($Total_rec_cuenta,0,'.',',' )."</div></b></td>
									<td align='right' nowrap=nowrap class='block'>&nbsp;<b><div id='divExcedenteResumido' class='block'>$".number_format($Total_exe_cuenta,0,'.',',' )."</div></b></td>
									<td align='right' nowrap=nowrap class='block'>&nbsp;<b><div id='divtotalResumido' class='block'>$".number_format($Total_r_e_cuenta ,0,'.',',' )."</div></b></td>
								</tr>";
										*/
							
							
							$resp.= $respaux;
							
							
							//----------------
							// $respdetallado .= "<tr align='left' class='".$tipoFact." conceptos'   width='100%' ><td  width='70%' colspan='6' >ANTICIPOS</td>
							// <td align='right'>&nbsp;$".number_format(0,0,'.',',' )."</td>
							// <td align='right'>&nbsp;$".number_format(0,0,'.',',' )."</td>
							// <td align='right'>&nbsp;-$".number_format($saldototal,0,'.',',' )."</td></tr>";
							$respdetallado .="<tr align='left'  width='100%'  class='doted2' >
												<td   width='70%' colspan='6'>ANTICIPOS</td>
												<td align='right' nowrap=nowrap class='block'>&nbsp;$".number_format(0,0,'.',',' )."</td>
												<td align='right' nowrap=nowrap class='block'>&nbsp;-$".number_format($saldototal,0,'.',',' )."</td>
												<td align='right' nowrap=nowrap class='block'>&nbsp;-$".number_format($saldototal,0,'.',',' )."</td>
											 </tr>";
							$saldoauxiliar = $saldototal * -1;
							
							//----------------
						}
						
						
					
					
					//----------------
					if($tipopaciente =='P')
					{
						
						$Total_r_e_cuenta=$Total_r_e_cuenta+ $valor_luego_copagos;
						$Total_rec_cuenta=$Total_rec_cuenta + $valor_luego_copagos;
						$resp .= "<tr align='left' class='".$tipoFact." conceptos'    width='100%' ><td  width='70%' colspan='6' style='font-weight:bold;background-color:#D7EBF9;color:#2779AA;cursor:pointer; border-radius:4px; border: 1px solid #2779AA;'>(Copagos,Cuotas de recuperacion y Franquicias)</td>
								<td id='valorextra_coutas'  class='valorextra' style='font-weight:bold;background-color:#D7EBF9;color:#2779AA; border-radius:4px; border: 1px solid #2779AA; display:none' align='right'>
								</td>	
								<td style='font-weight:bold;background-color:#D7EBF9;color:#2779AA; border-radius:4px; border: 1px solid #2779AA;' align='right'><div id='divValorReconocido_copagos'>$".number_format($valor_luego_copagos,0,'.',',' )."</div></td>
								<td style='font-weight:bold;background-color:#D7EBF9;color:#2779AA; border-radius:4px; border: 1px solid #2779AA;' align='right'><div id='divValorExcedente_Copagos'>$".number_format(0,0,'.',',' )."</div></td>
								<td style='font-weight:bold;background-color:#D7EBF9;color:#2779AA; border-radius:4px; border: 1px solid #2779AA;' align='right'>$".number_format($valor_luego_copagos,0,'.',',' )."</td></tr>";
				
						$respdetallado .= "<tr align='left' class='".$tipoFact." conceptos'   width='100%' ><td  width='70%' colspan='6' >(Copagos,Cuotas de recuperacion y Franquicias)</td>
							<td align='right'>&nbsp;$".number_format($valor_luego_copagos,0,'.',',' )."</td>
							<td align='right'>&nbsp;$".number_format(0,0,'.',',' )."</td>
							<td align='right'>&nbsp;$".number_format($valor_luego_copagos,0,'.',',' )."</td></tr>";
							
						$respresumido.= "<tr align='left'  width='100%'  class='doted2' >
												<td   width='70%' colspan='9'>(Copagos,Cuotas de recuperacion y Franquicias)</td>
							<td align='right'>&nbsp;$".number_format($valor_luego_copagos,0,'.',',' )."</td>
							<td align='right'  C style='display:none'></td>
							<td align='right'>&nbsp;$".number_format(0,0,'.',',' )."</td>
							<td align='right'>&nbsp;$".number_format($valor_luego_copagos,0,'.',',' )."</td></tr>";
					}
					else
					{
							
							$Total_exe_cuenta= $Total_exe_cuenta + $valor_luego_copagos;
							$Total_r_e_cuenta= $Total_r_e_cuenta + $valor_luego_copagos;
							$resp .= "<tr align='left' class='".$tipoFact." conceptos'    width='100%' ><td  width='70%' colspan='6' style='font-weight:bold;background-color:#D7EBF9;color:#2779AA;cursor:pointer; border-radius:4px; border: 1px solid #2779AA;'>(Copagos,Cuotas de recuperacion y Franquicias)</td>
								
								<td id='valorextra_coutas'   class='valorextra' style='font-weight:bold;background-color:#D7EBF9;color:#2779AA; border-radius:4px; border: 1px solid #2779AA; display:none' align='right'>
								
								</td>
								<td style='font-weight:bold;background-color:#D7EBF9;color:#2779AA; border-radius:4px; border: 1px solid #2779AA;' align='right'><div id='divValorReconocido_copagos'>$".number_format(0,0,'.',',' )."</div></td>
								
								<td style='font-weight:bold;background-color:#D7EBF9;color:#2779AA; border-radius:4px; border: 1px solid #2779AA;' align='right'><div id='divValorExcedente_Copagos'>$".number_format($valor_luego_copagos,0,'.',',' )."</div></td>
								<td style='font-weight:bold;background-color:#D7EBF9;color:#2779AA; border-radius:4px; border: 1px solid #2779AA;' align='right'>$".number_format($valor_luego_copagos,0,'.',',' )."</td></tr>";
							
							$respdetallado .= "<tr align='left' class='".$tipoFact." conceptos'   width='100%' ><td  width='70%' colspan='6' >(Copagos,Cuotas de recuperacion y Franquicias)</td>
								<td align='right'>&nbsp;$".number_format(0,0,'.',',' )."</td>
								<td align='right'>&nbsp;$".number_format($valor_luego_copagos,0,'.',',' )."</td>
								<td align='right'>&nbsp;$".number_format($valor_luego_copagos,0,'.',',' )."</td></tr>";
								
							$respresumido.= "<tr align='left'  width='100%'  class='doted2' >
												<td   width='70%' colspan='9'>(Copagos,Cuotas de recuperacion y Franquicias)</td>
								<td align='right'>&nbsp;$".number_format(0,0,'.',',' )."</td>
								<td align='right' class='valorextra' style='display:none'></td>
								<td align='right'>&nbsp;$".number_format($valor_luego_copagos,0,'.',',' )."</td>
								<td align='right'>&nbsp;$".number_format($valor_luego_copagos,0,'.',',' )."</td></tr>";
						
					}
					

					//------
						//----------------------------
					$resp.="
					<tr class='".$tipoFact." conceptos'   style='font-size: 9pt;font-family: verdana;'>
						<td colspan='3'></td>
						<td colspan='3' style='color:#2a5db0' nowrap=nowrap><b><div id='subtotal'>TOTALES CUENTA:&nbsp;</div></b></td>
						<td style='color:#2a5db0;display:none' nowrap=nowrap class='valorextra'  ><b><div id='totalgeneralextra'></div></b></td>
						<td align='right' style='color:#2a5db0'><b><input type='hidden' id='totalcuentareconocido' value='".$Total_rec_cuenta."' valor='".$Total_rec_cuenta."'><div id='totaltotalreconocido' valor='".$Total_rec_cuenta."'>$".number_format($Total_rec_cuenta,0,'.',',' )."</div></b></td>
						
<td align='right' style='color:#2a5db0'><b><input type='hidden' id='totalcuentaexcedente' value='".$Total_exe_cuenta."'><div id='totaltotalexcedente'>$".number_format($Total_exe_cuenta,0,'.',',' )."</div></b></td>
						<td align='right' style='color:#2a5db0'><b><input type='hidden' id='totalcuenta' value='".$Total_r_e_cuenta."'><div id='totaltotalcuenta' valororiginal='".($Total_r_e_cuenta + $saldoauxiliar)."'>$".number_format($Total_r_e_cuenta + $saldoauxiliar,0,'.',',' )."</div></b></td>
					</tr>";
					
					$resp.="
					<tr class='".$tipoFact." conceptos valorextra valorextra2'   style='font-size: 9pt;font-family: verdana; display:none'>
						<td colspan='3'></td>
						<td colspan='3' style='color:#2a5db0' nowrap=nowrap><b>VALOR EXTRA:&nbsp;</b></td>
						<td style='color:#2a5db0;' nowrap=nowrap   ><b><div ></div></b></td>
						<td align='right' style='color:#2a5db0'><b><input type='hidden' ><div ></div></b></td>
						<td align='right' style='color:#2a5db0'><b><input type='hidden' ><div id='divvalorextra' ></div></b></td>
						<td align='right' style='color:#2a5db0'><b><input type='hidden'><div ></div></b></td>
					</tr>";
					
				
					$resp.="
					<tr class='".$tipoFact." conceptos valorextra valorextra2'   style='font-size: 9pt;font-family: verdana; display:none'>
						<td colspan='3'></td>
						<td colspan='3' style='color:#2a5db0' nowrap=nowrap><b>TOTALES:&nbsp;</b></td>
						<td style='color:#2a5db0;' nowrap=nowrap   ><b><div ></div></b></td>
						<td align='right' style='color:#2a5db0'><b><input type='hidden' ><div id='divvalorreconocidototal'  ></div></b></td>
						<td align='right' style='color:#2a5db0'><b><input type='hidden' ><div id='divvalorexedentetotal'  ></div></b></td>
						<td align='right' style='color:#2a5db0'><b><input type='hidden'><div  id='divvalorextratotal'  ></div></b></td>
					</tr>";
					
					
					//-------------
					$respdetallado.="<tr><td colspan='9'>&nbsp;</td></tr>
					<tr  class='doted2'>
						<td colspan='3'></td>
						<td colspan='3'  nowrap=nowrap >&nbsp;<b><div>TOTALES CUENTA:</div></b></td>
						<td align='right' nowrap=nowrap class='block'>&nbsp;<b><div id='divreconocidoresumidod'>$".number_format($Total_rec_cuenta,0,'.',',' )."</div></b></td>
						<td align='right' nowrap=nowrap class='block'>&nbsp;<b><div id='divExcedenteResumidod' class='block'>$".number_format($Total_exe_cuenta,0,'.',',' )."</div></b></td>
						<td align='right' nowrap=nowrap class='block'>&nbsp;<b><div id='divtotalResumido' class='block'>$".number_format($Total_r_e_cuenta + $saldoauxiliar ,0,'.',',' )."</div></b></td>
					</tr>";
					//---------------
					
					
					
					$respresumido.="
					<tr  class='doted2'>
						<td colspan='6'></td>
						<td colspan='3'  nowrap=nowrap >&nbsp;<b><div id='subtotalresumido'>TOTALES CUENTA:</div></b></td>
						<td align='right' nowrap=nowrap class='block'>&nbsp;<b><div id='divreconocidoresumido'>$".number_format($Total_rec_cuenta,0,'.',',' )."</div></b></td>
						<td align='right' nowrap=nowrap class='block valorextra' style='display:none'>&nbsp;<b><div id=''></div></b></td>
						<td align='right' nowrap=nowrap class='block'>&nbsp;<b><div id='divExcedenteResumido' class='block'>$".number_format($Total_exe_cuenta,0,'.',',' )."</div></b></td>
						<td align='right' nowrap=nowrap class='block'>&nbsp;<b><div id='divtotalResumido' class='block'>$".number_format($Total_r_e_cuenta + $saldoauxiliar ,0,'.',',' )."</div></b></td>
					</tr>";
					
					
					$respresumido.="
					<tr  class='doted2 valorextra2'>
						<td colspan='6'></td>
						<td colspan='3'  nowrap=nowrap >&nbsp;<b><div>VALOR EXTRA</div></b></td>
						<td align='right' nowrap=nowrap class='block'>&nbsp;<b><div id=''></div></b></td>
						<td align='right' nowrap=nowrap class='block valorextra' style='display:none'>&nbsp;<b><div ></div></b></td>
						<td align='right' nowrap=nowrap class='block'>&nbsp;<b><div id='divrextraresumido'></div></b></td>
						<td align='right' nowrap=nowrap class='block'>&nbsp;<b><div  class='block'></div></b></td>
					</tr>";
					
					$respresumido.="
					<tr  class='doted2 valorextra2'>
						<td colspan='6'></td>
						<td colspan='3'  nowrap=nowrap >&nbsp;<b><div>TOTAL:</div></b></td>
						<td align='right' nowrap=nowrap class='block'>&nbsp;<b><div id='divreconocidototalresumido'></div></b></td>
						<td align='right' nowrap=nowrap class='block valorextra'>&nbsp;<b></b></td>
						<td align='right' nowrap=nowrap class='block'>&nbsp;<b><div id='divexcedentetotalresumido'></div></b></td>
						<td align='right' nowrap=nowrap class='block'>&nbsp;<b><div  class='block' id='divtotalresumido'></div></b></td>
					</tr>";

				}
			
				$resp.= "</table><br><br>";
				$respdetallado.= "</table>";
				$respresumido.= "</table>";
				
				$html .= $respencabezado;
				$html .= $resp; 
				$html2 .= $respresumido;
				$html3 .= $respdetallado;
				
				//--------------------------------
				
				//-----Total
				// $html.="<tr><td colspan='6'>&nbsp;</td></tr>";
				// $html.="<tr>
						// <td colspan='1'></td>
						// <td colspan='1' align='right' class='encabezadoTabla' style='font-size: 8pt;font-family: verdana;'><b>TOTALES:&nbsp;</b></td>
						// <td class='encabezadoTabla' align='right'>&nbsp;$".number_format($Total_rec,0,',','.' )."</td>
						// <td class='encabezadoTabla' align='right'>&nbsp;$".number_format($Total_exe,0,',','.' )."</td>
						// <td class='encabezadoTabla' align='right'>&nbsp;$".number_format($Total_r_e,0,',','.' )."</td>
						// <td></td>
					// </tr>";
				
				//--------------
				
				// $html.="</table>";
		
		
		
		
		$html.="<div id='EstCuentaResumido' style='display:none'>".$html2."</div>"; 
		$html.="<div id='EstCuentadetallado' style='display:none'>".$html3."</div>"; 
		//$html.="<div id='EstCuentaDetallado' style='display:none'>".$html3."</div>"; 
		
		
		echo $html;

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
		case 'resumen_pension':
		{
			resumen_pension($whistoria, $wing,$wtar,$wempresa,$wconcepto,$wtipo_ingreso,$wcambiodetipos,$wtipo_paciente, $wfechaparcial, $wcedula);
			break;
			return;
		}
		case 'cargar_datos':
		{
			$data = cargar_datos($whistoria, $wing, $wcargos_sin_facturar, $welemento);
			echo json_encode($data);
			break;
			return;
		}
		case 'cambiarvalor_imprimir':
		{
			break; 
			return;
		}
		case 'imprimirSoporte':
		{
			global $wbasedato;
			global $conex;
			global $wemp_pmla;
			global $wuse;
			$respuesta 		= array('Error' => false, 'Html' => '', 'Mensaje' => '');
			$wbasedatoMov 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
			
			$respuesta['Html'] = "
			<html>
				<head></head>
				<body style='margin:0mm'>
				<style type='text/css'>
					.doted{
						font-family: 'Courier New';
						font-size:3.5mm;
						font-weight: 400;
					}
					.doted2{
						font-family: 'Courier New';
						font-size:3.5mm;
						font-weight: 400;
					}
					.borde{					
						border:1px;
						border-style:dotted dotted dotted dotted;
						font-weight: 400;
					}
				</style>";
				
				
				// --> Consultar información del paciente
			$sqlInfoPac = "
			SELECT Pacno1, Pacno2, Pacap1, Pacap2, Pactdo, Pacdoc, Pacfna, Ingfei, Inghin, Ingtpa, Empnom, Empnit, Tarcod, Tardes,
			       Descripcion, Medno1, Medno2, Medap1, Medap2
			  FROM ".$wbasedato."_000100 AS A INNER JOIN ".$wbasedato."_000101 AS B ON(A.Pachis = B.Inghis AND B.Ingnin = '".$ingreso."')
				   LEFT  JOIN ".$wbasedato."_000024 	AS C ON(B.Ingcem = C.Empcod)
				   LEFT  JOIN ".$wbasedato."_000025 	AS D ON(C.Emptar = D.Tarcod)
				   LEFT  JOIN root_000011 				AS E ON(B.Ingdig = E.Codigo)
				   LEFT  JOIN ".$wbasedatoMov."_000048	AS F ON(B.Ingmei = F.Meddoc)
			 WHERE A.Pachis = '".$historia."'
			";
			$resInfoPac = mysql_query($sqlInfoPac, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInfoPac):</b><br>".mysql_error());
			if($rowInfoPac = mysql_fetch_array($resInfoPac))
			{
				// --> Si es tipo de empresa particular
				if($rowInfoPac['Ingtpa'] == 'P')
				{
					$codigoEmpPart = consultarAliasPorAplicacion($conex, $wemp_pmla, 'codigoempresaparticular');
					$sqlSqlEmpPart = "
					SELECT Empnit, Empnom, Emptar, Tardes
					  FROM ".$wbasedato."_000024 INNER JOIN ".$wbasedato."_000025 ON (Emptar = Tarcod)
					 WHERE Empcod = '".trim($codigoEmpPart)."'
					";
					$resSqlEmpPart = mysql_query($sqlSqlEmpPart, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlSqlEmpPart):</b><br>".mysql_error());
					if($rowSqlEmpPart = mysql_fetch_array($resSqlEmpPart))
					{
						$rowInfoPac['Empnom'] = $rowSqlEmpPart['Empnom'];
						$rowInfoPac['Empnit'] = $rowSqlEmpPart['Empnit'];
						$rowInfoPac['Tarcod'] = $rowSqlEmpPart['Emptar'];
						$rowInfoPac['Tardes'] = $rowSqlEmpPart['Tardes'];
					}
				}
				
				// --> Calcular edad
				$diff 	= abs(strtotime(date('Y-m-d')) - strtotime($rowInfoPac['Pacfna']));
				$years 	= floor($diff / (365*60*60*24));
				$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
				$edad	= (($years >= 0) ? $years : $months);
				$wfecha=date("Y-m-d");
				$whora = date("H:i:s");
				$encabezado 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'encabezadoImpresionSoporteCargoFacInt');
				$datosBasicos 	= "
				<table width='100%' class='doted' style='border:1px;border-style:none dotted none dotted;'>
					<tr>
						<td style='border:1px;border-style:dotted none dotted none;' align='center' colspan='3'>".$encabezado."</td>
					</tr>
					<tr>
						<td style='border:1px;border-style:none none dotted none;' colspan='3' align='left'><b>PACIENTE</b></td>
					</tr>
					<tr class='doted2'>
						<td align='left' nowrap='nowrap'>NOMBRE:".$rowInfoPac['Pacno1']." ".$rowInfoPac['Pacno2']." ".$rowInfoPac['Pacap1']." ".$rowInfoPac['Pacap2']."</td>
						<td align='left' nowrap='nowrap'>&nbsp;IDENTIF.:".$rowInfoPac['Pactdo']."-".$rowInfoPac['Pacdoc']."&nbsp;&nbsp;EDAD:".$edad."</td>
						<td align='left'>&nbsp;HISTORIA:".$historia."-".$ingreso."</td>
					</tr>
					<tr class='doted2'>
						<td align='left'>FECHA/HORA:".$rowInfoPac['Ingfei']."/".$rowInfoPac['Inghin']."</td>				
						<td align='left'>&nbsp;MEDICO:<span style='font-size:3mm;'>".$rowInfoPac['Medno1']." ".$rowInfoPac['Medno2']." ".$rowInfoPac['Medap1']." ".$rowInfoPac['Medap2']."</span></td>				
						<td align='left'>&nbsp;DIAGNOST.:<span style='font-size:3mm;'>".$rowInfoPac['Descripcion']."</span></td>				
					</tr>
					<tr>
						<td style='border:1px;border-style:dotted none dotted none;' colspan='3' align='left'><b>RESPONSABLE</b></td>
					</tr>
					<tr class='doted2'>
						<td align='left' nowrap='nowrap'>EMPRESA:".trim($rowInfoPac['Empnom'])."</td>
						<td align='left' nowrap='nowrap'>CED/NIT.:&nbsp;".$rowInfoPac['Empnit']."</td>
						<td align='left'>TARIFA:&nbsp;".trim($rowInfoPac['Tarcod'])."-".trim($rowInfoPac['Tardes'])."</td>
					</tr>
					<tr class='doted2'>
						<td align='left' colspan='3' nowrap='nowrap'>CORTE A : ".$wfecha." HORA : ".$whora."</td>
					</tr>
					";				
				
				$respuesta['Html'].= "
				<table width='100%' style='font-family:Courier New;font-size:2.5mm;font-weight:600;'>
					<tr><td align='right'>Pagina 1</td></tr>
				</table>".$datosBasicos;
			}
			else
			{
				$respuesta['Error'] 	= true;
				$respuesta['Mensaje'] 	= "No se encontro información del paciente";			
				$respuesta['Mensaje'] 	= utf8_encode($respuesta['Mensaje']);
				echo json_encode($respuesta);
				return;
			}
				
				
				
				
				$respuesta['Html'].=utf8_decode(str_replace('\\', '', $wcontenido))."
				<br><table width='100%' class='doted'>
					<tr>
						<td style='font-size:3.0mm;' align='left'>Observaciones: Senor usuario el estado de cuenta que acabamos de presentarle corresponde al valor de los servicios
																  prestados y reportados al sistema desde el momento del ingreso hasta la fecha y hora de corte especificada. No incluye
																  tarifa de anestesia hi honorarios medicos.
						</td>
					</tr>
				</table>
				<table width='100%' class='doted'>
					<tr>
						<td style='font-size:2.5mm;' align='right'>Fecha Imp:".date("Y-m-d")."&nbsp;&nbsp;Hora Imp:".date("H:i:s")."&nbsp;&nbsp;Usuario:".$wuse."</td>
					</tr>
				</table>
				</body>
				</html>";
			
			
			if(!$respuesta['Error'])
			{
				$wnombrePDF 	= "estadoCuenta_".$historia."-".$ingreso;
				$archivo_dir 	= "soportes/".$wnombrePDF.".html";
				$dir			= "soportes";
				
				if(is_dir($dir)){ }
				else { mkdir($dir,0777); }
			
				if(file_exists($archivo_dir)){
					unlink($archivo_dir);
				}
				
				$f = fopen($archivo_dir, "w+" );
				fwrite($f, $respuesta['Html']);
				fclose($f);
				
				if(file_exists("soportes/".$wnombrePDF.".pdf")){
					unlink("soportes/".$wnombrePDF.".pdf");
				}
				
				// chmod("./generarPdf_soportesCargos.sh", 0777);
				shell_exec( "./generarPdf_soportesCargos.sh ".$wnombrePDF );
				
				$respuesta['Html'] = "	
					<object type='application/pdf' data='../../../matrix/ips/procesos/soportes/".$wnombrePDF.".pdf' pdf#toolbar=1&amp;navpanes=0&amp;scrollbar=1 width='800' height='500'>"
					  ."<param name='src' value='soportes/".$wnombrePDF."' pdf#toolbar=1&amp;navpanes=0&amp;scrollbar=1 />"
					  ."<p style='text-align:center; width: 60%;'>"
						."Adobe Reader no se encuentra o la versión no es compatible, utiliza el icono para ir a la página de descarga <br />"
						."<a href='//get.adobe.com/es/reader/' onclick='this.target=\"_blank\">"
						  ."<img src='../../images/medical/root/prohibido.gif' alt='Descargar Adobe Reader' width='32' height='32' style='border: none;' />"
						."</a>"
					  ."</p>"
					."</object>
					<br>
				";
			}
			
			
			$respuesta['Mensaje'] 	= utf8_encode($respuesta['Mensaje']);
			$respuesta['Html'] 		= utf8_encode($respuesta['Html']);
			$respuesta['nombrePdf']	= $wnombrePDF;
			echo json_encode($respuesta);
			break;
		}
	}
}

//=======================================================================================================================================================
//	I N I C I O   E J E C U C I O N   N O R M A L   D E L   P R O G R A M A
//=======================================================================================================================================================
else
{
	?>
	<html>
	<head>
	  <title>Liquidación Pensión</title>
	</head>

		<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
		<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
		<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
		<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>
		<script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>
		<link type="text/css" href="../../../include/root/jquery.ui.timepicker.css" rel="stylesheet"/>
		<script src="../../../include/root/toJson.js" type="text/javascript"></script>
		<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
		<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
		<script type="text/javascript">
//=====================================================================================================================================================================
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T
//=====================================================================================================================================================================
	var url_add_params = addUrlCamposCompartidosTalento();
	var ArrayValores ;
	var ArrayValoresTerceros;
	var Arrayporcentajesterceros;
	var Arrayterceros;
	var ArraytercerosEspecialidad = new Array();

	$(document).ready(function() {

		// --> Crear variable compartidas para todo el gestor
		
		crear_variables_compartidas();
		
		//cargar_datos_caja();
		cargar_datos('wing');
		
		$("#accordionDatosPaciente").show();
		$("#accordionDatosPaciente" ).accordion("destroy");
		$("#accordionDatosPaciente" ).accordion({
			collapsible: true,
			heightStyle: "content"
		});

		$("#DatosPaciente").css( "display" , "block");
		
		$( "#accordionPension" ).show();
		$( "#accordionPension" ).accordion({
			collapsible: true,
			heightStyle: "fill"

		});

		// $("#detalle_liquidacion_general").css( "display" , "block");
			
		// $("#accordionDetCuenta" ).show();
		// $( "#accordionDetCuenta" ).accordion({
			// collapsible: true,
			// heightStyle: "fill",
			// active: -1
		// });

		// --> Cargar tooltips
		$('.tooltip').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });

		// --> se carga el datepicker wfeccar
		cargar_elementos_datapicker();
		$("#wfeccar").datepicker({
			showOn: "button",
			buttonImage: "../../images/medical/root/calendar.gif",
			buttonImageOnly: true,
			maxDate:"+0D"
		});

		// --> Actualizar la fecha y la hora desde el servidor
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:      		'',
			wemp_pmla:         		$('#wemp_pmla').val(),
			accion:            		'horaFechaDelServidor'
		}, function (data){
			$('#wfeccar').val(data.Fecha);
		}, 'json');

	});
	
	
	
	function traertope()
	{
		activar_regex_miles();
		cargar_elementos_datapicker();
		$("#fechatope").datepicker({
		showOn: "button",
		buttonImage: "../../images/medical/root/calendar.gif",
		buttonImageOnly: true,
		maxDate:"+0D"
		});
		$(".tope").show();
		//alert($("#wfecing").html());
		$("#fechatope").val($("#wfecing").html());
		
	}
	
	function ajustartope()
	{
		var tope = $("#tope").val();
		var porcentaje = $("#porcentaje").val();
		tope = tope.replace(/,/gi, "");
		tope = tope * 1 ;
		var fechatope = $("#fechatope").val();
		
		if( tope=='' || tope == 0 )
		{
			$(".recorrerporcentaje").each(function(){
						
						//alert("entro4");
						var concepto = $(this).attr('concepto');
						var reconocidoaux = 0;
						var fecha;
						var partereconocidodetalle = 0;
						var parteexedentedetalle   = 0;
						var partereconocidototal = 0;
						var parteexcedentetotal = 0;
						
						if($("#wconceptoestanciaXparametro").val() != concepto)
						{
						
							$(".valorOcultoReconocidoExcedente_"+concepto).each(function (){ 
								
								
								fecha  		  = $(this).attr('fecha');
								//alert(fecha);
								reconocidoaux = $(this).attr('reconocido');
								reconocidoaux = reconocidoaux.replace("$", "");
								reconocidoaux = reconocidoaux.replace(/,/gi, "");
								reconocidoaux = reconocidoaux * 1 ;
								
								excedenteaux  = $(this).attr('excedente');
								excedenteaux  = excedenteaux.replace("$", "");
								excedenteaux  = excedenteaux.replace(/,/gi, "");
								excedenteaux  = excedenteaux * 1;
								
								
								//-- si el valor reconocido del detalle es mayor resto el tope y lo pongo en cero y divido el valor reconocido y el excedente y me salgo del ciclo
								contador = $(this).attr('contador');
								
								if (porcentaje*1 == 100)
								{
									partereconocidodetalle = reconocidoaux;
									parteexedentedetalle   = excedenteaux;
									
								}
								else
								{
									var total = (reconocidoaux * 1 ) + (excedenteaux * 1);
									partereconocidodetalle = (total * ( porcentaje / 100) );
									parteexedentedetalle   = total - partereconocidodetalle;
									
								}
								
								
							
								//contador = $(this).attr('contador');
								$("#tdreconocidodetalle"+contador).html(formatearnumero(partereconocidodetalle));
								$("#tdexcendentedetalle"+contador).html(formatearnumero(parteexedentedetalle));
								
								
								$("#tdreconocidodetalle2_"+contador).html(formatearnumero(partereconocidodetalle));
								$("#tdexcendentedetalle2_"+contador).html(formatearnumero(parteexedentedetalle));
				
								
								parteexcedentetotal = parteexcedentetotal + parteexedentedetalle ;
								partereconocidototal = partereconocidototal + partereconocidodetalle;
								
							});
						
							//alert("hoola")
							$("#divValorReconocido_"+concepto).html("$"+formatearnumero(partereconocidototal));
							$("#div2ValorReconocido_"+concepto).html("$"+formatearnumero(partereconocidototal));
							$("#divValorExcedente_"+concepto).html("$"+formatearnumero(parteexcedentetotal));
							$("#div2ValorExcedente_"+concepto).html("$"+formatearnumero(parteexcedentetotal));
							$("#divreconocidoTotal_"+concepto).html("$"+formatearnumero(partereconocidototal));
							$("#divexcenteTotal_"+concepto).html("$"+formatearnumero(parteexcedentetotal));
				
							
						}

			});			
			
		}
		else 
		{
				
				$(".recorrerporcentaje").each(function(){
					
					
					var concepto = $(this).attr('concepto');
					var reconocidoaux = 0;
					var partereconocidodetalle = 0;
					var parteexedentedetalle   = 0;
					var partereconocidototal = 0;
					var parteexcedentetotal = 0;
					var fecha;
					if($("#wconceptoestanciaXparametro").val() != concepto)
					{
					
						$(".valorOcultoReconocidoExcedente_"+concepto).each(function (){ 
							
							
							fecha  		  = $(this).attr('fecha');
							
							if ( fecha >= fechatope)
							{
								//alert("fecha :"+fecha+" fecha tope:"+fechatope);
								//alert("entro");
								//alert("entro");
								reconocidoaux = $(this).attr('reconocido');
								reconocidoaux = reconocidoaux.replace("$", "");
								reconocidoaux = reconocidoaux.replace(/,/gi, "");
								reconocidoaux = reconocidoaux * 1 ;
								
								excedenteaux  = $(this).attr('excedente');
								excedenteaux  = excedenteaux.replace("$", "");
								excedenteaux  = excedenteaux.replace(/,/gi, "");
								excedenteaux  = excedenteaux * 1;
								
								
								//-- si el valor reconocido del detalle es mayor resto el tope y lo pongo en cero y divido el valor reconocido y el excedente y me salgo del ciclo
								contador = $(this).attr('contador');
								
								if(reconocidoaux == 0)
								{
									parteexedentedetalle = excedenteaux;
									partereconocidodetalle = reconocidoaux;
									//parteexcedentetotal = parteexcedentetotal + parteexedentedetalle ;
									//partereconocidototal = partereconocidototal + partereconocidodetalle;
									//alert("entro 1 --- "+partereconocidodetalle + "parte excedente "+parteexedentedetalle);
									
									
									
								}
								else
								{
									//alert("entro2")
									if(tope >= reconocidoaux )
									{
								
										if (porcentaje*1 == 100)
										{
											partereconocidodetalle = reconocidoaux;
											parteexedentedetalle   = excedenteaux;
											
										}
										else
										{
											var total = (reconocidoaux * 1 ) + (excedenteaux * 1);
											partereconocidodetalle = (total * ( porcentaje / 100) );
											parteexedentedetalle   = total - partereconocidodetalle;
											
										}
										
										
										tope = tope - partereconocidodetalle ;
										
										
									
										//contador = $(this).attr('contador');
										$("#tdreconocidodetalle"+contador).html(formatearnumero(partereconocidodetalle));
										$("#tdexcendentedetalle"+contador).html(formatearnumero(parteexedentedetalle));
										
										
										$("#tdreconocidodetalle2_"+contador).html(formatearnumero(partereconocidodetalle));
										$("#tdexcendentedetalle2_"+contador).html(formatearnumero(parteexedentedetalle));
						
									
									}
									else
									{
										
										if (porcentaje*1 == 100)
										{
											partereconocidodetalle = reconocidoaux;
											parteexedentedetalle   = excedenteaux;
											partereconocidodetalle = tope;
											parteexedentedetalle   = (reconocidoaux  - tope ) + excedenteaux;
											tope = 0 ;
											
										}
										else
										{
											var total = (reconocidoaux * 1 ) + (excedenteaux * 1);
											partereconocidodetalle = (total * ( porcentaje / 100) );
											parteexedentedetalle   = total - partereconocidodetalle;
											if(tope >  partereconocidodetalle)
											{
												tope =  tope - partereconocidodetalle;
											}
											else
											{
												
												partereconocidodetalle = tope;
												parteexedentedetalle   = (reconocidoaux  - tope ) + excedenteaux;
												tope = 0 ;
											}
											
											
										}
										
										
										
										//partereconocidodetalle = tope;
										
										
										
										$("#tdreconocidodetalle"+contador).html(formatearnumero(partereconocidodetalle));
										$("#tdexcendentedetalle"+contador).html(formatearnumero(parteexedentedetalle));
										
										
										$("#tdreconocidodetalle2_"+contador).html(formatearnumero(partereconocidodetalle));
										$("#tdexcendentedetalle2_"+contador).html(formatearnumero(parteexedentedetalle));
										
										
									}
									
								}
								
								parteexcedentetotal = parteexcedentetotal + parteexedentedetalle ;
								partereconocidototal = partereconocidototal + partereconocidodetalle;
							
							}
							else
							{
								//alert("entro3");
								reconocidoaux = $(this).attr('reconocido');
								reconocidoaux = reconocidoaux.replace("$", "");
								reconocidoaux = reconocidoaux.replace(/,/gi, "");
								reconocidoaux = reconocidoaux * 1 ;
								
								excedenteaux  = $(this).attr('excedente');
								excedenteaux  = excedenteaux.replace("$", "");
								excedenteaux  = excedenteaux.replace(/,/gi, "");
								excedenteaux  = excedenteaux * 1;
								
								parteexedentedetalle   =  excedenteaux;
								partereconocidodetalle =  reconocidoaux;
								parteexcedentetotal = parteexcedentetotal + parteexedentedetalle ;
								partereconocidototal = partereconocidototal + partereconocidodetalle
							}
							
						});
					
						//alert("hoola")
						$("#divValorReconocido_"+concepto).html("$"+formatearnumero(partereconocidototal));
						$("#div2ValorReconocido_"+concepto).html("$"+formatearnumero(partereconocidototal));
						$("#divValorExcedente_"+concepto).html("$"+formatearnumero(parteexcedentetotal));
						$("#div2ValorExcedente_"+concepto).html("$"+formatearnumero(parteexcedentetotal));
						$("#divreconocidoTotal_"+concepto).html("$"+formatearnumero(partereconocidototal));
						$("#divexcenteTotal_"+concepto).html("$"+formatearnumero(parteexcedentetotal));
			
						
					}	
				});
		}
		
				var reconocidodetalletotal = 0;
				var valorpresente = 0;
				$('div[id ^=divValorReconocido_]').each(function (){
					  
					valorpresente =$(this).html();
					valorpresente = valorpresente.replace("$", "");
					valorpresente = valorpresente.replace(/,/gi, "");
					//alert(valorpresente);
					reconocidodetalletotal = reconocidodetalletotal*1  +  valorpresente*1;
				});
				
				var excedentedetalletotal = 0;
				var valorpresente = 0;
				$('div[id ^=divValorExcedente_]').each(function (){
					  
					valorpresente =$(this).html();
					valorpresente = valorpresente.replace("$", "");
					valorpresente = valorpresente.replace(/,/gi, "");
					//alert(valorpresente);
					excedentedetalletotal = excedentedetalletotal*1  +  valorpresente*1;
				});
				
				//----------------------
				// miro el valor excedente de la hospitalizacion
				
				if($("#excedenteHospitalizacion").length > 0)
				{
					var varexcedentehospitalizacion = $("#excedenteHospitalizacion").val();
					excedentedetalletotal = excedentedetalletotal*1 + varexcedentehospitalizacion*1;
				}
				//---------------------
				$("#totaltotalreconocido").html("$"+formatearnumero(reconocidodetalletotal*1));
				$("#totaltotalexcedente").html("$"+formatearnumero(excedentedetalletotal*1));
				
				$("#totalcuentareconocido").val(reconocidodetalletotal*1);
				$("#totalcuentaexcedente").val(excedentedetalletotal*1);
				
				$("#divreconocidoresumido").html("$"+formatearnumero(reconocidodetalletotal*1));
				$("#divExcedenteResumido").html("$"+formatearnumero(excedentedetalletotal*1));
				
				$("#divreconocidoresumidod").html("$"+formatearnumero(reconocidodetalletotal*1));
				$("#divExcedenteResumidod").html("$"+formatearnumero(excedentedetalletotal*1));
				
		
	}
	/*
	function ajustartope()
	{
		var tope = $("#tope").val();
		tope = tope.replace(/,/gi, "");
		tope = tope * 1 ;
		var fechatope = $("#fechatope").val();
		if( tope=='' || tope == 0 )
		{
			$(".recorrerporcentaje").each(function(){
						//alert("entro4");
						var concepto = $(this).attr('concepto');
						var reconocidoaux = 0;
						var fecha;
						var partereconocidodetalle = 0;
						var parteexedentedetalle   = 0;
						var partereconocidototal = 0;
						var parteexcedentetotal = 0;
						
						if($("#wconceptoestanciaXparametro").val() != concepto)
						{
						
							$(".valorOcultoReconocidoExcedente_"+concepto).each(function (){ 
								
								
								fecha  		  = $(this).attr('fecha');
								//alert(fecha);
								reconocidoaux = $(this).attr('reconocido');
								reconocidoaux = reconocidoaux.replace("$", "");
								reconocidoaux = reconocidoaux.replace(/,/gi, "");
								reconocidoaux = reconocidoaux * 1 ;
								
								excedenteaux  = $(this).attr('excedente');
								excedenteaux  = excedenteaux.replace("$", "");
								excedenteaux  = excedenteaux.replace(/,/gi, "");
								excedenteaux  = excedenteaux * 1;
								
								
								//-- si el valor reconocido del detalle es mayor resto el tope y lo pongo en cero y divido el valor reconocido y el excedente y me salgo del ciclo
								contador = $(this).attr('contador');
								
							
								
								partereconocidodetalle = reconocidoaux;
								parteexedentedetalle   = excedenteaux;
							
								//contador = $(this).attr('contador');
								$("#tdreconocidodetalle"+contador).html(formatearnumero(partereconocidodetalle));
								$("#tdexcendentedetalle"+contador).html(formatearnumero(parteexedentedetalle));
								
								
								$("#tdreconocidodetalle2_"+contador).html(formatearnumero(partereconocidodetalle));
								$("#tdexcendentedetalle2_"+contador).html(formatearnumero(parteexedentedetalle));
				
								
								parteexcedentetotal = parteexcedentetotal + parteexedentedetalle ;
								partereconocidototal = partereconocidototal + partereconocidodetalle;
								
							});
						
							//alert("hoola")
							$("#divValorReconocido_"+concepto).html("$"+formatearnumero(partereconocidototal));
							$("#div2ValorReconocido_"+concepto).html("$"+formatearnumero(partereconocidototal));
							$("#divValorExcedente_"+concepto).html("$"+formatearnumero(parteexcedentetotal));
							$("#div2ValorExcedente_"+concepto).html("$"+formatearnumero(parteexcedentetotal));
							$("#divreconocidoTotal_"+concepto).html("$"+formatearnumero(partereconocidototal));
							$("#divexcenteTotal_"+concepto).html("$"+formatearnumero(parteexcedentetotal));
				
							
						}

			});			
			
		}
		else 
		{
				
				$(".recorrerporcentaje").each(function(){
					
					
					var concepto = $(this).attr('concepto');
					var reconocidoaux = 0;
					var partereconocidodetalle = 0;
					var parteexedentedetalle   = 0;
					var partereconocidototal = 0;
					var parteexcedentetotal = 0;
					var fecha;
					if($("#wconceptoestanciaXparametro").val() != concepto)
					{
					
						$(".valorOcultoReconocidoExcedente_"+concepto).each(function (){ 
							
							
							fecha  		  = $(this).attr('fecha');
							
							if ( fecha >= fechatope)
							{
								//alert("fecha :"+fecha+" fecha tope:"+fechatope);
								//alert("entro");
								//alert("entro");
								reconocidoaux = $(this).attr('reconocido');
								reconocidoaux = reconocidoaux.replace("$", "");
								reconocidoaux = reconocidoaux.replace(/,/gi, "");
								reconocidoaux = reconocidoaux * 1 ;
								
								excedenteaux  = $(this).attr('excedente');
								excedenteaux  = excedenteaux.replace("$", "");
								excedenteaux  = excedenteaux.replace(/,/gi, "");
								excedenteaux  = excedenteaux * 1;
								
								
								//-- si el valor reconocido del detalle es mayor resto el tope y lo pongo en cero y divido el valor reconocido y el excedente y me salgo del ciclo
								contador = $(this).attr('contador');
								
								if(reconocidoaux == 0)
								{
									parteexedentedetalle = excedenteaux;
									partereconocidodetalle = reconocidoaux;
									//parteexcedentetotal = parteexcedentetotal + parteexedentedetalle ;
									//partereconocidototal = partereconocidototal + partereconocidodetalle;
									//alert("entro 1 --- "+partereconocidodetalle + "parte excedente "+parteexedentedetalle);
								}
								else
								{
									//alert("entro2")
									if(tope >= reconocidoaux )
									{
										tope = tope - reconocidoaux ;
										
										partereconocidodetalle = (reconocidoaux);

										parteexedentedetalle   = excedenteaux;
									
										//contador = $(this).attr('contador');
										$("#tdreconocidodetalle"+contador).html(formatearnumero(partereconocidodetalle));
										$("#tdexcendentedetalle"+contador).html(formatearnumero(parteexedentedetalle));
										
										
										$("#tdreconocidodetalle2_"+contador).html(formatearnumero(partereconocidodetalle));
										$("#tdexcendentedetalle2_"+contador).html(formatearnumero(parteexedentedetalle));
						
									
									}
									else
									{
										

										partereconocidodetalle = tope;
										
										
										parteexedentedetalle   = (reconocidoaux  - tope ) + excedenteaux;
										tope = 0 ;
										$("#tdreconocidodetalle"+contador).html(formatearnumero(partereconocidodetalle));
										$("#tdexcendentedetalle"+contador).html(formatearnumero(parteexedentedetalle));
										
										
										$("#tdreconocidodetalle2_"+contador).html(formatearnumero(partereconocidodetalle));
										$("#tdexcendentedetalle2_"+contador).html(formatearnumero(parteexedentedetalle));
										
										
									}
									
								}
								
								parteexcedentetotal = parteexcedentetotal + parteexedentedetalle ;
								partereconocidototal = partereconocidototal + partereconocidodetalle;
							
							}
							else
							{
								//alert("entro3");
								reconocidoaux = $(this).attr('reconocido');
								reconocidoaux = reconocidoaux.replace("$", "");
								reconocidoaux = reconocidoaux.replace(/,/gi, "");
								reconocidoaux = reconocidoaux * 1 ;
								
								excedenteaux  = $(this).attr('excedente');
								excedenteaux  = excedenteaux.replace("$", "");
								excedenteaux  = excedenteaux.replace(/,/gi, "");
								excedenteaux  = excedenteaux * 1;
								
								parteexedentedetalle   =  excedenteaux;
								partereconocidodetalle =  reconocidoaux;
								parteexcedentetotal = parteexcedentetotal + parteexedentedetalle ;
								partereconocidototal = partereconocidototal + partereconocidodetalle
							}
							
						});
					
						//alert("hoola")
						$("#divValorReconocido_"+concepto).html("$"+formatearnumero(partereconocidototal));
						$("#div2ValorReconocido_"+concepto).html("$"+formatearnumero(partereconocidototal));
						$("#divValorExcedente_"+concepto).html("$"+formatearnumero(parteexcedentetotal));
						$("#div2ValorExcedente_"+concepto).html("$"+formatearnumero(parteexcedentetotal));
						$("#divreconocidoTotal_"+concepto).html("$"+formatearnumero(partereconocidototal));
						$("#divexcenteTotal_"+concepto).html("$"+formatearnumero(parteexcedentetotal));
			
						
					}	
				});
		}
		
				var reconocidodetalletotal = 0;
				var valorpresente = 0;
				$('div[id ^=divValorReconocido_]').each(function (){
					  
					valorpresente =$(this).html();
					valorpresente = valorpresente.replace("$", "");
					valorpresente = valorpresente.replace(/,/gi, "");
					//alert(valorpresente);
					reconocidodetalletotal = reconocidodetalletotal*1  +  valorpresente*1;
				});
				
				var excedentedetalletotal = 0;
				var valorpresente = 0;
				$('div[id ^=divValorExcedente_]').each(function (){
					  
					valorpresente =$(this).html();
					valorpresente = valorpresente.replace("$", "");
					valorpresente = valorpresente.replace(/,/gi, "");
					//alert(valorpresente);
					excedentedetalletotal = excedentedetalletotal*1  +  valorpresente*1;
				});
				
				//----------------------
				// miro el valor excedente de la hospitalizacion
				
				if($("#excedenteHospitalizacion").length > 0)
				{
					var varexcedentehospitalizacion = $("#excedenteHospitalizacion").val();
					excedentedetalletotal = excedentedetalletotal*1 + varexcedentehospitalizacion*1;
				}
				//---------------------
				$("#totaltotalreconocido").html("$"+formatearnumero(reconocidodetalletotal*1));
				$("#totaltotalexcedente").html("$"+formatearnumero(excedentedetalletotal*1));
				
				$("#totalcuentareconocido").val(reconocidodetalletotal*1);
				$("#totalcuentaexcedente").val(excedentedetalletotal*1);
				
				$("#divreconocidoresumido").html("$"+formatearnumero(reconocidodetalletotal*1));
				$("#divExcedenteResumido").html("$"+formatearnumero(excedentedetalletotal*1));
				
				$("#divreconocidoresumidod").html("$"+formatearnumero(reconocidodetalletotal*1));
				$("#divExcedenteResumidod").html("$"+formatearnumero(excedentedetalletotal*1));
				
		
	}*/
	
	function calcularvalores()
	{
		//alert("aqui va un valor");
		//calcularNuevoPorcentaje();
		ajustartope();
	}
	
	function calcularNuevoPorcentaje()
	{
		
	
		var porcentaje = $("#porcentaje").val();
		$(".recorrerporcentaje").each(function(){
			
			var concepto = $(this).attr('concepto');
			
			
			
			//-- si envio excedente  se sumaria todo al reconocido
		
			var excedente = $("#ValorECambio_"+concepto).val() ; 
			
			excedente = excedente.replace("$", "");
			excedente = excedente.replace(/,/gi, "");
			//alert(excedente);
			
			var reconocido = $("#ValorRCambio_"+concepto).val() ; 
			reconocido = reconocido.replace("$", "");
			reconocido = reconocido.replace(/,/gi, "");
			//alert(reconocido);
			
			var total = (excedente * 1 ) + (reconocido * 1);
			var partereconocido = (total * ( porcentaje / 100) );
			var parteexedente = total - partereconocido;
			//alert(total);
			
			//excedenteHospitalizacion
			
			
			
			
			if($("#wconceptoestanciaXparametro").val() == concepto)
			{
				//$("#excedenteHospitalizacion").val(parteexedente);
				//$("#divexcedentetotalhospitalizacion").html("$"+formatearnumero(parteexedente));
			}
			else
			{
				$("#divValorReconocido_"+concepto).html("$"+formatearnumero(partereconocido));
				$("#div2ValorReconocido_"+concepto).html("$"+formatearnumero(partereconocido));
				$("#divValorExcedente_"+concepto).html("$"+formatearnumero(parteexedente));
			}
			
			$("#div2ValorExcedente_"+concepto).html("$"+formatearnumero(parteexedente));
			$("#divexcenteTotal_"+concepto).html("$"+formatearnumero(parteexedente));
			$("#divreconocidoTotal_"+concepto).html("$"+formatearnumero(partereconocido));
			
			
			
			var totalexcedente = 0;
			var partereconocidodetalle = 0;
			var parteexedentedetalle = 0;
			$(".valorOcultoReconocidoExcedente_"+concepto).each(function (){
				
				reconocidodetalle = $(this).attr('reconocido');
				reconocidodetalle = reconocidodetalle.replace("$", "");
				reconocidodetalle = reconocidodetalle.replace(/,/gi, "");
				
				excedentedetalle  = $(this).attr('excedente');
				excedentedetalle = excedentedetalle.replace("$", "");
				excedentedetalle = excedentedetalle.replace(/,/gi, "");
				
				
				totaldetalle = (excedentedetalle * 1 ) + (reconocidodetalle * 1);
				partereconocidodetalle = (totaldetalle * ( porcentaje / 100) );
				parteexedentedetalle   = totaldetalle - partereconocidodetalle;
				
				totalexcedente =(totalexcedente * 1) + (totaldetalle * 1 );
				
				contador = $(this).attr('contador');
				$("#tdreconocidodetalle"+contador).html(formatearnumero(partereconocidodetalle));
				$("#tdexcendentedetalle"+contador).html(formatearnumero(parteexedentedetalle));
				
				
				$("#tdreconocidodetalle2_"+contador).html(formatearnumero(partereconocidodetalle));
				$("#tdexcendentedetalle2_"+contador).html(formatearnumero(parteexedentedetalle));
			});
			
			/*
			
			//---------------------------------------------------------
			
			*/
			//---------------------
				var reconocidodetalletotal = 0;
				var valorpresente = 0;
				$('div[id ^=divValorReconocido_]').each(function (){
					  
					valorpresente =$(this).html();
					valorpresente = valorpresente.replace("$", "");
					valorpresente = valorpresente.replace(/,/gi, "");
					//alert(valorpresente);
					reconocidodetalletotal = reconocidodetalletotal*1  +  valorpresente*1;
				});
				
				var excedentedetalletotal = 0;
				var valorpresente = 0;
				$('div[id ^=divValorExcedente_]').each(function (){
					  
					valorpresente =$(this).html();
					valorpresente = valorpresente.replace("$", "");
					valorpresente = valorpresente.replace(/,/gi, "");
					//alert(valorpresente)
					//alert(valorpresente);
					excedentedetalletotal = excedentedetalletotal*1  +  valorpresente*1;
				});
				
				//----------------------
				// miro el valor excedente de la hospitalizacion
				
				if($("#excedenteHospitalizacion").length > 0)
				{
					var varexcedentehospitalizacion = $("#excedenteHospitalizacion").val();
					excedentedetalletotal = excedentedetalletotal*1 + varexcedentehospitalizacion*1;
				}
				//---------------------
				$("#totaltotalreconocido").html("$"+formatearnumero(reconocidodetalletotal*1));
				$("#totaltotalexcedente").html("$"+formatearnumero(excedentedetalletotal*1));
				
				$("#totalcuentareconocido").val(reconocidodetalletotal*1);
				$("#totalcuentaexcedente").val(excedentedetalletotal*1);
				
				$("#divreconocidoresumido").html("$"+formatearnumero(reconocidodetalletotal*1));
				$("#divExcedenteResumido").html("$"+formatearnumero(excedentedetalletotal*1));
				
				$("#divreconocidoresumidod").html("$"+formatearnumero(reconocidodetalletotal*1));
				$("#divExcedenteResumidod").html("$"+formatearnumero(excedentedetalletotal*1));
				
				
				});
		
		
		
		
		
		

		
		
	}
	
	
	
	function enviarReconocido( concepto)
	{
		
		$("#imgreconocido_"+concepto).attr('valor' , 'no');
		$("#imgexcedente_"+concepto).attr('valor' , 'si');
		
		var reconocido = $("#ValorRCambio_"+concepto).val() ; 
		reconocido = reconocido.replace("$", "");
		reconocido = reconocido.replace(/,/gi, "");
	
		
		var excedente = $("#ValorECambio_"+concepto).val() ; 
		excedente = excedente.replace("$", "");
		excedente = excedente.replace(/,/gi, "");
	
		
		var total = (excedente * 1 ) + (reconocido * 1);
		
		
		$("#divValorExcedente_"+concepto).html("$"+formatearnumero(total));
		$("#div2ValorExcedente_"+concepto).html("$"+formatearnumero(total));
		$("#divValorReconocido_"+concepto).html("$"+0);
		$("#div2ValorReconocido_"+concepto).html("$"+0);

		
		if(concepto==$("#wconceptoestanciaXparametro").val())
		{
				$("#excedenteHospitalizacion").val(total);
				$("#divreconocidosubtotal").html(0);
				$("#divexcedentetotalhospitalizacion").html("$"+formatearnumero(total));
				$("#divValorexcedenteestancia").html(formatearnumero(total));
		}
		
		
		
		excedente = (excedente  * 1); 
		reconocido= (reconocido * 1);
		
		var excedentedetalle = 0;
		var reconocidodetalle = 0;
		var totaldetalle = 0;
		var totalreconocido = 0;
		$(".valorOcultoReconocidoExcedente_"+concepto).each(function (){
			
			reconocidodetalle = $(this).attr('reconocido');
			reconocidodetalle = reconocidodetalle.replace("$", "");
			reconocidodetalle = reconocidodetalle.replace(/,/gi, "");
			
			excedentedetalle  = $(this).attr('excedente');
			excedentedetalle = excedentedetalle.replace("$", "");
			excedentedetalle = excedentedetalle.replace(/,/gi, "");
			
			
			totaldetalle = (excedentedetalle * 1 ) + (reconocidodetalle * 1);
			totalreconocido =(totalreconocido * 1) + (totaldetalle * 1 );
			contador = $(this).attr('contador');
			$("#divexcenteTotal_"+concepto).html("$"+formatearnumero(totaldetalle));
			$("#tdexcendentedetalle"+contador).html(formatearnumero(totaldetalle));
			$("#tdreconocidodetalle"+contador).html(0);
			
			$("#tdexcendentedetalle2_"+contador).html(formatearnumero(totaldetalle));
			$("#tdreconocidodetalle2_"+contador).html(0);
		
	
		});
		
		//----------------------------
		var reconocidodetalletotal = 0;
		var valorpresente = 0;
		$('div[id ^=divValorReconocido_]').each(function (){
			  
			valorpresente =$(this).html();
			valorpresente = valorpresente.replace("$", "");
			valorpresente = valorpresente.replace(/,/gi, "");
			
			reconocidodetalletotal = reconocidodetalletotal*1  +  valorpresente*1;
		});
		
		var excedentedetalletotal = 0;
		var valorpresente = 0;
		$('div[id ^=divValorExcedente_]').each(function (){
			  
			valorpresente =$(this).html();
			valorpresente = valorpresente.replace("$", "");
			valorpresente = valorpresente.replace(/,/gi, "");
			
			excedentedetalletotal = excedentedetalletotal*1  +  valorpresente*1;
		});
		//----------------------
		// miro el valor excedente de la hospitalizacion
		
		if($("#excedenteHospitalizacion").length > 0)
		{
			var varexcedentehospitalizacion = $("#excedenteHospitalizacion").val();
			excedentedetalletotal = excedentedetalletotal*1 + varexcedentehospitalizacion*1;
		}
		//---------------------
		
		
		$("#totaltotalreconocido").html("$"+formatearnumero(reconocidodetalletotal*1));
		$("#totaltotalexcedente").html("$"+formatearnumero(excedentedetalletotal*1));
		
		$("#totalcuentareconocido").val(reconocidodetalletotal*1);
		$("#totalcuentaexcedente").val(excedentedetalletotal*1);
		//---------------------------------------------------------------------
		
		$("#divreconocidoresumido").html("$"+formatearnumero(reconocidodetalletotal*1));
		$("#divExcedenteResumido").html("$"+formatearnumero(excedentedetalletotal*1));
		
		$("#divreconocidoresumidod").html("$"+formatearnumero(reconocidodetalletotal*1));
		$("#divExcedenteResumidod").html("$"+formatearnumero(excedentedetalletotal*1));
		
		$("#divexcenteTotal_"+concepto).html("$"+formatearnumero(totalreconocido));
		$("#divreconocidoTotal_"+concepto).html("$"+formatearnumero(0));
	}
	
	
	function enviarExedente(concepto)
	{
		
		$("#imgreconocido_"+concepto).attr('valor' , 'si');
		$("#imgexcedente_"+concepto).attr('valor' , 'no');
		
		//-- si envio excedente  se sumaria todo al reconocido
		
		var excedente = $("#ValorECambio_"+concepto).val() ; 
		
		excedente = excedente.replace("$", "");
		excedente = excedente.replace(/,/gi, "");
		//alert(excedente);
		
		var reconocido = $("#ValorRCambio_"+concepto).val() ; 
		reconocido = reconocido.replace("$", "");
		reconocido = reconocido.replace(/,/gi, "");
		//alert(reconocido);
		
		var total = (excedente * 1 ) + (reconocido * 1);
		//alert(total);
		
		$("#divValorReconocido_"+concepto).html("$"+formatearnumero(total));
		$("#div2ValorReconocido_"+concepto).html("$"+formatearnumero(total));
		$("#divValorExcedente_"+concepto).html("$"+0);
		$("#div2ValorExcedente_"+concepto).html("$"+0);
		
		$("#totaltotalexcedente").html("$"+0);
		if(concepto==$("#wconceptoestanciaXparametro").val())
		{
				$("#excedenteHospitalizacion").val(0);
				$("#divreconocidosubtotal").html("$"+formatearnumero(total));
				$("#divexcedentetotalhospitalizacion").html(0);
				$("#divValorexcedenteestancia").html(formatearnumero(0));
		}
			
		
		
		
		
		var totalexcedente = 0;
		$(".valorOcultoReconocidoExcedente_"+concepto).each(function (){
			
			reconocidodetalle = $(this).attr('reconocido');
			reconocidodetalle = reconocidodetalle.replace("$", "");
			reconocidodetalle = reconocidodetalle.replace(/,/gi, "");
			
			excedentedetalle  = $(this).attr('excedente');
			excedentedetalle = excedentedetalle.replace("$", "");
			excedentedetalle = excedentedetalle.replace(/,/gi, "");
			
			
			totaldetalle = (excedentedetalle * 1 ) + (reconocidodetalle * 1);
			totalexcedente =(totalexcedente * 1) + (totaldetalle * 1 );
			contador = $(this).attr('contador');
			$("#tdreconocidodetalle"+contador).html(formatearnumero(totaldetalle));
			$("#tdexcendentedetalle"+contador).html(0);
			
			
			$("#tdreconocidodetalle2_"+contador).html(formatearnumero(totaldetalle));
			$("#tdexcendentedetalle2_"+contador).html(0);
		});
		
		//---------------------
		var reconocidodetalletotal = 0;
		var valorpresente = 0;
		$('div[id ^=divValorReconocido_]').each(function (){
			  
			valorpresente =$(this).html();
			valorpresente = valorpresente.replace("$", "");
			valorpresente = valorpresente.replace(/,/gi, "");
			//alert(valorpresente);
			reconocidodetalletotal = reconocidodetalletotal*1  +  valorpresente*1;
		});
		
		var excedentedetalletotal = 0;
		var valorpresente = 0;
		$('div[id ^=divValorExcedente_]').each(function (){
			  
			valorpresente =$(this).html();
			valorpresente = valorpresente.replace("$", "");
			valorpresente = valorpresente.replace(/,/gi, "");
			//alert(valorpresente);
			excedentedetalletotal = excedentedetalletotal*1  +  valorpresente*1;
		});
		
		//----------------------
		// miro el valor excedente de la hospitalizacion
		
		if($("#excedenteHospitalizacion").length > 0)
		{
			var varexcedentehospitalizacion = $("#excedenteHospitalizacion").val();
			excedentedetalletotal = excedentedetalletotal*1 + varexcedentehospitalizacion*1;
		}
		//---------------------
		$("#totaltotalreconocido").html("$"+formatearnumero(reconocidodetalletotal*1));
		$("#totaltotalexcedente").html("$"+formatearnumero(excedentedetalletotal*1));
		
		$("#totalcuentareconocido").val(reconocidodetalletotal*1);
		$("#totalcuentaexcedente").val(excedentedetalletotal*1);
		
		$("#divreconocidoresumido").html("$"+formatearnumero(reconocidodetalletotal*1));
		$("#divExcedenteResumido").html("$"+formatearnumero(excedentedetalletotal*1));
		
		$("#divreconocidoresumidod").html("$"+formatearnumero(reconocidodetalletotal*1));
		$("#divExcedenteResumidod").html("$"+formatearnumero(excedentedetalletotal*1));
		//---------------------------------------------------------
		
		$("#divexcenteTotal_"+concepto).html("$"+formatearnumero(0));
		$("#divreconocidoTotal_"+concepto).html("$"+formatearnumero(totalexcedente));
	}
	
	function enviarExedentedetalle(concepto , id)
	{
		//---------------------------------
		var excedente = $("#tdexcendentedetalle"+id).html();
		excedente = excedente.replace("$", "");
		excedente = excedente.replace(/,/gi, "");
		
		var reconocido = $("#tdreconocidodetalle"+id).html() ; 
	
		reconocido = reconocido.replace("$", "");
		reconocido = reconocido.replace(/,/gi, "");
		
		var total = (excedente * 1 ) + (reconocido * 1);
	
		$("#tdreconocidodetalle"+id).html(formatearnumero(total));
		$("#tdexcendentedetalle"+id).html(0);
		
		
		$("#tdexcendentedetalle2_"+id).html(0);
		$("#tdreconocidodetalle2_"+id).html(formatearnumero(total));
		
	
		//----------------
		///----------calcular valores totalesinformato
		var totalexcedente = 0;
		var totalreconocido = 0;
		$(".classtdexcendentedetalle_"+concepto).each(function (){ 
			
			excedentedetalle  = $(this).html();
			excedentedetalle = excedentedetalle.replace("$", "");
			excedentedetalle = excedentedetalle.replace(/,/gi, "");
			totalexcedente = (totalexcedente*1) + (excedentedetalle*1); 
		 
		});
		
		$(".classtdreconocidodetalle_"+concepto).each(function (){ 
			
			reconocidodetalle = $(this).html();
			reconocidodetalle = reconocidodetalle.replace("$", "");
			reconocidodetalle = reconocidodetalle.replace(/,/gi, "");
			totalreconocido = (totalreconocido*1) + (reconocidodetalle*1);
		});
	
		$("#divreconocidoTotal_"+concepto).html("$"+formatearnumero(totalreconocido));
		$("#divexcenteTotal_"+concepto).html("$"+formatearnumero(totalexcedente));
		$("#divValorExcedente_"+concepto).html("$"+formatearnumero(totalexcedente));
		$("#divValorReconocido_"+concepto).html("$"+formatearnumero(totalreconocido));
		
		
		$("#divValorExcedente_"+concepto).html("$"+formatearnumero(totalexcedente));
		$("#div2ValorExcedente_"+concepto).html("$"+formatearnumero(totalexcedente));
		$("#divValorReconocido_"+concepto).html("$"+formatearnumero(totalreconocido));
		$("#div2ValorReconocido_"+concepto).html("$"+formatearnumero(totalreconocido));
		//----------------------------------------------
				
		//--Totales generales		
		//---------------------
		var reconocidodetalletotal = 0;
		var valorpresente = 0;
		$('div[id ^=divValorReconocido_]').each(function (){
			  
			valorpresente =$(this).html();
			valorpresente = valorpresente.replace("$", "");
			valorpresente = valorpresente.replace(/,/gi, "");
			//alert(valorpresente);
			reconocidodetalletotal = reconocidodetalletotal*1  +  valorpresente*1;
		});
		
		var excedentedetalletotal = 0;
		var valorpresente = 0;
		$('div[id ^=divValorExcedente_]').each(function (){
			  
			valorpresente =$(this).html();
			valorpresente = valorpresente.replace("$", "");
			valorpresente = valorpresente.replace(/,/gi, "");
			//alert(valorpresente);
			excedentedetalletotal = excedentedetalletotal*1  +  valorpresente*1;
		});
		
		
		//----------------------
		// miro el valor excedente de la hospitalizacion
		
		if($("#excedenteHospitalizacion").length > 0)
		{
			var varexcedentehospitalizacion = $("#excedenteHospitalizacion").val();
			excedentedetalletotal = excedentedetalletotal*1 + varexcedentehospitalizacion*1;
		}
			//---------------------
		$("#totaltotalreconocido").html("$"+formatearnumero(reconocidodetalletotal*1));
		$("#totaltotalexcedente").html("$"+formatearnumero(excedentedetalletotal*1));
		
		$("#totalcuentareconocido").val(reconocidodetalletotal*1);
		$("#totalcuentaexcedente").val(excedentedetalletotal*1);
		
		$("#divreconocidoresumido").html("$"+formatearnumero(reconocidodetalletotal*1));
		$("#divExcedenteResumido").html("$"+formatearnumero(excedentedetalletotal*1));
		
		$("#divreconocidoresumidod").html("$"+formatearnumero(reconocidodetalletotal*1));
		$("#divExcedenteResumidod").html("$"+formatearnumero(excedentedetalletotal*1));
		//---------------------------------------------------------
		
		//---------------------
		
		
	}
	
	
	function enviarReconocidodetalle(concepto , id)
	{
		//-- poner los valores correspondientes
		var excedente = $("#tdexcendentedetalle"+id).html();
		excedente = excedente.replace("$", "");
		excedente = excedente.replace(/,/gi, "");
		
		var reconocido = $("#tdreconocidodetalle"+id).html() ; 
		reconocido = reconocido.replace("$", "");
		reconocido = reconocido.replace(/,/gi, "");
		
		var total = (excedente * 1 ) + (reconocido * 1);
		$("#tdexcendentedetalle"+id).html(formatearnumero(total));
		$("#tdreconocidodetalle"+id).html(0);
		
		$("#tdexcendentedetalle2_"+id).html(formatearnumero(total));
		$("#tdreconocidodetalle2_"+id).html(0);
		//------------------------------
		
		///----------calcular valores totalesinformato
		var totalexcedente = 0;
		var totalreconocido = 0;
		$(".classtdexcendentedetalle_"+concepto).each(function (){ 
			
			excedentedetalle  = $(this).html();
			excedentedetalle = excedentedetalle.replace("$", "");
			excedentedetalle = excedentedetalle.replace(/,/gi, "");
			totalexcedente = (totalexcedente*1) + (excedentedetalle*1); 
		 
		});
		
		$(".classtdreconocidodetalle_"+concepto).each(function (){ 
			
			reconocidodetalle = $(this).html();
			reconocidodetalle = reconocidodetalle.replace("$", "");
			reconocidodetalle = reconocidodetalle.replace(/,/gi, "");
			totalreconocido = (totalreconocido*1) + (reconocidodetalle*1);
		});
		$("#divValorExcedente_"+concepto).html("$"+formatearnumero(totalexcedente));
		$("#divValorReconocido_"+concepto).html("$"+formatearnumero(totalreconocido));
		$("#divreconocidoTotal_"+concepto).html("$"+formatearnumero(totalreconocido));
		$("#divexcenteTotal_"+concepto).html("$"+formatearnumero(totalexcedente));
		
		
		$("#divValorExcedente_"+concepto).html("$"+formatearnumero(totalexcedente));
		$("#div2ValorExcedente_"+concepto).html("$"+formatearnumero(totalexcedente));
		$("#divValorReconocido_"+concepto).html("$"+formatearnumero(totalreconocido));
		$("#div2ValorReconocido_"+concepto).html("$"+formatearnumero(totalreconocido));
		
		//----------------------------------------------
		
		
		//--Totales generales		
		//---------------------
		var reconocidodetalletotal = 0;
		var valorpresente = 0;
		$('div[id ^=divValorReconocido_]').each(function (){
			  
			valorpresente =$(this).html();
			valorpresente = valorpresente.replace("$", "");
			valorpresente = valorpresente.replace(/,/gi, "");
			//alert(valorpresente);
			reconocidodetalletotal = reconocidodetalletotal*1  +  valorpresente*1;
		});
		
		var excedentedetalletotal = 0;
		var valorpresente = 0;
		$('div[id ^=divValorExcedente_]').each(function (){
			  
			valorpresente =$(this).html();
			valorpresente = valorpresente.replace("$", "");
			valorpresente = valorpresente.replace(/,/gi, "");
			//alert(valorpresente);
			excedentedetalletotal = excedentedetalletotal*1  +  valorpresente*1;
		});
		
		
		//----------------------
		// miro el valor excedente de la hospitalizacion
		
		if($("#excedenteHospitalizacion").length > 0)
		{
			var varexcedentehospitalizacion = $("#excedenteHospitalizacion").val();
			excedentedetalletotal = excedentedetalletotal*1 + varexcedentehospitalizacion*1;
		}
			//---------------------
		$("#totaltotalreconocido").html("$"+formatearnumero(reconocidodetalletotal*1));
		$("#totaltotalexcedente").html("$"+formatearnumero(excedentedetalletotal*1));
		
		$("#totalcuentareconocido").val(reconocidodetalletotal*1);
		$("#totalcuentaexcedente").val(excedentedetalletotal*1);
		
		
		
		$("#divreconocidoresumido").html("$"+formatearnumero(reconocidodetalletotal*1));
		$("#divExcedenteResumido").html("$"+formatearnumero(excedentedetalletotal*1));
		
		
		$("#divreconocidoresumidod").html("$"+formatearnumero(reconocidodetalletotal*1));
		$("#divExcedenteResumidod").html("$"+formatearnumero(excedentedetalletotal*1));
		//---------------------------------------------------------
		
		//---------------------
	
	}
	
	
	function reestablecerValoresdetalle (concepto , id)
	{
		$("#IDvalorOcultoReconocidoExcedente_"+id)
		reconocidodetalle = $("#IDvalorOcultoReconocidoExcedente_"+id).attr('reconocido');
		reconocidodetalle = reconocidodetalle.replace("$", "");
		reconocidodetalle = reconocidodetalle.replace(/,/gi, "");
		
		excedentedetalle  = $("#IDvalorOcultoReconocidoExcedente_"+id).attr('excedente');
		excedentedetalle = excedentedetalle.replace("$", "");
		excedentedetalle = excedentedetalle.replace(/,/gi, "");
		
		reconocidodetalle = reconocidodetalle * 1;
		excedentedetalle = excedentedetalle * 1 ;
		
		/*totalexcedente = (totalexcedente*1) + (excedentedetalle*1); 
		totalreconocido = (totalreconocido*1) + (reconocidodetalle*1); */
	
		$("#tdreconocidodetalle"+id).html(formatearnumero(reconocidodetalle));
		$("#tdexcendentedetalle"+id).html(formatearnumero(excedentedetalle));
		
		$("#tdexcendentedetalle2_"+id).html(formatearnumero(excedentedetalle));
		$("#tdreconocidodetalle2_"+id).html(formatearnumero(reconocidodetalle));
		
		///----------calcular valores totalesinformato
		var totalexcedente = 0;
		var totalreconocido = 0;
		$(".classtdexcendentedetalle_"+concepto).each(function (){ 
			
			excedentedetalle  = $(this).html();
			excedentedetalle = excedentedetalle.replace("$", "");
			excedentedetalle = excedentedetalle.replace(/,/gi, "");
			totalexcedente = (totalexcedente*1) + (excedentedetalle*1); 
		 
		});
		
		$(".classtdreconocidodetalle_"+concepto).each(function (){ 
			
			reconocidodetalle = $(this).html();
			reconocidodetalle = reconocidodetalle.replace("$", "");
			reconocidodetalle = reconocidodetalle.replace(/,/gi, "");
			totalreconocido = (totalreconocido*1) + (reconocidodetalle*1);
		});
	
		$("#divreconocidoTotal_"+concepto).html("$"+formatearnumero(totalreconocido));
		$("#divexcenteTotal_"+concepto).html("$"+formatearnumero(totalexcedente));
		$("#divValorExcedente_"+concepto).html("$"+formatearnumero(totalexcedente));
		$("#divValorReconocido_"+concepto).html("$"+formatearnumero(totalreconocido));
		
		$("#divValorExcedente_"+concepto).html("$"+formatearnumero(totalexcedente));
		$("#div2ValorExcedente_"+concepto).html("$"+formatearnumero(totalexcedente));
		$("#divValorReconocido_"+concepto).html("$"+formatearnumero(totalreconocido));
		$("#div2ValorReconocido_"+concepto).html("$"+formatearnumero(totalreconocido));
		
		//----------------------------------------------
		
		//--Totales generales		
		//---------------------
		var reconocidodetalletotal = 0;
		var valorpresente = 0;
		$('div[id ^=divValorReconocido_]').each(function (){
			  
			valorpresente =$(this).html();
			valorpresente = valorpresente.replace("$", "");
			valorpresente = valorpresente.replace(/,/gi, "");
			//alert(valorpresente);
			reconocidodetalletotal = reconocidodetalletotal*1  +  valorpresente*1;
		});
		
		var excedentedetalletotal = 0;
		var valorpresente = 0;
		$('div[id ^=divValorExcedente_]').each(function (){
			  
			valorpresente =$(this).html();
			valorpresente = valorpresente.replace("$", "");
			valorpresente = valorpresente.replace(/,/gi, "");
			//alert(valorpresente);
			excedentedetalletotal = excedentedetalletotal*1  +  valorpresente*1;
		});
		
		
		//----------------------
		// miro el valor excedente de la hospitalizacion
		
		if($("#excedenteHospitalizacion").length > 0)
		{
			var varexcedentehospitalizacion = $("#excedenteHospitalizacion").val();
			excedentedetalletotal = excedentedetalletotal*1 + varexcedentehospitalizacion*1;
		}
			//---------------------
		$("#totaltotalreconocido").html("$"+formatearnumero(reconocidodetalletotal*1));
		$("#totaltotalexcedente").html("$"+formatearnumero(excedentedetalletotal*1));
		
		$("#totalcuentareconocido").val(reconocidodetalletotal*1);
		$("#totalcuentaexcedente").val(excedentedetalletotal*1);
		
		
		
		$("#divreconocidoresumido").html("$"+formatearnumero(reconocidodetalletotal*1));
		$("#divExcedenteResumido").html("$"+formatearnumero(excedentedetalletotal*1));
		
		
		$("#divreconocidoresumidod").html("$"+formatearnumero(reconocidodetalletotal*1));
		$("#divExcedenteResumidod").html("$"+formatearnumero(excedentedetalletotal*1));
		//---------------------------------------------------------
		
		//---------------------
	}
	
	
	function reestablecerValores (concepto)
	{
		//-- si envio excedente  se sumaria todo al reconocido
		
		var excedente = $("#ValorECambio_"+concepto).val() ; 
		
		excedente = excedente.replace("$", "");
		excedente = excedente.replace(/,/gi, "");
		excedente = excedente * 1;
		//alert(excedente);
		
		var reconocido = $("#ValorRCambio_"+concepto).val() ; 
		reconocido = reconocido.replace("$", "");
		reconocido = reconocido.replace(/,/gi, "");
		reconocido = reconocido * 1;
		
		$("#divValorReconocido_"+concepto).html("$"+formatearnumero(reconocido));
		$("#div2ValorReconocido_"+concepto).html("$"+formatearnumero(reconocido));
		$("#divValorExcedente_"+concepto).html("$"+formatearnumero(excedente));
		$("#div2ValorExcedente_"+concepto).html("$"+formatearnumero(excedente));
		
		
		if(concepto==$("#wconceptoestanciaXparametro").val())
		{
				$("#excedenteHospitalizacion").val(0);
				$("#divreconocidosubtotal").html("$"+formatearnumero(reconocido));
				$("#divexcedentetotalhospitalizacion").html("$"+formatearnumero(excedente));
		}
		
		
		//$("#divValorExcedente_"+concepto).html("$"+0);
		//alert(reconocido);
		var totalexcedente = 0;
		var totalreconocido = 0;
		$(".valorOcultoReconocidoExcedente_"+concepto).each(function (){
			
			reconocidodetalle = $(this).attr('reconocido');
			reconocidodetalle = reconocidodetalle.replace("$", "");
			reconocidodetalle = reconocidodetalle.replace(/,/gi, "");
			
			excedentedetalle  = $(this).attr('excedente');
			excedentedetalle = excedentedetalle.replace("$", "");
			excedentedetalle = excedentedetalle.replace(/,/gi, "");
			
			reconocidodetalle = reconocidodetalle * 1;
			excedentedetalle = excedentedetalle * 1 ;
			
			totalexcedente = (totalexcedente*1) + (excedentedetalle*1); 
			totalreconocido = (totalreconocido*1) + (reconocidodetalle*1); 
			
			contador = $(this).attr('contador');
			$("#tdreconocidodetalle"+contador).html(formatearnumero(reconocidodetalle));
			$("#tdexcendentedetalle"+contador).html(formatearnumero(excedentedetalle));
			
			$("#tdreconocidodetalle2_"+contador).html(formatearnumero(reconocidodetalle));
			$("#tdexcendentedetalle2_"+contador).html(formatearnumero(excedentedetalle));
			
			if(concepto==$("#wconceptoestanciaXparametro").val())
			{
				//alert("ENTRO");
				$("#excedenteHospitalizacion").val(excedentedetalle);
				//alert(excedentedetalle);
			}
			
			
		});
		
		//---------------------
		var reconocidodetalletotal = 0;
		var valorpresente = 0;
		$('div[id ^=divValorReconocido_]').each(function (){
			  
			valorpresente =$(this).html();
			valorpresente = valorpresente.replace("$", "");
			valorpresente = valorpresente.replace(/,/gi, "");
			
			reconocidodetalletotal = reconocidodetalletotal*1  +  valorpresente*1;
		});
		
		var excedentedetalletotal = 0;
		var valorpresente = 0;
		$('div[id ^=divValorExcedente_]').each(function (){
			  
			valorpresente =$(this).html();
			valorpresente = valorpresente.replace("$", "");
			valorpresente = valorpresente.replace(/,/gi, "");
			
			excedentedetalletotal = excedentedetalletotal*1  +  valorpresente*1;
		});
		
		//----------------------
		// miro el valor excedente de la hospitalizacion
		
		if($("#excedenteHospitalizacion").length > 0)
		{
			var varexcedentehospitalizacion = $("#excedenteHospitalizacion").val();
			excedentedetalletotal = excedentedetalletotal*1 + varexcedentehospitalizacion*1;
		}
		//---------------------
		
		$("#totaltotalreconocido").html("$"+formatearnumero(reconocidodetalletotal*1));
		$("#totaltotalexcedente").html("$"+formatearnumero(excedentedetalletotal*1));
		$("#totalcuentareconocido").val(reconocidodetalletotal*1);
		$("#totalcuentaexcedente").val(excedentedetalletotal*1);
		$("#divreconocidoresumido").html("$"+formatearnumero(reconocidodetalletotal*1));
		$("#divExcedenteResumido").html("$"+formatearnumero(excedentedetalletotal*1));	
		$("#divreconocidoresumidod").html("$"+formatearnumero(reconocidodetalletotal*1));
		$("#divExcedenteResumidod").html("$"+formatearnumero(excedentedetalletotal*1));
		//---------------------------------------------------------
		
		
		$("#divreconocidoTotal_"+concepto).html("$"+formatearnumero(totalreconocido));
		$("#divexcenteTotal_"+concepto).html("$"+formatearnumero(totalexcedente));
		
	}
	
	//-----------------------------------------------
	// Funcion habilita el input excedente (excedente es el valor que pagara como excedente el paciente) segun la clave que se envie
	//-----------------------------------------------
	
	
	
	
	//--------------------------------------------------
	// Funcion Generica para cargar y configurar un elemento datepicker 
	//--------------------------------------------------
	function cargar_elementos_datapicker()
	{
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
	}

	
	//-------------------------------------------------
	//-Funcion para limpiar los elementos principales
	//-------------------------------------------------
	function limpiarPantalla()
	{
		$("#whistoria").val('');
		$("#wing").val('');
		$("input[type='radio'][defecto='si']").attr("checked", true);
		$("#informacion_inicial").find("[limpiar=si]").html("");

	}
	function vercajonextra(concepto)
	{
		//
		
		
		if($('#habiliatarpagoextra').is(':checked'))
		{
		  	$(".valorextra").show();
			
			
		}
		else
		{
			$(".valorextra").hide();
			cambiarvalorextra(concepto ,'si');
			$("#extraestancia").val('');
		}
		
		//alert($("#extraestancia").val());
			if($("#extraestancia").val() =='')
			{
			
				//alert("entro");
				$("#subtotalresumido").html("TOTAL CUENTA:");
				$(".valorextra2").hide();
			}
			else
			{
				$("#subtotal").html("SUBTOTAL CUENTA:");
				$("#subtotalresumido").html("SUBTOTAL CUENTA:");
				$(".valorextra2").show();
			}
			
			
		$("#accordionPension").accordion("destroy");
		$("#accordionPension").show();
		$( "#accordionPension").accordion({
			collapsible: true,
			heightStyle: "content"
		});

	}
	
	
	function cambiarvalorexcedente(concepto)
	{
		
		
		total 		= ($("#TotalHospitalizacion").attr("valor"))*1;
		reconocido 	= ($("#idreconocidoestancia").attr("valor"))*1;
		excedente 	= ($("#excedenteHospitalizacion").val())*1;
		
		// alert("total"+total+"reconocido"+reconocido+"excedente"+excedente);
		// alert($('#habiliatarpagoextra').is(':checked') );
		
		
		//total247383reconocido247383excedente50000000
		//alert($("#TotalHospitalizacion").attr("valor")+"---"+reconocido+"---"+excedente);
		
		
		
		/*if($('#habiliatarpagoextra').is(':checked'))
		{
			
			
		}*/
		//else
		{
			if( (excedente*1) > (total*1))
			{
				alert("No puede ingresar un valor mayor al total");
				excedente = 0;
				$("#excedenteHospitalizacion").val(0);
			}
			
		}
		
		//alert(excedente);
		// alert("excedente "+excedente);
		// alert("total "+total);
		
		auxreconocido = $("#divValorReconocido_"+concepto).html();
		auxreconocido = auxreconocido.replace("$", "");
		auxreconocido = auxreconocido.replace(/,/gi, "");
		auxreconocido = auxreconocido * 1;
		
		// si el check de pago extra esta chequeado aumente el valor total de estancia
		/*if($('#habiliatarpagoextra').is(':checked'))
		{
			$("#divValorReconocido_"+concepto).html("$"+formatearnumero(total*1) );
			var valortotalestancia = (total *1) +   (excedente * 1);
			$("#TotalHospitalizacion").html("$"+formatearnumero(valortotalestancia*1));
			$("#resumidovalortotalestancia").html("$"+formatearnumero(valortotalestancia*1));
			$("#div2ValorReconocido_"+concepto).html("$"+formatearnumero(total*1) );
		}*/
		//else
		{
			$("#divValorReconocido_"+concepto).html("$"+formatearnumero(total*1 - excedente*1) );
		}
		var cuantos = $(".tdvalorreconocidoestancia").length;
		var fraccion = (excedente *1) / (cuantos *1) ; 
		var totalreconocidoaux = 0;
		
		$(".tdvalorreconocidoestancia").each(function(){
				var valortotaltd = $(this).attr('valororiginal');
				valortotaltd = valortotaltd.replace("$", "");
				valortotaltd = valortotaltd.replace(/,/gi, "");
				valortotaltd = valortotaltd * 1 - fraccion*1;
				$(this).html("$"+formatearnumero(valortotaltd));
				
			});
		/*$(".tdvalorreconocidoestancia").each(function(){
			
			//alert($(this).attr("valor"));
			var nuevo = ((total*1) - (fraccion*1));
			//$(this).attr("valor" ,nuevo );
				
			$(this).html("$"+formatearnumero(nuevo));
			
			totalreconocidoaux = (totalreconocidoaux*1) + (nuevo*1)
			
			
		});*/
		
		/*if($('#habiliatarpagoextra').is(':checked'))
		{
			$(".tdtotalhospitalizacion").each(function(){
				var valortotaltd = $(this).html('valororiginal');
				valortotaltd = valortotaltd.replace("$", "");
				valortotaltd = valortotaltd.replace(/,/gi, "");
				valortotaltd = valortotaltd * 1;
				
				valortotaltd = fraccion + valortotaltd ;
				$(this).html("$"+formatearnumero(valortotaltd));
				
			});
			$(".tdvalorunitarioestancia").each(function(){
				var valortotaltd = $(this).attr('valororiginal');
				valortotaltd = valortotaltd.replace("$", "");
				valortotaltd = valortotaltd.replace(/,/gi, "");
				valortotaltd = valortotaltd * 1;
				
				valortotaltd = fraccion + valortotaltd ;
				$(this).html("$"+formatearnumero(valortotaltd));
				
			});
			$(".tdvalorreconocidoestancia").each(function(){
				var valortotaltd = $(this).attr('valororiginal');
				valortotaltd = valortotaltd.replace("$", "");
				valortotaltd = valortotaltd.replace(/,/gi, "");
				valortotaltd = valortotaltd * 1;
				$(this).html("$"+formatearnumero(valortotaltd));
				
			});
			
			var valortotalcuenta = $("#totaltotalcuenta").attr('valororiginal');
			valortotalcuenta = valortotalcuenta.replace("$", "");
			valortotalcuenta = valortotalcuenta.replace(/,/gi, "");
			valortotalcuenta = valortotalcuenta * 1;
			valortotalcuenta = (valortotalcuenta*1 + excedente*1);
			valortotalcuenta = valortotalcuenta*1;
			//alert(valortotalcuenta);
			$("#totaltotalcuenta").html("$"+formatearnumero(valortotalcuenta));
			$("#divtotalResumido").html("$"+formatearnumero(valortotalcuenta));
			
		}*/
		//else
		{
			var valortotalcuenta = $("#totaltotalcuenta").attr('valororiginal');
			valortotalcuenta = valortotalcuenta*1;
			$("#totaltotalcuenta").html("$"+formatearnumero(valortotalcuenta));
		}
		
		$("#divreconocidosubtotal").html("$"+formatearnumero(totalreconocidoaux));
		
		
		$(".tdvalorreconocidoestancia2").each(function(){
		//var nuevo = (($(this).attr("valor")*1) - (fraccion*1));	
		$(this).html("$"+formatearnumero((($("#divreconocidosubtotal").attr("valor")*1)-(fraccion * 1)) *1));
			
		})
		//tdexcedenteestancia
		
		var totalexcedenteaux = 0;
		$(".tdexcedenteestancia").each(function(){
			
			
			var nuevo = ((excedente / cuantos));
			
			//$(this).attr("valor" ,nuevo );
			nuevo = nuevo*1;
			$(this).html(formatearnumero(nuevo));
			totalexcedenteaux = (totalexcedenteaux*1) + (nuevo*1);
		})
		
		
		$("#divexcedentetotalhospitalizacion").html(formatearnumero(totalexcedenteaux));
		$("#divValorexcedenteestancia").html(formatearnumero(totalexcedenteaux));
		
		
		/*if($('#habiliatarpagoextra').is(':checked'))
		{
			
		}*/
		//else
		{
			$("#div2ValorReconocido_"+concepto).html("$"+formatearnumero(totalreconocidoaux));
			$(".tdexcedenteestancia2").each(function(){
			
			var nuevo = ((excedente * cuantos));
			$(this).html(formatearnumero(nuevo));
			
			})
		}
		
		
		
		
		
		
		//---------------------
		var reconocidodetalletotal = 0;
		var valorpresente = 0;
		$('div[id ^=divValorReconocido_]').each(function (){
			  
			valorpresente =$(this).html();
			valorpresente = valorpresente.replace("$", "");
			valorpresente = valorpresente.replace(/,/gi, "");
			
			reconocidodetalletotal = reconocidodetalletotal*1  +  valorpresente*1;
		});
		
		var excedentedetalletotal = 0;
		var valorpresente = 0;
		$('div[id ^=divValorExcedente_]').each(function (){
			  
			valorpresente =$(this).html();
			valorpresente = valorpresente.replace("$", "");
			valorpresente = valorpresente.replace(/,/gi, "");
			
			excedentedetalletotal = excedentedetalletotal*1  +  valorpresente*1;
		});
		
		//----------------------
		// miro el valor excedente de la hospitalizacion
		
		if($("#excedenteHospitalizacion").length > 0)
		{
			var varexcedentehospitalizacion = $("#excedenteHospitalizacion").val();
			excedentedetalletotal = excedentedetalletotal*1 + varexcedentehospitalizacion*1;
			//reconocidodetalletotal = reconocidodetalletotal*1 - varexcedentehospitalizacion*1;
		}
		//---------------------
		$("#totaltotalreconocido").html("$"+formatearnumero(reconocidodetalletotal*1));
		$("#totaltotalexcedente").html("$"+formatearnumero(excedentedetalletotal*1));
		$("#totalcuentareconocido").val(reconocidodetalletotal*1);
		$("#totalcuentaexcedente").val(excedentedetalletotal*1);
		$("#divreconocidoresumido").html("$"+formatearnumero(reconocidodetalletotal*1));
		$("#divExcedenteResumido").html("$"+formatearnumero(excedentedetalletotal*1));	
		$("#divreconocidoresumidod").html("$"+formatearnumero(reconocidodetalletotal*1));
		$("#divExcedenteResumidod").html("$"+formatearnumero(excedentedetalletotal*1));
		
		//---------------------------------------------------------
		
		
	}
	
	
	function cambiarvalorextra(concepto,valor)
	{
		if(valor=='si')
		{
			var valorextra = 0;
		}
		else
		{
			var valorextra = $("#extraestancia").val();
		}
		
		
		if($("#extraestancia").val()  =='' || valorextra ==0 )
		{
		
			//alert("entro");
			$("#subtotal").html("TOTAL CUENTA:");
			$("#subtotalresumido").html("TOTAL CUENTA:");
			$(".valorextra2").hide();
		}
		else
		{
			$("#subtotal").html("SUBTOTAL CUENTA:");
			$("#subtotalresumido").html("SUBTOTAL CUENTA:");
			$(".valorextra2").show();
		}
		
		var cuantos = $(".tdvalorreconocidoestancia").length;
		var fraccion = (valorextra*1 / cuantos*1);
		// divido el valor fraccion y lo pongo en los campos de estancia
		$(".valorestra_"+concepto).html(formatearnumero(fraccion));
		// pongo el valor de la estancia general
		$("#totalgeneralestanciaextra").html("$"+formatearnumero(valorextra));
		$("#valorextraresumido").html("$"+formatearnumero(valorextra));
		
		// total general
		//$("#totalgeneralestancia").html(valorextra);
		
		//pongo valor al valor total del detalle de estancia
		var aux = $("#totalgeneralestancia").attr('valororiginal');
		aux = aux.replace("$", "");
		aux = aux.replace(/,/gi, "");
		aux = aux * 1;
		aux = aux*1 + valorextra*1 
		//alert("aux"+aux+" valor extra "+valorextra);
		$("#totalgeneralestancia").html(formatearnumero(aux));
		
		//---------------
		
		//-pongo valor al total de estancia
		
		var aux = $("#TotalHospitalizacion").attr('valor');
		aux = aux.replace("$", "");
		aux = aux.replace(/,/gi, "");
		aux = aux * 1;
		aux = aux*1 + valorextra*1 
		
		
		$("#TotalHospitalizacion").html(formatearnumero(aux));
		//------------------
		
		$(".tdtotalhospitalizacion").each(function(){
				var valortotaltd = $(this).attr('valororiginal');
				valortotaltd = valortotaltd.replace("$", "");
				valortotaltd = valortotaltd.replace(/,/gi, "");
				valortotaltd = valortotaltd * 1;
				
				valortotaltd = fraccion + valortotaltd ;
				$(this).html("$"+formatearnumero(valortotaltd));
				
		});
		
		//---- valor total cuenta
		var valortotal = $("#totaltotalcuenta").attr('valororiginal');
		valortotal = valortotal.replace("$", "");
		valortotal = valortotal.replace(/,/gi, "");
		valortotal = valortotal * 1;
		//valortotal = valortotal*1 + valorextra*1;
		valortotal = valortotal*1 ;
		$("#totaltotalcuenta").html("$"+formatearnumero(valortotal));
		//$("#totalgeneralextra").html("$"+formatearnumero(valorextra));
		
		var excedentedetalletotal = 0;
		var valorpresente = 0;
		$('div[id ^=divValorExcedente_]').each(function (){
			  
			valorpresente =$(this).html();
			valorpresente = valorpresente.replace("$", "");
			valorpresente = valorpresente.replace(/,/gi, "");
			//alert(valorpresente);
			excedentedetalletotal = excedentedetalletotal*1  +  valorpresente*1;
		});
		
		//----------------------
		// miro el valor excedente de la hospitalizacion
		
		if($("#excedenteHospitalizacion").length > 0)
		{
			var varexcedentehospitalizacion = $("#excedenteHospitalizacion").val();
			excedentedetalletotal = excedentedetalletotal*1 + varexcedentehospitalizacion*1;
		}
		
		
		//var auxexc = ;
		var auxexc = excedentedetalletotal * 1;
		
		$("#totaltotalexcedente").html("$"+formatearnumero(auxexc));
		$("#divExcedenteResumido").html("$"+formatearnumero(auxexc));
		
		$("#divvalorextra").html("$"+formatearnumero(valorextra));
		$("#divrextraresumido").html("$"+formatearnumero(valorextra));
		
		//
		//
		$("#divvalorreconocidototal").html("$"+formatearnumero($("#totaltotalreconocido").attr('valor')));
		$("#divreconocidototalresumido").html("$"+formatearnumero($("#totaltotalreconocido").attr('valor')));
		
		$("#divtotalResumido").html("$"+formatearnumero(valortotal));
		
		
		
		var valorextratotal = valortotal*1 + valorextra*1;
		$("#divvalorextratotal").html("$"+formatearnumero(valorextratotal));	
		$("#divtotalresumido").html("$"+formatearnumero(valorextratotal));	
		
		var valorexcedentetotal = excedentedetalletotal * 1 + valorextra*1;
		$("#divvalorexedentetotal").html("$"+formatearnumero(valorexcedentetotal));
		$("#divexcedentetotalresumido").html("$"+formatearnumero(valorexcedentetotal));
		
		
	}
	
	
	function activar_regex_miles(Contenedor)
	{
		//alert("hola");
		// --> cada vez que digiten en el input
		$(".miles").keyup(function(){
			if($(this).val() != "")
			{
				//alert("hollla");
				$(this).val($(this).val().replace(/[^0-9]/g, ""));

				num = $(this).val().replace(/\,/g,'');
				num = num.replace(/\./g,'');
				num = num.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1,');
				num = num.split('').reverse().join('').replace(/^[\,]/,'');
				$(this).val(num);
			}
		});
	}


	

	function resumen_pension(tomotemporal,temporal, fechaparcial)
	{
		
	
		$("#div_EstadoCuentas").html("<div><table height='200'><tr><td><br><b>Cargando...</b><br><img src='../../images/medical/ajax-loader.gif' ></td><tr></table></div>");
		
		$( "#accordionPension").accordion("destroy");
		$("#accordionPension").show();
		$( "#accordionPension").accordion({
			collapsible: true,
			heightStyle: "content"
		});

		

		// var blinkReautorizar;
		

		if($("#wconcepto").val()!='nada')
		{
			
			//alert( $("#div_documento").html())
			$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
			{
				consultaAjax:      '',
				wemp_pmla:         $('#wemp_pmla').val(),
				accion:            'resumen_pension',
				whistoria:		   $("#whistoria_tal").val(),
				wing:			   $("#wing_tal").val(),
				wconcepto:		   $("#wconcepto").val(),
				wtar:			   $("#tarifa_original_tal").val(),
				wempresa:		   $("#responsable_original_tal").val(),
				wtipo_ingreso:	   $("#wtipo_ingreso_tal").val(),
				wtipo_paciente:	   $("#wtip_paciente_tal").val(),
				nejemplo:		   $("#numeroejemplo").val(),
				wcambiodetipos:	   '',
				wfechaparcial:	   fechaparcial  ,
				wcedula:		   $("#div_documento").html()


			},function (data)
			{
				//alert(data);
				$("#div_EstadoCuentas").html('');
				$("#div_EstadoCuentas").html(data);
				//---------cargo buscadores tercero
				
				codIni = '';
				nomIni = '';
					
				var totalr;
				var totale;
				var totalesinformato;
				var totalfinal;
				var totalfinaltotal = 0;
				var cclave;
				var politicaManejoterceros = 0;
				$(".trppal").each(function ()
				{
					totalfinal = 0;
					cclave=$(this).attr("clave");
					
					for(j=1 ; j<=$("#numero_responsables").val(); j++)
					{
						totalr = 0;
						$(".reconocido_r_"+cclave+"_"+j).each(function(){
							totalr = ((totalr * 1)  + ($(this).attr("valor") *1) *1);
						});
						totalfinal = ((totalfinal * 1) + (totalr * 1) * 1);
						$("#input_reconocido_"+cclave+"_"+j).attr("valor" ,totalr );
						totalr = formatearnumero(totalr);
						$("#input_reconocido_"+cclave+"_"+j).val(totalr);


						totale = 0;
						totalesinformato = 0;
						$(".excedente_r_"+cclave).each(function(){
							totale = ((totale * 1)  + ($(this).attr("valor") *1) *1);
						});
						totalesinformato = totale;
						$("#input_excedente_"+cclave).attr("valor" , totale);
						totale = formatearnumero(totale);
						$("#input_excedente_"+cclave).val(totale);

					}
					
					totalfinal = totalfinal + totalesinformato;
					
					totalfinaltotal = (totalfinaltotal * 1 ) + (totalfinal * 1);
					totalfinal = formatearnumero(totalfinal);
					
					
					$("#input_total_"+cclave).html(totalfinal);
					
					
					
					if($("#busc_terceros_usuario_"+cclave+"_0").attr('contercero') == 'si' )
					{
						$("#divbutton_detalle_"+cclave).html("<img width='15' blink='' height='15' src='../../images/medical/root/info.png' style='cursor : pointer' >");
						
						clearInterval(blinkReautorizar);
						blinkReautorizar = setInterval(function(){
						$("[blink]").css('visibility' , $("[blink]").css('visibility') === 'hidden' ? '' : 'hidden');
						}, 400);
						
						//$("#imgrequieretercero"+cclave).("");
						if ( politicaManejoterceros == 0)
						{
							var r= $("#resumendepoliticas tr").length;
							politicaManejoterceros = 1;
							$("#resumendepoliticas tr").after("<tr><td><b>"+(r*1 + 1)+"</b></td><td>Dias de estancia con porcentaje a terceros</td></tr>");
						}
					}
				});
				
				
				totalfinaltotal = formatearnumero(totalfinaltotal);
				$("#grantotal").html("<b>"+totalfinaltotal+"</b>"); 
				//-----------------------------------------
				$("#div_liquidacion_pension").unblock();
				
				
				//--input por si el grabador tiene permiso de grabar parcial
				cargar_elementos_datapicker();
				$("#wfecparcial").datepicker({
					showOn: "button",
					buttonImage: "../../images/medical/root/calendar.gif",
					buttonImageOnly: true,
					maxDate:"+0D",
					onSelect: function(dateText, inst) { 
					var temporal = $("#wcambiodetipos").val();
				
					resumen_pension('no',temporal,dateText)
						
					} 
				});
				
				
			}).done(function (){
				
				$( "#accordionPension").accordion("destroy");
				$("#accordionPension").show();
				$( "#accordionPension").accordion({
					collapsible: true,
					heightStyle: "content"
				});
				
			} );


		}
		else
		{
			$("#div_liquidacion_pension").html('');
			
			$("#div_liquidacion_pension").unblock();
		}
	
		
	}

	function formatearnumero(numero)
	{
		var resultado;
		numero = numero*1;
		resultado = numero.toFixed(2).replace(/./g, function(c, i, a) {
		return i && c !== "." && !((a.length - i) % 3) ? ',' + c : c;
		});
		
		resultado = resultado.replace(".00", "");
		return resultado;

	}

	function crear_variables_compartidas()
	{
		// --> Historia
		if($("#div_campos_compartidos").find("#whistoria_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="whistoria_tal" type="hidden" value="" name="whistoria">');
		// --> Ingreso
		if($("#div_campos_compartidos").find("#wing_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="wing_tal" type="hidden" value="" name="wing">');
		// --> Nombre 1
		if($("#div_campos_compartidos").find("#wno1_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="wno1_tal" type="hidden" value="" name="wno1">');
		// --> Nombre 2
		if($("#div_campos_compartidos").find("#wno2_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="wno2_tal" type="hidden" value="" name="wno2">');
		// --> Apellido 1
		if($("#div_campos_compartidos").find("#wap1_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="wap1_tal" type="hidden" value="" name="wap1">');
		// --> Apellido 2
		if($("#div_campos_compartidos").find("#wap2_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="wap2_tal" type="hidden" value="" name="wap2">');
		// --> Documento
		if($("#div_campos_compartidos").find("#wdoc_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="wdoc_tal" type="hidden" value="" name="wdoc">');
		// --> Tipo Documento
		if($("#div_campos_compartidos").find("#wtip_doc_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="wtip_doc_tal" type="hidden" value="" name="wtip_doc_tal">');
		// --> Nombre de empresa
		if($("#div_campos_compartidos").find("#wnomemp_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="wnomemp_tal" type="hidden" value="" name="wnomemp">');
		// --> Fecha de ingreso
		if($("#div_campos_compartidos").find("#wfecing_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="wfecing_tal" type="hidden" value="" name="wfecing">');
		// --> Servicio de ingreso
		if($("#div_campos_compartidos").find("#wser_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="wser_tal" type="hidden" value="" name="wser">');
		// -->
		if($("#div_campos_compartidos").find("#wpactam_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="wpactam_tal" type="hidden" value="" name="wpactam">');
		// --> Nombre del servicio de ingreso
		if($("#div_campos_compartidos").find("#nomservicio_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="nomservicio_tal" type="hidden" value="" name="nomservicio">');
		// --> Nombre Responsable
		if($("#div_campos_compartidos").find("#div_responsable_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="div_responsable_tal" type="hidden" value="" name="div_responsable">');
		// --> Codigo Responsable
		if($("#div_campos_compartidos").find("#responsable_original_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="responsable_original_tal" type="hidden" value="" name="responsable_original">');
		// --> Nombre Tarifa
		if($("#div_campos_compartidos").find("#div_tarifa_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="div_tarifa_tal" type="hidden" value="" name="div_tarifa">');
		// --> Codigo Tarifa
		if($("#div_campos_compartidos").find("#tarifa_original_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="tarifa_original_tal" type="hidden" value="" name="tarifa_original">');
		// -->
		if($("#div_campos_compartidos").find("#div_documento_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="div_documento_tal" type="hidden" value="" name="div_documento">');
		// --> cco del facturador
		if($("#div_campos_compartidos").find("#wcco_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="wcco_tal" type="hidden" value="" name="wcco">');
		// --> Nombre del cco del facturador
		if($("#div_campos_compartidos").find("#div_servicio_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="div_servicio_tal" type="hidden" value="" name="div_servicio">');
		// --> Tipo de paciente
		if($("#div_campos_compartidos").find("#wtip_paciente_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="wtip_paciente_tal" type="hidden" value="" name="wtip_paciente">');
		// --> Div para pintar cuadro de datos basicos del paciente
		if($("#div_campos_compartidos").find("#div_datos_basicos_tal").length == 0)
			$("#div_campos_compartidos").append('<div id="div_datos_basicos_tal" style="display:none"></div>');
		// --> Usuario administrador
		if($("#div_campos_compartidos").find("#wcajadm_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="wcajadm_tal" type="hidden" value="" name="wcajadm">');
		// --> tipo de ingreso
		if($("#div_campos_compartidos").find("#wtipo_ingreso_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="wtipo_ingreso_tal" type="hidden" value="" name="wtipo_ingreso">');
		// --> Hubicacion del paciente
		if($("#div_campos_compartidos").find("#ccoActualPac_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="ccoActualPac_tal" type="hidden" value="" name="ccoActualPac">');
		// --> Nombre Hubicacion del paciente
		if($("#div_campos_compartidos").find("#nomCcoActualPac_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="nomCcoActualPac_tal" type="hidden" value="" name="nomCcoActualPac">');
		// --> Nombre del tipo de ingreso
		if($("#div_campos_compartidos").find("#wtipo_ingreso_nom_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="wtipo_ingreso_nom_tal" type="hidden" value="" name="wtipo_ingreso_nom">');
		// --> Tipo de empresa
		if($("#div_campos_compartidos").find("#tipoEmpresa_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="tipoEmpresa_tal" type="hidden" value="" name="tipoEmpresa">');
		// --> Nit de empresa
		if($("#div_campos_compartidos").find("#nitEmpresa_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="nitEmpresa_tal" type="hidden" value="" name="nitEmpresa">');
		// --> Si el usuario maneja bodega
		if($("#div_campos_compartidos").find("#wbod_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="wbod_tal" type="hidden" value="" name="wbod">');
		// --> Nombre del usuario
		if($("#div_campos_compartidos").find("#nomCajero_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="nomCajero_tal" type="hidden" value="" name="nomCajero">');
		// --> Si el usuario puede cambiar el responsable del cargo
		if($("#div_campos_compartidos").find("#permiteCambiarResponsable_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="permiteCambiarResponsable_tal" type="hidden" value="" name="permiteCambiarResponsable_tal">');
		// --> Si el usuario puede cambiar de tarifa del cargo
		if($("#div_campos_compartidos").find("#permiteCambiarTarifa_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="permiteCambiarTarifa_tal" type="hidden" value="" name="permiteCambiarTarifa_tal">');
		// --> Si el usuario puede regrabar cargos
		if($("#div_campos_compartidos").find("#permiteRegrabar_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="permiteRegrabar_tal" type="hidden" value="" name="permiteRegrabar_tal">');
		// --> Si el usuario puede seleccionar si el cargo es facturable o no
		if($("#div_campos_compartidos").find("#permiteSeleccionarFacturable_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="permiteSeleccionarFacturable_tal" type="hidden" value="" name="permiteSeleccionarFacturable_tal">');
		// --> Si el usuario puede seleccionar si el cargo es reconocido o excedente
		if($("#div_campos_compartidos").find("#permiteSeleccionarRecExc_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="permiteSeleccionarRecExc_tal" type="hidden" value="" name="permiteSeleccionarRecExc_tal">');
	}

	function obtener_array_permisos()
	{

		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
				{
					consultaAjax:      '',
					wemp_pmla:         $('#wemp_pmla').val(),
					accion:            'obtener_array_permisos'

				},function (data) {
					 ArrayValores  = eval('(' + data + ')');
					$('#permisos').val(ArrayValores);
				});

	}

	//-------------------------------------------
	// Descripcion : Reinicia la pension.
	//				 En el cambio de historia la pension que estaba de la historia anterior se borra para que se pueda ver la estancia.
	//				 de la nueva historia
	//-------------------------------------------
	function reiniciarpension()
	{
		//--Se rinicia el programa
		$("#div_liquidacion_pension").html('');
		$("#wcambiodetipos").val('0');
		$( "#accordionPension" ).accordion("destroy");
		$( "#accordionPension" ).accordion({
			collapsible: true,
			heightStyle: "content"
		});
		$('#wconcepto').val('nada');
		//-------------
	}
	
	//----------------------------
	//	Nombre: cargar_datos
	//	Descripcion: funcion que carga los datos basicos informativos dados una historia y un ingreso
	//	Entradas: elemento - elemento desde donde se hace el llamado a la funcion
	//	Salidas:
	//----------------------------
	function cargar_datos(elemento)
	{
		
		var id = elemento;//variable que almacena el id del elemento de donde se hizo el llamado a la funcion cargar_datos
		// si la historia es vacia  se  inician los datos y no se continua la ejecucion de la funcion
		if($("#whistoria_tal").val()=='' && $("#whistoria").val()=='')
		{
			
			return;
		}
		else
		{
			if($("#whistoria").val() == '')
			{
				$("#whistoria").val($("#whistoria_tal").val());
				$("#wing").val($("#wing_tal").val());
				
			}
			
		}

		// --> se hace una llamada ajax cargar_datos
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:      		'',
			wemp_pmla:         		$('#wemp_pmla').val(),
			accion:            		'cargar_datos',
			whistoria:		   		$('#whistoria').val(),
			wing:					$('#wing').val(),
			wcargos_sin_facturar:	$("#cargos_sin_facturar").val(),
			welemento:				id

		},function(data){

			//data.prueba indica si la historia existe
			if(data.prueba =='no')
			{
				alert('La historia no existe');
				$('#whistoria').val('');
				$('#wing').val('');
			
			}
			else
			{
				
				if(data.error ==1)
				{
					alert(data.mensaje);
					$('#whistoria').val('');
					$('#wing').val('');

				}
				else
				{

					// --> datos traidos desde la funcion
					$("#whistoria_tal").val($('#whistoria').val());

					$("#wing").val(data.wwing);
					$("#wing_tal").val(data.wwing);

					$("#wno1").val(data.wno1);
					$("#wno1_tal").val(data.wno1);

					$("#wno2").val(data.wno2);
					$("#wno2_tal").val(data.wno2);

					$("#wap1").val(data.wap1);
					$("#wap1_tal").val(data.wap1);

					$("#wap2").val(data.wap2);
					$("#wap2_tal").val(data.wap2);

					$("#wdoc").val(data.wdoc);
					$("#wdoc_tal").val(data.wdoc);
					
					// --> Documento
					$("#wtip_doc").val(data.wtip_doc);
					$("#wtip_doc_tal").val(data.wtip_doc);

					$("#wnomemp").val(data.wnomemp);
					$("#wnomemp_tal").val(data.wnomemp);

					$("#wfecing").html(data.wfecing);
					$("#wfecing_tal").val(data.wfecing);

					$("#wser").val(data.wser);
					$("#wser_tal").val(data.wser);

					// --> Ubicacion actual del paciente
					$("#divCcoActualPac").html(data.ccoActualPac+"-"+data.nomCcoActualPac);
					$("#ccoActualPac").val(data.ccoActualPac);
					$("#nomCcoActualPac").val(data.nomCcoActualPac);
					$("#ccoActualPac_tal").val(data.ccoActualPac);
					$("#nomCcoActualPac_tal").val(data.nomCcoActualPac);

					$("#wpactam").val(data.wpactam);
					$("#wpactam_tal").val(data.wpactam);

					$("#nomservicio").html(data.wnombreservicio);
					$("#nomservicio_tal").html(data.wnombreservicio);

					$("#div_tipo_servicio").html(data.wnombreservicio);

					$("#div_responsable").html(data.responsable);
					$("#div_responsable_tal").val(data.responsable);

					$("#responsable_original").val(data.wcodemp);
					$("#responsable_original_tal").val(data.wcodemp);

					$("#td_responsable").html(data.responsable);

					$("#hidden_responsable").val(data.wcodemp);

					$("#div_tarifa").html(data.tarifa);
					$("#div_tarifa_tal").val(data.tarifa);

					$("#tarifa_original").val(data.wtar);
					$("#tarifa_original_tal").val(data.wtar);

					$("#td_tarifa").html(data.tarifa);
					$("#hidden_tarifa").val(data.wtar);
					$("#div_paciente").html(data.paciente);

					// --> Pintar los otros responsables del paciente
					$("#tableResponsables").html('');
					$("#tableResponsables").append(data.otrosResponsables).show();

					$("#div_documento").html(data.wdoc);
					$("#div_documento_tal").val(data.wdoc);

					//$("#div_servicio").html($("#wcco").val()+'-'+nomcco);
					//$("#div_servicio_tal").val(nomcco);

					$("#wtip_paciente").val(data.wtip_paciente);
					$("#wtip_paciente_tal").val(data.wtip_paciente);

					$("#wtipo_ingreso").val(data.tipo_ingreso);
					$("#wtipo_ingreso_tal").val(data.tipo_ingreso);
					$("#wtipo_ingreso_nom_tal").val(data.nombre_tipo_ingreso);

					$("#div_tipo_ingreso").html(data.nombre_tipo_ingreso);

					// --> Tipo de empresa
					$("#tipoEmpresa").val(data.tipoEmpresa);
					$("#tipoEmpresa_tal").val(data.tipoEmpresa);

					// --> Nit de empresa
					$("#nitEmpresa").val(data.nitEmpresa);
					$("#nitEmpresa_tal").val(data.nitEmpresa);

					$("#linkAbrirHce").show();
					$( "#accordionDatosPaciente" ).accordion("destroy");
					$( "#accordionDatosPaciente" ).accordion({
						collapsible: true,
						heightStyle: "fill"
					});
					
	
					
					
				
				}
			}
		},
		'json').done(function (){
			// selectconceptospension();
			resumen_pension('no','', 'no');
		});
	}
	
	
	function imprimirSoporte(historia, ingreso)
	{
		
		jConfirm("Desea imprimir el Estado de cuenta?", 'Confirmar', function(respuesta) {
			if(respuesta)
			{
				$.blockUI({
					message: "<div style='background-color: #111111;color:#ffffff;font-size: 15pt;'><img width='19' heigth='19' src='../../images/medical/ajax-loader3.gif'>&nbsp;&nbsp;Consultando...</div>",
					css:{"border": "2pt solid #7F7F7F"}
				});
				if($("#estadoCuenta").val()==1)
				  var contenido = $("#EstCuentaResumido").html();
				else	
				  var contenido = $("#EstCuentadetallado").html();
				
				//alert($("#EstCuentaDetallado").html());
				//return;
				$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
				{
					consultaAjax:      	'',
					wemp_pmla:         	$('#wemp_pmla').val(),
					accion:            	'imprimirSoporte',
					historia:			historia,
					ingreso:			ingreso,
					wcontenido:         contenido
				}, function(respuesta){
					$.unblockUI();
					if(respuesta.Error)
						jAlert("<span style='color:red'>"+respuesta.Mensaje+"</span>", "Mensaje");
					else
					{	
						var contenido	= respuesta.Html;
						// --> Abrir modal 
						$("#imprimirSoporte").html(contenido).show().dialog({
							dialogClass: 'fixed-dialog',
							modal: true,
							title: "<div align='center' style='font-size:10pt'>Imprimir Soporte</div>",
							width: "auto",
							height: "700",
							buttons: {
								Cerrar: function() {
									$("#imprimirSoporte").html("").hide();
									$( this ).dialog( "close" );
									$( this ).dialog( "destroy" );
							}
						  }
						});
					}	
				}, 'json');
			}
		});		
	}
	
	function detalladoporconcepto(concepto)
	{
		
		if( $("#detalleCargoppal tr.procedimiento_"+concepto).is(":visible") ){
		$("#detalleCargoppal tr.procedimiento_"+concepto).hide();
		}
		else
		{
				$("#detalleCargoppal tr.procedimiento_"+concepto).show();
		}
		//$(".procedimiento_"+concepto)
		//procedimiento_0626
	}
	
	function detallado()
	{
		$(".procedimiento").show();
		$("#titulo").html("Estado de cuenta Detallado");
		$("#estadoCuenta").val(2);
		$(".procedimientoEncabezado").each(function (){
		
			
				$(this).addClass('encabezadoTabla');
			
		});
		
		$( "#accordionPension" ).accordion("destroy");
		$( "#accordionPension" ).accordion({
			collapsible: true,
			heightStyle: "fill"
		});
	}
	
	function resumido()
	{
		$(".procedimiento").hide();
		$("#titulo").html("Estado de cuenta Resumido");
		$("#estadoCuenta").val(1);
		$(".procedimientoEncabezado").each(function (){
			
				$(this).removeClass('encabezadoTabla');
			
			
		});
		
		$( "#accordionPension" ).accordion("destroy");
		$( "#accordionPension" ).accordion({
			collapsible: true,
			heightStyle: "fill"
		});
	}
	
	
	//------------------------------------------------------------------------
	//Funcion que oculta los detalles inactivos y que solo muestra los activos
	//-------------------------------------------------------------------------

	
	function abrirDetalle()
	{
		$(".procedimiento").toggle();
		
		
		$(".procedimientoEncabezado").each(function (){
			if ($(this).hasClass( "encabezadoTabla" ))
			{
				$(this).removeClass('encabezadoTabla');
			}
			else
				$(this).addClass('encabezadoTabla');
			
		});
		
	}

//=======================================================================================================================================================
//	F I N  F U N C I O N E S  J A V A S C R I P T
//=======================================================================================================================================================
	</script>

<!--=====================================================================================================================================================================
	E S T I L O S
=====================================================================================================================================================================-->
	<style type="text/css">
		.ui-autocomplete{
			max-width:         230px;
			max-height: 150px;
			overflow-y: auto;
			overflow-x: hidden;
			font-size:         9pt;
        }
		#tooltip{color: #2A5DB0;font-family: Arial,Helvetica,sans-serif;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:2px;opacity:1;}
		#tooltip h7, #tooltip div{margin:0; width:auto}
		.Titulo_azul{
			color:#3399ff;
			font-weight: bold;
			font-family: verdana;
			font-size: 10pt;
		}
		.BordeGris{
			border: 1px solid #999999;
		}
		.BordeNaranja{
			border: 1px solid orange;
		}
		/*.campoRequerido{
			border: 1px outset #3399ff ;
			background-color:lightyellow;
			color:gray;
		}*/
		
		.campoObligatorio{
			border-style:solid;
			border-color:red;
			border-width:1px;
		}
		.pad{
                       padding: 3px;
            }
		.trrequerido-iab {
				border: red 2px solid;
				border-width: 2px 0 2px 2px;
		}
		.trrequerido-dab {
				border: red 2px solid;
				border-width: 2px 2px  2px 0 ;
		}
		.trrequerido-ab {
				border: red 2px solid;
				border-width: 2px 0 2px 0;
		}
		.trrequerido-a {
				border: red 2px solid;
				border-width: 2px 0 0 0;
		}
		.trrequerido-b {
				border: red 2px solid;
				border-width: 0 0 2px 0;
		}
		
		
		.block { display: inline-block; }

	
	</style>
<!--=====================================================================================================================================================================
	F I N   E S T I L O S
=====================================================================================================================================================================-->

<!--=====================================================================================================================================================================
	I N I C I O   B O D Y
=====================================================================================================================================================================-->
	<BODY>
	<?php
	// -->	ENCABEZADO
	echo"<input type='hidden' id='permisos' value=''>";
	echo"<input type='hidden' id='array_terceros' value=''>";
	echo"<input type='hidden' id='array_terceros_especialidad' value=''>";
	echo "<input type='hidden' id='wemp_pmla' name='wemp_pmla' value='".$wemp_pmla."'>";
	//-->Datos ocultos propios del programa de cargos
	echo "<input type='hidden' id='wno1' name='wno1' >";
	echo "<input type='hidden' id='wno2' name='wno2' >";
	echo "<input type='hidden' id='wap1' name='wap1' >";
	echo "<input type='hidden' id='wap2' name='wap2' >";
	echo "<input type='hidden' id='wdoc' name='wdoc' >";
	echo "<input type='hidden' id='wtip_doc' name='wtip_doc' >";
	echo "<input type='hidden' id='wser' name='wser' >";
	echo "<input type='hidden' id='warctar' name='warctar' >";
	echo "<input type='hidden' id='wconmva' name='wconmva' >";
	echo "<input type='hidden' id='wtip_paciente' name='wtip_paciente' >";
	echo "<input type='hidden' id='wtipo_ingreso' name='wtipo_ingreso' >";
	echo "<input type='hidden' id='wcaja' name='wcaja' >";
	echo "<input type='hidden' id='nomCajero' name='nomCajero' >";
	echo "<input type='hidden' id='wcajadm' name='wcajadm' >";
	echo "<input type='hidden' id='permiteCambiarResponsable' 		name='permiteCambiarResponsable' >";
	echo "<input type='hidden' id='permiteCambiarTarifa' 			name='permiteCambiarTarifa' >";
	echo "<input type='hidden' id='permiteRegrabar' 				name='permiteRegrabar' >";
	echo "<input type='hidden' id='permiteSeleccionarFacturable' 	name='permiteSeleccionarFacturable' >";
	echo "<input type='hidden' id='permiteSeleccionarRecExc' 		name='permiteSeleccionarRecExc' >";
	echo "<input type='hidden' id='wnomcco' name='wnomcco' >";
	echo "<input type='hidden' id='wcco' name='wcco' >";
	echo "<input type='hidden' id='cargos_sin_facturar' name='cargos_sin_facturar' >";
	echo "<input type='hidden' id='wdevol' name='wdevol' >";
	echo "<input type='hidden' id='wcodpaq' name='wcodpaq' value='' >";
	echo "<input type='hidden' id='wconmvto' name='wconmvto' value='' >";
	echo "<input type='hidden' id='wbod' name='wbod' value='off' >";
	echo "<input type='hidden' id='wexidev' name='wexidev' value='' >";
	echo "<input type='hidden' id='num_paquete' name='num_paquete' value='1' >";
	echo "<input type='hidden' id='cuentaCongelada' name='cuentaCongelada' value='' >";
	echo "<input type='hidden' id='ccoActualPac' name='ccoActualPac' value='' >";
	echo "<input type='hidden' id='nomCcoActualPac' name='ccoActualPac' value='' >";
	$wconceptoestancia = consultarAliasPorAplicacion($conex, $wemp_pmla, 'concepto_estancia');
	echo "<input type='hidden' id='wconceptoestanciaXparametro' name='wconceptoestanciaXparametro' value='".$wconceptoestancia."' >";
	echo "
	<input type='hidden' id='wnomemp' name='wnomemp'>
	<input type='hidden' id='hidden_responsable'>
	<input type='hidden' id='responsable_original'>
	<input type='hidden' id='hidden_tarifa'>
	<input type='hidden' id='tarifa_original'>
	<input type='hidden' id='tipoEmpresa'>
	<input type='hidden' id='nitEmpresa'>";
	$wbasedato_movhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	echo "<input type='hidden' id='wbasedato_movhos' value='".$wbasedato_movhos."' >";

	echo"
	<div align='center'>
		<div width='95%' id='accordionDatosPaciente' style='display:none'>
			<h3>DATOS DEL PACIENTE</h3>
			<div height='10' class='pad' align='center' id='DatosPaciente' style='display : none'>";

				echo"
					<br>
					<table width='90%' style='border: 1px solid #999999;background-color: #ffffff;'>";
					echo'	<tr>
							<td colspan="7" align="left" id="linkAbrirHce" style="display:none">
								<span onmouseover="$(this).css({\'color\': \'#2A5DB0\'})" onmouseout="$(this).css({\'color\': \'#000000\'})" style="font-size:8pt;font-weight: normal;cursor:pointer;" onclick="abrirHce()";>
									Ver Historia Cl&iacute;nica
								</span>
								<b>&nbsp;|&nbsp;</b>
								<span onmouseover="$(this).css({\'color\': \'#2A5DB0\'})" onmouseout="$(this).css({\'color\': \'#000000\'})" style="font-size:8pt;font-weight: normal;cursor:pointer;" onclick="abrirOrdenes()";>
									Ver Ordenes M&eacute;dicas
								</span>
							</td>
						</tr>';
						echo'<tr>
									<td align=center colspan="7" class="encabezadoTabla"><b>D A T O S &nbsp&nbspD E L &nbsp&nbspP A C I E N T E</b></td>
											</tr>
											<tr class="fila1" style="font-weight: bold;">
												<td align="left" width="11%">
													<b>Historia:</b>
												</td>
												<td align="left" width="15%">
													<b>Ingreso Nro:</b>
												</td>
												<td align="left" colspan="2">
													<b>Paciente:</b>
												</td>
												<td align="left">
													<b>Documento:</b>
												</td>
												<td align="left">
													<b>Fecha Ingreso:</b>
												</td>
												<td align="left">
													<b>Fecha del cargo:</b>
												</td>
											</tr>
											<tr class="fila2">
												<td align="left">
													<input type="text" id="whistoria" size="15"  value="" onchange="cargar_datos(\'whistoria\');" >
												</td>
												<td align="left">
													<input type="text" id="wing" value="" size="3" onchange="cargar_datos(\'wing\')" >
												</td>
												<td align="left" colspan="2" id="div_paciente" limpiar="si">
												</td>
												<td align="left" id="div_documento" limpiar="si">
												</td>
												<td align="left" id="wfecing" limpiar="si">
												</td>
												<td align="left" >
													<input type="text" id="wfeccar" name="wfeccar" value="" size="10">
												</td>
											</tr>
											<tr class="fila1" style="font-weight: bold;">
												<td align="left">
													<b>Servicio de Ing:</b>
												</td>
												<td align="left" width="12%">
													<b>Tipo de Ingreso:</b>
												</td>
												<td align="left">
													<b>Ubicación:</b>
												</td>
												<td align="left">
													<b>Servicio de facturación:</b>
												</td>
												<td align="center" colspan="3">
													<b>Responsables:</b>
												</td>
											</tr>
											<tr class="fila2">
												<td align="left" id="div_tipo_servicio" limpiar="si">
												</td>
												<td align="left" id="div_tipo_ingreso" limpiar="si">
												</td>
												<td align="left" id="divCcoActualPac" limpiar="si">
												</td>
												<td align="left" id="div_servicio">
												</td>
												<td align="left" colspan="3" style="font-size:8pt;" >
													<table width="100%" id="tableResponsables" style="background-color: #ffffff;display:none" limpiar="si">
													</table>
													<div id="div_responsable" 	style="display:none"></div>
													<div id="div_tarifa"		style="display:none"></div>
												</td>
											</tr>';

				echo"	</table><br>

				</div>
		</div>
		<div width='95%' id='accordionPension' style='display : none'>
			<h3>Estado de cuenta</h3>
			<div id='div_EstadoCuentas'></div>";
	echo	"<div id='detalle_liquidacion_general' style='display : none'>
			";
	echo"	</div>
		</div>
		<div width='95%' align='right'><br>

			<div id='imprimirSoporte' style='display:none;align='center'>
			</div>
	
		</div>
	</div>";
	?>
	</BODY>
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