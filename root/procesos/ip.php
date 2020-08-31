<?php
include_once("conex.php");
   function captura_ip()
   {
      if ($_SERVER) {
         if ( $_SERVER[HTTP_X_FORWARDED_FOR] ) {
            $ip_real = $_SERVER['HTTP_X_FORWARDED_FOR'];
         }
         elseif ( $_SERVER['HTTP_CLIENT_IP'] ) {
            $ip_real = $_SERVER['HTTP_CLIENT_IP'];
         }
         else {
            $ip_real = $_SERVER['REMOTE_ADDR'];
         }
      }
      else {
         if ( getenv( 'HTTP_X_FORWARDED_FOR' ) ) {
            $ip_real = getenv( 'HTTP_X_FORWARDED_FOR' );
         }
         elseif ( getenv( 'HTTP_CLIENT_IP' ) ) {
            $ip_real = getenv( 'HTTP_CLIENT_IP' );
         }
         else {
            $ip_real = getenv( 'REMOTE_ADDR' );
         }
      }
      return $ip_real;
   }
   echo "IP:"."$REMOTE_ADDR<br>";
  $teste=$HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"];
  echo $teste."<br>";
?>