<head>
  <title>REPORTE DE TRASLADOS DE INVENTARIOS</title>
</head>

<body>
<?php
include_once("conex.php");

//<body BACKGROUND="images/fondo de bebida de limón.gif">

  /****************************************************
   *	           IMPRIMIR LOS TRASLADOS             *
   *REALIZADOS EN LA UNIDAD DE SERVICIOS FARMACEUTICOS*
   *   				CONEX, FREE => OK				  *
   ****************************************************/
//session_start();

if (!isset($user))
	{
		if(!isset($_SESSION['user']))
			session_register("user");

	}


if(!isset($_SESSION['user']))
	echo "error";
else
{
  

						or die("No se ralizo Conexion");
  


  $conexunix = odbc_connect('inventarios','invadm','1201')
  					    or die("No se ralizo Conexion con el Unix");

  $pos = strpos($user,"-");
  $wusuario = substr($user,$pos+1,strlen($user));


	                                                           // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
  $wactualiz="(Versión Enero 19 de 2005)";                     // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                                           // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //

  echo "<br>";
  echo "<br>";

  echo "<form action='Tras_detalle.php' method=post>";
  //echo "<center><table border=2 width=400 BACKGROUND='images/nubes.jpg'>";
  echo "<center><table border=2 width=400 BACKGROUND='/images/fondo de bebida de limón.gif'>";

  echo "<tr><td align=center colspan=240 bgcolor=#fffffff><font size=6 text color=#CC0000><b>CLINICA LAS AMERICAS</b></font></td></tr>";
  echo "<tr><td align=center colspan=240 bgcolor=#fffffff><font size=4 text color=#CC0000><b>TRASLADOS DE INVENTARIOS PENDIENTES DE RECIBO</b></font></td></tr>";
  echo "<tr><td align=center colspan=240 bgcolor=#fffffff><font size=4 text color=#CC0000><b>DETALLE DE DOCUMENTOS</b></font></td></tr>";
  echo "<tr><td align=center colspan=240 bgcolor=#fffffff><font size=3 text color=#CC0000><b>".$wactualiz."</b></font></td></tr>";


  if (!isset($grabar))
      /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      // Ya estan todos los campos setiados o iniciados ===================================================================================
      /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      {

	   echo "<tr><td colspan=6 align=center bgcolor=#66CC99><b>Fuente: ".$fuente;
       echo "<input type='HIDDEN' name= 'fuente' value='".$fuente."'>";

	   //DOCUMENTO
       echo " - Documento: ".$documento."</b></td></tr>";
       echo "<input type='HIDDEN' name= 'documento' value='".$documento."'>";

       echo "<input type='HIDDEN' name= 'wanoi' value='".$wanoi."'>";
       echo "<input type='HIDDEN' name= 'wmesi' value='".$wmesi."'>";
       echo "<input type='HIDDEN' name= 'wdiai' value='".$wdiai."'>";
       echo "<input type='HIDDEN' name= 'wanof' value='".$wanof."'>";
       echo "<input type='HIDDEN' name= 'wmesf' value='".$wmesf."'>";
       echo "<input type='HIDDEN' name= 'wdiaf' value='".$wdiaf."'>";
       echo "<input type='HIDDEN' name= 'wcco' value='".$wcco."'>";


       $query = " SELECT movdetart, artnom, uninom, movdetcan "
               ."   FROM ivmovdet, ivart, ivuni "
               ."  WHERE movdetfue = '".$fuente."'"
               ."    AND movdetdoc = '".$documento."'"
               ."    AND movdetanu = '0' "
               ."    AND movdetart = artcod "
               ."    AND artuni    = unicod "
               ."  ORDER BY artnom ";


       echo "<tr>";
       echo "<th bgcolor=#fffffff>Articulo</th>";
       echo "<th bgcolor=#fffffff>Descripción</th>";
       echo "<th bgcolor=#fffffff>Unidad de Medida</th>";
       echo "<th bgcolor=#fffffff>Cant. Enviada</th>";
       echo "<th bgcolor=#fffffff>Faltante</th>";
       echo "<th bgcolor=#fffffff>Adicional</th>";
       echo "</tr>";

       $res = odbc_do($conexunix,$query);


       echo "<option selected>. </option>";

       $i=0;
	   while(odbc_fetch_row($res))
	      {
		    echo "<tr>";
		    echo "<td align=center bgcolor=#99FFCC><font size=3><b>".odbc_result($res,1)."</b></font></td>";
		    echo "<td align=left   bgcolor=#99FFCC><font size=2><b>".odbc_result($res,2)."</b></font></td>";
		    echo "<td align=left   bgcolor=#99FFCC><font size=2><b>".odbc_result($res,3)."</b></font></td>";
		    echo "<td align=center bgcolor=#99FFCC><font size=3><b>".odbc_result($res,4)."</b></font></td>";
		    echo "<td align=center bgcolor=#99FFCC><INPUT TYPE='text' NAME='wdif[".$i."]' SIZE = 4 value=0 ></td>";
		    echo "<td align=center bgcolor=#99FFCC><INPUT TYPE='text' NAME='wadi[".$i."]' SIZE = 4 value=0 ></td>";
		    echo "</tr>";

		    $wart[$i]=odbc_result($res,1);
		    $wenv[$i]=odbc_result($res,4);

		    echo "<input type='HIDDEN' NAME= 'wart[".$i."]' value='".$wart[$i]."'>";
		    echo "<input type='HIDDEN' NAME= 'wenv[".$i."]' value='".$wenv[$i]."'>";

		    $i=$i+1;
		  }
	   echo "</SELECT></td></tr>";

	   echo "<input type='HIDDEN' NAME= 'fuente' value='".$fuente."'>";
	   echo "<input type='HIDDEN' NAME= 'documento' value='".$documento."'>";
	   echo "<input type='HIDDEN' NAME= 'i' value='".$i."'>";

	   echo "<tr><td align=center colspan=6 bgcolor=#cccccc><b><INPUT TYPE='checkbox' NAME='grabar' VALUE=3 CHECKED>Grabar OK</b></td></tr>";

	   echo "<tr><td align=center colspan=6 bgcolor=#cccccc></b><input type='submit' value='ACEPTAR'></b></td></tr>";
	   echo "</table></form>";

      } // else de todos los campos setiados
     else
        {
	     $fecha = date("Y-m-d");
		 $hora = (string)date("H:i:s");

		 $wgrabar="S";
		 //Aca evaluo si el dato de diferencia es numerico y si hay algun articulo en el que la diferencia sea mayor a la cantidad enviada
		 for ($j=0;$j<=$i-1;$j++)
		     {
			  if (is_numeric($wdif[$j]))                // Verifico que lo digitado en faltante sea numerico
			     {
				  if ($wdif[$j] > $wenv[$j])            // Verifico que lo digitado en faltante no sea mayor a lo enviado
	                 {
		              //echo "</tr><tr><td bgcolor=#66CC99><font size=6><b><CENTER>!!! E R R O R ¡¡¡</b></font></td></tr>";
		              echo "</tr><tr><td bgcolor=#66CC99><font size=4><b><CENTER>!!! La diferencia NO puede ser mayor a la cantidad enviada en el articulo : ".$wart[$j]." ¡¡¡</b></font></td></tr>";
		              $wgrabar="N";
		             }
		          if (!is_numeric($wadi[$j]))           // Verifico que lo digitado en adicional sea numerico
			         {
				      //echo "</tr><tr><td bgcolor=#66CC99><font size=6><b><CENTER>!!! E R R O R ¡¡¡</b></font></td></tr>";
				      echo "</tr><tr><td bgcolor=#66CC99><font size=4><b><CENTER>!!! Adicional debe ser numerico en el articulo : ".$wart[$j]." ¡¡¡</b></font></td></tr>";
				      $wgrabar="N";
	                 }
	              if ($wdif[$j] > 0 and $wadi[$j] > 0)  // Verifico que no digiten cantidad en faltante y en adicional en el mismo articulo
	                 {
		              //echo "</tr><tr><td bgcolor=#66CC99><font size=6><b><CENTER>!!! E R R O R ¡¡¡</b></font></td></tr>";
				      echo "</tr><tr><td bgcolor=#66CC99><font size=4><b><CENTER>!!! No puede faltar y sobrar al mismo tiempo en el articulo : ".$wart[$j]." ¡¡¡</b></font></td></tr>";
				      $wgrabar="N";
	                 }
                 }
                else
                   {
	                //echo "</tr><tr><td bgcolor=#66CC99><font size=6><b><CENTER>!!! E R R O R ¡¡¡</b></font></td></tr>";
                    echo "</tr><tr><td bgcolor=#66CC99><font size=4><b><CENTER>!!! La diferencia debe ser numerica en el articulo : ".$wart[$j]." ¡¡¡</b></font></td></tr>";
                    $wgrabar="N";
	               }
	         }

	     if ($wgrabar == "N")
	        {
		     echo "</tr><tr><td bgcolor=#66CC66><font size=4><b><CENTER>Para retornar a corregir, pulse -CLICK- en el icono -ATRAS- o -BACK- en el browser</b></font></td></tr>";
		     echo "</tr><tr><td bgcolor=#66CC99><font size=6><b><CENTER>!!! E R R O R ¡¡¡</b></font></td></tr>";
	        }

	     if ($wgrabar == "S")
	        {
		     //Antes de grabar borro el documento en la tabla de detalle
			 $q="  DELETE FROM invetras_000002 "
			   ."   WHERE fuente    = '".$fuente."'"
			   ."     AND documento = '".$documento."'";
			 $res2 = mysql_query($q,$conex);

		     $totaldif=0;
		     for ($j=0;$j<=$i-1;$j++)
		        {
			     //Grabo el documento
			     $q="     insert into invetras_000002 (Medico    ,   Fecha_data,   Hora_data,   Fuente    ,   Documento    ,   Articulo    ,   Cantidad    ,   Diferencia  ,   Adicional   , Seguridad) ";
				 $q=$q."                       values ('invetras','".$fecha."' ,'".$hora."' ,'".$fuente."','".$documento."','".$wart[$j]."','".$wenv[$j]."','".$wdif[$j]."','".$wadi[$j]."','C-".$wusuario."')";
				 $res2 = mysql_query($q,$conex);

			     $totaldif = $totaldif + $wdif[$j];
	            }

	         if ($totaldif == 0)  //Si el total de las diferencias es diferente de cero es porque se recibio parcialmente
	            {
		         //Antes de grabar borro el documento
			     $q="  DELETE FROM invetras_000001 "
			       ."   WHERE fuente    = '".$fuente."'"
			       ."     AND documento = '".$documento."'";
			     $res2 = mysql_query($q,$conex);

			     //Grabo el documento
		         $q="     insert into invetras_000001 (Medico    ,   Fecha_data,   Hora_data,   Fuente    ,   Documento    , Ok , Seguridad) ";
				 $q=$q."                       values ('invetras','".$fecha."' ,'".$hora."' ,'".$fuente."','".$documento."','on', 'C-".$wusuario."')";
				 $res2 = mysql_query($q,$conex);

				 echo "<tr></tr>";
				 echo "<tr></tr>";
				 echo "<tr></tr>";
				 echo "<tr></tr>";
				 echo "<tr></tr>";
				 echo "<tr><td bgcolor=#66CC99 colspan=6><font size=5><CENTER>** TRASLADO RECIBIDO OK **</font></td></tr></table>";
			    }
			   else
			      {
				   //Antes de grabar borro el documento en la tabla de encabezado
			       $q="  DELETE FROM invetras_000001 "
			         ."   WHERE fuente    = '".$fuente."'"
			         ."     AND documento = '".$documento."'";
			       $res2 = mysql_query($q,$conex);

			       //Grabo el documento
			       $q= "     insert into invetras_000001 (Medico    ,   Fecha_data,   Hora_data,   Fuente    ,   Documento    , Ok  , Seguridad) ";
				   $q= $q."                       values ('invetras','".$fecha."' ,'".$hora."' ,'".$fuente."','".$documento."','off', 'C-".$wusuario."')";
				   $res2 = mysql_query($q,$conex);

				   echo "<tr></tr>";
				   echo "<tr></tr>";
				   echo "<tr></tr>";
				   echo "<tr></tr>";
				   echo "<tr></tr>";
				   echo "<tr><td bgcolor=#66CC99 colspan=6><font size=5><b><CENTER>** TRASLADO RECIBIDO PARCIALMENTE **</b></font></td></tr></table>";
			      }
	        }
	    }
		
		odbc_close($conexunix);
		odbc_close_all();
} // if de register

echo "<br>";
echo "<font size=3><A href=Tras_invent.php?wanoi=".$wanoi."&amp;wmesi=".$wmesi."&amp;wdiai=".$wdiai."&amp;wanof=".$wanof."&amp;wmesf=".$wmesf."&amp;wdiaf=".$wdiaf."&amp;wcco=".$wcco."> Retornar</A></font>";

include_once("free.php");
//odbc_close($conexunix);
?>
