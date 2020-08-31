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

    echo "<form action='admon_med_mat.php' method=post>";

    if (!isset($whis) or !isset($wing))
       {
	    echo "<center><table border=1 BACKGROUND=.'nubes.gif'>";
        echo "<tr><td align=center colspan=5 bgcolor=#fffffff><font size=5 text color=#CC0000><b>CLINICA LAS AMERICAS</b></font></td></tr>";
        echo "<tr><td align=center colspan=5 bgcolor=#fffffff><font size=2 text color=#CC0000><b>REPORTE APLICACION Y ADMINISTRACION DE INSUMOS Y MEDICAMENTOS A PACIENTES</b></font></td></tr>";
        //echo "<tr><td align=center colspan=5 bgcolor=#fffffff><font size=4 text color=#CC0000><b>CONSULTA DE HORAS DE APLICACION</b></font></td></tr>";

	    ////Traigo el centro costo a partir del usuario que ingreso
	    //$query = " SELECT percco "
	    //        ."   FROM noper "
	    //      //."  WHERE percod = '".$wuser."'"
	    //        ."  WHERE percod = '03395'"      // 01074
	    //        ."    and peretr = 'A' ";
	    //$res = odbc_do($conexunix,$query);
	    //while(odbc_fetch_row($res))
	    //     $wcco = odbc_result($res,1);

	    /*
	    $query = " SELECT trahab, trahis, tranum, pacnom, pacap1, pacap2 "
	            ."   FROM inmtra, insercco, inpac "
	            ."  WHERE serccocco = '".$wcco."'"
	            ."    AND traser    = serccoser "
	            ."    AND trahis    = pachis "
	            ."    AND tranum    = pacnum "
	            ."    AND traegr    is null "
	            ."  GROUP BY 1,2,3,4,5,6 "
	            ."  ORDER BY 1 ";
	     */

	     $query = " SELECT trahab, trahis, tranum, pacnom, pacap1, pacap2, traser "
	            ."   FROM inmtra, inpac "
	            ."  WHERE trahis    = pachis "
	            ."    AND tranum    = pacnum "
	            ."    AND traegr    is null "
	            ."  GROUP BY 1,2,3,4,5,6,7 "
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
		       $wser = odbc_result($res,7);

		       if ($whabant != $whab)
		         {
			      echo "<tr>";
			      echo "<td align=center bgcolor=#99FFCC><font size=1><b>".$whab."</b></font></td>";
			      echo "<td align=center bgcolor=#99FFCC><font size=1><b>".$whis."</b></font></td>";
			      echo "<td align=center bgcolor=#99FFCC><font size=1><b>".$wing."</b></font></td>";
			      echo "<td align=left bgcolor=#99FFCC><font size=1><b>".$wpac."</b></font></td>";

			      echo "<td align=center bgcolor=#99FFCC><font size=2><b><A href='admon_med_mat.php?wser=".$wser."&amp;whis=".$whis."&amp;wing=".$wing."&amp;wpac=".$wpac."&amp;whab=".$whab."'>Imprimir</A></b></font></td>";
			      echo "</tr>";

			      $whabant = $whab;
		         }
		       }
		 echo "</table>";

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
	   * TODOS LOS PARAMETROS ESTAN SET *
	   ********************************/
	   {                                // #0066FF
		echo "<table border=1  BORDERCOLOR='#000000' CELLSPACING=0>";
		echo "<tr>";
		echo "<td rowspan=2><IMG SRC='/matrix/images/medical/root/clinica.jpg' ALIGN=LEFT><CENTER>&nbsp</CENTER></td>";
        echo "<td align=center colspan=24><FONT size=5 text color=#000000>APLICACION Y ADMINISTRACION DE</FONT></td><td align=center colspan=4>CODIGO</td>";
        echo "</tr>";                                          // #CC0000
        echo "<tr>";                                           // #CC0000
        echo "<td align=center colspan=24><FONT size=5 text color=#000000>INSUMOS Y MEDICAMENTOS</FONT></td><td align=center  colspan=4>FA-GSh-01-06</td>";
        echo "</tr>";

        $q =  " SELECT sernom "
		     ."   FROM inser"
		     ."  WHERE sercod = '".$wser."'";

		$res = odbc_do($conexunix,$q);
		$wnomser = odbc_result($res,1);

		$q =  " SELECT pacnom, pacap1, pacap2 "
		     ."   FROM inpac "
		     ."  WHERE pachis = ".$whis
		     ."    AND pacnum = ".$wing;

		$res = odbc_do($conexunix,$q);
		$wnom = odbc_result($res,1);
		$wap1 = odbc_result($res,2);
		$wap2 = odbc_result($res,3);

        echo "<tr>";
		echo "<td bgcolor=#fffffff colspan=3><b>HISTORIA N° : </b>".$whis." - ".$wing."</td>";
        echo "<td bgcolor=#fffffff colspan=16><b>SERVICIO : </b>".$wnomser."</td>";
        echo "<td bgcolor=#fffffff colspan=10<b>CAMA : </b>".$whab."</td>";
        echo "</tr>";

        echo "<tr>";
		echo "<td bgcolor=#fffffff colspan=12><b>APELLIDOS : </b>".$wap1." ".$wap2."</td>";
        echo "<td bgcolor=#fffffff colspan=17><b>NOMBRES : </b>".$wnom."</td>";
        echo "</tr>";

        $q = " SELECT articulo, fecha_data, ronda, sum(cantidad), descripcion "
            ."   FROM invetras_000003 "
		    ."  WHERE historia = ".$whis
		    ."    AND ingreso  = ".$wing
		    ."    AND activo = 'S' "
		    ."  GROUP BY fecha_data, articulo, ronda, descripcion "
		    ."  ORDER BY fecha_data, articulo, ronda ";

        $res3 = mysql_query($q,$conex);
        $wnr = mysql_num_rows($res3);

	    //Inicializo la MATRIZ a donde voy a llevar todo lo que le aplicaron al paciente en la estadia
	    for ($j=0;$j<=$wnr;$j++)
	       {
	        for ($l=0;$l<=24;$l++)     //24 horas que tiene el dia
	           {
	            $Mrondas[$j][$l]=0;    //Aca almaceno las cantidades de cada articulo segun la ronda
		       }
		    $Afechas[$j]=0;            //Aca llevo las fecha de aplicacion
		    $Aarticulos[$j]=0;         //Aca llevo el codigo del articulo de acuerdo a la hora de la ronda
		    $Adesc[$j]="";             //Aca llevo el codigo del articulo de acuerdo a la hora de la ronda
		   }

	    $j=1;
	    $i=1;
	    $row = mysql_fetch_row($res3);

	    while ($j <= $wnr)
	       {
		    $wfec = $row[1];
		    $wart = $row[0];
		    $wdesc = $row[4];

		    $Afechas[$i] = $row[1];
		    $Aarticulos[$i] = $row[0];
		    $Adesc[$i] = $row[4];

		    while ($wart == $row[0] and $wfec == $row[1] and $wdesc == $row[4])
		        {
			     $wronda=substr($row[2],0,(strpos($row[2],"-")-1));  //Traigo la parte numerica de la ronda unicamente, para ubicar la cantidad en la ronda que es
			     $wampm=substr($row[2],(strpos($row[2],"-")+1),strlen($row[2]));
			     if (trim($wampm) == "PM")
			        $wronda = $wronda+12;

		         $Mrondas[$i][$wronda]=$row[3];
		         $row = mysql_fetch_row($res3);
		         $j=$j+1;
	            }
	        $i=$i+1;
	       }


	    $t=$i;

	    echo "<tr>";
        echo "<th bgcolor=#fffffff><font size=1>FECHA</font></th>";
        echo "<th bgcolor=#fffffff><font size=1>CODIGO</font></th>";
        echo "<th bgcolor=#fffffff><font size=1>INSUMO</font></th>";
        echo "<th bgcolor=#fffffff><font size=1>Unidad</font></th>";
        $i=1;
        while ($i <= 24)
             {
	          if ($i <= 12)
	             echo "<th bgcolor=#CCCCCC><font size=1>".$i." AM"."</font></th>";
	            else
	               echo "<th bgcolor=#99FFFF><font size=1>".($i-12)." PM"."</font></th>";
	          $i=$i+1;
             }
        echo "<th bgcolor=#fffffff><font size=1>TOTAL</font></th>";
	    echo "</tr>";

	    $wfec="";
	    $i=1;
	    while ($i < $t)
	         {
		      $wuni=".";
		      $wnomart=$Adesc[$i];
		      if ($Aarticulos[$i] != "999")
		         {
			      $q =  " SELECT artnom, artuni "
			           ."   FROM ivart "
			           ."  WHERE artcod = '".$Aarticulos[$i]."'";
			      $res = odbc_do($conexunix,$q);
			      $wnomart = odbc_result($res,1);
			      $wuni = odbc_result($res,2);
                 }

		      $q = " SELECT count(*) "
                  ."   FROM invetras_000003 "
		          ."  WHERE historia   = ".$whis
		          ."    AND ingreso    = ".$wing
		          ."    AND fecha_data = '".$Afechas[$i]."'"
		          ."    AND activo     = 'S' "
		          ."  GROUP BY descripcion "; //Se agrupa por decripcion porque si se hace por codigo, falla el reporte para los medicamentos traidos por los pacientes porque estos tienen el mismo codigo pero diferente descripcion

		      $res3 = mysql_query($q,$conex);
              $wfilas = mysql_num_rows($res3);

		      echo "<tr>";
		      if ($wfec != $Afechas[$i])
		         {
		          echo "<td rowspan=".$wfilas." align=center><font size=2>".$Afechas[$i]."</font></td>";
		          $wfec = $Afechas[$i];
	             }
	          echo "<td><font size=1>".$Aarticulos[$i]."</font></td>";
		      echo "<td><font size=1>".$wnomart."</font></td>";
		      echo "<td><font size=1>".$wuni."</font></td>";
		      $j=1;
		      $wtotal=0;
		      while ($j <= 24)
		         {
			      if ($Mrondas[$i][$j] == 0)
			         echo "<td align=center><font size=1>&nbsp</font></td>";
			        else
			           {
			            echo "<td align=center bgcolor=#dddddd><font size=1>".$Mrondas[$i][$j]."</font></td>";
			            $wtotal = $wtotal + $Mrondas[$i][$j];
		               }
			      $j=$j+1;
		         }
		      $i=$i+1;
		      echo "<td align=center bgcolor=#dddddd><font size=1>".$wtotal."</font></td>";
		      echo "</tr>";
	         }
	    echo "</table>";
	    echo "<br>";
	   }
	   //echo "<font size=3><A href='admon_med_mat.php?whis=".$whis."&amp;wing=".$wing."&amp;wcco=".$wcco."&amp;wpac=".$wpac."&amp;whab=".$whab."'> Retornar</A></font>";
	   echo "<font size=3><A href='admon_med_mat.php'> Retornar</A></font>";
	   
	odbc_close($conexunix);
	odbc_close_all();
}
//}
include_once("free.php");
?>
