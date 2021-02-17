<?php
/*
Descripcion

Este programa se realiza con la finalidad de realizar incrementos para medicamentos y dispositivos en unix, utilizando una interfaz en la cual
se asignan filtros, generando un listado que les permite con una accion, actualizar estos articulos.

Creador : Jonatan Lopez Aguirre

*/ $actualiz = "2021-02-01"; /*
 ACTUALIZACIONES:
  * 2021-02-01:  Leandro Meneses, se modifica la instrucción ini_set('memory_limit', '2048M') ya que hay querys que pueden sobrepasar el limite de la memoria
				que a hoy está en 1024M.
 * Julio 10 de 2019 Camilo Zapata:
 * 2020-01-24:  Jerson, se coloca la instrucción ini_set('memory_limit', '1024M') ya que hay querys que pueden sobrepasar el limite de la memoria
				que a hoy está en 512M.
 * Julio 10 de 2019 Camilo Zapata:
   Se corrige el valor del costo en la tabla que se toma como base para generar el archivo de excel, puesto que estaba omitiendo el valor del iva. buscar "2019-07-10" en caso de ser necesario.
 * Febrero 8 de 2018 Jonatan
   Se actualiza el campo arttarumo y arttarfmo en la tabla ivarttar cuando se actualizan las tarifas.
 ==========================================================================================
 * Mayo 3 de 2017 Jonatan:
	Se agregan 5 alertas:
	Articulos que no se han comprado en los ultimos 2 años.
	Articulos creados en los últimos 6 meses.
	Articulos cuya tarifa de facturación es igual al costo.
	Articulos cuya tarifa de facturación esta por debajo del costo.
	Articulos cuyo margen de rentabilidad es menor al 10 %

	Ademas se cambia el calculo cuando se selecciona basado en el costo.

	Todas los articulos listados en las alertas se pueden excluir.
 ==========================================================================================
 * Noviembre 17 de 2017 Jonatan: Se permite adjuntar un archivo CSV que contendra un listado de articulos para incrementar, este archivo
									puede contener incrementos por valor o por porcentaje.
 ==========================================================================================
 * Febrero 15 2017 Edwar Jaramillo:
    - Se crean los campos "Incremento basado en costo" para determinar si el incremento tanto porcentaje o valor adicional se calcular con
    	el valor de tarifa o el valor de costo de compra para el artículo, si el campo "Incremento basado en costo" está chequeado entonces
    	el incremento se hace sobre el costo de compra.
	- Actualización de estilos.
	- Se crea objeto json para compartir los mismos datos con la operación "mostrar_art" sin repetir todos los parámetros en cada llamado.
	- Validación de sesión activa en los llamados ajax.
**/
include ("conex.php");
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

  mysql_select_db("matrix");
  include("root/comun.php");

  $wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
  conexionOdbc($conex, $wbasedato, $conexUnix, 'facturacion');
  $conexUnix = odbc_pconnect('facturacion','informix','sco') or die("No se ralizo Conexion con Unix");
  $conexUnixInventarios = odbc_pconnect('inventarios','informix','sco') or die("No se ralizo Conexion con Unix");
  $wbasedato_cliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');


if(isset($operacion) && $operacion == 'validar_archivo'){

		$datamensaje = array('mensaje'=>'', 'error'=>0 );
		$wfecha = date('Y-m-d');
		$whora = date('H:i:s');
		$entidad = trim($entidad);
		$error_validacion = "off";

		$datosArticulos = consultarArticulos();

		if ($_FILES['csv']['size'] > 0) {

			$query = "SELECT Logarc
						FROM ".$wbasedato_cliame."_000277
					   WHERE Lognit = '".trim($entidad)."'
						 AND Fecha_data = '".$wfecha."'
						 AND Logtar = '".$tarifa."'
						 AND Logarc = 'on'";
			$err_o = mysql_query($query, $conex) or die("Error en el query: ".$query."<br>Tipo Error:".mysql_error());
			$num_reg = mysql_num_rows($err_o);

			if($num_reg > 0 and $continuar == 'undefined'){

				$error_validacion = "ya_subio_hoy";

			}

			$csv = $_FILES['csv']['tmp_name'];

			$datos_archivo = fopen($csv,'r');
			$datos_archivo1 = fopen($csv,'r');


			$datamensaje_validacion = "";
			$error_validacion = "";

			while ($data = fgetcsv($datos_archivo,1000,";","'")){

				if ($data[0] != '') {

					$conteo = 0;

					$query = " SELECT count(*) as conteo
							     FROM ivart, ivarttar, inemp
								WHERE artcod = arttarcod
								  AND artact = 'S'
								  AND arttartar = '".$tarifa."'
								  AND empnit = '".trim($entidad)."'
								  AND emptar = arttartar
								  AND empact = 'S'
								  AND artcod = '".$data[0]."'";
					$err_o = odbc_do($conexUnix,$query) or die (odbc_errormsg());
					if(odbc_fetch_row($err_o)) {
						$conteo = trim(odbc_result($err_o,'conteo'));
						//Se valida que el articulo exista
						if($conteo == 0 and $continuar == 'undefined'){

									//echo $data[0]."/".$conteo1."*".$conteo."/-";
								$class = "class='fila".(($j%2)+1)."'";
								$nombre_gen = $datosArticulos[$data[0]]['Artgen'];
								$datamensaje['error'] = 1;

								if($data[1] == ''){
									$tarifa_valor = '';
								}else{
									$tarifa_valor = number_format($data[1],2);
								}

								$datamensaje_validacion .= "<tr $class><td>".$data[0]."</td><td>".utf8_encode($nombre_gen)."</td><td align=center>".$tarifa_valor."</td><td><font color='red'>El articulo no tienen relación con la tarifa y la entidad.</font></td></tr>";
								$error_validacion = "error";

						}
					}

					//Se valida que el articulo tenga valor asignado
					if($data[1] <= 0){

							$class = "class='fila".(($j%2)+1)."'";
							$nombre_gen = $datosArticulos[$data[0]]['Artgen'];
							$datamensaje_validacion .= "<tr $class><td>".$data[0]."</td><td>".utf8_encode($nombre_gen)."</td><td align=center></td><td><font color='#DF7401'>El articulo no tienen valor asignado.</font></td></tr>";
							$error_validacion = "error";

					}

					//Si el incremento es basado en el porcentaje se valida que ninguno supere el 100 porciento.
					if($radio_tipo_porcentaje != '' and $radio_tipo_porcentaje != 'undefined'){

						if($data[1] > 100 and $continuar == 'undefined'){

							$class = "class='fila".(($j%2)+1)."'";
							$nombre_gen = $datosArticulos[$data[0]]['Artgen'];
							$datamensaje_validacion .= "<tr $class><td>".$data[0]."</td><td>".utf8_encode($nombre_gen)."</td><td align='center'>".$data[1]." %</td><td><font color='blue'>El articulo tiene porcentaje de incremento mayor a 100 porciento.</font></td></tr>";
							$error_validacion = "error";
						}


					}
				}

				$j++;
			}


			switch($error_validacion){

				case 'error' :

						$datamensaje['error'] = 1;
						$datamensaje['mensaje'] = "Los siguientes articulos tienen errores favor revisar la columna error.<br>";
						$datamensaje['html'] .= "<table border='1' cellpadding='0' cellspacing='0'>";
						$datamensaje['html'] .= "<tr class='encabezadoTabla'><td align='center'>Código</td><td align='center'>Nombre</td><td align='center'>Tarifa asociada</td><td align=center>Error</td></tr>".$datamensaje_validacion;
						$datamensaje['html'] .= "</table>";
						echo json_encode($datamensaje);
						return;

				break;

				default:

						$incremento_sobre_costo = 'off';

						if($radio_tipo_porcentaje != '' and $radio_tipo_porcentaje != 'undefined'){

							if($radio_tipo_porcentaje == 'costo'){
								$incremento_sobre_costo = 'on';
							}
						}

						$log = "INSERT INTO ".$wbasedato_cliame."_000277 (       Medico           ,   Fecha_data    ,   Hora_data    ,       Lognit  ,           Logfin         ,         Loggin 	      ,      Loggfi       ,           Logexg        ,            Loging       ,        Logcin          ,       Logcfi         ,      Logcex              ,       Logcoi              ,      Logreg    ,     Logpar   ,    Logpos ,       Logtip        ,        Logidc      ,     Logtar   ,     Logred     ,       Loginp          ,      Loginv           ,        Logiva				 		, Logico 						,Logobs      ,    Logest,  Logarc, Seguridad ) "
												."                 VALUES ('".$wbasedato_cliame."','" . $wfecha . "','" . $whora . "', '".$entidad."','" . $fecha_inicio_inc . "','" . $grupo_inicial . "','". $grupo_final."','" .$grupos_excluidos. "','" .$grupos_incluidos. "','" .$codigo_inicial . "','" .$codigo_final. "' ,'".$articulos_excluidos."', '".$articulos_incluidos."', '".$regulado."', '".$pareto."', '".$pos."', '".$tipo_articulo."', '".$articulo_idc."', '".$tarifa."', '".$redondeo."', '".$porc_incremento."', '".$inc_tarifa_fija."', 		'".$inc_valor_adicional."'  , '".$incremento_sobre_costo."'	,'".$observaciones."','on','on','".$_SESSION['user']."')";
						$err = mysql_query($log, $conex) or die (mysql_errno().$log." - ".mysql_error());
						$id = mysql_insert_id();


						while ($data = fgetcsv($datos_archivo1,1000,";","'")){


							if ($data[0]) {

									$query = " SELECT artcos,arttarval,arttarvaa,arttarfec
												 FROM ivart, ivarttar,inemp
												WHERE artcod = arttarcod
												  AND artact = 'S'
												  AND arttartar = '".$tarifa."'
												  AND empnit = '".trim($entidad)."'
												  AND artcod = '".$data[0]."'
												  AND emptar = arttartar
												  AND empact = 'S' ";
									$err_o = odbc_do($conexUnix,$query) or die (odbc_errormsg());
									$arttarval = trim(odbc_result($err_o,'arttarval'));
									$arttarvaa = trim(odbc_result($err_o,'arttarvaa'));
									$arttarfec = trim(odbc_result($err_o,'arttarfec'));
									$artcos = trim(odbc_result($err_o,'artcos'));

									if($radio_tipo_porcentaje != '' and $radio_tipo_porcentaje != 'undefined'){

										if($radio_tipo_porcentaje == 'costo' and $arttarval > 0){

											$incremento = porcentaje($artcos, $data[1], $redondeo, 'on');

											$log = "INSERT INTO ".$wbasedato_cliame."_000272 (        Medico         ,   Fecha_data    ,   Hora_data    , Logconsec,   Logcodart  ,      Logoldval     ,      Lognewval   ,    Logoldvaa   ,   Lognewvaa      ,     Logoldfin    ,     Lognewfin            ,    Logtarifa   ,    Logcosto		,          Logincos				, Logestado	, Seguridad          ) "
																					   ."  VALUES ('".$wbasedato_cliame."','" . $wfecha . "','" . $whora . "', '".$id."','".$data[0]."','" . $arttarval . "','" . $incremento . "','".$arttarvaa."','" .$arttarval. "','" .$arttarfec. "','" .$fecha_inicio_inc . "','" .$tarifa. "' , '".$artcos."'	, '".$incremento_sobre_costo."'	,   'on'    , '".$_SESSION['user']."')";
											$err = mysql_query($log, $conex) or die (mysql_errno().$q." - ".mysql_error());

											$query_update = "UPDATE ivarttar SET arttarval = '".$incremento."', arttarvaa='".$arttarval."', arttarfec = '".$fecha_inicio_inc."', arttarumo = '".$_SESSION['user']."', arttarfmo = '".date('Y-m-d H:i:s')."' WHERE arttarcod = '".$data[0]."' AND arttartar = '".$tarifa."'";
											$err_o = odbc_do($conexUnix,$query_update) or die (odbc_errormsg());

										}elseif($radio_tipo_porcentaje == 'tarifa' and $arttarval > 0){

											$incremento = porcentaje($arttarval, $data[1], $redondeo, 'off');

											$log = "INSERT INTO ".$wbasedato_cliame."_000272 (        Medico         ,   Fecha_data    ,   Hora_data    , Logconsec,   Logcodart  ,      Logoldval     ,      Lognewval   ,    Logoldvaa   ,   Lognewvaa      ,     Logoldfin    ,     Lognewfin            ,    Logtarifa   ,    Logcosto		,          Logincos				, Logestado	, Seguridad          ) "
																					   ."  VALUES ('".$wbasedato_cliame."','" . $wfecha . "','" . $whora . "', '".$id."','".$data[0]."','" . $arttarval . "','" . $incremento . "','".$arttarvaa."','" .$arttarval. "','" .$arttarfec. "','" .$fecha_inicio_inc . "','" .$tarifa. "' , '".$artcos."'	, '".$incremento_sobre_costo."'	,   'on'    , '".$_SESSION['user']."')";
											$err = mysql_query($log, $conex) or die (mysql_errno().$q." - ".mysql_error());

											$query_update = "UPDATE ivarttar SET arttarval = '".$incremento."', arttarvaa='".$arttarval."', arttarfec = '".$fecha_inicio_inc."', arttarumo = '".$_SESSION['user']."', arttarfmo = '".date('Y-m-d H:i:s')."' WHERE arttarcod = '".$data[0]."' AND arttartar = '".$tarifa."'";
											$err_o = odbc_do($conexUnix,$query_update) or die (odbc_errormsg());

										}

									}elseif($valor_fijo != ''){

										$log = "INSERT INTO ".$wbasedato_cliame."_000272 (        Medico         ,   Fecha_data    ,   Hora_data    , Logconsec,   Logcodart  ,      Logoldval     ,      Lognewval   ,    Logoldvaa   ,   Lognewvaa      ,     Logoldfin    ,     Lognewfin            ,    Logtarifa   ,    Logcosto		,          Logincos				, Logestado	, Seguridad          ) "
																			       ."  VALUES ('".$wbasedato_cliame."','" . $wfecha . "','" . $whora . "', '".$id."','".$data[0]."','" . $arttarval . "','" . $data[1] . "','".$arttarvaa."','" .$arttarval. "','" .$arttarfec. "','" .$fecha_inicio_inc . "','" .$tarifa. "' , '".$artcos."'	, '".$incremento_sobre_costo."'	,   'on'    , '".$_SESSION['user']."')";
										$err = mysql_query($log, $conex) or die (mysql_errno().$q." - ".mysql_error());

										$query_update = "UPDATE ivarttar SET arttarval = '".$data[1]."', arttarvaa='".$arttarval."', arttarfec = '".$fecha_inicio_inc."', arttarumo = '".$_SESSION['user']."', arttarfmo = '".date('Y-m-d H:i:s')."' WHERE arttarcod = '".$data[0]."' AND arttartar = '".$tarifa."'";
										$err_o = odbc_do($conexUnix,$query_update) or die (odbc_errormsg());


									}



							}
						}

				break;

			}



		}


	echo json_encode($datamensaje);
	return;

}


if(isset($operacion) && $operacion == 'filtrar_grupos_excluidos'){

	$datamensaje = array('mensaje'=>'', 'error'=>0 , 'formulario'=>'', 'html'=>'', 'html_grupos'=>'');
	$caracteres =  array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü");
	$caracteres2 = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U");

	$codigo_grupo_excluidos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Grupos_medicamentos_no_incluidos');
	$codigo_grupo_excluidos= explode(",",$codigo_grupo_excluidos);
	$in_grupos = implode("','", $codigo_grupo_excluidos);

	$query = "SELECT SUBSTRING_INDEX( artgru, '-', 1 ) as grucod, SUBSTRING_INDEX( artgru, '-', -1 ) as grudes
				FROM ".$wbasedato."_000026
			   WHERE SUBSTRING_INDEX( artgru, '-', 1 ) BETWEEN '".trim($grupo_inicial)."' AND '".trim($grupo_final)."'
				 AND SUBSTRING_INDEX( artgru, '-', 1 ) NOT IN ('".trim($grupo_inicial)."','".trim($grupo_final)."')
				 AND Artest = 'on'
			GROUP BY artgru
			ORDER BY artgru";
	$err_o = mysql_query($query, $conex) or die("Error en el query: ".$query."<br>Tipo Error:".mysql_error());

	$datamensaje['html'] .= "<select id='excluir_grupo' multiselect='multiselect'>";

	 while ($row = mysql_fetch_array($err_o)){

		$row['grudes'] = str_replace( $caracteres, $caracteres2, $row['grudes'] );
		$row['grudes'] = utf8_decode( $row['grudes'] );
		$datamensaje['html'] .= "<option value='".$row['grucod']."'>".$row['grucod']." - ".$row['grudes']."</option>";

	}
	$datamensaje['html'] .= "</select>";

	//Consultar todos los grupos.
	$query = "SELECT SUBSTRING_INDEX( artgru, '-', 1 ) as grucod, SUBSTRING_INDEX( artgru, '-', -1 ) as grudes
				FROM ".$wbasedato."_000026
			   WHERE (Artesm = 'on' OR Artesm = 'off')
			     AND Artest = 'on'
			GROUP BY artgru
			ORDER BY artgru";
	$err_o = mysql_query($query, $conex) or die("Error en el query: ".$query."<br>Tipo Error:".mysql_error());

	$datamensaje['html_grupos'] .= "<select id='incluir_grupo' multiselect='multiselect'>";
	 while ($row_grupos = mysql_fetch_array($err_o))
		{

		$row_grupos['grudes'] = str_replace( $caracteres, $caracteres2, $row_grupos['grudes'] );
		$row_grupos['grudes'] = utf8_decode( $row_grupos['grudes'] );
		$datamensaje['html_grupos'] .= "<option value='".trim($row_grupos['grucod'])."'>".trim($row_grupos['grucod'])." - ".trim($row_grupos['grudes'])."</option>";

		}
	$datamensaje['html_grupos'] .= "</select>";

	echo json_encode($datamensaje);

	return;

}

if(isset($operacion) && $operacion == 'filtrar_codigos_excluidos'){

	$datamensaje = array('mensaje'=>'', 'error'=>0 , 'formulario'=>'');

	if($grupo_inicial != '' and $grupo_final != '')	{

		$filtro_grupos = " AND artgru BETWEEN '".trim($grupo_inicial)."' AND '".trim($grupo_final)."'";

	}

	$query = "SELECT artcod, artnom
				FROM ivart
			   WHERE artcod BETWEEN '".trim($codigo_inicial)."' AND '".trim($codigo_final)."'
			     AND artcod NOT IN ('".trim($codigo_inicial)."','".trim($codigo_final)."')
				 AND artact = 'S'
				 $filtro_grupos
			ORDER BY artcod";
	$err_o = odbc_do($conexUnix,$query) or die (odbc_errormsg());

	 $datamensaje['html'] .= "<select id='excluir_codigo_inicial' multiselect='multiselect'>";
	 while ($row = odbc_fetch_array($err_o)){

		$datamensaje['html'] .= "<option value='".$row['artcod']."'>".$row['artcod']." - ".utf8_encode($row['artnom'])."</option>";

	}
	$datamensaje['html'] .= "</select>";

	echo json_encode($datamensaje);

	return;

}



if(isset($operacion) && $operacion == 'tarifas_empresa'){

	$arreglo = array();

	$caracteres =  array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü");
	$caracteres2 = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U");

	if(trim($entidad) == '*'){

		$entidad = "%";
	}

	$query = "SELECT tarcod, tarnom
				FROM intar, inemp
			   WHERE emptar = tarcod
			     AND empact = 'S'
			     AND taract = 'S'
				 AND empnit LIKE '%".$entidad."%'
			GROUP BY tarcod, tarnom
			ORDER BY tarcod ";
	$err_o = odbc_do($conexUnix,$query) or die (odbc_errormsg());

	$arreglo = array();

	 while ($row = odbc_fetch_array($err_o)){

		if(!array_key_exists(trim($row['tarcod']), $arreglo))
		{

			$row['tarnom'] = str_replace( $caracteres, $caracteres2, $row['tarnom'] );
			$row['tarnom'] = utf8_decode( $row['tarnom'] );
			array_push($arreglo, trim($row['tarcod']).' - '.trim($row['tarnom']) );

		}

	}

	array_push($arreglo, '% - Todas las tarifas' );

	ksort($arreglo);

	echo json_encode($arreglo);

	return;
}

if(isset($operacion) && $operacion == 'cargar_articulos'){

	$arr_medicamento = array();

	$wbasedato_movhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

	$q_medicamento= "SELECT Artcod as codigo, Artcom as nombre
					   FROM {$wbasedato_movhos}_000026
					  WHERE Artest = 'on'";
	$r_medicamento = mysql_query($q_medicamento,$conex) or die("Error en el query: ".$q_medicamento."<br>Tipo Error:".mysql_error());

	while($row_medicamento = mysql_fetch_array($r_medicamento))
	{
		$row_medicamento['nombre'] =  utf8_encode($row_medicamento['nombre']);
		array_push($arr_medicamento, trim($row_medicamento['codigo']).' - '.trim($row_medicamento['nombre']) );

	}

	echo json_encode($arr_medicamento);

	return;
}

if(isset($operacion) && $operacion == 'info_alertas'){

	$wbasedato_movhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	$datosTarifas = datosTarifas();
	$datosArticulos = consultarArticulos();
	$fecha = date('Y-m-d');

	$datamensaje['html_alertas'] .= "<tr class='encabezadotabla'><td>Codigo</td><td>Tarifa</td><td>Nombre generico</td><td>Nombre comercial</td><td>UN</td><td>Registro Invima</td><td>Costo compra</td><td>Tarifa Actual</td><td>Tarifa incremento</td><th>Fecha Codificacion</th></tr>";
	$datamensaje['html_oculto_encab'] .= "<table id='exportTableAlerta'><thead><tr class='encabezadotabla'><th>Codigo</th><th>Tarifa</th><th>Generico</th><th>Comercial</th><th>UN</th><th>Invima</th><th>Costo</th><th>TarifaActual</th><th>TarifaIncremento</th><th>FechaCodificacion</th></tr></thead>";

	$datamensaje['html_oculto_pie'] .= "</tbody></table>";

	//Consulta medicamentos y dispositivos.
	$q_art_dis = "  SELECT * "
				."    FROM ".$wbasedato."_000026 "
				."	 WHERE Artest = 'on'"
				."	   AND (Artesm = 'off' OR Artesm = '' OR Artesm = 'on')";

	//Medicamentos es on y dispositivos es off
	if($tipo_articulo == 'on'){

		//Consulta los codigos de los medicamentos
		$q_art_dis = "  SELECT * "
					."    FROM ".$wbasedato."_000026 "
					."	 WHERE Artest = 'on'"
					."	   AND Artesm = 'on'";

	}elseif($tipo_articulo == 'off'){

		//Consulta los codigos de los dispositivos
		$q_art_dis = "  SELECT * "
					."    FROM ".$wbasedato."_000026 "
					."	 WHERE Artest = 'on'"
					."	   AND (Artesm = 'off' OR Artesm = '')";
	}


	switch ($nro_alerta) {
		case 1: //Articulos que no se han comprado en los ultimos 2 años.

				$saldo = consultarSaldoUnix();

				$dir          = '../../planos/incrementos'; // Directorio archivos planos
				eliminarArchivosDiasAnteriores($dir);

				$nombre_archivo = 'archivo_incrementos_'.date("YmdHis");
				$dir_nombre = $dir.'/'.$nombre_archivo.'.csv';

				$fpDll = fopen($dir_nombre,"a+");

				// $arr_detalle fila de datos
				$arr_titulos = array('Codigo'=>'Codigo','Generico'=>'Generico','Comercial'=>'Comercial','UN'=>'UN','Invima'=>'Invima','Costo'=>'Costo','FechaUltCompra'=>'FechaUltCompra','Saldo'=>'Saldo','FechaCodificacion'=>'FechaCodificacion');
				$arr_titulos = implode(";", $arr_titulos);
				fwrite($fpDll, $arr_titulos."\r\n");
				//$arr_furips = array("furips1"=>array("nombre"=>$nombre_archivo,"url"=>$dir_nombreFurips1);

				$sincomprarAnos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'sincomprarAnos');
				//Articulos que no tienen registro de compra en los ultimos 2 años.
				$fecha_ini_compra = strtotime ( '-'.$sincomprarAnos.' years' , strtotime ( $fecha ) ) ;
				$fecha_ini_compra = date ( 'Y-m-d' , $fecha_ini_compra );

				$query = "SELECT artcod, artnom, arttarval, arttarvaa, arttartar, arttarfec, artcos, artfec, artfco
							FROM ivart, ivarttar
							WHERE artcod = arttarcod
							AND artact = 'S'
						    AND artfco <= '".$fecha_ini_compra."'";
				$err_o = odbc_do($conexUnix,$query) or die (odbc_errormsg());


				while($row = odbc_fetch_array($err_o))
				{
					$array_resultado_alertas[trim(odbc_result($err_o,'artcod'))] = $row;
				}


				//La consulta para esta ejecucion de query esta al inicio de la funcion y es generica.
				$res = mysql_query($q_art_dis,$conex) or die("Error en el query: ".$q."<br>Tipo Error:".mysql_error());

				$array_articulos_medi_dispo = array();

				//Creo un arreglo con medicamentos o dispositivos.
				while($row = mysql_fetch_array($res)){

					$array_articulos_medi_dispo[$row['Artcod']] = $row;

				}


				foreach($array_articulos_medi_dispo as $key => $value){

						if(array_key_exists($key, $array_resultado_alertas)){

							$array_resultado_alertas_aux[$key] = $array_resultado_alertas[$key];

						}

				}

				$array_resultado_alertas_total = $array_resultado_alertas_aux;
				$array_resultado_alertas = array_slice($array_resultado_alertas_aux, 0, 1500);


				$datamensaje['encabezado_alerta1'] .= "<tr class='encabezadotabla'><td>Codigo</td><td>Nombre generico</td><td>Nombre comercial</td><td>UN</td><td>Registro Invima</td><td>Costo compra</td><th nowrap>Fecha Ult. compra</th><th nowrap>Saldo</th><th>Fecha Codificacion</th></tr>";

				foreach($array_resultado_alertas as $cod_art =>$value){

					$datosArticulosGenerico = limpiarString($datosArticulos[$value['artcod']]['Artgen']);
					$datosArticulosComercial = limpiarString($datosArticulos[$value['artcod']]['Artcom']);
					$datosArticulosInvima = limpiarString($datosArticulos[$value['artcod']]['Artreg']);
					//$saldo = consultarSaldoUnix($value['artcod']);

					$class = "class='fila".(($j%2)+1)."'";
					$datamensaje['alerta1'] .= "<tr $class><td>".$value['artcod']."</td><td>".utf8_encode($datosArticulosGenerico)."</td><td>".utf8_encode($datosArticulosComercial)."</td><td>".utf8_encode($datosArticulos[$value['artcod']]['Artuni'])."</td><td>".utf8_encode($datosArticulosInvima)."</td><td align=right>".number_format($value['artcos']*('1.'.$value['artiva']), 2)."</td><td align=center>".$value['artfco']."</td><td align=center>".$saldo[$value['artcod']]['saldo']."</td><td align=center>".$value['artfec']."</td></tr>";

					$j++;

					$datos_alert1[$value['artcod']] = $value['artcod'];

				}

				foreach($array_resultado_alertas_total as $cod_art_total =>$value_total){

					$datosArticulosGenerico = limpiarString($datosArticulos[$value_total['artcod']]['Artgen']);
					$datosArticulosComercial = limpiarString($datosArticulos[$value_total['artcod']]['Artcom']);
					$datosArticulosInvima = limpiarString($datosArticulos[$value_total['artcod']]['Artreg']);

					$class = "class='fila".(($j%2)+1)."'";
					$datamensaje['html_oculto1'] .= "<tr><td>".$value_total['artcod']."</td><td>".utf8_encode($datosArticulosGenerico)."</td><td>".utf8_encode($datosArticulosComercial)."</td><td>".utf8_encode($datosArticulos[$value_total['artcod']]['Artuni'])."</td><td>".utf8_encode($datosArticulosInvima)."</td><td align=right>".number_format($value_total['artcos'],2)."</td><td align=center>".$value_total['artfco']."</td><td align=center>".$saldo[$value['artcod']]['saldo']."</td><td align=center>".$value_total['artfec']."</td></tr>";
					$j++;

					$linea_detalle = $value_total['artcod'].";".utf8_encode($datosArticulosGenerico).";".utf8_encode($datosArticulosComercial).";".utf8_encode($datosArticulos[$value_total['artcod']]['Artuni']).";".utf8_encode($datosArticulosInvima).";".number_format($value_total['artcos'],2).";".$value_total['artfco'].";".$saldo[$value_total['artcod']]['saldo'].";".$value_total['artfec'];
					fwrite($fpDll, $linea_detalle."\r\n");

					$datos_alert1_total[$value_total['artcod']] = $value_total['artcod'];

				}

				if(count($datos_alert1) > 0 ){
					foreach($datos_alert1 as $key1 => $value1){

						$datos_alerta1 .= $key1."\n";

					}

					$datamensaje['html_alerta'] = "<input type='hidden' id='datos_alerta1' value='".$datos_alerta1."'><table><tr><td colspan=10><input type='checkbox' id='excluir_alerta1' onclick='excluir_alerta(\"alerta1\",\"Articulos que no han sido comprados en los ultimos 2 años.\")'><font size=3>Excluir ".number_format(count($datos_alert1_total))." articulos</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' value='Exportar' id='excelAlerta1' onClick='location.href=\"".$dir_nombre."\"'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;El total de articulos de esta es ".number_format(count($datos_alert1_total))." seleccione exportar para ver el listado completo.<br></td></tr>".$datamensaje['encabezado_alerta1'].$datamensaje['alerta1']."</table>";
					$datamensaje['ruta_descarga'] = $dir_nombre;

				}

			break;

		case 2: //Articulos creados en los últimos 6 meses.

				$datamensaje['html_alertas2'] .= "<tr class='encabezadotabla'><td>Codigo</td><td>Nombre generico</td><td>Nombre comercial</td><td>UN</td><td>Registro Invima</td><td>Costo compra</td><th>Fecha Codificacion</th></tr>";
				$datamensaje['html_oculto_encab2'] .= "<table id='exportTableAlerta'><thead><tr class='encabezadotabla'><th>Codigo</th><th>Generico</th><th>Comercial</th><th>UN</th><th>Invima</th><th>Costo</th><th>FechaCodificacion</th></tr></thead>";

				$creadosUltmeses = consultarAliasPorAplicacion($conex, $wemp_pmla, 'creadosUltmeses');
				//Validar registro en el arreglo que cumplan con la siguiente regla:
				//Alerta de artículos que fueron creados en el último semestre, con el fin de no aplicar un doble incremento en un tiempo igual o menor a 6 (seis) meses.
				$nuevafecha = strtotime ( '-'.$creadosUltmeses.' month' , strtotime ( $fecha ) ) ;
				$nuevafecha = date ( 'Y-m-d' , $nuevafecha );

				$query = "SELECT artcod, artnom, arttarval, arttarvaa, arttartar, arttarfec, artcos, artfec, artfco
								FROM ivart, ivarttar
							   WHERE artcod = arttarcod
								 AND artact = 'S'
								 AND artfec >= '".$nuevafecha."'";
				$err_o = odbc_do($conexUnix,$query) or die (odbc_errormsg());
				while($row = odbc_fetch_array($err_o))
				{
					$array_resultado_alertas[trim(utf8_encode(odbc_result($err_o,'artcod')))] = $row;
				}


				//La consulta para esta ejecucion de query esta al inicio de la funcion y es generica.
				$res = mysql_query($q_art_dis,$conex) or die("Error en el query: ".$q."<br>Tipo Error:".mysql_error());

				$array_articulos_medi_dispo = array();

				//Creo un arreglo con medicamentos o dispositivos.
				while($row = mysql_fetch_array($res)){

					$array_articulos_medi_dispo[$row['Artcod']] = $row;

				}


				foreach($array_articulos_medi_dispo as $key => $value){

						if(array_key_exists($key, $array_resultado_alertas)){

							$array_resultado_alertas_aux[$key] = $array_resultado_alertas[$key];

						}

				}

				$array_resultado_alertas = $array_resultado_alertas_aux;

				$datamensaje['html_oculto_encab_alertas2'] .= "<table id='exportTableAlerta2'><thead><tr class='encabezadotabla'><th>Codigo</th><th>Generico</th><th>Comercial</th><th>UN</th><th>Invima</th><th>Costo</th><th>FechaCodificacion</th></tr></thead>";

				foreach($array_resultado_alertas as $cod_art =>$value){

					$datosArticulosGenerico = limpiarString($datosArticulos[$value['artcod']]['Artgen']);
					$datosArticulosComercial = limpiarString($datosArticulos[$value['artcod']]['Artcom']);
					$datosArticulosInvima = limpiarString($datosArticulos[$value['artcod']]['Artreg']);

					$class = "class='fila".(($j%2)+1)."'";
					$datamensaje['alerta2'] .= "<tr $class><td>".$value['artcod']."</td><td>".utf8_encode($datosArticulosGenerico)."</td><td>".utf8_encode($datosArticulosComercial)."</td><td>".utf8_encode($datosArticulos[$value['artcod']]['Artuni'])."</td><td>".utf8_encode($datosArticulosInvima)."</td><td align=right>".number_format($value['artcos']*('1.'.$value['artiva']), 2)."</td><td align=center>".$value['artfec']."</td></tr>";
					$datamensaje['html_oculto2'] .= "<tr><td>".$value['artcod']."</td><td>".utf8_encode($datosArticulosGenerico)."</td><td>".utf8_encode($datosArticulosComercial)."</td><td>".utf8_encode($datosArticulos[$value['artcod']]['Artuni'])."</td><td>".utf8_encode($datosArticulosInvima)."</td><td align=right>".number_format($value['artcos']*('1.'.$value['artiva']), 2)."</td><td align=center>".$value['artfec']."</td></tr>";
					$j++;

					$datos_alert2[$value['artcod']] = $value['artcod'];

				}

				//Alerta de artículos que fueron creados en el último semestre, con el fin de no aplicar un doble incremento en un tiempo igual o menor a 6 (seis) meses.
				if(count($datos_alert2) > 0 ){
					foreach($datos_alert2 as $key2 => $value2){

						$datos_alerta2 .= $key2."\n";

					}

					$datamensaje['html_alerta'] = "<input type='hidden' id='datos_alerta2' value='".$datos_alerta2."'><table><tr><td colspan=10><input type='checkbox' id='excluir_alerta2' onclick='excluir_alerta(\"alerta2\",\"Articulos creados en los últimos 6 meses.\")'><font size=3>Excluir ".count($datos_alert2)." articulos</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' value='Exportar' id='excelAlerta2'><br><br></td></tr>".$datamensaje['html_alertas2'].$datamensaje['alerta2']."</table>";
					$datamensaje['html_oculto_exportar'] = $datamensaje['html_oculto_encab_alertas2'].$datamensaje['html_oculto2'].$datamensaje['html_oculto_pie'];
				}


			break;
		case 3: //Articulos cuya tarifa de facturación es igual al costo.

				if($tarifa == '%'){

					$query = "SELECT artcod, artnom, arttarval, arttarvaa, arttartar, arttarfec, artcos, artfec, artfco
								FROM ivart, ivarttar, inemp
							   WHERE artcod = arttarcod
								 AND artact = 'S'
								 AND emptar = arttartar
								 AND empact = 'S'
								 AND empnit = '".trim($entidad)."'
								 AND arttartar LIKE '%".$tarifa."%' ";

				}else{

					$query = "SELECT artcod, artnom, arttarval, arttarvaa, arttartar, arttarfec, artcos, artfec, artfco
								FROM ivart, ivarttar
							   WHERE artcod = arttarcod
								 AND artact = 'S'
								 AND arttartar LIKE '".$tarifa."' ";
				}

				$err_o = odbc_do($conexUnix,$query) or die (odbc_errormsg());

				while($row = odbc_fetch_array($err_o))
					{

						$array_resultado_alertas_345[trim(utf8_encode(odbc_result($err_o,'artcod')))][trim(odbc_result($err_o,'arttartar'))] = $row;

					}

				//La consulta para esta ejecucion de query esta al inicio de la funcion y es generica.
				$res = mysql_query($q_art_dis,$conex) or die("Error en el query: ".$q."<br>Tipo Error:".mysql_error());

				$array_articulos_medi_dispo = array();

				//Creo un arreglo con medicamentos o dispositivos.
				while($row = mysql_fetch_array($res)){

					$array_articulos_medi_dispo[$row['Artcod']] = $row;

				}


				foreach($array_articulos_medi_dispo as $key => $value){

						if(array_key_exists($key, $array_resultado_alertas_345)){

							$array_resultado_alertas_aux[$key] = $array_resultado_alertas_345[$key];

						}

				}

				$array_resultado_alertas_345 = $array_resultado_alertas_aux;



				$datamensaje['html_oculto_encab3'] .= "<table id='exportTableAlerta3'><thead><tr class='encabezadotabla'><th>Codigo</th><th>Tarifa</th><th>Generico</th><th>Comercial</th><th>UN</th><th>Invima</th><th>Costo</th><th>TarifaActual</th><th>TarifaIncremento</th><th>FechaCodificacion</th></tr></thead>";
				$datamensaje['html_oculto_encab_alertas3'] .= "<table id='exportTableAlerta3'><thead><tr class='encabezadotabla'><th>Codigo</th><th>Tarifa</th><th>Generico</th><th>Comercial</th><th>UN</th><th>Invima</th><th>Costo</th><th>TarifaActual</th><th>TarifaIncremento</th><th>FechaCodificacion</th></tr></thead>";

				foreach($array_resultado_alertas_345 as $cod_art =>$tarifas){

					foreach($tarifas as $cod_tarifa => $value){

						if($value['arttarval'] == $value['artcos']) {

							$valor_insumo = $value['arttarval'];
							if($inc_costo_porcentaje == "on"){
								$valor_insumo = $value['artcos'];
								$incremento_sobre_costo = 'on';
							}

							$incremento = porcentaje($valor_insumo, $porc_incremento, $redondeo, $inc_costo_porcentaje);

							$datosArticulosGenerico = limpiarString($datosArticulos[$value['artcod']]['Artgen']);
							$datosArticulosComercial = limpiarString($datosArticulos[$value['artcod']]['Artcom']);
							$datosArticulosInvima = limpiarString($datosArticulos[$value['artcod']]['Artreg']);

							$class = "class='fila".(($k%2)+1)."'";
							$datamensaje['alerta3'] .= "<tr $class><td>".$value['artcod']."</td><td>".$datosTarifas[$cod_tarifa]."</td><td>".utf8_encode($datosArticulosGenerico)."</td><td>".utf8_encode($datosArticulosComercial)."</td><td>".utf8_encode($datosArticulos[$value['artcod']]['Artuni'])."</td><td>".utf8_encode($datosArticulosInvima)."</td><td align=right>".number_format($value['artcos']*('1.'.$value['artiva']), 2)."</td><td align=right>".number_format($value['arttarval'],2)."</td><td align=right>".number_format($incremento,2)."</td><td align=center>".$value['artfec']."</td></tr>";
							$datamensaje['html_oculto3'] .= "<tr $class><td>".$value['artcod']."</td><td>".$datosTarifas[$cod_tarifa]."</td><td>".utf8_encode($datosArticulosGenerico)."</td><td>".utf8_encode($datosArticulosComercial)."</td><td>".utf8_encode($datosArticulos[$value['artcod']]['Artuni'])."</td><td>".utf8_encode($datosArticulosInvima)."</td><td align=right>".number_format($value['artcos']*('1.'.$value['artiva']), 2)."</td><td align=right>".number_format($value['arttarval'],2)."</td><td align=right>".number_format($incremento,2)."</td><td align=center>".$value['artfec']."</td></tr>";
							$k++;

							$datos_alert3[$value['artcod']] = $value['artcod'];
							$conteo_datos_alert[$cod_tarifa][$value['artcod']] = $value;

						}

					}
				}

				//Alerta de artículos cuya tarifa de facturación es igual al costo (sin margen de rentabilidad)
				if(count($datos_alert3) > 0 ){
					foreach($datos_alert3 as $key3 => $value3){

						$datos_alerta3 .= $key3."\n";

					}

					$datamensaje['html_alerta'] = "<input type='hidden' id='datos_alerta3' value='".$datos_alerta3."'><table><tr><td colspan=10><input type='checkbox' id='excluir_alerta3' onclick='excluir_alerta(\"alerta3\",\"Articulos cuya tarifa de facturación es igual al costo.\")'><font size=3>Excluir ".array_sum(array_map('count',$conteo_datos_alert))." articulos</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' value='Exportar' id='excelAlerta3'><br><br></td></tr>".$datamensaje['html_alertas'].$datamensaje['alerta3']."</table>";
					$datamensaje['html_oculto_exportar'] = $datamensaje['html_oculto_encab3'].$datamensaje['html_oculto3'].$datamensaje['html_oculto_pie'];
				}

			break;
		case 4: //Articulos cuya tarifa de facturación esta por debajo del costo.

				if($tarifa == '%'){

					$query = "SELECT artcod, artnom, arttarval, arttarvaa, arttartar, arttarfec, artcos, artfec, artfco
								FROM ivart, ivarttar, inemp
							   WHERE artcod = arttarcod
								 AND artact = 'S'
								 AND emptar = arttartar
								 AND empact = 'S'
								 AND empnit = '".trim($entidad)."'
								 AND arttartar LIKE '%".$tarifa."%' ";

				}else{

				$query = "SELECT artcod, artnom, arttarval, arttarvaa, arttartar, arttarfec, artcos, artfec, artfco
							FROM ivart, ivarttar
						   WHERE artcod = arttarcod
							 AND artact = 'S'
							 AND arttartar LIKE '".$tarifa."' ";
				}


				$err_o = odbc_do($conexUnix,$query) or die (odbc_errormsg());

				while($row = odbc_fetch_array($err_o))
					{

						$array_resultado_alertas_345[trim(utf8_encode(odbc_result($err_o,'artcod')))][trim(odbc_result($err_o,'arttartar'))] = $row;

					}

				//La consulta para esta ejecucion de query esta al inicio de la funcion y es generica.
				$res = mysql_query($q_art_dis,$conex) or die("Error en el query: ".$q."<br>Tipo Error:".mysql_error());

				$array_articulos_medi_dispo = array();

				//Creo un arreglo con medicamentos o dispositivos.
				while($row = mysql_fetch_array($res)){

					$array_articulos_medi_dispo[$row['Artcod']] = $row;

				}


				foreach($array_articulos_medi_dispo as $key => $value){

						if(array_key_exists($key, $array_resultado_alertas_345)){

							$array_resultado_alertas_aux[$key] = $array_resultado_alertas_345[$key];

						}

				}

				$array_resultado_alertas_345 = $array_resultado_alertas_aux;

				$datamensaje['html_oculto_encab4'] .= "<table id='exportTableAlerta4'><thead><tr class='encabezadotabla'><th>Codigo</th><th>Tarifa</th><th>Generico</th><th>Comercial</th><th>UN</th><th>Invima</th><th>Costo</th><th>TarifaActual</th><th>TarifaIncremento</th><th>FechaCodificacion</th></tr></thead>";
				//Alerta de artículos cuya tarifa de facturación está por debajo del costo (rentabilidad negativa)
				foreach($array_resultado_alertas_345 as $cod_art =>$tarifas){

					foreach($tarifas as $cod_tarifa => $value){

						if($value['arttarval'] < $value['artcos']) {

							$valor_insumo = $value['arttarval'];
							if($inc_costo_porcentaje == "on"){
								$valor_insumo = $value['artcos'];
								$incremento_sobre_costo = 'on';
							}

							$incremento = porcentaje($valor_insumo, $porc_incremento, $redondeo, $inc_costo_porcentaje);

							$datosArticulosGenerico = limpiarString($datosArticulos[$value['artcod']]['Artgen']);
							$datosArticulosComercial = limpiarString($datosArticulos[$value['artcod']]['Artcom']);
							$datosArticulosInvima = limpiarString($datosArticulos[$value['artcod']]['Artreg']);

							$class = "class='fila".(($l%2)+1)."'";
							$datamensaje['alerta4'] .= "<tr $class><td>".$value['artcod']."</td><td>".$datosTarifas[$cod_tarifa]."</td><td>".utf8_encode($datosArticulosGenerico)."</td><td>".utf8_encode($datosArticulosComercial)."</td><td>".utf8_encode($datosArticulos[$value['artcod']]['Artuni'])."</td><td>".utf8_encode($datosArticulosInvima)."</td><td align=right>".number_format($value['artcos']*('1.'.$value['artiva']), 2)."</td><td align=right>".number_format($value['arttarval'],2)."</td><td align=right>".number_format($incremento,2)."</td><td align=center>".$value['artfec']."</td></tr>";
							$datamensaje['html_oculto4'] .= "<tr $class><td>".$value['artcod']."</td><td>".$datosTarifas[$cod_tarifa]."</td><td>".utf8_encode($datosArticulosGenerico)."</td><td>".utf8_encode($datosArticulosComercial)."</td><td>".utf8_encode($datosArticulos[$value['artcod']]['Artuni'])."</td><td>".utf8_encode($datosArticulosInvima)."</td><td align=right>".number_format($value['artcos']*('1.'.$value['artiva']), 2)."</td><td align=right>".number_format($value['arttarval'],2)."</td><td align=right>".number_format($incremento,2)."</td><td align=center>".$value['artfec']."</td></tr>";
							$l++;

							$datos_alert4[$value['artcod']] = $value['artcod'];
							$conteo_datos_alert[$cod_tarifa][$value['artcod']] = $value;

						}
					}
				}

				//Alerta de artículos cuya tarifa de facturación está por debajo del costo (rentabilidad negativa)
				if(count($datos_alert4) > 0 ){
					foreach($datos_alert4 as $key4 => $value4){

						$datos_alerta4 .= $key4."\n";

					}

					$datamensaje['html_alerta'] = "<input type='hidden' id='datos_alerta4' value='".$datos_alerta4."'><table><tr><td colspan=10><input type='checkbox' id='excluir_alerta4' onclick='excluir_alerta(\"alerta4\",\"Articulos cuya tarifa de facturación esta por debajo del costo.\")'><font size=3>Excluir ".array_sum(array_map('count',$conteo_datos_alert))." articulos</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' value='Exportar' id='excelAlerta4'><br><br></td></tr>".$datamensaje['html_alertas'].$datamensaje['alerta4']."</table>";
					$datamensaje['html_oculto_exportar'] = $datamensaje['html_oculto_encab4'].$datamensaje['html_oculto4'].$datamensaje['html_oculto_pie'];
				}


			break;
		case 5: //Articulos cuyo margen de rentabilidad es menor al 10 %.

				if($tarifa == '%'){

					$query = "SELECT artcod, artnom, arttarval, arttarvaa, arttartar, arttarfec, artcos, artfec, artfco
								FROM ivart, ivarttar, inemp
							   WHERE artcod = arttarcod
								 AND artact = 'S'
								 AND emptar = arttartar
								 AND empact = 'S'
								 AND empnit = '".trim($entidad)."'
								 AND arttartar LIKE '%".$tarifa."%' ";

				}else{

				$query = "SELECT artcod, artnom, arttarval, arttarvaa, arttartar, arttarfec, artcos, artfec, artfco
							FROM ivart, ivarttar
						   WHERE artcod = arttarcod
							 AND artact = 'S'
							 AND arttartar LIKE '".$tarifa."' ";
				}


				$err_o = odbc_do($conexUnix,$query) or die (odbc_errormsg());

				while($row = odbc_fetch_array($err_o))
					{

						$array_resultado_alertas_345[trim(utf8_encode(odbc_result($err_o,'artcod')))][trim(odbc_result($err_o,'arttartar'))] = $row;

					}


				$datamensaje['html_oculto_encab_alertas5'] .= "<table id='exportTableAlerta5'><thead><tr class='encabezadotabla'><th>Codigo</th><th>Tarifa</th><th>Generico</th><th>Comercial</th><th>UN</th><th>Invima</th><th>Costo</th><th>TarifaActual</th><th>PorcentajeRentabilidad</th><th>TarifaIncremento</th><th>FechaCodificacion</th></tr></thead>";
				$datamensaje['encabezado_alerta5'] .= "<tr class='encabezadotabla'><td>Codigo</td><td>Tarifa</td><td>Nombre generico</td><td>Nombre comercial</td><td>UN</td><td>Registro Invima</td><td>Costo compra</td><td>Tarifa Actual</td><td>Porcentaje rentabilidad (%)</td><td>Tarifa incremento</td><th>Fecha Codificacion</th></tr>";

				//Alerta de artículos cuyo margen de rentabilidad se encuentre entre el 1 al 10%  (rentabilidad menor al 10%)
				//Formula para la rentabilidad de un articulo:
				// R = (1 - (Costo / Tarifa))*100

				$porcentajeRentabilidad = consultarAliasPorAplicacion($conex, $wemp_pmla, 'porcentajeRentabilidad');

				//La consulta para esta ejecucion de query esta al inicio de la funcion y es generica.
				$res = mysql_query($q_art_dis,$conex) or die("Error en el query: ".$q."<br>Tipo Error:".mysql_error());

				$array_articulos_medi_dispo = array();

				//Creo un arreglo con medicamentos o dispositivos.
				while($row = mysql_fetch_array($res)){

					$array_articulos_medi_dispo[$row['Artcod']] = $row;

				}


				foreach($array_articulos_medi_dispo as $key => $value){

						if(array_key_exists($key, $array_resultado_alertas_345)){

							$array_resultado_alertas_aux[$key] = $array_resultado_alertas_345[$key];

						}

				}

				$array_resultado_alertas_345 = $array_resultado_alertas_aux;


				foreach($array_resultado_alertas_345 as $cod_art =>$tarifas){

					foreach($tarifas as $cod_tarifa => $value){

						$rentabilidad = (1 - ($value['artcos'] / $value['arttarval'])) * 100;

						if($rentabilidad < $porcentajeRentabilidad){

							$valor_insumo = $value['arttarval'];
							if($inc_costo_porcentaje == "on"){
								$valor_insumo = $value['artcos'];
								$incremento_sobre_costo = 'on';
							}

							$incremento = porcentaje($valor_insumo, $porc_incremento, $redondeo, $inc_costo_porcentaje);

							$datosArticulosGenerico = limpiarString($datosArticulos[$value['artcod']]['Artgen']);
							$datosArticulosComercial = limpiarString($datosArticulos[$value['artcod']]['Artcom']);
							$datosArticulosInvima = limpiarString($datosArticulos[$value['artcod']]['Artreg']);

							$class = "class='fila".(($m%2)+1)."'";
							$datamensaje['alerta5'] .= "<tr $class><td>".$value['artcod']."</td><td>".$datosTarifas[$cod_tarifa]."</td><td>".utf8_encode($datosArticulosGenerico)."</td><td>".utf8_encode($datosArticulosComercial)."</td><td>".utf8_encode($datosArticulos[$value['artcod']]['Artuni'])."</td><td>".utf8_encode($datosArticulosInvima)."</td><td align=right>".number_format($value['artcos']*('1.'.$value['artiva']), 2)."</td><td align=right>".number_format($value['arttarval'],2)."</td><td align=center>".round($rentabilidad,2)."</td><td align=right>".number_format($incremento,2)."</td><td align=center>".$value['artfec']."</td></tr>";
							$datamensaje['html_oculto5'] .= "<tr><td>".$value['artcod']."</td><td>".$datosTarifas[$cod_tarifa]."</td><td>".utf8_encode($datosArticulosGenerico)."</td><td>".utf8_encode($datosArticulosComercial)."</td><td>".utf8_encode($datosArticulos[$value['artcod']]['Artuni'])."</td><td>".utf8_encode($datosArticulosInvima)."</td><td align=right>".$value['artcos']."</td><td align=right>".$value['arttarval']."</td><td>".round($rentabilidad,2)."</td><td align=right>".$incremento."</td><td>".$value['artfec']."</td></tr>";

							$m++;
							$datos_alert5[$value['artcod']] = $value['artcod'];
							$conteo_datos_alert[$cod_tarifa][$value['artcod']] = $value;


						}
					}
				}

				//Alerta de artículos cuyo margen de rentabilidad se encuentre entre el 1 al 10%  (rentabilidad menor al 10%)
				if(count($datos_alert5) > 0 ){
					foreach($datos_alert5 as $key5 => $value5){

						$datos_alerta5 .= $key5."\n";

					}

					$datamensaje['html_alerta'] = "<input type='hidden' id='datos_alerta5' value='".$datos_alerta5."'><table><tr><td colspan=10><input type='checkbox' id='excluir_alerta5' onclick='excluir_alerta(\"alerta5\", \"Articulos cuyo margen de rentabilidad es menor al 10 %.\")'><font size=3>Excluir ".array_sum(array_map('count',$conteo_datos_alert))." articulos</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' value='Exportar' id='excelAlerta5'><br><br></td></tr>".$datamensaje['encabezado_alerta5'].$datamensaje['alerta5']."</table>";
					$datamensaje['html_oculto_exportar'] = $datamensaje['html_oculto_encab_alertas5'].$datamensaje['html_oculto5'].$datamensaje['html_oculto_pie'];
				}


			break;
	}

	echo json_encode($datamensaje);

	return;

}

if(isset($operacion) && $operacion == 'mostrar_art'){

	$datamensaje = array('html_oculto'=>'', 'html'=>'', 'mensaje'=>'', 'error'=>0 , 'formulario'=>'');

	global $conex;
	$array_grupos_incluidos    = array();
	$array_articulos_incluidos = array();
	$articulos_filtro          = "";
	$select_grupos_incluidos   = "";
	$select_art_incluidos      = "";
	$grupos_filtro             = "";
	$cant_registros_pagina     = 0;

	$grupos_excluidos_array = explode("\n",$grupos_excluidos);
	foreach($grupos_excluidos_array as $key => $value){
		$codigo_g1 = explode("-",$value);
		if(trim($codigo_g1[0]) != ''){
			$array_grupos_excluidos[trim($codigo_g1[0])] = trim($codigo_g1[0]);
		}
	}

	$grupos_incluidos_array = explode("\n",$grupos_incluidos);
	foreach($grupos_incluidos_array as $key => $value){
		$codigo_g2 = explode("-",$value);
		if(trim($codigo_g2[0]) != ''){
			$array_grupos_incluidos[trim($codigo_g2[0])] = trim($codigo_g2[0]);
		}
	}

	$articulos_excluidos_array = explode("\n",$articulos_excluidos);
	foreach($articulos_excluidos_array as $key => $value){
		$codigo_a1 = explode("-",$value);
		if(trim($codigo_a1[0]) != ''){
			$array_articulos_excluidos[trim($codigo_a1[0])] = trim($codigo_a1[0]);
		}
	}

	//--- Alertas -----
	$articulos_excluidos_alerta1 = explode("\n",$articulos_excluidos_alerta1);
	foreach($articulos_excluidos_alerta1 as $key => $value){
		$codigo_art = explode("-",$value);
		if(trim($codigo_art[0]) != ''){
			$array_articulos_excluidos_alerta1[trim($codigo_art[0])] = trim($codigo_art[0]);
		}
	}

	$articulos_excluidos_alerta2 = explode("\n",$articulos_excluidos_alerta2);
	foreach($articulos_excluidos_alerta2 as $key => $value){
		$codigo_art = explode("-",$value);
		if(trim($codigo_art[0]) != ''){
			$array_articulos_excluidos_alerta2[trim($codigo_art[0])] = trim($codigo_art[0]);
		}
	}



	$articulos_excluidos_alerta3 = explode("\n",$articulos_excluidos_alerta3);
	foreach($articulos_excluidos_alerta3 as $key => $value){
		$codigo_art = explode("-",$value);
		if(trim($codigo_art[0]) != ''){
			$array_articulos_excluidos_alerta3[trim($codigo_art[0])] = trim($codigo_art[0]);
		}
	}



	$articulos_excluidos_alerta4 = explode("\n",$articulos_excluidos_alerta4);
	foreach($articulos_excluidos_alerta4 as $key => $value){
		$codigo_art = explode("-",$value);
		if(trim($codigo_art[0]) != ''){
			$array_articulos_excluidos_alerta4[trim($codigo_art[0])] = trim($codigo_art[0]);
		}
	}



	$articulos_excluidos_alerta5 = explode("\n",$articulos_excluidos_alerta5);
	foreach($articulos_excluidos_alerta5 as $key => $value){
		$codigo_art = explode("-",$value);
		if(trim($codigo_art[0]) != ''){
			$array_articulos_excluidos_alerta5[trim($codigo_art[0])] = trim($codigo_art[0]);
		}
	}

	//------

	$articulos_incluidos_array = explode("\n",$articulos_incluidos);
	foreach($articulos_incluidos_array as $key => $value){
		$codigo_a2 = explode("-",$value);
		if(trim($codigo_a2[0]) != ''){
			$array_articulos_incluidos[trim($codigo_a2[0])] = trim($codigo_a2[0]);
		}
	}

	$entidad = trim($entidad);
	$datosTarifas = datosTarifas();
	$datosArticulos = consultarArticulos();

	$q = "  SELECT * "
		."    FROM ".$wbasedato_cliame."_000271 "
		."	 WHERE Paremp = '".$entidad."'"
		."	   AND Parest = 'on'";
	$res = mysql_query($q,$conex) or die("Error en el query: ".$q."<br>Tipo Error:".mysql_error());
	$num = mysql_num_rows($res);

	if($pareto != '' and $num == 0){

		$datamensaje['mensaje'] = "La entidad seleccionada no tiene registros de pareto";
		$datamensaje['error'] = 1;
		echo json_encode($datamensaje);
		return;

	}

	$tabla['ivart'] = "ivart, ivarttar";
	$campos[] = "artcod, artnom, arttarval, arttarvaa, arttartar, arttarfec, artcos, artiva, artfec, artfco";
	$filtro[] = "artcod = arttarcod AND artact = 'S'";
	$filtro[] = "arttartar LIKE '%".trim($tarifa)."%'";


	$filtro_grupo = array();
	$filtro_art = array();

	if($entidad != '' and $entidad != '*'){

		$tabla['inemp'] = "inemp";
		$campos[] = "empnit, empnom";
		$filtro[] = "empnit = '".trim($entidad)."'";
		$filtro[] = "emptar = arttartar";
		$filtro[] = "empact = 'S'";

	}

	if($grupo_inicial != ''){

		if($grupo_final != ''){

			if(count($array_grupos_excluidos) > 0){
				$array_grupos_excluidos = implode("','",$array_grupos_excluidos);
				$filtro_grupo[] = "grucod NOT IN ('".$array_grupos_excluidos."')";
			}

			if(count($array_grupos_incluidos) > 0){
				$array_grupos_incluidos = implode("','",$array_grupos_incluidos);
				$filtro_gr_incluidos = "grucod IN ('".$array_grupos_incluidos."')";
			}

			$filtro_grupo[] = "grucod BETWEEN '".trim($grupo_inicial)."' AND '".trim($grupo_final)."'";

		}else{

			$filtro_grupo[] = "grucod = '".trim($grupo_inicial)."'";

		}

		$tabla['ivgru'] = "ivgru";
		$campos[] = "grucod, grunom";
		$filtro[] = "artgru = grucod";



	}

	if($codigo_inicial != '' AND $codigo_final == ''){

		$campos[] = "artcod, artnom";
		$filtro_art[] = "artcod = '".trim($codigo_inicial)."'";
	}

	if($codigo_inicial != '' AND $codigo_final != ''){

		//-- Articulos o dispositivos excluidos desde las alertas
		if(count($articulos_excluidos_alerta1) > 0){
			$articulos_excluidos_alerta1 = implode("','",$articulos_excluidos_alerta1);
			$filtro_art[] = "artcod NOT IN ('".$articulos_excluidos_alerta1."')";
		}

		if(count($articulos_excluidos_alerta2) > 0){
			$articulos_excluidos_alerta2 = implode("','",$articulos_excluidos_alerta2);
			$filtro_art[] = "artcod NOT IN ('".$articulos_excluidos_alerta2."')";
		}

		if(count($articulos_excluidos_alerta3) > 0){
			$articulos_excluidos_alerta3 = implode("','",$articulos_excluidos_alerta3);
			$filtro_art[] = "artcod NOT IN ('".$articulos_excluidos_alerta3."')";
		}

		if(count($articulos_excluidos_alerta4) > 0){
			$articulos_excluidos_alerta4 = implode("','",$articulos_excluidos_alerta4);
			$filtro_art[] = "artcod NOT IN ('".$articulos_excluidos_alerta4."')";
		}

		if(count($articulos_excluidos_alerta5) > 0){
			$articulos_excluidos_alerta5 = implode("','",$articulos_excluidos_alerta5);
			$filtro_art[] = "artcod NOT IN ('".$articulos_excluidos_alerta5."')";
		}
		//--------


		if(count($array_articulos_excluidos) > 0){
			$articulos_exluidos = implode("','",$array_articulos_excluidos);
			$filtro_art[] = "artcod NOT IN ('".$articulos_exluidos."')";
		}



		if(count($array_articulos_incluidos) > 0){
			$articulos_incluidos = implode("','",$array_articulos_incluidos);
			$filtro_art_incluidos = "artcod IN ('".$articulos_incluidos."')";
		}

		$campos[] = "artcod, artnom";
		$filtro_art[] = "artcod BETWEEN '".trim($codigo_inicial)."' AND '".trim($codigo_final)."'";

	}else{

		//-- Articulos o dispositivos excluidos desde las alertas
		if(count($articulos_excluidos_alerta1) > 0){
			$articulos_excluidos_alerta1 = implode("','",$articulos_excluidos_alerta1);
			$filtro_art[] = "artcod NOT IN ('".$articulos_excluidos_alerta1."')";
		}

		if(count($articulos_excluidos_alerta2) > 0){
			$articulos_excluidos_alerta2 = implode("','",$articulos_excluidos_alerta2);
			$filtro_art[] = "artcod NOT IN ('".$articulos_excluidos_alerta2."')";
		}

		if(count($articulos_excluidos_alerta3) > 0){
			$articulos_excluidos_alerta3 = implode("','",$articulos_excluidos_alerta3);
			$filtro_art[] = "artcod NOT IN ('".$articulos_excluidos_alerta3."')";
		}

		if(count($articulos_excluidos_alerta4) > 0){
			$articulos_excluidos_alerta4 = implode("','",$articulos_excluidos_alerta4);
			$filtro_art[] = "artcod NOT IN ('".$articulos_excluidos_alerta4."')";
		}

		if(count($articulos_excluidos_alerta5) > 0){
			$articulos_excluidos_alerta5 = implode("','",$articulos_excluidos_alerta5);
			$filtro_art[] = "artcod NOT IN ('".$articulos_excluidos_alerta5."')";
		}
		//--------

		if(count($array_articulos_excluidos) > 0){
			$articulos_exluidos = implode("','",$array_articulos_excluidos);
			$filtro_art[] = "artcod NOT IN ('".$articulos_exluidos."')";
		}

		if(count($array_articulos_incluidos) > 0){
			$articulos_incluidos = implode("','",$array_articulos_incluidos);
			$filtro_art_incluidos = "artcod IN ('".$articulos_incluidos."')";
		}

	}

	//Uno las tablas, los campos y los filtros.
	$tablas = implode(",",$tabla);
	$campos = implode(" ,",$campos);
	$filtro = implode(" AND ",$filtro);

	if(count($filtro_grupo) > 0){
		$filtro_grupo = implode(" AND ",$filtro_grupo);
		$grupos_filtro = " AND $filtro_grupo";
	}

	if(count($filtro_art) > 0){
		$filtro_art = implode(" AND ",$filtro_art);
		$articulos_filtro = " AND $filtro_art";
	}


	//Si hay grupos incluidos se agrega este union.
	if(count($array_grupos_incluidos) > 0){
		$select_grupos_incluidos = " UNION SELECT $campos FROM $tablas WHERE $filtro AND $filtro_gr_incluidos ";
	}

	//si hay articulos incluidos se agrega este union.
	if(count($array_articulos_incluidos) > 0){
		$select_art_incluidos = " UNION SELECT $campos FROM $tablas WHERE $filtro AND $filtro_art_incluidos ";
	}

	//Se asigna una cantidad de memoria suficiente para poblar los arreglos
	ini_set('memory_limit', '2048M');
	//Consulta final a unix.
	$query  = "SELECT $campos FROM $tablas WHERE $filtro $grupos_filtro $articulos_filtro";
	$query .= "$select_grupos_incluidos";
	$query .= "$select_art_incluidos";
	//echo $query;
	$err_o = odbc_do($conexUnix,$query) or die (odbc_errormsg());
	$num = odbc_num_fields($err_o);

	//****** Consultar articulos que no registran compras en los ultimos 2 años.
	$ano_actual = date('Y');
	$mes_actual = date('m');
	$ano_inicial = $ano_actual-2;

	// $query_compra  = "SELECT movdetart, movdetano, movdetmes FROM $tablas, ivmovdet WHERE $filtro AND movdetart = artcod AND movdetart = arttarcod  AND movdetano >= '".$ano_inicial."' GROUP BY 1,2,3 ORDER BY 2 desc, 3 desc ";
	// $res_compras = odbc_do($conexUnixInventarios,$query_compra) or die (odbc_errormsg());
	// $num_compras = odbc_num_fields($res_compras);

	// if($num_compras > 0){

		// $array_art_compras = array();

		// while($row_compras = odbc_fetch_array($res_compras))
        // {
			// $array_art_compras[$row_compras['movdetart']] = $row_compras;
        // }

	// }


	//***********************

	//****** Consultar articulos que no registran ventas en los ultimos 2 años.

	// $query_compra  = "SELECT drodetart FROM $tablas, ivdrodet WHERE $filtro AND drodetart = artcod AND drodetart = arttarcod  AND drodetano >= '".$ano_inicial."' GROUP BY 1 ";
	// $res_ventas = odbc_do($conexUnixInventarios,$query_compra) or die (odbc_errormsg());
	// $num_ventas = odbc_num_fields($res_ventas);

	// if($num_ventas > 0){

		// $array_art_ventas = array();

		// while($row_ventas = odbc_fetch_array($res_ventas))
        // {

			// $array_art_ventas[$row_ventas['drodetart']] = $row_ventas['drodetart'];

        // }

	// }

	//print_r($array_art_ventas);
	//***********************

	$i = 0;
	$array_resultado = array();

	 while($row = odbc_fetch_array($err_o))
        {

			$array_resultado[trim(utf8_encode(odbc_result($err_o,'artcod')))][trim(odbc_result($err_o,'arttartar'))] = $row;
			$array_resultado_aux[trim(utf8_encode(odbc_result($err_o,'artcod')))][trim(odbc_result($err_o,'arttartar'))] = $row;

        }
		// echo count($array_resultado);
	// print_r($array_resultado);

	//Filtros matrix
	if($pos != ''){

		if($pos == 'S'){
			//Consulta los codigos de los articulos pos
			 $q = "  SELECT * "
				."    FROM ".$wbasedato."_000026 "
				."	 WHERE Artest = 'on'"
				."	   AND Artpos = 'P'";
		}else{

			$q = "  SELECT * "
				."    FROM ".$wbasedato."_000026 "
				."	 WHERE Artest = 'on'"
				."	   AND Artpos != 'P'";
		}

		$res = mysql_query($q,$conex) or die("Error en el query: ".$q."<br>Tipo Error:".mysql_error());

		$array_articulos_pos = array();

		//Creo un arreglo con los articulos regulados
		while($row = mysql_fetch_array($res)){

			$array_articulos_pos[$row['Artcod']] = $row;

		}

		foreach($array_articulos_pos as $key => $value){

				if(array_key_exists($key, $array_resultado)){

					$array_resultado_pos[$key] = $array_resultado_aux[$key];

				}

		}

		//Igualo la variable $array_resultado para que sea leida correctamente en el foreach que sigue y pinte la informacion necesaria.
		$array_resultado = $array_resultado_pos;

		if(count($array_resultado) == 0){
			$array_resultado = array();
		}
		//print_r($array_resultado);
	}


	if($articulo_idc != ''){

		if($articulo_idc == 'Si'){
			//Consulta los codigos de los articulos pos
			 $q = "  SELECT * "
				."    FROM ".$wbasedato."_000026 "
				."	 WHERE Artest = 'on'"
				."	   AND Artidc = 'on'";
		}else{

			$q = "  SELECT * "
				."    FROM ".$wbasedato."_000026 "
				."	 WHERE Artest = 'on'"
				."	   AND Artidc = ''";
		}

		$res = mysql_query($q,$conex) or die("Error en el query: ".$q."<br>Tipo Error:".mysql_error());

		$array_articulos_idc = array();

		//Creo un arreglo con los articulos regulados
		while($row = mysql_fetch_array($res)){

			$array_articulos_idc[$row['Artcod']] = $row;

		}

		foreach($array_articulos_idc as $key => $value){

				if(array_key_exists($key, $array_resultado)){

					$array_resultado_idc[$key] = $array_resultado_aux[$key];

				}

		}
			//print_r($array_resultado_idc);
		//Igualo la variable $array_resultado para que sea leida correctamente en el foreach que sigue y pinte la informacion necesaria.
		$array_resultado = $array_resultado_idc;

		if(count($array_resultado) == 0){
			$array_resultado = array();
		}
		//print_r($array_resultado);
	}


	//Si seleccionan medicamentos regulados hace relacion con la tabla cliame_000207.
	if($regulado != '' and $pareto == ''){

		//Consulta los codigos de los articulos regulados
		$q = "  SELECT * "
			."    FROM ".$wbasedato_cliame."_000270 "
			."	 WHERE Regest = 'on'";
		$res = mysql_query($q,$conex) or die("Error en el query: ".$q."<br>Tipo Error:".mysql_error());

		$array_articulos_reg = array();

		//Creo un arreglo con los articulos regulados
		while($row = mysql_fetch_array($res)){

			$array_articulos_reg[$row['Regcod']] = $row;

		}

		if($regulado =='S'){

		foreach($array_articulos_reg as $key => $value){

				if(array_key_exists($key, $array_resultado)){

					$array_resultado_reg[$key] = $array_resultado_aux[$key];

				}
			}

			$array_resultado = $array_resultado_reg;

		}else{

			foreach($array_articulos_reg as $key => $value){

				if(array_key_exists($key, $array_resultado)){

					unset($array_resultado[$key]);

				}

			}

		}

		if(count($array_resultado) == 0){
			$array_resultado = array();
		}
	}

	if($pareto != '' and $regulado == ''){

			//Consulta los codigos de los articulos regulados
			$q = "  SELECT * "
				."    FROM ".$wbasedato_cliame."_000271 "
				."	 WHERE Paremp = '".$entidad."'"
				."	   AND Parest = 'on'";
			$res = mysql_query($q,$conex) or die("Error en el query: ".$q."<br>Tipo Error:".mysql_error());

			$array_articulos_pareto = array();

			//Creo un arreglo con los articulos regulados
			while($row = mysql_fetch_array($res)){

				$array_articulos_pareto[$row['Parcod']] = $row;

			}

			if($pareto == 'S'){

				foreach($array_articulos_pareto as $key => $value){

						if(array_key_exists($key, $array_resultado)){

							$array_resultado_pareto[$key] = $array_resultado_aux[$key];

						}
					}

					$array_resultado = $array_resultado_pareto;

			}else{

					foreach($array_articulos_pareto as $key => $value){

						if(array_key_exists($key, $array_resultado)){

							unset($array_resultado[$key]);

						}

					}

				}
			if(count($array_resultado) == 0){
			$array_resultado = array();
		}

		}


	if($pareto != '' and $regulado != ''){


		switch(1){

			case ($pareto == 'S' and $regulado == 'S'):

									//Consulta los codigos de los articulos regulados
									$q = "  SELECT * "
										."    FROM ".$wbasedato_cliame."_000270, ".$wbasedato_cliame."_000271 "
										."	 WHERE Paremp = '".$entidad."'"
										."	   AND Parcod = Regcod"
										."	   AND Parest = 'on'"
										."	   AND Regest = 'on'";
									$res = mysql_query($q,$conex) or die("Error en el query: ".$q."<br>Tipo Error:".mysql_error());

									$array_articulos_pareto_reg = array();

									//Creo un arreglo con los articulos regulados
									while($row = mysql_fetch_array($res)){

										$array_articulos_pareto_reg[$row['Parcod']] = $row;

									}


									foreach($array_articulos_pareto_reg as $key => $value){

										if(array_key_exists($key, $array_resultado)){

											$array_resultado_pareto_reg[$key] = $array_resultado_aux[$key];

											}

									}

									//Igualo la variable $array_resultado para que sea leida correctamente en el foreach que sigue y pinte la informacion necesaria.
									$array_resultado = $array_resultado_pareto_reg;

									if(count($array_resultado) == 0){
										$array_resultado = array();
									}


			break;

			case ($pareto == 'N' and $regulado == 'S'):

							//Consulta los codigos de los articulos regulados
							$q = "  SELECT * "
								."    FROM ".$wbasedato_cliame."_000270 "
								."	 WHERE Regest = 'on'";
							$res = mysql_query($q,$conex) or die("Error en el query: ".$q."<br>Tipo Error:".mysql_error());

							$array_articulos_reg = array();

							//Creo un arreglo con los articulos regulados
							while($row = mysql_fetch_array($res)){

								$array_articulos_reg[$row['Regcod']] = $row;

							}

							foreach($array_articulos_reg as $key => $value){

									if(array_key_exists($key, $array_resultado)){

										$array_resultado_reg[$key] = $array_resultado_aux[$key];

									}
								}

							$array_resultado = $array_resultado_reg;


							//Consulta los codigos de los articulos pareto
							$q = "  SELECT * "
								."    FROM ".$wbasedato_cliame."_000271 "
								."	 WHERE Paremp = '".$entidad."'"
								."	   AND Parest = 'on'";
							$res = mysql_query($q,$conex) or die("Error en el query: ".$q."<br>Tipo Error:".mysql_error());

							$array_articulos_pareto = array();

							//Creo un arreglo con los articulos regulados
							while($row = mysql_fetch_array($res)){

								$array_articulos_pareto[$row['Parcod']] = $row;

							}

							foreach($array_articulos_pareto as $key => $value){

									unset($array_resultado[$key]);

								}

							if(count($array_resultado) == 0){
										$array_resultado = array();
									}

			break;

			case ($pareto == 'S' and $regulado == 'N'):

							//Consulta los codigos de los articulos pareto
							$q = "  SELECT * "
								."    FROM ".$wbasedato_cliame."_000271 "
								."	 WHERE Paremp = '".$entidad."'"
								."	   AND Parest = 'on'";
							$res = mysql_query($q,$conex) or die("Error en el query: ".$q."<br>Tipo Error:".mysql_error());

							$array_articulos_pareto = array();

							//Creo un arreglo con los articulos regulados
							while($row = mysql_fetch_array($res)){

								$array_articulos_pareto[$row['Parcod']] = $row;

							}

							foreach($array_articulos_pareto as $key => $value){

									if(array_key_exists($key, $array_resultado)){

										$array_resultado_pareto[$key] = $array_resultado_aux[$key];

									}
								}

							$array_resultado = $array_resultado_pareto;

							//Consulta los codigos de los articulos regulados
							$q = "  SELECT * "
								."    FROM ".$wbasedato_cliame."_000270 "
								."	 WHERE Regest = 'on'";
							$res = mysql_query($q,$conex) or die("Error en el query: ".$q."<br>Tipo Error:".mysql_error());

							$array_articulos_reg = array();

							//Creo un arreglo con los articulos regulados
							while($row = mysql_fetch_array($res)){

								$array_articulos_reg[$row['Regcod']] = $row;

							}

							foreach($array_articulos_reg as $key => $value){

									unset($array_resultado[$key]);

								}

							if(count($array_resultado) == 0){
										$array_resultado = array();
									}

			break;

			case ($pareto == 'N' and $regulado == 'N'):

						//Consulta los codigos de los articulos pareto
							$q = "  SELECT * "
								."    FROM ".$wbasedato_cliame."_000271 "
								."	 WHERE Paremp = '".$entidad."'"
								."	   AND Parest = 'on'";
							$res = mysql_query($q,$conex) or die("Error en el query: ".$q."<br>Tipo Error:".mysql_error());

							$array_articulos_pareto = array();

							//Creo un arreglo con los articulos regulados
							while($row = mysql_fetch_array($res)){

								$array_articulos_pareto[$row['Parcod']] = $row;

							}

							foreach($array_articulos_pareto as $key => $value){

									unset($array_resultado[$key]);

								}

							//Consulta los codigos de los articulos regulados
							$q = "  SELECT * "
								."    FROM ".$wbasedato_cliame."_000270 "
								."	 WHERE Regest = 'on'";
							$res = mysql_query($q,$conex) or die("Error en el query: ".$q."<br>Tipo Error:".mysql_error());

							$array_articulos_reg = array();

							//Creo un arreglo con los articulos regulados
							while($row = mysql_fetch_array($res)){

								$array_articulos_reg[$row['Regcod']] = $row;

							}

							foreach($array_articulos_reg as $key => $value){

									unset($array_resultado[$key]);

								}

							if(count($array_resultado) == 0){
								$array_resultado = array();
							}

			break;


		}


		//print_r($array_resultado);
	}


	if($tipo_articulo != ''){

		//Medicamentos es on y dispositivos es off
		if($tipo_articulo == 'on'){
			//Consulta los codigos de los medicamentos
			$q = "  SELECT * "
				."    FROM ".$wbasedato."_000026 "
				."	 WHERE Artest = 'on'"
				."	   AND Artesm = 'on'";
		}else{
			//Consulta los codigos de los dispositivos
			$q = "  SELECT * "
				."    FROM ".$wbasedato."_000026 "
				."	 WHERE Artest = 'on'"
				."	   AND (Artesm = 'off' OR Artesm = '')";
		}

		$res = mysql_query($q,$conex) or die("Error en el query: ".$q."<br>Tipo Error:".mysql_error());

		$array_articulos_medi_dispo = array();

		//Creo un arreglo con los articulos regulados
		while($row = mysql_fetch_array($res)){

			$array_articulos_medi_dispo[$row['Artcod']] = $row;

		}

		foreach($array_articulos_medi_dispo as $key => $value){

				if(array_key_exists($key, $array_resultado)){

					$array_resultado_medi_dispo[$key] = $array_resultado_aux[$key];

				}

		}


		//Igualo la variable $array_resultado para que sea leida correctamente en el foreach que sigue y pinte la informacion necesaria.
		$array_resultado = $array_resultado_medi_dispo;
		//print_r($array_resultado);

		if(count($array_resultado) == 0){
				$array_resultado = array();
			}
	}

	$cant_registros = array_sum(array_map("count", $array_resultado)); //Suma la cantidad total de elementos en el arreglo ya que es multidimensional.

	if($cant_registros > 0){

		$array_resultado_final = array_slice($array_resultado, $registros_inicial, $registros_final); //Array porcionado, solo mostrara una cantidad definica de registros.
		$array_resultado_final_oculto = array_slice($array_resultado, 0, 1000000); //Array entero, para poder pintarlo oculto.
		$cant_registros_pagina = array_sum(array_map("count", $array_resultado_final)); //Cantidad de registros por pagina.

		asort($array_resultado_final);

		$wfecha = date('Y-m-d');
		$whora = date('H:i:s');

		if(count($array_resultado_final) > 0){

			$datamensaje['html'] .= "<br><input value='Actualizar tarifas' onclick='mostrar_art(\"on\");' type='button'><button id='exportButton'>Exportar a Excel</button><br><br>";
			$datamensaje['html'] .= "<table><tr class='encabezadotabla'><td>Codigo</td><td>Tarifa</td><td>Nombre generico</td><td>Nombre comercial</td><td>UN</td><td>Registro Invima</td><td>Costo compra</td><td>Tarifa Actual</td><td>Tarifa incremento</td><th>Fecha Codificacion</th><td>Basado en costo</td></tr>";
			$datamensaje['html_oculto'] .= "<table id='exportTable'><thead><tr class='encabezadotabla'><th>Codigo</th><th>Tarifa</th><th>Generico</th><th>Comercial</th><th>UN</th><th>Grupo</th><th>POS</th><th>CUM</th><th>Invima</th><th>Costo</th><th>TarifaActual</th><th>TarifaIncremento</th><th>Clasificacion</th><th>FechaCodificacion</th></tr></thead>";


			foreach($array_resultado_final as $cod_art =>$tarifas){

				foreach($tarifas as $cod_tarifa => $value){

				 $class2 = "class='fila".(($i%2)+1)."'";
				 $incremento_sobre_costo = 'off';
				 switch(1){

					case ($porc_incremento != '') :
											//si es por costo buscar el costo del articulo ******
											$valor_insumo = $value['arttarval'];
											if($inc_costo_porcentaje == "on"){
												$valor_insumo = $value['artcos']*('1.'.$value['artiva']);
												$incremento_sobre_costo = 'on';
											}
											$incremento = porcentaje($valor_insumo, $porc_incremento, $redondeo, $inc_costo_porcentaje);
					break;

					case ($inc_tarifa_fija != '') :
											$incremento = $inc_tarifa_fija;
					break;

					case ($inc_valor_adicional != ''):
											$valor_insumo = $value['arttarval'];
											if($inc_costo_adicional == "on"){
												$valor_insumo = $value['artcos']*('1.'.$value['artiva']);
												$incremento_sobre_costo = 'on';
											}
											$incremento = $inc_valor_adicional+$valor_insumo;
					break;
				 }

				 $datamensaje['html'] .= "<tr $class2><td>".$value['artcod']."</td><td>".$datosTarifas[$cod_tarifa]."</td><td>".utf8_encode($datosArticulos[$value['artcod']]['Artgen'])."</td><td>".utf8_encode($datosArticulos[$value['artcod']]['Artcom'])."</td><td>".utf8_encode($datosArticulos[$value['artcod']]['Artuni'])."</td><td>".utf8_encode($datosArticulos[$value['artcod']]['Artreg'])."</td><td align=right>".number_format($value['artcos']*(('1.'.$value['artiva'])*1), 2)."</td><td align=right>".number_format($value['arttarval'], 2)."</td><td align=right>".number_format($incremento, 2)."</td><td align=center>".$value['artfec']."</td><td align=center>".(($incremento_sobre_costo=='on') ? 'Si': 'No')."</td></tr>";

				 $i++;
				}
			}


			//Este segmento crea la tabla oculta
			foreach($array_resultado_final_oculto as $cod_art =>$tarifas){



				foreach($tarifas as $cod_tarifa => $value){

				 $incremento_sobre_costo = 'off';
				 switch(1){

					case ($porc_incremento != '') :
											$valor_insumo = $value['arttarval'];
											if($inc_costo_porcentaje == "on"){
												$valor_insumo = $value['artcos']*('1.'.$value['artiva']);
												$incremento_sobre_costo = 'on';
											}
											$incremento = porcentaje($valor_insumo, $porc_incremento, $redondeo, $inc_costo_porcentaje);
					break;

					case ($inc_tarifa_fija != '') :
											$incremento = $inc_tarifa_fija;
					break;

					case ($inc_valor_adicional != ''):
											$valor_insumo = $value['arttarval'];
											if($inc_costo_adicional == "on"){
												$valor_insumo = $value['artcos']*('1.'.$value['artiva']);
												$incremento_sobre_costo = 'on';
											}
											$incremento = $inc_valor_adicional+$valor_insumo;
					break;
				 }

				 $datosArticulosGenerico = limpiarString($datosArticulos[$value['artcod']]['Artgen']);
				 $datosArticulosComercial = limpiarString($datosArticulos[$value['artcod']]['Artcom']);
				 $datosArticulosInvima = limpiarString($datosArticulos[$value['artcod']]['Artreg']);
				 $datosArtPOS = limpiarString($datosArticulos[$value['artcod']]['Artpos']);

				 if($datosArtPOS == 'P'){
					 $datosArtPOS = "POS";
				 }elseif($datosArtPOS == 'N'){
					 $datosArtPOS = "NO POS";
				 }

				 $costoCorregido = $value['artcos']*(('1.'.$value['artiva'])*1);//-->2019-07-10
				 $datamensaje['html_oculto'] .= "<tr><td>".$value['artcod']."</td><td>".$datosTarifas[$cod_tarifa]."</td><td>".utf8_encode($datosArticulosGenerico)."</td><td>".utf8_encode($datosArticulosComercial)."</td><td>".utf8_encode($datosArticulos[$value['artcod']]['Artuni'])."</td><td>".utf8_encode($datosArticulos[$value['artcod']]['Artgru'])."</td><td>".$datosArtPOS."</td><td>".utf8_encode($datosArticulos[$value['artcod']]['Artcum'])."</td><td>".utf8_encode($datosArticulosInvima)."</td><td align=right>".$costoCorregido."</td><td align=right>".$value['arttarval']."</td><td align=right>".$incremento."</td><td align=right>".$datosArticulos[$value['artcod']]['Artcla']."</td><td>".$value['artfec']."</td></tr>";

			}
		}

			$datamensaje['html'] .= "</table>";
			$datamensaje['html_oculto'] .= "</tbody></table>";


			if($actualizar == 'on'){

				$log = "INSERT INTO ".$wbasedato_cliame."_000277 (       Medico           ,   Fecha_data    ,   Hora_data    ,       Lognit  ,           Logfin         ,         Loggin 	      ,      Loggfi       ,           Logexg        ,            Loging       ,        Logcin          ,       Logcfi         ,      Logcex              ,       Logcoi              ,      Logreg    ,     Logpar   ,    Logpos ,       Logtip        ,        Logidc      ,     Logtar   ,     Logred     ,       Loginp          ,      Loginv           ,        Logiva				 		, Logico 						,Logobs      ,    Logest, Seguridad ) "
										."                 VALUES ('".$wbasedato_cliame."','" . $wfecha . "','" . $whora . "', '".$entidad."','" . $fecha_inicio_inc . "','" . $grupo_inicial . "','". $grupo_final."','" .$grupos_excluidos. "','" .$grupos_incluidos. "','" .$codigo_inicial . "','" .$codigo_final. "' ,'".$articulos_excluidos."', '".$articulos_incluidos."', '".$regulado."', '".$pareto."', '".$pos."', '".$tipo_articulo."', '".$articulo_idc."', '".$tarifa."', '".$redondeo."', '".$porc_incremento."', '".$inc_tarifa_fija."', 		'".$inc_valor_adicional."'  , '".$incremento_sobre_costo."'	,'".$observaciones."','on','".$_SESSION['user']."')";
				$err = mysql_query($log, $conex) or die (mysql_errno().$log." - ".mysql_error());
				$id = mysql_insert_id();

				foreach($array_resultado_final_oculto as $cod_art =>$tarifas){

					foreach($tarifas as $cod_tarifa => $value){
						$incremento_sobre_costo = 'off';
						switch(1){
						case ($porc_incremento != '') :
											//si es por costo buscar el costo del articulo ******
											$valor_insumo = $value['arttarval'];
											if($inc_costo_porcentaje == "on"){
												$valor_insumo = $value['artcos'];
												$incremento_sobre_costo = 'on';
											}
											$incremento = porcentaje($valor_insumo, $porc_incremento, $redondeo, $inc_costo_porcentaje);
						break;

						case ($inc_tarifa_fija != '') :
												$incremento = $inc_tarifa_fija;
						break;

						case ($inc_valor_adicional != ''):
											$valor_insumo = $value['arttarval'];
											if($inc_costo_adicional == "on"){
												$valor_insumo = $value['artcos'];
												$incremento_sobre_costo = 'on';
											}
											$incremento = $inc_valor_adicional+$valor_insumo;
						break;
						}

						$log = "INSERT INTO ".$wbasedato_cliame."_000272 (       Medico       ,   Fecha_data ,   Hora_data     , Logconsec,    Logcodart         ,           Logoldval         ,      Lognewval      ,      Logoldvaa           ,        Lognewvaa          ,         Logoldfin         ,     Lognewfin            ,          Logtarifa       , Logcosto				, Logincos						, Logestado	, Seguridad          ) "
										."                 VALUES ('".$wbasedato_cliame."','" . $wfecha . "','" . $whora . "', '".$id."','".$value['artcod']."','" . $value['arttarval'] . "','" . $incremento . "','". $value['arttarvaa']."','" .$value['arttarval']. "','" .$value['arttarfec']. "','" .$fecha_inicio_inc . "','" .$value['arttartar']. "' , '".$value['artcos']."'	, '".$incremento_sobre_costo."'	, 'on' 		, '".$_SESSION['user']."')";
						$err = mysql_query($log, $conex) or die (mysql_errno().$q." - ".mysql_error());

						$query_update = "UPDATE ivarttar SET arttarval = '".$incremento."', arttarvaa='".$value['arttarval']."', arttarfec = '".$fecha_inicio_inc."', arttarumo = '".$_SESSION['user']."', arttarfmo = '".date('Y-m-d H:i:s')."' WHERE arttarcod = '".$value['artcod']."' AND arttartar = '".$cod_tarifa."'";
						$err_o = odbc_do($conexUnix,$query_update) or die (odbc_errormsg());

					}
				}
				$datamensaje['query_update_ok'] = "on";
				$datamensaje['mensaje_update'] = "Articulos actualizados";
			}
		}

	}

	 $datamensaje['cant_registros_pagina'] = $cant_registros_pagina;
	 $datamensaje['total_registros'] =  $cant_registros;

	echo json_encode($datamensaje);

	return;
}

function eliminarArchivosDiasAnteriores($dir, $dias_cache_archivos_furips = 1)
{
    if(is_dir($dir)){ }
    else { mkdir($dir,0777); }

    $arr_archivos_r = array();
    // Abrir un directorio conocido, y proceder a leer sus contenidos
    /*
        Esta sección se encarga de leer todos los archivos generados para resultados y verifíca si la fecha de creación es menor
        a la fecha actual, si es así entonces elimina esos archivos, esto con el fin de liberar el disco duro de estos archivos no necesarios.
    */
    if (is_dir($dir)) {
        if ($gd = opendir($dir)) {
            while ($archivo = readdir($gd)) {
                //echo "nombre de archivo: $archivo : tipo de archivo: " . filetype($dir . $archivo) . "\n";
                if($archivo != '.' && $archivo != '..'){
                    $arr_archivos_r[] = $dir."/".$archivo;
                }
            }
            closedir($gd);
        }

        foreach ($arr_archivos_r as $key => $archivo) {
            $fecha_creado = date("Ymd", filectime($archivo));
            $fecha_modifi = date("Ymd", filemtime($archivo));

            $fecha_actual = date('Ymd');
            $nuevafecha   = strtotime ('-'.$dias_cache_archivos_furips.' day',strtotime ($fecha_actual));
            $fecha_limite = date ('Ymd',$nuevafecha);

            if($fecha_creado < $fecha_limite || $fecha_modifi < $fecha_limite) { unlink($archivo); }
        }
    }
}


function limpiarString($string)
{
 $string = htmlentities($string);
 $string = preg_replace('/\&(.)[^;]*;/', '\\1', $string);
 return $string;
}

//Calculo de porcentaje y redondeo.
function porcentaje($total, $parte, $redondear, $tipo_incremento) {

	$redondear = ($redondear == '') ? 2 : $redondear;

	//on es basado en el costo y off basado en la tarifa.
	if($tipo_incremento == 'on'){
		$cifra = $total/((100-$parte)/100);
	}else{
		$cifra = $total*(($parte/100)+1);
	}

    return round($cifra, $redondear);
}

//Funcion que retorna la lista de grupos de medicamentos
function consultarGrupos(){

	global $conex;
	global $wbasedato;
	global $wemp_pmla;
	global $conexUnix;

	$caracteres =  array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü");
	$caracteres2 = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U");

	// $query = "SELECT grucod, grunom
				// FROM ivgru
			// ORDER BY grucod";
	// $err_o = odbc_do($conexUnix,$query) or die (odbc_errormsg());

	$query = "SELECT SUBSTRING_INDEX( artgru, '-', 1 ) as grucod, SUBSTRING_INDEX( artgru, '-', -1 ) as grudes
				FROM ".$wbasedato."_000026
			GROUP BY grudes
			ORDER BY grudes";
	$res = mysql_query($query,$conex) or die("Error en el query: ".$query."<br>Tipo Error:".mysql_error());

	$arreglo = array();

	 while ($row = mysql_fetch_array($res))
		{

		$row['grudes'] = str_replace( $caracteres, $caracteres2, $row['grudes'] );
		$row['grudes'] = utf8_decode( $row['grudes'] );
		array_push($arreglo, trim($row['grucod']).' - '.trim($row['grudes']) );


		}

	return $arreglo;
}


function consultarSaldoUnix(){


	global $conexUnix;
	global $conex;
	global $wbasedato_movhos;

	$ano = '2015';
	$mes = date('m');

	$arr_cco = array();

	$q_cco = "SELECT Ccocod
				FROM {$wbasedato_movhos}_000011
			   WHERE Ccotra = 'on'
				 AND Ccoima = 'off'";
	$r_cco = mysql_query($q_cco,$conex) or die("Error en el query: ".$q_cco."<br>Tipo Error:".mysql_error());

	while($row_cco = mysql_fetch_assoc($r_cco))
	{
		$arr_cco[trim($row_cco['Ccocod'])] = trim($row_cco['Ccocod']);
	}

	$centros_costo = implode("','",$arr_cco);

	$query = "SELECT salart, (salant+salent-salsal) as saldo
				FROM ivsal
			   WHERE salano = '".$ano."'
			     AND salmes = '".$mes."'
			     AND salser in ('".$centros_costo."')";
	$err_o = odbc_do($conexUnix,$query) or die (odbc_errormsg());

	$arreglo = array();

	 while ($row = odbc_fetch_array($err_o))
		{
			if($row['saldo'] > 0){

				$arreglo[trim($row['salart'])] = array('saldo'=>trim($row['saldo']));

			}else{

				$arreglo[trim($row['salart'])] = array('saldo'=>0);

			}

		}


	return $arreglo;
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
			   WHERE taract = 'S'
			ORDER BY tarcod ";
	$err_o = odbc_do($conexUnix,$query) or die (odbc_errormsg());

	$arreglo = array();

	 while ($row = odbc_fetch_array($err_o))
		{

		$row['tarnom'] = str_replace( $caracteres, $caracteres2, $row['tarnom'] );
		$row['tarnom'] = utf8_decode( $row['tarnom'] );
		array_push($arreglo, trim($row['tarcod']).' - '.trim($row['tarnom']) );

		}

	return $arreglo;
}

function datosTarifas(){

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
		$arreglo[trim($row['tarcod'])] = trim($row['tarcod'])." - ".trim($row['tarnom']);

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

	$query = "SELECT empnit, empcod, empnom
				FROM inemp
			   WHERE empact = 'S'
			ORDER BY empcod ";
	$err_o = odbc_do($conexUnix,$query) or die (odbc_errormsg());

	$arreglo = array();

	 while ($row = odbc_fetch_array($err_o))
		{

		$row['empnom'] = str_replace( $caracteres, $caracteres2, $row['empnom'] );
		$row['empnom'] = utf8_decode( $row['empnom'] );
		array_push($arreglo, trim($row['empnit']).' - '.trim($row['empcod']).' - '.trim($row['empnom']) );

		}

	array_push($arreglo, '* - Todas las Empresas ' );

	return $arreglo;
}


?>

<html>
<head>
  <meta content="text/html; charset=UTF8" http-equiv="content-type">
  <title>Incremento Medicamentos</title>
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
	<script type="text/javascript" src="../../../include/root/prettify.js"></script>
	<script type='text/javascript' src='../../../include/root/smartpaginator.js'></script>	<!-- Autocomplete -->
	<script type='text/javascript' src='../../../include/root/jquery.tooltip.js'></script>
	<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
	<script type="text/javascript" src="../../../include/root/shieldui-all.min.js"></script>
	<script type="text/javascript" src="../../../include/root/jszip.min.js"></script>
	<script type="text/javascript" >

	$(function(){
	$('#subida').submit(function(){

		var comprobar = $('#csv').val().length;
		var radio_tipo_incremento = $('input[class="radio_tipo_incremento"]:checked').val();

		if(radio_tipo_incremento == undefined){

				alert("Debe seleccionar el tipo de incremento.");
				return false;
			}

		if(comprobar > 0){

			 var formulario = $('#subida');
			 var archivos = new FormData();
			 var wemp_pmla = $("#wemp_pmla").val();
			 var entidad = $("#entidad").val();
			 var entidad = entidad.split("-");
			 var entidad = entidad[0];
			 var fecha_inicio_inc = $("#fecha_inicio_inc").val();
			 var tarifa = $( "#tarifa" ).val();
			 var tarifa = tarifa.split("-");
			 var tarifa = tarifa[0];
			 var tarifa = tarifa.trim();
			 var radio_tipo_porcentaje = $('input[name="radio_tipo_porcentaje"]:checked').val();
			 var valor_fijo = $("#valor_fijo").val();
			 var observaciones = $('#observaciones').val();
			 var continuar = $('#continuar').val();

			var url = 'incremento_tarifas.php?wemp_pmla='+wemp_pmla+'&consultaAjax=""&entidad='+entidad+'&fecha_inicio_inc='+fecha_inicio_inc+'&tarifa='+tarifa+'&operacion=validar_archivo&radio_tipo_porcentaje='+radio_tipo_porcentaje+'&valor_fijo='+valor_fijo+'&observaciones='+observaciones+'&continuar='+continuar;

				for (var i = 0; i < (formulario.find('input[type=file]').length); i++) {

               	 archivos.append((formulario.find('input[type="file"]:eq('+i+')').attr("name")),((formulario.find('input[type="file"]:eq('+i+')')[0]).files[0]));

      		 	}

			$.ajax({

				url: url,
				type: 'POST',
				dataType: "json",
				contentType: false,
            	data:  archivos,
               	processData:false,
				beforeSend : function (){

					$('#respuesta').html('<center><img src="../../images/medical/ajax-loader5.gif" width="50" heigh="50"></center>');

				},
				success: function(data){

					if(data.error == '1'){

						$('#respuesta_error').html('<label style="padding-top:10px; color:red;">'+data.mensaje+'</label>');
						$('#respuesta').html(data.html);
						//$('#btn_importar').prop('disabled','disabled');
						return false;

					}else{

						$("#modal_adjuntar_archivo").dialog('close');
						$("#respuesta").html('');
						$("#respuesta_error").html('');
						$('#csv').val('');
						$('#btn_importar').removeAttr('disabled');
						$("#radio_tipo_incremento").prop('checked', false);
						$("#valor_fijo").prop('checked', false);
						alert("Incremento de articulos exitoso.");
						//$('#respuesta').html('<label style="padding-top:10px; color:green;">Importación de archivo exitosa</label>');

						return false;

					}


				}

			});

			return false;

		}else{

			alert('Selecciona un archivo CSV para importar');

			return false;

		}
	});
});


	function mensajeFailAlert(mensaje, xhr, textStatus, errorThrown)
    {
        var msj_extra = '';
        msj_extra = (mensaje != '') ? "<br>"+mensaje: mensaje;
        jAlert($("#failJquery").val()+msj_extra, "Mensaje");
        // $("#div_error_interno").html(xhr.responseText);
        // console.log(xhr);
        // jAlert("error interno: "+xhr.responseText, "Mensaje"); console.log("error");
        // fnModalLoading_Cerrar();
        // $(".bloquear_todo").removeAttr("disabled");
    }

	function soloNumeros(e){

	var key = window.Event ? e.which : e.keyCode
	return ((key >= 48 && key <= 57) || (key==8) || (key==46))

	}

$(function() {


	setInterval(function() {
     
	$('.blink').effect("pulsate", {}, 5000);

	}, 1000);

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
	start.setDate(start.getDate() + 1);

   $("#fecha_inicio_inc").datepicker({
	    minDate: start

   });


	var grupos_array = new Array();
	//Selecciona la variable con las entidades y llena el arreglo para mostrarse en el input de "Entidad"
	var gruposx = $("#grupos_json").val();
	datos = eval ( gruposx );
	for( i in datos ){
		grupos_array.push( datos[i] );
	}
	//Autocompletar para las entidades responsables, cuando seleccione uno llama a buscarcodigoentidad
	$( "#grupo_inicial" ).autocomplete({
		source: grupos_array,
		minLength : 1,
		select: function(){
			$("#grupo_final").removeAttr("disabled");
		}
	});

	$( "#grupo_final" ).autocomplete({
		source: grupos_array,
		minLength : 1
	});

	var tarifas_array = new Array();
	//Selecciona la variable con las entidades y llena el arreglo para mostrarse en el input de "Entidad"
	var tarifasx = $("#tarifas_json").val();
	datos = eval ( tarifasx );
	for( i in datos ){
		tarifas_array.push( datos[i] );
	}
	//Autocompletar para las entidades responsables, cuando seleccione uno llama a buscarcodigoentidad
	$( "#tarifa" ).autocomplete({
		source: tarifas_array,
		minLength : 0
	});


	var empresas_array = new Array();
	//Selecciona la variable con las entidades y llena el arreglo para mostrarse en el input de "Entidad"
	var empresasx = $("#empresas_json").val();
	datos = eval ( empresasx );
	for( i in datos ){
		empresas_array.push( datos[i] );
	}
	//Autocompletar para las entidades responsables, cuando seleccione uno llama a buscarcodigoentidad
	$( "#entidad" ).autocomplete({
		source: empresas_array,
		minLength : 0
	});

	$( "#codigo_inicial" ).autocomplete({
		source: articulos_array,
		minLength : 3
	});

	$( "#codigo_final" ).autocomplete({
		source: articulos_array,
		minLength : 3
	});


	var articulos_array = new Array();
	var articulosx = $("#articulos_json").val();
	datos = eval ( articulosx );
	for( i in datos ){
		articulos_array.push( datos[i] );
	}

	var wemp_pmla = $("#wemp_pmla").val();

	llamadoArticulos(wemp_pmla);

	//Esta linea permite que el dropdownlist de registros relacionados se convierta en un dropdownlist con filtro y con multiple seleccion.
	$('#incluir_grupo').multiselect({
											   position: {
												  my: 'left bottom',
												  at: 'left top'

											   },
											selectedText: "# of # seleccionados",
											}).multiselectfilter();

	$('#excluir_grupo').multiselect({
											   position: {
												  my: 'left bottom',
												  at: 'left top'

											   },
											selectedText: "# of # seleccionados"
											}).multiselectfilter();

	$('#excluir_codigo_inicial').multiselect({
											   position: {
												  my: 'left bottom',
												  at: 'left top'

											   },
											selectedText: "# of # seleccionados"
											}).multiselectfilter();




});

function modal_adjuntar_archivo(){

	 var entidad = $("#entidad").val();
	 var entidad = entidad.split("-");
	 var entidad = entidad[0];
	 var fecha_inicio_inc = $("#fecha_inicio_inc").val();
	 var tarifa = $( "#tarifa" ).val();
	 var tarifa = tarifa.split("-");
	 var tarifa = tarifa[0];
	 var tarifa = tarifa.trim();

	 if(entidad == ''){

		 alert("Debe seleccionar una entidad.");
		 return;

	 }

	 if(tarifa == ''){

		 alert("Debe seleccionar una tarifa.");
		 return;

	 }

	 if(fecha_inicio_inc == ''){

		 alert("Debe seleccionar una fecha de inicio del incremento.");
		 return;

	 }


	$("#modal_adjuntar_archivo").dialog({
				modal	: true,
				width	: 500,
				height	: 300,
				title	: "<div align='center'>Incremento con archivo adjunto</div>",

			});

}


function descargar_archivo(){


	$("#archivo_descargar").trigger();

}


function abrir_modal_alertas(cod_alerta, mensaje){

	$("#alerta"+cod_alerta).dialog({
				modal	: true,
				width	: 1000,
				height	: 500,
				title	: "<div align='center'>"+mensaje+"</div>",

			});

}

function abrir_alertas(html){

	$("#"+html).dialog({
				modal	: true,
				width	: 500,
				height	: 300,
				title	: "<div align='center'>Alertas</div>",

			});

}

function tarifas_empresa(){

	var entidad = $("#entidad").val();
	var entidad = entidad.split("-");
	var entidad = entidad[0];

	$.ajax({
			url: "incremento_tarifas.php",
			type: "POST",
			data:{

				wemp_pmla		: $("#wemp_pmla").val(),
				consultaAjax 	: '',
				operacion 		: 'tarifas_empresa',
				entidad			: entidad


			},
			dataType: "html",
			async: true,
			success:function(data_json) {

				if (data_json.error == 1)
				{

				}
				else{

					$("#tarifa").val("");
					$("#tarifas_json").val(data_json);
					var tarifas_array = new Array();

					var tarifax = $("#tarifas_json").val();
					datos = eval ( tarifax );
					for( i in datos ){
						tarifas_array.push( datos[i] );
					}

					$( "#tarifa" ).autocomplete({
						source: tarifas_array,
						minLength : 0
					});
				}
			}

		});

}

function info_alertas(nro_alerta, titulo){

	var entidad = $("#entidad").val();
    var entidad = entidad.split("-");
	var entidad = entidad[0];

	 var tarifa = $( "#tarifa" ).val();
	 var tarifa = tarifa.split("-");
	 var tarifa = tarifa[0];
	 var tarifa = tarifa.trim();

	 var inc_costo_porcentaje = ($('#chk_inc_costo_porcentaje').is(":checked")) ? 'on': 'off';
	 var porc_incremento = $("#porc_incremento").val();

	 var tipo_articulo = $('#tipo_articulo').val();

	$("#loadalerta"+nro_alerta).html('<img src="../../images/medical/ajax-loader5.gif" >');

	$.ajax({
			url: "incremento_tarifas.php",
			type: "POST",
			data:{

				wemp_pmla		: $("#wemp_pmla").val(),
				consultaAjax 	: '',
				operacion 		: 'info_alertas',
				nro_alerta		: nro_alerta,
				titulo			: titulo,
				tarifa			: tarifa,
				entidad			: entidad,
				inc_costo_porcentaje : inc_costo_porcentaje,
				porc_incremento :	porc_incremento,
				tipo_articulo	:	tipo_articulo

			},
			dataType: "json",
			async: true,
			success:function(data_json) {

				if (data_json.error == 1)
				{

				}
				else{

					$("#alerta"+nro_alerta).html(data_json.html_alerta);

					$("#alerta"+nro_alerta).dialog({
						modal	: true,
						width	: 1000,
						height	: 500,
						title	: "<div align='center'>"+titulo+"</div>",

					});

					$("#loadalerta"+nro_alerta).html("");
					$("#lista_articulos_alerta"+nro_alerta).html(data_json.html_oculto_exportar);

				}
			}

		}).done(function(){

			//Excel alerta 1
			// $("#excelAlerta1").click(function () {

				// $("#archivo_descargar").trigger();

	        // });

			//Excel alerta 2
			$("#excelAlerta2").click(function () {

				$("#esperar_exportar").html('<img src="../../images/medical/ajax-loader5.gif" >');

	            // parse the HTML table element having an id=exportTable
	            var dataSource = shield.DataSource.create({
	                data: "#exportTableAlerta2",
	                schema: {
	                    type: "table",
	                    fields: {
	                        Codigo: { type: String },
	                        Generico: { type: String },
	                        Comercial: { type: String },
	                        UN: { type: String },
	                        Invima: { type: String },
	                        Costo: { type: String },
	                        FechaUltcompra: { type: String },
	                        FechaCodificacion: { type: String }
	                    }
	                }
	            });

	            // when parsing is done, export the data to Excel
	            dataSource.read().then(function (data) {
	                new shield.exp.OOXMLWorkbook({
	                    author: "ClinicaLasAmericas",
	                    worksheets: [
	                        {
	                            name: "ListaMedDis",
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
	                                            value: "Generico"
	                                        },
											{
	                                            style: {
	                                                bold: true
	                                            },
	                                            type: String,
	                                            value: "Comercial"
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
	                                            value: "Invima"
	                                        },
											{
	                                            style: {
	                                                bold: true
	                                            },
	                                            type: Number,
	                                            value: "Costo"
	                                        },
											{
	                                            style: {
	                                                bold: true
	                                            },
	                                            type: Number,
	                                            value: "FechaUltcompra"
	                                        },
											{
	                                            style: {
	                                                bold: true
	                                            },
	                                            type: String,
	                                            value: "FechaCodificacion"
	                                        }
	                                    ]
	                                }
	                            ].concat($.map(data, function(item) {
	                                return {
	                                    cells: [
	                                        { type: Number, value: item.Codigo },
	                                        { type: String, value: item.Generico },
	                                        { type: String, value: item.Comercial },
	                                        { type: String, value: item.UN },
	                                        { type: String, value: item.Invima },
	                                        { type: Number, value: item.Costo },
	                                        { type: String, value: item.FechaUltcompra },
	                                        { type: Number, value: item.FechaCodificacion }
	                                    ]
	                                };
	                            }))
	                        }
	                    ]
	                }).saveAs({
	                    fileName: "creadosultimosseismeses"
	                });

					$("#esperar").hide();

	            });
	        });

			//Excel alerta 3
			$("#excelAlerta3").click(function () {

				$("#esperar_exportar").html('<img src="../../images/medical/ajax-loader5.gif" >');

	            // parse the HTML table element having an id=exportTable
	            var dataSource = shield.DataSource.create({
	                data: "#exportTableAlerta3",
	                schema: {
	                    type: "table",
	                    fields: {
	                        Codigo: { type: String },
	                        Tarifa: { type: String },
	                        Generico: { type: String },
	                        Comercial: { type: String },
	                        UN: { type: String },
	                        Invima: { type: String },
	                        Costo: { type: String },
	                        TarifaActual: { type: String },
	                        TarifaIncremento: { type: String },
	                        FechaCodificacion: { type: String }
	                    }
	                }
	            });

	            // when parsing is done, export the data to Excel
	            dataSource.read().then(function (data) {
	                new shield.exp.OOXMLWorkbook({
	                    author: "ClinicaLasAmericas",
	                    worksheets: [
	                        {
	                            name: "ListaMedDis",
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
	                                            value: "Tarifa"
	                                        },
	                                        {
	                                            style: {
	                                                bold: true
	                                            },
	                                            type: String,
	                                            value: "Generico"
	                                        },
											{
	                                            style: {
	                                                bold: true
	                                            },
	                                            type: String,
	                                            value: "Comercial"
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
	                                            value: "Invima"
	                                        },
											{
	                                            style: {
	                                                bold: true
	                                            },
	                                            type: Number,
	                                            value: "Costo"
	                                        },
											{
	                                            style: {
	                                                bold: true
	                                            },
	                                            type: Number,
	                                            value: "TarifaActual"
	                                        },
											{
	                                            style: {
	                                                bold: true
	                                            },
	                                            type: Number,
	                                            value: "TarifaIncremento"
	                                        },
											{
	                                            style: {
	                                                bold: true
	                                            },
	                                            type: String,
	                                            value: "FechaCodificacion"
	                                        }
	                                    ]
	                                }
	                            ].concat($.map(data, function(item) {
	                                return {
	                                    cells: [
	                                          { type: Number, value: item.Codigo },
	                                        { type: String, value: item.Tarifa },
	                                        { type: String, value: item.Generico },
	                                        { type: String, value: item.Comercial },
	                                        { type: String, value: item.UN },
	                                        { type: String, value: item.Invima },
	                                        { type: Number, value: item.Costo },
	                                        { type: Number, value: item.TarifaActual },
	                                        { type: Number, value: item.TarifaIncremento },
	                                        { type: Number, value: item.FechaCodificacion }
	                                    ]
	                                };
	                            }))
	                        }
	                    ]
	                }).saveAs({
	                    fileName: "tarifaigualalcosto"
	                });

					$("#esperar").hide();

	            });
	        });


			//Excel alerta 4
			$("#excelAlerta4").click(function () {

				$("#esperar_exportar").html('<img src="../../images/medical/ajax-loader5.gif" >');

	            // parse the HTML table element having an id=exportTable
	            var dataSource = shield.DataSource.create({
	                data: "#exportTableAlerta4",
	                schema: {
	                    type: "table",
	                    fields: {
	                        Codigo: { type: String },
	                        Tarifa: { type: String },
	                        Generico: { type: String },
	                        Comercial: { type: String },
	                        UN: { type: String },
	                        Invima: { type: String },
	                        Costo: { type: String },
	                        TarifaActual: { type: String },
	                        TarifaIncremento: { type: String },
	                        FechaCodificacion: { type: String }
	                    }
	                }
	            });

	            // when parsing is done, export the data to Excel
	            dataSource.read().then(function (data) {
	                new shield.exp.OOXMLWorkbook({
	                    author: "ClinicaLasAmericas",
	                    worksheets: [
	                        {
	                            name: "ListaMedDis",
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
	                                            value: "Tarifa"
	                                        },
	                                        {
	                                            style: {
	                                                bold: true
	                                            },
	                                            type: String,
	                                            value: "Generico"
	                                        },
											{
	                                            style: {
	                                                bold: true
	                                            },
	                                            type: String,
	                                            value: "Comercial"
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
	                                            value: "Invima"
	                                        },
											{
	                                            style: {
	                                                bold: true
	                                            },
	                                            type: Number,
	                                            value: "Costo"
	                                        },
											{
	                                            style: {
	                                                bold: true
	                                            },
	                                            type: Number,
	                                            value: "TarifaActual"
	                                        },
											{
	                                            style: {
	                                                bold: true
	                                            },
	                                            type: Number,
	                                            value: "TarifaIncremento"
	                                        },
											{
	                                            style: {
	                                                bold: true
	                                            },
	                                            type: String,
	                                            value: "FechaCodificacion"
	                                        }
	                                    ]
	                                }
	                            ].concat($.map(data, function(item) {
	                                return {
	                                    cells: [
	                                        { type: Number, value: item.Codigo },
	                                        { type: String, value: item.Tarifa },
	                                        { type: String, value: item.Generico },
	                                        { type: String, value: item.Comercial },
	                                        { type: String, value: item.UN },
	                                        { type: String, value: item.Invima },
	                                        { type: Number, value: item.Costo },
	                                        { type: Number, value: item.TarifaActual },
	                                        { type: Number, value: item.TarifaIncremento },
	                                        { type: Number, value: item.FechaCodificacion }
	                                    ]
	                                };
	                            }))
	                        }
	                    ]
	                }).saveAs({
	                    fileName: "tarifadebajodelcosto"
	                });

					$("#esperar").hide();

	            });
	        });


			//Excel alerta 5
			$("#excelAlerta5").click(function () {

				$("#esperar_exportar").html('<img src="../../images/medical/ajax-loader5.gif" >');

	            // parse the HTML table element having an id=exportTable
	            var dataSource = shield.DataSource.create({
	                data: "#exportTableAlerta5",
	                schema: {
	                    type: "table",
	                    fields: {
	                        Codigo: { type: String },
	                        Tarifa: { type: String },
	                        Generico: { type: String },
	                        Comercial: { type: String },
	                        UN: { type: String },
	                        Invima: { type: String },
	                        Costo: { type: String },
	                        FechaUltcompra: { type: String },
	                        TarifaActual: { type: String },
	                        PorcentajeRentabilidad: { type: String },
	                        TarifaIncremento: { type: String },
	                        FechaCodificacion: { type: String }
	                    }
	                }
	            });

	            // when parsing is done, export the data to Excel
	            dataSource.read().then(function (data) {
	                new shield.exp.OOXMLWorkbook({
	                    author: "ClinicaLasAmericas",
	                    worksheets: [
	                        {
	                            name: "ListaMedDis",
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
	                                            value: "Tarifa"
	                                        },
	                                        {
	                                            style: {
	                                                bold: true
	                                            },
	                                            type: String,
	                                            value: "Generico"
	                                        },
											{
	                                            style: {
	                                                bold: true
	                                            },
	                                            type: String,
	                                            value: "Comercial"
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
	                                            value: "Invima"
	                                        },
											{
	                                            style: {
	                                                bold: true
	                                            },
	                                            type: Number,
	                                            value: "Costo"
	                                        },
											{
	                                            style: {
	                                                bold: true
	                                            },
	                                            type: Number,
	                                            value: "TarifaActual"
	                                        },
											{
	                                            style: {
	                                                bold: true
	                                            },
	                                            type: Number,
	                                            value: "PorcentajeRentabilidad"
	                                        },
											{
	                                            style: {
	                                                bold: true
	                                            },
	                                            type: Number,
	                                            value: "TarifaIncremento"
	                                        },
											{
	                                            style: {
	                                                bold: true
	                                            },
	                                            type: String,
	                                            value: "FechaCodificacion"
	                                        }
	                                    ]
	                                }
	                            ].concat($.map(data, function(item) {
	                                return {
	                                    cells: [
	                                        { type: Number, value: item.Codigo },
	                                        { type: String, value: item.Tarifa },
	                                        { type: String, value: item.Generico },
	                                        { type: String, value: item.Comercial },
	                                        { type: String, value: item.UN },
	                                        { type: String, value: item.Invima },
	                                        { type: Number, value: item.Costo },
	                                        { type: Number, value: item.TarifaActual },
	                                        { type: Number, value: item.PorcentajeRentabilidad },
	                                        { type: Number, value: item.TarifaIncremento },
	                                        { type: Number, value: item.FechaCodificacion }
	                                    ]
	                                };
	                            }))
	                        }
	                    ]
	                }).saveAs({
	                    fileName: "rentabilidadmenora10porc"
	                });

					$("#esperar").hide();

	            });
	        });
		});

}



function filtrar_grupos_excluidos(){

	 var grupo_inicial = $("#grupo_inicial").val();
	 var grupo_inicial = grupo_inicial.split("-");
	 var grupo_inicial = grupo_inicial[0];
	 var grupo_final = $("#grupo_final").val();
	 var grupo_final = grupo_final.split("-");
	 var grupo_final = grupo_final[0];

	 $.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
						css: 	{
									width: 	'auto',
									height: 'auto'
								}
				 });

	 if(grupo_final != ''){

	$.ajax({
			url: "incremento_tarifas.php",
			type: "POST",
			data:{

				wemp_pmla		: $("#wemp_pmla").val(),
				consultaAjax 	: '',
				operacion 		: 'filtrar_grupos_excluidos',
				grupo_inicial	: grupo_inicial,
				grupo_final		: grupo_final

			},
			dataType: "json",
			async: true,
			success:function(data_json) {

				if (data_json.error == 1)
				{

				}
				else{

					$('#select_excluir_grupo').html("");
					$('#select_excluir_grupo').html(data_json.html);

					//Esta linea permite que el dropdownlist de registros relacionados se convierta en un dropdownlist con filtro y con multiple seleccion.
					$('#excluir_grupo').multiselect({
															   position: {
																  my: 'left bottom',
																  at: 'left top'

															   },
															selectedText: "# of # seleccionados",
															click: function(event, ui){
																	var dato = $("#excluir_grupo option[value='"+ ui.value +"']").text()+"\n";
																	$('#text_excluir_grupo').val($('#text_excluir_grupo').val() + dato);

															},
															}).multiselectfilter();
					$('#excluir_grupo').multiselect("uncheckAll");

					//Se crea de nuevo el seleccionador de grupos
					$('#select_incluir_grupo').html("");
					$('#select_incluir_grupo').html(data_json.html_grupos);
					$('#incluir_grupo').multiselect({
											   position: {
												  my: 'left bottom',
												  at: 'left top'

											   },
											selectedText: "# of # seleccionados",
											click: function(event, ui){
															var dato = $("#incluir_grupo option[value='"+ ui.value +"']").text()+"\n";
															$('#text_incluir_grupo').val($('#text_incluir_grupo').val() + dato);

															},
											}).multiselectfilter();

					$('#incluir_grupo').multiselect("uncheckAll");
					$('#text_excluir_grupo').removeAttr("disabled");
					$('#text_incluir_grupo').removeAttr("disabled");

					$.unblockUI();
				}
			}

		});

	 }else{

		 jAlert("Debe seleccionar un grupo final","Mensaje");
		 return;
	 }



}


function filtrar_codigos_excluidos(){

     var grupo_inicial = $("#grupo_inicial").val();
	 var grupo_inicial = grupo_inicial.split("-");
	 var grupo_inicial = grupo_inicial[0];
	 var grupo_final = $("#grupo_final").val();
	 var grupo_final = grupo_final.split("-");
	 var grupo_final = grupo_final[0];

	 var codigo_inicial = $("#codigo_inicial").val();
	 var codigo_inicial = codigo_inicial.split("-");
	 var codigo_inicial = codigo_inicial[0];
	 var codigo_final = $("#codigo_final").val();
	 var codigo_final = codigo_final.split("-");
	 var codigo_final = codigo_final[0];

	$.ajax({
			url: "incremento_tarifas.php",
			type: "POST",
			data:{

				wemp_pmla		: $("#wemp_pmla").val(),
				consultaAjax 	: '',
				operacion 		: 'filtrar_codigos_excluidos',
				codigo_inicial	: codigo_inicial,
				codigo_final	: codigo_final,
				grupo_inicial	: grupo_inicial,
				grupo_final		: grupo_final

			},
			dataType: "json",
			async: true,
			success:function(data_json) {

				if (data_json.error == 1)
				{

				}
				else{

					$('#select_excluir_codigo_inicial').html("");
					$('#select_excluir_codigo_inicial').html(data_json.html);

					//Esta linea permite que el dropdownlist de registros relacionados se convierta en un dropdownlist con filtro y con multiple seleccion.
					$('#excluir_codigo_inicial').multiselect({
															   position: {
																  my: 'left bottom',
																  at: 'left top'

															   },
															selectedText: "# of # seleccionados",
															click: function(event, ui){
															var dato = $("#excluir_codigo_inicial option[value='"+ ui.value +"']").text()+"\n";
															$('#text_excluir_codigo_inicial').val($('#text_excluir_codigo_inicial').val() + dato);

															},
														}).multiselectfilter();

					$('#excluir_codigo_inicial').multiselect("uncheckAll");
					$('#incluir_codigo_inicial').removeAttr("disabled");
					$('#text_excluir_codigo_inicial').removeAttr("disabled");
					$('#text_incluir_codigo_inicial').removeAttr("disabled");

				}
			}

		});


}


function excluir_alerta(id_alerta, info_alerta){

	var datos_excluidos = $("#datos_"+id_alerta).val();
	console.log($("#texto_"+id_alerta));

	$("#excluir_articulos_"+id_alerta).val(datos_excluidos);

	$("#"+id_alerta).dialog( "close" );

	$("#html_alertas").dialog( "close" );

	$("#titulo_alertas_excluidas").show();

	var html_excluidas = $("#alertas_excluidas").html();

	$("#alertas_excluidas").html(info_alerta+"<br>"+html_excluidas); //Pone le texto en el listado de alertas excluidas.

	var text_alerta_excluida = "texto_"+id_alerta;

	mostrar_art('', text_alerta_excluida);



}

function split( val ) {
  return val.split( /,\s*/ );
}

function extractLast( term ) {
  return split( term ).pop();
}

function llamadoArticulos(wemp_pmla){


	$.ajax({
			url: "incremento_tarifas.php",
			type: "POST",
			data:{
				consultaAjax 	: '',
				operacion 		: 'cargar_articulos',
				wemp_pmla 		: wemp_pmla
			},
			dataType: "html",
			async: true,
			success:function(data_json) {

				if (data_json.error == 1)
				{

				}
				else{

					$("#articulos_json").val(data_json);
					var articulos_array = new Array();

					var articulosx = $("#articulos_json").val();
					datos = eval ( articulosx );
					for( i in datos ){
						articulos_array.push( datos[i] );
					}

					$( "#codigo_inicial" ).autocomplete({
						source: articulos_array,
						minLength : 3
					});

					$( "#codigo_final" ).autocomplete({
						source: articulos_array,
						minLength : 3
					});

					$( "#incluir_codigo_inicial" )
						  // don't navigate away from the field on tab when selecting an item
						  .on( "keydown", function( event ) {
							if ( event.keyCode === $.ui.keyCode.TAB &&
								$( this ).autocomplete( "instance" ).menu.active ) {
							  event.preventDefault();
							}
						  })
						  .autocomplete({
							minLength: 3,
							source: function( request, response ) {
							  // delegate back to autocomplete, but extract the last term
							  response( $.ui.autocomplete.filter(
								articulos_array, extractLast( request.term ) ) );
							},
							focus: function() {
							  // prevent value inserted on focus
							  return false;
							},
							select: function( event, ui ) {
							  var terms = split( $("#text_incluir_codigo_inicial").val() );
							  // remove the current input
							  terms.pop();
							  // add the selected item
							  terms.push( ui.item.value );

							  terms.push( "" );
							  this.value = terms.join( "\n" );
							  $("#text_incluir_codigo_inicial").val($("#text_incluir_codigo_inicial").val() + this.value + "\n");
							  $("#incluir_codigo_inicial").val("");

							  return false;
							}
						  })
				}
			}

		});


}

function activar_campo(campo, este){

	$("#"+campo).removeAttr("disabled","");

	if($(este).val() == ''){

		$("#"+campo).attr("disabled","");
	}

}


function control_campo_incremento(campo1, campo2, incremento_costo, este){

	var inc_costo_checked = ($("#chk_inc_costo_"+incremento_costo).is(":checked")) ? true:false; // Guarda temporalmente si estaba chequeado.
	$(":checkbox[id^=chk_inc_costo_]").removeAttr("disabled");
	$(":checkbox[id^=chk_inc_costo_]").removeAttr("checked");
	if($(este).val() != ""){
		$("#"+campo1).attr("disabled","disabled");
		$("#"+campo2).attr("disabled","disabled");
		$(":checkbox[id^=chk_inc_costo_]").attr("disabled","disabled");
		if(inc_costo_checked)
		{
			$("#chk_inc_costo_"+incremento_costo).attr("checked","checked");
		}
		$("#chk_inc_costo_"+incremento_costo).removeAttr("disabled");
		// $("#chk_inc_costo_"+incremento_costo).attr("disabled");
	}else{
		$("#"+campo1).removeAttr("disabled");
		$("#"+campo2).removeAttr("disabled");
	}
}

function limpiar_resultado(){

	location.reload();

}


var largo = 7;

function mostrar_art(actualizar, alerta_excluida){

	if(actualizar == 'on'){

			var r = confirm("¿Desea incrementar estos articulos?");

			if (r == true) {

				var actualizar = "on";

			}else{

				var actualizar = "off";
				return;
			}

		}

	 var entidad = $("#entidad").val();
	 var entidad = entidad.split("-");
	 var entidad = entidad[0];
	 var fecha_inicio_inc = $("#fecha_inicio_inc").val();
	 var cco = $( "#cco" ).val();
	 var tarifa = $( "#tarifa" ).val();
	 var tarifa = tarifa.split("-");
	 var tarifa = tarifa[0];
	 var tarifa = tarifa.trim();
	 var grupo_inicial = $("#grupo_inicial").val();
	 var grupo_inicial = grupo_inicial.split("-");
	 var grupo_inicial = grupo_inicial[0];
	 var grupos_excluidos = $("#text_excluir_grupo").val();
	 var grupos_incluidos = $("#text_incluir_grupo").val();
	 var grupo_final = $("#grupo_final").val();
	 var grupo_final = grupo_final.split("-");
	 var grupo_final = grupo_final[0];
	 var codigo_inicial = $("#codigo_inicial").val();
	 var codigo_inicial = codigo_inicial.split("-");
	 var codigo_inicial = codigo_inicial[0];
	 var codigo_final = $("#codigo_final").val();
	 var codigo_final = codigo_final.split("-");
	 var codigo_final = codigo_final[0];
	 var articulos_excluidos = $("#text_excluir_codigo_inicial").val();
	 var articulos_excluidos_alerta1 = $("#excluir_articulos_alerta1").val();
	 var articulos_excluidos_alerta2 = $("#excluir_articulos_alerta2").val();
	 var articulos_excluidos_alerta3 = $("#excluir_articulos_alerta3").val();
	 var articulos_excluidos_alerta4 = $("#excluir_articulos_alerta4").val();
	 var articulos_excluidos_alerta5 = $("#excluir_articulos_alerta5").val();
	 var articulos_incluidos = $("#text_incluir_codigo_inicial").val();
	 var regulado = $("#regulado").val();
	 var redondeo = $("#redondeo").val();
	 var pareto = $("#pareto").val();
	 var porc_incremento = $("#porc_incremento").val();
	 var inc_tarifa_fija = $("#inc_tarifa_fija").val();
	 var inc_valor_adicional = $("#inc_valor_adicional").val();
	 var pos = $("#pos").val();
	 var tipo_articulo = $('#tipo_articulo').val();
	 var articulo_idc = $('#articulo_idc').val();
	 var observaciones = $('#observaciones').val();
	 var inc_costo_porcentaje = ($('#chk_inc_costo_porcentaje').is(":checked")) ? 'on': 'off';
	 var inc_costo_adicional  = ($('#chk_inc_costo_adicional').is(":checked")) ? 'on': 'off';
	 var registros_pagina = 30;
	$(".campoRequerido").removeClass("campoRequerido");

	if(entidad == '' && tarifa != '*'){

		 var r = confirm("¿El formulario no tiene una entidad seleccionada, esta seguro de realizar la consulta?");

			if (r == true) {

			}else{
				$("#entidad").focus();
				return;
			}

	 }

	if(fecha_inicio_inc == ''){
		$("#fecha_inicio_inc").addClass('campoRequerido');
		jAlert("Fecha inicio incremento es obligatorio","Mensaje");
		$("#fecha_inicio_inc").focus();
		return;
	}

	if(tarifa == ''){
		$("#tarifa").addClass('campoRequerido');
		 jAlert("Debe seleccionar una tarifa.","Mensaje");
		 $("#tarifa").focus();
		 return;
	 }

	var validacion = false;
	$(".valor_incremento").each(function(){

		if($(this).val() != ''){

			validacion = true;
		}

	})

	if(validacion == false){

		jAlert("Debe seleccionar un valor de incremento.","Mensaje");
		return;
	}



	 if(entidad == '' && pareto != ''){
	 	 $("#entidad").addClass('campoRequerido');
		 jAlert("No puede seleccionar pareto si no tiene una entidad relacionada, favor seleccione la entidad","Mensaje");
		 $("#entidad").focus();
		 return;
	 }


	 $("#esperar").show();
	 $("#esperar").html('<img src="../../images/medical/ajax-loader5.gif" >');

	var obJson                     = {};
	obJson['consultaAjax']         = '';
	obJson['operacion']            = 'mostrar_art';
	obJson['wemp_pmla']            = $("#wemp_pmla").val();
	obJson['entidad']              = entidad;
	obJson['fecha_inicio_inc']     = fecha_inicio_inc;
	obJson['cco']                  = cco;
	obJson['tarifa']               = tarifa;
	obJson['grupo_inicial']        = grupo_inicial;
	obJson['grupo_final']          = grupo_final;
	obJson['codigo_inicial']       = codigo_inicial;
	obJson['codigo_final']         = codigo_final;
	obJson['regulado']             = regulado;
	obJson['redondeo']             = redondeo;
	obJson['pareto']               = pareto;
	obJson['porc_incremento']      = porc_incremento;
	obJson['inc_tarifa_fija']      = inc_tarifa_fija;
	obJson['inc_valor_adicional']  = inc_valor_adicional;
	obJson['pos']                  = pos;
	obJson['tipo_articulo']        = tipo_articulo;
	obJson['registros_inicial']    = (1*registros_pagina) - registros_pagina;
	obJson['registros_final']      = registros_pagina;
	obJson['actualizar']           = actualizar;
	obJson['grupos_excluidos']     = grupos_excluidos;
	obJson['grupos_incluidos']     = grupos_incluidos;
	obJson['articulos_excluidos']  = articulos_excluidos;
	obJson['articulos_excluidos_alerta1']  = articulos_excluidos_alerta1;
	obJson['articulos_excluidos_alerta2']  = articulos_excluidos_alerta2;
	obJson['articulos_excluidos_alerta3']  = articulos_excluidos_alerta3;
	obJson['articulos_excluidos_alerta4']  = articulos_excluidos_alerta4;
	obJson['articulos_excluidos_alerta5']  = articulos_excluidos_alerta5;
	obJson['articulos_incluidos']  = articulos_incluidos;
	obJson['articulo_idc']         = articulo_idc;
	obJson['observaciones']        = observaciones;
	obJson['inc_costo_porcentaje'] = inc_costo_porcentaje;
	obJson['inc_costo_adicional']  = inc_costo_adicional;

	 $.ajax({
				url: "incremento_tarifas.php",
				type: "POST",
				data: obJson,
				dataType: "json",
				async: true,
				success:function(data_json) {

					if (data_json.error == 1)
					{
						$("#pareto").val("");
						$("#esperar").hide();
						jAlert(data_json.mensaje,"Mensaje");
						return;
					}
					else{

						$("#esperar").hide();
						$("#cerrar_arriba").show();
						$("#lista_articulos").html(data_json.html);
						$("#lista_articulos_oculto").html(data_json.html_oculto);

						var total_registros = data_json.total_registros;
						var cant_registros_pagina = data_json.cant_registros_pagina;

						if(data_json.query_update_ok == 'on'){
							$("#lista_articulos").html("");
							jAlert(data_json.mensaje_update,"Mensaje");
							return;
						}

						$("#alerta_articulos").show();

						if(alerta_excluida != ''){

							$("#"+alerta_excluida).remove(); //Destruye el div que contiene el listado de articulos de la alerta excluida.

						}

						//Elimina el boton de alertas cuando se excluyen todas las alertas.
						if($("#html_alertas").find('div').length == 0){

							$("#alerta_articulos").remove();
						}

						if(data_json.total_registros > 0){

							$('#paginador').smartpaginator({

								totalrecords: total_registros,
								recordsperpage: cant_registros_pagina,
								length: largo,
								next: 'Sig',
								prev: 'Atras',
								first: 'Inicio',
								last: 'Ulti',
								go: 'Ir',
								theme: 'black',
								controlsalways: true,
								onchange:
								function (newPage) {
											$("#esperar").show();
											$("#esperar").html('<img src="../../images/medical/ajax-loader5.gif" >');

											obJson['registros_inicial'] = (newPage*registros_pagina) - registros_pagina;

											$.ajax({
											url: "incremento_tarifas.php",
											type: "POST",
											data: obJson,
											async: true,
											dataType: "json",
											success:function(data_json) {

												if (data_json.error == 1)
												{
													jAlert(data_json.mensaje,"Mensaje");
													return;
												}
												else{
													$("#esperar").hide();
													$("#lista_articulos").html(data_json.html);
												}
											}
										}
									)
								}
							});
					}else{

						$("#lista_articulos").html("La relacion de articulos no se encuentra.");
					}
				}
			}

		}).done(function(){

			$("#exportButton").click(function () {

				$("#esperar").show();
				$("#esperar").html('<img src="../../images/medical/ajax-loader5.gif" >');

	            // parse the HTML table element having an id=exportTable
	            var dataSource = shield.DataSource.create({
	                data: "#exportTable",
	                schema: {
	                    type: "table",
	                    fields: {
	                        Codigo: { type: String },
	                        Tarifa: { type: String },
	                        Generico: { type: String },
	                        Comercial: { type: String },
	                        UN: { type: String },
	                        Grupo: { type: String },
	                        POS: { type: String },
	                        CUM: { type: String },
	                        Invima: { type: String },
	                        Costo: { type: String },
	                        TarifaActual: { type: String },
	                        TarifaIncremento: { type: String },
	                        Clasificacion: { type: String },
	                        FechaCodificacion: { type: String }
	                    }
	                }
	            });

	            // when parsing is done, export the data to Excel
	            dataSource.read().then(function (data) {
	                new shield.exp.OOXMLWorkbook({
	                    author: "ClinicaLasAmericas",
	                    worksheets: [
	                        {
	                            name: "ListaMedDis",
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
	                                            value: "Tarifa"
	                                        },
	                                        {
	                                            style: {
	                                                bold: true
	                                            },
	                                            type: String,
	                                            value: "Generico"
	                                        },
											{
	                                            style: {
	                                                bold: true
	                                            },
	                                            type: String,
	                                            value: "Comercial"
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
	                                            value: "Grupo"
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
	                                            type: String,
	                                            value: "CUM"
	                                        },
											{
	                                            style: {
	                                                bold: true
	                                            },
	                                            type: String,
	                                            value: "Invima"
	                                        },
											{
	                                            style: {
	                                                bold: true
	                                            },
	                                            type: Number,
	                                            value: "Costo"
	                                        },{
	                                            style: {
	                                                bold: true
	                                            },
	                                            type: Number,
	                                            value: "TarifaActual"
	                                        },
											{
	                                            style: {
	                                                bold: true
	                                            },
	                                            type: Number,
	                                            value: "TarifaIncremento"
	                                        },
											{
	                                            style: {
	                                                bold: true
	                                            },
	                                            type: Number,
	                                            value: "Clasificacion"
	                                        },
											{
	                                            style: {
	                                                bold: true
	                                            },
	                                            type: String,
	                                            value: "FechaCodificacion"
	                                        }
	                                    ]
	                                }
	                            ].concat($.map(data, function(item) {
	                                return {
	                                    cells: [
	                                        { type: Number, value: item.Codigo },
	                                        { type: String, value: item.Tarifa },
	                                        { type: String, value: item.Generico },
	                                        { type: String, value: item.Comercial },
	                                        { type: String, value: item.UN },
	                                        { type: String, value: item.Grupo },
	                                        { type: String, value: item.POS },
	                                        { type: String, value: item.CUM },
	                                        { type: String, value: item.Invima },
	                                        { type: Number, value: item.Costo },
	                                        { type: Number, value: item.TarifaActual },
	                                        { type: Number, value: item.TarifaIncremento },
	                                        { type: Number, value: item.Clasificacion },
	                                        { type: Number, value: item.FechaCodificacion }
	                                    ]
	                                };
	                            }))
	                        }
	                    ]
	                }).saveAs({
	                    fileName: "ReporteExcelIncrementos"
	                });

					$("#esperar").hide();

	            });
	        });


		}).fail(function(xhr, textStatus, errorThrown) { mensajeFailAlert('', xhr, textStatus, errorThrown); });
}

	function activar_radio_button(){

		$("#id_radio_tipo_porcentaje").removeAttr("disabled");
		$("#id_tipo_tarifa").removeAttr("disabled");

		if($("#redondeo").val() == ''){

			alert("No ha seleccionado redondeo (opcional)");

		}


	}


	function validar_tipo_incremento_modal(){


		$("#id_radio_tipo_porcentaje").prop('checked', false);
		$("#id_radio_tipo_porcentaje").prop('disabled', 'disabled');
		$("#id_tipo_tarifa").prop('checked', false);
		$("#id_tipo_tarifa").prop('disabled', 'disabled');


	}

    function trOver(grupo)
    {
        $(grupo).addClass('classOver');
    }

    function trOut(grupo)
    {
        $(grupo).removeClass('classOver');
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
    width : 320px;
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

.boton{
	background-color:#C3D9FF;
    border: 1px solid #000000;
    border-radius: 7px;
	cursor: pointer;
	color: #000000;
	padding: 1;
	text-align: center;

}


#div_content{
    /*border: 2px solid #e0e0e0;*/
    /*background-color: #e6e6e6;*/
    /*border-top: 0px;*/
    font-family: Verdana;
    font-size: 11pt;
}


.classOver{
    background-color: #CCCCCC;
}

.campoRequerido{
	border: 1px orange solid;
	background-color:lightyellow;
}
</style>
<body>

<?php
encabezado( "INCREMENTO TARIFAS MEDICAMENTOS Y DISPOSITIVOS", $actualiz ,"clinica" );

$empid = consultarEmpresas();
$empresas = json_encode( $empid );

$tarid = consultarTarifas();
$tarifas = json_encode( $tarid );

$gruposid = consultarGrupos();
$grupos = json_encode( $gruposid );
?>

<input type="hidden" id="wemp_pmla" value="<?=$wemp_pmla?>">
<center>
<br>
<form>
<div id="div_content"  class='ui-tabs ui-widget ui-widget-content ui-corner-all'>
<table style="text-align: right;" border="0" cellpadding="0" cellspacing="1">
  <tbody>
	<tr align="center" class="encabezadoTabla">
      <td colspan="4" rowspan="1">INCREMENTO TARIFA MEDICAMENTOS</td>
    </tr>
    <tr>
      <td class="fila1" ><b>Entidad:</b></td>
      <td class="fila2" align=left><input id="entidad" size="41"  onchange="tarifas_empresa();"><input type='hidden' id='empresas_json' value='<?=$empresas?>'></td>
	  <!-- <td style="width: 28px;"></td> -->
      <td class="fila1" nowrap><b>Tarifa:</b></td>
      <td class="fila2" align=left nowrap>
      		<input type="text" id="tarifa" size="30">&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-weight:bold;padding-top: 0.5em;padding-bottom: 0.5em;" class="fila1">Fecha inicio incremento:</span> <input id="fecha_inicio_inc" size="10" readonly>
      		<input type='hidden' id='tarifas_json' value='<?=$tarifas?>'>
		</td>
    </tr>
    <tr>
      <td class="fila1" nowrap><b>Grupo Articulos Inicial:</b></td>
      <td class="fila2" align=left>
		  <input type='hidden' id='grupos_json' value='<?=$grupos?>'>
		  <input type="text" id="grupo_inicial" size="41" onchange="activar_campo('grupo_final', this)">
	   </td>
      <td class="fila1" nowrap><b>Grupo Articulos Final:</b></td>
	  <td class="fila2" align=left><input type="text" id="grupo_final" disabled onchange="filtrar_grupos_excluidos();" size="41"></td>
    </tr>
    <tr>
      <td class="fila1" ><b>Excluir grupo:</b></td>
      <td class="fila2" align=left>
	  	<div id="select_excluir_grupo" style="display: inline;">
	  		<select id="excluir_grupo" multiple="multiple" disabled>
			<?php
			  foreach($gruposid as $key => $value){
				echo '<option value="'.$value.'">'.$value.'</option>';
			  }
			  ?>
     		</select>
      	</div><br><textarea id="text_excluir_grupo" rows="4" cols="36" disabled></textarea></td>
      <td class="fila1" ><b>Incluir grupo:</b></td>
	  <td class="fila2" align=left>
		 <div id="select_incluir_grupo" style="display: inline;">
			 <select id="incluir_grupo" multiple="multiple" disabled>
			<?php
			  foreach($gruposid as $key => $value){
				echo '<option value="'.$value.'">'.$value.'</option>';
			  }
		  	?>
		    </select>
	    </div>
	    <br><textarea id="text_incluir_grupo" rows="4" cols="36" disabled></textarea>
      </td>
    </tr>
    <tr>
      <td class="fila1" ><b>Código Inicial:</b></td>
      <td class="fila2" align=left><input id="codigo_inicial" size="41" onchange="activar_campo('codigo_final', this)"><input type='hidden' id='articulos_json' value=''></td>
      <td class="fila1" ><b>Código Final:</b></td>
      <td class="fila2" align=left><input id="codigo_final" disabled onchange="filtrar_codigos_excluidos();" size="41"></td>
    </tr>
	<tr>
      <td class="fila1" ><b>Excluir Código:</b></td>
      <td class="fila2" align=left><div id="select_excluir_codigo_inicial" style="display: inline;"><select id="excluir_codigo_inicial" multiple="multiple" disabled></select></div>
	  <br><textarea id="text_excluir_codigo_inicial" rows="4" cols="50"></textarea></td>
      <td class="fila1" ><b>Incluir Código:</b></td>
      <td class="fila2" align=left><input type=text id="incluir_codigo_inicial" disabled size="41"><br><textarea id="text_incluir_codigo_inicial" rows="4" cols="50" ></textarea></td>
    </tr>
    <tr>
      <td class="fila1" ><b>Regulados:</b></td>
      <td class="fila2" align=left><select id="regulado"><option value="">Sin condición</option><option value="S">Si</option><option value="N">No</option></select></td>
      <td class="fila1" ><b>Redondear:</b></td>
      <td class="fila2" align=left>
		<select id="redondeo">
			<option value=""></option>
			<option value="">Sin redondeo</option>
			<option value="1">Al primer decimal</option>
			<option value="0">Cero decimales</option>
			<option value="-1">Decena</option>
			<option value="-2">Centena</option>
			<option value="-3">Miles</option>
		</select><span class="tooltip">&nbsp;&nbsp;<img src="../../images/medical/root/info.png" width=11 heigth=11><span class="tooltiptext">
		Ejemplo: <br>
		$ 27,421.68 sin redondeo <br>
		$ 27,421.70 al primer decimal <br>
		$ 27,422 cero decimales <br>
		$ 27,420 decena <br>
		$ 27,400 centena <br>
		$ 27,000 miles</span> </span></td>
    </tr>
    <tr>
      <td class="fila1" ><b>Pareto:</b></td>
      <td class="fila2" align=left><select id="pareto"><option value="">Sin condición</option><option value="S">Si</option><option value="N">No</option></select></td>
      <td class="fila1 lb_porcentaje" ><b>% Incremento:</b></td>
      <td class="fila2" align=left><input id="porc_incremento" class="valor_incremento lb_porcentaje" onKeyPress="return soloNumeros(event)" onchange="control_campo_incremento('inc_tarifa_fija','inc_valor_adicional', 'porcentaje', this)">
      		&nbsp;<span style="font-weight:bold;padding-top: 0.8em;padding-bottom: 0.5em;padding-left: 0.5em;border-left: 1px solid #f2f2f2;" class="fila1 lb_porcentaje" onmouseover="trOver($('.lb_porcentaje'));" onmouseout="trOut($('.lb_porcentaje'));" >% Incremento basado en costo</span> <input type="checkbox" value="" id="chk_inc_costo_porcentaje" onmouseover="trOver($('.lb_porcentaje'));" onmouseout="trOut($('.lb_porcentaje'));"></td>
    </tr>
    <tr>
      <td class="fila1" ><b>POS:</b></td>
      <td class="fila2" align=left><select id="pos"><option value="">Sin condición</option><option value="S">Si</option><option value="N">No</option></select></td>
      <td class="fila1 lb_adicional"><b>Valor(Fijar tarifa):</b></td>
      <td class="fila2" align=left><input type="text" id="inc_tarifa_fija" class="valor_incremento" onKeyPress="return soloNumeros(event)" onchange="control_campo_incremento('porc_incremento','inc_valor_adicional', '', this)"></td>
    </tr>
	<tr>
      <td class="fila1" ><b>Tipo Articulo:</b></td>
      <td class="fila2" align=left><select id="tipo_articulo"><option value=""></option><option value="on">Medicamento</option><option value="off">Dispositivo</option></select></td>
      <td class="fila1" ><b>Valor adicional:</b></td>
      <td class="fila2" align=left><input type="text" id="inc_valor_adicional" class="valor_incremento lb_adicional" onKeyPress="return soloNumeros(event)" onchange="control_campo_incremento('porc_incremento','inc_tarifa_fija', 'adicional', this)">
      		&nbsp;<span style="font-weight:bold;padding-top: 0.8em;padding-bottom: 0.5em;padding-left: 0.5em;border-left: 1px solid #f2f2f2;" class="fila1 lb_adicional" onmouseover="trOver($('.lb_adicional'));" onmouseout="trOut($('.lb_adicional'));" >Valor adicional basado en costo</span> <input type="checkbox" value="" id="chk_inc_costo_adicional" onmouseover="trOver($('.lb_adicional'));" onmouseout="trOut($('.lb_adicional'));"></td>
    </tr>
	<tr>
      <td class="fila1" ><b>Articulo IDC:</b></td>
      <td class="fila2" align=left><select id="articulo_idc"><option value="">Sin condición</option><option value="Si">Si</option><option value="No">No</option></select></td>
      <td class="fila1" ><b>Incremento por archivo:</b></td>
      <td class="fila2" align=left><input type="button" value="Adjuntar" onclick="modal_adjuntar_archivo();"></td>
    </tr>
	<tr>
      <td class="fila1" ><b>Observaciones:</b></td>
      <td class="fila2" colspan="3" align="center"><textarea id="observaciones" rows="4" cols="150"></textarea></td>
    </tr>

	<tr align="center">
      <td colspan="4" rowspan="1"><br><input value="Consultar Articulos" onclick="mostrar_art('');" type="button"><input name="Limpiar" value="Limpiar" onclick="limpiar_resultado();" type="reset">
    <fieldset id='titulo_alertas_excluidas' style="display:none;"><legend>Alertas Excluidas:</legend><span id='alertas_excluidas'></span></fieldset></td><td><span id="alerta_articulos" style="display:none;"><input type="button" value="Alertas" class="blink" onclick="abrir_alertas('html_alertas')"></span></td>
    </tr>
  </tbody>
</table>
<div id=esperar></div>
</form>
</center>
<center><div id="html_alertas" style="display:none;">
			<br>
			<br>
			<div id="texto_alerta1" class="boton" onclick="info_alertas('1','Articulos que no se han comprado en los ultimos 2 años.' )">Articulos que no se han comprado en los ultimos 2 años. <span id="loadalerta1"></span></div><br>
			<div id="texto_alerta2" class="boton" onclick="info_alertas('2','Articulos creados en los últimos 6 meses.')">Articulos creados en los últimos 6 meses. <span id="loadalerta2"></span></div><br>
			<div id="texto_alerta3" class="boton" onclick="info_alertas('3','Articulos cuya tarifa de facturación es igual al costo.')">Articulos cuya tarifa de facturación es igual al costo. <span id="loadalerta3"></span></div><br>
			<div id="texto_alerta4" class="boton" onclick="info_alertas('4','Articulos cuya tarifa de facturación esta por debajo del costo.')">Articulos cuya tarifa de facturación esta por debajo del costo. <span id="loadalerta4"></span></div><br>
			<div id="texto_alerta5" class="boton" onclick="info_alertas('5','Articulos cuyo margen de rentabilidad es menor al 10 %')">Articulos cuyo margen de rentabilidad es menor al 10 %. <span id="loadalerta5"></span></div>
		</div>
		<div id='alerta1' style="display:none;"></div>
		<div id='alerta2' style="display:none;"></div>
		<div id='alerta3' style="display:none;"></div>
		<div id='alerta4' style="display:none;"></div>
		<div id='alerta5' style="display:none;"></div>
		</center><br>
<center><div id="paginador"></div></center>
<br><center><div id="cerrar_arriba"  style="display:none;"><INPUT type='button' value='Cerrar' onclick='cerrarVentana();'></div></center>
<center><div id="lista_articulos"></div></center><br>
<center>
<div id="lista_articulos_oculto" style="display:none"></div>
<div id="lista_articulos_alerta1" style="display:none"></div>
<div id="lista_articulos_alerta2" style="display:none"></div>
<div id="lista_articulos_alerta3" style="display:none"></div>
<div id="lista_articulos_alerta4" style="display:none"></div>
<div id="lista_articulos_alerta5" style="display:none"></div>

</center><br>

<center><div id="query_update"></div></center>
<input type="hidden" id="excluir_articulos_alerta1" value="">
<input type="hidden" id="excluir_articulos_alerta2" value="">
<input type="hidden" id="excluir_articulos_alerta3" value="">
<input type="hidden" id="excluir_articulos_alerta4" value="">
<input type="hidden" id="excluir_articulos_alerta5" value="">
<br><center><INPUT type='button' value='Cerrar' onclick='cerrarVentana();'></center>
<br>

<div style="display:none;" id="modal_adjuntar_archivo">
	<div align="center">
		<form id="subida">
			 <br><br>
			 <table style="text-align: center; width: 364px;" border="1" cellpadding="0" cellspacing="0">
			  <tbody>
				<tr>
				  <td class="encabezadoTabla" style="text-align: center;" colspan="2" rowspan="1">
				  <input id="modal_porcentaje" value="porcentaje" name="tipo_incremento1" onclick="activar_radio_button();" type="radio"><b>Porcentaje</b></td>
				  <td rowspan="2" class="fila1" style="text-align: center;"><input class='radio_tipo_incremento' id="valor_fijo" name="tipo_incremento1" type="radio" value="valor_fijo" onclick="validar_tipo_incremento_modal();"><b>Valor fijo</b></td>
				</tr>
				<tr class="fila1">
				  <td><input class='radio_tipo_incremento' name="radio_tipo_porcentaje" id="id_radio_tipo_porcentaje" value="costo" type="radio"><b>Costo</b></td>
				  <td><input class="radio_tipo_incremento" name="radio_tipo_porcentaje" id="id_tipo_tarifa" value="tarifa" type="radio"><b>Tarifa</b></td>

				</tr>
			  </tbody>
			</table>
			<center><br><input type="file" id="csv" name="csv" /><br><br>
						<input type = "submit" id="btn_importar" value="Procesar incremento"/>
			</center>
			<br>
			<center><div id="respuesta_error"></div></center>
			<center><div id="respuesta"></div></center>
		</form>
	</div>
</div>

<input type='hidden' name='failJquery' id='failJquery' value='El programa terminó de ejecutarse pero con algunos inconvenientes <br>(El proceso no se completó correctamente)' >
</div>
</body>
</html>