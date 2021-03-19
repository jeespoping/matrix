<?php

namespace matrix\admisiones\presap\models;

//include_once("root/comun.php");
/*
 * Obtener los datos socioeconomicos de la admision
 */

class DatosSocioeconomicos {

    private $conex;
    private $edad;
    private $numeroHistoria;
    private $numeroIngreso;
    private $apellidosCompletos;
    private $aseguradora;
    private $diagnostico;
    private $genero;
    private $nombresCompletos;
    private $numeroDocumento;
    private $tipoDocumento;
    private $fechaNacimiento;
    private $nombre1;
    private $nombre2;
    private $apellido1;
    private $apellido2;

    /*
     * 
     */

    public function __construct($numeroHistoria, $numeroIngreso, $conex) {
        $this->numeroHistoria = $numeroHistoria;
        $this->numeroIngreso = $numeroIngreso;
        $this->conex = $conex;
        $this->getDatos();
        $this->calcularEdad($this->fechaNacimiento);
    }

    function setConex($conex) {
        $this->conex = $conex;
        return $this;
    }

    private function getDatos() {
        $query = "SELECT pac.Pactdo, pac.Pacdoc, pac.Pacno1, pac.Pacno2, pac.Pacap1, pac.Pacap2, ing.Ingdig, '', pac.Pacfna, pac.Pacsex "
                . " FROM matrix.cliame_000101 ing "
                . " JOIN matrix.cliame_000100 pac ON pac.Pachis = ing.inghis "
                . "WHERE ing.Inghis = ? AND ing.Ingnin = ?";

        if ($sql = mysqli_prepare($this->conex, $query)) {

            $numeroHistoria = $this->numeroHistoria;
            $numeroIngreso = $this->numeroIngreso;

            $sql->bind_param("ii", $numeroHistoria, $numeroIngreso);
            $sql->execute();

            $sql->bind_result(
                    $this->tipoDocumento, $this->numeroDocumento,
                    $this->nombre1, $this->nombre2,
                    $this->apellido1, $this->apellido2,
                    $this->diagnostico, $this->aseguradora,
                    $this->fechaNacimiento, $this->genero
            );

            $sql->fetch();
        }
    }

    /*
     * Retorna la edad, partiendo de la fecha de nacimiento.
     * 
     */

    function calcularEdad() {
        $fecha = new \DateTime($this->fechaNacimiento);
        $ahora = new \DateTime();
        $this->edad = $ahora->diff($fecha)->y;
    }

    function getEdad() {
        return $this->edad;
    }

    function getNumeroHistoria() {
        return $this->numeroHistoria;
    }

    function getNumeroIngreso() {
        return $this->numeroIngreso;
    }

    function getAseguradora() {
        return $this->aseguradora;
    }

    function getDiagnostico() {
        return $this->diagnostico;
    }

    function getGenero() {
        return $this->genero;
    }

    function getNumeroDocumento() {
        return $this->numeroDocumento;
    }

    function getTipoDocumento() {
        return $this->tipoDocumento;
    }

    function getNombre1() {
        return ucfirst(strtolower($this->nombre1));
    }

    function getNombre2() {
        return ucfirst(strtolower($this->nombre2));
    }

    function getApellido1() {
        return ucfirst(strtolower($this->apellido1));
    }

    function getApellido2() {
        return ucfirst(strtolower($this->apellido2));
    }

    function getNombres() {
        $this->nombresCompletos = $this->getNombre1();
        if ($this->nombre1) {
            $this->nombresCompletos .= " " . $this->getNombre2();
        }
        return $this->nombresCompletos;
    }

    function getApellidos() {
        $this->apellidosCompletos = $this->getApellido1();
        if ($this->apellido2) {
            $this->apellidosCompletos .= " " . $this->getApellido2();
        }
        return $this->apellidosCompletos;
    }

}
