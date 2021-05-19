<?php
namespace ReporteServicios\Api\Model;

use ReporteServicios\Api\Model\DB;

class PacientesModel
{

  private $numeroHistoria;
  private $numeroIdentificacion;
  private $baseDatos;
  private $db;

  function __construct($baseDatos, $numHist, $numIde, $dbName = "Matrix")
  {
    $this->baseDatos = $baseDatos;
    $this->numeroHistoria = $numHist;
    $this->numeroIdentificacion = $numIde;
    $this->db = new DB($dbName);
  }

  public function __destruct()
  {
    $this->db->close();
  }

  function getPacienteJson()
  {
    $json = "";
    $result = $this->getPacienteArray();
    if (count($result)) {
      $json = json_encode($result);
    }
    return $json;
  }

  function getPacienteArray(): array
  {
    $query = $this->db->query($this->getSql(), $this->numeroHistoria, $this->numeroIdentificacion);
    return $query->fetchAll();
  }

  function getSql()
  {
    $sql = "SELECT c100.pachis as numeroHistoria, c100.Pactdo as tipoDocumento,
                       c100.pacdoc as documento, c100.Pacnoa as nombre
                  FROM matrix.{$this->baseDatos}_000100 AS c100
            INNER JOIN {$this->baseDatos}_000101 AS c101 ON c100.Pachis = c101.Inghis
                 WHERE (c100.pachis = ? OR c100.pacdoc = ?)
              GROUP BY c100.pachis";
    return $sql;
  }
}
