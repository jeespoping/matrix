<?php

function endRoutine( $respuesta, $statusNumber = 0 ){

  odbc_close_all();
  $respuesta['status'] = ( $statusNumber != 0 ) ? $statusNumber : $respuesta['status'];
  header('X-PHP-Response-Code: '.$respuesta['status'], true, $respuesta['status']);
  echo json_encode(  $respuesta  );
  exit();
}

function respuestaErrorHttp( $mensaje = "" ){
  global $respuesta;
  $respuesta['mensaje'] = $mensaje;
  header("HTTP/1.1 500 Internal server error");
  echo json_encode( $respuesta  );
  exit();
}

?>