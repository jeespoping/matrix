<?php

namespace matrix\admisiones\presap\service;

use matrix\admisiones\presap\service\GeneradorCSV;
use matrix\admisiones\presap\models\Admisiones;

class ServicioAdmisiones
{
    /**
     * 
     */
    private $admisiones;
    /**
     * cabecera del archivo unidimensional a generar
     */
    private $cabecera = array(
        'fecha_ingreso',
        'primer nombre', 'segundo nombre',
        'primer apellido', 'segundo apellido', 'historia', 'estado civil',
        'documento', 'fecha de nacimiento',
        'edad', 'sexo', 'direccion', 'Telefono del domicilio',
        'Lugar de residencia', 'Nombre del acompanante',
        'Telefono del acompanante', 'Nombre del responsable',
        'Telefono del responsable', 'parentesco del responsable',
        'Aseguradora', 'Tipo de vinculacion'
    );
    /**
     * 
     */
    public function __construct(Admisiones $admisiones)
    {
        $this->admisiones = $admisiones;
    }
    /**
     * 
     */
    public function generarReporte(string $fechaInicio)
    {
        $reporteCSV = new GeneradorCSV();
        $reporte = $this->admisiones->todasPorFechaIngreso('"'.$fechaInicio.'"');
        $reporteCSV->setCabecera($this->cabecera);
        $reporteCSV->descargarArchivo($reporte);
    }
}
