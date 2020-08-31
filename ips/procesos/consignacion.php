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
//PROGRAMA:	consignacion.php
//AUTOR: Carolina Castaño.
$wautor="Carolina Castaño P.";

//TIPO DE SCRIPT: principal
//RUTA DEL SCRIPT: matrix\pos\procesos\consignacion.php

//HISTORIAL DE REVISIONES DEL SCRIPT:
//-------------------I------------------------I---------------------------------------------------------------------
//	  FECHA           I     AUTOR              I   MODIFICACION
//-------------------I------------------------I------------------------------------------------------------------------
//  2006-09-26       I Carolina Castaño  P    I creación del script.
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

function grabarEncabezado($fuenteT, $consecutivoT, $cajCod, $wbanF, $totalF, $wusuario)
{
	//consulto las formas de pago existentes para agrupar

	global $conex;
	global $wbasedato;

	$exp=explode('-', $wbanF);

	$q= " INSERT INTO ".$wbasedato."_000075 (   Medico       ,   Fecha_data,   Hora_data,   tenfue,           tennum ,              tencai  ,     tencaf    ,    tenval    ,        tenfec ,         tenest    , Seguridad        ) "
	."         VALUES ('".$wbasedato."',   '".date('Y-m-d')."', '".(string)date("H:i:s")."' ,'".$fuenteT."', '".$consecutivoT."', '".$cajCod."', '".$exp[0]."' , '".$totalF."' , '".date('Y-m-d')."'  , 'on' ,  'C-".$wusuario."') ";

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

function consultarEncabezado($cajCod,$consulta, $cco, $fuenteT, &$wbanF, &$totalF, &$fec )
{

	//*consulto el encabezado de una consignacion dado

	global $conex;
	global $wbasedato;

	$q="select tenfue, tencaf, tenval, tenfec, bannom, bancue "
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
	}else
	{
		$wbanF='';
		$fuenteT='';
		$totalF='';
		$fec='';
	}
}

function consultarRegistros ($cco, $fuenteT, $consulta, &$fuente, &$numero, &$fecha, &$valor, &$documento, &$banco, &$pagos, &$total, &$id)
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
		$valor[$contador][$rotador]=$row[2];
		$id[$contador][$rotador]=$row[10];
		$documento[$contador][$rotador]=$row[6];
		$fecha[$contador][$rotador]=$row[8];
		$total[$contador]=$total[$contador]+$row[2];
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

function calcularTotal($pagos, $fuente, $radio, $valor, $radio2 , &$total, &$totalF, &$radio)
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
					$total[$i]=$total[$i]+$valor[$i][$j];
				}
				else
				{
					$radio[$i][$j]='';
				}
			}
		}
		$totalF=$totalF+$total[$i];
	}
}
/***********************************************FUNCIONES DE PRESENTACION*************************************/

function pintarEncabezado($wautor)
{
	//* pinta el encabezado
	global $wbasedato;
	echo "<p align=right><font size=3><b>Autor: ".$wautor."</b></font></p>";
	echo "<center><table border='1' width='350'>";
	echo "<tr><td colspan='2' class='titulo1'><img src='/matrix/images/medical/POS/logo_".$wbasedato.".png' WIDTH=388 HEIGHT=70></td></tr>";
	echo "<tr><td colspan='2'class='titulo1'><b>PROGRAMA PARA CONSIGNACIÓN INTERNA</b></font></td></tr>";

	echo"<form action='consignacion.php' method='post' name='forma' >";
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
	echo "<tr><td class='titulo4' >NUMERO DE CONSIGNACION INTERAN: ".$consecutivoT."</td></tr>";
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
	echo"<form action='consignacion.php' method='post' name='forma1' ><CENTER><fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080>";
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
	echo"<form action='consignacion.php' method='post' name='forma1' ><CENTER><fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080>";
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
	echo"<form action='consignacion.php' method='post' name='forma1' ><CENTER><fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080>";
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
	echo"<form action='consignacion.php' method='post' name='forma1' ><CENTER><fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080>";
	echo "<table align='center' border=0 bordercolor=#000080 width=700 style='border:solid;'>";
	echo "<tr><td colspan='2' align=center><font size=3 color='#000080' face='arial' align=center><b>DEBE SELECCIONAR AL MENOS UN REGISTRO PARA REALIZAR LA CONSIGNACIÓN</td><tr>";
	echo "<tr><td colspan='2' align='center'><input type='button' name='aceptar' value='ACEPTAR' onclick='javascript:enter2()'></td><tr>";
	echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
	echo "</table></fieldset></form>";

}


function pintarFormulario($fuente, $numero, $fecha, $valor, $documento, $banco, $radio, $pagos, $total, $totalF, $cajCod, $cajDes, $consecutivoT, $clase, $id, $fuenteT, $radio2)
{
	global $wbasedato;
	$clase1="class='texto2'";
	$clase2="class='texto5'";

	echo "<form name='forma1' action='consignacion.php' method='post'>";

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
				echo "<tr><td class='titulo2' colspan='7'><input type='checkbox' name='radio2[".$i."]' ".$radio2[$i]." onclick='enter6()'>&nbsp;&nbsp;FORMA DE PAGO: ".$pagos['codigo'][$i]."-".$pagos['nombre'][$i]."</td></tr>";
				else
				echo "<tr><td class='titulo2' colspan='5'><input type='checkbox' name='radio2[".$i."]' ".$radio2[$i]." onclick='enter6()'>&nbsp;&nbsp;FORMA DE PAGO: ".$pagos['codigo'][$i]."-".$pagos['nombre'][$i]."</td></tr>";
			}
			else
			{
				if ($pagos['tipo'][$i]=='on')
				echo "<tr><td class='titulo2' colspan='7'>&nbsp;&nbsp;FORMA DE PAGO: ".$pagos['codigo'][$i]."-".$pagos['nombre'][$i]."</td></tr>";
				else
				echo "<tr><td class='titulo2' colspan='5'>&nbsp;&nbsp;FORMA DE PAGO: ".$pagos['codigo'][$i]."-".$pagos['nombre'][$i]."</td></tr>";
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
				echo "<td ".$clase1.">$ ".number_format($valor[$i][$j],",","",".")."</td>";
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
			echo "<td class='acumulado1' colspan='6'>TOTAL</td>";
			else
			echo "<td class='acumulado1' colspan='4'>TOTAL</td>";
			echo "<td class='acumulado1' >$ ".number_format($total[$i],",","",".")."</td>";
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

function pintarBoton1($totalF, $bancos, $wbanF)
{
	// * despliega lista de bancos y valor a consignar

	echo "<table align='center'>";
	echo "<tr><td class='titulo4' >VALOR A CONSIGNAR: $ ".number_format($totalF,",","",".")."</td></tr>";
	echo "<tr><td class='titulo4' >Banco destino: </font></b><select name='wbanF'>";
	for ($i=0;$i<count($bancos['codigo']);$i++)
	{
		if($bancos['codigo'][$i]!='')
		echo "<option>".$bancos['codigo'][$i]."-".$bancos['nombre'][$i]."-".$bancos['cuenta'][$i]."</option>";
	}
	echo "</select></td>";
	echo "<tr><td class='titulo4' >&nbsp;</td></tr>";
	echo "<tr><td class='titulo4' ><input type='checkbox' name='consignar'>Consignar	&nbsp;<input type='button' value='ACEPTAR'  onclick='javascript:enter()'></td></tr>";
	echo "</table></br>";
	echo "</form>";
}

function pintarBoton2($totalF, $consulta)
{

	$fila='enter5("'.$totalF.'", "'.$consulta.'")';
	echo "<table align='center'>";
	echo "<tr><td class='titulo4' >VALOR TOTAL CONSIGNACIÓN: $ ".number_format($totalF,",","",".")."</td></tr>";
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

	consultarBanco(&$cajCod, &$cajDes);
	consultarCaja(substr($user,2), &$cco, &$cajCod2, &$cajDes2);


	pintarEncabezado($wautor);

	if($cajCod != '')
	{
		if (isset($consignar) and (!isset($wbanF) or $wbanF=='' ) )
		{
			pintarAlert3(); //DEBE SELECCIONAR LA CAJA CUANDO VA A TRANSLADAR
			unset($consignar);
		}

		if (isset($consulta) and $consulta!='')// consulta de un translado
		{
			$fuenteT=consultarFuente();
			consultarEncabezado($cajCod,$consulta, $cco, $fuenteT, &$wbanF, &$totalF, &$fec );
			if ($wbanF!='')
			{
				consultarRegistros ($cco, $fuenteT, $consulta, &$fuente, &$numero, &$fecha, &$valor, &$documento, &$banco, &$pagos, &$total, &$id);
				pintarConsulta2($cajCod, $cajDes, $consulta, $fec, $wbanF);
				pintarFormulario($fuente, $numero, $fecha, $valor, $documento, $banco, '', $pagos, $total, $totalF, $cajCod, $cajDes, $consulta,2, $id, $fuenteT, $radio2 );
				pintarBoton2($totalF, $consulta); //determina la actividad de guardar translados
			}else //no se obtienen resultados para ese numero de envio
			{
				pintarAlert4($cajCod, $consulta);
			}

		}else // generación o anulacion de un translado
		{

			if (!isset ($bandera1) or $bandera1!=3) //generacion de informacion para el transalado
			{
				consultarConsecutivo(&$consecutivoT, &$fuenteT);

				if(!isset($consignar)) //proceso previo antes de guardar el translado
				{
					pintarConsulta1($cajCod, $cajDes, $consecutivoT);

					if (!isset($bandera1) or $bandera1==2)
					{
						//consulto formas de pago
						$pagos=consultarPagos();
						$contador=0;  //me cuenta cuantas formas de pago no tienen que ser transladadas
						//realizaremos la distribución por tipo de pago para cada recibo
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
									$id[$i][$j]=$registro['id'][$j];
									$fuente[$i][$j]=$registro['fuente'][$j];
									$numero[$i][$j]=$registro['numero'][$j];
									$fecha[$i][$j]=$registro['fecha'][$j];
									$valor[$i][$j]=$registro['valor'][$j];
									$documento[$i][$j]=$registro['documento'][$j];
									$banco[$i][$j]=$registro['banco'][$j];
									$radio[$i][$j]='checked';
									$contador++;
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
						calcularTotal($pagos, $fuente, $radio, $valor, $radio2, &$total, &$totalF, &$radio);
						$bancos=consultarBancos($wbanF, $cajCod);
						pintarFormulario($fuente, $numero, $fecha, $valor, $documento, $banco, $radio, $pagos, $total, $totalF, $cajCod, $cajDes, $consecutivoT,1, $id, $fuenteT, $radio2);
						pintarBoton1($totalF, $bancos, $wbanF); //determina la actividad de guardar translados
					}else
					{
						pintarAlert2($cajCod);
					}
				}else// REALIZO LAS ACTIVIDADES DE TRANSLADO Y SU ALMACENAMIENTO
				{
					//calculo el total
					calcularTotal($pagos, $fuente, $radio, $valor, $radio2, &$total, &$totalF, &$radio);

					if ($totalF>0)
					{
						//guardar el encabezado
						grabarEncabezado($fuenteT, $consecutivoT, $cajCod, $wbanF, $totalF, substr($user,2));
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
										cambiarEstado($numero[$i][$j], $fuente[$i][$j], $exp[0], $pagos['codigo'][$i], $id[$i][$j]);
									}
								}
							}
						}


						incrementarConsecutivo($fuenteT);

						consultarEncabezado($cajCod,$consecutivoT, $cco, $fuenteT, &$wbanF, &$totalF, &$fec ); //consultar el documento guardado

						if (isset($fuenteT) and $fuenteT!='')
						{
							consultarRegistros ($cco, $fuenteT, $consecutivoT, &$fuente2, &$numero2, &$fecha2, &$valor2, &$documento2, &$banco2, &$pagos2, &$total2, &$id2);
							pintarConsulta3($cajCod, $cajDes, $consecutivoT, $fec, $wbanF);
							pintarFormulario($fuente2, $numero2, $fecha2, $valor2, $documento2, $banco2, '', $pagos2, $total2, $totalF, $cajCod, $cajDes, $consecutivoT ,2, $id2, $fuenteT, $radio2);
							pintarBoton2($totalF, $consecutivoT); //determina la actividad de guardar translados
						}else //no se obtienen resultados para ese numero de envio
						{
							pintarAlert4($cajCod, $consecutivoT);
						}
					}else
					{
						pintarAlert7();
					}
				}
			}else  // 	PROCESO DE ANULACION
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
								cambiarEstado($numero[$i][$j], $fuente[$i][$j], $cajCod, $pagos['codigo'][$i], $id[$i][$j]);
								anularRegistro($consecutivoT, $fuenteT, $numero[$i][$j], $fuente[$i][$j],  $pagos['codigo'][$i], $id[$i][$j]);
							}
						}
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
