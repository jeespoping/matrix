<?php

include_once "model/db.php";
// include "ips/funciones_facturacionERP.php";

include "model/PacientesModel.php";

$dbname = "Matrix";
$id = '';
$json = "";

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
  case 'POST':
    $baseDatos = $_POST["baseDatos"];
    $numHis = $_POST["numHis"];
    $numIde = $_POST["numIde"];
    $fecFin = $_POST["fecFin"];
    $fecIni = $_POST["fecIni"];

    $pacientes = new PacientesModel($baseDatos, $numHis, $numIde, $dbname);

    break;
}

// run SQL statement

if ($method == 'GET' || $method == 'POST') {
  $result = $pacientes->getPacienteArray();

  if (!$id)
    $json = '[';
  for ($i = 0; $i < count($result); $i++) {
    $json .= ($i > 0 ? ',' : '') . json_encode($result[$i]);
  }
  if (!$id)
    $json .= ']';
  echo $json;
  // } elseif ($method == 'POST') {
  // echo json_encode($sql->fetchAll());
} else {
  echo $sql->affectedRows();
}

$pacientes = null;
