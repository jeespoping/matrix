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

            $aNotificaciones = $oMedida->getAllNotificaciones();
            
            //Llamo a la vista
            require("./View/informeNotificaciones.php");

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

            $aMedidasPersonal = $oMedida->getAllMedidasxPersona();
            
            //Llamo a la vista
            require("./View/listarMedidaPersona.php");
        }
    }
?>