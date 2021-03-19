<?php
namespace matrix;

include_once("conex.php");
include_once("root/comun.php");
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

    /*
     * 
     */

    public function __construct($numeroHistoria, $numeroIngreso) {
        $this->numeroHistoria = $numeroHistoria;
        $this->numeroIngreso = $numeroIngreso;
        $this->conex = $conex;
        $this->getDatos();
    }

    function setConex($conex) {
        $this->conex = $conex;
        return $this;
    }

    private function getDatos() {
        $query = "SELECT ing.Inghis, ing.Ingnin, pac.Pactdo, pac.Pacdoc, pac.Pacno1, pac.Pacno2, pac.Pacap1, pac.Pacap2, ing.Ingdig, '', pac.Pacfna, pac.Pacsex "
                . " FROM matrix.cliame_000101 ing "
                . " JOIN matrix.cliame_000100 pac ON pac.Pachis = ing.inghis "
                . "WHERE ing.Inghis = ? AND ing.Ingnin = ?";

        if ($sql = mysqli_prepare($this->conex, $query)) {
            $sql->bind_param("ii", $this->numeroHistoria, $this->numeroIngreso);
            $sql->execute();

            if ($sql->num_rows() > 0) {
                $sql->bind_result(
                        $this->numeroHistoria, $this->numeroIngreso,
                        $this->tipoDocumento, $this->numeroDocumento,
                        $nombre1, $nombre2, $apellido1, $apellido2,
                        $this->diagnostico, $this->aseguradora,
                        $fechaNacimiento, $this->genero);
                $this->edad = $this->getEdad($fechaNacimiento);
                $this->nombresCompletos($nombre1, $nombre2);
                $this->apellidosCompletos($apellido1, $apellido2);
                $fetch = $sql->fetch();
            }
        }
    }

    /*
     * Retorna la edad, partiendo de la fecha de nacimiento.
     * 
     */

    function getEdad($fechaNacimiento) {
        $fecha = new DateTime($fechaNacimiento);
        $ahora = new DateTime();
        return $ahora->diff($fecha)->y;
    }

    function getNombresCompletos() {
        $this->nombresCompletos = $this->nombre1;
        if ($this->nombre2) {
            $this->nombresCompletos .= " " . $this->nombre2;
        }
        return $this->nombresCompletos;
    }

    function getApellidos() {
        $this->apellidosCompletos = $this->apellido1;
        if ($this->apellido2) {
            $this->apellidosCompletos .= " " . $this->apellido2;
        }
        return $this->apellidosCompletos;
    }

}
