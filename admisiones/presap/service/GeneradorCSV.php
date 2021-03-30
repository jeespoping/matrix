<?php

namespace Admisiones\Service;

class GeneradorCSV
{

    private $archivo, $datos, $delimitador;

    public function __construct($filename = 'Sin-Nombre.csv', $delimitador = ',')
    {
        $this->archivo = $filename;
        $this->delimitador = $delimitador;
    }

    public function crearArchivo()
    {
        $filename = 'holaMundo.csv';
        $fields = array(
            'historia', 'nombre', 'estado civil',
            'documento', 'fecha de nacimiento',
            'edad', 'sexo', 'direccion', 'Telefono del domicilio',
            'Lugar de residencia', 'Nombre del acompanante',
            'Telefono del acompanante', 'Nombre del responsable',
            'Telefono del responsable', 'parentesco del responsable',
            'Aseguradora', 'Tipo de vinculacion');

        $test = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17'];

        $f = fopen('php://output', 'w');
        fputcsv($f, $fields, ',');
        fputcsv($f, $test, ',');
        fputcsv($f, $test, ',');
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '";');
        fclose($f);
    }
}