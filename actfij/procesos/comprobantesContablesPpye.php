<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA
//=========================================================================================================================================\\
//DESCRIPCION:
//AUTOR:				Jerson Andres Trujillo
//FECHA DE CREACION:	2014-11-10
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz='';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
//
//
//--------------------------------------------------------------------------------------------------------------------------------------------


//--------------------------------------------------------------------------------------------------------------------------------------------
// 	EJECUCION DEL SCRIPT
//--------------------------------------------------------------------------------------------------------------------------------------------


if(!isset($_SESSION['user']))
{
    echo '  <div style="color: #676767;font-family: verdana;background-color: #E4E4E4;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    exit;
}

	$tipoCuentasGBL = array(); //array("CD" => "Cuenta Débito", "CC" => "Cuenta Crédito");
		

	header('Content-type: text/html;charset=ISO-8859-1');
	$user_session 	= explode('-',$_SESSION['user']);
	$wuse 			= $user_session[1];
	

	include_once("root/comun.php");
	

	include "../../gesapl/procesos/gestor_aplicaciones_config.php";
	include_once("../../gesapl/procesos/gesapl_funciones.php");

	$conex 		= obtenerConexionBD("matrix");
	$wbasedato 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'activosFijos');
	$wfecha		= date("Y-m-d");
    $whora 		= date("H:i:s");
	
	include_once("actfij/funciones_activosFijos.php");
	$periodo = traerPeriodoActual();
	$anoActual = $periodo['ano'];
	$mesActual = $periodo['mes'];


//=====================================================================================================================================================================
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================

	//-------------------------------------------------------------------
	//	-->	Pinta la lista de formulas existentes
	//-------------------------------------------------------------------
	function formularioNuevoComprobante()
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;
		global $tipoCuentasGBL;
		global $arr_tipos;
		global $arr_nombreComprobante;
		global $anoActual, $mesActual;
		
		/*Traer las cuentas*/
		$arr_cuentas = array();
		$sqlLista = "SELECT Tcccod as codigo, Tccnom as nombre
					   FROM ".$wbasedato."_000027
					  WHERE Tccest = 'on'
					    AND Tccano = '".$anoActual."'
				        AND Tccmes = '".$mesActual."'
		";
		$resLista = mysql_query($sqlLista, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlLista):</b><br>".mysql_error());
		while($rowLista = mysql_fetch_assoc($resLista))
		{
			$rowLista['nombre'] = htmlentities($rowLista['nombre']);
			array_push($arr_cuentas, $rowLista);
		}
		
		
		echo "
		<div id='accordionLista' align='center'>
			<h1 style='font-size: 13pt;' align='left'>&nbsp;&nbsp;&nbsp;&nbsp;Nuevo comprobante</h1>
			<div style='font-family: verdana;font-weight: normal;' align='left'>
				<br><br>";
		echo "<table id='tablanuevo'>
						<tr>
							<td class='fila1' width='15%'>Comprobante</td>
							<td class='fila2'  width='45%'>							
								<select onchange='cambiarNombreComprobante()' obl='obligatorio' style='width:170px;font-size: 10pt;' id='nombreComprobante' >
										";
										foreach( $arr_nombreComprobante as $cod=>$nom )
											echo "<option value='".$cod."' >".$nom."</option>";
							echo "	</select>
							</td>
						</tr>
						<tr>
							<td class='fila1' width='15%'>Tipo</td>
							<td class='fila2'  width='45%'>							
								<select obl='obligatorio' onchange='consultarSubtipos()' style='width:170px;font-size: 10pt;' id='tipo' >
									<option value=''>Seleccione...</option>";
									foreach( $arr_tipos as $cod=>$nom )
										echo "<option value='".$cod."' >".$nom."</option>";
						echo "	</select>
							</td>
						</tr>
						<tr>
							<td class='fila1' width='15%'>SubTipo</td>
							<td class='fila2'  width='45%'>							
								<select obl='obligatorio'  onchange='consultarCausaRegistro()' style='width:170px;font-size: 10pt;' id='subtipo' >
								</select>
							</td>
						</tr>
						<tr id='tr_causa'>
							<td class='fila1' width='15%'>Causa</td>
							<td class='fila2'  width='45%'>							
								<select obl='obligatorio' onchange='consultarConsecutivoRegistro()' style='width:170px;font-size: 10pt;' id='causa' >
								</select>
							</td>
						</tr>
						<tr>
							<td class='fila1' width='15%'>Registro</td>
							<td class='fila2'  width='45%'>							
								<select obl='obligatorio' style='width:170px;font-size: 10pt;' id='registro'>
								</select>
							</td>
						</tr>
						<tr>
							<td class='fila1' width='15%'>Debito/Credito</td>
							<td class='fila2'  width='45%'>							
								<select obl='obligatorio' style='width:170px;font-size: 10pt;' id='debitocredito' >
									<option value=''>Seleccione...</option>";
								foreach ($tipoCuentasGBL as $codt => $nomt )
									echo "<option value='".$codt."' >".$nomt."</option>";
						echo "	</select>
							</td>
						</tr>
						<tr>
							<td class='fila1' width='15%'>Cuenta</td>
							<td class='fila2'  width='45%'>							
								<select obl='obligatorio' style='width:170px;font-size: 10pt;' id='cuenta'>
									<option value=''>&nbsp;</option>
								";								
								foreach ($arr_cuentas as $indc => $cuenta )
									echo "<option value='".$cuenta['codigo']."' >".$cuenta['nombre']."</option>";
						echo "
								</select>
													
							</td>
						</tr>					
						<tr>
							<td colspan=2 align='center'>
								<input type='button' class='botongrabar' id='botongrabar' value='Grabar' onclick='grabarComprobante()'>
								<input type='button' class='botonactualizar' id='botonactualizar' style='display : none' value='Actualizar' onclick='actualizarComprobante()'>
								<input type='button' class='botonanular' id='botonanular'	 style='display : none' value='Anular' onclick='anularComprobante()'>
								<input type='button' class='botonCancelar' id='botonCancelar'	 style='display :' value='Cancelar' onclick='limpiardatos()'>							
							</td>							
						</tr>
								";
		echo "</table>	
			</div>
		</div>";
		
		echo "<input type='hidden' value='".json_encode($arr_cuentas)."' id='arr_cuentas_oculto' />";
	}
	//-------------------------------------------------------------------
	//	-->	Pinta el formulario para crear o editar una formula
	//-------------------------------------------------------------------
	function formularioComprobantes()
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;
		global $arr_tipos;
		global $arr_nombreComprobante;
		global $anoActual, $mesActual;
		
		$nomComprobante = "";

		echo "
		<div id='accordionFormula' align='left' style='font-family: verdana;font-weight: normal;font-size: 10pt;'>
			<h1 style='font-size: 13pt;'>&nbsp;&nbsp;&nbsp;&nbsp;Comprobantes</h1>
			<div>
				<table style='padding:5px;margin:5px; width:100%; height: 500px;'>
					<tr>
						<td colspan='2'>
							<fieldset align='center' style='padding:15px;'>
								<legend class='fieldset'>FILTRAR</legend>
								<span style='font-weight:bold;'>
									Comprobante:&nbsp;&nbsp;&nbsp;</b>
									<select obl='obligatorio' style='width:170px;font-size: 10pt;' onchange='cambiarNombreComprobantec()' id='nombreComprobantec' >
										";
										foreach( $arr_nombreComprobante as $cod=>$nom ){
											echo "<option value='".$cod."' >".$nom."</option>";
											if( $nomComprobante == "" ) $nomComprobante = $nom;
										}
							echo "	</select>
								</span>
								<br>
								<br>
								<span style='font-weight:bold;'>
									Tipo:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b>
									<select obl='obligatorio' onchange='consultarSubtiposFiltro()' style='width:170px;font-size: 10pt;' id='tipoc' >
										<option value=''>TODOS</option>";
										foreach( $arr_tipos as $cod=>$nom )
											echo "<option value='".$cod."' >".$nom."</option>";
							echo "	</select>
								</span>
								<br>
								<br>
								<span style='font-weight:bold;'>
									Subtipo:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b>
									<select obl='obligatorio' style='width:170px;font-size: 10pt;' id='subtipoc' >	
										<option value=''>TODOS</option>
									</select>
								</span>	
								
								<br>
								<br>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<input type='button' value='Consultar' onclick='usarFiltros()' />
								<br>
								<a onclick='mostrarOcultarconvenciones()' style='float:right;'>Ver convenciones</a> 
							</fieldset>
						</td>
					</tr>
					
					<tr>						
						<td style='vertical-align:text-top;' align='left'>							
							<fieldset align='center' style='padding:15px;'>
								<legend class='fieldset' id='titulo_tabla_comprobantes'>Tabla de comprobantes {$nomComprobante}</legend>
								<div id='contenedor_tabla_comprobantes'>
								";
								tablaComprobantes();
							echo"
								</div>
							</fieldset>
						</td>
					</tr>					
				</table>
			</div>
		</div>
		";
	}
	
	function tablaComprobantes($wnombreComprobante='', $wtipo='',$wsubtipo=''){
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;
		global $arr_tipos;
		global $arr_nombreComprobante;
		global $tipoCuentasGBL;
		global $anoActual, $mesActual;
		
		
		if( $wnombreComprobante == '' ){
			reset($arr_nombreComprobante);
			$wnombreComprobante = key($arr_nombreComprobante);			
		}
		
		$arr_nombre_causas = array();
		
		$arr_subtipos = array();
		$sql = "SELECT Subtip as tipo, Subcod as subtipo, Subdes as nombre, Subcau as causa, Subtbc as tabla, Subcmc campo
					   FROM ".$wbasedato."_000030
					  ";
		if( $wtipo != "" )
			$sql.=" WHERE Subtip='".$wtipo."'";
		if( $wtipo != "" && $wsubtipo != "" )
			$sql.=" AND Subcod='".$wsubtipo."'";
			
		$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
		if( $res ){
			$num = mysql_num_rows($res);
			if( $num > 0 ){
				while( $row = mysql_fetch_assoc($res) ){
					if( array_key_exists( $row['tipo'], $arr_subtipos ) == false ){
						$arr_subtipos[ $row['tipo'] ] = array();
					}
					if( array_key_exists( $row['subtipo'], $arr_subtipos[ $row['tipo'] ] ) == false ){
						$arr_subtipos[ $row['tipo'] ][ $row['subtipo'] ] = htmlentities($row['nombre']);
					}
					
					if( $row['causa'] == 'on' ){
						$campos = explode(",",$row['campo']);
						$prefijo = substr($campos[0], 0, 3);
						
						if( count( $campos ) == 2 ){
							$sqlc = "SELECT ".$campos[0]." as codigo, ".$campos[1]." as nombre 
									   FROM ".$wbasedato."_".$row['tabla']."
									  WHERE ".$prefijo."est = 'on'
									   ";
							$resc = mysql_query($sqlc, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
							if( $resc ){							
								$numc = mysql_num_rows($resc);
								if( $numc > 0 ){									
									$arr_nombre_causas[$row['tipo']."-".$row['subtipo']] = array();
									while( $rowc = mysql_fetch_assoc($resc) ){
										foreach($rowc as $indice => &$valor)
											$valor = htmlentities($valor);
										
										$arr_nombre_causas[$row['tipo']."-".$row['subtipo']][ $rowc['codigo'] ] = $rowc['nombre'];
									}
								}
							}
						}
					}
					$arr_nombre_causas[$row['tipo']."-".$row['subtipo']]['*'] = '*';
				}
			}
		}
		
		$arr_nombres_tipos = array();
		$arr_nombres_tdb = array();
		
		$arr_rowspan_tipo = array();
		$arr_rowspan_subtipo = array();
		$arr_rowspan_causa = array();
		
		$arr_comprobantes = array();
		$sqlLista = "SELECT Comtdc as codigo_tdc, Tdcdes as nombre_tdb, Comtip as codigo_tipo, Tipdes as nombre_tipo, a.id,
							Comsub as codigo_subtipo, Comcau as codigo_causa, Comreg as registro, Comtcc as codigo_cuenta,  
							Tccnom as nombre_cuenta, Tccfij as fija, Comfor as formula
					   FROM ".$wbasedato."_000032 a LEFT JOIN ".$wbasedato."_000027 ON (Tcccod=Comtcc AND Tccano='".$anoActual."' AND Tccmes='".$mesActual."'), ".$wbasedato."_000033, ".$wbasedato."_000029 
					  WHERE Comano = '".$anoActual."'
						AND Commes = '".$mesActual."'
					    AND Comtdc = Tdccod
					    AND Comtip = Tipcod
						AND Comnco = '".$wnombreComprobante."'";
						if( $wtipo != "" )
							$sqlLista.=" AND Comtip='".$wtipo."'";
						if( $wsubtipo != "" )
							$sqlLista.=" AND Comsub='".$wsubtipo."'";
		$sqlLista.="
					    AND Comest = 'on'
				   ORDER BY Comtip, Comsub, Comcau, Comreg, Comtdc
		";
		
		$resLista = mysql_query($sqlLista, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlLista):</b><br>".mysql_error());
		while($row = mysql_fetch_assoc($resLista))
		{
			$arr_nombres_tipos[ $row['codigo_tipo'] ] = $row['nombre_tipo'];
			$arr_nombres_tdb[ $row['codigo_tdc'] ] = $row['nombre_tdb'];
			
			if( array_key_exists( $row['codigo_tipo'], $arr_rowspan_tipo ) == false ){
				$arr_rowspan_tipo[ $row['codigo_tipo'] ] = 0;
			}
			if( array_key_exists( $row['codigo_tipo']."-".$row['codigo_subtipo'], $arr_rowspan_subtipo ) == false ){
				$arr_rowspan_subtipo[ $row['codigo_tipo']."-".$row['codigo_subtipo'] ] = 0;
			}
			if( array_key_exists( $row['codigo_tipo']."-".$row['codigo_subtipo']."-".$row['codigo_causa'], $arr_rowspan_causa ) == false ){
				$arr_rowspan_causa[ $row['codigo_tipo']."-".$row['codigo_subtipo']."-".$row['codigo_causa'] ] = 0;
			}			
			if( array_key_exists( $row['codigo_tipo'], $arr_comprobantes ) == false ){
				$arr_comprobantes[ $row['codigo_tipo'] ] = array();
			}
			if( array_key_exists( $row['codigo_subtipo'], $arr_comprobantes[ $row['codigo_tipo'] ] ) == false ){
				$arr_comprobantes[ $row['codigo_tipo'] ][ $row['codigo_subtipo'] ] = array();
			}
			if( array_key_exists( $row['codigo_causa'], $arr_comprobantes[ $row['codigo_tipo'] ][ $row['codigo_subtipo'] ] ) == false ){
				$arr_comprobantes[ $row['codigo_tipo'] ][ $row['codigo_subtipo'] ][ $row['codigo_causa'] ] = array();
			}
			if( array_key_exists( $row['registro'], $arr_comprobantes[ $row['codigo_tipo'] ][ $row['codigo_subtipo'] ][ $row['codigo_causa'] ] ) == false ){
				$arr_comprobantes[ $row['codigo_tipo'] ][ $row['codigo_subtipo'] ][ $row['codigo_causa'] ][ $row['registro'] ] = array();
				
				$arr_rowspan_tipo[ $row['codigo_tipo'] ]++; //por cada registro se le suma rowspan al tipo
				$arr_rowspan_subtipo[ $row['codigo_tipo']."-".$row['codigo_subtipo'] ]++; //por cada registro se le suma rowspan al subtipo
				$arr_rowspan_causa[ $row['codigo_tipo']."-".$row['codigo_subtipo']."-".$row['codigo_causa'] ]++; //por cada registro se le suma rowspan a las causas
			}
			if( array_key_exists( $row['codigo_tdc'], $arr_comprobantes[ $row['codigo_tipo'] ][ $row['codigo_subtipo'] ][ $row['codigo_causa'] ][ $row['registro'] ] ) == false ){
				$arr_comprobantes[ $row['codigo_tipo'] ][ $row['codigo_subtipo'] ][ $row['codigo_causa'] ][ $row['registro'] ][ $row['codigo_tdc'] ] = array();
			}
			
			//Agregarle el nombre de las cuentas al json del campo $row['formula']
			
			
			$cuenta = array();
			$cuenta['codigo'] = $row['codigo_cuenta'];
			$cuenta['nombre'] = $row['nombre_cuenta'];
			$cuenta['fija'] = $row['fija'];
			$cuenta['formula'] = $row['formula'];
			$cuenta['id'] = $row['id'];
			
			array_push( $arr_comprobantes[ $row['codigo_tipo'] ][ $row['codigo_subtipo'] ][ $row['codigo_causa'] ][ $row['registro'] ][ $row['codigo_tdc'] ], $cuenta );				
		}	
		
		echo "	<table style='width:100%;  border: 1px solid black;' class='tabla_comprobantes'>
					<tr class='encabezadoTabla' align='center'>
						<td class='col_tipo'>
							Tipo
						</td>
						<td class='col_subtipo'>
							Subtipo
						</td>
						<td class='col_causa'>
							Causa
						</td>
						<td>
							Registro
						</td>";
		foreach( $tipoCuentasGBL as $cod=>$val){
			echo "		<td>";
			echo 		$val;
			echo "		</td>";
		}	
		echo "		</tr>";
		
		$tr_abierto = false;

		foreach( $arr_comprobantes as $keyTipo => $datosTipo ){
			if( $tr_abierto == false ){
				echo "<tr class='tr_comprobante' align='center'>";
				$tr_abierto = true;
			}
			echo "<td class='col_tipo' rowspan=".($arr_rowspan_tipo[$keyTipo]).">".$arr_nombres_tipos[$keyTipo]."</td>";
			
			foreach( $datosTipo as $keySubtipo => $datosSubtipo ){
			
				if( $tr_abierto == false ){
					echo "<tr class='tr_comprobante' class='tr_subtipo' align='center'>";
					$tr_abierto = true;
				}
				echo "<td rowspan=".($arr_rowspan_subtipo[$keyTipo."-".$keySubtipo]).">".$arr_subtipos[ $keyTipo ][ $keySubtipo ]."</td>";
				
				foreach( $datosSubtipo as $keyCausa => $datosCausa ){
				
					if(  $tr_abierto == false ){
						echo "<tr class='tr_comprobante' align='center'>"; 
						$tr_abierto = true;
					}
					
					echo "<td rowspan=".($arr_rowspan_causa[$keyTipo."-".$keySubtipo."-".$keyCausa]).">";
					echo $arr_nombre_causas[$keyTipo."-".$keySubtipo][$keyCausa]."</td>";
					
					foreach( $datosCausa as $keyRegistro => $datosRegistro ){
						if(  $tr_abierto == false ){
							echo "<tr class='tr_comprobante' align='center'>"; 
							$tr_abierto = true;
						}
						echo "<td>".$keyRegistro."</td>";
						
						foreach( $tipoCuentasGBL as $cod=>$val){
							if( isset( $datosRegistro[$cod]) ){
								echo "<td>
										<ul class='lista_cuentas'>";
								foreach( $datosRegistro[$cod] as $cuentas ){
										$arr_click = array('tipo'=>$keyTipo,
														   'subtipo'=>$keySubtipo,
														   'causa'=>$keyCausa,
														   'registro'=>$keyRegistro,
														   'debitocredito'=>$cod,
														   'cuenta'=>$cuentas['codigo'],														  												   
														   'id'=>$cuentas['id']														   
														   );
										
										if( $cuentas['fija'] == "on" ){
											if( $cuentas['formula'] != "" ){
												echo "<li class='item_cuenta_fija_gris' cuenta='".$cuentas['codigo']."' id_registro='".$cuentas['id']."' onclick='mostrarInfoComprobante(".json_encode($arr_click).")'>".$cuentas['nombre'];											
												echo "<img onclick='crearFormula(this)' class='imgformula' style='cursor:pointer;width:12px;height:12px;float:right;' src='../../images/medical/root/grabar.png' title='Tiene formula' >";
											}else{
												echo "<li class='item_cuenta_fija' cuenta='".$cuentas['codigo']."' id_registro='".$cuentas['id']."' onclick='mostrarInfoComprobante(".json_encode($arr_click).")'>".$cuentas['nombre'];											
												echo "<img onclick='crearFormula(this)' class='imgformula' style='cursor:pointer;width:12px;height:12px;float:right;' src='../../images/medical/root/borrar.png' title='Sin formula' >";
											}
											echo "<input type='hidden' class='formula' value='".$cuentas['formula']."' />";
											echo "</li>";
										}else{
											echo "<li class='item_cuenta' cuenta='".$cuentas['codigo']."' onclick='mostrarInfoComprobante(".json_encode($arr_click).")'>".$cuentas['nombre']."</li>";
										}
								}
								echo "	</ul>
									</td>";
							}else{
								echo "<td>&nbsp;</td>";
							}
						}
						echo "</tr>";
						$tr_abierto = false;
					}
				}
			}
		}
		
		echo "	</table>";
		
		echo "<input type='hidden' value='".json_encode($arr_comprobantes)."' id='arr_comprobantes' />";
	}

	function consultarSubtipos( $wtipo ){
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;
		global $tipoCuentasGBL;
		global $anoActual, $mesActual;
		
		/*Traer los tipos*/
		$arr_subtipos = array();
		$sql = "SELECT Subcod as codigo, Subdes as nombre
					   FROM ".$wbasedato."_000030
					  WHERE Subtip = '".$wtipo."'
					    AND Subest = 'on'";
		$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
		while($row = mysql_fetch_assoc($res))
		{
			foreach($row as $indice => &$valor)
				$valor = htmlentities($valor);
			array_push($arr_subtipos, $row );
		}
	
		return ( $arr_subtipos );
	}
	
	function consultarCausaRegistro( $wtipo, $wsubtipo ){
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;
		global $tipoCuentasGBL;
		global $anoActual, $mesActual;
		
		/*Traer los tipos*/
		$arr_datos = array();
		$sql = "SELECT Subcau as causa, Subtbc as tabla, Subcmc campo
					   FROM ".$wbasedato."_000030
					  WHERE Subtip = '".$wtipo."'
					    AND Subcod = '".$wsubtipo."'
					    AND Subest = 'on'";
		$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
		if( $res ){			
			$num = mysql_num_rows($res);
			if( $num > 0 ){	
				$row = mysql_fetch_assoc($res);
				if( $row['causa'] == 'on' ){
					$campos = explode(",",$row['campo']);
					$prefijo = substr($campos[0], 0, 3);
					
					if( count( $campos ) == 2 ){
						$sqlc = "SELECT ".$campos[0]." as codigo, ".$campos[1]." as nombre 
								   FROM ".$wbasedato."_".$row['tabla']."
								  WHERE ".$prefijo."est = 'on'
								   ";
						$resc = mysql_query($sqlc, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
						if( $resc ){							
							$numc = mysql_num_rows($resc);
							if( $numc > 0 ){								
								//$arr_datos['causas'] = array();
								while( $rowc = mysql_fetch_assoc($resc) ){
									foreach($rowc as $indice => &$valor)
										$valor = htmlentities($valor);
									array_push($arr_datos, $rowc );
								}
							}
						}
					}
				}else{
					//$arr_datos['registros'] = consultarConsecutivoRegistro($wtipo,$wsubtipo);
				}
			}
		}
	
		return ( $arr_datos );
	}
	
	function consultarConsecutivoRegistro($wtipo,$wsubtipo,$wcausa=''){
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;
		global $tipoCuentasGBL;
		global $anoActual, $mesActual;
		
		$arr_datos = array();
		
		$wandcausa = "";
		if( $wcausa != "" )
			$wandcausa = " AND Comcau = '".$wcausa."'";
		/*Traer los tipos*/
		$arr_datos = array();
		$sql = "SELECT Comreg as registro
					   FROM ".$wbasedato."_000032
					  WHERE Comtip = '".$wtipo."'
					    AND Comsub  = '".$wsubtipo."'
						".$wandcausa."
					    AND Comest = 'on'
						AND Comano = '".$anoActual."'
						AND Commes = '".$mesActual."'
				   GROUP BY Comtip, Comsub, Comcau
				   ORDER BY registro";
		$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
		if( $res ){
			$num = mysql_num_rows($res);
			if( $num > 0 ){
				$ult = 0;
				while( $row = mysql_fetch_array($res) ){
					array_push($arr_datos, $row['registro'] );
					$ult = intval($row['registro']);
				}
				$ult++;
				array_push($arr_datos, $ult ); //Crear una opcion mas, por si quiere agregar un nuevo registro	
			}
			else{
				array_push($arr_datos, 1 ); //Como no hay datos previos, sera el registro numero uno
			}
		}
		return $arr_datos;
	}

	function grabarComprobante ($nombreComprobante, $tipo, $subtipo, $causa, $registro, $debitocredito, $cuenta)
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;
		global $wfecha;
		global $whora;
		global $anoActual, $mesActual;
		
		$comtcc="";
		$comtcc = $cuenta;		

		$q = "INSERT ".$wbasedato."_000032 (		Medico 			, 	Fecha_data		, 		Hora_data		, 		Comano			 		, 		Commes			 		, 		Comnco			 		, 		Comtdc			 ,	 Comtip	 	, 	 Comsub			,   	Comcau		,   	Comreg			,   	Comtcc			,   	Comest		, 	Seguridad			) "
								 ." VALUES (	'".$wbasedato."'	,	'".$wfecha."'	,	'".$whora."'		,		'".$anoActual."'  	,				'".$mesActual."'  	,	'".$nombreComprobante."'  	,	'".$debitocredito."'  , '".$tipo."'  ,	'".$subtipo."' 	, 	 '".$causa."'	, 	 '".$registro."'	, 	 '".$comtcc."'		, 		'on'		, 'C-".$wbasedato."' 	)";

		mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query  ".$q." - ".mysql_error());

		echo "OK";
	}
	
	function actualizarComprobante($id_registro, $tipo, $subtipo, $causa, $registro, $debitocredito, $cuenta)
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;
		global $wfecha;
		global $whora;
		global $anoActual, $mesActual;
		
		$comtcc="";
		$comtcc = $cuenta;		

		$q = "UPDATE ".$wbasedato."_000032 SET Comtdc = '".$debitocredito."', 
											   Comtip = '".$tipo."',
											   Comsub = '".$subtipo."',
											   Comcau = '".$causa."',
											   Comreg = '".$registro."',
											   Comtcc = '".$comtcc."'
				WHERE id= ".$id_registro;

		mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query  ".$q." - ".mysql_error());

		echo "OK";
	}
	
	function anularComprobante($id_registro)
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;
		global $wfecha;
		global $whora;
		global $anoActual, $mesActual;
		
		//Actualizo info del movimiento
		$q = "UPDATE ".$wbasedato."_000032
				 SET Comest = 'off'
			   WHERE id = '".$id_registro."' ";
		mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query  ".$q." - ".mysql_error());
		echo "OK";
	}
	
	function guardarFormula($id_registro, $formula){
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;
		global $wfecha;
		global $whora;
		global $anoActual, $mesActual;
		
		//Actualizo info del movimiento
		$q = "UPDATE ".$wbasedato."_000032
				 SET Comfor = '".$formula."'
			   WHERE id = '".$id_registro."' ";
		mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query  ".$q." - ".mysql_error());
		echo "OK";
	
	}
	
	function formularioNuevaFormula()
	{		
		echo "
		<div id='panelFormula' align='center' style='display:none'>			
			<div style='font-family: verdana;font-weight: normal;' align='left'>
				<br>
				 <center><span id='titulonuevaformula' style='color: black;font-family: verdana; font-size:14pt;'></span></center>
				<br>
				<table border=1>
					<tr class='encabezadotabla'><td width='50%' align='center'>Lista de cuentas</td><td width='50%' align='center'>Formula</td></tr>
					<tr>
					<td width='50%'><ul class='lista_cuentas' id='lista_cuentas_formula'></ul></td>
					<td>
					<textarea id='textoformula' class='bordeCurvo' style='font-family: verdana;font-weight:bold;color:#605B60;background:#FFFFF2;font-size:9pt;width:300px;height:280px;border:solid 1px #999999;-moz-border-radius:6px;-webkit-border-radius:6px;border-radius:6px;' readonly></textarea>
					</td>
					</tr>
					<tr class='encabezadotabla'><td colspan=2 align='center'>Operadores</td></tr>
					<tr class='fila2'>
						<td colspan=2 align='center'>
							<input type='button' value='+' onclick='calculadora(\"+\")' class='botoncalculadora' />
							<input type='button' value='-' onclick='calculadora(\"-\")' class='botoncalculadora' />
							<input type='button' value='*' onclick='calculadora(\"*\")' class='botoncalculadora' />
							<input type='button' value='/' onclick='calculadora(\"/\")' class='botoncalculadora' />
							<input type='button' value='Limpiar' onclick='limpiarCalculadora()' class='botoncalculadora' style='width:93px;' />
						</td>
					</tr>
				</table>
			</div>
			<br><br>
				<input type='button' class='botongrabarformula' id='botongrabarformula' value='Grabar' onclick='grabarformula()'>
				<input type='button' class='botonCancelarformula' id='botonCancelarformula'	 style='display :' value='Cancelar' onclick='limpiardatosformula()'>
			<br>
			<input type='hidden' id='formulaOculta' value='[]' />
		</div>";
	}
	
	function mensajeconvenciones(){
		echo "	<div class='toggler' style='display:none;'>
					<div id='effect' class='ui-widget-content ui-corner-all'>
						<h3 class='ui-widget-header ui-corner-all'>Convenciones</h3>
						<ul>
							<li>
							Las cuentas fijas al ser insertadas, aparecen de <span style='background-color:#FF9797'>color rojo</span>. Esto es porque se les debe definir una fórmula que
							se crea al darle clic en el ícono que aparece al final del título de cuenta <img style='width:12px;height:12px;' src='../../images/medical/root/borrar.png'>.
							</li>
							<li>
							Al definir la fórmula para las cuentas fijas, aparecen de <span style='background-color:#CCCCCC'>color gris</span>.
							La fórmula se puede modificar al darle clic en el ícono que aparece al final del título de cuenta <img style='width:12px;height:12px;' src='../../images/medical/root/grabar.png'>.
							</li>
							<li>
							En la transacción Traslado, se definen los títulos de cuenta para los centros de costos origen. Durante la ejecución del comprobante,
							el sistema crea una copia del comprobante invirtiendo las cuentas Débito y Crédito para los centros de costos destino.
							</li>
						</ul>
						<br>
						<input type='button' value='Cerrar' onclick='mostrarOcultarconvenciones()' />
						<br><br>
					</div>
				</div>";
	}
	//=======================================================================================================================================================
//		F I N	 F U N C I O N E S	 P H P
//=======================================================================================================================================================

//=======================================================================================================================================================
//		F I L T R O S  	 D E  	L L A M A D O S  	P O R  	J Q U E R Y
//=======================================================================================================================================================
if(isset($accion))
{
	switch($accion)
	{
		case 'consultarSubtipos':
		{
			consultarSubtipos( $tipo );
			break;
		}
		
		case 'consultarCausaRegistro':
		{
			consultarCausaRegistro( $tipo, $subtipo );
			break;
		}
		case 'consultarConsecutivoRegistro':
		{
			$datos = consultarConsecutivoRegistro( $tipo, $subtipo, $causa );
			echo json_encode($datos);
			break;
		}
		
		case 'grabarComprobante':
		{
			grabarComprobante($nombreComprobante, $tipo, $subtipo, $causa, $registro, $debitocredito, $cuenta);
			break;
		}
		
		case 'actualizarComprobante':
		{
			actualizarComprobante($id_registro, $tipo, $subtipo, $causa, $registro, $debitocredito, $cuenta);
			break;
		}
		
		case 'anularComprobante':
		{
			anularComprobante($id_registro);
			break;
		}
		
		case 'guardarFormula':
		{
			guardarFormula($id_registro, $formula);
			break;
		}
		
		case 'consultarLista':
		{
			/*Traer los tipos*/
			$arr_tipos = array();
			$sql = "SELECT Tipcod as codigo, Tipdes as nombre
						   FROM ".$wbasedato."_000029
						  WHERE Tipest = 'on'";
			$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
			while($row = mysql_fetch_array($res))
			{
				foreach($row as $indice => &$valor)
					$valor = htmlentities($valor);
				$arr_tipos[$row['codigo']] = $row['nombre'];
			}
			
			/*Traer los tipos*/
			$tipoCuentasGBL = array();
			$sql = "SELECT Tdccod as codigo, Tdcdes as nombre
						   FROM ".$wbasedato."_000033
						  WHERE Tdcest = 'on'";
			$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
			while($row = mysql_fetch_array($res))
			{
				foreach($row as $indice => &$valor)
					$valor = htmlentities($valor);
				$tipoCuentasGBL[$row['codigo']] = $row['nombre'];
			}
			
			tablaComprobantes(@$nombreComprobante,@$tipo,@$subtipo);
			break;
		}
	}
	exit;
}
//=======================================================================================================================================================
//		F I N   F I L T R O S   A J A X
//=======================================================================================================================================================


//=======================================================================================================================================================
//	I N I C I O   E J E C U C I O N   N O R M A L   D E L   P R O G R A M A
//=======================================================================================================================================================

	?>
	<html>
	<head>
	  <title>...</title>
	</head>

		<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
		<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />

		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>
		<script src='../../../include/root/jquery.quicksearch.js' type='text/javascript'></script>

	<script type="text/javascript">
//=====================================================================================================================================================================
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T
//=====================================================================================================================================================================

	var url_add_params = addUrlCamposCompartidosTalento();

	$(function(){
		$("#accordionFormula").accordion({
			heightStyle: "content"
		});

		var altura = ($("#accordionFormula").find("div:eq(0)").height()/2)-44;

		$("#accordionLista").accordion({
			heightStyle: "fill",
			collapsible: false
		}).find("div:eq(0)").css("height", $("#accordionFormula").find("div:eq(0)").height());

	});
	
	function cambiarNombreComprobante(){
		var nombreComprobante = $("#nombreComprobante").val();
		$("#nombreComprobantec").val(nombreComprobante);
		$("#tipoc").val();
		$("#subtipoc").val();
		usarFiltros();
	}
	
	function cambiarNombreComprobantec(){
		var nombreComprobante = $("#nombreComprobantec").val();
		$("#nombreComprobante").val(nombreComprobante);
		$("#tipoc").val();
		$("#subtipoc").val();
		usarFiltros();
	}
	
	function consultarSubtiposFiltro(){
		obj = $("#tipoc");
		var tipo = obj.val();
		
		if( tipo == "" ){
			$("#subtipoc").html("<option value=''>TODOS</option>");
			return;			
		}
		
		$("#subtipoc").attr( "tipo", tipo );					

		var tiposSubtiposCausas = $("#tiposSubtiposCausas").val();
		tiposSubtiposCausas = $.parseJSON(tiposSubtiposCausas);
		var cadena_opciones = "<option value=''>TODOS</option>";
		
		var i=0;
		for( i=0;i<tiposSubtiposCausas.length;i++){
			if( tiposSubtiposCausas[i].codigo == tipo ){
				$.each( tiposSubtiposCausas[i].subtipos, function( key, value ) {
					cadena_opciones+="<option value='"+value.codigo+"'>"+value.nombre+"</option>";
				});
				$("#subtipoc").html( cadena_opciones );
				break;
			}
		}
	}
	
	function consultarSubtipos(){	
		obj = $("#tipo");
		var tipo = obj.val();
		
		if( tipo == "" ){
			$("#subtipo").html("");
			return;
		}
		
		$("#subtipo").attr( "tipo", tipo );
		$("#causa").attr( "tipo", tipo );					
		
		var tiposSubtiposCausas = $("#tiposSubtiposCausas").val();
		tiposSubtiposCausas = $.parseJSON(tiposSubtiposCausas);
		var cadena_opciones = "<option value=''>Seleccione</option>";
		
		var i=0;
		for( i=0;i<tiposSubtiposCausas.length;i++){
			if( tiposSubtiposCausas[i].codigo == tipo ){
				$.each( tiposSubtiposCausas[i].subtipos, function( key, value ) {
					cadena_opciones+="<option value='"+value.codigo+"'>"+value.nombre+"</option>";
				});
				$("#subtipo").html( cadena_opciones );
				break;
			}
		}
	}
	
	function consultarCausaRegistro(){
		obj = $("#subtipo");
		
		var tipo = obj.attr("tipo");
		var subtipo = obj.val();
		
		if( tipo == "" || subtipo == "" )
			return;
		
		$("#causa").attr( "subtipo", subtipo );
		
		var tiposSubtiposCausas = $("#tiposSubtiposCausas").val();
		tiposSubtiposCausas = $.parseJSON(tiposSubtiposCausas);
		var cadena_opciones = "<option value=''>Seleccione</option>";
		
		var i=0;
		for( i=0;i<tiposSubtiposCausas.length;i++){
			if( tiposSubtiposCausas[i].codigo == tipo ){
				var j=0;
				for( j=0;j<tiposSubtiposCausas[i].subtipos.length;j++){
					if( tiposSubtiposCausas[i].subtipos[j].codigo == subtipo ){
						if( tiposSubtiposCausas[i].subtipos[j].causas.length > 0 ){
							$.each( tiposSubtiposCausas[i].subtipos[j].causas, function( key, value ) {
								cadena_opciones+="<option value='"+value.codigo+"'>"+value.nombre+"</option>";
							});
							$("#causa").html( cadena_opciones );
							$("#causa").parent().parent().show();
						}else{
							$("#causa").parent().parent().hide();
							consultarConsecutivoRegistro();
						}
						break;
					}
				}
			}
		}		
	}
	
	function consultarConsecutivoRegistro(){
		obj = $("#causa");
		var tipo = obj.attr("tipo");
		var subtipo = obj.attr("subtipo");
		var causa = obj.val();
		if( $("#causa").val() == null || $("#causa").val() == undefined )
			causa = '*';
		
		if( tipo == "" || subtipo == "" )
			return;
		
		var arr_comprobantes = $("#arr_comprobantes").val();
		arr_comprobantes = $.parseJSON(arr_comprobantes);

		var cadena_opciones = "<option value=''>Seleccione</option>";
		
		var i=0;
		if( arr_comprobantes[tipo] != undefined && arr_comprobantes[tipo][subtipo] != undefined && arr_comprobantes[tipo][subtipo][causa] != undefined ){
			for( i in arr_comprobantes[tipo][subtipo][causa] ){
				cadena_opciones+="<option value='"+i+"'>"+i+"</option>";
			}
		}
		i++;
		cadena_opciones+="<option value='"+i+"'>"+i+"</option>";
		$("#registro").html( cadena_opciones );		
	}
	
	function grabarComprobante(){
		var permitirGuardar = true;
		var mensaje;
		$('#tablanuevo .campoObligatorio').removeClass('campoObligatorio');
		
		if( $("#causa").is(":visible") == false )
			$("#causa").attr("obl","");
		else
			$("#causa").attr("obl","obligatorio");

		// --> Validacion de campos obligatorios
		$("#tablanuevo").find("[obl=obligatorio]").each(function(){
			if($(this).val() == '')
			{				
				$(this).addClass('campoObligatorio');
				permitirGuardar = false;
				mensaje = 'Faltan campos por llenar';
			}
		});


		if(permitirGuardar)
		{
			var cuenta = $("#cuenta").val();
			var causa = '';
			if( $("#causa").val() == null || $("#causa").val() == "" || $("#causa").val() == undefined )
				causa = '*';
			else
				causa = $("#causa").val();
				
			$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
			{
				consultaAjax:   		'',
				accion:         		'grabarComprobante',
				nombreComprobante:      $("#nombreComprobante").val(),
				tipo:					$("#tipo").val(),
				subtipo:				$("#subtipo").val(),
				causa:					causa,
				registro:				$("#registro").val(),
				debitocredito:			$("#debitocredito").val(),
				cuenta:					cuenta

			}, function(data){

					if($.trim(data) == "OK"){
						limpiardatos();
						alert("Grabacion Exitosa");
						actualizarLista();
					}
					else{
						alert("Ocurrio un Error\n"+data)
					}
			});
		}
		else
		{
			alert(mensaje);
		}
	}
	
	function usarFiltros(){
		var nombreComprobante = $("#nombreComprobantec").val();
		var tipo = $("#tipoc").val();
		var subtipo = $("#subtipoc").val();
		
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   		'',
			accion:         		'consultarLista',
			nombreComprobante:      nombreComprobante,
			tipo:         			tipo,
			subtipo:         		subtipo

		}, function(data){
			$("#contenedor_tabla_comprobantes").html(data);
			$("#titulo_tabla_comprobantes").text("Tabla de comprobantes "+$("#nombreComprobantec option:selected").text())
		});
	}
	
	function actualizarLista(){
		var nombreComprobante = $("#nombreComprobantec").val();
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   		'',
			nombreComprobante:      nombreComprobante,
			accion:         		'consultarLista'

		}, function(data){
			$("#contenedor_tabla_comprobantes").html(data);			
		});
	}
	
	function mostrarInfoComprobante(datos){
		limpiardatos();
		$("#tipo").val(datos.tipo);
		consultarSubtipos();
		$("#subtipo").val(datos.subtipo);
		consultarCausaRegistro();
		$("#causa").val(datos.causa);
		consultarConsecutivoRegistro();
		$("#registro").val(datos.registro);
		$("#debitocredito").val(datos.debitocredito);
		$("#cuenta").val(datos.cuenta).attr("disabled",false).attr("obl","obligatorio");		
		
		$("#botongrabar").hide();
		$("#botonactualizar").show();
		$("#botonanular").show();
		
		$("#registroMostrado").val( datos.id );								
	}
	
	function actualizarComprobante()
	{
		var permitirGuardar = true;
		var mensaje;
		$('#tablanuevo .campoObligatorio').removeClass('campoObligatorio');

		if( $("#causa").is(":visible") == false )
			$("#causa").attr("obl","");
		else
			$("#causa").attr("obl","obligatorio");
			

		// --> Validacion de campos obligatorios
		$("#tablanuevo").find("[tipo=obligatorio]").each(function(){
			if($(this).val() == '')
			{
				$(this).addClass('campoObligatorio');
				permitirGuardar = false;
				mensaje = 'Faltan campos por llenar';
			}
		});
		
		if( $("#registroMostrado").val() == "" ){
			alert("No se puede actualizar, no existe el registro");
			return;
		}
		var estado = $("[name=estadotitulo]:checked").val();

		if(permitirGuardar)
		{
			var cuenta = $("#cuenta").val();
			var causa = '';
			if( $("#causa").val() == null || $("#causa").val() == undefined )
				causa = '*';
			else
				causa = $("#causa").val();
				
			$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
			{
				consultaAjax:   		'',
				accion:         		'actualizarComprobante',
				tipo:					$("#tipo").val(),
				subtipo:				$("#subtipo").val(),
				causa:					causa,
				registro:				$("#registro").val(),
				debitocredito:			$("#debitocredito").val(),
				cuenta:					cuenta,
				id_registro:			$("#registroMostrado").val() 

			}, function(data){

					if($.trim(data) == "OK"){
						limpiardatos();
						alert("Grabacion Exitosa");
						actualizarLista();
					}
					else{
						alert("Ocurrio un Error\n"+data)
					}

			});			
		}
		else
		{
			alert(mensaje);
		}
	}
	
	function anularComprobante()
	{	
		if( $("#registroMostrado").val() == "" ){
			alert("No se puede anular, no existe el registro");
			return;
		}
		if( confirm("¿Desea anular el registro?") == false )
			return;

		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   		'',
			accion:         		'anularComprobante',			
			id_registro:			$("#registroMostrado").val() 

		}, function(data){
			if($.trim(data) == "OK"){
				limpiardatos();
				alert("Exito al anular");
				actualizarLista();
			}
			else{
				alert("Ocurrio un Error \n"+data)
			}
		});
	}
	
	function limpiardatos(){
		$("#tipo").val("");		
		$("#subtipo").html("");
		$("#causa").html("");
		$("#registro").html("");
		$("#debitocredito").val("");
		$("#cuenta").val("").attr("disabled",false).attr("obl","obligatorio");
		
		$("#botongrabar").show();
		$("#botonactualizar").hide();
		$("#botonanular").hide();		
	}
	
	var objetoCuentaFija = null;
	
	function crearFormula(obj){
		console.log("a");
		obj = jQuery(obj);
		obj = obj.parent();
		objetoCuentaFija = obj;
		
		var cuenta = obj.attr("cuenta");
		var cuentasFila = new Array();		
		//Traer todas las cuentas que hacen parte de la fila del comprobante
		//solo se pueden usar dichas cuentas
		obj.parents(".tr_comprobante").find(".item_cuenta").each(function(){
			cuentasFila.push( $(this).attr("cuenta") );
		});
		if( cuentasFila.length == 0 ){
			alert("No existen más cuentas para crear una fórmula.");
			return;
		}
		var cuentas_todas = $("#arr_cuentas_oculto").val();
		console.log( cuentas_todas ); 
		cuentas_todas = $.parseJSON(cuentas_todas);
		console.log( cuentasFila );
		var i=0; var j=0;
		var html_cuentas = "";
		
		for( i=0;i<cuentasFila.length;i++){
			for( j=0;j<cuentas_todas.length;j++){
				if( cuentas_todas[j].codigo == cuentasFila[i] ){
					html_cuentas+="<li class='item_cuenta' cuenta='"+cuentas_todas[j].codigo+"' onclick='agregarCuentaAFormula(\""+cuentas_todas[j].codigo+"\",\""+cuentas_todas[j].nombre+"\")'>"+cuentas_todas[j].nombre+"</li>";
					break;
				}
			}
		}		
		$("#lista_cuentas_formula").html( html_cuentas );
		
		//Buscar el nombre de la cuenta que vamos a crearle formula
		console.log(cuenta);
		for( j=0;j<cuentas_todas.length;j++){
			if( cuentas_todas[j].codigo == cuenta ){
				$("#titulonuevaformula").text("Formula para la cuenta fija "+cuentas_todas[j].nombre);
			}
		}
		
		//Buscar la formula existente de la cuenta y pegarla en el formulario
		var formula = objetoCuentaFija.find(".formula").val();
		if( formula == "" ){
			formula = "[]";
		}
		var formulaEnPantalla		= JSON.parse(formula);
		var textoPantalla = "";
		$(formulaEnPantalla).each(function(index, objeto){
			if( objeto.nombre == undefined ){ 
				//El json de formula viene del servidor sin las claves de nombre para las cuentas, hay que agregarselo				
				for( j=0;j<cuentas_todas.length;j++){
					if( cuentas_todas[j].codigo == objeto.valor )
						objeto.nombre = cuentas_todas[j].nombre;
				}
			}
			textoPantalla = textoPantalla+"("+objeto.nombre+")";
		});
		$("#textoformula").val(textoPantalla);
		$("#formulaOculta").val(formula);
		
		$("#panelFormula").dialog({
			show:{
				effect: "blind",
				duration: 100
			},
			hide:{
				effect: "blind",
				duration: 100
			},
			width:  680,
			dialogClass: 'fixed-dialog',
			modal: true,
			title: "Formula"
		});
		
		setTimeout(function(){	
				limpiardatos();
		},250);
	}
	
	//Cuando le dan click a las cuentas
	function agregarCuentaAFormula( codigo, nombre ){
		var formulaEnPantalla 	= new Object();
		formulaEnPantalla		= JSON.parse($("#formulaOculta").val());
		var nuevoIndex			= formulaEnPantalla.length;
		var textoPantalla		= '';

		if(elemento != '')
		{
			var elemento 							= $(elemento);
			formulaEnPantalla[nuevoIndex] 			= new Object();
			formulaEnPantalla[nuevoIndex].tipo 		= "Cuenta"
			formulaEnPantalla[nuevoIndex].nombre 	= nombre;
			formulaEnPantalla[nuevoIndex].valor 	= codigo;		
		}

		$(formulaEnPantalla).each(function(index, objeto){
			textoPantalla = textoPantalla+"("+objeto.nombre+")";
		});
		$("#textoformula").val(textoPantalla);
		$("#formulaOculta").val(JSON.stringify(formulaEnPantalla));
	}
	
	//Cuando le dan click a los botones de operacion
	function calculadora( operacion ){
		var formulaEnPantalla 	= new Object();
		formulaEnPantalla		= JSON.parse($("#formulaOculta").val());
		var nuevoIndex			= formulaEnPantalla.length;
		var textoPantalla		= '';

		if(elemento != '')
		{
			var elemento 							= $(elemento);
			formulaEnPantalla[nuevoIndex] 			= new Object();
			formulaEnPantalla[nuevoIndex].tipo 		= "Operador"
			formulaEnPantalla[nuevoIndex].nombre 	= operacion;
			formulaEnPantalla[nuevoIndex].valor 	= operacion;		
		}

		$(formulaEnPantalla).each(function(index, objeto){
			textoPantalla = textoPantalla+"("+objeto.nombre+")";
		});
		$("#textoformula").val(textoPantalla);
		$("#formulaOculta").val(JSON.stringify(formulaEnPantalla));
	}
	
	function grabarformula(){
		var formula = $("#formulaOculta").val();
		if(  $("#formulaOculta").val() == "[]" ){
			var confi = confirm("¿Desea eliminar la formula existente para la cuenta?");
			if( confi ){
				objetoCuentaFija.find(".formula").val("[]");
				objetoCuentaFija.find(".imgformula").attr("src", "../../images/medical/root/borrar.png" );
				objetoCuentaFija.find(".imgformula").attr("title", "Sin formula" );
				objetoCuentaFija.removeClass("item_cuenta_fija_gris").addClass("item_cuenta_fija");
			}
		}else{
			objetoCuentaFija.find(".formula").val(  $("#formulaOculta").val() );			
			objetoCuentaFija.find(".imgformula").attr("src", "../../images/medical/root/grabar.png" );
			objetoCuentaFija.find(".imgformula").attr("title", "Tiene formula" );
			objetoCuentaFija.removeClass("item_cuenta_fija").addClass("item_cuenta_fija_gris");
		}
		$( "#panelFormula" ).dialog( "close" );
		$("#textoformula").val("");
		$("#formulaOculta").val("[]");
		
		//Quitarle la clave "nombre" al json, porque, si el nombre de la cuenta cambia en el maestro de cuentas, se debe mostrar el que es en la formula al traerlo del server
		formula = JSON.parse(formula);
		$(formula).each(function(index, objeto){
			if(objeto.tipo == "Cuenta" ){
				delete objeto.nombre;
			}
		});
		formula = JSON.stringify(formula);
		
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
			{
				consultaAjax:   		'',
				accion:         		'guardarFormula',			
				formula:				formula,
				id_registro:			objetoCuentaFija.attr("id_registro")
			}, function(data){
					if($.trim(data) == "OK"){
						alert("Exito al guardar formula");
					}
					else{
						alert("Ocurrio un Error\n"+data);
						actualizarLista();
					}

			});		
	}
	
	function limpiarCalculadora(){
		$("#formulaOculta").val("[]");
		$("#textoformula").val("");
	}
	
	function limpiardatosformula(){
		$("#formulaOculta").val("[]");
		$( "#panelFormula" ).dialog( "close" );
	}
	
	function isJSON( cadena ){
		var dev = true;
		try{
		   var json = JSON.parse(cadena);
		}catch(e){
			dev=false;
		}
		return dev;
	}	

	function mostrarOcultarconvenciones(){	
		$( ".toggler" ).toggle( "blind", {}, 1000 );
	}

//=======================================================================================================================================================
//	F I N  F U N C I O N E S  J A V A S C R I P T
//=======================================================================================================================================================
	</script>

<!--=====================================================================================================================================================================
	E S T I L O S
=====================================================================================================================================================================-->
	<style type="text/css">
		.toggler {
			width: 440px;
			height: 330px;
			z-index: 9999;
			position: absolute;
			top:240px;
			left: 800px;
		}
		#button {
			padding: .5em 1em;
			text-decoration: none;
		}
		#effect {
			position: relative;
			width: 100%;
			height: 100%;
			padding: 0.4em;
		}
		#effect h3 {
			margin: 0;
			padding: 0.4em;
			text-align: center;
		}  
		fieldset{
			border: 2px solid #e0e0e0;
		}
		legend{
			border: 2px solid #e0e0e0;
			border-top: 0px;
			font-family: Verdana;
			background-color: #e6e6e6;
			font-size: 13pt;
		}
		.fila1
		{
			background-color: #C3D9FF;
			color: #000000;
			font-size: 9pt;
			padding:2px;
			font-family: verdana;
		}
		.fila2
		{
			background-color: #E8EEF7;
			color: #000000;
			font-size: 9pt;
			padding:3px;
			font-family: verdana;
		}
		.encabezadoTabla {
			background-color: #2a5db0;
			color: #ffffff;
			font-size: 9pt;
			padding:3px;
			font-family: verdana;
		}
		.campoObligatorio{
			border-style:solid;
			border-color:red;
			border-width:1px;
		}
		.bordeCurvo{
			-moz-border-radius: 0.4em;
			-webkit-border-radius: 0.4em;
			border-radius: 0.4em;
		}
		.divAreaTrabajo{
			border: 				1px solid #dddddd;
			color: 					#362b36;
			-moz-border-radius: 	6px;
			-webkit-border-radius: 	6px;
			border-radius: 			6px;
			background-color: 		#F2F5F7;
			width:					100%;
		}		
		.tabla_comprobantes{
			border-collapse: collapse;
		}
		.tabla_comprobantes td, th {
			border: 1px solid black;
		}
		#tooltip{font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 6pt;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:3px;opacity:1;border-radius: 4px;}
		#tooltip div{margin:0; width:auto;}

		// --> Estylo para los placeholder
		/*Chrome*/
		[obl=obligatorio]::-webkit-input-placeholder {color:gray; background:lightyellow;font-size:10pt}
		[tipo=valorCalculado]::-webkit-input-placeholder {color:gray; background:#FCEAED;font-size:10pt}
		/*Firefox*/
		[tipo=otro]::-moz-placeholder {color:#000000; background:#E5F6FF;font-size:10pt}
		[obl=obligatorio]::-moz-placeholder {color:#000000; background:lightyellow;font-size:10pt}
		[tipo=valorCalculado]::-moz-placeholder {color:#000000; background:#FCEAED;font-size:10pt}
		/*Interner E*/
		[obl=obligatorio]:-ms-input-placeholder {color:gray; background:lightyellow;font-size:10pt}
		[obl=obligatorio]:-moz-placeholder {color:gray; background:lightyellow;font-size:10pt}
		[tipo=valorCalculado]:-ms-input-placeholder {color:gray; background:#FCEAED;font-size:10pt}
		[tipo=valorCalculado]:-moz-placeholder {color:gray; background:#FCEAED;font-size:10pt}

		.botoncalculadora {
			cursor:				pointer;
			border: 			1px solid #999999;
			background-color:	#D2E8FF;
			width:				45px;
			height:				35px;
			font-size: 			12pt;
			-moz-border-radius:3px;
			-webkit-border-radius:3px;
			border-radius:3px;
		}
		.ui-autocomplete{
			max-width: 	320px;
			max-height: 150px;
			overflow-y: auto;
			overflow-x: hidden;
			font-size: 	10pt;
		}
		.lista_cuentas{
			list-style-type: none; margin: 0; padding: 0; display:inline-block; vertical-align:top; 
			font-size: 3pt;
			font-weight: normal; 
		}
		.lista_cuentas li { margin: 0 3px 3px 0; padding: 0.4em; padding-left: 1.5em; font-size: 2.5em; }
		.item_cuenta  {border: 1px solid #C3D9FF; background: #C3D9FF; color: #000;}
		.item_cuenta  {
			background: #fff; /* Old browsers */
			background: -moz-linear-gradient(top,  #fff 0%, #C3D9FF 30%); /* FF3.6+ */
			background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#fff), color-stop(30%,#C3D9FF)); /* Chrome,Safari4+ */
			background: -webkit-linear-gradient(top,  #fff 0%,#C3D9FF 30%); /* Chrome10+,Safari5.1+ */
			background: -o-linear-gradient(top,  #fff 0%,#C3D9FF 30%); /* Opera 11.10+ */
			background: -ms-linear-gradient(top,  #fff 0%,#C3D9FF 30%); /* IE10+ */
			background: linear-gradient(to bottom,  #fff 0%,#C3D9FF 30%); /* W3C */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#C3D9FF',GradientType=0 ); /* IE6-8 */
			-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#C3D9FF')";
			zoom:1;
			cursor: pointer;
		}
		.item_cuenta_fija{
			background: #fff; /* Old browsers */
			background: -moz-linear-gradient(top,  #fff 0%, #FF9797 30%); /* FF3.6+ */
			background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#fff), color-stop(30%,#FF9797)); /* Chrome,Safari4+ */
			background: -webkit-linear-gradient(top,  #fff 0%,#FF9797 30%); /* Chrome10+,Safari5.1+ */
			background: -o-linear-gradient(top,  #fff 0%,#FF9797 30%); /* Opera 11.10+ */
			background: -ms-linear-gradient(top,  #fff 0%,#FF9797 30%); /* IE10+ */
			background: linear-gradient(to bottom,  #fff 0%,#FF9797 30%); /* W3C */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#FF9797',GradientType=0 ); /* IE6-8 */
			-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#FF9797')";
			zoom:1;
			cursor: pointer;
		}		
		.item_cuenta_fija_gris{
			background: #fff; /* Old browsers */
			background: -moz-linear-gradient(top,  #fff 0%, #CCCCCC 30%); /* FF3.6+ */
			background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#fff), color-stop(30%,#CCCCCC)); /* Chrome,Safari4+ */
			background: -webkit-linear-gradient(top,  #fff 0%,#CCCCCC 30%); /* Chrome10+,Safari5.1+ */
			background: -o-linear-gradient(top,  #fff 0%,#CCCCCC 30%); /* Opera 11.10+ */
			background: -ms-linear-gradient(top,  #fff 0%,#CCCCCC 30%); /* IE10+ */
			background: linear-gradient(to bottom,  #fff 0%,#CCCCCC 30%); /* W3C */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#CCCCCC',GradientType=0 ); /* IE6-8 */
			-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#CCCCCC')";
			zoom:1;
			cursor: pointer;
		}		
		.bordeCurvo{
			-moz-border-radius: 0.4em;
			-webkit-border-radius: 0.4em;
			border-radius: 0.4em;
		}		
		a{ 
			font-size: 11pt;
			text-decoration: underline;
			cursor: auto;
		}
		a:link:active, a:visited:active { 
			color: black;
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
	mensajeconvenciones();
	/*Traer los tipos*/
	$arr_tipos = array();
	$sql = "SELECT Tipcod as codigo, Tipdes as nombre
				   FROM ".$wbasedato."_000029
				  WHERE Tipest = 'on'";
	$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
	while($row = mysql_fetch_array($res))
	{
		foreach($row as $indice => &$valor)
			$valor = htmlentities($valor);
		$arr_tipos[$row['codigo']] = $row['nombre'];
	}
	
	/*Traer los tipos*/
	$tipoCuentasGBL = array();
	$sql = "SELECT Tdccod as codigo, Tdcdes as nombre
				   FROM ".$wbasedato."_000033
				  WHERE Tdcest = 'on'";
	$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
	while($row = mysql_fetch_array($res))
	{
		foreach($row as $indice => &$valor)
			$valor = htmlentities($valor);
		$tipoCuentasGBL[$row['codigo']] = $row['nombre'];
	}
	
	/*Traer el maestro nombre de comprobantes*/
	$arr_nombreComprobante = array();
	$sql = "SELECT Mcocod as codigo, Mcodes as nombre
				   FROM ".$wbasedato."_000037
				  WHERE Mcoest = 'on'";
	$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
	while($row = mysql_fetch_array($res))
	{
		foreach($row as $indice => &$valor)
			$valor = htmlentities($valor);
		$arr_nombreComprobante[$row['codigo']] = $row['nombre'];
	}
	
	// --> Fin Crear hidden

	echo '
	<table width="100%" cellspacing="15">
		<tr>
			<td width="30%" valign="top">';
				formularioNuevoComprobante();
	echo '	</td>
			<td rowspan="2" width="70%" valign="top">';
				formularioComprobantes();
	echo '	</td>
		</tr>
	</table>
	';
	
	$arr_oculto = array();
	//Crear variables ocultas
	foreach( $arr_tipos as $codTipo => $nomTipo ){
		$dato = array('codigo'=>$codTipo, 'nombre'=>$nomTipo);
		$dato['subtipos'] = consultarSubtipos( $codTipo );
		foreach( $dato['subtipos'] as $ind => &$subTipo ){
			$subTipo['causas'] = consultarCausaRegistro( $codTipo, $subTipo['codigo'] );
		}		
		array_push( $arr_oculto, $dato );
	}
	echo "<input type='hidden' value='".json_encode($arr_oculto)."' id='tiposSubtiposCausas' />";
	echo "<input type='hidden' id='registroMostrado' value=''>";
	
	formularioNuevaFormula();
	
	?>
	</BODY>
<!--=====================================================================================================================================================================
	F I N   B O D Y
=====================================================================================================================================================================-->
	</HTML>

