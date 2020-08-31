<head>
  <title>REPORTE DE NOTAS POR TERCEROS</title>
</head>

   <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}

    	.titulo1{color:#FFFFFF;background:#006699;font-size:18pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.titulo2{color:#003366;background:#A4E1E8;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.titulo3{color:#003366;background:#57C8D5;font-size:12pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	.titulo4{color:#003366;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.texto1{color:#003366;background:#FFFFFF;font-size:7pt;font-family:Tahoma;text-align:center;}
    	.texto2{color:#003366;background:#FFFFFF;font-size:7pt;font-family:Tahoma;}
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
	document.forma.submit();
}
function enter()
{
	document.forms.RepNotTer.submit();
}
</script>

<body>

<?php
include_once("conex.php");

/*========================================================DOCUMENTACION PROGRAMA================================================================================*/
/*

1. AREA DE VERSIONAMIENTO

Nombre del programa: Reporte de notas por tercero
Fecha de creacion: 2007-05-07
Autor: Carolina Castano

Actualizaciones:
//-------------------------------------------------------------------------------------------------------------------------------------------
//	-->	2013-12-24, Jerson trujillo.
//		El maestro de conceptos que actualmente es la tabla 000004, para cuando entre en funcionamiento la nueva facturacion del proyecto
//		del ERP, esta tabla cambiara por la 000200; Entonces se adapta el programa para que depediendo del paramatro de root_000051
//		'NuevaFacturacionActiva' realice este cambio automaticamente.
//-------------------------------------------------------------------------------------------------------------------------------------------


2. AREA DE DESCRIPCION:

Este reporte permite listar las notas contenidas en un rango de fechas detallada por cpto y tercero, el cual
relaciona su respectivo porcentaje de participacion.

3. AREA DE VARIABLES DE TRABAJO

4. AREA DE TABLAS

$wbasedato."_000018: select de Movimiento de Facturas
$wbasedato."_000065: select de Detalle de Facturas por Cpto.
*/


function hora_i()
  {
   echo "Hora Inicio: ".(string)date("H:i:s")."<br>";
  }

function hora_f($q)
  {
   echo $q."<br>";
   echo "Hora Final: ".(string)date("H:i:s")."<br><br>";
  }

//=================================================================================================================================
include_once("root/comun.php");
session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	if(!isset($wemp_pmla)){
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
	}

	$conex = obtenerConexionBD("matrix");

	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

	$wbasedato = strtolower($institucion->baseDeDatos);
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

	echo "<form action='RepNotTer.php' method=post name='forma'>";

	$wfecha=date("Y-m-d");
	$wbd=$wbasedato;

	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";

	//si estas variables estan setiadas ya se ha enviado al formulario, si no es la primera entrada, entonces pinto
	//formulario incial
	if (!isset($wfecini) or !isset($wfecfin) or !isset($wcon)or !isset ($resultado))
	{
		echo "<center><table border=0 width=80%>";
		echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=350 HEIGHT=100></td></tr>";
		echo "<tr><td class='titulo1'>REPORTE DE NOTAS POR TERCERO</td></tr>";

		//INGRESO DE VARIABLES PARA EL REPORTE//
		if (!isset ($bandera))
		{
			$wfecini=$wfecha;
			$wfecfin=$wfecha;
		}

		echo "<tr>";
		$cal="calendario('wfecini','1')";
		echo "<td align=center class='texto3'><b>FECHA INICIAL: </font></b>";
		campoFecha("wfecini");
		echo "</td>";

		echo "<td align=center class='texto3'><b>FECHA FINAL: </font></b>";
		campoFecha("wfecfin");
		echo "</td>";

     	echo "</tr>";

			  echo "<tr>";
			  echo "<td align=left class='texto3' >TIPO DE RESPONSABLE:
			  <select name='wtipres' onchange='enter()' ondblclick='enter()'>";
			  if (isset ($wtipres))
			  {
			  	if ($wtipres=='EMPRESA')
			  	{
			  		echo "<option>EMPRESA</option>";
			  		echo "<option>PARTICULAR</option>";
			  		echo "<option>AMBOS</option>";
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
			  	echo "<option>AMBOS</option>";
			  	echo "<option>EMPRESA</option>";
			  	echo "<option>PARTICULAR</option>";
			  }
			  echo "</select></td>";

			  echo "<td align=left class='texto3' >TERCERO:
			  <select name='wter' onchange='enter()' ondblclick='enter()'>";
			  if (!isset($wter))
			  {
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
			  		echo "<option>".$row[0]."-".$row[1]."</option>";
			  	}
			  	echo "</select>";
			  }
			  else
			  {
			  	echo "<option>".$wter."</option>";

			  	$query= "SELECT meddoc, mednom ".
			  	"  FROM ".$wbasedato."_000051 ".
			  	" WHERE medest = 'on' ".
			  	"   AND medcod !=  (mid('".$wter."',1,instr('".$wter."','-')-1)) ".
			  	" ORDER BY mednom";

			  	$err = mysql_query($query,$conex);
			  	$num = mysql_num_rows($err);

			  	for ($i=1;$i<=$num;$i++)
			  	{
			  		$row = mysql_fetch_array($err);
			  		echo "<option>".$row[0]."-".$row[1]."</option>";
			  	}
			  }
			  echo "</select></td>";
			  echo "</tr>";

			  echo "<tr>";
			  echo "<td align=left colspan=2 class='texto3' >CONCEPTO:
			  <select name='wcon' onchange='enter()' ondblclick='enter()'>";
			  if (!isset($wcon))
			  {
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
			  		echo "<option>".$row[0]."-".$row[1]."</option>";
			  	}
			  	echo "</select>";
			  }
			  else
			  {
			  	echo "<option>".$wcon."</option>";

			  	$query= "SELECT grucod, grudes ".
			  	"  FROM ".$tablaConceptos." ".
			  	" WHERE gruser in ('A','H') ".
			  	"   AND gruest = 'on' ".
			  	"   AND grucod !=  (mid('".$wcon."',1,instr('".$wcon."','-')-1)) ".
			  	" ORDER BY grudes";

			  	$err = mysql_query($query,$conex);
			  	$num = mysql_num_rows($err);

			  	for ($i=1;$i<=$num;$i++)
			  	{
			  		$row = mysql_fetch_array($err);
			  		echo "<option>".$row[0]."-".$row[1]."</option>";
			  	}
			  }
			  echo "</select></td>";
			  echo "</tr>";

			  echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";
			  echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";
			  echo "<input type='HIDDEN' NAME= 'resultado' value='1'>";

			  echo "<tr><td align=center class='texto3' COLSPAN='2'>";
			  echo "<input type='radio' name='vol' value='SI' onclick='Seleccionar()' checked> GENERAR REPORTE";
			  echo "</td></tr>";
			  echo "</table>";
			  echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
}
//MUESTRA DE DATOS DEL REPORTE
else
{
	if((!isset($wtipres) or ($wtipres== "")) and (isset($vol)))
	{
		?>
		<script>
		alert ("Debe Ingresar el Tipo de Responsable");
			</script>
			<?php
			$valido=0;

			echo "<table  align=center width='80%'>";
			echo "<tr><td>&nbsp;</td></tr>";
			echo "<tr><td align=CENTER><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=340 HEIGHT=100></td></tr>";
			echo "<tr><td>&nbsp;</td></tr>";
			echo "<tr><td><font size=2><B>Fecha: ".date('Y-m-d')."</B></font></td></tr>";
			echo "<tr><td><font size=2><B>REPORTE DE NOTAS POR TERCERO</B></font></td></tr>";

			$wtipres="EMPRESA";
			echo "</tr><td align=right><A href='RepNotTer.php?wemp_pmla=".$wemp_pmla."&amp;wfecini=".$wfecini."&amp;wfecfin=".$wfecfin."&amp;wtipres=".$wtipres."&amp;wter=".$wter."&amp;wcon=".$wcon."&amp;wbasedato=".$wbd."&amp;bandera='1'>VOLVER</A></td></tr>";
			echo "<tr align='center'><td><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></td></tr>";
			echo "</table>";
		}
		else
		{
			echo "<table  align=center width='80%'>";
			echo "<tr><td>&nbsp;</td></tr>";
			echo "<tr><td align=CENTER><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=340 HEIGHT=100></td></tr>";
			echo "<tr><td>&nbsp;</td></tr>";
			echo "<tr><td><font size=2><B>Fecha: ".date('Y-m-d')."</B></font></td></tr>";
			echo "<tr><td><font size=2><B>REPORTE DE NOTAS POR TERCERO</B></font></td></tr>";

			echo "</tr><td align=right><A href='RepNotTer.php?wemp_pmla=".$wemp_pmla."&amp;wfecini=".$wfecini."&amp;wfecfin=".$wfecfin."&amp;wtipres=".$wtipres."&amp;wter=".$wter."&amp;wcon=".$wcon."&amp;wbasedato=".$wbd."&amp;bandera='1'>VOLVER</A></td></tr>";
			echo "<tr align='center'><td><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></td></tr>";
			echo "<tr><td><font size=2>Fecha inicial: ".$wfecini."</font></td></tr>";
			echo "<tr><td><font size=2>Fecha final: ".$wfecfin."</font></td></tr>";
			echo "<tr><td><font size=2>Responsable: ".$wtipres."</font></td></tr>";
			echo "<tr><td><font size=2>Tercero: ".$wter."</font></td></tr>";
			echo "<tr><td><font size=2>Concepto: ".$wcon."</font></td></tr>";
			echo "</table></br>";

			echo "<input type='HIDDEN' NAME= 'wfecini' value='".$wfecini."'>";
			echo "<input type='HIDDEN' NAME= 'wfecfin' value='".$wfecfin."'>";
			echo "<input type='HIDDEN' NAME= 'wtipres' value='".$wtipres."'>";
			echo "<input type='HIDDEN' NAME= 'wter' value='".$wter."'>";
			echo "<input type='HIDDEN' NAME= 'wcon' value='".$wcon."'>";
			echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";

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

			//traigo todas las notas generadas en el rango de fechas, las cuales sean por conceptos de facturacion agrupadas por
			//grudes


			//On hora_i();

			if($wcres == "P")
			{
				//consulta de notas por conceptos de facturacion

			$q = " SELECT renfec, rencod, rdefue, rdenum, rdecon, fdeter, fdecon, fdecco, fdevco, rdefac, rdeffa, rdevco ".
				"   FROM  ".$wbasedato."_000020, ".$wbasedato."_000021, ".$wbasedato."_000065, ".$tablaConceptos." ".
				" 	WHERE renfec between '".$wfecini."'".
				"    AND '".$wfecfin."'".
				"    AND rencod = '01' ".
				"    AND renfue = rdefue ".
				"    AND rennum = rdenum ".
				"    AND rdeest = 'on' ".
				"    AND rdecon = '' ".
				"    AND rdevco = 0 ".
				"    AND rdevca = 0 ".
				"    AND renfue = fdefue ".
				"    AND rennum = fdedoc ".
				"    AND fdecon like '".$wccon."' ".
				"    AND fdeter like '".$wcter."' ".
				"    AND fdecon = grucod ".
				"    AND grutip = 'C' ".

				" UNION".

				"  SELECT renfec, rencod, rdefue, rdenum, rdecon, fdeter, fdecon, fdecco, fdevco, rdefac, rdeffa, rdevco ".
				"   FROM  ".$wbasedato."_000020, ".$wbasedato."_000021, ".$wbasedato."_000065, ".$tablaConceptos.", ".$wbasedato."_000040 ".
				" 	WHERE renfec between '".$wfecini."'".
				"    AND '".$wfecfin."'".
				"    AND rencod = '01' ".
				"    AND renfue = carfue ".
				"    AND (carncr = 'on' ".
				"     OR  carndb = 'on') ".
				"    AND carest='on' ".
				"    AND renfue = rdefue ".
				"    AND rennum = rdenum ".
				"    AND rdecon <> '' ".
				"    AND rdeest = 'on' ".
				"    AND fdefue=rdeffa ".
				"    AND fdedoc=rdefac ".
				"    AND fdedoc=rdefac ".
				"    AND fdecon like '".$wccon."' ".
				"    AND fdeter like '".$wcter."' ".
				"    AND fdecon = grucod ".
				"    AND grutip = 'C' ".
				"  ORDER BY  6,7, 8, 3,4  ";

			}
			else
			{
				if($wcres == "E")
				{

					$q = " SELECT renfec, rencod, rdefue, rdenum, rdecon, fdeter, fdecon, fdecco, fdevco, rdefac, rdeffa, rdevco ".
					"   FROM  ".$wbasedato."_000020, ".$wbasedato."_000021, ".$wbasedato."_000065, ".$tablaConceptos." ".
					" 	WHERE renfec between '".$wfecini."'".
					"    AND '".$wfecfin."'".
					"    AND rencod <> '01' ".
					"    AND renfue = rdefue ".
					"    AND rennum = rdenum ".
					"    AND rdeest = 'on' ".
					"    AND rdecon = '' ".
					"    AND rdevco = 0 ".
					"    AND rdevca = 0 ".
					"    AND renfue = fdefue ".
					"    AND rennum = fdedoc ".
					"    AND fdecon like '".$wccon."' ".
					"    AND fdeter like '".$wcter."' ".
					"    AND fdecon = grucod ".
				    "    AND grutip = 'C' ".

					" UNION".

					"  SELECT renfec, rencod, rdefue, rdenum, rdecon, fdeter, fdecon, fdecco, fdevco, rdefac, rdeffa, rdevco ".
					"   FROM  ".$wbasedato."_000020, ".$wbasedato."_000021, ".$wbasedato."_000065, ".$tablaConceptos.", ".$wbasedato."_000040 ".
					" 	WHERE renfec between '".$wfecini."'".
					"    AND '".$wfecfin."'".
					"    AND rencod <> '01' ".
					"    AND renfue = carfue ".
				    "    AND (carncr = 'on' ".
				    "     OR  carndb = 'on') ".
				    "    AND carest='on' ".
					"    AND renfue = rdefue ".
					"    AND rennum = rdenum ".
					"    AND rdecon <> '' ".
					"    AND rdeest = 'on' ".
					"    AND fdefue=rdeffa ".
					"    AND fdedoc=rdefac ".
					"    AND fdedoc=rdefac ".
					"    AND fdecon like '".$wccon."' ".
					"    AND fdeter like '".$wcter."' ".
					"    AND fdecon = grucod ".
				    "    AND grutip = 'C' ".
					"  ORDER BY  6,7, 8, 3,4  ";

				}
				else
				{

					$q = " SELECT renfec, rencod, rdefue, rdenum, rdecon, fdeter, fdecon, fdecco, fdevco, rdefac, rdeffa, rdevco ".
					"   FROM  ".$wbasedato."_000020, ".$wbasedato."_000021, ".$wbasedato."_000065, ".$tablaConceptos." ".
					" 	WHERE renfec between '".$wfecini."'".
					"    AND '".$wfecfin."'".
					"    AND renfue = rdefue ".
					"    AND rennum = rdenum ".
					"    AND rdeest = 'on' ".
					"    AND rdecon = '' ".
					"    AND rdevco = 0 ".
					"    AND rdevca = 0 ".
					"    AND renfue = fdefue ".
					"    AND rennum = fdedoc ".
					"    AND fdecon like '".$wccon."' ".
					"    AND fdeter like '".$wcter."' ".
					"    AND fdecon = grucod ".
				    "    AND grutip = 'C' ".

					" UNION".

					"  SELECT renfec, rencod, rdefue, rdenum, rdecon, fdeter, fdecon, fdecco, fdevco, rdefac, rdeffa, rdevco ".
					"   FROM  ".$wbasedato."_000020, ".$wbasedato."_000021, ".$wbasedato."_000065, ".$tablaConceptos.", ".$wbasedato."_000040 ".
					" 	WHERE renfec between '".$wfecini."'".
					"    AND '".$wfecfin."'".
					"    AND renfue = carfue ".
					"    AND (carncr = 'on' ".
					"     OR  carndb = 'on') ".
					"    AND carest='on' ".
					"    AND renfue = rdefue ".
					"    AND rennum = rdenum ".
					"    AND rdecon <> '' ".
					"    AND rdeest = 'on' ".
					"    AND fdefue=rdeffa ".
					"    AND fdedoc=rdefac ".
					"    AND fdedoc=rdefac ".
					"    AND fdecon like '".$wccon."' ".
					"    AND fdeter like '".$wcter."' ".
					"    AND fdecon = grucod ".
				    "    AND grutip = 'C' ".
					"  ORDER BY  6,7, 8, 3,4  ";

				}
			}
			$err = mysql_query($q,$conex);
			$num = mysql_num_rows($err);
			//On
			//hora_f($q);

			// se crean las variables $wttocre, $wtotde, $wtotdet, $wtotcrt para acumular los totales de notas
			//credito y debito por concepto y por tercero
			$wterant = "";
			$wconant= "";
			$wccoant= "";
			$wtotcon = 0;
			$wtotdeb = 0;
			$wtotcre = 0;
			$wtotpcre = 0;
			$wtotpdeb = 0;
			$wtotvpo = 0;
			$wtotter = 0;
			$wtotdet = 0;
			$wtotcrt = 0;
			$wtotpcrt = 0;
			$wtotpdet = 0;
			$wtotpag = 0;
			$wgtocon = 0;
			$wgtoter = 0;
			$wtotdef=0;
			$wtotcrf=0;
			$wtotpdef=0;
			$wtotpcrf=0;

			if ($num > 0)
			{
                //se crean dos campos mas para el reporte, notas debito y notas credito
                echo "<table align=center border=0 width=90%>";
				echo "<th align=CENTER class='titulo2' width='5%'>Documento</th>";
				echo "<th align=CENTER class='titulo2' width='10%'>Fecha</th>";
				echo "<th align=CENTER class='titulo2' width='10%'>Factura</th>";
				echo "<th align=CENTER class='titulo2' width='10%'>Identif.Pac</th>";
				echo "<th align=CENTER class='titulo2' width='15%'>Nombre</th>";
				echo "<th align=CENTER class='titulo2' width='10%'>Notas debito</th>";
				echo "<th align=CENTER class='titulo2' width='10%'>Notas credito</th>";
				echo "<th align=CENTER class='titulo2' width='5%'>% Part.</th>";
				echo "<th align=CENTER class='titulo2' width='5%'>% Notas debito</th>";
				echo "<th align=CENTER class='titulo2' width='5%'>% Notas credito</th>";
				echo "<th align=CENTER class='titulo2' width='15%'>Responsable</th>";

				for ($j=0;$j<$num;$j++)
				{
					$row = mysql_fetch_array($err);

					if(($wconant <> $row[6]) or ($wterant <> $row[5]) or ($wccoant <> $row[7]))
					{
						if($wconant <> "" and ($wtotdeb>0 or $wtotcre>0))
						{
							//2007-05-02 se agregan los totales para notas debito y credito
							echo "<tr><th colspan=5 class='texto5'> TOTAL CONCEPTO ".$wconant."  CC0 ".$wccoant.": </th></tr>";
							echo "<tr><th colspan=5 class='texto5'> NOTAS DEBITO: </th>";
							echo "<th colspan=1 class='texto6'> ".number_format($wtotdeb,2,'.',',')."</th>";
							echo "<th colspan=3 class='texto6'> ".number_format($wtotpdeb,2,'.',',')."</th></tr>";
							echo "<tr><th colspan=5 class='texto5'>  NOTAS CREDITO: </th>";
							echo "<th colspan=2 class='texto6'> ".number_format($wtotcre,2,'.',',')."</th>";
							echo "<th colspan=3 class='texto6'> ".number_format($wtotpcre,2,'.',',')."</th></tr>";

							$wtotcon = 0;
							$wtotcre = 0;
							$wtotdeb = 0;
							$wtotvpo = 0;
							$wtotpcre = 0;
							$wtotpdeb = 0;
						}
					}

					if($wterant <> $row[5] and ($wtotdet>0 or $wtotcrt>0))
					{
						if($wterant <> "")
						{
							echo "<tr><th colspan=5 class='texto5'> TOTAL TERCERO: </th></tr>";
							echo "<tr><th colspan=5 class='texto5'>  NOTAS DEBITO: </th>";
							echo "<th colspan=1 class='texto6'> ".number_format($wtotdet,2,'.',',')."</th>";
							echo "<th colspan=3 class='texto6'> ".number_format($wtotpdet,2,'.',',')."</th></tr>";
							echo "<tr><th colspan=5 class='texto5'> NOTAS CREDITO: </th>";
							echo "<th colspan=2 class='texto6'> ".number_format($wtotcrt,2,'.',',')."</th>";
							echo "<th colspan=3 class='texto6'> ".number_format($wtotpcrt,2,'.',',')."</th></tr>";

							$wtotter = 0;
							$wtotcrt = 0;
							$wtotdet = 0;
							$wtotpag = 0;
							$wtotpcrt = 0;
							$wtotpdet = 0;
						}
					}

					if ($row[4]=='')
					{
						$pintar='on';
					}
					else
					{
						$q = " SELECT conacp "
							. "  FROM ".$wbasedato."_000044 "
							. " WHERE concod= mid('".$row[4]."',1,instr('".$row[4]."','-')-1) "
							. "   AND confue = '".$row[2]."' "
							. "   AND conest = 'on' ";

						$erracp= mysql_query($q,$conex);

						$numacp = mysql_num_rows($erracp);
						$rowacp = mysql_fetch_array($erracp);
						if ($rowacp[0]=='on')
						{
							$pintar='on';
						}
						else
						{
							$pintar='off';
						}

					}

					if($wterant <> $row[5] and $pintar=='on')
					{
						$wterant=$row[5];
						$wconant = "";
						$wccoant = "";

						$q1 = " SELECT mednom ".
							  "   FROM ".$wbasedato."_000051 ".
							  "  WHERE meddoc = '".$row[5]."'";

						$err1 = mysql_query($q1,$conex);
						$num1 = mysql_num_rows($err1);
						$row1 = mysql_fetch_array($err1);

						echo "<tr><td><br></td></tr>";
						echo "<tr><th colspan=5 class='texto4'>".$row1[0]." C.C/NIT: ".$row[5]."</th></tr>";
					}

					if(($wconant <> $row[6] or $wccoant <> $row[7])and $pintar=='on')
					{
						$wconant=$row[6];
						$wccoant=$row[7];

						$q1 = " SELECT grudes ".
							  "   FROM ".$tablaConceptos." ".
							  "  WHERE grucod = '".$row[6]."'";

						$err1 = mysql_query($q1,$conex);
						$num1 = mysql_num_rows($err1);
						$row1 = mysql_fetch_array($err1);

						echo "<tr><th colspan=5 class='texto4'> CPTO: ".$row[6]." - ".$row1[0]." CCO: ".$row[7]."</th></tr>";
					}

					$q1 = " SELECT empnom ".
						  "   FROM ".$wbasedato."_000024 ".
						  "  WHERE empcod = '".$row[1]."'";

					$err1 = mysql_query($q1,$conex);
					$num1 = mysql_num_rows($err1);
					$row1 = mysql_fetch_array($err1);

					//se busca el valor de las notas debito y credito a ese cargo
					//consulto las fuentes para notas debito

					$q= "  SELECT carndb "
					   ."    FROM ".$wbasedato."_000040 "
					   ."   WHERE carfue = '".$row[2]."'" ;
					$errdeb = mysql_query($q,$conex);
					$numdeb = mysql_num_rows($errdeb);
					$rowdeb = mysql_fetch_array($errdeb);

					//consulto la factura para la nota en cuestion
					$q = " SELECT fendpa, fennpa, (fenval+fenviv+fencop+fencmo+fendes+fenabo) "
						."   FROM ".$wbasedato."_000018 "
						."  WHERE fenfac = '".$row[9]."' "
						."    AND fenffa = '".$row[10]."' ";

					$errfac = mysql_query($q,$conex);
					$numfac = mysql_num_rows($errfac);
					$rowfac = mysql_fetch_array($errfac);

					//consulto la participacion del medico y el concepto en la factura
					$q = " SELECT fdepte "
						."  FROM ".$wbasedato."_000065 "
						." WHERE fdedoc= '".$row[9]."' "
						."   AND fdefue = '".$row[10]."' "
						."   AND fdecon = '".$row[6]."' "
						."   AND fdeter = '".$row[5]."' "
						."   AND fdecco = '".$row[7]."' ";
					$errpar= mysql_query($q,$conex);
					$numpar = mysql_num_rows($errpar);
					$rowpar = mysql_fetch_array($errpar);

					if ($row[4]=='')
					{
						echo "<tr>";
						echo "<th align=left class='texto1'>".$row[2]."-".$row[3]."</th>";
						echo "<th align=left class='texto1'>".$row[0]."</th>";
						echo "<th align=left class='texto1'>".$row[10]."-".$row[9]."</th>";
						echo "<th align=left class='texto1'>".$rowfac[0]."</th>";
						echo "<th align=left class='texto1'>".$rowfac[1]."</th>";

						if ($rowdeb[0]=='on')
						{
							echo "<th align=right class='texto2'>".number_format($row[8],2,'.',',')."</th>";
							echo "<th align=right class='texto2'>&nbsp;</th>";
							$wtotdeb=$wtotdeb+$row[8];
							$wtotdet=$wtotdet+$row[8];
							$wtotdef=$wtotdef+$row[8];
							$wtotpdeb=$wtotpdeb+$row[8]*$rowpar[0]/100;
							$wtotpdet=$wtotpdet+$row[8]*$rowpar[0]/100;
							$wtotpdef=$wtotpdef+$row[8]*$rowpar[0]/100;
						}
						else
						{
							echo "<th align=right class='texto2'>&nbsp;</th>";
							echo "<th align=right class='texto2'>".number_format($row[8],2,'.',',')."</th>";
							$wtotcre=$wtotcre+$row[8];
							$wtotcrt=$wtotcrt+$row[8];
							$wtotcrf=$wtotcrf+$row[8];
							$wtotpcre=$wtotpcre+$row[8]*$rowpar[0]/100;
							$wtotpcrt=$wtotpcrt+$row[8]*$rowpar[0]/100;
							$wtotpcrf=$wtotpcrf+$row[8]*$rowpar[0]/100;
						}
						echo "<th align=center class='texto2'>".$rowpar[0]."</th>";
						if ($rowdeb[0]=='on')
						{
							echo "<th align=right class='texto2'>".number_format($row[8]*$rowpar[0]/100,2,'.',',')."</th>";
							echo "<th align=right class='texto2'>&nbsp;</th>";
						}
						else
						{
							echo "<th align=right class='texto2'>&nbsp;</th>";
							echo "<th align=right class='texto2'>".number_format($row[8]*$rowpar[0]/100,2,'.',',')."</th>";
						}
						echo "<th align=center class='texto2'>".$row[1]."-".$row1[0]."</th>";
						echo "</tr>";
					}
					else
					{

						//consulto si esa nota debe tenerse en cuenta

						if ($rowacp[0]=='on')
						{
							//consulto todas las notas con conceptos de facturacion para la factura
							$q = " SELECT sum(renvca) "
								."   FROM ".$wbasedato."_000021, ".$wbasedato."_000020, ".$wbasedato."_000040 "
								."  WHERE rdeffa = '".$row[10]."' "
								."    AND rdefac = '".$row[9]."' "
								."    AND rdeest = 'on' "
								."    AND rdecon = '' "
								."    AND rdevco = '0' "
								."    AND renfec BETWEEN '".$wfecini."' AND '".$wfecfin."' "
								."    AND renfue = rdefue "
								."    AND rennum = rdenum "
								."    AND renest = 'on' "
								."    AND carfue = rdefue  "
								."    AND carest = 'on' "
								."    AND carncr = 'on' "
								."  GROUP BY rdeffa, rdefac ";
							$resval = mysql_query($q,$conex);
							$rowval = mysql_fetch_row($resval);


							$q = " SELECT sum(renvca) "
								."   FROM ".$wbasedato."_000021, ".$wbasedato."_000020, ".$wbasedato."_000040 "
								."  WHERE rdeffa = '".$row[10]."' "
								."    AND rdefac = '".$row[9]."' "
								."    AND rdeest = 'on' "
								."    AND rdecon = '' "
								."    AND rdevco = '0' "
								."    AND renfue = rdefue "
								."    AND rennum = rdenum "
								."    AND renfec BETWEEN '".$wfecini."' AND '".$wfecfin."' "
								."    AND renest = 'on' "
								."    AND carfue = rdefue  "
								."    AND carest = 'on' "
								."    AND carndb = 'on' "
								."  GROUP BY rdeffa, rdefac ";
							$resval1 = mysql_query($q,$conex) ;
							$rowval1 = mysql_fetch_row($resval1);

							//consulto las notas DEBITO por concepto de facturación y voy sumando
							$q = " SELECT sum(fdevco) "
							    ."   FROM ".$wbasedato."_000021, ".$wbasedato."_000065 "
							    ."  WHERE rdeffa = '".$row[10]."' "
							    ."    AND rdefac = '".$row[9]."' "
							    ."    AND rdefue IN (SELECT carfue FROM ".$wbasedato."_000040 WHERE carfue = rdefue AND carndb='on') "
							    ."    AND rdeest = 'on' "
							    ."    AND rdecon = '' "
							    ."    AND rdevco = '0' "
							    ."    AND fdefue = rdefue "
							    ."    AND fdedoc = rdenum "
							    ."    AND fdecon = '".$row[6]."' "
							    ."    AND fdeter = '".$row[5]."' "
							    ."    AND fdeest = 'on' "
							    ."    AND fdecco = '".$row[7]."' ";
							$rescre = mysql_query($q,$conex) ;
							$row2cre = mysql_fetch_row($rescre);
							$wvaldeb=$row2cre[0];

							//consulto las notas CREDITO por concepto de facturación y voy sumando
							$q = " SELECT sum(fdevco) "
							    ."   FROM ".$wbasedato."_000021, ".$wbasedato."_000065 "
							    ."  WHERE rdeffa = '".$row[10]."' "
							    ."    AND rdefac = '".$row[9]."' "
							    ."    AND rdefue IN (SELECT carfue FROM ".$wbasedato."_000040 WHERE carfue = rdefue AND carncr='on') "
							    ."    AND rdeest ='on' "
							    ."    AND rdecon ='' "
							    ."    AND rdevco ='0' "
							    ."    AND fdefue = rdefue "
							    ."    AND fdedoc = rdenum "
							    ."    AND fdecon = '".$row[6]."' "
							    ."    AND fdeter = '".$row[5]."' "
							    ."    AND fdeest ='on' "
							    ."    AND fdecco = '".$row[7]."' ";
							$rescre = mysql_query($q,$conex) ;
							$row2cre = mysql_fetch_row($rescre);
							$wvalcre=$row2cre[0];

							$valfac=$rowfac[2]+$rowval1[0]-$rowval[0];
							$valor=$row[8]+$wvaldeb-$wvalcre;
							if ($valfac!=0)
							{
								$porcen=$valor*100/$valfac;
							}
							else
							{
								$porcen=0;
							}

							echo "<tr>";
							echo "<th align=left class='texto1'>".$row[2]."-".$row[3]."</th>";
							echo "<th align=left class='texto1'>".$row[0]."</th>";
							echo "<th align=left class='texto1'>".$row[10]."-".$row[9]."</th>";
							echo "<th align=left class='texto1'>".$rowfac[0]."</th>";
							echo "<th align=left class='texto1'>".$rowfac[1]."</th>";

							if ($rowdeb[0]=='on')
							{
								echo "<th align=right class='texto2'>".number_format(($row[11]*$porcen/100),2,'.',',')."</th>";
								echo "<th align=right class='texto2'>&nbsp;</th>";
								$wtotdeb=$wtotdeb+($row[11]*$porcen/100);
								$wtotdet=$wtotdet+($row[11]*$porcen/100);
								$wtotdef=$wtotdef+($row[11]*$porcen/100);
								$wtotpdeb=$wtotpdeb+($row[11]*$porcen*$rowpar[0]/10000);
								$wtotpdet=$wtotpdet+($row[11]*$porcen*$rowpar[0]/10000);
								$wtotpdef=$wtotpdef+($row[11]*$porcen*$rowpar[0]/10000);
							}
							else
							{
								echo "<th align=right class='texto2'>&nbsp;</th>";
								echo "<th align=right class='texto2'>".number_format(($row[11]*$porcen/100),2,'.',',')."</th>";
								$wtotcrt=$wtotcrt+($row[11]*$porcen/100);
								$wtotcrf=$wtotcrf+($row[11]*$porcen/100);
								$wtotcre=$wtotcre+($row[11]*$porcen/100);
								$wtotpcrt=$wtotpcrt+($row[11]*$porcen*$rowpar[0]/10000);
								$wtotpcrf=$wtotpcrf+($row[11]*$porcen*$rowpar[0]/10000);
								$wtotpcre=$wtotpcre+($row[11]*$porcen*$rowpar[0]/10000);
							}
							echo "<th align=center class='texto2'>".$rowpar[0]."</th>";
							if ($rowdeb[0]=='on')
							{
								echo "<th align=right class='texto2'>".number_format(($row[11]*$porcen*$rowpar[0]/10000),2,'.',',')."</th>";
								echo "<th align=right class='texto2'>&nbsp;</th>";
							}
							else
							{
								echo "<th align=right class='texto2'>&nbsp;</th>";
								echo "<th align=right class='texto2'>".number_format(($row[11]*$porcen*$rowpar[0]/10000),2,'.',',')."</th>";
							}
							echo "<th align=center class='texto2'>".$row[1]."-".$row1[0]."</th>";
							echo "</tr>";
						}
					}
				}

				echo "<tr><th colspan=5 class='texto5'> TOTAL CONCEPTO ".$wconant." CCO ".$wccoant.": </th>";
				echo "<tr><th colspan=5 class='texto5'> NOTA CREDITO: </th>";
				echo "<th colspan=1 class='texto6'> ".number_format($wtotdeb,2,'.',',')."</th>";
				echo "<th colspan=3 class='texto6'> ".number_format($wtotpdeb,2,'.',',')."</th></tr>";
				echo "<tr><th colspan=5 class='texto5'> NOTA CREDITO: </th>";
				echo "<th colspan=2 class='texto6'> ".number_format($wtotcre,2,'.',',')."</th>";
				echo "<th colspan=3 class='texto6'> ".number_format($wtotpcre,2,'.',',')."</th></tr>";


				echo "<tr><th colspan=5 class='texto5'> TOTAL TERCERO : </th>";
				echo "<th colspan=1 class='texto6'> &nbsp;</th></tr>";
				echo "<tr><th colspan=5 class='texto5'> NOTA DEBITO : </th>";
				echo "<th colspan=1 class='texto6'> ".number_format($wtotdet,2,'.',',')."</th>";
				echo "<th colspan=3 class='texto6'> ".number_format($wtotpdet,2,'.',',')."</th></tr>";
				echo "<tr><th colspan=5 class='texto5'> NOTA CREDITO : </th>";
				echo "<th colspan=2 class='texto6'> ".number_format($wtotcrt,2,'.',',')."</th>";
				echo "<th colspan=3 class='texto6'> ".number_format($wtotpcrt,2,'.',',')."</th></tr>";

				echo "<tr><td><br></td></tr>";
				echo "<tr><th colspan=5 class='texto5'> TOTAL GENERAL : </th>";
				echo "<tr><th colspan=5 class='texto5'> TOTAL GENERAL NOTA DEBITO : </th>";
				echo "<th colspan=1 class='texto6'> ".number_format($wtotdef,2,'.',',')."</th>";
				echo "<th colspan=3 class='texto6'> ".number_format($wtotpdef,2,'.',',')."</th></tr>";
				echo "<tr><th colspan=5 class='texto5'> TOTAL GENERAL NOTA CREDITO: </th>";
				echo "<th colspan=2 class='texto6'> ".number_format($wtotcrf,2,'.',',')."</th>";
				echo "<th colspan=3 class='texto6'> ".number_format($wtotpcrf,2,'.',',')."</th></tr>";


			}
			echo "</table>";

			if ($wtotdeb==0 and $wtotcre==0)
			{
				echo "<table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
				echo "<tr><td colspan='2' align='center'><font size=2 color='#000080' face='arial'><b>No se encontraron Facturas con los Parametros Seleccionados...</td><tr>";
			}
			else
			{
				echo "<table align=center border=0 width=90%>";
			}
			echo "</table>";
			echo "</br><center><A href='RepNotTer.php?wemp_pmla=".$wemp_pmla."&amp;wfecini=".$wfecini."&amp;wfecfin=".$wfecfin."&amp;wtipres=".$wtipres."&amp;wter=".$wter."&amp;wcon=".$wcon."&amp;wbasedato=".$wbd."&amp;bandera='1'>VOLVER</A></center>";
			echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
		}

	}
}
liberarConexionBD($conex);
?>
</body>
</html>
