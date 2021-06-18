<html>
<head>
  	<title>MATRIX  Comprobante de Inventarios</title>

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
		
		
		.table_datos th {background-color: #2A5DB0; color: #FFFFFF; font-family: "verdana"; font-size: 10pt; }
		.table_datos tr:nth-child(odd) {background-color: #C3D9FF;font-family: "verdana"; font-size: 10pt;}
		.table_datos tr:nth-child(even) {background-color: #E8EEF7;font-family: "verdana"; font-size: 10pt;}
   </style>

</head>
<body>
<BODY>
<?php

/******************************************************************************************************************************************
 * Modificaciones
 *
 * 2021-06-16	Edwin MG : Se realiza cambios para que los productos devueltos (Se identifican con concepto 05) NO realice un producto
 *						   cartesiano
 * 2020-03-20	Edwin MG : Se registra en tabla nueva DETALLE ARTICULO - GENERACION DE INVENTARIO (cenpro_000024) el comprobante
 *						   de inventario detallada por articulos
 ******************************************************************************************************************************************/

include_once("conex.php");
include_once("root/comun.php");

function consultarDatosFiltros( $conex, $wcenmez, $wmovhos ){

	// $val = [ 
			// 'fuentes' 	=> [],  
			// 'conceptos' => [],  
			// 'cuentas' 	=> [],  
			// 'ccos' 		=> [ 
					// '1051' => '1051',
					// '1050' => '1050',
					// '1060' => '1060',
				// ],  
		// ];
		
	$val = [ 
			'fuentes' 	=> [],  
			'conceptos' => [],  
			'cuentas' 	=> [],  
			'ccos' 		=> [],  
		];
		
	$sql = "SELECT Ccocod, Ccoima
			  FROM ".$wmovhos."_000011
			 WHERE ccoest  = 'on'
			   AND ccotra  = 'on'
			   AND ccodom != 'on'
		  ORDER BY Ccoima DESC
			";

	$res = mysql_query( $sql, $conex );

	while( $rows = mysql_fetch_array( $res ) ){
		$val['ccos'][$rows['Ccocod']] = $rows['Ccocod'];
	}


	$sql = "SELECT Icccon, Iccfue, Icccde
			  FROM ".$wcenmez."_000013
			";

	$res = mysql_query( $sql, $conex );

	while( $rows = mysql_fetch_array( $res ) ){
		
		if( !in_array( $rows['Iccfue'],$val['fuentes'] ) ){
			$val[ 'fuentes' ][] = $rows['Iccfue'];
		}
		
		if( !in_array( $rows['Icccon'],$val['conceptos'] ) ){
			$val[ 'conceptos' ][] = $rows['Icccon'];
		}
		
		if( !in_array( $rows['Icccde'],$val['cuentas'] ) ){
			$val[ 'cuentas' ][] = $rows['Icccde'];
		}
	}

	return $val;
}

function consultarUsuarioCenpro( $conex, $cod ){

	$val = "";

	$sql = "SELECT Descripcion
			  FROM usuarios
			 WHERE Codigo = '".$cod."'
			";

	$res = mysql_query( $sql, $conex );

	if( $rows = mysql_fetch_array( $res ) ){
		$val = $cod."-".$rows['Descripcion'];
	}

	return $val;
}

function consultarArticulo( $conex, $wmovhos, $cod ){

	$val = [];

	$sql = "SELECT Artcod, Artcom, Artgen
			  FROM ".$wmovhos."_000026
			 WHERE Artcod = '".$cod."'
			";

	$res = mysql_query( $sql, $conex );

	if( $rows = mysql_fetch_array( $res ) ){
		$val = $rows;
	}

	return $val;
}

function calcularValorProducto($cantidad, $lote, &$debito1, &$credito1, &$debito2, &$credito2, $concepto, $numli, &$articulos, $extra )
{ 
	//echo "Entro....";
	global $conex;
	global $empresa;
	global $articulos_por_presentacion;
	global $datos_por_funte;

	$query = "SELECT Mdeart, Mdecan, Mdepre, fecha_data, Mdecon, Seguridad from ".$empresa."_000007 ";
	$query .= " where  Mdecon='".$concepto."'";
	$query .= "   and  Mdenlo='".$lote."'"; 
// echo "<br>3: ".$query;
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
			if( empty( $articulos_por_presentacion[ $row2[2] ][ $row2[0] ] ) || empty( $articulos_por_presentacion[ $row2[2] ][ $row2[0] ]['mat'] ) ){
				//Se debe consultar tambien el codigo del insumo para encontrar el valor correcto de conversion (Appcnv)
				$query = "SELECT Appcos, Appcnv, Tipmat from ".$empresa."_000009, ".$empresa."_000001, ".$empresa."_000002 ";
				$query .= "where  Apppre='".$row2[2]."'";
				$query .= "  and  Appcod='".$row2[0]."' ";
				$query .= "  and  Appcod= artcod ";
				$query .= "  and  Arttip= tipcod ";

				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				$row = mysql_fetch_array($err);
			}
			else{
				$row[0] = $articulos_por_presentacion[ $row2[2] ][ $row2[0] ]['cos'];
				$row[1] = $articulos_por_presentacion[ $row2[2] ][ $row2[0] ]['cnv'];
				$row[2] = $articulos_por_presentacion[ $row2[2] ][ $row2[0] ]['mat'];
			}

			//if($row[2]=='on' and $numli>1)
			if($row[2]=='on' and $numli>1)
			{

				$debito2 +=$row[0]*$row2[1]*$cantidad/($row[1]*$rowp[0]);
				$credito2 +=$row[0]*$row2[1]*$cantidad/($row[1]*$rowp[0]);

				$articulos['fuente2'][ $row2[2] ] += $row[0]*$row2[1]*$cantidad/($row[1]*$rowp[0]);

				$datos_por_funte[ 'fuente2' ][] = [
								'concepto' 	=> $concepto,
								'fecha' 	=> $row2[3],
								'codigo' 	=> $row2[2],
								'cantidad' 	=> $row2[1],
								'valor' 	=> round( $row[0]*$row2[1]*$cantidad/($row[1]*$rowp[0]), 2 ),
								'usuario' 	=> $row2[5],
								'documento'	=> $extra['con']."-".$extra['doc'],
								'producto'	=> $exp[1],
								'lote'		=> $exp[0],
								'usumov'	=> $extra['usu'],
								'anexo'		=> $extra['anx'],
							];
			}
			else
			{	
				$debito1 +=$row[0]*$row2[1]*$cantidad/($row[1]*$rowp[0]);
				$credito1 +=$row[0]*$row2[1]*$cantidad/($row[1]*$rowp[0]);

				$articulos['fuente1'][ $row2[2] ] += $row[0]*$row2[1]*$cantidad/($row[1]*$rowp[0]);

				$datos_por_funte[ 'fuente1' ][] = [
								'concepto' 	=> $concepto,
								'fecha' 	=> $row2[3],
								'codigo' 	=> $row2[2],
								'cantidad' 	=> $row2[1],
								'valor' 	=> round( $row[0]*$row2[1]*$cantidad/($row[1]*$rowp[0]), 2 ),
								'usuario' 	=> $row2[5],
								'documento'	=> $extra['con']."-".$extra['doc'],
								'producto'	=> $exp[1],
								'lote'		=> $exp[0],
								'usumov'	=> $extra['usu'],
								'anexo'		=> $extra['anx'],
							];
			}
		}
		else
		{
			$res=calcularValorProducto(($row2[1]*$cantidad/$rowp[0]),$row2[0],$debito1, $credito1, $debito2, $credito2, $concepto, $numli, $articulos, $extra );
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

session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='compinv' action='compinv_detallado_movimiento.php?wemp_pmla=".$wemp_pmla."' method=post>";

	$wmovhos = consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );


	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wfeci) or !isset($wfecf) or !isset($wano) or !isset($wmes))
	{
		$consultarDatosFiltros = consultarDatosFiltros( $conex, $empresa, $wmovhos );
		
		echo "<center><table border=0 class='table_datos'>";
		echo "<tr><td class='texto5' colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td class='titulo1' colspan=2>GENERACION ARCHIVO MENSUAL DEL COMPROBANTE CONTABLE</td></tr>";
		echo "<tr><td class='texto4'>Año de Proceso</td>";
		echo "<td class='texto4'><input type='TEXT' name='wano' size=4 maxlength=4></td></tr>";
		echo "<tr><td class='texto4'>Mes de Proceso</td>";
		echo "<td class='texto4'><input type='TEXT' name='wmes' size=2 maxlength=2></td></tr>";
		echo "<tr><td class='texto4'>Fecha Inicial</td>";
		echo "<td class='texto4'><input type='TEXT' name='wfeci' size=10 maxlength=10></td></tr>";
		echo "<tr><td class='texto4'>Fecha Final</td>";
		echo "<td class='texto4'><input type='TEXT' name='wfecf' size=10 maxlength=10></td></tr>";
		
		echo "<tr><td class='texto4'>Fuente</td>";		
		echo "<td class='texto4'>";
		echo "<SELECT name='fuente_filtro'>";
		foreach( $consultarDatosFiltros['fuentes'] as $key => $value ){
			echo "<option value='".$value."'>".$value."</option>";
		}
		echo "</SELECT>";
		echo "</td></tr>";
		
		echo "<tr><td class='texto4'>Concepto</td>";		
		echo "<td class='texto4'>";
		echo "<SELECT name='concepto_filtro'>";
		foreach( $consultarDatosFiltros['conceptos'] as $key => $value ){
			echo "<option value='".$value."'>".$value."</option>";
		}
		echo "</SELECT>";
		echo "</td></tr>";
		
		
		
		echo "<tr><td class='texto4'>Centro de costos</td>";		
		echo "<td class='texto4'>";
		echo "<SELECT name='cco_filtro'>";
		foreach( $consultarDatosFiltros['ccos'] as $key => $value ){
			echo "<option value='".$value."'>".$value."</option>";
		}
		echo "</SELECT>";
		echo "</td></tr>";		
		
		
		echo "<tr><td class='texto4'>Cuenta</td>";		
		echo "<td class='texto4'>";
		echo "<SELECT name='cuenta_filtro'>";
		foreach( $consultarDatosFiltros['cuentas'] as $key => $value ){
			echo "<option value='".$value."'>".$value."</option>";
		}
		echo "</SELECT>";
		echo "</td></tr>";
		
		echo "<tr><td class='texto4'>Naturaleza</td>";		
		echo "<td class='texto4'>";
		echo "<SELECT name='naturaleza_filtro'>";
		echo "<option value='1'>Debito</option>";
		echo "<option value='2'>Credito</option>";
		echo "</SELECT>";
		echo "</td></tr>";
		
		
		echo "<tr><td class='texto1'  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		// $fuente_filtro = '13';
		// $concepto_filtro = '05';
		// $cco_filtro = '1051';
		// $cuenta_filtro = '14300503';
		// $naturaleza_filtro = '1';
		
		//Consultando concepto de devolucion
		$query = "SELECT concod 
					FROM ".$empresa."_000008 
				   WHERE conind='1' 
				     AND concar='on' ";

		$res = mysql_query($query,$conex) or die( "No se encuentra el concepto de devolución" );
		
		$rows = mysql_fetch_array( $res );
		
		$conDevolucion = $rows[ 'concod' ];


		$datos_por_funte = [];
		$datos_totales_por_funte2 = [];
		$datos_totales_por_funte = [];

		echo "<table border=0 align=center>";
		echo "<tr><td class='titulo1'><b>GENERACION DE COMPROBANTE CONTABLE</font> Ver 1.0</b></font></td></tr>";
		echo "<tr><td class='texto4'><font face='tahoma'><b>Fecha Inicial : </b>".$wfeci."</td></tr>";
		echo "<tr><td class='texto4'><font face='tahoma'><b>Fecha Final : </b>".$wfecf."</td></tr>";
		echo "</tr></table><br><br>";

		//Guardar articulos por presentación
		$articulos_por_presentacion = [];

		$ultreg=1;
		$query = "Select * from  ".$empresa."_000012 ";
		$query .= " where cinano = ".$wano;
		$query .= "     and cinmes = ".$wmes;
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if(true)
		{
			//ACA CREO UNA TABLA TEMPORAL CON TODOS LOS MOVIMIENTOS
			$query = "SELECT   Mencon, Menfec, Mendoc, Mencco, Menccd, Congec from ".$empresa."_000006,".$empresa."_000008 ";
			$query .= " where  Menfec between '".$wfeci."' and '".$wfecf."'";
			$query .= "     and   Mencon=Concod ";
			$query .= "     and   Congec='on'"; // and mendoc = '85640'
			$query .= "    Order by Mencon, Mendoc ";
// echo "<br><br><br>0:".$query;
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());

			/*		//ACA CREO UNA TABLA TEMPORAL CON TODOS LOS MOVIMIENTOS
			$query = "SELECT   Mencon, Menfec, Mendoc, Mencco, Menccd, Iccfue, Icccde, Iccccd, Iccted, Iccccr, Iccccc, Icctec, congec, Iccnig, Iccbad, Iccbac from ".$empresa."_000006,".$empresa."_000008,".$empresa."_000013 ";
			$query .= " where  Menfec between '".$wfeci."' and '".$wfecf."'";
			$query .= "     and   Mencon=Concod ";
			$query .= "     and   Congec='on' ";
			$query .= "     and   Mencon=Icccon ";
			$query .= "    Order by Iccfue,Mencon, Mendoc ";*/

			$num = mysql_num_rows($err);
			// echo "<b>Movimientos Totales  : ".$num."</b><br><br>";
			$wtotd1=0;
			$wtotc1=0;
			$wtotd2=0;
			$wtotc2=0;
			$k=0;
			$wconant="";
			$wfueant="";
			$cl=0;

			$articulos = [];
			$datos_por_funte = [];

			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);

				$wconant = $row[0];
				$wtotd1=0;
				$wtotc1=0;
				$wtotd2=0;
				$wtotc2=0;

				$articulos = [];
				$datos_por_funte = [];

				//consultamos las diferentes cuentas involucradas
				$query = "SELECT  Iccfue, Icccde, Iccccd, Iccted, Iccccr, Iccccc, Icctec, Icclin, Iccnig, Iccbad, Iccbac from ".$empresa."_000013 ";
				$query .= " where  Icccon='".$row[0]."' ";
				$query .= "    Order by Icclin ";
// echo "<br><br>1:".$query;
				$errli = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
				$numli = mysql_num_rows($errli);
				$rowli = mysql_fetch_array($errli);

				//si tine base debito
				if($rowli[9] == "on")
				$wbased1="S";
				else
				$wbased1="N";

				//si tiene base credito
				if($rowli[10] == "on")
				$wbasec1="S";
				else
				$wbasec1="N";
				$wfuente1=$rowli[0];

				//el debito que centro de costos es
				switch ($rowli[2])
				{
					case "origen":
					$wccde1=$row[3];
					break;
					case "destino":
					$wccde1=$row[4];
					break;
					case "no":
					$wccde1="00";
					break;
				}

				//nit del tercero
				$wnitde1="0";
				if($rowli[3] == "on")
				$wnitde1=$rowli[8];

				switch ($rowli[5])
				{
					case "origen":
					$wcccr1=$row[3];
					break;
					case "destino":
					$wcccr1=$row[4];
					break;
					case "no":
					$wcccr1="00";
					break;
				}

				$wnitcr1="0";
				if($rowli[6] == "on")
				$wnitcr1=$rowli[8];

				$wcdb1=$rowli[1];
				$wccr1=$rowli[4];

				if($numli>1)
				{
					$rowli = mysql_fetch_array($errli);

					//si tine base debito
					if($rowli[9] == "on")
					$wbased2="S";
					else
					$wbased2="N";

					//si tiene base credito
					if($rowli[10] == "on")
					$wbasec2="S";
					else
					$wbasec2="N";
					$wfuente2=$rowli[0];

					//el debito que centro de costos es
					switch ($rowli[2])
					{
						case "origen":
						$wccde2=$row[3];
						break;
						case "destino":
						$wccde2=$row[4];
						break;
						case "no":
						$wccde2="00";
						break;
					}

					//nit del tercero
					$wnitde2="0";
					if($rowli[3] == "on")
					$wnitde2=$rowli[8];

					switch ($rowli[5])
					{
						case "origen":
						$wcccr2=$row[3];
						break;
						case "destino":
						$wcccr2=$row[4];
						break;
						case "no":
						$wcccr2="00";
						break;
					}

					$wnitcr2="0";
					if($rowli[6] == "on")
					$wnitcr2=$rowli[8];

					$wcdb2=$rowli[1];
					$wccr2=$rowli[4];
				}

				//consultamos el tipo del articulo para saber si debe desglosarse en insumos o no
				$query = "SELECT Mdeart, Tippro, Mdepre, Mdecan, Mdenlo, Tipmat, a.Fecha_data, a.Mdecon, a.Seguridad, a.Mdedoc from ".$empresa."_000007 a,".$empresa."_000001,".$empresa."_000002  ";
				$query .= " where  Mdecon='".$row[0]."'";
				$query .= "     and   Mdedoc='".$row[2]."'";
				$query .= "     and   Mdeart=artcod ";
				$query .= "     and   Arttip=Tipcod ";
// echo "<br>2:".$query;
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);

				for ($j=0;$j<$num1;$j++)
				{
					$row1 = mysql_fetch_array($err1);

					if($row1[1]=='on')
					{
						// Esto si es un producto
						
						/**
						 * Si el concepto es el de devolución ( Concepto 05 al momento de realizar el cambio )
						 * La cantidad del lote siempre es 1
						 * Esto debido a que al realizar la devolución de un producto, la cantidad es siempre 1.
						 * Por ejemplo si es el programa de devolución de enfermería se hace una devolución de un producto, 
						 * realiza el mismo procedimiento tantas veces sea necesario
						 */
						if( $conDevolucion == $row[2] )
							$row1[3] = 1;
						
						//consultamos el movimiento de fabricación del lotes
						$query = "SELECT concod from   ".$empresa."_000008 ";
						$query .= " where  conind='-1' and congas='on' ";

						$err2 = mysql_query($query,$conex);
						$row2 = mysql_fetch_array($err2);

						//consultamos los valores para productos codificados
						//para esto hay que desglosarlo primero en insumos
						if( empty( $lotes[$row1[4]."-".$row1[9]] ) )
							$res=calcularValorProducto($row1[3],$row1[4],$wtotd1, $wtotc1, $wtotd2, $wtotc2, $row2[0], $numli, $articulos, [ 'con' => $row1['Mdecon'], 'doc' => $row1['Mdedoc'] , 'usu' => $row1['Seguridad'], 'anx' => $row['Menccd'] ] );
						$lotes[$row1[4]."-".$row1[9]] = 1;
					}
					else if($row1[1] != 'on')
					{
						//Cuando no es un producto....
						
						$exp=explode( '-', strtoupper( $row1[2] ) );	//presentacion del insumo
						$cod=explode( '-', strtoupper( $row1[0] ) );	//codigo del insumo

						/******************************************************************************
						 * Esto se hace para darle mayor velocidad al programa
						 ******************************************************************************/
						if( empty( $articulos_por_presentacion[ $exp[0] ][ $cod[0] ] ) ){

							//consultamos los valores para insumos
							//Para encontrar el valor correcto de conversion(Appcnv) debe incluir el codigo del articulo
							$query = "SELECT Appcos, Appcnv from ".$empresa."_000009 ";
							$query .= " where  Apppre='".$exp[0]."'";
							$query .= "   and  Appcod='".$cod[0]."'";

							$err2 = mysql_query($query,$conex);
							$num2 = mysql_num_rows($err2);
							$row2 = mysql_fetch_array($err2);

							$articulos_por_presentacion[ $exp[0] ][ $cod[0] ]['cos'] = $row2[0];
							$articulos_por_presentacion[ $exp[0] ][ $cod[0] ]['cnv'] = $row2[1];
						}
						else{
							$row2[0] = $articulos_por_presentacion[ $exp[0] ][ $cod[0] ]['cos'];
							$row2[1] = $articulos_por_presentacion[ $exp[0] ][ $cod[0] ]['cnv'];
						}

						if($row1[5]=='on' and $numli>1)
						{
							@$wtotd2 +=$row2[0]*$row1[3]/$row2[1];
							@$wtotc2 +=$row2[0]*$row1[3]/$row2[1];

							@$articulos[ 'fuente2' ][ $exp[0] ] +=$row2[0]*$row1[3]/$row2[1];

							$datos_por_funte[ 'fuente2' ][] = [
								'concepto' 	=> $row1[7],
								'fecha' 	=> $row1[6],
								'codigo' 	=> $exp[0],
								'cantidad' 	=> $row1[3],
								'valor' 	=> @round( $row2[0]*$row1[3]/$row2[1], 2 ),
								'usuario' 	=> $row1[8],
								'documento'	=> $row1['Mdecon']."-".$row1['Mdedoc'],
								'producto'	=> explode( "-", $row1['Mdenlo'] )[1],
								'lote'		=> explode( "-", $row1['Mdenlo'] )[0],
								'usumov'	=> $row1['Seguridad'],
								'anexo'		=> $row['Menccd'],
							];
						}
						else
						{

							$wtotd1 +=$row2[0]*$row1[3]/$row2[1];
							$wtotc1 +=$row2[0]*$row1[3]/$row2[1];

							$articulos[ 'fuente1' ][ $exp[0] ] +=$row2[0]*$row1[3]/$row2[1];

							$datos_por_funte[ 'fuente1' ][] = [
								'concepto' 	=> $row1[7],
								'fecha' 	=> $row1[6],
								'codigo' 	=> $exp[0],
								'cantidad' 	=> $row1[3],
								'valor' 	=> round( $row2[0]*$row1[3]/$row2[1], 2 ),
								'usuario' 	=> $row1[8],
								'documento'	=> $row1['Mdecon']."-".$row1['Mdedoc'],
								'producto'	=> explode( "-", $row1['Mdenlo'] )[1],
								'lote'		=> explode( "-", $row1['Mdenlo'] )[0],
								'usumov'	=> $row1['Seguridad'],
								'anexo'		=> $row['Menccd'],
							];
						}
					}
				}

				//guardamos los datos
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");

				IF($wtotd1>0)
				{
					if( count( $datos_por_funte['fuente1'] ) > 0 ){

						foreach( $datos_por_funte['fuente1'] as $key => $dato )
						{
							$articulo = consultarArticulo( $conex, $wmovhos, $dato['codigo'] );
							$usuario = consultarUsuarioCenpro( $conex, explode( "-", $dato['usuario'] )[1] );

							$datos_totales_por_funte[] = [
										'fecha' 			=> $dato['fecha'],
										'secuencia' 		=> $ultreg,
										'anio' 				=> $wano,
										'mes' 				=> $wmes,
										'fuente' 			=> $wfuente1,
										'concepto' 			=> $wconant,
										'cincco' 			=> $wccde1,
										'nit' 				=> $wnitde1,
										'cuenta' 			=> $wcdb1,
										'articulo' 			=> $dato['codigo'],
										'valor' 			=> $dato['valor'],
										'naturaleza'		=> 1,
										'baj' 				=> $wbased1,
										'usuario' 			=> $usuario,
										'conmatrix'			=> $dato['concepto'],
										'cantidad'			=> $dato['cantidad'],
										'nombreGenerico'	=> $articulo['Artgen'],
										'nombreComercial'	=> $articulo['Artcom'],
										'documento'			=> $dato['documento'],
										'producto'			=> $dato['producto'],
										'lote'				=> $dato['lote'],
										'usumov'			=> $dato['usumov'],
										'anexo'				=> $dato['anexo'],
									];
						}
					}

					if( count( $datos_por_funte['fuente1'] ) > 0 ){

						foreach( $datos_por_funte['fuente1'] as $key => $dato )
						{
							$articulo = consultarArticulo( $conex, $wmovhos, $dato['codigo'] );
							$usuario = consultarUsuarioCenpro( $conex, explode( "-", $dato['usuario'] )[1] );

							$datos_totales_por_funte[] = [
										'fecha' 			=> $dato['fecha'],
										'secuencia' 		=> $ultreg,
										'anio' 				=> $wano,
										'mes' 				=> $wmes,
										'fuente' 			=> $wfuente1,
										'concepto' 			=> $wconant,
										'cincco' 			=> $wcccr1,
										'nit' 				=> $wnitcr1,
										'cuenta' 			=> $wccr1,
										'articulo' 			=> $dato['codigo'],
										'valor' 			=> $dato['valor'],
										'naturaleza'		=> 2,
										'baj' 				=> $wbasec1,
										'usuario' 			=> $usuario,
										'conmatrix'			=> $dato['concepto'],
										'cantidad'			=> $dato['cantidad'],
										'nombreGenerico'	=> $articulo['Artgen'],
										'nombreComercial'	=> $articulo['Artcom'],
										'documento'			=> $dato['documento'],
										'producto'			=> $dato['producto'],
										'lote'				=> $dato['lote'],
										'usumov'			=> $dato['usumov'],
										'anexo'				=> $dato['anexo'],
									];
						}
					}

					$ultreg++;
				}

				IF($wtotd2>0)
				{
					if( count( $datos_por_funte['fuente2'] ) > 0 ){

						foreach( $datos_por_funte['fuente2'] as $key => $dato )
						{
							$articulo = consultarArticulo( $conex, $wmovhos, $dato['codigo'] );
							$usuario = consultarUsuarioCenpro( $conex, explode( "-", $dato['usuario'] )[1] );

							$datos_totales_por_funte[] = [
										'fecha' 			=> $dato['fecha'],
										'secuencia' 		=> $ultreg,
										'anio' 				=> $wano,
										'mes' 				=> $wmes,
										'fuente' 			=> $wfuente2,
										'concepto' 			=> $wconant,
										'cincco' 			=> $wccde2,
										'nit' 				=> $wnitde2,
										'cuenta' 			=> $wcdb2,
										'articulo' 			=> $dato['codigo'],
										'valor' 			=> $dato['valor'],
										'naturaleza'		=> 1,
										'baj' 				=> $wbased2,
										'usuario' 			=> $usuario,
										'conmatrix'			=> $dato['concepto'],
										'cantidad'			=> $dato['cantidad'],
										'nombreGenerico'	=> $articulo['Artgen'],
										'nombreComercial'	=> $articulo['Artcom'],
										'documento'			=> $dato['documento'],
										'producto'			=> $dato['producto'],
										'lote'				=> $dato['lote'],
										'usumov'			=> $dato['usumov'],
										'anexo'				=> $dato['anexo'],
									];
						}
					}

					if( count( $datos_por_funte['fuente2'] ) > 0 ){

						foreach( $datos_por_funte['fuente2'] as $key => $dato )
						{
							$articulo = consultarArticulo( $conex, $wmovhos, $dato['codigo'] );
							$usuario = consultarUsuarioCenpro( $conex, explode( "-", $dato['usuario'] )[1] );

							$datos_totales_por_funte[] = [
										'fecha' 			=> $dato['fecha'],
										'secuencia' 		=> $ultreg,
										'anio' 				=> $wano,
										'mes' 				=> $wmes,
										'fuente' 			=> $wfuente2,
										'concepto' 			=> $wconant,
										'cincco' 			=> $wcccr2,
										'nit' 				=> $wnitcr2,
										'cuenta' 			=> $wccr2,
										'articulo' 			=> $dato['codigo'],
										'valor' 			=> $dato['valor'],
										'naturaleza'		=> 2,
										'baj' 				=> $wbasec2,
										'usuario' 			=> $usuario,
										'conmatrix'			=> $dato['concepto'],
										'cantidad'			=> $dato['cantidad'],
										'nombreGenerico'	=> $articulo['Artgen'],
										'nombreComercial'	=> $articulo['Artcom'],
										'documento'			=> $dato['documento'],
										'producto'			=> $dato['producto'],
										'lote'				=> $dato['lote'],
										'usumov'			=> $dato['usumov'],
										'anexo'				=> $dato['anexo'],
									];
						}
					}

					$ultreg++;
				}
			}

			$total = 0;

			$encabezado_tabla = [
						'fecha' 			=> 'Fecha',
						'secuencia' 		=> 'Secuencia',
						'anio' 				=> 'A&ntilde;o',
						'mes' 				=> 'Mes',
						'fuente' 			=> 'Fuente',
						'concepto' 			=> 'Concepto',
						'documento'			=> 'Nro. Documento CM',
						'producto'			=> 'Producto',
						'lote'				=> 'Lote',
						'cincco' 			=> 'Cco',
						'nit' 				=> 'Nit',
						'cuenta' 			=> 'Cuenta',
						'articulo' 			=> 'Articulo',
						'nombreGenerico'	=> 'Nombre generico',
						'nombreComercial'	=> 'Nombre Comercial',
						'cantidad'			=> 'Cantidad',
						'valor' 			=> 'Valor',
						'naturaleza'		=> 'Naturaleza',
						// 'baj' 			=> '',
						'usuario' 			=> 'Responsable',
						// 'conmatrix'			=> 'Concepto matrix',
						// 'usumov'			=> 'Usuario Movimiento',
						'anexo'				=> 'Historia',
					];








			// echo count( $datos_totales_por_funte['11']['01']['1051']['14300503'][1] ); echo "<br>";
			// echo count( $datos_totales_por_funte ); echo "<br>";
			echo "<table class='table_datos'>";

			echo "<tr>";
			foreach( $encabezado_tabla as $key => $campo ){

				echo "<th>";
				echo $campo;
				echo "</th>";
			}
			echo "</tr>";
			
			$columns_valor = array_search( "valor", array_keys( $encabezado_tabla ) );

			foreach( $datos_totales_por_funte as $key => $valores ){

				if( $valores['fuente'] == $fuente_filtro && $valores['concepto'] == $concepto_filtro && $valores['cincco'] == $cco_filtro && $valores['cuenta'] == $cuenta_filtro && $valores['naturaleza'] == $naturaleza_filtro ){

					echo "<tr>";

					foreach( $encabezado_tabla as $key => $campo ){

						echo "<td>";
						echo $valores[$key];
						echo "</td>";

					}

					echo "</tr>";

					$total += round( $valores['valor'], 2 );
				}
			}

			echo "<tr>";

			echo "<td colspan='".$columns_valor."'>Total</td>";

			echo "<td colspan='".( count( $encabezado_tabla ) - $columns_valor )."'>";
			echo $total;
			echo "</td>";

			echo "</tr>";

			echo "</table>";

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
}
?>
</body>
</html>