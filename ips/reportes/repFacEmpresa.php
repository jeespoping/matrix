<html>
<head>
<title>Reporte de Facturacion por Empresas</title>
<label>
</label>
<style>
	.botona{
			font-size:13px;
			font-family:Verdana,Helvetica;
			font-weight:bold;
			color:white;
			background:#638cb5;
			border:0px;
			width:180px;
			height:30px;
			margin-left: 1%;
			cursor: pointer;
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
</style>
<script type='text/javascript' src='../../../include/root/jquery-1.3.2.js'></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
<link rel="stylesheet" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" />
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script>
	$.datepicker.regional['esp'] = {
		closeText: 'Cerrar',
		prevText: 'Antes',
		nextText: 'Despues',
		monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
		'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
		monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
		'Jul','Ago','Sep','Oct','Nov','Dic'],
		dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
		dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
		dayNamesMin: ['D','L','M','M','J','V','S'],
		weekHeader: 'Sem.',
		dateFormat: 'yy-mm-dd',
		yearSuffix: ''
	};

	$.datepicker.setDefaults($.datepicker.regional['esp']);
</script>
<script>
	$(document).ready(function(){

		$( "#txtFeInicial" ).datepicker( {
				 showOn: "button",
			buttonImage: "../../images/medical/root/calendar.gif",
		buttonImageOnly: true,
				maxDate: "+0D"
		} );

		$( "#txtFeFinal" ).datepicker( {
					showOn: "button",
					buttonImage: "../../images/medical/root/calendar.gif",
				buttonImageOnly: true,
						maxDate: "+0D"
			} );
	});

	function enter()
	{
		document.forma.submit();
	}
</script>

</head>

<body>

<?php
include_once("conex.php");

/********************************************************
 *     	Reporte de Facturacion por Empresas				*
 *														*
 *********************************************************/

//==================================================================================================================================
//PROGRAMA						:Reporte de Facturacion por Empresas
//AUTOR							:Juan Esteban Lopez Aguirre
//FECHA CREACION				:Abril de 2008
//FECHA ULTIMA ACTUALIZACION 	:
//DESCRIPCION					:
//MODIFICACIONES:
//==================================================================================================================================
//-------------------------------------------------------------------------------------------------------------------------------------------
//	-->	2013-12-24, Jerson trujillo.
//		El maestro de conceptos que actualmente es la tabla 000004, para cuando entre en funcionamiento la nueva facturacion del proyecto
//		del ERP, esta tabla cambiara por la 000200; Entonces se adapta el programa para que depediendo del paramatro de root_000051
//		'NuevaFacturacionActiva' realice este cambio automaticamente.
//-------------------------------------------------------------------------------------------------------------------------------------------

include_once("root/comun.php");

if(!isset($wemp_pmla)){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}

$conex = obtenerConexionBD("matrix");

$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

$wbasedato = strtolower($institucion->baseDeDatos);

//---------------------------------------------------------------------------------------------
// --> 	Consultar si esta en funcionamiento la nueva facturacion
//		Fecha cambio: 2013-12-24	Autor: Jerson trujillo.
//---------------------------------------------------------------------------------------------
$nuevaFacturacion = consultarAliasPorAplicacion($conex, $wemp_pmla, 'NuevaFacturacionActiva');
//---------------------------------------------------------------------------------------------
// --> 	MAESTRO DE CONCEPTOS:
//		- Antigua facturacion 	--> 000004
//		- Nueva facturacion 	--> 000200
//		Para la nueva facturacion cuando esta entre en funcionamiento el maestro
//		de conceptos cambiara por la tabla 000200.
//		Fecha cambio: 2013-12-24	Autor: Jerson trujillo.
//----------------------------------------------------------------------------------------------
$tablaConceptos = $wbasedato.(($nuevaFacturacion == 'on') ? '_000200' : '_000004');
//----------------------------------------------------------------------------------------------

$wentidad = $institucion->nombre;
$hoy = date("Y-m-d");
$hora = (string)date("H:i:s");
$feInicial 	 = date("Y-m-d");
$feFinal 	 = date("Y-m-d");
$key 		 = substr($user,2,strlen($user));
$consolidado = array();
$conceptos   = array();
$i = 1;// controlar el ciclo de entrada de datos qRes
$k = 1;// Controla los colores de la lista de resultados del query
$valTotalCon = 0;

$totalEmpCon = 0;
$totalEmpGen = 0;
$totalEmpPro = 0;
$contProc = 0;

echo"<form action='repFacEmpresa.php' method='post' name='forma'>";

if(isset($rbtnTipo))
echo "<input type='HIDDEN' NAME= 'rbtnTipo' value='".$rbtnTipo."'>";
echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";

session_start();
if(!isset($_SESSION['user']))
{
	echo"error";
}
else
{
	if(!isset($btnConsultar))
	{
		$wactualiz = 'Diciembre 24 de 2013';
		encabezado("Reporte de Facturacion por Empresas",$wactualiz, "clisur");
		echo"<table border='0' align='center'>";
		/*echo"<tr align='center'>";
		echo"<td><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=500 HEIGHT=100'></td>";
		echo"<td bgcolor='6699FF'><font size='+3'>Reporte de Facturacion por Empresas</font></td>";
		echo"</tr>";*/
		echo"<tr align='Center'>";
		echo"<td class='fila1' >Fecha Inicial : ";
		echo"<input type='text' id='txtFeInicial' name='txtFeInicial' value='{$hoy}'>";
		echo "</td>";
		echo"<td class='fila1'>Fecha Final : ";
		echo"<input type='text' id='txtFeFinal' name='txtFeFinal' value='{$hoy}'>";
		echo "</td>";

		echo"</tr>";

		$qSelect = "SELECT Ccocod, Ccodes"
		."			FROM ".$wbasedato."_000003"
		."			ORDER BY Ccocod";

		$resSelect = mysql_query($qSelect,$conex);
		$numSelect = mysql_num_rows($resSelect);

		echo"<tr class='fila2'>";
		echo"<td align='center'>Centro de Costos : ";
		echo"<select name='ddCdC'>";
		echo"<option>% - Todos Los Centros De Costos</option>";
		for($i=0;$i<=$numSelect;$i++)
		{

			$rowSelect = mysql_fetch_row($resSelect);
			if ($ddCdeC!=$rowSelect[0]."-".$rowSelect[1])
			{

				echo "<option>".$rowSelect[0]."-".$rowSelect[1]."</option>";

			}//FIN if ($ddCdeC!=$row[0]."-".$row[1])

		}// Fin for($i=1;$i<=$num;$i++)

		echo"</td>";

		$qSelect = "SELECT empcod, empnit, empnom"
		."			FROM ".$wbasedato."_000024"
		."			WHERE empcod = empres"
		."			ORDER BY empnit";

		$resSelect = mysql_query($qSelect,$conex);
		$numSelect = mysql_num_rows($resSelect);

		echo"<td align='center'>Empresa :";

		echo"<select name='ddEmp'>";
		echo"<option>% - Todas Las Empresas</option>";
		for($i=0;$i<=$numSelect;$i++)
		{

			$rowSelect = mysql_fetch_row($resSelect);

			if ($ddEmp!=$rowSelect[0]."-".$rowSelect[1])
			{

				echo "<option>".$rowSelect[0]."-".$rowSelect[2]."</option>";

			}//FIN if ($ddCdeC!=$row[0]."-".$row[1])

		}// Fin for($i=1;$i<=$num;$i++)

		echo"</td>";
		echo"</tr>";

		//SELECCIONAR CONCEPTO
		$qSelect = "  SELECT grucod, grudes "
		."    FROM ".$tablaConceptos." "
		."   WHERE gruabo != 'on' "
		."ORDER BY grucod, grudes ";

		$resSelect = mysql_query($qSelect,$conex);
		$numSelect = mysql_num_rows($resSelect);

		echo "<tr class='fila1'><td align=center>Concepto :<select name='ddCon'>";
		echo "<option>%-Todos los conceptos</option>";
		for ($i=1;$i<=$numSelect;$i++)
		{
			$rowSelect = mysql_fetch_array($resSelect);
			echo "<option>".$rowSelect[0]." - ".$rowSelect[1]."</option>";
		}
		echo "</select></td>";


		//SELECCIONAR PROCEDIMIENTO
		$qSelect = "  SELECT procod, pronom "
		."    FROM ".$wbasedato."_000103 "
		."   WHERE proest = 'on'  "
		."ORDER BY procod, pronom";

		$resSelect = mysql_query($qSelect,$conex);
		$numSelect = mysql_num_rows($resSelect);

		echo "<td align=center>Procedimiento :<select name='ddProc'>";
		echo "<option>%-Todos los procedimientos</option>";
		for ($i=1;$i<=$numSelect;$i++)
		{
			$rowSelect = mysql_fetch_array($resSelect);
			echo "<option>".$rowSelect[0]." - ".$rowSelect[1]."</option>";
		}
		echo "</select></td></tr>";



		echo"<tr align='center' class='fila2'>";
		echo"<td colspan='2'>Generar Reporte Detallado<input type='radio' name='rbtnTipo' value='1' checked>";
		echo"Generar Reporte Resumido<input type='radio' name='rbtnTipo' value='2'></td>";
		echo"</tr>";
		echo"</table><br>";
		echo"<div align='center'><input type='submit' value='Generar' name='btnConsultar'><br><br><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";

	}
	else
	{
		if(isset($rbtnTipo))
		echo "<input type='HIDDEN' NAME= 'rbtnTipo' value='".$rbtnTipo."'>";

		if($rbtnTipo == '2')
		{
			$wactualiz = 'Diciembre 24 de 2013';
			encabezado("Reporte de Facturacion por Empresas resumido",$wactualiz, "clisur");
		}
		else
		{
			$wactualiz = 'Diciembre 24 de 2013';
			encabezado("Reporte de Facturacion por Empresas detallado",$wactualiz, "clisur");
		}
		//echo"</table>";

		echo "<table style='border: 1px solid; border-color:#2A5DB0;' align='center'>";
		echo "<tr class='fila1'><td align='left'><B>Fecha inicial:</B> ".$txtFeInicial."</td>";
		echo "<td align='right'><B>Fecha final:</B> ".$txtFeFinal."</td></tr>";
		echo "<tr class='fila2'>";
		echo "<td align='left'><b>Empresa : </b>".$ddEmp."</td>";
		echo "<td align='right'><b>Centro De Costos : </b>".$ddCdC."</td>";
		echo"</table>";

		echo"&nbsp;";
		echo "<center><A href='RepFacEmpresa.php?wemp_pmla=".$wemp_pmla."&amp;feInicial=".$txtFeInicial."&amp;feFinal=".$txtFeFinal."&amp;bandera='1'&amp;rbtnTipo=".$rbtnTipo."'>VOLVER</A></center>";
		echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";

		$temp= explode('-',$ddCdC);
		$tempCon = explode('-',$ddCon);
		$tempPro = explode('-',$ddProc);

		if ($ddEmp == '% - Todas Las Empresas')
		{
			$ddEmp = '%';
		}

		$qRes ="  SELECT cs18.Fenfac, cs106.tcarser cco, cs03.ccodes, cs106.Tcarconcod concepto, cs106.Tcarconnom nombreConcepto, cs106.Tcarprocod procedimiento, cs106.Tcarpronom, cs66.rcfval, cs106.Tcarres reponsable, cs106.Tcarcan cantidad"
				."  FROM ".$wbasedato."_000003 cs03,".$wbasedato."_000018 cs18, ".$wbasedato."_000066 cs66, ".$wbasedato."_000106 cs106"
				." WHERE cs18.Fenfec between '".$txtFeInicial."' and '".$txtFeFinal."'"
				."   AND cs18.fenffa = cs66.rcfffa"
				."   AND cs18.Fenfac = cs66.rcffac"
				."   AND cs106.Tcarser = cs03.ccocod"
				."   AND cs106.Tcarser like '".trim($temp[0])."'"
				."   AND cs106.Tcarres like '".$ddEmp."'"
				."   AND cs106.Tcarconcod like '".trim($tempCon[0])."'"
				."   AND cs106.Tcarprocod like '".trim($tempPro[0])."'"
				."   AND cs106.id = cs66.rcfreg"
				."   AND cs106.Tcarconcod NOT IN ('9301','9302','9303','9304')"
				."   AND cs66.rcfest = 'on'"
				."   AND cs18.fenest = 'on'"
				." ORDER BY cs106.Tcarres,cs106.Tcarser,cs106.Tcarconcod,cs106.Tcarprocod";

		$Res = mysql_query($qRes,$conex) or die ("Error: " . mysql_errno() . " - en el query: - " . mysql_error());
		$num = mysql_num_rows($Res);

		if ($num == '0')
		{
			echo"<table align='center' border='0' bordercolor='#000080' width='500' style='border:solid'>";
			echo"<tr><td align='center'colspan='2'><b><font size='3' color='#000080'>La consulta entre las fechas ".$txtFeInicial." y ".
			$txtFeFinal." no Contienen Ningun Documento	Asociado</font><b></td>";
			echo"</tr>";
			echo"</table>";
		}else
			{
				while( $row = mysql_fetch_array($Res) )
				{
					$conceptosNombres[$row['concepto']]['nombre'] = $row['nombreConcepto'];
					$consolidado[trim($row['reponsable'])][$row['cco']][$row['concepto']][$row['procedimiento']]['nombre']   = $row[6];
					$consolidado[trim($row['reponsable'])][$row['cco']][$row['concepto']][$row['procedimiento']]['unidades'] += $row['cantidad'];
					$consolidado[trim($row['reponsable'])][$row['cco']][$row['concepto']][$row['procedimiento']]['valorTotal'] += $row[7];
				}
				$totalEmpresa = 0;

				if($rbtnTipo == '2')//Resumido
				{
					echo "<br><table style='border: 1px solid; border-color:#2A5DB0;' width='90%' align='center'>";
					$i = 0;
					foreach( $consolidado as $keyResponsable=>$ccos ){
							$totalEmpresa = 0;
							echo "<tr><td height='40px' align='left' class='encabezadotabla' colspan='4'><b>Empresa :".$keyResponsable."</b></td></tr>";
							( is_int($i/2) ) ? $wclass = "fila2" : $wclass = "fila1";
							foreach( $ccos as $keyCco=>$conceptos )
							{
								foreach( $conceptos as $keyConcepto=>$procedimientos ){
									echo "<tr class='botona'><td align='CENTER'>CENTRO DE COSTOS</td>";
									echo "<td align=CENTER>CONCEPTO</td>";
									echo "<td align=CENTER>DESCRIPCION</td>";
									echo "<td align=CENTER>VALOR TOTAL</td>";
									foreach( $procedimientos as $keyProcedimiento=>$datos ){
										$totalEmpresa += $datos['valorTotal'];
									}
									echo "<tr class='{$wclass}'><td align='left'>{$keyCco}</td>";
									echo "<td align='left' >".$keyConcepto."</td>";
									echo "<td align='left' >".$conceptosNombres[$keyConcepto]['nombre']."</td>";
									echo "<td align='left' >".number_format($totalEmpresa,0,'.',',')."</td></tr>";
								}
							}
							$totalEmpGen += $totalEmpresa;
							echo "<tr class='encabezadotabla'>";
							echo"<td align='Right' colspan='3'>TOTAL EMPRESA :</td>";
							echo "<td align='left' colspan='1'>".number_format($totalEmpresa,0,'.',',')."</td>";
							echo"</tr>";
						}
						echo "<tr class='encabezadotabla'>";
						echo"<td align='Right' colspan='3'>TOTAL GENERAL EMPRESAS :</td>";
						echo "<td align='left' colspan='1'>".number_format($totalEmpGen,0,'.',',')."</td>";
						echo"</tr>";
						echo"</table>";

					echo"</table>";
				}else//Detallado
					{
						echo "<br><table border=0 style='border: 1px solid; border-color:#2A5DB0;' width='90%' align =center>";
						foreach( $consolidado as $keyResponsable=>$ccos ){
							$totalEmpresa = 0;
							echo "<tr class='encabezadotabla'><td align='left' > EMPRESA: </td><td align='left' height='40px'  colspan='3'><b>".$keyResponsable."</b></td></tr>";
							foreach( $ccos as $keyCco=>$conceptos )
							{
								foreach( $conceptos as $keyConcepto=>$procedimientos ){
									echo "<tr class='encabezadotabla'><td>CONCEPTO : </td><td align='left' colspan='3'><b>".$keyConcepto."</b></td></tr>";
									$i = 0;
									echo "<tr class='botona'><td align='CENTER'>PROCEDIMIENTO</td>";
									echo "<td align=CENTER>DESCRIPCION PROCEDIMIENTO</td>";
									echo "<td align=CENTER>CANTIDAD</td>";
									echo "<td align=CENTER>VALOR TOTAL</td></tr>";
									foreach( $procedimientos as $keyProcedimiento=>$datos ){
										( is_int($i/2) ) ? $wclass = "fila2" :$wclass = "fila1";
										$i++;
										echo "<tr class='{$wclass}'><td align='left' >".$keyProcedimiento."</td>";
										echo "<td align='left'>".$datos['nombre']."</td>";
										echo "<td align='left'>".$datos['unidades']."</td>";
										echo "<td align='left' >".number_format($datos['valorTotal'],0,'.',',')."</td></tr>";
										$totalEmpresa += $datos['valorTotal'];
									}
								}
							}
							$totalEmpGen += $totalEmpresa;
							echo "<tr class='botona'>";
							echo"<td align='Right' colspan='3'>TOTAL EMPRESA :</td>";
							echo "<td align='left' colspan='1'>".number_format($totalEmpresa,0,'.',',')."</td>";
							echo"</tr>";
						}
						echo "<tr class='encabezadotabla'>";
						echo"<td align='Right' colspan='3'>TOTAL GENERAL EMPRESAS :</td>";
						echo "<td align='left' colspan='1'>".number_format($totalEmpGen,0,'.',',')."</td>";
						echo"</tr>";
						echo"</table>";
					}

					echo "<center><A href='RepFacEmpresa.php?wemp_pmla=".$wemp_pmla."&amp;feInicial=".$txtFeInicial."&amp;feFinal=".$txtFeFinal."&amp;bandera='1'&amp;rbtnTipo=".$rbtnTipo."'>VOLVER</A></center>";
					echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";

			}

	}// Fin if(!$btnConsultar)


}//Fin if(!session_start("user"))
liberarConexionBD($conex);
?>

</body>
</html>
