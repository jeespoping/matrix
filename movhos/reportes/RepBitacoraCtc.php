<html>
<head>
<title>MATRIX - [REPORTE BITACORA CTC]</title>

<!-- Funciones Javascript -->
<SCRIPT LANGUAGE="javascript">
	function enviar(){  
		document.forma.submit();		
	}
</SCRIPT>

</head>

<?php
include_once("conex.php");
/*
 * REPORTE BITACORA CTC
 */
//BS'D=================================================================================================================================
//PROGRAMA: RepBitacoraCtc.php
//AUTOR: Mauricio Sánchez Castaño.
//TIPO DE SCRIPT: reporte
//RUTA DEL SCRIPT: matrix\MOVHOS\Reportes\RepBitacoraCtc.php

//HISTORIAL DE REVISIONES DEL SCRIPT:
//+-------------------+------------------------+----------------------------------------------+
//|	   FECHA          |     AUTOR              |   MODIFICACION							      |
//+-------------------+------------------------+----------------------------------------------+
//|  2012-07-04       | Viviana Rodas          | Se agrega el llamado a las funciones         |
//|                   |                        |  consultaCentrosCostos que hace la consulta  |
//|                   |                        |  de los centros de costos de un grupo        |
//|                   |                        |  seleccionado y dibujarSelect                | 
//|                   |                        |  que dibuja el select con los                |
//|                   |                        |  centros de costos obtenidos de              |
//|                   |                        |  la primera funcion.                         |
//+-------------------------------------------------------------------------------------------+
//|  2011-03-15       | Juan C. Hernandez      | Se coloca la columna observacion como        |
//|                   |                        | TEXTAREA solo de lectura.                    |
//|  2009-10-22       | Mauricio Sánchez       | Se remueve join con la tabla 11		      |
//|  2009-02-16       | Mauricio Sánchez       | creación del script.					      |
//+-------------------+------------------------+----------------------------------------------+
	
//FECHA ULTIMA ACTUALIZACION 	: 2012-07-04


//=================================================================================================================================*/
class registroValorDescripcion{
	var $valor;
	var $descripcion;
}

class bitacoraCtc{
	var $consecutivo;
	var $fechaEventoNoPos;
	var $historiaClinica;
	var $ingresoHistoria;
	var $identificacionPaciente;
	var $nombrePaciente;
	var $servicioQueOrigina;
	var $entidadPagadora;
	var $observaciones;
	var $eventoNoPos;
	var $estadoCtc;
}

//Funciones
/**
 * Consulta generica de detalle de selecciones.
 * 
 * Usada para Entidades, Ctc de y estado. 
 *
 * @param unknown_type $conex
 * @param unknown_type $codigoFactura
 * @param unknown_type $fuenteFactura
 * @return unknown
 */
function consultarDetalleSeleccion($conex, $basedatos, $codigo)
{
	$q = "SELECT 
				subcodigo, descripcion   
		FROM 
				det_selecciones 
		WHERE 				 
				medico = '$basedatos'  
				AND codigo = '$codigo'
				AND activo = 'A'";
	
	$coleccion = array();
	
	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);	
	
	$cont1 = 0;
	
	while ($cont1 < $num)
	{
		$tupla = new registroValorDescripcion();		
		
		$resultSet = mysql_fetch_array($res);

		$tupla->valor = $resultSet['subcodigo'];
		$tupla->descripcion = $resultSet['descripcion'];
		
		$coleccion[] = $tupla;
		$cont1++;
	}
	
	return $coleccion;
}

/**
 * Consulta de la bitácora de CTC
 * 
 * @param unknown_type $conex
 * @param unknown_type $codigoFactura
 * @param unknown_type $fuenteFactura
 * @return unknown
 */
function consultarBitacoraCtc($conex, $basedatos, $fechaInicial, $fechaFinal, $ctc)
{
	$q = "SELECT 
			Consecutivo,Fecha_Orden_Evento_No_Pos, Historia_Clinica, Ingreso_historia_clinica, Identificacion_paciente, Paciente, Servicio_Que_Origina, Entidad_Pagadora, Evento_no_pos, Estado_ctc, (SELECT Cconom FROM {$basedatos}_000011 WHERE Ccocod = Servicio_Que_Origina) Cconom, Observaciones				 
		FROM 
			".$basedatos."_000057 
		WHERE 				 
			Fecha_Orden_Evento_No_Pos BETWEEN '$fechaInicial' AND '$fechaFinal'
			AND Servicio_Que_Origina LIKE '$ctc->servicioQueOrigina'
			AND SUBSTRING_INDEX( Entidad_Pagadora, '-', 1 ) LIKE '$ctc->entidadPagadora'
			AND SUBSTRING_INDEX( Ctc_de, '-', 1 ) LIKE '$ctc->tipoCtc'
			AND SUBSTRING_INDEX( Estado_ctc, '-', 1 ) LIKE '$ctc->estadoCtc'			 
		ORDER BY Fecha_Orden_Evento_No_Pos, Entidad_Pagadora, Estado_ctc";
	
//	echo $q;
	
	$coleccion = array();
	
	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);	
	
	$cont1 = 0;
	
	while ($cont1 < $num)
	{
		$solicitud = new bitacoraCtc();		
		$resultSet = mysql_fetch_array($res);

		$solicitud->consecutivo 					= $resultSet['Consecutivo'];
		$solicitud->fechaEventoNoPos 			= $resultSet['Fecha_Orden_Evento_No_Pos'];
		$solicitud->historiaClinica				= $resultSet['Historia_Clinica'];
		$solicitud->ingresoHistoria				= $resultSet['Ingreso_historia_clinica'];
		$solicitud->identificacionPaciente		= $resultSet['Identificacion_paciente'];
		$solicitud->nombrePaciente				= $resultSet['Paciente'];
		if(isset($resultSet['Cconom'])){
			$solicitud->servicioQueOrigina			= $resultSet['Servicio_Que_Origina']." - ".$resultSet['Cconom'];
		} else {
			$solicitud->servicioQueOrigina			= $resultSet['Servicio_Que_Origina'];
		}
		$solicitud->entidadPagadora				= $resultSet['Entidad_Pagadora'];
		$solicitud->eventoNoPos					= $resultSet['Evento_no_pos'];
		$solicitud->observaciones				= $resultSet['Observaciones'];	
		$solicitud->estadoCtc					= $resultSet['Estado_ctc']; 
		
		$coleccion[] = $solicitud;
		$cont1++;
	}
	return $coleccion;
}

/************************************************************************************
 ***************************************INICIO***************************************
 ************************************************************************************/
/*
 * Inicio aplicacion
 */
include_once("root/comun.php");

$wactualiz = " 2012-07-04";

if(!isset($_SESSION['user'])){
	echo "error";
}else{
	if(!isset($wemp_pmla)){
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");		
	}

	if(!isset($user)){
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."codigo de usuario");		
	}
	
	$conex = obtenerConexionBD("matrix");

	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$wentidad = "Clínica las Américas";

  	$wfecha=date("Y-m-d");
  	$hora = (string)date("H:i:s");
  	$wnomprog="RepBitacoraCtc.php";  //nombre del reporte
  	
  	$wcf1="#41627e";  //Fondo encabezado del Centro de costos
  	$wcf="#c2dfff";   //Fondo procedimientos
  	$wcf2="003366";   //Fondo titulo pantalla de ingreso de parametros
  	$wcf3="#659ec6";  //Fondo encabezado del detalle
  	$wclfg="003366";  //Color letra parametros
  	
  	//Semaforos de estados
  	$semaforoPendiente = "#CD5C5C";
  	$semaforoRadicado = "#87CEFA";
  	$semaforoNegadoDevuelto = "#F0E68C";
  	$semaforoAprobado = "#40E0D0";
  	$semaforoOtro = "#DCDCDC";
  	$semaforoEnviadoEntidad = "#E0FFFF";
  	$semaforoNoRealizado = "#41627e";
  	
  	echo "<form action='RepBitacoraCtc.php' method=post name='forma'>";
  	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";

  	encabezado("REPORTE BITACORA CTC", $wactualiz, "clinica");
  	
  	if (!isset($resultado))
  	{
		//Parámetros de consulta del reporte
  		if (!isset ($bandera))
  		{
  			$wfechaconsulta=$wfecha;
  		}

  		//Cuerpo de la pagina
  		echo "<table align='center' border=0 width=410>";
  			
  		echo '<span class="subtituloPagina2">';
  		echo "Par&aacute;metros de consulta";
  		echo "</span>";
  		echo "<br>";
  		echo "<br>";

  		//Fecha inicial de consulta
  		echo "<tr><td class='fila1'>Fecha inicial</td>";
  		echo "<td class='fila2' align='center'>";
  		campoFecha("wfechaconsulta");
  		echo "</td>";
  		echo "</tr>";
  			
  		//Fecha final de consulta
  		echo "<tr><td class='fila1'>Fecha final</td>";
  		echo "<td class='fila2' align='center'>";
  		campoFecha("wfechaconsulta2");
  		echo "</td>";
  		echo "</tr>";
  		
  		//Entidad
  		$colEntidades = consultarDetalleSeleccion($conex,$wbasedato,'04');

  		//Entidad
  		echo "<tr><td class='fila1'>Entidad</td>";
  		echo "<td class='fila2' align='center'>";
  		echo "<select name='wselent'>";
  		echo "<option value = '%'>Todas las entidades</option>";
  		foreach ($colEntidades as $entidad){
  			echo "<option value = '$entidad->valor'>$entidad->descripcion</option>";
  		}
  		echo "</select>";
  		echo "</td>";
  		echo "</tr>";
  			
  		//Tipo de CTC
  		$colTipos = consultarDetalleSeleccion($conex,$wbasedato,'03');
  		echo "<tr><td class='fila1'>Tipo de CTC</td>";
  		echo "<td class='fila2' align='center'>";
  		echo "<select name='wseltipctc'>";
  		echo "<option value = '%'>Todos los tipos</option>";
  		foreach ($colTipos as $tipo){
  			echo "<option value = '$tipo->valor'>$tipo->descripcion</option>";
  		}
  		echo "</select>";
  		echo "</td>";
  		echo "</tr>";
  			

  		//Estado del ctc
  		$colEstado = consultarDetalleSeleccion($conex,$wbasedato,'02');
  		echo "<tr><td class='fila1' width=64>Estado CTC</td>";
  		echo "<td class='fila2' align='center' width=200>";
  		echo "<select name='wselestctc'>";
  		echo "<option value = '%'>Todos los estados</option>";
  		foreach ($colEstado as $estado){
  			echo "<option value = '$estado->valor'>$estado->descripcion</option>";
  		}
  		echo "</select>";
  		echo "</td>";
  		echo "</tr>";

  		//Servicio
  		
		//**************** llamada a la funcion consultaCentrosCostos y dibujarSelect************
		$cco="Ccohos";
		$sub="off";
		$tod="Todos";
		$ipod="off";
		//$cco=" ";
		$centrosCostos = consultaCentrosCostos($cco);
					
		echo "<table align='center' border=0 width=410>";		
		$dib=dibujarSelect($centrosCostos, $sub, $tod, $ipod, "wselserctc");
					
		echo $dib;
		echo "</table>";
  			
  		echo "</table>";
  		
  		echo "<input type='HIDDEN' NAME= 'resultado' value='1'>";
    	
  		echo "<div align='center'><input type='submit' value='Consultar'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
  	} else {
	
		
		$wselserctc1=explode("-",$wselserctc);  //se separa el codigo del centro de costo del nombre.
		$wselserctc=$wselserctc1[0];
			 
  		echo "<table border=0 align=center >";
  		echo "<tr><td><B>Fecha inicial:</B> ".$wfechaconsulta."</td><td><B>Fecha final:</B> ".$wfechaconsulta2."</td>";
  		echo "</table>";

  		echo "<A href='RepBitacoraCtc.php?wemp_pmla=".$wemp_pmla."&wfechaconsulta=".$wfechaconsulta."&bandera='1'><center>VOLVER</center></A><br>";
  		echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";

  		echo "<input type='HIDDEN' NAME= 'wfechaconsulta' value='".$wfechaconsulta."'>";

  		echo "<br><br>Convenciones:";
  		echo "<table>";
  		echo "<tr>";
  		
  		echo "<td bgcolor='$semaforoAprobado' width=120><font text size=2>Autorizado</font></td>";
  		echo "<td bgcolor='$semaforoEnviadoEntidad' width=120><font text size=2>Enviado a entidad</font></td>";
  		echo "<td bgcolor='$semaforoNegadoDevuelto' width=120><font text size=2>Negado o devuelto</font></td>";
  		echo "<td bgcolor='$semaforoPendiente' width=120><font text size=2>Pendiente</font></td>";
  		echo "<td bgcolor='$semaforoRadicado' width=120><font text size=2>Radicado</font></td>";
  		echo "<td bgcolor='$semaforoOtro' width=120><font text size=2>No realizado</font></td>";
  		
  		echo "</tr>";
  		echo "</table>";
  		echo "<br><br>";
  		
  		//Resultados
  		echo "<table border=0 align=center>";
  			
  		//Encabezados de columna
  		echo "<tr class=encabezadoTabla>";
  		echo "<td align=center>CONS.</td>";
  		echo "<td align=center>FECHA EVENTO NO POS</td>";
  		echo "<td align=center>HISTORIA CLINICA</td>";
  		echo "<td align=center>IDENTIFICACION PACIENTE</td>";
  		echo "<td align=center>PACIENTE</td>";
  		echo "<td align=center>SERVICIO</td>";
  		echo "<td align=center>ENTIDAD</td>";
  		echo "<td align=center>EVENTO NO POS</td>";
  		echo "<td align=center>OBSERVACIONES</td>";
  		echo "<td align=center>ESTADO</td>";  		
  		echo "<td align=center>SEMAFORO</td>";
  		echo "</tr>";

  		$ctcConsulta = new bitacoraCtc();

  		$ctcConsulta->entidadPagadora = $wselent;
  		$ctcConsulta->tipoCtc = $wseltipctc;
  		$ctcConsulta->estadoCtc = $wselestctc;
  		$ctcConsulta->servicioQueOrigina = $wselserctc;

  		$colSolicitudesCtc = consultarBitacoraCtc($conex,$wbasedato,$wfechaconsulta,$wfechaconsulta2,$ctcConsulta);

  		foreach($colSolicitudesCtc as $solicitudCtc){

  			//Color del estado
  			$color = $semaforoOtro;
  			$vecEstadoCtc = explode("-",$solicitudCtc->estadoCtc);

  			switch ($vecEstadoCtc[0]) {
  				case '06':
  					$color = $semaforoPendiente;
  					break;
  				case '05':
  					$color = $semaforoRadicado;
  					break;
  				case '02':
  					$color = $semaforoNegadoDevuelto;
  					break;
  				case '04':
  					$color = $semaforoNegadoDevuelto;
  					break;
  				case '03':
  					$color = $semaforoAprobado;
  					break;
  				case '08':
  					$color = $semaforoEnviadoEntidad;
  					break;
  				default:
  					;
  					break;
  			}

  			//Dias del semaforo
  			$vecFecha = explode("-",$solicitudCtc->fechaEventoNoPos);

  			//defino fecha 1
  			$ano1 = $vecFecha[0];
  			$mes1 = $vecFecha[1];
  			$dia1 = $vecFecha[2];

  			//defino fecha 2
  			$ano2 = date("Y");
  			$mes2 = date("m");
  			$dia2 = date("d");

  			//calculo timestam de las dos fechas
  			$timestamp1 = mktime(0,0,0,$mes1,$dia1,$ano1);
  			$timestamp2 = mktime(4,12,0,$mes2,$dia2,$ano2);

  			//resto a una fecha la otra
  			$segundos_diferencia = $timestamp1 - $timestamp2;

  			//convierto segundos en días
  			$dias_diferencia = $segundos_diferencia / (60 * 60 * 24);

  			//obtengo el valor absoulto de los días (quito el posible signo negativo)
  			$dias_diferencia = abs($dias_diferencia);

  			//quito los decimales a los días de diferencia
  			$dias_diferencia = round($dias_diferencia);

  			echo "<tr>";
  			echo "<td align=left bgcolor=".$color."><font text size=2>$solicitudCtc->consecutivo</font></td>";
  			echo "<td align=left bgcolor=".$color."><font text size=2>$solicitudCtc->fechaEventoNoPos</font></td>";
  			echo "<td align=left bgcolor=".$color."><font text size=2>$solicitudCtc->historiaClinica - $solicitudCtc->ingresoHistoria</font></td>";
  			echo "<td align=left bgcolor=".$color."><font text size=2>$solicitudCtc->identificacionPaciente</font></td>";
  			echo "<td align=left bgcolor=".$color."><font text size=2>$solicitudCtc->nombrePaciente</font></td>";
  			echo "<td align=left bgcolor=".$color."><font text size=2>$solicitudCtc->servicioQueOrigina</font></td>";
  			echo "<td align=left bgcolor=".$color."><font text size=2>$solicitudCtc->entidadPagadora</font></td>";
  			echo "<td align=left bgcolor=".$color."><font text size=2>$solicitudCtc->eventoNoPos</font></td>";
  			//echo "<td align=left bgcolor=".$color."><font text size=2>$solicitudCtc->observaciones</font></td>";
			echo "<td align=left bgcolor=".$color."><font text size=2><TEXTAREA readonly cols=30 rows=4>$solicitudCtc->observaciones</TEXTAREA></font></td>";
  			echo "<td align=left bgcolor=".$color."><font text size=2>$vecEstadoCtc[1]</font></td>";

  			if($vecEstadoCtc[0] == '06'){
  				if ($dias_diferencia >= 0 && $dias_diferencia <= 3){
  					echo "<td align=center bgcolor='green'>$dias_diferencia dia(s)</td>";
  				}elseif ($dias_diferencia > 3 && $dias_diferencia <= 6){
  					echo "<td align=center bgcolor='yellow'>$dias_diferencia dia(s)</td>";
  				}elseif ($dias_diferencia > 6 && $dias_diferencia <= 14){
  					echo "<td align=center bgcolor=red>$dias_diferencia dia(s)</td>";
  				}elseif ($dias_diferencia > 14){
  					echo "<td align=center bgcolor=red>$dias_diferencia dia(s)</td>";
  				}
  			} else {
  				echo "<td align=center bgcolor=".$color."><font text size=2>$dias_diferencia dia(s)</font></td>";
  			}
  			
  		}
  		echo "</table>";
  		
  		if(count($colSolicitudesCtc) > 0){
  			echo "<br><br>Convenciones:";
  			echo "<table>";
  			echo "<tr>";

  			echo "<td bgcolor='$semaforoAprobado' width=120><font text size=2>Autorizado</font></td>";
  			echo "<td bgcolor='$semaforoEnviadoEntidad' width=120><font text size=2>Enviado a entidad</font></td>";
  			echo "<td bgcolor='$semaforoNegadoDevuelto' width=120><font text size=2>Negado o devuelto</font></td>";
  			echo "<td bgcolor='$semaforoPendiente' width=120><font text size=2>Pendiente</font></td>";
  			echo "<td bgcolor='$semaforoRadicado' width=120><font text size=2>Radicado</font></td>";
  			echo "<td bgcolor='$semaforoOtro' width=120><font text size=2>No realizado</font></td>";

  			echo "</tr>";
  			echo "</table>";

  			echo "<A href='RepBitacoraCtc.php?wemp_pmla=".$wemp_pmla."&wfechaconsulta=".$wfechaconsulta."&bandera='1'><center>VOLVER</center></A><br>";
  			echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
  		}
  	}
}
liberarConexionBD($conex);
?>
</html>