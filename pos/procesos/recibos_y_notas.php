<head>
  <title>RECIBOS DE CAJA Y NOTAS DEBITO Y CREDITO</title>
</head>
<body onload=ira()>
<script type="text/javascript">
	function enter()
	{
	   document.forms.recibos_y_notas.submit();
	}
</script>
<script type="text/javascript">
function enter1()
	{
	   $wvcon=0;
	}
</script>

<?php
include_once("conex.php");
  /*************************************************************************************
   *     PROGRAMA PARA LA GRABACION DE LAS RECIBOS DE CAJA Y NOTAS DEBITO Y CREDITO    *
   *                                   DE FARMASTORE                                   *
   *************************************************************************************/

//==================================================================================================================================
//PROGRAMA                   : Recibos_y_notas.php
//AUTOR                      : Juan Carlos Hernández M.
  $wautor="Juan C. Hernandez M.";
//FECHA CREACION             : Octubre 18 de 2005
//FECHA ULTIMA ACTUALIZACION :
  $wactualiz="(Versión Octubre 18 de 2005)";
//DESCRIPCION
//==================================================================================================================================
//Este programa debe sirve para hacer los recibos de caja a varias facturas de una misma empresa o las notas debito y credito, pudiendose
//hacer la cancelacion con conceptos de cartera por cada una de las facturas detalladas.
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
  


  


  $pos = strpos($user,"-");
  $wusuario = substr($user,$pos+1,strlen($user));

  	                                                           // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
  $wactualiz="(Versión Octubre 18 de 2005)";                   // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                                           // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //

  $wfecha=date("Y-m-d");
  $hora = (string)date("H:i:s");

  echo "<form name='recibos_y_notas' action='recibos_y_notas.php' method=post>";

  echo "<input type='HIDDEN' name= 'wini' value='".$wini."'>";
  echo "<input type='HIDDEN' name= 'wbasedato' value='".$wbasedato."'>";
  echo "<input type='HIDDEN' name= 'wemp_pmla' value='".$wemp_pmla."'>";

  if ($wini == "S")  //'S' Indica que se esta iniciando un recibo o nota
     {
      $wfecha_tempo=$wfecha;
      $whora_tempo=$hora;
      $wnuelin="";

      $wfecha_bor=date("Y-m-d");

      //=============================================================================
	  //BORRO LOS REGISTROS DE DOS DIA ANTES DE LA TABLA TEMPORAL DE RECIBOS Y NOTAS
	  //=============================================================================
	  $q = "  DELETE FROM ".$wbasedato."_000045 "
	      ."   WHERE fecha_data <= str_to_date(ADDDATE('".$wfecha_bor."',-2),'%Y-%m-%d')";
	  $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	  //=============================================================================

	  $k=1;
	 }
    else
      {
       echo "<input type='HIDDEN' name= 'wfecha_tempo' value='".$wfecha_tempo."'>";
	   echo "<input type='HIDDEN' name= 'whora_tempo' value='".$whora_tempo."'>";
      }

  //ACA TRAIGO LAS VARIABLES DE SUCURSAL, CAJA Y TIPO INGRESO QUE REALIZA CADA CAJERO
  $q =  " SELECT cjecco, cjecaj, cjetin "
       ."   FROM ".$wbasedato."_000030 "
       ."  WHERE cjeusu = '".$wusuario."'"
       ."    AND cjeest = 'on' ";

  $res = mysql_query($q,$conex);
  $num = mysql_num_rows($res);
  if ($num > 0)
     {
      $row = mysql_fetch_array($res);

      $pos = strpos($row[0],"-");
      $wcco = substr($row[0],0,$pos);
      $wnomcco = substr($row[0],$pos+1,strlen($row[0]));

      $pos = strpos($row[1],"-");
      $wcaja = substr($row[1],0,$pos);
      $wnomcaj = substr($row[1],$pos+1,strlen($row[1]));

      $wtiping = $row[2];
     }
    else
       echo "EL USUARIO ESTA INACTIVO O NO TIENE PERMISO PARA HACER RECIBOS Y/O NOTAS";

  $wcol=10;  //Numero de columnas que se tienen o se muestran en pantalla

  $wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
  $wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
  $wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
  $wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro


  ////////////////////////////////////////////////////////////////////////////////////////////////////
  //FUNCION PARA MOSTRAR LAS OPCIONES DE RECIBOS DE DINERO - FORMAS DE PAGO
  ////////////////////////////////////////////////////////////////////////////////////////////////////
  function formasdepago($fk,$wfpa,$wdocane,$wobsrec,$wvalfpa,$wtotrec)
      {
	    global $fk;
	    global $wbasedato;
	    global $conex;
	    global $wcf;
	    global $wcf2;
	    global $wclfa;
	    global $wclfg;
	    global $wcol;

	    for ($j=1;$j<=$fk;$j++)
	        {
		      echo "<tr>";

		      $q =  " SELECT fpacod, fpades "
			       ."   FROM ".$wbasedato."_000023 "
			       ."  ORDER BY fpacod ";

			  $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
			  $num = mysql_num_rows($res) or die (mysql_errno()." - ".mysql_error());

			  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
			  //FORMA DE PAGO
			  echo "<td align=left bgcolor=".$wcf2." colspan=2><b><font text color=".$wclfa.">Forma de pago: </font></b><select name='wfpa[".$j."]'>";
			  if (isset($wfpa[$j]))
			     echo "<option selected>".$wfpa[$j]."</option>";
			  for ($i=1;$i<=$num;$i++)
			     {
			      $row = mysql_fetch_array($res);
			      echo "<option>".$row[0]." - ".$row[1]."</option>";
			     }
			  echo "</select></td>";

			  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
			  //DOCUMENTO ANEXO
			  if (isset($wdocane[$j])) //Si ya fue digitado el documento anexo
			     echo "<td bgcolor=".$wcf2." colspan=1><b><font text color=".$wclfa.">Dcto Anexo: </font></b><INPUT TYPE='text' NAME='wdocane[".$j."]' VALUE='".$wdocane[$j]."'></td>";  //wdocane
			    else
			       echo "<td bgcolor=".$wcf2." colspan=1><b><font text color=".$wclfa.">Dcto Anexo: </font></b><INPUT TYPE='text' NAME='wdocane[".$j."]' ></td>";                        //wdocane
			  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
			  //OBSERVACIONES
			  if (isset($wobsrec[$j])) //Si ya fue digitado la observacion
			     echo "<td bgcolor=".$wcf2." colspan=1><b><font text color=".$wclfa.">Observ.: </font></b><INPUT TYPE='text' NAME='wobsrec[".$j."]' VALUE='".$wobsrec[$j]."'></td>";     //wobsrec
			    else
			       echo "<td bgcolor=".$wcf2." colspan=1><b><font text color=".$wclfa.">Observ.: </font></b><INPUT TYPE='text' NAME='wobsrec[".$j."]' ></td>";                           //wobsrec

			  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
			  //Con la siguiente instrucción en Javascript se ubica el cursor en el ultimo campo del valor de la forma de pago osea en: $wvalfpa[$j] : en el VALOR
			  ?>
			    <script>
			      //function ira(){document.Recibos_y_notas.elements.length;}
			      //function ira(){document.Recibos_y_notas.elements[document.Recibos_y_notas.elements.length-1].focus();}
			      function ira(){document.Recibos_y_notas.elements[document.Recibos_y_notas.elements.length-4].focus();}
			    </script>
			  <?php

			  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
			  //VALOR
			  if (isset($wvalfpa[$j]) & $wvalfpa > 0 ) //Si ya fue digitado el valor y es mayor a cero
			     {
				  $wpagado=0;
			      for ($y=1;$y<=$j;$y++)
			          {
				       $wvalfpa[$y]=str_replace(",","",$wvalfpa[$y]);    //Le quito el formato al número
			           $wpagado=$wpagado+$wvalfpa[$y];
		              }

			      $wvalfpa[$j]=str_replace(",","",$wvalfpa[$j]); //Esto se hace para quitarle el formato que trae el número
			      echo "<td bgcolor=".$wcf2." colspan=2><b><font text color=".$wclfa.">Valor: </font></b><INPUT TYPE='text' NAME='wvalfpa[".$j."]' VALUE='".number_format($wvalfpa[$j],2,'.',',')."'></td>";       //wvalfpa
			      if (($wtotrec-$wpagado) > 0 )
			         echo "<td bgcolor=".$wcf2." colspan=1><b><font text color='FFFFFF'>Saldo: </b>".number_format(($wtotrec-$wpagado),2,'.',',')."</font></td>";            //wtotventot-wtotfpa
			        else
			           echo "<td bgcolor=".$wcf2." colspan=1><b><font text color='FFFFFF'>Saldo: </b>".number_format((0),2,'.',',')."</font></td>";                             //wtotventot-wtotfpa
			     }
			    else
			       echo "<td bgcolor=".$wcf2." colspan=2><b><font text color=".$wclfa.">Valor: </font></b><INPUT TYPE='text' NAME='wvalfpa[".$j."]' VALUE='".number_format($wtotrec,2,'.',',')."'></td>";  //wvalfpa

			  echo "</tr>";
			}
	  }



  //===========================================================================================================================================
  //INICIO DEL PROGRAMA
  //===========================================================================================================================================

  $wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
  $wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
  $wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
  $wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro

  echo "<p align=right><font size=3><b>Autor: ".$wautor."</b></font></p>";
  //=======================================================================================================================================
  //ACA COMIENZA EL ENCABEZADO DEL RECIBO
  echo "<center><table border>";
  echo "<tr><td align=center rowspan=2 colspan=2><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=340 HEIGHT=100></td></tr>";
  echo "<tr><td align=center colspan=5 bgcolor=".$wcf2."><font size=7 text color=#FFFFFF><b>RECIBOS DE CAJA Y NOTAS</b></font></td></tr>";
  echo "<tr>";


  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //ACA TRAIGO TODOS LOS REGISTROS DE LA TABLA TEMPORAL
  $q =  " SELECT * "
       ."   FROM ".$wbasedato."_000045 "
       ."  WHERE seguridad = 'C-".$wusuario."'"
       ."    AND temcaj    = '".$wcaja."'";

  $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
  $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;

  //============================================================================================================================
  //============================================================================================================================
  //SI EL DOCUMENTO SE ESTA INICIANDO $WINI="S" AVERIGUO SI EL USUARIO Y LA CAJA TIENEN REGISTROS EN LA TABLA TEMPORAL, SI SI,
  //PREGUNTO SI LOS QUIERE RECUPERAR SI NO LOS BORRO DE LA TABLA
  //============================================================================================================================
  //$wseguir="S";  //Esta variable indica si el usuario eligio seguir cuando se le informo que existia un documento por recuperar
                   //sea cual sea la respuesta se coloca luego este indicador en S. $wseguir="S", mientras hace la pregunta lo coloca en N

  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //FUENTE
  //Cada que se cambie o seleccione una fuente voy a buscar el consecutivo que sigue para esa fuente
  echo "<td align=left bgcolor=".$wcf."><b><font text color=".$wclfg." size=3> Fuente: <br></font></b><select name='wfuente' onchange='enter()'>";
  if (isset($wfuente))
     {
	  //Aca traigo el consecutivo de la fuente
	  $q= "   SELECT carcon "
	     ."     FROM ".$wbasedato."_000040 "
         ."    WHERE carfue = mid('".$wfuente."',1,instr('".$wfuente."','-')-1) "
         ."      AND carest = 'on' ";

      $res1 = mysql_query($q,$conex);
      $num1 = mysql_num_rows($res1);
      $row1 = mysql_fetch_array($res1);
      if ($row1[0] != "" and !isset($wnrodoc))
         $wnrodoc=$row1[0]+1;

      //Aca traigo el campo de forma de pago de la fuente, para saber si se debe capturar forma de pago o no
	  $q= "   SELECT carfpa "
	     ."     FROM ".$wbasedato."_000040 "
	     ."    WHERE  carfue = (mid('".$wfuente."',1,instr('".$wfuente."','-')-1)) "
	     ."      AND (carfue > '24' "
         ."       OR  carfue = '09') ";
	  $res1 = mysql_query($q,$conex);
	  $row1 = mysql_fetch_array($res1);
	  $wcarfpa=$row1[0];


	  $q= "   SELECT count(*) "
	     ."     FROM ".$wbasedato."_000040 "
	     ."    WHERE carfue != (mid('".$wfuente."',1,instr('".$wfuente."','-')-1)) ";
	  $res1 = mysql_query($q,$conex);
	  $num1 = mysql_num_rows($res1);
	  $row1 = mysql_fetch_array($res1);
	  if ($row1[0] > 0)
	     {
	      echo "<option selected>".$wfuente."</option>";
	      $q= "   SELECT carfue, cardes "
	         ."     FROM ".$wbasedato."_000040 "
	         ."    WHERE carfue != (mid('".$wfuente."',1,instr('".$wfuente."','-')-1)) "
	         ."      AND (carfue  > '24' "
             ."       OR  carfue  = '09') ";

	      $res1 = mysql_query($q,$conex);
	      $num1 = mysql_num_rows($res1);
	      for ($i=1;$i<=$num1;$i++)
	         {
		      $row1 = mysql_fetch_array($res1);
	          echo "<option>".$row1[0]." - ".$row1[1]."</option>";
	         }
         }
 	 }
	else
	   {
		$q =  " SELECT carfue, cardes "
             ."   FROM ".$wbasedato."_000040 "
             ."  WHERE (carfue > '24' "
             ."     OR  carfue = '09') "
             ."  ORDER BY carfue ";
        $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
        $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;

		echo "<option selected>&nbsp</option>";
	    for ($i=1;$i<=$num;$i++)
	       {
	        $row = mysql_fetch_array($res);
	        echo "<option>".$row[0]." - ".$row[1]."</option>";
	       }
       }
  echo "</select></td>";

  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //NUMERO DOCUMENTO
  if (isset($wnrodoc))
     echo "<td align=left bgcolor=".$wcf."><b><font text color=".$wclfg.">Documento Nro: <br></font></b><INPUT TYPE='text' NAME='wnrodoc' VALUE='".$wnrodoc."' onchange='enter()'></td>";
    else
       echo "<td align=left bgcolor=".$wcf."><b><font text color=".$wclfg.">Documento Nro: <br></font></b><INPUT TYPE='text' NAME='wnrodoc' onchange='enter()'></td>";

  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //FECHA DE LA VENTA
  if (isset($wfecdoc))
     echo "<td align=left bgcolor=".$wcf."><b><font text color=".$wclfg.">Fecha: <br></font></b><INPUT TYPE='text' NAME='wfecdoc'  VALUE='".$wfecdoc."'></td>";
    else
       echo "<td align=left bgcolor=".$wcf."><b><font text color=".$wclfg.">Fecha: <br></font></b><INPUT TYPE='text' NAME='wfecdoc' VALUE='".$wfecha."'></td>";

  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //SUCURSAL
  echo "<td align=left bgcolor=".$wcf."><b><font text color=".$wclfg.">Sucursal: <br></font></b>".$wnomcco."</td>";
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //CAJA
  echo "<td align=left bgcolor=".$wcf."><b><font text color=".$wclfg.">Caja: <br></font></b>".$wnomcaj."</td>";

  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //RESPONSABLE
  if (isset($wempresa))
     {
	  echo "<td align=left bgcolor=".$wcf."><b><font text color=".$wclfg."> Responsable: <br></font></b><select name='wempresa' onchange='enter()'>";
	  $q= "   SELECT count(*) "
	     ."     FROM ".$wbasedato."_000024 "
         ."    WHERE empcod = (mid('".$wempresa."',1,instr('".$wempresa."','-')-1)) "
         ."      AND empcod = empres ";
      $res1 = mysql_query($q,$conex);
      $num1 = mysql_num_rows($res1);
      $row1 = mysql_fetch_array($res1);

      if ($row1[0] > 0)
         {
	      echo "<option selected>".$wempresa."</option>";
	      $q= "   SELECT count(*) "
	         ."     FROM ".$wbasedato."_000024 "
             ."    WHERE empcod != (mid('".$wempresa."',1,instr('".$wempresa."','-')-1)) "
             ."      AND empcod = empres ";
          $res = mysql_query($q,$conex);
          $num = mysql_num_rows($res);
          $row = mysql_fetch_array($res);
          if ($row[0] > 0)
             {
	          $q= "   SELECT empcod, empnit, empnom "
     	         ."     FROM ".$wbasedato."_000024 "
                 ."    WHERE empcod != (mid('".$wempresa."',1,instr('".$wempresa."','-')-1)) "
                 ."      AND empcod = empres ";
             $res1 = mysql_query($q,$conex);
              $num1 = mysql_num_rows($res1);
              for ($i=1;$i<=$num1;$i++)
		         {
		          $row1 = mysql_fetch_array($res1);
		          echo "<option>".$row1[0]." - ".$row1[1]." - ".$row1[2]."</option>";
		         }
	         }
         }
       echo "</select></td>";

     }
    else
       {
	    echo "<td align=left bgcolor=".$wcf."><b><font text color=".$wclfg."> Responsable: <br></font></b><select name='wempresa'>";
        $q =  " SELECT empcod, empnit, empnom "
             ."   FROM ".$wbasedato."_000024 "
             ."  WHERE empcod = empres "
             ."  ORDER BY empcod ";

        $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());
        $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());

        //echo "<option selected>&nbsp</option>";
        for ($i=1;$i<=$num;$i++)
	       {
	        $row = mysql_fetch_array($res);
	        echo "<option>".$row[0]." - ".$row[1]." - ".$row[2]."</option>";
	       }
	    echo "</select></td>";
	    //echo $num;
       }
  echo "</tr>";

  //=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=//
  //=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=//
  //=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=//
  /////////////////////////////////////////////////////////////////////////////////
  //CUANDO INGRESE A ESTE IF ES PORQUE YA SE DIGITO LA INFORMACION DEL ENCABEZADO//
  /////////////////////////////////////////////////////////////////////////////////
  //=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=//
  //=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=//
  //=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=/=//

  echo "</table>";

  echo "<center><table border>";


  //ACA ELIMINO EL REGISTRO AL QUE SE LE DIO CLICK EN Eliminar
  if (isset($wborrar) and isset($wid) and ($wborrar == "S"))
     {
	  $q="    DELETE FROM ".$wbasedato."_000045 "
	    ."     WHERE id = ".$wid;
	  $res = mysql_query($q,$conex);

	  $wborrar = "N";
	  $wini="N";
	  unset($wborrar);
	 }

  if (isset($wfuente) & isset($wnrodoc) & isset($wempresa))
     {
	  echo "<tr>";
	  echo "<td bgcolor=#ffcc66 colspan=7 align=center><b>DETALLE DE FACTURAS Y CONCEPTOS DE CARTERA</b></td>";
	  echo "</tr>";

	  echo "<th bgcolor=#ffcc66>Nro Factura</th>";
	  echo "<th bgcolor=#ffcc66>Valor</th>";
	  echo "<th bgcolor=#ffcc66>Saldo</th>";
	  echo "<th bgcolor=#ffcc66>Valor a Cancelar</th>";
	  echo "<th bgcolor=#ffcc66>Concepto</th>";
	  echo "<th bgcolor=#ffcc66>Valor Cpto</th>";
	  echo "<th bgcolor=#ffcc66>&nbsp</th>";

	  //ACA TRAIGO TODOS LOS REGISTROS DE LA TABLA TEMPORAL
	  $q =  " SELECT temfue, temdoc, temfec, temsuc, temcaj, temres, temvre, temnfa, temvfa, temsfa, "
	       ."        temvcf, temcon, temdco, temvco, temcco, temter, temfco, temffa, temccc, id  "
           ."   FROM ".$wbasedato."_000045 "
           ."  WHERE seguridad = 'C-".$wusuario."'"
           ."    AND temcaj    = '".$wcaja."'";

      $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
      $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;


      //============================================================================================================================
      //============================================================================================================================
      //SI EL DOCUMENTO SE ESTA INICIANDO $WINI="S" AVERIGUO SI EL USUARIO Y LA CAJA TIENEN REGISTROS EN LA TABLA TEMPORAL, SI SI,
      //PREGUNTO SI LOS QUIERE RECUPERAR SI NO LOS BORRO DE LA TABLA
      //============================================================================================================================
      if ($num > 0)
	     {
	      //ACA SE SUMAN TODOS LOS REGISTROS
		  $wtotvcafac=0;
		  $wtotvalcon=0;
		  for ($k=1;$k<=$num;$k++)
		     {
			  $row = mysql_fetch_array($res);
			  echo "<tr>";
			  echo "<td align=left bgcolor=".$wcf." > ".$row[7]." </td>";
			  echo "<td align=right bgcolor=".$wcf."> ".number_format($row[8],0,'.',',')." </td>";
			  echo "<td align=right bgcolor=".$wcf."> ".number_format($row[9],0,'.',',')." </td>";
			  echo "<td align=right bgcolor=".$wcf."> ".number_format($row[10],0,'.',',')." </td>";
			  echo "<td align=left bgcolor=".$wcf." > ".$row[11]." </td>";
			  echo "<td align=right bgcolor=".$wcf."> ".number_format($row[13],0,'.',',')." </td>";

			  echo "<td align=center><A href='Recibos_y_notas.php?wid=".$row[19]."&amp;wborrar=S"."&amp;wini=".$wini."&amp;wfuente=".$wfuente."&amp;wnrodoc=".$wnrodoc."&amp;wfecdoc=".$wfecdoc."&amp;wnomcco=".$wnomcco."&amp;wcco=".$wcco."&amp;wempresa=".$wempresa."&amp;whora_tempo=".$whora_tempo."&amp;wfecha_tempo=".$wfecha_tempo."&amp;wbasedato=".$wbasedato."'> Eliminar</A></td>";
		      echo "</tr>";
		      $wtotvcafac = $wtotvcafac + $row[10];
		      $wtotvalcon = $wtotvalcon + $row[13];
		     }
		 }
		else
		   {
			$wtotvcafac=0;
			$wtotvalcon=0;
			$k=1;
	       }

	  //ACA TOTALIZO TODOS LOS REGISTROS GRABADOS
      echo "<tr>";
      echo "<td colspan=3 bgcolor=#ffcc66>Totales </td>";
      echo "<td colspan=1 align=right bgcolor=#ffcc66>".number_format($wtotvcafac,0,'.',',')."</td>";
      echo "<td colspan=1 bgcolor=#ffcc66>&nbsp</td>";
      echo "<td colspan=1 align=right bgcolor=#ffcc66>".number_format($wtotvalcon,0,'.',',')."</td>";
      echo "<td colspan=1 align=right bgcolor=#ffcc66>".number_format($wtotvcafac+$wtotvalcon,0,'.',',')."</td>";
      echo "</tr>";

      if (!isset($wgrabar))  //No ha ingresado el primer valor de la forma de pago.
	  	 {
		  //===****====****===****====****===****====****===****====****===****====****===****====****===****====****===****====****===****====****===****====****===****====****===****====****===****====****===****====****===****====****===****====****===****====****===****====****===**
		  //===****====****===****====****===****====****===****====****===****====****===****====****===****====****===****====****===****====****===****====****===****====****===****====****===****====****===****====****===****====****===****====****===****====****===****====****===**
		  //Si no se ha digitado la factura a cancelar entro
		  //Deben estar setiados todos los campos para entrar por el else
		  //===****====****===****====****===****====****===****====****===****====****===****====****===****====****===****====****===****====****===****====****===****====****===****====****===****====****===****====****===****====****===****====****===****====****===****====****===**
		  if ((!isset($wnrofac[$k]) or $wnrofac[$k] == '' or !isset($wvcafac[$k]) or $wvcafac[$k] == '' or !isset($wconcar[$k]) or $wconcar[$k] == '' or !isset($wvalcon[$k]) or $wvalcon[$k]=='') or $wnuelin == "N" or (isset($wconcar[$k]) and (!isset($wvalcon[$k]) or $wvalcon[$k]=='')))
		    {
		     echo "<tr>";

		     //Si no esta setiado el campo entro al then. Si sí, traigo la que se habia digitado
		     //NRO FACTURA
		     if (!isset($wnrofac[$k]) or ($wnrofac[$k]==''))
		        echo "<td align=left bgcolor=".$wcf."><INPUT TYPE='text' NAME='wnrofac[".$k."]' onchange='enter()'></td>";
		       else
		          { ///
		           echo "<td align=left bgcolor=".$wcf."><INPUT TYPE='text' NAME='wnrofac[".$k."]' VALUE='".$wnrofac[$k]."' onchange='enter()'></td>";

		           $wnrofac1 = explode("-",$wnrofac[$k]);
				   //$wfuefac[$k]=$wnrofac1[0];
				   $wnrofac[$k]=$wnrofac1[0]."-".$wnrofac1[1];

				   $wempresa1=explode("-",$wempresa);
				   $wcod = trim($wempresa1[0]);
				   $wnit = trim($wempresa1[1]);

				   //Busco si existe la factura
				   $q= "   SELECT count(*) "
				      ."     FROM ".$wbasedato."_000018 "
		              //."    WHERE fenffa = '".$wfuefac[$k]."'"
		              ."    WHERE fenfac = '".$wnrofac[$k]."'"
		              ."      AND fenres = '".$wcod."'"
		              ."      AND fenest = 'on' ";

		           $res1 = mysql_query($q,$conex);
		           $num1 = mysql_num_rows($res1);
		           $row1 = mysql_fetch_array($res1);

		           if ($row1[0] > 0)
		              {
			           $q= "   SELECT fenval-fencmo, fensal-fencmo "
	     		          ."     FROM ".$wbasedato."_000018 "
		                  ."    WHERE fenfac = '".$wnrofac[$k]."'"
		                  ."      AND fenest = 'on' ";

		               $res1 = mysql_query($q,$conex);
		               $num1 = mysql_num_rows($res1);
		               $row1 = mysql_fetch_array($res1);
		               $wvalfac[$k]=$row1[0];
		               $wsalfac[$k]=$row1[1];
		               if ($wsalfac[$k] == 0)
		                  $wvcafac[$k]=0;

		               //Si la factura esta digitada y el saldo es mayor a cero me paro en el valor a cancelar si no en el concepto
		               if ($wsalfac[$k] > 0)
		                  {
			               $q="  SELECT sum(temvcf+temvco) "
			                 ."    FROM ".$wbasedato."_000045 "
			                 ."   WHERE temnfa = '".$wnrofac[$k]."'";
			               $res1 = mysql_query($q,$conex);
		                   $num1 = mysql_num_rows($res1);
		                   $row1 = mysql_fetch_array($res1);
		                   $wsalfac[$k]=$wsalfac[$k]-$row1[0];

		                   ?>
				            <script>
		    		           function ira(){document.recibos_y_notas.elements[document.recibos_y_notas.elements.length+3].focus();}
		    		           ///*document.write([document.recibos_y_notas.elements.length]); */
			                </script>
				           <?php
			              }
			             else
			                {
				             ?>
			                   <script>
	    		                  function ira(){document.recibos_y_notas.elements[document.recibos_y_notas.elements.length-3].focus();}
		                       </script>
			                 <?php
				            }
		              }
		             else
		                {
			             //No existe la factura o el responsable seleccionado no es el mismo de la factura
			             $wvalfac[$k]='';
		                 $wsalfac[$k]='';
		                 ?>
		                  <script>
			                 function ira(){document.recibos_y_notas.elements[document.recibos_y_notas.elements.length-5].focus();}
	                      </script>
		                 <?php
			            }
		          } ///

		     //VALOR FACTURA
		     if (!isset($wvalfac[$k]))
		        echo "<td align=left bgcolor=".$wcf.">&nbsp</td>";
		       else
		          echo "<td align=left bgcolor=".$wcf.">".number_format($wvalfac[$k],0,'.',',')."</td>";

		     //SALDO FACTURA
		     if (!isset($wsalfac[$k]))
		        echo "<td align=left bgcolor=".$wcf.">&nbsp</td>";
		       else
		          echo "<td align=left bgcolor=".$wcf.">".number_format($wsalfac[$k],0,'.',',')."</td>";


		     //VALOR A CANCELAR FACTURA
		     if (!isset($wvcafac[$k]) or $wvcafac[$k]=='')
		        echo "<td align=left bgcolor=".$wcf."><INPUT TYPE='text' NAME='wvcafac[".$k."]' VALUE=0 ></td>";
		       else
		          {
			       if (isset($wsalfac[$k]))
			          {
			           if ($wvcafac[$k] <= $wsalfac[$k])
				          {
				           echo "<td align=left bgcolor=".$wcf."><INPUT TYPE='text' NAME='wvcafac[".$k."]' VALUE='".$wvcafac[$k]."' ></td>";
				           if ($wvcafac[$k] >= 0 and $wvcafac[$k] != "")
						      {
							   ?>
						         <script>
				    		         function ira(){document.recibos_y_notas.elements[document.recibos_y_notas.elements.length-3].focus();}
					             </script>
						       <?php
							  }
					      }
					     else
					        {
						     echo "<td align=left bgcolor=".$wcf."><INPUT TYPE='text' NAME='wvcafac[".$k."]' ></td>";
						     ?>
						         <script>
					              alert ("El valor a cancelar No puede ser mayor al saldo");
					              function ira(){document.recibos_y_notas.elements[document.recibos_y_notas.elements.length-4].focus();}
					             </script>
					         <?php
				            }
			          }
			         else
			            echo "<td align=left bgcolor=".$wcf."><INPUT TYPE='text' NAME='wvcafac[".$k."]' VALUE=0 ></td>";
			      }

			 //CONCEPTO DE CARTERA
		     ///===========================================================
			 if (isset($wconcar[$k]))
			    $q =  " SELECT concod, condes, id, conmul"
			         ."   FROM ".$wbasedato."_000044 "
			         ."  WHERE conest = 'on' "
			         ."    AND concod != '".$wconcar[$k]."'"
			         ."    AND confue = mid('".$wfuente."',1,instr('".$wfuente."','-')-1) "
			         ."  ORDER BY id desc ";
			   else
			      $q =  " SELECT concod, condes, id, conmul "
			           ."   FROM ".$wbasedato."_000044 "
				       ."  WHERE conest = 'on' "
				       ."    AND confue = mid('".$wfuente."',1,instr('".$wfuente."','-')-1) "
				       ."  ORDER BY id desc ";

			 $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
			 $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;

			 //echo "<td align=left bgcolor=".$wcf."><INPUT TYPE='text' NAME='wconcar[".$k."]' VALUE='".strtoupper($wconcar[$k])."' ></td>";
			 echo "<td align=left bgcolor=".$wcf." colspan=2><select name='wconcar[".$k."]' onchange='enter()' >";

			 if (isset($wconcar[$k]))
			    echo "<option selected>".$wconcar[$k]."</option>";

			 for ($i=1;$i<=$num;$i++)
			    {
			     $row = mysql_fetch_array($res);
			     echo "<option>".$row[0]." - ".$row[1]."</option>";
			    }
			 echo "</select></td>";

			 if (isset($wnrofac[$k-1])) echo "<input type='HIDDEN' name='wnrofac[".$k."]' value='".$wnrofac[$k]."'>";
	 	     if (isset($wvcafac[$k-1])) echo "<input type='HIDDEN' name='wvcafac[".$k."]' value='".$wvcafac[$k]."'>";
	         if (isset($wsalfac[$k])) echo "<input type='HIDDEN' name='wsalfac[".$k."]' value='".$wsalfac[$k]."'>";
	         if (isset($wvalfac[$k])) echo "<input type='HIDDEN' name='wvalfac[".$k."]' value='".$wvalfac[$k]."'>";


	         //VALOR CONCEPTO DE CARTERA
		     if (!isset($wvalcon[$k]) or $wvalcon[$k] == "")
		        {
		         echo "<td align=left bgcolor=".$wcf."><INPUT TYPE='text' NAME='wvalcon[".$k."]' onblur='enter1()'></td>";
		        }
	           else
		          {
			       if ($wvalcon[$k]=="0")
			          $wnuelin="S";
			         else
			            {
				         if ($wvalcon[$k] > $wsalfac[$k])
			                {
						     echo "<td align=left bgcolor=".$wcf."><INPUT TYPE='text' NAME='wvalcon[".$k."]' ></td>";
						     ?>
						       <script>
					             alert ("El valor a cancelar No puede ser mayor al saldo");
					             function ira(){document.recibos_y_notas.elements[document.recibos_y_notas.elements.length-4].focus();}
					           </script>
					         <?php
				             }
			            }
			       echo "<td align=left bgcolor=".$wcf."><INPUT TYPE='text' NAME='wvalcon[".$k."]' VALUE='".$wvalcon[$k]."' onblur='enter1()'></td>";

			       $k=$k+1;
		          }
		     echo "</tr>";
		    }
		   else
		      {
			   if (isset($wvalcon))
			      {
				   if (isset($wvcafac[$k]))    //Resto al saldo el valor a cancelar de la factura
				      $wsalfac[$k]=$wsalfac[$k]-$wvcafac[$k];

			       if ($wvalcon[$k] <= $wsalfac[$k])
				      {
					   $wsalfac[$k]=$wsalfac[$k]-$wvalcon[$k];

				       $q= " INSERT INTO ".$wbasedato."_000045 (   Medico       ,   Fecha_data ,   Hora_data,   Temfue     ,   temdoc     ,   temfec      ,   temsuc  ,   temcaj   ,   temres      , temvre,   temnfa         ,   temvfa         ,   temsfa         ,   temvcf         ,   temcon         , temdco ,   temvco         ,  Seguridad) "
			              ."                            VALUES ('".$wbasedato."','".$wfecha."' ,'".$hora."' ,'".$wfuente."','".$wnrodoc."','".$wfecdoc."' ,'".$wcco."','".$wcaja."','".$wempresa."', '0'   ,'".$wnrofac[$k]."','".$wvalfac[$k]."','".$wsalfac[$k]."','".$wvcafac[$k]."','".$wconcar[$k]."', ''     ,'".$wvalcon[$k]."', 'C-".$wusuario."')";
					   $res2 = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

					   $wini="N";
					   echo "<input type='HIDDEN' name= 'wini' value='".$wini."'>";
					   echo "<input type='HIDDEN' name= 'wfecha_tempo' value='".$wfecha_tempo."'>";
		               echo "<input type='HIDDEN' name= 'whora_tempo' value='".$whora_tempo."'>";

		               $k=$k+1;
	                  }
	                 else
	                    {
		                 echo "<td align=left bgcolor=".$wcf."><INPUT TYPE='text' NAME='wvalcon[".$k."]' ></td>";
					     ?>
					       <script>
				             alert ("El valor a cancelar No puede ser mayor al saldo");
				             function ira(){document.recibos_y_notas.elements[document.recibos_y_notas.elements.length-4].focus();}
				           </script>
				         <?php
		                }
	              }
		         else
		             {
				      $q= " INSERT INTO ".$wbasedato."_000045 (   Medico       ,   Fecha_data ,   Hora_data,   Temfue     ,   temdoc     ,   temfec      ,   temsuc  ,   temcaj   ,   temres      , temvre,   temnfa         ,   temvfa         ,   temsfa         ,   temvcf         ,   temcon         , temdco ,   temvco         ,  Seguridad) "
			              ."                           VALUES ('".$wbasedato."','".$wfecha."' ,'".$hora."' ,'".$wfuente."','".$wnrodoc."','".$wfecdoc."' ,'".$wcco."','".$wcaja."','".$wempresa."', '0'   ,'".$wnrofac[$k]."','".$wvalfac[$k]."','".$wsalfac[$k]."','".$wvcafac[$k]."','".$wconcar[$k]."', ''     ,'".$wvalcon[$k]."', 'C-".$wusuario."')";
					  $res2 = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

			          $wini="N";
					  echo "<input type='HIDDEN' name= 'wini' value='".$wini."'>";
					  echo "<input type='HIDDEN' name= 'wfecha_tempo' value='".$wfecha_tempo."'>";
		              echo "<input type='HIDDEN' name= 'whora_tempo' value='".$whora_tempo."'>";

		              $k=$k+1;
	                 }
	          }
	          if (isset($wnuelin)) echo "<input type='HIDDEN' name='wnuelin' value='".$wnuelin."'>";
          }
         else
            {
	          if ($wcarfpa=='on')   //SI TIENE FORMA DE PAGO =============================================
	            {
		         if (!isset($wvalfpa[1]))
		            {
			         $fk=1;
		             formasdepago($fk,'','','','',$wtotvcafac);
		            }
	               else
	                  {
		               $wtotfpa=0;
				       for ($j=1;$j<=$fk;$j++)   //Aca sumo todas las formas de pago, para saber si se muestra el campo de cambio
				          {
					       $wvalfpa[$j]=str_replace(",","",$wvalfpa[$j]);    //Le quito el formato al número
				           $wtotfpa=$wtotfpa+$wvalfpa[$j];
				          }

		               if ($wvalfpa[$fk] != "" and $wvalfpa[$fk] > 0 and $wtotfpa < ($wtotvcafac))
		                  {
			               $fk=$fk+1;
			               formasdepago($fk,$wfpa,$wdocane,$wobsrec,$wvalfpa,$wtotvcafac);
	                      }
	                     else
	                        {
	                         formasdepago($fk,$wfpa,$wdocane,$wobsrec,$wvalfpa,($wtotvcafac));

	                         if ($wtotfpa == ($wtotvcafac))
	                            {
		                         //==========================================================================
		                         //Aca selecciono los siguientes campos:
		                         //==========================================================================
		                         //   temfue: fuente del documento
		                         //   temdoc: numero del documento
		                         //   temfec: fecha de grabacion del documento
		                         //   temsuc: centro de costo o sucursal
		                         //   temcaj: codigo de la caja que hizo el documento
		                         //   temres: responsable de las facturas detalladas en este documento
		                         //   temvre: valor del recibo (en este momento esta quedando en nulo)
		                         //   temnfa: numero de factura
		                         //   temvfa: valor de la factura
		                         //   temsfa: saldo en linea de la factura
		                         //   temvcf: valor a cancelar factura
		                         //   temcon: concepto de cartera
		                         //   temdco: descripcion del concepto de cartera
		                         //   temvco: valor concepto de cartera

		                         //==========================================================================
		                         //Q U E R Y ================================================================
		                         $q =  " SELECT temfue, temdoc, temfec, temsuc, temcaj, temres, temvre, "
		                              ."        temnfa, temvfa, temsfa, temvcf, temcon, temdco, temvco, id  "
							          ."   FROM ".$wbasedato."_000045 "
							          ."  WHERE seguridad = 'C-".$wusuario."'"
							          ."    AND temcaj    = '".$wcaja."'";
							     $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
							     $num = mysql_num_rows($res) or die (mysql_errno()." - ".mysql_error());
		                         $row = mysql_fetch_array($res);

		                         $wfuedoc=$row[0];
		                         $wcco   =$row[3];

		                         $wfuente=explode("-",$wfuedoc);

				                 //==============================================================================================
			                     //ACA SE BLOQUEA LA TABLA DE LAS FUENTES =======================================================
			                     $q = "lock table ".$wbasedato."_000040 LOW_PRIORITY WRITE";
				                 $errlock = mysql_query($q,$conex);

				                 //Aca actualizo el consecutivo de la fuente ====================================================
							     $q= "   UPDATE ".$wbasedato."_000040 "
							        ."      SET carcon = carcon + 1 "
						            ."    WHERE carfue = '".trim($wfuente[0])."'"
						            ."      AND carest = 'on' ";

						         $res1 = mysql_query($q,$conex);

						         $q = " UNLOCK TABLES";   //SE DESBLOQUEA LA TABLA DE FUENTES
								 $errunlock = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
								 //==============================================================================================


				                 $q= "  SELECT carncr, carndb, carrec, carcon "
				                    ."    FROM ".$wbasedato."_000040 "
				                    ."   WHERE carfue = '".trim($wfuente[0])."'";
				                 $err1 = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				                 $row1 = mysql_fetch_array($err1) or die (mysql_errno()." - ".mysql_error());

				                 if ($row1[0]=='on')
				                    $wtipdoc = "ncr";
				                   else
				                      if ($row1[1]=='on')
				                         $wtipdoc = "ndb";
				                        else
				                           if ($row1[2]=='on')
							                  $wtipdoc = "rec";

							     $wnrodoc = $row1[3]; //Numero del documento


					             //=============================================================================================
				                 //ACA SEGUN EL TIPO DE DOCUMENTO TOMO EL MULTIPLO PARA SUMAR O RESTAR EN LA CARTERA
				                 //=============================================================================================
				                 switch ($wtipdoc)
				                   {
				                    case "ncr":
				                         { $wmul=-1;  //multiplo
								           break;
				                         }

				                    case "ndb":
				                         { $wmul=1;   //multiplo
								           break;
				                         }

				                    case "rec":
				                         { $wmul=-1;  //multiplo
								           break;
				                         }
				                   }

								 //=============================================================================================
		                         $wcodfue=explode("-",$wfuedoc);

		                         $wempr=explode("-",$row[5]);
		                         //===================================================================================================================================
							     //GRABO EN LA TABLA DEL -- <ENCABEZADO DE RECIBOS Y NOTAS> -- EN EL **** RECIBO DE CAJA ****
							     $q= " INSERT INTO ".$wbasedato."_000020 (   Medico       ,   Fecha_data,   Hora_data,    renfue       ,  rennum   ,  renvca                                             ,   rencod      ,   rennom      ,   rencaj    ,   renusu      ,   rencco    , renest , Seguridad       ) "
								    ."                            VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".$wcodfue[0]."',".strtoupper($wnrodoc).",".number_format(($wtotvcafac+$wtotvalcon),0,'.','').",'".$wempr[0]."','".$wempr[2]."','".$row[4]."','".$wusuario."','".$row[3]."', 'on'   ,'C-".$wusuario."')";
							     $resins = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

							     for ($i=1;$i<=$num;$i++)
							         {
								      //===================================================================================================================================
							          //GRABO EN LA TABLA DEL -- <DETALLE DE FACTURAS> -- EN EL **** RECIBO DE CAJA ****
							          $q= " INSERT INTO ".$wbasedato."_000021 (   Medico       ,   Fecha_data,   Hora_data,   rdefue        ,  rdenum    ,   rdecco    ,   rdefac    , rdevta ,  rdevca    , rdeest,    rdecon    ,  rdevco    , Seguridad        ) "
								         ."                            VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".$wcodfue[0]."',".strtoupper($wnrodoc).",'".$row[3]."','".$row[7]."', ''     ,".$row[10].",'on'   ,'".$row[11]."',".$row[13].", 'C-".$wusuario."')";
							          $resins = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

							          //===================================================================================================================================
							          //ACTUALIZO EL SALDO DE CADA FACTURA -- <EN ENCABEZADO DE FACTURAS> --

							          switch ($wtipdoc)
							               {
								            case "ncr":
				                                 {
					                              $q= "  UPDATE ".$wbasedato."_000018 "
										             ."     SET fensal = fensal + ".(($row[10]+$row[13])*$wmul)
										             ."        ,fenvnc = fenvnc + ".($row[10]+$row[13])
										             ."   WHERE fenfac = '".$row[7]."'";
					                              $resupd = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
					                              break;
				                                 }
				                            case "ndb":
				                                 {
					                              $q= "  UPDATE ".$wbasedato."_000018 "
										             ."     SET fensal = fensal + ".(($row[10]+$row[13])*$wmul)
										             ."        ,fenvnd = fenvnd + ".($row[10]+$row[13])
										             ."   WHERE fenfac = '".$row[7]."'";
					                              $resupd = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
					                              break;
				                                 }
				                            case "rec":
				                                 {
					                              $q= "  UPDATE ".$wbasedato."_000018 "
										             ."     SET fensal = fensal + ".(($row[10]+$row[13])*$wmul)
										             ."        ,fenabo = fenabo + ".($row[10]+$row[13])
										             ."   WHERE fenfac = '".$row[7]."'";
					                              $resupd = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
					                              break;
				                                 }
				                           }
							          //===================================================================================================================================
		                              //BORRO CADA REGISTRO DE LA TABLA TEMPORAL
		                              $q = " DELETE FROM ".$wbasedato."_000045 "
				                          ."  WHERE id = ".$row[14];
				                      $res_bor = mysql_query($q,$conex);

				                      $row = mysql_fetch_array($res);  //avanzo en las filas del primer query. de la tabla temporal 000045
	                                 }

	                             for ($j=1;$j<=$fk;$j++)
	                                 {
		                              $wcodfue=explode("-",$wfuedoc);
		                              $wcodfpa=explode("-",$wfpa[$j]);

		                              //===================================================================================================================================
							          //GRABO EN LA TABLA DEL -- <DETALLE DE FORMAS DE PAGO> -- EN EL ** RECIBO DE CAJA **
							          $q= " INSERT INTO ".$wbasedato."_000022 (   Medico       ,   Fecha_data,   Hora_data,   rfpfue              ,  rfpnum    ,   rfpfpa              ,  rfpvfp        ,   rfpdan         ,   rfpobs         , rfpest,   rfpcco  , Seguridad        ) "
							 	        ."                             VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".trim($wcodfue[0])."',".strtoupper($wnrodoc).",'".trim($wcodfpa[0])."',".$wvalfpa[$j].",'".$wdocane[$j]."','".$wobsrec[$j]."', 'on'  ,'".$wcco."', 'C-".$wusuario."') ";
							          $resfpa = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

		                             }
	                             echo "<tr><td align=CENTER colspan=".$wcol."><Font size=4><b>DOCUMENTO GRABADO</b></font></td></tr>";
				                 echo "<td align=center bgcolor=#99FFCC colspan=".($wcol)."><font size=2><b><A href='imp_documento.php?wnrodoc=".strtoupper($wnrodoc)."&amp;wfuedoc=".$wcodfue[0]."&amp;wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."&amp;wcco=".$wcco."' TARGET='_blank'>Imprimir_Documento</A></b></font></td>";

								 unset ($wgrabar);
								}
	                        }
	                  }
		         echo "<input type='HIDDEN' name= 'fk' value='".$fk."'>";
		        }
	           else  //Por aca grabo los documentos que no tienen forma de pago
	              {
		            //////**************************************************************************///////
		            //==========================================================================
                    //Q U E R Y ================================================================
                    $q =  " SELECT temfue, temdoc, temfec, temsuc, temcaj, temres, temvre, "
                         ."        temnfa, temvfa, temsfa, temvcf, temcon, temdco, temvco, id  "
				         ."   FROM ".$wbasedato."_000045 "
				         ."  WHERE seguridad = 'C-".$wusuario."'"
				         ."    AND temcaj    = '".$wcaja."'";
				    $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				    $num = mysql_num_rows($res) or die (mysql_errno()." - ".mysql_error());
                    $row = mysql_fetch_array($res);

                    $wfuedoc=$row[0];
                    $wcco   =$row[3];

                    $wfuente=explode("-",$wfuedoc);


                    //==============================================================================================
                    //ACA SE BLOQUEA LA TABLA DE LAS FUENTES
                    $q = "lock table ".$wbasedato."_000040 LOW_PRIORITY WRITE";
	                $errlock = mysql_query($q,$conex);

	                //Aca actualizo el consecutivo de la fuente ====================================================
				    $q= "   UPDATE ".$wbasedato."_000040 "
				       ."      SET carcon = carcon + 1 "
			           ."    WHERE carfue = '".trim($wfuente[0])."'"
			           ."      AND carest = 'on' ";

			        $res1 = mysql_query($q,$conex);

			        $q = " UNLOCK TABLES";
					$errunlock = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
					//==============================================================================================


	                $q= "  SELECT carncr, carndb, carrec, carcon "
	                   ."    FROM ".$wbasedato."_000040 "
	                   ."   WHERE carfue = '".trim($wfuente[0])."'";
	                $err1 = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	                $row1 = mysql_fetch_array($err1) or die (mysql_errno()." - ".mysql_error());

	                if ($row1[0]=='on')
	                   $wtipdoc = "ncr";
	                  else
	                     if ($row1[1]=='on')
	                        $wtipdoc = "ndb";
	                       else
	                          if ($row1[2]=='on')
				                 $wtipdoc = "rec";

				    $wnrodoc = $row1[3]; //Numero del documento


		            //=============================================================================================
	                //ACA SEGUN EL TIPO DE DOCUMENTO TOMO EL MULTIPLO PARA SUMAR O RESTAR EN LA CARTERA
	                //=============================================================================================
	                switch ($wtipdoc)
	                  {
	                   case "ncr":
	                        { $wmul=-1;  //multiplo
					          break;
	                        }

	                   case "ndb":
	                        { $wmul=1;   //multiplo
					          break;
	                        }

	                   case "rec":
	                        { $wmul=-1;  //multiplo
					          break;
	                        }
	                  }

	                $wcodfue=explode("-",$wfuedoc);

	                $wempr=explode("-",$row[5]);
	                //===================================================================================================================================
				    //GRABO EN LA TABLA DEL -- <ENCABEZADO DE RECIBOS Y NOTAS> -- EN EL **** RECIBO DE CAJA ****
				    $q= " INSERT INTO ".$wbasedato."_000020 (   Medico       ,   Fecha_data,   Hora_data,    renfue       ,  rennum   ,  renvca                                             ,   rencod      ,   rennom      ,   rencaj    ,   renusu      ,   rencco    , renest , Seguridad       ) "
					    ."                            VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".$wcodfue[0]."',".strtoupper($wnrodoc).",".number_format(($wtotvcafac+$wtotvalcon),0,'.','').",'".$wempr[0]."','".$wempr[2]."','".$row[4]."','".$wusuario."','".$row[3]."', 'on'   ,'C-".$wusuario."')";
				    $resins = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

				    for ($i=1;$i<=$num;$i++)
				        {
					      //===================================================================================================================================
				          //GRABO EN LA TABLA DEL -- <DETALLE DE FACTURAS> -- EN EL **** RECIBO DE CAJA ****
				          $q= " INSERT INTO ".$wbasedato."_000021 (   Medico       ,   Fecha_data,   Hora_data,   rdefue        ,  rdenum    ,   rdecco    ,   rdefac    , rdevta ,  rdevca    , rdeest,    rdecon    ,  rdevco    , Seguridad        ) "
					         ."                            VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".$wcodfue[0]."',".strtoupper($wnrodoc).",'".$row[3]."','".$row[7]."', ''     ,".$row[10].",'on'   ,'".$row[11]."',".$row[13].", 'C-".$wusuario."')";
				          $resins = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

				          //===================================================================================================================================
				          //ACTUALIZO EL SALDO DE CADA FACTURA -- <EN ENCABEZADO DE FACTURAS> --

				          switch ($wtipdoc)
				               {
					            case "ncr":
	                                 {
		                              $q= "  UPDATE ".$wbasedato."_000018 "
							             ."     SET fensal = fensal + ".(($row[10]+$row[13])*$wmul)
							             ."        ,fenvnc = fenvnc + ".($row[10]+$row[13])
							             ."   WHERE fenfac = '".$row[7]."'";

							          $resupd = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		                              break;
	                                 }
	                            case "ndb":
	                                 {
		                              $q= "  UPDATE ".$wbasedato."_000018 "
							             ."     SET fensal = fensal + ".(($row[10]+$row[13])*$wmul)
							             ."        ,fenvnd = fenvnd + ".($row[10]+$row[13])
							             ."   WHERE fenfac = '".$row[7]."'";
		                              $resupd = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		                              break;
	                                 }
	                            case "rec":
	                                 {
		                              $q= "  UPDATE ".$wbasedato."_000018 "
							             ."     SET fensal = fensal + ".(($row[10]+$row[13])*$wmul)
							             ."        ,fenabo = fenabo + ".($row[10]+$row[13])
							             ."   WHERE fenfac = '".$row[7]."'";
		                              $resupd = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		                              break;
	                                 }
	                           }
				          //===================================================================================================================================
	                      //BORRO CADA REGISTRO DE LA TABLA TEMPORAL
	                      $q = " DELETE FROM ".$wbasedato."_000045 "
	                          ."  WHERE id = ".$row[14];
	                      $res_bor = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

	                      $row = mysql_fetch_array($res);  //avanzo en las filas del primer query. de la tabla temporal 000045
	                    }
	                 echo "<tr><td align=CENTER colspan=".$wcol."><Font size=4><b>DOCUMENTO GRABADO</b></font></td></tr>";
	                 echo "<td align=center bgcolor=#99FFCC colspan=".($wcol)."><font size=2><b><A href='imp_documento.php?wnrodoc=".strtoupper($wnrodoc)."&amp;wfuedoc=".$wcodfue[0]."&amp;wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."&wcco=".$wcco."' TARGET='_blank'>Imprimir_Documento</A></b></font></td>";

					 unset ($wgrabar);
		          }
	        }
	 }

  if (isset($wgrabar))
     echo "<tr><td align=center bgcolor=#dddddd colspan=7><font color=#000066 size=5><b>GRABAR</b><input type='checkbox' name='wgrabar' CHECKED></font></td></tr>";
    else
       echo "<tr><td align=center bgcolor=#dddddd colspan=7><font color=#000066 size=5><b>GRABAR</b><input type='checkbox' name='wgrabar'></font></td></tr>";

  if (isset($wfuente) and isset($wnrodoc) and $wfuente != "" and $wnrodoc != "")
     echo "<td align=center colspan=7 bgcolor=#ffcc66><font color=#000066 size=5><A href='Imp_documento.php?wnrodoc=".strtoupper($wnrodoc)."&amp;wfuedoc=".$wfuente."&amp;wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."&wcco=".$wcco."'> Imprimir Documento</A></font></td>";

  echo "<tr>";
  echo "<td align=center bgcolor=#cccccc colspan=7><input type='submit' value='OK'></td>";                            //submit
  echo "</tr>";
}
?>
