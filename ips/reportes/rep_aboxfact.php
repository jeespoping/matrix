<html>
<head>
<title>Reporte De Abonos Pendientes Por Facturar Por Tipo De Empresa</title>
</head>
<font face='arial'>
<BODY TEXT="#000066">

<script type="text/javascript">
	function enter()
	{
		document.forms.rep_gesreque.submit();
	}

	function inicioReporte(wemp_pmla,wfecini,wfecfin,wsede,wgrupo,wsubgrupo)
	{
	 	document.location.href='rep_aboxfact.php?wemp_pmla='+wemp_pmla;
	}

	function cerrarVentana()
	{
	 window.close()
	}
</script>

<?php
include_once("conex.php");

/*******************************************************************************************************************************************
*                                             REPORTE DE ABONOS PENDIENTES POR FACTURAR                                                    *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : Reporte para ver los abonos que estan pendientes por facturar x tipo de empresa.                            |
//AUTOR				          : Ing. Gustavo Alberto Avendano Rivera.                                                                       |
//FECHA CREACION			  : SEPTIEMBRE 07 DE 2007.                                                                                      |
//FECHA ULTIMA ACTUALIZACION  : SEPTIEMBRE 07 DE 2007.                                                                                      |
//DESCRIPCION			      : Este reporte sirve para observar los abonos pendientes por facturar x tipo de empresa.                      |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//root_000050       : Tabla de Bases de datos por Empresa.                                                                                  |
//clisur_000106     : Tabla de Detalle de cargos                                                                                            |
//clisur_000004     : Tabla de Maestros de Conceptos.                                                                                       |
//clisur_000021     : Tabla de Recibo de Cajas Detalles Facturas.                                                                           |
//clisur_000024     : Tabla de Maestros de Empresas.                                                                                        |
//                                                                                                                                          |
// Si llaman que no sale algun abono o que el abono esta facturado y sale en el reporte es por que en la tabla 21 en el campo factura       |
// aparece en blanco, verificar en la tabla 106 , 66 y la 21.                                                                               |
//                                                                                                                                          |
//==========================================================================================================================================
//MODIFICACIONES:
//-------------------------------------------------------------------------------------------------------------------------------------------
//	-->	2013-12-24, Jerson trujillo.
//		El maestro de conceptos que actualmente es la tabla 000004, para cuando entre en funcionamiento la nueva facturacion del proyecto
//		del ERP, esta tabla cambiara por la 000200; Entonces se adapta el programa para que depediendo del paramatro de root_000051
//		'NuevaFacturacionActiva' realice este cambio automaticamente.
//-------------------------------------------------------------------------------------------------------------------------------------------
//2012-08-21	Camilo Zapata:
//							 - se cambio el query para que realice el calculo de saldo sin facturar de cada abono y en caso de que sea mayor a cero lo muestre en pantalla.
//2012-08-16	Camilo Zapata:
//							 - se le modificó el query para que solo se incluyan aquellos abonos que no tienen facturas asociadas.
//2012-08-06	Camilo Zapata:
//							  - Se le dió al reporte la hoja de estilos que tienen los demas programas de matrix.
//							  - se Modifico el script para que utilice la funcion de consulta de institución por código, debido a que no estaba trayendo el prefijo de las tablas de manera
//								correcta.
//

$wactualiz="Diciembre 24 de 2013";

session_start();
if(!isset($_SESSION['user']))
 {
  echo "error";
 }
else
{

 $empresa='root';

 include_once("root/comun.php");
 

 

 //encabezado("REPORTE DE ABONOS PENDIENTES X FACTURAR",$wactualiz,$empresa);
$empre1="";
$empre2="";
$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
$empre1 = strtolower($institucion->baseDeDatos);
$winstitucion = $institucion->nombre;
encabezado("REPORTE DE ABONOS PENDIENTES X FACTURAR",$wactualiz,$empre1);
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
$tablaConceptos = $empre1.(($nuevaFacturacion == 'on') ? '_000200' : '_000004');
//----------------------------------------------------------------------------------------------

#echo $empre1;
 if (!isset($fec2) or $fec2 == '')
  {
  echo "<form name='rep_aboxfact' action='' method=post>";
  echo '<center><table align=center cellspacing="1" >';

  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////RANGO DE FECHAS

  echo "<tr class='encabezadotabla'>";
  echo "<td align=center colspan=2>Elija el Rango de fechas a consultar</td>";
  echo "</tr>";

  $hoy=date("Y-m-d");

  if (!isset($fec2))
       $fec2=$hoy;

	if(!isset($fec2fin))
		$fec2fin=$hoy;
	  echo "<tr id='fechas' align='center'>";
		echo "<td id='filafec' class='fila2' algin=center colspan=1> inicio corte: ";
		campoFechaDefecto( "fec2", $fec2 );
		 echo "</td>";
	  echo "<td id='filafec' class='fila2' algin=center colspan=1> final: ";
		campoFechaDefecto( "fec2fin", $fec2fin );
	  echo "</td></tr>";

   echo "<tr><td alinn=center colspan=4 bgcolor=#FFFFFF><b>&nbsp;</b></td></tr>";
   echo "<tr><td align=center colspan=4><input type='submit' id='searchsubmit' value='OK'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table></center>";
   echo '</div>';
   echo '</div>';
   echo '</div>';
   echo "<input type='hidden' name=bandera value=1>";

  }
 else // Cuando ya estan todos los datos escogidos
  {
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION

    echo "<center><table border=1>";
	echo "<tr class='encabezadotabla'><td align=center colspan=7><b>FECHA DE CORTE A: <i>".$fec2."</i> HASTA: <i>".$fec2fin."</i></b></b></font></td></tr>";
	echo "<tr class='fila1'><td align=center><b>FECHA</b></td><td align=center><b>FUENTE</b></td><td align=center><b>NRO_ABONO</b></td>" .
			 "<td align=center><b>HISTORIA</b></td><td align=center><b>INGRESO</b></td><td align=center><b>VALOR TOTAL</b></td><td align=center><b>VALOR X FACTURAR</b></td></tr>";

	//TABLA TEMPORAL DE ABONOS EN LA 106
	$qaux = "DROP TABLE IF EXISTS tmpAbonos";
	$err = mysql_query($qaux, $conex);
	$qtemp = "CREATE TEMPORARY TABLE IF NOT EXISTS tmpAbonos "
			."(INDEX idx(Tcarhis, Tcaring, registro))"
			."SELECT Tcarres, Tcarfec, Tcarhis, Tcaring, ABS(tcarvto) total, (ABS(tcarvto)-(ABS(tcarfex)+ABS(tcarfre))) valor, tcarfex, tcarfre, a.id registro  "
			."  FROM ".$empre1."_000106 a, ".$tablaConceptos.""
			." WHERE Tcarfec BETWEEN '".$fec2."' and '".$fec2fin."'
				 AND Gruabo = 'on'
				 AND Tcarconcod = Grucod
				 AND Tcarfac = 'S'
				 AND Tcarest = 'on'
				 HAVING(valor>0)";
	#echo $qtemp;
	$err = mysql_query($qtemp, $conex) or die (mysql_error());

//					    tip emp,     fechaCargo      fuente    numero  historia  ingreso
	$queryAbonos = "SELECT Tcarres, tmpAbonos.Tcarfec, rdefue, rdenum, Tcarhis, Tcaring, total, valor "
				."    FROM tmpAbonos, ".$empre1."_000021 a, ".$empre1."_000040 "
				."	 WHERE Rdehis = Tcarhis
					   AND Rdeing = Tcaring
					   AND Rdereg = registro
					   AND a.fecha_data BETWEEN '".$fec2."' and '".$fec2fin."'
					   AND Carabo='on'
					   AND Rdefue = Carfue
					   AND Rdeest ='on'
					 ORDER BY 1,2,5,6,3,4";

	#echo "<br>".$queryAbonos;
	$err1 = mysql_query($queryAbonos,$conex) or die("error: ".mysql_error()." número: ".mysql_error);
	$num1 = mysql_num_rows($err1);

    $tempant='';
	$totxfac=0;
	$totxfgen=0;
	$total=0;

    $tempant='';
	$swtitulo='SI';

	$fechaant='';
	$fteant='';
	$aboant=0;
	$hisant='';
	$ingant='';
	$vltotal=0;
	$vlrtotant=0;
	$vlrant=0;
	$wcfant='';

	for ($i=1;$i<=$num1;$i++)
	 {
	  if (is_int ($i/2))
	   {
	   	$wcf="fila1";  // color de fondo
	   }
	  else
	   {
	   	$wcf="fila2"; // color de fondo
	   }

     $row1 = mysql_fetch_array($err1);

   	 if ($swtitulo=='SI')
	  {
       $tempant = $row1[0];
	   echo "<tr class='encabezadotabla'><td align=center colspan=2><b>TIPO DE EMPRESA : </b></td><td align=center colspan=5>".$tempant."</td></tr>";
	   $swtitulo='NO';

	   if ($aboant<>0)
	    {
	     echo "<tr  class=".$wcfant."><td align=center>".$fechaant."</td><td align=center>".$fteant."</td><td align=center>".$aboant."</td><td align=center>".$hisant."</td><td align=center>".$ingant."</td><td align=center>".number_format($vlrtotant)."</td><td align=center>".number_format($vlrant)."</td></tr>";
	     $aboant=0;
	    }

	   }

	 if ($tempant==$row1[0] )
	  {
	   echo "<tr  class=".$wcf."><td align=center>".$row1[1]."</td><td align=center>".$row1[2]."</td><td align=center>".$row1[3]."</td><td align=center>".$row1[4]."</td><td align=center>".$row1[5]."</td><td align=center>".number_format($row1[6])."</td><td align=center>".number_format($row1[7])."</td></tr>";
	   $totxfac=$totxfac+$row1[7];
	   $totxfgen=$totxfgen+$row1[6];
	  }
	 else
	  {

	   echo "<tr><td alinn=center colspan=7 bgcolor=#FFFFFF><b>&nbsp;</b></td></tr>";
	   echo "<tr><td align=center colspan=3 class='encabezadotabla'><font text color=#FFFFFF><b>TOTAL TIPO EMPRESA : </b></font></td><td align=center colspan=2>".$tempant."</td><td>".number_format($totxfgen)."</td><td align=center>".number_format($totxfac)."</td></tr>";
	   echo "<tr><td alinn=center colspan=7 bgcolor=#FFFFFF><b>&nbsp;</b></td></tr>";
	   $total+=$totxfac;
	   $totxfac=0;
	   $totxfgen=0;
	   $swtitulo='SI';
	   $fechaant=$row1[1];
	   $fteant=$row1[2];
	   $aboant=$row1[3];
	   $hisant=$row1[4];
	   $ingant=$row1[5];
	   $vlrant=$row1[7];
	   $vlrtotant=$row1[6];
	   //$vltotal=$row1[6]
	   $wcfant=$wcf;
	   $totxfac=$totxfac+$row1[7];
	   $totxfgen=$totxfgen+$row1[6];
	  }
	}

	$total+=$totxfac;
	echo "<tr><td alinn=center colspan=7 bgcolor=#FFFFFF><b>&nbsp;</b></td></tr>";
	echo "<tr><td align=center colspan=3 class='encabezadotabla'><b>TOTAL TIPO EMPRESA : </b></td><td align=center colspan=2>".$tempant."</td><td>".number_format($totxfgen)."</td><td align=center>".number_format($totxfac)."</td></tr>";
	echo "<tr><td align=center colspan=3 class='encabezadotabla'><b>TOTAL TIPO EMPRESA : </b></td><td align=center colspan=2>".$tempant."</td><td>".number_format($totxfgen)."</td><td align=center>".number_format($totxfac)."</td></tr>";
	echo "<tr><td alinn=center colspan=7 bgcolor=#FFFFFF><b>&nbsp;</b></td></tr>";
	echo "<tr><td align=center colspan=6 class='encabezadotabla'><b>TOTAL GENERAL : </b></td><td align=center>".number_format($total)."</td></tr>";

	echo "</table>"; // cierra la tabla o cuadricula de la impresión

  } // cierre del else donde empieza la impresión
echo "<br>";
echo "<br>";
echo "<center><table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
echo "<tr><td align=center><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
if(isset($bandera)){
echo "<tr><td align=center>&nbsp;</td></tr>";
echo "<tr><td align=center><input type='button' value='Retornar' onClick='javascript:inicioReporte(\"$wemp_pmla\");'></td></tr>";
}
echo "</table></center>";
}
?>
