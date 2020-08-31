<?php
include_once("conex.php");
echo "<frameset rows='30%,70%' frameborder=0 framespacing=0>";
echo "<frame src='/matrix/Presupuestos/Reportes/000001_rc98B.php?wanop=".$wanop."&wper1=".$wper1."&wper2=".$wper2."' name='titulos' marginwidth=0 marginheiht=0>";
echo "<frame src='/matrix/Presupuestos/Reportes/000001_rc98C.php?wanop=".$wanop."&wper1=".$wper1."&wper2=".$wper2."&wcco1=".$wcco1."&wcco2=".$wcco2."&wpro1=".$wpro1."&wpro2=".$wpro2."&wtp=".$wtp."&wpv=".$wpv."' name='main' marginwidth=0 marginheiht=0>";
?>