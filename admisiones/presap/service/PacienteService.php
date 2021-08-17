<?php


namespace Admisiones\service;


use Admisiones\controllers\StickerController;
use Admisiones\models\Paciente;

class PacienteService
{
    private $paciente;

    public function __construct(Paciente $paciente)
    {
        $this->paciente = $paciente;
    }

}