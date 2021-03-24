<?php


namespace Admisiones\Model;
use DateTime;

/*
 * Informacion del paciente
 *
 * @author Edier Andrés Villaneda Navarro
 */
class Pacientes
{

    protected $apellido1;
    protected $apellido2;
    protected $apellidos;
    protected $aseguradora;
    protected $diagnostico;
    protected $edad;
    protected $fechaNacimiento;
    protected $genero;
    protected $nombre1;
    protected $nombre2;
    protected $nombres;
    protected $numeroDocumento;
    protected $numeroHistoria;
    protected $numeroIngreso;
    protected $tipoDocumento;

    /**
     * Primer apellido del paciente
     *
     * @return string Apellido en mayuscula inicial
     */
    function getApellido1(): string
    {
        return ucfirst(strtolower($this->apellido1));
    }

    function getApellido2(): string
    {
        return ucfirst(strtolower($this->apellido2));
    }

    /**
     * Apellidos completos
     * @return string Apellidos ccapitaLizados
     */
    function getApellidos(): string
    {
        $this->apellidos = $this->getApellido1();
        if ($this->apellido2) {
            $this->apellidos .= " " . $this->getApellido2();
        }
        return $this->apellidos;
    }

    function getAseguradora()
    {
        return $this->aseguradora;
    }

    function getDiagnostico(): string
    {
        return $this->diagnostico;
    }

    function getEdad(): string
    {
        return $this->edad;
    }

    function getFechaNacimiento(): \DateTime
    {
        return new DateTime($this->fechaNacimiento);
    }

    function getGenero()
    {
        return $this->genero;
    }

    function getNombre1(): string
    {
        return ucfirst(strtolower($this->nombre1));
    }

    function getNombre2(): string
    {
        return ucfirst(strtolower($this->nombre2));
    }


    /**
     * Nombres del paciente
     * @return string Nombre capitalizados
     */
    function getNombres(): string
    {
        $this->nombres = $this->getNombre1();
        if ($this->nombre1) {
            $this->nombres .= " " . $this->getNombre2();
        }
        return $this->nombres;
    }

    function getNumeroDocumento()
    {
        return $this->numeroDocumento;
    }

    function getNumeroHistoria()
    {
        return $this->numeroHistoria;
    }

    function getNumeroIngreso()
    {
        return $this->numeroIngreso;
    }

    function getTipoDocumento()
    {
        return $this->tipoDocumento;
    }

    function setApellido1($apellido1)
    {
        $this->apellido1 = $apellido1;
        return $this;
    }

    function setApellido2($apellido2)
    {
        $this->apellido2 = $apellido2;
        return $this;
    }

    function setApellidos($apellidos)
    {
        $this->apellidos = $apellidos;
        return $this;
    }

    function setAseguradora($aseguradora)
    {
        $this->aseguradora = $aseguradora;
        return $this;
    }

    function setDiagnostico($diagnostico)
    {
        $this->diagnostico = $diagnostico;
        return $this;
    }

    /**
     * Establecer edad del paciente
     * @param $edad
     * @return $this
     */
    function setEdad($edad)
    {
        $this->edad = $edad;
        return $this;
    }

    function setFechaNacimiento($fechaNacimiento)
    {
        $this->fechaNacimiento = $fechaNacimiento;
        return $this;
    }

    function setGenero($genero)
    {
        $this->genero = $genero;
        return $this;
    }

    function setNombre1($nombre1)
    {
        $this->nombre1 = $nombre1;
        return $this;
    }

    function setNombre2($nombre2)
    {
        $this->nombre2 = $nombre2;
        return $this;
    }

    function setNombres($nombres)
    {
        $this->nombres = $nombres;
        return $this;
    }

    function setNumeroDocumento($numeroDocumento)
    {
        $this->numeroDocumento = $numeroDocumento;
        return $this;
    }

    function setNumeroHistoria($numeroHistoria)
    {
        $this->numeroHistoria = $numeroHistoria;
        return $this;
    }

    function setNumeroIngreso($numeroIngreso)
    {
        $this->numeroIngreso = $numeroIngreso;
        return $this;
    }

    function setTipoDocumento($tipoDocumento)
    {
        $this->tipoDocumento = $tipoDocumento;
        return $this;
    }

}