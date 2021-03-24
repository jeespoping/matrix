<?php

namespace matrix\admisiones\presap\controllers;

include_once("conex.php");
include '../service/GeneradorCSV.php';
use matrix\admisiones\presap\service\GeneradorCSV;
include_once("root/comun.php");

class Reporte1995Controller
{
    public $request;
    private $response;

    public function index()
    {
        $responseObject = (object)[];
        $this->response = $responseObject;
    }

    public function createJsonResponse()
    {
        return json_encode($this->response);
    }

    public function decodePostBody($request)
    {
        return [
            'fechaInicio' => $request['fechaInicio'],
            'fechaFin' => $request['fechaFin'],
            'wemp_pmla' => $request['wemp_pmla'],
        ];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $JSONEntrada = file_get_contents('php://input');
    $request = json_decode($JSONEntrada, TRUE);
    $prueba = new GeneradorCSV('prueba.csv', ',');
    $prueba->crearArchivo();
    //echo json_encode($request);
}