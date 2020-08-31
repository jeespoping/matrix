<html>
<head>
  <title>CONSIGNACION INTERNA</title>

  <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
    	.titulo1{color:#FFFFFF;background:#006699;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.titulo3{color:#003366;background:#A4E1E8;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.titulo2{color:#003366;background:#57C8D5;font-size:12pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	.titulo4{color:#003366;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.texto1{color:#006699;background:#FFFFFF;font-size:9pt;font-family:Tahoma;text-align:center;}
    	.texto2{color:#006699;background:#f5f5dc;font-size:9pt;font-family:Tahoma;text-align:center;}
    	.texto3{color:#006699;background:#A4E1E8;font-size:9pt;font-weight:bold;font-family:Tahoma;text-align:center;}
    	.texto4{color:#006699;background:#FFFFFF;font-size:9pt;font-family:Tahoma;text-align:right;}
    	.texto5{color:#006699;background:#f5f5dc;font-size:9pt;font-family:Tahoma;text-align:right;}
    	.acumulado1{color:#003366;background:#FFCC66;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:right;}
    	.acumulado2{color:#003366;background:#FFCC66;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.acumulado3{color:#003366;background:#FFDBA8;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.acumulado4{color:#003366;background:#FFDBA8;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:right;}
    	.error1{color:#FF0000;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
   </style>

   <script type="text/javascript">
   function enter()
   {
   	if(document.forma1.consignar.checked==true)
   	{
   		var fRet;
   		fRet = confirm('Estas seguro que desea realizar la consignación');
   		if (fRet==false)
   		{
   			document.forma1.consignar.checked=false;
   		}else
   		{
   			document.forma1.bandera1.value=2;
   		}
   	}

   	document.forma1.submit();
   }


   function enter2()
   {
   	document.forma1.submit();
   }

   function enter3()
   {
   	document.forma.submit();
   }

   function enter4()
   {
   	document.forma1.bandera1.value=2;
   	document.forma.submit();
   }

   function enter5()
   {
   	document.forma1.bandera1.value=3;
   	document.forma1.submit();
   }

   function enter6()
   {
   	document.forma1.consignar.checked=false;
   	document.forma1.submit();
   }



							</script>

<?php
include_once("conex.php");


/*************************************************************************************
*     PROGRAMA PARA LA CONSIGNACIÓN INTERNA DE LA CAJA GENERAL A BANCOS   *
*                                                                 *
*************************************************************************************/
//=================================================================================================================================
//PROGRAMA:	consignacionN.php
//AUTOR: Carolina Castaño.


//TIPO DE SCRIPT: principal
//RUTA DEL SCRIPT: matrix\pos\procesos\consignacionN.php

//HISTORIAL DE REVISIONES DEL SCRIPT:
//-------------------I------------------------I---------------------------------------------------------------------
//	  FECHA           I     AUTOR             I   MODIFICACION
//-------------------I------------------------I------------------------------------------------------------------------
//  2006-09-26       I Carolina Castaño  P    I creación del script.
//-------------------I------------------------I-----------------------------------------------------------------------
//  2006-05-14      I Carolina Castaño  P     I se modifica de manera que permita realizar consignaciones parciales
//-------------------I------------------------I-----------------------------------------------------------------------
//-------------------I------------------------I-----------------------------------------------------------------------
//  2006-05-17      I Carolina Castaño  P     I se agrega un numero de autorizacion
//-------------------I------------------------I-----------------------------------------------------------------------

//DESCRIPCION:Este programa sirve para realizar translados de recibos ya cuadrados desde una caja auxiliar a una caja principal
//adicionalmente permite la consulta y la anulacion de un transalado que no haya sido cuadrado posteriormente

//TABLAS QUE MODIFICA:
// $wbasedato."_000045: Tabla temporal de almacenamiento de operaciones (notas y recibos), select, delete
// $wbasedato."_000030: Busqueda de cajeros autorizados, select
// $wbasedato."_000040: Maestro de Fuentes, select
// $wbasedato."_000018: encabezado de factura, select

//FUNCIONES:

// INCLUDES:
//  conex.php = include para conexión mysql

// VARIABLES:
//$contador= me indica si debo o no pintar el formulario, cuando no hay recibos cuadrados no pinto formulario
//$bandera=me indica que el formulario ha sido enviado
//=================================================================================================================================

?>

</head>

<body>

<?php

/***********************************************FUNCIONES DE PERSISTENCIA*************************************/

function consultarCaja($user, &$cco, &$cajCod2, &$cajDes2)
{
	//extrae los datos de la caja a la que pertenece el usuario

	global $conex;
	global $wbasedato;

	$q="select Cjecco, Cjecaj, Cajcod, Cajdes "
	."from ".$wbasedato."_000030, ".$wbasedato."_000028 "
	."where	Cjeusu = '".$user."' "
	."and	Cajcod = MID(Cjecaj, 1,2) ";

    echo "edb->".$q;
	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);

	if ($num > 0)
	{
		$row=mysql_fetch_array ($err);
		$cco = substr($row['Cjecco'],0,4);
		$cajCod2 = $row['Cajcod'];
		$cajDes2 = $row['Cajdes'];
	}
}

function consultarBanco(&$cajCod, &$cajDes)
{
	//* extrae los datos de la caja general por defecto

	global $conex;
	global $wbasedato;

	$q="select bancod, bannom "
	."from  ".$wbasedato."_000069 "
	."where	bancag = 'on' ";


	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);

	if ($num > 0)
	{
		$row=mysql_fetch_array ($err);
		$cajCod = $row['bancod'];
		$cajDes = $row['bannom'];
	}
}


function consultarPagos()
{
	//*consulto las formas de pago existentes para agrupar

	global $conex;
	global $wbasedato;

	$q="select fpacod, fpades, fpache, fpatar "
	."from	".$wbasedato."_000023 "
	."where	fpaest = 'on' ";

	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);

	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$pagos['codigo'][$i]=$row[0];
			$pagos['nombre'][$i]=$row[1];
			IF($row[2]=='on' OR $row[3]=='on')
			$pagos['tipo'][$i]='on';
			else
			$pagos['tipo'][$i]='off';
		}
		return $pagos;
	}else
	{
		return false;
	}
}

function consultarConsecutivo(&$consecutivoT, &$fuenteT)
{
	//* consulto el consecutivo y la fuente de las consignaciones internas

	global $conex;
	global $wbasedato;

	$q="select carfue, carcon "
	."from	".$wbasedato."_000040 "
	."where	carest = 'on' and carcsg='on' ";

	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);

	if ($num>0)
	{
		$row = mysql_fetch_array($err);
		$consecutivoT=$row[1]+1;
		$fuenteT=$row[0];
	}else
	{
		$consecutivoT='';
		$fuenteT='';
	}
}

function consultarFuente()
{

	global $conex;
	global $wbasedato;

	$q="select carfue, carcon "
	."from	".$wbasedato."_000040 "
	."where	carest = 'on' and carcsg='on' ";

	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);

	if ($num>0)
	{
		$row = mysql_fetch_array($err);
		$fuenteT=$row[0];
	}else
	{
		$fuenteT='';
	}

	return $fuenteT;
}


function consultarBancos($wbanF, $cajCod)
{
	//consulto los bancos principales

	global $conex;
	global $wbasedato;

	if ($wbanF!='')
	{
		$exp=explode ('-',$wbanF);

		$bancos['codigo'][0]=$exp[0];
		$bancos['nombre'][0]=$exp[1];
		$bancos['cuenta'][0]=$exp[2];


		$q="select bancod, bannom, bancue "
		."from	".$wbasedato."_000069 "
		."where	banest='on' and bancod<>'".$exp[0]."' and bannom<>'".$exp[1]."' and bancod<>'".$cajCod."' and banrec='on' ";

	}else
	{
		$bancos['codigo'][0]='';
		$bancos['nombre'][0]='';
		$bancos['cuenta'][0]='';

		$q="select bancod, bannom, bancue "
		."from	".$wbasedato."_000069 "
		."where	 banest='on' and bancod<>'".$cajCod."' and banrec='on' ";
	}
	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);

	if ($num>0)
	{
		for ($i=1;$i<=$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$bancos['codigo'][$i]=$row[0];
			$bancos['nombre'][$i]=$row[1];
			$bancos['cuenta'][$i]=$row[2];
		}
	}
	return $bancos;
}

function consultarRecibos($codPago, $cajCod, $cco, $cajCod2)
{
	//*consulta los recibos en estado cuadrado para una caja retornando un vector de recibos

	global $conex;
	global $wbasedato;

	$q="select A.Renfue, A.Rennum, A.Renfec, C.rfpvfp, C.rfpdan, C.rfpobs, C.id "
	."from	".$wbasedato."_000020 A, ".$wbasedato."_000022 C "
	."where	C.rfpban = '".$cajCod."' "
	."and	C.rfpcaf= '".$cajCod2."' "
	."and	C.Rfpecu='C' "
	."and	C.rfpest='on' "
	."and	C.rfpfpa = '".$codPago."' "
	."and	C.rfpcco= A.rencco "
	."and	C.rfpnum = A.rennum "
	."and	A.Renfue = C.rfpfue ";

	$err = mysql_query($q,$conex) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );
	$num = mysql_num_rows($err);

	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$registro['fuente'][$i]=$row[0];
			$registro['numero'][$i]=$row[1];
			$registro['id'][$i]=$row[6];
			$registro['fecha'][$i]=$row[2];
			$registro['valor'][$i]=$row[3];
			$registro['documento'][$i]=$row[4];

			//consulto nombre del banco a partir del codigo

			$q="select bannom "
			."from	".$wbasedato."_000069 "
			."where	bancod = '".$row[5]."' "
			."and	banest = 'on' ";

			$res = mysql_query($q,$conex);
			$row2 = mysql_fetch_array($res);


			$registro['banco'][$i]=$row[5]."-".$row2[0];
		}
		return $registro;
	}else
	{
		return false;
	}
}

function grabarEncabezado($fuenteT, $consecutivoT, $cajCod, $wbanF, $totalF, $wusuario, $auto)
{
	//consulto las formas de pago existentes para agrupar

	global $conex;
	global $wbasedato;

	$exp=explode('-', $wbanF);

	$q= " INSERT INTO ".$wbasedato."_000075 (   Medico       ,   Fecha_data,   Hora_data,   tenfue,           tennum ,              tencai  ,     tencaf    ,    tenval    ,        tenfec ,   tenaci,      tenest    , Seguridad        ) "
	."         VALUES ('".$wbasedato."',   '".date('Y-m-d')."', '".(string)date("H:i:s")."' ,'".$fuenteT."', '".$consecutivoT."', '".$cajCod."', '".$exp[0]."' , '".$totalF."' , '".date('Y-m-d')."'  , '".$auto."' , 'on' ,  'C-".$wusuario."') ";

	$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
}


function grabarDetalle($fuenteT, $consecutivoT, $numero, $fuente, $valor, $pago, $cajCod, $id, $wusuario)
{
	//consulto las formas de pago existentes para agrupar

	global $conex;
	global $wbasedato;

	$q= " INSERT INTO ".$wbasedato."_000076 (   Medico       ,   Fecha_data,   Hora_data,   tdefue,           tdenum ,              tdendo  ,     tdefdo   ,     tdeval    ,        tdefpa ,     tdereg,    tdeest    , Seguridad        ) "
	."         VALUES ('".$wbasedato."',   '".date('Y-m-d')."', '".(string)date("H:i:s")."' ,'".$fuenteT."', '".$consecutivoT."', '".$numero."', '".$fuente."' , '".$valor."' , '".$pago."'  ,  '".$id."'  ,     'on' ,  'C-".$wusuario."') ";

	$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
}

function grabarMovimientos($fuenteT, $consecutivoT, $numero, $fuente, $cantidad, $destino, $id, $valor, $caja, $wusuario, $pago)
{
	//cambio el estado de los recibo

	global $conex;
	global $wbasedato;

	$q="select rfpcco, rfpdan,  rfpobs, rfppla, rfpaut, rfpecu, rfpcaf, rfpbai from	 ".$wbasedato."_000022 C where	id = '".$id."' ";
	$res = mysql_query($q,$conex);
	$row2 = mysql_fetch_array($res);
	$wcco=$row2[0];

	if ($valor==$cantidad)
	{
		//2007-05-03 se agrega actualizacion de rfpbai
		$q=" UPDATE ".$wbasedato."_000022 SET rfpban='".$destino."' WHERE rfpfpa='".$pago."' and rfpnum='".$numero."' and rfpfue='".$fuente."' and rfpest='on' and id='".$id."' ";
		$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

		$q= " INSERT INTO ".$wbasedato."_000022 (   Medico       ,   Fecha_data,           Hora_data,                    rfpfue              ,   rfpnum    ,                rfpfpa              ,  rfpvfp        ,   rfpdan         ,   rfpobs     , rfpest,   rfpcco  ,    rfppla,           rfpaut,       rfpecu,   rfpcaf,            rfpban,      Seguridad        ) "
		."                             VALUES ('".$wbasedato."','".date('Y-m-d')."', '".(string)date("H:i:s")."' ,    '".$fuenteT."',          ".$consecutivoT.",        '".$pago."'         ,  ".$valor.",        '".$id."',      '',               'on'  ,  '".$wcco."', '', '',      'N',   '".$caja."',    '', 'C-".$wusuario."') ";

		$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	}
	else if ($valor>$cantidad)
	{
		$valor=$valor-$cantidad;

		$q=" UPDATE ".$wbasedato."_000022 SET rfpvfp='".$valor."' WHERE rfpfpa='".$pago."' and rfpnum='".$numero."' and rfpfue='".$fuente."' and rfpest='on' and id=".$id." ";
		$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

		$q= " INSERT INTO ".$wbasedato."_000022 (   Medico       ,   Fecha_data,           Hora_data,                    rfpfue              ,   rfpnum    ,                rfpfpa              ,  rfpvfp        ,   rfpdan         ,   rfpobs     , rfpest,   rfpcco  ,    rfppla,           rfpaut,       rfpecu,   rfpcaf,            rfpban,      Seguridad        ) "
		."                             VALUES ('".$wbasedato."','".date('Y-m-d')."', '".(string)date("H:i:s")."' ,'".$fuenteT."',          ".$consecutivoT.",        '".$pago."'         ,       ".$valor.",     '".$id."',      '',  'on'  ,  '".$wcco."', '', '',      'N',   '".$caja."',    '', 'C-".$wusuario."') ";

		$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

		//2007-05-03 se inserta tambien valor de rfpbai
		$q= " INSERT INTO ".$wbasedato."_000022 (   Medico       ,   Fecha_data,           Hora_data,                    rfpfue              ,   rfpnum    ,                rfpfpa              ,  rfpvfp        ,   rfpdan         ,   rfpobs     , rfpest,   rfpcco  ,    rfppla,           rfpaut,                   rfpecu,   rfpcaf,            rfpban,              rfpbai,   Seguridad        ) "
		."                             VALUES ('".$wbasedato."','".date('Y-m-d')."', '".(string)date("H:i:s")."' ,'".$fuente."',          ".$numero.",                '".$pago."'         ,  ".$cantidad.",    '".$row2[1]."',      '".$row2[2]."',  'on'  ,  '".$wcco."', '".$row2[3]."', '".$row2[4]."',      '".$row2[5]."',   '".$row2[6]."',  '".$destino."',  '".$row2[7]."', 'C-".$wusuario."') ";

		$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

		$id2= mysql_insert_id($conex);

		$q= " INSERT INTO ".$wbasedato."_000022 (   Medico       ,   Fecha_data,           Hora_data,                    rfpfue              ,   rfpnum    ,                rfpfpa              ,  rfpvfp        ,   rfpdan         ,   rfpobs     , rfpest,   rfpcco  ,    rfppla,           rfpaut,       rfpecu,   rfpcaf,            rfpban,      Seguridad        ) "
		."                             VALUES ('".$wbasedato."','".date('Y-m-d')."', '".(string)date("H:i:s")."' ,'".$fuenteT."',          ".$consecutivoT.",        '".$pago."'         ,  ".$cantidad.",    '".$id2."',      '',  'on'  ,  '".$wcco."', '', '',      'N',   '".$caja."',    '', 'C-".$wusuario."') ";

		$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	}
}

function consultarEncabezado($cajCod,$consulta, $cco, $fuenteT, &$wbanF, &$totalF, &$fec, &$auto )
{

	//*consulto el encabezado de una consignacion dado

	global $conex;
	global $wbasedato;

	$q="select tenfue, tencaf, tenval, tenfec, bannom, bancue, tenaci "
	."from	".$wbasedato."_000075, ".$wbasedato."_000069 "
	."where	tenest = 'on' and tennum='".$consulta."' and tencai='".$cajCod."' and bancod=tencaf and tenfue='".$fuenteT."' ";


	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);

	if ($num>0)
	{
		$row = mysql_fetch_array($err);
		$wbanF=$row[1]."-".$row[4]."-".$row[5];
		$fuenteT=$row[0];
		$totalF=$row[2];
		$fec=$row[3];
		$auto=$row[6];
	}else
	{
		$wbanF='';
		$fuenteT='';
		$totalF='';
		$fec='';
		$auto='';
	}
}

function consultarRegistros ($cco, $fuenteT, $consulta, &$fuente, &$numero, &$fecha, &$valor, &$documento, &$banco, &$pagos, &$total, &$id, &$cantidad)
{
	//consulto los registros detalle de un encabezao dado y los organizo en vectores según la forma de pago

	global $conex;
	global $wbasedato;

	$q=" select A.tdendo, A.tdefdo, A.tdeval, A.tdefpa, B.fpache, B.fpatar, C.rfpdan, C.rfpobs, D.renfec, B.fpades, A.tdereg "
	."from	".$wbasedato."_000076 A, ".$wbasedato."_000023 B, ".$wbasedato."_000022 C, ".$wbasedato."_000020 D "
	."where	A.tdeest = 'on' and A.tdenum='".$consulta."' and A.tdefue='".$fuenteT."' "
	."and B.fpacod=A.tdefpa and C.rfpfue=A.tdefdo and C.rfpnum=A.tdendo and C.rfpfpa=A.tdefpa and C.id=A.tdereg and D.renfue=A.tdefdo and  rfpcco= rencco and D.rennum=A.tdendo order by A.tdefpa";


	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);
	$forma='0';
	$contador=0;
	$rotador=0;


	for ($i=0;$i<$num;$i++)
	{
		$row = mysql_fetch_array($err);

		if ($forma!=$row[3])// la idea es poder agrupar por formas de pago
		{

			if($i!=0)
			{
				$contador++;
				$rotador=0;
			}
			$total[$contador]=0;
			//leno vector pagos
			$pagos['codigo'][$contador]=$row[3];
			$pagos['nombre'][$contador]=$row[9];
			IF($row[4]=='on' OR $row[5]=='on')
			$pagos['tipo'][$contador]='on';
			else
			$pagos['tipo'][$contador]='off';

			$forma=$row[3];

		}else if ($i!=0)
		{
			$rotador++;
		}

		$fuente[$contador][$rotador]=$row[1];
		$numero[$contador][$rotador]=$row[0];

		$q=" select rfpvfp "
		."from	".$wbasedato."_000022  "
		."where	rfpfue='".$fuenteT."' and rfpnum='".$consulta."' and rfpfpa='".$row[3]."'  and rfpdan='".$row[10]."' and rfpest= 'on' ";

		$err4 = mysql_query($q,$conex);
		$row4 = mysql_fetch_array($err4);

		if ($row4[0]=='')
		{
			$row4[0]=0;
		}
		//el valor puede ser diferente al registro puesto que puede haberse partido
		$q=" select rfpvfp "
		."from	".$wbasedato."_000022  "
		."where	rfpfue='".$fuenteT."' and rfpnum='".$consulta."' and rfpest= 'on' "
		."and rfpvfp=(".$row[2]."-".$row4[0].") and rfpdan in (select id from ".$wbasedato."_000022  where rfpfue='".$fuente[$contador][$rotador]."' and rfpnum='".$numero[$contador][$rotador]."' and rfpfpa='".$row[3]."'  and rfpdan<>'".$row[10]."' and rfpest= 'on') ";

		$err3 = mysql_query($q,$conex);
		$num3 = mysql_num_rows($err3);

		if ($num3>0)
		{
			$row3 = mysql_fetch_array($err3);
			$cantidad[$contador][$rotador]=$row3[0];
		}
		else
		{
			$cantidad[$contador][$rotador]=$row[2];
		}
		$valor[$contador][$rotador]=$row[2];
		$id[$contador][$rotador]=$row[10];
		$documento[$contador][$rotador]=$row[6];
		$fecha[$contador][$rotador]=$row[8];
		$total[$contador]=$total[$contador]+$cantidad[$contador][$rotador];
		if ($row[7]!='')
		{
			$q="select bannom "
			."from	".$wbasedato."_000069 "
			."where	bancod = '".$row[7]."' "
			."and	banest = 'on' ";

			$res = mysql_query($q,$conex);
			$row2 = mysql_fetch_array($res);
			$banco[$contador][$rotador]=$row2[0];
		}
		else
		$banco[$contador][$rotador]='';
	}

}

function cambiarEstado($numero, $fuente, $banco,  $pago, $id )
{
	//cambio el estado de los recibo

	global $conex;
	global $wbasedato;

	$q="     UPDATE ".$wbasedato."_000022 SET rfpban='".$banco."' WHERE rfpnum='".$numero."' and rfpfue='".$fuente."' and rfpest='on' and rfpfpa='".$pago."' and id='".$id."' ";

	$err = mysql_query($q,$conex);
}


function devolverMovimientos($fuenteT, $consecutivoT,  $numero, $fuente, $destino, $pago, $id, $valor, $cantidad)
{
	//cambio el estado de los recibo

	global $conex;
	global $wbasedato;

	//consulto el centro de costos del recibo
	$q="select rfpcco  from	 ".$wbasedato."_000022 C where	id = '".$id."' ";
	$res = mysql_query($q,$conex);
	$row2 = mysql_fetch_array($res);
	$wcco=$row2[0];

	if ($valor==$cantidad)
	{
		$q=" UPDATE ".$wbasedato."_000022 SET rfpban='".$destino."' WHERE rfpfpa='".$pago."' and rfpnum='".$numero."' and rfpfue='".$fuente."' and rfpest='on' and id='".$id."' ";
		$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

	}
	else if ($valor>$cantidad)
	{
		$q=" UPDATE ".$wbasedato."_000022 SET rfpvfp=rfpvfp+".$cantidad." WHERE rfpfpa='".$pago."' and rfpnum='".$numero."' and rfpfue='".$fuente."' and rfpest='on' and id='".$id."' ";
		$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

		$q=" select rfpcco from ".$wbasedato."_000022 WHERE rfpfpa='".$pago."' and rfpnum='".$numero."' and rfpfue='".$fuente."' and rfpest='on' and id='".$id."' ";
		$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		$row3 = mysql_fetch_array($err);

		$q="select rfpdan  from	".$wbasedato."_000022 where	rfpdan not in (select tdereg from	".$wbasedato."_000076 where	tdenum='".$consecutivoT."' and tdefue='".$fuenteT."') and rfpnum='".$consecutivoT."' and rfpfue='".$fuenteT."'  ";
		$err = mysql_query($q,$conex);
		$num2 = mysql_num_rows($err);

		for ($j=0;$j<$num2;$j++)
		{
			//debo buscar el id que tenga el mismo registro
			$row2 = mysql_fetch_array($err);

			$q="select rfpfue, rfpnum, rfpcco, rfpvfp, rfpfpa  from	".$wbasedato."_000022 where	id=".$row2[0];

			$res= mysql_query($q,$conex);
			$row6 = mysql_fetch_array($res);

			if ($row6[0]==$fuente and $row6[1]==$numero and $row6[2]==$row3[0] and $row6[4]==$pago and $row6[3]==$cantidad )
			{
				$q=" UPDATE ".$wbasedato."_000022 SET rfpest='off'  WHERE id='".$row2[0]."' and rfpnum='".$numero."' and rfpfue='".$fuente."' and rfpest='on' ";
				$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
			}
		}
	}

	$q=" UPDATE ".$wbasedato."_000022 SET rfpest='off'  WHERE rfpnum='".$consecutivoT."' and rfpfue='".$fuenteT."' and rfpest='on' ";

	$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

}

function incrementarConsecutivo($fuenteT)
{
	global $conex;
	global $wbasedato;

	$q = "lock table ".$wbasedato."_000040 LOW_PRIORITY WRITE";
	$errlock = mysql_query($q,$conex);


	$q= "   UPDATE ".$wbasedato."_000040 "
	."      SET carcon = carcon + 1 "
	."    WHERE carfue = '".trim($fuenteT)."'"
	."      AND carest = 'on' ";


	$res1 = mysql_query($q,$conex);

	$q = " UNLOCK TABLES";   //SE DESBLOQUEA LA TABLA DE FUENTES
	$errunlock = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
}

function consultarPermiso($fuen, $docu)
{
	global $conex;
	global $wbasedato;

	$permiso=true;

	$q = " SELECT tenfec FROM ".$wbasedato."_000075 WHERE tenfue = '".$fuen."' AND tennum = '".$docu."'  and tenest='on' ";
	$res = mysql_query($q,$conex);
	$row = mysql_fetch_array($res);

	$exp=explode('-', $row[0]);
	$exp2=explode('-', date('Y-m-d'));
	if ($exp[0]!=$exp2[0] or $exp[1]!=$exp2[1])
	{
		$permiso=false;
	}

	return $permiso;
}

function anularRegistro($consecutivoT, $fuenteT, $numero, $fuente, $pago, $id)
{
	global $conex;
	global $wbasedato;

	$q= "   UPDATE ".$wbasedato."_000076 SET tdeest = 'off' "
	."WHERE tdefue = '".$fuenteT."' and tdenum = '".$consecutivoT."' and tdendo = '".$numero."' and tdefdo = '".$fuente."' and tdefpa = '".$pago."' and tdereg = '".$id."' ";

	$res1 = mysql_query($q,$conex);
}


function anularEncabezado($fuenteT, $consecutivoT, $cajCod)
{
	global $conex;
	global $wbasedato;


	$q= "   UPDATE ".$wbasedato."_000075 SET tenest = 'off' "
	."WHERE tenfue = '".$fuenteT."' and tennum = '".$consecutivoT."' and tencai='".$cajCod."' ";

	$res1 = mysql_query($q,$conex);

}
/***********************************************FUNCIONES DE MODELO*************************************/

function calcularTotal($pagos, $fuente, $valor, $radio2 , &$total, &$totalF, &$radio, &$cantidad)
{
	$totalF=0;
	for ($i=0;$i<count($pagos['codigo']);$i++)
	{
		$total[$i]=0;
		if (isset($fuente[$i]))
		{
			for ($j=0;$j<count($fuente[$i]);$j++)
			{
				if (isset ($radio[$i][$j]) and $radio[$i][$j]!='' and isset($radio2[$i]) and $radio2[$i]!='' )
				{
					$radio[$i][$j]='checked';

					If ($cantidad[$i][$j]==0)
					{
						$cantidad[$i][$j]=$valor[$i][$j];
					}

					if ($cantidad[$i][$j]>$valor[$i][$j])
					{
						$radio[$i][$j]='';
						$cantidad[$i][$j]=0;
						pintarAlert8();
					}

					$total[$i]=$total[$i]+$cantidad[$i][$j];
				}
				else
				{
					$radio[$i][$j]='';
					$cantidad[$i][$j]=0;
				}
			}
		}
		$totalF=$totalF+$total[$i];
	}
}


/***********************************************FUNCIONES DE PRESENTACION*************************************/

function pintarEncabezado($wactualiz)
{
	//* pinta el encabezado
	global $wbasedato;
	echo "<p align=right><font size=3><b>Versión: ".$wactualiz."</b></font></p>";
	echo "<center><table border='1' width='350'>";
	echo "<tr><td colspan='2' class='titulo1'><img src='/matrix/images/medical/POS/logo_".$wbasedato.".png' WIDTH=388 HEIGHT=70></td></tr>";
	echo "<tr><td colspan='2'class='titulo1'><b>PROGRAMA PARA CONSIGNACIÓN INTERNA</b></font></td></tr>";

	echo"<form action='consignacionN.php' method='post' name='forma' >";
	echo "<tr><td colspan='2'class='titulo1'>Nº CONSIGNACION: <INPUT TYPE='text' NAME='consulta' VALUE='' size='10' ><INPUT TYPE='button' NAME='consultaR' VALUE='CONSULTAR' onclick='javascript:enter3()' ></td></tr>";
	echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
	echo "</form>";
	echo "</table></BR></BR>";
}

function pintarConsulta1($cajCod, $cajDes, $consecutivoT)
{
	//* pinta el encabezado de caja origen y banco destino

	echo "<table align='center'>";
	echo "<tr><td class='titulo4' >BANCO ORIGEN: ".$cajCod."-".$cajDes."</td></tr>";
	echo "<tr><td class='titulo4' >PENDIENTES PARA CONSIGNACION INTERNA A ".date('Y-m-d')."</td></tr>";
	echo "<tr><td class='titulo4' >&nbsp;</td></tr>";
	echo "<tr><td class='titulo4' >NUMERO DE CONSIGNACION INTERNA: ".$consecutivoT."</td></tr>";
	echo "</table></br>";
}

function pintarConsulta2($cajCod, $cajDes, $consecutivoT, $fecha, $wbanF)
{
	echo "<table align='center'>";
	echo "<tr><td class='titulo4' >BANCO DE ORIGEN: ".$cajCod."-".$cajDes."</td></tr>";
	echo "<tr><td class='titulo4' >&nbsp;</td></tr>";
	echo "<tr><td class='titulo4' >NUMERO DE CONSIGNACION INTERNA: ".$consecutivoT."</td></tr>";
	echo "<tr><td class='titulo4' >FECHA DE CONSIGNACION INTERNA: ".$fecha."</td></tr>";
	echo "<tr><td class='titulo4' >BANCO DESTINO: ".$wbanF."</td></tr>";
	echo "</table></br>";
}

function pintarConsulta3($cajCod, $cajDes, $consecutivoT, $fecha, $wbanF)
{
	echo "<table align='center'>";
	echo "<tr><td class='titulo4' >BANCO DE ORIGEN: ".$cajCod."-".$cajDes."</td></tr>";
	echo "<tr><td class='titulo4' >&nbsp;</td></tr>";
	echo "<tr><td class='titulo4' >SE HA REALIZADO EXITOSAMENTE LA CONSIGNACIÓN INTERNA: ".$consecutivoT."</td></tr>";
	echo "<tr><td class='titulo4' >FECHA DE CONSIGNACIÓN: ".$fecha."</td></tr>";
	echo "<tr><td class='titulo4' >BANCO DESTINO: ".$wbanF."</td></tr>";
	echo "</table></br>";
}

function pintarAlerta1()
{
	//*
	echo "</table>";
	echo"<form action='translado.php' method='post' name='form1' ><CENTER><fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080>";
	echo "<table align='center' border=0 bordercolor=#000080 width=700 style='border:solid;'>";
	echo "<tr><td colspan='2' align=center><font size=3 color='#000080' face='arial' align=center><b>NO EXISTE UNA CAJA REGISTRADA COMO ORIGEN</td><tr>";
	echo "<tr><td colspan='2' align='center'><input type='button' name='aceptar' value='ACEPTAR' onclick='javascript:window.close()'></td><tr>";
	echo "</table></fieldset></form>";
}

function pintarAlert2($cajCod)
{
	//*
	echo "</table>";
	echo"<form action='translado.php' method='post' name='form1' ><CENTER><fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080>";
	echo "<table align='center' border=0 bordercolor=#000080 width=700 style='border:solid;'>";
	echo "<tr><td colspan='2' align=center><font size=3 color='#000080' face='arial' align=center><b>LA CAJA ".$cajCod." NO TIENE NINGUN PENDIENTE DE CONSIGNACION INTERNA</td><tr>";
	echo "<tr><td colspan='2' align='center'><input type='button' name='aceptar' value='ACEPTAR' onclick='javascript:window.close()'></td><tr>";
	echo "</table></fieldset></form>";
}

function pintarAlert3()
{
	echo '<script language="Javascript">';
	echo 'alert ("DEBE SELECCIONAR EL BANCO AL CUAL VA A REALIZAR LA CONSIGNACIÓN INTERNA")';
	echo '</script>';
}


function pintarAlert4($cajCod, $consulta)
{
	global $wbasedato;
	echo "</table>";
	echo"<form action='consignacionN.php' method='post' name='forma1' ><CENTER><fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080>";
	echo "<table align='center' border=0 bordercolor=#000080 width=700 style='border:solid;'>";
	echo "<tr><td colspan='2' align=center><font size=3 color='#000080' face='arial' align=center><b>NO EXISTE UNA CONSIGNACIÓN REALIZADA EN LA CAJA ".$cajCod.", PARA EL NUMERO ".$consulta."</td><tr>";
	echo "<tr><td colspan='2' align='center'><input type='button' name='aceptar' value='ACEPTAR' onclick='javascript:enter2()'></td><tr>";
	echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
	echo "</table></fieldset></form>";
}

function pintarAlert5($consecutivoT)
{
	global $wbasedato;
	echo "</table>";
	echo"<form action='consignacionN.php' method='post' name='forma1' ><CENTER><fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080>";
	echo "<table align='center' border=0 bordercolor=#000080 width=700 style='border:solid;'>";
	echo "<tr><td colspan='2' align=center><font size=3 color='#000080' face='arial' align=center><b>LA CONSIGNACION INTERNA ".$consecutivoT." HA SIDO ANULADA</td><tr>";
	echo "<tr><td colspan='2' align='center'><input type='button' name='aceptar' value='ACEPTAR' onclick='javascript:enter2()'></td><tr>";
	echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
	echo "</table></fieldset></form>";
}

function pintarAlert6($consecutivoT)
{
	global $wbasedato;
	echo "</table>";
	echo"<form action='consignacionN.php' method='post' name='forma1' ><CENTER><fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080>";
	echo "<table align='center' border=0 bordercolor=#000080 width=700 style='border:solid;'>";
	echo "<tr><td colspan='2' align=center><font size=3 color='#000080' face='arial' align=center><b>LA CONSIGNACION INTERNA ".$consecutivoT." YA NO PUEDE SER ANULADA</td><tr>";
	echo "<tr><td colspan='2' align='center'><input type='button' name='aceptar' value='ACEPTAR' onclick='javascript:enter2()'></td><tr>";
	echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
	echo "</table></fieldset></form>";
}

function pintarAlert7()
{
	global $wbasedato;
	echo "</table>";
	echo"<form action='consignacionN.php' method='post' name='forma1' ><CENTER><fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080>";
	echo "<table align='center' border=0 bordercolor=#000080 width=700 style='border:solid;'>";
	echo "<tr><td colspan='2' align=center><font size=3 color='#000080' face='arial' align=center><b>DEBE SELECCIONAR AL MENOS UN REGISTRO PARA REALIZAR LA CONSIGNACIÓN</td><tr>";
	echo "<tr><td colspan='2' align='center'><input type='button' name='aceptar' value='ACEPTAR' onclick='javascript:enter2()'></td><tr>";
	echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
	echo "</table></fieldset></form>";

}

function pintarAlert8()
{
	echo '<script language="Javascript">';
	echo 'alert ("LOS VALORES PARA CONSIGNACION INTERNA NO DEBEN SER MAYORES AL VALOR DEL DOCUMENTO RESPECTIVO")';
	echo '</script>';
}

function pintarFormulario($fuente, $numero, $fecha, $valor, $documento, $banco, $radio, $pagos, $total, $totalF, $cajCod, $cajDes, $consecutivoT, $clase, $id, $fuenteT, $radio2, $cantidad, $con)
{
	global $wbasedato;
	$clase1="class='texto2'";
	$clase2="class='texto5'";

	echo "<form name='forma1' action='consignacionN.php' method='post'>";

	for($i=0; $i < count($pagos['codigo']); $i++)
	{

		if (isset ($fuente[$i][0]))
		{
			if (!isset ($radio2[$i]))
			{
				$radio2[$i]='';
			}else if ($radio2[$i]!='')
			{
				$radio2[$i]='checked';
			}

			echo "<table align='center'>";
			if ($clase==1)
			{
				if ($pagos['tipo'][$i]=='on')
				echo "<tr><td class='titulo2' colspan='8'><input type='checkbox' name='radio2[".$i."]' ".$radio2[$i]." onclick='enter6()'>&nbsp;&nbsp;FORMA DE PAGO: ".$pagos['codigo'][$i]."-".$pagos['nombre'][$i]."</td></tr>";
				else
				echo "<tr><td class='titulo2' colspan='6'><input type='checkbox' name='radio2[".$i."]' ".$radio2[$i]." onclick='enter6()'>&nbsp;&nbsp;FORMA DE PAGO: ".$pagos['codigo'][$i]."-".$pagos['nombre'][$i]."</td></tr>";
			}
			else
			{
				if ($pagos['tipo'][$i]=='on')
				echo "<tr><td class='titulo2' colspan='8'>&nbsp;&nbsp;FORMA DE PAGO: ".$pagos['codigo'][$i]."-".$pagos['nombre'][$i]."</td></tr>";
				else
				echo "<tr><td class='titulo2' colspan='6'>&nbsp;&nbsp;FORMA DE PAGO: ".$pagos['codigo'][$i]."-".$pagos['nombre'][$i]."</td></tr>";
			}

			if ($clase==1)
			echo "<tr><td class='titulo3'>SELECCIONAR</td>";
			else
			echo "<tr><td class='titulo3'>&nbsp;</td>";
			echo "<td class='titulo3'>FUENTE</td>";
			echo "<td class='titulo3'>N RECIBO</td>";
			echo "<td class='titulo3'>FECHA</td>";

			if ($pagos['tipo'][$i]=='on')
			{
				echo "<td class='titulo3'>N DOCUMENTO</td>";
				echo "<td class='titulo3'>DATOS BANCO</td>";
			}
			echo "<td class='titulo3'>VALOR</td>";
			echo "<td class='titulo3'>VALOR A CONSIGNAR</td>";
			echo "</tr>";


			for($j=0; $j < count($fuente[$i]); $j++)
			{

				if ($clase1=="class='texto2'")
				{
					$clase1="class='texto1'";
					$clase2="class='texto4'";
				}
				else
				{
					$clase1="class='texto2'";
					$clase2="class='texto5'";
				}

                /*if( !isset($radio2) )
                    $radio2 = array();
                if( !isset($radio2[$i]) )
                    $radio2[$i] = array();

                if( !isset($radio) )
                    $radio = array();
                if( !isset($radio[$i]) )
                    $radio[$i] = array();

                echo "<br>edb->".print_r($radio[$i]);
                echo "<br>edb->".print_r($radio[$i][$j]);*/
				if ($radio2[$i]=='')
				{
					$radio[$i][$j]='';
				}

				echo "<tr>";

				if ($clase==1)
				echo "<td ".$clase1."><input type='checkbox' name='radio[".$i."][".$j."]' ".$radio[$i][$j]." ></td>";
				else
				echo "<td ".$clase1.">&nbsp;</td>";
				echo "<td ".$clase1.">".$fuente[$i][$j]."</td>";
				echo "<td ".$clase1.">".$numero[$i][$j]."</td>";
				echo "<td ".$clase1.">".$fecha[$i][$j]."</td>";

				if ($pagos['tipo'][$i]=='on')
				{
					echo "<td ".$clase1.">".$documento[$i][$j]."</td>";
					echo "<td ".$clase1.">".$banco[$i][$j]."</td>";
				}
				echo "<td ".$clase1.">$ ".number_format($valor[$i][$j],0,"",".")."</td>";

				if ($con)
				{
					echo "<td ".$clase1."><input type='text' name='cantidad[".$i."][".$j."]' value='".$cantidad[$i][$j]."'></td>";
				}
				else
				{
					echo "<td ".$clase1.">$".number_format($cantidad[$i][$j],0,"",".")."</td>";
					echo "<input type='HIDDEN' name='cantidad[".$i."][".$j."]' value='".$cantidad[$i][$j]."'>";
				}
				echo "</tr>";


				echo "<input type='HIDDEN' name='fuente[".$i."][".$j."]' value='".$fuente[$i][$j]."'>";
				echo "<input type='HIDDEN' name='id[".$i."][".$j."]' value='".$id[$i][$j]."'>";
				echo "<input type='HIDDEN' name='numero[".$i."][".$j."]' value='".$numero[$i][$j]."'>";
				echo "<input type='HIDDEN' name='fecha[".$i."][".$j."]' value='".$fecha[$i][$j]."'>";
				echo "<input type='HIDDEN' name='valor[".$i."][".$j."]' value='".$valor[$i][$j]."'>";
				echo "<input type='HIDDEN' name='documento[".$i."][".$j."]' value='".$documento[$i][$j]."'>";
				echo "<input type='HIDDEN' name='banco[".$i."][".$j."]' value='".$banco[$i][$j]."'>";
			}
			if ($pagos['tipo'][$i]=='on')
			echo "<td class='acumulado1' colspan='7'>TOTAL</td>";
			else
			echo "<td class='acumulado1' colspan='5'>TOTAL</td>";
			echo "<td class='acumulado1' >$ ".number_format($total[$i],0,"",".")."</td>";
			echo "</table></br>";
		}

		echo "<input type='HIDDEN' name='pagos[codigo][".$i."]' value='".$pagos['codigo'][$i]."'>";
		echo "<input type='HIDDEN' name='pagos[nombre][".$i."]' value='".$pagos['nombre'][$i]."'>";
		echo "<input type='HIDDEN' name='pagos[tipo][".$i."]' value='".$pagos['tipo'][$i]."'>";
		echo "<input type='HIDDEN' name='total[".$i."]' value='".$total[$i]."'>";


	}
	echo "<input type='HIDDEN' name='bandera1' value='1'>";
	echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
	echo "<input type='HIDDEN' name='totalF' value='".$totalF."'>";
	echo "<input type='HIDDEN' name='consecutivoT' value='".$consecutivoT."'>";
	echo "<input type='HIDDEN' name='fuenteT' value='".$fuenteT."'>";
}

function pintarBoton1($totalF, $bancos, $wbanF, $auto)
{
	// * despliega lista de bancos y valor a consignar
	global $limitVar;
	global $limitVarMostrados;

	echo "<table align='center'>";
	echo "<tr><td class='titulo4'>TOTAL A GRABAR: ".($limitVarMostrados/9)." DE ".($limitVar/9)." REGISTROS</td></tr>";
	echo "<tr><td class='titulo4' >VALOR A CONSIGNAR: $ ".number_format($totalF,0,"",".")."</td></tr>";
	echo "<tr><td class='titulo4' >Banco destino: </font></b><select name='wbanF'>";
	for ($i=0;$i<count($bancos['codigo']);$i++)
	{
		if($bancos['codigo'][$i]!='')
		echo "<option>".$bancos['codigo'][$i]."-".$bancos['nombre'][$i]."-".$bancos['cuenta'][$i]."</option>";
	}
	echo "</select></td>";
	//2007-05-17 se agrega numero de autorizacion
	echo "<tr><td class='titulo4' >Numero de autorizacion: <input type='text' name='auto' value='".$auto."'></td></tr>";
	echo "<tr><td class='titulo4' >&nbsp;</td></tr>";
	echo "<tr><td class='titulo4' ><input type='checkbox' name='consignar'>Consignar	&nbsp;<input type='button' value='ACEPTAR'  onclick='javascript:enter()'></td></tr>";
	echo "</table></br>";
	echo "</form>";
}

function pintarBoton2($totalF, $consulta, $auto)
{

	$fila='enter5("'.$totalF.'", "'.$consulta.'")';
	echo "<table align='center'>";
	echo "<tr><td class='titulo4' >VALOR TOTAL CONSIGNACIÓN: $ ".number_format($totalF,0,"",".")."</td></tr>";
	//2007-05-17 se agrega numero de autorizacion
	echo "<tr><td class='titulo4' >NÚMERO DE AUTORIZACIÓN: ".$auto."</td></tr>";
	echo "<tr><td class='titulo4' >&nbsp;</td></tr>";
	echo "<tr><td class='titulo4' > <input type='button' value='ANULAR'  onclick='javascript:".$fila."'> <input type='button' value='VOLVER'  onclick='javascript:enter4()'></td></tr>";
	echo "</table></br>";
	echo "</form>";

}

/*********************************************** PROGRAMA PRINCIPAL *************************************/

session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
    /*PARAMETRO DE SEGURIDAD */
    $seguridad = "NO";






	$wactualiz="Agosto 5 de 2013";

	//obtengo los datos de la caja
	$cco = '';
	$cajCod = '';
	$cajDes = '';

	if (!isset($radio))
	{
		$radio[0][0]='';
	}

	if (!isset($radio2))
	{
		$radio2[0]='';
	}

	//consulto los datos de la que esta grabada como caja general
	consultarBanco($cajCod, $cajDes);
	//consulto los datos de la caja del usuario
	consultarCaja(substr($user,2), $cco, $cajCod2, $cajDes2);

	pintarEncabezado($wactualiz);

	if($cajCod != '')
	{
		//indica que la persona ya ha dado orden de hacer la consignacion sin seleccionar el banco destino
		if (isset($consignar) and (!isset($wbanF) or $wbanF=='' ) )
		{
			pintarAlert3(); //DEBE SELECCIONAR LA CAJA CUANDO VA A TRANSLADAR
			unset($consignar);
		}

		if (isset($consulta) and $consulta!='')// consulta de una cosignacion
		{
			$fuenteT=consultarFuente();
			consultarEncabezado($cajCod,$consulta, $cco, $fuenteT, $wbanF, $totalF, $fec, $auto );
			if ($wbanF!='')
			{
				consultarRegistros ($cco, $fuenteT, $consulta, $fuente, $numero, $fecha, $valor, $documento, $banco, $pagos, $total, $id, $cantidad);
				pintarConsulta2($cajCod, $cajDes, $consulta, $fec, $wbanF);
				pintarFormulario($fuente, $numero, $fecha, $valor, $documento, $banco, array(), $pagos, $total, $totalF, $cajCod, $cajDes, $consulta,2, $id, $fuenteT, $radio2, $cantidad, false );
				pintarBoton2($totalF, $consulta, $auto); //determina la actividad de guardar translados
			}else //no se obtienen resultados para ese numero de envio
			{
				pintarAlert4($cajCod, $consulta);
			}

		}else // generación o anulacion de un translado
		{

			if (!isset ($bandera1) or $bandera1!=3) //generacion de informacion para la consignacion
			{
				consultarConsecutivo($consecutivoT, $fuenteT);

				if(!isset($consignar)) //proceso previo antes de guardar el translado
				{
					//pinta los detalles iniciales de la consignacion
					pintarConsulta1($cajCod, $cajDes, $consecutivoT);

					if (!isset($bandera1) or $bandera1==2)
					{
						//consulto formas de pago
						$pagos=consultarPagos();
						$contador=0;  //me cuenta cuantas formas de pago no tienen que ser consignadas
						//realizaremos la distribución por tipo de pago para cada recibo
						$limitVar = 0;
						$limitVarMostrados = 0;
						for ($i=0;$i<count($pagos['codigo']);$i++)
						{
							//consulto para cada forma de pago los recibos
							$registro=consultarRecibos($pagos['codigo'][$i], $cajCod, $cco, $cajCod2);
							if ($registro)
							{
								$radio2[$i]='checked';
								//organizo un vector para la forma de pago
								for ($j=0;$j<count($registro['numero']);$j++)
								{
									if( true || $limitVar < 900 ){
										$id[$i][$j]=$registro['id'][$j];
										$fuente[$i][$j]=$registro['fuente'][$j];
										$numero[$i][$j]=$registro['numero'][$j];
										$fecha[$i][$j]=$registro['fecha'][$j];
										$valor[$i][$j]=$registro['valor'][$j];
										$documento[$i][$j]=$registro['documento'][$j];
										$banco[$i][$j]=$registro['banco'][$j];
										$radio[$i][$j]='checked';
										$cantidad[$i][$j]=$valor[$i][$j];
										$contador++;
										$limitVarMostrados += 9;
									}
									$limitVar += 9;
								}

							}
						}
						$wbanF='';
					}else
					{
						$contador=1;
					}

					if ($contador>0)
					{
						// adecuo los valores de seleccion y calculo los totales
						calcularTotal($pagos, $fuente, $valor, $radio2, $total, $totalF, $radio, $cantidad);
						$bancos=consultarBancos($wbanF, $cajCod);
						pintarFormulario($fuente, $numero, $fecha, $valor, $documento, $banco, $radio, $pagos, $total, $totalF, $cajCod, $cajDes, $consecutivoT,1, $id, $fuenteT, $radio2, $cantidad, true);
						if (!isset($auto))
						{
							$auto='';
						}
						pintarBoton1($totalF, $bancos, $wbanF, $auto); //determina la actividad de guardar translados
					}else
					{
						pintarAlert2($cajCod);
					}
				}else// REALIZO LAS ACTIVIDADES DE TRANSLADO Y SU ALMACENAMIENTO
				{
					//calculo el total
					calcularTotal($pagos, $fuente, $valor, $radio2, $total, $totalF, $radio, $cantidad);

					if( $totalF > 0 )
					{
						//guardar el encabezado
						grabarEncabezado($fuenteT, $consecutivoT, $cajCod, $wbanF, $totalF, substr($user,2), $auto);
						$exp=explode('-', $wbanF);

						for ($i=0;$i<count($pagos['codigo']);$i++)
						{
							if (isset($fuente[$i]))
							{
								for ($j=0;$j<count($fuente[$i]);$j++)
								{
									if (isset ($radio[$i][$j]) and $radio[$i][$j]!='')
									{
										grabarDetalle($fuenteT, $consecutivoT, $numero[$i][$j], $fuente[$i][$j], $valor[$i][$j], $pagos['codigo'][$i], $cajCod, $id[$i][$j], substr($user,2));
										grabarMovimientos($fuenteT, $consecutivoT, $numero[$i][$j], $fuente[$i][$j], $cantidad[$i][$j], $exp[0], $id[$i][$j], $valor[$i][$j], $cajCod2, substr($user,2), $pagos['codigo'][$i]);
									}
								}
							}
						}


						incrementarConsecutivo($fuenteT);

						consultarEncabezado($cajCod,$consecutivoT, $cco, $fuenteT, $wbanF, $totalF, $fec, $auto ); //consultar el documento guardado

						if (isset($fuenteT) and $fuenteT!='')
						{
							consultarRegistros ($cco, $fuenteT, $consecutivoT, $fuente2, $numero2, $fecha2, $valor2, $documento2, $banco2, $pagos2, $total2, $id2, $cantidad);
							pintarConsulta3($cajCod, $cajDes, $consecutivoT, $fec, $wbanF);
							pintarFormulario($fuente2, $numero2, $fecha2, $valor2, $documento2, $banco2, '', $pagos2, $total2, $totalF, $cajCod, $cajDes, $consecutivoT ,2, $id2, $fuenteT, $radio2, $cantidad, false);
							pintarBoton2($totalF, $consecutivoT, $auto); //determina la actividad de guardar translados
						}else //no se obtienen resultados para ese numero de envio
						{
							pintarAlert4($cajCod, $consecutivoT);
						}
					}else
					{
						pintarAlert7();
					}
				}
			}
			else  // 	PROCESO DE ANULACION
			{
				$permiso=consultarPermiso($fuenteT, $consecutivoT);
				if ($permiso) //se realiza anulación
				{
					for ($i=0;$i<count($pagos['codigo']);$i++)
					{
						if (isset($fuente[$i]))
						{
							for ($j=0;$j<count($fuente[$i]);$j++)
							{
								devolverMovimientos($fuenteT, $consecutivoT,  $numero[$i][$j], $fuente[$i][$j], $cajCod, $pagos['codigo'][$i], $id[$i][$j], $valor[$i][$j], $cantidad[$i][$j]);

								anularRegistro($consecutivoT, $fuenteT, $numero[$i][$j], $fuente[$i][$j],  $pagos['codigo'][$i], $id[$i][$j]);
							}
						}
					}
					anularEncabezado($fuenteT, $consecutivoT, $cajCod);
					pintarAlert5($consecutivoT);

				}
				else //mensaje de no poder realizar anulacion
				{
					pintarAlert6($consecutivoT);
				}
			}
		}
	}else
	{
		pintarAlerta1();
	}
	include_once("free.php");

}

?>
<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>
</body>
</html>
