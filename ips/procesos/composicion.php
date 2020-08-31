<html>
<head>
  <title>Cambio de composición</title>
  
 <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
    	.titulo1{color:#FFFFFF;background:#006699;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}	
    	.titulo2{color:#003366;background:#A4E1E8;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.titulo3{color:#003366;background:#57C8D5;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
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
   	if(document.forma1.transladar.checked==true)
   	{
   		var fRet;
   		fRet = confirm('Esta seguro de que desea realizar el cambio de composición');
   		if (fRet==false)
   		{
   			document.forma1.transladar.checked=false;
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

							</script>
   
<?php
include_once("conex.php");

/*************************************************************************************
*     PROGRAMA PARA EL CAMBIO DE COMPOSICIÓN DE EFECTIVO DE UNA CAJA    *
*                                                                 *
*************************************************************************************/
//=================================================================================================================================
//PROGRAMA:	composicion.php
//AUTOR: Carolina Castaño.
$wautor="Carolina Castaño P.";

//TIPO DE SCRIPT: principal
//RUTA DEL SCRIPT: matrix\pos\procesos\composicion.php

//HISTORIAL DE REVISIONES DEL SCRIPT:
//-------------------I------------------------I---------------------------------------------------------------------
//	  FECHA           I     AUTOR              I   MODIFICACION
//-------------------I------------------------I---------------------------------------------------------------------
//  2006-11-20       I Carolina Castaño  P    I creación del script.
//-------------------I------------------------I---------------------------------------------------------------------
//-------------------I------------------------I---------------------------------------------------------------------
//  2007-05-03       I Carolina Castaño  P    I El cambio de composicion afecta tambien el banco incial
//                   I                        I Cuando se crea un nuevo registro en la 22 tambien pone el banco inicial
//-------------------I------------------------I---------------------------------------------------------------------

//DESCRIPCION:Este programa sirve para realizar cambios de composicion de efectivo a otras formas de pago

//TABLAS QUE MODIFICA:
// $wbasedato."_000030: Busqueda de cajeros autorizados, select
// $wbasedato."_000028: Busqueda de datos de caja, select


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

function consultarCaja($user, &$cco, &$cajCod, &$cajDes)
{
	//extrae los datos de la caja a la que pertenece el usuario

	global $conex;
	global $wbasedato;

	$q="select Cjecco, Cjecaj, Cajcod, Cajdes "
	."from ".$wbasedato."_000030, ".$wbasedato."_000028 "
	."where	Cjeusu = '".$user."' and cjeest='on' "
	."and	Cajcod = MID(Cjecaj, 1,2) and cajest='on' ";

	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);

	if ($num > 0)
	{
		$row=mysql_fetch_array ($err);
		$cco = substr($row['Cjecco'],0,4);
		$cajCod = $row['Cajcod'];
		$cajDes = $row['Cajdes'];
	}
}

function consultarBancos()
{
	//consulto las formas de pago existentes para agrupar

	global $conex;
	global $wbasedato;

	$q= " SELECT bancod, bannom, bancue, banrec "
	."   FROM ".$wbasedato."_000069 "
	."  where banest='on' ";

	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);

	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$bancos['codigo'][$i]=$row[0];
			$bancos['nombre'][$i]=$row[1];
			$bancos['cuenta'][$i]=$row[2];
			$bancos['destino'][$i]=$row[3];
		}
		return $bancos;
	}else
	{
		return false;
	}
}

//2007-05-03 Se crea esta funcion para que cada vez que se escoja la forma de pago me arrastre el banco destino
function consultarDestinos($fpa)
{
	//consulto las formas de pago existentes para agrupar

	global $conex;
	global $wbasedato;

	$exp=explode('-',$fpa);
	$q = "SELECT bancod, bannom, bancue "
	."  FROM ".$wbasedato."_000023, ".$wbasedato."_000069 "
	." WHERE fpacod = '".$exp[0]."'"
	."   AND fpaest = 'on' "
	."   AND fpacba = bancod "
	."   AND banest='on' ";

	$res = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA ENCONTRADO EL BANCO PARA LA FORMA DE PAGO ".mysql_error());
	$row = mysql_fetch_array($res);
	$destinos[0]=$row[0]."-".$row[1]."-".$row[2];
	return $destinos;
}

function consultarConsecutivo(&$consecutivoT, &$fuenteT)
{
	//consulto las formas de pago existentes para agrupar

	global $conex;
	global $wbasedato;

	$q="select carfue, carcon "
	."from	".$wbasedato."_000040 "
	."where	carest = 'on' and carcom='on' ";

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
	."where	carest = 'on' and carcom='on' ";

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

function consultarFormas($wformaF)
{
	//consulto las formas de pago diferentes a efectivo

	global $conex;
	global $wbasedato;

	if ($wformaF!='')
	{
		$exp=explode ('-',$wformaF);

		$formas['codigo'][0]=$exp[0];
		$formas['nombre'][0]=$exp[1];

		$q="select fpache, fpatar "
		."from	".$wbasedato."_000023 "
		."where	fpaest='on' and fpacod='".$exp[0]."' and fpades='".$exp[1]."' ";

		$err = mysql_query($q,$conex);
		$row = mysql_fetch_array($err);
		if ($row[0]=='on' or $row[1]=='on')
		{
			$formas['obliga'][0]='on';
		}
		else
		{
			$formas['obliga'][0]='off';
		}

		$q="select fpacod, fpades, fpache, fpatar "
		."from	".$wbasedato."_000023 "
		."where	fpaest='on' and fpacod<>'".$exp[0]."' and fpades<>'".$exp[1]."' and fpacod<>'99' and fpades<>'EFECTIVO'";

	}else
	{
		$cajas['codigo'][0]='';
		$cajas['nombre'][0]='';

		$q="select fpacod, fpades, fpache, fpatar "
		."from	".$wbasedato."_000023 "
		."where	fpaest='on' and fpacod<>'99' and fpades<>'EFECTIVO'";
	}
	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);

	if ($num>0)
	{
		for ($i=1;$i<=$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$formas['codigo'][$i]=$row[0];
			$formas['nombre'][$i]=$row[1];

			if ($row[2]=='on' or $row[3]=='on')
			{
				$formas['obliga'][$i]='on';
			}
			else
			{
				$formas['obliga'][$i]='off';
			}

		}

	}
	return $formas;
}

function consultarRecibos($codPago, $cajCod, $cco)
{
	//consulta los recibos pagados en efectivo para una caja retornando un vector de recibos

	global $conex;
	global $wbasedato;

	$q="select A.Renfue, A.Rennum, A.Renfec, C.rfpvfp, C.rfpdan, C.rfpobs, C.id "
	."from	".$wbasedato."_000020 A, ".$wbasedato."_000040 B, ".$wbasedato."_000022 C "
	."where	carrec = 'on' "
	."and	B.Carest = 'on' "
	."and	A.Renfue = B.Carfue "
	."and	C.Rfpcaf = '".$cajCod."' "
	."and	C.rfpfue= A.Renfue "
	."and	C.rfpnum = A.rennum "
	."and	C.rfpcco = A.rencco "
	."and	C.rfpfpa = '".$codPago."' "
	."and	C.rfpest='on' "
	."and	C.rfpecu<>'C' "
	."and	C.rfpecu<>'I' ";


	$err = mysql_query($q,$conex);
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
			$registro['observacion'][$i]=$row[5];
		}
		return $registro;
	}else
	{
		return false;
	}
}

function grabarEncabezado($fuenteT, $consecutivoT, $forCod, $wformaF, $totalF, $cajCod, $wusuario)
{
	//consulto las formas de pago existentes para agrupar

	global $conex;
	global $wbasedato;


	$q= " INSERT INTO ".$wbasedato."_000075 (   Medico       ,   Fecha_data,   Hora_data,   tenfue,           tennum ,              tencai  ,     tencaf    ,    tenval    ,        tenfec ,         tenest    , Seguridad        ) "
	."         VALUES ('".$wbasedato."',   '".date('Y-m-d')."', '".(string)date("H:i:s")."' ,'".$fuenteT."', '".$consecutivoT."', '".$forCod."', '".$wformaF."' , '".$totalF."' , '".date('Y-m-d')."'  , 'on' ,  'C-".$wusuario."') ";

	$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
}


function grabarDetalle($fuenteT, $consecutivoT, $numero, $fuente, $valor, $forma, $cajCod, $id, $wusuario)
{
	//consulto las formas de pago existentes para agrupar

	global $conex;
	global $wbasedato;

	$q= " INSERT INTO ".$wbasedato."_000076 (   Medico       ,   Fecha_data,   Hora_data,     tdefue,           tdenum ,              tdendo  ,     tdefdo   ,    	tdeval    ,   tdefpa ,     tdereg,        tdeest    ,     Seguridad        ) "
	."         VALUES ('".$wbasedato."',   '".date('Y-m-d')."', '".(string)date("H:i:s")."' ,'".$fuenteT."', '".$consecutivoT."', '".$numero."', '".$fuente."' , '".$valor."' , '".$forma."'  ,  '".$id."'  ,   'on' ,  'C-".$wusuario."') ";

	$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
}

function consultarEncabezado($cajCod, $consulta, $cco, $fuenteT, &$wformaF, &$totalF, &$fec )
{

	//consulto el encabezado de un cambio de composicion dado

	global $conex;
	global $wbasedato;

	$q="select tenfue, tencaf, tenval, tenfec, fpades "
	."from	".$wbasedato."_000075, ".$wbasedato."_000023 "
	."where	tenest = 'on' and tennum='".$consulta."' and tencai='99' and tencaf=fpacod and tenfue='".$fuenteT."' ";


	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);

	if ($num>0)
	{
		$row = mysql_fetch_array($err);
		$wformaF=$row[1].'-'.$row[4];
		$fuenteT=$row[0];
		$totalF=$row[2];
		$fec=$row[3];
	}else
	{
		$wcajF='';
		$totalF='';
		$fec='';
	}
}

function consultarEfectivo()
{
	//consulto las formas de pago existentes para agrupar

	global $conex;
	global $wbasedato;

	$q = "SELECT fpacba "
	."  FROM ".$wbasedato."_000023 "
	." WHERE fpacod = '99'"
	."   AND fpaest = 'on' ";

	$res = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA ENCONTRADO EL CODIGO DESTINO PARA EL EFECTIVO ".mysql_error());
	$row = mysql_fetch_array($res);
	return $row[0];
}

function consultarRegistros ($cco, $fuenteT, $consulta, &$fuente, &$numero, &$fecha, &$valor, &$componer, &$wdocum, &$wBancoO, &$wobserv, &$wubica, &$wBancoF, &$id)
{
	//consulto los registros detalle de un encabezao dado y los organizo en vectores según la forma de pago

	global $conex;
	global $wbasedato;


	$q=" select tdereg from	".$wbasedato."_000076 where	tdeest='on' and tdenum='".$consulta."' and tdefue='".$fuenteT."'  ";
	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);


	$q=" select rfpdan from	".$wbasedato."_000022 where	rfpest = 'on' and rfpnum='".$consulta."' and rfpfue='".$fuenteT."' and rfpfpa<>'99' ";
	$errb = mysql_query($q,$conex);

	for ($i=0;$i<$num;$i++)
	{
		$row2 = mysql_fetch_array($err);
		$rowb = mysql_fetch_array($errb);

		$q=" Select C.rfpfue, C.rfpnum, C.rfpfpa, C.rfpvfp, C.rfpobs, C.rfpban, C.rfppla, C.rfpaut, C.rfpdan, C.rfpcco, D.fpache, D.fpatar "
		. " from ".$wbasedato."_000022 C, ".$wbasedato."_000023 D  where C.id=".$rowb[0]." and C.rfpfpa=D.fpacod and fpaest='on' ";

		$res = mysql_query($q,$conex);
		$row = mysql_fetch_array($res);

		$fuente[$i]=$row[0];
		$numero[$i]=$row[1];
		$cco2[$i]=$row[9];
		$componer[$i]=$row[3];
		$che[$i]=$row[10];
		$tar[$i]=$row[11];
		$id[$i]=$row2[0];

		if ($che[$i]=='on' or $tar[$i]=='on')
		{
			$q="select bannom "
			."from	".$wbasedato."_000069 "
			."where	bancod = '".$row[4]."' "
			."and	banest = 'on' ";

			$res = mysql_query($q,$conex);
			$row2 = mysql_fetch_array($res);
			$wBancoO=$row[4].'-'.$row2[0];
			$wobserv=$row[7];
		}
		else
		{
			$wobserv=$row[4];
			$wBancoO=$row[7];
		}

		$wubica=$row[6];
		$wdocum=$row[8];


		$q="select bannom "
		."from	".$wbasedato."_000069 "
		."where	bancod = '".$row[5]."' "
		."and	banest = 'on' ";

		$res = mysql_query($q,$conex);
		$row2 = mysql_fetch_array($res);

		$wBancoF=$row[5].'-'.$row2[0];

		$q=" Select tdeval from ".$wbasedato."_000076  where	tdenum='".$consulta."' and tdefue='".$fuenteT."' and tdefdo='".$fuente[$i]."' and tdendo='".$numero[$i]."' and  tdereg=".$id[$i];
		$res = mysql_query($q,$conex);
		$row = mysql_fetch_array($res);
		$valor[$i]=$row[0];

		$q=" Select renfec from ".$wbasedato."_000020  where renfue='".$fuente[$i]."' and rennum='".$numero[$i]."' and  rencco=".$cco2[$i];
		$res = mysql_query($q,$conex);
		$row = mysql_fetch_array($res);
		$fecha[$i]=$row[0];

	}
}

function grabarMovimientos($fuenteT, $consecutivoT, $numero, $fuente, $cantidad, $forma, $id, $valor, $wdocum, $wBancoO, $wBancoF, $wobserv, $wubica, $caja, $wusuario)
{
	//cambio el estado de los recibo

	global $conex;
	global $wbasedato;

	if ($wBancoO=='')
	{
		$wBancoO=$wobserv;
		$wauto='';
	}
	else
	{
		$wauto=$wobserv;
		$exp=explode('-',$wBancoO);
		$wBancoO=$exp[0];

	}

	$exp=explode('-',$wBancoF);
	$wBancoF=$exp[0];

	$q="select rfpcco  from	 ".$wbasedato."_000022 C where	id = '".$id."' ";
	$res = mysql_query($q,$conex);
	$row2 = mysql_fetch_array($res);
	$wcco=$row2[0];

	if ($valor==$cantidad)
	{
		//2007-05-03 se agrega actualizacion de rfpbai
		$q=" UPDATE ".$wbasedato."_000022 SET rfpban='".$wBancoF."', rfpbai='".$wBancoF."', rfpfpa = '".$forma."', rfpvfp = '".$cantidad."', rfpdan='".$wdocum."', rfpobs='".$wBancoO."', rfppla='".$wubica."', rfpaut='".$wauto."' WHERE rfpfpa='99'and rfpnum='".$numero."' and rfpfue='".$fuente."' and rfpest='on' and id='".$id."' ";

		$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

		$q= " INSERT INTO ".$wbasedato."_000022 (   Medico       ,   Fecha_data,           Hora_data,                    rfpfue              ,   rfpnum    ,                rfpfpa              ,  rfpvfp        ,   rfpdan         ,   rfpobs     , rfpest,   rfpcco  ,    rfppla,           rfpaut,       rfpecu,   rfpcaf,            rfpban,      Seguridad        ) "
		."                             VALUES ('".$wbasedato."','".date('Y-m-d')."', '".(string)date("H:i:s")."' ,    '".$fuenteT."',          ".$consecutivoT.",        '".$forma."'         ,  ".$valor.",        '".$id."',      '',  'on'  ,  '".$wcco."', '', '',      'N',   '".$caja."',    '', 'C-".$wusuario."') ";

		$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	}
	else if ($valor>$cantidad)
	{
		$valor=$valor-$cantidad;

		$q=" UPDATE ".$wbasedato."_000022 SET rfpvfp='".$valor."' WHERE rfpfpa='99'and rfpnum='".$numero."' and rfpfue='".$fuente."' and rfpest='on' and id=".$id." ";
		$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

		$q= " INSERT INTO ".$wbasedato."_000022 (   Medico       ,   Fecha_data,           Hora_data,                    rfpfue              ,   rfpnum    ,                rfpfpa              ,  rfpvfp        ,   rfpdan         ,   rfpobs     , rfpest,   rfpcco  ,    rfppla,           rfpaut,       rfpecu,   rfpcaf,            rfpban,      Seguridad        ) "
		."                             VALUES ('".$wbasedato."','".date('Y-m-d')."', '".(string)date("H:i:s")."' ,'".$fuenteT."',          ".$consecutivoT.",        '99'         ,           ".$valor.",     '".$id."',      '',  'on'  ,  '".$wcco."', '', '',      'N',   '".$caja."',    '', 'C-".$wusuario."') ";

		$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

		//2007-05-03 se inserta tambien valor de rfpbai
		$q= " INSERT INTO ".$wbasedato."_000022 (   Medico       ,   Fecha_data,           Hora_data,                    rfpfue              ,   rfpnum    ,                rfpfpa              ,  rfpvfp        ,   rfpdan         ,   rfpobs     , rfpest,   rfpcco  ,    rfppla,           rfpaut,       rfpecu,   rfpcaf,            rfpban,   rfpbai,   Seguridad        ) "
		."                             VALUES ('".$wbasedato."','".date('Y-m-d')."', '".(string)date("H:i:s")."' ,'".$fuente."',          ".$numero.",                '".$forma."'         ,  ".$cantidad.",    '".$wdocum."',      '".$wBancoO."',  'on'  ,  '".$wcco."', '".$wubica."', '".$wauto."',      'S',   '".$caja."',    '".$wBancoF."',  '".$wBancoF."', 'C-".$wusuario."') ";

		$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

		$id2= mysql_insert_id($conex);

		$q= " INSERT INTO ".$wbasedato."_000022 (   Medico       ,   Fecha_data,           Hora_data,                    rfpfue              ,   rfpnum    ,                rfpfpa              ,  rfpvfp        ,   rfpdan         ,   rfpobs     , rfpest,   rfpcco  ,    rfppla,           rfpaut,       rfpecu,   rfpcaf,            rfpban,      Seguridad        ) "
		."                             VALUES ('".$wbasedato."','".date('Y-m-d')."', '".(string)date("H:i:s")."' ,'".$fuenteT."',          ".$consecutivoT.",        '".$forma."'         ,  ".$cantidad.",    '".$id2."',      '',  'on'  ,  '".$wcco."', '', '',      'N',   '".$caja."',    '', 'C-".$wusuario."') ";

		$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	}
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




function consultarPermiso($id, $fuen, $docu)
{
	global $conex;
	global $wbasedato;

	$permiso=true;

	for ($j=0;$j<count($id);$j++)
	{
		$q="select rfpnum  from	".$wbasedato."_000022 where	id = '".	$id[$j]."' and	rfpest = 'on' and rfpecu = 'C' ";
		$err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);

		if ($num>0)
		{

			$permiso=false;
		}

		$q="select rfpnum  from	".$wbasedato."_000022 where	id = '".	$id[$j]."' and	rfpest = 'on' and rfpecu = 'I' ";
		$err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);

		if ($num>0)
		{

			$permiso=false;
		}

		$q="select rfpnum  from	".$wbasedato."_000022 where	id = '".	$id[$j]."' and	rfpest = 'on' and rfpecu = 'P' ";
		$err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);

		if ($num>0)
		{

			$permiso=false;
		}


		$q = " SELECT seguridad FROM ".$wbasedato."_000075  WHERE tenfue = '".	$fuen."' and tenest='on' and tennum = '".$docu."' ";
		$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		$row = mysql_fetch_array($res);

		$exp=explode('-', $row[0]);

		$q = " SELECT cjecaj FROM ".$wbasedato."_000030  WHERE cjeusu = '".	$exp[1]."'  ";


		$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		$row = mysql_fetch_array($res);
		$exp=explode('-', $row[0]);


		$q = " SELECT * FROM ".$wbasedato."_000022 A  WHERE A.id = '".	$id[$j]."' and A.rfpest='on' and A.rfpcaf<>'".$exp[0]."' ";

		$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		$num2 = mysql_num_rows($res);
		if ($num2>0)
		{
			$permiso=false;

		}

		//para ese id debo consultar los que tambien se generaron adicionales
		$q="select rfpnum, rfpfue, rfpcco  from	".$wbasedato."_000022 where	id = '".$id[$j]."' and	rfpest = 'on' ";
		$err = mysql_query($q,$conex);
		$row = mysql_fetch_array($err);

		$q="select id  from	".$wbasedato."_000022 where	id<>'".$id[$j]."' and rfpnum='".$row[0]."' and rfpfue='".$row[1]."' and rfpcco='".$row[2]."' ";
		$res = mysql_query($q,$conex);
		$num2 = mysql_num_rows($res);


		for ($i=0;$i<$num2;$i++)
		{
			//debo buscar el id que tenga el mismo registro
			$row2 = mysql_fetch_array($res);

			$q="select * from ".$wbasedato."_000022 where rfpdan='".$row2[0]."' and rfpnum='".	$docu."' and rfpfue='".	$fuen."' and rfpest='on' ";

			$err= mysql_query($q,$conex);
			$num3 = mysql_num_rows($err);

			if ($num3>0)
			{
				$q="select rfpnum  from	".$wbasedato."_000022 where	id = '".	$row2[0]."' and	rfpest = 'on' and rfpecu = 'C' ";
				$err = mysql_query($q,$conex);
				$num = mysql_num_rows($err);

				if ($num>0)
				{

					$permiso=false;

				}

				$q="select rfpnum  from	".$wbasedato."_000022 where	id = '".	$row2[0]."' and	rfpest = 'on' and rfpecu = 'I' ";
				$err = mysql_query($q,$conex);
				$num = mysql_num_rows($err);

				if ($num>0)
				{

					$permiso=false;

				}

				$q = " SELECT seguridad FROM ".$wbasedato."_000075  WHERE tenfue = '".	$fuen."' and tenest='on' and tennum = '".$docu."' ";
				$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				$row = mysql_fetch_array($err);

				$exp=explode('-', $row[0]);

				$q = " SELECT cjecaj FROM ".$wbasedato."_000030  WHERE cjeusu = '".	$exp[1]."'  ";
				$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				$row = mysql_fetch_array($err);
				$exp=explode('-', $row[0]);

				$q = " SELECT * FROM ".$wbasedato."_000022 A  WHERE A.id = '".	$row2[0]."' and A.rfpest='on' and A.rfpcaf<>'".$exp[0]."' ";
				$err= mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					$permiso=false;
				}
			}

		}

	}


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

function devolverMovimientos($fuenteT, $consecutivoT, $numero, $fuente, $cantidad, $forma, $id, $valor, $caja, $cajgen)
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
		//2007-05-03 se devuelve a caja general banco incial y banco final
		$q=" UPDATE ".$wbasedato."_000022 SET rfpban='".$cajgen."', rfpbai='".$cajgen."', rfpfpa = '99', rfpdan='', rfpobs='', rfppla='', rfpaut='' WHERE  rfpnum='".$numero."' and rfpfue='".$fuente."' and rfpest='on' and id=".$id." ";
		$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

	}
	else if ($valor>$cantidad)
	{
		$q=" UPDATE ".$wbasedato."_000022 SET rfpvfp=rfpvfp+".$cantidad." WHERE rfpfpa='99'and rfpnum='".$numero."' and rfpfue='".$fuente."' and rfpest='on' and id='".$id."' ";
		$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

		$q=" select rfpcco from ".$wbasedato."_000022 WHERE rfpfpa='99'and rfpnum='".$numero."' and rfpfue='".$fuente."' and rfpest='on' and id='".$id."' ";
		$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		$row3 = mysql_fetch_array($err);

		$q="select rfpdan  from	".$wbasedato."_000022 where	rfpdan not in (select tdereg from	".$wbasedato."_000076 where	tdenum='".$consecutivoT."' and tdefue='".$fuenteT."') and rfpnum='".$consecutivoT."' and rfpfue='".$fuenteT."'  ";
		$err = mysql_query($q,$conex);
		$num2 = mysql_num_rows($err);

		for ($j=0;$j<$num2;$j++)
		{
			//debo buscar el id que tenga el mismo registro
			$row2 = mysql_fetch_array($err);

			$q="select rfpfue, rfpnum, rfpcco from	".$wbasedato."_000022 where	id=".$row2[0];

			$res= mysql_query($q,$conex);
			$row6 = mysql_fetch_array($res);

			if ($row6[0]==$fuente and $row6[1]==$numero and $row6[2]==$row3[0])
			{
				$q=" UPDATE ".$wbasedato."_000022 SET rfpest='off'  WHERE id='".$row2[0]."' and rfpnum='".$numero."' and rfpfue='".$fuente."' and rfpest='on' ";
				$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
			}
		}

	}

	$q=" UPDATE ".$wbasedato."_000022 SET rfpest='off'  WHERE rfpnum='".$consecutivoT."' and rfpfue='".$fuenteT."' and rfpest='on' ";

	$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

}

function anularRegistro($consecutivoT, $fuenteT, $numero, $fuente, $id)
{
	global $conex;
	global $wbasedato;

	$q= "   UPDATE ".$wbasedato."_000076 SET tdeest = 'off' "
	."WHERE tdefue = '".$fuenteT."' and tdenum = '".$consecutivoT."' and tdendo = '".$numero."' and tdefdo = '".$fuente."'  and tdereg = '".$id."' ";

	$res1 = mysql_query($q,$conex);
}


function anularEncabezado($fuenteT, $consecutivoT, $cajCod)
{
	global $conex;
	global $wbasedato;


	$q= "   UPDATE ".$wbasedato."_000075 SET tenest = 'off' "
	."WHERE tenfue = '".$fuenteT."' and tennum = '".$consecutivoT."' and tencai='99' ";

	$res1 = mysql_query($q,$conex);

}
/***********************************************FUNCIONES DE MODELO*************************************/

function calcularTotal($fuente, $radio, $valor,  &$totalF, &$radio, &$componer )
{
	$totalF=0;


	for ($j=0;$j<count($fuente);$j++)
	{
		if (isset ($radio[$j]) and $radio[$j]!='no' )
		{
			$radio[$j]='checked';
			If ($componer[$j]==0)
			{
				$componer[$j]=$valor[$j];
			}

			if ($componer[$j]>$valor[$j])
			{
				$componer[$j]=0;
				$radio[$j]='';
				pintarAlert7();

			}
			$totalF=$totalF+$componer[$j];
		}
		else
		{
			$radio[$j]='';
			$componer[$j]=0;
		}
	}
}
/***********************************************FUNCIONES DE PRESENTACION*************************************/

function pintarEncabezado($wautor)
{
	global $wbasedato;
	echo "<p align=right><font size=3><b>Autor: ".$wautor."</b></font></p>";
	echo "<center><table border='1' width='350'>";
	echo "<tr><td colspan='2' class='titulo1'><img src='/matrix/images/medical/POS/logo_".$wbasedato.".png' WIDTH=388 HEIGHT=70></td></tr>";
	echo "<tr><td colspan='2'class='titulo1'><b>PROGRAMA PARA CAMBIO DE COMPOSICIÓN</b></font></td></tr>";

	echo"<form action='composicion.php' method='post' name='forma' >";
	echo "<tr><td colspan='2'class='titulo1'>Nº CAMBIO DE COMPOSICIÓN: <INPUT TYPE='text' NAME='consulta' VALUE='' size='10' ><INPUT TYPE='button' NAME='consultaR' VALUE='CONSULTAR' onclick='javascript:enter3()' ></td></tr>";
	echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
	echo "</form>";
	echo "</table></BR></BR>";
}

function pintarConsulta1($cajCod, $cajDes, $consecutivoT)
{

	echo "<table align='center'>";
	echo "<tr><td class='titulo4' >CAJA: ".$cajCod."-".$cajDes."</td></tr>";
	echo "<tr><td class='titulo4' >RECIBOS PARA CAMBIO DE COMPOSICION A ".date('Y-m-d')."</td></tr>";
	echo "<tr><td class='titulo4' >&nbsp;</td></tr>";
	echo "<tr><td class='titulo4' >NUMERO DE CAMBIO DE COMPOSICION: ".$consecutivoT."</td></tr>";
	echo "</table></br>";
}

function pintarConsulta2($cajCod, $cajDes, $consecutivoT, $fecha, $wcajF)
{
	echo "<table align='center'>";
	echo "<tr><td class='titulo4' >CAJA: ".$cajCod."-".$cajDes."</td></tr>";
	echo "<tr><td class='titulo4' >&nbsp;</td></tr>";
	echo "<tr><td class='titulo4' >NUMERO DE CAMBIO DE COMPOSICION: ".$consecutivoT."</td></tr>";
	echo "<tr><td class='titulo4' >FECHA DE CAMBIO DE COMPOSICION: ".$fecha."</td></tr>";
	echo "<tr><td class='titulo4' >NUEVA FORMA DE PAGO: ".$wcajF."</td></tr>";
	echo "</table></br>";
}

function pintarConsulta3($cajCod, $cajDes, $consecutivoT, $fecha, $wcajF)
{
	echo "<table align='center'>";
	echo "<tr><td class='titulo4' >CAJA: ".$cajCod."-".$cajDes."</td></tr>";
	echo "<tr><td class='titulo4' >&nbsp;</td></tr>";
	echo "<tr><td class='titulo4' >SE HA GUARDADO EXITOSAMENTE EL CAMBIO DE COMPOSICIÓN: ".$consecutivoT."</td></tr>";
	echo "<tr><td class='titulo4' >FECHA DEL CAMBIO DE COMPOSICION: ".$fecha."</td></tr>";
	echo "<tr><td class='titulo4' >FORMA DE PAGO FINAL: ".$wcajF."</td></tr>";
	echo "</table></br>";
}

function pintarAlerta1()
{
	echo "</table>";
	echo"<form action='composicion.php' method='post' name='form1' ><CENTER><fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080>";
	echo "<table align='center' border=0 bordercolor=#000080 width=700 style='border:solid;'>";
	echo "<tr><td colspan='2' align=center><font size=3 color='#000080' face='arial' align=center><b>EL USUARIO ESTA INACTIVO O NO TIENE PERMISO PARA HACER CAMBIOS DE COMPOSICION</td><tr>";
	echo "<tr><td colspan='2' align='center'><input type='button' name='aceptar' value='ACEPTAR' onclick='javascript:window.close()'></td><tr>";
	echo "</table></fieldset></form>";
}

function pintarAlert2($cajCod)
{
	echo "</table>";
	echo"<form action='composicion.php' method='post' name='form1' ><CENTER><fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080>";
	echo "<table align='center' border=0 bordercolor=#000080 width=700 style='border:solid;'>";
	echo "<tr><td colspan='2' align=center><font size=3 color='#000080' face='arial' align=center><b>LA CAJA".$cajCod." NO TIENE NINGUN RECIBO PAGADO EN EFECTIVO PARA REALIZAR EL CAMBIO DE COMPOSICIÓN</td><tr>";
	echo "<tr><td colspan='2' align='center'><input type='button' name='aceptar' value='ACEPTAR' onclick='javascript:window.close()'></td><tr>";
	echo "</table></fieldset></form>";
}

function pintarAlert3()
{
	echo '<script language="Javascript">';
	echo 'alert ("DEBE DILIGNECIAR TODA LA INFORMACIÓN SOLICITADA PARA LA FORMA DE PAGO SELECCIONADA")';
	echo '</script>';
}


function pintarAlert4($cajCod, $consulta)
{
	global $wbasedato;
	echo "</table>";
	echo"<form action='composicion.php' method='post' name='forma1' ><CENTER><fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080>";
	echo "<table align='center' border=0 bordercolor=#000080 width=700 style='border:solid;'>";
	echo "<tr><td colspan='2' align=center><font size=3 color='#000080' face='arial' align=center><b>NO EXISTE UN CAMBIO DE COMPOSICIÓN REALIZADO EN LA CAJA ".$cajCod.", PARA EL NUMERO ".$consulta."</td><tr>";
	echo "<tr><td colspan='2' align='center'><input type='button' name='aceptar' value='ACEPTAR' onclick='javascript:enter2()'></td><tr>";
	echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
	echo "</table></fieldset></form>";
}

function pintarAlert5($consecutivoT)
{
	global $wbasedato;
	echo "</table>";
	echo"<form action='composicion.php' method='post' name='forma1' ><CENTER><fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080>";
	echo "<table align='center' border=0 bordercolor=#000080 width=700 style='border:solid;'>";
	echo "<tr><td colspan='2' align=center><font size=3 color='#000080' face='arial' align=center><b>EL CAMBIO DE COMPOSICION ".$consecutivoT." HA SIDO ANULADO</td><tr>";
	echo "<tr><td colspan='2' align='center'><input type='button' name='aceptar' value='ACEPTAR' onclick='javascript:enter2()'></td><tr>";
	echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
	echo "</table></fieldset></form>";
}

function pintarAlert6($consecutivoT)
{
	global $wbasedato;
	echo "</table>";
	echo"<form action='composicion.php' method='post' name='forma1' ><CENTER><fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080>";
	echo "<table align='center' border=0 bordercolor=#000080 width=700 style='border:solid;'>";
	echo "<tr><td colspan='2' align=center><font size=3 color='#000080' face='arial' align=center><b>EL CAMBIO DE COMPOSICION ".$consecutivoT." YA NO PUEDE SER ANULADO</td><tr>";
	echo "<tr><td colspan='2' align='center'><input type='button' name='aceptar' value='ACEPTAR' onclick='javascript:enter2()'></td><tr>";
	echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
	echo "</table></fieldset></form>";
}

function pintarAlert7()
{
	echo '<script language="Javascript">';
	echo 'alert ("LOS VALORES PARA CAMBIOS DE CONPOSICIÓN NO DEBEN SER MAYORES AL VALOR DEL RECIBO RESPECTIVO")';
	echo '</script>';
}

function pintarAlert8()
{
	echo '<script language="Javascript">';
	echo 'alert ("EL VALOR PARA REALIZAR EL CAMBIO DE COMPOSICIÓN DEBE SER MAYOR A CERO")';
	echo '</script>';
}

function pintarFormulario($fuente, $numero, $fecha, $valor, $documento, $observacion, $radio, $totalF, $cajCod, $cajDes, $consecutivoT, $clase, $id, $fuenteT, $componer)
{
	global $wbasedato;
	$clase1="class='texto2'";

	echo "<form name='forma1' action='composicion.php' method='post'>";

	if (isset ($fuente[0]))
	{
		echo "<table align='center'>";

		if ($clase==1)
		echo "<tr><td class='titulo3'>SELECCIONAR</td>";
		else
		echo "<tr><td class='titulo3'>&nbsp;</td>";
		echo "<td class='titulo3'>FUENTE</td>";
		echo "<td class='titulo3'>N RECIBO</td>";
		echo "<td align='center' class='titulo3'>FECHA</td>";

		if ($clase==1)
		{
			//echo "<td class='titulo3'>DOCUMENTO ANEXO</td>";
			//echo "<td class='titulo3'>OBSERVACION</td>";
		}
		echo "<td class='titulo3'>VALOR</td>";
		echo "<td class='titulo3'>VALOR PARA COMPOSICION</td>";
		echo "</tr>";


		for($j=0; $j < count($fuente); $j++)
		{
			if ($clase1=="class='texto2'")
			{
				$clase1="class='texto1'";
			}
			else
			{
				$clase1="class='texto2'";
			}

			echo "<tr>";

			if ($clase==1)
			echo "<td $clase1><input type='checkbox' name='radio[".$j."]' ".$radio[$j]." ></td>";
			else
			echo "<td ".$clase1.">&nbsp;</td>";
			echo "<td ".$clase1.">".$fuente[$j]."</td>";
			echo "<td ".$clase1.">".$numero[$j]."</td>";
			echo "<td ".$clase1.">".$fecha[$j]."</td>";

			if ($clase==1)
			{
				//echo "<td $clase1>".$documento[$j]."</td>";
				//echo "<td $clase1>".$observacion[$j]."</td>";
			}
			echo "<td $clase1>$ ".number_format($valor[$j],0,"",",")."</td>";



			echo "<input type='HIDDEN' name='fuente[".$j."]' value='".$fuente[$j]."'>";
			echo "<input type='HIDDEN' name='id[".$j."]' value='".$id[$j]."'>";
			echo "<input type='HIDDEN' name='numero[".$j."]' value='".$numero[$j]."'>";
			echo "<input type='HIDDEN' name='fecha[".$j."]' value='".$fecha[$j]."'>";
			echo "<input type='HIDDEN' name='valor[".$j."]' value='".$valor[$j]."'>";
			echo "<input type='HIDDEN' name='documento[".$j."]' value='".$documento[$j]."'>";
			echo "<input type='HIDDEN' name='observacion[".$j."]' value='".$observacion[$j]."'>";
			echo "<input type='HIDDEN' name='componer[".$j."]' value='".$componer[$j]."'>";

			if ($clase==1)
			echo "<td ".$clase1." ><INPUT TYPE='text' NAME='componer[".$j."]' VALUE='".$componer[$j]."' size='10' ></td></tr>";
			else
			echo "<td ".$clase1." >$ ".number_format($componer[$j],",","",".")."</td></tr>";
		}
		if ($clase==1)
		echo "<td class='acumulado1' colspan='5'>TOTAL</td>";
		else
		echo "<td class='acumulado1' colspan='5'>TOTAL</td>";
		echo "<td class='acumulado1' align='center'>$ ".number_format($totalF,0,"",",")."</td>";
		echo "</table></br>";
	}

	echo "<input type='HIDDEN' name='bandera1' value='1'>";
	echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
	echo "<input type='HIDDEN' name='totalF' value='".$totalF."'>";
	echo "<input type='HIDDEN' name='consecutivoT' value='".$consecutivoT."'>";
	echo "<input type='HIDDEN' name='fuenteT' value='".$fuenteT."'>";
}

function pintarBoton1($totalF, $formas, $wformaF, $bancos, $wBancoO, $wBancoF, $wubica, $wdocum, $wobserv, $destinos )
{

	echo "<table align='center'>";
	echo "<tr><td class='titulo4' colspan='2'>VALOR PARA CAMBIO DE COMPOSICION: $ ".number_format($totalF,",","",".")."</td></tr>";
	echo "<tr><td class='titulo4' colspan='2'>&nbsp;</td></tr>";
	echo "<tr><td class='titulo4' colspan='2'>SELECCIONE LA NUEVA FORMA DE PAGO: </td></tr>";
	echo "<tr><td class='titulo4' colspan='2'>&nbsp;</td></tr>";
	echo "<tr><td class='titulo2' >FORMA DE PAGO: </font></b><select name='wformaF' onchange='javascript:enter2()'>";

	for ($i=0;$i<count($formas['codigo']);$i++)
	{

		if($formas['codigo'][$i]!='')
		{
			echo "<option>".$formas['codigo'][$i]."-".$formas['nombre'][$i]."</option>";

			if ($wformaF==$formas['codigo'][$i].'-'.$formas['nombre'][$i])
			{
				$wobliga=$formas['obliga'][$i];

			}
		}
	}
	echo "</select></td>";
	echo "<input type='HIDDEN' name='totalF' value='".$wobliga."'>";

	if ($wobliga=='on')
	{
		echo "<td class='titulo2' >DOCUMENTO ANEXO: </font></b><INPUT TYPE='text' NAME='wdocum' VALUE='".$wdocum."' size='10' ></td></tr>";
		echo "<td class='titulo2'>BANCO DE ORIGEN:<select name='wBancoO' >";
		for ($y=0;$y<count($bancos['codigo']);$y++)
		{
			if ($wBancoO==$bancos['codigo'][$y].'-'.$bancos['nombre'][$y]) //Si ya fue digitado la observacion
			{
				echo "<option selected>".$bancos['codigo'][$y].'-'.$bancos['nombre'][$y]."</option >";     //wobsrec
			}
			else
			echo "<option>".$bancos['codigo'][$y].'-'.$bancos['nombre'][$y]."</option>";
		}
		echo "</select></td>";

		if (isset($wubica) and $wubica<>'') //Si ya fue digitado el documento anexo
		{
			If ($wubica=='1-Local')
			{
				$otro='2-Otras plazas';
			}
			else
			{
				$otro='1-Local';
			}
			echo "<td class='titulo2'>UBICACIÓN:<select name='wubica' ><option selected>".$wubica."</option ><option>".$otro."</option></select></td>";
		}
		else
		{
			echo "<td class='titulo2'>UBICACIÓN<select name='wubica' ><option selected>1-Local</option ><option>2-Otras plazas</option></select></td>";
		}

		echo "<tr><td class='titulo2' >NÚMERO DE AUTORIZACIÓN: </font></b><INPUT TYPE='text' NAME='wobserv' VALUE='".$wobserv."' size='10' ></tr>";
	}else
	{
		echo "<td class='titulo2' >DOCUMENTO ANEXO: </font></b><INPUT TYPE='text' NAME='wdocum' VALUE='".$wdocum."' size='10' ></td></tr>";
		echo "<tr><td class='titulo2' >OBSERVACIÓN: </font></b><INPUT TYPE='text' NAME='wobserv' VALUE='".$wobserv."' size='10' ></td>";
		echo "<input type='HIDDEN' name='wubica' value=''>";
		echo "<input type='HIDDEN' name='wBancoO' value=''>";
	}
	echo "<td class='titulo2'>BANCO DESTINO:<select name='wBancoF' >";
	//2007-05-03 cambio de presentacion para mostrar el banco por defecto de la forma de pago
	for ($y=0;$y<count($destinos);$y++)
	{
		echo "<option selected>".$destinos[$y]."</option >";
		/*if ($wBancoF==$bancos['codigo'][$y].'-'.$bancos['nombre'][$y].'-'.$bancos['cuenta'][$y]) //Si ya fue digitado la observacion
		{
		echo "<option selected>".$bancos['codigo'][$y].'-'.$bancos['nombre'][$y].'-'.$bancos['cuenta'][$y]."</option >";     //wobsrec
		}
		else if ($bancos['destino'][$y]=='on')
		echo "<option>".$bancos['codigo'][$y].'-'.$bancos['nombre'][$y].'-'.$bancos['cuenta'][$y]."</option>";*/
	}
	echo "</select></td>";
	echo "<tr><td class='titulo2' colspan='2' align='center'><input type='checkbox' name='transladar'>REALIZAR CAMBIO DE COMPOSICIÓN</td></tr>";
	echo "<tr><td  colspan='2' align='center'>&nbsp;</td></tr>";
	echo "<tr><td  colspan='2' align='center'><input type='button' value='ACEPTAR'  onclick='javascript:enter()'></td></tr>";
	echo "<input type='HIDDEN' name='wobliga' value='".$wobliga."'>";
	echo "</table></br>";
	echo "</form>";
}

function pintarBoton2($totalF, $consulta, $wdocum, $wobserv, $wBancoF, $wBancoO, $wubica, $wformaF)
{

	$fila='enter5("'.$totalF.'", "'.$consulta.'")';
	echo "<table align='center'>";
	echo "<tr><td class='titulo4' colspan='2'>VALOR TOTAL DEL CAMBIO DE COMPOSICIÓN: $ ".number_format($totalF,",","",".")."</td></tr>";
	echo "<tr><td class='titulo4' colspan='2' >&nbsp;</td></tr>";
	echo "<tr><td class='titulo2' >FORMA DE PAGO: ".$wformaF."</td>";

	echo "<input type='HIDDEN' name='wformaF' value='".$wformaF."'>";

	if ($wBancoO!='')
	{
		echo "<td class='titulo2' >DOCUMENTO ANEXO: ".$wdocum." </td></tr>";
		echo "<tr><td class='titulo2'>BANCO DE ORIGEN:  ".$wBancoO." </td>";
		echo "<td class='titulo2'>UBICACIÓN: ".$wubica."</td></tr>";
		echo "<tr><td class='titulo2' >NÚMERO DE AUTORIZACIÓN: ".$wobserv."";
	}else
	{
		echo "<td class='titulo2' >DOCUMENTO ANEXO:".$wdocum."</td></tr>";
		echo "<tr><td class='titulo2' >OBSERVACIÓN: ".$wobserv."</td>";
	}
	echo "<td class='titulo2'>BANCO DESTINO: ".$wBancoF."</td></tr>";
	echo "<tr><td class='titulo4' colspan='2' >&nbsp;</td></tr>";
	echo "<tr><td class='titulo4' colspan='2'> <input type='button' value='ANULAR'  onclick='javascript:".$fila."'> <input type='button' value='VOLVER'  onclick='javascript:enter4()'></td></tr>";
	echo "</table></br>";
	echo "</form>";

}

/*********************************************** PROGRAMA PRINCIPAL *************************************/

session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{

	

	


	//obtengo los datos de la caja
	$cco = '';
	$cajCod = '';
	$cajDes = '';
	
	$cajgen=consultarEfectivo();

	consultarCaja(substr($user,2), &$cco, &$cajCod, &$cajDes );

	pintarEncabezado($wautor);

	if($cajCod != '')
	{
		if (isset($transladar) and $wobliga=='on'and ($wobserv=='' or $wdocum=='' ) )
		{
			pintarAlert3(); //DEBE INGRESAR TODOS LOS DATOS DE LA FORMA DE PAGO CUANDO PARA ESTA SEAN OBLIGATORIOS
			//OSEA EL DOCUMENTO ANEXO Y EL NUMERO DE AUTORIZACION
			unset($transladar);
		}

		if (isset($transladar) and !isset($radio))
		{
			pintarAlert8(); //RADIO ES UN VECTOR CON LOS CHECKBOX O CON LOS RECIBOS SELECCIONADOS
			//PARA EL CAMBIO DE COMPOSICION, SI NO SE SELECCIONO NINGUNO, RADIO NO EXISTE
			//Y NO SE PUEDE HACER EL CAMBIO DE COMPOSICION
			unset($transladar);
		}


		if (isset($consulta) and $consulta!='')// consulta de un cambio de composicion
		{
			$fuenteT=consultarFuente(); //CONSULTO FUENTE DE CAMBIO DE COMPOSICION
			consultarEncabezado($cajCod,$consulta, $cco, $fuenteT, &$wcajF, &$totalF, &$fec ); //PARA UN DOCUMENTO
			if ($totalF!='')
			{
				consultarRegistros ($cco, $fuenteT, $consulta, &$fuente, &$numero, &$fecha, &$valor, &$componer, &$wdocum, &$wBancoO, &$wobserv, &$wubica, &$wBancoF, &$id);
				pintarConsulta2($cajCod, $cajDes, $consulta, $fec, $wcajF);
				pintarFormulario($fuente, $numero, $fecha, $valor, '', '',                    '', $totalF,    $cajCod, $cajDes, $consulta,   2, $id, $fuenteT, $componer);
				pintarBoton2($totalF, $consulta, $wdocum, $wobserv, $wBancoF, $wBancoO, $wubica, $wcajF); //determina la actividad de guardar translados
			}else //no se obtienen resultados para ese numero de CAMBIO DE COMPOSICION
			{
				pintarAlert4($cajCod, $consulta);
			}
		}else // generación o anulacion de un translado
		{
			if (!isset ($bandera1) or $bandera1!=3) //generacion de informacion para el transalado
			{
				consultarConsecutivo(&$consecutivoT, &$fuenteT);

				if(!isset($transladar)) //proceso previo antes de guardar el translado
				{
					pintarConsulta1($cajCod, $cajDes, $consecutivoT); //muestra el numero del cambio de composicion

					if (!isset($bandera1) or $bandera1==2)
					{
						$contador=0;  //me cuenta si habrán recibos para cambiar de composición

						//consulto los recibos pagados en efectivo y para la caja
						$registro=consultarRecibos('99', $cajCod, $cco);
						if ($registro)
						{
							//organizo un vector para la forma de pago
							for ($j=0;$j<count($registro['numero']);$j++)
							{

								$id[$j]=$registro['id'][$j];
								$fuente[$j]=$registro['fuente'][$j];
								$numero[$j]=$registro['numero'][$j];
								$fecha[$j]=$registro['fecha'][$j];
								$valor[$j]=$registro['valor'][$j];
								$documento[$j]=$registro['documento'][$j];
								$observacion[$j]=$registro['observacion'][$j];
								$componer[$j]=0;
								$contador++;
							}

						}
						$wformaF='';
						$wBancoO='';
						$wBancoF='';
						$wubica='';
						$wdocum='';
						$wobserv='';
					}else
					{
						$contador=1; //ya se sabe que hay recibos para el cambio de composicion
					}

					if ($contador>0)
					{
						if (!isset ($radio))
						{
							$radio[0]='no';
						}
						// adecuo los valores de seleccion y calculo los totales
						calcularTotal($fuente, $radio, $valor, &$totalF, &$radio, &$componer );
						$formas=consultarFormas($wformaF);
						if ($wformaF=='')
						{
							$wformaF=$formas['codigo'][1].'-'.$formas['nombre'][1];
						}

						$bancos=consultarBancos();
						$destinos=consultarDestinos($wformaF);
						pintarFormulario($fuente, $numero, $fecha, $valor, $documento, $observacion, $radio, $totalF, $cajCod, $cajDes, $consecutivoT,1, $id, $fuenteT, $componer);
						pintarBoton1($totalF, $formas, $wformaF, $bancos, $wBancoO, $wBancoF, $wubica, $wdocum, $wobserv, $destinos); //determina la actividad de guardar translados
					}else
					{
						pintarAlert2($cajCod);
					}
				}else// REALIZO LAS ACTIVIDADES DE TRANSLADO Y SU ALMACENAMIENTO
				{
					//calculo el total
					calcularTotal($fuente, $radio, $valor, &$totalF, &$radio, &$componer);
					$exp=explode('-',$wformaF);
					//guardar el encabezado
					grabarEncabezado($fuenteT, $consecutivoT, '99', $exp[0], $totalF, $cajCod, substr($user,2));

					for ($j=0;$j<count($fuente);$j++)
					{
						if (isset ($radio[$j]) and $radio[$j]!='')
						{
							grabarDetalle     ($fuenteT, $consecutivoT, $numero[$j], $fuente[$j], $valor[$j], '99', $cajCod, $id[$j], substr($user,2));
							grabarMovimientos($fuenteT, $consecutivoT, $numero[$j], $fuente[$j], $componer[$j], $exp[0], $id[$j], $valor[$j], $wdocum, $wBancoO, $wBancoF, $wobserv, $wubica, $cajCod, substr($user,2));
						}
					}

					incrementarConsecutivo($fuenteT);
					consultarEncabezado($cajCod, $consecutivoT, $cco, $fuenteT, &$wcajF, &$totalF, &$fec ); //consultar el documento guardado

					if (isset($fuenteT) and $fuenteT!='')
					{
						consultarRegistros ($cco, $fuenteT, $consecutivoT, &$fuente2, &$numero2, &$fecha2, &$valor2, &$componer2, &$wdocum2, &$wBancoO2, &$wobserv2, &$wubica2, &$wBancoF2, &$id2);
						pintarConsulta3($cajCod, $cajDes, $consecutivoT, $fec, $wcajF);
						pintarFormulario($fuente2, $numero2, $fecha2, $valor2, '', '', '', $totalF, $cajCod, $cajDes, $consecutivoT,2, $id2, $fuenteT, $componer2);
						pintarBoton2($totalF, $consecutivoT, $wdocum2, $wobserv2, $wBancoF2, $wBancoO2, $wubica, $wcajF); //determina la actividad de guardar translados
					}else //no se obtienen resultados para ese numero de envio
					{
						pintarAlert4($cajCod, $consecutivoT);
					}

				}
			}else  // 	PROCESO DE ANULACION
			{
				$permiso=consultarPermiso($id, $fuenteT, $consecutivoT);
				if ($permiso) //se realiza anulación
				{
					for ($j=0;$j<count($fuente);$j++)
					{
						devolverMovimientos($fuenteT, $consecutivoT, $numero[$j], $fuente[$j], $componer[$j], $wformaF, $id[$j], $valor[$j], $cajCod, $cajgen);

						anularRegistro($consecutivoT, $fuenteT, $numero[$j], $fuente[$j], $id[$j]);
					}

					anularEncabezado($fuenteT, $consecutivoT, $cajCod);
					pintarAlert5($consecutivoT);

				}else //mensaje de no poder realizar anulacion
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
</body>
</html>
