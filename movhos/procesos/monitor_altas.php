<html>
<head>
<title>Monitor Sistema de Altas</title>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
</head>
<font face='arial'>
<BODY>

<script type="text/javascript">
	function enter()
	  {
		document.forms.monitor_altas.submit();
	  }

	function cerrarVentana()
	  {
	    window.close()
	  }
</script>


<?php
include_once("conex.php");

/**
* CONSULTA ESTADO DE HABITACIONES	                                                   *
*/
// ===========================================================================================================================================
// PROGRAMA				      :Programa para monitorear los movimientos de habitaciones en el sistema de altas.                              |
// AUTOR				      :Juan C Hernandez M.                                                                                           |
// FECHA CREACION			  :Noviembre 1 DE 2007.                                                                                          |
// FECHA ULTIMA ACTUALIZACION :03 de Octubre de 2007.                                                                                        |
// DESCRIPCION			      :Este reporte sirve para ver por centro de costos-habitacio y paciente que saldo tiene pendiente de aplicar.   |
//                                                                                                                                           |
// TABLAS UTILIZADAS :                                                                                                                       |
// root_000050       : Tabla de Empresas para escojer empresa y esta traer un campo para saber que centros de costos escojer.                |
// costosyp_000005   : Tabla de Centros de costos de Clinica las Americas, Laboratorio de Patologia, y Laboratorio Medico.                   |
// ==========================================================================================================================================
// Modificaciones
// Sept. 17 de 2015 - Juan C. Hdez
// Si codigo en pantalla (Habcpa) de la habitacion es igual al codigo (Habcod) solo se muestra el (Habcod)
// Sept. 16 de 2015 - Jonatan
// Se muestra en el listado el codigo en pantalla (Habcpa) de la habitacion si no tiene este codigo muestra el codigo (Habcod)
// Nov. 13 2014 - Camilo Zapata
// Se modifica las condiciones de suma en los datos consolidados para que no sume las habitaciones definidas como cubículos, ni las que perte-
// nezcan al centro de costos de urgencias
// Nov. 6 2014 - Juan C. Hdez
// Se modifica el query principal para que NO tome de la tabla movhos_000020 los registros que son tipo Cubiculo
// ==========================================================================================================================================
$wactualiz = "Enero 07 2022";

session_start();
if (!isset($_SESSION['user']))
{
    echo "error";
}
else
{
    $empresa = 'root';

    include_once("root/magenta.php");
    include_once("root/comun.php");

    if (is_null($selectsede)){
        $selectsede = consultarsedeFiltro();
    }

    encabezado("Monitor Sistema de Altas",$wactualiz, "clinica", true);

    $query = " SELECT Detapl,Detval"
     		."   FROM ".$empresa."_000051 "
     		."  WHERE Detemp = '" . $wemp_pmla . "'";
	$err = mysql_query($query, $conex);
    $num = mysql_num_rows($err);

    $empre1 = "";
    $empre2 = "";

    for ($i = 1;$i <= $num;$i++)
    {
        $row = mysql_fetch_array($err);

        IF ($row[0] == 'cenmez')
        {
            $wcenmez = $row[1];
        }
        else
        {
            if ($row[0] == 'movhos')
            {
                $wbasedato = $row[1];
            }
           else
              if ($row[0] == 'tabcco')
	            {
	              $wtabcco = $row[1];
	            }
        }
    }

    echo '<div id="page" align="center">';
    echo '<div id="feature" class="box-orange" align="center">';

    $estadosede=consultarAliasPorAplicacion($conex, $wemp_pmla, "filtrarSede");

    $sFiltroSede = '';

    if($estadosede=='on')
    {
        $sFiltroSede = (isset($selectsede) && ($selectsede !='')) ? " AND Ccosed = '{$selectsede}' " : "";
    }

    $wfecha = date("Y-m-d");
    $q  = " select Ccocod from {$wbasedato}_000011 where ccourg = 'on' and Ccoest = 'on' {$sFiltroSede}";
    $rs = mysql_query( $q, $conex );
    while( $row = mysql_fetch_assoc( $rs ) ){
        $ccourg = $row['Ccocod'];
    }

    $ccourg =

    //Traigo las habitaciones con su respectivo centro de costos
    if ($selectsede != ''){
        $q = " SELECT habcod, habhis, habing, habali, habdis, habest, habfal, habhal, habpro, habcco, cconom, habcub, habcpa "
            . "   FROM ".$wbasedato."_000020, ".$wbasedato."_000011"
            ."   WHERE habcco = ccocod and Ccosed = '".$selectsede."'"
            . "  ORDER BY 11, 1 ";
    }else{
        $q = " SELECT habcod, habhis, habing, habali, habdis, habest, habfal, habhal, habpro, habcco, cconom, habcub, habcpa "
            . "   FROM ".$wbasedato."_000020, ".$wbasedato."_000011"
            ."   WHERE habcco = ccocod"
            . "  ORDER BY 11, 1 ";
    }

       //. "  WHERE habcod = '".$whab."'";
    $res = mysql_query($q, $conex) or die("ERROR EN QUERY");
    $wnum = mysql_num_rows($res);


    echo '<div class="content">';
    echo "<center><input type='HIDDEN' id='wemp_pmla' name= 'wemp_pmla' value='".$wemp_pmla."'>";
    echo '<table align=center>';


    if ($wnum > 0)
       {
        echo "<br>";

        $i=1;
        $wcan_altas=0;
        $wcan_altdef=0;
        $wcan_recibir=0;
        $wcan_ocupadas=0;
        $wcan_desocu=0;
        $wcan_aseo=0;
        $wcan_posibles=0;
        $row = mysql_fetch_array($res);
        while ($i <= $wnum)
           {

	        $wcco = $row[9];
	        $wnomcco=$row[10];

	        echo "<tr class=encabezadoTabla><th colspan=5 align=left><font size=4><b>Servicio: ".$wcco." - ".$wnomcco."</b></font></th></tr>";

	        echo "<tr class=encabezadoTabla>";
	        echo "<th><b>Habitación</b></th>";
	        echo "<th><b>Historia</b></th>";
	        echo "<th><b>Paciente</b></th>";
	        echo "<th><b>Estado</b></th>";
	        echo "<th><b>Observaciones</b></th>";
	        echo "</tr>";

	        while ($i<=$wnum and $wcco==$row[9])
	           {
				   //Sept 16 2015              Sept 17 2015                 
				if(($row['habcpa'] != '') and (trim($row['habcod']) != trim($row['habcpa']))) {
					 $whab=$row['habcod']." - ".$row['habcpa'];
				}else{
					$whab=$row['habcod'];					
				}
		       
		        $whis=$row[1];
		        $wing=$row[2];
		        $wali=$row[3];
		        $wdis=$row[4];
		        $west=$row[5];
		        $wfal=$row[6];
		        $whal=$row[7];
		        $wpro=$row[8];
		        $wcco=$row[9];
				$wcub=$row[11];

		        if (is_integer($i/2))
	               $wclass="fila1";
	              else
	                 $wclass="fila2";

				if (isset($whis) and $whis!="" and trim($whis)!="NO APLICA")
	               {
		            $q = " SELECT pacno1, pacno2, pacap1, pacap2, ubialp, ubiald, ubiptr, ubihan, ubihac, ubifap, ubihap, ubiprg, ".$wbasedato."_000018.Fecha_data, ".$wbasedato."_000018.Hora_data "
		                ."   FROM root_000037, root_000036, ".$wbasedato."_000018 "
		                ."  WHERE oriori  = '".$wemp_pmla."'"
		                ."    AND orihis  = '".$whis."'"
		                ."    AND oriing  = '".$wing."'"
		                ."    AND oriced  = pacced "
		                ."    AND orihis  = ubihis "
		                ."    AND oriing  = ubiing "
		                ."    AND ubiald != 'on' ";
		            $res1 = mysql_query($q, $conex) or die("ERROR EN QUERY");
		            $wnum1 = mysql_num_rows($res1);
		            $row1 = mysql_fetch_array($res1);

		            $wpac=$row1[0]." ".$row1[1]." ".$row1[2]." ".$row1[3];
		            $walp=$row1[4];
		            $wald=$row1[5];
		            $wptr=$row1[6];
		            $whan=$row1[7];
		            $whac=$row1[8];
		            $wfap=$row1[9];
		            $whap=$row1[10];
		            $wprg=$row1[11];   //habitacion con posible traslado
					$wfecha=$row1[12];   //habitacion con posible traslado
					$whora=$row1[13];   //habitacion con posible traslado

		            //$wespera="Desde las ".$whap;

		            if ($wfecha>$wfap)
	                   $wespera="Desde ".$wfap." a las ".$whap;
	                  else
	                     $wespera="Desde las ".$whap;
		           }
		          else
		             {
			          $wpac="";
			          $walp="";
			          $wald="";
			          $wptr="";
			          $whan="";
			          $whac="";
			          $wfap="";
			          $whap="";
			          $wnum1=0;
			          $wprg="";
			         }

	            echo "<tr class=".$wclass.">";
		        echo "<td><font color=003300><b>".$whab."</b></font></td>";
		        echo "<td><font color=003300>".$whis." - ".$wing."</font></td>";
		        echo "<td><font color=003300>".$wpac."</font></td>";

		        if ($wnum1 > 0)                                                 //Tiene historia asiganada en la habitacion
		           {
		            echo "<td align=center><font color=003300><b>Ocupada</b></font></td>";
					if ($wcub <> 'on' and $wcco <> $ccourg) //Que no sea tipo cubiculo
		               $wcan_ocupadas++;
	               }
		          else
		             if ($west=="off")                                          //La habitacion esta inactiva o fuera de servicio
		                echo "<td><font color=003300>Fuera de servicio desde: (".$wfal.") (".$whal.")</font></td>";
		               else
		                  {
		                   echo "<td align=center><font color=003300><b>Desocupada</b></font></td>";
						   if ($wcub <> 'on' and $wcco <> $ccourg) //Que no sea tipo cubiculo
		                      $wcan_desocu++;
	                      }

		        if ($wptr=="on")
					{

				   $q1 = " SELECT Fecha_data, Hora_data, Eyrhis, Eyring "
		                ."   FROM ".$wbasedato."_000017 "
		                ."  WHERE Eyrhis  = '".$whis."'"
		                ."    AND Eyring  = '".$wing."'"
						."    AND Eyrsde  = '".$wcco."'";

		           $res1 = mysql_query($q1, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $q1 . " - " . mysql_error());
		           $row = mysql_fetch_array($res1);

		           $wrecibo= "Pendiente de recibir Desde: ".$row['Fecha_data']." a las ".$row['Hora_data']."";
		             }
		        if ($wali=="on" and $west=="on")                                //Esta para alistar y activa
		           {
		            echo "<td><font color=003300>En Central de Habitaciones desde: (".$row[6].") (".$row[7].")</font></td>";
					if ($wcub <> 'on' and $wcco <> $ccourg ) //Que no sea tipo cubiculo
		               $wcan_aseo++;
	               }
		          else
		             {
		              if ($wdis=="on" and $wpro=="off" and $west=="on")         //Esta disponible, no asignada y activa
		                 echo "<td><font color=003300>Disponible en Admisiones</td>";
		                else
		                   {
			                if ($wdis=="on" and $wpro=="on" and $west=="on")    //Esta disponible, Asignada y activa
		                       echo "<td><font color=003300>En proceso de ocupación, asignada por Admisiones</font></td>";
			                  else
			                     {
				                  if ($walp=="on" and $wdis!="on")              //Esta en proceso de alta
							         {
									  if ($wcub <> 'on' and $wcco <> $ccourg) //Que no sea tipo cubiculo
								         $wcan_altas++;
								      $q= " SELECT cuefac, cuegen, cuepag, cuecok, cuefpa, cuehpa "
								          ."  FROM ".$wbasedato."_000022 "
								          ." WHERE cuehis = '".$whis."'"
						                  ."   AND cueing = '".$wing."'";
						              $res2 = mysql_query($q, $conex) or die("ERROR EN QUERY");
						              $wnum2 = mysql_num_rows($res2);

								      if ($wnum2 > 0)         //Si hay registros es porque ya el facturador hizo el chequeo para facturar
								         {
									      $row2 = mysql_fetch_array($res2);
									      $wfac=$row2[0];
									      $wgen=$row2[1];
									      $wpag=$row2[2];
									      $wcok=$row2[3];
									      $wfpa=$row2[4];
									      $whpa=$row2[5];

									      if ($wcok == "on")                        //Puede facturar
									         {
									          if ($wgen=="on")                      //Genero factura
									             {
									              if ($wpag=="on")                  //Ya se hizo el pago
								                     {
													   if ($wcub <> 'on' and $wcco <> $ccourg ) //Que no sea tipo cubiculo
									                       $wcan_altdef++;

									                   if ($wfecha>$wfpa)
									                      $wespera="Desde ".$wfpa." a las ".$whpa;
									                     else
									                        $wespera="Desde las ".$whpa;

									                   if ($wptr=="on")
									                      echo "<td bgcolor=FFCC00><font color=003300>".$wrecibo." Con alta administrativa y pendiente de alta definitiva en el servicio ".$wespera."</font></td>";
									                     else
									                        echo "<td bgcolor=FFCC00><font color=003300>Con alta administrativa, pendiente de alta definitiva en el servicio ".$wespera."</font></td>";
									                 }
								                    else
								                       {
									                    if ($wptr=="on")
									                       echo "<td bgcolor=FFFF66><font color=003300>".$wrecibo." En proceso de Alta ".$wespera.", pendiente de que el responsable cancele en caja</font></td>";
									                      else
									                         echo "<td bgcolor=FFFF66><font color=003300>En proceso de Alta ".$wespera.", pendiente de que el responsable cancele en caja</font></td>";
									                   }
							                     }
								                else
								                   if ($wptr=="on")
								                      {                            //No ha generado factura
								                       echo "<td bgcolor=FFFF66><font color=003300>".$wrecibo." En Proceso de Alta ".$wespera.", pendiente que el facturador genere la factura</font></td>";
								                      }
								                     else
								                        echo "<td bgcolor=FFFF66><font color=003300>En Proceso de Alta ".$wespera.", pendiente que el facturador genere la factura</font></td>";
							                 }
							                else                                   //No se puede facturar todavia
							                   {
								                if ($wptr=="on")
								                   echo "<td bgcolor=FFFF66><font color=003300>".$wrecibo." En proceso de Alta ".$wespera.", pendiente de la devolucion o que se corrijan documentos en el integrador</font></td>";
								                  else
								                     echo "<td bgcolor=FFFF66><font color=003300>En proceso de Alta ".$wespera.", pendiente de la devolucion o que se corrijan documentos en el integrador</font></td>";
								               }
								         }
							            else
							               if ($wptr=="on")
							                  echo "<td bgcolor=FFFF66><font color=003300>".$wrecibo." En Proceso de Alta ".$wespera.", falta la devolucion o que el facturador chequee si puede generar factura</font></td>";
							                 else
							                    echo "<td bgcolor=FFFF66><font color=003300>En Proceso de Alta ".$wespera.", falta la devolucion o que el facturador chequee si puede generar factura</font></td>";
							         }
							        else
							           if ($wptr=="on")
							              {
							               echo "<td bgcolor=009966><font color=003300><b>".$wrecibo."</b></font></td>";
										   if ($wcub <> 'on' and $wcco <> $ccourg ) //Que no sea tipo cubiculo
						                      $wcan_recibir++;
						                  }
							             else
							               {
								            if (strlen($wprg)>1)
							                  {
								               if ($wprg=="Alta" and $whab!=$wprg)
								                  {
								                   echo "<td bgcolor=33FFCC><font color=003300><b>Posible Alta</b></font></td>";
												   if ($wcub <> 'on' and $wcco <> $ccourg) //Que no sea tipo cubiculo
								                      $wcan_posibles++;
							                      }
								                 else
								                    if ($whab!=$wprg)
								                       {
								                        echo "<td bgcolor=33FFCC><font color=003300><b>Posible traslado para la habitación &nbsp&nbsp[&nbsp".$wprg."&nbsp]</b></font></td>";
														if ($wcub <> 'on' and $wcco <> $ccourg) //Que no sea tipo cubiculo
								                           $wcan_posibles++;
							                           }
							                          else
							                             echo "<td>&nbsp</td>";
								              }
							                 else
		                                        echo "<td>&nbsp</td>";
	                                       }
				                 }
			               }
	                 }

		        echo "</tr>";

		        $row = mysql_fetch_array($res);
		        $i++;
	           }
           }
       }

       echo "</table>";

       echo "<br><br>";
       echo "<center><table>";

       echo "<tr><td bgcolor=99FF99 align=left colspan=2><font size=4 color=000000><b>RESUMEN ESTADO ACTUAL SISTEMA DE ALTAS<br>( no incluye urgencias ) </font></td></tr>";
       if ($wcan_altas > 0) echo "<tr><td bgcolor=FFFF66 align=left><font size=3 color=000000><b>Historias en proceso de alta : </b></font></td><td bgcolor=FFFF66 align=right><font size=3 color=000000><b>".$wcan_altas."</b></font></td></tr>";
       if ($wcan_altdef > 0) echo "<tr><td bgcolor=FFCC00 align=left><font size=3 color=000000><b>Historias esperando el alta definitva : </b></font></td><td bgcolor=FFCC00 align=right><font size=3 color=000000><b>".$wcan_altdef."</b></font></td></tr>";
       if ($wcan_recibir > 0) echo "<tr><td bgcolor=009966 align=left><font size=3 color=000000><b>Historias pendientes de Recibo : </b></font></td><td bgcolor=009966 align=right><font size=3 color=000000><b>".$wcan_recibir."</b></font></td></tr>";
       if ($wcan_desocu > 0) echo "<tr><td bgcolor=99CCCC align=left><font size=3 color=000000><b>Camas Ocupadas : </b></font></td><td bgcolor=99CCCC align=right><font size=3 color=000000><b>".$wcan_ocupadas."</b></font></td></tr>";
       if ($wcan_desocu > 0) echo "<tr><td bgcolor=99CCCC align=left><font size=3 color=000000><b>Camas Disponibles : </b></font></td><td bgcolor=99CCCC align=right><font size=3 color=000000><b>".($wcan_desocu-$wcan_aseo)."</b></font></td></tr>";
       if ($wcan_aseo > 0) echo "<tr><td bgcolor=99CCCC align=left><font size=3 color=000000><b>Camas en Aseo : </b></font></td><td bgcolor=99CCCC align=right><font size=3 color=000000><b>".$wcan_aseo."</b></font></td></tr>";

       if ($wcan_desocu > 0) echo "<tr><td bgcolor=99CCCC align=left><font size=3 color=000000><b>Total Camas Habilitadas: </b></font></td><td bgcolor=99CCCC align=right><font size=3 color=000000><b>".($wcan_ocupadas+$wcan_desocu)."</b></font></td></tr>";

       if ($wcan_posibles > 0)
          echo "<tr><td bgcolor=33FFCC align=left><font size=3 color=000000><b>Posibles Traslados y Altas : </td><td bgcolor=33FFCC align=right><font size=4 color=000000><b>".number_format($wcan_posibles,0,'.',',')."</b></font></td></tr>";


       if ($wcan_ocupadas > 0 and $wcan_desocu > 0)
          echo "<tr><td bgcolor=99FF99 align=left><font size=4 color=000000><b>Ocupación clínica  : </td><td bgcolor=99FF99 align=right><font size=4 color=000000><b>".number_format((($wcan_ocupadas/($wcan_ocupadas+$wcan_desocu))*100),2,'.',',')."%</b></font></td></tr>";

       echo "<tr></tr>";
       echo "<tr><td align=center colspan=5><A href='monitor_altas.php?wemp_pmla=".$wemp_pmla."&selectsede=".$selectsede."' id='searchsubmit'> Actualizar</A></td></tr>";
       echo "</table>"; // cierra la tabla o cuadricula de la impresión

	   echo "<br>";
	   echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";

	   echo "</table>";
}
?>
<script>
    $(document).on('change','#selectsede',function(){
        window.location.href = "monitor_altas.php?wemp_pmla="+$('#wemp_pmla').val()+"&selectsede="+$('#selectsede').val()
    });
</script>
</html>
