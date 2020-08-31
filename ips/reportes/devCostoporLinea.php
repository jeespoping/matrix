<html>
<head>
<title>costo de las devoluciones por linea</title>
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
	

	

	echo "<form action='devCostoporLinea.php' method=post>";

	$wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
	$wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
	$wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
	$wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro

	$wfecha=date("Y-m-d");

	//$wbasedato='farstore';

	echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";

	echo "<center><table border=2>";
	echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=340 HEIGHT=100></td></tr>";
	echo "<tr><td align=center bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>REPORTE DE DEVOLUCIONES</b></font></td></tr>";

	if (!isset($wfecini) or !isset($wfecfin) or !isset($wcco) )
	{
		/**********************************************Primera pagina**************************/
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

		echo "<tr><td align=center bgcolor=".$wcf." colspan=2>SELECCIONE LA SUCURSAL: ";
		echo "<select name='wcco'>";
		//echo "<option>&nbsp</option>";
		echo "<option>%-Todos los centros de costo</option>";
		for ($i=1;$i<=$num;$i++)
		{
			$row = mysql_fetch_array($res);
			echo "<option>".$row[0]."-".$row[1]."</option>";
		}
		echo "</select></td>";


		echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";

		echo "<tr>";
		echo "<td align=center bgcolor=#cccccc colspan=2><input type='submit' value='OK'></td>";                                         //submit
		echo "</tr>";
	}
	else
	{
		/**********************************************Segunda página**************************/
		/***********************************Consulto las devouciones sin nota credito********************/
		echo "<input type='HIDDEN' NAME= 'wcco' value='".$wcco."'>";
		$wccoe = explode("-",$wcco);


		echo "<tr>";
		echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Fecha Inicial (AAAA-MM-DD): </font></b>".$wfecini."</td>";
		echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Fecha Final (AAAA-MM-DD): </font></b>".$wfecfin."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td bgcolor=".$wcf." align=center colspan=2><b><font text color=".$wclfg.">SUCURSAL: </font></b>".$wcco."</td>";
		echo "<tr><td align=center colspan=2 bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>DEVOLUCIONES POR COSTO ASOCIADO</b></font></td></tr>";
		echo "</tr>";
		echo "</center></table >";

		//ajusto los centros de costo
		if ($wccoe[0]!='%')
		{
			$wccodes[0]=$wccoe[1];
			unset ($wccoe[1]);
		}
		else
		{
			$q =  " SELECT ccocod, ccodes "
			."   FROM ".$wbasedato."_000003 "
			."  WHERE ccoest='on' "
			."  ORDER BY 1 ";

			$res = mysql_query($q,$conex);
			$num = mysql_num_rows($res);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($res);
				$wccodes[$i]=$row[1];
				$wccoe[$i]=$row[0];
			}
		}

		//ajusto las fechas para establecer los rangos antes y depues del cambio en la forma de almacenamiento del sistema
		$fechaCorte='2007-01-26';
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
			$wfecfin='2006-12-31';
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

		//busco la fuente para la nota credito de cada centro de costos
		for ($i=0;$i<count($wccoe);$i++)
		{
			if ($rango2==1)
			{
				$q="SELECT Ccofnc "
				."FROM ".$wbasedato."_000003 "
				."WHERE	Ccocod = '".$wccoe[$i]."' "
				."and	Ccoest = 'on'";
				$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR LA FUENTE PARA LA NOTA CREDITO ".mysql_error());
				$num=mysql_num_rows($err)or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR LA FUENTE PARA LA NOTA CREDITO ".mysql_error());
				$row=mysql_fetch_array($err);
				$fueNota[$i]=$row[0]; //fuente de la transaccion
			}
		}

		/***********************************Consulto las devouciones sin nota credito********************/

		echo "</BR></BR><center><table border=2>";
		echo "<tr><td align=center colspan=2 bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>DEVOLUCIONES SIMPLES</b></font></td></tr>";
		echo "</center></table >";

		$wtotccobru=0;
		$wtotccoiva=0;
		$wtotccotot=0;

		for ($k=0;$k<count($wccoe);$k++)
		{
			$contador=0;
			if ($rango1==1)
			{
				$table=date("Mdis").'2';


				$query = "  CREATE TEMPORARY TABLE if not exists $table as ";
				$query= $query. "SELECT mendoc, menfac, menfec, mencco, mdeart, mdecan, mdevto ";
				$query= $query. "FROM " .$wbasedato."_000010, " .$wbasedato."_000011 ";
				$query= $query. "WHERE menfec BETWEEN '$wfecini' AND '$wfecfin' and mencon='".$movdev."' and mencco='".$wccoe[$k]."' ";
				$query= $query. "and mendoc = mdedoc and  mdecon = mencon ";
				$query= $query. "and Mendoc not in (select tradev FROM " .$wbasedato."_000055 where tradev = Mendoc and traven = Menfac and  tracco ='".$wccoe[$k]."' ) ";


				$err = mysql_query($query,$conex)or die (mysql_errno()." - ".mysql_error());


				$q = " SELECT menfac, venffa, artgru, grucod, grudes, sum(mdevto)  "
				."    FROM $table, ".$wbasedato."_000016, ".$wbasedato."_000017, ".$wbasedato."_000004, ".$wbasedato."_000001 "
				."   WHERE Menfec between '".$wfecini."'"
				."     AND '".$wfecfin."'"
				."     AND Menfac = vennum "
				."     AND vennum = vdenum "
				."  AND vdeart=Mdeart "
				."  AND artcod=Mdeart "
				."     AND Mencco = '".$wccoe[$k]."'"
				."   AND mid(artgru,1,instr(artgru,'-')-1) = grucod "
				."   GROUP BY grucod, grudes "
				."   ORDER BY Menfec, venffa, vennum";

				$err = mysql_query($q,$conex);
				$num = mysql_num_rows($err);

				if ($num>0)
				{

					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_row($err);

						$vector[$i][0]='-';
						for ($j=1;$j<=6;$j++)
						{
							$vector[$i][$j]=$row[$j-1];
						}


					}
					$contador=$num;
				}
			}

			if ($rango2==1)
			{
				//busco el consecutivo para bonos
				$q="SELECT Carfue "
				."FROM ".$wbasedato."_000040 "
				."WHERE	cardca= 'on' "
				."and	carest = 'on'";
				$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR LA FUENTE PARA ABONOS ".mysql_error());

				$num=mysql_num_rows($err);
				$row=mysql_fetch_row($err);
				$fue=$row[0]; //fuente de la transaccion


				$table=date("Mdis").'3';

				$query = "  CREATE TEMPORARY TABLE if not exists $table as ";
				$query= $query. "SELECT mendoc, menfac, menfec, mencco, mdeart, mdecan , mdevto, rdenum ";
				$query= $query. "FROM " .$wbasedato."_000010, " .$wbasedato."_000011, " .$wbasedato."_000021 ";
				$query= $query. "WHERE menfec BETWEEN '$wfecini2' AND '$wfecfin2' and mencon='".$movdev."' and mencco='".$wccoe[$k]."' ";
				$query= $query. "and mendoc = mdedoc and  mdecon = mencon ";
				$query= $query. "and rdefue =  '".$fueNota[$k]."' and  rdemiv = Mendoc and rdecco='".$wccoe[$k]."' ";
				$query= $query. "and Mendoc in (select tradev FROM " .$wbasedato."_000055 where traven = Menfac and  tracco ='".$wccoe[$k]."' and trafue='".$fue."' ) ";

				$err = mysql_query($query,$conex)or die (mysql_errno()." - ".mysql_error());

				$q = " SELECT rdenum, menfac, venffa, artgru, grucod, grudes, sum(mdevto)  "
				."    FROM $table, ".$wbasedato."_000016, ".$wbasedato."_000017, ".$wbasedato."_000004, ".$wbasedato."_000001 "
				."   WHERE Menfec between '".$wfecini2."'"
				."     AND '".$wfecfin2."'"
				."     AND Menfac = vennum "
				."     AND vennum = vdenum "
				."     AND vdeart=Mdeart "
				."     AND Mdeart = artcod "
				."     AND Mencco = '".$wccoe[$k]."'"
				."   AND mid(artgru,1,instr(artgru,'-')-1) = grucod "
				."   GROUP BY grucod, grudes "
				."   ORDER BY Mendoc, venffa, vennum";

				
				$err = mysql_query($q,$conex);
				$num = mysql_num_rows($err);

				if ($num>0)
				{

					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_row($err);

						for ($j=0;$j<7;$j++)
						{
							$vector[$contador][$j]=$row[$j];
						}

						$contador++;
					}
				}
			}

			if ($contador>0)
			{
				echo "<table border=1 align=center>";

				echo "<tr><th align=CENTER bgcolor=DDDDDD colspan='4'><font size=2>".$wccoe[$k]."-".$wccodes[$k]."</font></th></tr>";
				echo "<tr><th align=CENTER bgcolor=DDDDDD><font size=2>Nº NOTA</font></th>";
				echo "<th align=CENTER bgcolor=DDDDDD><font size=2>CODIGO</font></th>";
				echo "<th align=CENTER bgcolor=DDDDDD><font size=2>DESCRIPCION</font></th>";
				echo "<th align=CENTER bgcolor=DDDDDD><font size=2>COSTO DE LA DEVOLUCION</font></th>";

				$wtotvenbru = 0;
				$wtotveniva = 0;
				$wtotventot = 0;

				for ($i=0;$i<$contador;$i++)
				{
					$query= "SELECT fdefac ";
					$query= $query. "FROM " .$wbasedato."_000019  ";
					$query= $query. "WHERE fdeffa='".$vector[$i][2]."' and fdenve='".$vector[$i][1]."' ";

					$err = mysql_query($query,$conex) or die (mysql_errno()." - ".mysql_error());
					$cantidad = mysql_num_rows($err);

					if ($cantidad>=1)
					{
						$res= mysql_fetch_row($err);

						echo "<tr>";
						echo "<td><font size=2>".$vector[$i][0]."</font></td>";
						echo "<td><font size=2>".$vector[$i][4]."</font></td>";
						echo "<td><font size=2>".$vector[$i][5]."</font></td>";
						echo "<td align=right><font size=2>".number_format($vector[$i][6],0,'.',',')."</font></td>";
						echo "</tr>";

						$wtotvenbru = $wtotvenbru + $vector[$i][6];
					}
				}


				echo "<tr>";
				echo "<td colspan=3 >TOTALES</td>";
				echo "<td align=right><font size=2>".number_format($wtotvenbru,0,'.',',')."</font></td>";
				echo "</tr>";

				$wtotccobru=$wtotccobru+$wtotvenbru;
			}
		}

		echo "<tr>";
		echo "<td colspan=3 bgcolor=DDDDDD>TOTALES</td>";
		echo "<td align=right bgcolor=DDDDDD><font size=2>".number_format($wtotccobru,0,'.',',')."</font></td>";
		echo "<tr>";
		echo "</table>";


		/***********************************Consulto las devouciones con nota credito********************/

		/***********************************Consulto las devouciones con nota credito********************/



		echo "</BR></BR><center><table border=2>";
		echo "<tr><td align=center colspan=2 bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>DEVOLUCIONES CON NOTA CREDITO</b></font></td></tr>";
		echo "</center></table >";

		$wtotccobru1=0;
		$wtotccoiva1=0;
		$wtotccotot1=0;

		for ($k=0;$k<count($wccoe);$k++)
		{
			$contador=0;

			if ($rango1==1)
			{
				$table=date("Mdis").'1';


				$query = "  CREATE TEMPORARY TABLE if not exists $table as ";
				$query= $query. "SELECT mendoc, menfac, menfec, mencco, mdeart, mdecan, mdevto, tranum ";
				$query= $query. "FROM " .$wbasedato."_000010, " .$wbasedato."_000011,  " .$wbasedato."_000055  ";
				$query= $query. "WHERE menfec BETWEEN '$wfecini' AND '$wfecfin' and mencon='".$movdev."' and mencco='".$wccoe[$k]."' ";
				$query= $query. "and mendoc = mdedoc and  mdecon = mencon ";
				$query= $query. "and tradev=mendoc and traven = Menfac and  tracco ='".$wccoe[$k]."' and tratip <>'01-ANULACION' ";

				$err = mysql_query($query,$conex)or die (mysql_errno()." - ".mysql_error());


				$q = " SELECT tranum, menfac, venffa, artgru, grucod, grudes, sum(mdevto)  "
				."    FROM $table, ".$wbasedato."_000016, ".$wbasedato."_000017, ".$wbasedato."_000004, ".$wbasedato."_000001 "
				."   WHERE Menfec between '".$wfecini."'"
				."     AND '".$wfecfin."'"
				."     AND Menfac = vennum "
				."     AND vennum = vdenum "
				."  AND vdeart=Mdeart "
				."  AND artcod=Mdeart "
				."     AND Mencco = '".$wccoe[$k]."' "
				."     AND mid(artgru,1,instr(artgru,'-')-1) = grucod "
				."   GROUP BY grucod, grudes "
				."   ORDER BY Mendoc, venffa, vennum";

				$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				$num = mysql_num_rows($err);


				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_row($err);

					for ($j=0;$j<7;$j++)
					{
						$vector[$i][$j]=$row[$j];
					}
				}
				$contador=$num;
			}

			if ($rango2==1)
			{
				//busco la fuente para la nota credito

				$table=date("Mdis").'4';

				$query = "  CREATE TEMPORARY TABLE if not exists $table as ";
				$query= $query. "SELECT mendoc, menfac, menfec, mencco, mdeart, mdecan, mdevto, rdenum ";
				$query= $query. "FROM " .$wbasedato."_000010, " .$wbasedato."_000011, " .$wbasedato."_000021 ";
				$query= $query. "WHERE menfec BETWEEN '$wfecini2' AND '$wfecfin2' and mencon='".$movdev."' and mencco='".$wccoe[$k]."' ";
				$query= $query. "and mendoc = mdedoc and  mdecon = mencon ";
				$query= $query. "and rdefue =  '".$fueNota[$k]."' and  rdemiv = Mendoc and rdecco='".$wccoe[$k]."' ";
				$query= $query. "and Mendoc not in (select tradev FROM " .$wbasedato."_000055 where traven = Menfac and  tracco ='".$wccoe[$k]."' ) ";


				$err = mysql_query($query,$conex)or die (mysql_errno()." - ".mysql_error());

				$q = " SELECT rdenum, menfac, venffa, artgru, grucod, grudes, sum(mdevto)  "
				."    FROM $table, ".$wbasedato."_000016, ".$wbasedato."_000017, ".$wbasedato."_000004, ".$wbasedato."_000001 "
				."   WHERE Menfec between '".$wfecini2."'"
				."     AND '".$wfecfin2."'"
				."     AND Menfac = vennum "
				."     AND vennum = vdenum "
				."     AND vdeart=Mdeart "
				."     AND Mdeart = artcod "
				."     AND Mencco = '".$wccoe[$k]."'"
				."     AND mid(artgru,1,instr(artgru,'-')-1) = grucod "
				."   GROUP BY grucod, grudes "
				."   ORDER BY rdenum, venffa, vennum";

				$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				$num = mysql_num_rows($err);

				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_row($err);

					for ($j=0;$j<7;$j++)
					{
						$vector[$contador][$j]=$row[$j];
					}
					$contador++;
				}
			}

			if ($contador>0)
			{

				echo "<table border=1 align=center>";
				echo "<tr><th align=CENTER bgcolor=DDDDDD colspan='4'><font size=2>".$wccoe[$k]."-".$wccodes[$k]."</font></th></tr>";
				echo "<th align=CENTER bgcolor=DDDDDD><font size=2>Nº NOTA</font></th>";
				echo "<th align=CENTER bgcolor=DDDDDD><font size=2>CODIGO</font></th>";
				echo "<th align=CENTER bgcolor=DDDDDD><font size=2>DESCRIPCION</font></th>";
				echo "<th align=CENTER bgcolor=DDDDDD><font size=2>COSTO DEVOLUCION</font></th>";

				$wtotvenbru = 0;
				$wtotveniva = 0;
				$wtotventot = 0;

				for ($i=0;$i<$contador;$i++)
				{


					echo "<tr>";
					echo "<td ALIGN= CENTER><font size=2>".$vector[$i][0]."</font></td>";
					echo "<td><font size=2>".$vector[$i][4]."</font></td>";
					echo "<td><font size=2>".$vector[$i][5]."</font></td>";
					echo "<td align=right><font size=2>".number_format($vector[$i][6],0,'.',',')."</font></td>";
					echo "</tr>";

					$wtotvenbru = $wtotvenbru + $vector[$i][6];

				}

				echo "<tr>";
				echo "<td colspan=3>TOTALES</td>";
				echo "<td align=right><font size=2>".number_format($wtotvenbru,0,'.',',')."</font></td>";
				echo "</tr>";

				$wtotccobru1=$wtotccobru1+$wtotvenbru;
			}
		}
		echo "<tr>";
		echo "<td colspan=3 bgcolor=DDDDDD>TOTALES</td>";
		echo "<td align=right bgcolor=DDDDDD><font size=2>".number_format($wtotccobru1,0,'.',',')."</font></td>";
		echo "<tr>";

		/***********************************Consulto las devouciones con nota credito********************/

	}
}
?>
</body>
</html>