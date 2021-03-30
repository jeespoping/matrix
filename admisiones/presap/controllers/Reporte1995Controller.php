<?php

namespace matrix\admisiones\presap\controllers;
use matrix\admisiones\presap\service\ServicioAdmisiones;
use Exception;
/**
 * Maneja las peticiones y la generación del reporte para el reporte de admisiones
 */
class Reporte1995Controller
{
    /**
     * 
     */
    private $servicio;
    /**
     * 
     */
    private $peticion;

    /**
     * 
     */
    public function __construct(ServicioAdmisiones $servicio, array $peticion)
    {
        $this->servicio = $servicio;
        $this->peticion = $peticion;
    }
    /**
     * 
     */
    public function descargarReporte()
    {
        try{
            $fecha = $this->peticion['fechaInicio'];
            if(!isset($fecha))
                throw new Exception('No se encontró fecha en la petición');
            $this->servicio->generarReporte($fecha);
        }catch (Exception $e){
            throw new Exception("Error en la peticion: '{$e}'");
        }
    }
}
