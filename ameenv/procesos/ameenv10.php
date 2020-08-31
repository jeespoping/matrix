<?php
include_once("conex.php"); 
session_start();
if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>".
        " index.php</FONT></H1>\n</CENTER>");

// Este programa 1ro tiene la opcion para Subir un archivo plano en la ruta /www/matrix/ameenv/procesos con el Nombre facneps1.txt
// Cuando ya hemos cargado el archivo plano llenamos la tabla en UNIX ameenvah de manera que quede lista para que cartera radique

/* El archivo plano trae cuatro campos separados por | pipe ( FACTURA|VALOR SALDO|FECHA RADICADO|NUMERO RADICADO )
   Ejemplo del archivo plano:

4755648|3216548|20/12/2017|59424223
4749425|92949|20/12/2017|59424224
4743523|93670|20/12/2017|59424225
4752874|104989|20/12/2017|59424226

*/



mysql_select_db("matrix") or die("No se selecciono la base de datos");  
$conexN = odbc_connect('Facturacion','','') or die("No se realizo Conexion con la BD facturacion en Informix");

// Usuario que Entrega 
   $usrent='05900';
// Usuario que Recibe
   $usrrec='01102';
// Nro de Conciliacion
   $wcon=".";
// Ruta para el archivo plano 
   //$ruta="facneps1.txt";
     $ruta="/var/www/matrix/planos/facneps1.txt";  
// Caracter separador de campos en el archivo plano
   $wsep = "|";	      
   
$archivo = strtolower($ruta);
if (!file_exists($archivo)) 	
 exit( "No existe archivo plano en: ".$archivo." separado por ".$wsep." Para subir" );	

  $fecha = date("Y-m-d");
  $hora = (string)date("H:i:s");	
  $fechaw = $fecha." ".$hora;
  $fechaw = date ( 'Y-m-d H:i:s' , $fechaw );
	   
/* LEEMOS EL ARCHIVO PLANO CON EL SEPARADOR INDICADO Y VAMOS LLENANDO LA TABLA  ameenvah  */
  $file = fopen($archivo,"r");
  $i=0;
  while (!feof($file) and file_exists ($archivo)  )
  {
	$size = filesize($archivo)+1;	
	$data=fgetcsv($file,$size,$wsep);    //Leemos un registro y automaticamente se crea arreglo $data[] con el valor de cada campo 
	$num = count ($data);                //Numero de campos separados por por |
	
	if ($num > 1)   //Por si al final viene una linea en blanco chequeo que el nro de campos sea mayor 1
	{
		
	 // Como una factura puede estar en varias cartas de envio, Buscamos la ultima 
	 $query="Select MAX(envdetdoc) From caenvdet Where envdetfue='80' And envdetfan='20' And envdetdan=".$data[0];
     $resultadoC = odbc_do($conexN,$query);   // Ejecuto el query  
     if (odbc_fetch_row($resultadoC))         // Encontro 
	 {
	   $wnro=TRIM(odbc_result($resultadoC,1));
	   
	   //Verifico que la factura a grabar no este ya en estado RD Y RV o sea ya haya sido radicada
	   $query="Select encest From caenc Where encfue='20' And encdoc=".$data[0]." And encanu = 0 And encest NOT IN ('RD','RV')";
       $resultadoC = odbc_do($conexN,$query);   // Ejecuto el query  
       if (odbc_fetch_row($resultadoC))         // Encontro 
	   {
		  
	   //Como la tabla ameencah tiene como clave unica el nro de la carta de cobro + la fecha de adicion del registro le sumo un segundo
	   //a la hora para evitar duplicado
	   $fechaw = strtotime ( '+1 second' , strtotime ( $fechaw ) );
	   $nuevahora = date ( 'H:i:s' , $fechaw  );
	   $fechaw = $fecha." ".$nuevahora;
	   
	   // Ajusto la fecha de radicado que en el archivo viene dd-mm-yyyy al formato yyyy-mm-dd H:m:s   
	   $fechar = substr($data[2],6,4)."-".substr($data[2],3,2)."-".substr($data[2],0,2)." 00:01:01";
	   
	   // Ajusto la fecha de radicado al formato de fecha de Sello yyyy-mm-dd
	   $fechas = substr($data[2],6,4).substr($data[2],3,2).substr($data[2],0,2);
	   
 	   $query = "INSERT INTO ameenvah (envahnro,envahfac,envahent,envahdes,envahfec,envahrec,envahrad,envahusr,envahgui,envahcon,envahfad,envahfre,envahfra)"
                ." VALUES (".$wnro.",".$data[0].",'".$usrent."','CARTERA','".$fechas."','".$usrrec."','N','".$user."','".$data[3]."','".$wcon."','".$fechaw."','".$fechaw."','".$fechar."')";   
       //ADICIONO	
	   $resultado = odbc_do($conexN,$query) or die($query); 
       $i++;	   
	 
	   }
	   else   
		echo "<br>Factura ".$data[0]." Esta en estado RD o RV o esta anulada.";
	  }
	  else
	   echo "<br>Factura ".$data[0]." No tiene carta de cobro."; 
	} 

  }	
  
  echo "<br>En la tabla ameenvah se insertaron ".$i." registros.";
  fclose ($file); 
  odbc_free_result($resultadoC);
  odbc_close($conexN);
?>