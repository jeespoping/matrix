<html>
<head>
<title>MATRIX - [REPORTE CAMBIOS KARDEX]</title>

<script type="text/javascript">

	function inicio(){ document.location.href='RepCambiosKardex.php?wemp_pmla='+document.forms.forma.wemp_pmla.value; }
	
	function consultar(){ 
		var formulario = document.forms.forma;
		document.location.href='RepCambiosKardex.php?wemp_pmla='+formulario.wemp_pmla.value+'&waccion=a&wminutos='+formulario.wminutos.value+'&wservicio='+formulario.wservicio.value; 
	}
	function abrirPerfil(historia,fechaKardex){
		window.open('../procesos/perfilFarmacoterapeutico.php?wemp_pmla='+document.forms.forma.wemp_pmla.value+'&waccion=a&whistoria=' + historia + '&wfecha=' + fechaKardex,
					'perfilFarmacologico','width=1200,toolbar=no,menubar=no,height=700,resizable=yes,location=no,scrollbars=yes');
	}
	function cerrarVentana(){
		window.close(); 
	}
</script>

</head>

<body>
<?php
include_once("conex.php");
/*BS'D
 * REPORTE CAMBIOS EN EL KARDEX
 * Autor: Mauricio Sánchez Castaño.
 */
include_once("movhos/kardex.inc.php");

$wactualiz = " 1.0 02-Jun-09";
$usuarioValidado = true;

if (!isset($user) || !isset($_SESSION['user'])){
	$usuarioValidado = false; 	
}else {
	if (strpos($user, "-") > 0)
		$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
}
if(empty($wuser) || $wuser == ""){
	$usuarioValidado = false;	
}

//Estas variables se incluyen para variar la empresa y el codigo de base de datos (esquema a apuntar).  Por defecto sera la 01
if(!isset($wemp_pmla)){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}

//Encabezado
encabezado("Reporte cambios en el kardex",$wactualiz,"clinica");

if (!$usuarioValidado){
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";
	
	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
}else{
	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
	$winstitucion = $institucion->nombre;
	
	//Base de datos, se generaliza de generar kardex
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	
	//Forma
	echo "<form name='forma' action='RepCambiosKardex.php' method='post'>";
	
	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'/>";
	echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'/>";
	echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
	
	//Verificar que la fecha de consulta sea la actual de lo contrario todo el perfil sera de consulta
	$fechaActual = date("Y-m-d");
	
	//Estrategia de FC con parámetro waccion
	if(!isset($waccion)){
		$waccion = "";
	}
	
	//FC para hacer las acciones
	switch ($waccion){
		case 'a':		//
			if(isset($wminutos) && isset($wservicio)){
				//Consulta de que ha cambiado n minutos hacia atrás de la fecha y hora actual.
				//Quien cambio, quien grabo inicialmente, quien cambio, fecha y hora
				
				//Campos a desplegar: Historia, ingreso, mensaje auditoria, descripcion auditoria, usuario, agrupado por servicio
				echo '<span class="subtituloPagina2" align="center">';
				echo "Cambios realizados hace $wminutos minutos desde la fecha y hora actual";
				echo "</span><br><br>";

				echo "<table align='center'>";

				echo "<tr class='encabezadoTabla' align='center'>";
				
				/*
				$detalle->historia 			= 		$info['Kauhis'];
				$detalle->ingreso 			= 		$info['Kauing'];
				$detalle->fechaKardex 		= 		$info['Kaufec'];
				$detalle->fechaRegistro		= 		$info['Fecha_registro'];
				$detalle->horaRegistro		= 		$info['Hora_registro'];
				$detalle->mensaje			= 		$info['Kaumen'];
				$detalle->descripcion		= 		$info['Kaudes'];
				$detalle->seguridad			= 		$info['Usuario'];
				$detalle->servicio			= 		$info['servicio'];
				*/
				
				echo "<td height=35>Servicio</td>";
				echo "<td>Historia</td>";
				echo "<td>Tipo de cambio</td>";
//				echo "<td>Descripcion del cambio</td>";
				echo "<td>Usuario</td>";
				echo "<td>Ir al perfil</td>";
				
				echo "</tr>";
				
				$colCambiosKardex = consultarCambiosKardexPorTiempo($conex, $wbasedato, $wservicio, $wminutos);
				$cont1 = 1;
				$color = '';
				
				foreach ($colCambiosKardex as $cambioKardex) {
					
					$cont1 % 2 == 0 ? $color = 'fila1' : $color = 'fila2';
					  
					echo "<tr class=$color>";
					
					echo "<td>$cambioKardex->servicio</td>";
					echo "<td>$cambioKardex->historia - $cambioKardex->ingreso</td>";
					echo "<td>$cambioKardex->mensaje</td>";
//					echo "<td width='520'>$cambioKardex->descripcion</td>";
					echo "<td>$cambioKardex->seguridad</td>";
					echo "<td align='center'>";
					if($cambioKardex->confirmadoKardex == "on"){
						echo "<a href='javascript:void(0);' onclick=javascript:abrirPerfil($cambioKardex->historia,'$cambioKardex->fechaKardex');>Ver</a>";
					} else {
						echo "Kardex no confirmado";
					}
					echo "</td>";
					
					echo "</tr>";
					$cont1++;
				}
				echo "</table>";
				
				echo "<div align=center>";
				echo "<br>";
				echo "<input type='button' value='Regresar' onclick='javascript:inicio();'> | <input type=button value='Cerrar Ventana' onclick='javascript:cerrarVentana();'>";
				echo "</div>";				
				
			} else {
				mensajeEmergente("Debe especificar el servicio y los minutos hacia atrás desde la fecha y hora actual para realizar la consulta.");
				funcionJavascript("inicio();");
			}
			break;		
		default:		//Muestra la pantalla inicial			
			//Cuerpo de la pagina
			echo "<table align='center' border=0>";

			//Ingreso de fecha de consulta
			echo '<span class="subtituloPagina2">';
			echo 'Ingrese los parámetros de consulta';
			echo "</span>";
			echo "<br>";
			echo "<br>";
			
			//Por Servicio
			$colServiciosHospitalarios = consultarCentrosCostosHospitalarios();
			echo "<tr><td class='fila1'>Servicio</td>";
			echo "<td class='fila2' align='center'>";
			echo "<select id=wservicio name=wservicio>";
			echo "<option value='%'>Todos los servicios</option>";
			foreach ($colServiciosHospitalarios as $servicio){
				echo "<option value='$servicio->codigo'>$servicio->codigo - $servicio->nombre</option>"; 
			}
			echo "</select>";
			echo "</td>";
			echo "</tr>";

			//Fecha generacion kardex
			echo "<tr>";
			echo "<td class='fila1'>Minutos atrás</td>";
			echo "<td class='fila2' align='center'>";
			echo "<INPUT TYPE='text' NAME='wminutos' id='wminutos' value='15' SIZE=10>";
			echo "</td></tr>";
			
			echo "<div align='center'>";
			echo "<tr><td align=center colspan=2><input type=button value='Consultar' onclick='javascript:consultar();'>&nbsp;|&nbsp;<input type=button value='Cerrar Ventana' onclick='javascript:cerrarVentana();'></td></tr>";
			echo "</div>";
			echo "</table>";

			echo "<div align='center'>";
			
			echo "<br>";
			echo "Ingrese los minutos hacia atr&aacute;s desde la fecha y hora actual para verificar &uacute;ltimos cambios";
			echo "<br>";
			echo "<br>";
			
			echo "</div>";		

			break;
	}
}	
?>