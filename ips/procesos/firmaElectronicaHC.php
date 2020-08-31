<script>
	function habilitarBotonCambiar(){

		if( document.forms[0].elements['old'].value.length > 0 
			&& document.forms[0].elements['new'].value.length > 0
			&& document.forms[0].elements['confirm'].value.length > 0){

			document.forms[0].elements['cambiar'].disabled = false;
		}
		else{
			document.forms[0].elements['cambiar'].disabled = true;
		}

//	onkeypress='if ((event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 97 && event.keyCode <= 122) || (event.keyCode >= 65 && event.keyCode <= 90) )  event.returnValue = false'
//		onkeypress='if (event.keyCode >= 35 & event.keyCode <= 38)  event.returnValue = false'
	}

	function soloNumeros(evt){
		// NOTE: Backspace = 8, Enter = 13, '0' = 48, '9' = 57
		var key = evt.keyCode ? evt.keyCode : evt.which ;

		return ( (key >= 65 && key <= 90) || (key >= 48 && key <= 57) || (key >= 97 && key <= 122) || key == 8 );
	}


	window.onload = function(){ document.forms[0].elements['old'].focus(); }
</script>
<?php
include_once("conex.php");
/**
 * Cambia el password de la firma electronica para el medico
 * 
 * @param $new				La nueva contrase
 * @param $old				Contraseña actual
 * @param $key				Codigo de nomina del medico que desea cambiar la contraseña
 * @param $id				Id de la fila que se va a cambiar de la tabla 000051
 * @return unknown_type
 */
function cambiarFirmaElectronica( $new, $old, $key, $id ){
	
	global $conex;
	global $wbasedato;
	
	$sql = "UPDATE
				{$wbasedato}_000051
			SET
				medpwd = '$new'
			WHERE
				medpwd = '$old'
				AND medusu = '$key'
				AND id = '$id'
			";
		
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows() > 0 ){
		return true;
	}
	else{
		return false;		
	}
			
}

/********************************************************************************************************
 * INICIO DEL PROGRAMA
 *******************************************************************************************************/

include_once("root/comun.php");

if( !isset($wemp_pmla) ){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}
$key = substr($user, 2, strlen($user));

$espacios = array( chr(32), chr(9), chr(10), chr(13), chr(0));

$conex = obtenerConexionBD("matrix");

$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

$wbasedato = strtolower($institucion->baseDeDatos);
$wentidad = $institucion->nombre;

//El usuario se encuentra registrado
if( !isset($_SESSION['user']) ){
	echo "Error: Usuario No registrado";
}
else{
	
	encabezado("CAMBIO DE FIRMA ELECTRONICA", "2010-01-19", "logo_".$wbasedato );
	
	$mensaje = '';
	$error = false;
	
	echo "<br><br>";
	echo "<form method='post'>";
	
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000051
			WHERE
				medusu = '$key'
				AND medest = 'on'
			";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		
		//Validaciones antes de cambiar la contraseña		
		if( isset($old) && !empty($old) && isset($new) && !empty($new) && isset($confirm) && !empty($confirm) ){

			if( $old == $rows['Medpwd'] ){
				
				if( $old != $new ){
					
					if( strlen( str_replace( $espacios,'',$new ) ) == strlen($new) ){
					
						if( strlen($new) > 3 ){

							if( strlen($new) < 11 ){

								if( $new == $confirm ){
									cambiarFirmaElectronica( $new, $old, $key, $rows['id'] );
									$mensaje = "LA CONTRASEÑA HA SIDO CAMBIADA EXITOSAMENTE";
								}
								else{
									$mensaje = "LA NUEVA CONTRASEÑA NO ES IGUAL A LA CONFIRMADA";
									$error = true;
								}
							}
							else{
								$mensaje = "LA NUEVA CONTRASEÑA DEBE SER MENOR O IGUAL A 10 CARACTERES";
								$error = true;
							}
						}
						else{
							$mensaje = "LA NUEVA CONTRASEÑA DEBE SER MAYOR O IGUAL A 4 CARACTERES";
							$error = true;
						}
					}
					else{
						$mensaje = "LA NUEVA CONTRASEÑA NO DEBE CONTENER ESPACIOS";
						$error = true;
						
					}
				}
				else{
					$mensaje = "LA NUEVA CONTRASEÑA DEBE SER DIFERENTE A LA ACTUAL";
					$error = true;
				}
			}
			else{
				$mensaje = "LA CONTRASEÑA ACTUAL NO ES CORRECTA";
				$error = true;
			}
		}
		
		if( $error ){
			echo "<center><font color='red'><b>$mensaje</b></font></center><br>";
		}
		
		echo "<center><b>La contraseña debe tener entre 4 y 10 caractares (letras y/o números) sin incluir espacios ni simbolos.</b></center><br>";
		
		echo "<table align='center'>";
		echo "<tr>";
		echo "<td class='encabezadotabla' style='width:200'>Contraseña Actual</td>";
		echo "<td class='fila1'><INPUT type='password' name='old' maxlength='10' onChange='javascript: habilitarBotonCambiar();' onkeypress='return soloNumeros(event);'></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td class='encabezadotabla'>Nueva Contraseña</td>";
		echo "<td class='fila1'><INPUT type='password' name='new' maxlength='10' onChange='javascript: habilitarBotonCambiar();' onkeypress='return soloNumeros(event);'></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td class='encabezadotabla'>Confirmar Contraseña</td>";
		echo "<td class='fila1'><INPUT type='password' name='confirm' maxlength='10' onChange='javascript: habilitarBotonCambiar();' onkeypress='return soloNumeros(event);'></td>";
		echo "</tr>";
		echo "</table>";
		
		echo "<br><br>";
		echo "<table align='center'>";
		echo "<tr><td>";
		echo "<INPUT type='submit' value='Cambiar' style='width:100' disabled name='cambiar'>"; 
		echo "</td></tr>";
		echo "<tr><td>";
		echo "<br><INPUT type='button' value='Cerrar' style='width:100' onclick='javascript: cerrarVentana();'>"; 
		echo "</td></tr>";
		echo "</table>";
		
		echo "<br>";
		echo "<center><b>Si no puede cambiar su contraseña comuníquese con sistemas.</b></center><br>";
		
		echo "<br><br>";
		
		if( !$error && !empty($mensaje) ){
			echo "<center><font color='blue'><b>$mensaje</b></font></center>";
		}
	}
	else{
		echo "<center><b>EL MEDICO NO SE ENCUENTRA REGISTRADO EN LA BASE DE DATOS</b></center>";
	}
	
	echo "</form>";
	
}
?>
