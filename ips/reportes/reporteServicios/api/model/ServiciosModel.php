<?php

namespace ReporteServicios\Api\Model;

use ReporteServicios\Api\Model\DB;

/**
 * Ejecutar las consultas relacionadas a los servicios
 * de un numero de historia e ingreso.
 */
class ServiciosModel
{

  private $numeroHistoria;
  private $numeroIngreso;
  private $estado;
  private $baseDatos;
  private $db;

  /**
   * Constructor del modelo de Servicios
   * @param string $baseDatos Empresa en la que se realiza la consulta
   * @param integer $numHis Numero de historia que se quiere consultar
   * @param integer $numIde Numero de identificacion del paciente
   * @param string $dbName Nombre de la base de datos.
   */
  function __construct($baseDatos, $dbName = "Matrix")
  {
    $this->baseDatos = $baseDatos;
    $this->db = new DB($dbName);
  }

  /**
   * Establecer el numero de la historia
   * @param type $numeroHistoria
   * @return void
   */
  public function setNumeroHistoria($numeroHistoria): void
  {
    $this->numeroHistoria = $numeroHistoria;
  }

  /**
   * Establecer el numero del ingreso
   * @param type $numeroIngreso
   * @return void
   */
  public function setNumeroIngreso($numeroIngreso): void
  {
    $this->numeroIngreso = $numeroIngreso;
  }

  /**
   * Obtener los datos de los servicios de una historia e ingreso.
   * @return array Array de datos
   * @throws \Throwable
   */
  function getServicios()
  {
    try {
      $query = $this->db->query($this->getSQL(), $this->numeroHistoria, $this->numeroIngreso);
      return $query->fetchAll();
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Generar la clausula SQL de la consulta.
   * @return string
   */
  function getSql()
  {
    $sql = $this->getSelectFrom() . $this->getWhere() . $this->getOrderBy();
    return $sql;
  }

  /**
   * Generar la clausula SQL de select from.
   * @return string
   */
  function getSelectFrom()
  {
    $select = "SELECT c100.pachis as historia, c101.Ingnin AS ingreso,
                   c100.Pactdo as tipoDocumento, c100.pacdoc as Documento,
                   c100.Pacnoa as nombre, c101.Ingfei as fechaIngreso,
                   c101.Ingsei, m011.cconom as servicio, c101.Ingtin,
                   c175.Tiides, '' as estado ";
    $from = "FROM {$this->baseDatos}_000100 c100
             JOIN {$this->baseDatos}_000101 c101 on c100.pachis = c101.Inghis
             LEFT JOIN {$this->baseDatos}_000175 c175 ON c101.Ingtin = c175.Tiicod
             LEFT JOIN movhos_000011 m011 ON c101.Ingsei = m011.Ccocod ";
    return $select . $from;
  }

  /**
   * Generar la clausula SQL de condiciones.
   * @return string
   */
  function getWhere()
  {
    $where = "WHERE c100.pachis = ? AND c101.Ingnin = ? ";
    return $where;
  }

  /**
   * Generar la clausula SQL de ordenamiento.
   * @return string
   */
  function getOrderBy()
  {
    $orderBy = "ORDER BY c101.Ingfei Desc";
    return $orderBy;
  }

  /**
   * Consultar los datos del paciente.
   * @return array Array de los datos del paciente.
   */
  function getEstadoPaciente()
  {
    $estado =  "off";
    try {
      if ($conexUnix = odbc_connect('facturacion', 'informix', 'sco')) {
        // --> Consultar si el ingreso está activo en unix.
        $sqlIngAct = "SELECT pacnum FROM INPAC WHERE pachis = '{$this->numeroHistoria}' AND pacnum = {$this->numeroIngreso}";
        $resIngAct = odbc_exec($conexUnix, $sqlIngAct);
        if (odbc_fetch_row($resIngAct)) {
          if (trim(odbc_result($resIngAct, 'pacnum')) == $this->numeroIngreso) {
            $estado = "on";
          }
        }
      }
    } catch (\Exception $e) {
      echo 'Excepción capturada: ',  $e->getMessage(), "\n";
    }

    return $estado;
  }

  /**
   * Cerrar conexiones de la base de datos al dejar de usar la clase.
   */
  public function __destruct()
  {
    $this->db->close();
  }
}
