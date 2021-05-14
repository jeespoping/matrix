<?php

namespace ReporteServicios\Api\Model;

use ReporteServicios\Api\Model\DB;


class BaseServicios
{

  private $numeroHistoria;
  private $numeroIdentificacion;
  private $fechaInicial;
  private $fechaFinal;
  private $empresa;
  private $db;

  /**
   * Costructor del modelo de Ingresos
   */
  function __construct($empresa, $numHis, $numIde, $fecIni, $fecFin, $dbName = "Matrix")
  {
    $this->empresa = $empresa;
    $this->numeroHistoria = $numHis;
    $this->numeroIdentificacion = $numIde;
    $this->fechaInicial = $fecIni;
    $this->fechaFinal = $fecFin;
    $this->db = new DB($dbName);
  }

  public function __destruct()
  {
    $this->db->close();
  }
}
