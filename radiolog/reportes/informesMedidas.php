<?php
    //Variables de sesión
    //session_start();

    //Agrego el controlador de medida
    require_once("./Controller/informeMedidaController.php");

    //Valido si envío parámetro "action" por URL
    if(isset($_REQUEST["action"])){
        //Defino las acciones que se realizarán
        switch ($_REQUEST["action"]) {
            case 'informeNotificaciones':
                informeMedidaController::listNotificacionesMedida();
                break;

            default:
                informeMedidaController::listNotificacionesMedida();
                break;
        }
    } else {
        informeMedidaController::listNotificacionesMedida();
    }


?>