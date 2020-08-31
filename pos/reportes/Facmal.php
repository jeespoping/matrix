<html>
<head>
<title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

	echo "<form action='facmal.php' method=post>";

	$wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
	$wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
	$wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
	$wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro

	$wfecha=date("Y-m-d");

	echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";

	echo "<center><table border=2>";
	echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=340 HEIGHT=100></td></tr>";
	echo "<tr><td align=center bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>REPORTE DE VENTAS (ERRONEAS)</b></font></td></tr>";

	if (!isset($wfecini) or !isset($wfecfin) or !isset($wcco))
	{
		echo "<tr>";
		echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Fecha Inicial (AAAA-MM-DD): </font></b><INPUT TYPE='text' NAME='wfecini' value=".$wfecha." SIZE=10></td>";
		echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Fecha Final (AAAA-MM-DD): </font></b><INPUT TYPE='text' NAME='wfecfin' value=".$wfecha." SIZE=10></td>";
		echo "</tr>";

		//CENTRO DE COSTO
		$q =  " SELECT ccocod, ccodes "
		."   FROM ".$wbasedato."_000003 "
		."  ORDER BY 1 ";

		$res = mysql_query($q,$conex);
		$num = mysql_num_rows($res);

		echo "<tr><td align=center colspan='2'  bgcolor=".$wcf." >SELECCIONE LA SUCURSAL: ";
		echo "<select name='wcco'>";
		//echo "<option>&nbsp</option>";
		for ($i=1;$i<=$num;$i++)
		{
			$row = mysql_fetch_array($res);
			echo "<option>".$row[0]."-".$row[1]."</option>";
		}
		echo "</select></td></tr>";


		//SELECCIONAR TARIFA
		$q =  " SELECT tarcod, tardes "
		."   FROM ".$wbasedato."_000025 "
		."  ORDER BY 1 ";

		$res = mysql_query($q,$conex);
		$num = mysql_num_rows($res);

		/*	echo "<td align=center bgcolor=".$wcf." >SELECCIONE LA TARIFA: ";
		echo "<select name='wtar'>";
		//echo "<option>&nbsp</option>";
		for ($i=1;$i<=$num;$i++)
		{
		$row = mysql_fetch_array($res);
		echo "<option>".$row[0]."-".$row[1]."</option>";
		}
		echo "</select></td></tr>";*/

		echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";

		echo "<tr>";
		echo "<td align=center bgcolor=#cccccc colspan=2><input type='submit' value='OK'></td>";                                         //submit
		echo "</tr>";
	}
	else
	{
		//ajusto las fechas para establecer los rangos antes y depues del cambio en la forma de almacenamiento del sistema
		$fechaCorte='2009-01-26';
		$rango1=0;
		$rango2=0;
		if ($wfecfin<$fechaCorte)
		{
			$rango1=1;
		}
		if ($wfecini>=$fechaCorte)
		{
			$rango2=1;
			$wfecini2=$wfecini;
			$wfecfin2=$wfecfin;
		}
		if ($wfecfin>=$fechaCorte and $wfecini<$fechaCorte)
		{
			$rango1=1;
			$rango2=1;
			$wfecini2=$fechaCorte;
			$wfecfin2=$wfecfin;
			$wfecfin='2009-01-25';
		}

		//busco el consecutivo de devolucion
		//busco el codigo para el movimiento de venta
		$q="Select concod "
		."FROM ".$wbasedato."_000008 "
		."WHERE	conmve	= 'on' "
		."and	conest	= 'on' ";
		$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR EL CODIGO DEL MOVIMIENTO DE INVENTARIO EN VENTAS ".mysql_error());
		$num = mysql_num_rows($err) or die (mysql_errno()." -NO SE HA ENCONTRADO EL CODIGO DEL MOVIMIENTO DE INVENTARIO EN VENTAS  ".mysql_error());
		$row=mysql_fetch_array($err);
		$movven=$row[0];

		//busco el codigo para el movimiento de devolucion
		$q="Select concod "
		."FROM ".$wbasedato."_000008 "
		."WHERE	concan	= '".$movven."' "
		."and	conest	= 'on' ";
		$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR EL CODIGO DEL MOVIMIENTO DE INVENTARIO EN DEVOLUCIONES".mysql_error());
		$num = mysql_num_rows($err) or die (mysql_errno()." -NO SE HA ENCONTRADO EL CODIGO DEL MOVIMIENTO DE INVENTARIO EN DEVOLUCIONES  ".mysql_error());
		$row=mysql_fetch_array($err);
		$movdev=$row[0];


		echo "<input type='HIDDEN' NAME= 'wcco' value='".$wcco."'>";
		$wccoe = explode("-",$wcco);

		/*echo "<input type='HIDDEN' NAME= 'wtar' value='".$wtar."'>";
		$wtare = explode("-",$wtar);*/

		echo "<tr>";
		echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Fecha Inicial (AAAA-MM-DD): </font></b>".$wfecini."</td>";
		echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Fecha Final (AAAA-MM-DD): </font></b>".$wfecfin."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td bgcolor=".$wcf." align=center colspan=2><b><font text color=".$wclfg.">SUCURSAL: </font></b>".$wcco."</td>";
		echo "</tr>";


		//$hora = (string)date("H:i:s");
		//echo "1er Query Tiempo 1 : ".$hora."<br>";

		/////////////////////////////////////////////////////////////////
		// REPORTE PORVENTAS SIN FACTURA
		/////////////////////////////////////////////////////////////////
		echo "<br>";
		echo "<br>";
		echo "<br>";

		echo "<center><table border=2>";
		//echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/farmastore/logo farmastore.png' WIDTH=340 HEIGHT=100></td></tr>";
		echo "<tr><td align=center bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>REPORTE DE VENTAS POS ERRONEAS</b></font></td></tr>";

		$q = "  CREATE TEMPORARY TABLE if not exists tempo2 as "
		."  SELECT Vennum, venfec, venvto, vencon, vennmo  "
		."    FROM ".$wbasedato."_000016 "
		."   WHERE venfec between '".$wfecini."'"
		."     AND '".$wfecfin."'"
		."     AND vennfa = '' "
		."     AND vencco = '".$wccoe[0]."'";
		$err = mysql_query($q,$conex);

		$q = "CREATE INDEX tempo2idx on tempo2 (vencon,vennmo)";
		$err = mysql_query($q,$conex);

		$q = "  SELECT Vennum, venfec, venvto, vencon, sum(mdevto)  "
		."    FROM ".$wbasedato."_000011, tempo2 "
		."   WHERE mdecon = vencon "
		."     AND mdedoc = vennmo "
		."   GROUP BY 1,2,3,4 "
		."   ORDER BY  1,2";

		$err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);

		echo "<table border=1>";

		echo "<th align=CENTER bgcolor=DDDDDD><font size=2>VENTA</font></th>";
		echo "<th align=CENTER bgcolor=DDDDDD><font size=2>FECHA</font></th>";
		echo "<th align=CENTER bgcolor=DDDDDD><font size=2>VALOR VENTA</font></th>";
		echo "<th align=CENTER bgcolor=DDDDDD><font size=2>COSTO VENTA</font></th>";
		echo "<th align=CENTER bgcolor=DDDDDD><font size=2>NOTA CREDITO</font></th>";
		echo "<th align=CENTER bgcolor=DDDDDD><font size=2>VALOR NOTA</font></th>";


		$wtotventot = 0;
		$wtotcostot = 0;

		$totbrumal =0;
		$totivamal =0;



		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);

			$exp=explode('-', $row[0]);
			$facLet=$exp[0]; // letra o primera parte de la factura
			$facNum=$exp[1]; // numero o segunda parte de la factura
			$res=0;

			if (isset ($conLet) and isset ($conNum) and $conLet==$facLet  and $conNum!=$facNum and $facNum!=$conNum+1)
			$res=$facNum-$conNum;
			else if (!isset ($conLet) or !isset ($conNum) or !$conLet==$facLet)
			{
				$query = " select venviv from ".$wbasedato."_000016, ".$wbasedato."_000011 ";
				$query = $query. " where vennum= '".$facLet."-".($facNum-1)."' and mdecon = vencon AND mdedoc = vennmo  AND vencco = '".$wccoe[0]."' ";

				$err3 = mysql_query($query,$conex) or die (mysql_errno()." - ".mysql_error());
				$num3 = mysql_num_rows($err3);
				if ($num3<=0)
				$res=2;
			}

			for ($j=1;$j<$res;$j++)
			{
				$vennfa=$facNum-$j;

				$query = " select * from ".$wbasedato."_000016 ";
				$query = $query. " where vennum= '".$facLet."-".$vennfa."' and vennfa='' ";

				$err3 = mysql_query($query,$conex) or die (mysql_errno()." - ".mysql_error());
				$num3 = mysql_num_rows($err3);
				if ($num3>0)
				{


					$q = "  SELECT venfec, vdenum, SUM(ROUND((vdecan*vdevun/(1+(vdepiv/100))),0)-vdedes), SUM(ROUND((((vdecan*vdevun/(1+(vdepiv/100)))-vdedes)*(vdepiv/100)),0)), sum(mdevto) "
					."    FROM  ".$wbasedato."_000016, ".$wbasedato."_000017 , ".$wbasedato."_000011  "
					."     where vennfa='".$facLet."-".$vennfa."'"
					."     AND venffa='".$row[0]."'"
					."     AND vencco = '".$wccoe[0]."' "
					."     AND vennum = vdenum "
					."    and mdecon = vencon "
					."      AND mdedoc = vennmo "
					."      AND mdeart = vdeart "
					."   GROUP BY vdenum  ";
					$err2 = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
					$num2 = mysql_num_rows($err2);


					if ($num2>0)
					{
						for ($k=0;$k<$num2;$k++)
						{
							$row2 = mysql_fetch_array($err2);
							echo "<tr>";
							echo "<td bgcolor='yellow'><font size=2>".$facLet."-".$vennfa."</font></td>";
							echo "<td bgcolor='yellow'><font size=2>".$row2[0]."</font></td>";
							echo "<td align=right bgcolor='yellow'><font size=2>".number_format($row2[2]+$row2[3],0,'.',',')."</font></td>";
							echo "<td align=right bgcolor='yellow'><font size=2>".number_format($row2[4],0,'.',',')."</font></td>";

							echo "</tr>";
							$wtotventot = $wtotventot + $row2[2]+$row2[3];
							$wtotcostot = $wtotcostot + $row2[4];

							$totbrumal = $totbrumal  + $row2[2]+$row2[3];
							$totivamal =$totivamal + $row2[4];
						}
					}else
					{
						echo "<tr>";
						echo "<td bgcolor='#ffffff'><font size=2>".$facLet."-".$vennfa."</font></td>";
						echo "<td bgcolor='#ffffff'><font size=2>0</font></td>";
						echo "<td align=right bgcolor='#ffffff'><font size=2>".number_format(0,0,'.',',')."</font></td>";
						echo "<td align=right bgcolor='#ffffff'><font size=2>".number_format(0,0,'.',',')."</font></td>";

					}

					//consulto la nota credito para la venta
					if($rango1==1)
					{
						$query= "SELECT mendoc, menfac, menfec, mencco, mdeart, mdecan, mdevto, tranum, tratip ";
						$query= $query. "FROM " .$wbasedato."_000010, " .$wbasedato."_000011,  " .$wbasedato."_000055  ";
						$query= $query. "WHERE menfec BETWEEN '$wfecini' AND '$wfecfin' and mencon='".$movdev."' and mencco='".$wccoe[0]."' ";
						$query= $query. "and mendoc = mdedoc and  mdecon = mencon ";
						$query= $query. "and tradev=mendoc and traven = '".$facLet."-".$vennfa."' and traven = Menfac and  tracco ='".$wccoe[0]."' ";

						$errn = mysql_query($query,$conex)or die (mysql_errno()." - ".mysql_error());
						$numn = mysql_num_rows($errn);
						if ($numn<=0)
						{
							$not='&nbsp;';
							$notval='&nbsp;';
						}
						else
						{
							$rown = mysql_fetch_array($errn);
							$not=$rown[7];
							$notval=$rown[5];
						}

						echo "<td bgcolor='#ffffff'><font size=2>".$not."</font></td>";
						echo "<td align=right bgcolor='#ffffff'><font size=2>".number_format($notval,0,'.',',')."</font></td>";
						echo "</tr>";
					}

					if ($rango2==1)
					{

						echo "</tr>";
					}
				}
			}


			$conLet=$facLet; //contenedor del valor anterior de letra
			$conNum=$facNum; //contenedor del valor anterior de numero
		}

		/*echo "<tr>";
		echo "<td bgcolor='#ffffff' colspan='2'><font size=2>TOTAL</font></td>";
		echo "<td align=right bgcolor='#ffffff'><font size=2>".number_format(	$totbrumal,0,'.',',')."</font></td>";
		echo "<td align=right bgcolor='#ffffff'><font size=2>".number_format($totivamal ,0,'.',',')."</font></td>";
		echo "</tr>";*/

		echo "</table></BR>";


		//ACA CREO UNA TABLA TEMPORAL CON LAS FACTURAS DEL PERIODO
		$q = "  CREATE TEMPORARY TABLE if not exists tempo1 as "
		."  SELECT fenffa, fenfac, fenfec, fdenve, fenval "
		."    FROM ".$wbasedato."_000018, ".$wbasedato."_000019 "
		."   WHERE fenfec between '".$wfecini."'"
		."     AND '".$wfecfin."'"
		."     AND fenffa = fdeffa "
		."     AND fenfac = fdefac "
		."   GROUP BY fenffa, fenfac, fenfec, fdenve, fenval ";
		$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

		//$hora = (string)date("H:i:s");
		//echo "Termino 1er Query Tiempo 1 : ".$hora."<br>";

		/////////////////////////////////////////////////////////////////
		// REPORTE POR FACTURA CON EL TOTAL FACTURADO Y COSTO DE LA VENTA
		/////////////////////////////////////////////////////////////////
		echo "<br>";
		echo "<br>";
		echo "<br>";

		echo "<center><table border=2>";
		//echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/farmastore/logo farmastore.png' WIDTH=340 HEIGHT=100></td></tr>";
		echo "<tr><td align=center bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>REPORTE DE FACTURAS ERRONEAS</b></font></td></tr>";

		$q = "  CREATE TEMPORARY TABLE if not exists tempo3 as "
		."  SELECT fenffa, fenfac, fenfec, fenval, vencon, vennmo  "
		."    FROM tempo1, ".$wbasedato."_000016 "
		."   WHERE fenfec between '".$wfecini."'"
		."     AND '".$wfecfin."'"
		."     AND vennum = fdenve "
		."     AND vencco = '".$wccoe[0]."'"
		."   GROUP BY 1,2,3,4,5,6 ";
		$err = mysql_query($q,$conex);

		$q = "CREATE INDEX tempo3idx on tempo3 (vencon,vennmo)";
		$err = mysql_query($q,$conex);

		$q = "  SELECT fenffa, fenfac, fenfec, fenval, sum(mdevto)  "
		."    FROM ".$wbasedato."_000011, tempo3 "
		."   WHERE mdecon = vencon "
		."     AND mdedoc = vennmo "
		."   GROUP BY 1,2,3,4 "
		."   ORDER BY  fenffa, fenfac, fenfec ";

		$err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);

		echo "<table border=1>";

		echo "<th align=CENTER bgcolor=DDDDDD><font size=2>FUENTE</font></th>";
		echo "<th align=CENTER bgcolor=DDDDDD><font size=2>FACTURA</font></th>";
		echo "<th align=CENTER bgcolor=DDDDDD><font size=2>FECHA</font></th>";
		echo "<th align=CENTER bgcolor=DDDDDD><font size=2>VALOR VENTA</font></th>";
		echo "<th align=CENTER bgcolor=DDDDDD><font size=2>COSTO VENTA</font></th>";
		echo "<th align=CENTER bgcolor=DDDDDD><font size=2>NOTA CREDITO</font></th>";
		echo "<th align=CENTER bgcolor=DDDDDD><font size=2>VALOR NOTA</font></th>";


		$wtotventot = 0;
		$wtotcostot = 0;

		$totbrumal =0;
		$totivamal =0;


		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);

			$exp=explode('-', $row[1]);
			$facLet=$exp[0]; // letra o primera parte de la factura
			$facNum=$exp[1]; // numero o segunda parte de la factura
			$res=0;

			if (isset ($conLet) and isset ($conNum) and $conLet==$facLet  and $conNum!=$facNum and $facNum!=$conNum+1)
			$res=$facNum-$conNum;
			else if (!isset ($conLet) or !isset ($conNum) or !$conLet==$facLet)
			{
				$query = " select venviv from ".$wbasedato."_000018, ".$wbasedato."_000019, ".$wbasedato."_000016 ";
				$query = $query. " where fenfac= '".$facLet."-".($facNum-1)."'  AND fenffa = fdeffa  AND fenfac = fdefac  AND fdenve = vennum AND vencco = '".$wccoe[0]."' ";

				$err3 = mysql_query($query,$conex) or die (mysql_errno()." - ".mysql_error());
				$num3 = mysql_num_rows($err3);
				if ($num3<=0)
				$res=2;
			}

			for ($j=1;$j<$res;$j++)
			{
				$vennfa=$facNum-$j;

				$query = " select fenval from ".$wbasedato."_000018 ";
				$query = $query. " where fenfac= '".$facLet."-".$vennfa."'  and fenffa= '".$row[0]."'  ";

				$err3 = mysql_query($query,$conex) or die (mysql_errno()." - ".mysql_error());
				$num3 = mysql_num_rows($err3);
				if ($num3<=0)
				{
					$val='&nbsp;';
				}
				else
				{
					$row3 = mysql_fetch_array($err3);
					$val=$row3[0];
				}

				$q = "  SELECT venfec, vdenum, SUM(ROUND((vdecan*vdevun/(1+(vdepiv/100))),0)-vdedes), SUM(ROUND((((vdecan*vdevun/(1+(vdepiv/100)))-vdedes)*(vdepiv/100)),0)), sum(mdevto) "
				."    FROM  ".$wbasedato."_000016, ".$wbasedato."_000017 , ".$wbasedato."_000011  "
				."     where vennfa='".$facLet."-".$vennfa."'"
				."     AND venffa='".$row[0]."'"
				."     AND vencco = '".$wccoe[0]."' "
				."     AND vennum = vdenum "
				."    and mdecon = vencon "
				."      AND mdedoc = vennmo "
				."      AND mdeart = vdeart "
				."   GROUP BY vdenum  ";
				$err2 = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				$num2 = mysql_num_rows($err2);


				if ($num2>0)
				{
					for ($k=0;$k<$num2;$k++)
					{
						$row2 = mysql_fetch_array($err2);
						echo "<tr>";
						echo "<td bgcolor='yellow'><font size=2>".$row[0]."</font></td>";
						echo "<td bgcolor='yellow'><font size=2>".$facLet."-".$vennfa."</font></td>";
						echo "<td bgcolor='yellow'><font size=2>".$row2[0]."</font></td>";
						echo "<td align=right bgcolor='yellow'><font size=2>".number_format($row2[2]+$row2[3],0,'.',',')."</font></td>";
						echo "<td align=right bgcolor='yellow'><font size=2>".number_format($row2[4],0,'.',',')."</font></td>";

						$wtotventot = $wtotventot + $row2[2]+$row2[3];
						$wtotcostot = $wtotcostot + $row2[4];

						$totbrumal = $totbrumal  + $row2[2]+$row2[3];
						$totivamal =$totivamal + $row2[4];
					}
				}else
				{
					echo "<tr>";
					echo "<td bgcolor='#ffffff'><font size=2>".$row[0]."</font></td>";
					echo "<td bgcolor='#ffffff'><font size=2>".$facLet."-".$vennfa."</font></td>";
					echo "<td bgcolor='#ffffff'><font size=2>".$row[2]."</font></td>";
					echo "<td align=right bgcolor='#ffffff'><font size=2>".number_format($val,0,'.',',')."</font></td>";
					echo "<td align=right bgcolor='#ffffff'><font size=2>".number_format(0,0,'.',',')."</font></td>";
				}

				//Busco nota credito
				//consulto la nota credito para la venta
				if($rango1==1)
				{

					$query= "SELECT mendoc, menfac, menfec, mencco, mdeart, mdecan, mdevto, tranum, tratip ";
					$query= $query. "FROM " .$wbasedato."_000010, " .$wbasedato."_000011,  " .$wbasedato."_000055,  " .$wbasedato."_000016  ";
					$query= $query. "WHERE menfec BETWEEN '$wfecini' AND '$wfecfin' and mencon='".$movdev."' and mencco='".$wccoe[0]."' ";
					$query= $query. "and mendoc = mdedoc and  mdecon = mencon ";
					$query= $query. "and tradev=mendoc and traven = Menfac and  tracco ='".$wccoe[0]."' and vennum=traven and venffa='".$row[0]."' and vennfa='".$facLet."-".$vennfa."' ";

					$errn = mysql_query($query,$conex)or die (mysql_errno()." - ".mysql_error());
					$numn = mysql_num_rows($errn);
					if ($numn<=0)
					{
						$not='&nbsp;';
						$notval='&nbsp;';
					}
					else
					{
						$rown = mysql_fetch_array($errn);
						$not=$rown[7];
						$notval=$rown[6];
					}

					echo "<td bgcolor='#ffffff' align=right><font size=2>".$not."</font></td>";
					echo "<td align=right bgcolor='#ffffff' align=right><font size=2>".number_format($notval,0,'.',',')."</font></td>";
					echo "</tr>";

				}

				if ($rango2==1)
				{

				}

			}


			$conLet=$facLet; //contenedor del valor anterior de letra
			$conNum=$facNum; //contenedor del valor anterior de numero
		}

		/*echo "<tr>";
		echo "<td bgcolor='#ffffff' colspan='3'><font size=2>TOTAL</font></td>";
		echo "<td align=right bgcolor='#ffffff'><font size=2>".number_format(	$totbrumal,0,'.',',')."</font></td>";
		echo "<td align=right bgcolor='#ffffff'><font size=2>".number_format($totivamal ,0,'.',',')."</font></td>";
		echo "</tr>";*/

		echo "</table></BR>";
		
		echo "<table align=center border='0'>";
		echo "<td bgcolor='yellow' width='20'><font size=2>&nbsp;</font></td>";
		echo "<td  ALIGN=center><font size=2>=REALIZO MOVIMIENTO DE INVENTARIO</font></td>";
		echo "</table></br></br>";


	}
}
?>
</body>
</html>