<?php
include_once("conex.php");
 /************************************************************************************
 ************** IMPORTANTE - LEER ANTES DE MODIFICAR O USAR ESTE SCRIPT **************
 *************************************************************************************
 * Este programa de ventas maneja un include llamado "Grabar_venta_nue.php" el cual	 *
 * está ubicado en "include/IPS/" y graba los datos de la venta. Se debe tener en 	 *
 * cuenta que este include graba datos en unix, especificamente en las tablas 	 	 *
 * fanovacc y fasalacc, por esto en local este archivo siempre debe tener estos 	 *
 * nombres de tabla modificados a fanovacc1 y fasalacc1 que son tablas de pruebas,	 *
 * de modo que no se grabe en tablas de datos reales en Unix los datos de prueba	 *
 * que se hagan por ventas en modo local. 											 *
 * Solo cuando se vaya a subir el archivo a producción se deben poner los nombres de *
 * tablas reales: fanovacc y fasalacc								 				 *
 *************************************************************************************/
if(isset($accion))
{

	include_once("root/comun.php");


	$conex = obtenerConexionBD("matrix");
	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
	$wbasedato = $institucion->baseDeDatos;
	$key = substr($user,2,strlen($user));
	switch($accion)
	{

		case "buscar_facturas":
		{

			// este query saca las ventas a facturar  , si los parametros de busqueda estan llenos se agrega un filtro al query
			$query = "SELECT Vencco, Venfec, Vennit, Venvto, Venviv, Vencop, Vencmo, Vendes, Venrec, Vennum , Clinom ,Rdevca,Empnom,Empcod
						FROM ".$wbasedato."_000016 LEFT JOIN ".$wbasedato."_000041 ON Clidoc = Vennit LEFT JOIN  ".$wbasedato."_000021 ON Rdevta=Vennum , ".$wbasedato."_000017 ,  ".$wbasedato."_000024
					   WHERE  Vencco = '".$wfcco."' ";


			if($wfdocumento !='')
			{
				$query .= " AND Vennit = '".$wfdocumento."'";
			}

			if ($wfnombrepac !='')
			{
				$query .= " AND Clinom LIKE '%".$wfnombrepac."%'";
			}


			if ($f_venta !='')
			{
				$query .= " AND Vennum = '".$f_venta."'";
			}


			$query.=     " AND Venest = 'on'
						   AND Vencod = Empcod
						   AND (Vennfa = '' OR  Vennfa LIKE 'NO APLICA' )
						   AND Vennum = Vdenum
						   AND Vennum not in (select Traven from uvglobal_000055)
					   GROUP BY Vennum ";

			// echo $query;


			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				//<tr><td align='center'  colspan=13 class='fila1'><b>Marcar Todos</b><input type='checkbox' name='all'  onclick='enter()'></td></tr>
				echo"<table align='center' id='tabla_resultados'>
				<tr>
					<td colspan=16 align='center' class='encabezadoTabla'>DETALLE DE FACTURAS</td>
				</tr>

				<tr>
					<td class='encabezadoTabla' align='center'>NRO ITEM</td>
					<td class='encabezadoTabla' align='center'>FACTURAR</td>
					<td class='encabezadoTabla' align='center'>VENTA <BR> NRo.</td>
					<td class='encabezadoTabla' align='center'>C. DE C.</td>
					<td class='encabezadoTabla' align='center'>FECHA</td>
					<td class='encabezadoTabla' align='center'>CEDULA</td>
					<td class='encabezadoTabla' align='center'>PACIENTE</td>
					<td class='encabezadoTabla' align='center'>EMPRESA</td>
					<td class='encabezadoTabla' align='center'>VALOR<BR>TOTAL </td>

					<td class='encabezadoTabla' align='center'>VALOR <BR> RECIBO</td>
					<td class='encabezadoTabla' align='center'>VALOR <BR> SALDO</td>
				</tr>";

				//<td class='encabezadoTabla' align='center'>VALOR <BR> IVA </td>
					//<td class='encabezadoTabla' align='center'>VALOR <BR> COPAGO</td>
					//<td class='encabezadoTabla' align='center'>VALOR <BR> CUOTA<BR>MODERADORA</td>
					//<td class='encabezadoTabla' align='center' >VALOR <BR> DESCUENTO</td>
					//<td class='encabezadoTabla' align='center'>VALOR <BR> RECARGO</td>


				$data=array();
				$dta=1;
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$query = "select Clinom  from  ".$wbasedato."_000041 ";
			 		$query .= " where Clidoc = '".$row[2]."'";
					$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					$data[$i][0]=$row[0];
					$data[$i][1]=$row[1];
					$data[$i][2]=$row[2];
					$data[$i][11] =$row['Rdevca'];
					$data[$i][12] =$row['Empnom'];
					$data[$i][13] =$row['Empcod'];
					if($num1 == 1)
					{
						$row1 = mysql_fetch_array($err1);
						$data[$i][3]=$row1[0];
					}
					else
						$data[$i][3]="NO ESPECIFICO";
					$data[$i][4]=$row[3];
					$data[$i][5]=$row[4];
					$data[$i][6]=$row[5];
					$data[$i][7]=$row[6];
					$data[$i][8]=$row[7];
					$data[$i][9]=$row[8];
					$data[$i][10]=$row[9];
					$data[$i][11] =$row['Rdevca'];
					$data[$i][12] =$row['Empnom'];
					$data[$i][13] =$row['Empcod'];
				}
			}
			else
			{
				echo "<center>No hay datos para mostrar</center>";

			}

			if($dta == 1)
			{

				$wtotal = 0;
				$wfac = 0;
				$wiva = 0;
				$wcop = 0;
				$wcmo = 0;
				$wdes = 0;
				for ($i=0;$i<$num;$i++)
				{
					$wtotal += $data[$i][4];
					if($i % 2 == 0)
						$wfc="fila1";
					else
						$wfc="fila2";
					echo "<tr class='".$wfc." tr_resultados'>";
					echo "<td align=center>".$i."</td>";
					if(isset($fac[$i]) or isset($all))
					{
						$wfac += $data[$i][4];
						$wiva += $data[$i][5];
						$wcop += $data[$i][6];
						$wcmo += $data[$i][7];
						$wdes += $data[$i][8];
						$wrec += $data[$i][8];
						echo "<td  align=center><input class='checbox_facturacion' type='checkbox' codigo='".$i."' name='fac[".$i."]' id='ch_fac_".$i."' checked onclick='Sumarchequeada('".$i."')'></td>";
					}
					else
					{
						echo "<td  align=center><input class='checbox_facturacion' type='checkbox' codigo='".$i."' name='fac[".$i."]' id='ch_fac_".$i."' onclick='Sumarchequeada(".$i.")'></td>";
					}

					$query_saldo = "SELECT  Rdevta,Rdevca
									  FROM  ".$wbasedato."_000021
									 WHERE  Rdevta='".$data[$i][10]."' AND Rdeest='on'";

					$res2 = mysql_query($query_saldo,$conex);
					$saldo = 0;
					while($row3 = mysql_fetch_array($res2))
					{

						$saldo =$saldo + $row3['Rdevca'] ;

					}

					echo "<td >".$data[$i][10]."<input type='hidden' id='numventa_sel_".$i."' value='".$data[$i][10]."'></td>";
					echo "<td >".$data[$i][0]."</td>";
					echo "<td >".$data[$i][1]."</td>";
					echo "<td >".$data[$i][2]."<input type='HIDDEN' id= 'wcedpac_".$i."'  value=".$data[$i][2]."></td>";
					echo "<td >".$data[$i][3]."<input type='HIDDEN' id= 'wnompac_".$i."'  value=".$data[$i][3]."></td>";
					echo "<td >".$data[$i][12]."<input type='hidden' id='codempresa_".$i."' value='".$data[$i][13]."'></td>";


					// <tr><td class=encabezadoTabla >Centro de Costos</td></tr>
					// <tr class=".$wfc."><td >".$data[$i][0]."</td></tr>
					// <tr class=".$wfc."><td class=encabezadoTabla >Empresa</td></tr>
					// <tr class=".$wfc."><td >".$data[$i][12]."</td></tr>
					$toltip2="
					<table class=".$wfc.">
					<tr class=".$wfc."><td><b>Valor Iva :</b></td><td align=right> $".number_format((double)$data[$i][5],2,'.',',')."</td></tr>
					<tr class=".$wfc."><td><b>Valor Copago :</b></td><td align=right> $".number_format((double)$data[$i][6],2,'.',',')."</td></tr>
					<tr class=".$wfc."><td><b>Valor Cuota Moredadora :</b></td><td align=right> $".number_format((double)$data[$i][7],2,'.',',')."</td></tr>
					<tr class=".$wfc."><td><b>Valor Descuento :</b></td><td align=right> $".number_format((double)$data[$i][8],2,'.',',')."</td></tr>
					<tr class=".$wfc."><td><b>Valor Recargo :</b></td><td align=right> $".number_format((double)$data[$i][9],2,'.',',')."</td></tr></table>";

					echo "<td align=right  class='tooltip2'  style='cursor : pointer' title='".$toltip2."'>$".number_format((double)$data[$i][4],2,'.',',')."<img src='../../images/medical/root/info.png' width='16px' heigth='16px'/><input type='hidden' id='Valortotal_".$i."' value='".($data[$i][4])."'></td>";


					//echo "<td align=right >$".number_format((double)$data[$i][5],2,'.',',')."<input type='hidden' id='Valoriva_".$i."' value='".($data[$i][5])."'></td>";
					//echo "<td align=right>$".number_format((double)$data[$i][6],2,'.',',')."<input type='hidden' id='Valorcopago_".$i."' value='".($data[$i][6])."'></td>";
					//echo "<td align=right>$".number_format((double)$data[$i][7],2,'.',',')."<input type='hidden' id='Valorcmoderadora_".$i."' value='".($data[$i][7])."'></td>";
					//echo "<td align=right>$".number_format((double)$data[$i][8],2,'.',',')."<input type='hidden' id='Valordesc_".$i."' value='".($data[$i][8])."'></td>";
					//echo "<td align=right>$".number_format((double)$data[$i][9],2,'.',',')."<input type='hidden' id='Valorrecargo_".$i."' value='".($data[$i][9])."'></td>";
					//echo "<td align=right>$".number_format((double)$saldo,2,'.',',')."<input type='hidden' id='Valorrecibo_".$i."' value='".($saldo)."'></td>";
					echo "<td align=right>$".number_format((double)$saldo,2,'.',',')."
								<input type='hidden' id='Valorrecibo_".$i."' value='".($saldo)."'>
								<input type='hidden' id='Valoriva_".$i."' value='".($data[$i][5])."'>
								<input type='hidden' id='Valorcopago_".$i."' value='".($data[$i][6])."'>
								<input type='hidden' id='Valorcmoderadora_".$i."' value='".($data[$i][7])."'>
								<input type='hidden' id='Valordesc_".$i."' value='".($data[$i][8])."'>
								<input type='hidden' id='Valorrecargo_".$i."' value='".($data[$i][9])."'>
						  </td>";
					echo "<td align=right>$".number_format((double)($data[$i][4] - $saldo),2,'.',',')."<input type='hidden' id='Valorsaldo_".$i."' value='".($data[$i][4] - $saldo)."'></td>";
					echo "</tr>";
				}
					//hasta aqui

				echo "<tr class='fila1 tr_resultados'><td   colspan=8><b>TOTAL FACTURAS SELECCIONADAS : </b></td><td  align=right id='totalfacseleccionadas'></td><td  style='display : none' align=right id='totalivaseleccionadas'></td><td  align=right  style='display : none'  id='totalcopagoseleccionadas'></td><td  align=right id='totalcmoderadoraseleccionadas' style='display : none' ></td><td align=right id='totaldescseleccionadas' style='display : none'  ></td><td align=center id='totalrecargoseleccionadas' style='display : none'  >&nbsp</td><td align=center id='totalrec' >&nbsp</td><td align=center id='totalsald' >&nbsp</td></tr>";
				//echo "<tr class='fila1 tr_resultados'><td   colspan=8><b>TOTAL FACTURAS SELECCIONADAS : </b></td><td  align=right id='totalfacseleccionadas'></td><td   align=right id='totalivaseleccionadas'></td><td  align=right id='totalcopagoseleccionadas'></td><td  align=right id='totalcmoderadoraseleccionadas'></td><td align=right id='totaldescseleccionadas'></td><td align=center id='totalrecargoseleccionadas' >&nbsp</td><td align=center id='totalrec' >&nbsp</td><td align=center id='totalsald' >&nbsp</td></tr>";
				echo "<tr class='fila1 tr_resultados'><td   colspan=8><b>TOTAL FACTURAS  : </td><td  align=right><b>$".number_format((double)$wtotal,2,'.',',')."</b></td><td align=center  colspan=7>&nbsp</td></tr>";
				//echo "<tr class='fila1 tr_resultados'><td align=center  colspan=13><font size=5><b>FACTURAR</b><input type='checkbox' name='ok'></font></td></tr>";
				echo "<tr class='fila1 tr_resultados'><td align=center colspan=16><input type='button' value='Ok' id='botongrabarventanamodal2' class='btn_loading2' onclick='facturar()'></td></tr>";
				echo "<br><br>";
				echo "<input type='HIDDEN' name= 'wfini' value='".$wfini."'>";
				echo "<input type='HIDDEN' name= 'wffin' value='".$wffin."'>";
				echo "<input type='HIDDEN' name= 'wcco' value='".$wcco."'>";
				echo "<input type='HIDDEN' name= 'wemp' value='".$wemp."'>";
				echo "<input type='HIDDEN' name= 'dta' value=".$dta.">";
				echo "<input type='HIDDEN' name= 'num' value=".$num.">";
				echo "<input type='HIDDEN' id='wfac'    name= 'wfac' value=".$wfac.">";
				echo "<input type='HIDDEN' id= 'wiva'   name= 'wiva' value=".$wiva.">";
				echo "<input type='HIDDEN' id= 'wcop'   name= 'wcop' value=".$wcop.">";
				echo "<input type='HIDDEN' id= 'wcmo' name= 'wcmo' value=".$wcmo.">";
				echo "<input type='HIDDEN' id= 'wdes' name= 'wdes' value=".$wdes.">";
				echo "<input type='HIDDEN' id= 'wrec' name= 'wrec' value=".$wrec.">";
				echo "<input type='HIDDEN' id= 'wsald' name= 'wsald' value=".$wsald.">";

				echo "<input type='HIDDEN' name= 'data[".$i."][0]' value=".$data[$i][0].">";
				echo "<input type='HIDDEN' name= 'data[".$i."][1]' value=".$data[$i][1].">";
				echo "<input type='HIDDEN' name= 'data[".$i."][2]' value=".$data[$i][2].">";
				echo "<input type='HIDDEN' name= 'data[".$i."][3]' value='".$data[$i][3]."'>";
				echo "<input type='HIDDEN' name= 'data[".$i."][4]' value=".$data[$i][4].">";
				echo "<input type='HIDDEN' name= 'data[".$i."][5]' value=".$data[$i][5].">";
				echo "<input type='HIDDEN' name= 'data[".$i."][6]' value=".$data[$i][6].">";
				echo "<input type='HIDDEN' name= 'data[".$i."][7]' value=".$data[$i][7].">";
				echo "<input type='HIDDEN' name= 'data[".$i."][8]' value=".$data[$i][8].">";
				echo "<input type='HIDDEN' name= 'data[".$i."][9]' value=".$data[$i][9].">";
				echo "<input type='HIDDEN' name= 'data[".$i."][10]' value=".$data[$i][10].">";
				echo "</table>";
			}
			break;
		}
		case "buscar_ventas":
		{
			// este query saca las ventas a facturar  , si los parametros de busqueda estan llenos se agrega un filtro al query
			$query = "SELECT Vencco, Venfec, Vennit, Venvto, Venviv, Vencop, Vencmo, Vendes, Venrec, Vennum , Clinom ,Empnom,Empcod,Vennfa
						FROM ".$wbasedato."_000016 LEFT JOIN ".$wbasedato."_000041 ON Clidoc = Vennit ,  ".$wbasedato."_000024
					   WHERE  Vencco = '".$wfcco."' ";


			if($wfdocumento !='')
			{
				$query .= " AND Vennit = '".$wfdocumento."'";
			}

			if ($wfnombrepac !='')
			{
				$query .= " AND Clinom LIKE '%".$wfnombrepac."%'";
			}


			if ($f_venta !='')
			{
				$query .= " AND Vennum = '".$f_venta."'";
			}


			$query.=     " AND Venest = 'on'
						   AND Vencod = Empcod
						   AND Vennum not in (select Traven from uvglobal_000055)
					   GROUP BY Vennum ";

					   //AND Vennfa = ''

			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				//<tr><td align='center'  colspan=13 class='fila1'><b>Marcar Todos</b><input type='checkbox' name='all'  onclick='enter()'></td></tr>
				$html .= "<table align='center' id='tabla_resultados'>
				<tr>
					<td colspan=17 align='center' class='encabezadoTabla'>DETALLE DE FACTURAS</td>
				</tr>

				<tr>
					<td class='encabezadoTabla' align='center'>NRO ITEM</td>
					<td class='encabezadoTabla' align='center'>VENTA <BR> NRo.</td>
					<td class='encabezadoTabla' align='center'>FACTURA<BR> NRo.</td>

					<td class='encabezadoTabla' align='center'>FECHA</td>
					<td class='encabezadoTabla' align='center'>CEDULA</td>
					<td class='encabezadoTabla' align='center'>PACIENTE</td>
					<td class='encabezadoTabla' align='center'>VALOR<BR>TOTAL </td>
					<td class='encabezadoTabla' align='center'>VALOR <BR> RECIBO</td>
					<td class='encabezadoTabla' align='center'>VALOR <BR> SALDO</td>
					<td class='encabezadoTabla' align='center'></td>
					<td class='encabezadoTabla' align='center'></td>
				</tr>";


				$data=array();
				$dta=1;
				$i = 0;

				while($row = mysql_fetch_array($err))
				{
					$toltip ="";
					$i++;
					if($i % 2 == 0)
						$wfc="fila1";
					else
						$wfc="fila2";

					/*$toltip .="<table><tr>
					 	<td class=encabezadoTabla align=center>C. DE C.</td>
					    <td class=encabezadoTabla align=center>EMPRESA</td>
						<td class=encabezadoTabla align=center>VALOR <BR> IVA </td>
						<td class=encabezadoTabla align=center>VALOR <BR> COPAGO</td>
						<td class=encabezadoTabla align=center>VALOR <BR> CUOTA<BR>MODERADORA</td>
						<td class=encabezadoTabla align=center >VALOR <BR> DESCUENTO</td>
						<td class=encabezadoTabla align=center>VALOR <BR> RECARGO</td></tr>";
					*/
					$html .= "<tr class='".$wfc." tr_resultados' id='tr_abonos_".$i."' ocultar>";
					$html .="<td align=center>".($i)."</td>";

					$html .= "<td>".$row['Vennum']."</td>";
					$html .= "<td>".$row['Vennfa']."</td>";

					$html .= "<td>".$row['Venfec']."</td>";
					$html .= "<td>".$row['Vennit']."</td>";
					$html .= "<td>".$row['Clinom']."</td>";


					$toltip .="<table class=".$wfc."><tr><td nowrap=nowrap><b>Centro de Costos :</b></td><td align=left> ".$row['Vencco']."</td></tr>";
					$toltip .= "<tr><td  nowrap=nowrap><b>Empresa : </b></td><td  align=left >".$row['Empnom']."</td></tr>";
					$toltip .= "<tr><td  nowrap=nowrap><b>Valor Iva : </b></td><td align=left >$".number_format((double)$row['Venviv'],2,'.',',')."</td></tr>";
					$toltip .= "<tr><td  nowrap=nowrap><b>Valor Copago :</b></td><td align=left > $".number_format((double)$row['Vencop'],2,'.',',')."</td></tr>";
					$toltip .= "<tr><td  nowrap=nowrap><b>Valor Cuota Moredadora :</b></td><td align=left>$".number_format((double)$row['Vencmo'],2,'.',',')."</td></tr>";
					$toltip .= "<tr><td  nowrap=nowrap><b>Valor Descuento :</b></td><td align=left>$".number_format((double)$row['Vendes'],2,'.',',')."</td></tr>";
					$toltip .= "<tr><td  nowrap=nowrap><b>Valor Recargo :</b></td><td align=left> $".number_format((double)$row['Venrec'],2,'.',',')."</td></tr></table>";

					$html .= "<td align=right class='tooltip' style='cursor:pointer' title='".$toltip."' >$".number_format((double)$row['Venvto'],2,'.',',')."<img src='../../images/medical/root/info.png' width='16px' heigth='16px'/></td>";





						$query_saldo = "SELECT  Rdevta,Rdevca
										  FROM  ".$wbasedato."_000021
										 WHERE  Rdevta='".$row['Vennum']."' AND Rdeest='on'";

						$res2 = mysql_query($query_saldo,$conex);
						$vrecibido = 0;
						while($row3 = mysql_fetch_array($res2))
						{

							$vrecibido =$vrecibido + $row3['Rdevca'] ;

						}


					if($row['Vennfa']!='' && $row['Vennfa'] !='NO APLICA' )
					{
						//--Actualizo el valor del saldo en la tabla 18
						$query_saldo= "SELECT  Fensal , Fenrbo
										 FROM ".$wbasedato."_000018
										WHERE  Fenfac= '".$row['Vennfa']."' ";
						$res2 = mysql_query($query_saldo,$conex);

						while($row3 = mysql_fetch_array($res2))
						{
							$vsaldo = $row3['Fensal'];



						}


					}
					else
					{
						$vsaldo = $row['Venvto'] - $vrecibido;

					}

					$html .= "<td align=right>$".number_format((double)$vrecibido,2,'.',',')."</td>";

					$html .= "<td align=right> $".number_format((double)($vsaldo),2,'.',',')."</td>";
					$html .= "<td align=right><a onclick='verdetalleabono(\"".$i."\", \"".$row['Vennum']."\" , \"".$wfc."\")' style='cursor : pointer ; color : blue'>Ver detalle</a></td>";
					if($row['Venvto'] - $vrecibido == '0')
					{
						 //$html = str_replace('ocultar' , 'style=display:none' , $html);
						 $html = str_replace('ocultar' , '' , $html);
						 $html .="<td align=right></td>";


					}
					else
					{
						$html = str_replace('ocultar' , '' , $html);
						$html .="<td align=right><input type='button' onclick='Realizar_abono(\"".$row['Vennum']."\" , \"".($vsaldo)."\" , \"".$row['Empcod']."\",\"".$row['Empnom']."\", \"".$row['Vennum']."\",\"".$row['Venvto']."\",\"".$vrecibido."\",\"".$row['Clinom']."\",\"".$row['Vennit']."\")' value='Abonar'></td>";


					}

						$html .= "</tr>";

				}

			}
			else
			{
				$html .=$query."<center>No hay datos para mostrar</center>";

			}
			echo $html ;
			break ;
		}
		case "buscar_ventas_especifica":
		{



			// este query saca las ventas a facturar  , si los parametros de busqueda estan llenos se agrega un filtro al query
			$query = "SELECT Vencco, Venfec, Vennit, Venvto, Venviv, Vencop, Vencmo, Vendes, Venrec, Vennum , Clinom ,Rdevca,Empnom,Empcod,Venest
						FROM ".$wbasedato."_000016 LEFT JOIN ".$wbasedato."_000041 ON Clidoc = Vennit LEFT JOIN  ".$wbasedato."_000021 ON Rdevta=Vennum AND Rdeest='on' , ".$wbasedato."_000017 ,  ".$wbasedato."_000024
					   WHERE  Vennum = '".$wnumventa."'";



			$query.=     " AND Venest = 'on'
						   AND Vencod = Empcod
						   AND Vennum = Vdenum
						   AND Vennum not in (select Traven from uvglobal_000055)
					   GROUP BY Vennum ";

			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{

				while($row = mysql_fetch_array($err))
				{


					$query_saldo = "SELECT  Rdevta,Rdevca
									  FROM  ".$wbasedato."_000021
									 WHERE  Rdevta='".$row['Vennum']."' AND Rdeest='on'";

					$res2 = mysql_query($query_saldo,$conex);
					$saldo = 0;
					while($row3 = mysql_fetch_array($res2))
					{

						$saldo =$saldo + $row3['Rdevca'] ;

					}
					$valorsaldo = $row['Venvto'] - $saldo;
					$valorrecibo = $saldo;
					$resultado = array ();
					$resultado['saldo'] = $valorsaldo;
					$resultado['recibo'] = $valorrecibo;
					$resultado['hay'] = 'si';
					$resultado['estado'] = $row['Venest'];
					$resultado['query'] = $query;

				}



			}
			else
			{
				$resultado = array ();
				$resultado['hay'] = 'no';
				$resultado['saldo'] = 'no';
				$resultado['recibo'] = 'no';
				$resultado['estado'] = 'off';

			}


			echo json_encode($resultado);

			break ;
		}
		case "detalle_abono":
		{
		   echo "<fieldset align='center' style='padding:15px;margin:15px'>
					<legend class='fieldset'>Realizar Recibo a Venta</legend>";


			echo "<center><table align='center'>
					<tr class='encabezadoTabla'>
					<td colspan='7' align='center'>Detalle de Venta</td>
					</tr>
					<tr class='encabezadoTabla' style='font-size: 11px'>
						<td>Nombre</td>
						<td>Cedula</td>
						<td>Numero Venta</td>
						<td>Empresa</td>
						<td>Valor Total</td>
						<td>Valor Recibo</td>
						<td>Valor Saldo</td>
					</tr>
					<tr class='fila1'>
						<td>".$wnom_paciente."</td>
						<td>".$wcedula."</td>
						<td>".$wnum_venta."</td>
						<td>".$wnom_empresa."</td>
						<td>".number_format((double)$wval_total,2,'.',',')."</td>
						<td><div id='detalle_valor_recibido'>".number_format((double)$wval_recibo,2,'.',',')."</div></td>
						<td><div id='detalle_valor_saldo'>".number_format((double)$wsaldo,2,'.',',')."</div></td>
					</tr>


				  </table></center><br><input type='hidden' id='hiddennombrecliente' value='".$wnom_paciente."'><input type='hidden' id='hiddencedulacliente' value='".$wcedula."'><input type='hidden' id='hiddennum_venta' value='".$wnum_venta."'><input type='hidden' id='hiddennomempresa' value='".$wnom_empresa."'><input type='hidden' id='hiddenempresa' value='".$wcod_empresa."'><input type='hidden' id='hiddennumero' value='1'><input type='hidden' id='hiddensaldo' value='".$wsaldo."'><input type='hidden' id='hiddenobservacion' value=''>";
				  echo"<div id='ocultar_al_abonar'>
				  <table align='center' id='table_formas_pago'><tr class='encabezadoTabla' style='font-size: 11px'>
						<td class='uno' align='center'><b>Forma de Pago</b></td>
						<td class='uno' align='center'><b>Dcto Anexo:</b></td>
						<td class='uno' align='center'><b>Observ: </b></td>
						<td  align='center'><b>Datos del Banco:</b></td>
						<td  align='center'><b>Ubicacion:</b></td>
						<td  align='center'><b>N autorizacion:</b></td>
						<td class='uno' align='center'><b>Valor entregado:</b></td>
						<td class='uno' align='center'><b>Valor Abonado:</b></td>
						<td class='uno' align='center'><b>Saldo</b></td>
						<td class='uno' align='center'><b>En que Banco se consigna</b></td>
						<td class='uno' align='center'><input type='button' valor='Agregar Forma de pago' value='+' title='Agregar Forma de pago' onclick='AgregarFormadePago()'></td></tr>
						<tr class='fila2' class='tr_forma_pago' style='font-size: 11px'>
						<td class='uno'><select id='sel_formapago_1' class='formaspago' numero='1' onchange='cambiarformapago(this)'>";

						// query que trae de la tabla "wbasedato"_000023 , las formas de pago que se realizan a la entidad
						$queryformaspago = "SELECT Fpades, Fpacod
											  FROM ".$wbasedato."_000023
											 WHERE Fpaest = 'on' " ;

						$resformas = mysql_query($queryformaspago,$conex) or die (mysql_errno()." - ".mysql_error());



						while($rowformas = mysql_fetch_array($resformas))
						{
							if($rowformas['Fpacod']=='11')
							{
								echo "<option value='".$rowformas['Fpacod']."' selected>".$rowformas['Fpades']."</option>";
							}
							else
							{
								echo "<option value='".$rowformas['Fpacod']."' >".$rowformas['Fpades']."</option>";
							}
						}
						echo"</select></td>
						<td class='uno'><input id='docanexo_1' type='text'></td>
						<td class='uno'><input type='text' id='observacion_1'></td>
						<td ><select class='dos_1' style='display : none'   id='datosbanco_1'>";

						 $q = " SELECT bancod, bannom "
								 ."   FROM ".$wbasedato."_000069 "
								 ."  WHERE banest  = 'on' "
								 ."    AND bancag != 'on'";

						$resu = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


						//echo "<option value='' >...seleccione</option>";

		 		        while ($banc = mysql_fetch_array($resu))
						{

							if($banc['bancod']=="0TR")
							{
								echo "<option value='".$banc['bancod']."' selected=selected >".$banc['bannom']."</option>";

							}
							else
							{
								echo "<option value='".$banc['bancod']."' >".$banc['bannom']."</option>";
							}
						}


						echo "</select></td>
						<td ><select class='dos_1' style='display : none'   id='ubicacion_1'>";

						$otro1='2-Otras plazas';
				  	    $otro2='1-Local';
			            echo "<option value='".$otro2."' selected=selected>".$otro2."</option>
							  <option value='".$otro1."' >".$otro1."</option>
						";

						echo"</select></td>
						<td ><input class='dos_1 autorizacion' style='display : none'  type='text'  id='autorizacion_1'></td>
						<td class='uno'><input type='text' class='entregado' id='wentregado_1' onblur='actualizar_abonado(this,event)'  onkeyup='pasarconenterabonos1(this,event)' numero='1' value='".number_format((double)0,2,'.',',')."'></td>
						<td class='uno'><input type='text' class='abonado' id='abonado_1' onblur='actualizar_abonado2(this,event)'  onkeyup='pasarconenterabonos2(this,event)' numero='1' value='".number_format((double)0,2,'.',',')."'  ></td>
						<td class='uno'><input  type='text' id='Saldo_1' class='saldo' value='".number_format((double)$wsaldo,2,'.',',')."' disabled></td>
						<td class='uno'><div id='div_select_banco_1'><select>";

						$qbancos=	"  	SELECT bancod, bannom "
						."    FROM ".$wbasedato."_000069, ".$wbasedato."_000023 "
						."   WHERE fpacod = '11'"
						."     AND fpacba = bancod ";

						$resbancos = mysql_query($qbancos,$conex) or die (mysql_errno()." - ".mysql_error());


						while($rowbancos = mysql_fetch_array($resbancos))
						{
							echo "<option value='".$rowbancos['bancod']."' >".$rowbancos['bannom']."</option>";

						}

						echo"</select></div></td>
						<td class='uno'></td>";
			echo "</tr>";

			// if($hacefactura =='on')
			// {
				echo"<tr ><td align='left' class='encabezadoTabla'>Observacion General: </td><td class='fila1' align='left' colspan='10' ><textarea rows='1' cols='200' id='textareaobservacio'></textarea></td></tr>
				</table><table >
				<tr class='fila2'><td colspan='6'></td><td ><b>Cambio:</b></td><td><input type='input' disabled id='wcambio_abonos' value='".number_format((double)0,2,'.',',')."'></td></tr>
				</table><br>";
			// }
			echo "<table align='center'><tr ><td><b><font style='font-size:11px'>Clave:</font><input  type='password' id='claverecibo'  style='font-size:11px; size:6; width:50px'  ><input style='font-size:11px; size:6' type='button' id='grabar_abono' value='grabar' onclick='grabar_abonos()'></td></tr></table><br><center></center>";
			echo "</div>
			<input type='button' id='grabar_abono2' style='display:none' value='Imprimir_documento' onclick='imprimir_documento_rec()'>";
			echo "<br><div id='imprimir_rec'></div>";
			echo "<div id='msjAlerta1' style='display:none;'>
				<br>
				<img src='../../images/medical/root/Advertencia.png'/>
					<br><br><div id='textoAlerta1'></div><br><br>
				</div>";
			echo"</fieldset>";
			break;
		}
		case "grabar_abonos" :
		{

			// valido clave de grabacion


			$q =  " SELECT Cjecla "
			   ."   FROM ".$wbasedato."_000030 "
			   ."  WHERE cjeusu = '".$key."'"
			   ."    AND cjeest = 'on' ";

			 $res = mysql_query($q,$conex);
			 $num = mysql_num_rows($res);
			 $clavesiguales = false;
			 if ($num > 0)
			 {
				$row = mysql_fetch_array($res);
				if ($row['Cjecla'] == $wclave)
				{
					 $clavesiguales = true;
				}

			 }

			 if($clavesiguales)
			 {

					$q =  " SELECT cjecco, cjecaj, cjetin, cjetem "
					   ."   FROM ".$wbasedato."_000030 "
					   ."  WHERE cjeusu = '".$key."'"
					   ."    AND cjeest = 'on' ";

					$res = mysql_query($q,$conex);
					$num = mysql_num_rows($res);
				    if ($num > 0)
				    {
					  $row = mysql_fetch_array($res);

					  $pos = strpos($row[0],"-");
					  $wcco = substr($row[0],0,$pos);
					  $wnomcco = substr($row[0],$pos+1,strlen($row[0]));

					  $pos = strpos($row[1],"-");
					  $wcaja = substr($row[1],0,$pos);
					  $wnomcaj = substr($row[1],$pos+1,strlen($row[1]));

			        }


					// numero de consecutivo cambia cuando el valor de la venta
					$tomoconsecutivo = 'tabla_3';
					$tomoconsecutivo = consultarAliasPorAplicacion($conex, $wemp_pmla, "ConsecutivoRecibos");

					if( $tomoconsecutivo == '000040')
					{
						 $wfuerec = '30';
						 $wnrorec = traerNumeroConsecutivo2($wfuerec );
						 echo $wnrorec;
					}


					$wfueffa = '20';

					$wnrofac = '';

					$querytienefac =  " SELECT Fdefac
										 FROM   ".$wbasedato."_000019
										 WHERE  Fdeffa = '".$wfueffa."'
										   AND  Fdenve = '".$wnum_venta."'";

					$restiene = mysql_query($querytienefac,$conex);


					if($rowtienefac = mysql_fetch_array($restiene))
					{
						$wnrofac = $rowtienefac['Fdefac'];

					}

					$wfecha=date("Y-m-d");
					$hora = (string)date("H:i:s");
					$q= " INSERT INTO ".$wbasedato."_000020 (   Medico       	,   Fecha_data		,   Hora_data	,  renfue    	,  rennum    	,  renvca                          		,   rencod  	,   rennom     	,   rencaj		,      renusu      ,   rencco  			, renest		,   renfec    		, renobs					, Seguridad       ) "
					  ."                            VALUES 		('".$wbasedato."'	,'".$wfecha."'	,'".$hora."' 	,'".$wfuerec."'	,'".$wnrorec."'	,'".number_format($wtotpag,0,'.','')."'	,'".$wemp."'	,'".$wnomemp."'	,'".$wcaja."'	,'".$key."'			,'".$wcco."'		, 'on'  		,'".$wfecha."'		, '".$wobservacion."'    	, 'C-".$key."')";




					$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		   //===================================================================================================================================
		   //===================================================================================================================================


				    $q= " INSERT INTO ".$wbasedato."_000021 (  	 Medico       	,   Fecha_data	,   Hora_data	,  rdefue    	,  rdenum     	,  rdecco  		,   rdefac     		,   rdevta     		,  rdevca               						, rdeest		, rdecon		, rdevco	,    rdeffa     		,  rdesfa          		, rdehis			, rdeing	, rdereg	, rdeccc	,    rdemiv      	, rdeglo, Seguridad        ) "
					   ."                            VALUES ('".$wbasedato."'	,'".$wfecha."'	,'".$hora."' 	,".$wfuerec."	,".$wnrorec."	,'".$wcco."'	,'".$wnrofac."'		,'".$wnum_venta."'	,'".number_format($wtotpag,0,'.','')."'			 ,'on'   		, ''    		, '0'     	, '".$wfueffa."'		,'".$wsaldototal."'		, ''    			, ''    	, '0'    	, ''    	, '".$wnromvto."'	, ''    , 'C-".$key."')";
				    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());



					$qtienefactura = "SELECT Vennfa
										FROM ".$wbasedato."_000016
									   WHERE  Vennum 	= '".$wnum_venta."'
										 AND  Vencco	='".$wcco."'";

					$res1 = mysql_query($qtienefactura,$conex);

					$tiene_factura='no';
					$numero_de_factura='';
					if ($row1 = mysql_fetch_array($res1))
					{
						$tiene_factura='si';
						$numero_de_factura=$row1['Vennfa'];
					}

					if($tiene_factura=='si')
					{
						//--Actualizo el valor del saldo en la tabla 18
						$qupdatesaldo= "UPDATE  ".$wbasedato."_000018
										   SET  Fensal = (Fensal - '".$wtotpag."'),
												Fenrbo  = (Fenrbo + '".$wtotpag."')
										 WHERE  Fenfac= '".$numero_de_factura."' ";

						$err5 = mysql_query($qupdatesaldo,$conex) or die(mysql_errno().":".mysql_error());

					}

					$wvectordatos = json_decode(str_replace("\\", '',  $wvectordatos), TRUE);

					grabarPagos($wvectordatos, $wfecha, $hora, $wfuerec, $wnrorec, $wcco, $wcaja,  $key);
					//echo $html;

			 }
			 else
			 {
				echo "Clave Erronea";

			 }
			 break;

		}
		case "AgregarFormadePago":
		{

			//numerohidden

			echo"<tr class='fila2' class='tr_forma_pago' style='font-size: 11px' >
						<td><select class='formaspago' id='sel_formapago_".$numerohidden."' numero='".$numerohidden."' onchange='cambiarformapago(this)' >";

						// query que trae de la tabla "wbasedato"_000023 , las formas de pago que se realizan a la entidad
						$queryformaspago = "SELECT Fpades, Fpacod
											  FROM ".$wbasedato."_000023
											 WHERE Fpaest = 'on' " ;

						$resformas = mysql_query($queryformaspago,$conex) or die (mysql_errno()." - ".mysql_error());



						while($rowformas = mysql_fetch_array($resformas))
						{
							if($rowformas['Fpacod']=='11')
							{
								echo "<option value='".$rowformas['Fpacod']."' selected>".$rowformas['Fpades']."</option>";
							}
							else
							{
								echo "<option value='".$rowformas['Fpacod']."' >".$rowformas['Fpades']."</option>";
							}

						}
						echo"</select></td>
						<td class='uno' ><input id='docanexo_".$numerohidden."' type='text'></td>
						<td class='uno'><input type='text' id='observacion_".$numerohidden."'></td>
						<td ><select class='dos_".$numerohidden."' style='display : none'  id='datosbanco_".$numerohidden."'>";

						 $q = " SELECT bancod, bannom "
								 ."   FROM ".$wbasedato."_000069 "
								 ."  WHERE banest  = 'on' "
								 ."    AND bancag != 'on'";

						$resu = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());




		 		        while ($banc = mysql_fetch_array($resu))
						{

							if($banc['bancod']=="0TR")
							{
								echo "<option value='".$banc['bancod']."'  selected=selected >".$banc['bannom']."</option>";
							}
							else
							{
								echo "<option value='".$banc['bancod']."' >".$banc['bannom']."</option>";

							}
						}

						echo"</select></td>
						<td  ><select class='dos_".$numerohidden."' style='display : none'  id='ubicacion_".$numerohidden."'>";

						$otro1='2-Otras plazas';
				  	    $otro2='1-Local';

						//<option value='' selected>...seleccione</option >
			            echo "
							  <option value='".$otro2."' selected=selected>".$otro2."</option>
							  <option value='".$otro1."' >".$otro1."</option>
						";


						echo "</select></td>
						<td  ><input class='dos_".$numerohidden." autorizacion' style='display : none' type='text' id='autorizacion_".$numerohidden."'></td>
						<td class='uno'><input type='text' id='wentregado_".$numerohidden."' class='entregado' onblur='actualizar_abonado(this,event)'  onkeyup='pasarconenterabonos1(this,event)' numero='".$numerohidden."' value='".number_format((double)0,2,'.',',')."'></td>
						<td class='uno'><input class='abonado' type='text' id='abonado_".$numerohidden."' onblur='actualizar_abonado2(this,event)'  onkeyup='pasarconenterabonos2(this,event)' numero='".$numerohidden."' value='".number_format((double)$wsaldo,2,'.',',')."'></td>
						<td class='uno'><input  type='text' id='Saldo_".$numerohidden."' class='saldo' value='".number_format((double)$wsaldo,2,'.',',')."' disabled></td>
						<td class='uno'><div id='div_select_banco_".$numerohidden."'><select>";

						$qbancos=	"  	SELECT bancod, bannom "
						."    FROM ".$wbasedato."_000069, ".$wbasedato."_000023 "
						."   WHERE fpacod = '11'"
						."     AND fpacba = bancod ";

						$resbancos = mysql_query($qbancos,$conex) or die (mysql_errno()." - ".mysql_error());


						while($rowbancos = mysql_fetch_array($resbancos))
						{
							echo "<option value='".$rowbancos['bancod']."' >".$rowbancos['bannom']."</option>";

						}

						echo"</select></div></td>
						<td class='uno'></td>";
			echo "</tr>";

			break;
		}
		case "cambiarformapago" :
		{


			echo "<select >";
			$qbancos=	"  	SELECT bancod, bannom "
						."    FROM ".$wbasedato."_000069, ".$wbasedato."_000023 "
						."   WHERE fpacod = '".$wfpaini."'"
						."     AND fpacba = bancod ";

			$resbancos = mysql_query($qbancos,$conex) or die (mysql_errno()." - ".mysql_error());


			while($rowbancos = mysql_fetch_array($resbancos))
			{
				echo "<option value='".$rowbancos['bancod']."' >".$rowbancos['bannom']."</option>";

			}
			echo "</select>";
			break;
		}
		case "detalle_venta":
		{


			echo "<table>
					<tr class='encabezadoTabla'>
						<td>Detalle de Venta</td>
					</tr>
					<tr class='fila1'>
							<td>Articulo</td>
							<td>Descripcion</td>
							<td>Presentacion</td>
							<td>Cantidad</td>
							<td>V/r Unit.Con IVA</td>
							<td>% Descuento</td>
							<td>Descuento Total Art.</td>
							<td>Total Venta ConDscto Sin IVA</td>
							<td>% Iva</td><td>Valor Iva.</td>
							<td>Total</td>
					<tr>";
			$query =" SELECT *
						FROM ".$wbasedato."_000017
					   WHERE Vdenum = '".$numventa."'";


			$res = mysql_query($query,$conex);


			while($row = mysql_fetch_array($res))
			{
				echo "
					<tr class='fila2'>
							<td>".$row['Vdeart']."</td>
							<td></td>
							<td></td>
							<td>".$row['Vdecan']."</td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
					<tr>";

			}
			echo"</table>";
			break;
		}
		case "buscarordenlab" :
		{
			$query = "SELECT ordnro
						FROM ".$wbasedato."_000133
					   WHERE ordven='".$wordenlab."'";


			$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO VENTAS");
			$row = mysql_fetch_array($err1);

			$numero_orden =$row[0];
			$existe = "0";
			if($numero_orden != "")
			{
				 $existe = "1";
			}
			echo $existe;
			break;
		}
		case "facturar":
		{

				$sigo= true;
				foreach ($vectorventas as $clave => $valor) {

					$query = "SELECT Vennfa
								FROM ".$wbasedato."_000016
							   WHERE Vennum='".$valor."'";


						$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO VENTAS");
						$row = mysql_fetch_array($err1);

						$nrovta=$row[0];
						if($nrovta == "" or  $nrovta == " "  or $nrovta == "NO APLICA" )
						{

							$sigo=true;

						}
						else
						{
							$sigo=false;
						}


				}
				// echo $query;
				// return;


				// $data[0][10] = numero de la factura

				$query = "lock table ".$wbasedato."_000016 LOW_PRIORITY WRITE ";
				$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO CONSECUTIVO");

				$query = "SELECT Vennfa
							FROM ".$wbasedato."_000016
						   WHERE Vennum='".$wnum_venta."'";


				$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO VENTAS");
				$row = mysql_fetch_array($err1);

				$nrovta=$row[0];

				$query = " UNLOCK TABLES";
				$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO");

				$query = "SELECT Ccofai,Ccofaf
								FROM ".$wbasedato."_000003
							   WHERE Ccocod='".$wccofacturacion."'";

				$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO CONSECUTIVO");
				$row = mysql_fetch_array($err1);
				$wdoct=$row[0];
				$wdoctf=$row[1];
				$wdoct = ($wdoct * 1) +1;
				if($wdoct <= $wdoctf)
				{
					$puedegenerarfactura = true;
				}
				else
				{
				   $puedegenerarfactura = false;
				   echo "<center>¡¡¡¡Para poder hacer facturas debe actualizar el numero de facturas permitidas!!!</center>";
				   return;
				}

				if($sigo ==true)
				{

					$query = "lock table ".$wbasedato."_000003 LOW_PRIORITY WRITE ";
					$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO CONSECUTIVO");



					//traerConsecutivofacturacion
					/*
					$tomoconsecutivo = 'tabla_3';
					$tomoconsecutivo = consultarAliasPorAplicacion($conex, $wemp_pmla, "ConsecutivoFacturas");

					if( $tomoconsecutivo == '000040')
					{

						//Aca traigo el consecutivo de la fuente
						$q= "    SELECT carcon "
							."     FROM ".$wbasedato."_000040 "  //maestro de fuentes
							."    WHERE carfue ='".$wfuente."' "
							."      AND carest = 'on' ";

						$res1 = mysql_query($q,$conex) or die( " - Error en el query $q - ".mysql_error() );
						$num1 = mysql_num_rows($res1);
						$row1 = mysql_fetch_array($res1);
						if ($row1[0] != "")
						$wnrodoc=$row1[0]+1;  //valor del consecutivo de la fuente mas 1, para incrementar el numero, pero que no me cancele el pedido

						// se aumentaria el valor del consecutivo
						$q= "    UPDATE  ".$wbasedato."_000040 "  //maestro de fuentes
							."      SET  carcon = '".$wnrodoc."'
								  WHERE  carfue ='".$wfuente."' "
							."      AND carest = 'on' ";

						$res = mysql_query($q,$conex) or die( " - Error en el query $q - ".mysql_error() );



					}*/



					$query =  " UPDATE ".$wbasedato."_000003
								   SET Ccofai = Ccofai + 1
								 WHERE Ccocod='".$wccofacturacion."'";

					$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());



					$query = "SELECT Ccofai,Ccofaf,Ccoffa,Ccopfa
								FROM ".$wbasedato."_000003
							   WHERE Ccocod='".$wccofacturacion."'";


					$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO CONSECUTIVO");
					$row = mysql_fetch_array($err1);
					$wdoct=$row[0];
					$wdoctf=$row[1];
					$wfuente=$row[2];
					$wprefijo=$row[3];
					$wfecha=date("Y-m-d");



					$query = "UNLOCK TABLES";


					$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO");



					if($wdoct <= $wdoctf and $wfac > 0)
					{


						$query  = " SELECT cfgnit, cfgnom, cfgtre, cfgtel, cfgdir, cfgfran, cfgfian, cfgffan, cfgran, cfgfcar, cfgfrac, cfgfiac, cfgffac, cfgrac, cfgpin, cfgmai, cfgdom ";
						$query .= "   FROM ".$wbasedato."_000049 ";
						$query .= "  WHERE cfgcco = '".$wccofacturacion."'";
						$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
						$row1 = mysql_fetch_array($err1);
						if ($row1[9] > $wfecha)
						{
							$wnrores=$row1[8];  //Nro Resolucion Anterior
							$wfecres=$row1[5];  //Fecha Resolucion Anterior
							$wfacini=$row1[6];  //Factura Inicial Anterior
							$wfacfin=$row1[7];  //Factura Final Anterior
						}
						else
						{
							$wnrores=$row1[13];  //Nro Resolucion Actual
							$wfecres=$row1[10];  //Fecha Resolucion Actual
							$wfacini=$row1[11];  //Factura Inicial Anterior
							$wfacfin=$row1[12];  //Factura Final Anterior
						}
						 $wresolucion= "Resolución Nro: ".$wnrores." del ".$wfecres.", <BR>"
								   ."factura ".$wfacini." a la factura ".$wfacfin."<BR>"
								   ."Esta factura cambiaria de compraventa se asimila en <BR>"
								   ."todos sus efectos a una letra de cambio, Art. 621 y <BR>"
								   ."SS, 671 y SS 772, 773, 770 y SS del código de comercio.<BR>"
								   ."Factura impresa por computador cumpliendo con los<BR>"
								   ."requisitos del Art. 617 del E.T.<BR>";

						$wsw=0;
						$ventas=array();
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						//INSTRUCCION MODIFICADA EN 2010-05-11  se adiciono Emptem
						$query = "SELECT Empnit, Empres, Emptem
									FROM ".$wbasedato."_000024
								   WHERE Empcod='".$wempresafacturacion."'";

						$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO EMPRESAS");
						$row = mysql_fetch_array($err1);

						$selectnombre ="SELECT *
										  FROM ".$wbasedato."_000041
										 WHERE Clidoc = '' ";

						//INSTRUCCION MODIFICADA EN 2010-05-11
						if($wnitpersonal=='no')
						{
							$nitpac = $row[0];
						}
						else
						{
							$nitpac = $wnitpersonal ;
						}
						$query = "INSERT ".$wbasedato."_000018
											(medico		   	,fecha_data  	,hora_data	, Fenano				, Fenmes				, Fenfec		, Fenffa			, Fenfac					, Fentip		, Fennit		, Fencod					, Fenres		, Fenval		, Fenviv		, Fencop	, Fencmo		, Fendes	, Fenabo, Fenvnd, Fenvnc, Fensal, Fenest, Fencre, Fenpde, Fenrec, Fentop, Fenhis, Fening, Fenesf, Fenrln, Fencco, Fenrbo, Fenimp, Fendpa		, Fennpa					, Fendev	, seguridad)
									VALUES  ('".$wbasedato."','".$fecha."'	,'".$hora."',".substr($fecha,0,4)."	,".substr($fecha,5,2)."	,'".$fecha."'	,'".$wfuente."'		,'".$wprefijo."-".$wdoct."'	,'".$row[2]."'	,'".$row[0]."'	,'".$wempresafacturacion."'	,'".$row[1]."'	,".$wfac."		,".$wiva."		,".$wcop."	,".$wcmo."		,".$wdes."	,	0	,	0	,	0	,".$wfac.",'on',1,0,100,0,'','','GE','".$wresolucion."','".$wccofacturacion."',0,'on'					,'".$nitpac."'	,'".$wnombrepersonal."'		,0			,'C-".$key."')";
						$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());

						// actualizar saldo en la 18 segun el valor  de los recibos cambiar
						$valor_recibos = 0;
						$cuantos = 0;
						foreach ($vectorventas as $clave => $valor)
						{


								$wsw++;
								$ventas[$wsw]=$valor;

								// update de la tabla 21  campos  	Rdefac y Rdeffa
								$qactualizar21 = "UPDATE ".$wbasedato."_000021 SET Rdefac = '".$wprefijo."-".$wdoct."' , Rdeffa='".$wfuente."' WHERE Rdevta='".$valor."' ";
								$err4 = mysql_query($qactualizar21,$conex) or die(mysql_errno().":".mysql_error());
								//-------------------------------

								$cuantos++;
								//Sumo los recibos de las ventas hechas
								$q = "SELECT Rdevca
										FROM ".$wbasedato."_000021
									   WHERE Rdevta='".$valor."'
										 AND Rdeest ='on' ";

								$res = mysql_query($q,$conex) or die("ERROR CONSULTANDO EMPRESAS");
								while($row = mysql_fetch_array($res))
								{
									$valor_recibos = $valor_recibos + $row['Rdevca'];
								}
								//-------------------------------------

								//Actualizo la tabla uvglobal_000019 relacionando asi las ventas con la factura
								$query = "insert ".$wbasedato."_000019 (medico,fecha_data,hora_data, Fdeffa, Fdefac, Fdenve, Fdeest, seguridad) values ('".$wbasedato."','".$fecha."','".$hora."','".$wfuente."','".$wprefijo."-".$wdoct."','".$valor."','on','C-".$key."')";
								$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());

								// Actualizo la tabla uvglobal_000016 poniendo en el detalle de venta la factura correspondiente
								$query =  " update ".$wbasedato."_000016 set Vennfa = '".$wprefijo."-".$wdoct."', Venffa='".$wfuente."'  where Vennum='".$valor."'";
								$err3 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());

								// Actualizo las ordenes de laboratorio en la tabla uvglobal_000133
								$query = "UPDATE ".$wbasedato."_000133 SET ordffa = '".$wfuente."' , ordfac = '".$wprefijo."-".$wdoct."' WHERE ordven ='".$valor."'";
								$err4 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());

						}
						$recibo = true;
						if($wfac - $valor_recibos == 0)
						{
							$recibo=false;

						}
						//--Actualizo el valor del saldo en la tabla 18
						$q= "UPDATE  ".$wbasedato."_000018
								SET  Fensal = (Fenval - '".$valor_recibos."'),
									 Fenrbo  = '".$valor_recibos."'
							  WHERE  Fenfac= '".$wprefijo."-".$wdoct."' ";

						$err5 = mysql_query($q,$conex) or die(mysql_errno().":".mysql_error());

						//-------------------
						if($wsw > 0)
						{
							$items=" IN (".chr(34).$ventas[1].chr(34);
							for ($i=2;$i<=$wsw;$i++)
							{
								$items .= ",".chr(34).$ventas[$i].chr(34);
							}
							$items .= ")";
							$query = "SELECT ".$wbasedato."_000016.Vencco,MID(".$wbasedato."_000001.Artgru,1,LOCATE( '-', ".$wbasedato."_000001.Artgru )-1),sum(".$wbasedato."_000017.Vdevun*".$wbasedato."_000017.Vdecan),round(sum(((".$wbasedato."_000017.Vdevun*".$wbasedato."_000017.Vdecan)/(1+(".$wbasedato."_000017.Vdepiv/100)))*(".$wbasedato."_000017.Vdepiv/100)),0) ";
							$query .= " FROM ".$wbasedato."_000017,".$wbasedato."_000016,".$wbasedato."_000001 ";
							$query .= "WHERE ".$wbasedato."_000017.vdenum ".$items;
							$query .= "  AND ".$wbasedato."_000017.vdenum = ".$wbasedato."_000016.Vennum  ";
							$query .= "  AND ".$wbasedato."_000017.vdeart = ".$wbasedato."_000001.artcod  ";
							$query .= "GROUP by 1,2 ";
							$query .= "ORDER by 1 ";
							$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO VENTASX CPTO");
							$num1 = mysql_num_rows($err1);
							if($num1 > 0)
							{
								for ($i=0;$i<$num1;$i++)
								{
									$row1 = mysql_fetch_array($err1);
									$query = "INSERT ".$wbasedato."_000065 (	medico		,		fecha_data	,hora_data		, 	Fdefue		, 		Fdedoc					, Fdecco		, Fdecon		, Fdevco, Fdeter, Fdepte, Fdevde, Fdesal, Fdeest, fdeffa, fdefac, fdeviv, seguridad)
																	values ('".$wbasedato."','".$fecha."'		,'".$hora."'	,'".$wfuente."'	,'".$wprefijo."-".$wdoct."'		,'".$row1[0]."'	,'".$row1[1]."'	,".$row1[2].",'',0,0,0,'on','".$wfuente."','".$wprefijo."-".$wdoct."',".$row1[3].",'C-".$key."')";
									$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
								}
							}
						}
						if($wsw > 0)
						{
							$items=" IN (".chr(34).$ventas[1].chr(34);
							for ($i=2;$i<=$wsw;$i++)
							{
								$items .= ",".chr(34).$ventas[$i].chr(34);
							}
							$items .= ")";
							$query  = " SELECT ".$wbasedato."_000017.id,sum(".$wbasedato."_000017.Vdevun*".$wbasedato."_000017.Vdecan) ";
							$query .= " FROM ".$wbasedato."_000017 ";
							$query .= " WHERE ".$wbasedato."_000017.vdenum ".$items;
							$query .= " GROUP by 1 ";
							$query .= " ORDER by 1 ";


							$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO VENTAS X ID");
							$num1 = mysql_num_rows($err1);
							if($num1 > 0)
							{
								for ($i=0;$i<$num1;$i++)
								{
									$row1 = mysql_fetch_array($err1);
									$query = "INSERT ".$wbasedato."_000066 (medico,fecha_data,hora_data, Rcfffa, Rcffac, Rcfreg, Rcfval, Rcfest, Rcftip, seguridad) values ('".$wbasedato."','".$fecha."','".$hora."','".$wfuente."','".$wprefijo."-".$wdoct."','".$row1[0]."',".$row1[1].",'on','R','C-".$key."')";
									$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
								}
							}




						}

						//echo "FACTURA ".$wprefijo."-".$wdoct." GENERADA";
						// aqui
						$query = "SELECT Empnit, Empres, Empnom , Empraz
									FROM ".$wbasedato."_000024
								  WHERE Empcod='".$wempresafacturacion."'";

						$err1 	= mysql_query($query,$conex) or die("ERROR CONSULTANDO EMPRESAS");
						$row 	= mysql_fetch_array($err1);

						echo "<br><br><div id='resultadofactura' style='display:none'><table align='center'>
									<tr class='encabezadoTabla'><td colspan='2' align='center'><font size='5'>Factura ".$wprefijo."-".$wdoct." Generada</font></td></tr>";


									if($recibo)
									{
										echo "<tr class='fila1'><td>Hay saldo desea generar el recibo</td><td><input type='button' onclick='ir_a_recibos()' value='Generar Recibo'></td></tr>";

									}

									foreach ($vectorventas as $clave => $valor)
									{
										echo "<tr class='fila2'><td>Factura: ".$wprefijo."-".$wdoct." - Venta: ".$valor." </td><td><input type='button' onclick='ir_a_imprimirfac(\"".$valor."\")' value='Imprimir Factura'></td></tr>";
									}

									echo"
							  </table><br><center><input type='hidden' onclick='cerrar_imprimirfactura();' value='Cerrar'></center>
							  <input type=hidden id='wprefijorec'  value='".$wprefijo."'   >
							  <input type=hidden id='wnumfacrec'  value='".$wdoct."'   >
							  <input type=hidden id='wfuenterec'  value='".$wfuente."'   >
							  <input type=hidden id='wresrec'  value='".$wempresafacturacion." - ".$row['Empnit']." - ".$row['Empnom']." - ".$row['Empraz']."' >
							  </div>";

					}
							// echo "<br><br><div id='resultadofactura' style='display:none'><table align='center'>";

							$wccotrasladar = consultarAliasAplicacion($conex, $wemp_pmla, $wccofacturacion);
							//$wbodega = consultarAliasAplicacion($conex, $wemp_pmla, 'BodegaPrincipal');
							$wbodega = $wccofacturacion;
							$wconceptraslado = consultarAliasAplicacion($conex, $wemp_pmla, 'ConceptodeTraslado');
							$wsw=0;
							foreach ($vectorventas as $clave => $valor)
							{

								$wsw++;
								$ventas[$wsw]=$valor;
							}
							$wnromvto = 'true';
							$bandera_detalle = 'true';
							$wnromvtovar = 0;
							for ($i=1;$i<=$wsw;$i++)
							{
									$sql = "SELECT  Vdeart, Vdecan
											  FROM  ".$wbasedato."_000017
											 WHERE  Vdenum = '".$ventas[$i]."'";

									$err1 	= mysql_query($sql,$conex) or die("Error trayendo los items facturados");
									while ($row = mysql_fetch_array($err1))
									{


										//AVERIGUO SI EL ARTICULO DIGITADO PERTENECE A UN GRUPO QUE MUEVA INVENTARIOS O NO
									   //Si algún articulo de la venta no mueve inventarios este no queda grabado en las tablas de movimiento de inventario
									   //Pero si en las tablas de ventas
									   $q= "  SELECT gruinv "
										  ."    FROM ".$wbasedato."_000001, ".$wbasedato."_000004 "
										  ."   WHERE artcod = '".$row[0]."'"
										  ."     AND mid(artgru,1,instr(artgru,'-')-1) = grucod "
										  ."     AND artest = 'on' "
										  ."     AND gruest = 'on' ";
									   $res_gru = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
									   $num_gru = mysql_num_rows($res_gru);
									   if ($num_gru > 0)    //Si entra aca es porque el codigo digitado es externo. Entonces traigo el interno
										{
										   $row_gru = mysql_fetch_array($res_gru);
										   $WMUEINV_DET = $row_gru[0];
										}
										if($WMUEINV_DET == 'on')
										{
												//echo "<tr><td><br>hola".$ventas[$i]."</td><td>articulo ".$row['Vdeart']." cantidad ".$row['Vdecan']."</td></tr>";
												trasladoautomatico("", "", $wccotrasladar, $wconceptraslado, $wbodega, "", "", "", "", "", $row['Vdeart'], date("Y-m-d"), date("H:i:s"), "", $row['Vdecan'], $key, "", "" ,$bandera_detalle ,$wnromvtovar);

												// se actualiza el saldo en el centro de costo donde se traslado y se hace la venta

												$hora = (string)date("H:i:s");

												 $q_saldo	= "   UPDATE ".$wbasedato."_000007 "
															   ."    SET karexi = karexi - ".$row['Vdecan']
															   ."  WHERE karcco = '".$wbodega."'"
															   ."    AND karcod = '".$row['Vdeart']."'"
															   ."    AND karexi > 0 ";

												  $res_q_saldo 	= mysql_query($q_saldo,$conex) or die("Error actualizando saldo venta");

												if($wnromvto=='true')
												{
													// busco el concepto de venta y lo actualizo en su consecutivo
													$q= " UPDATE ".$wbasedato."_000008 "
													   ."    SET concon = concon + 1 "
													   ."  WHERE conmve = 'on' ";
													$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

													$q= " SELECT concon, concod "
													   ."   FROM ".$wbasedato."_000008 "
													   ."  WHERE conmve = 'on' ";
													$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
													$row_1 = mysql_fetch_array($err);
													$wnromvto=$row_1[0];
													$wconmvto=$row_1[1];


													 //GRABO EN LA TABLA DEL -- <ENCABEZADO> -- DEL **** MOVIMIENTO DE INVENTARIOS ****
													  $q= " INSERT INTO ".$wbasedato."_000010 (   Medico       ,   Fecha_data,   Hora_data,   menano      ,   menmes      ,   menfec    ,  mendoc       ,   mencon      ,   mencco     ,   menccd     , mendan, menpre, mennit, menusu, menfac						  , menest, Seguridad        ) "
														 ."                            VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".date("Y")."','".date("m")."','".$wfecha."','".$wnromvto."','".$wconmvto."','".$wbodega."','".$wbodega."', '.'   , 0     , '0'   , '.'   ,  '".$wprefijo."-".$wdoct."'   ,   'on', 'C-".$key."' )";
													  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
												}
												  //===================================================================================================================================
												  //===================================================================================================================================
												  //GRABO EN LA TABLA DEL -- <DETALLE> -- DE  **** MOVIMIENTO DE INVENTARIOS ****
												  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
												  //============================================================================================================================

												  //=========================================
												  //TRAIGO EL COSTO PROMEDIO DEL ARTICULO
												  $q= "SELECT karpro "
													 ."  FROM ".$wbasedato."_000007 "
													 ." WHERE karcco = '".$wbodega."'"
													 ."   AND karcod = '".$row['Vdeart']."'";
													 //."   AND karexi > 0 ";
												  $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
												  $row_cos = mysql_fetch_array($res2);

												  if ($row_cos[0] == "")
													 $row_cos[0]=0;

												  //=========================================
												  //GRABO EL DETALLE DEL ARTICULO
												  $q= " INSERT INTO ".$wbasedato."_000011 (   Medico       ,   Fecha_data,   Hora_data,   mdecon      ,  mdedoc       ,   mdeart     ,  mdecan      ,  mdevto                    , mdepiv , mdeest, Seguridad        ) "
													 ."                            VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".$wconmvto."','".$wnromvto."','".$row['Vdeart']."',".$row['Vdecan'].",".($row['Vdecan']*$row_cos[0]).", '0'    , 'on'  , 'C-".$key."')";
												  $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
										}


									}


							}
							// echo "</table></div>";
				}
				else
				{

					echo "<center>ERROR !!! ESTAS VENTAS YA FUERON FACTURADAS -- INTENTELO NUEVAMENTE!!!!</center>";
				}


			break;
		}
		case "buscardetalledeabono" :
		{


			 $q = " SELECT count(*) "
				  ."   FROM ".$wbasedato."_000021, ".$wbasedato."_000022 "
				  ."  WHERE rdevta = '".$wnumventa."'"
				  ."    AND rdefue = rfpfue "
				  ."    AND rdenum = rfpnum "
				  ."    AND rdeest = 'on' "
				  ."    AND rfpest = 'on' "
				  ."    AND rdecco = '".$wcco."'"
				  ."    AND rdecco = rfpcco ";

			 $aux2 = $q;
			 $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		     $num = mysql_num_rows($res);
		     $row = mysql_fetch_array($res);
		     $fk=$row[0];

			if ($fk > 0)
			{

				 $q = " SELECT rfpfpa, rfpdan, rfpobs, rfpvfp, fpades , ".$wbasedato."_000021.Fecha_data , Rdenum"
						  ."   FROM ".$wbasedato."_000021, ".$wbasedato."_000022, ".$wbasedato."_000023 "
						  ."  WHERE rdevta = '".$wnumventa."'"
						  ."    AND rdefue = rfpfue "
						  ."    AND rdenum = rfpnum "
						  ."    AND rdeest = 'on' "
						  ."    AND rfpest = 'on' "
						  ."    AND rfpfpa = fpacod "
						  ."    AND rdecco = '".$wcco."'"
						  ."    AND rdecco = rfpcco ";
				$aux = $q;

				$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				$num = mysql_num_rows($res);

				for ($i=1;$i<=$num;$i++)
				{
					$row = mysql_fetch_array($res);

					$wfpa[$i] = $row[0];
					$wdocane[$i] = $row[1];
					$wobsrec[$i] = $row[2];
					$wvalfpa[$i] = $row[3];
					$wfpades[$i] = $row[4];
					$fecha[$i] = $row[5];
					$numrec[$i] = $row[6];
				}

				$html.= "<tr><td class ='encabezadoTabla'># Recibo</td>";
				$html.= "<td class ='encabezadoTabla'>Fecha</td>";
				$html.="<td class ='encabezadoTabla'>Forma De Pago</td>";
				$html.= "<td class ='encabezadoTabla'>Doc.Anexo</td>";
				$html.= "<td class ='encabezadoTabla'>Observ.</td>";
				$html.= "<td class ='encabezadoTabla'>Valor</td></tr>";



				for ($i=1;$i<=$fk;$i++)
				{
					if ($wvalfpa[$i] > 0)
					{
					 $html.= "<tr class='".$color."'>";

					 $html.=  "<td >".$numrec[$i]."</td>";
					 $html.=  "<td >".$fecha[$i]."</td>";
					 $html.=  "<td >".$wfpa[$i]." - ".$wfpades[$i]."</td>";
					 $html.=  "<td >".$wdocane[$i]."</td>";
					 $html.=  "<td >".$wobsrec[$i]."</td>";
					 $html.=  "<td >".number_format($wvalfpa[$i],0,'.',',')."</td>";

					 $html.=  "</tr>";
					 $totalabonos = $totalabonos + $wvalfpa[$i];
					}
				}
			}
            echo $html;

			break;

		}
	}
	return;

}


if((isset($consultaAjax) && $consultaAjax=='envio') || (isset($consultaAjax2) && $consultaAjax2=='envio'))
{
	header("Content-Type: text/html;charset=ISO-8859-1");
}
// echo '<pre>'.print_r($_GET,true).'</pre>';
// echo '<pre>'.print_r($_POST,true).'</pre>';

function consultarAliasAplicacion($conexion, $codigoInstitucion, $nombreAplicacion){


	 $q = "   SELECT Detval
				FROM root_000051
			   WHERE Detemp = '".$codigoInstitucion."'
				 AND Detapl = '".$nombreAplicacion."'";
	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$alias = "";
	if ($num > 0)
	{
		$rs = mysql_fetch_array($res);

		$alias = $rs['Detval'];
	} else {
		return false;
	}
	return $alias;
}


function traerNumeroConsecutivo2($wfuente )
{
	global $conex;
	global $wbasedato;
	//global $wemp_pmla;

	//$wfuente = '30';

	//Aca traigo el consecutivo de la fuente
	$q= "    SELECT carcon "
		."     FROM ".$wbasedato."_000040 "  //maestro de fuentes
		."    WHERE carfue ='".$wfuente."' "
		."      AND carest = 'on' ";

	$res1 = mysql_query($q,$conex) or die( " - Error en el query $q - ".mysql_error() );
	$num1 = mysql_num_rows($res1);
	$row1 = mysql_fetch_array($res1);
	if ($row1[0] != "")
	$wnrodoc=$row1[0]+1;  //valor del consecutivo de la fuente mas 1, para incrementar el numero, pero que no me cancele el pedido

	// se aumentaria el valor del consecutivo
	$q= "    UPDATE  ".$wbasedato."_000040 "  //maestro de fuentes
		."      SET  carcon = '".$wnrodoc."'
			  WHERE  carfue ='".$wfuente."' "
		."      AND carest = 'on' ";

	$res = mysql_query($q,$conex) or die( " - Error en el query $q - ".mysql_error() );


	return $wnrodoc;
	//---------------




}

function grabarPagos($wvectordatos, $wfecha, $hora, $wfuenCod, $wnrodoc, $wcco, $wcaja,  $wusuario)
{
	global $wbasedato;
	global $conex;

	//print_r ($wvectordatos);
	foreach ($wvectordatos as $key ) {

	//--- regla par uvglobal
	//----pago en efectivo
	//---- si hay pago en efectivo el banco es 999

	$datosbanco = $key['datosbanco'];
	if(trim($key['formapago'])=='11')
	{
		$datosbanco ='999';
	}
	//------------------
	$q= " INSERT INTO ".$wbasedato."_000022 (   Medico       , Fecha_data,   Hora_data,    rfpfue              ,   rfpnum    ,                rfpfpa              ,  rfpvfp        ,   rfpdan         ,   rfpobs     				, rfpest,   rfpcco  	,  rfppla					,    rfpaut						,       rfpecu	,  rfpcaf			,  rfpban					,   rfpbai					,   Seguridad        ) "
	."                             VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".trim($wfuenCod)."',".strtoupper($wnrodoc).",'".trim($key['formapago'])."',".$key['valorpago'].",'".$key['anexo']."','".$key['observacion']."'	, 'on'  ,'".$wcco."'	, '".$key['ubicacion']."'	, '".$key['autorizacion']."'	, 		'S'		, '".$wcaja."'	,  '".$datosbanco."'	, '".$key['datosbanco']."'	, 'C-".$wusuario."') ";


	//echo $key['valorpago'];
	$resfpa = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HAN PODIDO GRABAR CORRECTAMENTE LAS FORMAS DE PAGO ".mysql_error());
	}
}


function trasladoautomatico($wexiste, $wdevol, $wbodega, $wconceptraslado, $wccogra, $wvaltar, $wno2, $waprovecha, $wconinv, $wpaquete, $wprocod, $wfecha, $hora, $wconmvto, $wcantidad, $wusuario, $whistoria, $wing ,&$bandera_detalle,&$wnromvtovar)
{

  global $conex;
  global $wbasedato;


//TRAIGO LAS EXISTENCIAS DEL CENTRO DE COSTOS DE LA VARIABLE $wbodega
$q= "SELECT karexi, Karcod, Karcco "
   ."  FROM ".$wbasedato."_000007 "
   ." WHERE karcco = '".$wbodega."'"
   ."   AND karcod = '".$wprocod."'";
$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
$row_exik = mysql_fetch_array($res);
$wexisteKardex = $row_exik['karexi'];

//TRAIGO EL NOMBRE DEL CENTRO DE COSTOS ASOCIADO A LA VARIABLE $wbodega
$q= "SELECT Ccocod, Ccodes "
   ."  FROM ".$wbasedato."_000003"
   ." WHERE Ccocod = '".$wbodega."'";
$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
$row = mysql_fetch_array($res);
$wnombrecco = strtoupper($row['Ccodes']);

// VALIDO SI LA CANTIDAD DE PRODUCTOS ES MAYOR A LA QUE HAY EN EL KARDEX, SI ES MAYOR SE DETIENE LA FUNCION Y NO MUESTRA EL MENSAJE DEL
// INVENTARIO DEL KARDEX ACTUAL PARA ESTE PRODUCTO
if ($wcantidad > $wexisteKardex)
	{
	return false;
	}

//SI LAS DEVOLUCIONES PARA LA HISTORIA E INGRESO ASOCIADAS A UN ARTICULOS SON IGUALES A CERO, DETIENE LA FUNCION trasladoautomatico
if ($wdevol=='on')
	{
	return false;
	}
elseif($wexiste == 0 and $wdevol=='on')
{
return false;
}

//VALIDO SI LA CANTIDAD DE PRODUCTOS EN EL KARDEX EN IGUAL A CERO O SI LA CANTIDAD EXISTENTE EN EL KARDEX EN MENOR A LA SOLICITADA
//SI ES ASI SE DETIENE LA FUNCION
if ($wexisteKardex == 0 or $wexisteKardex < $wcantidad)
	{
	echo "<script>
			alert ('NO HAY INVENTARIO EN $wnombrecco PARA ESTE PRODUCTO');
		  </script>";
	return false;
	}

 // BLOQUEA LAS TABLAS DEL KARDEX PARA ACTUALIZAR LOS CENTROS DE COSTOS DE TRASLADO
  $q = "lock table ".$wbasedato."_000007 LOW_PRIORITY WRITE";
  $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

		//ACTUALIZO EN LA TABLA DEL -- <SALDOS EN LINEA> -- DEL  **** KARDEX ****
  if ($wexiste >= 0 and $wexisteKardex > 0 and $wdevol!='on')
		{
		$q= " UPDATE ".$wbasedato."_000007 "
		   ."    SET karexi = karexi - ".$wcantidad
		   ."  WHERE karcco = '".$wbodega."'"
		   ."    AND karcod = '".$wprocod."'";
		$res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


		 //Recalculo el costo promedio del articulo en el centro de costos destino
		$q= "SELECT karexi, karpro "
			."  FROM ".$wbasedato."_000007 "
			." WHERE karcco = '".$wbodega."'"
			."   AND karcod = '".$wprocod."'";
		$res_cos = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$row_cos = mysql_fetch_array($res_cos);

		$wexist_actuales_origen = $row_cos[0];
		$wcosto_pro_actual_origen = $row_cos[1];

		$q7= "SELECT karexi, karpro "
			."  FROM ".$wbasedato."_000007 "
			." WHERE karcco = '".$wccogra."'"
			."   AND karcod = '".$wprocod."'";
		$res_cos = mysql_query($q7,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q7." - ".mysql_error());
		$row_cos = mysql_fetch_array($res_cos);

		$wexist_actuales_destino = $row_cos[0];
		$wcosto_pro_actual_destino = $row_cos[1];

		//Nuevo costo promedio del articulo en el centro de costos a grabar
		$wnuevocospro = (($wexist_actuales_destino * $wcosto_pro_actual_destino) + ($wcantidad * $wcosto_pro_actual_origen))/($wexist_actuales_destino + $wcantidad);
		$wnuevocospro = round($wnuevocospro,4);

		$q1= " UPDATE ".$wbasedato."_000007 "
			."    SET Karpro = ".$wnuevocospro." "
			."  WHERE Karcco = '".$wccogra."'"
			."    AND Karcod = '".$wprocod."'";
		$err = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


		//ACTUALIZA EL STOCK DEL CENTRO DE COSTOS
		$q1= " UPDATE ".$wbasedato."_000007 "
			."    SET karexi = karexi + ".$wcantidad
			."  WHERE karcco = '".$wccogra."'"
			."    AND karcod = '".$wprocod."'";
		$res3 = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q1." - ".mysql_error());



		//TRAIGO LAS NUEVAS EXISTENCIAS DEL KARDEX PARA REEMPLAZAR LA VARIABLE $wexiste
		$q= "SELECT karexi, Karcod, Karcco "
		   ."  FROM ".$wbasedato."_000007 "
		   ." WHERE karcco = '".$wccogra."'"
		   ."   AND karcod = '".$wprocod."'";
		$res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$row_exi = mysql_fetch_array($res2);
		$wexiste = $row_exi['karexi'];
		}
	$q= " UNLOCK TABLES";
	$res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


  /////////////////////////////////////////////////////////////////////////////////
  //ACTUALIZO Y TOMO EL CONSECUTIVO Y EL CODIGO DEL CONCEPTO DE VENTA EN INVENTARIO

  if($bandera_detalle =='true')
  {
	  $q = "lock table ".$wbasedato."_000008 LOW_PRIORITY WRITE";
	  $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


	  $q1= " UPDATE ".$wbasedato."_000008 "
		  ."    SET concon = concon + 1 "
		  ."  WHERE concod = ".$wconceptraslado." ";
	  $err = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	  $q= " SELECT concon, concod "
		 ."   FROM ".$wbasedato."_000008 "
		 ."  WHERE concod = ".$wconceptraslado." ";
	  $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $row = mysql_fetch_array($err);
	  $wnromvtovar=$row[0];
	  $wconmvto=$row[1];                            //Concepto de Salida (Ventas)

	  $q = " UNLOCK TABLES";
	  $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	  /////////////////////////////////////////////////////////////////////////////////
	  //ACA DESCARGO DEL INVENTARIO ===================================================
	  /////////////////////////////////////////////////////////////////////////////////

	  //===================================================================================================================================
	  //===================================================================================================================================
	  //GRABO EN LA TABLA DEL -- <ENCABEZADO> -- DEL **** MOVIMIENTO DE INVENTARIOS ****

	  $q= " INSERT INTO ".$wbasedato."_000010 (   Medico       ,   Fecha_data,   Hora_data,   menano      ,   menmes      ,   menfec    ,  mendoc       ,   mencon      ,   mencco     ,   menccd     , mendan, menpre, mennit, menusu, menfac, menest, Seguridad        ) "
		 ."                            VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".date("Y")."','".date("m")."','".$wfecha."','".$wnromvtovar."','".$wconceptraslado."','".$wbodega."','".$wccogra."', '.'   , 0     , '0'   , '.'   , '.'   ,   'on', 'C-".$wusuario."')";
	  $res = mysql_query($q,$conex) or die ("Error en la 10 para la funcion translado: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	  $bandera_detalle='false';
 }
  //===================================================================================================================================
  //===================================================================================================================================
  //GRABO EN LA TABLA DEL -- <DETALLE> -- DE  **** MOVIMIENTO DE INVENTARIOS ****
  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //============================================================================================================================

  //=========================================
  //TRAIGO EL COSTO PROMEDIO DEL ARTICULO
  $q= "SELECT karpro "
	 ."  FROM ".$wbasedato."_000007 "
	 ." WHERE karcco = '".$wbodega."'"
	 ."   AND karcod = '".$wprocod."'";
  $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $row_cos = mysql_fetch_array($res2);

  if ($row_cos[0] == "")
	 $row_cos[0]=0;

  //=========================================
  //GRABO EL DETALLE DEL ARTICULO
  $q= " INSERT INTO ".$wbasedato."_000011 (   Medico       ,   Fecha_data,   Hora_data,   mdecon      ,  mdedoc       ,   mdeart     ,  mdecan      ,  mdevto                    , mdepiv , mdeest, Seguridad        ) "
	 ."                            VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".$wconceptraslado."','".$wnromvtovar."','".$wprocod."',".$wcantidad.",".($wcantidad*$row_cos[0]).", '0'    , 'on'  , 'C-".$wusuario."')";
  $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());





}


function traer_hidden_empresas()
{


	global $conex;
	global $wbasedato;

	$arr_empresas = array();

	$query =  " SELECT Empres, Empnom, Emptde,Empnit
				  FROM ".$wbasedato."_000024
				 WHERE Empfac= 'off'
				 ORDER BY empcod";
	$res = mysql_query($query,$conex);
	$arr_empresas[""] = "";
	while($row = mysql_fetch_array($res))
	{
		//$arr_relacion_ne[trim($row['ppal'])] = trim($row['seleccionado']);
		$arr_empresas[utf8_encode(trim($row['Empres']))] = trim(utf8_encode(trim($row['Empres']))."-".utf8_encode(trim($row['Empnit']))."-".utf8_encode($row['Empnom']));
	}




	return $arr_empresas;

}

function traer_hidden_cco()
{


	global $conex;
	global $wbasedato;

	$arr_cco = array();


	$query =  " SELECT ccocod, ccodes
				  FROM ".$wbasedato."_000003
				 ORDER BY ccocod ";

	$res   = mysql_query($query,$conex);
	$arr_cco[""] = "";
	while($row = mysql_fetch_array($res))
	{
		$arr_cco[utf8_encode(trim($row['ccocod']))] = utf8_encode(trim($row['ccocod']))."-".trim(utf8_encode($row['ccodes']));
	}




	return $arr_cco;

}


/**
* Esta función reemplaza todos los caracteres especiales de un texto dado por su equivalente
*/
function stripAccents($string)
{
    $string = str_replace("ç","c",$string);
    $string = str_replace("Ç","C",$string);
    $string = str_replace("Ý","Y",$string);
    $string = str_replace("ý","y",$string);
    $string = str_replace("Ã‘","Ñ",$string);
    $string = str_replace("Ãƒâ€˜","Ñ",$string);
    return $string;
}


//Retorna el numero de dias que hay entre dos fechas
function dateDiff($d1, $d2)
 {

    return round(abs(strtotime($d1)-strtotime($d2))/86400);


 }

//Esta funcion muestra los laboratorios a los que esta afiliado el usuario.
function laboratorios_usuario($wdocpac)
{
    global $conex;
    global $wbasedato;

    $wlaboratorios= '';
    $br = '<br>';
    $array_datos = array();

    $q_usu = "SELECT Pronom "
            ."  FROM ".$wbasedato."_000006, ".$wbasedato."_000152"
            ." WHERE Usuced	= '".$wdocpac."'
                 AND Usulab = Pronit
                 AND Usuest = 'on'
			GROUP BY Usulab";
	$err_usu = mysql_query($q_usu,$conex) or die (mysql_errno()."  ".mysql_error());
    $num = mysql_num_rows($err_usu);

    while($row_usu = mysql_fetch_array($err_usu))
        {

            $wlaboratorios .= $row_usu['Pronom'].$br;

        }

   $array_datos = array('laboratorios'=>$wlaboratorios, 'cuantos'=>$num);

   return $array_datos;
}

//Esta funcion trae la informacion actual del usuario con relacion a un articulo y ese articulo relacionado a un laboratorio,
//si el usuario no esta registrado al laboratorio del articulo mostrara la ventana para registrarlo.
function validar_dsct_esc_art($warticulo, $wdocpac)
{
    global $conex;
    global $wbasedato;
    $wfecha_actual = date ("Y-m-d");
    $existe_usuario = '';

    $array_datos = array('cuantos'=>0,'nombre_lab'=>'','nombre_art'=>'', 'usuario'=>'', 'nit_lab'=>'');

    //Busco si el articulo tiene asociado descuento escalonado y si esta activo.
    $query_lab = "SELECT pronit, pronom, artnom
					FROM ".$wbasedato."_000001, ".$wbasedato."_000006, ".$wbasedato."_000147
				   WHERE descar = '".$warticulo."'
					 AND artcod = descar
					 AND desnit = pronit
					 AND desfin >= '".$wfecha_actual."'
					 AND proest = 'on'
					 AND desest = 'on'";
    $res_lab = mysql_query( $query_lab, $conex ) or die( mysql_errno()." - Error en el query $query_lab - ".mysql_error() );
    $num = mysql_num_rows($res_lab);
    $row = mysql_fetch_array($res_lab);

    //Busco si el usuario esta asociado a un laboratorio.
    $q_usu = "SELECT usulab "
            ."  FROM ".$wbasedato."_000147, ".$wbasedato."_000152"
            ." WHERE usulab = desnit "
			."   AND descar = usuart"
            ."   AND usuced = '".$wdocpac."'"
            ."   AND descar = '".$warticulo."'"
            ."   AND usuest = 'on'";
    $err_usu = mysql_query($q_usu,$conex) or die (mysql_errno()."  ".mysql_error());
    $num_usu = mysql_num_rows($err_usu);

    //Si no hay relacion, envia el parametro $existe_usuario con la palabra sin registro.
    if($num_usu == 0)
       {
          $existe_usuario = 'sin_registro';
       }

    $wdatos = array('cuantos'=>$num,'nombre_lab'=>$row['pronom'], 'nombre_art'=> $row['artnom'], 'usuario'=>$existe_usuario, 'nit_lab'=>$row['pronit']);

    return $wdatos;

}

//Busca el porcentaje de descuento de acuerdo al nivel.
function buscar_porcentaje($wart, $wnivel)
	{

	global $conex;
    global $wbasedato;

	//Consulto el valor y el porcentaje de descuento.
	$q_nivel = "SELECT Nivpor, Nivval "
			  ."  FROM ".$wbasedato."_000147 a, ".$wbasedato."_000151 b"
			  ." WHERE a.id = Nivcre
				   AND Descar = '".$wart."'
				   AND Nivniv = '".$wnivel."'
				   AND Desest = 'on'";
	$err_nivel = mysql_query($q_nivel,$conex) or die (mysql_errno()."  ".mysql_error());
	$row_nivel = mysql_fetch_assoc($err_nivel);
	$wniv_porc = $row_nivel['Nivpor'];
	$wniv_val = $row_nivel['Nivval'];

	return $wniv_porc."-".$wniv_val;

	}

//Esta funcion verifica si el articulo tiene descuento escalonado, si el usuario esta en la lista de reportados por los laboratorios,
//ademas de verificar en que nivel de descuento se encuentra el usuario.
function verif_desc_escalonado($wdocpac, $warticulo)
{

    global $conex;
    global $wbasedato;

    $array_datos = array();
    $fecha_actual = date("Y-m-d");

    //Busco el nit de la entidad segun el codigo del articulo
    $q_lab =  "SELECT Desnit "
             ."  FROM ".$wbasedato."_000147"
             ." WHERE Descar = '".$warticulo."' ";
	$err_lab = mysql_query($q_lab,$conex) or die (mysql_errno()." Error en la busqueda de laboratorio ".mysql_error());
    $row_lab = mysql_fetch_array($err_lab);
    $wnitlab = $row_lab['Desnit'];

  //Si el articulo tiene registros en descuento escalonado, busco si el usuario esta relacionado con el laboratorio.
  if($wnitlab != '')
    {

		$wactivo = 'condescuento';

		$q_usu = "SELECT id "
				."  FROM ".$wbasedato."_000152"
				." WHERE usulab	= '".$wnitlab."'"
				."   AND usuced	= '".$wdocpac."'"
				."   AND usuest = 'on'";
		$err_usu = mysql_query($q_usu,$conex) or die (mysql_errno()."  ".mysql_error());
		$num_usu = mysql_num_rows($err_usu);

        //Si hay relacion del laboratorio con el usuario, busco el nivel de descuento en el que va el usuario
      if($num_usu > 0)
        {

            //Consulto el consecutivo de venta del articulo para el paciente
            $q_con = "SELECT Usuniv, Usufuc"
                    ."  FROM ".$wbasedato."_000152"
                    ." WHERE Usuart = '".$warticulo."'
                         AND Usuced = '".$wdocpac."'";
            $res_con = mysql_query($q_con,$conex) or die (mysql_errno()."  ".mysql_error());
            $row_con = mysql_fetch_array($res_con);
            $wconsecutivo_actual = $row_con['Usuniv'];
            $wfecha_ult_compra = $row_con['Usufuc'];

            $wfecha_ult_compra_aux = '';

			//Si el usuario no ha comprado el articulo pondra la fecha por defecto la actual, sino tomara la ultima fecha de compra.
            if($wfecha_ult_compra == '0000-00-00')
                {
                 $wfecha_ult_compra = $fecha_actual;
                }
			else
				{
				$wfecha_ult_compra_aux = $wfecha_ult_compra;
				}

            //Calcula los dias que han transcurrido desde la ultima compra, a partir de la fecha actual.
            $diastranscurridos = dateDiff($fecha_actual, $wfecha_ult_compra);

			//Traigo los datos de descuento escalonado para el articulo.
            $q_dias = "SELECT Desdpd ,Desnde, Desfin, Descpm "
                     ."  FROM ".$wbasedato."_000147"
                     ." WHERE Descar  = '".$warticulo."'"
					 ."   AND Desfin >= '".$fecha_actual."'"
					 ."   AND Desest  = 'on'";
            $err_desc = mysql_query($q_dias,$conex) or die (mysql_errno()."  ".mysql_error());
            $row_desc = mysql_fetch_array($err_desc);

            $wdias_vence_desc = $row_desc['Desdpd'];
            $wnivel_devuelve = $row_desc['Desnde'];
            $wfecha_final_descuento = $row_desc['Desfin'];
			$wcajaspormes = $row_desc['Descpm'];

			//Consulto el nivel nivel maximo de descuento para un articulo
            $q_nivel = "SELECT MAX(Nivniv) as maxnivel"
                      ."  FROM ".$wbasedato."_000147 a, ".$wbasedato."_000151 b"
                      ." WHERE a.id = Nivcre
                           AND Descar = '".$warticulo."'
						   AND Desest = 'on'";
            $err_nivel = mysql_query($q_nivel,$conex) or die (mysql_errno()."  ".mysql_error());
            $row_nivel = mysql_fetch_array($err_nivel);
            $maxnivel = $row_nivel['maxnivel'];


			//Si la fecha actual es mayor a la fecha final de descuento, no tendra descuento.
			if($fecha_actual > $wfecha_final_descuento)
				{
					$wactivo = 'sindescuento';
					$wporcentajedes = '0';
				}
			 else
				{

					//Si la cantidad de dias supera los dias transcurridos desde la ultima compra, buscará el nivel al que se devuelve,
					//si es -1 se devolverá al nivel anterior del que se encuentra la compra.
					if($diastranscurridos > $wdias_vence_desc)
					{
						if($wnivel_devuelve == '-1')
						{
							$wconsecutivo_actual_final = $wconsecutivo_actual-1; //Menos uno al consecutivo actual.
						}
						else
						{
							$wconsecutivo_actual_final = $wnivel_devuelve; //Utiliza el nivel predeterminado al que se devolvera.
							$wcontrol_devuelve = 'on';
							$wnivel_devuelve_aux = $wnivel_devuelve;
						}
					}


				 //Busca la cantidad de cajas por mes para un articulo
				$q =     " SELECT Descpm "
						."   FROM ".$wbasedato."_000147, ".$wbasedato."_000152"
						."  WHERE Descar = Usuart
							  AND Descar = '".$warticulo."'
							  AND Usuced = '".$wdocpac."'
							  AND Desest = 'on'";
				$err = mysql_query($q,$conex) or die (mysql_errno()."  ".mysql_error());
				$row = mysql_fetch_array($err);

				//Nivel maximo de descuento por articulo
				$q_niv =     " SELECT max(nivniv) as max_nivel "
							."   FROM ".$wbasedato."_000147 a, ".$wbasedato."_000151 b"
							."  WHERE Nivcre = a.id
								  AND Descar = '".$warticulo."'
								  AND Desest = 'on'";
				$err_niv = mysql_query($q_niv,$conex) or die (mysql_errno()."  ".mysql_error());
				$row_niv = mysql_fetch_array($err_niv);

			}

        }

    }
    else
    {
        $wactivo = 'sindescuento';
        $wporcentajedes = '0';
    }


    $array_datos = array('activo'=>$wactivo, 'nota_aclaratorio_desc'=>$wnota, 'cajaspormes'=>$row['Descpm'], 'nivel_max'=>$row_niv['max_nivel'], 'devuelve'=>$wcontrol_devuelve, 'nivel_devuelve'=>$wnivel_devuelve_aux, 'usu_registrado'=>$num_usuario);

    return $array_datos;
}

function consultar_ase_estatal($wemp_pmla)
{
	global $conex;
	$consulta = "0";

	// Consulto los datos de la aseguradora estatal actual
	$q=  " SELECT Detval "
		."	 FROM root_000051  "
		."  WHERE Detapl = 'aseguradoraEstatal' "
		."	  AND Detemp = '".$wemp_pmla."'";
	$res = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if(isset($num) && $num>0)
		$consulta = "1";

	return $consulta;
}

//////////////////////////////////////////////////////////////////////////////////////
//FUNCIONES PARA VALIDACION DE DATOS NULOS EN RESULTADOS DE CONSULTAS A UNIX
function ejecutar_consulta_furips ($query_campos, $query_from, $query_where, $llaves, $conexUnix, $order = " ")
{
	global $conexUnix;
	global $increment;

	$increment++;
	$aleatorio = rand(1, 1000);
	if ($query_where == NULL)
		$query =   " SELECT $query_campos FROM $query_from ";
	else
		$query =   " SELECT $query_campos FROM $query_from WHERE $query_where";

	$table= date("Mdhis").$aleatorio.$increment;                //nombre de la tabla temporal
	$query=$query." into temp $table";              //creo la temporal con los resultados de la consulta que enviaron
	odbc_do($conexUnix,$query);

	$query1= "select * from $table";
	$err_o1 = odbc_do($conexUnix,$query1); // Consulto la tabla temporal

	$p=0;  //ooojjjjjjjooooooo temporal
	while (odbc_fetch_row($err_o1)) //RECORRO CADA REGISTRO DE LA TEMPORAL
	{
		$n_llaves=count($llaves);
		for ($x=0; $x<$n_llaves ;$x++)
		{
			$pk_valor[$x]=odbc_result($err_o1, $llaves[$x]);
			$pk_nombre[$x]=odbc_field_name($err_o1, $llaves[$x]);
		}
		for($i=1;$i<=odbc_num_fields($err_o1);$i++)
		{
			$campo=odbc_field_name($err_o1,$i);
			$valor=odbc_result($err_o1,$i);
			validar_nulos_furips($query_from, $query_where, $campo, $valor, $pk_nombre, $pk_valor, $conexUnix, $table);//ESTA FUNCION ACTUALIZA EL VALOR DEL CAMPO DE LA TEMPORAL, DEPENDIENDO DEL VALOR DE LA ORGINAL
		}
		$p++;
	}
	$query1="select * from $table $order";
	$err_o1 = odbc_do($conexUnix,$query1); // retornar la consulta sin null
	return $err_o1;
}

function validar_nulos_furips($query_from, $query_where, $campo, $valor, $pk_nombre, $pk_valor, $conexUnix, &$table)
{
	if ($query_where == NULL)
	{
		$query_no_null = " SELECT $campo FROM $query_from";
		$query_no_null = $query_no_null." Where $campo is not null  ";
	}
	else
	{
		$query_no_null = " SELECT $campo FROM $query_from WHERE $query_where";
		$query_no_null = $query_no_null." AND ($campo is not null or $campo != '')  ";
	}

	for($y=0; $y<count($pk_valor); $y++ )
	{
		$query_no_null = $query_no_null." AND ".$pk_nombre[$y]." = '".$pk_valor[$y]."' ";
	}
	//echo $query_no_null;
	$res_no_null = odbc_do($conexUnix, $query_no_null);


	if (odbc_fetch_row($res_no_null))
	{
		$valor_no_null = odbc_result($res_no_null, 1);
		$query4="update $table ";
		$query4=$query4."set $campo = '$valor_no_null'  ";
		$query4=$query4." WHERE ";
		for($y=0; $y<count($pk_valor); $y++ )
			{
				if ($y==0)
					$query4=$query4." ".$pk_nombre[$y]." = '".$pk_valor[$y]."' ";
				else
					$query4=$query4." AND ".$pk_nombre[$y]." = '".$pk_valor[$y]."' ";
			}
		$err_o4 = odbc_do($conexUnix,$query4);
	}

}
//////////////////////////////////////////////////////////////////////////////////////
?>
<html>
<head>

	<title>VENTAS AL PUBLICO </title>

	<!--<link type='text/css' href='../../../include/root/ui.core.css' rel='stylesheet' />
	<link type='text/css' href='../../../include/root/jquery.tooltip.css' rel='stylesheet' />

	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script type='text/javascript' src='../../../include/root/jquery.tooltip.js'></script>
	<script type='text/javascript' src='../../../include/root/jquery.blockUI.min.js'></script>
    <script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>-->






	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
	<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
	<link type="text/css" href="../../../include/root/smartpaginator.css" rel="stylesheet" /> <!-- Autocomplete -->
	<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />

	<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
	<script type='text/javascript' src='../../../include/root/jquery.tooltip.js'></script>
	<script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>
	<script src="../../../include/root/jquery.maskedinput.js" type="text/javascript"></script>
	<script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>
	<link type="text/css" href="../../../include/root/jquery.ui.timepicker.css" rel="stylesheet"/>
	<script type='text/javascript' src='../../../include/root/smartpaginator.js'></script>	<!-- Autocomplete -->
	<script src="../../../include/root/toJson.js" type="text/javascript"></script>




	<style type="text/css">
		BODY
		{
		font-family: verdana;
		font-size: 8.5pt;
		height: 1024px;
		width: 1280px;
		}
		.encabezadoTabla
		{
			 background-color: #2A5DB0;
			 color: #FFFFFF;
			 font-size: 8.5pt;
			 font-weight: bold;
		}
		.fila1
		{
			 background-color: #C3D9FF;
			 color: #000000;
			 font-size: 8.5pt;
		}
		.fila2
		{
			 background-color: #E8EEF7;
			 color: #000000;
			 font-size: 8.5pt;
		}
		.articuloControl
		{
			 background-color: #FFFFCC;
			 color: #000000;
			 font-size: 8.5pt;
			 font-weight: bold;
		}
		.articuloControl2
		{
			 background-color: #D8BFD8;
			 color: #000000;
			 font-size: 8.5pt;
			 font-weight: bold;
		}
		select, input
		{
			 color: #000000;
			 font-size: 8.5pt;
		}

       #laboratorios_flotante
       {
        border: 1px solid #CCC;
        background-color: #F2F2F2;
        }

		.campoObligatorio{
			border-style:solid;
			border-color:red;
			border-width:1px;
		}

		fieldset{
			border: 2px solid #e0e0e0;
		}
		legend{
			border: 2px solid #e0e0e0;
			border-top: 0px;
			font-family: Verdana;
			background-color: #e6e6e6;
			font-size: 11pt;
		}

		.campoObligatorio{
			border-style:solid;
			border-color:red;
			border-width:1px;
		}

		#tooltip{font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 6pt;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:3px;opacity:1;border-radius: 4px;}
		#tooltip div{margin:0; width:auto;}
		#tooltip2{font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 6pt;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:3px;opacity:1;border-radius: 4px;}
		#tooltip2 div{margin:0; width:auto;}
	</style>

</head>
<body>
<script type="text/javascript">





	function cerraremergente()
    {
       $.unblockUI();
    }
	//Cierra la ventana flotante de aprobacion de descuentos.
	function cerrar_flotante_aprob()
        {
			$('#mas_descuentos').hide(500);
			$('#ver_mas_descuentos').show(500);

        }

	//Cierra la ventana flotante de laboratorios asociados.
    function cerrar_flotante()
        {
			$('#laboratorios_flotante').hide(500);
			$('#ver_laboratorios').show(500);
        }

	//Cierra la ventana flotante de articulos para asociar.
    function cerrar_flotante_articulo()
        {
			$('#registrar_linea_vital').hide(500);
			$('#ver_articulos').show(500);
        }

	//Carga la ventana flotante de laboratorios asociados.
    function cargar_flotante()
        {
        var elemento = $("#bonos_dsct");
        var posicion = elemento.offset();
        $('#laboratorios_flotante').css({'left':posicion.left+70,'top':posicion.top+65});
        $('#laboratorios_flotante').show(500);
        $('#laboratorios_flotante').draggable();
        $('#ver_laboratorios').hide();

        }

	//Carga la ventana flotante de articulo por laboratorio.
    function cargar_flotante_articulo()
      {
          var elemento = $("#bonos_dsct");
          var posicion = elemento.offset();
          $('#registrar_linea_vital').css({'left':posicion.left+70,'top':posicion.top+65});
          $('#registrar_linea_vital').show(500);
          $('#registrar_linea_vital').draggable();
          $('#ver_articulos').hide();

       }

	//Abre la ventana modal con la que se le aprueban al cliente mas descuentos.
	function cargar_flotante_aprob()
        {
        var elemento = $("#bonos_dsct");
        var posicion = elemento.offset();
        $('#mas_descuentos').css({'left':posicion.left+70,'top':posicion.top+65});
        $('#mas_descuentos').show(500);
        $('#mas_descuentos').draggable();
        $('#ver_mas_descuentos').hide();

        }

	//Muestra u oculta los campos para afiliar el usuario.
    function mostrar_afiliar()
    {

		if($('#ingreso_codigo').is(':visible'))
			{
			 $('#ingreso_codigo').hide(500);
			}
		else
			{
			 $('#ingreso_codigo').show(500);
			}


    }

	//Muestra u oculta los campos para aprobarle descuentos adicionales al cliente.
	function mostrar_aprobacion(wart)
    {

		if($('#mostrar_aprobacion_'+wart+'').is(':visible'))
			{
			$('#mostrar_aprobacion_'+wart+'').hide(500);
			}
		else
			{
			$('#mostrar_aprobacion_'+wart+'').show(500);
			}
    }

    //Ventanana emergente para el registro de usuarios
  function afiliar_usuario(wemp_pmla, wbasedato, nit_laboratorio, wusuario_sistema, wcod_articulo, wvalor_art, wcantidad)
    {

    var cedula = $("#wdocpac").val();
    var cod_autorizacion = $("#codigo_autorizacion").val();
	var ref_autorizacion = $("#ref_autorizacion").val();
	var wfecha_tempo = $("#wfecha_tempo").val();
	var whora_tempo = $("#whora_tempo").val();
	var wnombrepac = $("#wnompac").val();

	//Se valida que el nombre del cliente sea diferente a cliente particular ya que si lo va a afiliar, debe tener nombre asociado.
	if(wnombrepac == "CLIENTE PARTICULAR")
		{
		alert('Favor ingresar el nombre del cliente, debe ser diferente a CLIENTE PARTICULAR.');
		return;
		}

    if(cod_autorizacion != '' && ref_autorizacion != '' )
        {

			$.post("../../../include/ips/Grabar_venta_nue.php",
			{
				consultaAjax2:      'guardar_usuario',
                wemp_pmla:          wemp_pmla,
                wbasedato:          wbasedato,
                wcedula:            cedula,
                nit_laboratorio:    nit_laboratorio,
                cod_autorizacion:   cod_autorizacion,
                wusuario_sistema:   wusuario_sistema,
				wref_autorizacion:	ref_autorizacion,
				wcod_articulo:		wcod_articulo,
				wfecha_tempo_aux:	wfecha_tempo,
				whora_tempo_aux:    whora_tempo,
				wvalor_art_aux:		wvalor_art,
				wcantidad_aux:		wcantidad

			}
			,function(data) {
					if(data.error == 1)
					{
					alert(data.mensaje);
					return;

					}
					else
					{

                        alert(data.mensaje);
                        var articulo = $("input[type=radio][datoarticulo="+wcod_articulo+"]").attr('id'); //Se marcara el articulo en la lista de articulos solicitados.

						//Se seleciona uno de los articulos de la lista para que muestre el descuento asociado.
                        if(articulo != undefined)
                            {
							  $("#"+articulo).click();
                            }

					}
				},
				"json"
			);
        }
        else
            {
                alert('Favor ingresar el código de activación y la referencia del funcionario que autoriza.');
            }
    }


  //Funcion que permite la aprobacion de mas descuentos para el cliente.
  function aprobar_descuentos(wemp_pmla, wbasedato, nit_laboratorio, wusuario_sistema, wcod_articulo, wvalor_art, wcantidad, wcedula, wcantidad_aprob)
    {

   	var cod_aprobacion = $("#cod_aprobacion").val();
	var wfecha_tempo = $("#wfecha_tempo").val();
	var whora_tempo = $("#whora_tempo").val();

    if(cod_aprobacion != '' )
        {

			$.post("../../../include/ips/Grabar_venta_nue.php",
			{
				consultaAjax2:      'aprobar_descuentos',
                wemp_pmla:          wemp_pmla,
                wbasedato:          wbasedato,
                wcedula:            wcedula,
                nit_laboratorio:    nit_laboratorio,
                cod_aprobacion:     cod_aprobacion,
                wusuario_sistema:   wusuario_sistema,
				wcod_articulo:		wcod_articulo,
				wfecha_tempo_aux:	wfecha_tempo,
				whora_tempo_aux:    whora_tempo,
				wvalor_art_aux:		wvalor_art,
				wcantidad_aux:		wcantidad,
				wcantidad_aprob:	wcantidad_aprob



			}
			,function(data) {
					if(data.error == 1)
					{
					alert(data.mensaje);
					return;

					}
					else
					{

                        alert(data.mensaje);
                         var articulo = $("input[type=radio][datoarticulo="+wcod_articulo+"]").attr('id'); //Se marcara el articulo en la lista de articulos solicitados.

                        if(articulo != undefined)
                            {
							  // $("#"+articulo).attr('checked', 'checked');
                               $("#"+articulo).click();
                            }

					}
				},
				"json"
			);
        }
        else
            {
                alert('Favor ingresar el código de autorización de descuento.');
            }
    }


	$(function() {

		$(".ProductAttributesSelect")

			.mouseover(function(){
				$(this)
					.data("origWidth", $(this).css("width"))
					.css("width", "auto");
			})

			.mouseout(function(){
				$(this).css("width", $(this).data("origWidth"));
			});

	});

	function isset(variable_name)
	{
		try {
			 if (typeof(eval(variable_name)) != 'undefined')
			 if (eval(variable_name) != null)
			 return true;
		 } catch(e) { }
		return false;
	}

	function getGET(){
	   var loc = document.location.href;
	   var getString = loc.split('?')[1];
	   var GET = getString.split('&');
	   var get = {};//this object will be filled with the key-value pairs and returned.

	   for(var i = 0, l = GET.length; i < l; i++){
		  var tmp = GET[i].split('=');
		  get[tmp[0]] = unescape(decodeURI(tmp[1]));
	   }
	   return get;
	}

    function enter()
	{
	   	if(document.forms.ventas.wtipfac.value=='Manual')
			alert("!!!! ATENCION !!!! ***** ESTA GRABANDO UNA FACTURA MANUAL *****");
	   	submit_form('on');
	   //document.forms.ventas.submit();
	}

    function graba_venta_ok()
	{


		//-------- Validacion para que la autorizacion sea obligatoria
		variableautorizacion = false;
		var contarautorizaciones=0;
		var contarigualesacero = 0;
		var existerecibos = 0;
		$( ".autorizacion" ).removeClass('campoObligatorio');

		$( "input[name*='wautori']" ).each(function( ) {
						if($(this).val()=='')
						{
							contarautorizaciones++;
							$(this).addClass('campoObligatorio');
						}


		});

		$( "input[name*='wvalfpa2']" ).each(function( ) {

			existerecibos++;

			//alert($(this).val().replace(/,/g, "")*1);
			if($(this).val().replace(/,/g, "")*1 > 0)
			{
				contarigualesacero++;
				//$(this).addClass('campoObligatorio');
			}


		});


		if(contarigualesacero ==0 && existerecibos != 0)
		{
			alert ("Debe llenar la cantidad para el recibo");
			$("input[name='wclave']").val('');
			return;
		}



		if(contarautorizaciones !=0)
		{
			alert ("Debe llenar el numero de autorizacion");
			$("input[name='wclave']").val('');
			return;
		}
		//-------------------------------------------------------------

		//---------Validacion para que tenga que entregar mas de un peso, parametro en la root_000051

		if($("#wpidesiempredinero").val())
		{
			var valortotalrecibos =0 ;
			$( "input[name*='wvalfpa']" ).each(function( ) {

				valortotalrecibos = (valortotalrecibos*1) + ($(this).val() * 1 );

			});

			if (valortotalrecibos == 0)
			{
				alert ("Se tiene que recibir dinero");
				$("input[name='wclave']").val('');
				return;
			}
		}

		//--------------------
		// alert(observacionppal);
		document.getElementById("wok_btn").value = "Grabando...";

		document.getElementById("wok_btn").disabled = true;

		if (document.getElementById('wtipcli') && document.getElementById('wtipcli').value=="01-PARTICULAR")
			poner_foco = 'wvalfpa[1]';
		else
			poner_foco = 'wclave';

		submit_form2(poner_foco);
		$("#nroventa").html($("#hiddennroventa").val());
		// $("#wobservacionppal").val(observacionppal);
		// $("#wobservacionppal").text(observacionppal);
	}

    function graba_venta()
	{

		if ($("#wemp_pmla").val()=='09')
		{
			var str3 = $("#wempresa").val();
			seleccionado = str3.split("-");

			var str2= $("#wtipcli").val();
			var tiporesponsable = str2.split("-");
			var str = $("#nit_responsable").val();
			var resp = str.split("-");

			// alert(tiporesponsable[0]);
			// alert(resp[0]+"---"+seleccionado[1]);

			if(tiporesponsable[0]=='12')
			{



				if(seleccionado[1] == resp[0])
				{
					//alert("son iguales");
				}
				else
				{
					alert("el responsable seleccionado es distinto al que se encuentra en unix")
					return;
				}
			}
		}




		var poner_foco = "";
		document.getElementById("wventa").value = 'S';

		//document.getElementById("wventa_btn").value = "Grabando...";
		document.getElementById("wventa_btn").disabled = true;

		/*if ((document.getElementById('wpago') && document.getElementById('wpago').value=="S") || (document.getElementById('WSINCUOTA') && document.getElementById('WSINCUOTA')=="S"))
			poner_foco = 'wclave';
		*/

		if (document.getElementById('wtipcli') && document.getElementById('wtipcli').value=="01-PARTICULAR")
			poner_foco = 'wvalfpa[1]';
		else
			poner_foco = 'wclave';

		submit_form2(poner_foco);
	}

	function cerrarVentana()
	 {
      window.close()
     }

	function enter1()
	{
	   submit_form('on');
	   alert ("Pulse de nuevo la tecla ENTER");
	}
	function alerta()
	{
	   alert ("!!!! ATENCION !!!! ***** ESTA GRABANDO UNA FACTURA MANUAL *****");
	}

  var a, mes, dia, anyo, febrero;

  function anyoBisiesto(anyo)
    {
        /**
        * si el año introducido es de dos cifras lo pasamos al periodo de 1900. Ejemplo: 25 > 1925
        */
        if (anyo < 100)
            var fin = anyo + 1900;
        else
            var fin = anyo ;

        /*
        * primera condicion: si el resto de dividir el año entre 4 no es cero > el año no es bisiesto
        * es decir, obtenemos año modulo 4, teniendo que cumplirse anyo mod(4)=0 para bisiesto
        */
        if (fin % 4 != 0)
            return false;
        else
        {
            if (fin % 100 == 0)
            {
                /**
                * si el año es divisible por 4 y por 100 y divisible por 400 > es bisiesto
                */
                if (fin % 400 == 0)
                {
                    return true;
                }
                /**
                * si es divisible por 4 y por 100 pero no lo es por 400 > no es bisiesto
                */
                else
                {
                    return false;
                }
            }
            /**
            * si es divisible por 4 y no es divisible por 100 > el año es bisiesto
            */
            else
            {
                return true;
            }
        }
    }

	function validarDay(day)
	{
	if ( day>=1 && day<=31)
	 {
	  return true;
	 }
	  else
	  {
	   return false;
	  }
	}


	function validarMonth(mes)
	{
	if ( mes>=1 && mes<=12)
	 {
	  return true;
	 }
	  else
	  {
	   return false;
	  }
	}

	function validarYear(year)
	{
	if ( year>=1900 && year<=2050)
	 {
	  return true;
	 }
	  else
	  {
	   return false;
	  }
	}

	 function validar_fecha(fecha)
	 {
	  //el parametro fecha entra en formato (aaaa-mm-dd)
	  dia=fecha.split("-")[2];
	  if (validarDay(dia)==false)
	   {
		return "Fecha incompleta o día incorrecto.";
	   }
	   else
	   {//1 else
		mes=fecha.split("-")[1];
		if (validarMonth(mes)==false)
		{
		 return "Fecha incompleta o mes incorrecto.";
		}
		else
		{//2 else
		 anyo=fecha.split("-")[0];
		 if (validarYear(anyo)==false)
		 {
		  return "Fecha incompleta o año incorrecto.";
		 }
		  else
		  {//3 else
		   if(anyoBisiesto(anyo))
			   febrero=29;
		   else
			   febrero=28;
		   /**
		   * si el mes introducido es negativo, 0 o mayor que 12 > alertamos y detenemos ejecucion
		   */
		   if ((mes<1) || (mes>12))
		   {
			   return "El mes no es válido. Por favor inserte un mes correcto.";
		   }
		   /**
		   * si el mes introducido es febrero y el dia es mayor que el correspondiente
		   * al año introducido > alertamos y detenemos ejecucion
		   */
		   if ((mes==2) && ((dia<1) || (dia>febrero)))
		   {
			   return "El día no es válido. Por favor inserte un día correcto.";
		   }
		   /**
		   * si el mes introducido es de 31 dias y el dia introducido es mayor de 31 > alertamos y detenemos ejecucion
		   */
		   if (((mes==1) || (mes==3) || (mes==5) || (mes==7) || (mes==8) || (mes==10) || (mes==12)) && ((dia<1) || (dia>31)))
		   {
			   return "El día no es válido. Por favor inserte un día correcto.";
		   }
		   /**
		   * si el mes introducido es de 30 dias y el dia introducido es mayor de 301 > alertamos y detenemos ejecucion
		   */
		   if (((mes==4) || (mes==6) || (mes==9) || (mes==11)) && ((dia<1) || (dia>30)))
		   {
			   return "El día no es válido. Por favor inserte un día correcto.";
		   }
		   /**
		   * si el mes año introducido es menor que 1900 o mayor que 2016 > alertamos y detenemos ejecucion
		   * NOTA: estos valores son a eleccion vuestra, y no constituyen por si solos fecha erronea
		   */
		   /*
		   if ((anyo<1900) || (anyo>2016))
		   {
			   return "El año no es válido. Por favor inserte un año entre 1900 y 2016.";
		   }
		   */
		   /**
		   * en caso de que todo sea correcto > enviamos los datos del formulario
		   * para ello debeis descomentar la ultima sentencia
		   */
		   else
			  return "";
		  }//3 else
		}//2 else
	   }// 1 else
	 }//fin function

	//funcion que valida que una fecha final sea mayor o igual a la
	//fecha inicial formato de entrada para las fechas es "yyyy-mm-dd"

	function rangoFechas(fecIni,fecFin)
	{
	  diaFecIni=fecIni.split("-")[2];
	  mesFecIni=fecIni.split("-")[1];
	  yearFecIni=fecIni.split("-")[0];

	  diaFecFin=fecFin.split("-")[2];
	  mesFecFin=fecFin.split("-")[1];
	  yearFecFin=fecFin.split("-")[0];


	  //constructor de fechas fecha= new Date(año,mes,dia)
	  fecIniCompleta= new Date(yearFecIni,mesFecIni,diaFecIni);
	  fecFinCompleta= new Date(yearFecFin,mesFecFin,diaFecFin);

	  delta = ((fecFinCompleta.getTime()-fecIniCompleta.getTime()) / 1000 / 60 / 60 / 24);
	  if(Math.round(delta)>=0)
	  {
		return "";
	  }
	  else
	  {
		return "La fecha no se encuentra en un rango permitido";
	  }
	}

	function valfecha(fecha)
	{
	  var datePat = /^((([0][1-9]|[12][\d])|[3][01])[-\/]([0][13578]|[1][02])[-\/][1-9]\d\d\d)|((([0][1-9]|[12][\d])|[3][0])[-\/]([0][13456789]|[1][012])[-\/][1-9]\d\d\d)|(([0][1-9]|[12][\d])[-\/][0][2][-\/][1-9]\d([02468][048]|[13579][26]))|(([0][1-9]|[12][0-8])[-\/][0][2][-\/][1-9]\d\d\d)$/;
	  var matchArray = fecha.match(datePat);
	  if(matchArray == null)
	  {
		alert("fecha inválida");
		return false;
	  }
	  else
		return true;
	}

	// Llamado ajax de envio de datos del formulario
	function submit_form(carga,foco)
	{
	  var get = getGET();
	  var envia_form = "on";

		if(isset(document.getElementById("wremitente")))
		{
			var remitente = document.getElementById("wcodrem").value;
			if(remitente=="" || remitente==" ")
			{
				envia_form = "off";
				alert("Debe ingresar el remitente");
				document.getElementById("wcodrem").focus();
				return false;
			}
		}

		/*
		//Pendiente verificar si se puede usar este codigo para validacion de medico
		if(isset(document.getElementById("wcodmed")))
		{
			var medico = document.getElementById("wcodmed").value;
			if(medico=="" || medico==" ")
			{
				envia_form = "off";
				alert("Debe ingresar el médico");
				document.getElementById("wcodmed").focus();
				return false;
			}
		}
		*/

		if(envia_form != "off")
		{

		  if(carga!='off')
		  {
			var parametros = "consultaAjax=envio";

			var formObj = document.getElementsByTagName("input");
			for (var i=0;i<formObj.length;i++)
			{
				if (formObj[i].name != undefined)
				{
					if (formObj[i].type == 'radio')
					{
						if(formObj[i].checked)
						{
							parametros += "&"+formObj[i].name+"="+formObj[i].value;
							if (formObj[i].name == 'wid')
								parametros += "&wborrar=S";
						}
					}
					else
					{
						parametros += "&"+formObj[i].name+"="+formObj[i].value;
						/*if (formObj[i].type == 'button' && formObj[i].name == 'wventa_btn')
							parametros += "&wventa=S";
						*/
					}
				}
			}

			var formObj = document.getElementsByTagName("select");
			for (var i=0;i<formObj.length;i++)
			{
				if (formObj[i].name != undefined)
					parametros += "&"+formObj[i].name+"="+formObj[i].value;
			}
		  }
		  else
		  {
			var parametros = "consultaAjax=envio&wini="+get['wini']+"&wemp_pmla="+get['wemp_pmla'];
			var contenedor2 = document.getElementById('articulos_content');
			contenedor2.innerHTML="";
		  }

		  //alert(parametros);

		  try
			{
				/*
				try {
					$.blockUI({ message: $('#msjEspere') });
				} catch(e){ }
				*/

				var ajax = nuevoAjax();

				ajax.open("POST", "Ventas_nue.php",true);
				ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				ajax.send(parametros);

				ajax.onreadystatechange=function()
				{
					var contenedor = document.getElementById('ventas_content');
					if (ajax.readyState==4 && ajax.status==200)
					{
						contenedor.innerHTML=ajax.responseText;

						if(isset(document.getElementById(foco)))
						{
							document.getElementById(foco).focus();
							if(foco!='wdato')
								document.getElementById(foco).select();
							else
								document.getElementById(foco).value="";
						}

						var formObjAlert = document.getElementsByName("walert");
						if(formObjAlert[0] && formObjAlert[0].value)
							alert(formObjAlert[0].value);

						//Ventana modal que muestra los laboratorios a los que esta afiliado el usuario.
						var posicion = $("#laboratorios_flotante").offset();
						var margenSuperior = 15;
						$(window).scroll(function() {
							if ($(window).scrollTop() > posicion.top) {
								$("#laboratorios_flotante").stop().animate({
									// marginTop: $(window).scrollTop() - posicion.top + margenSuperior
								});
							} else {
								$("#laboratorios_flotante").stop().animate({
									marginTop: 0
									});
								};
							 });


						var elemento = $("#bonos_dsct");
						var posicion = elemento.offset();
						$('#laboratorios_flotante').css({'left':posicion.left+70,'top':posicion.top+65});
						$('#laboratorios_flotante').show(500);
						$('#laboratorios_flotante').draggable();

					}
				}

				/*
				try {
					$.unblockUI();
				} catch(e){ }
				*/

				if ( !estaEnProceso(ajax) )
				{
					ajax.send(null);
				}
			}catch(e){	}
		}
	}

	function actuailizardatos(ele)
	{
		ele = jQuery(ele);

		if (ele.attr('tipo')=='entregado')
		{
			//alert(ele.attr('id'));
			var numero_real =  ele.val().replace(/,/g, "");
			var numero = formato_numero(numero_real, '2', '.', ',');
			ele.val(numero);
			var id = ele.attr('indicador');

			if(($("#wvalfpa\\["+id+"\\]").val().replace(/,/g, "") * 1 ) < ( numero_real  *1 ) && ($("#wvalfpa\\["+id+"\\]").val().replace(/,/g, "") * 1 != '0' ))
			{

			}
			else
			{
				//$("#wvalfpa\\["+id+"\\]").focus().val("").val(numero);
				$("#wvalfpa\\["+id+"\\]").val(numero);
			}


		}
		else
		{

			var respuesta = validarformadepago(ele);
			if(respuesta !=0)
			{

				submit_form2(respuesta);
				return ;
			}
			var numero_real =  ele.val().replace(/,/g, "");
			var numero = formato_numero(numero_real, '2', '.', ',');
			var id = ele.attr('indicador');
			//alert(($("#wvalfpa2\\["+id+"\\]").val().replace(/,/g, "") * 1 )+"---"+( numero_real  *1 ));
			if(($("#wvalfpa2\\["+id+"\\]").val().replace(/,/g, "") * 1 ) < ( numero_real  *1 ) )
			{
				var numero_aponer = $("#wvalfpa2\\["+id+"\\]").val();
				alerta2("El Valor de lo abonado no puede ser superior a lo entregado");
				ele.val('0.00').focus().select();
				//submit_form2("wclave");
			}
			else
			{

				var regla = /^(\d|-)?(\d|,)*\.?\d*$/;
				resultado = regla.test( ele.val() );
				//var cumplo ='si';
				//alert("1"+$(obj).val());
				if( !resultado ){
					//alert($(obj).val());
					//alert( "caracter no permitido" );
					ele.val("0.00");
					var id = ele.attr('indicador');
					ele.focus().select();
					recalculasaldo();
					return false;
				}

				//alert(numero_real);
				ele.val(numero);

				if(numero_real*1 != '0')
				{
					//alert("wclave");
					submit_form2("wclave");
				}
			}

		}




		recalculasaldo();


	}

	function alerta2( txt ){
		$("#textoAlerta").text( txt );
		$.blockUI({ message: $('#msjAlerta') });
			setTimeout( function(){
				$.unblockUI();
			}, 1000 );
	}

	function alerta4( txt ){
		$("#textoAlerta").text( txt );
		$.blockUI({ message: $('#msjAlerta') });
			setTimeout( function(){
				$.unblockUI();
			}, 5000 );
	}

	function alerta3( txt ){
		$("#textoAlerta1").text( txt );
		$('#msjAlerta1').show();
			setTimeout( function(){
				$('#msjAlerta1').hide();
			}, 2000 );
	}

	function pasarconenterabonos1(ele,e)
	{
		var esIE=(document.all);
		var esNS=(document.layers);
		var tecla=(esIE) ? event.keyCode : e.which;

		if(tecla==13)
		{

			actualizar_abonado(ele,e);
		}

	}

	function pasarconenterabonos2(ele,e)
	{
		var esIE=(document.all);
		var esNS=(document.layers);
		var tecla=(esIE) ? event.keyCode : e.which;


		if(tecla==13)
		{

			actualizar_abonado2(ele,e);
		}

	}





	function recalculasaldo ()
	{


		var valorabono=0;
			$(".abono").each(
				function() {
					var numero_real =  $(this).val().replace(/,/g, "");
					valorabono = (valorabono * 1) +  (numero_real * 1)
				}
			);

			var valorentrega=0;
			$(".entrega").each(
				function() {
					var numero_real =  $(this).val().replace(/,/g, "");
					valorentrega = (valorentrega * 1) +  (numero_real * 1)
				}

			);

			var saldo = $("#totalventa").val();

			var resultado 		= (saldo * 1) - (valorentrega*1);
			if(resultado > 0)
			{

				var resultadodos 	= (valorentrega*1) - (valorabono*1);
				if(resultadodos >= 0 )
				{
						resultadodos = formato_numero(resultadodos, '2', '.', ',');
						$("#wvalcam").val(resultadodos);
				}
			}
			else
			{


				var resultadotres 		= (saldo * 1) - (valorabono*1);
				if(resultadotres > 0)
				{

					var resultadodos 	= (valorentrega*1) - (valorabono*1);
					if(resultadodos > 0 )
					{

						resultadodos = formato_numero(resultadodos, '2', '.', ',');
						$("#wvalcam").val(resultadodos);
					}
					else
					{

						resultado = Math.abs(resultado)
						resultado = formato_numero(resultado, '2', '.', ',');
						$("#wvalcam").val(resultado);
					}
				}
				else
				{

						resultado = Math.abs(resultado)
						resultado = formato_numero(resultado, '2', '.', ',');
						$("#wvalcam").val(resultado);

				}
			}
	}

	function pasarconenter(ele,e)
	{
		var esIE=(document.all);
		var esNS=(document.layers);
		var tecla=(esIE) ? event.keyCode : e.which;
		var correcto = true;
		ele = jQuery(ele);

		var regla = /^(\d|-)?(\d|,)*\.?\d*$/;
        resultado = regla.test( ele.val() );
		//var cumplo ='si';
		//alert("1"+$(obj).val());
		if( !resultado ){
			//alert($(obj).val());
			alerta2( "caracter no permitido" );
			ele.val("0.00");
			var id = ele.attr('indicador');
			correcto = false;

		}
		var id = ele.attr('indicador');



		if (tecla==13 ){



			if(ele.val()!='0.00')
			{

				var numero_real =  ele.val().replace(/,/g, "");

				var valor_anterior = $("#wvalfpa\\["+id+"\\]").val();
				if(($("#wvalfpa\\["+id+"\\]").val().replace(/,/g, "") * 1 ) < ( numero_real  *1 ) && ($("#wvalfpa\\["+id+"\\]").val().replace(/,/g, "") * 1 )!='0')
				{
					var valor_anterior = $("#wvalfpa\\["+id+"\\]").val();
					$("#wvalfpa\\["+id+"\\]").focus().val("").val(valor_anterior).select();
				}
				else
				{
					var numero = formato_numero(numero_real, '2', '.', ',');
					ele.val(numero);
					$("#wvalfpa\\["+id+"\\]").focus().val("").val(numero).select();
				}



			}

		}else if ( !correcto ){
			$("#wvalfpa\\["+id+"\\]").val('0.00');
			recalculasaldo();
		}


	}

	function submit_form2(foco, forma, j)
	{

		var observacionppal = $("#wobserppal").val();
		//alert(observacionppal);
		$("#inputobservacion").val(observacionppal);

		var get = getGET();
		var parametros = "consultaAjax2=envio";
		var envia_form = "on";


		// Valida si se ha ingresado una fecha de vencimiento correcta para el articulo
		if(isset(document.getElementById("wfve")))
		{
			var valdate = validar_fecha(document.getElementById("wfve").value);
			var valmayor = rangoFechas(document.getElementById("wfecha_actual").value,document.getElementById("wfve").value);
			if(document.getElementById("wfve").value!="" && (valdate!="" || valmayor!=""))
			{
				envia_form = "off";
				alert(valdate+" "+valmayor);
				document.getElementById("wfve").focus();
				return false;
			}
		}

		// Valida si se ha ingresado código del remitente
		if(isset(document.getElementById("wcodrem")))
		{
			var remitente = document.getElementById("wcodrem").value;
			if(remitente=="" || remitente==" ")
			{
				envia_form = "off";
				alert("Debe ingresar el remitente");
				document.getElementById("wcodrem").focus();
				return false;
			}
		}



		if(envia_form != "off")
		{
			var formObj = document.getElementsByTagName("input");

			for (var i=0;i<formObj.length;i++)
			{
				if (formObj[i].name != undefined)
				{
					if (formObj[i].type == 'radio')
					{
						if(formObj[i].checked)
						{
							parametros += "&"+formObj[i].name+"="+formObj[i].value;
							if (formObj[i].name == 'wid')
							{
								parametros += "&wborrar=S";
							}
						}
					}
					else
					{
						parametros += "&"+formObj[i].name+"="+formObj[i].value;
						/*if (formObj[i].type == 'button' && formObj[i].name == 'wventa_btn')
							parametros += "&wventa=S";
						*/
					}
				}
			}

			var formObj = document.getElementsByTagName("select");
			for (var i=0;i<formObj.length;i++)
			{
				if (formObj[i].name != undefined)
					parametros += "&"+formObj[i].name+"="+formObj[i].value;
			}

		  //alert(parametros);

		  try
			{
				var ajax = nuevoAjax();

				ajax.open("POST", "Ventas_nue.php",true);
				ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				ajax.send(parametros);

				ajax.onreadystatechange=function()
				{

					// boton bloqueado
					$("#wok_btn").attr("disabled","disabled");
					var contenedor = document.getElementById('articulos_content');
					if (ajax.readyState==4 && ajax.status==200)
					{
						contenedor.innerHTML=ajax.responseText;

						if(isset(document.getElementById(foco)))
						{
							document.getElementById(foco).focus();

							if (forma=='Forma_pago')
							{
								var selec = document.getElementById(foco).value;
								if (selec =='BO - BONO DE DEVOLUCION')
								document.getElementById('wnrobon['+j+']').focus();
								else
								document.getElementById('wvalfpa['+j+']').focus();
							}
							if(foco!='wdato')
							{
								document.getElementById(foco).select();
							}
							else
								document.getElementById(foco).value="";
						}

						var formObjAlert = document.getElementsByName("walert");
						if(formObjAlert[0] && formObjAlert[0].value)
							alert(formObjAlert[0].value);

						if (isset(document.getElementById("wload").value))
						{
							if((document.getElementById("wload").value=="1"  || document.getElementById("wload").value=="2" || document.getElementById("wload").value=="3"))
							{
								submit_form('on');
							}
						}

						//Ventana modal que muestra el registro del usuario por la linea vital.
						var posicion = $("#registrar_linea_vital").offset();
						var margenSuperior = 15;
						$(window).scroll(function() {
							if ($(window).scrollTop() > posicion.top) {
								$("#registrar_linea_vital").stop().animate({
								});
							} else {
								$("#registrar_linea_vital").stop().animate({
									marginTop: 0
									});
								};
							 });


						var elemento = $("#bonos_dsct");
						var posicion = elemento.offset();
						$('#registrar_linea_vital').css({'left':posicion.left+70,'top':posicion.top+140});
						$('#registrar_linea_vital').show(500);
						$('#registrar_linea_vital').draggable();


						try{
							if($("#exiteimprimir").val()=='s'){
								//$( ".trocultar" ).css({display: "none"});
								//$( ".trocultar input" ).css({display: "none"});
								$(".trocultar input").attr("disabled",true);
								$(".trocultar select").attr("disabled",true);
							}
						}
						catch(e){
						}

						//==================================================================================
						//Muestra la ventana modal con los lab asociados y permitir mas descuentos.
						/* var posicion = $("#mas_descuentos").offset();
						if(posicion != null)
							{
							$('#laboratorios_flotante').hide();
							}
						var margenSuperior = 15;
						$(window).scroll(function() {
							if ($(window).scrollTop() > posicion.top) {
								$("#mas_descuentos").stop().animate({
								});
							} else {
								$("#mas_descuentos").stop().animate({
									marginTop: 0
									});
								};
							 });


						var elemento = $("#bonos_dsct");
						var posicion = elemento.offset();
						$('#mas_descuentos').css({'left':posicion.left+70,'top':posicion.top+65});
						$('#mas_descuentos').show(500);
						$('#mas_descuentos').draggable();  */


					}


					$("#nroventa").html($("#hiddennroventa").val());
					$(".entrega").each(
						function() {
							var valor = $(this).val();
							valor = $(this).val().replace(/,/g, "");
							valor = valor * 1 ;
							//alert($(this).attr('id')+"--"+$(this).attr('tipo'));
							if(valor == 0)
							{
								$(this).focus().val("").val("0.00").select();
								 // $(this).focus();

							}
						}

					);

					// desbloquear boton
					$("#wok_btn").removeAttr("disabled");
				}

				if ( !estaEnProceso(ajax) )
				{
					ajax.send(null);
				}
			}catch(e){	}
		}



	}

	function inactivar(campo)
	{
			campo.disabled = true;
	}

	function validar(e)
	{
		var esIE=(document.all);
		var esNS=(document.layers);
		var tecla=(esIE) ? event.keyCode : e.which;

		if (tecla==13) return true;
		else return false;
	}


	function valorpermitido(ele,e)
	{

		var esIE=(document.all);
		var esNS=(document.layers);
		var tecla=(esIE) ? event.keyCode : e.which;
		ele = jQuery(ele);


		var regla = /^(\d|-)?(\d|,)*\.?\d*$/;
        resultado = regla.test( ele.val() );
		//var cumplo ='si';
		//alert("1"+$(obj).val());
		if( !resultado ){
			//alert($(obj).val());
			alerta2( "caracter no permitido" );
			ele.val("0.00");
			var id = ele.attr('indicador');
			ele.focus().select();
			return false;
		}

		if (tecla==13)
		{
			if(ele.attr('tipo') == 'abono')
			{
				 var respuesta = 0;
				 var valortotalventa = $("#wtotventot").val();
				 var valorabonototal = 0;


				 var indicador_mandado= ele.attr('indicador');
				 var abono_mandado = 0;

				 $(".abono").each(function(){
						var indicador = $(this).attr('indicador');
						var valor_abono = $(this).val();
						var auxvalor_abono = $(this).val();
						valor_abono = valor_abono.replace(/,/g, "");
						var valor_saldo = $("#wsaldoppal_"+indicador).val();
						auxvalor_saldo = $("#wsaldoppal_"+indicador).val();
						valor_saldo = valor_saldo.replace(/,/g, "");

						if(indicador != indicador_mandado )
						{
							valorabonototal = (valorabonototal*1) + (valor_abono*1);
						}
						else
						{
							abono_mandado = valor_abono * 1;


						}




				 });

				valorabonototal = (valorabonototal*1) + (abono_mandado*1);

				 if ( (valortotalventa*1) - (valorabonototal *1) < 0 && abono_mandado*1 != 0  )
				 {
					 alerta4("El abono numero:"+indicador_mandado+"\npor "+abono_mandado+" \nes mayor que  el saldo "+formato_numero(auxvalor_saldo, '2', '.', ','));
					 ele.val("0.00");
					 respuesta = indicador_mandado;
					 ele.focus().select();



				 }

			}
            // if (respuesta != 0)
			// {
				// return false;
			// }
			var id = ele.attr("indicador");


			if(($("#wvalfpa\\["+id+"\\]").val().replace(/,/g, "") * 1 ) >   ($("#wvalfpa2\\["+id+"\\]").val().replace(/,/g, "") * 1 ))
			{
				alerta2("El Valor de lo abonado no puede ser superior a lo entregado");
				ele.val("0.00");
				ele.focus().select();
			}


			var abono = (ele.val().replace( /,/gi,"")*1);
			var entregado = ($("#wvalfpa2\\["+id+"\\]").val().replace( /,/gi,"")*1);
			if(abono > entregado)
			{
				ele.val($("#wvalfpa2\\["+id+"\\]").val());
			}


		}

		return true;




	}

	function validarformadepago(ele)
	{

		 var respuesta = 0;
		 var valortotalventa = $("#wtotventot").val();
		 var valorabonototal = 0;


		 var indicador_mandado= ele.attr('indicador');
		 var abono_mandado = 0;

		 $(".abono").each(function(){
				var indicador = $(this).attr('indicador');
				var valor_abono = $(this).val();
				var auxvalor_abono = $(this).val();
				valor_abono = valor_abono.replace(/,/g, "");
				var valor_saldo = $("#wsaldoppal_"+indicador).val();
				auxvalor_saldo = $("#wsaldoppal_"+indicador).val();
				valor_saldo = valor_saldo.replace(/,/g, "");

				if(indicador != indicador_mandado )
				{
					valorabonototal = (valorabonototal*1) + (valor_abono*1);
				}
				else
				{
					abono_mandado = valor_abono * 1;


				}




		 });

		 valorabonototal = (valorabonototal*1) + (abono_mandado*1);

		 if ( (valortotalventa*1) - (valorabonototal *1) < 0 && abono_mandado*1 != 0)
		 {
			 alerta4("El abono numero:"+indicador_mandado+"\npor "+abono_mandado+" \nes mayor que  el saldo "+formato_numero(auxvalor_saldo, '2', '.', ','));
			 ele.val("0.00");

			 respuesta = indicador_mandado;

		 }

		 return respuesta;


	}

	function actualizarabonado2(ele,e)
	{

		var esIE=(document.all);
		var esNS=(document.layers);
		var tecla=(esIE) ? event.keyCode : e.which;



			ele = jQuery(ele);
			var id = ele.attr("indicador");
			$("#wvalfpa\\["+id+"\\]").val("");
			$("#wvalfpa\\["+id+"\\]").val(ele.val());





	}

	/*
	// Llama a la función de submit_form cuando se teclea enter en un textbox
	document.onkeypress=function(e)
	{
		var esIE=(document.all);
		var esNS=(document.layers);
		var tecla=(esIE) ? event.keyCode : e.which;
		if(tecla==13)
		{
			submit_form('on');
		}
	}
	*/

	window.onload = function() {
    if (browser=="Microsoft Internet Explorer"){
               setInterval( "parpadear()", 500 );
        }
		submit_form('off','wdato');

		$('#ver_laboratorios').hide();
		$('#ver_articulos').hide();
	}


	function ir_a_facturar()
	{
		cargar_datapicker();
		$("#f_venta").val("");
		$("#f_venta").val($("#hiddennroventa").val());
		$("#wfnombrepac").val("");
		$("#wfnombrepac").val($("#wnompac").val());
		$("#f_documento").val("");
		$("#f_documento").val($("#wdocpac").val());

		$( "#factura_nueva" ).dialog({

			height: 800,
			width:  1300,
			modal: true,
			title: "Facturar"
		});

		//$("#f_documento").val($("#wdocpac").val());
		var nombre = "f_empresa";
		var HiddenArray = "hidden_empresas";
		Codini = $("#wempresa option:selected").attr("codigo");
		nomIni = $("#wempresa option:selected").attr("nombre");
		crear_autocomplete(nombre,HiddenArray,Codini,nomIni);

		nombre = "f_cco";
		HiddenArray = "hidden_cco";
		Codini = $("#cco_desde_ventas").val();
		nomIni = $("#nomcco_desde_ventas").val();
		crear_autocomplete(nombre,HiddenArray,Codini,nomIni);
		$("#wresponsable").val("");
		$("#resultados_busqueda").html("");

		$("#f_fecha_inicio").datepicker({
			showOn: "button",
			buttonImage: "../../images/medical/root/calendar.gif",
			buttonImageOnly: true,
			defaultTime: 'now'
		});

		$( "#f_fecha_inicio" ).datepicker({dateFormat:"yy-mm-dd"}).datepicker("setDate",new Date());

		$("#f_fecha_final").datepicker({
			showOn: "button",
			buttonImage: "../../images/medical/root/calendar.gif",
			buttonImageOnly: true,
			defaultTime: 'now'
		});

		$( "#f_fecha_final" ).datepicker({dateFormat:"yy-mm-dd"}).datepicker("setDate",new Date());

		$("#factura_nueva").show();

		validarautocompletar();

		if($("#nroventa").text()!='')
		{
			buscar_facturas();
		}


	}


	function cargar_datapicker()
	{
		$.datepicker.regional['esp'] = {
			closeText: 'Cerrar',
			prevText: 'Antes',
			nextText: 'Despues',
			monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
			'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
			monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
			'Jul','Ago','Sep','Oct','Nov','Dic'],
			dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
			dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
			dayNamesMin: ['D','L','M','M','J','V','S'],
			weekHeader: 'Sem.',
			dateFormat: 'yy-mm-dd',
			yearSuffix: ''
		};
		$.datepicker.setDefaults($.datepicker.regional['esp']);
	}

	function ir_a_abonar( wbasedato){



		$("#resultados_detalles_ventas").html("");
		$("#resultados_busqueda_ventas").html("");
		$("#f_venta_ventas").val("");
		$("#f_venta_ventas").val($("#hiddennroventa").val());
		$("#wfnombrepac_ventas").val("");
		$("#wfnombrepac_ventas").val($("#wnompac").val());
		$("#f_documento_ventas").val("");
		$("#f_documento_ventas").val($("#wdocpac").val());
		$("#f_cco_venta").val($("#cco_desde_ventas").val());
		$("#wbasedato1").val(wbasedato);
		$( "#dialog_abonos" ).dialog({

			height: 800,
			width:  1500,
			modal: true,
			title: "Realizar Abono"
		});

		//alert($("#wempresa").val());
		var empr= $("#wempresa").val();
		empr = empr.split("-");

		var codigoempre = empr[0];
		var nombreempre = empr[2];

		//alert($("#nroventa").text());
		if($("#nroventa").text()!='')
		{

			$.post("Ventas_nue.php",
				{
					consultaAjax:     '',
					wemp_pmla:        $('#wemp_pmla').val(),
					accion:           'buscar_ventas_especifica',
					wnumventa:		  $("#nroventa").text()


				},function(data) {

					//alert(data.query);
					if(data.hay=='si')
					{
						if(data.saldo =='0')
						{
							alert("Ya se pago el total de la venta");
							$("#dialog_abonos").dialog('close');
						}
						else
						{
							if(data.estado=='on')
							{
								Realizar_abono($("#nroventa").text() , data.saldo , codigoempre,nombreempre, $("#nroventa").text(),$("#resulventa").val(),data.recibo,$("#wnompac").val(),$("#wdocpac").val());
							}
							else
							{
								alert("La venta esta anulada");
							}
						}
					}
					else
					{
						if(data.estado=='on')
						{
							Realizar_abono($("#nroventa").text() , $("#resulsaldo").val() , codigoempre,nombreempre, $("#nroventa").text(),$("#resulventa").val(),$("#resulrec").val(),$("#wnompac").val(),$("#wdocpac").val());
						}
						else
						{
							alert("La venta esta anulada");
						}
					}




				},'json');

			//buscar_ventas();
			// Realizar_abono(venta,saldo,cod_empresa,nom_empresa,num_venta,val_total,val_recibo,nom_paciente,wcedula)
		}

	}
	function buscar_facturas()
	{

		var permitirbuscar = false;

		if($("#f_documento").val() != "")
			permitirbuscar = true;
		if($("#wfnombrepac").val() !="")
			permitirbuscar = true;
		if($("#f_venta").val() !="")
			permitirbuscar = true;




		if(permitirbuscar)
		{
			$("#resultados_busqueda").html("");
			$("#resultadofactura").remove();
			$.post("Ventas_nue.php",
				{
					consultaAjax:     '',
					wemp_pmla:        $('#wemp_pmla').val(),
					accion:           'buscar_facturas',
					wfdocumento:	  $("#f_documento").val(),
					wfempresa:		  $("#f_empresa").attr('valor'),
					wfcco:			  $("#f_cco").attr('valor'),
					wfinicio:		  $("#f_fecha_inicio").val(),
					wffinal:		  $("#f_fecha_final").val(),
					wfnombrepac:	  $("#wfnombrepac").val(),
					f_venta:	  	  $("#f_venta").val()

				},function(data) {

					$("#resultados_busqueda").html(data);
					$('.tooltip2').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });


				});
		}
		else
		{
			alert("Debe ingresar  al menos un criterio de busqueda");
		}

	}



	function buscar_ventas()
	{
		var permitirbuscar = false;

		if($("#f_documento_ventas").val() != "")
			permitirbuscar = true;
		if($("#wfnombrepac_ventas").val() !="")
			permitirbuscar = true;
		if($("#f_venta_ventas").val() !="")
			permitirbuscar = true;

		$("#resultados_busqueda_ventas").html("");
		$("#resultados_detalles_ventas").html("");
		//alert($("#wbasedato1").val());

		if(permitirbuscar)
		{

			//---------
			$(".btn_loading").attr("disabled","disabled");
			$("#botongrabarventanamodal").addClass("cargando");
			$("#botongrabarventanamodal").html('&nbsp;<img class="" border="0" src="../../images/medical/ajax-loader2.gif" title="Cargando.." >');


			//---------

			$("#resultados_busqueda").html("");
			$.post("Ventas_nue.php",
			{
				consultaAjax:     '',
				wemp_pmla:        $('#wemp_pmla').val(),
				accion:           'buscar_ventas',
				wfdocumento:	  $("#f_documento_ventas").val(),
				wfcco:			  $("#f_cco_venta").val(),
				wfnombrepac:	  $("#wfnombrepac_ventas").val(),
				f_venta:	  	  $("#f_venta_ventas").val(),
				wbasedato:		  $("#wbasedato1").val()

			},function(data) {

				$("#resultados_busqueda_ventas").html(data);
				$('.tooltip').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
				var this_btn_x = $(".cargando");
				$(this_btn_x).removeClass("cargando");
				$(this_btn_x).html("Grabar");
				$(".btn_loading").removeAttr("disabled");
			});
		}
		else
		{
			alert("Debe ingresar  al menos un criterio de busqueda");
		}



	}

	function facturar ()
	{


		var facturo = false;
		var vectorventas = new Object();
		var cuantas = 0;
		var nombrepersonal = '';
		var nitpersonal = '';
		var idventa = '';
		$('.checbox_facturacion:checked').each(
		 function() {
				facturo = true;
				numid = $(this).attr('codigo');
				idventa = numid;
				vectorventas[numid] = $("#numventa_sel_"+numid).val();
				//alert (numid);
				cuantas++;
			}

		);



		if (cuantas > 1)
		{
			nombrepersonal = 'VARIOS CLIENTES';
			nitpersonal = 'no';

		}
		else
		{
			nombrepersonal = $("#wnompac_"+idventa).val();
			nitpersonal = $("#wcedpac_"+idventa).val();

		}
		//alert (nombrepersonal) ;
		//return;

		if(facturo)
		{
			$(".btn_loading2").attr("disabled","disabled");
			$("#botongrabarventanamodal2").addClass("cargando2");
			$("#botongrabarventanamodal2").html('&nbsp;<img class="" border="0" src="../../images/medical/ajax-loader2.gif" title="Cargando.." >');

			// numid = $(this).attr('codigo');
			$.post("Ventas_nue.php",
			{
				consultaAjax:     '',
				wemp_pmla:        	 $('#wemp_pmla').val(),
				accion:           	 'facturar',
				vectorventas:	   	 vectorventas,
				wccofacturacion:  	 $("#f_cco").attr('valor'),
				wempresafacturacion: $("#wresponsable").val(),
				wfac:				 $("#wfac").val(),
				wiva:				 $("#wiva").val(),
				wcop:				 $("#wcop").val(),
				wcmo:				 $("#wcmo").val(),
				wdes:				 $("#wdes").val(),
				wnitpersonal:	     nitpersonal,
				wnombrepersonal:     nombrepersonal

			},function(data) {

				$("#resultados_busqueda").html(data);
				$( "#resultadofactura" ).dialog({

					height: 400,
					width:  500,
					modal: true,
					title: "Imprimir"
				});

				var this_btn_x = $(".cargando2");
				$(this_btn_x).removeClass("cargando2");
				$(this_btn_x).html("Grabar");
				$(".btn_loading2").removeAttr("disabled");



			});
		}
		else
		{
			alert("Debe seleccionar almenos una Factura");

		}

	}

	function grabar_abonos()
	{




		//formaspago
		var  total= 0;
		var  total2= 0;
		var datos   = new Array();
		var i=0;
		$('.formaspago').each(function() {

			var dato   = new Object();
			//alert($(this).val());
			var numero = ($(this).attr('numero'));
			var numero_formateado = $("#abonado_"+numero).val();
			var numero_real =  numero_formateado.replace(/,/g, "");
			numero_real = parseFloat(numero_real);
			total = (total * 1) + numero_real;
			var numero_formateado2= $("#wentregado_"+numero).val();
			var numero_real2 =  numero_formateado2.replace(/,/g, "");
			numero_real2 = parseFloat(numero_real2);
			total2 = (total2 * 1) + numero_real2;
			if(numero_real > 0)
			{
				dato['formapago'] = $(this).val();
				dato['valorpago'] = numero_real;
				//alert(numero_real);
				dato['banco'] = $("#div_select_banco_"+numero).val();
				dato['observacion'] = $("#observacion_"+numero).val();
				dato['anexo'] = $("#docanexo_"+numero).val();
				dato['ubicacion'] = $("#ubicacion_"+numero).val();
				dato['autorizacion'] = $("#autorizacion_"+numero).val();
				dato['datosbanco'] = $("#datosbanco_"+numero).val();
				datos.push(dato);
			}

		});

		// return;

		var datosJson = $.toJSON( datos )
		var cabono=  $("#wcambio_abonos").val() ;
		cabono = cabono.replace(/,/g, "");

		var asaldo = $("#Saldo_1").val();
		asaldo = asaldo.replace(/,/g, "");
			if((asaldo*1) == 0)
			{
				var totalpag = (total*1)

			}
			else
			{
				var totalpag = ((total*1));
			}

		if (totalpag == 0)
		{
			alert("No se puede generar un Recibo sin dinero");
			return;
		}

		variableautorizacion = false;
		$( ".autorizacion" ).removeClass('campoObligatorio');
		$( ".autorizacion" ).each(function( ) {
					//alert($(this).css('display'));

					if($(this).val()=='' && $(this).css('display')=='inline' && !$(this).is('[disabled]') )
					{
						$(this).addClass('campoObligatorio');
						variableautorizacion = true;
					}
		});

		//return;

		if(variableautorizacion)
		{
			alert("Debe llenar el numero de autorizacion");
			return;
		}

		//alert($("#textareaobservacio").val().length );
		if($("#textareaobservacio").val().length ==0)
		{
			var observacion = 'no' ;
		}
		else
		{
			var observacion = 'si' ;
		}

		$("#grabar_abono").attr("disabled",true);
		$.post("Ventas_nue.php",
		{
			consultaAjax:     '',
			wemp_pmla:        $('#wemp_pmla').val(),
			accion:           'grabar_abonos',
			wtotpag:		  totalpag,
			wcco:			  $("#cco_desde_ventas").val(),
			wemp:			  $("#hiddenempresa").val(),
			wnomemp:		  $("#hiddennomempresa").val(),
			wnum_venta:		  $("#hiddennum_venta").val(),
			wsaldorestante:   $("#hiddensaldo").val(),
			wvectordatos:	  datosJson,
			wsaldototal:	  ($("#Saldo_1").val().replace(/,/g, "") * 1),
			wentregadonose:	  total2,
			wobservacion:	  $("#textareaobservacio").val(),
			wclave:			  $("#claverecibo").val()


		},function(data) {


			if(data=='Clave Erronea')
			{
				alert("Digite la clave correctamente!!");

			}
			else
			{

				$("#ocultar_al_abonar").css("display" , "none");
				var numero_real =  totalpag;
				numero_real = formato_numero(numero_real, '2', '.', ',');
				$("#detalle_valor_recibido").html(numero_real);


				var numero_real = $("#Saldo_1").val();
				numero_real = numero_real.replace(/,/g, "");
				numero_real = formato_numero(numero_real, '2', '.', ',');
				$("#detalle_valor_saldo").html(numero_real);
				if(observacion =='si')
				{
					$("#imprimir_rec").html("<div ><table><tr><td class='fondoAmarillo'><b>¡¡¡Abono Generado Con Exito!!!!</b></td></tr></table></div><br><br><a href='Imp_documento.php?wnrodoc="+data+"&wfuedoc=30&wbasedato=uvglobal&wemp_pmla="+$('#wemp_pmla').val()+"&wcco="+$("#cco_desde_ventas").val()+"&wbandera=1&wnombrerec="+$("#hiddennombrecliente").val()+"&wcedularec="+$("#hiddencedulacliente").val()+"&wabentregado="+total2+"&wababonado="+$("#detalle_valor_recibido").html()+"&wabcambio="+$("#wcambio_abonos").val()+"&wabsaldo="+$("#Saldo_1").val()+"&obser=si'  style='color:blue' target='_blank'>Imprimir Recibo</a>")

				}
				else
				{
					$("#imprimir_rec").html("<div ><table><tr><td class='fondoAmarillo'><b>¡¡¡Abono Generado Con Exito!!!!</b></td></tr></table></div><br><br><a href='Imp_documento.php?wnrodoc="+data+"&wfuedoc=30&wbasedato=uvglobal&wemp_pmla="+$('#wemp_pmla').val()+"&wcco="+$("#cco_desde_ventas").val()+"&wbandera=1&wnombrerec="+$("#hiddennombrecliente").val()+"&wcedularec="+$("#hiddencedulacliente").val()+"&wabentregado="+total2+"&wababonado="+$("#detalle_valor_recibido").html()+"&wabcambio="+$("#wcambio_abonos").val()+"&wabsaldo="+$("#Saldo_1").val()+"'  style='color:blue' target='_blank'>Imprimir Recibo</a>")

				}
			}


		}).done(function(){

			//$("#grabar_abono").attr("disabled",false);
		});



	}



	function imprimir_documentos_rec()
	{
		/*Imp_documento.php?wnrodoc=206912&wfuedoc=30 &wbasedato=uvglobal&wemp_pmla=06&wcco=001*/
		//alert("hola");

	}

	function Realizar_abono(venta,saldo,cod_empresa,nom_empresa,num_venta,val_total,val_recibo,nom_paciente,wcedula){

		$("#resultados_busqueda_ventas").html("");
		$("#resultados_detalles_ventas").html("");
		$.post("Ventas_nue.php",
		{
			consultaAjax:     '',
			wemp_pmla:        $('#wemp_pmla').val(),
			accion:           'detalle_abono',
			wsaldo:			  saldo,
			wcod_empresa:     cod_empresa,
			wnom_empresa:	  nom_empresa,
			wnum_venta:		  num_venta,
			wval_total:		  val_total,
			wval_recibo:	  val_recibo,
			wnom_paciente:    nom_paciente,
			wcedula:		  wcedula

		},function(data) {


			$("#resultados_detalles_ventas").html(data);
			$("#wentregado_1").focus().select();

		});




	}

	function AgregarFormadePago()
	{

		var numero = (($("#hiddennumero").val()*1) + 1 );
		//alert(numero);
		$("#hiddennumero").val(numero);
		$.post("Ventas_nue.php",
		{
			consultaAjax:     '',
			wemp_pmla:        $('#wemp_pmla').val(),
			accion:           'AgregarFormadePago',
			numerohidden:	  numero

		},function(data) {

			if($(".entregado:last").val() * 1 ==0)
			{

			}
			else
			{
				$('#table_formas_pago tr:last').before(data);
			}

			//var r=$("#table_formas_pago tr").length;
			//alert(r);
			//$("#table_formas_pago").eq(r-3).before(data);

			//$('#table_formas_pago').append(data);
			// $(".entregado").eq($(".entregado").length - 1).focus().select();

			$(".entregado:last").focus().select();
			//$("#resultados_detalles_ventas").html(data);

		});

	}

	function actualizar_abonado(ele , e)
	{

			var esIE=(document.all);
			var esNS=(document.layers);
			var tecla=(esIE) ? event.keyCode : e.which;

			ele = jQuery(ele);
			if(ele.val()=='')
			{
				ele.val('0.00');
			}

			var regla = /^(\d|-)?(\d|,)*\.?\d*$/;
			resultado = regla.test( ele.val() );
			//var cumplo ='si';
			//alert("1"+$(obj).val());
			if( !resultado ){
				//alert($(obj).val());
				alerta3( "caracter no permitido" );
				ele.val("0.00");


			}


			var id = ele.attr("id");
			var numero = ele.attr("numero");



			// if (tecla==13) {

				var numero_real =  ele.val().replace(/,/g, "");
				//numero_real = parseFloat(numero);

				//alert(numero_real);
				var numero_formateado = formato_numero(numero_real, '2', '.', ',');
				ele.val(numero_formateado);

				if($("#abonado_"+numero).val().replace(/,/g, "")*1 < numero_real*1  &&   $("#abonado_"+numero).val().replace(/,/g, "")*1 != 0)
				{
					//$("#abonado_"+numero).val(numero_formateado);
					var numero_real =  $("#abonado_"+numero).val().replace(/,/g, "");
				}
				else
				{
					$("#abonado_"+numero).val(numero_formateado);
					var numero_real =  numero_formateado.replace(/,/g, "");
				}

				//$("#abonado_"+numero).focus().select();
				$("#abonado_"+numero).focus();



				numero_real = parseFloat(numero_real);
				recalcularSaldo(numero_real);
			// }
			// else
			// {
				//$("#abonado_"+numero).val(ele.val());
			// }



	}

	function crear_orden()
	{
		// alert($("#nroventa").text());
		if ($("#nroventa").text() =='')
		{



			url = "uvglobal00.php?wemp_pmla=06&cco="+$("#cco_desde_ventas").val()+"-"+$("#nomcco").text()+"";
			window.open(url, "nuevo", "directories=no, location=no, menubar=no, scrollbars=yes, statusbar=no, tittlebar=no, width=1000, height=600");

		}
		else
		{
			var ordenlab = $("#hiddennroventa").val();
			$.post("Ventas_nue.php",
			{
				consultaAjax:     '',
				wemp_pmla:        $('#wemp_pmla').val(),
				accion:           'buscarordenlab',
				wordenlab:		  ordenlab


			},function(data) {

				// alert(data);
				if(data == "1")
				{
					alert ("la orden de laboratorio para esta venta ya fue creada");
					url = "uvglobal00.php?wemp_pmla=06&cco="+$("#cco_desde_ventas").val()+"-"+$("#nomcco").text()+"";

				}
				else
				{

					url ="uvglobal01_fechaAjax.php?wemp_pmla=06&wdoc="+$("#wdocpac").val()+"&wven="+$("#hiddennroventa").val()+"&wproceso=Nuevo&wdesdeventas=si";

				}
				window.open(url, "nuevo", "directories=no, location=no, menubar=no, scrollbars=yes, statusbar=no, tittlebar=no, width=1000, height=600");


			});
		}

		//alert($("#wdocpac").val());
		//url = "RecibosNotasN.php?wemp_pmla=06&bandera=2&wfuente=30 - RECIBOS DE CAJA DE CARTERA&wempresa="+$("#wresrec").val()+"&fuefac="+$("#wfuenterec").val()+"&prefac="+$("#wprefijorec").val()+"&numfac="+$("#wnumfacrec").val()+"&wfecdoc="+wfecharecibo+"";
		$("#hiddennroventa").val();
		//alert($("#nroventa").text());



		if($("#nroventa").text() !='')
		{

		}

	}

	function actualizar_abonado2(ele,e){

		var esIE=(document.all);
		var esNS=(document.layers);
		var tecla=(esIE) ? event.keyCode : e.which;
		var llamoforma = 'si';
		ele = jQuery(ele);




		if(ele.val()=='')
		{
			ele.val('0.00');
		}

		// alert(ele.val().replace(/,/g, ""));
		// alert($(".saldo").val().replace(/,/g, "") * 1);

		var saldoaux = $("#hiddensaldo").val().replace(/,/g, "") * 1;
		var ttotal = 0;
		$(".abonado").each(function() {

				var tnumero =  $(this).val().replace(/,/g, "");
				tnumero = parseFloat(tnumero);
				ttotal = ttotal + tnumero;
		});


		if((ttotal * 1) >  saldoaux )
		{

			alerta3( "El valor abonado Es mayor al saldo" );
			ele.val('0.00');
			var llamoforma='no';



		}

		var id = ele.attr("id");
		var numero = ele.attr("numero");

		var regla = /^(\d|-)?(\d|,)*\.?\d*$/;
			resultado = regla.test( ele.val() );
			//var cumplo ='si';
			//alert("1"+$(obj).val());
			if( !resultado ){
				//alert($(obj).val());
				alerta3( "caracter no permitido" );
				ele.val("0.00");
				ele.focus().select();

			}
		//alert($("#abonado_"+numero).val().replace(/,/g, "") * 1 +" ------       "+$("#wentregado_"+numero).val().replace(/,/g, "") * 1);

		if($("#abonado_"+numero).val().replace(/,/g, "")*1 > $("#wentregado_"+numero).val().replace(/,/g, "")*1)
		{
			alerta3("valor abonado no puede ser mayor al entregado");
			// var numero_real = $("#wentregado_"+numero).val().replace(/,/g, "");
			var numero_real = "0.00";
			var llamoforma='no';

		}
		else
		{
			var numero_real =  ele.val().replace(/,/g, "");
		}
		// if (tecla==13) {


				// numero_real = parseFloat(numero);

				var numero_formateado = formato_numero(numero_real, '2', '.', ',');
				ele.val(numero_formateado);
				//$("#abonado_"+numero).val(numero_formateado);

				var numero_real =  numero_formateado.replace(/,/g, "");
				numero_real = parseFloat(numero_real);
				recalcularSaldo(numero_real);
			// }

			if($("#wentregado_"+numero).val().replace(/,/g, "")*1 =='0')
			{

			}
			else
			{
				//alert("aa");
				if($("#wentregado_"+((numero*1)+1)).length >= 1)
				{
					$(".entregado").each(function(){


								 $(this).Unselect();
								// $(this).focus().select();


							});
					$(".abonado").each(function(){
						$(this).Unselect();
					});
				}
				else
				{

					if(llamoforma == 'si')
					{
						AgregarFormadePago();

					}
					else
					{
							// $(".entregado").each(function(){

								// alert("entro");
								// $(this).Unselect();
								// $(this).focus().select();


							// });



					}
				}
			}
	}

	function recalcularSaldo (numero)
	{


		var saldo = $("#hiddensaldo").val();
		var total=0;
		var totale=0;
		$(".abonado").each(function() {

				var numero =  $(this).val().replace(/,/g, "");
				numero = parseFloat(numero);
				total = total + numero;
		});



		$(".entregado").each(function() {

				var numero =  $(this).val().replace(/,/g, "");
				numero = parseFloat(numero);
				totale = totale + numero;
		});

		//alert("total abonado"+total+"total entregado:"+totale+"");

		var resultado = ((saldo*1)-(total*1));
		var auxresultado = resultado;
		if(resultado < 0 )
		{
			//alert("entro2");
			var cambio = resultado;
			resultado  = 0;
			cambio = (cambio * (-1));
			cambio = formato_numero(cambio, '2', '.', ',');
			$("#wcambio_abonos").val(cambio);
		}

		if((total*1) <= (totale*1))
		{
			//alert("entro1");

			if(auxresultado < 0)
			{
				var cambio = (totale*1) - (total*1) + (auxresultado*-1);
			}
			else
			{
				var cambio = (totale*1) - (total*1)
			}

			cambio = formato_numero(cambio, '2', '.', ',');
			$("#wcambio_abonos").val(cambio);
		}
		else
		{
			//alert ("no entre");
		}
		var numero_formateado = formato_numero(resultado, '2', '.', ',');
		$(".saldo").val(numero_formateado);


	}

	function cambiarformapago(ele)
	{

			ele = jQuery(ele);
			var id = ele.attr("numero");
			$.post("Ventas_nue.php",
			{
				consultaAjax:     '',
				wemp_pmla:        $('#wemp_pmla').val(),
				accion:           'cambiarformapago',
				wfpaini:		   $("#sel_formapago_"+id).val()

			},function(data) {

				$("#div_select_banco_"+id).html(data);
				if($("#sel_formapago_"+id).val() == 11   )
				{
					 $("#abonado_"+id).removeAttr('disabled');
					 $("#autorizacion_"+id).val("");
					 $("#ubicacion_"+id).val("");
					 $("#datosbanco_"+id).val("");
					 $(".dos_"+id).css( "display" , "none");
				}
				else
				{
					// if( $("#sel_formapago_"+id).val() == 'BD')
					if( document.getElementById( "sel_formapago_"+id ).value == 'BD')
					{
						 $("#abonado_"+id).removeAttr('disabled');
						 $("#autorizacion_"+id).val("");
						 $("#ubicacion_"+id).val("");
						 $("#datosbanco_"+id).val("");
						 $(".dos_"+id).css( "display" , "none");


					}
					else
					{
						$("#abonado_"+id).attr('disabled','disabled');
						$("#abonado_"+id).val($("#wentregado_"+id).val());
						$(".dos_"+id).css( "display" , "");


					}

				}

			});




	}

	function ir_a_imprimirfac(venta)
	{
		//alert(venta)
		$( "#fechaactual" ).datepicker({dateFormat:"yy-mm-dd"}).datepicker("setDate",new Date());
		//$( "#fechaactual" ).datepicker({dateFormat:"yy-mm-dd"}).datepicker("setDate",new Date());


		url = "copia_factura.php?wnrovta="+venta+"&wfuefac="+$("#wfuenterec").val()+"&wnrofac="+$("#wprefijorec").val()+"-"+$("#wnumfacrec").val()+"&wfecini="+$( "#fechaactual" ).val()+"&wfecfin="+$( "#fechaactual" ).val()+"&wemp_pmla=06&wlotven=off&nocopia=1";

		//url = "RecibosNotasN.php?wemp_pmla=06&bandera=2&wfuente=30 - RECIBOS DE CAJA DE CARTERA&wempresa="+$("#wresrec").val()+"&fuefac="+$("#wfuenterec").val()+"&prefac="+$("#wprefijorec").val()+"&numfac="+$("#wnumfacrec").val()+"";
		window.open(url, "nuevo", "directories=no, location=no, menubar=no, scrollbars=yes, statusbar=no, tittlebar=no, width=1000, height=600");

		//http://matrixtest/matrix/ips/procesos/copia_factura.php?wnrovta=LA-25393&wfuefac=20&wnrofac=L-25424&wemp_pmla=06&wlotven=off

	}

	function limpiar_filtros(){

		  $("#f_documento").val("");
		  $("#wfnombrepac").val("");
		  $("#f_venta").val("");

	}

	function limpiar_filtros_abonos(){

		  $("#f_documento_ventas").val("");
		  $("#wfnombrepac_ventas").val("");
		  $("#f_venta_ventas").val("");

	}

	function crear_autocomplete(campo,HiddenArray,codIni,nomIni)
	{

		$("#"+campo).val(nomIni);
		$("#"+campo).attr("valor",codIni);
		$("#"+campo).attr("nombre",nomIni);

		var ArrayValores  = eval('(' + $('#'+HiddenArray).val() + ')');


		var ArraySource   = new Array();
		var index		  = -1;
		for (var CodVal in ArrayValores)
		{
			index++;
			ArraySource[index] = {};
			ArraySource[index].value  = CodVal;
			ArraySource[index].label  = ArrayValores[CodVal];
			ArraySource[index].name   = ArrayValores[CodVal];
		}



			$( "#"+campo ).autocomplete({
				minLength: 	0,
				source: 	ArraySource,
				select: 	function( event, ui ){
					$( "#"+campo ).val(ui.item.label);
					$( "#"+campo ).attr('valor', ui.item.value);
					$( "#"+campo ).attr('nombre', ui.item.name);

					return false;
				}
			});



	}

	function formato_numero(numero, decimales, separador_decimal, separador_miles){ // v2007-08-06
		numero=parseFloat(numero);
		if(isNaN(numero)){
			return "";
		}

		if(decimales!==undefined){
			// Redondeamos
			numero=numero.toFixed(decimales);
		}

		// Convertimos el punto en separador_decimal
		numero=numero.toString().replace(".", separador_decimal!==undefined ? separador_decimal : ",");

		if(separador_miles){
			// Añadimos los separadores de miles
			var miles=new RegExp("(-?[0-9]+)([0-9]{3})");
			while(miles.test(numero)) {
				numero=numero.replace(miles, "$1" + separador_miles + "$2");
			}
		}

		return numero;
	}
	function Sumarchequeada(i)
	{

	   var valor_total = 0;
	   var Valoriva = 0;
	   var Valorcopago = 0;
	   var Valorcmoderadora = 0;
	   var Valordesc = 0;
	   var Valorrecargo = 0;
	   var 	Valorrec = 0;
	   var 	Valorsaldo = 0;
	   var cambioresp = false;
	   var entro = 0;



	   $('.checbox_facturacion:checked').each(
		   function() {
				numid = $(this).attr('codigo');
				valor_total = (valor_total*1) + ($("#Valortotal_"+numid).val()*1);
				Valoriva = (Valoriva*1) + ($("#Valoriva_"+numid).val() * 1);
				Valorcopago = (Valorcopago*1) + ($("#Valorcopago_"+numid).val() * 1);
				Valorcmoderadora = (Valorcmoderadora*1) + ($("#Valorcmoderadora_"+numid).val() * 1);
				Valordesc = (Valordesc*1) + ($("#Valordesc_"+numid).val() * 1);
				Valorrec = (Valorrec*1) + ($("#Valorrecibo_"+numid).val() * 1);
				Valorrecargo = (Valorrecargo*1) + ($("#Valorrecargo_"+numid).val() * 1);
				Valorsaldo = (Valorsaldo*1) + ($("#Valorsaldo_"+numid).val() * 1);
				if( $("#wresponsable").val() != '' && $("#wresponsable").val()  != $("#codempresa_"+numid).val())
				{
					cambioresp = true;
					$(this).prop("checked", false);
					alert("No puede seleccionar ventas con diferentes responsables");


				}
				if($("#wresponsable").val() == '' &&  cambioresp == false )
				{
					$("#wresponsable").val($("#codempresa_"+numid).val());
				}
				entro = 1;



		   }
	   );

	   if (entro==0)
	   {
		$("#wresponsable").val("");

	   }




	   $("#wfac").val(valor_total);
	   $("#wiva").val(Valoriva);
	   $("#wcop").val(Valorcopago);
	   $("#wcmo").val(Valorcmoderadora);
	   $("#wdes").val(Valordesc);
	   $("#wrec").val(Valorrec);

	   var nvalor_total = formato_numero(valor_total, '2', '.', ',');
	   $("#totalfacseleccionadas").html(nvalor_total);


	   var nValoriva = formato_numero(Valoriva, '2', '.', ',');
	   $("#totalivaseleccionadas").html(nValoriva);

	   var nValorcopago = formato_numero(Valorcopago, '2', '.', ',');
	   $("#totalcopagoseleccionadas").html(nValorcopago);

	   var nValorcmoderadora = formato_numero(Valorcmoderadora, '2', '.', ',');
	   $("#totalcmoderadoraseleccionadas").html(nValorcmoderadora);

	   var nValordesc = formato_numero(Valordesc, '2', '.', ',');
	   $("#totaldescseleccionadas").html(nValordesc);

	   var nValorrecargo = formato_numero(Valorrecargo, '2', '.', ',');
	   $("#totalrecargoseleccionadas").html(nValorrecargo);

	   var nValorsaldo = formato_numero(Valorsaldo, '2', '.', ',');
	   $("#totalsald").html(nValorsaldo);


	   var nvalorrec = formato_numero(Valorrec, '2', '.', ',');
	   $("#totalrec").html(nvalorrec);


	}



	function ir_a_recibos()
	{
		// url = "../../../include/ips/Grabar_venta_nue.php"
		var f = new Date();
		var anorec = f.getFullYear();
		var mesrec = 0 ;
		if (f.getMonth()<9)
		{
			mesrec = '0' + (f.getMonth() +1) ;
		}
		else
		{
			mesrec = (f.getMonth() +1);
		}
		var diarec = 0;
		if( f.getDate() < 10 )
		{
			diarec =  '0' + f.getDate();
		}
		else
		{
			diarec =  f.getDate();
		}
		var wfecharecibo = anorec + "-" +  mesrec  + "-" + diarec;
		// aler (wfecharecibo);
		url = "RecibosNotasN.php?wemp_pmla=06&bandera=2&wfuente=30 - RECIBOS DE CAJA DE CARTERA&wempresa="+$("#wresrec").val()+"&fuefac="+$("#wfuenterec").val()+"&prefac="+$("#wprefijorec").val()+"&numfac="+$("#wnumfacrec").val()+"&wfecdoc="+wfecharecibo+"";
		window.open(url, "nuevo", "directories=no, location=no, menubar=no, scrollbars=yes, statusbar=no, tittlebar=no, width=1000, height=600");


	}


	function validarautocompletar(){
		$('.val_autocomplete').on({
			focusout: function(e) {
				if($(this).val().replace(/ /gi, "") == '')
				{
					$(this).val("");
					$(this).attr("valor","");
					$(this).attr("nombre","");
				}
				else
				{
					$(this).val($(this).attr("nombre"));
				}
			}
		});


	}

	function cerrar_imprimirfactura()
	{
		$("#resultadofactura" ).dialog('close');
	}

	function cerrar_ventana_facturas(){
		$("#factura_nueva").dialog('close');
	}

	function cerrar_ventana_abonos(){
		$("#dialog_abonos").dialog('close');
	}

	function verdetalleabono ( tr , numventa , color)
	{
		//alert($("#trdetalleabono_"+tr).length);

		if( $("#trdetalleabono_"+tr).length == 0)
		{

			$.post("Ventas_nue.php",
			{
				consultaAjax:     '',
				wemp_pmla:        $('#wemp_pmla').val(),
				accion:           'buscardetalledeabono',
				wnumventa:		  numventa,
				wemp_pmla:        $('#wemp_pmla').val(),
				wcco:			  $("#cco_desde_ventas").val(),
				color:			  color


			},function(data) {


						$("#tr_abonos_"+tr).after("<tr class='detalleabono_"+tr+"' id='trdetalleabono_"+tr+"'><td colspan='10'>&nbsp;</td></tr><tr class='detalleabono_"+tr+"'><td colspan='2'></td><td colspan ='6' align='center'><table class='"+color+"' ><tr><td>"+data+"</td></tr></table></td><td align='left' width='20px'><img width='16px' src='../../images/medical/hce/cancel.png' heigth='16px' style='cursor:pointer' onclick='cerrardetalle("+tr+")' ></td><td colspan='3'></td></tr><tr class='detalleabono_"+tr+"' ><td colspan='16'>&nbsp;</td></tr>");
						// $("#tr_abonos_"+tr).after("<tr><td ></td><td colspan ='5' class='"+color+"'>"+data+"</td><td><input type='button' value='cerrar' onclick='cerrardetalle('"+tr+"')'></td></tr>");
			});
		}else
		{

		}
		//

	}


	function cerrardetalle(tr){

		$(".detalleabono_"+tr).remove();
	}



</script>


<?php
  /***************************************************
   *     PROGRAMA PARA LA GRABACION DE LAS VENTAS    *
   *                  DE FARMASTORE                  *
   ***************************************************/

//==================================================================================================================================
//PROGRAMA                   : Ventas_nue.php
//AUTOR                      : Juan Carlos Hernández M.
//  $wautor="Juan C. Hernandez M. - Julio";
//FECHA CREACION             : Abril 28 de 2005
//FECHA ULTIMA ACTUALIZACION :

//DESCRIPCION
//========================================================================================================================================\\
//========================================================================================================================================\\
//     Este programa se hace con el objetivo de registrar las ventas de la empresa FARMASTORE, en donde se pueda luego realizar una       \\
//     facturación individual o por empresa y además de tener en cuenta que luego de poder facturar se generen los RIPS, además este      \\
//     programa tiene en cuenta la actualización del Inventario en línea, grabando también el movimiento de consumo en el inventario,     \\
//     El programa en general, tiene en cuenta el tipo de cliente, el responsable de la cuenta, las tarifas de los articulos según la     \\
//     empresa y el centro de costo (sucursal). tambien se tiene en cuenta que si la venta es para un particular o el paciente de         \\
//     empresa tiene que pagar salga una ventana en donde se le pide registrar un recibo de caja por el valor pagado.                     \\
//========================================================================================================================================\\
//ACTUALIZACIONES
//========================================================================================================================================\\
//Septiembre 02 DE 2015: Felipe Alvarez
//________________________________________________________________________________________________________________________________________\\
// Se hace un cambio en el programa para que al momento de hacer una venta no se genere una factura , esto se logra con un campo nuevo
// en la uvglobal_000024 que indica si la empresa genera factura (Empfac) si esta en on generara factura de lo contrario no generara en el momento
// de vender. Tambien se creo otro parametro en la tabla uvglobal_000024 (Emppre)  que indica si esta empresa recibe dinero cuando se realiza
// una venta.
// El programa tambien sufrio un cambio permitiendo ya buscar las ventas que no estan facturadas  permitiendo hacer la factura de estas ventas
// en cualquier momento, al momento de generar la factura  el programa crea una nueva factura y actualiza los campos que quedaron faltando en el
// momento de la venta.
// Tambien se puede hacer un abono a cualquier venta que le haga falta saldo para pagar , este abono se hace antes de realizar la factura.
//
// Acontinuacion se hara una explicacion mas detallada de los nuevos movimientos
//
// MOVIMIENTOS AL HACER UNA VENTA
//
// 1-Venta sin generar factura :( para la empresa o responsable a la que se le va hacer una venta en la tabla uvglobal_000024 debe estar el campo Empfac en off):
// La venta solo va a mover tablas que tienen que ver con solo ventas ya no se mueve ninguna tabla de facturacion es decir
// que solo mueve las tablas:
// 								uvglobal_000016 encabezado de ventas ,dejando los campos de Venffa,Vennfa vacios para su posterior actualizacion
//								uvglobal_000017 detalle de ventas
//
// 2-Venta sin generar factura y con recibo :
// se llenan las mismas tablas que se mueven en el movimiento anterior (Venta sin generar factura) ademas de las tablas que tiene que ver
// con los recibos los cuales son:
// 								uvglobal_000020 Encabezado de recibo de pago
//								uvglobal_000021 Recibo de caja detalle de facturas
//								uvglobal_000022 Recibo de caja detalle de forma de pago
//
//
//   Movimiento de inventarios para estos dos tipos:
//	 los movimientos de inventario tambien cambian pues se crean unos centros de costos virtuales dependiendo del centro de costos origen esto es
// 								para el centro de costos 001 se crea el centro de costos virtual 1001
//								para el centro de costos 002 se crea el centro de costos virtual 1002
//								para el centro de costos 003 se crea el centro de costos virtual 1003
//								para el centro de costos 004 se crea el centro de costos virtual 1004
//								para el centro de costos 005 se crea el centro de costos virtual 1005
//								para el centro de costos 006 se crea el centro de costos virtual 1006
//								para el centro de costos 007 se crea el centro de costos virtual 1007
//
// cuando se hace una venta y los articulos mueven inventario estos se mueven para su centro de costos correspondiente , es decir hacen
// un movimiento de traslado a su centro de costos correspondiente
//
//
// ya el movimiento de Venta generando factura es el que se ha venido haciendo siempre que afecta las tablas de la venta
//								uvglobal_000016, uvglobal_000017 y las de la facturacion.
//
//
// MOVIMIENTOS AL HACER UN ABONO
//
// Abono a venta sin factura con o sin recibo: este movimiento nos permite realizar un abono a una venta que tenga saldo pendiente por pagar
// las tablas que se mueven son:
//											uvglobal_000020 Encabezado de recibo de pago
//											uvglobal_000021 Recibo de caja detalle de facturas
//											uvglobal_000022 Recibo de caja detalle de forma de pago
//
//
//  MOVIMIENTOS AL HACER FACTURA
//
//	Factura a venta : Este movimiento se le hace a las ventas que no esten facturas , los movimiento de las tablas son los siguientes
//											uvglobal_000018  Se crea el encabezado de las factura.
//											uvglobal_000019	 Se crea el detalle de la factura y se relaciona con la venta
//											uvglobal_000065  se crea el detalle de facturas por concepto
//											uvglobal_000016 , se actualiza en el campo  Venffa la fuente de la factura y
//															, Vennfa el numero de la factura
//
//
//  Movimientos de inventario al hacer la factura de venta:
//	como anteriormente al hacer la venta sin factura se hace un traslado al centro de costos virtual al momento de facturar se vuelve a trasladar
//  al centro de costos de donde salio el articulo y desde este centro de costos se hace un movimientod de venta.
//
////________________________________________________________________________________________________________________________________________\\                                                                                                                    \\

//========================================================================================================================================\\
//========================================================================================================================================\\
//ACTUALIZACIONES
//========================================================================================================================================\\
//OCTUBRE 22 DE 2013: Jonatan Lopez
//________________________________________________________________________________________________________________________________________\\
//Se agrega al script los descuentos escalonados por cliente, esto quiere decir que de acuerdo a la cantidad de compras que un paciente
//haga de un articulo ira obteniendo un descuento.
//Ejm: Si compra dolex por primera vez aplicara un descuento (Este descuento solo aplicara
//si el usuario esta reportado por el laboratorio (tabla farpmla_000152) y segun la tabla de nivel de descuento
//(tabla farpmla_147(maestro de articulos - laboratorio), farpmla_151 (niveles de descuento))), la segunda vez aplicara un descuento mayor
//y asi sucesivamente, al llegar al mayor nivel de descuento se mantendra ese, si el cliente supera la cantidad de dias entre la ultima compra
//y la actual se devolverá el porcentaje de descuento configurado(la mayoria de veces al nivel 1).
////________________________________________________________________________________________________________________________________________\\                                                                                                                    \\
// M A R Z O  6  DE  2013:                                                                                                         \\
//________________________________________________________________________________________________________________________________________\\
// Se quita la condición if ((anyo<1900) || (anyo>2016)) de la función JavaScript validar_fecha pues no les estaba dejando poner fecha mayor
// del año 2016 en el vencimiento de un artículo
//
//________________________________________________________________________________________________________________________________________\\
// S E P T I E M B R E  5  DE  2012:                                                                                                         \\
//________________________________________________________________________________________________________________________________________\\
// Los campos accdettip y accdetnom de la tabla inaccdet de Unix, estaban apareciendo nulos para algunos registros, lo cual hacia que
// la pagina se quedará bloqueada. Se aplicó entonces para estos campos la verificacion de campo nulo mediante la funcion ejecutar_consulta_furips
// Mario Cadavid																														  \\
//========================================================================================================================================\\
//________________________________________________________________________________________________________________________________________\\
// J U N I O  12  DE  2012:                                                                                                         \\
//________________________________________________________________________________________________________________________________________\\
// Se adicionaron las funcionaes de validación de fecha en javascript, esto para validar el campo wfve (Fecha de vencimiento del articulo)
// También se adicionó la validación del campo wcodrem que es el código del remitente ya que estaba dejando grabar sin ingresarle este código
// Estas validaciones se hacen solo si los campos existen, esto para garantizar que no interfieran en ventas que no los involucran
// Mario Cadavid																														  \\
//________________________________________________________________________________________________________________________________________\\
// J U N I O  8  DE  2012:                                                                                                         \\
//________________________________________________________________________________________________________________________________________\\
// Se adicionaron los textbox wnumlot y wfve que permiten ingresar el número del lote y la fecha de vencimiento para los artículos de la
// venta, esto cuando no es venta a particular y solo para las empresas que tienen el parámetro incluye_lote_y_vencimiento = on en la
// tabla root_000051
// Se incluyen las funciones ejecutar_consulta_furips y validar_nulos_furips que permiten hacer la validación de campos nulos en consultas
// a Unix de una forma mas dinámica
// En la validación de campos nulos en Unix se incluyó el campo accdetffi que estaba llegando nulo en filas posteriores a la primera
// con la versión anterior de validación de nulos
// Mario Cadavid																														  \\
//________________________________________________________________________________________________________________________________________\\
//________________________________________________________________________________________________________________________________________\\
// M A Y O  14  DE  2012:                                                                                                         \\
//________________________________________________________________________________________________________________________________________\\
// Se hizo validación de campos nulos en Unix antes de obtener los datos. Se asigna un valor ' ' al campo si es nulo o vacio en las
// consultas a Unix
// Mario Cadavid																														  \\
//________________________________________________________________________________________________________________________________________\\
// M A Y O  2  DE  2012:                                                                                                         \\
//________________________________________________________________________________________________________________________________________\\
// Se cambió la validación de número de poliza y fecha de vencimiento para que cuando es la aseguradora estatal no valide estos campos
// Cuando se trae los valores de edad y sexo desde los datos del paciente y estos datos son vacios, se reemplazaba el valor que exista
// en los campos dejandolos vacios y no dejaba poner ningún valor al usuario, por esto se cambió para que el usuario pueda cambiar
// los valores en los campos edad y sexo y no los borre cuando consulta los datos del paciente
// Mario Cadavid																														  \\
//________________________________________________________________________________________________________________________________________\\
// A B R I L  19  DE  2012:
// Se validaron los movimientos de los bonos, se controlo que un pago en efectivo se grabe como bono, controlar la duplicacion de bonos
// dentro de la misma grabacion, filtrar los bonos que ya se han vencido, se corriguieron incosistencias en la acumulacion de los saldos
// de los bonos                                                                                                         \\
// Jerson Trujillo
//________________________________________________________________________________________________________________________________________\\
// Se adicionó la grabación del bono de devolución en la tabla 000143 teniendo en cuenta la columna Fpaval
// en la tabla 000023
// Mario Cadavid																														  \\
//________________________________________________________________________________________________________________________________________\\
// A B R I L  2  DE  2012:                                                                                                         \\
//________________________________________________________________________________________________________________________________________\\
// Se adicionó la grabación del bono de devolución en la tabla 000143 teniendo en cuenta la columna Fpaval
// en la tabla 000023
// Mario Cadavid																														  \\
//________________________________________________________________________________________________________________________________________\\
// D I C I E M B R E  9  DE 2011:                                                                                                         \\
//________________________________________________________________________________________________________________________________________\\
// En el condicional de la variable wload que permite saber si se llama la validación de datos de la parte superior del formulario		  \\
// se aumento el intervalo hasta 3 de modo que se llame validación de formulario si esta variable es iagual a 1,2 o 3.			      	  \\
// Mario Cadavid																														  \\
//________________________________________________________________________________________________________________________________________\\
// D I C I E M B R E  6  DE 2011:                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
// En el boton grabar venta y los campos wclave y wvalcam se agrego la inactivación de estos al hacer clic o al dar Enter de modo que     \\
// no se envie y grabe la información repetidamente cuando hacen clic o presionan enter varias veces 					 				  \\
// Se adicionó la función setInterval en javascript, dentro de window.onload, esto para que en ie funcione el parpadeo del <blink>
// Mario Cadavid																														  \\
//________________________________________________________________________________________________________________________________________\\
// D I C I E M B R E  2  DE 2011:                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
// Se adicionó el campo wload el cual permite llamar a submit_form('on') cuando se requiere un dato adicional en los pirmeros campos del  \\
// formulario, de este modo si se esta ya agregando los productos pero falta algún dato en la parte superior del formulario entonces	  \\
// wload define que se debe solicitar este dato
// Mario Cadavid																														  \\
//________________________________________________________________________________________________________________________________________\\
// N O V I E M B R E  24  DE 2011:                                                                                                       \\
//________________________________________________________________________________________________________________________________________\\
// En la consulta de datos de nit de empresas responsable 1 y 2 se modificó la consulta de la empresa responsable 2 para que no tome el   \\
// NIT con base en Unix sino en la tabla root_000051
// Mario Cadavid																														  \\
//________________________________________________________________________________________________________________________________________\\
// O C T U B R E  21  DE 2011:                                                                                                       \\
//________________________________________________________________________________________________________________________________________\\
// Se modificaron las funciones ajax para que habilite el cursor en el campo solicitado (poner foco ) segun el segundo parametro enviado  \\
// a las funciones submit_form y submit_form2
// Mario Cadavid																														  \\
//________________________________________________________________________________________________________________________________________\\
// O C T U B R E  18  DE 2011:                                                                                                       \\
//________________________________________________________________________________________________________________________________________\\
// Se permite la visualización de los botones "Cerrar ventana" y "Imprimir Copia de Factura" desde el inicio del programa ya que con el   \\
// que se hizo en Octubre 13 estos botones solo se visualizaban cuando se comenzaban a agregar productos.
// Mario Cadavid																														  \\
//________________________________________________________________________________________________________________________________________\\
// O C T U B R E  13  DE 2011:                                                                                                       \\
//________________________________________________________________________________________________________________________________________\\
// Se crea la funcion submit_form2 de javascript que permite llamar por ajax la seccion de adicion y listado de articulos de modo que al  \\
// darle enter en un textbox de la parte de datos del cliente, solo recargue esta parte por ajax y al darle enter en un textbox de la  	  \\
// parte de abajo donde está el listado de artículos solo cargue esta parte y asi sea más rápido el proceso de envio de datos en el script\\
// Mario Cadavid																														  \\
//________________________________________________________________________________________________________________________________________\\
// O C T U B R E  10  DE 2011:                                                                                                       \\
//________________________________________________________________________________________________________________________________________\\
// Se pone validación de responsable y topes en ventas SOAT, si la empresa responsable seleccionada no corresponde a la asociada al 	  \\
// documento del paciente en UNIX se muestra el respectivo mensaje de aviso y no se deja hacer la venta hasta que se seleccione el        \\
// responsable correcto, igual si el tope de la aseguradora se ha excedido se muestra el mensaje y no se deja hacer la venta al menos que \\
// se seleccione el segundo responsable que seria la aseguradora del estado, pero si el tope de aseguradora del paciente y la aseguradora \\
// del estado se han excedido no deja ejecutar la venta de ningun modo.																			  \\
// También se adicionó la grabación de venta en Unix como novedad en la tabla "fanovacc" y la actualización del saldo del accidente en    \\
// en Unix en la tabla "fasalacc"
// Mario Cadavid																														  \\
//________________________________________________________________________________________________________________________________________\\
//S E P T I E M B R E  23  DE 2011:                                                                                                       \\
//________________________________________________________________________________________________________________________________________\\
// Se cambia la consulte de topes de aseguradora del paciente y aseguradora del estado de modo que si el accidente tiene un tope asociado \\
// se trae éste, sino ahi si se carga el tope registrado para año actua en Matrix, esto en ventas SOAT                                    \\
//________________________________________________________________________________________________________________________________________\\
//S E P T I E M B R E  8  DE 2011:                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
// Aplicación de Ajax en el envío de datos del formulario, cambio de estilos para hacer el texto mas pequeño		  					  \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//A G O S T O  31  DE 2011:                                                                                                           	  \\
//________________________________________________________________________________________________________________________________________\\
// Adición de lista para seleccionar accidente a facturar. Se adiciona opción de seleccionar accidente a facturar		  				  \\
//cuando es venta por SOAT. Mario Cadavid  																								  \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//A G O S T O   19  DE 2011:                                                                                                           	  \\
//________________________________________________________________________________________________________________________________________\\
// Cambio de diseño basado en hoja de estilos del sistema. Adición de los datos de facturación en RIPS y enlace a consultar topes. 		  \\
// Carga automática de datos RIPS al ingresar documento de identificaciòn. Mario Cadavid												  \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//J U L I O   21  DE 2011:                                                                                                           	  \\
//________________________________________________________________________________________________________________________________________\\
//Se adicionó la visualización de topes y total facturado para la facturación por SOAT. Mario Cadavid									  \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//F E B R E R O   16   DE 2009:                                                                                                           \\
//________________________________________________________________________________________________________________________________________\\
//Se modifica el programa para que se puedan registrar o identificar ventas realizadas por internet, se agrego en el drop down de tipo de \\
//venta la palabra internet.                                                                                                              \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//E N E R O   14   DE 2009:                                                                                                               \\
//________________________________________________________________________________________________________________________________________\\
//Se adicionan los campos de diagnostico y rango al que pertenece un usuario, para los casos en los que la empresa exija esos datos.      \\
//esta modifcacion se hace inicialmente para el contrato de FARMASTORE CON COLSUBSIDIO-SUSALUD. Estos campos solo se piden al momento de  \\
//la venta si asi esta configurado en la empresa correspondiente (tabla 000024).                                                          \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//O C T U B R E   24   DE 2008:                                                                                                           \\
//________________________________________________________________________________________________________________________________________\\
//En Visual Global se necesita facturar unos excedentes, para esto se creo un articulo con tarifa de un peso ($1), pero por el redondeo   \\
//no mostraba el IVA, entonces se modifco el programa para que si la tarifa es de un peso ($1) muestre el IVA, siempre y cuando la        \\
//cantidad sea mayor a 3, con esto muestra discriminado el IVA.                                                                           \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//D I C I E M B R E   26   DE 2007:                                                                                                       \\
//________________________________________________________________________________________________________________________________________\\
//Se modifica el programa para que haga los movimientos o grabacion de registros queden con la estructura de la aplicación IPS, para poder\\
//utilizar los mismos programas de cartera, comprobantes y reportes para las diferentes empresas del grupo americas, como son:            \\
//FARMA STORE, CLINICA DEL SUR, SOE, INSTITUTO DE CANCEROLOGIA.                                                                           \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//M A Y O   5   DE 2006:                                                                                                                  \\
//________________________________________________________________________________________________________________________________________\\
//Cuando se hacian ventas a empleados se verificaba el cupo en nomina con X empleado y luego al ser aprobado el cupo se modificaba el     \\
//empleado, lo que hacia que el prestamo quedase con un empleado y la factura con otro, esto se modifico, para que verifique si se        \\
//modifico el empleado o se cambio el valor de la venta, si alguno de los casos ocurre, el programa exigira verificar de nuevo el cupo.   \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//A B R I L  18 DE 2006:                                                                                                                  \\
//________________________________________________________________________________________________________________________________________\\
//Se modifica el programa para que tenga en cuenta los descuentos por tipo de cliente (tabla 000042) en el cual se especifica que lineas  \\
//tiene descuento, es decir, dependiendo del tipo de cliente y la o las lineas de los productos que se esten comprando se calcula el      \\
//descuento que este configurado en la tabla de tipos de clientes (000042). Para esto se creo el campo de lineas (clelin) en la tabla     \\
//"000042" y el campo (temlpa) en la tabla "000034" y se creo tambien la variable de trabajo '$wlinpac'.                                  \\
//Esta modificacion quedo registrado en el requerimiento # 18.                                                                            \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//F E B R E R O  6 DE 2006:                                                                                                               \\
//________________________________________________________________________________________________________________________________________\\
//Se modifica el programa para que se puedan facturar servicios que no muevan inventarios, para esto se modifica la tabla de grupos (     \\
//farstore_00004 ) a la cual se le adiciono el campo gruinv, que indica en forma boleana si los articulos que pertenezcan a este grupo    \\
//afectan o no el inventario.                                                                                                             \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//O C T U B R E  25 DE 2005:                                                                                                              \\
//________________________________________________________________________________________________________________________________________\\
//Se modifica el programa porque el 24 de Octubre se realizo la misma venta por dos pantallas diferentes en la aguacatala y el programa   \\
//dejo realizarlas, por lo que se coloca el control que si no existe cantidad disponible en alguno de los articulos a vender no se realice\\
//la venta. Este control se hizo en el programa Grabar_venta.php. Con esto se evita que se vuelvan a generar negativos.                   \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//O C T U B R E  3 DE 2005:                                                                                                               \\
//________________________________________________________________________________________________________________________________________\\
//Se modifica el programa para que tome la nueva estructura de numeracion de facturas, recibos y notas, en donde se coloco numeracion     \\
//inicial y final para cada centro de costo asi como prefijo para las facturas; también se crearon las tablas Maestro de Bonos y Relacion \\
//Bonos X Linea, en donde se podra definir de acuerdo a un bono que lineas tienen descuento indicacndo cuando comienza y termina la       \\
//promoción.                                                                                                                              \\
//Se crea el campo 'empres' en el maestro de Empresas, para indicar de una empresa, que empresa se hace responsable, por ejemplo para los \\
//empleados de Promotora, cada empleado se crea como una empresa y a su vez la empresa responsable sera la clinica, para los empleados de \\
//Patologia la empresa responsable debe ser Patologia Las Americas S.A.                                                                   \\
//Tambien se modifico para que se generara factura cuando el campo 'empfac' este en 'on' en el maestro de Empresas.                       \\
//Se crea el campo 'temche' en el Maestro Tipos de Empresa,Con este campo se modifica el programa para que cuando el tipo de empresa tenga\\
//en 'on' este campo haga la verificación de pago por nomina,esto se utilizahasta el momento solo para empleados de las empresas de       \\
//Promotora.                                                                                                                              \\
//Se modifico el diseño de impresión de las Factura mostrando todos los valores con IVA, el descuento tambien lo muestra con el IVA       \\
//incluido, luego se muestra el resumen de IVA separando los valores de compra con IVA de los que no tienen.                              \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//S E P T I E M B R E  26 DE 2005:                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//Se modifica la presentacion de la pantalla de ventas, adicionanle las columnas de % descuento y descuento total por articulo, asi como  \\
//las columnas de valor venta con IVA y SIN IVA. Tambien se modifico el calculo de la base del iva para que solo tome el valor sin IVA de \\
//los articulos que tienen IVA menos el descuento dado al articulo, si lo tiene. Se adiciona la suma total de los descuentos.             \\
//Para los descuento se definio una sola variable                                                                                         \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//S E P T I E M B R E  21 DE 2005:                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//Se modifica el programa para que se puedan realizar descuentos dependiendo de la forma de pago y la linea del producto, por ejemplo:    \\
//Se le haran descuentos a los clientes que paguen con Bonos XX y lleve productos de la linea de improtados tendran un 10% de descuento   \\
//en esos productos.                                                                                                                      \\
//Para lograr hacer esto se creo la tabla Relacion formas de pago-lineas farstore_000047, en la cual se debe especificar la forma de pago,\\
//la linea a la cual se le va a hacer el descuento y la sublinea (opcional o si no todas), esto configiracion tiene un rango de fechas    \\
//de vigencia asi como un horario de aplicacion.                                                                                          \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//A G O S T O  10 DE 2005:                                                                                                                \\
//________________________________________________________________________________________________________________________________________\\
//Se crea la table de tipos de clientes especiales farstore_000042, en la cual se pueden especificar descuentos dentro de un rango de     \\
//fechas. Esta tabla esta ligada con la tabla de clientes farstore_000041, esta tabla se va grabando automaticamente a medida que se      \\
//realizan las ventas y a su vez toman la información de los clientes, todos los clientes que se registran desde la venta quedan con      \\
//el tipo de cliente GENERAL, para cambiar este tipo se tendra que ir directamente a la tabla de clientes.                                \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//A G O S T O  3 DE 2005:                                                                                                                 \\
//________________________________________________________________________________________________________________________________________\\
//SE CAMBIA LA FORMA DE CALCULAR EL IVA DEBIDO A QUE EL VALOR DE LA TARIFA YA LO TIENE INCLUIDO                                           \\
//                                                                                                                                        \\
//========================================================================================================================================\\
//========================================================================================================================================\\

session_start();

if (!isset($user))
	{
	 if(!isset($_SESSION['user']))
		session_register("user");
	}

if(!isset($_SESSION['user']))
	echo "error";
else
{

  include_once("root/comun.php");


  session_register("wpagook");
  session_register("wprestamo");




  //$conexunix = odbc_pconnect('facturacion','infadm','1201')
  //					    or die("No se ralizo Conexion con el Unix");

  $pos = strpos($user,"-");
  $wusuario = substr($user,$pos+1,strlen($user));

  																 // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= \\
 $wactualiz="Enero 30 de 2016";                    			 // Aca se coloca la ultima fecha de actualizacion de este programa \\
	                                                             // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= \\

  $wfecha=date("Y-m-d");
  $hora = (string)date("H:i:s");


if(!isset($consultaAjax))
	$consultaAjax = "";
if(!isset($consultaAjax2))
	$consultaAjax2 = "";

  if((isset($consultaAjax) && $consultaAjax=='envio'))
	echo "<form name='ventas' id='ventas' onsubmit='submit_form(\"on\")' method=post>";



  echo "<input type='HIDDEN' name='wini' id='wini' value='".$wini."'>";
  echo "<input type='HIDDEN' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";
  echo "<input type='HIDDEN' name='wfecha_actual' id='wfecha_actual' value='".$wfecha."'>";
 																											 // y quiere decir que no se puede hacer una venta sin recibir un peso


  if(!isset($wemp_pmla)){
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
	}

  // Llamo la función que me indica si se van a consultar datos desde Unix en la facturación por SOAT
  $consulta_unix = consultar_ase_estatal($wemp_pmla);

  $conex = obtenerConexionBD("matrix");

  $institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

  $wbasedato = $institucion->baseDeDatos;
  $wentidad = $institucion->nombre;
  $wingpos = $institucion->ingpos;

  if (!isset($wpidesiempredinero ))
	$wpidesiempredinero = consultarAliasPorAplicacion($conex, $wemp_pmla, "pedirabono");

  echo "<input type='hidden' name= 'wpidesiempredinero' value='".$wpidesiempredinero."'>"; // variable que se adiciona
																											 // es traida de un parametro en root_000051


  // Declaro campo de formulario que me indica si se van a consultar datos desde Unix en la facturación por SOAT
  echo "<input type='HIDDEN' name='consulta_unix' value='".$consulta_unix."'>";

  if (isset($wmedi)) echo "<input type='HIDDEN' name='wmedi' value='".$wmedi."'>";
  if (isset($wprog)) echo "<input type='HIDDEN' name='wprog' value='".$wprog."'>";
  if (isset($wremi)) echo "<input type='HIDDEN' name='wremi' value='".$wremi."'>";


  //$wpuntos="N";

  if ($wini == "S")  //'S' Indica que se esta iniciando una venta
     {
      $wfecha_tempo=$wfecha;
      $whora_tempo=$hora;
      $wpagook=0;           //Para indicar si la venta se hace con descuento por Nomina o NO   0:No 1:Si
      $wprestamo=0;
      $wchequeo="off";
      //$whabilita_venta="ENABLED";
      $whabilita_venta="";

      $wpuntos="N";


      //include_once("/pos/cierre.php");    //Se hace el cierre en la primera venta del mes siguiente

      $wfecha_bor=date("Y-m-d");

      //=============================================================================
	  //BORRO LOS REGISTROS DE LA TABLA DE VENTAS TEMPORALES
	  //=============================================================================
	  $q = "  DELETE FROM ".$wbasedato."_000034 "
	      ."   WHERE temfec <= str_to_date(ADDDATE('".$wfecha_bor."',-2),'%Y-%m-%d')";
	  $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	  //=============================================================================


      //Esto lo hago para indicar que la venta anterior ya termino, entonces inicializo las siguientes variables
      if (isset($wterm_vta) and ($wterm_vta=="S"))
         {
	      unset($wpidesiempredinero);
	      unset($wcarpun);
		  unset($wtipcli);
		  unset($wlotven);
		  unset($wempresa);
		  unset($wdocpac);
		  unset($wnompac);
		  unset($wte1pac);
		  unset($wdirpac);
		  unset($wmaipac);
		  unset($wcuotamod);
		  unset($wtipven);
		  unset($wtipfac);
		  unset($wmensajero);
		  unset($wdesemp);
		  unset($wdesart);
		  unset($wrecemp);
		  unset($wtotdes);
		  unset($wtotrec);
		  unset($wbondto);
		 }
	 }
    else
      {
       echo "<input type='HIDDEN' id= 'wfecha_tempo' name= 'wfecha_tempo' value='".$wfecha_tempo."'>";
	   echo "<input type='HIDDEN' id= 'whora_tempo' name= 'whora_tempo' value='".$whora_tempo."'>";
	  }

  echo "<input type='HIDDEN' name='wpagook' value='".$wpagook."'>";
  echo "<input type='HIDDEN' name='whabilita_venta' value='".$whabilita_venta."'>";

  //ACA TRAIGO LAS VARIABLES DE SUCURSAL, CAJA Y TIPO INGRESO QUE REALIZA CADA CAJERO
  $q =  " SELECT cjecco, cjecaj, cjetin, cjetem "
       ."   FROM ".$wbasedato."_000030 "
       ."  WHERE cjeusu = '".$wusuario."'"
       ."    AND cjeest = 'on' ";

  $res = mysql_query($q,$conex);
  $num = mysql_num_rows($res);
  if ($num > 0)
     {
      $row = mysql_fetch_array($res);

      $pos = strpos($row[0],"-");
      $wcco = substr($row[0],0,$pos);
      $wnomcco = substr($row[0],$pos+1,strlen($row[0]));

      $pos = strpos($row[1],"-");
      $wcaja = substr($row[1],0,$pos);
      $wnomcaj = substr($row[1],$pos+1,strlen($row[1]));

      $wtiping = $row[2];
      if (!isset($wtipcli)) $wtipcli = $row[3];
     }
    else
       echo "EL USUARIO ESTA INACTIVO O NO TIENE PERMISO PARA FACTURAR";

  $wcol=9;  //Numero de columnas que se tienen o se muestran en pantalla

  //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
  function bisiesto($year)
	{
     return(($year % 4 == 0 and $year % 100 != 0) or $year % 400 == 0);
	}


  function validar_fecha($dato)
	{
     $fecha="^([[:digit:]]{4})-([[:digit:]]{1,2})-([[:digit:]]{1,2})$";
	 if(ereg($fecha,$dato,$occur))
	   {
	    if($occur[2] < 0 or $occur[2] > 12)
	      return false;
	    if(($occur[3] < 0   or  $occur[3] > 31) or
	       ($occur[2] == 4  and $occur[3] > 30) or
	       ($occur[2] == 6  and $occur[3] > 30) or
		   ($occur[2] == 9  and $occur[3] > 30) or
		   ($occur[2] == 11 and $occur[3] > 30) or
		   ($occur[2] == 2  and $occur[3] > 29 and bisiesto($occur[1])) or
		   ($occur[2] == 2  and $occur[3] > 28 and !bisiesto($occur[1])))
		    return false;
		 return true;
	   }
	  else
	     return false;
	}

	function calcula_edad( $fecha ) {
		list($Y,$m,$d) = explode("-",$fecha);
		return( date("md") < $m.$d ? date("Y")-$Y-1 : date("Y")-$Y );
	}

	function traer_sexo( $sexo ) {
		if($sexo=='M')	return "M-Masculino";
		if($sexo=='F')	return "F-Femenino";
	}

	function traer_zona( $zona ) {
		if($zona=='U')	return "U-Urbana";
		if($zona=='R')	return "R-Rural";
	}

	//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&


  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //FUNCION PARA VERIFICAR QUE TODOS LOS CAMPOS ESTEN DIGITADOS Y DILIGENCIADOS CORRECTAMENTE
  function verifica_datos()
      {
	   global $wmedi;
	   global $wcodmed;
	   global $wnommed;
	   global $wprog;
	   global $wprograma;
	   global $wremi;
	   global $wcodrem;
	   global $wnomrem;
	   global $wdiag;
	   global $wran;
	   global $wtde;
	   global $wdiagnostico;
	   global $wrango;
	   global $wtipdes;

	   global $wrips;
	   global $wtipusu;
	   global $wtipdto;
	   global $wmuni;
	   global $wzona;
	   global $wsexo;
	   global $wpoliza;
	   global $wauto;

	   global $wtipven;
	   global $wtipcli;
	   global $wlotven;
	   global $wempresa;
	   global $wte1pac;
	   global $wnompac;
	   global $wtipfac;
	   global $wdocpac;
	   global $wbondto;

	   global $whabilita_venta;

	   global $wvaldat;
	   global $wfecven;
	   global $wedad;
	   global $wvalfacrips;

       global $wemp_pmla;
       global $conex;

	   $whabilita_venta="ENABLED";

        // Obtengo el NIT de la empresa responsable seleccionada en el formulario
        $wempresa_arr = explode('-',$wempresa);
        $wempresa_nit = $wempresa_arr[1];

        // Consulto código actual de la aseguradora estatal según root_000051
        $q =   "SELECT Detval "
            ."    FROM root_000051 "
            ."   WHERE Detemp = '".$wemp_pmla."' "
            ."     AND Detapl = 'aseguradoraEstatal' ";
        $res_ase_est = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
        $row_ase_est = mysql_fetch_array($res_ase_est);
        $ase_est = explode("-",$row_ase_est['Detval']);
        // Consulto el NIT de la aseguradora estatal
        $nit_ase_est = $ase_est[0];


	   if ($wmedi == "on")
	      {
		   if (!isset($wcodmed) or trim($wcodmed) == "" or trim($wcodmed) == "NO APLICA")
	          { $whabilita_venta="DISABLED";
	            $wvaldat="Codigo Médico"; }
	       if (!isset($wnommed) or trim($wnommed) == "" or trim($wnommed) == "NO APLICA")
	          { $whabilita_venta="DISABLED";
	            $wvaldat="Nombre Médico"; }
	      }

	   if ($wprog == "on")
	      if (!isset($wprograma) or trim($wprograma) == "" or trim($wprograma) == " - NO APLICA")
	         { $whabilita_venta="DISABLED";
	           $wvaldat="prog"; }

	   if ($wremi == "on")
	      {
	       if (!isset($wcodrem) or trim($wcodrem) == "" or trim($wcodrem) == "NO APLICA")
	          { $whabilita_venta="DISABLED";
	            $wvaldat="Codigo Médico Remitente"; }
	       if (!isset($wnomrem) or trim($wnomrem) == "" or trim($wnomrem) == "NO APLICA")
	          { $whabilita_venta="DISABLED";
	            $wvaldat="Nombre Médico Remitente"; }
	      }

	   if ($wdiag == "on")
	      if (!isset($wdiagnostico) or trim($wdiagnostico) == "" or trim($wdiagnostico) == " - NO APLICA")
	         { $whabilita_venta="DISABLED";
	           $wvaldat="Diagnostico"; }

	   if ($wran == "on")
	      if (!isset($wrango) or trim($wrango) == "" or trim($wrango) == " - NO APLICA")
	         { $whabilita_venta="DISABLED";
	           $wvaldat="Rango"; }

	   if ($wtde == "on")
	      if (!isset($wtipdes) or trim($wtipdes) == "" or trim($wtipdes) == " - NO APLICA")
	         { $whabilita_venta="DISABLED";
	           $wvaldat="Tipo de Despacho"; }

	   if ($wrips == "on")
	      {
		   if (!isset($wte1pac) or trim($wte1pac) == "SIN DATO")
		      { $whabilita_venta="DISABLED";
		        $wvaldat="Telefono"; }
		   if (!isset($wnompac) or trim($wnompac) == "CLIENTE PARTICULAR")
		      { $whabilita_venta="DISABLED";
		        $wvaldat="Nombre del Cliente"; }
		   if (!isset($wdocpac) or trim($wdocpac) == "9999")
		      { $whabilita_venta="DISABLED";
		        $wvaldat="Documento del Cliente"; }
		   if (!isset($wtipusu) or trim($wtipusu) == "")
	          { $whabilita_venta="DISABLED";
	            $wvaldat="Tipo de Usuario"; }
	       if (!isset($wtipdto) or trim($wtipdto) == "")
	          { $whabilita_venta="DISABLED";
	            $wvaldat="Tipo de Documento"; }
	       if (!isset($wmuni) or trim($wmuni) == "")
	          { $whabilita_venta="DISABLED";
	            $wvaldat="Ciudad"; }
	       if (!isset($wzona) or trim($wzona) == "")
	          { $whabilita_venta="DISABLED";
	            $wvaldat="Zona"; }
	       //if ((!isset($wsexo) or trim($wsexo) == "")  and $nit_ase_est != $wempresa_nit)
	       /*
		   if (!isset($wsexo) or trim($wsexo) == "")
	          { $whabilita_venta="DISABLED";
	            $wvaldat="Sexo"; }
			*/
           //if (!isset($wpoliza) or trim($wpoliza) == "")
           if ((!isset($wpoliza) or trim($wpoliza) == "") and $nit_ase_est != $wempresa_nit)
	          { $whabilita_venta="DISABLED";
	            $wvaldat="Poliza"; }
	       if (!isset($wauto) or trim($wauto) == "")
	          { $whabilita_venta="DISABLED";
	            $wvaldat="Autorización"; }
	       //if (!isset($wfecven) or trim($wfecven) == "" or validar_fecha($wfecven) == false)
           if ((!isset($wfecven) or trim($wfecven) == "" or validar_fecha($wfecven) == false) and $nit_ase_est != $wempresa_nit)
		     { //$whabilita_venta="DISABLED";
	            $wvaldat="Fecha de Vencimiento"; }
		   //if ((!isset($wedad) or trim($wedad) == "" or (is_numeric($wedad) == false) or ($wedad <= 0)) and $nit_ase_est != $wempresa_nit)
	       if (!isset($wedad) or trim($wedad) == "" or (is_numeric($wedad) == false) or ($wedad <= 0))
	          { $whabilita_venta="DISABLED";
	            $wvaldat="Edad"; }
	      }

	   if (!isset($wtipcli) or trim($wtipcli) == "")
	      { $whabilita_venta="DISABLED";
	        $wvaldat="Tipo de Cliente"; }
	   if (!isset($wtipven) or trim($wtipven) == "")
	      { $whabilita_venta="DISABLED";
	        $wvaldat="Tipo de Venta"; }
	   if (!isset($wempresa) or trim($wempresa) == "" or trim($wempresa) == "--")
	      { $whabilita_venta="DISABLED";
	        $wvaldat="Empresa"; }
	   if (!isset($wte1pac) or trim($wte1pac) == "")
	      { $whabilita_venta="DISABLED";
	        $wvaldat="Telefono"; }
	   if (!isset($wnompac) or trim($wnompac) == "")
	      { $whabilita_venta="DISABLED";
	        $wvaldat="Nombre del Cliente"; }
	   if (!isset($wtipfac) or trim($wtipfac) == "")
	      { $whabilita_venta="DISABLED";
	        $wvaldat="Tipo de Factura"; }
	   if (!isset($wdocpac) or trim($wdocpac) == "")
	      { $whabilita_venta="DISABLED";
	        $wvaldat="Documento del Cliente"; }
	   if (!isset($wbondto) or trim($wbondto) == "")
	      { $whabilita_venta="DISABLED";
	        $wvaldat="Bono VIP"; }

	   if (isset($wvalfacrips) && $wvalfacrips == "2")
	      { $whabilita_venta="DISABLED";
			$wvaldat="Responsable"; }
	   if (isset($wvalfacrips) && $wvalfacrips == "3")
	      { $whabilita_venta="DISABLED";
			$wvaldat="Tope responsable excedido"; }
	   if (isset($wvalfacrips) && $wvalfacrips == "4")
	      { $whabilita_venta="DISABLED";
			$wvaldat="Tope facturación excedido"; }
	   /*
	   if (isset($wfve) && $wfve < date("Y-m-d"))
	      { $whabilita_venta="DISABLED";
			$wvaldat="La fecha de vencimiento no puede ser menor a la actual"; }
		*/
	  }


  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //Busca el código del concepto de devoluciones
  function consultarConcepto($fconex)
  {
	global $wbasedato;

	//busco el codigo para el movimiento de venta
	$q="Select concod "
	."FROM ".$wbasedato."_000008 "
	."WHERE	conmve	= 'on' "
	."and	conest	= 'on' ";
	$err = mysql_query($q,$fconex) or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR EL CODIGO DEL MOVIMIENTO DE INVENTARIO EN VENTAS ".mysql_error());
	$num = mysql_num_rows($err) or die (mysql_errno()." -NO SE HA ENCONTRADO EL CODIGO DEL MOVIMIENTO DE INVENTARIO EN VENTAS  ".mysql_error());
	$row=mysql_fetch_array($err);
	$movven=$row['0'];

	//busco el codigo para el movimiento de devolucion
	$q="Select concod "
	."FROM ".$wbasedato."_000008 "
	."WHERE	concan	= '".$movven."' "
	."and	conest	= 'on' ";
	$err = mysql_query($q,$fconex) or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR EL CODIGO DEL MOVIMIENTO DE INVENTARIO EN DEVOLUCIONES".mysql_error());
	$num = mysql_num_rows($err) or die (mysql_errno()." -NO SE HA ENCONTRADO EL CODIGO DEL MOVIMIENTO DE INVENTARIO EN DEVOLUCIONES  ".mysql_error());
	$row=mysql_fetch_array($err);
	$movdev=$row['0'];

	return $movdev;
  }

  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //FUNCION PARA CALCULAR EL VALOR TOTAL DE UN BONO
	function consulta_total_bono($fconex,$movdev,$doc,$vennum)
	{
	    global $wfecha;
        global $hora;

	    global $wbasedato;
	    global $wusuario;
	    global $wcco;

		$total=0;

		$q="SELECT Mdeart,Artnom,Mdecan,Vdevun,  Vdepiv, Vdecan, Vdedes, ".$wbasedato."_000011.Fecha_data, ".$wbasedato."_000011.Hora_data "
			."FROM  ".$wbasedato."_000011, ".$wbasedato."_000017, ".$wbasedato."_000001 "
			."WHERE	Mdecon = '".$movdev."' "
			."AND	Mdedoc = '".$doc."' "
			."AND	Vdenum = '".$vennum."' "
			."AND	Vdeart = Mdeart "
			."AND	Artcod = Mdeart ";
		//echo $q."<br>";
		$err=mysql_query($q,$fconex);
		//echo mysql_errno().":".mysql_error();
		$num=mysql_num_rows($err);

		if($num > 0)
		{
			for($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$descuento=round(((($row["Vdepiv"]/100+1)*$row["Vdedes"])/$row["Vdecan"]),0);
				$articulos[$i]["Valuni"]=$row["Vdevun"]-$descuento;
				$articulos[$i]["Valtot"]=$articulos[$i]["Valuni"]*$row["Mdecan"];
				$total=$total+$articulos[$i]["Valtot"];
			}

		}

		return $total;
	}

	  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //FUNCION PARA MOSTRAR LAS OPCIONES DE RECIBOS DE DINERO - FORMAS DE PAGO
  function formasdepago($fk,$fconex,$fwcf,$fwcol,$fwclfg,$wfpa,$wdocane,$wobsrec,$wvalfpa,$wtotventot,$wnrobon)
      {

		global $wfecha;
        global $hora;
	    global $fk;
	    global $wcf2;
	    global $wclfa;
	    global $wbondto;
	    global $wbasedato;
	    global $wtipcli;
	    global $wlotven;
	    global $wusuario;
	    global $wfecha_tempo;
	    global $whora_tempo;
	    global $wcco;
	    global $wcaja;
	    global $wbandes;

	    global $wcuotamod;

	    global $whabilita_venta;

	    global $wtotbase_dev_iva;

		global $wfpa;
		global $wdocane;
		global $wobsrec;
		global $wvalfpa;
		global $wvalfpa2;
		global $wbandes;
		global $wautori;

	    //echo $whabilita_venta."<br>";

	    for ($j=1;$j<=$fk;$j++)
	        {
			  echo"<tr class='trocultar'>";
		      $q =  " SELECT fpacod, fpades "
			       ."   FROM ".$wbasedato."_000023 "
			       ."  WHERE fpaest = 'on' "
			       ."  ORDER BY fpacod ";

			  $res = mysql_query($q,$fconex); // or die (mysql_errno()." - ".mysql_error());;
			  $num = mysql_num_rows($res);    // or die (mysql_errno()." - ".mysql_error());;
	          if (isset($wfpa[$j]))
			  {
				  //////busco para la opcion seleccionada, si es tarjeta, cheque o valida forma de pago
				  $expefpa=explode('-',$wfpa[$j]);
				  $q =  " SELECT fpache, fpatar, fpaval "
					   ."   FROM ".$wbasedato."_000023 "
					   ."  WHERE fpacod =mid('".$wfpa[$j]."',1,instr('".$wfpa[$j]."','-')-1)";

				  $resche = mysql_query($q,$fconex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				  $numche = mysql_num_rows($resche) ;
				  $rowche = mysql_fetch_array($resche);

			  }
			  else
			  {
				$rowche[0]='off';
				$rowche[1]='off';
				$rowche[2]='off';
			  }

			  // Determina el ancho de la celda para la forma de pago
			  $width_fpa = "";
			  if ($rowche[2]=='on') $width_fpa = " width='270'";

			  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
			  //FORMA DE PAGO
			  echo "<td align=left class='fila2' colspan=2 ".$width_fpa." nowrap><div style='float:left'><b>Forma de pago: </b><br><select id='wfpa[".$j."]' name='wfpa[".$j."]' onchange='submit_form2(\"wfpa[".$j."]\", \"Forma_pago\", \"".$j."\" )'>";

			  if (isset($wfpa[$j]))
			     echo "<option value='".$wfpa[$j]."' selected>".$wfpa[$j]."</option>";

			  $q =  " SELECT fpabde "
			       ."   FROM ".$wbasedato."_000023 "
			       ."  WHERE fpacod = '".trim(substr($wfpa[$j],0,strpos($wfpa[$j],"-")))."'"
			       ."    AND fpaest = 'on' "
			       ."  ORDER BY fpacod ";
			  $res_bde = mysql_query($q,$fconex);
			  $row_bde = mysql_fetch_array($res_bde);
			  $wbas_dev=$row_bde[0];

			  for ($i=1;$i<=$num;$i++)
			     {
			      $row = mysql_fetch_array($res);
			      echo "<option value='".$row[0]." - ".$row[1]."'>".$row[0]." - ".$row[1]."</option>";

			      if ($i==1)
			         {
				      $wfpaini=$row[0];    //Forma de pago inicial, cuando apenas va registrar la forma de pago de la venta
			         }
			     }
			  echo "</select></div><div style='float:right'>";


			  $wexistebon="N";
	///////&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
	          if (isset($wfpa[$j]) and ($wbondto == "NO APLICA - NO APLICA"))     //Indica que si hay bono de descuento no busco formas de pago con bonos
	             {
		          if ($j >= 1)
		             {
			          $wwfpa=explode("-",$wfpa[$j]);

			          $q = " SELECT count(*) "
			              ."   FROM ".$wbasedato."_000057 "
			              ."  WHERE mid(codfpa,1,instr(codfpa,'-')-1) = '".trim($wwfpa[0])."'"
					      ."    AND tipemp                            = '".$wtipcli."'"
					      ."    AND fecha_ini                         <= '".$wfecha."'"
				          ."    AND fecha_fin                         >= '".$wfecha."'"
				          ."    AND hora_ini                          <= '".$hora."'"
				          ."    AND hora_fin                          >= '".$hora."'";

				      $resbonfpa = mysql_query($q,$fconex) or die (mysql_errno()." - ".mysql_error());
					  $numbonfpa = mysql_num_rows($resbonfpa);
					  $rowbonfpa = mysql_fetch_array($resbonfpa);

					  if ($rowbonfpa[0] > 0)
			             for ($h=1;$h<$j;$h++)
				             if ($wfpa[$h] == $wfpa[$j])
				                $wexistebon="S";
			         }
				    else
			           $wexistebon="N";


			      if ($wexistebon == "N")  //No Hay Bonos de Descuento, pero si hay formas de pago con Bonos
		             {
			          $wwfpa=explode("-",$wfpa[$j]);

			          $q = " SELECT lineas "
			              ."   FROM ".$wbasedato."_000057 "
			              ."  WHERE mid(codfpa,1,instr(codfpa,'-')-1) = '".$wwfpa[0]."'"
						  ."    AND tipemp                            = '".$wtipcli."'"
						  ."    AND fecha_ini                         <= '".$wfecha."'"
				          ."    AND fecha_fin                         >= '".$wfecha."'"
				          ."    AND hora_ini                          <= '".$hora."'"
				          ."    AND hora_fin                          >= '".$hora."'";
			          $resbonfpa = mysql_query($q,$fconex) or die (mysql_errno()." - ".mysql_error());
				      $numbonfpa = mysql_num_rows($resbonfpa);

				      if ($numbonfpa > 0)
				         {
					      $rowbonfpa = mysql_fetch_array($resbonfpa);
					      $wlineas=$rowbonfpa[0];

				          //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 			      //ACA TRAIGO TODO LO QUE HAY PENDIENTE DE FACTURAR EN ESTE CAJA
					      $q = " SELECT sum(temtot), pordscto, valorbono, compramin "
					          ."   FROM ".$wbasedato."_000034, ".$wbasedato."_000001, ".$wbasedato."_000057 "
					          ."  WHERE temusu                            = '".$wusuario."'"
					          ."    AND temfec                            = '".$wfecha_tempo."'"
					          ."    AND temhor                            = '".$whora_tempo."'"
					          ."    AND temsuc                            = '".$wcco."'"
					          ."    AND temcaj                            = '".$wcaja."'"
					          ."    AND temdem                            = 0 "
					          ."    AND temdar                            = 0 "
					          ."    AND temdpa                            = 0 "
					          ."    AND artcod                            = temart "
					          ."    AND mid(codfpa,1,instr(codfpa,'-')-1) = '".$wwfpa[0]."'"
							  ."    AND tipemp                            = '".$wtipcli."'"
							  ."    AND mid(artgru,1,instr(artgru,'-')-1) in (".$wlineas.") "
							  ."    AND fecha_ini                         <= '".$wfecha."'"
					          ."    AND fecha_fin                         >= '".$wfecha."'"
					          ."    AND hora_ini                          <= '".$hora."'"
					          ."    AND hora_fin                          >= '".$hora."'"
					          ."  GROUP BY pordscto, valorbono, compramin ";

					      $resbonfpa = mysql_query($q,$fconex) or die (mysql_errno()." - ".mysql_error());
					      $numbonfpa = mysql_num_rows($resbonfpa);

					      if ($numbonfpa > 0)
					         {
						      $rowbonfpa = mysql_fetch_array($resbonfpa);

						      $wcompracli = $rowbonfpa[0];
						      $wporcdscto = $rowbonfpa[1];
						      $wvalorbono = $rowbonfpa[2];
						      $wcompramin = $rowbonfpa[3];
						      if ($wcompracli >= $wcompramin)
						         if ($wporcdscto > 0)
						            $wvalfpa[$j] = $wcompracli*(1+($wporcdscto/100));
						           else
						              $wvalfpa[$j] = $wvalorbono;
						        else
						           {
						            unset ($wfpa[$j]);
									echo "<input type='hidden' name='walert' value='La compra NO alcanza el valor mínimo para aceptar esta forma de pago'>";
						            ?>
				    				  <script>
						                function ira(){document.ventas.elements[document.ventas.elements.length-8].focus(); }
				    				  </script>
				    				<?php
					               }
						     }
						    else
						       {
						        unset ($wfpa[$j]);
								echo "<input type='hidden' name='walert' value='En esta compra NO existen articulos habilitados para esta forma de pago'>";
						        ?>
				    			  <script>
						           function ira(){document.ventas.elements[document.ventas.elements.length-8].focus();}
				    			  </script>
				    			<?php
					           }
				         }
			         }
			        else
			           {
				        //unset ($wfpa[$j]);
				        $wfpa[$j]="";
						echo "<input type='hidden' name='walert' value='Ya se utilizó esta forma de pago'>";
				        ?>
		    			<?php
		    		   }
			     }
	///////&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
			  if ($rowche[2]=='on')  //Si valida forma de pago se muestra el campo de bono
				{
				  $jant = $j-1;

				  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
				  //BONO DE DEVOLUCIÓN
				  if (isset($wnrobon[$j]) and $wnrobon[$j]!="" and $wnrobon[$j]!=" ") //Si ya fue digitado el bono de devolucion
				    {
						$bono_aplicado=0;
						for($zz=1; $zz<$j; $zz++) //conocer si el bono ya se esta utilizando en la misma venta
						{
								if(@$wfpa[$zz]==$wfpa[$j] )
								{
									if (@$wnrobon[$zz]==$wnrobon[$j])
										{
										$bono_aplicado=1;
										}
								}

						}
						if($bono_aplicado==0)
						{
							// Consulto fuentes de devoluciones
							$q=  " SELECT Carfue "
								."   FROM ".$wbasedato."_000040 "
								."  WHERE Cardca= 'on' "
								."    AND Carest = 'on'";
							$err = mysql_query($q,$fconex) or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR LA FUENTE PARA BONOS ".mysql_error());
							$row=mysql_fetch_row($err);
							$fue=$row[0]; //fuente de la transaccion

							// Consulto el detalle de la devolución
							$q= " SELECT Tranum, Traven, Tracco, Tradev "
							   ."   FROM ".$wbasedato."_000055 "
							   ."  WHERE Trafue='".$fue."'"
							   ."    AND Tranum = '".$wnrobon[$j]."' ";
							$resbon1 = mysql_query($q,$fconex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
							$existe_bono = mysql_num_rows($resbon1) ;
							//echo $q."<br>";

							if($existe_bono>0)
							{
                                //Consulto que el bono no este vencido
                                $q= " SELECT Tranum, Traven, Tracco, Tradev, Trafec "
                                   ."   FROM ".$wbasedato."_000055 "
                                   ."  WHERE Trafue='".$fue."'"
                                   ."    AND Tranum = '".$wnrobon[$j]."' "
                                   ."	 AND DATEDIFF(CURDATE(), Trafec)<30";//2012-04-18 filtrar bonos que tengan mas de 30 dias de expedicion osea que esten vencidos
                                $resbon = mysql_query($q,$fconex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                                $no_vencido = mysql_num_rows($resbon) ;
                                $rowbon = mysql_fetch_array($resbon);

                                if($no_vencido>0)
                                {
                                    // Consulto si el bono ya fue usado
                                    $q= " SELECT Vfpnro "
                                       ."   FROM ".$wbasedato."_000143 "
                                       ."  WHERE Vfpnro = '".$wnrobon[$j]."' ";
                                    $resred = mysql_query($q,$fconex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                                    $numred = mysql_num_rows($resred) ;

                                    if($numred==0)
                                    {
                                       $movdev = consultarConcepto($fconex);
                                       $total_bono = consulta_total_bono($fconex,$movdev,$rowbon['Tradev'],$rowbon['Traven']);
                                       if(!isset($wpagado))
                                        {
                                            $wpagado=0;
                                        }
                                       $saldo=$wtotventot-$wpagado;
                                       if ($total_bono-$saldo > 0)
                                            {
                                                echo "<b>Nro Bono: </b><br><INPUT TYPE='text' id='wnrobon[".$j."]' NAME='wnrobon[".$j."]' size=10 onkeypress='if (validar(event)) submit_form2(\"OK1\")'></div>";  //wnrobon
                                                echo "<br><div style='float:right'><blink ><b class='articuloControl' align=center >El valor del Bono: $".$total_bono." . No puede superar el valor de la compra</b></blink>";
                                                unset($total_bono);
                                            }
                                        else
                                                echo "<b>Nro Bono: </b><br><INPUT TYPE='text' id='wnrobon[".$j."]' NAME='wnrobon[".$j."]' VALUE='".$wnrobon[$j]."' size=10 onkeypress='if (validar(event)) submit_form2(\"OK1\")'>";  //wnrobon
                                    }
                                }
							}

							// Si no encontro el bono o el bono esta vencido o el bono ya fue usado
							if($existe_bono==0 || $no_vencido==0 || $numred>0 )
							{
								//echo "<input type='hidden' name='walert' value='El bono ingresado no existe o ya ha sido redimido'>";
								echo "<b>Nro Bono: </b><br><INPUT TYPE='text' id='wnrobon[".$j."]' NAME='wnrobon[".$j."]' size=10 onkeypress='if (validar(event)) submit_form2(\"OK1\")'></div>";  //wnrobon
								if($existe_bono==0)
                                    echo "<br><div style='float:right'><blink><b class='articuloControl'>Bono No Existe</b></blink>";
								elseif($no_vencido==0)
                                        echo "<br><div style='float:right'><blink><b class='articuloControl'>Bono Vencido</b></blink>";
                                    else
                                        echo "<br><div style='float:right'><blink><b class='articuloControl'>Bono Ya Usado</b></blink>";
							}
						}
						else
							{
							echo "<b>Nro Bono: </b><br><INPUT TYPE='text' id='wnrobon[".$j."]' NAME='wnrobon[".$j."]' size=10 onkeypress='if (validar(event)) submit_form2(\"OK1\")'></div>";
							echo "<br><div style='float:right'><blink><b class='articuloControl'>Bono Repetido</b></blink>";
							}
					}
					else
					{
					   echo "<b>Nro Bono: </b><br><INPUT TYPE='text' id='wnrobon[".$j."]' NAME='wnrobon[".$j."]' size=10 onkeypress='if (validar(event)) submit_form2(\"OK1\")'>";                        //wnrobon
					}
				}

			  echo "</div></td>";

			  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
			  //DOCUMENTO ANEXO
			  if (isset($wdocane[$j])) //Si ya fue digitado el documento anexo
			     echo "<td class='fila2' colspan=1 nowrap=nowrap><b>Dcto Anexo: </b><br><INPUT TYPE='text' NAME='wdocane[".$j."]' VALUE='".$wdocane[$j]."' size=10 onkeypress='if (validar(event)) submit_form2()'></td>";  //wdocane
			    else
			       echo "<td class='fila2' colspan=1 nowrap=nowrap ><b>Dcto Anexo: </b><br><INPUT TYPE='text' NAME='wdocane[".$j."]' size=10 onkeypress='if (validar(event)) submit_form2()'></td>";                        //wdocane

			  if ($rowche[0]=='on' or $rowche[1]=='on')  //Si es tarjeta o cheque pido los sgtes datos adicionales
				{
				 $obliga[$j]='on';
				 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			     //BANCO, consulto lista de bancos
			     //echo "<td class=".$wcf2." colspan=1><b><font text color=".$wclfa.">Datos del Banco:<br></font></b><select name='wobsrec[".$j."]' >";
			     echo "<td class=fila2 colspan=1><b>Datos del Banco:<br></b><select name='wobsrec[".$j."]'>";

			     $q = " SELECT bancod, bannom "
			         ."   FROM ".$wbasedato."_000069 "
			         ."  WHERE banest  = 'on' "
			         ."    AND bancag != 'on'";

			     $resu = mysql_query($q,$fconex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			     $num1 = mysql_num_rows($resu) ;
				 for ($y=1;$y<=$num1;$y++)
				    {
		 		     $banc = mysql_fetch_array($resu);

		 		     if (isset($wobsrec[$j]) and $wobsrec[$j]==$banc[0].'-'.$banc[1])    //Si ya fue digitado la observacion
		                echo "<option value='".$banc[0].'-'.$banc[1]."' selected>".$banc[0].'-'.$banc[1]."</option >";     //wobsrec
		               else
		                  echo "<option value='".$banc[0].'-'.$banc[1]."'>".$banc[0].'-'.$banc[1]."</option>";
				    }
				 echo "</select></td>";

				 $colspan=9;
				 /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			     //PLAZA
			     if (isset($wubica[$j])) //Si ya fue digitada la plaza
			       {
				    If ($wubica[$j]=='1-Local')
				       $otro='2-Otras plazas';
				  	  else
				  	     $otro='1-Local';
			        //echo "<td class=".$wcf2." colspan=1><b><font text color=".$wclfa.">Ubicacion: </font></b><select name='wubica[".$j."]' ><option selected>".$wubica[$j]."</option ><option>".$otro."</option></select></td>";
			        echo "<td class='fila2' colspan=1><b><font text color=".$wclfa.">Ubicacion: </font></b><select name='wubica[".$j."]' ><option value='".$wubica[$j]."' selected>".$wubica[$j]."</option ><option value='".$otro."'>".$otro."</option></select></td>";
		           }
			      else
			         echo "<td class='fila2' colspan=1><b><font text color=".$wclfa.">Ubicacion: </font></b><select name='wubica[".$j."]' ><option value='1-Local' selected>1-Local</option ><option value='2-Otras plazas'>2-Otras plazas</option></select></td>";                        //wdocane
			         //echo "<td class=".$wcf2." colspan=1><b><font text color=".$wclfa.">Ubicacion: </font></b><select name='wubica[".$j."]' ><option selected>1-Local</option ><option>2-Otras plazas</option></select></td>";                        //wdocane
			     ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			     //NUMERO DE AUTORIZACION
			     if (isset($wautori[$j])) //Si ya fue digitada la autorizacion
			        echo "<td class='fila2' colspan=1 nowrap=nowrap><b><font text color=".$wclfa.">Nº autorización: 1</font></b><br><INPUT TYPE='text' NAME='wautori[".$j."]' class='autorizacion' VALUE='".$wautori[$j]."' size=10 onkeypress='if (validar(event)) submit_form2()'></td>";     //wobsrec
			       else
			          echo "<td class='fila2' colspan=1 nowrap=nowrap><b><font text color=".$wclfa.">Nº autorización: 2</font></b><br><INPUT TYPE='text' NAME='wautori[".$j."]' class='autorizacion' size=10 onkeypress='if (validar(event)) submit_form2()'></td>";                           //wobsrec
			    }
			   else
			      {
				  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
				  //OBSERVACIONES
				  if (isset($wobsrec[$j])) //Si ya fue digitado la observacion
				     echo "<td class='fila2' colspan=3><b>Observ: </b><br><INPUT TYPE='text' NAME='wobsrec[".$j."]' VALUE='".$wobsrec[$j]."' onkeypress='if (validar(event)) submit_form2()'></td>";     //wobsrec
				    else
				       echo "<td class='fila2' colspan=3><b>Observ: </b><br><INPUT TYPE='text' NAME='wobsrec[".$j."]' onkeypress='if (validar(event)) submit_form2()'></td>";                           //wobsrec
		          }

			  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
			  //Con la siguiente instrucción en Javascript se ubica el cursor en el ultimo campo del valor de la forma de pago osea en: $wvalfpa[$j] : en el VALOR
			  //$wvalfpa ==> Valor forma de pago
			  ?>
			    <script>
			      //function ira(){document.ventas.elements.length;}
			      //function ira(){document.ventas.elements[document.ventas.elements.length-1].focus();}
			      function ira(){document.ventas.elements[document.ventas.elements.length-4].focus();}
			    </script>
			  <?php

			  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
			  //VALOR
			  $jj=$j+1;


			  if (isset($wvalfpa[$j]) && $wvalfpa[$j] > 0 ) //Si ya fue digitado el valor y es mayor a cero
			     {
				  $wpagado=0;
			      for ($y=1;$y<=$j;$y++)
					{
						if($rowche[2]=='on' && @$wnrobon[$j]!=" " )
						{
							$wvalfpa[$j]=@$total_bono;
						}
						 $wpagado=$wpagado+$wvalfpa[$y];
					}
			      $wvalfpa[$j]=str_replace(",","",$wvalfpa[$j]); //Esto se hace para quitarle el formato que trae el número
			      $wvalfpa2[$j]=str_replace(",","",$wvalfpa2[$j]); //Esto se hace para quitarle el formato que trae el número

				  if ($rowche[2]=='on' && isset($total_bono) && $total_bono>0)  //Si valida forma de pago se muestra el valor del bono
				  {
					$wvalfpa[$j]=$total_bono;
					echo "<td class='fila2' colspan=1 nowrap=nowrap><b>Valor entregado:</b><br><INPUT TYPE='text' id='wvalfpa2[".$j."]'  tipo='entregado' class='entrega'  indicador='".$j."' NAME='wvalfpa2[".$j."]' VALUE='".@number_format($wvalfpa2[$j],2,'.',',')."' SIZE=15 onkeyup='pasarconenter(this,event)'  onblur='actuailizardatos(this)'  readonly='readonly'></td>";  //wvalfpa
					echo "<td class='fila2' colspan=1 nowrap=nowrap><b>Valor Abonado: </b><br><INPUT TYPE='text' id='wvalfpa[".$j."]'   tipo='abono' class='abono' indicador='".$j."'  NAME='wvalfpa[".$j."]' VALUE='".@number_format($wvalfpa[$j],2,'.',',')."' SIZE=15 onblur='actuailizardatos(this)' onkeyup='if(valorpermitido(this,event)){if (validar(event)){ submit_form2(\"wclave\")}}' readonly='readonly'></td>";  //wvalfpa
					echo "<script> submit_form2('wclave'); </script>";
					}
				  else
				  {
                      if($rowche[2]=='on' && @$wnrobon[$j]==" ")//2012-04-18 si la forma de pago es Bono de Devolucion y el campo numero del bono esta en vacio el campo valor queda en 0
                          {

                          $wvalfpa[$j]=0;
                          if ($j>1)
                          $wpagado=$wvalfpa[$j-1];
                          else
                          $wpagado=0;
                          echo "<td class='fila2' colspan=1 nowrap=nowrap><b>Valor entregado:</b><br><INPUT TYPE='text' id='wvalfpa2[".$j."]'  tipo='entregado' class='entrega'  indicador='".$j."' NAME='wvalfpa2[".$j."]' VALUE='".@number_format($wvalfpa[$j],2,'.',',')."' SIZE=15 onkeypress='if (validar(event)) submit_form2(\"wclave\")' readonly='readonly'></td>";
                          echo "<td class='fila2' colspan=1 nowrap=nowrap ><b>Valor Abonado: </b><br><INPUT TYPE='text' id='wvalfpa[".$j."]'   tipo='abono' class='abono' indicador='".$j."'  NAME='wvalfpa[".$j."]' VALUE='".@number_format($wvalfpa[$j],2,'.',',')."' SIZE=15 onblur='actuailizardatos(this)'  onkeyup='if(valorpermitido(this,event)){if (validar(event)){ submit_form2(\"wclave\")}}' readonly='readonly'></td>";  //wvalfpa

						  }
                      elseif($rowche[2]=='on' && (!isset($total_bono) or @!isset($wnrobon[$j]) ))
					  {
                               //echo "<td class='fila2' colspan=1><b>Valor entregado:</b><br><INPUT TYPE='text' id='wvalfp2[".$j."]' NAME='wvalfpa2[".$j."]' VALUE='".@number_format($wvalfpa[$j],2,'.',',')."' SIZE=15 onkeypress='if (validar(event)) submit_form2(\"wclave\")' readonly='readonly'></td>";
                                echo "<td class='fila2' colspan=1 nowrap=nowrap ><b>Valor entregado:</b><br><INPUT TYPE='text' id='wvalfp2[".$j."]' NAME='wvalfpa2[".$j."]' VALUE='".@number_format($wvalfpa2[$j],2,'.',',')."' SIZE=15 onkeyup='pasarconenter(this,event)'  onblur='actuailizardatos(this)'  readonly='readonly' tipo='entregado' class='entrega'  indicador='".$j."'></td>";
								echo "<td class='fila2' colspan=1 nowrap=nowrap ><b>Valor Abonado: </b><br><INPUT TYPE='text' id='wvalfpa[".$j."]'   tipo='abono' class='abono' indicador='".$j."'  NAME='wvalfpa[".$j."]' VALUE='".@number_format($wvalfpa[$j],2,'.',',')."' SIZE=15 onblur='actuailizardatos(this)' onkeyup='if(valorpermitido(this,event)){if (validar(event)){ submit_form2(\"wclave\")}}' readonly='readonly'></td>";  //wvalfpa

					  }
							else
					  {

								echo "<td class='fila2' colspan=1 nowrap=nowrap><b>Valor entregado:</b><br><INPUT TYPE='text' id='wvalfpa2[".$j."]'  tipo='entregado' class='entrega'  indicador='".$j."' NAME='wvalfpa2[".$j."]' VALUE='".@number_format($wvalfpa2[$j],2,'.',',')."' SIZE=15 onkeyup='pasarconenter(this,event)'  onblur='actuailizardatos(this)' ></td>";       //wvalfpa
								if($wfpa[$j]=="11 - EFECTIVO" or $wfpa[$j]=="" )
								{
									echo "<td class='fila2' colspan=1 nowrap=nowrap><b>Valor Abonado: </b><br><INPUT TYPE='text' id='wvalfpa[".$j."]'   tipo='abono' class='abono' indicador='".$j."'  NAME='wvalfpa[".$j."]' VALUE='".@number_format($wvalfpa[$j],2,'.',',')."' SIZE=15 onblur='actuailizardatos(this)' onkeyup='if(valorpermitido(this,event)){if (validar(event)){ submit_form2(\"wclave\")}}' ></td>";  //wvalfpa
								}
								else
								{
									echo "<td class='fila2' colspan=1 nowrap=nowrap><b>Valor Abonado: </b><br><INPUT TYPE='text' id='wvalfpa[".$j."]'   tipo='abono' class='abono' indicador='".$j."'   NAME='wvalfpa[".$j."]' VALUE='".@number_format($wvalfpa2[$j],2,'.',',')."' SIZE=15 onblur='actuailizardatos(this)' onkeyup='if(valorpermitido(this,event)){if (validar(event)){ submit_form2(\"wclave\")}}' readonly='readonly'></td>";  //wvalfpa
								}
					  }
				  }


					  if (($wtotventot-$wpagado) > 0 )
					  {
							if ($wcuotamod>0)
							{
								if ($wpagado > $wcuotamod)
								{
									echo "<td class='fondoAmarillo'  colspan=1><b>Saldo: </b><font color='#FF0000'>".number_format(0,0,'.',',')."</font><input type='hidden' id='wsaldoppal_".$j."'  value ='".(0)."' indicador='".$j."'></td>";            //wtotventot-wtotfpa
								}
								else
								{
								 echo "<td class='fondoAmarillo'  colspan=1 ><b>Saldo: </b><font color='#FF0000'>".number_format(($wcuotamod-$wpagado),0,'.',',')."</font><input type='hidden' id='wsaldoppal_".$j."' value ='".($wcuotamod-$wpagado)."' indicador='".$j."' ></td>";            //wtotventot-wtotfpa
								}
						   }
						   else
						   {
							   echo "<td class='fondoAmarillo'   colspan=1><b>Saldo: </b><font color='#FF0000'>".number_format(($wtotventot-$wpagado),0,'.',',')."</font><input type='hidden' id='wsaldoppal_".$j."' value ='".($wtotventot-$wpagado)."' indicador='".$j."' ></td>";            //wtotventot-wtotfpa
						   }
						   //echo "<td>wtotventot".$wtotventot."---".($wtotventot-$wpagado)."-wpagado".$wpagado."</td>";
					  }
					  else
					  {
						   echo "<td class='fondoAmarillo'   colspan=1><b>Saldo: </b><font color='#FF0000'>".number_format((0),0,'.',',')."</font><input type='hidden' id='wsaldoppal_".$j."' value ='".(0)."' indicador='".$j."' ></td>";                             //wtotventot-wtotfpa
					  }
			     }
			    else
			       {
				    if ($wcuotamod > 0)
				       {
			            //echo "<td class='fila2' colspan=1><b>Valor entregado:</b><br><INPUT TYPE='text' id='wvalfpa2[".$j."]'  tipo='entregado' class='entrega'  indicador='".$j."' NAME='wvalfpa2[".$j."]' VALUE='".@number_format($wcuotamod,2,'.',',')."' SIZE=15 onkeypress='if (validar(event)) submit_form2(\"wclave\")'></td>";  //wvalfpa
			            echo "<td class='fila2' colspan=1 nowrap=nowrap><b>Valor entregado: </b><br><INPUT TYPE='text' id='wvalfpa2[".$j."]'  tipo='entregado' class='entrega'  indicador='".$j."' NAME='wvalfpa2[".$j."]' VALUE='".@number_format($wcuotamod,2,'.',',')."' SIZE=15 onkeyup='pasarconenter(this,event)'  onblur='actuailizardatos(this)' ></td>";  //wvalfpa
					    echo "<td class='fila2' colspan=1 nowrap=nowrap><b>Valor Abonado: </b><br><INPUT TYPE='text' id='wvalfpa[".$j."]'   tipo='abono' class='abono' indicador='".$j."'   NAME='wvalfpa[".$j."]' VALUE='".@number_format($wvalfpa[$j],2,'.',',')."' SIZE=15 onblur='actuailizardatos(this)' onkeypress='if(valorpermitido(this,event)){if(validar(event)){ submit_form2(\"wclave\")}}' readonly='readonly'></td>";  //wvalfpa

					   }
		              else
					  {
						  if ($rowche[2]=='on')  //Si valida forma de pago
						  {	//$total_bono=0;
							//echo "<td class='fila2' colspan=1><b>Valor entregado:</b><br><INPUT TYPE='text' id='wvalfpa2[".$j."]'  tipo='entregado' class='entrega'  indicador='".$j."' NAME='wvalfpa2[".$j."]' VALUE='".@number_format($total_bono,2,'.',',')."' SIZE=15 onkeypress='if (validar(event)) submit_form2(\"wclave\")' readonly='readonly'></td>";  //wvalfpa
							echo "<td class='fila2' colspan=1 nowrap=nowrap><b>Valor entregado:</b><br><INPUT TYPE='text' id='wvalfpa2[".$j."]'  tipo='entregado' class='entrega'  indicador='".$j."' NAME='wvalfpa2[".$j."]' VALUE='".@number_format($total_bono,2,'.',',')."' SIZE=15 onkeyup='pasarconenter(this,event)'  onblur='actuailizardatos(this)'  readonly='readonly'></td>";  //wvalfpa
							echo "<td class='fila2' colspan=1 nowrap=nowrap><b>Valor Abonado: </b><br><INPUT TYPE='text' id='wvalfpa[".$j."]'   tipo='abono' class='abono'  indicador='".$j."'  NAME='wvalfpa[".$j."]' VALUE='".@number_format($wvalfpa[$j],2,'.',',')."' SIZE=15 onblur='if(valorpermitido(this,event)){{ submit_form2(\"wclave\")}}' onkeyup='if(valorpermitido(this,event)){if (validar(event)){ submit_form2(\"wclave\")}}' readonly='readonly'></td>";  //wvalfpa

							if(@$total_bono>0) echo "<script> submit_form2('wclave'); </script>";
						  }
						  else
						  {
							//echo "<td class='fila2' colspan=1><b>Valor entregado:</b><br><INPUT TYPE='text' id='wvalfpa2[".$j."]'  tipo='entregado' class='entrega'  indicador='".$j."' NAME='wvalfpa2[".$j."]' SIZE=15 onkeypress='if (validar(event)) submit_form2(\"wclave\")'></td>";  //wvalfpa
							echo "<td class='fila2' colspan=1 nowrap=nowrap><b>Valor entregado:</b><br><INPUT TYPE='text' id='wvalfpa2[".$j."]'  tipo='entregado' class='entrega'  indicador='".$j."' NAME='wvalfpa2[".$j."]'  VALUE='".@number_format($wvalfpa2[$j],2,'.',',')."'  SIZE=15 onkeyup='pasarconenter(this,event)'  onblur='actuailizardatos(this)'  ></td>";  //wvalfpa
							if($wfpa[$j]=="11 - EFECTIVO" or  $wfpa[$j]=="")
							{
								echo "<td class='fila2' colspan=1 nowrap=nowrap><b>Valor Abonado: </b><br><INPUT TYPE='text' id='wvalfpa[".$j."]'   tipo='abono' class='abono' indicador='".$j."'  NAME='wvalfpa[".$j."]' VALUE='".@number_format($wvalfpa[$j],2,'.',',')."' SIZE=15 onblur='actuailizardatos(this)' onkeyup='if(valorpermitido(this,event)){if (validar(event)){ submit_form2(\"wclave\")}}' ></td>";  //wvalfpa
							}
							else
							{
								echo "<td class='fila2' colspan=1 nowrap=nowrap ><b>Valor Abonado: </b><br><INPUT TYPE='text' id='wvalfpa[".$j."]'   tipo='abono' class='abono' indicador='".$j."'   NAME='wvalfpa[".$j."]' VALUE='".@number_format($wvalfpa[$j],2,'.',',')."' SIZE=15 onblur='actuailizardatos(this)' onkeyup='if(valorpermitido(this,event)){if (validar(event)){ submit_form2(\"wclave\")}}'  readonly='readonly' ></td>";  //wvalfpa
							}
						  }
					  }
						if($wtotventot-$wpagado > 0)
						{
							echo "<td class='fondoAmarillo'  colspan=1><b>Saldo: </b><font color='#FF0000'>".number_format(($wtotventot-$wpagado),0,'.',',')."</font><input type='hidden' id='wsaldoppal_".$j."' value='".($wtotventot-$wpagado)."' indicador='".$j."'></td>";            //wtotventot-wtotfpa
						}
						else
						{
							echo "<td class='fondoAmarillo'  colspan=1><b>Saldo: </b><font color='#FF0000'>".number_format(0,0,'.',',')."</font><input type='hidden' id='wsaldoppal_".$j."' value='".(0)."' indicador='".$j."' ></td>";            //wtotventot-wtotfpa

						}
				   }

	  			  $total_bono = 0;


		      //////////////////////////////////////////////////////////////
		      //BANCO EN EL QUE SE CONSIGNA O DESTINO
		      echo "<td class='fila2' colspan=1><b>En que Banco se consigna:<br></b><select name='wbandes[".$j."]'>";



		      /*
		      if (!isset($wbandes[$j]))
		         {
			      //Busco si la forma de pago tiene un banco (valido) por defecto
			      $q = " SELECT fpacba "
			          ."   FROM ".$wbasedato."_000023, ".$wbasedato."_000069 "
			          ."  WHERE fpacod = '".$wfpago[0]."'"
			          ."    AND fpacba = bancod ";
			      $resban = mysql_query($q,$fconex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		          $numban = mysql_num_rows($resban) ;
		          if ($numban > 0)  //Si tiene un banco por defecto el query es diferente que si no lo tuviera
		             {
			          //Con BANCO por defecto
			          $wbandef = mysql_fetch_array($resban);
			          $q =  "  SELECT bancod, bannom "
			          	   ."    FROM ".$wbasedato."_000069 "
			               ."   WHERE bancod = '".$wbandef[0]."'"
			               ."   UNION "
			          	   ."  SELECT bancod, bannom "
			               ."    FROM ".$wbasedato."_000069 "
			               ."   WHERE banest  = 'on' "
			               ."     AND banrec  = 'on' "
			               ."     AND bancod != '".$wbandef[0]."'";
			         }
			        else
			           {
				        //Sin BANCO por defecto
					    $q =  "  SELECT bancod, bannom "
					         ."    FROM ".$wbasedato."_000069 "
					         ."   WHERE banest = 'on' "
					         ."     AND bancag = 'on' "
					         ."     AND banrec = 'on' "
					         ."   UNION "
					         ."  SELECT bancod, bannom "
				             ."    FROM ".$wbasedato."_000069 "
				             ."   WHERE banest  = 'on' "
				             ."     AND bancag != 'on' "
				             ."     AND banrec  = 'on' "
				             ."   ORDER BY 1 DESC ";
			           }
	             }
	            else */
	             ///  {

		      if (!isset($wfpa[$j]))
		         {
		          $q="  SELECT bancod, bannom "
			        ."    FROM ".$wbasedato."_000069, ".$wbasedato."_000023 "
			        ."   WHERE fpacod = '".$wfpaini."'"
			        ."     AND fpacba = bancod ";
			     }
			    else
			      {
				   $wfpago=explode("-",$wfpa[$j]);

			       $q =  "  SELECT bancod, bannom "
			           ."    FROM ".$wbasedato."_000069, ".$wbasedato."_000023 "
			           ."   WHERE fpacod = '".$wfpago[0]."'"
			           ."     AND fpacba = bancod ";
		          }
		      $resban = mysql_query($q,$fconex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		      $numban = mysql_num_rows($resban) ;

		      for ($y=1;$y<=$numban;$y++)
			      {
				   $banc = mysql_fetch_array($resban);
	               echo "<option value='".$banc[0].'-'.$banc[1]."'>".$banc[0].'-'.$banc[1]."</option>";
			      }
			  echo "</select></td>";


		      //////////////////////////////////////////////////////////////



		       /*
			  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		      //BANCO EN EL QUE SE CONSIGNA O DESTINO
		      echo "<td bgcolor=FF6699 colspan=1><b><font text color=".$wclfa.">En que Banco se consigna:<br></font></b><select name='wbandes[".$j."]'>"; // onchange='enter()'>";
		      //if (!isset($wfpa[$j]))
		      if (!isset($wbandes[$j]))
		         {
			      $wfpago=explode("-",$wfpa[$j]);

			      //sleep(5);

			      $q= " SELECT bancod, bannom "
			         ."   FROM ".$wbasedato."_000069 "
			         ."  WHERE banest = 'on' "
			         ."    AND banrec = 'on' "
			         ."  ORDER BY 1 DESC ";
			      $resban = mysql_query($q,$fconex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			      $numban = mysql_num_rows($resban) ;
			      $banc = mysql_fetch_array($resban);

			      echo "<option selected>".$banc[0].'-'.$banc[1]."</option >";

			      for ($y=1;$y<=$numban;$y++)
				     {
					  $banc = mysql_fetch_array($resban);
		              echo "<option>".$banc[0].'-'.$banc[1]."</option>";
				     }
				  echo "</select></td>";
				 }
		        else
		           {
			        $wfpago=explode("-",$wfpa[$j]);

			        $q = "  SELECT bancod, bannom "
	                    ."    FROM ".$wbasedato."_000069, ".$wbasedato."_000023 "
	                    ."   WHERE banest  = 'on' "
	                    ."     AND bancag != 'on' "
	                    ."     AND banrec  = 'on' "
	                    ."     AND bancod  = fpacba "
			            ."     AND fpacod  = '".$wfpago[0]."'"
			            ."   UNION "
			            ."  SELECT bancod, bannom "
			            ."    FROM ".$wbasedato."_000069 "
			            ."   WHERE banest = 'on' "
			            ."     AND bancag = 'on' "
			            ."     AND banrec = 'on' "
			            ."   ORDER BY 1 DESC ";
			        $resu = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			        $num1 = mysql_num_rows($resu);
				    for ($y=1;$y<=$num1;$y++)
				       {
		 		        $banc = mysql_fetch_array($resu);

		 		        if (isset($wbandes[$j]) and $wbandes[$j]==$banc[0].'-'.$banc[1])    //Si ya fue digitado el banco
		                   echo "<option selected>".$banc[0].'-'.$banc[1]."</option >";
		                  else
		                     echo "<option>".$banc[0].'-'.$banc[1]."</option>";
				       }
				    echo "</select></td>";
				   }
				*/


			  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
			  //BASE DE DEVOLUCION
			  if ($wbas_dev=="on" and isset($wvalfpa[$j]))
			     echo "<td class='fila2' colspan=1 nowrap=nowrap><b>Base Dev. Iva: </b>".number_format(($wvalfpa[$j]*$wtotbase_dev_iva/$wtotventot),0,'.',',')."</td>";
			    else
			       echo "<td class='fila2' colspan=1 nowrap=nowrap><b>Base Dev. Iva: </b></td>";


			  ////if (isset($wbandes[$j]))
			  ////echo "<input type='hidden' NAME='wbandes[".$j."]' value='".$wbandes[$j]."'>";
			  echo "</tr>";
			}

	    echo "<input type='HIDDEN' name='whabilita_venta' value='".$whabilita_venta."'>";
	    echo "<input type='HIDDEN' name='wtotventot' id='wtotventot' value='".$wtotventot."'>";

	  }
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //FUNCION PARA MOSTRAR LOS ARTICULOS SELECCIONADOS PARA LA VENTA
  function mostrar($fwusuario,$fwfecha_tempo,$fwhora_tempo,$fwcco,$fwcaja,$fconex,$fwini,$fwdocpac,$fwnompac,$fwte1pac,$fwdirpac,$fwmaipac,$fwcol,$fwtipcli,$fwcuotamod,$fwempresa,$fwventa,$fwtipven,$fwmensajero,$fwdesemp,$fwrecemp,$fwdesart,$fwpdepac,$fwvdepac,$fwlinpac,$wemp_pmla,$wacc,$wlotven, $wcaningresada)
       {
	     global $wemp_pmla;

	     global $wtotventot;
	     global $wtotvenneg;
	     global $wtotvenpos;
	     global $wtotveniva;
	     global $wcf;
	     global $wcf2;
	     global $wclfa;
         global $wclfg;
         global $wtotdes;
         global $wtotrec;

         global $wbondto;
         global $wfecha;
         global $hora;
         global $wtipfac;
         global $wtipcli;
	     global $wlotven;
         global $wbasedato;
         global $wpagook;     //Indica que si puede pagar por Nomina 0:No 1:Si
         global $wchequeo;    //Indica si se verifica cupo en Nomina on:Si off:NO
         global $wmedico;
         global $wcodmed;
         global $wnommed;
         global $wprograma;
         global $wremitente;
         global $wcodrem;
         global $wnomrem;
         global $wcoddia;
         global $wnomdia;
         global $wrango;
         global $wtipdes;
         global $wnitemp;
         global $wpuntos;
         global $wval_pun;
         global $wcan_pun;
         global $wtipven;

         global $wprestamo;
         global $wemp;     //Codigo de la empresa responsable, cuando es empleado el carne

         global $wcarpun;     //Carne de puntos del cliente
         global $wpidesiempredinero;     //Carne de puntos del cliente

         //VARIABLES RIPS
         global $wrips;
         global $wtipusu;
         global $wtipdto;
         global $wmuni;
         global $wzona;
         global $wsexo;
         global $wpoliza;
         global $wauto;
         global $wfecven;
         global $wedad;

		 global $wcco;
		 global $wdocpac;

         global $whabilita_venta;

         global $wtotbase_dev_iva;

		 global $wacc;

         //global $wcuotamod;

         if ($wbondto == "NO APLICA - NO APLICA")
	        {
             $q = " UPDATE ".$wbasedato."_000034"
                 ."    SET temdbo = 0 "
                 ."  WHERE temusu = '".$fwusuario."'"
      	         ."    AND temfec = '".$fwfecha_tempo."'"
	             ."    AND temhor = '".$fwhora_tempo."'"
	             ."    AND temsuc = '".$fwcco."'"
	             ."    AND temcaj = '".$fwcaja."'";
             $res_lin = mysql_query($q,$fconex) or die (mysql_errno()." - ".mysql_error());
            }

         //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	     //ACA TRAIGO TODO LO QUE HAY PENDIENTE DE FACTURAR EN ESTE CAJA
	     $q = " SELECT temart, temdes, tempre, temcan, temvun, tempiv, temiva, temtot, ".$wbasedato."_000034.id, temdem, temrem, temdar, temdpa, tembpa, temdbo, temlpa, gruabo, temlot, temfve, temnde, temves, temade, temres, temusu, tempes "
	         ."   FROM ".$wbasedato."_000034, ".$wbasedato."_000001, ".$wbasedato."_000004 "
	         ."  WHERE temusu                            = '".$fwusuario."'"
	         ."    AND temfec                            = '".$fwfecha_tempo."'"
	         ."    AND temhor                            = '".$fwhora_tempo."'"
	         ."    AND temsuc                            = '".$fwcco."'"
	         ."    AND temcaj                            = '".$fwcaja."'"
	         ."    AND temart                            = artcod "
	         ."    AND mid(artgru,1,instr(artgru,'-')-1) = grucod "
	         ."  ORDER BY ".$wbasedato."_000034.id ";
	     $res = mysql_query($q,$fconex) or die (mysql_errno()." - ".mysql_error());
	     $num = mysql_num_rows($res);

	     if ($num > 0)
	        {
		     $wtotveniva=0;
	         $wtotventot=0;
	         $wtotvenneg=0;
	         $wtotvenpos=0;
	         $wtotdes=0;
	         $wtotrec=0;
	         $wtotbase_dev_iva=0;
	         $wtotartdessiniva=0;

	         echo "<tr><td align=center colspan=16 class='encabezadoTabla'><b>DETALLE DE VENTA</b></td></tr>";
		     echo "<tr>";
		     echo "<th class='encabezadoTabla'>Articulo</th>";
			 echo "<th class='encabezadoTabla' colspan=1>Descripción</th>";
			 echo "<th class='encabezadoTabla'>Presentación</th>";
			 echo "<th class='encabezadoTabla'>Cantidad</th>";
			 echo "<th class='encabezadoTabla'>V/r Unit.Con IVA</th>";
			 echo "<th class='encabezadoTabla'>V/r Unit.Sin IVA</th>";
			 echo "<th class='encabezadoTabla'>% Descuento</th>";
			 echo "<th class='encabezadoTabla'>Descuento<br>Total Art.</th>";
			 echo "<th class='encabezadoTabla'>Total Venta Con<br>Dscto Sin IVA</th>";
			 echo "<th class='encabezadoTabla'>% Iva</th>";
			 echo "<th class='encabezadoTabla'>Valor Iva.</th>";
			 echo "<th class='encabezadoTabla'>Total</th>";
			 if($wtipcli!=$wtipoEmpresaParticular && $wlotven=='on')
			 {
				 echo "<th class='encabezadoTabla'>Lote</th>";
				 echo "<th class='encabezadoTabla'>Fec. Vence</th>";
			 }
			 echo "<th class='encabezadoTabla'>Eliminar</th>";
			 echo "</tr>";

			 if (isset($wbondto) and $wbondto != "NO APLICA - NO APLICA")
			    {
				 $wbondto1=explode("-",$wbondto);

			     //ACA BUSCO SI EL BONO TIENE DESCUENTO
			     $q = " SELECT linea, sublinea, descuento, recargo "
			         ."   FROM ".$wbasedato."_000047 "
			         ."  WHERE mid(bono,1,instr(bono,'-')-1) = '".trim($wbondto1[0])."'"
			         ."    AND fecha_ini <= '".$wfecha."'"
			         ."    AND fecha_fin >= '".$wfecha."'"
			         ."    AND hora_ini  <= '".$hora."'"
			         ."    AND hora_fin  >= '".$hora."'";
			     $res_desc = mysql_query($q,$fconex);
			     $num_desc = mysql_num_rows($res_desc);

			     if ($num_desc > 0)
			        {
			         $row_desc = mysql_fetch_array($res_desc);
			         $wlin_bon=$row_desc[0];      //Linea
			         $wsub_bon=$row_desc[1];      //Sublinea
			         $wdes_bon=$row_desc[2];      //Descuento
			         $wrec_bon=$row_desc[3];      //Recargo
			        }
			       else
			          {
			           $wlin_bon="";     //Linea
			           $wsub_bon="";     //Sublinea
			           $wdes_bon=0;      //Descuento
			           $wrec_bon=0;      //Recargo
		              }
			     }


			 $wcuotamod=0;

			 //Con este for se recorren todos los articulos que hasta el momento se han registrado en la venta
			 for ($i=1;$i<=$num;$i++)
	            {
	             $row = mysql_fetch_array($res);

	             if ($i%2==0)
	                $wcolor="fila1";
	               else
	                  $wcolor="fila2";

				$wdatos_art = validar_dsct_esc_art($row['temart'], $row['temdcl']);
				$waprobar_desc = '';
				$waprobar_descuento = $row['temade'];

		        //Verifica si en la tabla temporal uno de los registros tiene aprobacion de descuentos adicionales.
				if($waprobar_descuento == 'on')
				{
				$waprobar_desc = "<p style='background-color:#FFFFCC;'><FONT FACE='arial' SIZE=2><a href='javascript:' onclick='mostrar_aprobacion(\"".$row['temart']."\");'>Aprobar descuento</a></FONT><p>
								<div id='mostrar_aprobacion_".$row['temart']."' style='display:none;'><p><b>Codigo de aprobación:</b><br><input type=text id='cod_aprobacion'></p><p align=center><input type=button value='OK' onclick='aprobar_descuentos(\"".$wemp_pmla."\", \"".$wbasedato."\", \"".$wdatos_art['nit_lab']."\", \"".$row['temusu']."\", \"".$row['temart']."\", \"".$row['temvun']."\", \"".$wcaningresada."\", \"".$wdocpac."\", \"".$row['temcan']."\" );'>";
				}

	             //============================================================================
	             //====== P R O G R A M A   P U N T O S =======================================
	             //ACA VOY A BUSCAR SI ESTE TIPO DE CLIENTE ACUMULA PUNTOS O NO
				 $q =  " SELECT count(*), cpuvun, cpupun "
				      ."   FROM ".$wbasedato."_000062 "
				      ."  WHERE cputem = '".$fwtipcli."'"
				      ."    AND cpufin <= '".$wfecha."'"
				      ."    AND cpuffi >= '".$wfecha."'"
				      ."  GROUP BY 2,3 ";
				 $res_pun = mysql_query($q,$fconex) or die (mysql_errno()." - ".mysql_error());
	             $row_pun = mysql_fetch_array($res_pun);

	             if ($row_pun[0] > 0)
	                {
	                 $wpuntos = "S";    //Indica que si acumula puntos
	                 $wval_pun=$row_pun[1];
	                 $wcan_pun=$row_pun[2];

	                 //*****************************************************************************************************************************************
	                 //Febrero 13 de 2009 - Si el tipo de venta es 'Internet' los puntos son dobles. Esto se coloco en funcionamiento desde el 16 de Feb de 2009
	                 if ($wtipven=="Internet")
	                    $wcan_pun=$wcan_pun*2;
	                 //*****************************************************************************************************************************************
                    }

                 //Esto lo hago porque si habia seleccionado un bono de descto y luego lo quito entonces actualizo la tabla temporal tambien.
                 //Esto podria ocurrir cuando ya esta digitando la forma de pago.

                 if (strpos($wbondto,"NO APLICA"))
	                {
                     $q = "UPDATE ".$wbasedato."_000034"
                         ."   SET temdbo = 0 "
                         ." WHERE id     = ".$row[8];
                     $res_lin = mysql_query($q,$fconex) or die (mysql_errno()." - ".mysql_error());
                    }


	             ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	             //========================================================================================================================\\
	             //SI HAY DESCUENTO POR BONO BUSCO SI HAY ALGUN ARTICULO DE LA VENTA QUE PERTENEZCA A LA LINEA QUE TIENE DESCUENTO         \\
	             //========================================================================================================================\\
	             if (isset($wdes_bon) and $wdes_bon > 0)
	                {
		             if ($wsub_bon != "NO APLICA")
		                $wlinea_bon = substr($wlin_bon,0,strpos($wlin_bon,"-"))."-".substr($wsub_bon,0,strpos($wsub_bon,"-"));
		               else
		                  $wlinea_bon = substr($wlin_bon,0,strpos($wlin_bon,"-"))."%";

		             $q = "SELECT descuento, recargo "
		                 ."  FROM ".$wbasedato."_000001, ".$wbasedato."_000047 "
		                 ." WHERE artcod                                          = '".$row[0]."'"                            //Articulo
		                 ."   AND ((mid(artgru,1,instr(artgru,'-')-1)               = mid(linea,1,instr(linea,'-')-1) "        //Linea
		                 ."   AND  mid(artgru,instr(artgru,'-')+1,length(artgru)) = mid(sublinea,1,instr(sublinea,'-')-1)) "  //Linea
		                 ."    OR (mid(artgru,1,instr(artgru,'-')-1)               = mid(linea,1,instr(linea,'-')-1) "        //Linea
		                 ."   AND  sublinea                                       = 'NO APLICA')) "
		                 ."   AND artest                                          = 'on' "
		                 ."   AND mid(bono,1,instr(bono,'-')-1)                   = '".trim($wbondto1[0])."'"
				         ."   AND fecha_ini                                      <= '".$wfecha."'"
				         ."   AND fecha_fin                                      >= '".$wfecha."'"
				         ."   AND hora_ini                                       <= '".$hora."'"
				         ."   AND hora_fin                                       >= '".$hora."'";
		             $res_lin = mysql_query($q,$fconex) or die (mysql_errno()." - ".mysql_error());
		             $num_lin = mysql_num_rows($res_lin);

		             $row_lin = mysql_fetch_array($res_lin);

		             if ($row_lin[0] == 0 or $num_lin==0)
		                $wdcto_bon=0;
		               else
		                  {
		                   $wdcto_bon=$row_lin[0];    //Descuento
		                   $wrec_bon =$row_lin[1];    //Recargo
	                      }
	                }
	               else
	                  $wdcto_bon=0;



                    //////////////////////////////////////////////////////////////////////////////////////////////////////
                    //ACA EVALUO SI LA CUENTA POSEE ALGUN DESCUENTO
                    //////////////////////////////////////////////////////////////////////////////////////////////////////

					 //Verifico si el registro de la tabla temporal tiene descuento escalonado.
                     if($row['temnde'] > 0)
                       {

						$wdesc_art = $row['tempes'];

                       }
                     else
                       {
                       if ($row[9] > 0)                      //Si tiene descuento empresa
                          {
                              $wdesc_art=$row[9];
                          }
                      else
                          {
                          if ($row[11] > 0)                 //Si tiene descuento por articulo
                              {
                                  $wdesc_art=$row[11];
                              }
                          else
                              {
                                  if ($row[12] > 0 or $fwlinpac != "" )     //Si tiene descuento por tipo de cliente por linea, El 'or' es porque si de pronto se digitaron antes los articulos
                                     {                                      //que los datos del cliente, para que recalcule el descuento. Porque no quedo el descto en la tabla temporal 000034
                                          $q = " SELECT clepde, clevde "
                                                  ."   FROM ".$wbasedato."_000001, ".$wbasedato."_000042, ".$wbasedato."_000041 "
                                                  ."  WHERE artcod                            = '".$row[0]."'"
                                                  ."    AND mid(artgru,1,instr(artgru,'-')-1) in (".$fwlinpac.") "
                                                  ."    AND clefid                            <= '".$wfecha."'"
                                                  ."    AND cleffd                            >= '".$wfecha."'"
                                                  ."    AND clidoc                             = '".$fwdocpac."'"
                                                  ."    AND clitip                             = clecla "
                                                  ."    AND cleest                             = 'on' ";
                                              $res_lpa = mysql_query($q,$fconex) or die (mysql_errno()." - ".mysql_error());
                                      $num_lpa = mysql_num_rows($res_lpa);
                                      $row_lpa = mysql_fetch_array($res_lpa);

                                      if($row_lpa[0] > 0)               //Si tiene porcentaje de descuento
                                          {
                                          $wdesc_art=$row_lpa[0]/100;
                                          }
                                        else
                                          {
                                           if ($row_lpa[1] > 0)          //Si tiene valor de descuento
                                              {
                                              $wvald_art=$row_lpa[1];
                                              }
                                             else
                                              {
                                              $wdesc_art=0;
                                              }
                                          }
                                    }
                                 else
                                    {
                                     if ($row[14] > 0)       //Si tiene descuento por bonos
                                      {
                                        $wdesc_art=$row[14];
                                      }
                                    else                  //Con lo siguiente averiguo si se selecciono el bono de descuento despues de los articulos.
                                      {
                                      if ($wdcto_bon > 0)               //Esto lo hago porque si el vendedor coloco el bono de descuento despues de digitar los articulos, esto hace que el descuento se le aplique a todos
                                         {                              //por este motivo debe de actualizar la tabla temporal colocandole al articulo el porcentaje de descuento que le corresponde.,
                                          $wdesc_art=$wdcto_bon/100;
                                          $q = "UPDATE ".$wbasedato."_000034"
                                              ."   SET temdbo = ".($wdcto_bon/100)
                                              ." WHERE id     = ".$row[8];
                                          $res_lin = mysql_query($q,$fconex) or die (mysql_errno()." - ".mysql_error());
                                          }
                                      else
                                          {
                                            $wdesc_art=0;
                                          }
                                      }
                                  }
                             }
                          }
                       }

			              //$row[3] = Cantidad
			              //$row[4] = Valor Unitario con IVA
			              //$row[5] = Porcentaje de IVA
			              //$row[6] = Valor IVA
			              //$row[7] = Total articulo

			                          //Octubre 24 de 2008
			     if ($row[4] == 1)    //Si la tarifa es de un peso entonces hago los calculos con decimales //Octubre 24 de 2008
			        {
				     $wvaluni=round((($row[4]-($row[6]/$row[3]))),2);                                                           //Valor Unitario sin IVA
				     $wdesart=round(($row[3]*round(($wvaluni*$wdesc_art),2)),2);                                                //Descuento total por Articulo
				     if ($wdesc_art > 0)
				        $wivaart=round(($row[3]*round((round(($wvaluni*(1-$wdesc_art)),2)*($row[5]/100)),2)),2);                //Valor IVA total por articulo
				       else
				          $wivaart=round((($row[4]*$row[3])-($wvaluni*$row[3])-(round((($row[4]*$wdesc_art)/(1+($row[5]/100))),2)*($row[5]/100))),2);       //Valor IVA total por articulo
				     $wtotart=((round((($row[3]*$wvaluni)),2)-$wdesart)+$wivaart);                                              //Valor Total articulo

				     $wtotartdessiniva=$wtotartdessiniva+(($row[3]*$wvaluni)-$wdesart);                                         //Suma Columna Valor Articulo articulo con descto SIN IVA
		            }
				   else
				      {
					   $wvaluni=round(($row[4]-($row[6]/$row[3])));                                                             //Valor Unitario sin IVA

					   $wdesart=round($row[3]*round($wvaluni*$wdesc_art));                                                      //Descuento total por Articulo
					   //echo "cantidad:".$row[3]."-valor unitario con iva:".$row[4]."-porcentajeiva:".$row[5]."-valor iva:"."-".$row[6]."-Valor Unitario sin IVA:".$wvaluni."-".$wdesc_art."dec total:".$wdesart."<br>";
					   if ($wdesc_art > 0)
					      $wivaart=round($row[3]*round(round($wvaluni*(1-$wdesc_art))*($row[5]/100)));                          //Valor IVA total por articulo
					     else
					        $wivaart=round(($row[4]*$row[3])-($wvaluni*$row[3])-(round(($row[4]*$wdesc_art)/(1+($row[5]/100)))*($row[5]/100)));       //Valor IVA total por articulo
					   $wtotart=((round(($row[3]*$wvaluni))-$wdesart)+$wivaart);                                                //Valor Total articulo

					   $wtotartdessiniva=$wtotartdessiniva+(($row[3]*$wvaluni)-$wdesart);                                       //Suma Columna Valor Articulo articulo con descto SIN IVA
				      }


			     echo "<tr>";
			     echo "<td align=center class=".$wcolor.">".$row[0]."</td>";                                                  //Articulo
			     echo "<td align=LEFT   class=".$wcolor.">".$row[1].$wnota_desc."</td>";                                                  //Descripcion
			     echo "<td align=center class=".$wcolor.">".$row[2]."</td>";                                                  //Unidad
	             echo "<td align=center class=".$wcolor.">".$row[3]."</td>";                                                  //Cantidad
	             if ($row[4]==1)  //Octubre 24 de 2008
	                {
		             echo "<td align=RIGHT class=".$wcolor.">".number_format($row[4],2,'.',',')."</td>";                          //Valor unitario CON IVA
					 echo "<td align=RIGHT class=".$wcolor.">".number_format($wvaluni,2,'.',',')."</td>";                         //Valor unitario SIN IVA
					 echo "<td align=RIGHT class=".$wcolor.">".number_format(($wdesc_art*100),2,'.',',')."</td>";          //% Descuento
					 echo "<td align=RIGHT class=".$wcolor.">".number_format($wdesart,2,'.',',')."</td>";                         //Descuento total articulo
					 echo "<td align=RIGHT class=".$wcolor.">".number_format((($row[3]*$wvaluni)-$wdesart),2,'.',',')."</td>";    //Total articulo CON DESCTO SIN IVA
					 echo "<td align=RIGHT class=".$wcolor.">".number_format($row[5],2,'.',',')."</td>";                          //Porcentaje de iva
					 echo "<td align=RIGHT class=".$wcolor.">".number_format($wivaart,2,'.',',')."</td>";                         //Valor iva total articulo
					 echo "<td align=RIGHT class=".$wcolor.">".number_format($wtotart,2,'.',',')."</td>";                         //Total articulo
	                }
	               else
	                  {
			           echo "<td align=RIGHT class=".$wcolor.">".number_format($row[4],0,'.',',')."</td>";                          //Valor unitario CON IVA
					   echo "<td align=RIGHT class=".$wcolor.">".number_format($wvaluni,0,'.',',')."</td>";                         //Valor unitario SIN IVA
					   echo "<td align=RIGHT class=".$wcolor.">".number_format(($wdesc_art*100),0,'.',',')."</td>";                 //% Descuento
					   echo "<td align=RIGHT class=".$wcolor.">".number_format($wdesart,0,'.',',')."".$waprobar_desc."</td>";       //Descuento total articulo
					   echo "<td align=RIGHT class=".$wcolor.">".number_format((($row[3]*$wvaluni)-$wdesart),0,'.',',')."</td>";    //Total articulo CON DESCTO SIN IVA
					   echo "<td align=RIGHT class=".$wcolor.">".number_format($row[5],0,'.',',')."</td>";                          //Porcentaje de iva
					   echo "<td align=RIGHT class=".$wcolor.">".number_format($wivaart,0,'.',',')."</td>";                         //Valor iva total articulo
					   echo "<td align=RIGHT class=".$wcolor.">".number_format($wtotart,0,'.',',')."</td>";                         //Total articulo
		              }

			     if($wtipcli!=$wtipoEmpresaParticular && isset($wlotven) && $wlotven=="on")
				 {
					 echo "<td align=center class=".$wcolor.">".$row[17]."</td>";
					 echo "<td align=center class=".$wcolor.">".$row[18]."</td>";
				 }

			     if (!isset($fwventa) or $fwventa == "N" )  //Solo da la opcion de eliminar mientras no se haya grabado la venta definitiva
					echo "<td align=center class='".$wcolor." posi_radiob-".$row[0]."'>&nbsp; <input type='radio' name='wid' id='wid_".$row[0]."' value='".$row[8]."' datoarticulo='".$row[0]."' onclick='submit_form2()'> &nbsp; </td>";
				 else
				    echo "<td align=center class=".$wcolor.">&nbsp;</td>";
			     echo "<tr>";

			     //$row[0]    = Codigo articulo
			     //$row[1]    = Descripcion articulo
			     //$row[2]    = Presentacion
			     //$row[3]    = Cantidad
			     //$row[4]    = Valor unitario
			     //$row[5]    = Porcentaje de IVA
			     //$row[6]    = Valor IVA
			     //$row[7]    = Valor total
			     //$row[8]    = Registro id
			     //$row[9]    = % Descuento empresa
			     //$row[10]   = % Recargo empresa
			     //$row[11]   = % Descuento articulo
			     //$row[12]   = % Descuento al usuario por tipo de cliente
			     //$row[13]   = Bono al usuario por tipo de cliente
			     //$wdcto_bon = Descuento por bonos traidos por el cliente (promocion)
			     //$wrec_bon  = Descuento por bonos traidos por el cliente (promocion)


			     $wtotdes=$wtotdes+$wdesart;
			     $wtotveniva=$wtotveniva+$wivaart;
			     $wtotventot=$wtotventot+$wtotart;
			     if ($wtotart < 0)
			        $wtotvenneg=$wtotvenneg+abs($wtotart);   //Total Venta Negativa, esto lo hago para cuando hay articulos con tarifas negativas
			       else
			          $wtotvenpos=$wtotvenpos+$wtotart;

			     if ($row[5] > 0)
			        $wtotbase_dev_iva=round(($wtotbase_dev_iva+(($row[3]*$wvaluni)-$wdesart)));
			    }
			  echo "<tr class='encabezadoTabla'>";
	          echo "<td align=RIGHT colspan=5><b>TOTALES &nbsp &nbsp</b></td>";
	          echo "<td>&nbsp</td>";
	          echo "<td>&nbsp</td>";
	          echo "<td align=RIGHT>".number_format($wtotdes,0,'.',',')."</td>";
	          echo "<td align=RIGHT>".number_format($wtotartdessiniva,0,'.',',')."</td>";
	          echo "<td>&nbsp</td>";
	          echo "<td align=RIGHT>".number_format($wtotveniva,0,'.',',')."</td>";
	          echo "<td align=RIGHT>".number_format($wtotventot,0,'.',',')."<input id='totalventa' type='hidden' value='".$wtotventot."' ></td>";
			  if($wtipcli!=$wtipoEmpresaParticular && $wlotven=='on')
			  {
				echo "<td>&nbsp</td>";
				echo "<td>&nbsp</td>";
	          }
			  echo "<td align=CENTER>Base Devolución: <br>".number_format($wtotbase_dev_iva,0,'.',',')."</td>";
	          echo "</tr>";

			  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	          //ACA VERIFICO SI EL TIPO DE CLIENTE DEBE SER CHEQUEADO
			  //POR AHORA SE VERIFICAN LOS QUE SEAN DE TIPO EMPLEADO
			  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			  $wcodigo=explode("-",$fwempresa);
			  $q =  " SELECT temche "
		           ."   FROM ".$wbasedato."_000029 "
		           ."  WHERE temcod = (mid('".$fwtipcli."',1,instr('".$fwtipcli."','-')-1)) "
			       ."  ORDER BY temcod ";

			  $res = mysql_query($q,$fconex); // or die (mysql_errno()." - ".mysql_error());;
		      $row = mysql_fetch_array($res);
		      $wchequeo=$row[0];

		      if ($row[0] == 'on')  //Si hay que verificar el cupo por Nomina
			     {
				  $q = " SELECT empres "
				      ."   FROM ".$wbasedato."_000024 "
				      ."  WHERE empcod = '".trim($wcodigo[0])."'"
				      ."    AND empnit = '".trim($wnitemp)."'"
				      ."    AND emptem = '".$wtipcli."'";

				  $res = mysql_query($q,$fconex); // or die (mysql_errno()." - ".mysql_error());;
		          $row = mysql_fetch_array($res);
		          $wresp=$row[0];

				  echo "<input type='HIDDEN' name='whabilita_venta' value='".$whabilita_venta."'>";
				  echo "<input type='HIDDEN' name='wemp' value='".trim($wresp)."'>";
				  echo "<input type='HIDDEN' name='wcodigo' value='".$wcodigo."'>";
				  echo "<input type='HIDDEN' name='wemp' value='".$wemp."'>";

		          if ($whabilita_venta == "ENABLED")
		             {
			          if (!isset($fwventa) or $fwventa == "N" )  //Solo da la opcion de Grabar Venta mientras no se haya grabado la venta definitiva
			             {
				          echo "<td align=center colspan=6 height=31><div class='fila1' style='width:270px;font-size:21px;valign:top;'><A href='prestamos.php?codigo=".trim($wcodigo[0])."&wemp=".trim($wresp)."&monto=".$wtotventot."&empresa=".$wbasedato."&wpagook=".$wpagook."&whabilita_venta=".$whabilita_venta."&wcarpun=".$wcarpun."' target='_blank'> Verificar Cupo</A></div></td>";
						  echo "<td align=center colspan=9 height=27 valign=middle> &nbsp; <input type='button' name='wventa_btn' id='wventa_btn' style='font-size:14px; font-weight:bold;' value='Grabar Venta' onclick='graba_venta()'> &nbsp; </td>";
						  echo "<input type='HIDDEN' name='wventa' id='wventa' value='N'>";      //Envio la venta como "N"
	                     }
                     }

                  //Si es un empleado y gana puntos tomo la cedula, el nombre y el codigo como los datos del cliente
                  if ($wpuntos == "S")
                     {
	                  $wte1pac=trim($wcodigo[0]);
                      $wdocpac=trim($wcodigo[1]);
                      $wnompac=trim($wcodigo[2]);

                      echo "<input type='HIDDEN' name='wdocpac' value='".$wdocpac."'>";
                      echo "<input type='HIDDEN' name='wnompac' value='".$wnompac."'>";
	                  echo "<input type='HIDDEN' name='wte1pac' value='".$wte1pac."'>";
	                  echo "<input type='HIDDEN' name='wemp' value='".$wemp."'>";
                     }
                 }
                else
                   if ($whabilita_venta == "ENABLED")
		              {
	                   if (!isset($fwventa) or $fwventa == "N" )  //Solo da la opcion de Grabar Venta mientras no se haya grabado la venta definitiva
					   {
						  echo "<td align=center colspan=15 height=27 valign=middle> &nbsp; <input type='button' name='wventa_btn' id='wventa_btn' style='font-size:14px; font-weight:bold;' value='Grabar Venta' onclick='graba_venta()'> &nbsp; </td>";
						  echo "<input type='HIDDEN' name='wventa' id='wventa' value='N'>";      //Envio la venta como "N"
					   }
                      }
	          echo "</tr>";

			  //echo "<input type='HIDDEN' name='wventa' value='N'>";      //Envio la venta como "N"

	          /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	        }

		  echo "<input type='hidden' name='wrips' value='".$wrips."'>";
		  echo "<input type='hidden' name='wdesemp' value='".$fwdesemp."'>";
		  echo "<input type='hidden' name='wrecemp' value='".$fwrecemp."'>";
		  if(isset($wcodigo))
			echo "<input type='hidden' name='wcodigo' value='".$wcodigo."'>";
		  echo "<input type='hidden' name='wtotventot' value='".$wtotventot."'>";
		  echo "<input type='hidden' name='wprestamo' value='".$wprestamo."'>";
		  ///////////////////////////////////////////////////////////////////////

	  }

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//FUNCION PARA CONSULTAR LA FACTURACIÓN EN MATRIX
	function facturacion_matrix($conex,$wbasedato_farm,$doc,$accidente)
	{
		global $tot_fac_matrix;
		$totalfac = 0;
		$totalnotas = 0;

		// Consulto código del tipo de empresa SOAT
		/*
		$q = " SELECT Temcod, Temdes "
		  ."     FROM ".$wbasedato_farm."_000029 "
		  ."  	WHERE Temvau = 'on' "
		  ."	  AND Temest = 'on' ";
		$res_soat = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		$row_soat = mysql_fetch_array($res_soat);
		$tipo_soat = $row_soat['Temcod']."-".$row_soat['Temdes'];

		// Consulto facturas generadas por Matrix
		$qlis = "  SELECT SUM(fenval+fenabo+fencmo+fencop+fendes) AS total "
			  ."     FROM ".$wbasedato_farm."_000018 "
			  ."  	WHERE Fendpa = '".$doc."' "
			  ."	  AND Fentip LIKE '".$tipo_soat."' "
			  ."	  AND Fennac = '".$accidente."' "
			  ."	  AND Fenest = 'on' "
			  ."	GROUP BY Fendpa ";
		$reslis = mysql_query($qlis,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qlis." - ".mysql_error());
		$rowlis = mysql_fetch_array($reslis);
		$totalfac = $rowlis['total'];

		// Consulto notas créditos generadas por Matrix
		$qlis2 =   "   SELECT SUM(Rdevca) AS total "
				  ."     FROM ".$wbasedato_farm."_000018, ".$wbasedato_farm."_000021, ".$wbasedato_farm."_000040 "
				  ."  	WHERE Fendpa = '".$doc."' "
				  ."	  AND Fentip LIKE '".$tipo_soat."' "
				  ."	  AND Fennac = '".$accidente."' "
				  ."	  AND Fenest = 'on' "
				  ."	  AND Fenfac = Rdefac "
				  ."	  AND Fenffa = Rdeffa "
				  ."	  AND Rdefue = Carfue "
				  ."	  AND Carncr = 'on' "
				  ."	  AND Carest = 'on' "
				  ."	GROUP BY Fenfac ";
		$reslis2 = mysql_query($qlis2,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qlis2." - ".mysql_error());
		$rowlis2 = mysql_fetch_array($reslis2);
		$totalnotas = $rowlis2['total'];

		$totalfac = $totalfac-$totalnotas;

		$tot_fac_matrix = $totalfac;
		*/

		$tot_fac_matrix = 0;
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//FUNCION PARA CONSULTAR LA FACTURACIÓN EN UNIX
	function facturacion_clinica($conex_o,$historia,$accidente)
	{
		global $tot_fac_unix;
		global $tot_fac_matrix;
		$total_lista = 0;

		// Consulto las facturas en clínica por historia
		$query = "SELECT carfec, cardoc, carcco, carval, carfue "
				."  FROM inacc, cacar, famovacc, sifue "
				." WHERE acchis = '$historia'"
				."   AND accacc = '$accidente'"
				."   AND acchis = movacchis"
				."   AND accacc = movaccacc"
			//	."   AND movaccfuo = '01'"
				."   AND movaccanu = '0'"
				."   AND carfue = movaccfue"
				."   AND cardoc = movaccdoc"
				."   AND caranu = '0'"
				."   AND movaccfue = fuecod"
				."   AND fueabr = 'FA'"
				." GROUP BY carfec, carfue, cardoc, carcco, carval";
		$err_o = odbc_exec($conex_o,$query);

		while(odbc_fetch_row($err_o))
		{
			// Consulto las notas crédito para la factura
			$queryn = "SELECT carfac, SUM(carval) "
					."  FROM cacar, catip "
					." WHERE carfca = '".odbc_result($err_o,5)."'"
					."   AND carfac = '".odbc_result($err_o,2)."'"
					."   AND caranu = '0'"
					."   AND carfue = tipfue"
					."   AND (tiptip = 'NC' OR tipfue = '28')"
					." GROUP BY carfac ";
			$err_n = odbc_exec($conex_o,$queryn);
			odbc_fetch_row($err_n);
			$notasc = odbc_result($err_n,2);
			$totalfacu = odbc_result($err_o,4);
			$totalfacu = $totalfacu-$notasc;

			$total_lista += $totalfacu;

		}

		// Consulto los cargos pendientes en clínica
		$query = "SELECT cardetfec, cardetdoc, cardetcco, SUM(cardettot-cardetvfa) "
				."  FROM inacc, facardet "
				." WHERE acchis = '$historia'"
				."   AND accacc = '$accidente'"
				."   AND acchis = cardethis"
				."   AND accnum = cardetnum"
				."   AND cardetfac = 'S' "
				."   AND cardetanu = '0'"
				."   AND cardettot-cardetvfa <> 0"
				."   AND cardettip = 'R' "
				." GROUP BY 1,2,3 ";
		$err_o = odbc_exec($conex_o,$query);

		//echo $query."<br>";

		while(odbc_fetch_row($err_o))
		{
			$total_lista += odbc_result($err_o,4);
		}

		// Consulto los cargos pendientes ayudas diagnósticas
		$query = "SELECT cardetfec, cardetdoc, cardetcco, SUM(cardettot-cardetvfa) "
				."  FROM inacc, aycardet "
				." WHERE acchis = '$historia'"
				."   AND accacc = '$accidente'"
				."   AND accfuo = cardetfue"
				."   AND accdoo = cardetdoc"
				."   AND cardetfac = 'S' "
				."   AND cardetanu = '0'"
				."   AND cardettot-cardetvfa <> 0"
				."   AND cardettip = 'R' "
				." GROUP BY 1,2,3 ";
		$err_o = odbc_exec($conex_o,$query);

		while(odbc_fetch_row($err_o))
		{
			$total_lista += odbc_result($err_o,4);
		}

		// Consulto novedades cargadas en clínica
		$query = "SELECT novaccfec, novaccfac, '', novaccval, novacchis, novaccacc "
				."  FROM inacc, fanovacc "
				." WHERE acchis = '$historia'"
				."   AND accacc = '$accidente'"
				."   AND acchis = novacchis"
				."   AND accacc = novaccacc"
				."   AND novaccval <> 0 "
				." GROUP BY  novaccfec, novacchis, novaccacc, novaccfac, novaccval";
		$err_o = odbc_exec($conex_o,$query);

		while(odbc_fetch_row($err_o))
		{
			$total_lista += odbc_result($err_o,4);
		}

		$tot_fac_unix = $total_lista;
		$total_lista = $tot_fac_unix+$tot_fac_matrix;

	}

	/*
	// se comenta porque para el calculo de facturación basta con la función facturacion_clinica
	function facturacion_clinica_general($conex_o,$historia,$accidente)
	{
		global $tot_fac_unix;
		$total = 0;
		$totalnotas = 0;
		$totalfac = 0;

		// Consulto las facturas en clínica por historia
		$query = "SELECT acchis, SUM(carval) "
				."  FROM inacc, cacar, famov, sifue "
				." WHERE acchis = '$historia'"
				."   AND accacc = '$accidente'"
				."   AND acchis = movhis"
				."   AND accnum = movnum"
				."   AND movfuo = '01'"
				."   AND movanu = '0'"
				."   AND carfue = movfue"
				."   AND cardoc = movdoc"
				."   AND caranu = '0'"
				."   AND carfue = fuecod"
				."   AND fueabr = 'FA'"
				." GROUP BY acchis ";
		$err_o = odbc_exec($conex_o,$query);
		odbc_fetch_row($err_o);
		if(odbc_result($err_o,2) && odbc_result($err_o,2)>0)
			$totalfac = odbc_result($err_o,2);

		// Consulto las notas crédito para la facturación
		$queryn = "SELECT acchis, SUM(carval) "
				."  FROM inacc, cacar, famov, catip "
				." WHERE acchis = '$historia'"
				."   AND accacc = '$accidente'"
				."   AND acchis = movhis"
				."   AND accnum = movnum"
				."   AND movanu = '0'"
				."   AND movfuo = '01'"
				."   AND carfue = movfue"
				."   AND cardoc = movdoc"
				."   AND caranu = '0'"
				."   AND movfue = tipfue"
				."   AND tiptip = 'NC'"
				." GROUP BY acchis ";
		$err_n = odbc_exec($conex_o,$queryn);
		odbc_fetch_row($err_n);
		if(odbc_result($err_n,2) && odbc_result($err_n,2)>0)
			$totalnotas = odbc_result($err_n,2);

		$total = $totalfac-$totalnotas;

		// Consulto facturación por ayuda diagnóstica
		$query = "SELECT acchis, SUM(carval) "
				."  FROM inacc, cacar, famov, sifue "
				." WHERE acchis = '$historia'"
				."   AND accacc = '$accidente'"
				."   AND accfuo != ''"
				."   AND accfuo = movfuo"
				."   AND accdoo = movhis"
				."   AND movanu = '0'"
				."   AND carfue = movfue"
				."   AND cardoc = movdoc"
				."   AND caranu = '0'"
				."   AND movfue = fuecod"
				."   AND fueabr = 'FA'"
				." GROUP BY acchis ";
		$err_ay = odbc_exec($conex_o,$query);
		odbc_fetch_row($err_ay);
		if(odbc_result($err_ay,2) && odbc_result($err_ay,2)>0)
			$total += odbc_result($err_ay,2);

		// Consulto los cargos pendientes en clínica
		$query = "SELECT acchis, SUM(cardettot-cardetvfa) "
				."  FROM inacc, facardet "
				." WHERE acchis = '$historia'"
				."   AND accacc = '$accidente'"
				."   AND acchis = cardethis"
				."   AND accnum = cardetnum"
				."   AND cardetfac = 'S' "
				."   AND cardetanu = '0'"
				."   AND cardettot-cardetvfa > 0"
				."   AND cardettip = 'R' "
				." GROUP BY acchis ";
		$err_cp = odbc_exec($conex_o,$query);
		odbc_fetch_row($err_cp);
		if(odbc_result($err_cp,2) && odbc_result($err_cp,2)>0)
			$total += odbc_result($err_cp,2);


		// Consulto los cargos pendientes ayudas diagnósticas
		$query = "SELECT acchis, SUM(cardettot-cardetvfa) "
				."  FROM inacc, aycardet "
				." WHERE acchis = '$historia'"
				."   AND accacc = '$accidente'"
				."   AND accfuo = cardetfue"
				."   AND accdoo = cardetdoc"
				."   AND cardetfac = 'S' "
				."   AND cardetanu = '0'"
				."   AND cardettot-cardetvfa > 0"
				."   AND cardettip = 'R' "
				." GROUP BY acchis ";
		$err_cpay = odbc_exec($conex_o,$query);
		odbc_fetch_row($err_cpay);
		if(odbc_result($err_cpay,2) && odbc_result($err_cpay,2)>0)
			$total += odbc_result($err_cpay,2);


		// Consulto novedades cargadas en clínica
		$query = "SELECT novaccfec, novaccfac, '', novaccval, novacchis, novaccacc, SUM(novaccval) "
				."  FROM inacc, fanovacc "
				." WHERE acchis = '$historia'"
				."   AND accacc = '$accidente'"
				."   AND acchis = novacchis"
				."   AND accacc = novaccacc"
				."   AND novaccval <> 0 "
				." GROUP BY  novaccfec, novacchis, novaccacc, novaccfac, novaccval";
		$err_nov = odbc_exec($conex_o,$query);
		odbc_fetch_row($err_nov);
		if(odbc_result($err_nov,2) && odbc_result($err_nov,2)>0)
			$total += odbc_result($err_nov,2);

		$tot_fac_unix = $total;

	}
	*/


  //===========================================================================================================================================
  //INICIO DEL PROGRAMA
  //===========================================================================================================================================

$wcf="fila1";   //COLOR DEL FONDO    -- Gris claro
$wcf2="fila2";  //COLOR DEL FONDO 2  -- Azul claro
$wclfa="003366"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
$wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro

$increment = 1;
/// Espacios para que no se limite el tamaño de los campos en el update que se hace a la temporal
$long='                                                                                              ';

if((!isset($consultaAjax) || $consultaAjax!='envio') && (!isset($consultaAjax2) || $consultaAjax2!='envio'))
{
	//Mensaje de espera
	echo "<div id='msjEspere' name='msjEspere' style='display:none;'>";
	echo "<img src='../../images/medical/ajax-loader5.gif'/><br /><br /> Por favor espere un momento ... <br /><br />";
	echo "</div>";

  //echo "<p align=right><font size=1><b>Autor: ".$wautor."</b></font></p>";
  //=======================================================================================================================================
  //ACA COMIENZA EL ENCABEZADO DE LA VENTA
  echo "<table border='0' width='1000' align='center'><tr><td>";
  encabezado("VENTAS AL PUBLICO",$wactualiz, "logo_".$wbasedato);
  echo "</td></tr></table>";
  /*
  echo "<center><table border=1>";
  echo "<tr><td align=center rowspan=2 colspan=2><img src='/matrix/images/medical/ips/logo_".$wbasedato.".png' WIDTH=314 HEIGHT=123></td></tr>";
  echo "<tr><td align=center colspan=6 class=".$wcf2."><font size=6 text color=#FFFFFF><b>VENTAS AL PUBLICO</b></font></td></tr>";
  echo "<tr>";
  */

  // Aca se pinta el encabezado del formulario de venta
  echo "<div name='ventas_content' id='ventas_content'>";
  echo "</div>";

  // Aca se pinta la seccion de abajo del formulario de venta
  // donde se agregan los productos y m{etodo de pago
  echo "<div name='articulos_content' id='articulos_content'>";
  echo "</div>";

}
if(isset($consultaAjax) && $consultaAjax=='envio')
{

if ($wbasedato =='uvglobal')
{
  echo "<table align='center'><tr><td><input type='button' value='Facturar' onclick='ir_a_facturar()'></td><td><input type='button' value='Abonar' onclick='ir_a_abonar(\"".$wbasedato."\")'></td><td><input type='button' value='Orden de laboratorio' onclick='crear_orden()'></td></tr></table>";
}
  echo "<center><table border=0 width='700' id='tabla_grabar_ventas'>";
  // <tr id='tr_ppal_oculta' ><td colspan='1' class='fila1'><b>Nro venta 1:&nbsp;<span id='nroventa'></span></b></td><td colspan='4'></td></tr>";
  echo "<tr>";

	  ////////////////////////////////////////////////////////////////////
	  // DATOS DE FACTURACIÓN FURIPS 									//
	  // Comienzo a consultar los datos del paciente para llenar de 	//
	  // forma automática los campos del formulario						//
	  ////////////////////////////////////////////////////////////////////

	  if(isset($consulta_unix) && $consulta_unix=="1")
	  {
		  $wbasedato_mov = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
		  conexionOdbc($conex, $wbasedato_mov, $conexUnix, 'facturacion');
			// SE CONSULTA SI SE INGRESO DOCUMENTO Y SI EL PACIENTE TIENE ACCIDENTES REGISTRADOS SEGÚN UNIX, SE TRAEN LOS DATOS DEL PACIENTE
			// PARA CARGARLOS EN EL FORMULARIO
			if (isset($wdocpac) && $wdocpac != "9999" and $wdocpac != "")
			{
				 if($conexUnix)
				 {
					// Obtengo la historia clínica con base al documento de identificación
					// También obtengo el último número de ingreso y la última fecha de ingreso
					//2012-10-03 se adiciona las columnas pactid y pacpol
					  $select = " pachis, pacnum, '".$long."' as pacnac, '".$long."' as pacsex ,'".$long."' as pactid,'".$long."' as pacpol,ctedetdes";
					  $from =	" inpac,sictedet ";
					  $where =  " pacced='$wdocpac' AND pactid=ctedetpar and ctedetcod='TIPIDE' ";
					  $order =  " ORDER BY 2 DESC ";

					  unset($llaves);
					  $llaves[0]=1;
					  $llaves[1]=2;

					  $err_o = ejecutar_consulta_furips ($select, $from, $where, $llaves, $conexUnix, $order );
					//$err_o = odbc_do($conexUnix,$query);

					if (odbc_fetch_row($err_o))
					{
						$tbpac = "inpac";
						$pachis = trim(odbc_result($err_o,1));
						$pacnum = trim(odbc_result($err_o,2));
						$edad = trim(odbc_result($err_o,3));

						$wpoliza = trim(odbc_result($err_o,6));
						$wtipdto = trim(odbc_result($err_o,5)."-".odbc_result($err_o,7));

						if(!empty($edad) && $edad!="")
							$wedad = calcula_edad($edad);
						$sexo = trim(odbc_result($err_o,4));
						if(!empty($sexo) && $sexo!="" && $sexo!=" ")
							$wsexo = traer_sexo($sexo);
					}
					else
					{
						//2012-10-03 se adiciona las columnas pactid y egrpol
						  $select = " pachis, pacnum, '".$long."' as pacnac, '".$long."' as pacsex,'".$long."' as pactid,'".$long."' as egrpol,ctedetdes ";
						  $from =	" inpaci,inmegr,sictedet ";
						  $where =  " pacced='$wdocpac' and pachis=egrhis and pacnum=egrnum and pactid=ctedetpar and ctedetcod='TIPIDE'";
						  $order =  " ORDER BY 2 DESC ";

						  unset($llaves);
						  $llaves[0]=1;
						  $llaves[1]=2;

						  $err_1 = ejecutar_consulta_furips ($select, $from, $where, $llaves, $conexUnix, $order );
						//$err_1 = odbc_do($conexUnix,$query);
						if (odbc_fetch_row($err_1))
						{
							$tbpac = "inpaci";
							$pachis = trim(odbc_result($err_1,1));
							$pacnum = trim(odbc_result($err_1,2));
							$edad = trim(odbc_result($err_1,3));

							$wpoliza = trim(odbc_result($err_1,6));
						    $wtipdto = trim(odbc_result($err_1,5)."-".odbc_result($err_1,7));

							if(!empty($edad) && $edad!="")
								$wedad = calcula_edad($edad);
							$sexo = trim(odbc_result($err_1,4));
							if(!empty($sexo) && $sexo!="" && $sexo!=" ")
								$wsexo = traer_sexo($sexo);
						}
						else
						{
							$tbpac = "";
							$pachis = "";
							$pacnum = "";
							if (!isset($wsexo) or trim($wsexo) == "")
								$wsexo = "";
							if (!isset($wedad) or trim($wedad) == "" or (is_numeric($wedad) == false) or ($wedad <= 0))
								$wedad = "";
							$pacfec = date("Y-m-d", strtotime ("next Day"));
						}
					}

					if (isset($wrips) and $wrips == "on")
					{
					  $select = " '".$long."' as accdetpol, '".$long."' as accdettip, accdetmun, '".$long."' as accdetffi, '".$long."' as accdetzon, '".$long."' as accdetnom, '".$long."' as accdettel, '".$long."' as accdetdir, accdetacc, accdethis ";
					  $from =	" inaccdet ";
					  $where =  " accdethis = '$pachis'"; // asi estaba " accdetced='$wdocpac' "; Gustavo cambia 2012/08/28
					  $order =  " ORDER BY 9 DESC ";

					  unset($llaves);
					  $llaves[0]=10;
					  $llaves[1]=9;

					  $err_acc = ejecutar_consulta_furips ($select, $from, $where, $llaves, $conexUnix, $order );
					  //$err_acc = odbc_exec($conexUnix,$query);

					  if(odbc_fetch_row($err_acc))
					  {

						  //$wpoliza = trim(odbc_result($err_acc,1));
						  //$wtipdto = trim(odbc_result($err_acc,2));
						  //$wmuni = trim(odbc_result($err_acc,3));
						  $wfecven = trim(odbc_result($err_acc,4));
						  $zona = trim(odbc_result($err_acc,5));
						  $wzona = traer_zona($zona);

						  $wnompac=trim(odbc_result($err_acc,6));
						  $wte1pac=trim(odbc_result($err_acc,7));
						  $wdirpac=trim(odbc_result($err_acc,8));

						  //=====================================================
						  // Modifico los campos consultados en linea
						  //=====================================================
						  echo "<script language='Javascript'>";
						  echo "document.forms.ventas.wpoliza.value='".$wpoliza."';";
						  echo "document.forms.ventas.wtipdto.value='".$wtipdto."';";
						  echo "document.forms.ventas.wmuni.value='".$wmuni."';";
						  if(!empty($wfecven) && $wfecven!="" && $wfecven!=" ")
							echo "document.forms.ventas.wfecven.value='".$wfecven."';";
						  echo "document.forms.ventas.wzona.value='".$wzona."';";
						  echo "document.forms.ventas.wnompac.value='".$wnompac."';";
						  echo "document.forms.ventas.wte1pac.value='".$wte1pac."';";
						  echo "document.forms.ventas.wdirpac.value='--".$wdirpac."';";
						  if(!empty($wedad) && $wedad!="" && $wedad!=" ")
							echo "document.ventas.wedad.value='".$wedad."';";
						  if(!empty($wsexo) && $wsexo!="" && $wsexo!=" ")
							echo "document.ventas.wsexo.value='".$wsexo."';";
						  echo "</script>";
						  //=====================================================
					  }
					}

					echo "<input type='HIDDEN' name='wpachis' value='".$pachis."'>";
				 }
			}
	  }
	  ////////////////////////////////////////////////////////////////////

  if (isset($wdocpac)) //Si ya fue digitado el documento del cliente
     {
      if ($wdocpac != "9999" and $wdocpac != "")
         {
	      $q= "SELECT clidoc, clinom, clite1, clidir, climai, clitip, clipun "
	         ."  FROM ".$wbasedato."_000041 "
	         ." WHERE clidoc = '".$wdocpac."'";
	      $res1 = mysql_query($q,$conex);
	      $num1 = mysql_num_rows($res1);
	      if ($num1 > 0)
	         {
		      $row1 = mysql_fetch_array($res1);

		      $wnompac=$row1[1];
		      $wte1pac=$row1[2];
		      $wdirpac=$row1[3];
		      $wmaipac=$row1[4];
		      $wcarpun=$row1[6];

		    //=====================================================
                    //Modifico los campos consultados en linea
                    //=====================================================
                    echo "<script language='Javascript'>";
                    echo "document.ventas.wnompac.value='".$wnompac."';";
                    echo "document.ventas.wte1pac.value='".$wte1pac."';";
                    echo "document.ventas.wdirpac.value='".$wdirpac."';";
                    echo "document.ventas.wmaipac.value='".$wmaipac."';";
                    echo "document.ventas.wcarpun.value='".$wcarpun."';";
                    echo "</script>";
			  //=====================================================
                    echo "<input type='HIDDEN' name='wcarpun' value='".$wcarpun."'>";

	          $wclitip=$row1[5];
	         }

                 $wlab_asociados = 'pordefecto'; // Se declara esta variable con datos para que al momento de consultar medicamentos no vuelva a imprimir los
                                                 //laboratorios en la respuesta ajax.
	  	  }
	  }

  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //CARNE O TARJETA DE PUNTOS
  //*************************************************************************************************************************************************************************************************************************************************************************************
  if (isset($wcarpun)) //Si ya fue digitado el documento del cliente
     {
	  $whabilita_puntos="ENABLED";
      if ($wcarpun != "000000" and $wcarpun != "")
         {
	      $whabilita_puntos="DISABLED";
	      echo "<td align=left class=".$wcf." colspan=1><b> Tarjeta Puntos: </b><br><INPUT TYPE='text' NAME='wcarpun' VALUE='".$wcarpun."' onkeypress='if (event.keyCode >= 35 & event.keyCode <= 38) event.returnValue = false' ".$whabilita_puntos."></td>";   //wcarpun
	      echo "<td align=left class=".$wcf." colspan=1 ><b>Nro venta:</b><br><span id='nroventa'></span></td>";
		  if ($whabilita_puntos == "DISABLED")
	         echo "<input type='HIDDEN' name='wcarpun' value='".$wcarpun."'>";
         }
        else
           if ($whabilita_venta=="ENABLED")
		   {
              echo "<td align=left class=".$wcf." colspan=1><b> Tarjeta Puntos: </b><br><INPUT TYPE='text' NAME='wcarpun' VALUE='".$wcarpun."' onkeypress='if (event.keyCode >= 35 & event.keyCode <= 38) event.returnValue = false' DISABLED></td>";       //wcarpun
			  echo "<td align=left class=".$wcf." colspan=1><b>Nro venta:</b><br><span id='nroventa'></span></td>";
		   }
		   else
		   {
                echo "<td align=left class=".$wcf." colspan=1><b> Tarjeta Puntos: </b><br><INPUT TYPE='text' NAME='wcarpun' VALUE='".$wcarpun."' onkeypress='if (event.keyCode >= 35 & event.keyCode <= 38) event.returnValue = false' ENABLED></td>";       //wcarpun
				echo "<td align=left class=".$wcf." colspan=1><b>Nro venta:</b><br><span id='nroventa'></span></td>";
		   }

      //SIN IMPORTAR SI EL CLIENTE ES DIFERENTE A 9999 O IGUAL BUSCO EN EL TIPO DE CLIENTE SI HAY DESCUENTO
      //ACA CONSULTO SI EL TIPO DE CLIENTE ESPECIAL TIENE DESCUENTO O BONO DE DESCUENTO PARA APLICARLO LUEGO EN LA VENTA
	  $q= "SELECT clepde, clevde, clelin "
	   	 ."  FROM ".$wbasedato."_000041, ".$wbasedato."_000042 "         //Tabla tipos de clientes
	     ." WHERE clipun  = '".$wcarpun."'"
	     ."   AND clitip  = clecla "
	     ."   AND clefid <= '".$wfecha."'"
	     ."   AND cleffd >= '".$wfecha."'"
	     ."   AND cleest  = 'on' "
	     ."   AND clelin  != 'NO APLICA' "
	     ."   AND clelin  != '' ";
	  $res1 = mysql_query($q,$conex);
	  $num1 = mysql_num_rows($res1);

	   if ($num1 > 0)
	    {
	     $row1 = mysql_fetch_array($res1);
	     $wpdepac=($row1[0]/100);
	     $wvdepac=$row1[1];
	     $wlinpac=$row[2];
	    }
	   else
	      {
	       $wpdepac=0;
           $wvdepac=0;
           $wlinpac="";
          }
     }
    else
       {
	    echo "<td align=left class=".$wcf." colspan=1><b> Tarjeta Puntos: </b><br><INPUT TYPE='text' NAME='wcarpun' VALUE='000000' onkeypress='if (event.keyCode >= 35 & event.keyCode <= 38)  event.returnValue = false'></td>";              //wcarpun
        echo "<td align=left class=".$wcf." colspan=1><b>Nro venta:</b><br><span id='nroventa'></span></td>";
		$wpdepac=0;
        $wvdepac=0;
        $wlinpac="";
       }
  //*************************************************************************************************************************************************************************************************************************************************************************************

    // Defino si la empresa actual requiere facturar con número de lote y fecha de vencimiento
	$q =   "SELECT Detval "
		."    FROM root_000051 "
		."   WHERE Detemp = '".$wemp_pmla."' "
		."     AND Detapl = 'incluye_lote_y_vencimiento' ";
	$res_lot_ven = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	$row_lot_ven = mysql_fetch_array($res_lot_ven);
	if($row_lot_ven && $row_lot_ven['Detval']=='on')
	{
		$wlotven = 'on';
		echo "<input type='hidden' name='wlotven' id='wlotven' value='".$wlotven."'>";
	}
	else
	{
		$wlotven = 'off';
		echo "<input type='hidden' name='wlotven' id='wlotven' value='".$wlotven."'>";
	}
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //FECHA DE LA VENTA
  echo "<td align=left class=".$wcf."><b>Fecha: </b><br>".$wfecha."</td>";
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //SUCURSAL
  echo "<td align=left class=".$wcf." colspan=1><b><font text color=".$wclfg.">Sucursal: </font></b><br><span id='nomcco'>".$wnomcco."</td>";
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //CAJA
  echo "<td align=left class=".$wcf." colspan=1><b><font text color=".$wclfg.">Caja: </font></b><br>".$wnomcaj."</td>";

  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////   function ira(){document.ventas.wvalfpa[0].focus();}
  //TIPO DE RESPONSABLE
  echo "<td align=left class=".$wcf." ><b><font text color=".$wclfg.">Tipo de Responsable: </font></b><br><select id='wtipcli' name='wtipcli' onchange='submit_form(\"on\")'>";

  if (isset($wtipcli))
     {
	  $q =  " SELECT temcod, temdes "
           ."   FROM ".$wbasedato."_000029 "
           ."  WHERE temcod not in (mid('".$wtipcli."',1,instr('".$wtipcli."','-')-1)) "
	       ."  ORDER BY temcod ";
	 }
    else
       {
        $q =  " SELECT temcod, temdes "
             ."   FROM ".$wbasedato."_000029 "
	         ."  ORDER BY temcod ";
	   }
  $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
  $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;
  if (isset($wtipcli))
     echo "<option value='".$wtipcli."' selected>".$wtipcli."</option>";
 $wrips=$row1[4];    //Si genera RIPS
 for ($i=1;$i<=$num;$i++)
     {
      $row = mysql_fetch_array($res);
      echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
     }
  echo "</select></td>";

  ////////////////////////////////////////////////////////////////////////////////////////////////////////////   function ira(){document.ventas.wvalfpa[0].focus();}
  //TIPO DE VENTA
  echo "</tr>";

  if (isset($wtipven) and ($wtipven <> "Directa"))
     {
      ////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  //MENSAJERO
	  if (isset($wmensajero))
	     {
		  $q =  " SELECT msjcod, msjnom "
		       ."   FROM ".$wbasedato."_000035 "
		       ."  WHERE msjcod <> '".$wmensajero."'"
		       ."    AND msjest = 'on'"
		       ."  ORDER BY msjcod ";
		 }
	    else
	       {
		    $q =  " SELECT msjcod, msjnom "
		         ."   FROM ".$wbasedato."_000035 "
		         ."  WHERE msjest = 'on'"
		         ."  ORDER BY msjcod ";
	       }

	  $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
	  $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;
	  echo "<td align=left class=".$wcf." colspan=1><b><font text color=".$wclfg.">Mensajero: <br></font></b><select name='wmensajero'>";

	  if (isset($wmensajero))
	     {
		  $q= "   SELECT count(*) FROM ".$wbasedato."_000035 "
	         ."    WHERE msjcod = (mid('".$wmensajero."',1,instr('".$wmensajero."','-')-1)) "
	         ."      AND msjest = 'on'";

	      $res1 = mysql_query($q,$conex);
	      $num1 = mysql_num_rows($res1);
	      $row1 = mysql_fetch_array($res1);
	      if ($row1[0] > 0)
		     echo "<option value='".$wmensajero."' selected>".$wmensajero."</option>";
	     }
	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res);
	      echo "<option value='".$row[0]." - ".$row[1]."'>".$row[0]." - ".$row[1]."</option>";
	     }
	  echo "</select></td>";
     }
    else
       $wmensajero="";

  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //RESPONSABLES
  if (isset($wtipcli))
     {
	  if ($wingpos=='on') //===>Farmastore es tipo pos = 'on'
	     {
	      $q =  " SELECT empcod, empnit, empnom "
		       ."   FROM ".$wbasedato."_000024 "
		       ."  WHERE emptem = '".$wtipcli."'"
		       ."    AND empest = 'on' "
		       ."  ORDER BY 1 ";
	     }
	    else
	      {
	       $q =  " SELECT '', '', '' "
		        ."   FROM ".$wbasedato."_000024 "
		        ."  GROUP BY 1,2,3 "
		        ."  UNION ALL "
		        ." SELECT empcod, empnit, empnom "
		        ."   FROM ".$wbasedato."_000024 "
		        ."  WHERE emptem = '".$wtipcli."'"
		        ."    AND empest = 'on' "
		        ."  ORDER BY 3 ";
	      }
     }
    else
       {
	    $q =  " SELECT '', '', '' "
	         ."   FROM ".$wbasedato."_000024 "
	         ."  GROUP BY 1,2,3 "
	         ."  UNION ALL "
	         ." SELECT empcod, empnit, empnom "
	         ."   FROM ".$wbasedato."_000024 "
	         ."  WHERE empcod != ' ' "
	         ."    AND empest = 'on' "
	         ."  ORDER BY 3 ";
       }
  $res = mysql_query($q,$conex)  or die (mysql_errno()." - ".mysql_error());
  $num = mysql_num_rows($res)   or die (mysql_errno()." - ".mysql_error());
  echo "<td align=left class=".$wcf." colspan=2><b><font text color=".$wclfg.">Responsable: </font></b><br><select id='wempresa' name='wempresa' onchange='submit_form(\"on\",\"wdocpac\")'>";

  if($wemp_pmla=='09')
  {
		if (isset($wempresa) && $wempresa!='' )
		{

			$tipoempresa1 = explode("-",$wtipcli);
			$temcod1 = $tipoempresa1['0'];
			$temdes1 = $tipoempresa1['1'];

			if($temcod1=='12')
			{




				// Consulto los datos del accidente
					if(isset($pachis) && $pachis!="" && $pachis!="0")
					{

						if(isset($wacc) && $wacc!="" && $wacc!="0")
						{
							  // Consulto datos de accidentes para la historia clínica del paciente
							  $select = " accacc, accfec, accnum, '".$long."' as accdetffi, accdetacc, accdethis ";
							  $from =	" inacc, inaccdet ";
							  $where =  " acchis='$pachis'
										   AND accacc='$wacc'
										   AND accind='P'
										   AND accdethis = acchis
										   AND accdetnum = accnum ";
							  $order =  " ORDER BY 1 DESC ";

							  unset($llaves);
							  $llaves[0]=6;
							  $llaves[1]=5;
						}
						else
						{
							  // Consulto datos de accidentes para la historia clínica del paciente
							  $select = " accacc, accfec, accnum, '".$long."' as accdetffi, accdetacc, accdethis ";
							  $from =	" inacc, inaccdet ";
							  $where =  " acchis='$pachis'
										   AND accind='P'
										   AND accdethis = acchis
										   AND accdetnum = accnum ";
							  $order =  " ORDER BY 1 DESC ";

							  unset($llaves);
							  $llaves[0]=6;
							  $llaves[1]=5;
						}

						$err_o = ejecutar_consulta_furips ($select, $from, $where, $llaves, $conexUnix, $order );
						//$err_o = odbc_exec($conexUnix,$query);
						//echo $query."<br>";


						// Muestro los datos del accidente al que se le va a cargar la factura
						if(odbc_fetch_row($err_o))
						{ // Si hay accidente seleccionado muestre los datos de éste
							$accacc=trim(odbc_result($err_o,1));

							$wacc=$accacc;


						}
						else
						{



							// Consulto datos de accidentes para la historia clínica del paciente
							$select = " accacc, accfec, accnum, '".$long."' as accdetffi, accdetacc, accdethis ";
							$from =	" inacc, inaccdet ";
							$where =  " acchis='$pachis'
									   AND accind='P'
									   AND accdethis = acchis
									   AND accdetnum = accnum ";
							$order =  " ORDER BY 1 DESC ";

							unset($llaves);
							$llaves[0]=6;
							$llaves[1]=5;

							$err_o = ejecutar_consulta_furips ($select, $from, $where, $llaves, $conexUnix, $order );
							//$err_o = odbc_exec($conexUnix,$query);



							if(odbc_fetch_row($err_o))
							{
								$accacc=trim(odbc_result($err_o,1));
								$wacc=$accacc;


							}
						}


					}




				if(isset($pachis) and $pachis !="" and isset($wacc) and $wacc !="")
				{
					$select = " salacctop, empcod as empnit ";
					$from =	" fasalacc, inemp ";
					$where =  " salacccer = empcod
								AND salacchis='$pachis'
								AND salaccacc = '".$wacc."'
								AND salaccnre = '1'";

					unset($llaves);
					$llaves[0]=1;
					$llaves[1]=2;



					$err_o = ejecutar_consulta_furips ($select, $from, $where, $llaves, $conexUnix );


					if (odbc_fetch_row($err_o))
					{


						$nit_resposableunix = trim(odbc_result($err_o,2));
						$nit_resposableunixaux = explode("-",$nit_resposableunix);
						$nit_resposableunix =  $nit_resposableunixaux[0];

						$entre ='si';

						 $q = "   SELECT COUNT(*), empmed, emppro, emprem, emprip, empdgn, empran, emptde, empnom ,empcod"
							."     FROM ".$wbasedato."_000024 ";


						$q.="    WHERE empnit = '".$nit_resposableunix."' ";




						  $q.="      AND emptem = '".$wtipcli."'";
						 // $q.="      AND emptar LIKE 'SOAT-SOAT'";
						  $q.="      AND empest = 'on' ";
						  $q.="    GROUP BY 2,3,4,5 ";

						  $ultima = $q;
						  $res1 = mysql_query($q,$conex);
						  $num1 = mysql_num_rows($res1);
						  $row1 = mysql_fetch_array($res1);

						  if ($row1[0] > 0)
								{
									  echo "<option codigo='".$row1['empcod']."' nombre='".$row1['empnom']."'  value='".$row1['empcod']."-".$nit_resposableunix."-".stripAccents($row1['empnom'])."' selected>".$row1['empcod']."-".$nit_resposableunix."-".stripAccents($row1['empnom'])."</option>";
									  $wmedi=$row1[1];    //Si pide medico
									  $wprog=$row1[2];    //Si pide programa de afiliacion
									  $wremi=$row1[3];    //Si pide remitente
									  $wrips=$row1[4];    //Si genera RIPS
									  $wdiag=$row1[5];    //Si pide Diagnostico
									  $wran =$row1[6];    //Si pide rango del usuario
									  $wtde =$row1[7];    //Tipo de Despacho (E)nfermedad general, (A)ccidente de Trabajo. Esto solo para Colsubsidio
								}
								else
								{
								 for ($i=1;$i<=$num;$i++)
								 {
									$row = mysql_fetch_array($res);
									echo "<option  codigo='".$row[0]."' nombre='".$row[1]."'  value='".$row[0]."-".$row[1]."-".stripAccents($row[2])."'>".$row[0]."-".$row[1]."-".stripAccents($row[2])."</option>";
								 }

								}



					}
					else
					{
						 $q = "   SELECT COUNT(*), empmed, emppro, emprem, emprip, empdgn, empran, emptde, empnom ,empcod"
							."     FROM ".$wbasedato."_000024 ";



						  $q.="    WHERE empcod = (mid('".$wempresa."',1,instr('".$wempresa."','-')-1)) ";



						  $q.="      AND emptem = '".$wtipcli."'";
						  $q.="      AND empest = 'on' ";
						  $q.="    GROUP BY 2,3,4,5 ";
						  $res1 = mysql_query($q,$conex);
						  $num1 = mysql_num_rows($res1);
						  $row1 = mysql_fetch_array($res1);

								if ($row1[0] > 0)
								{
									  echo "<option codigo='".$row1['empcod']."' nombre='".$row1['empnom']."'  value='".stripAccents($wempresa)."' selected>".stripAccents($wempresa)."--".$entre."--".$nit_resposableunix."no entre</option>";
									  $wmedi=$row1[1];    //Si pide medico
									  $wprog=$row1[2];    //Si pide programa de afiliacion
									  $wremi=$row1[3];    //Si pide remitente
									  $wrips=$row1[4];    //Si genera RIPS
									  $wdiag=$row1[5];    //Si pide Diagnostico
									  $wran =$row1[6];    //Si pide rango del usuario
									  $wtde =$row1[7];    //Tipo de Despacho (E)nfermedad general, (A)ccidente de Trabajo. Esto solo para Colsubsidio
								}
								else
								{
								 for ($i=1;$i<=$num;$i++)
								 {
									$row = mysql_fetch_array($res);
									echo "<option  codigo='".$row[0]."' nombre='".$row[1]."'  value='".$row[0]."-".$row[1]."-".stripAccents($row[2])."'>".$row[0]."-".$row[1]."-".stripAccents($row[2])." no entre dos</option>";
								 }

								}

					}

				}
				else
				{
					$q = "   SELECT COUNT(*), empmed, emppro, emprem, emprip, empdgn, empran, emptde, empnom ,empcod"
							."     FROM ".$wbasedato."_000024 ";



						  $q.="    WHERE empcod = (mid('".$wempresa."',1,instr('".$wempresa."','-')-1)) ";



						  $q.="      AND emptem = '".$wtipcli."'";
						  $q.="      AND empest = 'on' ";
						  $q.="    GROUP BY 2,3,4,5 ";
						  $res1 = mysql_query($q,$conex);
						  $num1 = mysql_num_rows($res1);
						  $row1 = mysql_fetch_array($res1);

								if ($row1[0] > 0)
								{
									  echo "<option codigo='".$row1['empcod']."' nombre='".$row1['empnom']."'  value='".stripAccents($wempresa)."' selected>".stripAccents($wempresa)."</option>";
									  $wmedi=$row1[1];    //Si pide medico
									  $wprog=$row1[2];    //Si pide programa de afiliacion
									  $wremi=$row1[3];    //Si pide remitente
									  $wrips=$row1[4];    //Si genera RIPS
									  $wdiag=$row1[5];    //Si pide Diagnostico
									  $wran =$row1[6];    //Si pide rango del usuario
									  $wtde =$row1[7];    //Tipo de Despacho (E)nfermedad general, (A)ccidente de Trabajo. Esto solo para Colsubsidio
								}
								else
								{
								 for ($i=1;$i<=$num;$i++)
								 {
									$row = mysql_fetch_array($res);
									echo "<option  codigo='".$row[0]."' nombre='".$row[1]."'  value='".$row[0]."-".$row[1]."-".stripAccents($row[2])."'>".$row[0]."-".$row[1]."-".stripAccents($row[2])."</option>";
								 }

								}
				}


			}


			  //Este query lo hago para saber si la empresa que esta en pantalla corresponde al tipo de cliente o empresa seleccionado en el campo anterior
			  //Si si corresponde la muestro, si no, solo muestra las seleccionadas en el query anterior






			//echo "<option value=''>".$q."</option>";
		}
		else
		{
			if ($wingpos!='on')    //Si el ingreso de los datos NO es tipo POS (FarmaStore), no muestro la empresa en blanco.
			echo "<option value=' - '>&nbsp-&nbsp</option>";

		     for ($i=1;$i<=$num;$i++)
			 {
			  $row = mysql_fetch_array($res);
			  echo "<option  codigo='".$row[0]."' nombre='".$row[1]."'  value='".$row[0]."-".$row[1]."-".stripAccents($row[2])."'>1".$row[0]."-".$row[1]."-".stripAccents($row[2])."</option>";
			 }

		}







  }
  else
  {
		if (isset($wempresa))
		 {
		  //Este query lo hago para saber si la empresa que esta en pantalla corresponde al tipo de cliente o empresa seleccionado en el campo anterior
		  //Si si corresponde la muestro, si no, solo muestra las seleccionadas en el query anterior
		  $q= "   SELECT COUNT(*), empmed, emppro, emprem, emprip, empdgn, empran, emptde, empnom ,empcod"
			 ."     FROM ".$wbasedato."_000024 "
			 ."    WHERE empcod = (mid('".$wempresa."',1,instr('".$wempresa."','-')-1)) "
			 ."      AND emptem = '".$wtipcli."'"
			 ."      AND empest = 'on' "
			 ."    GROUP BY 2,3,4,5 ";
		  $res1 = mysql_query($q,$conex);
		  $num1 = mysql_num_rows($res1);
		  $row1 = mysql_fetch_array($res1);
		  if ($row1[0] > 0)
			 {
			  echo "<option codigo='".$row1['empcod']."' nombre='".$row1['empnom']."'  value='".stripAccents($wempresa)."' selected>".stripAccents($wempresa)."</option>";
			  $wmedi=$row1[1];    //Si pide medico
			  $wprog=$row1[2];    //Si pide programa de afiliacion
			  $wremi=$row1[3];    //Si pide remitente
			  $wrips=$row1[4];    //Si genera RIPS
			  $wdiag=$row1[5];    //Si pide Diagnostico
			  $wran =$row1[6];    //Si pide rango del usuario
			  $wtde =$row1[7];    //Tipo de Despacho (E)nfermedad general, (A)ccidente de Trabajo. Esto solo para Colsubsidio
			 }
		 }
		 else
		 {
			if ($wingpos!='on')    //Si el ingreso de los datos NO es tipo POS (FarmaStore), no muestro la empresa en blanco.
			echo "<option value=' - '>&nbsp-&nbsp</option>";
		 }

		 for ($i=1;$i<=$num;$i++)
		 {
		  $row = mysql_fetch_array($res);
		  echo "<option  codigo='".$row[0]."' nombre='".$row[1]."'  value='".$row[0]."-".$row[1]."-".stripAccents($row[2])."'>1".$row[0]."-".$row[1]."-".stripAccents($row[2])."</option>";
		 }


  }




  echo "</select></td>";
//echo "<pre>"; var_dump( $_POST ); echo "</pre>";

  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //TELEFONO DEL CLIENTE
  if (isset($wte1pac)) //Si ya fue digitado el telefono del cliente
     {
      echo "<td align=left class=".$wcf."><b><font text color=".$wclfg."> Telefono: </font></b><br><INPUT TYPE='text' NAME='wte1pac' SIZE=9 VALUE='".$wte1pac."' onkeypress='if (validar(event)) submit_form(\"on\")'></td>";            //wte1pac
     }
    else
       if ($wingpos=='on')  //Si el tipo de Ingreso es tipo POS (Farmastore) coloco el valor del telefono en 'SIN DATO', si no es tipo POS lo pongo en blanco
          echo "<td align=left class=".$wcf."><b><font text color=".$wclfg."> Telefono: </font></b><br><INPUT TYPE='text' NAME='wte1pac' SIZE=9 VALUE='SIN DATO' onkeypress='if (validar(event)) submit_form(\"on\")'></td>";                    //wdirpac
         else
            echo "<td align=left class=".$wcf."><b><font text color=".$wclfg."> Telefono : <br></font></b><INPUT TYPE='text' NAME='wte1pac' SIZE=9 onkeypress='if (validar(event)) submit_form(\"on\")'></td>";                    //wdirpac

  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //NOMBRE DEL CLIENTE
  $wcolspan=1;
  if (isset($wnompac)) //Si ya fue digitado el nombre del cliente
     echo "<td align=left class=".$wcf." colspan=".$wcolspan."><b><font text color=".$wclfg."> Nombre: </font></b><br><INPUT TYPE='text' NAME='wnompac' id='wnompac' SIZE=30 VALUE='".$wnompac."' onkeypress='if (validar(event)) submit_form(\"on\")'></td>";          //wnompac
    else
       if ($wingpos=='on')  //Si el tipo de Ingreso es tipo POS (Farmastore) coloco el valor del telefono en 'SIN DATO', si no es tipo POS lo pongo en blanco
          echo "<td align=left class=".$wcf." colspan=".$wcolspan."><b><font text color=".$wclfg."> Nombre: </font></b><br><INPUT TYPE='text' NAME='wnompac' id='wnompac' SIZE=30 VALUE='CLIENTE PARTICULAR' onkeypress='if (validar(event)) submit_form(\"on\")'></td>";  //wnompac
         else
            echo "<td align=left class=".$wcf." colspan=".$wcolspan."><b><font text color=".$wclfg."> Nombre: </font></b><br><INPUT TYPE='text' NAME='wnompac' id='wnompac' SIZE=30 onkeypress='if (validar(event)) submit_form(\"on\")'></td>";  //wnompac

  ////////////////////////////////////////////////////////////////////////////////////////////////////////////   function ira(){document.ventas.wvalfpa[0].focus();}
  //TIPO DE FACTURA
  echo "<td align=left class=".$wcf."><b><font text color=".$wclfg.">Tipo de Factura: </font></b><br><select name='wtipfac' onchange='enter()'>";

  if (isset($wtipfac))
     if ($wtipfac == "Automatica")
        {
         echo "<option value='".$wtipfac."' selected>".$wtipfac."</option>";
         echo "<option value='Manual'>Manual</option>";
        }
       else
          {
	       $q = " SELECT ccopfm, ccoffm, ccofmi "
		        ."  FROM ".$wbasedato."_000003 "
	 		    ." WHERE ccocod='".$wcco."'";

		   $err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	       $row = mysql_fetch_array($err);

	       $mfueffa   =$row[1];
	       $mnrofac   =$row[0]."-".($row[2]+1);

           echo "<option value='".$wtipfac."' selected>".$wtipfac."</option>";
           echo "<option value='Automatica'>Automatica</option>";
          }
    else
       {
        echo "<option value='Automatica'>Automatica</option>";
        echo "<option value='Manual'>Manual</option>";
       }
  if (isset($wtipfac) and $wtipfac=="Manual")
     echo "</select><br><b>*** Proxima Factura: ".$mnrofac."</b></td>";
    else
       echo "</select></td>";

	echo "<td align=left class=".$wcf."><b><font text color=".$wclfg.">Tipo de Venta: </font></b><br><select name='wtipven' onchange='enter()'>";

	if (isset($wtipven))
	{
	 if ($wtipven == "Directa")
		{
		 echo "<option value='".$wtipven."' selected>".$wtipven."</option>";
		 echo "<option value='Domicilio'>Domicilio</option>";
		 echo "<option value='Internet'>Internet</option>";
		}
	 if ($wtipven == "Domicilio")
		{
		 echo "<option value='".$wtipven."' selected>".$wtipven."</option>";
		 echo "<option value='Directa'>Directa</option>";
		 echo "<option value='Internet'>Internet</option>";
		}
	 if ($wtipven == "Internet")
		{
		 echo "<option value='".$wtipven."' selected>".$wtipven."</option>";
		 echo "<option value='Directa'>Directa</option>";
		 echo "<option value='Domicilio'>Domicilio</option>";
		}
	}
	else
	  {
	   echo "<option value='Directa'>Directa</option>";
	   echo "<option value='Domicilio'>Domicilio</option>";
	   echo "<option value='Internet'>Internet</option>";
	  }
	echo "</select></td>";
    echo "</tr>";

	$tipoempresa = explode("-",$wtipcli);
	$temcod = $tipoempresa['0'];
	$temdes = $tipoempresa['1'];
    // CONSULTA DE TIPO DE EMPRESA
	$qtem = " SELECT Temcod, Temdes, Temvau "
	  ."     FROM ".$wbasedato."_000029 "
	  ."  	WHERE Temcod = '".$temcod."' ";
	$res_tem = mysql_query($qtem,$conex) or die (mysql_errno()." - ".mysql_error());
	$row_tem = mysql_fetch_array($res_tem);

  //////////////////////////////////////////////////////////////////
  //DOCUMENTO DEL CLIENTE
  echo "<tr>";
  if (isset($wdocpac)) //Si ya fue digitado el documento del cliente
     {
      if ($wdocpac != "9999" and $wdocpac != "")
         {
             echo "<td align=left class=".$wcf." colspan=2><b><font text color=".$wclfg."> Documento: </font></b><br><INPUT TYPE='text' NAME='wdocpac' id='wdocpac' VALUE='".$wdocpac."' onblur='submit_form(\"on\",\"wnommed\")' onkeypress='if (validar(event)) submit_form(\"on\",\"wdato\")'></td>";   //wdocpac
             // --> Si es el cuadro de mando de consulta.
             if (isset($wlab_asociados))
                {
                    //Se consultan los laboratorios asociados al paciente y se imprimen en pantalla.
                    $wlaboratorios_asociados = laboratorios_usuario($wdocpac);
                    if($wlaboratorios_asociados['cuantos'] > 0)
                        {

                        echo '<div id="laboratorios_flotante" align="right" style="cursor:pointer;border:2px solid #E2B709;background-color: #FFFFFF;display:;width:auto;position: fixed;">
                                <img onClick="cerrar_flotante();" style="width:11px;height:11px;" src="../../images/medical/eliminar1.png" title="Cerrar Opciones">
                                <p align=left><b>LABORATORIOS ASOCIADOS:</b></p>'.$wlaboratorios_asociados['laboratorios'].'

							  </div>';

                        }
                }
         }
        else
           echo "<td align=left class=".$wcf." colspan=2><b><font text color=".$wclfg."> Documento: </font></b><br><INPUT TYPE='text' NAME='wdocpac' id='wdocpac' VALUE='".$wdocpac."' onblur='submit_form(\"on\",\"wnommed\")' onkeypress='if (validar(event)) submit_form(\"on\",\"wdato\")'></td>";   //wdocpac

      //SIN IMPORTAR SI EL CLIENTE ES DIFERENTE A 9999 O IGUAL BUSCO EN EL TIPO DE CLIENTE SI HAY DESCUENTO
      //ACA CONSULTO SI EL TIPO DE CLIENTE ESPECIAL TIENE DESCUENTO O BONO DE DESCUENTO PARA APLICARLO LUEGO EN LA VENTA
	  $q= "SELECT clepde, clevde, clelin "
	   	 ."  FROM ".$wbasedato."_000041, ".$wbasedato."_000042 "         //Tabla tipos de clientes
	     ." WHERE clidoc  = '".$wdocpac."'"
	     ."   AND clitip  = clecla "
	     ."   AND clefid <= '".$wfecha."'"
	     ."   AND cleffd >= '".$wfecha."'"
	     ."   AND cleest  = 'on' "
	     ."   AND clelin  != 'NO APLICA' "
	     ."   AND clelin  != '' ";
	  $res1 = mysql_query($q,$conex);
	  $num1 = mysql_num_rows($res1);

	  if ($num1 > 0)
	    {
		 $row1 = mysql_fetch_array($res1);
	     $wpdepac=($row1[0]/100);
	     $wvdepac=$row1[1];
	     $wlinpac=$row1[2];
	    }
	   else
	      {
	       $wpdepac=0;
           $wvdepac=0;
           $wlinpac="";
          }
     }
    else
       {
	    if ($wingpos=='on')  //Si el tipo de Ingreso es tipo POS (Farmastore) coloco el valor del telefono en 'SIN DATO', si no es tipo POS lo pongo en blanco
           echo "<td align=left class=".$wcf." colspan=2><b><font text color=".$wclfg."> Documento: </font></b><br><INPUT TYPE='text' NAME='wdocpac' id='wdocpac' VALUE='9999' onblur='submit_form(\"on\",\"wnommed\")' onkeypress='if (validar(event)) submit_form(\"on\",\"wdato\")'></td>";              //wdocpac
          else
             echo "<td align=left class=".$wcf." colspan=2><b><font text color=".$wclfg."> Documento: </font></b><br><INPUT TYPE='text' NAME='wdocpac' id='wdocpac'  onblur='submit_form(\"on\",\"wnommed\")' onkeypress='if (validar(event)) submit_form(\"on\",\"wdato\")'></td>";              //wdocpac
        $wpdepac=0;
        $wvdepac=0;
        $wlinpac="";
       }

  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //DIRECCION DEL CLIENTE
  if (isset($wdirpac)) //Si ya fue digitada la direccióndel cliente
     echo "<td align=left class=".$wcf." colspan=1><b><font text color=".$wclfg."> Dirección:</font></b><br><INPUT TYPE='text' NAME='wdirpac' id='wdirpac' VALUE='".$wdirpac."'  onkeypress='if (validar(event)) submit_form(\"on\")'></td>";       //wdirpac
    else
       echo "<td align=left class=".$wcf." colspan=1><b><font text color=".$wclfg."> Dirección: </font></b><br><INPUT TYPE='text' NAME='wdirpac' id='wdirpac' VALUE='SIN DATO' onkeypress='if (validar(event)) submit_form(\"on\")'></td>";         //wdirpac

  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //E-MAIL DEL CLIENTE
  if (isset($wmaipac)) //Si ya fue digitado el mail del cliente
     echo "<td class=".$wcf." colspan=1><b><font text color=".$wclfg."> E-Mail: </font></b><br><INPUT TYPE='text' NAME='wmaipac' SIZE=40 VALUE='".$wmaipac."' onkeypress='if (validar(event)) submit_form(\"on\")'></td>";  //wmaipac
    else
       echo "<td class=".$wcf." colspan=1><b><font text color=".$wclfg."> E-Mail: </font></b><br><INPUT TYPE='text' NAME='wmaipac' SIZE=40 VALUE='SIN DATO' onkeypress='if (validar(event)) submit_form(\"on\")'></td>";    //wmaipac

  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //CUOTA MODERADORA
  if (isset($wcuotamod)) //Si ya fue digitada la cuota del cliente
     echo "<td align=left class=".$wcf." colspan=1><b><font text color=".$wclfg."> Copago o Cuota Moderadora: </font></b><br><INPUT TYPE='text' NAME='wcuotamod' VALUE='".$wcuotamod."' onkeypress='if (validar(event)) submit_form(\"on\")'></td>";          //wnompac
    else
       echo "<td align=left class=".$wcf." colspan=1><b><font text color=".$wclfg."> Copago o Cuota Moderadora: </font></b><br><INPUT TYPE='text' NAME='wcuotamod' VALUE='0' onkeypress='if (validar(event)) submit_form(\"on\")'></td>";            //wnompac

  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //BONOS DE DESCUENTO
  if (!isset($wbondto))
	  $q =  " SELECT boncod, bondes "
	       ."   FROM ".$wbasedato."_000048 "
	       ."  ORDER BY boncod ";
	 else
	    {
		 $wbondto1=explode("-",$wbondto);
	     $q =  " SELECT boncod, bondes "
	          ."   FROM ".$wbasedato."_000048 "
	          ."  WHERE boncod != ('".trim($wbondto1[0])."')"
	          ."  ORDER BY boncod ";
        }

  $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
  $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());

  echo "<td id='bonos_dsct' align=left class=".$wcf." colspan=2><b><font text color=".$wclfg."> Bonos de Dcto: <br></font></b><select name='wbondto' onchange='enter()'>";
  if (isset($wbondto))
     echo "<option value='".$wbondto."' selected>".$wbondto."</option>";
  for ($i=1;$i<=$num;$i++)
     {
	  $row = mysql_fetch_array($res);
	  echo "<option value='".$row[0]." - ".$row[1]."'>".$row[0]." - ".$row[1]."</option>";
     }
  echo "</select></td>";
  echo "</tr>";

  //ACA DEFINO LAS COLUMNAS A MOSTRAR DEPENDIENDO DE LOS DATOS ADICIONALES QUE SE PIDAN EN PANTALLA
  if (isset($wmedi) or isset($wprog) or isset($wremi) or isset($wdiag) or isset($wran) or isset($wtde))
     {
      echo "<tr>";
      $wcf="fila1";
      if ($wmedi=="on" and $wprog=="on" and $wremi=="on")
         {
	      $wcolmed1=1;
	      $wcolmed2=1;
	      $wcolprog=1;
	      $wcolrem1=1;
	      $wcolrem2=2;
         }

      if ($wmedi=="on" and $wprog=="on" and $wremi=="off")
         {
	      $wcolmed1=1;
	      $wcolmed2=2;
	      $wcolprog=3;
	     }

      if ($wmedi=="on" and $wprog=="off" and $wremi=="off")
         {
	      $wcolmed1=1;
	      $wcolmed2=5;
	      if (isset($wdiag) and $wdiag=="on")
	         $wcolmed2=1;
	     }
	  if ($wmedi=="off" and $wprog=="on" and $wremi=="on")
         {
	      $wcolprog=3;
	      $wcolrem1=1;
	      $wcolrem2=2;
	     }
	  if ($wmedi=="off" and $wprog=="off" and $wremi=="on")
	     {
          $wcolrem1=1;
          $wcolrem2=5;
         }
      if ($wmedi=="on" and $wprog=="off" and $wremi=="on")
	     {
		  $wcolmed1=1;
	      $wcolmed2=1;
          $wcolrem1=1;
          $wcolrem2=3;
         }

      if ($wmedi=="off" and $wprog=="on" and $wremi=="off")
         $wcolprog=6;

      if ($wdiag=="on" and $wran=="on" and $wtde=="on")
	     {
		  $wcoldia1=1;
	      $wcoldia2=1;
          $wcolran=1;
          $wcoltde=1;
         }
	 }


  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //MEDICO QUE FORMULA
  if (isset($wmedi) and $wmedi=="on")
     {
	  if ($wcolmed2 > 0)
         $wancho=40;
        else
           $wancho=20;

	  if (isset($wcodmed) and ($wcodmed != ""))
	     {
		  $q =  " SELECT medcod, mednom "
		       ."   FROM ".$wbasedato."_000051 "
		       ."  WHERE medest = 'on' "
		       ."    AND medcod = '".$wcodmed."'"
		       ."  ORDER BY mednom ";

	      $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());
	      $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());
	      if ($num > 0)
	         {
		      $row = mysql_fetch_array($res);
		      $wcodmed = $row[0];
		      $wnommed = $row[1];
		      $wmedico = $row[0]." - ".$row[1];
			  echo "<input type='hidden' id='wcodmed' name='wcodmed' value='".$wcodmed."'>";
			  echo "<input type='hidden' id='wnommed' name='wnommed' value='".$wnommed."'>";
			  echo "<input type='hidden' id='wmedico' name='wmedico' value='".$wmedico."'>";

		      /////
		      echo "<script language='Javascript'>";
              echo "document.ventas.wnommed.value='".$wnommed."';";
              echo "</script>";
		      /////

		      echo "<td align=left class=".$wcf." colspan=".$wcolmed1."><b> Código Médico: </b><br><INPUT TYPE='text' id='wcodmed' NAME='wcodmed' VALUE='".$wcodmed."' onblur='submit_form(\"on\",\"wnommed\")' onkeypress='if (validar(event)) submit_form(\"on\",\"wdato\")'></td>";             //wcodmed
		      echo "<td align=left class=".$wcf." colspan=".$wcolmed2."><b> Médico: </b><br><INPUT TYPE='text' id='wnommed' NAME='wnommed' VALUE='".$wnommed."' size = '".$wancho."' onkeypress='if (validar(event)) submit_form(\"on\",\"wdato\")'></td>"; // onchange='enter()'></td>";   //wnommed
	         }
	        else
	           {
		        echo "<td align=left class=".$wcf." colspan=".$wcolmed1."><b> Código Médico: </b><br><INPUT TYPE='text' id='wcodmed' NAME='wcodmed' onblur='submit_form(\"on\",\"wnommed\")' onkeypress='if (validar(event)) submit_form(\"on\",\"wdato\")'></td>";                                //wcodmed
			    echo "<td align=left class=".$wcf." colspan=".$wcolmed2."><b> Médico: </b><br><INPUT TYPE='text' id='wnommed' NAME='wnommed' size='".$wancho."' onkeypress='if (validar(event)) submit_form(\"on\",\"wdato\")'></td>"; // onchange='enter()'></td>";                      //wnommed
	           }
         }
        else
           {
	        if (isset($wnommed) and ($wnommed != ""))
			   {
				$wnommed=str_replace(" ","%",$wnommed);
			    $q =  " SELECT medcod, mednom "
			         ."   FROM ".$wbasedato."_000051 "
			         ."  WHERE medest = 'on' "
			         ."    AND mednom like '%".$wnommed."%'"
			         ."  ORDER BY mednom ";

			    $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
			    $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;

			    if ($num > 0)
			       {

				    echo "<td align=left class=".$wcf." colspan=".$wcolmed1."><b> Código Médico: </b><br><INPUT TYPE='text' id='wcodmed' NAME='wcodmed' onblur='submit_form(\"on\",\"wnommed\")' onkeypress='if (validar(event)) submit_form(\"on\",\"wdato\")'></td>";    //wcodmed
				    for ($i=1;$i<=$num;$i++)
				       {
					    $row = mysql_fetch_array($res);
				        if ($num == 1)
				           {
				            $wcodmed=$row[0];
				            //echo "<input type='HIDDEN' name='wcodmed' value='".$wcodmed."'>";

							//$wcodmed = $row[0];
							$wnommed = $row[1];
							$wmedico = $row[0]." - ".$row[1];
							echo "<input type='hidden' name='wmedico' value='".$wcodmed."'>";
							echo "<input type='hidden' name='wmedico' value='".$wnommed."'>";
							echo "<input type='hidden' name='wmedico' value='".$wmedico."'>";

				            /////
				            echo "<script language='Javascript'>";
				            echo "document.ventas.wcodmed.value='".$wcodmed."';";
				            echo "</script>";
						    /////
			               }
			              else
			                 {
				              if ($num > 1)           //Si entra por aca es porque el medico tiene varios registros con el nombre muy similar
				                 $wcodmed=$row[0];
			                 }
				        $wnommed1[$i]=$row[1];
				       }
				    echo "<td align=left class=".$wcf." colspan=".$wcolmed2."><b> Médico: </b><br><select name='wnommed' onchange='submit_form(\"on\",\"wdato\")'>";
				    for ($i=1;$i<=$num;$i++)
				       {
				        echo "<option value='".$wnommed1[$i]."'>".$wnommed1[$i]."</option>";
				        if ($num == 1)
				           $wnommed=$wnommed1[$i];
				       }
				    echo "</select></td>";
			       }
	              else
	                 {
			 	      echo "<td align=left class=".$wcf." colspan=".$wcolmed1."><b> Código Médico: </b><br><INPUT TYPE='text' id='wcodmed' NAME='wcodmed' onblur='submit_form(\"on\",\"wnommed\")' onkeypress='if (validar(event)) submit_form(\"on\",\"wdato\")'></td>";    //wcodmed
					  echo "<td align=left class=".$wcf." colspan=".$wcolmed2."><b> Médico: </b><br><INPUT TYPE='text' id='wnommed' NAME='wnommed' size='".$wancho."' onkeypress='if (validar(event)) submit_form(\"on\",\"wdato\")'></td>"; // onchange='enter()'></td>";          //wnommed
				     }
		         }
		        else
		           {
			 	    echo "<td align=left class=".$wcf." colspan=".$wcolmed1."><b> Código Médico: </b><br><INPUT TYPE='text' id='wcodmed' NAME='wcodmed' onblur='submit_form(\"on\",\"wnommed\")' onkeypress='if (validar(event)) submit_form(\"on\",\"wdato\")'></td>";    //wcodmed
					echo "<td align=left class=".$wcf." colspan=".$wcolmed2."><b> Médico: </b><br><INPUT TYPE='text' id='wnommed' NAME='wnommed' size='".$wancho."' onkeypress='if (validar(event)) submit_form(\"on\",\"wdato\")'></td>"; // onchange='enter()'></td>";          //wnommed
				   }
           }
     }


  ////////////////////////////////////////////////////////////////////////////////////////////////////////////   function ira(){document.ventas.wvalfpa[0].focus();}
  //PROGRAMA AL QUE ESTA AFILIADO O INSCRITO EL USUARIO
  if (isset($wprog) and $wprog=="on")
     {
	  if (isset($wprograma))
	     $q =  " SELECT procod, pronom "
		      ."   FROM ".$wbasedato."_000052 "
		      ."  WHERE proest = 'on' "
		      ."    AND procod != '".$wprograma."'"
		      ."  ORDER BY procod ";
	    else
	       $q =  " SELECT procod, pronom "
			     ."   FROM ".$wbasedato."_000052 "
			     ."  WHERE proest = 'on' "
			     ."  ORDER BY procod ";


	  $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
	  $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;

	  echo "<td align=left class=".$wcf." colspan=".$wcolprog."><b><font text color=".$wclfg."> Programa: </font></b><select name='wprograma' onchange='enter()' >";

	  if (isset($wprograma))
	      echo "<option value='".$wprograma."' selected>".$wprograma."</option>";

	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res);
	      echo "<option value='".$row[0]." - ".$row[1]."'>".$row[0]." - ".$row[1]."</option>";
	     }
	  echo "</select></td>";
     }


  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //REMITENTE
  if (isset($wremi) and $wremi=="on")
     {
	  if (isset($wcodrem) and ($wcodrem != ""))
	     {
		  $q =  " SELECT remreg, remnom "
		       ."   FROM ".$wbasedato."_000058 "
		       ."  WHERE remest = 'on' "
		       ."    AND remreg = '".$wcodrem."'"
		       ."  ORDER BY remnom ";

	      $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());
	      $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());

	      if ($num > 0)
	         {
		      $row = mysql_fetch_array($res);
		      $wcodrem = $row[0];
		      $wnomrem = $row[1];
		      $wremitente = $row[0]." - ".$row[1];

			  echo "<input type='hidden' name='wremitente' value='".$wremitente."'>";

		      /////
	          echo "<script language='Javascript'>";
	          echo "document.ventas.wnomrem.value='".$wnomrem."';";
	          echo "</script>";
			  /////

		      echo "<td align=left class=".$wcf." colspan=".$wcolrem1."><b> Remitente: </b><br><INPUT TYPE='text' NAME='wcodrem' VALUE='".$wcodrem."' onkeypress='if (validar(event)) submit_form(\"on\")'></td>";  //wcodmed
		      echo "<td align=left class=".$wcf." colspan=".$wcolrem2."><b></b><br><INPUT TYPE='text' NAME='wnomrem' VALUE='".$wnomrem."' onblur='enter()' onkeypress='if (validar(event)) submit_form(\"on\")'></td>";          //wnommed
	         }
	        else
	           {
		        echo "<td align=left class=".$wcf." colspan=".$wcolrem1."><b> Remitente: </b><br><INPUT TYPE='text' NAME='wcodrem' onkeypress='if (validar(event)) submit_form(\"on\")'></td>";    //wcodmed
			    echo "<td align=left class=".$wcf." colspan=".$wcolrem2."><b></b><br><INPUT TYPE='text' NAME='wnomrem' onkeypress='if (validar(event)) submit_form(\"on\")'></td>";          //wnommed
	           }
         }
        else
           {
	        if (isset($wnomrem) and ($wnomrem != ""))
			   {
			    $q =  " SELECT remreg, remnom "
			         ."   FROM ".$wbasedato."_000058 "
			         ."  WHERE remest = 'on' "
			         ."    AND remnom like '%".$wnomrem."%'"
			         ."  ORDER BY remnom ";
			    $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
			    $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;

			    if ($num > 0)
			       {
				    echo "<td align=left class=".$wcf." colspan=".$wcolrem1."><b> Remitente: </b><br><INPUT TYPE='text' NAME='wcodrem'  onkeypress='if (validar(event)) submit_form(\"on\")'></td>";    //wcodmed
				    for ($i=1;$i<=$num;$i++)
				       {
					    $row = mysql_fetch_array($res);
				        if ($num == 1)
				           {
				            $wcodrem=$row[0];
				            ///echo "<input type='HIDDEN' name='wcodrem' value='".$wcodrem."'>";

				            /////
				            echo "<script language='Javascript'>";
				            echo "document.ventas.wcodrem.value='".$wcodrem."';";
				            echo "</script>";
						    /////
			               }
				        $wnomrem1[$i]=$row[1];
				       }
				    echo "<td align=left class=".$wcf." colspan=".$wcolrem1."><b><font text color=".$wclfg."> </font></b><select name='wnomrem' onchange='enter()' >";
				    for ($i=1;$i<=$num;$i++)
				       {
				        echo "<option value='".$wnomrem1[$i]."'>".$wnomrem1[$i]."</option>";
				        if ($num == 1)
				           $wnomrem=$wnomrem1[$i];
				       }
				    echo "</select></td>";
			       }
	              else
	                 {
			 	      echo "<td align=left class=".$wcf." colspan=".$wcolrem1."><b> Remitente: </b><br><INPUT TYPE='text' NAME='wcodrem'  onkeypress='if (validar(event)) submit_form(\"on\")'></td>";    //wcodmed
					  echo "<td align=left class=".$wcf." colspan=".$wcolrem2."><b></b><br><INPUT TYPE='text' NAME='wnomrem' onkeypress='if (validar(event)) submit_form(\"on\")'></td>";          //wnommed
				     }
		         }
		        else
		           {
			 	    echo "<td align=left class=".$wcf." colspan=".$wcolrem1."><b> Remitente: </b><br><INPUT TYPE='text' NAME='wcodrem'  onkeypress='if (validar(event)) submit_form(\"on\")'></td>";    //wcodmed
					echo "<td align=left class=".$wcf." colspan=".$wcolrem2."><b></b><br><INPUT TYPE='text' NAME='wnomrem' onkeypress='if (validar(event)) submit_form(\"on\")'></td>";          //wnommed
				   }
           }
     }

  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //DIAGNOSTICO
  if (isset($wdiag) and $wdiag=="on")
     {
	  if (isset($wcoddia) and ($wcoddia != ""))
	     {
		  $q =  " SELECT codigo, descripcion "
		       ."   FROM root_000011 "
		       ."  WHERE codigo = '".$wcoddia."'"
		       ."  ORDER BY 2 ";

	      $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());
	      $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());

	      if ($num > 0)
	         {
		      $row = mysql_fetch_array($res);
		      $wcoddia = $row[0];
		      $wnomdia = $row[1];
		      $wdiagnostico = $row[0]." - ".$row[1];

		      /////
	          echo "<script language='Javascript'>";
	          echo "document.ventas.wnomdia.value='".$wnomdia."';";
	          echo "</script>";
			  /////

		      echo "<td align=left class=".$wcf." colspan=".$wcoldia1."><b> Diagnostico: </b><br><INPUT TYPE='text' NAME='wcoddia' VALUE='".$wcoddia."' onkeypress='if (validar(event)) submit_form(\"on\")'></td>";
		      echo "<td align=left class=".$wcf." colspan=".$wcoldia2."><b></b><br><INPUT TYPE='text' NAME='wnomdia' VALUE='".$wnomdia."' onchange='enter()' size=60 onkeypress='if (validar(event)) submit_form(\"on\")'></td>";
	         }
	        else
	           {
		        echo "<td align=left class=".$wcf." colspan=".$wcoldia1."><b> Diagnostico: </b><br><INPUT TYPE='text' NAME='wcoddia' onkeypress='if (validar(event)) submit_form(\"on\")'></td>";
			    echo "<td align=left class=".$wcf." colspan=".$wcoldia2."><b></b><br><INPUT TYPE='text' NAME='wnomdia' onkeypress='if (validar(event)) submit_form(\"on\")'></td>";
	           }
         }
        else
           {
	        if (isset($wnomdia) and ($wnomdia != ""))
			   {
			    $q =  " SELECT codigo, descripcion "
			         ."   FROM root_000011 "
			         ."  WHERE descripcion like '%".$wnomdia."%'"
			         ."  ORDER BY 2 ";
			    $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
			    $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;

			    if ($num > 0)
			       {
				    echo "<td align=left class=".$wcf." colspan=".$wcoldia1."><b> Diagnostico: </b><br><INPUT TYPE='text' NAME='wcoddia'  onkeypress='if (validar(event)) submit_form(\"on\")'></td>";
				    for ($i=1;$i<=$num;$i++)
				       {
					    $row = mysql_fetch_array($res);
				        if ($num == 1)
				           {
				            $wcoddia=$row[0];

				            /////
				            echo "<script language='Javascript'>";
				            echo "document.ventas.wcoddia.value='".$wcoddia."';";
				            echo "</script>";
						    /////
			               }
				        $wnomdia1[$i]=$row[1];
				       }
				    echo "<td align=left class=".$wcf." colspan=".$wcoldia1."><b><font text color=".$wclfg."> </font></b><select name='wnomdia' onchange='enter()' >";
				    for ($i=1;$i<=$num;$i++)
				       {
				        echo "<option value='".$wnomdia1[$i]."'>".$wnomdia1[$i]."</option>";
				        if ($num == 1)
				           $wnomdia=$wnomdia1[$i];
				       }
				    echo "</select></td>";
			       }
	              else
	                 {
			 	      echo "<td align=left class=".$wcf." colspan=".$wcolrem1."><b> Diagnostico: </b><br><INPUT TYPE='text' NAME='wcoddia'  onkeypress='if (validar(event)) submit_form(\"on\")'></td>";
					  echo "<td align=left class=".$wcf." colspan=".$wcolrem2."><b> </b><br><INPUT TYPE='text' NAME='wnomdia' onkeypress='if (validar(event)) submit_form(\"on\")'></td>";
				     }
		         }
		        else
		           {
			 	    echo "<td align=left class=".$wcf." colspan=".$wcoldia1."><b> Diagnostico: </b><br><INPUT TYPE='text' NAME='wcoddia'  onkeypress='if (validar(event)) submit_form(\"on\")'></td>";
					echo "<td align=left class=".$wcf." colspan=".$wcoldia2."><b> </b><br><INPUT TYPE='text' NAME='wnomdia' onkeypress='if (validar(event)) submit_form(\"on\")'></td>";
				   }
           }
     }


  ////////////////////////////////////////////////////////////////////////////////////////////////////////////   function ira(){document.ventas.wvalfpa[0].focus();}
  //RANGO DEL USUARIO
  if (isset($wran) and $wran=="on")
     {
	  if (isset($wrango) and trim($wrango)!="")
	     $q =  " SELECT rancod "
		      ."   FROM ".$wbasedato."_000091 "
		      ."  WHERE rancod != '".$wrango."'"
		      ."  ORDER BY 1 ";
	    else
	       $q =  " SELECT rancod "
			    ."   FROM ".$wbasedato."_000091 "
			    ."  ORDER BY 1 ";
	  $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
	  $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;

	  //creo una tabla dentro de un TD para ahorrar espacio con el campo que sigue

	  echo "<td align=left class=".$wcf." colspan=2>";
	  echo "<table>";
	  echo "<tr>";
	  echo "<td align=left class=".$wcf." colspan=".$wcolran."><b><font text color=".$wclfg."> Rango: </font></b><select name='wrango' >";

	  if (isset($wrango))
	      echo "<option value='".$wrango."' selected>".$wrango."</option>";

	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res);
	      echo "<option value='".$row[0]." - ".$row[1]."'>".$row[0]." - ".$row[1]."</option>";
	     }
	  echo "</select></td>";
     }

  ////////////////////////////////////////////////////////////////////////////////////////////////////////////   function ira(){document.ventas.wvalfpa[0].focus();}
  //TIPO DE DESPACHO
  if (isset($wtde) and $wtde=="on")
     {
	  if (isset($wtipdes) and trim($wtipdes)!="")
	     $q =  " SELECT descripcion "
		      ."   FROM det_selecciones "
		      ."  WHERE medico = '".$wbasedato."'"
		      ."    AND codigo = '014' "
		      ."    AND descripcion != '".$wtipdes."'"
		      ."  ORDER BY 1 desc";
	    else
	       $q =  " SELECT descripcion "
			    ."   FROM det_selecciones "
			    ."  WHERE medico = '".$wbasedato."'"
		        ."    AND codigo = '014' "
			    ."  ORDER BY 1 desc";
	  $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
	  $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;

	  echo "<td align=left class=".$wcf." colspan=".$wcoltde."><b><font text color=".$wclfg."> Tipo Despacho: </font></b><select name='wtipdes' >";

	  if (isset($wtipdes))
	      echo "<option value='".$wtipdes."' selected>".$wtipdes."</option>";

	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res);
	      echo "<option value='".$row[0]."'>".$row[0]."</option>";
	     }
	  echo "</select></td>";
	  echo "</tr>";
	  echo "</table>";
	  echo "</td>";
     }
  echo "</tr>";

  //Este tr sirve para cuando se minimiza la ventana flotante de los laboratorios asociados al usuario.
  echo "<tr id='ver_laboratorios' style='display:none;'>
            <td colspan='5' align='right'>
                    <div style='font-size: 8pt;cursor:pointer; font-weight:bold;' onClick='cargar_flotante();'>
                            Ver laboratorios asociados
                            <img src='../../images/medical/hce/mas.PNG' >
                    </div>
            </td>
        </tr>";
   //Este tr sirve para cuando se minimiza la ventana flotante de los articulos con dscto escalonado.
  echo "<tr id='ver_articulos' style='display:none;'>
        <td colspan='5' align='right'>
                <div style='font-size: 8pt;cursor:pointer; font-weight:bold;' onClick='cargar_flotante_articulo();'>
                        Ver registro linea vital
                        <img src='../../images/medical/hce/mas.PNG' >
                </div>
        </td>
        </tr>";
 echo "<tr id='ver_mas_descuentos' style='display:none;'>
		<td colspan='5' align='right'>
				<div style='font-size: 8pt;cursor:pointer; font-weight:bold;' onClick='cargar_flotante_aprob();'>
						Ver laboratorios asociados
						<img src='../../images/medical/hce/mas.PNG' >
				</div>
		</td>
	</tr>";




	if(isset($wfpa) && $wfpa)
		echo "<input type='HIDDEN' name='wfpa' value='".$wfpa."'>";
	if(isset($wnrobon) && $wnrobon)
		echo "<input type='HIDDEN' name='wnrobon' value='".$wnrobon."'>";
	if(isset($wdocane) && $wdocane)
		echo "<input type='HIDDEN' name='wdocane' value='".$wdocane."'>";
	if(isset($wobsrec) && $wobsrec)
		echo "<input type='HIDDEN' name='wobsrec' value='".$wobsrec."'>";
	if(isset($wvalfpa) && $wvalfpa)
		echo "<input type='HIDDEN' name='wvalfpa' value='".$wvalfpa."'>";
	if(isset($wbandes) && $wbandes)
		echo "<input type='HIDDEN' name='wbandes' value='".$wbandes."'>";

 ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ///====================================================================================================================================================
  ///===== A C A   E M P I E Z A N   L O S   R I P S ====================================================================================================
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //====== SI LA EMPRESA PIDE  **** R I P S ****
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

  echo "<input type='hidden' name='wrips' value='".$wrips."'>";
  if (isset($wrips) and $wrips == "on")
    {

	  echo "<tr><td align=center colspan=6 class='encabezadoTabla'><b>* * *   R I P S   * * *</b></td></tr>";

	  echo "<tr>";
	  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  //TIPO DE USUARIO
	  echo "<td align=left class=fila2  colspan=1><b>Tipo de Usuario: </b><br><select name='wtipusu' onchange='enter()'>";
	  if (isset($wtipusu))
	     {
		  $q =  " SELECT tuscod, tusdes "
	           ."   FROM root_000027 "
	           ."  WHERE tuscod != mid('".$wtipusu."',1,instr('".$wtipusu."','-')-1) "
	           ."    AND tusest = 'on' "
		       ."  ORDER BY 1 desc ";
		 }
	    else
	       {
	        $q =  " SELECT tuscod, tusdes "
	             ."   FROM root_000027 "
	             ."  WHERE tusest = 'on' "
		         ."  ORDER BY 1 desc ";
		   }
	  $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
	  $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;
	  if (isset($wtipusu))
	     echo "<option value='".$wtipusu."' selected>".$wtipusu."</option>";
	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res);
	      echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
	     }
	  echo "</select></td>";

	  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  //TIPO DE IDENTIFICACION O DOCUMENTO
	  echo "<td align=left class=fila2  colspan=1><b>Tipo de Dcto: </b><select name='wtipdto'>";
	  if (isset($wtipdto))
	     {
		  $q =  " SELECT codigo, descripcion "
	           ."   FROM root_000007 "
	           ."  WHERE codigo != mid('".$wtipdto."',1,instr('".$wtipdto."','-')-1) "
	           ."  ORDER BY 2, 1 ";
		 }
	    else
	       {
	        $q =  " SELECT codigo, descripcion "
	             ."   FROM root_000007 "
	             ."  ORDER BY 2, 1 ";
		   }
	  $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
	  $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;
	  if (isset($wtipdto))
	     echo "<option value='".$wtipdto."' selected>".$wtipdto."</option>";
	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res);
	      echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
	     }
	  echo "</select></td>";


	  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  //MUNICIPIO
	  if (!isset($wmuni)) $wmuni="05001-MEDELLIN";
	  echo "<td align=left class=fila2  colspan=1><b>Municipio: </b><select name='wmuni'>";
	  if (isset($wmuni))
	     {
		  $q =  " SELECT codigo, nombre "
	           ."   FROM root_000006 "
	           ."  WHERE medico = 'root' "
	           ."    AND codigo != mid('".$wmuni."',1,instr('".$wmuni."','-')-1) "
		       ."  ORDER BY 2, 1 ";
		 }
	    else
	       {
	        $q =  " SELECT codigo, nombre "
	           ."   FROM root_000006 "
	           ."  WHERE medico = 'root' "
	           ."  ORDER BY 2, 1 ";
		   }
	  $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
	  $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;
	  if (isset($wmuni))
	     echo "<option value='".$wmuni."' selected>".$wmuni."</option>";
	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res);
	      echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
	     }
	  echo "</select></td>";

	  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  //ZONA
	  echo "<td align=left class=fila2  colspan=1><b>Zona: </b><br><select name='wzona'>";
	  if (isset($wzona))
	     {
		  $q =  " SELECT zoncod, zondes "
	           ."   FROM root_000028 "
	           ."  WHERE zoncod != mid('".$wzona."',1,instr('".$wzona."','-')-1) "
	           ."    AND zonest = 'on' "
		       ."  ORDER BY 2 desc ";
		 }
	    else
	       {
	        $q =  " SELECT zoncod, zondes "
	             ."   FROM root_000028 "
	             ."  WHERE zonest = 'on' "
	             ."  ORDER BY 2 desc ";
		   }
	  $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
	  $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;
	  if (isset($wzona))
	     echo "<option value='".$wzona."' selected>".$wzona."</option>";
	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res);
	      echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
	     }
	  echo "</select></td>";

	  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  //SEXO
	  echo "<td align=left class=fila2  colspan=2><b>Sexo: </b><br><select name='wsexo'>";
	  if (isset($wsexo))
	     {
		  $q =  " SELECT sexcod, sexdes "
	           ."   FROM root_000029 "
	           ."  WHERE sexcod != mid('".$wsexo."',1,instr('".$wsexo."','-')-1) "
	           ."    AND sexest = 'on' "
		       ."  ORDER BY 2, 1 ";
		 }
	    else
	       {
	        $q =  " SELECT sexcod, sexdes "
	             ."   FROM root_000029 "
	             ."  WHERE sexest = 'on' "
	             ."  ORDER BY 2, 1 ";
		   }
	  $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
	  $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;
	  if (isset($wsexo) && $wsexo!="" && $wsexo!=" ")
	     echo "<option value='".$wsexo."' selected>".$wsexo."</option>";
	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res);
	      echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
	     }
	  echo "</select></td>";

	  echo "</tr>";


	  echo "<tr>";
	  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  //POLIZA
	  if (isset($wpoliza)) //Si ya fue digitada la poliza
	     echo "<td align=left class=fila2  colspan=1><b> Poliza: </b><br><INPUT TYPE='text' NAME='wpoliza' SIZE=30 VALUE='".$wpoliza."' onkeypress='if (validar(event)) submit_form(\"on\")'></td>";  //wpoliza
	    else
	       echo "<td align=left class=fila2  colspan=1><b> Poliza: </b><br><INPUT TYPE='text' NAME='wpoliza' SIZE=30 onkeypress='if (validar(event)) submit_form(\"on\")'></td>";                   //wpoliza

	  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  //NRO DE AUTORIZACION
	  if (isset($wauto)) //Si ya fue digitado el número de autorización
	     echo "<td align=left class=fila2  colspan=1><b> Autorización: </b><br><INPUT TYPE='text' NAME='wauto' SIZE=30 VALUE='".$wauto."' onkeypress='if (validar(event)) submit_form(\"on\")'></td>";
	    else
	       echo "<td align=left class=fila2  colspan=1><b> Autorización: </b><br><INPUT TYPE='text' NAME='wauto' SIZE=30 onkeypress='if (validar(event)) submit_form(\"on\")'></td>";

	  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  //FECHA VENCIMIENTO POLIZA
	  if (isset($wfecven) && $wfecven!="" && $wfecven!=" ")
 	     echo "<td align=left class=fila2  colspan=2><b> Vence en: </b><br><INPUT TYPE='text' NAME='wfecven' SIZE=30 VALUE='".$wfecven."' onkeypress='if (validar(event)) submit_form(\"on\")'></td>";  //wpoliza
 	    else
 	       echo "<td align=left class=fila2  colspan=2><b> Vence en: </b><br><INPUT TYPE='text' NAME='wfecven' SIZE=30 VALUE='".$wfecha."' onkeypress='if (validar(event)) submit_form(\"on\")'></td>";                   //wpoliza

	  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  //EDAD
 	  if (isset($wedad) && $wedad!="" && $wedad!=" ") //Si ya fue digitada la edad del cliente
 	     echo "<td align=left class=fila2  colspan=2><b> Edad: </b><br><INPUT TYPE='text' NAME='wedad' SIZE=30 VALUE='".$wedad."' onkeypress='if (validar(event)) submit_form(\"on\")'></td>";
 	    else
 	       echo "<td align=left class=fila2  colspan=2><b> Edad: </b><br><INPUT TYPE='text' NAME='wedad' SIZE=30 onkeypress='if (validar(event)) submit_form(\"on\")'></td>";
	  echo "</tr>";


	  /////////////////////////////////
	  // DATOS DE FACTURACIÓN FURIPS //
	  /////////////////////////////////


	  if(isset($consulta_unix) && $consulta_unix=="1")
	  {

		// Inicialización de variables globales
		$tot_fac_unix = 0;
		$tot_fac_matrix = 0;
		$j = 1;

		//echo "$wdocpacaux - $wdocpac <br>";
		// Miro si hay accidente seleccionado y lo asigno a la variable de accidente
	    if(isset($wacc_btn) && $wacc_btn!="" && $wacc_btn!="0" && isset($wdocpacaux) &&  $wdocpacaux!="9999" && $wdocpacaux!="" && $wdocpac==$wdocpacaux)
			$wacc = $wacc_btn;
		else
			$wacc = "";

		if($conexUnix)
		{

			// Obtengo la historia clínica con base al documento de identificación
			// También obtengo el último número de ingreso y la última fecha de ingreso

			if(isset($wacc) && $wacc!="")
				$query="SELECT pachis, pacnum, pacfec
						  FROM inpac, inaccdet
						 WHERE pacced='$wdocpac'
						   AND pacced=accdetced
						   AND accdetacc = '$wacc' ";
			else
				$query="SELECT pachis, pacnum, pacfec
						  FROM inpac
						 WHERE pacced='$wdocpac'
					  ORDER BY pacnum DESC";

			$err_o = odbc_do($conexUnix,$query);

			if (odbc_fetch_row($err_o))
			{
				$tbpac = "inpac";
				$pachis = odbc_result($err_o,1);
				$pacnum = odbc_result($err_o,2);
				$pacfec = odbc_result($err_o,3);
			}
			else
			{
				if(isset($wacc) && $wacc!="")
					$query="SELECT pachis, pacnum, pacing
							  FROM inpaci, inaccdet
							 WHERE pacced='$wdocpac'
							   AND pacced=accdetced
							   AND accdetacc = '$wacc' ";
				else
					$query="SELECT pachis, pacnum, pacing
							  FROM inpaci
							 WHERE pacced='$wdocpac'
						  ORDER BY pacnum DESC";

				$err_1 = odbc_do($conexUnix,$query);
				if (odbc_fetch_row($err_1))
				{
					$tbpac = "inpaci";
					$pachis = odbc_result($err_1,1);
					$pacnum = odbc_result($err_1,2);
					$pacfec = odbc_result($err_1,3);
				}
				else
				{
					$tbpac = "";
					$pachis = "";
					$pacnum = "";
					$pacfec = date("Y-m-d", strtotime ("next Day"));
				}
			}

			// Consulto los datos del accidente
			if(isset($pachis) && $pachis!="" && $pachis!="0")
			{

				if(isset($wacc) && $wacc!="" && $wacc!="0")
				{
					  // Consulto datos de accidentes para la historia clínica del paciente
					  $select = " accacc, accfec, accnum, '".$long."' as accdetffi, accdetacc, accdethis ";
					  $from =	" inacc, inaccdet ";
					  $where =  " acchis='$pachis'
								   AND accacc='$wacc'
								   AND accind='P'
								   AND accdethis = acchis
								   AND accdetnum = accnum ";
					  $order =  " ORDER BY 1 DESC ";

					  unset($llaves);
					  $llaves[0]=6;
					  $llaves[1]=5;
				}
				else
				{
					  // Consulto datos de accidentes para la historia clínica del paciente
					  $select = " accacc, accfec, accnum, '".$long."' as accdetffi, accdetacc, accdethis ";
					  $from =	" inacc, inaccdet ";
					  $where =  " acchis='$pachis'
								   AND accind='P'
								   AND accdethis = acchis
								   AND accdetnum = accnum ";
					  $order =  " ORDER BY 1 DESC ";

					  unset($llaves);
					  $llaves[0]=6;
					  $llaves[1]=5;
				}

				$err_o = ejecutar_consulta_furips ($select, $from, $where, $llaves, $conexUnix, $order );
				//$err_o = odbc_exec($conexUnix,$query);
				//echo $query."<br>";
				echo "<tr>";
				echo "<td colspan=13>";

				// Muestro los datos del accidente al que se le va a cargar la factura
				if(odbc_fetch_row($err_o))
				{ // Si hay accidente seleccionado muestre los datos de éste
					$accacc=trim(odbc_result($err_o,1));
					$accfec=trim(odbc_result($err_o,2));
					$accnum=trim(odbc_result($err_o,3));
					$accffi=trim(odbc_result($err_o,4));
					$wacc=$accacc;

					echo "<table align=left border=0>";
					echo "<tr height=27 class='fila1'>";
					echo "<td nowrap align=center width=210><b> &nbsp;Accidente cargado &nbsp; </b></td>";
					echo "<td align=center width=10 bgcolor='#ffffff'>&nbsp;</td>";
					echo "<td align=center class='articuloControl' nowrap><b> &nbsp; Nro. accidente: $accacc &nbsp; </b></td>";
					echo "<td nowrap align=center><b> &nbsp; Fecha: $accfec &nbsp; </b></td>";
					echo "<td align=center nowrap><b> &nbsp; Historia: $pachis &nbsp; </b></td>";
					echo "<td align=center nowrap><b> &nbsp; Nro. de Ingreso: $accnum &nbsp; </b></td>";
					echo "<td nowrap align=center><b> &nbsp; <a href='../Reportes/form_topes_furips.php?wemp_pmla=".$wemp_pmla."&historia=".$pachis."&ingreso=".$accnum."&accidente=".$accacc."&fechaing=".$accfec."&fechafin=".$accffi."&tp=1' target='_blank'>Consultar Facturación Accidente</a> &nbsp; </b> &nbsp; </td>";
					echo "</tr>";
					echo "</table>";
				}
				else
				{



					// Consulto datos de accidentes para la historia clínica del paciente
					$select = " accacc, accfec, accnum, '".$long."' as accdetffi, accdetacc, accdethis ";
					$from =	" inacc, inaccdet ";
					$where =  " acchis='$pachis'
							   AND accind='P'
							   AND accdethis = acchis
							   AND accdetnum = accnum ";
					$order =  " ORDER BY 1 DESC ";

					unset($llaves);
					$llaves[0]=6;
					$llaves[1]=5;

					$err_o = ejecutar_consulta_furips ($select, $from, $where, $llaves, $conexUnix, $order );
					//$err_o = odbc_exec($conexUnix,$query);

					echo "<tr>";
					echo "<td colspan=13>";

					if(odbc_fetch_row($err_o))
					{
						$accacc=trim(odbc_result($err_o,1));
						$accfec=trim(odbc_result($err_o,2));
						$accnum=trim(odbc_result($err_o,3));
						$accffi=trim(odbc_result($err_o,4));
						$wacc=$accacc;

						echo "<table align=left border=0>";
						echo "<tr height=27 class='fila1'>";
						echo "<td nowrap align=center width=210><b> &nbsp; Accidente cargado &nbsp; </b></td>";
						echo "<td align=center width=10 bgcolor='#ffffff'>&nbsp;</td>";
						echo "<td nowrap align=center><b> &nbsp; Fecha accidente: $accfec &nbsp; </b></td>";
						echo "<td align=center nowrap><b> &nbsp; Nro. accidente: $accacc &nbsp; </b></td>";
						echo "<td align=center nowrap><b> &nbsp; Nro. de Ingreso del paciente: $accnum &nbsp; </b></td>";
						echo "<td nowrap align=center><b> &nbsp; <a href='../Reportes/form_topes_furips.php?wemp_pmla=".$wemp_pmla."&historia=".$pachis."&ingreso=".$accnum."&accidente=".$accacc."&fechaing=".$accfec."&fechafin=".$accffi."&tp=1' target='_blank'>Consultar Facturación Accidente</a> &nbsp; </b> &nbsp; </td>";
						echo "</tr>";
						echo "</table>";
					}
				}

				echo "</td>";
				echo "</tr>";

				//echo"<input type='text' name='numerodeigreso' value='".$accnum."' id='numerodeigreso' >";
				echo"<input type='hidden' name='numerodeaccidente' value='".$accacc."' id='numerodeaccidente' >";
			}

			//echo "<tr><td></td></tr>"


			// Calculo el total facturado en el último ingreso
			  if(!isset($wdocpac) || $wdocpac=="" || $wdocpac==" " || $wdocpac=="9999")
			  {
					$facturado = 0;
			  }
			  else
			  {
					facturacion_matrix($conex,$wbasedato,$wdocpac,$wacc);
			  }

			  facturacion_clinica($conexUnix,$pachis,$wacc);

			  $total = $tot_fac_matrix+$tot_fac_unix;
			  $facturado = $total;
		}
		else
		{
			$total = 0;
			$facturado = $total;
		}

		if($conexUnix)
		{
			/*
			$tbunix = "inpaci";
			echo "TB: ".$tbunix."<br>";
			//$query="SELECT *
			//	FROM inaccpro WHERE accprohis='$historia' AND accproacc='$accidente'";
			$query = "SELECT *
						FROM ".$tbunix." WHERE pacced = '32482144'";
			echo $query."<br>";

			$err_o = odbc_do($conexUnix,$query);
			if (odbc_fetch_row($err_o))
			{
				for($i=1;$i<=odbc_num_fields($err_o);$i++)
				{
					echo odbc_field_name($err_o,$i)." - ".odbc_result($err_o,$i)."<br>";
				}
			}
			*/

		  $facturado_aseguradora = 0;
		  $facturado_ase_est = 0;

		  // Obtengo los topes para Aseguradora del paciente y Aseguradora Estatal
			$tope_aseguradora = 0;
			$tope_ase_est = 0;

			// Obtengo el NIT de la empresa responsable seleccionada en el formulario
			$wempresa_arr = explode('-',$wempresa);
			$wempresa_nit = $wempresa_arr[1];

			// Consulto los datos de tope y nit de la empresa resposable 1 (Aseguradora del paciente)
			 $select = " salacctop, '".$long."' as empnit ";
		   // $select = " salacctop, empcod as empnit ";
			$from =	" fasalacc, inemp ";
			$where =  " salacccer = empcod
						AND salacchis='$pachis'
					    AND salaccacc = '$wacc'
					    AND salaccnre = '1'";

			unset($llaves);
			$llaves[0]=1;
			$llaves[1]=2;



			$err_o = ejecutar_consulta_furips ($select, $from, $where, $llaves, $conexUnix );
			//$err_o = odbc_do($conexUnix,$query);
			//echo $query."<br>";

			// Consulto los datos de tope y nit de la empresa resposable 2 (Aseguradora estatal)
			// Consulto los datos de tope y nit de la empresa resposable 1 (Aseguradora del paciente)
			$select = " salacctop, '".$long."' as empnit ";
			$from =	" fasalacc, inemp ";
			$where =  " salacccer = empcod
						AND salacchis='$pachis'
					    AND salaccacc = '$wacc'
					    AND salaccnre = '2'";

			unset($llaves);
			$llaves[0]=1;
			$llaves[1]=2;

			$err_f = ejecutar_consulta_furips ($select, $from, $where, $llaves, $conexUnix );
			//$err_f = odbc_do($conexUnix,$query);

			if (odbc_fetch_row($err_o) && odbc_fetch_row($err_f))
			{ // Si el accidente tiene registro de tope en Unix, obtengo el tope
				$tope_aseguradora = trim(odbc_result($err_o,1));
				$tope_ase_est = trim(odbc_result($err_f,1));
				$nit_res = trim(odbc_result($err_o,2));
				//$nit_ase_est = odbc_result($err_f,2);
			}
			else
			{	// Sino obtengo el tope registrado para el año en curso en Matrix
				// Consulto los topes de aseguradora del paciente y asguradora estatal de este año en Matrix
				$q = " SELECT cfgcco, Cfgtas, Cfgtfo "
					."   FROM ".$wbasedato."_000049 "
					."  WHERE cfgcco = '".$wcco."' ";
				$res_tope = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				$row_tope = mysql_fetch_array($res_tope);
				$tope_aseguradora = $row_tope['Cfgtas'];
				$tope_ase_est = $row_tope['Cfgtfo'];
				$nit_res = "";
			}

			$select = " salacctop, empcod as empnit ";
			$from =	" fasalacc, inemp ";
			$where =  " salacccer = empcod
						AND salacchis='$pachis'
					    AND salaccacc = '$wacc'
					    AND salaccnre = '1'";

			unset($llaves);
			$llaves[0]=1;
			$llaves[1]=2;



			$err_o = ejecutar_consulta_furips ($select, $from, $where, $llaves, $conexUnix );


			if (odbc_fetch_row($err_o))
			{
				$nit_resposable1 = trim(odbc_result($err_o,2));
				$nit_empresa_seleccionada = explode("-" ,$wempresa);
				echo "<input id='nit_responsable' name='nit_responsable'  type='hidden'  value='".$nit_resposable1."'><input  type='hidden'  id='empresa_matrix'  name='empresa_matrix'  value='".$nit_empresa_seleccionada[1]."'>";
			}
			// Consulto código actual de la aseguradora estatal según root_000051
			$q =   "SELECT Detval "
				."    FROM root_000051 "
				."   WHERE Detemp = '".$wemp_pmla."' "
				."     AND Detapl = 'aseguradoraEstatal' ";
			$res_ase_est = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
			$row_ase_est = mysql_fetch_array($res_ase_est);
			$ase_est = explode("-",$row_ase_est['Detval']);
			// Consulto el NIT de la aseguradora estatal
			$nit_ase_est = $ase_est[0];
			// Consulto el nombre de la aseguradora estatal
			$nom_ase_est = $ase_est[1];

			// Quito espacios en blanco que puedan haber entre el nit de la empresa responsable seleccionada y el nit de la empresa responsable asociada al documento digitado
			// Esto para evitar error en la comparación posterior
			$nit_res = str_replace(" ","",$nit_res);
			$nit_ase_est = str_replace(" ","",$nit_ase_est);
			$wempresa_nit = str_replace(" ","",$wempresa_nit);

			// Se define el valor para la variable de validación de datos correctos para facturar por SOAT
			$valfacrips = '1';
			$alert = '';

			//echo $wempresa_nit." - ".$nit_res." - ".$nit_ase_est."<br>";
			if(isset($wempresa_nit) && $wempresa_nit && $nit_res!="" && $wempresa_nit!=$nit_res && $wempresa_nit!=$nit_ase_est && $wdocpac!='9999')
			{
				$alert = 'La empresa responsable asociada al documento no coincide con la empresa responsable seleccionada';
				$valfacrips = '2';
			}
			if(isset($wempresa_nit) && $wempresa_nit && $wempresa_nit==$nit_res && $facturado>$tope_aseguradora && $wdocpac!='9999')
			{
				$alert = 'No se puede facturar por la empresa responsable seleccionada ya que el tope para ésta se ha excedido';
				$valfacrips = '3';
			}
			if($facturado>=($tope_aseguradora+$tope_ase_est) && $wdocpac!='9999')
			{
				$alert = 'Los topes de facturación por SOAT ya han sido excedidos, debe seleccionar otra opción de venta';
				$valfacrips = '4';
			}

			// Se crea un campo oculto para llevar la variable de validación de datos correctos para facturar por SOAT
			if($alert!='')
			{
				echo "<input type='hidden' name='walert' value='".$alert."'>";
				echo "<input type='hidden' name='wvalfacrips' value='".$valfacrips."'>";
			}

			if(isset($wempresa_nit) && $wempresa_nit && $nit_res!="" && $wempresa_nit==$nit_res)
				echo "<input type='hidden' name='wnre' value='1'>";
			elseif(isset($wempresa_nit) && $wempresa_nit && $nit_ase_est!="" && $wempresa_nit==$nit_ase_est)
				echo "<input type='hidden' name='wnre' value='2'>";
			else
				echo "<input type='hidden' name='wnre' value='0'>";

			echo "<input type='hidden' name='wsaltopase' value='".$tope_aseguradora."'>";
			echo "<input type='hidden' name='wsaltopase_est' value='".$tope_ase_est."'>";
		    echo "<input type='hidden' name='wpachis' value='".$pachis."'>";
			//echo"<input type='text' name='numerodeaccidente' value='".$accacc."' id='numerodeaccidente' >";
		    echo "<input type='hidden' name='wdocpacaux' value='".$wdocpac."'>";

		  // Calculo facturado por aseguradora de paciente y aseguradora estatal
		  if($facturado > $tope_aseguradora)
		  {
			$facturado_aseguradora = $tope_aseguradora;

			  if($facturado > ($tope_aseguradora+$tope_ase_est))
			  {
				$facturado_ase_est = $tope_ase_est;
			  }
			  else
			  {
				$facturado_ase_est = $facturado - $tope_aseguradora;
			  }

		  }
		  else
		  {
			$facturado_aseguradora = $facturado;
			$facturado_ase_est = 0;
		  }

		  $resta_aseguradora = $tope_aseguradora - $facturado_aseguradora;
		  $resta_ase_est = $tope_ase_est - $facturado_ase_est;
		  $resta_tercero = $facturado - ($tope_ase_est + $tope_aseguradora);
		  if($resta_tercero < 0)
			$resta_tercero = 0;

		  $saldo_disponible = ($tope_ase_est+$tope_aseguradora)-$facturado;
		  if($saldo_disponible<0)
			$saldo_disponible = 0;

		  echo "<tr>";
		  echo "<td colspan=13>";
		  echo "<table align=center width=100% border=0>";
		  echo "<tr>";
		  echo "<td class=fila2 align=center width=210><b>Facturado Total</b></td>";
		  echo "<td align=center width=10>&nbsp;</td>";
		  echo "<td class=fila2 align=center><b>Tope Aseguradora</b></td>";
		  echo "<td class=fila2 align=center><b>Facturado Aseguradora</b></td>";
		  echo "<td class=fila2 align=center><b>Resta Aseguradora</b></td>";
		  echo "<td align=center>&nbsp;</td>";
		  echo "<td class=fila2 align=center><b>Tope ".$nom_ase_est." </b></td>";
		  echo "<td class=fila2 align=center><b>Facturado ".$nom_ase_est." </b></td>";
		  echo "<td class=fila2 align=center><b>Resta ".$nom_ase_est." </b></td>";
		  echo "<td align=center>&nbsp;</td>";
		  echo "<td class=fila1 align=center><b>Saldo Disponible</b></td>";
		  if($resta_tercero>0)
			echo "<td class=fila1 align=center><b>Resta Tercero</b></td>";
		  echo "<td align=center>&nbsp;</td>";
		  echo "</tr>";
		  echo "<tr>";
		  echo "<td class=fila2 align=center>&nbsp; ".number_format((float)$facturado,0,'.',',')." &nbsp;</td>";
		  echo "<td align=center>&nbsp;</td>";
		  echo "<td class=fila2 align=center>&nbsp; ".number_format((float)$tope_aseguradora,0,'.',',')." &nbsp;</td>";
		  echo "<td class=fila2 align=center>&nbsp; ".number_format((float)$facturado_aseguradora,0,'.',',')." &nbsp;</td>";
		  echo "<td class=fila2 align=center>&nbsp; ".number_format((float)$resta_aseguradora,0,'.',',')." &nbsp;</td>";
		  echo "<td align=center>&nbsp;</td>";
		  echo "<td class=fila2 align=center>&nbsp; ".number_format((float)$tope_ase_est,0,'.',',')." &nbsp;</td>";
		  echo "<td class=fila2 align=center>&nbsp; ".number_format((float)$facturado_ase_est,0,'.',',')." &nbsp;</td>";
		  echo "<td class=fila2 align=center>&nbsp; ".number_format((float)$resta_ase_est,0,'.',',')." &nbsp;</td>";
		  echo "<td align=center>&nbsp;</td>";
		  echo "<td class=fila2 align=center>&nbsp; ".number_format((float)$saldo_disponible,0,'.',',')." &nbsp;</td>";
		  if($resta_tercero>0)
			echo "<td class=fila2 align=center>&nbsp; ".number_format((float)$resta_tercero,0,'.',',')." &nbsp;</td>";
		  echo "<td align=center>&nbsp;</td>";
		  echo "</tr>";
		  echo "</table>";
		  echo "</td>";
		  echo "</tr>";

		}
		else
		{
			echo "<tr><td colspan='13'><table align='center' width='70%' border='0'><tr><td class='fondoAmarillo' align='center'><b>No se pudo establecer conexión con Unix. No se podrán mostrar los datos de accidentes</b></td></tr></table></td></tr>";
		}

		if(!isset($wacc))
			$wacc = "";

		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Lista de accidentes con opción de selección de uno de estos para ser tenido en cuenta en la factura //
		/////////////////////////////////////////////////////////////////////////////////////////////////////////

		if($conexUnix)
		{
			if(isset($pachis) && $pachis!="" && $pachis!="0" && isset($wacc) && $wacc!="" && $wacc!="0")
			{
				// Consulto datos de accidentes para la historia clínica del paciente
				$select = " accacc, accfec, accnum, '".$long."' as accdetffi, accdetacc, accdethis ";
				$from =	" inacc, inaccdet ";
				$where =  " acchis='$pachis'
						   AND accind='P'
						   AND accacc!=$wacc
						   AND accdethis = acchis
						   AND accdetnum = accnum ";
				$order =  " ORDER BY 1 DESC ";

				unset($llaves);
				$llaves[0]=6;
				$llaves[1]=5;

				$err_o = ejecutar_consulta_furips ($select, $from, $where, $llaves, $conexUnix, $order );
				//$err_o = odbc_do($conexUnix,$query);

				if(odbc_fetch_row($err_o))
				{
					$contador=0;
					echo "<tr>";
					echo "<td colspan=13>";

					echo "<table border=0 align=left>";
					echo "<tr class='encabezadoTabla'>";
					echo "<td nowrap align=center> &nbsp; Otros Accidentes &nbsp; </td>";
					echo "</tr>";

					do
					{
						$chk = " ";
						if (is_int ($contador/2))
						   $wcf="fila1";  // color de fondo de la fila
						else
						   $wcf="fila2"; // color de fondo de la fila

						$contador++;

						$accacc=trim(odbc_result($err_o,1));
						$accfec=trim(odbc_result($err_o,2));
						$accnum=trim(odbc_result($err_o,3));
						$accffi=trim(odbc_result($err_o,4));

						echo "<tr class='".$wcf."'>";
						echo "<td nowrap align=center> &nbsp; Fecha: $accfec &nbsp; </td>";
						echo "<td align=center nowrap> &nbsp; Nro. accidente: $accacc &nbsp; </td>";
						echo "<td align=center nowrap> &nbsp; Nro. Ingreso: $accnum &nbsp; </td>";
						echo "<td nowrap align=center> &nbsp; <a href='../Reportes/form_topes_furips.php?wemp_pmla=$wemp_pmla&historia=$pachis&ingreso=$accnum&accidente=$accacc&fechaing=$accfec&fechafin=$accffi&tp=1' target='_blank'>Consultar facturación accidente</a> &nbsp; </td>";
						if(isset($wacc_btn) && $wacc_btn==$accacc) $chk = "checked ";
						echo "<td nowrap align=center> &nbsp; <input type='radio' name='wacc_btn' id='wacc_btn' ".$chk." value='$accacc' onclick='enter()'> <b>Seleccionar</b> &nbsp; </td>";
						echo "</tr>";
					} while (odbc_fetch_row($err_o));

					echo "</table>";

					echo "</td>";
					echo "</tr>";
				}
			}
		}
	  }
	}
	else
	{
		$wacc = "";
		echo "<input type='hidden' id='wacc' name='wacc' value='$wacc'>";
	}


  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //====== ACA TERMINA LA INFORMACION PARA  **** R I P S ****
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	}

 if((isset($consultaAjax) && $consultaAjax=='envio') || (isset($consultaAjax2) && $consultaAjax2=='envio'))
 {
  //if (!isset($wventa) or ($wventa=="N"))
    // echo "<tr><td colspan=10>&nbsp</td></tr>";  //Solo muestra esta linea antes de realizar la venta efectiva

  if (isset($wclitip))
     if (($wclitip <> "GENERAL") and ($wclitip <> "NO APLICA"))
        echo "<tr class='articuloControl'><td align=center colspan=10>CLIENTE ESPECIAL: ".$wclitip."</td></tr>
			  <tr><td colspan=10></td></tr><tr><td colspan=10></td></tr>";
	// Determina los datos requeridas por la empresa
    if (isset($wempresa))
     {
      $q= "   SELECT COUNT(*), empmed, emppro, emprem, emprip, empdgn, empran, emptde, empnom "
         ."     FROM ".$wbasedato."_000024 "
         ."    WHERE empcod = (mid('".$wempresa."',1,instr('".$wempresa."','-')-1)) "
         ."      AND emptem = '".$wtipcli."'"
         ."      AND empest = 'on' "
         ."    GROUP BY 2,3,4,5 ";
      $res1 = mysql_query($q,$conex);
      $num1 = mysql_num_rows($res1);
      $row1 = mysql_fetch_array($res1);

	  $load_ajax1 = 0;
      if ($row1[0] > 0)
         {
	      if($row1[1]=='on') $load_ajax1 = 1;    //Si pide medico
	      if($row1[2]=='on') $load_ajax1 = 1;    //Si pide programa de afiliacion
	      if($row1[3]=='on') $load_ajax1 = 1;    //Si pide remitente
	      if($row1[4]=='on') $load_ajax1 = 1;    //Si genera RIPS
	      if($row1[5]=='on') $load_ajax1 = 1;    //Si pide Diagnostico
	      if($row1[6]=='on') $load_ajax1 = 1;    //Si pide rango del usuario
	      if($row1[7]=='on') $load_ajax1 = 1;    //Tipo de Despacho (E)nfermedad general, (A)ccidente de Trabajo. Esto solo para Colsubsidio
	     }
     }

	 if(isset($wload))
		$wload++;
	 else
		$wload = 0;

	if(isset($load_ajax1) && $load_ajax1==1)
		echo "<input type='hidden' name='wload' id='wload' value='$wload'>";

  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	echo "<input type='hidden' name='wmensajero' value='$wmensajero'>";
	echo "<input type='hidden' name='wpdepac' value='$wpdepac'>";
	echo "<input type='hidden' name='wvdepac' value='$wvdepac'>";
	echo "<input type='hidden' name='wlinpac' value='$wlinpac'>";
	echo "<input type='hidden' name='wacc' value='$wacc'>";

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //ACA EVALUO CUANDO SE HACE LA VENTA
  if (!isset($wventa) or ($wventa=="N"))
     {
	  if($consultaAjax2!='envio')
	  {
	  echo "<tr><td align=center colspan=10 class='encabezadoTabla'><b>* * *  BUSQUEDA DE ARTICULOS * * *</b></td></tr>";
	  echo "<tr>";
	  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      //BUSQUEDA POR CODIGO O DESCRIPCION
	  if (isset($wcons) && $wcons=='desart')
	  {
		$checkcod = '';
		$checkdes = 'checked';
	  }
	  else
	  {
		$checkcod = 'checked';
		$checkdes = '';
	  }
	  if ($wtiping=="C")   //Evaluo si el ingreso de articulos o busqueda se hace por codigo o descripcion
	     {
	      echo "<td class=".$wcf2." colspan='2'><b><font text color=".$wclfa."> Codigo      </font></b><input type='radio' name='wcons' VALUE='codart' ".$checkcod." SIZE=2 ></td>";                //wcons
	      echo "<td class=".$wcf2." colspan='2' ><b><font text color=".$wclfa."> Descripción </font></b><input type='radio' name='wcons' VALUE='desart' ".$checkdes." SIZE=2 ></td>";                        //wcons
	     }
	    else
	       {
	        echo "<td class=".$wcf2." colspan='2'><b><font text color=".$wclfa."> Codigo      </font></b><input type='radio' name='wcons' VALUE='codart' ".$checkcod." SIZE=2 ></td>";                             //wcons
	        echo "<td class=".$wcf2." colspan='2' ><b><font text color=".$wclfa."> Descripción </font></b><input type='radio' name='wcons' VALUE='desart' ".$checkdes." SIZE=2 ></td>";                     //wcons
	       }
	  //Siempre que utilice esta opcion de javascript, se debe cargar la funcion ira() arriba en el BODY
	  ?>
	    <script>
	      function ira(){document.ventas2.wdato.focus();}
	    </script>
	  <?php
	  //echo "<td bgcolor=#fffffff> <INPUT TYPE='text' name='wdato' id='wdato' onkeypress='if (event.keyCode >= 35 & event.keyCode <= 38 )  event.returnValue = false'></td>";                                                                  //wdato


	  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  //====== A C A   E V A L U O   S I   S E   D I G I T A R O N   T O D O S   L O S   D A T O S   O B L I G A T O R I O S =======================
	  //=== No se habilita el campo donde se digita el codigo o la descripcion hasta que se digiten todos los datos obligatorios ===================
	  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  if ($wini=="N")
	     {
		  $whabilita_venta="ENABLED";
	      verifica_datos();
         }

	  if ($whabilita_venta == "DISABLED")
	     {
		  echo "<div align='center' class='fondoRojo' style='width:510px'><b><blink>CAMPO QUE SE DEBE CORREGIR: (** ".$wvaldat." **)</blink></b></div>";
	      $wdato="";
	      echo " <input type='hidden' name='wdato' id='wdato' value='".$wdato."'>";  //Esto lo hago porque no se esta enviando ningun dato en la linea de busqueda
		  //echo "<input type='hidden' name='walert' value='FALTA ALGUN DATO POR INGRESAR O COLOCAR EL DATO CORRECTO'>";
         }
	  //============================================================================================================================================
	  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	  echo "<td class=".$wcf2."> <INPUT TYPE='text' name='wdato' id='wdato' ".$whabilita_venta." SIZE=40 onkeypress='if (validar(event)) submit_form2(\"wcan\")'></td>";                                   //wdato

	  if (!isset($wdato) or ($wdato == ""))
	     echo "<td align=left class=".$wcf2." colspan=3><input type='button' onclick='submit_form2(\"wcan\")' ".$whabilita_venta." value='Consultar'><input type=button onclick='submit_form(\"off\")' value='Iniciar venta'></td>";                                   //submit

	  echo "</table>";


	    }


	if(isset($consultaAjax2) && $consultaAjax2=='envio')
	 {

		echo "<input type='hidden' name='wmensajero' value='$wmensajero'>";
		echo "<input type='hidden' name='wpdepac' value='$wpdepac'>";
		echo "<input type='hidden' name='wvdepac' value='$wvdepac'>";
		echo "<input type='hidden' name='wlinpac' value='$wlinpac'>";
		echo "<input type='hidden' name='wacc' value='$wacc'>";

	  //ACA ELIMINO EL REGISTRO SELECCIONADO
	  if (isset($wborrar) and ($wborrar == 'S'))
	     {
		  //Busco si el articulo a borrar corresponde a un copago
		  $q = " SELECT gruabo, temtot "
		      ."   FROM ".$wbasedato."_000034, ".$wbasedato."_000001, ".$wbasedato."_000004 "
		      ."  WHERE ".$wbasedato."_000034.id          = ".$wid
		      ."    AND temart                            = artcod "
		      ."    AND mid(artgru,1,instr(artgru,'-')-1) = grucod ";
		  $res = mysql_query($q,$conex);
		  $row = mysql_fetch_array($res);
		  if ($row[0]=='on')
		     {
			  $wcuotamod=$wcuotamod-$row[1];

			  //=====================================================
              //Modifico el campo wcuotamod en linea
              //=====================================================
              echo '<script language="Javascript">';
              echo 'document.ventas.wcuotamod.value='.$wcuotamod.";";
			  echo '</script>';
			  //=====================================================
			 }

			  //Valido si el registro que esta seleccionando tiene descuentos mayores asociados al articulo,
			  //si es asi no lo deja eliminar y muestra mensaje diciendo que debe eliminar el de mas alto descuento.

			  $q_tem3 =     " SELECT temart "
						   ."   FROM ".$wbasedato."_000034"
					       ."  WHERE id = '".$wid."'";
			  $res_tem3 = mysql_query($q_tem3,$conex);
			  $row_tem3 = mysql_fetch_array($res_tem3);

			  $q_tem1 =   " SELECT max(temnde) as nivel_maximo "
					    ."   FROM ".$wbasedato."_000034"
					    ."  WHERE temfec = '".$wfecha_tempo."'"
						."    AND temhor = '".$whora_tempo."'"
						."	  AND temart = '".$row_tem3['temart']."'"
						."    AND temnde > 0";
			  $res_tem1 = mysql_query($q_tem1,$conex);
			  $row_tem1 = mysql_fetch_array($res_tem1);
			  $wnivel_max_temp = $row_tem1['nivel_maximo'];


			  $q_tem2 =     " SELECT temnde as nivel_actual "
						   ."   FROM ".$wbasedato."_000034"
					       ."  WHERE id     = '".$wid."'"
						   ."	 AND temart = '".$row_tem3['temart']."'";
			  $res_tem2 = mysql_query($q_tem2,$conex);
			  $row_tem2 = mysql_fetch_array($res_tem2);
			  $wnivel_actual_temp = $row_tem2['nivel_actual'];

			  //Esta validacion verifica que el registro seleccionado no tenga menor nivel que los actuales en la tabla temporal.
			  if($wnivel_actual_temp > 0)
				{
				   if($wnivel_max_temp > $wnivel_actual_temp)
					{
						echo "<div class='articuloControl' align=center>La venta seleccionada no puede ser eliminada, favor seleccione la de mayor descuento para el articulo.</div><br>";
					}
				else
					{

					//Se agrega este update ya que se elimino el ultimo descuento aprobado, para que en la proxima compra no acumule la cantidad aprobada.
					$q =  " UPDATE ".$wbasedato."_000034"
						 ."    SET temade = '' "
						 ."  WHERE temusu = '".$wusuario."'"
						 ."    AND temfec = '".$wfecha_tempo."'"
						 ."    AND temhor = '".$whora_tempo."'"
						 ."    AND temsuc = '".$wcco."'"
						 ."    AND temcaj = '".$wcaja."'"
						 ."	   AND temart = '".$row_tem3['temart']."'";
				 	$res_lin = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

					 //Se elimina el registro con el id respectivo.
					 $q = "  DELETE FROM ".$wbasedato."_000034 "
						."   WHERE id = ".$wid;
					 $res = mysql_query($q,$conex);

					}

				}
			else
				{

				 $q=	 "  DELETE FROM ".$wbasedato."_000034 "
						."   WHERE id = ".$wid;
				 $res = mysql_query($q,$conex);
				}

	      $wborrar='N';
	     }   //fin del if $wborrar

	  if (isset($wempresa))
	     {
		  $wempresa1=explode("-",$wempresa);

		  $wemp=$wempresa1[0];
	      $wnitemp=$wempresa1[1];
		  echo "<input type='HIDDEN' name='wemp' value='".$wemp."'>";
	     }

	  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  //====== A C A   E V A L U O   S I   S E   D I G I T A R O N   T O D O S   L O S   D A T O S   O B L I G A T O R I O S =======================
	  //=== No se habilita el campo donde se digita el codigo o la descripcion hasta que se digiten todos los datos obligatorios ===================
	  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  if ($wini=="N")
	     {
		  $whabilita_venta="ENABLED";
	      verifica_datos();
         }

	  if ($whabilita_venta == "DISABLED")
	     {
		  echo "<div align='center' class='fondoRojo' style='width:510px'><b><blink>CAMPO QUE SE DEBE CORREGIR: (** ".$wvaldat." **)</blink></b></div>";
	      $wdato="";
	      echo " <input type='hidden' name='wdato' id='wdato' value='".$wdato."'>";  //Esto lo hago porque no se esta enviando ningun dato en la linea de busqueda
		  //echo "<input type='hidden' name='walert' value='FALTA ALGUN DATO POR INGRESAR O COLOCAR EL DATO CORRECTO'>";
         }
	  //============================================================================================================================================

	  //$wpuntos="N";

	  if ($wini == "S")  //'S' Indica que se esta iniciando una venta
		 {
		  $wfecha_tempo=$wfecha;
		  $whora_tempo=$hora;
		  $wpagook=0;           //Para indicar si la venta se hace con descuento por Nomina o NO   0:No 1:Si
		  $wprestamo=0;
		  $wchequeo="off";
		  //$whabilita_venta="ENABLED";
		  $whabilita_venta="";

		  $wpuntos="N";


		  //include_once("/pos/cierre.php");    //Se hace el cierre en la primera venta del mes siguiente

		  $wfecha_bor=date("Y-m-d");

		  //=============================================================================
		  //BORRO LOS REGISTROS DE LA TABLA DE VENTAS TEMPORALES
		  //=============================================================================
		  $q = "  DELETE FROM ".$wbasedato."_000034 "
			  ."   WHERE temfec <= str_to_date(ADDDATE('".$wfecha_bor."',-2),'%Y-%m-%d')";
		  $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		  //=============================================================================


		  //Esto lo hago para indicar que la venta anterior ya termino, entonces inicializo las siguientes variables
		  if (isset($wterm_vta) and ($wterm_vta=="S"))
			 {
			  unset($wcarpun);
			  unset($wtipcli);
			  unset($wlotven);
			  unset($wempresa);
			  unset($wdocpac);
			  unset($wnompac);
			  unset($wte1pac);
			  unset($wdirpac);
			  unset($wmaipac);
			  unset($wcuotamod);
			  unset($wtipven);
			  unset($wtipfac);
			  unset($wmensajero);
			  unset($wdesemp);
			  unset($wdesart);
			  unset($wrecemp);
			  unset($wtotdes);
			  unset($wtotrec);
			  unset($wbondto);
			 }
		 }
		else
		  {
		   echo "<input type='HIDDEN' id='wfecha_tempo' name= 'wfecha_tempo' value='".$wfecha_tempo."'>";
		   echo "<input type='HIDDEN' id='whora_tempo' name= 'whora_tempo' value='".$whora_tempo."'>";
		  }

	  echo "<input type='HIDDEN' name='wpagook' value='".$wpagook."'>";
	  echo "<input type='HIDDEN' name='whabilita_venta' value='".$whabilita_venta."'>";

	 echo "<center><table border=0 width='950'>";
	  //////////////////////////////////////////////////////////////////////////////////////////////
	  //ACA TRAIGO LOS ARTICULOS QUE TENGAN TARIFA EN EL CONCEPTO DE VENTAS
	  if (isset($wcons) and !isset($wcan) and $wdato != "")
	     {
		  if ($wcons == "codart")
		     {
			  //==============================================================================================================
			  //VERIFICO QUE EL CODIGO DIGITADO SEA EXTERNO O NO  ============================================================
	          $q= "  SELECT axpart "
	             ."    FROM ".$wbasedato."_000009 "
	             ."   WHERE axpcpr = '".$wdato."'"
	             ."     AND axpest = 'on' ";
	          $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		      $num = mysql_num_rows($res);
		      if ($num > 0)    //Si entra aca es porque el codigo digitado es externo. Entonces traigo el interno
		         {
			      $row = mysql_fetch_array($res);

			      $pos = strpos($row[0],"-");
	              $wdato = substr($row[0],0,$pos);
			     }
			  //==============================================================================================================

			  //==============================================================================================================
			  //AVERIGUO SI EL ARTICULO DIGITADO PERTENECE A UN GRUPO QUE MUEVA INVENTARIOS O NO
			  $q= "  SELECT gruinv, grumva "
	             ."    FROM ".$wbasedato."_000001, ".$wbasedato."_000004 "
	             ."   WHERE artcod = '".$wdato."'"
	             ."     AND mid(artgru,1,instr(artgru,'-')-1) = grucod "
	             ."     AND artest = 'on' "
	             ."     AND gruest = 'on' ";
	          $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		      $num = mysql_num_rows($res);

		      if ($num > 0)    //Si entra aca es porque el codigo digitado es externo. Entonces traigo el interno
		         {
			      $row = mysql_fetch_array($res);
			      $WMUEINV = $row[0];
			      $WMODVAL = $row[1];
			     }
			    else
			       {
			        $WMUEINV="";
			        $WMODVAL="";
		           }
			  //==============================================================================================================


			  //==============================================================================================================
			  //==============================================================================================================
			  if (strtoupper($WMUEINV) == 'ON')
			     {
				  $q =  " SELECT artcod, artnom, mtavac, mtavan,  karexi, mtafec, artiva, artrec, artfre "
				       ."   FROM ".$wbasedato."_000001, ".$wbasedato."_000026, ".$wbasedato."_000024, ".$wbasedato."_000007 "
				       ."  WHERE artcod                            = '".$wdato."'"
				       ."    AND artcod                            = mid(mtaart,1,instr(mtaart,'-')-1) "
				       ."    AND mid(mtatar,1,instr(mtatar,'-')-1) = mid(emptar,1,instr(emptar,'-')-1) "
				       ."    AND empcod                            = '".$wemp."'"
				       ."    AND karcco                            = '".$wcco."'"
				       ."    AND mid(mtacco,1,instr(mtacco,'-')-1) = '".$wcco."'"
				       ."    AND karcod                            = artcod "
				       ."    AND artest                            = 'on' "
				       ."    AND mtaest                            = 'on' "
				       ."    AND emptem                            = '".$wtipcli."'"
				       ."  ORDER BY artcod ";
			     }
			    else
			       {
				    if (strtoupper($WMODVAL)=="N")  //Si el valor es fijo osea por tarifa
					    $q =  " SELECT artcod, artnom, mtavac, mtavan, 'serv', mtafec, artiva, artrec, artfre "
					         ."   FROM ".$wbasedato."_000001, ".$wbasedato."_000026, ".$wbasedato."_000024 "
					         ."  WHERE artcod                            = '".$wdato."'"
					         ."    AND artcod                            = mid(mtaart,1,instr(mtaart,'-')-1) "
					         ."    AND mid(mtatar,1,instr(mtatar,'-')-1) = mid(emptar,1,instr(emptar,'-')-1) "
					         ."    AND empcod                            = '".$wemp."'"
					         ."    AND mid(mtacco,1,instr(mtacco,'-')-1) = '".$wcco."'"
					         ."    AND artest                            = 'on' "
					         ."    AND mtaest                            = 'on' "
					         ."    AND emptem                            = '".$wtipcli."'"
					         ."  ORDER BY artcod ";
					   else  //El codigo no tiene tarifa
					      $q =  " SELECT artcod, artnom, 0, 0, 'serv', 0, artiva "
					           ."   FROM ".$wbasedato."_000001 "
					           ."  WHERE artcod                            = '".$wdato."'"
					           ."    AND artest                            = 'on' "
					           ."  ORDER BY artcod ";
			       }
			 }
	      if ($wcons == "desart")
		     {
			  $q =  " SELECT artcod, artnom, mtavac, mtavan, karexi, mtafec, artiva, artrec, artfre "
			       ."   FROM ".$wbasedato."_000001, ".$wbasedato."_000026, ".$wbasedato."_000024, ".$wbasedato."_000007, ".$wbasedato."_000004 "
			       ."  WHERE artnom                            like '%".$wdato."%'"
			       ."    AND artcod                            = mid(mtaart,1,instr(mtaart,'-')-1) "
			       ."    AND mid(mtatar,1,instr(mtatar,'-')-1) = mid(emptar,1,instr(emptar,'-')-1) "
			       ."    AND empcod                            = '".trim($wemp)."'"
			       ."    AND karcco                            = '".$wcco."'"
			       ."    AND mid(mtacco,1,instr(mtacco,'-')-1) = '".$wcco."'"
			       ."    AND karcod                            = artcod "
			       ."    AND artest                            = 'on' "
			       ."    AND mtaest                            = 'on' "
			       ."    AND emptem                            = '".$wtipcli."'"
			       ."    AND mid(artgru,1,instr(artgru,'-')-1) = grucod "
			       ."    AND gruinv                            = 'on' "

				   ."  UNION "

				   ." SELECT artcod, artnom, mtavac, mtavan, 'serv', mtafec, artiva, artrec, artfre "
			       ."   FROM ".$wbasedato."_000001, ".$wbasedato."_000026, ".$wbasedato."_000024, ".$wbasedato."_000004 "
			       ."  WHERE artnom                            like '%".$wdato."%'"
			       ."    AND artcod                            = mid(mtaart,1,instr(mtaart,'-')-1) "
			       ."    AND mid(mtatar,1,instr(mtatar,'-')-1) = mid(emptar,1,instr(emptar,'-')-1) "
			       ."    AND empcod                            = '".trim($wemp)."'"
			       ."    AND mid(mtacco,1,instr(mtacco,'-')-1) = '".$wcco."'"
			       ."    AND artest                            = 'on' "
			       ."    AND mtaest                            = 'on' "
			       ."    AND emptem                            = '".$wtipcli."'"
			       ."    AND mid(artgru,1,instr(artgru,'-')-1) = grucod "
			       ."    AND gruinv                            = 'off' "
			       ."  ORDER BY artnom ";
			 }
		  $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		  $num = mysql_num_rows($res);

		  if ($num > 0) //El articulo existe y tiene tarifa, entra por el then
		     {
			  echo "</table>";
			  echo "<table align=center width='950'>";
			  echo "<tr>";
			  echo "<td class='fila1' align=center valign=bottom><select name='warticulo'>";                                                //warticulo
			  for ($i=1;$i<=$num;$i++)
			     {
				  $row = mysql_fetch_array($res);

				  //=========================================================================================
				  //Esto lo hago para colocar todas las descripciones del mismo tamaño, osea de 60 caracteres
			      $j= 60-strlen($row[1]);
			      for ($k=1;$k<=$j;$k++)
			          $row[1]=$row[1].'&nbsp';

			      //EL 3 DE AGOSTO SE CAMBIA LA FORMA DE CALCULAR EL IVA DEBIDO A QUE EL VALOR DE LA TARIFA YA LO TIENE INCLUIDO
			      $wporiva = 1+(round($row[6]/100));
			      if ($wfecha < $row[5])   //Aca evaluo si tomo el valor anterior o el actual
			         $wval = $row[3];      //*$wporiva;    //Valor anterior
			        else
			           $wval = $row[2];    //*$wporiva;  //Valor actual
			      //=========================================================================================

			      //=============================================================================================
			      //ACA EVALUO SI EL ARTICULO TIENE RECAMBIO Y SI TODAVIA ESTA VIGENTE EL RECAMBIO SEGUN LA FECHA
			      //=============================================================================================
			      if ($wfecha <= $row[8])      //Aca evaluo si la fecha de recambio esta vigente
			         $wrecambio = $row[7];     //Variable de recambio
			        else
			           $wrecambio = "off";     //Variable de recambio

			      if ($wrecambio == "on")
			         echo "<b><option value='".$row[0]." | ".$row[1]." | "."$ ".number_format($wval,0,'.',',')." | ".$row[4]." | *** TIENE RECAMBIO ***'>".$row[0]." | ".$row[1]." | "."$ ".number_format($wval,0,'.',',')." | ".$row[4]." | *** TIENE RECAMBIO ***</option></b>";
			        else
			           echo "<option value='".$row[0]." | ".$row[1]." | "."$ ".number_format($wval,0,'.',',')." | ".$row[4]."'>".$row[0]." | ".$row[1]." | "."$ ".number_format($wval,0,'.',',')." | ".$row[4]."</option>";
			     }
			  echo "</select></td>";

			  if (isset($WMODVAL) and strtoupper($WMODVAL) == "ON") //No tiene tarifa
				 {
				  ?>
				    <script>
				      function ira(){document.ventas.wvalser.focus();}
				      function ira(){document.ventas.wvalser.select();}
				    </script>
				  <?php
				  echo "<td class=".$wcf."><BLINK>Valor <INPUT TYPE='text' NAME='wvalser' VALUE=1 onkeypress='if (validar(event)) submit_form2()'></td>";    //wcan
			     }


	          //===================================================================================================================
		      //Enero 30 de 2009 ==================================================================================================
			  ///if ($wcons=="codart")
			  ///   {
			      ?>
			        <script>
			          function ira(){document.ventas.wcan.focus();}
			          function ira(){document.ventas.wcan.select();}  //Deja seleccionado el valor por defecto
			        </script>
			      <?php

				  if ($wtipcli!=$wtipoEmpresaParticular && isset($wlotven) && $wlotven=="on")
                  {
					  echo "<td class='fila1'>Cantidad<br /><input type='text' name='wcan' id='wcan' size='10' value='1' onkeypress='if (validar(event)) submit_form2(\"wnumlot\")'></td>";    //wcan

					  echo "<td class='fila1'>Lote<br /><input type='text' name='wnumlot' id='wnumlot' size='10' value='' onkeypress='if (validar(event)) submit_form2(\"wfve\")'></td>";    //wnumlot

					  echo "<td class='fila1'>Fec. vence<br /><input type='text' name='wfve' id='wfve' size='10' value='0000-00-00' onkeypress='if (validar(event)) submit_form2(\"wdato\")'></td>";    //wfve
				  }
				  else
				  {
					  echo "<td class='fila1'>Cantidad<br /><input type='text' name='wcan' id='wcan' size='10' value='1' onkeypress='if (validar(event)) submit_form2(\"wdato\")'></td>";    //wcan

					  echo "<input type='hidden' name='wnumlot' value=''>";
					  echo "<input type='hidden' name='wfve' value=''>";
				  }

				  echo "<td align=center><input type='button' onclick='submit_form2(\"wdato\")' value='OK'></td>";

		      echo "</tr>";
		      echo "</table>";
		      echo "<table width='950'>";

		      $wventa="N";
		      $wdesemp=0;
		      $wrecemp=0;
		      $wdesart=0;
			//$whabilita_venta="ENABLED";
		      mostrar($wusuario,$wfecha_tempo,$whora_tempo,$wcco,$wcaja,$conex,$wini,$wdocpac,$wnompac,$wte1pac,$wdirpac,$wmaipac,$wcol,$wtipcli,$wcuotamod,$wempresa,$wventa,$wtipven,$wmensajero,$wdesemp,$wrecemp,$wdesart,$wpdepac,$wvdepac, $wlinpac, $wemp_pmla, $wacc, $wlotven, $wcan);
		     }
	        else  //Si el articulo no existe o no tiene tarifa para la empresa seleccionada
	           {
		        ///========================================================================================================
		        ///TARIFA DE COBRO POR GRUPO
		        ///========================================================================================================
		        ///Si no encontro tarifa para el articulo busco si existe tarifa o % de utilidad para el grupo del articulo
		        if ($wcons=="codart")
		           {
			        //VERIFICO QUE EL CODIGO DIGITADO SEA EXTERNO O NO  ============================================================
			        $q= "  SELECT axpcpr "
			           ."    FROM ".$wbasedato."_000009 "
			           ."   WHERE axpcpr = '".$wdato."'"
			           ."     AND axpest = 'on' ";
			        $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				    $num = mysql_num_rows($res);
				    if ($num > 0)    //Si entra aca es porque el codigo digitado es externo. Entonces traigo el interno
				       {
					    $row = mysql_fetch_array($res);
			            $wdato=$row[0];
			            $whomolo="S";
		               }
		              else
		                 $whomolo="N";

		            $q= "  SELECT artcod, artnom, (karpro+(karpro*(tgrpac/100))), (karpro+(karpro*(tgrpan/100))), karexi, tgrfec "
				       ."    FROM ".$wbasedato."_000001, ".$wbasedato."_000007, ".$wbasedato."_000027, ".$wbasedato."_000024 "
				       ."   WHERE artcod                             = '".$wdato."'"
				       ."     AND mid(artgru,1,instr(artgru,'-')-1)  = mid(tgrgru,1,instr(tgrgru,'-')-1) "
				       ."     AND empcod                             = '".$wemp."'"
				       ."     AND mid(tgrcod,1,instr(tgrcod,'-')-1)  = mid(emptar,1,instr(emptar,'-')-1) "
				       ."     AND mid(tgrcco,1,instr(tgrcco,'-')-1)  = '".$wcco."'"
				       ."     AND artcod                             = karcod "
				       ."     AND karcco                             = '".$wcco."'"
				       ."     AND tgrest                             = 'on' "
				       ."     AND artest                             = 'on' "
				       ."   ORDER BY artnom ";
			       }
		        if ($wcons=="desart")
		           {
			        $q= "  SELECT artcod, artnom, (karpro+(karpro*(tgrpac/100))), (karpro+(karpro*(tgrpan/100))), karexi, tgrfec "
			           ."    FROM ".$wbasedato."_000001, ".$wbasedato."_000007, ".$wbasedato."_000027, ".$wbasedato."_000024 "
			           ."   WHERE artnom                             like '".$wdato."'"
			           ."     AND mid(artgru,1,instr(artgru,'-')-1)  = mid(tgrgru,1,instr(tgrgru,'-')-1) "
			           ."     AND empcod                             = '".$wemp."'"
			           ."     AND mid(tgrcod,1,instr(tgrcod,'-')-1)  = mid(emptar,1,instr(emptar,'-')-1) "
			           ."     AND mid(tgrcco,1,instr(tgrcco,'-')-1)  = '".$wcco."'"
			           ."     AND artcod                             = karcod "
			           ."     AND karcco                             = '".$wcco."'"
			           ."     AND tgrest                             = 'on' "
			           ."     AND artest                             = 'on' "
			           ."     AND tgrfec                            <= '".$wfecha."'"
			           ."     AND (tgrpac                            > 0 "
			           ."      OR  tgrpan                            > 0) "
			           ."     AND emptem                             = '".$wtipcli."'"
			           ."  ORDER BY artnom ";
		           }
		        $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				$num = mysql_num_rows($res);

				if ($num > 0) //El articulo existe y tiene tarifa, entra por el then
				   {
					echo "</table>";
					echo "<table width='950'>";
					echo "<tr>";
				    echo "<td class='fila1' align=center valign=bottom><select name='warticulo'>";                         //warticulo
				    //echo "<option>&nbsp</option>";


				    for ($i=1;$i<=$num;$i++)
				       {
				         $row = mysql_fetch_array($res);
					     //=========================================================================================
						 //Esto lo hago para colocar todas las descripciones del mismo tamaño, osea de 60 caracteres
					     $j= 60-strlen($row[1]);
					     for ($k=1;$k<=$j;$k++)
					         $row[1]=$row[1].'&nbsp';

					     if ($wfecha < $row[5])   //Aca evaluo si tomo el valor anterior o el actual
				            $wval = $row[3];      //Valor anterior
				           else
				              $wval = $row[2];    //Valor actual
					     //=========================================================================================
					     echo "<option value='".$row[0]." | ".$row[1]." | "."$ ".number_format($wval,2,'.',',')." | ".$row[4]."'>".$row[0]." | ".$row[1]." | "."$ ".number_format($wval,2,'.',',')." | ".$row[4]."</option>";
					   }
					echo "</select></td>";

					?>
				      <script>
				        function ira(){document.ventas.wcan.focus();}
				        function ira(){document.ventas.wcan.select();}
				      </script>
				    <?php
					if ($wtipcli!=$wtipoEmpresaParticular && isset($wlotven) && $wlotven=="on")
					{
						echo "<td class='fila1'>Cantidad <input type='text' name='wcan' id='wcan' size='10' value='1' onkeypress='if (validar(event)) submit_form2(\"wnumlot\")'></td>";    //wcan

						echo "<td class='fila1'>Lote<br /><input type='text' name='wnumlot' id='wnumlot' size='10' value='' onkeypress='if (validar(event)) submit_form2(\"wfve\")'></td>";    //wnumlot

						echo "<td class='fila1'>Fec. vence<br /><input type='text' name='wfve' id='wfve' size='10' value='0000-00-00' onkeypress='if (validar(event)) submit_form2(\"wdato\")'></td>";    //wfve
					}
					else
					{
						echo "<td class='fila1'>Cantidad <input type='text' name='wcan' id='wcan' size='10' value='1' onkeypress='if (validar(event)) submit_form2(\"wdato\")'></td>";    //wcan

						echo "<input type='hidden' name='wnumlot' value=''>";
						echo "<input type='hidden' name='wfve' value=''>";
					}

				    echo "<td align=center><input type='button' onclick='submit_form2()' value='OK'></td>";                            //submit
				    echo "</tr>";
				    //echo "</table>";

				    $wventa="N";
				    $wdesemp=0;
		            $wrecemp=0;
		            $wdesart=0;
		            mostrar($wusuario,$wfecha_tempo,$whora_tempo,$wcco,$wcaja,$conex,$wini,$wdocpac,$wnompac,$wte1pac,$wdirpac,$wmaipac,$wcol,$wtipcli,$wcuotamod,$wempresa,$wventa,$wtipven,$wmensajero,$wdesemp,$wrecemp,$wdesart,$wpdepac,$wvdepac, $wlinpac, $wemp_pmla, $wacc, $wlotven, $wcan);
			       }
			      else
			         {
				      //===========================================================================================
				      //Aca hago la busqueda del motivo por el cual NO sale el articulo al momento de irlo a vender
				      //===========================================================================================
				      if ($wcons=="codart")
		                 {
			              $q =  " SELECT count(*) "
						       ."   FROM ".$wbasedato."_000001 "
						       ."  WHERE artcod = '".$wdato."'"
						       ."    AND artest = 'on' ";
						  $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
						  $num = mysql_num_rows($res);
					      $row = mysql_fetch_array($res);

					      echo "<table>";
					      echo "<tr>";
					      if ($row[0] == 0)
					         if ($whomolo == "S")
						        echo "<td class='articuloControl' colspan=".($wcol-5).">El Articulo No existe o Esta inactivo en el Maestro de Articulos</TD>";
						       else
						          echo "<td class='articuloControl' colspan=".($wcol-5).">El Articulo No ha sido homologado</TD>";
					        else
						       {
							    $q =  " SELECT count(*) "
						             ."   FROM ".$wbasedato."_000026, ".$wbasedato."_000024 "
						             ."  WHERE mid(mtaart,1,instr(mtaart,'-')-1) = '".$wdato."'"
						             ."    AND mid(mtatar,1,instr(mtatar,'-')-1) = mid(emptar,1,instr(emptar,'-')-1) "
						             ."    AND empcod                            = '".trim($wemp)."'"
						             ."    AND mid(mtacco,1,instr(mtacco,'-')-1) = '".trim($wcco)."'"
						             ."    AND mtaest                            = 'on' "
						             ."    AND emptem                            = '".$wtipcli."'";
							    $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
							    $num = mysql_num_rows($res);
							    $row = mysql_fetch_array($res);

							    if ($row[0] == 0)
						           echo "<td class='articuloControl' colspan=".($wcol-5).">El Articulo No tiene tarifa para la sucursal y responsable seleccionado</TD>";
						          else
						             {
						              $q =  " SELECT count(*) "
						                   ."   FROM ".$wbasedato."_000007, ".$wbasedato."_000024 "
							               ."  WHERE karcod = '".$wdato."'"
							               ."    AND karcco = '".trim($wcco)."'"
							               ."    AND karexi > 0 ";
							          $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
							          $num = mysql_num_rows($res);
							          $row = mysql_fetch_array($res);
							          if ($row[0] == 0)
						                 echo "<td class='articuloControl' colspan=".($wcol-5).">El Articulo No tiene existencias en esta sucursal</TD>";
						             }
					           }
					      }
						else
							{
							echo "<p class='articuloControl'>La descripción ingresada no tiene coincidencias</p>";
							}
				      //===========================================================================================
			          echo "<td align=center><input type='button' onclick='submit_form2(\"wdato\")' value='OK'></td>";                        //submit
			          echo "</table>";
		              echo "<table width='700'>";
			          $wventa="N";
			          $wdesemp=0;
		              $wrecemp=0;
		              $wdesart=0;
		              mostrar($wusuario,$wfecha_tempo,$whora_tempo,$wcco,$wcaja,$conex,$wini,$wdocpac,$wnompac,$wte1pac,$wdirpac,$wmaipac,$wcol,$wtipcli,$wcuotamod,$wempresa,$wventa,$wtipven,$wmensajero,$wdesemp,$wrecemp,$wdesart,$wpdepac,$wvdepac,$wlinpac, $wemp_pmla, $wacc, $wlotven, $wcan);
		             }
	           }
		 }
	   else
	       //===========================================================================================================================
	       //===========================================================================================================================
	       //ACA ESTAN LOS DATOS SETIADOS
	       //===========================================================================================================================
	       //===========================================================================================================================
		   {
			echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
			//echo "<input type='HIDDEN' name='wmueinv' value='".$WMUEINV."'>";

			if (isset($warticulo))
			   {

				//Verifico si el articulo tiene descuento escalonado
				$datos_art = explode("|", $warticulo);
				$wcod_art = trim($datos_art[0]);
				$wvalor_art = trim($datos_art[2], '$ ');
				$wvalor_art = str_replace(',','',$wvalor_art);
				$wsaldo_art = trim($datos_art[3]);
				$validar_dsct_esc_art =  validar_dsct_esc_art($wcod_art, $wdocpac);
				//Si el articulo tiene asociados descuentos escalonados, mostrará el div flotante para registrar al usuario, si no esta registrado y si es diferente a 9999
				if ($validar_dsct_esc_art['cuantos'] > 0 and $validar_dsct_esc_art['usuario'] == 'sin_registro' and $wdocpac != '9999' and $wsaldo_art != 0)
					{
					echo "<div id='registrar_linea_vital' align='right' style='cursor:pointer;border:2px solid #E2B709;background-color: #FFFFFF;display:;width:auto;position: fixed;'>
									<img onClick='cerrar_flotante_articulo();' style='width:11px;height:11px;' src='../../images/medical/eliminar1.png' title='Cerrar Opciones'>
								   <p align=center><b>PROGRAMA DESCUENTOS <br> ESCALONADOS</b></p>
								  <p align=left>Laboratorio: <u>".$validar_dsct_esc_art['nombre_lab']."</u><br>Artículo: ".$validar_dsct_esc_art['nombre_art']."</p>
								  <center><A HREF='javascript:' onclick='mostrar_afiliar();'>Afiliar usuario</A><center>
								  <div id='ingreso_codigo' style='display:none;'><p><b>Código de activación:</b><input type=text id='codigo_autorizacion'><b></p><p>Ref. Autorización:</b> <input type=text id='ref_autorizacion'></p><p align=center><input type=button value='OK' onclick='afiliar_usuario(\"".$wemp_pmla."\", \"".$wbasedato."\", \"".$validar_dsct_esc_art['nit_lab']."\", \"".$wusuario."\", \"".$wcod_art."\", \"".$wvalor_art."\", \"".$wcan."\");'></p></div>
						</div>";

					}

				//Se consultan los laboratorios asociados al paciente y se imprimen en pantalla, en este caso permitirá que el usuario .
				$wlaboratorios_asociados = laboratorios_usuario($wdocpac);
				if($wlaboratorios_asociados['cuantos'] > 0)
					{

					/*  echo '<div id="mas_descuentos" align="right" style="cursor:pointer;border:2px solid #E2B709;background-color: #FFFFFF;display:;width:auto;position: fixed;">
							<img onClick="cerrar_flotante_aprob();" style="width:11px;height:11px;" src="../../images/medical/eliminar1.png" title="Cerrar Opciones">
							<p align=left><b>LABORATORIOS ASOCIADOS:</b></p>'.$wlaboratorios_asociados['laboratorios'].'
						  <center><A HREF="javascript:" onclick="mostrar_aprobacion();">Aprobar descuentos adicionales</A><center>
							  <div id="ingreso_cod_aprueba" style="display:none;"><p><b>Codigo de aprobación:</b><br><input type=text id="cod_aprobacion"></p><p align=center><input type=button value="OK" onclick="aprobar_descuentos(\''.$wemp_pmla.'\', \''.$wbasedato.'\', \''.$validar_dsct_esc_art["nit_lab"].'\', \''.$wusuario.'\', \''.$wcod_art.'\', \''.$wvalor_art.'\', \''.$wcan.'\', \''.$wdocpac.'\');"></p>
						  </div>'; */

					}

				$pos = strpos($warticulo,"|");
		        $wart = substr($warticulo,0,$pos-1);

		        if (isset($wprog) and $wprog=="on")
		           {
			        echo "<script>window.open('buscar_ventas_anteriores.php?wbasedato=".$wbasedato."&wdocpac=".$wdocpac."&wart=".$wart."&wfecha=".$wfecha."','','height=400,width=600, top=200 left=200,scrollbars=yes')</script>";
	               }

		        ////////////////////////////////////////
				////////////////////////////////////////
				if (isset($wbondto) and $wbondto != "NO APLICA - NO APLICA")
				   {
				    $wbondto1=explode("-",$wbondto);

				    //ACA BUSCO SI EL BONO TIENE DESCUENTO
				    $q = " SELECT linea, sublinea, descuento, recargo "
				        ."   FROM ".$wbasedato."_000047 "
				        ."  WHERE mid(bono,1,instr(bono,'-')-1) = '".trim($wbondto1[0])."'"
				        ."    AND fecha_ini <= '".$wfecha."'"
				        ."    AND fecha_fin >= '".$wfecha."'"
				        ."    AND hora_ini  <= '".$hora."'"
				        ."    AND hora_fin  >= '".$hora."'";
				    $res_desc = mysql_query($q,$conex);
				    $num_desc = mysql_num_rows($res_desc);

				    if ($num_desc > 0)
				       {
				        $row_desc = mysql_fetch_array($res_desc);
				        $wlin_bon=$row_desc[0];      //Linea
				        $wsub_bon=$row_desc[1];      //Sublinea
				        $wdes_bon=$row_desc[2];      //Descuento
				        $wrec_bon=$row_desc[3];      //Recargo
				       }
				      else
				         {
				          $wlin_bon="";              //Linea
				          $wsub_bon="";              //Sublinea
				          $wdes_bon=0;               //Descuento
				          $wrec_bon=0;               //Recargo
			             }
				   }

				////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	            //========================================================================================================================\\
	            //SI HAY DESCUENTO POR BONO BUSCO SI HAY ALGUN ARTICULO DE LA VENTA QUE PERTENEZCA A LA LINEA QUE TIENE DESCUENTO         \\
	            //========================================================================================================================\\
	             if (isset($wdes_bon) and $wdes_bon > 0)
	                {
		             if ($wsub_bon != "NO APLICA")
		                $wlinea_bon = substr($wlin_bon,0,strpos($wlin_bon,"-"))."-".substr($wsub_bon,0,strpos($wsub_bon,"-"));
		               else
		                  $wlinea_bon = substr($wlin_bon,0,strpos($wlin_bon,"-"))."%";

		             $q = "SELECT count(*) "
		                 ."  FROM ".$wbasedato."_000001"
		                 ." WHERE artcod = '".$wart."'"       //Articulo
		                 ."   AND artgru like '".$wlinea_bon."'"   //Linea
		                 ."   AND artest = 'on' ";
		             $res_lin = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		             $num_lin = mysql_num_rows($res_lin);

		             $row_lin = mysql_fetch_array($res_lin);

		             if ($row_lin[0] == 0)
		                $wdcto_bon=0;
		               else
		                  $wdcto_bon=$wdes_bon/100;
	                }
	               else
	                  $wdcto_bon=0;

				////////////////////////////////////////
				////////////////////////////////////////

				$wini="N";
				echo "<input type='HIDDEN' name= 'wini' value='N'>";                                            //wini
		        echo "<input type='HIDDEN' id= 'wfecha_tempo' name= 'wfecha_tempo' value='".$wfecha_tempo."'>";                    //wfecha_tempo
		        echo "<input type='HIDDEN' id= 'whora_tempo' name= 'whora_tempo' value='".$whora_tempo."'>";                      //whora_tempo
		        echo "<input type='HIDDEN' name= 'wpdepac' value='".$wpdepac."'>";                              //wpdepac
		        echo "<input type='HIDDEN' name= 'wvdepac' value='".$wvdepac."'>";                              //wvdepac
		        echo "<input type='HIDDEN' name= 'wlinpac' value='".$wlinpac."'>";                              //wlinpac

		        //==============================================================================================================
	            //AVERIGUO SI EL ARTICULO DIGITADO PERTENECE A UN GRUPO QUE MUEVA INVENTARIOS O NO
			    $q= "  SELECT gruinv, grumva "
	               ."    FROM ".$wbasedato."_000001, ".$wbasedato."_000004 "
	               ."   WHERE artcod = '".$wart."'"
	               ."     AND mid(artgru,1,instr(artgru,'-')-1) = grucod "
	               ."     AND artest = 'on' "
	               ."     AND gruest = 'on' ";
	            $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		        $num = mysql_num_rows($res);

		        if ($num > 0)    //Si entra aca es porque el codigo digitado es externo. Entonces traigo el interno
		           {
			        $row = mysql_fetch_array($res);
			        $WMUEINV = $row[0];
			        $WMODVAL = $row[1];
			       }
			    //==============================================================================================================

			    if (strtoupper($WMUEINV) == 'ON')
			       {
				    $q =  " SELECT artcod, artnom, unides, mtavac, artiva, karexi, karpro, mtavan, mtafec, emppdt, empprt, mtapde "
					     ."   FROM ".$wbasedato."_000001, ".$wbasedato."_000026, ".$wbasedato."_000024, ".$wbasedato."_000002, ".$wbasedato."_000007 "
					     ."  WHERE artcod                            = '".$wart."'"
					     ."    AND artcod                            = mid(mtaart,1,instr(mtaart,'-')-1) "
					     ."    AND artest                            = 'on' "
					     ."    AND mtaest                            = 'on' "
					     ."    AND unicod                            = mid(artuni,1,instr(artuni,'-')-1) "
					     ."    AND mid(mtacco,1,instr(mtacco,'-')-1) = '".$wcco."'"
					     ."    AND karcco                            = '".$wcco."'"
				         ."    AND karcod                            = artcod "
				         ."    AND karexi                           >= ".$wcan
				         ."    AND empcod                            = '".$wemp."'"
				         ."    AND mid(mtatar,1,instr(mtatar,'-')-1) = mid(emptar,1,instr(emptar,'-')-1) "
				         ."    AND emptem                            = '".$wtipcli."'";
				   }
			      else
			         {
				      if (strtoupper($WMODVAL) == "N")
						  $q =  " SELECT artcod, artnom, unides, mtavac, artiva, 'serv', 0, mtavan, mtafec, emppdt, empprt, mtapde "
						       ."   FROM ".$wbasedato."_000001, ".$wbasedato."_000026, ".$wbasedato."_000024, ".$wbasedato."_000002, ".$wbasedato."_000004 "
						       ."  WHERE artcod                            = '".$wart."'"
						       ."    AND artcod                            = mid(mtaart,1,instr(mtaart,'-')-1) "
						       ."    AND artest                            = 'on' "
						       ."    AND mtaest                            = 'on' "
						       ."    AND unicod                            = mid(artuni,1,instr(artuni,'-')-1) "
						       ."    AND mid(mtacco,1,instr(mtacco,'-')-1) = '".$wcco."'"
						       ."    AND empcod                            = '".$wemp."'"
						       ."    AND mid(mtatar,1,instr(mtatar,'-')-1) = mid(emptar,1,instr(emptar,'-')-1) "
						       ."    AND emptem                            = '".$wtipcli."'"
						       ."    AND mid(artgru,1,instr(artgru,'-')-1) = grucod "
					           ."    AND gruinv                            = 'off' ";
					    else
					       $q = " SELECT artcod, artnom, 0, 0, 'serv', 0, artiva "
						       ."   FROM ".$wbasedato."_000001 "
						       ."  WHERE artnom                            like '%".$wdato."%'"
						       ."    AND artest                            = 'on' "
						       ."  ORDER BY artnom ";
					 }


				$res = mysql_query($q,$conex); //or die (mysql_errno()." - ".mysql_error());
			    $num = mysql_num_rows($res);   //or die (mysql_errno()." - ".mysql_error());

			    if ($num > 0)
			       {
				    $row = mysql_fetch_array($res);
			        //$wart    = $row[0];
			        $wdes    = $row[1];
			        $wuni    = $row[2];
			        $wvac    = $row[3];
			        $wporiva = $row[4];
			        $wcospro = $row[6];
			        if (strtoupper($WMUEINV) == 'ON' or strtoupper($WMODVAL) == "N")
			           {
				        $wvan    = $row[7];
				        $wfeccam = $row[8];
				        $wdesemp = ($row[9]/100);
		                $wrecemp = ($row[10]/100);
		                $wdesart = ($row[11]/100);
	                   }

			        if ($wfecha < $wfeccam)   //Aca evaluo si tomo el valor anterior o el actual
			           $wval = $wvan;
			          else
			             $wval = $wvac;

			        //Si el valor si digito entonces lo tomo como el valor a cobrar
			        if (isset($wvalser) and ($wvalser > 0))
			           $wval=$wvalser;

			        //////////////////////////////////////////////////////////////////////////////////////////////////////////////
			        //CALCULO DEL IVA ============================================================================================
			        //EL 3 DE AGOSTO SE CAMBIA LA FORMA DE CALCULAR EL IVA DEBIDO A QUE EL VALOR DE LA TARIFA YA LO TIENE INCLUIDO
			        //$wvaliva = (integer)($wcan*$wval*($wporiva/100));
			        //$wvaltot = (integer)(($wcan*$wval)+($wcan*$wval*($wporiva/100)));
			        if ($wporiva > 0)
			     	   $wvaliva = round((($wcan*$wval)-(($wcan*$wval)/(1+($wporiva/100)))));
			     	  else
			     	     $wvaliva=0;
			     	$wvaltot = round(($wcan*$wval));

			     	//Verifico que el descuento que viene en $wpdepac si corresponde a la linea del articulo
			        if (isset($wpdepac) and $wpdepac > 0)
			           {
				        $q = " SELECT COUNT(*) "
				            ."   FROM ".$wbasedato."_000001, ".$wbasedato."_000042, ".$wbasedato."_000041 "
				            ."  WHERE artcod                             = '".$wart."'"
				            ."    AND mid(artgru,1,instr(artgru,'-')-1) in (".$wlinpac.") "
				            ."    AND clelin                            in (".$wlinpac.") "
						    ."    AND clefid                            <= '".$wfecha."'"
				            ."    AND cleffd                            >= '".$wfecha."'"
				            ."    AND clidoc                             = '".$wdocpac."'"
				            ."    AND clitip                             = clecla "
				            ."    AND cleest                             = 'on' ";
					    $resdpa = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
						$rowdpa = mysql_fetch_array($resdpa) or die (mysql_errno()." - ".mysql_error());

						if ($rowdpa[0] <= 0)  //Quiere decir que el descuento por tipo de cliente no se aplica para el articulo actual
						   $wpdepac=0;
				       }
					//Valida si la cantidad es mayor a cero.
				    if ($wcan > 0)
			           {
						//Si entra por aca es porque ya se valido y por ende puede grabar el articulo en la tabla TEMPORAL
						$q= " INSERT INTO ".$wbasedato."_000034 (Medico          ,   Fecha_data ,   Hora_data,   temusu      ,   temfec           ,   temhor          ,   temsuc  ,   temcaj   ,   temtcl     ,   temres  ,   temdcl     ,   temncl     ,   temart ,    temdes  ,   tempre  ,  temcan ,  temvun ,  tempiv    ,  temiva    ,  temtot     , temcmo      ,  temcpr    ,  temdem    ,  temrem    ,  temdar    ,  temdpa    ,  tembpa    ,  temdbo      , temrbo      ,    temcpu     ,   temlpa,  temlot,  temfve,   Seguridad) "
						   ."                            VALUES ('".$wbasedato."','".$wfecha."' ,'".$hora."' ,'".$wusuario."','".$wfecha_tempo."' ,'".$whora_tempo."' ,'".$wcco."','".$wcaja."','".$wtipcli."','".$wemp."','".$wdocpac."','".$wnompac."','".$wart."','".$wdes."','".$wuni."',".$wcan.",".$wval.",".$wporiva.",".$wvaliva.",".$wvaltot.",".$wcuotamod.",".$wcospro.",".$wdesemp.",".$wrecemp.",".$wdesart.",".$wpdepac.",".$wvdepac.",".$wdcto_bon.",0            , '".$wcarpun."','".$wlinpac."', '".$wnumlot."','".$wfve."', 'C-".$wusuario."')";
						$res2 = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
						}

					//ACA TRAIGO TODO LO QUE HAY PENDIENTE DE FACTURAR EN ESTE CAJA PAR PARA EL CONTROL DE LOS DESCUENTOS ESCALONADOS.
				   $q_temp = " SELECT * "
							 ."   FROM ".$wbasedato."_000034, ".$wbasedato."_000001, ".$wbasedato."_000004 "
							 ."  WHERE temusu                            = '".$wusuario."'"
							 ."    AND temfec                            = '".$wfecha_tempo."'"
							 ."    AND temhor                            = '".$whora_tempo."'"
							 ."    AND temsuc                            = '".$wcco."'"
							 ."    AND temcaj                            = '".$wcaja."'"
							 ."    AND temart                            = artcod "
							 ."    AND mid(artgru,1,instr(artgru,'-')-1) = grucod "
							 ."  ORDER BY ".$wbasedato."_000034.id ";
					$res_temp = mysql_query($q_temp,$conex) or die (mysql_errno()." - ".mysql_error());

					$arr_datos_temp = array();

					//Cargo el array $arr_datos_temp con la informacion del pendiente por facturar.
					while($row_temp = mysql_fetch_assoc($res_temp))
					{

						if(!array_key_exists($row_temp['Temart'], $arr_datos_temp))
						{
							$arr_datos_temp[$row_temp['Temart']] = $row_temp;
						}else{
							$arr_datos_temp[$row_temp['Temart']]['Temcan'] += $row_temp['Temcan'];
							$arr_datos_temp[$row_temp['Temart']]['Temtot'] += $row_temp['Temcan']*$row_temp['Temvun'];
						}
					}

					$wcontrol_segundos = 0;
					$wcontrol_compras = '';

					foreach( $arr_datos_temp as $wcod_arti => $wdatostemp ){

						$wcan = $wdatostemp['Temcan']; //Para que cree los paquetes con la cantidad total del articulo
						//Traigo algunos datos del articulo en relacion con cedula del cliente.
						$descuento_escalonado = verif_desc_escalonado($wdatostemp['Temdcl'], $wdatostemp['Temart']);
						//Valido cual de los articulos en la temporal tiene descuento escalonado
						if($descuento_escalonado['activo'] == 'condescuento' and $wdatostemp['Temade'] !='ok' and $descuento_escalonado['cajaspormes'] > 0)
						{
							//Valor proveniente de la tabla root_000051 para controlar cuantos tratamientos maximo se le pueden vender al cliente.
							$wcantidad_descuentos_aplicables_por_compra = consultarAliasPorAplicacion($conex, $wemp_pmla, "desc_aplicables_por_compra");
							//Se crea un arreglo con la variable cantidad para hace rel control de paquetes.
							$warreglo_cantidad = range(1,$wcan);

							//Este arreglo separa la variable $arreglo_cantidad en paquetes, la variable $wcajasportto controla cuantos pqtes serán.
							$wpaquetes = array_chunk($warreglo_cantidad, $descuento_escalonado['cajaspormes']);

							//Evaluar cuantos niveles lleva y cuando los compró para saber si se le vende 1 o 2 paquetes.
							//Consulto el nivel de descuento actual para el cliente, para poder controlar cuanto se le puede vender.
							$q_con = "SELECT Usuniv, Usufuc"
									."  FROM ".$wbasedato."_000152"
									." WHERE Usuart = '".$wdatostemp['Temart']."'
										 AND Usuced = '".$wdatostemp['Temdcl']."'";
							$err_con = mysql_query($q_con,$conex) or die (mysql_errno()."  ".mysql_error());
							$row_con = mysql_fetch_array($err_con);
							$wnivel_actual = $row_con['Usuniv'];
							$wfecha_ult_compra = $row_con['Usufuc'];
							$wcajasxmes = $descuento_escalonado['cajaspormes'];	//Cajas por tratamiento.
							$wmes_compra = date("m", strtotime($wfecha_ult_compra)); //Mes de la ultima compra.
							$wmes_actual = date("m", strtotime($wfecha));

							//Busca cuantos compras realizó la ultima vez.
							$q_compac =  "SELECT sum(Rdecan)/".$wcajasxmes." as cuantos"
										."  FROM ".$wbasedato."_000169"
										." WHERE Rdedoc = '".$wdatostemp['Temdcl']."'
											 AND Rdeart = '".$wdatostemp['Temart']."'
										GROUP BY Fecha_data, Hora_data
										ORDER BY id desc";
							$err_compac = mysql_query($q_compac,$conex) or die (mysql_errno()."  ".mysql_error());
							$row_compac = mysql_fetch_assoc($err_compac);
							$wttos_ultimas_compras = $row_compac['cuantos'];

							//Busca los datos de la ultima compra en el log de compras.
							$q_datos_log =   "SELECT Rdecan, Rdecpt "
											."  FROM ".$wbasedato."_000169"
											." WHERE Rdedoc = '".$wdatostemp['Temdcl']."'
												 AND Rdeart = '".$wdatostemp['Temart']."'
											ORDER BY id desc";
							$res_datos_log = mysql_query($q_datos_log ,$conex) or die (mysql_errno()."  ".mysql_error());
							$row_datos_log = mysql_fetch_assoc($res_datos_log);
							$wunidad_compradas = $row_datos_log['Rdecan'];
							$wcajasxtto = $row_datos_log['Rdecpt'];

							//Si la cantidad de paquetes es mayor a cero, procesara la informacion del arreglo con los datos de la tabla tmeporal.
							if(count($wpaquetes) > 0)
							{

								//echo "<pre>";
								//print_r($wpaquetes);
								//echo "</pre>";

								//Calculo cuantos cajas hacen falta para completar un tratamiento y si es mayor a cero.
								$wfaltaenpqte = $wcajasxtto-$wunidad_compradas;
								$wfaltaenpqte_aux = abs($wfaltaenpqte - $wcan);
								//Si hace falta entonces proceso de nuevo el arreglo $wpaquetes.
								if($wfaltaenpqte > 0 and $wfaltaenpqte_aux > 0)
								{
								//Cuento cuantos elementos hay en la posicion cero del arreglo de paquetes y se lo resto a la cantidad faltante para completar un tratamiento.
								$wpqte_posicioninicial = count($wpaquetes[0])-$wfaltaenpqte;

								//Creo un areglo con la cantidad de posiciones en la primera posicion.
								$wpqte_posicioninicial_aux = range(1,$wpqte_posicioninicial);

								//Calculo cuanto es el sobrante despues de saber cuantas cajas le hacian falta para completar el tratamiento.
								$wpqtefinal = $wcan - $wpqte_posicioninicial;

								//Creo un arreglo con la cantidad de posiciones restantes.
								$wpqtefinal_aux = range(1,$wpqtefinal);

								//Este arreglo separa la variable $wpqtefinal_aux en paquetes, la variable $descuento_escalonado['cajaspormes'] controla cuantos pqtes serán.
								$wpaquetes_aux = array_chunk($wpqtefinal_aux, $descuento_escalonado['cajaspormes']);

								//Finalmente agrego al inicio del arreglo $wpaquetes_aux, el arreglo $wpqte_posicioninicial_aux.
								 array_unshift($wpaquetes_aux, $wpqte_posicioninicial_aux);

								 //Reasigno el arreglo resultante a la variable $wpaquetes para que sea procesado.
								 $wpaquetes = $wpaquetes_aux;

								 }

								$cantidades = array(); //Cantidades asociadas a cada posicion del array paquetes_registro.
								$niveles = array(); //Cantidad de niveles que se le dara al cliente.
								$desc_art_escalonados = array(); //Porcentajes de descuento.
								$valores_desc_escalonados = array(); //Valores calculados segun el porcentaje de descuento.

								//Aqui se valida si el ultimo consecutivo va en el segundo nivel, si es asi solo le permite comprar un paquete,
								//no pueden haber compras de 2 paquetes seguidos en diferentes compras.
								if($wttos_ultimas_compras > 1 )
								{
									//Valido si compro dos o mas paquetes y el mes es diferente, le de descuento.
									if($wmes_compra != $wmes_actual and $wfecha_ult_compra != '0000-00-00')
									{
									$wcontrol_compras = 'ok';
									}
									else
										{
										$wcontrol_compras = 'no_oka';
										}

									//Si yo compre mas de un paquete en la ultima compra, SIEMPRE se me aplica descuento para UN SOLO PAQUETE en la proxima compra
									$wptei = $wpaquetes[ 0 ];	//Solo un paquete tendra descuento
									array_push( $cantidades, count( $wptei ) );

								}else{

									//Valido que si compra 1 paquete o menos y el mes es diferente, le de descuento.
									if($wmes_compra != $wmes_actual and $wfecha_ult_compra != '0000-00-00')
									{
									$wcontrol_compras = 'no_ok';
									}
									else
										{
										$wcontrol_compras = 'ok';
										}


									//Recorre todos los paquetes creados
									for( $i=0; $i < count($wpaquetes); $i++){
										if( $i < $wcantidad_descuentos_aplicables_por_compra ){	//Solo se le aplica hasta la cantidad de descuentos que determine la variable $wcantidad_descuentos_aplicables_por_compra

											$wptei = $wpaquetes[ $i ];
											$wptei = count($wptei) ;

											//Agrego a la posicion la cantidad resultante.
											array_push( $cantidades, $wptei );
										}
									}
								}


								//Esta validacion identifica si el cliente ya se encuentra en el nivel maximo de descuento, por lo tanto se mantendra el ultimo porcentaje.
								if($wnivel_actual == $descuento_escalonado['nivel_max']){
									foreach( $cantidades as $cantt ){
										array_push( $niveles, $descuento_escalonado['nivel_max'] );
									}
								}else{

										//Consulto si el cliente ya cumplio con la cantidad de cajas de un tratamiento, si no lo ha hecho la variable $indi sera igual a cero, para que al ingresar al foreach aumente en uno.
										if(($wcajasxtto-$wunidad_compradas) > 0)
										{
										$indi=0;
										}
										else
											{
											//Si no esta en el ultimo nivel incrementa 1 al actual.
											$indi=1;
											}

										foreach( $cantidades as $cantiid ){
											$nuev_nivel = $wnivel_actual + $indi;

											//Si llego al nivel maximo se mantiene en él.
											if( $nuev_nivel > $descuento_escalonado['nivel_max'] )
												$nuev_nivel = $descuento_escalonado['nivel_max'];

											array_push( $niveles, $nuev_nivel );
											$indi++;
										}
								}

								//Aqui se valida si el articulo debe volver al primer descuento.
								if($descuento_escalonado['devuelve']=='on')
								{
									$wcontrol_compras = 'ok';
									$niveles = array();

									foreach( $cantidades as $cadnddt ){
										array_push( $niveles, $descuento_escalonado['nivel_devuelve'] );
									}

									$cantidades = array();
									//Si yo compre mas de un paquete en la ultima compra, SIEMPRE se me aplica descuento para UN SOLO PAQUETE en la proxima compra
									$wptei = $wpaquetes[ 0 ];	//Solo un paquete tendra descuento
									array_push( $cantidades, count( $wptei ) );

								}
								$wvaluni = '';
								//Recorro los niveles para calcular los valores de descuento de cada uno.
								for($i=0; $i<count($niveles); $i++){

									$nivelk = $niveles[$i];
									$wdesart1 = buscar_porcentaje( $wdatostemp['Temart'], $nivelk);
									$wdato_porc_valor1 = explode("-", $wdesart1);

									$wdesart_escalo = 0;
									$wvaluni = round($wdatostemp['Temvun']/(($wdatostemp['Tempiv']/100)+1)); //Se calcula el total sin iva.

									if($wdato_porc_valor1[0] == '' or $wdato_porc_valor1[0] == 0){
									  $wdesart_escalo = $wdato_porc_valor1[1]/$wvaluni; //Se cambia de valor a porcentaje sobre el valor del articulo.
									}
									else{
									   $wdesart_escalo = $wdato_porc_valor1[0]/100; //Se cambia el valor a porcentaje.
									}

									array_push( $desc_art_escalonados, $wdesart_escalo );

									$wvalor_descuento_esc = $cantidades[$i]*round($wvaluni*$wdesart_escalo); //El valor de descuento se calcula sobre el valor unitario sin iva.

									array_push( $valores_desc_escalonados, $wvalor_descuento_esc);
								}


								for($i=0; $i< count( $cantidades ); $i++){
									unset($wpaquetes[$i]); //Se elimina la posicion dependiendo de las cantidades en arreglo $wpaquetes,
														   //esto permite que lo restante en el arreglo se calculecomo el sobrante.
								}

							   //Se hace de nuevo esta consulta para extraer la hora del registro que se va a modificar en tabla temporal y controlar
							   //los registros a esa tabla, para no generar error de llaves fecha y hora data.
							   $q_hora_temp =  " SELECT max(".$wbasedato."_000034.Hora_data) as hora_temporal, temade, Temart"
											 ."   FROM ".$wbasedato."_000034, ".$wbasedato."_000001, ".$wbasedato."_000004 "
											 ."  WHERE temusu                            = '".$wusuario."'"
											 ."    AND temfec                            = '".$wfecha_tempo."'"
											 ."    AND temhor                            = '".$whora_tempo."'"
											 ."    AND temsuc                            = '".$wcco."'"
											 ."    AND temcaj                            = '".$wcaja."'"
											 ."    AND temusu                            = '".$wdatostemp['Temusu']."'"
											 ."    AND (temade    =  'ok' OR temade    =  'on')"
											 ."    AND temart                            = artcod "
											 ."    AND mid(artgru,1,instr(artgru,'-')-1) = grucod "
											 ."  GROUP BY temart ORDER BY ".$wbasedato."_000034.id ";
							  $res_hora_temp = mysql_query($q_hora_temp,$conex) or die (mysql_errno()." - ".mysql_error());

							  $arr_datos_temp_aux = array();
							  //En este ciclo se carga el arreglo arr_datos_temp_aux, con
							  while($row_temp_aux = mysql_fetch_assoc($res_hora_temp))
								{
									if(!array_key_exists($row_temp_aux['Temart'], $arr_datos_temp_aux))
									{
										$arr_datos_temp_aux[$row_temp_aux['Temart']] = $row_temp_aux;
									}

								}

							  //Aqui declaro la ultima hora en que se registro en la tabla temporal, si no hay datos, esa variable tendra el favor de la hora actual.
							  if($arr_datos_temp_aux[$wdatostemp['Temart']]['hora_temporal'] == '')
											$arr_datos_temp_aux[$wdatostemp['Temart']]['hora_temporal'] = date('H:i:s');


							   //Borro los registros de la tabla de ventas temporales
							   $q_delete = "   DELETE FROM ".$wbasedato."_000034 "
											."  WHERE temusu = '".$wdatostemp['Temusu']."'"
											." 	  AND temart = '".$wdatostemp['Temart']."'"
											."    AND temfec = '".$wfecha_tempo."'"
											."    AND temhor = '".$whora_tempo."'"
											."    AND temsuc = '".$wcco."'"
											."    AND temcaj = '".$wcaja."'";
							  $res_delete = mysql_query($q_delete,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_delete." - ".mysql_error());

								//Valida si la ultima compra realizada es en el mismo mes, si es asi no le dara descuento, debe ser de diferente mes.
								if($wcontrol_compras == 'ok')
								{

									//En este insert hay un ciclo de valores diferentes en hora y cantidad; en hora para que el indice de fecha/hora/usuario no sea igual y en cantidad para registrar la cantidad de datos de $cantidad[$i], diferenciando los paquetes.
									$q= " INSERT INTO ".$wbasedato."_000034 (Medico          ,   Fecha_data ,   Hora_data,   temusu      ,   temfec           ,   temhor          ,   temsuc  ,   temcaj   ,   temtcl                                 ,   temres  ,                              temdcl     ,   temncl                    ,   temart                ,                   temdes  ,                tempre           ,  temcan ,       temvun                  ,  tempiv    ,                           temiva    ,                           temtot           ,             temcmo      ,          temcpr    ,               temdem        ,    temrem              ,  temdar   ,          temdpa    ,       tembpa               ,  temdbo                 , temrbo      ,    temcpu                   ,          temlpa          ,        temlot,                  temfve,                         temnde,              temves,          tempes, temade,            Seguridad) ";
									$q.=" VALUES ";
									$waumento_seg = 1; //Variable que controla la insercion en la 34 para que no muestre error de llave primaria al insertar varios registros.
									$wcontrol_aprueba = "";

									//Aqui se controla si ya se le dio descuentos adicionales
									if($arr_datos_temp_aux[$wdatostemp['Temart']]['temade']=="ok")
									{
									$wcontrol_aprueba = "ok";
									}

									//Dependiendo de las cantidades en el arreglo $cantidades hará un ciclo insertando la informacion en la temporal.
									for($i=0; $i < count($cantidades); $i++){

										$hora_aux = strtotime($arr_datos_temp_aux[$wdatostemp['Temart']]['hora_temporal'])+$waumento_seg;
										$hora_aux = date('H:i:s',$hora_aux);

										//Con esto controlo la ultima coma, para que no genere error sql.
										if( $i > 0 ) $q.=",";

										$wvaluni = round($wdatostemp['Temvun']/(($wdatostemp['Tempiv']/100)+1)); //Se calcula el total sin iva.
										$wvaloriva = $cantidades[$i]*($wvaluni*($wdatostemp['Tempiv']/100)); //Se calcula el iva.

										//Se entregan los valores para la insercion de los datos
										$q.= " ('".$wbasedato."','".$wfecha."' ,'".$hora_aux."' ,'".$wdatostemp['Temusu']."','".$wfecha_tempo."' ,'".$whora_tempo."' ,'".$wdatostemp['Temsuc']."','".$wdatostemp['Temcaj']."','".$wdatostemp['Temtcl']."','".$wdatostemp['Temres']."','".$wdatostemp['Temdcl']."','".$wdatostemp['Temncl']."','".$wdatostemp['Temart']."','".$wdatostemp['Temdes']."','".$wdatostemp['Tempre']."',".$cantidades[$i].",".$wdatostemp['Temvun'].",".$wdatostemp['Tempiv'].",".$wvaloriva.",".($wdatostemp['Temvun']*$cantidades[$i]).",".$wdatostemp['Temcmo'].",".$wdatostemp['Temcpr'].",".$wdatostemp['Temdem'].",".$wdatostemp['Temrem'].",    0   ,".$wdatostemp['Temdpa'].",".$wdatostemp['Tembpa'].",".$wdatostemp['Temdbo'].",0            , '".$wdatostemp['Temcpu']."','".$wdatostemp['Temlpa']."', '".$wdatostemp['Temlot']."','".$wdatostemp['Temfve']."', '".$niveles[$i]."', '".$valores_desc_escalonados[$i]."' , '".$desc_art_escalonados[$i]."' ,'".$wcontrol_aprueba."','C-".$wusuario."')";

										$waumento_seg++;

									}

									//Aqui se ejecuta el insert con el resultado del anterior ciclo de values.
									$res2 = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

									//=========== Datos Sobrantes ========================
									//Sobrantes en el conteo de paquetes
									//Se declara una hora nueva con un segundo adicional basados en la ultima hora de la tabla, para poder hacer el registro en la tabla 34.
									$hora2 = strtotime($arr_datos_temp_aux[$wdatostemp['Temart']]['hora_temporal'])+$waumento_seg;
									$hora2 = date('H:i:s', ($hora2+1));

									$wsobrantes_key = count($wpaquetes); //Se cuentan las llaves del arreglo final de $wpaquetes.
									$wsobrantes_final = count($wpaquetes, COUNT_RECURSIVE);	//Se cuentan cuantos valores hay en todo el arreglo $wpaquetes.
									$wsobrantes_final = $wsobrantes_final - $wsobrantes_key; //Se restan los dos datos para tener la cantidad final sobrante.

									//Se evalua si la cantidad sobrante en mayor a 0 para que no inserte valores en cero en la tabla temporal y no se muestren mensajes de division por cero.
									if($wsobrantes_final > 0 )
										{

										$wcontrol_aprueba = "on";
										$wvalor_descuento_esc_sobrante = "";
										$wdesart_escalo_sobrante = "";
										$wsig_nivel = "";

										$wvaluni = round($wdatostemp['Temvun']/(($wdatostemp['Tempiv']/100)+1));
										$wvaloriva = $wsobrantes_final*($wvaluni*($wdatostemp['Tempiv']/100));

										//Finalmente se insertan el valor final sobrante sin descuento.
										$q2 = " INSERT INTO ".$wbasedato."_000034 (Medico          ,   Fecha_data ,   Hora_data  ,     temusu                ,   temfec           ,   temhor          ,   temsuc                  ,   temcaj                  ,   temtcl                  ,   temres                  ,   temdcl                  ,   temncl                  ,   temart                  ,    temdes                 ,   tempre                   ,  temcan             ,  temvun                 ,  tempiv                 ,  temiva                 ,  temtot                                    , temcmo                  ,  temcpr                 ,  temdem                 ,  temrem                 ,  temdar  ,  temdpa                 ,  tembpa                 ,  temdbo                 , temrbo      ,    temcpu                  ,   temlpa                  ,  temlot                   ,  temfve                 ,    temnde                                ,   temves                   ,      tempes           ,    temade               ,  Seguridad) "
										   ."                            VALUES ('".$wbasedato."','".$wfecha."' , '".$hora2."' ,'".$wdatostemp['Temusu']."','".$wfecha_tempo."' ,'".$whora_tempo."' ,'".$wdatostemp['Temsuc']."','".$wdatostemp['Temcaj']."','".$wdatostemp['Temtcl']."','".$wdatostemp['Temres']."','".$wdatostemp['Temdcl']."','".$wdatostemp['Temncl']."','".$wdatostemp['Temart']."','".$wdatostemp['Temdes']."','".$wdatostemp['Tempre']."',".$wsobrantes_final.",".$wdatostemp['Temvun'].",".$wdatostemp['Tempiv'].",".$wvaloriva.",".($wsobrantes_final*$wdatostemp['Temvun']).",".$wdatostemp['Temcmo'].",".$wdatostemp['Temcpr'].",".$wdatostemp['Temdem'].",".$wdatostemp['Temrem'].",     0    ,".$wdatostemp['Temdpa'].",".$wdatostemp['Tembpa'].",".$wdatostemp['Temdbo'].",0            , '".$wdatostemp['Temcpu']."','".$wdatostemp['Temlpa']."','".$wdatostemp['Temlot']."','".$wdatostemp['Temfve']."', '".$wsig_nivel."' , '".$wvalor_descuento_esc_sobrante."' ,  '".$wdesart_escalo_sobrante."', '".$wcontrol_aprueba."' ,'C-".$wusuario."')";
										$res2 = mysql_query($q2,$conex) or die (mysql_errno()." - ".mysql_error());
										}

								 }
								else{
								//Si es igual no le dara descuento a ningun mes

									$waumento_seg = 1; //Variable que controla la insercion en la 34 para que no muestre error de llave primaria al insertar varios registros.
									//Se declara una hora nueva con un segundo adicional basados en la ultima hora de la tabla, para poder hacer el registro en la tabla 34.
									$hora = strtotime($arr_datos_temp_aux[$wdatostemp['Temart']]['hora_temporal'])+$waumento_seg;
									$hora = date('H:i:s', ($hora));

									$q= " INSERT INTO ".$wbasedato."_000034 (Medico          ,   Fecha_data ,   Hora_data,   temusu      ,   temfec           ,   temhor          ,   temsuc  ,   temcaj   ,   temtcl     ,   temres  ,   temdcl     ,   temncl     ,   temart ,    temdes  ,   tempre  ,  temcan ,  temvun ,  tempiv    ,  temiva    ,  temtot     , temcmo      ,  temcpr    ,  temdem    ,    temrem    ,  temdar    ,          temdpa    ,  tembpa    ,  temdbo      , temrbo      ,    temcpu     ,   temlpa,        temlot,       temfve,        temnde,                temves,          tempes,             Seguridad) "
									   ."                            VALUES ('".$wbasedato."','".$wfecha."' ,'".$hora."' ,'".$wusuario."','".$wfecha_tempo."' ,'".$whora_tempo."' ,'".$wcco."','".$wcaja."','".$wtipcli."','".$wemp."','".$wdocpac."','".$wnompac."','".$wart."','".$wdes."','".$wuni."',".$wcan.",".$wval.",".$wporiva.",".$wvaliva.",".$wvaltot.",".$wcuotamod.",".$wcospro.",".$wdesemp.",".$wrecemp.",    0              ,".$wpdepac.",".$wvdepac.",".$wdcto_bon.",0            , '".$wcarpun."','".$wlinpac."', '".$wnumlot."','".$wfve."', '0', '0' , '0' ,'C-".$wusuario."')";
									$res2 = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

								}
							}
						}
					}


		            $wventa="N";

					//$whabilita_venta="ENABLED";
		            mostrar($wusuario,$wfecha_tempo,$whora_tempo,$wcco,$wcaja,$conex,$wini,$wdocpac,$wnompac,$wte1pac,$wdirpac,$wmaipac,$wcol,$wtipcli,$wcuotamod,$wempresa,$wventa,$wtipven,$wmensajero,$wdesemp,$wrecemp,$wdesart,$wpdepac,$wvdepac, $wlinpac, $wemp_pmla, $wacc, $wlotven, $wcan);
		           }
		           else  //Si el articulo no tiene la cantidad digitada con tarifa POR ARTICULO, busco la cantidad pero con tarifa por grupo
	                  {
		               $q= "  SELECT artcod, artnom, unides, (karpro+(karpro*(tgrpac/100))), artiva, karexi, karpro, (karpro+(karpro*(tgrpan/100))), tgrfec, emppdt, empprt "
				          ."    FROM ".$wbasedato."_000001, ".$wbasedato."_000027, ".$wbasedato."_000024, ".$wbasedato."_000002, ".$wbasedato."_000007 "
				          ."   WHERE artcod                             = '".$wart."'"
				          ."     AND mid(artgru,1,instr(artgru,'-')-1)  = mid(tgrgru,1,instr(tgrgru,'-')-1) "
				          ."     AND empcod                             = '".$wemp."'"
				          ."     AND mid(tgrcod,1,instr(tgrcod,'-')-1)  = mid(emptar,1,instr(emptar,'-')-1) "
				          ."     AND mid(tgrcco,1,instr(tgrcco,'-')-1)  = '".$wcco."'"
				          ."     AND artcod                             = karcod "
				          ."     AND karcco                             = '".$wcco."'"
				          ."     AND tgrest                             = 'on' "
				          ."     AND artest                             = 'on' "
				          ."     AND karexi                            >= ".$wcan
				          ."     AND emptem                            = '".$wtipcli."'";

				       $res = mysql_query($q,$conex); //or die (mysql_errno()." - ".mysql_error());
					   $num = mysql_num_rows($res);   //or die (mysql_errno()." - ".mysql_error());

					   if ($num > 0)
					      {
						   $row = mysql_fetch_array($res);
					       $wart    = $row[0];
					       $wdes    = $row[1];
					       $wuni    = $row[2];
					       $wvac    = $row[3];
					       $wporiva = $row[4];
					       $wcospro = $row[6];
					       $wvan    = $row[7];
					       $wfeccam = $row[8];
					       $wdesemp = ($row[9]/100);
			               $wrecemp = ($row[10]/100);

					       if ($wfecha < $wfeccam)   //Aca evaluo si tomo el valor anterior o el actual
					          $wval = $wvan;
					         else
					            $wval = $wvac;
					       //////////////////////////////////////////////////////////////////////////////////////////////////////////////
			               //CALCULO DEL IVA ============================================================================================
					       //EL 3 DE AGOSTO SE CAMBIA LA FORMA DE CALCULAR EL IVA DEBIDO A QUE EL VALOR DE LA TARIFA YA LO TIENE INCLUIDO
					       //$wvaliva = $wcan*$wval*($wporiva/100);
					       //$wvaltot = (($wcan*$wval)+($wcan*$wval*($wporiva/100)));
					       if ($wporiva > 0)
					          $wvaliva = round((($wcan*$wval)-(($wcan*$wval)/(1+($wporiva/100)))));
					         else
					            $wvaliva=0;
					       $wvaltot = round(($wcan*$wval));

						   if ($wcan > 0)
							{

							//Si entra por aca es porque ya se valido y por ende puede grabar el articulo en la tabla TEMPORAL
							$q= " INSERT INTO ".$wbasedato."_000034 (Medico          ,   Fecha_data ,   Hora_data,   temusu      ,   temfec           ,   temhor          ,   temsuc  ,   temcaj   ,   temtcl     ,   temres  ,   temdcl     ,   temncl     ,   temart ,    temdes  ,   tempre  ,  temcan ,  temvun ,  tempiv    ,  temiva    ,  temtot     , temcmo      ,  temcpr    ,  temdem    ,  temrem    ,  temdar    ,  temdpa    ,  tembpa    ,  temdbo      , temrbo      ,    temcpu     ,   temlpa,  temlot,  temfve,   Seguridad) "
							   ."                            VALUES ('".$wbasedato."','".$wfecha."' ,'".$hora."' ,'".$wusuario."','".$wfecha_tempo."' ,'".$whora_tempo."' ,'".$wcco."','".$wcaja."','".$wtipcli."','".$wemp."','".$wdocpac."','".$wnompac."','".$wart."','".$wdes."','".$wuni."',".$wcan.",".$wval.",".$wporiva.",".$wvaliva.",".$wvaltot.",".$wcuotamod.",".$wcospro.",".$wdesemp.",".$wrecemp.",".$wdesart.",".$wpdepac.",".$wvdepac.",".$wdcto_bon.",0            , '".$wcarpun."','".$wlinpac."', '".$wnumlot."','".$wfve."', 'C-".$wusuario."')";
							$res2 = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

							}

				           $wventa="N";
				           $wdesart=0;
				            mostrar($wusuario,$wfecha_tempo,$whora_tempo,$wcco,$wcaja,$conex,$wini,$wdocpac,$wnompac,$wte1pac,$wdirpac,$wmaipac,$wcol,$wtipcli,$wcuotamod,$wempresa,$wventa,$wtipven,$wmensajero,$wdesemp,$wrecemp,$wdesart,$wpdepac,$wvdepac,$wlinpac, $wemp_pmla, $wacc, $wlotven, $wcan);
				          }
		                 else
		                    {
		                     ////===========================================================================================================================
				             ////===========================================================================================================================
				             ////===========================================================================================================================
				             echo "<td colspan=".($wcol-2)." align='center' valign='top' height='41'><div align='center' class='fondoRojo' style='width:610px'>No se tiene disponible la cantidad solicitada o NO tiene asignada unidad de medida</div></td>";
				             $wventa="N";
				             $wdesemp=0;
		      				 $wrecemp=0;
		      				 $wdesart=0;
		      				 mostrar($wusuario,$wfecha_tempo,$whora_tempo,$wcco,$wcaja,$conex,$wini,$wdocpac,$wnompac,$wte1pac,$wdirpac,$wmaipac,$wcol,$wtipcli,$wcuotamod,$wempresa,$wventa,$wtipven,$wmensajero,$wdesemp,$wrecemp,$wdesart,$wpdepac,$wvdepac,$wlinpac, $wemp_pmla, $wacc, $wlotven, $wcan);
			                }
			          }
			   } // fin del if isset($warticulo)
			  else
			     if ($wini == 'N') //Aca entra porque no digito nada pero ya ha digitado otro u otros articulos
			        {
				     $wventa="N";
				     $wdesemp=0;
		             $wrecemp=0;
		             $wdesart=0;
		             if (!isset($wmensajero))
		                $wmensajero=" ";
		             mostrar($wusuario,$wfecha_tempo,$whora_tempo,$wcco,$wcaja,$conex,$wini,$wdocpac,$wnompac,$wte1pac,$wdirpac,$wmaipac,$wcol,$wtipcli,$wcuotamod,$wempresa,$wventa,$wtipven,$wmensajero,$wdesemp,$wrecemp,$wdesart,$wpdepac,$wvdepac,$wlinpac, $wemp_pmla, $wacc,$wlotven, $wcan);
				    }
		   }
		 }
	   echo "</tr>";
	 } //Fin del then del if de $wventa = 'N'
	else
       {
	    //=================================================================================================================
	    //=================================================================================================================
	    //ACA SE GRABA LA VENTA !!!!!!!!!!!
	    //=================================================================================================================
	    //Primero verifico que no se halla cambiado el empleado o el valor

	    if ($wtipfac == "Manual")
	       {
		    $q = " SELECT ccopfm, ccoffm, ccofmi "
		         ."  FROM ".$wbasedato."_000003 "
	 		     ." WHERE ccocod='".$wcco."'";

		    $err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	        $row = mysql_fetch_array($err);

	        $wfueffa   =$row[1];
	        $wnrofac   =$row[0]."-".$row[2];
			echo "<input type='hidden' name='walert' value='!!!! ATENCION !!!! ***** ESTA GRABANDO UNA FACTURA MANUAL *****'>";
	       }

	    if (isset($wprestamo) and $wprestamo > 0)
	      {
		    $q = "SELECT pnocod, pnoval "
		        ."  FROM ".$wbasedato."_000046 "
		        ." WHERE pnocon = ".$wprestamo;
		    $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		    $row = mysql_fetch_array($res);

		    if (isset($wemp) and isset($wtotventot))
		        if (trim($row[0]) != trim($wemp) or trim($row[1]) != trim($wtotventot))
			       {
					//echo "Empresa: ".$row[0]." - $wemp | Total: ".$row[1]." - $wtotventot";
				    $WEXISTE_PRESTAMO="off";
			        $whabilita_venta=="DISABLED";
			        $wventa="N";
			        $wprestamo=0;
			        $wpagook=0;

			        $wempleado=explode("-",$wempresa);

			        $wte1pac=$wempleado[0];
			        $wdocpac=$wempleado[1];
			        $wnompac=$wempleado[2];
			        $wcarpun="000000";

			        echo "<input type='HIDDEN' name= 'wcarpun' value='".$wcarpun."'>";
			        echo "<input type='HIDDEN' name= 'wte1pac' value='".$wte1pac."'>";
			        echo "<input type='HIDDEN' name= 'wdocpac' value='".$wdocpac."'>";
			        echo "<input type='HIDDEN' name= 'wnompac' value='".$wnompac."'>";
			        echo "<input type='HIDDEN' name= 'wtotventot' value='".$wtotventot."'>";

					echo "<input type='hidden' name='walert' value='!!!! ATENCION !!!! Se modifico el responsable del prestamo o su valor, favor repita el proceso de verificación de cupo'>";
			        ?>
				     <script>
				        //submit_form('on');
				     </script>
					<?php

				   }
	      }

		echo "<input type='HIDDEN' name='whabilita_venta' value='".$whabilita_venta."'>";
		echo "<input type='HIDDEN' name='wemp' value='".$wemp."'>";

	    if ($wini == "N" and $whabilita_venta=="ENABLED" )
	       {
		    echo "</table>";
	        echo "<center><table border=0 width='950'>";

		    $wdesart=0;
			if(isset($consultaAjax2) && $consultaAjax2=='envio')
				mostrar($wusuario,$wfecha_tempo,$whora_tempo,$wcco,$wcaja,$conex,$wini,$wdocpac,$wnompac,$wte1pac,$wdirpac,$wmaipac,$wcol,$wtipcli,$wcuotamod,$wempresa,$wventa,$wtipven,$wmensajero,$wdesemp,$wrecemp,$wdesart,$wpdepac,$wvdepac,$wlinpac, $wemp_pmla, $wacc, $wlotven, $wcan);

	        $WSINCUOTA="N";                                     //Indica que el responsable es una empresa pero no se le cobra nada al paciente

			//-Modificacion para que funcionen todas las empresas igual sin tener que quemar 01-PARTICULAR  en los if siguientes
			// if $wtipcli=="01-PARTICULAR" ya que solo estaba funcionando este para farmastore
			$wtipoEmpresaParticular='01-PARTICULAR';
			//----------

			$wtipoEmpresaParticular = consultarAliasPorAplicacion($conex, $wemp_pmla, "EmpParVentas_nue");


				 $q= "   SELECT Emppre"
						 ."     FROM ".$wbasedato."_000024 "
						 ."    WHERE empcod = (mid('".$wempresa."',1,instr('".$wempresa."','-')-1)) "
						 ."      AND emptem = '".$wtipcli."'"
						 ."      AND empest = 'on' ";

				$res1 = mysql_query($q,$conex) or die(mysql_errno().":".mysql_error());
			    if ($row1 = mysql_fetch_array($res1))
				{
					$hacefactura = $row1['Emppre'];
				}





			//----------
			//if ($wtipcli== $wtipoEmpresaParticular)
			if ($wclaveok != "S")
			{
				if ($hacefactura !='off' )
				   {
					include_once("ips/Grabar_venta_nue.php");
				   }
				  else  //Cuando entre por aca pregunto si la cuota moderadora es mayor a cero
					 {
					  if ($wcuotamod > 0 and $wtipcli <> $wtipoEmpresaParticular)
						 {
						  include_once("ips/Grabar_venta_nue.php");
						 }
						else
						   if ($wcuotamod == 0 and $wtipcli <> $wtipoEmpresaParticular)
							  {
							   if ($wchequeo=="on")
								  if ($wpagook==0)    //No tiene capacidad de pago por nomina
									 {
									  $fk=0;
									  $wventa="N";
									  echo "<td  class='articuloControl' colspan=13 align=center><b>EMPLEADO NO HABILITADO PARA DEDUCCION POR NOMINA O EL NUMERO DE CUOTAS NO ES SUFICIENTE</b></TD>";
									  echo "<tr><td align=center colspan=13><input type='button' onclick='submit_form2()' value='OK'></td></tr>";                            //submit
									 }
									else
									   {
										$WSINCUOTA="S";                  //Si entra por aca Indica que el responsable es una empresa pero no se le cobra nada al paciente
										include_once("ips/Grabar_venta_nue.php");
										$fk=0;
									   }
								 else
									{
									 $WSINCUOTA="S";                     //Si entra por aca Indica que el responsable es una empresa pero no se le cobra nada al paciente
									 include_once("ips/Grabar_venta_nue.php");
									 $fk=0;
									}
							  }
					 }
			}

		    echo "<input type='HIDDEN' name='wventa' id='wventa' value='".$wventa."'>";      //Envio la venta como "S"
	        echo "<input type='HIDDEN' name='fk' value='".$fk."'>";              //Contador de formas de pago que han digitado
	       }
       }
		echo "<input type='HIDDEN' name='whabilita_venta' value='".$whabilita_venta."'>";

  if((isset($consultaAjax2) && $consultaAjax2=='envio'))
  {
   $wdato="";
   $wcodart="";
   $wdesart="";
   unset($wcodart);
   unset($wdesart);
   unset($wdato);
  }
 }

 if((!isset($consultaAjax) || $consultaAjax!='envio') && (!isset($consultaAjax2) || $consultaAjax2!='envio'))
 {

   echo "<table border=0 align=center><tr><td align=center colspan=13><br><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'><br></td></tr>";
   echo "<tr><td align=center class='fila2'><a href='copia_factura.php?wcaja=".$wcaja."&amp;wbasedato=".$wbasedato."' target='_blank'> Imprimir Copia de Factura</a></font></td></tr>";
   //echo "<tr><td align=center class='fila2'><a href='uvglobal00.php' target='_blank'>Ir a orden de laboratorio</a></font></td></tr>";
   echo "</table>";



   //--------
   // campos ocultos
   // $wcco = substr($row[0],0,$pos);
   // $wnomcco
   echo "<input type='hidden' id = 'cco_desde_ventas' value='".$wcco."' >";
   echo "<input type='hidden' id = 'nomcco_desde_ventas' value='".$wnomcco."' >";
   //---------------------
   //--Vector empresas , se utiliza para crear buscador
   $vector_empresas= array();
   $vector_empresas= traer_hidden_empresas();
   echo "<input type='hidden' id='hidden_empresas' value='".json_encode($vector_empresas)."'>";

   //--Vector centros de costo, se utiliza para crear buscador
   $vector_cco= array();
   $vector_cco= traer_hidden_cco();
   echo "<input type='hidden' id='hidden_cco' value='".json_encode($vector_cco)."'>";


  // echo "<br><br><table align='center'><tr><td><input type='button' value='Facturar' onclick='ir_a_facturar()'></td></tr></table>";
   //div oculta para buscar ventas al momento de facturar
   echo "<div id='factura_nueva' style='display:none'>";

   echo"<input type='hidden' id='f_cco' class='val_autocomplete' tipo='obligatorio'>";
   echo "<table id='table_filtros' align='center'>
			<tr>
				<td class='encabezadoTabla' colspan='4'>Filtros de Busqueda</td>
			</tr>

			<tr>
				<td class='fila1' colspan='2'>Documento</td><td class='fila2' colspan='2'><input type='text' id='f_documento'></td>
			</tr>

			<tr>
				<td class='fila1' colspan='2'>Nombre Paciente</td><td class='fila2' colspan='2'><input type='text' id='wfnombrepac'></td>
			</tr>

			<tr>
				<td class='fila1' colspan='2'>Nro Venta:</td><td class='fila2' colspan='2'><input type='text' id='f_venta'></td>
			</tr>";



			echo"
			<tr align='center'>
				<td class='fila1' colspan='4'><input type='button' value='Buscar' onclick='buscar_facturas()'><input type='button' value='Limpiar filtros' onclick='limpiar_filtros()'></td>
			</tr>
			</table>
			<br><br><br>";

			echo "<input type='hidden' id='fechaactual'>
				  <input type='hidden' id='wresponsable'>";
			echo "<div id='resultados_busqueda'></div>";
			echo "<br><br><center><input type='button' value='Cerrar' onclick='cerrar_ventana_facturas()'></center>";


   echo "</div>";
   // div oculta para buscar ventas en el momento de abonar
   echo "<div id='dialog_abonos'  style='display:none'>";
   echo "
		 <input type='hidden' id='f_cco_venta'>
		 <input type='hidden' id='wbasedato1'>
		 <table id='table_filtros' align='center'>
			<tr>
				<td class='encabezadoTabla' colspan='4'>Filtros de Busqueda</td>
			</tr>

			<tr>
				<td class='fila1' colspan='2'>Documento</td><td class='fila2' colspan='2'><input type='text' id='f_documento_ventas'></td>
			</tr>

			<tr>
				<td class='fila1' colspan='2'>Nombre Paciente</td><td class='fila2' colspan='2'><input type='text' id='wfnombrepac_ventas'></td>
			</tr>

			<tr>
				<td class='fila1' colspan='2'>Nro Venta:</td><td class='fila2' colspan='2'><input type='text' id='f_venta_ventas'></td>
			</tr>
			<tr align='center'>
				<td class='fila1' colspan='4'><input type='button' id='botongrabarventanamodal' class='btn_loading' value='Buscar' onclick='buscar_ventas()'><input type='button' value='Limpiar filtros' onclick='limpiar_filtros_abonos()'></td>
			</tr>
		</table>
			<br><br><br>";

	echo "<input type='hidden' id='fechaactual'>
		  <input type='hidden' id='wresponsable'>";
	echo "<div id='resultados_busqueda_ventas'></div>";
	echo "<br><br><center><div id='resultados_detalles_ventas'></div></center>";
	echo "<br><br><center><input type='button' value='Cerrar' onclick='cerrar_ventana_abonos()'></center>";


   echo "</div>";

 }



echo "</table><br>";
echo "<div id='msjAlerta' style='display:none;'>
	       <br>
	       <img src='../../images/medical/root/Advertencia.png'/>
	       <br><br><div id='textoAlerta'></div><br><br>
	   </div>";
if((isset($consultaAjax) && $consultaAjax=='envio'))
	echo "</form>";
}




?>
</body>
</html>
