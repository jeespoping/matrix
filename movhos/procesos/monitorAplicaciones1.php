<html>
<head>

<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />

<script type="text/javascript" src="../../../include/root/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.core.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.tabs.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.draggable.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>


<script type="text/javascript" src="../../../include/root/jquery.dimensions.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.simple.tree.js"></script>

<style>
	*#col1 { border: 3px solid black; }

	.fondoAlertaConfirmar            
	{
		 background-color: #8181F7;
		 color: #000000;
		 font-size: 10pt;
	}

	.fondoAlertaEliminar            
	{
		 background-color: #F5D0A9;
		 color: #000000;
		 font-size: 10pt;
	}
</style>

<script>
	function fnMostrar( celda ){
	
		if( $("div", celda ) ){
	
			$.blockUI({ message: $("div", celda ), 
							css: { left: ( $(window).width() - 800 )/2 +'px', 
								    top: ( $(window).height() - $("div", celda ).height() )/2 +'px',
								  width: '800px'
								 } 
					  });
			
		}
	}
	
	function recargar(){
		document.getElementById( "mostrar" ).value = "on";
		document.forms[0].submit();		
	}
	
	function abrirNuevaVentana( path ){
		window.open(path,'','fullscreen=no, status=no, menubar=no, toolbar=no, directories=no, resizable=yes, scrollbars=yes,titlebar=yes'); 
	}
	
	/****************************************************************************************
	 * Esta funcion muestra un tooltip para las leyendas
	 ****************************************************************************************/
	function mostrarTooltip( celda ){
	
		if( !celda.tieneTooltip ){
			$( "*", celda ).tooltip();
			celda.tieneTooltip = 1;
		}
	}
	
	//FUNCION QUE PERMITE GENERAR UNA VENTANA EMERGENTE CON UN PATH ESPECIFICO
    function ejecutar(path)
    {
    window.open(path,'','fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=0,scrollbars=1,titlebar=0');
    }
	
	/****************************************************************************************************
	 * Envento que se ejecuta al cargar la pagina, inicia un blink sobre las etiquetas con blink y
	 * la cuenta para recargar la pagina
	 ****************************************************************************************************/
	window.onload = function(){
		setInterval( "parpadear();", 500 );
		
		if( document.getElementById( "inRecargar" ).value == "on" ){			
			setTimeout( "recargar();", 5*60000 );	//Recarga la pagina cada 5 minutos
		}
	}
</script>
</head>
<body>
<?php
include_once("conex.php");
//TABLA TEMPORAL Y CONSULTANDO TODOS LOS MEDICAMENTOS DE LA 15 EN UN SOLO PASO

/**********************************************************************************************************************************************************
 * Fecha de creacion:	Diciembre 19 de 2011
 * Por:					Edwin Molina Grisales
 * Descripci?n general:	Mostrar las aplicaciones faltantes por cama por cada ronda, basada en el kardex
 **********************************************************************************************************************************************************/
 
/**********************************************************************************************************************************************************
 * Especificaciones:
 *
 * - Por cada cama mostrar en que rondas no se ha aplicado medicamentos, estas estar?n de color rojo
 * - Por cada cama que se encuentre parcialmente aplicada, se encontrar? de color amarillo
 * - Por cada cama que se encuentre totalmente aplicada, se encontrar? de color verde
 * - Al dar click sobre una hora, mostrar un peque?o detalle de como se encuentra las aplicaciones del paciente para la hora determinada
 **********************************************************************************************************************************************************/

/**********************************************************************************************************************************************************

 * Modificaciones:
 * Junio 27 de 2012 Viviana Rodas.  Se agrega el llamado a las funciones consultaCentrosCostos que hace la consulta de los centros de costos 
 * 									de un grupo seleccionado y dibujarSelect que dibuja el select con los centros de costos obtenidos de la primera *                                  funcion. 
 * Mayo 11 de 2012		Edwin MG.	Se corrige el titulo del minireporte, oculto en la celdas con alguna imagen.
 * Mayo 10 de 2012		Edwin MG.	Se cambia la funcion consultarJustificacion para que detecte la hora de aplicacion como {Horamilitar} - {AM|PM}
 * Febrero 28 de 2012	Edwin MG.	Se toma en cuenta los cambios de frecuencia para el medicamento, esto para evitar que salgan x rojas por los cambios de frecuencia.
 * Enero 07 de 2012		Edwin MG.	Para tomar los medicamentos sin confirmar, se basa en el d?a que se coja como base, es decir, si el kardex esta sin confirmar
 *									se basa en el d?a anterior, por tanto parar determinar los medicamentos sin confirmar se basa en el d?a anterior, si tiene 
 *									kardex y esta confirmado se basa en el d?a actual.
 * Enero 06 de 2012		Edwin MG.	Si un articulo reemplaza a otro no se muestra las rondas anteriores
 *									Si un articulo generico esta sin confimar se muestra
 * Enero 31 de 2012		Edwin MG.	Se coloca el mensaje de kardex sin generar y kardex sin confirmar bajo, las imagenes, y no se quita el enlace al kardex
 * Enero 24 de 2012.	Edwin MG.	Se muestra una x amarillo si el medicamento es a necesisdad o tiene justificacion, o los medicamentos son todos a 
 * 									necesidad o tienen justificacion y no se aplico nada
 * Enero 10 de 2012. 	Santiago.	se agreg? un campo para ver la justificaci?n por que el medicamento no fue aplicado a la ronda correspondiente
 **********************************************************************************************************************************************************/
 
//Contiene la informaci?n b?sica de una habitaci?n
class habitacion{

	var $codigo;
	var $cco;
	var $historia;
	var $ingreso;
	var $nombre;
	var $horas;		//horas de aplicaci?n,
	var $ccoNombre;
	var $enProcesoTraslado;
	var $altaEnProceso;
	var $fechaHoraAltaEnProceso;	//Fecha y hora de alta en proceso, solo la ronda, en formato Unix(segundos desde 1970-01-01 a las 00:00:00
	
	var $esTrasladoUrgencia;		//Indica si fue trasladado
	var $fechaHoraTraslado;			//Indica tiene y fecha de traslado
	
	var $kardexConfirmado;			//Array con indice de fecha. Indica si el kardex para el d?a esta confirmado
	var $tieneKardex;
	
	var $artSinConfirmar = "";
	
	var $totalArticulos;
	var $articulosNecesidadSinRonda;	//cuenta el total de articulos a necesidad sin ronda
	
	var $consultoArticulosSinConfirmar;
	
	var $documento;
	var $tid;

	//constructor de clase
	function habitacion( $cco, $ccoNombre, $hab, $historia, $ingreso, $nombre, $enTraslado, $altaProceso, $fechaAltaProceso, $horaAltaProceso, $pacced, $pactid ){
	
		global $wbasedato;
		global $conex;
		
		global $hora;
		global $tiempoAMostrar;
		global $fechaFinal;
		
		$fechaFinal++;
	
		$this->codigo = $hab;
		$this->cco = $cco;
		$this->ccoNombre = $ccoNombre;
		$this->historia = $historia;
		$this->ingreso = $ingreso;
		$this->nombre = $nombre;
		$this->documento = $pacced;
		$this->tid = $pactid;
		
		$this->enProcesoTraslado = ( $enTraslado == 'on' )? true: false;
		$this->altaEnProceso  = ( $altaProceso == 'on' )? true: false;
		
		$this->consultoArticulosSinConfirmar = false;
		
		if( $this->altaEnProceso ){
			//ronda en que se hizo el proceso de alta definitiva
			$this->fechaHoraAltaEnProceso = strtotime( $fechaAltaProceso." ".$horaAltaProceso )-strtotime( "1970-01-01 00:00:00" );
			$this->fechaHoraAltaEnProceso = intval( $this->fechaHoraAltaEnProceso/(2*3600) )*2*3600+strtotime( "1970-01-01 00:00:00" );
		}
		else{
			$this->fechaHoraAltaEnProceso = 0;
		}
		
		//creo el array con objeto vacio hora
		for( $i = date( "G", $fechaFinal-$tiempoAMostrar*3600 ); $i <= date( "G", $fechaFinal-$tiempoAMostrar*3600 )+$tiempoAMostrar; $i += 2 ){

			$this->horas[ gmdate( "G", $i*3600 ) ] = new hora();
		}
		
		$fechaFinal--;
	}
	
	/********************************************************************************
	 * Consulta Justificaci?n Medicamento 
	 ********************************************************************************/
	function consultarJustificacion( $ronda, $codigoArticulo, $idOriginal ){
	
	    global $wbasedato;
	    global $fecha;
        global $conex;	
			
	    $sql = "SELECT 	SUBSTRING(Jusjus,INSTR(Jusjus, ' - ') + 2) as Jusjus  "
              ."  From ".$wbasedato."_000113 "
			  ." where Jushis = '".$this->historia ."' and "
              ."  Jusing = '".$this->ingreso ."' and ";	
              
			  // if($ronda >= 12){
	              // $sql .= "  Jusron ='".gmdate("g",$ronda*60*60).""; 		  
			      // $sql .= ":00 - PM' and ";
				// } 
			  // else{
				     // $sql .="  Jusron = '".gmdate("h",$ronda*60*60)."";
				  	 // $sql .=":00 - AM' and ";
				// } 
				
		$sql .= "  Jusron ='".gmdate("H:00 - A",$ronda*3600)."' and ";
				
        $sql .="  Jusart = '".$codigoArticulo."' and ";
		$sql .="  Jusfec	 = '".$fecha."' and ";
        $sql .="  Jusido	 = '".$idOriginal."'";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	   	  
    	$row = mysql_fetch_array($res);
		$Jusjus = $row['Jusjus'];	
		return $Jusjus;
		
	}
	/********************************************************************************
	 * Agrega un medicamento a una hora
	 *
	 * $ronda						Ronda, de 0 - 22, solo pares
	 * $codigoArticulo				Codigo del articulo
	 * $nombre						Nombre del medicamento
	 * $cantidadCargada				Cantidad a aplicar al paciente
	 * $unidad						Unidad de fraccion a aplicar al paciente
	 * $suspendido					Indica si esta suspendido o no
	 * $aNecesidad					Condicion de aplicacion
	 * $sinConfirmarPreparacion		Indica si el articulo es sin confirmar preparaciones, tipo bool
	 * $fechaInicio					Fecha y hora de inicio en formato unix (segundos)
	 * $frecuencia					Frecuencia en horas
	 * $fechaBase					Fecha en que se esta basando el kardex
	 * $dosisMaximas				Total de dosis maximas
	 * $diasTrtatamiento			Total de dias de tratamiento
	 ********************************************************************************/	 
	function procesarMedicamento( $ronda, $codigoArticulo, $nombre, $cantidadCargada, $unidad, $suspendido, $aNecesidad, $sinConfirmarPreparacion, $fechaInicio, $frecuencia, $fechaBase, $dosisMaximas, $diasTratatamiento, $filaArticulos ){
		
		global $conex;
		global $wbasedato;
		global $aplicaciones;			//Array con todas la aplicaciones realizadas
		global $fecha;					//Fecha de la ronda
		global $informacionArticulos;	//Array con la informaci?n basica de los articulos
		
		$idOriginal = $filaArticulos[ 'Kadido' ];
		
		$this->esTrasladoUrgencia = consultarUltimoTraslado( $conex, $wbasedato, $this->historia, $this->ingreso, $this->fechaHoraTraslado );
		
		if( $this->esTrasladoUrgencia ){
			if( $this->fechaHoraTraslado >= strtotime( $fecha." ".$ronda.":00:00" ) ){
				return;
			}
		}
		
		$info = Array();
		$info[ 'codigo' ] = $codigoArticulo;
		$info[ 'nombre' ] = $nombre;
		$info[ 'cantidadAAplicar' ] = $cantidadCargada;
		$info[ 'unidad' ] = $unidad;
		$info[ 'cantidadAplicada' ] = @$aplicaciones[ $this->historia."-".$this->ingreso ][ strtoupper( $codigoArticulo ) ][ $idOriginal ][$ronda];
		$info[ 'suspendido' ] = ( $suspendido == 'on' )? "S&iacute;" : "No";
		$info[ 'fechaHoraIncio' ] = $fechaInicio;
		$info[ 'frecuencia' ] = $frecuencia*3600;
		$info[ 'cantDosisAnterior' ] = $filaArticulos[ 'Kaddan' ];
		$info[ 'fechaUltimaModificacion' ] = $filaArticulos[ 'Kadfum' ];
		$info[ 'horaUltimaModificacion' ] = $filaArticulos[ 'Kadhum' ];
		
		//Busco si este articulo reemplazo otro
		$fechaHoraReemplazo = fechaHoraReemplazoArticuloNuevo( $this->historia, $this->ingreso, $fechaBase, $codigoArticulo, &$artAnterior );
		
		//Si el articulo reemplazo a otro antes de la ronda, no se muestra este articulo
		//																	 	   *       Este calculo da la ronda en que fue reemplazado       *
		if( !empty( $fechaHoraReemplazo ) && strtotime( "$fecha $ronda:00:00" ) <= ceil( strtotime( $fechaHoraReemplazo )/(2*3600) )*2*3600-2*3600 ){
		
			// if( !empty( $aplicaciones[ $this->historia."-".$this->ingreso ][ $artAnterior ][ $idOriginal ][ $ronda ] ) ){
			if( existeAplicacionInc( $aplicaciones, $this->historia, $this->ingreso, $artAnterior, $ronda ) ){
				return;
			}
		}
		
		if( $info[ 'suspendido' ] != "No" ){
		
			//Busco si el articulo fue reemplazado desde el perfil
			$reemplazado = fueReemplazado( $this->historia, $this->ingreso, $fechaBase, $codigoArticulo );
		
			if( !$reemplazado ){	//Si no fue reemplazado
		
				$estaSuspendido = buscarSiEstaSuspendidoInc( $this->historia, $this->ingreso, $codigoArticulo, $ronda, $fecha, $idOriginal );
				
				if( $estaSuspendido != 'on' ){
					$info[ 'suspendido' ] = "No";
				}
				else{
					return;
				}
			}
			else{	//Si fue reemplazado
				return;
			}
		}
		
		//verifico si tiene dosis m?xima
		//Si tienes dosis m?xima verifico que pertenezca a la ronda
		$dosisMaximas = trim( $dosisMaximas );
		if( !empty( $dosisMaximas ) ){
			if( strtotime( "$fecha $ronda:00:00" ) > $info[ 'fechaHoraIncio' ]+( $dosisMaximas-1 )*$info[ 'frecuencia' ] ){
				return;
			}
		}
		
		//Si tiene dias de tratamiento
		//Si tiene dias de trtamiento, miro que la ronda no supere los dias de tratamiento
		$diasTratatamiento = trim( $diasTratatamiento );
		if( !empty( $diasTratatamiento ) ){
			if( strtotime( "$fecha 23:59:59" ) > strtotime( date( "Y-m-d 23:59:59", $info[ 'fechaHoraIncio' ]+( $diasTratatamiento-1 )*24*3600 ) ) ){
				return;
			}
		}
		
		//Verifico si la cantidad de dosis cambio
		if( !empty( $info[ 'cantDosisAnterior' ] ) ){
			
			if( $info[ 'cantDosisAnterior' ] <= $info[ 'cantidadAAplicar' ] ){
				
				if( !empty( $info[ 'cantidadAplicada' ] ) && $info[ 'cantidadAplicada' ] >= $info[ 'cantDosisAnterior' ] ){
				
					//Calculo la ronda de modificaci?n en formato unix
					$rondaModificacionUnix = intval( ( strtotime( $info[ 'fechaUltimaModificacion' ]." ".$info[ 'horaUltimaModificacion' ] ) - strtotime( "1970-01-01 00:00:00" ) )/(2*3600) )*2*3600 + strtotime( "1970-01-01 00:00:00" );
					
					//Calculo el tiempo de hora de confirmacion del kardex
					$fcConfirmacionKardex = strtotime( $fechaBase." ".$filaArticulos['Karhco'] );
					
					//Si la fehca de modificaciones es m
					if( $rondaModificacionUnix >= strtotime( "$fecha $ronda:00:00" ) || $fcConfirmacionKardex >= strtotime( "$fecha $ronda:00:00" ) ){
						$info[ 'cantidadAAplicar' ] = $info[ 'cantDosisAnterior' ];
					}
				}
			}
		}
		
		//Averiguo si el articulo pertenece a la ronda, esto para saber si es a necesidad mostrarlo
		if( ( strtotime( "$fecha $ronda:00:00" ) - $fechaInicio )%$info[ 'frecuencia' ] == 0 ){
			$info[ 'perteneceRonda' ] = true;
		}
		else{
			$info[ 'perteneceRonda' ] = false;
		}
		
		$expCondicion = explode( " - ", estaANecesidad( $aNecesidad ) );
	
		@$info[ 'aNecesidad' ] = ( $expCondicion[1] == "AN" )? "S&iacute;" : "No"; //( esANecesidad( $aNecesidad ) )? "S&iacute;" : "No";
		$info[ 'condicion' ] = $expCondicion[0];
		
		//Si es de traslado o tiene alta se considera con justificacion
		//esto para que salga con x amarilla
		//																								*                hora par anterior                    *
		if( $this->enProcesoTraslado) { //|| ( $this->altaEnProceso && strtotime( "$fecha $ronda:00:00" ) >= intval( $this->fechaHoraAltaEnProceso/(2*3600) )*2*3600 ) ){
			//if( $this->enProcesoTraslado ){
				$Jusjus = "En proceso de traslado";
			//}
			//else{
			//	$Jusjus = "Alta en proceso";
			//}
		}
		else{
			$Jusjus = $this->consultarJustificacion( $ronda, $codigoArticulo, $idOriginal );
		}
		
		
		$info['Jusjus'] = $Jusjus;
		
		$this->horas[ $ronda ]->agregarMedicamento( $codigoArticulo."-".$idOriginal, $info, $sinConfirmarPreparacion );
		@$this->totalArticulos[ $fecha ]++;	//cuento el total de articulos que hay para el paciente
		@$this->totalArticulos[ 'total' ]++;
		
		if( !$info[ 'perteneceRonda' ] ){
			@$this->articulosNecesidadSinRonda[ $fecha ]++;
			@$this->articulosNecesidadSinRonda[ 'total' ]++;
		}
	}
};

class hora{

	var $medicamentos = Array();	//lista de medicamentos, solo el codigo
	var $color = 0;			//Solo el numero, ya hay array global que indica el color
	var $descripcion = '';	//Si es total, parcial o no aplicada
	
	var $totalAplicados = 0;
	var $totalMedicamentos = 0;
	var $totalMedicamentosNecesidad = 0;	//Indica cuantos medicamentos a Necesidad hay
	var $totalMedicamentosJustificados = 0;	//Indica cuantos medicamentos a Necesidad hay
	var $totalMedicamentosObligatorios = 0;
	var $totalMedicamentosObligatoriosAplicados = 0;
	
	var $esKardexConfirmado;
	var $tieneKardex;
	
	var $tieneArticulosSinConfirmar = false;
	
	var $agregadoMedSinConfirmar = false;
	
	var $totalMedicamentosNecesidadPertenecientesRonda = 0;
	
	/********************************************************************************
	 * Agrega la informaci?n pertinente para un articulo
	 ********************************************************************************/
	function agregarMedicamento( $codigo, $infoArticulo, $sinConfirmarPreparacion ){
	
		@$infoArticulo[ 'confirmado' ] = $sinConfirmarPreparacion;
		
		//Si ya existe, sumo la cantidad a aplicar nuevamente
		//Y dejo que todo continue normal
		if( isset( $this->medicamentos[ $codigo ][ 'codigo' ] ) ){
			$infoArticulo[ 'cantidadAAplicar' ] += $this->medicamentos[ $codigo ][ 'cantidadAAplicar' ];
		}
	
		if( true || !isset( $this->medicamentos[ $codigo ][ 'codigo' ] ) ){
		
			if( !$sinConfirmarPreparacion ){
				$this->tieneArticulosSinConfirmar = !$infoArticulo[ 'confirmado' ];
			}
			
			$this->medicamentos[ $codigo ] = $infoArticulo;
			
			$this->totalMedicamentos++;
			
			if( $this->medicamentos[ $codigo ][ 'cantidadAAplicar' ] == $this->medicamentos[ $codigo ][ 'cantidadAplicada' ] ){
				$this->totalAplicados++;
			}
			
			if( @$infoArticulo[ 'aNecesidad' ] != "No" ){
				$this->totalMedicamentosNecesidad++;
				
				if( @$infoArticulo[ 'perteneceRonda' ] ){
					$this->totalMedicamentosNecesidadPertenecientesRonda++;
				}
			}

			
			if( !empty( $infoArticulo[ 'Jusjus' ] ) ){
				$this->totalMedicamentosJustificados++;
			}
			
			//Son medicamentos Obligatorios aquellos que no tienen justificacion, no son a necesidad y no estan suspendidos
			if( $infoArticulo[ 'aNecesidad' ] == "No" && empty( $infoArticulo[ 'Jusjus' ] ) && $infoArticulo[ 'suspendido' ] == "No" ){

				$this->totalMedicamentosObligatorios++;
				
				if( $this->medicamentos[ $codigo ][ 'cantidadAAplicar' ] <= $this->medicamentos[ $codigo ][ 'cantidadAplicada' ] ){
					$this->totalMedicamentosObligatoriosAplicados++;
				}
			}
			
		}
	}
	
	/**********************************************************************
	 * Define el color a mostrar para la celda
	 **********************************************************************/
	function estado(){
	
		$val = '';
		
		if( true || $this->tieneKardex == 'on' ){
			if( true || $this->esKardexConfirmado == 'on' ){
			
				//Si el total de medicamentos para una ronda es igual a la cantidad de medicamentos a necesidad entonces no deben salir nada
				if( $this->totalMedicamentos > 0 /*&& $this->totalMedicamentos != $this->totalMedicamentosNecesidad*/ ){
				
					//Si no hay medicamentos aplicados o los medicamentos obligatorios no fueron todos aplicados
					if( $this->totalAplicados == 0 || $this->totalMedicamentosObligatorios > $this->totalMedicamentosObligatoriosAplicados ){
						
						if( $this->totalMedicamentosObligatorios == 0 && $this->totalMedicamentos <= $this->totalMedicamentosNecesidad + $this->totalMedicamentosJustificados ){
						
							if( $this->totalMedicamentos == $this->totalMedicamentosNecesidad && $this->totalAplicados > 0 ){
								
								$this->color = CLR_VERDE;
							}
							elseif( $this->totalMedicamentos != $this->totalMedicamentosNecesidad || $this->totalMedicamentos == $this->totalMedicamentosNecesidadPertenecientesRonda ){
								//Si los medicamentos no fueron aplicados pero todos son a necesidad o tiene justificacion
								$this->color = CLR_AMARILLO;
							}
						}
						//Si por lo menos un medicamento obligatorio no fue aplicado
						elseif( $this->totalMedicamentosObligatorios > $this->totalMedicamentosObligatoriosAplicados ){

							$this->color = CLR_ROJO;
						}
					}
					elseif( $this->totalAplicados > 0 ){
						//Si todo los medicamentos obligatorios fueron aplicados
						$this->color = CLR_VERDE;
					}			
				}
			}
			else{
				$this->color = CLR_KSC;
			}
		}
		else{
			$this->color = CLR_SK;
		}
		
		switch( $this->color ){
		
			case CLR_VERDE:
				$val = "<img  src='/matrix/images/medical/movhos/checkmrk.ico'>";
				break;
			
			case CLR_AMARILLO:
				$val = "<img  src='/matrix/images/medical/root/borrarAmarillo.png'>";
				break;
			
			case CLR_ROJO:
				$val = "<blink><img  src='/matrix/images/medical/root/borrar.png'></blink>";
				break;
			
			default: $val = '';
				break;
		}
		
		$this->descripcion = $val;
	}
};



/******************************************************************************************************************
 * Consulta la frecuencia de acuerdo al codigo
 ******************************************************************************************************************/
function consultarFrecuencia( $codigoFrecuencia ){

	global $conex;
	global $wbasedato;

	$val = false;
	
	$sql = "SELECT
				Perequ
			FROM
				{$wbasedato}_000043 b
			WHERE
				percod = '$codigoFrecuencia'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$val = $rows['Perequ'];
	}
	
	return $val;
}

/******************************************************************************************
 * Indica si un paciente tiene articulos en el kardex para una fecha
 ******************************************************************************************/
function tieneArticulosPorFecha( $historia, $ingreso, $fecha ){

	global $conex;
	global $wbasedato;

	$val = false;
	
	$sql = "SELECT
				Kadart, Kadfin, Kadhin, Perequ, Kadcfr, Kadufr, Kadsus, Kadcnd
			FROM
				{$wbasedato}_000054 a, {$wbasedato}_000043 b
			WHERE
				Kadhis = '$historia'
				AND kading = '$ingreso'
				AND kadfin = '$fecha'
				AND percod = kadper
				AND kadori = 'CM'
				AND kadcon = 'off'
				AND kadsus != 'on'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0){
		$val = true;
	}
	
	return $val;
}

/**************************************************************************************************
 * Devuelve un arreglo con los articulos sin confirmar para un paciente y fecha dados
 *
 * El array devuelto esta compuesto as?
 *
 * La primera posicion es asocitativo con indice igual al codigo del articulo
 * el valor igual al tiempo unix
 * [ art ] = tiempo unix
 **************************************************************************************************/
function articulosSinConfirmar( $historia, $ingreso, $fecha ){

	global $conex;
	global $wbasedato;
	global $informacionArticulos;
	
	$val = array();
	
	$sql = "SELECT
				Kadart, Kadfin, Kadhin, Perequ, Kadcfr, Kadufr, Kadsus, Kadcnd
			FROM
				{$wbasedato}_000054 a, {$wbasedato}_000043 b
			WHERE
				Kadhis = '$historia'
				AND kading = '$ingreso'
				AND kadfec = '$fecha'
				AND percod = kadper
				AND kadori = 'CM'
				AND kadcon = 'off'
				AND kadsus != 'on'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
	
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
			$val[ $rows['Kadart'] ][ 'inicio' ] = strtotime( $rows['Kadfin']." ".$rows['Kadhin'] );
			$val[ $rows['Kadart'] ][ 'frecuencia' ] = $rows['Perequ'];
			$val[ $rows['Kadart'] ][ 'unidadDeFraccion' ] = $rows['Kadufr'];
			$val[ $rows['Kadart'] ][ 'cantidadDeFraccion' ] = $rows['Kadcfr'];
			$val[ $rows['Kadart'] ][ 'suspendido' ] = $rows['Kadsus'];
			$val[ $rows['Kadart'] ][ 'condicion' ] = $rows['Kadcnd'];
			
			consultarInformacionProducto( $rows['Kadart'], &$informacionArticulos );
		}
	}
	
	return $val;
}

/****************************************************************************************************************
 * Valida si para una ronda hay un articulo sin confirmar
 *
 * $ronda		Numero entero de 0-23
 * $articulo	array de articulos
 * $art			medicamentos sin confirmar
 ****************************************************************************************************************/
function rondaConArticuloSinConfirmar( $articulos, $ronda, &$art, &$arts2 ){

	$val = false;
	
	if( !empty($articulos) && count($articulos) > 0 ){
	
		foreach( $articulos as $keyArt => $valArt ){
		
			if( strtotime( date( "Y-m-d" )." $ronda:00:00" ) >= $valArt[ 'inicio' ] ){

				if( ( strtotime( date( "Y-m-d" )." $ronda:00:00" ) - $valArt[ 'inicio' ] )%( $valArt[ 'frecuencia' ]*3600 ) == 0 ){
					$art[] = $keyArt;
					$arts2[ $keyArt ] = 1;
					$val = true;
				}
			}
		}
	}
	
	return $val;
}


/************************************************************************************
 * Segun la condicion de suministro del articulo, dice si dicha condici?n 
 * es considerada a necesidad o no
 *
 * Septiembre 2011-09-11
 ************************************************************************************/
function estaANecesidad( $condicion ){

	global $wbasedato;
	global $conex;

	$val = false;

	if( !empty( $condicion ) ){

		$sql = "SELECT 
					Contip, Condes
				FROM 
					{$wbasedato}_000042
				WHERE 
					concod = '$condicion'
				";
					
		$resAN = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$numrowsAN = mysql_num_rows( $resAN );
				
		if( $numrowsAN > 0 ){
		
			$rowsAN = mysql_fetch_array( $resAN );
			
			$val = $rowsAN[ 'Condes' ];
		
			if( $rowsAN[ 'Contip' ] == 'AN' ){
				$val .= " - AN";				
			}
		}
	}
	
	return $val;
}


/********************************************************************************************************************************************
 * Consulto la informaci?n b?sica del producto (nombre comercial y generico, unidad de medida y codigo
 * 
 * Guardo la informaci?n en una variable global, esto para no consultar la informaci?n de un articulo repetitivamente
 ********************************************************************************************************************************************/
function consultarInformacionProducto( $producto, &$articulos ){

	global $conex; 
	global $wcenmez;
	global $wbasedato;
	
	if( isset( $articulos[$producto] ) ){
		return;
	}
	
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000026
			WHERE
				artcod = '$producto'
			";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
	
		$rows = mysql_fetch_array( $res );
		
		$articulos[ $rows['Artcod'] ][ 'codigo' ] = $rows['Artcod'];
		$articulos[ $rows['Artcod'] ][ 'nombreComercial' ] = $rows['Artcom'];
		$articulos[ $rows['Artcod'] ][ 'nombreGenerico' ] = $rows['Artgen'];
		$articulos[ $rows['Artcod'] ][ 'unidadMinima' ] = $rows['Artuni'];
	}
	else{

		$sql = "SELECT
					*
				FROM
					{$wcenmez}_000002
				WHERE
					artcod = '$producto'
				";
				
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );
		
		if( $num > 0 ){
		
			$rows = mysql_fetch_array( $res );
			
			$articulos[ $rows['Artcod'] ][ 'codigo' ] = $rows['Artcod'];
			$articulos[ $rows['Artcod'] ][ 'nombreComercial' ] = $rows['Artcom'];
			$articulos[ $rows['Artcod'] ][ 'nombreGenerico' ] = $rows['Artgen'];
			$articulos[ $rows['Artcod'] ][ 'unidadMinima' ] = $rows['Artuni'];
		}
	}
}


function consultarMensajesSinLeerCPA( $conex, $wbasedato, $programa, $wcco, $wser, $wfecha ){

	$val = false;

	$sql = "SELECT count(*)
			  FROM {$wbasedato}_000127
			 WHERE Mencco  = '$wcco'
			   AND Menprg != '$programa'
			   AND Menlei != 'on'
			   AND Menser  = '$wser'
			   AND Menfec  = '$wfecha'
			";
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array($res) ){
		$val = $rows[ 0 ];
	}
	
	return $val;
}

function consultarProcedimientosSinLeer( $conex, $wbasedato, $historia, $ingreso ){

	$val = false;

	$sql = "SELECT 
				count(*)
			FROM 
				{$wbasedato}_000121
			WHERE 
				Dmohis = '$historia'
				AND Dmoing = '$ingreso'
				AND Dmolei != 'on'
			";
				 
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array($res) ){
		$val = $rows[ 0 ];
	}
	
	return $val;
}

function Consultar_servicioActual()
 {
  global $conex; 
  global $wbasedato;
  
  $whora  = (string)date("H:i:s");
  
  $q = " SELECT sercod, sernom "
      ."   FROM ".$wbasedato."_000076 "
	  ."  WHERE serhin <= '".$whora."'"
	  ."    AND serhad >= '".$whora."'"
	  ."    AND serest  = 'on' ";
  $res = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );
  $num = mysql_num_rows( $res );
  
  if ($num > 0)
     {
	  $row = mysql_fetch_array( $res );
	  $wcodser   = $row[0];
	  $wservicio = $row[1];
	 }
    else
       $wservicio="";
  
  return $wcodser."-".$wservicio;  
  
 }

function Consultar_dietas($whis,$wing)
 {
  global $conex; 
  global $wbasedato;
 
  $wfecha = date( "Y-m-d" );
    
  $whaypedido=0;
  
  $wser = Consultar_servicioActual();
  $wserv=explode("-",$wser);
  
  $wcodser=$wserv[0];
  $wservicio=$wserv[1];
  
  if (isset($wservicio) and trim($wservicio) != "")
    {
	 $q = " SELECT COUNT(*) "
	     ."   FROM ".$wbasedato."_000077 "
		 ."  WHERE movfec = '".$wfecha."'"
		 ."    AND movhis = '".$whis."'"
		 ."    AND moving = '".$wing."'"
		 ."    AND movest = 'on' "
		 ."    AND movser = '".$wcodser."'";
	 $res = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );
     $num = mysql_num_rows( $res );

	 $row = mysql_fetch_array( $res );
	 $whaypedido=$row[0];
    }	

   if ($whaypedido == 0)	
      return false;
	 else
	   return true;
 }


/********************************************************************************
 * Pinta los datos necesarios en pantalla
 ********************************************************************************/
function pintarDatosFila( $datos ){

	global $colores;
	global $fecha;
	global $hora;
	global $fechaFinal;
	global $wemp_pmla;
	global $conex;
	global $wbasedato;
	global $ccoCodigo;
	global $whce;
	global $wsp;       //Indica que no se debe mostrar el paciente (WSP)==> Sin Paciente
	
	
	$fechaFinal++;
	
	global $tiempoAMostrar;
	
	if( count($datos) > 0 ){
		
		$i = 0;
		foreach( $datos as $keyDatos => $valueDatos ){
		
			$wtraslado      = $valueDatos->enProcesoTraslado;
			$waltaEnProceso = $valueDatos->altaEnProceso;
			$whab           = $valueDatos->codigo;
		
			if( $i == 0 ){
				echo "<table align='center'>";
				
				echo "<tr style='opacity:90'>";
		
				if( $tiempoAMostrar == 24 ){
					echo "<td colspan='8' style='font-size:10pt'>";
				}
				else{
					echo "<td colspan='2' style='font-size:10pt'>";
				}
				
				echo "<td style='visibility:hidden'><img src='/matrix/images/medical/movhos/checkmrk.ico'></td>";
				
				// echo "<b>Nota:</b> No se muestran los medicamentos suspendidos.";
				echo "</td>";
				
				echo "<td class='fondoAlertaConfirmar' align='center' colspan='1' onMouseover='mostrarTooltip( this );'><a title='Mezclas sin confirmar'>Mezclas sin<br>confirmar</a></td>";
				
				echo "<td class='fila1' align='center' colspan='1' onMouseover='mostrarTooltip( this );'><a title='Sin Generar Kardex Hoy'><img src='/matrix/images/medical/movhos/NOTE16.ico'></a></td>";
				
				echo "<td class='fila1' align='center' colspan='1' onMouseover='mostrarTooltip( this );'><a title='Kardex Sin Confirmar Hoy'><img src='/matrix/images/medical/movhos/Key04.ico'></a></td>";
				
				echo "<td class='fila1' align='center' colspan='2' onMouseover='mostrarTooltip( this );'><a title='Todos los medicamentos obligatorios han sido aplicados'><img src='/matrix/images/medical/movhos/checkmrk.ico'> Aplicado</a></td>";
				
				echo "<td class='fondorojo' align='center' colspan='2' onMouseover='mostrarTooltip( this );'><a title='Al menos un medicamento obligatorio no ha sido aplicado'><img src='/matrix/images/medical/root/borrar.png'> Sin Aplicar o Sin Dieta</a></td>";
				
				echo "<td class='fondoamarillo' align='center' colspan='2' onMouseover='mostrarTooltip( this );'><a title='Los medicamentos no han sido aplicados pero tienen justificaci&oacute;n o son a necesidad'><img src='/matrix/images/medical/root/borrarAmarillo.png'> Sin Aplicar</a></td>";
				
				echo "</tr>";
		
		        //Consulto el servicio de alimentacion correspondiente a la hora en que se ejecuta este script
		        $wser      = Consultar_ServicioActual();
				$wserv     = explode("-",$wser);
				$wcodser   = $wserv[0];
				$wservicio = $wserv[1];
								
								
				echo "<tr class='fila2'>";
				
				$wc=1;
				if (!isset($wsp) or $wsp!="on")
				   $wc=0;
				
				//*** Link a la Entrega de turno
				$path = "Contingencia_Kardex_de_Enfermeria.php?wemp_pmla=".$wemp_pmla."&wcco=".$valueDatos->cco."&wfec=".$fecha;
				echo "<td colspan=2 style='cursor: pointer' onclick='ejecutar(".chr(34).$path.chr(34).")'><font size=4>Entrega de Turno";
				if( $tiempoAMostrar == 24 )
				  {
				    $wcol=16-$wc;
				    //Este path es para poder accesar a la lista de dietas desde cualquier punto del TR
		            echo "<td colspan=".$wcol."><font size=4>";
				  }
				 else
				   {
				    $wcol=10-$wc;
				    //Este path es para poder accesar a la lista de dietas desde cualquier punto del TR
		            echo "<td colspan=".$wcol."><font size=4>";
				   }
				$wmsjCPA    = consultarMensajesSinLeerCPA( $conex, $wbasedato, "dietas", $valueDatos->cco, $wcodser, $fecha );
				
				if ($wmsjCPA > 0)
				   {
				    $path = "Dietas.php?wemp_pmla=".$wemp_pmla."&wser=".$wcodser."&wcco=".$valueDatos->cco."&wfec=".$fecha;
				    echo "<b style='cursor: pointer' onclick='ejecutar(".chr(34).$path.chr(34).")'>".$valueDatos->cco." - ".$valueDatos->ccoNombre."  ====> <blink> Mensaje(s) sin leer de la CPA : ".$wmsjCPA."</blink></b>";
				   }
				   else
				      echo "<b>".$valueDatos->cco." - ".$valueDatos->ccoNombre."</b>";
				
				echo "</font></td>";
				echo "</tr>";
				
				echo "<tr class='encabezadotabla' align='center'>";
				echo "<td rowspan=2>Dieta</td>";
				echo "<td rowspan=2>Msj Kardex</td>";
				echo "<td rowspan=2>Msj Secre.</td>";
				echo "<td rowspan=2>Habitaci&oacute;n</td>";
				echo "<td rowspan=2>Historia</td>";
				if (!isset($wsp) or $wsp!="on")
				   {
					echo "<td rowspan=2 colspan=4>Nombre</td>";
				   }	
				
				if( true || date( "Y-m-d", $fechaFinal ) == date( "Y-m-d" ) ){
				
					if( date( "Y-m-d", $fechaFinal - $tiempoAMostrar*3600 ) == date( "Y-m-d", time() - 24*3600 ) ){	//Si al restar 12 horas es el dia anterior
						//Hago el rowspan correspondiente para el dia anterior
						$rowspanDiaAnterior = ( 24 - date( "G", $fechaFinal-$tiempoAMostrar*3600 ) )/2; 
						
						echo "<td colspan='".$rowspanDiaAnterior."' class='fila1'>".date( "Y-m-d", time()-$tiempoAMostrar*3600 )."</td>";
					}
					else{
						$rowspanDiaAnterior = 0;
					}
					
					if( $rowspanDiaAnterior*2 != $tiempoAMostrar ){
					
						if( $rowspanDiaAnterior == 0 ){
							echo "<td colspan='".( ( $tiempoAMostrar - $rowspanDiaAnterior*2 )/2 )."' class='fila1'>".date( "Y-m-d", $fechaFinal-1 )."</td>";
						}
						else{
							echo "<td colspan='".( ( $tiempoAMostrar - $rowspanDiaAnterior*2 )/2 )."'>".date( "Y-m-d", $fechaFinal-1 )."</td>";
						}
					}
					
					echo "<tr class='encabezadotabla'>";
					
					//Pinto las horas correspondientes
					for( $j = date( "G", $fechaFinal - $tiempoAMostrar*3600 ); $j < date( "G", $fechaFinal - $tiempoAMostrar*3600 )+$tiempoAMostrar; $j += 2 ){
						if( $j < 24){
							echo "<td align='center' style='width:50' style='border:10' class='fila1'>".gmdate( "G", $j*3600 )."</td>";
						}
						else{
							echo "<td align='center' style='width:50' style='border:10'>".gmdate( "G", $j*3600 )."</td>";
						}
					}
					echo "</tr>";
				}
				echo "</tr>";
			}
		
			$class = "class='fila".(($i%2)+1)."'";
			$class3 = "class='fila".((($i+1)%2)+1)."'";
			$i++;
			
			//Consulto si la historia esta en proceso de traslado
		    if ($wtraslado)
			   {
                echo "<tr class='colorAzul4'>";
				
				if ($waltaEnProceso)
				   {
					echo "<tr class='fondoAmarillo'>";
				   }	
			   }	
			  else 
			    if ($waltaEnProceso)
				   {
					echo "<tr class='fondoAmarillo'>";
				   }
                  else				   
			        echo "<tr $class>";
				 
			
			//Consulto si hay dietas o mjs del perfil o secretaria sin leer
		    $wdietas    = Consultar_dietas($valueDatos->historia,$valueDatos->ingreso);
			$wmsjkardex = consultarMensajesSinLeer( $conex, $wbasedato, "kardex", $valueDatos->historia, $valueDatos->ingreso );
			$wmsjSecret = consultarProcedimientosSinLeer( $conex, $wbasedato, $valueDatos->historia, $valueDatos->ingreso );
			
			if ($wdietas)
			   {
			    echo "<td align='center'>";
			    echo "<img src='/matrix/images/medical/movhos/checkmrk.ico'>";
				echo "</td>";
			   }
              else
			     {
				  $path = "Dietas.php?wemp_pmla=".$wemp_pmla."&wser=".$wcodser."&wcco=".$valueDatos->cco."&wfec=".$fecha;
				  echo "<td align='center' style='cursor: pointer' onclick='ejecutar(".chr(34).$path.chr(34).")'>";
				  echo "<blink><img src='/matrix/images/medical/root/borrar.png'></blink>";
				  echo "<br><b>".$wservicio."</b>";
				  echo "</td>";
				 }
			
			//Mensajes generados desde el Perfil (Servicio Farmaceutico) Sin Leer
			$path = "/matrix/movhos/procesos/generarKardex.php?wemp_pmla=".$wemp_pmla."&waccion=b&whistoria=".$valueDatos->historia."&wingreso=".$valueDatos->ingreso."&wfecha=".$fecha."&editable=off&et=on";
			echo "<td align='center' style='cursor: pointer' onclick='ejecutar(".chr(34).$path.chr(34).")'>";
			if ($wmsjkardex > 0)
			   {
                echo "<b><blink><class=tipo3V>($wmsjkardex)</blink></b>";
			   }	
			   else	
			      {
                   echo " ";
				  }
			echo "</td>";

			
			//Procedimientos o gestiones de la secretaria a los procedimientos, sin leer
			//$path = "entrega_turnos_secretaria.php?wemp_pmla=".$wemp_pmla."&key=".$key."&whis=".$valueDatos->historia."&wing=".$valueDatos->ingreso."&wcco=".$valueDatos->cco."&wfec=".$fecha;
			$path = "/matrix/movhos/procesos/generarKardex.php?wemp_pmla=".$wemp_pmla."&waccion=b&whistoria=".$valueDatos->historia."&wingreso=".$valueDatos->ingreso."&wfecha=".$fecha."&editable=off&et=on";
			echo "<td align='center' style='cursor: pointer' onclick='ejecutar(".chr(34).$path.chr(34).")'>";
			if ($wmsjSecret > 0)
			   {
			    echo "<b><blink>".$wmsjSecret."</blink></b>";
			   }	
			   else	
			      {
                   echo " ";
				  }
			echo "</td>";
			
				
			echo "<td align=center style='height:40px'><b>";
			echo $valueDatos->codigo;
			echo "</b></td>";
			
			//Link a HCE
			$path="../../hce/procesos/HCE_iFrames.php?empresa=".$whce."&whis=".$valueDatos->historia."&wing=".$valueDatos->ingreso."&accion=F&ok=0&wcedula=".$valueDatos->documento."&wtipodoc=".$valueDatos->tid;
			echo "<td align=center style='cursor: pointer' onclick='ejecutar(".chr(34).$path.chr(34).")'>";
			echo $valueDatos->historia." - ".$valueDatos->ingreso;
			echo "</td>";
			
			
			if (!isset($wsp) or $wsp!="on")
			   {
				echo "<td colspan=4>";
				echo $valueDatos->nombre;
				echo "</td>";
			   }
			
			//imprimo lo que hay por horas
			
			for( $k = date( "G", $fechaFinal-$tiempoAMostrar*3600 ), $l = 0; $k < date( "G", $fechaFinal-$tiempoAMostrar*3600 )+$tiempoAMostrar; $k += 2, $l += 2 ){
			// foreach( $valueDatos->horas as $keyHoras => $valueHoras ){

				//Si el articulo no tiene kardex para el d?a actual y no tiene articulos a mostrar, se muestra toda la fila en colspan con la imagen correspondiente a Kardex Sin Generar
				if( date( "Y-m-d", $fechaFinal-$tiempoAMostrar*3600 + $l*3600 ) == date( "Y-m-d" ) && $valueDatos->tieneKardex[ date( "Y-m-d", $fechaFinal-$tiempoAMostrar*3600 + $l*3600 ) ] != 'on' && empty( $valueDatos->totalArticulos[ date( "Y-m-d", $fechaFinal-$tiempoAMostrar*3600 + $l*3600 ) ] ) ){
				
					echo "<td $class colspan='".( $tiempoAMostrar-$l/2 )."' align='center'>"; 
					echo "<a style='font-size:6pt;'><img src='/matrix/images/medical/movhos/NOTE16.ico'></a>";
					echo "</td>";
					
					$k = $k + ( $tiempoAMostrar - $l );
					$l = $tiempoAMostrar*2;
				}
				elseif( ( @$valueDatos->totalArticulos[ 'total' ] == 0 || @$valueDatos->totalArticulos[ 'total' ] == @$valueDatos->articulosNecesidadSinRonda['total'] ) ){
					echo "<td $class colspan='".( $tiempoAMostrar-$l/2 )."' align='center'>"; 
					echo "<a style='font-size:8pt;'>SIN MEDICACI&Oacute;N</a>";
					echo "</td>";
					
					$k = $k + ( $tiempoAMostrar - $l );
					$l = $tiempoAMostrar*2;
				}
				else{
					$keyHoras = gmdate( "G",$k*3600 )*1;
					$valueHoras = $valueDatos->horas[ $keyHoras ];
					
					@$valueHoras->estado();
					
					if( !$valueHoras->tieneArticulosSinConfirmar ){
						if( $colores[ $valueHoras->color ] != "NA" ){
							$classCelda = "class='fondo".$colores[ $valueHoras->color ]."'";
						}
						else{
							if( $k >= 24 ){
								// $classCelda = $class3;
								$classCelda = $class;
							}
							else{
								$classCelda = $class;
							}
						}
					}
					else{
						$classCelda = "class='fondoAlertaConfirmar'";
					}
					
					echo "<td $classCelda align='center' onClick='fnMostrar( this )'>";

					echo $valueHoras->descripcion;
					
					if( !empty($valueHoras->descripcion) ){
						if( date( "Y-m-d", $fechaFinal-$tiempoAMostrar*3600 + $l*3600 ) == date( "Y-m-d" ) ){
							if( $valueDatos->tieneKardex[ date( "Y-m-d", $fechaFinal-$tiempoAMostrar*3600 + $l*3600 ) ] != 'on' ){
								echo "<a style='font-size:6pt;'><img src='/matrix/images/medical/movhos/NOTE16.ico'></a>";
							}
							elseif( $valueDatos->kardexConfirmado[ date( "Y-m-d", $fechaFinal-$tiempoAMostrar*3600 + $l*3600 ) ] != 'on' ){
								echo "<a style='font-size:6pt;'><img src='/matrix/images/medical/movhos/Key04.ico'></a>";	//Sin confirmar
							}
						}
					}
					
					if( count($valueHoras->medicamentos) > 0 && !empty($valueHoras->color) ){
					
						//Aqui se pinta el minireporte para mostrar
						echo "<div style='display:none;width:100%' title='Informaci&oacute;n para las $keyHoras:00:00'>";
					
						echo "<table width=100%>";
						
						// echo "<tr class='fondo".$colores[ $valueHoras->color ]."' align='center'>";
						echo "<tr $class3 align='center'>";
						
						echo "<td style='font-size:14pt'><b>Informaci&oacute;n para las ".date( "H:00:00 \d\e\l Y-m-d", $fechaFinal-$tiempoAMostrar*3600 + $l*3600 +1 )."</b></td>";
						
						echo "</tr>";
						
						echo "<tr class='fila2'>";
						echo "<td>";
						echo "<b>".$valueDatos->historia." - ".$valueDatos->ingreso;
						echo "<br>".$valueDatos->nombre."(".$valueDatos->codigo.")";					
						echo "</td>";
						echo "<tr>";
						
						/************************************************************************************************************************
						 * Julio 24 de 2012
						 ************************************************************************************************************************/
						if( $valueDatos->altaEnProceso && $valueDatos->fechaHoraAltaEnProceso <= $fechaFinal-$tiempoAMostrar*3600 + $l*3600 +1 ){
							echo "<tr>";
							echo "<td class='fondoAmarillo' align='center'><blink><b>EN PROCESO DE ALTA</blink></b></td>";
							echo "</tr>";
						}
						/************************************************************************************************************************/
						
						
						echo "</table>";
						echo "<br><br>";
					
						echo "<table align='center'>";
						
						echo "<tr align='center' class='encabezadotabla'>";
						echo "<td>C&oacute;digo</td>";
						echo "<td>Nombre</td>";
						echo "<td>Cantidad a Aplicar</td>";
						echo "<td>Cantidad Aplicada</td>";
						echo "<td>Aplicaci&oacute;n</td>";
						echo "<td>Condici&oacute;n</td>";
						echo "<td>A necesidad</td>";
						echo "<td>Justificaci?n</td>";
						echo "</tr>";
						
						$j = 0;
						foreach( $valueHoras->medicamentos as $keyMedicamentos => $valueMedicamentos ){
							
							$class2 = "class='fila".(($j%2)+1)."'";
							$j++;
							
							echo "<tr $class2>";
							
							echo "<td>";
							echo $valueMedicamentos[ 'codigo' ];
							echo "</td>";
							
							echo "<td style='width:300px'>";
							echo $valueMedicamentos['nombre'];
							echo "</td>";
							
							echo "<td align='center'>";
							echo $valueMedicamentos['cantidadAAplicar']." ".$valueMedicamentos['unidad'];
							echo "</td>";
							
							//Muestro la cantidad aplicada
							echo "<td align='center'>";
							if( !empty( $valueMedicamentos['cantidadAplicada'] ) ){
							
								echo $valueMedicamentos['cantidadAplicada']." ".$valueMedicamentos[ 'unidad' ];
							}
							else{
								echo "0 ".$valueMedicamentos['unidad'];
							}
							echo "</td>";
							
							if( $valueMedicamentos['cantidadAplicada'] >= $valueMedicamentos['cantidadAAplicar'] ){
							
								if( $valueMedicamentos['confirmado'] === true ){
									echo "<td align='center'>";
								}
								else{
									echo "<td align='center' class='fondoAlertaConfirmar'>";
								}
								echo "<img src='/matrix/images/medical/movhos/checkmrk.ico'>";
								echo "<INPUT type='hidden' name='incanapl' value='".$valueMedicamentos['cantidadAplicada']."'>";
							}
							else{
								
								if( !empty( $valueMedicamentos['Jusjus'] ) || $valueMedicamentos['aNecesidad'] != "No" ){
									
									if( $valueMedicamentos['confirmado'] === true ){
										echo "<td align='center'>";
									}
									else{
										echo "<td align='center' class='fondoAlertaConfirmar'>";
									}
									echo "<img src='/matrix/images/medical/root/borrarAmarillo.png'>";
									echo "<INPUT type='hidden' name='incanapl' value='".$valueMedicamentos['cantidadAplicada']."'>";
								}
								else{
									if( $valueMedicamentos['confirmado'] === true ){
										echo "<td align='center' class='fondorojo'>"; 
									}
									else{
										echo "<td align='center' class='fondoAlertaConfirmar'>";
									}
									echo "<img src='/matrix/images/medical/root/borrar.png'>";
									echo "<INPUT type='hidden' name='incanapl' value='".$valueMedicamentos['cantidadAplicada']."'>";
								}
							}
							
							echo "</td>";
							
							echo "<td align='center'>";
							echo @$valueMedicamentos['condicion'];
							echo "</td>";
							
							echo "<td align='center'>";
							echo @$valueMedicamentos['aNecesidad'];
							echo "</td>";
							
							echo "<td align='center'>";
							echo $valueMedicamentos['Jusjus'];
							echo "</td>";
							
							echo "</tr>";
						}
						
						echo "</table>";
						
						echo "<br><INPUT TYPE='button' value='cerrar' onClick='$.unblockUI();' style='width:100'>";
						
						echo "</div>";
						//fin de pintar minireporte					
						
					}
					
					echo "</td>";
				}
			}
			
			echo "</tr>";
		}
		
		echo "</table>";
	}
	else{
		echo "<center><b>NO SE ENCONTRARON DATOS</b></center>";
	}
}

/************************************************************************************************************************************
 * 															FIN DE FUNCIONES
 ************************************************************************************************************************************/

include_once( "root/comun.php" );
include_once( "movhos/movhos.inc.php" );
   
if(!isset($_SESSION['user'])){
	exit("<b>Usuario no registrado</b>");
}
else{

	$conex = obtenerConexionBD("matrix");
	
	if(!isset($wemp_pmla)){
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
	}

	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$wcenmez = consultarAliasPorAplicacion($conex, $wemp_pmla, "cenmez");
	$whce = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");

	encabezado( "SISTEMA DE GESTION DE ENFERMERIA", "Julio 27 de 2012" ,"clinica" );
	
	echo "<form method=post>";

	if( empty($fecha) ){
		$fecha = date( "Y-m-d" );
	}
	
	if( empty($mostrar) ){
		$mostrar = "off";
	}
	
	echo "<INPUT type='hidden' name='wemp_pmla' value='$wemp_pmla'>";
	
	if( $mostrar == "off"  ){
	
		
		//**************** llamada a la funcion consultaCentrosCostos y dibujarSelect************
		$cco="Ccoipd";
		$sub="off";
		$tod="";
		$ipod="off";
		//$cco=" ";
		$centrosCostos = consultaCentrosCostos($cco);
					
		echo "<table align='center' border=0>";		
		$dib=dibujarSelect($centrosCostos, $sub, $tod, $ipod,"slCcoDestino");
					
		echo $dib;
		echo "</table>";
					
		echo "<table align='center' width=370>";
		//echo "</tr>";
		echo "<tr>";
		
		echo "<td class='fila1' align='center' width=73>Fecha</td>";
		
		echo "<td class='fila2'>";
		campoFechaDefecto( "fecha", $fecha );
		echo "</td>";
		echo "</tr>";

		
		//echo "</tr>";

		echo "</table>";
		
		echo "<br><table align='center'>";
		
		echo "<tr><td>";
		echo "<center><INPUT type='button' value='Aceptar' onclick='document.forms[0].submit();'></center>";
		echo "</td>";
		
		echo "<td>";
		echo "<center><INPUT type='button' value='Cerrar' onclick='cerrarVentana();'></center>";
		echo "</td></tr>";
		
		echo "</table>";
		
		echo "<INPUT type='hidden' name='mostrar' value='on'>";
	}
	else{
	
		echo "<INPUT TYPE='hidden' id='inRecargar' value='on'>";
		
		//Defino constantes para los colores y poder usarlos en el programa
		//Esto permite que lo pueda cambiar los colores sin problemas
		define("CLR_VERDE", 1 );
		define("CLR_AMARILLO", 2 );
		define("CLR_ROJO", 3 );
		define("CLR_KSC", 4 );
		define("CLR_SK", 5 );

		//Creo Array de colores, sirven para pintar las celdas de los distintos colores
		$colores = Array();
		$colores[ 0 ] = "NA";
		$colores[ CLR_VERDE ] = "NA";		
		$colores[ CLR_AMARILLO ] = "NA";
		$colores[ CLR_ROJO ] = "Rojo";
		$colores[ CLR_KSC ] = "NA";
		$colores[ CLR_SK ] = "NA";
		
		if( $fecha == date( "Y-m-d" ) ){
			//Si la fecha es la actual el tiempo es de 12 horas
			$tiempoAMostrar = 24;	//Indica cuanto es el tiempo a mostrar en horas en el reporte
		}
		else{
			//Si la fecha de consulta es diferente a la actual se debe mostrar las 24 horas
			$tiempoAMostrar = 24;	//Indica cuanto es el tiempo a mostrar en horas en el reporte
		}
		
		
		//Separo la informaci?n del cco
		list( $ccoCodigo, $ccoDescipcion ) = explode( "-", $slCcoDestino );   
		
		echo "<INPUT TYPE='hidden' name='slCcoDestino' value='$slCcoDestino'>";
		echo "<INPUT TYPE='hidden' name='fecha' value='$fecha'>";
		
		//Se debe mostrar las ultimas 24 horas si la fecha es la actual
		if( $fecha == date( "Y-m-d" ) ){
			
			//Busco la hora par mas cercana
			$hora = intval( date( "G" )/2 )*2+2;
			
			$fechaFinal = strtotime( date( "Y-m-d 00:00:00" ) ) + $hora*3600 - 1;
			
			$fechaInicial = $fechaFinal - $tiempoAMostrar*3600 + 1;
			
			$hora = gmdate( "G", $hora*3600 );
		}
		else{
			$hora = 0;
			
			$fechaInicial = strtotime( $fecha." 00:00:00" );
			$fechaFinal = $fechaInicial + $tiempoAMostrar*3600-1;
		}
		
		$informacionArticulos = Array();		
		
		/************************************************************************************************************************
		 * Consulto todas las aplicaciones pacientes y la guardo en un arreglo para consultar las aplicaciones 
		 * de cada medicamento por variable
		 ************************************************************************************************************************/		
		 //AND SUBSTRING_INDEX( aplron, ':', 1 ) % 2 = 0			Se quita esta linea para que salgan todos los articulos asi no se encuentren en hora par
		$sql = "SELECT 
					Aplfec, Aplhis, Apling, Aplart, SUBSTRING_INDEX( aplron, ':', 1 ) as Ronda, SUBSTRING_INDEX( aplron, '-', -1 ) as Meridiano, sum(Apldos) as Apldos, Aplido
				FROM
					{$wbasedato}_000020 b, {$wbasedato}_000015 a
				WHERE
					habcco = '$ccoCodigo'
					AND habhis != ''
					AND aplhis = habhis
					AND apling = habing
					AND aplfec BETWEEN '".date( "Y-m-d", $fechaInicial )."' AND '".date( "Y-m-d", $fechaFinal )."'
					AND aplest = 'on'
				GROUP BY
					1,2,3,4,5,6,8
				";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
		while( $rows = mysql_fetch_array($res) ){
		
			//Conviero la ronda en la hora par mas cercano igual o anterior la hora
			$rows[ 'Ronda' ] = intval( $rows[ 'Ronda' ]/2 )*2;
		
			if( strtoupper( trim( $rows['Meridiano'] ) ) == "PM" ){	//Si la ronda es PM
				if( $rows[ 'Ronda' ]*1 < 12 ){
					$rows[ 'Ronda' ] += 12;
				}
			}
			else{	//Si la ronda es AM
				if( $rows[ 'Ronda' ]*1 == 12 ){
					$rows[ 'Ronda' ] = 0;
				}
				else{
					$rows[ 'Ronda' ] = $rows[ 'Ronda' ]*1;
				}
			}
			
			if( $rows[ 'Aplfec' ] == date( "Y-m-d", $fechaInicial ) ){
				if( $rows[ 'Ronda' ] >= date( "G", $fechaInicial ) ){
					@$aplicaciones[ $rows['Aplhis']."-".$rows['Apling'] ][ strtoupper( $rows['Aplart'] ) ][ $rows['Aplido'] ][ $rows['Ronda']*1 ] = $rows['Apldos']*1;
				}
			}
			else{
				if( $rows[ 'Ronda' ] <= date( "G", $fechaInicial ) ){
					@$aplicaciones[ $rows['Aplhis']."-".$rows['Apling'] ][ strtoupper( $rows['Aplart'] ) ][ $rows['Aplido'] ][ $rows['Ronda']*1 ] = $rows['Apldos']*1;
				}
			}
		}		
		/************************************************************************************************************************/
				
		$sql = "SELECT
					Habcco,Habcod,Habhis,Habing, CONCAT(pacno1,' ',pacno2,' ',pacap1,' ',pacap2) as Nombre, Cconom, Ubiptr, Ubialp, Ubifap, Ubihap, pacced, pactid
				FROM
					{$wbasedato}_000020,{$wbasedato}_000018,{$wbasedato}_000011,root_000036, root_000037
				WHERE
					habcco = '$ccoCodigo'
					AND habest = 'on'
					AND ubihis = habhis
					AND ubiing = habing
					AND orihis = habhis
					AND ccocod = habcco
					AND ccoipd = 'on'
					AND ccoest = 'on'
					AND oriori = '$wemp_pmla'
					AND pacced = Oriced
					AND pactid = Oritid
				ORDER BY
					Habord, Habcod
				";
			
		$resHabitaciones = mysql_query( $sql, $conex ) or die( mysql_error()." - Error en el query $sql -".mysql_error() );
		$numHabitaciones = mysql_num_rows( $resHabitaciones );
		
		if( $numHabitaciones > 0 ){
			
			//Recorro los pacientes encontrados
			for( $i = 0; $rowsHabitaciones = mysql_fetch_array( $resHabitaciones ); $i++ ){
				
				$datosHabitaciones[ $i ] = new habitacion( $ccoCodigo, $rowsHabitaciones['Cconom'], $rowsHabitaciones['Habcod'], $rowsHabitaciones['Habhis'], $rowsHabitaciones['Habing'], $rowsHabitaciones['Nombre'], $rowsHabitaciones['Ubiptr'], $rowsHabitaciones['Ubialp'], $rowsHabitaciones['Ubifap'], $rowsHabitaciones['Ubihap'], $rowsHabitaciones['pacced'], $rowsHabitaciones['pactid'] );
		
				if( $rowsHabitaciones['Habhis'] != '' ){
					
					//Consulto la informaci?n del kardex ronda por ronda
					for( $fechaInicial = $fechaFinal-$tiempoAMostrar*3600+1; $fechaInicial <= $fechaFinal; $fechaInicial += 2*3600 ){
					
						list( $fecha, $whora_par_actual ) = explode( "|", date( "Y-m-d|G", $fechaInicial) );	//Traigo la fecha y hora sin ceros iniciales y pares
						
						if( !isset( $datosHabitaciones[ $i ]->kardexConfirmado[ $fecha ] ) ){
							$datosHabitaciones[ $i ]->kardexConfirmado[ $fecha ] = consultarKardexConfirmado( $datosHabitaciones[ $i ]->historia, $datosHabitaciones[ $i ]->ingreso, $fecha );
						}
						
						if( $fecha == date( "Y-m-d" ) ){
							$datosHabitaciones[ $i ]->horas[ $whora_par_actual ]->esKardexConfirmado = $datosHabitaciones[ $i ]->kardexConfirmado[ $fecha ];
						}
						else{
							$datosHabitaciones[ $i ]->horas[ $whora_par_actual ]->esKardexConfirmado = 'on';
						}
						
						if( !isset( $datosHabitaciones[ $i ]->tieneKardex[ $fecha ] ) ){
							$datosHabitaciones[ $i ]->tieneKardex[ $fecha ] = tieneKardex( $datosHabitaciones[ $i ]->historia, $datosHabitaciones[ $i ]->ingreso, $fecha );
						}
						
						$datosHabitaciones[ $i ]->horas[ $whora_par_actual ]->tieneKardex = $datosHabitaciones[ $i ]->tieneKardex[ $fecha ];
						
						//Consulto todos los medicamentos por aplicar por ronda
						//tomando la funcion query_articulos de movhos.inc.php
						//Adicionalmente, si el kardex no esta confirmado o no tiene kardex, busco el dia anterior
						if( $datosHabitaciones[ $i ]->tieneKardex[ $fecha ] != 'on' || $datosHabitaciones[ $i ]->kardexConfirmado[ $fecha ] != 'on' ){
						
							if( !$datosHabitaciones[ $i ]->consultoArticulosSinConfirmar ){
								$datosHabitaciones[ $i ]->artSinConfirmar = articulosSinConfirmar( $rowsHabitaciones['Habhis'], $rowsHabitaciones['Habing'], date( "Y-m-d", $fechaInicial-24*3600 ) );
								$datosHabitaciones[ $i ]->consultoArticulosSinConfirmar = true;
							}
							
							query_articulos( $rowsHabitaciones['Habhis'], $rowsHabitaciones['Habing'], date( "Y-m-d", $fechaInicial-24*3600 ), &$resArticulos );
							$fechaBaseKardex = date( "Y-m-d", $fechaInicial-24*3600 );
						}
						else{
						
							if( !$datosHabitaciones[ $i ]->consultoArticulosSinConfirmar ){
								$datosHabitaciones[ $i ]->artSinConfirmar = articulosSinConfirmar( $rowsHabitaciones['Habhis'], $rowsHabitaciones['Habing'], $fecha );
								$datosHabitaciones[ $i ]->consultoArticulosSinConfirmar = true;
							}
							
							query_articulos( $rowsHabitaciones['Habhis'], $rowsHabitaciones['Habing'], $fecha, &$resArticulos );
							$fechaBaseKardex = $fecha;
						}
						
						$numArticulos = mysql_num_rows( $resArticulos );
						
						if( $numArticulos > 0 ){
						
							$arts2= array();
							rondaConArticuloSinConfirmar( $datosHabitaciones[ $i ]->artSinConfirmar, $whora_par_actual, $arts, $arts2 );
							
							//recorro todas la filas encontradas
							for( $j = 0; $rowsArticulos = mysql_fetch_array($resArticulos); $j++ ){
							
								/********************************************************************************************************
								 * Febrero 24 de 2011
								 ********************************************************************************************************/
								$cambioFrecuencia = false;
								//Si hay cambio de frecuencia y el cambio fue realizado posterior a la confirmacion del kardex, no muesto el articulo
								if( !empty( $rowsArticulos['Kadfra'] ) ){
									
									$fhCambioFrecuencia = strtotime( $rowsArticulos['Kadfcf']." ".$rowsArticulos['Kadhcf'] );
									
									if( $fhCambioFrecuencia > $fechaInicial || ( $rowsArticulos['Karhco'] != "00:00:00" && $fechaInicial < strtotime( $fechaBaseKardex." ".$rowsArticulos['Karhco'] ) ) ){
										$rowsArticulos['perequ'] = consultarFrecuencia( $rowsArticulos['Kadfra'] );
										
										//Averiguo si no pertenece a la ronda
										if( ( strtotime( $rowsArticulos['kadfin']." ".$rowsArticulos[5] ) - $fechaInicial )%($rowsArticulos['perequ']*3600) != 0 ){
											$cambioFrecuencia = true;
										}
									}
								}
								/********************************************************************************************************/
								
								if( !$cambioFrecuencia ){
							
									//debo verificar que la ronda si pertenece al dia, el query no lo valida								
									if( strtotime( $rowsArticulos['kadfin']." ".$rowsArticulos[5].":00:00" ) <= strtotime( "$fecha ".gmdate( "H:00:00", $whora_par_actual*3600 ) ) ){
										consultarInformacionProducto( $rowsArticulos['kadart'], $informacionArticulos );
										
										if( empty( $arts2[ $rowsArticulos['kadart'] ] ) ){	//Si es vacio, esta confirmado
										
											if( !esArticuloGenerico( $rowsArticulos['kadart'] ) ){	//Si es articulo generico se agrega, si no no
												//proceso la informacion
												$datosHabitaciones[ $i ]->procesarMedicamento( $whora_par_actual, $rowsArticulos['kadart'], $informacionArticulos[ $rowsArticulos['kadart'] ]['nombreComercial'],$rowsArticulos['Kadcfr'], $rowsArticulos['Kadufr'], $rowsArticulos['Kadsus'], $rowsArticulos['Kadcnd'], true, strtotime( $rowsArticulos['kadfin']." ".$rowsArticulos[5].":00:00" ), $rowsArticulos['perequ'], $fechaBaseKardex, $rowsArticulos['Kaddma'], $rowsArticulos['Kaddia'], $rowsArticulos );
											}
										}
										else{	//El articulo esta sin confirmar
											//proceso la informacion
											$datosHabitaciones[ $i ]->procesarMedicamento( $whora_par_actual, $rowsArticulos['kadart'], $informacionArticulos[ $rowsArticulos['kadart'] ]['nombreComercial'],$rowsArticulos['Kadcfr'], $rowsArticulos['Kadufr'], $rowsArticulos['Kadsus'], $rowsArticulos['Kadcnd'], false, strtotime( $rowsArticulos['kadfin']." ".$rowsArticulos[5].":00:00" ), $rowsArticulos['perequ'], $fechaBaseKardex, $rowsArticulos['Kaddma'], $rowsArticulos['Kaddia'], $rowsArticulos );
										}
									}
								}
							}
						}	//Fin total articulos encontrados
					}	//Fin horas
				}	//fin habitacion
			}	//Fin for pacientes encontrados
		}	//Fin habitaciones encontradas		
		
		if( !empty( $datosHabitaciones ) ){
			pintarDatosFila( $datosHabitaciones );
		}
		else{
			echo "<center><b>No se encontraron datos</b></center>";
		}
	
		echo "<br><table align='center'>";
		
		echo "<tr><td>";
		echo "<center><INPUT type='button' value='Retornar' onclick='document.forms[0].submit();' style='width:100px'></center>";
		echo "</td>";
		
		echo "<td>";
		echo "<center><INPUT type='button' value='Cerrar' onclick='cerrarVentana();' style='width:100px'></center>";
		echo "</td></tr>";
		
		echo "<INPUT type='hidden' name='mostrar' id='mostrar' value=''>";
		
		echo "</table>";
	}
	
	echo "</form>";
}
?>
</body>