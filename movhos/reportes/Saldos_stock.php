<head>
  <title>SALDOS DEL STOCK POR SERVICIO</title>
</head>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<body>
<script type="text/javascript">
	function cerrarVentana()
	 {
      window.close()
     }

	 function ordenar(tipo_orden){

		var coo = $('#cco').val();
		var wemp_pmla = $('#wemp_pmla').val();
		window.location.href = 'saldos_stock.php?wemp_pmla='+wemp_pmla+'&wcco='+coo+'&ordenar='+tipo_orden+'';

	 }
</script>
<?php
include_once("conex.php");
  /*************************************
   *   SALDOS DEL STOCK POR SERVICIO   *
   *     	CONEX, FREE => OK		   *
   *************************************/
   //=========================================================================================================================
   //Junio 01 de 2015 (Felipe Alvarez)
   //Se agrega la columna Nombre generico del articulo
   //=========================================================================================================================
   //Marzo 05 de 2014 (Jonatan)
   // Se agrega la columna de ubicacion del articulo, se agrega ordenar por ubicacion y por descripcion.
   //=========================================================================================================================
   //Julio 23 de 2013 (Jonatan)
   //Se agrega un union a la consulta unix para verificar si el campo artreg es nulo y permita mostrar el reporte.
   //=========================================================================================================================
   //Julio 22 de 2013 (Jonatan)
   //Se agrega la columna Registro Invima al reporte despues del codigo del articulo.
   //=========================================================================================================================

session_start();

if (!isset($user))
	if(!isset($_SESSION['user']))
	  session_register("user");


if(!isset($_SESSION['user']))
	echo "error";
else
{

  

  

  include_once("root/comun.php");

  $conexunix = odbc_connect('inventarios','informix','sco')
  					    or die("No se ralizo Conexion con el Unix");


  	                                         // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
  $wactualiz="2015-06-01";                   // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                         // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //


  echo "<input type='HIDDEN' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";


  echo "<form name='saldos' action='Saldos_stock.php' method=post>";

  if (strpos($user,"-") > 0)
     $wuser = substr($user,(strpos($user,"-")+1),strlen($user));

  //Traigo TODAS las aplicaciones de la empresa, con su respectivo nombre de Base de Datos
  $q = " SELECT detapl, detval, empdes "
      ."   FROM root_000050, root_000051 "
      ."  WHERE empcod = '".$wemp_pmla."'"
      ."    AND empest = 'on' "
      ."    AND empcod = detemp ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $num = mysql_num_rows($res);

  if ($num > 0 )
     {
	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res);

	      if ($row[0] == "cenmez")
	         $wcenmez=$row[1];

	      if ($row[0] == "afinidad")
	         $wafinidad=$row[1];

	      if ($row[0] == "movhos")
	         $wbasedato=$row[1];

	      if ($row[0] == "tabcco")
	         $wtabcco=$row[1];
         }
     }
    else
       echo "NO EXISTE NINGUNA EMPRESA DEFINIDA PARA ESTE CODIGO";

  $winstitucion=$row[2];


  encabezado("SALDOS DEL STOCK POR SERVICIO",$wactualiz, "clinica");



  if (!isset($wcco) or (trim($wcco)==""))
     {
	  echo '<center><h2 class="seccion1"><b>INTRODUZCA EL CENTRO DE COSTO:</b></h2>';

	  echo "<center><table>";

	  if ($wuser=="03150")
	     {
	      ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	      //ACA TRAIGO LOS DESTINOS DIGITADOS EN LA TABLA DE MATRIX
	      $q = " SELECT ".$wtabcco.".ccocod, ".$wtabcco.".cconom "
	          ."   FROM ".$wtabcco.", ".$wbasedato."_000011"
	          ."  WHERE ".$wtabcco.".ccocod = ".$wbasedato."_000011.ccocod ";
	      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	      $num = mysql_num_rows($res);

		  echo "<tr><td align=center><select name='wcco'>";
		  echo "<option>&nbsp</option>";
		  for ($i=1;$i<=$num;$i++)
		     {
		      $row = mysql_fetch_array($res);
		      echo "<option>".$row[0]." - ".$row[1]."</option>";
	         }
	      echo "</select></td></tr>";
         }
        else
          {
	       ?>
			 <script>
			     function ira(){document.saldos.wcco.focus();}
			 </script>
		   <?php
           echo "<tr class=seccion1><td align=center><INPUT TYPE='password' NAME='wcco' SIZE=7></td></tr>";
          }

      echo "<input type='HIDDEN' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";

	  echo "<tr><td align=center><input type='submit' value='ENTRAR'></td></tr>";

	  echo "</table>";
	 }
   else
     {

	  echo "<input type='HIDDEN' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";

	  if (strpos($wcco,"-") > 0)
	     {
	      $wccosto=explode("-",$wcco);
	      $wcco=$wccosto[0];
         }
        else
          {
           if (strpos($wcco,".") > 0)
	         {
	          $wccosto=explode(".",$wcco);
	          $wcco=$wccosto[1];
	         }
	      }

	  $q = " SELECT cconom "
          ."   FROM ".$wtabcco
          ."  WHERE ccocod = '".$wcco."'";
      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $row = mysql_fetch_array($res);
      $wnomcco=$row[0];

	  echo "<input type='HIDDEN' name='cco' id='cco' value='".$wcco."'>";
      if (trim($wnomcco)=="")
        {
         ?>
	      <script>
	       alert ("EL CENTRO DE COSTO NO FUE INGRESADO POR CODIGO DE BARRAS");
	      </script>
	     <?php
	    }

	  //Consulta de unix todos los articulos del centro de costos seleccionado.
	  $q_ubi =   " SELECT artubiart, artubiubi "
				."   FROM ivartubi "
				."  WHERE artubiser = '".$wcco."'";
	  $res_ubi = odbc_do($conexunix,$q_ubi);

	  $array_articulos_cco_ubi = array();

	  //Se crea un arreglo con el codigo del articulo y la ubicacion.
	  while(odbc_fetch_row($res_ubi)){

		  if(!array_key_exists(odbc_result($res_ubi,1), $array_articulos_cco_ubi))
			{
				$array_articulos_cco_ubi[odbc_result($res_ubi,1)] = array('codigo' => odbc_result($res_ubi,1), 'ubicacion' => odbc_result($res_ubi,2));
			}

	  }


	  $q = " SELECT salart, artnom, saluni, uninom, (salant+salent-salsal) AS saldo, artreg,artgen "
	      ."   FROM ivsal, ivart, ivuni "
	      ."  WHERE salser                  = '".$wcco."'"
	      ."    AND salano                  = '".date("Y")."'"
	      ."    AND salmes                  = '".date("m")."'"
	      ."    AND (salant+salent-salsal) != 0 "
	      ."    AND salart                  = artcod "
	      ."    AND artuni                  = unicod "
		  ."	AND artreg is not null"
		  ."  UNION "
		  ." SELECT salart, artnom, saluni, uninom, (salant+salent-salsal) AS saldo, '' as artreg ,artgen"
	      ."   FROM ivsal, ivart, ivuni "
	      ."  WHERE salser                  = '".$wcco."'"
	      ."    AND salano                  = '".date("Y")."'"
	      ."    AND salmes                  = '".date("m")."'"
	      ."    AND (salant+salent-salsal) != 0 "
	      ."    AND salart                  = artcod "
	      ."    AND artuni                  = unicod "
		  ."	AND artreg is null"
	      ."  ORDER BY 2 ";
	  $res2 = odbc_do($conexunix,$q);

	  $array_articulos_cco = array();

	  //Se crea un arreglo con la anterior consulta y se le agrega la posicion desde el arreglo $array_articulos_cco_ubi.
	  while(odbc_fetch_row($res2)){

		  if(!array_key_exists(odbc_result($res2,1), $array_articulos_cco))
			{

				$wubicacion = '';
				//Si el articulo no esta en el arreglo asignara el valor de ubicacion vacia.
				if(array_key_exists(odbc_result($res2,1), $array_articulos_cco_ubi ))
				{
				$wubicacion = $array_articulos_cco_ubi[odbc_result($res2,1)]['ubicacion'];
				}
				if($ordenar != 'ubicacion'){
				$array_articulos_cco[odbc_result($res2,1)] = array( 'descripcion' => odbc_result($res2,2), 'codigo' => odbc_result($res2,1), 'ubicacion'=>$wubicacion, 'reginv' => odbc_result($res2,6),'unidad' => odbc_result($res2,4) , 'presentacion'=> odbc_result($res2,3), 'saldo_actual'=> odbc_result($res2,5), 'nom_generico'=> odbc_result($res2,7));
				}else{
				$array_articulos_cco[odbc_result($res2,1)] = array( 'ubicacion'=>$wubicacion, 'reginv' => odbc_result($res2,6), 'descripcion' => odbc_result($res2,2), 'codigo' => odbc_result($res2,1),'unidad' => odbc_result($res2,4) , 'presentacion'=> odbc_result($res2,3), 'saldo_actual'=> odbc_result($res2,5),  'nom_generico'=> odbc_result($res2,7));
				}
			}

	  }


	  echo "<center><table>";

	  echo "<tr class=seccion1>";
	  echo "<td colspan=8 align=center><b>Servicio o Unidad: </b>".$wnomcco."</td>";
	  echo "</tr>";

	  echo "<tr class=encabezadoTabla>";
	  echo "<th>Código</th>";
	  echo "<th>Ubicación<a onclick='ordenar(\"ubicacion\");'><img src='/matrix/images/medical/iconos/gifs/i.p.next[1].gif' ></a></th>";
	  echo "<th>Registro Invima</th>";
	  echo "<th>Descripción<a onclick='ordenar(\"descripcion\");'><img src='/matrix/images/medical/iconos/gifs/i.p.next[1].gif' ></a></th></th>";
	  echo "<th>Generico</th>";
	  echo "<th>Presentación</th>";
	  echo "<th>Saldo Actual</th>";
	  echo "</tr>";

	  //Ordena el arreglo por la primera posicion en este caso la ubicacion.
	  asort($array_articulos_cco);

	  $i=1;
	  foreach($array_articulos_cco as $k => $v)
	     {
		  if ($i % 2 == 0)
		    {
		     $wclass = "fila1";
		    }
		   else
		     {
		      $wclass = "fila2";
		     }


	      echo "<tr class=".$wclass.">";
	      echo "<td>".$v['codigo']."</td>";
		  echo "<td>".$v['ubicacion']."</td>";
		  echo "<td>".$v['reginv']."</td>";
	      echo "<td>".$v['descripcion']."</td>";
	      echo "<td nowrap='nowrap'>".$v['nom_generico']."</td>";
	      echo "<td>".$v['presentacion']."</td>";
	      echo "<td align=right>".$v['saldo_actual']."</td>";

	      $i++;
	    }

	  echo "</table>";
	  echo "<br>";
	  echo "<center><table>";
	  echo "<font size=3><A href=saldos_stock.php?wemp_pmla=".$wemp_pmla."> Retornar</A></font>";
	  echo "</table>";
	 }

	 echo "<input type='HIDDEN' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";

	 echo "</form>";

	 echo "<center><table>";
     echo "<tr><td align=center><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
     echo "</table>";

	odbc_close($conexunix);
	odbc_close_all();

} // if de register



include_once("free.php");

?>
