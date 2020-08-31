<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
      	.titulo1{color:#FFFFFF;background:#006699;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.titulo2{color:#006699;background:#FFFFFF;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.titulo3{color:#003366;background:#A4E1E8;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.titulo4{color:#FFFFFF;background:green;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.titulo5{color:#FFFFFF;background:purple;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto1{color:#003366;background:#FFDBA8;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}	
    	.texto2{color:#003366;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto3{color:#003366;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto4{color:#003366;background:#f5f5dc;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto6{color:#FFFFFF;background:#006699;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.texto5{color:#003366;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	
   </style>
     <script type="text/javascript">
     function enter()
     {
     	document.producto.accion.value=1;
     	document.producto.submit();
     }

     function enter1()
     {
     	document.producto.lote.value='';
     	document.producto.submit();
     }

     function enter2(i)
     {
     	document.producto.eliminar.value=i;
     	document.producto.submit();
     }

     function enter3()
     {
     	document.producto.realizar.value='modificar';
     	document.producto.submit();
     }

     function enter4()
     {
     	document.producto.realizar.value='desactivar';
     	document.producto.submit();
     }

     function enters(valor)
     {
     	document.producto.estado.value='inicio';
     	document.producto.submit();
     }

     function enter7()
     {
     	document.producto2.submit();
     }

     function enter8()
     {
     	document.producto4.submit();
     }

     function enter9()
     {
     	document.producto.grabar.value='1';
     	document.producto.submit();
     }

     function hacerFoco()
     {
     	if (document.producto.elements[1].value=='')
     	{
     		document.producto.elements[1].focus();
     	}
     	else
     	{
     		document.producto.elements[13].focus();
     	}
     }

    </script>
<title>CARGOS CENTRAL DE MEZCLAS</title>
</head>
<body onload="hacerFoco()">
<?php
include_once("conex.php");
//actualizacion: 2008-01-21 se corrige la busqueda de articulos por codigo de central
//actualizacion: 2007-11-06 se crea la opcion del carro y se cargan automaticamente los codificados
/**
* ========================================================FUNCIONES==========================================================================
*/
// ----------------------------------------------------------funciones de persitencia------------------------------------------------

/**
 * Condiciones para grabar en el KE
 * 
 * @param array $pac		informacion del paciente en el KE
 * @param array $art		informacion del articulo
 * @return 
 * 
 * Nota: Las condiciones son
 * - Tener KE (Si anteriormente tuvo un KE)
 * - Que la cantidad a dispensar (kadcdi) sea mayor a la cantidad dispensada (kaddis) 
 *   para los articulos, aunque hallan duplicados.
 * - Que tenga KE actualizado (Halla KE para el dia de hoy)
 * - Que el KE este confirmado (Que la doctora confirme el KE)
 */
function condicionesKE( &$pac, $art ){
	
	global $conex;
    global $wbasedato;
    
    $pac['sal'] = false;	//Indica si tiene saldo para poder dispensar
    $pac['art'] = false;	//Indica si el articulo existe para el paciente de ke
    $pac['ke'] = false;
	$pac['con'] = false;
	$pac['act'] = false;
	$pac['gra'] = false;
	
	esKE( $pac['his'], $pac['ing'], $pacKE);
	
	$pac['ke'] = $pacKE['ke'];
	$pac['con'] = $pacKE['con'];
	$pac['act'] = $pacKE['keact'];
	$pac['gra'] = $pacKE['kegra'];
	
	//El articulo debe tener saldo antes de guardar
	$sql = "SELECT 
				SUM((kadcdi)-(kaddis)) as sal, kadart
			FROM 
				movhos_000054
			WHERE	
				kadhis = '{$pac['his']}' 
				AND kading = '{$pac['ing']}'
				AND kadart = '$art'
				AND kadfec = '".date("Y-m-d")."'
				AND kadcon = 'on'
			GROUP BY kadart";
	
	$res = mysql_query( $sql, $conex );

	if ( $rows = mysql_fetch_array( $res ) ){
		if( $rows['sal'] > 0 ){
			$pac['sal'] = true;
		}
		$pac['art'] = true;
	}
}

/**
 * 
 * @param $his
 * @param $ing
 * @return unknown_type
 */
function ArticulosXPaciente( $his, $ing ){
	
	global $conex;
	global $bd;
	
	$articulos = array();		//Guarda los articulos con saldo positivos
	$vacios = array();		//Guarda los articulos con saldo en 0
	$numrows = false; 
	
	//Buscando al paciente en Kardex electrónico por orden de estanteria
	$sql = "SELECT 
				sum(kadcdi) as cdi, sum(kaddis) as dis, 
				kadart as cod, artcom as nom
	        FROM 
				{$bd}_000054 a, cenpro_000002 b
	        WHERE 
	        	kadhis='$his'  
	        	AND kading='$ing'
	        	AND a.kadfec = '".date("Y-m-d")."'
	        	AND kadcon = 'on'
	        	AND kadsus != 'on'
	        	AND kadori = 'CM'
	        	AND artcod = kadart
	        GROUP BY kadart";

	$res = mysql_query( $sql, $conex );
	
	for($i = 0; $rows = mysql_fetch_array( $res ) ; $i++ ){		
		$articulos[$i]['cod'] = $rows['cod'];
		$articulos[$i]['nom'] = $rows['nom'];
		$articulos[$i]['cdi'] = $rows['cdi'];
		$articulos[$i]['dis'] = $rows['dis'];
		$articulos[$i]['sal'] = $rows['cdi'] - $rows['dis'];
	}
	
	return $articulos;
}

/**
 * Registra un articulo al paciente en el kardex electrónico
 * 
 * @param $art		Codigo del Aritculo del paciente
 * @param $his		Historia del paciente
 * @param $ing		Ingreso del paciente
 * @return bool		True si es verdaderos
 */
function registrarArticuloKE( $art, $his, $ing ){
	
	global $conex;
	global $bd;
	
	$sqlid="SELECT 
				max(id) 
			FROM 
				movhos_000054 
			WHERE 
				kadart = '$art'
				AND kadcdi > kaddis+0
				AND kadfec = '".date("Y-m-d")."'
				AND kadhis = '$his'  
	       		AND kading = '$ing'
	       		AND kadsus != 'on'
	       		AND kadcon = 'on'
			GROUP BY kadart";
	
	$resid = mysql_query( $sqlid, $conex );
	
	if( $row = mysql_fetch_array( $resid ) ){
		$id = $row[0];
	}
	else{
		$row[0] = "";
	}
	
	//Actualizando registro con el articulo cargado
	$sql = "UPDATE 
				movhos_000054 
	       	SET 
	       		kaddis = kaddis+1
	        WHERE 
	        	kadcdi >= kaddis+1  
	       		AND kadart = '$art' 
	       		AND kadhis = '$his'  
	       		AND kading = '$ing'
	       		AND kadfec = '".date("Y-m-d")."'
	       		AND kadsus != 'on'
	       		AND kadcon = 'on' 
	       		AND id = {$row[0]}";
	
	$res = mysql_query( $sql, $conex );
	
	if( $res && mysql_affected_rows() > 0 )
		return true;
	else
		return false;
}

/**
 * Indica si el paciente se encuentra en Kardex Electronico o no
 * 
 * @param array $his	Paciente al que se le va a cargar los articulos
 * @param array $ing	Ingreso del paciente al que se le va a cargar los articulos
 * @return bool $ke		Devuelve true en caso de se Kardex electronico, en caso contrario false
 */

/* 
 * - Tiene KE si esta en la tabla 53
 * - Esta grabado si el campo gra esta en on
 * - ESta confirmado el KE si con esta en on
 * - Esta actualizado las fechas son iguales en 53 y 54 (kadfec) 
 */
function esKE( $his, $ing, &$packe ){
	
	global $conex;
	global $bd;
	
	$ke = 0;
	$pac = array();
	$pac['his'] = $his;
	$pac['ing'] = $ing;
	$pac['keact']=false;
	$pac['kegra']=false;
	$pac['con'] = false;
	$pac['ke'] = false;
	
	//Busca kardex electronico para el paciente con la fecha mas reciente
	$sql = "SELECT 
				Fecha_data, Kargra, Karcon, MAX(Karing) as Karing 
			FROM 
				movhos_000053 a 
	        WHERE 
	        	karhis = '$his'
	        GROUP BY a.Fecha_Data
	        ORDER BY Karing DESC, a.Fecha_Data DESC";
	
	$res = mysql_query( $sql, $conex );
	
	if( mysql_num_rows($res) > 0 ){
		
		//Tiene ke		
		$ke = 1;
		$pac['ke']=true;
		
		$rows = mysql_fetch_array( $res );

		if( $rows['Karing'] == $pac['ing'] ){
			if( $rows['Fecha_data'] == date("Y-m-d") ){
				
				//KE esta confirmado
				if( $rows['Karcon'] == "on" ){
					$pac['con'] = true;
				}
	
				//KE esta grabado
				if( $rows['Kargra'] == "on" ){
					$pac['kegra'] = true;
				}
			
				//Busca kardex electronico para el paciente con la fecha mas reciente
				$sql = "SELECT 
							Fecha_data, Kadfec
						FROM 
							movhos_000054 a 
				        WHERE 
				        	kadhis = '{$pac['his']}' AND
				        	kading = '{$pac['ing']}' AND
				        	kadfec = '{$rows['Fecha_data']}'  
				        GROUP BY a.Fecha_Data
				        ORDER BY a.Fecha_Data DESC"; 
				
				$result = mysql_query( $sql, $conex );
		
				if( mysql_num_rows($result) > 0 ){
					$pac['keact']=true;
				}
			}
		}
	}	
	
	$packe = $pac;	
	return $ke;
	
	
//	$sql = "SELECT 
//				*, MAX(a.kadfec) as afd, b.Fecha_data as bfd 
//			FROM 
//				movhos_000054 a, movhos_000053 b 
//	        WHERE 
//	        	kadhis = '$his' AND
//	        	kading = '$ing' AND 
//	        	kadhis = karhis AND
//	        	kading = karing AND
//	        	a.Kadfec = b.Fecha_data
//	        GROUP BY a.Kadfec
//	        ORDER BY a.Kadfec DESC";
//
//	$res = mysql_query( $sql, $conex );
//	
//	if( mysql_num_rows($res) > 0 ){
//		//Tiene KE
//		$ke = 1;
//		$rows = mysql_fetch_array( $res );
//		$pac['ke']=true;
//		
//		//Buscando que el KE este actualizado
//		if( $rows['Karcon'] == "on" ){
//			$pac['con'] = true;
//		}
//		else{
//			$pac['con'] = false;
//		}
//		
//		//Buscando que el KE este actualizado
//		if( $rows['afd'] == date("Y-m-d") && $rows['bfd'] == date("Y-m-d") ){
//			$pac['keact']=true;
//			$ke = 2;
//		}
//		else{
//			$pac['keact']=false;
//		}
//		
////		$ke = true;
//	}

	$packe = $pac;	
	return $ke;
	
}

function consultarCentros($cco)
{
	global $conex;
	global $wbasedato;

	if ($cco != '') // cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
	{
		$ccos[0] = $cco;
		$cadena = "A.Ccocod != mid('" . $cco . "',1,instr('" . $cco . "','-')-1) AND";
		$inicio = 1;
	}
	else
	{
		$cadena = '';
		$inicio = 0;
	}
	// consulto los conceptos
	$q = " SELECT A.Ccocod as codigo, B.Cconom as nombre"
	. "        FROM movhos_000011 A, costosyp_000005 B "
	. "      WHERE " . $cadena . " "
	. "        A.Ccoima = 'on' "
	. "        AND A.Ccocod = B.Ccocod "
	. "        AND A.Ccoest='on' ";

	$res1 = mysql_query($q, $conex);
	$num1 = mysql_num_rows($res1);
	if ($num1 > 0)
	{
		for ($i = 0;$i < $num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);
			$ccos[$inicio] = $row1['codigo'] . '-' . $row1['nombre'];
			$inicio++;
		}
	}
	else if ($inicio == 0)
	{
		$ccos = '';
	}

	return $ccos;
}

/**
* Se consulta el concsecutivo del movimiento
* 
* @param number $signo , -1 si es una salida, 1 si es entrada
* @return caracter $resultado, consecutivo de acuerdo al concepto que cumple con los parametro recibidos
*/
function consultarConsecutivo($signo)
{
	global $conex;
	global $wbasedato;

	$q = " SELECT Concod, Concon "
	. "       FROM " . $wbasedato . "_000008  "
	. "    WHERE Conind = '" . $signo . "' "
	. "       AND Concar = 'on' "
	. "       AND Conest = 'on' ";

	$res = mysql_query($q, $conex);
	$row = mysql_fetch_array($res);

	$resultado = $row[0] . '-' . ($row[1] + 1);
	return $resultado;
}

/**
* consultamos el insumo, el nombre y si es un insumo producto codificado o no codificado
* 
* @param caracter $parbus valor que se busca
* @param caractere $forbus la forma de busqueda
* @return vector lista de insumos que cumplen con el criterio de busqueda
*/
function consultarInsumos($parbus, $forbus)
{
	global $conex;
	global $wbasedato;

	switch ($forbus)
	{
		case 'Rotulo':
		$q = " SELECT Tippro "
		. "       FROM " . $wbasedato . "_000002, " . $wbasedato . "_000001 "
		. "    WHERE  Artcod = '" . $parbus . "' "
		. "       AND Artest = 'on' "
		. "       AND Tipest = 'on' "
		. "       AND Tipcod = Arttip ";
		$res = mysql_query($q, $conex);
		$row = mysql_fetch_array($res);
		// $exp=explode('-', $parbus);
		// if (isset($exp[1]))
		if (isset($row[0]) and $row[0] == 'on')
		{
			$q = " SELECT Artcod, Artcom, Artgen, Artuni, Unides, Tippro, Tipcdo "
			. "       FROM " . $wbasedato . "_000002, " . $wbasedato . "_000001, movhos_000027 "
			. "    WHERE Artcod = '" . $parbus . "' "
			. "       AND Artest = 'on' "
			. "       AND Artuni= Unicod "
			. "       AND Tipest = 'on' "
			. "       AND Tipcod = Arttip "
			. "       AND Uniest='on' "
			. "    Order by 1 ";
		}
		else
		{
			$q = " SELECT C.Artcod, C.Artcom, C.Artgen, C.Artuni, B.Unides, D.Tippro, D.Tipcdo  "
			. "       FROM movhos_000026 A, movhos_000027 B, " . $wbasedato . "_000002 C, " . $wbasedato . "_000001 D, " . $wbasedato . "_000009 E"
			. "    WHERE A. Artcod = '" . $parbus . "' "
			. "       AND A. Artest = 'on' "
			. "       AND A. Artcod = E.Apppre "
			. "       AND E. Appest = 'on' "
			. "       AND E. Appcod = C.Artcod "
			. "       AND C. Artest = 'on' "
			. "       AND C. Artuni= B.Unicod "
			. "       AND B.Uniest='on' "
			. "       AND D.Tipcod = C.Arttip "
			. "       AND D.Tipest = 'on' "
			. "    Order by 1 ";
		}
		break;

		case 'Codigo':
		$q = " SELECT Artcod, Artcom, Artgen, Artuni, Unides, Tippro, Tipcdo "
		. "       FROM " . $wbasedato . "_000002, " . $wbasedato . "_000001, movhos_000027 "
		. "    WHERE Artcod like '%" . $parbus . "%' "
		. "       AND Artest = 'on' "
		. "       AND Artuni= Unicod "
		. "       AND Tipest = 'on' "
		. "       AND Tipcod = Arttip "
		. "       AND Uniest='on' "
		. "    Order by 1 ";

		break;

		case 'Nombre comercial':

		$q = " SELECT Artcod, Artcom, Artgen, Artuni, Unides, Tippro, Tipcdo "
		. "       FROM " . $wbasedato . "_000002,  " . $wbasedato . "_000001, movhos_000027 "
		. "    WHERE Artcom like '%" . $parbus . "%' "
		. "       AND Artest = 'on' "
		. "       AND Tipest = 'on' "
		. "       AND Tipcod = Arttip "
		. "       AND Artuni= Unicod "
		. "       AND Uniest='on' "
		. "    Order by 1 ";

		break;
		case 'Nombre genérico':

		$q = " SELECT Artcod, Artcom, Artgen, Artuni, Unides, Tippro, Tipcdo "
		. "       FROM " . $wbasedato . "_000002,  " . $wbasedato . "_000001, movhos_000027 "
		. "    WHERE Artgen like '%" . $parbus . "%' "
		. "       AND Artest = 'on' "
		. "       AND Tipest = 'on' "
		. "       AND Tipcod = Arttip "
		. "       AND Artuni= Unicod "
		. "       AND Uniest='on' "
		. "    Order by 1 ";

		break;
	}

	$res = mysql_query($q, $conex);
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		for ($i = 0;$i < $num;$i++)
		{
			$row = mysql_fetch_array($res);

			$productos[$i]['cod'] = $row[0];
			$productos[$i]['nom'] = str_replace('-', ' ', $row[1]);
			$productos[$i]['gen'] = str_replace('-', ' ', $row[2]);
			$productos[$i]['pre'] = $row[3] . '-' . $row[4];
			$productos[$i]['est'] = 'on';
			if ($row[5] == 'on')
			{
				$productos[$i]['lot'] = 'on';
			}
			else
			{
				$productos[$i]['lot'] = 'off';
			}

			if ($row[6] == 'on')
			{
				$productos[$i]['cdo'] = 'on';
			}
			else
			{
				$productos[$i]['cdo'] = 'off';
			}
		}
	}
	else
	{
		$productos = false;
	}
	return $productos;
}

/**
* se consultan todos los posibles lotes para el producto, si ya se ha ingresado un lote se manda a la funcion
* 
* @param caracter $parbus codigo del insumo
* @param caracter $cco centro de costos (codigo-nombre)
* @param caracter $lote lote que ha sido escogido previamente
* @param caracter $crear Si es devolver, solo se deben mostrar los lotes que han sido cargados a paciente
* @param caracter $tipo si es tipo cargar aplica lo de mostrar solo los lotes cargados previamente
* @param caracter $destino si es tipo C aca vendra la historia-ingreso del paciente
* @return vector lista de lotes para mostrar en drop down
*/
function consultarLotes($parbus, $cco, $lote, $accion, $destino)
{
	global $conex;
	global $wbasedato;

	if ($lote != '') // cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
	{
		if ($accion != 'Cargo')
		{
			$q = "   SELECT Concod from " . $wbasedato . "_000008 "
			. "    WHERE Conind = '-1' "
			. "      AND Concar = 'on' "
			. "      AND Conest = 'on' ";

			$res1 = mysql_query($q, $conex);
			$row2 = mysql_fetch_array($res1);

			$q = " SELECT distinct Plocod "
			. "        FROM " . $wbasedato . "_000006, " . $wbasedato . "_000007, " . $wbasedato . "_000004 "
			. "    WHERE   Mencon= '" . $row2[0] . "' "
			. "            and Mencco =mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
			. "            and Menccd = '" . $destino . "' "
			. "            and Mdecon = Mencon "
			. "            and Mdedoc = Mendoc "
			. "  		  and Mdeart = '" . $parbus . "' "
			. "            and Mdeest = 'on' "
			. "            and Menest = 'on' "
			. "            and Plocod= mid(Mdenlo,1,instr(Mdenlo,'-')-1) "
			. "            and mid(Mdenlo,1,instr(Mdenlo,'-')-1)= '" . $lote . "' "
			. "            and Plopro= '" . $parbus . "' "
			. "        ORDER BY " . $wbasedato . "_000004.id asc";

			$res = mysql_query($q, $conex);
			$num = mysql_num_rows($res);
			if ($num > 0)
			{
				$consultas[0] = $lote;
				$cadena = "Plocod != '" . $lote . "' AND";
				$inicio = 1;
			}
			else
			{
				$cadena = '';
				$inicio = 0;
			}
		}
		else
		{
			$consultas[0] = $lote;
			$cadena = "Plocod != '" . $lote . "' AND";
			$inicio = 1;
		}
	}
	else
	{
		$cadena = '';
		$inicio = 0;
	}

	$dias = date('d')-20;
	if ($dias > 0)
	{
		$fecha = date('Y') . '-' . date('m') . '-' . $dias;
	}
	else
	{
		$dias = 31 + $dias;
		$fecha = mktime(0, 0, 0, date("m")-1, $dias, date("Y"));
		$fecha = date('Y-m-d', $fecha);
	}

	if ($accion == 'Cargo')
	{
		$q = " SELECT Plocod, Plopro, Plocco, Plofcr, Plofve, Plohve, Plocin, Plosal, Ploela, Plocco, Ploest "
		. "       FROM " . $wbasedato . "_000004 "
		. "    WHERE " . $cadena . " "
		. "       Plopro = '" . $parbus . "' "
		. "       AND Plocco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
		. "       AND Ploest = 'on' "
		. "       AND Plosal > 0 "
		. "       AND Plofve >= '" . date('Y-m-d') . "' "
		. "    Order by 1 asc  ";
	}
	else
	{
		$q = "   SELECT Concod from " . $wbasedato . "_000008 "
		. "    WHERE Conind = '-1' "
		. "      AND Concar = 'on' "
		. "      AND Conest = 'on' ";

		$res1 = mysql_query($q, $conex);
		$row2 = mysql_fetch_array($res1);

		$q = " SELECT distinct Plocod "
		. "        FROM " . $wbasedato . "_000006, " . $wbasedato . "_000007, " . $wbasedato . "_000004 "
		. "    WHERE " . $cadena . " "
		. "            Mencon= '" . $row2[0] . "' "
		. "            and Mencco =mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
		. "            and Menccd = '" . $destino . "' "
		. "            and Mdecon = Mencon "
		. "            and Mdedoc = Mendoc "
		. "  		  and Mdeart = '" . $parbus . "' "
		. "            and Mdeest = 'on' "
		. "            and Menest = 'on' "
		. "            and Plocod= mid(Mdenlo,1,instr(Mdenlo,'-')-1) "
		. "            and Plopro= '" . $parbus . "' "
		. "        ORDER BY " . $wbasedato . "_000004.id desc";
	}

	$res = mysql_query($q, $conex);
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		for ($i = 0;$i < $num;$i++)
		{
			$row = mysql_fetch_array($res);

			$consultas[$inicio] = $row['Plocod'];
			$inicio++;
		}
		return $consultas;
	}

	if (!isset($consultas[0]))
	{
		return false;
	}
	else
	{
		return $consultas;
	}
}

/**
    * A partir de un numero de movimiento se consulta toda la info de ese movimiento
    * 
    * Pide:
    * 
    * @param caracter $concepto concepto del movimeitno a consultar
    * @param caracter $consecutivo numero del movimiento a consultar
    * 
    * devuelve:
    * @param date $fecha , fecha del movimeinto
    * @param caracter $tipo tipo de movimiento
    * @param vector $ccos vector con centro de costos desde donde se carga
    * @param vector $destinos o centro de costos de la averia o historia clinica con ingreso
    * @param caracter $historia numero de historia clinica
    * @param caracter $estado si se anulo o esta activo
    * @param caracter $cco centro de costos desde donde se carga
    * @param caracter $ingreso nuemro de ingreso
    * @param vector $insumos producto que se cargo
    * @param vector $lotes lista de lotes o lote que se cargo o devolvio
    * @param vector $unidades lista de unidad minima de trabajo del insumo
    * @param vector $inslis lista de insumos del producto
    */
function consultarTraslado($concepto, $consecutivo, $fecha, &$tipo, &$ccos, &$destinos, &$historia, &$estado, &$cco, &$ingreso, &$mart, &$mlot, &$mpre, &$mcan, &$mpaj, &$mcaj, &$mmov1, &$mmov2, $y)
{
	global $conex;
	global $wbasedato;

	$q = "SELECT Mdeart, Mdecan, Mdepre, Mdenlo, Mencco, Menccd, Menest, Artcom, Artgen, Artuni, Unides, Mdecaj, Mdepaj, Mendan "
	. "     FROM   " . $wbasedato . "_000007 A, " . $wbasedato . "_000006, " . $wbasedato . "_000002, movhos_000027 "
	. "   WHERE Mdecon = '" . $concepto . "' "
	. "     AND Mdedoc = '" . $consecutivo . "' "
	. "     AND Mdecon = Mencon "
	. "     AND Mdedoc = Mendoc "
	. "     AND Mdeart = Artcod "
	. "     AND Artest = 'on' "
	. "     AND Unicod = Artuni "
	. "     AND Uniest = 'on'";

	$res = mysql_query($q, $conex);
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		for ($i = $y;$i < $num + $y;$i++)
		{
			$row = mysql_fetch_array($res);

			$exp = explode('-', $row['Mencco']);
			if (isset($exp[1]))
			{
				$historia = $exp[0];
				$ingreso = $exp[1];

				$q = "SELECT Cconom  "
				. "     FROM   movhos_000011 "
				. "   WHERE Ccocod = '" . $row['Menccd'] . "' "
				. "     AND Ccoest = 'on' ";
				$res1 = mysql_query($q, $conex);
				$row1 = mysql_fetch_array($res1);

				$destinos[0] = $row['Menccd'] . '-' . $row1['Cconom'];
				$cco = $row['Menccd'] . '-' . $row1['Cconom'];
			}
			else
			{
				$q = "SELECT Cconom  "
				. "     FROM   movhos_000011 "
				. "   WHERE Ccocod = '" . $row['Mencco'] . "' "
				. "     AND Ccoest = 'on' ";
				$res1 = mysql_query($q, $conex);
				$row1 = mysql_fetch_array($res1);

				$ccos[0] = $row['Mencco'] . '-' . $row1['Cconom'];
				$cco = $row['Mencco'] . '-' . $row1['Cconom'];

				$exp = explode('-', $row['Menccd']);
				$historia = $exp[0];
				$ingreso = $exp[1];
			}

			if ($row['Menest'] == 'on')
			{
				$estado = 'Creado';
			}
			else
			{
				$estado = 'Desactivado';
			}

			$exp = explode('-', $row['Mdenlo']);
			if (isset($exp[1]))
			{
				$mart[$i] = $exp[1];
				$mlot[$i] = $exp[0];
			}
			else
			{
				$mart [$i] = $row['Mdeart'];
				$mlot [$i] = '';
			}

			$mpre[$i] = $row['Mdepre'];

			$q = "SELECT Artcom "
			. "     FROM   " . $wbasedato . "_000002, movhos_000027, " . $wbasedato . "_000001 "
			. "   WHERE Artcod='" . $mart[$i] . "' "
			. "     AND Unicod = Artuni "
			. "     AND Tipcod = Arttip "
			. "     AND Tipest = 'on' ";

			$res1 = mysql_query($q, $conex);
			$num1 = mysql_num_rows($res1);
			$row1 = mysql_fetch_array($res1);

			$mart[$i] = $mart[$i] . '-' . $row1[0];
			$mcan[$i] = $row['Mdecan'];
			$mcaj[$i] = $row['Mdecaj'];
			$mpaj[$i] = $row['Mdepaj'];
			$mmov1[$i] = $row['Mendan'];

			$exp = explode('-', $mmov1[$i]);
			$q = "SELECT Mendan "
			. "     FROM    " . $wbasedato . "_000006 "
			. "   WHERE Mencon='" . $exp[0] . "' "
			. "     AND Mendoc = '" . $exp[1] . "' ";

			$res1 = mysql_query($q, $conex);
			$num1 = mysql_num_rows($res1);
			$row1 = mysql_fetch_array($res1);

			$mmov2[$i] = $row1['Mendan'];
		}
		return true;
	}
	else
	{
		return false;
	}
}

/**
    * Consulta las difernetes presntaciones para un insumo
    * 
    * @param caracter $codigo codigo del insumo
    * @param caracter $cco centro de costos(codigo-descripcion)
    * @param caracter $unidad presentacion del insumo previamente seleccionado por el usuario
    * @return vector lista de presentaciones del insumo
    */
function consultarUnidades($codigo, $cco, $unidad)
{
	global $conex;
	global $wbasedato;

	if ($unidad != '') // cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
	{
		// consulto los conceptos
		$q = " SELECT Apppre, Artcom, Artgen, Appcnv, Appexi "
		. "        FROM  " . $wbasedato . "_000009, movhos_000026 "
		. "      WHERE Apppre='" . $unidad . "' "
		. "            and Appcco=mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
		. "            and Appest='on' "
		. "            and Apppre=Artcod "
		. "            and Appcod='". $codigo ."' ";

		$res1 = mysql_query($q, $conex);
		$num1 = mysql_num_rows($res1);
		if ($num1 > 0)
		{
			$row1 = mysql_fetch_array($res1);
			$enteras = floor($row1['Appexi'] / $row1['Appcnv']);
			$fracciones = $row1['Appexi'] % $row1['Appcnv'];
			$unidades[0] = $row1['Apppre'] . '-' . str_replace('-', ' ', $row1['Artcom']) . '-' . str_replace('-', ' ', $row1['Artgen']);
			$cadena = "Apppre != '" . $unidad . "' AND";
			$inicio = 1;
		}
		else
		{
			$cadena = '';
			$inicio = 0;
		}
	}
	else
	{
		$cadena = '';
		$inicio = 0;
	}
	// consulto los conceptos
	$q = " SELECT Apppre, Artcom, Artgen, Appcnv, Appexi "
	. "        FROM  " . $wbasedato . "_000009, movhos_000026 "
	. "      WHERE " . $cadena . " "
	. "             Appcod='" . $codigo . "' "
	. "            and Appcco=mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
	. "            and Appest='on' "
	. "            and Apppre=Artcod ";

	$res1 = mysql_query($q, $conex);
	$num1 = mysql_num_rows($res1);
	if ($num1 > 0)
	{
		for ($i = 0;$i < $num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);

			$enteras = floor($row1['Appexi'] / $row1['Appcnv']);
			$fracciones = $row1['Appexi'] % $row1['Appcnv'];
			$unidades[$inicio] = $row1['Apppre'] . '-' . str_replace('-', ' ', $row1['Artcom']) . '-' . str_replace('-', ' ', $row1['Artgen']);
			$inicio++;
		}
		return $unidades;
	}

	if (!isset($unidades[0]))
	{
		return false;
	}
	else
	{
		return $unidades;
	}
}

/**
    * Se buscan los movimientos realizados segun parametros de busqueda ingresados por el usuario
    * 
    * @param caracter $parcon valor de busqueda 1
    * @param caracter $parcon2 valor de busqueda dos (por ejemplo si se busca por historia es fehca incial)
    * @param caracter $parcon3 valor de busqueda tres (por ejemplo si se busca por historia es fehca final)
    * @param caracter $insfor si se busca por articulo, es forma de busqueda del articulo
    * @param caracter $forcon forma de busqueda del movimiento
    * @return vector , lista de movimeitos encontrados para desplegar en dropdown
    */
function BuscarTraslado($parcon, $parcon2, $parcon3, $insfor, $forcon, $accion)
{
	global $conex;
	global $wbasedato;

	if ($accion == 'Cargo')
	{
		$signo = -1;
	}
	else
	{
		$signo = 1;
	}

	switch ($forcon)
	{
		case 'Numero de movimiento':
		$exp = explode('-', $parcon);
		if (isset($exp[1]))
		{
			$q = "SELECT Mdecon, Mdedoc, A.Fecha_data "
			. "     FROM   " . $wbasedato . "_000007 A, " . $wbasedato . "_000008 "
			. "   WHERE Mdedoc = '" . $exp[1] . "' "
			. "     AND Mdeest = 'on' "
			. "     AND Mdecon = Concod "
			. "     AND Concar = 'on'  "
			. "    AND Conind = '" . $signo . "'  "
			. "     GROUP BY 1, 2, 3 ";
		}
		else
		{
			$q = "SELECT Mdecon, Mdedoc, A.Fecha_data "
			. "     FROM   " . $wbasedato . "_000007 A, " . $wbasedato . "_000008 "
			. "   WHERE Mdedoc = '" . $parcon . "' "
			. "     AND Mdeest = 'on' "
			. "     AND Mdecon = Concod "
			. "     AND Concar = 'on' "
			. "    AND Conind = '" . $signo . "'  "
			. "     GROUP BY 1, 2, 3 ";
		}
		break;

		case 'Articulo':
		if (!isset ($parcon2) or $parcon2 == '')
		{
			$parcon2 = date('Y-m') . '-01';
		}

		if (!isset ($parcon2) or $parcon2 == '')
		{
			$parcon3 = date('Y-m-d');
		}

		if ($insfor == 'Codigo')
		{
			$q = "SELECT Mdecon, Mdedoc, A.Fecha_data "
			. "     FROM   " . $wbasedato . "_000007 A, " . $wbasedato . "_000008 "
			. "   WHERE Mdeart like '%" . $parcon . "%' "
			. "     AND A.Fecha_Data between  '" . $parcon2 . "' and '" . $parcon3 . "'"
			. "     AND Mdeest = 'on' "
			. "     AND Mdecon = Concod "
			. "     AND  Concar = 'on' "
			. "    AND Conind = '" . $signo . "'  "
			. "     GROUP BY 1, 2, 3 ";
		}
		else if ($insfor == 'Nombre comercial')
		{
			$q = "SELECT Mdecon, Mdedoc, A.Fecha_data"
			. "     FROM   " . $wbasedato . "_000007 A , " . $wbasedato . "_000002, " . $wbasedato . "_000008 "
			. "   WHERE Artcom like '%" . $parcon . "%' "
			. "   AND Artest = 'on' "
			. "   AND Mdeart = Artcod "
			. "     AND A.Fecha_Data between  '" . $parcon2 . "' and '" . $parcon3 . "'"
			. "     AND Mdeest = 'on' "
			. "     AND Mdecon = Concod "
			. "     AND Concar = 'on' "
			. "    AND Conind = '" . $signo . "'  "
			. "     GROUP BY 1, 2, 3 ";
		}
		else
		{
			$q = "SELECT Mdecon, Mdedoc, A.Fecha_data"
			. "     FROM   " . $wbasedato . "_000007 A, " . $wbasedato . "_000002, " . $wbasedato . "_000008 "
			. "   AND Artgen like '%" . $parcon . "%' "
			. "   AND Artest = 'on' "
			. "   AND Mdeart = Artcod "
			. "     AND A.Fecha_Data between  '" . $parcon2 . "' and '" . $parcon3 . "'"
			. "     AND Mdeest = 'on' "
			. "     AND Mdecon = Concod "
			. "     AND Concar = 'on' "
			. "    AND Conind = '" . $signo . "'  "
			. "     GROUP BY 1, 2, 3 ";
		}
		break;

		case 'Historia':
		if (!isset ($parcon2) or $parcon2 == '')
		{
			$parcon2 = date('Y-m') . '-01';
		}

		if (!isset ($parcon2) or $parcon2 == '')
		{
			$parcon3 = date('Y-m-d');
		}

		$q = "SELECT Mdecon, Mdedoc, Menfec "
		. "     FROM   " . $wbasedato . "_000007, " . $wbasedato . "_000006, " . $wbasedato . "_000008 "
		. "   WHERE (mid(Mencco,1,instr(Mencco,'-')-1) = '" . $parcon . "' OR mid(Menccd,1,instr(Menccd,'-')-1) = '" . $parcon . "') "
		. "     AND Menfec between  '" . $parcon2 . "' and '" . $parcon3 . "'"
		. "     AND Menest = 'on' "
		. "     AND Mencon = Mdecon "
		. "     AND Mendoc = Mdedoc "
		. "     AND Mdeest = 'on' "
		. "     AND Mdecon = Concod "
		. "     AND Concar = 'on' "
		. "    AND Conind = '" . $signo . "'  "
		. "     GROUP BY 1, 2, 3 ";

		break;

		default:
		if (!isset ($parcon2) or $parcon2 == '')
		{
			$parcon2 = date('Y-m') . '-01';
		}

		if (!isset ($parcon2) or $parcon2 == '')
		{
			$parcon3 = date('Y-m-d');
		}

		if ($insfor == 'Codigo')
		{
			$q = "SELECT Mdecon, Mdedoc, Menfec "
			. "     FROM  " . $wbasedato . "_000007, " . $wbasedato . "_000006, " . $wbasedato . "_000008 "
			. "   WHERE ( Menccd = '" . $parcon . "' or Mencco = '" . $parcon . "') "
			. "     AND Menfec between  '" . $parcon2 . "' and '" . $parcon3 . "'"
			. "     AND Menest = 'on' "
			. "     AND Mencon = Mdecon "
			. "     AND Mendoc = Mdedoc "
			. "     AND Mdeest = 'on' "
			. "     AND Mdecon = Concod "
			. "     AND Concar = 'on' "
			. "    AND Conind = '" . $signo . "'  "
			. "     GROUP BY 1, 2, 3 ";
		}
		else if ($insfor == 'Nombre')
		{
			$q = "SELECT Mdecon, Mdedoc, Menfec "
			. "     FROM   " . $wbasedato . "_000007, " . $wbasedato . "_000006, movhos_000011, " . $wbasedato . "_000008 "
			. "   WHERE Cconom like '%" . $parcon . "%' "
			. "     AND Ccoest = 'on' "
			. "     AND (Menccd = Ccocod or Mencco = Ccocod) "
			. "     AND Menfec between  '" . $parcon2 . "' and '" . $parcon3 . "'"
			. "     AND Menest = 'on' "
			. "     AND Mencon = Mdecon "
			. "     AND Mendoc = Mdedoc "
			. "     AND Mdeest = 'on' "
			. "     AND Mdecon = Concod "
			. "     AND Concar = 'on' "
			. "    AND Conind = '" . $signo . "'  "
			. "     GROUP BY 1, 2, 3 ";
		}
		break;
	}

	$res1 = mysql_query($q, $conex);
	$num1 = mysql_num_rows($res1);
	if ($num1 > 0)
	{
		for ($i = 0; $i < $num1; $i++)
		{
			$row1 = mysql_fetch_array($res1);

			$q = "SELECT Mencon, Mendoc, A.Fecha_data "
			. "     FROM   " . $wbasedato . "_000006 A, " . $wbasedato . "_000008 "
			. "   WHERE Mendan = '" . $row1[0] . "-" . $row1[1] . "' "
			. "     AND Menest = 'on' "
			. "     AND Mencon = Concod "
			. "     AND Conane = 'on'  "
			. "    AND Conind = '" . ($signo * -1) . "'  ";

			$res3 = mysql_query($q, $conex);
			$num3 = mysql_num_rows($res3);
			$row3 = mysql_fetch_array($res3);

			if ($num3 > 0)
			{
				$consultas[$i] = $row3[0] . '-' . $row3[1] . '-(' . $row1[2] . ')';
			}
			else
			{
				return '';
			}
		}
		return $consultas;
	}
	else
	{
		return '';
	}
}

/**
    * Se buscan los movimientos realizados segun parametros de busqueda ingresados por el usuario
    * 
    * @param caracter $parcon valor de busqueda 1
    * @param caracter $parcon2 valor de busqueda dos (por ejemplo si se busca por historia es fehca incial)
    * @param caracter $parcon3 valor de busqueda tres (por ejemplo si se busca por historia es fehca final)
    * @param caracter $insfor si se busca por articulo, es forma de busqueda del articulo
    * @param caracter $forcon forma de busqueda del movimiento
    * @return vector , lista de movimeitos encontrados para desplegar en dropdown
    */
function BuscarTraslado2($numtra, $accion, $historia, $ingreso, $wusuario, &$fecha, &$mov)
{
	global $conex;
	global $wbasedato;

	if ($accion == 'Cargo')
	{
		$signo = -1;
	}
	else
	{
		$signo = 1;
	}

	$exp = explode('-', $numtra);

	$q = "SELECT A.id "
	. "     FROM   " . $wbasedato . "_000006 A, " . $wbasedato . "_000008 "
	. "   WHERE Mendoc = '" . $exp[1] . "' "
	. "     AND Menest = 'on' "
	. "     AND Mencon = Concod "
	. "     AND Menccd = '" . $historia . "-" . $ingreso . "' "
	. "     AND Concar = 'on'  "
	. "    AND Conind = '" . $signo . "'  ";

	$res1 = mysql_query($q, $conex);
	$row = mysql_fetch_array($res1);

	if ($row[0] != '')
	{
		$q = "SELECT Mencon, Mendoc, A.Fecha_data "
		. "     FROM   " . $wbasedato . "_000006 A, " . $wbasedato . "_000008 "
		. "   WHERE A.id >= '" . $row[0] . "' "
		. "     AND Menest = 'on' "
		. "     AND Mencon = Concod "
		. "     AND Menccd = '" . $historia . "-" . $ingreso . "' "
		. "     AND A.Seguridad = 'C-" . $wusuario . "' "
		. "     AND Concar = 'on'  "
		. "    AND Conind = '" . $signo . "'  ";

		$res1 = mysql_query($q, $conex);
		$num1 = mysql_num_rows($res1);
		if ($num1 > 0)
		{
			for ($i = 0; $i < $num1; $i++)
			{
				$row = mysql_fetch_array($res1);
				$fecha[$i] = $row[2];

				$q = "SELECT Mencon, Mendoc "
				. "     FROM   " . $wbasedato . "_000006 A, " . $wbasedato . "_000008 "
				. "   WHERE  Menest = 'on' "
				. "     AND Mencon = Concod "
				. "     AND Mendan = '" . $row[0] . "-" . $row[1] . "' "
				. "     AND A.Seguridad = 'C-" . $wusuario . "' "
				. "     AND Conane = 'on'  "
				. "    AND Conind = '" . - $signo . "'  ";

				$res3 = mysql_query($q, $conex);
				$row3 = mysql_fetch_array($res3);
				$mov[$i] = $row3[0] . '-' . $row3[1];
			}
			return true;
		}
		else
		{
			return false;
		}
	}
	else
	{
		$mov = '';
		return true;
	}
}

function BuscarTraslado3($accion, $historia, $ingreso, $wusuario, &$fecha, &$mov)
{
	global $conex;
	global $wbasedato;

	if ($accion == 'Cargo')
	{
		$signo = -1;
	}
	else
	{
		$signo = 1;
	}

	$q = "SELECT A.id "
	. "     FROM   " . $wbasedato . "_000006 A, " . $wbasedato . "_000008 "
	. "   WHERE A.Fecha_data = '" . date('Y-m-d') . "' "
	. "     AND Menest = 'on' "
	. "     AND Mencon = Concod "
	. "     AND Menccd = '" . $historia . "-" . $ingreso . "' "
	. "     AND Concar = 'on'  "
	. "    AND Conind = '" . $signo . "'  ";

	$res1 = mysql_query($q, $conex);
	$row = mysql_fetch_array($res1);

	if ($row[0] != '')
	{
		$q = "SELECT Mencon, Mendoc, A.Fecha_data "
		. "     FROM   " . $wbasedato . "_000006 A, " . $wbasedato . "_000008 "
		. "   WHERE A.Fecha_data = '" . date('Y-m-d') . "' "
		. "     AND Menest = 'on' "
		. "     AND Mencon = Concod "
		. "     AND Menccd = '" . $historia . "-" . $ingreso . "' "
		. "     AND Concar = 'on'  "
		. "    AND Conind = '" . $signo . "'  "
		. "    order by  A.id  desc ";

		$res1 = mysql_query($q, $conex);
		$num1 = mysql_num_rows($res1);
		if ($num1 > 0)
		{
			for ($i = 0; $i < $num1; $i++)
			{
				$row = mysql_fetch_array($res1);
				$fecha[$i] = $row[2];

				$q = "SELECT Mencon, Mendoc "
				. "     FROM   " . $wbasedato . "_000006 A, " . $wbasedato . "_000008 "
				. "   WHERE  Menest = 'on' "
				. "     AND Mencon = Concod "
				. "     AND Mendan = '" . $row[0] . "-" . $row[1] . "' "
				. "     AND Conane = 'on'  "
				. "    AND Conind = '" . - $signo . "'  ";

				$res3 = mysql_query($q, $conex);
				$row3 = mysql_fetch_array($res3);
				$mov[$i] = $row3[0] . '-' . $row3[1];
			}
			return true;
		}
		else
		{
			return false;
		}
	}
	else
	{
		$mov = '';
		return true;
	}
}

/**
    * Se valida que la historia este activa y que no este en procesos de alta
    * 
    * @param caracter $cco centro de costos (codigo-nombre)
    * @param caracter $historia numero de historia clinica
    * @param caracter $ingreso numero de ingreso, se consulta a partir de la validacion
    * @param caracter $mensaje mensaje que devuelve la funcion en caso de no encontrar la historia
    * @param caracter $nombre nombre del paciente que se extrae de la validacion
    * @param caracter $habitacion habitacion del paciente que se extrae de la validacion
    * @return boolean si encuentra la historia reotrna true
    */
function validarHistoria($cco, $historia, &$ingreso, &$mensaje, &$nombre, &$habitacion, &$servicio)
{
	global $conex;
	global $wbasedato;

	if ($historia == '0')
	{
		$q = " SELECT Ccohcr "
		. "     FROM movhos_000011 "
		. "   WHERE	Ccocod = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
		. "     AND	Ccoest = 'on'";

		$err = mysql_query($q, $conex);
		$row = mysql_fetch_array($err);

		if ($row['hcr'] == 'on')
		{
			$ingreso = '';
			$habitacion = '';
			$servicio = '';
			$val = true;
		}
		else
		{
			$mensaje = "ESTE CENTRO DE COSTOS NO PERMITE CARGOS A  LA HISTORIA CERO";
			return (false);
		}
	}
	else if (is_numeric($historia))
	{
		$q = "SELECT Oriing, Pacno1, Pacno2, Pacap1, Pacap2 "
		. "      FROM root_000037, root_000036 "
		. "     WHERE Orihis = '" . $historia . "' "
		. "       AND Oriori = '01' "
		. "       AND Oriced = Pacced ";

		$err = mysql_query($q, $conex);
		$num = mysql_num_rows($err);
		if ($num > 0)
		{
			$row = mysql_fetch_array($err);
			$ingreso = $row['Oriing'];
			$nombre = $row['Pacno1'] . ' ' . $row['Pacno2'] . ' ' . $row['Pacap1'] . ' ' . $row['Pacap2'];

			$q = "SELECT * "
			. "      FROM movhos_000018 "
			. "     WHERE Ubihis = '" . $historia . "' "
			. "       AND Ubiing = '" . $ingreso . "' "
			. "       AND Ubiald <> 'on' ";

			$err1 = mysql_query($q, $conex);
			$num1 = mysql_num_rows($err1);
			if ($num1 > 0)
			{
				$row = mysql_fetch_array($err1);
				if ($row['Ubiptr'] != 'on')
				{
					$habitacion = $row['Ubihac'];
					$servicio = $row['Ubisac'];
					if ($row['Ubialp'] == 'on')
					{
						$mensaje = "EL PACIENTE ESTA EN PROCESO DE ALTA";
					}
					else
					{
						$mensaje = '';
					}
					return (true);
				}
				else
				{
					$mensaje = "EL PACIENTE ESTA EN PROCESO DE TRASLADO";
					return(false);
				}
			}
			else
			{
				$mensaje = "EL PACIENTE TIENE ALTA DEFINITIVA";
				return(false);
			}
		}
		else
		{
			$mensaje = "EL PACIENTE NO SE ENCUENTRA ACTIVO";
			return (false);
		}
	}
	else if (!is_numeric($historia))
	{
		$mensaje = "LAS HISTORIAS CLINICAS DEBEN SER NUMERICAS";
		return (false);
	}
	return(true);
}
// ----------------------------------------------------------funciones de presentacion------------------------------------------------
/**
    * Escribe el titulo de la aplicacion, fecha y hora adicionalmente da el acceso a otros scripts
    */
function pintarTitulo($tipo)
{
	echo "<table ALIGN=CENTER width='50%'>";
	// echo "<tr><td align=center colspan=1 ><img src='/matrix/images/medical/general/logo_promo.gif' height='100' width='250' ></td></tr>";
	echo "<tr><td class='titulo1'>PRODUCCION CENTRAL DE MEZCLAS</td></tr>";
	echo "<tr><td class='titulo2'>Fecha: " . date('Y-m-d') . "&nbsp Hora: " . (string)date("H:i:s") . "</td></tr></table></br>";

	if ($tipo == 'C')
	{
		echo "<table ALIGN=CENTER width='90%' >";
		// echo "<tr><td align=center colspan=1 ><img src='/matrix/images/medical/general/logo_promo.gif' height='100' width='250' ></td></tr>";
		echo "<tr><td class='texto5' width='15%'><a style='text-decoration:none;color:black' href='cen_Mez.php?wbasedato=cen_mez'>PRODUCTOS</a></td>";
		echo "<td class='texto5' width='15%'><a style='text-decoration:none;color:black' href='lotes.php?wbasedato=lotes.php'>LOTES</a></td>";
		echo "<td class='texto6' width='15%'><a style='text-decoration:none;color:white' href='cargosprucm.php?wbasedato=lotes.php&tipo=C'>CARGOS A PACIENTES</a></td>";
		echo "<td class='texto5' width='15%'><a style='text-decoration:none;color:black' href='pos.php?wbasedato=lotes.php&tipo=A'>VENTA EXTERNA</a></td></TR>";
		// echo "<a href='cargos.php?wbasedato=lotes.php&tipo=A'><td class='texto5' width='15%'>AVERIAS</td></a>";
		// echo "<a href='descarte.php?wbasedato=cenmez'><td class='texto5' width='15%'>DESCARTES</td></TR></a>";
		echo "<tr><td class='texto6' >&nbsp;</td>";
		echo "<td class='texto6' >&nbsp;</td>";
		echo "<td class='texto6' >&nbsp;</td>";
		echo "<td class='texto6' >&nbsp;</td></tr></table>";
	}
}

/**
    * funcion que pinta formulario de html para la busqueda de un documento determinado
    * 
    * @param vector $consultas vectorpara desplegar dropdown con lista de movimientos encontrados sgun parametros de usuario
    * @param caracter $forcon forma de busqueda escogida anterirormente por el usuario
    * @param carater $tipo si es un cargo a paciente o en una averia
    */
function pintarBusqueda( $consultas, $forcon, $tipo, $accion)
{
	echo "<table border=0 ALIGN=CENTER width=90%>";
	echo "<form name='producto2' action='cargosprucm.php' method=post>";
	echo "<tr><td class='titulo3' colspan='3' align='center'>Consulta: ";
	echo "<select name='forcon' class='texto5' onchange='enter7()'>";
	echo "<option>" . $forcon . "</option>";
	if ($forcon != 'Numero de movimiento')
	echo "<option>Numero de movimiento</option>";

	if ($forcon != 'Historia')
	echo "<option>Historia</option>";

	if ($forcon != 'Articulo')
	echo "<option>Articulo</option>";
	echo "</select>";
	switch ($forcon)
	{
		case '':
		echo "&nbsp";
		break;

		case 'Numero de movimiento':
		echo "<input type='TEXT' name='parcon' value='' size=10 class='texto5'>&nbsp;<INPUT TYPE='button' NAME='buscar' VALUE='Buscar' onclick='enter7()' class='texto5'> ";
		break;

		case 'Articulo':
		echo "</tr><tr><td class='titulo3' colspan='3' align='center'> Consulta de " . $forcon . ": ";
		echo "<select name='insfor' class='texto5'>";
		echo "<option>Codigo</option>";
		echo "<option>Nombre comercial</option>";
		echo "<option>Nombre genérico</option>";
		echo "</select>";
		echo "<input type='TEXT' name='parcon' value='' size=10 class='texto5'>&nbsp; ";
		echo "&nbsp; Fecha inicial:<input type='TEXT' name='parcon2' value='' size=10 class='texto5'>&nbsp;Fecha final:<input type='TEXT' name='parcon3' value='' size=10 class='texto5'>&nbsp;<INPUT TYPE='button' NAME='buscar' VALUE='Buscar' onclick='enter7()' class='texto5'> ";
		echo "</tr><tr><td class='titulo3' colspan='3' align='center'>";
		break;

		case 'Historia':
		echo "</tr><tr><td class='titulo3' colspan='3' align='center'> Consulta de " . $forcon . ": ";
		echo "<input type='TEXT' name='parcon' value='' size=10 class='texto5'>&nbsp; ";
		echo "&nbsp; Fecha inicial:<input type='TEXT' name='parcon2' value='' size=10 class='texto5'>&nbsp;Fecha final:<input type='TEXT' name='parcon3' value='' size=10 class='texto5'>&nbsp;<INPUT TYPE='button' NAME='buscar' VALUE='Buscar' onclick='enter7()' class='texto5'> ";
		echo "</tr><tr><td class='titulo3' colspan='3' align='center'>";
		break;

		default:
		echo "</tr><tr><td class='titulo3' colspan='3' align='center'> Consulta de " . $forcon . ": ";
		echo "<select name='insfor' class='texto5'>";
		echo "<option>Codigo</option>";
		echo "<option>Nombre</option>";
		echo "</select>";
		echo "<input type='TEXT' name='parcon' value='' size=10 class='texto5'>&nbsp; ";
		echo "&nbsp; Fecha inicial:<input type='TEXT' name='parcon2' value='' size=10 class='texto5'>&nbsp;Fecha final:<input type='TEXT' name='parcon3' value='' size=10 class='texto5'>&nbsp;<INPUT TYPE='button' NAME='buscar' VALUE='Buscar' onclick='enter7()' class='texto5'> ";
		echo "</tr><tr><td class='titulo3' colspan='3' align='center'>";
		break;
	}

	echo "&nbsp; Resultados: <select name='consulta' class='texto5' onchange='enter7()'>";
	echo "hola".$consultas[0];
	
	if ($consultas[0]['cod'] != '')
	{
		for ($i = 0;$i < count($consultas);$i++)
		{
			echo "<option>" . $consultas[$i] . "</option>";
		}
	}
	else
	{
		echo "<option value=''></option>";
	}
	echo "</select>";
	echo "</td></tr>";
	echo "<input type='hidden' name='tipo' value='" . $tipo . "'>";
	echo "<input type='hidden' name='accion' value='" . $accion . "'>";
	echo "</form>";
}

/**
    * Se pinta el formulario de ingreso de historia clinica
    * 
    * @param unknown_type $estado 
    * @param unknown_type $ccos 
    * @param unknown_type $numtra 
    * @param unknown_type $tipo 
    * @param unknown_type $historia 
    * @param unknown_type $destinos 
    * @param unknown_type $fecha 
    * @param unknown_type $ingreso 
    * @param unknown_type $nombre 
    * @param unknown_type $habitacion 
    * @param unknown_type $crear 
    * @param unknown_type $numcan 
    */
function pintarFormulario($estado, $ccos, $historia, $destinos, $fecha, $ingreso, $nombre, $habitacion, $tipo, $accion, $boton, $numtra, $aplica, $carro)
{
	echo "<form name='producto3' action='cargosprucm.php' method=post>";
	echo "<input type='hidden' name='tipo' value='" . $tipo . "'>";
	echo "<tr><td colspan=3 class='titulo3' align='center'><INPUT TYPE='submit' NAME='NUEVO' VALUE='Nuevo' class='texto5' ></td></tr>";
	echo "<input type='hidden' name='tipo' value='" . $tipo . "'></td>";
	echo "<input type='hidden' name='accion' value='" . $accion . "'></td>";
	echo "</table></form>";

	echo "<form name='producto' action='cargosprucm.php' method=post>";
	echo "<table border=0 ALIGN=CENTER width=90%>";

	echo "<tr><td colspan=3 class='titulo3' align='center'><b>Informacion general del " . $accion . "</b></td></tr>";
	echo "<tr><td class='texto1' colspan='2' align='center'>Centro de costos de origen: ";

	echo "<select name='cco' class='texto5' onchange='enter8()'>";
	if ($ccos[0] != '')
	{
		for ($i = 0;$i < count($ccos);$i++)
		{
			echo "<option >" . $ccos[$i] . "</option>";
		}
	}
	else
	{
		echo "<option value=''></option>";
	}
	echo "</select>";
	echo "</td>";

	echo "<td class='texto1' colspan='1' align='center'>Numero de historia: <input type='TEXT' name='historia' value='" . $historia . "' size=10 class='texto5'> ";

	echo "</td></tr>";

	echo "<tr><td class='texto2' colspan='1' align='left'>Numero de Ingreso: <input type='TEXT' name='ingreso' value='" . $ingreso . "' readonly='readonly' class='texto2' size='5'></td>";
	echo "<td class='texto2' colspan='1' align='left'>Habitacion: <input type='TEXT' name='habitacion' value='" . $habitacion . "' readonly='readonly' class='texto2' size='5'></td>";
	if($aplica)
	{
		echo "<td class='titulo5' colspan='1' align='left'>Nombre: <input type='TEXT' name='nombre' value='" . $nombre . "' readonly='readonly' class='texto2' size='40'></td></tr>";
	}
	else
	{
		echo "<td class='titulo4' colspan='1' align='left'>Nombre: <input type='TEXT' name='nombre' value='" . $nombre . "' readonly='readonly' class='texto2' size='40'></td></tr>";
	}
	echo "<tr>";
	if ($boton == 1)
	{
		echo "<td class='texto2' colspan='3' align='left'><input type='SUBMIT' name='ok' value='OK'></td></tr>";
	}
	
	/*if($accion=='Cargo')
	{
		if($carro=='on')
		{
			echo "<tr><td colspan=3 class='titulo3' align='center'><b><input type='checkbox' name='carro' checked>Para carro de dispensacion</b></td></tr>";
	
		}
		else 
		{
			echo "<tr><td colspan=3 class='titulo3' align='center'><b><input type='checkbox' name='carro'>Para carro de dispensacion</b></td></tr>";
		}
	}
	else 
	{
		echo "<input type='hidden' name='nada' value=''></td>";
	}*/

	echo "<input type='hidden' name='estado' value='" . $estado . "'></td>";
	echo "<input type='hidden' name='tipo' value='" . $tipo . "'></td>";
	echo "<input type='hidden' name='accion' value='" . $accion . "'></td>";
	echo "<input type='hidden' name='numtra' value='" . $numtra . "'></td>";
	echo "<input type='hidden' name='tvh' value='0'></td>";
	echo "<input type='hidden' name='grabar' value='0'></td>";
	echo "<input type='hidden' name='grabar' value='0'></td>";
	echo "</table></br>";
}

/**
    * Se pienta formulario de lista de insumos
    * 
    * @param unknown_type $insumos 
    * @param unknown_type $inslis 
    * @param unknown_type $unidades 
    * @param unknown_type $lotes 
    * @param unknown_type $presentaciones 
    * @param unknown_type $tipo 
    * @param unknown_type $preparacion 
    * @param unknown_type $escogidos 
    * @param unknown_type $numcan 
    */
function pintarInsumos($insumos, $unidades, $lotes, $tipo, $escogidos, $accion, $cco, $historia, $ingreso, $servicio, $confm, $confc, $carro)
{
	if( empty($insumos[0]['lot']) ) {
		$insumos[0]['lot']='';		
	}
	
	if( empty($insumos[0]['cdo']) ) {
		$insumos[0]['cdo']='';		
	}
	
	if( empty($insumos[0]['cod']) ) {
		$insumos[0]['cod']='';		
	}
	
	if( empty($unidades[0]) ){
		$unidades[0] = '';
	}
	
	echo "<table border=0 ALIGN=CENTER width=90%>";
	echo "<tr><td colspan='6' class='titulo3' align='center'><b>DETALLE DEL ARTICULO</b></td></tr>";

	echo "<tr><td class='texto1' colspan='6' align='center'>Buscar Articulo por: ";
	echo "<select name='forbus2' class='texto5'>";
	echo "<option>Rotulo</option>";
	echo "<option>Codigo</option>";
	echo "<option>Nombre comercial</option>";
	echo "<option>Nombre generico</option>";
	echo "</select><input type='TEXT' name='parbus2' value='' size=10 class='texto5'>&nbsp;<INPUT TYPE='submit' NAME='buscar' VALUE='Buscar'  class='texto5'></td> ";
	echo "<tr><td class='texto1' colspan='4' align='center'>Articulo: <select name='insumo' class='texto5' onchange='enter1()'>";
	if ($insumos != '' || !empty($insumos) )
	{
		for ($i = 0;$i < count($insumos);$i++)
		{
			echo "<option value='" . $insumos[$i]['cod'] . "-" . $insumos[$i]['nom'] . "-" . $insumos[$i]['gen'] . "-" . $insumos[$i]['pre'] . "-" . $insumos[$i]['lot'] . "-" . $insumos[$i]['est'] . "'>" . $insumos[$i]['cod'] . "-" . $insumos[$i]['nom'] . "</option>";
		}
		echo "<input type='hidden' name='insumos[0]['lot']' value='" . $insumos[0]['lot'] . "'></td>";
		echo "<input type='hidden' name='insumos[0]['cdo']' value='" . $insumos[0]['cdo'] . "'></td>";
	}
	else
	{
		echo "<option ></option>";
	}
	echo "</select></td>";
	if ($insumos[0]['lot'] == 'on')
	{
		echo "<td class='texto1' colspan='2' align='center'>Lote: <select name='lote' class='texto5'>";
		if (is_array($lotes))
		{
			for ($i = 0;$i < count($lotes);$i++)
			{
				echo "<option>" . $lotes[$i] . "</option>";
			}
		}
		else
		{
			echo "<option ></option>";
		}
		echo "</select></td></tr>";
		$ind = 0;
		
	}
	else if (is_array($unidades))
	{
		echo "<input type='hidden' name='lote' value=''>";
		echo "<td class='texto1' colspan='2' align='center'>Presentacion: <select name='prese' class='texto5' onchange='enter1()'>";
		if ($unidades[0] != '')
		{
			for ($i = 0;$i < count($unidades);$i++)
			{
				echo "<option>" . $unidades[$i] . "</option>";
			}
		}
		else
		{
			echo "<option ></option>";
		}
		echo "</select></td></tr>";
		$ind = 0;
	}
	else
	{
		echo "<td class='texto1' colspan='2' align='center'>&nbsp;</td></tr>";
	}

	echo "<tr><td colspan='6' class='titulo3' align='center'>&nbsp</td></tr>";

	//Precondiciones de grabado pare el KE
	$packe = array();
	$packe['his'] = $historia;
	$packe['ing'] = $ingreso;
	condicionesKE( $packe, $insumos[0]['cod'] ); 
	if ( $insumos != '' and ($lotes[0] != '' or $unidades[0] != ''))
	{
		if ($lotes[0] != '')
		{
			$var = $lotes[0];
		}
		else
		{
			$var = urlencode($unidades[0]);
		}
		if( $insumos[0]['cdo']=='on' && false )
		{
			if ($accion == 'Cargo')
			{
//				$packe = array();
//				$packe['his'] = $historia;
//				$packe['ing'] = $ingreso; 
//				condicionesKE( $packe, $insumos[0]['cod'] );
				if( !$packe['ke'] ){
					$grab=cargoCM($insumos[0]['cod'], $cco, $var, $historia, $ingreso, $servicio, &$error, $carro);
					if($grab)
					{
						echo "<input type='hidden' name='confm' value='SE HA REALIZADO EL CARGO EXITOSAMENTE'></td>";
						echo "<input type='hidden' name='confc' value='#DDDDDD'></td>";
					}
					else
					{
						echo "<input type='hidden' name='confm' value='".$error['ok']."'></td>";
						echo "<input type='hidden' name='confc' value='red'></td>";
					}
				}
				else{
					//Verificando precondiciones para grabar en el KE
					if( $packe['act'] )
					{
						if( $packe['gra'] )
						{
							if( $packe['con'] )
							{
								if( $packe['act'] )
								{
									if( $packe['art'] )
									{
										if( $packe['sal'] )
										{
											$grab=cargoCM($insumos[0]['cod'], $cco, $var, $historia, $ingreso, $servicio, &$error, $carro);
											
											if( $grab )
												$val = registrarArticuloKE( $insumos[0]['cod'], $historia, $ingreso );
											
											if( $grab && $val )
											{						
												echo "<input type='hidden' name='confm' value='SE HA REALIZADO EL CARGO EXITOSAMENTE'></td>";
												echo "<input type='hidden' name='confc' value='#DDDDDD'></td>";
											}
											else
											{
												if( !val ){
													$error['ok'] = 'NO SE PUDO REGISTRAR EL ARTICULO';
												}
												echo "<input type='hidden' name='confm' value='".$error['ok']."'></td>";
												echo "<input type='hidden' name='confc' value='red'></td>";
											}
										}
										else{
											$error['ok'] = 'EL ARTICULO YA FUE DISPENSADO';
											echo "<input type='hidden' name='confm' value='".$error['ok']."'></td>";
											echo "<input type='hidden' name='confc' value='red'></td>";
										}
									}
									else{
										$error['ok'] = 'EL ARTICULO NO ESTA CARGADO AL PACIENTE';
										echo "<input type='hidden' name='confm' value='".$error['ok']."'></td>";
										echo "<input type='hidden' name='confc' value='red'></td>";
									}
								}
								else{
									$error['ok'] = 'EL KE NO HA SIDO ACTUALIZADO';
									echo "<input type='hidden' name='confm' value='".$error['ok']."'></td>";
									echo "<input type='hidden' name='confc' value='red'></td>";
								}
							}
							else{
								$error['ok'] = 'EL KE NO HA SIDO CONFIRMADO';
								echo "<input type='hidden' name='confm' value='".$error['ok']."'></td>";
								echo "<input type='hidden' name='confc' value='red'></td>";
							}
						}
						else
						{
							$error['ok'] = 'EL PACIENTE NO TIENE KE GRABADO';
							echo "<input type='hidden' name='confm' value='".$error['ok']."'></td>";
							echo "<input type='hidden' name='confc' value='red'></td>";
						}
					}
					else
					{
						$error['ok'] = 'EL PACIENTE NO TIENE KE ACTUALIZADO';
						echo "<input type='hidden' name='confm' value='".$error['ok']."'></td>";
						echo "<input type='hidden' name='confc' value='red'></td>";
					}
				}
			}
			else
			{
				$exp = explode('-', $cco);
				$ccs['cod']=$exp[0];
				$ccs['nom']=$exp[1];

				$art['cod']=$insumos[0]['cod'];
				$art['nom']=$insumos[0]['nom'];
				$art['can']=1;

				$pac['his']=$historia;
				$pac['ing']=$ingreso;
				$pac['sac']=$servicio;
				$grab=devolucionCM($ccs, $art, $pac, &$error, &$dronum, &$drolin, $carro);

				if($grab)
				{
					echo "<input type='hidden' name='confm' value='SE HA REALIZADO EL CARGO EXITOSAMENTE'></td>";
					echo "<input type='hidden' name='confc' value='#DDDDDD'></td>";
				}
				else
				{
					echo "<input type='hidden' name='confm' value='".$error['ok']."'></td>";
					echo "<input type='hidden' name='confc' value='red'></td>";
				}
			}

									?>
									<script>
									document . producto . insumo.options[document.producto.insumo.selectedIndex].value='';
									document . producto . submit();
                                     </script >
                                    <?php
		}
		else
		{
			if($accion == 'Cargo' )
			{
				if( ( !$packe['ke'] || ($packe['ke'] && $packe['act'] && $packe['con'] && $packe['gra']) && $packe['art'] )){
					echo "<td class='texto2' colspan='6' align='left'><a href='cargo.php?cod=" . $insumos[0]['cod'] . "&cco=" . $cco . "&var=" . $var . "&historia=" . $historia . "&ingreso=" . $ingreso . "&servicio=" . $servicio . "&carro=" . $carro . "' target='_blank'><font size='4'>CARGAR</a></td></tr>";
				}
			}
			else
			{
				echo "<td class='texto2' colspan='6' align='left'><a href='devolucion.php?cod=" . $insumos[0]['cod'] . "&cco=" . $cco . "&var=" . $var . "&historia=" . $historia . "&ingreso=" . $ingreso . "&servicio=" . $servicio . "&carro=" . $carro . " ' target='_blank'><font size='4'>DEVOLVER</a></td></tr>";
			}
		}
	}
	else if ($confm!='')
	{
		echo "<td bgcolor='".$confc."' colspan='6' align='center'><font color='#003366'><b>".$confm."</b></font></td></tr>";
	}
	
	//Mostrando la lista de articulos para un paciente con KE
	if( $packe['ke'] ){
		
		if( empty($confm) ){
			if( $packe['gra'] ){

				if( $packe['act'] ){
					if( !$packe['con'] ){
						$error['okke'] = 'EL PACIENTE NO TIENE KE CONFIRMADO';
						echo "<td bgcolor='red' colspan='6' align='center'><font color='#003366'><b>".$error['okke']."</b></font></td></tr>";
					}
				}
				else{
					$error['okke'] = 'EL PACIENTE NO TIENE KE ACTUALIZADO';
					echo "<td bgcolor='red' colspan='6' align='center'><font color='#003366'><b>".$error['okke']."</b></font></td></tr>";
				}
			}
			else{
				$error['okke'] = 'EL PACIENTE NO TIENE KE GRABADO';
				echo "<td bgcolor='red' colspan='6' align='center'><font color='#003366'><b>".$error['okke']."</b></font></td></tr>";
			}
		}
		
		echo "<tr><td colspan=8 align='center' class='titulo3'><font size='3'>PACIENTE CON KE</font></td></tr>";
		
		$listart = ArticulosXPaciente( $historia, $ingreso );

		for( $i = 0; $i < count($listart); $i++ ){
			if( $i == 0 ){
				echo "<tr><td colspan='8' class='titulo3' align='center'><font size='3'>ARTICULOS REGISTRADOS EN EL KARDEX ELECTRONICO</font></td></tr>
				<tr class='texto1' align='center'>
					<td>Codigo</td>
					<td>Nombre</td>
					<td>Cantidad a dispensar</td>
					<td>Cantidad dispensada</td>					
					<td>Saldo</td>
				</tr>";
			}
			$class='texto3';
			if( $listart[$i]['sal'] > 0 ){
			echo "<tr>
				<td class='" . $class . "' align='center'>{$listart[$i]['cod']}</td>
				<td class='" . $class . "' align='center'>{$listart[$i]['nom']}</td>
				<td class='" . $class . "' align='center'>{$listart[$i]['cdi']}</td>
				<td class='" . $class . "' align='center'>{$listart[$i]['dis']}</td>
				<td class='" . $class . "' align='center'>{$listart[$i]['sal']}</td>
			</tr>";
			}
		}
	}

	echo "<input type='hidden' name='accion' value='" . $accion . "'></td>";
	echo "<input type='hidden' name='realizar' value='0'></td>";
	echo "<input type='hidden' name='eliminar' value='0'></td>";
	echo "</table></br></form>";
}

/**
    * Se pinta lo que se ha cargado en el dia, modificaicion 2007-07-18
    * 
    * @param vector $cargado informacion de lo que se ha cargado en el dia
    */
function pintarCargos($mart, $mlot, $mpre, $mcan, $mpaj, $mcaj, $mmov1, $mmov2, $text)
{
	$ant = '';
	$cont = 0;
	for ($i = 0;$i < count($mart);$i++)
	{
		if ($ant != $mmov1[$i])
		{
			$cont++;
			$ant = $mmov1[$i];
		}
	}

	echo "<table border=0 ALIGN=CENTER width=90%>";
	echo "<tr><td colspan=8 class='titulo3' align='center'><b><font size='3'>NUMERO DE MOVIMIENTOS ".$text.": </font><font size='5'>" . $cont . "</font></b></td></tr>";
	echo "<tr><td class='texto1' align='center'>MOVIMIENTO CENTRAL</td>";
	echo "<td class='texto1' align='center'>MOVIMIENTO INTEGRADOR</td>";
	echo "<td class='texto1' align='center'>ARTICULO</td>";
	echo "<td class='texto1' align='center'>LOTE</td>";
	echo "<td class='texto1' align='center'>PRESENTACION</td>";
	echo "<td class='texto1' align='center'>CANTIDAD</td>";
	echo "<td class='texto1' align='center'>AJUSTE</td>";
	echo "<td class='texto1' align='center'>CANTIDAD</td></tr>";

	$ant = '';
	$class1 = 'texto3';
	$class = 'texto4';
	for ($i = 0;$i < count($mart);$i++)
	{
		if ($ant != $mmov1)
		{
			$class2 = $class;
			$class = $class1;
			$class1 = $class2;
			$ant = $mmov1;
		}

		echo "<tr><td class='" . $class . "' align='center'>" . $mmov1 [$i] . "</td>";
		echo "<td class='" . $class . "' align='center'>" . $mmov2 [$i] . "</td>";
		echo "<td class='" . $class . "' align='center'>" . $mart [$i] . "</td>";
		echo "<td class='" . $class . "' align='center'>" . $mlot [$i] . "</td>";
		echo "<td class='" . $class . "' align='center'>" . $mpre [$i] . "</td>";
		echo "<td class='" . $class . "' align='center'>" . $mcan [$i] . "</td>";
		echo "<td class='" . $class . "' align='center'>" . $mpaj [$i] . "</td>";
		echo "<td class='" . $class . "' align='center'>" . $mcaj [$i] . "</td></tr>";
	}
	echo "</table></br>";
}

/**
    * =========================================================PROGRAMA==========================================================================
    */
session_start();
if (!isset($user))
{
	if (!isset($_SESSION['user']))
	session_register("user");
}

if (!isset($_SESSION['user']))
echo "error";
else
{
	$wbasedato = 'cenpro';
	

	

	if (!isset($tipo))
	{
		$tipo = 'A';
	}
	pintarTitulo($tipo); //Escribe el titulo de la aplicacion, fecha y hora adicionalmente da el acceso a otros scripts
	if (!isset($accion))
	{
		$accion = 'Cargo';
	}
	$bd = 'movhos';
	// invoco la funcion connectOdbc del inlcude de ana, para saber si unix responde, en caso contrario,
	// este programa no debe usarse
	// include_once("pda/tablas.php");
	include_once("movhos/fxValidacionArticulo.php");
	include_once("movhos/registro_tablas.php");
	include_once("movhos/otros.php");
	include_once("CENPRO/funciones.php");
	include_once("CENPRO/cargoCM.php");
	include_once("CENPRO/devolucionCM.php");
	connectOdbc(&$conex_o, 'facturacion');
	if ($conex_o != 0)
	{
		// consulto los datos del usuario de la sesion
		$pos = strpos($user, "-");
		$wusuario = substr($user, $pos + 1, strlen($user)); //extraigo el codigo del usuario
		// consulto los centros de costos que se administran con esta aplicacion
		// estos se cargan en un select llamado ccos.
		if (isset($cco))
		{
			$ccos = consultarCentros($cco);
		}
		else
		{
			$ccos = consultarCentros('');
		}
		// si el tipo es un cargo a paciente se pregunta la historia del paciente
		// y se valida que esta historia exita.
		if ((isset($historia) and $historia != ''))
		{
			$historia=trim($historia);
			$val = validarHistoria($cco, $historia, &$ingreso, &$mensaje, &$nombre, &$habitacion, &$servicio);
			if (!$val)
			{
				pintarAlert1($mensaje);
				$historia = '';
				$ingreso = '';
				$nombre = '';
				$habitacion = '';
			}
			else if ($mensaje!='')
			{
				pintarAlert1($mensaje);
			}
			// 2007-07-18
			/**
                * if ((!isset($consulta) or $consulta == '') and (!isset($grabar) or $grabar != '1'))
                * {
                * $cargado = consultarCargos($historia, $ingreso, $crear, date('Y-m-d'), $cco);
                * }
                */
		}
		else // si aun no se ha ingresado historia se incializa en cero
		{
			$historia = '';
			$ingreso = '';
			$nombre = '';
			$habitacion = '';
		}

		if (!isset($estado))
		{
			// para saber que mensaje desplegar al pintar el formulario
			$estado = 'inicio';
		}
		// se estamos buscando un movimiento determinado, se realiza la busqueda con los parametros ingresados por usuario
		if (isset ($parcon) and $parcon != '')
		{
			if (!isset ($insfor))
			{
				$insfor = '';
			}
			if (!isset ($parcon2))
			{
				$parcon2 = '';
			}
			if (!isset ($parcon3))
			{
				$parcon3 = '';
			}
			$consultas = BuscarTraslado($parcon, $parcon2, $parcon3, $insfor, $forcon, $accion);
			$consulta = $consultas[0];
		}
		// si esta setiado consulta se busca un cargo determinado
		if (isset($consulta) and $consulta != '')
		{
			$exp = explode('-', $consulta);
			$numtra = $exp[0] . '-' . $exp[1];
			$exp2 = explode('(', $consulta);
			$fecha = substr($exp2[1], 0, (strlen($exp2[1])-1));
			$res = consultarTraslado($exp[0], $exp[1], $fecha, &$tipo, &$ccos, &$destinos, &$historia, &$estado, &$cco, &$ingreso, &$mart, &$mlot, &$mpre, &$mcan, &$mpaj, &$mcaj, $mmov1, $mmov2, 0);
			if (!$res)
			{
				pintarAlert1('No se encuentra un movimiento realizado con las carcteristicas ingresadas');
				$mart = '';
			}
			else
			{
				$ingres = $ingreso;
				validarHistoria($cco, $historia, &$ingres, &$mensaje, &$nombre, &$habitacion, &$servicio);
				if (!isset($servicio))
				{
					$servicio = '';
				}
			}
		}
		else if ((!isset ($numtra) or $numtra == '')and isset($historia) and $historia != '')
		{
			if ($accion == 'Cargo')
			{
				$numtra = consultarConsecutivo(-1);
			}
			else
			{
				$numtra = consultarConsecutivo(1);
			}
			
			$parad = BuscarTraslado3($accion, $historia, $ingreso, $wusuario, &$fechad, &$movd);

			if ($parad)
			{
				$z = 0;
				for($i = 0; $i < count($movd); $i++)
				{
					if ($movd[$i] != '')
					{
						$exp = explode('-', $movd[$i]);
						$res = consultarTraslado($exp[0], $exp[1], $fechad, &$tipod, &$ccosd, &$destinosd, &$historiad, &$estadod, &$ccod, &$ingresod, &$martd, &$mlotd, &$mpred, &$mcand, &$mpajd, &$mcajd, $mmov1d, $mmov2d, $z);
						$z = count($martd);
					}
				}
			}
		}
		else if (isset ($numtra) and isset($historia) and $historia != '')
		{
			// consulto todos los movimientos desde el consecutivo inicial hasta el momento para la historia y el responsable
			$para = BuscarTraslado2($numtra, $accion, $historia, $ingreso, $wusuario, &$fecha, &$mov);

			if ($para)
			{
				$y = 0;
				for($i = 0; $i < count($mov); $i++)
				{
					if ($mov[$i] != '')
					{
						$exp = explode('-', $mov[$i]);
						$res = consultarTraslado($exp[0], $exp[1], $fecha, &$tipo, &$ccos, &$destinos, &$historia, &$estado, &$cco, &$ingreso, &$mart, &$mlot, &$mpre, &$mcan, &$mpaj, &$mcaj, $mmov1, $mmov2, $y);
						$y = count($mart);
					}
				}
			}

			$parad = BuscarTraslado3($accion, $historia, $ingreso, $wusuario, &$fechad, &$movd);

			if ($parad)
			{
				$z = 0;
				for($i = 0; $i < count($movd); $i++)
				{
					if ($movd[$i] != '')
					{
						$exp = explode('-', $movd[$i]);
						$res = consultarTraslado($exp[0], $exp[1], $fechad, &$tipod, &$ccosd, &$destinosd, &$historiad, &$estadod, &$ccod, &$ingresod, &$martd, &$mlotd, &$mpred, &$mcand, &$mpajd, &$mcajd, $mmov1d, $mmov2d, $z);
						$z = count($martd);
					}
				}
			}
		}
		// si la busqueda de movimentos no se utilizo se pnene las variables en vacio
		if (!isset($forcon))
		{
			$forcon = '';
		}
		if (!isset($consultas) || empty($consultas) )
		{
			$consultas = array();
		}
		// se pinta html de formulario para buscar un movimiento determinado
		pintarBusqueda($consultas, $forcon, $tipo, $accion);

		if (isset($parbus2) and $parbus2 != '')
		{
			// se desetean las variable por si se ha cambiado el producto
			// no queden cargados los insumos que lo conforman
			if (isset($insumos))
			{
				unset($insumos);
			}

			if (isset($inslis))
			{
				unset($inslis);
			}
		}
		
		if (isset($insumo) and $insumo == '')
		{
			if (isset($insumos))
			{
				unset($insumos);
			}
		}
		// si ya tenemos un insumo seleccionado, llenamos las variables de busqueda para volver a buscar el dato
		if (isset($insumo) and $insumo != '' and (!isset($accion) or $accion != 1) and (!isset($parbus2) or $parbus2 == '') and (!isset($consulta) or $consulta == ''))
		{
			$parbus2 = explode('-', $insumo);
			$parbus2 = $parbus2[0];
			$forbus2 = 'Codigo';
		}

		if (isset($parbus2) and $parbus2 != '')
		{
			// consultamos el insumo, el nombre y si es un insumo producto codificado o no codificado
			$insumos = consultarInsumos($parbus2, $forbus2);
			// si es un prducto
			if ($insumos[0]['lot'] == 'on')
			{
				$nueva = $historia . '-' . $ingreso;
				$lotes = consultarLotes($insumos[0]['cod'], $ccos[0], '', $accion, $nueva);

				if (!$lotes)
				{
					if ($accion == 'Cargo')
					{
						pintarAlert1('NO EXISTE LOTE CON SALDO PARA DEL PRODUCTO');
					}
					else
					{
						pintarAlert1('PACIENTE SIN CARGOS DEL PRODUCTO');
					}
				}

				$unidades = '';
			}
			else // si es un insumo consultamos las diferentes presentaciones para este
			{
				if ($forbus2 == 'rotulo') // depende de si ya se ingreso una presentacion o no, si se busca por rotulo lo que se busca es la presentacion
				{
					$unidades = consultarUnidades($insumos[0]['cod'], $ccos[0], $parbus2);

				}
				else
				{
					// $unidades=consultarUnidades($insumos[0]['cod'], $ccos[0], '');
					if (isset($prese) and $prese != '')
					{
						$prese = explode('-', $prese);
						$prese = $prese[0];
						$unidades = consultarUnidades($insumos[0]['cod'], $ccos[0], $prese);
					}
					else
					{
						$unidades = consultarUnidades($insumos[0]['cod'], $ccos[0], '');
						//echo $ccos[0];
					}
				}
			}
		}
		// se incializan en vacio todas las variables en caso de que no este setiadas
		if (!isset ($unidades))
		{
			$unidades = '';
			$unidades[0] = '';
		}
		if (!isset ($escogidos))
		{
			$escogidos = '';
		}

		if (!isset ($insumos) || empty($insumos) )
		{
			$insumos = array();
			$insumos = array();
//			$insumos[0]['cdo'] = '';
//			$insumos[0]['lot'] = '';
//			$insumos[0]['cod'] = ''; 
		}
		if (!isset($lotes))
		{
			$lotes = '';
			$lotes[0] ='';
		}
		// incializamos variables sin inicializar
		if (!isset ($fecha))
		{
			$fecha = date('Y-m-d');
		}
		
		if (!isset($carro))
		{
			$carro='off';
		}
		else 
		{
			$carro='on';
		}
		// pintamos el formulario donde se ingresa la historia clinica
		if ((isset ($historia) and $historia != ''))
		{
			$ser['cod']=$servicio;
			getCco(&$ser, 'C', '01');
			pintarFormulario($estado, $ccos, $historia, '', $fecha, $ingreso, $nombre, $habitacion, $tipo, $accion, 0, $numtra, $ser['apl'], $carro);
		}
		else
		{
			pintarFormulario($estado, $ccos, $historia, '', $fecha, $ingreso, $nombre, $habitacion, $tipo, $accion, 1, '', false, $carro);
		}
		// pintamos el formulario donde se ingresan los insumos de un producto y la lista a cargar
		
		if (!isset($confm))
		{
			$confm='';
			$confc='';
		}
		
		if ((isset ($historia) and $historia != ''))
		{
			pintarInsumos($insumos, $unidades, $lotes, $tipo, $escogidos, $accion, $ccos[0], $historia, $ingreso, $servicio, $confm, $confc, $carro);
		}

		if (isset($mart) and $mart != '')
		{
			pintarCargos($mart, $mlot, $mpre, $mcan, $mpaj, $mcaj, $mmov1, $mmov2, '');
		}

		if (isset($martd) and $martd != '')
		{
			pintarCargos($martd, $mlotd, $mpred, $mcand, $mpajd, $mcajd, $mmov1d, $mmov2d, 'DEL DIA');
		}
	}
	else
	{
		pintarAlert2('EN ESTE MOMENTO NO ES POSIBLE CONECTARSE CON UNIX PARA REALIZAR EL CARGO, POR FAVOR INGRESE MAS TARDE');
	}
}

        ?>
</body>
</html>