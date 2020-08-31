<html>
<head>
<title>REPORTE DE FORMAS DE PAGO RECIBIDAS POR TERCERO</title>

<!-- Funciones Javascript -->
<SCRIPT LANGUAGE="JavaScript1.2">
	function onLoad(){	loadMenus();  }
	function Seleccionar(){
		document.forma.submit();
	}
	function calendario(id,vrl){
		if (vrl == "1"){
			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfecini',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
		}
		if (vrl == "2"){
			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfecfin',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
		}
	}
</SCRIPT>

</head>

<!-- Inclusion del calendario -->
<link rel="stylesheet" href="../../zpcal/themes/winter.css" />

<!-- Fin inclusión calendario -->

<?php
include_once("conex.php");
/*
<script type="text/javascript" src="../../zpcal/src/utils.js"></script>
<script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
<script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>
<script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>
 */

/*
 * REPORTE DE FORMAS DE PAGO RECIBIDAS POR TERCERO
 */
//=================================================================================================================================
//PROGRAMA: RepFormasdePagoRecibidasXTercero.php
//AUTOR: Juan Carlos Hernandez.
//TIPO DE SCRIPT: principal
//RUTA DEL SCRIPT: matrix\IPS\Reportes\RepFormasdePagoRecibidasXTercero.php

//HISTORIAL DE REVISIONES DEL SCRIPT:
//+-------------------+------------------------+-----------------------------------------+
//|	   FECHA          |     AUTOR              |   MODIFICACION							 |
//+-------------------+------------------------+-----------------------------------------+
//|  2009-04-01       | Juan C. Hernandez      | creación del script.					 |
//+-------------------+------------------------+-----------------------------------------+

//FECHA ULTIMA ACTUALIZACION 	: 2013-12-24
//-------------------------------------------------------------------------------------------------------------------------------------------
//  --> 2016-11-21, Arleyda Insignares Ceballos,
//       Se corrigen los cálculos, agregando dos columnas nuevas: total facturación por tercero, y valor de facturación calculando el 
//       porcentaje de participación. Se adicionan los abonos anteriores a la Fecha inicial.
//	-->	2013-12-24, Jerson trujillo.
//		 El maestro de conceptos que actualmente es la tabla 000004, para cuando entre en funcionamiento la nueva facturacion del proyecto
//		 del ERP, esta tabla cambiara por la 000200; Entonces se adapta el programa para que depediendo del paramatro de root_000051
//		'NuevaFacturacionActiva' realice este cambio automaticamente.
//-------------------------------------------------------------------------------------------------------------------------------------------

/*DESCRIPCION:Este reporte muestra los valores recibidos por las formas de pago tipo tarjeta (debito o credito) para cada tercero, de
              acuerdo a las facturas canceladas dentro de un periodo o rango de fechas dado.

TABLAS QUE UTILIZA:
  basedato_000018 Encabezado de facturas.
  basedato_000020 Encabezado de Recibos de caja y Notas.
  basedato_000021 Detalle de Recibos de caja y Notas.
  basedato_000022 Detalle de Recibos de caja con forma de pago.
  basedato_000023 Maestro de formas de pago.
  basedato_000040 Maestro de fuentes de cartera.
  basedato_000051 Maestro de Médicos u Odontologos.
  basedato_000065 Detalle de facturas y otros.

INCLUDES:
  conex.php = include para conexión mysql
  comun.php = include con funciones y/o clases comunes

VARIABLES:
 $wbasedato= variable que permite el codigo multiempresa, se incializa desde invocación de programa
 $wfecha=date("Y-m-d");
 $wfecini= fecha inicial del reporte
 $wfecfin = fecha final del reporte
 $resultado =
=================================================================================================================================*/
include_once("root/comun.php");

//Inicio
if(!isset($_SESSION['user'])){
	echo "error";
}else{

	if(!isset($wemp_pmla)){
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
	}

	$conex = obtenerConexionBD("matrix");

	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

	$wbasedato = $institucion->baseDeDatos;
	$wentidad = $institucion->nombre;

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

  	$wfecha=date("Y-m-d");
  	$hora = (string)date("H:i:s");
  	//$wnomprog="RepFormasdePagoRecibidasXTercero.php";  //nombre del reporte

  	$wcf1="#41627e";  //Fondo encabezado del Centro de costos
  	$wcf="#c2dfff";   //Fondo procedimientos
  	$wcf2="003366";   //Fondo titulo pantalla de ingreso de parametros
  	$wcf3="#659ec7";  //Fondo encabezado del detalle
  	$wclfg="003366";  //Color letra parametros

  	echo "<form action='RepFormasdePagoRecibidasXTercero.php' method=post name='forma'>";
  	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";

  //if (!isset($wfecini) or !isset($wfecfin) or !isset($wgrucod) or !isset($wprocod) or !isset($wccocod) or !isset($resultado))
  if (!isset($wfecini) or !isset($wfecfin) or !isset($wtippac))
  	{
  		echo "<center><table border=0>";
  		echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=350 HEIGHT=100></td></tr>";
  		echo "<tr><td align=center bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>Reporte de Formas de pago recibidas por Tercero</b></font></td></tr>";
  		echo "</table>";
  		echo "<br>";
		//Parámetros de consulta del reporte
		if (!isset($wfecini) or !isset($wfecfin))
		   {
  		    $wfecini=$wfecha;
  		    $wfecfin=$wfecha;
  		   }

  		echo "<center><table border=0>";
		//Fecha inicial de recaudo
  		$cal="calendario('wfecini','1')";
  		echo "<tr>";
  		echo "<td bgcolor=".$wcf." align=center class='texto3' colspan=1><b>Fecha Inicial de Recaudo: </font></b><INPUT TYPE='text' readonly='readonly' NAME='wfecini' id='wfecini' value=".$wfecini." SIZE=10><input type='button' name='envio1' id='envio1' value='...' onclick=".$cal."' size=10 maxlength=10></td>";
        ?>
		  <script type="text/javascript">//<![CDATA[
		 	Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'12',electric:false,inputField:'wfecini',button:'envio1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});//]]>
		  </script>
		<?php


  		//Fecha final de recaudo
  		$cal="calendario('wfecfin','2')";
  		echo "<td bgcolor=".$wcf." align=center class='texto3' colspan=1><b>Fecha Final de Recaudo: </font></b><INPUT TYPE='text' readonly='readonly' NAME='wfecfin' id='wfecfin' value=".$wfecfin." SIZE=10><input type='button' name='envio2' id='envio2' value='...' onclick=".$cal."' size=10 maxlength=10></td>";
        ?>
		  <script type="text/javascript">//<![CDATA[
		 	Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'12',electric:false,inputField:'wfecfin',button:'envio2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});//]]>
		  </script>
		<?php
  		echo "</tr>";

		//Tipo de Paciente
  		echo "<tr>";
  		echo "<td align=center bgcolor=".$wcf." COLSPAN='2'><font text color=".$wclfg." ><b>Tipo de Paciente:&nbsp;&nbsp;";
    	echo "<input type='radio' name='wtippac' value='E' onclick='Seleccionar()'>Empresa&nbsp;&nbsp;";
    	echo "<input type='radio' name='wtippac' value='P' onclick='Seleccionar()'>Particular&nbsp;&nbsp;";
    	echo "<input type='radio' name='wtippac' value='%' onclick='Seleccionar()' checked>Ambos&nbsp;&nbsp;";
    	echo "</font></b></td>";
    	echo "</tr>";
    	echo "</table>";
    } else
        {
          echo "<table align=center>";
          echo "<tr>";
          echo "<td align=center colspan=7><H1>$wentidad</H1></td>";
          echo "</tr>";
          echo "<tr>";
  		  //echo "<td align=left  colspan=3><B>&nbsp</B></td>";
  		  echo "<td align=left colspan=7><B>Fecha:</B> ".date('Y-m-d')."</td>";
  		  echo "</tr>";
  		  echo "<tr>";
  		  echo "<td align=left  colspan=5><B>Hora :&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</B> ".$hora."</td>";
  		  //echo "<td align=right colspan=2><B>Programa:</B> ".$wnomprog."</td>";
  		  echo "</tr>";
  		  echo "<tr><td>&nbsp</td></tr>";

  		  switch ($wtippac)
  		    {
  		     case 'E':
  		       { echo "<tr><td colspan=7 align=center><B><font size=4>Reporte de Formas de pago recibidas por Tercero (Empresas)</font></B></td></tr>";
  		         $wtpac= " AND fentip != '01-PARTICULAR' "; }
  		         break;
  		     case 'P':
  			   { echo "<tr><td colspan=7 align=center><B><font size=4>Reporte de Formas de pago recibidas por Tercero (Particulares)</font></B></td></tr>";
  			     $wtpac= " AND fentip = '01-PARTICULAR' ";}
  			     break;
  		     case '%':
  			   { echo "<tr><td colspan=7 align=center><B><font size=4>Reporte de Formas de pago recibidas por Tercero (Empresas y Particulares)</font></B></td></tr>";
  			     $wtpac= " AND fentip LIKE '%' ";}
  			     break;
  		    }

  		  echo "<tr>";
  		  echo "<td colspan=7 align=center><B>Fecha inicial:</B> ".$wfecini." <B>Fecha final:</B> ".$wfecfin."</td>";
  		  echo "</tr>";

  		  echo "<input type='HIDDEN' NAME= 'wfecini' value='".$wfecini."'>";
  		  echo "<input type='HIDDEN' NAME= 'wfecfin' value='".$wfecfin."'>";
  		  echo "<input type='HIDDEN' NAME= 'wtippac' value='".$wtippac."'>";


	      //==============================================================================================================================================================
		  //1ra Consulta:
		  //==============================================================================================================================================================
		  //Con este query traigo todas facturas canceladas (saldo cero) a la fecha y que no tengan recibos ni notas credito despues de la fecha máxima del reporte,
		  //porque puede ser que una factura tenga saldo cero, pero se este generando un período anterior al actual y no debe traer las facturas que no estaban canceladas
		  //en ese período solicitado. Además solo trae las facturas con conceptos compartidos.
		  //Trae los recibos y notas con su respectiva factura, concepto, documento del tercero, % porcentaje de participacion del concepto con respecto al total de la factura. 
		  //==============================================================================================================================================================
		  $q = " CREATE TEMPORARY TABLE IF NOT EXISTS participacion AS "
		      ." SELECT renfue, rennum, fdeter, rdefac,fdefue, fdedoc, fdecon,  fensal, 'ensauno' ensayo,fdevco, fdefac,round((fdepte/100),2) Portercero, fdepte, ((((fdevco+fdevde)/(fenval+fenabo))*100)) PartCon "
		      ."   FROM ".$wbasedato."_000065, ".$wbasedato."_000018, ".$wbasedato."_000020, ".$wbasedato."_000021, ".$wbasedato."_000040,  ".$tablaConceptos." "
		      ."  WHERE ( (renfec  BETWEEN '".$wfecini."' AND '".$wfecfin."') OR "  
		      ."    (fenfec  BETWEEN '".$wfecini."' AND '".$wfecfin."' AND renfec  < '".$wfecini."') )"
		      ."    AND fdeffa  = rdeffa"
		      ."    AND fdefac  = rdefac "		      
		      ."    AND fdeffa  = fenffa "
		      ."    AND fdefac  = fenfac "	
		      ."    AND fdefue  = fenffa "
		      ."    AND fdedoc  = fenfac "	      		      
 		      ."    AND renfue  = rdefue "
		      ."    AND rennum  = rdenum "
		      ."    AND fensal  = 0 "
		      ."    AND renest  = 'on' "
		      ."    AND rdeest  = 'on' "
		      ."    AND fenest  = 'on' "
		      ."    AND renfue  = carfue "
		      ."    AND (carrec = 'on' "
		      ."     OR  carncr = 'on' ) "
		      ."    AND rdefac  NOT IN ( SELECT rdefac "
		      ."                	       FROM ".$wbasedato."_000020, ".$wbasedato."_000021, ".$wbasedato."_000040 "
		      ."              		      WHERE renfue   = rdefue "
		      ."                    	    AND rennum   = rdenum "
		      ."                            AND rdeffa   = fdefue "
		      ."                            AND rdefac   = fdedoc "
		      ."                    	    AND renfue   = carfue "
		      ."                    	    AND (carrec  = 'on' "
		      ."                      	     OR  carncr  = 'on') "
		      ."                    	    AND renfec   > '".$wfecfin."'"
		      ."                            AND rdefac  != '' "
		      ."                          GROUP BY 1 ) "
		      ."   AND fdecon = grucod "
		      ."   AND grutip = 'C' " 
		      .$wtpac 		      
		      ."   Group by fdefue,fdedoc,fdecon,fdevco,PartCon"
		      ."   Order by fdefue,fdedoc,fdecon,fdevco,PartCon";

		  $res = mysql_query($q,$conex);

		  //==============================================================================================================================================================
		  //2da Consulta:
		  //==============================================================================================================================================================
		  //Con este query traigo todas facturas canceladas (saldo cero) a la fecha y que no tengan recibos ni notas credito despues de la fecha máxima del reporte,
		  //porque puede ser que una factura tenga saldo cero, pero se este generando un período anterior al actual y no debe traer las facturas que no estaban canceladas
		  //en ese período solicitado. Además solo trae las facturas con conceptos compartidos.
		  //Trae los recibos y notas con su respectiva factura, concepto, documento del tercero, % porcentaje de participacion del concepto con respecto al total de la factura.
		  //No se toma el porcentaje de particiapcion del tercero en el concepto porque la gerencia de SOE dijo que se aplicara sobre toda la forma y no solo sobre la parte que corresponde al médico.
		  //==============================================================================================================================================================
         
          //Consultar porcentaje de participación 
          $q = " CREATE TEMPORARY TABLE IF NOT EXISTS listporcentaje AS " 
              ." SELECT sum(PartCon) Porcentaje,fdeter,fdefue,fdedoc from participacion "
              ." GROUP BY fdeter,fdefue,fdedoc";

          $res = mysql_query($q,$conex);


          //Consultar la tabla de abonos (soe_000021) para sumar los abonos por factura, teniendo en cuenta que los conceptos,
          //maestro de fuentes sean recibos o notas credito.       
          $q = " CREATE TEMPORARY TABLE IF NOT EXISTS lisabonos AS "
              ." SELECT round(sum(rdevca),0) totabono, rdefac factuabo, rdeffa fuenteabo"
              ."      From ".$wbasedato."_000021 A"
              ."      INNER JOIN ".$wbasedato."_000040 B "        
              ."        on A.rdefue = B.carfue "
              ."      WHERE (B.carrec ='on' or B.carncr = 'on') "
              ."        and (B.carenv ='off' and B.carrad = 'off' and B.carglo = 'off') "
              ."        and A.rdeest ='on' "
              ."      GROUP BY A.rdefac, rdeffa" ;
           
          //and A.fecha_data > '".$wfecini."'  
          $res = mysql_query($q,$conex);

           
          //Consultar el valor a descontar cuando son pagos por tarjetas débito o crédito  
          $q = " CREATE TEMPORARY TABLE IF NOT EXISTS listarjetas AS " 
              ." SELECT rfpfpa,rfpvfp,rfpest,rfpnum numerotar,rfpfue fuentetar, fpacom,SUM(round((rfpvfp*(fpacom/100)),0)) AS VALOR_COMI "
		      ."   FROM ".$wbasedato."_000021, ".$wbasedato."_000022, ".$wbasedato."_000023 "
			  ."  WHERE rdefue = rfpfue "
			  ."    AND rdenum = rfpnum " // Relaciona Detalles Forma de Pago
			  ."    AND rfpest = 'on' "
			  ."    AND rfpfpa = fpacod " // Relaciona la 23, Formas de pago para obtener los %
		      ."    AND fpatar = 'on' "
		      ."    AND (rfpfpa = 'TD' or rfpfpa = 'TC') "
			  ."  GROUP BY rfpnum,rfpfue "
			  ."  ORDER BY rfpnum,rfpfue ";
		  
		  $res = mysql_query($q,$conex);

         // Consultar listarjetas totalizado por factura
		  $q = " CREATE TEMPORARY TABLE IF NOT EXISTS lispagos AS " 
              ." SELECT numerotar numeropag,fuentetar fuentepag, rdefac facturapag, SUM(VALOR_COMI) AS VALOR_COMISION "
		      ."   FROM listarjetas, ".$wbasedato."_000021 "
			  ."  WHERE fuentetar = rdefue "
			  ."    AND numerotar = rdenum " 
			  ."    AND rfpest = 'on' "
		      ."    AND (rfpfpa = 'TD' or rfpfpa = 'TC') "
			  ."  GROUP BY rdefac,fuentetar "
			  ."  ORDER BY rdefac,fuentetar ";

		  $res = mysql_query($q,$conex);


          // Consulta final para mostrar en pantalla
          $q=  "  SELECT A.ensayo, A.fdeter, E.mednom, A.Portercero, A.PartCon,A.fdefue, A.fdedoc, A.fdefac, A.fdecon, F.Porcentaje, B.totabono, A.fdevco,"
              ."  SUM(C.VALOR_COMISION * (A.PartCon/100)) TOTAL_VALOR_COMISION, SUM(A.fdevco) VALOR_TOTAL_FAC, "

              ."  SUM(if((B.totabono * (A.PartCon/100)) > A.fdevco, round( if(F.Porcentaje > 100, B.totabono, A.fdevco * (F.Porcentaje/100)),0), round((B.totabono * (A.PartCon/100)),2) )) VALOR_TOTAL_ABO, "              

              ."  SUM(if(A.Portercero=0,0,if(F.Porcentaje >= 100, A.fdevco * A.Portercero,  ( A.fdevco * A.Portercero)))) VALOR_PARTI_FAC, "

              ."  SUM(if(A.Portercero=0,0,(if((B.totabono * (A.PartCon/100)) >= A.fdevco, round( if(F.Porcentaje > 100, B.totabono, A.fdevco * A.Portercero ),0), round((B.totabono * (A.PartCon/100) * A.Portercero),2) )))) VALOR_PARTI_ABO "
		      ."  FROM participacion A "
		      ."  Left join lisabonos B on A.fdefue = B.fuenteabo AND A.fdedoc = B.factuabo "
		      ."  Left join lispagos  C on A.renfue = C.fuentepag AND A.rdefac = C.facturapag "
		      ."  Left join listporcentaje F on A.fdeter = F.fdeter AND A.fdefue = F.fdefue AND A.fdedoc = F.fdedoc "
		      ."  Inner join ".$wbasedato."_000051 E on  A.fdeter = E.meddoc "
			  ."  GROUP BY A.fdeter "
			  ."  ORDER BY A.fdeter,A.fdefac ";
           

		  $res = mysql_query($q,$conex);

          $num = mysql_num_rows($res);

          if ($num > 0)
             {
	          $wtotvalcon=0;
			  $wtotvalfpa=0;
			  $wtotvaldes=0;

			  echo "<table border=1 align=center>";

			  echo "<th colspan=2 align=center class='encabezadoTabla'><font color=white size=4>Profesional</font></th>";
			  echo "<th colspan=1 align=center class='encabezadoTabla'><font color=white size=3>Valor Facturado</font></th>";
			  echo "<th colspan=1 align=center class='encabezadoTabla'><font color=white size=3>Valor Recibido x Facturacion</font></th>";
			  echo "<th colspan=1 align=center class='encabezadoTabla'><font color=white size=3>Valor Partic. Tercero </font></th>";
			  echo "<th colspan=1 align=center class='encabezadoTabla'><font color=white size=3>Valor Recibido Partic. Tercero</font></th>";
			  echo "<th colspan=1 align=center class='encabezadoTabla'><font color=white size=3>Valor a descontar</font></th>";
			  $i = 0;
			  while($row = mysql_fetch_assoc($res)){			  	  

                  
				  if (is_integer($i/2))
		              $wcolor="E8EEF7";
		          else
		              $wcolor="C3D9FF";
   
				  echo "<tr>";
				  echo "<td align=left   bgcolor=".$wcolor.">".$row['fdeter']."</td>";  						
				  echo "<td align=left   bgcolor=".$wcolor.">".$row['mednom']."</td>";  						 
				  echo "<td align=right  bgcolor=".$wcolor.">".number_format($row['VALOR_TOTAL_FAC'],0,'.',',')."</td>"; 
				  echo "<td align=right  bgcolor=".$wcolor.">".number_format($row['VALOR_TOTAL_ABO'],0,'.',',')."</td>"; 
				  echo "<td align=right  bgcolor=".$wcolor.">".number_format($row['VALOR_PARTI_FAC'],0,'.',',')."</td>"; 
				  echo "<td align=right  bgcolor=".$wcolor.">".number_format($row['VALOR_PARTI_ABO'],0,'.',',')."</td>"; 
				  echo "<td align=right  bgcolor=".$wcolor.">".number_format($row['TOTAL_VALOR_COMISION'],0,'.',',')."</td>"; 
				  echo "</tr>";

				  $wtotvalfac1=$wtotvalfac1+$row['VALOR_TOTAL_FAC'];
				  $wtotvalabo1=$wtotvalabo1+$row['VALOR_TOTAL_ABO'];
				  $wtotvalfac2=$wtotvalfac2+$row['VALOR_PARTI_FAC'];
				  $wtotvalabo2=$wtotvalabo2+$row['VALOR_PARTI_ABO'];
				  $wtotvalcomi=$wtotvalcomi+$row['TOTAL_VALOR_COMISION'];
				  
				  $i++;
			  }
			  echo "<tr>";
			  echo "<td align=right  bgcolor=".$wcolor." colspan=2><b><font color=000099 size=3>Totales</font></b></td>";
			  echo "<td align=right  bgcolor=".$wcolor."><b><font color=000099 size=3>".number_format($wtotvalfac1,0,'.',',')."</font></b></td>"; 
			  echo "<td align=right  bgcolor=".$wcolor."><b><font color=000099 size=3>".number_format($wtotvalabo1,0,'.',',')."</font></b></td>"; 
			  echo "<td align=right  bgcolor=".$wcolor."><b><font color=000099 size=3>".number_format($wtotvalfac2,0,'.',',')."</font></b></td>"; 
			  echo "<td align=right  bgcolor=".$wcolor."><b><font color=000099 size=3>".number_format($wtotvalabo2,0,'.',',')."</font></b></td>"; 
			  echo "<td align=right  bgcolor=".$wcolor."><b><font color=000099 size=3>".number_format($wtotvalcomi,0,'.',',')."</font></b></td>"; 
			  echo "</b></tr>";

			  echo "</table>";
		     }
		    else
		      {
			   echo "<table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
			   echo "<tr><td colspan=4 align='center'><font size=3 color='#000080' face='arial'><b>No se encontraron documentos con los criterios especificados</td><tr>";
			   echo "</table>";
		      }
		   echo "<br>";
		   echo "<table align=center>";
		   echo "<div align='center'><A href='RepFormasdePagoRecibidasXTercero.php?wemp_pmla=".$wemp_pmla."&wfecini=".$wfecini."&wfecfin=".$wfecfin."'><center>Retornar</center></A></div>";
		   echo "</table>";
        }
        echo "<table align=center>";
        echo "<br>";
        echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
        echo "</table>";
}
liberarConexionBD($conex);
?>
</html>
