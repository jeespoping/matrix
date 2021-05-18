<?php

namespace ReporteServicios\Api\Model;

use ReporteServicios\Api\Model\DB;

//include "ips/funciones_facturacionERP.php";

class IngresosModel
{

  private $numeroHistoria;
  private $numeroIdentificacion;
  private $fechaInicial;
  private $fechaFinal;
  private $baseDatos;
  private $db;

  /**
   * Costructor del modelo de Ingresos
   */
  function __construct($baseDatos, $numHis, $numIde, $fecIni, $fecFin, $dbName = "Matrix")
  {
    $this->baseDatos = $baseDatos;
    $this->numeroHistoria = $numHis;
    $this->numeroIdentificacion = $numIde;
    $this->fechaInicial = $fecIni;
    $this->fechaFinal = $fecFin;
    $this->db = new DB($dbName);
  }

  function getIngresos()
  {
    $query = $this->db->query($this->getSQL());
    return $query->fetchAll();
  }

  /**
   * Generar la consulta SQL
   * @return string
   */
  function getSQL()
  {
    $sql = $this->getSelectFrom() . $this->getWhere() . $this->getOrder();
    return $sql;
  }

  function getSelectFrom()
  {
    $select = "SELECT c100.pachis as historia, c101.Ingnin as ingreso, c100.pacdoc as documento ";
    $from = "FROM {$this->baseDatos}_000100 AS c100 ";
    $leftJoin = "LEFT JOIN {$this->baseDatos}_000101 AS c101 ON c100.Pachis = c101.Inghis ";

    return $select . $from . $leftJoin;
  }

  function getWhere()
  {
    $sql = "WHERE ";
    if (!empty($this->numeroHistoria)) {
      $sql .= "c100.pachis = '{$this->numeroHistoria}' ";
    } elseif (!empty($this->numeroIdentificacion)) {
      $sql .= "c100.pacdoc = '{$this->numeroIdentificacion}' ";
    }

    if (!empty($this->fechaInicial) && !empty($this->fechaFinal)) {
      $sql .= "AND c101.Ingfei BETWEEN '{$this->fechaInicial}' AND '{$this->fechaFinal}' ";
    } elseif (!empty($this->fechaInicial) && empty($this->fechaFinal)) {
      $sql .= "AND c101.Ingfei >= '{$this->fechaInicial}' ";
    } elseif (empty($this->fechaInicial) && !empty($this->fechaFinal)) {
      $sql .= "AND c101.Ingfei <= '{$this->fechaFinal}' ";
    }
    return $sql;
  }

  function getOrder()
  {
    return "ORDER BY c101.ingfei DESC ";
  }

  public function __destruct()
  {
    $this->db->close();
  }
}
