<?php
    //Variables de sesión
    //session_start();

    //Agrego el controlador de medida
    require_once("./Controller/medidaController.php");

    //Inicio la clase
    $oMedidaController = new medidaController();

    //Valido si envío parámetro "action" por URL
    if(isset($_REQUEST["action"])){
        //Defino las acciones que se realizarán
        switch ($_REQUEST["action"]) {
            case 'createMedida':
                $oMedidaController->create();
                break;
            
            case 'createMedidaxPersona':
                $oMedidaController->createMedidaxPersona();
                break;

            case 'listMedidaxPersona':
                $oMedidaController->listMedidaxPersona();
                break;

            case 'buscarPersona':
                /** Se inicializa el bufer de salida de php **/
                ob_start();
                /** Limpiamos el buffer de salida de php para no retornar los datos de los "echos" que se hacen en los include **/
                ob_end_clean();
                $oMedidaController->buscarPersona();
                break;

            case 'informeMedidaPersona':
                $oMedidaController->informeMedidaxPersona();
                break;

            case 'listMedidas':
                $oMedidaController->index();
                break;
            
            case 'editMedidaPersona':
                $oMedidaController->editMedidaxPersona();
                break;
            
            case 'deleteMedidaPersona':
                $oMedidaController->deleteMedidaxPersona();
                break;

            case 'buscarCentroCosto':
                $oMedidaController->buscarCentrodeCosto();
                break;

            default:
                $oMedidaController->index();
                break;
        }
    } else {
        $oMedidaController->index();
    }


?>