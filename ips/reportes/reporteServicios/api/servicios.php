<?php
// include_once("conex.php");
// include_once("root/comun.php");
// include_once("ips/funciones_facturacionERP.php");
include_once("model/db.php");

include "model/ServiciosModel.php";
include "model/IngresosModel.php";

use ReporteServicios\Api\Model\IngresosModel;
use ReporteServicios\Api\Model\ServiciosModel;

$dbname = "Matrix";
$id = '';
$json = "";

//$db = new DB($dbname);

$method = $_SERVER['REQUEST_METHOD'];
//$movhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
// $server = $_SERVER['PATH_INFO'];
// $request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
//$input = json_decode(file_get_contents('php://input'),true);

switch ($method) {
  case 'GET':
    // $sql = "select * from contacts" . ($id ? " where id=$id" : '');
    if (isset($_GET['id'])) {
      $id = $_GET['id'];
      // $sql = $db->query('select * from contacts where id = ? ', $id);
    } else {
      $sql = $db->query('select tcaring, Tcarconnom, tcarfec, tcarest from matrix.cliame_000106 order by tcarfec LIMIT 0, 10');
      // $sql = $db->query("select * from contacts");
    }
    break;
  case 'POST':
    $baseDatos = $_POST["baseDatos"];
    $numHis = $_POST["numHis"];
    $numIde = $_POST["numIde"];
    $fecFin = $_POST["fecFin"];
    $fecIni = $_POST["fecIni"];

    $ingresos = new IngresosModel($baseDatos, $numHis, $numIde, $fecIni, $fecFin, $dbname);
    $dataIngresos = $ingresos->getIngresos();

    $servicios = new ServiciosModel($baseDatos);

    $result = array();
    for ($j = 0; $j < count($dataIngresos); $j++) {
      $numeroHistoria = $dataIngresos[$j]['historia'];
      $numeroIngreso = $dataIngresos[$j]['ingreso'];
      $servicios->setNumeroHistoria($numeroHistoria);
      $servicios->setNumeroIngreso($numeroIngreso);
      $dataServicio = $servicios->getServicios();
      // $estadoIngreso = rand(0, 1) == 0 ? "off" : "on";
      $estadoIngreso = $servicios->getEstadoPaciente();
      // $dataServicio[0]['servicio'] =

      if (!empty($dataServicio)) {
        for ($k = 0; $k < count($dataServicio); $k++) {
          $dataServicio[$k]['estado'] = $estadoIngreso;
          array_push($result, $dataServicio[$k]);
        }
      }
    }
    break;
}

// run SQL statement

if ($method == 'GET' || $method == 'POST') {
  // $result = $sql->fetchAll();

  if (!empty($result))
    $json = '[';
  for ($i = 0; $i < count($result); $i++) {
    $json .= ($i > 0 ? ',' : '') . json_encode($result[$i]);
  }
  if (!empty($result)) {
    $json .= ']';
  }
  echo $json;
  // } elseif ($method == 'POST') {
  // echo json_encode($sql->fetchAll());
} else {
  echo $sql->affectedRows();
}

$ingresos = null;
$servicios = null;
