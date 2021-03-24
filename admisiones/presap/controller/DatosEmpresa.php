<?php

namespace Admisiones\Controller;

/**
 * Description of DatosEmpresa
 *
 * @author edier
 */
class DatosEmpresa {

    private $codigo;
    private $nombre;
    private $tcc;
    private $estado;
    private $nit;
    private $baseDeDatos;
    private $ingpos;

    function __construct($conex, $wEmp) {
        $institucion = consultarInstitucionPorCodigo($conex, $wEmp);
        
        $this->codigo = $institucion->codigo;
        $this->nombre = $institucion->nombre;
        $this->tcc = $institucion->tcc;
        $this->estado = $institucion->estado;
        $this->nit = $institucion->nit;
        $this->baseDeDatos = $institucion->baseDeDatos;
        $this->ingpos = $institucion->ingpos;
    }

}
