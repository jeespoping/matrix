<html>
<head>
<title>DEVOLUVIONES POR LINEA POR TIPO DE IVA RESUMIDO</title>
<script type="text/javascript">

function retornar(wemp_pmla,wfecini,wfecfin,bandera,wcco)
	{
		location.href = "devLineaTIvaRes.php?wemp_pmla="+wemp_pmla+"&wfecini="+wfecini+"&wfecfin="+wfecfin+"&bandera="+bandera+"&wcco="+wcco;
		
    }
	
function cerrar_ventana()
	{
		window.close();
    }	

</script>

</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
// ==========================================================================================================================================
// M O D I F I C A C I O N E S 	 
// ==========================================================================================================================================
// ==========================================================================================================================================
// ==========================================================================================================================================
// Febrero 9 de 2012 :   Ing. Santiago Rivera Botero
// ==========================================================================================================================================
// - Se intercambiaron los resultados de devoluciones de notas simples con los de nota crédito ya que se encontraban trocados
// - Se actualizaron los estilos con los que se presenta el informe 
session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	include_once("root/comun.php");
	

	$titulo = "REPORTE DE DEVOLUCIONES";
	$wactualiz = "2012-01-30";
	 encabezado($titulo,$wactualiz, "clinica");  
	
	//consultamos la base de datos de la empresa correspondiente
	 $q = " SELECT detapl, detval "
        ."    FROM root_000050, root_000051 "
        ."   WHERE empcod = '".$wemp_pmla."'"
        ."     AND empest = 'on' "
        ."     AND empcod = detemp ";
		
     $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
     $num = mysql_num_rows($res); 
  
     if($num > 0 )
        {
	     for($i=1;$i<=$num;$i++)
	        {   
	         $row = mysql_fetch_array($res);
	      
	         if($row[0] == "cenmez")
	            $wcenmez=$row[1];
	         
	         if($row[0] == "afinidad")
	            $wafinidad=$row[1];
			  
			 if(strtoupper($row[0]) == "HCE")
	            $whce=$row[1];
	         
	         if($row[0] == "tabcco")
	            $wtabcco=$row[1];
				
			 if($row[0] == "camilleros")
	            $wcencam=$row[1];
			 
			 if($row[0] == "farpmla")
	            $wbasedato=$row[1];
			 
			 if($row[0] == "facturacion")
	            $wbasedato=$row[1];
			
			 if($row[0] == "farmastore")
	            $wbasedato=$row[1];		
            } 		
       
        }
        else
		    { 
             echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";
			 
	        }
	
	echo "<form action='devLineaTIvaRes.php' method=post>";

	//$wbasedato='farstore';
	echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
	echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";
	
	if (!isset($wfecini) or !isset($wfecfin) or !isset($wcco) or isset($bandera) )
	{
		/**********************************************Primera pagina**************************/
		if(!isset($wfecini ) && !isset($wfecfin ))
	    {
			$wfecini = date("Y-m-d");
			$wfecfin = date("Y-m-d");
	    }
		
		echo "<center><table border=0>";
		echo "<tr>";
		echo "<td class='fila1' align=center><b><font >Fecha Inicial: </font></b>";
		campoFechaDefecto("wfecini", $wfecini);
		echo "</td>";
		echo "<td class='fila1' align=center><b><font >Fecha Final: </font></b>";
		campoFechaDefecto("wfecfin", $wfecfin);
		echo "</td>";
		echo "</tr>";

		//CENTRO DE COSTO
		$q =  " SELECT ccocod, ccodes "
		."   FROM ".$wbasedato."_000003 "
		."  ORDER BY 1 ";
		
		$res = mysql_query($q,$conex);
		$num = mysql_num_rows($res);

		echo "<tr><td align=center class='fila1' colspan=2><b>Selecione la Sucursal: </b>";
		echo "<select name='wcco'>";	
		echo "<option>%-Todos los centros de costo</option>";
		for ($i=1;$i<=$num;$i++)
		{
			$row = mysql_fetch_array($res);
			if(isset($wcco) && $row[0]==$wcco)
				echo "<option selected >".$row[0]."-".$row[1]."</option>";
			else
				echo "<option>".$row[0]."-".$row[1]."</option>";
		}
		echo "</select></td>";


		echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";

		echo "<tr>";
		echo "<td align=center class='fila1' colspan=2><input type='submit' value='OK'></td>";                                         //submit
		echo "</tr>";
		echo "</table></center>";
	}
	else
	{
		/**********************************************Segunda página**************************/
		/***********************************Consulto las devouciones sin nota credito********************/
		
		
		echo "<input type='HIDDEN' NAME= 'wcco' value='".$wcco."'>";
		$wccoe = explode("-",$wcco);
		$wccoe1 = $wccoe[0];
		echo "<center><table >";
		echo "<tr>";
		echo "<td class='fila1' align=center><b><font >Fecha Inicial : </font>".$wfecini." </b></td>";
		echo "<td class='fila1' align=center><b><font >Fecha Final : </font>".$wfecfin." </b></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td class='fila1' align=center colspan=2><b><font >SUCURSAL: </font>".$wcco." </b></td>";
		echo "<tr><td align=center colspan=2 class='fila1'><font size=6 text ><b>DEVOLUCIONES POR COSTO ASOCIADO</b></font></td></tr>";
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
		
		/***********************************Consulto las devouciones simples********************/
		echo "</BR></BR><center><table border=0>";
		echo "<tr><td align=center colspan=2 class='fila1'><font size=6 text ><b>DEVOLUCIONES SIMPLES</b></font></td></tr>";
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

				$q = " SELECT menfac, menfec, venffa, grucod, grudes, arttiv, artiva, SUM(ROUND((Mdecan*vdevun/(1+(vdepiv/100))),0)-vdedes*Mdecan/vdecan), SUM(ROUND((((Mdecan*vdevun/(1+(vdepiv/100)))-vdedes*Mdecan/vdecan)*(vdepiv/100)),0)), SUM(ROUND((Mdecan*vdevun/(1+(vdepiv/100))),0)-vdedes*Mdecan/vdecan) + SUM(ROUND((((Mdecan*vdevun/(1+(vdepiv/100)))-vdedes*Mdecan/vdecan)*(vdepiv/100)),0))"
				."    FROM $table, ".$wbasedato."_000001, ".$wbasedato."_000016, ".$wbasedato."_000017, ".$wbasedato."_000004 "
				."   WHERE Menfec between '".$wfecini."'"
				."     AND '".$wfecfin."'"
				."     AND Menfac = vennum "
				."     AND vennum = vdenum "
				."  AND vdeart=Mdeart "
				."  AND artcod=Mdeart "
				."     AND Mencco = '".$wccoe[$k]."'"
				."     AND mid(artgru,1,instr(artgru,'-')-1) = grucod "
				."   GROUP BY grucod, grudes, arttiv, artiva "
				."   ORDER BY Menfec, venffa, vennum";

				$err = mysql_query($q,$conex);
				$num = mysql_num_rows($err);

				if ($num>0)
				{

					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_row($err);

						$vector[$i][0]='-';
						for ($j=1;$j<=10;$j++)
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

				$q = " SELECT rdenum, menfac, menfec, venffa, grucod, grudes, arttiv, artiva, SUM(ROUND((Mdecan*vdevun/(1+(vdepiv/100))),0)-vdedes*Mdecan/vdecan), SUM(ROUND((((Mdecan*vdevun/(1+(vdepiv/100)))-vdedes*Mdecan/vdecan)*(vdepiv/100)),0)), SUM(ROUND((Mdecan*vdevun/(1+(vdepiv/100))),0)-vdedes*Mdecan/vdecan) + SUM(ROUND((((Mdecan*vdevun/(1+(vdepiv/100)))-vdedes*Mdecan/vdecan)*(vdepiv/100)),0))"
				."    FROM $table, ".$wbasedato."_000001, ".$wbasedato."_000016, ".$wbasedato."_000017, ".$wbasedato."_000004 "
				."   WHERE Menfec between '".$wfecini2."'"
				."     AND '".$wfecfin2."'"
				."     AND Menfac = vennum "
				."     AND vennum = vdenum "
				."     AND vdeart=Mdeart "
				."     AND Mdeart = artcod "
				."     AND Mencco = '".$wccoe[$k]."'"
				."     AND mid(artgru,1,instr(artgru,'-')-1) = grucod "
				."   GROUP BY grucod, grudes, arttiv, artiva "
				."   ORDER BY rdenum, Menfec, venffa, vennum";

				$err = mysql_query($q,$conex);
				$num = mysql_num_rows($err);

				if ($num>0)
				{

					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_row($err);

						for ($j=0;$j<11;$j++)
						{
							$vector[$contador][$j]=$row[$j];
						}

						$contador++;
					}
				}
			}

			if ($contador>0)
			{
				echo "<table align=center>";

				echo "<tr class='encabezadoTabla'><th align=CENTER 	 colspan='8'><font size=2>".$wccoe[$k]."-".$wccodes[$k]."</font></th></tr>";
				echo "<th align=CENTER class='encabezadoTabla'	><font size=2>Nº NOTA</font></th>";
				echo "<th align=CENTER class='encabezadoTabla'><font size=2>LINEA</font></th>";
				echo "<th align=CENTER class='encabezadoTabla'><font size=2>DESCRIPCION</font></th>";
				echo "<th align=CENTER class='encabezadoTabla'><font size=2>TIPO IVA</font></th>";
				echo "<th align=CENTER class='encabezadoTabla'><font size=2>IVA</font></th>";
				echo "<th align=CENTER class='encabezadoTabla'><font size=2>VENTA BRUTA</font></th>";
				echo "<th align=CENTER class='encabezadoTabla'><font size=2>VALOR IVA</font></th>";
				echo "<th align=CENTER class='encabezadoTabla'><font size=2>TOTAL FACTURADO</font></th>";

				$wtotvenbru = 0;
				$wtotveniva = 0;
				$wtotventot = 0;

				for ($i=0;$i<$contador;$i++)
				{
					$query= "SELECT fdefac ";
					$query= $query. "FROM " .$wbasedato."_000019  ";
					$query= $query. "WHERE fdeffa='".$vector[$i][3]."' and fdenve='".$vector[$i][1]."' ";
					
					if($i % 2 == 0)
						$wclass="fila1";
					else
						$wclass="fila2";

					$err = mysql_query($query,$conex) or die (mysql_errno()." - ".mysql_error());
					$cantidad = mysql_num_rows($err);

					if ($cantidad>=1)
					{
						$res= mysql_fetch_row($err);

						echo "<tr>";
						echo "<td class=".$wclass."><font size=2>".$vector[$i][0]."</font></td>";
						echo "<td class=".$wclass."><font size=2>".$vector[$i][4]."</font></td>";
						echo "<td class=".$wclass."><font size=2>".$vector[$i][5]."</font></td>";
						echo "<td class=".$wclass."><font size=2>".$vector[$i][6]."</font></td>";
						echo "<td class=".$wclass."><font size=2>".$vector[$i][7]."</font></td>";
						echo "<td class=".$wclass." align=right><font size=2>".number_format($vector[$i][8],0,'.',',')."</font></td>";
						echo "<td class=".$wclass." align=right><font size=2>".number_format($vector[$i][9],0,'.',',')."</font></td>";
						echo "<td class=".$wclass." align=right><font size=2>".number_format($vector[$i][10],0,'.',',')."</font></td>";
						echo "</tr>";

						$wtotvenbru = $wtotvenbru + $vector[$i][8];
						$wtotveniva = $wtotveniva + $vector[$i][9];
						$wtotventot = $wtotventot + $vector[$i][10];
					}
				}

				echo "<tr class='encabezadoTabla'>";
				echo "<td colspan=5 >TOTALES</td>";
				echo "<td align=right><font size=2>".number_format($wtotvenbru,0,'.',',')."</font></td>";
				echo "<td align=right><font size=2>".number_format($wtotveniva,0,'.',',')."</font></td>";
				echo "<td align=right><font size=2>".number_format($wtotventot,0,'.',',')."</font></td>";
				echo "</tr>";

				$wtotccobru=$wtotccobru+$wtotvenbru;
				$wtotccoiva=$wtotccoiva+$wtotveniva;
				$wtotccotot=$wtotccotot+$wtotventot;
			}
		}

		$bandera=1;
		echo "<tr class='encabezadoTabla'>";
		echo "<td colspan=5 >TOTALES</td>";
		echo "<td align=right ><font size=2>".number_format($wtotccobru,0,'.',',')."</font></td>";
		echo "<td align=right ><font size=2>".number_format($wtotccoiva,0,'.',',')."</font></td>";
		echo "<td align=right ><font size=2>".number_format($wtotccotot,0,'.',',')."</font></td>";
		echo "<tr>";
		echo "</table>";
		
		/***********************************Consulto las devouciones con nota credito********************/
		
		echo "</BR></BR><center><table border=0>";
		echo "<tr><td align=center colspan=2 class='fila1'><font size=6 ><b>DEVOLUCIONES CON NOTA CREDITO</b></font></td></tr>";
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

				$q = " SELECT tranum, menfac, menfec, venffa, grucod, grudes, arttiv, artiva, SUM(ROUND((Mdecan*vdevun/(1+(vdepiv/100))),0)-vdedes*Mdecan/vdecan), SUM(ROUND((((Mdecan*vdevun/(1+(vdepiv/100)))-vdedes*Mdecan/vdecan)*(vdepiv/100)),0)), SUM(ROUND((Mdecan*vdevun/(1+(vdepiv/100))),0)-vdedes*Mdecan/vdecan) + SUM(ROUND((((Mdecan*vdevun/(1+(vdepiv/100)))-vdedes*Mdecan/vdecan)*(vdepiv/100)),0))"
				."    FROM $table, ".$wbasedato."_000001, ".$wbasedato."_000016, ".$wbasedato."_000017, ".$wbasedato."_000004 "
				."   WHERE Menfec between '".$wfecini."'"
				."     AND '".$wfecfin."'"
				."     AND Menfac = vennum "
				."     AND vennum = vdenum "
				."  AND vdeart=Mdeart "
				."  AND artcod=Mdeart "
				."     AND Mencco = '".$wccoe[$k]."' "
				."     AND mid(artgru,1,instr(artgru,'-')-1) = grucod "
				."   GROUP BY grucod, grudes, arttiv, artiva "
				."   ORDER BY Mendoc, venffa, vennum";

				$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				$num = mysql_num_rows($err);


				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_row($err);

					for ($j=0;$j<11;$j++)
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

				$q = " SELECT rdenum, menfac, menfec, venffa, grucod, grudes, arttiv, artiva, SUM(ROUND((Mdecan*vdevun/(1+(vdepiv/100))),0)-vdedes*Mdecan/vdecan), SUM(ROUND((((Mdecan*vdevun/(1+(vdepiv/100)))-vdedes*Mdecan/vdecan)*(vdepiv/100)),0)), SUM(ROUND((Mdecan*vdevun/(1+(vdepiv/100))),0)-vdedes*Mdecan/vdecan) + SUM(ROUND((((Mdecan*vdevun/(1+(vdepiv/100)))-vdedes*Mdecan/vdecan)*(vdepiv/100)),0))"
				."    FROM $table, ".$wbasedato."_000001, ".$wbasedato."_000016, ".$wbasedato."_000017, ".$wbasedato."_000004 "
				."   WHERE Menfec between '".$wfecini2."'"
				."     AND '".$wfecfin2."'"
				."     AND Menfac = vennum "
				."     AND vennum = vdenum "
				."     AND vdeart=Mdeart "
				."     AND Mdeart = artcod "
				."     AND Mencco = '".$wccoe[$k]."'"
				."     AND mid(artgru,1,instr(artgru,'-')-1) = grucod "
				."   GROUP BY grucod, grudes, arttiv, artiva "
				."   ORDER BY rdenum, venffa, vennum";

				$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				$num = mysql_num_rows($err);

				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_row($err);

					for ($j=0;$j<11;$j++)
					{
						$vector[$contador][$j]=$row[$j];
					}
					$contador++;
				}
			}

			if ($contador>0)
			{
				echo "<table border=0 align=center>";
				echo "<tr><th align=CENTER class='encabezadoTabla' colspan='8'><font size=2>".$wccoe[$k]."-".$wccodes[$k]."</font></th></tr>";
				echo "<th align=CENTER class='encabezadoTabla'><font size=2>Nº NOTA</font></th>";
				echo "<th align=CENTER class='encabezadoTabla'><font size=2>LINEA</font></th>";
				echo "<th align=CENTER class='encabezadoTabla'><font size=2>DESCRIPCION</font></th>";
				echo "<th align=CENTER class='encabezadoTabla'><font size=2>TIPO IVA</font></th>";
				echo "<th align=CENTER class='encabezadoTabla'><font size=2>IVA</font></th>";
				echo "<th align=CENTER class='encabezadoTabla'><font size=2>VENTA BRUTA</font></th>";
				echo "<th align=CENTER class='encabezadoTabla'><font size=2>VALOR IVA</font></th>";
				echo "<th align=CENTER class='encabezadoTabla'><font size=2>TOTAL FACTURADO</font></th>";

				$wtotvenbru = 0;
				$wtotveniva = 0;
				$wtotventot = 0;
				
				for ($i=0;$i<$contador;$i++)
				{

					if($i % 2 == 0)
						$wclass="fila1";
					else
						$wclass="fila2";
						
					echo "<tr>";
					echo "<td class=".$wclass." ALIGN= CENTER><font size=2>".$vector[$i][0]."</font></td>";
					echo "<td class=".$wclass."><font size=2>".$vector[$i][4]."</font></td>";
					echo "<td class=".$wclass."><font size=2>".$vector[$i][5]."</font></td>";
					echo "<td class=".$wclass."><font size=2>".$vector[$i][6]."</font></td>";
					echo "<td class=".$wclass."><font size=2>".$vector[$i][7]."</font></td>";
					echo "<td class=".$wclass." align=right><font size=2>".number_format($vector[$i][8],0,'.',',')."</font></td>";
					echo "<td class=".$wclass." align=right><font size=2>".number_format($vector[$i][9],0,'.',',')."</font></td>";
					echo "<td class=".$wclass." align=right><font size=2>".number_format($vector[$i][10],0,'.',',')."</font></td>";
					echo "</tr>"; 
	      
					$wtotvenbru = $wtotvenbru + $vector[$i][8];
					$wtotveniva = $wtotveniva + $vector[$i][9];
					$wtotventot = $wtotventot + $vector[$i][10];

				}

				echo "<tr class='encabezadoTabla'>";
				echo "<td colspan=5>TOTALES</td>";
				echo "<td align=right><font size=2>".number_format($wtotvenbru,0,'.',',')."</font></td>";
				echo "<td align=right><font size=2>".number_format($wtotveniva,0,'.',',')."</font></td>";
				echo "<td align=right><font size=2>".number_format($wtotventot,0,'.',',')."</font></td>";
				echo "</tr>";

				$wtotccobru1=$wtotccobru1+$wtotvenbru;
				$wtotccoiva1=$wtotccoiva1+$wtotveniva;
				$wtotccotot1=$wtotccotot1+$wtotventot;
			}
		}
		echo "<tr class='encabezadoTabla'>";
		echo "<td colspan=5 >TOTALES</td>";
		echo "<td align=right ><font size=2>".number_format($wtotccobru1,0,'.',',')."</font></td>";
		echo "<td align=right ><font size=2>".number_format($wtotccoiva1,0,'.',',')."</font></td>";
		echo "<td align=right ><font size=2>".number_format($wtotccotot1,0,'.',',')."</font></td>";
		echo "<tr>";
		echo "</table>";
		
		echo "<br/>";
		echo "<center><input type='button' name='btn_retornar2' value='Retornar' align='left' onclick='retornar(\"".$wemp_pmla."\",\"".$wfecini."\",\"".$wfecfin."\",\"".$bandera."\", \"".$wccoe1."\")'/>&nbsp; &nbsp; <input type='button' align='right' name='btn_cerrar2' value='Cerrar' onclick='cerrar_ventana()'/></center>";

	}
}
?>
</body>
</html>
