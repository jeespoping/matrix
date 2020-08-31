<?php
include_once("conex.php");
/**
 PROGRAMA                   : rep_ent_y_rec-pac.php
 AUTOR                      : Edwar Jaramillo.
 FECHA CREACION             : 14 Mayo de 2012

 DESCRIPCION:
 Reporte de Recibo y entrega de paciente, en este reporete se muestran dos culumnas, en la primer columna
 se listan los datos de entrega y en la segunda columna se listan los datos de recibo, en cada uno de los casos
 se muestra la lista de medicamentos o artículos con los que se entregó o se recibió al paciente
 (en algunos casos no hay detalle de articulos).

 ACTUALIZACIONES:
 *  xx xx xxxx
    yyyyy yyyy          :
 *  Mayo 14 de 2012
    Edwar Jaramillo     : Fecha de la creación del reporte.
	
 *  Febrero 21 de 2019
    Juan C Hdez         : Se adiciona el filtro multiempresa para la tabla root_000036 en el query principal

 *  Febrero 28 de 2019
    Arleyda I.C.        : Migración realizada.   

**/
$wactualiz = "Febrero 21 de 2019";
?>
<head>
    <title>Reporte de Entrega y Recibo de Pacientes</title>
    <script type='text/javascript' src='../../../include/root/jquery-1.3.2.js'></script>
    <script type='text/javascript' src='../../../include/root/jquery.blockUI.min.js'></script>
    <style type="text/css">
        A   {text-decoration: none;color: #000066;}
        .tipo3V{color:#000066;background:#dddddd;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px}
        .tipo3V:hover {color: #000066; background: #999999;}
    </style>
</head>
<script type="text/javascript">
   function cerrarVentana()
    {
        window.close()
    }

    function enter()
    {
        document.rep_ent_rec.submit();
    }

    //FUNCION QUE PERMITE GENERAR UNA VENTANA EMERGENTE CON UN PATH ESPECIFICO
    function ejecutar(path)
    {
        window.open(path,'','fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=0,scrollbars=1,titlebar=0');
    }
</script>
<body>

<?php

//*****************************************************************************************************
//                                              F U N C I O N E S
//=====================================================================================================

/**
 * Función buscar_articulo(&$wcodart), para buscar la descripción de los articulos según el código que llega por parámetro
 *
 * @param $wcodart: parámetro por referencia que corresponde al código de un articulo.
 * @return $wcodart: código de articulo modificado.
 * @return $wartnom: variable global modificada, corresponde al nombre del articulo.
 * @return $wunides: variable global modificada, corresponde a las unidades (cantidad) del articulo.
 */
function buscar_articulo(&$wcodart)
{
    global $wbasedato;
    global $wcenmez;
    global $conex;
    global $wok;
    global $wartnom;
    global $wartuni;
    global $wunides;

    // Busco el nombre del articulo en el maestro de  articulos de movhos
    $q = " SELECT artcom, artuni, unides "
    . "   FROM " . $wbasedato . "_000026, " . $wbasedato . "_000027 "
    . "  WHERE artcod = '" . $wcodart . "'"
    . "    AND artuni = unicod ";
    $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
    $num = mysql_num_rows($res);
    if ($num > 0)
    {
        $row = mysql_fetch_array($res);
        $wartnom = $row[0];
        $wartuni = $row[1];
        $wunides = $row[2];
        $wok = "on";
    }
    else
    {
        // Busco el nombre del articulo en la base de datos de central de mezclas
        $q = " SELECT artcom, artuni, unides "
        . "   FROM " . $wcenmez . "_000002, " . $wbasedato . "_000027 "
        . "  WHERE artcod = '" . $wcodart . "'"
        . "    AND artuni = unicod ";
        $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
        $num = mysql_num_rows($res);
        if ($num > 0)
        {
            $row = mysql_fetch_array($res);
            $wartnom = $row[0];
            $wartuni = $row[1];
            $wunides = $row[2];
            $wok = "on";
        }
        else
        {
            // Busco el nombre del articulo en la base de datos de central de 'movhos', pero buscando con el
            // codigo del proveedor en la tabla movhos_000009
            $wcodart=BARCOD($wcodart);
            $q = " SELECT artcom, artuni, unides, " . $wbasedato . "_000009.artcod "
            . "   FROM " . $wbasedato . "_000009, " . $wbasedato . "_000026, " . $wbasedato . "_000027 "
            . "  WHERE artcba                       = '" . $wcodart . "'"
            . "    AND " . $wbasedato . "_000009.artcod = " . $wbasedato . "_000026.artcod "
            . "    AND artuni                       = unicod ";

            $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
            $num = mysql_num_rows($res);
            if ($num > 0)
            {
                $row = mysql_fetch_array($res);
                $wartnom = $row[0];
                $wartuni = $row[1];
                $wunides = $row[2];
                $wcodart = $row[3];
                $wok = "on";
            }
            else
            {
                $wartnom = "Codigo no existe";
                $wartuni = "";
                $wunides = "";
                $wok = "off";
            }
        }
    }
}

/**
 * Función buscar_usuario($wcod_funcionario), según un código de usuario se buscan sus nombres
 *
 * @param $wcod_funcionario:código o identidicador alfanumérico de un usuario, se busca en la tabla usuarios de matrix.
 *                          El código puede ser de la forma 'C-9999' ó '9999', la función valída si debe hacer explode o no.
 * @return array $usuario:  retorna un array con el código del funcionario y con el nombre o descripción del funcionario,
 *                          el array tiene los campos
 *                          array (
                                    'nombre' => '',
                                    'codigo' => ''
 *                                )
 */
function buscar_usuario($wcod_funcionario)
{
    global $conex;

    if (strpos($wcod_funcionario, '-'))
    {
        $explode = explode('-',$wcod_funcionario);
        $wcod_funcionario = $explode[1];
    }

    $usuario = array('nombre'=>'','codigo'=>'');
    $query = "  SELECT  Descripcion as nombre
                FROM    usuarios
                WHERE   Codigo = '$wcod_funcionario'";
    $res = mysql_query($query, $conex) or die ("Error: ".mysql_errno()." - en el query - Buscar usuarios: ".$query." - ".mysql_error());
    $num = mysql_num_rows($res);

    if ($num > 0)
    {
        $row = mysql_fetch_array($res);
        $usuario['nombre'] = $row['nombre'];
    }
    else
    { $usuario['nombre'] = 'Nombre de usuario no diponible'; }
    return $usuario;
}
/*****************************************************************************************************************************************/

/**
 * Función buscar_centro_costo($wbasedato, $wcco), esta función busca el nombre de un centro de costo a partir de su código.
 *
 * @param string $wbasedato :   Nombre de la empresa-tabla en la que se hace la búsqueda del centro de costo.
 * @param unknown $wcco     :   Código del centro de costos para el cual se va a buscar la descripción.
 * @return string           :   Retorna el nombre o descripción del centro de costos consultado.
 */
function buscar_centro_costo($wbasedato, $wcco)
{
    global $conex;

    $query = "  SELECT  Ccocod AS cco, Cconom
                FROM   ".$wbasedato."_000011
                WHERE   Ccocod = '$wcco'";
    $res = mysql_query($query, $conex) or die ("Error: ".mysql_errno()." - en el query - Buscar Centro costo: ".$query." - ".mysql_error());
    $row = mysql_fetch_array($res);
    return $row['Cconom'];
}
/*****************************************************************************************************************************************/

/**
 * Función consulta_movimiento($wbasedato, $accion=''), se encarga de buscar un número de historia y un número de ingreso en la tabla
 * movhos_000017, arrojando una lista de entregas y recibos del paciente consultado. La función retorna un array con tablas html armadas
 * que corresponden al detalle de la entrega y del recibo.
 *
 * @param string $wbasedato :   Nombre de la empresa-tabla en la que se hace la búsqueda de los traslados.
 * @param string $whis      :   Número de la historia a consultar en la tabla de traslados.
 * @param string $wing      :   Número de ingreso a consultar, para listar los traslados el paciente en ese ingreso.
 * @param string $accion    :   Parámetro aún no es usado en esta función, puede ser usado para listar solo las entregas
 *                              o solo los recibos (en este momento se retonan tanto entregas como recibos encontrados).
 * @return array $array_tablas_html :
 *                              En cada posición del array se guarda un nuevo array con los índices 'tabla' y 'accion'
 *                              en el primer índice se guarda el código html con el detalle del movimiento, el segundo
 *                              índice guarda la acción que se realizó en el movimiento, indica si es 'Entrega' o 'Recibo'
 *
 *                              array_tablas_html
 *                              (
 *                                  [0] => array
 *                                         (
 *                                              'tabla'     =>  <table> <tr> <td> aaaa <td> <td> bbbb <td> </tr> ... </tabla>
 *                                              'accion'    =>  Entrega
 *                                          ),
 *                                  [1] => array
 *                                         (
 *                                              'tabla'     =>  <table> <tr> <td> aaaa <td> <td> bbbb <td> </tr> ... </tabla>
 *                                              'accion'    =>  Recibo
 *                                          )
 *                                   ....
 *                              )
 */
function consulta_movimiento($wbasedato, $whis='', $wing='', $accion='')
{
    global $conex;
    global $wartnom;
    global $wartuni;
    global $wunides;
    $tabla_etq = '';
    $array_tablas_html = array();

    // CONSULTA 1:  Consulta todos los registros del paciente en la tabla de entregas y recibos del paciente asociado a la historia y el ingreso consultados
    $q = "  SELECT  Eyrnum, Fecha_data, Hora_data, Eyrsor AS serv_origen, Eyrsde AS serv_destino, Seguridad AS usuario, Eyrtip AS accion
                    , Eyrhor AS hab_orig, Eyrhde AS hab_dest, Eyrest AS estado_mov
            FROM    ".$wbasedato."_000017
            WHERE   eyrhis = '".$whis."'
                    AND eyring = '".$wing."'

            ORDER BY Fecha_data ASC, Hora_data ASC";

    $res_17 = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $num = mysql_num_rows($res_17);

    if ($num > 0)
    {
        while($row_17 = mysql_fetch_array($res_17))
        {
            // CONSULTA 2:   Consulta los articulos que se entregaron con el paciente para cada uno de los registros que se listan en CONSULTA 1.
            $q = "  SELECT  Detart, sum(Detcan) as cantidad
                    FROM    ".$wbasedato."_000019
                    WHERE   detnum = '".$row_17['Eyrnum']."'
                    GROUP BY Detart
                    ORDER BY Detart";

            $res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q . " - ".mysql_error());
            $num = mysql_num_rows($res);


            $cama = '';
            $accion = $row_17['accion'];
            $estado_mov = $row_17['estado_mov'];

            if ($estado_mov == 'on' || ($estado_mov == 'off' && $num > 0))
            {
                // $encabezado_arr es un array que se encarga de mostras los mensajes apropiados en la cabezara de cada acción
                // por ejemplo, mostra en los encabezados 'Servicio Origen' cuando es una entrega o 'Servicio destino' cuando es un recibo
                $encabezado_arr = array();
                if ($accion == 'Entrega')
                {
                    $cama = $row_17['hab_orig'];
                    $encabezado = array(
                                            'enc_fecha'     =>'Fecha entrega',
                                            'enc_servicio'  =>'Servicio origen',
                                            'enc_accion'    =>'Entreg&oacute;',
                                            'ccosto'        =>buscar_centro_costo($wbasedato, $row_17['serv_origen']));
                }
                else
                {
                    $serv = $row_17['serv_destino'];
                    $cama = $row_17['hab_dest'];
                    $encabezado = array(
                                            'enc_fecha'     =>'Fecha recib&iacute;do',
                                            'enc_servicio'  =>'Servicio destino',
                                            'enc_accion'    =>'Recibi&oacute;',
                                            'ccosto'        =>buscar_centro_costo($wbasedato, $row_17['serv_destino']));
                }

                $usuario = buscar_usuario($row_17['usuario']);

                // Se arma el encabezado de cada detalle del movimiento.
                $tabla_etq = "
                <table  width='100%'>
                    <tr>
                        <td colspan='4' class='encabezadoTabla'>".$encabezado['enc_fecha'].":</td>
                        <td colspan='4' class='fila1' style='font-weight:bold;'>".$row_17['Fecha_data'].' '.$row_17['Hora_data']."</td>
                    </tr>
                    <tr class='encabezadoTabla'>
                        <td colspan='4' class='encabezadoTabla'>".$encabezado['enc_servicio'].":</td>
                        <td colspan='4' class='fila1'>".$encabezado['ccosto']." (Cama-$cama)</td>
                    </tr>
                    <tr class='encabezadoTabla'>
                        <td colspan='4' class='encabezadoTabla'>".$encabezado['enc_accion'].":</td>
                        <td colspan='4' class='fila1'>".$usuario['nombre']."</td>
                    </tr>
                </table>
                <table border='0' width='100%'>
                    <tr class='encabezadoTabla'>
                        <td>Articulo</td>
                        <td>Descripci&oacute;n</td>
                        <td>Presentaci&oacute;n</td>
                        <td>Cantidad</td>
                    </tr>";

                // Si CONSULTA 2 tiene datos, se generó un traslado acompañado de articulo, sino se muestra el mensaje 'No hay detalle'
                if($num > 0)
                {
                    $i = 0;
                    // Este ciclo recorre el resultado de la CONSULTA 2 y genera el detalle de la entrega o del recibo del paciente
                    while ($row = mysql_fetch_array($res))
                    {
                        $wclass = (is_integer($i / 2)) ? "fila1": "fila2";
                        $i++;

                        buscar_articulo($row['Detart']);

                        $tabla_etq .= "
                        <tr class='".$wclass."'>
                            <td align=center>".$row['Detart']."</td>
                            <td align=center>".$wartnom."</td>
                            <td align=center>".$wunides."</td>
                            <td align=center>".$row['cantidad']."</td>
                        </tr>";
                    }
                    if ($estado_mov == 'off' && $num > 0)
                    {
                        $tabla_etq .= "
                        <tr class='".$wclass."'>
                            <td align=center colspan='4' style='font-weight:bold;'><font size='4'>ANULADO</font></td>
                        </tr>";
                    }
                    $tabla_etq .= "</table>";
                    $array_tablas_html[] = array('tabla'=>$tabla_etq,'accion'=>$accion);
                }
                else
                {
                    $tabla_etq .= "
                        <tr class='fila1'>
                            <td align=center colspan='4'>No hay detalle</td>
                        </tr>
                        </table>";
                    $array_tablas_html[] = array('tabla'=>$tabla_etq,'accion'=>$accion);
                }
            }
        }
    }
    else
    { $array_tablas_html[] = array('tabla'=>"<tr><td colspan='2' align='center'><br><b>NO HAY REGISTROS DE ENTREGA Y RECIBO</b><br></td></tr>",'accion'=>''); }
    return $array_tablas_html;
}
/*****************************************************************************************************************************************/

/**
 * Función verEntregasRecibos($wbasedato), se encarga de mostrar en web las entregas de un paciente y los recibos de un paciente con sus respectivos
 * detalles del traslado, esta misma función organiza las entregas de un paciente en una columna y los recibos del mismo paciente en otra columna.
 *
 * @param string $wbasedato :   Nombre de la empresa-tabla en la que se hace la búsqueda de los traslados.
 * @return                  :   Imprime en pantalla el html con el resultado del reporte.
 */
function verEntregasRecibos($wbasedato)
{
    global $conex;
    global $wmovhos;
    global $wemp_pmla;
    global $whis;
    global $wing;
    global $wartnom;
    global $wartuni;
    global $wunides;

    // $respuesta es un array que en cada posición tiene una tabla html con la descripción del movimiento y el tipo de movimiento, entrega o recibo.
    $respuesta = consulta_movimiento($wbasedato, $whis, $wing);
    // echo '<pre>';print_r($respuesta);echo '</pre>';

    $dato_paciente = buscar_paciente($whis);

    echo "
        <div align='center'>
        <table border='0px'>
            <tr>
                <td align='center' class='encabezadoTabla'><font size='4'>Nombre:</font></td>
                <td align='center' class='fila1'><font size='4'>".$dato_paciente['nombres_pac']."</font></td>
                <td align='center' class='encabezadoTabla'><font size='4'>Documento:</font></td>
                <td align='center' class='fila1'><font size='4'>".$dato_paciente['doc']."</font></td>
                <td align='center' class='encabezadoTabla'><font size='4'>Historia:</font></td>
                <td align='center' class='fila1'><font size='4'>".$whis."-".$wing."</font></td>
            </tr>
            <tr>
                <td colspan='6'>&nbsp;</td>
            </tr>
            <tr>
                <td colspan='6' align='center'><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td>
            </tr>
        </table>
        <table style='width: 900px;'>
            <tr class='encabezadoTabla'>
                <th align='center' style='width: 450px;'>ENTREGA</th>
                <th align='center' style='width: 450px;'>RECIBO</th>
            </tr>";

    // Organiza en una tabla principal del reporte, las tablas de los detalles de entrega y recibi
    $accion_tmp = '';
    foreach($respuesta as $key => $value)
    {
        if($accion_tmp == $value['accion'] && $value['accion'] == 'Entrega')
        {
            echo "  <td class='fila2'>&nbsp;</td>
                </tr>
                <tr><td colspan='2'><hr/></td></tr>";
        }

        $accion = $value['accion'];
        if ($accion == '')
        {
            echo $value['tabla'];
        }
        elseif ($accion == 'Entrega')
        {
            echo "
            <tr>
                <td>".$value['tabla']."</td>";
        }
        else
        {
            if ($key == 0)  // Si es la primera posición, es recibo y no existe entrega, se imprime la primer columna vacía.
            {
                echo "
                <tr>
                    <td class='fila2'>&nbsp;</td>";
            }
            echo "
                <td>".$value['tabla']."</td>
            </tr>";

            echo "<tr><td colspan='2'><hr/></td></tr>";
        }
        $accion_tmp = $value['accion'];
    }

    if($accion_tmp == 'Entrega')
    {
        echo "  <td class='fila2'>&nbsp;</td>
                </tr>
                <tr><td colspan='2'><hr/></td></tr>";
    }

    echo "
        </table>
        </div>
        ";
}
/*****************************************************************************************************************************************/

/**
 * Función buscar_paciente($whis, $wing=''), se encarga de buscar un paciente por medio de su história y retornar en un array
 * los datos de identificación el paciente (Documento, nombres completos).
 *
 * @param string $whis  : Número de historia del paciente.
 * @param string $wing  : Número de ingreso del paciente, este parámeto puede ser obviado al momento de llamar la función.
 * @return array $datos : Array que contiene los datos del paciente cosultado
 *                          $datos
 *                              (
 *                                  'nombres_pac'   => xxxxxx yyyyyy zzzzzzz
 *                                  'doc'           => 99999
 *                                  'whis'          => 99999
 *                                  'wing'          => 99
 *                              )
 */
function buscar_paciente($whis, $wing='')
{
    global $conex;
	global $wemp_pmla;
	
    // Consulta para listar los datos del paciente relacionado a la historia consultada.
    $ing = '';
    if ($wing != '')
    {
        $ing = "AND pacientes_id.Oriing = '".$whis."'";
    }

    $query_info = "
        SELECT  pacientes_id.Oriced, pacientes_id.Oriing
                , datos_paciente.Pacno1, datos_paciente.Pacno2
                , datos_paciente.Pacap1, datos_paciente.Pacap2
        FROM    root_000037 as pacientes_id, root_000036 as datos_paciente
        WHERE   pacientes_id.Orihis = '".$whis."'
                $ing
                AND pacientes_id.Oriced = datos_paciente.Pacced
				AND oriori = '".$wemp_pmla."'";

	$res_info = mysql_query($query_info, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query_info." - ".mysql_error());
    $row_datos = mysql_fetch_array($res_info);
    $nombres_pac = trim($row_datos['Pacno1'].' '.$row_datos['Pacno2'].' '.$row_datos['Pacap1'].' '.$row_datos['Pacap2']);
    $datos = array('nombres_pac'=>$nombres_pac,'doc'=>$row_datos['Oriced'],'whis'=>$whis,'wing'=>$wing);
    return $datos;
}
/*****************************************************************************************************************************************/

/**
 * Función mostrar_filtros(), se encarga de mostrar el filtro para digitar el número de historia a buscar
 * y tambien se encarga de mostar la lista de ingresos luego de encontrar la historia del paciente buscado.
 *
 * @return unknown
 */
function mostrar_filtros()
{
    global $conex;
    global $wmovhos;
    global $wemp_pmla;
    global $wusuario;
    global $wtabcco;
    global $wcco;
    global $wfechainicial;
    global $wfechafinal;
    global $whis;

    $wcco1 = explode("-", $wcco);

    echo "
    <div align='center'>
        <table class =fila1>
            <tr>
                <td align=''><font size=4>Ingrese la Historia</font></td>
                <td align=''>:<input type='text' size='10' name='whis'></td>
            </tr>
        </table>";

    echo "<table>";
    echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
    echo "</table>
    </div>";

    $num = 0;
    if (isset($whis) && $whis != '')
    {
        //Consulta para listar los ingresos del paciente
        $query =  " SELECT  ingresos.Ubihis, ingresos.Ubiing, ingresos.Fecha_data
                    FROM    ".$wmovhos."_000018 as ingresos
                    WHERE   ingresos.Ubihis = '".$whis."'
                    ORDER BY ingresos.Fecha_data DESC, ingresos.Ubiing DESC";
        $res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
        $num = mysql_num_rows($res);
    }

    if ($num > 0)
    {
        $datos_paciente = buscar_paciente($whis);

        echo "
            <div align='center'>
            <table style='width: 500px;'>
                <tr>
                    <td colspan='3' class='encabezadoTabla'><font size='4' >Nombre:</font></td>
                    <td colspan='3' class='fila1'><font size='4' >".ucwords(strtolower($datos_paciente['nombres_pac']))."</font></td>
                </tr>
                <tr>
                    <td colspan='3' class='encabezadoTabla'><font size='4' >Documento:</font></td>
                    <td colspan='3' class='fila1'><font size='4' >".$datos_paciente['doc']."</font></td>
                </tr>
            </table>
            <table style='width: 500px;'>
                <tr class='encabezadoTabla'>
                    <th><font size='3'>Fecha - Ingreso</font></th>
                    <th><font size='3'>Historia - Ingreso</font></th>
                    <th><font size='3'>&nbsp;</font></th>
                </tr>";

            $i=0;
            while ($row = mysql_fetch_array($res))
            {
                $wclass = (is_integer($i / 2)) ? "fila1" : "fila2";
                $i++;

                $whis = $row['Ubihis'];
                $wing = $row['Ubiing'];
                $wfechaing = $row['Fecha_data'];

                $wfechaing = $row['Fecha_data']; //Fecha del ingreso

                $path = "/matrix/movhos/reportes/rep_ent_y_rec_pac.php?wemp_pmla=".$wemp_pmla."&wing=".$wing."&whis=".$whis;
                echo "
                <tr class=".$wclass.">
                    <td align=center><b>".$wfechaing."</b></td>
                    <td align=center><b>". $whis."-" .$wing. "</b></td>
                    <td align=center><a href=# onclick=\"ejecutar('".$path."')\">Ver</a></td>
                </tr>
                </div>";
            }
    }
    else
        echo "<div align='center'><br><b>NO HAY REGISTROS</b></div><br>";
    //echo "</table>";
}
/*****************************************************************************************************************************************/


//===============================================================================================================
//                                              P R I N C I P A L
//===============================================================================================================
session_start();

if (!isset($_SESSION['user']))
{ echo "error"; }
else
{
    $pos        = strpos($user, "-");
    $wusuario   = substr($user, $pos + 1, strlen($user));

    include_once "movhos/movhos.inc.php";
    include_once "root/barcod.php";

    $wtabcco = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tabcco');
    $wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

    echo "<br>";
    echo "<br>";

    $q = " SELECT detapl, detval, empdes "
        ."   FROM root_000050, root_000051 "
        ."  WHERE empcod = '".$wemp_pmla."'"
        ."    AND empest = 'on' "
        ."    AND empcod = detemp ";
    $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
    $num = mysql_num_rows($res);

    if ($num > 0)
    {
        for ($i = 1;$i <= $num;$i++)
        {
            $row = mysql_fetch_array($res);

            if ($row[0] == "cenmez")
            $wcenmez = $row[1];

            if ($row[0] == "afinidad")
            $wafinidad = $row[1];

            if ($row[0] == "movhos")
            $wbasedato = $row[1];

            if ($row[0] == "tabcco")
            $wtabcco = $row[1];

            $winstitucion=$row[2];
        }
    }
    else
    {
        echo "NO EXISTE NINGUNA APLICACIÓN DEFINIDA PARA ESTA EMPRESA";
    }

    echo "<form name='rep_ent_rec' action='' method=post>";
    echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";

    encabezado("Reporte de Entrega y Recibo de Pacientes", $wactualiz, "clinica");

    if (isset($wing))
    {
        // Luego se leccionar un número de ingreso (Ver) se muestran todos los movimientos ocurridos en ese ingreso.
        verEntregasRecibos($wbasedato);
        echo "<br><br>";
    }
    else
    {
        // Llamado a la función que permite mostrar el filtro para la busqueda de paciente por #historia y mostar la lista de ingresos.
        mostrar_filtros();
        echo "<br>";
        echo "<table align=center>";
        echo "</table>";
    }

    echo "<table align=center>";
    echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
    echo "</table>";
}
?>