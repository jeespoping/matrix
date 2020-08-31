<html>
<head>
<title>Evoluci&oacute;n registro de caracterizaci&oacute;n</title>
</head>
<script type="text/javascript" src="../../../include/root/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
<script>
function mostrarDiv(seccion, cco)
{
    div="div"+seccion+"_"+cco;
    $.blockUI({ message: $("#"+div),
                            css: { left: '15%',
                                    top: '15%',
                                  width: '60%',
                                  height: '65%'
                                 }
                      });

}
function ocultarDiv(seccion, cco)
{
    $.unblockUI();
}
</script>
<?php
include_once("conex.php");
/*
*********************************************************************************************************************************************************************
*Fecha de creación: 2012-09-05
*Camilo Zapata.
*Este reporte muestra cuantos usuarios de cada centro de costos han llenado los campos de cada sección de la caracterización.
*/

/*----------------------------------------------------MODIFICACIONES----------------------------------------------------------------------------------------------|
* 2012/09/10 Camilo Zapata: Se agregaron dos columnas en los consolidados que aparecen al final del reporte los cuales muestran el total de empleados sea de la
*							clinica o del centro de costos y el porcentaje del 100%.
*-----------------------------------------------------------------------------------------------------------------------------------------------------------------|
*/
include_once("root/comun.php");
include_once("../procesos/funciones_talhuma.php");




$wactualiz="2012-09-10";
$wbasedato='talhuma';
encabezado("Evolución registro de caracterización ", $wactualiz, "clinica");
session_start();
if (!isset($_SESSION['user']))
    echo "error";
else
{
    $wbasedato = consultarPrefijo($conex, $wemp_pmla, '01');
    //FUNCIONES.

    //Esta funcion relaciona los arreglos de usuarios que han llenado alguna sección, los empleados y los centros de costos, con el fin de poder mostrar en pantalla de manera
    //ordenada cuantos usuarios y de cuales centros de costos ya realizaron los cuestionarios de cada sección.
    function relacionarUsuariosSeccionCco($usuariosEnSeccion, $empleados, $centrosCostos)
    {
        $consolidado=array();
        foreach($usuariosEnSeccion as $keySeccion => $usuariosSeccion)
        {
            foreach($usuariosSeccion['usuarios'] as $keyUsuario=>$dato)
            {
                $cco = $empleados[$keyUsuario]['cco'];
                    if($consolidado[$keySeccion][$cco]['total']=='' or !isset($consolidado[$keySeccion][$cco]['total']))
                        $consolidado[$keySeccion][$cco]['total']=0;
                    $consolidado[$keySeccion][$cco]['total']++;
                    if($consolidado[$keySeccion][$cco]['usuarios']=='' or !isset($consolidado[$keySeccion][$cco]['usuarios']))

                        $consolidado[$keySeccion][$cco]['usuarios']=$keyUsuario."|".$empleados[$keyUsuario]['nombre']."|S";
                        else
                         {
                            $consolidado[$keySeccion][$cco]['usuarios'].=",".$keyUsuario."|".$empleados[$keyUsuario]['nombre']."|S";
                         }
            }
        }
        return(@$consolidado);
    }

    //funcion que busca los usuarios que faltan en registrar las secciones. organizados por centros de costos.
    function usuariosFalatantesPorSeccion($consolidado, $empleados, $secciones)
    {
        //buscamos cada usuario en el arreglo de usuarios por sección del arreglo "secciones", y de esta manera sabemos si exíste o no
        foreach($secciones as $keySeccion=>$datosSeccion)
        {
            foreach($empleados as $keyEmpleado=>$datosEmpleado)//recorro cada empleado
            {
                if(!(array_key_exists($keyEmpleado, $datosSeccion['usuarios'])))
                {
                    //echo "<br>".$keyEmpleado." no existe en: ".$keySeccion." centro costos: ".$datosEmpleado['cco'];
                    if(($consolidado[$keySeccion][$datosEmpleado['cco']]['usuarios']=='') or !(isset($consolidado[$keySeccion][$datosEmpleado['cco']]['usuarios'])))
                         $consolidado[$keySeccion][$datosEmpleado['cco']]['usuarios']=$keyEmpleado."|".$datosEmpleado['nombre']."|N";
                        else
                            $consolidado[$keySeccion][$datosEmpleado['cco']]['usuarios'].=",".$keyEmpleado."|".$datosEmpleado['nombre']."|N";
                }
            }
        }
        return $consolidado;
    }

    //funcion que recorre el array consolidado, de tal manera que se presente en pantalla la relación de empleados de cada centro de costos con la sección de caracterización
    function mostrarResultados($secciones, $ccos, $consolidado)
    {
        $i=0;
        $numSecciones = sizeof($secciones);
        $numCcos = sizeof($ccos);
        $empleadosOkSeccion = array();
        $totalEmpleados = 0;

        echo "<center><table>";
        echo "<tr class='encabezadotabla' aling='center'>";
            echo "<td align='center' colspan=2>CENTRO<br>DE COSTOS</td><td align='center' >TOTAL <br>EMPLEADOS</td>";
            foreach($secciones as $keySeccion=>$datosSeccion)
            {
                echo "<td width=25 align='center' nowrap='nowrap'>".$datosSeccion['nombre']."</td>";
            }
        echo "</tr>";
        foreach($ccos as $keycco=>$datosCco)
        {
            if(is_integer($i/2))
                $wclass='fila1';
                else
                 $wclass='fila2';
            echo "<tr class=".$wclass."><td align='left'>".$keycco."</td><td nowrap='nowrap' align='left'>".$datosCco['nombre']."</td><td align='center'>".$datosCco['totalEmpleados']."</td>";
            $totalEmpleados += $datosCco['totalEmpleados'];
            foreach($secciones as $keySeccion=>$datosSeccion)
            {
                    $empleadosOkSeccion[$keySeccion] += $consolidado[$keySeccion][$keycco]['total'];
                    $faltantes=$datosCco['totalEmpleados']-$consolidado[$keySeccion][$keycco]['total'];
                    if($faltantes==0)
                        echo "<td align='center' onclick='mostrarDiv(\"".$keySeccion."\",\"".$keycco."\")'><img src='../../images/medical/root/CHECKMRK.ICO'></td>";
                        else if($faltantes==$datosCco['totalEmpleados'])
                            echo "<td align='center' onclick='mostrarDiv(\"".$keySeccion."\",\"".$keycco."\")'><span title='falta(n): ".$faltantes." empleado(s)'><img src='../../images/medical/root/borrar.png'></span></td>";
                            else
                                echo "<td align='center' onclick='mostrarDiv(\"".$keySeccion."\",\"".$keycco."\")'><span title='falta(n): ".$faltantes." empleado(s)'><img src='../../images/medical/root/borrarAmarillo.png'></span></td>";
            }
            echo "</tr>";
            $i++;
        }
        echo "<tr><td colspan=".(3 + $numSecciones).">&nbsp</td></tr>";
        echo "<tr class='encabezadotabla'>";
        echo "<td colspan=2>TOTAL EVOLUCI&Oacute;N CARACTERIZACI&Oacute;N - Num&eacute;rico</td>";
        echo "<td align='center'>".$totalEmpleados."</td>";
        foreach ($empleadosOkSeccion as $keySeccion=>$totales)
        {
            echo "<td align='center'>".$totales."</td>";
        }
        echo "</tr>";
        echo "<tr class='encabezadotabla'>";
        echo "<td colspan=2>TOTAL EVOLUCI&Oacute;N CARACTERIZACI&Oacute;N - Porcentual</td>";
        echo "<td align='center'>100%</td>";
        //echo "<td>".$totalEmpleados."</td>";
        foreach ($empleadosOkSeccion as $keySeccion=>$totales)
        {
            $porcentaje = ($totales/$totalEmpleados) *100;
            echo "<td align='center'>".number_format($porcentaje,2,",","")."%</td>";
        }
        echo "</tr>";
        echo "</table></center>";
    }

    //funcion que construye los divs con el detalle de la relación de centros de costos con seccion de caracterización
    function crearDivs($consolidado, $secciones)
    {
        foreach($consolidado as $keySeccion=>$datosSeccion)
        {
            foreach ($datosSeccion as $keyCco=>$datosCco)
            {

                $tabla='';
                $aux = 0;
                $empleados = explode(",", $datosCco['usuarios']);
                $numEmpleados = sizeof($empleados);
                $numTablas = 1;
                if($numEmpleados>30)
                    $numTablas=2;

                $sobrante = $numEmpleados % $numTablas;
                $mitad = round($numEmpleados/ $numTablas);

                $arregloEmpleados = array_chunk($empleados, $numTablas, true);
                $td_abre = "<td align='center' valign='top'	>";
                for ($j = 1; $j <= $numTablas; $j++)
                {
                    if($tabla=='')
                        {
                        $tabla = $td_abre."<center><table>";
                        }else
                            {
                                $tabla .= $td_abre."<center><table style='border-left:2px solid #999999;'>";
                            }
                    $tabla .= "<tr class='encabezadotabla'>";
                        $tabla .= "<td colspan=3 align='center'>Detalle relacion de Empleados del Centro de costos:".$keyCco."<br> en la secci&oacute;n: <br>".$secciones[$keySeccion]['nombre']."</td>";
                    $tabla .= "</tr>";
                    $tabla .= "<tr class='encabezadotabla'>";
                        $tabla .= "<td>C&oacute;digo</td>";
                        $tabla .= "<td>Nombre</td>";
                        $tabla .= "<td>Estado</td>";
                    $tabla .= "</tr>";

                    if($j==$numTablas && $sobrante>0)
                        $limite = ($mitad*2)-1;
                        else
                            $limite = $mitad*$j;

                    for($i=$aux; $i < $limite; $i++)
                        {
                            if(is_integer($i/2))
                                $wclass='fila1';
                                else
                                    $wclass='fila2';

                            $empleado = explode("|", $empleados[$i]);
                            $tabla .= "<tr class='".$wclass."'>";
                                $tabla .= "<td nowrap='nowrap'>".$empleado[0]."</td>";
                                $tabla .= "<td>".$empleado[1]."</td>";
                                if($empleado[2]=='S')
                                    $tabla .= "<td align='center'><img src='../../images/medical/root/CHECKMRK.ICO'></td>";
                                 else
                                     $tabla .= "<td align='center'><img src='../../images/medical/root/borrar.png'></td>";
                            $tabla .="</tr>";
                            $aux++;
                        }
                        $tabla .= "</table></center>";
                        $tabla .= "</td>";
                    }
                $ppal = "<table><tr>$tabla</tr></table>";

                echo "<div id='div".$keySeccion."_".$keyCco."' align='center' style='display:none; cursor:default; background:none repeat scroll 0 0; position:relative; width:98%; height:98%; overflow:auto;'>";
                    echo $ppal;
                    echo "<center><table>";
                    echo "<tr><td>&nbsp;";
                    echo "</td></tr>";
                    echo "<tr><td>";
                    echo "<input type='button' id='btn_div".$keySeccion."_".$keyCco."' value='Cerrar' onclick='ocultarDiv(\"".$keySeccion."\",\"".$keycco."\")'>";
                    echo "</td></tr>";
                echo"</table></center>";
                echo "</div>";
            }
        }
    }
    //FIN FUNCIONES

 if(!isset($wcco))//Menú inicial.
  {
    echo "<form name='reporteCaracterizacion' action='rep_caracterizacion.php' method=post>";
    $q = "SELECT tb1.Ccocod AS codigo, tb1.Cconom AS nombre
            FROM costosyp_000005 AS tb1
           INNER JOIN ".$wbasedato."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)
           WHERE tal.ideest = 'on'
           GROUP BY  tb1.Ccocod
           ORDER BY  tb1.Cconom";
    $rs = mysql_query($q, $conex);
    $numCcos = mysql_num_rows($rs);
    echo "<center><table>";
    echo "<tr class='encabezadotabla'><td>ELIJA EL CENTRO DE COSTOS</td><tr>";
    echo "<tr><td>";
     echo "<select name='wcco'>";
        echo "<option value='%'>Todos</option>";
        for($i = 1; $i <= $numCcos; $i++)
        {
            $rowCco = mysql_fetch_array($rs);
            echo "<option value='".$rowCco['codigo']."'>".$rowCco['codigo']."-"."".$rowCco['nombre']."</option>";
        }
     echo "</select>";

    echo"</td><tr>";
    echo "</table></center>";
    echo "<br>";
    echo "<center><table>";
    echo "<tr><td><input type='submit' value='Buscar'></input></td></tr>";
    echo "</table></center>";
    echo "<input type='hidden' name='wemp_pmla' value='".$wemp_pmla."'>";
    echo "</form>";
  }else
   {
    //Declaración de variables.
    $secciones = Array();  //array que contendrá la información de cada sección de caracterización.
    $centrosCostos = Array(); //array que contendrá la información de cada centro de costos(código, nombre, y cantidad total de empleados)
    $empleados = Array();  //array que contendrá la información de cada empleado(código, nombre y centro de costos al que pertenece)
    $tablasSeccion =  Array();  //array que contendrá la información de secciones con las tablas y campos que la componen, ademas de los usuarios que los han llenado.

    //consulto las secciones.
    $q = "SELECT Distinct Seccod as codigo, Secnom as nombre
            FROM ".$wbasedato."_000045
           WHERE Secest = 'on' ";
    $rs = mysql_query($q, $conex);
    $numSecciones = mysql_num_rows($rs);
    //Armo el array de secciones.
    for($i = 1; $i <= $numSecciones; $i++)
    {
        $rowSecciones = mysql_fetch_array($rs);
        $secciones[$rowSecciones['codigo']]['nombre']=$rowSecciones['nombre'];
    }
    $filtroCco='';
    if($wcco!='%')
    {
        $filtroCco = "AND tal.idecco = '".$wcco."'";
    }
    //consulto y armo el arreglo con los centros de costos que existen en la tabla 13
    $q = "SELECT tb1.Ccocod AS codigo, tb1.Cconom AS nombre
            FROM costosyp_000005 AS tb1
           INNER JOIN ".$wbasedato."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)
           WHERE tal.ideest = 'on'
                 ".$filtroCco."
           GROUP BY  tb1.Ccocod
           ORDER BY  tb1.Cconom";
    $rs = mysql_query($q, $conex);
    $numCcos = mysql_num_rows($rs);
    for($i = 1; $i <= $numCcos; $i++)
    {
        $rowCcos = mysql_fetch_array($rs);
        $centrosCostos[$rowCcos['codigo']]['nombre']=$rowCcos['nombre'];

        //consultamos la totalidad de usuarios en cada centro de costos;
        $q2 = "SELECT count(*) as empleados
                 FROM ".$wbasedato."_000013
                WHERE Idecco='".$rowCcos['codigo']."'
                  AND Ideest='on'";
        $rs2 = mysql_query($q2, $conex);
        $row1=mysql_fetch_array($rs2);
        $centrosCostos[$rowCcos['codigo']]['totalEmpleados']=$row1['empleados'];
    }

    //consulto los usuarios organizados por centros de costos.
    $filtroCco='';
    if($wcco!='%')
    {
        $filtroCco = "AND idecco = '".$wcco."'";
    }
    $q = "SELECT DISTINCT ideuse as codigo, idecco as cco, ideno1, ideno2, ideap1, ideap2
            FROM ".$wbasedato."_000013
           WHERE Ideest = 'on'
           ".$filtroCco."";
    $rs = mysql_query($q, $conex) or die(mysql_error());
    $numEmpleados = mysql_num_rows($rs);
    for($i = 1; $i<=$numEmpleados; $i++)
    {
        $rowEmpleado = mysql_fetch_array($rs);
        $empleados[$rowEmpleado['codigo']]['cco']=$rowEmpleado['cco'];
        $empleados[$rowEmpleado['codigo']]['nombre']=$rowEmpleado[2]." ".$rowEmpleado[3]." ".$rowEmpleado[4]." ".$rowEmpleado[5];
    }
    //consulto las tablas que hay que consultar por cada opcion.
    $q = "SELECT camsec as seccion, camtab as tabla, camuse
            FROM ".$wbasedato."_000046
            WHERE camest = 'on'
           GROUP BY 1,2,3";
           //WHERE camobl = 'on'

    $rs = mysql_query($q, $conex);
    $numRelSeccionTablas = mysql_num_rows($rs);
    for($i = 1; $i<=$numRelSeccionTablas; $i++)
    {
        $rowRelacion = mysql_fetch_array($rs);
        $tablasSeccion[$rowRelacion['seccion']][$rowRelacion['tabla']]['iduser']=$rowRelacion[2];
    }

    //consulto los campos que se tienen que revisar por seccion y por tabla;
    foreach ($tablasSeccion as $keySeccion=>$tablas)
    {
        foreach($tablas as $keyTabla=>$campos)
        {
            $queryCampos = "SELECT camcam
                              FROM ".$wbasedato."_000046
                             WHERE camsec = '".$keySeccion."'
                               AND camtab = '".$keyTabla."'
                               AND camest = 'on'";
                               //AND camobl = 'on'";
            $rsCampos = mysql_query($queryCampos, $conex);
            $numCampos = mysql_num_rows($rsCampos);
            $campos = '';
            for($i = 1; $i <= $numCampos; $i++)
            {
                $rowCampo = mysql_fetch_array($rsCampos);
                if($i==1)
                    $campos=$rowCampo['camcam']." <>''";
                    else
                    $campos.=" or ".$rowCampo['camcam']." <>''";
            }
            $tablasSeccion[$keySeccion][$keyTabla]['campos']=$campos;
        }
    }
// echo '<pre>';print_r($tablasSeccion);echo '</pre>';
    //Se consultan todos aquellos usuarios que hayan llenado algo en cada sección.
    $arrrr = array();
    foreach ($tablasSeccion as $keySeccion=>$tablas)
    {
        foreach($tablas as $keyTabla=>$campos)
        {
            $tabla13='';
            $join13='';
            $filtroCco='';
            if($wcco!='%')
            {
                $filtroCco = "AND idecco = '".$wcco."'";
            }
            if($keyTabla!="000013")
            {
                $tabla13=", ".$wbasedato."_000013";
                $join13= "AND Ideuse = ".$tablasSeccion[$keySeccion][$keyTabla]['iduser']."";
            }
            $queryUsuarios = "SELECT DISTINCT ".$tablasSeccion[$keySeccion][$keyTabla]['iduser']."
                              FROM ".$wbasedato."_".$keyTabla."".$tabla13."
                             WHERE (".$tablasSeccion[$keySeccion][$keyTabla]['campos'].")
                             ".$join13."
                             ".$filtroCco."
                               AND Ideest='on'";
            // echo "<br>query usuarios".$queryUsuarios;
            $rsUsuarios = mysql_query($queryUsuarios, $conex) or die(mysql_error().'AQUIIIIIIIIIIII  '.$queryUsuarios);
            $numUsuarios = mysql_num_rows($rsUsuarios);
            // $secciones[$keySeccion]['usuarios']=array();
            while($rowUsuario = mysql_fetch_array($rsUsuarios))
            {

                $secciones[$keySeccion]['usuarios'][$rowUsuario[0]]='s';
                // echo $keySeccion.' '.$secciones[$keySeccion]['usuarios'][$rowUsuario[0]].' '.$rowUsuario[0].'<br/>';
            }
            $arrrr[] = $secciones;
            // for($i = 1; $i <= $numUsuarios; $i++)
            // {
                // $rowUsuario = mysql_fetch_array($rsUsuarios);
                // $secciones[$keySeccion]['usuarios'][$rowUsuario[0]]='s';
            // }
        }
    }
    // echo '<pre>';print_r($secciones);echo '</pre>';
    // echo '<pre>TODOSSS';print_r($arrrr);echo '</pre>';

    $consolidado = relacionarUsuariosSeccionCco(@$secciones, @$empleados, @$centrosCostos );
    $consolidado = usuariosFalatantesPorSeccion(@$consolidado, @$empleados, @$secciones);
    /*echo "<pre>";
        print_r($consolidado);
    echo "</pre>";*/
    mostrarResultados(@$secciones, @$centrosCostos, @$consolidado);
    crearDivs(@$consolidado, @$secciones);
    echo "<center><table>";
    echo "<tr><td>&nbsp;</td></tr>";
    echo "<tr><td align='center'><a href='rep_caracterizacion.php?wemp_pmla=".$wemp_pmla."'>Retornar</a></td></tr>";
    echo "<tr><td>&nbsp;</td></tr>";
    echo "<tr>";
    echo "<td align='center'><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td>";
    echo"</tr>";
    echo "</table></center>";
   }
}
?>
</html>