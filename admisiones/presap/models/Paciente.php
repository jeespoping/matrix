<?php


namespace Admisiones\models;

use DateTime;

/*
 * Informacion del paciente
 *
 * @author Edier Andrés Villaneda Navarro
 */

class Paciente
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
    protected $nombreCompleto;
    protected $documento;

    public function __construct($numeroHistoria, $numeroIngreso)
    {
        $this->setNumeroHistoria($numeroHistoria);
        $this->setNumeroIngreso($numeroIngreso);
    }

    /**
     * Primer apellido del paciente
     * @return string Apellido en mayuscula inicial
     */
    public function getApellido1(): string
    {
        return ucfirst(strtolower($this->apellido1));
    }

    public function getApellido2(): string
    {
        return ucfirst(strtolower($this->apellido2));
    }

    /**
     * Apellidos completos
     * @return string Apellidos ccapitaLizados
     */
    public function getApellidos(): string
    {
        $this->apellidos = $this->getApellido1();
        if ($this->apellido2) {
            $this->apellidos .= " " . $this->getApellido2();
        }
        return $this->apellidos;
    }

    public function getAseguradora()
    {
        return $this->aseguradora;
    }

    public function getDiagnostico(): string
    {
        return $this->diagnostico== null ? '': $this->diagnostico;
    }

    public function getEdad(): string
    {
        $unidad = "";
        $ahora = new DateTime();
        $edad = $ahora->diff($this->getFechaNacimiento())->y;
        $unidad = " anos";
        if ($edad < 1) {
            $edad = $ahora->diff($this->getFechaNacimiento())->m;
            $unidad = " meses";
        }
//            $unidad = iconv('UTF-8', 'windows-1252', $unidad);

        return $edad . $unidad;
    }

    public function getFechaNacimiento(): \DateTime
    {
        return new DateTime($this->fechaNacimiento);
    }

    public function getGenero()
    {
        return $this->genero;
    }

    public function getNombre1(): string
    {
        return ucfirst(strtolower($this->nombre1));
    }

    public function getNombre2(): string
    {
        return ucfirst(strtolower($this->nombre2));
    }


    /**
     * Nombres del paciente
     * @return string Nombre capitalizados
     */
    public function getNombres(): string
    {
        $this->nombres = $this->getNombre1();
        if ($this->nombre1) {
            $this->nombres .= " " . $this->getNombre2();
        }
        return $this->nombres;
    }

    /**
     * Obtener el nombre completo del paciente
     * @return string
     */
    public function getNombreCompleto(): string
    {
        return $this->getNombres() . " " . $this->getApellidos();
    }

    public function getNumeroDocumento()
    {
        return $this->numeroDocumento;
    }

    public function getNumeroHistoria()
    {
        return $this->numeroHistoria;
    }

    public function getNumeroIngreso()
    {
        return $this->numeroIngreso;
    }

    public function getTipoDocumento()
    {
        return $this->tipoDocumento;
    }

    /**
     * Obtener el tipo y numero de documento.
     * @return string
     */
    public function getDocumento(): string
    {
        return $this->getTipoDocumento() . " " . $this->getNumeroDocumento();
    }

    public function setNumeroHistoria($numeroHistoria)
    {
        $this->numeroHistoria = $numeroHistoria == "" ? 0 : $numeroHistoria;
        return $this;
    }

    public function setNumeroIngreso($numeroIngreso)
    {
        $this->numeroIngreso = $numeroIngreso == "" ? 0 : $numeroIngreso;
        return $this;
    }

    /**
     * @param $conex
     * @param $empresa
     */
    public function getDatosPaciente($conex, $empresa)
    {
        $query = "SELECT pac.Pactdo AS tipodocumento, pac.Pacdoc as documento, "
            . "pac.Pacno1 AS nombre, pac.Pacno2 AS segundo_nombre, "
            . "pac.Pacap1 AS primer_apellido, pac.Pacap2 AS segundo_apellido, "
            . "ing.Ingdig AS diagnostico, aseg.Empnom AS aseguradora, pac.Pacfna AS fecha_nacimiento, pac.Pacsex as sexo "
            . "FROM matrix.{$empresa}_000101 AS ing "
            . "INNER JOIN matrix.{$empresa}_000100 AS pac ON pac.Pachis = ing.inghis "
            . "INNER JOIN matrix.{$empresa}_000105 AS est ON pac.Pacest = est.Selcod AND est.seltip = 25 "
            . "LEFT JOIN matrix.{$empresa}_000024 AS aseg ON ing.Ingcem = aseg.Empcod "
            . "WHERE ing.Inghis = ? AND ing.Ingnin = ?";

        try {
            $sql = mysqli_prepare($conex, $query);

            $sql->bind_param("ii", $this->numeroHistoria, $this->numeroIngreso);
            $sql->execute();

            $sql->bind_result($this->tipoDocumento, $this->numeroDocumento,
                $this->nombre1, $this->nombre2,
                $this->apellido1, $this->apellido2,
                $this->diagnostico, $this->aseguradora,
                $this->fechaNacimiento, $this->genero);

            $sql->fetch();
        } catch (Exception $exc) {
            throw new Exception($exc);
        }
    }
}