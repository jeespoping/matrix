<head>
  <title>REPORTE APLICACION Y ADMINISTRACION DE INSUMOS Y MEDICAMENTOS</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000000">
<?php
include_once("conex.php");
  /***************************************************
	*	  REPORTE DE ADMINISTRACION DE MEDICAMENTOS  *
	*	  Y MATERIAL MEDICO QX POR SERVICIO O UNIDAD *
	*	        RESUMIDO PARA LOS AUDITORES          *
	*				CONEX, FREE => OK				 *
	**************************************************/
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	

	

	

						or die("No se ralizo Conexion");
    


	$conexunix = odbc_connect('facturacion','facadm','1201')
  					    or die("No se ralizo Conexion con el Unix");


  	$key = substr($user,2,strlen($user));

	if (strpos($user,"-") > 0)
          $wuser = substr($user,(strpos($user,"-")+1),strlen($user));

    echo "<form action='admon_med_mat_res.php' method=post>";

    if (!isset($whis) or !isset($wing))
       {
	    echo "<center><table border=1 BACKGROUND=.'nubes.gif'>";
        echo "<tr><td align=center colspan=5 bgcolor=#fffffff><font size=5 text color=#CC0000><b>CLINICA LAS AMERICAS</b></font></td></tr>";
        echo "<tr><td align=center colspan=5 bgcolor=#fffffff><font size=2 text color=#CC0000><b>REPORTE RESUMIDO DE APLICACION Y ADMINISTRACION DE INSUMOS Y MEDICAMENTOS A PACIENTES</b></font></td></tr>";

        //Esto no lo hago porque este reporte es para los auditores y ellos podrian seleccionar cualquier historia activa o inactiva
        /*
	    //Traigo el centro costo a partir del usuario que ingreso
	    $query = " SELECT percco "
	            ."   FROM noper "
	          //."  WHERE percod = '".$wuser."'"
	            ."  WHERE percod = '01727'"
	            ."    and peretr = 'A' ";
	    $res = odbc_do($conexunix,$query);
	    while(odbc_fetch_row($res))
	         $wcco = odbc_result($res,1);

	         $wcco="%*%";
	    */

	    $query = " SELECT trahab, trahis, tranum, pacnom, pacap1, pacap2 "
	            ."   FROM inmtra, inpac "
	            ."  WHERE trahis    = pachis "
	            ."    AND tranum    = pacnum "
	            ."    AND traegr    is null "

	            ."  UNION ALL "

	            ." SELECT trahab, trahis, tranum, pacnom, pacap1, pacap2 "
	            ."   FROM inmtra, inpaci "
	            ."  WHERE trahis    = pachis "
	            ."    AND tranum    = pacnum "
	            ."    AND traegr    is null "
	            ."  GROUP BY 1,2,3,4,5,6 "
	            ."  ORDER BY 1 ";


	     echo "<tr>";
	     echo "<th bgcolor=#fffffff>Habitacion</th>";
	     echo "<th bgcolor=#fffffff>Historia</th>";
	     echo "<th bgcolor=#fffffff>Ingreso</th>";
	     echo "<th bgcolor=#fffffff colspan=2>Paciente</th>";
	     echo "</tr>";

	     $res = odbc_do($conexunix,$query);


	     echo "<option selected>. </option>";

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

			      echo "<td align=center bgcolor=#99FFCC><font size=2><b><A href='admon_med_mat_res.php?whis=".$whis."&amp;wing=".$wing."&amp;wpac=".$wpac."&amp;whab=".$whab."'>Imprimir</A></b></font></td>";
			      echo "</tr>";

			      $whabant = $whab;
		         }
		       }

		 echo "</table>";
		 echo "<br>";
		 echo "<center><table border=1 width=400 BACKGROUND=.'nubes.gif'>";
		 echo "<tr><td bgcolor=#99FFCC><b>Ingrese la Historia :</b><INPUT TYPE='text' NAME='whis' SIZE=10></td>";
		 echo "<td bgcolor=#99FFCC><b>Nro de Ingreso :</b><INPUT TYPE='text' NAME='wing' SIZE=10></td></tr>";
		 echo "<center><tr><td align=center colspan=6 bgcolor=#cccccc></b><input type='submit' value='ACEPTAR'></b></td></tr></center>";
		 echo "</table>";
	   }
	else
	   /********************************
	   * TODOS LOS PARAMETROS ESTAN SET*
	   *********************************/
	   {
		if (isset($whis) and isset($wing) and $whis != "" and $wing != "" )
		   {
			//Aca traigo los datos de la historia seleccionada
			$query = " SELECT trahab, pacnom, pacap1, pacap2, pacfec, min(traing), traser, max(traegr) "
		            ."   FROM inmtra, inpac "
		            ."  WHERE trahis    = pachis "
		            ."    AND tranum    = pacnum "
		            ."    AND traegr    is null "
		            ."    AND pachis = ".$whis
		            ."    AND pacnum = ".$wing
		            ."  GROUP BY 1,2,3,4,5,7 "

		            ."  UNION ALL "

		            ." SELECT trahab, pacnom, pacap1, pacap2, pacfec, min(traing), traser, max(traegr) "
		            ."   FROM inmtra, inpac "
		            ."  WHERE trahis    = pachis "
		            ."    AND tranum    = pacnum "
		            ."    AND traegr    is not null "
		            ."    AND pachis = ".$whis
		            ."    AND pacnum = ".$wing
		            ."    AND pachis not in (SELECT trahis "
		            ."                         FROM inmtra "
		            ."                        WHERE trahis = pachis "
		            ."                          AND tranum = pacnum "
		            ."                          AND traegr is null ) "
		            ."  GROUP BY 1,2,3,4,5,7 "

		            ."  UNION ALL "

		            ." SELECT trahab, pacnom, pacap1, pacap2, pacing, min(traing), traser, max(traegr) "
		            ."   FROM inmtra, inpaci "
		            ."  WHERE trahis    = pachis "
		            ."    AND tranum    = pacnum "
		            ."    AND pachis = ".$whis
		            ."    AND pacnum = ".$wing
		            ."  GROUP BY 1,2,3,4,5,7 "
		            ."  ORDER BY 1 ";

		    $res = odbc_do($conexunix,$query);

		   if (odbc_fetch_row($res))
		       {
			    $whab       = odbc_result($res,1);
				$wnom       = odbc_result($res,2);
				$wap1       = odbc_result($res,3);
				$wap2       = odbc_result($res,4);
				$wfecing    = odbc_result($res,5);
				$wfecinghab = odbc_result($res,6);
				$wser       = odbc_result($res,7);
				$wfecegr    = odbc_result($res,8);

				if ($wfecegr == "")
				    $wfecegr = " ";

			    $wentrar = "S";
		       }
			  else
			     {
			      echo "<br><center><b>LA HISTORIA Y EL INGRESO NO EXISTEN</b></center><br>";
			      $wentrar = "N";
			      unset ($whis);
			      unset ($wing);
		         }
	       }
	      else
	         {
		      echo "<br><center><b>DEBE INGRESAR LA HISTORIA Y EL NUMERO DE INGRESO A REVISAR</b></center><br>";
			  $wentrar = "N";
		     }


		if ($wentrar == "S")
	       {
		    //echo "<center><table border=1  BORDERCOLOR='#000000' CELLSPACING=0></center>";
			echo "<center><table border=1  BORDERCOLOR='#000000'></center>";
			echo "<tr>";
			echo "<td rowspan=2><IMG SRC='/matrix/images/medical/root/clinica.jpg' ALIGN=LEFT><CENTER>&nbsp</CENTER></td>";
		    echo "<td align=center colspan=2><FONT size=4 text color=#000000>RESUMEN DE APLICACION Y ADMINISTRACION DE</FONT></td><td align=center>CODIGO</td>";
		    echo "</tr>";
		    echo "<tr>";
		    echo "<td align=center colspan=2><FONT size=4 text color=#000000>INSUMOS Y MEDICAMENTOS</FONT></td><td align=center>FA-GSh-01-06</td>";
		    echo "</tr>";

		    //Esto lo hago para poder hacer la actualizacion del chequeo antes de consultar los articulos de la historia
			//y asi poder mostrar los articulos si estan chequeados o no, sin tener que salirse de la pagina
			//Se hace el llamado de la pagina enviando tambien como parametro o variable oculta 'wnr' y preguntando si esta setiada
			if (isset($wnr))
				for ($j=1;$j<=$wnr;$j++)
			       {
				    if (isset($aprobado[$j]))
				       {
					    $q = "   UPDATE invetras_000003 "
				            ."      SET aprobado = 'on' "
				            ."    WHERE historia = ".$whis
				            ."      AND ingreso  = ".$wing
				            ."      AND articulo = '".$warticulo[$j]."'"
				            ."      AND descripcion = '".$wdesc[$j]."'"
				            ."      AND cco = '".$wccog[$j]."'";

				        $res4 = mysql_query($q,$conex);
			           }
		           }


	        $q =  " SELECT sernom "
			     ."   FROM inser "
			     ."  WHERE sercod = '".$wser."'";

			$res = odbc_do($conexunix,$q);
			$wnomser = odbc_result($res,1);

			/*
			$q =  " SELECT pacnom, pacap1, pacap2 "
			     ."   FROM inpac "
			     ."  WHERE pachis = ".$whis
			     ."    AND pacnum = ".$wing;

			$res = odbc_do($conexunix,$q);
			$wnom = odbc_result($res,1);
			$wap1 = odbc_result($res,2);
			$wap2 = odbc_result($res,3);
			*/

	        echo "<tr>";
			echo "<td bgcolor=#fffffff><b>HISTORIA N° : </b>".$whis." - ".$wing."</td>";
	        echo "<td bgcolor=#fffffff><b>SERVICIO : </b>".$wnomser."</td>";
	        echo "<td bgcolor=#fffffff colspan=2><b>CAMA : </b>".$whab."</td>";
	        echo "</tr>";

	        echo "<tr>";
			echo "<td bgcolor=#fffffff colspan=4><b>PACIENTE : </b>".$wnom." ".$wap1." ".$wap2."<b> || INGRESO : </b>".$wfecing."<b> || EGRESO : </b>".$wfecegr."</td>";
	        echo "</tr>";
	        echo "</table>";

	        echo "<br>";
	        echo "<br>";

	        echo "<center><table border=0  BORDERCOLOR='#000000'></center>";

	        $q = " SELECT articulo, descripcion, cco, sum(cantidad), aprobado "
	            ."   FROM invetras_000003 "
			    ."  WHERE historia = ".$whis
			    ."    AND ingreso  = ".$wing
			    ."    AND activo = 'S' "
			    ."    AND articulo != '999' "
			    ."  GROUP BY articulo, descripcion, cco, aprobado "
			    ."  ORDER BY articulo ";

	        $res3 = mysql_query($q,$conex);
	        $wnr = mysql_num_rows($res3);

	        echo "<tr>";
	        echo "<th bgcolor=#ffcc66><font size=1>CODIGO</font></th>";
	        echo "<th bgcolor=#ffcc66><font size=1>INSUMO</font></th>";
	        echo "<th bgcolor=#ffcc66 colspan=1><font size=1>Unidad <SIZE=15></font></th>";
	        echo "<th bgcolor=#ffcc66><font size=1>SERVICIO O UNIDAD</font></th>";
	        echo "<th bgcolor=#ffcc66><font size=1>CANTIDAD</font></th>";
	        echo "<th bgcolor=#ffcc66><font size=1>APROBADO</font></th>";

	        $i=1;
	        $wuni="";
	        $wart="";
	        $j=1;
	        $iart=0; //Para controlar la cantidad de servicios por articulo y poder mostrar el total general del articulo
		    while ($i <= $wnr)
		         {
			      $row = mysql_fetch_row($res3);
			      if ($row[0] != "999")
			         {
				      $q =  " SELECT artnom, artuni "
				           ."   FROM ivart "
				           ."  WHERE artcod = '".$row[0]."'";
				      $res = odbc_do($conexunix,$q);
				      $wuni = odbc_result($res,2);
		             }

		          //Aca traigo la cantidad de registros por cada articulo, para poder mostrar el mismo articulo en varias filas, pero una sola vez
		          /////////////////////////////////////////////////////////////////////
		          $q = " SELECT count(*) "
	                  ."   FROM invetras_000003 "
			          ."  WHERE historia = ".$whis
	                  ."    AND ingreso  = ".$wing
			          ."    AND activo = 'S' "
			          ."    AND articulo = '".$row[0]."'"
			          ."  GROUP BY cco "
			          ."  ORDER BY cco ";

	              $rescco = mysql_query($q,$conex);
	              $wregcco = mysql_num_rows($rescco);

	              if ($wregcco > 1)
	                 $wregcco = $wregcco +1; //Esto lo hago para poder adicioanr la linea de total articulo

	              $warticulo[$i] = $row[0];
			      $wdesc[$i] = $row[1];
			      $wccog[$i] = $row[2];

			      echo "<tr>";
			      if ($wart != $row[0])
			         {
				      $iart=0;
				      $wtotart = 0;   //Cada que se muestra un articulo nuevo inicio el total en cero
				      if (is_integer($j/2))
			             $wcolor = "99ccff";
			            else
			               $wcolor = "dddddd";

			          echo "<td bgcolor=".$wcolor." rowspan=".$wregcco."><font size=1>".$row[0]."</font></td>";
			          echo "<td bgcolor=".$wcolor." rowspan=".$wregcco."><font size=1>".$row[1]."</font></td>";
			          echo "<td bgcolor=".$wcolor." rowspan=".$wregcco."><font size=1>".$wuni."</font></td>";
			          $wart=$row[0];
			          $j=$j+1;
			          if ($wregcco > 1)
			             $wregcc = $wregcco -1;
			            else
			               $wregcc = 0;
		             }

		          $iart = $iart +1;

		          $q =  " SELECT cconom "
			           ."   FROM cocco "
			           ."  WHERE ccocod = '".$row[2]."'";  //Aca traigo el nombre del centro de costo, para mostrarlo en el listado
			      $res = odbc_do($conexunix,$q);
			      $wnomcco = odbc_result($res,1);

			      echo "<td bgcolor=".$wcolor."><font size=1>".$wnomcco."</font></td>";
			      echo "<td align=center bgcolor=".$wcolor."><font size=1>".$row[3]."</font></td>";

			      //Sumo las cantidades de cada articulo siempre y cuando esten en mas de un servicio
			      if ($wregcc > 1)
			         $wtotart = $wtotart + $row[3];

			      if ($row[4] == "on")
			         echo "<td align=center bgcolor=".$wcolor."><font size=1><INPUT TYPE=CHECKBOX NAME='aprobado[".$i."]' CHECKED></font></td>";
		            else
			           echo "<td align=center bgcolor=".$wcolor."><font size=1><INPUT TYPE=CHECKBOX NAME='aprobado[".$i."]'</font></td>";

                  $i=$i+1;

                  if ($iart == $wregcc)
                     {
	                  echo "</tr>";
	                  echo "<tr>";
	                  echo "<td bgcolor=".$wcolor."><font size=1><b>TOTAL ARTICULO: </font></b></td>";
			          echo "<td align=center bgcolor=".$wcolor."><b><font size=1>".$wtotart."</font></b></td>";
			          echo "<td align=center bgcolor=".$wcolor."><b><font size=1>&nbsp</font></b></td>";
			          //$iart=0;
			         }


			      echo "</tr>";
		         }
		    echo "</table>";

		    echo "<br>";

		    //De aca en adelante envio todas la variables ocultas para poder refrescar la pantalla
		    $i=1;
		    while ($i <= $wnr)
		        {
		         echo "<input type='HIDDEN' NAME= 'warticulo[".$i."]' value='".$warticulo[$i]."'>";
		         echo "<input type='HIDDEN' NAME= 'wdesc[".$i."]' value='".$wdesc[$i]."'>";
		         echo "<input type='HIDDEN' NAME= 'wccog[".$i."]' value='".$wccog[$i]."'>";
		         $i=$i+1;
	            }

		    //echo "<input type='HIDDEN' NAME= 'wcco' value='".$wcco."'>";
		    echo "<input type='HIDDEN' NAME= 'whis' value='".$whis."'>";
		    echo "<input type='HIDDEN' NAME= 'wing' value='".$wing."'>";
		    echo "<input type='HIDDEN' NAME= 'whab' value='".$whab."'>";
		    echo "<input type='HIDDEN' NAME= 'wnr'  value=".$wnr.">";


		    echo "<br><center><font bgcolor=#993300>Para que la revisión tenga efecto debe pulsar 'Click' en el botón ACEPTAR</font></center><br>";
		    echo "<center><tr><td align=center colspan=6 bgcolor=#cccccc></b><input type='submit' value='ACEPTAR'></b></td></tr></center>";
	       }
	   }
	   echo "<br>";
	   echo "<center><font size=3><A href='admon_med_mat_res.php'> Retornar</A></font></center>";
	   echo "</form>";
	   
		odbc_close($conexunix);
		odbc_close_all();
}
include_once("free.php");
?>
