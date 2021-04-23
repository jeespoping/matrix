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

        // Si pulso el botón cancelar, vuelvo al inicio
        if ( isset($_POST['cancel']) )
        {
            $wemp_pmla = isset($_POST['wemp_pmla']) ? $_POST['wemp_pmla'] : null;
            header('Location: medidas.php?wemp_pmla='.$wemp_pmla);
            return;
        }
        elseif ( isset($_POST['add']) )
        {

            //Valido existencia de datos
            $sCodigo = isset($_POST['codigo']) ? $_POST['codigo'] : null;
            $sNombre = isset($_POST['nombre']) ? $_POST['nombre'] : null;
            $sDescripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : null;
            $sIdUnidad = isset($_POST['unidad']) ? $_POST['unidad'] : null;
            $bEnviarNotificacion = !isset($_POST['enviarnotificacion']) ? false : true;
            $sSeguridad = "C-".$_SESSION['codigo'];

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
                $_SESSION["error"]=$oMedida->getMensaje();
                header("Location: medidas.php?wemp_pmla=".$wemp_pmla."&action=createMedida");
                return;
            }

            //Seteo la variable de respuesta
            $_SESSION["success"]="Medida guardada";
            header("Location: medidas.php?wemp_pmla=".$wemp_pmla);
            return;
        }
        else
        {
            //Llamo a la vista
            require(".//crearMedidaView.php");
        }
    }

    /**
     * Funcion para ver una medida
     * @by: sebastian.nevado
     * @date: 2021/04/22
     */
    public function view()
    {
        
        //Creo la variable medida
        $oMedida = new Medida();

        // Si pulso el botón cancelar, vuelvo al inicio
        if ( isset($_POST['cancel']) )
        {
            header('Location: ./');
            return;
        }
        else
        {
            if(isset($_REQUEST['id']))
            {
                //Seteo el id y cargo la medida
                $oMedida->setId($_REQUEST['id']);
                $oMedida->load();

                //Le paso los datos a la vista
                $iIdentificacion = $oMedida->getIdentificacion();
                $sNombre = $oMedida->getNombre();
                $sApellidos = $oMedida->getApellidos();
                $dFechaNacimiento = $oMedida->getFechaNacimiento();
                $iId = $oMedida->getId();
                
                //Llamo a la vista
                require("view/medida/view.php");
            }
            else
            {
                $_SESSION["error"]= "No se envió el id de la medida";
                header("Location: index.php?action=createMedida");
                return;
            }
        }
    }

    /**
     * Funcion para editar una medida
     * @by: sebastian.nevado
     * @date: 2021/04/22
     */
    public function edit()
    {
        
        //Creo la variable medida
        $oMedida = new Medida();

        // Si pulso el botón cancelar, vuelvo al inicio
        if ( isset($_POST['cancel']) )
        {
            header('Location: ./');
            return;
        }
        elseif ( isset($_POST['add']) )
        {

            //Valido existencia de datos
            $iId = isset($_POST['id']) ? $_POST['id'] : null;
            $sNombre = isset($_POST['nombre']) ? $_POST['nombre'] : null;
            $sApellidos = isset($_POST['apellidos']) ? $_POST['apellidos'] : null;
            $iIdentificacion = isset($_POST['identificacion']) ? $_POST['identificacion'] : null;
            $dFechaNacimiento = isset($_POST['fechanacimiento']) ? $_POST['fechanacimiento'] : null;

            //Seteo los datos para guardar
            $oMedida->setId($iId);
            $oMedida->setIdentificacion($iIdentificacion);
            $oMedida->setNombre($sNombre);
            $oMedida->setApellidos($sApellidos);
            $oMedida->setFechaNacimiento($dFechaNacimiento);

            //Guardo y manejo los mensajes
            if(!$oMedida->save())
            {
                $_SESSION["error"]=$oMedida->getMensaje();
                header("Location: index.php?action=editMedida&id=".$_REQUEST['id']);
                return;
            }

            //Seteo la variable de respuesta
            $_SESSION["success"]="Medida guardada";
            header("Location: ./");
            return;
        }
        else
        {
            if(isset($_REQUEST['id']))
            {
                $oMedida->setId($_REQUEST['id']);
                $oMedida->load();

                $iIdentificacion = $oMedida->getIdentificacion();
                $sNombre = $oMedida->getNombre();
                $sApellidos = $oMedida->getApellidos();
                
                $date = new DateTime($oMedida->getFechaNacimiento());
                $dFechaNacimiento = $date->format('Y-m-d');

                $iId = $oMedida->getId();
                
                //Llamo a la vista
                require("view/medida/edit.php");
            }
            else
            {
                $_SESSION["error"]= "No se envió el id de la medida";
                header("Location: ./");
                return;
            }
        }
    }

    /**
     * Funcion para eliminar una medida
     * @by: sebastian.nevado
     * @date: 2021/04/22
     */
    public function delete()
    {
        
        //Creo la variable medida
        $oMedida = new Medida();

        // Si pulso el botón cancelar, vuelvo al inicio
        if ( isset($_POST['cancel']) )
        {
            header('Location: ./');
            return;
        }
        elseif ( isset($_POST['delete']) )
        {

            //Valido existencia de datos
            $iId = isset($_POST['id']) ? $_POST['id'] : null;

            //Seteo los datos para eliminar
            $oMedida->setId($iId);

            //Guardo y manejo los mensajes
            if(!$oMedida->delete())
            {
                $_SESSION["error"]=$oMedida->getMensaje();
                header("Location: index.php?action=deleteMedida&id=".$_REQUEST['id']);
                return;
            }

            //Seteo la variable de respuesta
            $_SESSION["success"]="Medida eliminada";
            header("Location: ./");
            return;
        }
        else
        {
            if(isset($_REQUEST['id']))
            {
                $oMedida->setId($_REQUEST['id']);
                $oMedida->load();
                
                $iIdentificacion = $oMedida->getIdentificacion();
                $sNombre = $oMedida->getNombre();
                $sApellidos = $oMedida->getApellidos();
                
                $date = new DateTime($oMedida->getFechaNacimiento());
                $dFechaNacimiento = $date->format('Y-m-d');

                $iId = $oMedida->getId();
                
                //Llamo a la vista
                require("view/medida/delete.php");
            }
            else
            {
                $_SESSION["error"]= "No se envió el id de la medida";
                header("Location: ./");
                return;
            }
        }
    }

}