<html>
<head>
  	<title>Reporte seguimiento de dispositivos</title>
</head>
<body  BGCOLOR="FFFFFF">
<BODY TEXT="#000066">

<script type="text/javascript">
function enter()
{
    document.forms.seg_dispositivos.submit();
}
</script>

<?php
include_once("conex.php");
//==================================================================================================================================
//PROGRAMA						:Reporte de seguimiento de dispositivo
//AUTOR							:Juan David Londoño
//FECHA CREACION				:2007-05-07
//FECHA ULTIMA ACTUALIZACION 	:2016-02-05
$wactualiz="2016-02-05";
//==================================================================================================================================
//ACTUALIZACIONES
//==================================================================================================================================
/*
    2016-02-04 : Eimer Castro: Se corrige el query de los cateteres, puesto que no estaban teniendo en cuenta la condición cuando se
        seleccionaba un centro de costo en particular.
    2016-02-04 : Eimer Castro: Se cambia el query que realiza la consulta para el reporte de las sondas vesicales, eliminando un IF dentro
        del WHERE y agregando un UNION que especifica en cada SELECT las opciones del IF eliminado. Así se mejora el rendimiento del query.
        Se cambian los nombres quemados de las bases de datos usuarios y det_selecciones.
        Se cambia el query que realiza la consulta para el reporte de las ventilaciones mécanicas, eliminando un IF dentro
        del WHERE y agregando un UNION que especifica en cada SELECT las opciones del IF eliminado. Así se mejora el rendimiento del query.
        Esto tambien debido a que el query no se ejecutaba entre las dos fechas indicadas sino que siempre se hacia para las fechas
        superiores a 2007-01-01.
        Se agregan dos botones al inicio de los reportes, los cuales son "Cerrar" y "Retornar".
        Se elimina el método para cargar el reporte al momento de cambiar la opción del tipo de dispositivo.
        Se cambian los nombres quemados de las bases de datos usuarios y det_selecciones.
        Se cambia el estilo que manejan las tablas para mostrar los resultados de las consultas.
    2016-01-28 : Eimer Castro: Se cambia el query que realiza la consulta para el reporte de los cateter, eliminando un IF dentro
        del WHERE y agregando un UNION que especifica en cada SELECT las opciones del IF eliminado. Así se mejora el rendimiento del query.
        Se cambian los nombres quemados de las bases de datos usuarios y det_selecciones.
    2008-07-29 Se cambio en las consultas donde se tomaban datos de los formulario cominf_000032 y cominf_000033 por
        movhos_000032 y movhos_000033
*/
//==================================================================================================================================

include_once("root/comun.php");

session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{

	

	


	echo "<form name=seg_dispositivos action='' method=post>";
	$wbasedato='cominf';
	$wbasedato1='movhos';
    $wbasedato_usuarios = "usuarios";
    $wbasedato_det_selecciones = "det_selecciones";
	// ENCABEZADO
    encabezado("REPORTE SEGUIMIENTO DE DISPOSITIVOS", $wactualiz ,"clinica");//
	if (!isset ($fecha2) or (isset ($fecha2) and $fecha2==''))

	{

	   	//$wfecha=date("Y-m-d");// esta es la fecha actual

		echo "<center><table border=0>";
		//echo "<tr><td align=center colspan=2><font size=5><img src='/matrix/images/medical/invecla/INVECLA.jpg' WIDTH=100 HEIGHT=80></font></td></tr>";
		echo "<tr><td><br></td></tr>";
		// echo "<tr><td align=center colspan=2><font size=5>REPORTE SEGUIMIENTO DE DISPOSITIVOS Ver.".$wactualiz."</font></td></tr>";
		if (!isset ($tipo))
		{
			echo "<tr><td class='fila1' align=center colspan=2><b>Tipo de dispositivo:</b> <select name='tipo'></font>";
			echo "<option>01-CATETERES</option><option>02-SONDAS VESICALES</option><option>03-VENTILACION MECANICA</option></select></td></tr>";
		}
		else
		{
			echo "<tr><td class='fila1' align=center colspan=2><b>Tipo de dispositivo: </b>".$tipo."</td>";
			echo "<input type='hidden' name='tipo' value='".$tipo."'></tr>";
		}

		if (isset ($tipo) and $tipo == '03-VENTILACION MECANICA')
		{
			echo "<tr><td align=center><input type='submit' value='CONSULTAR'></td>";
            echo "<td align='center' width='150'><INPUT type='button' value='Cerrar' style='width:100' onClick='javascript:top.close();'></INPUT></td></tr></table>";
			$fecha2=2007;
			echo "<input type='hidden' name='fecha2' value='".$fecha2."'></tr>";

		}
		else
		{
			echo "<td class='fila1' align=center colspan=2><b>Servicio:</b> <select name='servicio'>";
			// query para traerme las unidades
	        $query =  " SELECT Subcodigo, Descripcion" .
	        		      "   FROM " . $wbasedato_det_selecciones .
	        		      "  WHERE Medico = 'cominf'".
	        		      "    AND Codigo = '040'" .
	        		     "ORDER BY 2";
	        $err = mysql_query($query,$conex);
	        $num = mysql_num_rows($err);
	        echo "<option>*TODOS LOS SERVICIOS</option>";
		      for ($i=1;$i<=$num;$i++)
		        {
		            $row = mysql_fetch_array($err);
		            echo "<option>".$row[0]."-".$row[1]."</option>";
		        }
	        echo "</select></td></tr>";
	        echo "<tr class='encabezadotabla'><td align=center>Fecha Inicial</td>";
		    echo "<td align=center>Fecha Final</td></tr>";
            echo "<tr class='fila1'><td align='center'>";
            $fecha_inicio = (!isset($fecha_inicio)) ? date("Y-m-d") : $fecha_inicio;
            $fecha_fin = (!isset($fecha_fin)) ? date("Y-m-d") : $fecha_fin;
            campoFechaDefecto( "fecha1", $fecha_inicio);
            echo "</td><td align='center'>";
            campoFechaDefecto( "fecha2", $fecha_fin) ;
            echo "</td></tr>";
		    echo "<tr><td align=center><input type='submit' value='CONSULTAR'></td>";
            echo "<td align='center' width='150'><INPUT type='button' value='Cerrar' style='width:100' onClick='javascript:top.close();'></INPUT></td></tr></table>";

		//
		}


	//


	}
	////////////////////////////////////////////////////////apartir de aca comienza la impresion
	else
	{
	        $query1 =  " SELECT Subcodigo, Descripcion" .
	        		      "   FROM " . $wbasedato_det_selecciones .
	        		      "  WHERE Medico = 'cominf'".
	        		      "    AND Codigo = '040'" .
	        		     "ORDER BY 2";
	        $err1 = mysql_query($query1,$conex);
	        $num1 = mysql_num_rows($err1);

		      for ($i=1;$i<=$num1;$i++)
		      {
		         $row = mysql_fetch_array($err1);
		         $servicio1[$i][0]=$row[0];
		         $servicio1[$i][1]=$row[1];
		      }


		   if ($tipo != '03-VENTILACION MECANICA')
		   {
			   if ($servicio !='*TODOS LOS SERVICIOS' )
		   	 {
		   		 $vble="AND Servicio= substring('".$servicio."',1,4)";
		   	 }
		   	 else
		   	 {
		   	   $vble=" ";
		   	 }
		   }
	if ($tipo == '01-CATETERES') // PARA CATETÉRES
	    // este query me trae los datos que necesito que me muestre en pantalla como historia, número de ingreso,
		  // número de catéter, tipo de catéter, unidad que instala, fecha de instalación y usuario que graba.
	{

    //Fecha Modificación: 2016-01-28
    //Autor Modificación: Eimer Castro
    $query= "SELECT CI25.Historia_clinica, CI25.Num_ingreso, CI25.Num_cat, CI25.Tipo_cateter, CI25.Unidad_instala, CI25.Fecha_instala, U.Descripcion, MH33.Servicio, CI25.usuario, CI25.Seguridad
                    FROM ".$wbasedato1."_000033 AS MH33
                        INNER JOIN ".$wbasedato."_000025 AS CI25 ON (CI25.Historia_clinica=MH33.Historia_clinica AND CI25.Num_ingreso=MH33.Num_ingreso)
                        LEFT JOIN " . $wbasedato_usuarios . " AS U ON (CI25.usuario=U.Codigo)
                WHERE Dias_cateter < 0 " . $vble . "
                    AND Fecha_egre_serv between '".$fecha1."' and '".$fecha2."'
                    AND Tipo_egre_serv IN ('ALTA', 'MUERTE MAYOR A 48 HORAS', 'MUERTE MENOR A 48 HORAS')
                    AND usuario = ''
                    AND Codigo = substr(CI25.Seguridad,3)
                GROUP BY 1, 2, 3
            UNION
            SELECT CI25.Historia_clinica, CI25.Num_ingreso, CI25.Num_cat, CI25.Tipo_cateter, CI25.Unidad_instala, CI25.Fecha_instala, U.Descripcion, MH33.Servicio, CI25.usuario, CI25.Seguridad
                    FROM ".$wbasedato1."_000033 AS MH33
                        INNER JOIN ".$wbasedato."_000025 AS CI25 ON (CI25.Historia_clinica=MH33.Historia_clinica AND CI25.Num_ingreso=MH33.Num_ingreso)
                        LEFT JOIN " . $wbasedato_usuarios . " AS U ON (CI25.usuario=U.Codigo)
                WHERE Dias_cateter < 0 " . $vble . "
                    AND Fecha_egre_serv between '".$fecha1."' and '".$fecha2."'
                    AND Tipo_egre_serv IN ('ALTA', 'MUERTE MAYOR A 48 HORAS', 'MUERTE MENOR A 48 HORAS')
                    AND usuario != ''
                    AND Codigo=CI25.usuario
                GROUP BY 1, 2, 3
                ORDER BY 8, 6";

		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);

		if ($num > 0 and $vble!=" ")   // Impresión de catéteres sin fecha de retiro según el servicio que se seleccione
		{
		  echo "<center><table border=0>";// este es el encabezado del resultado
		  // echo "<tr><td align=center colspan=7><font size=5><img src='/matrix/images/medical/invecla/INVECLA.jpg' WIDTH=100 HEIGHT=80></font></td></tr>";
		  echo "<tr><td><br></td></tr>";
		  // echo "<tr><td align=center colspan=7><font size=5>REPORTE SEGUIMIENTO DE DISPOSITIVOS</font></td></tr>";
		  echo "<tr><td align=center colspan=7>Desde: <b>".$fecha1."</b> hasta <b>".$fecha2."</b><br>Unidad de consulta: <b>".$servicio."</b></td></tr>";
		  echo "<tr><td>&nbsp</td></tr>";
		  echo "<tr><td align=center colspan=7><font size=3>CATÉTERES CENTRALES SIN FECHA DE CIERRE DESPUÉS DEL ALTA DEL PACIENTE</font></td></tr>";
  	  echo "<tr><td>&nbsp</td></tr></table>";
      echo "<table align='center'>
        <tr>
            <td width='150'>
                <INPUT type='button' value='Cerrar' onClick='javascript:cerrarVentana();' style='width:100'>
            </td>
            <td width='150'>
                <input type='hidden' id='fecha_inicio' name='fecha_inicio' value='".$fecha1."'>
                <input type='hidden' id='fecha_fin' name='fecha_fin' value='".$fecha2."'>
                <INPUT type='submit' value='Retornar' style='width:100'>
            </td>
            <td></td>
        </tr>
        </table><br><br>";
  	  echo "<table><tr class=encabezadoTabla><td align=center>HISTORIA</td><td align=center>Nº INGRESO</td><td align=center>Nº CATETER</td>";
		  echo "<td align=center>TIPO CATETER</td><td align=center>UNIDAD QUE INSTALA</td><td align=center>FECHA INSTALACIÓN</td><td align=center>USUARIO QUE GRABA</td></tr>";

		  for ($i=1;$i<=$num;$i++)
		  {
		  	$row = mysql_fetch_array($err);
		  	if (is_int ($i/2))
		       // $wcf="DDDDDD";
                $wcf = "fila1";
		    else
		       // $wcf="FFFFFF";
                $wcf = "fila2";

            //if(($row[8] == '' && $row[10] == substr($row[9],3)) || ($row[8] != '' && $row[10] == $row[8]))
            {
                echo "<tr class=".$wcf." border=1><td>".$row[0]."</td><td align=center>".$row[1]."</td><td align=center>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td>".$row[5]."</td><td>".$row[6]."</td></tr>";
            }
		  	//echo "<tr bgcolor=".$wcf." border=1><td>".$row[0]."</td><td align=center>".$row[1]."</td><td align=center>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td>".$row[5]."</td><td>".$row[6]."</td></tr>";
		  }
		   	echo "<tr class=encabezadoTabla><td align=left colspan=6>TOTAL: </br></td><td align=center colspan=1>".$num."</br></td></tr></table>";
		}
		else if ($num > 0 and $vble=" ") // Impresión de catéteres sin fecha de retiro para todos los servicios
		{
		  // Estas dos variables van a servir para imprimir lo correspondiente a cada servicio cuando la selección inicial sea "TODOS LOS SERVICIOS"
	    $serv = mysql_result($err,0,7);
	    $serv1 = $serv;

	    for ($j=1;$j<=28;$j++)
	    {
        if ($serv1 == $servicio1[$j][0])
        {
          $serv2 = $servicio1[$j][1];
        }
      }

      //-----------

      echo "<center><table border=0>";// este es el encabezado del resultado
  		// echo "<tr><td align=center colspan=7><font size=5><img src='/matrix/images/medical/invecla/INVECLA.jpg' WIDTH=100 HEIGHT=80></font></td></tr>";
  		echo "<tr><td><br></td></tr>";
  		// echo "<tr><td align=center colspan=7><font size=5>REPORTE SEGUIMIENTO DE DISPOSITIVOS</font></td></tr>";
  		echo "<tr><td align=center colspan=7>Desde: <b>".$fecha1."</b> hasta <b>".$fecha2."</b><br>Unidad de consulta: <b>".$servicio."</b></td></tr>";
  		echo "<tr><td>&nbsp</td></tr>";
  		echo "<tr><td align=center colspan=7><font size=3>CATÉTERES CENTRALES SIN FECHA DE CIERRE DESPUÉS DEL ALTA DEL PACIENTE</font></td></tr>";
    	echo "<tr><td>&nbsp</td></tr></table>";
        echo "<table align='center'>
        <tr>
            <td width='150'>
                <INPUT type='button' value='Cerrar' onClick='javascript:cerrarVentana();' style='width:100'>
            </td>
            <td width='150'>
                <input type='hidden' id='fecha_inicio' name='fecha_inicio' value='".$fecha1."'>
                <input type='hidden' id='fecha_fin' name='fecha_fin' value='".$fecha2."'>
                <INPUT type='submit' value='Retornar' style='width:100'>
            </td>
            <td></td>
        </tr>
        </table><br><br>";
    	$query1 =  " SELECT Subcodigo, Descripcion" .
	        		      "   FROM " . $wbasedato_det_selecciones .
	        		      "  WHERE Medico = 'cominf'".
	        		      "    AND Codigo = '040'" .
    					  "    AND Subcodigo = '".$serv1."'".
	        		      "ORDER BY 2";
	        //echo $query1;
    		$err1 = mysql_query($query1,$conex);
	        $num1 = mysql_num_rows($err1);
	        $row = mysql_fetch_array($err1);
		echo "SERVICIO DE EGRESO:"." ".$serv1."-".$row[1];
    	echo "<table><tr class=encabezadoTabla><td align=center>HISTORIA</td><td align=center>Nº INGRESO</td><td align=center>Nº CATETER</td>";
  		echo "<td align=center>TIPO CATETER</td><td align=center>UNIDAD QUE INSTALA</td><td align=center>FECHA INSTALACIÓN</td><td align=center>USUARIO QUE GRABA</td></tr>";
      $n=0;
      mysql_data_seek($err,0);

      for ($i=1;$i<=$num;$i++)
      {
        $fila=mysql_fetch_row($err);
        $serv1=$fila[7];
        for ($j=1;$j<=28;$j++)
	      {
           if ($serv1 == $servicio1[$j][0])
           {
              $serv2 = $servicio1[$j][1];
            }
        }
        if (is_int ($i/2))
           // $wcf="DDDDDD";
           $wcf = "fila1";
        else
           // $wcf="FFFFFF";
           $wcf = "fila2";

        if ($serv == $serv1)
        {
           echo "<tr class=".$wcf." border=1><td>".$fila[0]."</td><td align=center>".$fila[1]."</td><td align=center>".$fila[2]."</td><td>".$fila[3]."</td><td>".$fila[4]."</td><td>".$fila[5]."</td><td>".$fila[6]."</td></tr>";
           $n++;
        }
        else
        {
           echo "<tr class=encabezadoTabla><td colspan=6>TOTAL SERVICIO</font></td><td align=center colspan=1>$n</font></td></tr></table><br><br>";
           $query1 =  " SELECT Subcodigo, Descripcion" .
	        		      "   FROM " . $wbasedato_det_selecciones .
	        		      "  WHERE Medico = 'cominf'".
	        		      "    AND Codigo = '040'" .
    					  "    AND Subcodigo = '".$serv1."'".
	        		      "ORDER BY 2";
	        //echo $query1;
    		$err1 = mysql_query($query1,$conex);
	        $num1 = mysql_num_rows($err1);
	        $row = mysql_fetch_array($err1);
		echo "SERVICIO DE EGRESO:"." ".$serv1."-".$row[1];
           //echo "SERVICIO DE EGRESO:"." ".$serv1."-".$serv2;
           echo "<table><tr class=encabezadoTabla><td align=center>HISTORIA</td><td align=center>Nº INGRESO</td><td align=center>Nº CATETER</td>";
  		     echo "<td align=center>TIPO CATETER</td><td align=center>UNIDAD QUE INSTALA</td><td align=center>FECHA INSTALACIÓN</td><td align=center>USUARIO QUE GRABA</td></tr>";
           echo "<tr class=".$wcf." border=1><td>".$fila[0]."</td><td align=center>".$fila[1]."</td><td align=center>".$fila[2]."</td><td>".$fila[3]."</td><td>".$fila[4]."</td><td>".$fila[5]."</td><td>".$fila[6]."</td></tr>";
           $n=1;
           $serv=$serv1;
        }
      }
      echo "<tr class=encabezadoTabla><td colspan=6>TOTAL SERVICIO</td><td align=center colspan=1>$n</td></tr></table><br><br>";

      echo "<table align='center'>
        <tr>
            <td width='150'>
                <INPUT type='button' value='Cerrar' onClick='javascript:cerrarVentana();' style='width:100'>
            </td>
            <td width='150'>
                <input type='hidden' id='fecha_inicio' name='fecha_inicio' value='".$fecha1."'>
                <input type='hidden' id='fecha_fin' name='fecha_fin' value='".$fecha2."'>
                <INPUT type='submit' value='Retornar' style='width:100'>
            </td>
            <td></td>
        </tr>
        </table>";

        echo "<INPUT type='hidden' name='mostrar' value='off'>";
    }
    else
    {
      echo "<center><h1><font size=5 face='arial' color=#006699><b>NO EXISTEN CATETERES SIN FECHA DE CIERRE CON ESTAS CONDICIONES</b></font></h1></center>";

            echo "<table align='center'>
        <tr>
            <td width='150'>
                <INPUT type='button' value='Cerrar' onClick='javascript:cerrarVentana();' style='width:100'>
            </td>
            <td width='150'>
                <input type='hidden' id='fecha_inicio' name='fecha_inicio' value='".$fecha1."'>
                <input type='hidden' id='fecha_fin' name='fecha_fin' value='".$fecha2."'>
                <INPUT type='submit' value='Retornar' style='width:100'>
            </td>
            <td></td>
        </tr>";

        echo "<INPUT type='hidden' name='mostrar' value='off'>";
    }
	}


	else if ($tipo == '02-SONDAS VESICALES') // para sondas
	// este query me trae los datos que necesito que me muestre en pantalla como historia, número de ingreso,
  // número de sonda, tipo de sonda, unidad que instala, fecha de instalación y usuario que graba.
  {

        //Fecha Modificación: 2016-02-04
        //Autor Modificación: Eimer Castro
		$query =  " SELECT CI26.Historia_clinica, CI26.Num_ingreso, CI26.Num_sond, CI26.Sonda, CI26.Unidad_instala, CI26.Fecha_instala, U.Descripcion, MH33.Servicio, CI26.usuario, CI26.Seguridad
                    FROM ".$wbasedato1."_000033 AS MH33
                        INNER JOIN ".$wbasedato."_000026 AS CI26 ON (CI26.Historia_clinica=MH33.Historia_clinica AND CI26.Num_ingreso=MH33.Num_ingreso)
                        LEFT JOIN " . $wbasedato_usuarios . " AS U ON (CI26.usuario=U.Codigo)
                      WHERE Dias_sv < 0 ".$vble."
                        AND Fecha_egre_serv between '".$fecha1."' and '".$fecha2."'
                        AND Tipo_egre_serv IN ('ALTA', 'MUERTE MAYOR A 48 HORAS', 'MUERTE MENOR A 48 HORAS')
                        AND usuario = ''
                        AND Codigo = substr(CI26.Seguridad,3)
                      GROUP BY 1, 2, 3
              UNION
            SELECT CI26.Historia_clinica, CI26.Num_ingreso, CI26.Num_sond, CI26.Sonda, CI26.Unidad_instala, CI26.Fecha_instala, U.Descripcion, MH33.Servicio, CI26.usuario, CI26.Seguridad
                    FROM ".$wbasedato1."_000033 AS MH33
                        INNER JOIN ".$wbasedato."_000026 AS CI26 ON (CI26.Historia_clinica=MH33.Historia_clinica AND CI26.Num_ingreso=MH33.Num_ingreso)
                        LEFT JOIN " . $wbasedato_usuarios . " AS U ON (CI26.usuario=U.Codigo)
                      WHERE Dias_sv < 0 ".$vble."
                        AND Fecha_egre_serv between '".$fecha1."' and '".$fecha2."'
                        AND Tipo_egre_serv IN ('ALTA', 'MUERTE MAYOR A 48 HORAS', 'MUERTE MENOR A 48 HORAS')
                        AND usuario != ''
                        AND Codigo=CI26.usuario
                      GROUP BY 1, 2, 3
                      ORDER BY 8, 6 ";

		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);


		if ($num > 0 and $vble!=" ") // Impresión de sondas sin fecha de retiro según el servicio que se seleccione
  	{
      echo "<center><table>";// este es el encabezado del resultado
      // echo "<tr><td align=center colspan=7><font size=5><img src='/matrix/images/medical/invecla/INVECLA.jpg' WIDTH=100 HEIGHT=80></font></td></tr>";
      echo "<tr><td><br></td></tr>";
      //echo "<tr><td align=center colspan=7><font size=5>REPORTE DE SEGUIMIENTO DE DISPOSITIVOS</font></td></tr>";
      echo "<tr><td align=center colspan=7>Desde: <b>".$fecha1."</b> hasta <b>".$fecha2."</b><br>Unidad de consulta: <b>".$servicio."</b></td></tr>";
      echo "<tr><td>&nbsp</td></tr>";
      echo "<tr><td align=center colspan=7><font size=3>SONDAS VESICALES SIN FECHA DE CIERRE DESPUÉS DEL ALTA DEL PACIENTE</font></td></tr>";
      echo "<tr><td>&nbsp</td></tr>";
      echo "<table align='center'>
        <tr>
            <td width='150'>
                <INPUT type='button' value='Cerrar' onClick='javascript:cerrarVentana();' style='width:100'>
            </td>
            <td width='150'>
                <input type='hidden' id='fecha_inicio' name='fecha_inicio' value='".$fecha1."'>
                <input type='hidden' id='fecha_fin' name='fecha_fin' value='".$fecha2."'>
                <INPUT type='submit' value='Retornar' style='width:100'>
            </td>
            <td></td>
        </tr>
        </table><br><br>";
      echo "<table><tr class=encabezadoTabla><td align=center>HISTORIA</td><td align=center>Nº INGRESO</td><td align=center>Nº SONDA</td>";
      echo "<td align=center>TIPO DE SONDA</font></td><td align=center>UNIDAD QUE INSTALA</font></td><td align=center>FECHA INSTALACIÓN</font></td><td align=center>USUARIO QUE GRABA</td></tr>";
  		for ($i=1;$i<=$num;$i++)
  		{
  			$row = mysql_fetch_array($err);
  			if (is_int ($i/2))
  		     // $wcf="DDDDDD";
             $wcf = "fila1" ;
  		  else
  		     // $wcf="FFFFFF";
             $wcf = "fila2" ;
  			echo "<tr class=".$wcf."><td>".$row[0]."</td><td align=center>".$row[1]."</td><td align=center>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td>".$row[5]."</td><td>".$row[6]."</td></tr>";
  		}
  		  echo "<tr class=encabezadoTabla><td align=left colspan=6><b>TOTAL: </br></td><td align=center colspan=1><b>".$num."</br></td></tr></table>";
  	}
  	else if ($num > 0 and $vble=" ") // Impresión de  sondas sin fecha de retiro para todos los servicios
  	{
   		// Estas dos variables van a servir para imprimir lo correspondiente a cada servicio cuando la selección inicial sea "TODOS LOS SERVICIOS"
  	  $serv = mysql_result($err,0,7);
  	  $serv1 = $serv;

	    for ($j=1;$j<=28;$j++)
	    {
        if ($serv1 == $servicio1[$j][0])
        {
          $serv2 = $servicio1[$j][1];
        }
      }
  		//-----------

      echo "<center><table border=0>";// este es el encabezado del resultado
      // echo "<tr><td align=center colspan=7><font size=5><img src='/matrix/images/medical/invecla/INVECLA.jpg' WIDTH=100 HEIGHT=80></font></td></tr>";
      echo "<tr><td><br></td></tr>";
      //echo "<tr><td align=center colspan=7><font size=5>REPORTE DE SEGUIMIENTO DE DISPOSITIVOS</font></td></tr>";
      echo "<tr><td align=center colspan=7>Desde: <b>".$fecha1."</b> hasta <b>".$fecha2."</b><br>Unidad de consulta: <b>".$servicio."</b></td></tr>";
      echo "<tr><td>&nbsp</td></tr>";
      echo "<tr><td align=center colspan=7><font size=3>SONDAS VESICALES SIN FECHA DE CIERRE DESPUÉS DEL ALTA DEL PACIENTE</font></td></tr>";
      echo "<tr><td>&nbsp</td></tr></table>";
      echo "<table align='center'>
        <tr>
            <td width='150'>
                <INPUT type='button' value='Cerrar' onClick='javascript:cerrarVentana();' style='width:100'>
            </td>
            <td width='150'>
                <input type='hidden' id='fecha_inicio' name='fecha_inicio' value='".$fecha1."'>
                <input type='hidden' id='fecha_fin' name='fecha_fin' value='".$fecha2."'>
                <INPUT type='submit' value='Retornar' style='width:100'>
            </td>
            <td></td>
        </tr>
        </table><br><br>";
      $query1 =  " SELECT Subcodigo, Descripcion" .
	        		      "   FROM " . $wbasedato_det_selecciones .
	        		      "  WHERE Medico = 'cominf'".
	        		      "    AND Codigo = '040'" .
    					  "    AND Subcodigo = '".$serv1."'".
	        		      "ORDER BY 2";
	        //echo $query1;
    		$err1 = mysql_query($query1,$conex);
	        $num1 = mysql_num_rows($err1);
	        $row = mysql_fetch_array($err1);
		echo "SERVICIO DE EGRESO:"." ".$serv1."-".$row[1];
      //echo "SERVICIO DE EGRESO:"." ".$serv1."-".$serv2;
      echo "<table><tr class=encabezadoTabla><td align=center>HISTORIA</td><td align=center>Nº INGRESO</td><td align=center>Nº SONDA</td>";
      echo "<td align=center>TIPO DE SONDA</td><td align=center>UNIDAD QUE INSTALA</td><td align=center>FECHA INSTALACIÓN</td><td align=center><b>USUARIO QUE GRABA</td></tr>";
      $n=0;
      mysql_data_seek($err,0);

      for ($i=1;$i<=$num;$i++)
      {
        $fila=mysql_fetch_row($err);
        $serv1=$fila[7];
  	    for ($j=1;$j<=28;$j++)
  	    {
          if ($serv1 == $servicio1[$j][0])
          {
            $serv2 = $servicio1[$j][1];
          }
        }
       	if (is_int ($i/2))
           // $wcf="DDDDDD";
           $wcf = "fila1";
        else
           // $wcf="FFFFFF";
           $wcf = "fila2";
        if ($serv == $serv1)
        {
           echo "<tr class=".$wcf."><td>".$fila[0]."</td><td align=center>".$fila[1]."</td><td align=center>".$fila[2]."</td><td>".$fila[3]."</td><td>".$fila[4]."</td><td>".$fila[5]."</td><td>".$fila[6]."</td></tr>";
           $n++;
        }
        else
        {
           echo "<tr class=encabezadoTabla><td colspan=6>TOTAL SERVICIO</td><td align=center colspan=1>$n</td></tr></table><br><br>";
           $query1 =  " SELECT Subcodigo, Descripcion" .
	        		      "   FROM " . $wbasedato_det_selecciones .
	        		      "  WHERE Medico = 'cominf'".
	        		      "    AND Codigo = '040'" .
    					  "    AND Subcodigo = '".$serv1."'".
	        		      "ORDER BY 2";
	        //echo $query1;
    		$err1 = mysql_query($query1,$conex);
	        $num1 = mysql_num_rows($err1);
	        $row = mysql_fetch_array($err1);
			echo "SERVICIO DE EGRESO:"." ".$serv1."-".$row[1];
          //echo "SERVICIO DE EGRESO:"." ".$serv1."-".$serv2;
           echo "<table><tr class=encabezadoTabla><td align=center>HISTORIA</td><td align=center>Nº INGRESO</td><td align=center>Nº SONDA</td>";
           echo "<td align=center>TIPO DE SONDA</td><td align=center>UNIDAD QUE INSTALA</td><td align=center>FECHA INSTALACIÓN</td><td align=center>USUARIO QUE GRABA</td></tr>";
           echo "<tr class=".$wcf."><td>".$fila[0]."</td><td align=center>".$fila[1]."</td><td align=center>".$fila[2]."</td><td>".$fila[3]."</td><td>".$fila[4]."</td><td>".$fila[5]."</td><td>".$fila[6]."</td></tr>";
           $n=1;
           $serv=$serv1;
        }
      }
      echo "<tr class=encabezadoTabla><td colspan=6>TOTAL SERVICIO</td><td align=center colspan=1>$n</td></tr></table><br><br>";

      echo "<table align='center'>
        <tr>
            <td width='150'>
                <INPUT type='button' value='Cerrar' onClick='javascript:cerrarVentana();' style='width:100'>
            </td>
            <td width='150'>
                <input type='hidden' id='fecha_inicio' name='fecha_inicio' value='".$fecha1."'>
                <input type='hidden' id='fecha_fin' name='fecha_fin' value='".$fecha2."'>
                <INPUT type='submit' value='Retornar' style='width:100'>
            </td>
            <td></td>
        </tr>";

        echo "<INPUT type='hidden' name='mostrar' value='off'>";

    }
   else
  	{
  		echo "<center><h1><font size=5 face='arial' color=#006699><b>NO EXISTEN SONDAS SIN FECHA DE CIERRE CON ESTAS CONDICIONES</b></font></h1></center>";

        echo "<table align='center'>
        <tr>
            <td width='150'>
                <INPUT type='button' value='Cerrar' onClick='javascript:cerrarVentana();' style='width:100'>
            </td>
            <td width='150'>
                <input type='hidden' id='fecha_inicio' name='fecha_inicio' value='".$fecha1."'>
                <input type='hidden' id='fecha_fin' name='fecha_fin' value='".$fecha2."'>
                <INPUT type='submit' value='Retornar' style='width:100'>
            </td>
            <td></td>
        </tr>";

        echo "<INPUT type='hidden' name='mostrar' value='off'>";
  	}
	}


	else if ($tipo == '03-VENTILACION MECANICA')  // PARA VENTILACIÓN MECÁNICA
	// este query me trae los datos que necesito que me muestre en pantalla como historia, número de ingreso,
  // unidad que instala, fecha de instalación y usuario que graba.
	{

        //Fecha Modificación: 2016-02-04
        //Autor Modificación: Eimer Castro
        $query =  "SELECT CI27.Historia_clinica, CI27.Num_ingreso, CI27.Servicio_inivm, CI27.Fecha_inivm, U.Descripcion, CI27.usuario, CI27.Seguridad
                            FROM ".$wbasedato1."_000033 AS MH33
                                INNER JOIN ".$wbasedato."_000027 AS CI27 ON (CI27.Historia_clinica=MH33.Historia_clinica AND CI27.Num_ingreso=MH33.Num_ingreso)
                                LEFT JOIN " . $wbasedato_usuarios . " AS U ON CI27.usuario = U.Codigo
                            WHERE Dias_vm < 0
                                    AND Fecha_inivm between '".$fecha1."' and '".$fecha2."'
                                    AND CI27.usuario = ''
                                    AND U.Codigo = substr(CI27.Seguridad,3)
                        GROUP BY 1, 2, 4
                    UNION
                    SELECT CI27.Historia_clinica, CI27.Num_ingreso, CI27.Servicio_inivm, CI27.Fecha_inivm, U.Descripcion, CI27.usuario, CI27.Seguridad
                            FROM ".$wbasedato1."_000033 AS MH33
                                INNER JOIN ".$wbasedato."_000027 AS CI27 ON (CI27.Historia_clinica=MH33.Historia_clinica AND CI27.Num_ingreso=MH33.Num_ingreso)
                                LEFT JOIN usuarios AS U ON CI27.usuario = U.Codigo
                            WHERE Dias_vm < 0
                                    AND Fecha_inivm between '".$fecha1."' and '".$fecha2."'
                                    AND CI27.usuario != ''
                                    AND U.Codigo=CI27.usuario
                        GROUP BY 1, 2, 4
                        ORDER BY 1";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num > 0)
  	{
  	  echo "<center><table border=0>";// este es el encabezado del resultado
  	  // echo "<tr><td align=center colspan=5><font size=5><img src='/matrix/images/medical/invecla/INVECLA.jpg' WIDTH=100 HEIGHT=80></font></td></tr>";
  	  echo "<tr><td><br></td></tr>";
  	  // echo "<tr><td align=center colspan=5><font size=5>REPORTE SEGUIMIENTO DE DISPOSITIVOS</font></td></tr>";
  	  echo "<tr><td>&nbsp</td></tr>";
  	  echo "<tr><td align=center colspan=5><font size=3>VENTILACIÓN MECÁNICA SIN FECHA DE CIERRE DESPUÉS DE ALTA O TRASLADO A OTRA UNIDAD</font></td></tr>";
  	  echo "<tr><td>&nbsp</td></tr></table>";
      echo "<table align='center'>
        <tr>
            <td width='150'>
                <INPUT type='button' value='Cerrar' onClick='javascript:cerrarVentana();' style='width:100'>
            </td>
            <td width='150'>
                <input type='hidden' id='fecha_inicio' name='fecha_inicio' value='".$fecha1."'>
                <input type='hidden' id='fecha_fin' name='fecha_fin' value='".$fecha2."'>
                <INPUT type='submit' value='Retornar' style='width:100'>
            </td>
            <td></td>
        </tr>
        </table><br><br>";
  	  echo "<table><tr class=encabezadoTabla><td align=center>HISTORIA</td><td align=center>Nº INGRESO</td><td align=center>UNIDAD QUE INSTALA</td>";
  	  echo "<td align=center>FECHA DE INSTALACIÓN<td align=center>USUARIO QUE GRABA</td></tr>";
  	  for ($i=1;$i<=$num;$i++)
  	 	{
  	 		$row = mysql_fetch_array($err);
  	 		if (is_int ($i/2))
  	       // $wcf="DDDDDD";
           $wcf = "fila1" ;
  	    else
  	       // $wcf="FFFFFF";
           $wcf = "fila2" ;
  			echo "<tr class=".$wcf."><td>".$row[0]."</td><td align=center>".$row[1]."</td><td>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td></tr>";
  	 	}
  		 	echo "<tr class=encabezadoTabla><td align=left colspan=4><b>TOTAL: </br></td><td align=center colspan=1><b>".$num."</br></td></tr>";

            echo "<table align='center'>
        <tr>
            <td width='150'>
                <INPUT type='button' value='Cerrar' onClick='javascript:cerrarVentana();' style='width:100'>
            </td>
            <td width='150'>
                <input type='hidden' id='fecha_inicio' name='fecha_inicio' value='".$fecha1."'>
                <input type='hidden' id='fecha_fin' name='fecha_fin' value='".$fecha2."'>
                <INPUT type='submit' value='Retornar' style='width:100'>
            </td>
            <td></td>
        </tr>";

        echo "<INPUT type='hidden' name='mostrar' value='off'>";
    }
  	else
  	{
  	  echo "<center><h1><font size=5 face='arial' color=#006699><b>NO EXISTE VENTILACIÓN MECÁNICA SIN FECHA DE RETIRO CON ESTAS CONDICIONES</b></font></h1></center></table>";

      echo "<table align='center'>
        <tr>
            <td width='150'>
                <INPUT type='button' value='Cerrar' onClick='javascript:cerrarVentana();' style='width:100'>
            </td>
            <td width='150'>
                <input type='hidden' id='fecha_inicio' name='fecha_inicio' value='".$fecha1."'>
                <input type='hidden' id='fecha_fin' name='fecha_fin' value='".$fecha2."'>
                <INPUT type='submit' value='Retornar' style='width:100'>
            </td>
            <td></td>
        </tr>";

        echo "<INPUT type='hidden' name='mostrar' value='off'>";
  	}
  }
 }
}
?>
</body>
</html>

























