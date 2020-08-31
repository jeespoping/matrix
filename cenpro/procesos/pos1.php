<html>
<head>
<title>SALIDA A ENTIDADES EXTERNAS DE CENTRAL DE MEZCLAS</title>

<style type="text/css">
//
body {
	background: white url(portal.gif) transparent center no-repeat scroll;
}

.titulo1 {
	color: #FFFFFF;
	background: #006699;
	font-size: 9pt;
	font-family: Tahoma;
	font-weight: bold;
	text-align: center;
}

.titulo2 {
	color: #006699;
	background: #FFFFFF;
	font-size: 9pt;
	font-family: Tahoma;
	font-weight: bold;
	text-align: center;
}

.titulo3 {
	color: #003366;
	background: #A4E1E8;
	font-size: 9pt;
	font-family: Tahoma;
	font-weight: bold;
	text-align: center;
}

.texto1 {
	color: #003366;
	background: #FFDBA8;
	font-size: 7pt;
	font-family: Tahoma;
	font-weight: bold;
	text-align: center;
}

.texto2 {
	color: #003366;
	background: #DDDDDD;
	font-size: 7pt;
	font-family: Tahoma;
	font-weight: bold;
	text-align: center;
}

.texto3 {
	color: #003366;
	background: #FFFFFF;
	font-size: 7pt;
	font-family: Tahoma;
	font-weight: bold;
	text-align: center;
}

.texto4 {
	color: #003366;
	background: #f5f5dc;
	font-size: 7pt;
	font-family: Tahoma;
	font-weight: bold;
	text-align: center;
}

.texto6 {
	color: #FFFFFF;
	background: #006699;
	font-size: 7pt;
	font-family: Tahoma;
	font-weight: bold;
	text-align: center;
}

.texto5 {
	color: #003366;
	background: #FFFFFF;
	font-size: 7pt;
	font-family: Tahoma;
	font-weight: bold;
	text-align: center;
}
</style>

<script type="text/javascript">

function cambiarLote( lote ){
	nlo = new String();
	nlo = lote;
	for( var i = 0; i < document.producto.lote.options.length; i++ ){
		if( parseInt(document.producto.lote.options[i].text) == nlo || parseInt(document.producto.lote.options[i].value) == nlo ){
			document.producto.lote.selectedIndex = i;
			return;
		}
	}  
}

function ocultarDiv(){
	dv = document.getElementById("dvpass");
	ifr = document.getElementById("ifrpass");
	dv.style.display = "none";
	ifr.style.display = "none";
}

function divAceptar(){
	dv = document.getElementById("dvpass");
	ip = document.getElementById("pass2");
	document.producto.pwd.value = ip.value;

	document.producto.submit();
}

   function enter()
   {
   	document.producto.accion.value=1;
   	document.producto.submit();
   }

   function enter1( control )
   {
		var valor =  control.value;
		dv = document.getElementById("dvpass");
		ifr = document.getElementById("ifrpass");
		if( valor != "cargar" && dv.style.display == "none" ){
			ifr.style.display="";
			ifr.style.left=(parseInt(document.body.offsetWidth)-parseInt(dv.style.width))/2;
			ifr.style.top = (parseInt(document.body.offsetHeight)-parseInt(dv.style.height))/2;
			dv.style.display="";
			dv.style.left = (parseInt(document.body.offsetWidth)-parseInt(dv.style.width))/2;
			dv.style.top = (parseInt(document.body.offsetHeight)-parseInt(dv.style.height))/2;
			pass = document.getElementById("pass2");
			pass.focus();
   		}
		else{
   			document.producto.submit();
		}
   }
   
    function enter9()
   {
   	document.producto.prese.value='';
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

   function enter6()
   {
   	document.producto.cantidad.value='';
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

   function hacerFoco()
   {
   	if (document.producto.elements[10].value=='')
   	{
   		document.producto.elements[8].focus();
   	}
   	else
   	{
   		document.producto.elements[12].focus();
   	}

   }

   function validarFormulario()
   {
   	textoCampo = window.document.producto.cantidad.value;
   	textoCampo = validarEntero(textoCampo);
   	window.document.producto.cantidad.value = textoCampo;
   }

   function validarEntero(valor)
   {
   	if (isNaN(valor))
   	{
   		alert('Debe ingresar un numero entero');
   		return '';
   	}else if (valor<=0)
   	{
   		alert('Debe ingresar un numero entero mayor a cero');
   		return '';
   	}
   	else
   	{
   		return valor;
   	}

   }

    </script>

</head>

<body onload="hacerFoco()">

<?php
include_once("conex.php");

/*========================================================DOCUMENTACION PROGRAMA================================================================================*/
/*

1. AREA DE VERSIONAMIENTO

Nombre del programa:informatica.php
Fecha de creacion: 2007-07-06
Autor: Carolina Castano P
Ultima actualizacion: 2007-07-06


2. AREA DE DESCRIPCION:

Este script realiza las siguientes historias: traslado de insumo o producto codificado de centro de costos a central
y traslado de insumo o producto codificado de central a centro de costos


3. AREA DE VARIABLES DE TRABAJO

4. AREA DE TABLAS


VARIABLES

$numtra				Contiene el consecutivo por concepto de traslado
$cco				Centro de Costos
$inslis				Lista de insumos (Array con la lista de articulos para cargar o devolver
*/

/*========================================================FUNCIONES==========================================================================*/

//----------------------------------------------------------funciones de persitencia------------------------------------------------

/**
 * Pide un pasword para hacer efectivas las devoluciones
 * 
 */
function pedirPassword( $pass, $codcon ){
	
	global $conex;
	global $wbasedato;
	
	$valido = false;
	
	$cod = explode( "-", $codcon );
	$cod = $cod[0];
	
	$sql = "SELECT 
				conpwd
			FROM 
				{$wbasedato}_000008
			WHERE
				concod = '$cod'";
				
	$res = mysql_query( $sql, $conex );

	if( $rows = mysql_fetch_array( $res ) ){
		if( $pass == $rows[0] ){
			echo $valido;
			$valido = true;
		}
	} 
//	else{
//		echo "NOooooooooo!";
//	}
	
	return $valido;
}
/*
 * Consulta si un lote esta vencido o no. 
 * 
 * Parametros
 * $lote			Numero del lote
 * $producto		Producto
 * 
 * Valores Retornados
 * String 'on' 		En caso de que el lote este vencido
 * String 'off'		En caso de que el lote NO este vencido
 */

function consultarLoteVencido( $lote, $producto ){
	
	global $wbasedato;
	global $conex;
	
	//Consulta 17
	$sql = "SELECT Plofve, Plohve 
			FROM {$wbasedato}_000004 
			WHERE Plopro = '$producto' 
			AND Plocod = '$lote' ";

	$res = mysql_query( $sql, $conex );
	$row = mysql_fetch_array( $res ) or die( "Error - Consulta 17" );
	
	if( date("Y-m-d") > $row[0] || ( date("Y-m-d") == $row[0] && date("H:i:s") > $row[1] ) )
		return 'on';
	
	return 'off';
}

/*
 * Parametros
 *
 * $parcon
 * $parcon2
 * $parcon3
 * $insfor
 * $forcon		Valor por el que se busca un producto (Código, Nombre Comercial, Nombre Génerico)
 * $pintar		Escribe el titulo de la aplicacion, fecha y hora adicionalmente da el acceso a otros scripts
 * $inslis		Matrix de lista de insumos
 */
function BuscarTraslado($parcon, $parcon2,$parcon3, $insfor, $forcon, $pintar)
{
	global $conex;
	global $wbasedato;

	switch ($forcon)
	{
		case 'Numero de movimiento':
			$exp=explode('-',$parcon);
			if (isset($exp[1]))
			{
				if ($pintar==0)
				{
					$q= "SELECT Mdecon, Mdedoc, A.Fecha_data "
					."     FROM   ".$wbasedato."_000007 A, ".$wbasedato."_000008 "
					."   WHERE Mdecon = '".$exp[0]."' "
					."     AND Mdedoc = '".$exp[1]."' "
					."     AND Mdeest = 'on' "
					."     AND Mdecon = Concod "
					."     AND Conven = 'on' "
					."     GROUP BY 1, 2, 3 ";
				}
				else
				{
					$q= "SELECT Mdecon, Mdedoc, A.Fecha_data "
					."     FROM   ".$wbasedato."_000007 A, ".$wbasedato."_000008 "
					."   WHERE Mdecon = '".$exp[0]."' "
					."     AND Mdedoc = '".$exp[1]."' "
					."     AND Mdeest = 'on' "
					."     AND Mdecon = Concod "
					."     AND Conins = 'on' "
					."     GROUP BY 1, 2, 3 ";
				}
			}
			else
			{
				if ($pintar==0)
				{
					$q= "SELECT  Mdecon, Mdedoc, A.Fecha_data "
					."     FROM   ".$wbasedato."_000007 A, ".$wbasedato."_000008 "
					."   WHERE Mdedoc = '".$parcon."' "
					."     AND Mdeest = 'on' "
					."     AND Mdecon = Concod "
					."     AND Conven = 'on' "
					."     GROUP BY 1, 2, 3 ";
				}
				else
				{
					$q= "SELECT  Mdecon, Mdedoc, A.Fecha_data "
					."     FROM   ".$wbasedato."_000007 A, ".$wbasedato."_000008 "
					."   WHERE Mdedoc = '".$parcon."' "
					."     AND Mdeest = 'on' "
					."     AND Mdecon = Concod "
					."     AND Conins = 'on' "
					."     GROUP BY 1, 2, 3 ";
				}
			}
			break;

		case 'Articulo':
			if (!isset ($parcon2) or $parcon2=='')
			{
				$parcon2=date('Y-m').'-01';
			}

			if (!isset ($parcon2) or $parcon2=='')
			{
				$parcon3=date('Y-m-d');
			}

			if ($insfor=='Codigo')
			{
				if ($pintar==0)
				{
					$q= "SELECT Mdecon, Mdedoc, A.Fecha_data "
					."     FROM   ".$wbasedato."_000007 A, ".$wbasedato."_000008 "
					."   WHERE Mdeart = '".$parcon."' "
					."     AND A.Fecha_Data between  '".$parcon2."' and '".$parcon3."'"
					."     AND Mdeest = 'on' "
					."     AND Mdecon = Concod "
					."     AND Conven = 'on' "
					."     GROUP BY 1, 2, 3 ";
				}
				else
				{
					$q= "SELECT Mdecon, Mdedoc, A.Fecha_data "
					."     FROM   ".$wbasedato."_000007 A, ".$wbasedato."_000008 "
					."   WHERE Mdeart = '".$parcon."' "
					."     AND A.Fecha_Data between  '".$parcon2."' and '".$parcon3."'"
					."     AND Mdeest = 'on' "
					."     AND Mdecon = Concod "
					."     AND Conins = 'on' "
					."     GROUP BY 1, 2, 3 ";
				}
			}
			else if ($insfor=='Nombre comercial')
			{
				if ($pintar==0)
				{
					$q= "SELECT Mdecon, Mdedoc, A.Fecha_data"
					."     FROM   ".$wbasedato."_000007 A , ".$wbasedato."_000002, ".$wbasedato."_000008 "
					."   WHERE Artcom like '%".$parcon."%' "
					."   WHERE Artest = 'on' "
					."   WHERE Mdeart = Artcod "
					."     AND A.Fecha_Data between  '".$parcon2."' and '".$parcon3."'"
					."     AND Mdeest = 'on' "
					."     AND Mdecon = Concod "
					."     AND Conven = 'on' "
					."     GROUP BY 1, 2, 3 ";
				}
				else
				{
					$q= "SELECT Mdecon, Mdedoc, A.Fecha_data"
					."     FROM   ".$wbasedato."_000007 A , ".$wbasedato."_000002, ".$wbasedato."_000008 "
					."   WHERE Artcom like '%".$parcon."%' "
					."   WHERE Artest = 'on' "
					."   WHERE Mdeart = Artcod "
					."     AND A.Fecha_Data between  '".$parcon2."' and '".$parcon3."'"
					."     AND Mdeest = 'on' "
					."     AND Mdecon = Concod "
					."     AND Conins = 'on' "
					."     GROUP BY 1, 2, 3 ";
				}
			}
			else
			{
				if ($pintar==0)
				{
					$q= "SELECT Mdecon, Mdedoc, A.Fecha_data"
					."     FROM   ".$wbasedato."_000007 A, ".$wbasedato."_000002, ".$wbasedato."_000008 "
					."   WHERE Artgen like '%".$parcon."%' "
					."   WHERE Artest = 'on' "
					."   WHERE Mdeart = Artcod "
					."     AND A.Fecha_Data between  '".$parcon2."' and '".$parcon3."'"
					."     AND Mdeest = 'on' "
					."     AND Mdecon = Concod "
					."     AND Conven = 'on' "
					."     GROUP BY 1, 2, 3 ";
				}
				else
				{
					$q= "SELECT Mdecon, Mdedoc, A.Fecha_data"
					."     FROM   ".$wbasedato."_000007 A, ".$wbasedato."_000002, ".$wbasedato."_000008 "
					."   WHERE Artgen like '%".$parcon."%' "
					."   WHERE Artest = 'on' "
					."   WHERE Mdeart = Artcod "
					."     AND A.Fecha_Data between  '".$parcon2."' and '".$parcon3."'"
					."     AND Mdeest = 'on' "
					."     AND Mdecon = Concod "
					."     AND Conins = 'on' "
					."     GROUP BY 1, 2, 3 ";
				}
			}
			break;

		case 'Centro de costos de origen':
			if (!isset ($parcon2) or $parcon2=='')
			{
				$parcon2=date('Y-m').'-01';
			}

			if (!isset ($parcon2) or $parcon2=='')
			{
				$parcon3=date('Y-m-d');
			}


			if ($insfor=='Codigo')
			{
				$q= "SELECT Mdecon, Mdedoc, Menfec "
				."     FROM   ".$wbasedato."_000007, ".$wbasedato."_000006, ".$wbasedato."_000008 "
				."   WHERE Mencco = '".$parcon."' "
				."     AND Menfec between  '".$parcon2."' and '".$parcon3."'"
				."     AND Menest = 'on' "
				."     AND Mencon = Mdecon "
				."     AND Mendoc = Mdedoc "
				."     AND Mdeest = 'on' "
				."     AND Mdecon = Concod "
				."     AND Conven = 'on' "
				."     GROUP BY 1, 2, 3 ";

			}
			else if ($insfor=='Nombre')
			{
				$q= "SELECT Mdecon, Mdedoc, Menfec "
				."     FROM   ".$wbasedato."_000007, ".$wbasedato."_000006, movhos_000011, ".$wbasedato."_000008 "
				."   WHERE Cconom like '%".$parcon."%' "
				."     AND Ccoest = 'on' "
				."     AND Mencco = Ccocod "
				."     AND Menfec between  '".$parcon2."' and '".$parcon3."'"
				."     AND Menest = 'on' "
				."     AND Mencon = Mdecon "
				."     AND Mendoc = Mdedoc "
				."     AND Mdeest = 'on' "
				."     AND Mdecon = Concod "
				."     AND Conven = 'on' "
				."     GROUP BY 1, 2, 3 ";
			}
			break;

		case 'Centro de costos destino':
			if (!isset ($parcon2) or $parcon2=='')
			{
				$parcon2=date('Y-m').'-01';
			}

			if (!isset ($parcon2) or $parcon2=='')
			{
				$parcon3=date('Y-m-d');
			}

			if ($insfor=='Codigo')
			{
				$q= "SELECT Mdecon, Mdedoc, Menfec "
				."     FROM  ".$wbasedato."_000007, ".$wbasedato."_000006, ".$wbasedato."_000008 "
				."   WHERE Menccd = '".$parcon."' "
				."     AND Menfec between  '".$parcon2."' and '".$parcon3."'"
				."     AND Menest = 'on' "
				."     AND Mencon = Mdecon "
				."     AND Mendoc = Mdedoc "
				."     AND Mdeest = 'on' "
				."     AND Mdecon = Concod "
				."     AND Conven = 'on' "
				."     GROUP BY 1, 2, 3 ";
			}
			else if ($insfor=='Nombre')
			{
				$q= "SELECT Mdecon, Mdedoc, Menfec "
				."     FROM   ".$wbasedato."_000007, ".$wbasedato."_000006, movhos_000011, ".$wbasedato."_000008 "
				."   WHERE Cconom like '%".$parcon."%' "
				."     AND Ccoest = 'on' "
				."     AND Menccd = Ccocod "
				."     AND Menfec between  '".$parcon2."' and '".$parcon3."'"
				."     AND Menest = 'on' "
				."     AND Mencon = Mdecon "
				."     AND Mendoc = Mdedoc "
				."     AND Mdeest = 'on' "
				."     AND Mdecon = Concod "
				."     AND Conven = 'on' "
				."     GROUP BY 1, 2, 3 ";
			}
			break;
	}

	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	if ($num1>0)
	{
		for ($i=0; $i<$num1; $i++)
		{
			$row1 = mysql_fetch_array($res1);
			$consultas[$i]=$row1[0].'-'.$row1[1].'-('.$row1[2].')';
		}
		return $consultas;
	}
	else
	{
		return '';
	}

}

function consultarUnidades($codigo, $cco, $unidad, &$insumo)
{
	global $conex;
	global $wbasedato;

	if ($unidad!='') //cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
	{
		//consulto los conceptos
		$q =  " SELECT Apppre, Artcom, Artgen, Appcnv, Appexi, Artuni, Unides "
		."        FROM  ".$wbasedato."_000009, movhos_000026, movhos_000027 "
		."      WHERE Apppre='".$unidad."' "
		."            and Appcco=mid('".$cco."',1,instr('".$cco."','-')-1) "
		."            and Appest='on' "
		."            and Apppre=Artcod "
		."            and Artuni=Unicod ";


		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);
		if ($num1>0)
		{
			$row1 = mysql_fetch_array($res1);
			$enteras=floor($row1['Appexi']/$row1['Appcnv']);
			$fracciones=$row1['Appexi']%$row1['Appcnv'];
			$unidades[0]=$row1['Apppre'].'-'.str_replace('-',' ',$row1['Artcom']).'-'.str_replace('-',' ',$row1['Artgen']).'-'.$enteras.'-'.$fracciones;
			$cadena="Apppre != '".$unidad."' AND";
			$inicio=1;
			$insumo=$row1['Artuni'].'-'.$row1['Unides'];
		}
		else
		{
			$cadena='';
			$inicio=0;
		}
	}
	else
	{
		$cadena='';
		$inicio=0;
	}

	//consulto los conceptos
	$q =  " SELECT Apppre, Artcom, Artgen, Appcnv, Appexi, Artuni, Unides "
	."        FROM  ".$wbasedato."_000009, movhos_000026, movhos_000027 "
	."      WHERE ".$cadena." "
	."             Appcod='".$codigo."' "
	."            and Appcco=mid('".$cco."',1,instr('".$cco."','-')-1) "
	."            and Appest='on' "
	."            and Apppre=Artcod "
	."            and Artuni=Unicod ";


	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	if ($num1>0)
	{
		for ($i=0;$i<$num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);

			$enteras=floor($row1['Appexi']/$row1['Appcnv']);
			$fracciones=$row1['Appexi']%$row1['Appcnv'];
			$unidades[$inicio]=$row1['Apppre'].'-'.str_replace('-',' ',$row1['Artcom']).'-'.str_replace('-',' ',$row1['Artgen']).'-'.$enteras.'-'.$fracciones;
			$inicio++;
			if($inicio==1)
			{
				$insumo=$row1['Artuni'].'-'.$row1['Unides'];
			}
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


function consultarCentros($cco)
{
	global $conex;
	global $wbasedato;

	if ($cco!='') //cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
	{
		$ccos[0]=$cco;
		$cadena="A.Ccocod != mid('".$cco."',1,instr('".$cco."','-')-1) AND";
		$inicio=1;
	}
	else
	{
		$cadena='';
		$inicio=0;
	}

	//consulto los conceptos
	$q =  " SELECT A.Ccocod as codigo, B.Cconom as nombre"
	."        FROM movhos_000011 A, costosyp_000005 B "
	."      WHERE ".$cadena." "
	."        A.Ccoima = 'on' "
	."        AND A.Ccocod = B.Ccocod "
	."        AND A.Ccoest='on' ";


	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	if ($num1>0)
	{
		for ($i=0;$i<$num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);
			$ccos[$inicio]=$row1['codigo'].'-'.$row1['nombre'];
			$inicio++;
		}

	}
	else if ($inicio==0)
	{
		$ccos='';
	}

	return $ccos;
}


function consultarCco($cco)
{
	global $conex;
	global $wbasedato;

	$exp=explode('-',$cco);

	$q= " SELECT Ccoima "
	."       FROM movhos_000011  "
	."    WHERE Ccocod = '".$exp[0]."' "
	."       AND Ccoest = 'on' "
	."       AND Ccotra = 'on' ";

	$res = mysql_query($q,$conex);
	$row = mysql_fetch_array($res);

	$resultado=$row[0];
	return $resultado;
}

/**
 * Consulta el consecutivo que se grabara en los detalles del movimiento.
 * La función retorna un array con el codigo y el consecutivo
 * 
 * @param $tipo
 * @param $movimiento
 * @return unknown_type
 */

function consultarConsecutivo($tipo, $movimiento)
{
	global $conex;
	global $wbasedato;

	if ($movimiento==0)
	{
		$q= " SELECT Concod, Concon "
		."       FROM ".$wbasedato."_000008  "
		."    WHERE Conind = '".$tipo."' "
		."       AND Conven = 'on' "
		."       AND Conest = 'on' ";
	}
	else
	{
		$q= " SELECT Concod, Concon "
		."       FROM ".$wbasedato."_000008  "
		."    WHERE Conind = '".$tipo."' "
		."       AND Conins = 'on' "
		."       AND Conest = 'on' ";
	}

	$res = mysql_query($q,$conex);
	$row = mysql_fetch_array($res);

	$resultado=$row[0].'-'.($row[1]+1);
	return $resultado;
}

/*
 * $parbus			Parametro de busqueda ingresado por el usuario para buscar el producto
 * $forbus			El tipo de Parametro con que se va a buscar el articulo (Rotulo, Codigo, Nombre Comercial o Nombre Génerico)
 *
 * VALORES RETORNADOS
 *
 * Devuelve Array de productos
 */

function consultarInsumos($parbus, $forbus)
{
	global $conex;
	global $wbasedato;

	switch ($forbus)
	{
		case 'rotulo':
			$q= " SELECT Tippro "
			."       FROM ".$wbasedato."_000002, ".$wbasedato."_000001 "
			."    WHERE Artcod = '".$parbus."' "
			."       AND Artest = 'on' "
			."       AND Tipest = 'on' "
			."       AND Tipcod = Arttip ";
			$res = mysql_query($q,$conex);
			$row = mysql_fetch_array($res);

			//$exp=explode('-', $parbus);
			//if (isset($exp[1]))
			if (isset($row[0]) and $row[0]=='on')
			{
				$q= " SELECT Artcod, Artcom, Artgen, Artuni, Unides, Tippro "
				."       FROM ".$wbasedato."_000002, ".$wbasedato."_000001, movhos_000027 "
				."    WHERE Artcod = '".$parbus."' "
				."       AND Artest = 'on' "
				."       AND Artuni= Unicod "
				."       AND Tipest = 'on' "
				."       AND Tipcdo = 'on' "
				."       AND Uniest='on' "
				."    Order by 1 ";
			}
			else
			{
				$q= " SELECT C.Artcod, C.Artcom, C.Artgen, C.Artuni, B.Unides, D.Tippro  "
				."       FROM movhos_000026 A, movhos_000027 B, ".$wbasedato."_000002 C, ".$wbasedato."_000001 D, ".$wbasedato."_000009 E"
				."    WHERE A. Artcod = '".$parbus."' "
				."       AND A. Artest = 'on' "
				."       AND A. Artcod = E.Apppre "
				."       AND E. Appest = 'on' "
				."       AND E. Appcod = C.Artcod "
				."       AND C. Artest = 'on' "
				."       AND C. Artuni= B.Unicod "
				."       AND B.Uniest='on' "
				."       AND D.Tipcod = C.Arttip "
				."       AND D.Tipest = 'on' "
				."    Order by 1 ";
			}
			break;

		case 'Codigo':
			$q= " SELECT Artcod, Artcom, Artgen, Artuni, Unides, Tippro "
			."       FROM ".$wbasedato."_000002, ".$wbasedato."_000001, movhos_000027 "
			."    WHERE Artcod like '%".$parbus."%' "
			."       AND Artest = 'on' "
			."       AND Artuni= Unicod "
			."       AND Tipest = 'on' "
			."       AND Tipcod = Arttip "
			."       AND Uniest='on' "
			."    Order by 1 ";

			break;
		case 'Nombre comercial':

			$q= " SELECT Artcod, Artcom, Artgen, Artuni, Unides, Tippro "
			."       FROM ".$wbasedato."_000002,  ".$wbasedato."_000001, movhos_000027 "
			."    WHERE Artcom like '%".$parbus."%' "
			."       AND Artest = 'on' "
			."       AND Tipest = 'on' "
			."       AND Tipcod = Arttip "
			."       AND Artuni= Unicod "
			."       AND Uniest='on' "
			."    Order by 1 ";

			break;
		case 'Nombre genérico':

			$q= " SELECT Artcod, Artcom, Artgen, Artuni, Unides, Tippro "
			."       FROM ".$wbasedato."_000002,  ".$wbasedato."_000001, movhos_000027 "
			."    WHERE Artgen like '%".$parbus."%' "
			."       AND Artest = 'on' "
			."       AND Tipest = 'on' "
			."       AND Tipcod = Arttip "
			."       AND Artuni= Unicod "
			."       AND Uniest='on' "
			."    Order by 1 ";

			break;
	}

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($res);

			$productos[$i]['cod']=$row[0];
			$productos[$i]['nom']=str_replace('-',' ',$row[1]);
			$productos[$i]['gen']=str_replace('-',' ',$row[2]);
			$productos[$i]['est']='on';

			if ($row[5]=='on')
			{
				$productos[$i]['lot']='on';
				$productos[$i]['pre']=$row[3].'-'.$row[4];
			}
			else
			{
				$productos[$i]['lot']='off';
				$productos[$i]['pre']='';
			}

		}

	}
	else
	{
		$productos=false;
	}
	return $productos;
}


function consultarConversor($codigo, $cco)
{
	global $conex;
	global $wbasedato;


	//consulto los conceptos
	$q =  " SELECT Appcnv "
	."        FROM  ".$wbasedato."_000009 "
	."      WHERE Apppre=mid('".$codigo."',1,instr('".$codigo."','-')-1) "
	."            and Appcco=mid('".$cco."',1,instr('".$cco."','-')-1) "
	."            and Appest='on' ";


	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	if ($num1>0)
	{
		$row1 = mysql_fetch_array($res1);
		return $row1[0];
	}
	else
	{
		return false;
	}

}

/*
 * Consulta los lotes creados de un prodcuto 8 dias antes de la fecha actual
 *
 * $parbus				Parametro de busqueda por el codigo del producto
 * $cco					Centro de Costos
 *
 */
function consultarLotes($parbus, $cco, $lote, &$cantidad)
{
	global $conex;
	global $wbasedato;

	if ($lote!='') //cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
	{
		$consultas[0]=$lote;
		$cadena="Plocod != '".$lote."' AND";
		$inicio=1;

		$q= " SELECT Plosal "
		."       FROM ".$wbasedato."_000004 "
		."    WHERE  Plocod = '".$lote."' "
		."       AND Plopro = '".$parbus."' "
		."       AND Plocco = mid('".$cco."',1,instr('".$cco."','-')-1) "
		."       AND Ploest = 'on' "
		."    Order by 1 desc  ";
		
		$q= " SELECT Plosal "
		."       FROM ".$wbasedato."_000004 "
		."    WHERE  Plocod = '".$lote."' "
		."       AND Plopro = '".$parbus."' "
		."       AND Plocco = mid('".$cco."',1,instr('".$cco."','-')-1) "
		."       AND Ploest = 'on' "
		."    Order by 1 desc  ";
		
		$res = mysql_query($q,$conex);
		$row = mysql_fetch_array($res);
		$cantidad=$row['Plosal'];
	}
	else
	{
		$cadena='';
		$inicio=0;
	}

	//Calcula la fecha 8 dias antes
	$dias=date('d')-8;
	if ($dias>0)
	{
		$fecha=date('Y').'-'.date('m').'-'.$dias;
	}
	else
	{
		$dias=31+$dias;
		$mes=date('m')-1;
		if($mes>0)
		{
			$fecha = mktime(0, 0, 0, $mes, $dias,   date("Y"));
		}
		else
		{
			$mes=12;
			$fecha = mktime(0, 0, 0, $mes, $dias,   date("Y")-1);
		}
		$fecha=date('Y-m-d', $fecha);

	}

	$q= " SELECT Plocod, Plopro, Plocco, Plofcr, Plofve, Plohve, Plocin, Plosal, Ploela, Plocco, Ploest "
	."       FROM ".$wbasedato."_000004 "
	."    WHERE ".$cadena." "
	."       Plopro = '".$parbus."' "
	."       AND Plocco = mid('".$cco."',1,instr('".$cco."','-')-1) "
	."       AND Ploest = 'on' "
//	."       AND fecha_data > '".$fecha."' "
	."    Order by 1 desc";

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($res);
			$consultas[$inicio]=$row['Plocod'];
			if ($inicio==0)
			{
				$cantidad=$row['Plosal'];
			}
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


function consultarTraslado($concepto, $consecutivo, $fecha, &$ccoOri, &$ccoDes, &$estado, &$inslis)
{
	global $conex;
	global $wbasedato;

	$q= "SELECT Mdeart, Mdecan, Mdepre, Mdenlo, Mencco, Menccd, Menest, Artcom, Artgen, Artuni, Unides "
	."     FROM   ".$wbasedato."_000007 A, ".$wbasedato."_000006, ".$wbasedato."_000002, movhos_000027 "
	."   WHERE Mdecon = '".$concepto."' "
	."     AND Mdedoc = '".$consecutivo."' "
	."     AND Mdecon = Mencon "
	."     AND Mdedoc = Mendoc "
	."     AND Mdeart = Artcod "
	."     AND Artest = 'on' "
	."     AND Unicod = Artuni "
	."     AND Uniest = 'on' ";


	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($res);
			if ($i==0)
			{
				$q= "SELECT Conind  "
				."     FROM    ".$wbasedato."_000008 "
				."   WHERE Concod = '".$concepto."' "
				."     AND Conest = 'on' ";

				$res1 = mysql_query($q,$conex);
				$row1 = mysql_fetch_array($res1);
				$ind=$row1[0];

				$q= "SELECT Cconom  "
				."     FROM   movhos_000011 "
				."   WHERE Ccocod = '".$row['Mencco']."' "
				."     AND Ccoest = 'on' ";
				$res1 = mysql_query($q,$conex);
				$row1 = mysql_fetch_array($res1);

				$ccoOri=$row['Mencco'].'-'.$row1['Cconom'];

				if ($ind=='-1')
				{
					$cco=$row['Mencco'].'-'.$row1['Cconom'];
				}

				$q= "SELECT Cconom  "
				."     FROM   movhos_000011 "
				."   WHERE Ccocod = '".$row['Menccd']."' "
				."     AND Ccoest = 'on' ";
				$res1 = mysql_query($q,$conex);
				$row1 = mysql_fetch_array($res1);

				$ccoDes=$row['Menccd'].'-'.$row1['Cconom'];

				if ($ind=='1')
				{
					$cco=$row['Menccd'].'-'.$row1['Cconom'];
				}


				if ($row['Menest']=='on')
				{
					if ($ind==-1)
					{
						$estado='Creado';
					}
					else
					{
						$estado='Devuelto';
					}

				}
				else
				{
					$estado='Desactivado';
				}
			}

			$inslis[$i]['cod']=$row['Mdeart'];
			$inslis[$i]['nom']=str_replace('-',' ',$row['Artcom']);
			$inslis[$i]['gen']=str_replace('-',' ',$row['Artgen']);
			$inslis[$i]['pre']=$row['Artuni'].'-'.$row['Unides'];
			$inslis[$i]['can']=$row['Mdecan'];

			if ($row['Mdenlo']!='')
			{

				$inslis[$i]['lot']='on';
				$inslis[$i]['est']='on';
				$inslis[$i]['nlo']=$row['Mdenlo'];
				$inslis[$i]['prese']='';
			}
			else
			{
				$q =  " SELECT Appcnv, Artcom, Artgen "
				."        FROM  ".$wbasedato."_000009, movhos_000026 "
				."      WHERE Appcod='".$row['Mdeart']."' "
				."            and Appcco=mid('".$cco."',1,instr('".$cco."','-')-1) "
				."            and Appest='on' "
				."            and Apppre='".$row['Mdepre']."' "
				."            and Apppre=Artcod ";

				$res1 = mysql_query($q,$conex);
				$num1 = mysql_num_rows($res1);
				$row1 = mysql_fetch_array($res1);

				$inslis[$i]['lot']='off';
				$inslis[$i]['est']='on';
				$inslis[$i]['nlo']='';
				$inslis[$i]['prese']=$row['Mdepre'].'-'.str_replace('-',' ',$row1['Artcom']).'-'.str_replace('-',' ',$row1['Artgen']);
			}

		}
	}
}

/**************************************************************************************************************
 * 
 * Valida que la cantidad de insumos que se encuentran en la lista tenga la cantidad correcta para cargar o
 * la cantidad a devolver este entre el rango de cantidad cargada y la cantidad inicial con la que se creo el
 * lote.
 *
 * $inslist			Lista de productos con su respectivo lote, ya sea para cargar o devolver por el usuario
 * $cco				Centro de Costos
 * 
 * Valores Retornados
 * 
 * Para el caso de Cargar
 * 
 * <0				Indica que no se encuentra existencias en el maestro de articulos 
 * 0				Indica que no hay existencias para cargar
 * 1				Indica que la cantidad a cargar es mayor de la disponible
 * 2				Indica que no hay problemas
 * 
 * Para el caso de Devolver
 * 
 * 0				Indica que debe ingresar un valor a la cantidad a devolver
 * 1				Indica que la cantidad a devolver es mayor que la cantidad inicial
 * 2				Indica que no hay problemas
 * 
 *************************************************************************************************************/
function validarComposicionMatrix(&$inslis, $cco)
{
	global $conex;
	global $wbasedato;
	
	global $crear;

	$val=2;
	
	if($crear != 'cargar' && !empty($inslis) )
	{
		for ($i=0; $i<count($inslis); $i++){

			$q= " SELECT Plosal, Plocin "
			."       FROM ".$wbasedato."_000004 "
			."    WHERE Plopro = '".$inslis[$i]['cod']."' "
			."       AND Plocod='".$inslis[$i]['nlo']."' "
			."       AND Ploest='on' "
			."       AND Plocco= mid('".$cco."',1,instr('".$cco."','-')-1) ";

			$result = mysql_query( $q, $conex );

			while($rows = mysql_fetch_array( $result ) ){
				if( ($rows['Plocin'] - $rows['Plosal']) <= 0 ){
					$inslis[$i]['can'] = $rows['Plosal'];
					$inslis[$i]['est'] = 'off';
					$val = 3;
				}
				else if( $inslis[$i]['can'] > ($rows['Plocin'] - $rows['Plosal']) ){
					$inslis[$i]['can'] = $rows['Plocin'] - $rows['Plosal'];
					$inslis[$i]['est'] = 'off';
					$val = 1;
				}
				else{
					if( $inslis[$i]['can'] <= 0 ){
						$inslis[$i]['can'] = $rows['Plocin'] - $rows['Plosal'];
						$inslis[$i]['est'] = 'off';
						
						$val = 0;
					}
				}
			}
		}
		return $val;
	}
	

	for ($i=0; $i<count($inslis); $i++)
	{
		$inslis[$i]['est']='on';

		$q = " SELECT * FROM ".$wbasedato."_000002 where Artcod='".$inslis[$i]['cod']."' and Artest='on' ";
		$res1 = mysql_query($q,$conex);
		$num3 = mysql_num_rows($res1);
		if ($num3>0)
		{
			//Convierte la cantidad a las unidades correspondientes
			if ($inslis[$i]['lot']!='on')
			{
				$q =  " SELECT Appcnv "
				."        FROM  ".$wbasedato."_000009 "
				."      WHERE Apppre=mid('".$inslis[$i]['prese']."',1,instr('".$inslis[$i]['prese']."','-')-1) "
				."            and Appcod='".$inslis[$i]['cod']."' "
				."            and Appcco=mid('".$cco."',1,instr('".$cco."','-')-1) "
				."            and Appest='on' ";

				$res1 = mysql_query($q,$conex);
				$num1 = mysql_num_rows($res1);

				$row1 = mysql_fetch_array($res1);
				$cantidad=$inslis[$i]['can']*$row1[0];
				$mul=$row1[0];
			}
			else
			{
				$cantidad=$inslis[$i]['can'];
			}
			
			//Se busca la cantidad disponible del lote
			$q = " SELECT karexi FROM ".$wbasedato."_000005 where karcod='".$inslis[$i]['cod']."' and Karcco= mid('".$cco."',1,instr('".$cco."','-')-1) ";
			$res1 = mysql_query($q,$conex);
			$num1 = mysql_num_rows($res1);
			if ($num1>0)
			{
				$row1 = mysql_fetch_array($res1);
				if ($row1[0]>=$cantidad)
				{
					if ($inslis[$i]['nlo']!='')
					{
						$q= " SELECT Plosal, Plocin "
						."       FROM ".$wbasedato."_000004 "
						."    WHERE Plopro = '".$inslis[$i]['cod']."' "
						."       AND Plocod='".$inslis[$i]['nlo']."' "
						."       AND Ploest='on' "
						."       AND Plocco= mid('".$cco."',1,instr('".$cco."','-')-1) ";
					}
					else
					{
						$q =  " SELECT Appexi "
						."        FROM  ".$wbasedato."_000009 "
						."      WHERE Appcod='".$inslis[$i]['cod']."' "
						."            and Appcco=mid('".$cco."',1,instr('".$cco."','-')-1) "
						."            and Appest='on' "
						."            and Apppre=mid('".$inslis[$i]['prese']."',1,instr('".$inslis[$i]['prese']."','-')-1) ";
					}

					$res2 = mysql_query($q,$conex);
					$row2 = mysql_fetch_array($res2);
					if ( $row2[0]>=$cantidad )
					{
							$inslis[$i]['est']='on';
					}
					else
					{
						$inslis[$i]['est']='off';
						$inslis[$i]['can']=$row2[0];
						if ($inslis[$i]['nlo']=='')
						{
							$inslis[$i]['can']=floor($inslis[$i]['can']/$mul);
						}
//						if ( $inslis[$i]['nlo']!='' && $row2[1] < $cantidad && $crear != 'cargar' ){
//							$val=3;
//							return $val;
//						}
						if ($val>0)
						{
							$val=1;
						}
					}
				}
				else
				{
					$inslis[$i]['est']='off';

					$inslis[$i]['can']=$row1[0];
					if ($inslis[$i]['nlo']=='')
					{
						$q =  " SELECT Appexi "
						."        FROM  ".$wbasedato."_000009 "
						."      WHERE Appcod='".$inslis[$i]['cod']."' "
						."            and Appcco=mid('".$cco."',1,instr('".$cco."','-')-1) "
						."            and Appest='on' "
						."            and Apppre=mid('".$inslis[$i]['prese']."',1,instr('".$inslis[$i]['prese']."','-')-1) ";
						$res2 = mysql_query($q,$conex);
						$row2 = mysql_fetch_array($res2);
						$inslis[$i]['can']=floor($row2[0]/$mul);
					}
					if ($val>0)
					{
						$val=1;
					}
				}
			}
			else
			{
				$inslis[$i]['est']='off';
				if ($val>=0)
				{
						$val=0;
				}					 
			}
		}
		else
		{
			$inslis[$i]['est']='off';
			$val=-1;
		}

	}

	return $val;
}

/*
 *
 */

function validarMaterial($inslis, $cco)
{
	global $conex;
	global $wbasedato;

	$val=true;

	for ($i=0; $i<count($inslis); $i++)
	{
		$q = " SELECT Tipmmq, Tipmat, Tipvdi FROM ".$wbasedato."_000002, ".$wbasedato."_000001 where Artcod='".$inslis[$i]['cod']."' and Artest='on' and Arttip=Tipcod and tipest='on' ";

		$res1 = mysql_query($q,$conex);
		$num3 = mysql_num_rows($res1);
		if ($num3>0)
		{
			$row1 = mysql_fetch_array($res1);
			if ($row1[0]!='on' and $row1[1]!='on' and $row1[2]!='on')
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	return $val;
}

function validarMatrix($insumo, $cco, $otro)
{
	global $conex;
	global $wbasedato;

	$q = " SELECT * FROM ".$wbasedato."_000002 where Artcod='".$insumo['cod']."' and Artest='on' ";
	$res1 = mysql_query($q,$conex);
	$num3 = mysql_num_rows($res1);
	if ($num3>0)
	{
		$q = " SELECT karexi FROM ".$wbasedato."_000005 where karcod='".$insumo['cod']."' and Karcco= mid('".$cco."',1,instr('".$cco."','-')-1) ";
		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);
		if ($num1>0)
		{
			$row1 = mysql_fetch_array($res1);
			if ($row1[0]>0)
			{
				if ($insumo['lot']!='')
				{
					$q= " SELECT Plosal, Plofve, plohve "
					."       FROM ".$wbasedato."_000004 "
					."    WHERE Plopro = '".$insumo['cod']."' "
					."       AND Plocod='".$otro."' "
					."       AND Ploest='on' "
					."       AND Plocco= mid('".$cco."',1,instr('".$cco."','-')-1) ";
				}
				else
				{
					$q =  " SELECT Appexi "
					."        FROM  ".$wbasedato."_000009 "
					."      WHERE Appcod='".$insumo['cod']."' "
					."            and Appcco=mid('".$cco."',1,instr('".$cco."','-')-1) "
					."            and Appest='on' "
					."            and Apppre=mid('".$otro."',1,instr('".$otro."','-')-1) ";
				}

				$res2 = mysql_query($q,$conex);
				$num2 = mysql_num_rows($res1);
				if ($num2>0)
				{
					$row2 = mysql_fetch_array($res2);
					if ($row2[0]<0)
					{
						$val=1;
					}
					else if ($insumo['lot']=='on' and ($row2[1]<date('Y-m-d') or ($row2[1]==date('Y-m-d') and $row2[2]<date("H:i:s"))))
					{
						$val=-2;
					}
					else
					{
						$val=2;
					}
				}
				else
				{
					$val=-3;
				}
			}
			else
			{
				$val=0;
			}
		}
		else
		{
			$val=0;
		}
	}
	else
	{
		$val=-1;
	}

	return $val;
}

/**************************************************************************************************
 * Actualiza el consecutivo en la tabla de MAESTRO POR CONCEPTO por concepto e inserta el movimiento
 * de salida en la base de datos
 *
 * Parametros
 *
 * &$codigo					Da el codigo del Concepto que se actualiza
 * &$consecutivo			Da el consecutivo por concepto despues de actualizar la tabla
 * $cco						Centro de costos
 * $usuario					Usuario quien crea la salida
 * $cco2					Centro de costos de destino
 * $movimiento
 *
 * Parametros devueltos
 *
 * Ninguno
 **************************************************************************************************/
function grabarEncabezadoSalidaMatrix(&$codigo, &$consecutivo, $cco, $usuario, $cco2, $movimiento)
{
	global $conex;
	global $wbasedato;

	$q = "lock table ".$wbasedato."_000008 LOW_PRIORITY WRITE";
	//$errlock = mysql_query($q,$conex);

	if ($movimiento==0)
	{
		$q= "   UPDATE ".$wbasedato."_000008 "
		."      SET Concon = (Concon + 1) "
		."    WHERE Conind = '-1' "
		."      AND Conven = 'on' "
		."      AND Conest = 'on' ";

		$res1 = mysql_query($q,$conex);

		$q= "   SELECT Concon, Concod from ".$wbasedato."_000008 "
		."    WHERE Conind = '-1'"
		."      AND Conven = 'on' "
		."      AND Conest = 'on' ";
	}
	else
	{
		$q= "   UPDATE ".$wbasedato."_000008 "
		."      SET Concon = (Concon + 1) "
		."    WHERE Conind = '-1' "
		."      AND Conins = 'on' "
		."      AND Conest = 'on' ";

		$res1 = mysql_query($q,$conex);

		$q= "   SELECT Concon, Concod from ".$wbasedato."_000008 "
		."    WHERE Conind = '-1'"
		."      AND Conins = 'on' "
		."      AND Conest = 'on' ";
	}

	$res1 = mysql_query($q,$conex);
	$row2 = mysql_fetch_array($res1);
	$codigo=$row2[1];
	$consecutivo=$row2[0];

	$q = " UNLOCK TABLES";   //SE DESBLOQUEA LA TABLA DE FUENTES
	$errunlock = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());


	$q= " INSERT INTO ".$wbasedato."_000006 (   Medico       ,   Fecha_data        ,                 Hora_data  ,   Menano       , Menmes         ,     Mendoc   ,   Mencon       ,    Menfec          ,           Mencco                          ,             Menccd                          ,  Mendan ,  Menusu  ,    Menfac          ,  Menest , Seguridad       ) "
	."                               VALUES ('".$wbasedato."',  '".date('Y-m-d')."', '".(string)date("H:i:s")."', '".date('Y')."', '".date('m')."','".$row2[0]."', '".$row2[1]."' , '".date('Y-m-d')."', mid('".$cco."',1,instr('".$cco."','-')-1) , mid('".$cco2."',1,instr('".$cco2."','-')-1) ,   ''    , $usuario ,      ''            , 'on'    , 'C-".$usuario."') ";

	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO GRABAR EL ENCABEZADO DEL MOVIIENTO DE SALIDA DE INSUMOS111 ".mysql_error());
}

/**************************************************************************************************
 *
 * Graba la Salida del Articulo en la base de datos
 * $inscod:			Codigo del insumo
 * $inscan:			Cantidad del insumo
 * $codigo:			Código del Concepto
 * $consecutivo:	Consecutivo por Concepto, (sen encuentra en la tabla 8, campo concon)
 * $usuario: 		Usuario quien genera la salida
 * $lote:			Lote
 * $prese:			Presentacion
 *
 **************************************************************************************************/

function grabarDetalleSalidaMatrix($inscod, $inscan, $codigo, $consecutivo, $usuario, $lote, $prese)
{
	global $conex;
	global $wbasedato;

	if ($lote!='')
	{
		$q= " INSERT INTO ".$wbasedato."_000007 (   Medico       ,   Fecha_data        ,         Hora_data          ,    Mdecon    ,       Mdedoc      ,     Mdeart  ,    Mdecan     ,     Mdenlo             , Mdepre,  Mdeest,  Seguridad    ) "
		."                               VALUES ('".$wbasedato."',  '".date('Y-m-d')."', '".(string)date("H:i:s")."', '".$codigo."', '".$consecutivo."','".$inscod."', '".$inscan."' , '".$lote."-".$inscod."',   ''  ,  'on'  , 'C-".$usuario."') ";
	}
	else
	{
		$q= " INSERT INTO ".$wbasedato."_000007 (   Medico       ,   Fecha_data,                  Hora_data,              Mdecon,              Mdedoc ,     Mdeart   ,     Mdecan    , Mdenlo,     Mdepre    , Mdeest,  Seguridad      ) "
		."                               VALUES ('".$wbasedato."',  '".date('Y-m-d')."', '".(string)date("H:i:s")."', '".$codigo."', '".$consecutivo."','".$inscod."', '".$inscan."' ,  ''   ,  '".$prese."' , 'on'  , 'C-".$usuario."') ";
	}
	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO GRABAR EL DETALLE DE SALIDA DE UN ARTICULO ".mysql_error());

}

/*****************************************************************************
 * $prese:		Presentacion
 * $inscod:		Codigo del insumo
 * $inscan:		Cantidad del insumo
 * $lote:		No. de Lote
 ****************************************************************************/
function descontarInsumoMatrix($inscod, $inscan, $cco, $lote, $prese)
{
	global $conex;
	global $wbasedato;

	global $conex;
	global $wbasedato;

	$q= "   UPDATE ".$wbasedato."_000005 "
	."      SET karexi = karexi - ".$inscan." "
	."    WHERE Karcod = '".$inscod."' "
	."      AND karcco = mid('".$cco."',1,instr('".$cco."','-')-1) ";


	$res1 = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO DESCONTAR UN INSUMO ".mysql_error());

	if ($lote!='')
	{
		$q= "   UPDATE ".$wbasedato."_000004 "
		."      SET Plosal = Plosal-".$inscan.""
		."    WHERE Plocod =  '".$lote."' "
		."      AND Plopro ='".$inscod."' "
		."      AND Ploest ='on' "
		."      AND Plocco = mid('".$cco."',1,instr('".$cco."','-')-1) ";
	}

	if ($prese!='')
	{
		$q= "   UPDATE ".$wbasedato."_000009 "
		."      SET Appexi = Appexi-".$inscan.""
		."    WHERE Apppre =  mid('".$prese."',1,instr('".$prese."','-')-1) "
		."      AND Appcod ='".$inscod."' "
		."      AND Appest ='on' "
		."      AND Appcco = mid('".$cco."',1,instr('".$cco."','-')-1) ";
	}

	$res1 = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO DESCONTAR UN INSUMO ".mysql_error());
}

function sumarInsumoMatrix($inscod, $inscan, $cco, $lote, $prese)
{
	global $conex;
	global $wbasedato;

	$q= "   UPDATE ".$wbasedato."_000005 "
	."      SET karexi = karexi + ".$inscan." "
	."    WHERE Karcod = '".$prese."' "
	."      AND karcco = mid('".$cco."',1,instr('".$cco."','-')-1) ";


	$res1 = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO SUMAR UN INSUMO ".mysql_error());

	if ($prese!='')
	{
		$q= "   UPDATE ".$wbasedato."_000009 "
		."      SET Appexi = Appexi+".$inscan.""
		."    WHERE Apppre = mid('".$inscod."',1,instr('".$inscod."','-')-1) "
		."      AND Appcod ='".$prese."' "
		."      AND Appest ='on' "
		."      AND Appcco = mid('".$cco."',1,instr('".$cco."','-')-1) ";
	}

	if ($lote!='')
	{
		$q= "   UPDATE ".$wbasedato."_000004 "
		."      SET Plosal = Plosal+".$inscan.""
		."    WHERE Plocod =  '".$lote."' "
		."      AND Plopro ='".$inscod."' "
		."      AND Ploest ='on' "
		."      AND Plocco = mid('".$cco."',1,instr('".$cco."','-')-1) ";
	}

	$res1 = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO SUMAR UN INSUMO ".mysql_error());
}

function grabarEncabezadoEntradaMatrix(&$codigo, &$consecutivo, $cco, $cco2, $usuario, $movimiento)
{
	global $conex;
	global $wbasedato;

	$q = "lock table ".$wbasedato."_000008 LOW_PRIORITY WRITE";
	$errlock = mysql_query($q,$conex);

	if ($movimiento==0)
	{
		$q= "   UPDATE ".$wbasedato."_000008 "
		."      SET Concon = (Concon + 1) "
		."    WHERE Conind = '1'"
		."      AND Conven = 'on' "
		."      AND Conest = 'on' ";

		$res1 = mysql_query($q,$conex);

		$q= "   SELECT Concon, Concod from ".$wbasedato."_000008 "
		."    WHERE Conind = '1'"
		."      AND Conven = 'on' "
		."      AND Conest = 'on' ";
	}

	else
	{
		$q= "   UPDATE ".$wbasedato."_000008 "
		."      SET Concon = (Concon + 1) "
		."    WHERE Conind = '1'"
		."      AND Conins = 'on' "
		."      AND Conest = 'on' ";

		$res1 = mysql_query($q,$conex);

		$q= "   SELECT Concon, Concod from ".$wbasedato."_000008 "
		."    WHERE Conind = '1'"
		."      AND Conins = 'on' "
		."      AND Conest = 'on' ";
	}

	$res1 = mysql_query($q,$conex);
	$row2 = mysql_fetch_array($res1);
	$codigo=$row2[1];
	$consecutivo=$row2[0];

	$q = " UNLOCK TABLES";   //SE DESBLOQUEA LA TABLA DE FUENTES
	$errunlock = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());


	$q= " INSERT INTO ".$wbasedato."_000006 (   Medico       ,   Fecha_data,                  Hora_data,              Menano,              Menmes ,     Mendoc   ,   Mencon  ,             Menfec,           Mencco ,   Menccd    ,  Mendan,  Menusu,    Menfac,  Menest, Seguridad) "
	."                               VALUES ('".$wbasedato."',  '".date('Y-m-d')."', '".(string)date("H:i:s")."', '".date('Y')."', '".date('m')."','".$row2[0]."', '".$row2[1]."' , '".date('Y-m-d')."', mid('".$cco2."',1,instr('".$cco2."','-')-1) , mid('".$cco."',1,instr('".$cco."','-')-1) ,       '', $usuario,      '' , 'on', 'C-".$usuario."') ";


	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO GRABAR EL ENCABEZADO DEL MOVIIENTO DE SALIDA DE INSUMOS ".mysql_error());
}

function grabarDetalleEntradaMatrix($codpro, $inscan, $codigo, $consecutivo, $usuario, $lote, $prese)
{
	global $conex;
	global $wbasedato;

	if ($prese=='')
	{
		$prese=$codpro;
	}

	if ($lote!='')
	{
		$q= " INSERT INTO ".$wbasedato."_000007 (   Medico       ,     Fecha_data      ,      Hora_data             ,    Mdecon    ,      Mdedoc       ,   Mdeart   ,    Mdecan     ,     Mdenlo              , Mdepre         ,  Mdeest,  Seguridad) "
		."                               VALUES ('".$wbasedato."',  '".date('Y-m-d')."', '".(string)date("H:i:s")."', '".$codigo."', '".$consecutivo."','".$prese."', '".$inscan."' ,  '".$lote."-".$codpro."',   '".$codpro."', 'on'   , 'C-".$usuario."') ";
	}
	else
	{
		$q= " INSERT INTO ".$wbasedato."_000007 (   Medico       ,   Fecha_data        ,       Hora_data            ,   Mdecon     ,     Mdedoc        ,   Mdeart   ,    Mdecan     ,   Mdenlo,   Mdepre       , Mdeest,  Seguridad) "
		."                               VALUES ('".$wbasedato."',  '".date('Y-m-d')."', '".(string)date("H:i:s")."', '".$codigo."', '".$consecutivo."','".$prese."', '".$inscan."' ,     ''  ,  '".$codpro."' , 'on'  , 'C-".$usuario."') ";
	}

	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO GRABAR EL DETALLE DE SALIDA DE UN ARTICULO ".mysql_error());

}


//----------------------------------------------------------funciones del modelo---------------------------
function eliminarInsumo($lista, $index)
{
	if (count($lista)>1)
	{
		unset($lista[$index]);
		$lista = array_reverse(array_reverse($lista));
	}
	else
	{
		$lista =false;
	}
	return $lista;
}
//----------------------------------------------------------funciones de presentacion------------------------------------------------

/**
 * Escribe el titulo de la aplicacion, fecha y hora adicionalmente da el acceso a los scripts consulta.php, seguimiento.php
 * para consulta.php existen dos opciones mandandole el paramentro para=recibidos o para=enviados, asi ese Script consultara
 * uno u otro tipo de requerimiento
 *
 * Adicionalmente esta funcione se encarga de abrir la forma del Script que se llama informatica
 *
 * No necesita ningun parametro ni devuelve
 */
function pintarTitulo($pintar)
{

	if ($pintar==0)
	{
		echo "<table ALIGN=CENTER width='50%'>";
		//echo "<tr><td align=center colspan=1 ><img src='/matrix/images/medical/general/logo_promo.gif' height='100' width='250' ></td></tr>";
		echo "<tr><td class='titulo1'>PRODUCCION CENTRAL DE MEZCLAS</td></tr>";
		echo "<tr><td class='titulo2'>Fecha: ".date('Y-m-d')."&nbsp Hora: ".(string)date("H:i:s")."</td></tr></table></br>";

		echo "<table ALIGN=CENTER width='90%' >";
		//echo "<tr><td align=center colspan=1 ><img src='/matrix/images/medical/general/logo_promo.gif' height='100' width='250' ></td></tr>";
		echo "<tr><td class='texto5' width='15%'><a style='text-decoration:none;color:black' href='cen_Mez.php?wbasedato=cen_mez'>PRODUCTOS</td></a>";
		echo "<td class='texto5' width='15%'><a style='text-decoration:none;color:black' href='lotes.php?wbasedato=lotes.php'>LOTES</a></td>";
		echo "<td class='texto5' width='15%'><a style='text-decoration:none;color:black' href='cargos.php?wbasedato=lotes.php&tipo=C'>CARGOS A PACIENTES</a></td>";
		echo "<td class='texto6' width='15%'><a style='text-decoration:none;color:white' href='pos.php?wbasedato=pos.php&tipo=A'>VENTA EXTERNA</a></td></TR>";
		//echo "<a href='cargos.php?wbasedato=lotes.php&tipo=A'><td class='texto5' width='15%'>AVERIAS</td></a>";
		//echo "<a href='descarte.php?wbasedato=cenmez'><td class='texto5' width='15%'>DESCARTES</td></TR></a>";
		echo "<tr><td class='texto6' >&nbsp;</td>";
		echo "<td class='texto6' >&nbsp;</td>";
		echo "<td class='texto6' >&nbsp;</td>";
		echo "<td class='texto6' >&nbsp;</td></tr></table>";
	}
	else
	{
		echo "<table ALIGN=CENTER width='50%'>";
		//echo "<tr><td align=center colspan=1 ><img src='/matrix/images/medical/general/logo_promo.gif' height='100' width='250' ></td></tr>";
		echo "<tr><td class='titulo1'>AJUSTE DE MATERIAL MEDICO QUIRURGICO CENTRAL DE MEZCLAS</td></tr>";
		echo "<tr><td class='titulo2'>Fecha: ".date('Y-m-d')."&nbsp Hora: ".(string)date("H:i:s")."</td></tr></table></br>";
	}
}


function pintarBusqueda($consultas, $forcon, $pintar)
{
	if( empty($consultas) )
	$consultas = array();
	echo "<table border=0 ALIGN=CENTER width=90%>";
	echo "<form name='producto2' action='pos.php' method=post>";
	echo "<tr><td class='titulo3' colspan='3' align='center'>Consulta: ";
	echo "<select name='forcon' class='texto5' onchange='enter7()'>";
	echo "<option>".$forcon."</option>";
	if ($forcon!='Numero de movimiento')
	echo "<option value='Numero de movimiento'>Numero de movimiento</option>";
	if ($forcon!='Articulo')
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
			echo "</tr><tr><td class='titulo3' colspan='3' align='center'> Consulta de ".$forcon.": ";
			echo "<select name='insfor' class='texto5'>";
			echo "<option>Codigo</option>";
			echo "<option>Nombre comercial</option>";
			echo "<option>Nombre genérico</option>";
			echo "</select>";
			echo "<input type='TEXT' name='parcon' value='' size=10 class='texto5'>&nbsp; ";
			echo "&nbsp; Fecha inicial:<input type='TEXT' name='parcon2' value='' size=10 class='texto5'>&nbsp;Fecha final:<input type='TEXT' name='parcon3' value='' size=10 class='texto5'>&nbsp;<INPUT TYPE='button' NAME='buscar' VALUE='Buscar' onclick='enter7()' class='texto5'> ";
			echo "</tr><tr><td class='titulo3' colspan='3' align='center'>";
			break;

		default:
			echo "</tr><tr><td class='titulo3' colspan='3' align='center'> Consulta de ".$forcon.": ";
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
	if ($consultas[0]['cod']!='')
	{
		for ($i=0;$i<count($consultas);$i++)
		{
			echo "<option>".$consultas[$i]."</option>";
		}
	}
	else
	{
		echo "<option value=''></option>";
	}
	echo "</select>";
	echo "</td></tr>";
	echo "<input type='hidden' name='pintar' value='".$pintar."'></td>";
	echo "</form>";
}


/*se le envia para pintar en html un vector de usuario ($usuario) con las siguientes caracteristicas:
 *  ['cod']=codigo del usuario
 *  ['cco']=primer centro de costos encontrado para el usuario (podrian haber mas, luego se muestran)
 *  ['ext']=extension del usuario
 *  ['ema']=email
 *  ['car']=cargo
 *  ['nom']=nombre
 *  ['sup']= en on quiere decir que debe permitir ingresar otros usuarios
 *
 * Adicionalmente se le manda la lista de usuarios ($personas) por si el usuario puede solicitar por otro
 */
function pintarFormulario($estado, $ccos, $numtra, $fecha, $pintar)
{
	echo "<form name='producto3' action='pos.php' method=post>";
	echo "<tr><td colspan=3 class='titulo3' align='center'><INPUT TYPE='submit' NAME='NUEVO' VALUE='Nuevo' class='texto5' ></td></tr>";
	echo "<input type='hidden' name='pintar' value='".$pintar."'></td>";
	echo "</table></form>";

	echo "<form name='producto' action='pos.php' method=post>";
	echo "<table border=0 ALIGN=CENTER width=90%>";

	echo "<tr><td colspan=3 class='titulo3' align='center'><b>Informacion general del traslado</b></td></tr>";

	echo "<tr><td class='texto1' colspan='3' align='center'>Centro de costos: ";
	echo "<select name='cco' class='texto5' onchange='enter8()'>";
	if ($ccos[0]!='')
	{
		for ($i=0;$i<count($ccos);$i++)
		{
			echo "<option >".$ccos[$i]."</option>";
		}
	}
	else
	{
		echo "<option value=''></option>";
	}
	echo "</select>";


	echo "</td></tr>";

	echo "<tr><td class='texto2' colspan='1' align='left'>Numero de Movimiento: <input type='TEXT' name='numtra' value='".$numtra."' readonly='readonly' class='texto2' size='10'></td>";
	echo "<td class='texto2' colspan='2' align='left'>Fecha: <input type='TEXT' name='fecha' value='".$fecha."' readonly='readonly' class='texto2' size='10'></td></tr>";
	switch($estado)
	{
		case 'inicio':
			echo "<tr>
					<td colspan=3 class='titulo3' align='center'>
						<input type='radio' name='crear' class='titulo3' value='cargar' onclick='enter1(this)'>Cargar &nbsp;&nbsp;&nbsp;
						<input type='radio' name='crear' class='titulo3' value='devolver' onclick='enter1(this)'>Devolver
						<input type='hidden' name='pwd'>
					</td></tr>";
			break;
		case 'creado':
			echo "<tr><td colspan=3 class='titulo3' align='center'>SE HA TRASLADADO EL PRODUCTO EXITOSAMENTE</td></tr>";
			break;
		case 'devuelto':
			echo "<tr><td colspan=3 class='titulo3' align='center'>EL PRODUCTO HA SIDO DEVUELTO</td></tr>";
			break;
		case 'Devuelto':
			echo "<tr><td colspan=3 class='titulo3' align='center'>DEVOLUCION ACTIVA</td></tr>";
			break;
		case 'Creado':
			echo "<tr><td colspan=3 class='titulo3' align='center'>TRASLADO ACTIVO</td></tr>";
			break;

	}

	echo "<input type='hidden' name='estado' value='".$estado."'></td>";
	echo "</table></br>";
}


function pintarInsumos($insumos, $inslis, $unidades, $lotes, $pintar, $cantidad)
{
	$aviso = "&nbsp";
	
	echo "<table border=0 ALIGN=CENTER width=90%>";
	echo "<tr><td colspan='4' class='titulo3' align='center'><b>Lista de articulos</b></td></tr>";

	echo "<tr><td class='texto1' colspan='4' align='center'>Buscar Articulo por: ";
	echo "<select name='forbus2' class='texto5'>";
	echo "<option>rotulo</option>";
	echo "<option>Codigo</option>";
	echo "<option>Nombre comercial</option>";
	echo "<option>Nombre generico</option>";
	echo "</select><input type='TEXT' name='parbus2' value='' size=10 class='texto5'>&nbsp;<INPUT TYPE='submit' NAME='buscar' VALUE='Buscar' class='texto5'></td> ";
	echo "<tr><td class='texto1' colspan='2' align='center'>Articulo: <select name='insumo' class='texto5' onchange='enter9()'>";
	if ($insumos!='')
	{
		for ($i=0;$i<count($insumos);$i++)
		{
			echo "<option value='".$insumos[$i]['cod']."-".$insumos[$i]['nom']."-".$insumos[$i]['gen']."-".$insumos[$i]['pre']."-".$insumos[$i]['lot']."-".$insumos[$i]['est']."-".$insumos[$i]['ven']."'>".$insumos[$i]['cod']."-".$insumos[$i]['nom']."</option>";
			
			if($insumos[$i]['ven'] == 'on'){
				$aviso = "LOTE VENCIDO";
			}
		}
	}
	else
	{
		echo "<option ></option>";
	}
	echo "</select></td>";
	if ($insumos[0]['lot']=='on')
	{
		echo "<td class='texto1' colspan='2' align='center'>Lote: <select name='lote' class='texto5' onchange='enter6()'>";
		if (is_array($lotes))
		{
			for ($i=0;$i<count($lotes);$i++)
			{
				echo "<option>".$lotes[$i]."</option>";
			}
		}
		else
		{
			echo "<option ></option>";
		}
		echo "</select></td></tr>";
		$ind=0;
	}
	if (is_array($unidades))
	{
		echo "<td class='texto1' colspan='1' align='center'>Presentacion: <select name='prese' class='texto5' onchange='enter1()'>";
		if ($unidades[0]!='')
		{
			for ($i=0;$i<count($unidades);$i++)
			{
				echo "<option>".$unidades[$i]."</option>";
			}
		}
		else
		{
			echo "<option ></option>";
		}
		echo "</select></td></tr>";
		$ind=0;
	}

	if (!isset($ind))
	{
		echo "<td class='texto1' colspan='2' align='center'>&nbsp;</td></tr>";
	}
	echo "<tr><td class='texto1' colspan='4' align='center'>Cantidad: <input type='TEXT' name='cantidad' value='".$cantidad."'  class='texto5' onchange='validarFormulario()'><input type='TEXT' name='nompro' value='".$insumos[0]['pre']."'  class='texto5' >";
	echo "</td>";
	echo "<tr><td colspan='4' class='texto1' align='center'><INPUT TYPE='button' NAME='buscar' VALUE='Agregar' onclick='enter()' class='texto5'></td></tr>";

	echo "<tr><td class='texto1' colspan=4 style='color:red;font-size:10pt'>$aviso</td></tr>";
	echo "<tr><td colspan='4' class='titulo3' align='center'>&nbsp</td></tr>";


	if ($inslis!='')
	{
		echo "<tr><td class='texto2' colspan='1' align='center'>Articulo</td>";
		echo "<td class='texto2' colspan='1' align='center'>Detalle</td>";
		echo "<td class='texto2' colspan='1' align='center'>Cantidad</td>";
		echo "<td class='texto2' colspan='1' align='center'>Eliminar</td></tr>";

		for ($i=0;$i<count($inslis);$i++)
		{
			if (is_int($i/2))
			{
				$class='texto3';
			}
			else
			{
				$class='texto4';
			}
			echo "<tr><td class='".$class."' colspan='1' align='center'>".$inslis[$i]['cod']."-".$inslis[$i]['nom']."<input type='hidden' name='inslis[".$i."][cod]' value='".$inslis[$i]['cod']."'><input type='hidden' name='inslis[".$i."][nom]' value='".$inslis[$i]['nom']."'><input type='hidden' name='inslis[".$i."][gen]' value='".$inslis[$i]['gen']."'></td>";
			echo "<input type='hidden' name='inslis[".$i."][pri]' value='checked'>";
			echo "<input type='hidden' name='inslis[".$i."][nlo]' value='".$inslis[$i]['nlo']."'>";
			echo "<input type='hidden' name='inslis[".$i."][prese]' value='".$inslis[$i]['prese']."'>";

			if ($inslis[$i]['nlo']!='')
			{
				echo "<td class='".$class."' colspan='1' align='center'>Lote: ".$inslis[$i]['nlo']."</td>";
			}
			else if ($inslis[$i]['prese']!='')
			{
				echo "<td class='".$class."' colspan='1' align='center'>Presentacion: ".$inslis[$i]['prese']."</td>";
			}
			else
			{
				echo "<td class='".$class."' colspan='1' align='center'>&nbsp;</td>";
			}

			if ($inslis[$i]['est']=='on')
			{
				echo "<td class='".$class."' colspan='1' align='center'><input type='TEXT' size='10' readonly='readonly' name='inslis[".$i."][can]' value='".$inslis[$i]['can']."'  class='texto3'><input type='TEXT' name='inslis[".$i."][pre]' value='".$inslis[$i]['pre']."'  class='texto3'></td>";

			}
			else
			{
				echo "<td bgcolor='red' colspan='1' align='center'><input type='TEXT' size='10' name='inslis[".$i."][can]' value='".$inslis[$i]['can']."'  class='texto3'><input type='TEXT' name='inslis[".$i."][pre]' value='".$inslis[$i]['pre']."'  class='texto3'></td>";
			}

			echo "<td class='".$class."' colspan='1' align='center'><input type='checkbox' name='eli' class='texto3' onclick='enter2(".$i.")'></td></tr>";

			echo "<input type='hidden' name='inslis[".$i."][lot]' value='".$inslis[$i]['lot']."'>";
			echo "<input type='hidden' name='inslis[".$i."][est]' value='".$inslis[$i]['est']."'>";
			
			echo "<input type='hidden' name='inslis[".$i."][ven]' value='".$inslis[$i]['ven']."'>";
		}
	}

	echo "<input type='hidden' name='accion' value='0'></td>";
	echo "<input type='hidden' name='realizar' value='0'></td>";
	echo "<input type='hidden' name='eliminar' value='0'></td>";
	echo "<input type='hidden' name='pintar' value='".$pintar."'></td>";
	
	echo "</table></br></form>";
	
	//Div para pedir password
	$s = "<DIV z-index=10 id='dvpass' style='display:none;position:absolute;top:100;position:absolute;left:700;background:silver;width:220;height:60;overflow:hidden'>";
	echo "
	<div id='dvpass' style='display:none;position:absolute;top:100;position:absolute;left:700;background:silver;width:220;height:60;z-index:3'>
		  <table align='center'>
		  	<tr align='center'>
		  		<td colspan='2' class='texto2'>Ingrese la clave</td>
		  	</tr>
		  	<tr align='center'>
		  		<td colspan='2'><INPUT type='password' id='pass2' class='texto5'></td>
		  	</tr>
		  	<tr align='center'>
		  		<td><INPUT type='button' onClick='javascript:divAceptar();' value='Aceptar' style='width:100' class='texto5'></td>
		  		<td><INPUT type='button' onClick='javascript:ocultarDiv();' value='Cancelar' style='width:100' class='texto5'></td>
		  	</tr>
		  </table>
	</div>
	";
	
	echo "<iframe id='ifrpass' style='display:none;position:absolute;top:100;position:absolute;left:700;width:220;height:60;z-index:2;border-width:0'></iframe>";
}

/*===========================================================================================================================================*/
/*=========================================================PROGRAMA==========================================================================*/
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
	$wbasedato='cenpro';
	

	or die("No se ralizo Conexion");
	



	//pintarVersion(); //Escribe en el programa el autor y la version del Script.

	if (!isset($pintar))
	{
		$pintar=0;  //Escribe el titulo de la aplicacion, fecha y hora adicionalmente da el acceso a otros scripts

	}
	pintarTitulo($pintar);
	$bd='movhos';
	//invoco la funcion connectOdbc del inlcude de ana, para saber si unix responde, en caso contrario,
	//este programa no debe usarse
	//include_once("pda/tablas.php");
	include_once("movhos/fxValidacionArticulo.php");
	include_once("movhos/registro_tablas.php");
	include_once("movhos/otros.php");
	include_once("CENPRO/funciones.php");


	//consulto los datos del usuario de la sesion
	$pos = strpos($user,"-");
	$wusuario = substr($user,$pos+1,strlen($user)); //extraigo el codigo del usuario

	if (isset($cco))
	{
		$ccos=consultarCentros($cco);
	}
	else
	{
		$ccos=consultarCentros('');
	}

	if (!isset($crear) or $crear!='devolver')
	{
		$numtra=consultarConsecutivo(-1, $pintar);
	}
	else
	{
		$numtra=consultarConsecutivo(1, $pintar);
	}


	if (!isset($estado))
	{
		$estado='inicio';
	}

	if (isset($crear) and $crear!='Enviar')
	{
		if (isset($numtra) and $numtra!='')
		{
			if ($pintar==1)
			{
				if ($crear=='cargar')
				{
					$val=validarMaterial($inslis, $cco);
				}
				else
				{
					$val=true;
				}
			}
			else
			{
				$val=true;
			}

			if ($val)
			{
				if (!isset($inslis))
				{
					pintarAlert1('Debe ingresar al menos un articulo para realizar el traslado');
				}
				else
				{
					if ($crear=='cargar')
					{
						$val=validarComposicionMatrix(&$inslis, $cco);
						if ($val>=0)
						{
							if ($val==0)
							{
								pintarAlert1('Sin existencia de los articulos señalados en rojo, en el kardex de inventario');
							}
							else
							{
								if ($val==1)
								{
									pintarAlert1('Los articulos indicados tienen menos existencias de las requeridas, se propone la cantidad maxima para el traslado');
								}
								else
								{
									grabarEncabezadoSalidaMatrix(&$codigo, &$consecutivo, $cco, $wusuario, '', $pintar);
									for ($i=0; $i<count($inslis); $i++)
									{
										if ($inslis[$i]['lot']!='on')
										{
											$cnv=consultarConversor($inslis[$i]['prese'], $cco);
											$cantidad=$inslis[$i]['can']*$cnv;
										}
										else
										{
											$cantidad=$inslis[$i]['can'];
										}
										grabarDetalleSalidaMatrix($inslis[$i]['cod'], $cantidad, $codigo, $consecutivo, $wusuario, $inslis[$i]['nlo'], $inslis[$i]['prese']);
										descontarInsumoMatrix($inslis[$i]['cod'], $cantidad, $cco, $inslis[$i]['nlo'], $inslis[$i]['prese']);
									}
									$estado='creado';
								}
							}
						}
						else
						{
							pintarAlert1('Verifique la existencia de los articulo señalados en rojo, en el maestro de articulos');
						}

					}
					else
					{
						$val=validarComposicionMatrix(&$inslis, $cco);
						if( pedirPassword( $pwd, $numtra ) ){
							if( $val == 2 ){
								grabarEncabezadoEntradaMatrix(&$codigo, &$consecutivo,  '', $cco, $wusuario, $pintar);
								for ($i=0; $i<count($inslis); $i++)
								{
									//echo $inslis[$i]['cod'];
									if ($inslis[$i]['lot']=='on')
									{
										grabarDetalleEntradaMatrix($inslis[$i]['cod'], $inslis[$i]['can'], $codigo, $consecutivo, $wusuario, $inslis[$i]['nlo'], $inslis[$i]['prese']);
										sumarInsumoMatrix($inslis[$i]['cod'], $inslis[$i]['can'], $cco, $inslis[$i]['nlo'],$inslis[$i]['cod']);
									}
									else
									{
										grabarDetalleEntradaMatrix($inslis[$i]['prese'], $inslis[$i]['can'], $codigo, $consecutivo, $wusuario, $inslis[$i]['nlo'], $inslis[$i]['cod']);
										sumarInsumoMatrix($inslis[$i]['prese'], $inslis[$i]['can'], $cco, '', $inslis[$i]['cod']);
									}

								}
								$estado='creado';
							}
							else{
								if( $val == 1 )
								pintarAlert1('La cantidad a devolver es mayor a la cantidad cargada, se recomienda la cantidad cargada');
								else if( $val == 0 )
								pintarAlert1('No ha ingresado cantidad para devolver, se recomienda la cantidad cargada');
								if( $val == 3 )
								pintarAlert1('No hay cantidades cargadas');
							}
						}
						else{
							pintarAlert1('No tiene permiso para devolver inventarios');
						}
					}
				}
			}
			else
			{
				pintarAlert1('Solo puede ajustar la cantidad del inventario, del material medico quirurgico');
			}
			$cantidad='';
		}
	}

	if (isset ($parcon) and $parcon!='')
	{
		if (!isset ($insfor))
		{
			$insfor='';
		}
		if (!isset ($parcon2))
		{
			$parcon2='';
		}
		if (!isset ($parcon3))
		{
			$parcon3='';
		}
		$consultas=BuscarTraslado($parcon, $parcon2,$parcon3, $insfor, $forcon, $pintar);
		$consulta=$consultas[0];
	}

	if (isset($consulta) and $consulta!='')
	{

		$exp=explode('-',$consulta);
		$numtra=$exp[0].'-'.$exp[1];
		$exp2=explode('(',$consulta);
		$fecha=substr($exp2[1],0, (strlen($exp2[1])-1));
		consultarTraslado($exp[0], $exp[1], $fecha, &$origenes[0], &$destinos[0], &$estado, &$inslis);
		$consultas=array();
	}

	if (!isset($forcon))
	{
		$forcon='';
	}
	if (!isset($consultas))
	{
		//		$consultas='';
		$consultas=array();
	}

	pintarBusqueda($consultas,$forcon, $pintar);

	if (!isset ($fecha))
	{
		$fecha=date('Y-m-d');
	}
	pintarFormulario($estado, $ccos, $numtra, $fecha, $pintar);

	if (isset($insumo) and $insumo!='' and (!isset($cantidad) or $cantidad=='') and (!isset($parbus2) or $parbus2==''))
	{
		$parbus2=explode('-',$insumo);
		$parbus2=$parbus2[0];
		$forbus2='Codigo';
	}

	if (isset($parbus2) and $parbus2!='')
	{
		$insumos=consultarInsumos($parbus2, $forbus2);
		if ($insumos)
		{			
			if ($insumos[0]['lot']=='on')
			{
				$exp=explode('-', $parbus2);
				if (isset($exp[1]))
				{
					$lotes=consultarLotes($insumos[0]['cod'], $cco,$exp[1], &$cantidad);
					$insumos[0]['ven'] = consultarLoteVencido( $exp[1], $insumos[0]['cod']);
				}
				else
				{
					if (!isset($lote))
					{
						$lote='';
					}

					$lotes=consultarLotes($insumos[0]['cod'], $cco, $lote, &$cantidad);
					
					if( $lote != '' && ($lote > $lotes[ 1 ])  ){
						$lote = $lotes[ count( $lotes )-1 ];
						$lote = $lotes[ 1 ];
					}
					
					if($lote!=''){
						$insumos[0]['ven'] = consultarLoteVencido( $lote, $insumos[0]['cod']);
					}
					else{
						if( !empty($lotes) ){
							$insumos[0]['ven'] = consultarLoteVencido( $lotes[ count($lotes)-1 ], $insumos[0]['cod']);
						}
					}
				}
				$unidades='';
			}
			else
			{
				if ($forbus2=='rotulo')
				{
					$unidades=consultarUnidades($insumos[0]['cod'], $cco, $parbus2, &$insumos[0]['pre']);
				}
				else
				{
					if (isset($prese) and $prese!='')
					{
						$prese=explode('-',$prese);
						$prese=$prese[0];
						$unidades=consultarUnidades($insumos[0]['cod'], $cco, $prese, &$insumos[0]['pre']);
					}
					else
					{
						$unidades=consultarUnidades($insumos[0]['cod'], $cco, '', &$insumos[0]['pre']);
					}
				}
			}
		}
	}
	else
	{
		if (isset($insumo) and $insumo!='' and isset($cantidad) and $cantidad!='')
		{
			if (!isset($prese))
			{
				$prese='';
			}

			if (!isset($lote))
			{
				$lote='';
			}
			$exp=explode('-',$insumo);
			if (!isset($inslis))
			{
				$inslis[0]['cod']=$exp[0];
				$inslis[0]['nom']=$exp[1];
				$inslis[0]['gen']=$exp[2];
				$inslis[0]['pre']=$exp[3].'-'.$exp[4];
				//echo $inslis[0]['pre'];
				$inslis[0]['can']=$cantidad;
				$inslis[0]['lot']=$exp[5];
				$inslis[0]['est']=$exp[6];
				$inslis[0]['nlo']=$lote;
				$inslis[0]['prese']=$prese;
				
				$inslis[0]['ven']=$exp[7];

			}
			else
			{
				for ($i=0; $i<count($inslis); $i++)
				{
					if($inslis[$i]['cod']==$exp[0] and $inslis[$i]['nlo']==$lote and $inslis[$i]['prese']==$prese)
					{
						$inslis[$i]['can']=$inslis[$i]['can']+$cantidad;
						$repetido='on';
					}
				}
				if (!isset($repetido))
				{
					$inslis[count($inslis)]['cod']=$exp[0];
					$inslis[count($inslis)-1]['nom']=$exp[1];
					$inslis[count($inslis)-1]['gen']=$exp[2];
					$inslis[count($inslis)-1]['pre']=$exp[3].'-'.$exp[4];
					//echo $inslis[$i]['pre'];
					$inslis[count($inslis)-1]['can']=$cantidad;
					$inslis[count($inslis)-1]['est']=$exp[6];
					$inslis[count($inslis)-1]['lot']=$exp[5];
					$inslis[count($inslis)-1]['nlo']=$lote;
					$inslis[count($inslis)-1]['prese']=$prese;
					
					$inslis[count($inslis)-1]['ven']=consultarLoteVencido( $lote, $exp[0] );
				}
			}
			$cantidad='';
		}

		if (isset($eli))
		{
			$inslis=eliminarInsumo($inslis, $eliminar);
			if ($inslis==false)
			{
				$inslis='';
			}
		}
		$insumos='';
//		$insumos=array();

	}

	if (!isset ($unidades))
	{
		$unidades='';
	}

	if (!isset($lotes))
	{
		$lotes='';
	}
	if (!isset($inslis))
	{
		$inslis='';
	}

	if (!isset($cantidad))
	{
		$cantidad='';
	}
	pintarInsumos($insumos,$inslis, $unidades, $lotes, $pintar, $cantidad);
	
	if( isset($lote) ){
		if($lote != '' || !empty($lote) ){
		}
		echo "<script type=\"text/javascript\">cambiarLote( $lote );</script>";
	}

}
/*===========================================================================================================================================*/

?>


</body>
</html>
