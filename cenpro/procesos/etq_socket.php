 <html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
/**
 * Acutalizacion:
 *
 * Septiembre 14 de 2018	Jessica 	Se aumenta el tamaño de la letra del TIEMPO DE INFUSION y la nota de CONSERVAR EN NEVERA.
 * Abril 18 de 2018			Jessica 	En la impresión de la etiqueta en zpl la posicion inicial de algunos elementos que estaban 
 *										a 5 puntos se modifican a 10 puntos para evitar que en algunas impresoras no se visualice 
 *										correctamente el codigo de barras
 * Febrero 20 de 2018		Jessica 	Se comenta la impresión de la etiqueta en html y se agrega la impresión de la etiqueta en zpl
 * Febrero 15 de 2018		Jessica 	Se comenta la impresión de la etiqueta en epl ya que se agrega la etiqueta en html en el 
 *										script stickerProductos.php porque se deben imprimir las firmas.
 * Enero 24 de 2018			Jessica 	Modificacion en la impresión de la etiqueta, se quitan algunos espacios ya que algunos 
 *										datos no quedaban legibles
 * Enero 23 de 2018			Jessica 	Modificaciones en la impresión de la etiqueta, se cambian los textos:
 * 										- POR: - PREPARADO POR QF: y REVISADO POR: - APROBO:
 * Enero 22 de 2018			Jessica 	Modificaciones en la impresión de la etiqueta: 
 *										- Se cambia el orden de algunos campos (Codigo del producto, lote y fecha de vencimiento,quien prepara y quien revisa)
 *										- Se agregan nuevos datos (Hora de preparación, tiempo de infusión y una nota fija para todos los productos)
 * Septiembre 12 de 2017	Jessica 	Se registra la preparación de las dosis adaptadas despues de imprimir los stickers.	
 * Diciembre 10 de 2012.	Edwin MG.	Al imprimir quien elabora el lote (Aparece despues de  "Por:" ), se aumenta la cantidad de caracteres a imprimir de 27 a 38 
 *										y se disminuye el tamaño de la letra, estaba en 2 y queda en 1.
 *										
 */
 
function consultarDatosLote($codProducto,$lote)
{
	global $conex;
	
	// En el editor online de ZPL se cargan y se obtiene la firma que se almacena en cenpro_000023
	// http://labelary.com/viewer.html
	
	// La firma no debe exceder las siguientes dimensiones
	// ancho: 160px y alto: 45px
	
	$queryUsuarios = "SELECT Ploela AS CodigoElabora,Plorev AS CodigoRevisa,b.Descripcion AS NombreElabora,c.Descripcion AS NombreRevisa,Plofcr AS FechaPreparacion,a.Hora_data AS HoraPreparacion,d.Firfir AS FirmaElabora,e.Firfir AS FirmaRevisa
						FROM cenpro_000004 a
				   LEFT JOIN usuarios b
						  ON b.Codigo=Ploela
				   LEFT JOIN usuarios c
						  ON c.Codigo=Plorev
				   LEFT JOIN cenpro_000023 d
						  ON d.Fircod=Ploela
				   LEFT JOIN cenpro_000023 e
						  ON e.Fircod=Plorev	  
					   WHERE Plopro='".$codProducto."' 
						 AND Plocod='".$lote."';";
					 
	$resUsuarios = mysql_query($queryUsuarios,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$queryUsuarios." - ".mysql_error());
	$numUsuarios = mysql_num_rows($resUsuarios);
	
	$arrayUsuarios = array();
	if($numUsuarios > 0)
	{
		$rowsUsuarios = mysql_fetch_array($resUsuarios);
		
		$arrayUsuarios['CodigoElabora'] = $rowsUsuarios['CodigoElabora'];
		$arrayUsuarios['CodigoRevisa'] = $rowsUsuarios['CodigoRevisa'];
		$arrayUsuarios['NombreElabora'] = substr($rowsUsuarios['NombreElabora'],0,25);
		$arrayUsuarios['NombreRevisa'] = substr($rowsUsuarios['NombreRevisa'],0,25);
		$arrayUsuarios['FechaPreparacion'] = $rowsUsuarios['FechaPreparacion'];
		$arrayUsuarios['HoraPreparacion'] = $rowsUsuarios['HoraPreparacion'];
		$arrayUsuarios['FirmaElabora'] = $rowsUsuarios['FirmaElabora'];
		$arrayUsuarios['FirmaRevisa'] = $rowsUsuarios['FirmaRevisa'];
	}
	
	return $arrayUsuarios;
}

function consultarTiempoInfusion($codigo)
{
	global $conex;
	global $wbasedato;
	
	$queryTiempoInsusion = " SELECT Arttin 
							   FROM ".$wbasedato."_000002 
							  WHERE Artcod='".$codigo."';";

	$resTiempoInsusion = mysql_query($queryTiempoInsusion,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$queryTiempoInsusion." - ".mysql_error());
	$numTiempoInsusion = mysql_num_rows($resTiempoInsusion);
	
	$tiempoInfusion = "";
	if($numTiempoInsusion > 0)
	{
		$rowsTiempoInsusion = mysql_fetch_array($resTiempoInsusion);
		
		if($rowsTiempoInsusion['Arttin']>0)
		{
			$tfd = floor($rowsTiempoInsusion['Arttin']/60);
			$tfh = $rowsTiempoInsusion['Arttin']%60;
			
			if($tfd>0)
			{
				$tfd = $tfd." horas y ";
			}
			else
			{
				$tfd = "";
			}
			
			if($tfh<10)
			{
				$tfh = "0".$tfh;
			}
			
			$tiempoInfusion = $tfd.$tfh." minutos";
		}
		
		
	}
	
	return $tiempoInfusion;
}

function registrarPreparacion($historia,$ingreso,$ido,$wnom1,$wnom2,$codDA,$wlot,$wfpr,$ronda,$fechaPreparacion)
{
	global $conex;
	global $wbasedato;
	global $bd;
	global $key;
	
	$queryDA = " SELECT *
				   FROM cenpro_000022
				  WHERE Prehis='".$historia."' 
					AND Preing='".$ingreso."' 
					AND Precod='".$codDA."' 
					AND Preido='".$ido."' 
					AND Prefec='".$fechaPreparacion."' 
					AND Preron='".$ronda."' 
					AND Preest='on';";

	$resDA = mysql_query($queryDA,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$queryDA." - ".mysql_error());
	$numDA = mysql_num_rows($resDA);
	
	if($numDA > 0)
	{
		$mensajeInsert = "La ronda: ".$ronda." del ".$fechaPreparacion." para la historia: ".$historia."-".$ingreso." ya fue enviada al monitor de preparacion";
	}
	else
	{
		$qInsert = "INSERT INTO ".$wbasedato."_000022 (Medico,Fecha_data,Hora_data,Prehis,Preing,Precod,Preido,Prefec,Preron,Prelot,Preno1,Preno2,Preuip,Prefip,Prehip,Prerea,Preest,Seguridad) VALUES ('".$wbasedato."','".date('Y-m-d')."','".date("H:i:s")."','".$historia."','".$ingreso."','".$codDA."','".$ido."','".$fechaPreparacion."','".$ronda."','".$wlot."','".$wnom1."','".$wnom2."','".$key."','".date('Y-m-d')."','".date("H:i:s")."','off','on','C-".$wbasedato."');";
		
		$resInsert = mysql_query($qInsert,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$qInsert." - ".mysql_error());	
		
		$mensajeInsert = "";
		if($resInsert)
		{
			$mensajeInsert = "Se envío el producto: ".$wcod." al monitor de preparacion";
		}
		else
		{
			$mensajeInsert = "Error, no se pudo enviar el producto: ".$wcod." al monitor de preparacion";
		}
	}
	
	return $mensajeInsert;
}

function consultarNombreProductos( &$wnom1, &$wnom2, $cod ){
	
	global $conex;
	global $wbasedato;
	
	$wnom1 = "";
	$wnom2 = "";
	
	$sql = "SELECT
				artcom, artgen
			FROM
				{$wbasedato}_000026
			WHERE
				artcod = '$cod'";
				
	$res = mysql_query( $sql, $conex ) or die( "Error en el query $sql") ;
	
	if( $rows = mysql_fetch_array($res) ){
		$wnom1 = $rows[0];
		$wnom2 = $rows[1];
	}
	
	return;
}

/**
 * Busca el nombre del que elabora el lote
 * 
 * @param $producto			Código del producto
 * @param $lote				Nro del lote
 * @return String
 */
function elaboradorLote( $producto, $lote, &$fcr, &$hpr ){
	 
	global $conex;
	global $wbasedato;
	
	$nombre = '';
	$fcr = "";
	$hpr = "";
	 
	 $sql = "SELECT
	 			descripcion, plofcr,Hora_data
	 		 FROM
	 		 	{$wbasedato}_000004, usuarios
	 		 WHERE
	 		 	plopro = '$producto'
	 		 	AND plocod = '$lote'
	 		 	AND ploela = codigo";
	 
	 $res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en la consulta $sql - ".mysql_error() );
	 
	 if( $rows = mysql_fetch_array( $res ) ){
	 	$nombre = $rows[0];
	 	$fcr = $rows[1];	 	
	 	$hpr = $rows[2];	 	
	 }
	 
	 return $nombre;
}

$wemp_pmla="01";
$soloconsulta=true;

include_once("root/comun.php");
include_once(get_include_path()."/../matrix/cenpro/procesos/monitorProduccionDA.php");

$conex = obtenerConexionBD("matrix");

if( !isset($bd) || empty($bd) ){
	$bd = "cenmez";
}

$wbasedato = consultarAliasPorAplicacion( $conex, "01", $bd );

session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
  	$key = substr($user,2,strlen($user));
//  	

//	

  	echo "<form action='etq_socket.php' method=post>";
  	echo "<INPUT TYPE='hidden' name='bd' value='$bd'>";
  	echo "<INPUT TYPE='hidden' name='whistoria' value='".$whistoria."'>";
  	echo "<INPUT TYPE='hidden' name='wingreso' value='".$wingreso."'>";
  	echo "<INPUT TYPE='hidden' name='wido' value='".$wido."'>";
  	echo "<INPUT TYPE='hidden' name='wronda' value='".$wronda."'>";
  	echo "<INPUT TYPE='hidden' name='wfecharonda' value='".$wfecharonda."'>";
  	if(!isset($wcod) or !isset($wlot) or !isset($wfev) or !isset($wnom) or !isset($wetq) or !isset($wip))
	{
		
		if( $bd != "movhos" ){	//OPCIONES DE CENTRAL DE MEZCLAS
			
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>CENTRAL DE MEZCLAS</td></tr>";
			echo "<tr><td align=center colspan=2>GENERACION DE STIKERS DE CODIGOS DE BARRAS</td></tr>";
			echo "<tr><td bgcolor=#cccccc>Codigo del Producto</td>";
			echo "<td bgcolor=#cccccc><input type='TEXT' name='wcod' size=10 maxlength=10></td></tr>";
			echo "<tr><td bgcolor=#cccccc>Nro. de Lote</td>";
			echo "<td bgcolor=#cccccc><input type='TEXT' name='wlot' size=20 maxlength=20></td></tr>";
			echo "<tr><td bgcolor=#cccccc>Fecha de Vencimiento</td>";
			echo "<td bgcolor=#cccccc><input type='TEXT' name='wfev' size=10 maxlength=10></td></tr>";
			echo "<tr><td bgcolor=#cccccc>Nombre del Producto</td>";
			echo "<td bgcolor=#cccccc><input type='TEXT' name='wnom' size=80 maxlength=80></td></tr>";
			echo "<tr><td bgcolor=#cccccc>Numero de Etiquetas</td>";
			echo "<td bgcolor=#cccccc><input type='TEXT' name='wetq' size=6 maxlength=6></td></tr>";	
			echo "<tr><td bgcolor=#cccccc>Numero de IP</td>";
			echo "<td bgcolor=#cccccc><input type='TEXT' name='wip' size=15 maxlength=15></td></tr>";
			echo "<tr><td bgcolor=#cccccc colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else{	//OPCIONES DEL SERVICIO FARMACEUTICO
			
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>SERVICIO FARMACEUTICO</td></tr>";
			echo "<tr><td align=center colspan=2>GENERACION DE STIKERS DE CODIGOS DE BARRAS</td></tr>";
			echo "<tr><td bgcolor=#cccccc>Codigo del Producto</td>";
			echo "<td bgcolor=#cccccc><input type='TEXT' name='wcod' size=10 maxlength=10></td></tr>";
			echo "<tr><td bgcolor=#cccccc>Nro. de Lote</td>";
			echo "<td bgcolor=#cccccc><input type='TEXT' name='wlot' size=20 maxlength=20></td></tr>";
			echo "<tr><td bgcolor=#cccccc>Fecha de Vencimiento</td>";
			echo "<td bgcolor=#cccccc>";
			campoFechaDefecto( "wfev", "" ); 
			echo "</td></tr>";
			echo "<tr><td bgcolor=#cccccc>Fecha de Preparacion</td>";
			echo "<td bgcolor=#cccccc>";
			campoFechaDefecto( "wfpr", "" ); 
			echo "</td></tr>";
			echo "<tr><td bgcolor=#cccccc>Elaborado por</td>";
			echo "<td bgcolor=#cccccc><input type='TEXT' name='wela' size=27 maxlength=13></td></tr>";
			echo "<tr style='display:none;'><td bgcolor=#cccccc>Revisado Por</td>";
			echo "<td bgcolor=#cccccc><input type='TEXT' name='wrev' size=27 maxlength=13></td></tr>";
			echo "<tr><td bgcolor=#cccccc>Numero de Etiquetas</td>";
			echo "<td bgcolor=#cccccc><input type='TEXT' name='wetq' size=6 maxlength=6></td></tr>";	
			echo "<tr><td bgcolor=#cccccc>Numero de IP</td>";
			echo "<td bgcolor=#cccccc><input type='TEXT' name='wip' size=15 maxlength=15 value='132.1.18.98'></td></tr>";
			echo "<tr><td bgcolor=#cccccc colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
			
			echo "<INPUT TYPE='hidden' name='ok' value='1'>";
			echo "<INPUT TYPE='hidden' name='wnom' value='1'>";
		}
	}
	else
	{
		if(!isset($ok) && $bd != "movhos" )
		{
			$ok=1;
			
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>CENTRAL DE MEZCLAS</td></tr>";
			echo "<tr><td align=center colspan=2>GENERACION DE STIKERS DE CODIGOS DE BARRAS</td></tr>";
			echo "<tr><td align=center colspan=2>Confirmacion De Nombre</td></tr>";
			
			if($whistoria!="" && $wingreso!="" && $wido!="")
			{
				$datosPreparacion = consultarInsumosPreparacion($conex,"movhos",$wbasedato,$wcod,$wlot,$whistoria,$wingreso);
				
				echo "<tr><td align=center colspan=2 style='font-size:8pt;font-weight:bold;'>&nbsp;</td></tr>";
				echo "<tr><td align=center colspan=2 style='font-size:8pt;font-weight:bold;'>Dosis adaptada: ".$wcod."</td></tr>";
				echo "<tr><td align=center colspan=2 style='font-size:8pt;font-weight:bold;'>Historia: ".$whistoria."-".$wingreso."</td></tr>";
				if($datosPreparacion[0]['volAdministrar']!="")
				{
					echo "<tr><td align=center colspan=2 style='font-size:8pt;font-weight:bold;'>VOLUMEN A ADMINISTRAR AL PACIENTE DA: ".$datosPreparacion[0]['volAdministrar']." ML</td></tr>";
				}
			}
			
			
			echo "<input type='HIDDEN' name= 'wcod' value='".$wcod."'>";
			echo "<input type='HIDDEN' name= 'wlot' value='".$wlot."'>";
			echo "<input type='HIDDEN' name= 'wfev' value='".$wfev."'>";
			//echo "<input type='HIDDEN' name= 'wetq' value='".$wetq."'>";
			echo "<input type='HIDDEN' name= 'wnom' value='".$wnom."'>";
			echo "<input type='HIDDEN' name= 'wip' value='".$wip."'>";
			echo "<input type='HIDDEN' name= 'ok' value='".$ok."'>";
			
//			echo "<input type='HIDDEN' name= 'wfev' value='".$wfev."'>";
			
			
			
			echo "<tr><td bgcolor=#cccccc align=center>Parte 1 Nombre del Producto</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wnom1' size=27 maxlength=27 value='".substr($wnom,0,27)."'></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Parte 2 Nombre del Producto</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wnom2' size=27 maxlength=27 value='".substr($wnom,27,27)."'></td></tr>";
			
			if( !isset($wrev) ){
				$wrev = "";
			}
			
			echo "<tr style='display:none;'><td bgcolor=#cccccc align=center>Revisado Por</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wrev' size=27 maxlength=38></td></tr>";
			
			echo "<tr><td bgcolor=#cccccc align=center>Cantidad a Imprimir: </td>";echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wetq' size=27 maxlength=27 value='".$wetq."'></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
			
		}
		else
		{
			if( $bd == "movhos" ){
				consultarNombreProductos( $wnom1, $wnom2, $wcod );
			}
			
			if( $bd != "movhos" ){
				// $wela = elaboradorLote( $wcod, $wlot, $wfpr, $whpr );											//Se busca quien elabora el lote
				// //echo "<br>.....: ".$wela = substr($wela,0,38);
				
				$arrayLote = consultarDatosLote( $wcod, $wlot );											//Se busca quien elabora el lote
				
				$wela = $arrayLote['NombreElabora'];
				$wrev = $arrayLote['NombreRevisa'];
				$wfpr = $arrayLote['FechaPreparacion'];
				$whpr = $arrayLote['HoraPreparacion'];
				$codEla = $arrayLote['CodigoElabora'];
				$codRev = $arrayLote['CodigoRevisa'];
				$firmaEla = $arrayLote['FirmaElabora'];
				$firmaRev = $arrayLote['FirmaRevisa'];
			}
			
			if($whpr=="")
			{
				$whpr = date("H:i:s");
			}
			
			$wcod = strtoupper($wcod);
			// ----------------------------------------------------------------------
			
			if($whistoria!="" && $wingreso!="" && $wido!="")
			{
				registrarPreparacion($whistoria,$wingreso,$wido,$wnom1,$wnom2,$wcod,$wlot,$wfpr,$wronda,$wfecharonda);
			}
			
			// echo "	<script>
						// var path = 'stickerProductos.php?wemp_pmla=01&codigo=".$wcod."&lote=".$wlot."&nombre1=".$wnom1."&nombre2=".$wnom2."&fechaVencimiento=".$wfev."&fechaPrep=".$wfpr."&horaPrep=".$whpr."&cantidad=".$wetq."';
						// window.open(path,'','fullscreen=no, status=no, menubar=no, toolbar=no, directories=no, resizable=yes, scrollbars=yes,titlebar=yes');
						// // window.close();
					// </script>";
					
			// echo "PAQUETE ENVIADO <br>\n";
			
			// return;
			
			
			$tiempoInfusion = consultarTiempoInfusion($wcod);
			if($tiempoInfusion!="")
			{
				$tiempoInfusion = "TIEMPO INFUSION:".$tiempoInfusion;
			}
			
			// return;
		
			// ----------------------------------------------------------------------
			// http://labelary.com/viewer.html?density=8&width=50&height=38&units=mm&index=0&zpl=%5EXA%0A%5EFX%20Codigo%20de%20barras%0A%5EFO5%2C10%0A%5EBCN%2C70%2CN%2CN%5EFDNU41257%5EFS%0A%0A%5EFX%20Codigo%20%2Clote%20y%20fecha%20de%20vencimiento%0A%5ECFR%0A%5EFO240%2C10%5EFDNU40138%5EFS%0A%5ECFP%0A%5EFO240%2C40%5EFDLOTE%3A%20000001%5EFS%0A%5EFO240%2C60%5EFDF.V%3A%202018-02-16%5EFS%0A%0A%0A%5EFX%20Nombre%20del%20producto%0A%5ECFR%2C1%0A%5EFO5%2C85%5EFD123456789012345678901234567%5EFS%0A%5EFO5%2C115%5EFD123456789012345678901234567%5EFS%0A%0A%0A%5EFX%20Fecha%20y%20hora%20de%20preparaci%C3%B3n%0A%5ECFP%0A%5EFO10%2C150%5EFDF.%20PREP%3A%202018-02-16%5EFS%0A%5EFO190%2C150%5EFDH.%20PREP%3A%2017%3A52%3A36%5EFS%0A%0A%0A%5EFX%20Preparado%20por%0A%5ECFP%0A%5EFO10%2C175%5EFDPREPARADO%20POR%20QF%3A%5EFS%0A%5EFO10%2C200%5EFDDANIEL%20ANDRES%20GUARIN%5EFS%0A%0A%0A%5EFX%20Aprobado%20por%0A%5ECFP%0A%5EFO10%2C220%5EFDAPROBO%3A%5EFS%0A%5EFO10%2C240%5EFDSILVIA%20ELENA%20TORO%20ARIAS%5EFS%0A%0A%5EFX%20Tiempo%20de%20infusi%C3%B3n%20y%20nota%0A%5ECFP%0A%5EFO10%2C260%5EFDTIEMPO%20DE%20INFUSION%3A%2024%20horas%20y%2000%20minutos%5EFS%0A%5EFO10%2C280%5EFDConservar%20en%20nevera%20de%202%27%20a%208%27%20C%5EFS%0A%0A%5EFX%20Firma%20de%20quien%20prepara%0A%5EFO230%2C180%5EGFA%2C612%2C612%2C17%2C%2C%3A%3A%3AI07C%2C007IFU03F%2C03E001FT0E78%2C07J03CS0C3CI07F%2C0CK07R0180C001E1C%2C08K01CQ0180E001802%2CN07Q01806003%2CN018P01806002%2CN08EP01006002%2CN0C38O01806002%2CK018040CO01802002%2CK0180406P0802003%2CL0C0603P0C02003%2CL0C04019EK01E0C06001%2CL060400F1J013204040018%2CL0608006018001E2060C0018%2CL03J030387C0660238I0C%2CL018I010ECC703C03EJ0CFF6%2CL018I01FC783C1IF8J0FC1F%2CR0F0300CJ08I07B0038%2CR0CO0C001C18018%2CR0CO0700301C018%2CR04O0180C00603%2CR0CP0CJ0383%2C0018N0CP07K0FE%2CI03N0CP03EJ07%2CJ0FL01CQ07C%2CJ03CK038R078%2CK07CI01E%2CL0FF00FC%2CM07FFC%2C%2C%5EFS%0A%0A%0A%5EFX%20Firma%20de%20quien%20aprueba%0A%5EFO230%2C220%0A%5EGFA%2C272%2C272%2C8%2C%2C03%2C%3A038%2C078%2C0FC%2C0DE%2C0CE%2C0CF8N01C%2C0CFCM07FE%2C0C6EL03FFC%2C0667L07C%2C06338J01E%2C0631CJ01C%2C0230EJ038%2C03187J03%2C031838I07%2C018C18I06%2C018C0CI06%2C018F0EI06%2C00CFFEI06%2C00CIFI06038%2C004E1F80060FF%2C00660780071FB8%2C006607C003B818%2C007E03C001E018%2C003E00E001E018%2C001E00EI0F038%2CI0E004I07C3%2CI08L01FF%2CQ07C%2C%2C%3A%3A%5EFS%0A%0A%0A%5EXZ
			// http://labelary.com/viewer.html?density=8&width=50&height=38&units=mm&index=0&zpl=%0A%5EXA%0A%5EFX%20Codigo%20de%20barras%0A%5EFO12%2C10%0A%5EBCN%2C70%2CN%2CN%5EFDNU37400%5EFS%0A%0A%5EFX%20Codigo%20%2Clote%20y%20fecha%20de%20vencimiento%0A%5ECFR%0A%5EFO240%2C10%5EFDNU37400%5EFS%0A%5ECFP%0A%5EFO240%2C40%5EFDLOTE%3A%20000007%5EFS%0A%5EFO240%2C60%5EFDF.V%3A%202018-09-16%5EFS%0A%0A%0A%5EFX%20Nombre%20del%20producto%0A%5ECFR%2C1%0A%5EFO10%2C82%5EFDNutricion%20Parenteral%5EFS%0A%5EFO10%2C107%5EFDprueba%5EFS%0A%0A%0A%5EFX%20Fecha%20y%20hora%20de%20preparaci%C3%B3n%0A%5ECFP%0A%5EFO10%2C142%5EFDF.%20PREP%3A%202018-09-13%5EFS%0A%5EFO190%2C142%5EFDH.%20PREP%3A%2010%3A29%3A32%5EFS%0A%0A%0A%5EFX%20Preparado%20por%0A%5ECFP%0A%5EFO10%2C162%5EFDPREPARADO%20POR%20QF%3A%5EFS%0A%5EFO10%2C180%5EFDDANIEL%20ANDRES%20GUARIN%20ECHA%5EFS%0A%0A%0A%5EFX%20Aprobado%20por%0A%5ECFP%0A%5EFO10%2C200%5EFDAPROBO%3A%5EFS%0A%5EFO10%2C220%5EFDSANDRA%20MILENA%20RODRIGUEZ%20S%5EFS%0A%0A%5EFX%20Tiempo%20de%20infusi%C3%B3n%20y%20nota%0A%5ECFQ%0A%5EFO10%2C240%5EFDTIEMPO%20INFUSION%3A%2024%20horas%20y%2000%20minutos%5EFS%0A%5EFO10%2C265%5EFDConservar%20en%20nevera%20de%202%27%20a%208%27%20C%5EFS%0A%0A%5EFX%20Firma%20de%20quien%20prepara%0A%5EFO230%2C162%0A%5EGFA%2C612%2C612%2C17%2C%2C%3A%3A%3AI07C%2C007IFU03F%2C03E001FT0E78%2C07J03CS0C3CI07F%2C0CK07R0180C001E1C%2C08K01CQ0180E001802%2CN07Q01806003%2CN018P01806002%2CN08EP01006002%2CN0C38O01806002%2CK018040CO01802002%2CK0180406P0802003%2CL0C0603P0C02003%2CL0C04019EK01E0C06001%2CL060400F1J013204040018%2CL0608006018001E2060C0018%2CL03J030387C0660238I0C%2CL018I010ECC703C03EJ0CFF6%2CL018I01FC783C1IF8J0FC1F%2CR0F0300CJ08I07B0038%2CR0CO0C001C18018%2CR0CO0700301C018%2CR04O0180C00603%2CR0CP0CJ0383%2C0018N0CP07K0FE%2CI03N0CP03EJ07%2CJ0FL01CQ07C%2CJ03CK038R078%2CK07CI01E%2CL0FF00FC%2CM07FFC%2C%2C%5EFS%0A%0A%0A%0A%5EFX%20Firma%20de%20quien%20aprueba%0A%5EFO230%2C200%0A%5EGFA%2C805%2C805%2C23%2C%2C%3AgW01F8%2CgW078C%2CgV01C04%2CgV03004%2CgV0600C%2CgV0C008%2CgG08N0F006018018%2Cg018N09C0607006%2Cg018M01070F0600C%2CK01ET0CM0301CB0603%2CJ01FF8R01CM01007F060E%2CJ0F01CS0CM01801F0618%2CI03801CS0CM018031063%2CI060018S04M018061063FFE%2CI060018S06N081C10201CFF%2CI0400188R060E0F0C00838106J038%2CI0603IFEQ020IFC800830187J01C%2CI03FEI03C3F1019F800218C86800820083J016%2CP07JF9B880FF18801C008J03J01C%2CP038I01B04FDFB8803C008N0FF%2CP034I01B07003F8C066N01FFE%2CP01EI07B06001D861C2L01FFE%2CI02M07IFDB87IF183FL0IF8%2C03F8FF8J06J0F063FC18K0BFFE%2C1EJ0FEI04J0704M01IFA%2C18K03F83CN03NF7FQ0C%2CN01FFM03KFK01JFCL01FE%2CV07FFCT0IF8K0E%2CU0IFY07IF006%2CT03FgJ017F4%2C%2C%3A%3A%5EFS%0A%0A%5EFX%20Cantidad%20de%20etiquetas%20a%20imprimir%0A%5EPQ2%0A%0A%5EXZ
			$impresionZPL ="^XA
							^FX Codigo de barras
							^FO12,10
							^BCN,70,N,N^FD".$wcod."^FS

							^FX Codigo ,lote y fecha de vencimiento
							^CFR
							^FO240,10^FD".$wcod."^FS
							^CFP
							^FO240,40^FDLOTE: ".$wlot."^FS
							^FO240,60^FDF.V: ".$wfev."^FS


							^FX Nombre del producto
							^CFR,1
							^FO10,82^FD".$wnom1."^FS
							^FO10,107^FD".$wnom2."^FS


							^FX Fecha y hora de preparación
							^CFP
							^FO10,142^FDF. PREP: ".$wfpr."^FS
							^FO190,142^FDH. PREP: ".$whpr."^FS


							^FX Preparado por
							^CFP
							^FO10,162^FDPREPARADO POR QF:^FS
							^FO10,180^FD".$wela."^FS


							^FX Aprobado por
							^CFP
							^FO10,200^FDAPROBO:^FS
							^FO10,220^FD".$wrev."^FS

							^FX Tiempo de infusión y nota
							^CFQ
							^FO10,240^FD".$tiempoInfusion."^FS
							^FO10,265^FDConservar en nevera de 2' a 8' C^FS

							^FX Firma de quien prepara
							^FO225,162
							".$firmaEla."


							^FX Firma de quien aprueba
							^FO225,200
							".$firmaRev."

							^FX Cantidad de etiquetas a imprimir
							^PQ".$wetq."

							^XZ";
							
			
							// echo "<pre>".print_r($impresionZPL,true)."</pre>";
						
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
			// return;
			
			// ----------------------------------------------------------------------
			// $longcb=strlen($wcod);
			
			// // ETIQUETA EN EPL
			// $paquete="";
			// $paquete=$paquete."N".chr(13).chr(10);                                             //Limpia del buffer la anterior impresion
			// $paquete=$paquete."FK".chr(34)."CENPRO".chr(34).chr(13).chr(10);                   //Limpia la forma que habia en memoria 'CENPRO'
			// $paquete=$paquete."FS".chr(34)."CENPRO".chr(34).chr(13).chr(10);;                  //Se le da nombre a la forma = 'CENPRO'
			// $paquete=$paquete."V00,".$longcb.",L,".chr(34)."CODIGO".chr(34).chr(13).chr(10);   //Para la variable 00...V00, longcb=largo del contenido, 'CODIGO' nombre del campo
			// $paquete=$paquete."V01,27,L,".chr(34)."LOTE".chr(34).chr(13).chr(10);              //Para la variable 01...V01, 27=largo del contenido, 'LOTE' nombre del campo
			// $paquete=$paquete."V02,27,L,".chr(34)."FECVEN".chr(34).chr(13).chr(10);            //Para la variable 02...V01, 27=largo del contenido, 'FECVEN' nombre del campo
			// $paquete=$paquete."V03,27,L,".chr(34)."NOMBRE1".chr(34).chr(13).chr(10);           //Para la variable 03...V01, 27=largo del contenido, 'NOMBRE1' nombre del campo
			// $paquete=$paquete."V04,27,L,".chr(34)."NOMBRE2".chr(34).chr(13).chr(10);           //Para la variable 04...V01, 27=largo del contenido, 'NOMBRE2' nombre del campo
			// $paquete=$paquete."V05,38,L,".chr(34)."PREPREEMP".chr(34).chr(13).chr(10);		   //Para la variable 06...V06, 27=largo del contenido, 'PREPREEMP' (fecha de preparacion o de empaque) nombre del campo
			// $paquete=$paquete."V06,27,L,".chr(34)."HORAPREP".chr(34).chr(13).chr(10);		   //Para la variable 06...V06, 27=largo del contenido, 'PREPREEMP' (fecha de preparacion o de empaque) nombre del campo
			// $paquete=$paquete."V07,27,L,".chr(34)."POR".chr(34).chr(13).chr(10);           	   //Para la variable 05...V05, 38=largo del contenido, 'POR' nombre del campo			
			// $paquete=$paquete."V08,38,L,".chr(34)."ELA".chr(34).chr(13).chr(10);		   //Para la variable 06...V07, 27=largo del contenido, 'ELA'  nombre del campo. Diciembre 10 de 2012
			// $paquete=$paquete."V09,38,L,".chr(34)."TIEMPINF".chr(34).chr(13).chr(10);		   //Para la variable 06...V07, 27=largo del contenido, 'ELA'  nombre del campo. Diciembre 10 de 2012
			// $paquete=$paquete."V10,38,L,".chr(34)."NOTA".chr(34).chr(13).chr(10);		   //Para la variable 06...V07, 27=largo del contenido, 'ELA'  nombre del campo. Diciembre 10 de 2012
			// $paquete=$paquete."q650".chr(13).chr(10);                                          //q650: Indica el ancho de la etiqueta, 650= es el ancho en milimtros
			// $paquete=$paquete."S3".chr(13).chr(10);                                            //S3 : Indica la velocidad de la impresora para la 2844=3 que es 2.5 ips (63 mm/s)
			// $paquete=$paquete."D4".chr(13).chr(10);                                            //D4 : Define el formato de fecha a imprimirse, 4 : Indica que se muestra elaño de 4 digitos
			// $paquete=$paquete."ZT".chr(13).chr(10);                                            //ZT : Z:indica la orientacion de la impresion, T:Indica donde se imprime en este caso en el TOP, B:Seria Bottom
			// $paquete=$paquete."TTh:m".chr(13).chr(10);                                         //TT: Setea el formato de la hora. Si se utiliza
			// $paquete=$paquete."TDy2.mn.dd".chr(13).chr(10);                                    //TD: Setea el formato de la fecha y2=Año 2 digitos, mn:Mes dos digitos y dd:dia de dos digitos 
			// $paquete=$paquete."B145,10,0,1,2,5,70,N,V00".chr(13).chr(10); //Imprime en la columna=215, fila=20, Parametros del código de Barras
			// $paquete=$paquete."A355,10,0,4,1,1,N,V00".chr(13).chr(10);   //Imprime en la columna=140, fila=110, tamaño=4, V00=Codigo
			// $paquete=$paquete."A355,35,0,3,1,1,N,V01".chr(13).chr(10);   //Imprime en la columna=140, fila=140, tamaño=3, V01=Lote
			// $paquete=$paquete."A355,60,0,2,1,1,N,V02".chr(13).chr(10);   //Imprime en la columna=140, fila=200, tamaño=3, V02=F.Vencimiento
			// $paquete=$paquete."A140,100,0,4,1,1,N,V03".chr(13).chr(10);   //Imprime en la columna=140, fila=230, tamaño=3, V03=Nombre1 (27 Caracteres)
			// $paquete=$paquete."A140,130,0,4,1,1,N,V04".chr(13).chr(10);   //Imprime en la columna=140, fila=260, tamaño=3, V04=Nombre2 (27 Caracteres)
			// $paquete=$paquete."A140,160,0,1,1,1,N,V05".chr(13).chr(10);   //Imprime en la columna=140, fila=170, tamaño=3, V05=Fecha de Preparacion o de Reempaque
			// $paquete=$paquete."A355,160,0,1,1,1,N,V06".chr(13).chr(10);   //Imprime en la columna=140, fila=170, tamaño=3, V05=Fecha de Preparacion o de Reempaque
			// $paquete=$paquete."A140,190,0,1,1,1,N,V07".chr(13).chr(10);   //Imprime en la columna=140, fila=210, tamaño=3, V06=POR
			// $paquete=$paquete."A140,210,0,1,1,1,N,V08".chr(13).chr(10);   //Imprime en la columna=140, fila=250, tamaño=1, V08=REVISADO POR.	Diciembre 10 de 2012
			// $paquete=$paquete."A140,240,0,1,1,1,N,V09".chr(13).chr(10);   //Imprime en la columna=140, fila=270, tamaño=1, V09=Quien elabora el lote.	Diciembre 10 de 2012
			// $paquete=$paquete."A140,260,0,1,1,1,N,V10".chr(13).chr(10);   //Imprime en la columna=140, fila=270, tamaño=1, V09=Quien elabora el lote.	Diciembre 10 de 2012
			// $paquete=$paquete."FE".chr(13).chr(10);
			// $paquete=$paquete.".".chr(13).chr(10);
			// $paquete=$paquete."FR".chr(34)."CENPRO".chr(34).chr(13).chr(10);
			// $paquete=$paquete."?".chr(13).chr(10);
			// $paquete=$paquete.$wcod.chr(13).chr(10);                      //Imprime el código normal
			// $paquete=$paquete."LOTE:".$wlot.chr(13).chr(10);             //Imprime la palabra lote y la variable
			// $paquete=$paquete."F.V:".$wfev.chr(13).chr(10);             //Imprime la fecha de vencimiento y la variable
			// $paquete=$paquete.$wnom1.chr(13).chr(10);                     //Imprime la variable wnom1
			// $paquete=$paquete.$wnom2.chr(13).chr(10);                     //Imprime la variable wnom2
			// $paquete=$paquete."F. PREP:".$wfpr.chr(13).chr(10);    //Imprime F. PREP/REEMP y la variable wfpr
			// $paquete=$paquete."H. PREP:".$whpr.chr(13).chr(10);    //Imprime F. PREP/REEMP y la variable wfpr
			// $paquete=$paquete."PREPARADO POR QF: ".$wela.chr(13).chr(10); 			  //Imprime quien elabora el lote y la variable $wela			
			// $paquete=$paquete."APROBO: ".$wrev.chr(13).chr(10);	  //Imprime quien revisa el lote //Diciembre 10 de 2012
			// $paquete=$paquete.$tiempoInfusion.chr(13).chr(10);	  //Imprime quien revisa el lote //Diciembre 10 de 2012
			// $paquete=$paquete."Conservar en nevera de 2".chr(96)."a 8".chr(96)." C".chr(13).chr(10);	  //Imprime quien revisa el lote //Diciembre 10 de 2012
			// $paquete=$paquete."P".$wetq.chr(13).chr(10);
			// $paquete=$paquete.".".chr(13).chr(10);
			
			// $addr=$wip;
			// $fp = fsockopen( $addr,9100, $errno, $errstr, 30);
			// if(!$fp) 
			// echo "ERROR : "."$errstr ($errno)<br>\n";
			// else 
			// {
			// fputs($fp,$paquete);
			// #echo "PAQUETE ENVIADO $errstr ($errno)<br>\n";
			// echo "PAQUETE ENVIADO <br>\n";
			// fclose($fp);
			// }
			// sleep(5);
		}
	}
}
?>
</body>
</html>