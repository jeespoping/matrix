<?php
include_once("conex.php");
/*
Descripcion

Este programa muestra el listado de todos los incrementos realizados por el programa incremento_tarifas, mostrando el listado de todos los articulos
que se actualizaron, permitiendo devolver ese incremento y generando listados en excel.

Creador : Jonatan Lopez Aguirre

*/ $actualiz = "2017-02-02"; /*
 ACTUALIZACIONES:
 *
**/


if(isset($operacion) && !array_key_exists('user',$_SESSION))
{
    $msjss = "Recargue o inicie sesión nuevamente en la página principal de Matrix para que pueda seguir utilizando este programa normalmente.";
    $data = array('error'=>1,'mensaje'=>$msjss,'html'=>'');
    echo json_encode($data);
    return;
}
elseif(!isset($operacion) && !array_key_exists('user',$_SESSION))
{
    echo '  <br /><br /><br /><br />
            <div style="color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}

  

  include_once("root/comun.php");

  $wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
  conexionOdbc($conex, $wbasedato, $conexUnix, 'facturacion');
  $conexUnix = odbc_pconnect('facturacion','informix','sco') or die("No se ralizo Conexion con Unix");
  $wbasedato_cliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');


function limpiarString($string)
{
 $string = htmlentities($string);
 $string = preg_replace('/\&(.)[^;]*;/', '\\1', $string);
 return $string;
}

if(isset($operacion) && $operacion == 'devolver_incremento'){

	$wfecha = date('Y-m-d');
	$whora = date('H:i:s');

	$query = "SELECT Logcodart, Logoldval, Lognewval, Logoldvaa, Lognewvaa, Logoldfin, Lognewfin, Logtarifa
			    FROM ".$wbasedato_cliame."_000277 as encabezado, ".$wbasedato_cliame."_000272 as detalle
			   WHERE encabezado.id = detalle.Logconsec
			     AND detalle.Logconsec = '".$id."'
				 AND Logestado = 'on'
				 AND Logest = 'on'";
	$res = mysql_query($query,$conex) or die("Error en el query: ".$q_medicamento."<br>Tipo Error:".mysql_error());

	while($row = mysql_fetch_array($res)){

		$query_update = "UPDATE ivarttar SET arttarval = '".$row['Logoldval']."', arttarvaa='".$row['Logoldvaa']."', arttarfec = '".$row['Logoldfin']."' WHERE arttarcod = '".$row['Logcodart']."' AND arttartar = '".$row['Logtarifa']."'";
		$err_o = odbc_do($conexUnix,$query_update) or die (odbc_errormsg());

		$datamensaje['mensaje'] = "Incremento devuelto";
	}

	$log = "INSERT INTO ".$wbasedato_cliame."_000280 (       Medico           ,   Fecha_data    ,   Hora_data   ,   Devcon  ,    Seguridad ) "
										."    VALUES ('".$wbasedato_cliame."','" . $wfecha . "','" . $whora . "', '".$id."' ,'".$_SESSION['user']."')";
	$err = mysql_query($log, $conex) or die (mysql_errno().$log." - ".mysql_error());

	echo json_encode($datamensaje);

	return;


}


if(isset($operacion) && $operacion == 'ver_detalle'){

	$datamensaje = array('mensaje'=>'', 'error'=>0 , 'formulario'=>'', 'boton_devolver'=>'',
						 'html_detalle_boton'=>'',
						 'html_encabezado'=>'',
						 'html_titulo_detalle'=>'',
						 'html_titulo_detalle_excel'=>'',
						 'html_detalle_datos'=>'',
						 'html_detalle_datos_excel'=>'',);
	$caracteres =  array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü");
	$caracteres2 = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U");

	$array_articulos = consultarArticulos();
	$array_regulados = consultarRegulados();
	$array_tarifas = consultarTarifas();
	$array_empresas = consultarEmpresas();

	$query = "SELECT *
				FROM ".$wbasedato_cliame."_000280
				WHERE Devcon = '".$id."'";
	$res = mysql_query($query,$conex) or die("Error en el query: ".$query."<br>Tipo Error:".mysql_error());
	$num = mysql_num_rows($res);

	if($num > 0){

		$no_devolver = "disabled";
		$mensaje_devolucion = "Esta devolución ya ha sido realizada";
		$datamensaje['boton_devolver'] .= "<center><input type='button' $no_devolver value='Devolver incremento' id='btndevolver' onclick='devolver_incremento(\"".$id."\")'><br>$mensaje_devolucion</center>";

	}else{

		$query = "SELECT Devcon
					FROM ".$wbasedato_cliame."_000280
			    ORDER BY id DESC
				   LIMIT 1";
		$res = mysql_query($query,$conex) or die("Error en el query: ".$query."<br>Tipo Error:".mysql_error());
		$row = mysql_fetch_array($res);

		$query1 = "SELECT id
					FROM ".$wbasedato_cliame."_000277
				   WHERE Logest = 'on'
			    ORDER BY id DESC
				   LIMIT 1";
		$res1 = mysql_query($query1,$conex) or die("Error en el query: ".$query1."<br>Tipo Error:".mysql_error());
		$row1 = mysql_fetch_array($res1);

		//Solo puede devolver el incremento anterior.
		if(((int)$row['Devcon'] - (int)$id) == 1){

			$datamensaje['boton_devolver'] = "<center><input type='button' value='Devolver incremento' id='btndevolver' onclick='devolver_incremento(\"".$id."\")'></center>";
		}

		if($id == $row1['id']){

			$datamensaje['boton_devolver'] = "<center><input type='button' value='Devolver incremento' id='btndevolver' onclick='devolver_incremento(\"".$id."\")'></center>";

		}
	}

	$datamensaje['html_detalle_boton'] .= "<center><button id='exportButton'>Exportar a Excel</button></center>";

	$query = "SELECT *
				FROM ".$wbasedato_cliame."_000277
			   WHERE Logest = 'on'
				 AND id = '".$id."'";
	$res = mysql_query($query,$conex) or die("Error en el query: ".$q_medicamento."<br>Tipo Error:".mysql_error());

	$datamensaje['html_encabezado'] .= "<center><table>";
	$datamensaje['html_encabezado'] .= "<tr class=encabezadoTabla><td>Fecha y Hora de incremento</td><td>Nit Entidad</td><td>Fecha inicio incremento</td><td>Tarifa</td><td>Inc. Porcentaje</td><td>Inc. Valor Fijo</td><td>Inc. Valor adicional</td><td>Basado en costo</td></tr>";
	$i = 0;
	while($row = mysql_fetch_array($res)){
		$Logtar = $row['Logtar'];
		$LogtarNom = ($row['Logtar'] == '%') ? 'Todas las tarifas': $array_tarifas[$Logtar];
		$inc_sobre_costo = ($row['Logico'] == 'on') ? 'Si': 'No';
		$datamensaje['html_encabezado'] .= "<tr class='fila1'><td>".$row['Fecha_data']." ".$row['Hora_data']."</td><td>".$row['Lognit']." - ".$array_empresas[trim($row['Lognit'])]."</td><td align=center>".$row['Logfin']."</td><td align=center>".$Logtar." - ".$LogtarNom."</td><td align=center>".$row['Loginp']."</td><td align=center>".$row['Loginv']."</td><td align=center>".$row['Logiva']."</td><td align=center>".$inc_sobre_costo."</td></tr>";

	}
	$datamensaje['html_encabezado'] .= "</table></center>";

	$query = "SELECT Logcodart, Logoldval, Lognewval, Logoldvaa, Lognewvaa, Logoldfin, Lognewfin, Logtarifa, Logcosto, Logincos
			    FROM ".$wbasedato_cliame."_000277 as encabezado, ".$wbasedato_cliame."_000272 as detalle
			   WHERE encabezado.id = detalle.Logconsec
			     AND detalle.Logconsec = '".$id."'
				 AND Logestado = 'on'";
	$res = mysql_query($query,$conex) or die("Error en el query: ".$q_medicamento."<br>Tipo Error:".mysql_error());

	$datamensaje['html_titulo_detalle'] .= "<div align=left><b>Buscar:</b><input id='buscar_articulo' class='bordeRed' placeholder='Buscar articulo' type='text'></span><br><br>";
	$datamensaje['html_titulo_detalle'] .= "<center><table>";
	$datamensaje['html_titulo_detalle'] .= "<thead><tr class='encabezadotabla'><th>Codigo</th><th>Nombre generico</th><th>Nombre comercial</th><th>UN</th><th>Registro Invima</th><th>Código Tarifa</th><th>Tarifa anterior</th><th>Tarifa Actual</th><th>Basado en costo</th><th>Fecha inicio anterior</th></tr></thead><tbody>";

	$datamensaje['html_titulo_detalle_excel'] .= "<center><table id='tableListaArticulos' style='display:none;'>";
	$datamensaje['html_titulo_detalle_excel'] .= "<thead><tr><th>Codigo</th><th>Nombre_generico</th><th>Nombre_comercial</th><th>UN</th><th>POS</th><th>Precio_regulado</th><th>Registro_Invima</th><th>Grupo</th><th>Cum</th><th>Tarifa</th><th>Clase_articulo</th><th>Fecha_codificacion</th></tr></thead><tbody>";
	$j = 0;
	while($row = mysql_fetch_array($res)){

		$array_articulos[$row['Logcodart']]['Artgen'] = limpiarString($array_articulos[$row['Logcodart']]['Artgen']);
		$array_articulos[$row['Logcodart']]['Artcom'] = limpiarString($array_articulos[$row['Logcodart']]['Artcom']);
		$array_articulos[$row['Logcodart']]['Artreg'] = limpiarString($array_articulos[$row['Logcodart']]['Artreg']);

		$class2 = "class='fila".(($j%2)+1)." findArticulos'";
		$datamensaje['html_detalle_datos'] .= "	<tr $class2><td>".$row['Logcodart']."</td>
													<td>".utf8_encode($array_articulos[$row['Logcodart']]['Artgen'])."</td>
													<td>".utf8_encode($array_articulos[$row['Logcodart']]['Artcom'])."</td>
													<td>".$array_articulos[$row['Logcodart']]['Artuni']."</td>
													<td>".$array_articulos[$row['Logcodart']]['Artreg']."</td>
													<td align=right>".$row['Logtarifa']." - ".$array_tarifas[$row['Logtarifa']]."</td>
													<td align=right>".number_format($row['Logoldval'], 2)."</td>
													<td align=right>".((is_numeric($row['Lognewval'])) ? number_format($row['Lognewval'], 2) : '')."</td>
													<td align=right>".(($row['Logincos'] == 'on') ? 'Si' : 'No')."</td>
													<td align=right>".((is_numeric($row['Logoldfin'])) ? number_format($row['Logoldfin'], 2) : '')."</td>
												</tr>";

		$clase_articulo = '';
		if($array_articulos[$row['Logcodart']]['Artesm'] == 'on'){

			$clase_articulo = "Medicamento";

		}elseif($array_articulos[$row['Logcodart']]['Artesm'] == 'off'){

			$clase_articulo = "Dispositivo";

		}

		$artpos = '';
		$regulado = '';
		$grupo_aux = '';
		$grupo = '';

		if($array_articulos[$row['Logcodart']]['Artpos'] == 'P'){

			$artpos = "P";

		}elseif($array_articulos[$row['Logcodart']]['Artpos'] == 'N'){

			$artpos = "N";

		}elseif($array_articulos[$row['Logcodart']]['Artpos'] == 'O'){

			$artpos = "O";

		}

		if(array_key_exists($row['Logcodart'], $array_regulados) && count($array_regulados[$row['Logcodart']]) > 0){
			$regulado = "SI";
		}else{
			$regulado = "NO";
		}

		$grupo_aux = explode("-", $array_articulos[$row['Logcodart']]['Artgru']);
		$grupo = $grupo_aux[0];

		$datamensaje['html_detalle_datos_excel'] .= "<tr><td>".$row['Logcodart']."</td><td>".utf8_encode($array_articulos[$row['Logcodart']]['Artgen'])."</td><td>".utf8_encode($array_articulos[$row['Logcodart']]['Artcom'])."</td><td>".$array_articulos[$row['Logcodart']]['Artuni']."</td><td>".$artpos."</td><td>".$regulado."</td><td>".$array_articulos[$row['Logcodart']]['Artreg']."</td><td>".$grupo."</td><td>".$array_articulos[$row['Logcodart']]['Artcum']."</td><td align=right>".number_format($row['Lognewval'], 2)."</td><td>".$clase_articulo."</td><td>".consultar_fecha_codif($row['Logcodart'])."</td></tr>";
		$j++;

	}

	$datamensaje['html_detalle_datos'] .= "</tbody></table></center>";

	$datamensaje['html_detalle'] = $datamensaje['html_detalle_boton'].$datamensaje['html_titulo_detalle'].$datamensaje['html_detalle_datos'];
	$datamensaje['html_detalle_excel'] = $datamensaje['html_titulo_detalle_excel'].$datamensaje['html_detalle_datos_excel'];

	echo json_encode($datamensaje);

	return;

}

function traer_listado_log(){

	global $conex;
	global $wbasedato;
	global $wemp_pmla;
	global $wbasedato_cliame;
	$empresas = consultarEmpresas();
	$respuesta = array("html" => "");

	$query = "SELECT *
			    FROM ".$wbasedato_cliame."_000277
			   WHERE Logest = 'on'
			ORDER BY id desc";
	$res = mysql_query($query,$conex) or die("Error en el query: ".$q_medicamento."<br>Tipo Error:".mysql_error());

	$respuesta['html'] .=  "<table id='tableLista'>";
	$respuesta['html'] .=  "<tr class=encabezadoTabla><td colspan='23' align=center>LISTA DE INCREMENTOS REALIZADOS</td></tr>";
	$respuesta['html'] .=  "<tr class=encabezadoTabla><td>Fecha y Hora</td><td>Nit Entidad</td><td>Fecha inicio incremento</td><td>Grupo Articulos Inicial</td><td>Grupo Articulos Final</td><td>Grupos Excluidos</td><td>Grupos Incluidos</td><td>Código Inicial</td><td>Código final</td><td>Codigos Excluidos</td><td>Codigos Incluidos</td><td>Regulados</td><td>Pareto</td><td>POS</td><td>Tipo</td><td>Articulo IDC</td><td>Tarifa</td><td>Redondear</td><td>% Incremento</td><td>Valor(Tarifa fijada)</td><td>Valor adicional</td><td>Basado en costo</td><td>Observaciones</td></tr>";
	$respuesta['html_encabezado'] =  "<thead><tr><th>Fecha y Hora</th><th>Nit Entidad</th><th>Fecha inicio incremento</th><th>Grupo Articulos Inicial</th><th>Grupo Articulos Final</th><th>Grupos Excluidos</th><th>Grupos Incluidos</th><th>Código Inicial</th><th>Código final</th><th>Codigos Excluidos</th><th>Codigos Incluidos</th><th>Regulados</th><th>Pareto</th><th>POS</th><th>Tipo</th><th>Articulo IDC</th><th>Tarifa</th><th>Redondear</th><th>% Incremento</th><th>Valor(Tarifa fijada)</th><th>Valor adicional</th><th>Observaciones</th></tr>";
	$j = 0;
	while($row = mysql_fetch_array($res)){
		$class2 = "class='fila".(($j%2)+1)." find'";
		$grupos_excluidos = '';
		$grupos_incluidos = '';
		$observaciones = '';

		if(trim($row['Logexg']) != ''){

			$grupos_excluidos = "<span class='tooltip'>&nbsp;&nbsp;<img src='../../images/medical/root/info.png' width=11 heigth=11><span class='tooltiptext' style='text-align:left;'>".nl2br($row['Logexg'])."</span>";

		}

		if($row['Loging'] != ''){

			$grupos_incluidos = "<span class='tooltip'>&nbsp;&nbsp;<img src='../../images/medical/root/info.png' width=11 heigth=11><span class='tooltiptext' style='text-align:left;'>".nl2br($row['Loging'])."</span>";

		}
		
		if($row['Logcoi'] != ''){
			
			$codigos_incluidos = "<span class='tooltip'>&nbsp;&nbsp;<img src='../../images/medical/root/info.png' width=11 heigth=11><span class='tooltiptext' style='text-align:left;'>".nl2br($row['Logcoi'])."</span>";
		}
		
		if($row['Logcex'] != ''){
			
			$codigos_excluidos = "<span class='tooltip'>&nbsp;&nbsp;<img src='../../images/medical/root/info.png' width=11 heigth=11><span class='tooltiptext' style='text-align:left;'>".nl2br($row['Logcex'])."</span>";
		}
		
		if($row['Logtip']=='on'){
			$row['Logtip'] = 'Medicamento';
		}elseif($row['Logtip']=='off'){
			$row['Logtip'] = 'Dispositivo';
		}

		if($row['Logreg']=='S'){
			$row['Logreg'] = 'Si';
		}elseif($row['Logreg']=='N'){
			$row['Logreg'] = 'No';
		}

		if($row['Logpar']=='S'){
			$row['Logpar'] = 'Si';
		}elseif($row['Logpar']=='N'){
			$row['Logpar'] = 'No';
		}

		if($row['Logpos']=='S'){
			$row['Logpos'] = 'Si';
		}elseif($row['Logpos']=='N'){
			$row['Logpos'] = 'No';
		}

		if($row['Logobs'] != ''){

			$observaciones = "<span class='tooltip'>&nbsp;&nbsp;<img src='../../images/medical/root/info.png' width=11 heigth=11><span class='tooltiptext' style='text-align:left;'>".$row['Logobs']."</span>";

		}

		$inc_sobre_costo = ($row['Logico'] == 'on') ? 'Si': 'No';
		$respuesta['html'] .=  "<tr $class2 style='cursor:pointer;text-align:center;' onclick='ver_detalle(\"".$row['id']."\")'><td>".$row['Fecha_data']." ".$row['Hora_data']."</td><td>".trim($row['Lognit'])." - ".$empresas[trim($row['Lognit'])]."</td><td>".$row['Logfin']."</td><td>".$row['Loggin']."</td><td>".$row['Loggfi']."</td><td>".$grupos_excluidos."</td><td>".$grupos_incluidos."</td><td>".$row['Logcin']."</td><td>".$row['Logcfi']."</td><td>".$codigos_excluidos."</td><td>".$codigos_incluidos."</td><td>".$row['Logreg']."</td><td>".$row['Logpar']."</td><td>".$row['Logpos']."</td><td>".$row['Logtip']."</td><td>".$row['Logidc']."</td><td>".$row['Logtar']."</td><td>".$row['Logred']."</td><td>".$row['Loginp']."</td><td>".$row['Loginv']."</td><td>".$row['Logiva']."</td><td>".$inc_sobre_costo."</td><td>".$observaciones."</td></tr>";
		$j++;
	}

	$respuesta['html'] .=  "</table>";

	return $respuesta;


}

//Funcion que retorna la lista de grupos de medicamentos
function consultarGrupos(){

	global $conex;
	global $wbasedato;
	global $wemp_pmla;
	global $conexUnix;

	$caracteres =  array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü");
	$caracteres2 = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U");

	$query = "SELECT grucod, grunom
				FROM ivgru
			ORDER BY grucod";
	$err_o = odbc_do($conexUnix,$query) or die (odbc_errormsg());

	$arreglo = array();

	 while ($row = odbc_fetch_array($err_o))
		{

		$row['grunom'] = str_replace( $caracteres, $caracteres2, $row['grunom'] );
		$row['grunom'] = utf8_decode( $row['grunom'] );
		array_push($arreglo, trim($row['grucod']).' - '.trim($row['grunom']) );


		}

	return $arreglo;
}

function consultar_fecha_codif($cod_art){

	global $conexUnix;

	$query = "SELECT artfec
				FROM ivart
			   WHERE artcod = '".$cod_art."'";
	$err_o = odbc_do($conexUnix,$query) or die (odbc_errormsg());
	$row = odbc_fetch_array($err_o);

	return $row['artfec'];


}

function consultarRegulados(){

	global $conex;
	global $wbasedato_cliame;
	global $wemp_pmla;

	$caracteres =  array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü");
	$caracteres2 = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U");

	$arr_medicamento_reg = array();

	$q_medicamento_reg = "SELECT *
					        FROM {$wbasedato_cliame}_000270
					       WHERE Regest = 'on'";
	$r_medicamento_reg = mysql_query($q_medicamento_reg,$conex) or die("Error en el query: ".$q_medicamento_reg."<br>Tipo Error:".mysql_error());

	while($row_medicamento_reg = mysql_fetch_array($r_medicamento_reg))
	{
		$arr_medicamento_reg[trim($row_medicamento_reg['Regcod'])] = $row_medicamento_reg;

	}

	return $arr_medicamento_reg;

}

function consultarArticulos(){

	global $conex;
	global $wbasedato;
	global $wemp_pmla;

	$caracteres =  array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü");
	$caracteres2 = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U");

	$arr_medicamento = array();

	$wbasedato_movhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

	$q_medicamento= "SELECT *
					   FROM {$wbasedato_movhos}_000026
					  WHERE Artest = 'on'";
	$r_medicamento = mysql_query($q_medicamento,$conex) or die("Error en el query: ".$q_medicamento."<br>Tipo Error:".mysql_error());

	while($row_medicamento = mysql_fetch_array($r_medicamento))
	{
		$arr_medicamento[trim($row_medicamento['Artcod'])] = $row_medicamento;

	}

	return $arr_medicamento;

}


//Funcion que retorna la lista de tarifas
function consultarTarifas(){

	global $conex;
	global $wbasedato;
	global $wemp_pmla;
	global $conexUnix;

	$caracteres =  array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü");
	$caracteres2 = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U");

	$query = "SELECT tarcod, tarnom
				FROM intar
			ORDER BY tarcod ";
	$err_o = odbc_do($conexUnix,$query) or die (odbc_errormsg());

	$arreglo = array();

	 while ($row = odbc_fetch_array($err_o))
		{

		$row['tarnom'] = str_replace( $caracteres, $caracteres2, $row['tarnom'] );
		$row['tarnom'] = utf8_decode( $row['tarnom'] );
		$arreglo[trim($row['tarcod'])] = trim($row['tarnom']);


		}

	return $arreglo;
}

//Funcion que retorna la lista de entidades responsables
function consultarEmpresas(){

	global $conex;
	global $wbasedato;
	global $wemp_pmla;
	global $conexUnix;

	$caracteres =  array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü");
	$caracteres2 = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U");

	$query = "SELECT empnit, empnom
				FROM inemp ";
	$err_o = odbc_do($conexUnix,$query) or die (odbc_errormsg());

	$arreglo = array();

	 while ($row = odbc_fetch_array($err_o))
		{

		$row['empnom'] = str_replace( $caracteres, $caracteres2, $row['empnom'] );
		$row['empnom'] = utf8_decode( $row['empnom'] );
		$arreglo[trim($row['empnit'])] = trim($row['empnom']);


		}

	return $arreglo;
}


?>

<html>
<head>
  <meta content="text/html; charset=UTF8" http-equiv="content-type">
  <title>Reporte Incremento Tarifas</title>
	<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
	<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
	<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />

	<link type="text/css" href="../../../include/root/smartpaginator.css" rel="stylesheet" /> <!-- Autocomplete -->
	<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />

	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
	<script src="../../../include/root/modernizr.custom.js" type="text/javascript"></script>
	<script type='text/javascript' src='../../../include/root/jquery.blockUI.min.js'></script>
	<link rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.css" />
	<link rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.filter.css" />
	<script type="text/javascript" src="../../../include/root/jquery.multiselect.js"></script>
	<script type="text/javascript" src="../../../include/root/jquery.multiselect.filter.js"></script>
	<script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>
	<script type="text/javascript" src="../../../include/root/prettify.js"></script>
	<script type='text/javascript' src='../../../include/root/smartpaginator.js'></script>	<!-- Autocomplete -->
	<script type='text/javascript' src='../../../include/root/jquery.tooltip.js'></script>
	<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
	<script type="text/javascript" src="../../../include/root/shieldui-all.min.js"></script>
	<script type="text/javascript" src="../../../include/root/jszip.min.js"></script>

	<script type="text/javascript" >
	function soloNumeros(e){

	var key = window.Event ? e.which : e.keyCode
	return ((key >= 48 && key <= 57) || (key==8) || (key==46))

	}

$(function() {

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

	var start = new Date();

	inicarAcordeon();

	$('#buscar').quicksearch('#tableLista .find');

});

function moverseA(idDelElemento) {
    location.hash = "#" + idDelElemento;
}

function inicarAcordeon()
	{
		$("#accordionDetalle").accordion({
			// heightStyle: "auto"
			heightStyle: "content"
		});
	}

function blockUI()
	{
		$.blockUI({
			message: "<div style='background-color: #111111;color:#ffffff;font-size: 15pt;'><img width='19' heigth='19' src='../../images/medical/ajax-loader3.gif'>&nbsp;&nbsp;Consultando...</div>",
			css:{"border": "2pt solid #7F7F7F"}
		});
	}

function devolver_incremento(id){


		var r = confirm("¿Desea devolver estos articulos a esta tarifa?");

		if (r == true) {

			$.ajax({
					url: "repIncrementoTarifas.php",
					type: "POST",
					data:{

						wemp_pmla		: $("#wemp_pmla").val(),
						consultaAjax 	: '',
						operacion 		: 'devolver_incremento',
						id				: id


					},
					dataType: "json",
					async: true,
					success:function(data_json) {

						if (data_json.error == 1)
						{

						}
						else{
							$("#btndevolver").attr("disabled","disabled");
							alert(data_json.mensaje);
						}
					}
				});

		}

}

function ver_detalle(id){

	blockUI();



	$.ajax({
			url: "repIncrementoTarifas.php",
			type: "POST",
			data:{

				wemp_pmla		: $("#wemp_pmla").val(),
				consultaAjax 	: '',
				operacion 		: 'ver_detalle',
				id				: id


			},
			dataType: "json",
			async: true,
			success:function(data_json) {
				if (data_json.error == 1)
				{

				}
				else{
					moverseA("contenido");
					$.unblockUI();
					$("#mostrar_encabezado").html(data_json.html_encabezado);
					$("#mostrar_detalle").html(data_json.html_detalle);
					$("#mostrar_detalle_excel").html(data_json.html_detalle_excel);
					$("#boton_devolver").html(data_json.boton_devolver);
				}
			}

		}).done(function(){

			$('#buscar_articulo').quicksearch('#tableListaArticulos .findArticulos');

			$("#exportButton").click(function () {

			$("#esperar").show();
			$("#esperar").html('<img src="../../images/medical/ajax-loader5.gif" >');

            var dataSource = shield.DataSource.create({
                data: "#tableListaArticulos",
                schema: {
                    type: "table",
                    fields: {
                        Codigo: { type: String },
                        Nombre_generico: { type: String },
                        Nombre_comercial: { type: String },
                        UN: { type: String },
                        POS: { type: String },
                        Precio_regulado: { type: String },
                        Registro_Invima: { type: String },
                        Cum: { type: String },
                        Grupo: { type: String },
                        Tarifa: { type: String },
                        Clase_articulo: { type: String },
                        Fecha_codificacion: { type: String }
                    }
                }
            });

            // when parsing is done, export the data to Excel
            dataSource.read().then(function (data) {
                new shield.exp.OOXMLWorkbook({
                    author: "ClinicaLasAmericas",
                    worksheets: [
                        {
                            name: "ListaIncrementados",
                            rows: [
                                {
                                    cells: [
                                        {
                                            style: {
                                                bold: true
                                            },
                                            type: Number,
                                            value: "Codigo"
                                        },
                                        {
                                            style: {
                                                bold: true
                                            },
                                            type: String,
                                            value: "Nombre_generico"
                                        },
										{
                                            style: {
                                                bold: true
                                            },
                                            type: String,
                                            value: "Nombre_comercial"
                                        },
										{
                                            style: {
                                                bold: true
                                            },
                                            type: String,
                                            value: "UN"
                                        },
										{
                                            style: {
                                                bold: true
                                            },
                                            type: String,
                                            value: "POS"
                                        },
										{
                                            style: {
                                                bold: true
                                            },
                                            type: Number,
                                            value: "Regulado"
                                        },
										{
                                            style: {
                                                bold: true
                                            },
                                            type: String,
                                            value: "Registro_Invima"
                                        },
										{
                                            style: {
                                                bold: true
                                            },
                                            type: Number,
                                            value: "Grupo"
                                        },
										{
                                            style: {
                                                bold: true
                                            },
                                            type: String,
                                            value: "Cum"
                                        },
										{
                                            style: {
                                                bold: true
                                            },
                                            type: String,
                                            value: "Tarifa"
                                        },
										{
                                            style: {
                                                bold: true
                                            },
                                            type: String,
                                            value: "Clase_articulo"
                                        },
										{
                                            style: {
                                                bold: true
                                            },
                                            type: String,
                                            value: "Fecha_codificacion"
                                        }
                                    ]
                                }
                            ].concat($.map(data, function(item) {
                                return {
                                    cells: [
                                        { type: Number, value: item.Codigo },
                                        { type: String, value: item.Nombre_generico },
                                        { type: String, value: item.Nombre_comercial },
                                        { type: String, value: item.UN },
                                        { type: String, value: item.POS },
                                        { type: Number, value: item.Precio_regulado },
                                        { type: String, value: item.Registro_Invima },
                                        { type: Number, value: item.Grupo },
                                        { type: String, value: item.Cum },
                                        { type: String, value: item.Tarifa },
                                        { type: String, value: item.Clase_articulo },
                                        { type: String, value: item.Fecha_codificacion }
                                    ]
                                };
                            }))
                        }
                    ]
                }).saveAs({
                    fileName: "ReporteExcelEmpresas"
                });

				$("#esperar").hide();

            });
        });



		});


}


</script>
</head>
<style type="text/css">
/* ToolTip classses */
.tooltip {
display: inline-block;
}
.tooltip .tooltiptext {
    margin-left:9px;
    width : 500px;
    visibility: hidden;
    background-color: #FFF;
    border-radius:4px;
    border: 1px solid #aeaeae;
    position: absolute;
    z-index: 1;
    padding: 5px;
    margin-top : -15px; /* according to application */
    opacity: 0;
    transition: opacity 1s;
}

.tooltip:hover .tooltiptext {
    visibility: visible;
    opacity: 1;
}

.ui-multiselect { height:25px; overflow-x:hidden; padding:2px 0 2px 4px; text-align:left; font-size:10pt }

.fila1 {
    background-color: #C3D9FF;
    color: #000000;
    font-size: 8pt;
    padding: 1px;
    font-family: verdana;
}

.fila2 {
    background-color: #E8EEF7;
    color: #000000;
    font-size: 8pt;
    padding: 1px;
    font-family: verdana;
}

#div_content{
    /*border: 2px solid #e0e0e0;*/
    /*background-color: #e6e6e6;*/
    /*border-top: 0px;*/
    font-family: Verdana;
    font-size: 11pt;
}

.encabezadoTabla {
    background-color:   #2a5db0;
    color:              #ffffff;
    font-size:          8pt;
    padding:            1px;
    font-family:        verdana;
}

fieldset{
    border: 2px solid #e0e0e0;
}

legend{
    border: 2px solid #e0e0e0;
    border-top: 0px;
    font-family: Verdana;
    background-color: #e6e6e6;
    font-size: 11pt;
}

</style>
<body>

<?php
encabezado( "Reporte incremento tarifas", $actualiz ,"clinica" );

echo '<input type="hidden" id="wemp_pmla" value="'.$wemp_pmla.'">';

echo "
	<div class='ui-tabs ui-widget ui-widget-content ui-corner-all' style='width:1370px;'>
		<fieldset align='center' style='padding:15px;margin:15px;width:350px'>
			<legend class='fieldset'>Incrementos realizados</legend>
			<table width='100%'>
				<tr>
					<td align='left'>
						<span style='font-family: verdana;font-weight:bold;font-size: 10pt;'>
							Buscar:
							<input id='buscar' class='bordeRed' placeholder='Digite palabra clave' type='text'>
						</span>
					</td>
				</tr>
			</table>
			<div id='listaDeglosas'>";
				 $datosLog = traer_listado_log();
				 echo $datosLog["html"];

	echo "
			</div>
		</fieldset>
	</div>
	<br>
	<div id='accordionDetalle' align='center'>
		<h1 style='font-size: 11pt;' align='left'>&nbsp;&nbsp;&nbsp;Detalle articulos incrementados</h1>
		<div align='center' id='contenido' style=''>
			<fieldset align='center' style='padding:15px;margin-top:15px;'>
				<legend class='fieldset'>Encabezado</legend>
				<div id='mostrar_encabezado'></div>
			</fieldset><br>
			<div id='boton_devolver' align='center'></div>
			<fieldset align='center' style='padding:15px;margin-bottom:15px'>
				<legend class='fieldset'>Detalle</legend>
				<div id='mostrar_detalle'></div>
				<div id='mostrar_detalle_excel' style='display:none;'></div>
			</fieldset>
		</div>
	</div>
	<br>";
?>
<br>
</body>
</html>
