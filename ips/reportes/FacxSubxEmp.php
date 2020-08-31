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
	//-------------------------------------------------------------------------------------------------------------------------------------------
	//	-->	2013-12-24, Jerson trujillo.
	//		El maestro de conceptos que actualmente es la tabla 000004, para cuando entre en funcionamiento la nueva facturacion del proyecto
	//		del ERP, esta tabla cambiara por la 000200; Entonces se adapta el programa para que depediendo del paramatro de root_000051
	//		'NuevaFacturacionActiva' realice este cambio automaticamente.
	//-------------------------------------------------------------------------------------------------------------------------------------------

 session_start();
 if(!isset($_SESSION['user']))
 echo "error";
 else
 {
  $key = substr($user,2,strlen($user));
  

  

  echo "<form action='FacxSubxEmp.php' method=post>";

  $wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
  $wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
  $wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
  $wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro

  $wfecha=date("Y-m-d");

  include_once("root/comun.php");

  if(!isset($wemp_pmla)){
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
	}

	$conex = obtenerConexionBD("matrix");

	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

	$wbasedato = $institucion->baseDeDatos;
	$wentidad = $institucion->nombre;

  echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";
  //echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";

	//---------------------------------------------------------------------------------------------
	// --> 	Consultar si esta en funcionamiento la nueva facturacion
	//		Fecha cambio: 2013-12-24	Autor: Jerson trujillo.
	//---------------------------------------------------------------------------------------------
	$nuevaFacturacion = consultarAliasPorAplicacion($conex, $wemp_pmla, 'NuevaFacturacionActiva');
	//---------------------------------------------------------------------------------------------
	// --> 	MAESTRO DE CONCEPTOS:
	//		- Antigua facturacion 	--> 000004
	//		- Nueva facturacion 	--> 000200
	//		Para la nueva facturacion cuando esta entre en funcionamiento el maestro
	//		de conceptos cambiara por la tabla 000200.
	//		Fecha cambio: 2013-12-24	Autor: Jerson trujillo.
	//----------------------------------------------------------------------------------------------
	$tablaConceptos = $wbasedato.(($nuevaFacturacion == 'on') ? '_000200' : '_000004');
	//----------------------------------------------------------------------------------------------

  echo "<center><table border=2>";
  echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/ips/logo_".$wbasedato.".png' WIDTH=340 HEIGHT=100></td></tr>";
  echo "<tr><td align=center bgcolor=".$wcf2."><font size=5 text color=#FFFFFF><b>FACTURADO POR SUBSIDIOS EMPRESA</b></font></td></tr>";

  if (!isset($wfecini) or !isset($wfecfin) or !isset($wcco) or !isset($wemp))
   {
	echo "<tr>";
    echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Fecha Inicial (AAAA-MM-DD): </font></b><INPUT TYPE='text' NAME='wfecini' value=".$wfecha." SIZE=10></td>";
    echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Fecha Final (AAAA-MM-DD): </font></b><INPUT TYPE='text' NAME='wfecfin' value=".$wfecha." SIZE=10></td>";
    echo "</tr>";

    //CENTRO DE COSTO
    $q =  " SELECT ccocod, ccodes "
		 ."   FROM ".$wbasedato."_000003 "
		 ."  ORDER BY 1 ";

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);


	echo "<tr><td align=center bgcolor=".$wcf." colspan=1>SELECCIONE LA SUCURSAL: ";
	echo "<select name='wcco'>";
	echo "<option>% - Todos</option>";
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
	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	echo "<td align=center bgcolor=".$wcf." >SELECCIONE LA EMPRESA: ";
	echo "<select name='wemp'>";
	echo "<option>% - Todas las empresas</option>";
	for ($i=1;$i<=$num;$i++)
	   {
	    $row = mysql_fetch_array($res);
	    echo "<option>".$row[0]."-".$row[1]."- <b>[&nbsp&nbsp&nbsp&nbsp".$row[2]."&nbsp&nbsp&nbsp&nbsp]</b></option>";
       }
	echo "</select></td></tr>";


	echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";

    echo "<tr>";
    echo "<td align=center bgcolor=#cccccc colspan=2><input type='submit' value='OK'></td>";
    echo "</tr>";
   }
  else
     {
	  echo "<input type='HIDDEN' NAME= 'wcco' value='".$wcco."'>";
	  $wccoe = explode("-",$wcco);

	  echo "<input type='HIDDEN' NAME= 'wemp' value='".$wemp."'>";
	  $wempe = explode("-",$wemp);

	  echo "<tr>";
      echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Fecha Inicial (AAAA-MM-DD): </font></b>".$wfecini."</td>";
      echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Fecha Final (AAAA-MM-DD): </font></b>".$wfecfin."</td>";
      echo "</tr>";
      echo "<tr>";
      echo "<td bgcolor=".$wcf." align=center colspan=2><b><font text color=".$wclfg.">Sucursal: </font></b>".$wcco."</td>";
      echo "</tr>";
      echo "<tr>";
      echo "<td bgcolor=".$wcf." align=center colspan=2><b><font text color=".$wclfg.">Empresa: </font></b>".$wemp."</td>";
      echo "</tr>";

      //TRAIGO LAS FACTURAS DETALLADAS POR LOS CONCEPTOS QUE SE NECESITAN, COPAGOS Y SUBSIDIOS, LOS SUBSIDIOS SE IDENTIFCAN EN EL CAMPO 'abotiq'
      //DE LA TABLA '_000116' "
      $q = "  SELECT fenffa, fenfac, fendpa, fennpa, IFNULL((SELECT fdevco-fdevde "
										        ."    		   FROM ".$wbasedato."_000018 FacS, ".$wbasedato."_000024, ".$wbasedato."_000065, ".$tablaConceptos.", ".$wbasedato."_000116 "
										        ."   		  WHERE FacS.fenfec                  BETWEEN '".$wfecini."' AND '".$wfecfin."'"
										        ."    		    AND FacS.fenres                  = empcod "
										        ."     		    AND FacS.fenffa                  = fdefue "
										        ."    			AND FacS.fenfac                  = fdedoc "
										        ."    			AND FacS.fencco                  LIKE '".trim($wccoe[0])."'"
										        ."    			AND FacS.fencod                  LIKE '".trim($wempe[0])."'"
										        ."    			AND fdecon                       = grucod "
										        ."    			AND grutab                       = abocod "
										        ."    			AND FacS.fenest                  = 'on' "
										        ."    			AND abotiq                       = 'on' "
										        ."    			AND ".$wbasedato."_000018.fenffa = FacS.fenffa "
										        ."    			AND ".$wbasedato."_000018.fenfac = FacS.fenfac),'0') SUBSIDIO, "
										 	    ."   IFNULL((SELECT fdevco-fdevde "
												."             FROM ".$wbasedato."_000018 FacC, ".$wbasedato."_000024, ".$wbasedato."_000065, "
										        ."              	".$tablaConceptos.", ".$wbasedato."_000116 "
										        ." 			  WHERE FacC.fenfec                  BETWEEN '".$wfecini."' AND '".$wfecfin."'"
										        ."       	    AND FacC.fenres                  = empcod "
										        ."       	    AND FacC.fenffa                  = fdefue "
										        ."       	    AND FacC.fenfac                  = fdedoc "
										        ."       	    AND FacC.fencco                  LIKE '".trim($wccoe[0])."'"
										        ."       	    AND FacC.fencod                  LIKE '".trim($wempe[0])."'"
										        ."       	    AND fdecon                       = grucod "
										        ."       	    AND grutab                       = abocod "
										        ."       	    AND FacC.fenest                  = 'on' "
										        ."       	    AND abocop                       = 'on' "
										        ."       	    AND ".$wbasedato."_000018.fenffa = FacC.fenffa "
										        ."       	    AND ".$wbasedato."_000018.fenfac = FacC.fenfac), '0') COPAGO, "
										        ."   fencod "
		  ."  FROM ".$wbasedato."_000024, ".$wbasedato."_000065, ".$tablaConceptos.", ".$wbasedato."_000116, ".$wbasedato."_000018 "
          ." WHERE fenfec BETWEEN '".$wfecini."' AND '".$wfecfin."'"
          ."   AND fenres = empcod "
      	  ."   AND fenffa = fdefue "
          ."   AND fenfac = fdedoc "
          ."   AND fencco LIKE '".trim($wccoe[0])."'"
          ."   AND fencod LIKE '".trim($wempe[0])."'"
          ."   AND fdecon = grucod "
          ."   AND grutab = abocod "
          ."   AND fenest = 'on' "
 		  ." GROUP BY 1,2,3,4,6 "
          ." ORDER BY 1,2 ";
      $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	  $num = mysql_num_rows($res) or die (mysql_errno()." - ".mysql_error());

	  echo "<table border=1>";

	  $wtotcop=0;
	  $wtotsub=0;

	  $row = mysql_fetch_array($res);

	  echo "<th bgcolor=".$wcf2."><font size=4 text color=#FFFFFF>FUENTE</th>";
	  echo "<th bgcolor=".$wcf2."><font size=4 text color=#FFFFFF>FACTURA</th>";
	  echo "<th bgcolor=".$wcf2."><font size=4 text color=#FFFFFF>DCTO ID.</th>";
	  echo "<th bgcolor=".$wcf2."><font size=4 text color=#FFFFFF>USUARIO</th>";
	  echo "<th bgcolor=".$wcf2."><font size=4 text color=#FFFFFF>SUBSIDIO</th>";
	  echo "<th bgcolor=".$wcf2."><font size=4 text color=#FFFFFF>COPAGO</th>";
	  echo "<th bgcolor=".$wcf2."><font size=4 text color=#FFFFFF>DIFERENCIA</th>";
	  echo "<th bgcolor=".$wcf2."><font size=4 text color=#FFFFFF>IPS o Empresa</th>";

	  for ($i=1;$i<=$num;$i++)
	     {
		  if (is_integer($i / 2))
			 $wcolor = "00FFFF";
			else
			   $wcolor = $wclfa;

		  echo "<tr>";
		  $wcol=((mysql_num_fields($res))-2);

		  for ($k=0;$k<=$wcol;$k++)
		    {
			 if ($k <= $wcol)
			    {
			    if ($k==($wcol-1))        //Si el campo actual es igual al campo de SUBSIDIO (que viene negativo, lo muestro positivo)
			       {
			        echo "<td align=right bgcolor=".$wcolor.">".number_format(abs($row[$k]),0,'.',',')."</td>";
			        $wtotsub=$wtotsub+abs($row[$k]);
		           }
			      else
			         if ($k==$wcol)       //Esto se hace para mostrar el campo de COPAGO con formato
			            {
			             echo "<td align=right bgcolor=".$wcolor.">".number_format($row[$k],0,'.',',')."</td>";
			             $wtotcop=$wtotcop+$row[$k];
		                }
			           else
		                  echo "<td bgcolor=".$wcolor.">".strtoupper($row[$k])."</td>";
		        }
		    }
		  echo "<td align=right bgcolor=".$wcolor.">".number_format((abs($row[$k-2])-$row[$k-1]),0,'.',',')."</td>";
		  //===========================================================================
          //Traigo el NOMBRE DE LA EMPRESA (IPS) de la tabla 000024 (Empresas)
          //===========================================================================
          $wempcod=explode("-",$row[6]);
          $q = " SELECT empnom "
              ."   FROM ".$wbasedato."_000024 "
              ."  WHERE empcod = '".$wempcod[0]."'";
          $resemp = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
          $numemp=mysql_affected_rows();

		  if ($numemp > 0 ) {
		      $rowemp = mysql_fetch_array($resemp);
		      echo "<td align=LEFT bgcolor=".$wcolor.">".$rowemp[0]."</td>";
	         }
		    else
		       echo "<td align=LEFT bgcolor=".$wcolor.">&nbsp</td>";
		  //===========================================================================
		  echo "</tr>";
		  $row = mysql_fetch_array($res);
		 }



	  echo "<tr>";
	  echo "<td colspan=4 align=center  bgcolor=".$wcf2."><font size=4 text color=#FFFFFF>Total general"."</td>";
	  echo "<td ALIGN=right bgcolor=".$wcf2."><font size=4 text color=#FFFFFF>".number_format($wtotsub,0,'.',',')."</td>";
	  echo "<td ALIGN=right bgcolor=".$wcf2."><font size=4 text color=#FFFFFF>".number_format($wtotcop,0,'.',',')."</td>";
	  echo "<td ALIGN=right bgcolor=".$wcf2."><font size=4 text color=#FFFFFF>".number_format(($wtotsub-$wtotcop),0,'.',',')."</td>";
	  echo "<td ALIGN=right bgcolor=".$wcf2." colspan=2>&nbsp</td>";
	  echo "</tr>";
	  echo "</table>";
	 }
	 echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
}
 liberarConexionBD($conex);
?>
</body>
</html>