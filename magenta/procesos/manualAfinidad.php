<html>
<head>
<title>AFINIDAD</title>
</head>
<body >
<?php
include_once("conex.php");

/**
 * MANUAL DE INSTALACION DE AFINIDAD
 * 
 * El programa de Afinidad, tiene como objeto llevar una base de datos de Clientes importantes para la Clinica, 
 * poder actualizar la información de dicha base de datos, realizar consultas sobre la información 
 * y realizar gestión sobre la forma en que se está dando atención a estos Clientes importantes. 
 * (Para más detalle, refierase al documento de requisitos del sistema)
 * ORDEN DE LOS SCRIPTS
 * El Script inicial es magenta.php el cual llama a persona.php y comentarios.php
 * El programa persona.php llama a actualizar.php
 * El resto son programas independientes
 * BASE DE DATOS:
 * 1. El sistema de afinidad consta de las siguientes tablas:
 *    000003 maestro de zonas, indica la zona para cada codigo de barrio de unix
 * 			 indice unico=Codigo_barrio
 *    000007 maestro de usuario, configura los permisos de usuario para consultar, clasificar y actualizar la informacion de los pacientes
 * 								los tipos de usuario son una seleccion
 *           indice unico=Codigo_nomina
 * 	  000008 tabla con la información principal de los clientes de afinidad (AFIN AAA, AFIN BBB, VIP y otros que se creen en el futuro)
 *           indice unico=Clidoc, Clitid
 * 	  000009 tabla con informacion auxiliar de los clientes de afinidad (AFIN AAA, AFIN BBB, VIP y otros que se creen en el futuro)
 *           indice unico=Cdedoc, Cdetid
 *    000011 tabla con las relaciones entre los afines AAA y BBB, tiene dos registros por cada relacion
 *           indice unico=Cafpdo, Cafpti, Cafrdo, Cafrti
 *    000013 maestro de tipos de paciente de afinidad, (AFIN AAA, AFIN BBB, VIP y otros que se creen en el futuro)
 *           indice unico=tipniv, tiptip
 * 	  000014 Tabla con la informacion de los pacientes clasificados (AFIN AAA, AFIN BBB, VIP ) que han venido a la clinica y detalles de la estancia
 *           indice unico=Reping, rephor, repdoc
 * 	  000015 maestro de empresas utilizadas para clasificar o desclasificar a un usuario AFIN AAA (deben ser las responsables de la facturacion en almenso uno de sus ultimos ingresos) 
 *           indice unico=Empcod
 *    Estas tablas deben crearse segun como se encuentra detallado en el diccionario de datos
 * 2. Llenar los maestros
 * 		El maestro de zonas puede quedar vacio si no se desea que los AFINES sean clasificados automaticamente por el sistema
 *      El maestro de usuarios debe llenarse de igual forma para afin, vip y no clasificados o desclasificados, pues sus parametros estan quemados en el programa
 * 3. Crear las siguientes selecciones propias de magenta:
 *    01 documento
 *    05 estado civil
 *    14 pais
 *    02 sexo
 *    16 usuario magenta
 *    13 zona usuario 
 *    15 servicios clinica
 * ARCHIVOS ADICIONALES A LOS SCRIPTS
 * matrix-images-medical-Magenta-m4.jpg 
 * matrix-magenta-procesos-index.htm  
 * matrix-magenta-procesos-primero.htm  
 * matrix-magenta-procesos-segundo.htm  
 * matrix-magenta-procesos-tercero.htm  
 * MATRIX-images-medical-root-clinica.JPG
 * MATRIX-images-medical-Magenta-aaa.gif
 * MATRIX-images-medical-Magenta-aaa.gif
 * MATRIX-images-medical-Magenta-bbb.gif
 * MATRIX-images-medical-Magenta-noclasificado.gif
 * MATRIX-images-medical-Magenta-VIP.gif
 * MATRIX-images-medical-Magenta-AAA.gif
 * MATRIX-images-medical-Magenta-BBB.gif
 * MATRIX-images-medical-Magenta-VIP2.gif
 * 
 * @name matrix\magenta\procesos\manualAfinidad.php
 * 
 */ 

echo 'manual';
/**
 * fucniones de afinidad
 */
include_once("/Magenta/incVisitas.php");
?>
</body>

</html>