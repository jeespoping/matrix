<head>
  <title>BUSQUEDA DE PACIENTES Y UTILIZACION DE CAMAS POR SERVICIO y HABITACION</title>
</head>
<body BACKGROUND="nubes.gif">
<script type="text/javascript">
	function enter()
	{
	   document.forms.camas.submit();
	}

</script>
<?php
include_once("conex.php");
  /**********************************************************
   *       UTILIZACION DE CAMAS POR SERVICIO HABITACION     *
   *       PORCENTAJES DE UTILIZACION SERVICIO Y CLINICA    *
   *     				CONEX, FREE => OK				    *
   *********************************************************/

   //==================================================================================================================================================//
   // Este programa muestra la ocupación total de la clinica y de cada una de sus unidades de hospitalización, cirugia y urgencias, asi como de los
   // pacientes que se les hace ingreso y solo vienen a realizarse examenes. Para los pacientes de Urgencias y Cirugia tiene en cuenta el dia anterior
   // y el actual y que no se hallan facturado.
   // Además cuando se consulta por algún campo de la pantalla, trae los pacientes que fuerón dados de alta y que ya no se encuentran en la habitación,
   // pero que todavia figuran como activos en el sistema.Estos pacientes se identifican con el servicio de "Egresados"
   // También trae los pacientes que ingresan por algún servicio y se van a quedar hospitalizados, pero aún no se les ha asignado habitación, estos
   // pacientes se identifican con el servicio "Sin habitación"
   //==================================================================================================================================================//

session_start();

if (!isset($user))
	if(!isset($_SESSION['user']))
	  session_register("user");


if(!isset($_SESSION['user']))
	echo "error";
else
{

  

						or die("No se ralizo Conexion");
  


  $conexunix = odbc_connect('informix','facadm','1201')
  					    or die("No se ralizo Conexion con el Unix");


  $pos = strpos($user,"-");
  $wusuario = substr($user,$pos+1,strlen($user));

	                                                           // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
  $wactualiz="(Versión Febrero 22 de 2005)";                   // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                                           // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //

  $wfecha = date("Y-m-d");
  $whora  = (string)date("H:i:s");

  echo "<br>";
  echo "<br>";

  echo "<form name='camas' action='Utilizacion_camas.php' method=post>";

  //HABITACION
  //echo "<tr><td bgcolor=#cccccc colspan=8><font size=2><b>Habitacion :</b></font><select name='whab'>";
  $q = " SELECT habcod, habcco, habhis  "
      ."   FROM inhab "
      ."  WHERE habact= 'S' "
      ."  ORDER BY habcod ";
  $res = odbc_do($conexunix,$q);

  //echo "<option selected>*- Todas las Habitaciones </option>";
  while(odbc_fetch_row($res))
      {
       //echo "<option value>".odbc_result($res,1)."</option>";

       if (odbc_result($res,3) != "")
          {
	       //Busco el numero de ingreso de la historia
	       $q = " SELECT pacnum  "
		      ."   FROM inpac "
		      ."  WHERE pachis= ".odbc_result($res,3);
		   $resing = odbc_do($conexunix,$q);

		   $wing=odbc_result($resing,1);
	      }
	     else
	        $wing="";

	   /*
       echo odbc_result($res,1)."<br>";
       echo odbc_result($res,2)."<br>";
       echo odbc_result($res,3)."<br>";
       echo $wing."<br>";
	   */

       $q= " INSERT INTO movhos_000020 ( medico ,   fecha_data ,   hora_data ,   habcod                ,   habcco                ,   habhis                ,   habing  , habest, seguridad ) "
          ."                    VALUES ('movhos','".$wfecha."' ,'".$whora."' ,'".odbc_result($res,1)."','".odbc_result($res,2)."','".odbc_result($res,3)."','".$wing."', 'on'  ,'C-".$wusuario."')";
       $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      }
  echo "TERMINO DE PASAR LAS HABITACIONES";
  
  	odbc_close($conexunix);
	odbc_close_all();
} // if de register

include_once("free.php");

?>
