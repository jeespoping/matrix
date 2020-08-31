<head>
  <title>COMPROBANTE DE CARTERA DETALLADO</title>
</head>
<body onload=ira()>
<script type="text/javascript">
	function enter()
	{
	   document.forms.comprobante_fac_det_farpmla.submit();
	}

	function enter1()
	{
	   document.forms.comprobante_fac_det_farpmla.submit();
	   alert ("Pulse de nuevo la tecla ENTER");
	}

	function cerrarVentana()
	 {
      window.close()
     }

</script>

<?php
include_once("conex.php");
  /************************************************
   * PROGRAMA PARA comprobantes_cartera_detallado *
   ************************************************/

//==================================================================================================================================
//PROGRAMA                   : Comprobantes_Cartera_Detallado.php
//AUTOR                      : Juan Carlos Hernández M.
  $wautor="Juan C. Hernandez M.";
//FECHA CREACION             : Noviembre 20 de 2006
//FECHA ULTIMA ACTUALIZACION :
  $wactualiz="Diciembre 24 de 2013";
//DESCRIPCION
//====================================================================================================================================\\
//Objetivo:                                                                                                                           \\
//====================================================================================================================================\\


//========================================================================================================================================\\
//========================================================================================================================================\\
//ACTUALIZACIONES                                                                                                                         \\
//========================================================================================================================================\\
//========================================================================================================================================\\
//Junio 14 DE 2012:                                                                                                                       \\
//________________________________________________________________________________________________________________________________________\\
// Camilo Zapata: se agrega el filtro de fechas en todas las consultas que involucran a la tabla 18, para que no sume las facturas que por error\\
//				  tengan el mismo número de factura.																							\\
//				  Tambien se modificaron los querys que involucran tanto la 18 con la 65 comparando en ambos las fecha datas para evitar sumas \\
//				  invalidas																													    \\
//________________________________________________________________________________________________________________________________________\\                                                                                                                                       \\
//	-->	Fecha del cambio: 2013-12-24	Autor: Jerson trujillo.
//		El maestro de conceptos que actualmente es la tabla 000004, para cuando entre en funcionamiento la nueva facturacion del proyecto
//		del ERP, esta tabla cambiara por la 000200; Entonces se adapta el programa para que depediendo del paramatro de root_000051
//		'NuevaFacturacionActiva' realice este cambio automaticamente.
//_______________________________________________________________________________________________________________________________________\\
//X X X X X X X X X  ## DE 2009:                                                                                                          \\
//________________________________________________________________________________________________________________________________________\\
//xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx     \\
//xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx     \\
//xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx     \\
//xxxxxxxxxxxxxxxxxxx.                                                                                                                    \\
//                                                                                                                                        \\
//                                                                                                                                        \\
//========================================================================================================================================\\
//========================================================================================================================================\\

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
  

  

  include_once("root/comun.php");


	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

	$wbasedato = $institucion->baseDeDatos;
	//---------------------------------------------------------------------------------------------
	// --> 	Consultar si esta en funcionamiento la nueva facturacion
	//		Fecha cambio: 2013-12-24	Autor: Jerson trujillo.
	//---------------------------------------------------------------------------------------------
	$nuevaFacturacion = consultarAliasPorAplicacion($conex, $wemp_pmla, 'NuevaFacturacionActiva');
	//---------------------------------------------------------------------------------------------
	// --> 	MAESTRO DE CONCEPTOS:
	//		- Antigua facturacion 	--> 000004
	//		- Nueva facturacion 	--> 000200
	//		Para la nueva facturacion cuando esta entre en funcionamiento el maestro
	//		de conceptos cambiara por la tabla 000200.
	//		Fecha cambio: 2013-12-24	Autor: Jerson trujillo.
	//----------------------------------------------------------------------------------------------
	$tablaConceptos = $wbasedato.(($nuevaFacturacion == 'on') ? '_000200' : '_000004');
	//----------------------------------------------------------------------------------------------

  $pos = strpos($user,"-");
  $wusuario = substr($user,$pos+1,strlen($user));

  $wfecha=date("Y-m-d");
  $hora = (string)date("H:i:s");

  echo "<form name='comprobante_fac_det_farpmla' action='Comprobante_fac_det_farpmla.php' method=post>";

  echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
  echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";


  //===========================================================================================================================================
  function ver_comprobante_detallado($wfnum, $wfres, $wtip)
    {

	 global $wbasedato;
	 global $conex;
	 global $wtotcomD;
	 global $wtotcomC;
	 global $wtotdocD;
	 global $wtotdocC;


	 $row  = mysql_fetch_array($wfres);

	 //====================================================================================================================
	 // OJO ESTE PROCESO AYUDA A BUSCAR DESCUADRES -- LEERLO
	 //====================================================================================================================
	 //Este echo muestra cada una de las facturas procesadas, esto sirve para buscar descuadres cuando en este comprobante
	 //sale mas dinero que el comprobante resumido.
	 /*echo $row[1]." | ".$row[6]."<br>";*/
	 //====================================================================================================================


	 $i=1;
	 while ($i<=$wfnum)
	    {
		 $wcta = $row[2];

		 $wtotctaD=0;   //Total cuenta Debitos
		 $wtotctaC=0;   //Total cuenta Créditos

		 while ($i<=$wfnum and $wcta == $row[2])
		      {
			   if ($row[6] != 0)  //Si el valor es diferente cero
			      {
				   //Con este procedimiento cambio el valor a absoluto y la naturaleza del valor de debito a credito o viceversa
				   //Porque cuando el valor es negativo quiere decir que es un abono y por lo tanto los valores de estos conceptos
				   //deben mostrarse con naturaleza contraria.
				   if ($row[6] < 0)
				      {
				       if (trim($row[3])=="D")
				          $row[3]="C";
				         else
					        if (trim($row[3])=="C")
					           $row[3]="D";

				       $row[6]=abs($row[6]);    //Paso el valor a absoluto
			          }

			       if ($i%2==0)
			          $wcolor="00FFFF";
			         else
			            $wcolor="";

			       $wnitter="";
			       $wnitnom="";

			       if ($row[5] != "" and $row[8]=="off")         //Si tiene tercero, lo busco en el maestro de terceros
				      {
					   $q= " SELECT mednom "
					      ."   FROM ".$wbasedato."_000051 "
					      ."  WHERE meddoc = '".$row[5]."'";
					   $rester = mysql_query($q,$conex);
	                   $rowter = mysql_fetch_array($rester);

	                   if (trim($rowter[0]) != "")  //Nombre del tercero
	                      {
		                   $wnitter=$row[5];
	                       $wnomter=$rowter[0];
                          }
	                     else  //Si no existe busco el tercero en el maestro de empresas
	                        {
		                     $q= " SELECT empnom "
							    ."   FROM ".$wbasedato."_000024 "
							    ."  WHERE empnit = '".$row[5]."'";
							 $rester = mysql_query($q,$conex);
			                 $rowter = mysql_fetch_array($rester);

			                 if (trim($rowter[0]) != "")
			                    {
				                 $wnitter=$row[5];
			                     $wnomter=$rowter[0];
		                        }
			                   else
			                      {
				                   $wnitter=$row[5];
	                               $wnomter="NO EXISTE";
                                  }
                            }
				      }
				     else
                        {
	                     if ($row[8] == "on")   //Indica si se imprime el nombre del responsable de la factura particular o no
				            {
					         $wnitter=$row[9];
					         $wnomter=$row[10];
					        }
				           else
				              {
					           $wnitter="";
					           $wnomter="";
					          }
				        }


				   if ($row[3]=="D")
			         {
				      echo "<tr class=fila2>";
				      echo "<td>".$row[0]."</td>";
				      echo "<td>".$row[1]."</td>";
					  echo "<td>".$row[2]."</td>";
					  echo "<td>&nbsp</td>";
					  echo "<td>".$row[4]."</td>";
					  //echo "<td>".$row[5]."</td>";
					  echo "<td>".$wnitter."</td>";
					  echo "<td>".$wnomter."</td>";
					  echo "<td align=right>".number_format($row[6],0,'.',',')."</td>";
					  echo "<td>&nbsp</td>";
					  echo "</tr>";
					  $wtotdocD=$wtotdocD+$row[6];
					  $wtotcomD=$wtotcomD+$row[6];
				     }
				    else
				       if ($row[3]=="C")
				         {
					      echo "<tr class=fila2>";
						  echo "<td>".$row[0]."</td>";
						  echo "<td>".$row[1]."</td>";
						  echo "<td>".$row[2]."</td>";
						  echo "<td>&nbsp</td>";
						  echo "<td>".$row[4]."</td>";
						  echo "<td>".$wnitter."</td>";
						  echo "<td>".$wnomter."</td>";
						  echo "<td>&nbsp</td>";
						  echo "<td align=right>".number_format($row[6],0,'.',',')."</td>";
						  echo "</tr>";
						  $wtotdocC=$wtotdocC+$row[6];
						  $wtotcomC=$wtotcomC+$row[6];
						 }
				  }
				 $row  = mysql_fetch_array($wfres);
				 $i++;
			 }
		}
	}


  //===========================================================================================================================================
  //INICIO DEL PROGRAMA
  //===========================================================================================================================================

  $wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
  //$wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
  //$wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
  $wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro

  //===========================================================================================================================================
  //ACA COMIENZA EL ENCABEZADO DE LA VENTA

  encabezado("COMPROBANTE DE FACTURACION Y NOTAS DETALLADO",$wactualiz,$wbasedato);

  if ((!isset($wfuecar) and !isset($wfec_i)) or (!isset($wfec_f))) // and !isset($wgra_mov))
     {
	  echo "<br>";
	  echo "<center><table>";

	  if (!isset($wfuecar)) $wfuecar="";

	  echo "<tr class=seccion1>";
      $q =  " SELECT carfue, cardes "
           ."   FROM ".$wbasedato."_000040, ".$wbasedato."_000078 "
	       ."  WHERE carest     = 'on' "
		   ."    AND carfue     = relfuecod "
		   ."    AND relfueest  = 'on' "
		   ."    AND (carndb    = 'on' "
		   ."     OR  carncr    = 'on' "
		   ."     OR  carfac    = 'on') "
		   ."  GROUP BY 1,2 "
		   ."  ORDER BY carfue ";

	  $res = mysql_query($q,$conex);
	  $num = mysql_num_rows($res);

	  echo "<td align=left colspan=2><b>Fuente cartera: </b><select name='wfuecar'>";

	  if (isset($wfuecar))
	     echo "<option selected>".$wfuecar."</option>";

	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res);
	      echo "<option>".$row[0]." - ".$row[1]."</option>";
	     }
	  echo "</select></td>";


	  echo "<tr class=seccion1>";
      echo "<td align=center><b>Fecha Inicial</b><br>";
      campoFecha("wfec_i");
      echo "</td>";
      echo "<td align=center><b>Fecha Final</b><br>";
	  campoFecha("wfec_f");
	  echo "</td>";
	  echo "</tr>";

	  echo "</table>";
	 }
    else
       {
	     echo "<br><br>";
	     echo "<center><table cellspacing=1 cellspading=1>";

	     echo "<tr class=seccion1>";
	     echo "<td align=center><b>Fuente de Cartera:</b>".$wfuecar."</td>";
	     echo "<tr>";
	      echo "<tr class=seccion1>";
	     echo "<td align=center colspan=3><b>Fecha Inicial:</font></b>".$wfec_i." <b>&nbsp&nbsp&nbspFecha Final:</b>".$wfec_f."</td>";
		 echo "</tr>";

		 echo "</table>";
		 echo "<br><br>";

		 $wfuecar1=$wfuecar;

	     $pos = strpos($wfuecar,"-");
	     $wfuecar = substr($wfuecar,0,$pos-1);

	     $q= " SELECT carncr, carndb "
		    ."   FROM ".$wbasedato."_000040 "
		    ."  WHERE  carfue = '".$wfuecar."'"
		    ."    AND  carest = 'on' "
		    ."    AND (carndb    = 'on' "
		    ."     OR  carncr    = 'on' "
		    ."     OR  carfac    = 'on') "
		    ."  GROUP BY 1,2 ";
		 $resfue = mysql_query($q,$conex);
	     $numfue = mysql_num_rows($resfue);
	     $rowfue = mysql_fetch_array($resfue);

	     //***************************************************************************************
	     //COMPROBANTE DE *** FACTURACION DETALLADO ***
	     //***************************************************************************************
	     //Si ambos son diferentes a 'on' indica que la fuente es de facturas
	     //Si alguno de los dos es off es porque corresponde a una fuente de notas
	     if ($numfue > 0 and $rowfue[0] != "on" and $rowfue[1] != "on")    //COMPROBANTE DE FACTURACION
	        {
		     //==============================================================================
		     //ESTE ES EL QUERY BASE PARA EL COMPROBANTE DE FACTURACION
		     //==============================================================================
		     $q= " CREATE TEMPORARY TABLE if not exists tempo1 as "
		        ." SELECT fenffa as fue, fenfac as doc, mid(fentip,1,instr(fentip,'-')-1) as tip, fencco as cco, fecha_data as fecha_data"
			    ."   FROM ".$wbasedato."_000018 "
			    ."  WHERE fenffa = '".$wfuecar."'"
			    ."    AND fenfec between '".$wfec_i."' AND '".$wfec_f."'"
			    //."    AND fenfac = 'A-2948' "
			    ."    AND fenest = 'on' "
			    ."  GROUP BY 1,2 ";
			 $res = mysql_query($q,$conex);
			}
		   else
             {
	          //***************************************************************************************
              //COMPROBANTE DE *** NOTAS DE CARTERA ***
              //***************************************************************************************
		      //Si ambos son diferentes a 'off' indica que la fuente es de notas
		      //Si alguno de los dos es off es porque corresponde a una fuente de notas
		      if ($numfue > 0 and $rowfue[0] == "on" or $rowfue[1] == "on")    //COMPROBANTE DE NOTAS DE CARTERA
		         {
			      //==============================================================================
			      //ESTE ES EL QUERY BASE PARA EL COMPROBANTE DE NOTAS DE CARTERA
			      //==============================================================================
			      $q= " CREATE TEMPORARY TABLE if not exists tempo1 as "
			         ." SELECT renfue as fue, rennum as doc, mid(emptem,1,instr(emptem,'-')-1) as tip, rencco cco, b.fecha_data as fecha_data"
			         ."   FROM ".$wbasedato."_000020, ".$wbasedato."_000024, ".$wbasedato."_000021, ".$wbasedato."_000018 b"
			         ."  WHERE renfue = '".$wfuecar."'"
			         ."    AND renfec between '".$wfec_i."' AND '".$wfec_f."'"
			         ."    AND renest = 'on' "
			         ."    AND renfue = rdefue "
			         ."    AND rennum = rdenum "
			         ."    AND rdeffa = fenffa "
			         ."    AND rdefac = fenfac "
			         ."    AND fencod = empcod "
			         ."  GROUP BY 1,2 ";
			      $res = mysql_query($q,$conex);
			     }
	         }

	         echo "<center><table border=0>";

		     echo "<tr class=encabezadoTabla>";
		     echo "<th>FUENTE</th>";
		     echo "<th>DOCUMENTO</th>";
	         echo "<th>CUENTA</th>";
			 echo "<th>NOMBRE</th>";
			 echo "<th>C.COSTO</th>";
			 echo "<th>NIT/CED</th>";
			 echo "<th>NOMBRE</th>";
			 echo "<th>DEBITOS</th>";
			 echo "<th>CREDITOS</th>";
			 echo "</tr>";

			 //=============================================================================================================================================================================
		     //=============================================================================================================================================================================
		     // ACA TRAIGO TODOS LOS DOCUMENTOS SELECCIONADOS
		     //=============================================================================================================================================================================
		     //=============================================================================================================================================================================
		     $q = " SELECT fue, doc, tip, cco, fecha_data "
		         ."   FROM tempo1 ";
		     $resdoc = mysql_query($q,$conex);
		     $numdoc = mysql_num_rows($resdoc);
		     //=============================================================================================================================================================================

		     $wtotcomD=0;           //Total comprobante Debitos
		     $wtotcomC=0;           //Total comprobante Creditos

		     $wcolor="";

		     for ($j=1;$j<=$numdoc;$j++)
			   {
				 $wtotdocD=0;       //Total documento Debitos
		         $wtotdocC=0;       //Total documento Creditos

			     $rowdoc = mysql_fetch_array($resdoc);

			                        //carncr!=on             carndb!=on
			     if ($numfue > 0 and $rowfue[0] != "on" and $rowfue[1] != "on")  //Indica que es el comprobante de Facturacion
			        {
				     $q= " SELECT fenffa, fenfac, relfuecta, relfuenat, fencco, fennit, sum(fenval), empnom, relfuenit, fendpa as dre, fennpa as nre"
					    ."   FROM ".$wbasedato."_000018,".$wbasedato."_000078,".$wbasedato."_000024 "
					    ."  WHERE fenffa    = '".$rowdoc[0]."'"
					    ."    AND fenfac    = '".$rowdoc[1]."'"
						."    AND fenfec between '".$wfec_i."' AND '".$wfec_f."'"
					    ."    AND fenffa    = relfuecod "
					    ."    AND relfuetem = '".$rowdoc[2]."'"
					    ."    AND fenest    = 'on' "
					    ."    AND fenres    = empcod "
					    ."  GROUP BY 1,2,3,4,5,6,8,9,10,11 "
					    ."  ORDER BY 1,2 ";

					 $wnat1="D";
		             $wnat2="C";
					}
				   else
				      {
					   //===================================================================================
				       //Con este query se obtiene el comprobante resumido por ** fuente de notas **
				       $q= " SELECT renfue, rennum, relfuecta, relfuenat, rencco, empnit, renvca, empnom, relfuenit, fencod as dre, empnom as nre, empcod "
				          ."   FROM ".$wbasedato."_000020,".$wbasedato."_000078, ".$wbasedato."_000024, ".$wbasedato."_000021, ".$wbasedato."_000018 "
				          ."  WHERE renfue    = '".$rowdoc[0]."'"
				          ."    AND rennum    = '".$rowdoc[1]."'"
				          ."    AND renfue    = relfuecod "
				          ."    AND relfuetem = '".$rowdoc[2]."'"
				          ."    AND renest    = 'on' "
				          ."    AND renfue    = rdefue "
				          ."    AND rennum    = rdenum "
				          ."    AND rdeffa    = fenffa "
				          ."    AND rdefac    = fenfac "
				          ."    AND fencod    = empcod "
						  ."    AND fenfec between '".$wfec_i."' AND '".$wfec_f."'"
				          ."    AND rencco    = '".$rowdoc[3]."'"
				          //."  GROUP BY 1, 2,3,4,5,6,7,8,9,10,11 "
				          ."  GROUP BY 1, 2 "
				          ."  ORDER BY 1, 2 ";
				       $wnat1="C";
		               $wnat2="D";
				      }
				 $res = mysql_query($q,$conex);
		         $num = mysql_num_rows($res);


		         ver_comprobante_detallado($num,$res,"S");


		         //=========================================================================================================================================================================
			     //=========================================================================================================================================================================
			     //CON ESTE PROCEDIMIENTO VERIFICO QUE LA RELACION CONCEPTO, CCO Y TIPO DE EMPRESA EXISTAN PARA CADA UNA DE LAS FACTURAS
			     //Y POR CADA CONCEPTO-CENTRO DE COSTOS DE LA FACTURA.
			     //=========================================================================================================================================================================
			     //=========================================================================================================================================================================

			     if ($numfue > 0 and $rowfue[0] != "on" and $rowfue[1] != "on")  //Indica que es el comprobante de Facturacion
			        {
				                                                   //tip
				     $q= " SELECT fdefue, fdedoc, fdecon, fdecco, '".$rowdoc[2]."'"
				        ."   FROM ".$wbasedato."_000065"
				        ."  WHERE fdefue  = '".$rowdoc[0]."'"
				        ."    AND fdedoc  = '".$rowdoc[1]."'"
						."	  AND fecha_data = '".$rowdoc[4]."'"
				        ."    AND fdeest  = 'on' ";
			        }
			       else                            //Indica que es el comprobante de Notas
			          {
				       $q= " SELECT fdefue, fdedoc, fdecon, fdecco, '".$rowdoc[2]."'"
				          ."   FROM ".$wbasedato."_000065 "
				          ."  WHERE fdefue  = '".$rowdoc[0]."'"
				          ."    AND fdedoc  = '".$rowdoc[1]."'"
				          ."    AND fdecco  = '".$rowdoc[3]."'"
						  ."	  AND fecha_data = '".$rowdoc[4]."'"
				          ."    AND fdeest  = 'on' ";
				      }
			     $res = mysql_query($q,$conex);
			     $num = mysql_num_rows($res);

			     if ($num > 0)
			        {
				     for ($i=1;$i<=$num;$i++)
				         {
				          $row = mysql_fetch_array($res);

				          $q = " SELECT count(*) "
				              ."   FROM ".$wbasedato."_000077 "
				              ."  WHERE relconcon = '".$row[2]."'"
				              ."    AND relconcco = '".$row[3]."'"
				              ."    AND relcontem = '".$row[4]."'";   //Tipo de empresa
				          $res_exi = mysql_query($q,$conex);
				          $row_exi = mysql_fetch_array($res_exi);

				          if ($row_exi[0] == 0)
				             echo "Falta relación del concepto: ".$row[2]." con el centro de costo: ".$row[3]." y el tipo de empresa: '".$row[4]."' para la factura: ".$row[0]."-".$row[1]."<br>";
				         }
			        }


			     if ($numfue > 0 and $rowfue[0] != "on" and $rowfue[1] != "on")  //Facturacion
			        {
				     //=========================================================================================================================================================================
				     //para las cuentas de ** INGRESOS POR CONCEPTO **.
				     //=========================================================================================================================================================================
				     $q=  " SELECT fdefue, fdedoc, relconcin, '".$wnat2."', fdecco, fdeter, SUM(ROUND(fdevco*((100-fdepte)/100))-fdeviv), '', relconnit, fendpa as dre, fennpa as nre "
				         ."   FROM ".$wbasedato."_000065 a, ".$wbasedato."_000077, ".$wbasedato."_000018 b"
					     ."  WHERE fdefue    = '".$rowdoc[0]."'"
					     ."    AND fdedoc    = '".$rowdoc[1]."'"
					     ."    AND fdecon    = relconcon "
					     ."    AND fdecco    = relconcco "
						 ."    AND fenfec between '".$wfec_i."' AND '".$wfec_f."'"
						 ."	   AND a.fecha_data = b.fecha_data"
					     ."    AND relcontem = '".$rowdoc[2]."'"
					     ."    AND relconest = 'on'"    // se adiciona 2009-02-12  para saber el estado en la tabla 77
					     ."    AND fdeest    = 'on' "
					     ."    AND fdefue    = fenffa "
					     ."    AND fdedoc    = fenfac "
					     ."  GROUP BY 1,2,3,4,5,6,8,9,10,11 ";
					 $res = mysql_query($q,$conex);
				     $num = mysql_num_rows($res);

				     ver_comprobante_detallado($num,$res,"S");


				     //=========================================================================================================================================================================
				     //DESCUENTO DE INGRESOS
				     //=========================================================================================================================================================================
				     if ($rowdoc[2]=="01")
				        {
					     $q=" SELECT fdefue, fdedoc, relconcdi, '".$wnat1."', fdecco, fdeter, SUM(ROUND((fdevde*(100-fdepte)/100)/(1+(fdeviv/(fdevco-fdeviv))))), '', relconnit, fendpa as dre, fennpa as nre "
					       ."   FROM ".$wbasedato."_000065 a, ".$wbasedato."_000077, ".$wbasedato."_000018 b"
					       ."  WHERE fdefue    = '".$rowdoc[0]."'"
					       ."    AND fdedoc    = '".$rowdoc[1]."'"
					       ."    AND fdecon    = relconcon "
						   ."    AND fenfec between '".$wfec_i."' AND '".$wfec_f."'"
						   ."	 AND a.fecha_data = b.fecha_data"
					       ."    AND fdecco    = relconcco "
					       ."    AND relcontem = '".$rowdoc[2]."'"
					       ."    AND relconest = 'on'"    // se adiciona 2009-02-12  para saber el estado en la tabla 77
					       ."    AND fdeest    = 'on' "
					       ."    AND fdefue    = fenffa "
					       ."    AND fdedoc    = fenfac "
					       ."  GROUP BY 1,2,3,4,5,6,8,9,10,11 ";
					    }
				       else
				          {
					       $q=  " SELECT fdefue, fdedoc, relconcdi, '".$wnat1."', fdecco, fdeter, SUM(CEIL((fdevde*(100-fdepte)/100)/(1+(fdeviv/(fdevco-fdeviv))))), '', relconnit, fendpa as dre, fennpa as nre "
						       ."   FROM ".$wbasedato."_000065 a, ".$wbasedato."_000077, ".$wbasedato."_000018 b"
						       ."  WHERE fdefue    = '".$rowdoc[0]."'"
						       ."    AND fdedoc    = '".$rowdoc[1]."'"
						       ."    AND fdecon    = relconcon "
							   ."    AND fenfec between '".$wfec_i."' AND '".$wfec_f."'"
							   ."	 AND a.fecha_data = b.fecha_data"
						       ."    AND fdecco    = relconcco "
						       ."    AND relcontem = '".$rowdoc[2]."'"
						       ."    AND relconest = 'on'"    // se adiciona 2009-02-12  para saber el estado en la tabla 77
						       ."    AND fdeest    = 'on' "
						       ."    AND fdefue    = fenffa "
						       ."    AND fdedoc    = fenfac "
						       ."  GROUP BY 1,2,3,4,5,6,8,9,10,11 ";
				         }
				     $res = mysql_query($q,$conex);
				     $num = mysql_num_rows($res);


				     ver_comprobante_detallado($num,$res,"S");

				     //===========================================================================
					 //Con este query traigo el comprobante resumido por concepto de facturacion
					 //para las cuentas de ** TERCEROS POR CADA CONCEPTO **.
				     $q=  " SELECT fdefue, fdedoc, relconcte, '".$wnat2."', fdecco, fdeter, SUM(ROUND(fdevco*(fdepte/100))), '', relconnit, fendpa as dre, fennpa as nre "
					     ."   FROM ".$wbasedato."_000065 a, ".$wbasedato."_000077, ".$wbasedato."_000018 b"
					     ."  WHERE fdefue    = '".$rowdoc[0]."'"
					     ."    AND fdedoc    = '".$rowdoc[1]."'"
					     ."    AND fdecon    = relconcon "
					     ."    AND fdecco    = relconcco "
						 ."    AND fenfec between '".$wfec_i."' AND '".$wfec_f."'"
						 ."	   AND a.fecha_data = b.fecha_data"
					     ."    AND relcontem = '".$rowdoc[2]."'"
					     ."    AND relconest = 'on'"    // se adiciona 2009-02-12  para saber el estado en la tabla 77
					     ."    AND fdeest    = 'on' "
					     ."    AND fdefue    = fenffa "
					     ."    AND fdedoc    = fenfac "
					     ."  GROUP BY 1,2,3,4,5,6,8,9,10,11 ";
		             $res = mysql_query($q,$conex);
				     $num = mysql_num_rows($res);

				     ver_comprobante_detallado($num,$res,"S");

				     //===========================================================================
					 //Con este query traigo el comprobante resumido por concepto de facturacion
					 //para las cuentas de ** DESCUENTO DE TERCEROS **.
				     $q=  " SELECT fdefue, fdedoc, relconcdt, '".$wnat1."', fdecco, fdeter, SUM(ROUND(fdevde*(fdepte/100))), '', relconnit, fendpa as dre, fennpa as nre "
					     ."   FROM ".$wbasedato."_000065 a, ".$wbasedato."_000077, ".$wbasedato."_000018 b"
					     ."  WHERE fdefue    = '".$rowdoc[0]."'"
					     ."    AND fdedoc    = '".$rowdoc[1]."'"
					     ."    AND fdecon    = relconcon "
					     ."    AND fdecco    = relconcco "
						 ."    AND fenfec between '".$wfec_i."' AND '".$wfec_f."'"
						 ."	   AND a.fecha_data = b.fecha_data"
					     ."    AND relcontem = '".$rowdoc[2]."'"
					     ."    AND relconest = 'on'"    // se adiciona 2009-02-12  para saber el estado en la tabla 77
					     ."    AND fdeest    = 'on' "
					     ."    AND fdefue    = fenffa "
					     ."    AND fdedoc    = fenfac "
					     ."  GROUP BY 1,2,3,4,5,6,8,9,10,11 ";
		             $res = mysql_query($q,$conex);
				     $num = mysql_num_rows($res);

				     ver_comprobante_detallado($num,$res,"S");
			        }
			       else  //Entra por aca cuando la fuente es de Notas de cartera
			          {
					   //=========================================================================================================================================================================
					   //para las cuentas de ** INGRESOS POR CONCEPTO **.
					   //=========================================================================================================================================================================
					   $q=  " SELECT fdefue, fdedoc, relconcin, '".$wnat2."', fdecco, fdeter, SUM(ROUND(fdevco*((100-fdepte)/100))-fdeviv), '', relconnit, '' as dre, '' as nre "
					       ."   FROM ".$wbasedato."_000065, ".$wbasedato."_000077, ".$wbasedato."_000040 "
					       ."  WHERE fdefue    = '".$rowdoc[0]."'"
					       ."    AND fdedoc    = '".$rowdoc[1]."'"
					       ."    AND fdecon    = relconcon "
						   ."    AND fdecco    = relconcco "
						   ."    AND relcontem = '".$rowdoc[2]."'"
						   ."    AND relconest = 'on' "    // se adiciona 2009-02-12  para saber el estado en la tabla 77
						   ."    AND fdeest    = 'on' "
						   ."    AND fdefue    = carfue "
						   ."    AND carcfa    = 'on' "
						   ."    AND fdecco    = '".$rowdoc[3]."'"
						   ."  GROUP BY 1,2,3,4,5,6,8,9,10,11 ";
					   $res = mysql_query($q,$conex);
					   $num = mysql_num_rows($res);

					   //===========================================================================
					   //Esto se hace solo con la notas credito que se hicieron por devolucion total
					   //===========================================================================
					   if ($num == 0)
					      {
					       if ($numfue > 0 and $rowfue[0] == "on")
					        {
						     $q=  " SELECT fdefue, fdedoc, relconcin, '".$wnat2."', fdecco, fdeter, SUM(ROUND(fdevco*((100-fdepte)/100))-fdeviv), '', relconnit, '' as dre, '' as nre "
						         ."   FROM ".$wbasedato."_000021,".$wbasedato."_000065, ".$wbasedato."_000077, ".$wbasedato."_000040 "
						         ."  WHERE rdefue    = '".$rowdoc[0]."'"
						         ."    AND rdenum    = '".$rowdoc[1]."'"
						         ."    AND rdecco    = '".$rowdoc[3]."'"
						         ."    AND fdefue    = rdeffa "
						         ."    AND fdedoc    = rdefac "
						         ."    AND fdecon    = relconcon "
						         ."    AND fdecco    = relconcco "
						         ."    AND relcontem = '".$rowdoc[2]."'"
						         ."    AND relconest = 'on' "
						         ."    AND fdeest    = 'on' "
						   		 ."    AND fdefue    = carfue "
						   		 ."    AND carcfa    = 'on' "
						   		 ."    AND fdecco    = '".$rowdoc[3]."'"
						         ."  GROUP BY 1,2,3,4,5,6,8,9,10,11 ";
							  $res = mysql_query($q,$conex);
					   		  $num = mysql_num_rows($res);
						    }
					    }
					  //===========================================================================

					  if ($rowfue[1] != 'on')   //Si no es Nota Debito entra
					     {
						  ver_comprobante_detallado($num,$res,"S");
				         }

					   //DESCUENTO DE INGRESOS
					   $q=  " SELECT fdefue, fdedoc, relconcdi, '".$wnat1."', fdecco, fdeter, SUM(ROUND((fdevde*((100-fdepte)/100))-(fdeviv/(fdevco-fdeviv)))), '', relconnit, '' as dre, '' as nre "
					       ."   FROM ".$wbasedato."_000065, ".$wbasedato."_000077, ".$wbasedato."_000040 "
					       ."  WHERE fdefue    = '".$rowdoc[0]."'"
					       ."    AND fdedoc    = '".$rowdoc[1]."'"
					       ."    AND fdecon    = relconcon "
					       ."    AND fdecco    = relconcco "
					       ."    AND relcontem = '".$rowdoc[2]."'"
					       ."    AND relconest = 'on'"    // se adiciona 2009-02-12  para saber el estado en la tabla 77
					       ."    AND fdeest    = 'on' "
					       ."    AND fdefue    = carfue "
						   ."    AND carcfa    = 'on' "
						   ."    AND fdecco    = '".$rowdoc[3]."'"
					       ."  GROUP BY 1,2,3,4,5,6,8,9,10,11 ";
					   $res = mysql_query($q,$conex);
					   $num = mysql_num_rows($res);

					   if ($rowfue[1] != 'on')   //Si no es Nota Debito entra
					      ver_comprobante_detallado($num,$res,"S");

					   //===========================================================================
					   //Con este query traigo el comprobante resumido por concepto de facturacion
					   //para las cuentas de ** TERCEROS POR CADA CONCEPTO **.
					   $q=  " SELECT fdefue, fdedoc, relconcte, '".$wnat2."', fdecco, fdeter, SUM(ROUND(fdevco*(fdepte/100))), '', relconnit, '' as dre, '' as nre "
					       ."   FROM ".$wbasedato."_000065, ".$wbasedato."_000077, ".$wbasedato."_000040 "
					       ."  WHERE fdefue    = '".$rowdoc[0]."'"
					       ."    AND fdedoc    = '".$rowdoc[1]."'"
					       ."    AND fdecon    = relconcon "
					       ."    AND fdecco    = relconcco "
					       ."    AND relcontem = '".$rowdoc[2]."'"
					       ."    AND relconest = 'on'"    // se adiciona 2009-02-12  para saber el estado en la tabla 77
					       ."    AND fdeest    = 'on' "
					       ."    AND fdefue    = carfue "
						   ."    AND carcfa    = 'on' "
						   ."    AND fdecco    = '".$rowdoc[3]."'"
					       ."  GROUP BY 1,2,3,4,5,6,8,9,10,11 ";
			           $res = mysql_query($q,$conex);
					   $num = mysql_num_rows($res);

					   if ($rowfue[1] != 'on')  //Si no es Nota Debito entra
					      ver_comprobante_detallado($num,$res,"S");

					   //===========================================================================
					   //Con este query traigo el comprobante resumido por concepto de facturacion
					   //para las cuentas de ** DESCUENTO DE TERCEROS **.
					   $q=  " SELECT fdefue, fdedoc, relconcdt, '".$wnat1."', fdecco, fdeter, SUM(ROUND(fdevde*(fdepte/100))), '', relconnit, '' as dre, '' as nre "
					       ."   FROM ".$wbasedato."_000065, ".$wbasedato."_000077, ".$wbasedato."_000040 "
					       ."  WHERE fdefue    = '".$rowdoc[0]."'"
					       ."    AND fdedoc    = '".$rowdoc[1]."'"
					       ."    AND fdecon    = relconcon "
					       ."    AND fdecco    = relconcco "
					       ."    AND relcontem = '".$rowdoc[2]."'"
					       ."    AND relconest = 'on'"    // se adiciona 2009-02-12  para saber el estado en la tabla 77
					       ."    AND fdeest    = 'on' "
					       ."    AND fdefue    = carfue "
						   ."    AND carcfa    = 'on' "
						   ."    AND fdecco    = '".$rowdoc[3]."'"
					       ."  GROUP BY 1,2,3,4,5,6,8,9,10,11 ";
					   $res = mysql_query($q,$conex);
					   $num = mysql_num_rows($res);

					   if ($rowfue[1] != 'on')   //Si no es Nota Debito entra
					      ver_comprobante_detallado($num,$res,"S");

				      }


				 //===========================================================================
				 //Con este query traigo el comprobante resumido por concepto de facturacion
				 //para las cuentas de **** IVA ****.
				 if ($rowdoc[2]=="01")  //PARTICULAR
				    {
					 //$q=  " SELECT fdefue, fdedoc, relconciv, '".$wnat2."', '', '', SUM(fdeviv-(FLOOR((fdevde/(fdevco-fdeviv))*fdeviv))),'', '', '', '' "
				     $q=  " SELECT fdefue, fdedoc, relconciv, '".$wnat2."', '', '', SUM(fdeviv*(1-(fdevde/fdevco))),'', '', '', '' "
					     ."   FROM ".$wbasedato."_000065, ".$wbasedato."_000077, ".$tablaConceptos
					     ."  WHERE fdefue    = '".$rowdoc[0]."'"
					     ."    AND fdedoc    = '".$rowdoc[1]."'"
					     ."    AND fdecon    = relconcon "
					     ."    AND fdecco    = relconcco "
					     ."    AND relcontem = '".$rowdoc[2]."'"
					     ."    AND fdeest    = 'on' "
					     ."    AND relconest = 'on' "
					     ."    AND relconcon = grucod "
					     ."    AND gruabo    != 'on' "
					     ."    AND fdecco    = '".$rowdoc[3]."'"
					     ."  GROUP BY 1 ";
					}
				   else
				      {
					   $q=  " SELECT fdefue, fdedoc, relconciv, '".$wnat2."', '', '', SUM(fdeviv*(1-(fdevde/fdevco))),'', '', '', '' "
					       ."   FROM ".$wbasedato."_000065, ".$wbasedato."_000077, ".$tablaConceptos
					       ."  WHERE fdefue    = '".$rowdoc[0]."'"
					       ."    AND fdedoc    = '".$rowdoc[1]."'"
					       ."    AND fdecon    = relconcon "
					       ."    AND fdecco    = relconcco "
					       ."    AND relcontem = '".$rowdoc[2]."'"
					       ."    AND fdeest    = 'on' "
					       ."    AND relconest = 'on' "
					       ."    AND relconcon = grucod "
					       ."    AND gruabo   != 'on' "
					       ."    AND fdecco    = '".$rowdoc[3]."'"
					       ."  GROUP BY 1 ";
					  }
				 $res = mysql_query($q,$conex);
			     $num = mysql_num_rows($res);

			     //===========================================================================
			     //Esto se hace solo con la notas credito que se hicieron por devolucion total
			     //===========================================================================
			     if ($num == 0)
			        {
			         if ($numfue > 0 and $rowfue[0] == "on")
				        {
					     $q=  " SELECT fdefue, fdedoc, relconciv, '".$wnat2."', '', '', SUM(fdeviv*(1-(fdevde/fdevco))),'', '', '', '' "
					         ."   FROM ".$wbasedato."_000021,".$wbasedato."_000065, ".$wbasedato."_000077, ".$tablaConceptos
					         ."  WHERE rdefue    = '".$rowdoc[0]."'"
					         ."    AND rdenum    = '".$rowdoc[1]."'"
					         ."    AND rdecco    = '".$rowdoc[3]."'"
					         ."    AND fdefue    = rdeffa "
					         ."    AND fdedoc    = rdefac "
					         ."    AND fdecon    = relconcon "
					         ."    AND fdecco    = relconcco "
					         ."    AND relcontem = '".$rowdoc[2]."'"
					         ."    AND fdeest    = 'on' "
					         ."    AND relconest = 'on' "
					         ."    AND relconcon = grucod "
					         ."    AND gruabo   != 'on' "
					         ."    AND fdecco    = '".$rowdoc[3]."'"
					         ."  GROUP BY 1 ";
					    }
				    }
				 //===========================================================================


			     ver_comprobante_detallado($num,$res,"N");


			     $wtotdocD=round($wtotdocD,0);
			     $wtotdocC=round($wtotdocC,0);

			     if ($numfue > 0 and $rowfue[0] != "on" and $rowfue[1] != "on")    //COMPROBANTE DE FACTURACION
		            {

				     echo "<tr class=fila1>";
				     echo "<td colspan=7 >Total Documento </td>";
				     //On *** con esto puede sacar el total por documento para analizarlo en excel echo "<td colspan=7 bgcolor=CCCC33>".$rowdoc[1]." | ".$wtotdocD."</td>";
				     if ($wtotdocD!=$wtotdocC)
				        {
				         $wcolor="CC3300";
				         $wforcolor="ffffff";
			            }
				       else
				         {
				          $wcolor="";
				          $wforcolor="";
			             }

				     echo "<td align=right bgcolor=".$wcolor."><font color=".$wforcolor.">".number_format($wtotdocD,0,'.',',')."</td>";
				     echo "<td align=right bgcolor=".$wcolor."><font color=".$wforcolor.">".number_format($wtotdocC,0,'.',',')."</td>";
				     echo "</tr>";

                    }
	               else
					  {
					   //===========================================================================
					   //Con este query traigo el comprobante resumido por concepto de cartera
					   //para las cuentas de ** CONCEPTOS DE CARTERA **.
					   $q=  " SELECT rdefue, rdenum, concue, connat, rdecco, '', sum(rdevco), '', '', '', '' "
					       ."   FROM ".$wbasedato."_000021, ".$wbasedato."_000044 "
					       ."  WHERE rdefue = '".$rowdoc[0]."'"
					       ."    AND rdenum = '".$rowdoc[1]."'"
					       ."    AND mid(rdecon,1,instr(rdecon,'-')-1) = concod "
					       ."    AND rdefue = confue "
					       ."    AND rdeest = 'on' "
					       ."    AND conest = 'on' "
					       ."    AND rdecco    = '".$rowdoc[3]."'"
					       ."  GROUP BY 1,2,3,4,5 ";
					   $res = mysql_query($q,$conex);
					   $num = mysql_num_rows($res);

					   ver_comprobante_detallado($num,$res,"S");

					   if ($wtotdocD!=$wtotdocC)
				        $wcolor="CC3300";
				       else
				          $wcolor="";

			           echo "<tr class=fila1>";
				       echo "<td colspan=7   bgcolor=".$wcolor.">Total Documento de Notas</td>";
				       echo "<td align=right bgcolor=".$wcolor.">".number_format($wtotdocD,0,'.',',')."</td>";
				       echo "<td align=right bgcolor=".$wcolor.">".number_format($wtotdocC,0,'.',',')."</td>";
				       echo "</tr>";

		              }
		       }
            echo "<tr class=encabezadoTabla>";
		    echo "<td colspan=7 bgcolor=".$wcolor.">Total Comprobante ".$wfuecar1."</td>";

		    $wtotcomD=round($wtotcomD,0);
			$wtotcomC=round($wtotcomC,0);

		    if ($wtotcomD!=$wtotcomC)
		       $wcolor="CC3300";
		      else
		         $wcolor="";

		    echo "<td align=right bgcolor=".$wcolor.">".number_format($wtotcomD,0,'.',',')."</td>";
		    echo "<td align=right bgcolor=".$wcolor.">".number_format($wtotcomC,0,'.',',')."</td>";
		    echo "</tr>";
		    echo "</table>";
	    }

  echo "<br><br>";
  echo "<center><table>";
  echo "<tr><td align=center colspan=7><input type='submit' value='OK'>&nbsp|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:window.close();'></td></tr>";
  echo "</table>";

}
?>