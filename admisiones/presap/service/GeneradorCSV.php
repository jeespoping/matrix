<?php
namespace matrix\admisiones\presap\service;

class GeneradorCSV{

    private $archivo, $datos, $delimitador;

    public function __construct($filename='Sin-Nombre.csv',$delimitador=','){
        $this->archivo =$filename;
        $this->delimitador = $delimitador;
    }
    public function crearArchivo($datos){
        $filename = 'holaMundo.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . "holamundo.csv" . '";');
        $cabecera = array(
            'fecha_ingreso',
            'primer nombre','segundo nombre',
            'primer apellido','segundo apellido'
            ,'historia','estado civil',
            'documento', 'fecha de nacimiento', 
            'edad', 'sexo', 'direccion','Telefono del domicilio',
            'Lugar de residencia', 'Nombre del acompanante',
            'Telefono del acompanante', 'Nombre del responsable',
            'Telefono del responsable', 'parentesco del responsable',
            'Aseguradora', 'Tipo de vinculacion');
        
        $f = fopen('php://output', 'w');
        fputcsv($f,$cabecera);
        foreach($datos as $dato){
            fputcsv($f,$dato);
        }
        fclose($f);
    }
}