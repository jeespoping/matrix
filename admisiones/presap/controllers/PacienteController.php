<?php

namespace Admisiones\controllers;

require dirname(__FILE__) . '/../models/Paciente.php';

use Admisiones\Models\Paciente;
use Exception;

/*
 * Datos socioeconomicos de un paciente.
 *
 * @author Edier Andres Villaneda Navarro
 * @version 1.0
 */

class PacienteController extends Paciente
{

    private $errorCode;
    public $request;
    private $response;

    /**
     * Consultar los datos socioeconomicos de un paciente.
     * @param object $conex          Datos de la conexión a la base de datos. (Obligatorio)
     * @param string $empresa        Alias de la empresa
     * @param        $numeroHistoria Número de Historia Clinica del paciente.
     * @param        $numeroIngreso  Numero de ingreso del paciente.
     * @throws Exception
     */
    public function __construct($conex, string $empresa, $numeroHistoria, $numeroIngreso)
    {
        $this->setNumeroHistoria($numeroHistoria);
        $this->setNumeroIngreso($numeroIngreso);
        if ($this->validarDatos()) {
            $this->getDatosPaciente($conex, $empresa);
        }
    }

    /**
     * Validar si los numeros de Historia e Ingreso son validos.
     * @return boolean
     * @throws Exception
     */
    public function validarDatos(): bool
    {
        $this->errorCode = false;
        switch (true) {
            case $this->getNumeroHistoria() == 0:
                $this->errorCode = true;
                throw new \Exception("Número de Historia invalido");
                break;
            case $this->getNumeroIngreso() == 0:
                $this->errorCode = true;
                throw new \Exception("Número de Ingreso invalido");
                break;
        }
        if ($this->errorCode) {
            return false;
        }
        return true;
    }

    /**
     * Serializacion de datos de la consulta
     * @return array array de datos del paciente
     */
    public function jsonSerialize(): array
    {
        return [
            'numeroHistoria' => $this->getNumeroHistoria(),
            'numeroIngreso' => $this->getNumeroIngreso(),
            'documento' => $this->getDocumento(),
            'tipoDocumento' => $this->getTipoDocumento(),
            'numeroDocumento' => $this->getNumeroDocumento(),
            'nombreCompleto' => $this->getNombreCompleto(),
            'nombres' => $this->getNombres(),
            'apellidos' => $this->getApellidos(),
            'aseguradora' => $this->getAseguradora(),
            'diagnostico' => $this->getDiagnostico(),
            'edad' => $this->getEdad()
        ];

    }
}
