<?php
include_once("conex.php");
if(!isset($accion))
{
    echo '<!DOCTYPE html>';
}
/**
 PROGRAMA                   : rep_notasERP.php
 AUTOR                      : Edwar Jaramillo.
 FECHA CREACION             : 01 Diciembre de 2015

 DESCRIPCION: Reporte para consultar facturas y notas relacionadas con cargos generados desde matrix (ERP).


 Notas:
/*

//--------------------------------------------------------------------------------------------------------------------------------------------
//                  CAMBIOS PARA MIGRACION
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
	CODIGO	|	FECHA		|	AUTOR 	|	DESCRIPCION	
----------------------------------------------------------------------------------------------------------------------------------------------
	MIGRA_1	|	2019-02-27	|	Jerson	|	Se corrigen tildes

	
----------------------------------------------------------------------------------------------------------------------------------------------

 --
 */ $wactualiza = "(Diciembre 14 de 2015)"; /*
 ACTUALIZACIONES:
 * Diciembre 14 2015 Edwar Jaramillo:
    - Nueva variable "total_grabado_mx" para sumar el total de cargos grabador en matrix que son facturables, ese valor se muestre en un tootip sobre el valor facturado matrix.
    - La variable "total_facturado_mx" ahora tiene en cuenta que si un insumo es un producto entonces solamente se debe sumar el valor facturado en matrix solo una vez
        y no tantas veces como insumos compongan el producto.
    - Se creó una nueva columna para sumar todos los cargos que están en matrix pero que no aparecen como facturados (o facturables) en unix.
    - NOTA: Muchas veces los valores de facturado matrix y facturado unix no van a ser iguales porque puede que en unix se hayan cambiado insumos
        a si facturable, o insumos que estaban S Fact. se hayan cambiado a N Fact. incluso estos dos casos se pueden presentar en una misma liquidación.
 * Diciembre 10 2015 Edwar Jaramillo:
    - Mejor control de errores en las consultas a Unix, '@' para controlar el error y poder leer un vlaor booleano.
    - Se optimiza consultar para insumos, unificando ITDRODOC e IVDRODET, para evitar ejecutar consultas separadas, con base a esta consulta se genera un array
        con las líneas de los insumos y luego se consulta es el array y no una consulta directa nuevamente por cada línea a unix (solo se haría por cada documento).
    - Ahora la respuesta del array se divide en dos pasos, inicialmente consulta todos los cargos facturados, muestra el resultado en el reporte e inmediatamente
        se genera una nueva petición para consultar las cifras totales por cada mes (total facturado, notas totales, porcentaje total), si hay años para comparar
        entonces tambien se completa esa información.
    - Se cambian las consultas a unix para calcular las cifras totales por año mes y se elimina la colunma notas credito unix.
 * Diciembre 04 2015 Edwar Jaramillo:
    - Se genera el mismo proceso que en el reporte MedicamentosUnixMatrix para que en los insumos se valíde si el artículo corresponde a un producto
        o se reemplaza por otro al momento de grabar el cargo en unix, en ese nuevo proceso para el reporte se debe verificar por cada insumo si el número de
        línea para casa insumo tiene correspondencia en matrix y unix o si ha cambiado, si cambió entonces se debe buscar la línea a la que corresponde el
        código del insumo y ya con los datos correctos se busca el detalle del cargo en facardet.
 * Diciembre 03 2015 Edwar Jaramillo:
    - Nuevos filtros por centros de costos y años para comparación por cada mes actual generado en el reporte.

 *  Diciembre 01 de 2015 Edwar Jaramillo:
    - Fecha de la creación del reporte.
**/

$fecha_actual = date("Y-m-d");
$hora_actual  = date("H:i:s");

$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");






if(isset($accion) && !array_key_exists('user',$_SESSION))
{
    $msjss = "Recargue o inicie sesión nuevamente en la página principal de Matrix para que pueda seguir utilizando este programa normalmente.";
    $data = array('error'=>1,'mensaje'=>$msjss,'html'=>'');
    echo json_encode($data);
    return;
}
$user_session      = explode('-',$_SESSION['user']);
$user_session      = $user_session[1];
// $user_session_wemp = empresaEmpleado($wemp_pmla, $conex, $wbasedato, $user_session);

/*****  DICCIONARIO LOCAL *****/
// define('PROCEDIMIENTO'      ,'Procedimiento');

/**
 * [seguimiento description: Función para uso solo de desarrollo, en ambiente local, crea un archivo de texto donde se imprimen variables y arrays para su seguimiento]
 * @param  [type] $seguir [Cadena de texto a guardar en el archivo, para guardar array recordar usar print_r($al_array, true), puede usar saltos de línea PHP así PHP_EOL ]
 * @return [type]         [description]
 */
function seguimiento($seguir)
{
    $fp = fopen("rep_notasERP.txt","a+");
    fwrite($fp, "[".date("Y-m-d H:i:s")."]".PHP_EOL.$seguir);
    fclose($fp);
}

/**
 * [limpiarString: quita multiples espacios y espacios al final del string]
 * @param  [type] $string_ [description]
 * @return [type]          [description]
 */
function limpiarString($string_)
{
    return trim(preg_replace('/[ ]+/', ' ', $string_));
}

/**
 * [nombreDiaSemana description: Esta función recibe los número de año, més y día, y devuelve el nombre del día de la semana en la fecha indicada]
 * @param  [type] $ano [description]
 * @param  [type] $mes [description]
 * @param  [type] $dia [description]
 * @return [type]      [String, nombre del día de la semana]
 */
function nombreDiaSemana($ano,$mes,$dia)
{
    $nameDias[] = 'Domingo';
    $nameDias[] = 'Lunes';
    $nameDias[] = 'Martes';
    $nameDias[] = 'Miercoles';
    $nameDias[] = 'Jueves';
    $nameDias[] = 'Viernes';
    $nameDias[] = 'Sabado';
    // 0->domingo    | 6->sabado
    $dia= date("w",mktime(0, 0, 0, $mes, $dia, $ano));
    return $nameDias[$dia];
}

function inicializarHistoria($idx_historia_ing_rep, $fecha_rep, $row, &$arr_cargosReporte)
{
    if(!array_key_exists($fecha_rep, $arr_cargosReporte))
    {
        $arr_cargosReporte[$fecha_rep] = array();
    }

    // $idx_his_ing = $idx_historia_ing_rep.'_'.$ingreso_rep;
    if(!array_key_exists($idx_historia_ing_rep, $arr_cargosReporte[$fecha_rep]))
    {
        $nombres_paciente = str_replace("  "," ", trim($row["Tcarno1"].' '.$row["Tcarno2"].' '.$row["Tcarap1"].' '.$row["Tcarap2"]));
        $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep] = array("nombre_paciente"=>$nombres_paciente, "total_grabado_mx"=>0, "total_facturado_mx"=>0, "total_facturado_unx"=>0, "total_notas_credito"=>0,"detalle_cargos"=>array(), "facturas"=>array(), "fuentes_facturas_famovdet"=>array(), "notas_credito"=>array(), "consultas_sql"=>array(), "cargos_grabados"=>array(), "cargos_devueltos"=>array());
    }
}

function agregarDetalle($fecha_rep, $idx_historia_ing_rep, $row, &$arr_cargosReporte)
{
    // $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["detalle_cargos"][] = array(  "fuen_insumo"    => $row["fuen_insumo"], // fuente insumo
    //                                                                             "Tcardoi"        => $row["Tcardoi"],
    //                                                                             "Tcarlin"        => $row["linea_insumo"],
    //                                                                             "doc_insumo"     => "", // Documento insumo
    //                                                                             "reg_unix"       => $row["reg_unix"],
    //                                                                             "valor_matrix"   => $row["valor_matrix"],
    //                                                                             "cco"            => $row["cco"],
    //                                                                             "invent"         => $row["invent"], // Es de inventario
    //                                                                             "facturable_mx"  => $row["facturable_mx"],
    //                                                                             "facturable_unx" => $row["facturable_mx"],
    //                                                                             "facturado_unx"  => "");
}

function detalleCargosFacturadosNotasCredito($conex, $conexUnix, $wbasedato, $arr_parametros, $selectfacardetFacarfac, $row, &$data, &$arr_cargosReporte, &$arr_cargosHisFactNotas, &$arr_consultas_por_historia)
{
    foreach ($arr_parametros as $key => $value) {
        $$key = $value;
    }

    // $arr_consultas_por_historia[$idx_historia_ing_rep][] = $selectfacardetFacarfac;
    if($resfacar = @odbc_do($conexUnix, $selectfacardetFacarfac))
    {
        $registroOK = false;
        while(odbc_fetch_row($resfacar))
        {
            $registroOK = true;
            inicializarHistoria($idx_historia_ing_rep, $fecha_rep, $row, $arr_cargosReporte);

            $carfacfue      = odbc_result($resfacar,"carfacfue");
            $carfacdoc      = odbc_result($resfacar,"carfacdoc");
            $cardetcon      = odbc_result($resfacar,"cardetcon");
            $facturado_unix = odbc_result($resfacar,"fcf_facturado_unx");

            if(!array_key_exists($carfacfue, $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["facturas"]))
            {
               $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["facturas"][$carfacfue] = array();
            }
            $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["consultas_sql"][] = "[* 1111 *] selectfacardetFacarfac: ".$selectfacardetFacarfac;

            if(!array_key_exists($carfacdoc, $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["facturas"][$carfacfue]))
            {
               $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["facturas"][$carfacfue][$carfacdoc] = array();
            }


            // >>>>>> Controlar que solo se lea la nota credito una vez por cada historia-fuente-documento-concepto
            if(!array_key_exists($idx_historia_ing_rep,$arr_cargosHisFactNotas))
            {
               $arr_cargosHisFactNotas[$idx_historia_ing_rep] = array("facturas"=> array(),"total_valor_notas_historia"=>0);
            }
            if(!array_key_exists($carfacfue, $arr_cargosHisFactNotas[$idx_historia_ing_rep]["facturas"]))
            {
               $arr_cargosHisFactNotas[$idx_historia_ing_rep]["facturas"][$carfacfue] = array();
            }
            if(!array_key_exists($carfacdoc, $arr_cargosHisFactNotas[$idx_historia_ing_rep]["facturas"][$carfacfue]))
            {
               $arr_cargosHisFactNotas[$idx_historia_ing_rep]["facturas"][$carfacfue][$carfacdoc] = array();
            }
            // <<<<<<


            $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["total_facturado_unx"] += $facturado_unix*1;

            if(!array_key_exists($cardetcon, $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["facturas"][$carfacfue][$carfacdoc]))
            {
                $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["facturas"][$carfacfue][$carfacdoc][$cardetcon] = ($facturado_unix*1);

                // Si aún no se ha tenído en cuenta fuente-documento(factura) para la misma historia
                if(!array_key_exists($cardetcon, $arr_cargosHisFactNotas[$idx_historia_ing_rep]["facturas"][$carfacfue][$carfacdoc]))
                {
                    $arr_cargosHisFactNotas[$idx_historia_ing_rep]["facturas"][$carfacfue][$carfacdoc][$cardetcon] = ($facturado_unix*1);

                    $selectcacar = "SELECT  carfue, cardoc, movdetfue, movdetdoc, movdetcon, SUM(movdetval) AS total_factura_nota
                                    FROM    CACAR, FAMOVDET
                                    WHERE   carfue = '27'
                                            AND carfca = '{$carfacfue}'
                                            AND carfac = '{$carfacdoc}'
                                            AND caranu = '0'
                                            AND movdetfue = carfue
                                            AND movdetdoc = cardoc
                                            AND movdetcon = '{$cardetcon}'
                                            AND movdetcco IN ('{$wccos_rep}')
                                            AND movdetanu = '0'
                                    GROUP BY carfue, cardoc, movdetfue, movdetdoc, movdetcon";
                    // $arr_consultas_por_historia[$idx_historia_ing_rep][] = $selectcacar;
                    if($rescacar = @odbc_do($conexUnix, $selectcacar))
                    {
                        $sumatoria_notas_concepto = 0;
                        while(odbc_fetch_row($rescacar))
                        {
                            $carfue             = odbc_result($rescacar,"carfue");
                            $cardoc             = odbc_result($rescacar,"cardoc");
                            $movdetfue          = odbc_result($rescacar,"movdetfue");
                            $movdetdoc          = odbc_result($rescacar,"movdetdoc");
                            $movdetcon          = odbc_result($rescacar,"movdetcon");
                            $total_factura_nota = (odbc_result($rescacar,"total_factura_nota")*1);
                            $sumatoria_notas_concepto += $total_factura_nota;

                            if(!array_key_exists($carfue, $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["notas_credito"]))
                            {
                               $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["notas_credito"][$carfue] = array();
                            }

                            if(!array_key_exists($cardoc, $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["notas_credito"][$carfue]))
                            {
                                $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["notas_credito"][$carfue][$cardoc] = array();
                            }

                            if(!array_key_exists($movdetcon, $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["notas_credito"][$carfue][$cardoc]))
                            {
                                $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["notas_credito"][$carfue][$cardoc][$movdetcon] = 0;
                            }
                            $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["notas_credito"][$carfue][$cardoc][$movdetcon] += $total_factura_nota;
                            $arr_cargosHisFactNotas[$idx_historia_ing_rep]["total_valor_notas_historia"] += $total_factura_nota;
                        }

                        // El total de notas credito no puede superar lo facturado unix, entonces hacer que las notas solo tengan como valor el máximo facturado.
                        if($sumatoria_notas_concepto > $facturado_unix)
                        {
                            $sumatoria_notas_concepto = $facturado_unix;
                        }

                        $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["total_notas_credito"] += $sumatoria_notas_concepto*1;
                        $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["consultas_sql"][] = "[* 2222 *] selectcacar: ".$selectcacar;
                        // $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["facturas"][$carfacfue][$carfacdoc][$cardetcon] = $sumatoria_notas_concepto;
                    }
                    else
                    {
                        $data["error"] = 1;
                        $data["mensaje"] = "Problemas al generar el reporte";
                        $data["evidencia_error"][] = "selectcacar: ".$selectcacar.PHP_EOL.odbc_errormsg();
                    }
                }
            }
        }

        //agregar detalle del cargo
        if($registroOK)
        {
            // inicializarHistoria($idx_historia_ing_rep, $fecha_rep, $row, $arr_cargosReporte);
            // Si el cargo esta facturado en unix (FACARFAC) entonces se suma al total facturado de cargos en matrix.
            if($row["facturable_mx"] == 'S')
            {
                // Cuando los cargos se dividen en varios no se deben sumar más de una vez en el conteo de matrix, si hay devolución tambien se debe tener en cuenta solo una vez.
                if($row['Tcardoi'] != '') // Si es cargo de insumos
                {
                    if(!array_key_exists($row['Tcardoi'], $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["cargos_grabados"]))
                    {
                        $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["cargos_grabados"][$row['Tcardoi']] = array();
                    }

                    if(!array_key_exists($row['Tcardoi'], $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["cargos_devueltos"]))
                    {
                        $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["cargos_devueltos"][$row['Tcardoi']] = array();
                    }

                    if(!array_key_exists($row["linea_insumo"], $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["cargos_grabados"][$row['Tcardoi']]) && $row["Tcardev"] != 'on')
                    {
                        $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["cargos_grabados"][$row['Tcardoi']][$row["linea_insumo"]] = $row["linea_insumo"];
                        $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["total_grabado_mx"]                                       += $row["valor_matrix"]*1;
                        $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["total_facturado_mx"]                                     += $row["valor_matrix"]*1;
                    }
                    elseif(!array_key_exists($row["linea_insumo"], $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["cargos_devueltos"][$row['Tcardoi']]) && $row["Tcardev"] == 'on')
                    {
                        $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["cargos_devueltos"][$row['Tcardoi']][$row["linea_insumo"]] = $row["linea_insumo"];
                        $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["total_grabado_mx"]                                       += $row["valor_matrix"]*1;
                        $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["total_facturado_mx"]                                      += $row["valor_matrix"]*1;
                    }
                    /*else
                    {
                        if(!array_key_exists("106", $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]))
                        {
                            $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["106"] = array();
                        }
                        $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["106"][] = $row;
                    }*/
                }
                else
                {
                    // Si es un cargo de concepto normal
                    $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["total_grabado_mx"] += $row["valor_matrix"]*1;
                    $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["total_facturado_mx"] += $row["valor_matrix"]*1;
                }
                // if(!array_key_exists("106", $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]))
                // {
                //     $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["106"] = array();
                // }
                // $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["106"][] = $row;
                //$arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["total_facturado_mx"] += $row["valor_matrix"]*1;
            }
            agregarDetalle($fecha_rep, $idx_historia_ing_rep, $row, $arr_cargosReporte);
        }
        else
        {
            //Es lo facturable en matrix que no esta facturado en unix.
            if($row["facturable_mx"] == 'S')
            {
                inicializarHistoria($idx_historia_ing_rep, $fecha_rep, $row, $arr_cargosReporte);

                // Cuando los cargos se dividen en varios no se deben sumar más de una vez en el conteo de matrix, si hay devolución tambien se debe tener en cuenta solo una vez.
                if($row['Tcardoi'] != '') // Si es cargo de insumos
                {
                    if(!array_key_exists($row['Tcardoi'], $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["cargos_grabados"]))
                    {
                        $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["cargos_grabados"][$row['Tcardoi']] = array();
                    }

                    if(!array_key_exists($row['Tcardoi'], $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["cargos_devueltos"]))
                    {
                        $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["cargos_devueltos"][$row['Tcardoi']] = array();
                    }

                    if(!array_key_exists($row["linea_insumo"], $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["cargos_grabados"][$row['Tcardoi']]) && $row["Tcardev"] != 'on')
                    {
                        $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["cargos_grabados"][$row['Tcardoi']][$row["linea_insumo"]] = $row["linea_insumo"];

                        $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["total_mx_NOUNIX"]  += $row["valor_matrix"]*1;
                        $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["total_grabado_mx"] += $row["valor_matrix"]*1;
                        // $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["total_facturado_mx"] += $row["valor_matrix"]*1; // No sumar aquí porque se grabó en matrix y no se facturó en unix
                        // if(!array_key_exists("rows", $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]))
                        // {
                        //     $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["rows"] = array();
                        // }
                        // $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["rows"][] = $row;
                    }
                    elseif(!array_key_exists($row["linea_insumo"], $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["cargos_devueltos"][$row['Tcardoi']]) && $row["Tcardev"] == 'on')
                    {
                        $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["cargos_devueltos"][$row['Tcardoi']][$row["linea_insumo"]] = $row["linea_insumo"];
                        $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["total_mx_NOUNIX"]                                         += $row["valor_matrix"]*1;
                        $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["total_grabado_mx"]                                       += $row["valor_matrix"]*1;
                        // $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["total_facturado_mx"]                                      += $row["valor_matrix"]*1; // No sumar aquí porque se grabó en matrix y no se facturó en unix
                    }
                }
                else
                {
                    $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["total_mx_NOUNIX"]  += $row["valor_matrix"]*1;
                    $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["total_grabado_mx"] += $row["valor_matrix"]*1;
                    // $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["total_facturado_mx"] += $row["valor_matrix"]*1; // No sumar aquí porque se grabó en matrix y no se facturó en unix
                }
            }
        }
        // if(!array_key_exists("106", $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]))
        //         {
        //             $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["106"] = array();
        //         }
        //         $arr_cargosReporte[$fecha_rep][$idx_historia_ing_rep]["106"][] = $row;
    }
    else
    {
        $data["error"] = 1;
        $data["mensaje"] = "Problemas al generar el reporte";
        $data["evidencia_error"][] = "selectfacardetFacarfac: No se pudo ejecutar el query en unix: ".$selectfacardetFacarfac.PHP_EOL.odbc_errormsg();
    }

    // foreach ($arr_parametros as $key => $value) {
    //     $arr_parametros = $$key;
    // }
}

function historiaCargosPorFactura($conex, $conexUnix, $wbasedato, $arr_parametros, $selectfacardetFacarfac, $row, &$arr_cargosReporte)
{
    foreach ($arr_parametros as $key => $value) {
        $$key = $value;
    }

    $turno_cirugia = $row["Turtur"];

    if($resfacar = odbc_do($conexUnix, $selectfacardetFacarfac))
    {
        while(odbc_fetch_row($resfacar))
        {
            $carfacfue      = odbc_result($resfacar,"carfacfue");
            $carfacdoc      = odbc_result($resfacar,"carfacdoc");
            $carfacreg      = odbc_result($resfacar,"carfacreg");
            $facturado_unix = odbc_result($resfacar,"fcf_facturado_unx");

            if(!array_key_exists($idx_historia_ing_rep, $arr_cargosReporte))
            {
                $arr_cargosReporte[$idx_historia_ing_rep] = array("facturas"=>array());
            }

            $idx_fue_doc = $carfacfue.'_'.$carfacdoc;
            if(!array_key_exists($idx_fue_doc, $arr_cargosReporte[$idx_historia_ing_rep]["facturas"]))
            {
               $arr_cargosReporte[$idx_historia_ing_rep]["facturas"][$idx_fue_doc] = array();
            }

            // if(!array_key_exists($carfacdoc, $arr_cargosReporte[$idx_historia_ing_rep]["facturas"][$carfacfue]))
            // {
            //    $arr_cargosReporte[$idx_historia_ing_rep]["facturas"][$carfacfue][$carfacdoc] = array();
            // }

            if(!array_key_exists($turno_cirugia, $arr_cargosReporte[$idx_historia_ing_rep]["facturas"][$idx_fue_doc]))
            {
               $arr_cargosReporte[$idx_historia_ing_rep]["facturas"][$idx_fue_doc][$turno_cirugia] = array();
            }

            if(!array_key_exists($carfacreg, $arr_cargosReporte[$idx_historia_ing_rep]["facturas"][$idx_fue_doc][$turno_cirugia]))
            {
               $arr_cargosReporte[$idx_historia_ing_rep]["facturas"][$idx_fue_doc][$turno_cirugia][$carfacreg] = $carfacreg;
            }

            // $arr_cargosReporte[$idx_historia_ing_rep]["facturas"][$carfacfue][$carfacdoc][$turno_cirugia] = $carfacreg;


            /*if(!array_key_exists($idx_historia_ing_rep, $arr_cargosReporte))
            {
                $arr_cargosReporte[$idx_historia_ing_rep] = array();
            }

            if(!array_key_exists($turno_cirugia, $arr_cargosReporte[$idx_historia_ing_rep]))
            {
               $arr_cargosReporte[$idx_historia_ing_rep][$turno_cirugia] = array("facturas"=>array());
            }

            if(!array_key_exists($carfacfue, $arr_cargosReporte[$idx_historia_ing_rep][$turno_cirugia]["facturas"]))
            {
               $arr_cargosReporte[$idx_historia_ing_rep][$turno_cirugia]["facturas"][$carfacfue] = array();
            }

            if(!array_key_exists($carfacdoc, $arr_cargosReporte[$idx_historia_ing_rep][$turno_cirugia]["facturas"][$carfacfue]))
            {
               $arr_cargosReporte[$idx_historia_ing_rep][$turno_cirugia]["facturas"][$carfacfue][$carfacdoc] = array();
            }
            $arr_cargosReporte[$idx_historia_ing_rep][$turno_cirugia]["facturas"][$carfacfue][$carfacdoc][] = $carfacreg;*/
        }
    }
}

function queryCargoUnix($tipo_cargo, $arr_FiltroSql, $fuente_insumo, $drodocdoc, $Tcarlin, $historia_rep, $ingreso_rep, $reg_unix)
{
    $selectfacar = "";
    if($tipo_cargo == 'insumo') // else, es un concepto
    {
        $selectfacar = "SELECT  carfacfue, carfacdoc {$arr_FiltroSql['select']}, SUM(carfacval) AS fcf_facturado_unx
                        FROM    FACARDET, FACARFAC
                        WHERE   cardetfue = '{$fuente_insumo}'
                                AND cardetdoc = '{$drodocdoc}'
                                AND cardethis = '{$historia_rep}'
                                AND cardetnum = '{$ingreso_rep}'
                                AND cardetlin = '{$Tcarlin}'
                                AND cardetfac = 'S'
                                AND carfacreg = cardetreg
                                AND cardetanu = '0'
                                AND carfacanu = '0'
                        GROUP BY carfacfue, carfacdoc {$arr_FiltroSql['group']}";
    }
    else
    {
        $selectfacar = "SELECT  carfacfue, carfacdoc {$arr_FiltroSql['select']}, SUM(carfacval) AS fcf_facturado_unx
                        FROM    FACARDET, FACARFAC
                        WHERE   cardetreg = '{$reg_unix}'
                                AND cardethis = '{$historia_rep}'
                                AND cardetnum = '{$ingreso_rep}'
                                AND cardetfac = 'S'
                                AND carfacreg = cardetreg
                                AND cardetanu = '0'
                                AND carfacanu = '0'
                        GROUP BY carfacfue, carfacdoc {$arr_FiltroSql['group']}";
    }
    return $selectfacar;
}

function consultarCargoInsumoPorLinea($conex, $conexUnix, $wbasedato, $arr_parametros, $fuente_insumo, $drodocdoc, $linea, $codigo_insumo_mx, $arr_FiltroSql, $historia_rep, $ingreso_rep, $row, $arr_DocumentosFuentes, $Tcardoi, &$data, &$arr_cargosReporte, &$arr_cargosHisFactNotas, &$arr_consultas_por_historia)
{
    $historia_ing = $historia_rep.'_'.$ingreso_rep;
    // se selecciona el estado del registro en ivdrodet facturable o no facturable
    $selectiv1   = "SELECT  drodetfac, drodetart
                    FROM    IVDRODET
                    WHERE   drodetfue = '{$fuente_insumo}'
                            AND drodetdoc = '{$drodocdoc}'
                            AND drodetite = '{$linea}'";

    // if($resiv = odbc_exec($conexUnix, $selectiv1))

    // if(array_key_exists($fuente_insumo, $arr_DocumentosFuentes) && array_key_exists($Tcardoi, $arr_DocumentosFuentes[$fuente_insumo])
    //     && array_key_exists($drodocdoc, $arr_DocumentosFuentes[$fuente_insumo][$Tcardoi]))
    {
        // $drodetfac = $arr_DocumentosFuentes[$fuente_insumo][$drodocdoc][$line];// odbc_result($resiv,'drodetfac');
        // $drodetart = $arr_DocumentosFuentes[$fuente_insumo][$Tcardoi][$drodocdoc][$linea];// odbc_result($resiv,'drodetart');
        $drodetart = '';
        if(array_key_exists($fuente_insumo, $arr_DocumentosFuentes) && array_key_exists($Tcardoi, $arr_DocumentosFuentes[$fuente_insumo])
        && array_key_exists($drodocdoc, $arr_DocumentosFuentes[$fuente_insumo][$Tcardoi]) && array_key_exists($linea, $arr_DocumentosFuentes[$fuente_insumo][$Tcardoi][$drodocdoc]))
        {
            $drodetart = $arr_DocumentosFuentes[$fuente_insumo][$Tcardoi][$drodocdoc][$linea];
        }
        // $data["evidencia_error"][] = "DEBUG drodetart: ".$drodetart.' > DEBUG codigo_insumo_mx: '.$codigo_insumo_mx.' > '.PHP_EOL;
        /*
        Se agrega esta nueva validacion  , donde se ve si el articulo en Matrix corresponde al de Unix.

        Explicacion: En Matrix se graba un documento y linea  por cada articulo grabado, Esto mismo se hace en Unix  , Existe
        una tabla en Unix donde hay relacion del documento y linea Matrix con documento y linea Unix  generalmente son distintos los documentos
        pero el numero de linea coincide. Para estar seguros de que el articulo en Matrix corresponda al de Unix se  compara tambien el articulo
        si es el articulo se trabaja con la linea  de matrix porque se sabe que es la misma sino se busca en todo el documento unix la linea que corresponde
        a la linea en matrix

        Si sí corresponde   se consulta el estado de facturable o no en Facardet
        */

        if($drodetart == $codigo_insumo_mx)
        {
            $selectfacar = queryCargoUnix('insumo', $arr_FiltroSql, $fuente_insumo, $drodocdoc, $linea, $historia_rep, $ingreso_rep, '');

            detalleCargosFacturadosNotasCredito($conex, $conexUnix, $wbasedato, $arr_parametros, $selectfacar, $row, $data, $arr_cargosReporte, $arr_cargosHisFactNotas, $arr_consultas_por_historia);
            // foreach ($arr_parametros as $key => $value) {
            //     $$key = $value;
            // }
        }
        else
        {
            // Entra aquí si las líneas de matrix vs Unix no son las mismas
            // Hago una busqueda del articulo y documento y asi hallo la nueva linea

            $selecti2 = "   SELECT  drodetite
                            FROM    IVDRODET
                            WHERE   drodetfue = '{$fuente_insumo}'
                                    AND drodetdoc = '{$drodocdoc}'
                                    AND drodetart = '{$codigo_insumo_mx}'";
            // $data["evidencia_error"][] = "DEBUG selecti2: ".$selecti2.' > '.PHP_EOL;
            // $arr_consultas_por_historia[$historia_ing][] = $selecti2;
            if($resiv = odbc_exec($conexUnix, $selecti2))
            {
                $drodetlinea = odbc_result($resiv,'drodetite');
                $existeprocedimiento = true;
                if($drodetlinea != '')
                {
                    $selectfacar = queryCargoUnix('insumo', $arr_FiltroSql, $fuente_insumo, $drodocdoc, $drodetlinea, $historia_rep, $ingreso_rep, '');

                    detalleCargosFacturadosNotasCredito($conex, $conexUnix, $wbasedato, $arr_parametros, $selectfacar, $row, $data, $arr_cargosReporte, $arr_cargosHisFactNotas, $arr_consultas_por_historia);
                }
                else
                {
                    // $data["error"] = 1;
                    $data["mensaje"] = "Problemas al generar el reporte. No existe insumo en Unix";
                    $data["evidencia_error"][] = "Linea en blanco (unx $drodetart  mx $codigo_insumo_mx, Tcardoi: {$row['Tcardoi']}, Histo: {$row['Tcarhis']}, drodocdoc: $drodocdoc) selecti2: ".$selecti2.' > ';
                }
            }
            else
            {
                $data["error"] = 1;
                $data["mensaje"] = "Problemas al generar el reporte";
                $data["evidencia_error"][] = "selectiv2: ".$selectiv2.' > '.mysql_error();
            }
        }
        //$html3.= "<td>".odbc_result($resu,1)."-".$linea."</td>";
    }
    /*else
    {
        // $data["error"] = 1;
        $data["mensaje"] = "Problemas al generar el reporte";
        $data["evidencia_error"][] = "selectiv1: ".$selectiv1.PHP_EOL.' > codigo_insumo_mx: '.$codigo_insumo_mx;
    }*/
}

function consultaCargosUnix($conex, $conexUnix, $wbasedato, $wbasedato_movhos, $solo_facturas, $wccos_rep, $row, &$data, &$arr_cargosReporte, &$arr_cargosHisFactNotas, &$Tcardoi_ant, &$fuente_insumo, &$drodocdoc, &$arr_DocumentosFuentes, &$arr_consultas_por_historia)
{
    $Tcarlin      = $row['linea_insumo'];
    $fecha_rep    = $row['Turfec'];
    $historia_rep = $row['Tcarhis'];
    $ingreso_rep  = $row['Tcaring'];
    $reg_unix     = $row['reg_unix'];

    $arr_parametros                         = array();
    $arr_parametros["historia_rep"]         = $historia_rep;
    $arr_parametros["ingreso_rep"]          = $ingreso_rep;
    $arr_parametros["fecha_rep"]            = $fecha_rep;
    $arr_parametros["row"]                  = $row;
    // $arr_parametros["data"]                 = $data;
    $arr_parametros["idx_historia_ing_rep"] = $historia_rep.'_'.$ingreso_rep;
    $arr_parametros["wccos_rep"]            = $wccos_rep;
    $historia_ing = $historia_rep.'_'.$ingreso_rep;
    // if(!array_key_exists($historia_ing, $arr_consultas_por_historia))
    // {
    //     $arr_consultas_por_historia[$historia_ing] = array();
    // }

    $arr_FiltroSql = array("select"=>"","group"=>"");
    if($solo_facturas)
    {
        // $arr_FiltroSql["select"] = ", carfacreg";
        // $arr_FiltroSql["group"]  = ", carfacreg";
    }
    else
    {
        $arr_FiltroSql["select"] = ", cardetcon";
        $arr_FiltroSql["group"]  = ", cardetcon";
    }

    if($row['invent'] == 'on')
    {
        // Si es de inventario, Tcardoi y fuen_insumo son diferentes a un valor anterior entonces consulte nuevamente en ITDRODOC
        // el número de documento
        $sqlu = '';

        if(!array_key_exists($row['fuen_insumo'], $arr_DocumentosFuentes))
        {
            $arr_DocumentosFuentes[$row['fuen_insumo']] = array();
        }

        // if($row['Tcardoi'] != $Tcardoi_ant || $row['fuen_insumo'] != $fuente_insumo)
        if(!array_key_exists($row['Tcardoi'], $arr_DocumentosFuentes[$row['fuen_insumo']]))
        {
            // $t_ini = date("H:i:s");
            $Tcardoi_ant   = $row['Tcardoi'];
            $fuente_insumo = $row['fuen_insumo'];
            // $sqlu = "   SELECT  drodocdoc
            //             FROM    ITDRODOC
            //             WHERE   drodocfue  = '{$fuente_insumo}'
            //                     AND drodocnum  = '{$Tcardoi_ant}'";
            $sqlu = "   SELECT  drodocdoc, drodetart, drodetite
                        FROM    ITDRODOC, IVDRODET
                        WHERE   drodocfue  = '{$fuente_insumo}'
                                AND drodocnum  = '{$Tcardoi_ant}'
                                AND drodetfue = drodocfue
                                AND drodetdoc = drodocdoc";
            // $data["evidencia_error"][] = "DEBUG sqlu: ".$sqlu.' > '.PHP_EOL;
            // $arr_consultas_por_historia[$historia_ing][] = $sqlu;
            if($resu = @odbc_do($conexUnix, $sqlu))
            {
                while(odbc_fetch_row($resu))
                {
                    $drodocdoc     = odbc_result($resu,"drodocdoc");
                    $drodetite_lin = odbc_result($resu,"drodetite");
                    $drodetart     = odbc_result($resu,"drodetart");
                    if(!array_key_exists($Tcardoi_ant, $arr_DocumentosFuentes[$fuente_insumo]))
                    {
                        $arr_DocumentosFuentes[$fuente_insumo][$Tcardoi_ant] = array();
                    }

                    if(!array_key_exists($drodocdoc, $arr_DocumentosFuentes[$fuente_insumo][$Tcardoi_ant]))
                    {
                        $arr_DocumentosFuentes[$fuente_insumo][$Tcardoi_ant][$drodocdoc] = array();
                    }

                    if($drodetite_lin != '')
                    {
                        if(!array_key_exists($drodetite_lin, $arr_DocumentosFuentes[$fuente_insumo][$Tcardoi_ant][$drodocdoc]))
                        {
                            $arr_DocumentosFuentes[$fuente_insumo][$Tcardoi_ant][$drodocdoc][$drodetite_lin] = ""; //array("drodetart"=>$drodetart);
                        }
                        $arr_DocumentosFuentes[$fuente_insumo][$Tcardoi_ant][$drodocdoc][$drodetite_lin] = $drodetart;
                    }
                }
                // $arr_tiempo_exe[""];
            }
            else
            {
                $drodocdoc = '';
                $data["error"] = 1;
                $data["mensaje"] = "Problemas al generar el reporte";
                $data["evidencia_error"][] = "sqlu: No se pudo ejecutar el query en unix: ".$sqlu.' > '.PHP_EOL.odbc_errormsg();
            }
            // $sql_ivdrodet = PHP_EOL.PHP_EOL.$sqlu.PHP_EOL."SELECT  drodetfac, drodetart
            //                             FROM    IVDRODET
            //                             WHERE   drodetfue = '{$fuente_insumo}'
            //                                     AND drodetdoc = '{$drodocdoc}'
            //                                     AND drodetite = '{}'";
            // $data["evidencia_error"][] = "integrar: ".$sql_ivdrodet;
        }
        else
        {
            $arr_key_doc = array_keys($arr_DocumentosFuentes[$fuente_insumo][$row['Tcardoi']]);
            $drodocdoc = $arr_key_doc[0];
            // $data["evidencia_error"][] = "DEBUG drodocdoc: ".$drodocdoc.' > '.PHP_EOL;
            if(count($arr_key_doc) > 1)
            {
                $data["evidencia_error"][] = 'NO DEBERIA SER MAYOR A 1 > '.print_r($arr_key_doc,true);
            }
        }

        if($drodocdoc != '')
        {
            if($row['Logdoc'] != '')
            {
                $lineasNuevasOReemplazo = array();
                if($row['Logpro'] =='on')
                {
                    $querycenpro = "SELECT  Pdeins
                                    FROM    cenpro_000003
                                    WHERE   Pdepro ='{$row['Tcarprocod']}'";
                    // $data["evidencia_error"][] = "DEBUG querycenpro: ".$querycenpro.' > '.PHP_EOL;
                    // $arr_consultas_por_historia[$historia_ing][] = $querycenpro;
                    if($resquerycenpro =  mysql_query( $querycenpro, $conex  ))
                    {
                        $p = -1;
                        while($rowquerycenpro = mysql_fetch_array($resquerycenpro))
                        {
                            $p++;
                            $lineasNuevasOReemplazo[] = ($row['linea_insumo']*1) + $p;
                        }
                    }
                    else
                    {
                        $data["error"] = 1;
                        $data["mensaje"] = "Problemas al generar el reporte";
                        $data["evidencia_error"][] = "querycenpro: ".$querycenpro.' > '.mysql_error();
                    }
                }
                else
                {
                    $lineasNuevasOReemplazo[] = $row['linea_insumo'];
                }

                $nuevasLineas = implode("','", $lineasNuevasOReemplazo);
                $sqlval       = "   SELECT  Logdoc,Loglin,Logaor,Logare
                                    FROM    {$wbasedato_movhos}_000158
                                    WHERE   Logdoc = '{$row['Tcardoi']}'
                                            AND Loglin IN ('{$nuevasLineas}')
                                            AND Logaor = '{$row['Tcarprocod']}'";
                // $data["evidencia_error"][] = "DEBUG sqlval: ".$sqlval.' > '.PHP_EOL;
                // $arr_consultas_por_historia[$historia_ing][] = $sqlval;
                if($resval = mysql_query( $sqlval, $conex))
                {
                    while($rowval = mysql_fetch_array($resval))
                    {
                        consultarCargoInsumoPorLinea($conex, $conexUnix, $wbasedato, $arr_parametros, $fuente_insumo, $drodocdoc, $rowval['Loglin'], $rowval['Logare'], $arr_FiltroSql, $historia_rep, $ingreso_rep, $row, $arr_DocumentosFuentes, $row['Tcardoi'], $data, $arr_cargosReporte, $arr_cargosHisFactNotas, $arr_consultas_por_historia);
                    }
                }
                else
                {
                    $data["error"] = 1;
                    $data["mensaje"] = "Problemas al generar el reporte";
                    $data["evidencia_error"][] = "sqlval: ".$sqlval.' > '.mysql_error();
                }
            }
            else
            {
                consultarCargoInsumoPorLinea($conex, $conexUnix, $wbasedato, $arr_parametros, $fuente_insumo, $drodocdoc, $Tcarlin, $row['Tcarprocod'], $arr_FiltroSql, $historia_rep, $ingreso_rep, $row, $arr_DocumentosFuentes, $row['Tcardoi'], $data, $arr_cargosReporte, $arr_cargosHisFactNotas, $arr_consultas_por_historia);
            }
        }
        else
        {
            // $data["error"] = 1;
            $data["mensaje"] = "Problemas al generar el reporte";
            $data["evidencia_error"][] = "drodocdoc: es un valor vacío, no esta integrado, Tcardoi: {$row['Tcardoi']}, fuen_insumo: {$row['fuen_insumo']}, Tcarprocod: {$row['Tcarprocod']}";
            // $data["evidencia_error"][] = $sqlu;
        }
    }
    else
    {
        $selectfacar = queryCargoUnix('concepto', $arr_FiltroSql, '', '', '', $historia_rep, $ingreso_rep, $reg_unix);

        detalleCargosFacturadosNotasCredito($conex, $conexUnix, $wbasedato, $arr_parametros, $selectfacar, $row, $data, $arr_cargosReporte, $arr_cargosHisFactNotas, $arr_consultas_por_historia);
        // foreach ($arr_parametros as $key => $value) {
        //     $$key = $value;
        // }
    }
}


function consultarFacturadoNotasTotal($conex, $conexUnix, $anio, $mes, $wccos_rep, $temporal, &$arr_TotalFactYNotas, &$data)
{
    $total_facturadoMes     = 0;
    $total_notasMes         = 0;
    $aniomes = $anio.'_'.$mes;

    // $sqlUnxFactMes = "  SELECT  movdetfue,movdetmes, sum(movdetval) AS movdetval
    //                     FROM    famovdet
    //                     WHERE   movdetfue = '20'
    //                             AND movdetano = '{$anio}'
    //                             AND movdetmes = '{$mes}'
    //                             AND movdetcco IN ('{$wccos_rep}')
    //                             AND movdetanu = '0'
    //                     GROUP BY movdetfue,movdetmes
    //                     ORDER BY movdetfue,movdetmes";
    // if($resFacturadoMes = @odbc_exec($conexUnix, $sqlUnxFactMes))
    // {
    //     if(odbc_fetch_row($resFacturadoMes)) // Si la consulta responde varios registros, solo se toma el primero que es el de mayor prioridad.
    //     {
    //         if((odbc_result($resFacturadoMes, 'movdetval')*1) >= 0)
    //         {
    //             $total_facturadoMes = (odbc_result($resFacturadoMes, 'movdetval')*1);
    //         }
    //     }
    // }
    // else
    // {
    //     $data["evidencia_error"][] = "sqlUnxFactMes: ".$sqlUnxFactMes.PHP_EOL.odbc_errormsg();
    // }

    // Consultar lo facturado para cada año-mes actual TABLA TEMPORAL
    // $sqlUnxNotasMesTmp = "  SELECT  movfue,movdoc,movano,movmes
    //                         FROM    famov,famovdet
    //                         WHERE   movfue='20'
    //                                 AND movano='{$anio}'
    //                                 AND movmes between '01' and '12'
    //                                 AND movanu='0'
    //                                 AND movfue=movdetfue
    //                                 AND movdoc=movdetdoc
    //                                 AND movdetcco IN ('{$wccos_rep}')
    //                         GROUP BY movfue,movdoc,movano,movmes
    //                         ORDER BY movfue,movdoc,movano,movmes
    //                         INTO temp {$temporal}";

    $sqlUnxNotasMesTmp = "  SELECT movdetfue fue, movdetdoc doc, sum(movdetval) val_fact
                              FROM famovdet
                             WHERE movdetfue='20'
                               AND movdetano='{$anio}'
                               AND movdetmes='{$mes}'
                               AND movdetanu='0'
                               AND movdetcco IN ('{$wccos_rep}')
                             GROUP BY movdetfue,movdetdoc
                             ORDER BY movdetfue,movdetdoc
                            INTO temp {$temporal}";
    if($resNotasmesTmp = @odbc_exec($conexUnix, $sqlUnxNotasMesTmp))
    {
        //
    }
    else
    {
        $data["evidencia_error"][] = "sqlUnxNotasMesTmp: ".$sqlUnxNotasMesTmp.PHP_EOL.odbc_errormsg();
    }

    $sqlUnxFactMes = "  SELECT  sum(val_fact) AS val_fact
                        FROM    {$temporal}";
    if($resFacturadoMes = @odbc_exec($conexUnix, $sqlUnxFactMes))
    {
        if(odbc_fetch_row($resFacturadoMes)) // Si la consulta responde varios registros, solo se toma el primero que es el de mayor prioridad.
        {
            if((odbc_result($resFacturadoMes, 'val_fact')*1) >= 0)
            {
                $total_facturadoMes = (odbc_result($resFacturadoMes, 'val_fact')*1);
            }
        }
    }
    else
    {
        $data["evidencia_error"][] = "sqlUnxFactMes: ".$sqlUnxFactMes.PHP_EOL.odbc_errormsg();
    }

    // Consultar lo facturado para cada año-mes actual
    // $sqlUnxNotasMes = " SELECT  movano, movmes, SUM(movdetval) AS movdetval
    //                     FROM    {$temporal},cacar,famovdet
    //                     WHERE   movfue=carfca
    //                             AND movdoc=carfac
    //                             AND carfue='27'
    //                             AND caranu='0'
    //                             AND carfue=movdetfue
    //                             AND cardoc=movdetdoc
    //                             AND movdetcco in ('{$wccos_rep}')
    //                     GROUP BY movano,movmes
    //                     ORDER BY movano,movmes";
    $sqlUnxNotasMes = " SELECT SUM(movdetval) AS val
                         FROM cacar,{$temporal},famovdet
                        WHERE carfca=fue
                          AND carfac=doc
                          AND carfue='27'
                          AND caranu='0'
                          AND carfue=movdetfue
                          AND cardoc=movdetdoc
                          AND movdetcco in ('{$wccos_rep}')";
    if($resNotasmes = @odbc_exec($conexUnix, $sqlUnxNotasMes))
    {
        if(odbc_fetch_row($resNotasmes)) // Si la consulta responde varios registros, solo se toma el primero que es el de mayor prioridad.
        {
            if((odbc_result($resNotasmes, 'val')*1) >= 0)
            {
                $total_notasMes = (odbc_result($resNotasmes, 'val')*1);
            }
        }
    }
    else
    {
        $data["evidencia_error"][] = "sqlUnxNotasMes: ".$sqlUnxNotasMes.PHP_EOL.odbc_errormsg();
    }

    if(!array_key_exists($aniomes, $arr_TotalFactYNotas))
    {
        $arr_TotalFactYNotas[$aniomes] = array("total_facturadoMes"=> $total_facturadoMes, "total_notasMes"=>$total_notasMes);
    }

    // $guardar = "sqlUnxNotasMesTmp: ".print_r($sqlUnxNotasMesTmp,true).PHP_EOL;
    // $guardar .= "sqlUnxFactMes: ".print_r($sqlUnxFactMes,true).PHP_EOL;
    // $guardar .= "sqlUnxNotasMes: ".print_r($sqlUnxNotasMes,true).PHP_EOL;
    // seguimiento($guardar);

    // return $arr_TotalFactYNotas;
}

if(isset($accion) && isset($form))
{
    // if (file_exists("rep_notasERP.txt")) {
    //     unlink("rep_notasERP.txt");
    // }

    $data = array('error'=>0,'mensaje'=>'','html'=>'','sql'=>'');
    $no_exec_sub = 'No se ejecutó ningúna rutina interna del programa';

    switch($accion)
    {
        case 'insert':
            switch($form)
            {
                default :
                        $data['mensaje'] = $no_exec_sub;
                        $data['error'] = 1;
                break;
            }
            echo json_encode($data);
            break;

        case 'update' :
            switch($form)
            {
                default :
                        $data['mensaje'] = $no_exec_sub;
                        $data['error'] = 1;
                    break;
            }
            echo json_encode($data);
            break;

        case 'load' :
            switch($form)
            {
                case 'generar_reporte_notas':
                        if (file_exists("rep_notasERP.txt")) {
                            unlink("rep_notasERP.txt");
                        }
                        $wanios_comparables = (!isset($wanios_comparables)) ? array(): $wanios_comparables;
                        // print_r($wanios_comparables);return;
                        $data["evidencia_error"] = array();
                        $whistoria               = limpiarString($whistoria);
                        $wingreso                = limpiarString($wingreso);
                        $wdocumento              = limpiarString($wdocumento);
                        $filtros                 = "";
                        $and                     = "";
                        $calculando              = '<img class="" border="0" width="15" height="15" src="../../images/medical/ajax-loader2.gif" title="Cargando.." >';

                        // Array para crear los valores totales por mes actual y totales para cada año comparado.
                        $arr_totalFactPorAnioMesCcos = array();

                        $explCco = explode("_", $wccos_rep);
                        $wccos_rep = implode("','", $explCco);

                        if($whistoria != '')
                        {
                            $filtros .= $and." tcx11.Turhis = '".$whistoria."'";
                            $and = "AND";
                        }
                        if($wingreso != '')
                        {
                            $filtros .= $and." tcx11.Turnin = '".$wingreso."'";
                            $and = "AND";
                        }
                        if($wdocumento != '')
                        {
                            $filtros .= $and." tcx11.Turdoc = '".$wdocumento."'";
                            $and = "AND";
                        }

                        $arr_tiempo_exe         = array();// array("historias"=>array(), "total_proceso_ini_exe"=>"","total_proceso_fin_exe"=>"");
                        $arr_cargosReporte      = array();
                        $arr_consultas_por_historia = array();

                        $sql = "SELECT  tcx11.Turfec, tcx11.Turtur
                                        , c106.Tcarhis, c106.Tcaring, c106.Tcardoi, c106.Tcarlin AS linea_insumo, c106.Tcarfac AS facturable_mx, c106.Tcarvto AS valor_matrix, c106.Tcarser AS cco
                                        , c106.Tcarno1, c106.Tcarno2, c106.Tcarap1, c106.Tcarap2, c106.Tcarprocod, c106.Tcarconcod, c106.Tcardev
                                        , c107.Audrcu AS reg_unix
                                        , mh2.Fenfue AS fuen_insumo
                                        , t200.Gruinv AS invent, m158.Logdoc, m158.Logpro
                                FROM    {$wbasedato_tcx}_000011 AS tcx11
                                        INNER JOIN
                                        {$wbasedato}_000199 AS c199 ON  (c199.Enltur = tcx11.Turtur AND c199.Enlest = 'on')
                                        INNER JOIN
                                        {$wbasedato}_000198 AS c198 ON (c198.Liqhis = c199.Enlhis AND c198.Liqing = c199.Enling AND c198.Liqcaq = c199.Enlcaq AND c198.Liqpro = c199.Enlpro AND c198.Liqest = 'on')
                                        INNER JOIN
                                        {$wbasedato}_000106 AS c106 ON (c106.id = c198.Liqidc)
                                        INNER JOIN
                                        {$wbasedato}_000107 AS c107 ON (c107.Audreg = c106.id AND c107.Audacc = 'GRABO' AND c107.Audhis = c106.Tcarhis AND c107.Auding = c106.Tcaring)
                                        LEFT JOIN
                                        {$wbasedato_movhos}_000002 AS mh2 ON (mh2.Fennum = c106.Tcardoi)
                                        INNER JOIN
                                        {$wbasedato}_000200 AS t200 ON (c106.Tcarconcod = t200.Grucod)
                                        LEFT JOIN
                                        {$wbasedato_movhos}_000158 AS m158 ON (c106.Tcardoi = m158.Logdoc AND c106.Tcarlin = m158.Loglin)
                                WHERE   tcx11.Turfec BETWEEN '{$fecha_inicio}' AND '{$fecha_final}'
                                        {$and} {$filtros}
                                ORDER BY tcx11.Turfec, tcx11.Turtur, c199.Enlhis, c199.Enling, c199.Enltur, c106.Tcarconcod";
                        // echo "<pre>".print_r($sql,true)."</pre>";
                        $arr_consultas_por_historia["principal"] = $sql;
                        $data["sql"] = $sql;
                        if($result = mysql_query($sql, $conex))
                        {
                            $conexUnix = '';
                            if($conexUnix = @odbc_connect('facturacion','informix','sco'))
                            {
                                // $arr_turnos = array();
                                // $arr_html_turnos = array();
                                $Tcardoi_ant           = "";
                                $fuente_insumo         = "";
                                $drodocdoc             = "";
                                $arr_historiafacturasFueDocUnx = array();

                                $arr_result = array();
                                $arr_cargosHisFactNotas = array();
                                /*while ($row = mysql_fetch_array($result))
                                {
                                    $arr_result[] = $row;
                                    consultaCargosUnix($conex, $conexUnix, $wbasedato, true, $row, $data, $arr_historiafacturasFueDocUnx, $arr_cargosHisFactNotas, $Tcardoi_ant, $fuente_insumo, $drodocdoc);
                                }*/

                                $arr_excluirHistorias = array();//Historias que tienen más de una liquidación en una misma factura
                                // [historia][facturas] [fuente_documento:20_4270401[]] => [turnos:149706[]] [cargos:[]]
                                /*if(count($arr_historiafacturasFueDocUnx) > 0)
                                {
                                    foreach ($arr_historiafacturasFueDocUnx as $historia_ing => $arr_facturas_hist)
                                    {
                                        foreach ($arr_facturas_hist["facturas"] as $fuente_factura => $arr_turnos_factura)
                                        {
                                            if(count($arr_turnos_factura) > 1)
                                            {
                                                if(!array_key_exists($historia_ing, $arr_excluirHistorias))
                                                {
                                                    $arr_excluirHistorias[$historia_ing] = $historia_ing;
                                                }
                                                // $guardar = "Dif. Liq.: HIs: $historia_ing, Fue_fact: $fuente_factura".PHP_EOL;
                                                // seguimiento($guardar);
                                            }
                                        }
                                    }
                                }*/

                                $Tcardoi_ant           = "";
                                $fuente_insumo         = "";
                                $drodocdoc             = "";
                                $arr_cargosHisFactNotas = array();
                                $arr_DocumentosFuentes  = array();

                                while ($row = mysql_fetch_assoc($result))
                                // foreach ($arr_result as $key => $row)
                                {
                                    $historia_rep = $row['Tcarhis'];
                                    $ingreso_rep  = $row['Tcaring'];
                                    $idx_historia_ing_rep = $historia_rep.'_'.$ingreso_rep;
                                    if(!array_key_exists($idx_historia_ing_rep, $arr_excluirHistorias))
                                    {
                                        consultaCargosUnix($conex, $conexUnix, $wbasedato, $wbasedato_movhos, false, $wccos_rep, $row, $data, $arr_cargosReporte, $arr_cargosHisFactNotas, $Tcardoi_ant, $fuente_insumo, $drodocdoc, $arr_DocumentosFuentes, $arr_consultas_por_historia);
                                    }
                                }

                                $ocultar_tdRelleno = (count($wanios_comparables) == 0) ? true: false;
                                $colspan_relleno = (count($wanios_comparables) > 0) ? (count($wanios_comparables) * 3) : 1;

                                // $arr_cargosReporte=array();
                                if(count($arr_cargosReporte) > 0)
                                {
                                    $cont_trs = 0;
                                    $html_resp = "";
                                    $html_tr_historias = "";
                                    $arr_porMes = array();
                                    foreach ($arr_cargosReporte as $idx_fecha => $arr_historias)
                                    {
                                        $html_fila_his       = "";
                                        $total_grabado_mx    = 0;
                                        $total_facturado_mx  = 0;
                                        $total_facturado_unx = 0;
                                        $total_mx_NOUNIX     = 0;
                                        $total_notas_credito = 0;
                                        $idx_fecha_html      = str_replace("-", "_", $idx_fecha);
                                        $yy_mm               = explode("-", $idx_fecha);

                                        $anio_mes = $yy_mm[0].'_'.$yy_mm[1];

                                        if(!array_key_exists($anio_mes, $arr_porMes))
                                        {
                                            $arr_porMes[$anio_mes] = array("filas_dias"=>"", "total_hist_mes"=>0,"total_grab_mx_mes"=>0,"total_mx_mes"=>0,"total_unx_mes"=>0,"total_mx_NOUNIX_mes"=>0,"total_notas_mes"=>0);
                                        }

                                        foreach ($arr_historias as $idx_historia_ing => $arr_datos_his)
                                        {
                                            $css_tr = ($cont_trs % 2 == 0) ? 'fila1': 'fila2';

                                            $nombre_pac        = utf8_encode($arr_datos_his["nombre_paciente"]);
                                            $vlr_grabado_mx  = ($arr_datos_his["total_grabado_mx"]*1);
                                            $vlr_facturado_mx  = ($arr_datos_his["total_facturado_mx"]*1);
                                            $vlr_facturado_unx = ($arr_datos_his["total_facturado_unx"]*1);
                                            $vlr_mx_NOUNIX     = ($arr_datos_his["total_mx_NOUNIX"]*1);
                                            $vlr_notas_credito = ($arr_datos_his["total_notas_credito"]*1);

                                            $total_grabado_mx    += $vlr_grabado_mx;
                                            $total_facturado_mx  += $vlr_facturado_mx;
                                            $total_facturado_unx += $vlr_facturado_unx;
                                            $total_mx_NOUNIX     += $vlr_mx_NOUNIX;
                                            $total_notas_credito += $vlr_notas_credito;
                                            $idx_historia_ing    = str_replace("_", "-", $idx_historia_ing);

                                            $simbolo = "";
                                            $stleBg_vlr_nota = ($vlr_notas_credito > 0) ? "background-color:#ffb3b3;": "";
                                            if(($vlr_facturado_mx*1) > ($vlr_facturado_unx*1)) { $simbolo = '>>'; }
                                            elseif(($vlr_facturado_mx*1) < ($vlr_facturado_unx*1)) { $simbolo = '<<'; }
                                            elseif(($vlr_facturado_mx*1) == ($vlr_facturado_unx*1)) { $simbolo = '='; }

                                            $porcentaje_notamx = ($vlr_facturado_mx > 0) ? (($vlr_notas_credito/$vlr_facturado_mx)*100): 0;
                                            $porcentaje_notaux = ($vlr_facturado_unx > 0) ? (($vlr_notas_credito/$vlr_facturado_unx)*100): 0;

                                            $td_relleno = '';
                                            if(!$ocultar_tdRelleno)
                                            {
                                                $td_relleno = ' <td style="text-align:right;font-weight:bold;" class="comparable" colspan="'.$colspan_relleno.'">
                                                                    &nbsp;
                                                                </td>';
                                            }
                                            $html_fila_his .= ' <tr style="display:none;" class=" '.$css_tr.' historiasXFecha_'.$idx_fecha_html.' historiasXMesDia_'.$anio_mes.'" onmouseover="trOver(this);" onmouseout="trOut(this);" >
                                                                    <td>&nbsp;</td>
                                                                    <td>'.$idx_historia_ing.'</td>
                                                                    <td>'.$nombre_pac.'</td>
                                                                    <td style="text-align:right;font-weight:bold;" title="Total grabado Matrix: '.number_format($vlr_grabado_mx,2).'" class="tooltip">
                                                                        '.number_format($vlr_facturado_mx,2).'
                                                                    </td>
                                                                    <td style="text-align:center;font-weight:bold;">
                                                                        '.$simbolo.'
                                                                    </td>
                                                                    <td style="text-align:right;font-weight:bold;">
                                                                        '.number_format($vlr_facturado_unx,2).'
                                                                    </td>
                                                                    <td style="text-align:right;font-weight:bold;">
                                                                        '.number_format($vlr_mx_NOUNIX,2).'
                                                                    </td>
                                                                    <td style="text-align:right;font-weight:bold;'.$stleBg_vlr_nota.'">
                                                                        '.number_format($vlr_notas_credito,2).'
                                                                    </td>
                                                                    <td style="text-align:right;font-weight:bold;'.$stleBg_vlr_nota.'">
                                                                        '.number_format($porcentaje_notamx,2).'
                                                                    </td>
                                                                    <!-- <td style="text-align:right;font-weight:bold;'.$stleBg_vlr_nota.'">
                                                                        '.number_format($porcentaje_notaux,2).'
                                                                    </td> -->
                                                                    <td style="text-align:right;font-weight:bold;">
                                                                        &nbsp;
                                                                    </td>
                                                                    <td style="text-align:right;font-weight:bold;">
                                                                        &nbsp;
                                                                    </td>
                                                                    <td style="text-align:right;font-weight:bold;">
                                                                        &nbsp;
                                                                    </td>
                                                                    '.$td_relleno.'
                                                                </tr>';
                                            $cont_trs++;
                                        }

                                        $simbolo = "";
                                        $stleBg_vlr_nota = (($total_notas_credito*1) > 0) ? "background-color:#ea6666;color: white;": "";
                                        if(($total_facturado_mx*1) > ($total_facturado_unx*1)) { $simbolo = '>>'; }
                                        elseif(($total_facturado_mx*1) < ($total_facturado_unx*1)) { $simbolo = '<<'; }
                                        elseif(($total_facturado_mx*1) == ($total_facturado_unx*1)) { $simbolo = '='; }

                                        $porcentaje_notamx = ($total_facturado_mx > 0) ? (($total_notas_credito/$total_facturado_mx)*100): 0;
                                        $porcentaje_notaux = ($total_facturado_unx > 0) ? (($total_notas_credito/$total_facturado_unx)*100): 0;

                                        $td_relleno = '';
                                        if(!$ocultar_tdRelleno)
                                        {
                                            $td_relleno = ' <td style="text-align:right;font-weight:bold;" class="comparable" colspan="'.$colspan_relleno.'">
                                                                &nbsp;
                                                            </td>';
                                        }

                                        //'.$yy_mm[0].' '.$meses[(($yy_mm[1]*1)-1)].' '.$yy_mm[2].'
                                        $html_dias = ' <tr style="display:none;" class="classFecha historiasXMes_'.$anio_mes.'" onclick="verOcultarLista(\'historiasXFecha_'.$idx_fecha_html.'\',\'\');" onmouseover="trOver(this);" onmouseout="trOut(this);" >
                                                            <td style="text-align:right;font-weight:bold;">
                                                                Día: '.$yy_mm[2].'
                                                            </td>
                                                            <td style="text-align:right;">
                                                                Cant: '.count($arr_historias).'
                                                            </td>
                                                            <td>&nbsp;</td>
                                                            <td style="text-align:right;font-weight:bold;" title="Total grabado Matrix: '.number_format($total_grabado_mx,2).'" class="tooltip">
                                                                $'.number_format($total_facturado_mx,2).'
                                                            </td>
                                                            <td style="text-align:center;font-weight:bold;">
                                                                '.$simbolo.'
                                                            </td>
                                                            <td style="text-align:right;font-weight:bold;">
                                                                $'.number_format($total_facturado_unx,2).'
                                                            </td>
                                                            <td style="text-align:right;font-weight:bold;">
                                                                $'.number_format($total_mx_NOUNIX,2).'
                                                            </td>
                                                            <td style="text-align:right;font-weight:bold;'.$stleBg_vlr_nota.'">
                                                                $'.number_format($total_notas_credito,2).'
                                                            </td>
                                                            <td style="text-align:right;font-weight:bold;'.$stleBg_vlr_nota.'">
                                                                '.number_format($porcentaje_notamx,2).'%
                                                            </td>
                                                            <!-- <td style="text-align:right;font-weight:bold;'.$stleBg_vlr_nota.'">
                                                                '.number_format($porcentaje_notaux,2).'%
                                                            </td> -->
                                                            <td style="text-align:right;font-weight:bold;" class="">
                                                                &nbsp;
                                                            </td>
                                                            <td style="text-align:right;font-weight:bold;" class="">
                                                                &nbsp;
                                                            </td>
                                                            <td style="text-align:right;font-weight:bold;" class="">
                                                                &nbsp;
                                                            </td>
                                                            '.$td_relleno.'
                                                        </tr>'.$html_fila_his;

                                        $arr_porMes[$anio_mes]["filas_dias"]          .= $html_dias;
                                        $arr_porMes[$anio_mes]["total_hist_mes"]      += count($arr_historias);
                                        $arr_porMes[$anio_mes]["total_grab_mx_mes"]   += $total_grabado_mx;
                                        $arr_porMes[$anio_mes]["total_mx_mes"]        += $total_facturado_mx;
                                        $arr_porMes[$anio_mes]["total_unx_mes"]       += $total_facturado_unx;
                                        $arr_porMes[$anio_mes]["total_mx_NOUNIX_mes"] += $total_mx_NOUNIX;
                                        $arr_porMes[$anio_mes]["total_notas_mes"]     += $total_notas_credito;
                                    }

                                    $arr_anio             = date("Y");
                                    // $arr_anio_comparativo = (date("Y")-1);
                                    foreach ($arr_porMes as $anio_mes => $arr_dias)
                                    {
                                        $simbolo = "";
                                        $stleBg_vlr_nota = (($arr_dias["total_notas_mes"]*1) > 0) ? "background-color:#ea6666;color: white;": "";
                                        if(($arr_dias["total_mx_mes"]*1) > ($arr_dias["total_unx_mes"]*1)) { $simbolo = '>>'; }
                                        elseif(($arr_dias["total_mx_mes"]*1) < ($arr_dias["total_unx_mes"]*1)) { $simbolo = '<<'; }
                                        elseif(($arr_dias["total_mx_mes"]*1) == ($arr_dias["total_unx_mes"]*1)) { $simbolo = '='; }

                                        $total_grab_mx_mes = $arr_dias["total_grab_mx_mes"]*1;
                                        $total_mx_mes      = $arr_dias["total_mx_mes"]*1;
                                        $total_unx_mes     = $arr_dias["total_unx_mes"]*1;
                                        $total_notas_mes   = $arr_dias["total_notas_mes"]*1;
                                        $porcentaje_notamx = ($total_mx_mes > 0) ? (($total_notas_mes/$total_mx_mes)*100): 0;
                                        $porcentaje_notaux = ($total_unx_mes > 0) ? (($total_notas_mes/$total_unx_mes)*100): 0;

                                        $yy_mm                        = explode("_", $anio_mes);
                                        $arr_anio                     = $yy_mm[0];
                                        $arr_mes                      = $yy_mm[1];
                                        $total_facturadoMesActual     = 0;
                                        $total_notasMesActual         = 0;

                                        if(!array_key_exists($anio_mes, $arr_totalFactPorAnioMesCcos))
                                        {
                                            $arr_totalFactPorAnioMesCcos[$anio_mes] = array("tipo_aniomes"=>"consultado","nombre_temp"=>'tmpact'.$anio_mes, "centro_costos"=>$wccos_rep, "anio"=>$arr_anio, "mes"=>$arr_mes, "anios_mes_comparables"=>array());
                                        }

                                        // $arr_TotalFactYNotasAniosMesActual     = array();
                                        // $arr_TotalFactYNotasAniosMesAnteriores = array();

                                        // consultarFacturadoNotasTotal($conex, $conexUnix, $arr_anio, $arr_mes, $wccos_rep, 'tmpact'.$anio_mes, $arr_TotalFactYNotasAniosMesActual, $data);
                                        // >>>>>>>>>>>>>>>>>>>>>> FACTURADO Y NOTAS DEL MES-AÑO ACTUAL >>>>>>>>>>>>>>>>>>>>>>>

                                        $total_facturadoMesActual     = ""; //$arr_TotalFactYNotasAniosMesActual[$anio_mes]["total_facturadoMes"];
                                        $total_notasMesActual         = ""; //$arr_TotalFactYNotasAniosMesActual[$anio_mes]["total_notasMes"];
                                        $porcentajenotasMesAnioActual = ""; //($total_facturadoMesActual > 0) ? (($total_notasMesActual/$total_facturadoMesActual)*100): 0;
                                        $stleBg_vlr_notaMAct          = ""; //(($total_notasMesActual*1) > 0) ? "background-color:#ea6666;color: white;": "";
                                        $stleBg_vlr_notaPorcAct       = ""; //(($porcentajenotasMesAnioActual*1) > 0) ? "background-color:#ea6666;color: white;": "";
                                        // <<<<<<<<<<<<<<<<<<<<<<< FACTURADO Y NOTAS DEL MES-AÑO ACTUAL <<<<<<<<<<<<<<<<<<<<<<<

                                        // >>>>>>>>>>>>>>>>>>>>>> FACTURADO Y NOTAS DEL MES-AÑO COMPARATIVO >>>>>>>>>>>>>>>>>>>>>>>
                                        // Consultar lo facturado para cada año-mes actual

                                        $htmlTDsComparables = "";
                                        foreach ($wanios_comparables as $keyComp => $anioComp)
                                        {
                                            $aniomesComp = $anioComp.'_'.$arr_mes;
                                            if(!array_key_exists($aniomesComp, $arr_totalFactPorAnioMesCcos[$anio_mes]['anios_mes_comparables']))
                                            {
                                                $arr_totalFactPorAnioMesCcos[$anio_mes]['anios_mes_comparables'][$aniomesComp] = array("tipo_aniomes"=>"comparable","nombre_temp"=>'tmpcomp'.$aniomesComp, "centro_costos"=>$wccos_rep, "anio"=>$anioComp, "mes"=>$arr_mes, "anios_mes_comparables"=>array());
                                            }
                                            // consultarFacturadoNotasTotal($conex, $conexUnix, $anioComp, $arr_mes, $wccos_rep, 'tmpcomp'.$aniomesComp, $arr_TotalFactYNotasAniosMesAnteriores, $data);

                                            $total_facturadoMesCompar     = "";//$arr_TotalFactYNotasAniosMesAnteriores[$aniomesComp]["total_facturadoMes"];
                                            $total_notasMesActualCompar   = "";//$arr_TotalFactYNotasAniosMesAnteriores[$aniomesComp]["total_notasMes"];
                                            $porcentajenotasMesAnioCompar = "";//($total_facturadoMesCompar > 0) ? (($total_notasMesActualCompar/$total_facturadoMesCompar)*100): 0;
                                            $stleBg_vlr_notaCompar        = "";//(($total_notasMesActualCompar*1) > 0) ? "background-color:#ea6666;color: white;": "";
                                            $stleBg_vlr_notaPorCompar     = "";//(($porcentajenotasMesAnioCompar*1) > 0) ? "background-color:#ea6666;color: white;": "";

                                            $htmlTDsComparables .= '<td id="td_factUnixTot'.$aniomesComp.'comparable" style="text-align:right;font-weight:bold;" class="comparable tipo_td">
                                                                        <span id="spn_factUnixTot_'.$aniomesComp.'comparable">$'.$calculando.'</span>
                                                                    </td>
                                                                    <td id="td_notaUnixTot_'.$aniomesComp.'comparable" style="text-align:right;font-weight:bold;'.$stleBg_vlr_notaCompar.'" class="comparable tipo_td">
                                                                        <span id="spn_notaUnixTot_'.$aniomesComp.'comparable">$'.$calculando.'</span>
                                                                    </td>
                                                                    <td id="td_porcenUnixT_'.$aniomesComp.'comparable" style="text-align:right;font-weight:bold;'.$stleBg_vlr_notaPorCompar.'" class="comparable tipo_td">
                                                                        <span id="spn_porcenUnixTot_'.$aniomesComp.'comparable">'.$calculando.'%</span>
                                                                    </td>';
                                        }
                                        // <<<<<<<<<<<<<<<<<<<<<<< FACTURADO Y NOTAS DEL MES-AÑO COMPARATIVO <<<<<<<<<<<<<<<<<<<<<<<


                                        // $sqlUnxDropTmp = "DROP TABLE tmp";
                                        // $resNotasmes = odbc_exec($conexUnix, $sqlUnxDropTmp);

                                        $html_resp .= ' <tr class="classMes" onclick="verOcultarLista(\'historiasXMes_'.$anio_mes.'\',\''.$anio_mes.'\');" onmouseover="trOver(this);" onmouseout="trOut(this);" >
                                                            <td style="text-align:left;font-weight:bold;">
                                                                '.$meses[(($yy_mm[1]*1)-1)].' '.$yy_mm[0].'
                                                            </td>
                                                            <td style="text-align:right;">
                                                                Total: '.$arr_dias["total_hist_mes"].'
                                                            </td>
                                                            <td>&nbsp;</td>
                                                            <td style="text-align:right;font-weight:bold;" title="Total grabado Matrix: '.number_format($arr_dias["total_grab_mx_mes"],2).'" class="tooltip" >
                                                                Total: $'.number_format($arr_dias["total_mx_mes"],2).'
                                                            </td>
                                                            <td style="text-align:center;font-weight:bold;">
                                                                '.$simbolo.'
                                                            </td>
                                                            <td style="text-align:right;font-weight:bold;">
                                                                Total: $'.number_format($arr_dias["total_unx_mes"],2).'
                                                            </td>
                                                            <td style="text-align:right;font-weight:bold;">
                                                                Total: $'.number_format($arr_dias["total_mx_NOUNIX_mes"],2).'
                                                            </td>
                                                            <td style="text-align:right;font-weight:bold;'.$stleBg_vlr_nota.'">
                                                                Total: $'.number_format($arr_dias["total_notas_mes"],2).'
                                                            </td>
                                                            <td style="text-align:right;font-weight:bold;'.$stleBg_vlr_nota.'">
                                                                Total: '.number_format($porcentaje_notamx,2).'%
                                                            </td>
                                                            <!-- <td style="text-align:right;font-weight:bold;'.$stleBg_vlr_nota.'">
                                                                Total: '.number_format($porcentaje_notaux,2).'%
                                                            </td> -->
                                                            <td id="td_factUnixTot_'.$anio_mes.'consultado" style="text-align:right;font-weight:bold;" class="tipo_td">
                                                                <span id="spn_factUnixTot'.$anio_mes.'consultado">$'.$calculando.'
                                                            </td>
                                                            <td id="td_notaUnixTot_'.$anio_mes.'consultado" style="text-align:right;font-weight:bold;'.$stleBg_vlr_notaMAct.'" class="tipo_td">
                                                                <span id="spn_notaUnixTot'.$anio_mes.'consultado">$'.$calculando.'
                                                            </td>
                                                            <td id="td_porcenUnixTot_'.$anio_mes.'consultado" style="text-align:right;font-weight:bold;'.$stleBg_vlr_notaPorcAct.'" class="tipo_td">
                                                                <span id="spn_porcenUnixTot'.$anio_mes.'consultado">'.$calculando.'%
                                                            </td>
                                                            '.$htmlTDsComparables.'
                                                        </tr>'.$arr_porMes[$anio_mes]["filas_dias"];
                                    }

                                    $html_anios_comparar = "";
                                    $html_encabezados_aniosComp = "";
                                    foreach ($wanios_comparables as $keyComp => $anioComp)
                                    {
                                        $html_anios_comparar .= '<td colspan="3" style="text-align:center; background-color: #5b8ec1;color: white; font-weight:bold; font-size:12pt;" class="comparable">
                                                                    '.$anioComp.'
                                                                </td>';
                                        $html_encabezados_aniosComp .= '<td style="background-color:#5b8ec1;" class="comparable">Fact. total mes</td>
                                                                        <td style="background-color:#5b8ec1;" class="comparable">Notas total mes</td>
                                                                        <td style="background-color:#5b8ec1;" class="comparable">(%) Total mes</td>';
                                    }

                                    $html = '<table id="tabla_resultado_reporte" class="tabla_resultado_reporte" align="center" style="width:100%" >
                                                            <tr>
                                                                <td colspan="12" style="text-align:center; background-color: #6c9ff2;color: white; font-weight:bold; font-size:12pt;">
                                                                    '.$arr_anio.'
                                                                </td>
                                                                '.$html_anios_comparar.'
                                                            </tr>
                                                            <tr class="encabezadoTabla">
                                                                <td>Fecha</td>
                                                                <td>Historia-Ing</td>
                                                                <td>Nombres paciente</td>
                                                                <td>($) Facturado Matrix</td>
                                                                <td>Dif.</td>
                                                                <td>($) Facturado Unix</td>
                                                                <td>($) Grabado Mx, No Fact. Unix</td>
                                                                <td>($) Notas crédito Fact. Mx.</td>
                                                                <td>(%) N.C. Matrix</td>
                                                                <!-- <td>(%) N.C. Unix</td> -->
                                                                <td>Fact. total mes</td>
                                                                <td>Notas total mes</td>
                                                                <td>(%) Total mes</td>
                                                                '.$html_encabezados_aniosComp.'
                                                            </tr>
                                                            '.$html_resp.'
                                                        </table>';
                                    $data["html"] = $html;
                                }
                                else
                                {
                                    $data["html"] = '<table id="tabla_resultado_reporte_" align="center">
                                                        <tr class="encabezadoTabla">
                                                            <td>NO SE ENCONTRARON DATOS!</td>
                                                        </tr>
                                                    </table>';
                                }

                                odbc_close($conexUnix);
                                odbc_close_all();
                            }
                            else
                            {
                                $validacionUnixCorrecto = false;
                                $simul_respuesta_mensajes_err["error_conexion_unix"] = array(   "mensaje_err"   =>"No fue posible realizar conexion con Unix en este momento",
                                                                                                "arr_errores"   =>array("Puede intentar más tarde."),
                                                                                                "evidenciaError"=>"");
                            }
                        }
                        else
                        {
                            $data["error"]   = 1;
                            $data["mensaje"] = "No se pudo ejecutar la consulta para generar el reporte";
                            $data["sql"]     = mysql_errno()." - ".mysql_error()." >> ".print_r($sql,true);
                        }

                        // $guardar = "arr_excluirHistorias: ".print_r($arr_excluirHistorias,true).PHP_EOL;
                        // $guardar = "arr_cargosReporte: ".print_r($arr_cargosReporte,true).PHP_EOL;
                        // $guardar .= "arr_cargosHisFactNotas: ".print_r($arr_cargosHisFactNotas,true).PHP_EOL;
                        // $guardar = "$arr_DocumentosFuentes: ".print_r($arr_DocumentosFuentes,true).PHP_EOL;
                        // $guardar = "$arr_totalFactPorAnioMesCcos: ".print_r($arr_totalFactPorAnioMesCcos,true).PHP_EOL;
                        // $guardar .= "$arr_consultas_por_historia: ".print_r($arr_consultas_por_historia,true).PHP_EOL;
                        // seguimiento($guardar);
                        $data["arr_calcular_totales"] = base64_encode(serialize($arr_totalFactPorAnioMesCcos));
                    break;

                case 'generar_totales_anio_mes':
                        $arr_calcular_totales = unserialize(base64_decode($arr_calcular_totales));
                        // $guardar = "$arr_calcular_totales: ".print_r($arr_calcular_totales,true).PHP_EOL;
                        // seguimiento($guardar);

                        $arr_actualizarTotales = array();
                        $conexUnix = '';
                        if(count($arr_calcular_totales) > 0)
                        {
                            if($conexUnix = @odbc_connect('facturacion','informix','sco'))
                            {
                                foreach ($arr_calcular_totales as $anio_mes => $arr_infoConsultado)
                                {
                                    $yy_mm                        = explode("_", $anio_mes);
                                    $arr_anio                     = $yy_mm[0];
                                    $arr_mes                      = $yy_mm[1];
                                    $total_facturadoMesActual     = 0;
                                    $total_notasMesActual         = 0;
                                    $wccos_rep = $arr_infoConsultado["centro_costos"];

                                    $arr_TotalFactYNotasAniosMesActual     = array();
                                    $arr_TotalFactYNotasAniosMesAnteriores = array();

                                    consultarFacturadoNotasTotal($conex, $conexUnix, $arr_anio, $arr_mes, $wccos_rep, 'tmpact'.$anio_mes, $arr_TotalFactYNotasAniosMesActual, $data);
                                    // >>>>>>>>>>>>>>>>>>>>>> FACTURADO Y NOTAS DEL MES-AÑO ACTUAL >>>>>>>>>>>>>>>>>>>>>>>

                                    $total_facturadoMesActual = $arr_TotalFactYNotasAniosMesActual[$anio_mes]["total_facturadoMes"];
                                    $total_notasMesActual = $arr_TotalFactYNotasAniosMesActual[$anio_mes]["total_notasMes"];
                                    $porcentajenotasMesAnioActual  = ($total_facturadoMesActual > 0) ? (($total_notasMesActual/$total_facturadoMesActual)*100): 0;
                                    $stleBg_vlr_notaMAct = (($total_notasMesActual*1) > 0) ? "background-color:#ea6666;color: white;": "";
                                    $stleBg_vlr_notaPorcAct = (($porcentajenotasMesAnioActual*1) > 0) ? "background-color:#ea6666;color: white;": "";
                                    // <<<<<<<<<<<<<<<<<<<<<<< FACTURADO Y NOTAS DEL MES-AÑO ACTUAL <<<<<<<<<<<<<<<<<<<<<<<

                                    // >>>>>>>>>>>>>>>>>>>>>> FACTURADO Y NOTAS DEL MES-AÑO COMPARATIVO >>>>>>>>>>>>>>>>>>>>>>>

                                    $htmlTDsComparables = "";
                                    foreach ($arr_infoConsultado["anios_mes_comparables"] as $aniomesComp => $anioComp)
                                    {
                                        $yy_mm    = explode("_", $aniomesComp);
                                        $anioComp = $yy_mm[0];

                                        consultarFacturadoNotasTotal($conex, $conexUnix, $anioComp, $arr_mes, $wccos_rep, 'tmpcomp'.$aniomesComp, $arr_TotalFactYNotasAniosMesAnteriores, $data);

                                        $total_facturadoMesCompar     = $arr_TotalFactYNotasAniosMesAnteriores[$aniomesComp]["total_facturadoMes"];
                                        $total_notasMesActualCompar   = $arr_TotalFactYNotasAniosMesAnteriores[$aniomesComp]["total_notasMes"];
                                        $porcentajenotasMesAnioCompar = ($total_facturadoMesCompar > 0) ? (($total_notasMesActualCompar/$total_facturadoMesCompar)*100): 0;
                                        $stleBg_vlr_notaCompar        = (($total_notasMesActualCompar*1) > 0) ? "background-color:#ea6666;color: white;": "";
                                        $stleBg_vlr_notaPorCompar     = (($porcentajenotasMesAnioCompar*1) > 0) ? "background-color:#ea6666;color: white;": "";

                                        $arr_actualizarTotales['td_factUnixTot'.$aniomesComp.'comparable']     = "";
                                        $arr_actualizarTotales['td_notaUnixTot_'.$aniomesComp.'comparable']    = $stleBg_vlr_notaCompar;
                                        $arr_actualizarTotales['td_porcenUnixT_'.$aniomesComp.'comparable']  = $stleBg_vlr_notaPorCompar;
                                        $arr_actualizarTotales['spn_factUnixTot_'.$aniomesComp.'comparable']   = '$'.number_format($total_facturadoMesCompar,2);
                                        $arr_actualizarTotales['spn_notaUnixTot_'.$aniomesComp.'comparable']   = '$'.number_format($total_notasMesActualCompar,2);
                                        $arr_actualizarTotales['spn_porcenUnixTot_'.$aniomesComp.'comparable'] = number_format($porcentajenotasMesAnioCompar,2).'%';
                                    }
                                    // <<<<<<<<<<<<<<<<<<<<<<< FACTURADO Y NOTAS DEL MES-AÑO COMPARATIVO <<<<<<<<<<<<<<<<<<<<<<<

                                    // $sqlUnxDropTmp = "DROP TABLE tmp";
                                    // $resNotasmes = odbc_exec($conexUnix, $sqlUnxDropTmp);

                                    $arr_actualizarTotales['td_factUnixTot_'.$anio_mes.'consultado'] = "";
                                    $arr_actualizarTotales['td_notaUnixTot_'.$anio_mes.'consultado'] = $stleBg_vlr_notaMAct;
                                    $arr_actualizarTotales['td_porcenUnixTot_'.$anio_mes.'consultado'] = $stleBg_vlr_notaPorcAct;
                                    $arr_actualizarTotales['spn_factUnixTot'.$anio_mes.'consultado'] = '$'.number_format($total_facturadoMesActual,2);
                                    $arr_actualizarTotales['spn_notaUnixTot'.$anio_mes.'consultado'] = '$'.number_format($total_notasMesActual,2);
                                    $arr_actualizarTotales['spn_porcenUnixTot'.$anio_mes.'consultado'] = number_format($porcentajenotasMesAnioActual,2).'%';
                                }
                            }
                            else
                            {
                                $validacionUnixCorrecto = false;
                                $simul_respuesta_mensajes_err["error_conexion_unix"] = array(   "mensaje_err"   =>"No fue posible realizar conexion con Unix en este momento",
                                                                                                "arr_errores"   =>array("Puede intentar más tarde."),
                                                                                                "evidenciaError"=>"");
                            }
                        }
                        $data["arr_actualizarTotales"] = $arr_actualizarTotales;
                    break;
                default:
                        $data['mensaje'] = $no_exec_sub;
                        $data['error'] = 1;
                    break;
            }
            echo json_encode($data);
            break;

        case 'delete' :
            switch ($form)
            {
                /*case 'CODIGO_EJEMPLO':
                        $query = "  UPDATE  ".$wbasedato."_".OBSERVACIONES_ORDEN."
                                            SET Segest = 'off'
                                    WHERE   id = '".$id_observ."'";
                        if($result = mysql_query($query, $conex))
                        {

                        }
                        else
                        {
                            debug_log_inline('',"<span class=\"error\">ERROR</span> Error al borrar obsrvación de la orden: $worden Fuente: $wfuente <br>&raquo; ".$query."<br>&raquo;No. ".mysql_errno().'<br>&raquo;Err: '.mysql_error()."<br>");
                            $descripcion = "(".mysql_errno().') '.mysql_error()."|obs:'Error al borrar obsrvaci? de la orden: $worden Fuente: $wfuente";
                            // insertLog($conex, $wbasedato, $user_session, $accion, $form, 'error_sql', $descripcion, $wfuente.'-'.$worden, $query);
                            $data['mensaje'] = 'No se pudo eliminar la observaci?.';
                            $data['error'] = 1;
                        }
                        $data['debug_log'] = utf8_encode(debug_log_inline());
                    break;*/

                default:
                    $data['mensaje'] = 'No se ejecutó ningúna rutina interna del programa';
                    break;
            }
            echo json_encode($data);
            break;
        default : break;
    }
    return;
}

include_once("root/comun.php");
$wbasedato     = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
$wbasedato_tcx = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tcx');
$wbasedato_movhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

//Centros de costos
$sqlCco = " SELECT  Ccocod, Ccocir, Ccourg
            FROM    {$wbasedato_movhos}_000011
            WHERE   Ccoest = 'on'
                    AND (Ccocir='on' OR Ccourg ='on')";

$arr_ccos = array();
if($resultccos = mysql_query($sqlCco, $conex))
{
    while($rowccos = mysql_fetch_array($resultccos))
    {
        $tipo_cco = "";
        $nom_cco = "";
        if($rowccos['Ccocir'] == 'on')
        {
            $tipo_cco = "ccocir";
            $nom_cco = "Cirugía";
        }

        if($rowccos['Ccourg'] == 'on')
        {
            $tipo_cco = "ccourg";
            $nom_cco = "Urgencias";
        }

        if(!array_key_exists($tipo_cco, $arr_ccos))
        {
            $arr_ccos[$tipo_cco] = array("nombre"=>$nom_cco,"codigos"=>array());
        }

        $arr_ccos[$tipo_cco]["codigos"][] = $rowccos['Ccocod'];
    }
}

$anioAct          = date("Y");
$limite_aniosComp = 3;
$anios_select     = array();
$cont_anios       = 1;
while($limite_aniosComp > 0)
{
    $anios_select[] = $anioAct-$cont_anios;
    $limite_aniosComp--;
    $cont_anios++;
}

?>
<html lang="es-ES">
<head>
    <title>Rep. Notas Cr&eacute;dito ERP</title>
    <meta charset="utf-8">

    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <!-- Librer? para detectar compatibilidad HTML5 con varios navegadores -->
    <script src="../../../include/root/modernizr.custom.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
    <script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
    <script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>

    <link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
    <script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>

    <script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>
    <link type="text/css" href="../../../include/root/jquery.ui.timepicker.css" rel="stylesheet"/>

    <script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>

    <script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
    <link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />

    <script src="../../../include/root/toJson.js" type="text/javascript"></script>

    <script type="text/javascript">
        var regExDecimal = /(^[0]{1}\.{1}[0-9]+$)|(^\d+\.{1}[0-9]+$)|(^\d+$)|(^[0]$)/;

        // Inicializar primer acordeón
        $(function(){
            // $("#div_datos_basicos").attr("acordeon", "");
            // $("#div_datos_basicos").accordion({
            //      collapsible: true
            //     ,heightStyle: "content"
            //     //,active: -1
            // });

            // $('.numerico').on({
            //     keypress: function(e) {
            //         var r = soloNumeros(e);
            //         if(r==true)
            //         {
            //             var codeentr = (e.which) ? e.which : e.keyCode; /*if(codeentr == 13) { buscarDatosBasicos(); }*/
            //             return true;
            //         }
            //         return false;
            //     }
            // });
        });

        $(document).ready( function ()
        {
            reiniciarTooltip();

            $("#fecha_inicio_rep").datepicker({
                showOn: "button",
                buttonImage: "../../images/medical/root/calendar.gif",
                buttonImageOnly: true,
                maxDate:"+0D",
                onSelect: function (date) {
                    var dt1 = $('#fecha_inicio_rep').datepicker('getDate');
                    var dt2 = $('#fecha_final_rep').datepicker('getDate');
                    if (dt1 > dt2) {
                        $('#fecha_final_rep').datepicker('setDate', dt1);
                    }
                    $('#fecha_final_rep').datepicker('option', 'minDate', dt1);
                }
            });

            $("#fecha_final_rep").datepicker({
                showOn: "button",
                buttonImage: "../../images/medical/root/calendar.gif",
                buttonImageOnly: true,
                maxDate:"+0D",
                onClose: function () {
                    var dt1 = $('#fecha_inicio_rep').datepicker('getDate');
                    var dt2 = $('#fecha_final_rep').datepicker('getDate');
                    //check to prevent a user from entering a date below date of dt1
                    if (dt2 <= dt1) {
                        var minDate = $('#fecha_final_rep').datepicker('option', 'minDate');
                        $('#fecha_final_rep').datepicker('setDate', minDate);
                    }
                }
            });

            actualizarSearch();
        });

        function actualizarSearch()
        {
            $('input#id_search_consulta').quicksearch('.tabla_resultado_reporte .find');
        }

        function generarReporte()
        {
            $("#btn_generar_rep").attr("disabled","disabled");
            $("#gif_carga").html('&nbsp;<img class="" border="0" width="15" height="15" src="../../images/medical/ajax-loader2.gif" title="Cargando.." >');

            var continuar = false;
            $(".datoreq").each(function(){
                var dato_val = $(this).val();
                if(dato_val.replace(/ /gi, "") != '')
                {
                    continuar = true;
                }
            });

            var msj_fechas = "";
            if($("#fecha_inicio_rep").val().replace(/ /gi, "") == '' || $("#fecha_final_rep").val().replace(/ /gi, "") == '')
            {
                msj_fechas = "\nLos campos de fechas no deben estar vacíos";
                continuar = false;
            }

            if(continuar)
            {
                var wanios_comparables = ($("#wanios_comparables").val() == null || $("#wanios_comparables").val()=='') ? new Array() : $("#wanios_comparables").val();
                var obJson                   = parametrosComunes();
                obJson['accion']             = 'load';
                obJson['form']               = 'generar_reporte_notas';
                obJson['fecha_inicio']       = $("#fecha_inicio_rep").val();
                obJson['fecha_final']        = $("#fecha_final_rep").val();
                obJson['whistoria']          = $("#whistoria_rep").val();
                obJson['wingreso']           = $("#ingreso_rep").val();
                obJson['wdocumento']         = ''; //$("#wdocumento_rep").val();
                obJson['wbasedato_movhos']   = $("#wbasedato_movhos").val();
                obJson['wanios_comparables'] = wanios_comparables;
                obJson['wccos_rep']          = $("#wccos_rep").val();
                $.post("rep_notasERP.php",
                    obJson,
                    function(data){
                        if(isset(data.error) && data.error == 1)
                        {
                            jAlert(data.mensaje, "Mensaje");
                            $("#btn_generar_rep").removeAttr("disabled");
                            $("#gif_carga").html("");
                        }
                        else
                        {
                            //console.log("Se modificaron las posiciones de las cirugías ")
                            $("#td_contenedor_rep").html(data.html);
                        }
                        return data;
                    },
                    "json"
                ).done(function(data){

                    if(data.error != 1)
                    {
                        generarTotalesPorAnioMes(data.arr_calcular_totales);
                    }

                    // actualizarSearch();
                    // $("#btn_generar_rep").removeAttr("disabled");
                    // $("#gif_carga").html("");
                });
            }
            else
            {
                var alerta = "Debe ingresar datos en los filtros del reporte para generarlo."+msj_fechas;
                jAlert(alerta, "Mensaje");
            }
        }

        function generarTotalesPorAnioMes(arr_calcular_totales)
        {
            // var arrJson = $.toJSON(arr_insumos);
            var obJson                     = parametrosComunes();
            obJson['accion']               = 'load';
            obJson['form']                 = 'generar_totales_anio_mes';
            obJson['wbasedato_movhos']     = $("#wbasedato_movhos").val();
            obJson['arr_calcular_totales'] = arr_calcular_totales;//;$.toJSON(arr_calcular_totales);
            $.post("rep_notasERP.php",
                obJson,
                function(data){
                    if(isset(data.error) && data.error == 1)
                    {
                        jAlert(data.mensaje, "Mensaje");
                        $("#btn_generar_rep").removeAttr("disabled");
                        $("#gif_carga").html("");
                    }
                    else
                    {
                        //console.log("Se modificaron las posiciones de las cirugías ")
                        // $("#td_contenedor_rep").html(data.html);
                        var actualizarElementos = data.arr_actualizarTotales;
                        for (var id_elem in actualizarElementos)
                        {
                            var elem = $("#"+id_elem);
                            var valor = actualizarElementos[id_elem];
                            if(elem.hasClass("tipo_td"))
                            {
                                var style = elem.attr("style");
                                elem.attr("style",style+valor);
                            }
                            else
                            {
                                elem.html(valor);
                            }
                            // index++;
                            // if(arr_opciones_seleccion == 'arr_terceros_especialidad')
                            // {
                            //     arr_datos[index]                = {};
                            //     arr_datos[index].value          = CodVal+'-'+datos[CodVal]['nombre'];
                            //     arr_datos[index].label          = CodVal+'-'+datos[CodVal]['nombre'];
                            //     arr_datos[index].codigo         = CodVal;
                            //     arr_datos[index].nombre         = CodVal+'-'+datos[CodVal]['nombre'];
                            //     arr_datos[index].especialidades = datos[CodVal]['especialidad'];
                            // }
                            // else
                            // {
                            //     arr_datos[index] = {};
                            //     arr_datos[index].value  = CodVal+'-'+datos[CodVal];
                            //     arr_datos[index].label  = CodVal+'-'+datos[CodVal];
                            //     arr_datos[index].codigo = CodVal;
                            //     arr_datos[index].nombre = CodVal+'-'+datos[CodVal];
                            // }
                        }
                    }
                    return data;
                },
                "json"
            ).done(function(data){
                actualizarSearch();
                $("#btn_generar_rep").removeAttr("disabled");
                $("#gif_carga").html("");
            });
        }

        /**
         * Se encarga de recorrer los id de la respuesta y setear los valores en cada uno de los campos o input html.
         *
         * @return unknown
         */
        var arregloDependientes = new Array(); // arreglo de selects que son dependientes de otros selects.
        function setearCamposHtml(arr)
        {
            var ejecutarDepend = new Array();
            $.each(arr, function(index, value) {
                if ($("#"+index).length > 0)
                {
                    // if(index == 'wfracciones59_defvia_edit') { alert(index+'-'+value+'|'+$("#"+index).attr('multiple')); }

                    if($("#"+index).is("input,select") && $("#"+index).attr("type") != 'checkbox' && $("#"+index).attr('multiple') == undefined) // Si es input o select entonces escribe en un campo u opción de un select sino escribe en html.
                    {
                        $("#"+index).val(value);
                        //Si es un select y adicionalmente tiene el evento onchange entonces debe ejecutar el evento para que el select dependiente se cargue con las opciones válidas.
                        if($("#"+index).is("select") && $("#"+index).attr('onchange'))
                        { ejecutarDepend.push( index );  }// "i"=antioquia
                        if($("#"+index).is('.dependiente')) //Municipios
                        { arregloDependientes[index] = value;}//Arreglo en la posicion "wmuni"=medellin
                    }
                    else if($("#"+index).attr("type") == 'checkbox')
                    {
                        if(value == 'on') { $("#"+index).attr("checked","checked"); }
                        else if(value == 'off') { $("#"+index).removeAttr("checked"); }
                    }
                    else if($("#"+index).attr('multiple') != undefined)
                    {
                        var opciones = value.split(",");

                        $("#"+index+" option").each(function(){
                                //alert(jQuery.inArray($(this).val(), opciones));
                                //if(opciones.indexOf($(this).val()) != -1) { $(this).attr("selected","selected"); } // No funciona en IE  >:(
                                if((jQuery.inArray($(this).val(), opciones)) != -1) { $(this).attr("selected","selected"); }
                        });
                    }
                    else
                    { $("#"+index).html(value); }
                }
            });
            for (var i = 0, elemento; elemento = ejecutarDepend[i]; i++) {
                $("#"+elemento).trigger("change");
            }
        }

        function parametrosComunes()
        {
            var obJson              = {};
            obJson['wemp_pmla']     = $("#wemp_pmla").val();
            obJson['wbasedato']     = $("#wbasedato").val();
            obJson['wbasedato_tcx'] = $("#wbasedato_tcx").val();
            return obJson;
        }

        function reiniciarTooltip()
        {
            $('.tooltip').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
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



    </script>

    <script type="text/javascript">

        function verOcultarLista(id_elem, anio_mes)
        {
            if($("#"+id_elem).length > 0)
            {
                if($("#"+id_elem).is(":visible"))
                {
                    $("#"+id_elem).hide(0);
                }
                else
                {
                    $("#"+id_elem).show(0);
                }
            }
            else if($("."+id_elem).length > 0)
            {
                if($("."+id_elem).is(":visible"))
                {
                    $("."+id_elem).hide(0);
                }
                else
                {
                    $("."+id_elem).show(0);
                }

                if(anio_mes != '')
                {
                    $(".historiasXMesDia_"+anio_mes).hide();
                }
            }
        }

        function simularPlaceHolder()
        {
            // P?ina con etiquetas de html5 de las que se podr? verificar su compatibilidad
            // https://github.com/Modernizr/Modernizr/wiki/HTML5-Cross-browser-Polyfills
            // http://geeks.ms/blogs/gperez/archive/2012/01/10/modernizr-ejemplo-pr-225-ctico-1-utilizando-placeholder.aspx
            // http://www.hagenburger.net/BLOG/HTML5-Input-Placeholder-Fix-With-jQuery.html
            if(!Modernizr.input.placeholder)
            {
                console.log("NAVEGADOR NO COMPATIBLE CON placeholder de HTML5, Se sim?la atributo placeholder.");
                $('[placeholder]').focus(function() {
                    var input = $(this);
                    if (input.val() == input.attr('placeholder')) {
                       input.val('');
                       input.removeClass('placeholder');
                    }
                }).blur(function() {
                    var input = $(this);
                    if (input.val() == '' || input.val() == input.attr('placeholder')) {
                        input.addClass('placeholder');
                        input.val(input.attr('placeholder'));
                    }
                }).blur();
                $('[placeholder]').parents('form').submit(function() {
                    $(this).find('[placeholder]').each(function() {
                    var input = $(this);
                    if (input.val() == input.attr('placeholder')) {
                       input.val('');
                    }
                  })
                });
            }
        }

        function aplicarAcordeon(id_div)
        {
            $("#"+id_div).accordion({
                collapsible: true
                ,autoHeight: false
                // ,clearStyle: true
                // ,heightStyle: "content"
                // ,active: -1
            });
        }

        function isset ( strVariableName ) {
            try {
                eval( strVariableName );
            } catch( err ) {
                if ( err instanceof ReferenceError )
                   return false;
            }
            return true;
        }

        //Function to convert hex format to a rgb color
        function rgb2hex(rgb){
         rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
         return "#" +
          ("0" + parseInt(rgb[1],10).toString(16)).slice(-2) +
          ("0" + parseInt(rgb[2],10).toString(16)).slice(-2) +
          ("0" + parseInt(rgb[3],10).toString(16)).slice(-2);
        }

        function cambioImagen(img1, img2)
        {
            $('#'+img1).hide(1000);
            $('#'+img2).show(1000);
        }

        function ocultarElemnto(elemento){
            $("#"+elemento).hide(1000);
        }

        function trOver(grupo)
        {
            // $("#"+grupo.id).addClass('classOver');
            $(grupo).addClass('classOver');
        }

        function trOut(grupo)
        {
            // $("#"+grupo.id).removeClass('classOver');
            $(grupo).removeClass('classOver');
        }

        function cerrarVentanaPpal()
        {
            window.close();
        }

    </script>

    <style type="text/css">
        .placeholder
        {
          color: #aaa;
        }

        /* CORRECCION DE BUG PARA EL DATEPICKER Y CONFIGURACION DEL TAMA?  */
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

        .ui-autocomplete{
            max-width:  230px;
            max-height: 150px;
            overflow-y: auto;
            overflow-x: hidden;
            font-size:  9pt;
        }

        .fixed-dialog{
             position: fixed;
             top: 100px;
             left: 100px;
        }

        .titulopagina2
        {
            border-bottom-width: 1px;
            /*border-color: <?=$bordemenu?>;*/
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

        .classFecha{
            background-color:#e5e5e5;
            cursor:pointer;
        }

        .classMes{
            background-color:#d6d6d6;
            cursor:pointer;
        }

    </style>
</head>
<body>
<?php
    encabezado("<div class='titulopagina2'>Reporte cargos cirug&iacute;a con Notas Cr&eacute;dito - facturaci&oacute;n inteligente</div>", $wactualiza, "clinica");
?>
<!-- <div style="color:red; font-weight:bold; text-align:center;font-size:14pt;"><img border="0" src="../../images/medical/root/CONSTRUC.GIF"width="30" height="30">[SE ESTÁ DESARROLLANDO ACTUALMENTE]<img border="0" src="../../images/medical/root/CONSTRUC.GIF"width="30" height="30"></div> -->
<!-- <div id="actualiza" class="version" style="text-align:right;" >Subversi&oacute;n: <?=$wactualiza?></div> -->
<input type='hidden' name='wbasedato_tcx' id='wbasedato_tcx' value="<?=$wbasedato_tcx?>">
<input type='hidden' name='wbasedato_movhos' id='wbasedato_movhos' value="<?=$wbasedato_movhos?>">
<input type='hidden' name='wbasedato' id='wbasedato' value="<?=$wbasedato?>">
<input type='hidden' name='wemp_pmla' id='wemp_pmla' value="<?=$wemp_pmla?>">

<table align="center" style="width:95%;">
    <tr>
        <td style="text-align:left;">
            <div id="contenedor_programa_reporte" align="left">
                <div id="div_filtros" style="width:100%;" align="center">
                    <table id="tabla_filtros" align="center" >
                        <tr>
                            <td colspan="4" class="encabezadoTabla" style="text-align:center;">Filtros del reporte</td>
                        </tr>
                        <tr class="tooltip" title="Fechas de los turnos de cirugías">
							<!--MIGRA_1-->
                            <td class="encabezadoTabla">Fecha inicio (cirug&iacute;a)</td>
                            <td class="fila2"><input type="text" class="datoreq" id="fecha_inicio_rep" name="fecha_inicio_rep" value="<?=date("Y-m-d")?>" size="8" disabled="disabled"></td>
                            <td class="encabezadoTabla">Fecha fin (cirug&iacute;a)</td>
                            <td class="fila2"><input type="text" class="datoreq" id="fecha_final_rep" name="fecha_final_rep" value="<?=date("Y-m-d")?>" size="8" disabled="disabled"></td>
                        </tr>
                        <tr>
                            <td class="encabezadoTabla">Historia</td>
                            <td class="fila2"><input type="text" class="datoreq" id="whistoria_rep" name="whistoria_rep" value="" size="8" ></td>
                            <td class="encabezadoTabla">Ingreso</td>
                            <td class="fila2"><input type="text" class="datoreq" id="ingreso_rep" name="ingreso_rep" value="" size="8" ></td>
                        </tr>
                        <tr>
                            <td class="encabezadoTabla">Centros de costo</td>
                            <td class="fila2">
                                <select name="wccos_rep" id="wccos_rep">
                                <?php
                                    foreach ($arr_ccos as $key => $arr_info) {
                                        $selctd = ($key == 'ccocir') ? 'selected="selected"': "";
                                        echo '<option '.$selctd.' value="'.implode("_", $arr_info["codigos"]).'">'.utf8_decode($arr_info["nombre"]).'</option>';
                                    }
                                ?>
                                </select>
                            </td>
                            <td class="encabezadoTabla">A&ntilde;os para comparar<br>(Puede seleccionar<br>varios)</td>
                            <td class="fila2">
                                <select name="wanios_comparables" id="wanios_comparables" multiple="multiple">
                                    <?php
                                        foreach ($anios_select as $key => $aniosel) {
                                            echo '<option value="'.$aniosel.'">'.$aniosel.'</option>';
                                        }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <!-- <tr>
                            <td class="encabezadoTabla">Documento</td>
                            <td class="fila2"><input type="text" class="datoreq" id="wdocumento_rep" name="wdocumento_rep" value="" size="8" ></td>
                            <td class="encabezadoTabla">&nbsp;</td>
                            <td class="fila2">&nbsp;</td>
                        </tr> -->
                        <tr>
                            <td colspan="4" class="fila2" style="text-align:center;"><input type="button" id="btn_generar_rep" name="btn_generar_rep" value="Consultar" onclick="generarReporte();"><span id="gif_carga"></span></td>
                        </tr>
                    </table>
                </div>
                <div id="div_reporte" style="width:100%;" align="center">
                    <table id="tabla_contenedor_rep">
                        <!-- <tr class="encabezadoTabla">
                            <td>Filtrar insumos: <input type="text" id="id_search_consulta" name="id_search_consulta" value="" ></td>
                        </tr> -->
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
                    </table>
                </div>
            </div>
        </td>
    </tr>
</table>
<br />
<br />
<table align='center'>
    <tr><td align="center" colspan="9"><input type="button" value="Cerrar Ventana" onclick="cerrarVentanaPpal();"></td></tr>
</table>
<br />
<br />
</body>
</html>