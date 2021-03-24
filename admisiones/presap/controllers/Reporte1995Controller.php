<?php

namespace matrix\admisiones\presap\controllers;

include_once("conex.php");
//include_once("root/comun.php");

class Reporte1995Controller
{
    public $request;
    private $response;
    private $servicioGeneradorCSV;

    public function __construct($servicioGeneradorCSV){
        $this->servicioGeneradorCSV = $servicioGeneradorCSV;
    }

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
    //$controlador = new Reporte1995Controller('test');
    echo json_encode($request);
    exit;
}
