<html>
<head>

<script type="text/javascript" src="../../../include/root/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.core.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.tabs.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.draggable.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery-ui.min.js"></script>


<script type="text/javascript" src="../../../include/root/jquery.dimensions.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.simple.tree.js"></script>

<script>
	function mostrar( celda ){
	
		if( $("div", celda ) ){
	
			$.blockUI({ message: $("div", celda ), 
							css: { left: ( $(window).width() - 800 )/2 +'px', 
								    top: ( $(window).height() - $("div", celda ).height() )/2 +'px',
								  width: '800px'
								 } 
					  });
			
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
 * Descripción general:	Mostrar las aplicaciones faltantes por cama por cada ronda, basada en el kardex
 **********************************************************************************************************************************************************/
 
/**********************************************************************************************************************************************************
 * Especificaciones:
 *
 * - Por cada cama mostrar en que rondas no se ha aplicado medicamentos, estas estarán de color rojo
 * - Por cada cama que se encuentre parcialmente aplicada, se encontrará de color amarillo
 * - Por cada cama que se encuentre totalmente aplicada, se encontrará de color verde
 * - Al dar click sobre una hora, mostrar un pequeño detalle de como se encuentra las aplicaciones del paciente para la hora determinada
 **********************************************************************************************************************************************************/
 
//Contiene la información básica de una habitación
class habitacion{

	var $codigo;
	var $cco;
	var $historia;
	var $ingreso;
	var $nombre;
	var $horas;		//horas de aplicación,
	var $ccoNombre;

	//constructor de clase
	function habitacion( $cco, $ccoNombre, $hab, $historia, $ingreso, $nombre ){
	
		global $wbasedato;
		global $conex;
	
		$this->codigo = $hab;
		$this->cco = $cco;
		$this->ccoNombre = $ccoNombre;
		$this->historia = $historia;
		$this->ingreso = $ingreso;
		$this->nombre = $nombre;
		
		//creo el array con objeto vacio hora
		for( $i = 0; $i < 24; $i += 2 ){
			$this->horas[ $i ] = new hora();
		}
	}
	
	/********************************************************************************
	 * Agrega un medicamento a una hora
	 ********************************************************************************/
	function agregarMedicamento( $ronda, $codigoArticulo, $nombre, $cantidadCargada, $unidad, $suspendido, $aNecesidad ){
		
		global $aplicaciones;
		
		$info = Array();
		$info[ 'codigo' ] = $codigoArticulo;
		$info[ 'nombre' ] = $nombre;
		$info[ 'cantidadAAplicar' ] = $cantidadCargada;
		$info[ 'unidad' ] = $unidad;
		$info[ 'cantidadAplicada' ] = @$aplicaciones[ $this->historia."-".$this->ingreso ][$codigoArticulo][$ronda]; //$this->consultarCantidadAplicada( $ronda, $codigoArticulo );
		$info[ 'suspendido' ] = ( $suspendido == 'on' )? "S&iacute;" : "No";
		
		$expCondicion = explode( " - ", esANecesidad( $aNecesidad ) );
		
		@$info[ 'aNecesidad' ] = ( $expCondicion[1] == "AN" )? "S&iacute;" : "No"; //( esANecesidad( $aNecesidad ) )? "S&iacute;" : "No";
		$info[ 'condicion' ] = $expCondicion[0];
		
		$this->horas[ $ronda ]->agregarMedicamento( $codigoArticulo, $info );
	}
};

class hora{

	var $medicamentos = Array();	//lista de medicamentos, solo el codigo
	var $color = 0;			//Solo el numero, ya hay array global que indica el color
	var $descripcion = '';	//Si es total, parcial o no aplicada
	
	var $totalAplicados = 0;
	var $totalMedicamentos = 0;
	var $totalMedicamentosNecesidad = 0;	//Indica cuantos medicamentos a Necesidad hay
	
	/********************************************************************************
	 * Agrega la información pertinente para un articulo
	 ********************************************************************************/
	function agregarMedicamento( $codigo, $infoArticulo ){
		$this->medicamentos[ $codigo ] = $infoArticulo;
		
		$this->totalMedicamentos++;
		
		if( $this->medicamentos[ $codigo ][ 'cantidadAAplicar' ] == $this->medicamentos[ $codigo ][ 'cantidadAplicada' ] ){
			$this->totalAplicados++;
		}
		
		if( $infoArticulo[ 'aNecesidad' ] != "No" ){
			$this->totalMedicamentosNecesidad++;
		}
	}
	
	function estado(){
	
		$val = '';
		
		if( $this->totalMedicamentos > 0 ){
		
			if( $this->totalAplicados > 0 ){
				if( $this->totalAplicados == $this->totalMedicamentos ){
					$this->color = CLR_VERDE;
				}
				else{
					$this->color = CLR_AMARILLO;
				}
			}
			else{
				if( $this->totalMedicamentos - $this->totalMedicamentosNecesidad > 0 ){
					$this->color = CLR_ROJO;
				}
			}
		}
		
		switch( $this->color ){
		
			case CLR_VERDE:
				$val = 'Total';
				break;
			
			case CLR_AMARILLO:
				$val = 'Parcial';
				break;
			
			case CLR_ROJO:
				$val = 'Sin Aplicar';
				break;
			
			default: $val = '';
				break;
		}
		
		$this->descripcion = $val;
	}
};

/************************************************************************************
 * Segun la condicion de suministro del articulo, dice si dicha condición 
 * es considerada a necesidad o no
 *
 * Septiembre 2011-09-11
 ************************************************************************************/
function esANecesidad( $condicion ){

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
 * Consulto la información básica del producto (nombre comercial y generico, unidad de medida y codigo
 * 
 * Guardo la información en una variable global, esto para no consultar la información de un articulo repetitivamente
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


/********************************************************************************
 * Pinta los datos necesarios en pantalla
 ********************************************************************************/
function pintarDatosFila( $datos ){

	global $colores;
	global $fecha;
		
	if( count($datos) > 0 ){
		
		$i = 0;
		foreach( $datos as $keyDatos => $valueDatos ){
		
			// $valueDatos->consultarCantidadAplicada();
		
			if( $i == 0 ){
				echo "<table align='center'>";
		
				echo "<tr class='fila2'>";
				echo "<td colspan=12 style='font-size:14pt'>";
				echo "<b>".$valueDatos->cco." - ".$valueDatos->ccoNombre."</b>";
				echo "</td>";
				echo "<td colspan=4 style='font-size:14pt' align='right'>";
				echo "<b>".$fecha."</b>";
				echo "</td>";
				echo "</tr>";
				
				echo "<tr class='encabezadotabla' align='center'>";
				// echo "<td rowspan=2>Centro de costos</td>";
				echo "<td rowspan=2>Habitaci&oacute;n</td>";
				echo "<td rowspan=2>Historia</td>";
				echo "<td rowspan=2>Nombre</td>";
				echo "<td colspan=12>Horas</td>";
				echo "</tr>";
				
				echo "<tr class='encabezadotabla'>";
				
				for( $i = 0; $i < 24; $i += 2 ){
					echo "<td align='center' style='width:50'>$i</td>";
				}
				
				echo "</tr>";
			}
		
			$class = "class='fila".(($i%2)+1)."'";
			$i++;
			echo "<tr $class>";
		
			echo "<td align=center style='height:40px'>";
			echo $valueDatos->codigo;
			echo "</td>";
			
			echo "<td align=center>";
			echo $valueDatos->historia." - ".$valueDatos->ingreso;
			echo "</td>";
			
			echo "<td>";
			echo $valueDatos->nombre;
			echo "</td>";
			
			//imprimo lo que hay por horas
			foreach( $valueDatos->horas as $keyHoras => $valueHoras ){
				$valueHoras->estado();
				
				echo "<td class='fondo".$colores[ $valueHoras->color ]."' align='center' onClick='mostrar( this )'>";
				
				echo $valueHoras->descripcion;
				
				if( count($valueHoras->medicamentos) > 0 && !empty($valueHoras->color) ){
				
					//Aqui se pinta el minireporte para mostrar
					echo "<div style='display:none;width:100%' title='Informaci&oacute;n para las $keyHoras:00:00'>";
				
					echo "<table width=100%>";
					
					echo "<tr class='fondo".$colores[ $valueHoras->color ]."' align='center'>";
					if( $keyHoras >= 10 ){
						echo "<td style='font-size:14pt'><b>Informaci&oacute;n para las $keyHoras:00:00</b></td>";
					}
					else{
						echo "<td style='font-size:14pt'><b>Informaci&oacute;n para las 0$keyHoras:00:00</b></td>";
					}
					echo "</tr>";
					
					echo "<tr class='fila2'>";
					echo "<td>";
					echo "<b>".$valueDatos->historia." - ".$valueDatos->ingreso;
					echo "<br>".$valueDatos->nombre."(".$valueDatos->codigo.")";					
					echo "</td>";
					echo "<tr>";
					
					echo "</table>";
					echo "<br><br>";
				
					echo "<table align='center'>";
					
					echo "<tr align='center' class='encabezadotabla'>";
					echo "<td>C&oacute;digo</td>";
					echo "<td>Nombre</td>";
					echo "<td>Cantidad a Aplicar</td>";
					echo "<td>Cantidad Aplicada</td>";
					echo "<td>Suspendido</td>";
					echo "<td>Condici&oacute;n</td>";
					echo "<td>A necesidad</td>";
					echo "</tr>";
					
					$j = 0;
					foreach( $valueHoras->medicamentos as $keyMedicamentos => $valueMedicamentos ){
						
						$class2 = "class='fila".(($j%2)+1)."'";
						$j++;
						
						echo "<tr $class2>";
						
						echo "<td>";
						echo $keyMedicamentos;
						echo "</td>";
						
						echo "<td style='width:300px'>";
						echo $valueMedicamentos['nombre'];
						echo "</td>";
						
						echo "<td align='center'>";
						echo $valueMedicamentos['cantidadAAplicar']." ".$valueMedicamentos['unidad'];
						echo "</td>";
						
						echo "<td align='center'>";
						echo $valueMedicamentos['cantidadAplicada'];
						
						if( $valueMedicamentos['cantidadAplicada'] > 0 ){
							echo " ".$valueMedicamentos['unidad'];
						}
						
						echo "</td>";
						
						echo "<td align='center'>";
						echo $valueMedicamentos['suspendido'];
						echo "</td>";
						
						echo "<td align='center'>";
						echo $valueMedicamentos['condicion'];
						echo "</td>";
						
						echo "<td align='center'>";
						echo $valueMedicamentos['aNecesidad'];
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

	encabezado( "APLICACIONES FALTANTES POR CAMA", "1.0 Septiembre 6 de 2011" ,"clinica" );

	echo "<form method=post>";

	if( empty($fecha) ){
		$fecha = date( "Y-m-d" );
	}
	
	if( empty($mostrar) ){
		$mostrar = "off";
	}
	
	echo "<INPUT type='hidden' name='wemp_pmla' value='$wemp_pmla'>";
	
	if( $mostrar == "off"  ){
	
		echo "<table align='center'>";

		echo "<tr class='encabezadotabla'>";
		echo "<td class='fila1'>Centro de costos</td>";
		
		//Buscando los centro de costos de traslado (SF y CM)
		//Estos son los de origen
		$sql = "SELECT
					*
				FROM
					{$wbasedato}_000011
				WHERE
					ccoest = 'on'
					AND ccoipd = 'on'
					AND ccoest = 'on'
				";
				
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		echo "<td class='fila2'>";
		echo "<select name='slCcoDestino'>";
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
			
			if( isset($slCcoDestino) && $slCcoDestino == "{$rows['Ccocod']} - {$rows['Cconom']}" ){
				echo "<option selected value='{$rows['Ccocod']} - {$rows['Cconom']}'>{$rows['Ccocod']} - {$rows['Cconom']}</option>";
			}
			else{
				echo "<option value='{$rows['Ccocod']} - {$rows['Cconom']}'>{$rows['Ccocod']} - {$rows['Cconom']}</option>";
			}
		}
		
		echo "</select>";
		echo "</td>";
		
		echo "</tr>";
		echo "<tr>";
		
		echo "<td class='fila1'><b>Fecha</b></td>";
		
		echo "<td class='fila2'>";
		campoFechaDefecto( "fecha", $fecha );
		echo "</td>";
		echo "</tr>";

		
		echo "</tr>";

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
		//Defino constantes para los colores y poder usarlos en el programa
		//Esto permite que lo pueda cambiar los colores sin problemas
		define("CLR_VERDE", 1 );
		define("CLR_AMARILLO", 2 );
		define("CLR_ROJO", 3 );

		//Creo Array de colores, sirven para pintar las celdas de los distintos colores
		$colores = Array();
		$colores[ 0 ] = "NA";
		$colores[ CLR_VERDE ] = "Verde";		
		$colores[ CLR_AMARILLO ] = "Amarillo";
		$colores[ CLR_ROJO ] = "Rojo";
		
		
		//$fecha es la fecha actual y es global
		// $fecha = date("Y-m-d");
		
		//Separo la información del cco
		list( $ccoCodigo, $ccoDescipcion ) = explode( " - ", $slCcoDestino );
		
		echo "<INPUT TYPE='hidden' name='slCcoDestino' value='$slCcoDestino'>";
		echo "<INPUT TYPE='hidden' name='fecha' value='$fecha'>";
		
		$informacionArticulos = Array();		
		
		/************************************************************************************************************************
		 * Consulto todas las aplicaciones pacientes y la guardo en un arreglo para consultar las aplicaciones 
		 * de cada medicamento por variable
		 ************************************************************************************************************************/
		$sql = "SELECT 
					Aplhis, Apling, Aplart, SUBSTRING_INDEX( aplron, ':', 1 ) as Ronda, SUBSTRING_INDEX( aplron, '-', -1 ) as Meridiano, sum(Apldos) as Apldos
				FROM
					{$wbasedato}_000020 b, {$wbasedato}_000015 a
				WHERE
					habcco = '$ccoCodigo'
					AND habhis != ''
					AND aplhis = habhis
					AND apling = habing
					AND aplfec = '$fecha'
					AND aplest = 'on'
					AND SUBSTRING_INDEX( aplron, ':', 1 ) % 2 = 0
				GROUP BY
					1,2,3,4,5
				";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
		while( $rows = mysql_fetch_array($res) ){
		
			if( strtoupper( trim( $rows['Meridiano'] ) ) == "PM" ){
				if( $rows[ 'Ronda' ] < 12 ){
					$rows[ 'Ronda' ] += 12;
				}
			}
			else{
				if( $rows[ 'Ronda' ] == 12 ){
					$rows[ 'Ronda' ] = 0;
				}
				else{
					$rows[ 'Ronda' ] = $rows[ 'Ronda' ]*1;
				}
			}
		
			@$aplicaciones[ $rows['Aplhis']."-".$rows['Apling'] ][ $rows['Aplart'] ][ $rows['Ronda']*1 ] = $rows['Apldos'];
		}		
		/************************************************************************************************************************/
		 
		
		//Consulto todos los pacientes para el cco seleccionado
		$sql = "SELECT
					Habcco,Habcod,Habhis,Habing, CONCAT(pacno1,' ',pacno2,' ',pacap1,' ',pacap2) as Nombre, Cconom
				FROM
					{$wbasedato}_000020,{$wbasedato}_000011,root_000036, root_000037
				WHERE
					habcco = '$ccoCodigo'
					AND habest = 'on'
					AND orihis = habhis
					AND oriori = '$wemp_pmla'
					AND pacced = Oriced
					AND pactid = Oritid
					AND ccocod = habcco
					AND ccoipd = 'on'
					AND ccoest = 'on'
				ORDER BY
					habcco, habcod
				";
			
		$resHabitaciones = mysql_query( $sql, $conex ) or die( mysql_error()." - Error en el query $sql -".mysql_error() );
		$numHabitaciones = mysql_num_rows( $resHabitaciones );
		
		if( $numHabitaciones > 0 ){
			
			for( $i = 0; $rowsHabitaciones = mysql_fetch_array( $resHabitaciones ); $i++ ){
			
				$datosHabitaciones[ $i ] = new habitacion( $ccoCodigo, $rowsHabitaciones['Cconom'], $rowsHabitaciones['Habcod'], $rowsHabitaciones['Habhis'], $rowsHabitaciones['Habing'], $rowsHabitaciones['Nombre'] );
		
				if( $rowsHabitaciones['Habhis'] != '' ){
				
					for( $whora_par_actual = 0; $whora_par_actual < 24; $whora_par_actual += 2 ){

						//Consulto todos los medicamentos por aplicar por ronda
						//tomando la funcion query_articulos de movhos.inc.php					
						query_articulos( $rowsHabitaciones['Habhis'], $rowsHabitaciones['Habing'], $fecha, &$resArticulos );
						
						$numArticulos = mysql_num_rows( $resArticulos );
						
						if( $numArticulos > 0 ){
							
							//recorro todas la filas encontradas
							for( $j = 0; $rowsArticulos = mysql_fetch_array($resArticulos); $j++ ){
							
								//debo verificar que la ronda si pertenece al dia, el query no lo valida								
								if( strtotime( $rowsArticulos['kadfin']." ".$rowsArticulos[5].":00:00" ) <= strtotime( "$fecha ".gmdate( "H:00:00", $whora_par_actual*3600 ) ) ){
									consultarInformacionProducto( $rowsArticulos['kadart'], $informacionArticulos );
									
									//proceso la informacion
									$datosHabitaciones[ $i ]->agregarMedicamento( $whora_par_actual, $rowsArticulos['kadart'], $informacionArticulos[ $rowsArticulos['kadart'] ]['nombreComercial'],$rowsArticulos['Kadcfr'], $rowsArticulos['Kadufr'], $rowsArticulos['Kadsus'], $rowsArticulos['Kadcnd'] );
								}
							}
						}
						
					}
				}
			}
		}		
		
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
		
		echo "</table>";
	}
	
	echo "</form>";
}
?>
</body>