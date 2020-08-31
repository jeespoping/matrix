<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<title>Programa de Querys de medios magneticos</title>

<!-- UTF-8 is the recommended encoding for your pages -->
    <meta http-equiv="content-type" content="text/xml; charset=utf-8" />
    <title>Zapatec DHTML Calendar</title>

<!-- Loading Theme file(s) -->
    <link rel="stylesheet" href="../../zpcal/themes/fancyblue.css" />

<!-- Loading Calendar JavaScript files -->
    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>

<!-- Loading language definition file -->
    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>

</head>
<body>
<?php
include_once("conex.php"); 

/**
 * 	Querys que se han pedido para entregar informacion de medios magneticos a la DIAN
 * 
 * 
 * @name  matrix\ips\procesos\querysMagneticos.php
 * @author Carolina Castaño Portilla.
 * 
 * @modified 2007-03-26 Carolina Castaño Portilla. se cambia para cuando el valor neto de la factura sea negativo no sea tenido en cuenta en la suma del valor facturado
 * 
 * @created 2007-03-21
 * @version 2007-03-26
 * 
 * 
*/

$wautor="Carolina Castano P.";
$wversion='2007-03-26';

//=================================================================================================================================

/********************************funciones de persistencia************************************/
function consultarEmpresasXNit()
{
	global $conex;
	global $wbasedato;

	$q =  " SELECT  empraz, empnit, empdir "
	."   FROM ".$wbasedato."_000024 "
	."  WHERE empcod=empres "
	."  GROUP BY empnit ORDER BY 1 desc  ";

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);
	$contador=1;
	for ($i=0;$i<$num;$i++)
	{
		$row = mysql_fetch_array($res);
		if ($row[1]=='99999')
		{
			$emp[0]['nom']=$row[0];
			$emp[0]['nit']=$row[1];
			$emp[0]['dir']=$row[2];
		}
		else
		{
			$emp[$contador]['nom']=$row[0];
			$emp[$contador]['nit']=$row[1];
			$emp[$contador]['dir']=$row[2];
			$contador++;
		}
	}

	if ($i>0)
	{
		return $emp;
	}
	else
	{
		return false;
	}
}

function consultarParticulares($fecha1, $fecha2)
{
	global $conex;
	global $wbasedato;

	if ($fecha2!='')
	{
		$q = " SELECT distinct (fendpa), fennpa, fenhis "
		."    FROM  ".$wbasedato."_000018 "
		."     WHERE  fenfec between '".$fecha1."'"
		."     AND '".$fecha2."'"
		."     AND fennit = '99999' "
		."     AND fenest = 'on' "
		."     AND fencco not in (select ccocod from ".$wbasedato."_000003 where ccotip='P' and ccoest='on') "
		."     AND fencco<>'' "
		."     group by fendpa ";

	}
	else
	{
		$q = " SELECT distinct (fendpa), fennpa, fenhis "
		."    FROM  ".$wbasedato."_000018 "
		."     WHERE  fenfec <= '".$fecha1."'"
		."     AND fencod = '01' "
		."     AND fenest = 'on' "
		."     AND fencco not in (select ccocod from ".$wbasedato."_000003 where ccotip='P' and ccoest='on') "
		."     AND fencco<>'' "
		."     group by fendpa ";

	}

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	for ($i=0;$i<$num;$i++)
	{
		$row = mysql_fetch_array($res);
		$par[$i]['nom']=$row[1];
		$par[$i]['nit']=$row[0];

		$q =  " SELECT  pacdir "
		."   FROM ".$wbasedato."_000100 "
		."  WHERE pachis='".$row[2]."' ";

		$res1 = mysql_query($q,$conex);
		$row1 = mysql_fetch_array($res1);

		$par[$i]['dir']=$row1[0];
	}
	if ($i>0)
	{
		return $par;
	}
	else
	{
		return false;
	}
}

function consultarCuentas()
{
	global $conex;
	global $wbasedato;


	$q = " SELECT distinct (relconcin) "
	."   	FROM  ".$wbasedato."_000077 "
	." 		WHERE  (mid(relconcin,1,2)='41' or  mid(relconcin,1,2)='42' or  mid(relconcin,1,2)='24') order by relconcin";

	$errx = mysql_query($q,$conex);
	$numx = mysql_num_rows($errx);

	for ($j=0; $j<$numx; $j++)
	{
		$rowx = mysql_fetch_array($errx);
		$cuentas[$j]=$rowx[0];
	}
	return $cuentas;
}

function calcularAcumuladoFacturacion($wfecini, $wfecfin, $emp, &$resPar, &$resEmp, &$acuPar, &$acuEmp, &$acuTot, $cuenta)
{
	global $conex;
	global $wbasedato;


	$acuPar=0;

	$q = " SELECT sum(fdevco), fendpa, fennpa"
	."   	FROM  ".$wbasedato."_000018, ".$wbasedato."_000065, ".$wbasedato."_000077, ".$wbasedato."_000024  "
	." 		WHERE  fenfec between '".$wfecini."'"
	."    	AND '".$wfecfin."'"
	."    	AND fenest = 'on' "
	."      AND fencco not in (select ccocod from ".$wbasedato."_000003 where ccotip='P' and ccoest='on') "
	."     AND fencco<>'' "
	."     AND fennit = '99999' "
	."     AND relconcin= '".$cuenta."' "
	."     AND fencod = empcod "
	."     AND fennit = empnit"
	."     AND relcontem = mid(emptem,1,instr(emptem,'-')-1) "
	."     AND fdecon = relconcon "
	."     AND fdecco = relconcco "
	."     AND fdefue = fenffa "
	."     AND fdedoc = fenfac "
	."     AND fdeest = 'on' "
	."  	GROUP BY fendpa ";

	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);


	for ($i=0; $i<$num; $i++)
	{
		$row = mysql_fetch_array($err);

		$resPar[$i]['nit']=$row[1];
		$resPar[$i]['nombre']=$row[2];
		$resPar[$i]['valor']=$row[0];
		$acuPar=$acuPar+$row[0];
	}


	if ($i==0)
	{
		$resPar=false;
	}
	$contador=0;

	$acuEmp=0;


	$q = " SELECT sum(fdevco), fennit, empraz "
	."   	FROM  ".$wbasedato."_000018, ".$wbasedato."_000065, ".$wbasedato."_000077, ".$wbasedato."_000024 "
	." 		WHERE  fenfec between '".$wfecini."'"
	."    	AND '".$wfecfin."'"
	."    	AND fenest = 'on' "
	."      AND fencco not in (select ccocod from ".$wbasedato."_000003 where ccotip='P' and ccoest='on') "
	."     AND fencco<>'' "
	."     AND relconcin= '".$cuenta."' "
	."     AND fencod = empcod "
	."     AND fennit = empnit"
	."     AND relcontem = mid(emptem,1,instr(emptem,'-')-1) "
	."     AND fdecon = relconcon "
	."     AND fdecco = relconcco "
	."     AND fdefue = fenffa "
	."     AND fdeest = 'on' "
	."     AND fdedoc = fenfac "
	."  	GROUP BY fennit ";

	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);

	for ($i=0; $i<$num; $i++)
	{
		$row = mysql_fetch_array($err);
		$resEmp[$contador]['nit']=$row[1];
		$resEmp[$contador]['nombre']=$row[2];
		$resEmp[$contador]['valor']=$row[0];
		$contador++;
		$acuEmp=$acuEmp+$row[0];
	}



	if ($contador==0)
	{
		$resEmp=false;
	}

	$acuTot=$acuEmp;

}

function calcularNotasCredito($wfecini, $wfecfin, $emp, &$resPar, &$resEmp, &$acuPar, &$acuEmp, &$acuTot)
{
	global $conex;
	global $wbasedato;

	$acuPar=0;

	$q = " SELECT  sum(renvca), fendpa, fennpa"
	."    FROM ".$wbasedato."_000020 a, ".$wbasedato."_000021 b, ".$wbasedato."_000018 c "
	."   	WHERE  a.renfec between '".$wfecini."'"
	."     AND '".$wfecfin."'"
	."     AND a.renest = 'on' "
	."     AND a.renfue = '27' "
	."     AND b.rdefue = a.renfue "
	."     AND b.rdenum = a.rennum "
	."     AND b.rdecco = a.rencco "
	."     AND b.rdeffa = c.fenffa "
	."     AND b.rdefac = c.fenfac "
	."     AND c.fencod = '01' "
	."     GROUP BY c.fendpa ";

	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);

	for ($i=0; $i<$num; $i++)
	{
		$row = mysql_fetch_array($err);
		$resPar[$i]['nit']=$row[1];
		$resPar[$i]['nombre']=$row[2];
		$resPar[$i]['valor']=$row[0];
		$acuPar=$acuPar+$row[0];
	}

	if ($i==0)
	{
		$resPar=false;
	}

	$contador=0;
	$acuEmp=0;

	if ($emp)
	{
		for ($i=0; $i<count($emp); $i++)
		{
			$q = " SELECT  sum(renvca) "
			."    FROM ".$wbasedato."_000020 a, ".$wbasedato."_000021 b, ".$wbasedato."_000018 c "
			."   	WHERE  a.renfec between '".$wfecini."'"
			."     AND '".$wfecfin."'"
			."     AND a.renest = 'on' "
			."     AND a.renfue = '27' "
			."     AND b.rdefue = a.renfue "
			."     AND b.rdenum = a.rennum "
			."     AND b.rdecco = a.rencco "
			."     AND b.rdeffa = c.fenffa "
			."     AND b.rdefac = c.fenfac "
			."     AND c.fennit= '".$emp[$i]['nit']."' "
			."     GROUP BY c.fennit ";

			$err = mysql_query($q,$conex);
			$num = mysql_num_rows($err);

			if ($num>0)
			{
				$row = mysql_fetch_array($err);
				$resEmp[$contador]['nit']=$emp[$i]['nit'];
				$resEmp[$contador]['nombre']=$emp[$i]['nom'];
				$resEmp[$contador]['valor']=$row[0];
				$contador++;
				$acuEmp=$acuEmp+$row[0];
			}
		}
	}

	if ($contador==0)
	{
		$resEmp=false;
	}

	$acuTot=$acuEmp;
}

function calcularSaldosCartera($wfecini, $emp, &$resPar, &$resEmp, &$acuPar, &$acuEmp, &$acuTot)
{
	global $conex;
	global $wbasedato;

	$acuPar=0;
	$contador=-1;

	$q = " SELECT  a.fenffa, a.fenfac, a.fenval, a.fendpa, a.fennpa, a.fenhis "
	."    FROM  ".$wbasedato."_000018 a"
	."   	WHERE  a.fenfec <= '".$wfecini."'"
	."     AND a.fenest = 'on' "
	."     AND a.fencco<>'' "
	."     AND a.fenval>0 "
	."     AND a.fencco not in (select ccocod from ".$wbasedato."_000003 where ccotip='P' and ccoest='on') "
	."     AND a.fencod = '01' "
	."     order BY  a.fendpa ";

	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);
	$ant=false;

	for ($i=0; $i<$num; $i++)
	{
		$row = mysql_fetch_array($err);

		$q = " SELECT  b.rdesfa "
		."    FROM  ".$wbasedato."_000020 a, ".$wbasedato."_000021 b   "
		."   	WHERE   rdefac= '".$row[1]."' "
		."     AND rdeffa= '".$row[0]."' "
		."     AND rdeest= 'on' "
		."     AND rdesfa<>'' "
		."     AND rdereg=0 "
		."     AND renfec <= '".$wfecini."'  "
		."     AND renfue=rdefue  "
		."     AND rennum=rdenum  "
		."     AND rencco=rdecco  "
		."     ORDER BY  b.id desc";

		$err2 = mysql_query($q,$conex);
		$num2 = mysql_num_rows($err2);
		$row2 = mysql_fetch_array($err2);

		if ($row[3]!=$ant)
		{
			if ($num2>0)
			{
				if ($row2[0]>0)
				{
					$contador++;
					$resPar[$contador]['nit']=$row[3];
					$resPar[$contador]['nombre']=$row[4];

					$q =  " SELECT  pacdir "
					."   FROM ".$wbasedato."_000100 "
					."  WHERE pachis='".$row[5]."' ";

					$res1 = mysql_query($q,$conex);
					$row1 = mysql_fetch_array($res1);

					$resPar[$contador]['direccion']=$row1[0];
					$resPar[$contador]['valor']=$row2[0];
					$acuPar=$acuPar+$resPar[$contador]['valor'];
					$ant=$row[3];
				}
			}
			else
			{
				if ($i!=0)
				{
					$contador++;
				}

				$resPar[$contador]['nit']=$row[3];
				$resPar[$contador]['nombre']=$row[4];

				$q =  " SELECT  pacdir "
				."   FROM ".$wbasedato."_000100 "
				."  WHERE pachis='".$row[5]."' ";

				$res1 = mysql_query($q,$conex);
				$row1 = mysql_fetch_array($res1);

				$resPar[$contador]['direccion']=$row1[0];
				$resPar[$contador]['valor']=$row[2];
				$acuPar=$acuPar+$resPar[$contador]['valor'];
				$ant=$row[3];
			}

		}
		else
		{
			if ($num2>0)
			{
				if ($row2[0]>0)
				{
					$resPar[$contador]['valor']=$resPar[$contador]['valor']+$row2[0];
					$acuPar=$acuPar+$row2[0];
				}
			}
			else
			{
				$resPar[$contador]['valor']=$resPar[$contador]['valor']+$row[2];
				$acuPar=$acuPar+$row[2];
			}
		}
	}

	if ($contador<=0)
	{
		$resPar=false;
	}
	$contador=0;
	$acuEmp=0;

	if ($emp)
	{
		for ($i=0; $i<count($emp); $i++)
		{
			$q = " SELECT  a.fenffa, a.fenfac, a.fenval "
			."    FROM  ".$wbasedato."_000018 a"
			."   	WHERE  a.fenfec <= '".$wfecini."'"
			."     AND a.fennit = '".$emp[$i]['nit']."' "
			."     AND a.fenest = 'on' "
			."     AND a.fencco<>'' "
			."     AND a.fenval>0 "
			."     AND a.fencco not in (select ccocod from ".$wbasedato."_000003 where ccotip='P' and ccoest='on') ";

			$err = mysql_query($q,$conex);
			$num = mysql_num_rows($err);
			$valor=0;

			for ($j=0; $j<$num; $j++)
			{
				$row = mysql_fetch_array($err);

				$q = " SELECT  b.rdesfa "
				."    FROM  ".$wbasedato."_000020 a, ".$wbasedato."_000021 b   "
				."   	WHERE   rdefac= '".$row[1]."' "
				."     AND rdeffa= '".$row[0]."' "
				."     AND rdeest= 'on' "
				."     AND rdesfa<>'' "
				."     AND rdereg=0 "
				."     AND renfec <= '".$wfecini."'  "
				."     AND renfue=rdefue  "
				."     AND rennum=rdenum  "
				."     AND rencco=rdecco  "
				."     ORDER BY  b.id desc";


				$err2 = mysql_query($q,$conex);
				$num2 = mysql_num_rows($err2);
				$row2 = mysql_fetch_array($err2);

				if ($num2>0)
				{
					$valor=$valor+$row2[0];
				}
				else
				{
					$valor=$valor+$row[2];
				}
			}

			if ($num>0 and $valor>0)
			{
				$resEmp[$contador]['nit']=$emp[$i]['nit'];
				$resEmp[$contador]['nombre']=$emp[$i]['nom'];
				$resEmp[$contador]['direccion']=$emp[$i]['dir'];
				$resEmp[$contador]['valor']=$valor;
				$contador++;
				$acuEmp=$acuEmp+$valor;
			}
		}
	}

	if ($contador==0)
	{
		$resEmp=false;
	}

	$acuTot=$acuEmp;
}

/********************************funciones DE PRESENTACION************************************/
function pintarAlert3($mensaje)
{
	echo '<script language="Javascript">';
	echo 'alert ("'.$mensaje.'");';
	echo 'document.volver.submit();';
	echo '</script>';
}

function pintarResultado($resPar, $resEmp, $acuTot, $pintarDir)
{
	if($acuTot>0)
	{
		echo '<table cellspacing="1" cellpadding="1" border="1" width="92%" align="center">';
		echo '<tr>';
		echo '<th bgcolor="006699"><font color="FFFFFF">NIT</font></th>';
		echo '<th bgcolor="006699"><font color="FFFFFF">NOMBRE O RAZON SOCIAL</font></th>';
		if ($pintarDir==1)
		{
			echo '<th bgcolor="006699"><font color="FFFFFF">DIRECCION</font></th>';
		}
		echo '<th bgcolor="006699"><font color="FFFFFF">VALOR</font></th>';
		echo '</tr>';

		if ($resPar)
		{
			$color='DDDDDD';
			for ($i=0;$i<count($resPar);$i++)
			{
				If ($color=='DDDDDD')
				{
					$color='FFFFFF';
				}
				else
				{
					$color='DDDDDD';
				}
				echo '<tr>';
				echo '<td bgcolor="'.$color.'">'.$resPar[$i]['nit'].'</td>';
				echo '<td bgcolor="'.$color.'">'.$resPar[$i]['nombre'].'</td>';
				if ($pintarDir==1)
				{
					echo '<td bgcolor="'.$color.'">'.$resPar[$i]['direccion'].'</td>';
				}
				echo '<td bgcolor="'.$color.'" align="right">'.number_format($resPar[$i]['valor'],0,'.',',').'</td>';
				echo '</tr>';
			}
		}

		if ($resEmp)
		{
			$color='FFFFFF';
			for ($i=0;$i<count($resEmp);$i++)
			{
				If ($color=='#FFDBA8')
				{
					$color='FFFFFF';
				}
				else
				{
					$color='#FFDBA8';
				}

				echo '<tr>';
				echo '<td bgcolor="'.$color.'">'.$resEmp[$i]['nit'].'</td>';
				echo '<td bgcolor="'.$color.'">'.$resEmp[$i]['nombre'].'</td>';
				if ($pintarDir==1)
				{
					echo '<td bgcolor="'.$color.'">'.$resEmp[$i]['direccion'].'</td>';
				}
				echo '<td bgcolor="'.$color.'" align="right">'.number_format($resEmp[$i]['valor'],0,'.',',').'</td>';
				echo '</tr>';
			}
		}


		if ($pintarDir==1)
		{
			$colspan=3;
		}
		else
		{
			$colspan=2;
		}
		echo '<tr>';
		echo '<td colspan="'.$colspan.'" align="right" bgcolor="#FFCC66">TOTAL</td>';
		echo '<td bgcolor="#FFCC66" align="right">'.number_format($acuTot,0,'.',',').'</td>';
		echo '</tr>';
		echo '</table>';
	}
	else
	{
		echo"<form action='querysMagneticos.php' method='post' name='form1' ><CENTER><fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080>";
		echo "<table align='center' border=0 bordercolor=#000080 width=700 style='border:solid;'>";
		echo "<tr><td colspan='2' align=center><font size=3 color='#000080' face='arial' align=center><b>'NO SE ENCONTRO NINGUN REGISTRO CON LOS PARAMETROS ENCONTRADOS'</td><tr>";
		echo "<tr><td colspan='2' align='center'><input type='button' name='aceptar' value='ACEPTAR' onclick='javascript:window.close()'></td><tr>";
		echo "</table></fieldset></form>";
	}
}

/****************************PROGRAMA************************************************/
session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{

	/////////////////////////////////////////////////encabezado general///////////////////////////////////
	echo "<table align='right'>" ;
	echo "<tr>" ;
	echo "<td><font color=\"#D02090\" size='2'>Autor: ".$wautor."</font></td>";
	echo "</tr>" ;
	echo "<tr>" ;
	echo "<td><font color=\"#D02090\" size='2'>Version: ".$wversion."</font></td>" ;
	echo "</tr>" ;
	echo "</table></br></br></br>" ;

	echo "<table align='center'>\n" ;
	echo "<tr>" ;
	echo "<td><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png'  height='100' width='300'></td>";
	echo "</tr>" ;
	echo "</table></br>" ;

	echo "<center><b><font size=\"4\"><A HREF='querysMagneticos.php?lista=1&wbasedato=".$wbasedato."'><font color=\"#D02090\">QUERYS MEDIOS MAGNETICOS</font></a></b></font></center>\n" ;
	echo "<center><b><font size=\"2\"><font color=\"#D02090\">querysMagneticos.php</font></font></center></br></br></br>\n" ;
	echo "\n" ;

	/////////////////////////////////////////////////encabezado general///////////////////////////////////
	/////////////////////////////////////////////////inicialización de variables///////////////////////////////////

	/**
	 * include de conexión a base de datos Matrix
	 *
	 */
	

	


	/////////////////////////////////////////////////inicialización de variables//////////////////////////
	/////////////////////////////////////////////////acciones concretas///////////////////////////////////


	if (!isset ($radio)) ///////////////////////////entramos por primera vez al reporte/////////////////////////
	{

		echo "<center><font color='#00008B'>SELECCIONE POR FAVOR LA CONSULTA DESEADA:</font></center></BR>";


		// Busqueda de comentario entre dos fechas

		echo "<fieldset style='border:solid;border-color:#00008B; width=700' align=center></br>";

		echo "<form NAME='ingreso' ACTION='' METHOD='POST'>";
		echo "<table align='center'>";

		echo "<tr>";
		echo "<td  bgcolor=#336699 ><input type='Radio' name='radio' value='1'><font size=3  face='arial' color='#ffffff'>ACUMULADO DE FACTURACION</font>";
		$cal="calendario('wfecini','1')";
		echo "<td align=center bgcolor=#336699><font size=2  face='arial' color='#ffffff'>Fecha inicial: </font><INPUT TYPE='text' readonly='readonly' NAME='wfecini1' value='' SIZE=10><input type='button' name='envio1' value='...' onclick=".$cal."' size=10 maxlength=10></td>";
				?>
				<script type="text/javascript">//<![CDATA[
				Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'12',electric:false,inputField:'wfecini1',button:'envio1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
				//]]></script>
				<?php
				echo "<td  align=center bgcolor=#336699><font size=2  face='arial' color='#ffffff'>Fecha final:</font><INPUT TYPE='text' readonly='readonly' NAME='wfecfin1' value='' SIZE=10><input type='button' name='envio2' value='...' onclick=".$cal."' size=10 maxlength=10></td>";
				?>
				<script type="text/javascript">//<![CDATA[
				Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'12',electric:false,inputField:'wfecfin1',button:'envio2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
				//]]></script>
				<?php
				echo "</tr>";

				echo "<tr>";
				echo "<td  bgcolor=#336699 ><input type='Radio' name='radio' value='2'><font size=3  face='arial' color='#ffffff'>NOTAS CREDITO (fuente 27)</font>";
				$cal="calendario('wfecini','1')";
				echo "<td align=center bgcolor=#336699><font size=2  face='arial' color='#ffffff'>Fecha inicial: </font><INPUT TYPE='text' readonly='readonly' NAME='wfecini2' value='' SIZE=10><input type='button' name='envio3' value='...' onclick=".$cal."' size=10 maxlength=10></td>";
				?>
				<script type="text/javascript">//<![CDATA[
				Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'12',electric:false,inputField:'wfecini2',button:'envio3',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
				//]]></script>
				<?php
				echo "<td  align=center bgcolor=#336699><font size=2  face='arial' color='#ffffff'>Fecha final: </font><INPUT TYPE='text' readonly='readonly' NAME='wfecfin2' value='' SIZE=10><input type='button' name='envio4' value='...' onclick=".$cal."' size=10 maxlength=10></td>";
				?>
				<script type="text/javascript">//<![CDATA[
				Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'12',electric:false,inputField:'wfecfin2',button:'envio4',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
				//]]></script>
				<?php
				echo "</tr>";


				echo "<tr>";
				echo "<td  bgcolor=#336699 ><input type='Radio' name='radio' value='3'><font size=3  face='arial' color='#ffffff'>SALDOS DE CARTERA</font>";
				$cal="calendario('wfecini','1')";
				echo "<td align=center bgcolor=#336699><font size=2  face='arial' color='#ffffff'>Fecha de corte: </font><INPUT TYPE='text' readonly='readonly' NAME='wfecini3' value='' SIZE=10><input type='button' name='envio5' value='...' onclick=".$cal."' size=10 maxlength=10></td>";
				?>
				<script type="text/javascript">//<![CDATA[
				Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'12',electric:false,inputField:'wfecini3',button:'envio5',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
				//]]></script>
				<?php
				echo "<td  bgcolor=#336699 >&nbsp;</font></td>";
				echo "</tr>";

				echo "</td>";
				echo "</tr></TABLE></br>";
				echo "<TABLE align=center><tr>";
				echo "<tr>";
				echo "<td align=center colspan=14><font size='2'  align=center face='arial'><input type='submit' name='aceptar' value='CONSULTAR' ></td></tr>";
				echo "</TABLE>";
				echo "</td>";
				echo "</tr>	";
				echo "</form>";
				echo "</fieldset>";
}
else ////////////////////se han seleccionado los parametros del reporte///////////////////////////////////
{
	echo "<form NAME='volver' ACTION='' METHOD='POST'>";
	$emp=consultarEmpresasXNit(); //se consultan empresas en un vector agrupadas por nit

	switch ($radio)
	{
		case 1:
		if ($wfecini1=='' or $wfecini1=='')
		{
			pintarAlert3('DEBE INGRESAR LA FECHA INICIAL Y FINAL PARA LA REALIZACION DE QUERY');
		}
		else
		{
			echo "<center><font color='#00008B'>ACUMULADO DE FACTURACION DESDE ".$wfecini1." HASTA ".$wfecfin1.":</center></br>";
			$cuentas=consultarCuentas();
			$total=0;
			for ($j=0; $j<count($cuentas); $j++)
			{

				calcularAcumuladoFacturacion($wfecini1, $wfecfin1, $emp, &$resPar, &$resEmp, &$acuPar, &$acuEmp, &$acuTot, $cuentas[$j]);
				$total=$total+$acuTot;
				IF ($acuTot>0)
				{
					echo "<BR><center>CUENTA NUMERO: ".$cuentas[$j]."</center></BR>";
					pintarResultado($resPar, $resEmp,  $acuTot, 0);
				}
				if (isset($resPar))
				unset($resPar);
				if (isset($resEmp))
				unset($resEmp);
			}

		}
		echo $total;
		break;

		case 2:
		if ($wfecini2=='' or $wfecfin2=='')
		{
			pintarAlert3('DEBE INGRESAR LA FECHA INICIAL Y FINAL PARA LA REALIZACION DE QUERY');
		}
		else
		{
			echo "<center><font color='#00008B'>NOTAS CREDITO (fuente 27) DESDE ".$wfecini2." HASTA ".$wfecfin2.":</center></br>";
			calcularNotasCredito($wfecini2, $wfecfin2, $emp, $resPar, $resEmp, &$acuPar, &$acuEmp, &$acuTot);
			pintarResultado($resPar, $resEmp, $acuTot, 0);
		}
		break;

		case 3:
		if ($wfecini3=='')
		{
			pintarAlert3('DEBE INGRESAR LA FECHA DE CORTE PARA LA REALIZACION DE QUERY');
		}
		else
		{
			echo "<center><font color='#00008B'>SALDOS DE CARTERA A ".$wfecini3.": </center></br> ";
			calcularSaldosCartera($wfecini3, $emp, $resPar, $resEmp, &$acuPar, &$acuEmp, &$acuTot);
			pintarResultado($resPar, $resEmp, $acuTot, 1);
		}
		break;
	}
	echo "</form>";
}
}

?>

</body>
</html>