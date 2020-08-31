<?php
// $cmd = 'ls -l';
// $cmd = 'whoami';
 $cmd = 'ping -c 1 132.1.18.9 2>&1';
 echo exec($cmd, $rtn, $stat);
 echo '<br />';
 if ($stat == 0) {
  print_r($rtn);
 } else {
  echo 'No se pudo ejecutar el Comando';
 }
 echo '<hr>';
 passthru($cmd, $rtn);
 print_r($rtn);
 echo '<hr>';
 echo system($cmd, $rtn);
 echo '<br />';
 print_r($rtn);
 echo '<hr>';
 echo shell_exec($cmd);
?>
