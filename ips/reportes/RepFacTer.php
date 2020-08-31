<html>
<head>
  <title>REPORTE DE FACTURACION POR TERCEROS</title>
</head>

   <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}

    	.titulo1{color:#FFFFFF;background:#006699;font-size:18pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.titulo2{color:#003366;background:#A4E1E8;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.titulo3{color:#003366;background:#57C8D5;font-size:12pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	.titulo4{color:#003366;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.texto1{color:#003366;background:#FFFFFF;font-size:7pt;font-family:Tahoma;text-align:center;}
    	.texto2{color:#003366;background:#FFFFFF;font-size:7pt;font-family:Tahoma;text-align:right;}
    	.texto3{color:#006699;background:#A4E1E8;font-size:9pt;font-weight:bold;font-family:Tahoma;text-align:center;}
    	.texto4{color:#003366;background:#cccccc;font-size:7pt;font-family:Tahoma;text-align:left;}
    	.texto5{color:#003366;background:#FFFFFF;font-size:7pt;font-family:Tahoma;text-align:right;}
    	.texto6{color:#003366;background:#cccccc;font-size:7pt;font-family:Tahoma;text-align:right;}
    	.texto7{color:#003366;background:#FFFFFF;font-size:7pt;font-family:Tahoma;text-align:left;}
    	.acumulado1{color:#003366;background:#FFCC66;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:right;}
    	.acumulado2{color:#003366;background:#FFFFFF;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.acumulado3{color:#003366;background:#FFFFFF;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	.acumulado4{color:#003366;background:#FFFFFF;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:right;}
    	.error1{color:#FF0000;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
   </style>

<SCRIPT LANGUAGE="JavaScript1.2">
function Seleccionar()
{

	if(document.getElementById("wtipres").selectedIndex == -1 || document.getElementById("wtipres").options[document.getElementById("wtipres").selectedIndex].text=="")
		alert ("Debe Ingresar el Tipo de Responsable");
	else
		document.forma.submit();
}
function enter()
{
	document.forms.RepFacTer.submit();
}
</script>
<body>

<?php
include_once("conex.php");

/**
 * NOMBRE:  REPORTE DE FACTURACION POR TERCERO
 *
 * PROGRAMA: RepFacTer.php
 * TIPO DE SCRIPT: PRINCIPAL
 * //DESCRIPCION: Este reporte permite listar la facturacion contenida en un rango de fechas detallada por cpto y tercero, el cual
 * relaciona su respectivo porcentaje de participacion.
 *
 * HISTORIAL DE ACTAULIZACIONES:
 * 2006-12-29 Juan David Jaramillo, creacion del script

// Enero 30 de 2012 :   Ing. Santiago Rivera Botero
// ==========================================================================================================================================
// - Se Adicionó un campo para mostrar el responsable sobre quien realizo la factura
// - Se actualizaron los estilos con los que se presenta el informe
//-------------------------------------------------------------------------------------------------------------------------------------------
//	-->	2013-12-24, Jerson trujillo.
//		El maestro de conceptos que actualmente es la tabla 000004, para cuando entre en funcionamiento la nueva facturacion del proyecto
//		del ERP, esta tabla cambiara por la 000200; Entonces se adapta el programa para que depediendo del paramatro de root_000051
//		'NuevaFacturacionActiva' realice este cambio automaticamente.
//-------------------------------------------------------------------------------------------------------------------------------------------

 * Tablas que utiliza:
 * $wbasedato."_000018: select de Movimiento de Facturas
 * $wbasedato."_000065: select de Detalle de Facturas por Cpto.

 *
 * @author jjaramillo
 * @package defaultPackage
 */

$wautor="Juan David Jaramillo R";
//=================================================================================================================================
include_once("root/comun.php");



$titulo = "REPORTE DE FACTURACION POR TERCERO";
$wactualiz = "Diciembre 24 de 2013";
encabezado($titulo,$wactualiz, "clinica");

session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	if(!isset($wemp_pmla)){
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
	}

	$key = substr($user,2,strlen($user));

	$conex = obtenerConexionBD("matrix");

	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

	$wbasedato = strtolower($institucion->baseDeDatos);
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

	$wentidad = $institucion->nombre;

	echo "<form action='RepFacTer.php' method=post name='forma'>";

	$wfecha=date("Y-m-d");
	$wbd=$wbasedato;

	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";

	if (!isset($wfecini) or !isset($wfecfin) or !isset($wcon)or !isset ($resultado))
	{
		echo "<center><table border=0 width=80%>";

		//INGRESO DE VARIABLES PARA EL REPORTE//
		if(!isset($wfecini ) && !isset($wfecfin ))
			   {
					$wfecini = date("Y-m-d");
					$wfecfin = date("Y-m-d");
			   }

		echo "<tr>";
		echo "<td align=center class='fila1'><b>FECHA INICIAL: </font></b>";
		campoFechaDefecto("wfecini", $wfecini);
		echo "</td>";

		echo "<td align=center class='fila1'><b>FECHA FINAL: </font></b>";
		campoFechaDefecto("wfecfin", $wfecfin);
		echo "</td>";

		echo "</tr>";

		echo "<tr>";
		echo "<td align=left class='fila1' ><b>TIPO DE RESPONSABLE</b>:
			  <select name='wtipres' id='wtipres' onchange='enter()' ondblclick='enter()'>";
			if (isset ($wtipres))
			{
				if ($wtipres=='EMPRESA')
				{
					echo "<option >EMPRESA</option>";
					echo "<option >PARTICULAR</option>";
					echo "<option >AMBOS</option>";
				}
				if ($wtipres=='PARTICULAR')
				{
					echo "<option>PARTICULAR</option>";
					echo "<option>EMPRESA</option>";
					echo "<option>AMBOS</option>";
				}
				if ($wtipres=='AMBOS')
				{
					echo "<option>AMBOS</option>";
					echo "<option>EMPRESA</option>";
					echo "<option>PARTICULAR</option>";
				}
			}
			else
			{
				echo "<option> </option>";
				echo "<option>EMPRESA</option>";
				echo "<option>PARTICULAR</option>";
				echo "<option>AMBOS</option>";
			}
		echo "</select></td>";

		echo "<td align=left class='fila1' ><b>TERCERO</b>:
			  <select name='wter' id='wter' onchange='enter()' ondblclick='enter()'>";

		$query= "SELECT meddoc, mednom ".
				"  FROM ".$wbasedato."_000051 ".
				" WHERE medest = 'on' ".
				" ORDER BY mednom";

		$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);

		echo "<option></option>";
		for ($i=1;$i<=$num;$i++)
		{
			$row = mysql_fetch_array($err);
			if(isset($wter) && $row[0]==$wter)
				echo "<option selected>".$row[0]."-".$row[1]."</option>";
			else
				echo "<option>".$row[0]."-".$row[1]."</option>";
		}

		echo "</select></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td align=left colspan=2 class='fila1' ><b>CONCEPTO</b>:<select name='wcon' onchange='enter()' ondblclick='enter()'>";

		$query= "SELECT grucod, grudes ".
				"  FROM ".$tablaConceptos." ".
				" WHERE gruser in ('A','H') ".
				"   AND gruest = 'on' ".
				" ORDER BY grudes";

		$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);

		echo "<option></option>";
		for ($i=1;$i<=$num;$i++)
		{
			$row = mysql_fetch_array($err);
			if(isset($wcon) && $row[0]==$wcon)
				echo "<option selected>".$row[0]."-".$row[1]."</option>";
			else
				echo "<option>".$row[0]."-".$row[1]."</option>";
		}
		echo "</select></td>";
		echo "</tr>";

		echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";
		echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";
		echo "<input type='HIDDEN' NAME= 'resultado' value='1'>";

		echo "<tr><td align=center class='fila1' COLSPAN='2'>";
		echo "<input type='radio' name='vol' value='SI' onclick='Seleccionar()' > <b>GENERAR REPORTE</b>";
		echo "</td></tr>";
		echo "<tr align='center'><td COLSPAN='2'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></td></tr>";
		echo "</table>";
		echo "</form>";
	}
	//MUESTRA DE DATOS DEL REPORTE
	else
	{
		$wcon1=explode("-",$wcon);
		$wter1=explode("-",$wter);

		echo "<table  align=center  >";
		echo "<tr class=fila2><td><font size=2><B>Fecha: ".date('Y-m-d')."</B></font></td></tr>";
		echo "<tr class=fila2><td><font size=2><B>REPORTE DE FACTURACION POR TERCERO</B></font></td></tr>";
		echo "<tr class=fila2><td><font size=2>Fecha inicial: ".$wfecini."</font></td></tr>";
		echo "<tr class=fila2><td><font size=2>Fecha final: ".$wfecfin."</font></td></tr>";
		echo "<tr class=fila2><td><font size=2>Responsable: ".$wtipres."</font></td></tr>";
		echo "<tr class=fila2><td><font size=2>Tercero: ".$wter."</font></td></tr>";
		echo "<tr class=fila2><td><font size=2>Concepto: ".$wcon."</font></td></tr>";
		echo "</table></br>";

		echo "<input type='HIDDEN' NAME= 'wfecini' value='".$wfecini."'>";
		echo "<input type='HIDDEN' NAME= 'wfecfin' value='".$wfecfin."'>";
		echo "<input type='HIDDEN' NAME= 'wtipres' value='".$wtipres."'>";
		echo "<input type='HIDDEN' NAME= 'wter' value='".$wter."'>";
		echo "<input type='HIDDEN' NAME= 'wcon' value='".$wcon."'>";
		echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";
		echo "<center><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></center>";
		echo "<br/>";
		/***********************************Consulto lo pedido ********************/

		//Realizo la busqueda de las facturas con su respectivo tercero y % de participacion en la 18 y la 65

		switch($wtipres)
		{
			case "EMPRESA":
			$wcres= "E";
			break;
			case "PARTICULAR":
			$wcres= "P";
			break;
			case "AMBOS":
			$wcres= "A";
			break;
		}

		if(!isset($wcon) or ($wcon == ""))
		$wccon="%";
		else
		{
			$wccon=explode("-",$wcon);
			$wccon=$wccon[0];
		}

		if(!isset($wter) or ($wter == ""))
		$wcter="%";
		else
		{
			$wcter=explode("-",$wter);
			$wcter=$wcter[0];
		}

		if($wcres == "P")
		{
			$q = " SELECT fdeter,fdecon,fenffa,fenfac,fenfec,fendpa,fennpa,sum(fdevco-fdevde),fdepte,((sum(fdevco-fdevde)*fdepte)/100),fenres, fdecco,MID(".$wbasedato."_000065.Seguridad,INSTR(".$wbasedato."_000065.Seguridad,'-')+1) ".
				 "   FROM ".$wbasedato."_000018, ".$wbasedato."_000065, ".$tablaConceptos." ".
				 " 	WHERE fenfec between '".$wfecini."'".
				 "    AND '".$wfecfin."'".
				 "    AND fencod = '01' ".
				 "    AND fdecon like '".$wccon."' ".
				 "    AND fdeter like '".$wcter."' ".
				 "    AND fenest = 'on' ".
				 "    AND grutip = 'C' ".
				 "    AND fdefue = fenffa".
				 "    AND fdedoc = fenfac".
				 "    AND grucod = fdecon".
				 "  GROUP BY 1,2,3,4,5,6,7,9,11 ".
				 "  ORDER BY  fdeter,fdecon,fdecco,fenffa,fenfac ";
		}
		else
		{
			if($wcres == "E")
			{
				$q = " SELECT fdeter,fdecon,fenffa,fenfac,fenfec,fendpa,fennpa,sum(fdevco-fdevde),fdepte,((sum(fdevco-fdevde)*fdepte)/100),fenres, fdecco,MID(".$wbasedato."_000065.Seguridad,INSTR(".$wbasedato."_000065.Seguridad,'-')+1) ".
					 "   FROM ".$wbasedato."_000018, ".$wbasedato."_000065, ".$tablaConceptos." ".
					 " 	WHERE fenfec between '".$wfecini."'".
					 "    AND '".$wfecfin."'".
					 "    AND fencod <> '01' ".
					 "    AND fdecon like '".$wccon."' ".
					 "    AND fdeter like '".$wcter."' ".
					 "    AND fenest = 'on' ".
					 "    AND grutip = 'C' ".
					 "    AND fdefue = fenffa".
					 "    AND fdedoc = fenfac".
					 "    AND grucod = fdecon".
					 "  GROUP BY 1,2,3,4,5,6,7,9,11 ".
					 "  ORDER BY  fdeter,fdecon,fdecco,fenffa,fenfac ";
			}
			else
			{
				$q = " SELECT fdeter,fdecon,fenffa,fenfac,fenfec,fendpa,fennpa,sum(fdevco-fdevde),fdepte,((sum(fdevco-fdevde)*fdepte)/100),fenres, fdecco,MID(".$wbasedato."_000065.Seguridad,INSTR(".$wbasedato."_000065.Seguridad,'-')+1) ".
					 "   FROM ".$wbasedato."_000018, ".$wbasedato."_000065, ".$tablaConceptos." ".
					 " 	WHERE fenfec between '".$wfecini."'".
					 "    AND '".$wfecfin."'".
					 "    AND fdecon like '".$wccon."' ".
					 "    AND fdeter like '".$wcter."' ".
					 "    AND fenest = 'on' ".
					 "    AND grutip = 'C' ".
					 "    AND fdefue = fenffa".
					 "    AND fdedoc = fenfac".
					 "    AND grucod = fdecon".
					 "  GROUP BY 1,2,3,4,5,6,7,9,11 ".
					 "  ORDER BY  fdeter,fdecon,fdecco,fenffa,fenfac ";

			}
		}

		$err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);

		$wterant = "";
		$wconant = "";
		$wccoant="";
		$wtotcon = 0;
		$wtotvpo = 0;
		$wtotter = 0;
		$wtotpag = 0;
		$wgtocon = 0;
		$wgtoter = 0;

		if ($num > 0)
		{
			echo "<table align=center border=0 width=90%>";
			echo "<tr class=encabezadoTabla>";
			echo "<th align=CENTER >Documento</th>";
			echo "<th align=CENTER >Fecha</th>";
			echo "<th align=CENTER >Identif.Pac</th>";
			echo "<th align=CENTER >Nombre del Paciente</th>";
			echo "<th align=CENTER >Valor Facturado</th>";
			echo "<th align=CENTER >% Part.</th>";
			echo "<th align=CENTER >Valor %.Tercero</th>";
			echo "<th align=CENTER >Empresa Responsable</th>";
			echo "<th align=CENTER >Empleado</th>";
			echo "</tr>";
			for ($j=0;$j<$num;$j++)
			{

				if($j % 2 == 0)
					$wclass="fila1";
				else
					$wclass="fila2";

				$row = mysql_fetch_array($err);
				$wsegu = $row[12];
				if(($wconant <> $row[1]) or ($wterant <> $row[0]) or ($wccoant <> $row[11]) )
				{
					if($wconant <> "")
					{
						echo "<tr class=encabezadoTabla><th colspan=4 > TOTAL CONCEPTO ".$wconant." CCO ".$wccoant.": </th>";
						echo "<th colspan=1 > ".number_format($wtotcon,2,'.',',')."</th><th colspan=4 > </th></tr>";
						echo "<tr class=encabezadoTabla><th colspan=4 > TOTAL % PARTICIPACION: </th>";
						echo "<th colspan=3 > ".number_format($wtotvpo,2,'.',',')."</th><th colspan=2 > </th></tr>";

						$wtotcon = 0;
						$wtotvpo = 0;
					}
				}

				if($wterant <> $row[0])
				{
					if($wterant <> "")
					{
						echo "<tr class=encabezadoTabla><th colspan=4 > TOTAL TERCERO: </th>";
						echo "<th colspan=1 > ".number_format($wtotter,2,'.',',')."</th><th colspan=4 > </th></tr>";
						echo "<tr class=encabezadoTabla><th colspan=4 class=encabezadoTabla> TOTAL A PAGAR: </th>";
						echo "<th colspan=3 > ".number_format($wtotpag,2,'.',',')."</th><th colspan=2 > </th></tr>";

						$wtotter = 0;
						$wtotpag = 0;
					}
				}

				if($wterant <> $row[0])
				{
					$wterant=$row[0];
					$wconant = "";
					$wccoant='';

					$q1 = " SELECT mednom ".
					"   FROM ".$wbasedato."_000051 ".
					" 	WHERE meddoc = '".$row[0]."'";

					$err1 = mysql_query($q1,$conex);
					$num1 = mysql_num_rows($err1);
					$row1 = mysql_fetch_array($err1);

					echo "<tr><td><br></td></tr>";
					echo "<tr class=encabezadoTabla><th colspan=3 >".$row1[0]." C.C/NIT: ".$row[0]."</th><th colspan=6> </th></tr>";
				}
				if($wconant <> $row[1] or $wccoant<>$row[11])
				{
					$wconant=$row[1];
					$wccoant=$row[11];

					$q1 = " SELECT grudes ".
					"   FROM ".$tablaConceptos." ".
					" 	WHERE grucod = '".$row[1]."'";

					$err1 = mysql_query($q1,$conex);
					$num1 = mysql_num_rows($err1);
					$row1 = mysql_fetch_array($err1);

					echo "<tr class=encabezadoTabla	><th colspan=3 > CPTO: ".$row[1]." - ".$row1[0]." CCO ".$wccoant."</th> <th colspan=6> </th></tr>";
				}

				$q1 = " SELECT empnom ".
					  "   FROM ".$wbasedato."_000024 ".
					  "  WHERE empcod = '".$row[10]."'";

				$err1 = mysql_query($q1,$conex);
				$num1 = mysql_num_rows($err1);
				$row1 = mysql_fetch_array($err1);

				echo "<tr>";
				echo "<th align=center class=".$wclass.">".$row[2]."-".$row[3]."</th>";
				echo "<th align=center class=".$wclass.">".$row[4]."</th>";
				echo "<th align=center class=".$wclass.">".$row[5]."</th>";

				$q2 = " SELECT Descripcion ".
					  "   FROM usuarios ".
					  "  WHERE Codigo = '".$wsegu."' ";

				$err2 = mysql_query($q2,$conex);
				$row2 = mysql_fetch_array($err2);

				echo "<th align=center class=".$wclass.">".$row[6]."</th>";
				echo "<th align=center class=".$wclass.">".number_format($row[7],2,'.',',')."</th>";
				echo "<th align=center class=".$wclass.">".$row[8]."</th>";
				echo "<th align=center class=".$wclass.">".number_format($row[9],2,'.',',')."</th>";
				echo "<th align=center class=".$wclass.">".$row1[0]."</th>";
				echo "<th align=center class=".$wclass.">".$row2[0]."</th>";
				echo "</tr>";

				$wtotcon=$wtotcon+$row[7];
				$wtotvpo=$wtotvpo+$row[9];
				$wtotter=$wtotter+$row[7];
				$wtotpag=$wtotpag+$row[9];
				$wgtocon=$wgtocon+$row[7];
				$wgtoter=$wgtoter+$row[9];
			}
			echo "<tr class=encabezadoTabla><th colspan=4 > TOTAL CONCEPTO ".$wconant." CCO ".$wccoant.": </th>";
			echo "<th colspan=1 > ".number_format($wtotcon,2,'.',',')."</th><th colspan=4 > </th></tr>";
			echo "<tr class=encabezadoTabla><th colspan=4 > TOTAL % PARTICIPACION: </th>";
			echo "<th colspan=3 > ".number_format($wtotvpo,2,'.',',')."</th><th colspan=2 > </th></tr>";

			echo "<tr class=encabezadoTabla><th colspan=4 > TOTAL TERCERO : </th>";
			echo "<th colspan=1 > ".number_format($wtotter,2,'.',',')."</th><th colspan=4 > </th></tr>";
			echo "<tr class=encabezadoTabla><th colspan=4 > TOTAL A PAGAR: </th>";
			echo "<th colspan=3 > ".number_format($wtotpag,2,'.',',')."</th><th colspan=2 > </th></tr>";

			echo "<tr><td><br></td></tr>";
			echo "<tr class=encabezadoTabla><th colspan=4 > TOTAL GENERAL : </th>";
			echo "<th colspan=1 > ".number_format($wgtocon,2,'.',',')."</th><th colspan=4 > </th></tr>";
			echo "<tr class=encabezadoTabla><th colspan=4 > TOTAL GENERAL A PAGAR: </th>";
			echo "<th colspan=3> ".number_format($wgtoter,2,'.',',')."</th><th colspan=2 > </th></tr>";

		}
		echo "</table>";

		if ($wtotcon==0)
		{
			echo "<table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
			echo "<tr><td colspan='2' align='center'><font size=2 color='#000080' face='arial'><b>No se encontraron Facturas con los Parametros Seleccionados...</td><tr>";
		}
		else
		{
			echo "<table align=center border=0 width=90%>";
		}
		echo "</table>";
		echo "</br><center><A href='RepFacTer.php?wemp_pmla=".$wemp_pmla."&amp;wfecini=".$wfecini."&amp;wfecfin=".$wfecfin."&amp;wtipres=".$wtipres."&amp;wter=".$wter1[0]."&amp;wcon=".$wcon1[0]."&amp;wbasedato=".$wbd."&amp;bandera='1'>VOLVER</A></center>";
		echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";

	}
}
liberarConexionBD($conex);
?>
</body>
</html>
