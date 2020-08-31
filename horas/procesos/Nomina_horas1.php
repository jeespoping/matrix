<head>
  <title>REPORTE DE RECARGOS Y HORAS EXTRAS DEL PERSONAL</title>
</head>
<body BACKGROUND="/inetpub/wwwroot/matrix/images/medical/root/fondo de bebida de limón.gif">
<?php
include_once("conex.php");
  /***************************************************
   *	          IMPRIMIR LAS LECTURAS              *
   *	  REALIZADAS EN LA UNIDAD DE IMAGINOLOGIA	 *
   *				CONEX, FREE => OK				 *
   ***************************************************/
session_start();

if (!isset($user))
	{
		if(!isset($_SESSION['user']))
			session_register("user");

	}

if(!isset($_SESSION['user']))
	echo "error";
else
{
    //--- Se incluye este segmento de codigo para que funcione con la nueva funcion de conexion para unix. 8 mayo 2012
    

	include_once("root/comun.php");
	


	$key = substr($user,2,strlen($user));
	$conex = obtenerConexionBD("matrix");
	$basedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");

	conexionOdbc($conex, $basedato, &$conexunix, 'nomina');
	//--------------------------

    $conexunix = odbc_connect('nomina','informix','sco')
  					   or die("No se ralizo Conexion con el Unix");

 // if ($conexunix == FALSE)
 //    echo "Fallo la conexión UNIX";

	                                                           // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
  $wactualiz="(Versión Noviembre 16 de 2004)";                 // Aca se coloca la ultima fecha de actualizacion de este programa //
  $wano=date("Y");
  $wmes=date("m");                                                           // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //

  echo "<br>";
  echo "<br>";

  echo "<form action='Nomina_horas1.php' method=post>";
  echo "<center><table border=2 width=400>";
  echo "<input type='hidden' name='wemp_pmla' value='".$wemp_pmla."'>";
  echo "<input type='hidden' name='basedato' value='".$basedato."'>";
  echo "<tr><td align=center colspan=240 bgcolor=#fffffff><font size=6 text color=#CC0000><b>CLINICA LAS AMERICAS</b></font></td></tr>";
  echo "<tr><td align=center colspan=5 bgcolor=#fffffff><font size=4 text color=#CC0000><b>REPORTE DE RECARGOS Y HORAS EXTRAS DEL PERSONAL</b></font></td></tr>";
  echo "<tr><td align=center colspan=5 bgcolor=#fffffff><font size=3 text color=#CC0000><b>".$wactualiz."</b></font></td></tr>";


  if(!isset($wano) or !isset($wmes) or !isset($wqui))
    {
	 //AÑO
     echo "<td bgcolor=#cccccc ><font size=4><b>Año:</b></font><select name='wano'>";
     for($f=2004;$f<2051;$f++)
       {
        if($f == $wano)
          echo "<option selected>".$f."</option>";
         else
            echo "<option>".$f."</option>";
       }
	   echo "</select>";

	 //MES
     echo "<td bgcolor=#cccccc ><font size=4><b>Mes :</b></font><select name='wmes'>";
     for($f=1;$f<13;$f++)
       {
        if($f == $wmes)
          if($f < 10)
            echo "<option selected>0".$f."</option>";
           else
              echo "<option selected>".$f."</option>";
	     else
	        if($f < 10)
	          echo "<option>0".$f."</option>";
	         else
	            echo "<option>".$f."</option>";
	   }
	   echo "</select>";

     //QUINCENA
     echo "<td bgcolor=#cccccc ><font size=4><b>Quincena :</b></font><select name='wqui'>";
     for($f=1;$f<3;$f++)
       {
        echo "<option>".$f."</option>";
       }
	   echo "</td></select></td></tr>";

     echo"<tr><td align=center bgcolor=#cccccc colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";
    }
   else
      /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      // Ya estan todos los campos setiados o iniciados ===================================================================================
      /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      {
	   //AÑO
       echo "<tr><td align=center bgcolor=#66CC99>Año: ".$wano."</td>";
       echo "<input type='HIDDEN' name= 'wano' value='".$wano."'>";

	   //MES
       echo "<td align=center bgcolor=#66CC99>Mes: ".$wmes."</td>";
       echo "<input type='HIDDEN' name= 'wmes' value='".$wmes."'>";

       //QUINCENA
       echo "<td align=center bgcolor=#66CC99>Quincena: ".$wqui."</td>";
       echo "<input type='HIDDEN' name= 'wqui' value='".$wqui."'>";

	   //EMPLEADO
	   echo "<tr><td bgcolor=#66CC99 colspan=3><font size=4><b>Empleado: </b></font><SELECT name='wempleado'>";

	 ///  //Averiguo si la quincena en Nomina esta abierta en UNIX
	 ///  $q= "       SELECT count(*) AS can ";
	 ///  $q= $q."      FROM nocse ";
	 ///  $q= $q."     WHERE cseano = '".$wano."'";
	 ///  $q= $q."       AND csemes = '".$wmes."'";
	 ///  $q= $q."       AND csenpm = '".$wqui."'";
	 ///  $q= $q."       AND csefec is null ";       //si es nulo es porque esta abierta

	 ///  $res = odbc_do($conexunix,$q);

     ///  if (odbc_result($res,1) >= 1)
     ///     { // If quincena abierta en UNIX
           // Averiguo si la quincena en MATRIX esta abierta
           $q= "    SELECT count(*) AS can ";
           $q= $q."   FROM rephor_000002 ";
           $q= $q."  WHERE ano      = '".$wano."'";
           $q= $q."    AND mes      = '".$wmes."'";
           $q= $q."    AND quincena = '".$wqui."'";
           $q= $q."    AND cerrado  = 'off' ";
           $res = mysql_query($q,$conex);
           $row = mysql_fetch_array($res);

           if ($row[0] >= 1 )
              {   // if quincena abierta MATRIX

               $pos = strpos($user,"-");
		       $wusuario = substr($user,$pos+1,strlen($user));

			   //Aca selecciono los empleados que pertenecen al centro de costo de acuerdo al centro de costo que tiene asignado el usuario
			   //autorizado para ingresar a este proceso.
			   $q = "         SELECT Carne_nomina ";
			   $q = $q."        FROM rephor_000001 ";
			   $q = $q."       WHERE Usuario_matrix = '".$wusuario."'";

			   $res = mysql_query($q,$conex);
               $row = mysql_fetch_array($res);

               if ($row[0] <> "" )   //Si es diferente de null, es porque el usuario esta autorizado a ingresar al proceso
	              {	//If de usuario autorizado a entrar
	               //Traigo el centro de costo del usuario autorizado, con el carne busco en Nomina (Unix)
	               //autorizado para ingresar a este proceso.
	               $q= "       SELECT percco ";
	               $q= $q."      FROM noper ";
	               $q= $q."     WHERE percod = '".$row[0]."'";
	               $q= $q."       AND peretr = 'A' ";       //si esta activo
                   $res = odbc_do($conexunix,$q);

                   if (odbc_result($res,1) <> "")
                      $wcco = odbc_result($res,1);

	               //Traigo los nombres de los empleados del centro costo antes seleccionado
	               $query = "        SELECT percod, perno1, perno2, perap1, perap2 ";
                   $query = $query."   FROM noper ";
                   $query = $query."  WHERE peretr = 'A' ";
                   $query = $query."    AND percco = '".$wcco."'";
                   $query = $query."  ORDER BY perno1, perno2,perap1,perap2 ";

    	           $res = odbc_do($conexunix,$query);

	               while(odbc_fetch_row($res))
	                    {
		                 if (isset($wempleado))
		                    {
			                 $pos = strpos($wempleado,"-");
		                     $wcodemp = substr($wempleado,0,$pos);

                             $wemple= odbc_result($res,1);
		                     if (trim($wcodemp) == trim($wemple))
	                            echo "<option selected>".odbc_result($res,1)."-".odbc_result($res,2)."-".odbc_result($res,3)."-".odbc_result($res,4)."-".odbc_result($res,5)."</option>";
	                           else
	                              echo "<option value>".odbc_result($res,1)."-".odbc_result($res,2)."-".odbc_result($res,3)."-".odbc_result($res,4)."-".odbc_result($res,5)."</option>";
	                        }
	                       else
	                          echo "<option value>".odbc_result($res,1)."-".odbc_result($res,2)."-".odbc_result($res,3)."-".odbc_result($res,4)."-".odbc_result($res,5)."</option>";
	                    }
	               echo "</SELECT></td></tr></table><br><br>";
	               ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	               //DAYNAME(date)

	               if ($wqui == "1")
	                  if ($wmes == '01')          //Si es Enero entonces el año cambia y el mes anterior es 12
	                     {
	                      $wanofec = ($wano-1);
	                      $wmesfec = 12;
                         }
                        else                     //Si no es Enero, siempre me devuelvo un mes si es la primera quincena
                           {
	                        $wanofec = $wano;
	                        $wmesfec = $wmes-1;
                           }
                     else                        //Para la segunda quincena nunca cambio ni el año ni el mes
                        {
	                     $wanofec = $wano;
	                     $wmesfec = $wmes;
                        }


                   //Aca averiguo el ultimo dia del mes anterior al período digitado
		           $fecha = date("1-".($wmesfec)."-".$wanofec,"%d-%m-%Y");
		           $q="SELECT dayofmonth(last_day(str_to_date('".$fecha."','%d-%m-%Y')))";
		           $err = mysql_query($q,$conex);
		           $row = mysql_fetch_array($err);
		           $wultdia = $row[0];
		           //echo " Ultimo dia del mes ".$wultdia;

		           //Para la primera quincena
		           if ($wqui == "2")
		             {
		              $wultdia = 15;
		              $k=1;
	                 }
	                else         //Para la segunda quincena se inicia k=16 y wultdia se deja como venia
	                   $k=16;

	               echo "<center><table border=1>";
		           //Aca coloco los numeros de los dias en la fila de la tabla
		           echo "<tr>";
		           echo "<td bgcolor=#66CC99>Código</td>";
		           echo "<td bgcolor=#66CC99>Descripción</td>";
		           for ($j=$k;$j<=$wultdia;$j++)
		               {
			            ////////////////////////////////////////////////////////////////////////////////
			            $fecha = date($j."-".$wmesfec."-".$wanofec,"%d-%m-%Y");
			            $q="SELECT DAYNAME(str_to_date('".$fecha."','%d-%m-%Y'))";
			            $err = mysql_query($q,$conex);
			            $row = mysql_fetch_array($err);

			            if ($row[0] == "Sunday")        //Averiguo si el dia es dominical
			               $color = "#CC3333";
			              else
			                 {
				              //Averiguo si el dia es fectivo
				              $q="   SELECT count(*) "
				                 ."    FROM rephor_000004 "
				                 ."   WHERE Ano = '".$wanofec."'"
				                 ."     AND Mes = '".$wmesfec."'"
				                 ."     AND Dia = '".$j."'";
				              $err = mysql_query($q,$conex);
			                  $row = mysql_fetch_array($err);

			                  if ($row[0] > 0)          //Si es dia festivo
			                     $color = "#CC3333";
				                else
				                   $color = "#66CC99";  //No es dia festivo ni dominical
			                 }

			            //echo $j." - ".$row[0];
			            ////////////////////////////////////////////////////////////////////////////////

		   		        echo "<td align=center bgcolor=".$color.">$j</td>";
	   	               }
	   	           echo "<td bgcolor=#66CC99>Total</td>";
		           echo "</tr>";

		           //Aca traigo todos los conceptos que existen
		           $q=" SELECT subcodigo, descripcion "
		            ."    FROM det_selecciones "
		            ."   WHERE lcase(medico) = 'rephor' "   //Nombre del usuario dueño de la seleccion
		            ."     AND codigo = '001' "             //Codigo de la seleccion en la tabla det_selecciones
		            ."   ORDER BY subcodigo ";

		           $res = mysql_query($q,$conex);
		           $num = mysql_num_rows($res);

		           if ($num > 0)
		              {
			           //Aca comienzo a crear la cuadricula de captura de horas
			           $sw=0;
			           for ($t=0;$t<$num;$t++)
			               {
			                $row = mysql_fetch_array($res);
			                $cod = $row[0];    //Codigo
			                $des = $row[1];    //Descripcion

			                //Esto lo hago para intercambiar colores entre líneas
			                if ($sw==0)
			                   {
			                    $color="#fffffff";
			                    $sw=1;
		                       }
			                  else
			                     {
			                      $color="#66CC99";
			                      $sw=0;
		                         }
		                    echo "<tr>";

		                    //===================================================================================================================================
		                    //Aca comienzo la evaluacion del radio buton, con el cual se determina la accion a seguir
		                    //===================================================================================================================================
		                    if (isset($radio1))
		                       {
		                        switch ($radio1)
		                           {
			                           //========================================================================================================================
		                               //ACTUALIZAR  ============================================================================================================
		                               case 2 :
		                                  {
			                                $totcod=0;
							                echo "<td bgcolor=".$color.">".$cod."</td>";
							                echo "<td bgcolor=".$color.">".$des."</td>";
							                //Aca se crean los cuadros de captura de datos hasta el final de la quincena
						                    for ($j=$k;$j<=$wultdia;$j++)
						                        {
							                     $nomcasilla = $cod."-".$j;
							                     if (isset($wempleado))  //Si selecciono ya el empleado
							                        {
							                         $q= "     SELECT Cantidad ";
							                         $q= $q."    FROM rephor_000003 ";
							                         $q= $q."   WHERE ano           = '".$wano."'";
							                         $q= $q."     AND mes           = '".$wmes."'";
				    			                     $q= $q."     AND quincena      = '".$wqui."'";
					     		                     $q= $q."     AND empleado      = '".$wcodemp."'";
						     	                     $q= $q."     AND Tipo_hora_dia = '".$nomcasilla."'";
							                         $res1 = mysql_query($q,$conex);
						                             $num1 = mysql_num_rows($res1);

						                             if ($num1 > 0)
						                               {
							                            $row1 = mysql_fetch_array($res1);

							                            //Borro lo que tenia grabdo el empleado en esta quincena
			                                            $q= "     DELETE FROM rephor_000003 ";
						   		                        $q= $q."   WHERE ano           = '".$wano."'";
						   		                        $q= $q."     AND mes           = '".$wmes."'";
						   		                        $q= $q."     AND quincena      = '".$wqui."'";
						   		                        $q= $q."     AND empleado      = '".$wcodemp."'";
						   		                        $q= $q."     AND Tipo_hora_dia = '".$nomcasilla."'";
						   		                        $res2 = mysql_query($q,$conex);
							                           }
						                            } //fin del then si esta seleccionado wempleado

						                         if (isset($$nomcasilla))
						                            {
							                          //Si el valor es cero, no lo muestre en la grilla
					     		                      if ($$nomcasilla > 0)
						     		                     echo "<td bgcolor=".$color."><INPUT TYPE='text' NAME='".$nomcasilla."' VALUE = ".$$nomcasilla." SIZE = 1 maxlength=4></td>";
						     		                    else
						   	    	                       echo "<td bgcolor=".$color."><INPUT TYPE='text' NAME='".$nomcasilla."' SIZE = 1 maxlength=4></td>";

						   		                      if (isset($nomcasilla) and ($$nomcasilla > 0))
				                                         {
					                                      $totcod=$totcod+$$nomcasilla;
						   		                          //echo "Celda # : ".$nomcasilla." Dato: ".$$nomcasilla;    ////////======000000======000000======000000
						   		                          //Aca inserto la informacion digitada en la cuadricula, pero primero borro todo lo que tenga el empleado
						   		                          //grabado en esta misma quincena.

							                              $fecha = date("Y-m-d");
									                      $hora = (string)date("H:i:s");
							                                                                                                                                                //number_format($numero, 2, '.', '');
									                      $q= "     insert into rephor_000003 (Medico      ,   Fecha_data,   Hora_data,   ano     ,   mes,        quincena,   cco     ,   empleado   ,   Tipo_hora_dia ,  cantidad                               , Seguridad) ";
							                              $q= $q."                  values ('rephor','".$fecha."' ,'".$hora."' ,'".$wano."','".$wmes."','".$wqui."','".$wcco."','".$wcodemp."','".$nomcasilla."',".number_format($$nomcasilla,1,'.', '').", 'C-".$wusuario."')";
							                              $res2 = mysql_query($q,$conex);
							                             }
				   		                            }
				   		                           else
				   		                              echo "<td bgcolor=".$color."><INPUT TYPE='text' NAME='".$nomcasilla."' SIZE = 1 maxlength=4></td>";
					   		                    }
					   	                    echo "<td bgcolor=#66CC99>$totcod</td>";
						                    echo "</tr>";

						                    break;
					                      }  //Fin del case 2 //Actualizar
					                   //========================================================================================================================
					                   //CONSULTAR ==============================================================================================================
					                   case 3 :
					                      {
						                    $totcod=0;
							                echo "<td bgcolor=".$color.">".$cod."</td>";
							                echo "<td bgcolor=".$color.">".$des."</td>";
							                //Aca se crean los cuadros de captura de datos hasta final de la quincena
						                    for ($j=$k;$j<=$wultdia;$j++)
						                        {
							                     $nomcasilla = $cod."-".$j;
							                     $$nomcasilla = 0;
							                     if (isset($wempleado))  //Si selecciono ya el empleado
							                        {
							                         $q= "     SELECT Cantidad ";
							                         $q= $q."    FROM rephor_000003 ";
							                         $q= $q."   WHERE ano           = '".$wano."'";
							                         $q= $q."     AND mes           = '".$wmes."'";
				    			                     $q= $q."     AND quincena      = '".$wqui."'";
					     		                     $q= $q."     AND empleado      = '".$wcodemp."'";
						     	                     $q= $q."     AND Tipo_hora_dia = '".$nomcasilla."'";
							                         $res1 = mysql_query($q,$conex);
						                             $num1 = mysql_num_rows($res1);

						                             if ($num1 > 0)
						                               {
							                            $row1 = mysql_fetch_array($res1);
							                            $$nomcasilla=$row1[0];
							                           }
						                            } //fin del then si esta setiado wempleado

						                         if (isset($$nomcasilla))
						                            {
				     		                          //Si el valor es cero, no lo muestre en la grilla
					     		                      if ($$nomcasilla > 0)
						     		                     echo "<td bgcolor=".$color."><INPUT TYPE='text' NAME='".$nomcasilla."' VALUE = ".$$nomcasilla." SIZE = 1 maxlength=4></td>";
						     		                    else
						   	    	                       echo "<td bgcolor=".$color."><INPUT TYPE='text' NAME='".$nomcasilla."' SIZE = 1 maxlength=4></td>";

						   		                      if (isset($nomcasilla) and ($$nomcasilla > 0))
				                                         {
						   		                          $totcod=$totcod+$$nomcasilla;
						   		                         }
				   		                            }
				   		                           else
				   		                              echo "<td bgcolor=".$color."><INPUT TYPE='text' NAME='".$nomcasilla."' SIZE = 1 maxlength=4></td>";
					   		                    }
					   	                    echo "<td bgcolor=#66CC99>$totcod</td>";
						                    echo "</tr>";

						                    break;
					                      } // Fin del case 3 // Consultar
					                   //========================================================================================================================
					                   //BORRAR =================================================================================================================
					                   case 4 :
					                       {
						                    $q= "     DELETE FROM rephor_000003 ";
						   		            $q= $q."   WHERE ano      = '".$wano."'";
						   		            $q= $q."     AND mes      = '".$wmes."'";
						   		            $q= $q."     AND quincena = '".$wqui."'";
						   		            $q= $q."     AND empleado = '".$wcodemp."'";
						   		            $res2 = mysql_query($q,$conex);

						   		            break;
						   		            //echo "</table><br><br><br><TABLE><TR><TD><b><font size=3>SE ELIMINO EL REGISTRO</font></b></TD></TR></TABLE>";
					   		               } // Fin case 4 // Borrar
		                           } //Fin del switch Radio1
                             } //fin then del if isset(radio1)
		                   } //fin del for
	                   }    // fin del then de $num > 0



	               ////////////////////////////////////////////////////
	               if (isset($radio1))
		             {
	                  switch ($radio1)
	                     {
	                      case 2 :
	                       {
		                    echo "<tr><td bgcolor=#339933 colspan=".($wultdia+3)."><CENTER>** REGISTRO ACTUALIZADO **</td></tr>";
		                    break;
	                       }
	                      case 3 :
	                       {
		                    echo "<tr><td colspan=".($wultdia+3)."><CENTER></td></tr>";
		                    break;
	                       }
	                      case 4 :
	                       {
		                    echo "<tr><td bgcolor=#339966 colspan=".($wultdia+3)."><CENTER>** REGISTRO ELIMINADO **</tr>";
		                    break;
	                       }
                         }
                     }
                   ////////////////////////////////////////////////////
                   echo "<tr><td colspan=".($wultdia+3)."><CENTER>";

                   echo "<B>";
                   echo "<INPUT CENTER TYPE = 'Radio' NAME = 'radio1' VALUE = 2 > Actualizar";
                   echo "<INPUT CENTER TYPE = 'Radio' NAME = 'radio1' VALUE = 3 CHECKED> Consultar";
                   echo "<INPUT CENTER TYPE = 'Radio' NAME = 'radio1' VALUE = 4 > Borrar";
                   echo "</B></td></tr>";

                   echo "<font size=3><A href=/matrix/HORAS/reportes/000003_rh02.php"."> Imprimir</A></font>";

		           echo"<tr><td align=center bgcolor=#cccccc colspan=".($wultdia+3)."><input type='submit' value='ENVIAR'></td></tr></form>";
	              }  //Fin del then del if del usuario autorizado a entrar
		        else // else del usuario autorizado
		           echo "</table><br><br><br><TABLE><TR><TD><b><font size=5>EL USUARIO NO ESTA AUTORIZADO PARA INGRESAR A ESTE PROCESO</font></b></TD></TR></TABLE>";
	          }  // Fin del then quincena abierta en MATRIX
	         else
                echo "</table><br><br><br><TABLE><TR><TD><b><font size=5>LA QUINCENA DIGITADA YA ESTA CERRADA EN MATRIX o NO EXISTE</font></b></TD></TR></TABLE>";
	   ///   }     // Fin del then quincena abierta en UNIX
	   ///  else  // else del if quincena abierta en UNIX
	   ///     echo "</table><br><br><br><TABLE><TR><TD><b><font size=5>LA QUINCENA DIGITADA YA ESTA CERRADA EN NOMINA o NO EXISTE</font></b></TD></TR></TABLE>";
      } // else de todos los campos setiados
	  
	odbc_close($conexunix);
	odbc_close_all();
	  
} // if de register

unset($wano);
unset($wmes);
unset($wqui);
unset($wempleado);
//odbc_close($conexunix);
echo "<br>";
echo "<font size=3><A href=Nomina_horas1.php"."> Retornar</A></font>";

include_once("free.php");
//odbc_close($conexunix);
?>
