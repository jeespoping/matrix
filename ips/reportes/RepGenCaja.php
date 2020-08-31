<html>
<head>
  <title>REPORTE GENERAL DE CAJA</title>

   <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}

    	.titulo1{color:#FFFFFF;background:#006699;font-size:20pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.titulo2{color:#003366;background:#A4E1E8;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.titulo3{color:#003366;background:#57C8D5;font-size:12pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	.titulo4{color:#003366;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.texto1{color:#003366;background:#cccccc;font-size:7pt;font-family:Tahoma;text-align:center;}
    	.texto2{color:#006699;background:#f5f5dc;font-size:9pt;font-family:Tahoma;text-align:center;}
    	.texto3{color:#006699;background:#A4E1E8;font-size:9pt;font-weight:bold;font-family:Tahoma;text-align:center;}
    	.texto4{color:#003366;background:#999999;font-size:7pt;font-family:Tahoma;text-align:center;}
    	.texto5{color:#006699;background:#006699;font-size:9pt;font-family:Tahoma;text-align:center;}
    	.acumulado1{color:#003366;background:#FFCC66;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:right;}
    	.acumulado2{color:#003366;background:#FFCC66;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.acumulado3{color:#003366;background:#FFDBA8;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.acumulado4{color:#003366;background:#FFDBA8;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:right;}
    	.error1{color:#FF0000;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
   </style>

<SCRIPT LANGUAGE="JavaScript1.2">
function Seleccionar()
{
	document.forma.submit();
}

</script>

</head>
<body>
<?php
include_once("conex.php");
include_once("root/comun.php");
/**
 * NOMBRE:  REPORTE GENERAL DE CAJA
 *
 * PROGRAMA: RepGenCaja.php
 * TIPO DE SCRIPT: PRINCIPAL
 * //DESCRIPCION: Este reporte presenta un detalle consolidado de formas de recibo que se encuentran pendientes por egresar  con
 *                respectiva caja y usuario.
 *
 * HISTORIAL DE ACTAULIZACIONES:
 * 2006-11-15 Juan David Jaramillo, creacion del script
 *
 *
 * Tablas que utiliza:
 * $wbasedato."_000024: Maestro de Fuentes, select
 * $wbasedato."_000018: select de facturas entre dos fechas
 * $wbasedato."_000020: select en encabezado de cartera
 * $wbasedato."_000021: select en detalle de cartera
 *
 * @author jjaramillo
 * @package defaultPackage
 */

$wautor="Juan David Jaramillo R";

//=================================================================================================================================

session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	if(!isset($wemp_pmla)){
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
	}
	
	$key = substr($user,2,strlen($user));

	/**
	 * include de conexión a base de datos
	 *
	 */
	$conex = obtenerConexionBD("matrix");
	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
	
	$wbasedato = strtolower($institucion->baseDeDatos);
	
	echo "<form action='RepGenCaja.php' method=post name='forma'>";

	$wfecha=date("Y-m-d");

	echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";
	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";

	if (!isset($wfecini) or !isset($wfecfin) or !isset ($resultado))
	{
		echo "<center><table border=1>";
		echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=350 HEIGHT=100></td></tr>";
		echo "<tr><td class='titulo1'>REPORTE GENERAL DE CAJA</td></tr>";


		//INGRESO DE VARIABLES PARA EL REPORTE//
		if (!isset ($bandera))
		{
			$wfecini=$wfecha;
			$wfecfin=$wfecha;
		}

		echo "<tr>";
		echo "<td align=center class='texto3'><b>FECHA INICIAL DE RECIBOS: </font></b>";
		campoFecha("wfecini");
		echo "</td>";

		echo "<td  align=center class='texto3'><b>FECHA FINAL DE RECIBOS: </font></b>";
		campoFecha("wfecfin");
		echo "</td>";

		echo "</tr>";

		//SELECCIONAR estado a consultar
		echo "<tr>";
		echo "<td align=left colspan=2 class='texto3' >ESTADOS DEL RECIBO: ";
		echo "<select name='west'>";
			if (isset ($west))
			{
				if ($west=='SIN CUADRAR')
				{
					echo "<option>SIN CUADRAR</option>";
					echo "<option>PENDIENTE</option>";
					echo "<option>TODOS</option>";
				}
				if ($west=='PENDIENTE')
				{
					echo "<option>PENDIENTE</option>";
					echo "<option>SIN CUADRAR</option>";
					echo "<option>TODOS</option>";
				}
				if ($west=='TODOS')
				{
					echo "<option>TODOS</option>";
					echo "<option>PENDIENTE</option>";
					echo "<option>SIN CUADRAR</option>";
				}
			}
			else
			{
				echo "<option>SIN CUADRAR</option>";
				echo "<option>PENDIENTE</option>";
				echo "<option>TODOS</option>";
			}
		echo "</select></td></tr>";

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
		echo "<table  align=center width='60%'>";
		echo "<tr><td>&nbsp;</td></tr>";
		echo "<tr><td align=CENTER><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=340 HEIGHT=100></td></tr>";
		echo "<tr><td>&nbsp;</td></tr>";
		echo "<tr><td><B>Fecha: ".date('Y-m-d')."</B></td></tr>";
		echo "<tr><td><B>GENERAR REPORTE GENERAL DE CAJA</B></td></tr>";

		echo "</tr><td align=right><A href='RepGenCaja.php?wemp_pmla=".$wemp_pmla."&amp;wfecini=".$wfecini."&amp;wfecfin=".$wfecfin."&amp;west=".$west."&amp;bandera='1'>VOLVER</A></td></tr>";
		echo "<tr><td><tr><td>Fecha inicial: ".$wfecini."</td></tr>";
		echo "<tr><td>Fecha final: ".$wfecfin."</td></tr>";
		echo "<tr><td>Estado: ".$west."</td></tr>";
		echo "</table></br>";

		echo "<input type='HIDDEN' NAME= 'wfecini' value='".$wfecini."'>";
		echo "<input type='HIDDEN' NAME= 'wfecfin' value='".$wfecfin."'>";
		echo "<input type='HIDDEN' NAME= 'west' value='".$west."'>";
		echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";

		/***********************************Consulto lo pedido ********************/

		//se busca en la tabla 22 el detalle de recibos pendientes por cuadrar en estado = on y entre el rango de fechas.

		$clase1="class='texto1'";
		$clase2="class='texto4'";

		if($west=="SIN CUADRAR")
			$wcest="S";
		else {
			if($west=="PENDIENTE")
				$wcest="P";
			else $wcest="T";
		}

		if($wcest=="T")
		{
			$q = " SELECT  fecha_data,rfpfue,rfpnum,rfpcco,rfpfpa,rfpvfp,rfpobs,rfpest,rfpecu,rfpcaf,Seguridad ".
	     	 "   FROM  ".$wbasedato."_000022 "
			." 	WHERE  fecha_data between '".$wfecini."'"
			."    AND  '".$wfecfin."'"
			."    AND  rfpecu in ('P','S') "
			."    AND  rfpest = 'on' "
			."  ORDER BY  fecha_data,rfpnum ";
		}
		else
		{
			$q = " SELECT  fecha_data,rfpfue,rfpnum,rfpcco,rfpfpa,rfpvfp,rfpobs,rfpest,rfpecu,rfpcaf,Seguridad ".
	     	 "   FROM  ".$wbasedato."_000022 "
			." 	WHERE  fecha_data between '".$wfecini."'"
			."    AND  '".$wfecfin."'"
			."    AND  rfpecu = '".$wcest."' "
			."    AND  rfpest = 'on' "
			."  ORDER BY  fecha_data,rfpnum ";
		}

		$err = mysql_query($q,$conex);
		$num1 = mysql_num_rows($err);

		$wtotrec = 0;

		if ($num1>0)
		{
			echo "<table align=center border=0 width=90%>";
			echo "<th align=CENTER class='titulo2' width='20%'>Fecha</th>";
			echo "<th align=CENTER class='titulo2' width='20%'>Fuente</th>";
			echo "<th align=CENTER class='titulo2' width='20%'>Documento</th>";
			echo "<th align=CENTER class='titulo2' width='20%' >Forma Pago</th>";
			echo "<th align=CENTER class='titulo2' width='20%' >Vlr.Recibo</th>";
			echo "<th align=CENTER class='titulo2' width='20%' >Observaciones</th>";
			echo "<th align=CENTER class='titulo2' width='20%' >Estado</th>";
			echo "<th align=CENTER class='titulo2' width='20%' >Caja Actual</th>";
			echo "<th align=CENTER class='titulo2' width='20%' >Usuario</th>";

			for ($j=0;$j<$num1;$j++)
			{
	      		$row = mysql_fetch_array($err);

	      		if($row[8]=="S")
	      			$west="SIN CUADRAR";
			    else {
						if($row[8]=="P")
							$west="PENDIENTE";
						else $west="TODOS";
					 }

				if ($j%2==0)
               		$clase1="class='texto1'";
            	else
	           		$clase1="class='texto4'";

				echo "<tr>";
				echo "<th align=center ".$clase1." width='20%'>".$row[0]."</th>";
				echo "<th align=center ".$clase1." width='20%'>".$row[1]."</th>";
				echo "<th align=center ".$clase1." width='20%'>".$row[2]."</th>";
				echo "<th align=center ".$clase1." width='20%'>".$row[4]."</th>";
				echo "<th align=center ".$clase1." width='20%'>".$row[5]."</th>";
				echo "<th align=center ".$clase1." width='20%'>".$row[6]."</th>";
				echo "<th align=center ".$clase1." width='20%'>".$west."</th>";
				echo "<th align=center ".$clase1." width='20%'>".$row[9]."</th>";
				echo "<th align=center ".$clase1." width='20%'>".$row[10]."</th>";
				echo "</tr>";

	     		$wtotrec=$wtotrec+$row[5];
	  		}
    	}

		if ($wtotrec==0)
		{
			echo "<table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
			echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>Sin ningun documento en el rango de fechas seleccionado</td><tr>";
		}
		else if ($wtotrec>0)
		{
			echo "<tr><th align=CENTER class='acumulado3' colspan=4>TOTAL VALOR RECIBOS: </th>";
			echo "<th align=CENTER class='acumulado3'>".number_format($wtotrec,0,'.',',')."</th>";
			echo "<th align=CENTER class='acumulado3' colspan=4> </th></tr>";

		}
		echo "</table>";		
		echo "</br><center><A href='RepGenCaja.php?wemp_pmla=".$wemp_pmla."&amp;wfecini=".$wfecini."&amp;wfecfin=".$wfecfin."&amp;west=".$west."&amp;bandera='1'>VOLVER</A><br><br><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></center>";
	}
}
liberarConexionBD($conex);
?>
</body>
</html>
