<?php

namespace Admisiones\Controller;

include dirname(__FILE__) . '\..\model\Paciente.php';

use Admisiones\Model\Pacientes;
use DateTime;
use Exception;

//include_once("root/comun.php");
/*
 * Datos socioeconomicos de un paciente.
 *
 * @author Edier Andres Villaneda Navarro
 */

class DatosSocioeconomicosController extends Pacientes
{

    private $conex;
    private $error;

    /**
     * Consultar los datos socioeconomicos de un paciente.
     *
     * @param object $conex datos de la conexión a la base de datos. (Obligatorio)
     * @param string $numeroHistoria Numero de Historia Clinica del paciente.
     * @param string $numeroIngreso Numero de ingreso del paciente.
     */
    public function __construct($conex, $numeroHistoria = "", $numeroIngreso = "")
    {
        $this->conex = $conex;
        $this->setNumeroHistoria($numeroHistoria);
        $this->setNumeroIngreso($numeroIngreso);
        $this->error = "";
    }

    /**
     * Validar si los numeros de Historia e Ingreso son validos.
     *
     * @return boolean
     */
    public function validarDatos(): bool
    {
        switch (true) {
            case $this->getNumeroHistoria() == "":
                $this->error = "Número de Historia invalido";
                //                throw new \Exception("Número de Historia invalido");
                break;
            case $this->getNumeroIngreso() == "":
                $this->error = "Número de Ingreso invalido";
                //                throw new \Exception("Número de Ingreso invalido");
                break;
        }
        if ($this->error != "") {
            return false;
        }
        return true;
    }

    /**
     *
     */
    public function getDatosPaciente()
    {
        $query = "SELECT pac.Pactdo AS tipodocumento, pac.Pacdoc as documento, "
            . "pac.Pacno1 AS nombre, pac.Pacno2 AS segundo_nombre, "
            . "pac.Pacap1 AS primer_apellido, pac.Pacap2 AS segundo_apellido, "
            . "ing.Ingdig AS diagnostico, aseg.Empnom AS aseguradora, pac.Pacfna AS fecha_nacimiento, pac.Pacsex as sexo "
            . "FROM matrix.cliame_000101 AS ing "
            . "INNER JOIN matrix.cliame_000100 AS pac ON pac.Pachis = ing.inghis "
            . "INNER JOIN matrix.cliame_000105 AS est ON pac.Pacest = est.Selcod AND est.seltip = 25 "
            . "INNER JOIN matrix.cliame_000024 AS aseg ON ing.Ingcem = aseg.Empcod "
            . "WHERE ing.Inghis = ? AND ing.Ingnin = ?";

        try {
            $sql = mysqli_prepare($this->conex, $query);

            $sql->bind_param("ii", $this->numeroHistoria, $this->numeroIngreso);
            $sql->execute();

            $sql->bind_result($this->tipoDocumento, $this->numeroDocumento,
                $this->nombre1, $this->nombre2,
                $this->apellido1, $this->apellido2,
                $this->diagnostico, $this->aseguradora,
                $this->fechaNacimiento, $this->genero);

            $sql->fetch();

            $this->calcularEdad($this->fechaNacimiento);
        } catch (Exception $exc) {
            $this->error = $exc->error;
        }
    }

    public function getDatosDiagnostico()
    {
        $query = "SELECT  'K088', 'Descripcion' "
            . " FROM matrix.cliame_000101 ing "
            . " JOIN matrix.cliame_000100 pac ON pac.Pachis = ing.inghis "
            . "WHERE ing.Inghis = ? AND ing.Ingnin = ?";
        try {
            $sql = mysqli_prepare($this->conex, $query);

            $sql->bind_param("ii", $this->numeroHistoria, $this->numeroIngreso);
            $sql->execute();

            $sql->bind_result($this->diagnostico, $this->aseguradora);

            $sql->fetch();

            $this->calcularEdad($this->fechaNacimiento);
        } catch (Exception $exc) {
            $this->error = $exc->error;
        }
    }

    /*
     * Retorna la edad, partiendo de la fecha de nacimiento.
     *
     */

    private function calcularEdad()
    {
        $ahora = new DateTime();
        $edad = $ahora->diff($this->getFechaNacimiento())->y;
        $unidad = " años";
        if ($edad < 1) {
            $edad = $ahora->diff($this->getFechaNacimiento())->m;
            $unidad = " meses";
        }
        $this->setEdad($edad . $unidad);
    }

    function getError(): string
    {

        return $this->error;
    }

}
