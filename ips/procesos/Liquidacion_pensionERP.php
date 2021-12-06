<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA
//=========================================================================================================================================\\
//DESCRIPCION:
//AUTOR:				Felipe Alvarez
//FECHA DE CREACION:
//FUNCIONAMIENTO:  consulta principal que trae la estancia de un paciente. movimientos en la  tabla 17 con el campo Eyrtipo ='Recibo'
//--------------------------------------------------------------------------------------------------------------------------------------------
// ACTUALIZACIONES
// --> 2020-03-16: 	Jerson Trujillo, cambian todas la variables ejem datoauxfinal_clave por datoauxfinal_clave2
//					ya que generaba un error js que decia que las variables no existian
//2020-01-22, Jerson: Mostrar mensaje de ventana de mantenimiento dependiendo de variables en la root_51
// 		2019-07-18, Jerson Trujillo: Validar que no hayan habitaciones sin seleccion de tipo de habitacion

//--- 20 de octubre 2016  Se adiciona la posibilidad de editar el campo excedente 
//--- 20 de Febrero 2018  Se adiciona politica para cambiar cargos ya grabados  segun la habitacion  que se le cobra al paciente Ejemplo:
//    pacientes de eps  que tengan  como politica que una nebulizacion no se cobra en habitacion UCI o UCE   al momento de grabar la estancia,
//    mira si hay cargos como este cobrados en habitaciones de UCI o UCE y los cambia a no facturables, El programa Evaluara los cargos y hara elemento
//    cambio leyendo politicas, adicional a esto estos cargos quedaran marcados pues si hay una devolucion quedaran en el estado inicial (si antes estaban facturables
//    cuando se pase a no facturables por la politica y luego se anulen quedaran denuevo en no facturables )
//
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
$wactualiz='2020-03-16';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
//
//
//--------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------------------------------------------------
// 	EJECUCION DEL SCRIPT
//--------------------------------------------------------------------------------------------------------------------------------------------
/** DESCOMENTAR REVISAR CON EDWIN */
//if(!isset($_SESSION['user']))
	
/* 
 * Iniciativa Cargos Automaticos (Estancia)	
 * Cristhian Barros
 * 28 Abr 2021
	
	Modificamos la validacion para que solo valide el usuario cuando no se realiace la peticion POST 
	Por medio del crontab, es decir, cuando la variable $_POST['crontab'] no este definida.
*/

$validar_usuario = !isset($_SESSION['user']) && !isset($_POST['crontab']) ? true : false;
if($validar_usuario)
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
	
	
	$hay_unix 		= consultarAplicacion($conex,$wemp_pmla,"conexionUnix");
	$graba_unix 	= consultarAplicacion($conex,$wemp_pmla,"grabarUnix");
	if($hay_unix == "off" && $graba_unix == "on" ){
		echo '<br/><br/><br/><br/>
			<div style="color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;" >
				[?] PROGRAMA NO DISPONIBLE...<br />Nos encontramos en una ventana de mantenimiento, por favor intente ingresar mas tarde, Disculpas por las molestias.
			</div>';
		return;
	}

//=====================================================================================================================================================================
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================

	//-----------
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
	
	//-------------------------------------------------------------------------------------------------------
	//	Funcion que obtiene la informacion relacionada al grabador (Usuario) asi como sus dierentes permisos
	//-------------------------------------------------------------------------------------------------------
	function cargar_datos_caja()
	{
		global $conex;
		global $wemp_pmla;
		global $wbasedato;
		global $wuse;

		$data = array();
		$q =  " SELECT Cjecco, Cjecaj, Cjetin, cjetem, cjeadm, cjebod, Descripcion, Cjecrc, Cjectc, Cjeprc, Cjesfc, Cjesre
				  FROM ".$wbasedato."_000030, usuarios
				 WHERE Cjeusu = '".$wuse."'
				   AND Cjeest = 'on'
				   AND Cjeusu = Codigo";

			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num = mysql_num_rows($res);

		if ($row = mysql_fetch_array($res))
		{
			$pos 									= strpos($row['Cjecco'],"-");
			$data['wcco']    						= substr($row['Cjecco'],0,$pos);
			$data['wnomcco'] 						= substr($row['Cjecco'],$pos+1,strlen($row['Cjecco']));
			$data['wbod'] 	 						= $row['cjebod'];
			$pos 									= strpos($row['Cjecaj'],"-");
			$data['wcaja']   						= substr($row['Cjecaj'],0,$pos);
			$data['wnomcaj'] 						= substr($row['Cjecaj'],$pos+1,strlen($row['Cjecaj']));
			$data['wcajadm'] 						= $row['cjeadm'];
			$data['wtiping'] 						= $row['Cjetin'];
			$data['nomCajero'] 						= $row['Descripcion'];
			$data['cambiarResponsable'] 			= $row['Cjecrc'];
			$data['cambiarTarifa'] 					= $row['Cjectc'];
			$data['permiteRegrabar'] 				= $row['Cjeprc'];
			$data['permiteSeleccionarFacturable'] 	= $row['Cjesfc'];
			$data['permiteSeleccionarRecExc'] 		= $row['Cjesre'];
			$data['wtipcli'] 						= $row['cjetem'];
		}

		return $data;
	}


	function traer_detalle_estancia($whistoria, $wing, $datos_ingreso, $datos_egreso ,$vec_politicas,$wconcepto,$wtarifa,$wempresa,$wcambiar_valor,$wcambiar_dias,$wtipo_ingreso,$nejemplo,$wcambiodetipos,$wtipo_paciente,$fechaingreso_liquidacion_parcial,$horaingreso_liquidacion_parcial)
	{

		global $conex;
		global $wemp_pmla;
		global $wbasedato;
		global $wuse;
		$array_valores = array();
		
		//-- establece los permisos de modificar valor y cantidad de dias , en cada detalle de pension

		if(trim($wcambiar_valor) =='on')
			$wcambiar_valor ='enabled=true';
		else
			$wcambiar_valor ='disabled=true';

		if(trim($wcambiar_dias) =='on')
			$wcambiar_dias ='enabled=true';
		else
			$wcambiar_dias ='disabled=true';
		//-----------------------------------

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
		
		
		// $guardar = "Vector prueba".print_r($vector_pension,true).PHP_EOL;
		// seguimiento($guardar, true );
		
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
			
					$nuevovalor = datos_desde_procedimiento($vec_pension[$clave]['hab_tipo'], $wconcepto	, $vec_pension[$clave]['cod_cco_ing']	, $vec_pension[$clave]['cod_cco_ing'] 	,$wempresa	,$vec_pension[$clave]['fec_ing_original']								,$wtipo_ingreso, '*', 'on', false,'', $vec_pension[$clave]['fec_ing_original']	, date("H:i:s"));
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

		//---------------------------------------------------------------------
		// $guardar = "Vector prueba 2".print_r($vec_pension,true).PHP_EOL;
		// seguimiento($guardar, false );

		$vec_pension_final = array(); // array que sera utilizado oficialmente para calcular los dias y para
									  // ser mostrado en pantalla en una tabla
		
		// $guardar = "inicio de calcular dias ".print_r($vec_politicas,true).PHP_EOL;
		// seguimiento($guardar, false );
		
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
		// $guardar = "Vector final".print_r($vec_pension_final,true).PHP_EOL;
		// seguimiento($guardar, false );		
		
		// $guardar = "Vector responsables".print_r($array_vector_responsables_paf,true).PHP_EOL;
		// seguimiento($guardar, false );
		//------
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
					   AND Resdes != 'on'
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
						   AND Resdes != 'on'
						   AND Resnit = Empcod
						   AND Empest = 'on'
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
			/* SE AGREGAN ESTAS VARIABLES FALTANTES 2021-11-23 CIDENET SAS*/
			
			$arr_var['whistoria']			=$whistoria;
			$arr_var['wing']				=$wing;
			$arr_var['wfeccar']				=date('Y-m-d');
			
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
				/* SE AGREGAN ESTAS VARIABLES FALTANTES 2021-11-23 CIDENET SAS*/
				
				$arr_var['whistoria']			=$whistoria;
				$arr_var['wing']				=$wing;
				$arr_var['wfeccar']				=date('Y-m-d');
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
						<input  '.$wcambiar_dias.' id="input_dias_'.$clave.'"  style="text-align: center"  type="text" value="'.$vec_pension_final[$clave]['dias_cobro'].'"  size="3" onchange="recalcular_estancia('.$clave.')">
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
					$htmloption	= $vec_pension_final[$clave]['faltantes']."<select   class='habitacion'  diainicial='".$vec_pension_final[$clave]['dia_empieza_cobro']."'  diafinal='".$vec_pension_final[$clave]['dia_fin_cobro']."' id='tipo_hab_facturacion_".$clave."_".$u."' style='font-size:8pt; width:120pt'  onchange='cambiar_tipo_habitacion_facturacion(\"".$clave."\",\"".$u."\")'  >";
				}
				else
				{
					$htmloption	="<select  class='habitacion' diainicial='".$vec_pension_final[$clave]['dia_empieza_cobro']."' diafinal='".$vec_pension_final[$clave]['dia_fin_cobro']."'  id='tipo_hab_facturacion_".$clave."_".$u."'  style='font-size:8pt; width:120pt'   onchange='cambiar_tipo_habitacion_facturacion(\"".$clave."\",\"".$u."\")'  >";

				}
				$x=0;//-- Contador que sirve para controlar la primera vez que recorre el ciclo y no trae habitacion en el vector principal.
				while($row_tip_hab = mysql_fetch_array($res_tip_hab))
				{
					// cuando no hay cambio de tipo de habitacion seleccionado por el usuario grabador
					if(!isset($tiposcambiados[$clave][$u]))
					{
						// si el tipo unico viene vacio y es la primera iteracion del ciclo
						if ($tipo_unico =='' && $x==0)
						{
							$htmloption   .="<option   selected value='' >NO HAY TIPO HABITACION ".$x."</option>";
						}
						if($row_tip_hab['Procod'] == $tipo_unico)
							$htmloption   .="<option   selected value='".$row_tip_hab['Procod']."' >".$row_tip_hab['Procod']."-".$row_tip_hab['Pronom']." --".$x."</option>";
						else
							$htmloption   .="<option   value='".$row_tip_hab['Procod']."' >".$row_tip_hab['Procod']."-".$row_tip_hab['Pronom']."--".$x."</option>";
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
				$html.='<td class="'.$wcf.'" rowspan="2" id="td_excedente_'.$clave.'"><input type="text" id="input_excedente_'.$clave.'" style="text-align: right" size="9" disabled="true" value="0" class="entero"><input type="checkbox" id="checkbox_excedente_'.$clave.'" title="Habilitar el campo excedente" onclick="habilitar_excedente('.$clave.')"></td>';
			}
			else
			{
				$html.='<td class="'.$wcf.'" rowspan="2" id="td_excedente_'.$clave.'"><input type="text" id="input_excedente_'.$clave.'" style="text-align: right" size="9" disabled="true" value="0"></td>';
			}
			$html .='<td class="'.$wcf.' classtotales"  id="input_total_'.$clave.'"  align="right" nowrap=nowrap  rowspan="2"></td>';

			if($vec_pension_final[$clave]['info']=='')
				$html .='<td style="display : block" rowspan="2">'.$vec_pension_final[$clave]['info'].'</td>';
			else
				$html .='<td style="display : block" class="fondoAmarillo" rowspan="2">'.$vec_pension_final[$clave]['info'].'</td>';

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
		
		$html.="<input type='hidden' id='cantidad' value= '".$i."' />";
		
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
		
		// $guardar = "primera vez dia inicio".$dia_hasta.PHP_EOL;
		// seguimiento($guardar, false );


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
		seguimiento("", true );
		$horaespecifica = $vec_politicas['horaespecifica']; 
		foreach ($vec_pension as $clave => $valor)
		{
			
			switch($calculo_segun_cco)
			{

				// tipo de cobro en dias de traslado
				case 'ccomayor':
				{
					
					$guardar = "ccomayor".$calculo_segun_cco.PHP_EOL;
					seguimiento($guardar, false );
					
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
					if($vec_pension[$clave]['dia_empieza_cobro'] > $vec_pension[$clave]['dia_fin_cobro'])
					{
						$vec_pension[$clave]['dia_fin_cobro'] =$vec_pension[$clave]['dia_empieza_cobro'];
						$dia_hasta = $vec_pension[$clave]['dia_fin_cobro'];
					}

					// $guardar = "fecha inicio".$vec_pension[$clave]['dia_empieza_cobro'].PHP_EOL;
					// $guardar .= "fecha fin".$vec_pension[$clave]['dia_fin_cobro'].PHP_EOL;
					// seguimiento($guardar, false );
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
					
					// $guardar = "cconoche dia en que empieza".$dia_hasta.PHP_EOL;
					// seguimiento($guardar, false );

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
					
					if($vec_pension[$clave]['dia_empieza_cobro'] > $vec_pension[$clave]['dia_fin_cobro'])
					{
						$vec_pension[$clave]['dia_fin_cobro'] =$vec_pension[$clave]['dia_empieza_cobro'];
						$dia_hasta = $vec_pension[$clave]['dia_fin_cobro'];
					}
					break;
				}
				// el tipo de cobro por traslado tipo = centro de costo de ingreso o default
				default :
				{
					$guardar = "default".$calculo_segun_cco.PHP_EOL;
					seguimiento($guardar, false );
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
					if($vec_pension[$clave]['dia_empieza_cobro'] > $vec_pension[$clave]['dia_fin_cobro'])
					{
						$vec_pension[$clave]['dia_fin_cobro'] =$vec_pension[$clave]['dia_empieza_cobro'];
						$dia_hasta = $vec_pension[$clave]['dia_fin_cobro'];
					}
					$aux_cantidad_iteraciones++;
					//---------------------------------------------------------
					break;
				}
			}
			
			// $guardar = "Vec pension dia empieza cobro".print_r($vec_pension[$clave]['dia_empieza_cobro'],true).PHP_EOL;
			// seguimiento($guardar, false );
			
			
			
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
	
	function borrar_estancia_unix($whistoria,$wing,$wfechaegreso, $whoraegreso)
	{
		$wfecha=date("Y-m-d");
		$whora = date("H:i:s");
		global $wbasedato;
		global $conex;
		global $wemp_pmla;
		global $wuse;
		
		
		// --> Conexion a unix
		$conexUnix = odbc_connect('facturacion','informix','sco');
		
		$documento ='';
		// --> se obtiene el numero de documentos en inmtra que hay asociados a la historia 
		$sqlinmtra = "SELECT tradoc
						FROM INMTRA
					   WHERE trahis   = '".$whistoria."' 
						 AND tranum   = '".$wing."'";
					   
		$resinmtra = odbc_exec($conexUnix, $sqlinmtra);
		
		$prueba .= "\n\n1.".$sqlinmtra;
		// mientras existan documentos haga
		while(odbc_fetch_row($resinmtra))
		{
			
			
			// con estos documentos se selecciona en facardet 
			$documento = trim(odbc_result($resinmtra,'tradoc'));
			$sqlfacardet = "SELECT cardetdoc
						      FROM FACARDET
					         WHERE cardethis   = '".$whistoria."' 
						       AND cardetnum   = '".$wing."'
							   AND cardetdoc   = '".$documento."'
							   AND cardetfue   = '02'
							   AND cardetori   = 'HC'";
							   
			$prueba .= "\n\n2.".$sqlfacardet;
					   
			$resfacardet = odbc_exec($conexUnix, $sqlfacardet);
			// si existe el registro se actualiza en Facardet
			if(odbc_fetch_row($resfacardet))
			{
					$sqlupdate1 = "	    UPDATE FACARDET
										   SET cardetfac   = 'N'
									     WHERE cardethis   = '".$whistoria."' 
										   AND cardetnum   = '".$wing."'
										   AND cardetdoc   = '".$documento."'
										   AND cardetfue   = '02'
										   AND cardetori   = 'HC'
										   AND cardetvfa   = '0'";
					$resodbc = odbc_do( $conexUnix, $sqlupdate1 );	
					$prueba .= "\n\n3.".$sqlupdate1;
				
			}
			else
			{
					// si no existe  se selecciona  el registro y se mira si traegr es null
					$sqlinmtra2 = "SELECT  trahab , traser , tradoc
									 FROM  INMTRA 
								    WHERE  trahis   = '".$whistoria."' 
									  AND  tranum   = '".$wing."'
									  AND  tradoc   = '".$documento."'
									  AND  traegr   IS NULL ";
					$resinmtra2 = odbc_exec($conexUnix, $sqlinmtra2);
					$prueba .= "\n\n4.".$sqlinmtra2;
					$whabitacion = '';
					$wservicio   = '';
					$wdocumento  = '';
					$fechaegre	 = '';
					$hora_de_ingreso	 = '';
					// si traegr es null  
					if(odbc_fetch_row($resinmtra2))
					{
						
						$whabitacion = odbc_result($resinmtra2,'trahab');
						$wservicio   = odbc_result($resinmtra2,'traser');
						$wdocumento  = odbc_result($resinmtra2,'tradoc');
						
							$hora_de_ingreso = substr($whoraegreso,0,5);
							$hora_de_ingreso = str_replace(":",".",$hora_de_ingreso);
							//$prueba .= "prueba 1";
							// se actualiza inmtra
							$sqlupdate2 = " UPDATE INMTRA
											   SET trades   = '0' , traliq = '1' , traegr ='".$wfechaegreso."' , trahoe = '".$hora_de_ingreso."'
											 WHERE trahis   = '".$whistoria."' 
											   AND tranum   = '".$wing."'
											   AND tradoc   = '".$documento."'";
							$resodbc = odbc_do( $conexUnix, $sqlupdate2 );
							$prueba .= "\n\n5.".$sqlupdate2;
							
							//$prueba .= "prueba 1";
							// actualizacion de inhab
							$sqlupdate3 = " UPDATE INHAB
											   SET habest ='0' , habhis=''
											 WHERE habcod   = '".$whabitacion."' 
											   AND habhis   = '".$whistoria."'";
							$resodbc = odbc_do( $conexUnix, $sqlupdate3 );
							$prueba .= "\n\n6.".$sqlupdate3;
							
							//, falta documento
							// se inserta en innha , una novedad 
							$hora_de_ingreso = substr($whoraegreso,0,5);
							$qinsertinnha = "
							INSERT INTO INNHA 	 (     nhahab			,	nhaser			,		nhaori	,	nhadoi			,		nhafin			,		 nhahin					,	nhaorf	,	  nhadof	, 	nhaffi	, nhahfi,nhaest	,	  nhauad		,			nhafad		 			,	nhaumo		,	nhafmo)
										  VALUES ('".$whabitacion."'	,'".$wservicio."'	, 		  'F'	, '".$wdocumento."'	,	'".$wfechaegreso."'	,		'".$hora_de_ingreso."'	,	  'T'	,		''		,	  ''	,	''	,  '0'	, '".$wuse."'		,	'".$wfecha." ".$whora."'		,	'".$wuse."'	, '".$wfecha." ".$whora."'  )";
							$resDetCargo = @odbc_exec($conexUnix, $qinsertinnha);
							
							$prueba .= "\n\n7.".$qinsertinnha;
							
							
						
						
					}
					else
					{
						
						
						$sqlupdate2 = " UPDATE INMTRA
										   SET trades   = '0' , traliq = '1'
										 WHERE trahis   = '".$whistoria."' 
										   AND tranum   = '".$wing."'
										   AND tradoc   = '".$documento."'";
						$resodbc = odbc_do( $conexUnix, $sqlupdate2 );
						$prueba .= "\n\n8.".$sqlupdate2;
						
					}
					
					
				
			}
			
			//$documento='';
			$prueba .= "\n\n-----------------------------------------------------------------------\n----------------------------------------";
		
		}
	
		odbc_close($conexUnix);
		odbc_close_all();
		return $prueba;
		
	}

	function grabar_pension($whistoria,$wing,$wno1,$wno2,$wap1,$wap2,$wdoc,$wcodemp,$wnomemp,$wser,$wfecing,$wcodcon,$wnomcon,$wfeccar,$wccogra,$wvalor,$wprocedimiento,$wnprocedimiento,$wtarifa,$wcantidad,$wvaltarExce,$wvaltarReco,$wfacturable,$wtip_paciente,$wtipo_ingreso,$wtipoEmpresa,$wnitEmpresa,$wcodter,$wnomter,$wporter,$wcco,$whora_cargo,$wnomCajero,$wcobraHonorarios,$wespecialidad,$wgraba_varios_terceros,$wcodcedula,$wparalelo,$wid_grabado_ant,$wrecexc,$wnumerohab,$wid_tope_afectado,$whora_ingreso,$whora_egreso, $wfecha_ingreso, $wfecha_egreso,$wterunix,$wtraerfechaparcial)
	{


		$wfecha=date("Y-m-d");
		$whora = date("H:i:s");
		global $wbasedato;
		global $conex;
		global $wemp_pmla;
		global $wuse;

		$vector_respuesta = array();
		
		//-----------------
		// borrar_estancia_unix($whistoria,$wing);
		//-----------------
		
		$datos = array();
		$datos['whistoria'] 	= $whistoria;					//ok 1
		$datos['wing'] 			= $wing;						//ok 2
		$datos['wno1'] 			= $wno1;						//ok 3
		$datos['wno2'] 			= $wno2;						//ok 4
		$datos['wap1'] 			= $wap1;						//ok 5
		$datos['wap2'] 			= $wap2;						//ok 6
		$datos['wdoc'] 			= $wdoc;						//ok 7
		$datos['wcodemp'] 		= $wcodemp ;					//ok 8
		$datos['wnomemp'] 		= $wnomemp;						//ok 9
		$datos['tipoEmpresa'] 	= $wtipoEmpresa;				//ok 10
		$datos['nitEmpresa']	= $wnitEmpresa;					//ok 11
		$datos['tipoPaciente']	= $wtip_paciente;				//ok 12
		$datos['tipoIngreso']	= $wtipo_ingreso;				//ok 13
		$datos['wser'] 			= $wser;						//ok 14
		$datos['wfecing']		= $wfecing;						//ok 15
		$datos['wtar']			= $wtarifa;						//ok 16
		$datos['wcodcon'] 		= $wcodcon;						//ok 17
		$datos['wnomcon'] 		= $wnomcon;						//ok 18
		$datos['wprocod']		= $wprocedimiento;				//ok 19
		$datos['wpronom']		= $wnprocedimiento;				//ok 20
		$datos['wcodter']		= $wcodter;						//ok 21
		$datos['wnomter']		= $wnomter;						//ok 22
		$datos['wporter']		= $wporter;						//ok 23
		$datos['grupoMedico']	='';							//falta 24
		$datos['wterunix']		= $wterunix;					//ok 25
		$datos['wcantidad'] 	= $wcantidad;					//ok 26
		$datos['wvaltar'] 		= $wvalor;						//ok 27
		$datos['wrecexc']		= $wrecexc;						//ok 28
		$datos['wfacturable'] 	= $wfacturable;					//ok 29
		$datos['wcco'] 			= $wcco;						//ok 30
		$datos['wccogra'] 		= $wccogra;						//ok 31
		$datos['wfeccar'] 		= $wfeccar;						//ok 32
		$datos['whora_cargo']	= $whora_cargo;					//ok 33
		$datos['wconinv'] 		= 'off';						//ok 34
		$datos['wconabo']		='';							//ok 35 Estan relacionados con medicamentos no aplicarian en estancia
		$datos['wdevol']		='';							//ok 36 Estan relacionados con medicamentos no aplicarian en estancia
		$datos['waprovecha']	='';							//ok 37 Estan relacionados con medicamentos no aplicarian en estancia
		$datos['wconmvto']		='';							//ok 38 Estan relacionados con medicamentos no aplicarian en estancia
		$datos['wexiste']		='';							//ok 39 Estan relacionados con medicamentos no aplicarian en estancia
		$datos['wbod']			='';							//ok 40 Estan relacionados con medicamentos no aplicarian en estancia
		$datos['wconser'] 		= 'H' ;							//ok 41
		$datos['wtipfac']		= "CODIGO";						//ok 42
		$datos['wexidev']		='';							//ok 43 Esta relacionado con devolucion de medicamentos no aplicarian en estancia
		$datos['wfecha'] 		= date("Y-m-d");				//ok 44
		$datos['whora']  		= date("H:i:s");				//ok 45
		$datos['nomCajero']		='';					 		//ok 46 Se esta mandando en vacio
		$datos['cobraHonorarios']	= '';						//ok 47 Pendiente para cuando se prueben las politicas de terceros
		$datos['wespecialidad'] 	= $wespecialidad;			//ok 48
		$datos['wgraba_varios_terceros'] = $wgraba_varios_terceros; //ok 49
		$datos['wvaltarReco']	= $wvaltarReco;
		$datos['enParalelo'] 	= $wparalelo;
		
		if($wparalelo=='off')
		{
			$datos['idParalelo'] 	= '';
			
		}
		else
		{
			$datos['idParalelo'] 	= $wid_grabado_ant;
		}
		$datos['wvaltarExce']	= $wvaltarExce;
		$datos['habitacion'] 	= 	$wnumerohab;
		$datos['fecIngHab'] 	= 	$wfecha_ingreso;
		$datos['horIngHab'] 	= 	$whora_ingreso;
		$datos['diasFacturados']= 	$wcantidad;
		$datos['diasEstancia']	=	$wcantidad;
		$datos['fecEgrHab'] 	= 	$wfecha_egreso;
		$datos['horEgrHab'] 	=   $whora_egreso;
		//$datos['idTope'] 		=   $wid_tope_afectado;
		$datos['idTope'] 		=   "";
		$datos['desdeEstancia'] =   true;
		$idCargo	='';
		$respuesta	='';
		
		
		
		$respuesta = GrabarCargo($datos,$idCargo,false);
		//------
		//------
		if($idCargo!='')
		{
			$insertdetalle = "INSERT INTO ".$wbasedato."_000221 ( 		Dethis			, Deting			, 		Detide		, Detest,		Medico			,Fecha_data		, Hora_data		,Seguridad)
														 VALUES (  '".$whistoria."'		,	'".$wing."'		, 	 '".$idCargo."'	, 'on'	,	'".$wbasedato."'	, '".$wfecha."'	, '".$whora."'	, 'C-".$wuse."'	)";


			mysql_query($insertdetalle, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$qActTope." - ".mysql_error());
		}
		
		if($wtraerfechaparcial !='no')
		{
			// insert a la tabla cliame_000263 liquidacion parcial
			$insertcargosparcial = "INSERT INTO ".$wbasedato."_000263 ( 		Fephis			,	 Feping    		,		 	Fepfec				,  	Fephor					,	Medico				,Fecha_data		, Hora_data		,Seguridad)
																VALUES (  '".$whistoria."'		,	'".$wing."'		, 	 '".$wtraerfechaparcial."'	, 	'".$whora."'			,	'".$wbasedato."'	, '".$wfecha."'	, '".$whora."'	, 'C-".$wuse."'	)";


			mysql_query($insertcargosparcial, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$qActTope." - ".mysql_error());

			
			//---
		}
		
		$vector_respuesta['idcargo'] = $idCargo;
		$vector_respuesta['respuesta'] = $respuesta;

		return $vector_respuesta;
	}

	function descongelar_y_grabarDetalle($whistoria,$wing,$whtml,$wvector_saldos)
	{
		$wfecha=date("Y-m-d");
		$whora = date("H:i:s");
		global $wbasedato;
		global $conex;
		global $wemp_pmla;
		global $wuse;

		$tipoCongelacion = 'ES';
		$accion = 'off';

		//congelarCuentaPaciente($whistoria, $wing, $tipoCongelacion, $accion);

		$q="INSERT INTO ".$wbasedato."_000173
					    ( Fecha_data  ,  Hora_data ,    penhis 		,   	pening   , penhtm      ,penest ,medico   , seguridad )
				 VALUES ('".$wfecha."','".$whora."', '".$whistoria."', 	  '".$wing."' , '".$whtml."' , 'on' , '".$wbasedato."', 'C-".$wuse."'  )";

		mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query (Grabar grupo): ".$q." - ".mysql_error());


		$wvector_saldos = str_replace("\\t", "&#92;t", $wvector_saldos);
		$wvector_saldos = str_replace("\\n", "&#92;n", $wvector_saldos);
		$wvector_saldos = str_replace("\\", "", $wvector_saldos);
		$wvector_saldos = str_replace("\"[", "[", $wvector_saldos);
		$wvector_saldos = str_replace("]\"", "]", $wvector_saldos);
		$wvector_saldos = json_decode( $wvector_saldos, true );

		foreach ($wvector_saldos as $key => $valor)
		{

				$qActTope = "UPDATE ".$wbasedato."_000204
								 SET Topsal = '".$valor."'
							   WHERE id = '".$key."' 
							     AND topdia ='off'
				";
				//mysql_query($qActTope, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$qActTope." - ".mysql_error());

		}
	}

	function anular_pension ($whistoria,$wing)
	{
		global $wbasedato;
		global $conex;
		global $wemp_pmla;
		$array_respuestas = array();


		
		$arrayIdCargos = array();

		$q = " SELECT Detide
				 FROM ".$wbasedato."_000221
				WHERE Dethis = '".$whistoria."'
				  AND Deting = '".$wing."' 
				  AND Detest = 'on' ";

		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$error   = false;
		$mensaje  ='';
		$mensaje2 ='Estos cargos no se pueden eliminar por estancia';
		while($row = mysql_fetch_array($res))
			$arrayIdCargos[] = $row['Detide'];
		
		// --> 21-10-2019, Jerson Trujillo: Antes de proceder a anular validar que ninguno de los cargos haya sido facturado
		$hayCargosFac = false;
		foreach($arrayIdCargos as $idCargo2)
		{
			$cargoFac = verificarCargoFacturado($idCargo2);
			if($cargoFac == 'on')
				$hayCargosFac = true;
		}
		
		if($hayCargosFac){
			echo "No se puede anular la estancia porque ya tiene cargos facturados.<br>Para poder anularla primero debe anular la factura.";
			return;
		}
		
		
		
		foreach($arrayIdCargos as $idCargo)
		{
			$array_respuestas = anular($idCargo);
			
			if($array_respuestas['Error'])
			{
				$mensaje = $array_respuestas['Mensaje'];
				$error   = true;
			}
			else
			{
				$q = " UPDATE ".$wbasedato."_000221
						  SET Detest = 'off'
						WHERE Dethis = '".$whistoria."'
						  AND Deting = '".$wing."' 
						  AND Detide = '".$idCargo."'";
				
				mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				
				$mensaje2 = "Cargos anulados Correctamente";
			}
			
		
		
		}
		
		$q = "UPDATE ".$wbasedato."_000173 "
			."   SET penest ='off' "
			." WHERE penhis ='".$whistoria."'"
			."   AND pening ='".$wing."' ";

		mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query (eliminar participacion 2paso): ".$q." - ".mysql_error());
		
		
		
		//------------tabla de estancia parcial 
		$q = " DELETE
				 FROM ".$wbasedato."_000263
				WHERE Fephis = '".$whistoria."'
				  AND Feping = '".$wing."' ";

		mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

		if($error)
		{
			echo $mensaje;
		}
		else
		{
			echo $mensaje2;
		}
		
		
		//-----Busco si la estancia tiene datos en la tabla cliame 000308 y revierto los cambio que pudo hacer la politica de cambio de cargos
		$query ="SELECT carmat ,	caruni , carest,id
				   FROM  ".$wbasedato."_000308
				  WHERE  carhis = '".$whistoria."' 
				    AND  caring = '".$wing."' 
					AND  carest = 'on' ";
		$resquery = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
		

		$conexUnix = odbc_connect('facturacion','informix','sco');		
		while($rowquery = mysql_fetch_array($resquery))
		{
			if($rowquery['caruni']!='')
			{
				$updatequery = $rowquery['caruni'];
				//ejecuto unix
				odbc_do( $conexUnix, $updatequery );	
			}
			if($rowquery['carmat']!='')
			{
				$updatequery = $rowquery['carmat'];
				// ejecuto matrix
				mysql_query($updatequery,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$updatequery." - ".mysql_error());
			}
			
			$update = "UPDATE ".$wbasedato."_000308 SET carest='off' WHERE id='".$rowquery['id']."' ";
			mysql_query($update,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$update." - ".mysql_error());
		}
		
		odbc_close($conexUnix);
		odbc_close_all();
		
	}

	function detalle_cuenta($whistoria,$wing)
	{
		global $wbasedato;
		global $conex;
		global $wemp_pmla;

		$q = 	 "SELECT penhis , pening , penhtm "
				."  FROM ".$wbasedato."_000173 "
				." WHERE penhis ='".$whistoria."' "
				."   AND pening ='".$wing."' "
				."   AND penest ='on' ";

		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);

		if ($num == 0 || $num =='0')
			$existe_pension =false;
		else
			$existe_pension = true;
		
		
		$existe_pension = true;
		
		if($existe_pension)
		{
			$row = mysql_fetch_array($res);
			$html = "<br><br><table>";
			//$html .= $row['penhtm'];
			$html .= "</table><br><br>";

			
			$html .= detalle_cuenta_resumido ($whistoria, $wing);
			
			$html.= "<br><br>
					<table>
						<tr>
							<td>
								<input type='button' value='Anular' id='id_boton_anular' onclick='anular_pension()'>
							</td>
						</tr>
					</table>";
			echo $html;
		}
		else
		{
			echo "<table><tr><td><div class='fondoAmarillo' style='font-size:12pt; width=300px'>No se han grabado los cargos de estancia de este paciente</div></td></tr></table>";
			$html.= "<br>
					<table>
						<tr>
							<td>
								<input id='id_boton_anular' type='button' value='Anular' disabled onclick='anular_pension()'>
							</td>
						</tr>
					</table>";
			echo $html;
		}

	}

	function resumen_pension($whistoria, $wing,$wtar,$wempresa,$wconcepto,$wcambiar_valor,$wcambiar_dias,$wtipo_ingreso,$wcambiodetipos,$wtipo_paciente, $wfechaparcial)
	{
		global $wbasedato;
		global $conex;
		global $wemp_pmla;
		global $wuse;
		
		$wbasedato_movhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

		// se verifica si la pension ya esta cargada
		$q = 	 "SELECT penhis , pening "
				."  FROM ".$wbasedato."_000173 "
				." WHERE penhis ='".$whistoria."' "
				."   AND pening ='".$wing."' "
				."   AND penest ='on' ";

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
			//---------Estancia parcial
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
		
		if(!$existe_pension && date('Y-m-d') == $fechaingreso_liquidacion_parcial) {
			$html = "	<br>
							<br>
							<br>
							<table>
								<tr>
									<td><img width='15'height='15' src='../../images/medical/root/info.png' /></td><td>Lo sentimos, La historia ($whistoria-$wing), ya cuenta con Liquidaci&oacute;n de Estancia Parcial.</td>
								</tr>
							</table>
							<br>
							<br>
							<br>";
				echo $html;
				return;
		}
		
		
		
		//----------------------------------------------------

		// como se forma la pension
		// 1. primero va a la cliame_101 a buscar el dia de ingreso , fecha de ingreso y el servicio de ingreso
		// 2.busca en la tabla movhos_118 el dia de egreso , fecha de egreso, hora de egreso
		// 3. se va a la funcion traer_detalle_estancia  y primero se consulta los movimientos que tuvo un paciente
		//    consultado los recibos esto da   las fechas y horas de ingreso

		if(!$existe_pension)// si no existe
		{

			$datos_ingreso =array();

			//--Datos de ingreso del paciente
			//----------------------------------------------
			$q_diaingreso =  "SELECT Fecha_data, Hora_data ,id"
							."  FROM ".$wbasedato_movhos."_000017 "
							." WHERE Eyrhis = '".$whistoria."' "
							."   AND Eyring = '".$wing."'"
							."   AND Eyrtip = 'recibo'"
							."   AND Eyrest = 'on'
								 ORDER BY Fecha_data ASC";


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
				
				//$html .="<tr><td>no entro</td></tr>";
				$datos_ingreso ['dia'] 	= 	$fechaingreso_liquidacion_parcial;
				$dia_ingreso 			=	$fechaingreso_liquidacion_parcial;
				$datos_ingreso ['hora'] = 	$horaingreso_liquidacion_parcial;
			}
			
			// $guardar = "primera vez que entro".$datos_ingreso['dia']."----".$row['id'].PHP_EOL;
			// seguimiento($guardar, false );
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
				$html .= "<tr><td><input type='hidden' id='altaprogramada' value='off'></td></tr>";
				$liquidarparcial="si";
			}
			else
			{
				if($wfechaparcial!='no')
				{
					$datos_egreso['dia'] 	= $wfechaparcial;
					$dia_egreso 			= $wfechaparcial;
					$datos_egreso['hora'] 	= date("H:i:s");
				}
				else
				{
					$html .= "<tr><td><input type='hidden' id='altaprogramada' value='on'></td></tr>";
					$liquidarparcial="no";
				}
			}
			
			/// si liquidarparcial es igual a si cuando no tiene alta programada  entonces se haria una liquidacion parcial
			//------------------------------------------------------------------------------
			//--- Si tiene alta en proceso se puede liquidar  la estancia ???
			if( $dia_egreso == '0000-00-00' or $dia_egreso == '' )
			{

				$html = "	<br>
							<br>
							<br>
							<table>
								<tr>
									<td><img width='15'height='15' src='../../images/medical/root/info.png' /></td><td>No se puede liquidar la estancia del paciente porque aun no tiene en proceso. </td>
								</tr>
							</table>
							<br>
							<br>
							<br>";
				echo $html;
				return;

			}
			//----------------------------------------------------------------

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
			$arr_variables['CambiodeCargos']	= "";
			$arr_variables['esdepension']		="Es de pension";

			/* SE AGREGAN ESTAS VARIABLES FALTANTES 2021-11-23 CIDENET SAS*/
			
			$arr_variables['whistoria']			=$whistoria;
			$arr_variables['wing']				=$wing;
			$arr_variables['wfeccar']			=date('Y-m-d');
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
			$vec_politicas ['horaespecifica']   = $arr_variables['horaespecifica'];     // cambio de procedimiento
			$vec_politicas ['CambiodeCargos']   = $arr_variables['CambiodeCargos'];     // cambio de procedimiento

			//-------------------------------------------------------------------------------------------------------
			//-------------------------------------------------------------------------------------------------------

			//---------------------------------------------------------------
			//----------------Informacion de politicas
			$html .='<tr>
						<td>
							<div id="div_datos_politica" >';
						if($arr_variables['dia_inicio_cobro']== '' && $arr_variables['dia_final_cobro'] == '' && $arr_variables['tipo_facturacion'] =='' )
						{
							/*$html .='	<table align="right" class="BordeNaranja" >
											<tr class="encabezadoTabla">
												<td colspan="2">No se esta aplicando politica</td>
											</tr>
										</table>';*/
						}
						else
						{
							/*$html .='<table align="right" class="BordeNaranja" >
										<tr class="encabezadoTabla">
											<td colspan="2" align="center">Datos desde politica</td>
										</tr>
										<tr class="fila1">
											<td >Cobro apartir del dia </td><td>'.$vec_politicas ["dia_inicio_cobro"].'</td>
										</tr>
										<tr class="fila2">
											<td>Cobro hasta el dia </td><td>'.$vec_politicas ["dia_final_cobro"].'</td>
										</tr>
									</table>';*/
						}
			$html .='</div>';
			//-------------------------------------------------------------------
			//-------------------------------------------------------------------
			//-------------------------------------------------			

			$html .='</td>
					 </tr>
					 <tr>';
			
			//--Impresion de la estancia
			$vector_resp = array();
			//---------------Vector respuesta trae el html de la politica
			$vector_resp = traer_detalle_estancia($whistoria, $wing, $datos_ingreso, $datos_egreso ,$vec_politicas,$wconcepto,$wtar,$wempresa,$wcambiar_valor,$wcambiar_dias,$wtipo_ingreso,$nejemplo,$wcambiodetipos,$wtipo_paciente,$fechaingreso_liquidacion_parcial,$horaingreso_liquidacion_parcial);
			//----------------------------------------------------------------
			
			if (count($vector_resp['pension'])*1 ==0)
			{
				$html .= "<td><table><tr><td align='center'><div class='fondoAmarillo' style='font-size:12pt; width=300px'>La Estancia asociada al numero de Historia: <b>".$whistoria." - ".$wing."</b> <br> No tiene movimiento hospitalario</div></td></tr></table></td></tr></table>";
				
			}
			else
			{
			
				$html .= '<td>
							<div id="titulo_detalle_pension">
									<table>
										<tr>
											<td>
												<br>
												<b>RESUMEN DE ESTANCIA </b><br><br>
											</td>
										</tr>
									</table>
								</div>
							</td>
						 </tr>
						 <tr>';
				
				// --> Obtener si el usuario puede liquidar Estancia parcial
				$qEstparUse = " SELECT Cjeppe
								 FROM ".$wbasedato."_000030
								WHERE Cjeusu = '".$wuse."'
								  AND Cjeest = 'on'";
				$ResEstparUse = mysql_query($qEstparUse,$conex) or die("Error en el query: ".$qEstparUse."<br>Tipo Error:".mysql_error());
				if($RowEstparUse = mysql_fetch_array($ResEstparUse))
					$UseEstparcial = $RowEstparUse['Cjeppe'];
				else
					$UseEstparcial = 'off';
				
				if($UseEstparcial =='')
				{
					$UseEstparcial = 'off';
				}
				
				//--------------
				$wconceptoestancia = consultarAliasPorAplicacion($conex, $wemp_pmla, 'concepto_estancia');
				//---Obtener si tiene liquidacion por paquete y restar dias de estancia
				$qEstanciaPaquete = " SELECT Tcarprocod , Tcarcan 
										FROM ".$wbasedato."_000106
									   WHERE Tcarhis ='".$whistoria."' 
									     AND Tcaring ='".$wing."' 
										 AND Tcartfa ='PAQUETE'
										 AND Tcarest ='on'
										 AND Tcarconcod = '".$wconceptoestancia."'";
										 
				
				$ResEstanciaPaquete = mysql_query($qEstanciaPaquete,$conex) or die("Error en el query: ".$qEstanciaPaquete."<br>Tipo Error:".mysql_error());
				$array_estancia_paquete2 = array();
				while($RowEstanciaPaquete = mysql_fetch_array($ResEstanciaPaquete))
				{
					$array_estancia_paquete2[$RowEstanciaPaquete['Tcarprocod']] = $RowEstanciaPaquete['Tcarcan'];
				}
			
				//---------------------
				$html .= '<tr><td align="left" ><table><tr><td width="76%"><a style="cursor:pointer">Ver politicas aplicadas</a></td><td align="center"><div class="fondoAmarillo"><b>'.($liquidarparcial == "si"  ? 'Paciente sin alta programada' : 'Paciente con alta programada' ).'</b>';
				
				if($UseEstparcial=='on')
				{
					$html .='<br>Fecha final para grabar parcial<input type="text" id="wfecparcial" name="wfecparcial" value="'.$datos_egreso['dia'].'" size="10"></div></td></tr></table><input id="permisoestaciaparcial" type="hidden" value="'.$UseEstparcial.'"></tr>';
				}
				else
				{
					$html .='</div></td></tr></table><input id="permisoestaciaparcial" type="hidden" value="'.$UseEstparcial.'"></tr>';
				}
				//----------------------------------------------------------------
				//--Aqui estara el resumen de las politicas aplicadas
				//----------------------------------------------------------------
				$html .='<tr><td align="left"><div><table id="resumendepoliticas">';
				
							$contadorpoliticas = 0;
							if($arr_variables['dia_inicio_cobro'] == '1')
							{
								$contadorpoliticas++;
								$html .='<tr><td><b>'.$contadorpoliticas.'</b></td><td>No se cobra dia inicial de estancia</td></tr>';
							}
							if($arr_variables['dia_final_cobro'] == '1')
							{
								$contadorpoliticas++;
								$html .='<tr><td><b>'.$contadorpoliticas.'</b></td><td>No se cobra dia final de estancia</td></tr>';
							}	
							
							if($arr_variables['CambiodeCargos'] != '')
							{
								$contadorpoliticas++;
								$html .='<tr><td><b>'.$contadorpoliticas.'</b></td><td>Cambio de cargos grabados<input id="Cambiocargos" type="hidden" value="'.$arr_variables['CambiodeCargos'].'"></td></tr>';
							}
							
							if($vector_resp['topes']['existe_tope']=='on')
							{
								$contadorpoliticas++;
								$html .='<tr><td><b>'.$contadorpoliticas.'</b></td><td>Tiene topes</td></tr>';
								
							}
							foreach($array_estancia_paquete2 as $keypaquetes => $valorpaquetes)
							{
								$contadorpoliticas++;
								$html .='<tr><td><b>'.$contadorpoliticas.'</b></td><td>Cirugia por paquete:  '.$valorpaquetes.' dias en habitacion tipo '.$keypaquetes.'</td></tr>';
							}
				$html .='</table><br><br></div><td></tr>';
				//-----------------------------------------------------
				
				$html.='<tr>';		
			
				$html.= '<td>
						 <fieldset id="fieldsetppal">
							<legend align="left">Datos de liquidación</legend>
								<div style="padding: 3px;">';
				$html .= 			$vector_resp['html'];
				$html .= 		'</div>
						  </fieldset>
						</td>';
				//------------------
				//----------------------------------------------------
				$html .='</tr>
					 <tr>
						<td>
						<br>
							<table align="center">
								<tr>
									<td><input type="button" value="Grabar" id="boton_grabar" onclick="grabar_pension(\'si\')"></td>
								<tr>
							</table>
						</td>
					 </tr>
					 <tr>
					  <td><div id="divCargoscambiados"></div></td>
					 </tr>
					</table>';
			}
			$html2 = str_replace("'" ,"\'", $html);

			//--------------------------------------
			$tipoCongelacion = 'ES';
			$accion = 'on';
			//congelarCuentaPaciente($whistoria, $wing, $tipoCongelacion, $accion);
			guardarDetalleCuenta($whistoria, $wing,$html2);

		}
		else
		{

			$html .= "<table>
						<tr>
							<td id='tdidmensajeliquidada'><div class='fondoAmarillo' style='display:none;border: 1px solid #2A5DB0;padding: 5px;' align='center'>!!!La pension ya fue liquidada</div></td>
						</tr>
				  </table>";
		}

		echo $html;

	}
	
	//------------------------------------------------------------------------------
	//	Funcion que muestra el detalle de la cuenta resumido y en acordeones
	//------------------------------------------------------------------------------
	function detalle_cuenta_resumido($whistoria, $wing)
	{
		global $conex;
		global $wbasedato;
		global $wemp_pmla;
		global $wuse;

		$array_paq_cargados 	= array();
		$array_cuenta_cargos	= array();
		$array_cargParalelos	= array();

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
				  Tcardev, Tcarreg, Tcarusu, Gruinv, Audpol
			 FROM ".$wbasedato."_000106 as A INNER JOIN ".$wbasedato."_000024 AS B ON (A.Tcarres = B.Empcod) INNER JOIN ".$wbasedato."_000107 AS C ON (A.id = C.Audreg), ".$wbasedato."_000200
			WHERE Tcarhis 	= '".$whistoria."'
			  AND Tcaring 	= '".$wing."'
			  AND Tcarconcod   = '".$wconceptoestancia."'
			  AND Tcarest 	= 'on'
			  AND Tcarconcod= Grucod
		 ORDER BY Tcarconnom, Tcarfec DESC, Registro DESC
		";

		$res_get_cargos = mysql_query($q_get_cargos,$conex) or die("Error en el query: ".$q_get_cargos."<br>Tipo Error:".mysql_error());
		while($row_get_cargos = mysql_fetch_array($res_get_cargos))
		{
			// --> Crear un array con la informacion organizada
			$inf_cargo['CodProcedi']		= $row_get_cargos['Tcarprocod'];
			$inf_cargo['NomProcedi']		= $row_get_cargos['Tcarpronom'];
			$inf_cargo['Fecha'] 			= $row_get_cargos['Tcarfec'];
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
			$facturado						= (($row_get_cargos['Tcarfex'] == 0 && $row_get_cargos['Tcarfre'] == 0) ? 'no_facturado' : 'facturado');

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

		// --> Pintar informacion
		$resp.= "
		<table width='100%' id='detalleCargo'>
			<tr>
				<td colspan='17' align='right'>
					<table style='font-size: 7pt;font-family: verdana;font-weight:bold'>
						<tr><td colspan='5' align='center' class='encabezadoTabla' style='font-size: 8pt;font-family: verdana;'>Convenciones</td></tr>
						<tr>
							<td style='border:1px solid #999999;padding:2px'>
								Cargo facturado: <img width='15' height='15' src='../../images/medical/root/grabar.png'/>
							</td>
							<td style='border:1px solid #999999;padding:3px'>
								Cargo con paralelo: <img src='../../images/medical/iconos/gifs/i.p.next[1].gif'>
							</td>
							<td style='border:1px solid #999999;padding:3px'>
								Cargo por revisar: <img width='15' height='15' src='../../images/medical/sgc/Warning-32.png'>
							</td>
							<td style='border:1px solid #999999;padding:3px'>
								Cargo con pol&iacute;tica: <img width='15' height='15' src='../../images/medical/sgc/Mensaje_alerta.png'>
							</td>
							<td style='border:1px solid #999999;padding:3px'>
								Anular cargo: <img src='../../images/medical/eliminar1.png'>
							</td>
						</tr>
					</table><br>
				</td>
			</tr>
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

		// --> Pintar cargos
		foreach($array_cuenta_cargos as $tipoFact => $arrCoceptos)
		{
			// --> Barra de cargos facturados o no facturados
			$resp.="
			<tr align='left' width='100%'>
				<td colspan='17' style='background-color : #83D8F7;border: 1px solid #999999;font-family: verdana;'>
					&nbsp;<img onclick='desplegar(this, \"".$tipoFact."\", \"conceptos\")' valign='middle' style=' display: inline-block; cursor : pointer'  src='../../images/medical/hce/mas.PNG'>
					&nbsp;&nbsp;".(($tipoFact == 'facturado') ? 'Cargos facturados' : 'Cargos no facturados' )."
				</td>
			</tr>
			<tr align='center' class='".$tipoFact." conceptos' style='display:none'>
				<td width='6%' colspan='2'></td>
				<td class='encabezadoTabla'>Fecha</td>
				<td class='encabezadoTabla'>Procedimiento</td>
				<td class='encabezadoTabla'>C.Costos</td>
				<td class='encabezadoTabla'>Tercero</td>
				<td class='encabezadoTabla'>Tipo Fac</td>
				<td class='encabezadoTabla'>Rec/Exc</td>
				<td class='encabezadoTabla'>Fact.</td>
				<td class='encabezadoTabla'>Cantidad</td>
				<td class='encabezadoTabla'>Valor. Uni</td>
				<td class='encabezadoTabla'>Valor. Rec</td>
				<td class='encabezadoTabla'>Valor. Exc</td>
				<td class='encabezadoTabla'>Valor. Tot</td>
				<td class='encabezadoTabla'>Entidad</td>
				<td class='encabezadoTabla'>Usuario resp.</td>
				<td class='encabezadoTabla'>Registro</td>
			</tr>";

			$Total_rec_cuenta = 0;
			$Total_exe_cuenta = 0;
			$Total_r_e_cuenta = 0;

			foreach($arrCoceptos as $codConcepto => $arrInfoConceptos)
			{
				$Total_rec = 0;
				$Total_exe = 0;

				// --> Barra del nombre del concepto
				$resp.="
				<tr align='left' class='".$tipoFact." conceptos' style='display:none'>
					<td width='3%'></td>
					<td colspan='12' class='fondoAmarillo' style='border: 1px solid #999999;'>
						&nbsp;<img class='".$tipoFact."-imagen' onclick='desplegar(this, \"".$tipoFact.'-'.$codConcepto."\", \"procedimiento\")' valign='middle' style=' display: inline-block; cursor : pointer'  src='../../images/medical/hce/mas.PNG'>
						&nbsp;&nbsp;<b>".$codConcepto."-".$arrInfoConceptos['NomConcepto']."</b>
					</td>
					<td id='".$tipoFact."-".$codConcepto."' class='fondoAmarillo' style='border: 1px solid #999999;' align='right'></td>
					<td colspan='3' class='fondoAmarillo' style='border: 1px solid #999999;'></td>
				</tr>";

				$ColorFila 		= 'fila1';
				foreach($arrInfoConceptos['InfConcepto'] as $idRegistro => $variables)
				{
					if(!array_key_exists($idRegistro, $array_cargParalelos))
					{
						if($ColorFila == 'fila1')
							$ColorFila = 'fila2';
						else
							$ColorFila = 'fila1';

						// --> Tooltip para mostrar si se aplico alguna politica
						if($variables['politicaAplico'] != "")
						{
							$title = "
								<span style=\"font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 9pt;\">
									&nbsp;<img width=\"12\" height=\"12\" src=\"../../images/medical/root/info.png\">
									<b>Log Pol&iacute;tica: </b>".utf8_encode($variables['politicaAplico'])."&nbsp;
								</span>";
							$infoCargo = "<img tooltip='si' width='15' height='15' src='../../images/medical/sgc/Mensaje_alerta.png' title='".$title."'><br>";
						}
						else
							$infoCargo = "";

						$resp.="
						<tr class='".$tipoFact." ".$tipoFact.'-'.$codConcepto." procedimiento' style='display:none'>";
						// --> Si el cargo tiene un paralelo relacionado
						if($variables['GraboParalelo'] == 'on' && array_key_exists($variables['idParalelo'], $array_cargParalelos))
						{
							$title = "
							<span style=\"font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 9pt;\">
								&nbsp;<img width=\"12\" height=\"12\" src=\"../../images/medical/root/info.png\">
								Cargo en paralelo&nbsp;
							</span>";
							$infoCargo.= "<img imgParalelo title='".$title."' tooltip='si' onClick='verParalelo(this, \"".$variables['idParalelo']."\")' style='cursor:pointer' src='../../images/medical/iconos/gifs/i.p.next[1].gif'>";
							$pintarParalelo = true;
						}
						else
						{
							if($variables['pendienteRevicion'] == 'CR')
							{
								$title = "
								<span style=\"font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 9pt;\">
									&nbsp;<img width=\"12\" height=\"12\" src=\"../../images/medical/root/info.png\" >
									Pendiente de revision&nbsp;
								</span>";
								$infoCargo.= "<img id ='imagen_redistro_".$idRegistro."' style='cursor:pointer' width='15' title='".$title."' tooltip='si'  onclick='comfirmar_revision(\"".$idRegistro."\")' height='15' src='../../images/medical/sgc/Warning-32.png'>";
							}
							if($variables['pendienteRevicion'] == 'PT')
							{
								$title = "
								<span style=\"font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 9pt;\">
									&nbsp;<img width=\"12\" height=\"12\" src=\"../../images/medical/root/info.png\" >
									Pendiente de tarifa&nbsp;
								</span>";
								$infoCargo.= "<img id ='imagen_redistro_".$idRegistro."' style='cursor:pointer' width='15' title='".$title."' tooltip='si'  height='15' src='../../images/medical/sgc/Warning-32.png'>";
							}

							$pintarParalelo = false;
						}
						// --> Tooltip para la especialidad
						$toolEspe = "";
						if($variables['nomEspecialidad'] != '')
						{
							$toolEspe = "
							<span style=\"font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 8pt;\">
								&nbsp;<b>Especialidad:</b>&nbsp;".$variables['nomEspecialidad']."&nbsp;
							</span>";
						}

						// --> Tooltip para el nombre del cco
						$toolNomCco = "
							<span style=\"font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 8pt;\">
								&nbsp;<b>Cco:</b>&nbsp;".$variables['NomServicio']."&nbsp;
							</span>";

						// --> Tooltip para el codigo de la entidad
						$toolCodEnt = "
							<span style=\"font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 8pt;\">
								&nbsp;<b>C&oacute;digo:</b>&nbsp;".$variables['codEntidad']."&nbsp;
							</span>";

						// --> Pintar informacion del cargo
						$resp.="
							<td></td>
							<td align='right'>".$infoCargo."</td>
							<td class='".$ColorFila."' align='center'>".$variables['Fecha']."</td>
							<td class='".$ColorFila."' style='font-size: 8pt;font-family: verdana;' >".$variables['CodProcedi']."-".$variables['NomProcedi']."</td>
							<td class='".$ColorFila."' style='cursor:help;' tooltip='si' title='".$toolNomCco."' >".$variables['Servicio']."</td>
							<td class='".$ColorFila."' style='font-size: 8pt;font-family: verdana;cursor:help;' ".(($variables['nomEspecialidad'] != '') ? " tooltip='si' title='".$toolEspe."' " : "" ).">
								".$variables['NomTercero']."
							</td>
							<td class='".$ColorFila."' style='font-size: 8pt;font-family: verdana;' >".(($variables['Devolucion'] == 'on') ? "Devoluci&oacute;n" : $variables['TipoFact'])."</td>
							<td class='".$ColorFila."' align='center'>".$variables['ReconExced']."</td>
							<td class='".$ColorFila."' align='center'>".$variables['Facturable']."</td>
							<td class='".$ColorFila."' align='center'>".$variables['Cantidad']."</td>
							<td class='".$ColorFila."' align='right'>".number_format($variables['ValorUn'],0,',','.' )."</td>
							<td class='".$ColorFila."' align='right'>".number_format($variables['ValorRe'],0,',','.' )."</td>
							<td class='".$ColorFila."' align='right'>".number_format($variables['ValorEx'],0,',','.' )."</td>
							<td class='".$ColorFila."' align='right'>".number_format($variables['ValorTo'],0,',','.' )."</td>
							<td class='".$ColorFila."' style='font-size: 8pt;font-family: verdana;cursor:help;' tooltip='si' title='".$toolCodEnt."'  >".$variables['Entidad']."</td>
							<td class='".$ColorFila."' style='font-size: 8pt;font-family: verdana;' >".(($variables['Usuario'] != '') ? $variables['Usuario'] : $variables['CodUsuario'])."</td>
							<td class='".$ColorFila."' align='center'>".$idRegistro."</td>
							<td align='center'>";

							if($tipoFact == 'facturado')
								$resp.= "<img tooltip='si' title='".$toltGra."' width='15' height='15' src='../../images/medical/root/grabar.png'/>";
							else
							{
								$mesCargo 		= explode('-', $variables['Fecha']);
								$cargoMesAct 	= ((date("Y-m") == $mesCargo[0]."-".$mesCargo[1]) ? true : false);
							}
						$resp.="
							</td>
							<td align='center'>";
						$resp.="</td>

						</tr>
						";
						// --> Aqui se pinta el cargo paralelo si lo hay.
						if($pintarParalelo)
						{
							$varParalelo = $arrInfoConceptos['InfConcepto'][$variables['idParalelo']];
							$resp.="
							<tr id='".$variables['idParalelo']."' class='".$tipoFact." ".$tipoFact.'-'.$codConcepto." procedimiento-paralelo' style='display:none'>
								<td colspan='2'></td>
								<td class='".$ColorFila."' style='border-left: 2px dotted #72A3F3;' align='center'>".$varParalelo['Fecha']."</td>
								<td class='".$ColorFila."' style='font-size: 8pt;font-family: verdana;' >".$varParalelo['CodProcedi']."-".$varParalelo['NomProcedi']."</td>
								<td class='".$ColorFila."'>".$varParalelo['Servicio']."</td>
								<td class='".$ColorFila."' style='font-size: 8pt;font-family: verdana;' >".$varParalelo['NomTercero']."</td>
								<td class='".$ColorFila."' style='font-size: 8pt;font-family: verdana;' >".$varParalelo['TipoFact']."</td>
								<td class='".$ColorFila."' align='center'>".$varParalelo['ReconExced']."</td>
								<td class='".$ColorFila."' align='center'>".$varParalelo['Facturable']."</td>
								<td class='".$ColorFila."' align='center'>".$varParalelo['Cantidad']."</td>
								<td class='".$ColorFila."' align='right'>".number_format($varParalelo['ValorUn'],0,',','.' )."</td>
								<td class='".$ColorFila."' align='right'>".number_format($varParalelo['ValorRe'],0,',','.' )."</td>
								<td class='".$ColorFila."' align='right'>".number_format($varParalelo['ValorEx'],0,',','.' )."</td>
								<td class='".$ColorFila."' align='right'>".number_format($varParalelo['ValorTo'],0,',','.' )."</td>
								<td class='".$ColorFila."' style='font-size: 8pt;font-family: verdana;' >".$varParalelo['Entidad']."</td>
								<td class='".$ColorFila."' style='font-size: 8pt;font-family: verdana;' >".$varParalelo['Usuario']."</td>
								<td class='".$ColorFila."' style='border-right: 2px dotted #72A3F3;' align='center'>".$varParalelo['Registro']."</td>
							</tr>
							";
						}
					}

					$Total_rec+=(($variables['Facturable'] == 'S') ? $variables['ValorRe'] : 0);
					$Total_exe+=(($variables['Facturable'] == 'S') ? $variables['ValorEx'] : 0);
				}
				$Total_r_e = $Total_rec+$Total_exe;
				$html_barra= '<b>$'.number_format($Total_r_e,0,',','.' ).'</b>';

				$resp.="
				<input type='hidden' class='HiddenTotales' id_barra='".$tipoFact."-".$codConcepto."' value='".$html_barra."'>
				";

				$resp.="
					<tr class='".$tipoFact." ".$tipoFact."-".$codConcepto." procedimiento' style='display:none'>
						<td colspan='9'></td>
						<td colspan='2' align='right' class='encabezadoTabla' style='font-size: 8pt;font-family: verdana;'><b>TOTALES:&nbsp;</b></td>
						<td class='encabezadoTabla' align='right'>&nbsp;$".number_format($Total_rec,0,',','.' )."</td>
						<td class='encabezadoTabla' align='right'>&nbsp;$".number_format($Total_exe,0,',','.' )."</td>
						<td class='encabezadoTabla' align='right'>&nbsp;$".number_format($Total_r_e,0,',','.' )."</td>
					</tr>";
				$Total_rec_cuenta+=$Total_rec;
				$Total_exe_cuenta+=$Total_exe;
				$Total_r_e_cuenta+=$Total_r_e;
			}
			$resp.="
			<tr><td colspan='19'>&nbsp;</td></tr>
			<tr class='".$tipoFact." conceptos' style='display:none' style='font-size: 9pt;font-family: verdana;'>
				<td colspan='8'></td>
				<td colspan='3' style='color:#2a5db0'><b>TOTALES CUENTA:&nbsp;</b></td>
				<td align='right' style='color:#2a5db0'>&nbsp;<b>$".number_format($Total_rec_cuenta,0,',','.' )."</b></td>
				<td align='right' style='color:#2a5db0'>&nbsp;<b>$".number_format($Total_exe_cuenta,0,',','.' )."</b></td>
				<td align='right' style='color:#2a5db0'>&nbsp;<b>$".number_format($Total_r_e_cuenta,0,',','.' )."</b></td>
			</tr>
			<tr>
				<td colspan='19' align='right' style='font-size: 8pt;font-family: verdana;'color:#ffffff;><br>
				</td>
			</tr>";

			$msj = "<font style=\"font-weight:normal;text-align:justify\">&nbsp;<img width=\"12\" height=\"12\" src=\"../../images/medical/root/info.png\">&nbsp;Se regrabaran solo los cargos<br>que esten checkeados&nbsp;</font>";
			$resp.= "
			<tr>
				<td colspan='18' align='center'><br>
					<button tooltip='si' title='".$msj."' id='botonRegrabar' style='font-family: verdana;font-weight:bold;font-size: 8pt;display:none' onClick='ventanaReGrabar(this)' >REGRABAR</button>
				</td>
			</tr>";
		}
		$resp.= "</table>";

		return $resp;
		
	}

	function selectconceptospension($whistoria, $wing )
	{
		global $wbasedato;
		global $conex;
		global $wemp_pmla;
		
		
		//-- se valida si existe pension en la tabla cliame_173
		$q = "SELECT penhis , pening "
			."  FROM ".$wbasedato."_000173 "
			." WHERE penhis ='".$whistoria."' "
			."   AND pening ='".$wing."' "
			."   AND penest ='on' ";

		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);
		if ($num == 0 || $num =='0')
			$existe_pension =false;
		else
			$existe_pension = true;

		// comprobacion si tiene liquidacion de estancia parcial
		if($existe_pension)
		{
			$q = "SELECT Fepfec ,Fephor  
					FROM ".$wbasedato."_000263 
				   WHERE Fephis = '".$whistoria."' 
					 AND Feping = '".$wing."' ";
					 
			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num = mysql_num_rows($res);
			
			
			if ($num == 0 || $num =='0')
				$existe_pension =true;
			else
				$existe_pension =false;
			
		}
		
		if(!$existe_pension)
		{
			$tomotemporal = 'si'; // Variable que especifica si se toma de la temporal  por defecto si
			$temporal = '';
			$fechaparcial = 'no';

			//---------------------------------------------------------------
			//-- Se Crea el select del concepto  de Estancia
			$html =  "<br><b>CONCEPTO:</b> <select name='wconcepto' id='wconcepto' onchange='resumen_pension(\"".$tomotemporal."\" ,\"".$temporal."\" ,\"".$fechaparcial."\")' >";

			// se consulta el concepto de estancia
			$q   = 		   "  SELECT  *  "
						  ."    FROM ".$wbasedato."_000200 "
						  ."   WHERE Grutpr = 'H' ";

			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$html .= "<option valor='nada' value='nada'>Seleccione Concepto</option>";
			while($row = mysql_fetch_array($res))
			{
				$html .= "<option valor='".$row['Grudes']."'  value='".$row['Grucod']."'>".$row['Grucod']."-".$row['Grudes']."</option>";
			}
			//------------------------------------------------
			$html .= "</select>";
			$html .= "<input type='hidden'   id='wcambiodetipos' value='0'><br><br>";// este hidden se crea para guardar todos los cambios de tipo de habitacion.
		}
		else
		{

			$html = "<table>
						<tr>
							<td><br><div class='fondoAmarillo' style='font-size:12pt; width=300px'>La pension para esta historia ya fue liquidada</div></td>
						</tr>
				  </table>";
		}
		return $html;


	}

	function guardarDetalleCuenta($whistoria, $wing, $html)
	{

		global $conex;
		global $wbasedato;
		global $wemp_pmla;
		$sqlTemporal = "   UPDATE ".$wbasedato."_000160
							  SET  Ecotem = '".$html."'
							WHERE Ecohis = '".$whistoria."'
							  AND  Ecoing = '".$wing."'
							  AND  Ecotip = 'ES'
							  AND  Ecoest = 'on' ";

		$res = mysql_query($sqlTemporal,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$sqlTemporal." - ".mysql_error());

	}

	function cargarCuentaPaciente($historia, $ingreso)
	{
		global $conex;
		global $wbasedato;
		global $wemp_pmla;

		$q = "SELECT Ecotem
				FROM ".$wbasedato."_000160
			   WHERE  Ecohis = '".$historia."'
			     AND  Ecoing=  '".$ingreso."'  ";
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

			$row = mysql_fetch_array($res);

		return $row['Ecotem'];

	}
	
	// funcion que valida los cargos en unix  y asi saber si se puede grabar toda la pension sin que existan errores.
	//function validarTarifaUnix($conex_fn, $wbasedato_fn, $wemp_pmla_fn, $conexUnix, $datos)
	function validarTarifaUnix($wdatos)
	{
		global $conex;
		global $wbasedato;
		global $wemp_pmla;
		
		// $conex     = $conex_fn;
		// $wbasedato = $wbasedato_fn;
		// $wemp_pmla = $wemp_pmla_fn;
		
		$tempData = str_replace("\\", "",$wdatos);
		$tempData = json_decode($tempData,true);
		
		
		$respuesta = array('error' => 0 , 'Accion' => [], 'MensajeError' => '', 'EvidenciaError' => '', "mensaje_err"=>"", "mensaje"=>"", "cod_cargo"=>"", "error_tipo"=>"");
		
		foreach ($tempData AS $key => $cargo )
		{
			
			
			$variablesUnix['tercero']       = $cargo['wcodter'];
			$variablesUnix['especialidad']  = $cargo['wespecialidad'];
			$variablesUnix['tipoLiquida']   = 'CODIGO';
			$variablesUnix['concepto']      = $cargo['concepto_cargo'];
			$variablesUnix['procedimiento'] = $cargo['wprocedimiento'];
			$variablesUnix['ccoGraba']      = $cargo['ccogra'];
			$variablesUnix['tipoEmpresa']   = $cargo['tipoEmpresa'];
			$variablesUnix['tarifa']        = $cargo['wtarifa'];
			$variablesUnix['responsable']   = $cargo['wresponsable'];
			$variablesUnix['tipoIngreso']   = $cargo['tipoIngreso'];
			$variablesUnix['tipoPaciente']	= $cargo['tipoPaciente'];
			$cobraHonorarios ='';
			$variablesUnix['cobraHono']     = $cobraHonorarios;
			$estaEnTurno ='';
			$variablesUnix['estaEnTurno']   = $estaEnTurno;
			$grupoMedico ='';
			$variablesUnix['grupoMedico']   = $grupoMedico;
			$variablesUnix['terceroUnix']   = $cargo['wtercero_unix'];
			$terceroUnix  					= $variablesUnix['terceroUnix'];
			
			
			homologarConUnix($variablesUnix);
			$concepto = $variablesUnix['concepto'] ;
			$procedimiento					= $variablesUnix['procedimiento'];
			$tarifa							= $variablesUnix['tarifa'];
			$responsable				    = $variablesUnix['responsable'];
			
			// --> Obtener conexion a unix
			$conexionUnixExterna = false;
			$sihayconexion = true;
			if(isset($variablesUnix['conexUnix_FacturacionPpal']) && $variablesUnix['conexUnix_FacturacionPpal']!='')
			{
				// Si ya hay una conexión a unix desde un programa externo entonces asignar ese indice a la conexión local de este función.
				$conexUnix = $variablesUnix['conexUnix_FacturacionPpal'];
				$conexionUnixExterna = true;
			}
			else
			{
				if($conexUnix = @odbc_connect('facturacion','informix','sco'))
				{
					//
				}
				else
				{
					$sihayconexion = false;
					
				}
			}
			
			if($sihayconexion == false)
			{
				$respuesta['error_tipo']     = "error_conexion"; // No hay relación concepto-tercero en unix.
				$respuesta['error']          = 1;
				$respuesta['mensaje']    = "No hay conexion con Unix";
				return $respuesta;
			}
			
			$infoTarifa = obtenerTarifaUnix($conexUnix, $concepto, $procedimiento, $tarifa, $responsable);
			
			// --> Si no tiene tarifa termino el proceso de grabacion a unix.
			if(!$infoTarifa['tieneTarifa'])
			{
				$cod_homologacion = (($variablesUnix['codHomologar']!='') ? " (Homologación: {$variablesUnix['codHomologar']})": "");
				$respuesta['error']          = 1;
				$respuesta['error_tipo']     = "error_tarifa";
				$respuesta['mensaje_err']    = "1-No se encontro tarifa en Unix ";
				$respuesta['mensaje']        = '<span style="font-weight:bold;">Procedimiento:</span> '.$procedimiento.$cod_homologacion.'; <span style="font-weight:bold;">Concepto:</span> '.$concepto.';<span style="font-weight:bold;"> Tarifa:</span> '.$tarifa.'; <span style="font-weight:bold;">Responsable:</span> '.$responsable.'';
				$respuesta['EvidenciaError'] = $infoTarifa['mensaje'].' (Pro: '.$procedimiento.')>> '.str_replace("'", "", $infoTarifa['queryTarifa']);
				return $respuesta;
			}
			else
			{
				
				// [updt-75] Se crea validación para comprobar que la relación concepto homologado y tercero, existan en unix, en caso contrario no permitir grabar la liquidación.
				if(!empty($terceroUnix))
				{
					
					$sqlTipoCon = " SELECT  contip
									FROM    facon
									WHERE   concod = '{$concepto}'";

					$resTipoCon = odbc_exec($conexUnix, $sqlTipoCon);
					odbc_fetch_row($resTipoCon);
					$tipoConcepto = trim(odbc_result($resTipoCon, 1));
					
					
					// Se valída la relación concepto-tercero solo si el concepto es compartirdo.
					if($tipoConcepto == "C")
					{
						$sql_unx = "SELECT  connitnit
									FROM    faconnit
									WHERE   connitcon = '{$concepto}'
									  AND   connitnit = '{$terceroUnix}'";
						// $result = odbc_exec($conexUnix, $sql_unx);
						$registro_unix  = false;
						
						if($result_unx = odbc_exec($conexUnix, $sql_unx))
						{
							while(odbc_fetch_row($result_unx))
							{
								$connitnit = trim(odbc_result($result_unx,'connitnit'));
								if($connitnit == $terceroUnix) 
								{ 
									$registro_unix = true; 
									
								}
							}
						}
						$sql_unx_ter = $sql_unx;

						if(!$registro_unix)
						{
							$respuesta['error_tipo']     = "error_con_ter_unx"; // No hay relación concepto-tercero en unix.
							$respuesta['error']          = 1;
							$respuesta['mensaje_err']    = "No se encontro relacion concepto-tercero en unix.";
							$respuesta['mensaje']        = 'El tercero: <span style="font-weight:bold;">'.$terceroUnix.'</span> y concepto: <span style="font-weight:bold;">'.$concepto.'</span>, no están relacionados en unix';
							$respuesta['EvidenciaError'] = $infoTarifa['mensaje'].' (Pro: '.$procedimiento.')>> '.str_replace("'", "", $sql_unx_ter);
							return $respuesta;
						}
					}
				}
				$respuesta['Accion']['queryTarifa'] = str_replace("'", "", $infoTarifa['queryTarifa'].$sql_unx_ter);
				
		
				
			}
			
			
			//------- cierro conexiones con unix
			odbc_close($conexUnix);
			odbc_close_all();
		}
		return $respuesta;
		
		
		
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
		case 'validarCargos':
		{
			$data = validarTarifaUnix ($wdatos);
			echo json_encode($data);
			break;
			return;
		}
		case 'grabar_pension':
		{
			$data = grabar_pension ($whistoria,$wing,$wno1,$wno2,$wap1,$wap2,$wdoc,$wcodemp,$wnomemp,$wser,$wfecing,$wcodcon,$wnomcon,$wfeccar,$wccogra,$wvalor,$wprocedimiento,$wnprocedimiento,$wtarifa,$wcantidad,$wvaltarExce,$wvaltarReco,$wfacturable,$wtip_paciente,$wtipo_ingreso,$wtipoEmpresa,$wnitEmpresa,$wcodter,$wnomter,$wporter,$wcco,$whora_cargo,$wnomCajero,$wcobraHonorarios,$wespecialidad,$wgraba_varios_terceros,$wcodcedula,$wparalelo,$wid_grabado_ant,$wrecexc,$wnumerohab,$wid_tope_afectado,$whora_ingreso,$whora_egreso, $wfecha_ingreso, $wfecha_egreso,$wterunix,$wtraerfechaparcial);
			echo json_encode($data);
			break;
			return;
		}
		case 'borrar_estancia_unix':
		{
			
			$data = borrar_estancia_unix($whistoria,$wing,$wfechaegreso,$whoraegreso);
			echo $data;
			break;
			return;
		}
		case 'descongelar_y_grabarDetalle':
		{
			descongelar_y_grabarDetalle($whistoria,$wing,$whtml,$wvector_saldos);
			break;
			return;

		}

		case 'anular_pension':
		{

			anular_pension ($whistoria,$wing);
			break;
			return;
		}

		case 'detalle_cuenta':
		{

			detalle_cuenta($whistoria,$wing);
			break;
			return;
		}

		case 'resumen_pension':
		{

			resumen_pension($whistoria, $wing,$wtar,$wempresa,$wconcepto,$wcambiar_valor,$wcambiar_dias,$wtipo_ingreso,$wcambiodetipos,$wtipo_paciente, $wfechaparcial);
			break;
			return;
		}
		
		case 'ModificarCargos':
		{
			
			// busco politica de cambio de cargos
			
			// $wcambiocargos; 
			
			$auxwcambiocargos =   explode('!',$wcambiocargos); 
			for($t=1 ; $t<count($auxwcambiocargos) ; $t++ )
			{
					
					$auxwcambiocargos2 = explode(':',$auxwcambiocargos[$t]);
					$vectorhabitaciones[$auxwcambiocargos2[0]][$auxwcambiocargos2[1]][$auxwcambiocargos2[2]]=$auxwcambiocargos2[3];
			}
			// $vectorhabitaciones['O']['0700']['*']='S';
			// $vectorhabitaciones['O']['0700']['903839']='S';
			//$vectorhabitaciones['O']['0700']['911020']='S';
			$html = "";
		
			$tempData = str_replace("\\", "",$wdatos);
			$tempData = json_decode($tempData,true);
		
		
			$respuesta = array('error' => 0 , 'Accion' => '', 'MensajeError' => '', 'EvidenciaError' => '', "mensaje_err"=>"", "mensaje"=>"", "cod_cargo"=>"", "error_tipo"=>"");
			if(count($auxwcambiocargos)>0)
			{
				$html .="<br><br><table><tr><td class='encabezadoTabla' align='center' colspan='2'>Modificación de Cargos</td></tr>";
				$i=0;
				foreach ($tempData AS $key => $cargo )
				{
					
					//$html .="<tr class='encabezadoTabla'><td>".$whistoria."-".$wing."</td><td>".$cargo['tipo']."</td><td>".$cargo['inicial']."</td><td>".$cargo['final']."</td><td>Cambio matrix</td><td>cambio unix</td></tr>";
					// busco los cargos grabados esos dias		
					$select = "SELECT *
								 FROM ".$wbasedato."_000106 
								WHERE Tcarhis = '".$whistoria."'
								  AND Tcaring = '".$wing."'
								  AND Tcarfec BETWEEN '".$cargo['inicial']."' AND '".$cargo['final']."'
								  Order by id";
					
					$res	= mysql_query($select, $conex) or die("Error en el query: ".$select."<br>Tipo Error:".mysql_error());
				
					while($row = mysql_fetch_array($res))
					{
						if (($i%2)==0)
							$wcf="fila1";  // color de fondo de la fila
						else
							$wcf="fila2"; // color de fondo de la fila
						
						
						// En este vector estan las habitaciones en donde se tiene que mirar la politica
						if(isset($vectorhabitaciones[$cargo['tipo']]))
						{
							if(isset($vectorhabitaciones[$cargo['tipo']][$row['Tcarconcod']]) || isset($vectorhabitaciones[$cargo['tipo']]['*']))
							{
								if(isset($vectorhabitaciones[$cargo['tipo']][$row['Tcarconcod']][$row['Tcarprocod']]) || isset($vectorhabitaciones[$cargo['tipo']]['*']['*']) || isset($vectorhabitaciones[$cargo['tipo']]['*'][$row['Tcarprocod']]) || isset($vectorhabitaciones[$cargo['tipo']][$row['Tcarconcod']]['*']) )
								{	
									
									//miro el valor , facturable o no facturable
									$value = (isset($vectorhabitaciones[$cargo['tipo']][$row['Tcarconcod']][$row['Tcarprocod']]) ? $vectorhabitaciones[$cargo['tipo']][$row['Tcarconcod']][$row['Tcarprocod']] : 'no' );
									if($value =='no')
									{
										$value = (isset($vectorhabitaciones[$cargo['tipo']][$row['Tcarconcod']]['*']) ? $vectorhabitaciones[$cargo['tipo']][$row['Tcarconcod']]['*'] : 'no' );
									}
									
									if($value =='no')
									{
										$value = (isset($vectorhabitaciones[$cargo['tipo']]['*'][$row['Tcarprocod']]) ? $vectorhabitaciones[$cargo['tipo']]['*'][$row['Tcarprocod']] : 'no' );
									}
									
									if($value =='no')
									{
										$value = (isset($vectorhabitaciones[$cargo['tipo']]['*']['*']) ? $vectorhabitaciones[$cargo['tipo']]['*']['*'] : 'no' );
									}
									
									
									// mueve inventario 
									if($row["Tcardoi"] !='')
									{
										if($row["Tcardod"] !='')
										{
											
										}
										else
										{
											// ES un medicamento y hay que cambiarlo en unix y en matrix
											if ($row["Tcardun"] !="" AND $row["Tcarlun"] !="" AND $row["Tcarfun"] !="")
											{
													if($row["Tcarfac"] == $value)
													{
														//$html .="--".$row["Tcarfac"]."--</td><td></td></tr>";
														
													}
													else
													{
														$i++;
														$html .="<tr class='fondoAmarillo'><td>".$row["Tcarprocod"]."-".$row["Tcarpronom"]."  Será cambiado a ".(($value =='N') ? 'No facturable' : 'facturable')."</td><td style='display:none'>".$row["Tcarser"]."</td><td style='display:none'>".$row["id"]."</td><td style='display:none'>".$value;
								
														//$html .="--".$row["Tcarfac"]."--Hago cambio</td></tr>";
														
														$updatematrix ="UPDATE ".$wbasedato."_000106"  
																	   ."  SET Tcarfac = '".$value."'"
																	   ." WHERE id = '".$row["id"]."'";
																		 
														$fuente=explode('-', $row["Tcarfun"]); 
														$fuente = $fuente[0];
														$updateunix =  " UPDATE FACARDET " 
																	  ."	SET Cardetfac ='".$value."'" 
																	  ."  WHERE cardetfue ='".$row["Tcarfun"]."'"
																	  ."	AND cardetdoc ='".$row["Tcardun"]."' "
																	  ."	AND cardetlin ='".$fuente."'";
														
														$updateunix=base64_encode($updateunix);
														$kk=base64_encode($updatematrix);
														$html .="--".$row["Tcarfac"]."--".$updatematrix."</td><td style='display:none' class='cambio' unix='".$updateunix."' matrix='".$kk."'>".$updateunix."</td></tr>";
													}
											}
											
											
										}
										
									}
									else
									{
										if($row["Tcarfac"] == $value)
										{
											//$html .="<tr class='fondoAmarillo'><td>".$row["Tcarconcod"]."-".$row["Tcarconnom"]."</td><td>".$row["Tcarprocod"]."-".$row["Tcarpronom"]."</td><td>".$row["Tcarser"]."</td><td>".$row["id"]."</td><td>".$value."</td><td></td></tr>";
								
										}
										else
										{
											$i++;
											$html .="<tr class='fondoAmarillo' ><td>".$row["Tcarprocod"]."-".$row["Tcarpronom"]." Será cambiado a ".(($value =='N') ? 'No facturable' : 'facturable')."</td><td style='display:none'>".$row["Tcarser"]."</td><td style='display:none'>".$row["id"]."</td><td style='display:none'>".$value;
											
											$updatematrix ="UPDATE ".$wbasedato."_000106"  
															." SET Tcarfac = '".$value."'"
															." WHERE id = '".$row["id"]."'";
																		 
											$kk=base64_encode($updatematrix);							 
											$select2 = "SELECT Audrcu
														 FROM ".$wbasedato."_000107 
														WHERE Audreg = '".$row["id"]."'";
											
											$res2	= mysql_query($select2, $conex) or die("Error en el query: ".$select."<br>Tipo Error:".mysql_error());
											$registrounix = "";
											if($row2 = mysql_fetch_array($res2))
											{
												$registrounix = $row2["Audrcu"];
											}
																		 
											$updateunix =   " UPDATE FACARDET " 
															."   SET cardetfac ='".$value."'"
															." WHERE cardetreg ='".$registrounix."'";
											$updateunix= base64_encode($updateunix);
											
											$html .="--".$row["Tcarfac"]."--".$updatematrix."</td><td class='cambio' unix='".$updateunix."' matrix='".$kk."' style='display:none'>".$updateunix."</td></tr>";
										
										}
										
									}
								}
							}
								
						}
						else
						{
							//$html .="<tr class='".$wcf."'><td>".$i."---NO---".$row["Tcarconcod"]."-".$row["Tcarconnom"]."</td><td>".$row["Tcarprocod"]."-".$row["Tcarpronom"]."</td><td>".$row["Tcarser"]."</td><td>".$row["id"]."</td></tr>";
						
						}
						
						//$html .="<tr class='".$wcf."'><td>".$i."----NO---".$row["Tcarconcod"]."-".$row["Tcarconnom"]."</td><td>".$row["Tcarprocod"]."-".$row["Tcarpronom"]."</td><td>".$row["Tcarser"]."</td><td>".$row["id"]."</td></tr>";
								
					
					
					}
				
				}
				
				if($i==0)
				{
					$html.="<tr><td class='fila1' colspan='2'>No hay cargos que cumplan las condiciones para ser cambiados</td></tr>";
				}
				
				
				$html .="</table><br>";
			
			}
			echo $html;
			break;
			return;
		}
		
		

		case 'GrabarPoliticasCambio':
		{
			
			global $wbasedato;
			global $conex;
			global $wemp_pmla;
			$wfecha			= date("Y-m-d");
			$whora 			= date("H:i:s");
			$tempData = str_replace("\\", "",$wdatos);
			$tempData = json_decode($tempData,true);
			$conexUnix = odbc_connect('facturacion','informix','sco');
		
			$count = 0;
			foreach ($tempData AS $key => $cargo )
			{
				
				//print_r($cargo);
				$select = base64_decode($cargo['matrix']);
				if($res	= mysql_query($select, $conex))
				{
					
					if (strpos($select, "'N'")) {
						$select = str_replace("'N'", "'S'" ,$select);
					}
					else
					{
						$select = str_replace("'S'", "'N'" ,$select);
					}
					$insert = "INSERT INTO ".$wbasedato."_000308 ( Medico , Fecha_data, Hora_data , carhis , caring,  carmat  , caruni , carest , Seguridad ) 
							   VALUES 							 ( '".$wbasedato."' ,'".$wfecha."' , '".$whora."' , '".$whis."' , '".$wing."' , '".str_replace("'", "\"" ,$select)."','','on' , 'C-".$wbasedato."')";
					
					mysql_query($insert, $conex);
				} 
				else
				{
					echo ("Error en el query: ".$select."<br>Tipo Error:".mysql_error());
				}
				
				
				$updatematrixc = base64_decode($cargo['unix']);		
				odbc_do( $conexUnix, $updatematrixc );	
				
				
				if (strpos($updatematrixc, "'N'")) {
					$updatematrixc = str_replace("'N'", "'S'" ,$updatematrixc);
				}
				else
				{
					$updatematrixc = str_replace("'S'", "'N'" ,$updatematrixc);
				}
				$insert = "INSERT INTO ".$wbasedato."_000308 ( Medico , Fecha_data, Hora_data , carhis , caring, caruni , carmat , carest , Seguridad) 
						        VALUES 							 ( '".$wbasedato."' ,'".$wfecha."' , '".$whora."' , '".$whis."' , '".$wing."' , '".str_replace("'", "\"" ,$updatematrixc)."' , '','on' , 'C-".$wbasedato."')";
				
				mysql_query($insert, $conex);	
				
			}
			
			odbc_close($conexUnix);
			odbc_close_all();
			break;
			return;
		}
		
		case 'selectconceptospension':
		{

			echo $html = selectconceptospension($whistoria, $wing);
			break;
			return;
		}
		case 'obtener_array_permisos':
		{
			$arr_permisos=obtener_array_permisos();
			echo json_encode($arr_permisos);
			break;
			return;
		}
		case 'obtener_array_terceros':
		{
			$arr_terceros=obtener_array_terceros_especialidad();
			echo json_encode($arr_terceros);
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
		case 'datos_desde_tercero':
		{

			$data = datos_desde_tercero($wcodter,$wcodesp,$wcodcon,$wtip_paciente,$whora_cargo, $wfecha_cargo,$wtipoempresa,$wtarifa,$wempresa,$wcco,$wcod_procedimiento,$wcuadro_turno='',$wparticipacion );
			echo json_encode($data);
			break;
			return;
		}

		case 'cargar_datos_caja':
		{
			$data = cargar_datos_caja();
			echo json_encode($data);
			break;
			return;
		}
		case 'estadoCuentaCongelada':
		{
			$infoEncabezado = estadoCongelacionCuentaPaciente($historia, $ingreso);

			// --> Si hay un encabezado
			if($infoEncabezado['hayEncabezado'])
				$infoEncabezado = $infoEncabezado['valores'];
			else
				$infoEncabezado['Ecoest'] = 'off';

			$infoEncabezado['wuse'] = $wuse;
			echo json_encode($infoEncabezado);
			break;
		}

		case 'congelarCuentaPaciente':
		{
			//congelarCuentaPaciente($historia, $ingreso, 'ES', $congelar);
			break;
		}
		case 'cargarCuentaPaciente':
		{
			$imprimir = cargarCuentaPaciente($historia, $ingreso);
			echo $imprimir;
			break;
		}

		case 'horaFechaDelServidor':
		{
			$data['Hora']  = date("H:i");
			$data['Fecha'] = date("Y-m-d");
			echo json_encode($data);
			break;
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
		cargar_datos_caja();
		cargar_datos('wing');

		$("#accordionDatosPaciente").show();
		$( "#accordionDatosPaciente" ).accordion({
			collapsible: true,
			heightStyle: "content"
		});

		$("#DatosPaciente").css( "display" , "block");
		
		$( "#accordionPension" ).show();
		$( "#accordionPension" ).accordion({
			collapsible: true,
			heightStyle: "content"

		});

		$("#detalle_liquidacion_general").css( "display" , "block");
			
		$("#accordionDetCuenta" ).show();
		$( "#accordionDetCuenta" ).accordion({
			collapsible: true,
			heightStyle: "content",
			active: -1
		});


		// --> Cargar tooltips
		$('.tooltip').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });

		obtener_array_permisos();
		
		
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

		// --> Cargar datos basicos del paciente
		
		//--> Cargar terceros
		obtener_terceros();
		//--> cargar terceros especialidad
		

	});
	
	//--------------------------------------------------------------------
	//	Funcion que despliega y oculta el detalle de la cuenta
	//--------------------------------------------------------------------
	function desplegar(elemento, clase, tipo)
	{
		elemento = jQuery(elemento);
		if(elemento.attr('src')== '../../images/medical/hce/mas.PNG')
		{
			elemento.attr('src', '../../images/medical/hce/menos.PNG');
			$('.'+clase+'.'+tipo).show();
		}
		else
		{
			elemento.attr('src', '../../images/medical/hce/mas.PNG');
			$('.'+clase).hide();
			$('.'+clase+'-imagen').each(function(){
				$(this).attr('src', '../../images/medical/hce/mas.PNG');
			});

			// --> Colocar flecha abajo en los tr que manejen paralelo
			Elemento = $('.'+clase).find("[imgParalelo]");
			Elemento.attr("src", "../../images/medical/iconos/gifs/i.p.next[1].gif");

			// --> Quitar estilos de los paralelos
			Elemento.parent().next().css({
				'border-left': 		''
			});
			Elemento.parent().parent().find('td[class]').css({
				'border-top': 		''
			});
			Elemento.parent().parent().find('td[class]:last').css({
				'border-right': 		''
			});
		}

		// --> redimencionar acordeon
		$( "#accordionDetCuentaResumido" ).accordion("destroy");
		$( "#accordionDetCuentaResumido" ).accordion({
			collapsible: true,
			heightStyle: "content"
		});
	}
	
	//------------------------------------------------------------------
	// Funcion que va por los terceros para cargarlos en el buscador
	//-----------------------------------------------------------------
	function obtener_terceros()
	{
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:      '',
			wemp_pmla:         $('#wemp_pmla').val(),
			accion:            'obtener_array_terceros'

		},function (data) {
			 Arrayterceros  = data;
			 $('#array_terceros').val(Arrayterceros);
		});
		
		
	}
	
	
	
	//----------------------------------------------
	//Funcion que  pinta un select de especialidades
	//---------------------------------------------
	function cargarSelectEspecialidades( cadena,n  ,auxclave )
    {
		var especialidades = cadena.split(",");
		var html_options = "";
		for( var i in especialidades ){
			var especialidad = especialidades[i].split("-");
			html_options+="<option value='"+especialidad[0]+"'>"+especialidad[1]+"</option>";
		}
		$("#busc_especialidades_"+auxclave+"_"+n).html( html_options );


    }
	
	
	//-------------------------------------------------------------------------
	//	Funcion para abrir la historia clinica
	//-------------------------------------------------------------------------
	function abrirHce()
	{
		//alert($("#wemp_pmla").val()+"&wcedula="+$("#wdoc").val()+"&wtipodoc="+$("#wtip_doc").val()+"&wdbmhos="+$("#wbasedato_movhos").val()+"&whis="+$("#whistoria").val()+"&wing="+$("#wing").val());
		
		if($("#wdoc").val() != '' && $("#wtip_doc").val() != '')
		{
			var url 	= "/matrix/HCE/procesos/HCE_Impresion.php?empresa=hce&origen="+$("#wemp_pmla").val()+"&wcedula="+$("#wdoc").val()+"&wtipodoc="+$("#wtip_doc").val()+"&wdbmhos="+$("#wbasedato_movhos").val()+"&whis="+$("#whistoria").val()+"&wing="+$("#wing").val()+"&wservicio=*&protocolos=0&CLASE=C&BC=1";
			alto		= screen.availHeight;
			ventana 	= window.open('','','fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=0,scrollbars=1,titlebar=0');
			ventana.document.open();
			ventana.document.write("<span><b>CONSULTA DESDE GRABACI&Oacute;N DE CARGOS<b></span><br><iframe name='' src='" + url + "' height='" + (parseInt(alto,10) - 70) + "' width='100%' scrolling=yes frameborder='0'></iframe>");
		}
	}
	
	//-------------------------------------------------------------------------
	//	Funcion para abrir ordenes medicas
	//-------------------------------------------------------------------------
	function abrirOrdenes()
	{
		if($("#wdoc").val() != '' && $("#wtip_doc").val() != '')
		{
			var url 	= "/matrix/hce/procesos/ordenes_imp.php?wemp_pmla="+$("#wemp_pmla").val()+"&whistoria="+$("#whistoria").val()+"&wingreso="+$("#wing").val()+"&tipoimp=imp&alt=off&wtodos_ordenes=on&orden=asc&origen=on";
			alto		= screen.availHeight;
			ventana 	= window.open('','','fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=0,scrollbars=1,titlebar=0');
			ventana.document.open();
			ventana.document.write("<span><b>CONSULTA DESDE GRABACI&Oacute;N DE CARGOS<b></span><br><iframe name='' src='" + url + "' height='" + (parseInt(alto,10)) + "' width='100%' scrolling=no frameborder='0'></iframe>");
		}
	}
	
	
	//-------------------------------------------------------------------------
	//	Funcion Generica para crear los autocompletar 
	//-------------------------------------------------------------------------
	function crear_autocomplete( campo , ArrayValores)
	{
		
		//var ArrayValores  = eval('(' + $('#'+HiddenArray).val() + ')');
		var index			= -1;
		var arrayTerceros	= new Array();
		for (var cod_ter in ArrayValores)
		{
			index++;
			arrayTerceros[index] = {};
			arrayTerceros[index].value  	= cod_ter;
			arrayTerceros[index].label  	= cod_ter+'-'+ArrayValores[cod_ter]['nombre'];
			arrayTerceros[index].name  	= ArrayValores[cod_ter]['nombre'];
			arrayTerceros[index].especialidades  = ArrayValores[cod_ter]['especialidad'];
		}
		$( "#"+campo ).autocomplete({
			minLength: 	0,
			source: 	arrayTerceros,
			select: 	function( event, ui ){
				$( "#"+campo ).val(ui.item.label);
				$( "#"+campo ).attr('valor', ui.item.value);
				$( "#"+campo ).attr('nombre', ui.item.name);
				n = $( "#"+campo ).attr('numerorow');
				auxclave = $( "#"+campo ).attr('auxclave');
				cargarSelectEspecialidades(ui.item.especialidades, n ,auxclave );

				datos_desde_tercero( ui.item.value , n ,auxclave);
				//verificarDisponibilidad();

				return false;
			}
		});
		//limpiaAutocomplete('busc_terceros_'+n);
	}
	
	//-----------------------------------------------
	// Funcion habilita el input excedente (excedente es el valor que pagara como excedente el paciente) segun la clave que se envie
	//-----------------------------------------------
	function habilitar_excedente(clave)
	{
		if(!$("#detalle_"+clave).is(':visible'))
		{
			$("#detalle_"+clave).show();
		}
		if( $('#checkbox_excedente_'+clave).prop('checked') ) 
		{
			$("#input_excedente_"+clave).val("0.00");
			$("#input_excedente_"+clave).attr("valor","0");
			
			$(".excedente_r_"+clave).each(function (){
				$(this).removeAttr("valor");
				$(this).removeAttr("value");
				$(this).attr("placeholder", "Ingrese el valor");
				$(this).removeAttr("disabled");
				$(this).attr("onblur", "calcularExcedente("+clave+" , "+$(this).attr('id')+" )");
			});
			
			
		}
		else
		{
			$("#input_excedente_"+clave).prop( "disabled", true );
			$("#input_excedente_"+clave).val("0.00");
			$("#input_excedente_"+clave).attr("valor","0");
			$("#input_excedente_"+clave).attr("value","0.00");
			
			
			$(".excedente_r_"+clave).each(function (){
				$(this).attr("valor", "0");
				$(this).val("0.00");
				$(this).prop( "disabled", true );
			});
		}
	}
	
	function calcularExcedente(clave , id)
	{
		id = jQuery(id);
		
		var valor = id.val();
		//alert(valor);
		valor = valor.replace(".00", "");
		//alert(valor);
		valor = valor.replace(",", "");
		//alert(valor);
		valor = valor *1;
		//alert(valor);
		var numero= id.attr("numero");
		var totalformateado = formatearnumero(valor);
		id.val(totalformateado);
		id.attr("valor" , valor);
		var total = 0;
		var auxiliar=0;
		$(".excedente_r_"+clave).each(function (){
			
			
			if($(this).attr("numero")*1 > numero )
			{
				$(this).val(totalformateado);
				$(this).attr("valor" , valor);
				
			}
			else
			{
				//$(this).val(formatearnumero($(this).val()));
				//$(this).attr("valor" , valor);
			}
			
			if( !$(this).attr("valor") )
			{
				//$(this).attr("valor" , 0);
				//$(this).val("0.00");
				auxiliar = 0;
				
			}
			else
			{
				auxiliar = $(this).attr("valor")*1;
			}
			total = (total*1) + (auxiliar*1);
			$("#input_excedente_"+clave).val(formatearnumero(total));
			$("#input_excedente_"+clave).attr('valor', total );
		});
		

	}
	
	
	//------------------------------------------------------------------
	//-Funcion que trae los datos segun el tercero dado , el dato traido es el porcentaje de participacion que tiene un tercero
	//-Segun el cargo que se vaya a grabar
	//-------------------------------------------------------------------
	function datos_desde_tercero(codigo,n,auxclave)
	{
		
	
		// establece el tipo de participacion, si esta chequeado vale 1 de lo contrario vale 2
		if ($("#disponible_"+auxclave+"_"+n+":checked").length == 1)
				wparticipacion = 1;
		else
			    wparticipacion = 2;
		
		
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:      	'',
			wemp_pmla:         	$('#wemp_pmla').val(),
			accion:            	'datos_desde_tercero',
			wcodter:		   	codigo,
			wcodesp:		   	$("#busc_especialidades_"+auxclave+"_"+n).val(),
			wtip_paciente:	   	$('#wtip_paciente').val(),
			whora_cargo:	   	'',
			wfecha_cargo:      	'',
			wtipoempresa:		$("#tarifa_original_tal").val(),
			wtarifa:			$("#tarifa_original_tal").val(),
			wempresa:			$("#responsable_original_tal").val(),
			wcodcon:		   	$('#reconocido_clave'+auxclave+'_'+n+'_res1').attr('concepto'),
			wcco:				$("#id_tr_ppal_cobro_"+auxclave+"_"+n+"").attr('ccogra'),
			wcod_procedimiento:	$('#reconocido_clave'+auxclave+'_'+n+'_res1').attr('procedimiento'),
			wparticipacion:     wparticipacion,
			/*conDisponibilidad:	(($("#conDisponibilidad").is(":checked")) ? $("#conDisponibilidad").attr("codMedDisponible") : $("#conDisponibilidad").attr("codMedNoDisponible")),*/
			cuadroTurno:		''

		}, function (data) {
			if(data.error > 0)
				alert(data.mensaje);
			else
			{
			
				if($.trim(data.dobleCuadroDeTurno) == 1)
				{
					if(data.wporter!='')
					{
						$("#idporcentaje_"+auxclave+"_"+n+"").val(data.wporter+"%");
						$("#idporcentaje_"+auxclave+"_"+n+"").removeClass('campoObligatorio');
						$('#porter_'+auxclave+'_'+n).val(data.wporter);
						$("#busc_terceros_usuario_"+auxclave+"_"+n).attr('ctercero',codigo);
						$("#busc_terceros_usuario_"+auxclave+"_"+n).attr('ctercero_unix',data.wterunix);
					}
					if(data.wporter=='')
					{
						$("#idporcentaje_"+auxclave+"_"+n+"").val("No tiene porcentaje");
						$('#porter_'+auxclave+'_'+n).val(data.wporter);
						$("#busc_terceros_usuario_"+auxclave+"_"+n).attr('ctercero',codigo);
						$("#busc_terceros_usuario_"+auxclave+"_"+n).attr('ctercero_unix',data.wterunix);
						$("#idporcentaje_"+auxclave+"_"+n+"").addClass('campoObligatorio');
					}
				}
				else
				{
					if ($("#tipoCuadroTurno").attr("validar") == 'no')
					{
						if(data.wporter!='')
						{
							$("#idporcentaje_"+auxclave+"_"+n+"").val(data.wporter+"%");
							$("#idporcentaje_"+auxclave+"_"+n+"").removeClass('campoObligatorio');
							$('#porter_'+auxclave+'_'+n).val(data.wporter);
							$("#busc_terceros_usuario_"+auxclave+"_"+n).attr('ctercero',codigo);
							$("#busc_terceros_usuario_"+auxclave+"_"+n).attr('ctercero_unix',data.wterunix);
						}
						if(data.wporter=='')
						{
							$("#idporcentaje_"+auxclave+"_"+n+"").val("No tiene porcentaje");
							$('#porter_'+auxclave+'_'+n).val(data.wporter);
							$("#busc_terceros_usuario_"+auxclave+"_"+n).attr('ctercero',codigo);
							$("#busc_terceros_usuario_"+auxclave+"_"+n).attr('ctercero_unix',data.wterunix);
							$("#idporcentaje_"+auxclave+"_"+n+"").addClass('campoObligatorio');
						}
					}
					else
					{
						if(data.wporter!='')
						{
							$("#idporcentaje_"+auxclave+"_"+n+"").val(data.wporter+"%");
							$("#idporcentaje_"+auxclave+"_"+n+"").removeClass('campoObligatorio');
							$('#porter_'+auxclave+'_'+n).val(data.wporter);
							$("#busc_terceros_usuario_"+auxclave+"_"+n).attr('ctercero_unix',data.wterunix);
							$("#busc_terceros_usuario_"+auxclave+"_"+n).attr('ctercero',codigo);
						}
						if(data.wporter=='')
						{
							$("#idporcentaje_"+auxclave+"_"+n+"").val("No tiene porcentaje");
							$('#porter_'+auxclave+'_'+n).val(data.wporter);
							$("#busc_terceros_usuario_"+auxclave+"_"+n).attr('ctercero_unix',data.wterunix);
							$("#busc_terceros_usuario_"+auxclave+"_"+n).attr('ctercero',codigo);
							$("#idporcentaje_"+auxclave+"_"+n+"").addClass('campoObligatorio');
						}
					
					}
				}
			}

		},'json');
	}
	
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

	//--------------------------------------------------------------------------------------------------
	// Funcion para Validar si la cuenta se encuentra congelada, ya que si acurrio un cierre inesperado
	// del programa  la cuenta puede quedar congelada y no se puede permitir que graben cargos
	//--------------------------------------------------------------------------------------------------
	function validarEstadoDeCuentaCongelada(desdeSelectorConcepto)
	{
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   '',
			wemp_pmla:      $('#wemp_pmla').val(),
			accion:         'estadoCuentaCongelada',
			historia:		$("#whistoria").val(),
			ingreso:		$("#wing").val()
		}, function(info){

			// --> Si la cuenta se encuentra congelada
			if(info.Ecoest == 'on')
			{
				// --> si el usuario que la congelo es diferente al actual
				if(info.Ecousu != info.wuse)
				{
					// --> No se permiten grabar cargos, ya que la cuenta esta congelada por otro usuario
					var mensaje = 	'<br>'+
									' En este momento no se le pueden grabar cargos al paciente.<br>'+
									' La cuenta se encuentra congelada por <b>'+info.nomUsuario+'</b>'+
									', en un proceso de <b>liquidacion de '+info.Nomtip+'</b>.';

					// --> Mostrar mensaje
					$( '#divMsjCongelar').html(mensaje);
					$( '#divMsjCongelar').dialog({
						width:  500,
						dialogClass: 'fixed-dialog',
						modal: true,
						title: '<img width="15" height="15" src="../../images/medical/root/info.png" /> &nbsp;&nbsp;Mensaje',
						close: function( event, ui ) {
							if(desdeSelectorConcepto)
							{
								$("#busc_concepto_1").val('');
								$("#busc_concepto_1").attr('valor', '');
								$("#busc_concepto_1").attr("nombre", '');
								$("#busc_concepto_1").attr("polManejoTerceros", '');
							}
							else
								limpiarPantalla();
						}
					});
				}
				// --> Si es el mismo usuario que la congelo
				else
				{
					if(!desdeSelectorConcepto)
					{
						// --> Si el usuario la congelo desde un programa diferente al de cargos
						if(info.Ecotip != 'CA')
						{
							mensaje = "Usted tiene una liquidación de <b>"+info.Nomtip+"</b> en proceso.<br>Para conservar dicho proceso de Click en <b>Aceptar</b> y luego abra su programa correspondiente.<br>Si desea cancelar el proceso y poder grabarle cargos al paciente de Click en <b>Cancelar</b>.";
							$( '#divMsjCongelar').html(mensaje);
							$( '#divMsjCongelar').dialog({
								width:  680,
								dialogClass: 'fixed-dialog',
								modal: true,
								title: '<img width="15" height="15" src="../../images/medical/root/info.png" /> &nbsp;&nbsp;Mensaje',
								close: function( event, ui ) {
										//limpiarPantalla();
								},
								buttons:{
									"Aceptar": function() {
										$(this).dialog("close");
										cargarCuentaPaciente( )
									},
									Cancel: function() {
										//congelarCuentaPaciente('off');
										$(this).dialog("destroy");
									}
								 }
							});
						}
						// --> Si es desde el mismo programa de cargos que estaba congelada, entonces se descongela automaticamente
						else
						{
						//congelarCuentaPaciente('off');	
						}
							
					}
				}
			}
			// --> Si no esta congelada se congela
			else
			{
				if(desdeSelectorConcepto)
				{
					//congelarCuentaPaciente('on');
				}
					
			}
		}, 'json');
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

	//-------------------------------------------------------------------
	// Funcion para	Realiza la congelacion de la cuenta del paciente
	//-------------------------------------------------------------------
	function congelarCuentaPaciente(congelar)
	{

		var estadoActual = $("#cuentaCongelada").val();

		if($("#whistoria").val() != '' && $("#wing").val() != '' && estadoActual != congelar)
		{
			$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
			{
				consultaAjax:   '',
				wemp_pmla:      $('#wemp_pmla').val(),
				accion:         'congelarCuentaPaciente',
				historia:		$("#whistoria").val(),
				ingreso:		$("#wing").val(),
				congelar:		congelar
			}, function(data){
				$("#cuentaCongelada").val(congelar);
			});
		}
	}

	function cargarCuentaPaciente ()
	{
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
			{
				consultaAjax:   '',
				wemp_pmla:      $('#wemp_pmla').val(),
				accion:         'cargarCuentaPaciente',
				historia:		$("#whistoria").val(),
				ingreso:		$("#wing").val(),
			}, function(data){

			$('#div_liquidacion_pension').html(data);
			$( "#accordionPension" ).accordion("destroy");
			$('.tooltip').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
			$( "#accordionPension" ).accordion({
			collapsible: true,
			heightStyle: "content"
			});
			$('#wconcepto').val($('#wconceptoestanciaXparametro').val());

			//calculo de totales
				var totalr;
				var totale;
				var totalesinformato;
				var totalfinal;
				var cclave;
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
					totalfinal = formatearnumero(totalfinal);
					$("#input_total_"+cclave).html(totalfinal);
				});
				//-----------------

			});




	}


	function recalcular_estancia(clave)
	{
		var auxdias =  0;
		var auxval  =  0;

		var texto = $("#input_val_hab_"+clave).val() * $("#input_dias_"+clave).val();
		$("#td_total_"+clave).text(texto);


		//-actualiza total de dias
		$('[id^=input_dias_]').each(function (){
			auxdias = ((auxdias * 1) + ($(this).val() * 1));
		});
		$('#td_tot_dias').text(auxdias);
		//------------

		//-actuliza total general
		$('[id^=td_total_]').each(function (){
			auxval = ((auxval*1) + ($(this).text() * 1));
		});
		$('#td_tot_val').text(auxval);
		recalcular_reconocido(clave);
		recalcular_excedente(clave);
		//---------

	}
	
	
	function paciente_sin_alta_programada()
	{
		
		puedegrabarparcial = $("#permisoestaciaparcial").val();
		//alert(puedegrabarparcial);
		if(puedegrabarparcial=='on')
		{
			
			
		}
		else
		{
			jAlert("<span>No tiene permiso para grabar estancia parcial</span>", "Mensaje");
			return;
		}
		
		jConfirm('Desea grabar cargos de estancia hasta la fecha', '', function (answer) {
                        if (!answer)
                            return;
                        else 
						{
							grabar_pension('no');
						}
		});
		
		return;
		
	}
	//--------------------------------------------------------------------
	//-- Funcion que graba la pension 
	//-- se implementan algunas validaciones. 
	//-- 1 anuncia cuando se intenta grabar y hay una tarifa con valor de cero.
	//-- 2 Valida cuando hay un concepto compartido (que pide tercero) y no tiene asignado ninguno.
	//---------------------------------------------------------------------
	function grabar_pension(validar_alta)
	{
	
		var grabar = 'si';
		var clave;
		var ndia;
		var fechacargo;
		var ccogra;
		var wprocedimiento;
		var wvalor;
		var wnprocedimiento;
		var wresponsable;
		var wnresponsable;
		var wtarifa;
		var tipoEmpresa;
		var wnitEmpresa;
		var id_tope_afectado;
		var whora_ingreso;
		var whora_egreso;
		
		var ultimafecha;
		var ultimahora;
		$(".fecha_movimiento").each(function(){
			ultimafecha = $(this).attr('fecha');			
			ultimahora  = $(this).attr('hora');			
		});
		
		
		var validaciontarifa=0
		var claveaux;
		
		//---------------------------------
		//--Proceso de validacion
		//---------------------------------
		//-- se quita las clases de validacion y se reinicia todo para que no quede ninguna clase asociada a la ultima verificacion 
		$(".ppalreconocido").each(function (){
			claveaux = $(this).attr('clave');
			$("#habitacion_"+claveaux).removeClass('trrequerido-iab');
			$("#tdnumerodiasppal_"+claveaux).removeClass('trrequerido-ab');
			$("#td_excedente_"+claveaux).removeClass('trrequerido-ab');
			$("#input_total_"+claveaux).removeClass('trrequerido-dab');
			$("#td_reconocido_"+claveaux).removeClass('trrequerido-ab');
			$(".tdselecthabitacion_"+claveaux).removeClass('trrequerido-ab');
			$( "#tdfechainicialppal_"+claveaux).removeClass('trrequerido-a');
			$( "#tdfechainiciocobroppal_"+claveaux).removeClass('trrequerido-a');
			$( "#tdfechafinalppal_"+claveaux).removeClass('trrequerido-b');
			$( "#tdfechafinalcobroppal_"+claveaux).removeClass('trrequerido-b');
				
		});
		//-------------------------------
		
		//--------------------------------
		//--Se hace  la validacion para ver si el cobro es 0 , si es cero quiere decir que no existe la tarifa , o que se cobran 0 dias (en algunos casos las politicas pueden 
		//-- arrojar que se cobra cero dias) , si no hay tarifa la validacion tendra que pintar el recuadro del cargo en rojo
		/*$(".ppalreconocido").each(function (){
			if(($(this).val()*1)==0 && $(this).attr('numero_de_responsable')==1)
			{
				claveaux = $(this).attr('clave');
				// si el numero de dias es cero , el valor reconocido sera cero, por lo tanto se excluye de la validacion
				if(($("#input_dias_"+claveaux).val() * 1) == 0 )
				{
						  
				}
				else
				{
					//---------Asi se pone un tr con la clase requerido
					validaciontarifa=1;
					$("#habitacion_"+claveaux).addClass('trrequerido-iab');
					$("#tdnumerodiasppal_"+claveaux).addClass('trrequerido-ab');
					$("#td_excedente_"+claveaux).addClass('trrequerido-ab');
					$("#input_total_"+claveaux).addClass('trrequerido-dab');
					$(".td_valorPagoResponsable").addClass('trrequerido-ab');
					$(".tdselecthabitacion_"+claveaux).addClass('trrequerido-ab');
					$( "#tdfechainicialppal_"+claveaux).addClass('trrequerido-a');
					$( "#tdfechainiciocobroppal_"+claveaux).addClass('trrequerido-a');
					$( "#tdfechafinalppal_"+claveaux).addClass('trrequerido-b');
					$( "#tdfechafinalcobroppal_"+claveaux).addClass('trrequerido-b');
					//------------------------------------------------------------

				}
			}
			
			
						
		});*/
		
	
		//-----------------------------------------------------------------
		
		//-- validacion de cargos sin tarifa
		//-- Mensaje de cargos sin tarifa
		if(validaciontarifa==1)
		{
			jAlert("<span>Hay días de estancia sin tarifa</span>", "Mensaje");
			return;
		}
		
		//--Validacion de cargos compartidos sin tercero
		//--Se valida si el cargo pide tercero y si este tercero esta lleno, tambien se valida si el tercero trae porcentaje, si no , se pinta en rojo
		var tieneporcentaje = 0;
		var claveaux;
		$(".classporcentaje").each(function (){
			claveaux = $(this).attr('auxclave');
			if($(this).val() =='No tiene porcentaje')
			{
				
				tieneporcentaje++;
				if(!$("#habitacion_"+claveaux).hasClass('trrequerido-iab'))
				{
					$("#habitacion_"+claveaux).addClass('trrequerido-iab');
					$("#tdnumerodiasppal_"+claveaux).addClass('trrequerido-ab');
					$("#td_excedente_"+claveaux).addClass('trrequerido-ab');
					$("#input_total_"+claveaux).addClass('trrequerido-dab');
					$("#td_reconocido_"+claveaux).addClass('trrequerido-ab');
					$(".tdselecthabitacion_"+claveaux).addClass('trrequerido-ab');
					$( "#tdfechainicialppal_"+claveaux).addClass('trrequerido-a');
					$( "#tdfechainiciocobroppal_"+claveaux).addClass('trrequerido-a');
					$( "#tdfechafinalppal_"+claveaux).addClass('trrequerido-b');
					$( "#tdfechafinalcobroppal_"+claveaux).addClass('trrequerido-b');
				}
					
			}
			
		});
		//------------------------------------------------------------------------------
		
		//--Mensaje de cargo compartido sin tercero
		if(tieneporcentaje > 0)
		{
			jAlert("<span >Hay un concepto con tercero sin porcentaje</span>", "Mensaje");
			return;
		}
		//---------------------------------------
		//---------------------------------------
		
		//--------
		// validacion de alta programada (si el paciente no tiene aun alta programada, no se puede liquidar la pension)
		
		if (validar_alta =='si')
		{
			if($("#altaprogramada").val()=='off')
			{
				paciente_sin_alta_programada();
				return;
			}
		}
		
		//--Fin de validaciones.
		
		//---Se pone el boton de grabacion deshabilitado
		$("#boton_grabar").html('&nbsp;<img class="" border="0" src="../../images/medical/ajax-loader2.gif" title="Cargando.." >').attr("disabled","disabled");
		$("#numero_responsables").val();
		//----------------------------------------------
		
		//--------------------------------------------------------
		//--Proceso de contruccion de vectores para la grabacion
		//---------------------------------------------------------
		//--Se estan construyendo tres vecotres uno  que se llama datos , que es el principal se usa para grabar los cargos , en este vector estan los cargos resumidos por cada traslado, sumandose
		//--los dias y los valores a pagar (en conclusion se suman los dias y los valores cuando el paciente esta en la misma habitacion)
		//--El segundo vector datosaux contiene los cargos detallados dia a dia, se usa con el fin de validar en unix todas las politicas y comprobar si tiene tarifa y si el tercero tiene relacion
		//--con el concepto en la tabla de honorarios de unix.
		//--El tercer vector contiene  datosauxfinal  es muy parecido a datos  pero  tiene en cuenta los terceros que intervinieron  cuando el paciente estuvo en la misma habitacion asi , si el paciente
		//--se le graban cargos de estancia sin tercero, o donde el tercero no varia por habitacion , el vector datosauxfinal seria igual a datos , pero si durante la estancia por ejemplo estuvo 5 dias en una habitacion de cuidados intensivos 
		//--centro de costos neonatos, ahi se cobra con tercero y si el tercero cambio dos dias para el tercero X y 3 para el Y entonces el cargo se parte en dos.
		
		//-- se cuenta en total los dias de estancia
		var diasauxiliar = 0;
		var auxiliarexcedente=0;
		var tiene_paquete = false;
		$(".cobroxdia").each(function ()
		{
			//alert($(this).attr("nosecobraporpaquete"));
			if($(this).attr("nosecobraporpaquete")=='si')
			{
				paqclave = $(this).attr('clave');
				$("#detalle_"+clave).hide();
				paqndia = $(this).attr('ndia');
				auxiliarexcedente = (auxiliarexcedente *1) + ($('#excedente_'+paqclave+'_'+paqndia).attr('valor') *1);
				tiene_paquete = true;
			}
			else
			{	
				diasauxiliar++;
			}
		});
		
	
		//-------------
		var datos = new Array();//array que contiene toda la informacion de los registros a grabar , agrupados por los dias que el paciente estuvo en la habitacion , este array es basicamente para grabar
		var datosaux = new Array();//array que contiene toda la informacion de los datos dia a dia , durante el tiempo que el paciente permanecio en la estancia, este array es para validar diariamente la cirugia
		var datosauxfinal = new Array();//array que contiene toda la informacion de los datos dia a dia , durante el tiempo que el paciente permanecio en la estancia, este array es para validar diariamente la cirugia
		var datosauxfinal2 = new Array();//array que contiene toda la informacion de los datos dia a dia , durante el tiempo que el paciente permanecio en la estancia, este array es para validar diariamente la cirugia
		var datos_dos = new Array();//array que contiene toda la informacion de los datos dia a dia , durante el tiempo que el paciente permanecio en la estancia, este array es para validar diariamente la cirugia
		var auxclave;
		var auxtercero ='';
		var diasauxiliar1 = 0;
		var diasauxiliar2 = 0;
		var contadore=0;
		$(".cobroxdia").each(function ()
		{
			
			clave = $(this).attr('clave');
			$("#detalle_"+clave).hide();
		
			ndia = $(this).attr('ndia');
			fechacargo = $("#fechacargo_"+clave+"_"+ndia).attr("valor");
			whora_ingreso = $("#fechacargo_"+clave+"_"+ndia).attr("hora_ingreso");
			whora_egreso = $("#fechacargo_"+clave+"_"+ndia).attr("hora_egreso");
			ccogra = $(this).attr('ccogra');
			aux_id_grabado='';
			var wtercero ='';
			var wnomtercero ='';
			var auxwtercero ='';

			for(j=$("#numero_responsables").val() ; j>=1; j--)
			{
				
				wprocedimiento = "";
				wnprocedimiento = "";
				wnumerohab = "";
				wvalor = "";
				wresponsable="";
				wnresponsable="";
				wtarifa="";
				id_tope_afectado="";
				if( (($('#reconocido_clave'+clave+'_'+ndia+'_res'+j).attr('valor') * 1)!=0 || $('#reconocido_clave'+clave+'_'+ndia+'_res'+j).attr('paf')=='si' || $('#reconocido_clave'+clave+'_'+ndia+'_res'+j).attr('escomplementario')=='si')   &&  $('#reconocido_clave'+clave+'_'+ndia+'_res'+j).length)
				{
					
					
					wnprocedimiento = $('#reconocido_clave'+clave+'_'+ndia+'_res'+j).attr('nombre_hab');
					wnumerohab =	$('#habitacion_'+clave).attr('numero');
					wvalor = ($('#valhab_clave'+clave+'_'+ndia+'_res'+j).attr('valor') *1);
					
					wreconocido = ($('#reconocido_clave'+clave+'_'+ndia+'_res'+j).attr('valor') * 1);
					wprocedimiento = $('#reconocido_clave'+clave+'_'+ndia+'_res'+j).attr('procedimiento');
					wresponsable = $('#reconocido_clave'+clave+'_'+ndia+'_res'+j).attr('responsable');
					
			
					wnresponsable = $('#reconocido_clave'+clave+'_'+ndia+'_res'+j).attr('nresponsable');
					wtarifa = $('#reconocido_clave'+clave+'_'+ndia+'_res'+j).attr('tarifa');
					tipoEmpresa = $('#reconocido_clave'+clave+'_'+ndia+'_res'+j).attr('tresponsable');
					nitEmpresa = $('#reconocido_clave'+clave+'_'+ndia+'_res'+j).attr('nitresponsable');
					concepto_cargo = $('#reconocido_clave'+clave+'_'+ndia+'_res'+j).attr('concepto');
					wnconcepto =  $('#reconocido_clave'+clave+'_'+ndia+'_res'+j).attr('nconcepto');
					id_tope_afectado = $('#reconocido_clave'+clave+'_'+ndia+'_res'+j).attr('id_tope_afectado');

					//--
					//--Proceso para saber el tercero que corresponde a cada cargo de pension ; si es que se tiene
					if ($('#busc_terceros_usuario_'+clave+'_'+ndia).attr('contercero') =='si')
					{
						if($('#porter_'+clave+'_'+ndia).length)
						{
							wtercero 				 =  $('#busc_terceros_usuario_'+clave+'_'+ndia).attr('ctercero');
							wnomtercero				 =  $('#busc_terceros_usuario_'+clave+'_'+ndia).attr('nombre');
							wtercero_especialidad	 =  $('#busc_terceros_usuario_'+clave+'_'+ndia).attr('cespecialidad');
							wgraba_varios_terceros 	 = 0;
							wporter = $('#porter_'+clave+'_'+ndia).val();
							wtercero_unix 			 = $('#busc_terceros_usuario_'+clave+'_'+ndia).attr('ctercero_unix');							
							
						}
					}
					else
					{
						
						wtercero 				= '';
						wnomtercero			 	= '';
						wgraba_varios_terceros 	= 0;
						wtercero_especialidad	= '';
						wporter = '';
						wtercero_unix 			= '';

					}
					//------------------------------------------------------
					aux = (j*1)+1;

					if($('#reconocido_clave'+clave+'_'+ndia+'_res'+aux).length && ($('#reconocido_clave'+clave+'_'+ndia+'_res'+aux).attr('valor') * 1)!=0 )
					{
						wparalelo = $('#reconocido_clave'+clave+'_'+ndia+'_res'+aux).attr('paralelo');
						wvaltarExce='';

					}
					else
					{
						wparalelo ='off';
						if(!$('#excedente_'+clave+'_'+ndia).attr('valor'))
						{
							wvaltarExce =0;
						}
						else
						{
							wvaltarExce=$('#excedente_'+clave+'_'+ndia).attr('valor');
							if(tiene_paquete)
							{
								
								wvaltarExce= (wvaltarExce*1) + (auxiliarexcedente*1);
								tiene_paquete=false;
							}
						}
					}

					dato_clave = clave;
					dato_responsable = wresponsable;
					var existe = false;
						

					//-- Se  hace el arreglo  que contendra los cargos de pension unificados
					//-- Proceso para unificar los cargos por cada traslado y por cada responsable que tenga
					for(jj=0;jj<datos.length;jj++){
						var datoget = datos[jj];
						if(datoget['clave'] == dato_clave && datoget['wresponsable']==dato_responsable ){
							if ($('#busc_terceros_usuario_'+clave+'_'+ndia).attr('contercero') =='si')
							{
									datoget['ndia'] 			= ((datoget['ndia'])*1) + 1;
									datoget['whora_ingreso'] 	= datoget['whora_ingreso'];
									datoget['whora_egreso']		= whora_egreso;
									datoget['wfecha_ingreso']	= datoget['fechacargo'];
									datoget['wfecha_egreso']	= fechacargo;
									datoget['wreconocido']		= ((datoget['wreconocido']*1) + (wreconocido*1))*1;
									existe = true;
									
									datos[jj] = datoget;
								
							}
							else
							{
									datoget['ndia'] 			= ((datoget['ndia'])*1) + 1;
									datoget['whora_ingreso'] 	= datoget['whora_ingreso'];
									datoget['whora_egreso']		= whora_egreso;
									datoget['wfecha_ingreso']	= datoget['fechacargo'];
									datoget['wfecha_egreso']	= fechacargo;
									datoget['wreconocido']		= ((datoget['wreconocido']*1) + (wreconocido*1))*1;
									existe = true;
									
									datos[jj] = datoget;
								
							}
							
						}
						
					}
					//-------------
					//----------------------------
						
					//-- Aqui se construye el array principal inicial , este array contiene los datos de pension resumidos por traslado, pero
					//-- no tenia en cuenta si en estos dias habian varios terceros y se tenia que partir el cargo, por esto se hizo otro vector mas abajo
					//-- datosauxfinal 
					if( existe == false ){
						var dato 						= new Object();
						dato['clave'] 					= clave;
						dato['ndia']					= 1 ;
						dato['fechacargo']				= fechacargo;
						dato['whora_ingreso']			= whora_ingreso;
						dato['whora_egreso']			= whora_egreso;
						dato['ccogra']					= ccogra;
						dato['wnprocedimiento']			= wnprocedimiento;
						dato['wnumerohab']				= wnumerohab;
						dato['wvalor']					= wvalor;
						dato['wreconocido']				= wreconocido;
						dato['wprocedimiento']			= wprocedimiento;
						dato['wresponsable']			= wresponsable;
						dato['wnresponsable']			= wnresponsable;
						dato['wtarifa']					= wtarifa;
						dato['tipoEmpresa']				= tipoEmpresa;
						dato['nitEmpresa']				= nitEmpresa;
						dato['concepto_cargo']			= concepto_cargo;
						dato['wnconcepto']				= wnconcepto;
						dato['id_tope_afectado']		= id_tope_afectado;
						dato['wtercero']				= wtercero;
						dato['wtercero_nombre']			= wnomtercero;
						dato['wtercero_unix']			= wtercero_unix;
						dato['wtercero_especialidad']	= wtercero_especialidad;
						dato['wgraba_varios_terceros']	= wgraba_varios_terceros;
						dato['wporter']					= wporter;
						dato['wparalelo']				= wparalelo;
						dato['wvaltarExce']				= wvaltarExce;
						dato['wfecha_ingreso']			= fechacargo;
						dato['wfecha_egreso']			= fechacargo;
						datos.push(dato);
					}
					//-------------------------------------------------------
						
						
					// se llena el objeto datoaux
					//-- este vector contendra todos los cargos dia por dia, detallado , no resumido, para validar en unix si tiene tarifa, o si
					//-- el tercero tiene concepto amarrado a los honorarios
					var datoaux 						= new Object();
					datoaux['clave'] 					= clave;
					datoaux['ndia']						= 1 ;
					datoaux['fechacargo']				= fechacargo;
					datoaux['whora_ingreso']			= whora_ingreso;
					datoaux['whora_egreso']				= whora_egreso;
					datoaux['ccogra']					= ccogra;
					datoaux['wnprocedimiento']			= wnprocedimiento;
					datoaux['wnumerohab']				= wnumerohab;
					datoaux['wvalor']					= wvalor;
					datoaux['wreconocido']				= wreconocido;
					datoaux['wprocedimiento']			= wprocedimiento;
					datoaux['wresponsable']				= wresponsable;
					datoaux['wnresponsable']			= wnresponsable;
					datoaux['wtarifa']					= wtarifa;
					datoaux['tipoEmpresa']				= tipoEmpresa;
					datoaux['nitEmpresa']				= nitEmpresa;
					datoaux['concepto_cargo']			= concepto_cargo;
					datoaux['wnconcepto']				= wnconcepto;
					datoaux['id_tope_afectado']			= id_tope_afectado;
					datoaux['wtercero']					= wtercero;
					datoaux['wtercero_nombre']			= wnomtercero;
					datoaux['wtercero_unix']			= wtercero_unix;
					datoaux['wtercero_especialidad']	= wtercero_especialidad;
					datoaux['wgraba_varios_terceros']	= wgraba_varios_terceros;
					datoaux['wporter']					= wporter;
					datoaux['wparalelo']				= wparalelo;
					datoaux['wvaltarExce']				= wvaltarExce;
					datoaux['wfecha_ingreso']			= fechacargo;
					datoaux['wfecha_egreso']			= fechacargo;
					datosaux.push(datoaux);
					//-------------------------------------------------------------
					
					if($("#numero_responsables").val() == 1)
					{
						contadore++;
					
						//-Se inicia proceso para crear un vector discriminando  cargos por cada tercero
						//------------------------------------------------
						if(diasauxiliar1 == 0)
						{
							
							auxclave = clave;
							
							datoauxfinal_clave					= '';
							datoauxfinal_ndia					= '';
							datoauxfinal_fechacargo				= fechacargo;
							datoauxfinal_whora_ingreso			= whora_ingreso;
							datoauxfinal_whora_egreso			= '';
							datoauxfinal_ccogra					= '';
							datoauxfinal_wnprocedimiento		= '';
							datoauxfinal_wnumerohab				= '';
							datoauxfinal_wvalor					= '';
							datoauxfinal_wreconocido			= '';
							datoauxfinal_wprocedimiento			= '';
							datoauxfinal_wresponsable			= '';
							datoauxfinal_wnresponsable			= '';
							datoauxfinal_wtarifa				= '';
							datoauxfinal_tipoEmpresa			= '';
							datoauxfinal_nitEmpresa				= '';
							datoauxfinal_concepto_cargo			= '';
							datoauxfinal_wnconcepto				= '';
							datoauxfinal_id_tope_afectado		= '';
							datoauxfinal_wtercero				= '';
							datoauxfinal_wtercero_nom			= '';
							datoauxfinal_wtercero_unix			= '';
							datoauxfinal_wtercero_especialidad	= '';
							datoauxfinal_wgraba_varios_terceros	= '';
							datoauxfinal_wporter				= '';
							datoauxfinal_wparalelo				= '';
							datoauxfinal_wvaltarExce			= '';
							datoauxfinal_wfecha_ingreso			= fechacargo;
							datoauxfinal_wfecha_egreso			= '';
						}	
						
						if( clave == auxclave )
						{
							
							if(datoaux['wtercero'] == auxtercero  )
							{
								
									datoauxfinal_clave					= clave;
									datoauxfinal_ndia					= ((datoauxfinal_ndia * 1) + 1);
									datoauxfinal_fechacargo				= datoauxfinal_fechacargo;
									datoauxfinal_whora_ingreso			= datoauxfinal_whora_ingreso;
									datoauxfinal_whora_egreso			= whora_egreso;
									datoauxfinal_ccogra					= ccogra;
									datoauxfinal_wnprocedimiento		= wnprocedimiento;
									datoauxfinal_wnumerohab				= wnumerohab;
									datoauxfinal_wvalor					= wvalor;
									datoauxfinal_wreconocido			= ((datoauxfinal_wreconocido * 1) + (wreconocido*1))*1;
									datoauxfinal_wprocedimiento			= wprocedimiento;
									datoauxfinal_wresponsable			= wresponsable;
									datoauxfinal_wnresponsable			= wnresponsable;
									datoauxfinal_wtarifa				= wtarifa;
									datoauxfinal_tipoEmpresa			= tipoEmpresa;
									datoauxfinal_nitEmpresa				= nitEmpresa;
									datoauxfinal_concepto_cargo			= concepto_cargo;
									datoauxfinal_wnconcepto				= wnconcepto;
									datoauxfinal_id_tope_afectado		= id_tope_afectado;
									datoauxfinal_wtercero				= wtercero;
									datoauxfinal_wtercero_nom			= wnomtercero;
									datoauxfinal_wtercero_unix			= wtercero_unix;
									datoauxfinal_wtercero_especialidad	= wtercero_especialidad;
									datoauxfinal_wgraba_varios_terceros	= wgraba_varios_terceros;
									datoauxfinal_wporter				= wporter;
									datoauxfinal_wparalelo				= wparalelo;
									datoauxfinal_wvaltarExce			= ((datoauxfinal_wvaltarExce * 1) + (wvaltarExce*1))*1;
									datoauxfinal_wfecha_ingreso			= datoauxfinal_wfecha_ingreso;
									datoauxfinal_wfecha_egreso			= fechacargo;
								
							}
							else
							{
								
								var datoauxfinal 	= new Object();
								datoauxfinal['clave'] = datoauxfinal_clave;
								datoauxfinal['ndia'] = datoauxfinal_ndia;
								datoauxfinal['fechacargo'] = datoauxfinal_fechacargo	;
								datoauxfinal['whora_ingreso'] = datoauxfinal_whora_ingreso;
								datoauxfinal['whora_egreso'] = datoauxfinal_whora_egreso;
								datoauxfinal['ccogra']	 = datoauxfinal_ccogra	;
								datoauxfinal['wnprocedimiento'] = datoauxfinal_wnprocedimiento;
								datoauxfinal['wnumerohab'] = datoauxfinal_wnumerohab	;
								datoauxfinal['wvalor'] = datoauxfinal_wvalor;
								datoauxfinal['wreconocido'] = datoauxfinal_wreconocido;
								datoauxfinal['wprocedimiento'] = datoauxfinal_wprocedimiento;
								datoauxfinal['wresponsable'] = datoauxfinal_wresponsable;
								datoauxfinal['wnresponsable'] = datoauxfinal_wnresponsable;
								datoauxfinal['wtarifa'] = datoauxfinal_wtarifa;
								datoauxfinal['tipoEmpresa']	 = datoauxfinal_tipoEmpresa;
								datoauxfinal['nitEmpresa'] = datoauxfinal_nitEmpresa;
								datoauxfinal['concepto_cargo'] = datoauxfinal_concepto_cargo;
								datoauxfinal['wnconcepto']	 = datoauxfinal_wnconcepto;
								datoauxfinal['id_tope_afectado'] = datoauxfinal_id_tope_afectado;
								datoauxfinal['wtercero'] = datoauxfinal_wtercero;
								datoauxfinal['wtercero_nombre'] = datoauxfinal_wtercero_nom;
								datoauxfinal['wtercero_unix'] = datoauxfinal_wtercero_unix;
								datoauxfinal['wtercero_especialidad'] = datoauxfinal_wtercero_especialidad;
								datoauxfinal['wgraba_varios_terceros'] = datoauxfinal_wgraba_varios_terceros;
								datoauxfinal['wporter'] = datoauxfinal_wporter;
								datoauxfinal['wparalelo'] = datoauxfinal_wparalelo;
								datoauxfinal['wvaltarExce'] = datoauxfinal_wvaltarExce;
								datoauxfinal['wfecha_ingreso'] = datoauxfinal_wfecha_ingreso;
								datoauxfinal['wfecha_egreso'] = datoauxfinal_wfecha_egreso;
								
								//-- se hace push
								console.log("push 9");
								datosauxfinal.push(datoauxfinal);
								
								//-- se inicializan de nuevo las variables
								datoauxfinal_clave					= clave;
								datoauxfinal_ndia					= 1 ;
								datoauxfinal_fechacargo				= fechacargo;
								datoauxfinal_whora_ingreso			= whora_ingreso;
								datoauxfinal_whora_egreso			= whora_egreso;
								datoauxfinal_ccogra					= ccogra;
								datoauxfinal_wnprocedimiento		= wnprocedimiento;
								datoauxfinal_wnumerohab				= wnumerohab;
								datoauxfinal_wvalor					= wvalor;
								datoauxfinal_wreconocido			= wreconocido ;
								datoauxfinal_wprocedimiento			= wprocedimiento;
								datoauxfinal_wresponsable			= wresponsable;
								datoauxfinal_wnresponsable			= wnresponsable;
								datoauxfinal_wtarifa				= wtarifa;
								datoauxfinal_tipoEmpresa			= tipoEmpresa;
								datoauxfinal_nitEmpresa				= nitEmpresa;
								datoauxfinal_concepto_cargo			= concepto_cargo;
								datoauxfinal_wnconcepto				= wnconcepto;
								datoauxfinal_id_tope_afectado		= id_tope_afectado;
								datoauxfinal_wtercero				= wtercero;
								datoauxfinal_wtercero_nom			= wnomtercero;
								datoauxfinal_wtercero_unix			= wtercero_unix;
								datoauxfinal_wtercero_especialidad	= wtercero_especialidad;
								datoauxfinal_wgraba_varios_terceros	= wgraba_varios_terceros;
								datoauxfinal_wporter				= wporter;
								datoauxfinal_wparalelo				= wparalelo;
								datoauxfinal_wvaltarExce			= wvaltarExce;
								datoauxfinal_wfecha_ingreso			= fechacargo;
								datoauxfinal_wfecha_egreso			= fechacargo;
								
							}
											
						}
						else
						{
							
							var datoauxfinal = new Object();
							datoauxfinal['clave'] = datoauxfinal_clave;
							datoauxfinal['ndia'] = datoauxfinal_ndia;
							datoauxfinal['fechacargo'] = datoauxfinal_fechacargo	;
							datoauxfinal['whora_ingreso'] = datoauxfinal_whora_ingreso;
							datoauxfinal['whora_egreso'] = datoauxfinal_whora_egreso;
							datoauxfinal['ccogra']	 = datoauxfinal_ccogra	;
							datoauxfinal['wnprocedimiento'] = datoauxfinal_wnprocedimiento;
							datoauxfinal['wnumerohab'] = datoauxfinal_wnumerohab	;
							datoauxfinal['wvalor'] = datoauxfinal_wvalor;
							datoauxfinal['wreconocido'] = datoauxfinal_wreconocido;
							datoauxfinal['wprocedimiento'] = datoauxfinal_wprocedimiento;
							datoauxfinal['wresponsable'] = datoauxfinal_wresponsable;
							datoauxfinal['wnresponsable'] = datoauxfinal_wnresponsable;
							datoauxfinal['wtarifa'] = datoauxfinal_wtarifa;
							datoauxfinal['tipoEmpresa']	 = datoauxfinal_tipoEmpresa;
							datoauxfinal['nitEmpresa'] = datoauxfinal_nitEmpresa;
							datoauxfinal['concepto_cargo'] = datoauxfinal_concepto_cargo;
							datoauxfinal['wnconcepto']	 = datoauxfinal_wnconcepto;
							datoauxfinal['id_tope_afectado'] = datoauxfinal_id_tope_afectado;
							datoauxfinal['wtercero'] = datoauxfinal_wtercero;
							datoauxfinal['wtercero_nombre'] = datoauxfinal_wtercero_nom;
							datoauxfinal['wtercero_unix'] = datoauxfinal_wtercero_unix;
							datoauxfinal['wtercero_especialidad'] = datoauxfinal_wtercero_especialidad;
							datoauxfinal['wgraba_varios_terceros'] = datoauxfinal_wgraba_varios_terceros;
							datoauxfinal['wporter'] = datoauxfinal_wporter;
							datoauxfinal['wparalelo'] = datoauxfinal_wparalelo;
							datoauxfinal['wvaltarExce'] = datoauxfinal_wvaltarExce;
							datoauxfinal['wfecha_ingreso'] = datoauxfinal_wfecha_ingreso;
							datoauxfinal['wfecha_egreso'] = datoauxfinal_wfecha_egreso;
							//-- se hace push
						    console.log("push 8");
							datosauxfinal.push(datoauxfinal);
							//-- se inicializan de nuevo las variables
							datoauxfinal_clave					= clave;
							datoauxfinal_ndia					= 1 ;
							datoauxfinal_fechacargo				= fechacargo;
							datoauxfinal_whora_ingreso			= whora_ingreso;
							datoauxfinal_whora_egreso			= whora_egreso;
							datoauxfinal_ccogra					= ccogra;
							datoauxfinal_wnprocedimiento		= wnprocedimiento;
							datoauxfinal_wnumerohab				= wnumerohab;
							datoauxfinal_wvalor					= wvalor;
							datoauxfinal_wreconocido			= wreconocido ;
							datoauxfinal_wprocedimiento			= wprocedimiento;
							datoauxfinal_wresponsable			= wresponsable;
							datoauxfinal_wnresponsable			= wnresponsable;
							datoauxfinal_wtarifa				= wtarifa;
							datoauxfinal_tipoEmpresa			= tipoEmpresa;
							datoauxfinal_nitEmpresa				= nitEmpresa;
							datoauxfinal_concepto_cargo			= concepto_cargo;
							datoauxfinal_wnconcepto				= wnconcepto;
							datoauxfinal_id_tope_afectado		= id_tope_afectado;
							datoauxfinal_wtercero				= wtercero;
							datoauxfinal_wtercero_nom			= wnomtercero;
							datoauxfinal_wtercero_unix			= wtercero_unix;
							datoauxfinal_wtercero_especialidad	= wtercero_especialidad;
							datoauxfinal_wgraba_varios_terceros	= wgraba_varios_terceros;
							datoauxfinal_wporter				= wporter;
							datoauxfinal_wparalelo				= wparalelo;
							datoauxfinal_wvaltarExce			= wvaltarExce;
							datoauxfinal_wfecha_ingreso			= fechacargo;
							datoauxfinal_wfecha_egreso			= fechacargo;
						
						}
						
						diasauxiliar1++;
						if(diasauxiliar1 == diasauxiliar)
						{
							
							var datoauxfinal = new Object();
							datoauxfinal['clave'] = datoauxfinal_clave;
							datoauxfinal['ndia'] = datoauxfinal_ndia;
							datoauxfinal['fechacargo'] = datoauxfinal_fechacargo	;
							datoauxfinal['whora_ingreso'] = datoauxfinal_whora_ingreso;
							datoauxfinal['whora_egreso'] = datoauxfinal_whora_egreso;
							datoauxfinal['ccogra']	 = datoauxfinal_ccogra	;
							datoauxfinal['wnprocedimiento'] = datoauxfinal_wnprocedimiento;
							datoauxfinal['wnumerohab'] = datoauxfinal_wnumerohab	;
							datoauxfinal['wvalor'] = datoauxfinal_wvalor;
							datoauxfinal['wreconocido'] = datoauxfinal_wreconocido;
							datoauxfinal['wprocedimiento'] = datoauxfinal_wprocedimiento;
							datoauxfinal['wresponsable'] = datoauxfinal_wresponsable;
							datoauxfinal['wnresponsable'] = datoauxfinal_wnresponsable;
							datoauxfinal['wtarifa'] = datoauxfinal_wtarifa;
							datoauxfinal['tipoEmpresa']	 = datoauxfinal_tipoEmpresa;
							datoauxfinal['nitEmpresa'] = datoauxfinal_nitEmpresa;
							datoauxfinal['concepto_cargo'] = datoauxfinal_concepto_cargo;
							datoauxfinal['wnconcepto']	 = datoauxfinal_wnconcepto;
							datoauxfinal['id_tope_afectado'] = datoauxfinal_id_tope_afectado;
							datoauxfinal['wtercero'] = datoauxfinal_wtercero;
							datoauxfinal['wtercero_nombre'] = datoauxfinal_wtercero_nom;
							datoauxfinal['wtercero_unix'] = datoauxfinal_wtercero_unix;
							datoauxfinal['wtercero_especialidad'] = datoauxfinal_wtercero_especialidad;
							datoauxfinal['wgraba_varios_terceros'] = datoauxfinal_wgraba_varios_terceros;
							datoauxfinal['wporter'] = datoauxfinal_wporter;
							datoauxfinal['wparalelo'] = datoauxfinal_wparalelo;
							datoauxfinal['wvaltarExce'] = datoauxfinal_wvaltarExce;
							datoauxfinal['wfecha_ingreso'] = datoauxfinal_wfecha_ingreso;
							datoauxfinal['wfecha_egreso'] = datoauxfinal_wfecha_egreso;
							console.log("push 7");
							datosauxfinal.push(datoauxfinal);
							
						}
						//Fin de construccion de vector unificado, detallando dias de cobro para cada tercero-----------------------------------------------
						//---------------------------------------------
						auxclave = clave;
						auxtercero = datoaux['wtercero'];
					
					}
					else if($("#numero_responsables").val() == 2)
					{
						
						if(j==1)
						{
							if(diasauxiliar1 == 0)
							{
								
							
								auxclave=clave;
								
								datoauxfinal_clave					= '';
								datoauxfinal_ndia					= '';
								datoauxfinal_fechacargo				= fechacargo;
								datoauxfinal_whora_ingreso			= whora_ingreso;
								datoauxfinal_whora_egreso			= '';
								datoauxfinal_ccogra					= '';
								datoauxfinal_wnprocedimiento		= '';
								datoauxfinal_wnumerohab				= '';
								datoauxfinal_wvalor					= '';
								datoauxfinal_wreconocido			= '';
								datoauxfinal_wprocedimiento			= '';
								datoauxfinal_wresponsable			= '';
								datoauxfinal_wnresponsable			= '';
								datoauxfinal_wtarifa				= '';
								datoauxfinal_tipoEmpresa			= '';
								datoauxfinal_nitEmpresa				= '';
								datoauxfinal_concepto_cargo			= '';
								datoauxfinal_wnconcepto				= '';
								datoauxfinal_id_tope_afectado		= '';
								datoauxfinal_wtercero				= '';
								datoauxfinal_wtercero_nom			= '';
								datoauxfinal_wtercero_unix			= '';
								datoauxfinal_wtercero_especialidad	= '';
								datoauxfinal_wgraba_varios_terceros	= '';
								datoauxfinal_wporter				= '';
								datoauxfinal_wparalelo				= '';
								datoauxfinal_wvaltarExce			= '';
								datoauxfinal_wfecha_ingreso			= fechacargo;
								datoauxfinal_wfecha_egreso			= '';
							}
						}
						if(j==2)
						{
							if(diasauxiliar2 == 0)
							{
							
								
								auxclave=clave;
								datoauxfinal_clave2					= '';
								datoauxfinal_ndia2					= '';
								datoauxfinal_fechacargo2				= fechacargo;
								datoauxfinal_whora_ingreso2			= whora_ingreso;
								datoauxfinal_whora_egreso2			= '';
								datoauxfinal_ccogra2					= '';
								datoauxfinal_wnprocedimiento2		= '';
								datoauxfinal_wnumerohab2				= '';
								datoauxfinal_wvalor2					= '';
								datoauxfinal_wreconocido2			= '';
								datoauxfinal_wprocedimiento2			= '';
								datoauxfinal_wresponsable2			= '';
								datoauxfinal_wnresponsable2			= '';
								datoauxfinal_wtarifa2				= '';
								datoauxfinal_tipoEmpresa2			= '';
								datoauxfinal_nitEmpresa2				= '';
								datoauxfinal_concepto_cargo2			= '';
								datoauxfinal_wnconcepto2				= '';
								datoauxfinal_id_tope_afectado2		= '';
								datoauxfinal_wtercero2				= '';
								datoauxfinal_wtercero_nom2			= '';
								datoauxfinal_wtercero_unix2			= '';
								datoauxfinal_wtercero_especialidad2	= '';
								datoauxfinal_wgraba_varios_terceros2	= '';
								datoauxfinal_wporter2				= '';
								datoauxfinal_wparalelo2				= '';
								datoauxfinal_wvaltarExce2			= '';
								datoauxfinal_wfecha_ingreso2			= fechacargo;
								datoauxfinal_wfecha_egreso2			= '';
							}
						}
						
						console.log("responsable: "+j+ " clave: "+clave+"---auxclave: "+auxclave);
						if( clave == auxclave )
						{
							
							
							if(j==1)
							{
								
								if(datoaux['wtercero'] == auxtercero  )
								{
										
										
										
										//alert("entro responsable");
										datoauxfinal_clave					= clave;
										datoauxfinal_ndia					= ((datoauxfinal_ndia * 1) + 1);
										//alert(datoauxfinal_ndia);
										datoauxfinal_fechacargo				= datoauxfinal_fechacargo;
										datoauxfinal_whora_ingreso			= datoauxfinal_whora_ingreso;
										datoauxfinal_whora_egreso			= whora_egreso;
										datoauxfinal_ccogra					= ccogra;
										datoauxfinal_wnprocedimiento		= wnprocedimiento;
										datoauxfinal_wnumerohab				= wnumerohab;
										datoauxfinal_wvalor					= wvalor;
										datoauxfinal_wreconocido			= ((datoauxfinal_wreconocido * 1) + (wreconocido*1))*1;
										datoauxfinal_wprocedimiento			= wprocedimiento;
										datoauxfinal_wresponsable			= wresponsable;
										datoauxfinal_wnresponsable			= wnresponsable;
										datoauxfinal_wtarifa				= wtarifa;
										datoauxfinal_tipoEmpresa			= tipoEmpresa;
										datoauxfinal_nitEmpresa				= nitEmpresa;
										datoauxfinal_concepto_cargo			= concepto_cargo;
										datoauxfinal_wnconcepto				= wnconcepto;
										datoauxfinal_id_tope_afectado		= id_tope_afectado;
										datoauxfinal_wtercero				= wtercero;
										datoauxfinal_wtercero_nom			= wnomtercero;
										datoauxfinal_wtercero_unix			= wtercero_unix;
										datoauxfinal_wtercero_especialidad	= wtercero_especialidad;
										datoauxfinal_wgraba_varios_terceros	= wgraba_varios_terceros;
										datoauxfinal_wporter				= wporter;
										datoauxfinal_wparalelo				= wparalelo;
										datoauxfinal_wvaltarExce			= ((datoauxfinal_wvaltarExce * 1) + (wvaltarExce*1))*1;
										datoauxfinal_wfecha_ingreso			= datoauxfinal_wfecha_ingreso;
										datoauxfinal_wfecha_egreso			= fechacargo;
										
										//console.log(datoauxfinal_ndia);
									
								}
								else
								{
									

									var datoauxfinal 						= new Object();
									datoauxfinal['clave'] = datoauxfinal_clave;
									datoauxfinal['ndia'] = datoauxfinal_ndia;
									datoauxfinal['fechacargo'] = datoauxfinal_fechacargo	;
									datoauxfinal['whora_ingreso'] = datoauxfinal_whora_ingreso;
									datoauxfinal['whora_egreso'] = datoauxfinal_whora_egreso;
									datoauxfinal['ccogra']	 = datoauxfinal_ccogra	;
									datoauxfinal['wnprocedimiento'] = datoauxfinal_wnprocedimiento;
									datoauxfinal['wnumerohab'] = datoauxfinal_wnumerohab	;
									datoauxfinal['wvalor'] = datoauxfinal_wvalor;
									datoauxfinal['wreconocido'] = datoauxfinal_wreconocido;
									datoauxfinal['wprocedimiento'] = datoauxfinal_wprocedimiento;
									datoauxfinal['wresponsable'] = datoauxfinal_wresponsable;
									datoauxfinal['wnresponsable'] = datoauxfinal_wnresponsable;
									datoauxfinal['wtarifa'] = datoauxfinal_wtarifa;
									datoauxfinal['tipoEmpresa']	 = datoauxfinal_tipoEmpresa;
									datoauxfinal['nitEmpresa'] = datoauxfinal_nitEmpresa;
									datoauxfinal['concepto_cargo'] = datoauxfinal_concepto_cargo;
									datoauxfinal['wnconcepto']	 = datoauxfinal_wnconcepto;
									datoauxfinal['id_tope_afectado'] = datoauxfinal_id_tope_afectado;
									datoauxfinal['wtercero'] = datoauxfinal_wtercero;
									datoauxfinal['wtercero_nombre'] = datoauxfinal_wtercero_nom;
									datoauxfinal['wtercero_unix'] = datoauxfinal_wtercero_unix;
									datoauxfinal['wtercero_especialidad'] = datoauxfinal_wtercero_especialidad;
									datoauxfinal['wgraba_varios_terceros'] = datoauxfinal_wgraba_varios_terceros;
									datoauxfinal['wporter'] = datoauxfinal_wporter;
									datoauxfinal['wparalelo'] = datoauxfinal_wparalelo;
									datoauxfinal['wvaltarExce'] = datoauxfinal_wvaltarExce;
									datoauxfinal['wfecha_ingreso'] = datoauxfinal_wfecha_ingreso;
									datoauxfinal['wfecha_egreso'] = datoauxfinal_wfecha_egreso;
									
									//-- se hace push
									console.log("push 6");
									console.log(datoauxfinal);
									datosauxfinal.push(datoauxfinal);
									
									//-- se inicializan de nuevo las variables
									datoauxfinal_clave					= clave;
									datoauxfinal_ndia					= 1 ;
									datoauxfinal_fechacargo				= fechacargo;
									datoauxfinal_whora_ingreso			= whora_ingreso;
									datoauxfinal_whora_egreso			= whora_egreso;
									datoauxfinal_ccogra					= ccogra;
									datoauxfinal_wnprocedimiento		= wnprocedimiento;
									datoauxfinal_wnumerohab				= wnumerohab;
									datoauxfinal_wvalor					= wvalor;
									datoauxfinal_wreconocido			= wreconocido ;
									datoauxfinal_wprocedimiento			= wprocedimiento;
									datoauxfinal_wresponsable			= wresponsable;
									datoauxfinal_wnresponsable			= wnresponsable;
									datoauxfinal_wtarifa				= wtarifa;
									datoauxfinal_tipoEmpresa			= tipoEmpresa;
									datoauxfinal_nitEmpresa				= nitEmpresa;
									datoauxfinal_concepto_cargo			= concepto_cargo;
									datoauxfinal_wnconcepto				= wnconcepto;
									datoauxfinal_id_tope_afectado		= id_tope_afectado;
									datoauxfinal_wtercero				= wtercero;
									datoauxfinal_wtercero_nom			= wnomtercero;
									datoauxfinal_wtercero_unix			= wtercero_unix;
									datoauxfinal_wtercero_especialidad	= wtercero_especialidad;
									datoauxfinal_wgraba_varios_terceros	= wgraba_varios_terceros;
									datoauxfinal_wporter				= wporter;
									datoauxfinal_wparalelo				= wparalelo;
									datoauxfinal_wvaltarExce			= wvaltarExce;
									datoauxfinal_wfecha_ingreso			= fechacargo;
									datoauxfinal_wfecha_egreso			= fechacargo;
									
								}
							}
							else if(j==2)
							{
								
								if(datoaux['wtercero'] == auxtercero  )
								{
									
										//console.log("responsable 2 datos tercero iguales");
										datoauxfinal_clave2					= clave;
										
										//---En empresas paf solo suma si es diferente de cero
										if ($('#reconocido_clave'+clave+'_'+ndia+'_res'+j).attr('paf'))
										{
											if($('#reconocido_clave'+clave+'_'+ndia+'_res'+j).attr('cuenta')=="no" )
											{
												datoauxfinal_ndia2					= ((datoauxfinal_ndia2 * 1));
												datoauxfinal_wvalor2				= 0;
											}
											else
											{
												datoauxfinal_ndia2					= ((datoauxfinal_ndia2 * 1) + 1);
												datoauxfinal_wvalor2				= wvalor;												
											}
										
										}
										else
										{
											datoauxfinal_ndia2					= ((datoauxfinal_ndia2 * 1) + 1);
											datoauxfinal_wvalor2				= wvalor;
										}
										datoauxfinal_fechacargo2				= datoauxfinal_fechacargo2;
										datoauxfinal_whora_ingreso2			= datoauxfinal_whora_ingreso2;
										datoauxfinal_whora_egreso2			= whora_egreso;
										datoauxfinal_ccogra2					= ccogra;
										datoauxfinal_wnprocedimiento2		= wnprocedimiento;
										datoauxfinal_wnumerohab2				= wnumerohab;
										//datoauxfinal_wvalor2					= wvalor;
										datoauxfinal_wreconocido2			= ((datoauxfinal_wreconocido2 * 1) + (wreconocido*1))*1;
										//alert(datoauxfinal_wreconocido2);
										datoauxfinal_wprocedimiento2			= wprocedimiento;
										datoauxfinal_wresponsable2			= wresponsable;
										datoauxfinal_wnresponsable2			= wnresponsable;
										datoauxfinal_wtarifa2				= wtarifa;
										datoauxfinal_tipoEmpresa2			= tipoEmpresa;
										datoauxfinal_nitEmpresa2				= nitEmpresa;
										datoauxfinal_concepto_cargo2			= concepto_cargo;
										datoauxfinal_wnconcepto2				= wnconcepto;
										datoauxfinal_id_tope_afectado2		= id_tope_afectado;
										datoauxfinal_wtercero2				= wtercero;
										datoauxfinal_wtercero_nom2			= wnomtercero;
										datoauxfinal_wtercero_unix2			= wtercero_unix;
										datoauxfinal_wtercero_especialidad2	= wtercero_especialidad;
										datoauxfinal_wgraba_varios_terceros2	= wgraba_varios_terceros;
										datoauxfinal_wporter2				= wporter;
										datoauxfinal_wparalelo2				= wparalelo;
										
										datoauxfinal_wvaltarExce2			= ((datoauxfinal_wvaltarExce2 * 1) + (wvaltarExce*1))*1;
										
										datoauxfinal_wfecha_ingreso2			= datoauxfinal_wfecha_ingreso2;
										datoauxfinal_wfecha_egreso2			= fechacargo;
									
								}
								else
								{
									
									// --> 2020-03-16: Jerson Trujillo, cambian todas la variables ejem datoauxfinal_clave por datoauxfinal_clave2
									//	ya que generaba un error js que decia que las variables no existian
									var datoauxfinal2 						= new Object();
									datoauxfinal2['clave'] = datoauxfinal_clave2;
									datoauxfinal2['ndia'] = datoauxfinal_ndia2;
									datoauxfinal2['fechacargo'] = datoauxfinal_fechacargo2	;
									datoauxfinal2['whora_ingreso'] = datoauxfinal_whora_ingreso2;
									datoauxfinal2['whora_egreso'] = datoauxfinal_whora_egreso2;
									datoauxfinal2['ccogra']	 = datoauxfinal_ccogra2	;
									datoauxfinal2['wnprocedimiento'] = datoauxfinal_wnprocedimiento2;
									datoauxfinal2['wnumerohab'] = datoauxfinal_wnumerohab2	;
									datoauxfinal2['wvalor'] = datoauxfinal_wvalor2;
									datoauxfinal2['wreconocido'] = datoauxfinal_wreconocido2;
									//alert(datoauxfinal_wreconocido2);
									datoauxfinal2['wprocedimiento'] = datoauxfinal_wprocedimiento2;
									datoauxfinal2['wresponsable'] = datoauxfinal_wresponsable2;
									datoauxfinal2['wnresponsable'] = datoauxfinal_wnresponsable2;
									datoauxfinal2['wtarifa'] = datoauxfinal_wtarifa2;
									datoauxfinal2['tipoEmpresa']	 = datoauxfinal_tipoEmpresa2;
									datoauxfinal2['nitEmpresa'] = datoauxfinal_nitEmpresa2;
									datoauxfinal2['concepto_cargo'] = datoauxfinal_concepto_cargo2;
									datoauxfinal2['wnconcepto']	 = datoauxfinal_wnconcepto2;
									datoauxfinal2['id_tope_afectado'] = datoauxfinal_id_tope_afectado2;
									datoauxfinal2['wtercero'] = datoauxfinal_wtercero2;
									datoauxfinal2['wtercero_nombre'] = datoauxfinal_wtercero_nom2;
									datoauxfinal2['wtercero_unix'] = datoauxfinal_wtercero_unix2;
									datoauxfinal2['wtercero_especialidad'] = datoauxfinal_wtercero_especialidad2;
									datoauxfinal2['wgraba_varios_terceros'] = datoauxfinal_wgraba_varios_terceros2;
									datoauxfinal2['wporter'] = datoauxfinal_wporter2;
									datoauxfinal2['wparalelo'] = datoauxfinal_wparalelo2;
									datoauxfinal2['wvaltarExce'] = datoauxfinal_wvaltarExce2;
									datoauxfinal2['wfecha_ingreso'] = datoauxfinal_wfecha_ingreso2;
									datoauxfinal2['wfecha_egreso'] = datoauxfinal_wfecha_egreso2;
									
									// --> 2020-03-16: Hasta aca
									
									
									//-- se hace push
									console.log("push 5");
									console.log(datoauxfinal2);
									datosauxfinal.push(datoauxfinal2);
									
									//-- se inicializan de nuevo las variables
									datoauxfinal_clave2					= clave;
									datoauxfinal_ndia2					= 1 ;
									datoauxfinal_fechacargo2				= fechacargo;
									datoauxfinal_whora_ingreso2			= whora_ingreso;
									datoauxfinal_whora_egreso2			= whora_egreso;
									datoauxfinal_ccogra2					= ccogra;
									datoauxfinal_wnprocedimiento2		= wnprocedimiento;
									datoauxfinal_wnumerohab2				= wnumerohab;
									datoauxfinal_wvalor2					= wvalor;
									datoauxfinal_wreconocido2			= wreconocido ;
									datoauxfinal_wprocedimiento2			= wprocedimiento;
									datoauxfinal_wresponsable2			= wresponsable;
									datoauxfinal_wnresponsable2			= wnresponsable;
									datoauxfinal_wtarifa2				= wtarifa;
									datoauxfinal_tipoEmpresa2			= tipoEmpresa;
									datoauxfinal_nitEmpresa2				= nitEmpresa;
									datoauxfinal_concepto_cargo2			= concepto_cargo;
									datoauxfinal_wnconcepto2				= wnconcepto;
									datoauxfinal_id_tope_afectado2		= id_tope_afectado;
									datoauxfinal_wtercero2				= wtercero;
									datoauxfinal_wtercero_nom2			= wnomtercero;
									datoauxfinal_wtercero_unix2			= wtercero_unix;
									datoauxfinal_wtercero_especialidad2	= wtercero_especialidad;
									datoauxfinal_wgraba_varios_terceros2	= wgraba_varios_terceros;
									datoauxfinal_wporter2				= wporter;
									datoauxfinal_wparalelo2				= wparalelo;
									datoauxfinal_wvaltarExce2			= wvaltarExce;
									datoauxfinal_wfecha_ingreso2			= fechacargo;
									datoauxfinal_wfecha_egreso2			= fechacargo;
									
								}
								
								
							}
						}
						else
						{
							
							if(j==1)
							{
								//console.log("1      claves distintas  Clave"+clave+ "auxclave"+auxclave ); 
								var datoauxfinal 						= new Object();
								datoauxfinal['clave'] = datoauxfinal_clave;
								datoauxfinal['ndia'] = datoauxfinal_ndia;
								datoauxfinal['fechacargo'] = datoauxfinal_fechacargo	;
								datoauxfinal['whora_ingreso'] = datoauxfinal_whora_ingreso;
								datoauxfinal['whora_egreso'] = datoauxfinal_whora_egreso;
								datoauxfinal['ccogra']	 = datoauxfinal_ccogra	;
								datoauxfinal['wnprocedimiento'] = datoauxfinal_wnprocedimiento;
								datoauxfinal['wnumerohab'] = datoauxfinal_wnumerohab	;
								datoauxfinal['wvalor'] = datoauxfinal_wvalor;
								datoauxfinal['wreconocido'] = datoauxfinal_wreconocido;
								datoauxfinal['wprocedimiento'] = datoauxfinal_wprocedimiento;
								datoauxfinal['wresponsable'] = datoauxfinal_wresponsable;
								datoauxfinal['wnresponsable'] = datoauxfinal_wnresponsable;
								datoauxfinal['wtarifa'] = datoauxfinal_wtarifa;
								datoauxfinal['tipoEmpresa']	 = datoauxfinal_tipoEmpresa;
								datoauxfinal['nitEmpresa'] = datoauxfinal_nitEmpresa;
								datoauxfinal['concepto_cargo'] = datoauxfinal_concepto_cargo;
								datoauxfinal['wnconcepto']	 = datoauxfinal_wnconcepto;
								datoauxfinal['id_tope_afectado'] = datoauxfinal_id_tope_afectado;
								datoauxfinal['wtercero'] = datoauxfinal_wtercero;
								datoauxfinal['wtercero_nombre'] = datoauxfinal_wtercero_nom;
								datoauxfinal['wtercero_unix'] = datoauxfinal_wtercero_unix;
								datoauxfinal['wtercero_especialidad'] = datoauxfinal_wtercero_especialidad;
								datoauxfinal['wgraba_varios_terceros'] = datoauxfinal_wgraba_varios_terceros;
								datoauxfinal['wporter'] = datoauxfinal_wporter;
								datoauxfinal['wparalelo'] = datoauxfinal_wparalelo;
								datoauxfinal['wvaltarExce'] = datoauxfinal_wvaltarExce;
								datoauxfinal['wfecha_ingreso'] = datoauxfinal_wfecha_ingreso;
								datoauxfinal['wfecha_egreso'] = datoauxfinal_wfecha_egreso;
								//-- se hace push
								console.log("push 4");
								console.log(datoauxfinal);
								datosauxfinal.push(datoauxfinal);
								//-- se inicializan de nuevo las variables
								datoauxfinal_clave					= clave;
								datoauxfinal_ndia					= 1 ;
								datoauxfinal_fechacargo				= fechacargo;
								datoauxfinal_whora_ingreso			= whora_ingreso;
								datoauxfinal_whora_egreso			= whora_egreso;
								datoauxfinal_ccogra					= ccogra;
								datoauxfinal_wnprocedimiento		= wnprocedimiento;
								datoauxfinal_wnumerohab				= wnumerohab;
								datoauxfinal_wvalor					= wvalor;
								datoauxfinal_wreconocido			= wreconocido ;
								datoauxfinal_wprocedimiento			= wprocedimiento;
								datoauxfinal_wresponsable			= wresponsable;
								datoauxfinal_wnresponsable			= wnresponsable;
								datoauxfinal_wtarifa				= wtarifa;
								datoauxfinal_tipoEmpresa			= tipoEmpresa;
								datoauxfinal_nitEmpresa				= nitEmpresa;
								datoauxfinal_concepto_cargo			= concepto_cargo;
								datoauxfinal_wnconcepto				= wnconcepto;
								datoauxfinal_id_tope_afectado		= id_tope_afectado;
								datoauxfinal_wtercero				= wtercero;
								datoauxfinal_wtercero_nom			= wnomtercero;
								datoauxfinal_wtercero_unix			= wtercero_unix;
								datoauxfinal_wtercero_especialidad	= wtercero_especialidad;
								datoauxfinal_wgraba_varios_terceros	= wgraba_varios_terceros;
								datoauxfinal_wporter				= wporter;
								datoauxfinal_wparalelo				= wparalelo;
								datoauxfinal_wvaltarExce			= wvaltarExce;
								datoauxfinal_wfecha_ingreso			= fechacargo;
								datoauxfinal_wfecha_egreso			= fechacargo;
						
							}
							else if(j==2)
							{
								
								//console.log("2  claves distintas  Clave"+clave+ "auxclave"+auxclave ); 
								var datoauxfinal2 						= new Object();
								datoauxfinal2['clave'] = datoauxfinal_clave2;
								datoauxfinal2['ndia'] = datoauxfinal_ndia2;
								datoauxfinal2['fechacargo'] = datoauxfinal_fechacargo2	;
								datoauxfinal2['whora_ingreso'] = datoauxfinal_whora_ingreso2;
								datoauxfinal2['whora_egreso'] = datoauxfinal_whora_egreso2;
								datoauxfinal2['ccogra']	 = datoauxfinal_ccogra2	;
								datoauxfinal2['wnprocedimiento'] = datoauxfinal_wnprocedimiento2;
								datoauxfinal2['wnumerohab'] = datoauxfinal_wnumerohab2	;
								datoauxfinal2['wvalor'] = datoauxfinal_wvalor2;
								datoauxfinal2['wreconocido'] = datoauxfinal_wreconocido2;
								datoauxfinal2['wprocedimiento'] = datoauxfinal_wprocedimiento2;
								datoauxfinal2['wresponsable'] = datoauxfinal_wresponsable2;
								datoauxfinal2['wnresponsable'] = datoauxfinal_wnresponsable2;
								datoauxfinal2['wtarifa'] = datoauxfinal_wtarifa2;
								datoauxfinal2['tipoEmpresa']	 = datoauxfinal_tipoEmpresa2;
								datoauxfinal2['nitEmpresa'] = datoauxfinal_nitEmpresa2;
								datoauxfinal2['concepto_cargo'] = datoauxfinal_concepto_cargo2;
								datoauxfinal2['wnconcepto']	 = datoauxfinal_wnconcepto2;
								datoauxfinal2['id_tope_afectado'] = datoauxfinal_id_tope_afectado2;
								datoauxfinal2['wtercero'] = datoauxfinal_wtercero2;
								datoauxfinal2['wtercero_nombre'] = datoauxfinal_wtercero_nom2;
								datoauxfinal2['wtercero_unix'] = datoauxfinal_wtercero_unix2;
								datoauxfinal2['wtercero_especialidad'] = datoauxfinal_wtercero_especialidad2;
								datoauxfinal2['wgraba_varios_terceros'] = datoauxfinal_wgraba_varios_terceros2;
								datoauxfinal2['wporter'] = datoauxfinal_wporter2;
								datoauxfinal2['wparalelo'] = datoauxfinal_wparalelo2;
								datoauxfinal2['wvaltarExce'] = datoauxfinal_wvaltarExce2;
								datoauxfinal2['wfecha_ingreso'] = datoauxfinal_wfecha_ingreso2;
								datoauxfinal2['wfecha_egreso'] = datoauxfinal_wfecha_egreso2;
								//alert(datoauxfinal_wreconocido2);
								//-- se hace push
								if (datoauxfinal2['wreconocido'] >0 )
								{
									if (datoauxfinal2['ndia'] >0 )
									{
										console.log("push 3");
										console.log(datoauxfinal2);
										datosauxfinal.push(datoauxfinal2);
									
									}
								}
								
								//-- se inicializan de nuevo las variables
								datoauxfinal_clave2					= clave;
								datoauxfinal_ndia2					= 1 ;
								datoauxfinal_fechacargo2				= fechacargo;
								datoauxfinal_whora_ingreso2			= whora_ingreso;
								datoauxfinal_whora_egreso2			= whora_egreso;
								datoauxfinal_ccogra2					= ccogra;
								datoauxfinal_wnprocedimiento2		= wnprocedimiento;
								datoauxfinal_wnumerohab2			= wnumerohab;
								datoauxfinal_wvalor2					= wvalor;
								datoauxfinal_wreconocido2			= wreconocido ;
								datoauxfinal_wprocedimiento2			= wprocedimiento;
								datoauxfinal_wresponsable2			= wresponsable;
								datoauxfinal_wnresponsable2			= wnresponsable;
								datoauxfinal_wtarifa2				= wtarifa;
								datoauxfinal_tipoEmpresa2			= tipoEmpresa;
								datoauxfinal_nitEmpresa2				= nitEmpresa;
								datoauxfinal_concepto_cargo2			= concepto_cargo;
								datoauxfinal_wnconcepto2				= wnconcepto;
								datoauxfinal_id_tope_afectado2		= id_tope_afectado;
								datoauxfinal_wtercero2				= wtercero;
								datoauxfinal_wtercero_nom2			= wnomtercero;
								datoauxfinal_wtercero_unix2			= wtercero_unix;
								datoauxfinal_wtercero_especialidad2	= wtercero_especialidad;
								datoauxfinal_wgraba_varios_terceros2	= wgraba_varios_terceros;
								datoauxfinal_wporter2				= wporter;
								datoauxfinal_wparalelo2				= wparalelo;
								datoauxfinal_wvaltarExce2			= wvaltarExce;
								datoauxfinal_wfecha_ingreso2			= fechacargo;
								datoauxfinal_wfecha_egreso2			= fechacargo;
								
							}
						}
						
						if(j==1)
							diasauxiliar1++;
						else
							diasauxiliar2++;
						
						if(j==1)
						{
							
							if(diasauxiliar1 == diasauxiliar)
							{
								
								var datoauxfinal 						= new Object();
								datoauxfinal['clave'] = datoauxfinal_clave;
								datoauxfinal['ndia'] = datoauxfinal_ndia;
								datoauxfinal['fechacargo'] = datoauxfinal_fechacargo	;
								datoauxfinal['whora_ingreso'] = datoauxfinal_whora_ingreso;
								datoauxfinal['whora_egreso'] = datoauxfinal_whora_egreso;
								datoauxfinal['ccogra']	 = datoauxfinal_ccogra	;
								datoauxfinal['wnprocedimiento'] = datoauxfinal_wnprocedimiento;
								datoauxfinal['wnumerohab'] = datoauxfinal_wnumerohab	;
								datoauxfinal['wvalor'] = datoauxfinal_wvalor;
								datoauxfinal['wreconocido'] = datoauxfinal_wreconocido;
								datoauxfinal['wprocedimiento'] = datoauxfinal_wprocedimiento;
								datoauxfinal['wresponsable'] = datoauxfinal_wresponsable;
								datoauxfinal['wnresponsable'] = datoauxfinal_wnresponsable;
								datoauxfinal['wtarifa'] = datoauxfinal_wtarifa;
								datoauxfinal['tipoEmpresa']	 = datoauxfinal_tipoEmpresa;
								datoauxfinal['nitEmpresa'] = datoauxfinal_nitEmpresa;
								datoauxfinal['concepto_cargo'] = datoauxfinal_concepto_cargo;
								datoauxfinal['wnconcepto']	 = datoauxfinal_wnconcepto;
								datoauxfinal['id_tope_afectado'] = datoauxfinal_id_tope_afectado;
								datoauxfinal['wtercero'] = datoauxfinal_wtercero;
								datoauxfinal['wtercero_nombre'] = datoauxfinal_wtercero_nom;
								datoauxfinal['wtercero_unix'] = datoauxfinal_wtercero_unix;
								datoauxfinal['wtercero_especialidad'] = datoauxfinal_wtercero_especialidad;
								datoauxfinal['wgraba_varios_terceros'] = datoauxfinal_wgraba_varios_terceros;
								datoauxfinal['wporter'] = datoauxfinal_wporter;
								datoauxfinal['wparalelo'] = datoauxfinal_wparalelo;
								datoauxfinal['wvaltarExce'] = datoauxfinal_wvaltarExce;
								datoauxfinal['wfecha_ingreso'] = datoauxfinal_wfecha_ingreso;
								datoauxfinal['wfecha_egreso'] = datoauxfinal_wfecha_egreso;
								
								if (datoauxfinal['wreconocido'] >0 )
								{
									if (datoauxfinal['ndia'] >0 )
									{
										console.log("push 2");
										console.log(datoauxfinal);
										datosauxfinal.push(datoauxfinal);
									}
								}
								
							}
						}
						if (j==2)
						{
							
							if(diasauxiliar2 == diasauxiliar)
							{
								// console.log("Responsable 2 dias auxiliar es igual a diasauxiliar2");
								//alert("diasauxiliar2");
								var datoauxfinal2 						= new Object();
								datoauxfinal2['clave'] = datoauxfinal_clave2;
								datoauxfinal2['ndia'] = datoauxfinal_ndia2;
								datoauxfinal2['fechacargo'] = datoauxfinal_fechacargo2	;
								datoauxfinal2['whora_ingreso'] = datoauxfinal_whora_ingreso2;
								datoauxfinal2['whora_egreso'] = datoauxfinal_whora_egreso2;
								datoauxfinal2['ccogra']	 = datoauxfinal_ccogra2	;
								datoauxfinal2['wnprocedimiento'] = datoauxfinal_wnprocedimiento2;
								datoauxfinal2['wnumerohab'] = datoauxfinal_wnumerohab2	;
								datoauxfinal2['wvalor'] = datoauxfinal_wvalor2;
								datoauxfinal2['wreconocido'] = datoauxfinal_wreconocido2;
								datoauxfinal2['wprocedimiento'] = datoauxfinal_wprocedimiento2;
								datoauxfinal2['wresponsable'] = datoauxfinal_wresponsable2;
								datoauxfinal2['wnresponsable'] = datoauxfinal_wnresponsable2;
								datoauxfinal2['wtarifa'] = datoauxfinal_wtarifa2;
								datoauxfinal2['tipoEmpresa']	 = datoauxfinal_tipoEmpresa2;
								datoauxfinal2['nitEmpresa'] = datoauxfinal_nitEmpresa2;
								datoauxfinal2['concepto_cargo'] = datoauxfinal_concepto_cargo2;
								datoauxfinal2['wnconcepto']	 = datoauxfinal_wnconcepto2;
								datoauxfinal2['id_tope_afectado'] = datoauxfinal_id_tope_afectado2;
								datoauxfinal2['wtercero'] = datoauxfinal_wtercero2;
								datoauxfinal2['wtercero_nombre'] = datoauxfinal_wtercero_nom2;
								datoauxfinal2['wtercero_unix'] = datoauxfinal_wtercero_unix2;
								datoauxfinal2['wtercero_especialidad'] = datoauxfinal_wtercero_especialidad2;
								datoauxfinal2['wgraba_varios_terceros'] = datoauxfinal_wgraba_varios_terceros2;
								datoauxfinal2['wporter'] = datoauxfinal_wporter2;
								datoauxfinal2['wparalelo'] = datoauxfinal_wparalelo2;
								datoauxfinal2['wvaltarExce'] = datoauxfinal_wvaltarExce2;
								datoauxfinal2['wfecha_ingreso'] = datoauxfinal_wfecha_ingreso2;
								datoauxfinal2['wfecha_egreso'] = datoauxfinal_wfecha_egreso2;
								//alert(datoauxfinal_wreconocido2);
								if (datoauxfinal2['wreconocido'] >0 )
								{
									if (datoauxfinal2['ndia'] >0 )
									{
										console.log("push 1");
										console.log(datoauxfinal2);
										datosauxfinal.push(datoauxfinal2);
									
									}
								}
								
								
							}
						}
						//alert ("j:"+j+"auxclave:"+auxclave+"igual a "+clave);
						
						auxtercero = datoaux['wtercero'];
					}
							
				}
				else
				{
					
					console.log("no entro , Responsable "+j+"  valor reconocido = "+$('#reconocido_clave'+clave+'_'+ndia+'_res'+j).attr('valor') +"clave"+clave+"ndia"+ndia);
					//alert($('#reconocido_clave'+clave+'_'+ndia+'_res'+j).attr('valor'));
					//alert($('#reconocido_clave'+clave+'_'+ndia+'_res'+j).length);
					
				}
			
				if(j==1)
				{
					console.log("aumento clave"+auxclave);
					auxclave = clave;
					//alert("j:"+j+"--"+wvalor);
					
				}
				if(j==2)
				{
					//alert("j:"+j+"--"+wvalor);
				}
				
						//alert("actualizo clave 3  clave: "+clave+" auxclave "+auxclave);
						
						
			
			}

		});
		
		for(tt=0;tt<datosauxfinal.length;tt++){
			var auxiliarvector = [];
			auxiliarvector = datosauxfinal[tt];
			if(isNaN(auxiliarvector['wvalor'])) //isNaN()
			{
				auxiliarvector['wvalor'] = 0;
			}				
		}
		// console.log(datos);
		console.log(datosauxfinal);
		
		// console.log(datosauxfinal2);
		// console.log(datos_dos);
		//return;
		
		
		//--------------------
		//-Validar antes a unix para grabar 
		// Se validan todos los cargos para ver si tienen tarifa en unix  y si tienen tercero, se tiene que hacer esta simulacion , para que no queden
		// datos "cojos" en Matrix o en Unix
		//------------------
		//------------------
		
		
		// --> 2019-07-18, Jerson Trujillo: Validar que no hayan habitaciones sin seleccion de tipo de habitacion
		sinTipoHab = false;
		
		$(".habitacion").each(function(index ){
			if($.trim($(this).val()) == "")
				sinTipoHab = true;
		});
		
		if(sinTipoHab){
			$("#boton_grabar").html('GRABAR').removeAttr("disabled");
			alert("Todas las habitaciones deben tener un tipo seleccionado.");
			return;			
		}			
		
		var devolver = false;
		var vector_id_grabados = [];
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:      '',
			wemp_pmla:         $('#wemp_pmla').val(),
			accion:            'validarCargos',
			whistoria:		   $("#whistoria_tal").val(),
			wing:			   $("#wing_tal").val(),
			wdatos:			   JSON.stringify(datosaux)


		},function (data) {
			//
			if(data.error == 1)
			{
				if(data.error_tipo=="error_tarifa")
				{
					jAlert("<span ><b>No se encontro tarifa en Unix</b></span><br>"+data.mensaje, "Mensaje");
					$("#boton_grabar").html('GRABAR').removeAttr("disabled");	
				}
				else if(data.error_tipo=="error_con_ter_unx")
				{
					jAlert("<span ><b>No se encontro relacion concepto-tercero en unix.</b></span><br>"+data.mensaje, "Mensaje");
					$("#boton_grabar").html('GRABAR').removeAttr("disabled");	
					
				}
				else 
				{
					jAlert("<span ><b>Error al grabar </b></span><br>"+data.mensaje, "Mensaje");
					$("#boton_grabar").html('GRABAR').removeAttr("disabled");	
					
				}
				
			}
			else
			{
				
					$("#boton_grabar").html('GRABAR').removeAttr("disabled");
					//var vector_id_grabados = [];
					var ii =0;
					var cuantos = datosauxfinal.length;
					var traerfechaparcial = 'no';
				
					
					//---Se elimina y se ajusta los registros de estancia que hay en unix
					//-------------------
					$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
					{
						consultaAjax:      '',
						wemp_pmla:         $('#wemp_pmla').val(),
						accion:            'borrar_estancia_unix',
						whistoria:		   $("#whistoria_tal").val(),
						wing:			   $("#wing_tal").val(),
						wfechaegreso:	   ultimafecha,
						whoraegreso:	   ultimahora


					},function (data) {
					
					});
				
					for(jj=0;jj<datosauxfinal.length;jj++){
					
						if(jj == cuantos-1)
						{
								if($("#wfecparcial").length>0)
								{
									traerfechaparcial = $("#wfecparcial").val();
								}
								else
									traerfechaparcial ='no';
						}
						else
						{
								traerfechaparcial ='no';
						}
						var datoget = datosauxfinal[jj];
						if(datoget['clave']!=""){
						
							if((datoget['wvalor']*1)!=0 && (datoget['ndia']*1)!=0 )
							{
						
								$.ajax(
								{
									url: "<?=$URL_AUTOLLAMADO?>?"+url_add_params,
									context: document.body,
									type: "POST",
									data:
									{
										consultaAjax:      '',
										wemp_pmla:         $('#wemp_pmla').val(),
										accion:            'grabar_pension',
										whistoria:			$("#whistoria_tal").val(),
										wing:				$("#wing_tal").val(),
										wno1 : 				$("#wno1_tal").val(),
										wno2 : 				$("#wno2_tal").val(),
										wap1 : 				$("#wap1_tal").val(),
										wap2 :				$("#wap2_tal").val(),
										wdoc : 				$("#wdoc_tal").val(),
										wcodemp : 			datoget['wresponsable'],
										wnomemp : 			datoget['wnresponsable'],
										wser : 				$("#wser_tal").val(),
										wfecing:			$("#wfecing_tal").val(),
										wcodcon : 			datoget['concepto_cargo'],
										wnomcon :  			datoget['wnconcepto'],
										wfeccar:			datoget['fechacargo'],
										wccogra:			datoget['ccogra'],
										wvalor:				datoget['wvalor'],
										wprocedimiento:		datoget['wprocedimiento'],
										wnprocedimiento:	datoget['wnprocedimiento'],
										wtarifa:			datoget['wtarifa'],
										wcantidad:			datoget['ndia'],
										wvaltarExce:		datoget['wvaltarExce'],
										wvaltarReco:		datoget['wreconocido'],
										wfacturable:		'S',
										wtip_paciente:		$("#wtip_paciente_tal").val(),
										wtipo_ingreso:		$("#wtipo_ingreso_tal").val(),
										wtipoEmpresa:		datoget['tipoEmpresa'],
										wnitEmpresa:		datoget['nitEmpresa'],
										wcodter	:			datoget['wtercero'],
										wnomter :			datoget['wtercero_nombre'],
										wporter :			datoget['wporter'],
										wcco : 				'',
										whora_cargo:		'',
										wnomCajero:			'',
										wcobraHonorarios:	'',
										wespecialidad:		datoget['wtercero_especialidad'],
										wgraba_varios_terceros: datoget['wgraba_varios_terceros'] ,
										wcodcedula:			'',
										wparalelo: 			datoget['wparalelo'],
										wid_grabado_ant:	aux_id_grabado,
										wrecexc:			'R',
										wnumerohab:			datoget['wnumerohab'],
										wid_tope_afectado:  datoget['id_tope_afectado'],
										whora_ingreso:		datoget['whora_ingreso'],
										whora_egreso:		datoget['whora_egreso'],
										wfecha_ingreso:		datoget['wfecha_ingreso'],
										wfecha_egreso:		datoget['wfecha_egreso'],
										wterunix:		    datoget['wtercero_unix'],
										wtraerfechaparcial: traerfechaparcial
										
									},

										async: false,
										success:function(data) {

										arraydata = eval('('+data+')');
										
											aux_id_grabado=arraydata.idcargo;
											
											if(arraydata.respuesta !='')
											{
												
												
											}
											
											if(aux_id_grabado =='')
											{
												devolver = true;
											}
											else
											{
												vector_id_grabados[ii]=aux_id_grabado;
												vector_id_grabados[ii]=aux_id_grabado;
												ii++;
												
											}

									}
								},'json').done(function(data){
									
									
								});
								
							}
							
						}

					}
					
					///
					 var datos = new Array();
					 var clave1 = 1;
					 $(".cambio").each(function(){
						
						
						//alert($(this).attr('matrix'));
						var dato  = new Object();
						dato['clave']   = clave1;
						dato['matrix']  =$(this).attr('matrix');
						dato['unix'] 	=$(this).attr('unix');
						datos.push(dato);
						clave1 ++;
					 });
					 
					 
					
					 
					$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
					{
						consultaAjax:      '',
						wemp_pmla:         $('#wemp_pmla').val(),
						accion:            'GrabarPoliticasCambio',
						wdatos:			   JSON.stringify(datos),
						whis:		       $("#whistoria_tal").val(),
						wing:			   $("#wing_tal").val(),


					},function (info) {
						//alert(info);
					});
					 	
					///
					
				//}
			}
			return data;
			
			
		},'json').done(function(data){
			
			if(data.error != 1)
			{

				if ( devolver ==false)
				{
					var datos               = new Object();

					var ArrayValores  = eval('(' + $("#vector_saldos").attr('valor') + ')');

					for (var CodVal in ArrayValores)
					{
						datos[CodVal] = {};
						datos[CodVal] = ArrayValores[CodVal];
					}


					var datosJson = $.toJSON( datos ); //convertir el arreglo de objetos en una variable json

					$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
					{
						consultaAjax:      '',
						wemp_pmla:         $('#wemp_pmla').val(),
						accion:            'descongelar_y_grabarDetalle',
						whistoria:		   $("#whistoria_tal").val(),
						wing:			   $("#wing_tal").val(),
						whtml:			   $("#tabla_pension").html(),
						wvector_saldos:	   datosJson


					},function (data) {
						
						//alert("entro descongelar");

					});
					//--> Activar boton grabar

					//$("#div_liquidacion_pension").html("<br><br><br><div id='div_mensajes2' class='fondoAmarillo' style='display:none;border: 1px solid #2A5DB0;padding: 5px;' align='center'></div>");
					$("#sel_concepto").html("");
					
					
					//
					var traerfechaparcial = 'no';
					
					if($("#wfecparcial").length>0)
					{
						//traerfechaparcial ='si';
						traerfechaparcial = $("#wfecparcial").val();
					}
					else
						traerfechaparcial ='no';
						
					
					//
					if(traerfechaparcial =='no')
					{
						
						
						jAlert("<span >Grabación Exitosa!!</span>", "Mensaje");
						
						selectconceptospension();
						$("#div_liquidacion_pension").html("<br><br><br><br>");
						$("#div_detalle_cuenta").html('<div id="div_mensajes3"></div>');
						//$( "#accordionDetCuenta" ).accordion( "option", "active", false );
						// --> Cargar tooltips
						$("#tdidmensajeliquidada").html('');
						

						//---
						$( "#accordionPension" ).accordion("destroy");
						$( "#accordionPension" ).accordion({
						collapsible: true,
						heightStyle: "content"
						});
						
						detalle_cuenta();
						$( "#accordionDetCuenta" ).accordion("destroy");
						$('.tooltip').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
						$( "#accordionDetCuenta" ).accordion({
						collapsible: true,
						heightStyle: "content"
						});
					
					}
					else
					{
						jAlert("<span >Grabación Exitosa!!</span>", "Mensaje");
						
						selectconceptospension();
						$("#div_liquidacion_pension").html("<br><br><br><br>");
						$("#div_detalle_cuenta").html('<div id="div_mensajes3"></div>');
						//$( "#accordionDetCuenta" ).accordion( "option", "active", false );
						// --> Cargar tooltips
						$("#tdidmensajeliquidada").html('');
						

						//---
						$( "#accordionPension" ).accordion("destroy");
						$( "#accordionPension" ).accordion({
						collapsible: true,
						heightStyle: "content"
						});
						
						detalle_cuenta();
						$( "#accordionDetCuenta" ).accordion("destroy");
						$('.tooltip').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
						$( "#accordionDetCuenta" ).accordion({
						collapsible: true,
						heightStyle: "content"
						});
						
						
					
					}
					
					
				}
				else
				{
					datosvector_id_grabados = $.toJSON(vector_id_grabados);
					// anular_pension ('si');
					alert("error al grabar");
				}
			}
			$("#boton_grabar").html('GRABAR').removeAttr("disabled");	
		});

		

		

		

	}

	function mostrar_mensaje(mensaje)
	{
		$("#div_mensajes2").html("<img width='20' height='20' src='../../images/medical/root/info.png' />&nbsp;"+mensaje);
		$("#div_mensajes2").css({"width":"250","opacity":" 0.6","fontSize":"11px"});
		$("#div_mensajes2").hide();

		$("#div_mensajes2").effect("pulsate", {}, 1500);
			setTimeout(function() {
			$("#div_mensajes2").hide(400);
		}, 5000);


		//---
		$( "#accordionPension" ).accordion("destroy");
		$('.tooltip').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
		$( "#accordionPension" ).accordion({
		collapsible: true,
		heightStyle: "content"
		});
		//--


	}

	function mostrar_mensaje2(mensaje)
	{
		$("#div_mensajes3").html("<img width='30' height='30' src='../../images/medical/root/info.png' />&nbsp;"+mensaje);
		$("#div_mensajes3").css({"width":"250","opacity":" 0.6","fontSize":"20px"});
		$("#div_mensajes3").hide();

		$("#div_mensajes3").effect("pulsate", {}, 1500);
			setTimeout(function() {
			$("#div_mensajes3").hide(400);
		}, 5000);


		//---
		$( "#accordionDetCuenta" ).accordion("destroy");
		$('.tooltip').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
		$( "#accordionDetCuenta" ).accordion({
		collapsible: true,
		heightStyle: "content"
		});
		//--


	}
	
	//- Anula cargos
	function anular_pension(desdegrabacion ='no')
	{

		// desdegrabacion quiere decir que si la anulacion se genera desde la grabacion de los cargos  al ocurrir un error en esta
		// o desdegrabacion == no cuando se le da propiamente al boton anular
		
		
		$("#id_boton_anular").prop( "disabled", true );
		if(desdegrabacion =='no')
		{
			$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
				{
					consultaAjax:      '',
					wemp_pmla:         $('#wemp_pmla').val(),
					accion:            'anular_pension',
					whistoria:		   $("#whistoria_tal").val(),
					wing:			   $("#wing_tal").val()


				},function (data) {

					
					
						
						selectconceptospension();
						$("#div_liquidacion_pension").html("<br><br><br><br>");
						$("#div_detalle_cuenta").html('<div id="div_mensajes3"></div>');
						//mostrar_mensaje2(data);
						jAlert("<span >"+data+"</span>", "Mensaje");
						//$( "#accordionDetCuenta" ).accordion( "option", "active", false );
						// --> Cargar tooltips
						$("#tdidmensajeliquidada").html('');
						//$( "#accordionPension" ).accordion("destroy");
						
/*						
							//---
							$( "#accordionPension" ).accordion("destroy");
							$( "#accordionPension" ).accordion({
							collapsible: true,
							heightStyle: "content"
							});
							//--

*/
					
				}).done(function(){
					
					detalle_cuenta();
				})
		}
		else
		{

			$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
				{
					consultaAjax:      '',
					wemp_pmla:         $('#wemp_pmla').val(),
					accion:            'anular_pension',
					whistoria:		   $("#whistoria_tal").val(),
					wing:			   $("#wing_tal").val()


				},function (data) {

				});

		}
		
		$("#id_boton_anular").prop( "disabled", false );
	}

	function detalle_cuenta()
	{


		$.ajax(
		{

			url: "<?=$URL_AUTOLLAMADO?>?"+url_add_params,
			context: document.body,
			type: "POST",
			data:
			{
				consultaAjax:      '',
				wemp_pmla:         $('#wemp_pmla').val(),
				accion:            'detalle_cuenta',
				whistoria:		   $("#whistoria_tal").val(),
				wing:			   $("#wing_tal").val()
			},

				async: false,
				success:function(data) {
				$("#div_detalle_cuenta").html(data);

					$('.tooltip').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
					// --> Redimecionar el tamaño del acordeo
					$("#accordionDetCuenta").accordion("destroy");
					$("#accordionDetCuenta").accordion({
						collapsible: true,
						autoHeight:true,
						active:	-1
					});

					//------------
					//calculo de totales
					var totalr;
					var totale;
					var totalesinformato;
					var totalfinal;
					var cclave;


					$(".trppal").each(function ()
					{
						totalfinal = 0;
						cclave=$(this).attr("clave");
						for(j=1 ; j<=5 /*$("#numero_responsables").val()*/; j++)
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
							totalesinformato = totale * 1;
							$("#input_excedente_"+cclave).attr("valor" , totale);
							totale = formatearnumero(totale);
							$("#input_excedente_"+cclave).val(totale);

						}

						totalfinal = totalfinal + totalesinformato;
						totalfinal = formatearnumero(totalfinal);
						$("#input_total_"+cclave).html(totalfinal);
					});



				}

		});
	}


	function resumen_pension(tomotemporal,temporal, fechaparcial)
	{
		
		if($("#whistoria").val()=='')
		{
			jAlert("<span >Digite una historia e ingreso por favor</span>", "Mensaje");
			return;
		}	
		if(tomotemporal =='si')
		{
			$("#wcambiodetipos").val('0')
		}
		
		
		
		$("#div_liquidacion_pension").html("<div><table height='200'><tr><td></td><tr></table></div>");
		$( "#accordionPension" ).accordion("destroy");
					$('.tooltip').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
					$( "#accordionPension" ).accordion({
						collapsible: true,
						heightStyle: "content"
					});


		$("#div_liquidacion_pension").block({
			message: '<br> <br> <br> <br> <br><b>Cargando...</b><br><br><img src="../../images/medical/ajax-loader.gif" >' ,
            css: { 	border: '3px solid #F9FAFB' ,
					border: '5px solid #F9FAFB',
					backgroundColor: '#F9FAFB',
					height : '200px',
					width: '100%'

				 }
		});


		if (tomotemporal == 'si')
		{
			wcambiodetipos = $("#wcambiodetipos").val() ;
		}
		else
		{
			wcambiodetipos =  temporal;
		}
		var cambiar_dias  = (ArrayValores['Cjepdi']);
		var cambiar_valor = (ArrayValores['Cjepva']);
		var blinkReautorizar;
		
		
		
		
		if($("#wconcepto").val()!='nada')
		{

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
				wcambiar_valor:	   cambiar_valor,
				wcambiar_dias:	   cambiar_dias,
				wtipo_ingreso:	   $("#wtipo_ingreso_tal").val(),
				wtipo_paciente:	   $("#wtip_paciente_tal").val(),
				nejemplo:		   $("#numeroejemplo").val(),
				wcambiodetipos:	   wcambiodetipos,
				wfechaparcial:	   fechaparcial   


			},function (data)
			{
			
				$("#div_liquidacion_pension").html(data);
				
				//---------cargo buscadores tercero
				HiddenArray = $("#array_terceros").val() ;
				HiddenArray  = eval('(' + HiddenArray + ')');
				codIni = '';
				nomIni = '';
				$(".buscador_terceros").each(function(){
					

					
						var campo = $(this).attr('id');
						
						crear_autocomplete(campo,HiddenArray,codIni,nomIni);
						
					
					
				});
				//------------------------------------

				// --> Redimecionar el tamaño del acordeon
				$( "#accordionPension" ).accordion("destroy");
				$('.tooltip').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
				$( "#accordionPension" ).accordion({
				collapsible: true,
				heightStyle: "content"
				});
				//------------
				
				//--Campos que aceptan solo valores enteros
				$('.entero').keyup(function(){
					if ($(this).val() !="")
					{
						$(this).val($(this).val().replace(".00", ""));
						$(this).val($(this).val().replace(/[^0-9]/g, ""));
					}
				});
				

				//calculo de totales
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
						$("#divbutton_detalle_"+cclave).html("<img width='15' blink='' height='15' src='../../images/medical/root/info.png' style='cursor : pointer' title='Este concepto requiere tercero \npor favor verificar'>");
						
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
					// alert(temporal);
					resumen_pension('no',temporal,dateText)} 
				});
				
			}).done(function (){
						var datos_estancia = new Array();
						
						
						//envio tipo de habitaciones, con su dia inicial y fin
						// construyo vector  para luego ir a buscar los cargos grabados en las tablas.
						$(".habitacion").each(function(){
							
							//alert($(this).val()+"--dia inicial"+$(this).attr('diainicial')+"--dia final"+$(this).attr('diafinal'));
							//
								var dato = new Object();
								dato['tipo'] = $(this).val();
								dato['inicial'] = $(this).attr('diainicial');
								dato['final'] = $(this).attr('diafinal');
								datos_estancia.push(dato);
							//
							
							
						});
						//alert($("#Cambiocargos").val());
						$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
						{
							consultaAjax:      '',
							wemp_pmla:         $('#wemp_pmla').val(),
							accion:            'ModificarCargos',
							whistoria:		   $("#whistoria_tal").val(),
							wing:			   $("#wing_tal").val(),
							wdatos:			   JSON.stringify(datos_estancia),
							wcambiocargos:	   $("#Cambiocargos").val()


						},function (data) {
							$("#divCargoscambiados").html(data);
						});
				
			});


		}
		else
		{
			$("#div_liquidacion_pension").html('');
			$( "#accordionPension" ).accordion("destroy");
					$('.tooltip').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
					$( "#accordionPension" ).accordion({
						collapsible: true,
						heightStyle: "content"
					});

			$("#div_liquidacion_pension").unblock();
		}

	}

	function formatearnumero(numero)
	{
		var resultado;
		resultado = numero.toFixed(2).replace(/./g, function(c, i, a) {
		return i && c !== "." && !((a.length - i) % 3) ? ',' + c : c;
		});

		return resultado;

	}

	//------------------------------------------------------------------------------------------------------
	
	//------------------------------------------------------------------------------------------------------
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


	function selectconceptospension()
	{
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
				{
					consultaAjax:      '',
					wemp_pmla:         $('#wemp_pmla').val(),
					accion:            'selectconceptospension',
					whistoria:		   $("#whistoria_tal").val(),
					wing:			   $("#wing_tal").val()



				},function (data) {
					$('#sel_concepto').html(data);
				});
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

	function cargar_datos_caja()
	{
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:      '',
			wemp_pmla:         $('#wemp_pmla').val(),
			accion:            'cargar_datos_caja'

		},function(data){
			nomcco = data.wnomcco ;
			$("#div_servicio_tal").val(nomcco);
			$("#div_servicio").html(data.wcco+'-'+nomcco);
			$("#wcajadm").val(data.wcajadm);
			$("#wcajadm_tal").val(data.wcajadm);

			$("#wcco").val(data.wcco);
			$("#wcco_tal").val(data.wcco);
			$("#wnomcco").val(data.wnomcco);
			$("#wbod").val(data.wbod);
			$("#wbod_tal").val(data.wbod);
			$("#wcaja").val(data.wcaja);
			$("#nomCajero").val(data.nomCajero);
			$("#nomCajero_tal").val(data.nomCajero);
			$("#permiteCambiarResponsable").val(data.cambiarResponsable);
			$("#permiteCambiarResponsable_tal").val(data.cambiarResponsable);
			$("#permiteCambiarTarifa").val(data.cambiarTarifa);
			$("#permiteCambiarTarifa_tal").val(data.cambiarTarifa);
			$("#permiteRegrabar").val(data.permiteRegrabar);
			$("#permiteRegrabar_tal").val(data.permiteRegrabar);
			$("#permiteSeleccionarFacturable").val(data.permiteSeleccionarFacturable);
			$("#permiteSeleccionarFacturable_tal").val(data.permiteSeleccionarFacturable);
			$("#permiteSeleccionarRecExc").val(data.permiteSeleccionarRecExc);
			$("#permiteSeleccionarRecExc_tal").val(data.permiteSeleccionarRecExc);
			if(data.permiteSeleccionarRecExc == 'on')
				$("[name=wrecexc_1]").removeAttr('disabled');
			if(data.permiteSeleccionarFacturable == 'on')
				$("[name=wfacturable_1]").removeAttr('disabled');
			if(data.cambiarResponsable == 'on')
				$("#ImgCambioRes").show();
			if(data.cambiarTarifa == 'on')
				$("#ImgCambioTar").show();

		},
		'json');
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
	
	//---------------------------
	function verParalelo(Elemento, id)
	{
		if(jQuery(Elemento).attr("src") == "../../images/medical/iconos/gifs/i.p.next[1].gif")
			jQuery(Elemento).attr("src", "../../images/medical/iconos/gifs/i.p.previous[1].gif");
		else
			jQuery(Elemento).attr("src", "../../images/medical/iconos/gifs/i.p.next[1].gif");

		// --> Pintar borde azul
		if($("#"+id).is(':hidden'))
		{
			jQuery(Elemento).parent().next().css({
				'border-left': 		'2px dotted #72A3F3'
			});
			jQuery(Elemento).parent().parent().find('td[class]').css({
				'border-top': 		'2px dotted #72A3F3'
			});
			jQuery(Elemento).parent().parent().find('td[class]:last').css({
				'border-right': 		'2px dotted #72A3F3'
			});
		}
		// --> Quitar borde azul
		else
		{
			jQuery(Elemento).parent().next().css({
				'border-left': 		''
			});
			jQuery(Elemento).parent().parent().find('td[class]').css({
				'border-top': 		''
			});
			jQuery(Elemento).parent().parent().find('td[class]:last').css({
				'border-right': 		''
			});
		}
		// --> Colocarle borde azul a los td del paralelo
		$("#"+id).find('td[class]').css({
			'border-bottom': 		'2px dotted #72A3F3'
		});

		// --> Ocultar y mostrar paralelo
		$("#"+id).toggle(0);
	}

	//----------------------------
	//	Nombre: cargar_datos
	//	Descripcion: funcion que carga los datos basicos informativos dados una historia y un ingreso
	//	Entradas: elemento - elemento desde donde se hace el llamado a la funcion
	//	Salidas:
	//----------------------------
	function cargar_datos(elemento)
	{
		
		$("#linkAbrirHce").hide();
		var id = elemento;//variable que almacena el id del elemento de donde se hizo el llamado a la funcion cargar_datos
		// si la historia es vacia  se  inician los datos y no se continua la ejecucion de la funcion
		if($("#whistoria_tal").val()=='' && $("#whistoria").val()=='')
		{
			limpiarPantalla();
			reiniciarpension();
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
				reiniciarpension();
			}
			else
			{
				// data.error indica si hay un error  en el llamado de la funcion
				// que no exista la historia 
				if(data.error ==1)
				{
					alert(data.mensaje);
					$('#whistoria').val('');
					$('#wing').val('');
					limpiarPantalla();

					reiniciarpension();

				}
				else
				{


					reiniciarpension();

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

					$("#div_servicio").html($("#wcco").val()+'-'+nomcco);
					$("#div_servicio_tal").val(nomcco);

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

					// --> Pintar el detalle de la cuenta simple
					// $("#cargos_sin_facturar").val(data.cargos_sin_facturar);
					// $("#tabla_informativos_basicos").css("display" , "block");

					// --> Pintar el detalle de la cuenta simple resumido
					//PintarDetalleCuentaResumido($('#whistoria').val(), data.wwing);

					// --> verificar si se pueden grabar cargos, por congelacion de cuenta.
					//validarEstadoDeCuentaCongelada(false);
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
			
			detalle_cuenta();
			selectconceptospension();
		});
	}

	//------------------------------------------------------------------------
	//Funcion que oculta los detalles inactivos y que solo muestra los activos
	//-------------------------------------------------------------------------
	function ver_detalle(clave)
	{
		//$(".detalle").hide();
		
		$("#detalle_"+clave).toggle("slow");
	}
	//--------------------------------------
	// Funcion que calcula la pension  permitiendo un cambio de tipo de habitacion, esto es que puedo generar una pension
	// teniendo en cuenta un diferente tipo de habitacion que el original
	function cambiar_tipo_habitacion_facturacion(clave,responsable)
	{
		var tomotemporal = 'no';
		var temporal = "";
		var antes = $("#wcambiodetipos").val();
		if($("#wcambiodetipos").val() == 0)
		{
			temporal = clave+":"+responsable+":"+$("#tipo_hab_facturacion_"+clave+"_"+responsable).val();
			$("#wcambiodetipos").val(temporal);
		}
		else
		{
			temporal = antes+"___"+clave+":"+responsable+":"+$("#tipo_hab_facturacion_"+clave+"_"+responsable).val();
			$("#wcambiodetipos").val(temporal);
		}
		if($("#wfecparcial").length>0)
		{
			resumen_pension( tomotemporal, temporal, $("#wfecparcial").val() );
		}
		else
		{
			resumen_pension( tomotemporal, temporal, 'no' );
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
	// -->  Div para mostrar el mensaje de que la cuenta se encuentra congelada
	echo "
	<div id='divMsjCongelar' align='center' style='display:none;font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 10pt;'>
		<br>
	</div>";
	echo"
	<div align='center'>
		<div width='95%' id='accordionDatosPaciente' style='display:none'>
			<h3>DATOS DEL PACIENTE</h3>
			<div height='10' class='pad' align='center' id='DatosPaciente' style='display : none'>";

				echo"
					<br><br>
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

				echo"	</table><br><br>

				</div>
		</div>
		<div width='95%' id='accordionPension' style='display : none'>
			<h3>LIQUIDACION DE PENSION</h3>";
	echo	"<div id='detalle_liquidacion_general' style='display : none'>
				<div id='sel_concepto' >";
	echo 		$conceptos = selectconceptospension($whistoria, $wing ,$div_tarifa,$div_responsable);
	echo       "</div>
				<div id='div_liquidacion_pension'></div>";
	echo"	</div>
		</div>
		<div width='95%' id='accordionDetCuenta' style='display: none'>
			<h3>DETALLE DE LA CUENTA</h3>
			<div id='div_detalle_cuenta'>";
			detalle_cuenta($whistoria,$wing);
	// echo "<script>";
	// echo "detalle_cuenta();";
	// echo "</script>";
	echo"	</div>
		</div>
		<div width='95%' align='right'><br>
			<div id='div_mensajes' class='fondoAmarillo' style='display:none;border: 1px solid #2A5DB0;padding: 5px;' align='center'>
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