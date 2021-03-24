<?php

namespace matrix\admisiones\presap\controllers;

include_once("conex.php");
include '../service/GeneradorCSV.php';
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