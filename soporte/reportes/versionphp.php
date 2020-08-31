<?php
include_once("conex.php");
// Imprime ejemplo 'Versión actual de PHP'
echo 'Versión actual de PHP: ' . phpversion();

// Imprime ejemplo '2.0' o nada si la extensión no está habilitada
echo phpversion('tidy');
// Imprime ejemplo 'Versión actual de PHP'

?>
