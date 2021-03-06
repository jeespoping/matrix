<head>
  <title>COMPROBANTE DE FACTURACION RESUMIDO</title>
</head>
<body onload=ira()>
<script type="text/javascript">
	function enter()
	{
	   document.forms.comprobante_fac_res.submit();
	}

	function enter1()
	{
	   document.forms.comprobante_fac_res.submit();
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
   *     PROGRAMA PARA comprobantes_cartera       *
   ************************************************/

//==================================================================================================================================
//PROGRAMA                   : comprobantes_cartera.php
//AUTOR                      : Juan Carlos Hernández M.
  $wautor="Juan C. Hernandez M.";
//FECHA CREACION             : Octubre 24 de 2006
//FECHA ULTIMA ACTUALIZACION :
  $wactualiz=" Mayo 8 de 2012 ";
//DESCRIPCION
//====================================================================================================================================\\
//Objetivo:                                                                                                                           \\
//====================================================================================================================================\\


//========================================================================================================================================\\
//========================================================================================================================================\\
//ACTUALIZACIONES                                                                                                                         \\
//========================================================================================================================================\\
//________________________________________________________________________________________________________________________________________\\
//MAYO 8 DE 2012:                                                                                                                       \\
//________________________________________________________________________________________________________________________________________\\
//Se generaron UNION's para las consultas de Unix de modo que no sacaran error cuando existan campos Nulos. También se elimino la creación\\
//de tablas temporales ya que estas sacaban error cuando se usaban en mas de 1 Query. se crean entonces físicas y se borran antes de 	  \\
//finalizar el script y también antes de crearlas nuevamente si exiten
//________________________________________________________________________________________________________________________________________\\
//FEBRERO 9 DE 2012:                                                                                                                       \\
//________________________________________________________________________________________________________________________________________\\
//Se crearon las tablas temporalesSe tempo7 y tempo8 para mejorar la velocidad en la generacion del comprobante, estas tablas 	\\
//se usaronn en varios query´s para mejorar su velocidad																		\\
//________________________________________________________________________________________________________________________________________\\
//JULIO 12 DE 2011:                                                                                                                       \\
//________________________________________________________________________________________________________________________________________\\
//Se modifica el query de la linea 458 para que el group by no lo haga con el codigo de la empresa (campo 10), porque hay empresas con mas\\
//de un codigo.                                                                                                                           \\                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//Mayo 19 DE 2009:                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//Se corrige un problema con las notas debito, estaba saliendo tomando dos veces la misma informacion, se condiciona con un if.           \\
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
  session_register("wpagook");
  session_register("wprestamo");

  

  

  include_once("root/comun.php");

  //Traigo el nombre de la conexion segun la empresa
  $bd_unix = consultarAliasPorAplicacion($conex,$wemp_pmla,"unix_contabilidad");
  $conexunix = odbc_connect($bd_unix,'informix','sco')
  					    or die("No se realizo Conexion con el Unix");


  $institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

  $wbasedato = $institucion->baseDeDatos;

  $pos = strpos($user,"-");
  $wusuario = substr($user,$pos+1,strlen($user));

  $wfecha=date("Y-m-d");
  $hora = (string)date("H:i:s");

  echo "<form name='comprobante_fac_res' action='Comprobante_fac_res1.php' method=post>";

  echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
  echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";


  //===========================================================================================================================================
  function ver_comprobante($wfnum, $wfres, $wfitem, $wfgraba)
    {
	 global $wbasedato;
	 global $conex;
	 global $wtotcomD;
	 global $wtotcomC;
	 global $conexunix;

	 global $wgra_com;
	 global $wfec_com;
	 global $wdoc_com;
	 global $wfue_com;

	 global $wfuecartera;

	 $row  = mysql_fetch_array($wfres);   //Query se le envia a la funcion desde el programa ppal
	 $i=1;
	 while ($i<=$wfnum)
	    {
		 $wcta = $row[0];

		 $wtotctaD=0;   //Total cuenta Debitos
		 $wtotctaC=0;   //Total cuenta Créditos

		 while ($i<=$wfnum and $wcta == $row[0])
		      {
			   if ($row[4] != 0)       //Si el valor es diferente de cero
			      {
				   if ($i%2==0)
		              $wcolor="00FFFF";
		             else
		                $wcolor="";

		           if ($row[6] == 'on')     //Viene del query del campo relconnit: Indica si se imprime el nombre del responsable del documento
		              {
			           $wcem=$row[7];       //Codigo empresa

			           if ($wcem=="01")     //Si es PARTICULAR tomo el documento y el nombre del responsable del query
			              {
			               $wdre=$row[8];   //Documento responsable
			               $wnre=$row[9];   //Nombre responsable
		                  }
			             else               //Si es EMPRESA tomo el documento y el nombre del responsable de la tabla 000024
			                {
				             $q = " SELECT empnit, empnom "
				                 ."   FROM ".$wbasedato."_000024 "
				                 ."  WHERE empcod = '".$wcem."'";
				             $resemp = mysql_query($q,$conex);
		      				 $rowemp = mysql_fetch_array($resemp);

		      				 $wdre=$rowemp[0];
		      				 $wnre=$rowemp[1];
				            }
			          }
			         else  //relconnit=off pero el nit que trae corresponde a un tercero, entonces busco el tercero en medicos y si existe coloco el medico
			            {
				         if ($row[3] != "")
				            {
					         $q = " SELECT mednom "
					             ."   FROM ".$wbasedato."_000051 "
					             ."  WHERE meddoc = '".$row[3]."'";
					         $resmed = mysql_query($q,$conex);
			      			 $rowmed = mysql_fetch_array($resmed);

			      			 if ($rowmed != "")
			      			    {
				      			 $wdre=$row[3];
			      			     $wnre=$rowmed[0];
		      			        }
		      			       else
		      			          {
			      			       $wdre=$row[3];   //Documento responsable
				                   $wnre=$row[5];   //Nombre responsable
		      			          }
				            }
				           else
				              {
		      			       $wdre=$row[3];   //Documento responsable
			                   $wnre=$row[5];   //Nombre responsable
	      			          }
				        }

				   if ($wnre == "")   //Si el Nombre del tercero esta en blanco lo busco en medicos
				      {
					   $q = " SELECT mednom "
			               ."   FROM ".$wbasedato."_000051 "
			               ."  WHERE meddoc = '".$wdre."'";
			           $resmed = mysql_query($q,$conex);
	      			   $rowmed = mysql_fetch_array($resmed);

	      			   $wnre=$rowmed[0];
      			      }

	      		   if ($wnre=="")
				      {
					   $q = " SELECT empnit, empnom "
		                   ."   FROM ".$wbasedato."_000024 "
		                   ."  WHERE empcod = '".$row[3]."'";
		               $resemp = mysql_query($q,$conex);
      				   $rowemp = mysql_fetch_array($resemp);

      				   if ($wdre=="")
      				      {
      				       $wdre=$rowemp[0];
      				       $wnre=$rowemp[1];
      				      }
      				     else
      				        $wnre=$rowemp[1];
					  }

				   //Con este procedimiento cambio el valor a absoluto y la naturaleza del valor de debito a credito o viceversa
				   //Porque cuando el valor es negativo quiere decir que es un abono y por lo tanto los valores de estos conceptos
				   //deben mostrarse con naturaleza contraria.
				   if ($row[4] < 0)
				      {
					   if ($row[1] == "D")
				          $wnat="C";
				       if ($row[1] == "C")
				          $wnat="D";

				       $row[4]=abs($row[4]);    //Paso el valor a absoluto
			          }
			         else
			            $wnat=$row[1];

			       if ($wnat == "D")
				     {
				      echo "<tr class=fila2>";
					  echo "<td>".trim($row[0])."</td>";   									//Cuenta
					  echo "<td>&nbsp</td>";         										//Blanco
					  echo "<td>".trim($row[2])."</td>";   									//Centro de costos
					  echo "<td>".trim($wdre)."</td>";   									//Documento tercero o Responsable
					  echo "<td>".trim($wnre)."</td>";   									//Nombre Tercero o Responsable
					  echo "<td align=right>".number_format($row[4],0,'.',',')."</td>"; 	//Valor
					  echo "<td>&nbsp</td>";												//Blanco
					  echo "</tr>";
					  $wtotctaD=$wtotctaD+$row[4];
					  $wtotcomD=$wtotcomD+$row[4];
					 }
				    else
				       if ($wnat == "C")
					     {
					      echo "<tr class=fila2>";
						  echo "<td>".trim($row[0])."</td>";								//Cuenta
						  echo "<td>&nbsp</td>";											//Blanco
						  echo "<td>".trim($row[2])."</td>";								//Centro de costos
						  echo "<td>".trim($wdre)."</td>";									//Documento tercero o responsable
						  echo "<td>".trim($wnre)."</td>";									//Nombre Tercero o responsable
						  echo "<td>&nbsp</td>";											//Valor
						  echo "<td align=right>".number_format($row[4],0,'.',',')."</td>"; //Blanco
						  echo "</tr>";
						  $wtotctaC=$wtotctaC+$row[4];
						  $wtotcomC=$wtotcomC+$row[4];
						 }

				  //===============================================================================================================================================================================================================================================================
			      //ACA GRABO EN LA CONTABILIDAD DEL UNIX
			      //===============================================================================================================================================================================================================================================================
				  if ($wfgraba == "on")
				     {
					  //=====================================================
					  //Aca busco si la cuenta se detalla por centro de costo
					   $query = " SELECT cueicc "
				               ."   FROM cocue "
				               ."  WHERE cuecod = '".$row[0]."'"
							   ."	 AND cueicc is not null "
							   ."  UNION "
							   ." SELECT ' ' "
				               ."   FROM cocue "
				               ."  WHERE cuecod = '".$row[0]."'"
							   ."	 AND cueicc is null ";
				       $res = odbc_do($conexunix,$query);

				       while(odbc_fetch_row($res))
					     {
					      if (odbc_result($res,1) == "N" )  //Si no detalla, coloco el C.C. en nulo
					         $row[2]="00";
					     }
					  //=====================================================

					  //=====================================================
					  //Aca busco si la cuenta se detalla por Nit
					   $query = " SELECT cuesni "
				               ."   FROM cocue "
				               ."  WHERE cuecod = '".$row[0]."'"
							   ."	 AND cuesni is not null "
							   ."  UNION "
							   ." SELECT ' ' "
				               ."   FROM cocue "
				               ."  WHERE cuecod = '".$row[0]."'"
							   ."	 AND cuesni is null ";
				       $res = odbc_do($conexunix,$query);

				       while(odbc_fetch_row($res))
					     {
					      if (odbc_result($res,1) == "N" )  //Si no detalla, coloco el Nit. en nulo
					         $wdre="0";
					     }
					  //=====================================================

					  //=====================================================
					  //Aca busco si la cuenta mueve base con su respectivo concepto
					   $query = " SELECT cuecon, conpor "
				               ."   FROM cocue, cocon "
				               ."  WHERE cuecod = '".$row[0]."'"
				               ."    AND cuebas = 'S' "
				               ."    AND conact = 'S' "
				               ."    AND cuecon is not null "
				               ."    AND conpor is not null "
							   ."  UNION "
							   ." SELECT cuecon, '0' "
				               ."   FROM cocue, cocon "
				               ."  WHERE cuecod = '".$row[0]."'"
				               ."    AND cuebas = 'S' "
				               ."    AND conact = 'S' "
				               ."    AND cuecon is not null "
				               ."    AND conpor is null "
							   ."  UNION "
							   ." SELECT ' ', conpor "
				               ."   FROM cocue, cocon "
				               ."  WHERE cuecod = '".$row[0]."'"
				               ."    AND cuebas = 'S' "
				               ."    AND conact = 'S' "
				               ."    AND cuecon is null "
				               ."    AND conpor is not null "
							   ."  UNION "
							   ." SELECT ' ', '0' "
				               ."   FROM cocue, cocon "
				               ."  WHERE cuecod = '".$row[0]."'"
				               ."    AND cuebas = 'S' "
				               ."    AND conact = 'S' "
				               ."    AND cuecon is null "
				               ."    AND conpor is null ";
				       $res = odbc_do($conexunix,$query);

				       $wconbas="";
				       $wvalbas=0;
				       while(odbc_fetch_row($res))
					     {
						  $wconbas= odbc_result($res,1);
					      $wporbas= odbc_result($res,2);

					      $wvalbas=round($row[4]*($wporbas/100));
					     }
					  //=====================================================

					  if ($wnat=="D")
					     $wnat="1";
					    else
					       $wnat="2";

					  $wfec=explode("-",$wfec_com);

					  $k=$wfitem-$wfnum+$i; //Aca resto el total de items de todo el comprobante menos los items de cada query mas 1, para que me de el valor real del item a grabar en el comprobante

					  $q = "INSERT INTO comov(   movfue      ,   movdoc      , movane,   movano     ,   movmes     ,  movite,   movfec      ,   movcue    ,   movcco          ,   movnit        , movdes                              ,   movind  ,  movval   ,   movcon     ,   movbas    , movfac, movuni, movcam, movbaj, movanu) "
					      ."           VALUES('".$wfue_com."','".$wdoc_com."', ''    ,'".$wfec[0]."','".$wfec[1]."',".$k."  ,'".$wfec_com."','".$row[0]."','".trim($row[2])."','".trim($wdre)."','COMPROBANTE FUENTE ".$wfuecartera."','".$wnat."',".$row[4].",'".$wconbas."', ".$wvalbas.", 0     , 0     , 0     , 'N'   , '0' )   ";
					  $res = odbc_do($conexunix,$q);
					 }
				   //===============================================================================================================================================================================================================================================================
				 }
			   $row  = mysql_fetch_array($wfres);
			   $i++;
			 }

	     if (($wtotctaD != 0) or ($wtotctaC != 0))
	        {
		     echo "<tr class=fila1>";
		     echo "<td colspan=5><b>Total Cuenta ".$wcta."</b></td>";
		     echo "<td align=right><b>".number_format($wtotctaD,0,'.',',')."</b></td>";
		     echo "<td align=right><b>".number_format($wtotctaC,0,'.',',')."</b></td>";
		     echo "</tr>";
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

  encabezado("COMPROBANTE DE FACTURACION Y NOTAS RESUMIDO ",$wactualiz,$wbasedato);


  if (((!isset($wfuecar) and !isset($wfec_i)) or (!isset($wfec_f)) or (!isset($wfue_com) and !isset($wfec_com))) and !isset($wgra_com))
     {
	  echo "<br>";
	  echo "<center><table>";

	  if (!isset($wfuecar)) $wfuecar="";

	  echo "<tr class=seccion1>";
      $q =  " SELECT carfue, cardes "
           ."   FROM ".$wbasedato."_000040, ".$wbasedato."_000078 "
	       ."  WHERE carest    = 'on' "
		   ."    AND carfue    = relfuecod "
		   ."    AND relfueest = 'on' "
		   ."    AND (carndb   = 'on' "
		   ."     OR  carncr   = 'on' "
		   ."     OR  carfac   = 'on') "
		   ."  GROUP BY 1,2 "
		   ."  ORDER BY carfue ";
	  $res = mysql_query($q,$conex);
	  $num = mysql_num_rows($res);

	  echo "<td align=center colspan=2><b>Fuente cartera: </b><select name='wfuecar'>";

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


	  //EMPRESA
	  echo "<tr class=seccion1>";
      $q =  " SELECT empcod, empnit, empnom "
           ."   FROM ".$wbasedato."_000024 "
           ."  WHERE empcod = empres "
	       ."  GROUP BY 1,2,3 "
		   ."  ORDER BY 3,1 ";

	  $res = mysql_query($q,$conex);
	  $num = mysql_num_rows($res);

	  echo "<td align=left colspan=2><b>Empresa: </b><select name='wempresa'>";

	  if (isset($wempresa))
	     echo "<option selected>".$wempresa."</option>";
	    else
	       echo "<option selected>% - Todas</option>";

	  for ($i=1;$i<=$num;$i++)
	     {
		  $row = mysql_fetch_array($res);
	      echo "<option>".$row[0]." - ".$row[1]." - ".$row[2]."</option>";
	     }
	  echo "</select></td>";

	  echo "<tr class=seccion1>";
	  echo "<td align=center bgcolor=".$wcf."><b><font text color=".$wclfg." size=4>Grabar el Comprobante</font></b><input type='checkbox' name='wgra_com' SIZE=2 ></td>";
	  echo "<td align=center bgcolor=".$wcf."><b><font text color=".$wclfg." size=4>Reemplazar si Existe</font></b><input type='checkbox' name='wreem_com' SIZE=2 ></td>";
	  echo "</tr>";


	  echo "<tr class=seccion1><td align=center colspan=2><b>Fuente Contable Comprobante:</b><INPUT TYPE='text' NAME='wfue_com'></td></tr>";
	  echo "<tr class=seccion1><td align=center colspan=2><b>Nro Dcto Comprobante:</b><INPUT TYPE='text' NAME='wdoc_com'></td></tr>";
	  echo "<tr class=seccion1><td align=center colspan=2><b>Fecha del Comprobante:</b><INPUT TYPE='text' NAME='wfec_com'></td></tr>";
	  echo "</table>";
	  echo "<br>";
	 }
    else
       {
	     echo "<br><br>";
	     echo "<center><table>";

	     $wfue=explode("-",$wfuecar);
	     $wempresa=explode("-",$wempresa);
		 $wfuecartera=$wfuecar;

	     //*******************************************************************************
        //// ***  T E M P O R A L *******************************************************
        /*
        $wfue=explode("-",$wfuecar);

        $q=  " CREATE TEMPORARY TABLE if not exists facturas as "
	        ." SELECT fenffa as fue, fenfac as doc, mid(fentip,1,instr(fentip,'-')-1) as tip"
		    ."   FROM ".$wbasedato."_000018 "
		    ."  WHERE fenffa = '".$wfue[0]."'"
		    ."    AND fenfec BETWEEN '".$wfec_i."' AND '".$wfec_f."'"
		    ."    AND fenfac = 'CS-2995' "
		    ."    AND fenest = 'on' "
		    ."  GROUP BY 1,2 ";
	    $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

	    $q = " SELECT doc "
	        ."   FROM facturas "
	        ."  WHERE trim(doc) != '' " ;
	    $resfac = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	    $numfac = mysql_num_rows($resfac);
	    */
	    //*******************************************************************************

	 ///*** Temporal el for
	 //for ($i=1;$i<=$numfac;$i++)                  //On temporal
	 //  {
	 //   $fac = mysql_fetch_array($resfac);        //On temporal



	     echo "<tr class=seccion1>";
	     echo "<td align=left><b>Fuente de Cartera:</b>".$wfuecar."</td>";
	     echo "<td align=center colspan=3><b>Fecha Inicial:</font></b>".$wfec_i." <b>&nbsp&nbsp&nbspFecha Final:</b>".$wfec_f."</td>";
		 echo "</tr>";
		 echo "<tr class=seccion1>";
		 echo "<td align=left><b>Fuente Contable Comprobante:</b>".$wfue_com."</td>";
		 echo "<td align=left><b>Nro Dcto Comprobante:</b>".$wdoc_com."</td>";
		 echo "<td align=left><b>Fecha del Comprobante:</b>".$wfec_com."</td>";
		 echo "</tr>";
		 echo "</table>";
		 echo "<br><br>";


		 ///$wempresa=explode("-",$wempresa);

		 ///$wfuecartera=$wfuecar;

	     ///$pos = strpos($wfuecar,"-");
	     ///$wfuecar = substr($wfuecar,0,$pos-1);

	     $q= " SELECT carncr, carndb "
		    ."   FROM ".$wbasedato."_000040 "
		    ."  WHERE carfue = '".$wfue[0]."'"
		    ."    AND carest = 'on' "
		    ."    AND (carndb    = 'on' "
		    ."     OR  carncr    = 'on' "
		    ."     OR  carfac    = 'on') "
		    ."  GROUP BY 1,2 ";
		 $resfue = mysql_query($q,$conex);
	     $numfue = mysql_num_rows($resfue);
	     $rowfue = mysql_fetch_array($resfue);

	     //***************************************************************************************
	     //COMPROBANTE DE *** FACTURACION ***
	     //***************************************************************************************
	     //Si ambos son diferentes a 'on' indica que la fuente es de facturas
	     //Si alguno de los dos es off es porque corresponde a una fuente de notas
	     if ($numfue > 0 and $rowfue[0] != "on" and $rowfue[1] != "on")    //COMPROBANTE DE FACTURACION
	        {

		     /*
		     $q = " DROP TEMPORARY TABLE if exists tempo1 ";
		     $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		     $q = " DROP TEMPORARY TABLE if exists tempo2 ";
		     $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		     $q = " DROP TEMPORARY TABLE if exists tempo3 ";
		     $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		     $q = " DROP TEMPORARY TABLE if exists tempo4 ";
		     $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		     $q = " DROP TEMPORARY TABLE if exists tempo5 ";
		     $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		     $q = " DROP TEMPORARY TABLE if exists tempo6 ";
		     $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		     */

		     //==============================================================================
		     //ESTE ES EL QUERY BASE PARA EL COMPROBANTE DE FACTURACION
		     //==============================================================================

				// Borra la tabla temporal tempo1
				$qdel = "	DROP TABLE IF EXISTS tempo1 ";
				$resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());

		     $q= " CREATE TABLE IF NOT EXISTS tempo1 AS "
		        ." SELECT fenffa as fue, fenfac as doc, mid(fentip,1,instr(fentip,'-')-1) as tip, fencod as cem, fendpa as dre, fennpa as nre, b.fecha_data as fecha_data "
			    ."   FROM ".$wbasedato."_000018 b"
			    ."  WHERE fenffa = '".$wfue[0]."'"
			    ."    AND fenfec between '".$wfec_i."' AND '".$wfec_f."'"
			    //."    AND fenfac = '".$fac[0]."'"                              //On temporal
			    ."    AND fenest = 'on' "
			    ."    AND fencod like '".trim($wempresa[0])."'"
			    //."    AND fennit = '39270589' "    ///On quitar
			    ."  GROUP BY 1, 2, 3, 4, 5, 6 "
			    ."  ORDER BY 3, 4, 5 ";
			 $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

			 //====================================================================================================================
			 //====================================================================================================================
			 //====================================================================================================================
			 // OJO OJO OJO OJO ESTE PROCESO AYUDA A BUSCAR DESCUADRES -- LEERLO -- OJO OJO OJO OJO
			 //====================================================================================================================
			 //Este echo muestra cada una de las facturas procesadas, esto sirve para buscar descuadres cuando en este comprobante
			 //sale menos dinero que el comprobante detallado.
			 /*

			 $q= " SELECT doc "
			    ."   FROM tempo1 "
			    ."  ORDER BY 1 ";
				 $resfac = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
			 $numfac = mysql_num_rows($resfac);
			 if ($numfac>0)
			    {
				 for ($i=1;$i<=$numfac;$i++)
				     {
					  $rowfac = mysql_fetch_array($resfac);
					  echo $rowfac[0]."<br>";
					 }
			    }
			 */
			 //====================================================================================================================
			 //====================================================================================================================
			 //====================================================================================================================

				// Borra la tabla temporal tempo2
				$qdel = "	DROP TABLE IF EXISTS tempo2 ";
				$resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());

			 //====================================================================================================================
		     //Con este query se obtiene el comprobante resumido por ** fuente de facturas **
			 $q= " CREATE TABLE IF NOT EXISTS tempo2 AS "
			    ." SELECT relfuecta as cta, relfuenat as nat, '' as cco, fennit as ter, sum(fenval) as val, empnom as res, relfuenit as nit, cem, dre, nre "
			    ."   FROM ".$wbasedato."_000018,".$wbasedato."_000078, tempo1, ".$wbasedato."_000024 "
			    ."  WHERE fenffa    = fue "
				."    AND fenfec between '".$wfec_i."' AND '".$wfec_f."'"
			    ."    AND fenfac    = doc "
			    ."    AND fenffa    = relfuecod "
			    ."    AND relfuetem = tip "
			    ."    AND fenest    = 'on' "
			    ."    AND fencod    = empcod "
			    ."    AND fencod    = '01' "              //Particulares
			    ."    AND relfuenit = 'on' "
			    ."    AND fencod like '".trim($wempresa[0])."'"
			    ."    AND fenval   >= 0 "
			    ."    AND fenres    = empres "
			    ."  GROUP BY 1, 2, 3, 4, 6, 7, 8, 9, 10 "

			    ." UNION "

			    ." SELECT relfuecta as cta, relfuenat as nat, '' as cco, relfueter as ter, sum(fenval) as val, '' as res, relfuenit as nit, '' as cem, '' as dre, '' as nre "
			    ."   FROM ".$wbasedato."_000018,".$wbasedato."_000078, tempo1, ".$wbasedato."_000024 "
			    ."  WHERE fenffa    = fue "
				."    AND fenfec between '".$wfec_i."' AND '".$wfec_f."'"
			    ."    AND fenfac    = doc "
			    ."    AND fenffa    = relfuecod "
			    ."    AND relfuetem = tip "
			    ."    AND fenest    = 'on' "
			    ."    AND fencod    = empcod "
			    ."    AND fencod    = '01' "              //Particulares
			    ."    AND relfuenit = 'off' "
			    ."    AND fencod like '".trim($wempresa[0])."'"
			    ."    AND fenval   >= 0 "
			    ."    AND fenres    = empres "
			    ."  GROUP BY 1, 2, 3, 4, 6, 7 "


			    ." UNION "

			    ." SELECT relfuecta as cta, relfuenat as nat, '' as cco, empnit as ter, sum(fenval) as val, empnom as res, relfuenit as nit, empcod as cem, fendpa as dre, fennpa as nre "
			    ."   FROM ".$wbasedato."_000018,".$wbasedato."_000078, tempo1, ".$wbasedato."_000024 "
			    ."  WHERE fenffa     = fue "
				."    AND fenfec between '".$wfec_i."' AND '".$wfec_f."'"
			    ."    AND fenfac     = doc "
			    ."    AND fenffa     = relfuecod "
			    ."    AND relfuetem  = tip "
			    ."    AND fenest     = 'on' "
			    ."    AND fenres     = empcod "    /// ojo
			    ."    AND fencod    != '01' "             //Otras Empresas
			    ."    AND relfuenit  = 'on' "             //Indica que debe mostrar el tercero
			    ."    AND relfueben != 'on' "
			    ."    AND fencod    like '".trim($wempresa[0])."'"
			    ."    AND fenval    >= 0 "
			    ."    AND fenres     = empres "
			    ."  GROUP BY 1, 2, 3, 4, 6, 7, 8, 9, 10 "

			    ." UNION "

			    ." SELECT relfuecta as cta, relfuenat as nat, '' as cco, fendpa as ter, sum(fenval) as val, fennpa as res, relfuenit as nit, empcod as cem, empnit as dre, empnom as nre "
			    ."   FROM ".$wbasedato."_000018,".$wbasedato."_000078, tempo1, ".$wbasedato."_000024 "
			    ."  WHERE fenffa     = fue "
				."    AND fenfec between '".$wfec_i."' AND '".$wfec_f."'"
			    ."    AND fenfac     = doc "
			    ."    AND fenffa     = relfuecod "
			    ."    AND relfuetem  = tip "
			    ."    AND fenest     = 'on' "
			    ."    AND fencod     = empcod "    /// ojo
			    ."    AND fencod    != '01' "             //Otras Empresas
			    ."    AND relfuenit != 'on' "             //Indica que NO debe mostrar el tercero
			    ."    AND relfueben  = 'on' "             //Indica que como tercero se muestra al beneficiario
			    ."    AND fencod    like '".trim($wempresa[0])."'"
			    ."    AND fenval    >= 0 "
			    ."    AND fenres     = empres "
			    ."  GROUP BY 1, 2, 3, 4, 6, 7, 8, 9, 10 "

			    ." UNION "

			    ." SELECT relfuecta as cta, relfuenat as nat, '' as cco, relfueter as ter, sum(fenval) as val, '' as res, relfuenit as nit, '' as cem, '' as dre, '' as nre "
			    ."   FROM ".$wbasedato."_000018,".$wbasedato."_000078, tempo1, ".$wbasedato."_000024 "
			    ."  WHERE fenffa     = fue "
				."    AND fenfec between '".$wfec_i."' AND '".$wfec_f."'"
			    ."    AND fenfac     = doc "
			    ."    AND fenffa     = relfuecod "
			    ."    AND relfuetem  = tip "
			    ."    AND fenest     = 'on' "
			    ."    AND fencod     = empcod "
			    ."    AND fencod    != '01' "             //Otras Empresas
			    ."    AND relfuenit != 'on' "            //No debe mostrar el tercero
			    ."    AND relfueben != 'on' "            //No debe mostrar el tercero
			    ."    AND fencod    like '".trim($wempresa[0])."'"
			    ."    AND fenval    >= 0 "
			    ."    AND fenres     = empres "
			    ."  GROUP BY 1, 2, 3, 4, 6, 7 "



			    ////////////////////////   De aca para abajo toma los valores de las facturas ** Negativas **.
			    ." UNION "

			    ." SELECT relfuecta as cta, relfuenat as nat, '' as cco, fennit as ter, sum(fenval) as val, empnom as res, relfuenit as nit, cem, dre, nre "
			    ."   FROM ".$wbasedato."_000018,".$wbasedato."_000078, tempo1, ".$wbasedato."_000024 "
			    ."  WHERE fenffa    = fue "
				."    AND fenfec between '".$wfec_i."' AND '".$wfec_f."'"
			    ."    AND fenfac    = doc "
			    ."    AND fenffa    = relfuecod "
			    ."    AND relfuetem = tip "
			    ."    AND fenest    = 'on' "
			    ."    AND fencod    = empcod "
			    ."    AND fencod    = '01' "              //Particulares
			    ."    AND relfuenit = 'on' "
			    ."    AND fencod like '".trim($wempresa[0])."'"
			    ."    AND fenval    < 0 "
			    ."    AND fenres    = empres "
			    ."  GROUP BY 1, 2, 3, 4, 6, 7, 8, 9, 10 "

			    ." UNION "

			    ." SELECT relfuecta as cta, relfuenat as nat, '' as cco, relfueter as ter, sum(fenval) as val, '' as res, relfuenit as nit, '' as cem, '' as dre, '' as nre "
			    ."   FROM ".$wbasedato."_000018,".$wbasedato."_000078, tempo1, ".$wbasedato."_000024 "
			    ."  WHERE fenffa    = fue "
				."    AND fenfec between '".$wfec_i."' AND '".$wfec_f."'"
			    ."    AND fenfac    = doc "
			    ."    AND fenffa    = relfuecod "
			    ."    AND relfuetem = tip "
			    ."    AND fenest    = 'on' "
			    ."    AND fencod    = empcod "
			    ."    AND fencod    = '01' "              //Particulares
			    ."    AND relfuenit = 'off' "
			    ."    AND fencod like '".trim($wempresa[0])."'"
			    ."    AND fenval    < 0 "
			    ."    AND fenres    = empres "
			    ."  GROUP BY 1, 2, 3, 4, 6, 7 "

			    ." UNION "

			    ." SELECT relfuecta as cta, relfuenat as nat, '' as cco, fennit as ter, sum(fenval) as val, empnom as res, relfuenit as nit, '' as cem, '' as dre, '' as nre "
			    ."   FROM ".$wbasedato."_000018,".$wbasedato."_000078, tempo1, ".$wbasedato."_000024 "
			    ."  WHERE fenffa    = fue "
				."    AND fenfec between '".$wfec_i."' AND '".$wfec_f."'"
			    ."    AND fenfac    = doc "
			    ."    AND fenffa    = relfuecod "
			    ."    AND relfuetem = tip "
			    ."    AND fenest    = 'on' "
			    ."    AND fencod    = empcod "
			    ."    AND fencod   <> '01' "             //Otras Empresas
			    ."    AND relfuenit = 'on' "             //Indica que debe mostrar el tercero
			    ."    AND fencod like '".trim($wempresa[0])."'"
			    ."    AND fenval    < 0 "
			    ."    AND fenres    = empres "
			    ."  GROUP BY 1, 2, 3, 4, 6, 7, 8, 9, 10 "

			    ." UNION "

			    ." SELECT relfuecta as cta, relfuenat as nat, '' as cco, relfueter as ter, sum(fenval) as val, '' as res, relfuenit as nit, '' as cem, '' as dre, '' as nre "
			    ."   FROM ".$wbasedato."_000018,".$wbasedato."_000078, tempo1, ".$wbasedato."_000024 "
			    ."  WHERE fenffa    = fue "
				."    AND fenfec between '".$wfec_i."' AND '".$wfec_f."'"
			    ."    AND fenfac    = doc "
			    ."    AND fenffa    = relfuecod "
			    ."    AND relfuetem = tip "
			    ."    AND fenest    = 'on' "
			    ."    AND fencod    = empcod "
			    ."    AND fencod   <> '01' "             //Otras Empresas
			    ."    AND relfuenit = 'off' "            //No debe mostrar el tercero
			    ."    AND fencod like '".trim($wempresa[0])."'"
			    ."    AND fenval    < 0 "
			    ."    AND fenres    = empres "
			    ."  GROUP BY 1, 2, 3, 4, 6, 7 "

			    ////////////////////////    Hasta aca tiene en cuenta las facturas ** Negativas **.
			    ."  ORDER BY 1, 2, 7, 8, 9 ";
			 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

			 $q= " SELECT cta,nat,cco,ter,sum(val),res,nit,cem,dre,nre "
	             ."  FROM tempo2 "
	             ." WHERE val >= 0 "
	             ." GROUP BY 1, 2, 3, 4, 6, 7, 8, 9, 10  "
	             ." UNION "

		         ." SELECT cta,nat,cco,ter,sum(val),res,nit,cem,dre,nre "
		         ."  FROM tempo2 "
	             ."  WHERE val < 0 "
		         ."  GROUP BY 1, 2, 3, 4, 6, 7, 8, 9, 10 ";
	         $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	         $num = mysql_num_rows($res);

	         $wnat1="D";
	         $wnat2="C";
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
				// Borra la tabla temporal tempo1
				$qdel = "	DROP TABLE IF EXISTS tempo7 ";
				$resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());

					 $q= " CREATE TABLE if not exists tempo7 as "
						." SELECT * "
						."   FROM ".$wbasedato."_000020 "
						."  WHERE renfue = '".$wfue[0]."'"
						."    AND renfec between '".$wfec_i."' AND '".$wfec_f."'"
						."    AND renest = 'on' ";
					 $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

				  //Se le crea un indice a la temporal
			      $q = " CREATE UNIQUE INDEX idxren ON tempo7 (renfue(4), rennum, rencco(6))";
			      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

			      $q = " CREATE INDEX idxfec ON tempo7 (renfec)";
			      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			      //===================================================================================
			      //ESTE ES EL QUERY BASE PARA EL COMPROBANTE DE NOTAS DE CARTERA
			      //===================================================================================
					// Borra la tabla temporal tempo1
					$qdel = "	DROP TABLE IF EXISTS tempo1 ";
					$resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());

			      $q= " CREATE TABLE IF NOT EXISTS tempo1 AS "
			         ." SELECT renfue as fue, rennum as doc, mid(emptem,1,instr(emptem,'-')-1) as tip, fencod as cem, empnit as dre, empnom as nre, renest, rencco, renvca, b.fecha_data as fecha_data "
			         ."   FROM tempo7, ".$wbasedato."_000024, ".$wbasedato."_000021, ".$wbasedato."_000018 b"
			         ."  WHERE renfue = rdefue "
			         ."    AND rennum = rdenum "
			         ."    AND rdeffa = fenffa "
			         ."    AND rdefac = fenfac "
			         //."    AND fencod = empcod "
			         ."    AND fencod like '".trim($wempresa[0])."'"
			         ."    AND fenres = empres "
					 ."    AND fenfec between '".$wfec_i."' AND '".$wfec_f."'"
			         //."  GROUP BY 1,2,3,4,5,6 ";
			         ."  GROUP BY 1,2 ";
			      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

			      //Se le crea un indice a la temporal
			      $q = " CREATE UNIQUE INDEX fuedoc ON tempo1 (fue(4), doc, tip(10), cem(10), dre, nre)";
			      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


					// Borra la tabla temporal tempo2
					$qdel = "	DROP TABLE IF EXISTS tempo2 ";
					$resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());

			      //===================================================================================
			      //Con este query se obtiene el comprobante resumido por ** fuente de notas **
				  $q= " CREATE TABLE IF NOT EXISTS tempo2 AS  "
				     ." SELECT relfuecta as cta, relfuenat as nat, rencco as cco, relfueter as ter, sum(renvca) as val, nre as res, relfuenit as nit, cem, dre, nre"
				     ."   FROM ".$wbasedato."_000078, tempo1 "
				     ."  WHERE fue    = relfuecod "
				     ."    AND relfuetem = tip "
				     ."    AND renest    = 'on' "
				     ."    AND tip       = '01' "
				     ."    AND relfuenit = 'on' "              //Indica que debe mostrar el tercero
				     ."  GROUP BY 1,2,3,4,6,7,8,9,10 "

				     ."  UNION "

				     ." SELECT relfuecta as cta, relfuenat as nat, rencco as cco, relfueter as ter, sum(renvca) as val, '' as res, relfuenit as nit, cem, dre, nre "
				     ."   FROM ".$wbasedato."_000078, tempo1 "
				     ."  WHERE fue    = relfuecod "
				     ."    AND relfuetem = tip "
				     ."    AND renest    = 'on' "
				     ."    AND tip       = '01' "
				     ."    AND relfuenit = 'off' "             //Indica que No debe mostrar el tercero
				     ."  GROUP BY 1,2,3,4,7 "

				     ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

				     ."  UNION "

				     ." SELECT relfuecta as cta, relfuenat as nat, rencco as cco, relfueter as ter, sum(renvca) as val, nre as res, relfuenit as nit, cem, dre, nre "
				     ."   FROM ".$wbasedato."_000078, tempo1 "
				     ."  WHERE fue    = relfuecod "
				     ."    AND relfuetem = tip "
				     ."    AND renest    = 'on' "
				     ."    AND tip       <> '01' "
				     ."    AND relfuenit = 'on' "             //Indica que debe mostrar el tercero
				     ."  GROUP BY 1,2,3,4,6,7,8,9,10 "

				     ."  UNION "

				     ." SELECT relfuecta as cta, relfuenat as nat, rencco as cco, relfueter as ter, sum(renvca) as val, '' as res, relfuenit as nit, '' as cem, '' as dre, '' as nre "
				     ."   FROM ".$wbasedato."_000078, tempo1 "
				     ."  WHERE fue    = relfuecod "
				     ."    AND relfuetem = tip "
				     ."    AND renest    = 'on' "
				     ."    AND tip       <> '01' "
				     ."    AND relfuenit = 'off' "             //Indica que No debe mostrar el tercero
				     ."  GROUP BY 1,2,3,4,7 "
				     ."  ORDER BY 1,2,3,4 ";
				  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

				  $q= " SELECT cta,nat,cco,ter,sum(val),res,nit,cem,dre,nre "
		             ."   FROM tempo2 "
		             ."  GROUP BY 1, 2, 3, 4, 6, 7, 8, 9, 10 "
		             ."  ORDER BY 1, 2, 3, 4 ";
		          $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		          $num = mysql_num_rows($res);

		          $wnat1="C";
	              $wnat2="D";
	             }
	         }

	         echo "<center><table>";

		     echo "<tr class=encabezadoTabla>";
	         echo "<th>CUENTA</th>";
			 echo "<th>NOMBRE</th>";
			 echo "<th>C.COSTO</th>";
			 echo "<th>NIT/CED</th>";
			 echo "<th>NOMBRE</th>";
			 echo "<th>DEBITOS</th>";
			 echo "<th>CREDITOS</th>";
			 echo "</tr>";

			 $wtotcomD=0;       //Total comprobante Debitos
		     $wtotcomC=0;       //Total comprobante Creditos



		     //================================================================================================================================
		     //================================================================================================================================
		     //ACA EVALUO SI SE QUIERE GRABAR EL COMPROBANTE
		     //================================================================================================================================
		     //================================================================================================================================
		     $wgraba="off";
		     $sw="off";
		     if (isset($wgra_com) and $wgra_com == "on")
			     {

				  $wgraba="on";

				  if ($wfec_com != "")
				     {
				      $wfec=explode("-",$wfec_com);

					  //Verifico si el periodo esta cerrado en la contabilidad de UNIX
					  $q = " SELECT count(*) "
					      ."   FROM sicie "
					      ."  WHERE cieapl = 'CONTAB' "
					      ."    AND cieanc = '".$wfec[0]."'"
					      ."    AND ciemes = '".$wfec[1]."'"
					      ."    AND ciefec <> '' ";
					  $resunix = odbc_do($conexunix,$q);
					  $wnumunix=odbc_result($resunix,1);

					  if ($wnumunix == 0)  //Indica que esta abierto por lo tanto se puede actualizar o grabar un comprobante nuevo
					     {
						  $wdoc_com="000000".$wdoc_com;

						  $q = " SELECT count(*) "
						      ."   FROM comovenc "
						      ."  WHERE movencfue = '".$wfue_com."'"
						      ."    AND movencdoc = '".$wdoc_com."'"
						      ."    AND movencano = '".$wfec[0]."'"
						      ."    AND movencmes = '".$wfec[1]."'"
						      ."    AND movencanu = '0' ";
						  $resunix = odbc_do($conexunix,$q);
						  $wexiste=odbc_result($resunix,1);

						  if ($wexiste > 0)  //Indica que ya existe y si tiene el indicador de reemplazar lo reemplaza
						     {
							  if (isset($wreem_com) and $wreem_com == "on")
							     {
								  //BORRO EL COMPROBANTE EXISTENTE
							      $q = "DELETE FROM comov WHERE movano = '".$wfec[0]."' and movmes = '".$wfec[1]."' and movfue = '".$wfue_com."' AND movdoc = '".$wdoc_com."'";
							      $resunix = odbc_do($conexunix,$q);

							      $q = "DELETE FROM comovenc WHERE movencano = '".$wfec[0]."' and movencmes = '".$wfec[1]."' and movencfue = '".$wfue_com."' AND movencdoc = '".$wdoc_com."'";
							      $resunix = odbc_do($conexunix,$q);

							      //GRABO EL COMPROBANTE
								  $q = "INSERT INTO comovenc(   movencano  ,   movencmes  ,   movencfue   ,   movencdoc   ,   movencusu   , movencanu) "
								      ."              VALUES('".$wfec[0]."','".$wfec[1]."','".$wfue_com."','".$wdoc_com."','".$wusuario."', '0' )   ";
								  $resunix = odbc_do($conexunix,$q);

								  $sw="on";
								 }
							    else
							       {
								    $wgraba="off";
								    $sw="off";
							        ?>
									  <script>
									    alert ("EL COMPROBANTE CON ESTOS PARAMETROS YA EXISTE Y NO SE SEŃALO REEMPLAZAR. NO SE ACTUALIZO LA INFORMACIÓN EN CONTABILIDAD");
							          </script>
									<?php
								   }
						     }
						    else  //SI NO EXISTE LO GRABO
						       {
							    $wfec=explode("-",$wfec_com);

								$q = "INSERT INTO comovenc(   movencano  ,   movencmes  ,   movencfue   ,   movencdoc   ,   movencusu   , movencanu) "
								    ."              VALUES('".$wfec[0]."','".$wfec[1]."','".$wfue_com."','".$wdoc_com."','".$wusuario."', '0' )   ";
								$resunix = odbc_do($conexunix,$q);

								$sw="on";
							   }
					     }
					    else
					       {
						    ?>
							  <script>
							    alert ("EL PERIODO CONTABLE ESTA CERRADO EN CONTABILIDAD");
					          </script>
							<?php
						   }
				  	 }
				  	else
				  	   {
					    ?>
						  <script>
						    alert ("NO SE DIGITO FECHA DEL COMPROBANTE, el comprobante no ha sido grabado");
				          </script>
						<?php
					   }
				 }
		     //================================================================================================================================
		     //================================================================================================================================


		     $witem=0;
		     $witem=$witem+$num;

		     ver_comprobante($num,$res,$witem,$wgraba);

		     //=========================================================================================================================================================================
		     //=========================================================================================================================================================================
		     //CON ESTE PROCEDIMIENTO VERIFICO QUE LA RELACION CONCEPTO, CCO Y TIPO DE EMPRESA EXISTAN PARA CADA UNA DE LAS FACTURAS
		     //Y POR CADA CONCEPTO-CENTRO DE COSTOS DE LA FACTURA.
		     //=========================================================================================================================================================================
					// Borra la tabla temporal tempo8
					$qdel = "	DROP TABLE IF EXISTS tempo8 ";
					$resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());

					 $q= " CREATE TABLE IF NOT EXISTS tempo8 AS "
						." SELECT fue, doc, fdecon, fdecco, tip, cem, dre, nre, fdeter, fdevco, fdeviv, fdepte, fdefue, fdevde "
						."   FROM ".$wbasedato."_000065 a, tempo1  b" //.$wbasedato."_000004 "
						."  WHERE fdefue  = fue "
						//."    AND fenfec between '".$wfec_i."' AND '".$wfec_f."'"
						."	  AND a.fecha_data = b.fecha_data"
						."    AND fdedoc  = doc "
						."    AND fdeest  = 'on' ";
					 $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

			      //Se le crea un indice a la temporal
			      $q = " CREATE INDEX fdedoc ON tempo8 (fue(4), doc, fdecon(10), fdecco(6), fdeter)";
			      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

			      $q = " CREATE INDEX idxfue ON tempo8 (fue(4))";
			      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		     //=========================================================================================================================================================================
		     $q= " SELECT fue, doc, fdecon, fdecco, tip, cem, dre, nre "
		        ."   FROM tempo8 ";
		     $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		     $num = mysql_num_rows($res);

		     if ($num > 0)
		        {
			     for ($i=1;$i<=$num;$i++)
			         {
			          $row = mysql_fetch_array($res);

			          $q = " SELECT count(*) "
			              ."   FROM ".$wbasedato."_000077 "
			              ."  WHERE  relconcon = '".$row[2]."'"
			              ."    AND  relconcco = '".$row[3]."'"
			              ."    AND (relcontem = '".$row[4]."'"
			              ."     OR  relcontem = '*') "
			              ."    AND  relconest = 'on' ";
			          $res_exi = mysql_query($q,$conex);
			          $row_exi = mysql_fetch_array($res_exi);

			          if ($row_exi[0] == 0)
			             {
				          echo "Falta relación del concepto: ".$row[2]." con el centro de costo: ".$row[3]." y el tipo de empresa: ".$row[4]." para la factura: ".$row[0]."-".$row[1]."<br>";
		                 }
			         }
		        }
		     //=========================================================================================================================================================================

		     //Busco si la fuente es de notas debito
		     $q = " SELECT carndb "
		         ."   FROM ".$wbasedato."_000040 "
		         ."  WHERE carfue = '".$wfue[0]."'"
		         ."    AND carest = 'on' ";
		     $res_exi = mysql_query($q,$conex);
			 $row_ndb = mysql_fetch_array($res_exi);
			 if ($row_ndb[0]=='on')   //CAMBIO LA NATURALEZA PARA LOS CONCEPTOS DE FACTURACION-- CUANDO LA FUENTE A GENERAR ES DE ** NOTAS DEBITO **
		        {
			     $wnat1="D";
                 $wnat2="C";
                }

             if ($num > 0)
		     	{
			     //===========================================================================
				 //Con este query traigo el comprobante resumido por concepto de facturacion
				 //para las cuentas de ** INGRESOS POR CONCEPTO **.
				// Borra la tabla temporal tempo2
				 $qdel = "	DROP TABLE IF EXISTS tempo3 ";
				 $resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());

				 $q=  " CREATE TABLE IF NOT EXISTS tempo3 AS "
				     ." SELECT relconcin as cin, '".$wnat2."' as nat, fdecco as cco, fdeter as ter, SUM(ROUND(fdevco*((100-fdepte)/100))-fdeviv) as val, '' as res, relconnit as nit, cem, dre, nre "
				     ."   FROM tempo8, ".$wbasedato."_000077, ".$wbasedato."_000040 "
				     ."  WHERE fdecon    = relconcon "
				     ."    AND fdecco    = relconcco "
				     ."    AND relcontem in ("."tip".",'*') "
				     ."    AND relconest = 'on'"    // se adiciona 2009-02-12  para saber el estado en la tabla 77
				     ."    AND tip       = '01' "
				     ."    AND relconnit = 'on' "
				     ."    AND fdefue    = carfue "
				     ."    AND carcfa    = 'on' "
				     ."  GROUP BY 1,2,3,4,6,7,8,9,10 "

				     ." UNION "

				     /*
				     /////
				     ."   FROM ".$wbasedato."_000065, ".$wbasedato."_000077, ".$wbasedato."_000018 "
					     ."  WHERE fdefue    = '".$rowdoc[0]."'"
					     ."    AND fdedoc    = '".$rowdoc[1]."'"
					     ."    AND fdecon    = relconcon "
					     ."    AND fdecco    = relconcco "
					     ."    AND relcontem = '".$rowdoc[2]."'"
					     ."    AND relconest = 'on'"    // se adiciona 2009-02-12  para saber el estado en la tabla 77
					     ."    AND fdeest    = 'on' "
					     ."    AND fdefue    = fenffa "
					     ."    AND fdedoc    = fenfac "
					     ."  GROUP BY 1,2,3,4,5,6,8,9,10,11 ";

				     ////
				     */

				     ." SELECT relconcin as cin, '".$wnat2."' as nat, fdecco as cco, relconter as ter, SUM(ROUND(fdevco*((100-fdepte)/100))-fdeviv) as val, '' as res, relconnit as nit , '' as cem, '' as dre, '' as nre "
				     ."   FROM tempo8, ".$wbasedato."_000077, ".$wbasedato."_000040 "
				     ."  WHERE fdecon    = relconcon "
				     ."    AND fdecco    = relconcco "
				     ."    AND relcontem in ("."tip".",'*') "
				     ."    AND relconest = 'on'"    // se adiciona 2009-02-12  para saber el estado en la tabla 77
				     ."    AND tip       = '01' "
				     ."    AND relconnit = 'off' "
				     ."    AND fdefue    = carfue "
				     ."    AND carcfa    = 'on' "
				     //."  GROUP BY 1,2,3,4,7 "
				     ."  GROUP BY 1,2,3,4,6,7,8,9,10 "

				     /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

				     ." UNION "

				     ." SELECT relconcin as cin, '".$wnat2."' as nat, fdecco as cco, relconter as ter, SUM(ROUND(fdevco*((100-fdepte)/100))-fdeviv) as val, '' as res, relconnit as nit , '' as cem, '' as dre, '' as nre "
				     ."   FROM tempo8, ".$wbasedato."_000077, ".$wbasedato."_000040 "
				     ."  WHERE fdecon    = relconcon "
				     ."    AND fdecco    = relconcco "
				     ."    AND relcontem in ("."tip".",'*') "
				     ."    AND relconest = 'on'"    // se adiciona 2009-02-12  para saber el estado en la tabla 77
				     ."    AND tip       <> '01' "
				     ."    AND fdeter    <> '' "
				     ."    AND relconnit = 'on' "
				     ."    AND fdefue    = carfue "
				     ."    AND carcfa    = 'on' "
				     //."  GROUP BY 1,2,3,4,7 "
				     ."  GROUP BY 1,2,3,4,6,7,8,9,10 "

				     ." UNION "

				     ." SELECT relconcin as cin, '".$wnat2."' as nat, fdecco as cco, relconter as ter, SUM(ROUND(fdevco*((100-fdepte)/100))-fdeviv) as val, '' as res, relconnit as nit , '' as cem, '' as dre, '' as nre "
				     ."   FROM tempo8, ".$wbasedato."_000077, ".$wbasedato."_000040 "
				     ."  WHERE fdecon    = relconcon "
				     ."    AND fdecco    = relconcco "
				     ."    AND relcontem in ("."tip".",'*') "
				     ."    AND relconest = 'on'"    // se adiciona 2009-02-12  para saber el estado en la tabla 77
				     ."    AND tip       <> '01' "
				     ."    AND fdeter    <> '' "
				     ."    AND relconnit = 'off' "
				     ."    AND fdefue    = carfue "
				     ."    AND carcfa    = 'on' "
				     //."  GROUP BY 1,2,3,4,7 "
				     ."  GROUP BY 1,2,3,4,6,7,8,9,10 "

				     /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

				     ." UNION "

				     ." SELECT relconcin as cin, '".$wnat2."' as nat, fdecco as cco, relconter as ter, SUM(ROUND(fdevco*((100-fdepte)/100))-fdeviv) as val, '' as res, relconnit as nit , '' as cem, '' as dre, '' as nre "
				     ."   FROM tempo8, ".$wbasedato."_000077, ".$wbasedato."_000040 "
				     ."  WHERE fdecon    = relconcon "
				     ."    AND fdecco    = relconcco "
				     ."    AND relcontem in ("."tip".",'*') "
				     ."    AND relconest = 'on'"    // se adiciona 2009-02-12  para saber el estado en la tabla 77
				     ."    AND tip       <> '01' "
				     ."    AND fdeter    = '' "
				     ."    AND relconnit = 'on' "
				     ."    AND fdefue    = carfue "
				     ."    AND carcfa    = 'on' "
				     //."  GROUP BY 1,2,3,4,7 "
				     ."  GROUP BY 1,2,3,4,6,7,8,9,10 "

				     ." UNION "

				     ." SELECT relconcin as cin, '".$wnat2."' as nat, fdecco as cco, relconter as ter, SUM(ROUND(fdevco*((100-fdepte)/100))-fdeviv) as val, '' as res, relconnit as nit , '' as cem, '' as dre, '' as nre "
				     ."   FROM tempo8, ".$wbasedato."_000077, ".$wbasedato."_000040 "
				     ."  WHERE fdecon    = relconcon "
				     ."    AND fdecco    = relconcco "
				     ."    AND relcontem in ("."tip".",'*') "
				     ."    AND relconest = 'on'"    // se adiciona 2009-02-12  para saber el estado en la tabla 77
				     ."    AND tip       <> '01' "
				     ."    AND fdeter    = '' "
				     ."    AND relconnit = 'off' "
				     ."    AND fdefue    = carfue "
				     ."    AND carcfa    = 'on' "
				     //."  GROUP BY 1,2,3,4,7"
				     ."  GROUP BY 1,2,3,4,6,7,8,9,10 "
				     ."  ORDER BY 1,2,3,4 ";
				 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

				 $q = " SELECT * "
				     ."   FROM tempo3 ";
				 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				 $num = mysql_num_rows($res);


				 if ($num > 0)
				    {
					 $q= " SELECT cin,nat,cco,ter,sum(val),res,nit,cem,dre,nre "
			             ."  FROM tempo3 "
			             ." GROUP BY 1, 2, 3, 4, 6, 7, 8, 9, 10 ";
			         $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			         $num = mysql_num_rows($res);

			         $witem=$witem+$num;

			         if ($row_ndb[0] != 'on')   //Si no es Nota Debito entra
			            ver_comprobante($num,$res,$witem,$wgraba);

					// Borra la tabla temporal tempo1
					$qdel = "	DROP TABLE IF EXISTS tempo4 ";
					$resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());

			         //DESCUENTO DE INGRESOS
				     $q=  " CREATE TABLE IF NOT EXISTS tempo4 AS "
				         //." SELECT relconcdi as cdi, '".$wnat1."' as nat, fdecco as cco, fdeter as ter, sum(round(fdevde*((100-fdepte)/100))) as val, '' as res, relconnit as nit, cem, dre, nre "
				         ." SELECT relconcdi as cdi, '".$wnat1."' as nat, fdecco as cco, fdeter as ter, SUM(ROUND((fdevde*(100-fdepte)/100)/(1+(fdeviv/(fdevco-fdeviv))))) as val, '' as res, relconnit as nit, cem, dre, nre "
					     ."   FROM tempo8, ".$wbasedato."_000077, ".$wbasedato."_000040 "
					     ."  WHERE fdecon    = relconcon "
					     ."    AND fdecco    = relconcco "
					     ."    AND relcontem in ("."tip".",'*') "
					     ."    AND relconest = 'on'"    // se adiciona 2009-02-12  para saber el estado en la tabla 77
					     ."    AND tip       = '01' "
					     ."    AND relconnit = 'on' "
					     ."    AND fdefue    = carfue "
					     ."    AND carcfa    = 'on' "
					     ."  GROUP BY 1,2,3,4,6,7,8,9,10 "

					     ." UNION "

					     //." SELECT relconcdi as cdi, '".$wnat1."' as nat, fdecco as cco, relconter as ter, sum(round(fdevde*((100-fdepte)/100))) as val, '' as res, relconnit as nit, '' as cem, '' as dre, '' as nre"
					     ." SELECT relconcdi as cdi, '".$wnat1."' as nat, fdecco as cco, relconter as ter, SUM(ROUND((fdevde*(100-fdepte)/100)/(1+(fdeviv/(fdevco-fdeviv))))) as val, '' as res, relconnit as nit, '' as cem, '' as dre, '' as nre"
					     ."   FROM tempo8, ".$wbasedato."_000077, ".$wbasedato."_000040 "
				         ."  WHERE fdecon    = relconcon "
					     ."    AND fdecco    = relconcco "
					     ."    AND relcontem in ("."tip".",'*') "
					     ."    AND relconest = 'on'"    // se adiciona 2009-02-12  para saber el estado en la tabla 77
					     ."    AND tip       = '01' "
					     ."    AND relconnit = 'off' "
					     ."    AND fdefue    = carfue "
					     ."    AND carcfa    = 'on' "
					     ."  GROUP BY 1,2,3,4,7 "

					     ." UNION "

					     ." SELECT relconcdi as cdi, '".$wnat1."' as nat, fdecco as cco, relconter as ter, SUM(CEIL((fdevde*((100-fdepte)/100))/(1+(fdeviv/(fdevco-fdeviv))))) as val, '' as res, relconnit as nit, '' as cem, '' as dre, '' as nre"
					     ."   FROM tempo8, ".$wbasedato."_000077, ".$wbasedato."_000040 "
				         ."  WHERE fdecon    = relconcon "
					     ."    AND fdecco    = relconcco "
					     ."    AND relcontem in ("."tip".",'*') "
					     ."    AND relconest = 'on'"    // se adiciona 2009-02-12  para saber el estado en la tabla 77
					     ."    AND tip       <> '01' "
					     ."    AND relconnit = 'on' "
					     ."    AND fdefue    = carfue "
					     ."    AND carcfa    = 'on' "
					     ."  GROUP BY 1,2,3,4,7 "

					     ." UNION "

					     ." SELECT relconcdi as cdi, '".$wnat1."' as nat, fdecco as cco, relconter as ter, SUM(CEIL((fdevde*((100-fdepte)/100))/(1+(fdeviv/(fdevco-fdeviv))))) as val, '' as res, relconnit as nit, '' as cem, '' as dre, '' as nre"
					     ."   FROM tempo8, ".$wbasedato."_000077, ".$wbasedato."_000040 "
				         ."  WHERE fdecon    = relconcon "
					     ."    AND fdecco    = relconcco "
					     ."    AND relcontem in ("."tip".",'*') "
					     ."    AND relconest = 'on'"    // se adiciona 2009-02-12  para saber el estado en la tabla 77
					     ."    AND tip       <> '01' "
					     ."    AND relconnit = 'off' "
					     ."    AND fdefue    = carfue "
					     ."    AND carcfa    = 'on' "
					     ."  GROUP BY 1,2,3,4,7 "
					     ."  ORDER BY 1,2,3,4 ";
					 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

					 $q= " SELECT cdi,nat,cco,ter,sum(val),res,nit,cem,dre,nre "
			             ."  FROM tempo4 "
			             ." GROUP BY 1, 2, 3, 4, 6, 7, 8, 9, 10 ";
			         $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			         $num = mysql_num_rows($res);

			         $witem=$witem+$num;
				     if ($row_ndb[0] != 'on')   //Si no es Nota Debito entra
				        ver_comprobante($num,$res,$witem,$wgraba);

					// Borra la tabla temporal tempo2
					$qdel = "	DROP TABLE IF EXISTS tempo5 ";
					$resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());

				     //===========================================================================
					 //Con este query traigo el comprobante resumido por concepto de facturacion
					 //para las cuentas de ** TERCEROS POR CADA CONCEPTO **.
				     $q=   " CREATE TABLE IF NOT EXISTS tempo5 AS "
				         ." SELECT relconcte as cte, '".$wnat2."' as nat, fdecco as cco, fdeter as ter, sum(round(fdevco*(fdepte/100))) as val, '' as res, relconnit as nit, cem, dre, nre "
					     ."   FROM tempo8, ".$wbasedato."_000077, ".$wbasedato."_000040 "
				         ."  WHERE fdecon    = relconcon "
					     ."    AND fdecco    = relconcco "
					     ."    AND relcontem in ("."tip".",'*') "
					     ."    AND relconest = 'on'"    // se adiciona 2009-02-12  para saber el estado en la tabla 77
					     ."    AND tip       = '01' "
					     ."    AND relconnit = 'on' "
					     ."    AND fdefue    = carfue "
					     ."    AND carcfa    = 'on' "
					     ."  GROUP BY 1,2,3,4,6,7,8,9,10 "

					     ." UNION "

					     ." SELECT relconcte as cte, '".$wnat2."' as nat, fdecco as cco, relconter as ter, sum(round(fdevco*(fdepte/100))) as val, '' as res, relconnit as nit, '' as cem, '' as dre, '' as nre"
					     ."   FROM tempo8, ".$wbasedato."_000077, ".$wbasedato."_000040 "
				         ."  WHERE fdecon    = relconcon "
					     ."    AND fdecco    = relconcco "
					     ."    AND relcontem in ("."tip".",'*') "
					     ."    AND relconest = 'on'"    // se adiciona 2009-02-12  para saber el estado en la tabla 77
					     ."    AND tip       = '01' "
					     ."    AND relconnit = 'off' "
					     ."    AND fdefue    = carfue "
					     ."    AND carcfa    = 'on' "
					     ."  GROUP BY 1,2,3,4,7 "

					     ." UNION "

					     ." SELECT relconcte as cte, '".$wnat2."' as nat, fdecco as cco, relconter as ter, sum(round(fdevco*(fdepte/100))) as val, '' as res, relconnit as nit, '' as cem, '' as dre, '' as nre"
					     ."   FROM tempo8, ".$wbasedato."_000077, ".$wbasedato."_000040 "
				         ."  WHERE fdecon    = relconcon "
					     ."    AND fdecco    = relconcco "
					     ."    AND relcontem in ("."tip".",'*') "
					     ."    AND relconest = 'on'"    // se adiciona 2009-02-12  para saber el estado en la tabla 77
					     ."    AND tip      <> '01' "
					     ."    AND relconnit = 'on' "
					     ."    AND fdefue    = carfue "
					     ."    AND carcfa    = 'on' "
					     ."  GROUP BY 1,2,3,4,7 "

					     ." UNION "

					     ." SELECT relconcte as cte, '".$wnat2."' as nat, fdecco as cco, relconter as ter, sum(round(fdevco*(fdepte/100))) as val, '' as res, relconnit as nit, '' as cem, '' as dre, '' as nre"
					     ."   FROM tempo8, ".$wbasedato."_000077, ".$wbasedato."_000040 "
				         ."  WHERE fdecon    = relconcon "
					     ."    AND fdecco    = relconcco "
					     ."    AND relcontem in ("."tip".",'*') "
					     ."    AND relconest = 'on'"    // se adiciona 2009-02-12  para saber el estado en la tabla 77
					     ."    AND tip      <> '01' "
					     ."    AND relconnit = 'off' "
					     ."    AND fdefue    = carfue "
					     ."    AND carcfa    = 'on' "
					     ."  GROUP BY 1,2,3,4,7 "
					     ."  ORDER BY 1,2,3,4 ";
					 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

				     $q= " SELECT cte,nat,cco,ter,sum(val),res,nit,cem,dre,nre "
			             ."  FROM tempo5 "
			             ." GROUP BY 1, 2, 3, 4, 6, 7, 8, 9, 10 ";
			         $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			         $num = mysql_num_rows($res);

			         $witem=$witem+$num;
				     if ($row_ndb[0] != 'on')   //Si no es Nota Debito entra
				        ver_comprobante($num,$res,$witem,$wgraba);

					// Borra la tabla temporal tempo2
					$qdel = "	DROP TABLE IF EXISTS tempo6 ";
					$resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());

				     //===========================================================================
					 //Con este query traigo el comprobante resumido por concepto de facturacion
					 //para las cuentas de ** DESCUENTO DE TERCEROS **.
				     $q=   " CREATE TABLE IF NOT EXISTS tempo6 AS "
				         ." SELECT relconcdt as cdt, '".$wnat1."' as nat, fdecco as cco, fdeter as ter, sum(round(fdevde*(fdepte/100))) as val, '' as res, relconnit as nit, cem, dre, nre "
					     ."   FROM tempo8, ".$wbasedato."_000077, ".$wbasedato."_000040 "
				         ."  WHERE fdecon    = relconcon "
					     ."    AND fdecco    = relconcco "
					     ."    AND relcontem in ("."tip".",'*') "
					     ."    AND relconest = 'on'"    // se adiciona 2009-02-12  para saber el estado en la tabla 77
					     ."    AND tip       = '01' "
					     ."    AND relconnit = 'on' "
					     ."    AND fdefue    = carfue "
					     ."    AND carcfa    = 'on' "
					     ."  GROUP BY 1,2,3,4,6,7,8,9,10 "

					     ." UNION "

					     ." SELECT relconcdt as cdt, '".$wnat1."' as nat, fdecco as cco, relconter as ter, sum(round(fdevde*(fdepte/100))) as val, '' as res, relconnit as nit, '' as cem, '' as dre, '' as nre "
					     ."   FROM tempo8, ".$wbasedato."_000077, ".$wbasedato."_000040 "
				         ."  WHERE fdecon    = relconcon "
					     ."    AND fdecco    = relconcco "
					     ."    AND relcontem in ("."tip".",'*') "
					     ."    AND relconest = 'on'"    // se adiciona 2009-02-12  para saber el estado en la tabla 77
					     ."    AND tip       = '01' "
					     ."    AND relconnit = 'off' "
					     ."    AND fdefue    = carfue "
					     ."    AND carcfa    = 'on' "
					     ."  GROUP BY 1,2,3,4,7 "

					     ." UNION "

					     ." SELECT relconcdt as cdt, '".$wnat1."' as nat, fdecco as cco, relconter as ter, sum(round(fdevde*(fdepte/100))) as val, '' as res, relconnit as nit, '' as cem, '' as dre, '' as nre "
					     ."   FROM tempo8, ".$wbasedato."_000077, ".$wbasedato."_000040 "
				         ."  WHERE fdecon    = relconcon "
					     ."    AND fdecco    = relconcco "
					     ."    AND relcontem in ("."tip".",'*') "
					     ."    AND relconest = 'on'"    // se adiciona 2009-02-12  para saber el estado en la tabla 77
					     ."    AND tip      <> '01' "
					     ."    AND relconnit = 'on' "
					     ."    AND fdefue    = carfue "
					     ."    AND carcfa    = 'on' "
					     ."  GROUP BY 1,2,3,4,7 "

					     ." UNION "

					     ." SELECT relconcdt as cdt, '".$wnat1."' as nat, fdecco as cco, relconter as ter, sum(round(fdevde*(fdepte/100))) as val, '' as res, relconnit as nit, '' as cem, '' as dre, '' as nre "
					     ."   FROM tempo8, ".$wbasedato."_000077, ".$wbasedato."_000040 "
				         ."  WHERE fdecon    = relconcon "
					     ."    AND fdecco    = relconcco "
					     ."    AND relcontem in ("."tip".",'*') "
					     ."    AND relconest = 'on'"    // se adiciona 2009-02-12  para saber el estado en la tabla 77
					     ."    AND tip      <> '01' "
					     ."    AND relconnit = 'off' "
					     ."    AND fdefue    = carfue "
					     ."    AND carcfa    = 'on' "
					     ."  GROUP BY 1,2,3,4,7 "
					     ."  ORDER BY 1,2,3,4 ";
		             $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

				     $q= " SELECT cdt,nat,cco,ter,sum(val),res,nit,cem,dre,nre "
			             ."  FROM tempo6 "
			             ." GROUP BY 1, 2, 3, 4, 6, 7, 8, 9, 10 ";
			         $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			         $num = mysql_num_rows($res);

			         $witem=$witem+$num;


			         if ($row_ndb[0] != 'on')   //Si no es Nota Debito entra
				        ver_comprobante($num,$res,$witem,$wgraba);


				     //===========================================================================
					 //Con este query traigo el comprobante resumido por concepto de facturacion
					 //para las cuentas de **** IVA ****.
				     //$q=  " SELECT relconciv, '".$wnat2."', '', '', sum(fdeviv-(floor((round((fdevde/(1+(round(fdeviv/(fdevco-fdeviv))))),2)/(fdevco-fdeviv))*fdeviv))),'', '', '', '', '' "
				                                                              //SUM(fdeviv*(1-(fdevde/fdevco)))
				     $q=  " SELECT relconciv, '".$wnat2."', '', '', SUM(fdeviv*(1-(fdevde/fdevco))),'', '', '', '', '' "
					     ."   FROM tempo8, ".$wbasedato."_000077, ".$wbasedato."_000004 "
					     ."  WHERE fdecon           = relconcon "
					     ."    AND fdecco           = relconcco "
					     ."    AND relcontem        = tip"
					     ."    AND relcontem       != '01' "
					     ."    AND relconest        = 'on' "
					     ."    AND fdecon           = relconcon "
					     ."    AND relconcon        = grucod "
					     ."    AND gruabo          != 'on' "
					     ."    AND trim(relconciv) != '' "
					     ."    AND relconciv       != 'NO APLICA' "
					     ."  GROUP BY 1 "
					     ."  UNION "
					     //." SELECT relconciv, '".$wnat2."', '', '', sum(fdeviv-(floor((fdevde/(fdevco-fdeviv))*fdeviv))),'', '', '', '', '' "
					     ." SELECT relconciv, '".$wnat2."', '', '', SUM(fdeviv*(1-(fdevde/fdevco))),'', '', '', '', '' "
					     ."   FROM tempo8, ".$wbasedato."_000077, ".$wbasedato."_000004 "
					     ."  WHERE fdecon           = relconcon "
					     ."    AND fdecco           = relconcco "
					     ."    AND relcontem        = tip"
					     ."    AND relcontem        = '01' "
					     ."    AND relconest        = 'on' "
					     ."    AND fdecon           = relconcon "
					     ."    AND relconcon        = grucod "
					     ."    AND gruabo          != 'on' "
					     ."    AND trim(relconciv) != '' "
					     ."    AND relconciv       != 'NO APLICA' "
					     ."  GROUP BY 1 "    ;
					 $res = mysql_query($q,$conex);
				     $num = mysql_num_rows($res);

				     ver_comprobante($num,$res,$witem,$wgraba);
				}
	         }  //Fin del if ($num>0) de la linea 858

	         if ($numfue > 0 and ($rowfue[0] == "on" or $rowfue[1] == "on"))    //COMPROBANTE DE NOTAS
	            {
		         if ($row_ndb[0]=='on')   //** VUELVO ** CAMBIO LA NATURALEZA PARA LOS CONCEPTOS DE FACTURACION DE LAS NOTAS DEBITO
			        {
				     $wnat1="C";
	                 $wnat2="D";
                    }
				 //===========================================================================
				 //Con este query traigo el comprobante resumido por concepto de cartera
				 //para las cuentas de ** CONCEPTOS DE CARTERA **.
				 $q=  " SELECT concue, connat, rdecco, conter, sum(rdevco), '', connit, fencod, fendpa, fennpa "
				     ."   FROM ".$wbasedato."_000020,".$wbasedato."_000021, ".$wbasedato."_000044, tempo1,".$wbasedato."_000018 "
				     ."  WHERE rdefue = fue "
					 ."    AND fenfec between '".$wfec_i."' AND '".$wfec_f."'"
				     ."    AND rdenum = doc "
				     ."    AND mid(rdecon,1,instr(rdecon,'-')-1) = concod "
				     ."    AND rdefue = confue "
				     ."    AND rdeest = 'on' "
				     ."    AND conest = 'on' "
				     ."    AND rdefue = renfue "
				     ."    AND rdenum = rennum "
				     ."    AND rdeffa = fenffa "
				     ."    AND rdefac = fenfac "
				     ."    AND fencod = '01' "
				     //."  GROUP BY 1,2,3,4,6,7,8,9,10 "
					 ."  GROUP BY 1,2,3,4,6,7,9,10 "
				     ."  UNION "
				     ." SELECT concue, connat, rdecco, conter, sum(rdevco), '', connit, fencod, empnit, empnom "
				     ."   FROM ".$wbasedato."_000020,".$wbasedato."_000021, ".$wbasedato."_000044, tempo1,".$wbasedato."_000024,".$wbasedato."_000018 "
				     ."  WHERE rdefue  = fue "
					 ."    AND fenfec between '".$wfec_i."' AND '".$wfec_f."'"
				     ."    AND rdenum  = doc "
				     ."    AND mid(rdecon,1,instr(rdecon,'-')-1) = concod "
				     ."    AND rdefue  = confue "
				     ."    AND rdeest  = 'on' "
				     ."    AND conest  = 'on' "
				     ."    AND rdefue  = renfue "
				     ."    AND rdenum  = rennum "
				     ."    AND rdeffa  = fenffa "
				     ."    AND rdefac  = fenfac "
				     ."    AND fencod  = empcod "
				     ."    AND fenres  = empres "
				     ."    AND fencod != '01' "
				     //."  GROUP BY 1,2,3,4,6,7,8,9,10 ";
					 ."  GROUP BY 1,2,3,4,6,7,9,10 ";
		         $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				 $num = mysql_num_rows($res);

				 $witem=$witem+$num;
				 ver_comprobante($num,$res,$witem,$wgraba);
				}


	          if ($wtotcomD!=$wtotcomC)
		         $wcolor="CC3300";
		        else
		          $wcolor="";



		      echo "<tr class=encabezadoTabla>";
		      echo "<td colspan=5><b>Total Comprobante ".$wfuecartera."</b></td>";
		      echo "<td align=right bgcolor=".$wcolor."><b>".number_format($wtotcomD,0,'.',',')."</b></td>";
		      echo "<td align=right bgcolor=".$wcolor."><b>".number_format($wtotcomC,0,'.',',')."</b></td>";
		      echo "</tr>";

	          if ($sw == "on")
	             {
		          ?>
				    <script>
				      alert ("EL COMPROBANTE FUE GRABADO OK");
		            </script>
			      <?php
		         }

		 //   }   ////On temporal fin del FOR

	    }
  echo "</table>";

  echo "<center><table>";
  echo "<tr><td><input type='submit' value='OK'>&nbsp|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:window.close();'></td></tr>";
  echo "</table>";
  
	odbc_close($conexunix);
	odbc_close_all();
}
?>