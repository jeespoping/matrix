<!--El programa realiza la consulta de un query respectivo a los comprobantes de recibo de egresos para tesoreria, y manda el enlace respectivo para generar la descarga en comprobantes_imprimir. -->
<!--//==========================================================================================================================================
//PROGRAMA				      : El programa realiza la consulta de un query respectivo a los comprobantes de recibo de egresos para tesoreria, y manda el enlace respectivo para generar la descarga en comprobantes_imprimir.                                      |
//AUTOR				          : Didier Orozco Carmona.                                                                                       |
//FECHA CREACION			  : 2020-03-04.                                                                                             |
//FECHA ULTIMA ACTUALIZACION  : 2020-03-04.                                                                                             |
//
//                                                                                                                                          |
//TABLAS UTILIZADAS DE MODO CONSULTA SON: cbmov,cbmovdet,cpmovdet,fpmovenc,fpmovenc,cppro,cptip,cbmov
//
//  
// 
//                                                                                                                                     |
//==========================================================================================================================================	
-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<?php
    include_once("conex.php");
    include_once("root/comun.php");
	//$conex_o = odbc_connect('cajban','cybadm','1201')  or die("No se realizo conexión con la BD de Facturación");
    if(!isset($_SESSION['user']))
    {
?>
<style>
		html body {
				width: 80%;
				margin: 20px auto 0 auto;
		}
</style>
<style>
    .alternar:hover{ background-color:#e1edf7;}
	.Estilo4 {color: #000000; font-weight: bold; }
</style>
<div align="center">
		<label>Usuario no autenticado en el sistema.<br />Recargue la pagina principal de Matrix o inicie sesion nuevamente.</label>
</div>
<?php
    return;
    }
    else
    {
        $user_session = explode('-', $_SESSION['user']);
        $wuse = $user_session[1];
        
        mysql_select_db("matrix");
        $conex = obtenerConexionBD("matrix");
    }
?>
    <script src="//code.jquery.com/jquery-1.9.1.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" /><!--Estilo para el calendario-->
	<link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
	<script src="//mx.lasamericas.com.co/matrix/paf/procesos/calendariopaf.js" type="text/javascript"></script>
	<script src="//mx.lasamericas.com.co/matrix/paf/procesos/calendariopaf.js" type="text/javascript"></script>
</head>
<body width="1200" height="60">
 <?php
	$Fuente = $_GET['fue'];
	$Documento_i = $_GET['doci'];
	$Documento_f = $_GET['docf'];
	$Fecha1 = $_GET['fec1'];
	$Fecha2 = $_GET['fec2'];
	$Metodo = $_GET['met'];
	$Conexion = $_GET['cone'];
	// Si est&aacute; vac&iacute;o, lo informamos, sino realizamos la búsqueda
		//VALIRDAR PARA FUENTE 47
	if ($Conexion == '01')
		$conex_o = odbc_connect('cajban','cybadm','1201')  or die("No se realizo conexión con la BD de Facturación"); //promotora conexion
	if ($Conexion == '02')
		$conex_o = odbc_connect('cybsur','cybsur','aa057')  or die("No se realizo conexión con la BD de Facturación");  //Clisur conexion
	/*switch ($Conexion) {
    case "promo":
        echo "La conexion es promo";
		$conex_o = odbc_connect('cajban','cybadm','1201')  or die("No se realizo conexión con la BD de Facturación");
        break;
    case "clisur":
        echo "La conexion es clisur";
		$conex_o = odbc_connect('cybsur','cybsur','aa057')  or die("No se realizo conexión con la BD de Facturación");
        break;
	}*/
	//
	//VALIRDAR PARA FUENTE 47
		if($Fuente == '47'){
			if($Metodo == 'Rango de Fechas')
				{
					//ENCABEZADO CON FECHAS FUENTE 47
					$select_parafte47 = "select movfue,movdoc,movnom,movnit,movval,movban,movche,movfec,bannom
										from cbmov,cbban
										where cbmov.movban = cbban.bancod
										and movfue='$Fuente'
										and movfec BETWEEN '$Fecha1' and '$Fecha2'";
					$resultado_parafte47 = odbc_do($conex_o, $select_parafte47);
					while(odbc_fetch_row($resultado_parafte47)){
					//odbc_fetch_row($resultado_parafte47);
					$fue = odbc_result($resultado_parafte47, 1);
					$doc = odbc_result($resultado_parafte47, 2);
					$nom = odbc_result($resultado_parafte47, 3);
					$nit = odbc_result($resultado_parafte47, 4);
					$val = odbc_result($resultado_parafte47, 5);
					$ban = odbc_result($resultado_parafte47, 6);
					$che = odbc_result($resultado_parafte47, 7);
					$fec = odbc_result($resultado_parafte47, 8);
					$bnom = odbc_result($resultado_parafte47, 9);
					$select_parafte47D = "SELECT movdetcue,movdetdoc,movdetval,'2',0
										FROM cbmovdet
										WHERE movdetfue='$fue'
										AND movdetdoc = '$doc'
										UNION ALL
										SELECT tipcua,movencdoc,movval,'1',0
										FROM fpmovenc,cppro,cptip,cbmov
										WHERE movencfue = '$fue'
										AND movencdoc  = '$doc'
										AND movencpro=procod
										AND protip=tipcod
										AND movencfue=movfue
										AND movencdoc=movdoc";
										?>
						<table width="1000" border="1" align="center">
							<tr>
								<td colspan="4" style="background-color: #C3D9FF" height="100"> 
								&ensp;
								</td>
							</tr>
							<tr>
							  <td colspan="1"> 
			                    <div align="left">
	                                <?php if($Conexion == '01') { ?>
										<input type="image" id="btnVer" src="//mx.lasamericas.com.co/matrix/images/medical/paf/logo1.jpg" width="460" height="155">
									<?php }else{ ?>
										<input type="image" id="btnVer" src="//mx.lasamericas.com.co/matrix/images/medical/paf/logo2.jpg" width="460" height="155">
									<?php } ?>
	                            </div>
							  </td>
							  <td colspan="3" width="0"><font color="solido"><h4><strong>COMPROBANTE </br> DE EGRESO No. </strong></font><font color="maroon"><?php echo $doc ?> </h4></font> 
								  </br>
									<strong>Fecha: </strong><?php echo $fec ?>
							  </td>
							</tr>
							<tr>
								<td colspan="2"><p align="left"><strong>A FAVOR DE: </strong><?php echo $nom ?></p></td>
								<td colspan="1"><p align="left"><strong>C.C. o NIT: </strong><?php echo $nit ?></p></td>
								<td colspan="1"><p align="left"><strong>POR: </strong><br/> $<?php echo number_format($val,0,'',',') ?></p></td>
							</tr>
							<tr height="60">
								<td colspan="4"><p><strong>CONCEPTO:</strong></p></td>
							</tr>
						</table>									<!-- PINTAR LOS VALORES -->
						<table width="1000" height="44" border="1" align="center">												
							<tr>	
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>IMPUTACION CONTABLE</strong></div></td>
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>DOCUMENTO REFERENCIA</strong></div></td>											
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>VALOR</strong></div></td>
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>DB 1 CR 2</strong></div></td>
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>CEDULA / NIT. </strong></div></td>
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>BASE IVA - RF.</strong></div></td>
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>CENTRO DE COSTOS</strong></div></td>
							</tr>	
						<?php
						//OBTENER RESULTADO DEL QUERY 
						$resultado_valoresfte47=odbc_do($conex_o, $select_parafte47D);
						while(odbc_fetch_row($resultado_valoresfte47)){  
						$movdetcue = odbc_result($resultado_valoresfte47, 1);
						$movdetdoc = odbc_result($resultado_valoresfte47, 2);
						$movdetval = odbc_result($resultado_valoresfte47, 3);
						$ind = odbc_result($resultado_valoresfte47, 4);
						$bas = odbc_result($resultado_valoresfte47, 5);
						?>
							<tr>
								<td colspan="7" width="50" height="18"><?php echo $movdetcue ?> <div align="center"></div></td>
								<td colspan="7" width="20"><?php echo $movdetdoc ?></td>
								<td colspan="7" width="50"><?php echo number_format($movdetval,0,'',',') ?></td>
								<td colspan="7" width="50"><?php echo $ind ?></td>
								<td colspan="7" width="50"><?php echo '' ?></td>
								<td colspan="7" width="50"><?php echo $bas ?></td>
								<td colspan="7" width="50"><?php echo '' ?></td>
							</tr>
						
						<?php
						}
						?>
						</table>
						<table width="1000" height="44" border="1" align="center">
							<tr>
								<td colspan="7" height="60" height="18"><strong><div style="padding-top:1px; height:100px"><?php echo 'CHEQUE No.' ?></strong><?php echo $che ?></div></td>
								<td colspan="7" height="60" height="18"><strong><div style="padding-top:1px; height:100px"><?php echo 'EFECTIVO     ' ?></strong><input type="checkbox" disabled="on"><label for="cbox2"></label></div></td>
								<td width="50"  height="60" colspan="7" rowspan="2"><div style="padding-top:1px; height:200px"><strong>FIRMA Y SELLO BENEFICIARIO</strong></div></td>
							</tr>
							<tr>
								<td colspan="7" height="60"><strong><div style="padding-top:1px; height:100px"><?php echo 'BANCO ' ?></strong><?php echo $ban.'-'.$bnom ?></div></td>
								<td colspan="7" height="60"><strong><div style="padding-top:1px; height:100px"><?php echo 'APROBADO' ?></strong></div></td>
							</tr>
						</table>
						<td>&ensp;</td>
						<div style="page-break-after: always"></div>
				<?php
					}
				}else
					{
						////ENCABEZADO CON DOCUMENTOS
					$select_parafte47 = "select movfue,movdoc,movnom,movnit,movval,movban,movche,movfec,bannom
										from cbmov,cbban
										where cbmov.movban = cbban.bancod
										and movfue='$Fuente'
										and movdoc BETWEEN '$Documento_i' and '$Documento_f'";
					$resultado_parafte47 = odbc_do($conex_o, $select_parafte47);
					while(odbc_fetch_row($resultado_parafte47)){
					//odbc_fetch_row($resultado_parafte47);
					
					$fue = odbc_result($resultado_parafte47, 1);
					$doc = odbc_result($resultado_parafte47, 2);
					$nom = odbc_result($resultado_parafte47, 3);
					$nit = odbc_result($resultado_parafte47, 4);
					$val = odbc_result($resultado_parafte47, 5);
					$ban = odbc_result($resultado_parafte47, 6);
					$che = odbc_result($resultado_parafte47, 7);
					$fec = odbc_result($resultado_parafte47, 8);
					$bnom = odbc_result($resultado_parafte47, 9);
					$select_parafte47D = "SELECT movdetcue,movdetdoc,movdetval,'2',0
										FROM cbmovdet
										WHERE movdetfue='$fue'
										AND movdetdoc = '$doc'
										UNION ALL
										SELECT tipcua,movencdoc,movval,'1',0
										FROM fpmovenc,cppro,cptip,cbmov
										WHERE movencfue = '$fue'
										AND movencdoc  = '$doc'
										AND movencpro=procod
										AND protip=tipcod
										AND movencfue=movfue
										AND movencdoc=movdoc";
						?>
						<table width="1000" border="1" align="center">
							<tr>
								<td colspan="4" style="background-color: #C3D9FF" height="100"> 
								&ensp;
								</td>
							</tr>
							<tr>
							  <td colspan="1"> 
			                    <div align="left">
	                                <?php if($Conexion == '01') { ?>
										<input type="image" id="btnVer" src="//mx.lasamericas.com.co/matrix/images/medical/paf/logo1.jpg" width="460" height="155">
									<?php }else{ ?>
										<input type="image" id="btnVer" src="//mx.lasamericas.com.co/matrix/images/medical/paf/logo2.jpg" width="460" height="155">
									<?php } ?>
	                            </div>
							  </td>
							  <td colspan="3" width="0"><font color="solido"><h4><strong>COMPROBANTE </br> DE EGRESO No. </strong></font><font color="maroon"><?php echo $doc ?> </h4></font> 
								  </br>
									<strong>Fecha: </strong><?php echo $fec ?>
							  </td>
							</tr>
							<tr>
								<td colspan="2"><p align="left"><strong>A FAVOR DE: </strong><?php echo $nom ?></p></td>
								<td colspan="1"><p align="left"><strong>C.C. o NIT: </strong><?php echo $nit ?></p></td>
								<td colspan="1"><p align="left"><strong>POR: </strong><br/> $<?php echo number_format($val,0,'',',') ?></p></td>
							</tr>
							<tr height="60">
								<td colspan="4"><p><strong>CONCEPTO:</strong></p></td>
							</tr>
						</table>									<!-- PINTAR LOS VALORES -->
						<table width="1000" height="44" border="1" align="center">												
							<tr>	
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>IMPUTACION CONTABLE</strong></div></td>
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>DOCUMENTO REFERENCIA</strong></div></td>											
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>VALOR</strong></div></td>
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>DB 1 CR 2</strong></div></td>
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>CEDULA / NIT. </strong></div></td>
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>BASE IVA - RF.</strong></div></td>
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>CENTRO DE COSTOS</strong></div></td>
							</tr>	
						<?php
						//OBTENER RESULTADO DEL QUERY 
						$resultado_valoresfte47=odbc_do($conex_o, $select_parafte47D);
						while(odbc_fetch_row($resultado_valoresfte47)){  
						$movdetcue = odbc_result($resultado_valoresfte47, 1);
						$movdetdoc = odbc_result($resultado_valoresfte47, 2);
						$movdetval = odbc_result($resultado_valoresfte47, 3);
						$ind = odbc_result($resultado_valoresfte47, 4);
						$bas = odbc_result($resultado_valoresfte47, 5);
						?>
							<tr>
								<td colspan="7" width="50" height="18"><?php echo $movdetcue ?> <div align="center"></div></td>
								<td colspan="7" width="20"><?php echo $movdetdoc ?></td>
								<td colspan="7" width="50"><?php echo number_format($movdetval,0,'',',') ?></td>
								<td colspan="7" width="50"><?php echo $ind ?></td>
								<td colspan="7" width="50"><?php echo '' ?></td>
								<td colspan="7" width="50"><?php echo $bas ?></td>
								<td colspan="7" width="50"><?php echo '' ?></td>
							</tr>
						
						<?php
						}
						?>
						</table>
						<table width="1000" height="44" border="1" align="center">
							<tr>
								<td colspan="7" height="60" height="18"><strong><div style="padding-top:1px; height:100px"><?php echo 'CHEQUE No.' ?></strong><?php echo $che ?></div></td>
								<td colspan="7" height="60" height="18"><strong><div style="padding-top:1px; height:100px"><?php echo 'EFECTIVO     ' ?></strong><input type="checkbox" disabled="on"><label for="cbox2"></label></div></td>
								<td width="50"  height="60" colspan="7" rowspan="2"><div style="padding-top:1px; height:200px"><strong>FIRMA Y SELLO BENEFICIARIO</strong></div></td>
							</tr>
							<tr>
								<td colspan="7" height="60"><strong><div style="padding-top:1px; height:100px"><?php echo 'BANCO ' ?></strong><?php echo $ban.'-'.$bnom ?></div></td>
								<td colspan="7" height="60"><strong><div style="padding-top:1px; height:100px"><?php echo 'APROBADO' ?></strong></div></td>
							</tr>
						</table>
						<td>&ensp;</td>
						<div style="page-break-after: always"></div>
			<?php
					}
			}
		} // FINAL VALIDAR FUENTE 47
		//PARA LA FUENTE 50
		
		if ($Fuente == '50'){
			//ENCABEZADO CON FECHAS FUENTE 50
			 if($Metodo == 'Rango de Fechas')
				{
					$select_parafte50 = "select movfue,movdoc,movnom,movnit,movval,movban,movche,movfec,bannom
										from cbmov,cbban
										where cbmov.movban = cbban.bancod
										and movfue='$Fuente'
										and movfec BETWEEN '$Fecha1' and '$Fecha2'";
					$resultado_parafte50 = odbc_do($conex_o, $select_parafte50);
					while(odbc_fetch_row($resultado_parafte50)){
					//odbc_fetch_row($resultado_parafte50);
					$fuef50 = odbc_result($resultado_parafte50, 1);
					$docf50 = odbc_result($resultado_parafte50, 2);
					$nom = odbc_result($resultado_parafte50, 3);
					$nit = odbc_result($resultado_parafte50, 4);
					$val = odbc_result($resultado_parafte50, 5);
					$ban = odbc_result($resultado_parafte50, 6);
					$che = odbc_result($resultado_parafte50, 7);
					$fec = odbc_result($resultado_parafte50, 8);
					$bnom = odbc_result($resultado_parafte50, 9);
					
					$select_parafte50Con1 ="SELECT movdetfue,movdetdoc,movencfue,movencdoc,movencfac,movdetval
											FROM cpmovdet,fpmovenc
											WHERE movdetfue='$fuef50'
											AND movdetdoc='$docf50'
											AND movdetfca=movencfue
											AND movdetdca=movencdoc";		
					
					$select_parafte50D = "SELECT movdetcue,movdetdoc,movdetval, '2'	ind,0
											FROM cbmovdet
											WHERE movdetfue='$fuef50'
											AND movdetdoc = '$docf50'
											UNION ALL
										  SELECT tipcue,movencdoc,movval,'1',0
											FROM fpmovenc,cppro,cptip,cbmov
											WHERE movencfue='$fuef50'
											AND movencdoc = '$docf50'
											AND movencpro=procod
											AND protip=tipcod
											AND movencfue=movfue
											AND movencdoc=movdoc";
						?>
						<table width="1000" border="1" align="center">
							<tr>
								<td colspan="4" style="background-color: #C3D9FF" height="100"> 
								&ensp;
								</td>
							</tr>
							<tr>
							  <td colspan="1"> 
			                    <div align="left">
	                                <?php if($Conexion == '01') { ?>
										<input type="image" id="btnVer" src="//mx.lasamericas.com.co/matrix/images/medical/paf/logo1.jpg" width="460" height="155">
									<?php }else{ ?>
										<input type="image" id="btnVer" src="//mx.lasamericas.com.co/matrix/images/medical/paf/logo2.jpg" width="460" height="155">
									<?php } ?>
	                            </div>
							  </td>
							  <td colspan="3" width="0"><font color="solido"><h4><strong>COMPROBANTE </br> DE EGRESO No. </strong></font><font color="maroon"><?php echo $docf50 ?> </h4></font> 
								</br>
									<strong>Fecha: </strong><?php echo $fec ?>
							  </td>
							</tr>
							<tr>
								<td colspan="2"><p align="left"><strong>A FAVOR DE: </strong><?php echo $nom ?></p></td>
								<td colspan="1"><p align="left"><strong>C.C. o NIT: </strong><?php echo $nit ?></p></td>
								<td colspan="1"><p align="left"><strong>POR: </strong><br/> $<?php echo number_format($val,0,'',',') ?></p></td>
							</tr>
							<tr height="60">
								<td colspan="4"><p><strong>CONCEPTO:</strong></p>
									<p><strong>Cancela los documentos:</strong></p>
										<table border="0" style="width:100%">
										<?php
										// CODIGO PARA CREAR COLUMNAS SEGUIN EL RESULTADO
										$resultado_parafte50Con1=odbc_do($conex_o, $select_parafte50Con1);
										$columna = 0;
										while(odbc_fetch_row($resultado_parafte50Con1)){
											$movencfueC = odbc_result($resultado_parafte50Con1, 3);
											$movencdocC = odbc_result($resultado_parafte50Con1, 4);
											$movencfacC = odbc_result($resultado_parafte50Con1, 5);
											$movdetvalC = odbc_result($resultado_parafte50Con1, 6);
											$formatmovdetval = number_format($movdetvalC,0,'',',');
											$concat_confte50c1 = $movencdocC.'-'.$movencfueC.'-'.$movencfacC.': '.'$'.$formatmovdetval;											
											$arrayResultado[] = $concat_confte50c1;											
											$columna += 1;											
											if($columna==1)
											{
												echo "<tr>";
											}
											echo "	<td>- ".$concat_confte50c1."</td>";
											if($columna==4)
											{
												echo "</tr>";
												$columna = 0;
											}
										}										
										echo "</table>";
									?>
								</td>
							</tr>
						</table>									<!-- PINTAR LOS VALORES -->
						<table width="1000" height="44" border="1" align="center">												
							<tr>	
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>IMPUTACION CONTABLE</strong></div></td>
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>DOCUMENTO REFERENCIA</strong></div></td>											
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>VALOR</strong></div></td>
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>DB 1 CR 2</strong></div></td>
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>CEDULA / NIT. </strong></div></td>
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>BASE IVA - RF.</strong></div></td>
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>CENTRO DE COSTOS</strong></div></td>
							</tr>	
						<?php
						//OBTENER RESULTADO DEL QUERY 
						$resultado_valoresfte50=odbc_do($conex_o, $select_parafte50D);
						while(odbc_fetch_row($resultado_valoresfte50)){  
						$movdetcue = odbc_result($resultado_valoresfte50, 1);
						$movdetdoc = odbc_result($resultado_valoresfte50, 2);
						$movdetval = odbc_result($resultado_valoresfte50, 3);
						$ind = odbc_result($resultado_valoresfte50, 4);
						$bas = odbc_result($resultado_valoresfte50, 5);
						?>
							<tr>
								<td colspan="7" width="50" height="18"><?php echo $movdetcue ?> <div align="center"></div></td>
								<td colspan="7" width="20"><?php echo $movdetdoc ?></td>
								<td colspan="7" width="50"><?php echo number_format($movdetval,0,'',',') ?></td>
								<td colspan="7" width="50"><?php echo $ind ?></td>
								<td colspan="7" width="50"><?php echo $nit ?></td>
								<td colspan="7" width="50"><?php echo $bas ?></td>
								<td colspan="7" width="50"><?php echo '' ?></td>
							</tr>
						
						<?php
						}
						?>
						</table>
						<table width="1000" height="44" border="1" align="center">
							<tr>
								<td colspan="7" height="60" height="18"><strong><div style="padding-top:1px; height:100px"><?php echo 'CHEQUE No.' ?></strong><?php echo $che ?></div></td>
								<td colspan="7" height="60" height="18"><strong><div style="padding-top:1px; height:100px"><?php echo 'EFECTIVO     ' ?></strong><input type="checkbox" disabled="on"><label for="cbox2"></label></div></td>
								<td width="50"  height="60" colspan="7" rowspan="2"><div style="padding-top:1px; height:200px"><strong>FIRMA Y SELLO BENEFICIARIO</strong></div></td>
								
							</tr>
							<tr>
								<td colspan="7" height="60"><strong><div style="padding-top:1px; height:100px"><?php echo 'BANCO ' ?></strong><?php echo $ban.'-'.$bnom ?></div></td>
								<td colspan="7" height="60"><strong><div style="padding-top:1px; height:100px"><?php echo 'APROBADO' ?></strong></div></td>
							</tr>
						</table>
						<td>&ensp;</td>
						<div style="page-break-after: always"></div>
			<?php
					}
				}else
					{
						//ENCABEZADO CON DOCUMENTOS FUENTE 50
					$select_parafte50 = "select movfue,movdoc,movnom,movnit,movval,movban,movche,movfec,bannom
										from cbmov,cbban
										where cbmov.movban = cbban.bancod
										and movfue='$Fuente'
										and movdoc BETWEEN '$Documento_i' and '$Documento_f'";
					$resultado_parafte50 = odbc_do($conex_o, $select_parafte50);
					while(odbc_fetch_row($resultado_parafte50)){
					//odbc_fetch_row($resultado_parafte50);
					$fuef50 = odbc_result($resultado_parafte50, 1);
					$docf50 = odbc_result($resultado_parafte50, 2);
					$nom = odbc_result($resultado_parafte50, 3);
					$nit = odbc_result($resultado_parafte50, 4);
					$val = odbc_result($resultado_parafte50, 5);
					$ban = odbc_result($resultado_parafte50, 6);
					$che = odbc_result($resultado_parafte50, 7);
					$fec = odbc_result($resultado_parafte50, 8);
					$bnom = odbc_result($resultado_parafte50, 9);
					
					$select_parafte50Con1 ="SELECT movdetfue,movdetdoc,movencfue,movencdoc,movencfac,movdetval
											FROM cpmovdet,fpmovenc
											WHERE movdetfue='$fuef50'
											AND movdetdoc='$docf50'
											AND movdetfca=movencfue
											AND movdetdca=movencdoc";		
					
					$select_parafte50D = "SELECT movdetcue,movdetdoc,movdetval, '2'	ind,0
											FROM cbmovdet
											WHERE movdetfue='$fuef50'
											AND movdetdoc = '$docf50'
											UNION ALL
										  SELECT tipcue,movencdoc,movval,'1',0
											FROM fpmovenc,cppro,cptip,cbmov
											WHERE movencfue='$fuef50'
											AND movencdoc = '$docf50'
											AND movencpro=procod
											AND protip=tipcod
											AND movencfue=movfue
											AND movencdoc=movdoc";
						?>
						<table width="1000" border="1" align="center">
							<tr>
								<td colspan="4" style="background-color: #C3D9FF" height="100"> 
								&ensp;
								</td>
							</tr>
							<tr>
							  <td colspan="1"> 
			                    <div align="left">
	                                <?php if($Conexion == '01') { ?>
										<input type="image" id="btnVer" src="//mx.lasamericas.com.co/matrix/images/medical/paf/logo1.jpg" width="460" height="155">
									<?php }else{ ?>
										<input type="image" id="btnVer" src="//mx.lasamericas.com.co/matrix/images/medical/paf/logo2.jpg" width="460" height="155">
									<?php } ?>
	                            </div>
							  </td>
							  <td colspan="3" width="0"><font color="solido"><h4><strong>COMPROBANTE </br> DE EGRESO No. </strong></font><font color="maroon"><?php echo $docf50 ?> </h4></font> 
								</br>
									<strong>Fecha: </strong><?php echo $fec ?>
							  </td>
							</tr>
							<tr>
								<td colspan="2"><p align="left"><strong>A FAVOR DE: </strong><?php echo $nom ?></p></td>
								<td colspan="1"><p align="left"><strong>C.C. o NIT: </strong><?php echo $nit ?></p></td>
								<td colspan="1"><p align="left"><strong>POR: </strong><br/> $<?php echo number_format($val,0,'',',') ?></p></td>
							</tr>
							<tr height="60">
								<td colspan="4"><p><strong>CONCEPTO:</strong></p>
									<p><strong>Cancela los documentos:</strong></p>
										<table border="0" style="width:100%">
										<?php
										// CODIGO PARA CREAR COLUMNAS SEGUIN EL RESULTADO
										$resultado_parafte50Con1=odbc_do($conex_o, $select_parafte50Con1);
										$columna = 0;
										while(odbc_fetch_row($resultado_parafte50Con1)){
											$movencfueC = odbc_result($resultado_parafte50Con1, 3);
											$movencdocC = odbc_result($resultado_parafte50Con1, 4);
											$movencfacC = odbc_result($resultado_parafte50Con1, 5);
											$movdetvalC = odbc_result($resultado_parafte50Con1, 6);
											$formatmovdetval = number_format($movdetvalC,0,'',',');
											$concat_confte50c1 = $movencdocC.'-'.$movencfueC.'-'.$movencfacC.': '.'$'.$formatmovdetval;											
											$arrayResultado[] = $concat_confte50c1;											
											$columna += 1;											
											if($columna==1)
											{
												echo "<tr>";
											}
											echo "	<td>- ".$concat_confte50c1."</td>";
											if($columna==4)
											{
												echo "</tr>";
												$columna = 0;
											}
										}										
										echo "</table>";
									?>
								</td>
							</tr>
						</table>									<!-- PINTAR LOS VALORES -->
						<table width="1000" height="44" border="1" align="center">												
							<tr>	
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>IMPUTACION CONTABLE</strong></div></td>
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>DOCUMENTO REFERENCIA</strong></div></td>											
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>VALOR</strong></div></td>
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>DB 1 CR 2</strong></div></td>
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>CEDULA / NIT. </strong></div></td>
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>BASE IVA - RF.</strong></div></td>
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>CENTRO DE COSTOS</strong></div></td>
							</tr>	
						<?php
						//OBTENER RESULTADO DEL QUERY 
						$resultado_valoresfte50=odbc_do($conex_o, $select_parafte50D);
						while(odbc_fetch_row($resultado_valoresfte50)){  
						$movdetcue = odbc_result($resultado_valoresfte50, 1);
						$movdetdoc = odbc_result($resultado_valoresfte50, 2);
						$movdetval = odbc_result($resultado_valoresfte50, 3);
						$ind = odbc_result($resultado_valoresfte50, 4);
						$bas = odbc_result($resultado_valoresfte50, 5);
						?>
							<tr>
								<td colspan="7" width="50" height="18"><?php echo $movdetcue ?> <div align="center"></div></td>
								<td colspan="7" width="20"><?php echo $movdetdoc ?></td>
								<td colspan="7" width="50"><?php echo number_format($movdetval,0,'',',') ?></td>
								<td colspan="7" width="50"><?php echo $ind ?></td>
								<td colspan="7" width="50"><?php echo $nit ?></td>
								<td colspan="7" width="50"><?php echo $bas ?></td>
								<td colspan="7" width="50"><?php echo '' ?></td>
							</tr>
						
						<?php
						}
						?>
						</table>
						<table width="1000" height="44" border="1" align="center">
							<tr>
								<td colspan="7" height="60" height="18"><strong><div style="padding-top:1px; height:100px"><?php echo 'CHEQUE No.' ?></strong><?php echo $che ?></div></td>
								<td colspan="7" height="60" height="18"><strong><div style="padding-top:1px; height:100px"><?php echo 'EFECTIVO     ' ?></strong><input type="checkbox" disabled="on"><label for="cbox2"></label></div></td>
								<td width="50"  height="60" colspan="7" rowspan="2"><div style="padding-top:1px; height:200px"><strong>FIRMA Y SELLO BENEFICIARIO</strong></div></td>
								
							</tr>
							<tr>
								<td colspan="7" height="60"><strong><div style="padding-top:1px; height:100px"><?php echo 'BANCO ' ?></strong><?php echo $ban.'-'.$bnom ?></div></td>
								<td colspan="7" height="60"><strong><div style="padding-top:1px; height:100px"><?php echo 'APROBADO' ?></strong></div></td>
							</tr>
						</table>
						<td>&ensp;</td>
						<div style="page-break-after: always"></div>
			<?php
					}
			}
			
		} // FINAL PARA LA FUENTE 50
		//PARA LA FUENTE 70
		if ($Fuente == '70'){
			 
			if($Metodo == 'Rango de Fechas')
				{
					//ENCABEZADO FECHAS FUENTE 70
					$select_parafte70 = "select movfue,movdoc,movnom,movnit,movval,movban,movche,movfec,bannom
										from cbmov,cbban
										where cbmov.movban = cbban.bancod
										and movfue='$Fuente'
										and movfec BETWEEN '$Fecha1' and '$Fecha2'";				
					$resultado_parafte70 = odbc_do($conex_o, $select_parafte70);
					while(odbc_fetch_row($resultado_parafte70)){ 
					//odbc_fetch_row($resultado_parafte70);
					$fue = odbc_result($resultado_parafte70, 1);
					$doc = odbc_result($resultado_parafte70, 2);
					$nom = odbc_result($resultado_parafte70, 3);
					$nit = odbc_result($resultado_parafte70, 4);
					$val = odbc_result($resultado_parafte70, 5);
					$ban = odbc_result($resultado_parafte70, 6);
					$che = odbc_result($resultado_parafte70, 7);
					$fec = odbc_result($resultado_parafte70, 8);
					$bnom = odbc_result($resultado_parafte70, 9);
					$select_parafte70D = "SELECT movconcue,movconane,movconval,movconind,movconbas
											FROM cbmovcon
											WHERE movconfue='$fue'
											AND movcondoc = '$doc'";
											//AND movcondoc between '$Documento_i' and '$Documento_f'";

					
						?>
						<table width="1000" border="1" align="center">
							<tr>
								<td colspan="4" style="background-color: #C3D9FF" height="100"> 
								&ensp;
								</td>
							</tr>
							<tr>
							  <td colspan="1"> 
			                    <div align="left">
	                                <?php if($Conexion == '01') { ?>
										<input type="image" id="btnVer" src="//mx.lasamericas.com.co/matrix/images/medical/paf/logo1.jpg" width="460" height="155">
									<?php }else{ ?>
										<input type="image" id="btnVer" src="//mx.lasamericas.com.co/matrix/images/medical/paf/logo2.jpg" width="460" height="155">
									<?php } ?>
	                            </div>
							  </td>
							  <td colspan="3" width="0"><font color="solido"><h4><strong>COMPROBANTE </br> DE EGRESO No. </strong></font><font color="maroon"><?php echo $doc ?> </h4></font> 
								</br>
									<strong>Fecha: </strong><?php echo $fec ?>
							  </td>
							</tr>
							<tr>
								<td colspan="2"><p align="left"><strong>A FAVOR DE: </strong><?php echo $nom ?></p></td>
								<td colspan="1"><p align="left"><strong>C.C. o NIT: </strong><?php echo $nit ?></p></td>
								<td colspan="1"><p align="left"><strong>POR: </strong><br/> $<?php echo number_format($val,0,'',',') ?></p></td>
							</tr>
							<tr height="60">
								<td colspan="4"><p><strong>CONCEPTO:</strong></p></td>
							</tr>
						</table>									<!-- PINTAR LOS VALORES -->
						<table width="1000" height="44" border="1" align="center">												
							<tr>	
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>IMPUTACION CONTABLE</strong></div></td>
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>DOCUMENTO REFERENCIA</strong></div></td>											
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>VALOR</strong></div></td>
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>DB 1 CR 2</strong></div></td>
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>CEDULA / NIT. </strong></div></td>
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>BASE IVA - RF.</strong></div></td>
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>CENTRO DE COSTOS</strong></div></td>
							</tr>	
						<?php
						//OBTENER RESULTADO DEL QUERY 
						$resultado_valoresfte70=odbc_do($conex_o, $select_parafte70D);
						while(odbc_fetch_row($resultado_valoresfte70)){  
						$movdetcue = odbc_result($resultado_valoresfte70, 1);
						$movdetdoc = odbc_result($resultado_valoresfte70, 2);
						$movdetval = odbc_result($resultado_valoresfte70, 3);
						$ind = odbc_result($resultado_valoresfte70, 4);
						$bas = odbc_result($resultado_valoresfte70, 5);
						?>
							<tr>
								<td colspan="7" width="50" height="18"><?php echo $movdetcue ?> <div align="center"></div></td>
								<td colspan="7" width="20"><?php echo $movdetdoc ?></td>
								<td colspan="7" width="50"><?php echo number_format($movdetval,0,'',',') ?></td>
								<td colspan="7" width="50"><?php echo $ind ?></td>
								<td colspan="7" width="50"><?php echo '' ?></td>
								<td colspan="7" width="50"><?php echo $bas ?></td>
								<td colspan="7" width="50"><?php echo '' ?></td>
							</tr>
						
						<?php
						}
						?>
						</table>
						<table width="1000" height="44" border="1" align="center">
							<tr>
								<td colspan="7" height="60" height="18"><strong><div style="padding-top:1px; height:100px"><?php echo 'CHEQUE No.' ?></strong><?php echo $che ?></div></td>
								<td colspan="7" height="60" height="18"><strong><div style="padding-top:1px; height:100px"><?php echo 'EFECTIVO     ' ?></strong><input type="checkbox" disabled="on"><label for="cbox2"></label></div></td>
								<td width="50"  height="60" colspan="7" rowspan="2"><div style="padding-top:1px; height:200px"><strong>FIRMA Y SELLO BENEFICIARIO</strong></div></td>
							</tr>
							<tr>
								<td colspan="7" height="60"><strong><div style="padding-top:1px; height:100px"><?php echo 'BANCO ' ?></strong><?php echo $ban.'-'.$bnom ?></div></td>
								<td colspan="7" height="60"><strong><div style="padding-top:1px; height:100px"><?php echo 'APROBADO' ?></strong></div></td>
							</tr>
						</table>
						<div style="page-break-after: always"></div>
			<?php
					}
				}else
					{
						//Encabezado
					$select_parafte70 = "select movfue,movdoc,movnom,movnit,movval,movban,movche,movfec,bannom
										from cbmov,cbban
										where cbmov.movban = cbban.bancod
										and movfue='$Fuente'
										and movdoc BETWEEN '$Documento_i' and '$Documento_f'";
					$resultado_parafte70 = odbc_do($conex_o, $select_parafte70);
					while(odbc_fetch_row($resultado_parafte70)){ 
					//odbc_fetch_row($resultado_parafte70);
					$fue = odbc_result($resultado_parafte70, 1);
					$doc = odbc_result($resultado_parafte70, 2);
					$nom = odbc_result($resultado_parafte70, 3);
					$nit = odbc_result($resultado_parafte70, 4);
					$val = odbc_result($resultado_parafte70, 5);
					$ban = odbc_result($resultado_parafte70, 6);
					$che = odbc_result($resultado_parafte70, 7);
					$fec = odbc_result($resultado_parafte70, 8);
					$bnom = odbc_result($resultado_parafte70, 9);
					$select_parafte70D = "SELECT movconcue,movconane,movconval,movconind,movconbas
											FROM cbmovcon
											WHERE movconfue='$fue'
											AND movcondoc = '$doc'";
											//AND movcondoc between '$Documento_i' and '$Documento_f'";

					
						?>
						<table width="1000" border="1" align="center">
							<tr>
								<td colspan="4" style="background-color: #C3D9FF" height="100"> 
								&ensp;
								</td>
							</tr>
							<tr>
							  <td colspan="1"> 
			                    <div align="left">
	                                <?php if($Conexion == '01') { ?>
										<input type="image" id="btnVer" src="//mx.lasamericas.com.co/matrix/images/medical/paf/logo1.jpg" width="460" height="155">
									<?php }else{ ?>
										<input type="image" id="btnVer" src="//mx.lasamericas.com.co/matrix/images/medical/paf/logo2.jpg" width="460" height="155">
									<?php } ?>
	                            </div>
							  </td>
							  <td colspan="3" width="0"><font color="solido"><h4><strong>COMPROBANTE </br> DE EGRESO No. </strong></font><font color="maroon"><?php echo $doc ?> </h4></font> 
								</br>
									<strong>Fecha: </strong><?php echo $fec ?>
							  </td>
							</tr>
							<tr>
								<td colspan="2"><p align="left"><strong>A FAVOR DE: </strong><?php echo $nom ?></p></td>
								<td colspan="1"><p align="left"><strong>C.C. o NIT: </strong><?php echo $nit ?></p></td>
								<td colspan="1"><p align="left"><strong>POR: </strong><br/> $<?php echo number_format($val,0,'',',') ?></p></td>
							</tr>
							<tr height="60">
								<td colspan="4"><p><strong>CONCEPTO:</strong></p></td>
							</tr>
						</table>									<!-- PINTAR LOS VALORES -->
						<table width="1000" height="44" border="1" align="center">												
							<tr>	
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>IMPUTACION CONTABLE</strong></div></td>
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>DOCUMENTO REFERENCIA</strong></div></td>											
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>VALOR</strong></div></td>
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>DB 1 CR 2</strong></div></td>
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>CEDULA / NIT. </strong></div></td>
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>BASE IVA - RF.</strong></div></td>
								<td colspan="7" width="50" style="background-color: #C3D9FF"><div align="center"><strong>CENTRO DE COSTOS</strong></div></td>
							</tr>	
						<?php
						//OBTENER RESULTADO DEL QUERY 
						$resultado_valoresfte70=odbc_do($conex_o, $select_parafte70D);
						while(odbc_fetch_row($resultado_valoresfte70)){  
						$movdetcue = odbc_result($resultado_valoresfte70, 1);
						$movdetdoc = odbc_result($resultado_valoresfte70, 2);
						$movdetval = odbc_result($resultado_valoresfte70, 3);
						$ind = odbc_result($resultado_valoresfte70, 4);
						$bas = odbc_result($resultado_valoresfte70, 5);
						?>
							<tr>
								<td colspan="7" width="50" height="18"><?php echo $movdetcue ?> <div align="center"></div></td>
								<td colspan="7" width="20"><?php echo $movdetdoc ?></td>
								<td colspan="7" width="50"><?php echo number_format($movdetval,0,'',',') ?></td>
								<td colspan="7" width="50"><?php echo $ind ?></td>
								<td colspan="7" width="50"><?php echo '' ?></td>
								<td colspan="7" width="50"><?php echo $bas ?></td>
								<td colspan="7" width="50"><?php echo '' ?></td>
							</tr>
						
						<?php
						}
						?>
						</table>
						<table width="1000" height="44" border="1" align="center">
							<tr>
								<td colspan="7" height="60" height="18"><strong><div style="padding-top:1px; height:100px"><?php echo 'CHEQUE No.' ?></strong><?php echo $che ?></div></td>
								<td colspan="7" height="60" height="18"><strong><div style="padding-top:1px; height:100px"><?php echo 'EFECTIVO     ' ?></strong><input type="checkbox" disabled="on"><label for="cbox2"></label></div></td>
								<td width="50"  height="60" colspan="7" rowspan="2"><div style="padding-top:1px; height:200px"><strong>FIRMA Y SELLO BENEFICIARIO</strong></div></td>
							</tr>
							<tr>
								<td colspan="7" height="60"><strong><div style="padding-top:1px; height:100px"><?php echo 'BANCO ' ?></strong><?php echo $ban.'-'.$bnom ?></div></td>
								<td colspan="7" height="60"><strong><div style="padding-top:1px; height:100px"><?php echo 'APROBADO' ?></strong></div></td>
							</tr>
						</table>
						<div style="page-break-after: always"></div>
			<?php
					}
			}
		} // FINAL PARA LA FUENTE 70
		
	// FINAL DEL CICLO BUSCADOR PRINCIPAL
	?>
	<script> window.print(); 
			 window.close(); 
	</script>
</body>
</html>