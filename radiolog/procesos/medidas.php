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
        case 'viewMedida':
            medidaController::view();
            break;
        case 'editMedida':
            medidaController::edit();
            break;
        case 'deleteMedida':
            medidaController::delete();
            break;

        default:
            medidaController::index();
            break;
    }
} else {
    medidaController::index();
}


?>