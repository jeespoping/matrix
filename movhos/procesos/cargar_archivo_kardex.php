<?php
include_once("conex.php");
include_once("movhos/kardex.inc.php");

$real= $HTTP_POST_FILES['file']['name'];
$files=$HTTP_POST_FILES['file']['tmp_name'];

//$dh = opendir($ruta);
//if(readdir($dh) == false){
//	mkdir($ruta,0777);
//}

$fechaNombre = date("Ymd");
$horaNombre = date("His");

$extension = substr($real,strrpos($real,'.'));
$extension = strtolower($extension);

$ruta = "./../../planos/$historia-$ingreso-$fecha-$fechaNombre-$horaNombre$extension";

$existe = in_array($extension,$extensionesPosibles);

if($existe){
	//Borro archivo anterior si aplica
	@unlink($ruta);

	if (!isset($ruta) or !copy($files, $ruta)){
		echo "No se pudo cargar el archivo";
	} else {
		//Actualizo la ruta en el encabezado
		actualizarRutaArchivoKardex($historia,$ingreso,$fecha,$ruta,$usuario);

		echo "Archivo cargado exitosamente: ";
		echo "<a id='lkRuta' href='$ruta' target='_blank'><span class='textoMedio'>Ver</span></a><br/>";
			
		echo "<input type=hidden id='hdRutaOrden' name='hdRutaOrden' value='$ruta'>";
	}
} else {
	echo "El tipo de archivo de imagen no es válido.";
}
?>