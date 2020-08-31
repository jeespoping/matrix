<?php
include_once("conex.php");
if(!isset($accion))
{
    echo '<!DOCTYPE html>';
}

/**
	PROGRAMA                   : Rep_GralDispositivosImplantables.php
	AUTOR                      : Juan Carlos Hernández.
	FECHA CREACION             : 13 Agosto de 2015

	DESCRIPCION: En este reporte se obtiene toda la información de los diferentes dispositivos implantables que se instalan en el cirugía
				en donde para cada dispositivo le registran el lote. Los datos son:
				- Fecha del procemiento
				- Médico que realiza el procedimiento
				- Unidad
				- Nombre del paciente
				- Identificación
				- Historia clínica con número d ingreso
				- Código del dispositivo médico
				- Descripción del dispositivo médico
				- Número de lote
				- Proveedor

				La información es basada en Tarjeta de Dispositivos IMPLANTABLES

	Notas:
	--
 */ $wactualiza = "Mayo 10 de 2018"; /*
 	ACTUALIZACIONES:

	*	Mayo 10 de 2018
		Jessica Madrid Mejía	:	En el query slq_disp_impl_fechas se modifica el left join con cliame_000240 ya que se crea en movhos_000026
									el campo Artfab para relacionar los fabricantes con cada artículo.
	*	Febrero 28 de 2018
    	Edwin MG 	    		: 	Se modifica el programa para mostrar los dispositivos implantables dispensados en pisos
	
	*	Octubre 08 de 2015
    	Eimer Castro     		: 	Se modifica el programa para mostrar la historia clínica y el número de ingreso del paciente.

	*	Septiembre 23 de 2015
    	Eimer Castro     		: 	Se modifica el programa para mostrar el registro INVIMA de los dispositivos implantables.

 	*	Septiembre 21 de 2015
    	Eimer Castro     		: 	Se modifica el programa para mostrar la dirección del fabricante de los dispositivos implantables.

	*	Septiembre 08 de 2015
		Eimer Castro:			:	Se genera el código para la presentación de la información dada en la descripción.

	*	Agosto 13 de 2015
		Juan Carlos Hernández	:	Fecha de la creación del programa.
**/

$fecha_actual = date("Y-m-d");
$hora_actual  = date("H:i:s");






if(!isset($accion) && !isset($_SESSION['user']) && !isset($accion))
{
    echo '  <br /><br /><br /><br />
            <div style="color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}
elseif(isset($accion) && !array_key_exists('user',$_SESSION))
{
    $msjss = "Recargue o inicie sesión nuevamente en la página principal de Matrix para que pueda seguir utilizando este programa normalmente.";
    $data = array('error'=>1,'mensaje'=>$msjss,'html'=>'');
    echo json_encode($data);
    return;
}
$user_session      = explode('-',$_SESSION['user']);
$user_session      = $user_session[1];

//Traigo TODAS las aplicaciones de la empresa, con su respectivo nombre de Base de Datos
$sql_init_data = "	SELECT Detapl, Detval, Empdes
						FROM root_000050, root_000051
							WHERE Empcod = '" . $wemp_pmla. "'
							AND Empest = 'on'
							AND Empcod = Detemp";

$res = mysql_query($sql_init_data, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $sql_init_data . " - " . mysql_error());
$num = mysql_num_rows($res);

  if ($num > 0 )
     {
	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res);

	      if ($row[0] == "cenmez")
	         $wcenmez=$row[1];

	      if ($row[0] == "afinidad")
	         $wafinidad=$row[1];

	      if ($row[0] == "movhos")
	         $wbasedato=$row[1];

	      if ($row[0] == "tabcco")
	         $wtabcco=$row[1];

		  if ($row[0] == "tcx")
	         $wtcx=$row[1];

		  if ($row[0] == "cliame")
	         $wcliame=$row[1];
         }
     }
    else
	{
       echo "NO EXISTE NINGUNA EMPRESA DEFINIDA PARA ESTE CODIGO";
	}

/**
 * Lógica de los llamados AJAX de todo el programa
 */
if(isset($accion) && isset($form))
{
	include_once("root/comun.php");

    $data = array('error'=>0, 'mensaje'=>'', 'html'=>'', 'sql'=>'');
    $no_exec_sub = 'No se ejecutó ningúna rutina interna del programa';

    switch($accion)
    {
		case 'load' :
					switch($form)
					{
						case 'cargar_dispositivos_implantables_fecha':

							/*
							Se traen todos los datos necesarios para el reporte de las tablas de Turnos de Cx tcx_11, quirofanos tcx_12,
							mercados de Cx cliame_240 y los maestros de centros de costo mv_11 y articulos mv_26; en un rango de fechas
							determinado
							*/
							$wmovhos = $wbasedato;
							/* $slq_disp_impl_fechas = "	SELECT  Turfec, Turmed, Cconom, Turnom, Turdoc, Turhis, Turnin, t26.Artcod, t26.Artfab, t26.Artcom, t248.Fabdes, Pronom, Lotlot, Lotcan
														FROM 	{$wtcx}_000011, {$wtcx}_000012,
																{$wmovhos}_000026 AS t26
																LEFT JOIN
																{$wcliame}_000248 AS t248 ON (t26.Artfab = t248.Fabcod)
																LEFT JOIN
																{$wcliame}_000006 AS t6 ON (t248.Fabpro = t6.Procod),
																{$wmovhos}_000011,
																{$wcliame}_000240
														WHERE Turfec BETWEEN '{$fecha_inicio}' and '{$fecha_final}'
															AND Turqui = Quicod
															AND Quicco = Ccocod
															AND Turtur = Lottur
															AND Lotins = t26.Artcod"; */

							// $slq_disp_impl_fechas = "	SELECT  Turfec, Turmed, Cconom, Turnom, Turdoc, Turhis, Turnin, t26.Artcod, t26.Artpro, t26.Artcom, t26.Artreg, t248.Fabdes, t248.Fabdir, t248.Fabcod, t6.Pronom, Lotlot, Lotcan
														// FROM 	{$wtcx}_000011, {$wtcx}_000012,
																// {$wmovhos}_000026 AS t26
																// LEFT JOIN
																// {$wcliame}_000006 AS t6 ON (t26.Artpro = t6.Procod)
																// LEFT JOIN
																// {$wcliame}_000248 AS t248 ON (t6.Profab = t248.Fabcod),
																// {$wmovhos}_000011,
																// {$wcliame}_000240
														// WHERE Turfec BETWEEN '{$fecha_inicio}' and '{$fecha_final}'
															// AND Turqui = Quicod
															// AND Quicco = Ccocod
															// AND Turtur = Lottur
															// AND Lotins = t26.Artcod";
							
							
							$slq_disp_impl_fechas = "	SELECT  Turfec, Turmed, Cconom, Turnom, Turdoc, Turhis, Turnin, t26.Artcod, t26.Artpro, t26.Artcom, t26.Artreg, t248.Fabdes, t248.Fabdir, t248.Fabcod, t6.Pronom, Lotlot, Lotcan, Lotdev
														  FROM 	{$wtcx}_000011, {$wtcx}_000012,
																{$wmovhos}_000026 AS t26
													 LEFT JOIN  {$wcliame}_000006 AS t6 
													        ON  t26.Artpro = t6.Procod
													 LEFT JOIN	{$wcliame}_000248 AS t248 
													        ON  t248.Fabcod = t26.Artfab,
																{$wmovhos}_000011,
																{$wcliame}_000240
														 WHERE  Turfec BETWEEN '{$fecha_inicio}' AND '{$fecha_final}'
														   AND  Turqui = Quicod
														   AND  Quicco = Ccocod
														   AND  Turtur = Lottur
														   AND  Lotins = t26.Artcod
														   
														 UNION  
														SELECT  t240.Lotfmo as Turfec, 
																Lotmed as Turmed, 
																Cconom, 
																CONCAT( Pacno1, ' ',Pacno2, ' ', Pacap1, ' ', Pacap2 ) as Turnom, 
																Pacced as Turdoc, 
																Lothis as Turhis, 
																Loting as Turnin, 
																t26.Artcod, 
																t26.Artpro, 
																t26.Artcom, 
																t26.Artreg, 
																t248.Fabdes, 
																t248.Fabdir, 
																t248.Fabcod, 
																t6.Pronom, 
																Lotlot, 
																SUM( Lotcan ) as Lotcan,
																SUM( Lotdev ) as Lotdev
														  FROM  {$wcliame}_000240 AS t240,
																{$wmovhos}_000011,
																root_000036 AS t36,
																root_000037 AS t37,
																{$wmovhos}_000026 AS t26
													 LEFT JOIN  {$wcliame}_000006 AS t6 
													        ON  t26.Artpro = t6.Procod
													 LEFT JOIN	{$wcliame}_000248 AS t248 
													        ON  t248.Fabcod = t26.Artfab
													     WHERE  Lotfmo BETWEEN '{$fecha_inicio}' AND '{$fecha_final}'
														   AND  Lotins = t26.Artcod
														   AND  Orihis = Lothis
														   AND  Oriori = '".$wemp_pmla."'
														   AND  Pactid = Oritid
														   AND  Pacced = Oriced
														   AND  Ccocod = Lotcco
													  GROUP BY  2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16
														 ";

							$reg_disp_impl_fechas = mysql_query($slq_disp_impl_fechas, $conex) or die($slq_disp_impl_fechas.' - '.mysql_error());
							$array_disp_impl_fechas = array();
							while($row = mysql_fetch_array($reg_disp_impl_fechas))
							{
								$array_disp_impl_fechas[] = $row;
							}

							$html = "";
							if(count($array_disp_impl_fechas) > 0)
							{
								$cont = 0;
								foreach($array_disp_impl_fechas as $index => $row) {
									$cont++;
									$css = ($cont % 2 == 0) ? 'fila1': 'fila2';
									$html .= '<tr class="' . $css . ' find">
										<td>' . $row['Turfec'] . '</td>
										<td>' . utf8_encode($row['Turmed']) . '</td>
										<td>' . utf8_encode($row['Cconom']) . '</td>
										<td>' . utf8_encode($row['Turnom']) . '</td>
										<td>' . $row['Turdoc'] . '</td>
										<td>' . $row['Turhis'] . '</td>
										<td>' . $row['Turnin'] . '</td>
										<td>' . $row['Artcod'] . '</td>
										<td>' . utf8_encode($row['Artcom']) . '</td>
										<td>' . utf8_encode($row['Artreg']) . '</td>
										<td>' . $row['Lotlot'] . '</td>
										<td>' . ( $row['Lotcan'] - $row['Lotdev'] ). '</td>
										<td>' . utf8_encode($row['Pronom']) . '</td>
										<td>' . utf8_encode($row['Fabdes']) . '</td>
										<td>' . utf8_encode($row['Fabdir']) . '</td>
									</tr>';
								}
							}
							else
							{
								$html .= '<tr class="fila1 find">
												<td colspan="13" style="text-align:center;">NO SE ENCONTRARON DATOS!</td>
											</tr>';
							}
							$data["html"] = $html;
							$data["sql"] = $slq_disp_impl_fechas;

							break;

						default:
								$data['mensaje'] = $no_exec_sub;
								$data['error'] = 1;
							break;
					}
					echo json_encode($data);
					break;

		default : break;
    }
    return;
}


/*
Parametros iniciales
*/
$wemp_pmla = (!isset($wemp_pmla)) ? "": $wemp_pmla;
include_once("root/comun.php");

/*
Se traen todos los datos necesarios para el reporte de las tablas de Turnos de Cx tcx_11, quirofanos tcx_12,
mercados de Cx cliame_240 y los maestros de centros de costo mv_11 y articulos mv_26; en un rango de fechas
determinado
*/
$slq_disp_impl = "	SELECT Turfec, Turmed, Cconom, Turnom, Turdoc, Turhis, Turnin, Artcod, Artcom, Lotlot, Lotcan
						FROM " . $wtcx . "_000011, " . $wtcx . "_000012," . $wbasedato . "_000026, " . $wbasedato . "_000011, " . $wcliame . "_000240
							WHERE turfec between '2015-08-01' and '2015-08-30'
								AND Turqui = Quicod
								AND Quicco = Ccocod
								AND Turtur = Lottur
								AND Lotins = Artcod";

$reg_disp_impl = mysql_query($slq_disp_impl, $conex) or die($slq_disp_impl.' - '.mysql_error());
$array_disp_impl = array();
while($row = mysql_fetch_array($reg_disp_impl))
{
	$array_disp_impl[] = $row;
}

?>
<html lang="es-ES">
	<head>
		<title>DISPOSITIVOS IMPLANTABLES</title>
		<meta charset = "utf-8">

		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<!-- Librería para detectar compatibilidad HTML5 con varios navegadores -->
		<script src="../../../include/root/modernizr.custom.js" type="text/javascript"></script>

		<script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>

		<script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>
		<link type="text/css" href="../../../include/root/jquery.ui.timepicker.css" rel="stylesheet"/>

		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
		<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>

		<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
		<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />

		<script type="text/javascript">

			$(document).ready( function (){
				initSearch();

				$("#fecha_inicio_rep").datepicker({
	                showOn: "button",
	                buttonImage: "../../images/medical/root/calendar.gif",
	                buttonImageOnly: true,
	                maxDate:"+0D",
	                buttonText: "Fecha inicial"

				});

				$("#fecha_final_rep").datepicker({
					showOn: "button",
					buttonImage: "../../images/medical/root/calendar.gif",
					buttonImageOnly: true,
					maxDate:"+0D",
	                buttonText: "Fecha final"
				});
			});

			function initSearch()
			{
				$('#id_search_disp_impl').quicksearch('#tablaDispImpl .find');
			}

			function filtrarPorFechas() {
				var objson					= parametrosComunes();
				objson['accion']			= 'load';
				objson['form']				= 'cargar_dispositivos_implantables_fecha';
				objson['fecha_inicio']     	= $("#fecha_inicio_rep").val();
                objson['fecha_final']      	= $("#fecha_final_rep").val();
				$.post("Rep_GralDispositivosImplantables.php",
						objson,
					function(data){
                        if(data.error == 1)
                        {
                            alert(data.mensaje);
                        }
                        else
                        {
							if($("#fecha_inicio_rep").val() <= $("#fecha_final_rep").val())
							{
								$("#div_tablaDispImpl").find(".find").remove();
								$("#div_tablaDispImpl").show();
								$("#div_reporte").show();
								$("#tablaDispImpl").find(".encabezadoTabla2").after(data.html);
							}
							else
							{
								jAlert("La fecha de inicio debe ser menor o igual a la fecha final.", "Alerta");
							}
						}
					},
					"json"

				).done(function() {
					initSearch();
				});
			}

			function parametrosComunes()
			{
				var objson              = {};
				objson['consultaAjax']	= '';
				objson['wemp_pmla']     = $("#wemp_pmla").val();
				objson['wbasedato']     = $("#wbasedato").val();
				return objson;
			}

			 function ordenar(tipo_orden) {
				var coo = $('#cco').val();
				var wemp_pmla = $('#wemp_pmla').val();
				window.location.href = 'saldos_stock.php?wemp_pmla='+wemp_pmla+'&wcco='+coo+'&ordenar='+tipo_orden+'';
			 }

			$.datepicker.regional['esp'] = {
				closeText: 'Cerrar',
				prevText: 'Antes',
				nextText: 'Despues',
				monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
				'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
				monthNamesShort: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
				'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
				dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
				dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
				dayNamesMin: ['D','L','M','M','J','V','S'],
				weekHeader: 'Sem.',
				dateFormat: 'yy-mm-dd',
				yearSuffix: '',
				changeYear: true,
				changeMonth: true,
				yearRange: '-100:+0'
			};
			$.datepicker.setDefaults($.datepicker.regional['esp']);

			function cerrarVentanaPpal()
			{
				window.close();
			}

		</script>

		<style type="text/css">
			label, input { display:block; }
			input.text { margin-bottom:12px; width:95%; padding: .4em; }
			fieldset { padding:0; border:0; margin-top:25px; }
			h1 { font-size: 1.2em; margin: .6em 0; }
			div#users-contain { width: 350px; margin: 20px 0; }
			div#users-contain table { margin: 1em 0; border-collapse: collapse; width: 100%; }
			div#users-contain table td, div#users-contain table th { border: 1px solid #eee; padding: .6em 10px; text-align: left; }
			.ui-dialog .ui-state-error { padding: .3em; }
			.validateTips { border: 1px solid transparent; padding: 0.3em; }

			.placeholder
			{
			  color: #aaa;
			}

			.encTabla{
				text-align: center;
			}

			/* CORRECCION DE BUG PARA EL DATEPICKER Y CONFIGURACION DEL TAMAÑO  */
			.ui-datepicker {font-size:12px;}
			/* IE6 IFRAME FIX (taken from datepicker 1.5.3 */
			.ui-datepicker-cover {
				display: none; /*sorry for IE5*/
				display/**/: block; /*sorry for IE5*/
				position: absolute; /*must have*/
				z-index: -1; /*must have*/
				filter: mask(); /*must have*/
				top: -4px; /*must have*/
				left: -4px; /*must have*/
				width: 200px; /*must have*/
				height: 200px; /*must have*/
			}

			.classOver{
				background-color: #CCCCCC;
			}
			A   {text-decoration: none;color: #000066;}
			.tipo3V{color:#000066;background:#dddddd;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px }
			.tipo3V:hover {color: #000066; background: #999999;}

			.brdtop {
				border-top-style: solid; border-top-width: 2px;
				border-color: #2A5BD0;
			}
			.brdleft{
				border-left-style: solid; border-left-width: 2px;
				border-color: #2A5BD0;
			}
			.brdright{
				border-right-style: solid; border-right-width: 2px;
				border-color: #2A5BD0;
			}
			.brdbottom{
				border-bottom-style: solid; border-bottom-width: 2px;
				border-color: #2A5BD0;
			}

			.alto{
				height: 140px;
			}

			.vr
			{
				display:inline;
				height:50px;
				width:1px;
				border:1px inset;
				/*margin:5px*/
				border-color: #2A5BD0;
			}

			.bgGris1{
				background-color:#F6F6F6;
			}

			.tbold{
				font-weight:bold;
				text-align:left;
			}
			.alng{
				text-align:left;
			}
			.img_fondo{
				background: url('../../images/medical/tal_huma/fondo.png');
				background-repeat: no-repeat;
			}
			.disminuir{
				font-size:11pt;
			}
			.imagen { width: 250px; height: auto;}
			.btnActivo { background-color: #0033ff; }
			.padding_info{
				padding-bottom: 4px;
			}
			.border_ppal{
				border: 2px solid #2A5DB0;
			}
			.txt1{
				/*color:#2A5DB0;*/
				font-weight:bold;
			}
			.fondoEncabezado{
				background-color: #2A5DB0;
				color: #FFFFFF;
				font-size: 10pt;
				font-weight: bold;
			}

			.campoRequerido{
				border: 1px orange solid;
				background-color:lightyellow;
			}

			.st_boton{
				/*font-size:10px;
				font-family:Verdana,Helvetica;
				font-weight:bold;
				color:white;
				background:#638cb5;
				border:0px;
				width:80px;
				height:19px;*/

			   background-color: #4D90FE;
			   background-image: -webkit-gradient(linear,left top,left bottom,from(#4D90FE),to(#4787ED));
			   background-image: -moz-linear-gradient(top,#4D90FE,#4787ED);
			   background-image: -ms-linear-gradient(top,#4D90FE,#4787ED);
			   background-image: -o-linear-gradient(top,#4D90FE,#4787ED);
			   background-image: -webkit-linear-gradient(top,#4D90FE,#4787ED);
			   background-image: linear-gradient(top,#4D90FE,#4787ED);
			  filter: progid:DXImageTransform.Microsoft.gradient
			   (startColorStr='#4d90fe',EndColorStr='#4787ed');
			   border: 1px solid #3079ED;
			   -moz-border-radius: 2px;
			   -webkit-border-radius: 2px;
			   border-radius: 2px;
			   -moz-user-select: none;
			   -webkit-user-select: none;
			   color: white;
			   display: inline-block;
			   font-weight: bold;
			   height: 25px;
			   line-height: 20px;
			   text-align: center;
			   text-decoration: none;
			   padding: 0 8px;
			   margin: 0px auto;
			  font: 13px/27px Arial,sans-serif;
			  cursor:pointer;
			}

			.parrafo1{
				color: #333333;
				background-color: #cccccc;
				font-family: verdana;
				font-weight: bold;
				font-size: 10pt;
				text-align: left;
			}
			.no_save{
				border: red 1px solid;
			}
			.mayuscula{
				text-transform: uppercase;
			}

			#tooltip{font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 8pt;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:3px;opacity:1;border-radius: 4px;}

			/*#tooltip{
				color: #FE2E2E;font-family: Arial,Helvetica,sans-serif;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:5px;opacity:1;
			}*/
			#tooltip h3, #tooltip div{
				margin:0; width:auto
			}

			#tooltip_pro{font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 8pt;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:3px;opacity:1;border-radius: 4px;}

			/*#tooltip{
				color: #FE2E2E;font-family: Arial,Helvetica,sans-serif;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:5px;opacity:1;
			}*/
			#tooltip_pro h3, #tooltip_pro div{
				margin:0; width:auto
			}

			.error{
				font-weight: bold;
				color: red;
			}
			.correct{
				font-weight: bold;
				color: green;
			}
			.endlog{
				font-weight: bold;
				color: orange;
			}

			#caja_flotante{
				position: absolute;
				/*top:0;*/
				/*left: 10px;*/
				border: 1px solid #CCC;
				background-color: #F2F2F2;
				/*width:150px;*/
			}

			.caja_flotante{
				position: absolute;
				/*top:0;*/
				/*left: 10px;*/
				border: 1px solid #CCC;
				background-color: #F2F2F2;
				/*width:150px;*/
			}

			/* TABS */
			ul.pestania {
				border-bottom: 1px solid #E5E5E5;
				float: left;
				font-size: 0;
				margin: 10px 0 -1px;
				padding: 0;
				width: 100%;
			}
			ul.pestania.left {
				text-align: left;
			}
			ul.pestania.center {
				text-align: center;
			}
			ul.pestania.right {
				text-align: right;
			}
			ul.pestania.right li {
				margin: 0 0 0 -2px;
			}
			ul.pestania li {
				display: inline-block;
				font-size: 14px;
				left: 0;
				list-style-type: none;
				margin: 0 -2px 0 0;
				padding: 0;
				position: relative;
				top: 0;
			}
			ul.pestania li a {
				-moz-border-bottom-colors: none;
				-moz-border-left-colors: none;
				-moz-border-right-colors: none;
				-moz-border-top-colors: none;
				background: none repeat scroll 0 0 #F5F5F5;
				border-color: #E5E5E5 #E5E5E5 -moz-use-text-color;
				border-image: none;
				border-style: solid solid none;
				border-width: 1px 1px 0;
				box-shadow: 0 -3px 3px rgba(0, 0, 0, 0.03) inset;
				color: #666666;
				display: inline-block;
				font-size: 0.9em;
				left: 0;
				line-height: 100%;
				padding: 9px 15px;
				position: relative;
				text-decoration: none;
				top: 0;
			}
			ul.pestania li a:hover {
				background: none repeat scroll 0 0 #FFFFFF;
			}
			ul.pestania li.current a {
				background: none repeat scroll 0 0 #FFFFFF;
				box-shadow: none;
				color: #222222;
				left: 0;
				position: relative;
				top: 1px;
			}

			.tab-content {
				background: none repeat scroll 0 0 #FFFFFF;
				border: 1px solid #E5E5E5;
				clear: both;
				margin: 0 0 3px;
				padding: 3px;
				/*margin: 0 0 40px;
				padding: 20px;*/
			}
			/* TABS */

			.ui-autocomplete{
				max-width:  230px;
				max-height: 150px;
				overflow-y: auto;
				overflow-x: hidden;
				font-size:  9pt;
			}

			/* NOTIFICACIÓN */
			#notificacion {
				background-color: #F2F2F2;
				background-repeat: no-repeat;
				font-family: Helvetica;
				font-size: 20px;
				line-height: 30px;
				position: absolute;
				text-align: center;
				width: 30%;
				left: 35%;
				top: -30px;
			}
			.chat {
				background-image: url("../../images/medical/root/info.png");
			}

			/*.notificar {
				background-color: #59AADA;
				border-radius: 6px;
				border: 1px solid #60B4E5;
				color: #FFFFFF;
				display: block;
				font-size: 30px;
				font-weight: bold;
				letter-spacing: -2px;
				margin: 60px auto;
				padding: 20px;
				text-align: center;
				text-shadow: 1px 1px 0 #145982;
				width: 350px;
				cursor: pointer;
			}*/

			/*.notificar:hover {
				background-color: #4a94bf;
			}*/
			/* NOTIFICACIÓN */

			.fixed-dialog{
				 position: fixed;
				 top: 100px;
				 left: 100px;
			}

			.ui-dialog
			{
				background: #FFFEEB;
			}

			.texto_add{
				font-size: 8pt;
			}

			.submit{
				text-align: center;
				background: #C3D9FF;
			}
			.pad{
				padding:    4px;
			}

			.margen-superior-eventos{
				margin-top:15px;
				border:2px #2A5DB0 solid;
			}

			.datos-adds-eventos{
				text-align:left; border: 1px solid #cccccc;
			}

			ul{
				margin:0;
				padding:0;
				list-style-type:none;
			}

			table[id^='tabla_lista_cxs_'] td {
				font-size: 8.5pt;
			}

			.alinear_derecha {
				display: block;
				float:right;
				width: 70px;
				text-align: center;
				/*color: #FF2F00;*/
			}

			.div_alinear{
				margin-left: 10px;
			}

			.td_noTarifa{
				background-color: #ffffcc;
			}
			.titulopagina2
			{
				border-bottom-width: 1px;
				border-color: <?=$bordemenu?>;
				border-left-width: 1px;
				border-top-width: 1px;
				font-family: verdana;
				font-size: 18pt;
				font-weight: bold;
				height: 30px;
				margin: 2pt;
				overflow: hidden;
				text-transform: uppercase;
			}
		</style>

	</head>

	<body>
		<input type="hidden" id="wemp_pmla" value="<?=$wemp_pmla?>" >
		<input type='hidden' id="wbasedato" value="<?=$wbasedato?>" name='wbasedato' >
		<?php
			encabezado("<div class='titulopagina2'>Dispositivos Implantables</div>", $wactualiza, "clinica");
		?>

		<br />

		<table align="center" style="width:95%;">
			<tr>
				<td style="text-align:left;">
					<div id="contenedor_programa_reporte" align="left">
						<div id="div_filtros" style="width:100%;" align="center">
							<table id="tabla_filtros" align="center" >
								<tr>
									<td colspan="4" class="encabezadoTabla" style="text-align:center;">Filtros del reporte</td>
								</tr>
								<tr class="tooltip" title="Rango de fechas para generar reporte.">
									<td class="encabezadoTabla">Fecha inicio</td>
									<td class="fila2"><input type="text" class="datoreq" id="fecha_inicio_rep" name="fecha_inicio_rep" value="<?=date("Y-m-d")?>" size="8" disabled="true" style="display: inline;"></td>
									<td class="encabezadoTabla">Fecha fin</td>
									<td class="fila2"><input type="text" class="datoreq" id="fecha_final_rep" name="fecha_final_rep" value="<?=date("Y-m-d")?>" size="8" disabled="true" style="display: inline;"></td>
								</tr>
								<tr>
									<td colspan="4" class="encabezadoTabla" align="center"><input id="btn_filtrar_fechas" type="button" onclick="filtrarPorFechas();" value="Consultar" name="btn_filtrar_fechas" ></td>
								</tr>
							</table>
						</div>
						<div id="div_reporte" style="width:100%;" align="center" hidden="true">
							<table id="tabla_contenedor_rep">
								<tr>
									<td id="td_contenedor_rep">
										<table id="tabla_resultado_reporte" align="center">
											<tr class="encabezadoTabla">
												<td>
													USE LOS FILTROS DEL REPORTE PARA CONSULTAR
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr class="encabezadoTabla" align="center">
									<td>Buscar: <input id="id_search_disp_impl" type="text" value="" name="id_search_disp_impl" ></td>
								</tr>
							</table>
						</div>
					</div>
				</td>
			</tr>
		</table>
		<br />
		<div style="" id="div_tablaDispImpl" hidden="true">
			<table align="center" style="width:95%;" id="tablaDispImpl">
				<tr class="encabezadoTabla encabezadoTabla2">
					<th>Fecha Procedimiento</th>
					<th>M&eacute;dico</th>
					<th>Unidad</th>
					<th>Paciente</th>
					<th>Documento Id</th>
					<th>Historia Cl&iacute;nica</th>
					<th>Ingreso</th>
					<th>C&oacute;digo Dispositivo</th>
					<th>Nombre Dispositivo</th>
					<th>Registro INVIMA <br>Dispositivo</th>
					<th>Nro Lote</th>
					<th>Cantidad</th>
					<th>Proveedor</th>
					<th>Fabricante</th>
					<th>Direcci&oacute;n Fabricante</th>
				</tr>
			</table>
		</div>
		<br />
		<br />
		<table align='center'>
			<tr><td align="center" colspan="9"><input type="button" value="Cerrar Ventana" onclick="cerrarVentanaPpal();"></td></tr>
		</table>
		<br />
		<br />
	</body>
</html>