 <html>
<head>
  <title>MATRIX</title>
  <script>
		function consultar(wcod) {
			var validacion = null;
			ancho = 300;    alto = 120;
            var winl = (screen.width - ancho) / 2;
            var wint = 250;
			settings2 = 'height=' + alto + ',width=' + ancho + ',top=' + wint + ',left=' + winl + ', scrollbars=yes, toolbar=no';
			validacion = window.open ("validarMedicamento.php?wemp_pmla="+wemp_pmla.value+"&wcod="+wcod,"miwin",settings2);
			validacion.focus();
		}
   </script>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
 /**
 * PROGRAMA					  : Se toma el programa modelo etq_socket.php, y se procede a modificar el formulario 
								segun con los campos requeridos y se deja El mismo legunaje Zebra ZPL para generar y diseñar las etiquetas.	 			  		
 * AUTOR        			  : Didier Orozco Carmona.                                                                                       |
 * FECHA PUBLICACION	      : 2020-06-26.                                                                                             |
 * FECHA ULTIMA ACTUALIZACION : 2019-06-26. 
 *
 *									
 */
    include_once("conex.php");
    include_once("root/comun.php");
	$conex = obtenerConexionBD("matrix");
    $conex_o = odbc_connect('facturacion','','')  or die("No se realizo conexión con la BD de Facturación");
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cenmez');
	$wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
    if(!isset($_SESSION['user']))
    {
        ?>
<div align="center">
				<label>Usuario no autenticado en el sistema.<br />Recargue la pagina principal de Matrix o inicie sesion nuevamente.</label>
</div>
        <?php
        return;
    }
    else
    {
		$institucion = consultarInstitucionPorCodigo( $conex, $wemp_pmla );
		$wactualiz = "2019-06-26";
		encabezado( "GENERACION DE ETIQUETAS DE CODIGOS DE BARRAS", $wactualiz, $institucion->baseDeDatos );
        $user_session = explode('-', $_SESSION['user']);
        $wuse = $user_session[1];
        
        mysql_select_db("matrix");
        $conex = obtenerConexionBD("matrix");
    }

	function centroCostosCM()
	{
	
		global $conex;
		global $wmovhos;
		
		$sql = "SELECT
					Ccocod
				FROM
					".$wmovhos."_000011
				WHERE
					ccofac LIKE 'on' 
					AND ccotra LIKE 'on' 
					AND ccoima !='off' 
					AND ccodom !='on'
				";
		
		$res= mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		if ( mysql_num_rows($res) > 1 )
	{
		return "Hay más de 1 centro de costos con los mismos parámetros";
	}
	$rows = mysql_fetch_array( $res );
	return $rows[ 'Ccocod' ];
		
	} 

//---------------------------->>> CODIGO PARA SACAR FECHA Y LA HORA ACTUAL <<<----------------------------------------
$fecha_actual = date('d-m-Y');
$hora_actual = date('H:i');
//---------------------------->>> CAPTURAR LOS DATOS DEL FORMULARIO <<<----------------------------------------
$wcod = $_POST['wcod'];
$wlot=$_POST['wlot'];
$wlotm=$_POST['wlotm'];
$wnom = $_POST['wnom'];
$wetq = $_POST['wetq'];
$wip = $_POST['wip'];
$wnomc = $_POST['wnomc'];
$winv = $_POST['winv'];
$wacon = $_POST['wacon'];
$whor = $_POST['whor'];
$wfecv = $_POST['wfecv'];
$wres = $_POST['wres'];
$wapro = $_POST['wapro'];
$wvia = $_POST['wvia'];
$wobs = $_POST['wobs'];
//---------------------------->>> SACAR USUARIO ACTUAL NOMBRE Y FIRMA<<<----------------------------------------
$select_usuario = "SELECT descripcion,Firfir 
					from usuarios left join ".$wbasedato."_000023 on codigo = fircod
						WHERE codigo='{$wuse}'";
$resultado_usuario = mysql_query($select_usuario, $conex);
$res = mysql_fetch_array($resultado_usuario);
$user_nom = $res['descripcion'];
$respo_firma = $res['Firfir'];
			// $resultado_usuario=mysql_fetch_array($select_usuario);
				// $user_nom = substr($resultado_usuario[0],0,21);
				// $respo_firma = $resultado_usuario[1];
				//$concat_usuario = $wuse.'-'.$user_nom;

  	echo "<form action='impre_etiquetas.php?wemp_pmla=".$wemp_pmla."' method=post id='info_medicamento' name='info_medicamento'>";
  	echo "<INPUT TYPE='hidden' name='bd' value='$bd'>";
	echo "<INPUT TYPE='hidden' name='wemp_pmla' value='$wemp_pmla' id='wemp_pmla'>";
			echo "<center><table border=0>";
			//echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>CENTRAL DE MEZCLAS</td></tr>";
			//echo "<tr><td align=center colspan=2>GENERACION DE ETIQUETAS DE CODIGOS DE BARRAS</td></tr>";
			echo "<tr><td bgcolor=#cccccc>Codigo del Producto</td>";
			echo "<td bgcolor=#cccccc><input type='TEXT' id='wcod' value='' name='wcod' size=10 maxlength=10 onblur='consultar(this.value)'></td></tr>";
			
			echo "<tr><td bgcolor=#cccccc>Nombre Generico</td>";
			echo "<td bgcolor=#cccccc><input type='TEXT'  id='wnom' name='wnom' size=80 maxlength=80 readonly></td></tr>";
			
			echo "<tr><td bgcolor=#cccccc>Nombre Comercial</td>";
			echo "<td bgcolor=#cccccc><input type='TEXT' id='wnomc' name='wnomc' size=80 maxlength=80 readonly></td></tr>";
			
			echo "<tr><td bgcolor=#cccccc>Registro de Invima</td>";
			echo "<td bgcolor=#cccccc><input type='TEXT' id='winv' name='winv' size=20 maxlength=20></td></tr>";
			
			echo "<tr><td bgcolor=#cccccc>Fecha de Acondicionamiento</td>";
			echo "<td bgcolor=#cccccc><input type='text' value='".$fecha_actual."' name='wacon' size=20 maxlength=20></td></tr>";
			
			echo "<tr><td bgcolor=#cccccc>Hora</td>";
			echo "<td bgcolor=#cccccc><input type=text' value='".$hora_actual."' name='whor' size=10 maxlength=10></td></tr>";
			
			//echo "<tr><td bgcolor=#cccccc>Nro. de Lote Acondicionamiento</td>";
			//echo "<td bgcolor=#cccccc><input type='TEXT' id='wlot' name='wlot' size=20 maxlength=20 readonly></td></tr>";
			
			echo "<tr><td bgcolor=#cccccc>Nro. de Lote de Medicamento</td>";
			echo "<td bgcolor=#cccccc><input type='TEXT' id='wlotm' name='wlotm' size=20 maxlength=20></td></tr>";
			
			echo "<tr><td bgcolor=#cccccc>Fecha de Vencimiento:</td>";
			echo "<td bgcolor=#cccccc><input type='date' name='wfecv' size=20 maxlength=20></td></tr>";
			
			echo "<tr><td bgcolor=#cccccc>Acondiciona</td>";
			echo "<td bgcolor=#cccccc><input type='TEXT' value='".$user_nom."' name='wres' size=80 maxlength=80 readonly></td></tr>";
			
			echo "<tr><td bgcolor=#cccccc>Verifica</td>";
			?>
							<td bgcolor=#cccccc>						
									<select id="wapro" name="wapro">
										<?php
										$queryDetalle = "SELECT fircod,descripcion from usuarios, ".$wbasedato."_000023 
															where codigo = fircod
															and Activo = 'A'";
										$resutlDetalle = mysql_query($queryDetalle, $conex) or die (mysql_errno()." - en el query: ".$queryDetalle." - ".mysql_error());
											while($datoplantilla = mysql_fetch_assoc($resutlDetalle))
											{
												$Fircod = $datoplantilla['fircod'];
												$Descripcion = $datoplantilla['descripcion'];
												echo "<option value='".$Fircod."'>".$Descripcion."</option>";
											}
										?>
										   <option selected disabled>Seleccione...</option>
									</select>
							 
							</td>
			<?php
			echo "<tr><td bgcolor=#cccccc>Vía de Administración</td>";
			echo "<td bgcolor=#cccccc><input type='TEXT' name='wvia' size=15 maxlength=15 required></td></tr>";
			
			echo "<tr><td bgcolor=#cccccc>Observacion</td>";
			echo "<td bgcolor=#cccccc><input type='TEXT' name='wobs' size=80 maxlength=80></td></tr>";
			
			echo "<tr><td bgcolor=#cccccc>Numero de Etiquetas</td>";
			echo "<td bgcolor=#cccccc><input type='number' name='wetq' size=6 maxlength=6 min=1 max=500 required></td></tr>";	
			
			echo "<tr><td bgcolor=#cccccc>Numero de IP</td>";
			//echo "<td bgcolor=#cccccc><input type='TEXT' name='wip' size=15 maxlength=15 required></td></tr>";
			?>
							<td bgcolor=#cccccc>						
									<select id="wip" name="wip">
										<?php
										$cco = centroCostosCM();
										$queryIp = "SELECT Impcod,Impnip from root_000053 
															where Impcco = ".$cco."
															and Impest = 'on'";
															
										$resutlIp = mysql_query($queryIp, $conex) or die (mysql_errno()." - en el query: ".$queryIp." - ".mysql_error());
											while($datoIp = mysql_fetch_assoc($resutlIp))
											{
												$Impcod = $datoIp['Impcod'];    $Impnip = $datoIp['Impnip'];
												echo "<option value='".$Impnip."'>".$Impcod.'-'.$Impnip."</option>";
											}
										?>
										   <option selected disabled>Seleccione...</option>
									</select>
							 
							</td>
			<?php
			
			echo "<tr><td bgcolor=#cccccc colspan=2 align=center><input type='submit' value='ENTER' name='buscador'></td></tr></table>";
			
			
			echo "</form>";
			if 	($_POST['buscador']){			
			/////////////////////////////--> REALIZAR CONSULTA DE LA FIRMA DE QUIEN APRUEBA <--////////////////////////////////////////
			$select_aprobo = "SELECT descripcion,Firfir 
					from usuarios left join ".$wbasedato."_000023 on codigo = fircod
						WHERE codigo='{$wapro}'";
			$resultado_aprobo = mysql_query($select_aprobo, $conex);
			$res = mysql_fetch_array($resultado_aprobo);
			$apro_nom = $res['descripcion'];
			$apro_firma = $res['Firfir'];

			// $select_aprobo = mysql_queryV("SELECT descripcion,Firfir 
			// 					from usuarios left join ".$wbasedato."_000023 on codigo = fircod
			// 					where codigo='$wapro'");					
			// $resultado_aprobo=mysql_fetch_array($select_aprobo);
			// 	$apro_nom = substr($resultado_aprobo[0],0,21);
			// 	$apro_firma = $resultado_aprobo[1];
			// // ----------------------------------------------------------------------
			// http://labelary.com/viewer.html?density=8&width=50&height=38&units=mm&index=0&zpl=%5EXA%0A%5EFX%20Codigo%20de%20barras%0A%5EFO5%2C10%0A%5EBCN%2C70%2CN%2CN%5EFDNU41257%5EFS%0A%0A%5EFX%20Codigo%20%2Clote%20y%20fecha%20de%20vencimiento%0A%5ECFR%0A%5EFO240%2C10%5EFDNU40138%5EFS%0A%5ECFP%0A%5EFO240%2C40%5EFDLOTE%3A%20000001%5EFS%0A%5EFO240%2C60%5EFDF.V%3A%202018-02-16%5EFS%0A%0A%0A%5EFX%20Nombre%20del%20producto%0A%5ECFR%2C1%0A%5EFO5%2C85%5EFD123456789012345678901234567%5EFS%0A%5EFO5%2C115%5EFD123456789012345678901234567%5EFS%0A%0A%0A%5EFX%20Fecha%20y%20hora%20de%20preparaci%C3%B3n%0A%5ECFP%0A%5EFO10%2C150%5EFDF.%20PREP%3A%202018-02-16%5EFS%0A%5EFO190%2C150%5EFDH.%20PREP%3A%2017%3A52%3A36%5EFS%0A%0A%0A%5EFX%20Preparado%20por%0A%5ECFP%0A%5EFO10%2C175%5EFDPREPARADO%20POR%20QF%3A%5EFS%0A%5EFO10%2C200%5EFDDANIEL%20ANDRES%20GUARIN%5EFS%0A%0A%0A%5EFX%20Aprobado%20por%0A%5ECFP%0A%5EFO10%2C220%5EFDAPROBO%3A%5EFS%0A%5EFO10%2C240%5EFDSILVIA%20ELENA%20TORO%20ARIAS%5EFS%0A%0A%5EFX%20Tiempo%20de%20infusi%C3%B3n%20y%20nota%0A%5ECFP%0A%5EFO10%2C260%5EFDTIEMPO%20DE%20INFUSION%3A%2024%20horas%20y%2000%20minutos%5EFS%0A%5EFO10%2C280%5EFDConservar%20en%20nevera%20de%202%27%20a%208%27%20C%5EFS%0A%0A%5EFX%20Firma%20de%20quien%20prepara%0A%5EFO230%2C180%5EGFA%2C612%2C612%2C17%2C%2C%3A%3A%3AI07C%2C007IFU03F%2C03E001FT0E78%2C07J03CS0C3CI07F%2C0CK07R0180C001E1C%2C08K01CQ0180E001802%2CN07Q01806003%2CN018P01806002%2CN08EP01006002%2CN0C38O01806002%2CK018040CO01802002%2CK0180406P0802003%2CL0C0603P0C02003%2CL0C04019EK01E0C06001%2CL060400F1J013204040018%2CL0608006018001E2060C0018%2CL03J030387C0660238I0C%2CL018I010ECC703C03EJ0CFF6%2CL018I01FC783C1IF8J0FC1F%2CR0F0300CJ08I07B0038%2CR0CO0C001C18018%2CR0CO0700301C018%2CR04O0180C00603%2CR0CP0CJ0383%2C0018N0CP07K0FE%2CI03N0CP03EJ07%2CJ0FL01CQ07C%2CJ03CK038R078%2CK07CI01E%2CL0FF00FC%2CM07FFC%2C%2C%5EFS%0A%0A%0A%5EFX%20Firma%20de%20quien%20aprueba%0A%5EFO230%2C220%0A%5EGFA%2C272%2C272%2C8%2C%2C03%2C%3A038%2C078%2C0FC%2C0DE%2C0CE%2C0CF8N01C%2C0CFCM07FE%2C0C6EL03FFC%2C0667L07C%2C06338J01E%2C0631CJ01C%2C0230EJ038%2C03187J03%2C031838I07%2C018C18I06%2C018C0CI06%2C018F0EI06%2C00CFFEI06%2C00CIFI06038%2C004E1F80060FF%2C00660780071FB8%2C006607C003B818%2C007E03C001E018%2C003E00E001E018%2C001E00EI0F038%2CI0E004I07C3%2CI08L01FF%2CQ07C%2C%2C%3A%3A%5EFS%0A%0A%0A%5EXZ
			// http://labelary.com/viewer.html?density=8&width=50&height=38&units=mm&index=0&zpl=%0A%5EXA%0A%5EFX%20Codigo%20de%20barras%0A%5EFO12%2C10%0A%5EBCN%2C70%2CN%2CN%5EFDNU37400%5EFS%0A%0A%5EFX%20Codigo%20%2Clote%20y%20fecha%20de%20vencimiento%0A%5ECFR%0A%5EFO240%2C10%5EFDNU37400%5EFS%0A%5ECFP%0A%5EFO240%2C40%5EFDLOTE%3A%20000007%5EFS%0A%5EFO240%2C60%5EFDF.V%3A%202018-09-16%5EFS%0A%0A%0A%5EFX%20Nombre%20del%20producto%0A%5ECFR%2C1%0A%5EFO10%2C82%5EFDNutricion%20Parenteral%5EFS%0A%5EFO10%2C107%5EFDprueba%5EFS%0A%0A%0A%5EFX%20Fecha%20y%20hora%20de%20preparaci%C3%B3n%0A%5ECFP%0A%5EFO10%2C142%5EFDF.%20PREP%3A%202018-09-13%5EFS%0A%5EFO190%2C142%5EFDH.%20PREP%3A%2010%3A29%3A32%5EFS%0A%0A%0A%5EFX%20Preparado%20por%0A%5ECFP%0A%5EFO10%2C162%5EFDPREPARADO%20POR%20QF%3A%5EFS%0A%5EFO10%2C180%5EFDDANIEL%20ANDRES%20GUARIN%20ECHA%5EFS%0A%0A%0A%5EFX%20Aprobado%20por%0A%5ECFP%0A%5EFO10%2C200%5EFDAPROBO%3A%5EFS%0A%5EFO10%2C220%5EFDSANDRA%20MILENA%20RODRIGUEZ%20S%5EFS%0A%0A%5EFX%20Tiempo%20de%20infusi%C3%B3n%20y%20nota%0A%5ECFQ%0A%5EFO10%2C240%5EFDTIEMPO%20INFUSION%3A%2024%20horas%20y%2000%20minutos%5EFS%0A%5EFO10%2C265%5EFDConservar%20en%20nevera%20de%202%27%20a%208%27%20C%5EFS%0A%0A%5EFX%20Firma%20de%20quien%20prepara%0A%5EFO230%2C162%0A%5EGFA%2C612%2C612%2C17%2C%2C%3A%3A%3AI07C%2C007IFU03F%2C03E001FT0E78%2C07J03CS0C3CI07F%2C0CK07R0180C001E1C%2C08K01CQ0180E001802%2CN07Q01806003%2CN018P01806002%2CN08EP01006002%2CN0C38O01806002%2CK018040CO01802002%2CK0180406P0802003%2CL0C0603P0C02003%2CL0C04019EK01E0C06001%2CL060400F1J013204040018%2CL0608006018001E2060C0018%2CL03J030387C0660238I0C%2CL018I010ECC703C03EJ0CFF6%2CL018I01FC783C1IF8J0FC1F%2CR0F0300CJ08I07B0038%2CR0CO0C001C18018%2CR0CO0700301C018%2CR04O0180C00603%2CR0CP0CJ0383%2C0018N0CP07K0FE%2CI03N0CP03EJ07%2CJ0FL01CQ07C%2CJ03CK038R078%2CK07CI01E%2CL0FF00FC%2CM07FFC%2C%2C%5EFS%0A%0A%0A%0A%5EFX%20Firma%20de%20quien%20aprueba%0A%5EFO230%2C200%0A%5EGFA%2C805%2C805%2C23%2C%2C%3AgW01F8%2CgW078C%2CgV01C04%2CgV03004%2CgV0600C%2CgV0C008%2CgG08N0F006018018%2Cg018N09C0607006%2Cg018M01070F0600C%2CK01ET0CM0301CB0603%2CJ01FF8R01CM01007F060E%2CJ0F01CS0CM01801F0618%2CI03801CS0CM018031063%2CI060018S04M018061063FFE%2CI060018S06N081C10201CFF%2CI0400188R060E0F0C00838106J038%2CI0603IFEQ020IFC800830187J01C%2CI03FEI03C3F1019F800218C86800820083J016%2CP07JF9B880FF18801C008J03J01C%2CP038I01B04FDFB8803C008N0FF%2CP034I01B07003F8C066N01FFE%2CP01EI07B06001D861C2L01FFE%2CI02M07IFDB87IF183FL0IF8%2C03F8FF8J06J0F063FC18K0BFFE%2C1EJ0FEI04J0704M01IFA%2C18K03F83CN03NF7FQ0C%2CN01FFM03KFK01JFCL01FE%2CV07FFCT0IF8K0E%2CU0IFY07IF006%2CT03FgJ017F4%2C%2C%3A%3A%5EFS%0A%0A%5EFX%20Cantidad%20de%20etiquetas%20a%20imprimir%0A%5EPQ2%0A%0A%5EXZ
			$impresionZPL ="^XA
							^FX Codigo de barras
							^FO10,10
							^BCN,70,N,N^FD".$wcod."^FS

							^FX Codigo, lote Acondicionamiento y invima
							^CFR
							^FO240,10^FD".$wcod."^FS
							
							^FX Lote medicamento y fecha vencimiento
							^CFP
							^FO240,40^FDLOTE M:".$wlotm."^FS
							^FO240,60^FDF.V: ".$wfecv."^FS

							^FX Nombre del producto
							^CFP
							^FO10,85^FD".$wnom."^FS
							^FO10,110^FD".$wnomc."^FS

							^FX Fecha y hora Acondicionamiento y lote de medicamento
							^CFP
							^FO10,140^FDF. ACON: ".$wacon."^FS
							^FO190,140^FDH. ACOND: ".$whor."^FS
							^FO10,160^FDINVIMA: ".$winv."^FS
							
							^FX Acondiciona
							^CFP
							^FO10,180^FDACONDICIONA:^FS
							^FO10,200^FD".$wres."^FS

							^FX Verifico
							^CFP
							^FO10,220^FDVERIFICO:^FS
							^FO10,240^FD".$apro_nom."^FS

							^FX Via
							^CFP
							^FO10,260^FDVIA:".$wvia."^FS                                                                                                            
	                        
							^FX Observacion
							^CFP
							^FO10,280^FDObservacion:".$wobs."^FS                                                                                 
                                              

							^FX Firma de Acondiciona
							^FO240,180
							".$respo_firma."

							^FX Firma de Verifico
							^FO240,220
							".$apro_firma."

							^FX Cantidad de etiquetas a imprimir
							^PQ".$wetq."

							^XZ";
							
			
							//echo "<pre>".print_r($impresionZPL,true)."</pre>";
							//echo $impresionZPL;
			$addr=$wip;
			$fp = fsockopen( $addr,9100, $errno, $errstr, 30);
			if(!$fp) 
			echo "ERROR : "."$errstr ($errno)<br>\n";
			else 
			{
			fputs($fp,$impresionZPL);
			echo "PAQUETE ENVIADO <br>\n";
			fclose($fp);
			}
			sleep(5);			
		}
?>
</body>
</html>