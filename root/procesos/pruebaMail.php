<?php
include_once("conex.php");
/************************************************************************************************************************
 * ESTE CODIGO SIRVE PARA ILUSTRAR EL MECANISMO PARA USAR LA CLASE PHPMAILER, PARA ENVIO DE CORREO POR EL PROTOCOLO SMTP
 ************************************************************************************************************************ 
 */
require("class.phpmailer.php");  				//Include de la clase PHPMailer

$mail = new PHPMailer();						//Instancia de la clase PHPMailer

$mail->IsSMTP(); 								//Indica que se va a usar SMTP
$mail->SMTPAuth = true;							//Indica que se requiere Autenticación

$mail->Username = "magenta";					//Indica el nombre del usuario con el que se realiza la Autenticación
$mail->Password = "servmagenta";				//Indica 

$mail->Host = "132.1.18.1"; 					//Direccion del servidor de salida 
$mail->From = "magenta@lasamericas.com.co";		//Origen del correo...NOTA: Lo reemplaza usuario SMTP
$mail->AddAddress("msanchez@lasamericas.com.co");			// Destinatario

$mail->Subject = "TITULO DEL CORREO - PRUEBA DEL CORREO";	//Titulo
$mail->Body = "CUERPO DEL MENSAJE CUERPO DEL MENSAJE....";	//Cuerpo

$mail->WordWrap = 100;							//Salto de linea cada 100 caracteres

if(!$mail->Send())
{ echo "El correo no fue enviado.  Error: ".$mail->ErrorInfo; }
else {	echo "Enviado correctamente."; }
$mail->ClearAddresses();
?>