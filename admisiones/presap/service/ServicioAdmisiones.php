<?php

namespace Admisiones\Service;

class ServicioAdmisiones
{

    private $modelo;
    private $datosDemograficos;

    public function __construct($modeloAdmisiones)
    {
        $this->modelo = $modeloAdmisiones;
    }

    public function calcularEdad()
    {
        return $this->modelo->todas();
    }
}