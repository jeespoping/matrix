<head>
  <title>SALIDA A ENTIDADES EXTERNAS DE CENTRAL DE MEZCLAS</title>
  
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
      	
   </style>
  
   <script type="text/javascript">
   function enter()
   {
   	document.producto.accion.value=1;
   	document.producto.submit();
   }

   function enter1()
   {
   	document.producto.submit();
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

*/

/*========================================================FUNCIONES==========================================================================*/

//----------------------------------------------------------funciones de persitencia------------------------------------------------

/**
 * Se anula un cargo de terminado por devolucion, poniendo en encabezado en off
 *
 * @param caracter $cco  centro de costos origen del cargo
 * @param caracter $destino  centro de costos destino del cargo
 * @param caracter $insumo   articulo
 * @param caracter $dato     presentacion si es insumo y lote si es producto
 * @param caracter $lote    si tiene lote
 */
function anularCargo($numtra)
{
	global $conex;
	global $wbasedato;

	$exp=explode('-', $numtra);
	$q= "   UPDATE ".$wbasedato."_000006 "
	."      SET Menest = 'off' "
	."    WHERE Mencon= '".$exp[0]."' "
	."      AND Mendoc= '".$exp[1]."' ";
	
	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ANULAR EL CARGO ".mysql_error());
}

function BuscarTraslado($parcon, $parcon2,$parcon3, $insfor, $forcon, $pintar)
{
	global $conex;
	global $wbasedato;
	global $wbasedatomovhos;

	switch ($forcon)
	{
		case 'Numero de movimiento':
		$exp=explode('-',$parcon);
		if (isset($exp[1]))
		{
			$q= "SELECT Mdecon, Mdedoc, A.Fecha_data "
			."     FROM   ".$wbasedato."_000007 A, ".$wbasedato."_000008 "
			."   WHERE Mdecon = '".$exp[0]."' "
			."     AND Mdedoc = '".$exp[1]."' "
			."     AND Mdeest = 'on' "
			."     AND Mdecon = Concod "
			."     AND conave = 'on' "
			."     GROUP BY 1, 2, 3 ";
		}
		else
		{
			$q= "SELECT  Mdecon, Mdedoc, A.Fecha_data "
			."     FROM   ".$wbasedato."_000007 A, ".$wbasedato."_000008 "
			."   WHERE Mdedoc = '".$parcon."' "
			."     AND Mdeest = 'on' "
			."     AND Mdecon = Concod "
			."     AND conave = 'on' "
			."     GROUP BY 1, 2, 3 ";
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
			$q= "SELECT Mdecon, Mdedoc, A.Fecha_data "
			."     FROM   ".$wbasedato."_000007 A, ".$wbasedato."_000008 "
			."   WHERE Mdeart = '".$parcon."' "
			."     AND A.Fecha_Data between  '".$parcon2."' and '".$parcon3."'"
			."     AND Mdeest = 'on' "
			."     AND Mdecon = Concod "
			."     AND conave = 'on' "
			."     GROUP BY 1, 2, 3 ";
		}
		else if ($insfor=='Nombre comercial')
		{
			$q= "SELECT Mdecon, Mdedoc, A.Fecha_data"
			."     FROM   ".$wbasedato."_000007 A , ".$wbasedato."_000002, ".$wbasedato."_000008 "
			."   WHERE Artcom like '%".$parcon."%' "
			."   AND Artest = 'on' "
			."   AND Mdeart = Artcod "
			."     AND A.Fecha_Data between  '".$parcon2."' and '".$parcon3."'"
			."     AND Mdeest = 'on' "
			."     AND Mdecon = Concod "
			."     AND conave = 'on' "
			."     GROUP BY 1, 2, 3 ";
		}
		else
		{

			$q= "SELECT Mdecon, Mdedoc, A.Fecha_data"
			."     FROM   ".$wbasedato."_000007 A, ".$wbasedato."_000002, ".$wbasedato."_000008 "
			."   WHERE Artgen like '%".$parcon."%' "
			."   AND Artest = 'on' "
			."   AND Mdeart = Artcod "
			."     AND A.Fecha_Data between  '".$parcon2."' and '".$parcon3."'"
			."     AND Mdeest = 'on' "
			."     AND Mdecon = Concod "
			."     AND conave = 'on' "
			."     GROUP BY 1, 2, 3 ";
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
			."     AND conave = 'on' "
			."     GROUP BY 1, 2, 3 ";

		}
		else if ($insfor=='Nombre')
		{
			$q= "SELECT Mdecon, Mdedoc, Menfec "
			."     FROM   ".$wbasedato."_000007, ".$wbasedato."_000006, ".$wbasedatomovhos."_000011, ".$wbasedato."_000008 "
			."   WHERE Cconom like '%".$parcon."%' "
			."     AND Ccoest = 'on' "
			."     AND Mencco = Ccocod "
			."     AND Menfec between  '".$parcon2."' and '".$parcon3."'"
			."     AND Menest = 'on' "
			."     AND Mencon = Mdecon "
			."     AND Mendoc = Mdedoc "
			."     AND Mdeest = 'on' "
			."     AND Mdecon = Concod "
			."     AND conave = 'on' "
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
			."     AND conave = 'on' "
			."     GROUP BY 1, 2, 3 ";
		}
		else if ($insfor=='Nombre')
		{
			$q= "SELECT Mdecon, Mdedoc, Menfec "
			."     FROM   ".$wbasedato."_000007, ".$wbasedato."_000006, ".$wbasedatomovhos."_000011, ".$wbasedato."_000008 "
			."   WHERE Cconom like '%".$parcon."%' "
			."     AND Ccoest = 'on' "
			."     AND Menccd = Ccocod "
			."     AND Menfec between  '".$parcon2."' and '".$parcon3."'"
			."     AND Menest = 'on' "
			."     AND Mencon = Mdecon "
			."     AND Mendoc = Mdedoc "
			."     AND Mdeest = 'on' "
			."     AND Mdecon = Concod "
			."     AND conave = 'on' "
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
	global $wbasedatomovhos;

	if ($unidad!='') //cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
	{
		//consulto los conceptos
		$q =  " SELECT Apppre, Artcom, Artgen, Appcnv, Appexi, Artuni, Unides "
		."        FROM  ".$wbasedato."_000009, ".$wbasedatomovhos."_000026, ".$wbasedatomovhos."_000027 "
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
	."        FROM  ".$wbasedato."_000009, ".$wbasedatomovhos."_000026, ".$wbasedatomovhos."_000027 "
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
	global $wbasedatomovhos;
	global $wcostosyp;

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
	."        FROM ".$wbasedatomovhos."_000011 A, ".$wcostosyp."_000005 B "
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


/**
 * Se consultan los centros de costos para cargo de averias
 *
 * @param caracter $forbus  forma en que se busca el centro de costos, por nombre, codigo etc
 * @param caracter $parbus valor buscado
 * @return vector $centros lista de centros encontrados para desplegar en select
 */
function consultarDestinos($forbus, $parbus)
{
	global $conex;
	global $wbasedato;
	global $wbasedatomovhos;
	global $wcostosyp;

	switch ($forbus)
	{
		case 'codigo':
		$q= " SELECT A.Ccocod, B.Cconom "
		."       FROM ".$wbasedatomovhos."_000011 A, ".$wcostosyp."_000005 B "
		."    WHERE A.Ccocod like '%".$parbus."%' "
		."       AND A.Ccoest = 'on' "
		."       AND A.Ccocod = B.Ccocod "
		."    Order by 1 ";
		$inicio=0;

		break;

		case 'nombre':
		$q= " SELECT A.Ccocod, B.Cconom "
		."       FROM ".$wbasedatomovhos."_000011 A, ".$wcostosyp."_000005 B "
		."    WHERE B.Cconom like '%".$parbus."%' "
		."       AND A.Ccoest = 'on' "
		."       AND A.Ccocod = B.Ccocod "
		."    Order by 1 ";
		$inicio=0;

		break;
		case 'todos':

		if ($parbus!='')
		{
			$centros[0]=$parbus;
			$cadena=" A.Ccocod <> mid('".$parbus."',1,instr('".$parbus."','-')-1) AND";
			$inicio=1;
		}
		else
		{
			$cadena='';
			$inicio=0;
		}
		$q= " SELECT A.Ccocod, B.Cconom "
		."       FROM ".$wbasedatomovhos."_000011 A, ".$wcostosyp."_000005 B "
		."    WHERE ".$cadena." "
		."       A.Ccoest = 'on' "
		."       AND A.Ccocod = B.Ccocod "
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

			$centros[$inicio]=$row[0].'-'.$row[1];
			$inicio++;
		}
	}
	else if (!isset($centros[0]))
	{
		$centros=false;
	}
	return $centros;
}

function consultarConsecutivo($tipo, $movimiento)
{
	global $conex;
	global $wbasedato;

	if ($movimiento==0)
	{
		$q= " SELECT Concod, Concon "
		."       FROM ".$wbasedato."_000008  "
		."    WHERE Conind = '".$tipo."' "
		."       AND conave = 'on' "
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

function consultarInsumos($parbus, $forbus)
{
	global $conex;
	global $wbasedato;
	global $wbasedatomovhos;

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
			."       FROM ".$wbasedato."_000002, ".$wbasedato."_000001, ".$wbasedatomovhos."_000027 "
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
			."       FROM ".$wbasedatomovhos."_000026 A, ".$wbasedatomovhos."_000027 B, ".$wbasedato."_000002 C, ".$wbasedato."_000001 D, ".$wbasedato."_000009 E"
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
		."       FROM ".$wbasedato."_000002, ".$wbasedato."_000001, ".$wbasedatomovhos."_000027 "
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
		."       FROM ".$wbasedato."_000002,  ".$wbasedato."_000001, ".$wbasedatomovhos."_000027 "
		."    WHERE Artcom like '%".$parbus."%' "
		."       AND Artest = 'on' "
		."       AND Tipest = 'on' "
		."       AND Tipcod = Arttip "
		."       AND Artuni= Unicod "
		."       AND Uniest='on' "
		."    Order by 1 ";

		break;
		case 'Nombre generico':

		$q= " SELECT Artcod, Artcom, Artgen, Artuni, Unides, Tippro "
		."       FROM ".$wbasedato."_000002,  ".$wbasedato."_000001, ".$wbasedatomovhos."_000027 "
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

		$res = mysql_query($q,$conex);
		$row = mysql_fetch_array($res);
		$cantidad=$row['Plosal'];
	}
	else
	{
		$cadena='';
		$inicio=0;
	}

	$q= " SELECT Plocod, Plopro, Plocco, Plofcr, Plofve, Plohve, Plocin, Plosal, Ploela, Plocco, Ploest "
	."       FROM ".$wbasedato."_000004 "
	."    WHERE ".$cadena." "
	."       Plopro = '".$parbus."' "
	."       AND Plocco = mid('".$cco."',1,instr('".$cco."','-')-1) "
	."       AND Ploest = 'on' "
	."       AND plosal > 0 "
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


function consultarTraslado($concepto, $consecutivo, $fecha, &$ccoOri, &$ccoDes, &$estado, &$inslis, &$wobs)
{
	global $conex;
	global $wbasedato;
	global $wbasedatomovhos;

	$q= "SELECT Mdeart, Mdecan, Mdepre, Mdenlo, Mencco, Menccd, Menest, Artcom, Artgen, Artuni, Unides, Menfac "
	."     FROM   ".$wbasedato."_000007 A, ".$wbasedato."_000006, ".$wbasedato."_000002, ".$wbasedatomovhos."_000027 "
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
				."     FROM   ".$wbasedatomovhos."_000011 "
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
				."     FROM   ".$wbasedatomovhos."_000011 "
				."   WHERE Ccocod = '".$row['Menccd']."' "
				."     AND Ccoest = 'on' ";
				$res1 = mysql_query($q,$conex);
				$row1 = mysql_fetch_array($res1);

				$wobs=$row['Menfac'];
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
				}
				else
				{
					$estado='Devuelto';
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
				$q =  " SELECT Appcnv, Artcom, Artgen, Artuni, Unides "
				."        FROM  ".$wbasedato."_000009, ".$wbasedatomovhos."_000026, ".$wbasedatomovhos."_000027 "
				."      WHERE Appcod='".$row['Mdeart']."' "
				."            and Appcco=mid('".$cco."',1,instr('".$cco."','-')-1) "
				."            and Appest='on' "
				."            and Apppre=mid('".$row['Mdepre']."',1,instr('".$row['Mdepre']."','-')-1) "
				."            and Apppre=Artcod "
				."            and Artuni=Unicod ";

				$res1 = mysql_query($q,$conex);
				$num1 = mysql_num_rows($res1);
				$row1 = mysql_fetch_array($res1);

				$inslis[$i]['can']=$inslis[$i]['can']/$row1['Appcnv'];
				$inslis[$i]['pre']=$row1['Artuni'].'-'.$row1['Unides'];
				$inslis[$i]['lot']='off';
				$inslis[$i]['est']='on';
				$inslis[$i]['nlo']='';
				$inslis[$i]['prese']=$row['Mdepre'].'-'.str_replace('-',' ',$row1['Artcom']).'-'.str_replace('-',' ',$row1['Artgen']);
			}

		}
	}
}

function validarComposicionMatrix(&$inslis, $cco)
{
	global $conex;
	global $wbasedato;

	$val=2;

	for ($i=0; $i<count($inslis); $i++)
	{
		$inslis[$i]['est']='on';

		$q = " SELECT * FROM ".$wbasedato."_000002 where Artcod='".$inslis[$i]['cod']."' and Artest='on' ";
		$res1 = mysql_query($q,$conex);
		$num3 = mysql_num_rows($res1);
		if ($num3>0)
		{
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
						$q= " SELECT Plosal "
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
					if ($row2[0]>=$cantidad)
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


function grabarEncabezadoSalidaMatrix(&$codigo, &$consecutivo, $cco, $usuario, $cco2, $movimiento, $wobs)
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
		."      AND conave = 'on' "
		."      AND Conest = 'on' ";

		$res1 = mysql_query($q,$conex);

		$q= "   SELECT Concon, Concod from ".$wbasedato."_000008 "
		."    WHERE Conind = '-1'"
		."      AND conave = 'on' "
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


	$q= " INSERT INTO ".$wbasedato."_000006 (   Medico       ,   Fecha_data,                  Hora_data,              Menano,              Menmes ,     Mendoc   ,   Mencon  ,             Menfec,           Mencco ,   Menccd    ,  Mendan,  Menusu,    Menfac,  Menest, Seguridad) "
	."                               VALUES ('".$wbasedato."',  '".date('Y-m-d')."', '".(string)date("H:i:s")."', '".date('Y')."', '".date('m')."','".$row2[0]."', '".$row2[1]."' , '".date('Y-m-d')."', mid('".$cco."',1,instr('".$cco."','-')-1) , mid('".$cco2."',1,instr('".$cco2."','-')-1) ,       '', $usuario,      '".$wobs."' , 'on', 'C-".$usuario."') ";


	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO GRABAR EL ENCABEZADO DEL MOVIIENTO DE SALIDA DE INSUMOS ".mysql_error());
}

function grabarDetalleSalidaMatrix($inscod, $inscan, $codigo, $consecutivo, $usuario, $lote, $prese)
{
	global $conex;
	global $wbasedato;

	if ($lote!='')
	{
		$q= " INSERT INTO ".$wbasedato."_000007 (   Medico       ,   Fecha_data,                  Hora_data,              Mdecon,              Mdedoc ,     Mdeart   ,             Mdecan ,      Mdefve, Mdenlo, Mdepre,  Mdeest,  Seguridad) "
		."                               VALUES ('".$wbasedato."',  '".date('Y-m-d')."', '".(string)date("H:i:s")."', '".$codigo."', '".$consecutivo."','".$inscod."', '".$inscan."' , '',     '".$lote."-".$inscod."', '',  'on', 'C-".$usuario."') ";
	}
	else
	{
		$q= " INSERT INTO ".$wbasedato."_000007 (   Medico       ,   Fecha_data,                  Hora_data,              Mdecon,              Mdedoc ,     Mdeart   ,             Mdecan ,      Mdefve, Mdenlo, Mdepre, Mdeest,  Seguridad) "
		."                               VALUES ('".$wbasedato."',  '".date('Y-m-d')."', '".(string)date("H:i:s")."', '".$codigo."', '".$consecutivo."','".$inscod."', '".$inscan."' , '',     '',  '".$prese."' , 'on', 'C-".$usuario."') ";
	}

	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO GRABAR EL DETALLE DE SALIDA DE UN ARTICULO ".mysql_error());

}

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
		$exp=explode('-',$lote);
		$q= "   UPDATE ".$wbasedato."_000004 "
		."      SET Plosal = Plosal+".$inscan.""
		."    WHERE Plocod =  '".$exp[0]."' "
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
		."      AND conave = 'on' "
		."      AND Conest = 'on' ";

		$res1 = mysql_query($q,$conex);

		$q= "   SELECT Concon, Concod from ".$wbasedato."_000008 "
		."    WHERE Conind = '1'"
		."      AND conave = 'on' "
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
		$q= " INSERT INTO ".$wbasedato."_000007 (   Medico       ,            Fecha_data,                  Hora_data,        Mdecon,           Mdedoc ,                                     Mdeart   ,        Mdecan , Mdefve,                   Mdenlo, Mdepre,  Mdeest,  Seguridad) "
		."                               VALUES ('".$wbasedato."',  '".date('Y-m-d')."', '".(string)date("H:i:s")."', '".$codigo."', '".$consecutivo."','".$prese."', '".$inscan."' ,     '',  '".$lote."-".$codpro."',   '".$codpro."', 'on', 'C-".$usuario."') ";
	}
	else
	{
		$q= " INSERT INTO ".$wbasedato."_000007 (   Medico       ,   Fecha_data,                  Hora_data,              Mdecon,              Mdedoc ,     Mdeart   ,                                        Mdecan , Mdefve,   Mdenlo,          Mdepre, Mdeest,  Seguridad) "
		."                               VALUES ('".$wbasedato."',  '".date('Y-m-d')."', '".(string)date("H:i:s")."', '".$codigo."', '".$consecutivo."','".$prese."', '".$inscan."' ,      '',     '',  '".$codpro."' , 'on', 'C-".$usuario."') ";
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
function pintarTitulo()
{
	global $wemp_pmla;
	$wactualiz ="2007-07-06";
	$institucion = consultarInstitucionPorCodigo( $conex, $wemp_pmla );
	encabezado( "AVERIAS CENTRAL DE MEZCLAS", $wactualiz, $institucion->baseDeDatos );
	echo "<table ALIGN=CENTER width='50%'>";
	//echo "<tr><td align=center colspan=1 ><img src='/matrix/images/medical/general/logo_promo.gif' height='100' width='250' ></td></tr>";
	//echo "<tr><td class='titulo1'>AVERIAS CENTRAL DE MEZCLAS</td></tr>";
	echo "<tr><td class='titulo2'>Fecha: ".date('Y-m-d')."&nbsp Hora: ".(string)date("H:i:s")."</td></tr></table></br>";
}


function pintarBusqueda($consultas, $forcon, $pintar)
{
	global $wemp_pmla;
	echo "<table border=0 ALIGN=CENTER width=90%>";
	echo "<form name='producto2' action='averia.php?wemp_pmla=".$wemp_pmla."' method=post>";
	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";
	echo "<tr><td class='titulo3' colspan='3' align='center'>Consulta: ";
	echo "<select name='forcon' class='texto5' onchange='enter7()'>";
	echo "<option>".$forcon."</option>";
	if ($forcon!='Numero de movimiento')
	echo "<option value='Numero de movimiento'>Numero de movimiento</option>";
	if ($forcon!='Articulo')
	echo "<option>Articulo</option>";
	if ($forcon!='Centro de costos destino')
	echo "<option>Centro de costos destino</option>";
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
		echo "<option>Nombre gen?rico</option>";
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
	if (is_array($consultas) && $consultas[0]['cod']!='')
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
function pintarFormulario($estado, $ccos, $numtra, $fecha, $pintar, $wobs, $destinos)
{
	global $wemp_pmla;
	echo "<form name='producto3' action='averia.php?wemp_pmla=".$wemp_pmla."' method=post>";
	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";
	echo "<tr><td colspan=3 class='titulo3' align='center'><INPUT TYPE='submit' NAME='NUEVO' VALUE='Nuevo' class='texto5' ></td></tr>";
	echo "<input type='hidden' name='pintar' value='".$pintar."'></td>";
	echo "</table></form>";

	echo "<form name='producto' action='averia.php?wemp_pmla=".$wemp_pmla."' method=post>";
	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";
	echo "<table border=0 ALIGN=CENTER width=90%>";

	echo "<tr><td colspan=3 class='titulo3' align='center'><b>Informacion general de la aver?a</b></td></tr>";
	echo "<tr><td class='texto1' colspan='3' align='center'>Centro de costos de origen: ";


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
	echo "</td>";

	echo "</tr><tr><td class='texto1' colspan='3' align='center'>Centro de costos destino: ";
	echo "<select name='fordes' class='texto5'>";
	echo "<option>codigo</option>";
	echo "<option>nombre</option>";
	echo "</select><input type='TEXT' name='pardes' value='' size=10 class='texto5'>&nbsp;<INPUT TYPE='button' NAME='buscar' VALUE='Buscar' onclick='enter1()' class='texto5'> ";
	echo "<select name='ccoDes' class='texto5' onchange='enter1()'>";
	if ($destinos[0]!='')
	{
		for ($i=0;$i<count($destinos);$i++)
		{
			echo "<option >".$destinos[$i]."</option>";
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

	echo "<tr><td class='texto2' colspan='3' align='left'>Observacion:</BR> <textarea name='wobs' cols='80' rows='2'>".$wobs."</textarea></td></tr>";

	switch($estado)
	{
		case 'inicio':
		echo "<tr><td colspan=3 class='titulo3' align='center'><input type='checkbox' name='crear' class='titulo3'>CARGAR &nbsp;<INPUT TYPE='submit' NAME='buscar' VALUE='Aceptar' class='texto5'></tr>";
		break;
		case 'creado':
		echo "<tr><td colspan=3 class='titulo3' align='center'>SE HA CARGADO EL PRODUCTO EXITOSAMENTE&nbsp;&nbsp;&nbsp;<input type='checkbox' name='devolver' class='titulo3'>ANULAR &nbsp;<INPUT TYPE='submit' NAME='buscar' VALUE='Aceptar' class='texto5'></td></tr>";
		break;
		case 'devuelto':
		echo "<tr><td colspan=3 class='titulo3' align='center'>SE HA ANULADO EXITOSAMENTE</td></tr>";
		break;
		case 'Devuelto':
		echo "<tr><td colspan=3 class='titulo3' align='center'>DOCUMENTO ANULADO</td></tr>";
		break;
		case 'Creado':
		echo "<tr><td colspan=3 class='titulo3' align='center'>TRASLADO ACTIVO&nbsp;&nbsp;&nbsp;<input type='checkbox' name='devolver' class='titulo3'>ANULAR &nbsp;<INPUT TYPE='submit' NAME='buscar' VALUE='Aceptar' class='texto5'></td></tr>";
		break;

	}

	echo "<input type='hidden' name='estado' value='".$estado."'></td>";
	echo "</table></br>";
}


function pintarInsumos($insumos, $inslis, $unidades, $lotes, $pintar, $cantidad)
{
	echo "<table border=0 ALIGN=CENTER width=90%>";
	echo "<tr><td colspan='4' class='titulo3' align='center'><b>Lista de articulos</b></td></tr>";


	echo "<tr><td class='texto1' colspan='4' align='center'>Buscar Articulo por: ";
	echo "<select name='forbus2' class='texto5'>";
	echo "<option>rotulo</option>";
	echo "<option>Codigo</option>";
	echo "<option>Nombre comercial</option>";
	echo "<option>Nombre generico</option>";
	echo "</select><input type='TEXT' name='parbus2' value='' size=10 class='texto5'>&nbsp;<INPUT TYPE='submit' NAME='buscar' VALUE='Buscar' class='texto5'></td> ";
	echo "<tr><td class='texto1' colspan='2' align='center'>Articulo: <select name='insumo' class='texto5' onchange='enter6()'>";
	if ($insumos!='')
	{
		for ($i=0;$i<count($insumos);$i++)
		{
			echo "<option value='".$insumos[$i]['cod']."-".$insumos[$i]['nom']."-".$insumos[$i]['gen']."-".$insumos[$i]['pre']."-".$insumos[$i]['lot']."-".$insumos[$i]['est']."'>".$insumos[$i]['cod']."-".$insumos[$i]['nom']."</option>";
		}
	}
	else
	{
		echo "<option ></option>";
	}
	echo "</select></td>";
	if (is_array($insumos) && $insumos[0]['lot']=='on')
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
		echo "<td class='texto1' colspan='2' align='center'>Presentacion: <select name='prese' class='texto5' onchange='enter1()'>";
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
	$datos = '';
	if(is_array($insumos)){
	$datosaux = $insumos[0]['pre'];
    }
	echo "<tr><td class='texto1' colspan='4' align='center'>Cantidad: <input type='TEXT' name='cantidad' value='".$cantidad."'  class='texto5' onchange='validarFormulario()'><input type='TEXT' name='nompro' value='".$datosaux."'  class='texto5' >";
	echo "</td>";
	echo "<tr><td colspan='4' class='texto1' align='center'><INPUT TYPE='button' NAME='buscar' VALUE='Agregar' onclick='enter()' class='texto5'></td></tr>";


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
			echo "<input type='hidden' name='inslis[".$i."][pri]'  value='checked'>";
			echo "<input type='hidden' name='inslis[".$i."][nlo]'  value='".$inslis[$i]['nlo']."'>";
			echo "<input type='hidden' name='inslis[".$i."][prese]'  value='".$inslis[$i]['prese']."'>";

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
		}
	}

	echo "<input type='hidden' name='accion' value='0'></td>";
	echo "<input type='hidden' name='realizar' value='0'></td>";
	echo "<input type='hidden' name='eliminar' value='0'></td>";
	echo "<input type='hidden' name='pintar' value='".$pintar."'></td>";
	echo "</table></br></form>";
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
	//$wbasedato='cenpro';
	
	
//	$conex = mysql_connect('localhost','root','')
//	or die("No se ralizo Conexion");
//	

	
	include_once( "conex.php" );
	include_once("root/comun.php");
	$wbasedato = consultarAliasPorAplicacion( $conex, $wemp_pmla, "cenmez" );
	$wbasedatomovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$wcostosyp = consultarAliasPorAplicacion($conex, $wemp_pmla, "COSTOS");
//    $conex = obtenerConexionBD("matrix");


	//pintarVersion(); //Escribe en el programa el autor y la version del Script.

	if (!isset($pintar))
	{
		$pintar=0;  //Escribe el titulo de la aplicacion, fecha y hora adicionalmente da el acceso a otros scripts

	}
	pintarTitulo();
	$bd='movhos';
	//invoco la funcion connectOdbc del inlcude de ana, para saber si unix responde, en caso contrario,
	//este programa no debe usarse
	//include_once("pda/tablas.php");
	include_once("movhos/fxValidacionArticulo.php");
	include_once("movhos/registro_tablas.php");
	include_once("movhos/otros.php");
	include_once("cenpro/funciones.php");


	//consulto los datos del usuario de la sesion
	$pos = strpos($user,"-");
	$wusuario = substr($user,$pos+1,strlen($user)); //extraigo el codigo del usuario

	//consulto los centros de costos que se administran con esta aplicacion
	//estos se cargan en un select llamado ccos.
	if (isset($cco))
	{
		$ccos=consultarCentros($cco);
	}
	else
	{
		$ccos=consultarCentros('');
	}

	//se consultan los centros de costos de acuerdo a los parametros de busqueda ingresados por el usuario
	//fordes dice si se busca por codigo, nombre del centro de costos
	//pardes que debe buscarse
	//el resultado de centro de costos se guarda en la variable ccoDes
	if (isset($pardes) and $pardes!='')
	{
		$destinos=consultarDestinos($fordes, $pardes);
	}
	else
	{
		if (isset($ccoDes))
		{
			$destinos=consultarDestinos('todos', $ccoDes);
		}
		else
		{
			$destinos=consultarDestinos('todos', '');
		}
	}

	if (!isset($crear) and !isset($devolver))
	{
		$numtra=consultarConsecutivo(-1, $pintar);
	}



	if (!isset($estado))
	{
		$estado='inicio';
	}

	if (isset($crear))
	{
		if (isset($numtra) and $numtra!='')
		{
			if ($wobs=='')
			{
				$val=false;
			}
			else
			{
				$val=true;
			}

			if ($val)
			{
				if (!isset($inslis))
				{
					pintarAlert1('Debe ingresar al menos un articulo para realizar el cargo');
				}
				else
				{
					$val=validarComposicionMatrix($inslis, $cco);
					if ($val>=0)
					{
						if ($val==0)
						{
							pintarAlert1('Sin existencia de los articulos se?alados en rojo, en el kardex de inventario');
						}
						else
						{
							if ($val==1)
							{
								pintarAlert1('Los articulos indicados tienen menos existencias de las requeridas, se propone la cantidad maxima para el traslado');
							}
							else
							{
								grabarEncabezadoSalidaMatrix($codigo, $consecutivo, $cco, $wusuario, $ccoDes, $pintar, $wobs);
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
						pintarAlert1('Verifique la existencia de los articulo se?alados en rojo, en el maestro de articulos');
					}
				}
			}
			else
			{
				pintarAlert1('DEBE INGRESAR UNA OBSERVACION PARA PODER REALIZAR EL CARGO');
			}
			$cantidad='';
		}
	}

	if (isset($devolver))
	{
		anularCargo($numtra);
		grabarEncabezadoEntradaMatrix($codigo, $consecutivo,  $ccoDes, $cco, $wusuario, $pintar);
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
				$cnv=consultarConversor($inslis[$i]['prese'], $cco);
				grabarDetalleEntradaMatrix($inslis[$i]['prese'], $inslis[$i]['can']*$cnv, $codigo, $consecutivo, $wusuario, $inslis[$i]['nlo'], $inslis[$i]['cod']);
				sumarInsumoMatrix($inslis[$i]['prese'], $inslis[$i]['can']*$cnv, $cco, '', $inslis[$i]['cod']);
			}

		}
		$estado='devuelto';
		$cantidad='';
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
		consultarTraslado($exp[0], $exp[1], $fecha, $origenes[0], $destinos[0], $estado, $inslis, $wobs);
	}

	if (!isset($forcon))
	{
		$forcon='';
	}
	if (!isset($consultas))
	{
		$consultas='';
	}

	pintarBusqueda($consultas,$forcon, $pintar);

	if (!isset ($fecha))
	{
		$fecha=date('Y-m-d');
	}

	if (!isset($wobs))
	{
		$wobs='';
	}

	pintarFormulario($estado, $ccos, $numtra, $fecha, $pintar, $wobs, $destinos);

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
					$lotes=consultarLotes($insumos[0]['cod'], $cco,$exp[1], $cantidad);
				}
				else
				{
					if (!isset($lote))
					{
						$lote='';
					}
					$lotes=consultarLotes($insumos[0]['cod'], $cco, $lote, $cantidad);
				}
				$unidades='';
			}
			else
			{
				if ($forbus2=='rotulo')
				{
					$unidades=consultarUnidades($insumos[0]['cod'], $cco, $parbus2, $insumos[0]['pre']);
				}
				else
				{
					if (isset($prese) and $prese!='')
					{
						$prese=explode('-',$prese);
						$prese=$prese[0];
						$unidades=consultarUnidades($insumos[0]['cod'], $cco, $prese, $insumos[0]['pre']);
					}
					else
					{
						$unidades=consultarUnidades($insumos[0]['cod'], $cco, '', $insumos[0]['pre']);
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

}
/*===========================================================================================================================================*/

?>


</body >
</html >
