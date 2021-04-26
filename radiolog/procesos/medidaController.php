<?php

    //Clase
    require_once "medidaModel.php";

    /***
     * Creo una clase para el controlador de Medida
     * @by: sebastian.nevado
     * @date: 2021/04/22
     ***/
    class medidaController
    {
        
        /**
         * Funcion para el listado de medidas
         * @by: sebastian.nevado
         * @date: 2021/04/22
         */
        public function index()
        {
            //Obtengo el parámetro
            $wemp_pmla = isset($_GET["wemp_pmla"]) ? $_GET["wemp_pmla"] : $_POST["wemp_pmla"]  ;

            //Creo la variable medida
            $oMedida = new Medida($wemp_pmla);
            
            //Asigno los coches a una variable que estará esperando la vista
            $aMedidas = $oMedida->getAll();

            //Llamo a la vista
            require("listarMedidaView.php");
        }

        /**
         * Funcion para creación de medidas
         * @by: sebastian.nevado
         * @date: 2021/04/22
         */
        public function create()
        {
            //Obtengo el parámetro
            $wemp_pmla = isset($_GET["wemp_pmla"]) ? $_GET["wemp_pmla"] : $_POST["wemp_pmla"]  ;
            
            //Creo la variable medida
            $oMedida = new Medida($wemp_pmla);

            // Proceso si doy aceptar
            if ( isset($_POST['add']) )
            {

                //Valido existencia de datos
                $sCodigo = isset($_POST['codigo']) ? $_POST['codigo'] : null;
                $sNombre = isset($_POST['nombre']) ? $_POST['nombre'] : null;
                $sDescripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : null;
                $sIdUnidad = isset($_POST['unidad']) ? $_POST['unidad'] : null;
                $bEnviarNotificacion = !isset($_POST['enviarnotificacion']) ? false : true;
                $sUsuario = $_SESSION['usera'];
                $sSeguridad = "C-".$sUsuario;

                //Data validations
                $oMedida->setCodigo($sCodigo);
                $oMedida->setNombre($sNombre);
                $oMedida->setDescripcion($sDescripcion);
                $oMedida->setIdUnidad($sIdUnidad);
                $oMedida->setEnviarNotificacion($bEnviarNotificacion);
                $oMedida->setSeguridad($sSeguridad);

                //Guardo y manejo los mensajes
                if(!$oMedida->save())
                {
                    $_SESSION["error"] = $oMedida->getMensaje();
                    $_SESSION["codigo"] = $sCodigo;
                    $_SESSION["nombre"] = $sNombre;
                    $_SESSION["descripcion"] = $sDescripcion;
                    $_SESSION["unidad"] = $sIdUnidad;
                    $_SESSION["enviarnotificacion"] = ($bEnviarNotificacion) ? "checked" : "";
                    header("Location: medidas.php?wemp_pmla=".$wemp_pmla."&action=createMedida");
                    return;
                }

                //Limpio variables de sesión
                unset($_SESSION['codigo']);
                unset($_SESSION['nombre']);
                unset($_SESSION['descripcion']);
                unset($_SESSION['unidad']);
                unset($_SESSION['enviarnotificacion']);

                //Seteo la variable de respuesta
                $_SESSION["success"]="Medida guardada";
                header("Location: medidas.php?wemp_pmla=".$wemp_pmla);
                return;
            }
            else
            {
                //Llamo a la vista
                require("crearMedidaView.php");

                //Limpio variables de sesión
                unset($_SESSION['codigo']);
                unset($_SESSION['nombre']);
                unset($_SESSION['descripcion']);
                unset($_SESSION['unidad']);
                unset($_SESSION['enviarnotificacion']);
            }
        }

        /**
         * Funcion para creación de medidas
         * @by: sebastian.nevado
         * @date: 2021/04/26
         */
        public function createMedidaxPersona()
        {
            //Obtengo el parámetro
            $wemp_pmla = isset($_GET["wemp_pmla"]) ? $_GET["wemp_pmla"] : $_POST["wemp_pmla"]  ;
            
            //Creo la variable medida
            $oMedida = new Medida($wemp_pmla);

            // Proceso si doy aceptar
            if ( isset($_POST['add']) )
            {

                //Valido existencia de datos
                $iIdMedida = isset($_POST['idmedida']) ? $_POST['idmedida'] : null;
                $sCodigoPersona = isset($_POST['codigopersona']) ? $_POST['codigopersona'] : null;
                $dFechaMedida = isset($_POST['fechamedida']) ? $_POST['fechamedida'] : null;
                $dHoraMedida = isset($_POST['horamedida']) ? $_POST['horamedida'] : null;
                $dValorMedida = isset($_POST['valormedida']) ? $_POST['valormedida'] : null;
                $iIdMedidaxPersonal = isset($_POST['idmedidaxpersona']) ? $_POST['idmedidaxpersona'] : null;
                $bContinuarIngresando = !isset($_POST['seguiringresando']) ? false : true;
                $sUsuario = $_SESSION['usera'];
                $sSeguridad = "C-".$sUsuario;

                //Data validations
                $oMedida->setId($iIdMedida);
                $oMedida->setSeguridad($sSeguridad);

                //Guardo y manejo los mensajes
                if(!$oMedida->saveMedidaxPersona($sCodigoPersona, $dFechaMedida, $dHoraMedida, $dValorMedida, $iIdMedidaxPersonal))
                {
                    $_SESSION["error"]=$oMedida->getMensaje();
                    $_SESSION["idmedida"] = $iIdMedida;
                    $_SESSION["codigopersona"] = $sCodigoPersona;
                    $_SESSION["fechamedida"] = $dFechaMedida;
                    $_SESSION["horamedida"] = $dHoraMedida;
                    $_SESSION["valormedida"] = $dValorMedida;
                    $_SESSION["idmedidaxpersona"] = $iIdMedidaxPersonal;
                    header("Location: medidas.php?wemp_pmla=".$wemp_pmla."&action=createMedidaxPersona");
                    return;
                }

                //Limpio variables de sesión
                unset($_SESSION['idmedida']);
                unset($_SESSION['codigopersona']);
                unset($_SESSION['fechamedida']);
                unset($_SESSION['horamedida']);
                unset($_SESSION['valormedida']);
                unset($_SESSION['idmedidaxpersona']);

                //Seteo la variable de respuesta
                $_SESSION["success"]="Medida por personal guardada";
                if($bContinuarIngresando)
                {
                    header("Location: medidas.php?wemp_pmla=".$wemp_pmla."&action=createMedidaxPersona");
                }
                else
                {
                    header("Location: medidas.php?wemp_pmla=".$wemp_pmla);
                }
                return;
            }
            else
            {
                //Llamo a la vista
                require("crearMedidaPersonaView.php");

                //Limpio variables de sesión
                unset($_SESSION['codigo']);
                unset($_SESSION['nombre']);
                unset($_SESSION['descripcion']);
                unset($_SESSION['unidad']);
                unset($_SESSION['enviarnotificacion']);
            }
        }

    }
?>