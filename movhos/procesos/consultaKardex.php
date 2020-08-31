<?php
include_once("conex.php");

/******************************************************************************
 * 									FUNCIONES
 ******************************************************************************/

/**
 * Consulta los campos completos de la tabla 53 y devuelve un Array con esos datos
 * 
 * @param $his
 * @param $ing
 * @param $fecha
 * @return unknown_type
 * 
 * Nota: Los campos a buscar en la tabla de ENCABEZADOS DEL KARDEX (movhos_000053) son:
 * - Kardia: Diagnóstico
 * - Karson: Sondas
 * - Karais: Aislamiento
 * - Karter: Terapia
 * - Karcip: Cirugías pendientes
 * - Karobs: Observaciones generales
 * - Karcui: Cuidados intensivos
 * - Kardem: Dextrometer
 */

class infoEncabezadoKardex{
	
	var $diagnostico;
	var $sondas;
	var $aislamiento;
	var $terapia;
	var $cirugiasPendiente;
	var $observacionesGenerales;
	var $cuidadosIntensivos;
	var $observacionesDextrometer;
	var $observacionesDietas;
	
	function infoEncabezadoKardex( $his, $ing, $fecha ){
		
		global $conex;
		global $wbasedato;
		
		$diagnostico = Array();
		
		$sql = "SELECT
					*
				FROM
					{$wbasedato}_000053
				WHERE
					karhis = '$his'
					AND karing = '$ing'
					AND fecha_data = '$fecha'
				";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query consulta de disgnóstico $sql - ".mysql_error() );
		$numrows = mysql_num_rows( $res );
		
		if( $rows = mysql_fetch_array( $res ) ){
			
			$this->diagnostico = $rows['Kardia'];
			$this->sondas = $rows['Karson'];
			$this->aislamiento = $rows['Karais'];
			$this->terapia = $rows['Karter'];
			$this->cirugiasPendiente = $rows['Karcip'];
			$this->observacionesGenerales = $rows['Karobs'];
			$this->cuidadosIntensivos = $rows['Karcui']; 
			$this->observacionesDextrometer = $rows['Kardem'];
			$this->observacionesDietas = $rows['Kardie'];
		}
		else{
			
			$this->diagnostico = '';
			$this->sondas = '';
			$this->aislamiento = '';
			$this->terapia = '';
			$this->cirugiasPendiente = '';
			$this->observacionesGenerales = '';
			$this->cuidadosIntensivos = '';
			$this->observacionesDextrometer = '';
			$this->observacionesDietas = '';
		}
	}
};

class liquidos{
	
	var $liquidos;
	
	function liquidos( $his, $ing, $fecha ){
		
		global $conex;
		global $wbasedato;
		
		$this->liquidos = Array();
		
		$sql = "SELECT
					*
				FROM
					{$wbasedato}_000051 a 
				WHERE
					inkhis = '$his'
					AND inking = '$ing'
					AND inkfec = '$fecha'
				";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query de Medico Tratante $sql - ".mysql_error() );
		$numrows = mysql_num_rows( $res );
		
		if( $numrows > 0 ){
			
			for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
				$this->liquidos[ $i ] = $rows;
			}
		}
	}
	
	/************************************************************************************************
	 * Devuleve las observaciones de un liquido segun la posicion que ocupe
	 * 
	 * @param $i
	 * @return unknown_type
	 ************************************************************************************************/
	function getObservaciones( $i ){
	
		if( @$this->liquidos[ $i ] ){
			return $this->liquidos[ $i ]['Inkobs'];
		}
		else{
			return Array();
		}
	}
	
	/**************************************************************************************************
	 * Devuelve las descripciones de un liquido
	 * @param $i
	 * @return unknown_type
	 **************************************************************************************************/
	function getDescripcion( $i ){
		
		if( @$this->liquidos[ $i ] ){
			$exp = explode( ";", $this->liquidos[ $i ]['Inkdes'] );
			return $exp;
		}
		else{
			return Array();
		}
	}
	
	/********************************************************************************
	 * Devuelve el texto de Descripción en formato Html
	 * @param $i
	 * @return unknown_type
	 ********************************************************************************/
	function getDescripcionTextoHtml( $i ){
		
		if( @$this->liquidos[ $i ] ){
			$exp =str_replace( ";", "<br>", htmlentities( trim($this->liquidos[ $i ]['Inkdes'] ) ) ); 
			return $exp;
		}
		else{
			return "";
		}
	}
}

/************************************************************************************************************
 * Consulta el detalle del Kardex
 * 
 * @param $historia
 * @param $ingreso
 * @param $fecha
 * @param $tipoProtocolo
 * @return unknown_type
 * 
 * Nota: 
 * Los protocoles son
 * 		N: Normales.  Si no hay nada en este campo se asume normal
 * 		U: Nutrición
 * 		Q: Quimioterapia
 * 		A: Analgesias
 ************************************************************************************************************/
function detalleKardex($historia,$ingreso,$fecha,$tipoProtocolo){
	global $wbasedato;
	global $conex;

	global $centroCostosServicioFarmaceutico;
	global $codigoServicioFarmaceutico;
	global $codigoCentralMezclas;
	global $centroCostosCentralMezclas;

	global $usuario;		//Información de usuario

	$coleccion = array();

	//*******************************Grupos que puede ver el centro de costos del usuario
	$tieneGruposIncluidos = false;
	if($usuario->gruposMedicamentos != "*" && $usuario->gruposMedicamentos != '' && $usuario->gruposMedicamentos != 'NO APLICA'){
		$tieneGruposIncluidos = true;
	}
	//********************************

	$q = "	SELECT
				Kadart,Artcom,Artgen,Artuni,SUBSTRING_INDEX( Artgru, '-', 1 ) Artgru,Kadcfr,Kadufr,Kaddia,Kadest,Kadess,Kadper,Kadffa,Kadfin,Kadhin,Kadvia,Kadfec,Kadcon,Kadobs,Kadori,Kadsus,Kadcnd,Kaddma,Kaddis,Kaduma,Kadcma,Kadpro,Kadcco,Deffra,Deffru,Defven,Defdis,Defdup
			FROM
				".$wbasedato."_000054, ".$wbasedato."_000026 LEFT JOIN ( SELECT Deffra, Deffru, Defart, Defven,Defdis,Defdup  FROM {$wbasedato}_000059 WHERE Defest = 'on' AND Defcco = '$centroCostosServicioFarmaceutico') a ON a.Defart = Artcod
			WHERE
				Kadhis = '$historia'
				AND Kading = '$ingreso'
				AND Kadfec = '$fecha' ";
				if($tieneGruposIncluidos){
					$q .= " AND Kadori = '$codigoServicioFarmaceutico' ";
					$q .= " AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN $usuario->gruposMedicamentosQuery ";
				}
				$q .= " AND Kadpro = '$tipoProtocolo'
				AND Artcod = Kadart ";

	$subConsulta = " SELECT
				Kadart,Artcom,Artgen,Artuni,'' Artgru,Kadcfr,Kadufr,Kaddia,Kadest,Kadess,Kadper,Kadffa,Kadfin,Kadhin,Kadvia,Kadfec,Kadcon,Kadobs,Kadori,Kadsus,Kadcnd,Kaddma,Kaddis,Kaduma,Kadcma,Kadpro,Kadcco,Deffra,Deffru,Defven,Defdis,Defdup
			FROM
				".$wbasedato."_000054, cenpro_000002 LEFT JOIN ( SELECT Deffra, Deffru, Defart, Defven,Defdis,Defdup  FROM {$wbasedato}_000059 WHERE Defest = 'on' AND Defcco = '$centroCostosCentralMezclas') a ON a.Defart = Artcod
			WHERE
				Kadhis = '$historia'
				AND Kading = '$ingreso'
				AND Kadfec = '$fecha'
				AND Kadpro = '$tipoProtocolo'
				AND Kadori = '$codigoCentralMezclas'
				AND Artcod = Kadart ";

	if($usuario->esUsuarioCM){
		$q = $subConsulta;
	} else {
		if(!$tieneGruposIncluidos){
			$q = $q." UNION ".$subConsulta;
		}
	}
	$q = $q." ORDER BY Artcom ";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$cont1 = 0;

		while ($cont1 < $num){
			$cont1++;

			$detalle = new detalleKardexDTO();

			$info = mysql_fetch_array($res);

			$detalle->historia = $historia;
			$detalle->ingreso = $ingreso;
			$detalle->fecha = $fecha;
			$detalle->codigoArticulo = $info['Kadart']."-".$info['Kadori']."-".$info['Artcom'];
			$detalle->cantidadDosis = $info['Kadcfr'];
			$detalle->unidadDosis = $info['Kadufr'];
			$detalle->diasTratamiento = $info['Kaddia'];
			$detalle->estadoRegistro = $info['Kadest'];
			$detalle->estadoAdministracion = $info['Kadess'];
			$detalle->periodicidad = $info['Kadper'];
			$detalle->condicionSuministro = $info['Kadcnd'];
			$detalle->formaFarmaceutica = $info['Kadffa'];
			$detalle->dosisMaxima = $info['Kaddma'];
			$detalle->fechaInicioAdministracion = $info['Kadfin'];
			$detalle->horaInicioAdministracion = $info['Kadhin'];
			$detalle->via = $info['Kadvia'];
			$detalle->fechaKardex = $info['Kadfec'];
			$detalle->suspendido = $info['Kadsus'];
			$detalle->estaConfirmado = $info['Kadcon'];
			$detalle->origen = $info['Kadori'];
			$detalle->observaciones = $info['Kadobs'];
			$detalle->cantidadUnidadManejo = $info['Kadcma'];
			$detalle->unidadManejo = $info['Artuni'];
			$detalle->grupo = $info['Artgru'];
			$detalle->tipoProtocolo = $info['Kadpro'];

			if(isset($info['Deffra']) && !empty($info['Deffra'])){
				$detalle->maximoUnidadManejo = $info['Deffra'];
				$detalle->unidadMaximoManejo = $info['Deffru'];
				$detalle->vencimiento		 = $info['Defven'];
				$detalle->esDispensable		 = $info['Defdis'];
				$detalle->esDuplicable		 = $info['Defdup'];

				$detalle->permiteModificar 	 = true;

				//Consulta de los dias de tratamiento de este articulo
				obtenerDatosAdicionalesArticulo($historia, $ingreso, $info['Kadart'], &$detalle->diasTotalesTto, &$detalle->dosisTotalesTto);

				//Consulta de cantidades ctc
				//****************************Consulta de las cantidades del CTC autorizado acumulado y usado
				$q2 = "SELECT Ctccau,Ctccus,Ctcuca FROM {$wbasedato}_000095 WHERE Ctchis = '".$historia."' AND Ctcing = '".$ingreso."' AND Ctcart = '".$info['Kadart']."'";
				$res2 = mysql_query($q2, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
				while($info2 = mysql_fetch_array($res2)){
					$detalle->cantidadAutorizadaCtc 	= $info2['Ctccau'];
					$detalle->cantidadUtilizadaCtc 		= consultarCantidadAcumuladaDispensada($conex,$wbasedato,$historia,$ingreso,$info['Kadart']);
					$detalle->unidadesCantidadesCtc 	= $info2['Ctcuca'];
				}
				//***************************
				$coleccion[] = $detalle;
			}
		}
	}
	return $coleccion;
}

/**
 * Devuelve el nombre comercial del medicamento
 * @param $codigo
 * @return unknown_type
 */
function consultarMedicamento( $codigo ){
	
	global $conex;
	global $wbasedato;
	
	$this->liquidos = Array();
	
	$sql = "SELECT
				Artcom
			FROM
				{$wbasedato}_000026 a
			WHERE
				artcod = '$codigo'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query de Medico Tratante $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $rows = mysql_fetch_array( $res ) ){
		return $rows['Artcom'];
	}
	else{
		
		$sql = "SELECT
					Artcom
				FROM
					cenpro_000002 a
				WHERE
					artcod = '$codigo'
				";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query de Medico Tratante $sql - ".mysql_error() );
		
		if( $rows = mysql_fetch_array( $res ) ){
			return $rows['Artcom'];
		}
		else{
			return "";
		}
	}
}

/**
 * Clase dexotrometer, guarda la información básico del dextrometer
 * @author Administrador
 *
 */
class dextrometer{
	
	var $insulina;
	var $frecuencia;
	var $observaciones;
	var $codigoEsquema;
	var $esquema;
	
	function dextrometer( $his, $ing, $fecha ){
		
		global $conex;
		global $wbasedato;
		
		$this->liquidos = Array();
		
		$sql = "SELECT
					*
				FROM
					{$wbasedato}_000070 a, 
					{$wbasedato}_000042 b
				WHERE
					infhis = '$his'
					AND infing = '$ing'
					AND inffec = '$fecha'
					AND infcde = concod 
					AND contip = 'I'
				";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query de Medico Tratante $sql - ".mysql_error() );
		$numrows = mysql_num_rows( $res );
		
		if( $numrows > 0 ){
			
			for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){

				$this->frecuencia = $rows['Inffde'];
				$this->codigoEsquema = $rows['Condes'];
				
				$sql = "SELECT
							Artnom
						FROM
							{$wbasedato}_000026
						WHERE
							artcod = '{$rows['Infade']}'
						";
				
				$resart = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
				
				if( $rowsart = mysql_fetch_array( $resart ) ){
				
					$this->insulina = $rows['Infade']."".$rowsart['Artnom'];
				}
			}
		}
	}
}

/**
 * Calcula la edad de la persona de acuerdo a la fecha de nacimiento
 * 
 * @param date $fnac		Fecha de nacimientos				
 * @return entero			Edad de la persona
 */
function calculoEdad( $fnac ){
	
	$edad = 0;
	
	$nac = explode( "-", $fnac );				//fecha de nacimiento
	$fact = date( "Y-m-d" );					//fecha actual

	if( count($nac) == 3 ){
		$edad = date("Y") - $nac[0];
		
		if( date("Y-m-d") < date( "Y-".$nac[1]."-".$nac[2] ) ){
			$edad--;
		}
	}
		
	return $edad;
}

/**
 * 
 * @param $his
 * @param $ing
 * @return unknown_type
 */
function consultarKardexActual( $his, $ing ){
	
	$fecha = date( "Y-m-d" );
		
	//Recibiendo habitación
	
//	//Consultando información del paciente
//	$infoPaciente = new pacienteKardexDTO();
//	$infoPaciente = consultarInfoPacienteKardex( $his, "" );
//	$ing = $infoPaciente->ingresoHistoriaClinica;
	
	$medicos = Array();
	//$medicos = medicosTratantes( $his, $ing, $fecha );
	$medicos = consultarMedicosTratantesDefinitivoKardex( $his, $ing, $fecha );

	$liquidos = new liquidos( $his, $ing, $fecha );
	
	$dietas = new dietaKardexDTO();
	$dietas = consultarDietasDefinitivoPaciente( $his, $ing, $fecha );		
	
	$encabezadoKardex = new infoEncabezadoKardex( $his, $ing, $fecha );
	//$perfil = consultarDetallePerfilKardex( $his, $ing, $fecha );
	//$perfil = new detalleKardexDTO();

	$examenes = new ExamenKardexDTO();
	$examenes = consultarExamenesLaboratorioDefinitivoKardex( $his, $ing, $fecha );
	
	$dextrometer = new dextrometer( $his, $ing, $fecha );
	$dextrometer = consultarEsquemaInsulina( $his, $ing, $fecha );
	
	//Consultando esquema del dextrometer
	$esquemaDextrometer = new IntervaloDextrometerDTO();
	$esquemaDextrometer = consultarIntervalosDextrometer( $his, $ing, $fecha );
	
	$mezclasA = detalleKardex( $his, $ing, $fecha, "A" );
	$mezclasU = detalleKardex( $his, $ing, $fecha, "U" );
	$quimitoterapia = detalleKardex( $his, $ing, $fecha, "Q" );
	
	$pendientesEncabezado = Array();
	$pendientesObservaciones = Array();
	
	if( !empty($encabezadoKardex->sondas) ){
		$pendientesEncabezado[] ="Sondas";
		$pendientesObservaciones[] = $encabezadoKardex->sondas;
	} 
	
	if( !empty($encabezadoKardex->aislamiento) ){
		$pendientesEncabezado[] = "Aislamientos";
		$pendientesObservaciones[] = $encabezadoKardex->aislamiento;
	}
	
	if( !empty($encabezadoKardex->terapia) ){
		$pendientesEncabezado[] = "Terapias";
		$pendientesObservaciones[] = $encabezadoKardex->terapia;
	}
	
	if( !empty($encabezadoKardex->cirugiasPendiente ) ){
		$pendientesEncabezado[] = "Cirug&iacute;as pendientes";
		$pendientesObservaciones[] = $encabezadoKardex->cirugiasPendiente;
	}
	
	if( !empty($encabezadoKardex->observacionesGenerales) ){
		$pendientesEncabezado[] = "Observaciones Generales";
		$pendientesObservaciones[] = $encabezadoKardex->observacionesGenerales;
	}
	
	if( !empty( $encabezadoKardex->cuidadosIntensivos ) ){
		$pendientesEncabezado[] = "Cuidados de enfermer&iacute;a";
		$pendientesObservaciones[] = $encabezadoKardex->cuidadosIntensivos;
	}
	
if( true ){
			
	echo "<div id='dvInfoKardex'>";
	
	echo "<table align='center' style='width:90%'>";
	
	if( !empty( $medicos[0]->nombre1 ) || !empty( $encabezadoKardex->diagnostico ) ){
		echo "<tr class='encabezadotabla'>";
		echo "<td align='center'>Diagn&oacute;stico</td>";
		echo "<td align='center'>M&eacute;dicos tratantes</td>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td  style='width:50%' class='fila2'>";
		echo htmlentities( $encabezadoKardex->diagnostico, ENT_QUOTES );
		echo "</td>";
		
		echo "<td style='width:50%' class='fila2'>";
		
		if( count($medicos) > 0 ){
			
			echo "<table>";
			echo "<tr>";
			echo "<td>";
			foreach( $medicos as $med ){
				//$med = new medicoDTO( );
				echo htmlentities( $med->nombre1." ".$med->nombre2." ".$med->apellido1." ".$med->apellido2, ENT_QUOTES )."<br>";
				
			}
			echo "</td>";
			echo "</tr>";
			echo "</table>";
		}
		
		echo "</td>";
		echo "</tr>";
	}
	
	echo "</table>";
	
	echo "<table align='center' style='width:90%'>";
	
	if( !empty($dextrometer->codigo) || count($liquidos->liquidos) > 0 || count( $examenes ) > 0 ){
		//Aqui comienza los campos de controles
		echo "<tr class='encabezadotabla'>";
		echo "<td align='center' colspan='2'>Controles</td>";
		echo "</tr>";
		
		if( count($liquidos->liquidos) > 0 ){
			
			echo "<tr class='fila1'>";
			echo "<td colspan='2'><b>Liquidos endovenosos</b></td>";
			
			echo "<tr><td align='center' colspan='2'>";
			echo "<table>";
			echo "<tr class='encabezadotabla'>";
			echo "<td style='font-size:10pt'><b>Componentes</b></td>";
			echo "<td style='font-size:10pt'><b>Observaciones</b></td>";
			echo "</tr>";
			
	
			for( $i = 0; $i < count($liquidos->liquidos); $i++ ){
				
				$class="class='fila".( ($i%2) + 1 )."'";
//				$i++;
				
				//Mostrando liquidos
				echo "<tr $class>";
				
				echo "<td>";			
				echo $liquidos->getDescripcionTextoHtml( $i );
				echo "</td>";
				
				echo "<td>";			
				echo $liquidos->getObservaciones( $i );
				echo "</td>";
				
				echo "</tr>";
			}
			
			echo "</table>";
		
			echo "</tr>";
		}

		
		if( count( $examenes ) > 0 ){
			echo "<tr class='fila1'>";
			echo "<td colspan='2'><b>Ex&aacute;menes</b></td>";
			echo "</tr>";
			
			echo "<tr>";
			echo "<td align='center' colspan='2'>";
			
			echo "<table>";
				
				echo "<tr class='encabezadotabla'>";
				echo "<td style='font-size:10pt'><b>Examen</b></td>";
				echo "<td style='font-size:10pt'><b>Observaciones</b></td>";
				echo "<td style='font-size:10pt'><b>Fecha</b></td>";
				echo "<td style='font-size:10pt'><b>Estado</b></td>";
				echo "</tr>";
				
				$i = 0;
				foreach( $examenes as $exa ){
					
					$class="class='fila".( ($i%2) + 1 )."'";
					
					//$exa = new ExamenKardexDTO();
					echo "<tr $class>";
					echo "<td>$exa->descripcionExamen</td>";
					echo "<td>".htmlentities( $exa->observaciones, ENT_QUOTES )."</td>";
					echo "<td>$exa->fechaDeSolicitado</td>";
					echo "<td>".htmlentities( consultarEstadoExamen( $exa->estado ), ENT_QUOTES )."</td>";
					echo "</tr>";
					
					$i++;
				}
				
			echo "</table>";
			
			echo "</td>";
			echo "</tr>";
		}
		
		if( !empty($dextrometer->codigo) ){
			
			echo "<tr class='fila1'>";
			echo "<td colspan='2'><b>Dextrometer</b></td>";
			echo "</tr>";
	
			echo "<tr>";
			echo "<td colspan=2>";
			
			echo "<table align='center'>";
			
			//$dextrometer = new ArticuloDTO();
			
			echo "<tr>";
			echo "<td class='fila1'>Insulina</td>";
			echo "<td class='fila2'>";
			echo htmlentities( $dextrometer->codigo." - ".consultarMedicamento( $dextrometer->codigo ), ENT_QUOTES ); 
			echo "</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td class='fila1'>Frecuencia</td>";
			echo "<td class='fila2'>";
			echo htmlentities( consultarFrecuenciaDextrometer( $dextrometer->frecuencia ), ENT_QUOTES ); 
			echo "</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td class='fila1'>Observaciones</td>";
			echo "<td class='fila2'>";
			echo htmlentities( $encabezadoKardex->observacionesDextrometer, ENT_QUOTES ); 
			echo "</td>";
			echo "</tr>";
				
			echo "</table>";
			
			echo "<br>";
			
			echo "<table align=center>";
			
			echo "<tr class='encabezadotabla'>";
			echo "<td style='font-size:10pt'><b>M&iacute;nimo</b></td>";
			echo "<td style='font-size:10pt'><b>M&aacute;ximo</b></td>";
			echo "<td style='font-size:10pt'><b>Dosis</b></td>";
			echo "<td style='font-size:10pt'><b>V&iacute;a</b></td>";
			echo "<td style='font-size:10pt'><b>Observaciones</b></td>";
			echo "</tr>";
			
			$i = 0;
			foreach( $esquemaDextrometer as $esq ){
				
				$class="class='fila".( ($i%2) + 1 )."'";
				$i++;
				
				//$esq = new IntervaloDextrometerDTO();
				echo "<tr $class>";
				echo "<td>";
				echo htmlentities( $esq->minimo, ENT_QUOTES );
				echo "</td>";
				echo "<td>";
				echo htmlentities( $esq->maximo, ENT_QUOTES );
				echo "</td>";
				echo "<td>";
				echo htmlentities( $esq->dosis." ".consultarUnidadManejo( $esq->unidadDosis ), ENT_QUOTES );
				echo "</td>";
				echo "<td>";
				echo htmlentities( consultarVia( $esq->via ), ENT_QUOTES );
				echo "</td>";
				echo "<td>";
				echo htmlentities( $esq->observaciones, ENT_QUOTES );
				echo "</td>";
				echo "</tr>";
			}
			echo "</table>";
		
			echo "</td>";
			echo "</tr>";
		}
	}
	
	
	
	//Pendientes
	if( !empty($encabezadoKardex->sondas) 
		|| !empty($encabezadoKardex->aislamiento)
		|| !empty($encabezadoKardex->terapia)
		|| !empty($encabezadoKardex->cirugiasPendiente)
		|| !empty($encabezadoKardex->observacionesGenerales)
		|| !empty( $encabezadoKardex->cuidadosIntensivos )
		|| !empty( $dietas[0]->descripcionDieta )
	){
		echo "<tr class='encabezadotabla'>";
		echo "<td align='center' colspan='2'>Pendientes</td>";
		echo "</tr>";
		
		
		if( /*count( $dietas ) > 0 &&*/ !empty( $dietas[0]->descripcionDieta ) ){
			
			echo "<tr>";
			echo "<td colspan='2' class='fila1'><b>Dietas</b></td>";
			echo "</tr>";
			
			echo "<tr>";
			echo "<td colspan='2'>";
			
			echo "<table align='center'>";
			echo "<tr>";
			echo "<td>";
			
			echo "<tr class='encabezadotabla'>";
			echo "<td style='font-size:10pt'><b>Descripci&oacute;n</b></td>";
			echo "<td style='font-size:10pt'><b>Observaciones</b></td>";
			echo "</tr>";
			
			echo "<td style='width:50%' class='fila2'>";
			foreach( $dietas as $die ){
					
				//$die = new dietaKardexDTO();
				
				echo htmlentities( $die->descripcionDieta, ENT_QUOTES )."<br>";
			}
			echo "</td>";
			
			echo "<td class='fila2'>";
			echo htmlentities( $encabezadoKardex->observacionesDietas, ENT_QUOTES );
			echo "</td>";
			
			echo "</tr>";
			
			echo "</table>";
			
			echo "</td>";
			echo "</tr>";
		}
		
		
		
		for( $i = 0; $i < count($pendientesEncabezado); $i = $i+2 ){
			
			echo "<tr class='fila1'>";
			for( $j = 0; $j < 2; $j++ ){
				echo "<td style='width:50%'><b>".@$pendientesEncabezado[$i+$j]."</b></td>";
			}
			echo "</tr>";
			
			echo "<tr class='fila2'>";
			for( $j = 0; $j < 2; $j++ ){
				echo "<td style='width:50%'>".@str_replace( "\r", "<br>", $pendientesObservaciones[$i+$j] )."</td>";
			}
			echo "</tr>"; 
		}
		
	}
	//Fin de pendientes
	
	if(  count( $mezclasA ) > 0 || count( $mezclasU ) > 0 ){
		echo "<tr class='encabezadotabla'>";
		echo "<td align='center' colspan='2'>Mezclas</td>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td colspan='2'>";
		
		if( count( $mezclasA ) > 0 ){
			echo "<table align='center'>";
			
			echo "<tr>";
			echo "<td colspan='6' align='center' class='fila1'><b>ANALGESIAS</b></td>";
			echo "</tr>";
			
			echo "<tr class='encabezadotabla'>";
			echo "<td style='font-size:10pt;'>Nombre</td>";
			echo "<td style='font-size:10pt;'>Dosis</td>";
			echo "<td style='font-size:10pt;'>Frecuencia</td>";
			echo "<td style='font-size:10pt;'>V&iacute;a</td>";
			echo "<td style='font-size:10pt;'>Condici&oacute;n</td>";
			echo "<td style='font-size:10pt;'>Observaciones</td>";
			echo "</tr>";
			
			$i = 0;
			foreach( $mezclasA as $det ){
				
				$class = "class='fila".(($i%2)+1)."'";
				$i++;
				
				echo "<tr $class>";
				
				//$det = new detalleKardexDTO();
				echo "<td>";
				echo $det->codigoArticulo;
				echo "</td>";
				
				echo "<td>";
				echo $det->cantidadDosis." ".$det->unidadDosis;
				echo "</td>";
				
				echo "<td>";
				echo htmlentities( consultarFrecuencia( $det->periodicidad ), ENT_QUOTES );
				echo "</td>";
				
				echo "<td>";
				echo htmlentities( consultarVia( $det->via ), ENT_QUOTES );
				echo "</td>";
				
				echo "<td>&nbsp;";
				echo htmlentities( consultarCondicion( $det->condicionSuministro ), ENT_QUOTES );
				echo "</td>";
				
				echo "<td>";
				echo $det->observaciones;
				echo "</td>";
				
				echo "</tr>";
				
			}
			
			echo "</table>";
		}
		
	if( count( $mezclasU ) > 0 ){
			echo "<table align='center'>";
			
			echo "<tr>";
			echo "<td colspan='6' align='center' class='fila1'><b>Nutriciones</b></td>";
			echo "</tr>";
			
			echo "<tr class='encabezadotabla'>";
			echo "<td style='font-size:10pt;'>Nombre</td>";
			echo "<td style='font-size:10pt;'>Dosis</td>";
			echo "<td style='font-size:10pt;'>Frecuencia</td>";
			echo "<td style='font-size:10pt;'>V&iacute;a</td>";
			echo "<td style='font-size:10pt;'>Condici&oacute;n</td>";
			echo "<td style='font-size:10pt;'>Observaciones</td>";
			echo "</tr>";
			
			$i = 0;
			foreach( $mezclasU as $det ){
				
				$class = "class='fila".(($i%2)+1)."'";
				$i++;
				
				echo "<tr $class>";
				
				//$det = new detalleKardexDTO();
				echo "<td>";
				echo $det->codigoArticulo;
				echo "</td>";
				
				echo "<td>";
				echo $det->cantidadDosis." ".$det->unidadDosis;
				echo "</td>";
				
				echo "<td>";
				echo htmlentities( consultarVia( $det->via ), ENT_QUOTES );
				echo "</td>";
				
				echo "<td>";
				echo htmlentities( consultarVia( $det->via ), ENT_QUOTES );
				echo "</td>";
				
				echo "<td>&nbsp;";
				echo htmlentities( consultarCondicion( $det->condicionSuministro ), ENT_QUOTES );
				echo "</td>";
				
				echo "<td>";
				echo $det->observaciones;
				echo "</td>";
				
				echo "</tr>";
				
			}
			
			echo "</table>";
		}
		
		echo "</td>"; 
		echo "</tr>";
	}
	
	if( count( $quimitoterapia ) > 0 ){
		echo "<tr class='encabezadotabla'>";
		echo "<td align='center' colspan='2'>Quimioterpia</td>";
		echo "</tr>";
		
		echo "<tr><td colspan='2'>";
		if( count( $quimitoterapia ) > 0 ){
			
			echo "<table align='center'>";
			
			echo "<tr class='encabezadotabla'>";
			echo "<td style='font-size:10pt;'>Nombre</td>";
			echo "<td style='font-size:10pt;'>Dosis</td>";
			echo "<td style='font-size:10pt;'>Frecuencia</td>";
			echo "<td style='font-size:10pt;'>V&iacute;a</td>";
			echo "<td style='font-size:10pt;'>Condici&oacute;n</td>";
			echo "<td style='font-size:10pt;'>Observaciones</td>";
			echo "</tr>";
			
			$i = 0;
			foreach( $quimitoterapia as $det ){
				
				$class = "class='fila".(($i%2)+1)."'";
				$i++;
				
				echo "<tr $class>";
				
				//$det = new detalleKardexDTO();
				echo "<td>";
				echo $det->codigoArticulo;
				echo "</td>";
				
				echo "<td>";
				echo $det->cantidadDosis." ".$det->unidadDosis;
				echo "</td>";
				
				echo "<td>";
				echo htmlentities( consultarFrecuencia( $det->periodicidad ), ENT_QUOTES );
				echo "</td>";
				
				echo "<td>";
				echo htmlentities( consultarVia( $det->via ), ENT_QUOTES );
				echo "</td>";
				
				echo "<td>&nbsp;";
				echo htmlentities( consultarCondicion( $det->condicionSuministro ), ENT_QUOTES );
				echo "</td>";
				
				echo "<td>";
				echo $det->observaciones;
				echo "</td>";
				
				echo "</tr>";
			}
			
			echo "</table>";
		}
		echo "</td></tr>";
	}
	
	echo "</table>";
	
	echo "</div>";
}
}

/**
 * Consulta las habitaciones ocupadas por Cco
 * 
 * @param $basedatos
 * @param $servicio
 * @return unknown_type
 */
function consultarHabitacionesCco( $basedatos, $servicio ){
	
	global $wemp_pmla;
	
	$conexion = obtenerConexionBD("matrix");

	$q = "SELECT
			Habcod, Habcco, Habhis, Habing,
			(SELECT CONCAT(pacno1,' ', pacno2,' ', pacap1,' ', pacap2) FROM root_000036, root_000037 WHERE oriced = pacced AND oriori = '01' AND orihis = Habhis AND oriing = Habing AND Oritid = Pactid) nombre
		FROM
			{$basedatos}_000020
		WHERE
			Habcco = '$servicio'
			AND Habdis = 'off'
			AND Habhis != ''
			AND Habest = 'on'
		UNION
		SELECT 'Urgencias'Habcod, Ubisac Habcco, Ubihis Habhis, Ubiing Habing, IFNULL((
		SELECT CONCAT( pacno1, ' ', pacno2, ' ', pacap1, ' ', pacap2 )
		FROM root_000036, root_000037
		WHERE oriced = pacced AND oriori = '01' AND orihis = Ubihis AND oriing = Ubiing AND Oritid = Pactid ) , '')nombre
		FROM
			{$basedatos}_000018, {$basedatos}_000011
		WHERE
			Ccourg = 'on'
			AND Ccocod = Ubisac
			AND Ccoest = 'on'
			AND Ccocod = '$servicio'
			AND {$basedatos}_000018.fecha_data >= DATE_FORMAT(DATE_SUB(NOW(),INTERVAL 2 DAY),'%Y-%m-%d')
		ORDER by 1,5;";

	$res = mysql_query($q, $conexion);
	$num = mysql_num_rows($res);

	//Con tabla
	$consulta = "<table style='width:100%'>";

	$cont1 = 0;
	$clase = 'fila1';

	if($num > 0){
		$consulta = $consulta."<tr class=encabezadoTabla align=center>";
		$consulta = $consulta."<td style=font-size:10pt>Habitacion</td>";
		$consulta = $consulta."<td style=font-size:10pt>Historia</td>";
		$consulta = $consulta."<td style=font-size:10pt>Paciente</td>";
		$consulta = $consulta."<td style=font-size:10pt>Accion</td>";
		$consulta = $consulta."</tr>";

		while ($cont1 < $num)
		{
			$rs = mysql_fetch_array($res);

			if(isset($rs['nombre']) && $rs['nombre'] != ''){
				$consulta = $consulta."<tr class='$clase'>";
				if($clase == 'fila2'){
					$clase = 'fila1';
				} else {
					$clase = 'fila2';
				}

				$consulta = $consulta."<td align=center>".$rs['Habcod']."</td>";
				$consulta = $consulta."<td align=center>".$rs['Habhis']."-".$rs['Habing']."</td>";
				$consulta = $consulta."<td>".$rs['nombre']."</td>";
				$consulta = $consulta."<td align='center'><INPUT type='button' onClick='javascript: entregarTurno( \"consultaKardex.php?wemp_pmla=$wemp_pmla&wbasedato=$basedatos&his={$rs['Habhis']}&cco=$servicio\" );' value='Ver'></td>";
				$consulta = $consulta."</tr>";
			}
			$cont1++;
		}
	} else {
		$consulta = $consulta."<tr><td colspan=3 class=encabezadoTabla>No se encontraron pacientes</td></tr>";
	}
	$consulta = $consulta."</table>";

//	liberarConexionBD($conexion);

	return $consulta;
}

function  elegirCco( $Ccos ){
	
	echo "<select id='slCco' style='width:100%' onChange='javascript: seleccionarCco( this );'>";
	echo "<option>Selecccion centro de costos...</option>";
	
	if( count($Ccos) > 0 ){
		
		foreach( $Ccos as $cco ){
			
			//$cco = new centroCostosDTO();
	
			echo "<option>";
			echo $cco->codigo."-".$cco->nombre;
			echo "</option>";
		}
	}
	
	echo "</select>";
}

function consultarFrecuencia( $codigo ){
	
	global $conex;
	global $wbasedato;
	
	$sql = "SELECT
				CONCAT(percan,' ',peruni ) as frecuencia
			FROM
				{$wbasedato}_000043 a
			WHERE
				percod = '$codigo'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query de Medico Tratante $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $rows = mysql_fetch_array( $res ) ){
		return $rows['frecuencia'];
	}
	else{
		return "";
	}
}

function consultarVia( $codigo ){
	
	global $conex;
	global $wbasedato;
	
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000040 a
			WHERE
				viacod = '$codigo'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query de Medico Tratante $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $rows = mysql_fetch_array( $res ) ){
		return $rows['Viades'];
	}
	else{
		return "";
	}
}

function consultarCondicion( $codigo ){
	
	global $conex;
	global $wbasedato;
	
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000042 a
			WHERE
				concod = '$codigo'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query de Medico Tratante $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $rows = mysql_fetch_array( $res ) ){
		return $rows['Condes'];
	}
	else{
		return "";
	}
}


/**
 * 
 * @param $codigo
 * @return unknown_type
 */
function consultarEstadoExamen( $codigo ){
	
	global $conex;
	global $wbasedato;
	
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000045 a
			WHERE
				eexcod = '$codigo'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query de Medico Tratante $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $rows = mysql_fetch_array( $res ) ){
		return $rows['Eexdes'];
	}
	else{
		return "";
	}
}

/**
 * Consulta la frecuencia del dextrometer
 * @param $codigo
 * @return unknown_type
 */
function consultarFrecuenciaDextrometer( $codigo ){
	
	global $conex;
	global $wbasedato;
	
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000042 a
			WHERE
				concod = '$codigo'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query de Medico Tratante $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $rows = mysql_fetch_array( $res ) ){
		return $rows['Condes'];
	}
	else{
		return "";
	}
}

/**
 * Consulta las unidades de manejo
 * @param $codigo
 * @return unknown_type
 */
function consultarUnidadManejo( $codigo ){
	
	global $conex;
	global $wbasedato;
	
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000027 a
			WHERE
				unicod = '$codigo'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query de Medico Tratante $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $rows = mysql_fetch_array( $res ) ){
		return $rows['Unides'];
	}
	else{
		return "";
	}
}


/**
 * Muestra los datos demográficos del paciente en pantalla
 * @param $paciente
 * @return unknown_type
 */
function mostrarDatosDemograficos( $paciente ){
	
	//$paciente = new pacienteKardexDTO();
	
	
	echo "<table align='center' style='width:90%; font-size:14pt' class='demograficos'>";
	
	echo "<tr align='center'>";
	echo "<td class='fila1' style='font-size:14pt;'>Habitaci&oacute;n</td>";
	echo "<td class='fila1' style='font-size:14pt;'>Identificaci&oacute;n</td>";
	echo "<td class='fila1' style='font-size:14pt;'>Historia</td>";
	echo "<td class='fila1' style='font-size:14pt;'>Nombre</td>";
	echo "<td class='fila1' style='font-size:14pt;'>Edad</td>";
	echo "</tr>";
	
	echo "<tr>";
	
	echo "<td class='encabezadotabla' align='center' style='font-size:22pt; color:yellow'>";
	echo "<b>".$paciente->habitacionActual."</b>";
	echo "</td>";
	
	echo "<td class='fila2' align='center' style='font-size:14pt'>";
	echo $paciente->tipoDocumentoIdentidad."-".$paciente->documentoIdentidad;
	echo "</td>";
	
	echo "<td class='fila2' align='center' style='font-size:14pt'>";
	echo $paciente->historiaClinica;
	echo "</td>";

	
	echo "<td class='fila2' align='center' style='font-size:14pt'>";
	echo $paciente->nombre1." ".$paciente->nombre2." ".$paciente->apellido1." ".$paciente->apellido2;
	echo "</td>";
	
	
	//$paciente = new pacienteKardexDTO();
	echo "<td class='fila2' align='center' style='font-size:14pt'>";
	echo calculoEdad( $paciente->fechaNacimiento );
	echo "</td>";
	
	echo "</tr>";
	
	echo "</table><br><br>";
	
	return;
}

function tieneKardex( $his, $ing ){
	
	global $conex;
	global $wbasedato;
	
	$this->liquidos = Array();
	
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000053 a
			WHERE
				karhis = '$his'
				AND karing = '$ing'
				AND fecha_data = '".date( "Y-m-d" )."'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query de Medico Tratante $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $rows = mysql_fetch_array( $res ) ){
		return true;
	}
	else{
		return false;
	}
}

/******************************************************************************
 * 								FIN DE FUNCIONES
 ******************************************************************************/


/******************************************************************************
 * 								INICIO PROGRAMA
 ******************************************************************************/

include_once("movhos/kardex.inc.php");
include_once("root/comun.php");

if(!isset($wemp_pmla)){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}
$key = substr($user, 2, strlen($user));
$usuario = $key;

$usuarioEntrega = consultarUsuarioKardex( $key );

$conex = obtenerConexionBD("matrix");

$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

//$wbasedato = $institucion->baseDeDatos;
$wentidad = $institucion->nombre;

if( @!$consultaAjax ){
	encabezado("CONSULTA DE KARDEX", "2009-11-30", "clinica" );
}

//El usuario se encuentra registrado
if( !isset($_SESSION['user']) ){
	echo "Error: Usuario No registrado";
}
else{
	
	if( @$consultaAjax ){
		
		switch( $consultaAjax ){
			
			case "10":{
				$fotoString = "";
				foreach( $foto as $ft ){
					$fotoString .= $ft;
				}
				
				$val = grabarEntregaTurno( $cco, $habitacion, $his, $ing, $observaciones, $usuEntrega, $usuRecibe, utf8_decode( $fotoString ), $turno );
			}
			break;
			
			case "11":{
				$val = validarUsuarioRecibe( $codigo );
			}
			break;
			
			case "11":{
				$val = actualizarEntregaTurnos( $id, $observacioness );
			}
			break;
			
			case "12":{
				echo consultarHabitacionesCco( $wbasedato, $cco );
			} break;
		}
		
	}
	else{

?>

<head>
<style type="text/css">

	table td{
		font-family :verdana;
		font-size: 10pt;
	}
	
	table {
		width : 100%;
	}
	
	body{
		font-size: 10pt;
	}
	
	.demograficos{
		font-size: 14pt;
	}
	
	.encabezadotabla td{
		font-size: 12pt;
	}
	
	.encabezadotabla1 encabezadotabla td{
	
</style>

<script type="text/javascript">

/************************************************************
 * AJAX
 ***********************************************************/

 	/******************************************************************
 	 * Realiza una llamada ajax a una pagina
 	 * 
 	 * met:		Medtodo Post o Get
 	 * pag:		Página a la que se realizará la llamada
 	 * param:	Parametros de la consulta
 	 * as:		Asincronro? true para asincrono, false para sincrono
 	 * fn:		Función de retorno del Ajax
 	 *
 	 * Nota: Si la llamada es GET las opciones deben ir con la pagina.
 	 ******************************************************************/
	function consultasAjax( met, pag, param, as, fn ){
		
		this.metodo = met;
		this.parametros = param; 
		this.pagina = pag;
		this.asc = as;
		this.fnchange = fn; 

		try{
			this.ajax=nuevoAjax();
	
			this.ajax.open( this.metodo, this.pagina, this.asc );
			this.ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			this.ajax.send( this.parametros );
	
			if( this.asc ){
				var xajax = this.ajax;
//				this.ajax.onreadystatechange = this.fnchange;
				this.ajax.onreadystatechange = function(){ fn( xajax ) };
				
				if ( !estaEnProceso(this.ajax) ) {
					this.ajax.send(null);
				}
			}
			else{
				return this.ajax.responseText;
			}
		}catch(e){	}
	}

/************************************************************
 * FIN DE AJAX
 ***********************************************************/

	function respuestaValidar( ajax ){

		if( ajax.readyState==4 && ajax.status==200 ){

			var info = document.getElementById( "dvInfoRecibe" );

			info.innerHTML = ajax.responseText;
		}
	}
 
	function validar(){

		var hiRecibe = document.getElementById( "idUsuRecibeValidar" ).value;

		if( hiRecibe != '' ){

			respuesta = consultasAjax( "POST", "entregaTurnos.php",
										"codigo=" + hiRecibe +
										"&consultaAjax=11"+
										"&wbasedato="+wbasedato+
										"&wemp_pmla=01",
										true, respuestaValidar 
									 );
			 
		}
		else{
			alert( "Ingrese el codigo matrix de quien recibe" );
		}
	}

	function respuestaGrabar( ajax ){

		if( ajax.readyState==4 && ajax.status==200 ){

			if( ajax.responseText != '' ){
				alert( "Error al insertar datos:" + ajax.responseText );
			}
			else{

				var dvG = document.getElementById( "dvMsjGuardado" );
				var cbG = document.getElementById( "cbGrabar" );
				var btA = document.getElementById( "btAceptar" );

				cbG.parentNode.parentNode.style.display='none';
				btA.parentNode.parentNode.style.display='none';
				dvG.parentNode.parentNode.style.display='';
			}
		}
	}
 


	function respuestaSeleccionarCco( ajax ){

		if( ajax.readyState==4 && ajax.status==200 ){

			if( document.getElementById( "slCco" ).selectedIndex > 0 ){
				document.getElementById( "dvCcos" ).innerHTML = ajax.responseText;
			}
			else{
				document.getElementById( "dvCcos" ).innerHTML = "";
			} 
		}
	}
	
	function seleccionarCco( campoCco ){

		var txCco = campoCco.options[ campoCco.selectedIndex ].text;

		if( txCco ){
			var cco = txCco.split( '-' );
	
			if( cco ){

				var wbasedato = document.getElementById( "wbasedato" ).value;
				
				respuesta = consultasAjax( "POST", "consultaKardex.php",
											"&cco=" + cco[0] +
											"&consultaAjax=12"+
											"&wbasedato="+wbasedato+
											"&wemp_pmla=01",
											true, respuestaSeleccionarCco 
					 					);
			}
		}
	}
	
	function onClickGrabar( grabar ){

		/*if( grabar.checked == true ){
			document.getElementById( "btAceptar" ).disabled = 'false';
		}
		else{
			document.getElementById( "btAceptar" ).disabled = 'true';
		}*/
	}
	
	function recargar(){

		var grabar = document.getElementById( "cbGrabar" );

		if( grabar != undefined ){
			
			if( grabar.checked == true ){

				var slRecibe = document.getElementById( "idUsuRecibe" );

				if( slRecibe && slRecibe.value != '' ){

					var usuRecibe = slRecibe.value;

					var usuEntrega = document.getElementById( "idUsuEntrega" ).value;

					if( usuEntrega != usuRecibe ){
						
						var dvAux = document.createElement( "div" );

						dvAux.innerHTML = "<INPUT type='hidden' name='foto'>";

						dvAux.firstChild.value = document.getElementById( "dvInfoKardex" ).innerHTML;

						document.forms[0].appendChild( dvAux.firstChild );

						document.forms[0].submit();
					}
					else{
						alert( "El usuario que entrega debe ser distinto del que recibe" );
					}
				}
				else{
					alert( "No se encuentra el código de quien recibe" );
				}
			}
			else{
				alert( "Debe estar marcado la GRABAR" );
			}
		}
		else{
			alert( "Error en el script" );
		}
	}

	function entregarTurno( url ){

		location.href = url;
	}

	window.onload = function(){

		if( document.getElementById( "dvConsulta" ) ){
			if( document.getElementById( "dvConsulta" ).firstChild.firstChild.rows.length == 0 ){
				document.getElementById( "dvConsulta" ).innerHTML = "<center><h3><b>SIN INFORMACION PARA MOSTRAR</b></h3></center>";
			}
		}
		

		var auxCco = document.getElementById( "hiCco" );
		var auxSelCco = document.getElementById( "slCco" );

		if( auxCco ){

			if( auxCco.value ){

				for( var i = 0; i < auxSelCco.options.length; i++ ){

					var text = auxSelCco.options[ i ].text.split( "-" ); 

					if( text[0] == auxCco.value ){
						auxSelCco.selectedIndex = i;
						auxSelCco.onchange();
						return;
					}
				}
				
			}
		}
		else{
			if( auxSelCco ){
				auxSelCco.onchange();
			}
		}
	}
 
</script>

</head>




<?php 
	//Si no se selecciona piso, el usuario debe seleccionar el piso 
//	if( !isset($cco) ){
	if( !isset($his) ){
		
		echo "<form method='post'>";
		
		$Ccos = consultarCentrosCostosHospitalarios();
		
		echo "<table style='width:50%' align='center'>";
	
		echo "<tr>";
		echo "<td class='fila1' style='width:30%'>Servicio</td>";
		echo "<td>";

		elegirCco( $Ccos );	
		
		echo "</td>";
		echo "</tr>";
		
		echo "<td colspan='2' align='center'>"; 
		echo "<div id='dvCcos'>";
		echo "</div></td>";
	
		echo "</table>";
		
		
//		elegir_centro_de_costo();
		
//		echo consultarHabitacionPacienteServicio( 'movhos', '1182' );
		
		echo "<br>";
		echo "<table align='center' style='width:300'>";
		
		echo "<tr>";
		
//		echo "<td align='center'>";
//		echo "<INPUT type='submit' value='Aceptar' style='width:100'>";
//		echo "</td>";
		
		echo "<td align='center'>";
		echo "<INPUT type='submit' value='Cerrar ventana' onClick='javascript: cerrarVentana();' style='width:100'>";
		echo "</td>";
		
		echo "</tr>";
		
		echo "</table>";
		
		
		echo "<INPUT type='hidden' name='wemp_pmla' id='wemp_pmla' value='$wemp_pmla'>";
		echo "<INPUT type='hidden' name='wbasedato' id='wbasedato' value='$wbasedato'>";
		
		if( isset( $cco ) ){
			echo "<INPUT type='hidden' name='hiCco' id='hiCco' value='$cco'>";
		}
		
		echo "</form>";
		
	}
	else{
		$fecha = date( "Y-m-d" );
		
		//consultarHabitacionPacienteServicio
		
		//Recibiendo habitación
		
		//Consultando información del paciente
		$infoPaciente = new pacienteKardexDTO();
		$infoPaciente = consultarInfoPacienteKardex( $his, "" );
		$ing = $infoPaciente->ingresoHistoriaClinica;
		
		$liquidos = new liquidos( $his, $ing, $fecha );
		
		$encabezadoKardex = new infoEncabezadoKardex( $his, $ing, $fecha );

		//mostrando información en pantalla
		mostrarDatosDemograficos( $infoPaciente );
		
		if( !tieneKardex( $his, $ing ) ){
			echo "<center><b>EL PACIENTE NO TIENE KARDEX ELECTRONICO EL DÍA DE HOY</b></center>";
		}
		else{
			echo "<div id='dvConsulta'>";
			consultarKardexActual( $his, $ing );
			echo "</div>";
		}
		
		echo "<br><br>";
		echo "<table align='center' style='width:20%'>";
		echo "<tr>";
		echo "<td align='center'>";
//		echo "<a href='entregaTurnos.php?wbasedato=$wbasedato&wemp_pmla=$wemp_pmla'>Regresar</a>";
		echo "<INPUT type='button' value='Regresar' onClick='javascript: location.href=\"consultaKardex.php?wbasedato=$wbasedato&wemp_pmla=$wemp_pmla&cco=$cco\"' style='width:100'>";
		echo "</td>";
		
		echo "<td align='center'>";
		echo "<INPUT type='button' value='Cerrar ventana' onClick='javascript: cerrarVentana();' style='width:100'>";
		echo "</td>";
		
		echo "</tr>";
		echo "</table>";
	}
}
}
?>