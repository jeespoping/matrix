<html>
<head>
<title>MATRIX</title>
<!--
<style type="text/css">
BODY
{
    font-family: Verdana;
    font-size: 4pt;
    margin: 0px;
}
</style>
-->
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<script type="text/javascript">
	function cerrarVentana()
	 {
      window.close()
     }

    function enter()
	 {
	   document.forms.apl_med_mat.submit();
     }

</script>
<?php
include_once("conex.php");
/**---------------------------------------------------------------------------------MODIFICACIONES----------------------------------------------------------------------------------

2013-05-08 Camilo Zapata: se adicionó la columna de hora de creación de la factura

---------------------------------------------------------------------------------MODIFICACIONES----------------------------------------------------------------------------------**/
 session_start();
 if(!isset($_SESSION['user']))
 echo "error";
 else
 {
  $key = substr($user,2,strlen($user));
  echo "<form action='RepFacGenNeto.php' method=post>";

  $wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
  $wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
  $wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
  $wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro

  $wfecha=date("Y-m-d");

  $wactualiz = '2013-05-08';

  include_once("root/comun.php");

  if(!isset($wemp_pmla)){
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
	}

	$conex = obtenerConexionBD("matrix");

	$institucion = ConsultarInstitucionPorCodigo($conex, $wemp_pmla);

	$wbasedato = strtolower($institucion->baseDeDatos);
	echo $wbasedato;
	$wentidad = $institucion->nombre;
	$wactualiz = '2009-11-09';

	encabezado( "FACTURADO GENERAL NETO", $wactualiz, "logo_".$wbasedato );

  echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";
  //echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";

  echo "<center><table border=2>";
//  echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/ips/logo_".$wbasedato.".png' WIDTH=340 HEIGHT=100></td></tr>";
//  echo "<tr><td align=center bgcolor=".$wcf2."><font size=5 text color=#FFFFFF><b>FACTURADO GENERAL NETO</b></font></td></tr>";

  if (!isset($wfecini) or !isset($wfecfin) or !isset($wcco) or !isset($wemp))
   {
   	if( !isset($wfecini) ){
   		$wfecini = $wfecha;
   	}

   	if( !isset($wfecfin) ){
   		$wfecfin =  $wfecha;
   	}

	echo "<tr class='fila1'>";
    echo "<td align=center><b><font text color=".$wclfg.">Fecha Inicial (AAAA-MM-DD): </font></b>";
    campoFechaDefecto("wfecini", $wfecini );
    echo "</td>";

    echo "<td align=center><b><font text color=".$wclfg.">Fecha Final (AAAA-MM-DD): </font></b>";
    campoFechaDefecto("wfecfin", $wfecfin );
    echo "</td>";
    echo "</tr>";

    //CENTRO DE COSTO
    $q =  " SELECT ccocod, ccodes "
		 ."   FROM ".$wbasedato."_000003 "
		 ."  ORDER BY 1 ";

	$res = mysql_query($q,$conex) or die( mysql_errno()." - Error en el query $q - ".mysql_error());
	$num = mysql_num_rows($res);


	echo "<tr class='fila1'><td align=center colspan=1>SELECCIONE LA SUCURSAL: ";
	echo "<select name='wcco'>";
	for ($i=1;$i<=$num;$i++)
	   {
	    $row = mysql_fetch_array($res);
	    echo "<option>".$row[0]."-".$row[1]."</option>";
       }
	echo "</select></td>";


	//SELECCIONAR EMPRESA
    $q =  " SELECT empcod, empnom, temdes "
		 ."   FROM ".$wbasedato."_000024, ".$wbasedato."_000029 "
		 ."  WHERE trim(mid(emptem,1,instr(emptem,'-')-1)) = temcod "
		 ."  ORDER BY 3,1 ";
	$res = mysql_query($q,$conex) or die( mysql_errno()." - Error en el query $q - ".mysql_error());
	$num = mysql_num_rows($res);

	echo "<td align=center >SELECCIONE LA EMPRESA: ";
	echo "<select name='wemp'>";
	echo "<option>% - Todas las empresas</option>";
	for ($i=1;$i<=$num;$i++)
	   {
	    $row = mysql_fetch_array($res);
	    echo "<option>".$row[0]."-".$row[1]."- <b>[&nbsp&nbsp&nbsp&nbsp".$row[2]."&nbsp&nbsp&nbsp&nbsp]</b></option>";
       }
	echo "</select></td></tr>";


	echo "<tr class='fila1'><td colspan=2 align=center><font text color=".$wclfg."><b>ESTADO : </b></font>";
	echo "<select name='west'>";
			echo "<option>on-Activo</option>";
			echo "<option>off-Anulado</option>";
	echo "</select></td></tr>";


	echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";

    echo "<tr class='fila1'>";
    echo "<td align=center colspan=2><input type='submit' value='OK'></td>";
    echo "</tr>";
   }
  else
     {
	  echo "<input type='HIDDEN' NAME= 'wcco' value='".$wcco."'>";
	  $wccoe = explode("-",$wcco);

	  echo "<input type='HIDDEN' NAME= 'wemp' value='".$wemp."'>";
	  $wempe = explode("-",$wemp);

	  $west1=explode("-",$west);

      $wfactot=0;
	  $wfaccco=0;
	  $wfacemp=0;

	  $wdestot=0;
	  $wdescco=0;
	  $wdesemp=0;

	  $wncrtot=0;
	  $wncrcco=0;
	  $wncremp=0;

	  $wrctot=0;
	  $wrccco=0;
	  $wrcemp=0;

	  $wsaltot=0;
	  $wsalcco=0;
	  $wsalemp=0;

	  echo "<A href='RepFacGenNeto.php?wemp_pmla=".$wemp_pmla."&wfecini=".$wfecini."&wfecfin=".$wfecfin."'><center>VOLVER</center></A><br>";

	  echo "<center><table>";
	  echo "<tr class='fila1'>";
      echo "<td align=center><b><font text color=".$wclfg.">Fecha Inicial (AAAA-MM-DD): </font></b>".$wfecini."</td>";
      echo "<td align=center><b><font text color=".$wclfg.">Fecha Final (AAAA-MM-DD): </font></b>".$wfecfin."</td>";
      echo "</tr>";
      echo "<tr class='fila1'>";
      echo "<td align=center colspan=2><b><font text color=".$wclfg.">Sucursal: </font></b>".$wcco."</td>";
      echo "</tr>";
      echo "<tr class='fila1'>";
      echo "<td align=center colspan=2><b><font text color=".$wclfg.">Empresa: </font></b>".$wemp."</td>";
      echo "</tr>";
      echo "</table>";

      echo "<br><br>";

      $q = " SELECT ccocod, ccodes, empnit, fencod, fenres, fenffa, fenfac, fenfec, fendpa, fennpa, fenval+fenabo+fencmo+fencop+fendes, fenvnc, fenrbo, fensal, fendes, clite1, a.hora_data  "
	      ."   FROM  ".$wbasedato."_000018 a,".$wbasedato."_000003,".$wbasedato."_000024, ".$wbasedato."_000041"
	      ."  WHERE  fenfec BETWEEN '".$wfecini."' AND '".$wfecfin."'"
	      ."    AND fencco LIKE TRIM('".$wccoe[0]."') "
	      ."    AND fencod LIKE TRIM('".$wempe[0]."') "
	      ."    AND fenest = TRIM('".$west1[0]."') "
	      ."    AND fencco = ccocod "
	      ."    AND fencod = empcod "
		  ."    AND clidoc = fendpa "
		  ."    GROUP BY clidoc, ccocod, ccodes, empnit, fencod, fenres, fenffa, fenfac, fenfec, fendpa, fennpa, fenval+fenabo+fencmo+fencop+fendes, fenvnc, fenrbo, fensal, fendes"
	      ."  ORDER BY ccocod, empcod, empnit, fenfac ";
	  $err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	  $num = mysql_num_rows($err) or die (mysql_errno()." - ".mysql_error());

	  $row = mysql_fetch_array($err);

	  echo "<table border=1>";

	  echo "<tr class='fila1'>";
	  echo "<td colspan=14 ><b>Empresa : ".$row[2]."</b></td>";
	  echo "</tr>";

	  $i=1;
	  while ($i<=$num)
	   {
		$wccoaux = $row[0];

		echo "<tr class='fila1'>";
		echo "<td colspan=14><b>Sucursal : ".$row[0]." - ".$row[1]."</b></td>";
		echo "</tr>";

		echo "<tr class='encabezadotabla'>";
		echo "<th><font size=4 text color=#FFFFFF>Factura</th>";
		echo "<th><font size=4 text color=#FFFFFF>Fecha</th>";
		echo "<th><font size=4 text color=#FFFFFF>Hora</th>";
		echo "<th><font size=4 text color=#FFFFFF>Dcto</th>";
		echo "<th nowrap='nowrap'><font size=4 text color=#FFFFFF>Nombre Usuario</th>";
		echo "<th><font size=4 text color=#FFFFFF>Tel&eacute;fono</th>";
		echo "<th><font size=4 text color=#FFFFFF>Vlr Factura</th>";
		echo "<th><font size=4 text color=#FFFFFF>Vlr Descuento</th>";
		echo "<th><font size=4 text color=#FFFFFF>Vlr Nota Credito</th>";
		echo "<th><font size=4 text color=#FFFFFF>Facturado Neto</th>";
		echo "<th><font size=4 text color=#FFFFFF>Vlr Recibo</th>";
		echo "<th><font size=4 text color=#FFFFFF>Vlr Saldo</th>";
		echo "<th><font size=4 text color=#FFFFFF>IPS</th>";
		echo "<th><font size=4 text color=#FFFFFF>Caja<br> Fisica</th>";



		while ($i<=$num and $wccoaux == $row[0])
		   {
			if (is_integer($i / 2))
			   $wcolor = "class='fila1'";//$wcolor = "00FFFF";
			  else
			    $wcolor = "class='fila2'"; //$wcolor = $wclfa;

			echo "<tr $wcolor>";
			echo "<td align=left>".$row[5]."-".$row[6]."</td>";    						 	 		//Factura
			echo "<td align=center>".$row[7]."</td>";                						 	 		//Fecha Factura
			echo "<td align=center>".$row[16]."</td>";                						 	 		//Fecha Factura
			echo "<td align=left>".$row[8]."</td>";                						 			//Dcto Uusario
			echo "<td align=left>".$row[9]."</td>";                						 	 		//Nombre Usuario
			echo "<td align=left>".$row[15]."</td>";                						 	 		//Telefono
			echo "<td align=right>".number_format($row[10],0,'.',',')."</td>";               		//Valor Factura
			echo "<td align=right>".number_format($row[14],0,'.',',')."</td>";               		//Valor descuento
			echo "<td align=right>".number_format($row[11],0,'.',',')."</td>";                      //Notas Credito
			echo "<td align=right>".number_format(($row[10]-$row[11]-$row[14]),0,'.',',')."</td>";  //Facturado menos notas credito
			echo "<td align=right>".number_format($row[12],0,'.',',')."</td>";               		//Recibos de Caja
			echo "<td align=right>".number_format($row[13],0,'.',',')."</td>";               		//Saldo
			//=================================================================================================================
			$q = " SELECT empnom "
			    ."   FROM ".$wbasedato."_000024 "
			    ."  WHERE empcod = '".$row[3]."'";
			$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	        $num1 = mysql_num_rows($res) or die (mysql_errno()." - ".mysql_error());
	        if ($num1 > 0 ) {
		        $row1 = mysql_fetch_array($res);
		        $wnomemp=$row1[0];
	           }
			echo "<td align=left>".$wnomemp."</td>";                						 		//IPS
			//=================================================================================================================

			//=================================================================================================================
			$q = " SELECT ordcaj "
			    ."   FROM ".$wbasedato."_000133 "
			    ."  WHERE ordffa = '".$row[5]."'"
			    ."    AND ordfac = '".$row[6]."'";
			$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
			$num1=mysql_affected_rows();
			if ($num1 > 0 ) {
		        $row1 = mysql_fetch_array($res);
		        $wcaja=$row1[0];
	           }
	          else
	             $wcaja="&nbsp";
	        echo "<td align=left>".$wcaja."</td>";                						     //Caja Fisica
	        //=================================================================================================================
			echo "</tr>";

			$wfactot=$wfactot+$row[10];
			$wfaccco=$wfaccco+$row[10];
			$wfacemp=$wfaccco+$row[10];

			$wdestot=$wdestot+$row[14];
			$wdescco=$wdescco+$row[14];
			$wdesemp=$wdescco+$row[14];

			$wncrtot=$wncrtot+$row[11];
			$wncrcco=$wncrcco+$row[11];
			$wncremp=$wncrcco+$row[11];

			$wrctot=$wrctot+$row[12];
			$wrccco=$wrccco+$row[12];
			$wrcemp=$wrccco+$row[12];

			$wsaltot=$wsaltot+$row[13];
			$wsalcco=$wsalcco+$row[13];
			$wsalemp=$wsalcco+$row[13];

			$row = mysql_fetch_array($err);
			$i++;
		   }
		echo "<tr class='encabezadotabla'>";
	    echo "<td colspan=6 align=center><font size=4 text color=#FFFFFF>Totales Sucursal: ".$wcco."</font></td>";
	    echo "<td ALIGN=right><font size=4 text color=#FFFFFF>".number_format($wfaccco,0,'.',',')."</font></td>";
	    echo "<td ALIGN=right><font size=4 text color=#FFFFFF>".number_format($wdescco,0,'.',',')."</font></td>";
	    echo "<td ALIGN=right><font size=4 text color=#FFFFFF>".number_format($wncrcco,0,'.',',')."</font></td>";
	    echo "<td ALIGN=right><font size=4 text color=#FFFFFF>".number_format(($wfaccco-$wdescco-$wncrcco),0,'.',',')."</font></td>";
	    echo "<td ALIGN=right><font size=4 text color=#FFFFFF>".number_format($wrccco,0,'.',',')."</font></td>";
	    echo "<td ALIGN=right><font size=4 text color=#FFFFFF>".number_format($wsalcco,0,'.',',')."</font></td>";
	    echo "<td colspan=2 align=center>&nbsp</td>";
	    echo "</tr>";
	  }
	  echo "<tr class='encabezadotabla'>";
	  echo "<td colspan=6 align=center><font size=4 text color=#FFFFFF>Totales General: </font></td>";
	  echo "<td ALIGN=right><font size=4 text color=#FFFFFF>".number_format($wfactot,0,'.',',')."</font></td>";
	  echo "<td ALIGN=right><font size=4 text color=#FFFFFF>".number_format($wdestot,0,'.',',')."</font></td>";
	  echo "<td ALIGN=right><font size=4 text color=#FFFFFF>".number_format($wncrtot,0,'.',',')."</font></td>";
	  echo "<td ALIGN=right><font size=4 text color=#FFFFFF>".number_format(($wfactot-$wdestot-$wncrtot),0,'.',',')."</font></td>";
	  echo "<td ALIGN=right><font size=4 text color=#FFFFFF>".number_format($wrctot,0,'.',',')."</font></td>";
	  echo "<td ALIGN=right><font size=4 text color=#FFFFFF>".number_format($wsaltot,0,'.',',')."</font></td>";
	  echo "<td colspan=2 align=center>&nbsp</td>";
	  echo "</tr>";
	  echo "</table>";
	  echo "<A href='RepFacGenNeto.php?wemp_pmla=".$wemp_pmla."&wfecini=".$wfecini."&wfecfin=".$wfecfin."'><center>VOLVER</center></A><br>";
	 }
	 echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
}
 liberarConexionBD($conex);
?>
</body>
</html>
