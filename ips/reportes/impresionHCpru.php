<?php
include_once("conex.php");

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

function fechaNacimiento( $his ){

	global $conex;
	global $wbasedato;
	
	$fna = '';
	
	$sql = "SELECT
				pacfna
			FROM
				{$wbasedato}_000100
			WHERE
				pachis = '$his'";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array($res) ){
		$fna = $rows[0];
	}
	
	return $fna;
}

function registroMedico( $cod ){
	
	global $conex;
	global $wbasedato;
	
	$reg = "";
	
	$sql = "SELECT
				Medreg
			FROM
				{$wbasedato}_000051
			WHERE
				medcod = '$cod'";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array($res) ){
		$reg = $rows['Medreg']; 
	}
	
	return $reg;
}

/**
 * Devuleve el diagnostico Cie 10
 * 
 * @param $cod
 * @return unknown_type
 */
function diagnosticoCie10( $cod ){
	
	global $conex;
	
	$cie10 = "NO APLICA";
	
	$sql = "SELECT
				codigo, descripcion
			FROM
				root_000011
			WHERE
				codigo = '$cod'
			";
	
	$res = mysql_query( $sql, $conex ) or die( "Error en el query - $sql - " );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$cie10 = $rows['codigo']."-".$rows['descripcion']; 		
	}
	
	return $cie10;
}

include_once("root/comun.php");

if(!isset($wemp_pmla)){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}
$key = substr($user, 2, strlen($user));

$conex = obtenerConexionBD("matrix");

$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

$wbasedato = $institucion->baseDeDatos;
$wentidad = $institucion->nombre;

//El usuario se encuentra registrado
if( !isset($_SESSION['user']) ){
	echo "Error: Usuario No registrado";
}
else{
	
	if( !isset($his) || empty($his) ){
		//OPCIONES DE IMPRESION DE LA HISTORIA CLINICA
		
		encabezado("REPORTE DE HISTORIA CLINICA", "2009-11-30", "logo_".$wbasedato );
	
		echo "<form method=post>";
		
		if( !isset($enf) ){
			echo "<INPUT type='hidden' name='enf' value='off'>";
			$enf = 'off';
		}
		else{
			echo "<INPUT type='hidden' name='enf' value='$enf'>";
		}
		
		echo "<input type='hidden' name='wemp_pmla' value='$wemp_pmla'>";
		
//		echo "<center><b>El '%' es el comodin.<br>Ejemplo Nombre: %consuelo%</b></center><br><br>";
		
		echo "<br><table align='center'>
				<tr class='encabezadotabla'>
					<td colspan='2'>Parametros de Busqueda</td>					
				</tr>
				<tr class='fila1'>
					<td>HISTORIA</td>
					<td><INPUT TYPE='text' name='bsHis'></td>
				</tr>
				<tr class='fila1'>
					<td>DOUCMENTO</td>
					<td><INPUT TYPE='text' name='bsDoc'></INPUT></td>
				</tr>
				<tr class='fila1'>
					<td>NOMBRE</td>
					<td><INPUT TYPE='text' name='bsNom'></INPUT></td>
				</tr>
				<tr>
					<td colspan=2 class='fila2'><b>Nota: </b>El '%' es el comodin.<br>Ejemplo Nombre: %consuelo%</td>
				</tr>
				<tr>
					<td colspan='2' align='center'><br><INPUT TYPE='SUBMIT' value='Consultar' style='width:100'></td>
				</tr>
			</table>";
		
		
		if( ( isset($bsNom) && !empty($bsNom) ) 
			|| ( isset($bsHis) && !empty($bsHis) ) 
			|| ( isset($bsDoc) && !empty($bsDoc) ) ){
			
			$sql = "SELECT
					hclfec, hclhor, hclmce, hclapf, hclafa, hclefi, hclcon, hclcie, hclacl, hclmed, hclnom, hclhis, hcldoc
				FROM
					{$wbasedato}_000139
				WHERE
					hclhis like '$bsHis%'
					AND hcldoc like '$bsDoc%'
					AND hclnom like '$bsNom%'
				GROUP BY hclhis
				ORDER BY hclfec desc
			   ";

			$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query - ".mysql_error() );
			$num = mysql_num_rows( $res );
			
			if( $num > 0 ){
				
				echo "<br><br>";
				echo "<table align='center'>";
				echo "<tr class='encabezadotabla' align='center'>";
				echo "<td width='100'>Historia</td>";
				echo "<td width='100'>Documento</td>";
				echo "<td width='300'>Nombre</td>";
				echo "<td width='150'>Enlace</td>";
				echo "</tr>";
				
				for( $i = 0; $rows = mysql_fetch_array($res); $i++ ){
					
					$class = "class='fila".(($i%2)+1)."'";
					
					echo "<tr $class>";
					echo "<td align='right'>{$rows['hclhis']}</td>";	
					echo "<td align='right'>{$rows['hcldoc']}</td>";
					echo "<td>{$rows['hclnom']}</td>";
					echo "<td><a href='impresionHCpru.php?wemp_pmla=$wemp_pmla&his={$rows['hclhis']}&enf=$enf' target='blank'>Imprimir</a></td>";
					echo "</tr>";
				}
				
				echo "</table>";
			}
			
		}
		
		echo "</form>";
			
	}
	else{
		
		//IMPRESION DE  LA HISTORIA CLINICA
		
		$sql = "SELECT
					hclfec, 
					hclhor, 
					hclmce, 
					hclapf, 
					hclafa, 
					hclefi, 
					hclcon, 
					hclcie, 
					hclacl, 
					hclmed, 
					hclnom, 
					hclhis, 
					hcldoc,
					hclci1,
					hclci2,
					hclci3,
					hclevo,
					hclico,
					hclfum,
					hcldir,
					hcltel,
					hclsex,
					hclgin,
					hclrsa
				FROM
					{$wbasedato}_000139
				WHERE
					hclhis = '$his'
				ORDER BY hclfec, hclhor desc
			   ";
					
		$res = mysql_query( $sql, $conex ) or die( mysql_errno(). "- Error en el query $sql - ".mysql_error() );
		$numrows = mysql_num_rows( $res );
		
		//TITULO DE LA HISTORIA CLINICA
		echo 
			"<table  border=1 frame='border' cellspacing=0 width='750' cellspading=0>
				<tr>
					<td align='center' width='150'><img src='../../images/medical/root/logo_clisur.jpg' width=120 heigth=76></img></td>
					<td align='center'><b>HISTORIA CLINICA</b></td>
				</tr>
			</table>
			<br>
			<br>";
		
		if( $numrows > 0 ){
			$val1 = true;
			for( $i = 0; ($rows = mysql_fetch_array( $res )) && $val1 == true; $i++ ){
				
				$cie10 = "";
				$cie1 = "";
				$cie2 = "";
				$cie3 = "";
				$regMed = "";
				
				$cie10 = diagnosticoCie10( $rows['hclcie'] );
				$cie1 = diagnosticoCie10( $rows['hclcie'] );
				$cie2 = diagnosticoCie10( $rows['hclcie'] );
				$cie3 = diagnosticoCie10( $rows['hclcie'] );
				
				$codreg = explode( "-", $rows['hclmed'] );	//codigo del medico
				
				if( count( $codreg) > 0 ){
					$regMed = registroMedico( $codreg[0] );
				}
				
				$rows['hclmce'] = str_replace("\n","<br>",$rows['hclmce']);
				$rows['hclapf'] = str_replace("\n","<br>",$rows['hclapf']);
				$rows['hclafa'] = str_replace("\n","<br>",$rows['hclafa']);
				$rows['hclgin'] = str_replace("\n","<br>",$rows['hclgin']);
				$rows['hclcon'] = str_replace("\n","<br>",$rows['hclcon']);
				$rows['hclefi'] = str_replace("\n","<br>",$rows['hclefi']);
				$rows['hclico'] = str_replace("\n","<br>",$rows['hclico']);
				$rows['hclacl'] = str_replace("\n","<br>",$rows['hclacl']);
				$rows['hclevo'] = str_replace("\n","<br>",$rows['hclevo']);
				$rows['hclrsa'] = str_replace("\n","<br>",$rows['hclrsa']);
				
				if( $rows['hclacl'] == '.' ){
					$rows['hclacl'] = 'NO APLICA';
				}
				
				if( $rows['hclico'] == '.' ){
					$rows['hclico'] = 'NO APLICA';
				}
				
				if( $i == 0 ){
					
					//ENCABEZADO DE LA HISTORIA CLINICA
					//INFORMACION DEMOGRAFICA
					echo 
						"<table border=1 frame='border' cellspacing=0 width='750' cellspading=0>
							<tr>
								<td><b>HISTORIA:<b></td>
								<td>{$rows['hclhis']}</td>
							</tr>
							<tr>
								<td><b>NOMBRES Y APELIDOS:</b></td>
								<td>{$rows['hclnom']}</td>
							</tr>
							<tr>
								<td><b>SEXO:</b></td>
								<td>{$rows['hclsex']}</td>
							</tr>
							<tr>
								<td><b>DOCUMENTO:</b></td>
								<td>{$rows['hcldoc']}</td>
							</tr>
							<tr>
								<td><b>FECHA DE NACIMIENTO:</b></td>
								<td>".fechaNacimiento($rows['hclhis'])."</td>
							</tr>
							<tr>
								<td><b>EDAD:</b></td>
								<td>".calculoEdad( fechaNacimiento($rows['hclhis']) )."</td>
							</tr>
							<tr>
								<td><b>DIRECCION:</b></td>
								<td>{$rows['hcldir']}</td>
							</tr>
							<tr>
								<td><b>TELEFONO:</b></td>
								<td>{$rows['hcltel']}</td>
							</tr>
						</table>
						";
					
				}
				
				//FEHCA Y HORA
				echo 
					"<br><br><table border=1 frame='border' cellspacing=0 width='750'>
						<tr>
							<td><b>FECHA:<b/></td>
							<td>{$rows['hclfec']}</td>
							<td><b>HORA:</b></td>
							<td>{$rows['hclhor']}</td>
						</tr>
					</table>";
				
				echo "<br><br>";
				
				
				//CUERPO DE LA HISTORIA CLINICA
				echo 
					"<table border=1 frame='border' cellspacing=0 width='750' style='table-layout:fixed;'>
						<tr>
							<td><b>MOTIVO DE LA CONSUTLA Y ENFERMEDAD ACTUAL</b></td>
						</tr>
						<tr>
							<td><div style='word-wrap: break-word; width:745'>{$rows['hclmce']}</div><br><br></td>
						</tr>
						<tr>
							<td><b>ANTECEDENTES PERSONALES</b></td>
						</tr>
						<tr>
							<td><div style='word-wrap: break-word; width:745'>{$rows['hclapf']}</div><br><br></td>
						</tr>
						<tr>
							<td><b>ANTECEDENTES FAMILIARES</b></td>
						</tr>
						<tr>
							<td><div style='word-wrap: break-word; width:745'>{$rows['hclafa']}</div><br><br></td>
						</tr>";
						
				if( $rows['hclsex'] != "M" ){
					echo "
						<tr>
							<td><b>ANTECEDENTES GINECOBSTETRICOS</b></td>
						</tr>
						<tr>
							<td><div style='word-wrap: break-word; width:745'>{$rows['hclgin']}</div><br><br></td>
						</tr>
						<tr>
							<td><b>FECHA ULTIMA MENSTRUACION:</b> {$rows['hclfum']}</td>
						</tr>";
				}
						
				echo "
						<tr>
							<td><b>EXAMEN FISICO</b></td>
						</tr>
						<tr>
							<td><div style='word-wrap: break-word; width:745'>{$rows['hclefi']}</div><br><br></td>
						</tr>
						
						<tr>
							<td><b>DIAGNOSTICOS PRINCIPAL</b></td>
						</tr>
						</tr>
							<td>$cie10<br><br></td>
						</tr>
						<tr>
							<td><b>DIAGNOSTICOS SECUNDARIO 1</b></td>
						</tr>
						</tr>
							<td>$cie1<br><br></td>
						</tr>
						<tr>
							<td><b>DIAGNOSTICOS SECUNDARIO 2</b></td>
						</tr>
						</tr>
							<td>$cie2<br><br></td>
						</tr>
						<tr>
							<td><b>DIAGNOSTICOS SECUNDARIO 3</b></td>
						</tr>
						</tr>
							<td>$cie3<br><br></td>
						</tr>
						
						<tr>
							<td><b>CONDUCTA</b></td>
						</tr>
						<tr>
							<td><div style='word-wrap: break-word; width:745'>{$rows['hclcon']}</div><br><br></td>
						</tr>
						
						<tr>
							<td><b>EVOLUCION</b></td>
						</tr>
						<tr>
							<td><div style='word-wrap: break-word; width:745'>{$rows['hclevo']}</div><br><br></td>
						</tr>
						<tr>
							<td><b>INTERCONSULTA</b></td>
						</tr>
						<tr>
							<td><p><div style='word-wrap: break-word; width:745'>{$rows['hclico']}</div><br><br></p></td>
						</tr>
						
						<tr>
							<td><b>ACLARACIONES</b></td>
						</tr>
						<tr>
							<td><div style='word-wrap: break-word; width:745'>{$rows['hclacl']}</div><br><br></td>
						</tr>
						
						<tr>
							<td><b>RECOMENDACIONES Y SIGNOS DE ALARMA</b></td>
						</tr>
						<tr>
							<td><div style='word-wrap: break-word; width:745'>{$rows['hclrsa']}</div><br><br></td>
						</tr>
						
						<tr>
							<td><b>MEDICO TRATANTE</b></td>
						</tr>
						<tr>
							<td>{$rows['hclmed']}<br><br></td>
						</tr>
						
						<tr>
							<td><b>REGISTRO MEDICO:</b> $regMed</td>
						</tr>
						
					</table>";
				
				if( isset( $enf ) && $enf == 'on' ){
					$val1 = false;					
				}
			}
			
			echo "<table width=750><tr><td align='right'><br><b>FIRMADA ELECTRONICAMENTE</b></td></tr></table>";
		}
		else
		{
			echo "<p align='center'><b>LA HISTORIA CLINICA NO EXISTE EN EL SISTEMA</b></p>";
		}
	}
}
?>