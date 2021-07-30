<head>
  <title>TRASLADOS DE CENTRAL DE MEZCLAS</title>
  
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
   
    //Esta funcion muestra un mensaje si el formulario se esta enviando e intentan selecciona de nuevo el boton aceptar. 20 Enero de 2014.
    var cuenta=0;
	function enviado() {
	
	if (cuenta == 0){
		cuenta++;
		return true;
	}else{
		alert("El formulario ya está siendo enviado, por favor espere un momento.");
		return false;
		}
	}
   
   function enter()
   {
   	document.producto.accion.value=1;
   	document.producto.submit();
   }

   function enter1()
   {
   	document.producto.submit();
   }

   function enter6()
   {
   	document.producto.cantidad.value='';
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
   	if (document.producto.elements[12].value=='')
   	{
   		document.producto.elements[10].focus();
   	}
   	else
   	{
   		document.producto.elements[13].focus();
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
   	valor = parseInt(valor);
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
// *========================================================DOCUMENTACION PROGRAMA================================================================================*/
/** 
* 1. AREA DE VERSIONAMIENTO
* 
* Nombre del programa: traslados.php
* Fecha de creacion:  2007-06-15
* Autor: Carolina Castano P
* Ultima actualizacion:
* 2017-02-06  Jessica Madrid M  Se modifica el número de item para que tenga el orden en que se agregan los articulos
* 2017-01-30  Jessica Madrid M  Se agrega número de item y se modifica para que cada vez que se agregue un artículo quede en la parte 
*								superior para que sea más fácil visualizar los artículos recién agregados.
* 2014-01-20  Jonatan Lopez. 	Se agrega la funcion enviado() tipo javascript, para evitar el envio doble de informacion y asi evitar registros duplicados.
* 2007-08-21  Carolina Castaño  Se cambia para que se graba el centro de costos origen o destino en Unix segun el tipo de movimiento y para guardar el 
*								numero de unix como documento anexo.
* 2007-07-13  Carolina Castaño  Se arregla para que se reconozca codigo de barras del proveedor
* 2007-07-09  Carolina Castaño  Se evitan los guiones en el nombre comercial y genericos de los producto
* 2007-07-03  Carolina Castaño  Publicacion
* 
* 
* 2. AREA DE DESCRIPCION:
* 
* Este script realiza las historias:
* 
* 1. Traslado de insumos hacia la central
* 2. Traslado de insumos desde la central
* 3. Consulta de traslados
* 
* 3. AREA DE VARIABLES DE TRABAJO
* 
* $aprov           boolean          indica cuando esta en on, que la transaccion no mueve inventario
* $art             vector           vector con codigo y cantidad del articulo que se pasa a las funciones de ana de validacion de articulo en unix
* $bd              caracter         base de datos que se pasa a las funciones de ana de validacion de articulos en unix
* $cantidad        numerico         cantidad a trasladar del insumo que se esta ingresando a la lista de insumos
* $ccoDes          caracter         centro de costos destino (codigo-descripcion)
* $ccoOri          caracter         centro de costos origen (codigo-descripcion)
* $centro          vector           vector con datos del centro de costos origen, para pasar a validaciones unix
* $centro2         caracter         vector con datos del centro de costos destino, para pasar a validaciones unix
* $cnv             numerico         factor de conversion a unidad minima de trabajo de una presentacion
* $codigo          caracter         concepto de inventario para grabar el traslado
* $concepto2       caracter         concepto de inventario que raba el movmiento de unix
* $consecutivo     numerico         consecutivo para el movimiento de inventario
* $consulta        caracter         Traslado escogido en el select al consultar un traslado por un parametro determinado
* $consultas       vector           Lista de resultados de la busqueda de traslados por un parametro determiando
* $crear           boolean          Esta setiado cuando se ha decidido grabar el traslado
* $destino         boolean          En on cuando quien tienen el inventario en matrix es el destino
* $destinos        vector           Lista de centros de costos que puede escoger el usuario como destino
* $documento2      numerico         consecutivo para el movimiento de inventario que registra el movimiento en unix
* $eli             boolean          setiado cuando vamos a eliminar un registro del vector de articulos a trasladar
* $eliminar        numerico         indice del registro que se va a elimiar de la lista de articulos
* $estado          caracter         Indica si es un traslado activo, creado o desactivado
* $fecha           date             Fecha de realizacion del traslado
* $forbus2         caracter         Forma de busqueda de un insumo (por rotulo, codigo o nombre)
* $forcon          caracter         Forma de busqueda de un traslado(por numero, articulo, centro de costos)
* $fuente2         caracter         Se refiere a la fuente con la que se graba el movimiento en unix
* $insfor          caracter         Cuando se va a buscar el traslado por insumo, forma de ingreso del insumo (codigo, nombre)
* $inslis          vector           Lista de articulos que se van a trasladar y sus cantidades
* $insumo          caracter         Insumo que se va a ingresar a la lista de articulos a trasladar
* $insumos         vector           Lista de insumos encontrados en una busqueda determinada de articulos
* $lote            caracter         Numero de lote seleccionado por el usuario para un producto ingresado
* $lotes           vector           Lista de lotes para un producto a ingresar, desplegables en un select
* $numtra          caracter         Numero del traslado (concepto-consecutivo)
* $origen          boolean          Si el centro de costos de origen tiene inventario en matrix, esta en on
* $origenes        vector           Vector de centros de costos de origen que puede seleccionar el usuario
* $parbus2        caracter          Valor de busqueda de un insumo determinado
* $parcon         caracter          Valor de busqueda de un traslado determinado
* $parcon2        caracter          Cuando se busca un traslado por insumo o cco, hay que meter la fecha de incio de busqueda
* $parcon3        caracter          Cuando se busca un traslado por insumo o cco, hay que meter la fecha de final de bsuqueda
* $prese          caracter          Presentacion de un insumo determinado a trasladar
* $present        caracter          Presentacion de un insumo determinado a trasladar para pasar a las funciones de grabar en unix
* $repetido       boolean           Indica que un insumo ingresado ya esta en la lista de articulos a trasladar, se suma la cantidad
* $tipTrans       caracter          Tipo de transaccion, se psas C a las funciones de validadcion de articulos en unix
* $unidades       vector            Vector con las difrentes presentaciones de un insumo determinado
* $val            boolean           Indica si pasa o no todas las validaciones para la grabacion del traslado
* 
* 4. AREA DE TABLAS
* 000001 SELECT
* 000002 SELECT
* 000004 SELECT, UPDATE
* 000005 SELECT, UPDATE
* 000006 SELECT, INSERT
* 000007 SELECT, INSERT
* 000008 SELECT, UPDATE
* 000009 SELECT UPDATE
* MOVHOS_000011 SELECT
* MOVHOS_000026 SELECT
* Usuarios SELECT
* movhos_000027
* COSTOSYP_000005
* 
* 
* /*========================================================FUNCIONES==========================================================================
*/
// ----------------------------------------------------------funciones de persitencia------------------------------------------------
/**
* Se buscan los traslados que concuerden con una forma de busqueda y valores determinados
* 
* @param caracter $parcon valor de la busqueda
* @param caracter $parcon2 si la busqueda es por insumo, es la fecha incial de la busqueda
* @param caracter $parcon3 si la busqueda es por insumo, es la fecha final de la busqueda
* @param caracter $insfor si la busqueda por insumo, el valor con el que se indica el insumo
* @param caracter $forcon forma de busqueda (por codigo, insumo, numero de traslado etc)
* @return vector $consultas      lista de traslados encontrados segun los parametros de busqueda
*/
function BuscarTraslado($parcon, $parcon2, $parcon3, $insfor, $forcon)
{
	global $conex;
	global $wbasedato;
	global $bd;
	
	

	switch ($forcon)
	{
		case 'Numero de traslado':
		$exp = explode('-', $parcon);
		if (isset($exp[1]))
		{
			$q = "SELECT Mdecon, Mdedoc, A.Fecha_data "
			. "     FROM   " . $wbasedato . "_000007 A, " . $wbasedato . "_000008 "
			. "   WHERE Mdecon = '" . $exp[0] . "' "
			. "     AND Mdedoc = '" . $exp[1] . "' "
			. "     AND Mdeest = 'on' "
			. "     AND Mdecon = Concod "
			. "     AND Contra = 'on' "
			. "     GROUP BY 1, 2, 3 ";
		}
		else
		{
			$q = "SELECT  Mdecon, Mdedoc, A.Fecha_data "
			. "     FROM   " . $wbasedato . "_000007 A, " . $wbasedato . "_000008 "
			. "   WHERE Mdedoc = '" . $parcon . "' "
			. "     AND Mdeest = 'on' "
			. "     AND Mdecon = Concod "
			. "     AND Contra = 'on' "
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
			. "   WHERE Mdeart = '" . $parcon . "' "
			. "     AND A.Fecha_Data between  '" . $parcon2 . "' and '" . $parcon3 . "'"
			. "     AND Mdeest = 'on' "
			. "     AND Mdecon = Concod "
			. "     AND Contra = 'on' "
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
			. "     AND Contra = 'on' "
			. "     GROUP BY 1, 2, 3 ";
		}
		else
		{
			$q = "SELECT Mdecon, Mdedoc, A.Fecha_data"
			. "     FROM   " . $wbasedato . "_000007 A, " . $wbasedato . "_000002, " . $wbasedato . "_000008 "
			. "   WHERE Artgen like '%" . $parcon . "%' "
			. "   AND Artest = 'on' "
			. "   AND Mdeart = Artcod "
			. "     AND A.Fecha_Data between  '" . $parcon2 . "' and '" . $parcon3 . "'"
			. "     AND Mdeest = 'on' "
			. "     AND Mdecon = Concod "
			. "     AND Contra = 'on' "
			. "     GROUP BY 1, 2, 3 ";
		}
		break;

		case 'Centro de costos de origen':
		
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
			. "     FROM   " . $wbasedato . "_000007, " . $wbasedato . "_000006, " . $wbasedato . "_000008 "
			. "   WHERE Mencco = '" . $parcon . "' "
			. "     AND Menfec between  '" . $parcon2 . "' and '" . $parcon3 . "'"
			. "     AND Menest = 'on' "
			. "     AND Mencon = Mdecon "
			. "     AND Mendoc = Mdedoc "
			. "     AND Mdeest = 'on' "
			. "     AND Mdecon = Concod "
			. "     AND Contra = 'on' "
			. "     GROUP BY 1, 2, 3 ";
		}
		else if ($insfor == 'Nombre')
		{
			$q = "SELECT Mdecon, Mdedoc, Menfec "
			. "     FROM   " . $wbasedato . "_000007, " . $wbasedato . "_000006, ".$bd."_000011, " . $wbasedato . "_000008 "
			. "   WHERE Cconom like '%" . $parcon . "%' "
			. "     AND Ccoest = 'on' "
			. "     AND Mencco = Ccocod "
			. "     AND Menfec between  '" . $parcon2 . "' and '" . $parcon3 . "'"
			. "     AND Menest = 'on' "
			. "     AND Mencon = Mdecon "
			. "     AND Mendoc = Mdedoc "
			. "     AND Mdeest = 'on' "
			. "     AND Mdecon = Concod "
			. "     AND Contra = 'on' "
			. "     GROUP BY 1, 2, 3 ";
		}
		break;

		case 'Centro de costos destino':
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
			. "   WHERE Menccd = '" . $parcon . "' "
			. "     AND Menfec between  '" . $parcon2 . "' and '" . $parcon3 . "'"
			. "     AND Menest = 'on' "
			. "     AND Mencon = Mdecon "
			. "     AND Mendoc = Mdedoc "
			. "     AND Mdeest = 'on' "
			. "     AND Mdecon = Concod "
			. "     AND Contra = 'on' "
			. "     GROUP BY 1, 2, 3 ";
		}
		else if ($insfor == 'Nombre')
		{
			$q = "SELECT Mdecon, Mdedoc, Menfec "
			. "     FROM   " . $wbasedato . "_000007, " . $wbasedato . "_000006, ".$bd."_000011, " . $wbasedato . "_000008 "
			. "   WHERE Cconom like '%" . $parcon . "%' "
			. "     AND Ccoest = 'on' "
			. "     AND Menccd = Ccocod "
			. "     AND Menfec between  '" . $parcon2 . "' and '" . $parcon3 . "'"
			. "     AND Menest = 'on' "
			. "     AND Mencon = Mdecon "
			. "     AND Mendoc = Mdedoc "
			. "     AND Mdeest = 'on' "
			. "     AND Mdecon = Concod "
			. "     AND Contra = 'on' "
			. "     GROUP BY 1, 2, 3 ";
		}
		break;
	}

	$res1 = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());
	$num1 = mysql_num_rows($res1);
	if ($num1 > 0)
	{
		for ($i = 0; $i < $num1; $i++)
		{
			$row1 = mysql_fetch_array($res1);
			$consultas[$i] = $row1[0] . '-' . $row1[1] . '-(' . $row1[2] . ')';
		}
		return $consultas;
	}
	else
	{
		return '';
	}
}

/**
* Consulta las diferentes presentaciones en que viene un insumo, con la unidad en que se miden las presentaciones
* 
* @param caracter $codigo codigo del insumo de la central
* @param caracter $cco centro de costos que tiene inventario en matrix
* @param caracter $unidad presentacion que ya ha sido ingresada por el usuario
* @param caracter $insumo unidad de medida de las presentaciones de ese insumo
* @return vector $unidades  vector con las diferentes presentaciones para el insumo
*/
function consultarUnidades($codigo, $cco, $unidad, &$insumo)
{
	global $conex;
	global $wbasedato;
	global $bd;

	if ($unidad != '') // cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
	{
		// consulto los conceptos
		$q = " SELECT Apppre, Artcom, Artgen, Appcnv, Appexi, Artuni, Unides "
		. "        FROM  " . $wbasedato . "_000009, ".$bd."_000026,".$bd."_000027 "
		. "      WHERE Apppre='" . $unidad . "' "
		. "            and Appcco=mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
		. "            and Appest='on' "
		. "            and Apppre=Artcod "
		. "            and Artuni=Unicod ";

		$res1 = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());
		$num1 = mysql_num_rows($res1);
		if ($num1 > 0)
		{
			$row1 = mysql_fetch_array($res1);
			$enteras = floor($row1['Appexi'] / $row1['Appcnv']);
			$fracciones = $row1['Appexi'] % $row1['Appcnv'];
			$row1['Artcom'] = str_replace('-', ' ', $row1['Artcom']);
			$row1['Artgen'] = str_replace('-', ' ', $row1['Artgen']);
			$unidades[0] = $row1['Apppre'] . '-' . $row1['Artcom'] . '-' . $row1['Artgen'] . '-' . $enteras . '-' . $fracciones;
			$cadena = "Apppre != '" . $unidad . "' AND";
			$inicio = 1;
			$insumo = $row1['Artuni'] . '-' . $row1['Unides'];
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
	$q = " SELECT Apppre, Artcom, Artgen, Appcnv, Appexi, Artuni, Unides "
	. "        FROM  " . $wbasedato . "_000009, ".$bd."_000026,".$bd."_000027 "
	. "      WHERE " . $cadena . " "
	. "             Appcod='" . $codigo . "' "
	. "            and Appcco=mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
	. "            and Appest='on' "
	. "            and Apppre=Artcod "
	. "            and Artuni=Unicod ";

	$res1 = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());
	$num1 = mysql_num_rows($res1);
	if ($num1 > 0)
	{
		for ($i = 0;$i < $num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);

			$enteras = floor($row1['Appexi'] / $row1['Appcnv']);
			$fracciones = $row1['Appexi'] % $row1['Appcnv'];
			$row1['Artcom'] = str_replace('-', ' ', $row1['Artcom']);
			$row1['Artgen'] = str_replace('-', ' ', $row1['Artgen']);
			$unidades[$inicio] = $row1['Apppre'] . '-' . $row1['Artcom'] . '-' . $row1['Artgen'] . '-' . $enteras . '-' . $fracciones;
			$inicio++;
			if ($inicio == 1)
			{
				$insumo = $row1['Artuni'] . '-' . $row1['Unides'];
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

/**
* Se buscan lo centros de costos que pueden hacer traslados y de donde hacia donde segun el usuario (ccotra en on en movhos_000011)
* 
* @param caracter $cco centro de costos que ya habia sido escogido previamente por el usuario
* @param caracter $usuario codigo del usuario del sistema
* @param boolena $origen , si es el centro de origen o no
* @return vector $centro, lista de centros encontrados
*/
function consultarCentros($cco, $usuario, $origen, $wemp_pmla)
{
	global $conex;
	global $wbasedato;
	global $bd;

	if ($cco != '')
	{
		$centros[0] = $cco;
		$cadena = " A.Ccocod <> mid('" . $cco . "',1,instr('" . $cco . "','-')-1) AND";
		$inicio = 1;
	}
	else
	{
		$cadena = '';
		$inicio = 0;
	}

	$q = " SELECT A.Ccoima "
	. "      FROM ".$bd."_000011 A, usuarios C "
	. "    WHERE  A.Ccoest = 'on' "
	. "       AND A.Ccotra = 'on' "
	. "       AND C.Ccostos = A.Ccocod "
	. "       AND C.Codigo = '" . $usuario . "' "
	. "    Order by 1 ";

	$res = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());
	$row = mysql_fetch_array($res);
	$num = mysql_num_rows($res);

	if (($row[0] == 'on' and $origen == 'on') or ($row[0] != 'on' and $origen != 'on') or ($num < 0 and $origen != 'on'))
	{
		$q = " SELECT A.Ccocod, B.Cconom "
		. "       FROM ".$bd."_000011 A, costosyp_000005 B "
		. "    WHERE " . $cadena . " "
		. "       A.Ccoest = 'on' "
		. "       AND A.Ccotra = 'on' "
		. "       AND A.Ccoima = 'on' "
		. "       AND A.Ccocod = B.Ccocod "
		. "       AND B.Ccoemp = '".$wemp_pmla."' " 
		. "    Order by 1 ";

	}
	else
	{
		$q = " SELECT A.Ccocod, B.Cconom "
		. "       FROM ".$bd."_000011 A, costosyp_000005 B "
		. "    WHERE " . $cadena . " "
		. "       A.Ccoest = 'on' "
		. "       AND A.Ccotra = 'on' "
		. "       AND A.Ccocod = B.Ccocod "
		. "       AND A.Ccoima <> 'on' "
		. "       AND B.Ccoemp = '".$wemp_pmla."' " 
		. "    Order by 1 ";

	}

	$res = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		for ($i = 0;$i < $num;$i++)
		{
			$row = mysql_fetch_array($res);

			$centros[$inicio] = $row[0] . '-' . $row[1];
			$inicio++;
		}
	}
	else if (!isset($centros[0]))
	{
		$centros = false;
	}
	return $centros;
}

/**
* consulta si el centro de costos tienen inventario en matrix
* 
* @param caracter $cco (codigo-descripcion)
* @return boolean $resultado si tienen inventario en matrix (on) o no (off)
*/
function consultarCco($cco)
{
	global $conex;
	global $wbasedato;
	global $bd;
	

	$exp = explode('-', $cco);

	 $q = " SELECT Ccoima "
	. "       FROM ".$bd."_000011  "
	. "    WHERE Ccocod = '" . $exp[0] . "' "
	. "       AND Ccoest = 'on' "
	. "       AND Ccotra = 'on' ";
	

	$res = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());
	$row = mysql_fetch_array($res);

	$resultado = $row[0];
	
	return $resultado;
}

/**
* Consulta el consecutivo del movimiento de inventario, de pendeindo de si es entrada o salida
* 
* @param caracter $tipo si es una salida de matrix (-1), si es una entrada a matrix (1)
* @return caracter $resultado el consecutivo del movimiento de inventario
*/
function consultarConsecutivo($tipo)
{
	global $conex;
	global $wbasedato;

	$q = " SELECT Concod, Concon "
	. "       FROM " . $wbasedato . "_000008  "
	. "    WHERE Conind = '" . $tipo . "' "
	. "       AND Contra = 'on' "
	. "       AND Conest = 'on' ";

	$res = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());
	$row = mysql_fetch_array($res);

	$resultado = $row[0] . '-' . ($row[1] + 1);
	return $resultado;
}

/**
* Se busca un articulo de acuerdo a un parametro de busqueda, entregando un vector con la lista de articulos encontrados.
*   Si el centro de origen tiene inventario en matrix, se busca el insumo de codigo propio e la central
* 
* @param caracter $parbus valor que se busca
* @param caracter $forbus forma de busqueda
* @param caracter $origen esta en on, cuando el articulo viene de matrix
* @return vector $rpoductos lista de articulos encontrados
*/
function consultarInsumos($parbus, $forbus, $origen)
{
	global $conex;
	global $wbasedato;
	global $bd;
    
	if ($origen == 'on')
	{
		switch ($forbus)
		{
			case 'rotulo':

			$q = " SELECT Tippro "
			. "       FROM " . $wbasedato . "_000002, " . $wbasedato . "_000001 "
			. "    WHERE Artcod = '" . $parbus . "' "
			. "       AND Artest = 'on' "
			. "       AND Tipest = 'on' "
			. "       AND Tipcod = Arttip "; 
			
			$res = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());
			$row = mysql_fetch_array($res);
			// $exp=explode('-', $parbus);
			// if (isset($exp[1]))
			if (isset($row[0]) and $row[0] == 'on')
			{
				$q = " SELECT Artcod, Artcom, Artgen, Artuni, Unides, Tippro "
				. "       FROM " . $wbasedato . "_000002, " . $wbasedato . "_000001, ".$bd."_000027 "
				. "    WHERE Artcod = '" . $parbus . "' "
				. "       AND Artest = 'on' "
				. "       AND Artuni= Unicod "
				. "       AND Tipest = 'on' "
				. "       AND Tipcod = Arttip "
				. "       AND Tipcdo = 'on' "
				. "       AND Uniest='on' "
				. "    Order by 1 "; 
			}
			else
			{
				$q = " SELECT C.Artcod, C.Artcom, C.Artgen, C.Artuni, B.Unides, D.Tippro  "
				. "       FROM ".$bd."_000026 A, ".$bd."_000027 B, " . $wbasedato . "_000002 C, " . $wbasedato . "_000001 D, " . $wbasedato . "_000009 E"
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
			$q = " SELECT Artcod, Artcom, Artgen, Artuni, Unides, Tippro "
			. "       FROM " . $wbasedato . "_000002, " . $wbasedato . "_000001, ".$bd."_000027 "
			. "    WHERE Artcod like '%" . $parbus . "%' "
			. "       AND Artest = 'on' "
			. "       AND Artuni= Unicod "
			. "       AND Tipest = 'on' "
			. "       AND Tipcod = Arttip "
			. "       AND Uniest='on' "
			. "    Order by 1 "; 

			break;
			case 'Nombre comercial':

			$q = " SELECT Artcod, Artcom, Artgen, Artuni, Unides, Tippro "
			. "       FROM " . $wbasedato . "_000002,  " . $wbasedato . "_000001, ".$bd."_000027 "
			. "    WHERE Artcom like '%" . $parbus . "%' "
			. "       AND Artest = 'on' "
			. "       AND Tipest = 'on' "
			. "       AND Tipcod = Arttip "
			. "       AND Artuni= Unicod "
			. "       AND Uniest='on' "
			. "    Order by 1 "; 
		

			break;
			case 'Nombre generico':

			$q = " SELECT Artcod, Artcom, Artgen, Artuni, Unides, Tippro "
			. "       FROM " . $wbasedato . "_000002,  " . $wbasedato . "_000001, ".$bd."_000027 "
			. "    WHERE Artgen like '%" . $parbus . "%' "
			. "       AND Artest = 'on' "
			. "       AND Tipest = 'on' "
			. "       AND Tipcod = Arttip "
			. "       AND Artuni= Unicod "
			. "       AND Uniest='on' "
			. "    Order by 1 ";

			break;
		}
	}
	else
	{
		switch ($forbus)
		{
			case 'rotulo':
			$exp = explode('-', $parbus);
			if (isset($exp[1]))
			{
				$q = " SELECT Artcod, Artcom, Artgen, Artuni, Unides "
				. "       FROM ".$bd."_000026, ".$bd."_000027 "
				. "    WHERE Artcod = '" . $exp[0] . "' "
				. "       AND Artest = 'on' "
				. "       AND Artuni= Unicod "
				. "       AND Uniest='on' "
				. "    Order by 1 "; 
			}
			else
			{
				$q = " SELECT Artcod, Artcom, Artgen, Artuni, Unides "
				. "       FROM ".$bd."_000026, ".$bd."_000027 "
				. "    WHERE Artcod = '" . $parbus . "' "
				. "       AND Artest = 'on' "
				. "       AND Artuni= Unicod "
				. "       AND Uniest='on' "
				. "    Order by 1 ";
			}
			break;

			case 'Codigo':
			$q = " SELECT Artcod, Artcom, Artgen, Artuni, Unides "
			. "       FROM ".$bd."_000026, ".$bd."_000027 "
			. "    WHERE Artcod like '%" . $parbus . "%' "
			. "       AND Artest = 'on' "
			. "       AND Artuni= Unicod "
			. "       AND Uniest='on' "
			. "    Order by 1 ";

			break;
			case 'Nombre comercial':

			$q = " SELECT Artcod, Artcom, Artgen, Artuni, Unides "
			. "       FROM ".$bd."_000026,  ".$bd."_000027 "
			. "    WHERE Artcom like '%" . $parbus . "%' "
			. "       AND Artest = 'on' "
			. "       AND Artuni= Unicod "
			. "       AND Uniest='on' "
			. "    Order by 1 "; 

			break;
			case 'Nombre generico':

			$q = " SELECT Artcod, Artcom, Artgen, Artuni, Unides "
			. "       FROM ".$bd."_000026, ".$bd."_000027 "
			. "    WHERE Artgen like '%" . $parbus . "%' "
			. "       AND Artest = 'on' "
			. "       AND Artuni= Unicod "
			. "       AND Uniest='on' "
			. "    Order by 1 ";

			break;
		}
	}
	
	$res = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());
	$num = mysql_num_rows($res);	

	if ($num > 0)
	{
		for ($i = 0;$i < $num;$i++)
		{
			$row = mysql_fetch_array($res);

			$productos[$i]['cod'] = $row[0];
			$productos[$i]['nom'] = str_replace('-', ' ', $row[1]);
			$productos[$i]['gen'] = str_replace('-', ' ', $row[2]);
			$productos[$i]['est'] = 'on';
			if ($origen == 'on')
			{
				if ($row[5] == 'on')
				{
					$productos[$i]['lot'] = 'on';
					$productos[$i]['pre'] = $row[3] . '-' . $row[4];
				}
				else
				{
					$productos[$i]['lot'] = 'off';
					$productos[$i]['pre'] = '';
				}
			}
			else
			{
				$productos[$i]['pre'] = $row[3] . '-' . $row[4];
				$q = " SELECT * "
				. "       FROM " . $wbasedato . "_000002,  " . $wbasedato . "_000001 "
				. "    WHERE Artcod = '" . $productos[$i]['cod'] . "' "
				. "       AND Artest = 'on' "
				. "       AND Tipest = 'on' "
				. "       AND Tipcod = Arttip "
				. "       AND Tippro = 'on' ";
				$res3 = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());
				$num2 = mysql_num_rows($res3);
				if ($num2 > 0)
				{
					$productos[$i]['lot'] = 'on';
				}
				else
				{
					$productos[$i]['lot'] = 'off';
				}
			}
		}
	}
	else if ($forbus == 'rotulo')
	{
		$art['cod'] = BARCOD($parbus);
		ArticuloCba ($art);

		if ($origen == 'on')
		{
			$q = " SELECT C.Artcod, C.Artcom, C.Artgen, C.Artuni, B.Unides, D.Tippro  "
			. "       FROM ".$bd."_000026 A, ".$bd."_000027 B, " . $wbasedato . "_000002 C, " . $wbasedato . "_000001 D, " . $wbasedato . "_000009 E"
			. "    WHERE A. Artcod = '" . $art['cod'] . "' "
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
		else
		{
			$q = " SELECT Artcod, Artcom, Artgen, Artuni, Unides "
			. "       FROM ".$bd."_000026, ".$bd."_000027 "
			. "    WHERE Artcod = '" . $art['cod'] . "' "
			. "       AND Artest = 'on' "
			. "       AND Artuni= Unicod "
			. "       AND Uniest='on' "
			. "    Order by 1 ";
		}

		$res = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());
		$num = mysql_num_rows($res);

		if ($num > 0)
		{
			for ($i = 0;$i < $num;$i++)
			{
				$row = mysql_fetch_array($res);

				$productos[$i]['cod'] = $row[0];
				$productos[$i]['nom'] = str_replace('-', ' ', $row[1]);
				$productos[$i]['gen'] = str_replace('-', ' ', $row[2]);
				$productos[$i]['est'] = 'on';
				if ($origen == 'on')
				{
					if ($row[5] == 'on')
					{
						$productos[$i]['lot'] = 'on';
						$productos[$i]['pre'] = $row[3] . '-' . $row[4];
					}
					else
					{
						$productos[$i]['lot'] = 'off';
						$productos[$i]['pre'] = '';
					}
				}
				else
				{
					$productos[$i]['pre'] = $row[3] . '-' . $row[4];
					$q = " SELECT * "
					. "       FROM " . $wbasedato . "_000002,  " . $wbasedato . "_000001 "
					. "    WHERE Artcod = '" . $productos[$i]['cod'] . "' "
					. "       AND Artest = 'on' "
					. "       AND Tipest = 'on' "
					. "       AND Tipcod = Arttip "
					. "       AND Tippro = 'on' ";
					$res3 = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());
					$num2 = mysql_num_rows($res3);
					if ($num2 > 0)
					{
						$productos[$i]['lot'] = 'on';
					}
					else
					{
						$productos[$i]['lot'] = 'off';
					}
				}
			}
		}
		else
		{
			$productos = false;
		}
	}
	else
	{
		$productos = false;
	}
	return $productos;
}

/**
* Se consulta el factor de conversion del insumo a una presentacion determinada (cuanta unidad minima hay en la presentacion)
* 
* @param caracter $codigo presentacion utilizada
* @param caracter $cco centro de costos
* @return numerico factor de conversion
*/
function consultarConversor($codigo, $cco)
{
	global $conex;
	global $wbasedato;
	// consulto los conceptos
	$q = " SELECT Appcnv "
	. "        FROM  " . $wbasedato . "_000009 "
	. "      WHERE Apppre=mid('" . $codigo . "',1,instr('" . $codigo . "','-')-1) "
	. "            and Appcco=mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
	. "            and Appest='on' ";

	$res1 = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());
	$num1 = mysql_num_rows($res1);
	if ($num1 > 0)
	{
		$row1 = mysql_fetch_array($res1);
		return $row1[0];
	}
	else
	{
		return false;
	}
}

/**
* Consulto los lotes de un producto, si el origen es matrix, debo consultar los lotes con saldo y si viene de unix
* se despliegan los lotes del mas nuevo al mas viejo aunque no tenga saldo
* 
* @param caracter $parbus codigo del producto
* @param caracter $cco centro de costos del que tiene inventario en matrix (codigo-descripcion)
* @param caracter $lote numero de lote si ya se ha seleciconado uno
* @param boolean $origen en on si el origen es del inventario de matrix
* @return vector $consultas lista de lotes encontrados
*/
function consultarLotes($parbus, $cco, $lote, $origen, &$cantidad)
{
	global $conex;
	global $wbasedato;

	if ($lote != '') // cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
	{
		$consultas[0] = $lote;
		$cadena = "Plocod != '" . $lote . "' AND";
		$inicio = 1;

		if ($origen == 'on')
		{
			$q = " SELECT Plosal "
			. "       FROM " . $wbasedato . "_000004 "
			. "    WHERE  Plocod = '" . $lote . "' "
			. "       AND Plopro = '" . $parbus . "' "
			. "       AND Plocco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
			. "       AND Ploest = 'on' "
			. "    Order by 1 desc  ";

			$res = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());
			$row = mysql_fetch_array($res);
			$cantidad = $row['Plosal'];

			if ($cantidad > 0)
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

	if ($origen == 'on')
	{
		$q = " SELECT Plocod, Plopro, Plocco, Plofcr, Plofve, Plohve, Plocin, Plosal, Ploela, Plocco, Ploest "
		. "       FROM " . $wbasedato . "_000004 "
		. "    WHERE " . $cadena . " "
		. "       Plopro = '" . $parbus . "' "
		. "       AND Plocco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
		. "       AND Ploest = 'on' "
		. "       AND Plosal > 0 "
		. "    Order by 1 asc  ";
	}
	else
	{
		$q = " SELECT Plocod, Plopro, Plocco, Plofcr, Plofve, Plohve, Plocin, Plosal, Ploela, Plocco, Ploest "
		. "       FROM " . $wbasedato . "_000004 "
		. "    WHERE " . $cadena . " "
		. "       Plopro = '" . $parbus . "' "
		. "       AND Plocco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
		. "       AND Ploest = 'on' "
		. "       AND fecha_data > '" . $fecha . "' "
		. "    Order by 1 desc  ";
	}

	$res = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		for ($i = 0;$i < $num;$i++)
		{
			$row = mysql_fetch_array($res);
			$consultas[$inicio] = $row['Plocod'];
			if ($origen == 'on' and $inicio == 0)
			{
				$cantidad = $row['Plosal'];
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

/**
* Se consultan los detalla de un traslado determinado, centro de origen, centro destion , estado y cantidades y articulos traladados
* 
* @param caracter $concepto concepto de traslado
* @param caracter $consecutivo consucutivo del traslado
* @param caracter $fecha fecha de realizacion del traslado
* 
* retorna:
* @param caracter $ccoOri centro de costos de origen (codigo-descripcion)
* @param caracter $ccoDes centro de costos destino (codigo-descripcion)
* @param caracter $estado creado o desactivado
* @param vector $inslis lista de articulos trasladados y cantidad
*/
function consultarTraslado($concepto, $consecutivo, $fecha, &$ccoOri, &$ccoDes, &$estado, &$inslis)
{
	global $conex;
	global $wbasedato;
	global $bd;

	$q = "SELECT Mdeart, Mdecan, Mdepre, Mdenlo, Mencco, Menccd, Menest, Artcom, Artgen, Artuni, Unides "
	. "     FROM   " . $wbasedato . "_000007 A, " . $wbasedato . "_000006, " . $wbasedato . "_000002, ".$bd."_000027 "
	. "   WHERE Mdecon = '" . $concepto . "' "
	. "     AND Mdedoc = '" . $consecutivo . "' "
	. "     AND Mdecon = Mencon "
	. "     AND Mdedoc = Mendoc "
	. "     AND Mdeart = Artcod "
	. "     AND Artest = 'on' "
	. "     AND Unicod = Artuni "
	. "     AND Uniest = 'on' ";

	$res = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		for ($i = 0;$i < $num;$i++)
		{
			$row = mysql_fetch_array($res);
			if ($i == 0)
			{
				$q = "SELECT Conind  "
				. "     FROM    " . $wbasedato . "_000008 "
				. "   WHERE Concod = '" . $concepto . "' "
				. "     AND Conest = 'on' ";

				$res1 = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());
				$row1 = mysql_fetch_array($res1);
				$ind = $row1[0];

				$q = "SELECT Cconom  "
				. "     FROM   ".$bd."_000011 "
				. "   WHERE Ccocod = '" . $row['Mencco'] . "' "
				. "     AND Ccoest = 'on' ";
				$res1 = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());
				$row1 = mysql_fetch_array($res1);

				$ccoOri = $row['Mencco'] . '-' . $row1['Cconom'];

				if ($ind == '-1')
				{
					$cco = $row['Mencco'] . '-' . $row1['Cconom'];
				}

				$q = "SELECT Cconom  "
				. "     FROM   ".$bd."_000011 "
				. "   WHERE Ccocod = '" . $row['Menccd'] . "' "
				. "     AND Ccoest = 'on' ";
				$res1 = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());
				$row1 = mysql_fetch_array($res1);

				$ccoDes = $row['Menccd'] . '-' . $row1['Cconom'];

				if ($ind == '1')
				{
					$cco = $row['Menccd'] . '-' . $row1['Cconom'];
				}

				if ($row['Menest'] == 'on')
				{
					$estado = 'Creado';
				}
				else
				{
					$estado = 'Desactivado';
				}
			}

			$inslis[$i]['cod'] = $row['Mdeart'];
			$inslis[$i]['nom'] = $row['Artcom'];
			$inslis[$i]['gen'] = $row['Artgen'];
			$inslis[$i]['pre'] = $row['Artuni'] . '-' . $row['Unides'];
			$inslis[$i]['can'] = $row['Mdecan'];

			if ($row['Mdenlo'] != '')
			{
				$inslis[$i]['lot'] = 'on';
				$inslis[$i]['est'] = 'on';
				$inslis[$i]['nlo'] = $row['Mdenlo'];
				$inslis[$i]['prese'] = '';
			}
			else
			{
				if ($ind == '-1')
				{
					$q = " SELECT Appcnv, Artcom, Artgen "
					. "        FROM  " . $wbasedato . "_000009, ".$bd."_000026 "
					. "      WHERE Appcod='" . $row['Mdeart'] . "' "
					. "            and Appcco=mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
					. "            and Appest='on' "
					. "            and Apppre=mid('" . $row['Mdepre'] . "',1,instr('" . $row['Mdepre'] . "','-')-1) "
					. "            and Apppre=Artcod ";
				}
				else
				{
					$q = " SELECT Appcnv, Artcom, Artgen "
					. "        FROM  " . $wbasedato . "_000009, ".$bd."_000026 "
					. "      WHERE Appcod='" . $row['Mdeart'] . "' "
					. "            and Appcco=mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
					. "            and Appest='on' "
					. "            and Apppre='" . $row['Mdepre'] . "' "
					. "            and Apppre=Artcod ";
				}

				$res1 = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());
				$num1 = mysql_num_rows($res1);
				$row1 = mysql_fetch_array($res1);

				$inslis[$i]['lot'] = 'off';
				$inslis[$i]['est'] = 'on';
				$inslis[$i]['nlo'] = '';
				$row1['Artcom'] = str_replace('-', ' ', $row1['Artcom']);
				$row1['Artgen'] = str_replace('-', ' ', $row1['Artgen']);
				$inslis[$i]['prese'] = $row['Mdepre'] . '-' . $row1['Artcom'];
			}
		}
	}
}

/**
* Valida que existan las cantidades de los articulos ingresados en el kardex
* 
* @param vector $inslis lista de articulos ingresados
* @param caracter $cco centro de costos de matrix (codigo-descripcion)
* @return numerico $val  un numero de acuerdo a la validacion que no pasa, si pasa es 2
*/
function validarComposicionMatrix(&$inslis, $cco)
{
	global $conex;
	global $wbasedato;

	$val = 2;

	for ($i = 0; $i < count($inslis); $i++)
	{
		$inslis[$i]['est'] = 'on';

		$q = " SELECT * FROM " . $wbasedato . "_000002 where Artcod='" . $inslis[$i]['cod'] . "' and Artest='on' ";
		$res1 = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());
		$num3 = mysql_num_rows($res1);
		if ($num3 > 0)
		{
			if ($inslis[$i]['lot'] != 'on')
			{
				$q = " SELECT Appcnv "
				. "        FROM  " . $wbasedato . "_000009 "
				. "      WHERE Apppre=mid('" . $inslis[$i]['prese'] . "',1,instr('" . $inslis[$i]['prese'] . "','-')-1) "
				. "            and Appcod='" . $inslis[$i]['cod'] . "' "
				. "            and Appcco=mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
				. "            and Appest='on' ";

				$res1 = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());
				$num1 = mysql_num_rows($res1);

				$row1 = mysql_fetch_array($res1);
				$cantidad = $inslis[$i]['can'] * $row1[0];
				$mul = $row1[0];
			}
			else
			{
				$cantidad = $inslis[$i]['can'];
			}

			$q = " SELECT karexi FROM " . $wbasedato . "_000005 where karcod='" . $inslis[$i]['cod'] . "' and Karcco= mid('" . $cco . "',1,instr('" . $cco . "','-')-1) ";
			$res1 = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());
			$num1 = mysql_num_rows($res1);
			if ($num1 > 0)
			{
				$row1 = mysql_fetch_array($res1);
				if ($row1[0] >= $cantidad)
				{
					if ($inslis[$i]['nlo'] != '')
					{
						$q = " SELECT Plosal "
						. "       FROM " . $wbasedato . "_000004 "
						. "    WHERE Plopro = '" . $inslis[$i]['cod'] . "' "
						. "       AND Plocod='" . $inslis[$i]['nlo'] . "' "
						. "       AND Ploest='on' "
						. "       AND Plocco= mid('" . $cco . "',1,instr('" . $cco . "','-')-1) ";
					}
					else
					{
						$q = " SELECT Appexi "
						. "        FROM  " . $wbasedato . "_000009 "
						. "      WHERE Appcod='" . $inslis[$i]['cod'] . "' "
						. "            and Appcco=mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
						. "            and Appest='on' "
						. "            and Apppre=mid('" . $inslis[$i]['prese'] . "',1,instr('" . $inslis[$i]['prese'] . "','-')-1) ";
					}

					$res2 = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());
					$row2 = mysql_fetch_array($res2);
					if ($row2[0] >= $cantidad)
					{
						$inslis[$i]['est'] = 'on';
					}
					else
					{
						$inslis[$i]['est'] = 'off';
						$inslis[$i]['can'] = $row2[0];
						if ($inslis[$i]['nlo'] == '')
						{
							$inslis[$i]['can'] = floor($inslis[$i]['can'] / $mul);
						}
						if ($val > 0)
						{
							$val = 1;
						}
						echo $inslis[$i]['cod'];
					}
				}
				else
				{
					$inslis[$i]['est'] = 'off';
					$inslis[$i]['can'] = $row1[0];
					if ($inslis[$i]['nlo'] == '')
					{
						$q = " SELECT Appexi "
						. "        FROM  " . $wbasedato . "_000009 "
						. "      WHERE Appcod='" . $inslis[$i]['cod'] . "' "
						. "            and Appcco=mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
						. "            and Appest='on' "
						. "            and Apppre=mid('" . $inslis[$i]['prese'] . "',1,instr('" . $inslis[$i]['prese'] . "','-')-1) ";

						$res2 = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());
						$row2 = mysql_fetch_array($res2);
						$inslis[$i]['can'] = floor($row2[0] / $mul);
					}
					if ($val > 0)
					{
						$val = 1;
					}
				}
			}
			else
			{
				$inslis[$i]['est'] = 'off';
				if ($val >= 0)
				{
					$val = 0;
				}
				echo $inslis[$i]['cod'];
			}
		}
		else
		{
			$inslis[$i]['est'] = 'off';
			$val = -1;
			echo $inslis[$i]['cod'];
		}
	}

	return $val;
}

/**
* Convierte un articulo a las unidades de minimo trabajo o unidades matrix
* 
* @param vector $inslis vector de insumos
* @param caracter $cco centro de costos (codigo-descripcion)
* @return numerico $val 2 cuando es encontrado correctamente el articulo, -1 si no es encontrado
*/
function convertirMatrix(&$inslis, $cco)
{
	global $conex;
	global $wbasedato;
	global $bd;

	$val = 2;

	for ($i = 0; $i < count($inslis); $i++)
	{
		$inslis[$i]['est'] = 'on';
		// buscamos el nombre del articulo para el inventario de Matrix, sus unidades y tipo
		$q = " SELECT Appcod, Appcnv, tippro, Artuni, Unides "
		. "        FROM  " . $wbasedato . "_000009, " . $wbasedato . "_000001, " . $wbasedato . "_000002, ".$bd."_000027"
		. "      WHERE Appcco=mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
		. "            and Appest='on' "
		. "            and Apppre='" . $inslis[$i]['cod'] . "' "
		. "            and Artcod=Appcod "
		. "            and Artest='on' "
		. "            and Arttip=Tipcod "
		. "            and Tipest='on' "
		. "            and Artuni=Unicod ";

		$res1 = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());
		$num1 = mysql_num_rows($res1);

		if ($num1 > 0)
		{
			$row1 = mysql_fetch_array($res1);
			$inslis[$i]['can'] = $inslis[$i]['can'] * $row1[1];
			if ($row1[2] == 'on')
			{
				$inslis[$i]['nlo'] = '';
				$inslis[$i]['prese'] = '';
				$inslis[$i]['est'] = 'on';
			}
			else
			{
				$inslis[$i]['nlo'] = '';
				$inslis[$i]['prese'] = $row1[0];
				$inslis[$i]['est'] = 'on';
				$inslis[$i]['pre'] = $row1[3] . '-' . $row1[4];
			}
		}
		else
		{
			// buscamos el articulo directamente en la 02 sin convercion
			$q = " SELECT Artcod, tippro "
			. "        FROM   " . $wbasedato . "_000001, " . $wbasedato . "_000002  "
			. "      WHERE Artest='on' "
			. "        and Artcod='" . $inslis[$i]['cod'] . "' "
			. "        and Arttip=Tipcod "
			. "        and Tipest='on' ";

			$res3 = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());
			$num3 = mysql_num_rows($res3);
			if ($num3 > 0)
			{
				$inslis[$i]['prese'] = '';
				$inslis[$i]['est'] = 'on';
			}
			else
			{
				$inslis[$i]['est'] = 'off';
				$val = -1;
			}
		}
	}
	return $val;
}

/**
* Cambia las cantidades de la unidad minima a la unidad utilizada en unix, al consultar un traslado
* 
* @param vector $inslis lista de articulos ingresados
* @param caracter $cco centro de costos de matrix (codigo-descripcion)
*/
function cambiarCantidades1(&$inslis, $cco)
{
	global $conex;
	global $wbasedato;
	global $bd;

	for ($i = 0; $i < count($inslis); $i++)
	{
		if ($inslis[$i]['lot'] != 'on')
		{
			$q = " SELECT Appcnv "
			. "        FROM  " . $wbasedato . "_000009 "
			. "      WHERE Apppre=mid('" . $inslis[$i]['prese'] . "',1,instr('" . $inslis[$i]['prese'] . "','-')-1) "
			. "            and Appcod='" . $inslis[$i]['cod'] . "' "
			. "            and Appcco=mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
			. "            and Appest='on' ";

			$res1 = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());
			$num1 = mysql_num_rows($res1);

			if ($num1 > 0)
			{
				$row1 = mysql_fetch_array($res1);
				$inslis[$i]['can'] = $inslis[$i]['can'] / $row1[0];

				$q = " SELECT Artuni, Unides FROM ".$bd."_000026, ".$bd."_000027 "
				. " where Artcod=mid('" . $inslis[$i]['prese'] . "',1,instr('" . $inslis[$i]['prese'] . "','-')-1) "
				. " and Artuni=Unicod "
				. " and Artest='on' ";

				$res1 = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());
				$row1 = mysql_fetch_array($res1);
				$inslis[$i]['pre'] = $row1[0] . '-' . $row1[1];
			}
		}
	}
}

/**
* Cambia las cantidades de la unidad minima a la unidad utilizada en unix, al grabar un traslado
* 
* @param vector $inslis lista de articulos ingresados
* @param caracter $cco centro de costos de matrix (codigo-descripcion)
*/
function cambiarCantidades2(&$inslis, $cco)
{
	global $conex;
	global $wbasedato;
	global $bd;

	for ($i = 0; $i < count($inslis); $i++)
	{
		if (!empty( $inslis[0] ) && $inslis[$i]['lot'] != 'on')
		{
			$q = " SELECT Appcnv "
			. "        FROM  " . $wbasedato . "_000009 "
			. "      WHERE Apppre='" . $inslis[$i]['cod'] . "' "
			. "            and Appcod='" . $inslis[$i]['prese'] . "' "
			. "            and Appcco=mid('" . $cco . "',1,instr('" . $cco . "','-')-1) "
			. "            and Appest='on' ";

			$res1 = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());
			$num1 = mysql_num_rows($res1);

			if ($num1 > 0)
			{
				$row1 = mysql_fetch_array($res1);
				$inslis[$i]['can'] = $inslis[$i]['can'] / $row1[0];

				$q = " SELECT Artuni, Unides FROM ".$bd."_000026, ".$bd."_000027 "
				. " where Artcod='" . $inslis[$i]['cod'] . "' "
				. " and Artuni=Unicod "
				. " and Artest='on' ";

				$res1 = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());
				$row1 = mysql_fetch_array($res1);
				$inslis[$i]['pre'] = $row1[0] . '-' . $row1[1];
			}
		}
	}
}

/**
* graba el encabezado del movmineto de inventario
* 
* retorna:
* 
* @param caracter $codigo concepto de inventario
* @param numerico $consecutivo consecutivo para el concepto
* 
* recibe
* @param caracter $cco centro de costos de origen
* @param caracter $cco2 centro de costos destino
* @param caracter $usuario usuario que realiza el traslado
*/
function grabarEncabezadoSalidaMatrix(&$codigo, &$consecutivo, $cco, $usuario, $cco2, $documento2)
{
	global $conex;
	global $wbasedato;

	$q = "lock table " . $wbasedato . "_000008 LOW_PRIORITY WRITE";
	$errlock = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());

	$q = "   UPDATE " . $wbasedato . "_000008 "
	. "      SET Concon = (Concon + 1) "
	. "    WHERE Conind = '-1' "
	. "      AND Contra = 'on' "
	. "      AND Conest = 'on' ";

	$res1 = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());

	$q = "   SELECT Concon, Concod from " . $wbasedato . "_000008 "
	. "    WHERE Conind = '-1'"
	. "      AND Contra = 'on' "
	. "      AND Conest = 'on' ";

	$res1 = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());
	$row2 = mysql_fetch_array($res1);
	$codigo = $row2[1];
	$consecutivo = $row2[0];

	$q = " UNLOCK TABLES"; //SE DESBLOQUEA LA TABLA DE FUENTES
	$errunlock = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());

	$q = " INSERT INTO " . $wbasedato . "_000006 (   Medico       ,   Fecha_data,                  Hora_data,              Menano,              Menmes ,     Mendoc   ,   Mencon  ,             Menfec,           Mencco ,   Menccd    ,  Mendan,  Menusu,    Menfac,  Menest, Seguridad) "
	. "                               VALUES ('" . $wbasedato . "',  '" . date('Y-m-d') . "', '" . (string)date("H:i:s") . "', '" . date('Y') . "', '" . date('m') . "','" . $row2[0] . "', '" . $row2[1] . "' , '" . date('Y-m-d') . "', mid('" . $cco . "',1,instr('" . $cco . "','-')-1) , mid('" . $cco2 . "',1,instr('" . $cco2 . "','-')-1) ,       '" . $documento2 . "', '" . $usuario . "',      '' , 'on', 'C-" . $usuario . "') ";

	$err = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO GRABAR EL ENCABEZADO DEL MOVIIENTO DE SALIDA DE INSUMOS " . mysql_error());
}

/**
* Graba el movmiento de salida de matrix
* 
* @param caracter $codpro codigo del insumo
* @param numerico $inscan cantidad del insumo
* @param caracter $codigo concepto de inventario
* @param numerico $consecutivo consecutivo del concepto
* @param caracter $usuario codigo del usuario que realiza el movmiento
* @param caracter $lote numero de lote, si es producto
* @param caracter $prese presentacion si en arituclo
*/
function grabarDetalleSalidaMatrix($inscod, $inscan, $codigo, $consecutivo, $usuario, $lote, $prese)
{
	global $conex;
	global $wbasedato;

	if ($lote != '')
	{
		$q = " INSERT INTO " . $wbasedato . "_000007 (   Medico       ,   Fecha_data,                  Hora_data,              Mdecon,              Mdedoc ,     Mdeart   ,             Mdecan ,      Mdefve, Mdenlo, Mdepre,  Mdeest,  Seguridad) "
		. "                               VALUES ('" . $wbasedato . "',  '" . date('Y-m-d') . "', '" . (string)date("H:i:s") . "', '" . $codigo . "', '" . $consecutivo . "','" . $inscod . "', '" . $inscan . "' , '',     '" . $lote . "-" . $inscod . "', '',  'on', 'C-" . $usuario . "') ";
	}
	else
	{
		$q = " INSERT INTO " . $wbasedato . "_000007 (   Medico       ,   Fecha_data,                  Hora_data,              Mdecon,              Mdedoc ,     Mdeart   ,             Mdecan ,      Mdefve, Mdenlo, Mdepre, Mdeest,  Seguridad) "
		. "                               VALUES ('" . $wbasedato . "',  '" . date('Y-m-d') . "', '" . (string)date("H:i:s") . "', '" . $codigo . "', '" . $consecutivo . "','" . $inscod . "', '" . $inscan . "' , '',     '',  '" . $prese . "' , 'on', 'C-" . $usuario . "') ";
	}

	$err = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO GRABAR EL DETALLE DE SALIDA DE UN ARTICULO " . mysql_error());
}

/**
* Descuenta un insumo de matrix por efecto del traslado
* 
* @param caracter $inscod codigo del insumo
* @param numerico $inscan cantidad del insumo
* @param caracter $cco centro de costos (codigo-descripcion)
* @param caractere $lote numero de lote
* @param caracter $prese codigo de la presentacion del insumo
*/
function descontarInsumoMatrix($inscod, $inscan, $cco, $lote, $prese)
{
	global $conex;
	global $wbasedato;

	global $conex;
	global $wbasedato;

	$q = "   UPDATE " . $wbasedato . "_000005 "
	. "      SET karexi = karexi - " . $inscan . " "
	. "    WHERE Karcod = '" . $inscod . "' "
	. "      AND karcco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) ";

	$res1 = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO DESCONTAR UN INSUMO " . mysql_error());

	if ($lote != '')
	{
		$q = "   UPDATE " . $wbasedato . "_000004 "
		. "      SET Plosal = Plosal-" . $inscan . ""
		. "    WHERE Plocod =  '" . $lote . "' "
		. "      AND Plopro ='" . $inscod . "' "
		. "      AND Ploest ='on' "
		. "      AND Plocco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) ";
	}

	if ($prese != '')
	{
		$q = "   UPDATE " . $wbasedato . "_000009 "
		. "      SET Appexi = Appexi-" . $inscan . ""
		. "    WHERE Apppre =  mid('" . $prese . "',1,instr('" . $prese . "','-')-1) "
		. "      AND Appcod ='" . $inscod . "' "
		. "      AND Appest ='on' "
		. "      AND Appcco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) ";
	}

	$res1 = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO DESCONTAR UN INSUMO " . mysql_error());
}

/**
* Suma un insumo en matrix por efecto del traslado
* 
* @param caracter $inscod codigo del insumo
* @param numerico $inscan cantidad del insumo
* @param caracter $cco centro de costos (codigo-descripcion)
* @param caractere $lote numero de lote
* @param caracter $prese codigo de la presentacion del insumo
*/
function sumarInsumoMatrix($inscod, $inscan, $cco, $lote, $prese)
{
	global $conex;
	global $wbasedato;

	$q = "   UPDATE " . $wbasedato . "_000005 "
	. "      SET karexi = karexi + " . $inscan . " "
	. "    WHERE Karcod = '" . $prese . "' "
	. "      AND karcco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) ";

	$res1 = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO SUMAR UN INSUMO " . mysql_error());

	if ($prese != '')
	{
		$q = "   UPDATE " . $wbasedato . "_000009 "
		. "      SET Appexi = Appexi+" . $inscan . ""
		. "    WHERE Apppre =  '" . $inscod . "' "
		. "      AND Appcod ='" . $prese . "' "
		. "      AND Appest ='on' "
		. "      AND Appcco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) ";
	}

	if ($lote != '')
	{
		$q = "   UPDATE " . $wbasedato . "_000004 "
		. "      SET Plosal = Plosal+" . $inscan . ""
		. "    WHERE Plocod =  '" . $lote . "' "
		. "      AND Plopro ='" . $inscod . "' "
		. "      AND Ploest ='on' "
		. "      AND Plocco = mid('" . $cco . "',1,instr('" . $cco . "','-')-1) ";
	}

	$res1 = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO SUMAR UN INSUMO " . mysql_error());
}

/**
* graba el encabezado del movmineto de inventario
* 
* retorna:
* 
* @param caracter $codigo concepto de inventario
* @param numerico $consecutivo consecutivo para el concepto
* 
* recibe
* @param caracter $cco centro de costos de origen
* @param caracter $cco2 centro de costos destino
* @param caracter $usuario usuario que realiza el traslado
*/
function grabarEncabezadoEntradaMatrix(&$codigo, &$consecutivo, $cco, $cco2, $usuario, $documento2)
{
	global $conex;
	global $wbasedato;

	$q = "lock table " . $wbasedato . "_000008 LOW_PRIORITY WRITE";
	$errlock = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());

	$q = "   UPDATE " . $wbasedato . "_000008 "
	. "      SET Concon = (Concon + 1) "
	. "    WHERE Conind = '1'"
	. "      AND Contra = 'on' "
	. "      AND Conest = 'on' ";

	$res1 = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());

	$q = "   SELECT Concon, Concod from " . $wbasedato . "_000008 "
	. "    WHERE Conind = '1'"
	. "      AND Contra = 'on' "
	. "      AND Conest = 'on' ";

	$res1 = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());
	$row2 = mysql_fetch_array($res1);
	$codigo = $row2[1];
	$consecutivo = $row2[0];

	$q = " UNLOCK TABLES"; //SE DESBLOQUEA LA TABLA DE FUENTES
	$errunlock = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());

	$q = " INSERT INTO " . $wbasedato . "_000006 (   Medico       ,   Fecha_data,                  Hora_data,              Menano,              Menmes ,     Mendoc   ,   Mencon  ,             Menfec,           Mencco ,   Menccd    ,  Mendan,  Menusu,    Menfac,  Menest, Seguridad) "
	. "                               VALUES ('" . $wbasedato . "',  '" . date('Y-m-d') . "', '" . (string)date("H:i:s") . "', '" . date('Y') . "', '" . date('m') . "','" . $row2[0] . "', '" . $row2[1] . "' , '" . date('Y-m-d') . "', mid('" . $cco2 . "',1,instr('" . $cco2 . "','-')-1) , mid('" . $cco . "',1,instr('" . $cco . "','-')-1) ,  '" . $documento2 . "', '" . $usuario . "',      '' , 'on', 'C-" . $usuario . "') ";

	$err = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO GRABAR EL ENCABEZADO DEL MOVIIENTO DE SALIDA DE INSUMOS " . mysql_error());
}

/**
* graba el detalle del movmiento de inventario
* 
* @param caracter $codpro codigo del insumo
* @param numerico $inscan cantidad del insumo
* @param caracter $codigo concepto de inventario
* @param numerico $consecutivo consecutivo del concepto
* @param caracter $usuario codigo del usuario que realiza el movmiento
* @param caracter $lote numero de lote, si es producto
* @param caracter $prese presentacion si en arituclo
*/
function grabarDetalleEntradaMatrix($codpro, $inscan, $codigo, $consecutivo, $usuario, $lote, $prese)
{
	global $conex;
	global $wbasedato;

	if ($prese == '')
	{
		$prese = $codpro;
	}

	if ($lote != '')
	{
		$q = " INSERT INTO " . $wbasedato . "_000007 (   Medico       ,            Fecha_data,                  Hora_data,        Mdecon,           Mdedoc ,                                     Mdeart   ,        Mdecan , Mdefve,                   Mdenlo, Mdepre,  Mdeest,  Seguridad) "
		. "                               VALUES ('" . $wbasedato . "',  '" . date('Y-m-d') . "', '" . (string)date("H:i:s") . "', '" . $codigo . "', '" . $consecutivo . "','" . $prese . "', '" . $inscan . "' ,     '',  '" . $lote . "-" . $codpro . "',   '" . $codpro . "', 'on', 'C-" . $usuario . "') ";
	}
	else
	{
		$q = " INSERT INTO " . $wbasedato . "_000007 (   Medico       ,   Fecha_data,                  Hora_data,              Mdecon,              Mdedoc ,     Mdeart   ,                                        Mdecan , Mdefve,   Mdenlo,          Mdepre, Mdeest,  Seguridad) "
		. "                               VALUES ('" . $wbasedato . "',  '" . date('Y-m-d') . "', '" . (string)date("H:i:s") . "', '" . $codigo . "', '" . $consecutivo . "','" . $prese . "', '" . $inscan . "' ,      '',     '',  '" . $codpro . "' , 'on', 'C-" . $usuario . "') ";
	}

	$err = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO GRABAR EL DETALLE DE SALIDA DE UN ARTICULO " . mysql_error());
}
// ----------------------------------------------------------funciones del modelo---------------------------
/**
* Elimina un insumo del vector de insumos a trasladar
* 
* @param vector $lista 
* @param numero $index posicion a trasladar
* @return vector $lista el mismo vector con el insumo eliminado de la lista
*/
function eliminarInsumo($lista, $index)
{
	if (count($lista) > 1)
	{
		unset($lista[$index]);
		$lista = array_reverse(array_reverse($lista));
	}
	else
	{
		$lista = false;
	}
	return $lista;
}
// ----------------------------------------------------------funciones de presentacion------------------------------------------------
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
	echo "<table ALIGN=CENTER width='50%'>";
	// echo "<tr><td align=center colspan=1 ><img src='/matrix/images/medical/general/logo_promo.gif' height='100' width='250' ></td></tr>";
	echo "<tr><td class='titulo1'>TRASLADOS DE CENTRAL DE MEZCLAS</td></tr>";
	echo "<tr><td class='titulo2'>Fecha: " . date('Y-m-d') . "&nbsp Hora: " . (string)date("H:i:s") . "</td></tr></table></br>";

	/**
    * if ($pintar==0)
    * {
    * echo "<table ALIGN=CENTER width='90%' >";
    * //echo "<tr><td align=center colspan=1 ><img src='/matrix/images/medical/general/logo_promo.gif' height='100' width='250' ></td></tr>";
    * echo "<tr><a href='cen_Mez.php?wbasedato=cen_mez'><td class='texto5' width='15%'>PRODUCTOS</td></a>";
    * echo "<a href='lotes.php?wbasedato=lotes.php'><td class='texto5' width='15%'>LOTES</td></a>";
    * echo "<a href='traslados.php?wbasedato=lotes.php><td class='texto6' width='15%'>TRASLADOS</td></a>";
    * echo "<a href='cargos.php?wbasedato=lotes.php&tipo=C'><td class='texto5' width='15%'>CARGOS</td></a>";
    * echo "<a href='cargos.php?wbasedato=lotes.php&tipo=A'><td class='texto5' width='15%'>AVERIAS</td></a>";
    * echo "<a href='ensayo.php?wbasedato=ensayo.php&tipo=A'><td class='texto5' width='15%'>POS</td></a></TR>";
    * //echo "<a href='consulta.php?para=enviados'><td class='texto5' width='20%'>LISTADO POR PRODUCIR</td></a>";
    * //echo "<a href='enviado.php'><td class='texto5' width='20%'>LISTADO PRODUCIDO</td></tr></a>";
    * echo "<tr><td class='texto6' >&nbsp;</td>";
    * echo "<td class='texto6' >&nbsp;</td>";
    * echo "<td class='texto6' >&nbsp;</td>";
    * echo "<td class='texto6' >&nbsp;</td>";
    * echo "<td class='texto6' >&nbsp;</td>";
    * echo "<td class='texto6' >&nbsp;</td></tr></table>";
    * }
    */
}

/**
* se pinta el formulario que permite buscar algun tralsado por un parametro de busqueda
* 
* @param vector $consultas lista de traslados encontrados bajo un parametro de busqueda, se despliegan en un select
* @param caracter $forcon , forma de busqueda escogida por un usuario para los traslados, se despliega de priemra en select de
*                           forma de busqueda
*/
function pintarBusqueda($consultas, $forcon, $wemp_pmla)
{
	echo "<table border=0 ALIGN=CENTER width=90%>";
	echo "<form name='producto2' action='traslados.php?wemp_pmla=".$wemp_pmla."' method=post>";
	echo "<tr><td class='titulo3' colspan='3' align='center'>Consulta de Traslado: ";
	echo "<select name='forcon' class='texto5' onchange='enter7()'>";
	echo "<option>" . $forcon . "</option>";
	if ($forcon != 'Numero de traslado')
	echo "<option>Numero de traslado</option>";
	if ($forcon != 'Centro de costos de origen')
	echo "<option>Centro de costos de origen</option>";
	if ($forcon != 'Centro de costos destino')
	echo "<option>Centro de costos destino</option>";
	if ($forcon != 'Articulo')
	echo "<option>Articulo</option>";
	echo "</select>";

	switch ($forcon)
	{
		case '':
		echo "&nbsp";
		break;

		case 'Numero de traslado':
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
	if ( !empty( $consultas[0] ) && $consultas[0]['cod'] != '')
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
	echo "</form>";
}

/**
* se pintan el formulario con los datos generales del traslado:
* 
* @param caracter $estado estado del traslado, para indicar si el traslado no se ha creado, esta activo o fue desactivado
* @param vector $origenes vector con la lista de centros de costos de origen para desplegar en select
* @param vector $destinos vector con la lista de centros de costos destino para desplegar en select
* @param caracter $numtra numero del traslado (concepto-consecutivo)
* @param date $fecha fecha del traslado
*/
function pintarFormulario($estado, $origenes, $destinos, $numtra, $fecha, $wemp_pmla)
{
	echo "<form name='producto3' action='traslados.php?wemp_pmla=".$wemp_pmla."' method=post>";
	echo "<tr><td colspan=3 class='titulo3' align='center'><INPUT TYPE='submit' NAME='NUEVO' VALUE='Nuevo' class='texto5' ></td></tr>";
	echo "</table></form>";

	echo "<form name='producto4' action='traslados.php?wemp_pmla=".$wemp_pmla."' method=post onSubmit='return enviado();'>";
	echo "<table border=0 ALIGN=CENTER width=90%>";
	echo "<tr><td colspan=3 class='titulo3' align='center'><b>Informacion general del traslado</b></td></tr>";
	echo "<tr><td class='texto1' colspan='1' align='center'>Centro de costos de origen: ";
	echo "<select name='ccoOri' class='texto5' onchange='enter8()'>";
	if ($origenes[0] != '')
	{
		for ($i = 0;$i < count($origenes);$i++)
		{
			echo "<option >" . $origenes[$i] . "</option>";
		}
	}
	else
	{
		echo "<option value=''></option>";
	}
	echo "</select>";
	echo "</td>";

	echo "<td class='texto1' colspan='2' align='center'>Centro de costos de destino: ";
	echo "<select name='ccoDes' class='texto5' onchange='enter8()'>";
	if ($destinos[0] != '')
	{
		for ($i = 0;$i < count($destinos);$i++)
		{
			echo "<option >" . $destinos[$i] . "</option>";
		}
	}
	else
	{
		echo "<option value=''></option>";
	}
	echo "</select>";
	echo "</td></tr>";
	echo "</form>";

	echo "<form name='producto' action='traslados.php?wemp_pmla=".$wemp_pmla."' method=post  onSubmit='return enviado();'>";
	echo "<tr><td class='texto2' colspan='1' align='right'>Numero de Movimiento: <input type='TEXT' name='numtra' value='" . $numtra . "' readonly='readonly' class='texto2' size='10'></td>";
	echo "<td class='texto2' colspan='2' align='left'>Fecha: <input type='TEXT' name='fecha' value='" . $fecha . "' readonly='readonly' class='texto2' size='10'></td></tr>";

	switch ($estado)
	{
		case 'inicio':
		echo "<tr><td colspan=3 class='titulo3' align='center'><input type='checkbox' name='crear' class='titulo3' id='crear'>Trasladar &nbsp;<INPUT TYPE='submit' NAME='buscar' VALUE='Aceptar' id='btnAceptar' class='texto5'></td></tr>";
		break;
		case 'creado':
		echo "<tr><td colspan=3 class='titulo3' align='center'>SE HA REALIZADO EL TRASLADADO  EXITOSAMENTE</td></tr>";
		break;
		case 'Desactivado':
		echo "<tr><td colspan=3 class='titulo3' align='center'>EL TRASLADO HA SIDO DESACTIVADO</td></tr>";
		break;
		case 'Creado':
		echo "<tr><td colspan=3 class='titulo3' align='center'>TRASLADO ACTIVO</td></tr>";
		break;
	}
	echo "<input type='hidden' name='estado' value='" . $estado . "'></td>";
	echo "<input type='hidden' name='ccoOri' value='" . $origenes[0] . "'></td>";
	echo "<input type='hidden' name='ccoDes' value='" . $destinos[0] . "'></td>";
	echo "<input type='hidden' name='tfh' value='0'></td>";
	echo "<input type='hidden' name='tvh' value='0'></td>";
	echo "</table></br>";
}

function centroCostosCM()
	{
		global $conex;
		global $bd;
		
		$sql = "SELECT
					Ccocod
				FROM
					".$bd."_000011
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

/**
* Se pinta el formulario que permite buscar los articulos a trasladar, seccionar lotes o presentaciones
* ingresar la cantidad y desplegar la lista de articulos ingresados
* 
* @param vector $insumos lista de articulos encontrados bajo un parametro de busqueda
* @param vector $inslis lista de articulos que han sido ingresados para el traslado
* @param vector $unidades lista de presentaciones para un insumo que va a ser ingresado
* @param vector $lotes lista de lotes de un producto que va a ser agregado
*/
function pintarInsumos($insumos, $inslis, $unidades, $lotes, $cantidad)
{
	echo "<table border=0 ALIGN=CENTER width=90%>";
	echo "<tr><td colspan='5' class='titulo3' align='center'><b>Lista de articulos</b></td></tr>";

	echo "<tr><td class='texto1' colspan='5' align='center'>Buscar Articulo por: ";
	echo "<select name='forbus2' class='texto5'>";
	echo "<option>rotulo</option>";
	echo "<option>Codigo</option>";
	echo "<option>Nombre comercial</option>";
	echo "<option>Nombre generico</option>";
	echo "</select><input type='TEXT' name='parbus2' value='' size=10 class='texto5'>&nbsp;<INPUT TYPE='button' NAME='buscar' VALUE='Buscar' onclick='enter1()' class='texto5'></td> ";
	echo "<tr><td class='texto1' colspan='3' align='center'>Articulo: <select name='insumo' class='texto5' onchange='enter6()'>";
	if ($insumos != '')
	{
		for ($i = 0;$i < count($insumos);$i++)
		{
			echo "<option value='" . $insumos[$i]['cod'] . "-" . $insumos[$i]['nom'] . "-" . $insumos[$i]['gen'] . "-" . $insumos[$i]['pre'] . "-" . $insumos[$i]['lot'] . "-" . $insumos[$i]['est'] . "'>" . $insumos[$i]['cod'] . "-" . $insumos[$i]['nom'] . "</option>";
		}
	}
	else
	{
		echo "<option ></option>";
	}
	echo "</select></td>";
	$insuon = $insumos[0]['lot'];
	if ($insuon == 'on')
	{
		echo "<td class='texto1' colspan='2' align='center'>Lote: <select name='lote' class='texto5' onchange='enter6()'>";
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
	if (is_array($unidades))
	{
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

	if (!isset($ind))
	{
		echo "<td class='texto1' colspan='2' align='center'>&nbsp;</td></tr>";
	}
	echo "<tr><td class='texto1' colspan='5' align='center'>Cantidad: <input type='TEXT' name='cantidad' value='" . $cantidad . "'  class='texto5' onchange='validarFormulario()'><input type='TEXT' name='nompro' value='" . $insumos[0]['pre'] . "'  class='texto5' >";
	echo "</td>";
	echo "<tr><td colspan='5' class='texto1' align='center'><INPUT TYPE='button' NAME='buscar' VALUE='Agregar' onclick='enter()' class='texto5'></td></tr>";

	echo "<tr><td colspan='5' class='titulo3' align='center'>&nbsp</td></tr>";

	if ($inslis != '')
	{
		echo "<tr><td class='texto2' colspan='1' align='center'>Nro. Item</td>";
		echo "<td class='texto2' colspan='1' align='center'>Articulo</td>";
		echo "<td class='texto2' colspan='1' align='center'>Detalle</td>";
		echo "<td class='texto2' colspan='1' align='center'>Cantidad</td>";
		echo "<td class='texto2' colspan='1' align='center'>Eliminar</td></tr>";

		// for ($i = 0;$i < count($inslis);$i++)
		for ($i = count($inslis)-1;$i >=0 ;$i--)
		{
			if (is_int($i / 2))
			{
				$class = 'texto3';
			}
			else
			{
				$class = 'texto4';
			}

			echo "<tr>";
			echo "<td class='" . $class . "' colspan='1' align='center'>" . ($i+1). "</td>";
			echo "<td class='" . $class . "' colspan='1' align='center'>" . $inslis[$i]['cod'] . "-" . $inslis[$i]['nom'] . "<input type='hidden' name='inslis[" . $i . "][cod]' value='" . $inslis[$i]['cod'] . "'><input type='hidden' name='inslis[" . $i . "][nom]' value='" . $inslis[$i]['nom'] . "'><input type='hidden' name='inslis[" . $i . "][gen]' value=" . $inslis[$i]['gen'] . "></td>";
			echo "<input type='hidden' name='inslis[" . $i . "][pri]'  value='checked'>";
			echo "<input type='hidden' name='inslis[" . $i . "][nlo]'  value='" . $inslis[$i]['nlo'] . "'>";
			echo "<input type='hidden' name='inslis[" . $i . "][prese]'  value='" . $inslis[$i]['prese'] . "'>";

			if ($inslis[$i]['nlo'] != '')
			{
				echo "<td class='" . $class . "' colspan='1' align='center'>Lote: " . $inslis[$i]['nlo'] . "</td>";
			}
			else if ($inslis[$i]['prese'] != '')
			{
				echo "<td class='" . $class . "' colspan='1' align='center'>Presentacion: " . $inslis[$i]['prese'] . "</td>";
			}
			else
			{
				echo "<td class='" . $class . "' colspan='1' align='center'>&nbsp;</td>";
			}

			if ($inslis[$i]['est'] == 'on')
			{
				echo "<td class='" . $class . "' colspan='1' align='center'><input type='TEXT' size='10' readonly='readonly' name='inslis[" . $i . "][can]' value='" . $inslis[$i]['can'] . "'  class='texto3'><input type='TEXT' name='inslis[" . $i . "][pre]' value='" . $inslis[$i]['pre'] . "'  class='texto3'></td>";
			}
			else
			{
				echo "<td bgcolor='red' colspan='1' align='center'><input type='TEXT' size='10'  name='inslis[" . $i . "][can]' value='" . $inslis[$i]['can'] . "'  class='texto3'><input type='TEXT' name='inslis[" . $i . "][pre]' value='" . $inslis[$i]['pre'] . "'  class='texto3'></td>";
			}

			echo "<td class='" . $class . "' colspan='1' align='center'><input type='checkbox' name='eli' class='texto3' onclick='enter2(" . $i . ")'></td></tr>";

			echo "<input type='hidden' name='inslis[" . $i . "][lot]' value='" . $inslis[$i]['lot'] . "'>";
			echo "<input type='hidden' name='inslis[" . $i . "][est]' value='" . $inslis[$i]['est'] . "'>";
			
		}
	}

	echo "<input type='hidden' name='accion' value='0'></td>";
	echo "<input type='hidden' name='realizar' value='0'></td>";
	echo "<input type='hidden' name='eliminar' value='0'></td>";
	echo "</table></br></form>";
}

/**
* ===========================================================================================================================================
*/
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
	//$wbasedato = 'cenpro';
	
	include_once( "conex.php" );
//    $conex = obtenerConexionBD("matrix");
    

    
//	$conex = mysql_connect('localhost', 'root', '')
//	or die("No se ralizo Conexion");
	
	// pintarVersion(); //Escribe en el programa el autor y la version del Script.
	pintarTitulo(); //Escribe el titulo de la aplicacion, fecha y hora adicionalmente da el acceso a otros scripts

	//$bd = 'movhos';
	// invoco la funcion connectOdbc del inlcude de ana, para saber si unix responde, en caso contrario,
	// este programa no debe usarse
	// include_once("pda/tablas.php");
	include_once("movhos/fxValidacionArticulo.php");
	include_once("movhos/registro_tablas.php");
	include_once("movhos/otros.php");
	include_once("cenpro/funciones.php");
	include_once("root/barcod.php");

	include_once("root/comun.php");
	$wbasedato = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'cenmez' );
	$bd  = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

	connectOdbc($conex_o, 'inventarios');
	//$test = centroCostosCM();echo $test;
	if ($conex_o != 0)
	{
		// consulto los datos del usuario de la sesion
		$pos = strpos($user, "-");
		$wusuario = substr($user, $pos + 1, strlen($user)); //extraigo el codigo del usuario
		/**
        * $origenes[0]='1050-SERVICIO FARMACEUTICO';
        * $destinos[0]='1051-CENTRAL DE MEZCLAS';
        * $ccoDes=$destinos[0];
        * $ccoOri=$origenes[0];
        */

		if (isset($ccoOri)) // si ya se habia seleccionado un centro de costos de origen se manda para que quede de primero
		{
			$origenes = consultarCentros($ccoOri, $wusuario, 'on', $wemp_pmla);
		}
		else
		{
			$origenes = consultarCentros('', $wusuario, 'on', $wemp_pmla);
		}

		$ccoOri = $origenes[0];
		if (isset($ccoDes)) // si ya se habia seleccionado un centro de costos de destino se manda para que quede de primero
		{
			$destinos = consultarCentros($ccoDes, $wusuario, 'off', $wemp_pmla);
		}
		else
		{
			$destinos = consultarCentros('', $wusuario, 'off', $wemp_pmla);
		}
		$ccoDes = $destinos[0];
		// si ya se han seleccionado ambos centros de costos: origen y destino, valido que se pueda hacer traslados entre ellos
		// para ello uno debe tener inventario en unix y el otro en matrix(ima en on en movhos_000011)
		if (isset($ccoOri) and $ccoOri != '' and isset($ccoDes) and $ccoDes != '')
		{
			$exc=explode('-',$ccoOri);
			$cco = centroCostosCM();
			if ($exc[0] == $cco)
			{
				echo "<font size=12 color=#FFFFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#009999 LOOP=-1>TRASLADOS DESDE CENTRAL DE MEZCLAS</MARQUEE></FONT></br></br>";
			}

			$origen = consultarCCo($origenes[0]);
			$destino = consultarCCo($destinos[0]);
			
			// si ambos tienen inventario en unix o matrix, el traslado no esta autorizado
			if ($origen == $destino)
			{
				pintarAlert1('No esta permitido el traslado entre estos centros de costo');
				$numtra = '';
			}
			else // si esta autorizado se consulta el consecutivo dependiendo si es una entrada o salida del inventario de matrix
			{
				if ($origen == 'on')
				{
					$numtra = consultarConsecutivo(-1);
				}
				else
				{
					$numtra = consultarConsecutivo(1);
				}
			}
		} //si no se han seleccionado los centros de costo se incializa el numero del traslado en vacio
		else
		{
			$numtra = '';
		}

		if (!isset($estado)) // el estado del movimiento esta en incio, no se ha relaizado aun el traslado
		{
			$estado = 'inicio';
		}
		// se ha seleccionado el check box para efectuar el traslado
		if (isset($crear))
		{
			// debe existir un numero de traslado y por ende, debe haberse permitido el traslado entre los cco
			if (isset($numtra) and $numtra != '')
			{
				if (!isset($inslis)) // debe haberse ingresado al menos un articulo
				{
					pintarAlert1('Debe ingresar al menos un articulo para realizar el traslado');
				}
				else
				{
					// para cada articulo ingresado
					for ($i = 0; $i < count($inslis); $i++)
					{
						// si el origne es matrix y no es producto, el codigo del articulo es el de la presentacion
						if ($origen == 'on' and $inslis[$i]['lot'] != 'on')
						{
							$exp = explode('-', $inslis[$i]['prese']);
							$art['cod'] = $exp[0];
						}
						else
						{
							$art['cod'] = $inslis[$i]['cod'];
						}

						$art['can'] = $inslis[$i]['can'];
						$exp = explode('-', $ccoOri);
						$centro['cod'] = $exp[0];
						$exp = explode('-', $ccoDes);
						$centro2['cod'] = $exp[0];
						$tipTrans = 'C'; //el traslado se mandara C a la funcion
						$art['neg'] = false; //no maneja articulos especiales
						$aprov = false; //no son aprovechamientos
						// si el origen es matrix, no importa que no hay saldo en unix
						if ($origen != 'on')
						{
							$centro['neg'] = false;
						}
						else
						{
							$centro['neg'] = true;
						}

						$centro2['neg'] = true;
						// funcion de include fxvalidarArticulo, que valida si el articulo existe en unix
						$res = ArticuloExiste ($art, $error);
						if ($res) // si pasa validacion
						{
							// funcion de include fxvalidarArticulo, que valida si el articulo existe en unix en centro de costos
							// si el origen no es matrix ($centro['neg']=false), validara que tenga saldo
							$res2 = TarifaSaldo($art, $centro, $tipTrans, $aprov, $error);
							if (!$res2)
							{
								$val = false;
								$inslis[$i]['est'] = 'off';
							}
							else
							{
								$res3 = TarifaSaldo($art, $centro2, $tipTrans, $aprov, $error);
								if (!$res3)
								{
									$val = false;
									$inslis[$i]['est'] = 'off';
								}
								else
								{
									if (!isset($val) or $val != false)
									$val = true;
								}
							}
						}
						else
						{
							$inslis[$i]['est'] = 'off';
							$val = false;
						}
					}

					if ($val) // si pasa las validaciones de unix
					{
						if ($origen == 'on') // si el origen es matrix, se valida que existan las cantidades en matrix
						{
							$val = validarComposicionMatrix($inslis, $ccoOri);
							if ($val >= 0) // si val es -1 no estaba el codigo en el maestor
							{
								if ($val == 0) // si esta en cero no esta en kardex de articulos
								{
									pintarAlert1('Sin existencia de los articulos señalados en rojo, en el kardex de inventario');
								}
								else
								{
									if ($val == 1)
									{
										pintarAlert1('Los articulos indicados tienen menos existencias de las requeridas, se propone la cantidad maxima para el traslado');
									}
									else // pasa todas las validaciones
									{
										// grabamos el encabezado del movimiento, ivmov
										$res = grabarEncabezadoUnix($centro['cod'], $centro2['cod'], $concepto2, $fuente2, $documento2, 'on');
										if ($res)
										{
											// se graba el detalle del movimiento ivmovdet
											// se incrementa o descuenta el ivsal que no tiene inventario en unix
											grabarEncabezadoSalidaMatrix($codigo, $consecutivo, $ccoOri, $wusuario, $ccoDes, $documento2);
											for ($i = 0; $i < count($inslis); $i++)
											{
												if ($inslis[$i]['lot'] != 'on')
												{
													$cnv = consultarConversor($inslis[$i]['prese'], $ccoOri);
													$cantidad = $inslis[$i]['can'] * $cnv;
													$ecod = explode('-', $inslis[$i]['prese']);
													$cod = $ecod[0];
													$present = consultarPresentacion($cod);
												}
												else
												{
													$cantidad = $inslis[$i]['can'];
													$cod = $inslis[$i]['cod'];
													$present = consultarPresentacion($cod);
												}

												grabarDetalleUnix($centro['cod'], $centro2['cod'], $cod, $inslis[$i]['can'], $present, $concepto2, $fuente2, $documento2, $i + 1, 'on');
												grabarDetalleSalidaMatrix($inslis[$i]['cod'], $cantidad, $codigo, $consecutivo, $wusuario, $inslis[$i]['nlo'], $inslis[$i]['prese']);
												descontarInsumoMatrix($inslis[$i]['cod'], $cantidad, $ccoOri, $inslis[$i]['nlo'], $inslis[$i]['prese']);
											}
											$estado = 'creado';
										}
										else
										{
											pintarAlert1('NO SE HA PODIDO GRABAR EL ENCABEZADO DEL TRASLADO EN UNIX');
										}
									}
								}
							}
							else
							{
								pintarAlert1('Verifique la existencia de los articulo señalados en rojo, en el maestro de articulos');
							}
						} //las actividades de entrada a matrix
						else
						{
							$val = convertirMatrix($inslis, $ccoDes);
							if ($val > 0)
							{
								$res = grabarEncabezadoUnix($centro['cod'], $centro2['cod'], $concepto2, $fuente2, $documento2, 'off');
								if ($res)
								{
									grabarEncabezadoEntradaMatrix($codigo, $consecutivo, $ccoDes, $ccoOri, $wusuario, $documento2);
									for ($i = 0; $i < count($inslis); $i++)
									{
										if ($inslis[$i]['lot'] != 'on')
										{
											$cnv = consultarConversor($inslis[$i]['cod'] . '-', $ccoDes);
											$cantidad = $inslis[$i]['can'] / $cnv;
										}
										else
										{
											$cantidad = $inslis[$i]['can'];
										}
										$present = consultarPresentacion($inslis[$i]['cod']);
										grabarDetalleUnix($centro['cod'], $centro2['cod'], $inslis[$i]['cod'], $cantidad, $present, $concepto2, $fuente2, $documento2, $i + 1, 'off');
										grabarDetalleEntradaMatrix($inslis[$i]['cod'], $inslis[$i]['can'], $codigo, $consecutivo, $wusuario, $inslis[$i]['nlo'], $inslis[$i]['prese']);
										if ($inslis[$i]['nlo'] != '')
										{
											sumarInsumoMatrix($inslis[$i]['cod'], $inslis[$i]['can'], $ccoDes, $inslis[$i]['nlo'], $inslis[$i]['cod']);
										}
										else
										{
											sumarInsumoMatrix($inslis[$i]['cod'], $inslis[$i]['can'], $ccoDes, '', $inslis[$i]['prese']);
										}
									}
									$estado = 'creado';
								}
								else
								{
									pintarAlert1('NO SE HA PODIDO GRABAR EL ENCABEZADO DEL TRASLADO EN UNIX');
								}
							}
							else
							{
								pintarAlert1('Verifique la existencia de los articulo señalados en rojo, en los maestros de la Central ');
							}
						}
						$cantidad = '';
					}
					else
					{
						pintarAlert1('Algunos articulos no tienen saldo o no exiten en Unix o No tienen Tarifa ');
					}
				}
			}
		}
		// si se ha ingresado un valor de busqueda de un traslado
		if (isset ($parcon) and $parcon != '')
		{
			// si se selecciona buscar el traslado de un articulo, existe insfor que dice si se busca el articulo por nombre etc
			// si no esta setiada porque no se busca por articulo se inicializa en cero
			if (!isset ($insfor))
			{
				$insfor = '';
			}
			// igualmete al buscar por articulo se inicializa esta valor para ingresar la fehca incial de busqueda
			// si no se manda en vacio
			if (!isset ($parcon2))
			{
				$parcon2 = '';
			}
			// igualmete al buscar por articulo se inicializa esta valor para ingresar la fehca final de busqueda
			// si no se manda en vacio
			if (!isset ($parcon3))
			{
				$parcon3 = '';
			}
			// se busca el traslado con los parametros de busqueda
			$consultas = BuscarTraslado($parcon, $parcon2, $parcon3, $insfor, $forcon);
			$consulta = $consultas[0];
		}
		// ya se ha seleccionado uno de los traslados encontrados
		if (isset($consulta) and $consulta != '')
		{
			$exp = explode('-', $consulta);
			$numtra = $exp[0] . '-' . $exp[1];
			$exp2 = explode('(', $consulta);
			$fecha = substr($exp2[1], 0, (strlen($exp2[1])-1));
			// se buscan los detalles del traslado
			consultarTraslado($exp[0], $exp[1], $fecha, $origenes[0], $destinos[0], $estado, $inslis);
		}
		// si no se ha ingresado ninguna forma de busqueda de traslados se incializa en cero
		if (!isset($forcon))
		{
			$forcon = '';
		}
		// si no se tiene una lista de traslados encontrados bajo algun parametro de busca este vector se incializa en cero
		if (!isset($consultas))
		{
			$consultas = '';
			$consultas = array();
		}
		// se pinta el formulario que permite buscar algun tralsado por un parametro de busqueda
		pintarBusqueda($consultas, $forcon, $wemp_pmla);
		// si no esta setiada la fecha, se inicializa como la fecha del dia (el usuario la puede cambiar despues)
		if (!isset ($fecha))
		{
			$fecha = date('Y-m-d');
		}
		// se pinta el formulario para ingreso de los datos del traslado
		pintarFormulario($estado, $origenes, $destinos, $numtra, $fecha, $wemp_pmla);
		// si se ha ingresado un articulo sin cantidad y se ha dado submit, se vuelve a buscar por el codigo de este insumo
		// para adaptar las unidades
		if (isset($insumo) and $insumo != '' and (!isset($cantidad) or $cantidad == '') and (!isset($parbus2) or $parbus2 == '')) // (!isset($accion) or $accion!=1)
		{
			$parbus2 = explode('-', $insumo);
			$parbus2 = $parbus2[0];
			$forbus2 = 'Codigo';
		}
		// si se ha ingresado algun valor para busqueda de articulo se busca el articulo de acuerdo a los parametros de busqueda
		if (isset($parbus2) and $parbus2 != '')
		{
			$insumos = consultarInsumos($parbus2, $forbus2, $origen);
			// para el primer resultado deben establecerse las carateristicas a mostrar en pantalla
			if ($insumos)
			{
				// si es un producto, es decir, requiere de un lote
				if ($insumos[0]['lot'] == 'on')
				{
					// si el codigo ingresado esta compuesto por lote
					$exp = explode('-', $parbus2);
					if (isset($exp[1]))
					{
						if ($origen == 'on')
						{
							$lotes = consultarLotes($insumos[0]['cod'], $ccoOri, $exp[1], 'on', $cantidad);
						}
						else
						{
							$lotes = consultarLotes($insumos[0]['cod'], $destinos[0], $exp[1], 'off', $cantidad);
						}
					}
					else // si no, se buscan los lotes
					{
						if (!isset ($lote))
						{
							$lote = '';
						}
						if ($origen == 'on')
						{
							$lotes = consultarLotes($insumos[0]['cod'], $ccoOri, $lote, 'on', $cantidad);
						}
						else
						{
							$lotes = consultarLotes($insumos[0]['cod'], $destinos[0], $lote, 'off', $cantidad);
						}
					}
					// se ininicializa en vacio, porque los productos no tienen diferentes presentaciones
					$unidades = '';
				}
				else if ($origen == 'on') // si no es un producto el articulo a trasladar y viene de matrix
				{
					if ($forbus2 == 'rotulo') // como se ingreso por rotulo, ya se ha escogido una presentacion
					{
						$unidades = consultarUnidades($insumos[0]['cod'], $ccoOri, $parbus2, $insumos[0]['pre']);
					}
					else // cuando no se ingresa por rotulo se ingresa el insumo propio de la central
					{
						// el usuario selecciono una presentacion para ese insumo, que se pone de primera
						// para desplegar en select
						if (isset($prese) and $prese != '')
						{
							$prese = explode('-', $prese);
							$prese = $prese[0];
							$unidades = consultarUnidades($insumos[0]['cod'], $ccoOri, $prese, $insumos[0]['pre']);
						}
						else // se deben buscar las presentaciones para el insumo
						{
							$unidades = consultarUnidades($insumos[0]['cod'], $ccoOri, '', $insumos[0]['pre']);
						}
					}
				}
				else // si el origen es de matrix, el articulo corresponde a una presentacion, entonces esto no aplica
				{
					$unidades = '';
				}
			}
		}
		else // no se ha ingresado un valor de busqueda de articulo
		{
			// no debo dejar ingresar un producto sin lote
			if (isset($lote) and $lote == '')
			{
				$cantidad = '';
			}
			// se ha seleccionado el insumo y la cantidad a trasladar
			if (isset($insumo) and $insumo != '' and isset($cantidad) and $cantidad != '') // and $accion==1
			{
				// el sel valor escogido del usuario para el select de presentaciones, cuando esto no es necesario se
				// inicializa en vacio
				if (!isset($prese))
				{
					$prese = '';
				}
				// es eel valor escogido del usuario para el select de lotes,cundo no es un producto se inicializa en vacio
				if (!isset($lote))
				{
					$lote = '';
				}

				$exp = explode('-', $insumo);
				// se creal todo el vector para el primer insumo ingresado para trasladar
				if (!isset($inslis))
				{
					$inslis[0]['cod'] = $exp[0];
					$inslis[0]['nom'] = $exp[1];
					$inslis[0]['gen'] = $exp[2];
					$inslis[0]['pre'] = $exp[3] . '-' . $exp[4];
					// echo $inslis[0]['pre'];
					$inslis[0]['can'] = $cantidad;
					$inslis[0]['lot'] = $exp[5];
					$inslis[0]['est'] = $exp[6];
					$inslis[0]['nlo'] = $lote;
					$inslis[0]['prese'] = $prese;
				}
				else // cuando ya hay otros insumos agregados
				{
					// se busca que ese insumo ya no haya sido agregado, en caso tal,no se agrega a la lista
					// si no que se incrementa la cantidad para el insumo ya ingresado
					for ($i = 0; $i < count($inslis); $i++)
					{
						if ($inslis[$i]['cod'] == $exp[0] and $inslis[$i]['nlo'] == $lote and $inslis[$i]['prese'] == $prese)
						{
							$inslis[$i]['can'] = $inslis[$i]['can'] + $cantidad;
							$repetido = 'on';
						}
					}
					// si el insumo no estaba repetido, lo agrego a la lista de insumos
					if (!isset($repetido))
					{
						$inslis[count($inslis)]['cod'] = $exp[0];
						$inslis[count($inslis)-1]['nom'] = $exp[1];
						$inslis[count($inslis)-1]['gen'] = $exp[2];
						$inslis[count($inslis)-1]['pre'] = $exp[3] . '-' . $exp[4];
						// echo $inslis[$i]['pre'];
						$inslis[count($inslis)-1]['can'] = $cantidad;
						$inslis[count($inslis)-1]['est'] = $exp[6];
						$inslis[count($inslis)-1]['lot'] = $exp[5];
						$inslis[count($inslis)-1]['nlo'] = $lote;
						$inslis[count($inslis)-1]['prese'] = $prese;
					}
				}
				$cantidad = '';
			}
			// si se ha dado click sobre un checkbox de eliminar para algun insumo, este se elimina de la lista
			if (isset($eli))
			{
				$inslis = eliminarInsumo($inslis, $eliminar);
				if ($inslis == false)
				{
					$inslis = '';
					$inslis = array();
				}
			}
			$insumos = '';
			$insumos =array();
		}
		// si no es necesario el vector de presentaciones se incializa en vacio
		if (!isset ($unidades))
		{
			$unidades = '';
		}
		// si no es necesario el vector de lotes se incializa en vacio
		if (!isset($lotes))
		{
			$lotes = '';
		}
		// si no se han igresado insumos para trasladar, se incializa en vacio
		if (!isset($inslis))
		{
			$inslis = [];
		}
		// se pinta el formulario que permite buscar los articulos a trasladar, seccionar lotes o presentaciones
		// ingresar la cantidad y desplegar la lista de articulos ingresados
		// cuando se consulta un traslado, se debe pasar las cnatidades de la unidad minima a la utilizada en matrix,
		// para desplegar en pantalla a quiene tienen inventario en unix
		if ($origen != 'on' and (isset($consulta) and $consulta != ''))
		{
			cambiarCantidades1($inslis, $ccoDes);
		}
		// cuando se graba un traslado, se debe pasar las cnatidades de la unidad minima a la utilizada en matrix,
		// para desplegar en pantalla a quienes tienen inventario en unix
		if ($origen != 'on' and isset($crear))
		{
			cambiarCantidades2($inslis, $ccoDes);
		}

		if (!isset($cantidad))
		{
			$cantidad = '';
		}
		pintarInsumos($insumos, $inslis, $unidades, $lotes, $cantidad);
	}
	else
	{
		pintarAlert2('EN ESTE MOMENTO NO ES POSIBLE CONECTARSE CON UNIX PARA REALIZAR EL CARGO, POR FAVOR INGRESE MAS TARDE');
	}
}
/**
                                * ===========================================================================================================================================
                                */

                                ?>


</body >
</html >
