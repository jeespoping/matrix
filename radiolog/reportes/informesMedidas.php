<?php
    //Variables de sesión
    //session_start();

    //Agrego el controlador de medida
    require_once("./Controller/informeMedidaController.php");

    //Inicio la clase
    $oInformeMedidaController = new informeMedidaController();

    //Valido si envío parámetro "action" por URL
    if(isset($_REQUEST["action"])){
        //Defino las acciones que se realizarán
        switch ($_REQUEST["action"]) {
            case 'informeNotificaciones':
                $oInformeMedidaController->listNotificacionesMedida();
                break;
            
            case 'informeBusquedas':
                $oInformeMedidaController->listBusquedasMedidasPorPersona();
                break;
            
            case 'informeTotalMedidas':
                $oInformeMedidaController->listTotalMedidasPorPersona();
                break;
                

            default:
                $oInformeMedidaController->listNotificacionesMedida();
                break;
        }
    } else {
        $oInformeMedidaController->listNotificacionesMedida();
    }


?>