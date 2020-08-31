<html>
<head>
<title>DEVOLUCIONES POR TERCERO</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
/*
* HISTORIAL DE ACTAULIZACIONES:
* * 2012-06-15 Camilo Zapata, quito en los querys que involucran la tabla 18, el filtro fenfec between wfecini y wfecfini para que filtre tambien por rango de fechas y  se habilito solo
//							el filtro de centro de costos de las facturas
* 2012-06-15 Camilo Zapata, agregó en los querys que involucran la tabla 18, el filtro fenfec between wfecini y wfecfini para que filtre tambien por rango de fechas y tambien se agregó
//							el filtro de centro de costos de las facturas
*/
session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{

	//$wbasedato='farstore';

	$key = substr($user,2,strlen($user));
	

	

	echo "<form action='devolucionesPorTercero.php' method=post>";

	$wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
	$wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
	$wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
	$wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro

	$wfecha=date("Y-m-d");

	echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";

	echo "<center><table border=2>";
	echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=340 HEIGHT=100></td></tr>";
	echo "<tr><td align=center bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>REPORTE DE DEVOLUCIONES POR TERCERO</b></font></td></tr>";

	if (!isset($wfecini) or !isset($wfecfin) or !isset($wcco) or !isset($wemp))
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


		echo "<tr><td align=center bgcolor=".$wcf." colspan=1>SELECCIONE LA SUCURSAL: ";
		echo "<select name='wcco'>";
		for ($i=1;$i<=$num;$i++)
		{
			$row = mysql_fetch_array($res);
			echo "<option>".$row[0]."-".$row[1]."</option>";
		}
		echo "</select></td>";


		//SELECCIONAR EMPRESA
		$q =  " SELECT empcod, empnom, temdes "
		."   FROM ".$wbasedato."_000024, ".$wbasedato."_000029 "
		."  WHERE trim(mid(emptem,1,instr(emptem,'-')-1)) = temcod "
		."  ORDER BY 3,1 ";
		$res = mysql_query($q,$conex);
		$num = mysql_num_rows($res);

		echo "<td align=center bgcolor=".$wcf." >SELECCIONE LA EMPRESA: ";
		echo "<select name='wemp'>";
		echo "<option>% - Todas las empresas</option>";
		for ($i=1;$i<=$num;$i++)
		{
			$row = mysql_fetch_array($res);
			echo "<option>".$row[0]."-".$row[1]."- <b>[&nbsp&nbsp&nbsp&nbsp".$row[2]."&nbsp&nbsp&nbsp&nbsp]</b></option>";
		}
		echo "</select></td></tr>";


		echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";

		echo "<tr>";
		echo "<td align=center bgcolor=#cccccc colspan=2><input type='submit' value='OK'></td>";                                         //submit
		echo "</tr>";
	}  
	else
	{
		/**********************************************Segunda página**************************/
		echo "<input type='HIDDEN' NAME= 'wcco' value='".$wcco."'>";
		$wccoe = explode("-",$wcco);

		echo "<input type='HIDDEN' NAME= 'wemp' value='".$wemp."'>";
		$wempe = explode("-",$wemp);

		echo "<tr>";
		echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Fecha Inicial (AAAA-MM-DD): </font></b>".$wfecini."</td>";
		echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Fecha Final (AAAA-MM-DD): </font></b>".$wfecfin."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td bgcolor=".$wcf." align=center colspan=2><b><font text color=".$wclfg.">SUCURSAL: </font></b>".$wcco."</td>";
		echo "<tr><td align=center colspan=2 bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>DEVOLUCIONES FACTURADAS POR TERCERO</b></font></td></tr>";
		echo "</tr>";
		echo "</center></table >";

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

		//busco la fuente para la nota credito deL  centro de costos
		if ($rango2==1)
		{
			$q="SELECT Ccofnc "
			."FROM ".$wbasedato."_000003 "
			."WHERE	Ccocod = '".$wccoe[0]."' "
			."and	Ccoest = 'on'";
			$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR LA FUENTE PARA LA NOTA CREDITO ".mysql_error());
			$num=mysql_num_rows($err)or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR LA FUENTE PARA LA NOTA CREDITO ".mysql_error());
			$row=mysql_fetch_array($err);
			$fueNota=$row[0]; //fuente de la transaccion
		}

		/***********************************Consulto las devoluciones simples ********************/


		echo "</BR></BR><center><table border=2>";
		echo "<tr><td align=center colspan=2 bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>DEVOLUCIONES SIMPLES</b></font></td></tr>";
		echo "</center></table >";

		$contador=0;

		if ($rango1==1)
		{	
			$table=date("Mdis").'2';

			$query = "  CREATE TEMPORARY TABLE if not exists $table as ";
			$query= $query. "SELECT mendoc, menfac, menfec, mencco, mdeart, mdecan ";
			$query= $query. "FROM " .$wbasedato."_000010, " .$wbasedato."_000011 ";
			$query= $query. "WHERE menfec BETWEEN '$wfecini' AND '$wfecfin' and mencon='".$movdev."' and mencco='$wccoe[0]' ";
			$query= $query. "and mendoc = mdedoc and  mdecon = mencon ";
			$query= $query. "and Mendoc not in (select tradev FROM " .$wbasedato."_000055 where tradev = Mendoc and traven = Menfac and  tracco ='$wccoe[0]') ";

			$err = mysql_query($query,$conex)or die (mysql_errno()." - ".mysql_error());
			
			$condicco='';
			if($wbasedato=='farpmla')
				$condicco="AND Fencco=".$wccoe[0]."";
			$q = " SELECT menfac, menfec, mdeart, venffa, fdefac, fentip, fenres,  fencod, empnit, empnom, SUM(ROUND((Mdecan*vdevun/(1+(vdepiv/100))),0)-vdedes*Mdecan/vdecan) + SUM(ROUND((((Mdecan*vdevun/(1+(vdepiv/100)))-vdedes*Mdecan/vdecan)*(vdepiv/100)),0)) "
			."    FROM $table, ".$wbasedato."_000016, ".$wbasedato."_000017, ".$wbasedato."_000019,  ".$wbasedato."_000018, ".$wbasedato."_000024  "
			."   WHERE Menfec between '".$wfecini."'"
			."     AND '".$wfecfin."'"
			/*." 	   AND fenfec between '".$wfecini."'"
			."     AND '".$wfecfin."'"*/
			."     AND Mencco = '".$wccoe[0]."' "
			."	   ".$condicco
			."     AND Menfac = vennum "
			."     AND menfac = vdenum "
			."     AND vdeart = mdeart "
			."     AND fdenve = menfac"
			."     AND fdeffa = venffa"
			."     AND fenfac = fdefac"
			."     AND fenffa = venffa"
			."     AND fencod like '".trim($wempe[0])."'"
			."     AND fenest = 'on' "
			."     AND empcod = fenres"
			."     AND empest = 'on' "
			."   GROUP BY venffa, fdefac, menfec, fentip, fenres, empnit, empnom, fencod "
			."   ORDER BY fentip, fenres, Menfec, venffa, fdefac ";

			$err = mysql_query($q,$conex);
			$num = mysql_num_rows($err);

			for ($i=0;$i<$num;$i++)
			{
				$res = mysql_fetch_row($err);
				for ($j=0;$j<=10;$j++)
				{
					$vector[$i][$j]=$res[$j];
				}
				$vector[$i][11]='-';
			}

			$contador=$num;
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
			$query= $query. "SELECT mendoc, menfac, menfec, mencco, mdeart, mdecan, rdenum  ";
			$query= $query. "FROM " .$wbasedato."_000010, " .$wbasedato."_000011, " .$wbasedato."_000021 ";
			$query= $query. "WHERE menfec BETWEEN '$wfecini2' AND '$wfecfin2' and mencon='".$movdev."' and mencco='".$wccoe[0]."' ";
			$query= $query. "and mendoc = mdedoc and  mdecon = mencon ";
			$query= $query. "and rdefue =  '".$fueNota."' and  rdemiv = Mendoc and rdecco='".$wccoe[0]."' ";
			$query= $query. "and Mendoc in (select tradev FROM " .$wbasedato."_000055 where traven = Menfac and  tracco ='".$wccoe[0]."' and trafue='".$fue."' ) ";

			$err = mysql_query($query,$conex)or die (mysql_errno()." - ".mysql_error());

			$condicco='';
			if($wbasedato=='farpmla')
				$condicco="AND Fencco=".$wccoe[0]."";
				
			$q = " SELECT menfac, menfec, mdeart, venffa, fdefac, fentip, fenres,  fencod, empnit, empnom, SUM(ROUND((Mdecan*vdevun/(1+(vdepiv/100))),0)-vdedes*Mdecan/vdecan) + SUM(ROUND((((Mdecan*vdevun/(1+(vdepiv/100)))-vdedes*Mdecan/vdecan)*(vdepiv/100)),0)), rdenum "
			."    FROM $table, ".$wbasedato."_000016, ".$wbasedato."_000017, ".$wbasedato."_000019,  ".$wbasedato."_000018, ".$wbasedato."_000024  "
			."   WHERE Menfec between '".$wfecini2."'"
			."     AND '".$wfecfin2."'"
			/*." 	   AND fenfec between '".$wfecini."'"
			."     AND '".$wfecfin."'"*/
			."     AND Mencco = '".$wccoe[0]."' "
			."	   ".$condicco
			."     AND Menfac = vennum "
			."     AND menfac = vdenum "
			."     AND vdeart = mdeart "
			."     AND fdenve = menfac"
			."     AND fdeffa = venffa"
			."     AND fenfac = fdefac"
			."     AND fenffa = venffa"
			."     AND fencod like '".trim($wempe[0])."'"
			."     AND fenest = 'on' "
			."     AND empcod = fenres"
			."     AND empest = 'on' "
			."   GROUP BY venffa, fdefac, menfec, fentip, fenres, empnit, empnom, fencod "
			."   ORDER BY fentip, fenres, Menfec, venffa, fdefac ";

			$err = mysql_query($q,$conex);
			$num = mysql_num_rows($err);

			for ($i=$contador;$i<$num+$contador;$i++)
			{
				$res = mysql_fetch_array($err);
				for ($j=0;$j<=11;$j++)
				{
					$vector[$i][$j]=$res[$j];
				}
			}

			$contador=$num+$contador;
		}


		echo "<table border=1 align =center>";

		$wtotgen=0;
		$wtotncgen=0;

		$i=0;
		while ($i < $contador)
		{
			$wtottem=0;
			$wtotnctem=0;
			$wtipemp=$vector[$i][5];

			echo "<tr><td colspan=9 bgcolor=CCCCFF><b>Tipo de empresa: ".$wtipemp."</b></font></td></tr>";

			while (($i<$contador) and ($wtipemp==$vector[$i][5]))
			{
				$wtotemp=0;
				$wtotncemp=0;
				$wempresa=$vector[$i][6];
				$wnomemp=$vector[$i][8]."-".$vector[$i][9];
				if ($wtipemp != "01-PARTICULAR")
				{
					echo "<tr><td colspan=9 bgcolor=FFFFCC><b>Empresa: ".$vector[$i][6]."-".$vector[$i][7]."<b></td></tr>";
					echo "<th align=CENTER bgcolor=DDDDDD><font size=2>Nº NOTA</font></th>";
					echo "<th align=CENTER bgcolor=DDDDDD><font size=2>FECHA</font></th>";
					echo "<th align=CENTER bgcolor=DDDDDD><font size=2>FUENTE</font></th>";
					echo "<th align=CENTER bgcolor=DDDDDD><font size=2>FACTURA</font></th>";
					echo "<th align=CENTER bgcolor=DDDDDD><font size=2>VENTA NRO</font></th>";
					echo "<th align=CENTER bgcolor=DDDDDD><font size=2>NIT/CEDULA</font></th>";
					echo "<th align=CENTER bgcolor=DDDDDD><font size=2>NOMBRE CLIENTE</font></th>";
					echo "<th align=CENTER bgcolor=DDDDDD><font size=2>VALOR DEVOLUCION</font></th>";

				}

				while (($i<$contador) and ($wtipemp==$vector[$i][5]) and ($wempresa==$vector[$i][6]))
				{
					if ($wtipemp != "01-PARTICULAR")
					{
						echo "<tr>";
						echo "<td ALIGN=CENTER>".$vector[$i][11]."</td>";
						echo "<td>".$vector[$i][1]."</td>";
						echo "<td>".$vector[$i][3]."</td>";
						echo "<td>".$vector[$i][4]."</td>";


						//ACA TRAIGO EL NUMERO DE VENTA DE LA FACTURA, CUANDO ESTA ES INDIVIDUAL
						$q = " SELECT vennum "
						."   FROM ".$wbasedato."_000016, ".$wbasedato."_000024 "
						."  WHERE vennfa = '".$vector[$i][4]."'"
						."    AND vencod = empcod "
						."    AND empfac = 'on' ";
						$err_vta = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
						$num_vta = mysql_num_rows($err_vta);
						if ($num_vta > 0)
						{
							$row_vta = mysql_fetch_array($err_vta);
							echo "<td>".$row_vta[0]."</td>";
						}
						else
						echo "<td>&nbsp</td>";

						//Traigo el nombre del cliente por cada factura
						//Si el codigo de la empresa y el codigo del responsable son iguales traigo el nombre del cliente
						//desde la tabla de clientes, si no, debe ser una venta a empleados entonces traigo el nombre de
						//la tabla de empresas.
						if ($vector[$i][5] == $vector[$i][8])
						{
							$q = " SELECT clinom, clidoc "
							."   FROM ".$wbasedato."_000016,".$wbasedato."_000041 "
							."  WHERE vennfa = '".$vector[$i][4]."'"
							."    AND vennit = clidoc ";
						}
						else
						{
							$q = " SELECT empnom, empnit "
							."   FROM ".$wbasedato."_000016,".$wbasedato."_000024 "
							."  WHERE vennfa = '".$vector[$i][4]."'"
							."    AND vencod = empcod ";
						}
						$err_nom = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
						$num_nom = mysql_num_rows($err_nom);

						if ($num_nom != "")
						{
							$row_nom = mysql_fetch_array($err_nom);
							echo "<td>".$row_nom[1]."</td>";
							echo "<td>".$row_nom[0]."</td>";
						}
						else
						{
							echo "<td>&nbsp</td>";
							echo "<td>&nbsp</td>";
						}
						echo "<td ALIGN=RIGHT>".number_format($vector[$i][10],0,'.',',')."</td>";

						echo "</tr>";
					}

					$wtotemp = $wtotemp + $vector[$i][10];
					$wtottem = $wtottem + $vector[$i][10];
					$wtotgen = $wtotgen + $vector[$i][10];

					$i=$i+1;
				}

				if ($wtipemp != "01-PARTICULAR")
				{
					echo "<tr>";
					echo "<td colspan=6 bgcolor=FFFFCC><b>Total empresa: ".$wnomemp."</b></td>";
					echo "<td bgcolor=FFFFCC ALIGN=RIGHT><b>".number_format($wtotemp,0,'.',',')."</b></td>";
					//  echo "<td bgcolor=FFFFCC>&nbsp</td>";
					// echo "<td bgcolor=FFFFCC ALIGN=RIGHT><b>".number_format($wtotncemp,0,'.',',')."</b></td>";

					echo "</tr>";
				}
			}
			echo "<tr>";
			echo "<td colspan=6 bgcolor=CCCCFF><b>Total tipo de empresa: ".$wtipemp."</b></td>";
			echo "<td bgcolor=CCCCFF ALIGN=RIGHT><b>".number_format($wtottem,0,'.',',')."</b></td>";
			////echo "<td bgcolor=CCCCFF>&nbsp</td>";
			////echo "<td bgcolor=CCCCFF ALIGN=RIGHT><b>".number_format($wtotnctem,0,'.',',')."</b></td>";
			echo "</tr>";
			echo "<tr><td colspan=9 bgcolor=CCCCCC>&nbsp</td></tr>";
		}
		echo "<tr>";
		echo "<td colspan=6   bgcolor=CCFFCC><b>Total general"."</b></td>";
		echo "<td ALIGN=RIGHT bgcolor=CCFFCC><b>".number_format($wtotgen,0,'.',',')."</b></td>";
		//  echo "<td bgcolor=CCFFCC>&nbsp</td>";
		// echo "<td ALIGN=RIGHT bgcolor=CCFFCC><b>".number_format($wtotncgen,0,'.',',')."</b></td>";
		echo "</tr>";
		echo "</table>";

		/***********************************Consulto las devoluciones con nota credito ********************/


		echo "</BR></BR><center><table border=2>";
		echo "<tr><td align=center colspan=2 bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>DEVOLUCIONES CON NOTA CREDITO</b></font></td></tr>";
		echo "</center></table >";


		$contador=0;

		if ($rango1==1)
		{

			$table=date("Mdis").'4';


			$query = "  CREATE TEMPORARY TABLE if not exists $table as ";
			$query= $query. "SELECT mendoc, menfac, menfec, mencco, mdeart, mdecan, tranum ";
			$query= $query. "FROM " .$wbasedato."_000010, " .$wbasedato."_000011,  " .$wbasedato."_000055  ";
			$query= $query. "WHERE menfec BETWEEN '$wfecini' AND '$wfecfin' and mencon='".$movdev."' and mencco='$wccoe[0]' ";
			$query= $query. "and mendoc = mdedoc and  mdecon = mencon ";
			$query= $query. "and tradev=mendoc and traven = Menfac and  tracco ='$wccoe[0]'and tratip <>'01-ANULACION' ";


			$err = mysql_query($query,$conex)or die (mysql_errno()." - ".mysql_error());


			$q = " SELECT menfac, menfec, mdeart, venffa, vennfa, ventcl, venfec,  vencod, empnit, empnom, SUM(ROUND((Mdecan*vdevun/(1+(vdepiv/100))),0)-vdedes*Mdecan/vdecan) + SUM(ROUND((((Mdecan*vdevun/(1+(vdepiv/100)))-vdedes*Mdecan/vdecan)*(vdepiv/100)),0)), tranum "
			."    FROM $table, ".$wbasedato."_000016, ".$wbasedato."_000017,  ".$wbasedato."_000024  "
			."   WHERE Menfec between '".$wfecini."'"
			."     AND '".$wfecfin."'"
			."     AND Mencco = '".$wccoe[0]."' "
			."     AND Vencco = '".$wccoe[0]."' "
			."     AND Menfac = vennum "
			."     AND vennum = vdenum "
			."     AND vdeart = mdeart "
			."     AND empcod = vencod"
			."     AND empest = 'on' "
			."   GROUP BY venffa, vennfa, mendoc, ventcl, vencod, empnit, empnom "
			."   ORDER BY ventcl, vencod, Menfec, venffa, vennfa ";

			$err = mysql_query($q,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$res = mysql_fetch_array($err);
				for ($j=0;$j<=11;$j++)
				{

					$vector[$i][$j]=$res[$j];
				}

			}

			$contador=$num;
		}

		if ($rango2==1)
		{
			$table=date("Mdis").'5';


			$query = "  CREATE TEMPORARY TABLE if not exists $table as ";
			$query= $query. "SELECT mendoc, menfac, menfec, mencco, mdeart, mdecan , rdenum ";
			$query= $query. "FROM " .$wbasedato."_000010, " .$wbasedato."_000011, " .$wbasedato."_000021 ";
			$query= $query. "WHERE menfec BETWEEN '$wfecini2' AND '$wfecfin2' and mencon='".$movdev."' and mencco='".$wccoe[0]."' ";
			$query= $query. "and mendoc = mdedoc and  mdecon = mencon ";
			$query= $query. "and rdefue =  '".$fueNota."' and  rdemiv = Mendoc and rdecco='".$wccoe[0]."' ";
			$query= $query. "and Mendoc not in (select tradev FROM " .$wbasedato."_000055 where traven = Menfac and  tracco ='".$wccoe[0]."' ) ";


			$err = mysql_query($query,$conex)or die (mysql_errno()." - ".mysql_error());


			$q = " SELECT menfac, menfec, mdeart, venffa, vennfa, ventcl, venfec,  vencod, empnit, empnom, SUM(ROUND((Mdecan*vdevun/(1+(vdepiv/100))),0)-vdedes*Mdecan/vdecan) + SUM(ROUND((((Mdecan*vdevun/(1+(vdepiv/100)))-vdedes*Mdecan/vdecan)*(vdepiv/100)),0)), rdenum, sum(Mdecan) "
			."    FROM $table, ".$wbasedato."_000016, ".$wbasedato."_000017,  ".$wbasedato."_000024  "
			."   WHERE Menfec between '".$wfecini2."'"
			."     AND '".$wfecfin2."'"
			."     AND Mencco = '".$wccoe[0]."' "
			."     AND Menfac = vennum "
			."     AND vennum = vdenum "
			."     AND vdeart = mdeart "
			."     AND empcod = vencod";
			if( trim($wempe[0]) !='%')
			$q.="  AND empcod = '".trim($wempe[0])."' ";
			
			$q.="   AND empest = 'on' "
			."   GROUP BY venffa, vennfa, mendoc, ventcl, vencod, empnit, empnom "
			."   ORDER BY ventcl, vencod, Menfec, venffa, vennfa ";

			$err = mysql_query($q,$conex);
			$num = mysql_num_rows($err);

			for ($i=$contador;$i<$num+$contador;$i++)
			{
				$res = mysql_fetch_array($err);
				for ($j=0;$j<=12;$j++)
				{
					$vector[$i][$j]=$res[$j];
				}
			}

			$contador=$num+$contador;
		}


		//echo '<pre>';print_r($vector);echo '</pre>';
		echo "<table border=1 align =center>";

		$wtotgen=0;
		$wtotncgen=0;

		$i=0;
		while ($i < $contador)
		{
			$wtottem=0;
			$wtotnctem=0;
			$wtipemp=$vector[$i][5];

			echo "<tr><td colspan=10 bgcolor=CCCCFF><b>Tipo de empresa: ".$wtipemp."</b></font></td></tr>";

			while (($i<$contador) and ($wtipemp==$vector[$i][5]))
			{
				$wtotemp=0;
				$wtotncemp=0;
				$wempresa=$vector[$i][7];
				$wnomemp=$vector[$i][8]."-".$vector[$i][9];
				if ($wtipemp != "01-PARTICULAR")
				{
					echo "<tr><td colspan=10 bgcolor=FFFFCC><b>Empresa: ".$vector[$i][9]."-".$vector[$i][7]."<b></td></tr>";
					echo "<th align=CENTER bgcolor=DDDDDD><font size=2>Nº NOTA</font></th>";
					echo "<th align=CENTER bgcolor=DDDDDD><font size=2>FECHA</font></th>";
					echo "<th align=CENTER bgcolor=DDDDDD><font size=2>FUENTE</font></th>";
					echo "<th align=CENTER bgcolor=DDDDDD><font size=2>FACTURA</font></th>";
					echo "<th align=CENTER bgcolor=DDDDDD><font size=2>VENTA NRO</font></th>";
					echo "<th align=CENTER bgcolor=DDDDDD><font size=2>NIT/CEDULA</font></th>";
					echo "<th align=CENTER bgcolor=DDDDDD><font size=2>NOMBRE CLIENTE</font></th>";
					echo "<th align=CENTER bgcolor=DDDDDD><font size=2>VALOR DEVOLUCION</font></th>";

				}

				while (($i<$contador) and ($wtipemp==$vector[$i][5]) and ($wempresa==$vector[$i][7]))
				{
					if ($wtipemp != "01-PARTICULAR")
					{
						echo "<tr>";
						echo "<td ALIGN=CENTER>".$vector[$i][11]."</td>";
						echo "<td>".$vector[$i][1]."</td>";
						echo "<td>".$vector[$i][3]."</td>";
						echo "<td>".$vector[$i][4]."</td>";

						if($vector[$i][0]=='')
						{
							//ACA TRAIGO EL NUMERO DE VENTA DE LA FACTURA, CUANDO ESTA ES INDIVIDUAL
							$q = " SELECT vennum "
							."   FROM ".$wbasedato."_000016, ".$wbasedato."_000024 "
							."  WHERE vennfa = '".$vector[$i][4]."'"
							."    AND vencod = empcod "
							."    AND empfac = 'on' ";
							$err_vta = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
							$num_vta = mysql_num_rows($err_vta);
							if ($num_vta > 0)
							{
								$row_vta = mysql_fetch_array($err_vta);
								$venta=$row_vta[0];
								echo "<td>".$row_vta[0]."</td>";
							}
							else
							echo "<td>&nbsp</td>";
						}
						else
						{
							echo "<td>".$vector[$i][0]."</td>";
							$venta=$vector[$i][0];
						}
						//Traigo el nombre del cliente por cada factura
						//Si el codigo de la empresa y el codigo del responsable son iguales traigo el nombre del cliente
						//desde la tabla de clientes, si no, debe ser una venta a empleados entonces traigo el nombre de
						//la tabla de empresas.
						if ($vector[$i][5] == $vector[$i][8])
						{
							$q = " SELECT clinom, clidoc "
							."   FROM ".$wbasedato."_000016,".$wbasedato."_000041 "
							."  WHERE vennfa = '".$venta."'"
							."    AND vennit = clidoc ";
						}
						else
						{
							$q = " SELECT empnom, empnit "
							."   FROM ".$wbasedato."_000016,".$wbasedato."_000024 "
							."  WHERE vennfa = '".$venta."'"
							."    AND vencod = empcod ";
						}
						$err_nom = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
						$num_nom = mysql_num_rows($err_nom);

						if ($num_nom != "")
						{
							$row_nom = mysql_fetch_array($err_nom);
							echo "<td>".$row_nom[1]."</td>";
							echo "<td>".$row_nom[0]."</td>";
						}
						else
						{
							echo "<td>&nbsp</td>";
							echo "<td>&nbsp</td>";
						}
						echo "<td ALIGN=RIGHT>".number_format($vector[$i][10],0,'.',',')."</td>";

						echo "</tr>";
					}

					$wtotemp = $wtotemp + $vector[$i][10];
					$wtottem = $wtottem + $vector[$i][10];
					$wtotgen = $wtotgen + $vector[$i][10];

					$i=$i+1;
				}

				if ($wtipemp != "01-PARTICULAR")
				{
					echo "<tr>";
					echo "<td colspan=7 bgcolor=FFFFCC><b>Total empresa: ".$wnomemp."</b></td>";
					echo "<td bgcolor=FFFFCC ALIGN=RIGHT><b>".number_format($wtotemp,0,'.',',')."</b></td>";
					//  echo "<td bgcolor=FFFFCC>&nbsp</td>";
					// echo "<td bgcolor=FFFFCC ALIGN=RIGHT><b>".number_format($wtotncemp,0,'.',',')."</b></td>";

					echo "</tr>";
				}
			}
			echo "<tr>";
			echo "<td colspan=7 bgcolor=CCCCFF><b>Total tipo de empresa: ".$wtipemp."</b></td>";
			echo "<td bgcolor=CCCCFF ALIGN=RIGHT><b>".number_format($wtottem,0,'.',',')."</b></td>";
			////echo "<td bgcolor=CCCCFF>&nbsp</td>";
			////echo "<td bgcolor=CCCCFF ALIGN=RIGHT><b>".number_format($wtotnctem,0,'.',',')."</b></td>";
			echo "</tr>";
			echo "<tr><td colspan=10 bgcolor=CCCCCC>&nbsp</td></tr>";
		}
		echo "<tr>";
		echo "<td colspan=7   bgcolor=CCFFCC><b>Total general"."</b></td>";
		echo "<td ALIGN=RIGHT bgcolor=CCFFCC><b>".number_format($wtotgen,0,'.',',')."</b></td>";
		//  echo "<td bgcolor=CCFFCC>&nbsp</td>";
		// echo "<td ALIGN=RIGHT bgcolor=CCFFCC><b>".number_format($wtotncgen,0,'.',',')."</b></td>";
		echo "</tr>";
		echo "</table>";


	}
}
?>
</body>
</html>
