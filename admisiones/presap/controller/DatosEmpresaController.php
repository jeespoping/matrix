<?php

namespace Admisiones\Controller;

include('root/comun.php');

/**
 * Description of DatosEmpresaController
 *
 * @author edier
 */
class DatosEmpresaController
{

    private $codigo;
    private $nombre;
    private $tcc;
    private $estado;
    private $nit;
    private $baseDeDatos;
    private $ingpos;
    private $codigoEmpresa;

    function __construct($conex, $wEmp)
    {
        $institucion = new consultarInstitucionPorCodigo($conex, $wEmp);
        $aliasInstitucion = new consultarAliasPorAplicacion();

        $this->codigo = $institucion->codigo;
        $this->nombre = $institucion->nombre;
        $this->tcc = $institucion->tcc;
        $this->estado = $institucion->estado;
        $this->nit = $institucion->nit;
        $this->baseDeDatos = $institucion->baseDeDatos;
        $this->ingpos = $institucion->ingpos;
    }

    public function getDatosPaciente()
    {
        $query = "SELECT Empcod, Empdes, Emptcc, Empest, Empnit, Empbda, Empipo "
            . " FROM root_000050 "
            . " WHERE empcod = ? AND empest = 'on'";

        try {
            $sql = mysqli_prepare($this->conex, $query);

            $sql->bind_param("i", $this->codigoEmpresa);
            $sql->execute();

            $sql->bind_result(
                $this->codigo, $this->nombre,
                $this->tcc, $this->estado,
                $this->nit, $this->aseguradora,
                $this->fechaNacimiento, $this->genero);

            $sql->fetch();

            $this->calcularEdad($this->fechaNacimiento);
        } catch (Exception $exc) {
            $this->error = $exc->error;
        }
    }

    /**
     * @return mixed
     */
    public function getImagenURL()
    {
        return '../../images/medical/root/cliame.jpg';
    }
}
