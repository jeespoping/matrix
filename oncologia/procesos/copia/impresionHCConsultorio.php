<style>

.tdInferior{
	border-bottom-style : solid;
	border-bottom-width : 1px;
	border-bottom-color : black;
}

.tdIzquierdo{
	border-left-style : solid;
	border-left-width : 1px;
	border-left-color : black;
}

.tdDerecho{
	border-right-style : solid;
	border-right-width : 1px;
	border-right-color : black;
}

</style>
<?php
include_once("conex.php");
/*************************************************************************************************************
 * Modificaciones:
 * 
 * Mayo 06 de 2016			Se muestra el estadio del paciente
 * Abril 05 de 2016			Se agrega la hora de la consulta en la imprsion
 * Febrero 02 de 2016		Se agrega la hora de la consulta en la imprsion
 * Julio 30 de 2015			Se cambia el texto Pulso (Kg) por Pulsos por Min.
 * Abril 24 de 2014			Se agrega impresión de notas adicionales
 ************************************************************************************************************/

 
 /**
 * Devuelve una cadena con todos los datos del ingreso del paciente.
 * 
 * @param $his
 * @param $ing
 * @return unknown_type
 */
function buscarDatosIngresoPaciente( $his, $ing, &$info_ingreso ){
	
	global $conex;
	global $wbasedato;
	
	$sql_ing = "  SELECT *
					FROM {$wbasedato}_000003
				   WHERE inghis = '$his'
			         AND inging = '$ing'";	
	$res_ing = mysql_query( $sql_ing, $conex ) or die( mysql_errno()." - Error en el query $sql_ing - ".mysql_error() );
	
	if( $rows_ing = mysql_fetch_array( $res_ing ) ){
		$info_ingreso = $rows_ing;
	}

}
 
 
/**
 * Busca el ulitmo ingreso de un paciente
 * 
 * @param $his	Historia
 * @return unknown_type
 */
function ultimoIngreso( $his ){
	
	global $conex;
	global $wbasedato;	
	
	$ing = '';
	
	$sql = "SELECT
				MAX( hcling ) as ing
			FROM
				{$wbasedato}_000001
			WHERE
				hclhis = '$his'
			GROUP BY 
				hclhis
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );			
				
	if( $rows = mysql_fetch_array( $res ) ){
		$ing = $rows['ing'];
	}
	
	return $ing;
	
}

/**
 * Devuelve una cadena con todos los seguimientos anteriores a la historia e ingreso dados
 * 
 * @param $his
 * @param $ing
 * @return unknown_type
 */
function buscarSeguimientosAnteriores( $his, $ing ){
	
	global $conex;
	global $wbasedato;	
	
	$seg = '';
	
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000001
			WHERE
				hclhis = '$his'
				AND hcling < '$ing'
			ORDER BY 
				hcling
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );			
				
	for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
		
		if( $i = 0 ){
			// $seg .= $rows['Hclfec'].", ".$rows['Hclhor']."<br>".$rows['Hclseg'];
		}
		else{
			$seg .= $rows['Hclseg'];
		}
		break;
	}
	
//	return trim( $seg, "<br><br>" );
	return $seg;

}

include_once( "root/comun.php" );
include_once( "../../consultorios/procesos/funcionesGenerales.php" );

$conex = obtenerConexionBD("matrix");

//Seteando variables
if( !isset($infoMedico) ){
	$infoMedico = new classMedico( $doc );
}

$key = substr( $user, 2, strlen($user) );

$wbasedato = $infoMedico->bdHC;
$wbasecitas = $infoMedico->bdCitas;

if(!isset( $infoPac ) ){
	$infoPac = '';
	infoPaciente( $his, $infoPac );
	buscarDatosIngresoPaciente( $his, $ing, &$info_ingreso );
}

if( false ){
	
}
else{
	
	if( isset($uing) && $uing == 'on' ){
		$ing2 = ultimoIngreso( $his );
	}
	
	if( isset($ing2) && $ing2 != '' ){
		$ing = $ing2;
	}
	
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000001
			WHERE
				hclhis = '$his'
				AND hcling like '$ing'
			ORDER BY
				hclfec, hcling
			";
				
	$res = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		
		echo "<div id='dvHC'>";
		echo "<table id='tbHC' align='center' style='border-style:solid;border-collapse:collapse;border-color:black;font-size:8pt;width:100%;border-width:1px'>";
		
		echo "<tr>";
		echo "<td align='center' colspan='5' class='tdinferior'>";
		echo "<b>".strtoupper( $infoMedico->nombre )."</b><br>";
		echo "<b>MEDICINA INTERNA</b> - <b>".strtoupper( $infoMedico->especialidad->descripcion )."</b><br>";
		echo "CC".number_format( $infoMedico->nroIdentificacion, 0, '', '.')." - Registro Medico: ".$infoMedico->registro."<br>";
		echo $infoMedico->consultorio->direccion." - Tel: ".$infoMedico->consultorio->telefono." - Colombia.";
		echo "<br><br>";
		echo "</td>";
		echo "<tr>";
		
		// echo "<tr>";
		// echo "<td align='center' colspan='5' class='tdinferior'>";
		// echo "<b>HISTORIA CLINICA</b><br>";
		// echo "<b>".strtoupper( $infoMedico->nombre )."</b><br>";
		// echo "<b>MEDICINA INTERNA</b><br>";
		// echo "<b>".strtoupper( $infoMedico->especialidad->descripcion )."</b><br>";
		// echo "CC".number_format( $infoMedico->nroIdentificacion, 0, '', '.')."<br>";
		// echo "Registro Medico: ".$infoMedico->registro."<br>";
		// echo $infoMedico->consultorio->direccion."<br>";
		// echo "Tel: ".$infoMedico->consultorio->telefono."<br>";
		// echo "Colombia.";
		// // echo "<br><br><br>";
		// echo "</td>";
		// echo "<tr>";
		
		for( $i = 0; $rows = mysql_fetch_array($res); $i++ ){
			
//			$drg = ($rows['Hcldrg'] == 'on') ? 'Sí' : 'No';
			
			if( $i == 0 ){
				
				
				$sql = "SELECT
							*
						FROM
							{$wbasedato}_000001
						WHERE
							hclhis = '$his'
						ORDER BY
							hclfec, hcling
						";
							
				$res2 = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
				
				$rowsHC1 = mysql_fetch_array( $res2 ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
				
				$rows[ 'Hclmed' ] = $rowsHC1['Hclmed'];
				$rows[ 'Hcltox' ] = $rowsHC1['Hcltox'];
				$rows[ 'Hclale' ] = $rowsHC1['Hclale'];
				$rows[ 'Hclgin' ] = $rowsHC1['Hclgin'];
				$rows[ 'Hclant' ] = $rowsHC1['Hclant'];
				
				
				
				if( $infoPac['Pacsex'] == "01-M"  ){
					$sexo = "Masculino";
				}
				else{
					$sexo = "Femenino";
				}
				
				$segumientosAnteriores = buscarSeguimientosAnteriores( $his, $ing );
				
//				echo "<table align='center' style='border-style:solid;border-collapse:collapse;border-color:black;font-size:8pt;width:100%;border-width:1px'>";
				
				echo "<tr>"; 
				echo "<td bgcolor='#DDDDDD' colspan='5' class='tdInferior'>";
				echo "<center><b>INFORMACION DEL PACIENTE</b></center>";
				echo "</td>";
				echo "</tr>";
				
				echo "<tr>";				
				echo "<td>";
					echo "<table align=center style='width:100%'>";
						echo "<tr>";
						echo "<td><font size=1><b>Historia</b></font></td>";
						echo "<td><font size=1><b>Identificación</b></font></td>";
						echo "<td><font size=1><b>Sexo</b></font></td>";
						echo "<td align='center'><font size=1><b>Fecha de nacimiento</b></font></td>";
						echo "<td align='center'><font size=1><b>Edad</b></font></td>";
						echo "</tr>";
						
						echo "<tr>";
						echo "<td align='center' style='border-bottom-style:solid;border-width:1px;border-color:black;'><font size=1>{$infoPac['Pachis']}</font></td>";
						echo "<td align='left' style='border-bottom-style:solid;border-width:1px;border-color:black'><font size=1>".substr( $infoPac['Pactid'], 0, 2 )."-{$infoPac['Pacnid']}</font></td>";
						echo "<td style='border-bottom-style:solid;border-width:1px;border-color:black'><font size=1>$sexo</font></td>";
						echo "<td align='center' style='border-bottom-style:solid;border-width:1px;border-color:black'><font size=1>{$infoPac['Pacfna']}</font></td>";
						echo "<td align='center' style='border-bottom-style:solid;border-width:1px;border-color:black'><font size=1>".calculoEdad( $infoPac['Pacfna'] )."</font></td>";
						echo "</tr>";
						
						echo "<tr>";
						echo "<td colspan='2'><font size=1><b>Nombre</b></font></td>";
						echo "<td><font size=1><b>Dirección</b></font></td>";
						echo "<td><font size=1><b>EPS</b></font></td>";
						echo "<td align='center'><font size=1><b>Teléfono</b></font></td>";
						echo "</tr>";
						
						echo "<tr>";
						echo "<td colspan='2' style='border-bottom-style:solid;border-width:1px;border-color:black'><font size=1>{$infoPac['Pacnpa']}</font></td>";
						echo "<td  style='border-bottom-style:solid;border-width:1px;border-color:black'><font size=1>{$infoPac['Pacdir']}</font></td>";
						echo "<td  style='border-bottom-style:solid;border-width:1px;border-color:black'><font size=1>{$info_ingreso['Ingeps']}</font></td>";
						echo "<td align='center' style='border-bottom-style:solid;border-width:1px;border-color:black'><font size=1>{$infoPac['Pactel']}</font></td>";
						echo "</tr>";
						
						echo "<tr>";
						echo "<td colspan='2'><font size=1><b>Profesión</b></font></td>";
						echo "<td><font size=1><b>Ocupación</b></font></td>";
						echo "<td><font size=1><b>Acompañante</b></font></td>";
						echo "<td><font size=1><b>Empresa Responsable</b></font></td>";
						echo "</tr>";
						
						echo "<tr>";
						echo "<td colspan='2' style='border-bottom-style:solid;border-width:1px;border-color:black'><font size=1>{$infoPac['Pacpro']}</font></td>";
						echo "<td  style='border-bottom-style:solid;border-width:1px;border-color:black'><font size=1>{$infoPac['Pacdocu']}</font></td>";
						echo "<td  style='border-bottom-style:solid;border-width:1px;border-color:black'><font size=1>{$info_ingreso['Ingaco']}</font></td>";
						echo "<td align='center' style='border-bottom-style:solid;border-width:1px;border-color:black'><font size=1>{$info_ingreso['Ingemp']}</font></td>";
						echo "</tr>";
					echo "</table>";
				echo "</td>";
				echo "</tr>";
				
				
				
				
				
				
				echo "<tr>";
				echo "<td class='tdinferior' colspan='5' bgcolor='#DDDDDD' >";
				echo "<b><center>ANTECEDENTES PERSONALES Y FAMILIARES</center></b>";
				echo "</td>";
				echo "</tr>";
				
				echo "<tr>";
				echo "<td class='tdinferior' colspan='5'>";				
				echo "<b>Médicos</b><br>";
				echo str_replace( "\n","<br>", htmlentities( $rows['Hclmed'] ) );
				echo "<br><br>";
				echo "</td>";
				echo "</tr>";
				
				
				
				echo "<tr>";
				echo "<td class='tdinferior' colspan='5'>";
				echo "<b>Tóxicos</b><br>";
				echo str_replace( "\n","<br>", htmlentities( $rows['Hcltox'] ) );;
				echo "<br><br>";
				echo "</td>";
				echo "</tr>";
				
				
				
				echo "<tr>";
				echo "<td colspan='5' class='tdinferior'>";
				echo "<b>Alérgicos</b><br>";
				echo str_replace( "\n","<br>", htmlentities( $rows['Hclale'] ) );;
				echo "<br><br>";
				echo "</td>";
				echo "</tr>";
				
				
				
				if( $sexo != 'Masculino' ){
					
					echo "<tr>";
					echo "<td class='tdinferior' colspan='5'>";
					echo "<b>Ginécologicos</b><br>";
					echo "{$rows[ 'Hclgin' ]}";
					echo "<br><br>";
					echo "</td>";
					echo "</tr>";
					
					echo "<tr>";
					echo "<td colspan='5' align='center' class='tdinferior'>";
					
					echo "<table align='center' style='border-style:solid;border-collapse:collapse;border-color:black;font-size:8pt;width:100%;border-width:0px'>";
					
					echo "<tr>";
					echo "<td class='tdDerecho'>";
					echo "<b>Menarca</b>";
					echo "</td>";
					echo "<td class='tdDerecho'>";
					echo "<b>F.U.M.</b>";
					echo "</td>";
					echo "<td class='tdDerecho'>";
					echo "<b>Gravida</b>";
					echo "</td>";
					echo "<td class='tdDerecho'>";
					echo "<b>Abortos</b>";
					echo "</td>";
					echo "<td>";
					echo "<b>Partos</b>";
					echo "</td>";
//					echo "<td>";
//					echo "<b>Consume medicamentos?</b>";
//					echo "</td>";
					echo "</tr>";
					
					echo "<tr>";
					echo "<td width='20%' class='tdDerecho' align='center'>";
					echo ( $rows[ 'Hclmen' ] != 'NO APLICA' && $rows[ 'Hclmen' ] != '0000-00-00' ) ? $rows[ 'Hclmen' ]: "";
					echo "</td>";
					echo "<td width='20%' class='tdDerecho' align='center'>";
					echo ( $rows[ 'Hclfum' ] != 'NO APLICA' && $rows[ 'Hclfum' ] != '0000-00-00' ) ? $rows[ 'Hclfum' ] : "";
					echo "</td>";
					echo "<td width='20%' class='tdDerecho'>";
					echo "{$rows[ 'Hclg' ]}";
					echo "</td>";
					echo "<td width='20%' class='tdDerecho'>";
					echo "{$rows[ 'Hcla' ]}";
					echo "</td>";
					echo "<td width='20%'>";
					echo "{$rows[ 'Hclp' ]}";
					echo "</td>";
					echo "</tr>";
					
					
					echo "</table>";
					
					echo "</td>";
					echo "</tr>";
					
				}
				
				
				echo "<tr>";
				echo "<td class='tdinferior' colspan='5'>";
				echo "<b>Familiares:</b>";
				echo "<br>";
				echo "{$rows['Hclant']}";
				echo "<br><br>";
				echo "</td>";
				echo "</tr>";
				
	
				
				// if( $segumientosAnteriores !== '' ){

					// echo "<tr>";
					// echo "<td class='tdinferior' colspan='5' bgcolor='#DDDDDD' >";
					// echo "<b><center>RESUMEN</center></b>";
					// echo "</td>";
					// echo "</tr>";

					// echo "<tr>";
					// echo "<td class='tdinferior' colspan='5'>";
					// echo $segumientosAnteriores;
					// echo "<br><br>";
					// echo "</td>";
					// echo "</tr>";

				// }
				
		
				
				// echo "<tr>";
				// echo "<td colspan='5' bgcolor='#DDDDDD' class='tdinferior'>";
				// echo "<b><center>ATENCION CRONOLOGICA</center></b>";
				// echo "</td>";
				// echo "</tr>";
				

				
			}

			if( $i > -1 ){
				
				
				echo "<tr>";
				echo "<td colspan='5' bgcolor='#A9D0F5' class='tdinferior'>";
				echo "<b>Fecha de consulta: </b>".$rows['Hclfec']." a las ".$rows['Hclhor'];
				echo "</td>";
				echo "</tr>";
	
				
				
				echo "<tr>";
				echo "<td colspan='5' class='tdinferior'>";
				echo "<b>Motivo de la consulta y enfermedad actual</b><br>";
				echo "{$rows[ 'Hclmce' ]}";
				echo "<br><br>";
				echo "</td>";
				echo "</tr>";
				
				echo "<tr>";
				echo "<td colspan='5' class='tdinferior'>";
				echo "<b>Revisión de Sistemas</b><br>";
				echo "".str_replace( "\n", "<br>", $rows[ 'Hclrds' ] )."";
				echo "<br><br>";
				echo "</td>";
				echo "</tr>";
				
				
				echo "<tr>";
				echo "<td class='tdinferior' colspan='5' bgcolor='#DDDDDD'>";
				echo "<b><center>EXAMEN FÍSICO</center></b>";
				echo "</td>";
				echo "</tr>";

				echo "<tr>";
				echo "<td colspan='5' align='center' class='tdinferior'>";
				
				echo "<table width='100%' style='border-style:solid;border-collapse:collapse;border-color:black;font-size:8pt;width:100%;border-width:0px'>";
				
				echo "<tr align='center'>";
				echo "<td class='tdderecho' width='15%'>";
				echo "<b>Presión Arterial</b>";
				echo "</td>";
				echo "<td class='tdDerecho' width='15%'>";
				echo "<b>Pulsos por Min.</b>";
				echo "</td>";
				echo "<td class='tdDerecho' width='15%'>";
				echo "<b>Peso (Kg)</b>";
				echo "</td>";
				echo "<td class='tdDerecho' width='15%'>";
				echo "<b>Talla (cm)</b>";
				echo "</td>";
				echo "<td class='tdDerecho'>";
				echo "<b>Otras</b>";
				echo "</td>";
				echo "<td class='tdDerecho' width='15%'>";
				echo "<b>Superficie Corporal (m<SUP>2</SUP>)</b>";
				echo "</td>";
				echo "<td width='15%'>";
				echo "<b>Indice Corporal (Kg/m<SUP>2</SUP>)</b>";
				echo "</td>";
				echo "</tr>";
				
				
				echo "<tr align='center'>";
				echo "<td class='tdDerecho'>";
				echo ( $rows['Hclpar'] != '.' ) ? $rows['Hclpar'] : "";
				echo "</td>";
				echo "<td class='tdDerecho'>";
				echo $rows['Hclpes'];
				echo "</td>";
				echo "<td class='tdDerecho'>";
				echo $rows['Hclpul'];
				echo "</td>";
				echo "<td class='tdDerecho'>";
				echo $rows['Hcltal'];
				echo "</td>";
				echo "<td class='tdDerecho'>";
				echo ( $rows['Hclotr'] == '.' || empty($rows['Hclotr']) ) ? "NO APLICA": str_replace( "\n","<br>", htmlentities( $rows['Hclotr'] ) );
				echo "</td>";
				echo "<td class='tdDerecho'>";
				echo number_format( $rows['Hclspc'], '3' );
				echo "</td>";
				echo "<td>";
				echo number_format( $rows['Hclico'], '3' );
				echo "</td>";
				echo "</tr>";
				
				
				echo "</table>";
				
				
				echo "</td>";
				echo "</tr>";
				
				
				echo "<tr>";
				echo "<td colspan='5' class='tdinferior'>";
				echo str_replace( "\n","<br>", htmlentities( $rows['Hcloef'] ) );
				echo "<br><br>";
				echo "</td>";
				echo "</tr>";
				
				
				echo "<tr>";
				echo "<td class='tdinferior' colspan='5'>";
				echo "<b>Impresión dignóstica: </b>";
				echo $rows['Hcltdx'];
				echo "</td>";
				echo "</tr>";
				
				
				echo "<tr>";
				echo "<td class='tdinferior' colspan='5'>";
				echo "<b>Diagnóstico: </b>";
				echo $rows['Hcldxp']."-".str_replace( "\n","<br>", htmlentities( consultarDescripcionCie10($rows['Hcldxp']) ) );
				echo "</td>";
				echo "</tr>";
				
				echo "<tr>";
				echo "<td class='tdinferior' colspan='5'>";
				echo "<table width='100%' style='border-style:solid;border-collapse:collapse;border-color:black;font-size:8pt;width:100%;border-width:0px'>";
				echo "<tr><td class='tdDerecho'>";
				echo "<b>Estadio</b>";
				echo "</td>";
				echo "<td class='' align='right' style='width:50;'><b>T:</b></td>";
				echo "<td class='tdDerecho'>";
				echo $rows['Hclt'];
				echo "</td>";
				echo "<td class='' align='right' style='width:50;'><b>N:</b></td>";
				echo "<td class='tdDerecho'>";
				echo $rows['Hcln'];
				echo "</td>";
				echo "<td class='' align='right' style='width:50;'><b>M:</b></td>";
				echo "<td class='tdDerecho'>";
				echo $rows['Hclm'];
				echo "</td>";
				echo "<td class='' align='right' style='width:100;'><b>Estado:</b></td>";
				echo "<td>";
				echo $rows['Hclest'];
				echo "</td></tr>";
				echo "</table>";
				echo "</td>";
				echo "</tr>";
			}
			
			echo "<tr>";
			echo "<td colspan='5' class='tdinferior' bgcolor='#DDDDDD'>";
			echo "<b>Conducta</b>";
			echo "</td>";
			echo "</tr>";
			
			echo "<tr>";
			echo "<td colspan='5' class='tdinferior'>";
			echo str_replace( "\n","<br>", htmlentities(  $rows['Hclcon'] ) );
			echo "<br><br>";;
			echo "</td>";
			echo "</tr>";
			
			// echo "<tr>";
			// echo "<td colspan='5' class='tdinferior' bgcolor='#DDDDDD'>";
			// echo "<b>Seguimiento actual</b>";
			// echo "</td>";
			// echo "</tr>";
			
			// echo "<tr>";
			// echo "<td colspan='5' class='tdinferior'>";
			// echo str_replace( "\n","<br>", htmlentities(  $rows['Hclseg'] ) );
			// echo "<br><br>";;
			// echo "</td>";
			// echo "</tr>";
			
			
			//Notas Adicionales
			//impresion de notas adicionales
			$sql = "SELECT * 
					  FROM {$wbasedato}_000033
					 WHERE Nadhis = '$his'
					   AND Nading = '$ing'
					   AND Nadest = 'on'
					";
			
			$resNad = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$numNad = mysql_num_rows( $resNad );
			
			if( $numNad > 0 ){
			
				echo "<tr>";
				echo "<td class='tdinferior' bgcolor=#DDDDDD align='center'>";
				echo "<b>NOTAS ADICIONALES</b>";
				echo "</td>";
				
				echo "<td>";
			
				for( $iNad = 0; $rowsNad = mysql_fetch_array( $resNad ); $iNad++ ){
				
					echo "<tr><td class='tdinferior'>";
					
					echo "<table style='font-size:8pt;width:100%;border-width:1px'>";
					
					echo "<tr>";
					echo "<td>"; 
					// echo calculoEdad( $infoPac['Pacfna'] );
					// echo $infoPac['Pachis'];
					echo $infoPac['Pacnpa'];
					echo "<br>".substr( $infoPac['Pactid'], 0, 2 )."-{$infoPac['Pacnid']}<br><br>";
					// echo $sexo;
					// echo "<br>Fecha de nacimiento: ".$infoPac['Pacfna'];
					
					echo "</td>";
					echo "</tr>";
					
					echo "<tr>";
					echo "<td><b>Fecha:</b> ".$rowsNad[ 'Nadfec' ]." ".$rowsNad[ 'Nadhor' ]."</td>";
					echo "</tr>";
					
					echo "<tr>";
					echo "<td>";
					
					echo "<br>".str_replace( "\n", "<br>", htmlentities( trim($rowsNad[ 'Nadobs' ]) ) );
					echo "</td>";
					echo "</tr>";
					
					echo "<tr>";
					echo "<td>";
					
					echo "<br><br>";
			
					// echo "Atentamente, ";
					// echo "<br><br><br><br>";
					echo $infoMedico->nombre;
					echo "<br>C.C. ";
					echo number_format( $infoMedico->nroIdentificacion, 0, '', '.' );
					echo "<br>R.M.: ";
					echo $infoMedico->registro;
					echo "<br>";
					
					echo "</td>";
					echo "</tr>";
					
					echo "</table>";
					
					echo "</td></tr>";
				}
				
				echo "</td></tr>";
			}
		}
		
		echo "</table>";
		
		
		
		
		echo "</div>";
	}
}

?>