	<?php
include_once("conex.php");
	function imprimir($tip,$so,$data)
	{
		$ERR=fwrite ($so,$data);
		if($tip == 1)
 			sleep(1);
		if($ERR === false)
 			echo "Error en escritura<br>";
	}
	$addr="132.1.18.76";
	$fp = fsockopen( $addr,9100, $errno, $errstr, 30);
   	if(!$fp) 
			echo "ERROR : "."$errstr ($errno)<br>\n";
   	else 
   	{
		echo  "FP: ".$fp;
		// Longitud del area de impresion ultimo parametro : 26 lineas
 		imprimir (0,$fp,chr("27").chr("67").chr("15"));
 		// Colocarle slash al cero ultimo parametro en : 1
 		imprimir (0,$fp,chr("27").chr("47").chr("1"));
 		// Cancela la expansion de ancho y alto dos ultimos parametros en : 0 y 0
 		imprimir (0,$fp,chr("27").chr("105").chr("0").chr("0"));
 		// Especifica 12 puntos x pulgada
 		imprimir (0,$fp,chr("27").chr("77"));
 		// Centra la impresion ultimo parametro : 0 izquierda 1 centro 2 derecha
 		imprimir (0,$fp,chr("27").chr("29").chr("97").chr("1"));
 		// Impresion de un logo en la memoria posicion 1(49) tipo normal 0(48)
 		imprimir (0,$fp,chr("27").chr("28").chr("112").chr("1").chr("0"));
 		// Imprimir LF
 		imprimir (0,$fp,chr("10"));
 		// Coloca el texto subrayado ultimo parametro en 1(49)
 		imprimir (0, $fp, chr("27").chr("45").chr("49"));
 		// Texto y LF
 		imprimir (0, $fp,"VIVA EL VERDE".chr("10"));
 		// Cancela el texto subrayado ultimo parametro en 0(48)
 		imprimir (0, $fp, chr("27").chr("45").chr("48"));
 		// Impresion de codigo de barras parametro 3(54 = 4 = 3de9) parametro 4(2 = adiciona caracteres debajo de las barras y LF)  parametro 5(1 ancho del codigo 2:6 puntos) parametro 6(alto del codigo = 60) siguen datos(en codigo decimal) y termina con RS(30)
 		imprimir (0,$fp,chr("27").chr("98").chr("52").chr("2").chr("1").chr("60").chr("50").chr("52").chr("54").chr("55").chr("52").chr("53").chr("30"));
 		// Form Feed
 		imprimir (0,$fp,chr("12"));
 		// Auto cortado del papel ultimo parametro (0 = Full)
 		imprimir (1,$fp,chr("27").chr("100").chr("0"));
	 }
	fclose($fp);
?>

