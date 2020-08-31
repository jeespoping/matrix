<head>
  <title>IMPRESION DE LECTURAS</title>
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
			//$user="1-".strtolower($codigo);
	}

if(!isset($_SESSION['user']))
	echo "error";
else
{
  $conexunix = odbc_connect('informix','facadm','1201')
  					    or die("No se ralizo Conexion con el Unix");

  //$conex = mysql_pconnect('localhost','root','')
  //						or die("No se ralizo Conexion");
  //



	                                                              // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
  $wactualiz="(Version Diciembre 2 de 2004)";                     // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                                              // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //


  if((!isset($wdcto) and !isset($wingreso))  or !isset($wcodexa))
    {
	  echo "<br>";
	  echo "<br>";
	  echo "<br>";
	  echo "<br>";
	  echo "<br>";
	  echo "<br>";

	  echo "<form action='lecturas.php' method=post>";
	  echo "<center><table border=2 width=400>";
	  echo "<tr><td align=center colspan=240 bgcolor=#fffffff><font size=6 text color=#CC0000><b>CLINICA LAS AMERICAS</b></font></td></tr>";
	  echo "<tr><td align=center colspan=3 bgcolor=#fffffff><font size=4 text color=#CC0000><b>CONSULTA DE LECTURAS IMAGINOLOGIA</b></font></td></tr>";
	  echo "<tr><td align=center colspan=3 bgcolor=#fffffff><font size=3 text color=#CC0000><b>".$wactualiz."</b></font></td></tr>";
	  //echo "<tr></tr>";

	  /*==========================================================================================================*/
	  /*============= A C A   S E   D I G I T A    E L   D O C U M E N T O   O   E L  I N G R E S O ==============*/
	  /*==========================================================================================================*/
	  /*==========================================================================================================*/
	  if(!isset($wdcto) and !isset($wingreso))
	    {
		 echo "<td bgcolor=#cccccc ><b>Digite el Nro del Documento de Identificacion:</b><INPUT TYPE='text' NAME=wdcto></td>";
		 echo "<td bgcolor=#cccccc ><b>Digite el Nro de Ingreso dado en Imaginologia :</b><INPUT TYPE='text' NAME=wingreso></td>";
		 echo"<tr><td align=center bgcolor=#cccccc colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";
		 exit;
 	    }

 	  /*================================================================================================*/
	  /*== A C A   Y A   E S T A   D I G I T A D O   E L   I N G R E S O ===============================*/
	  /*================================================================================================*/
	  /*================================================================================================*/
	  if (isset($wingreso) and (!isset($wdcto) or $wdcto == ""))
        {

	     //echo "<input type='HIDDEN' name= 'wingreso' value='".$wingreso."'>";

	     $pos1=strpos($wingreso,"-");
	     if ($pos1 > 0) $wingreso = substr($wingreso,0,$pos1);

	     echo "<tr><td bgcolor=#cccccc colspan=1><b>Ingreso Nro: </b></td>";
         echo "<td bgcolor=#cccccc colspan=2><SELECT name='wingreso'>";

	     $query = "        SELECT lecing ";
	     $query = $query."   FROM lecturas ";
	     $query = $query."  WHERE lecing like '".$wingreso."'";
	     $query = $query."    AND lecfue in ('RA','RX','OR') ";
	     $query = $query."  GROUP BY lecing ";

	     $res = odbc_do($conexunix,$query);
         while(odbc_fetch_row($res))
	       {
	        echo "<option selected>".odbc_result($res,1)."</option>";
           }
		 echo "</select>";


	     /*====================================================================*/
	     /*======= A C A   S E   S E L E C C I O N A   E L   E X A M E N ======*/
	     /*====================================================================*/
	     /*====================================================================*/
	     echo "<tr><td bgcolor=#cccccc colspan=1><b>Examen: </b></td>";
         echo "<td bgcolor=#cccccc colspan=2><SELECT name='wcodexa'>";

	     $query = "        SELECT lecexa, lecnom ";
	     $query = $query."   FROM lecturas ";
	     $query = $query."  WHERE lecing like '".$wingreso."'";
	     $query = $query."    AND lecfue in ('RA','RX','OR')";
	     $query = $query."  ORDER BY lecexa ";

	     $res = odbc_do($conexunix,$query);
         while(odbc_fetch_row($res))
	        {
		     echo "<option selected>".odbc_result($res,1)."-".odbc_result($res,2)."</option>";
	        }
	     echo "</select>";
	     echo"</td></tr></table>";
	     echo"<tr><td align=center bgcolor=#cccccc colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";
	     exit;
        }

	  /*================================================================================================*/
	  /*== A C A   Y A   E S T A   D I G I T A D O   E L   D O C U M E N T O ===========================*/
	  /*================================================================================================*/
	  /*================================================================================================*/
	  if ((!isset($wingreso) or $wingreso == "") and (isset($wdcto)))
        {
	     //echo "<input type='HIDDEN' name= 'wdcto' value='".$wdcto."'>";

	     echo "<tr><td bgcolor=#cccccc colspan=1><b>Ingreso Nro: </b></td>";
         echo "<td bgcolor=#cccccc colspan=2><SELECT name='wingreso'>";

         $query = "        SELECT lecing, lecfec ";
	     $query = $query."   FROM lecturas ";
	     $query = $query."  WHERE lecdoc like '".$wdcto."'";
	     $query = $query."    AND lecfue in ('RA','RX','OR')";
	     $query = $query."  GROUP BY lecing, lecfec ";
	     $query = $query."  ORDER BY lecfec desc, lecing ";

	     $res = odbc_do($conexunix,$query);
	     while(odbc_fetch_row($res))
	          {
		       echo "<option selected>".odbc_result($res,1)."-   ".odbc_result($res,2)."</option>";
	          }
	          echo "</select>";
	          echo"<tr><td align=center bgcolor=#cccccc colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";
	          exit;

         /*====================================================================*/
	     /*======= A C A   S E   S E L E C C I O N A   E L   E X A M E N ======*/
	     /*====================================================================*/
	     /*====================================================================*/
	     echo "<tr><td bgcolor=#cccccc colspan=1><b>Examen: </b></td>";
         echo "<td bgcolor=#cccccc colspan=2><SELECT name='wcodexa'>";

	     $query = "        SELECT lecexa, lecnom ";
	     $query = $query."   FROM lecturas ";
	     $query = $query."  WHERE lecing like '".$wingreso."'";
	     $query = $query."    AND lecfue in ('RA','RX','OR')";
	     $query = $query."  ORDER BY lecexa ";

	     $res = odbc_do($conexunix,$query);
	     while(odbc_fetch_row($res))
	        {
		     echo "<option selected>".odbc_result($res,1)."-".odbc_result($res,2)."</option>";
	        }
	        echo "</select>";
	        echo"</td></tr></table>";
	     echo"<tr><td align=center bgcolor=#cccccc colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";
	     exit;
	    }
     }
	else
	  /**********************************
	  * TODOS LOS PARAMETROS ESTAN SET  *
	  **********************************/
	  {

		$pos1=strpos($wcodexa,"-");
		$wcodexa = substr($wcodexa,0,$pos1);

		$q = "    SELECT movced, lecfue, lecing, (movnom||movape||movap2), lecexa, lecnom, lecmem, lecfec, lecusu, lecmed, lecrem, lecome "
		       ."   FROM lecturas, aymov "
		       ."  WHERE lecing = '".$wingreso."'"
		       ."    AND lecexa = '".$wcodexa."'"
		       ."    AND lecfue in ('RA','RX','OR')"
		       ."    AND lecfue = movfue "
		       ."    AND lecing = movdoc ";

		$res = odbc_do($conexunix,$q);

	    while(odbc_fetch_row($res))
	       {
		    $NOMPAC = odbc_result($res,4);
		    $CODREM = odbc_result($res,11);
		    $CODMED = odbc_result($res,10);
		    $CODOME = odbc_result($res,12);
		    $CODUSU = odbc_result($res,9);
	       }

		   echo "<br>";
		   echo "<br>";
		   echo "<br>";
		   echo "<br>";

		   $wfecha=odbc_result($res,8);

		   $wano = substr($wfecha,0,4);
		   $wmes = substr($wfecha,5,2);
		   $wdia = substr($wfecha,8,2);

		   switch ($wmes)
		     {
		      case "01": $wmes="Enero"; break;
		      case "02": $wmes="Febrero"; break;
		      case "03": $wmes="Marzo"; break;
		      case "04": $wmes="Abril"; break;
		      case "05": $wmes="Mayo"; break;
		      case "06": $wmes="Junio"; break;
		      case "07": $wmes="Julio"; break;
		      case "08": $wmes="Agosto"; break;
		      case "09": $wmes="Septiembre"; break;
		      case "10": $wmes="Octubre"; break;
		      case "11": $wmes="Noviembre"; break;
		      case "12": $wmes="Diciembre"; break;
		     }

		   echo "Medellín, ".$wdia." de ".$wmes." de ".$wano;
		   echo "<br>";
		   echo "<br>";
		   echo "<br>";
		   echo "<br>";
		   echo "<br>";
		   echo "<br>";
		   echo "<b>"."PACIENTE: ".$NOMPAC." - ".$wingreso."</b><br>";
           echo "<b>"."ESTUDIO: ".odbc_result($res,6)." - ".$wcodexa."</b><br>";

           $q = " SELECT mednom "
		       ."   FROM inmed "
		       ."  WHERE medcod = '".$CODREM."'";
		   $res1 = odbc_do($conexunix,$q);
		   $NOMREM="";
		   while(odbc_fetch_row($res1))
	           $NOMREM = odbc_result($res1,1);

	       IF ($NOMREM == "")                          //Si no esta en inmed lo busco en remitentes
	          $q = " SELECT remnom "
		          ."   FROM remitentes "
		          ."  WHERE remcod = '".$CODREM."'";
		      $res1 = odbc_do($conexunix,$q);
		      while(odbc_fetch_row($res1))
	               $NOMREM = odbc_result($res1,1);

	       echo "<b>"."MEDICO: ".$NOMREM."</b><br>";
	       echo "<br>";
	       echo "<br>";
	       echo odbc_result($res,7); //Campo de la Transcripcion


	       echo "<br>";
	       echo "<br>";
	       echo "<br>";
	       echo "<br>";

	       $q = " SELECT mednom "
		       ."   FROM inmed "
		       ."  WHERE medcod = '".$CODMED."'";
		   $res1 = odbc_do($conexunix,$q);
		   $NOMMED="";
		   while(odbc_fetch_row($res1))
	           $NOMMED = odbc_result($res1,1);

	       IF ($NOMMED == "")                          //Si no esta en inmed lo busco en remitentes
	          $q = " SELECT remnom "
		          ."   FROM remitentes "
		          ."  WHERE remcod = '".$CODMED."'";
		      $res1 = odbc_do($conexunix,$q);
		      while(odbc_fetch_row($res1))
	               $NOMMED = odbc_result($res1,1);

	       echo "<b>".$NOMMED."</b><BR>";
	       echo "<b>"."M.D. RADIOLOGO </b><br>";

	       $q = " SELECT usunom "
		       ."   FROM logins "
		       ."  WHERE usucod = '".$CODUSU."'";
		   $res1 = odbc_do($conexunix,$q);
		   $NOMUSU="";
		   while(odbc_fetch_row($res1))
	           $NOMUSU = odbc_result($res1,1);

	       echo "<Font size=1>".$NOMUSU;

	       echo "<br>";

	       unset($wingreso);
	       unset($wdcto);
	       unset($wcodexa);

		   echo "<font size=1><A href=lecturas.php"."> Ir</A></font>";
	  }    // else del If de $wingreso
	  
		odbc_close($conexunix);
		odbc_close_all();

} // if de register
//include_once("free.php");
//odbc_close($conexaccess);
?>