<?php
include_once("conex.php");
// Imprime ejemplo 'Versi�n actual de PHP'
echo 'Versi�n actual de PHP: ' . phpversion();

// Imprime ejemplo '2.0' o nada si la extensi�n no est� habilitada
echo phpversion('tidy');
// Imprime ejemplo 'Versi�n actual de PHP'

?>
