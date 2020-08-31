<head>
  <title>CERTIFCADO DE ESTADISTICA</title>
</head>
<body>
<?php
include_once("conex.php");
  /***************************************************
   *       GRABA LOS CERTIFICADOS DE ESTADISTICA     *
   * HOSPITALIZADOS, CIRUGIA AMBULATORIA Y EGRESADOS *
   *				CONEX, FREE => OK				 *
   ***************************************************/

//==================================================================================================================================
//PROGRAMA                   : cer_estadistica.php
//AUTOR                      : Juan Carlos Hernández M.
//FECHA CREACION             : Marzo 15 de 2005
//FECHA ULTIMA ACTUALIZACION :
  $wactualiz="(Ver. 2007-04-03)";
//DESCRIPCION
//==================================================================================================================================
//Este programa se hace con el objetivo de tener una base de datos de todos los certificados de estancia que se hacen en
//registros medicos, el cual pide como parametros, Tipo de certificado (hay 4 tipos), el documento de identidad, nombres y apellidos
//del paciente, con alguno oalgunos parametros de estos, se va ha buscar los ingresos y egresos que tenga en el momento el paciente
//y asi el usuario de registros medicos selecciona sobre cual ingreso va sacar el certificado. El diagnostico y el procedimiento se
//deben seleccionar tambien de acuerdo al numero de ingreso seleccionado.
//Para reimprimir un certificado se debe saber el numero de este. y digitarlo en el campo nro de certificado y enter y saldra la
//misma información que se imprimio cuando se genero el original.
//==================================================================================================================================

//==================================================================================================================================
//MODIFICACIONES 2007-04-03: Se agrega un campo para poner observaciones y se crea el campo en la tabla cerest_000001.
//==================================================================================================================================
// Septiembre 1
// Se adionado el certifcado de Urgencias
//==================================================================================================================================

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
  

						or die("No se ralizo Conexion");
  


  $conexunix = odbc_connect('admisiones','infadm','1201')
  					  or die("No se ralizo Conexion con el Unix");

  $pos = strpos($user,"-");
		       $wusuario = substr($user,$pos+1,strlen($user));

  echo "<br>";
  echo "<br>";

  echo "<form action='cer_estadisticapru.php' method=post>";

  //========================================================================================================
  //Aca entra solo cuando se va ha consultar un certificado
  //========================================================================================================
  if (isset($wcer) and $wcer != "")
     {
      //Traigo el certificado digitado
      $q = "  SELECT fecha_data, Nro_certificado, Tipo   , Historia  , Ingreso, Documento, Coddia, Nomdia, "
          ."         Codpro    , Nompro         , Destino, Solicitado, Firmo, Observacion "
          ."    FROM cerest_000001 "
 	      ."   WHERE Nro_certificado = ".$wcer;
      $res1 = mysql_query($q,$conex);
      $num1 = mysql_num_rows($res1);
      //echo mysql_errno() ."=". mysql_error();
    if ($num1 > 0)
       {
	    $row = mysql_fetch_array($res1);
        $wfecha  = $row[0];
        $wcer    = $row[1];
        $wtip    = $row[2];
        $whis    = $row[3];
        $whis1   = $row[3];
        $wing    = $row[4];
        $wdoc    = $row[5];
        $wcoddia = $row[6];
        $wnomdia = $row[7];
        $wcodpro = $row[8];
        $wnompro = $row[9];
        $wdes    = $row[10];
        $wsol    = $row[11];
        $wfir    = $row[12];
        $obser    = $row[13];

        //Traigo los datos que corresponden al certificado pero que estan solo almacenados en el UNIX
        $query = " SELECT pacfec, pacnom, pacap1, pacap2, pacfec "
	            ."   FROM inpac "
	            ."  WHERE pacced = '".$wdoc."'"
	            ."    AND pachis = ".$whis1
	            ."    AND pacnum = ".$wing

	            ."  UNION ALL "

	            ." SELECT pacing, pacnom, pacap1, pacap2, pacing "
	            ."   FROM inpaci "
	            ."  WHERE pacced = '".$wdoc."'"
	            ."    AND pachis = ".$whis1
	            ."    AND pacnum = ".$wing

	            ."  UNION ALL "

		            ." SELECT egring, pacnom, pacap1, pacap2, egregr "
	            ."   FROM inpaci, inmegr "
	            ."  WHERE pacced = '".$wdoc."'"
	            ."    AND pachis = ".$whis1
	            ."    AND pacnum = ".$wing
	            ."    AND pachis =  egrhis "
	            ."    AND pacnum != egrnum "
	            ."  GROUP BY 5, 1, 2, 3, 4 "
	            ."  ORDER BY 5 desc, 1, 2 ";

	    $res = odbc_do($conexunix,$query);
	    //echo $query;
	    //echo mysql_errno() ."=". mysql_error();
	    while(odbc_fetch_row($res))
	        {
	         $wfecing = odbc_result($res,1);
	         $wnom    = odbc_result($res,2);
	         $wap1    = odbc_result($res,3);
	         $wap2    = odbc_result($res,4);
	         $wfecegr = odbc_result($res,5);
	        }
         //$wfecing=2006/02/02;
        //Traigo el cargo del responsable de la firma
        $q = "  SELECT cargo "
            ."    FROM cerest_000002 "
 	        ."   WHERE Nombre = '".$wfir."'";
        $res1 = mysql_query($q,$conex);
        $num1 = mysql_num_rows($res1);
        if ($num1 > 0)
          {
	       $row = mysql_fetch_array($res1);
           $wempleado = $wfir;
           $wcargo    = $row[0];
          }
       }
      else
         echo "EL CERTIFICADO NO EXISTE";
	 }

  //========================================================================================================
  //Aca entra solo si es para crear un nuevo certificado
  //========================================================================================================
  if(!isset($wtip) or !isset($wdoc) or !isset($whis))
    {
	 echo "<center><table border=1>";
     echo "<tr><td align=center colspan=5 bgcolor=#fffffff><font size=5 text color=#CC0000><b>CLINICA LAS AMERICAS</b></font></td></tr>";
     echo "<tr><td align=center colspan=5 bgcolor=#fffffff><font size=3 text color=#CC0000><b>CERTIFICADOS ESTADISTICA</b></font></td></tr>";
     echo "<tr><td align=center colspan=5 bgcolor=#fffffff><font size=2 text color=#CC0000><b>".$wactualiz."</b></font></td></tr>";

     if (!isset($wtip) or !isset($wdoc))
	    {
		 //TIPOS DE CERTIFICADO
		 $q = "  SELECT subcodigo, descripcion "
		     ."    FROM det_selecciones "
		     ."   WHERE medico = 'cerest' "
		     ."     AND codigo = '01' ";

		 $res1 = mysql_query($q,$conex);
		 $num1 = mysql_num_rows($res1);

		 echo "<td bgcolor=#99FFCC colspan=5 ><b>Tipo de certificado: </b><select name='wtip'>";
		 for($i=1;$f<$num1;$f++)
		    {
		     $row = mysql_fetch_array($res1);
		     echo "<option>".$row[0]." - ".$row[1]."</option>";
		    }
		 echo "</select></td>";

		 //NRO DE CERTIFICADO
		 echo "<tr><td bgcolor=#99FFCC><b>Nro de certificado :</b><INPUT TYPE='text' NAME='wcer' SIZE=15></td></tr>";

		 //DOCUMENTO
		 echo "<tr><td bgcolor=#cccccc colspan=5 ><b>Documento :</b><INPUT TYPE='text' NAME='wdoc' SIZE=15 VALUE='*'></td></tr>";

		 //NOMBRES
		 echo "<tr><td bgcolor=#cccccc colspan=5 ><b>Nombres :</b><INPUT TYPE='text' NAME='wnom' SIZE=30 VALUE='*'></td></tr>";

		 //PRIMER APELLIDO
		 echo "<tr><td bgcolor=#cccccc colspan=5 ><b>Primer apellido :</b><INPUT TYPE='text' NAME='wap1' SIZE=30 VALUE='*'></td></tr>";

		 //SEGUNDO APELLIDO
		 echo "<tr><td bgcolor=#cccccc colspan=5 ><b>Segundo apellido :</b><INPUT TYPE='text' NAME='wap2' SIZE=30 VALUE='*'></td></tr>";

		  }

	 //No ha seleccionado la historia pero si los otros datos
	 if (!isset($whis) and (isset($wdoc) or isset($wnom) or isset($wap1) or isset($wap2)))
	    {
		 //Si digita solo asteriscos, no se hace la consulta porque seria muy grande y demorada
		 if ($wdoc != '*' or $wnom != '*' or $wap1 != '*' or $wap2 != '*')
		    {
			 //TIPO DE CERTIFICADO
			 echo "<tr><td bgcolor=#99FFCC colspan=5 ><b>Tipo de certificado : ".$wtip."</td></tr>";

			 $wdoc = strtoupper($wdoc);
			 $wap1 = strtoupper($wap1);
			 $wap2 = strtoupper($wap2);
			 $wnom = strtoupper($wnom);


			 //TRAIGO TODOS LOS INGRESOS DEL PACIENTE HISTORIA, NOMBRE FECHAS DE INGRESO Y EGRESO
			 $query = " SELECT pachis, pacnum, pacnom, pacap1, pacap2, pacfec, pacfec, 'Activo' "
			         ."   FROM inpac "
			         ."  WHERE pacced  matches '".$wdoc."'"
			         ."    AND pacnom  matches '".$wnom."'"
			         ."    AND pacap1  matches '".$wap1."'"
			         ."    AND (pacap2 matches '".$wap2."'"
			         ."     OR  pacap2 is null ) "

			         ."  UNION ALL "

			         /*
			         ." SELECT pachis, pacnum, pacnom, pacap1, pacap2, pacing, pacing, 'Ultimo Egresado' "
			         ."   FROM inpaci "
			         ."  WHERE pacced matches '".$wdoc."'"
			         ."    AND pacnom matches '".$wnom."'"
			         ."    AND pacap1 matches '".$wap1."'"
			         ."    AND pacap2 matches '".$wap2."'"

			         ."  UNION ALL "
			         */

			         ." SELECT egrhis, egrnum, pacnom, pacap1, pacap2, egring, egregr, 'Egresado' "
			         ."   FROM inpaci, inmegr "
			         ."  WHERE pacced  matches '".$wdoc."'"
			         ."    AND pacnom  matches '".$wnom."'"
			         ."    AND pacap1  matches '".$wap1."'"
			         ."    AND (pacap2 matches '".$wap2."'"
			         ."     OR  pacap2 is null ) "
			         ."    AND pachis  = egrhis "
			         //."    AND pacnum != egrnum "
			         ."  GROUP BY 6, 1, 2, 3, 4, 5, 7, 8 "
			         ."  ORDER BY 6 desc, 1, 2 ";

			 $res = odbc_do($conexunix,$query);

			 echo "<tr><td bgcolor=#cccccc colspan=5 ><b>Seleccione la Historia e ingreso: </b><select name='whis'>";
		     while(odbc_fetch_row($res))
		         echo "<option><b>Fecha Ingreso: </b>".odbc_result($res,6)." _ "  //Fecha de ingreso
		                     ."<b>Historia:      </b>".odbc_result($res,1)." _ "  //Historia
		                     ."<b>Ingreso Nro:   </b>".odbc_result($res,2)." _ "  //Ingreso
		                                              .odbc_result($res,3)." _ "  //Nombre paciente
		                                              .odbc_result($res,4)." _ "  //1er apellido
		                                              .odbc_result($res,5)." _ "  //2do apellido
		                     ."<b>Fecha Egreso:  </b>".odbc_result($res,7)." _ "  //Fecha de egreso
		                                              .odbc_result($res,8)        //Estado
		             ."</option>";
		     echo "</select></td></tr>";


		     //ACA TRAIGO LOS DIAGNOSTICOS QUE TENGA LA HISTORIA EN EL UNIX
		     $query = " SELECT diacod, dianom, egrhis, egrnum "
			         ."   FROM inpaci, inmegr, india, inmdia "
			         ."  WHERE pacced  matches '".$wdoc."'"
			         ."    AND pacnom  matches '".$wnom."'"
			         ."    AND pacap1  matches '".$wap1."'"
			         ."    AND (pacap2 matches '".$wap2."'"
			         ."     OR  pacap2 is null ) "
			         ."    AND pachis  = egrhis "
			         ."    AND egrhis  = mdiahis "
			         ."    AND egrnum  = mdianum "
			         ."    AND mdiadia = diacod "
			         ."    AND mdiatip = 'P' "
			         ."  GROUP BY 1,2,3,4 "
			         ."  ORDER BY 2, 3 desc ";
			 $res = odbc_do($conexunix,$query);

			 echo "<tr><td bgcolor=#cccccc colspan=5 ><b>Seleccione el diagnostico: </b>";
			 echo "<select name='wdiag'>";
			 echo "<option selected>XXXX - NO ESPECIFICADO</option>";
		     while(odbc_fetch_row($res))
		         echo "<option>".odbc_result($res,1)." - "
		                        .odbc_result($res,2)." - "
		                        ."Historia: ".odbc_result($res,3)
		                        ." - Ingreso Nro: ".odbc_result($res,4)
		             ."</option>";
		     echo "</select></td></tr>";

		     //ACA TRAIGO LOS PROCEDIMIENTOS QUE TIENE LA HISTORIA EN EL UNIX
		     $query = " SELECT procod, pronom, egrhis, egrnum "
			         ."   FROM inpaci, inmegr, inpro, inmpro "
			         ."  WHERE pacced  matches '".$wdoc."'"
			         ."    AND pacnom  matches '".$wnom."'"
			         ."    AND pacap1  matches '".$wap1."'"
			         ."    AND (pacap2 matches '".$wap2."'"
			         ."     OR  pacap2 is null ) "
			         ."    AND pachis  = egrhis "
			         ."    AND egrhis  = mprohis "
			         ."    AND egrnum  = mpronum "
			         ."    AND mpropro = procod "
			         ."    AND mprotip = 'P' "
			         ."  GROUP BY 1,2,3,4 "
			         ."  ORDER BY 2, 3 desc ";
			 $res = odbc_do($conexunix,$query);

			 echo "<tr><td bgcolor=#cccccc colspan=5 ><b>Seleccione el procedimiento: </b>";
			 echo "<select name='wpro'>";
			 echo "<option selected>XXXX - NO ESPECIFICADO</option>";
		     while(odbc_fetch_row($res))
		         echo "<option>".odbc_result($res,1)." - "
		                        .odbc_result($res,2)." - "
		                        ."Historia: ".odbc_result($res,3)
		                        ." - Ingreso Nro: ".odbc_result($res,4)
		             ."</option>";
		     echo "</select></td></tr>";

		     //SE PIDE LA OBSERVACION
			 echo "<tr><td bgcolor=#cccccc colspan=5 ><b>Observacion :</b><TEXTAREA Name='obser' rows='3' cols='50'>*</TEXTAREA></td></tr>";

		      //PIDO LA ENTIDAD DESTINO
		     echo "<tr><td bgcolor=#cccccc colspan=5 ><b>Entidad a quien se dirige :</b><INPUT TYPE='text' NAME='wdes' SIZE=80 VALUE='*'></td></tr>";

		     //PIDO EL SOLICITANTE
		     echo "<tr><td bgcolor=#cccccc colspan=5 ><b>A solicitud de: </b><INPUT TYPE='text' NAME='wsol' SIZE=80 VALUE='*'></td></tr>";

		     //PIDO DEL EMPLEADO QUE FIRMA
			 $q = "  SELECT nombre, cargo "
			     ."    FROM cerest_000002 ";
			 $res1 = mysql_query($q,$conex);
			 $num1 = mysql_num_rows($res1);

			 echo "<td bgcolor=#cccccc colspan=5 ><b>Empleado que registra: </b><select name='wfir'>";
			 for($i=1;$f<$num1;$f++)
			    {
			     $row = mysql_fetch_array($res1);
			     echo "<option>".$row[0]." - ".$row[1]."</option>";
			    }
			 echo "</select></td>";


		     echo "<input type='HIDDEN' name= 'wtip' value='".$wtip."'>";
		     echo "<input type='HIDDEN' name= 'wdoc' value='".$wdoc."'>";
	        }
		}
	    echo "<br>";
        echo"<tr><td align=center bgcolor=#cccccc colspan=5 ><input type='submit' value='ACEPTAR'></td></tr></form>";
        echo "</table>";
    }
   else
      /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      // Ya estan todos los campos setiados o iniciados ===================================================================================
      /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      {
	   echo "<input type='HIDDEN' name= 'wtip' value='".$wtip."'>";
	   echo "<input type='HIDDEN' name= 'wdoc' value='".$wdoc."'>";

	   $wfecha=date("Y-m-d");
	   $wano = substr($wfecha,0,4);
	   $wmes = substr($wfecha,5,2);
	   $wdia = substr($wfecha,8,2);

	   //Si el certificado se esta consultando no entra a este if. Para ingresar a este if es porque se esta creando el certificado
	   if (!isset($wcer) or $wcer == "" )
	      {
		   $pos0    = strpos($whis,":");

		   $pos1    = strpos($whis,"_",$pos0+1);
		   $wfecing = substr($whis,$pos0+1,$pos1-$pos0-1);

		   $pos1_2    = strpos($whis,":",$pos1+1);

		   $pos2    = strpos($whis,"_",$pos1_2+1);
		   $whis1   = substr($whis,$pos1_2+1,$pos2-$pos1_2-1);

		   $pos2_2    = strpos($whis,":",$pos2+1);

		   $pos3    = strpos($whis,"_",$pos2_2+1);
		   $wing    = substr($whis,$pos2_2+1,$pos3-$pos2_2-1);

		   $pos4    = strpos($whis,"_",$pos3+1);
		   $wnom    = substr($whis,$pos3+1,$pos4-$pos3-1);

		   $pos5    = strpos($whis,"_",$pos4+1);
		   $wap1    = substr($whis,$pos4+1,$pos5-$pos4-1);

		   $pos6    = strpos($whis,"_",$pos5+1);
		   $wap2    = substr($whis,$pos5+1,$pos6-$pos5-1);

		   $pos6_2    = strpos($whis,":",$pos6+1);

		   $pos7    = strpos($whis,"_",$pos6_2+1);
		   $wfecegr = substr($whis,$pos6_2+1,$pos7-$pos6_2-1);

		   $westado = substr($whis,$pos7+1,strlen($whis));

		   //SEPARO codigo y nombre del diagnostico
		   $pos1    = strpos($wdiag,"-");
		   $wcoddia = substr($wdiag,0,$pos1);

		   $pos2    = strpos($wdiag,"-",$pos1+1);
		   if ($pos2 == 0)
		      $wnomdia = substr($wdiag,$pos1+1,strlen($wdiag));
		     else
		        $wnomdia = substr($wdiag,$pos1+1,$pos2-$pos1-1);

		   //SEPARO codigo y nombre del procedimiento
		   $pos1    = strpos($wpro,"-");
		   $wcodpro = substr($wpro,0,$pos1);

		   $pos2    = strpos($wpro,"-",$pos1+1);
		   if ($pos2 == 0)
		      $wnompro = substr($wpro,$pos1+1,strlen($wpro));
		     else
		        $wnompro = substr($wpro,$pos1+1,$pos2-$pos1-1);

		   //SEPARO nombre y cargo del empleado
		   $pos1    = strpos($wfir,"-");
		   $wempleado = substr($wfir,0,$pos1);
		   $wcargo = substr($wfir,$pos1+1,strlen($wfir));

		   echo "<input type='HIDDEN' name= 'wdiag' value='".$wdiag."'>";
		   echo "<input type='HIDDEN' name= 'wpro' value='".$wpro."'>";
		   echo "<input type='HIDDEN' name= 'wdes' value='".$wdes."'>";
		   echo "<input type='HIDDEN' name= 'wsol' value='".$wsol."'>";
		   echo "<input type='HIDDEN' name= 'wfir' value='".$wfir."'>";


		   //Traigo el ultimo numero de los certificados
		   $q = "  SELECT MAX(nro_certificado) "
			   ."    FROM cerest_000001 ";
		   $res1 = mysql_query($q,$conex);
		   $row = mysql_fetch_array($res1);
		   $wnrocer = $row[0]+1;

		   //$wfecha=date("Y-m-d");
		   //$wano = substr($wfecha,0,4);
		   //$wmes = substr($wfecha,5,2);
		   //$wdia = substr($wfecha,8,2);

		   $hora = (string)date("H:i:s");

		   $q = "     insert into cerest_000001 (Medico  ,   Fecha_data,    Hora_data,   Nro_certificado     ,   Tipo    ,   Historia ,   Ingreso ,   Documento,   Coddia     ,   Nomdia     ,   Codpro     ,   Nompro     ,   Destino ,   Solicitado,   Firmo        , Observacion,     Seguridad) "
		       ."                        values ('cerest','".$wfecha."' ,'".$hora."' ,'".$wnrocer."'         ,'".$wtip."', ".$whis1." , ".$wing." ,'".$wdoc."' ,'".$wcoddia."','".$wnomdia."','".$wcodpro."','".$wnompro."','".$wdes."','".$wsol."'  ,'".$wempleado."', '".$obser."','C-".$wusuario."')";
		   $res2 = mysql_query($q,$conex) or die(mysql_errno().":".mysql_error());
          }

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
		//echo $wfecing;
	   $wfechai = date(trim($wfecing)); //Fecha de Ingreso
	   $wanoi   = substr($wfechai,0,4);
	   $wmesi   = substr($wfechai,5,2);
	   $wdiai  = substr($wfechai,8,2);

	   echo "Medellín, ".$wdia." de ".$wmes." de ".$wano;
	   echo "<br><br><br><br>";
	   echo "<center><font size=4> LA COORDINADORA DE REGISTROS MEDICOS</font></center>";
	   echo "<br><br>";
	   echo "<p align=right><b>CERTIFICADO NRO.: $wcer</b></p>";
	   echo "<br>";
	   echo "<center><font size=3>CERTIFICA:</font></center>";
	   echo "<br><br><br><br>";

	   switch (Trim($wtip))
	      {
		   case "01 - HOSPITALIZADO":    //Hospitalizado
		      {
			   switch ($wmesi)
			     {
			      case "01": $wmesi="Enero"; break;
			      case "02": $wmesi="Febrero"; break;
			      case "03": $wmesi="Marzo"; break;
			      case "04": $wmesi="Abril"; break;
			      case "05": $wmesi="Mayo"; break;
			      case "06": $wmesi="Junio"; break;
			      case "07": $wmesi="Julio"; break;
			      case "08": $wmesi="Agosto"; break;
			      case "09": $wmesi="Septiembre"; break;
			      case "10": $wmesi="Octubre"; break;
			      case "11": $wmesi="Noviembre"; break;
			      case "12": $wmesi="Diciembre"; break;
			     }
			   echo  "<font size=3>Que el(la) paciente ".$wnom." ".$wap1." ".$wap2." con identificación ".$wdoc.", "
			        ."es atendido(a) en esta institución en el servicio de hospitalización desde el "
			        .$wdiai." de ".$wmesi." de ".$wanoi." hasta nueva orden médica.</font>";

			   echo "<br><br><br>";

			   echo "<font size=3>Diagnóstico: ".$wnomdia."</font><br><br>";
			   echo "<font size=3>Procedimiento: ".$wnompro."</font>";

			   if ($obser != '*' and $obser !='')// este es el if para cuando no existe observacion
			   {
				   echo "<br><br><br>";
				   echo "<font size=3>Observacion: ".$obser."</font>";
			   }
			   echo "<br><br><br>";
			   echo "<font size=3>Este certificado se expide para presentar en: ".$wdes."</font><br><br>";
			   echo "<font size=3>A solicitud de: ".$wsol."</font>";
			   echo "<br><br><br>";
			   echo  "<font size=3>'Esta información pertenece a la historia clínica, cuya confidencialidad "
			        ."está protegida por ley, y ésta prohibe cualquier otro uso de ella o "
			        ."revelarla a otros, sin el consentimiento escrito de la persona a quien "
			        ."pertenece'.</font>";
			   echo "<br><br><br>";
			   echo "<font size=3>Cordialmente,</font><br>";
			   echo "<br><br><br>";
			   echo "<font size=3>".$wempleado."</font><br>";
			   echo "<font size=3>".$wcargo."</font><br>";
			   BREAK;
              }

           case "02 - CIRUGIA AMBULATORIA":    //Cirugia Ambulatoria
		      {
			   switch ($wmesi)
			     {
			      case "01": $wmesi="Enero"; break;
			      case "02": $wmesi="Febrero"; break;
			      case "03": $wmesi="Marzo"; break;
			      case "04": $wmesi="Abril"; break;
			      case "05": $wmesi="Mayo"; break;
			      case "06": $wmesi="Junio"; break;
			      case "07": $wmesi="Julio"; break;
			      case "08": $wmesi="Agosto"; break;
			      case "09": $wmesi="Septiembre"; break;
			      case "10": $wmesi="Octubre"; break;
			      case "11": $wmesi="Noviembre"; break;
			      case "12": $wmesi="Diciembre"; break;
			     }
			   echo  "<font size=3>Que el(la) paciente ".$wnom." ".$wap1." ".$wap2." con identificación ".$wdoc.", "
			        ."fue atendido(a) en esta institución en el servicio de cirugía ambulatoria el "
			        .$wdiai." de ".$wmesi." de ".$wanoi.".</font>";

			   echo "<br><br><br>";

			   echo "<font size=3>Diagnóstico: ".$wnomdia."</font><br><br>";
			   echo "<font size=3>Procedimiento: ".$wnompro."</font>";

			    if ($obser != '*' and $obser !='')// este es el if para cuando no existe observacion
			   {
				   echo "<br><br><br>";
				   echo "<font size=3>Observacion: ".$obser."</font>";
			   }
			   echo "<br><br><br>";
			   echo "<font size=3>Este certificado se expide para presentar en: ".$wdes."</font><br><br>";
			   echo "<font size=3>A solicitud de: ".$wsol."</font>";
			   echo "<br><br><br>";
			   echo  "<font size=3>'Esta información pertenece a la historia clínica, cuya confidencialidad "
			        ."está protegida por ley, y ésta prohibe cualquier otro uso de ella o "
			        ."revelarla a otros, sin el consentimiento escrito de la persona a quien "
			        ."pertenece'.</font>";
			   echo "<br><br><br>";
			   echo "<font size=3>Cordialmente,</font><br>";
			   echo "<br><br><br>";
			   echo "<font size=3>".$wempleado."</font><br>";
			   echo "<font size=3>".$wcargo."</font><br>";
			   BREAK;
              }
           case "03 - EGRESADO DE HOSPITALIZACION":    //Egresados de Hospitalización
		      {
			   switch ($wmesi)
			     {
			      case "01": $wmesi="Enero"; break;
			      case "02": $wmesi="Febrero"; break;
			      case "03": $wmesi="Marzo"; break;
			      case "04": $wmesi="Abril"; break;
			      case "05": $wmesi="Mayo"; break;
			      case "06": $wmesi="Junio"; break;
			      case "07": $wmesi="Julio"; break;
			      case "08": $wmesi="Agosto"; break;
			      case "09": $wmesi="Septiembre"; break;
			      case "10": $wmesi="Octubre"; break;
			      case "11": $wmesi="Noviembre"; break;
			      case "12": $wmesi="Diciembre"; break;
			     }

			   $wfecha_e=date(trim($wfecegr)); //Fecha de Ingreso
			   $wano_e = substr($wfecha_e,0,4);
			   $wmes_e = substr($wfecha_e,5,2);
			   $wdia_e = substr($wfecha_e,8,2);

			   switch ($wmes_e)
			     {
			      case "01": $wmes_e="Enero"; break;
			      case "02": $wmes_e="Febrero"; break;
			      case "03": $wmes_e="Marzo"; break;
			      case "04": $wmes_e="Abril"; break;
			      case "05": $wmes_e="Mayo"; break;
			      case "06": $wmes_e="Junio"; break;
			      case "07": $wmes_e="Julio"; break;
			      case "08": $wmes_e="Agosto"; break;
			      case "09": $wmes_e="Septiembre"; break;
			      case "10": $wmes_e="Octubre"; break;
			      case "11": $wmes_e="Noviembre"; break;
			      case "12": $wmes_e="Diciembre"; break;
			     }

			   echo  "<font size=3>Que el(la) paciente ".$wnom." ".$wap1." ".$wap2." con identificación ".$wdoc.", "
			        ."fue atendido(a) en esta institución en el servicio de hospitalización desde el "
			        .$wdiai." de ".$wmesi." de ".$wanoi." hasta el ".$wdia_e." de ".$wmes_e." de ".$wano_e.".</font>";

			   echo "<br><br><br>";

			   echo "<font size=3>Diagnóstico: ".$wnomdia."</font><br><br>";
			   echo "<font size=3>Procedimiento: ".$wnompro."</font>";

			    if ($obser != '*' and $obser !='')// este es el if para cuando no existe observacion
			   {
				   echo "<br><br><br>";
				   echo "<font size=3>Observacion: ".$obser."</font>";
			   }
			   echo "<br><br><br>";
			   echo "<font size=3>Este certificado se expide para presentar en: ".$wdes."</font><br><br>";
			   echo "<font size=3>A solicitud de: ".$wsol."</font>";
			   echo "<br><br><br>";
			   echo  "<font size=3>'Esta información pertenece a la historia clínica, cuya confidencialidad "
			        ."está protegida por ley, y ésta prohibe cualquier otro uso de ella o "
			        ."revelarla a otros, sin el consentimiento escrito de la persona a quien "
			        ."pertenece'.</font>";
			   echo "<br><br><br>";
			   echo "<font size=3>Cordialmente,</font><br>";
			   echo "<br><br><br>";
			   echo "<font size=3><b>".$wempleado."</b></font><br>";
			   echo "<font size=3><b>".$wcargo."</b></font><br>";
			   BREAK;
              }

           case "04 - HOSPITALIZADO FALLECIDO":    //Egresados de Hospitalización
		      {
			   switch ($wmesi)
			     {
			      case "01": $wmesi="Enero"; break;
			      case "02": $wmesi="Febrero"; break;
			      case "03": $wmesi="Marzo"; break;
			      case "04": $wmesi="Abril"; break;
			      case "05": $wmesi="Mayo"; break;
			      case "06": $wmesi="Junio"; break;
			      case "07": $wmesi="Julio"; break;
			      case "08": $wmesi="Agosto"; break;
			      case "09": $wmesi="Septiembre"; break;
			      case "10": $wmesi="Octubre"; break;
			      case "11": $wmesi="Noviembre"; break;
			      case "12": $wmesi="Diciembre"; break;
			     }

			   $wfecha_e=date(trim($wfecegr)); //Fecha de Ingreso
			   $wano_e = substr($wfecha_e,0,4);
			   $wmes_e = substr($wfecha_e,5,2);
			   $wdia_e = substr($wfecha_e,8,2);

			   switch ($wmes_e)
			     {
			      case "01": $wmes_e="Enero"; break;
			      case "02": $wmes_e="Febrero"; break;
			      case "03": $wmes_e="Marzo"; break;
			      case "04": $wmes_e="Abril"; break;
			      case "05": $wmes_e="Mayo"; break;
			      case "06": $wmes_e="Junio"; break;
			      case "07": $wmes_e="Julio"; break;
			      case "08": $wmes_e="Agosto"; break;
			      case "09": $wmes_e="Septiembre"; break;
			      case "10": $wmes_e="Octubre"; break;
			      case "11": $wmes_e="Noviembre"; break;
			      case "12": $wmes_e="Diciembre"; break;
			     }

			   echo  "<font size=3>Que el(la) paciente ".$wnom." ".$wap1." ".$wap2." con identificación ".$wdoc.", "
			        ."fue atendido(a) en esta institución en el servicio de hospitalización desde el "
			        .$wdiai." de ".$wmesi." de ".$wanoi." hasta el ".$wdia_e." de ".$wmes_e." de ".$wano_e." y falleció.</font>";

			   echo "<br><br><br>";

			   echo "<font size=3>Diagnóstico: ".$wnomdia."</font><br><br>";
			   echo "<font size=3>Procedimiento: ".$wnompro."</font>";

			    if ($obser != '*' and $obser !='')// este es el if para cuando no existe observacion
			   {
				   echo "<br><br><br>";
				   echo "<font size=3>Observacion: ".$obser."</font>";
			   }
			   echo "<br><br><br>";
			   echo "<font size=3>Este certificado se expide para presentar en: ".$wdes."</font><br><br>";
			   echo "<font size=3>A solicitud de: ".$wsol."</font>";
			   echo "<br><br><br>";
			   echo  "<font size=3>'Esta información pertenece a la historia clínica, cuya confidencialidad "
			        ."está protegida por ley, y ésta prohibe cualquier otro uso de ella o "
			        ."revelarla a otros, sin el consentimiento escrito de la persona a quien "
			        ."pertenece'.</font>";
			   echo "<br><br><br>";
			   echo "<font size=3>Cordialmente,</font><br>";
			   echo "<br><br><br>";
			   echo "<font size=3><b>".$wempleado."</b></font><br>";
			   echo "<font size=3><b>".$wcargo."</b></font><br>";
			   BREAK;
              }

           case "05 - URGENCIAS":    //Urgencias
		      {
			   switch ($wmesi)
			     {
			      case "01": $wmesi="Enero"; break;
			      case "02": $wmesi="Febrero"; break;
			      case "03": $wmesi="Marzo"; break;
			      case "04": $wmesi="Abril"; break;
			      case "05": $wmesi="Mayo"; break;
			      case "06": $wmesi="Junio"; break;
			      case "07": $wmesi="Julio"; break;
			      case "08": $wmesi="Agosto"; break;
			      case "09": $wmesi="Septiembre"; break;
			      case "10": $wmesi="Octubre"; break;
			      case "11": $wmesi="Noviembre"; break;
			      case "12": $wmesi="Diciembre"; break;
			     }
			   echo  "<font size=3>Que el(la) paciente ".$wnom." ".$wap1." ".$wap2." con identificación ".$wdoc.", "
			        ."fue atendido(a) en esta institución en el servicio de urgencias el día ".$wdiai.
			        " de ".$wmesi." de ".$wanoi.".</font>";

			   echo "<br><br><br>";

			   echo "<font size=3>Diagnóstico: ".$wnomdia."</font><br><br>";
			   echo "<font size=3>Procedimiento: ".$wnompro."</font>";

			    if ($obser != '*' and $obser !='')// este es el if para cuando no existe observacion
			   {
				   echo "<br><br><br>";
				   echo "<font size=3>Observacion: ".$obser."</font>";
			   }
			   echo "<br><br><br>";
			  echo "<font size=3>Este certificado se expide para presentar en: ".$wdes."</font><br><br>";
			   echo "<font size=3>A solicitud de: ".$wsol."</font>";
			   echo "<br><br><br>";
			   echo  "<font size=3>'Esta información pertenece a la historia clínica, cuya confidencialidad "
			        ."está protegida por ley, y ésta prohibe cualquier otro uso de ella o "
			        ."revelarla a otros, sin el consentimiento escrito de la persona a quien "
			        ."pertenece'.</font>";
			   echo "<br><br><br>";
			   echo "<font size=3>Cordialmente,</font><br>";
			   echo "<br><br><br>";
			   echo "<font size=3>".$wempleado."</font><br>";
			   echo "<font size=3>".$wcargo."</font><br>";
			   BREAK;
              }

          }

      } // else de todos los campos setiados
	  
	  	odbc_close($conexunix);
		odbc_close_all();	
} // if de register

echo "<br>";
//echo "<td colspan=3><left><font size=3><A href=cer_estadistica.php".">&nbsp;&nbsp;&nbsp;    Retornar &nbsp;&nbsp;&nbsp; </A></font></td></tr></table>";
//echo "<td colspan=3><left><font size=1><A href=cer_estadistica.php".">Ir</A></font></td></tr></table>";

include_once("free.php");
//odbc_close($conexunix);
?>
