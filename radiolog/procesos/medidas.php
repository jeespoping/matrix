<?php
    //Variables de sesión
    //session_start();

    //Agrego el controlador de medida
    require_once("medidaController.php");

    //Valido si envío parámetro "action" por URL
    if(isset($_REQUEST["action"])){
        //Defino las acciones que se realizarán
        switch ($_REQUEST["action"]) {
            case 'createMedida':
                medidaController::create();
                break;
            case 'createMedidaxPersona':
                medidaController::createMedidaxPersona();
                break;

            default:
                medidaController::index();
                break;
        }
    } else {
        medidaController::index();
    }


?>