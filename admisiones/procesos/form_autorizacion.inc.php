<?php
	global $conex;
	global $wemp_pmla;

	$msjPermisoUsuario = consultarAliasPorAplicacion( $conex, $wemp_pmla, "preguntaPermisoEgreso" );

	$resTiposDoc = consultaMaestros( 'root_000007', 'Codigo, Descripcion', $where="Estado='on'", '', '' );
	$resPare = consultaMaestros('root_000103','Parcod,Pardes',$where="Parest='on'",'','');
	$param = "class='reset' msgError ux='_ux_pactid_ux_midtii' ";
?>

<!-- División Frmularios -->
<center>
	<table width='50%' id='tabla_autorizaciones'>
		<tr>
			<th class='encabezadotabla' colspan='8'>
				Autorizaciones
			</th>
		</tr>
		<tr>
			<td class='fila1' colspan=3>
				<?php echo $msjPermisoUsuario; ?>
			</td>
			<td class='fila2'>
				<table width='100%' border='0'>
					<tr>
						<td width='50%' align='center' style='font-size: 11px;' nowrap>
							Si &nbsp; <input egresoAutomatico='no' type='radio' msgcampo='Autoriza informacion o publicidad' style='width:14px;height:12px;' name='aut_inf_radAut' id='aut_inf_radAutS' value='on' onclick='' msgaqua=''>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							No &nbsp; <input egresoAutomatico='no' type='radio' msgcampo='Autoriza informacion o publicidad' style='width:14px;height:12px;' name='aut_inf_radAut' id='aut_inf_radAutN' value='off' onclick='' msgaqua=''>
						</td>
					</tr>
				</table>
			</td>
		</tr>

		<tr>
			<td class='encabezadotabla' colspan=4 align='center'>
				Personas Autorizadas
				<span style='float:right' id="spn_tabla_diagnostico" onclick="addFila2('tabla_personas_autorizadas');" class='add-person-aut'><?php echo NOMBRE_ADICIONAR; ?></span>
			</td>
		</tr>

		<tr>
			<td colspan=4>
				<table width='100%' id='tabla_personas_autorizadas'>
					<!-- personas autorizadas -->
					<tr class='encabezadotabla'>
						<td>Tipo Doc.</td>
						<td>Documento</td>
						<td>Nombre</td>
						<td>Parentesco</td>
					</tr>
					<tr class='fila2 fila_personas_autorizadas'>
						<td>
							<?php
								mysql_data_seek ( $resTiposDoc , 0 );
								echo crearSelectHTMLAcc($resTiposDoc,'dau_tdo','dau_tdo',"","egresoAutomatico='no'");
							?>
							<input  type='hidden' name='dau_tip' class='txt-autoriza' egresoAutomatico='no' value='1' >
						</td>
						<td>
							<input type='text' name='dau_doc' class='txt-autoriza' msgaqua='Documento' egresoAutomatico='no'>
						</td>
						<td>
							<input type='text' name='dau_nom' class='txt-autoriza' msgaqua='Nombre' egresoAutomatico='no'>
						</td>
						<td>
							<?php
								$resPare=consultaMaestros('root_000103','Parcod,Pardes',$where="Parest='on'",'','');
								echo crearSelectHTMLAcc($resPare,'dau_par','dau_par',"", "egresoAutomatico='no'");
							?>
						</td>
					</tr>
				</table>
			</td>
		</tr>

		<tr>
			<td class='encabezadotabla' colspan=4 align='center'>
				Personas que reclaman historia
				<span style='float:right' id="spn_tabla_diagnostico" onclick="addFila2('tabla_personas_reclaman');" class='add-person-recl'><?php echo NOMBRE_ADICIONAR; ?></span>
			</td>
		</tr>

		<tr>
			<td colspan=4>
				<table width='100%' id='tabla_personas_reclaman'>
					<!-- personas que reclaman historia -->
					<tr class='encabezadotabla'>
						<td>Tipo Doc.</td>
						<td>Documento</td>
						<td>Nombre</td>
						<td>Parentesco</td>
					</tr>
					<tr class='fila2 fila_personas_reclaman'>
						<td>
							<?php
								mysql_data_seek ( $resTiposDoc , 0 );
								echo crearSelectHTMLAcc($resTiposDoc,'dau_tdo','dau_tdo',"","egresoAutomatico='no'");
							?>
							<input type='hidden' name='dau_tip' class='txt-reclama' value='2'  egresoAutomatico='no'> <!-- dautip es el tipo de persona, 2 para personas que reclaman -->
						</td>
						<td>
							<input type='text' name='dau_doc' class='txt-reclama' msgaqua='Documento' egresoAutomatico='no'>
						</td>
						<td>
							<input type='text' name='dau_nom' class='txt-reclama' msgaqua='Nombre'    egresoAutomatico='no'>
						</td>
						<td>
							<?php
								mysql_data_seek ( $resPare , 0 );
								echo crearSelectHTMLAcc($resPare,'dau_par','dau_par',"", "egresoAutomatico='no'");
							?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class='encabezadotabla' colspan=4 align='center'>Observaciones</td>
		</tr>
		<tr>
			<td colspan=4 align='center' class='fila2'>
				<textarea rows='3' cols='70' name='aut_obs' id='aut_obs' egresoAutomatico='no'></textarea>
			</td>
		</tr>
	</table>
</center>