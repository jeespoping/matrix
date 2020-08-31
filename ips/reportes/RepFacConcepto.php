<html>
<head>
<title>REPORTE FACTURADO POR CONCEPTO</title>

<!-- Funciones Javascript -->
<SCRIPT LANGUAGE="JavaScript1.2">
	function Seleccionar(){
		document.forma.submit();
	}
</SCRIPT>

</head>

<?php
include_once("conex.php");
/*
 * REPORTE DE FACTURACION POR CENTRO DE COSTO, DETALLE Y PROCEDIMIENTO
 */
//=================================================================================================================================
//PROGRAMA: RepFacConcepto.php
//AUTOR: Mauricio Sánchez Castaño.
//TIPO DE SCRIPT: principal
//RUTA DEL SCRIPT: matrix\IPS\Reportes\RepFacConcepto.php

//HISTORIAL DE REVISIONES DEL SCRIPT:
//+-------------------+------------------------+-----------------------------------------+
//|	   FECHA          |     AUTOR              |   MODIFICACION							 |
//+-------------------+------------------------+-----------------------------------------+
//|  2008-03-27       | Mauricio Sanchez       | creación del script.					 |
//+-------------------+------------------------+-----------------------------------------+

//FECHA ULTIMA ACTUALIZACION 	: Diciembre 24 de 2013

/*DESCRIPCION:Este reporte presenta la lista de facturas por centro(s) de costo(s), por detalle y por procedimiento

TABLAS QUE UTILIZA:
 clisur_000003 Maestro de centros de costos.
 clisur_000004 Maestro de conceptos.
 clisur_000018 Información basica de la factura.
 clisur_000066 Relación entre conceptos y procedimientos.
 clisur_000106 Procedimientos.

INCLUDES:
  conex.php = include para conexión mysql

VARIABLES:
 $wbasedato= variable que permite el codigo multiempresa, se incializa desde invocación de programa
 $wfecha=date("Y-m-d");
 $wfecini= fecha inicial del reporte
 $wfecfin = fecha final del reporte
 $wccocod = centro de costos
 $resultado =

//-------------------------------------------------------------------------------------------------------------------------------------------
//	-->	2013-12-24, Jerson trujillo.
//		El maestro de conceptos que actualmente es la tabla 000004, para cuando entre en funcionamiento la nueva facturacion del proyecto
//		del ERP, esta tabla cambiara por la 000200; Entonces se adapta el programa para que depediendo del paramatro de root_000051
//		'NuevaFacturacionActiva' realice este cambio automaticamente.
//-------------------------------------------------------------------------------------------------------------------------------------------

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

	$wbasedato = strtolower($institucion->baseDeDatos);
	$wentidad = $institucion->nombre;

  	$wfecha=date("Y-m-d");
  	$hora = (string)date("H:i:s");
  	$wnomprog="RepFacConcepto.php";  //nombre del reporte

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

  	$wcf1="#41627e";  //Fondo encabezado del Centro de costos
  	$wcf="#c2dfff";   //Fondo procedimientos
  	$wcf2="003366";  //Fondo titulo pantalla de ingreso de parametros
  	$wcf3="#659ec7";  //Fondo encabezado del detalle
  	$wclfg="003366"; //Color letra parametros

  	echo "<form action='RepFacConcepto.php' method=post name='forma'>";
  	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";

  if (!isset($wfecini) or !isset($wfecfin) or !isset($wgrucod) or !isset($wprocod) or !isset($wccocod) or !isset($resultado))
  	{
  		echo "<center><table border=0>";
  		echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=350 HEIGHT=100></td></tr>";
  		echo "<tr><td align=center bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>REPORTE DE FACTURACION POR CONCEPTO</b></font></td></tr>";

		//Parámetros de consulta del reporte
  		if (!isset ($bandera))
  		{
  			$wfecini=$wfecha;
  			$wfecfin=$wfecha;
  		}

		//Fecha inicial de consulta
  		echo "<tr>";
  		echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Fecha inicial facturaci&oacute;n </font></b>";
  		campoFecha("wfecini");
  		echo "</td>";
  		echo "</td>";

  		//Fecha final de consulta
  		echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Fecha final facturaci&oacute;n: </font></b>";
  		campoFecha("wfecfin");
  		echo "</td>";
  		echo "</tr>";

		//Centro de costos
  		echo "<tr>";
  		echo "<td align=center bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Centro de costos:</font></b>";
  		echo "<select name='wccocod' style='width: 350px'>";
  		$q=  "SELECT ccocod, ccodes "
  		."    FROM ".$wbasedato."_000003 "
  		."    ORDER by 1";
  		$res1 = mysql_query($q,$conex);
  		$num1 = mysql_num_rows($res1);
  		if ($num1 > 0 )
  		{
  			echo "<option>%-Todos los centros de costos</option>";
  			for ($i=1;$i<=$num1;$i++)
  			{
  				$row1 = mysql_fetch_array($res1);
  				echo "<option>".$row1[0]." - ".$row1[1]." - ".$row1[2]."</option>";
  			}
  		}
  		echo "</select></td>";

  		//Conceptos
  		echo "<td align=center bgcolor=".$wcf." ><b><font text color=".$wclfg.">Concepto : </font></b>";
  		echo "<select name='wgrucod' style='width: 450px'>";
  		$q2= "SELECT grucod, grudes "
  		."    FROM ".$tablaConceptos." "
  		."    WHERE gruabo != 'on'  "
  		."     order by grucod, grudes ";
  		$res2 = mysql_query($q2,$conex);
  		$num2 = mysql_num_rows($res2);
  		echo "<option>%-Todos los conceptos</option>";
  		for ($i=1;$i<=$num2;$i++)
  		{
  			$row2 = mysql_fetch_array($res2);
  			echo "<option>".$row2[0]." - ".$row2[1]."</option>";
  		}
  		echo "</select></td>";
  		echo "</tr>";

  		//Procedimientos
  		echo "<tr>";
  		echo "<td align=center bgcolor=".$wcf." colspan='2'><b><font text color=".$wclfg."> Procedimiento : </font></b>";
  		echo "<select name='wprocod' style='width: 650px'>";
  		$q3= "SELECT procod, pronom "
  		."    FROM ".$wbasedato."_000103 "
  		."    WHERE proest = 'on'  "
  		."     order by procod, pronom ";
  		$res3 = mysql_query($q3,$conex);
  		$num3 = mysql_num_rows($res3);
  		echo "<option>%-Todos los procedimientos</option>";
  		for ($i=1;$i<=$num3;$i++)
  		{
  			$row3 = mysql_fetch_array($res3);
  			echo "<option>".$row3[0]." - ".$row3[1]."</option>";
  		}
  		echo "</select></td>";
  		echo "<tr>";

  		echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";
  		echo "<input type='HIDDEN' NAME= 'resultado' value='1'>";

  		echo "<tr>";
  		echo "<td align=center bgcolor=".$wcf." COLSPAN='2'><font text color=".$wclfg." ><b>Tipo de reporte:&nbsp;&nbsp;";
    	echo "<input type='radio' name='vol' value='SI' onclick='Seleccionar()' checked> Detallado&nbsp;&nbsp;";
    	echo "<input type='radio' name='vol' value='NO'  onclick='Seleccionar()'>Resumido&nbsp;&nbsp;";
    	echo "</font></b></td>";
    	echo "</tr>";
    	echo "</table>";
    	echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
  	} else {
  		echo "<table border=0 width=100%>";
  		echo "<tr><td align=left><B>Facturacion:</B>".$wentidad."</td>";
  		echo "<td align=right><B>Fecha:</B> ".date('Y-m-d')."</td></tr>";
  		echo "<tr><td align=left><B>Programa:</B> ".$wnomprog."</td>";
  		echo "<td align=right><B>Hora :</B> ".$hora."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>";
  		echo "</table>";
  		echo "<table border=0 align=center >";
  		echo "<tr><td align=center><H1>$wentidad</H1></td></tr>";

  		if ($vol=='SI'){
  			echo "<tr><td><B>REPORTE DE FACTURACION DETALLADO</B></td></tr>";
  		}else{
  			echo "<tr><td><B>REPORTE DE FACTURACION RESUMIDO</B></td></tr>";
  		}
  		echo "</table></br>";

  		echo "<table border=0 align=center >";
  		echo "<tr><td><B>Fecha inicial:</B> ".$wfecini."</td>";
  		echo "<td><B>Fecha final:</B> ".$wfecfin."</td>";
  		echo "<td><B>Concepto :</B> ".$wgrucod."</td></tr>";
  		echo "<tr><td><B>Procedimiento :</B> ".$wprocod."</td>";
  		echo "<tr><td colspan=3><B>Centro de Costo :</B> ".$wccocod."</td></tr>";
  		echo "</table>";

  		echo "<A href='RepFacConcepto.php?wemp_pmla=".$wemp_pmla."&wfecini=".$wfecini."&wfecfin=".$wfecfin."&wgrucod=".$wgrucod."&wprocod=".$wprocod."&bandera='1'><center>VOLVER</center></A><br>";
  		echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
  		echo "<input type='HIDDEN' NAME= 'wfecini' value='".$wfecini."'>";
  		echo "<input type='HIDDEN' NAME= 'wfecfin' value='".$wfecfin."'>";
  		echo "<input type='HIDDEN' NAME= 'wgrucod' value='".$wgrucod."'>";
  		echo "<input type='HIDDEN' NAME= 'wprocod' value='".$wprocod."'>";
  		echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";

  		//Preparación de los parámetros
  		$vecCco=explode('-', $wccocod);
  		$vecGru=explode('-', $wgrucod);
  		$vecPro=explode('-', $wprocod);

	//Consulta
	$q = "SELECT cargos.Tcarser, ccostos.Ccodes, inventario.Grucod, inventario.Grudes, cargos.Tcarprocod, cargos.Tcarpronom, count(*), sum(relCargos.Rcfval) "
		." FROM "
			.$wbasedato."_000003 ccostos,"
			.$tablaConceptos." inventario,"
			.$wbasedato."_000018 factura,"
			.$wbasedato."_000066 relCargos,"
			.$wbasedato."_000106 cargos"
		." WHERE"
			." ccostos.Ccocod = cargos.Tcarser"
			." AND cargos.Tcarconcod = inventario.Grucod"
			." AND inventario.Gruabo = 'off'"
			." AND factura.Fenest = 'on'"
			." AND relCargos.Rcfest = 'on'"
			." AND relCargos.Rcfffa = factura.Fenffa"
			." AND relCargos.Rcffac = factura.Fenfac"
			." AND relCargos.Rcfreg = cargos.Id"
			." AND cargos.Tcarser LIKE '".trim($vecCco[0])."'"
			." AND cargos.Tcarconcod LIKE '".trim($vecGru[0])."'"
			." AND cargos.Tcarprocod LIKE '".trim($vecPro[0])."'"
			." AND factura.Fenfec BETWEEN '".$wfecini."' AND '".$wfecfin."'"
		." GROUP BY"
			." cargos.Tcarser, ccostos.Ccodes, inventario.Grucod, inventario.Grudes, cargos.Tcarprocod, cargos.Tcarpronom "
		." ORDER BY"
			." cargos.Tcarser, inventario.Grucod, cargos.Tcarprocod;";

		$err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);

		//Variables acumuladoras
		$acumCco = 0;
		$acumGru = 0;
		$acumTotal = 0;

		if($num > 0){
			//Variables de control
			$cdCco = "";
			$cdGru = "";
			$auxNombreCco = "";
			$auxNombreGru = "";

			$cont1 = 1;
			$row = mysql_fetch_array($err);

			echo "<table border=0 align=center>";

			while($cont1 <= $num){

				//Encabezado centro de costos
				echo "<tr>";
				echo "<td align=center colspan='4' bgcolor=".$wcf1."><font text color=#efffff><b>".$row[0]." - ".$row[1]."</b></font></td>";
				echo "</tr>";

				//Muestra el titulo de cada columna
				if($cont1 == 1){
					echo "<tr>";
					echo "<td align=center bgcolor=".$wcf1."><font text color=#efffff><b>CODIGO</b></font></td>";
					echo "<td align=center bgcolor=".$wcf1."><font text color=#efffff><b>NOMBRE</b></font></td>";
					if($vol == 'SI') {
						echo "<td align=center width='30' bgcolor=".$wcf1."><font text color=#efffff><b>CANTIDAD</b></font></td>";
					}
					echo "<td align=center bgcolor=".$wcf1."><font text color=#efffff><b>VALOR</b></font></td>";
					echo "</tr>";
				}

				$cdCco = $row[0];
				while($cdCco == $row[0]){

					//Si es detallado $vol viene con el valor SI, en ese caso se despliega detallado
					if ($vol=='SI'){
						echo "<tr>";
						echo "<td align=center colspan='4' bgcolor=".$wcf3."><font text color=#efffff>".$row[2]." - ".$row[3]."</b></font></td>";
						echo "</tr>";
					}

					$cdGru = $row[2];

					while($cdGru == $row[2]){
						if ($vol=='SI'){
							echo "<tr>";
							echo "<td align=left bgcolor=".$wcf.">".$row[4]."</td>";
							echo "<td align=left bgcolor=".$wcf.">".$row[5]."</td>";
							echo "<td align=center bgcolor=".$wcf.">".$row[6]."</td>";
							echo "<td align=right bgcolor=".$wcf.">".number_format($row[7],0,'.',',')."</td>";
							echo "</tr>";
						}
						$acumGru += $row[7];
						$auxNombreCco = $row[1];
						$auxNombreGru = $row[3];
						$row = mysql_fetch_array($err);
						$cont1++;
					}

					if ($vol=='SI'){
						echo "<tr>";
						echo "<td align=right colspan='4' bgcolor=".$wcf3."><font text color=#efffff>TOTAL: ".number_format($acumGru,0,'.',',')."</b></font></td>";
						echo "</tr>";
					} else {
						echo "<tr>";
						echo "<td width='40' bgcolor=".$wcf.">".$cdGru."</b></td>";
						echo "<td bgcolor=".$wcf.">".$auxNombreGru."</b></td>";
						echo "<td align='center' bgcolor=".$wcf.">".number_format($acumGru,0,'.',',')."</b></td>";
						echo "</tr>";
					}

					$acumCco += $acumGru;
					$acumGru = 0;
				}

				echo "<tr>";
				echo "<td align=right colspan='4' bgcolor=".$wcf1."><font text color=#efffff><b>TOTAL ".$auxNombreCco.": ".number_format($acumCco,0,'.',',')."</b></font></td>";
				echo "</tr>";
				$acumTotal += $acumCco;
				$acumCco = 0;
			}
			echo "<tr>";
			echo "<td align=right colspan='4' bgcolor=".$wcf1."><font text color=#efffff><b>TOTAL FACTURACION: ".number_format($acumTotal,0,'.',',')."</b></font></td>";
			echo "</tr>";
			echo "</table>";
		} else {
			echo "<table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
			echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>No se encontraron documentos con los criterios especificados</td><tr>";
		}
//		echo "<A href='RepFacConcepto.php?wemp_pmla=".$wemp_pmla."&wfecini=".$wfecini."&wfecfin=".$wfecfin."&wgrucod=".$wgrucod."&wprocod=".$wprocod."&bandera='1'><center>VOLVER</center></A><br>";
//		echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
  	}
}
liberarConexionBD($conex);
?>
</html>
