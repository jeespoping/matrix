<head>
  <title>RECIBO MEDICAMENTOS Y MATERIAL GRABADOS A LOS PACIENTES</title>
</head>
<body BACKGROUND="nubes.gif">
<?php
include_once("conex.php");
  /**********************************************************
   * RECIBIR LOS CARGOS DE MEDICAMENTOS Y MATERIAL GRABADOS *
   *        EN LA UNIDAD DE SERVICIOS FARMACEUTICOS         *
   *     				CONEX, FREE => OK				    *
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
  $wactualiz="(Versión Enero 27 de 2005)";                     // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                                           // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //

  echo "<br>";
  echo "<br>";

  echo "<form action='Tras_Paci_detalle.php' method=post>";
  echo "<center><table border=2 width=400 BACKGROUND=.'nubes.gif'>";
  echo "<tr><td align=center colspan=240 bgcolor=#fffffff><font size=6 text color=#CC0000><b>CLINICA LAS AMERICAS</b></font></td></tr>";
  echo "<tr><td align=center colspan=240 bgcolor=#fffffff><font size=4 text color=#CC0000><b>MEDICAMENTOS Y MATERIAL GRABADO A PACIENTES PENDIENTE DE RECIBO</b></font></td></tr>";
  echo "<tr><td align=center colspan=240 bgcolor=#fffffff><font size=4 text color=#CC0000><b>RELACION DE DOCUMENTOS POR PACIENTE</b></font></td></tr>";
  echo "<tr><td align=center colspan=240 bgcolor=#fffffff><font size=3 text color=#CC0000><b>".$wactualiz."</b></font></td></tr>";


  if(!isset($wanoi) or !isset($wmesi) or !isset($wdiai) or !isset($wanof) or !isset($wmesf) or !isset($wdiaf) or !isset($wcco))
    {

	 //$fecha = date("Y-m-d");
	 $wano = date("Y");
	 $wmes = date("m");
	 $wdia = date("d");

	 //FECHA INICIAL
	 echo "<tr>";
	 echo "<td align=center colspan=3 bgcolor=#66CC99><font size=4><b>FECHA INICIAL</b></font></td>";
	 echo "<td align=center colspan=3 bgcolor=#66CC99><font size=4><b>FECHA FINAL</b></font></td>";
	 echo "</tr>";

	 //AÑO INICIAL
     echo "<td bgcolor=#cccccc ><font size=4><b>Año:</b></font><select name='wanoi'>";
     for($f=2005;$f<2051;$f++)
       {
        if ($f == $wano)
           echo "<option selected>".$f."</option>";
          else
             echo "<option>".$f."</option>";
       }
	   echo "</select>";


	 //MES INICIAL
     echo "<td bgcolor=#cccccc ><font size=4><b>Mes :</b></font><select name='wmesi'>";
     for($f=1;$f<13;$f++)
       {
        if ($f == $wmes)
           echo "<option selected>".$f."</option>";
          else
             echo "<option>".$f."</option>";
	   }
	   echo "</select>";


     //DIA INICIAL
     echo "<td bgcolor=#cccccc ><font size=4><b>Dia :</b></font><select name='wdiai'>";
     for($f=1;$f<32;$f++)
       {
	    if ($f == $wdia)
           echo "<option selected>".$f."</option>";
          else
             echo "<option>".$f."</option>";
       }
	   echo "</td></select></td>";


	 //AÑO FINAL
     echo "<td bgcolor=#cccccc ><font size=4><b>Año:</b></font><select name='wanof'>";
     for($f=2005;$f<2051;$f++)
       {
        if($f == $wano)
          echo "<option selected>".$f."</option>";
         else
            echo "<option>".$f."</option>";
       }
	   echo "</select>";

	 //MES FINAL
     echo "<td bgcolor=#cccccc ><font size=4><b>Mes :</b></font><select name='wmesf'>";
     for($f=1;$f<13;$f++)
       {
        if($f == $wmes)
           echo "<option selected>".$f."</option>";
          else
             echo "<option>".$f."</option>";
	   }
	   echo "</select>";

     //DIA FINAL
     echo "<td bgcolor=#cccccc ><font size=4><b>Dia :</b></font><select name='wdiaf'>";
     for($f=1;$f<32;$f++)
       {
	    if ($f == $wdia)
           echo "<option selected>".$f."</option>";
          else
             echo "<option>".$f."</option>";
       }
	   echo "</td></select></td></tr>";


	 //CENTROS DE COSTO
	 echo "<center><td bgcolor=#cccccc colspan = 6><font size=4><b>Centro de Costo :</b></font><select name='wcco'>";
	 $query = " SELECT ccocod, cconom "
             ."   FROM cocco "
             ."  ORDER BY ccocod ";

     $res = odbc_do($conexunix,$query);

     echo "<option selected>*- Todos los centros de costo </option>";
	 while(odbc_fetch_row($res))
	     {
	      echo "<option value>".odbc_result($res,1)."-".odbc_result($res,2)."</option>";
	     }
	 echo "</SELECT></td></tr></table><br><br>";

     echo"<tr><td align=center bgcolor=#cccccc colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";
    }
   else
      /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      // Ya estan todos los campos setiados o iniciados ===================================================================================
      /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      {
	   //FECHA INICIAL Y FINAL
	   echo "<tr>";
	   echo "<td colspan=2 align=center bgcolor=#66CC99><font size=4><b>Fecha Inicial : : ".$wanoi."/".$wmesi."/".$wdiai."</b></font></td>";
	   echo "<td colspan=3 align=center bgcolor=#66CC99><font size=4><b>Fecha Final : ".$wanof."/".$wmesf."/".$wdiaf."</b></font></td>";
	   echo "</tr>";


	   //AÑO INICIAL
       echo "<input type='HIDDEN' name= 'wano' value='".$wanoi."'>";
       //MES INICIAL
       echo "<input type='HIDDEN' name= 'wmes' value='".$wmesi."'>";
	   //DIA INICIAL
       echo "<input type='HIDDEN' name= 'wdia' value='".$wdiai."'>";
       //AÑO FINAL
       echo "<input type='HIDDEN' name= 'wano' value='".$wanof."'>";
       //MES FINAL
       echo "<input type='HIDDEN' name= 'wmes' value='".$wmesf."'>";
	   //DIA FINAL
       echo "<input type='HIDDEN' name= 'wdia' value='".$wdiaf."'>";
       //CENTRO DE COSTO
       echo "<td colspan=1 align=center bgcolor=#66CC99><b>Centro de Costo: ".$wcco."</b></td>";
       echo "<input type='HIDDEN' name= 'wcco' value='".$wcco."'>";
       //HABITACION
       echo "<td colspan=1 align=center bgcolor=#66CC99><b>Habitación: ".$whab."</b></td>";
       echo "<input type='HIDDEN' name= 'whab' value='".$whab."'>";
       //HISTORIA
       echo "<td colspan=1 align=center bgcolor=#66CC99><b>Historia: ".$whis."/".$wing."</b></td>";
       echo "<input type='HIDDEN' name= 'whis' value='".$whis."'>";
       //NOMBRE DEL PACIENETE
       echo "<td colspan=3 align=center bgcolor=#66CC99><b>Paciente: ".$wpac."</b></td>";
       echo "<input type='HIDDEN' name= 'wpac' value='".$wpac."'>";
       //FUENTE
       echo "<input type='HIDDEN' name= 'wfue' value='".$wfue."'>";
       //DOCUMENTO
       echo "<input type='HIDDEN' name= 'wdoc' value='".$wdoc."'>";
       //INGRESO
       echo "<input type='HIDDEN' name= 'whis' value='".$whis."'>";

       //Aca traigo los documentos de traslado para el centro de costo y fecha digitada
       //$fecha = date($wdia."-".$wmesfec."-".$wanofec,"%d-%m-%Y");
       $fechaI = date($wanoi."-".$wmesi."-".$wdiai,"%Y/%m/%d");
       $fechaF = date($wanof."-".$wmesf."-".$wdiaf,"%Y/%m/%d");

       $query = " SELECT drofec, drofue, drodoc, drocco"
               ."   FROM ivdro, inmtra, insercco, inpac "
               ."  WHERE drofue    = '11' "
               ."    AND droano    = '2005' "
               ."    AND dromes    = '01' "
               ."    AND drofec    BETWEEN '".$fechaI."' AND '".$fechaF."'"
               ."    AND drocco    in ('1050','1051') "
               ."    AND drohis    = trahis "
               ."    AND dronum    = tranum "
               ."    AND drohis    = ".$whis
               ."    AND dronum    = ".$wing
               ."    AND traser    = serccoser "
               ."    AND serccocco = '".$wcco."'"
               ."    AND drohis    = pachis "
               ."    AND dronum    = pacnum "
               ."    AND droanu    = '0' "
               ."  GROUP BY 1,2,3,4 "
               ."  ORDER BY 1 ";

       echo "<tr>";
       echo "<th bgcolor=#fffffff>Fecha</th>";
       echo "<th bgcolor=#fffffff>Fuente</th>";
       echo "<th bgcolor=#fffffff>Documento</th>";
       echo "<th bgcolor=#fffffff colspan=2>Servicio Origen</th>";
       echo "</tr>";

       $res = odbc_do($conexunix,$query);

       //echo "<option selected> </option>";

	   while(odbc_fetch_row($res))
	      {
		    //Aca busco si el documento ya esta registrado en MATRIX, si si, entonces no lo vuelvo a mostrar

		    $q = "  SELECT COUNT(*) AS can "
		        ."    FROM invetras_000001 "
		        ."   WHERE fuente    = '".odbc_result($res,2)."'"
		        ."     AND documento = '".odbc_result($res,3)."'"
		        ."     AND ok        = 'on' ";

		    $res1 = mysql_query($q,$conex);
            $row = mysql_fetch_array($res1);

            if ($row[0] == 0 )    //Si es 0 indica que no ha sido procesado
               {

	            $wfec = odbc_result($res,1);
	            $wfue = odbc_result($res,2);
	            $wdoc = odbc_result($res,3);
	            $wccor = odbc_result($res,4);

	            echo "<tr>";
		        echo "<td align=center bgcolor=#99FFCC><font size=3><b>".$wfec."</b></font></td>";
		        echo "<td align=center bgcolor=#99FFCC><font size=3><b>".$wfue."</b></font></td>";
		        echo "<td align=center bgcolor=#99FFCC><font size=3><b>".$wdoc."</b></font></td>";
		        echo "<td align=center bgcolor=#99FFCC><font size=3><b>".$wccor."</b></font></td>";

		        echo "<td align=center bgcolor=#99FFCC><font size=3><b><A href='tras_Paci_detalle1.php?wfue=".$wfue."&amp;wdoc=".$wdoc."&amp;wanoi=".$wanoi."&amp;wmesi=".$wmesi."&amp;wdiai=".$wdiai."&amp;wanof=".$wanof."&amp;wmesf=".$wmesf."&amp;wdiaf=".$wdiaf."&amp;wcco=".$wcco."&amp;whis=".$whis."&amp;wing=".$wing."&amp;wpac=".$wpac."&amp;whab=".$whab."'>Detallar</A></b></font></td>";
		        echo "</tr>";
		       }
	      }
	   echo "</SELECT></td></tr></table><br><br>";
      } // else de todos los campos setiados
	  
		odbc_close($conexunix);
		odbc_close_all();

	  
} // if de register

echo "<br>";
echo "<font size=3><A href='Tras_Paci_invent.php?wfue=".$wfue."&amp;wdoc=".$wdoc."&amp;wanoi=".$wanoi."&amp;wmesi=".$wmesi."&amp;wdiai=".$wdiai."&amp;wanof=".$wanof."&amp;wmesf=".$wmesf."&amp;wdiaf=".$wdiaf."&amp;wcco=".$wcco."&amp;whis=".$whis."&amp;wing=".$wing."&amp;wpac=".$wpac."'> Retornar</A></font>";

//include_once("free.php");
//odbc_close($conexunix);
?>
