<html>
<head>
  	<title>MATRIX PLANILLA DE INVENTARIO</title>
  	
<style type="text/css">
table.sample {
	border-width: 1px;
	border-style: solid;
	border-color: gray;
	border-collapse: separate;
}
table.sample td {
	border-width: 1px;
	border-style: solid;
	border-color: gray;
}

</style>


   <script type="text/javascript">
   function cambiar(i)
   {
   	document.planilla.ajustar.value=0;
   	document.planilla.submit();
   }
   </script>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>AJUSTE AUTOMATICO DE INVENTARIO CENTRAL DE MEZCLAS</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b>planilla3.php Ver. 1.00</b></font></tr></td></table></br>
</center> 
<?php

/********************************************************************************************************************************
 * 
 * Actualización: 	2021-07-08 - sebastian.nevado: Se reemplaza el "C-cenpro" del campo Seguridad las inserciones en base de datos 
* 						para que indique el usuario que realiza la acción.
 * 
 ********************************************************************************************************************************/

include_once("conex.php");
include_once("root/comun.php");
// se convierte en la variable empresa ya que $empresa=cenpro
$empresa = consultarAliasPorAplicacion( $conex, $wemp_pmla, "cenmez" );
$bdMovhos  = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");

function calcularProducto($cantidad, $lote, $signo, $ano, $mes)
{
	global $conex;
	global $empresa;

	$query = "SELECT concod from   ".$empresa."_000008 ";
	$query .= " where  conind='-1' and congas='on' ";

	$err2 = mysql_query($query,$conex);
	$row2 = mysql_fetch_array($err2);
	$concepto=$row2[0];

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
			$q= "   UPDATE ".$empresa."_000014 "
			."      SET Salexi = Salexi+".(($row2[1]*$cantidad/$rowp[0])*$signo).""
			."    WHERE Salcod =  '".$row2[2]."' "
			."      AND Salano =".$ano." "
			."      AND Salmes =".$mes." ";

			$res1 = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO SUMAR UN INSUMO ".mysql_error());
		}
		else
		{
			$res=calcularProducto($row2[1]*$cantidad/$rowp[0],$row2[0], $signo, $ano, $mes);
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


function verificarProducto($cantidad, $lote, $signo, $ano, $mes)
{
	global $conex;
	global $empresa;

	$query = "SELECT concod from   ".$empresa."_000008 ";
	$query .= " where  conind='-1' and congas='on' ";

	$err2 = mysql_query($query,$conex);
	$row2 = mysql_fetch_array($err2);
	$concepto=$row2[0];

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
			$q= "   SELECT * FROM ".$empresa."_000014 "
			."      SET Salexi >= ".($row2[1]*$cantidad/$rowp[0]).""
			."    WHERE Salcod =  '".$row2[2]."' "
			."      AND Salano =".$ano." "
			."      AND Salmes =".$mes." ";

			$errv = mysql_query($query,$conex);
			$numv = mysql_num_rows($errv);
			if($numv<=0)
			{
				return false;
			}
		}
		else
		{
			$res=verificarProducto($row2[1]*$cantidad/$rowp[0],$row2[0], $signo, $ano, $mes);
		}
	}

	return true;

}
///////////////////////////////////////////////////////////////////////////////Programa//////////////////////////////////////

session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	echo "<form name='planilla' action='planilla3.php?wemp_pmla=".$wemp_pmla."' method=post>";
	echo "<input type='hidden' id='wemp_pmla' name='wemp_pmla' value='".$wemp_pmla."'>";
	//echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	

	if (!isset($fec))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha de inventario</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='fec' value='".date('Y-m-d')."' size=10 ></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha de ajuste</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='fec2' value='".date('Y-m-d')."' size=10 ></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='REALIZAR AJUSTE'></td></tr></table>";
		echo "<input type='HIDDEN' name= 'primera' value='1'>";
	}
	else
	{
		if (isset($primera))
		{
			$insumos='on';
			$productos='on';
			$ajustar=0;
		}

		echo "<table border=0 align=center>";

		echo "<tr><td bgcolor=#dddddd><font face='tahoma'><b>FECHA INVENTARIO: </b>".$fec."</td></tr>";
		echo "<tr><td bgcolor=#dddddd><font face='tahoma'><b>FECHA AJUSTE: </b>".$fec2."</td></tr>";

		//Buscamos los insumos que hay que ajustar
		$query = "SELECT Invcod, Artcom, Invcan, Artuni, Unides, Invcon, Appcnv, Appcod";
		$query .=" from ".$empresa."_000017, ".$bdMovhos."_000026, ".$bdMovhos."_000027, ".$empresa."_000009 ";
		$query .= " where  Invfec = '".$fec."' " ;
		$query .= "   and  Invpro = 'off' ";
		$query .= "   and  Invcod = Artcod ";
		$query .= "   and  Invaju <> 'on' ";
		$query .= "   and  (Invcan-Invcon) <> 0 ";
		$query .= "   and  Artuni = Unicod ";
		$query .= "   and  Apppre = Invcod ";
		$query .= "   and  Appest = 'on' ";
		$query .= "   ORDER BY 2 ";

		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);

		echo "</table></br><table align=center class='sample' cellspacing=0>";

		$colspan='6';

		echo "<tr><td align=center bgcolor=#999999 colspan='".$colspan."'><font face='tahoma' size=2><b>INSUMOS</b></font></td>";
		echo "<tr><td align=center bgcolor=#999999><font face='tahoma' size=1><b>ARTICULO</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=1><b>UNIDAD</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=1><b>CANTIDAD AJUSTE UNIDAD MAXIMA</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=1><b>CONVERSION</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=1><b>CANTIDAD AJUSTE UNIDAD MINIMA</b></font></td>";
		if(isset($insumos))
		{
			echo "<td bgcolor='red' align='right' ><font face='tahoma' size=2><input type='checkbox' name='insumos' checked value='SI' onclick='cambiar()'></font></td></tr>";
		}
		else
		{
			echo "<td bgcolor='red' align='right' ><font face='tahoma' size=2><input type='checkbox' name='insumos'  value='SI' onclick='cambiar()'></font></td></tr>";
		}

		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);

			if(is_int($i/2))
			{
				$color='#FFFFFF';
			}
			else
			{
				$color="#dddddd";
			}

			echo "<tr>";
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[0]."-".$row[1]."</font></td>";
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[3]."-".$row[4]."</font></td>";

			$dif=$row[2]-$row[5];

			echo "<td bgcolor=".$color." align='right'><font face='tahoma' size=2>".number_format($dif,2,'.',',')."</font></td>";


			if($dif>0)
			{
				if(isset($grabar[$i]) and isset($ajustar) and $ajustar==1)
				{
					//Busco si existe el producto
					$q= "   SELECT * from ".$empresa."_000009 "
					."    WHERE Apppre = '".$row[0]."'"
					."      AND Appest = 'on' "
					."      AND Appexi >= ".round($dif*$row[6],2)." ";

					$res2 = mysql_query($q,$conex);
					$num2 = mysql_num_rows($res2);

					if($num2>0)
					{
						$exp=explode('-',$fec2);
						if($exp[1]<date('m'))
						{
							$q= "   SELECT * from ".$empresa."_000014 "
							."    WHERE Salcod =  '".$row[0]."' "
							."      AND Salano =".$exp[0]." "
							."      AND Salmes =".$exp[1]." "
							."      AND Salexi >=".($dif*$row[6])." ";

							$res2 = mysql_query($q,$conex);
							$num2 = mysql_num_rows($res2);
						}
					}

					if($num2>0)
					{

						$q = "lock table ".$empresa."_000008 LOW_PRIORITY WRITE";
						$errlock = mysql_query($q,$conex);

						$q= "   UPDATE ".$empresa."_000008 "
						."      SET Concon = (Concon + 1) "
						."    WHERE Conind = '-1'"
						."      AND Conaju = 'on' "
						."      AND Conest = 'on' ";

						$res1 = mysql_query($q,$conex);

						$q= "   SELECT Concon, Concod from ".$empresa."_000008 "
						."    WHERE Conind = '-1'"
						."      AND Conaju = 'on' "
						."      AND Conest = 'on' ";

						$res1 = mysql_query($q,$conex);
						$row2 = mysql_fetch_array($res1);
						$codigo=$row2[1];
						$consecutivo=$row2[0];

						$q = " UNLOCK TABLES";   //SE DESBLOQUEA LA TABLA DE FUENTES
						$errunlock = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

						$q= "   UPDATE ".$empresa."_000005 "
						."      SET karexi = karexi - ".($dif*$row[6])." "
						."    WHERE Karcod = '".$row[7]."' ";

						$res1 = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO DESCONTAR UN INSUMO ".mysql_error());


						$q= "   UPDATE ".$empresa."_000009 "
						."      SET Appexi = Appexi-".($dif*$row[6]).""
						."    WHERE Apppre =  '".$row[0]."' "
						."      AND Appest ='on' ";

						$res1 = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO DESCONTAR UN INSUMO ".mysql_error());

						$exp=explode('-',$fec2);
						if($exp[1]<date('m'))
						{
							//si el mes del inventario es menor, se deben modificar tambien los saldos
							$q= "   UPDATE ".$empresa."_000014 "
							."      SET Salexi = Salexi-".($dif*$row[6]).""
							."    WHERE Salcod =  '".$row[0]."' "
							."      AND Salano =".$exp[0]." "
							."      AND Salmes =".$exp[1]." ";

							$res1 = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO DESCONTAR UN INSUMO DE LA TABLA DE SALDOS".mysql_error());
						}
					}
				}
				echo "<td bgcolor=".$color." align='right' ><font face='tahoma' size=2>".number_format(($row[6]),2,'.',',')."</font></td>";
				echo "<td bgcolor=".$color." align='right' ><font face='tahoma' size=2>".number_format(($dif*$row[6]),2,'.',',')."</font></td>";
			}
			else
			{
				$dif=abs($dif);
				if(isset($grabar[$i]) and isset($ajustar) and $ajustar==1)
				{
					//Busco si existe el producto
					$q= "   SELECT * from ".$empresa."_000009 "
					."    WHERE Apppre = '".$row[0]."'"
					."      AND Appest = 'on' ";

					$res2 = mysql_query($q,$conex);
					$num2 = mysql_num_rows($res2);

					if($num2>0)
					{
						$q = "lock table ".$empresa."_000008 LOW_PRIORITY WRITE";
						$errlock = mysql_query($q,$conex);

						$q= "   UPDATE ".$empresa."_000008 "
						."      SET Concon = (Concon + 1) "
						."    WHERE Conind = '1'"
						."      AND Conaju = 'on' "
						."      AND Conest = 'on' ";

						$res1 = mysql_query($q,$conex);

						$q= "   SELECT Concon, Concod from ".$empresa."_000008 "
						."    WHERE Conind = '1'"
						."      AND Conaju = 'on' "
						."      AND Conest = 'on' ";

						$res1 = mysql_query($q,$conex);
						$row2 = mysql_fetch_array($res1);
						$codigo=$row2[1];
						$consecutivo=$row2[0];

						$q = " UNLOCK TABLES";   //SE DESBLOQUEA LA TABLA DE FUENTES
						$errunlock = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());


						$q= "   UPDATE ".$empresa."_000005 "
						."      SET karexi = karexi + ".($dif*$row[6])." "
						."    WHERE Karcod = '".$row[7]."' ";

						$res1 = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO DESCONTAR UN INSUMO ".mysql_error());

						$q= "   UPDATE ".$empresa."_000009 "
						."      SET Appexi = Appexi+".($dif*$row[6]).""
						."    WHERE Apppre =  '".$row[0]."' "
						."      AND Appest ='on' ";

						$res1 = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO DESCONTAR UN INSUMO ".mysql_error());

						$exp=explode('-',$fec2);
						if($exp[1]<date('m'))
						{
							//si el mes del inventario es menor, se deben modificar tambien los saldos
							$q= "   UPDATE ".$empresa."_000014 "
							."      SET Salexi = Salexi+".($dif*$row[6]).""
							."    WHERE Salcod =  '".$row[0]."' "
							."      AND Salano =".$exp[0]." "
							."      AND Salmes =".$exp[1]." ";

							$res1 = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO DESCONTAR UN INSUMO DE LA TABLA DE SALDOS".mysql_error());
						}
					}
				}
				echo "<td bgcolor=".$color." align='right' ><font face='tahoma' size=2>".number_format(($row[6]),2,'.',',')."</font></td>";
				echo "<td bgcolor=".$color." align='right' ><font face='tahoma' size=2>".number_format(($dif*$row[6]*-1),2,'.',',')."</font></td>";
			}

			if(isset($grabar[$i]) and isset($ajustar) and $ajustar==1)
			{
				if($num2>0)
				{
					$q= " INSERT INTO ".$empresa."_000006 (   Medico       ,   Fecha_data,                  Hora_data,              Menano,              Menmes ,     Mendoc   ,   Mencon  ,             Menfec,           Mencco ,   Menccd    ,  Mendan,  Menusu,    Menfac,  Menest, Seguridad) "
					."                               VALUES ('".$empresa."',  '".$fec2."', '".(string)date("H:i:s")."', '".$exp[0]."', '".$exp[1]."','".$consecutivo."', '".$codigo."' , '".$fec2."', '1051' , '1051' ,       '', 'cenpro',      '' , 'on', 'C-".$usera."') ";


					$errf = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO GRABAR EL ENCABEZADO DEL MOVIIENTO DE SALIDA DE INSUMOS ".mysql_error());

					$q= " INSERT INTO ".$empresa."_000007 (   Medico       ,   Fecha_data,                  Hora_data,              Mdecon,              Mdedoc ,     Mdeart   ,    Mdecan , Mdefve,   Mdenlo,          Mdepre, Mdeest,  Seguridad) "
					."                               VALUES ('".$empresa."',  '".$fec2."', '".(string)date("H:i:s")."', '".$codigo."', '".$consecutivo."','".$row[7]."', '".($dif*$row[6])."' ,      '',     '',  '".$row[0]."-".$row[1]."' , 'on', 'C-".$usera."') ";


					$errf = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO GRABAR EL DETALLE DE SALIDA DE UN ARTICULO ".mysql_error());

					$q = " UPDATE " . $empresa . "_000017 "
					. "    SET invaju='on', invdoc='".$codigo."-".$consecutivo."' "
					. "  WHERE invcod='".$row[0]."' "
					. "  and invfec='".$fec."' ";
					$erf = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

					echo "<td bgcolor=".$color." align='right' ><font face='tahoma' size=2>SI</font></td>";


				}
				else
				{
					echo "<td bgcolor=".$color." align='right' ><font face='tahoma' size=2>NO</font></td>";
				}
			}
			else if(isset($ajustar) and $ajustar==1)
			{
				echo "<td bgcolor=".$color." align='right' ><font face='tahoma' size=2>&nbsp</font></td>";
			}
			else if(isset($insumos))
			{
				echo "<td bgcolor=".$color." align='right' ><font face='tahoma' size=2><input type='checkbox' name='grabar[".$i."]' checked value='SI'></font></td>";
			}
			else
			{
				echo "<td bgcolor=".$color." align='right' ><font face='tahoma' size=2><input type='checkbox' name='grabar[".$i."]' value='SI'></font></td>";
			}
			echo "</tr>";
		}

		//Buscamos los productos
		$query = "SELECT Invcod, Artcom, Invcan, Artuni, Unides, Invcon, 1 as karcos ";
		$query .=" from ".$empresa."_000002, ".$empresa."_000017, ".$bdMovhos."_000027, ".$empresa."_000005 ";
		$query .= " where  Invfec = '".$fec."' " ;
		$query .= "   and  Invpro = 'on' ";
		$query .= "   and  Invaju <> 'on' ";
		$query .= "   and  (Invcan-Invcon) <> 0 ";
		$query .= "   and  Invcod = Artcod ";
		$query .= "   and  Artuni = Unicod ";
		$query .= "   and  Karcod = Artcod";
		$query .= "   ORDER BY 2 ";

		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);

		echo "</table></br><table class='sample' align=center cellspacing=0> ";
		echo "<tr><td align=center bgcolor=#999999 colspan='4'><font face='tahoma' size=2><b>PRODUCTOS</b></font></td>";
		echo "<tr><td align=center bgcolor=#999999><font face='tahoma' size=1><b>ARTICULO</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=1><b>UNIDAD</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=1><b>CANTIDAD AJUSTE</b></font></td>";

		if(isset($productos))
		{
			echo "<td bgcolor='red' align='right' ><font face='tahoma' size=2><input type='checkbox' name='productos' checked value='SI' onclick='cambiar()'></font></td></tr>";
		}
		else
		{
			echo "<td bgcolor='red' align='right' ><font face='tahoma' size=2><input type='checkbox' name='productos'  value='SI' onclick='cambiar()'></font></td></tr>";
		}

		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);

			if(is_int($i/2))
			{
				$color='#FFFFFF';
			}
			else
			{
				$color="#dddddd";
			}

			echo "<tr>";
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[0]."-".$row[1]."</font></td>";
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[3]."-".$row[4]."</font></td>";

			$dif=$row[2]-$row[5];

			echo "<td bgcolor=".$color." align='right'><font face='tahoma' size=2>".number_format($dif,2,'.',',')."</font></td>";

			if($dif>=0)
			{
				if(isset($grabar2[$i]) and isset($ajustar) and $ajustar==1)
				{
					//miro si existe saldo de ese articulo en el lote mas viejo para descontar
					$q= "   SELECT sum(Plosal) from ".$empresa."_000004 "
					."    WHERE Plopro = '".$row[0]."'"
					."      AND Ploest = 'on' ";

					$res2 = mysql_query($q,$conex);
					$num2 = mysql_num_rows($res2);
					if($num2>0)
					{
						$row2 = mysql_fetch_array($res2);
						if($row2[0]>=$dif)
						{
							$num2=1;
						}
						else
						{
							$num2=0;
						}

					}

					//verificamos que si haya de cada insumo del producto para realizar el ajuste (todo o nada)
					if($num2>0)
					{
						$exp=explode('-',$fec2);
						if($exp[1]<date('m'))
						{
							//consultamos los lotes con saldo de atras pa delante
							$q= "   SELECT Plocod, Plosal from ".$empresa."_000004 "
							."    WHERE Plopro = '".$row[0]."'"
							."      AND Ploest = 'on' "
							."      AND Plosal > 0 "
							."      ORDER BY Plofcr asc ";

							$resv = mysql_query($q,$conex);
							$numv = mysql_num_rows($resv);

							$dif2=$dif;
							for ($j=0;$j<$numv;$j++)
							{
								$rowv = mysql_fetch_array($resv);

								if($rowv[1]<$dif2)
								{
									$des=$rowv[1];
									$dif2=$dif2-$des;
								}
								else
								{
									$des=$dif2;
									$dif2=0;
								}

								if($dif2<=0)
								{
									$j=$numv+1;
								}

								$verificacion=verificarProducto($des, $rowv[0]."-".$rowv[0], -1, $exp[0], $exp[1]);
								if(!$verificacion)
								{
									$num2=0;
								}
							}

						}
					}

					if($num2>0)
					{
						$q = "lock table ".$empresa."_000008 LOW_PRIORITY WRITE";
						$errlock = mysql_query($q,$conex);

						$q= "   UPDATE ".$empresa."_000008 "
						."      SET Concon = (Concon + 1) "
						."    WHERE Conind = '-1'"
						."      AND Conaju = 'on' "
						."      AND Conest = 'on' ";

						$res1 = mysql_query($q,$conex);

						$q= "   SELECT Concon, Concod from ".$empresa."_000008 "
						."    WHERE Conind = '-1'"
						."      AND Conaju = 'on' "
						."      AND Conest = 'on' ";

						$res1 = mysql_query($q,$conex);
						$row2 = mysql_fetch_array($res1);
						$codigo=$row2[1];
						$consecutivo=$row2[0];

						$q = " UNLOCK TABLES";   //SE DESBLOQUEA LA TABLA DE FUENTES
						$errunlock = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

						$exp=explode('-',$fec2);

						$q= " INSERT INTO ".$empresa."_000006 (   Medico       ,   Fecha_data,                  Hora_data,              Menano,              Menmes ,     Mendoc   ,   Mencon  ,             Menfec,           Mencco ,   Menccd    ,  Mendan,  Menusu,    Menfac,  Menest, Seguridad) "
						."                               VALUES ('".$empresa."',  '".$fec2."', '".(string)date("H:i:s")."', '".$exp[0]."', '".$exp[1]."','".$consecutivo."', '".$codigo."' , '".$fec2."', '1051' , '1051' ,       '', 'cenpro',      '' , 'on', 'C-".$usera."') ";


						$errf = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO GRABAR EL ENCABEZADO DEL MOVIIENTO DE SALIDA DE INSUMOS ".mysql_error());

						//consultamos los lotes con saldo de atras pa delante
						$q= "   SELECT Plocod, Plosal from ".$empresa."_000004 "
						."    WHERE Plopro = '".$row[0]."'"
						."      AND Ploest = 'on' "
						."      AND Plosal > 0 "
						."      ORDER BY Plofcr asc ";

						$res2 = mysql_query($q,$conex);
						$num2 = mysql_num_rows($res2);

						$dif2=$dif;
						for ($j=0;$j<$num2;$j++)
						{
							$row2 = mysql_fetch_array($res2);


							if($row2[1]<$dif2)
							{
								$des=$row2[1];
								$dif2=$dif2-$des;
							}
							else
							{
								$des=$dif2;
								$dif2=0;
							}

							if($dif2<=0)
							{
								$j=$num2+1;
							}

							$q= "   UPDATE ".$empresa."_000004 "
							."      SET Plosal = Plosal-".$des.""
							."    WHERE Plopro =  '".$row[0]."' "
							."      AND Plocod ='".$row2[0]."' "
							."      AND Ploest ='on' ";

							$res1 = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO DESCONTAR UN INSUMO ".mysql_error());

							$q= "   UPDATE ".$empresa."_000005 "
							."      SET karexi = karexi - ".$des." "
							."    WHERE Karcod = '".$row[0]."' ";

							$res1 = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO DESCONTAR UN INSUMO ".mysql_error());

							if($exp[1]<date('m'))
							{
								calcularProducto($des, $row2[0]."-".$row[0], -1, $exp[0], $exp[1]);
							}

							$q= " INSERT INTO ".$empresa."_000007 (   Medico       ,   Fecha_data,                  Hora_data,              Mdecon,              Mdedoc ,     Mdeart   ,    Mdecan , Mdefve,   Mdenlo,          Mdepre, Mdeest,  Seguridad) "
							."                               VALUES ('".$empresa."',  '".$fec2."', '".(string)date("H:i:s")."', '".$codigo."', '".$consecutivo."','".$row[0]."-".$row[1]."', '".$des."' ,      '',     '".$row2[0]."-".$row[0]."',  '' , 'on', 'C-".$usera."') ";


							$errf = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO GRABAR EL DETALLE DE SALIDA DE UN ARTICULO ".mysql_error());
						}

					}
				}
			}
			else
			{
				$dif=abs($dif);

				if(isset($grabar2[$i]) and isset($ajustar) and $ajustar==1)
				{
					//miro si existe cantidad para umentar a los lotes
					$q= "   SELECT sum(Plocin-Plosal) from ".$empresa."_000004 "
					."    WHERE Plopro = '".$row[0]."'"
					."      AND Ploest = 'on' ";

					$res2 = mysql_query($q,$conex);
					$num2 = mysql_num_rows($res2);
					if($num2>0)
					{
						$row2 = mysql_fetch_array($res2);
						if($row2[0]>=$dif)
						{
							$num2=1;
						}
						else
						{
							$num2=0;
						}

					}

					if($num2>0)
					{
						$q = "lock table ".$empresa."_000008 LOW_PRIORITY WRITE";
						$errlock = mysql_query($q,$conex);

						$q= "   UPDATE ".$empresa."_000008 "
						."      SET Concon = (Concon + 1) "
						."    WHERE Conind = '1'"
						."      AND Conaju = 'on' "
						."      AND Conest = 'on' ";

						$res1 = mysql_query($q,$conex);

						$q= "   SELECT Concon, Concod from ".$empresa."_000008 "
						."    WHERE Conind = '1'"
						."      AND Conaju = 'on' "
						."      AND Conest = 'on' ";

						$res1 = mysql_query($q,$conex);
						$row2 = mysql_fetch_array($res1);
						$codigo=$row2[1];
						$consecutivo=$row2[0];

						$q = " UNLOCK TABLES";   //SE DESBLOQUEA LA TABLA DE FUENTES
						$errunlock = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

						$exp=explode('-',$fec2);

						$q= " INSERT INTO ".$empresa."_000006 (   Medico       ,   Fecha_data,                  Hora_data,              Menano,              Menmes ,     Mendoc   ,   Mencon  ,             Menfec,           Mencco ,   Menccd    ,  Mendan,  Menusu,    Menfac,  Menest, Seguridad) "
						."                               VALUES ('".$empresa."',  '".$fec2."', '".(string)date("H:i:s")."', '".$exp[0]."', '".$exp[1]."','".$consecutivo."', '".$codigo."' , '".$fec2."', '1051' , '1051' ,       '', 'cenpro',      '' , 'on', 'C-".$usera."') ";


						$errf = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO GRABAR EL ENCABEZADO DEL MOVIIENTO DE SALIDA DE INSUMOS ".mysql_error());

						//consultamos los lotes con saldo de atras pa delante
						$q= "   SELECT Plocod, (Plocin-plosal) from ".$empresa."_000004 "
						."    WHERE Plopro = '".$row[0]."'"
						."      AND Ploest = 'on' "
						."      AND (Plocin-Plosal) > 0 "
						."      ORDER BY Plofcr asc ";

						$res2 = mysql_query($q,$conex);
						$num2 = mysql_num_rows($res2);

						$dif2=$dif;
						for ($j=0;$j<$num2;$j++)
						{
							$row2 = mysql_fetch_array($res2);


							if($row2[1]<$dif2)
							{
								$des=$row2[1];
								$dif2=$dif2-$des;
							}
							else
							{
								$des=$dif2;
								$dif2=0;
							}

							if($dif2<=0)
							{
								$j=$num2+1;
							}

							$q= "   UPDATE ".$empresa."_000004 "
							."      SET Plosal = Plosal+".$des.""
							."    WHERE Plopro =  '".$row[0]."' "
							."      AND Plocod ='".$row2[0]."' "
							."      AND Ploest ='on' ";

							$res1 = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO DESCONTAR UN INSUMO ".mysql_error());

							$q= "   UPDATE ".$empresa."_000005 "
							."      SET karexi = karexi + ".$des." "
							."    WHERE Karcod = '".$row[0]."' ";

							$res1 = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO DESCONTAR UN INSUMO ".mysql_error());

							if($exp[1]<date('m'))
							{
								calcularProducto($des, $row2[0]."-".$row[0], 1, $exp[0], $exp[1]);
							}

							$q= " INSERT INTO ".$empresa."_000007 (   Medico       ,   Fecha_data,                  Hora_data,              Mdecon,              Mdedoc ,     Mdeart   ,    Mdecan , Mdefve,   Mdenlo,          Mdepre, Mdeest,  Seguridad) "
							."                               VALUES ('".$empresa."',  '".$fec2."', '".(string)date("H:i:s")."', '".$codigo."', '".$consecutivo."','".$row[0]."-".$row[1]."', '".$des."' ,      '',     '".$row2[0]."-".$row[0]."',  '' , 'on', 'C-".$usera."') ";


							$errf = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO GRABAR EL DETALLE DE SALIDA DE UN ARTICULO ".mysql_error());
						}
					}
				}
			}

			if(isset($grabar2[$i]) and isset($ajustar) and $ajustar==1)
			{
				if($num2>0)
				{
					echo "<td bgcolor=".$color." align='right' ><font face='tahoma' size=2>SI</font></td>";

					$q = " UPDATE " . $empresa . "_000017 "
					. "    SET invaju='on', invdoc='".$codigo."-".$consecutivo."' "
					. "  WHERE invcod='".$row[0]."' "
					. "  and invfec='".$fec."' ";
					$errf = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
				}
				else
				{
					echo "<td bgcolor=".$color." align='right' ><font face='tahoma' size=2>NO</font></td>";
				}
			}
			else if(isset($ajustar) and $ajustar==1)
			{
				echo "<td bgcolor=".$color." align='right' ><font face='tahoma' size=2>&nbsp</font></td>";
			}
			else if(isset($productos))
			{
				echo "<td bgcolor=".$color." align='right' ><font face='tahoma' size=2><input type='checkbox' name='grabar2[".$i."]' checked value='SI'></font></td>";
			}
			else
			{
				echo "<td bgcolor=".$color." align='right' ><font face='tahoma' size=2><input type='checkbox' name='grabar2[".$i."]' value='SI'></font></td>";
			}
			echo "</tr>";
		}

		echo "<input type='HIDDEN' name= 'ajustar' value=1>";
		echo "<input type='HIDDEN' name= 'fec' value='".$fec."'>";
		echo "<input type='HIDDEN' name= 'fec2' value='".$fec2."'>";
		echo "</table>";

		echo "<table align='center'>";
		echo "<tr><td align='center'>&nbsp;</td></tr>";
		echo "<tr><td align='center'><input type='submit' value='REALIZAR AJUSTE'></td></tr></table>";
	}
}
?>
</body>
</html>