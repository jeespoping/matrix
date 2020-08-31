<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
 	echo "error";
 else
{

	// ESTE PROGRAMA PORMITE CONSULTAR UNA ORDEN DE TRABAJO DADA LA FUENTE Y EL NRO DE FACTURA

echo "<HTML>";
echo "<HEAD>";
echo "<TITLE>BIENVENIDA</TITLE>";
echo "</HEAD>";
echo "<BODY>";
echo "<center><table border=1>";
echo "<tr><td rowspan=1 colspan=1 align=center ><IMG SRC='/matrix/images/medical/pos/logo_uvglobal.png' ></td>";
echo "</table>";
echo "<br>";
echo "<center><table border=0>";
echo "<tr><td align=center bgcolor=#DDDDDD colspan=1><b><font text color=#003366 size=4><i>UNIDAD VISUAL GLOBAL S.A.</font></b><br>";
echo "<tr><td align=center bgcolor=#DDDDDD colspan=1><b><font text color=#003366 size=2><i>CONSULTA DE ORDENES POR FACTURA</font></b><br>";
echo "<tr><td align=center bgcolor=#DDDDDD colspan=1><b><font text color=#003366 size=2><i>uvglobal03.php Ver. 2008/10/07</font></b><br>";
echo "</table>";
echo "<br>";

echo "<center><table border=0>";


	//

	//or die("No se ralizo Conexion");

	

	mysql_select_db("matrix") or die("No se selecciono la base de datos");

	echo "<form name='uvglobal03' action='uvglobal03.php' method=post>";

	echo "<tr><td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2> <i>FUENTE: </font></b>";
	if (isset($wffa))
     echo "<INPUT TYPE='text' NAME='wffa' size=5  VALUE='".$wffa."'></INPUT>";
    else
     echo "<INPUT TYPE='text' NAME='wffa' size=5 VALUE='20'></INPUT></td>";

	echo "<tr><td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2> <i>FACTURA: </font></b>";
	if (isset($wfac))
     echo "<INPUT TYPE='text' NAME='wfac' size=10 VALUE='".$wfac."'></INPUT></td>";
    else
     echo "<INPUT TYPE='text' NAME='wfac' size=10></INPUT></td>";


	echo "<tr><td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2> <i>VENTA: </font></b>";
	if (isset($wven))
     echo "<INPUT TYPE='text' NAME='wven' size=10 VALUE='".$wven."'></INPUT></td>";
    else
     echo "<INPUT TYPE='text' NAME='wven' size=10></INPUT></td>";


     echo "<tr><td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2> <i>NRO. ORDEN: </font></b>";
     if( isset($wnro) )
     	echo "<INPUT TYPE='text' name='wnro' value='$wnro' size=10>";
     else
     	echo "<INPUT TYPE='text' name='wnro' size=10>";

     echo "<tr><td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2> <i>CEDULA: </font></b>";
     if( isset($wdoc) )
     	echo "<INPUT TYPE='text' name='wdoc' size=10 value='$wdoc'>";
     else
     	echo "<INPUT TYPE='text' name='wdoc' size=10>";

     echo "<tr><td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2> <i>NOMBRE: </font></b>";
     if( isset($wnom) ){
     	echo "<INPUT TYPE='text' name='wnom' size=10 value='$wnom'>";
     }
     else{
     	echo "<INPUT TYPE='text' name='wnom' size=10>";
     }


    // Boton Consultar
    echo "<tr><td align=center colspan=2 bgcolor=#cccccc size=10>";
    echo "<input type='submit' value='Buscar'>";
    echo "</table>";


     echo "<br><br><table align='center'>";

    if( ((isset($wffa) AND isset($wfac)) || isset($wnro) || isset($wdoc) || isset($wnom) || isset($wven) )
    	 && ((!empty($wffa) AND !empty($wfac)) || !empty($wnro) || !empty($wdoc) || !empty($wnom) || !empty($wven))
    )
   	{
   		if( !isset($wffa) || $wffa == "" ){
   			$wffa = "%";
   		}

   		if( !isset($wfac) || $wfac == "" ){
   			$wfac = "%";
   		}

   		if( !isset($wnro) || $wnro == "" ){
   			$wnro = "%";
   		}

   		if( !isset($wdoc) || $wdoc == "" ){
   			$wdoc = "%";
   		}

	   	if( !isset($wnom) || $wnom == "" ){
   			$wnom = "%";
   		}

		if( !isset($wven) || $wven == "" ){
   			$wven = "%";
   		}

	 	if ( ($wffa != "") AND ($wfac != "") )
		{
			echo "<tr>";
		  	echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Orden Nro<b></td>";
			echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Fecha<b></td>";
			echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Cedula<b></td>";
			echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Nombre<b></td>";
			echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Fuente<b></td>";
			echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Factura<b></td>";
			echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Venta<b></td>";
			echo "<td colspan=2 align=center bgcolor=#DDDDDD></td>";
			echo "<td colspan=2 align=center bgcolor=#DDDDDD></td>";
			echo "</tr>";

		    $query = "SELECT "
			       ."	ordnro,orddoc,ordran,ordtus,orddsi,orddes,orddci,orddej,orddad,orddte,ordisi,ordies,ordici,ordiej,ordiad,ordite,"
			       ."	ordled,ordlei,ordedp,ordtra,ordbif,ordmon,ordref,ordmet,ordcom,ordcol,ordpin,ordde1,ordbra,ordde2,ordter,ordde3,"
			       ."	ordpla,ordde4,ordotr,ordde5,ordobs,ordcaj,ordffa,ordfac,ordfec,ordfre,ordfen,ordvel,ordvem,CLINOM,ordinv,ordlot,ordini,ordloi,ordven"
			       ."  FROM "
			       ."	 uvglobal_000133 LEFT JOIN uvglobal_000041"
			       ."    ON uvglobal_000133.orddoc = uvglobal_000041.clidoc"
			       ." WHERE "
			       ." 	 ordffa like '%".$wffa."%' "
			       ."	 AND ordfac like '%".$wfac."%'"
			       ."	 AND orddoc like '%".$wdoc."%'"
			       ."	 AND ordnro like '%".$wnro."%'"
			       ."	 AND clinom like '%".$wnom."%'"
			       ."	 AND ordven like '%".$wven."%'"
			       ." ORDER BY ordfen DESC";
	       $resultado = mysql_query($query);
	       if ($resultado)
	       {
			 $nroreg = mysql_num_rows($resultado);
			 $numcam = mysql_num_fields($resultado);

			$i = 1;
			While ($i <= $nroreg)
			{
		     // color de fondo
		     if (is_int ($i/2))  // Cuando la variable $i es par coloca este color
		      $wcf="DDDDDD";
		   	 else
		   	  $wcf="CCFFFF";

			 $registro = mysql_fetch_array($resultado);
			 echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[0]."</td>";    //Nro de Orden
			 echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[40]."</td>";   //Fecha
			 echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[1]."</td>";    //Cedula
			 echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[45]."</td>";   //Nombre
			 echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[38]."</td>";   //Fuente
			 
			 if($registro[39]=='NO APLICA')
			 {
					echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3></td>";   //Factura
				 
			 }
			 else
			 {
					echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[39]."</td>";   //Factura
				 
			 }
			
			 
			 echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro['ordven']."</td>";   //Factura


		     echo "<td colspan=2 align=center color=#FFFFFF bgcolor=".$wcf.">";
		     // LLAMADO SIN PARAMETROS
		     // echo "<A HREF='uvglobal01.php'>Nuevo</A></td>";

		     // LLAMADO CON PARAMETROS
		     // echo "<A HREF='uvglobal01.php?wnro=".$registro[0]."&wfec=".$registro[1]."&wdoc=".$registro[2]."'>Editar</A></td>";

		     /* SIN EMBARGO COMO EN ESTE CASO SON 45 CAMPOS QUE TENGO QUE ENVIAR COMO PARAMETROS, ENTONCES SI TENGO
		        LA PRECAUCION DE DAR NOMBRES DE LOS CAMPOS EN LA TABLA ASI:       ordnro,orddoc,ordran,... Y SI EN LA FORMA
		        UTILIZO COMO NOMBRE DE VARIABLES PARA MANIPULAR ESTOS  CAMPOS:      wnro,  wdoc,  wran,...
		        ARMO MEDIANTE UN STRING UN href TOMANDO LOS ULTIMOS TRES CARACTERES DE LOS NOMBRES DE LOS CAMPOS
		     */
		        $l="<A HREF='uvglobal01.php?w".substr(mysql_field_name($resultado,0),3)."=".$registro[0];
		        for ($j=1;$j<=$numcam-1;$j++)
				{
		          if($registro[$j] =='NO APLICA')
				  {
					$registro[$j] ='';  
					  
				  }
				  
				  $l = $l."&w".substr(mysql_field_name($resultado,$j),3)."=".$registro[$j];
				}
		        $l = $l."&wproceso=Consultar'>Editar</A></td>";  //Adiciono una columna adicional para indicar que voy a "consultar"

		        $l = $l."<td colspan=2 align=center color=#FFFFFF bgcolor=".$wcf.">";     // Otra que llame el programa que imprime
		        $l = $l."<A HREF='uvglobal02.php?wnro=".$registro[0]."'>Imprimir</A></td>";

		        echo $l;
	            echo "</tr>";
	          // OTRA FORMA SERIA ENVIAR SOLO LOS CAMPOS CLAVES EN ESTE CASO ordnro Y EL PROGRAMA LLAMADO EMPIEZO HACIENDO
	          // UN SELECT PARA LLENAR LAS VARIABLES
	 	      // echo "<A HREF='uvglobal01.php?wnro=".$registro[0]."'>Editar</A></td>";

	          $i++;
		    }
	       }

      }
     }

echo "</table>";
echo "</HTML>";
echo "</BODY>";

}    // De la sesion
?>


