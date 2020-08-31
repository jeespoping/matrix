<html>
<head>
  <title>Traslado de Caja </title>
  
  <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
    	<!--Fondo Azul no muy oscuro y letra blanca -->
    	.titulo1{color:#FFFFFF;background:#006699;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}
    	<!-- -->
    	.titulo2{color:#003366;background:#57C8D5;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.titulo3{color:#003366;background:#A4E1E8;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.titulo4{color:#003366;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.texto{color:#006699;background:#FFFFFF;font-size:9pt;font-family:Tahoma;text-align:center;}
    	.acumulado1{color:#003366;background:#FFCC66;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.acumulado2{color:#003366;background:#FFDBA8;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.error1{color:#FF0000;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
   </style>
   
   <script type="text/javascript">
   function enter()
   {
   	if(document.forma1.transladar.checked==true)
   	{
   		var fRet;
   		fRet = confirm('Estas seguro que desea realizar el translado');
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
*     PROGRAMA PARA EL TRANSLADO DE CAJA AUXILIAR A CAJA PRINCIPAL    *
*                                                                 *
*************************************************************************************/
//=================================================================================================================================
//PROGRAMA:	translado.php
//AUTOR: Carolina Castaño.
$wautor="Carolina Castaño P.";

//TIPO DE SCRIPT: principal
//RUTA DEL SCRIPT: matrix\pos\procesos\translado.php

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

/**
 * Esta funcion indica si un recibo tiene nota credito o no
 * 
 * @param $cco
 * @param $fue
 * @param $num
 * @return unknown_type
 */
function tieneNotaCredito( $cco, $fue, $num ){

	global $conex;
	global $wbasedato;
	
	//Buscar la fuente nota credito
	$sql = "SELECT
				ccofnc
			FROM
				{$wbasedato}_000003
			WHERE
				ccocod = '$cco'
				AND ccoest = 'on'";
				
	$res = mysql_query( $sql );
	
	if( $row = mysql_fetch_array( $res ) ){
		$nc = $row[0];
		
		//Buscar la factura
		$sql = "SELECT
					rdefac
				FROM
					{$wbasedato}_000021
				WHERE
					rdecco = '$cco'
					AND rdenum = '$num'
					AND rdefue = '$fue'
					AND rdeest = 'on'";
					
		$res = mysql_query( $sql );
		
		if( $rows = mysql_fetch_array( $res ) ){
			
			//Buscar la nota credito
			$sql = "SELECT
						rdefac
					FROM
						{$wbasedato}_000021
					WHERE
						rdecco = '$cco'
						AND rdefac = '{$rows[0]}'
						AND rdefue = '$nc'
						AND rdeest = 'on'";
						
			$res = mysql_query( $sql );
			
			if( $rows = mysql_fetch_array($res) ){
				return true;
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
	}
	else{
		return false;
	}
	
}

function consultarCaja($user, &$cco, &$cajCod, &$cajDes, &$cuaAnt)
{
	//extrae los datos de la caja a la que pertenece el usuario

	global $conex;
	global $wbasedato;

	$q="select Cjecco, Cjecaj, Cajcod, Cajdes, Cajcua "
	."from ".$wbasedato."_000030, ".$wbasedato."_000028 "
	."where	Cjeusu = '".$user."' "
	."and	Cajcod = MID(Cjecaj, 1,2) ";

	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);

	if ($num > 0)
	{
		$row=mysql_fetch_array ($err);
		$vecCco = explode('-', $row['Cjecco']);
		$cco = $vecCco[0];		
		//$cco = substr($row['Cjecco'],0,4);		
		$cajCod = $row['Cajcod'];
		$cajDes = $row['Cajdes'];
		$cuaAnt = $row['Cajcua'];
	}
}

function consultarPagos()
{
	//consulto las formas de pago existentes para agrupar

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
	//consulto las formas de pago existentes para agrupar

	global $conex;
	global $wbasedato;

	$q="select carfue, carcon "
	."from	".$wbasedato."_000040 "
	."where	carest = 'on' and cartra='on' ";

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
	."where	carest = 'on' and cartra='on' ";

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

function consultarCajasPpa($wcajF)
{
	//consulto las cajas principales para hacer translado

	global $conex;
	global $wbasedato;

	if ($wcajF!='')
	{
		$exp=explode ('-',$wcajF);

		$cajas['codigo'][0]=$exp[0];
		$cajas['nombre'][0]=$exp[1];

		$q="select cajcod, cajdes "
		."from	".$wbasedato."_000028 "
		."where	cajppa = 'on' and cajest='on' and cajcod<>'".$exp[0]."' and cajdes<>'".$exp[1]."' ";

	}else
	{
		$cajas['codigo'][0]='';
		$cajas['nombre'][0]='';

		$q="select cajcod, cajdes "
		."from	".$wbasedato."_000028 "
		."where	cajppa = 'on' and cajest='on' ";
	}
	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);

	if ($num>0)
	{
		for ($i=1;$i<=$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$cajas['codigo'][$i]=$row[0];
			$cajas['nombre'][$i]=$row[1];
		}
	}
	return $cajas;
}

function consultarRecibos($codPago, $cajCod, $cco)
{
	//consulta los recibos en estado cuadrado para una caja retornando un vector de recibos

	global $conex;
	global $wbasedato;

	$q="select A.Renfue, A.Rennum, A.Renfec, C.rfpvfp, C.rfpdan, C.rfpobs, C.id, C.rfpdev "
	."from	".$wbasedato."_000020 A, ".$wbasedato."_000040 B, ".$wbasedato."_000022 C "
	."where	carrec = 'on' "
	."and	B.Carest = 'on' "
	."and	A.Renfue = B.Carfue "
	."and	A.Rencco = '".$cco."' "
	."and	C.Rfpcaf = '".$cajCod."' "
	."and	C.rfpfue= A.Renfue "
	."and	C.rfpnum = A.rennum "
	."and	C.Rfpecu='C' "
	."and	C.rfpfpa = '".$codPago."' "
	."and	C.rfpest='on' ";

	$err = mysql_query($q,$conex) or die( "Error en el query $q - ".mysql_error() );
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

			if( $row[7] > 0 && tieneNotaCredito( $cco, $row[0], $row[1] ) ){
				$registro['valor'][$i]=$row[3] - $row[7];
			}
			
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

function grabarEncabezado($fuenteT, $consecutivoT, $cajCod, $wcajF, $totalF, $wusuario)
{
	//consulto las formas de pago existentes para agrupar

	global $conex;
	global $wbasedato;

	$exp=explode('-', $wcajF);

	$q= " INSERT INTO ".$wbasedato."_000075 (   Medico       ,   Fecha_data,   Hora_data,   tenfue,           tennum ,              tencai  ,     tencaf    ,    tenval    ,        tenfec ,         tenest    , Seguridad        ) "
	."         VALUES ('".$wbasedato."',   '".date('Y-m-d')."', '".(string)date("H:i:s")."' ,'".$fuenteT."', '".$consecutivoT."', '".$cajCod."', '".$exp[0]."' , '".$totalF."' , '".date('Y-m-d')."'  , 'on' ,  'C-".$wusuario."') ";

	$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
}


function grabarDetalle($fuenteT, $consecutivoT, $numero, $fuente, $valor, $pago, $cajCod, $id, $wusuario)
{
	//consulto las formas de pago existentes para agrupar

	global $conex;
	global $wbasedato;

	$q= " INSERT INTO ".$wbasedato."_000076 (   Medico       ,   Fecha_data         ,   Hora_data                 ,   tdefue     ,           tdenum   ,              tdendo  ,     tdefdo    ,   tdeval     ,       tdefpa ,     tdereg  ,  tdeest  , Seguridad        ) "
	."         						 VALUES ('".$wbasedato."',   '".date('Y-m-d')."', '".(string)date("H:i:s")."' ,'".$fuenteT."', '".$consecutivoT."', '".$numero."'        , '".$fuente."' , '".$valor."' , '".$pago."'  ,  '".$id."'  ,     'on' ,  'C-".$wusuario."') ";

	$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
}

function consultarEncabezado($cajCod,$consulta, $cco, $fuenteT, &$wcajF, &$totalF, &$fec )
{

	//consulto el encabezado de un trasnlado dado

	global $conex;
	global $wbasedato;

	$q="select tenfue, tencaf, tenval, tenfec, cajdes "
	."from	".$wbasedato."_000075, ".$wbasedato."_000028 "
	."where	tenest = 'on' and tennum='".$consulta."' and tencai='".$cajCod."' and cajcod=tencaf and tenfue='".$fuenteT."' ";


	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);

	if ($num>0)
	{
		$row = mysql_fetch_array($err);
		$wcajF=$row[1]."-".$row[4];
		$fuenteT=$row[0];
		$totalF=$row[2];
		$fec=$row[3];
	}else
	{
		$wcajF='';
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
	."and B.fpacod=A.tdefpa and C.rfpfue=A.tdefdo and C.rfpnum=A.tdendo and C.rfpfpa=A.tdefpa and C.id=A.tdereg and D.renfue=A.tdefdo and  rfpcco='".$cco."' and  rencco='".$cco."'  and D.rennum=A.tdendo order by A.tdefpa";


	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);
	$forma=0;
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

function cambiarEstado($numero, $fuente, $estado, $caja,  $pago, $id )
{
	//cambio el estado de los recibo

	global $conex;
	global $wbasedato;

	$q="     UPDATE ".$wbasedato."_000022 SET rfpecu = '".$estado."', rfpcaf='".$caja."' WHERE rfpnum='".$numero."' and rfpfue='".$fuente."' and rfpest='on' and rfpfpa='".$pago."' and id='".$id."' ";

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

function consultarPermiso($pagos, $id, $fuen, $docu)
{
	global $conex;
	global $wbasedato;

	$permiso=true;

	for ($i=0;$i<count($pagos['codigo']);$i++)
	{
		if (isset($id[$i]))
		{
			for ($j=0;$j<count($id[$i]);$j++)
			{
				$q="select rfpnum  from	".$wbasedato."_000022 where	id = '".	$id[$i][$j]."' and	rfpest = 'on' and rfpecu = 'C' ";
				$err = mysql_query($q,$conex);
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

function calcularTotal($pagos, $fuente, $radio, $valor, &$total, &$totalF, &$radio)
{
	$totalF=0;
	for ($i=0;$i<count($pagos['codigo']);$i++)
	{
		$total[$i]=0;
		if (isset($fuente[$i]))
		{
			for ($j=0;$j<count($fuente[$i]);$j++)
			{
				if (isset ($radio[$i][$j]) )
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
	global $wbasedato;
	echo "<p align=right><font size=3><b>Autor: ".$wautor."</b></font></p>";
	echo "<center><table border='1' width='350'>";
	echo "<tr><td colspan='2' class='titulo1'><img src='/matrix/images/medical/POS/logo_".$wbasedato.".png' WIDTH=388 HEIGHT=70></td></tr>";
	echo "<tr><td colspan='2'class='titulo1'><b>PROGRAMA PARA TRASLADO DE CAJA</b></font></td></tr>";

	echo"<form action='transladopru.php' method='post' name='forma' >";
	echo "<tr><td colspan='2'class='titulo1'>Nº TRASLADO: <INPUT TYPE='text' NAME='consulta' VALUE='' size='10' ><INPUT TYPE='button' NAME='consultaR' VALUE='CONSULTAR' onclick='javascript:enter3()' ></td></tr>";
	echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
	echo "</form>";
	echo "</table></BR></BR>";
}

function pintarConsulta1($cajCod, $cajDes, $consecutivoT)
{

	echo "<table align='center'>";
	echo "<tr><td class='titulo4' >CAJA: ".$cajCod."-".$cajDes."</td></tr>";
	echo "<tr><td class='titulo4' >RECIBOS PARA TRASLADAR A ".date('Y-m-d')."</td></tr>";
	echo "<tr><td class='titulo4' >&nbsp;</td></tr>";
	echo "<tr><td class='titulo4' >NUMERO DE TRASLADO: ".$consecutivoT."</td></tr>";
	echo "</table></br>";
}

function pintarConsulta2($cajCod, $cajDes, $consecutivoT, $fecha, $wcajF)
{
	echo "<table align='center'>";
	echo "<tr><td class='titulo4' >CAJA: ".$cajCod."-".$cajDes."</td></tr>";
	echo "<tr><td class='titulo4' >&nbsp;</td></tr>";
	echo "<tr><td class='titulo4' >NUMERO DE TRASLADO: ".$consecutivoT."</td></tr>";
	echo "<tr><td class='titulo4' >FECHA DE TRASLADO: ".$fecha."</td></tr>";
	echo "<tr><td class='titulo4' >CAJA DESTINO: ".$wcajF."</td></tr>";
	echo "</table></br>";
}

function pintarConsulta3($cajCod, $cajDes, $consecutivoT, $fecha, $wcajF)
{
	echo "<table align='center'>";
	echo "<tr><td class='titulo4' >CAJA: ".$cajCod."-".$cajDes."</td></tr>";
	echo "<tr><td class='titulo4' >&nbsp;</td></tr>";
	echo "<tr><td class='titulo4' >SE HA GUARDADO EXITOSAMENTE EL TRASLADO: ".$consecutivoT."</td></tr>";
	echo "<tr><td class='titulo4' >FECHA DE TRASLADO: ".$fecha."</td></tr>";
	echo "<tr><td class='titulo4' >CAJA DESTINO: ".$wcajF."</td></tr>";
	echo "</table></br>";
}

function pintarAlerta1()
{
	echo "</table>";
	echo"<form action='transladopru.php' method='post' name='form1' ><CENTER><fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080>";
	echo "<table align='center' border=0 bordercolor=#000080 width=700 style='border:solid;'>";
	echo "<tr><td colspan='2' align=center><font size=3 color='#000080' face='arial' align=center><b>EL USUARIO ESTA INACTIVO O NO TIENE PERMISO PARA HACER TRASLADOS</td><tr>";
	echo "<tr><td colspan='2' align='center'><input type='button' name='aceptar' value='ACEPTAR' onclick='javascript:window.close()'></td><tr>";
	echo "</table></fieldset></form>";
}

function pintarAlert2($cajCod)
{
	echo "</table>";
	echo"<form action='transladopru.php' method='post' name='form1' ><CENTER><fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080>";
	echo "<table align='center' border=0 bordercolor=#000080 width=700 style='border:solid;'>";
	echo "<tr><td colspan='2' align=center><font size=3 color='#000080' face='arial' align=center><b>LA CAJA".$cajCod." NO TIENE NINGUN RECIBO PENDIENTE DE TRASLADO</td><tr>";
	echo "<tr><td colspan='2' align='center'><input type='button' name='aceptar' value='ACEPTAR' onclick='javascript:window.close()'></td><tr>";
	echo "</table></fieldset></form>";
}

function pintarAlert3()
{
	echo '<script language="Javascript">';
	echo 'alert ("DEBE SELECCIONAR LA CAJA A LA CUAL VA A REALIZAR EL TRASLADO")';
	echo '</script>';
}


function pintarAlert4($cajCod, $consulta)
{
	global $wbasedato;
	echo "</table>";
	echo"<form action='transladopru.php' method='post' name='forma1' ><CENTER><fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080>";
	echo "<table align='center' border=0 bordercolor=#000080 width=700 style='border:solid;'>";
	echo "<tr><td colspan='2' align=center><font size=3 color='#000080' face='arial' align=center><b>NO EXISTE UN TRASLADO REALIZADO EN LA CAJA ".$cajCod.", PARA EL NUMERO ".$consulta."</td><tr>";
	echo "<tr><td colspan='2' align='center'><input type='button' name='aceptar' value='ACEPTAR' onclick='javascript:enter2()'></td><tr>";
	echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
	echo "</table></fieldset></form>";
}

function pintarAlert5($consecutivoT)
{
	global $wbasedato;
	echo "</table>";
	echo"<form action='transladopru.php' method='post' name='forma1' ><CENTER><fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080>";
	echo "<table align='center' border=0 bordercolor=#000080 width=700 style='border:solid;'>";
	echo "<tr><td colspan='2' align=center><font size=3 color='#000080' face='arial' align=center><b>EL TRASLADO ".$consecutivoT." HA SIDO ANULADO</td><tr>";
	echo "<tr><td colspan='2' align='center'><input type='button' name='aceptar' value='ACEPTAR' onclick='javascript:enter2()'></td><tr>";
	echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
	echo "</table></fieldset></form>";
}

function pintarAlert6($consecutivoT)
{
	global $wbasedato;
	echo "</table>";
	echo"<form action='transladopru.php' method='post' name='forma1' ><CENTER><fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080>";
	echo "<table align='center' border=0 bordercolor=#000080 width=700 style='border:solid;'>";
	echo "<tr><td colspan='2' align=center><font size=3 color='#000080' face='arial' align=center><b>EL TRASLADO ".$consecutivoT." YA NO PUEDE SER ANULADO</td><tr>";
	echo "<tr><td colspan='2' align='center'><input type='button' name='aceptar' value='ACEPTAR' onclick='javascript:enter2()'></td><tr>";
	echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
	echo "</table></fieldset></form>";
}


function pintarFormulario($fuente, $numero, $fecha, $valor, $documento, $banco, $radio, $pagos, $total, $totalF, $cajCod, $cajDes, $consecutivoT, $clase, $id, $fuenteT)
{
	global $wbasedato;

	echo "<form name='forma1' action='transladopru.php' method='post'>";

	for($i=0; $i < count($pagos['codigo']); $i++)
	{
		if (isset ($fuente[$i][0]))
		{
			echo "<table align='center'>";
			if ($pagos['tipo'][$i]=='on')
			echo "<tr><td class='titulo2' colspan='7'>FORMA DE PAGO: ".$pagos['codigo'][$i]."-".$pagos['nombre'][$i]."</td></tr>";
			else
			echo "<tr><td class='titulo2' colspan='5'>FORMA DE PAGO: ".$pagos['codigo'][$i]."-".$pagos['nombre'][$i]."</td></tr>";

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
				echo "<tr>";

				if ($clase==1)
				echo "<td class='texto'><input type='checkbox' name='radio[".$i."][".$j."]' ".$radio[$i][$j]." ></td>";
				else
				echo "<td class='texto'>&nbsp;</td>";
				echo "<td class='texto'>".$fuente[$i][$j]."</td>";
				echo "<td class='texto'>".$numero[$i][$j]."</td>";
				echo "<td class='texto'>".$fecha[$i][$j]."</td>";

				if ($pagos['tipo'][$i]=='on')
				{
					echo "<td class='texto'>".$documento[$i][$j]."</td>";
					echo "<td class='texto'>".$banco[$i][$j]."</td>";
				}
				echo "<td class='texto'>$ ".number_format($valor[$i][$j],",","",".")."</td>";
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

function pintarBoton1($totalF, $cajas, $wcajF)
{
	echo "<table align='center'>";
	echo "<tr><td class='titulo4' >VALOR A TRASLADAR: $ ".number_format($totalF,",","",".")."</td></tr>";
	echo "<tr><td class='titulo4' >Caja destino: </font></b><select name='wcajF'>";
	for ($i=0;$i<count($cajas['codigo']);$i++)
	{
		if($cajas['codigo'][$i]!='')
		echo "<option>".$cajas['codigo'][$i]."-".$cajas['nombre'][$i]."</option>";
	}
	echo "</select></td>";
	echo "<tr><td class='titulo4' >&nbsp;</td></tr>";
	echo "<tr><td class='titulo4' ><input type='checkbox' name='transladar' disabled>Trasladar	&nbsp;<input type='button' value='ACEPTAR'  onclick='javascript:enter()'></td></tr>";
	echo "</table></br>";
	echo "</form>";
}

function pintarBoton2($totalF, $consulta)
{

	$fila='enter5("'.$totalF.'", "'.$consulta.'")';
	echo "<table align='center'>";
	echo "<tr><td class='titulo4' >VALOR TOTAL TRASLADADO: $ ".number_format($totalF,",","",".")."</td></tr>";
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
	$cuaAnt = '';
	consultarCaja(substr($user,2), &$cco, &$cajCod, &$cajDes, &$cuaAnt);

	pintarEncabezado($wautor);

	if($cajCod != '')
	{
		if (isset($transladar) and (!isset($wcajF) or $wcajF=='' ) )
		{
			pintarAlert3(); //DEBE SELECCIONAR LA CAJA CUANDO VA A TRANSLADAR
			unset($transladar);
		}

		if (isset($consulta) and $consulta!='')// consulta de un translado
		{
			$fuenteT=consultarFuente();
			consultarEncabezado($cajCod,$consulta, $cco, $fuenteT, &$wcajF, &$totalF, &$fec );
			if ($wcajF!='')
			{
				consultarRegistros ($cco, $fuenteT, $consulta, &$fuente, &$numero, &$fecha, &$valor, &$documento, &$banco, &$pagos, &$total, &$id);
				pintarConsulta2($cajCod, $cajDes, $consulta, $fec, $wcajF);
				pintarFormulario($fuente, $numero, $fecha, $valor, $documento, $banco, '', $pagos, $total, $totalF, $cajCod, $cajDes, $consulta,2, $id, $fuenteT);
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

				if(!isset($transladar)) //proceso previo antes de guardar el translado
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
							$registro=consultarRecibos($pagos['codigo'][$i], $cajCod, $cco);
							if ($registro)
							{
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
						$wcajF='';
					}else
					{
						$contador=1;
					}

					if ($contador>0)
					{
						// adecuo los valores de seleccion y calculo los totales
						calcularTotal($pagos, $fuente, $radio, $valor, &$total, &$totalF, &$radio);

						$cajas=consultarCajasPpa($wcajF);
						pintarFormulario($fuente, $numero, $fecha, $valor, $documento, $banco, $radio, $pagos, $total, $totalF, $cajCod, $cajDes, $consecutivoT,1, $id, $fuenteT);
						pintarBoton1($totalF, $cajas, $wcajF); //determina la actividad de guardar translados
					}else
					{
						pintarAlert2($cajCod);
					}
				}else// REALIZO LAS ACTIVIDADES DE TRANSLADO Y SU ALMACENAMIENTO
				{
					//calculo el total
					calcularTotal($pagos, $fuente, $radio, $valor, &$total, &$totalF, &$radio);

					//guardar el encabezado
					grabarEncabezado($fuenteT, $consecutivoT, $cajCod, $wcajF, $totalF, substr($user,2));
					$exp=explode('-', $wcajF);
					for ($i=0;$i<count($pagos['codigo']);$i++)
					{
						if (isset($fuente[$i]))
						{
							for ($j=0;$j<count($fuente[$i]);$j++)
							{
								if (isset ($radio[$i][$j]) and $radio[$i][$j]!='')
								{
									grabarDetalle($fuenteT, $consecutivoT, $numero[$i][$j], $fuente[$i][$j], $valor[$i][$j], $pagos['codigo'][$i], $cajCod, $id[$i][$j], substr($user,2));
									cambiarEstado($numero[$i][$j], $fuente[$i][$j], 'T', $exp[0], $pagos['codigo'][$i], $id[$i][$j]);
								}
							}
						}
					}
					incrementarConsecutivo($fuenteT);

					consultarEncabezado($cajCod,$consecutivoT, $cco, $fuenteT, &$wcajF, &$totalF, &$fec ); //consultar el documento guardado

					if (isset($fuenteT) and $fuenteT!='')
					{
						consultarRegistros ($cco, $fuenteT, $consecutivoT, &$fuente2, &$numero2, &$fecha2, &$valor2, &$documento2, &$banco2, &$pagos2, &$total2, &$id2);
						pintarConsulta3($cajCod, $cajDes, $consecutivoT, $fec, $wcajF);
						pintarFormulario($fuente2, $numero2, $fecha2, $valor2, $documento2, $banco2, '', $pagos2, $total2, $totalF, $cajCod, $cajDes, $consecutivoT ,2, $id2, $fuenteT);
						pintarBoton2($totalF, $consecutivoT); //determina la actividad de guardar translados
					}else //no se obtienen resultados para ese numero de envio
					{
						pintarAlert4($cajCod, $consecutivoT);
					}

				}
			}else  // 	PROCESO DE ANULACION
			{
				$permiso=consultarPermiso($pagos, $id, $fuenteT, $consecutivoT);
				if ($permiso) //se realiza anulación
				{
					for ($i=0;$i<count($pagos['codigo']);$i++)
					{
						if (isset($fuente[$i]))
						{
							for ($j=0;$j<count($fuente[$i]);$j++)
							{
								cambiarEstado($numero[$i][$j], $fuente[$i][$j], 'C', $cajCod, $pagos['codigo'][$i], $id[$i][$j]);
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
</body>
</html>
