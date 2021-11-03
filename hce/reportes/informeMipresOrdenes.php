<?php
    //Agrego el controlador de medida
    include_once("../procesos/ordenes.inc.php");

    //Si el wemm_pmla llega vacío, uso el global
    $sWemp_pmla = is_null($sWemp_pmla) ? $wemp_pmla : $sWemp_pmla;

    //Si la fecha llega nula, pongo la fecha actual
    $sFechaBusqueda = (isset($_POST["fechabusqueda"])  && ($_POST["fechabusqueda"] != '')) ? $_POST["fechabusqueda"] : null;
    $sFechaBusqueda = is_null($sFechaBusqueda) ? date("Y-m-d") : $sFechaBusqueda;

    $_SESSION["fechabusqueda"] = $sFechaBusqueda;

    // Proceso si doy aceptar
    if ( isset($_POST['buscar']) )
    {
        //Guardo y manejo los mensajes
        $aMedicamentos = obtenerDatosInformeMipresOrdenes($wemp_pmla, $sFechaBusqueda);

        //Seteo la variable de respuesta
        $_SESSION["success"] = "Se gener&oacute; el informe de MIPRES ingresados desde &Oacute;rdenes";
        $_SESSION["success"] .= (count($aMedicamentos) == 0) ? ". No se encontraron registros para ".$sFechaBusqueda."." : ".";
    }
    else
    {
        //Variable de respuesta
        $aMedicamentos = obtenerDatosInformeMipresOrdenes($sWemp_pmla, $sFechaBusqueda);
    }

    //Llamo a la vista
    require("../reportes/viewInformeMipresOrdenes.php");
        
    return;


?>