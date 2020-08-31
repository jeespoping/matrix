<?php
include_once("conex.php"); 

/************************REPORTE DE INGRESOS A URGENCIAS POR ENTIDAD**************************************
 * 
 *  El reporte suministra una lista de los pacientes que en un rango de tiempo han ingresado a urgencias, discriminado por entidad.
 * 
 * Programa: rep_urgenciasPorEntidad.php
 * Autor: Juan Felipe Balcero Loaiza
 * Tipo de Script: Reporte
 * Ruta del script: matrix\movhos\reportes\rep_urgenciasPorEntidad.php
 * 
*/

$wautor = "Juan Felipe Balcero L.";
$wversion='2018-06-13';
$wactualiz='2018-06-14';
session_start();
if(!isset($_SESSION['user']))
{
    echo "error";
    return;
}   


 


header('Content-type: text/html;charset=ISO-8859-1');

if(!isset($wemp_pmla)){
    terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");		
}

$titulo = "REPORTE DE INGRESOS A URGENCIAS POR ENTIDAD";

    /****************************************Consulta AJAX************************************************/
    if(isset($_POST['accion']) && $_POST['accion'] == 'generarReporte')
    {   
        $entidad = str_replace(",","','",$entidad);       
        
        $registros = consultarDatosInicialesReporte($conex,$wbasedato,$wbasedatocliame,$wbasedatomovhos,$ccUrgencias,$entidad,$fechaInicial,$fechaFinal);
        $numregistros = mysql_num_rows($registros);

        //Sí la consulta arrojó algún resultado, se imprime la tabla con ellos
        if($numregistros > 0)
        {
            //Encabezado de la tabla

            $data .= '  <div class="numero-resultados">';
            $data .=        $numregistros." resultados.";
            $data .= '  </div>';
            $data .= "<div class='tabla-resultado centrado' >";
            $data .= '<table class="centrado">';
            $data .= '   <tr id="encabezado">';
            $data .= '        <th width="100px">';
            $data .= '            Fecha de Ingreso';
            $data .= '        </th>';
            $data .= '        <th width="100px">';
            $data .= '            Mes de Ingreso';
            $data .= '        </th>';
            $data .= '        <th width="100px">';
            $data .= '            Historia e Ingreso';
            $data .= '        </th>';
            $data .= '        <th width="320px">';
            $data .= '            Nombre(s)';
            $data .= '        </th>';
            $data .= '        <th width="320px">';
            $data .= '            Apellido(s)';
            $data .= '        </th>';
            $data .= '        <th width="320px">';
            $data .= '            Entidad';
            $data .= '        </th>';
            $data .= '        <th width="140px">';
            $data .= '            Identificación';
            $data .= '        </th>';
            $data .= '        <th width="100px">';
            $data .= '            Fecha de nacimiento';
            $data .= '        </th>';
            $data .= '        <th width="100px">';
            $data .= '            Edad';
            $data .= '        </th>';
            $data .= '        <th width="100px">';
            $data .= '            Nivel Triage';
            $data .= '        </th>';
            $data .= '        <th width="100px">';
            $data .= '            Fecha y hora del Triage';
            $data .= '        </th>';
            $data .= '        <th width="100px">';
            $data .= '            Fecha y hora de la atención';
            $data .= '        </th>';
            $data .= '        <th width="100px">';
            $data .= '            Tiempo de espera';
            $data .= '        </th>';
            $data .= '        <th width="100px">';
            $data .= '            Fecha y hora de alta urgencias';
            $data .= '        </th>';
            $data .= '        <th width="100px">';
            $data .= '            Tiempo estancia urgencias';
            $data .= '        </th>';
            $data .= '        <th width="300px">';
            $data .= '            Diagnostico';
            $data .= '        </th>';
            $data .= '        <th width="100px">';
            $data .= '            Hospitalario';
            $data .= '        </th>';
            $data .= '        <th width="100px">';
            $data .= '            Fecha y hora solicitud hospitalización';
            $data .= '        </th>';
            $data .= '        <th width="100px">';
            $data .= '            Hora asignación de cama';
            $data .= '        </th>';
            $data .= '        <th width="100px">';
            $data .= '            Fecha y hora llegada al piso';
            $data .= '        </th>';
            $data .= '    </tr>';

            //Información para exportar a excel
            $dataExcel = "  <thead><tr> ";
            $dataExcel .="      <th>Fecha_ingreso</th> ";
            $dataExcel .="      <th>Mes_ingreso</th> ";
            $dataExcel .="      <th>Historia_ingreso</th> ";
            $dataExcel .="      <th>Nombres</th> ";
            $dataExcel .="      <th>Apellidos</th> ";
            $dataExcel .="      <th>Entidad</th> ";
            $dataExcel .="      <th>Identificacion</th> ";
            $dataExcel .="      <th>Fecha_nacimiento</th> ";
            $dataExcel .="      <th>Edad</th> ";
            $dataExcel .="      <th>Nivel_triage</th> ";
            $dataExcel .="      <th>Hora_triage</th> ";
            $dataExcel .="      <th>Hora_atencion</th> ";
            $dataExcel .="      <th>Espera_atencion</th> ";
            $dataExcel .="      <th>Hora_alta_urgencias</th> ";
            $dataExcel .="      <th>Estancia_urgencia</th> ";
            $dataExcel .="      <th>Diagnostico</th> ";
            $dataExcel .="      <th>Hospitalario</th> ";
            $dataExcel .="      <th>Hora_solicitud_hospitalizacion</th> ";
            $dataExcel .="      <th>Hora_asignacion_cama</th> ";
            $dataExcel .="      <th>Hora_llegada_piso</th> ";
            $dataExcel .="      </tr></thead> ";
            $dataExcel .="      <tbody> ";

            //Recorro los registros de la consulta
            //Cuerpo de la tabla

            while($registro = mysql_fetch_row($registros))
            {
                $fechaIngreso =     $registro[0];
                $historia =         $registro[1];
                $ingreso =          $registro[2];
                $nombre =           utf8_encode($registro[3])." ".utf8_encode($registro[4]);
                $apellido =         utf8_encode($registro[5])." ".utf8_encode($registro[6]);
                $tipoDocumento =    $registro[7];
                $documento =        $registro[8];
                $nacimiento =       $registro[9];
                $triage =           $registro[10];
                $fechaTriage =      $registro[11];
                $fechaAtencion =    $registro[12];
                $horaAtencion =     $registro[13];
                $fechaAlta =        $registro[14];
                $horaAlta =         $registro[15];
                $codDiagnostico =   $registro[16];
                $diagnostico =      utf8_encode($registro[17]);
                $hospitalario =     utf8_encode($registro[18]);
                $empresa =          utf8_encode($registro[19]);

                $data .= "<tr>";
                $data .= '  <td title="Fecha de ingreso">'.$fechaIngreso."</td>";
                $data .= '  <td title="Mes de ingreso">'.date_format(date_create($fechaIngreso),"m")."</td>";
                $data .= '  <td title="Historia e ingreso">'.$historia."-".$ingreso."</td>";
                $data .= "  <td style='text-align: left;'>".$nombre."</td>";
                $data .= "  <td style='text-align: left;'>".$apellido."</td>";
                $data .= "  <td style='text-align: left;'>".$empresa."</td>";
                $data .= "  <td style='text-align: left;'>".$tipoDocumento." - ".$documento."</td>";
                $data .= "  <td title='Fecha de nacimiento'>".$nacimiento."</td>";                
                $data .= "  <td title='Edad'>".edad($nacimiento)."</td>";    //Se calcula la edad a partir de la fecha de nacimiento
                $data .= "  <td title='Nivel de Triage'>".$triage."</td>";

                $dataExcel .="<tr> ";
                $dataExcel .= '  <td>'.$fechaIngreso."</td>";
                $dataExcel .= '  <td>'.date_format(date_create($fechaIngreso),"m")."</td>";
                $dataExcel .= '  <td>'.$historia."-".$ingreso."</td>";
                $dataExcel .= "  <td>".$nombre."</td>";
                $dataExcel .= "  <td>".$apellido."</td>";
                $dataExcel .= "  <td>".$empresa."</td>";
                $dataExcel .= "  <td>".$tipoDocumento." - ".$documento."</td>";
                $dataExcel .= "  <td>".$nacimiento."</td>";                
                $dataExcel .= "  <td>".edad($nacimiento)."</td>";    //Se calcula la edad a partir de la fecha de nacimiento
                $dataExcel .= "  <td>".$triage."</td>";

                //Si se realizó el triage antes de la consulta, se procede normalmente
                if($fechaTriage != NULL)
                {
                    $data .= "  <td title='Fecha del triage'>".$fechaTriage."</td>";
                    $data .= "  <td title='Fecha de la atencion'>".$fechaAtencion." ".$horaAtencion."</td>";
                    $data .= "  <td title='Espera para la atencion'>".date_diff(date_create($fechaTriage), date_create($fechaAtencion." ".$horaAtencion))->format('%H:%I:%S'). "\n"."</td>";

                    $dataExcel .= "  <td>".$fechaTriage."</td>";
                    $dataExcel .= "  <td>".$fechaAtencion." ".$horaAtencion."</td>";
                    $dataExcel .= "  <td>".date_diff(date_create($fechaTriage), date_create($fechaAtencion." ".$horaAtencion))->format('%H:%I:%S'). "\n"."</td>";
                }                
                else //De lo contrario se hizo en la consulta y se le asigna el mismo tiempo de la consulta
                {
                    $data .= "  <td title='Fecha del triage'>".$fechaAtencion." ".$horaAtencion."</td>";
                    $data .= "  <td title='Fecha de la atencion'>".$fechaAtencion." ".$horaAtencion."</td>";
                    $data .= "  <td title='Espera para la atencion'>".date_diff(date_create($fechaAtencion." ".$horaAtencion), date_create($fechaAtencion." ".$horaAtencion))->format('%H:%I:%S'). "\n"."</td>";

                    $dataExcel .= "  <td>".$fechaAtencion." ".$horaAtencion."</td>";
                    $dataExcel .= "  <td>".$fechaAtencion." ".$horaAtencion."</td>";
                    $dataExcel .= "  <td>".date_diff(date_create($fechaAtencion." ".$horaAtencion), date_create($fechaAtencion." ".$horaAtencion))->format('%H:%I:%S'). "\n"."</td>";
                }

                //Si el paciente fue hospitalizado, la hora de alta de urgencias se toma como la hora de entrega desde urgencias a la otra unidad
                if($hospitalario == 'on')
                {
                    $altasHospitalarias = consultarSalidaUrgencias($conex,$historia,$ingreso,$ccUrgencias,$wbasedatomovhos);
                    $datos = mysql_fetch_row($altasHospitalarias);

                    $data .= "  <td title='Fecha de alta de urgencias'>".$datos[0]." ".$datos[1]."</td>";
                    $data .= "  <td title='Estancia en urgencias'>".date_diff(date_create($fechaAtencion." ".$horaAtencion), date_create($datos[0]." ".$datos[1]))->format('%H:%I:%S'). "\n"."</td>";

                    $dataExcel .= "  <td>".$datos[0]." ".$datos[1]."</td>";
                    $dataExcel .= "  <td>".date_diff(date_create($fechaAtencion." ".$horaAtencion), date_create($datos[0]." ".$datos[1]))->format('%H:%I:%S'). "\n"."</td>";
                }
                else
                {
                    $data .= "  <td title='Fecha de alta de urgencias'>".$fechaAlta." ".$horaAlta."</td>";
                    $data .= "  <td title='Estancia en urgencias'>".date_diff(date_create($fechaAtencion." ".$horaAtencion), date_create($fechaAlta." ".$horaAlta))->format('%H:%I:%S'). "\n"."</td>";

                    $dataExcel .= "  <td>".$fechaAlta." ".$horaAlta."</td>";
                    $dataExcel .= "  <td>".date_diff(date_create($fechaAtencion." ".$horaAtencion), date_create($fechaAlta." ".$horaAlta))->format('%H:%I:%S'). "\n"."</td>";
                }
                
                $data .= "  <td>".$codDiagnostico." - ".$diagnostico."</td>"; 
                
                $dataExcel .= "  <td>".$codDiagnostico." - ".$diagnostico."</td>";

                //En caso de que el paciente haya sido hospitalizado, se hace una consulta adicional sobre los datos de su translado a una cama
                if($hospitalario == 'on')
                {
                    
                    $registrosHospitalizados = consultarDatosAdicionales($conex,$historia,$ingreso,$wbasedatomovhos, $wbasedatocencam, $ccUrgencias);
                    $row = mysql_fetch_row($registrosHospitalizados);
                    
                    $fechaLlegadaPiso = $row[0];
                    $horaLlegadaPiso = $row[1];
                    $fechaSolicitud = $row[2];
                    $horaSolicitud = $row[3];
                    $fechaAsignacion = $row[4];
                    $horaAsignacion = $row[5];  

                    $data .= "  <td title='¿Hospitalario?'> Si </td>";    
                    $data .= "  <td title='Fecha de solicitud hospitalizacion'>".$fechaSolicitud." ".$horaSolicitud."</td>";
                    $data .= "  <td title='Fecha de asignacion de cama'>".$fechaAsignacion." ".$horaAsignacion."</td>";
                    $data .= "  <td title='Fecha de llegada a piso'>".$fechaLlegadaPiso." ".$horaLlegadaPiso."</td>";

                    $dataExcel .= "  <td> Si </td>";    
                    $dataExcel .= "  <td>".$fechaSolicitud." ".$horaSolicitud."</td>";
                    $dataExcel .= "  <td>".$fechaAsignacion." ".$horaAsignacion."</td>";
                    $dataExcel .= "  <td>".$fechaLlegadaPiso." ".$horaLlegadaPiso."</td>";
                }
                else
                {
                    $data .= "  <td title='¿Hospitalario?'> No </td>"; 
                    $data .= "  <td title='Fecha de solicitud hospitalizacion'>-</td>";
                    $data .= "  <td title='Fecha de asignacion de cama'>-</td>";
                    $data .= "  <td title='Fecha de llegada a piso'>-</td>";

                    $dataExcel .= "  <td> No </td>"; 
                    $dataExcel .= "  <td>-</td>";
                    $dataExcel .= "  <td>-</td>";
                    $dataExcel .= "  <td>-</td>";
                }
                
                $data .= "</tr>";
                $dataExcel .= "</tr>";
            }
            $dataExcel .= "</tbody>";                                 
        }
        else
        {
            $data = 'N';            
        }
        $respuesta['tabla'] = $data;
        $respuesta['excel'] = $dataExcel;
        
        echo json_encode($respuesta);

        return;
                
    }

    /****************************************Funciones PHP************************************************/
    function consultarSalidaUrgencias($conex,$historia,$ingreso,$ccUrgencias,$wbasedatomovhos)
    {
        //En caso de que un paciente pase de urgencias a hospitalización, la hora de alta de urgencias será la hora de entrega. Esta función consulta la fecha y hora de la entrega para un determinado paciente en un ingreso a urgencias

        $sql = "    SELECT Fecha_data, Hora_data ";
        $sql .= "   FROM ".$wbasedatomovhos."_000017 ";
        $sql .= "   WHERE Eyrhis = '".$historia."' ";
        $sql .= "   AND Eyring ='".$ingreso."' ";
        $sql .= "   AND Eyrsor = '".$ccUrgencias."' "; 
        $sql .= "   AND Eyrtip = 'Entrega' ";
        
        $respuesta = mysql_query($sql, $conex) or die(mysql_errno()." - en el query: ".$sql." - ".mysql_error());
        return $respuesta;
    }

    function edad($fecha){
        $fecha = str_replace("/","-",$fecha);
        $fecha = date('Y/m/d',strtotime($fecha));
        $hoy = date('Y/m/d');
        $edad = $hoy - $fecha;
        return $edad;
    }

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

    function consultarCCUrgencias($conex, $wbasedatomovhos)
    {
        //Consulta el código de centro de costos de urgencias, se hace esta consulta una vez y se guarda en una variable, para no tener que hacer una sub consulta en la consulta principal

        $sql1 = " SELECT Ccocod ";
        $sql1 .= " FROM `".$wbasedatomovhos."_000011` ";
        $sql1 .= " WHERE Ccourg = 'on' ";
        $res = mysql_query($sql1, $conex) or die(mysql_errno()." - en el query: ".$sql1." - ".mysql_error());
        $row = mysql_fetch_row($res);
        $ccUrgencias = $row[0];

        return $ccUrgencias;
        
    }

    function consultarDatosInicialesReporte($conex,$wbasedato,$wbasedatocliame,$wbasedatomovhos,$ccUrgencias,$entidad,$fechaInicial,$fechaFinal)
    {
        //Función de consulta principal para el reporte, carga todos los datos para pacientes que entran por urgencias, pero no son hospitalizados. 
        
        global $conex;

        $sql = "    SELECT A.Fecha_data, Mtrhis, Mtring, Pacno1, Pacno2, Pacap1, Pacap2, Pactdo, Pacdoc, Pacfna, Mtrtri, Atufit, Mtrfco, Mtrhco, Ubifad, Ubihad, Ingdig, Descripcion, Ccohos, Empnom ";
        $sql .= "   FROM `".$wbasedato."_000022` A ";
        $sql .= "   INNER JOIN `".$wbasedatocliame."_000101` B ON A.Mtrhis = B.Inghis 
                    AND A.Mtring = B.Ingnin AND B.Ingcem IN ('".$entidad."') "; 
        $sql .= "   INNER JOIN `".$wbasedatocliame."_000100` C ON A.Mtrhis = C.Pachis 
                    AND Mtrcci = '".$ccUrgencias."' ";
        $sql .= "   INNER JOIN `".$wbasedatomovhos."_000018` D ON A.Mtrhis = D.Ubihis AND A.Mtring = D.Ubiing ";
        $sql .= "   LEFT JOIN `".$wbasedatomovhos."_000178` E ON A.Mtrtur = E.Atutur ";
        $sql .= "   INNER JOIN `root_000011` F ON B.Ingdig = F.Codigo
                    INNER JOIN `".$wbasedatomovhos."_000011` G ON D.Ubisac = G.Ccocod ";
        $sql .= "   INNER JOIN `".$wbasedatocliame."_000024` H ON B.Ingcem = H.Empcod ";
        $sql .= "   WHERE A.Fecha_data BETWEEN '".$fechaInicial."' AND '".$fechaFinal."'; ";

       
        $respuesta = mysql_query($sql, $conex) or die(mysql_errno()." - en el query: ".$sql." - ".mysql_error());
        //$numregistros = mysql_num_rows($respuesta);

        return $respuesta;
    }
    
    function consultarDatosAdicionales($conex,$historia,$ingreso,$wbasedatomovhos,$wbasedatocencam,$ccUrgencias)
    {   
        //En caso de que un paciente sea hospitalizado luego de entrar por urgencias, se hace esta consulta de información adicional.

        $sql = "    SELECT A.Fecha_data, A.Hora_data, C.Fecha_data,                    C.Hora_data, Fec_asigcama, Hora_asigcama ";
        $sql .= "   FROM ".$wbasedatomovhos."_000017 A ";
        $sql .= "   INNER JOIN `".$wbasedatomovhos."_000011` B ON A.Eyrsde             = B.Ccocod 
                    AND Ccohos = 'on' ";
        $sql .= "   INNER JOIN `".$wbasedatocencam."_000003` C ON A.Eyrhde             = C.Hab_asignada AND A.Eyrhis = C.Historia ";
        $sql .= "   WHERE Eyrhis = '".$historia."' ";
        $sql .= "   AND Eyrsor = '".$ccUrgencias."' ";
        $sql .= "   AND Eyring = '".$ingreso."' ";
        $sql .= "   AND Eyrtip = 'recibo' ";
        $sql .= "   AND Motivo = 'SOLICITUD DE CAMA';";

        $respuesta = mysql_query($sql, $conex) or die(mysql_errno()." - en el query: ".$sql." - ".mysql_error());

        return $respuesta;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>    
    <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>INGRESOS URGENCIAS POR ENTIDAD</title>
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
            width: 66%;
            float: left;
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
        tr:nth-child(even){
            background-color: #C3D9FF;
            text-transform : uppercase;
        }
        tr:nth-child(odd){
            background-color: #E8EEF7;
            text-transform : uppercase;
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
        
        .tabla-resultado td,
        .tabla-resultado th{
            text-align: center;
            border: #ffffff 1px solid;            
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
        }
        .ui-multiselect { background:white; background-color:white; color: black; font-weight: normal; border-color: black; border: 2px; height:20px; width:450px; overflow-x:auto; text-align:left;font-size: 10pt;border-radius: 1px; overflow-y:auto;}

        .ui-multiselect-menu { background:white; background-color:white; color: black;}

        .ui-multiselect-header { background:white; background-color:lightgray; color: black;}

        .fijo{
            position: fixed;
            top : 0;
        }

        #seleccionados{
            height : 130px;
            width : 30%;
            padding-left : 5px;
            text-transform : uppercase;
        }
        textarea{
            resize: none;
        }
    </style>
    
    <script src="../../../include/gentelella/vendors/jquery/dist/jquery.min.js" ></script>
    <script src="../../../include/root/bootstrap.min.js"></script>
    <script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>    
    <script type="text/javascript" src="../../../include/root/jquery.multiselect.js"></script>
    <script type="text/javascript" src="../../../include/root/jquery.multiselect.filter.js"></script>
    <script type="text/javascript">                
        $(function () {
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
          });

            $('#entidad').multiselect({
                     numberDisplayed: 1,
                     multiple:true,
                     selectedText: '# seleccionados'
          }).multiselectfilter();
            
            $("#entidad").on("multiselectclose", function(event, ui){
                var escogidos = $('#entidad').val();                
                if(escogidos != null)
                {
                    var elegidos = escogidos.toString();
                    var newchar = '&#10;- ';
                    var aux = elegidos.split(',');

                    for(var i = 0; i < aux.length; i++)
                    {
                        aux2=aux[i].split('-');
                        aux[i] = aux2[aux2.length - 1];                    
                    }
                    elegidos = aux.join(newchar);
                    $('#seleccionados').show();
                    $('#seleccionados').html("Entidades seleccionadas:&#10;- " + elegidos);
                }
                else
                {
                    $('#seleccionados').hide();
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
                
        });

        function Consultar(){

            var fechaInicial    = $('#txtfecini').val();
            var fechaFinal      = $('#txtfecfin').val();

            //Se revisa que esté asignadas las fechas 
            if(fechaInicial == '' || fechaFinal == '')
            {
                $('#errorFechas').modal();
                return;
            }

            var entidad         = $('#entidad').val();

            if(entidad == null)
            {
                $('#errorEntidad').modal();
                return;
            }
            else
            {
                entidad = entidad.toString();
            }
            

            var newchar = ',';
            var aux = entidad.split(',');

            for(var i = 0; i < aux.length; i++)
            {
                aux2=aux[i].split('-');
                aux2.splice(aux2.length - 1, 1);
                aux[i] = aux2.join('-');
            }

            entidad = aux.join(newchar);
            var wbasedato       = $('#wbasedato').val();
            var wbasedatomovhos = $('#wbasedatomovhos').val();
            var wbasedatocliame = $('#wbasedatocliame').val();
            var wbasedatocencam = $('#wbasedatocencam').val();
            var ccUrgencias     = $('#ccUrgencias').val();           

            
            document.getElementById("divcargando").style.display   = "";

            $.post("rep_urgenciasPorEntidad.php",
                {
                    consultaAjax    :   true,
                    accion          :   'generarReporte',
                    fechaInicial    :   fechaInicial,
                    fechaFinal      :   fechaFinal,                      
                    entidad         :   entidad,
                    wbasedato       :   wbasedato,
                    wbasedatomovhos :   wbasedatomovhos,
                    wbasedatocliame :   wbasedatocliame,
                    wbasedatocencam :   wbasedatocencam,
                    ccUrgencias     :   ccUrgencias,
                    wemp_pmla       :   $("#wemp_pmla").val(),

                },function(data)
                {                    
                    document.getElementById("divcargando").style.display    = "none";
                    if(data.tabla != 'N')
                    {
                        $('#resultados').html(data.tabla);
                        $('#tablaExcel').html(data.excel);
                        $('#exportar').prop('disabled', false);                        
                    }
                    else
                    {                        
                        $('#resultados').html("No se encontraron resultados");
                        $('#exportar').prop('disabled', true);
                    }
                    
                },"json");
        }

        function Exportar(){
     
            //Creamos un Elemento Temporal en forma de enlace
            var tmpElemento = document.createElement('a');
            var data_type = 'data:application/vnd.ms-excel'; //Formato anterior xls

            // Obtenemos la información de la tabla
            var tabla_div = document.getElementById('tablaExcel');
            var tabla_html = tabla_div.outerHTML.replace(/ /g, '%20');
            
            tmpElemento.href = data_type + ', ' + tabla_html;
            //Asignamos el nombre a nuestro EXCEL
            tmpElemento.download = 'reporte_urgencias_por_entidad.xls';
            // Simulamos el click al elemento creado para descargarlo
            tmpElemento.click();

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

            $entidades = consultarEntidades($conex, $wbasedatocliame);
            $ccUrgencias = consultarCCUrgencias($conex, $wbasedatomovhos);

            //Hidden inputs para guardar los alias de la base, para poder obtenerlos en el llamado AJAX

            echo '<input type="hidden" id="wbasedato" value="'.$wbasedato.'">';
            echo '<input type="hidden" id="wbasedatomovhos" value="'.$wbasedatomovhos.'">';
            echo '<input type="hidden" id="wbasedatocliame" value="'.$wbasedatocliame.'">';
            echo '<input type="hidden" id="wbasedatocencam" value="'.$wbasedatocencam.'">';
            echo '<input type="hidden" id="ccUrgencias" value="'.$ccUrgencias.'">';
            echo '<input type="hidden" id="wemp_pmla" value="'.$wemp_pmla.'">';
        
    ?>
    
    <div class="contenido"> 
        <div class="row">
            <div class="well">
                <form action="rep_urgenciasPorEntidad.php">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="row" style="padding: 5px">
                                Fecha inicial: 
                                <input type='text' id='txtfecini' name='txtfecini' size='15' readonly>
                                Fecha final: 
                                <input type='text' id='txtfecfin' name='txtfecfin' size='15' readonly>
                            </div>
                            
                            <div style="padding: 5px">
                                Entidad: 
                                <select class="selectpicker" data-live-search="true" data-size="8" multiple style="position: static; margin-bottom: 5px; *width: 180px" id="entidad">
                                    <?php
                                        foreach($entidades as $key => $value)
                                        {
                                            echo strtoupper(utf8_decode('<option value="'.$key.'-'.$value.'">'.$key.' - '.$value.'</option>'));
                                        }
                                    ?>                            
                                </select> 
                            </div>                          
                        </div>
                        <input type="button" class="centrado" id="consultar" onclick='Consultar()' value="Buscar">
                        <input type="button" class="centrado" id="exportar" onclick='Exportar()' value="Exportar" disabled>
                        <input type="button" id="cancel_edit" value="Cerrar"></input>
                    </div>                
                </form>            
            </div><!-- Cierre del well-->

            <textarea id="seleccionados" readonly style="display: none"></textarea>
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
            <div class="modal fade" id="errorEntidad" role="dialog">
                <div class="modal-dialog">    
                <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Por favor ingrese el la(s) entidad(es) a consultar.</h4>
                        </div>
                    </div>
      
                </div>
            </div><!-- Cierre del modal-->        
    </div><!-- Cierre del contenido-->
    <div id="divcargando" name="divcargando" style='display:none;' ><center><img width="26" height="26" border="0" src="../../images/medical/ajax-loader9.gif"></center></div>   
    <div class="resultados" id="resultados"></div>
    <table id="tablaExcel" style='display:none;'></table>       
</body>
</html>