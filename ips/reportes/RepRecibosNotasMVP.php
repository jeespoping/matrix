<html>

<head>
  <title>REPORTE DE RECIBOS DE CAJA Y NOTAS DEBITO Y CREDITO</title>

   <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
  
    	.titulo1{color:#FFFFFF;background:#006699;font-size:20pt;font-family:Arial;font-weight:bold;text-align:center;}	
    	.titulo2{color:#003366;background:#A4E1E8;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.titulo3{color:#003366;background:#57C8D5;font-size:12pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	.titulo4{color:#003366;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.texto1{color:#006699;background:#FFFFFF;font-size:9pt;font-family:Tahoma;text-align:center;}
    	.texto2{color:#006699;background:#f5f5dc;font-size:9pt;font-family:Tahoma;text-align:center;}
    	.texto3{color:#006699;background:#A4E1E8;font-size:9pt;font-weight:bold;font-family:Tahoma;text-align:center;}
    	.texto4{color:#006699;background:#FFFFFF;font-size:9pt;font-family:Tahoma;text-align:right;}
    	.texto5{color:#006699;background:#f5f5dc;font-size:9pt;font-family:Tahoma;text-align:right;}
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

/**
 * NOMBRE:  REPORTE DE RECIBOS CON MAYOR VALOR PAGADO
 *
 * PROGRAMA: RepRecibosNotasMVP.php
 * TIPO DE SCRIPT: PRINCIPAL
 * //DESCRIPCION:Este reporte presenta la lista de recibos entre dos fechas
 * 
 * HISTORIAL DE ACTAULIZACIONES:
 * 2007-04-18 carolina castano, creacion del script
 * 
 * Tablas que utiliza:
 * $wbasedato."_000040: Maestro de Fuentes, select
 * $wbasedato."_000024: select en maestro de empresas
 * $wbasedato."_000020: select en encabezado de cartera
 * $wbasedato."_000021: select en detalle de cartera
 * $wbasedato."_000065: select en detalle por conceptos de facturacion
 * 
 * @author ccastano
 * @package defaultPackage
 */

$wautor="Carolina Castano P.";



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

	$key = substr($user,2,strlen($user));

	$conex = obtenerConexionBD("matrix");

	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

	$wbasedato = strtolower($institucion->baseDeDatos);
	$wentidad = $institucion->nombre;

	echo "<form action='RepRecibosNotasMVP.php' method=post name='forma'>";

	$wfecha=date("Y-m-d");

	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";

	if (!isset($wfecini) or !isset($wfecfin) or !isset($wemp)  or !isset ($resultado))
	{

		echo "<center><table border=0>";
		echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=500 HEIGHT=100></td></tr>";
		echo "<tr><td class='titulo1'>REPORTE DE RECIBOS</td></tr>";

		//INGRESO DE VARIABLES PARA EL REPORTE//
		if (!isset ($bandera))
		{
			$wfecini=$wfecha;
			$wfecfin=$wfecha;
			$wfeccor=$wfecha;

		}

		echo "<tr>";
		echo "<td align=center class='texto3'><b>FECHA INICIAL DE FACTURACION: </font></b>";
		campoFecha("wfecini");
		echo "</td>";
		
		echo "<td align=center class='texto3'><b>FECHA FINAL DE FACTURACION: </font></b>";
		campoFecha("wfecfin");
		echo "</td>";

		echo "</tr>";

				//fuente
				$q= "   SELECT carfue, cardes "
				."     FROM ".$wbasedato."_000040 "
				."    WHERE carrec='on' ";

				$res = mysql_query($q,$conex);
				$num = mysql_num_rows($res);

				echo "<tr><td align=center class='texto3' colspan=2><b>FUENTE: ";
				echo "<select name='wfue'>";
				if (isset ($bandera))
				echo "<option>".$wfue."</option>";

				for ($i=1;$i<=$num;$i++)
				{
					$row = mysql_fetch_array($res);
					if ($wfue!=$row[0]."-".$row[1])
					echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				echo "</select><input type='checkbox' name='can' >Mostrar Valor a Cancelar</td>";

				echo "</tr>";

				echo "<tr>";

				//SELECCIONAR EMPRESA
				$q =  " SELECT empcod, empnom, empnit  "
				."   FROM ".$wbasedato."_000024 " //.$wbasedato."_000029 "
				."  WHERE empcod=empres "
				."  ORDER BY 2   ";

				$res = mysql_query($q,$conex);
				$num = mysql_num_rows($res);

				echo "<td align=center class='texto3' colspan='2'>EMPRESA: ";
				echo "<select name='wemp'>";
				if (isset ($bandera))
				{
					echo "<option>".$wemp."</option>";
					if ($wemp!='% - Todas las empresas')
					{
						echo "<option>% - Todas las empresas</option>";
					}
				}
				else
				echo "<option>% - Todas las empresas</option>";


				for ($i=1;$i<=$num;$i++)
				{
					$row = mysql_fetch_array($res);
					$print=explode ('-',$wemp);
					$prin=$print[0].'-'.$print[1];
					if ($prin!=$row[0]."-".$row[1])
					echo "<option>".$row[0]."-".$row[2]."-".$row[1]."- <b>[&nbsp&nbsp&nbsp&nbsp".$row[2]."&nbsp&nbsp&nbsp&nbsp]</b></option>";

				}
				echo "</select></td></tr>";


				echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";

				echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";
				echo "<input type='HIDDEN' NAME= 'resultado' value='1'>";

				echo "<tr><td align=center class='texto3' COLSPAN='2'>";
				echo "<input type='radio' name='vol' value='SI' onclick='Seleccionar()' checked> DESPLEGAR REPORTE DETALLADO&nbsp;&nbsp;&nbsp;&nbsp;";
				echo "<input type='radio' name='vol' value='NO'  onclick='Seleccionar()' > DESPLEGAR REPORTE RESUMIDO&nbsp;&nbsp;";                //submit
				echo "</b></td></tr></table></br>";
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
	echo "<tr><td><B>REPORTE DE: ".$wfue."</B></td></tr>";
	echo "</tr><td align=right ><A href='RepRecibosNotasMVP.php?wemp_pmla=".$wemp_pmla."&amp;wfecini=".$wfecini."&amp;wfecfin=".$wfecfin."&amp;wfue=".$wfue."&amp;wemp=".$wemp."&amp;bandera='1'>VOLVER</A></td></tr>";
	echo "<tr align='center'><td><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></td></tr>";
	echo "<tr><td><tr><td>Fecha inicial: ".$wfecini."</td></tr>";
	echo "<tr><td>Fecha final: ".$wfecfin."</td></tr>";
	echo "<tr><td>Empresa: ".$wemp."</td></tr>";
	echo "</table></br>";

	echo "<input type='HIDDEN' NAME= 'wfecini' value='".$wfecini."'>";
	echo "<input type='HIDDEN' NAME= 'wfecfin' value='".$wfecfin."'>";
	echo "<input type='HIDDEN' NAME= 'wemp' value='".$wemp."'>";
	echo "<input type='HIDDEN' NAME= 'wfue' value='".$wfue."'>";
	echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";

	$print=explode ('-',$wfue);
	$fuente=$print[0];


	/***********************************Consulto lo pedido ********************/

	// si la empresa es diferente a todas las enpresas, la meto en el vector solo
	// si es todas las empresas meto todas en un vector para luego preguntarlas en un for

	if ($wemp !='% - Todas las empresas')
	{
		$print=explode('-', $wemp);
		$empCod[0]=trim ($print[0]);
		$empNom[0]=trim ($print[2]);
		$empNit[0]=trim ($print[1]);
		$empTip[0]=trim ($print[2]).'-'.trim ($print[3]);
		$print=substr($empTip[0],5, strlen($empTip[0]));
		$empTip[0]=substr($print,0, (strlen($print)-5));

		$empresa[0]=$empNom[0];
		$num=1;

	}
	else
	{

		$q =  " SELECT empcod, empnom, empnit "
		."   FROM ".$wbasedato."_000024 "
		."  WHERE empcod=empres "
		."  ORDER BY 3 desc ,1 ";

		$res = mysql_query($q,$conex);
		$num = mysql_num_rows($res);
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($res);
			$empCod[$i]=$row[0];
			$empNom[$i]=$row[1];
			$empNit[$i]=$row[2];
			$empresa[$i]=$row[1];
		}
	}

	//identifico si el documento es un recibo
	$q= "   SELECT carrec  "
	."     FROM ".$wbasedato."_000040 "
	."    WHERE carfue = '".$fuente."' ";
	$res1 = mysql_query($q,$conex);
	$row1 = mysql_fetch_array($res1);
	if ($row1[0]=='on')
	{
		$wcarrec=true;
	}
	else
	{
		$wcarrec=false;
	}

	//se busca en la tabla 20 y 21 registros con esa fuente, empresa por empresa en un for y entre las fechas escogidas
	$senal=0;
	$wtotal=0;
	for ($i=0;$i<$num;$i++)
	{
		if ($vol=='SI')
		{
			$q = " SELECT  a.rennum, a.rencod, a.renvca, a.renfec, b.rdefac, b.rdevca, b.rdevco, b.rdeffa, b.rdecon, b.rdecco "
			."    FROM ".$wbasedato."_000020 a, ".$wbasedato."_000021 b "
			."   	WHERE  a.renfec between '".$wfecini."'"
			."     AND '".$wfecfin."'"
			."     AND a.renest = 'on' "
			."     AND a.rencod = '".$empCod[$i]."' "
			."     AND a.renfue = '".$fuente."' "
			."     AND b.rdefue = a.renfue "
			."     AND b.rdenum = a.rennum "
			."     AND b.rdecco = a.rencco "
			."     ORDER BY  a.rennum, b.rdefac, a.renfec ";
		}
		else
		{

			$q = " SELECT  a.rennum, a.rencod, a.renvca, a.renfec,  b.rdecco, sum(b.rdevca), sum(b.rdevco) "
			."    FROM ".$wbasedato."_000020 a, ".$wbasedato."_000021 b "
			."   	WHERE  a.renfec between '".$wfecini."'"
			."     AND '".$wfecfin."'"
			."     AND a.renest = 'on' "
			."     AND a.rencod = '".$empCod[$i]."' "
			."     AND a.renfue = '".$fuente."' "
			."     AND b.rdefue = a.renfue "
			."     AND b.rdenum = a.rennum "
			."     AND b.rdecco = a.rencco "
			."     group BY  b.rdenum, b.rdefue, b.rdecco";
		}

		$err = mysql_query($q,$conex);
		$num1 = mysql_num_rows($err);

		if ($num1>0)
		{

			echo "<table  align =center width='60%'>";
			echo "<tr><td colspan=9 class='titulo3'>Empresa: ".$empCod[$i]."-".$empNom[$i]."</td></tr>";
			echo "<th align=CENTER class='titulo2'>FECHA</th>";
			echo "<th align=CENTER class='titulo2'>FUENTE DCO</th>";
			echo "<th align=CENTER class='titulo2'>NRO DOCUMENTO</th>";
			if (!isset ($can))
			{
				echo "<th align=CENTER class='titulo2'>VLR TOTAL DOCUMENTO</th>";
			}
			else
			{
				echo "<th align=CENTER class='titulo2'>VLR A CANCELAR DOCUMENTO</th>";
			}
			if ($vol=='SI')
			{
				echo "<th align=CENTER class='titulo2'>FUENTE FACTURA</th>";
				echo "<th align=CENTER class='titulo2'>NRO FACTURA</th></tr>";
			}
			else
			{
				echo "</tr>";
			}

			$row = mysql_fetch_array($err);
			$wtotemp = 0;


			for ($j=0;$j<$num1;$j++)
			{

				if (is_int ($j/2))
				{
					$clase1="class='texto1'";
					$clase2="class='texto4'";
				}
				else
				{
					$clase1="class='texto2'";
					$clase2="class='texto5'";
				}

				if ($vol!='SI')
				{
					$q = " SELECT  b.rdecon "
					."    FROM  ".$wbasedato."_000021 b "
					."   	WHERE  b.rdefue = '".$row[0]."' "
					."     AND b.rdenum = '".$fuente."' "
					."     AND b.rdecco = '".$row[4]."' ";

					$errcon = mysql_query($q,$conex);
					$con = mysql_fetch_array($errcon);
					$row[8]=$con[0];

				}

				if ($row[6]=='0' and $row[8]=='' and $row[5]=='0')
				{
					if (!isset($can))
					{
						if ($vol=='SI')
						{
							$q="select fdevco from ".$wbasedato."_000065 where fdefue='".$fuente."' and fdeest='on' and fdedoc='".$row[0]."' ";
						}
						else
						{
							$q="select sum(fdevco) from ".$wbasedato."_000065 where fdefue='".$fuente."' and fdeest='on' and fdedoc='".$row[0]."' ";
						}

						$err2 = mysql_query($q,$conex);
						$y = mysql_num_rows($err2);
						$row2 = mysql_fetch_array($err2);

						$valorReg=$row2[0];
					}
					else
					{
						$valorReg=0;
						$y=1;
					}

				}
				else
				{
					if ($wcarrec)
					{
						if ($vol=='SI' and $row[8]!='')
						{
							//debo consultar el multiplicador del concepto para ponerle signo
							$q="select conmul from ".$wbasedato."_000044 where concod=(mid('".$row[8]."',1,instr('".$row[8]."','-')-1)) and confue='".$fuente."'  ";
							$mulres = mysql_query($q,$conex);
							$mul = mysql_fetch_row($mulres);
							if($mul[0]==1)
							{
								$row[6]=0;
							}
						}

						if ($vol!='SI')
						{
							//debo consultar la suma del valor de los conceptos que suman en vez de restar al valor total del recibo
							//entonces se consultan y se restan dos veces
							$q = " SELECT  sum(b.rdevco) "
							."    FROM  ".$wbasedato."_000021 b, ".$wbasedato."_000044 c "
							."   	WHERE b.rdefue = '".$fuente."' "
							."     AND b.rdenum = '".$row[0]."' "
							."     AND b.rdecco = '".$row[4]."' "
							."     AND c.concod = (mid(rdecon,1,instr(rdecon,'-')-1)) "
							."     AND c.conmul = 1 "
							."     AND c.confue = rdefue "
							."     AND c.conest = 'on' ";
							$mulres = mysql_query($q,$conex);
							$mul = mysql_fetch_row($mulres);
							$row[6]=$row[6]-$mul[0];
						}
					}

					if (!isset ($can))
					{
						$valorReg=$row[5]+$row[6];
					}
					else
					{
						$valorReg=$row[5];
					}
					$y=1;
				}


				for ($k=1;$k<=$y;$k++)
				{
					echo "<th align=CENTER ".$clase1.">".$row[3]."</th>";
					echo "<th align=CENTER ".$clase1.">".$wfue."</th>";
					echo "<th align=CENTER ".$clase1.">".$row[0]."</th>";
					echo "<th  $clase2 >".number_format($valorReg,0,'.',',')."</th>";

					if ($vol=='SI')
					{
						echo "<th align=CENTER ".$clase1.">".$row[7]."</th>";
						echo "<th align=CENTER ".$clase1.">".$row[4]."</th></tr>";
					}
					else
					{
						echo "</tr>";
					}

					$wtotemp = $wtotemp + $valorReg;
					$wtotal=$wtotal + $valorReg;

					if ($y>1)
					{
						$row2 = mysql_fetch_array($err2);
						$valorReg=$row2[0];
					}
				}

				$row = mysql_fetch_array($err);

			}

			echo "<th align=CENTER class='acumulado3' colspan=3>TOTAL EMPRESA</th>";
			echo "<th align=CENTER class='acumulado4'>".number_format($wtotemp,0,'.',',')."</font></th>";
			if($vol=='SI')
			{
				echo "<th align=CENTER class='acumulado3'>&nbsp</th>";
				echo "<th align=CENTER class='acumulado3'>&nbsp</th></tr>";
			}
			else
			{
				echo "</tr>";
			}
		}else
		{
			$senal =$senal+1;

		}
	}

	if ($senal==$num)
	{
		echo "<table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
		echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>Sin ningun documento en el rango de fechas seleccionado</td><tr>";

	}

	else if ($num>1)
	{
		echo "<tr><th align=CENTER class='acumulado2' colspan=3>TOTAL</th>";
		echo "<th align=CENTER class='acumulado1' >".number_format($wtotal,0,'.',',')."</th>";
		if($vol=='SI')
		{
			echo "<th align=CENTER class='acumulado2' >&nbsp</th>";
			echo "<th align=CENTER class='acumulado2' >&nbsp</th></tr>";
		}
		else
		{
			echo "</tr>";
		}
	}
	echo "</table>";
	echo "</br><center><A href='RepRecibosNotasMVP.php?wemp_pmla=".$wemp_pmla."&amp;wfecini=".$wfecini."&amp;wfecfin=".$wfecfin."&amp;wfue=".$wfue."&amp;wemp=".$wemp."&amp;bandera='1'>VOLVER</A></center>";
	echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
}

echo "</form>";
}
liberarConexionBD($conex);
?>
</body>
</html>
