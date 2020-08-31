<head>
  <title>DEVOLUCION POR ALTA</title>
</head>
<body onload=ira() BACKGROUND="nubes.gif">
<?php
include_once("conex.php");
  /*********************************************************
   * DEVOLUCION DE INSUMOS DE LAS CUENTAS DE LOS PACIENTES *
   *         ESTA DEVOLUCION SE GRABA EN LOS PISOS         *
   *     				CONEX, FREE => OK				   *
   *********************************************************/
session_start();

if (!isset($user))
	if(!isset($_SESSION['user']))
	  session_register("user");


if(!isset($_SESSION['user']))
	echo "error";
else
{

  

						or die("No se ralizo Conexion");
  


  $conexunix = odbc_connect('facturacion','facadm','1201')
  					    or die("No se ralizo Conexion con el Unix");

 // if ($conexunix == FALSE)
 //    echo "Fallo la conexión UNIX";


	                                                           // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
  $wactualiz="(Versión Febrero 18 de 2005)";                   // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                                           // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //

  echo "<br>";
  echo "<br>";

  echo "<form name='Grabar_devolucion_Piso' action='Grabar_devolucion_Piso.php' method=post>";
  echo "<center><table border=1 BACKGROUND=.'nubes.gif'>";
  echo "<tr><td align=center colspan=4 bgcolor=#fffffff><font size=5 text color=#CC0000><b>CLINICA LAS AMERICAS</b></font></td></tr>";
  echo "<tr><td align=center colspan=4 bgcolor=#fffffff><font size=3 text color=#CC0000><b>DEVOLUCION POR ALTA</b></font></td></tr>";
  echo "<tr><td align=center colspan=4 bgcolor=#fffffff><font size=2 text color=#CC0000><b>".$wactualiz."</b></font></td></tr>";

  echo $wini;

  if ($wini=="S")
     $k=1;
  //  else
  //     $k=$k+1;

  if (strpos($user,"-") > 0)
     $wuser = substr($user,(strpos($user,"-")+1),strlen($user));

  //Traigo el centro costo a partir del usuario que ingreso
  $query = " SELECT percco "
          ."   FROM noper "
        //  ."  WHERE percod = '".$wuser."'"
          ."  WHERE percod = '00069'"
          ."    and peretr = 'A' ";
  $res = odbc_do($conexunix,$query);
  while(odbc_fetch_row($res))
       $wcco = odbc_result($res,1);


  //ACA TRAIGO EL NOMBRE DEL SERVICIO AL QUE ESTA ASOCIADO EL EMPLEADO O CENTRO DE COSTO
  $query = " SELECT sernom "
          ."   FROM insercco, inser "
          ."  WHERE serccocco = '".$wcco."'"
          ."    AND serccoser = sercod "
          ."    AND seract = 'S' ";

  $res = odbc_do($conexunix,$query);
  if (odbc_fetch_row($res))
     echo "<tr><td align=center colspan=4 bgcolor=#fffffff><font size=3 text color=#CC0000><b>SERVICIO: ".odbc_result($res,1)."</b></font></td></tr>";

  if (!isset($wcodigo))
     {
	  ?>
		<script>
		  function ira(){document.Grabar_devolucion_Piso.wcodigo.focus();}
		</script>
      <?php
      echo "<tr><td align=center bgcolor=#99FFCC><b>Ingrese su código de usuario :</b><INPUT TYPE='text' NAME='wcodigo' SIZE=10></td></tr>";

      echo "<input type='HIDDEN' name= 'wini' value='".$wini."'>";
	  echo "<input type='HIDDEN' name= 'k' value='".$k."'>";
     }
    else
       {
	      //ACA VERIFICO QUE EL CODIGO DIGITADO SI CORRESPONDA A UN USUARIO DE MATRIX
		  $q =  " SELECT descripcion "
		       ."   FROM usuarios "
		       ."  WHERE codigo = '".$wcodigo."'"
		       ."    AND activo = 'A' ";

		  $res = mysql_query($q,$conex);
		  $num = mysql_num_rows($res);
		  if ($num > 0)
	         {
		      $row = mysql_fetch_array($res);
		      $wresponsable=$row[0];
		      /////////////////////////////////////////////
		      echo "<tr><td align=center colspan=4 bgcolor=#fffffff><font size=1><b>RESPONSABLE: ".$row[0]."</b></font></td></tr>";

		      if (!isset($whistoria) or ($whistoria == ""))
		         {
			      //ACA TRAIGO LOS PACIENTES QUE SE ENCUENTRAN EN EL SERVICIO
				  $query = " SELECT trahab, trahis, tranum, pacnom, pacap1, pacap2 "
				          ."   FROM inmtra, insercco, inpac "
				          ."  WHERE serccocco = '".$wcco."'"
				          ."    AND traser    = serccoser "
				          ."    AND trahis    = pachis "
				          ."    AND tranum    = pacnum "
				          ."    AND traegr    is null "
				          ."  GROUP BY 1,2,3,4,5,6 "
				          ."  ORDER BY 1 ";

				  echo "<tr>";
				  echo "<th bgcolor=#fffffff>Habitacion</th>";
				  echo "<th bgcolor=#fffffff>Historia</th>";
				  echo "<th bgcolor=#fffffff>Ingreso</th>";
				  echo "<th bgcolor=#fffffff>Paciente</th>";
				  echo "</tr>";

				  $res = odbc_do($conexunix,$query);

				  $whabant = "";
				  while(odbc_fetch_row($res))
					  {
					   $whab = odbc_result($res,1);
					   $whis = odbc_result($res,2);
					   $wing = odbc_result($res,3);
					   $wpac = odbc_result($res,4)." ".odbc_result($res,5)." ".odbc_result($res,6);

					   if ($whabant != $whab)
					     {
					      echo "<tr>";
					      echo "<td align=center bgcolor=#99FFCC><font size=1><b>".$whab."</b></font></td>";
					      echo "<td align=center bgcolor=#99FFCC><font size=1><b>".$whis."</b></font></td>";
					      echo "<td align=center bgcolor=#99FFCC><font size=1><b>".$wing."</b></font></td>";
					      echo "<td align=left bgcolor=#99FFCC><font size=1><b>".$wpac."</b></font></td>";
					      echo "</tr>";

					      $whabant = $whab;
					     }
					  }
				  //echo "</table>";

				  ?>
				    <script>
				        function ira(){document.Grabar_devolucion_Piso.whistoria.focus();}
				    </script>
		          <?php
		          echo "<tr>";
			      echo "<td align=center bgcolor=#99FFCC><b>Historia :</b><INPUT TYPE='text' NAME='whistoria' SIZE=10></td>";
			      echo "</tr>";

			      echo "<input type='HIDDEN' name= 'wcodigo' value='".$wcodigo."'>";
			      echo "<input type='HIDDEN' name= 'wini' value='".$wini."'>";
			      echo "<input type='HIDDEN' name= 'k' value='".$k."'>";
		         }
			    else
			       {
				    //////////////////////////////////////////////////////////////////////////////////////////////////////////
				    //ACA COMIENZA EL DETALLE DE LOS ARTICULOS
				    //////////////////////////////////////////////////////////////////////////////////////////////////////////

				    echo $k;

				    for ($i=1;$i<=$k;$i++)
				        {
					     echo "I : ".$i;

					     //if (!isset($wcodart[$i]) or !isset($wcantidad[$i]) or $wcantidad[$i] == "")
					     //   {
						     echo "<tr>";
						     if (!isset($wcodart[$i]))
						        {
						         ?>
					               <script>
					                   function ira(){document.Grabar_devolucion_Piso.wcodart.focus();}
					               </script>
			                     <?php
							     echo "<td align=center bgcolor=#99FFCC><b>Articulo :</b><INPUT TYPE='text' NAME='wcodart[".$i."]' SIZE=10></td>";
							     echo "<td align=center bgcolor=#cccccc><input type='submit' value='OK'></td>";
					            }
						       else
						          {
						           echo "<td align=center bgcolor=#99FFCC><b>Articulo :</b><INPUT TYPE='text' NAME='wcodart[".$i."]' VALUE='".$wcodart[$i]."'></td>";
							       //Traigo la descripcion del articulo
							 	   $query = " SELECT artnom "
							 	           ."   FROM ivart "
								           ."  WHERE artcod = '".strtoupper($wcodart[$i])."'";

								   $res = odbc_do($conexunix,$query);
								   while(odbc_fetch_row($res))
								        $wartnom = odbc_result($res,1);
								   echo "<td align=center bgcolor=#99FFCC>".$wartnom."</td>";

								   if (!isset($wcantidad[$i]))
								      {
									   ?>
							             <script>
							                 function ira(){document.Grabar_devolucion_Piso.wcantidad.focus();}
							             </script>
					                   <?php
								       echo "<td align=center bgcolor=#99FFCC><b>Cantidad :</b><INPUT TYPE='text' NAME='wcantidad[".$i."]' SIZE=10></td>";
								       $wini="N";
								       $k=$k+1;
								       echo "<td align=center bgcolor=#cccccc><input type='submit' value='OK'></td>";
							          }
								     else
								        {
								         echo "<td align=center bgcolor=#99FFCC><b>Cantidad :</b><INPUT TYPE='text' NAME='wcantidad[".$i."]' VALUE='".$wcantidad[$i]."'></td>";
								         $wini="N";
								        }
							      }

						     echo "</tr>";
						     echo "<input type='HIDDEN' name= 'whistoria' value='".$whistoria."'>";
						     echo "<input type='HIDDEN' name= 'wcodigo' value='".$wcodigo."'>";
						     if (isset($wcodart[$i])) echo "<input type='HIDDEN' name= 'wcodart[".$i."]' value='".$wcodart[$i]."'>";
						     if (isset($wcantidad[$i])) echo "<input type='HIDDEN' name= 'wcantidad[".$i."]' value='".$wcantidad[$i]."'>";
						 //   }
			            }
			         //echo "<td align=center bgcolor=#cccccc><input type='submit' value='OK'></td>";
		           }
		     }
		    else
		       echo "<tr><td align=center colspan=4 bgcolor=#fffffff><font size=3 text color=#CC0000><b>EL CODIGO NO ES VALIDO</b></font></td></tr>";
	   }
	   
		odbc_close($conexunix);
		odbc_close_all();
} // if de register

echo "<input type='HIDDEN' name= 'wini' value='".$wini."'>";
echo "<input type='HIDDEN' name= 'k' value='".$k."'>";

echo "<br>";
echo "<font size=3><A href=Grabar_devolucion_Piso.php"."> Retornar</A></font>";

unset($wano);
unset($wmes);
unset($wdia);
unset($wcco);

include_once("free.php");

?>
