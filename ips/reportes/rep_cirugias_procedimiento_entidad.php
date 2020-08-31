<?php
include_once("conex.php"); 

/************************REPORTE DE CIRUGIAS POR PROCEDIMIENTO O ENTIDAD**************************************
 * 
 *  En el reporte se pueden consultar los procedimientos quirúrgicos realizados en un rango de fechas. La consulta puede realizarse por entidad o por procedimiento. Se desglosa hasta el detalle de los elementos médicos usados.
 * 
 * Programa: rep_cirugias_procedimiento_entidad.php
 * Autor: Juan Felipe Balcero Loaiza
 * Tipo de Script: Reporte
 * Ruta del script: matrix\ips\reportes\rep_cirugias_procedimiento_entidad.php
 * 
*/

$wautor = "Juan Felipe Balcero L.";
$wversion='2018-06-28';
$wactualiz='2018-06-28';
session_start();
if(!isset($_SESSION['user']))
{
    echo "<center>[?] Usuario no autenticado en el sistema.<br>Recargue la pagina principal de Matrix o inicie sesion nuevamente.</center>";
    return;
}   


 


header('Content-type: text/html;charset=ISO-8859-1');

if(!isset($wemp_pmla)){
    terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");		
}

$titulo = "REPORTE DE CIRUGIAS POR PROCEDIMIENTO O ENTIDAD";

    /*********************************************************Consultas AJAX**************************************************************************************/

    if(isset($_POST['accion']) && $_POST['accion'] == 'porProcedimiento')
    {   
        //Consulta principal para el reporte por procedimiento. Se usa también como consulta auxiliar al dar click en cada entidad en el reporte por Entidad.
        //Aquí se consulta la tabla de procedimientos y en cada procedimiento, la tabla de pacientes

        //Se da formato a la lista de entidades y cirugias seleccionadas para que puedan ser leídas correctamente en el sql
        $entidad = str_replace(",","','",$entidad);
        $cirugias = str_replace(",","','",$cirugias);

        $procedimiento = consultarProcedimientosYTotales($conex, $wbasedatocliame, $entidad, $fechaInicial, $fechaFinal, $facturable, $cirugias, $limPro);
        $numProced = mysql_num_rows($procedimiento);

        //Para traducir el codigo del procedimiento al nombre, da un mejor rendimiento que asociar las tablas en la consulta.
        $CodigosProcedimientos = consultarProcedimientos($conex,$wbasedatocliame);
        $Paquetes = consultarPaquetes($conex, $wbasedatocliame);       

        if($numProced > 0) 
        {
            //Encabezado de la tabla de procedimientos
            $data .= '<table style="width:100%;">';
            $data .= '   <tr id="encabezado" style="width:100%;">';
            $data .= '        <th style="width:70%;" colspan="2">Procedimiento</th>';
            $data .= '        <th style="width:15%;">Cantidad</th>';
            $data .= '        <th style="width:15%;">Total Procedimiento</th>';
            $data .= '   </tr>';
            $data .= '   <tr class="blanca" style="width:100%;">';
            $data .= '      <td colspan="2"></td>';
            if($rotulo != 'todos')
            {
                $data .= '      <td style="text-align: right;"><img src="../../images/medical/root/chart.png" width="16" height="14" style="cursor: pointer;" onclick="graficar(\'graf'.$entidad.'1\',\''.$nombre.': Cantidad/Procedimientos\')"></td>';
                $data .= '      <td style="text-align: right;"><img src="../../images/medical/root/chart.png" width="16" height="14" style="cursor: pointer;" onclick="graficar(\'graf'.$entidad.'2\',\''.$nombre.': Total/Procedimiento\')"></td>';
            }
            else
            {
                $data .= '      <td style="text-align: right;"><img src="../../images/medical/root/chart.png" width="16" height="14" style="cursor: pointer;" onclick="graficar(\'grafprocedimientos1\',\'Cantidad/Procedimientos\')"></td>';
                $data .= '      <td style="text-align: right;"><img src="../../images/medical/root/chart.png" width="16" height="14" style="cursor: pointer;" onclick="graficar(\'grafprocedimientos2\',\'Total/Procedimiento\')"></td>';
                $total = 0;
                $cantidadTotal = 0;
            }
            
            $data .= '   </tr>';
            
            $dataGraf1 = '<tbody>';
            $dataGraf2 = '<tbody>';            

            while($row = mysql_fetch_assoc($procedimiento))
            {
                $data .='<tr class="procedimiento" name="'.$row['Enlpro'].'" style="width:100%;">';
                if($CodigosProcedimientos[$row['Enlpro']] == '')
                {
                    $nombreCirugia = $Paquetes[$row['Enlpro']];                                        
                }
                else
                {
                    $nombreCirugia = $CodigosProcedimientos[$row['Enlpro']];                                        
                }

                $data .='   <td style="text-align:left;"style="width:70%;"colspan="2"><img style="cursor:pointer" desplegar="" width="15px" height="15px" src="../../images/medical/sgc/mas.png">&nbsp;&nbsp;'.$row['Enlpro'].' '.$nombreCirugia.'</td>';
                $dataGraf1 .= '<tr><td>'.$nombreCirugia.'</td>';
                $dataGraf2 .= '<tr><td>'.$nombreCirugia.'</td>';
                
                $cirugia = $row['Enlpro'];
                $pacientes = consultarPacientes($conex, $wbasedatocliame, $wbasedatotcx, $wbasedatomovhos, $entidad, $fechaInicial, $fechaFinal, $cirugia, $facturable);
                $cantidad = mysql_num_rows($pacientes);
                
                $data .='   <td style="width:15%;">'.$cantidad.'</td>';
                $dataGraf1 .= '<td>'.$cantidad.'</td></tr>';

                $data .='   <td style="width:15%; text-align:right;">$'.number_format($row['Total']).'</td>';
                $dataGraf2 .= '<td>'.$row['Total'].'</td></tr>';
                $data .='</tr>';

                if($rotulo == 'todos')
                {
                    $total += $row['Total'];
                    $cantidadTotal += $cantidad;
                }

                //Encabezado de la tabla de pacientes para cada procedimiento
                $data .='<tr id="'.$row['Enlpro'].'" style="display:none;"><td style="width:4%;"></td><td style="width:96%;" class="detalle" colspan="4"><table style="width:100%;"><tr style="width:100%;">';
                $data .= '        <th width="10%" colspan="2">Historia e ingreso</th>';
                $data .= '        <th width="25%">Paciente</th>';
                $data .= '        <th width="25%">Medico</th>';
                $data .= '        <th width="20%">Especialidad</th>';
                $data .= '        <th width="10%">Tipo de cirugía</th>';
                $data .= '        <th width="10%">Total Paciente</th>';
                $data .= '      </tr>';
                $data .= '   <tr class="blanca" style="width:100%;">';
                $data .= '      <td colspan="6"></td>';

                if($rotulo != 'todos')
                {
                    $data .= '      <td style="text-align: right;"><img src="../../images/medical/root/chart.png" width="16" height="14" style="cursor: pointer;" onclick="graficar(\'graf'.$entidad.$cirugia.'\',\''.$nombre.': '.$nombreCirugia.': Total/Paciente\')"></td>';
                }
                else
                {
                    $data .= '      <td style="text-align: right;"><img src="../../images/medical/root/chart.png" width="16" height="14" style="cursor: pointer;" onclick="graficar(\'graf'.$cirugia.'\',\''.$nombreCirugia.': Total/Paciente\')"></td>';
                }
                
                $data .= '   </tr>';
                
                $dataGraf3 = '<tbody>';

                while($paciente = mysql_fetch_assoc($pacientes))
                {
                    $data .= '   <tr class="paciente" name="'.$paciente['Enlcaq'].'/'.$row['Enlpro'].'/'.$entidad.'/'.$paciente['Enlcaq'].$row['Enlpro'].'/'.$facturable.'/'.$rotulo.'/'.$nombre.'/'.$nombreCirugia.'/'.$paciente['Enlhis'].'-'.$paciente['Enling'].'">';
                    $data .='   <td style="text-align:left;" colspan="2"><img style="cursor:pointer" desplegar="" width="15px" height="15px" src="../../images/medical/sgc/mas.png">&nbsp;&nbsp;'.$paciente['Enlhis'].'-'.$paciente['Enling'].'</td>';
                    $data .='   <td style="text-align:left;">'.utf8_encode($paciente['Pacno1']).' '.utf8_encode($paciente['Pacno2']).' '.utf8_encode($paciente['Pacap1']).' '.utf8_encode($paciente['Pacap2']).'</td>';
                    $data .='   <td style="text-align:left;">'.utf8_encode($paciente['Medno1']).' '.utf8_encode($paciente['Medno2']).' '.utf8_encode($paciente['Medap1']).' '.utf8_encode($paciente['Medap2']).'</td>';
                    $data .='   <td style="text-align:left;">'.$paciente['Espnom'].'</td>';

                    switch($paciente['Turtcx'])
                    {
                        case 'A':
                            $data .='   <td>Ambulatoria</td>';
                            break;
                        case 'H':
                            $data .='   <td>Hospitalaria</td>';
                            break;
                        case 'E':
                            $data .='   <td>Especial</td>';
                            break;
                        default:
                            $data .='   <td>Tipo no valido</td>';
                            break;
                    }
                    
                    $data .='   <td style="text-align:right;">$'.number_format($paciente['Total']).'</td>';
                    $dataGraf3 .= '<tr><td>'.$paciente['Enlhis'].'-'.$paciente['Enling'].'</td><td>'.$paciente['Total'].'</td></tr>';
                    $data .= '   </tr>';                    

                    $data .='<tr id="'.$paciente['Enlcaq'].$row['Enlpro'].'" width="100%"></tr>';
                }
                $dataGraf3 .= '</tbody>';
                if($rotulo != 'todos')
                {
                    $data .= '<input type="hidden" value="'.$dataGraf3.'" id="graf'.$entidad.$cirugia.'">';
                }
                else
                {
                    $data .= '<input type="hidden" value="'.$dataGraf3.'" id="graf'.$cirugia.'">';
                }
                $data .='</table></td></tr>';
            }

            $dataGraf1 .= '</tbody>';
            $dataGraf2 .= '</tbody>';

            if($rotulo != 'todos')
            {
                $data .= '<input type="hidden" value="'.$dataGraf1.'" id="graf'.$entidad.'1">';
                $data .= '<input type="hidden" value="'.$dataGraf2.'" id="graf'.$entidad.'2">';
            }
            else
            {
                $data .= '<input type="hidden" value="'.$dataGraf1.'" id="grafprocedimientos1">';
                $data .= '<input type="hidden" value="'.$dataGraf2.'" id="grafprocedimientos2">';
                $data .= '<tr class="procedimiento"><td class="blanca" colspan="2" style="text-align:left; padding-left:29px;"><b>TOTAL</b></td><td><b>'.$cantidadTotal.'</b></td><td style="text-align:right;"><b>$'.number_format($total).'</b></td></tr>';
            }
            
            $data .= '</table>';
        }
        else
        {
            $data = "N";
        }

        $respuesta['tabla'] = $data;
        $respuesta['excel'] = 'dataExcel';
        
        echo json_encode($respuesta);
        return;
    }

    if(isset($_POST['accion']) && $_POST['accion'] == 'porEntidad')
    {
        //Consulta principal para el reporte por Entidad. Se consulta la tabla de entidades y totales

        //Se da formato a la lista de entidades y cirugias seleccionadas para que puedan ser leídas correctamente en el sql
        $entidad = str_replace(",","','",$entidad);
        $cirugias = str_replace(",","','",$cirugias);

        $empresas = consultarEntidadesYTotales($conex, $wbasedatocliame, $entidad, $fechaInicial, $fechaFinal, $facturable, $cirugias, $limEnt);
        $numEmp = mysql_num_rows($empresas);

        if($numEmp > 0)
        {
            //Encabezado de tabla de entidades
            $data .= '<table style="width:100%;">';
            $data .= '   <tr id="encabezado" style="width:100%;">';
            $data .= '        <th style="width:75%;" colspan="2">Entidad</th>';
            $data .= '        <th style="width:25%;">Total Entidad</th>';
            $data .= '   </tr>';
            $data .= '   <tr class="blanca" style="width:100%;">';
            $data .= '      <td colspan="2"></td>';
            $data .= '      <td style="text-align: right;"><img src="../../images/medical/root/chart.png" width="16" height="14" onclick="graficar(\'graficaEntidades\',\'Total/Entidad\')" style="cursor: pointer;"><input type="hidden" id="graficaEntidades" ></td>';
            $total = 0;
            $data .= '   </tr>';
            
            $dataGraf .= '<tbody>';

            while($empresa = mysql_fetch_assoc($empresas))
            {
                $data .='<tr class="empresa" name="'.$empresa['Tcarres'].'/'.$facturable.'/'.$fechaInicial.'/'.$fechaFinal.'/'.$cirugias.'/'.utf8_encode($empresa['Empnom']).'/0"style="width:100%;">';
                $data .='   <td style="text-align:left;" style="width:75%;"colspan="2"><img style="cursor:pointer" desplegar="" width="15px" height="15px" src="../../images/medical/sgc/mas.png">&nbsp;&nbsp;'.$empresa['Tcarres'].' '.utf8_encode($empresa['Empnom']).'</td>';
                $data .='   <td style="text-align:right;"style="width:25%;">$'.number_format($empresa['Total']).'</td>';
                $data .='</tr>';
                $data .='<tr id="'.$empresa['Tcarres'].'" style="display:none;"><td width="4%"></td><td style="width:96%;" id="aux'.$empresa['Tcarres'].'" class="detalle"colspan="2"></td></tr>';

                $total += $empresa['Total'];
                $dataGraf .= '   <tr>';
                $dataGraf .= '        <td>'.utf8_encode($empresa['Empnom']).'</td>';
                $dataGraf .= '        <td>'.$empresa['Total'].'</td>';
                $dataGraf .= '   </tr>';
            }
            $data .= '<tr class="empresa"><td class="blanca" colspan="2" style="text-align:left; padding-left:29px;"><b>TOTAL</b></td><td style="text-align:right;"><b>$'.number_format($total).'</b></td></tr>';
            $data .= '</table>';
            $dataGraf .= '</tbody>';
        }
        else
        {
            $data = "N";
        }

        $respuesta['tabla'] = $data;
        $respuesta['grafica'] = $dataGraf;

        echo json_encode($respuesta);
        return;
        
    }

    if(isset($_POST['accion']) && $_POST['accion'] == 'consultarDetalle')
    {
        //Consulta AJAX del listado detallado de los conceptos facturados a un paciente. Se realiza al dar click en un paciente.
        //Se consulta la tabla de conceptos liquidados para el paciente, y en cada uno, la tabla de detalle de los conceptos
        $entidad = str_replace("\\","", $entidad);

        $conceptos = consultarConceptos($conex, $consecutivo, $wbasedatocliame, $entidad, $procedimiento, $facturable);
        $numConcep = mysql_num_rows($conceptos);

        if($numConcep > 0)
        {
            //Encabezado tabla de conceptos
            $data = '<td width="4%"></td><td width="96%" colspan ="6" class="detalle"><table width="100%"><tr width="100%">';
            $data .= '        <th width="40%" colspan="2">Concepto</th>';
            $data .= '        <th width="40%">Entidad</th>';
            $data .= '        <th width="20%">Valor Total</th>';
            $data .= '      </tr>';
            $data .= '   <tr class="blanca" width="100%">';
            $data .= '      <td colspan="3"></td>';
            if($rotulo != 'todos')
            {
                $data .= '      <td style="text-align: right;"><img src="../../images/medical/root/chart.png" width="16" height="14" style="cursor: pointer;" onclick="graficar(\'graf'.$entidad.$procedimiento.$consecutivo.'\',\''.$nombre.': '.$nombreCirugia.': '.$nombrePaciente.': Total/Concepto\')"></td>';
            }
            else
            {
                $data .= '      <td style="text-align: right;"><img src="../../images/medical/root/chart.png" width="16" height="14" style="cursor: pointer;" onclick="graficar(\'graf'.$procedimiento.$consecutivo.'\',\''.$nombreCirugia.': '.$nombrePaciente.': Total/Concepto\')"></td>';
            }
            $data .= '   </tr>';
            $dataGraf = '<tbody>'; 

            while($concepto = mysql_fetch_assoc($conceptos))
            {
                $data .= '  <tr class="concepto">';
                $data .='   <td style="text-align:left;" colspan="2"><img style="cursor:pointer" desplegar="" width="15px" height="15px" src="../../images/medical/sgc/mas.png">&nbsp;&nbsp;'.$concepto['Tcarconcod'].' '.$concepto['Tcarconnom'].'</td>';
                $data .='   <td>'.utf8_encode($concepto['Empnom']).'</td>';                
                $data .='   <td style="text-align:right;">$'.number_format($concepto['Total']).'</td>';                        
                $data .='   </tr>';

                $dataGraf .= '<tr><td>'.$concepto['Tcarconnom'].'</td><td>'.$concepto['Total'].'</td></tr>';

                $codigoConcepto = $concepto['Tcarconcod'];
                
                $arraycodigosInventario = explode(',',$codigosInventario);
                if(in_array($codigoConcepto,$arraycodigosInventario))
                {
                    $detalles = consultarDetalles($conex, $consecutivo, $wbasedatocliame, $entidad, $procedimiento, $facturable, $codigoConcepto,$wbasedatomovhos);
                }
                else
                {
                    $detalles = consultarDetallesalt($conex, $consecutivo, $wbasedatocliame, $entidad, $procedimiento, $facturable, $codigoConcepto);
                }
                
                $numDeta = mysql_num_rows($detalles);            

                if($numDeta > 0)
                {
                    //Encabezado tabla de detalle para cada concepto
                    $data .='   <tr style="display:none;"><td width="4%"></td><td width="96%" colspan ="4" class="detalle"><table width="100%"><tr width="100%">';
                    $data .='       <th>Detalle</th>';
                    $data .='       <th>Facturable</th>';
                    $data .='       <th>Entidad</th>';
                    $data .='       <th>Valor</th>';
                    $data .='   </tr>';
                    $data .= '   <tr class="blanca" width="100%">';
                    $data .= '      <td colspan="3"></td>';

                    if($rotulo != 'todos')
                    {
                        $data .= '      <td style="text-align: right;"><img src="../../images/medical/root/chart.png" width="16" height="14" style="cursor: pointer;" onclick="graficar(\'graf'.$entidad.$procedimiento.$consecutivo.$codigoConcepto.'\',\''.$nombre.': '.$nombreCirugia.': '.$nombrePaciente.': '.$concepto['Tcarconnom'].': Total/Item\')"></td>';
                    }
                    else
                    {
                        $data .= '      <td style="text-align: right;"><img src="../../images/medical/root/chart.png" width="16" height="14" style="cursor: pointer;" onclick="graficar(\'graf'.$procedimiento.$consecutivo.$codigoConcepto.'\',\''.$nombreCirugia.': '.$nombrePaciente.': '.$concepto['Tcarconnom'].': Total/Item\')"></td>';
                    }
                    $data .= '   </tr>';
                    $dataGraf2 = '<tbody>';
                    while($detalle = mysql_fetch_assoc($detalles))
                    {
                        $data .= '<tr class="entrada">';
                        $data .='   <td style="text-align: left; padding-left: 10px;">'.$detalle['Tcarprocod'].' '.$detalle['nombre'].'</td>';
                        if($detalle['Liqfac'] == 'S')
                        {
                            $data .='   <td>Si</td>';
                        }
                        else
                        {
                            $data .='   <td>No</td>';
                        }
                        $data .='   <td>'.utf8_encode($detalle['Empnom']).'</td>';
                        $data .='   <td style="text-align:right;">$'.number_format($detalle['Liqvlf']).'</td>';
                        $dataGraf2 .= '<tr><td>'.$detalle['Tcarprocod'].' '.$detalle['nombre'].'</td><td>'.$detalle['Liqvlf'].'</td></tr>';
                        $data .= '</tr>';
                    }
                    $dataGraf2 .= '</tbody>';
                    if($rotulo != 'todos')
                    {
                        $data .= '<input type="hidden" value="'.$dataGraf2.'" id="graf'.$entidad.$procedimiento.$consecutivo.$codigoConcepto.'">';
                    }
                    else
                    {
                        $data .= '<input type="hidden" value="'.$dataGraf2.'" id="graf'.$procedimiento.$consecutivo.$codigoConcepto.'">';
                    }
                    $data .='</table></td></tr>';
                }
                else
                {
                    $data .= "No se encontraron resultados.";
                }
            }
            $dataGraf .= '</tbody>'; 
            if($rotulo != 'todos')
            {
                $data .= '<input type="hidden" value="'.$dataGraf.'" id="graf'.$entidad.$procedimiento.$consecutivo.'">';
            }
            else
            {
                $data .= '<input type="hidden" value="'.$dataGraf.'" id="graf'.$procedimiento.$consecutivo.'">';
            }
            $data .='</table></td>';
        }
        else
        {
            $data = 'N';
        }

        $respuesta['respuesta'] = $data;

        echo json_encode($respuesta);
        return;
    }

    /*******************************************************************Funciones PHP****************************************************************************/
    
    function consultarEntidades($conex,$wbasedato)
    {   
        //Esta función carga las entidades que se colocan en van en la opción del select antes de consultar el reporte

        $respuesta = array();
        $sql = " SELECT Empcod, Empnom ";
        $sql .= " FROM ".$wbasedato."_000024 ";
        $sql .= " WHERE Empest = 'on'; ";    
        $res = mysql_query($sql, $conex) or die(mysql_errno()." - en el query: ".$sql." - ".mysql_error());

        while($row = mysql_fetch_assoc($res))
        {
            $respuesta[$row['Empcod']] = utf8_encode(strtoupper($row['Empnom']));
        }
        return $respuesta;
    }
    
    function consultarProcedimientos($conex,$wbasedatocliame)
    {   
        //Esta función carga las entidades que se colocan en van en la opción del select antes de consultar el reporte

        $respuesta = array();
        $sql = " SELECT Procod, Pronom ";
        $sql .= " FROM ".$wbasedatocliame."_000103 ";
        //$sql .= " WHERE Proest = 'on'; ";    
        $res = mysql_query($sql, $conex) or die(mysql_errno()." - en el query: ".$sql." - ".mysql_error());

        while($row = mysql_fetch_assoc($res))
        {
            $respuesta[$row['Procod']] = utf8_encode($row['Pronom']);
        }
        return $respuesta;
    }

    function consultarPaquetes($conex,$wbasedatocliame)
    {
        //Función que consulta los nombre de las compras de paquete, para añadir su nombre en la tabla de procedimientos
        
        $respuesta = array();
        $sql =  " SELECT Paqcod, Paqnom ";
        $sql .= " FROM ".$wbasedatocliame."_000113 ";

        $res = mysql_query($sql, $conex) or die(mysql_errno()." - en el query: ".$sql." - ".mysql_error());

        while($row = mysql_fetch_assoc($res))
        {
            $respuesta[$row['Paqcod']] = utf8_encode($row['Paqnom']);
        }
        return $respuesta;
    }

    function consultarEntidadesYTotales($conex, $wbasedatocliame, $entidad, $fechaInicial, $fechaFinal, $facturable, $cirugias, $limEnt)
    {
        //Función que realiza la consulta de datos para la tabla de entidades
        
        $sql .= " SELECT Tcarres, Empnom, SUM(Liqvlf) AS Total ";
        $sql .= " FROM(	 ";
        $sql .= " SELECT C.Tcarres, D.Empnom, Liqvlf, C.id ";
        $sql .= " FROM  ".$wbasedatocliame."_000199 A, ".$wbasedatocliame."_000198 B, ".$wbasedatocliame."_000106 C, ".$wbasedatocliame."_000024 D ";
        $sql .= " WHERE A.Enlhis = B.Liqhis AND A.Enling = B.Liqing AND A.Enlpro = B.Liqpro AND A.Enlcaq = B.Liqcaq ";
        $sql .= " AND B.Liqidc = C.id ";
        $sql .= " AND C.Tcarres = D.Empcod ";
        if($cirugias != 'todos')
        {
            $sql .= " AND B.Liqpro IN ('".$cirugias."') ";
        }
        if($entidad != 'todos')
        {
            $sql .= " AND C.Tcarres IN ('".$entidad."') ";
        }        
        if($facturable == 'si')
        {
            $sql .= " AND B.Liqfac = 'S' ";
        }
        else if ($facturable ==  'no')
        {
            $sql .= " AND B.Liqfac = 'N' ";
        }
        $sql .= " AND B.Fecha_data BETWEEN '".$fechaInicial."' AND '".$fechaFinal."' ";
        $sql .= " GROUP BY C.id ";
        $sql .= " ) AS tb3 ";
        $sql .= " GROUP BY Empnom ";
        $sql .= " ORDER BY Total DESC ";
        if($limEnt > 0)
        {
            $sql .= " LIMIT ".$limEnt.";";
        }
        

        $respuesta = mysql_query($sql, $conex) or die(mysql_errno()." - en el query: ".$sql." - ".mysql_error());

        return $respuesta;
    }
    
    function consultarProcedimientosYTotales($conex, $wbasedatocliame, $entidad, $fechaInicial, $fechaFinal, $facturable, $cirugias, $limPro)
    {   
        //Función que consulta los datos para la tabla de procedimientos

        $sql =  " SELECT Enlpro, SUM(Liqvlf) AS Total ";
        $sql .= " FROM ( ";
		$sql .= " SELECT Enlhis, Liqhis, Enling, Liqing, Enlpro, Liqpro, Enlcaq, Liqcaq, Liqidc, C.id, Liqvlf ";
		$sql .= " FROM ".$wbasedatocliame."_000199 A, ".$wbasedatocliame."_000198 B, ".$wbasedatocliame."_000106 C ";
		$sql .= " WHERE A.Enlhis = B.Liqhis AND A.Enling = B.Liqing AND A.Enlpro = B.Liqpro AND A.Enlcaq = B.Liqcaq ";
        $sql .= " AND B.Liqidc = C.id ";
        if($facturable == 'si')
        {
            $sql .= " AND B.Liqfac = 'S' ";
        }
        else if ($facturable ==  'no')
        {
            $sql .= " AND B.Liqfac = 'N' ";
        }
        if($entidad != 'todos')
        {
            $sql .= " AND C.Tcarres IN ('".$entidad."') ";
        } 
        if($cirugias != 'todos')
        {
            $sql .= " AND B.Liqpro IN ('".$cirugias."') ";
        }
		$sql .= " AND A.Fecha_data BETWEEN '".$fechaInicial."' AND '".$fechaFinal."' ";
		$sql .= " GROUP BY 1,2,3,4,5,6,7,8,9,10 ";
        $sql .= " ) AS tb1 ";
        $sql .= " GROUP BY Enlpro ";
        $sql .= " ORDER BY Total DESC ";
        if($limPro > 0)
        {
            $sql .= " LIMIT ".$limPro.";";
        }
        

        $respuesta = mysql_query($sql, $conex) or die(mysql_errno()." - en el query: ".$sql." - ".mysql_error());

        return $respuesta;
    }

    function consultarPacientes($conex, $wbasedatocliame, $wbasedatotcx, $wbasedatomovhos, $entidad, $fechaInicial, $fechaFinal, $cirugia, $facturable)
    {
        // Función que consulta los datos para la tabla de pacientes para un procedimiento

        $sql =  " SELECT Enlhis, Enling, Pacno1, Pacno2, Pacap1, Pacap2, Medno1, Medno2, Medap1, Medap2, Enlcaq, Espnom, Turtcx, SUM(Liqvlf) AS Total ";
        $sql .= " FROM ( ";
        $sql .= "     SELECT A.Enlhis, A.Enling, A.Enlcaq, A.Enlter, E.Espnom, Liqvlf, Turtcx, Turtur, Medno1, Medno2, Medap1, Medap2, B.id, Pacno1, Pacno2, Pacap1, Pacap2 ";
        $sql .= "     FROM ".$wbasedatocliame."_000199 A, ".$wbasedatocliame."_000106 B, ".$wbasedatocliame."_000198 C, ".$wbasedatotcx."_000011 D, ".$wbasedatomovhos."_000044 E, ".$wbasedatomovhos."_000048 F, ".$wbasedatocliame."_000100 G ";
        $sql .= "     WHERE A.Enlhis = C.Liqhis AND A.Enling = C.Liqing AND A.Enlpro = C.Liqpro AND A.Enlcaq = C.Liqcaq ";
        $sql .= "     AND C.Liqidc = B.id ";
        $sql .= "     AND A.Enltur = D.Turtur ";
        $sql .= "     AND A.Enlesp = E.Espcod ";
        $sql .= "     AND A.Enlter = F.Meddoc ";
        $sql .= "     AND A.Enlhis = G.Pachis ";
        if($facturable == 'si')
        {
            $sql .= " AND C.Liqfac = 'S' ";
        }
        else if ($facturable ==  'no')
        {
            $sql .= " AND C.Liqfac = 'N' ";
        }
        if($entidad != 'todos')
        {
            $sql .= " AND B.Tcarres IN ('".$entidad."') ";
        } 
        $sql .= "     AND A.Enlpro = '".$cirugia."' ";
        $sql .= "     AND A.Fecha_data BETWEEN '".$fechaInicial."' AND '".$fechaFinal."' ";
        $sql .= "     GROUP BY Enlhis, Enling, Turtur, B.id ";
        $sql .= " ) AS tb2 ";
        $sql .= " GROUP BY Enlcaq ";
        $sql .= " ORDER BY Total DESC; ";

        $respuesta = mysql_query($sql, $conex) or die(mysql_errno()." - en el query: ".$sql." - ".mysql_error());

        return $respuesta;
    }

    function consultarConceptos($conex, $consecutivo, $wbasedatocliame, $entidad, $procedimiento, $facturable)
    {   
        //Función que consulta los datos para la tabla de conceptos para un paciente determinado

        $sql =  " SELECT SUM(Liqvlf) AS Total, C.Empnom, B.Tcarconnom, B.Tcarconcod ";
        $sql .= " FROM ".$wbasedatocliame."_000198, ".$wbasedatocliame."_000106 B, ".$wbasedatocliame."_000024 C ";
        $sql .= " WHERE Liqidc = B.id ";
        $sql .= " AND B.Tcarres = C.Empcod ";
        if($facturable == 'si')
        {
            $sql .= " AND Liqfac = 'S' ";
        }
        else if ($facturable ==  'no')
        {
            $sql .= " AND Liqfac = 'N' ";
        }
        if($entidad != 'todos')
        {
            $sql .= " AND B.Tcarres IN ('".$entidad."') ";
        } 
        $sql .= " AND Liqpro = '".$procedimiento."' ";
        $sql .= " AND Liqcaq = '".$consecutivo."' ";
        $sql .= " GROUP BY Tcarconcod ";
        $sql .= " ORDER BY Total DESC; ";

        $respuesta = mysql_query($sql, $conex) or die(mysql_errno()." - en el query: ".$sql." - ".mysql_error());

        return $respuesta;
    }

    function consultarDetalles($conex, $consecutivo, $wbasedatocliame, $entidad, $procedimiento, $facturable, $codigoConcepto,$wbasedatomovhos)
    {
        //Función que consulta los datos para la tabla de detalles para un concepto determinado, para un paciente determinado

        $sql =  " SELECT Liqvlf, C.Empnom, Liqfac, Tcarprocod, Artcom AS nombre ";
        $sql .= " FROM ".$wbasedatocliame."_000198, ".$wbasedatocliame."_000106 B, ".$wbasedatocliame."_000024 C, ".$wbasedatomovhos."_000026 D ";
        $sql .= " WHERE Liqidc = B.id ";
        $sql .= " AND B.Tcarres = C.Empcod ";
        $sql .= " AND B.Tcarprocod = D.Artcod ";
        if($entidad != 'todos')
        {
            $sql .= " AND B.Tcarres IN ('".$entidad."') ";
        }
        if($facturable == 'si')
        {
            $sql .= " AND Liqfac = 'S' ";
        }
        else if ($facturable ==  'no')
        {
            $sql .= " AND Liqfac = 'N' ";
        }
        $sql .= " AND Liqpro = '".$procedimiento."' ";
        $sql .= " AND B.Tcarconcod = '".$codigoConcepto."' ";
        $sql .= " AND Liqcaq = '".$consecutivo."' ";
        $sql .= " ORDER BY Liqvlf DESC; ";

        $respuesta = mysql_query($sql, $conex) or die(mysql_errno()." - en el query: ".$sql." - ".mysql_error());

        return $respuesta;
    }

    function consultarDetallesalt($conex, $consecutivo, $wbasedatocliame, $entidad, $procedimiento, $facturable, $codigoConcepto)
    {
        //Función que consulta los datos para la tabla de detalles para un concepto determinado, para un paciente determinado

        $sql =  " SELECT Liqvlf, C.Empnom, B.Tcarpronom AS nombre, Liqfac, Tcarprocod ";
        $sql .= " FROM ".$wbasedatocliame."_000198, ".$wbasedatocliame."_000106 B, ".$wbasedatocliame."_000024 C ";
        $sql .= " WHERE Liqidc = B.id ";
        $sql .= " AND B.Tcarres = C.Empcod ";
        if($entidad != 'todos')
        {
            $sql .= " AND B.Tcarres IN ('".$entidad."') ";
        }
        if($facturable == 'si')
        {
            $sql .= " AND Liqfac = 'S' ";
        }
        else if ($facturable ==  'no')
        {
            $sql .= " AND Liqfac = 'N' ";
        }
        $sql .= " AND Liqpro = '".$procedimiento."' ";
        $sql .= " AND B.Tcarconcod = '".$codigoConcepto."' ";
        $sql .= " AND Liqcaq = '".$consecutivo."' ";
        $sql .= " ORDER BY Liqvlf DESC; ";

        $respuesta = mysql_query($sql, $conex) or die(mysql_errno()." - en el query: ".$sql." - ".mysql_error());

        return $respuesta;
    }

    function consultarCodigosQueMuevenInventario($conex, $wbasedatocliame)
    {
        $sql .= " SELECT Grucod ";
        $sql .= " FROM ".$wbasedatocliame."_000200 ";
        $sql .= " WHERE Gruinv = 'on'; ";

        $respuesta = mysql_query($sql, $conex) or die(mysql_errno()." - en el query: ".$sql." - ".mysql_error());

        return $respuesta;
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>    
    <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MATRIX</title>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />   
    <link rel="stylesheet" href="../../../include/root/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.css" />
    <link rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.filter.css" />
    <style type="text/css">
        .clearfix {
            &:before,
            &:after {
            content: " ";
            display: table;
            }
            &:after {
            clear: both;
            }
        }
        BODY{
            width : 100%!important;
        }
        .titulopagina2{
            border-bottom-width: 1px;
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
        .well .entrada-fecha{
            width: 200px;
            background-color: #ffffff;
        }
        .well{
            background-color: #E8EEF7;
            width: 55%;
            float: left;
            margin: 0;
            padding-left: 10px;
            padding-rigth: 10px;
        }
        .contenido{
            width: 60%;            
            text-align: center;    
            margin: 0 auto;        
        }
        .alert-info{
            background-color: #2a5db0;
            color: #ffffff;
        }
        th{
            background-color: #2a5db0;
            color: #ffffff;
            padding: 5px;
        }
        tr.empresa{
            background-color:   #E8EEF7;
            text-transform:     uppercase;
        }
        tr.procedimiento{
            background-color:   #C3D9FF;
            text-transform:     uppercase;
        }
        tr.paciente{
            background-color:   #ffffcc;
            text-transform:     uppercase;
        }
        tr.concepto{
            background-color:   #dddddd;
            text-transform:     uppercase;
        }
        tr.entrada:nth-child(even){
            background-color:   #E8EEF7;
            text-transform:     uppercase;
        }
        tr.entrada:nth-child(odd){
            background-color:   #C3D9FF;
            text-transform:     uppercase;
        }
        .resultado1{
            background-color:   #C3D9FF;
            color:              #000000;
            font-size:          8pt;
            padding:            1px;
            font-family:        verdana;
        }
        .resultado2{
            background-color:   #E8EEF7;
            color:              #000000;
            font-size:          8pt;
            padding:            1px;
            font-family:        verdana;
        }
        .centrado{
            margin: 0 auto;
        }
        .container{
            width: 95%;
        }
        
        #resultados td,
        #resultados th{
            text-align: center;
            border: #ffffff 2px solid;            
        }
        td{
            padding: 4px;
        }
        .numero-resultados{
            text-align: right;
            padding:    5px;
            width: 90%;
        }
        #resultados{
            text-align: center;
            margin: 20px auto;
        }
        .ui-multiselect { background:white; background-color:white; color: black; font-weight: normal; border-color: black; border: 2px; height:20px; width:450px; overflow-x:auto; text-align:left;font-size: 10pt;border-radius: 1px; overflow-y:auto;}

        .ui-multiselect-menu { background:white; background-color:white; color: black;}

        .ui-multiselect-header { background:white; background-color:lightgray; color: black;}

        .fijo{
            position: fixed;
            top : 0;
        }
        #seleccionados,
        #seleccionadosPro{
            resize: none;
            height: 102.5px;
            width: 32%;
            padding-left: 5px;
            text-align: left;
            overflow: auto;
            padding-top: 5px;
            margin-left: 10px;
            margin-bottom: 5px;
        }
        select {
            width:300px; 
            overflow:hidden; 
            white-space:pre; 
            text-overflow:ellipsis;
            -webkit-appearance: none;
        }
        .detalle{
            padding: 15px 0;
        }
        tr.blanca{
            background-color:white;
            height: 20px;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
        .mostrada{
            animation: fadeIn 0.5s ease-in;
        }
        .ocultada{
            animation: fadeOut 1s ease-out;
        }
        
    </style>
    
    <script src="../../../include/gentelella/vendors/jquery/dist/jquery.min.js" ></script>
    <script src="../../../include/root/bootstrap.min.js"></script>
    <script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>    
    <script type="text/javascript" src="../../../include/root/jquery.multiselect.js"></script>
    <script type="text/javascript" src="../../../include/root/jquery.multiselect.filter.js"></script>
    <script src="../../../include/root/LeerTablaAmericas.js"  type="text/javascript"></script>
    <script src="../../../include/root/amcharts/amcharts.js" type="text/javascript"></script>
    <script type="text/javascript">                
        $(function () {
            
            var fechaActual = new Date();

            $("#txtfecini,#txtfecfin").datepicker({
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
              showOn: "button",
              buttonImage: "../../images/medical/root/calendar.gif",
              buttonImageOnly: true,
              changeMonth: true,
              changeYear: true,
              maxDate:fechaActual
            });    

            $('#entidad').multiselect({
                     numberDisplayed: 1,
                     multiple:true,
                     selectedText: '# seleccionados'
            }).multiselectfilter();

            $('#procedimientos').multiselect({
                     numberDisplayed: 1,
                     multiple:true,
                     selectedText: '# seleccionados'
            }).multiselectfilter();

            $("#procedimientos").on("multiselectclose", function(event, ui){

                //Esta funcion se activa al cerrar el select de procedimientos y actualiza la lista de seleccionados
                var escogidos = $("#procedimientos").val();
                                
                if(escogidos != null)
                {
                    var newchar = '<br>- ';
                    var aux;

                    for(var j = 0; j < escogidos.length; j++)
                    {
                        var elegidos = escogidos[j].toString();
                        aux = elegidos.split('-');
                        escogidos[j] = aux[1];
                    }
                    
                    elegidos = escogidos.join(newchar);
                    $('#seleccionadosPro').show();
                    $('#seleccionadosPro').html("<b>Procedimientos seleccionados:</b><br>- " + elegidos);
                }
                else
                {
                    $('#seleccionadosPro').html("<b>Procedimientos seleccionados:</b><br>- Todos");
                }
                
            });
            
            $("#entidad").on("multiselectclose", function(event, ui){

                //Esta funcion se activa al cerrar el select de entidades y actualiza la lista de seleccionados
                var escogidos = $('#entidad').val();                
                if(escogidos != null)
                {
                    var elegidos = escogidos.toString();
                    var newchar = '<br>- ';
                    var aux = elegidos.split(',');

                    for(var i = 0; i < aux.length; i++)
                    {
                        aux2=aux[i].split('-');
                        aux[i] = aux2[aux2.length - 1];                    
                    }
                    elegidos = aux.join(newchar);
                    $('#seleccionados').show();
                    $('#seleccionados').html("<b>Entidades seleccionadas:</b><br>- " + elegidos);
                }
                else
                {
                    $('#seleccionados').html("<b>Entidades seleccionadas:</b><br>- Todas");
                }
                
            });

            $("#cancel_edit").click(function(){             
                
                var confirmacion = confirm("Esta seguro que desea salir?");
                if(confirmacion == true)
                {
                    window.open('','_parent',''); 
                    window.close();
                }
                 
            });

            $('input[type=radio][name=tipo]').change(function() {
                if (this.value == 'procedimiento') {
                    $('#limProced').prop('disabled', false);
                    $('#limEntidad').prop('disabled', true);
                }
                else if (this.value == 'entidad') {
                    $('#limEntidad').prop('disabled', false);
                    $('#limProced').prop('disabled', true);
                }
            });

        });

        function Consultar(){
            //Función asociada al botón de consulta. Toma los inputs, valida los valores y dependiendo del tipo de reporte seleccionado, realiza una de dos consultas ajax

            var fechaInicial    = $('#txtfecini').val();
            var fechaFinal      = $('#txtfecfin').val();
            var facturable      = $('input[name="facturable"]:checked').val();
            var tipo            = $('input[name="tipo"]:checked').val();
            var limEnt          = $('#limEntidad').val();
            var limPro          = $('#limProced').val();            
            
            //Se revisa que esté asignadas las fechas 
            if(fechaInicial == '' || fechaFinal == '')
            {
                $('#errorFechas').modal();
                return;
            }

            //Se revisa que esté seleccionado el tipo de reporte
            if(tipo == null)
            {
                $('#errorTipo').modal();
                return;
            }
            
            var newchar = ',';
            
            //Se valida y se da formato a las entidades seleccionadas
            var entidad         = $('#entidad').val();
            if(entidad == null)
            {
                entidad = 'todos';
            }
            else
            {
                entidad = entidad.toString();
                var aux = entidad.split(',');
                for(var i = 0; i < aux.length; i++)
                {
                    aux2=aux[i].split('-');
                    aux2.splice(aux2.length - 1, 1);
                    aux[i] = aux2.join('-');
                }
                entidad = aux.join(newchar);
            }         
            
            //Se valida y se da formato a los procedimientos seleccionados
            var cirugias = $('#procedimientos').val()
            if(cirugias == null)
            {
                cirugias = 'todos';
            }
            else
            {
                cirugias = cirugias.toString();
                var aux = cirugias.split('-');
                for(var i = 0; i < aux.length - 1; i++)
                {
                    var aux2 = aux[i].split(',');
                    aux[i] = aux2[aux2.length - 1];
                }
                aux.splice(-1, 1);
                cirugias = aux.join(newchar);
            }

            if(limEnt == null || limEnt < 1)
            {
                limEnt = 0;
            }

            if(limPro == null || limPro < 1)
            {
                limPro = 0;
            }

            var wbasedato       = $('#wbasedato').val();
            var wbasedatomovhos = $('#wbasedatomovhos').val();
            var wbasedatocliame = $('#wbasedatocliame').val();
            var wbasedatocencam = $('#wbasedatocencam').val();
            var wbasedatotcx    = $('#wbasedatotcx').val(); 

            
            $('#cargando').modal({
                backdrop: 'static',
                keyboard: false
            });
            
            if(tipo == 'procedimiento')
            {
                $.post("rep_cirugias_procedimiento_entidad.php",
                {
                    consultaAjax    :   true,
                    accion          :   'porProcedimiento',
                    fechaInicial    :   fechaInicial,
                    fechaFinal      :   fechaFinal,                      
                    entidad         :   entidad,
                    rotulo          :   'todos',
                    cirugias        :   cirugias,
                    facturable      :   facturable,
                    limPro          :   limPro,
                    wbasedato       :   wbasedato,
                    wbasedatomovhos :   wbasedatomovhos,
                    wbasedatocliame :   wbasedatocliame,
                    wbasedatocencam :   wbasedatocencam,
                    wbasedatotcx    :   wbasedatotcx,
                    wemp_pmla       :   $("#wemp_pmla").val()

                },function(data)
                {                    
                    $('#cargando').modal('toggle');
                    if(data.tabla != 'N')
                    {
                        $('#resultados').html(data.tabla);
                        $('.procedimiento img').not( ".conEvento" ).on( "click", mostrarDetalle).addClass('conEvento');
                        $('.paciente img').not( ".conEvento" ).on( "click", mostrarMasDetalle).addClass('conEvento');                        
                    }
                    else
                    {                        
                        $('#resultados').html("No se encontraron resultados.");
                    }
                    
                },"json");
            }
            else if(tipo == 'entidad')
            {                
                $.post("rep_cirugias_procedimiento_entidad.php",
                {
                    consultaAjax    :   true,
                    accion          :   'porEntidad',
                    fechaInicial    :   fechaInicial,
                    fechaFinal      :   fechaFinal,                      
                    entidad         :   entidad,
                    cirugias        :   cirugias,
                    facturable      :   facturable,
                    limEnt          :   limEnt,
                    wbasedato       :   wbasedato,
                    wbasedatomovhos :   wbasedatomovhos,
                    wbasedatocliame :   wbasedatocliame,
                    wbasedatocencam :   wbasedatocencam,
                    wbasedatotcx    :   wbasedatotcx,
                    wemp_pmla       :   $("#wemp_pmla").val()

                },function(data)
                {                    
                    $('#cargando').modal('toggle');
                    if(data.tabla != 'N')
                    {
                        $('#resultados').html(data.tabla);
                        $('.empresa img').not( ".conEvento" ).on( "click", mostrarProcedimientos).addClass('conEvento');
                        $('#graficaEntidades').val(data.grafica);             
                    }
                    else
                    {                        
                        $('#resultados').html("No se encontraron resultados.");
                    }
                    
                },"json");
            }
            
        }

        function mostrarProcedimientos(){
            //Si el tipo de reporte es por entidad, esta función se llama al dar click en una entidad y se consultan los procedimientos   

            var img = $(this);
            var obj  = $(this).parent().parent();
            var name = $(this).parent().parent().attr('name');                
            var aux = name.split('/');
            var entidad = aux[0];            
            var facturable = aux[1];
            var fechaInicial = aux[2];
            var fechaFinal = aux[3];
            var cirugias = aux[4];
            var nombre = aux[5];
            var limPro = aux[6];
            cirugias = cirugias.split("'");
            cirugias = cirugias.join();
            var selector = '#aux' + entidad;
            var selector2 = '#' + entidad; 

            if($(selector).is(':empty'))
            {                              

                var wbasedato       = $('#wbasedato').val();
                var wbasedatomovhos = $('#wbasedatomovhos').val();
                var wbasedatocliame = $('#wbasedatocliame').val();
                var wbasedatocencam = $('#wbasedatocencam').val();
                var wbasedatotcx    = $('#wbasedatotcx').val();
                
                $('#cargando').modal({
                    backdrop: 'static',
                    keyboard: false
                });

                $.post("rep_cirugias_procedimiento_entidad.php",
                {
                    consultaAjax    :   true,
                    accion          :   'porProcedimiento',
                    fechaInicial    :   fechaInicial,
                    fechaFinal      :   fechaFinal,                      
                    entidad         :   entidad,
                    rotulo          :   'uno',
                    cirugias        :   cirugias,
                    facturable      :   facturable,
                    nombre          :   nombre,
                    limPro          :   limPro,
                    wbasedato       :   wbasedato,
                    wbasedatomovhos :   wbasedatomovhos,
                    wbasedatocliame :   wbasedatocliame,
                    wbasedatocencam :   wbasedatocencam,
                    wbasedatotcx    :   wbasedatotcx,
                    wemp_pmla       :   $("#wemp_pmla").val()

                },function(data)
                {                    
                    $('#cargando').modal('toggle');
                    obj.next("tr").toggle();
                    obj.next("tr").toggleClass("mostrada");
                    img.attr("src",  "../../images/medical/sgc/menos.png");
                    if(data.tabla != 'N')
                    {
                        $(selector).html(data.tabla);
                        $('.procedimiento img').not( ".conEvento" ).on( "click", mostrarDetalle).addClass('conEvento');
                        $('.paciente img').not( ".conEvento" ).on( "click", mostrarMasDetalle).addClass('conEvento');                      
                    }
                    else
                    {                        
                        $(selector).html("No se encontraron resultados.");
                    }
                    
                },"json");
            }
            else
            {
                obj.next("tr").toggleClass("ocultada");
                obj.next("tr").toggle();
                obj.next("tr").toggleClass("mostrada");                
                if(img.attr("src") == "../../images/medical/sgc/mas.png")
                {
                    img.attr("src",  "../../images/medical/sgc/menos.png");
                }
                else
                {
                    img.attr("src",  "../../images/medical/sgc/mas.png");
                }
            }         
            
        }

        function mostrarDetalle(){
            //Función para mostrar los pacientes de un procedimientos

            var obj  = $(this).parent().parent();
            var img = $(this);
            obj.next("tr").toggle();
            obj.next("tr").toggleClass("mostrada");
            if(img.attr("src") == "../../images/medical/sgc/mas.png")
            {
                img.attr("src",  "../../images/medical/sgc/menos.png");
            }
            else
            {
                img.attr("src",  "../../images/medical/sgc/mas.png");
            }
        }

        function mostrarMasDetalle(){
            //Función para consultar los conceptos de un paciente

            var img = $(this);
            var obj  = $(this).parent().parent();
            var wbasedatocliame = $('#wbasedatocliame').val();
            var wbasedatomovhos = $('#wbasedatomovhos').val();
            var codigosInventario = $('#codigosInventario').val();
            var name = $(this).parent().parent().attr('name');
            var aux = name.split('/');
            var consecutivo = aux[0];
            var procedimiento = aux[1];
            var entidad = aux[2];
            var id = aux[3];
            var facturable = aux[4];
            var rotulo = aux[5];
            var nombre = aux[6];
            var nombreCirugia = aux[7];
            var nombrePaciente = aux[8];
            var selector = '#' + id;

            if($(selector).is(':empty'))
            {                
                $('#cargando').modal({
                    backdrop: 'static',
                    keyboard: false
                });

                $.post("rep_cirugias_procedimiento_entidad.php",
                {
                    consultaAjax    :   true,
                    accion          :   'consultarDetalle',
                    wbasedatocliame :   wbasedatocliame,
                    wbasedatomovhos :   wbasedatomovhos,
                    consecutivo     :   consecutivo,
                    procedimiento   :   procedimiento,
                    entidad         :   entidad,
                    codigosInventario : codigosInventario,
                    rotulo          :   rotulo,
                    nombre          :   nombre,
                    nombreCirugia   :   nombreCirugia,
                    nombrePaciente  :   nombrePaciente,
                    facturable      :   facturable,
                    wemp_pmla       :   $("#wemp_pmla").val()

                },function(data)
                {                  
                    if(data.respuesta != 'N')
                    {
                        $('#cargando').modal('toggle');
                        img.attr("src",  "../../images/medical/sgc/menos.png");
                        obj.next("tr").toggleClass("mostrada");
                        $(selector).html(data.respuesta);
                         
                        $('.concepto img').not( ".conEvento" ).on( "click", mostrarAunMasDetalle).addClass('conEvento');

                    }
                    else
                    {                        
                        $(selector).html("No se encontraron resultados");
                    }
                    
                },"json");
            }
            else
            {
                obj.next("tr").toggle();
                obj.next("tr").toggleClass("mostrada");
                obj.next("tr").toggleClass("ocultada");
                if(img.attr("src") == "../../images/medical/sgc/mas.png")
                {
                    img.attr("src",  "../../images/medical/sgc/menos.png");
                }
                else
                {
                    img.attr("src",  "../../images/medical/sgc/mas.png");
                }
            }            

        }

        function mostrarAunMasDetalle(){
            //Función para mostrar los detalles de un concepto
            $(this).parent().parent().next("tr").toggle();
            $(this).parent().parent().next("tr").toggleClass("mostrada");
            var img = $(this);
            if(img.attr("src") == "../../images/medical/sgc/mas.png")
            {
                img.attr("src",  "../../images/medical/sgc/menos.png");
            }
            else
            {
                img.attr("src",  "../../images/medical/sgc/mas.png");
            }
        }

        function graficar(ruta,subtitulo)
        {   
            //Dialogo que pregunta el número de registros que quieren ser graficados. Por defecto tiene un valor de 10.
                     
            var ensayo = $('#'+ruta).val()
            $("#prueba").html(ensayo);
            $('#subtitulo').val(subtitulo); 
            $('#filas').val('10');
            $('#pre-grafica').modal();                       
            
        }

        function pintarGrafica()
        {
            //Limita los datos para que solo se grafiquen el numero de entradas seleccionadas e imprime la gráfica

            var numFilas = $('#filas').val();
            if(numFilas == null || numFilas < 1)
            {
                $('#contGra').modal('show').on('shown.bs.modal', function () {
                    $('#prueba').LeerTablaAmericas(
                    {
                        empezardesdefila: 	0,
                        titulo 			: 	$('#subtitulo').val(),
                        datosadicionales: 	'nada'	,
                        divgrafica 		:	'amchart2',
                        opcionesdelgrafico  :   'si',
                        dimension 		:	'3d'
                    });
                    $('#div_opciones').show();
                    $('#contGra').on('hidden.bs.modal', function(){
                        $('body').css('padding-right','0');
                    });
                });
            }
            else
            {
                var filas = $("#prueba tr:nth-child(-n+"+numFilas+")");
                $('#prueba2').html(filas);

                $('#contGra').modal('show').on('shown.bs.modal', function () {
                    $('#prueba3').LeerTablaAmericas(
                    {
                        empezardesdefila: 	0,
                        titulo 			: 	$('#subtitulo').val(),
                        datosadicionales: 	'nada'	,
                        divgrafica 		:	'amchart2',
                        opcionesdelgrafico  :   'si',
                        dimension 		:	'3d'
                    });
                    $('#div_opciones').show();
                    $('#contGra').on('hidden.bs.modal', function(){
                        $('body').css('padding-right','0');
                    });
                });
            }
            
            $('#pre-grafica').modal('toggle');
        }      

        
</script>
    <SCRIPT LANGUAGE="JavaScript1.2">
    
        function onLoad() {
	        loadMenus();
        }
    </SCRIPT>
</head>
<body width=100%>
    <?php
    
        

    /**********************************Inicio del programa********************************************/
        
            include_once("root/comun.php");
            
            
            /****************************Encabezado************************************/
            

            // Se muestra el encabezado del programa
            encabezado("<div class='titulopagina2'>{$titulo}</div>", $wactualiz, "clinica");

            /***************************Conexion e inicio de variables ****************/

                                  
            $wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");
            $wbasedatomovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
            $wbasedatocliame = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
            $wbasedatocencam = consultarAliasPorAplicacion($conex, $wemp_pmla, 'camilleros');
            $wbasedatotcx = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tcx');

            $entidades = consultarEntidades($conex, $wbasedatocliame);
            $procedimientos = consultarProcedimientos($conex,$wbasedatocliame);
            $codigosInventario = consultarCodigosQueMuevenInventario($conex, $wbasedatocliame);
            
            while($row = mysql_fetch_assoc($codigosInventario))
            {
                $mueveInvetario .= $row['Grucod'].",";
            }
            $mueveInvetario = substr($mueveInvetario, 0, -1);
            $fechaActual = date("Y-m-d");

            //Hidden inputs para guardar los alias de la base, para poder obtenerlos en el llamado AJAX

            echo '<input type="hidden" id="wbasedato" value="'.$wbasedato.'">';
            echo '<input type="hidden" id="wbasedatomovhos" value="'.$wbasedatomovhos.'">';
            echo '<input type="hidden" id="wbasedatocliame" value="'.$wbasedatocliame.'">';
            echo '<input type="hidden" id="wbasedatocencam" value="'.$wbasedatocencam.'">';
            echo '<input type="hidden" id="wbasedatotcx" value="'.$wbasedatotcx.'">';
            //echo '<input type="hidden" id="procedimientos" value="'.json_encode($procedimientos).'">';
            echo '<input type="hidden" id="wemp_pmla" value="'.$wemp_pmla.'">';
            echo '<input type="hidden" id="codigosInventario" value="'.$mueveInvetario.'">';
            //echo '<input type="hidden" id="fechaActual" value="'.$fechaActual.'">';
        
    ?>
    
    <div class="contenido"> 
        <div class="row">
            <div class="well">
                <form action="rep_urgenciasPorEntidad.php">
                    <div class="container">
                        <div class="row justify-content-center">
                        <table>
                            <tr style="padding: 5px">
                                Fecha inicial: <input type='text' id='txtfecini' name='txtfecini' size='15' readonly value="<?php echo $fechaActual; ?>"> 
                                Fecha final: <input type='text' id='txtfecfin' name='txtfecfin' size='15' readonly value="<?php echo $fechaActual; ?>"> 
                            </tr>
                            <tr>
                                <td style="text-align: right">Tipo de Reporte:</td>
                                <td></td>
                                <td colspan="2"><input type="radio" name="tipo" value="procedimiento" > Por Procedimiento</td>
                                <td width="150px"><input type="radio" name="tipo" value="entidad" > Por Entidad</td>
                            </tr>
                            <tr>
                                <td style="text-align: right">Entidad:</td>
                                <td></td>
                                <td colspan="3"><select class="selectpicker" data-live-search="true" data-size="8" multiple style="position: static; margin-bottom: 5px; *width: 180px" id="entidad">
                                    <?php
                                        foreach($entidades as $key => $value)
                                        {
                                            echo strtoupper(utf8_decode('<option value="'.$key.'-'.$value.'">'.$key.' - '.$value.'</option>'));
                                        }
                                    ?>                            
                                </select></td>                                
                            </tr>
                            <tr>
                                <td style="text-align: right">Procedimiento:</td>
                                <td></td>
                                <td colspan="3"><select  data-size="8" multiple style="position: static; margin-bottom: 5px; *width: 180px" id="procedimientos">
                                    <?php
                                        foreach($procedimientos as $key => $value)
                                        {
                                            echo strtoupper(utf8_decode('<option value="'.$key.'-'.$value.'">'.$key.' - '.$value.'</option>'));
                                        }
                                    ?>                            
                                </select></td>
                            </tr>
                            <!-- <tr>
                                <td style="text-align: right">Facturado:</td>
                                <td></td>
                                <td><input type="radio" name="facturar" value="facturado" > S&iacute;</td>
                                <td><input type="radio" name="facturar" value="pendiente" > Pendiente</td>
                                <td><input type="radio" name="facturar" value="ambos" > Ambos</td>
                            </tr> -->
                            <tr>
                                <td style="text-align: right">Facturable:</td>
                                <td></td>
                                <td><input type="radio" name="facturable" value="si" > S&iacute;</td>
                                <td><input type="radio" name="facturable" value="no" > No</td>
                                <td><input type="radio" name="facturable" value="ambos" checked> Ambos</td>
                            </tr>
                        </table>                         
                        </div>
                        <input type="button" class="centrado" id="consultar" onclick='Consultar()' value="Buscar" style="margin-top: 5px;">
                        <input type="button" id="cancel_edit" value="Cerrar" style="margin-top: 5px;"></input>
                    </div>                
                </form>            
            </div><!-- Cierre del well-->
            <div id="seleccionados" readonly class="well"><p><b>Entidades seleccionadas:</b><br>- Todas</p></div>
            
            <div id="seleccionadosPro" readonly class="well"><p><b>Procedimientos seleccionados:</b><br>- Todos </p></div>
        </div>
        <div class="modal fade" id="errorFechas" role="dialog">
                <div class="modal-dialog">    
                <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Por favor ingrese el rango de fechas a consultar.</h4>
                        </div>
                    </div>
                </div>
            </div><!-- Cierre del modal-->
            <div class="modal fade" id="errorFacturable" role="dialog">
                <div class="modal-dialog">    
                <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Por favor seleccione una opci&oacute;n en el campo "Facturable".</h4>
                        </div>
                    </div>
                </div>
            </div><!-- Cierre del modal-->
            <div class="modal fade" id="errorTipo" role="dialog">
                <div class="modal-dialog">    
                <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Por favor seleccione el tipo de reporte a generar.</h4>
                        </div>
                    </div>
                </div>
            </div><!-- Cierre del modal-->
            <div class="modal fade" id="contGra" role="dialog">
                <div class="modal-dialog modal-dialog-centered" style="width: 1050px;">    
                <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-body">
                            <fieldset class="centrado" style='padding:15px;width:950px'>
                                <div id='amchart2' style='font-size:5pt;width:950px; height:500px;' ></div>
                            </fieldset>
                            <table id='prueba' style='display:none'>
                            </table>
                            <table id='prueba3' style='display:none'>
                                <tbody id='prueba2'>
                                </tbody>
                            </table> 
                        </div>                                            
                    </div>
                </div>
            </div><!-- Cierre del modal-->
            <div class="modal fade" id="cargando" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">                        
                        <div class="modal-body">
                            Cargando...
                        </div>
                    </div>
                </div>
            </div><!-- Cierre del modal-->
            <div class="modal fade" id="pre-grafica" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">                      
                        <div class="modal-body">
                            <form>
                                <div class="form-group">
                                    <label for="filas" class="col-form-label">N&uacute;mero de datos a graficar:</label>
                                    <input type="number" class="form-control" id="filas">
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary" onclick="pintarGrafica()">Graficar</button>
                        </div>
                    </div>
                </div>
            </div><!-- Cierre del modal-->                 
    </div><!-- Cierre del contenido-->     

    <div style='display:none;' id="subtitulo"></div>
    <div id="divcargando" name="divcargando" style='display:none;' ><center><img width="26" height="26" border="0" src="../../images/medical/ajax-loader9.gif"></center></div>   
    <div class="resultados centrado" id="resultados" style="width: 90%;"></div>
         
</body>
</html>