<?php
/** Se inicializa el bufer de salida de php **/
ob_start();
include_once("conex.php");        //publicacion Matrix
include_once("root/comun.php");   //publicacion Matrix
include_once("trazFunctions.php");
/** Limpiamos el buffer de salida de php para no retornar los datos de los "echos" que se hacen en los include **/
ob_end_clean();
$conex = obtenerConexionBD("matrix");

$fecha_Actual = date('Y-m-d');  $hora_Actual = date('H:m:s');
//La variable accion es la que divide las tareas.
$accion = $_GET['accion'];
if($accion == null){$accion = $_POST['accion'];}
$idRegistro = $_GET['idRegistro'];  $Coddispo = $_GET['Coddispo'];  $idReuso = $_GET['idReuso'];    if($idReuso == null){$idReuso = $_POST['idReuso'];}
if (isset($idRegistro))$idRegistro = trim($idRegistro);
//VARIABLES ACTUALIZAR REGISTRO
$trazUpdate = $_POST['trazUpdate']; $numCalibreUp = $_POST['numCalibre'];   $codItemUp = $_POST['codItem']; $invimaUp = $_POST['invima'];
$limiteUp = $_POST['limite'];       $observacionUp = $_POST['observacion'];
//VARIABLES INSERTAR REUSO
$codReusoIns = $_POST['codReusoIns'];   $numCalIns = $_POST['numCalIns'];           $codItemIns = $_POST['codItemIns'];     $invimaIns = $_POST['invimaIns'];
$numReusoIns = $_POST['numReusoIns'];   $obserReusoIns = $_POST['obserReusoIns'];   $codDispoReu = $_POST['codDispoReu'];
$idDivNumCod = $_POST['idDivNumCod'];
//VARIABLES INSERTAR DISPOSITIVO
$idCco = $_GET['idCco'];
$codDispoInsert = $_POST['codDispoInsert']; $cCostDispoInsert = $_POST['codCcoDispo']; $descDispoInsert = $_POST['descDispoInsert'];
$codCcoDispo = $_GET['codCcoDispo']; if($codCcoDispo == null){$codCcoDispo = $_POST['codCcoDispo'];}
$numCalDispoInsert = $_POST['numCalDispoInsert'];   $codItemInsert = $_POST['codItemInsert'];   $invDispoInsert = $_POST['invDispoInsert'];
$limReuDispoInsert = $_POST['limReuDispoInsert'];
//VER REUSO EN REPORTES
$codReuso13 = $_GET['codReuso13'];
//EXPORTAR A EXCEL
$fecIniReport = $_GET['fecIni'];    $fecFinReport = $_GET['fecFin'];  $cCostoReport = $_REQUEST['codCcoDispo'];
$Coddispo = $_REQUEST['Coddispo'];
// MAESTRO DE USUARIOS
$codUsuario = '';
if (isset($_GET['codusu'])) $codUsuario = $_GET['codusu'];
elseif (isset($_POST['codusu'])) $codUsuario = $_POST['codusu'];
if (isset($_GET['wemp_pmla'])) $wemp_pmla = $_GET['wemp_pmla'];
elseif (isset($_POST['wemp_pmla'])) $wemp_pmla = $_POST['wemp_pmla'];
$userStateUpdate = $_REQUEST['userStateUpdate'];
$userPriorUpdate = $_REQUEST['userPriorUpdate'];
$userStateAdd = $_REQUEST['userStateAdd'];
$userPriorAdd = $_REQUEST['userPriorAdd'];
$userName = $_REQUEST['userName'];
$stateDispo = $_REQUEST['stateDispo'];
$idChangeState = $_REQUEST['idChangeState'];
$respuesta = array();
// MAESTRO DE CENTROS DE COSTOS
$ccoAdd = $_REQUEST['ccoAdd'];
$codCco = $_REQUEST['codCco'];
$bdCenest = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cenest');

// Si la accion es adicionar un usuario
if($accion == 'accionadd'){
    if (is_numeric($codUsuario)){
        $q = "SELECT Descripcion, Activo FROM usuarios WHERE Codigo = '{$codUsuario}' AND Empresa = '{$wemp_pmla}' LIMIT 1";
        $res = mysql_query($q, $conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $numRows = mysql_num_rows($res);
        if ($numRows>0){
            $caracteres = array( "á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","\\","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??","?£", "°");
            $caracteres2 = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","","","a","e","i","o","u","A","E","I","O","U","A","S","", "", "N", "N", "U", "");
            while( $datos = mysql_fetch_array( $res ) ){
                // Se realiza una validacion de caracteres con tildes
                $respuesta['datos']['nombre'] = str_replace( $caracteres, $caracteres2, utf8_encode($datos['Descripcion']));
                $nomTmp = $respuesta['datos']['nombre'];
                //Si el usuario esta activo, puede insertarlo en la tabla de usuarios cenest
                if ($datos['Activo'] == 'A'){
                    $sqlAdd = "INSERT INTO {$bdCenest}_000015 (Codigo,Descripcion,Prioridad,Grupo,Activo)
                                VALUES
                            ('{$codUsuario}','{$nomTmp}','{$userPriorAdd}','cenest','{$userStateAdd}');";
                    $resAdd = mysql_query( $sqlAdd, $conex ) or ($descripcion = utf8_encode(mysql_errno() . " - Error actualizando tabla - " . mysql_error()));
                    if (mysql_affected_rows($conex) > 0){
                        $respuesta['message'] = "Usuario: $nomTmp fue agregado con Exito";
                    }else{
                        $respuesta['error'] = "No se pudo insertar en la Base de Datos";
                    }
                }else{
                    $respuesta['error'] = "Usuario Inactivo";
                }
            }
        }else{
            $respuesta['error'] = "No existe en Matrix";
        }  
    }
    echo json_encode($respuesta,JSON_UNESCAPED_UNICODE);
}

// Si la accion es adicionar un nuevo centro de costos
if($accion == 'addNewCco'){
    if (is_numeric($ccoAdd)){
        $bdMovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
        $q = "SELECT Ccocod, Cconom, Ccocen FROM {$bdMovhos}_000011 WHERE Ccocod = '{$ccoAdd}'";
        $res = mysql_query($q, $conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $numRows = mysql_num_rows($res);
        // Si existe el Centro de costos
        if ($numRows>0){
            while( $datos = mysql_fetch_array( $res ) ){
                $respuesta['datos']['nombreCco'] = htmlentities($datos['Cconom']);
                $nomTmp = $respuesta['datos']['nombreCco'];
                // Se actualiza el campo Ccocen y se setea en on para que lo reconozca como usuario de la app cenest
                if ($datos['Ccocen'] != 'on'){
                    $sqlAdd = "UPDATE {$bdMovhos}_000011 SET Ccocen = 'on' WHERE Ccocod = '{$ccoAdd}';";
                    $resAdd = mysql_query( $sqlAdd, $conex ) or ($descripcion = utf8_encode(mysql_errno() . " - Error actualizando tabla - " . mysql_error()));
                    if (mysql_affected_rows($conex) > 0){
                        $respuesta['message'] = "El centro de costos: $nomTmp fue agregado con Exito";
                    }else{
                        $respuesta['error'] = "No se pudo agregar el centro de costos";
                    }
                }else{
                    $respuesta['error'] = "El centro de costos ya se encuentra incluido";
                }
            }
        }else{
            $respuesta['error'] = "No existe en Matrix";
        }
        
    }else{
        $respuesta['error'] = "EL centro de costos no es numerico";
    }
    echo json_encode($respuesta,JSON_UNESCAPED_UNICODE);
}

// Si la accion es actualizar los permisos de un usuario en la plataforma
if($accion == 'updateUser'){
    $sqlUpdate = "UPDATE {$bdCenest}_000015 SET Prioridad = '{$userPriorUpdate}' , Activo = '{$userStateUpdate}' WHERE Codigo = '{$codUsuario}';";
	$resUpdate = mysql_query( $sqlUpdate, $conex ) or ($descripcion = utf8_encode(mysql_errno() . " - Error actualizando tabla - " . mysql_error()));
	if (mysql_affected_rows($conex) > 0){
        $respuesta['datos']['codigo'] = $codUsuario;
        $respuesta['message'] = "El usuario con codigo {$codUsuario} fue actualizado con Exito";
    }else{
        $respuesta['error'] = "No se pudo actualizar el usuario";
    }
    echo json_encode($respuesta,JSON_UNESCAPED_UNICODE);
}

// Accion para borrar un usuario de la plataforma
if($accion == 'deleteUser'){
    $sqlDelete = "DELETE FROM {$bdCenest}_000015 WHERE codigo = '{$codUsuario}';";
	$resDelete = mysql_query( $sqlDelete, $conex) or ($descripcion = utf8_encode(mysql_errno() . " - Error Eliminando al usuario - " . mysql_error()));
	if (mysql_affected_rows($conex) > 0){
        $respuesta['datos']['codigo'] = $codUsuario;
        $respuesta['message'] = "El usuario con codigo {$codUsuario} fue eliminado con Exito";
    }else{
        $respuesta['error'] = "No se pudo eliminar el usuario";
    }
    echo json_encode($respuesta,JSON_UNESCAPED_UNICODE);
}

// Accion para borrar un centro de costos de la plataforma(se actualiza el campo ccocen en off)
if($accion == 'deleteCco'){
    $bdMovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
    $sqlDelete = "UPDATE {$bdMovhos}_000011
                    SET Ccocen  = 'off'
                    WHERE Ccocod = '$codCco'";
	$resDelete = mysql_query( $sqlDelete, $conex) or ($descripcion = utf8_encode(mysql_errno() . " - Error Eliminando al usuario - " . mysql_error()));
	if (mysql_affected_rows($conex) > 0){
        $respuesta['datos']['codigo'] = $codCco;
        $respuesta['message'] = "El centro de costos {$codCco} fue eliminado con Exito";
    }else{
        $respuesta['error'] = "No se pudo eliminar el centro de costos";
    }
    echo json_encode($respuesta,JSON_UNESCAPED_UNICODE);
}

//Accion que devuelve el nombre del dispositivo x Centros de costos
if($accion == 'nombreDispoXcod'){
    $query2 = "SELECT * FROM {$bdCenest}_000011 WHERE Codigo = '$Coddispo' AND Codcco = '$codCcoDispo'";
    $commit2 = mysql_query($query2, $conex) or die (mysql_errno()." - en el query: ".$query2." - ".mysql_error());
    $datos2 = mysql_fetch_array($commit2);
    $dispoNombre = $datos2['Descripcion'];
    echo json_encode($dispoNombre,JSON_UNESCAPED_UNICODE);
}

// Accion que devuelve el modal para modificar los reusos
if ($accion == 'modalModificarReuso'){
    
    $query2 = "SELECT * FROM {$bdCenest}_000011 WHERE Codigo = '$Coddispo' AND Codcco = '$codCcoDispo'";
    $commit2 = mysql_query($query2, $conex) or die (mysql_errno()." - en el query: ".$query2." - ".mysql_error());
    $datos2 = mysql_fetch_array($commit2);
    $datos2 = validateHtmlEntities($datos2);
    $dispoNombre = $datos2['Descripcion'];
    $query3 = "select * from {$bdCenest}_000012 WHERE id = '$idRegistro' AND Codcco = '$codCcoDispo'";
    $commit3 = mysql_query($query3, $conex) or die (mysql_errno()." - en el query: ".$query3." - ".mysql_error());
    $datos3 = mysql_fetch_array($commit3);
    $datos3 = validateHtmlEntities($datos3);
    $Codreuso = $datos3['Codreuso'];    $numCalibre = $datos3['Ncalibre'];      $codItem = $datos3['Coditem'];
    $invima = $datos3['Invima'];        $observacion = $datos3['Observacion'];  $limite = $datos3['limite'];
    $htmlreturn = '<div class="panel panel-info contenido" style="margin-left: 0">
    <div class="panel-heading encabezado">
        <div class="panel-title titulo1">Matrix - Modificacion de Registros Reuso</div>
        </div>
        <h4 style="text-align: center">'.$Codreuso.' - '.$dispoNombre .'</h4>
        <form method="post" action="trazProcess.php?wemp_pmla='.$wemp_pmla.'">
            <table align="center">
                <tr>
                    <td align="left">
                    <div class="input-group selectDispo" style="margin-left: 10px">
                        <span class="input-group-addon input-sm"><label for="numCalibre">NUMERO CALIBRE:</label></span>
                        <input type="text" id="numCalibre'.$idRegistro.'" name="numCalibre" class="form-control form-sm" style="width: 80px" value="'.$numCalibre.'">

                        <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 135px"></span>
                        <span class="input-group-addon input-sm"><label for="codItem">&ensp;CODIGO ITEM:</label></span>
                        <input type="text" id="codItem'.$idRegistro.'" name="codItem" class="form-control form-sm" style="width: 170px" value="'.$codItem.'">
                    </div>
                    <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 5px">
                        <span class="input-group-addon input-sm"><label for="invima">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;INVIMA:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label></span>
                        <input type="text" id="invima'.$idRegistro.'" name="invima" class="form-control form-sm" style="width: 230px" value="'.$invima.'">
                        
                        <span class="input-group-addon input-sm"><label for="limite">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;LIMITE:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label></span>
                        <input type="number" min="1" id="limite'.$idRegistro.'" name="limite" class="form-control form-sm" style="width: 160px" value="'.$limite.'">
                    </div>
                    <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 5px">
                        <span class="input-group-addon input-sm"><label for="observacion">&nbsp;OBSERVACION:&nbsp;</label></span>
                        <input type="text" id="observacion'.$idRegistro.'" name="observacion" class="form-control form-sm" style="width: 550px" value="'.$observacion.'">
                    </div>
                    <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 5px">
                        <span class="input-group-addon input-sm"><label for="user">&nbsp;USUARIO QUE MODIFICA:&nbsp;</label></span>
                        <input type="text" id="user'.$idRegistro.'" name="user" class="form-control form-sm" style="width: 450px" value="'.returnUserLogin().'" disabled>
                    </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="hidden" id="accion" name="accion" value="actualizarReuso">
                        <input type="hidden" id="dispoNombreCod'.$idRegistro.'" name="dispoNombreCod" value="' . htmlentities($Codreuso.' - '.$dispoNombre) . '">
                        <input type="hidden" id="trazUpdate'.$idRegistro.'" name="trazUpdate" value="'.$idRegistro.'">
                        <div class="input-group" id="'.$idRegistro.'" style="margin-top: 10px; text-align: center; display: block;">
                            <input type="button" class="btn btn-info btn-sm" value="Actualizar" title="Actualizar" style="width: 120px"
                            onclick="updateReuso(\''.$idRegistro.'\')">
                        </div>
                    </td>
                </tr>
            </table>
        </form>
    </div>';
    echo $htmlreturn;
    
}

// Accion que actualiza el reuso
if($accion == 'actualizarReuso'){
    
    if($trazUpdate != null and $numCalibreUp != null and $codItemUp != null and $invimaUp != null and $limiteUp != null)
    {
        mysqli_set_charset($conex, 'utf8');
        $seguridad = 'C-'.wUser();
        $queryUp12 = "UPDATE {$bdCenest}_000012
        SET Fecha_data = '$fecha_Actual', Hora_data = '$hora_Actual', Ncalibre = '$numCalibreUp', Coditem = '$codItemUp',
        Invima = '$invimaUp', Observacion = '$observacionUp', limite = '$limiteUp', Seguridad = '$seguridad'
        WHERE id = '$trazUpdate'";
        $commQryUp12 = mysql_query($queryUp12, $conex);
        if (mysql_affected_rows($conex) > 0){
            $respuesta['message'] = "Dispositivo actualizado con exito";
        }else{
            $respuesta['error'] = "No se pudo actualizar el Dispositivo";
        }
        echo json_encode($respuesta,JSON_UNESCAPED_UNICODE);
    }
}

// Accion que retorna el ultimo dispositivo por centro de costos
if($accion == 'ultimoDispoXcco'){
    $queryDispo = "SELECT * FROM {$bdCenest}_000011 WHERE Codcco = '$idCco' ORDER BY Codigo DESC LIMIT 1";
    $commitDispo = mysql_query($queryDispo, $conex) or die (mysql_errno()." - en el query: ".$queryDispo." - ".mysql_error());
    $datosDispo = mysql_fetch_array($commitDispo);
    $lastIdDispo = $datosDispo['Codigo'];
    $idDispo = $lastIdDispo + 1;
    echo json_encode($idDispo);
}

// Accion que retorna el modal para el maestro de dispositivos
if ($accion == 'modalMaestroDispo'){
    $queryDispo = "SELECT * FROM {$bdCenest}_000011 WHERE Codcco = '$idCco' ORDER BY Codigo DESC LIMIT 1";
    $commitDispo = mysql_query($queryDispo, $conex) or die (mysql_errno()." - en el query: ".$queryDispo." - ".mysql_error());
    $datosDispo = mysql_fetch_array($commitDispo);
    $lastIdDispo = $datosDispo['Codigo'];
    $idDispo = $lastIdDispo + 1;

    $htmlreturn = '<div class="panel panel-info contenido" style="margin-left: 0">
            <div class="panel-heading encabezado">
                <div class="panel-title titulo1">Matrix - Agregar Nuevo Dispositivo</div>
            </div>
            <form method="post" action="trazProcess.php?wemp_pmla='.$wemp_pmla.'">
                <table align="center">
                    <tr>
                        <td align="left">
                            <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 10px">
                                <span class="input-group-addon input-sm"><label for="codDispoInsert">CODIGO DISPOSITIVO:</label></span>
                                <input type="text" id="codDispoInsert" name="codDispoInsert" class="form-control form-sm" style="width: 80px" value="'.$idDispo.'" readonly>

                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 5px"></span>

                                <span class="input-group-addon input-sm"><label for="cCostDispoInsert">&ensp;C. COSTOS:</label></span>
                                <input type="text" id="cCostDispoInsert" name="cCostDispoInsert" class="form-control form-sm" style="width: 300px" value="'.datosUnidadxCco($idCco,$conex).'" readonly>
                            </div>
                            <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 10px">
                                <span class="input-group-addon input-sm"><label for="descDispoInsert">DESCRIPCION:</label></span>
                                <input type="text" id="descDispoInsert" name="descDispoInsert" class="form-control form-sm" style="width: 547px" required/>
                            </div>

                            <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 10px">
                                <span class="input-group-addon input-sm"><label for="numCalDispoInsert">NUMERO CALIBRE:</label></span>
                                <input type="text" id="numCalDispoInsert" name="numCalDispoInsert" class="form-control form-sm" style="width: 100px">

                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 5px"></span>

                                <span class="input-group-addon input-sm"><label for="codItemInsert">&ensp;CODIGO CLINICA:</label></span>
                                <input type="text" id="codItemInsert" name="codItemInsert" class="form-control form-sm" style="width: 285px">
                            </div>

                            <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 10px">
                                <span class="input-group-addon input-sm"><label for="invDispoInsert">INVIMA:</label></span>
                                <input type="text" id="invDispoInsert" name="invDispoInsert" class="form-control form-sm" style="width: 160px">

                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 5px"></span>

                                <span class="input-group-addon input-sm"><label for="limReuDispoInsert">&ensp;LIMITE REUSO:</label></span>
                                <input type="number" min="1" id="limReuDispoInsert" name="limReuDispoInsert" class="form-control form-sm" style="width: 280px">
                            </div>

                            <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 10px">
                                <span class="input-group-addon input-sm"><label for="userDispoInsert">USUARIO QUE DILIGENCIA:</label></span>
                                <input type="text" id="userDispoInsert" name="userDispoInsert" class="form-control form-sm" style="width: 350px" value="'.returnUserLogin().'" disabled/>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="hidden" id="accion" name="accion" value="insertDispo">
                            <input type="hidden" id="codCcoDispo" name="codCcoDispo" value="'.$idCco.'">
                            <div class="input-group" id="newDispo" style="margin-top: 10px; text-align: center; display: block;">
                                <input type="button" class="btn btn-info btn-sm" value="Insertar" title="Insertar Dispositivo" style="width: 120px"
                                onclick="newDispo()">
                            </div>
                        </td>
                    </tr>
                </table>
            </form>
        </div>';

    echo $htmlreturn;
}

// Accion que actualiza el dispositivo maestro
if($accion == 'insertDispoMaestro')
{
    if($codDispoInsert != null and $cCostDispoInsert != null and $descDispoInsert != null)
    {
        mysqli_set_charset($conex, 'utf8');
        $seguridad = 'C-'.wUser();
        $queryInsDispo = "UPDATE {$bdCenest}_000011
                            SET Descripcion  = '$descDispoInsert', numCalibre = '$numCalDispoInsert', codItem = '$codItemInsert', invima = '$invDispoInsert', LimReuso = '$limReuDispoInsert', Seguridad = '$seguridad'
                            WHERE Codcco = '$cCostDispoInsert'
                            AND Codigo = '$codDispoInsert'";
        $commQryInsDispo = mysql_query($queryInsDispo, $conex);
        if (mysql_affected_rows($conex) > 0){
            $respuesta['message'] = "El dispositivo: $descDispoInsert fue actualizado con Exito";
        }else{
            $respuesta['error'] = "No se pudo insertar en la Base de Datos";
        }
    }
    else
    {
        $respuesta['error'] = "El campo de descripcion es obligatorio";
        
    }
    echo json_encode(validateHtmlEntities($respuesta),JSON_UNESCAPED_UNICODE);

}

// Accion para insertar un dispositivo nuevo
if($accion == 'insertDispo')
{
    if($codDispoInsert != null and $cCostDispoInsert != null and $descDispoInsert != null)
    {
        mysqli_set_charset($conex, 'utf8');
        $seguridad = 'C-'.wUser();
        $queryInsDispo = "INSERT INTO {$bdCenest}_000011
                        VALUES('cenest','$fecha_Actual','$hora_Actual','$codDispoInsert','$descDispoInsert',
                        '$numCalDispoInsert','$codItemInsert','$invDispoInsert','$limReuDispoInsert','$cCostDispoInsert','on','$seguridad','')";
        $commQryInsDispo = mysql_query($queryInsDispo, $conex)  or ($desc = mysql_errno()." - en el query: ".$query2." - ".mysql_error());;
        if (mysql_affected_rows($conex) > 0){
            $respuesta['message'] = "El dispositivo: $descDispoInsert fue agregado con Exito";
        }else{
            $respuesta['error'] = "No se pudo insertar en la Base de Datos";
        }
    }
    else
    {
        $respuesta['error'] = "El campo de descripcion es obligatorio";
        
    }
    echo json_encode(validateHtmlEntities($respuesta),JSON_UNESCAPED_UNICODE);
}

//Accion que genera el modal para insertar el nuevo reuso
if($accion == 'modalInsertarReuso'){
    $blockSelect = 'readonly';
    $addHtml = '';
    $delimiter = '';
    $query2 = "SELECT * FROM {$bdCenest}_000011 WHERE Codigo = '$idRegistro' AND Codcco = '$codCcoDispo'";
    $commit2 = mysql_query($query2, $conex) or die (mysql_errno()." - en el query: ".$query2." - ".mysql_error());
    $datos2 = mysql_fetch_array($commit2);
    $dispoNombre = $datos2['Descripcion'];  $codCcoDisp = $datos2['Codcco'];

    $queryReu = "SELECT Codreuso FROM {$bdCenest}_000012 WHERE Coddispo = '$idRegistro' AND id = '$idReuso' AND Codcco = '$codCcoDisp'";
    $commQryReu = mysql_query($queryReu, $conex) or die (mysql_errno()." - en el query: ".$queryReu." - ".mysql_error());
    $datosReu = mysql_fetch_array($commQryReu);
    $codLastReu = $datosReu[0];
    $htmlreturn = '<div class="panel panel-info contenido" style="margin-left: 0">
                        <div class="panel-heading encabezado">
                            <div class="panel-title titulo1">Matrix - Adicionar Registros de Reuso</div>
                        </div>';
            //Si tiene algun codigo de reuso creado
            if($codLastReu != null)
            {
                $lastReu = generateNewCod($codLastReu); //funcion que genera el nuevo codigo de reuso de acuerdo al anterior
                if (!$lastReu){
                    $blockSelect = '';
                    $lastReu = 'Escribalo manualmente';
                }
               
            }
            elseif($codLastReu == null)
            {
                $queryNewReu = "SELECT Codreuso FROM {$bdCenest}_000012 WHERE Coddispo = '$idRegistro' AND Codcco = '$codCcoDisp' ORDER BY Codreuso DESC LIMIT 1";
                $commNewReu = mysql_query($queryNewReu, $conex) or die (mysql_errno()." - en el query: ".$queryNewReu." - ".mysql_error());
                $datosNewReu = mysql_fetch_array($commNewReu);
                $codLastReu = $datosNewReu[0];
                if ($codLastReu!= null){
                    if (strpos($codLastReu,'.')){
                        $delimiter = '.';
                    }else if (strpos($codLastReu,'-')){
                        $delimiter = '-';
                    }else{
                        $blockSelect = '';
                        $lastReu = 'Escribalo manualmente';
                    }
                    if ($delimiter != ''){
                        list($parte1,$parte2,$parte3) = explode($delimiter, $codLastReu); //DIVIDO LA CADENA POR LOS PUNTOS
                        $lastReu = $parte3;
                        $lastReu = $lastReu + 1;
                        if ($lastReu > 0 and $lastReu < 10){
                            $lastReu = '0'. $lastReu;
                        }
                        $lastReu = $parte1.$delimiter.$parte2.$delimiter.$lastReu;
                    }
                }else{
                    $middlePart = validateAbrNameXCco($codCcoDisp);
                    $numCalFirstDispo = '';
                    $invimaFirstDispo = '';
                    $limReusoFirstDispo = '';
                    if ($middlePart != 'undefined'){
                        $lastCod = $idRegistro;
                        if ($lastCod > 0 and $lastCod < 10){
                            $parte1 = '0'. $lastCod;
                        }else{
                            $parte1 = $lastCod;
                        }
                        $parte2 =  $middlePart;
                        $parte3 = '01';
                        $lastReu = $parte1.'-'.$parte2.'-'.$parte3;
                        $blockSelect = '';
                    }else{
                        $blockSelect = '';
                    }
                    if (isset($datos2['numCalibre']))$numCalFirstDispo = $datos2['numCalibre'];
                    if (isset($datos2['invima']))$invimaFirstDispo = $datos2['invima'];
                    if (isset($datos2['LimReuso']))$limReusoFirstDispo = $datos2['LimReuso'];

                }
            }
            // Se crea el html del cuerpo del modal para insertar nuevo reuso
            $htmlreturn .= '<h4 style="text-align: center">'.$idRegistro.' - '.$dispoNombre.'</h4>

            <form method="post" action="trazProcess.php?wemp_pmla='.$wemp_pmla.'">
                <table align="center">
                    <tr>
                        <td align="left">
                            <div class="input-group selectDispo" style="margin-left: 10px">
                                
                                <span class="input-group-addon input-sm"><label for="codReusoIns">CODIGO REUSO:</label></span>
                                <input type="text" id="codReusoIns'.$idReuso.'" name="codReusoIns" class="form-control form-sm" style="width: 230px"
                                       value="'.$lastReu.'" '.$blockSelect.'>

                                <span class="input-group-addon input-sm"><label for="codCcoDispo">SERVICIO:</label></span>
                                <input type="text" id="codCcoDispo'.$idReuso.'" name="codCcoDispo" class="form-control form-sm" style="width: 195px" value="'.$codCcoDispo.'" readonly>
                            </div>

                            <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 5px">
                                <span class="input-group-addon input-sm"><label for="numCalIns">NUMERO CALIBRE:</label></span>
                                <input type="text" id="numCalIns'.$idReuso.'" name="numCalIns" class="form-control form-sm" style="width: 80px"
                                value="'.$numCalFirstDispo.'" >
                                '.$addHtml.'
                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 135px"></span>

                                <span class="input-group-addon input-sm"><label for="codItemIns">&ensp;REFERENCIA:</label></span>
                                <input type="text" id="codItemIns'.$idReuso.'" name="codItemIns" class="form-control form-sm" style="width: 170px">
                            </div>
                            <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 5px">
                                <span class="input-group-addon input-sm"><label for="invimaIns">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;INVIMA:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label></span>
                                <input type="text" id="invimaIns'.$idReuso.'" name="invimaIns" class="form-control form-sm" style="width: 230px"
                                value="'.$invimaFirstDispo.'" >

                                <span class="input-group-addon input-sm"><label for="numReusoIns">&nbsp;LIMITE REUSO:&nbsp;</label></span>
                                <input type="number" min="1" id="numReusoIns'.$idReuso.'" name="numReusoIns" class="form-control form-sm" style="width: 160px"
                                value="'.$limReusoFirstDispo.'">
                            </div>
                            <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 5px">
                                <span class="input-group-addon input-sm"><label for="obserReusoIns">&nbsp;OBSERVACION:&nbsp;</label></span>
                                <input type="text" id="obserReusoIns'.$idReuso.'" name="obserReusoIns" class="form-control form-sm" style="width: 550px">
                            </div>
                            <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 5px">
                                <span class="input-group-addon input-sm"><label for="userReusoIns">&nbsp;USUARIO QUE MODIFICA:&nbsp;</label></span>
                                <input type="text" id="userReusoIns'.$idReuso.'" name="userReusoIns" class="form-control form-sm" style="width: 450px" value="'.returnUserLogin().'" disabled>
                            </div>
                        </td>
                    </tr>';
                    // Si no es un reuso definido a actualizar
                    if($idReuso == 'undefined'){
                        $htmlreturn .= '<tr>
                                            <td>
                                            <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 5px">
                                                <span class="input-group-addon input-sm"><label for="numCodAct">Nro CODIGOS ACTIVAR:</label></span>
                                                <input type="number" min="1" id="numCodAct" name="numCodAct" class="form-control form-sm" style="width: 80px" value="1">
                                            </div>
                                            </td>
                                        </tr>';
                    }
                    
                    
                    $htmlreturn .= '<tr>
                        <td>
                            <input type="hidden" id="accion" name="accion" value="insertReuso">
                            <input type="hidden" id="codDispoReu'.$idReuso.'" name="codDispoReu" value="'.$idRegistro.'">
                            <input type="hidden" id="idReuso'.$idReuso.'" name="idReuso" value="'.$idReuso.'">';
                            
                            if($idReuso != 'undefined')
                            {
                                
                                $htmlreturn .= '<div class="input-group" id="'.$idReuso.'" style="margin-top: 10px; text-align: center;display: block;">
                                    <input type="button" class="btn btn-info btn-sm" value="Actualizar" title="Actualizar" style="width: 120px"
                                    onclick="newReuso(\''.$idReuso.'\')">
                                </div>';
                                
                            }
                            else
                            {  
                                
                                $htmlreturn .= '<div class="input-group" id="'.$idReuso.'" style="margin-top: 10px; text-align: center;display: block;">
                                    <input type="button" class="btn btn-info btn-sm" value="Insertar" title="Apertura de Codigos" style="width: 120px"
                                    onclick="newReuso(\''.$idReuso.'\')">
                                </div>';
                                
                            }
                            
        $htmlreturn .= '</td>
                    </tr>
                </table>
            </form>
        </div>';

        echo $htmlreturn;
}

// Accion que arma el modal para ver la trazabilidad del reuso
if($accion == 'modalVerReuso')
{
    $htmlreturn = '';
    $qryDatDispo = "SELECT * FROM {$bdCenest}_000012 WHERE Codreuso = '$codReuso13'";
    $commQryDispo = mysql_query($qryDatDispo, $conex) or die (mysql_errno()." - en el query: ".$qryDatDispo." - ".mysql_error());
    $datDispo = mysql_fetch_array($commQryDispo);
    $calibreDispo = $datDispo['Ncalibre'];  $invimaDispo = $datDispo['Invima']; $nusosDispo = $datDispo['Numuso'];

    $queryVerReu = "SELECT * FROM {$bdCenest}_000014 WHERE id = '$idRegistro'";
    $commitVerReu = mysql_query($queryVerReu, $conex) or die (mysql_errno()." - en el query: ".$queryVerReu." - ".mysql_error());
    $datosVerReu = mysql_fetch_array($commitVerReu);
    $fecUsoReu = $datosVerReu['Fechauso'];          $nomUsReu = $datosVerReu['Nomusuario'];     $docUsReu = $datosVerReu['Docusuario'];
    $numQuiReu = $datosVerReu['NumQuirofano'];      $obsReu = $datosVerReu['Observacion'];      $usrEntEsteril = $datosVerReu['UserServicio'];
    $usrRecEsteril = $datosVerReu['UserEsteril'];   $fecEsteril = $datosVerReu['FechaEsteril']; $equiEsteril = $datosVerReu['EquipoEsteril'];
    $metEsteril = $datosVerReu['MetodoEsteril'];    $cicEsteril = $datosVerReu['CicloEsteril']; $respEsteril = $datosVerReu['RespEsteril'];
    $respDiligen = $datosVerReu['RespDiligen'];
    $htmlreturn .=    '<div class="panel panel-info contenido" style="margin-left: 0">
            <div class="panel-heading encabezado">
                <div class="panel-title titulo1">Matrix - Datos de Reuso</div>
            </div>
            <form method="post" action="trazProcess.php?wemp_pmla='.$wemp_pmla.'">
                <table align="center">
                    <tr>
                        <td align="left">
                            <div class="input-group selectDispo" style="margin-left: -5px; margin-top: 10px">
                                <span class="input-group-addon input-sm"><label for="codReuso'.$idRegistro.'">CODIGO REUSO:</label></span>
                                <input type="text" id="codReuso'.$idRegistro.'" class="form-control form-sm" style="width: 178px; background-color: white" value="'.$codReuso13.'" readonly>

                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 5px"></span>

                                <span class="input-group-addon input-sm"><label for="calReuso'.$idRegistro.'">&ensp;CALIBRE:</label></span>
                                <input type="text" id="calReuso'.$idRegistro.'" class="form-control form-sm" style="width: 80px; background-color: white" value="'.$calibreDispo.'" readonly>

                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 5px"></span>

                                <span class="input-group-addon input-sm"><label for="nusosReuso'.$idRegistro.'">NUMERO USOS:</label></span>
                                <input type="text" id="nusosReuso'.$idRegistro.'" class="form-control form-sm" style="width: 60px; background-color: white" value="'.$nusosDispo.'" readonly>
                            </div>
                            <div class="input-group selectDispo" style="margin-left: -5px; margin-top: 5px">
                                <span class="input-group-addon input-sm"><label for="invReuso'.$idRegistro.'">INVIMA:&ensp;&ensp;&ensp;&ensp;</label></span>
                                <input type="text" id="invReuso'.$idRegistro.'" class="form-control form-sm" style="width: 312px; background-color: white" value="'.$invimaDispo.'" readonly>

                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 5px"></span>

                                <span class="input-group-addon input-sm"><label for="nusosReuso'.$idRegistro.'">FECHA UTILIZACION:</label></span>
                                <input type="text" id="nusosReuso'.$idRegistro.'" class="form-control form-sm" style="width: 115px; background-color: white" value="'.$fecUsoReu.'" readonly>
                            </div>
                            <div class="input-group selectDispo" style="margin-left: -5px; margin-top: 10px">
                                <span class="input-group-addon input-sm"><label for="NombreUsu'.$idRegistro.'">NOMBRE DE USUARIO:&ensp;&ensp;&ensp;&ensp;&ensp;</label></span>
                                <input type="text" id="NombreUsu'.$idRegistro.'" class="form-control form-sm" style="width: 498px; background-color: white" value="'.$nomUsReu.'" readonly>
                            </div>
                            <div class="input-group selectDispo" style="margin-left: -5px; margin-top: 5px">
                                <span class="input-group-addon input-sm"><label for="NumIdentif'.$idRegistro.'">NUMERO IDENTIFICACION:</label></span>
                                <input type="text" id="NumIdentif'.$idRegistro.'" class="form-control form-sm" style="width: 234px; background-color: white" value="'.$docUsReu.'" readonly>

                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 5px"></span>

                                <span class="input-group-addon input-sm"><label for="numQuirof'.$idRegistro.'">NUMERO QUIROFANO:</label></span>
                                <input type="text" id="numQuirof'.$idRegistro.'" class="form-control form-sm" style="width: 100px; background-color: white" value="'.$numQuiReu.'" readonly>
                            </div>
                            <div class="input-group selectDispo" style="margin-left: -5px; margin-top: 10px">
                                <span class="input-group-addon input-sm"><label for="ObservReuso'.$idRegistro.'">OBSERVACIONES:</label></span>
                                <input type="text" id="ObservReuso'.$idRegistro.'" class="form-control form-sm" style="width: 560px; background-color: white" value="'.$obsReu.'" readonly>
                            </div>
                            <div class="input-group selectDispo" style="margin-left: -5px; margin-top: 5px">
                                <span class="input-group-addon input-sm"><label for="funcEntReuso'.$idRegistro.'">FUNCIONARIO QUE ENTREGA A ESTERILIZACION:</label></span>
                                <input type="text" id="funcEntReuso'.$idRegistro.'" class="form-control form-sm" style="width: 384px; background-color: white" value="'.datosUsrMtx($usrEntEsteril,$conex).'" readonly>
                            </div>
                            <div class="input-group selectDispo" style="margin-left: -5px; margin-top: 10px">
                                <span class="input-group-addon input-sm"><label for="funcRecReuso'.$idRegistro.'">FUNCIONARIO QUE RECIBE EN ESTERILIZACION:&ensp;</label></span>
                                <input type="text" id="funcRecReuso'.$idRegistro.'" class="form-control form-sm" style="width: 383px; background-color: white" value="'.datosUsrMtx($usrRecEsteril,$conex).'" readonly>
                            </div>
                            <div class="input-group selectDispo" style="margin-left: -5px; margin-top: 5px">
                                <span class="input-group-addon input-sm"><label for="fecEstReuso'.$idRegistro.'">FECHA ESTERILIZACION:</label></span>
                                <input type="text" id="fecEstReuso'.$idRegistro.'" class="form-control form-sm" style="width: 230px; background-color: white" value="'. $fecEsteril.'" readonly>

                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 5px"></span>

                                <span class="input-group-addon input-sm"><label for="eqEstReuso'.$idRegistro.'">EQUIPO ESTERILIZADOR:</label></span>
                                <input type="text" id="eqEstReuso'.$idRegistro.'" class="form-control form-sm" style="width: 98px; background-color: white" value="'.$equiEsteril.'" readonly>
                            </div>
                            <div class="input-group selectDispo" style="margin-left: -5px; margin-top: 5px">
                                <span class="input-group-addon input-sm"><label for="metEstReuso'.$idRegistro.'">METODO ESTERILIZACION:</label></span>
                                <input type="text" id="metEstReuso'.$idRegistro.'" class="form-control form-sm" style="width: 216px; background-color: white" value="'.$metEsteril.'" readonly>

                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 5px"></span>

                                <span class="input-group-addon input-sm"><label for="cicEstReuso'.$idRegistro.'">CICLO DE ESTERILIZACION:</label></span>
                                <input type="text" id="cicEstReuso'.$idRegistro.'" class="form-control form-sm" style="width: 80px; background-color: white" value="'.$cicEsteril.'" readonly>
                            </div>
                            <div class="input-group selectDispo" style="margin-left: -5px; margin-top: 10px">
                                <span class="input-group-addon input-sm"><label for="respEstReuso'.$idRegistro.'">RESPONSABLE DE ESTERILIZACION:</label></span>
                                <input type="text" id="respEstReuso'.$idRegistro.'" class="form-control form-sm" style="width: 449px; background-color: white" value="'.datosUsrMtx($respEsteril,$conex).'" readonly>
                            </div>
                            <div class="input-group selectDispo" style="margin-left: -5px; margin-top: 10px">
                                <span class="input-group-addon input-sm"><label for="respDilReuso'.$idRegistro.'">RESPONSABLE DILIGENCIAMIENTO:</label></span>
                                <input type="text" id="respDilReuso'.$idRegistro.'" class="form-control form-sm" style="width: 453px; background-color: white" value="'.datosUsrMtx($respDiligen,$conex).'" readonly>
                            </div>
                        </td>
                    </tr>
                </table>
            </form>
        </div>';
        echo $htmlreturn;
}

//Accion que inserta un nuevo dispositivo de reuso y pone en estado off al reuso si es una actualizacion
if($accion == 'insertReuso'){
    if($codDispoReu != null and $codReusoIns != null and $numCalIns != null and $codItemIns != null and $numReusoIns != null){
        mysqli_set_charset($conex, 'utf8');
        if (strpos($codReusoIns,'.')){
            $delimiter = '.';
        }else if (strpos($codReusoIns,'-')){
            $delimiter = '-';
        }
        $queryUp12 = "UPDATE {$bdCenest}_000012 SET Estado = 'off' WHERE id = '$idReuso'";
        mysql_query($queryUp12, $conex);
        $seguridad = 'C-'.wUser();
            if ($idReuso == 'undefined'){
                for ($i = 0; $i < $idDivNumCod; $i++) {
                    $queryIns12 = "INSERT INTO {$bdCenest}_000012
                                    VALUES('cenest','$fecha_Actual','$hora_Actual','$codReusoIns','$numCalIns','$codItemIns','$invimaIns','$obserReusoIns',
                                        '0','$codDispoReu','st','$numReusoIns','$codCcoDispo','$seguridad','')";
                    $commQryIns12 = mysql_query($queryIns12, $conex);
                    $codReusoIns = newcod($codReusoIns,$delimiter);
                }
            }else{
                $queryIns12 = "INSERT INTO {$bdCenest}_000012
                                VALUES('cenest','$fecha_Actual','$hora_Actual','$codReusoIns','$numCalIns','$codItemIns','$invimaIns','$obserReusoIns',
                                    '0','$codDispoReu','on','$numReusoIns','$codCcoDispo','$seguridad','')";
                $commQryIns12 = mysql_query($queryIns12, $conex);
            }
            if (mysql_affected_rows($conex) > 0){
                $respuesta['message'] = "El reuso fue agregado con Exito";
            }else{
                $respuesta['error'] = "No se pudo insertar en la Base de Datos";
            }
    }
    else{
        $respuesta['error'] = "Todos los campos son obligatorios";
    }
    echo json_encode(validateHtmlEntities($respuesta),JSON_UNESCAPED_UNICODE);
}

// Accion que cambia el estado del dispositivo de st: standby a on y viceverza
if($accion == 'changeStateDispo'){
    $q = "UPDATE {$bdCenest}_000012 SET Estado = '{$stateDispo}' WHERE id = '{$idChangeState}'";
    mysql_query($q, $conex);
}

// Accion que elabora un select con los reusos disponibles por centro de costos y codigo del dispositivo
if($accion == 'findReusosXCod'){
    $query22 = "SELECT * FROM {$bdCenest}_000012 WHERE Coddispo = '$Coddispo' AND Codcco = '$cCostoReport'";
    $commitQuery22 = mysql_query($query22, $conex) or die (mysql_errno()." - en el query: ".$query22." - ".mysql_error());
    while($datoReuso = mysql_fetch_array($commitQuery22))
    {
        $codReuso = $datoReuso['Codreuso']; $idReuso = $datoReuso['id'];
        $usos = $datoReuso['Numuso'];     $limiteUsos = $datoReuso['limite'];
            echo "<option value='".$codReuso.'_'.$idReuso."'>".$codReuso."</option>";
    }
}

// Accion que envia los datos al autocompletar de los codigos de reuso para los reportes
if($accion == 'autocomplete'){
    if(isset($_POST['search'])){
        $search = $_POST['search'];
        $query22 = "SELECT * FROM {$bdCenest}_000012 WHERE Coddispo = '$Coddispo' AND Codcco = '$cCostoReport' AND Codreuso LIKE '%".$search."%'";
        $commitQuery22 = mysql_query($query22, $conex) or die (mysql_errno()." - en el query: ".$query22." - ".mysql_error());
        while($datoReuso = mysql_fetch_array($commitQuery22))
        {
            $codReuso = $datoReuso['Codreuso']; $idReuso = $datoReuso['id'];
            $usos = $datoReuso['Numuso'];     $limiteUsos = $datoReuso['limite'];
            $response[] = array("value" => $codReuso.'_'.$idReuso,"label"=> $datoReuso['Codreuso']);
        }
        echo json_encode($response);
    }
}

// Accion que elabora un select con los dispositivos X centros de costos
if($accion == 'findDispoXCco'){
    $query2 = "SELECT * FROM {$bdCenest}_000011 WHERE Codcco = '$cCostoReport' AND Estado = 'on'";
    $commitQuery2 = mysql_query($query2, $conex) or die (mysql_errno()." - en el query: ".$query2." - ".mysql_error());
    while($datoDispo = mysql_fetch_array($commitQuery2))
    {
        $codDispo = $datoDispo['Codigo'];   $descDispo = $datoDispo['Descripcion'];
        echo "<option value='".$codDispo."'>".$codDispo.' - '.$descDispo."</option>";
    }
}

// Accion que elabora el modal para modificar los dispositivos maestros
if ($accion == 'modalModificarDispoMaestro'){
    $queryDispo = "SELECT * FROM {$bdCenest}_000011 WHERE Codcco = '$codCcoDispo' AND Codigo = '$Coddispo'";
    $commitDispo = mysql_query($queryDispo, $conex) or die (mysql_errno()." - en el query: ".$queryDispo." - ".mysql_error());
    $datosDispo = mysql_fetch_array($commitDispo);
    
    $htmlreturn = '<div class="panel panel-info contenido" style="margin-left: 0">
            <div class="panel-heading encabezado">
                <div class="panel-title titulo1">Matrix - Editar Dispositivo</div>
            </div>
            <form method="post" action="trazProcess.php?wemp_pmla='.$wemp_pmla.'">
                <table align="center">
                    <tr>
                        <td align="left">
                            <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 10px">
                                <span class="input-group-addon input-sm"><label for="codDispoInsertMaestro">CODIGO DISPOSITIVO:</label></span>
                                <input type="text" id="codDispoInsertMaestro" name="codDispoInsertMaestro" class="form-control form-sm" style="width: 80px" value="'.$Coddispo.'" readonly>

                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 5px"></span>

                                <span class="input-group-addon input-sm"><label for="cCostDispoInsertMaestro">&ensp;C. COSTOS:</label></span>
                                <input type="text" id="cCostDispoInsertMaestro" name="cCostDispoInsertMaestro" class="form-control form-sm" style="width: 300px" value="'.datosUnidadxCco($codCcoDispo,$conex).'" readonly>
                            </div>
                            <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 10px">
                                <span class="input-group-addon input-sm"><label for="descDispoInsertMaestro">DESCRIPCION:</label></span>
                                <input type="text" id="descDispoInsertMaestro" name="descDispoInsertMaestro" class="form-control form-sm" style="width: 547px" value="'.$datosDispo['Descripcion'].'">
                            </div>
                            <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 10px">
                                <span class="input-group-addon input-sm"><label for="userAntesDispoInsertMaestro">ULTIMO USUARIO QUE MODIFICO:</label></span>
                                <input type="text" id="userAntesDispoInsertMaestro" name="userAntesDispoInsertMaestro" class="form-control form-sm" style="width: 450px" value="'.datosUsuarioXseg($datosDispo['Seguridad']).'" disabled>
                            </div>
                            <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 10px">
                                <span class="input-group-addon input-sm"><label for="userDispoInsertMaestro">USUARIO QUE MODIFICA:</label></span>
                                <input type="text" id="userDispoInsertMaestro" name="userDispoInsertMaestro" class="form-control form-sm" style="width: 450px" value="'.returnUserLogin().'" disabled>
                            </div>

                            <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 10px">
                                <span class="input-group-addon input-sm"><label for="numCalDispoInsertMaestro">NUMERO CALIBRE:</label></span>
                                <input type="text" id="numCalDispoInsertMaestro" name="numCalDispoInsertMaestro" class="form-control form-sm" style="width: 100px" value="'.$datosDispo['numCalibre'].'">

                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 5px"></span>

                                <span class="input-group-addon input-sm"><label for="codItemInsertMaestro">&ensp;CODIGO CLINICA:</label></span>
                                <input type="text" id="codItemInsertMaestro" name="codItemInsertMaestro" class="form-control form-sm" style="width: 285px" value="'.$datosDispo['codItem'].'">
                            </div>

                            <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 10px">
                                <span class="input-group-addon input-sm"><label for="invDispoInsertMaestro">INVIMA:</label></span>
                                <input type="text" id="invDispoInsertMaestro" name="invDispoInsertMaestro" class="form-control form-sm" style="width: 160px" value="'.$datosDispo['invima'].'">

                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 5px"></span>

                                <span class="input-group-addon input-sm"><label for="limReuDispoInsertMaestro">&ensp;LIMITE REUSO:</label></span>
                                <input type="number" min="1" id="limReuDispoInsertMaestro" name="limReuDispoInsertMaestro" class="form-control form-sm" style="width: 280px" value="'.$datosDispo['LimReuso'].'">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="hidden" id="accion" name="accion" value="insertDispoMaestro">
                            <input type="hidden" id="codCcoDispoMaestro" name="codCcoDispoMaestro" value="'.$codCcoDispo.'">
                            <div class="input-group" id="editDispo" style="margin-top: 10px; text-align: center; display: block;">
                                <input type="button" class="btn btn-info btn-sm" value="Actualizar" title="Editar Dispositivo" style="width: 120px"
                                onclick="editDispo()">
                            </div>
                        </td>
                    </tr>
                </table>
            </form>
        </div>';

    echo $htmlreturn;
}