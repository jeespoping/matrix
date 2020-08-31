<?php
include_once("conex.php");
/************************************************************************************************************************************************************************
 * Modificaciones:
 * Octubre 22 de 2013       Edwar. * Los filtros de interconsultas se mejoran para que solo filtre las especialidades y especialistas que lo antendieron
                                     en el ingreso que se está consultando, y las fechas se llenan de acuerdo a las fechas de ingreso y egreso del paciente.
                                   * La sección de aclaraciones que antes estaba oculta se vuelve a habilitar.
 * Octubre 16 de 2013       Edwar. Se crea un formulario intermedio para pedir las fechas en las que se quiere filtrar las interconsultas para una impresión, tambien es posible
                                    filtrar por especialidad y por especialista para la sección de interconsultas.
                                    La sección de aclaraciones se inactiva y no se muestra en la impresión.
 * Agosto 12 de 2013		Edwar. Se crea la función interconsultasClasificadasPorEspecialidad(), para mostrar especialistas agrupados por Especialidad y se reordenan algunas secciones.
 * Julio 9 de 2013			Edwin Molina G. Se impide que ciertas consultar realizadas por especialistas se impriman.
 * Julio 2 de 2013			Edwin Molina G. Se hacen cambios para que en la impresión salga la interconsulta realizadas por médicos especialistas.
 * Abril 19 de 2013			Edwin Molina G.	Se corrige los diagnósticos secundarios, pues estos estaban todos con el diangóstico principal.
 ************************************************************************************************************************************************************************/

if(isset($accion))
{
    

    

    $data=array("html"=>"","error"=>0,"mensaje"=>"");

    switch ($accion) {
        case 'cargarEspecialistas':
            $sqlOrdEsp = "  SELECT  c51.Medcod, c51.Mednom, c51.Medesp
                            FROM    {$wbasedatoTEMP}_000140 AS c140
                                    INNER JOIN
                                    {$wbasedatoTEMP}_000051 AS c51 ON (c51.Medcod = SUBSTRING_INDEX( c140.Intmed, '-', 1 ))
                                    INNER JOIN
                                    {$wbasedatoTEMP}_000053 AS c53 ON (c53.Espcod = SUBSTRING_INDEX( c51.Medesp, '-', 1 ) )
                                    INNER JOIN
                                    {$wbasedatoTEMP}_000168 AS c168 ON (c168.Comhis = c140.Inthis
                                            AND c168.Coming = c140.Inting
                                            AND c168.Comdoc = c140.Intdoc
                                            AND c168.Commed = SUBSTRING_INDEX( c140.Intmed, '-', 1 )
                                            AND c168.Comest = 'on')
                            WHERE   SUBSTRING_INDEX( Medesp, '-', 1 ) like '{$codEspecialidad}'
                                    AND c140.Inthis = '{$whistoria_imp}'
                                    AND c140.Inting = '{$wingreso_imp}'
                            GROUP BY c51.Medcod
                            ORDER BY c51.Mednom";
            // echo "<pre>"; print_r($sqlOrdEsp); echo "</pre>";

            $options = '<option value="%">% TODOS %</option>';
            if($resOrdEsp = mysql_query( $sqlOrdEsp, $conex ))
            {
                while ($row = mysql_fetch_array($resOrdEsp))
                {
                    $options .= '<option value="'.$row['Medcod'].'">'.utf8_encode($row['Mednom']).'</option>';
                }
            }
            else
            {
                $data['error'] = 1;
                $data['mensaje'] = utf8_encode("No se ejecutó consulta de médicos");
            }

            $data["html"] = $options;
            break;

        default:
            # code...
            break;
    }
    echo json_encode($data);
    return;
}

/**
 * Busca la especialidad del Medico tratante
 *
 * @param $cod
 * @return unknown_type
 */
function especialidad( $cod ){

	global $conex;
	global $wbasedato;

	 $sql = "SELECT
				medesp
			FROM
				{$wbasedato}_000051, {$wbasedato}_000053
			WHERE
				medcod = '$cod'
				AND espcod = SUBSTRING_INDEX( medesp, '-', 1 )";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	if( $rows = mysql_fetch_array( $res ) ){
		return $rows[0];
	}
	else{
		return "NO APLICA";
	}
}

/**
 * Busca los registros anteriores y se crea un campo textarea para ellos
 *
 * @param $his
 * @return unknown_type
 */
function antecedentesAnteriores( $his, $ing ){

	global $conex;
	global $wbasedato;

	$antecedentes = array();
	$antecedentes['personales'] = "";
	$antecedentes['familiares'] = "";
	$antecedentes['aclaraciones'] = "";
	$antecedentes['ginecobstetricos'] = "";
	$antecedentes['evolucion'] = "";
	$antecedentes['interconsulta'] = "";

	$sql = "SELECT
				inthis, inting, intcom, intfec
			FROM
				{$wbasedato}_000140
			WHERE
				inthis = '$his'
				AND inting = '$ing'
			ORDER BY intfec, inthor desc";

	$sql = "SELECT
				inthis, inting, intcom, intfec, intmed, intcie, intdx1, intdx2
			FROM
				{$wbasedato}_000140
			WHERE
				inthis = '$his'
				AND inting = '$ing'
			ORDER BY intmed, intfec, inthor desc";

	$sql = "SELECT
				inthis, inting, intcom, intfec, intmed, intcie, intdx1, intdx2
			FROM
				{$wbasedato}_000140 a, {$wbasedato}_000051 b, {$wbasedato}_000053 c
			WHERE
				inthis = '$his'
				AND inting = '$ing'
				AND SUBSTRING_INDEX( intmed, '-', 1 ) = Medcod
				AND SUBSTRING_INDEX( Medesp, '-', 1 ) = Espcod
				AND espnim != 'on'
			ORDER BY intmed, intfec, inthor desc";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()."- Error en el query $sql - ".mysql_error() );

	for( $i = 0, $j = 0, $k = 0; $rows = mysql_fetch_array( $res ) ; $i++ ){

		// if( $rows['intcom'] != "." ){
			// $antecedentes['interconsulta'] .= "\r\n".$rows['intcom']."\n\r";
			// $i++;
		// }

		// $antecedentes['interconsulta'] .=  "\r\n<b>M&eacute;dico</b>: ".$rows['intmed']."\n\r";
		// $antecedentes['interconsulta'] .=  "\r\n".$rows['intcom']."\n\r";
		// $antecedentes['interconsulta'] .=  "\r\n<b>Diagn&oacute;stico principal</b>: ".diagnosticoCie10( $rows['intcie'] )."\n\r";
		// $antecedentes['interconsulta'] .=  "<b>Diagn&oacute;stico secundario</b>: ".$rows['intdx1']."\n\r";
		// $antecedentes['interconsulta'] .=  "<b>Diagn&oacute;stico secundario</b>: ".$rows['intdx2']."\n\r";
		// $antecedentes['interconsulta'] .=  "=================================================\n\r";
		$exp = explode( "-", $rows['intmed'] );
		$esp = especialidad( $exp[0] );

		$antecedentes['interconsulta'] .=  "\r\n<table cellspacing=0>";
		$antecedentes['interconsulta'] .=  "<tr>";

		if( $i == 0 ){
			$antecedentes['interconsulta'] .=  "<td width='200'><b>Especialista de la salud</b>:</td>";
			$antecedentes['interconsulta'] .=  "<td width='550'><b>".substr( $rows['intmed'], strpos( $rows['intmed'], '-' )+1 )."</b></td>";
		}
		else{
			$antecedentes['interconsulta'] .=  "<td style='border-top:1px #000 solid' width='200'><b>Especialista de la salud</b>:</td>";
			$antecedentes['interconsulta'] .=  "<td style='border-top:1px #000 solid' width='550'><b>".substr( $rows['intmed'], strpos( $rows['intmed'], '-' )+1 )."</b></td>";
		}

		$antecedentes['interconsulta'] .=  "</td>";
		$antecedentes['interconsulta'] .=  "</tr>";
		$antecedentes['interconsulta'] .=  "<tr>";
		$antecedentes['interconsulta'] .=  "<td><b>Especialidad</b>:</td>";
		$antecedentes['interconsulta'] .=  "<td><b>".substr( $esp, strpos($esp, '-' ) +1  )."</b></td>";
		$antecedentes['interconsulta'] .=  "</td>";
		$antecedentes['interconsulta'] .=  "</tr>";
		$antecedentes['interconsulta'] .=  "</table>";

		// $antecedentes['interconsulta'] .=  "<tr>";
		// $antecedentes['interconsulta'] .=  "<td colspan='2'>";
		$antecedentes['interconsulta'] .=  $rows['intcom']."\r\n\r\n";
		// $antecedentes['interconsulta'] .=  "\r\n<br></td>";
		// $antecedentes['interconsulta'] .=  "</tr>";

		$antecedentes['interconsulta'] .=  "<table>";
		$antecedentes['interconsulta'] .=  "<tr>";
		$antecedentes['interconsulta'] .=  "<td><b>Diagn&oacute;stico principal</b>:</td>";
		$antecedentes['interconsulta'] .=  "<td>".diagnosticoCie10( $rows['intcie'] )."</td>";
		$antecedentes['interconsulta'] .=  "</tr>";
		// $antecedentes['interconsulta'] .=  "<tr>";
		// $antecedentes['interconsulta'] .=  "<td><b>Diagn&oacute;stico secundario</b>:</td>";
		// $antecedentes['interconsulta'] .=  "<td>".$rows['intdx1']."</td>";
		// $antecedentes['interconsulta'] .=  "</tr>";
		// $antecedentes['interconsulta'] .=  "<tr>";
		// $antecedentes['interconsulta'] .=  "<td><b>Diagn&oacute;stico secundario</b>: </td>";
		// $antecedentes['interconsulta'] .=  "<td>".$rows['intdx2']."<td>";
		// $antecedentes['interconsulta'] .=  "</tr>";
		$antecedentes['interconsulta'] .=  "</table>";

	}

	//Se construye los textarea de solo lectura para agregar antes de los campos respectivos
	//por javascript
	// if( !empty( $antecedentes['interconsulta'] ) ){
		// $antecedentes['interconsulta'] = trim( $antecedentes['interconsulta'] );
		// $antecedentes['interconsulta'] = "<textarea name=taIntcom readonly rows=5>{$antecedentes['interconsulta']}</textarea>";
	// }

	return $antecedentes;
}

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

function interconsultasClasificadasPorEspecialidad($his, $ing, $fecha_inicio, $fecha_fin, $wespecialidad, $wespecialista)
{
    global $conex;
    global $wbasedato;

    $antecedentes = "";
    // Consulta las interconsultas del paciente y las clasifica por especialidades
    /*$sqlOrdEsp = "  SELECT  Intfec,
                            Inthor,
                            Intcie,
                            Intcom,
                            Intmed,
                            SUBSTRING_INDEX( Medesp, '-', 1 ) codigo_especialidad,
                            Medesp, Espnom as nombre_especialidad
                    FROM    {$wbasedato}_000140 AS c140
                            INNER JOIN
                            {$wbasedato}_000051 AS c51 ON (Medcod = SUBSTRING_INDEX( Intmed, '-', 1 ))
                            INNER JOIN
                            {$wbasedato}_000053 AS c53 ON (Espcod = SUBSTRING_INDEX( Medesp, '-', 1 ))
                    WHERE   Inthis = '$his'
                            AND Inting = '$ing'
                    ORDER BY SUBSTRING_INDEX( Medesp, '-', 1 ) ASC, intfec DESC, inthor DESC";*/

    $filtroFechas = '';
    if($fecha_inicio != '')
    {
        $filtroFechas = "AND c168.Comfec BETWEEN '".$fecha_inicio." 00:00:00' AND '".$fecha_fin." 23:59:59'";
    }

    $filtroEspecialidad = '';
    if($wespecialidad != '' && $wespecialidad != '%')
    {
        $filtroEspecialidad = "AND c53.Espcod = '".$wespecialidad."'";
    }

    $filtroEspecialista = '';
    if($wespecialista != '' && $wespecialista != '%')
    {
        $filtroEspecialista = "AND c51.Medcod = '".$wespecialista."'";
    }

    $sqlOrdEsp = "  SELECT  c168.Comfec,
                            c168.Comhor,
                            c168.Comcom,
                            Intcie,
                            Intmed,
                            SUBSTRING_INDEX( Medesp, '-', 1 ) codigo_especialidad,
                            Medesp, Espnom as nombre_especialidad
                    FROM    {$wbasedato}_000140 AS c140
                            INNER JOIN
                            {$wbasedato}_000051 AS c51 ON (Medcod = SUBSTRING_INDEX( Intmed, '-', 1 ))
                            INNER JOIN
                            {$wbasedato}_000053 AS c53 ON (Espcod = SUBSTRING_INDEX( Medesp, '-', 1 ) {$filtroEspecialidad})
                            INNER JOIN
                            {$wbasedato}_000168 AS c168 ON (c168.Comhis = c140.Inthis
                                                            AND c168.Coming = c140.Inting
                                                            AND c168.Comdoc = c140.Intdoc
                                                            AND c168.Commed = SUBSTRING_INDEX( c140.Intmed, '-', 1 )
                                                            {$filtroFechas})
                    WHERE   Inthis = '$his'
                            AND Inting = '$ing'
                            {$filtroEspecialista}
                    ORDER BY SUBSTRING_INDEX( Medesp, '-', 1 ) ASC, c168.Comfec DESC, c168.Comhor DESC";

    // echo "<pre>"; print_r($sqlOrdEsp); echo "</pre>";
    $resOrdEsp = mysql_query( $sqlOrdEsp, $conex ) or die( mysql_errno()." - Error en el query <pre>$sqlOrdEsp</pre> - ".mysql_error() );
    $arr_especialidadPac = array(); // Especialidades que estan asociadas al paciente.
    /*
        while ($rowEsp = mysql_fetch_array($resOrdEsp))
        {
            if( $rowEsp['Comcom'] != "." || !empty($rowEsp['Comcom']) )
            {
                $cod_especialidad = $rowEsp['codigo_especialidad'];
                if(!array_key_exists($cod_especialidad, $arr_especialidadPac))
                {
                    $arr_especialidadPac[$cod_especialidad] = array("nombre_especialidad"=>$rowEsp['nombre_especialidad'],"arr_especialistas"=>array());
                }
                $exp_med = explode( "-", $rowEsp['Intmed'] );
                $codigo_medico = $exp_med[0];
                $nombre_medico = $exp_med[1];

                if(!array_key_exists($codigo_medico, $arr_especialidadPac[$cod_especialidad]["arr_especialistas"]))
                {
                    $arr_especialidadPac[$cod_especialidad]["arr_especialistas"][$codigo_medico]['info_especialista'] =
                                                                                        array(
                                                                                        "codigo_especialista" => $codigo_medico,
                                                                                        "nombre_especialista" => $nombre_medico,
                                                                                        "fecha_bloque"        => $rowEsp['Comfec'],
                                                                                        "hora_bloque"         => $rowEsp['Comhor'],
                                                                                        "diagnostico"         => diagnosticoCie10( $rowEsp['Intcie'] ));
                    $arr_especialidadPac[$cod_especialidad]["arr_especialistas"][$codigo_medico]['registros'] = array();
                }
               $arr_especialidadPac[$cod_especialidad]["arr_especialistas"][$codigo_medico]['registros'][] = trim( htmlentities(  $rowEsp['Comcom'], ENT_QUOTES  ) );
            }
        }
    */

    while ($rowEsp = mysql_fetch_array($resOrdEsp))
    {
        if( $rowEsp['Comcom'] != "." || !empty($rowEsp['Comcom']) )
        {
            $cod_especialidad = $rowEsp['codigo_especialidad'];
            if(!array_key_exists($cod_especialidad, $arr_especialidadPac))
            {
                $arr_especialidadPac[$cod_especialidad] = array("nombre_especialidad"=>$rowEsp['nombre_especialidad'],"arr_especialistas"=>array(),"comentarios"=>array());
            }
            $exp_med = explode( "-", $rowEsp['Intmed'] );
            $codigo_medico = $exp_med[0];
            $nombre_medico = $exp_med[1];


            $arr_especialidadPac[$cod_especialidad]['comentarios'][] = "<b>".$rowEsp['Comfec'].", ".$rowEsp['Comhor']."</b><br>"."
                            ".str_replace( PHP_EOL, '<br />', trim($rowEsp['Comcom']) )."
                            <table cellspacing=0>
                                <tr>
                                    <td style='font-size:7pt' width='130'><b>Especialista de la salud</b>:</td>
                                    <td style='font-size:7pt' width=''><b>".$codigo_medico.'-'.$nombre_medico." [".$rowEsp['nombre_especialidad']."]</b></td>
                                </tr>
                                <tr>
                                    <td style='font-size:7pt'><b>Diagn&oacute;stico principal</b>:</td>
                                    <td style='font-size:7pt'><b>".diagnosticoCie10( $rowEsp['Intcie'] )."</b></td>
                                </tr>
                            </table>";
            /*
            <tr>
                <td style='font-size:7pt'><b>Especialidad</b>:</td>
                <td style='font-size:7pt'><b>".$rowEsp['nombre_especialidad']."</b></td>
            </tr>
            */


            /*if(!array_key_exists($codigo_medico, $arr_especialidadPac[$cod_especialidad]["arr_especialistas"]))
            {
                $arr_especialidadPac[$cod_especialidad]["arr_especialistas"][$codigo_medico]['info_especialista'] =
                                                                                    array(
                                                                                    "codigo_especialista" => $codigo_medico,
                                                                                    "nombre_especialista" => $nombre_medico,
                                                                                    "fecha_bloque"        => $rowEsp['Comfec'],
                                                                                    "hora_bloque"         => $rowEsp['Comhor'],
                                                                                    "diagnostico"         => diagnosticoCie10( $rowEsp['Intcie'] ));
                $arr_especialidadPac[$cod_especialidad]["arr_especialistas"][$codigo_medico]['registros'] = array();
            }
            $arr_especialidadPac[$cod_especialidad]["arr_especialistas"][$codigo_medico]['registros'][] = trim( htmlentities(  $rowEsp['Comcom'], ENT_QUOTES  ) );*/
        }
    }

    // echo "<pre>"; print_r($arr_especialidadPac); echo "</pre>"; exit();
    // exit();

    foreach($arr_especialidadPac as $cod_especialidad => $arr_comentarios)
    {
        $nom_especialidad = $arr_especialidadPac[$cod_especialidad]['nombre_especialidad'];
        $antecedentes .= '
                <br>
                <br>
                <table cellspacing="0" border="0" cellspading="0">
                    <tr>
                        <td style="font-size:10pt;font-weight:bold;border:1px solid #000000;border:1px solid #000000;width:750px;">ESPECIALIDAD: '.$nom_especialidad.'</td>
                    </tr>
                </table>';

        /*foreach ($arr_especialistas['arr_especialistas'] as $codigo_especialista => $info)
        {
            $info_especialista = $info['info_especialista'];
            $arr_registros = $info['registros'];
            $antecedentes .=  "
                <table cellspacing=0>
                    <tr>
                        <td style='border-top:1px #000 solid' width='200'><b>Especialista de la salud</b>:</td>
                        <td style='border-top:1px #000 solid' width='550'><b>".$info_especialista['codigo_especialista'].'-'.$info_especialista['nombre_especialista']."</b></td>
                        </td>
                    </tr>
                    <tr>
                        <td><b>Especialidad</b>:</td>
                        <td><b>".$nom_especialidad."</b></td>
                        </td>
                    </tr>
                </table>
                <br>
                ".str_replace("\n","<br>",implode("\n\n**\n\n", $arr_registros))."
                <br>
                <br>
                <table style='width:;'>
                    <tr>
                        <td><b>Diagn&oacute;stico principal</b>:</td>
                        <td>".$info_especialista['diagnostico']."</td>
                    </tr>
                </table>
                <br>";
        }*/

        foreach ($arr_comentarios['comentarios'] as $key => $info)
        {
            $antecedentes .=  $info."<br>";
        }


            /*$exp = explode( "-", $rows['intmed'] );
            $esp = especialidad( $exp[0] );

            $antecedentes .=  "\r\n<table cellspacing=0>";
            $antecedentes .=  "<tr>";

            if( $i == 0 ){
                $antecedentes .=  "<td width='200'><b>Especialista de la salud</b>:</td>";
                $antecedentes .=  "<td width='550'><b>".substr( $rows['intmed'], strpos( $rows['intmed'], '-' )+1 )."</b></td>";
            }
            else{
                $antecedentes .=  "<td style='border-top:1px #000 solid' width='200'><b>Especialista de la salud</b>:</td>";
                $antecedentes .=  "<td style='border-top:1px #000 solid' width='550'><b>".substr( $rows['intmed'], strpos( $rows['intmed'], '-' )+1 )."</b></td>";
            }

            $antecedentes .=  "</td>";
            $antecedentes .=  "</tr>";
            $antecedentes .=  "<tr>";
            $antecedentes .=  "<td><b>Especialidad</b>:</td>";
            $antecedentes .=  "<td><b>".substr( $esp, strpos($esp, '-' ) +1  )."</b></td>";
            $antecedentes .=  "</td>";
            $antecedentes .=  "</tr>";
            $antecedentes .=  "</table>";

            $antecedentes .=  $rows['intcom']."\r\n\r\n";

            $antecedentes .=  "<table style='width:100%;'>";
            $antecedentes .=  "<tr>";
            $antecedentes .=  "<td><b>Diagn&oacute;stico principal</b>:</td>";
            $antecedentes .=  "<td>".diagnosticoCie10( $rows['intcie'] )."</td>";
            $antecedentes .=  "</tr>";
            $antecedentes .=  "</table>";*/

    }

    // $arr_imprimir["interconsulta"] = $arr_especialidadPac;
    return $antecedentes;
}

if( (!empty($his) && !empty($his)) && !isset($imprimir))
{
    ?>
        <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
        <script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
        <link href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet" />
        <script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>
        <script type="text/javascript">

        $(function(){
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
                yearSuffix: '',
                changeYear: true,
                changeMonth: true,
                yearRange: '-100:+0'
            };
            $.datepicker.setDefaults($.datepicker.regional['esp']);


            $("#fecha_inicio").datepicker({
              showOn: "button",
              buttonImage: "../../images/medical/root/calendar.gif",
              buttonImageOnly: true,
              maxDate:"+0D"
            });

            $("#fecha_fin").datepicker({
              showOn: "button",
              buttonImage: "../../images/medical/root/calendar.gif",
              buttonImageOnly: true,
              maxDate:"+0D"
            });
        });

        function especialistasSelect(ele)
        {
            // console.log($(ele).val());
            $.post("impresionHC.php",
            {
                    accion          : 'cargarEspecialistas',
                    consultaAjax    : '',
                    wbasedato       : $("#wbasedato").val(),
                    whistoria_imp   : $("#whistoria_imp").val(),
                    wingreso_imp    : $("#wingreso_imp").val(),
                    wbasedatoTEMP   : $("#wbasedatoTEMP").val(),
                    codEspecialidad : $(ele).val()
                },
                function(data){
                    if(data.error == 1)
                    {
                        alert(data.mensaje);
                    }
                    else
                    {
                        $("#wespecialista").html(data.html);
                    }
                },
                "json"
            );
        }

        function validarFecha()
        {
            if($("#fecha_inicio").val() == '' || $("#fecha_fin").val()=='')
            {
                alert("Campo de fecha incompleto");
                return false;
            }
            document.forms[0].submit();
        }

        </script>
        <style type="text/css">
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
        <?php
}


include_once("root/comun.php");

if(!isset($wemp_pmla)){
    terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}
$key = substr($user, 2, strlen($user));

$conex = obtenerConexionBD("matrix");

$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

$wbasedato = strtolower($institucion->baseDeDatos);
$wentidad = $institucion->nombre;

if( (!empty($his) && !empty($his)) && !isset($imprimir))
{
    encabezado("REPORTE DE HISTORIA CLINICA - FILTRO PARA INTERCONSULTAS", "2013-07-09", "logo_".$wbasedato );
}

//El usuario se encuentra registrado
if( !isset($_SESSION['user']) ){
	echo "Error: Usuario No registrado";
}
else{

	if( !isset($his) || empty($his) ){
		//OPCIONES DE IMPRESION DE LA HISTORIA CLINICA

		encabezado("REPORTE DE HISTORIA CLINICA", "2013-07-09", "logo_".$wbasedato );

		echo "<form method=post>";

		if( !isset($enf) ){
			echo "<INPUT type='hidden' name='enf' value='off'>";
			$enf = 'off';
		}
		else{
			echo "<INPUT type='hidden' name='enf' value='$enf'>";
		}

		echo "<input type='hidden' name='wemp_pmla' value='$wemp_pmla'>";

		// echo "<center><b>El '%' es el comodin.<br>Ejemplo Nombre: %consuelo%</b></center><br><br>";

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
					hclfec, hclhor, hclmce, hclapf, hclafa, hclefi, hclcon, hclcie, hclacl, hclmed, hclnom, hclhis, hcldoc, (hcling+0) as hcling
				FROM
					{$wbasedato}_000139
				WHERE
					hclhis like '$bsHis%'
					AND hcldoc like '$bsDoc%'
					AND hclnom like '$bsNom%'
				GROUP BY hclhis, hcling
				ORDER BY hclhis desc, hcling desc
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
					echo "<td align='right'>{$rows['hclhis']}-{$rows['hcling']}</td>";
					echo "<td align='right'>{$rows['hcldoc']}</td>";
					echo "<td>{$rows['hclnom']}</td>";
					echo "<td><a href='impresionHC.php?wemp_pmla=$wemp_pmla&his={$rows['hclhis']}&enf=$enf&ing={$rows['hcling']}' target='blank'>Imprimir</a></td>";
					echo "</tr>";
				}

				echo "</table>";
			}

		}

		echo "</form>";

	}
    elseif(!isset($imprimir))
    {

        echo "<form method=post>
                <input type='hidden' id='whistoria_imp' name='whistoria_imp' value='".$his."'>
                <input type='hidden' id='wingreso_imp' name='wingreso_imp' value='".$ing."'>
                <input type='hidden' id='wbasedatoTEMP' name='wbasedatoTEMP' value='".$wbasedato."'>
                <input type='hidden' id='imprimir' name='imprimir' value='ok'>";

        if( !isset($enf) ){
            echo "<INPUT type='hidden' name='enf' value='off'>";
            $enf = 'off';
        }
        else{
            echo "<INPUT type='hidden' name='enf' value='$enf'>";
        }

        echo "<input type='hidden' name='wemp_pmla' value='$wemp_pmla'>";

        // Lista de especialidades
        $sql = "SELECT  c53.Espcod, c53.Espnom
                FROM    {$wbasedato}_000140 AS c140
                        INNER JOIN
                        {$wbasedato}_000051 AS c51 ON (c51.Medcod = SUBSTRING_INDEX( c140.Intmed, '-', 1 ))
                        INNER JOIN
                        {$wbasedato}_000053 AS c53 ON (c53.Espcod = SUBSTRING_INDEX( c51.Medesp, '-', 1 ) )
                        INNER JOIN
                        {$wbasedato}_000168 AS c168 ON (c168.Comhis = c140.Inthis
                                        AND c168.Coming = c140.Inting
                                        AND c168.Comdoc = c140.Intdoc
                                        AND c168.Commed = SUBSTRING_INDEX( c140.Intmed, '-', 1 ))
                WHERE   Inthis = '{$his}'
                        AND Inting = '{$ing}'
                GROUP BY c53.Espcod
                ORDER BY c53.Espnom";

        $resultEsplista = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query - ".mysql_error() );
        $options = '';
        while ($rowEsp = mysql_fetch_array($resultEsplista))
        {
            $options .= '
                        <option value="'.$rowEsp['Espcod'].'">'.$rowEsp['Espnom'].'</option>';
        }

        $sqlX = "SELECT Ingfei FROM {$wbasedato}_000101 WHERE   Inghis='{$his}' AND Ingnin = '{$ing}'";
        $resultX = mysql_query( $sqlX, $conex ) or die( mysql_errno()." - Error en el query - ".mysql_error() );
        $rowX = mysql_fetch_array($resultX);
        $fechaInicioX = date("Y-m-d");
        if($rowX['Ingfei'] != '')
        {
            $fechaInicioX = $rowX['Ingfei'];
        }

        $sqlX = "SELECT  Pacact FROM {$wbasedato}_000100 WHERE Pachis='{$his}'";
        $resultX = mysql_query( $sqlX, $conex ) or die( mysql_errno()." - Error en el query - ".mysql_error() );
        $rowX = mysql_fetch_array($resultX);
        $fechaFinX = date("Y-m-d");
        if($rowX['Pacact'] != 'on')
        {
            $fechaFinX = date("Y-m-d");
        }
        else
        {
            //
            $sqlX = "SELECT Egrfee FROM {$wbasedato}_000108 WHERE Egrhis='{$his}' AND Egring = '{$ing}'";
            $resultX = mysql_query( $sqlX, $conex ) or die( mysql_errno()." - Error en el query - ".mysql_error() );
            $rowX = mysql_fetch_array($resultX);

            if($rowX['Egrfee'] != '')
            {
                $fechaFinX = $rowX['Egrfee'];
            }
        }

        // echo "<center><b>El '%' es el comodin.<br>Ejemplo Nombre: %consuelo%</b></center><br><br>";

        echo "<br><table align='center'>
                <tr class='encabezadotabla'>
                    <td colspan='2'>Filtro para interconsultas</td>
                </tr>
                <tr class='fila1'>
                    <td>Feche Inicio</td>
                    <td><INPUT TYPE='text' id='fecha_inicio' name='fecha_inicio' value='".$fechaInicioX."'></td>
                </tr>
                <tr class='fila1'>
                    <td>Fecha fin</td>
                    <td><INPUT TYPE='text' id='fecha_fin' name='fecha_fin' value='".$fechaFinX."'></td>
                </tr>
                <tr class='fila1'>
                    <td>Especialidad</td>
                    <td>
                        <select id='wespecialidad' name='wespecialidad' onchange='especialistasSelect(this);'>
                            <option value='%'>% TODOS %</option>
                            ".$options."
                        </select>
                    </td>
                </tr>
                <tr class='fila1'>
                    <td>Especialista</td>
                    <td>
                        <select id='wespecialista' name='wespecialista'>
                            <option value='%'>% TODOS %</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan='2' align='center'><br><INPUT TYPE='button' value='Consultar' style='width:100' onclick='validarFecha();'></td>
                </tr>
            </table>";


        if( ( isset($bsNom) && !empty($bsNom) )
            || ( isset($bsHis) && !empty($bsHis) )
            || ( isset($bsDoc) && !empty($bsDoc) ) ){

            $sql = "SELECT
                    hclfec, hclhor, hclmce, hclapf, hclafa, hclefi, hclcon, hclcie, hclacl, hclmed, hclnom, hclhis, hcldoc, (hcling+0) as hcling
                FROM
                    {$wbasedato}_000139
                WHERE
                    hclhis like '$bsHis%'
                    AND hcldoc like '$bsDoc%'
                    AND hclnom like '$bsNom%'
                GROUP BY hclhis, hcling
                ORDER BY hclhis desc, hcling desc
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
                    echo "<td align='right'>{$rows['hclhis']}-{$rows['hcling']}</td>";
                    echo "<td align='right'>{$rows['hcldoc']}</td>";
                    echo "<td>{$rows['hclnom']}</td>";
                    echo "<td><a href='impresionHC.php?wemp_pmla=$wemp_pmla&his={$rows['hclhis']}&enf=$enf&ing={$rows['hcling']}' target='blank'>Imprimir</a></td>";
                    echo "</tr>";
                }

                echo "</table>";
            }

        }

        echo "</form>";
    }
	else{

		?>
		<style>
		table{
			 font-size:8pt
		}
		</style>
		<?php
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
					hclhis like '$his'
					AND hcling like '$ing'
				ORDER BY hclfec, hclhor desc";
        // echo ".....".$sql;

		$res = mysql_query( $sql, $conex ) or die( mysql_errno(). "- Error en el query $sql - ".mysql_error() );
		$numrows = mysql_num_rows( $res );

		//TITULO DE LA HISTORIA CLINICA
		echo
			"<table  border=1 frame='border' cellspacing=0 width='750' cellspading=0  style='font-size:8pt'>
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
				$cie1 = diagnosticoCie10( $rows['hclci1'] );	//Abril 19 de 2013
				$cie2 = diagnosticoCie10( $rows['hclci2'] );	//Abril 19 de 2013
				$cie3 = diagnosticoCie10( $rows['hclci3'] );	//Abril 19 de 2013

				$codreg = explode( "-", $rows['hclmed'] );	//codigo del medico

				if( count( $codreg) > 0 ){
					$regMed = registroMedico( $codreg[0] );
				}

                $fecha_inicio  = (!isset($fecha_inicio )) ? '': $fecha_inicio;
                $fecha_fin     = (!isset($fecha_fin )) ? '': $fecha_fin;
                $wespecialidad = (!isset($wespecialidad )) ? '': $wespecialidad;
                $wespecialista = (!isset($wespecialista)) ? '': $wespecialista;

				$rows['hclico'] = interconsultasClasificadasPorEspecialidad($his, $ing, $fecha_inicio, $fecha_fin, $wespecialidad, $wespecialista);

				// $rows['hclico'] = antecedentesAnteriores( $his, $ing );
				// $rows['hclico'] = trim( $rows['hclico'][ 'interconsulta' ] );

				$rows['hclmce'] = str_replace("\n","<br>",$rows['hclmce']);
				$rows['hclapf'] = str_replace("\n","<br>",$rows['hclapf']);
				$rows['hclafa'] = str_replace("\n","<br>",$rows['hclafa']);
				$rows['hclgin'] = str_replace("\n","<br>",$rows['hclgin']);
				$rows['hclcon'] = str_replace("\n","<br>",$rows['hclcon']);
				$rows['hclefi'] = str_replace("\n","<br>",$rows['hclefi']);
				// $rows['hclico'] = str_replace("\n","<br>",$rows['hclico']);
				$rows['hclacl'] = str_replace("\n","<br>",$rows['hclacl']);
				$rows['hclevo'] = str_replace("\n","<br>",$rows['hclevo']);
				$rows['hclrsa'] = str_replace("\n","<br>",$rows['hclrsa']);

				if( $rows['hclacl'] == '.' ){
					$rows['hclacl'] = 'NO APLICA';
				}

				if( $rows['hclico'] == '.' || $rows['hclico'] == '' ){
					$rows['hclico'] = 'NO APLICA';
				}

				if( $i == 0 ){

					//ENCABEZADO DE LA HISTORIA CLINICA
					//INFORMACION DEMOGRAFICA
					echo
						"<table border=1 frame='border' cellspacing=0 width='750' cellspading=0 >
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
							<td><b>MOTIVO DE LA CONSULTA Y ENFERMEDAD ACTUAL</b></td>
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
							<td><b>EVOLUCION</b></td>
						</tr>
						<tr>
							<td><div style='word-wrap: break-word; width:745'>{$rows['hclevo']}</div><br><br></td>
						</tr>

						<tr>
							<td><b>RECOMENDACIONES Y SIGNOS DE ALARMA</b></td>
						</tr>
						<tr>
							<td><div style='word-wrap: break-word; width:745'>{$rows['hclrsa']}</div><br><br></td>
						</tr>

                        <tr>
                            <td><b>CONDUCTA</b></td>
                        </tr>
                        <tr>
                            <td><div style='word-wrap: break-word; width:745'>{$rows['hclcon']}</div><br><br></td>
                        </tr>

                        <tr>
                            <td><b>ACLARACIONES HCE</b></td>
                        </tr>
                        <tr>
                            <td><div style='word-wrap: break-word; width:745'>{$rows['hclacl']}</div><br><br></td>
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

						<tr>
							<td><b>CONSULTAS</b></td>
						</tr>
						<tr>
							<td><div style='word-wrap: break-word; width:725;margin-left:10px;text-align:justify;'>{$rows['hclico']}</div><br><br></td>
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
