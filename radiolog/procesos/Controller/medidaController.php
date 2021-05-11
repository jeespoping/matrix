<?php

    //Clase
    //require_once("../../../include/radiolog/medida.php");
    include_once("radiolog/medida.php");

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
            require("./View/listarMedida.php");
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
                $sCodigo = isset($_POST['codigomedida']) ? $_POST['codigomedida'] : null;
                $sNombre = isset($_POST['nombre']) ? $_POST['nombre'] : null;
                $sDescripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : null;
                $sIdUnidad = (isset($_POST['unidad']) && ($_POST['unidad'] != '')) ? $_POST['unidad'] : "NULL";
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
                    $_SESSION["codigomedida"] = $sCodigo;
                    $_SESSION["nombre"] = $sNombre;
                    $_SESSION["descripcion"] = $sDescripcion;
                    $_SESSION["unidad"] = $sIdUnidad;
                    $_SESSION["enviarnotificacion"] = ($bEnviarNotificacion) ? "checked" : "";
                    header("Location: medidas.php?wemp_pmla=".$wemp_pmla."&action=createMedida");
                    return;
                }

                //Limpio variables de sesión
                unset($_SESSION['codigomedida']);
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
                //Listado de unidades
                $aUnidades = $oMedida->getUnidades();

                //Llamo a la vista
                require("./View/crearMedida.php");

                //Limpio variables de sesión
                unset($_SESSION['codigomedida']);
                unset($_SESSION['nombre']);
                unset($_SESSION['descripcion']);
                unset($_SESSION['unidad']);
                unset($_SESSION['enviarnotificacion']);
            }
        }

        /**
         * Funcion para creación de medidas por persona
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
                $sCodigoPersona = isset($_POST['personasselect']) ? $_POST['personasselect'] : null;
                $dFechaMedida = isset($_POST['fechamedida']) ? $_POST['fechamedida'] : null;
                $dHoraMedida = isset($_POST['horamedida']) ? $_POST['horamedida'] : null;
                $dValorMedida = isset($_POST['valormedida']) ? $_POST['valormedida'] : null;
                $iIdMedidaxPersonal = isset($_POST['idmedidaxpersona']) ? $_POST['idmedidaxpersona'] : null;
                $sUsuario = $_SESSION['usera'];
                $sSeguridad = "C-".$sUsuario;

                $sBusquedaPersona = isset($_POST['codigopersona']) ? $_POST['codigopersona'] : null;
                $sTipoBusquedaPersona = isset($_POST['tipobusqueda']) ? $_POST['tipobusqueda'] : null;

                //Data validations
                $oMedida->setId($iIdMedida);
                $oMedida->setSeguridad($sSeguridad);

                //Guardo y manejo los mensajes
                if(!$oMedida->saveMedidaxPersona($sCodigoPersona, $dFechaMedida, $dHoraMedida, $dValorMedida, $iIdMedidaxPersonal))
                {
                    $_SESSION["error"]=$oMedida->getMensaje();
                    $_SESSION["idmedida"] = $iIdMedida;
                    $_SESSION["personasselect"] = $sCodigoPersona;
                    $_SESSION["fechamedida"] = $dFechaMedida;
                    $_SESSION["horamedida"] = $dHoraMedida;
                    $_SESSION["valormedida"] = $dValorMedida;
                    $_SESSION["idmedidaxpersona"] = $iIdMedidaxPersonal;
                    $_SESSION["codigopersona"] = $sBusquedaPersona;
                    $_SESSION["tipobusqueda"] = $sTipoBusquedaPersona;
                    header("Location: medidas.php?wemp_pmla=".$wemp_pmla."&action=createMedidaxPersona");
                    return;
                }

                //Seteo la variable de respuesta
                $_SESSION["success"] = $oMedida->getMensaje();

                //Limpio variables de sesión
                unset($_SESSION['idmedida']);
                unset($_SESSION['personasselect']);
                unset($_SESSION['fechamedida']);
                unset($_SESSION['horamedida']);
                unset($_SESSION['valormedida']);
                unset($_SESSION['idmedidaxpersona']);

                unset($_SESSION['codigopersona']);
                unset($_SESSION['tipobusqueda']);

                header("Location: medidas.php?wemp_pmla=".$wemp_pmla."&action=createMedidaxPersona");
                
                return;
            }
            else
            {
                $aMedidas = $oMedida->getAll();
                $aPersonas = $oMedida->getUsuariosMedidas();
                $aCentrosCosto = $oMedida->getCentrosCosto();
                
                //Llamo a la vista
                require("./View/crearMedidaPersona.php");

                //Limpio variables de sesión
                unset($_SESSION['idmedida']);
                unset($_SESSION['personasselect']);
                unset($_SESSION['fechamedida']);
                unset($_SESSION['horamedida']);
                unset($_SESSION['valormedida']);
                unset($_SESSION['idmedidaxpersona']);

                unset($_SESSION['codigopersona']);
                unset($_SESSION['tipobusqueda']);
            }
        }

        /**
         * Funcion para búsqueda de personas
         * @by: sebastian.nevado
         * @date: 2021/04/26
         */
        public function buscarPersona()
        {
            //Obtengo el parámetro
            $wemp_pmla = isset($_POST["wemp_pmla"]) ? $_POST["wemp_pmla"] : null;
            $sValorBusqueda = isset($_POST["busquedapersona"]) ? $_POST["busquedapersona"] : null;
            $sTipoBusqueda = isset($_POST["tipoBusqueda"]) ? $_POST["tipoBusqueda"] : null;
            $sCentroCosto = isset($_POST["codigoCentroCosto"]) ? $_POST["codigoCentroCosto"] : null;
            $bLimpiar = isset($_POST["limpiar"]) ? ($_POST["limpiar"]=="true") : false;
            
            //Creo la variable medida
            $oMedida = new Medida($wemp_pmla);
            $aPersonas = $oMedida->getUsuariosMedidas($sTipoBusqueda, $sValorBusqueda, $sCentroCosto);
            
            $response = array();
            if(count($aPersonas) == 0)
            {
                $response[] = array("value"=>"","label"=>"No se encontró ninguna persona");
            }
            foreach ($aPersonas as $oPersona)
            {
                $response[] = array("value"=>$oPersona['codigo'],"label"=>$oPersona['codigo']." - ".$oPersona['nombre']." (".$oPersona['documento'].")");
            }


            /** Limpiamos el buffer de salida de php para no retornar los datos de los "echos" que se hacen en los include **/
            ob_end_clean();

            echo json_encode($response);
            return ;
        }

        /**
         * Funcion para listado de medidas por persona
         * @by: sebastian.nevado
         * @date: 2021/04/29
         */
        public function listMedidaxPersona()
        {
            //Obtengo el parámetro
            $wemp_pmla = isset($_GET["wemp_pmla"]) ? $_GET["wemp_pmla"] : $_POST["wemp_pmla"]  ;
            
            //Creo la variable medida
            $oMedida = new Medida($wemp_pmla);

            $aMedidasPersonal = $oMedida->getAllMedidasxPersona();
            
            //Llamo a la vista
            require("./View/listarMedidaPersona.php");

        }

        /**
         * Funcion para informe de medidas por persona
         * @by: sebastian.nevado
         * @date: 2021/04/30
         */
        public function informeMedidaxPersona()
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
                
                $bGuardarBusqueda=true;

                //Guardo y manejo los mensajes
                $aMedidasPersonal = $oMedida->getAllMedidasxPersona($sTipoBusqueda, $sValorBusqueda, $iIdMedida, $bGuardarBusqueda);

                //Seteo la variable de respuesta
                $_SESSION["success"] = $oMedida->getMensaje();
                $_SESSION["success"] .= (count($aMedidasPersonal) == 0) ? ". No se encontraron registros con ".$sTipoBusqueda." ".$sValorBusqueda."." : ".";

                $aMedidas = $oMedida->getAll();

                //Llamo a la vista
                require("./View/informeMedidaPersona.php");
                
                return;
            }
            else
            {
                $aMedidas = $oMedida->getAll();
                
                //Llamo a la vista
                require("./View/informeMedidaPersona.php");
            }
        }

        /**
         * Funcion para edición de medidas por persona
         * @by: sebastian.nevado
         * @date: 2021/05/06
         */
        public function editMedidaxPersona()
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
                $sCodigoPersona = isset($_POST['personasselect']) ? $_POST['personasselect'] : null;
                $dFechaMedida = isset($_POST['fechamedida']) ? $_POST['fechamedida'] : null;
                $dHoraMedida = isset($_POST['horamedida']) ? $_POST['horamedida'] : null;
                $dValorMedida = isset($_POST['valormedida']) ? $_POST['valormedida'] : null;
                $iIdMedidaxPersonal = isset($_POST['idmedidaxpersona']) ? $_POST['idmedidaxpersona'] : null;
                $sCodigoCentroCosto = isset($_POST['codigocentrocosto']) ? $_POST['codigocentrocosto'] : null;
                $sUsuario = $_SESSION['usera'];
                $sSeguridad = "C-".$sUsuario;

                $sBusquedaPersona = isset($_POST['codigopersona']) ? $_POST['codigopersona'] : null;
                $sTipoBusquedaPersona = isset($_POST['tipobusqueda']) ? $_POST['tipobusqueda'] : null;

                //Data validations
                $oMedida->setId($iIdMedida);
                $oMedida->setSeguridad($sSeguridad);

                //Guardo y manejo los mensajes
                if(!$oMedida->saveMedidaxPersona($sCodigoPersona, $dFechaMedida, $dHoraMedida, $dValorMedida, $iIdMedidaxPersonal))
                {
                    $_SESSION["error"] = $oMedida->getMensaje();
                    $_SESSION["idmedida"] = $iIdMedida;
                    $_SESSION["personasselect"] = $sCodigoPersona;
                    $_SESSION["fechamedida"] = $dFechaMedida;
                    $_SESSION["horamedida"] = $dHoraMedida;
                    $_SESSION["valormedida"] = $dValorMedida;
                    $_SESSION["idmedidaxpersona"] = $iIdMedidaxPersonal;
                    $_SESSION["codigopersona"] = $sBusquedaPersona;
                    $_SESSION["tipobusqueda"] = $sTipoBusquedaPersona;
                    $_SESSION["codigocentrocosto"] = $sCodigoCentroCosto;
                    header("Location: medidas.php?wemp_pmla=".$wemp_pmla."&action=editMedidaxPersona&idmedidaxpersona=".$iIdMedidaxPersonal);
                    return;
                }

                //Limpio variables de sesión
                unset($_SESSION['idmedida']);
                unset($_SESSION['personasselect']);
                unset($_SESSION['fechamedida']);
                unset($_SESSION['horamedida']);
                unset($_SESSION['valormedida']);
                unset($_SESSION['idmedidaxpersona']);

                unset($_SESSION['codigopersona']);
                unset($_SESSION['tipobusqueda']);

                //Seteo la variable de respuesta
                $_SESSION["success"] = $oMedida->getMensaje();
                
                header("Location: medidas.php?wemp_pmla=".$wemp_pmla."&action=listMedidaxPersona");

                return;
            }
            else
            {
                $iIdMedidaxPersonal = isset($_GET["idmedidaxpersona"]) ? $_GET["idmedidaxpersona"] : $_POST["idmedidaxpersona"];
                $oDatosMedidaPersona = $oMedida->loadMedidaPersona($iIdMedidaxPersonal);

                $iIdMedida = isset($_SESSION['error']) ? $_SESSION['idmedida'] : $oDatosMedidaPersona['idmedida'];
                $sCodigoPersona = isset($_SESSION['error']) ? $_SESSION['personasselect'] : $oDatosMedidaPersona['codigopersonamedida'];
                $dFechaMedida = isset($_SESSION['error']) ? $_SESSION['fechamedida'] : $oDatosMedidaPersona['fechamedida'];
                $dHoraMedida = isset($_SESSION['error']) ? $_SESSION['horamedida'] : $oDatosMedidaPersona['horamedida'];
                $dValorMedida = isset($_SESSION['error']) ? $_SESSION['valormedida'] : $oDatosMedidaPersona['valormedida'];
                $sCodigoCentroCosto = isset($_SESSION['error']) ? $_SESSION['codigocentrocosto'] : $oDatosMedidaPersona['codigocentrocosto'];
                $sBusquedaPersona = isset($_SESSION['error']) ? $_SESSION['codigopersona'] : null;
                $sTipoBusquedaPersona = isset($_SESSION['error']) ? $_SESSION['tipobusqueda'] : null;

                $aMedidas = $oMedida->getAll();
                $aPersonas = $oMedida->getUsuariosMedidas('codigo', $sCodigoPersona, $sCodigoCentroCosto);
                $aCentrosCosto = $oMedida->getCentrosCosto();
                
                //Llamo a la vista
                require("./View/editarMedidaPersona.php");

                //Limpio variables de sesión
                unset($_SESSION['idmedida']);
                unset($_SESSION['personasselect']);
                unset($_SESSION['fechamedida']);
                unset($_SESSION['horamedida']);
                unset($_SESSION['valormedida']);
                unset($_SESSION['idmedidaxpersona']);

                unset($_SESSION['codigopersona']);
                unset($_SESSION['tipobusqueda']);
            }
        }


        /**
         * Funcion para eliminación de medidas por persona
         * @by: sebastian.nevado
         * @date: 2021/05/07
         */
        public function deleteMedidaxPersona()
        {
            //Obtengo el parámetro
            $wemp_pmla = isset($_GET["wemp_pmla"]) ? $_GET["wemp_pmla"] : $_POST["wemp_pmla"];
            $iIdMedidaxPersonal = isset($_POST['idmedidaxpersona']) ? $_POST['idmedidaxpersona'] : null;
            
            //Creo la variable medida
            $oMedida = new Medida($wemp_pmla);

            //Variable de respuesta
            $aDatosRespuesta = array('error'=>0,'mensaje'=>'');

            //Guardo y manejo los mensajes
            if(!$oMedida->deleteMedidaxPersona($iIdMedidaxPersonal))
            {
                $_SESSION["idmedidaxpersona"] = $iIdMedidaxPersonal;
                $_SESSION["error"] = $oMedida->getMensaje();
                $aDatosRespuesta['mensaje'] = $oMedida->getMensaje();
                $aDatosRespuesta['error'] = 1;
            }
            else
            {
                //Limpio variables de sesión
                unset($_SESSION['idmedidaxpersona']);

                //Seteo la variable de respuesta
                $_SESSION["success"] = $oMedida->getMensaje();
                $aDatosRespuesta['mensaje'] = $oMedida->getMensaje();
            }

            /** Limpiamos el buffer de salida de php para no retornar los datos de los "echos" que se hacen en los include **/
            ob_end_clean();

            echo json_encode($aDatosRespuesta);
            return ;
        }

        /**
         * Funcion buscar centro de costo
         * @by: sebastian.nevado
         * @date: 2021/05/11
         */
        public function buscarCentrodeCosto()
        {
            //Obtengo el parámetro
            $wemp_pmla = isset($_REQUEST["wemp_pmla"]) ? $_REQUEST["wemp_pmla"] : null;
            $sValorBusqueda = isset($_REQUEST["busqueda"]) ? $_REQUEST["busqueda"] : null;
            
            //Creo la variable medida
            $oMedida = new Medida($wemp_pmla);
            $aCentroCostos = $oMedida->getCentrosCosto($sValorBusqueda);
            
            $response = array();
            if(count($aCentroCostos) == 0)
            {
                $response[] = array("value"=>"","label"=>"No se encontró ningún centro de costo");
            }
            foreach ($aCentroCostos as $oCentroCosto)
            {
                $response[] = array("value"=>$oCentroCosto['codigo'],"label"=>$oCentroCosto['codigo']." - ".$oCentroCosto['nombre']);
            }

            /** Limpiamos el buffer de salida de php para no retornar los datos de los "echos" que se hacen en los include **/
            ob_end_clean();

            echo json_encode($response);
            return ;
        }
    }
?>