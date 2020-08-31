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
		$query = "delete  from ".$empresa."_000014 ";
		$query .= "  where Salano = ".$wano;
		$query .= "       and Salmes = ".$wmes;
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());

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
				}
			}
			echo "<b>TOTAL REGISTROS INSERTADOS  : </b>".$contador."<br>";
		}
		else
		{
			echo "<br><br><center><table border=0 aling=center>";
			echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table>";
			echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>AÑO -- MES  : YA FUE GENERADO  !!!!</MARQUEE></FONT>";
			echo "<input type='submit' value='Continuar'></center>";
			echo "<br><br>";
		}
	}

?>
</body>
</html>