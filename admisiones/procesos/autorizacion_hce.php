<?php
	/**
	 * * PROGRAMA					: AUTORIZACIONES PARA ACCEDER A LA HISTORIA
	 * * AUTOR						: Ing. Joel David Payares Hernández.
	 * * FECHA CREACION				: 13 de Mayo de 2021.
	 * * FECHA ULTIMA ACTUALIZACION	: 
	 * * DESCRIPCION				: Front End de programa de autorización, el cual permite consultar
	 * *							  la información del paciente y tambien las personas autorizadas y
	 * *							  que reclaman la historia clínica del paciente.
	 */

	/**
	 * * Se inicializa el bufer de salida de php
	 */
	ob_start();

	/*
	 * Includes
	*/
	include_once("conex.php");
	
	/**
	 * * Limpiamos el buffer de salida de php para no retornar los datos de los "echos" que se hacen en los include
	 */
	ob_end_clean();
	
	/****************************************************************************
	* acciones
	*****************************************************************************/
	include_once("root/comun.php");
	include_once("root/erp_unix_egreso.php");
	
	header("Content-Type: text/html;charset=ISO-8859-1");

	define("NOMBRE_ADICIONAR",'<IMG id="imgAdicionar" SRC="../../images/medical/root/adicionar2.png" WIDTH=18 HEIGHT=18 title="Se adicionara una fila nueva." />');
	
	$titulo = 'AUTORIZACIONES PARA ACCEDER A HISTORIA';
	
	$conex = obtenerConexionBD("matrix");
	$wemp_pmla = $_GET['wemp_pmla'];
	
	$wactualiz = "Noviembre 13 de 2019.";
	
	$usuarioValidado = true;
	if ( !isset($user) || !isset($_SESSION['user']) )
	{
		$usuarioValidado = false;
	}
	elseif ( strpos($user, "-") > 0 )
	{
		$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
	}
	
	if( empty($wuser) || $wuser == "" )
	{
		$usuarioValidado = false;
	}
	
	if( !isset($wemp_pmla) )
	{
		terminarEjecucion( $MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla" );
	}

	if( !$usuarioValidado ){
		die('
			<br/><br/><br/><br/>
			<div style="color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;" >
				[?] USUARIO NO AUTENTICADOEN EL SISTEMA.<br/><br/>
				Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.<br/><br/>
			</div>');
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html" charset="UTF-8"/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>MATRIX - [<?php echo $titulo ?>]</title>
	<link rel="stylesheet" href="../../../include/root/bootstrap4/css/bootstrap.min.css">
	<link rel="stylesheet" href="../../../include/root/select2/select2.min.css">
	<link rel="stylesheet" href="autorizacion.css">
</head>
<body>

	<?php
		//Encabezado
		encabezado( "AUTORIZACIONES PARA ACCEDER A LA HISTORIA", $wactualiz, "clinica" );

		$msjPermisoUsuario = consultarAliasPorAplicacion( $conex, $wemp_pmla, "preguntaPermisoEgreso" );

		$resTiposDoc = consultaMaestros( 'root_000007', 'Codigo, Descripcion', $where="Estado='on'", '', '' );
		$resPare = consultaMaestros('root_000103','Parcod,Pardes',$where="Parest='on'",'','');
		$param = "class='reset' msgError ux='_ux_pactid_ux_midtii' ";
	?>

	<input type="hidden" name="wemp_pmla" id="wemp_pmla" value="<?php echo $wemp_pmla; ?>">

	<div class="consulta-parametros">
		<!-- <center> -->
			<form id="form-consultar-parametros">
				<table id='tabla-consulta-datos'>
					<tr>
						<th class='encabezadotabla encabezado-consulta' colspan='8'>
							<span class="center-titulo">Parametros de Busqueda</span>
						</th>
					</tr>
					<tr>
						<td class='fila1 fila1-consulta' colspan='3'>
							<span class="center-titulo">Historia</span>
						</td>
						<td>
							<input type="text" class="form-control form-control-sm parametro" id="Authis" name="Authis" data-nombre="historia" require>
							<label for="Authis"></label>
						</td>
					</tr>
					<tr>
						<td class='fila1 fila1-consulta' colspan='3'>
							<span class="center-titulo">Ingreso</span>
						</td>
						<td>
							<input type="text" class="form-control form-control-sm parametro" id="Auting" name="Auting" data-nombre="ingreso" require>
						</td>
					</tr>
				</table>
			</form>

			<div class="alerta col-sm-4">
				<div id="alert-message" class="alert alert-success oculto" role="alert">
					<span id="message" ></span>
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
			</div>

			<div id="div-carga" class="oculto">
				<span>
					<em class="fas fa-spinner fa-pulse"></em>
					 Consultando Datos!!, Favor espere.
				</span>
			</div>

			<div id="div-boton-consultar">
				<button class="btn btn-sm consultar-datos">Consultar Datos</button>
			</div>
		<!-- </center> -->
	</div>

	<div class="respuesta-consulta oculto">
		<div id="datos-basicos-paciente">
			<center>
				<table width='75%'>
					<tr>
						<th class='encabezadotabla encabezado-datos-basicos' colspan='6'>
							Datos b&aacute;sicos de paciente
						</th>
					</tr>
					<tr class='fila1'>
						<td style='width:100px' colspan="1">Historia</td>
						<td style='width:80px' colspan="1">Ingreso</td>
						<td style='width:100px' colspan="4">Nombres Completos</td>
					</tr>

					<tr class='fila1'>
						<td colspan="1">
							<input type='text' name='egr_histxtNumHis' id='egr_histxtNumHis' class='form-control form-control-sm' ux='_ux_egrhis' readonly>
						</td>
						<td colspan="1">
							<input type='text' name='egr_ingtxtNumIng' id='egr_ingtxtNumIng' class='form-control form-control-sm' ux='_ux_egrnum' readonly>
						</td>
						<td colspan="4">
							<input type='text' msgcampo='Nombre Paciente' name='nom_pac' id='nom_pac' class='form-control form-control-sm reset' alfabetico ux='_ux_pnom1_ux_midno1' readonly>
						</td>
					</tr>

					<tr class='fila1'>
						<td colspan='2'>Tipo Documento</td>
						<td colspan='2'>N&uacute;mero Documento</td>
						<td colspan='1'>Edad</td>
						<td colspan='1'>Sexo</td>
					</tr>

					<tr class='fila1'>
						<td colspan='2'>
							<input type='text' name='pac_tdoselTipoDoc' id='pac_tdoselTipoDoc' class='form-control form-control-sm reset' ux='_ux_pacced_ux_midide' readonly>
						</td>
						<td colspan='2'>
							<input type='text' name='pac_doctxtNumDoc' id='pac_doctxtNumDoc' class='form-control form-control-sm reset' ux='_ux_pacced_ux_midide' readonly>
						</td>
						<td colspan='1'>
							<input type='text' msgcampo='Edad' name='pac_edatxtEdad' id='pac_edatxtEdad' class='form-control form-control-sm' readonly>
						</td>
						<td colspan='1'>
							<input type='text' msgcampo='Sexo' name='pac_sextxtSexo' id='pac_sextxtSexo' class='form-control form-control-sm' readonly>
						</td>
					</tr>

					<tr class='fila1'>
						<td colspan='1'>Fecha de Ingreso</td>
						<td colspan='1'>Hora de Ingreso</td>
						<td colspan='4'>Entidad</td>
					</tr>

					<tr class='fila1'>
						<td colspan='1'>
							<input type='text' name='ing_feitxtFecIng' id='ing_feitxtFecIng' class='form-control form-control-sm' readonly>
						</td>
						<td colspan='1'>
							<input type='text' name='ing_hintxtHorIng' id='ing_hintxtHorIng' class='form-control form-control-sm' readonly>
						</td>
						<td colspan='4'>
							<input type='text' msgcampo='Entidad' name='pac_epstxtEps' id='pac_epstxtEps' class='form-control form-control-sm' readonly>
						</td>
					</tr>
				</table>
			</center>
		</div>

		<div id='div_datos_autorizaciones'>

			<?php include_once 'form_autorizacion.inc.php'; ?>

			<div class="alerta-respuesta col-sm-4">
				<div id="alert-respuesta" class="alert alert-success oculto" role="alert">
					<span id="message-respuesta" ></span>
					<button type="button" class="close close-respuesta" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
			</div>

			<center>
				<div id="div-carga-guardar" class="oculto">
					<span class="text-carga">
						<em class="fas fa-spinner fa-pulse"></em>
						Guardando Datos!!, Favor espere.
					</span>
				</div>

				<div class="div-buttons">
					<button class="btn btn-sm regresar">Regresar</button>
					<button class="btn btn-sm guardar-datos">Guardar</button>
				</div>
			</center>
		</div>
	</div>

	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
	<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery.validate.js"></script>
	<script src="../../../include/root/select2/select2.min.js"></script>
	<script src="../../../include/root/toJson.js"></script>
	<script type="text/javascript" src="autorizacion.js"></script>

	<?php
		function consultaMaestros($tabla, $campos, $where, $group, $order, $cant=1){
			global $conex;
			global $wbasedato;
			global $prueba;


				if ($cant==1)
				{
					$q = " SELECT ".$campos."
							FROM ".$tabla."";
					if ($where != "")
					{
						$q.= " WHERE ".$where."";
					}
				}
				else
				{

				$q = " SELECT ".$campos."
						FROM ".$wbasedato."_".$tabla."";
					if ($where != "")
					{
						$q.=" WHERE ".$where."";
					}
				}

					if ($group != "")
					{
						$q.="   GROUP BY ".$group." ";
					}
					if ($order != "")
					{
						$q.=" ORDER BY ".$order." ";
					}

				$res1 = mysql_query($q,$conex) or die (" Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
				// $num1 = mysql_num_rows($res1);

			return $res1;
		}

		/**
		 * Crea un select con el id y name
		 *
		 * @param [type] $res
		 * @param [type] $id Identificador del select
		 * @param [type] $name Nombre del select
		 * @param string $style Estilos del select
		 * @param string $atributos Atributos del select
		 * @return void
		 */
		function crearSelectHTMLAcc($res, $id, $name, $style = "", $atributos = "" ){

			$select= "<SELECT id='$id' name='$name' $atributos $style>";
			$select.= "<option value=''>Seleccione...</option>";

			$num = mysql_num_rows( $res );

			if( $num > 0 ){

					while( $rows = mysql_fetch_assoc( $res ) ){

							$value = "";
							$des = "";

							$i = 0;
							foreach( $rows  as $key => $val ){

									if( $i == 0 ){
											$value = $val;
									}
									else{
											$des .= "-".$val;
									}

									$i++;
							}

							$select.= "<option value='{$value}'>".substr( $des, 1 )."</option>";
					}
			}

			$select.= "</SELECT>";

			return $select;
		}
	?>
</body>
</html>