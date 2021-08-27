<?php

/**
 * Archivo de funciones PHP de TrazMaster.php
 * @author Julian Mejia - julian.mejia@lasamericas.com.co
 */

/*******
 * Funcion para construir un modal para agregar centro de costos
 * @return html
 */
function construirModalAddCco(){
    global $wemp_pmla;
    $htmlreturn = '<div class="panel panel-info contenido" style="margin-left: 0">
            <div class="panel-heading encabezado">
                <div class="panel-title titulo1" align="center">AGREGAR CENTRO DE COSTOS</div>
            </div>

            <form method="post" action="trazProcess.php?wemp_pmla='.$wemp_pmla.'" name="formAddCco">
                <table align="center">
                    <tr>
                        <td align="center">
                            <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 5px">
                                <span class="input-group-addon input-sm"><label for="CcoAdd">CENTRO DE COSTOS</label></span>
                                <input type="text" id="CcoAdd" name="CcoAdd" class="form-control form-sm" style="width: 120px" required/>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="hidden" id="accion" name="accion" value="addNewCco">
                            <input type="hidden" id="wemp_pmla" name="wemp_pmla" value="'.$wemp_pmla.'">
                            <div id="divAddCco" class="input-group" style="margin-top: 10px; text-align: center; display:block;">
                                <input type="button" class="btn btn-info btn-sm" value="Agregar Centro de Costos" title="Adicionar Centro de costos" style="width: 200px" 
                                onclick="addCco()">
                                <div id="divAddCcoApp" style="margin-top: 5px">
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </form>
        </div>';
        echo $htmlreturn;
}

/*******************************
 * Funcion que construye el modal para agregar un nuevo usuario
 * @return html
 */
function construirModalAddUser(){
    global $wemp_pmla;
    echo '<!--div class="container" style="margin-left: 0"-->
    <div class="panel panel-info contenido" style="margin-left: 0">
        <div class="panel-heading encabezado">
            <div class="panel-title titulo1" align="center">AGREGAR USUARIO</div>
        </div>

        <form method="post" action="trazProcess.php?wemp_pmla='.$wemp_pmla.'" name="formAdd" onsubmit="return validarCamposMaestro()">
            <table align="center">
                <tr>
                    <td align="left">
                        <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 5px">
                            <span class="input-group-addon input-sm"><label for="codUsuAdd">CODIGO MATRIX:</label></span>
                            <input type="text" id="codUsuAdd" name="codUsuAdd" class="form-control form-sm" style="width: 120px" required/>

                            <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 10px"></span>

                            <span class="input-group-addon input-sm"><label for="nomUsuAdd">&ensp;NOMBRE USUARIO:</label></span>
                            <input type="text" id="nomUsuAdd" name="nomUsuAdd" class="form-control form-sm" title="Nombre sin tilde ni &ntilde;" style="width: 300px" required/>
                        </div>
                        <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 5px">
                            <span class="input-group-addon input-sm"><label for="userPriorAdd">PRIORIDAD:</label></span>
                            <!--input type="text" id="userPriorUpdate" name="userPriorUpdate" class="form-control form-sm" style="width: 100px"-->
                            <select id="userPriorAdd" name="userPriorAdd" class="form-control form-sm" style="width: 170px; min-height: 34px">
                                <option selected disabled>Prioridad</option>
                                <option value="1">1 - Coordinador</option>
                                <option value="2">2 - Usuario</option>
                            </select>
                            <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 5px"></span>
                            <span class="input-group-addon input-sm"><label for="userStateAdd">&ensp;ESTADO:</label></span>
                            <select id="userStateAdd" name="userStateAdd" class="form-control form-sm" style="width: 170px; min-height: 34px">
                                <option selected disabled>Estado Usuario</option>
                                <option value="on">on - Activo</option>
                                <option value="off">off - Inactivo</option>
                            </select>
                            
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="hidden" id="accion" name="accion" value="addUser">
                        <input type="hidden" id="wemp_pmla" name="wemp_pmla" value="'.$wemp_pmla.'">
                        <!--input type="hidden" id="codusu" name="codusu" value="<?php echo $codUsuario ?>"-->
                        <!--input type="hidden" id="userPriority" name="codusu" value="<?php echo $codUsuario ?>"-->
                        <div id="divAddUser" class="input-group" style="margin-top: 10px; text-align: center; display:block;">
                            <input type="button" class="btn btn-info btn-sm" value="Agregar Usuario" title="Adicionar Usuario" style="width: 120px" 
                            onclick="addUser()">
                            <div id="divAddUserApp" style="margin-top: 5px">
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </form>
    </div>
<!--/div-->';
}

/*******************************
 * Funcion que construye el modal para actualizar un usuario determinado
 * @return html
 */
function construirModalUpdateUser($codUsuario,$userName,$idState,$idPrior,$idDiv){
    global $wemp_pmla;
    $retorno = '<div class="panel panel-info contenido" style="margin-left: 0">
    <div class="panel-heading encabezado"> 
        <div class="panel-title titulo1" align="center">ACTUALIZAR USUARIO</div>
    </div>

    <form method="post" action="trazProcess.php?wemp_pmla='.$wemp_pmla.'" name="formUpdate" onsubmit="return validarCampos()">
        <table align="center">
            <tr>
                <td align="left">
                    <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 10px">
                        <span class="input-group-addon input-sm"><label for="codUsuarioModificar">CODIGO USUARIO:</label></span>
                        <input type="text" id="codusu" name="codusu" class="form-control form-sm" style="width: 100px" value="'.$codUsuario.'" readonly>

                        <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 5px"></span>

                        <span class="input-group-addon input-sm"><label for="nombreUsuarioModificar">&ensp;NOMBRE:</label></span>
                        <input type="text" id="nombreUsuarioModificar" name="nombreUsuarioModificar" class="form-control form-sm" style="width: 300px" value="'.$userName.'" readonly>
                    </div>

                    <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 5px">
                        <span class="input-group-addon input-sm"><label for="'.$idPrior.'">PRIORIDAD:</label></span>
                        <select id="'.$idPrior.'" name="'.$idPrior.'" class="form-control form-sm" style="width: 170px; min-height: 34px">
                            <option selected disabled>Prioridad</option>
                            <option value="1">1 - Coordinador</option>
                            <option value="2">2 - Usuario</option>
                        </select>
                        <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 5px"></span>
                        <span class="input-group-addon input-sm"><label for="'.$idState.'">&ensp;ESTADO:</label></span>
                        <select id="'.$idState.'" name="'.$idState.'" class="form-control form-sm" style="width: 170px; min-height: 34px">
                            <option selected disabled>Estado Usuario</option>
                            <option value="on">on - Activo</option>
                            <option value="off">off - Inactivo</option>
                        </select>
                        
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="hidden" id="accion" name="accion" value="updateUser">
                    <input type="hidden" id="wemp_pmla" name="wemp_pmla" value="'."$wemp_pmla".'">
                    <div id="'."$idDiv".'" class="input-group" style="margin-top: 10px; text-align: center; display: block;">
                        <input type="button" class="btn btn-info btn-sm" value="Actualizar" title="Actualizar Usuario" style="width: 120px" 
                        onclick="updateUser(\''.$codUsuario.'\',\''.$idState.'\',\''.$idPrior.'\',\''.$idDiv.'\')">
                        <div id="'."$idDiv".'App" style="margin-top: 5px">
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </form>
</div>';
    return $retorno;
}

/*******************************
 * Funcion que construye el modal para eliminar un centro de costos de la plataforma
 * @return string
 */
function construirModalDeleteCco($codCco,$nombreCco,$idDiv){
    global $wemp_pmla;
    $retorno = '<div class="panel panel-info contenido" style="margin-left: 0">
                <div class="panel-heading encabezado"> 
                    <div class="panel-title titulo1" align="center">ELIMINAR CENTRO DE COSTOS</div>
                </div>

                <form method="post" action="trazProcess.php?wemp_pmla='.$wemp_pmla.'" name="formDeleteCco"">
                    <table align="center">
                        <tr>
                            <td align="left">
                                <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 10px">
                                    <span class="input-group-addon input-sm"><label for="ccocenest">CENTRO DE COSTOS:</label></span>
                                    <input type="text" id="ccocenest" name="ccocenest" class="form-control form-sm" style="width: 100px" value="'.$codCco.'" readonly>

                                    <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 5px"></span>

                                    <span class="input-group-addon input-sm"><label for="nombreCcoDelete">&ensp;NOMBRE:</label></span>
                                    <input type="text" id="nombreCcoDelete" name="nombreCcoDelete" class="form-control form-sm" style="width: 300px" value="'.$nombreCco.'" readonly>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="hidden" id="accion" name="accion" value="deleteCco">
                                <input type="hidden" id="wemp_pmla" name="wemp_pmla" value="'."$wemp_pmla".'">
                                <div id="'."$idDiv".'" class="input-group" style="margin-top: 10px; text-align: center; display: block;">
                                    <input type="button" class="btn btn-danger btn-sm" value="Eliminar" title="Eliminar Centro de Costos" style="width: 120px" 
                                    onclick="deleteCco(\''.$codCco.'\',\''.$idDiv.'\')">
                                    <div id="'."$idDiv".'App" style="margin-top: 5px">
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>';
    return $retorno;
}

/*******************************
 * Funcion que construye el modal para eliminar un usuario de la plataforma
 * @return string
 */
function construirModalDeleteUser($codUsuario,$userName,$idDiv){
    global $wemp_pmla;
    $retorno = '<div class="panel panel-info contenido" style="margin-left: 0">
    <div class="panel-heading encabezado"> 
        <div class="panel-title titulo1" align="center">ELIMINAR USUARIO</div>
    </div>

    <form method="post" action="trazProcess.php?wemp_pmla='.$wemp_pmla.'" name="formUpdate" onsubmit="return validarCampos()">
        <table align="center">
            <tr>
                <td align="left">
                    <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 10px">
                        <span class="input-group-addon input-sm"><label for="codUsuarioModificar">CODIGO USUARIO:</label></span>
                        <input type="text" id="codusu" name="codusu" class="form-control form-sm" style="width: 100px" value="'.$codUsuario.'" readonly>

                        <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 5px"></span>

                        <span class="input-group-addon input-sm"><label for="nombreUsuarioModificar">&ensp;NOMBRE:</label></span>
                        <input type="text" id="nombreUsuarioModificar" name="nombreUsuarioModificar" class="form-control form-sm" style="width: 300px" value="'.$userName.'" readonly>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="hidden" id="accion" name="accion" value="deleteUser">
                    <input type="hidden" id="wemp_pmla" name="wemp_pmla" value="'."$wemp_pmla".'">
                    <!--input type="hidden" id="codusu" name="codusu" value="<?php //echo $codUsuario ?>"-->
                    <div id="'."$idDiv".'" class="input-group" style="margin-top: 10px; text-align: center; display: block;">
                        <input type="button" class="btn btn-danger btn-sm" value="Eliminar" title="Eliminar Usuario" style="width: 120px" 
                        onclick="deleteUser(\''.$codUsuario.'\',\''.$idDiv.'\')">
                        <div id="'."$idDiv".'App" style="margin-top: 5px">
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </form>
</div>';
    return $retorno;
}

/*******************************
 * Funcion para consultar el nombre de una unidad por centro de costos
 * @return nombre_unidad
 */
function datosUnidadxCco2($ccoUnidad,$conex,$parametro)
{
    global $wemp_pmla;
    // Centro de costos que se adiciona virtual para que puedan diferenciar entre cirugia general y cardio
    if($ccoUnidad == '10162')
    {
        return 'CIRUGIA CARDIO';
    }
    else
    {

        switch($parametro)
        {
            case 1: 
                $bdMovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
                $query1 = "SELECT Cconom FROM {$bdMovhos}_000011 WHERE Ccocod = '$ccoUnidad' AND Ccocen = 'on'";
                $commitQuery1 = mysql_query($query1, $conex) or die (mysql_errno()." - en el query: ".$query1." - ".mysql_error());
                $datoUnidad = mysql_fetch_array($commitQuery1);
                $nombreUnidad = $datoUnidad[0];
                if ($ccoUnidad == '1202' && $nombreUnidad == '' )$nombreUnidad = 'RT-UNIDAD RADIOTERAPIA-BRAQUITERAPIA';
                return $nombreUnidad;
                break;
            case 2: 
                //conectarse a matrix financiero
                break;
        }
        

    }
}

/*******************************
 * Funcion que retorna el nombre del la unidad de acuerdo al centro de costos 
 */
function datosUnidadxCco($ccoUnidad,$conex)
{
    global $wemp_pmla;
    
        $bdMovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
        $query1 = "SELECT Cconom FROM {$bdMovhos}_000011 WHERE Ccocod = '$ccoUnidad' AND Ccocen = 'on'";
        $commitQuery1 = mysql_query($query1, $conex) or die (mysql_errno()." - en el query: ".$query1." - ".mysql_error());
        $datoUnidad = mysql_fetch_array($commitQuery1);
        $nombreUnidad = $datoUnidad[0];
        if ($ccoUnidad == '1202' && $nombreUnidad == '' )$nombreUnidad = 'RT-UNIDAD RADIOTERAPIA-BRAQUITERAPIA';
        if ($ccoUnidad == '10162')$nombreUnidad = 'CIRUGIA CARDIO';
        return $nombreUnidad;
    
}

/*******************************
 * 
 */
function dispoxCco($ccoUnidad,$conex)
{
    global $bdCenest;
    $query2 = "SELECT * FROM {$bdCenest}_000011 WHERE Codcco = '$ccoUnidad' AND Estado = 'on'";
    $commitQuery2 = mysql_query($query2, $conex) or die (mysql_errno()." - en el query: ".$query2." - ".mysql_error());
    $datoDispo = mysql_fetch_array($commitQuery2);
}

/*******************************
 * Funcion que realiza un conteo de la trazabilidad por centro de costo en determinadas fechas
 * @return Conteo Reusos trazabilidad
 */
function reusosxServicio($cCostos,$conex,$fecIniReporte,$fecFinReporte)
{
    global $bdCenest;
    $queryRexSer = "SELECT count(id) CONTEO FROM {$bdCenest}_000014 WHERE Ccostos = '$cCostos' AND Fechauso BETWEEN '$fecIniReporte' AND '$fecFinReporte'";
    $commRexSer = mysql_query($queryRexSer, $conex) or die (mysql_errno()." - en el query: ".$queryRexSer." - ".mysql_error());
    $datoRexSer = mysql_fetch_array($commRexSer);

    $contRexSer = $datoRexSer['CONTEO'];
    echo $contRexSer;
}

/*******************************
 * Funcion que retorna un codigo de reuso determinado
 */
function datosReuso($codReu13,$conex)
{
    global $bdCenest;
    $query22 = "SELECT * FROM {$bdCenest}_000012 WHERE id = '$codReu13'";
    $commitQuery22 = mysql_query($query22, $conex) or die (mysql_errno()." - en el query: ".$query22." - ".mysql_error());
    $datoReuso = mysql_fetch_array($commitQuery22);
    $Codreuso = $datoReuso['Codreuso'];
    return $Codreuso;
}

/*******************************
 * funcion que regresa el nombre de usuario X codigo matrix
 */
function datosUsrMtx($funEnt13,$conex)
{
    $query22 = "SELECT * FROM usuarios WHERE Codigo = '$funEnt13'";
    $commitQuery22 = mysql_query($query22, $conex) or die (mysql_errno()." - en el query: ".$query22." - ".mysql_error());
    $datoUsuario = mysql_fetch_array($commitQuery22);
    $nomUsrMtx = $datoUsuario['Descripcion'];
    if ($nomUsrMtx != '')return $nomUsrMtx;
    else return $funEnt13;
}

/*******************************
 * FUNCION que consulta si hay reusos en ese centro de costos y rango de fechas
 */
function existeReuso($ccoUnidad,$conex,$fecIniReporte,$fecFinReporte)
{
    global $bdCenest;
    $query2 = "SELECT count(id) CONTEO FROM {$bdCenest}_000014 WHERE Ccostos = '$ccoUnidad' AND Fechauso BETWEEN '$fecIniReporte' AND '$fecFinReporte'";
    $commitQuery2 = mysql_query($query2, $conex) or die (mysql_errno()." - en el query: ".$query2." - ".mysql_error());
    $datoDispo = mysql_fetch_array($commitQuery2);

    $existeReuso = $datoDispo['CONTEO'];
    return $existeReuso;
}

/*******************************
 * Funcion que elabora la tabla de los usuarios y los botones que abren los modales para cada usuario
 */
function getUsuariosPrioridad(){
    global $bdCenest;
    global $conex;
    global $wemp_pmla;
    $matrixClass = 'Fila2';
    $tabla = '';
    $cntAccion = 0;
    $q = "SELECT Codigo, Descripcion, Prioridad, Activo FROM {$bdCenest}_000015 ORDER BY Descripcion";
    $res = mysql_query($q, $conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
    while($usuariosPrioridad = mysql_fetch_array($res)){
        $matrixClass =  $matrixClass == 'Fila2' ? 'Fila1' : 'Fila2';
        $tabla .= '<tr class="'.$matrixClass.'">';
        $tabla .= "<td class='text-center'>" . $usuariosPrioridad['Codigo'] . "</td>";
        $tabla .= "<td>" . $usuariosPrioridad['Descripcion'] . "</td>";
        $tabla .= "<td class='text-center'>" . $usuariosPrioridad['Prioridad'] . "</td>";
        $tabla .= "<td class='text-center'>" . $usuariosPrioridad['Activo'] . "</td>";
        $varAccion = "usuariosx";
        $varAccion2 = "usuariosy";
        $varModalUpdate = "modalUpdate";
        $varModalDelete = "modalDelete";
        $varState = "userStateUpdate";
        $varPrior = "userPriorUpdate";
        $varDivUpdate = "divUpdateUser";
        $varDivDelete = "divDeleteUser";
        $varAccion .=  $cntAccion;
        $varAccion2 .=  $cntAccion;
        $varModalUpdate .=  $cntAccion;
        $varModalDelete .=  $cntAccion;
        $varState .=  $cntAccion;
        $varPrior .=  $cntAccion;
        $varDivUpdate .=  $cntAccion;
        $varDivDelete .=  $cntAccion;
        $cntAccion++;
        // SI LA PRIORIDAD ES DIFERENTE A LA DEL SUPER USUARIO
        if ($usuariosPrioridad['Prioridad'] != '10'){
            $tabla .= '<td align="center">
                        <input type="hidden" id="'.$varAccion.'" name="'.$varAccion.'" value="modificarUsuario">
                        <input type="button" class="btn btn-warning btn-sm" value="M" title="Modificar Permisos de Usuario" style="width: -1px;"
                        onclick="showModal(\''.$varModalUpdate.'\')">
                        <input type="hidden" id="'.$varAccion2.'" name="'.$varAccion2.'" value="eliminarUsuario">
                        <input type="button" class="btn btn-danger btn-sm" value="X" title="Eliminar Usuario" style="width: -1px;"
                        onclick="showModal(\''.$varModalDelete.'\')">
                <div class="modal fade bs-example-modal-lg" id="'.$varModalUpdate.'" class="text-center" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" style="width:100%">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="removerClase(\''.$varDivUpdate.'App\')"><span aria-hidden="true">&times;</span></button>
                                    ' . construirModalUpdateUser("{$usuariosPrioridad['Codigo']}","{$usuariosPrioridad['Descripcion']}","{$varState}","{$varPrior}","{$varDivUpdate}"). '
                            </div>
                            <div class="modal-body" id="modalBodyUpdate">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade bs-example-modal-lg" id="'.$varModalDelete.'" class="text-center" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" style="width:100%">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"  onclick="removerClase(\''.$varDivDelete.'App\')"><span aria-hidden="true">&times;</span></button>
                                    ' . construirModalDeleteUser("{$usuariosPrioridad['Codigo']}","{$usuariosPrioridad['Descripcion']}","{$varDivDelete}"). '
                            </div>
                            <div class="modal-body" id="modalBodyUpdate">
                            </div>
                        </div>
                    </div>
                </div>
            </td>';
        }else{
            $tabla .= '<td align="center">
                    <input type="hidden" id="'.$varAccion.'" name="'.$varAccion.'" value="modificarUsuario">
                    <input type="button" class="btn btn-warning btn-sm" value="M" title="Modificar Usuario" style="width: -1px;"
                    onclick="modificarUsuario(\''. $usuariosPrioridad['Codigo'] .'\','.$varAccion.',\''.$wemp_pmla.'\')" disabled>
                    <input type="hidden" id="'.$varAccion2.'" name="'.$varAccion2.'" value="eliminarUsuario">
                    <input type="button" class="btn btn-danger btn-sm" value="X" title="Eliminar Usuario" style="width: -1px;"
                    onclick="eliminarUsuario(\''. $usuariosPrioridad['Codigo'] .'\',\''. $usuariosPrioridad['Descripcion'] .'\','.$varAccion2.',\''.$wemp_pmla.'\')" disabled>
        </td>';
        }
            $tabla .= "</tr>";
    }
    echo $tabla;
}

/*******************************
 * Funcion que construye la tabla de los centros de costos asociados a la app
 */
function getCcoCenest(){
    global $bdMovhos;
    global $conex;
    $matrixClass = 'Fila2';
    $tabla = '';
    $cntAccion = 0;
    $q = "SELECT Ccocod, Cconom, Ccocen FROM {$bdMovhos}_000011 WHERE Ccocen = 'on'";
    $res = mysql_query($q, $conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
    while($ccoCenest = mysql_fetch_array($res)){
        $matrixClass =  $matrixClass == 'Fila2' ? 'Fila1' : 'Fila2';
        $tabla .= '<tr class="'.$matrixClass.'">';
        $tabla .= "<td class='text-center'>" . $ccoCenest['Ccocod'] . "</td>";
        $tabla .= "<td>" . $ccoCenest['Cconom'] . "</td>";
        $tabla .= "<td class='text-center'>" . $ccoCenest['Ccocen'] . "</td>";;
        $varAccion = "ccox";
        $varModalDelete = "modalDeleteCco";
        $varDivDelete = "divDeleteCco";
        $varAccion .=  $cntAccion;
        $varModalDelete .=  $cntAccion;
        $varDivDelete .=  $cntAccion;
        $cntAccion++;
            $tabla .= '<td align="center">
                        <input type="hidden" id="'.$varAccion.'" name="'.$varAccion.'" value="EliminarCco">
                        <input type="button" class="btn btn-danger btn-sm" value="X" title="Eliminar Centro de Costos" style="width: -1px;"
                        onclick="showModal(\''.$varModalDelete.'\')">
                <div class="modal fade bs-example-modal-lg" id="'.$varModalDelete.'" class="text-center" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" style="width:100%">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"  onclick="removerClase(\''.$varDivDelete.'App\')"><span aria-hidden="true">&times;</span></button>
                                    ' . construirModalDeleteCco("{$ccoCenest['Ccocod']}","{$ccoCenest['Cconom']}","{$varDivDelete}"). '
                            </div>
                            <div class="modal-body" id="modalBodyDeleteCco">
                            </div>
                        </div>
                    </div>
                </div>
            </td>';
       
            $tabla .= "</tr>";
    }
    echo $tabla;
}

/*******************************
 * Funcion que valida las entidades html de un array para que se puedan imprimir bien
 * @return array
 */
function validateHtmlEntities($data = array()){
    $arrayValidate = array();
    foreach($data as $key => $value){
        $arrayValidate[$key] = htmlentities($value);
    }
    return $arrayValidate;
}

/*******************************
 * Funcion que valida la abreviatura de centro de costos para la creacion de codigos de reuso
 */
function validateAbrNameXCco($cco){
    $arrayCcoNom = array(
        '1130' => 'URG',
        '1020' => 'UCI',
        '1330' => 'HX',
        '1016' => 'CX',
        '1030' => 'RX',
        '10162' => 'CV',
        '1260' => 'END',
        '1320' => 'NEUMO',
        '1195' => 'undefined', //INSTITUTO DE LA MUJER TODO todavia no
        '1075' => 'MF',
        '1202' => 'BX', //IDC BRAQUITERAPIA
        '1335' => 'undefined', //ELECTROFISIOLOGIA TODO todavia no
        '1610' => 'undefined', //Mantenimiento no tiene codificacion
        '1190' => 'NX' //NEONATOS
    );
    if (isset($arrayCcoNom[$cco]))return $arrayCcoNom[$cco];
    else return 'undefined';
}

/*******************************
 * Funcion que se encarga de generar un codigo de reuso nuevo 
 * con nomenclatura de letras a 3 letras hasta ZZZ
 */
function generateNewCod($codIn){
    $codIn = strtoupper($codIn);
    if (strpos($codIn,'.')){
        $delimiter = '.';
    }else if (strpos($codIn,'-')){
        $delimiter = '-';
    } else return false;
    list($parte1,$parte2,$parte3) = explode($delimiter,$codIn);
    $parte3 = str_replace(' ', '', $parte3);
    list($num,$letras) = preg_split('/(?<=[0-9])(?=[a-z]+)/i', $parte3);
    if ($letras != ''){
        $numLet = strlen($letras);
        if ($numLet == 1){
            if ($letras == 'Z'){
                return trim(substr($codIn, 0, -1)) . 'AA';
            }else{
                $nextChar = chr(ord($letras) + 1);
                return trim(substr($codIn, 0, -1)) .  $nextChar;  
            }
        }else if ($numLet == 2){
            list($letra1,$letra2) = str_split($letras);
            if ($letra1 == 'Z' && $letra2 == 'Z') return trim(substr($codIn, 0, -2)) . 'AAA';;
            if ($letra1 != 'Z' && $letra2 != 'Z'){
                $nextChar = chr(ord($letra2) + 1);
                return  trim(substr($codIn, 0, -2)) . $letra1 .  $nextChar; 
            } else if ($letra1 != 'Z' && $letra2 == 'Z'){
                $nextChar = chr(ord($letra1) + 1);
                return  trim(substr($codIn, 0, -2)) . $nextChar . 'A'; 
            } else if($letra1 == 'Z' && $letra2 != 'Z'){
                $nextChar = chr(ord($letra2) + 1);
                return  trim(substr($codIn, 0, -2)) . $letra1 . $nextChar;
            }else return false;
        }else if ($numLet == 3){
            list($letra1,$letra2,$letra3) = str_split($letras);
            if ($letra1 == 'Z' && $letra2 == 'Z' && $letra3 == 'Z') return false;
            if ($letra1 != 'Z' && $letra2 != 'Z' && $letra3 != 'Z'){
                $nextChar = chr(ord($letra3) + 1);
                return  trim(substr($codIn, 0, -3)) . $letra1 . $letra2 . $nextChar; 
            } else if ($letra1 != 'Z' && $letra2 != 'Z' && $letra3 == 'Z'){
                $nextChar = chr(ord($letra2) + 1);
                return  trim(substr($codIn, 0, -3)) . $letra1 . $nextChar . 'A'; 
            } else if ($letra1 != 'Z' && $letra2 == 'Z' && $letra3 != 'Z'){
                $nextChar = chr(ord($letra3) + 1);
                return  trim(substr($codIn, 0, -3)) . $letra1 . $letra2 . $nextChar; 
            } else if ($letra1 == 'Z' && $letra2 != 'Z' && $letra3 != 'Z'){
                $nextChar = chr(ord($letra3) + 1);
                return  trim(substr($codIn, 0, -3)) . $letra1 . $letra2 . $nextChar; 
            } else if ($letra1 != 'Z' && $letra2 == 'Z' && $letra3 == 'Z'){
                $nextChar = chr(ord($letra1) + 1);
                return  trim(substr($codIn, 0, -3)) . $nextChar . 'AA'; 
            } else if ($letra1 == 'Z' && $letra2 == 'Z' && $letra3 != 'Z'){
                $nextChar = chr(ord($letra3) + 1);
                return  trim(substr($codIn, 0, -3)) . $letra1 . $letra2 . $nextChar;
            } else if ($letra1 == 'Z' && $letra2 != 'Z' && $letra3 == 'Z'){
                $nextChar = chr(ord($letra2) + 1);
                return  trim(substr($codIn, 0, -3)) . $letra1 . $nextChar . 'A';
            }else return false;
        }else return false;
    }else{
        return $codIn . 'A';
    }
    return false;
}

/*****************************
 * funcion que crea un nuev codigo simple de reuso
 * **/
function newcod($codReuso,$delimiter){
    list($parte1,$parte2,$parte3) = explode($delimiter,$codReuso);
    $lastReu = $parte3 + 1;
        if ($lastReu > 0 and $lastReu < 10){
            $lastReu = '0'.$lastReu;
        }
    $fin = $parte1 .$delimiter. $parte2 . $delimiter . $lastReu;
    return $fin;
}

/*******************************
 * Funcion que crea el select de los usuarios que tienen acceso a la plataforma
 */
function createSelectUsers(){
    global $conex;
    global $bdCenest;
    $htmlreturn = '<option value="">Seleccione...</option>';
    $qryResEsteril = "SELECT Codigo, Descripcion FROM {$bdCenest}_000015 ORDER BY Descripcion ASC";
    $commQryEsteril = mysql_query($qryResEsteril, $conex) or die (mysql_errno()." - en el query: ".$qryResEsteril." - ".mysql_error());
    while($datoRespEst = mysql_fetch_assoc($commQryEsteril))
    {
        $codRespEsteril = $datoRespEst['Codigo'];  
        $nomRespEsteril = $datoRespEst['Descripcion'];
        $htmlreturn .= "<option value='".$codRespEsteril."'>".$nomRespEsteril."</option>";
    }
    return $htmlreturn;
}

/*******************************
 * Funcion que crea el select de los centros de costos asociados a la plataforma
 */
function createSelectCco(){
    global $conex;
    global $bdMovhos;
    $ccoBraquiterapia = '1202';
    $ccoNomBraquiterapia = 'RT-UNIDAD RADIOTERAPIA-BRAQUITERAPIA';
    $ccoCirugiaCardio = '10162';
    $ccoNomCirugiaCardio = 'CIRUGIA CARDIO';
    $braquiterapiaAux = false;
    $queryCco = "SELECT Ccocod,Cconom FROM {$bdMovhos}_000011 WHERE Ccocen = 'on' ORDER BY Cconom ASC";
    $commitCco = mysql_query($queryCco, $conex) or die (mysql_errno()." - en el query: ".$queryCco." - ".mysql_error());
    while($datoempresa = mysql_fetch_assoc($commitCco))
    {
        $codigoCco = $datoempresa['Ccocod'];    $nombreCco = $datoempresa['Cconom'];
        echo "<option value='".$codigoCco."'>".$codigoCco.' - '.$nombreCco."</option>";
        if ($codigoCco == $ccoBraquiterapia )$braquiterapiaAux = true;
    }
    if (!$braquiterapiaAux)echo "<option value='".$ccoBraquiterapia."'>".$ccoBraquiterapia.' - '.$ccoNomBraquiterapia."</option>";
    echo "<option value='".$ccoCirugiaCardio."'>".$ccoCirugiaCardio.' - '.$ccoNomCirugiaCardio."</option>";
}

/*******************************
 * Funcion que identifica si es dispositivo malo o infectado
 * @return 0 si no hay novedad
 * @return 1 si esta malo 
 * @return 2 si esta infectado
 */
function esDispoMaloOfinfec($id,$cco){
    global $conex;
    global $bdCenest;
    $dispositivoMaloOInfectado = 0;
    $q = "SELECT novDispo FROM {$bdCenest}_000014 WHERE Ccostos = '{$cco}' AND Codigo = '{$id}'";
    $res = mysql_query($q, $conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
    while($datos = mysql_fetch_assoc($res)){
        if ($datos['novDispo'] == 'MALO'){
            $dispositivoMaloOInfectado = 1;
            return $dispositivoMaloOInfectado;
        }else if ($datos['novDispo'] == 'PACIENTE INFECTADO'){
            $dispositivoMaloOInfectado = 2;
            return $dispositivoMaloOInfectado;
        }else{
            $dispositivoMaloOInfectado = 0;
        }
    }
    return $dispositivoMaloOInfectado;
}

/*******************************
 * Funcion que imprime el nombre del usuario que se logueo
 */
function userLogin(){
    global $conex;
    if (isset($_SESSION['user'])) {
		$user = $_SESSION['user'];
		$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
	}
    echo datosUsrMtx($wuser,$conex);
}

/*******************************
 * Funcion que retorna el nombre del usuario que se logueo
 */
function returnUserLogin(){
    global $conex;
    if (isset($_SESSION['user'])) {
		$user = $_SESSION['user'];
		$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
	}
    return datosUsrMtx($wuser,$conex);
}

/*******************************
 * Funcion que retorna el codigo de matrix de la sesion
 */
function wUser(){
    global $conex;
    if (isset($_SESSION['user'])) {
		$user = $_SESSION['user'];
		$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
	}
    return $wuser;
}

/*******************************
 * Funcion que retorna el nombre del usuario desde el campo seguridad
 */
function datosUsuarioXseg($user){
    global $conex;
    $wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
    return datosUsrMtx($wuser,$conex);
}

/*******************************
 * Funcion que arma la tabla para el resumen en los reportes de acuerdo a los parametros de consulta
 */
function buildResumeTable($fecIniReporte, $fecFinReporte, $filtroCco, $filtroDispo, $filtroCodReu){
    global $bdCenest;
    global $conex;
    global $wemp_pmla;
    $matrixClass = 'Fila2';
    $tabla = '';
    $cntAccion = 0;
    $htmlreturn = '';
    $adQuery = '';
    $queryJoin = '';
    $selectJoin = '';
    $codReuso = '';
    $datosArray = array();
    //se arma el query de acuerdo a los parametros de consulta enviados
    $idModalVerReuso = 'modalVerReuso';
    if ($filtroCco != 'TODOSCCO' && $filtroCco != null){
        $conteoReCard = existeReuso($filtroCco,$conex,$fecIniReporte,$fecFinReporte);
        $adQuery = "C.Ccostos = '$filtroCco' AND ";
        if ($filtroDispo != 'TODOSDISPO' && $filtroDispo != null){
            $adQuery .= " A.Coddispo = '$filtroDispo' AND ";
            $selectJoin .= ", A.Codreuso ";
            $queryJoin .= " INNER JOIN {$bdCenest}_000012 A
                            ON C.Codigo = A.id ";
            if ($filtroCodReu != 'TODOSREUSOS' && $filtroCodReu != null){
                list($codReusoaux,$idReuso) = explode("_",$filtroCodReu);
                $adQuery .= " A.id = '$idReuso' AND ";
            }
        }
    }else {
        $conteoReCard = 1;
    }
    if ($conteoReCard > 0){

        $q = "SELECT C.Codigo, C.id, C.Fechauso, C.Nomusuario, C.Docusuario, C.NumQuirofano, C.Observacion, C.UserServicio {$selectJoin} 
         FROM {$bdCenest}_000014 C {$queryJoin} WHERE {$adQuery} C.Fechauso BETWEEN '$fecIniReporte' AND '$fecFinReporte'";
        $res = mysql_query($q, $conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
        if (mysql_num_rows($res) > 0){
            while($reporteReusos = mysql_fetch_array($res)){
                $idModalVerReuso .= $cntAccion;
                $codReuso = datosReuso($reporteReusos['Codigo'],$conex);
                $datosArray['datos']['id'] = $reporteReusos['id'];
                $datosArray['datos']['CodReuso'] = $codReuso;
                $datosArray['datos']['idModal'] = $idModalVerReuso;
                $matrixClass =  $matrixClass == 'Fila2' ? 'Fila1' : 'Fila2';
                $htmlreturn .= '<tr class="'.$matrixClass.'">';
                $htmlreturn .= "<td class='text-center'>" . $codReuso . "</td>";
                $htmlreturn .= "<td class='text-center'>" . $reporteReusos['Fechauso'] . "</td>";
                $htmlreturn .= "<td class='text-center'>" . $reporteReusos['Nomusuario'] . "</td>";
                $htmlreturn .= "<td class='text-center'>" . $reporteReusos['Docusuario'] . "</td>";
                $htmlreturn .= "<td class='text-center'>" . $reporteReusos['NumQuirofano'] . "</td>";
                $htmlreturn .= "<td class='text-center'>" . $reporteReusos['Observacion'] . "</td>";
                $htmlreturn .= "<td class='text-center'>" . substr(datosUsrMtx($reporteReusos['UserServicio'],$conex), 0, 25) . ".." . "</td>";
                $htmlreturn .= "<td class='text-center'>";
                $htmlreturn .= '<input type="button" class="btn btn-info btn-sm" value="V" title="Ver Reuso" style="width: -1px"';
                $htmlreturn .= "onclick='buildModalHeader(".json_encode($datosArray,JSON_UNESCAPED_SLASHES).",\"VerReuso\")'>";
                $htmlreturn .= '<div class="modal fade bs-example-modal-lg" id="'.$idModalVerReuso.'" class="text-center" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" style="width:100%">
                                </div>';
                $htmlreturn .= "</td>";
                $htmlreturn .= "</tr>";
                $cntAccion ++;
            }
        }else{
            return false;
        }
    }else{
        return false;
    }
    return $htmlreturn;
}

/*******************************
 * Funcion que construye el select de los reusos por codigo del dispositivo
 */
function findReusosXCod($Coddispo,$Codcco){
    global $bdCenest;
    global $conex;
    $query22 = "SELECT * FROM {$bdCenest}_000012 WHERE Coddispo = '$Coddispo' AND Codcco = '$Codcco'";
    $commitQuery22 = mysql_query($query22, $conex) or die (mysql_errno()." - en el query: ".$query22." - ".mysql_error());
    while($datoReuso = mysql_fetch_array($commitQuery22))
    {
        $codReuso = $datoReuso['Codreuso']; $idReuso = $datoReuso['id'];
        $usos = $datoReuso['Numuso'];     $limiteUsos = $datoReuso['limite'];
            echo "<option value='".$codReuso.'_'.$idReuso."'>".$codReuso."</option>";
    }
}

function codigosMostrarTraz($codIt,$ccoUnidad){
    global $bdCenest;
    global $conex;
    $arrCodigosMostrar = [];
    $query22 = "SELECT Codreuso,Numuso,limite,id 
                FROM {$bdCenest}_000012
                WHERE Coddispo = '$codIt' AND Estado = 'on' AND Codcco = '$ccoUnidad'";
    $commitQuery22 = mysql_query($query22, $conex) or die (mysql_errno().mysql_error());
    while($datoReuso = mysql_fetch_array($commitQuery22))
    {
        $codReuso = $datoReuso['Codreuso']; $idReuso = $datoReuso['id'];
        $usos = $datoReuso['Numuso'];     $limiteUsos = $datoReuso['limite'];
        if($usos < $limiteUsos && esDispoMaloOfinfec($idReuso,$ccoUnidad) == 0)
        {
            $arrCodigosMostrar[$idReuso] = $codReuso;
        }
    }
    return $arrCodigosMostrar;
}

?>