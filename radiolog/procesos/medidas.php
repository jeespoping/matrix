<?php
    //Variables de sesión
    //session_start();

    //Agrego el controlador de medida
    require_once("./Controller/medidaController.php");

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

            case 'listMedidaxPersona':
                medidaController::listMedidaxPersona();
                break;

            case 'buscarPersona':
                /** Se inicializa el bufer de salida de php **/
                ob_start();
                /** Limpiamos el buffer de salida de php para no retornar los datos de los "echos" que se hacen en los include **/
                ob_end_clean();
                medidaController::buscarPersona();
                break;

            case 'informeMedidaPersona':
                medidaController::informeMedidaxPersona();
                break;

            default:
                medidaController::index();
                break;
        }
    } else {
        medidaController::index();
    }


?>