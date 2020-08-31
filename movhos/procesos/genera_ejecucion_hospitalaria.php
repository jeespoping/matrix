<?php
include_once("conex.php");
if(!array_key_exists('user',$_SESSION))
{
    echo '  <br /><br /><br /><br />
            <div style="color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente para que pueda seguir utilizando este programa normalmente.
            </div>';
    exit();
}

include_once("root/comun.php");
$conex = obtenerConexionBD("matrix");
$costosyp = consultarAliasPorAplicacion($conex, $wemp_pmla, "costos");
	
if(isset($consultaAjax))
	{

	switch($consultaAjax){

		case 'listar_sub_pro':
					{
						echo listar_sub_pro($wemp_pmla, $cco);
					}
		break;		

		default: break;

		}
	return;
	}


?>
<html>
<head>
<title>GENERACION AUTOMATICA EJECUCION OPERATIVA HOSPITALARIA</title>
<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />

<script type="text/javascript" src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../include/root/jqueryui_1_9_2/jquery-ui.js"></script>
<script type="text/javascript" src="../../../include/root/ui.core.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.tabs.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.draggable.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.dimensions.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.simple.tree.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
</head>

<script type="text/javascript">
	function cerrarVentana()
	 {
      top.close();
     }

	function verificarEnvio()
	{	
		if($("#wsubprocesos").val() == ''){
			
			alert('Debes seleccionar un subproceso'); 
			return false ; 
		}
		
		if(confirm("Desea guardar en la tabla de movimiento operativo"))
		{
			$("#grabaMovOperativo").val("on");
		}
		return true;
	}
	
	function listar_sub_pro(){
		
		
		var cco = $("#cco").val();
		
		$.ajax({
					url: "genera_ejecucion_hospitalaria.php",
					type: "POST",
					data:{
						wemp_pmla		: $("#wemp_pmla").val(),
						consultaAjax 	: 'listar_sub_pro',					
						cco				: cco
						
					},
					dataType: "json",			
					async: false,
					success:function(data_json) {

						if (data_json == 1)
						{
							
							return;
						}
						else{
							
							$("#wsubprocesos").html(data_json.html);

						}
					}

				});
		
		
	}
	
	$(function(){ 
	
	var cco = $("#cco").val();
	
	if(cco != '%' && cco != ''){
	
	$('#cco').val(cco).prop('selected',true).trigger('change');	
	
	}
});
	
	
</script>

<body>

<?php

/*************************************************************************************************************************
 * Realizado por: John Mario Cadavid
 * Fechas de creación: Marzo 8 de 2011
 * Generación Automática Ejecución Operativa Hospitalaria
 *
 * Descipción:  Proceso que ingresa los datos en la tabla costosyp_000090 y costosyp_000142
 *				según el año y mes consultados
 *				También ingresa los datos en la tabla costosyp_000032 según el año y mes consultados
 *
 *  Actualizaciones:
 * 2018-06-08 Jonatan Lopez
				- Se corrige la grabacion de la tabla de movimiento operativo.
 * 2017-06-07 Pedro Ortiz
 * 				- Se le agrega la funcion trim a la cama para evitar los espacios en blanco en la tabal costosyp_000090.. Señale con una X. Señale con una X
 * 2016-11-15 Jonatan
				- Se agrega buscador de subprocesos asociados a un centro de costos.
				- Se agrega seleccionador de empresa para que se registre en la tabla 90.
 * 2013-09-16.  (Edwar)
 				- En los INSERT o UPDATE a la tabla "_000090" se modifica el valor del campo "Seguridad" para que siempre guarde el usuario "C-costosyp".
 				- El programa seguía funcionado normalmente así la sesión en matrix ya no estuviera activa, se puso una validación y un mensaje al
 				  principio del script para verificar que la sesión esta activa sino es así entonces se detiene el programa y se muestra un mensaje informativo.

 * 2013-08-13.  (Jonatan Lopez)
				Se repararon los datos de este reporte de la siguiente forma:
 * 			    - Ya no tiene en cuenta los movimientos de la tabla movhos_000017.
 *				- Se agrego la validacion de ingresos y egresos en un mismo dia para un centro de costos.
 *				- Para el centro de costos 1179 no tiene en cuenta los ingresos y egresos del mismo dia.
 *				- Se agrega la funcion consultarIndicadoresHospitalarios para comparar los resultados del programa indicadores_hospitalarios_dia.php con ese informe
				  exactamente en los diascamaocupada, si hay diferencias mostrara un mensaje al final del indicador del cco y los dias de diferencia.
 *
 * 2013-02-14. Se adicionó la función javascript verificarEnvio que lanza una confirmación para que el usuario decida si se va a guaradar
 * en la tabla de movimiento operativo (costosyp_000032)
 *
 * 2013-02-13. Se modificó la actualización y grabado en la tabla costosyp_000032 de modo que cuando Morcod = 12 entonces Mortip = 'P'
 * y cuando Morcod = 27 ó Morcod = 34 entonces Mortip = 'S'. El punto anterior se hace por solicitud de costos.
 * En la función tiempoOcupacion se modificó el cálculo de ocupación para las camas de modo que cumpla la siguiente regla:
 * Si en la tabla movhos_000067 se encuentra historia para la cama al final del día, se cuenta ese día como ocupado
 * Si en la tabla movhos_000067 NO se encuentra historia para la cama al final del día, se busca si el día anterior la cama tenia historia
 * al final del día O si en la tabla movhos_000017 esa cama fue ocupada durante el día
 *
 *  Julio 11 de 2012 Viviana Rodas.  Se agrega el llamado a las funciones
 * consultaCentrosCostos que hace la consulta de los centros de costos
 * de un grupo seleccionado y dibujarSelect que dibuja el select con los
 * centros de costos obtenidos de la primera funcion.
 *
 *  2012-05-30 - Se cambió  la función tiempoOcupacion para que haga el cálculo de ocupación ´con base en días de ocupación
 *	y no en horas como estaba antes.
 *
 *************************************************************************************************************************/

/*****************************************************************************
 *                                   FUNCIONES
 ****************************************************************************/

//Lista los subprocesos asociados a un centro de costos.
function listar_sub_pro($wemp_pmla, $cco){
	
	global $conex;
	global $wbasedato;
	global $costosyp;
	
	$datamensaje = array('mensaje'=>'', 'error'=>0, 'actualizado'=>'');
	
	$cco = explode("-",$cco);
	
	$select_sub_pro = "SELECT *
						 FROM ".$costosyp."_000018
						WHERE Diccco LIKE '%".trim($cco[0])."%'
					 ORDER BY Dicnsu";
    $res = mysql_query($select_sub_pro, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $select_sub_pro . " - " . mysql_error());
	
	$sub_procesos = "<option value=''></option>";
	while($rs = mysql_fetch_array($res)){
		
		$sub_procesos .= "<option value='".$rs['Dicsub']."'>".$rs['Dicsub']."-".$rs['Dicnsu']."</option>";
	}
	
	$datamensaje['html'] = utf8_decode($sub_procesos);
	
	echo json_encode($datamensaje);
    return;
	
	
}
 
//Consulta la habitacion que ocupo y desocupo en un mismo dia.
function consul_hab_ocupada($whis,$wing, $wcco, $wfecha_ingreso)
	{

	global $conex;
	global $wbasedato;

	$sql = "SELECT Eyrhor
			  FROM ".$wbasedato."_000017
		     WHERE Eyrhis = '".$whis."'
			   AND Eyring = '".$wing."'
			   AND Fecha_data = '".$wfecha_ingreso."'
			   AND Eyrsde LIKE '%".$wcco."%'
			   AND Eyrtip = 'Recibo'
			   AND Eyrest = 'on'";
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql -".mysql_error() );
	$row = mysql_fetch_array($res);

	//echo $sql."<br>";
	return $row['Eyrhor'];

	}

 //Funcion que verifica si una cama ha sido ocupada el ultimo dia del mes anterior al que se esta consultando.
function cama_ocup_ult_dia($whabcod, $wcco, $fechaini)
	{

	global $conex;
	global $wbasedato;

    $sql = "SELECT id
			  FROM ".$wbasedato."_000067
		     WHERE Habcod = '".$whabcod."'
			   AND Fecha_data = '".$fechaini."'
			   AND Habhis != ''
			   AND Habcco = '".$wcco."'
			   AND Habest = 'on'";
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql -".mysql_error() );
	$num = mysql_num_rows($res);

	//echo $sql."<br>";

	return $num;

	}



// Genera las opciones del menú mes
function menu_mes($mesinp = ""){
  if($mesinp != "")
	$mes = (int) $mesinp;
  else
	$mes = date("n");

	for($i=1;$i<13;$i++)
	{
	   if($i < 10)
		 $ii = "0".$i;
	   else
	     $ii = $i;

	   if($i == $mes)
	  	 echo "<option  value='$i' selected>".mes_texto($i)."</option>";
	   else
		 echo "<option value='$i'>".mes_texto($i)."</option>";
	}
}

// Genera las opciones del menú año
function menu_anio($anioinp = ""){
  if($anioinp != "")
	$anio = (int) $anioinp;
  else
	$anio = date("Y");

  for($i=date("Y")-10;$i<=date("Y");$i++)
	if($i == $anio)
		echo "<option  value='$i' selected>$i</option>";
	else
		echo "<option value='$i'>$i</option>";
}

function obtenerDiasMes($mes, $anio)
{
      return date("d",mktime(0,0,0,$mes+1,0,$anio));
}

/**
 * Limpia los registros de las tablas 142 y 90 de costosyp
 */
function limpiarRegistros($anio,$mes,$ccocod)
{
	global $conex;
	global $wbasedatocyp;

	if(!isset($ccocod) || $ccocod=='' || $ccocod==' ' || $ccocod=='%')
	{
		$qdel ="DELETE
				FROM
					".$wbasedatocyp."_000142
				WHERE
					Rdsdri = 'NCAM'";
		$resdel = mysql_query( $qdel, $conex ) or die( mysql_errno()." - Error en el query $qdel -".mysql_error() );


		$qdel ="DELETE
				FROM
					".$wbasedatocyp."_000090
				WHERE 	Mdaano = '".$anio."'
				AND 	Mdames = '".$mes."'
				AND 	Mdaemp = '".$empresa[0]."'
				AND 	Mdadri = 'NCAM'";
		$resdel = mysql_query( $qdel, $conex ) or die( mysql_errno()." - Error en el query $qdel -".mysql_error() );
	}
	else
	{
		$qdel ="DELETE
				FROM
					".$wbasedatocyp."_000142
				WHERE 	Rdsdri = 'NCAM'
				AND		Rdscco = '".$ccocod."'";
		$resdel = mysql_query( $qdel, $conex ) or die( mysql_errno()." - Error en el query $qdel -".mysql_error() );


		$qdel ="DELETE
				FROM
					".$wbasedatocyp."_000090
				WHERE 	Mdaano = '".$anio."'
				AND 	Mdames = '".$mes."'
				AND 	Mdaemp = '".$empresa[0]."'
				AND 	Mdadri = 'NCAM'
				AND 	Mdacco = '".$ccocod."'";
		$resdel = mysql_query( $qdel, $conex ) or die( mysql_errno()." - Error en el query $qdel -".mysql_error() );
	}
}

/**
 * Calcula el tiempo transcurrido entre dos horas del mismo día.
 */
function calcularTiempo( $horaini, $horafin ){

	$time = 0;

	$exp = explode( ":", $horaini );

	$horini = $exp[0];
	$minini = $exp[1];
	$segini = $exp[2];

	$exp = explode( ":", $horafin );

	$horfin = $exp[0];
	$minfin = $exp[1];
	$segfin = $exp[2];

	$inicial = mktime( $horini, $minini, $segini );
	$final = mktime( $horfin, $minfin, $segfin );

	$time = $final - $inicial;

	return abs( $time );

}

/**
 * Busca los pacientes que ocuparon la cama durante el día y devuelve el array de estos
 *
 * @param $cco
 * @param $cama
 * @param $fecha
 * @return unknown_type
 */
function pacientesDuranteDia( $cco, $cama, $fecha )
{
	global $conex;
	global $wbasedato;

	$pacientes = array();

	//Se busca si hubo entrega de un paciente a otro cento de costos
	$sql = "SELECT
				*, a.Hora_data as Hora_data
			FROM
				{$wbasedato}_000017 a, {$wbasedato}_000016 b
			WHERE
				eyrtip = 'Recibo'
				AND eyrsde = '$cco'
				AND eyrhde = '$cama'
				AND a.fecha_data = '$fecha'
				AND inghis = eyrhis
				AND inging = eyring
				AND eyrest = 'on'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql -".mysql_error() );


	for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
		$pacientes[ $i ] = $rows;
	}
//	echo $sql."<br>";
	return $pacientes;

}

/**
 * Busca los pacientes que ocuparon la cama durante el día y devuelve el array de estos
 *
 * @param $cco
 * @param $cama
 * @param $fecha
 * @return unknown_type
 */
function egresosDuranteDia( $cco, $cama, $fecha )
{
	global $conex;
	global $wbasedato;

	$pacientes = array();

	//Se busca si hubo entrega de un paciente a otro cento de costos
	$sql = "SELECT
				*, a.Hora_data as Hora_data
			FROM
				{$wbasedato}_000017 a, {$wbasedato}_000016 b
			WHERE
				eyrtip = 'Entrega'
				AND eyrsde = '$cco'
				AND eyrhde = '$cama'
				AND a.fecha_data = '$fecha'
				AND inghis = eyrhis
				AND inging = eyring
				AND eyrest = 'on'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql -".mysql_error() );


	for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
		$pacientes[ $i ] = $rows;
	}

	return $pacientes;

}

/**
 * Devuelve la hora en que fue dada una cama al paciente
 *
 * @param $cco			Centro de Costps
 * @param $cama			Cama
 * @param $fecha		Fecha en que fue dada la cama
 * @param $his			Historia
 * @param $ing			Ingreso
 * @return unknown_type
 */
function horaEntrada( $cco, $cama, $fecha, $his, $ing ){

	global $conex;
	global $wbasedato;

	$hora = '23:59:59';

	//Se busca si hubo entrega de un paciente a otro cento de costos
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000017
			WHERE
				eyrtip = 'Recibo'
				AND fecha_data = '$fecha'
				AND eyrsde = '$cco'
				AND eyrhde = '$cama'
				AND eyrhis = '$his'
				AND eyring = '$ing'
				AND eyrest = 'on'
			";// echo "<pre>......$sql</pre>";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql -".mysql_error() );

	if( $rows = mysql_fetch_array( $res ) ){
		$hora = $rows['Hora_data'];
	}

	return $hora;
}

/**
 * Busca la hora en que a un paciente se entrega a otra habitación o se le dio de alta
 *
 * @param $cco
 * @param $cama
 * @param $fecha
 * @param $his
 * @param $ing
 * @return unknown_type
 */
function horaSalida( $cco, $cama, $fecha, $his, $ing ){

	global $conex;
	global $wbasedato;

	$hora = '00:00:00';

	//Se busca si hubo entrega de un paciente a otro cento de costos
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000017
			WHERE
				eyrtip = 'Entrega'
				AND eyrsor = '$cco'
				AND eyrhor = '$cama'
				AND eyrhis = '$his'
				AND eyring = '$ing'
				AND fecha_data = '$fecha'
				AND eyrest = 'on'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql -".mysql_error() );
	$numrows = mysql_num_rows( $res );

	if( $numrows > 0 ){

		if( $rows = mysql_fetch_array( $res ) ){
			$hora = $rows['Hora_data'];
		}
	}
	else{

		//Se busca si hubo alta definitiva para la cama durante el día
		$sql = "SELECT
					*
				FROM
					{$wbasedato}_000018
				WHERE
					ubihis = '$his'
					AND ubiing = '$ing'
					AND ubihac = '$cama'
					AND ubisac = '$cco'
					AND ubiald = 'on'
					AND ubifad = '$fecha'
				";

		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql -".mysql_error() );
		$numrows = mysql_num_rows( $res );

		if( $rows = mysql_fetch_array( $res ) ){
			$hora = $rows['Ubihad'];
		}

	}

	return $hora;

	global $conex;
	global $wbasedato;

	$hora = '00:00:00';

	//Se busca si hubo alta definitiva para la cama durante el día
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000018
			WHERE
				ubihis = '$his'
				AND ubiing = '$ing'
				AND ubihac = '$cama'
				AND ubisac = '$cco'
				AND ubiald = 'on'
				AND ubifad = '$fecha'
			";


	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql -".mysql_error() );
	$numrows = mysql_num_rows( $res );

	if( $numrows > 0 ){

		if( $rows = mysql_fetch_array( $res ) ){
			$hora = $rows['Ubihad'];
		}
	}
	else{

		//Se busca si hubo entrega de un paciente a otro cento de costos
		$sql = "SELECT
					*
				FROM
					{$wbasedato}_000017
				WHERE
					eyrtip = 'Entrega'
					AND eyrsor = '$cco'
					AND eyrhor = '$cama'
					AND eyrhis = '$his'
					AND eyring = '$ing'
					AND fecha_data = '$fecha'
					AND eyrest = 'on'
				";

		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql -".mysql_error() );
		$numrows = mysql_num_rows( $res );

		if( $rows = mysql_fetch_array( $res ) ){
			$hora = $rows['Hora_data'];
		}

	}

	return $hora;

}

/**
 * Calcula el tiempo de ocupación por cama durante el rango de fechas dado
 *
 * @param $cco				Centro de costos
 * @param $cama				Cama
 * @param $fechaini			Fecha inicial
 * @param $fechafin			Fecha final
 * @return unknown_type
 */
function tiempoOcupacion( $cco, $cama, $fechaini, $fechafin ){

	global $conex;
	global $wbasedato;

	//Definición de variables

	$hisAnterior = '';				//Indica la historia del paciente del día anterior
	$ingAnterior = '';				//Indica el ingreso del paciente del día anterior
	$ocupacionDia = 0;				//Tiempo de ocupación de una cama durante el día (en horas)
	$tiempoOcupacion = 0;			//Tiempo de ocupación de una cama durante el rango de fechas (en horas)

	//Calculo la fecha de día anterior de la inicial
	$exp = explode( "-", $fechaini );
	$fechaini = date( "Y-m-d", mktime( 0, 0, 0, $exp[1], $exp[2]-1, $exp[0] ) );


	//Buscando los pacientes que se encuentran en cama al final del día
	$sql = "SELECT
				*, b.Fecha_data as Fecha_data
			FROM
				{$wbasedato}_000067 b LEFT OUTER JOIN {$wbasedato}_000016 a
				ON  habhis = inghis AND habing = inging
			WHERE
				b.habcco = '$cco'
				AND b.habcod = '$cama'
				AND b.fecha_data BETWEEN '$fechaini' AND '$fechafin'
				AND b.habest = 'on'
				AND b.habhis != 'NO APLICA'
			ORDER BY b.Fecha_data";
	//	echo $sql."<br>";
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );

	// Si hay registros de ocupación
	if( $numrows > 0 )
	{
		//$rows = mysql_fetch_array( $res ); //Se comenta esta linea ya que interfiere con la linea 477,
											//la cual usa de nuevo el mysql_fetch_array, al usarlo aqui no
											//tiene en cuenta el primer registro (Jonatan Agosto 06 - 2013)

		$hisAnterior = $rows[ 'Habhis' ];
		$ingAnterior = $rows[ 'Habing' ];

		//Creando array por cama y tipo de empresa
		for( $i = 0; $rows = mysql_fetch_array( $res ) ; $i++ )
		{
			// Se inicializan variables
			$ocupacionDia = 0;

			// Si hay paciente en la habitación al final del día
			if( trim($rows['Habhis']) != '' && trim($rows['Habhis']) != 'NO APLICA' )
			{
				//echo $rows['Fecha_data']."-".$rows['Habcod']."<br>";

				if($rows['Fecha_data'] != $fechaini)
					{
					// Registro ocupación para el día actual
					$ocupacionDia += 1;
					}

			}
		else
		 	{
				//Verifica si el primer dia del mes la cama estaba desocupada, si es asi revisara si el ultimo dia del mes anterior lo estaba,
				//si es asi la toma como ocupada(Jonatan 06 Agosto 2013)

				if($rows['Fecha_data'] == $fechaini)
					{

					$wcamaocupada = cama_ocup_ult_dia($rows['Habcod'], $rows['Habcco'], $fechaini);

					if($wcamaocupada > 0)
						{

						$ocupacionDia += 1;

						}
					}
			}


			/*  else
			{
				//Busco los pacientes en la cama durante el día y que no sean los mismo del día anterior
				$pacientes = pacientesDuranteDia( $cco, $cama, $rows[ 'Fecha_data' ] );

				if( ( count( $pacientes ) > 0 ) || ($hisAnterior != '' && $ingAnterior != '') )
				{
					// Resgistro ocupación para el día actual
					$ocupacionDia += 1;
				}
			}  */

			$tiempoOcupacion += $ocupacionDia;


			$hisAnterior = $rows[ 'Habhis' ];
			$ingAnterior = $rows[ 'Habing' ];
		}

	}

	return $tiempoOcupacion;

}

/**
 * Calcula los dias transcurridos entre dos fechas, en caso de que alguna de las fechas
 * ingresadas como parametro sea falsa, la funcion retornará 0
 */

function calcularDias( $fecini, $fecfin){

	$ini = strtotime( $fecini );
	$fin = strtotime( $fecfin );

	if( $ini == -1 || $fin ==  -1 ){
		return 0;
	}
	else{
		$dif = $fin - $ini;
		return ( $dif/(24*3600) + 1 );
	}
}


/*********************************************************************************
 * AQUI COMIENZA EL PROGRAMA
 ********************************************************************************/


$wactualiz = " Junio 8 de 2018 ";

// Validación de usuario
if (!isset($user))
{
	if (!isset($_SESSION['user']))
	{
		session_register("user");
	}
}

//Codigo de usuario que ingreso al sistema
if (strpos($user, "-") > 0)
	$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));

$usuario = new Usuario();

$usuario->codigo = $wuser;

//Variable para determinar la empresa
if(!isset($wemp_pmla))
{
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}

//Valida codigo de usuario en sesion si no esta registrado el sistema termina la ejecucion
if (!isset($_SESSION['user']))
{
	terminarEjecucion("usuario no autenticado.  Por favor cierre esta ventana y vuelva a ingresar a Matrix.");
}
else
{
$seguridad = $usuario->codigo;
$conex = obtenerConexionBD("matrix");

$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
$wbasedatocyp = consultarAliasPorAplicacion($conex, $wemp_pmla, "costos");
$wtabcco = consultarAliasPorAplicacion($conex, $wemp_pmla, "tabcco");

encabezado("GENERACION AUTOMATICA EJECUCION OPERATIVA HOSPITALARIA", $wactualiz,"clinica");

if( !isset($mostrar) ){
	$mostrar = 'off';
}


class indicador{
	var $servicio;						//Servicio
	var $fecha;							//Fecha generación indicador
	var $camasOcupadas = 0; 			//Camas ocupadas
	var $camasDisponibles = 0;			//Camas disponibles
	var $camasDelServicio = 0;			//Camas del servicio

	var $ingU = 0;						//Ingresos urgencias
	var $ingA = 0;						//Ingresos admisiones
	var $ingC = 0;						//Ingresos cirugia
	var $ingT = 0;						//Ingresos por traslado
	var $ingTotales = 0;				//Ingresos totales.  No incluye ingresos por traslado.
	var $ingTotalesSinTrasl=0;

	var $ingYEgrDia = 0;				//Ingresos y egresos del dia

	var $egrA = 0;						//Egresos por altas
	var $egrMmay48 = 0;					//Egresos por muerte mayor a 48 horas
	var $egrMmen48 = 0;					//Egresos por muerte menor a 48 horas
	var $egrT = 0;						//Egresos por traslado
	var $egrTotales = 0;				//Egresos totales
	var $egrTotalesSinTrasl=0;

	var $diasEAltasM = 0;				//Dias de estancia altas y muertes
	var $diasEEgrT = 0;					//Dias de estancia egresos por traslado

	var $pacDiaAnterior = 0;			//Pacientes dia anterior
	var $pacALaFecha = 0;				//Pacientes a la fecha

	var $nroCamas = 0;					//Numero de camas
	var $diasCamaDisponible = 0;		//Dias cama disponible para calculos de indicadores
	var $totalDiasCamaDisponible = 0;	//Dias cama disponible para total
	var $diasCamaOcupada = 0;			//Dias cama ocupada
	var $promCamasOcupadas = 0;			//Promedio dias camas ocupadas
	var $diasEstanciaTotales = 0;		//Dias estancia egresos altas y muertes mas dias estancia egresos por traslado

	var $porcOcupacion = 0;				//Porcentaje ocupación
	var $promDiasEstancia = 0;			//Promedio dias estancia
	var $rendimientoHospitalario = 0;	//Rendimiento hospitalario
	var $indiceSustitucion = 0;			//Indice sustitución
	var $tasaMortalidad = 0;			//Tasa mortalidad
	var $tasaMortalidadMayor48 = 0;		//Tasa mortalidad mayor a 48 horas
	var $tasaMortalidadMenor48 = 0;		//Tasa mortalidad menor a 48 horas
}


/*************************************************************************************************************************************************
 *Consulta los indicadores dados los parametros (Esta funcion viene del programa indicadores_hospitalarios_dia.php) //Jonatan Lopez 13 Agosto 2013
 *************************************************************************************************************************************************/
function consultarIndicadoresHospitalarios($wservicio,$wfechaInicial,$wfechaFinal,$diasFechasConsulta)
{

	global $wbasedato;
	global $wtabcco;
	global $conex;

	$coleccion = array();

	$q = "    SELECT 	cieser, ciedis, cieocu, cieing, cieegr, cieiye, ciedes, Ciemmay, Ciemmen, Cieinu, Cieinc, Cieina, Cieint, Ciegrt, Ciedit,
						Ciediam, Cieeal, ".$wtabcco.".cconom, A.fecha_data,
						(SELECT  sum(cieocu)
						   FROM  ".$wbasedato."_000038 B
						  WHERE B.fecha_data = DATE_SUB(A.fecha_data, INTERVAL 1 DAY) AND B.cieser = A.cieser) pacDiaAnterior
				FROM 	".$wbasedato."_000038 A, ".$wtabcco.", ".$wbasedato."_000011
			   WHERE 	A.fecha_data BETWEEN '".$wfechaInicial."' AND '".$wfechaFinal."'
			     AND 	cieser LIKE '".$wservicio."'
			     AND 	cieser = ".$wtabcco.".ccocod
			     AND 	cieser = ".$wbasedato."_000011.ccocod
			     AND 	Ccourg != 'on'
		    ORDER BY   	cieser,A.fecha_data";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$rs = mysql_fetch_array($res);

	while($rs)
	{
		$indicador = new indicador();

		//Consultados
		$indicador->servicio 			= $rs['cieser']." - ".$rs['cconom'];	//Servicio
		$indicador->camasDisponibles	= $rs['ciedis'];						//Camas Desocupadas
		$indicador->camasOcupadas		= $rs['cieocu'];						//Camas Ocupadas
		$indicador->ingTotales			= $rs['cieing'];						//Ingresos
		$indicador->egrTotales 			= $rs['cieegr'];						//Egresos
		$indicador->ingYEgrDia 			= $rs['cieiye'];						//Ing y Egr del mismo dia
		$indicador->diasEstanciaTotales	= $rs['ciedes'];						//Dias estancia (Egresados)
		$indicador->egrMmay48 			= $rs['Ciemmay'];						//Muertes mayores a 48 horas
		$indicador->egrMmen48			= $rs['Ciemmen'];						//Muertes menores a 48 horas
		$indicador->ingU	 			= $rs['Cieinu'];						//Ingresos por urgencias
		$indicador->ingC	 			= $rs['Cieinc'];						//Ingresos por cirugía
		$indicador->ingA 				= $rs['Cieina'];						//Ingresos por admisiones
		$indicador->ingT	 			= $rs['Cieint'];						//Ingresos por traslados
		$indicador->egrT	 			= $rs['Ciegrt'];						//Egresos por traslado
		$indicador->diasEEgrT			= $rs['Ciedit'];						//Dias estancia traslado
		$indicador->diasEAltasM			= $rs['Ciediam'];						//Dias estancia altas muertes
		$indicador->egrA 				= $rs['Cieeal'];						//Egresos por altas
		$indicador->fecha	 			= $rs['fecha_data'];

		if(isset($rs['pacDiaAnterior']))
		{
			$indicador->pacDiaAnterior	= $rs['pacDiaAnterior'];
		}
		else
		{
			$indicador->pacDiaAnterior	= 0;
		}


		//Calculados
		$indicador->ingTotalesSinTrasl	= intval($indicador->ingU) + intval($indicador->ingC) + intval($indicador->ingA);
		$indicador->egrTotalesSinTrasl	= intval($indicador->egrA) + intval($indicador->egrMmay48) + intval($indicador->egrMmen48);
		$indicador->pacALaFecha			= abs(intval($indicador->pacDiaAnterior) + intval($indicador->ingTotales) - intval($indicador->egrTotales));										//Pacientes a la fecha = Pacientes dia anterior + Ingresos - Egresos
		$indicador->diasCamaDisponible	= intval($indicador->camasDisponibles) + intval($indicador->camasOcupadas); 																//Total Días Cama Disponible = Camas disponibles + camas ocupadas
		$indicador->diasCamaOcupada		= abs(intval($indicador->pacDiaAnterior) + intval($indicador->ingTotales) - intval($indicador->egrTotales) + intval($indicador->ingYEgrDia));	//Total Días Cama Ocupada    = (Ingresos - Egresos + (Pacientes que Ingresaron y Egresaron el mismo día))

		if(intval($indicador->egrTotales) > 0)
		{
			$indicador->promDiasEstancia		= (intval($indicador->diasEstanciaTotales) / intval($indicador->egrTotales));															//Dias estancia = Total dias estancia del período/Total egresos del período
			$indicador->tasaMortalidad			= ((intval($indicador->egrMmay48) + intval($indicador->egrMmen48) / intval($indicador->egrTotales)) * 100);								//Tasa de Mortalidad = ((número de muertes del período/Total egresos del período)*100)
			$indicador->tasaMortalidadMayor48	= ((intval($indicador->egrMmay48) / intval($indicador->egrTotales)) * 100);																//Tasa de Mortalidad = ((número de muertes del período > 48/Total egresos del período)*100)
			$indicador->tasaMortalidadMenor48	= ((intval($indicador->egrMmen48) / intval($indicador->egrTotales)) * 100);																//Tasa de Mortalidad = ((número de muertes del período < 48/Total egresos del período)*100)
		}
		else
		{
			$indicador->promDiasEstancia		= 0;																								//Dias estancia = Total dias estancia del período/Total egresos del período
			$indicador->tasaMortalidad			= 0;
			$indicador->tasaMortalidadMayor48	= 0;
			$indicador->tasaMortalidadMenor48	= 0;
		}
			//Controlar div por cero
			$indicador->porcOcupacion		= @( intval($indicador->diasCamaOcupada) / intval($indicador->diasCamaDisponible) * 100 );   											//Porcentaje Ocupacional

		//El indicador se consulta por dia.  Asi que este valor siempre será 1
		$diasFechasConsulta = 1;
		$indicador->promCamasOcupadas 		= intval($indicador->diasCamaOcupada) / $diasFechasConsulta;

		if( $wservicio == '1179' ){
			//Es para los pisos que tienen ingreso directo, como medicina nuclear
			$indicador->diasCamaOcupada		= abs(intval($indicador->pacDiaAnterior) + intval($indicador->ingTotales) - intval($indicador->egrTotales));
			$indicador->porcOcupacion		= @( intval($indicador->diasCamaOcupada) / intval($indicador->diasCamaDisponible) * 100 );
			$indicador->promCamasOcupadas 		= intval($indicador->diasCamaOcupada);
		}

		//Controlar div por cero
		$indicador->rendimientoHospitalario = @(intval($indicador->egrTotales) / ROUND(intval($indicador->diasCamaDisponible)));  								//Rendimiento hospitalario = Total egresos del período/Numero de Camas
		$indicador->nroCamas				= intval($indicador->diasCamaDisponible) / $diasFechasConsulta;

		if($indicador->porcOcupacion > 0)
		{
			$indicador->indiceSustitucion 	= ((100-$indicador->porcOcupacion)*$indicador->promDiasEstancia) / $indicador->porcOcupacion;
		}
		else
		{
			$indicador->indiceSustitucion 	= 0;
		}

		$coleccion[] = $indicador;

		$rs = mysql_fetch_array($res);

	}

	return $coleccion;
}


if( $mostrar == 'off' )
{


	
	// Si ya se ha consultado obtengo los datos consultados
	if( !isset( $cco ) ){
		$cco = '%';
	}
	if( !isset( $wanio ) ){
		$wanio = date("Y");
	}
	if( !isset( $wmes ) ){
		$wmes = date("m");
	}
	if( !isset( $wsubprocesos ) ){
		$wsubprocesos = '';
	}
	
	$q = "    SELECT *
			    FROM ".$costosyp."_000153
				ORDER BY Empcod";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		
	while($rs = mysql_fetch_array($res)){
		
		$empresas .= "<option value='".$rs['Empcod']."-".$rs['Empdes']."'>".$rs['Empcod']."-".$rs['Empdes']."</option>";
		
	}
	
	echo "<form action='genera_ejecucion_hospitalaria.php?wemp_pmla=01' method='post' onSubmit='return verificarEnvio();'>";

	// Inicio de impresión del formulario
	echo "<br><br>
			<table align='center'>
				<tr>
					<td align='center' class='fila1'><b>Año</b></td>
					<td align='center' class='fila1'><b>Mes</b></td>
				</tr>
				<tr>
				<tr>
					<td align='center' class='fila2'>";
	echo "				<select name='wanio' id='wanio'>";
							menu_anio($wanio);
	echo "				</select>
					</td>
					<td align='center' class='fila2'>";
	echo "				<select name='wmes' id='wmes'>";
							menu_mes($wmes);
	echo "				</select>";
	echo "			</td>
				</tr>
				<tr>
					<td align='center' colspan='2' class='fila1'><b>Empresa</b></td>
				<tr>
					<td align='center' colspan='2' class='fila2'>
						<select name='empresa' id='empresa'>							
							$empresas
						</select>
					</td>
				</tr>
				<tr>
					<td align='center' colspan='2' class='fila1'><b>Centro de costos</b></td>
				<tr>
					<td align='center' colspan='2' class='fila2'>
						<select name='cco' id='cco'>
							<option value=''></option>
							<option value='%-Todos los centros de costos'>Todos los centros de costos</option>";
							$selected = '';

							$cco1="ccohos = 'on' AND ccoest = 'on' AND ccourg <> 'on' AND ccocir <> 'on'";
							$filtro="--";

							$centrosCostos = consultaCentrosCostos($cco1, $filtro);
							foreach ($centrosCostos as $centroCostos)
							{
								if($centroCostos->codigo == $cco) $selected = " selected";
								echo "<option value='".$centroCostos->codigo."-".$centroCostos->nombre."' ".$selected.">".$centroCostos->codigo."-".$centroCostos->nombre."</option>";
								$selected = '';
							}
	echo "				</select>
					</td>
				</tr>
				<tr>
					<td align='center' colspan='2' class='fila1'><b>Subprocesos</b></td>
				<tr>
					<td align='center' colspan='2' class='fila2'>";
	echo "				<input name='wsubprocesos' id='wsubprocesos' value='".$wsubprocesos."' size='41'>";
	echo "			</td>
				</tr>
			</table>";


	//Botones Generar y Cerrar
	echo "<br>
			<table align='center'>
				<tr>
					<td align='center' width='150'><INPUT type='submit' value='Generar' style='width:100' name='btVer'></INPUT></td>
					<td align='center' width='150'><INPUT type='button' value='Cerrar' style='width:100' onClick='javascript:top.close();'></INPUT></td>
				</tr>
			</table>";

	echo "<INPUT type='hidden' name='mostrar' value='on'>";
	echo "<INPUT type='hidden' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";
	echo "<INPUT type='hidden' name='grabaMovOperativo' id='grabaMovOperativo' value='off'>";

	echo "</form>";
}
else
{
	// Inicia la impresi{on de los resultados de la consulta
	$fecha = date("Y-m-d");
	$hora = date("H:i:s");
	$detalleCamas = Array();
	$ultimoDia = obtenerDiasMes($wmes, $wanio);
	$fechaini = "$wanio-$wmes-01";
	$fechafin = "$wanio-$wmes-$ultimoDia";
	$dias = calcularDias( $fechaini, $fechafin );
	$auxcco = explode("-", $cco);
	$ccocod = $auxcco[0];
	$cconom = $auxcco[1];

	//Diferencia de dias en la fecha de consulta
	$vecFechaInicial = explode("-",$fechaini);
	$vecFechaFinal = explode("-",$fechafin);

	$calcDiaInicial = mktime(0,0,0,$vecFechaInicial[1],$vecFechaInicial[2],$vecFechaInicial[0]);
	$calcDiaFinal = mktime(0,0,0,$vecFechaFinal[1],$vecFechaFinal[2],$vecFechaFinal[0]);

	$diasFechasConsulta = ROUND(($calcDiaFinal-$calcDiaInicial)/(60*60*24)) + 1;

	echo "<form action='genera_ejecucion_hospitalaria.php?wemp_pmla=01' method='post'>";

	//Buscando las camas a mostrar, con su respectivo centro de costos, se agrega filtro habhis != 'NO APLICA', para que no la tenga en cuenta.
	$sql = "SELECT
				TRIM(Habcod) as cama, Habcco as cco, cconom as nom, MAX(a.Fecha_data) as fecha, MAX(a.Hora_data) as hora
			FROM
				".$wbasedato."_000067 a, ".$wbasedato."_000011 b
			WHERE ccocod LIKE '%".$ccocod."%'
				AND ccohos  = 'on'
				AND ccourg != 'on'
				AND ccocir != 'on'
				AND ccoest  = 'on'
				AND habhis != 'NO APLICA'
				AND ccocod  = habcco
				AND habest  = 'on'
				AND	habtmp != 'on'
				AND a.Fecha_data BETWEEN '".$fechaini."' AND '".$fechafin."'
			GROUP BY cco, cama";
	//echo $sql."<br>";
	$res = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $sql -".mysql_error() );
	$hasRow = false;
	$rows = mysql_fetch_array($res);
	$arr_hab_dia = array();


	echo "<div>";
	echo "<pre>";
		//print_r($arr_hab_dia);
	echo "</pre>";
	echo "</div>";

	echo "<center><b>RESULTADO DE GENERACIÓN AUTOMÁTICA</b></center>";
	echo "<br>";
	//encabezado del informe
	$empresa = explode("-", $empresa);	
	echo "<table align='center' width='410'>
			<tr align='left'>
				<td class='fila1' align='center'><b>Año</b></td>
				<td class='fila1' align='center'><b>Mes</b></td>
			</tr>
			<tr class='fila1' align='left'>
				<td class='fila2' align='center'>".$wanio."</td>
				<td class='fila2' align='center'>".mes_texto($wmes)."</td>
			</tr>
			<tr>
				<td align='center' colspan='2' class='fila1'><b>Empresa</b></td>
			</tr>
			<tr>
				<td align='center' colspan='2' class='fila2'>".$empresa[1]."</td>
			</tr>
			<tr>
				<td align='center' colspan='2' class='fila1'><b>Centro de costos</b></td>
			</tr>
			<tr>
				<td align='center' colspan='2' class='fila2'>".$cconom."</td>
			</tr>
			<tr>
				<td align='center' colspan='2' class='fila1'><b>Subprocesos</b></td>
			<tr>
				<td align='center' colspan='2' class='fila2'>".$wsubprocesos."</td>
			</tr>
		  </table><br>";
	echo "<table align='center' width='240'>
			<tr>
				<td width='50%' align='left'>
					<INPUT type='button' value='Cerrar' onClick='javascript:cerrarVentana();' style='width:100'>
				</td>
				<td width='50%' align='right'>
					<INPUT type='submit' value='Retornar' style='width:100'>
				</td>
			</tr>";
	echo "</table><br>";

	limpiarRegistros($wanio,$wmes,$ccocod);



	for(; $rows;)
	{
	$wdias_cama_ocupada = 0;
	$wporcenta_ocupacion = 0;
	$wporcentaje_ocup = 0;

	//Esta funcion proviene del script indicadores_hospitalarios_dia.php
	$colIndicadoresHospitalarios = consultarIndicadoresHospitalarios($rows['cco'],$fechaini,$fechafin,$diasFechasConsulta);

	//Pacientes que estuvieron en el cco mas de una vez
   $q = "   SELECT ing.Historia_clinica, ing.Num_ingreso, ing.Fecha_data as fecha_ingreso
			   FROM ".$wbasedato."_000032 ing, ".$wbasedato."_000033 egr
			  WHERE	ing.Fecha_data = egr.Fecha_data
				AND egr.Historia_clinica = ing.Historia_clinica
				AND ing.Servicio = egr.Servicio
				AND ing.Fecha_data BETWEEN '".$fechaini."' AND '".$fechafin."'
				AND ing.Servicio LIKE '%".$rows['cco']."%' ";
	$res1 = mysql_query($q,$conex);

	while($row_his_ing = mysql_fetch_array($res1))
			{

			$whab_ocupada = consul_hab_ocupada($row_his_ing['Historia_clinica'],$row_his_ing['Num_ingreso'], $rows['cco'], $row_his_ing['fecha_ingreso'] );

			 //Se verifica si el producto ya se encuentra en el arreglo, si no esta lo agrega.
			if(!array_key_exists($whab_ocupada, $arr_hab_dia))
			{
				$arr_hab_dia[$whab_ocupada] = array();
			}

			//Aqui se forma el arreglo
			$arr_hab_dia[$whab_ocupada][] = $row_his_ing['Historia_clinica'];

			}

	// echo "<div>";
	// echo "<pre>";
	// print_r($arr_hab_dia);
	// echo "</pre>";
	// echo "</div>";

	foreach ($colIndicadoresHospitalarios as $key => $value)
			{

			$wdias_cama_ocupada += $value->diasCamaOcupada;
			$wporcenta_ocupacion += $value->porcOcupacion;

			}

	// echo "Dias ocupados:".$wdias_cama_ocupada."<br>";
	// echo "Indice de ocupacion:".number_format($wporcentaje_ocup,2,'.',',')."<br>";
	// echo "Dias:".$dias;

		// Encabezado de la tabla por centro de costos
			echo "<table align='center' width='470'>
				<tr class='encabezadotabla'>
					<td colspan='3'>Centro de Costos: {$rows['cco']}-{$rows['nom']}</td>
				</tr>";
			echo "<tr class='encabezadotabla' align='center'>
					<td width='100'>Habitación</td>
					<td width='100'>Driver</td>
					<td width='100'>Días ocupación</td>
				</tr>";

		// Calcula el número de camas disponibles en el periodo de fechas y servicio indicado
		$qcam= "SELECT
					habcco, COUNT(habcod) camas
				FROM
					{$wbasedato}_000011 c, {$wbasedato}_000067  b LEFT OUTER JOIN {$wbasedato}_000016 a ON habhis = inghis AND habing = inging
				WHERE
					habcco like '".$rows['cco']."'
					AND b.fecha_data BETWEEN '$fechaini' AND '$fechafin'
					AND habest = 'on'
					AND habcco = ccocod
					AND ccohos = 'on'
					AND ccourg != 'on'
					AND ccocir != 'on'
					AND ccoest = 'on'
				  GROUP BY 1 ";
		$res_cam = mysql_query( $qcam, $conex );
		$row_cam = mysql_fetch_array($res_cam);
		$camas = $row_cam['camas'];

		$auxcco = $rows['cco'];
		$sumDiasOcupados = 0;
		$contadorCamas = 0;
		for( $i = 0; $auxcco == $rows['cco']; $i++ )
		{
			$ocupacion = tiempoOcupacion( $rows['cco'], $rows['cama'], $fechaini, $fechafin, $arr_hab_dia );

			if($ocupacion>0)
			{

				$codcama = trim($rows['cama']);
			   //Si la habitacion tuvo ingreso y egresos el mismo dia sumara uno o mas a la ocupacion
			   //(El arreglo $arr_hab_dia viene de la consulta a las tablas 17, 32, 33, mas arriba de este comentario).
			   if(array_key_exists($codcama, $arr_hab_dia) and $rows['cco'] != '1179')
					{
					$ocupacion += count($arr_hab_dia[$codcama]);
					}

				$diasOcupados = number_format( ($ocupacion),2,".","" );
				//$rowscount['dso'] = $dias - $ocupacion;
				$fila =  "class='fila".($i%2+1)."'";

				$q = "	SELECT
							id
						FROM
							".$wbasedatocyp."_000090
						WHERE 	Mdaano = '".$wanio."'
						AND		Mdames = '".$wmes."'
						AND 	Mdaemp = '".$empresa[0]."'
						AND 	Mdacco = '".$rows['cco']."'
						AND 	Mdasub = '".trim($rows['cama'])."'
						AND		Mdadri = 'NCAM'";
				$resq = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query $q -".mysql_error() );
				$filas = mysql_num_rows($resq);

				if($rows['cama']!='' && $rows['cama']!=' ' && $rows['cco']!='' && $rows['cco']!=' ')
				{
					if($filas>0)
					{
						$qins = "UPDATE
									".$wbasedatocyp."_000090
								 SET Fecha_data = '".$fecha."', Hora_data = '".$hora."', Mdacan = '".$diasOcupados."', Seguridad = 'C-costosyp'
								 WHERE 	Mdaano = '".$wanio."'
								 AND	Mdames = '".$wmes."'
								 AND 	Mdaemp = '".$empresa[0]."'
								 AND 	Mdacco = '".$rows['cco']."'
								 AND 	Mdasub = '".trim($rows['cama'])."'
								 AND	Mdadri = 'NCAM'
								";
						$resins = mysql_query( $qins, $conex ) or die( mysql_errno()." - Error en el query $qins -".mysql_error() );
					}
					else
					{
						$qins = "INSERT INTO
									".$wbasedatocyp."_000090
									(
										Medico, Fecha_data, Hora_data, Mdaemp, Mdaano, Mdames, Mdacco, Mdasub, Mdadri, Mdacan, Seguridad, id
									)
								 VALUES
									(
										'costosyp','".$fecha."','".$hora."','".$empresa[0]."', '".$wanio."','".$wmes."','".$rows['cco']."','".trim($rows['cama'])."','NCAM','".$diasOcupados."','C-costosyp',NULL
									)
								";
						$resins = mysql_query( $qins, $conex ) or die( mysql_errno()." - Error en el query $qins -".mysql_error() );
					}
				}

				$q = "	SELECT
							id
						FROM
							".$wbasedatocyp."_000142
						WHERE 	Rdscco = '".$rows['cco']."'
						AND 	Rdssub = '".trim($rows['cama'])."'
						AND		Rdsdri = 'NCAM'";
				$resq = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query $q -".mysql_error() );
				$filas = mysql_num_rows($resq);

				if($rows['cama']!='' && $rows['cama']!=' ' && $rows['cco']!='' && $rows['cco']!=' ')
				{
					if($filas>0)
					{
						$qins = "UPDATE
									".$wbasedatocyp."_000142
								 SET Fecha_data = '".$fecha."', Hora_data = '".$hora."', Seguridad = 'C-".$seguridad."'
								 WHERE 	Rdscco = '".$rows['cco']."'
								AND 	Rdssub = '".$rows['cama']."'
								AND		Rdsdri = 'NCAM'";
						$resins = mysql_query( $qins, $conex ) or die( mysql_errno()." - Error en el query $qins -".mysql_error() );
					}
					else
					{
						$qins = "INSERT INTO
									".$wbasedatocyp."_000142
									(
										Medico, Fecha_data, Hora_data, Rdscco, Rdssub, Rdsdri, Seguridad, id
									)
								 VALUES
									(
										'costosyp','".$fecha."','".$hora."','".$rows['cco']."','".trim($rows['cama'])."','NCAM','C-".$seguridad."',NULL
									)
								";

						$resins = mysql_query( $qins, $conex ) or die( mysql_errno()." - Error en el query $qins -".mysql_error() );
					}
				}

				echo "<tr $fila align='center'>
						<td>".$rows['cama']."</td>
						<td>NCAM</td>
						<td>".$diasOcupados."</td>
					</tr>";

				$sumDiasOcupados = $sumDiasOcupados + $ocupacion;
				$contadorCamas = $contadorCamas+1;
			}
			$rows = mysql_fetch_array($res);
		}

		$sumDiasOcupados = number_format( ($sumDiasOcupados),2,".","" );

		echo "<tr class='encabezadotabla'>
				<td colspan='3'>Totales</td>
			  </tr>";

		// Adición subprocesos por centro de costo
		echo "<tr class='fila2' align='center'>
				<td colspan=3 align=center>|";
		$subproceso = explode(",", $wsubprocesos);
		for( $i = 0; $i < count($subproceso); $i++)
		{
		  if($subproceso[$i]!='' && $subproceso[$i]!=' ')
		  {
			$q = "	SELECT
						id
					FROM
						".$wbasedatocyp."_000090
					WHERE 	Mdaano = '".$wanio."'
					AND		Mdames = '".$wmes."'
					AND 	Mdaemp = '".$empresa[0]."'
					AND 	Mdacco = '".$auxcco."'
					AND 	Mdasub = '".trim($subproceso[$i])."'
					AND		Mdadri = 'NCAM'";
			$resq = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query $q -".mysql_error() );
			$filas = mysql_num_rows($resq);

			if($filas>0)
			{
				$qins = "UPDATE
							".$wbasedatocyp."_000090
						 SET Fecha_data = '".$fecha."', Hora_data = '".$hora."', Mdacan = '".$sumDiasOcupados."', Seguridad = 'C-costosyp'
						 WHERE 	Mdaano = '".$wanio."'
						 AND	Mdames = '".$wmes."'
						 AND 	Mdaemp = '".$empresa[0]."'
						 AND 	Mdacco = '".$auxcco."'
						 AND 	Mdasub = '".trim($subproceso[$i])."'
						 AND	Mdadri = 'NCAM'
						";
				$resins = mysql_query( $qins, $conex ) or die( mysql_errno()." - Error en el query $qins -".mysql_error() );
			}
			else
			{
				$qins = "INSERT INTO
							".$wbasedatocyp."_000090
							(
								Medico, Fecha_data, Hora_data, Mdaemp, Mdaano, Mdames, Mdacco, Mdasub, Mdadri, Mdacan, Seguridad, id
							)
						 VALUES
							(
								'costosyp','".$fecha."','".$hora."','".$empresa[0]."','".$wanio."','".$wmes."','".$auxcco."','".trim($subproceso[$i])."','NCAM','".$sumDiasOcupados."','C-costosyp',NULL
							)
						";
				$resins = mysql_query( $qins, $conex ) or die( mysql_errno()." - Error en el query $qins -".mysql_error() );
			}

			$q = "	SELECT
						id
					FROM
						".$wbasedatocyp."_000142
					WHERE 	Rdscco = '".$auxcco."'
					AND 	Rdssub = '".$subproceso[$i]."'
					AND		Rdsdri = 'NCAM'";
			$resq = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query $q -".mysql_error() );
			$filas = mysql_num_rows($resq);

			if($filas>0)
			{
				$qins = "UPDATE
							".$wbasedatocyp."_000142
						 SET Fecha_data = '".$fecha."', Hora_data = '".$hora."', Seguridad = 'C-".$seguridad."'
						 WHERE 	Rdscco = '".$auxcco."'
						 AND 	Rdssub = '".$subproceso[$i]."'
						 AND	Rdsdri = 'NCAM'";
				$resins = mysql_query( $qins, $conex ) or die( mysql_errno()." - Error en el query $qins -".mysql_error() );
			}
			else
			{
				$qins = "INSERT INTO
							".$wbasedatocyp."_000142
							(
								Medico, Fecha_data, Hora_data, Rdscco, Rdssub, Rdsdri, Seguridad, id
							)
						 VALUES
							(
								'costosyp','".$fecha."','".$hora."','".$auxcco."','".$subproceso[$i]."','NCAM','C-".$seguridad."',NULL
							)
						";
				$resins = mysql_query( $qins, $conex ) or die( mysql_errno()." - Error en el query $qins -".mysql_error() );
			}

			echo $subproceso[$i]."|";
		  }
		}
		echo " = ".$sumDiasOcupados."</td>
			</tr>";
		///////////////////////////////////////////////////////


		// Consulta días cama
		$q1 = "	SELECT
					id
				FROM
					".$wbasedatocyp."_000032
				WHERE 	Morano = '".$wanio."'
				AND		Mormes = '".$wmes."'
				AND 	Morcco = '".$auxcco."'
				AND 	Morcod = '12'";
		$resq1 = mysql_query( $q1, $conex ) or die( mysql_errno()." - Error en el query $q1 -".mysql_error() );
		$filas1 = mysql_num_rows($resq1);

		$diasCama = $sumDiasOcupados;

		echo "<tr class='fila1' align='center'>
				<td colspan=2 align=center> Días ocupación camas </td>
				<td align=center>".$diasCama."</td>
			</tr>";
		///////////////////////////////////////////////////////

		// Consulta indice de oucpación
		$q2 = "	SELECT
					id
				FROM
					".$wbasedatocyp."_000032
				WHERE 	Morano = '".$wanio."'
				AND		Mormes = '".$wmes."'
				AND 	Morcco = '".$auxcco."'
				AND 	Morcod = '27'";
		$resq2 = mysql_query( $q2, $conex ) or die( mysql_errno()." - Error en el query $q2 -".mysql_error() );
		$filas2 = mysql_num_rows($resq2);

		$indiceOcupacion = ($sumDiasOcupados/$camas) * 100;
		$indiceOcupacion = number_format( ($indiceOcupacion),2,".","" );

		echo "<tr class='fila1' align='center'>
				<td colspan=2 align=center> Indice de ocupación </td>
				<td align=center>".$indiceOcupacion."</td>
			</tr>";
		///////////////////////////////////////////////////////

		// Consulta de número de camas
		$q3 = "	SELECT
					id
				FROM
					".$wbasedatocyp."_000032
				WHERE 	Morano = '".$wanio."'
				AND		Mormes = '".$wmes."'
				AND 	Morcco = '".$auxcco."'
				AND 	Morcod = '34'";
		$resq3 = mysql_query( $q3, $conex ) or die( mysql_errno()." - Error en el query $q3 -".mysql_error() );
		$filas3 = mysql_num_rows($resq3);

		$numeroCamas = $contadorCamas;

		echo "<tr class='fila1' align='center'>
				<td colspan=2 align=center> Número de camas </td>
				<td align=center>".$numeroCamas."</td>
			</tr>";

		//Esta validacion se refiere a un cierre de cama incorrecto o que ubicaron a un paciente en una habitacion sin hacer el ingreso respectivo
		//en la tabla movhos_000032.
		//Cuando se repara la tabla movhos_000032 al no hacer cierre a las 23:59:00, es posible que algunos pacientes hayan ingresdo en el lapso de
		//tiempo entre las 22:59:00 (hora regular de cierre correcto) y las 23:59:00(Hora en que se debe cerrar), esos pacientes son registrados en la tabla
		//movhos_000067(historial de ocupacion de habitaciones de la tabla movhos_000020) y por esta razon el programa indicadores_hospitalarios_dia.php
		//en algunos casos no concuerda con los datos de este programa, el programa de indicadores_hospitalrios_dia.php se basa en la tabla movhos_000038(ciere diario)
		//y este se basa en la tabla movhos_000067(historial de ocupacion de habitaciones).

		if($wdias_cama_ocupada != $diasCama)
			{

			$wdiferencia = abs($wdias_cama_ocupada - $diasCama);

			echo "<tr class='fila1' align='center'>
					<td colspan=3 align=center>El historial de ocupacion de habitaciones no concuerda con los indicadores hospitalarios,
											   ya que no hubo un cierre correcto o un paciente fue ubicado en un habitación
											   sin registrar el ingreso respectivo en este centro de costos, la diferencia es de ($wdiferencia dias).</td>
				</tr>";
			}





		///////////////////////////////////////////////////////

		// Si se confirmó la grabación de movimiento operativo
		if($grabaMovOperativo=="on")
		{
			// Adición días cama
			if($filas1>0)
			{
				$qins = "UPDATE
							".$wbasedatocyp."_000032
						 SET Fecha_data = '".$fecha."', Hora_data = '".$hora."', Morcan = '".$diasCama."', Morfte = 'H', Mortip = 'P', Seguridad = 'C-".$seguridad."'
						 WHERE 	Morano = '".$wanio."'
						 AND	Mormes = '".$wmes."'
						 AND 	Morcco = '".$auxcco."'
						 AND 	Morcod = '12'";
				$resins = mysql_query( $qins, $conex ) or die( mysql_errno()." - Error en el query $qins -".mysql_error() );
			}
			else
			{
				$qins = "INSERT INTO
							".$wbasedatocyp."_000032
							(Medico,Fecha_data,Hora_data,Morano,Mormes,Morcco,Morcod,Morcan,Morfte,Mortip,Seguridad,id)
						 VALUES
							('costosyp','".$fecha."','".$hora."','".$wanio."','".$wmes."','".$auxcco."','12',".$diasCama.", 'H','P','C-".$seguridad."',NULL)
						";
				$resins = mysql_query( $qins, $conex ) or die( mysql_errno()." 5 - Error en el query $qins -".mysql_error() );
			}
			///////////////////////////////////////////////////////

			// Adición indice de oucpación
			if($filas2>0)
			{
				$qins = "UPDATE
							".$wbasedatocyp."_000032
						 SET Fecha_data = '".$fecha."', Hora_data = '".$hora."', Morcan = '".$indiceOcupacion."', Morfte = 'H', Mortip = 'S', Seguridad = 'C-".$seguridad."'
						 WHERE 	Morano = '".$wanio."'
						 AND	Mormes = '".$wmes."'
						 AND 	Morcco = '".$auxcco."'
						 AND 	Morcod = '27'";
				$resins = mysql_query( $qins, $conex ) or die( mysql_errno()." - Error en el query $qins -".mysql_error() );
			}
			else
			{
				$qins = "INSERT INTO
							".$wbasedatocyp."_000032
							(Medico,Fecha_data,Hora_data,Morano,Mormes,Morcco,Morcod,Morcan,Morfte,Mortip,Seguridad,id)
						 VALUES
							('costosyp','".$fecha."','".$hora."','".$wanio."','".$wmes."','".$auxcco."','27','".$indiceOcupacion."', 'H','S','C-".$seguridad."',NULL)
						";
				$resins = mysql_query( $qins, $conex ) or die( mysql_errno()." 6 - Error en el query $qins -".mysql_error() );
			}
			///////////////////////////////////////////////////////

			// Adición de número de camas
			if($filas3>0)
			{
				$qins = "UPDATE
							".$wbasedatocyp."_000032
						 SET Fecha_data = '".$fecha."', Hora_data = '".$hora."', Morcan = '".$numeroCamas."', Morfte = 'H', Mortip = 'S', Seguridad = 'C-".$seguridad."'
						 WHERE 	Morano = '".$wanio."'
						 AND	Mormes = '".$wmes."'
						 AND 	Morcco = '".$auxcco."'
						 AND 	Morcod = '34'";
				$resins = mysql_query( $qins, $conex ) or die( mysql_errno()." - Error en el query $qins -".mysql_error() );
			}
			else
			{
				$qins = "INSERT INTO
							".$wbasedatocyp."_000032
							(Medico,Fecha_data,Hora_data,Morano,Mormes,Morcco,Morcod,Morcan,Morfte,Mortip,Seguridad,id)
						 VALUES
							(
								'costosyp','".$fecha."','".$hora."','".$wanio."','".$wmes."','".$auxcco."','34','".$numeroCamas."', 'H','S','C-".$seguridad."',NULL
							)
						";
				$resins = mysql_query( $qins, $conex ) or die( mysql_errno()." 7 - Error en el query $qins -".mysql_error() );
			}
		}

		///////////////////////////////////////////////////////

		echo "</table><br><br>";
		$hasRow = true;
	}

	if( !$hasRow ){
		echo "<p align='center'>NO HAY INFORMACION PARA LA CONSULTA SUMINISTRADA</p>";
	}

	echo "<input type='hidden' name='wanio' value='".$wanio."'>";
	echo "<input type='hidden' name='wmes' value='".$wmes."'>";
	echo "<input type='hidden' name='cco' value='".$ccocod."'>";
	echo "<input type='hidden' name='wsubprocesos' value='".$wsubprocesos."'>";

	echo "<table align='center' width='240'>
		<tr>
			<td width='50%' align='left'>
				<INPUT type='button' value='Cerrar' onClick='javascript:cerrarVentana();' style='width:100'>
			</td>
			<td width='50%' align='right'>
				<INPUT type='submit' value='Retornar' style='width:100'>
			</td>
		</tr>";
	echo "</table><br>";

	echo "<input type='hidden' name='mostrar' value='off'>";

	echo "</form>";

//	mysql_close( $conex );
}
}
?>
</body>
</html>
