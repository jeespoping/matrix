<?php 

/** ===========================================================================================|
 * TODO: WEBSERVICE PARA ACTUALIZACION DE URL PARA LABORATORIO EN MATRIX
 * ============================================================================================|
 * * REPORTE					:	ACTUALIZACION URL MATRIX X LABORATORIO
 * * AUTOR						:	Ing. Jaime David Mejia Quintero
 * * FECHA CREACIÓN				:	2021-10-01
 * * FECHA ULTIMA ACTUALIZACIÓN	:	2021-10-01
 * * DESCRIPCIÓN				:	Obtiene un json con la url antigua y actualizada:
 * * 									- estado.
 * * 									- urlAntigua.
 * * 									- urlNueva.

 * ============================================================================================|
 * TODO: ACTUALIZACIONES
 * ============================================================================================|
 * . @update [2021-05-28]	-	Se crea inicialmente el script para la actualizacion
 */ 

/** Se inicializa el bufer de salida de php **/
ob_start();

/*
* Includes
*/

include_once("conex.php");
include_once("root/comun.php");

ob_end_clean();
$conex = obtenerConexionBD("matrix");
$whce =  consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

function actualizarUrlsLaboratorioXMatrix($conex = null, $wemp_pmla, $jsonData){
    $patientList = json_decode($jsonData, true);
    $count = count($patientList);

    for ($i = 0; $i < $count; $i++) {

        $historia = $patientList[$i]["historia"];
        $ingreso =  $patientList[$i]["ingreso"];

        ConsultaLaboratorio($conex,$wemp_pmla,$historia,$ingreso);
    }
}

function ConsultaLaboratorio($conex, 
    $wemp_pmla, 
    $historia, 
    $ingreso){

    global $whce;
    global $wbasedato;

    /* Se hace un primer Query para sacar todos los estados de las ordenes en la MOVHOS_000045
    Para buscar todas las ordenes diferentes de R(realizado) que pueden exisitir y armar un HAVING
    Con esos estados*/
    $sacarEstadosDeLaboratorio = "SELECT Eexcod
    FROM " . $wbasedato . "_000045 o
    WHERE Eexcas = 'on'";

    $resEstadosDeLaboratorio = mysql_query($sacarEstadosDeLaboratorio, $conex) or die("Error: " . mysql_errno() . 
    " - en el query: " . $resEstadosDeLaboratorio . " - " . mysql_error());
    $numEstadosDeLaboratorio = mysql_num_rows($resEstadosDeLaboratorio);    

    $having = '';

    if ($numEstadosDeLaboratorio> 0) {
        $having = 'having(';
        while( $row = mysql_fetch_array($resEstadosDeLaboratorio)){
            $having .= 'NOT FIND_IN_SET("'.$row['Eexcod'].'",estados) AND ';         
        }   

        $having = substr($having, 0, -5);
        $having .= ')'; 
    }
        
    /* Query para traer desde la HCE_000027 La historia,ingreso y el numero de ordenes
    En la HCE_000028 se procesa cada orden y se trae la URL Respectiva*/

    $urlLaboratorioMatrix = "SELECT DISTINCT Deturp,Detotr FROM(
    SELECT Detotr,GROUP_CONCAT(DISTINCT od.Deturp) Deturp, GROUP_CONCAT(od.Detesi) estados        
    FROM " . $whce . "_000027 o
    INNER JOIN " . $whce . "_000028 od ON o.Ordnro = od.Detnro
    WHERE Ordhis = $historia
    AND Ording = $ingreso
    AND od.Detesi <> 'C'
    AND Deturp <> ''
    GROUP BY od.Detotr
    $having) as subSql;";

    
    /*$urlLaboratorioMatrix = "SELECT *    
        FROM " . $whce . "_000027 o
        INNER JOIN " . $whce . "_000028 od ON o.Ordnro = od.Detnro
        WHERE Ordhis = $historia
        AND Ording = $ingreso
        AND od.Detesi <> 'C'
        AND Deturp <> ''
        GROUP BY od.Detotr;";*/

    //echo $urlLaboratorioMatrix;

    $resUrlLaboratorioMatrix = mysql_query($urlLaboratorioMatrix, $conex) or die("Error: " . mysql_errno() . 
    " - en el query: " . $urlLaboratorioMatrix . " - " . mysql_error());
    $numUrlACambiar = mysql_num_rows($resUrlLaboratorioMatrix);
    
    if ($numUrlACambiar > 0) {
        $ContadorActualizarUrl = 0;
        while ($rowsPrescripciones = mysql_fetch_array($resUrlLaboratorioMatrix)){

            $urlARevisar = $rowsPrescripciones['Deturp'];
            $numeroDeOrdenOTR = $rowsPrescripciones['Detotr'];
            //Se verifica que la URL venga actualizada con el .pdf o si viene sin el .pdf se manda a actualizar
            $verificarURL = explode(",",$urlARevisar);
            foreach($verificarURL as $resultadoLaboratorio){
                $verificarExtensionPDF = substr($resultadoLaboratorio,-4);

                if($verificarExtensionPDF !== '.pdf'){
                    // Crear un manejador cURL
                    $urlAmazon = consultarAliasPorAplicacion($conex,$wemp_pmla,'urlActualizarLaboratorio');
                    $resultadoCortado = recortarUrl($urlAmazon,$resultadoLaboratorio);

                    $url = 'http://131.1.18.156/encuestas_/scriptmatrix/hojalaboratorio.php?actualizarUrl=descarga'.'&urlMatrix='.
                    $resultadoCortado.'&urlAmazon='.$urlAmazon.'&numeroDeOrden='.$numeroDeOrdenOTR;
                                        
                    $ch = curl_init();
                    if (!$ch) {
                        die("Couldn't initialize a cURL handle");
                    }
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HEADER, 0);

                    $ingresarAHojaLaboratorio = curl_exec($ch);

                    //trae el resultado en json de la hoja de laboratorio
                    $resultadoDecodificado = json_decode($ingresarAHojaLaboratorio);
                    $resultadoUrlLaboratorio = $resultadoDecodificado->urlCompleta;
                    $resultadoNumeroDeOrden = $resultadoDecodificado->numeroDeOrden;
                    $estado = $resultadoDecodificado->state;
                    
                    curl_close($ch); // close cURL handler       
                    if($estado == 200){
                        //Se hace una cadena para acceder mas rapido al resultado
                        $likeResultadoLaboratorio = '%'.$resultadoLaboratorio.'%';

                        //Se hace el update a la tabla de matrix donde se tiene la vieja url por la nueva
                        if(!empty($resultadoUrlLaboratorio) && !empty($likeResultadoLaboratorio)){
                            $actualizarURLEnMatrix = "UPDATE " . $whce . "_000028
                            SET Deturp = '$resultadoUrlLaboratorio'
                            WHERE Deturp LIKE '$likeResultadoLaboratorio'
                            AND Detotr = '$resultadoNumeroDeOrden';";
            
                            $ActualizarUrlQuery = mysql_query($actualizarURLEnMatrix,$conex) or die("Error: " . mysql_errno() . 
                            " - en el query: " . $ActualizarUrlQuery . " - " . mysql_error());  
                            
                            $ContadorActualizarUrl += 1;

                        }
                    }    
                }                 
            }
        }
        if($ContadorActualizarUrl > 0){
            $respuestaLaboratorio['status'] = 200;
            $respuestaLaboratorio['result'] = 'Se ha actualizado las URL correctamente';
        }
        else{
            $respuestaLaboratorio['status'] = 400;
            $respuestaLaboratorio['result'] = 'Las Url ya se encuentran actualizadas,No hay necesidad de actualizar';
        }
    }
    else{
        $respuestaLaboratorio['status'] = 400;
        $respuestaLaboratorio['result'] = 'No hay registros con URL para actualizar';
    }
    return $respuestaLaboratorio;
    }

    function recortarUrl($urlAmazon,$urlMatrix){
        if (!is_bool(strpos($urlMatrix, $urlAmazon)))
        return substr($urlMatrix, strpos($urlMatrix,$urlAmazon)+strlen($urlAmazon));
    };

    // Se valida si la acción fue enviada por GET o por POST para las pruebas
    if (isset($_REQUEST['accionGet'])){
        $arrayRespuesta = ConsultaLaboratorio($conex,$_GET['wemp_pmla'],$_GET['historia'],$_GET['ingreso']);
    }
    else if(isset($_REQUEST['accionPost'])){
        $arrayRespuesta = actualizarUrlsLaboratorioXMatrix($conex, $_GET['wemp_pmla'], $_POST['patients']);        
    }

    // Respuesta codificada en Javascript 
    header('Content-type: text/javascript');
    echo json_encode($arrayRespuesta, JSON_PRETTY_PRINT);

?>