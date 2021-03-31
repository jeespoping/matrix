<?php

namespace matrix\admisiones\presap\service;
/**
 * Se encarga de generar archivos CSV unidimensionales
 */
class GeneradorCSV
{

    private $archivo, $datos, $delimitador, $cabecera = array();

    public function __construct(string $filename = 'Sin-Nombre.csv', string $delimitador = ',')
    {
        $this->archivo = $filename;
        $this->delimitador = $delimitador;
    }
    /**
     * Cambia el valor de la cabecera que puede llevar el archivo
     * @param array $cabecera datos de cabecera
     */
    public function setCabecera(array $cabecera)
    {
        $this->cabecera = $cabecera;
    }
    /**
     * Obtiene los datos de cabecera
     * @param null 
     * @return array datos de cabecera
     */
    public function getCabecera(): array
    {
        return $this->cabecera;
    }
    /**
     * genera el archivo y permite su descarga en una respuesta blob
     * @param array $datos información a descargar
     */
    public function descargarArchivo(array $datos)
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $this->archivo . '";');
        $f = fopen('php://output', 'w');
        fputcsv($f, $this->cabecera);
        foreach ($datos as $dato) {
            fputcsv($f, $dato);
        }
        fclose($f);
    }
}
