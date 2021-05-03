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
                require("./View/crearMedida.php");

                //Limpio variables de sesión
                unset($_SESSION['codigo']);
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
                $bContinuarIngresando = !isset($_POST['seguiringresando']) ? false : true;
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
                    $_SESSION['seguiringresando'] = ($bContinuarIngresando) ? "checked" : "";
                    $_SESSION["codigopersona"] = $sBusquedaPersona;
                    $_SESSION["tipobusqueda"] = $sTipoBusquedaPersona;
                    header("Location: medidas.php?wemp_pmla=".$wemp_pmla."&action=createMedidaxPersona");
                    return;
                }

                //Limpio variables de sesión
                unset($_SESSION['idmedida']);
                unset($_SESSION['personasselect']);
                unset($_SESSION['fechamedida']);
                unset($_SESSION['horamedida']);
                unset($_SESSION['valormedida']);
                unset($_SESSION['idmedidaxpersona']);
                //unset($_SESSION['seguiringresando']);

                unset($_SESSION['codigopersona']);
                unset($_SESSION['tipobusqueda']);

                //Seteo la variable de respuesta
                $_SESSION["success"]="Medida por personal guardada";
                if($bContinuarIngresando)
                {
                    header("Location: medidas.php?wemp_pmla=".$wemp_pmla."&action=createMedidaxPersona");
                }
                else
                {
                    header("Location: medidas.php?wemp_pmla=".$wemp_pmla."&action=listMedidaxPersona");
                }
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
                //unset($_SESSION['seguiringresando']);

                unset($_SESSION['codigopersona']);
                unset($_SESSION['tipobusqueda']);
            }
        }

        /**
         * Funcion para creación de medidas
         * @by: sebastian.nevado
         * @date: 2021/04/26
         */
        public function buscarPersona()
        {
            //Obtengo el parámetro
            $wemp_pmla = isset($_POST["wemp_pmla"]) ? $_POST["wemp_pmla"] : null;
            $sValorBusqueda = isset($_POST["codigoPersona"]) ? $_POST["codigoPersona"] : null;
            $sTipoBusqueda = isset($_POST["tipoBusqueda"]) ? $_POST["tipoBusqueda"] : null;
            $sCentroCosto = isset($_POST["codigoCentroCosto"]) ? $_POST["codigoCentroCosto"] : null;
            $bLimpiar = isset($_POST["limpiar"]) ? ($_POST["limpiar"]=="true") : false;
            
            //Creo la variable medida
            $oMedida = new Medida($wemp_pmla);
            $aPersonas = $oMedida->getUsuariosMedidas($sTipoBusqueda, $sValorBusqueda, $sCentroCosto);
            
            $aDatos = array('error'=>0,'mensaje'=>'','html'=>'','personas'=>'');
            $sHtml = '<select style="max-width:60%; width:60%" id="personasselect" name="personasselect">
                        <option value="" selected>--Seleccione una persona--</option>';
            
            $sSelected = ($bLimpiar) ? "" : "selected";
            foreach ($aPersonas as $oPersona)
            {
                $sHtml .= "<option value='".$oPersona['codigo']."' ".$sSelected.">".$oPersona['codigo']." - ".utf8_encode($oPersona['nombre'])." (".$oPersona['documento'].")"."</option>";
            }
            $sHtml .= "</select>";

            //$aDatos['personas'] = $aPersonas;
		    $aDatos['mensaje'] = 'Se ha filtrado la información de personas.';
            $aDatos['html'] = $sHtml;

            /** Limpiamos el buffer de salida de php para no retornar los datos de los "echos" que se hacen en los include **/
            ob_end_clean();

            echo json_encode($aDatos);
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

                //Limpio variables de sesión
                unset($_SESSION['idmedida']);
                unset($_SESSION['personasselect']);
                unset($_SESSION['fechamedida']);
                unset($_SESSION['horamedida']);
                unset($_SESSION['valormedida']);
                unset($_SESSION['idmedidaxpersona']);
                //unset($_SESSION['seguiringresando']);

                unset($_SESSION['codigopersona']);
                unset($_SESSION['tipobusqueda']);

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

                //Limpio variables de sesión
                unset($_SESSION['idmedida']);
                unset($_SESSION['personasselect']);
                unset($_SESSION['fechamedida']);
                unset($_SESSION['horamedida']);
                unset($_SESSION['valormedida']);
                unset($_SESSION['idmedidaxpersona']);
                //unset($_SESSION['seguiringresando']);

                unset($_SESSION['codigopersona']);
                unset($_SESSION['tipobusqueda']);
            }
        }
    }
?>