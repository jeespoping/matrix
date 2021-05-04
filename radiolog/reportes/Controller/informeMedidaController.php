<?php

    //Clase
    include_once("radiolog/medida.php");

    /***
     * Creo una clase para el controlador de Informes de Medida
     * @by: sebastian.nevado
     * @date: 2021/05/03
     ***/
    class informeMedidaController
    {

        /**
         * Funcion para listado de medidas por persona
         * @by: sebastian.nevado
         * @date: 2021/05/03
         */
        public function listNotificacionesMedida()
        {
            //Obtengo el parámetro
            $wemp_pmla = isset($_GET["wemp_pmla"]) ? $_GET["wemp_pmla"] : $_POST["wemp_pmla"]  ;
            
            //Creo la variable medida
            $oMedida = new Medida($wemp_pmla);

            // Proceso si doy aceptar
            if ( isset($_POST['buscar']) )
            {

                //Valido existencia de datos
                $sValorBusqueda = isset($_POST["codigopersona"]) ? $_POST["codigopersona"] : null;
                $sTipoBusqueda = isset($_POST["tipobusqueda"]) ? $_POST["tipobusqueda"] : null;
                $iIdMedida = isset($_POST['idmedida']) ? $_POST['idmedida'] : null;
                $sUsuario = $_SESSION['usera'];
                $sSeguridad = "C-".$sUsuario;

                //Data validations
                $oMedida->setId($iIdMedida);
                $oMedida->setSeguridad($sSeguridad);

                //Guardo y manejo los mensajes
                $aNotificaciones = $oMedida->getAllNotificaciones($sTipoBusqueda, $sValorBusqueda, $iIdMedida);

                //Limpio variables de sesión
                unset($_SESSION['idmedida']);
                unset($_SESSION['codigopersona']);
                unset($_SESSION['tipobusqueda']);

                //Seteo la variable de respuesta
                $_SESSION["success"] = $oMedida->getMensaje();
                $_SESSION["success"] .= (count($aNotificaciones) == 0) ? ". No se encontraron registros con ".$sTipoBusqueda." ".$sValorBusqueda."." : ".";

                //Obtengo el listado de medidas
                $aMedidas = $oMedida->getAll();

                //Llamo a la vista
                require("./View/informeNotificaciones.php");
                
                return;
            }
            else
            {
                //Obtengo el listado de notificaciones
                $aNotificaciones = $oMedida->getAllNotificaciones();

                //Obtengo el listado de medidas
                $aMedidas = $oMedida->getAll();
                
                //Llamo a la vista
                require("./View/informeNotificaciones.php");

                //Limpio variables de sesión
                unset($_SESSION['idmedida']);
                unset($_SESSION['codigopersona']);
                unset($_SESSION['tipobusqueda']);
            }

        }

        /**
         * Funcion para listado de medidas por persona
         * @by: sebastian.nevado
         * @date: 2021/05/03
         */
        public function listBusquedasMedidasPorPersona()
        {
            //Obtengo el parámetro
            $wemp_pmla = isset($_GET["wemp_pmla"]) ? $_GET["wemp_pmla"] : $_POST["wemp_pmla"]  ;
            
            //Creo la variable medida
            $oMedida = new Medida($wemp_pmla);

            //Obtengo el listado de medidas
            $aMedidas = $oMedida->getAll();

            // Proceso si doy aceptar
            if ( isset($_POST['buscar']) )
            {

                //Valido existencia de datos
                $sValorBusqueda = isset($_POST["codigopersona"]) ? $_POST["codigopersona"] : null;
                $sTipoBusqueda = isset($_POST["tipobusqueda"]) ? $_POST["tipobusqueda"] : null;
                $iIdMedida = isset($_POST['idmedida']) ? $_POST['idmedida'] : null;
                $sUsuario = $_SESSION['usera'];
                $sSeguridad = "C-".$sUsuario;

                //Data validations
                $oMedida->setId($iIdMedida);
                $oMedida->setSeguridad($sSeguridad);

                //Guardo y manejo los mensajes
                $aBusquedas = $oMedida->getAllConsultas($sTipoBusqueda, $sValorBusqueda, $iIdMedida);

                //Limpio variables de sesión
                unset($_SESSION['idmedida']);
                unset($_SESSION['codigopersona']);
                unset($_SESSION['tipobusqueda']);

                //Seteo la variable de respuesta
                $_SESSION["success"] = $oMedida->getMensaje();
                $_SESSION["success"] .= (count($aBusquedas) == 0) ? ". No se encontraron registros con ".$sTipoBusqueda." ".$sValorBusqueda."." : ".";

                //Llamo a la vista
                require("./View/informeConsultas.php");
                
                return;
            }
            else
            {
                //Obtengo el listado de notificaciones
                $aBusquedas = $oMedida->getAllConsultas();
                
                //Llamo a la vista
                require("./View/informeConsultas.php");

                //Limpio variables de sesión
                unset($_SESSION['idmedida']);
                unset($_SESSION['codigopersona']);
                unset($_SESSION['tipobusqueda']);

                return;
            }
        }
    }
?>