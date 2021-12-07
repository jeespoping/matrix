<html>
<head>
  	<title>MATRIX Generacion Automatica de Saldos del mes</title>
  	
  	 <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
      	.titulo1{color:#FFFFFF;background:#006699;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.titulo2{color:#006699;background:#FFFFFF;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.titulo3{color:#003366;background:#A4E1E8;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto1{color:#003366;background:#FFDBA8;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}	
    	.texto2{color:#003366;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto3{color:#003366;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto4{color:#003366;background:#f5f5dc;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto6{color:#FFFFFF;background:#006699;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.texto5{color:#003366;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
   </style>
  	 
</head>
<body>
<center>
<table border=0 align=center>
<tr><td class="titulo1"><font size=5>Generacion Automatica de Saldos del mes</font></tr></td>
<tr><td class="texto5"><b>Saldos.php Ver. 2007-01-09</b></tr></td></table>
</center> 
<?php

/******************************************************************************************************************************************
 * Modificaciones:
 *
 * 2021-12-02:	Se agrega log a base de datos para su ejecución para poder realizar trazabilidad de cuándo se ejecutó y si hubo error.
 * 2020-03-20:	Se hacen modifaciones varias para registrar en tabla saldos de inventarios(cenpro_000014), el insumo y el estado con 
 *				que se encuentra la presentación
 ******************************************************************************************************************************************/

include_once("conex.php");

function calcularProducto($cantidad, $lote, &$ins, $concepto)
{
	global $conex;
	global $empresa;

	$query = "SELECT Mdeart, Mdecan, Mdepre from ".$empresa."_000007 ";
	$query .= " where  Mdecon='".$concepto."'";
	$query .= "   and  Mdenlo='".$lote."'";
	//echo $query;
	$err2 = mysql_query($query,$conex);
	$num2 = mysql_num_rows($err2);

	for ($i=0; $i<$num2; $i++)
	{
		$row2 = mysql_fetch_array($err2);
		
		$exp=explode('-',$lote);
		$query = "SELECT plocin from ".$empresa."_000004 ";
		$query .= " where  plopro='".$exp[1]."' and plocod='".$exp[0]."' ";
		$errp = mysql_query($query,$conex);
		$nump = mysql_num_rows($errp);
		$rowp = mysql_fetch_array($errp);
		
		if($row2[2]!='')
		{
			//echo $cantidad;
			$puesto=bi($ins,count($ins),$row2[2],'cod');
			$ins[$puesto]['exi']=$ins[$puesto]['exi']+$row2[1]*$cantidad/$rowp[0];
		}
		else
		{
			$res=calcularProducto($row2[1]*$cantidad/$rowp[0],$row2[0], $ins, $concepto);
		}
	}

	if ($i>0)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function bi($d,$n,$k,$i)
{
	$n--;
	if($n > 0)
	{
		$li=0;
		$ls=$n;
		while ($ls - $li > 1)
		{
			$lm=(integer)(($li + $ls) / 2);
			if(strtoupper($k) == strtoupper($d[$lm][$i]))
			return $lm;
			elseif(strtoupper($k) < strtoupper($d[$lm][$i]))
			$ls=$lm;
			else
			$li=$lm;
		}
		if(strtoupper($k) == strtoupper($d[$li][$i]))
		return $li;
		elseif(strtoupper($k) == strtoupper($d[$ls][$i]))
		return $ls;
		else
		return -1;
	}
	else
	return -1;
}

/**
 * Se inserta la información de la ejecución del cron en la tabla de logs
 * 
 * @author	Sebastián Nevado
 * @since	2021-12-01
 */
function insertarLogCron($sFuncion, $sDescripcion)
{
	global $conex;

	//Consulta para guartar en la tabla de log
	$sQuery = "INSERT INTO root_000118 (Medico, Fecha_data, Hora_data, 
	Logfun, Logdes, Logfue, Loghue, 
	Logema, Logest, Seguridad)
	VALUES ('root', ?, ?, 
	?, ?, ?, ?,
	'on', 'on', 'C-root')";

	//Preparo y envío los parámetros
	$sentencia = mysqli_prepare($conex, $sQuery);
	mysqli_stmt_bind_param($sentencia, "ssssss", date("Y-m-d"), date("H:i:s"), $sFuncion, $sDescripcion, date("Y-m-d"), date("H:i:s"));

	//Guardo en BD
	$bResultado = (mysqli_stmt_execute($sentencia)) ? true : false;

	return $bResultado;
}

///////////////////////////////////////////////////////PROGRAMA/////////////////////////////////////////

	echo "<form name='saldos' action='Saldos.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	$wmes=date('m')-1;
	if($wmes==0)
	{
		$wmes=12;
		$wano=date('Y')-1;
	}
	else 
	{
		$wano=date('Y');
	}
	
	$sFuncion = "Cron Saldos - Inicio ".date("Y-m-d")." ".(string)date("H:i:s");
	$sDescripcion = 'Inicia la ejecución del cron de Saldos '.date("Y-m-d")." ".(string)date("H:i:s");
	insertarLogCron($sFuncion, $sDescripcion);
	/**
     * Se agrega la captura para excepciones en mysqli y el try catch
     * @by: sebastian.nevado
     * @date: 2021/12/02
     */
	$driver = new mysqli_driver();
    $driver->report_mode = MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ERROR;
	try {
		if(!isset($wano) or !isset($wmes))
		{
			$wlinant="";
			echo "<input type='HIDDEN' name= 'wlinant' value='".$wlinant."'>";
			echo "<center><table border=0>";
			echo "<tr><td class='texto4'>Año de Proceso</td>";
			echo "<td class='texto4'><input type='TEXT' name='wano' value='".date('Y')."' size=4 maxlength=4 readonly=readonly></td></tr>";
			echo "<tr><td class='texto4'>Mes de Proceso</td>";
			echo "<td class='texto4'><input type='TEXT' name='wmes' value='".date('m')."' size=2 maxlength=2 readonly=readonly></td></tr>";
			echo "<tr><td class='texto4' colspan=2 ><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$sFuncion = "Cron Saldos - Inicio eliminación - ".date("Y-m-d")." ".date("H:i:s");
			$sDescripcion = "Se inicia proceso de eliminación de registros en ".$empresa."_000014.";
			insertarLogCron($sFuncion, $sDescripcion);
			
			$query = "delete  from ".$empresa."_000014 ";
			$query .= "  where Salano = ".$wano;
			$query .= "       and Salmes = ".$wmes;
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());

			$sFuncion = "Cron Saldos - Fin eliminación - ".date("Y-m-d")." ".date("H:i:s");
			$sDescripcion = "Se finaliza proceso de eliminación de registros en ".$empresa."_000014.";
			insertarLogCron($sFuncion, $sDescripcion);

			echo "<table border=0 align=center>";
			echo "<tr><td class='texto4'><font face='tahoma'><b>AÑO DE PROCESO : </b>".$wano."</td></tr>";
			echo "<tr><td class='texto4'><font face='tahoma'><b>MES DE PROCESO : </b>".$wmes."</td></tr></table><br><br>";
			$ins=array();
			//consultamos en la tabla de saldos de insumos, las cantidades
			$query = "SELECT  Appcco, Apppre, Appexi, Appcnv, Appcos, Appcod, Appest  from ".$empresa."_000009 order by Apppre ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);

			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$ins[$i]['cco']=$row[0];
				$ins[$i]['cod']=$row[1];
				$ins[$i]['exi']=$row[2];
				$ins[$i]['cnv']=$row[3];
				$ins[$i]['cos']=$row[4];
				$ins[$i]['ins']=$row[5];
				$ins[$i]['est']=$row[6];
			}

			//consultamos en la tabla de lotes, los productos con saldo
			$query = "SELECT plocod, plopro, plosal from ".$empresa."_000004 where plosal>0 and ploest='on' ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				//consultamos el movimiento de fabricación del lotes
				$query = "SELECT concod from   ".$empresa."_000008 ";
				$query .= " where  conind='-1' and congas='on' ";

				$err2 = mysql_query($query,$conex);
				$row2 = mysql_fetch_array($err2);

				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					//consultamos los valores para productos codificados
					//para esto hay que desglosarlo primero en insumos
					$res=calcularProducto($row[2],$row[0].'-'.$row[1], $ins, $row2[0]);
				}
			}

			echo "<b>Registros Totales Kardex : ".count($ins)."</b><br><br>";
			$sFuncion = "Cron Saldos - Registros Totales Kardex - ".date("Y-m-d")." ".date("H:i:s");
			$sDescripcion = "Registros Totales Kardex a procesar : ".count($ins);
			insertarLogCron($sFuncion, $sDescripcion);

			if(count($ins)>0)
			{
				$contador=0;
				for ($i=0;$i<count($ins);$i++)
				{
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$query = "insert ".$empresa."_000014 (medico,fecha_data, hora_data, Salano, Salmes, Salcod,             Salcco,               Salexi,               Salpro,            Salvuc,               Salmax, Salmin, Salpor, Salfuc, Salins, Salest, seguridad) ";
					$query .= "values ('".$empresa."','".$fecha."','".$hora."', ".$wano.",".$wmes.",'".$ins[$i]['cod']."','".$ins[$i]['cco']."','".$ins[$i]['exi']."','".$ins[$i]['cnv']."','".$ins[$i]['cos']."', 0,      0,    0,  '".date('Y-m-d')."', '".$ins[$i]['ins']."','".$ins[$i]['est']."', 'C-".$empresa."')";
					$err3 = mysql_query($query,$conex);
					if ($err3 != 1)
					echo mysql_errno().":".mysql_error()."<br>";
					else
					{
						$contador++;
						echo "REGISTRO INSERTADO NRo : ".$contador."<br>";
						$sFuncion = "Cron Saldos - Registro ".$contador." ".date("Y-m-d")." ".date("H:i:s");
						$sDescripcion = "Registro nro : ".$contador." insertado exitosamente ".date("Y-m-d")." ".date("H:i:s");
						insertarLogCron($sFuncion, $sDescripcion);
					}
				}
				echo "<b>TOTAL REGISTROS INSERTADOS  : </b>".$contador."<br>";
				
				$sFuncion = "Cron Saldos - Fin exitoso ".date("Y-m-d")." ".date("H:i:s");
				$sDescripcion = 'Cron de saldos ejecutado exitosamente. Total registros insertados: '.$contador;
				insertarLogCron($sFuncion, $sDescripcion);
			}
			else
			{
				echo "<br><br><center><table border=0 aling=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>AÑO -- MES  : YA FUE GENERADO  !!!!</MARQUEE></FONT>";
				echo "<input type='submit' value='Continuar'></center>";
				echo "<br><br>";

				$sFuncion = "Cron Saldos - Fin sin inserción".date("Y-m-d")." ".date("H:i:s");
				$sDescripcion = "No se generó error, pero se detectó que Año {$wano} - Mes {$wmes} ya fue generado. No se almacenó ningún registro.";
				insertarLogCron($sFuncion, $sDescripcion);
			}
		}
	} catch (Exception $e) {
		echo "<b>Cron Saldos - Fin con error ".date("Y-m-d")." ".date("H:i:s")."</b><br> Error: ".$e->getMessage()."<br><br>";
		$sFuncion = "Cron Saldos - Fin con error ".date("Y-m-d")." ".date("H:i:s");
		$sDescripcion = 'El error fue: '.$e->getMessage();
		insertarLogCron($sFuncion, $sDescripcion);
    }

?>
</body>
</html>