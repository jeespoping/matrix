<?php
include_once("conex.php");

echo "
<html>
<head>
  <title>CONSULTA DE DEVOLUCIONES-POS</title>
</head>
<body>";

/**
 * PROGRAMA RECIBO DE DEVOLUCIÓN O NOTA CRÉDITO PARA EL POS
 * 
 * Muestra en pantalla la información de una transacción efectuada por el programa de devolución,
 * sin importar si es tipo devolución, nota crédito a particulas o a empresa, o anulación.  
 * Funciona como un reporte y como un programa llamado por otro. El reporte pide como parámetros fechas y
 * centro de costos, muestra al usuario las transacciones correspondientes a estos datos, ellos eligen
 * una y se muestra la información de esta en pantalla. 
 
 * @author Ana María Betancur Vargas
 * @version 2005-08-15
 * @created 2005-08-01
 * 
 * @table pos_000001	SELECT
 * @table pos_000003	SELECT
 * @table pos_000010	SELECT
 * @table pos_000011	SELECT
 * @table pos_000016	SELECT
 * @table pos_000017	SELECT
 * @table pos_000024	SELECT
 * @table pos_000028	SELECT
 * @table pos_000041	SELECT
 * @table pos_000055	SELECT 
 * @table usuarios		SELECT
 *  
 * @wvar Arr[][]		$cco	Almacena los centros de costos, con su código y descripción.
 * @wvar String[8]	$fecha1	Fecha de inicio de las busquedas, establecida por el usuario
 * @wvar String[8]	$fecha2	Fecha de Fin de las busquedas, establecida por el usuario
 * @wvar Array[][] 	$trans	Array con las transacciones encontradas. 
 * 							['Doc']:Número de la devolución en el documento del movimiento de inventario, 
 * 							['Vta']:Número de la venta a la que se le hizo la devolución, 
 * 							['Tipo']:Tipo de Transacción "Nota Crédito" o simple "Devolución"
 * @wvar String[4]	$wcco	Centro de costos de la transacción elegida.
 * @wvar Int			$doc		Número de la devolución, es decir, el documento del movimiento de inventario (mendoc, mdedoc)
 * @wvar Int			$conex		Id de conexión a la base de datos
 * @wvar Int			$vta 		Número de la venta
 * @wvar String		$usu		Usuario que realiza la devolución
 * @wvar Arr[][]		$articulos	Guarda la información de los artículos devueltos en esa transacción. 
 * 							["Codigo"]:Código del articulo, ["Nombre"]		Nombre del Artículo,
 *							["Cantidad"]:Cantidad devuelta para el artículo,
 *							["Valuni"]:Valor unitario del artículo,
 *							["Valtot"]:Valor del monto de la devolución del articulo en las cantidades mensionadas
 * @wvar Doubble		$total		Valor total de la devolución
 * @wvar String		$fecha_act	Fecha en que se realizo la transacción
 * @wvar String		$hora_act	Hora en la que se realizo la transacción
 * @wvar String 		$resp		Nombre del Responsable de la transacción
 * @wvar String 		$clidoc			Documento del Clíente
 * @wvar String		$clinom			Nombre del Clíente
 * @wvar String		$empnom			Nombre de la empresa responsable.
 * @wvar String		$motivo		Razón por la cual se efectua la transacción, en caso de ser una devolución no aplica o es igual a vacio.
 * 
 * 
 * @modified 2006-01-15 Se borra, de la funcion TraerDev, la busqueda de la fuente de notas crédito por ser innecesaria.
 * @modified 2006-01-15	Se encuentra un bug, la hora mostrada en el reporte no es la de la transacción si no la del momento en que se tira el reporte, 
 * se soluciona agregando como parámetro en la función TraerArticulo y se borra $hora_act=date("H:i:s");
 * @modified 2006-06-23 Se ponde dentro de la impresión el número de la factura
 * @modified 2005-12-27 Se modifica para que diga a que caja pertenece
 * @modified 2005-10-24 Se modifican las tablas para que sean variables, de farstore a $tipo_tablas. Igual se modifica la imagen del logo.
 * @modified 2005-10-06 Ingreso a la carpeta POS, Partir el programa en funciones
 * @modified 2005-10-17 Se añade la varible $tip_rc que dice que el recibo es de una devolución, se usa en el enclude info_pos.php
 * @modified 2005-10-18 Se hace una modificación para que las notas crédito tengan su número correspondiente.
 */

$wautor="Ana Maria Betancur.";
$wmodificado="Carolina Castano P.";
$wversion="2007-02-14";

//////////////////////////////////////////////////////////////FUNCIONES//////////////////////////////////////////////////////////////////

include_once("root/comun.php");

/**
 * Llena el array $cco con lod códigos y las descripciones de los centros de costos de la tabla $tipo_tablas._000003
 * 
 * @return void
 * @param String	$tipo_tablas	nombre ppal de las tablas en donde se buscan los Centros de costos
 * @param Arr[][]	$cco		Almacena los centros de costos, con su código y descripción
 * @param Int		$conex		Id de conexión a la base de datos.
 */

function TraerCco ($tipo_tablas, $cco, $conex)
{
	$q="SELECT Ccocod, Ccodes "
	."FROM ".$tipo_tablas."_000003 "
	."WHERE	Ccoest = 'on' ";
	$err=mysql_query($q,$conex);
	$num=mysql_num_rows($err);
	//echo mysql_error();
	if($num > 0)
	{
		for($i=0;$i<$num;$i++)
		{
			$row=mysql_fetch_array($err);
			$cco[$i]['Cod']=$row[0];
			$cco[$i]['Desc']=$row[1];
		}
	}
}


/**
 * Busca la información referente a las transacciones realizadas entre dos fechas.
 * 
 * @return void
 * @param String		$tipo_tablas	nombre ppal de las tablas en donde se buscan los Centros de costos
 * @param Array[][] 	$trans		Array con las transacciones encontradas
 *						['Doc']:Número de la devolución en el documento del movimiento de inventario, 
 * 						['Vta']:Número de la venta a la que se le hizo la devolución, 
 * 						['Tipo']:Tipo de Transacción "Nota Crédito" o simple "Devolución"
 * @param Int		$conex		Id de conexión a la base de datos.
 * @param String		$fecha1		Fecha de inicio de las busquedas, establecida por el usuario
 * @param String		$fecha2		Fecha de Fin de las busquedas, establecida por el usuario
 * @param String		$wcco 		Centro de Costos
 * 
 * @modified 2006-01-15 Se modifica todo, de modo que la funete principal de información se ala tabla 000055. 
 * @modified 2012-01-26 Se adicionó la función numeroBono que permite mostrar el número del bono en la impresión - Mario Cadavid
 */

function consultarConcepto()
{
	global $conex;
	global $tipo_tablas;

	//busco el codigo para el movimiento de venta
	$q="Select concod "
	."FROM ".$tipo_tablas."_000008 "
	."WHERE	conmve	= 'on' "
	."and	conest	= 'on' ";
	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR EL CODIGO DEL MOVIMIENTO DE INVENTARIO EN VENTAS ".mysql_error());
	$num = mysql_num_rows($err) or die (mysql_errno()." -NO SE HA ENCONTRADO EL CODIGO DEL MOVIMIENTO DE INVENTARIO EN VENTAS  ".mysql_error());
	$row=mysql_fetch_array($err);
	$movven=$row['0'];

	//busco el codigo para el movimiento de devolucion
	$q="Select concod "
	."FROM ".$tipo_tablas."_000008 "
	."WHERE	concan	= '".$movven."' "
	."and	conest	= 'on' ";
	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR EL CODIGO DEL MOVIMIENTO DE INVENTARIO EN DEVOLUCIONES".mysql_error());
	$num = mysql_num_rows($err) or die (mysql_errno()." -NO SE HA ENCONTRADO EL CODIGO DEL MOVIMIENTO DE INVENTARIO EN DEVOLUCIONES  ".mysql_error());
	$row=mysql_fetch_array($err);
	$movdev=$row['0'];

	return $movdev;
}

function TraerDev ($tipo_tablas, &$trans, $conex, $fecha1, $fecha2, $wcco, $movdev)
{

	$q="SELECT mendoc, menfac "
	. "FROM ".$tipo_tablas."_000010 "
	."WHERE	mencco = '".$wcco."' "
	."AND	mencon='".$movdev."' and menfec between '".$fecha1."' AND '".$fecha2."' ";
	$err=mysql_query($q,$conex);
	$num=mysql_num_rows($err);
	if($num > 0)
	{
		for($i=0;$i<$num;$i++)
		{
			$row=mysql_fetch_array($err);
			$trans['Doc'][$i]=$row['mendoc'];
			$trans['Vta'][$i]=$row['menfac'];

			$q="SELECT Ventcl, Vennit, Vencod "
			."FROM ".$tipo_tablas."_000016 "
			."WHERE Vennum= '".$trans['Vta'][$i]."' and vencco='".$wcco."' ";

			$err2=mysql_query($q,$conex);
			$num2=mysql_num_rows($err2);
			if($num2 > 0)
			{
				$row2=mysql_fetch_array($err2);
				$trans['Tcl'][$i]=$row2[0];
				$trans['Nit'][$i]=$row2[1];
				$trans['Cod'][$i]=$row2[2];
			}
		}
	}
}

/**
 * Trae la información del cliente que realizo la venta.
 * 
 * @return void
 * @param String	$tipo_tablas	Nombre del dueño de las tablas
 * @param Int		$vta 			Número de la venta
 * @param String	$wcco			Centro de costos
 * @param Int		$conex			Id de conexión a la base de datos
 * @param String 	$clidoc			Documento del Clíente
 * @param String	$clinom			Nombre del Clíente
 * @param String	$empnom			Nombre de la empresa responsable.
 */
function  TraerCaja($tipo_tablas,$wcco,&$caja, $vencaj, $conex)
{

	$q= "select Cajdes from ".$tipo_tablas."_000028 "
	."where	Cajcod = '".$vencaj."' "
	."and	Cajcco = '".$wcco."' ";
	$err=mysql_query($q,$conex);
	$num=mysql_num_rows($err);
	if($num>0)
	{
		$row=mysql_fetch_array($err);
		$caja = $row["Cajdes"];
	}


}

/**
 * Trae toda la información de los articulos devueltos en la transacción y los ingresa al array $articulos
 * 
 * @return void
 * @param String		$tipo_tablas	Nombre del dueño de las tablas
 * @param Int			$doc			Número de la devolución en el documento del movimiento de inventario
 * @param Int			$conex			Id de conexión a la base de datos
 * @param Arr[][]		$articulos		Guarda la información de los artículos devueltos en esa transacción.
 *						 ["Codigo"]:Código del articulo,
 *						 ["Nombre"]:Nombre del Artículo,
 *						 ["Cantidad"]:Cantidad devuelta para el artículo,
 *						 ["Valuni"]:Valor unitario del artículo,
 *						 ["Valtot"]:Valor del monto de la devolución del articulo en las cantidades mensionadas
 * @param Doubble		$total			Valor total de la devolución
 * @param String		$fecha_act		Fecha en que se realizo la transacción
 * @param String		$hora_act		Hora en la que se realizo la transacción
 */

function TraerArticulos ($tipo_tablas,$doc,$conex,&$articulos,&$total,&$fecha_act, &$hora_act, $movdev, $vennum)
{
	$total=0;

	$q="SELECT Mdeart,Artnom,Mdecan,Vdevun,  Vdepiv, Vdecan, Vdedes, ".$tipo_tablas."_000011.Fecha_data, ".$tipo_tablas."_000011.Hora_data "
	."FROM  ".$tipo_tablas."_000011, ".$tipo_tablas."_000017, ".$tipo_tablas."_000001 "
	."WHERE	Mdecon = '".$movdev."' "
	."AND	Mdedoc = '".$doc."' "
	."AND	Vdenum = '".$vennum."' "
	."AND	Vdeart = Mdeart "
	."AND	Artcod = Mdeart ";

	$err=mysql_query($q,$conex);
	//echo mysql_errno().":".mysql_error();
	$num=mysql_num_rows($err);

	if($num > 0)
	{
		for($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$articulos[$i]["Codigo"]=$row["Mdeart"];
			$articulos[$i]["Nombre"]=$row["Artnom"];
			$articulos[$i]["Cantidad"]=$row["Mdecan"];
			$descuento=round(((($row["Vdepiv"]/100+1)*$row["Vdedes"])/$row["Vdecan"]),0);
			$articulos[$i]["Valuni"]=$row["Vdevun"]-$descuento;
			$articulos[$i]["Valtot"]=$articulos[$i]["Valuni"]*$row["Mdecan"];
			$total=$total+$articulos[$i]["Valtot"];
		}

		$fecha_act=$row["Fecha_data"];
		$hora_act=$row["Hora_data"];
	}
}

/**
 * Traela información faltante de la venta.
 * @return void
 * @param String	$tipo_tablas	Nombre del dueño de las tablas
 * @param Int		$doc			Número de la devolución en el documento del movimiento de inventario
 * @param String	$wcco			Centro de costos
 * @param Int		$conex			Id de conexión a la base de datos
 * @param Int		$vta 			Número de la venta
 * @param String	$usu			Usuario que realiza la devolución
 */
function TraerVenta ($tipo_tablas,$doc,$wcco,$conex,&$vennum,&$usu, &$ven, $movdev)
{
	//busco el codigo de la venta para el numero de la devolucion de inventario
	$q="SELECT Menfac, Menusu "
	."FROM ".$tipo_tablas."_000010 "
	."WHERE	Mencon = '".$movdev."' "
	."AND	Mendoc = '".$doc."' "
	."AND	Mencco = '".$wcco."' ";
	//."AND	Menest = 'on' ";
	$err=mysql_query($q,$conex);
	$num=mysql_num_rows($err);
	if($num > 0)
	{
		$row = mysql_fetch_array($err);
		$vennum = $row["Menfac"];
		$usu = $row["Menusu"];
	}


	$q="SELECT Ventcl, Vencmo, Vennfa, Vencod, Vencaj, Vennit, Vencod "
	."FROM ".$tipo_tablas."_000016 "
	."WHERE Vennum= '".$vennum."' and vencco='".$wcco."' ";
	$err=mysql_query($q,$conex);
	$num=mysql_num_rows($err);
	if($num > 0)
	{
		$ven = mysql_fetch_array($err);
		if($ven['Vennfa'] == "")
		{
			$ven['Vennfa']='NO APLICA';
		}
	}
}

/**
 * Trae el nombre del responsable
 *
 * @param Int	$conex		Id de conexión a la base de datos
 * @param String $resp		Nombre del Responsable de la transacción
 */
function TraerResp($conex,$resp){

	$q="SELECT Descripcion "
	."FROM usuarios "
	."WHERE Codigo='".$resp."' ";
	$err=mysql_query($q,$conex);
	$num=mysql_num_rows($err);
	if($num > 0) {
		$row = mysql_fetch_array($err);
		$resp= $row['Descripcion'];
	}
}

function buscarEmpresa($Vencod, $Ventcl, &$emp)
{
	global $conex;
	global $tipo_tablas;

	$q= "select Empcod, Empnit, Empnom, Emptem, Empfac, Emptar from ".$tipo_tablas."_000024 "
	."where Empcod = ".$Vencod." "
	."AND	Emptem = '".$Ventcl."' "
	."AND	Empest = 'on'";

	$err=mysql_query($q,$conex);
	$num=mysql_num_rows($err);
	if($num>0)
	{
		$emp=mysql_fetch_array($err);
		return true;
	}else
	{
		return false;
	}

}

function buscarCliente($Vennit, &$cli)
{
	global $conex;
	global $tipo_tablas;

	//busco los datos del cliente de la venta
	$q= "select Clidoc, Clinom, Clitip, Clipun, Clite1 from ".$tipo_tablas."_000041 "
	."where Clidoc	= '".$Vennit."'";
	$err=mysql_query($q,$conex);
	$num=mysql_num_rows($err);

	if($num>0)
	{
		$cli=mysql_fetch_array($err);
		return true;
	}
	else
	{
		$q= "select Clidoc, Clinom, Clitip, Clipun, Clite1 from ".$tipo_tablas."_000041 "
		."where Clidoc	= '9999'";
		$err=mysql_query($q,$conex);

		$cli=mysql_fetch_array($err);
		return true;
	}
}

function buscarBono ($tipo_tablas,$doc,$wcco,$conex,&$vennum )
{
	//busco la fuente del bono

	$q="SELECT Carfue "
	."FROM ".$tipo_tablas."_000040 "
	."WHERE	cardca= 'on' "
	."and	carest = 'on'";
	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR LA FUENTE PARA LBONOS ".mysql_error());

	$num=mysql_num_rows($err)or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR LA FUENTE PARA BONOS ".mysql_error());

	if($num>0)
	{
		$row=mysql_fetch_row($err);
		$fue=$row[0]; //fuente de la transaccion
	}

	$q="SELECT * "
	."FROM ".$tipo_tablas."_000055 "
	."WHERE tracco='".$wcco."' and traven= '".$vennum."' and trafue='".$fue."' and traest='on' and tradev='".$doc."' ";
	$err=mysql_query($q,$conex);
	$num=mysql_num_rows($err);
	if($num > 0)
	{
		return true;
	}

	return false;
}

function numeroBono ($tipo_tablas,$doc,$wcco,$conex,&$vennum )
{
	//busco la fuente del bono

	$q="SELECT Carfue "
	."FROM ".$tipo_tablas."_000040 "
	."WHERE	cardca= 'on' "
	."and	carest = 'on'";
	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR LA FUENTE PARA LBONOS ".mysql_error());

	$num=mysql_num_rows($err)or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR LA FUENTE PARA BONOS ".mysql_error());

	if($num>0)
	{
		$row=mysql_fetch_row($err);
		$fue=$row[0]; //fuente de la transaccion
	}

	$q="SELECT Tranum "
	."FROM ".$tipo_tablas."_000055 "
	."WHERE tracco='".$wcco."' and traven= '".$vennum."' and trafue='".$fue."' and traest='on' and tradev='".$doc."' ";
	$err2=mysql_query($q,$conex);
	$row2=mysql_fetch_row($err2);
	return $row2[0];
}

function buscarAnulacion ($tipo_tablas,$doc,$wcco,$conex, $vennum )
{
	//busco la fuente del bono

	$q="SELECT Carfue "
	."FROM ".$tipo_tablas."_000040 "
	."WHERE	caravt= 'on' "
	."and	carest = 'on'";
	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR LA FUENTE PARA ANULACION ".mysql_error());

	$num=mysql_num_rows($err)or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR LA FUENTE PARA ANULACION ".mysql_error());

	if($num>0)
	{
		$row=mysql_fetch_row($err);
		$fue=$row[0]; //fuente de la transaccion
	}

	$q="SELECT * "
	."FROM ".$tipo_tablas."_000055 "
	."WHERE tracco='".$wcco."' and traven= '".$vennum."' and trafue='".$fue."' and traest='on' and tradev='".$doc."' ";
	$err=mysql_query($q,$conex);
	$num=mysql_num_rows($err);
	if($num > 0)
	{
		$row = mysql_fetch_array($err);
		$motivo = $row["Tramot"];
	}
	else
	{
		$motivo = '';
	}

	return $motivo;
}

function buscarNota($tipo_tablas,$doc,$wcco,$conex, $vennum)
{
	//busco la fuente del bono

	$q="SELECT  Ccofnc "
	."FROM ".$tipo_tablas."_000003 "
	."WHERE	Ccocod = '$wcco' "
	."and	Ccoest = 'on'";
	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR LA FUENTE PARA LA NOTA CREDITO ".mysql_error());

	$num=mysql_num_rows($err)or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR LA FUENTE PARA LA NOTA CREDITO ".mysql_error());
	if($num>0)
	{
		$row=mysql_fetch_row($err);
		$fue=$row[0]; //fuente de la transaccion
	}

	$q="SELECT renobs "
	."FROM ".$tipo_tablas."_000021, ".$tipo_tablas."_000020 "
	."WHERE rdecco='".$wcco."' and rdemiv= '".$doc."' and rdevta='".$vennum."' and rdeest='on' and rdefue='".$fue."' "
	." and rdecco=rencco and renest='on' and renfue=rdefue and rennum=rdenum ";

	$err=mysql_query($q,$conex);
	$num=mysql_num_rows($err);
	if($num > 0)
	{
		$row = mysql_fetch_row($err);
		$motivo = $row[0];
	}
	else
	{
		$motivo = '';
	}

	return $motivo;
}
//////////////////////////////////////////////////////////////INICIACION DE VARIABLES//////////////////////////////////////////////////////
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
	$key = substr($user,2,strlen($user));
	

	


	$AzulClar="006699";  //Azul claro
	$AguaOsc="#57C8D5";	//Aguamarina Oscuro



	////////////////////////////////////////////////////////PROGRAMA//////////////////////////////////////////////////////////

	if(!isset($wcco))//centro de costos una vez ya se escogio la devolucion
	{
		echo "<table align='right'>" ;
		echo "<tr>" ;
		echo "<td><font color=\"#D02090\" size='2'>Autor: ".$wautor."</font></td>";
		echo "</tr>" ;
		echo "<tr>" ;
		echo "<td><font color=\"#D02090\" size='2'>Modificado por: ".$wmodificado."</font></td>" ;
		echo "</tr>";
		echo "<tr>" ;
		echo "<td><font color=\"#D02090\" size='2'>Version: ".$wversion."</font></td>" ;
		echo "</tr>" ;
		echo "</table></br></br></br>" ;

		echo "<form action='' method='POST'";

		if(!isset($cco)) //nombre del select de centros de costo, se muestra primer pantalla de seleccion de los datos para la consulta
		{
			TraerCco($tipo_tablas,&$cco,$conex); //arma el select de centros de costo
			if (!isset($fecha1))
			{
				$fecha1=date("Y-m-d");
				$fecha2=date("Y-m-d");
			}

			echo "<br><br><center><table border width='60%'>";
			echo "<tr><td align=center><img src='/matrix/images/medical/POS/logo_".$tipo_tablas.".png' WIDTH=388 HEIGHT=70></td></tr>";
			echo "<tr><td align=center bgcolor=".$AzulClar."><font size=3 text color=#FFFFFF><b>REPORTE DE DEVOLUCIONES</b></font></td></tr>";

			$cal="calendario('fecha1','1')";

			echo "<tr><td align=left bgcolor=".$AzulClar." ><font size=3 color='#FFFFFF'><b>FECHA INICIAL: </b></font>";
			echo campoFechaDefecto("fecha1", $fecha1);
			echo "</td></tr>";
			?>
	<?php

			echo "<tr><td align=left bgcolor=".$AzulClar." ><font size=3 color='#FFFFFF'><b>FECHA FINAL: </b></font>";
			echo "&nbsp;&nbsp;&nbsp;";
			echo campoFechaDefecto("fecha2", $fecha2);
			echo "</td></tr>";
			?>
	<?php

			echo "<tr><td align=left bgcolor=".$AzulClar." ><font size=3 color='#FFFFFF'><b>CENTRO DE COSTOS: </b></font>";
			echo "<select name='cco'>";
			for($i=0;$i<count($cco);$i++)
			{
				//Trae los centros de costos
				echo "<option value='".$cco[$i]['Cod']."'>".$cco[$i]['Cod']."-".$cco[$i]['Desc']."</option>";
			}
			echo "</select></td></tr>";
			echo "<tr><td align='center'  bgcolor='".$AzulClar."'><input type='submit' name='aceptar' value='ACEPTAR'></td></tr>";
		}
		else //se despliga lista de devoluciones en las fechas seleccionadas
		{

			echo "<br><br><center><table border width='60%'>";
			echo "<tr><td align=center colspan='4' ><img src='/matrix/images/medical/POS/logo_".$tipo_tablas.".png' ></td></tr>";
			echo "<tr><td align=center bgcolor=".$AzulClar." colspan='4'><font size=3 text color=#FFFFFF><b>REPORTE DE DEVOLUCIONES</b></font></td></tr>";

			$movdev=consultarConcepto();
			TraerDev($tipo_tablas,&$trans,$conex,$fecha1,$fecha2,$cco, $movdev);

			if (isset ($trans['Doc']) and count($trans['Doc'])>0)
			{
				for ($i=0; $i<count($trans['Doc']); $i++)
				{
					BuscarEmpresa($trans['Cod'][$i], $trans['Tcl'][$i], &$emp); //busco datos de la empresa que se devuelven en $emp

					//identificamos que tipo de certificado es:
					$exp=explode('-',$trans["Tcl"][$i]);
					$trans["Tcl"][$i]=trim($exp[0]);
					switch ($trans["Tcl"][$i])
					{
						case '01': //caso en que sea particular
						$bono=buscarBono ($tipo_tablas,$trans["Doc"][$i],$cco,$conex,$trans["Vta"][$i] );
						if ($bono)
						{
							$trans['Tipo'][$i]='BONO A PARTICULAR';
						}
						else
						{
							$trans['Tipo'][$i]='NOTA CREDITO A PARTICULAR';
						}
						break;

						default: //caso en que sea empresa
						// si la empresa genera factura al momento de la venta
						if ($emp['Empfac']=='on')
						{
							$trans['Tipo'][$i]='NOTA CREDITO A EMPRESA';
						}
						else
						{
							$trans['Tipo'][$i]='ANULACION A EMPRESA';
						}
						break;
					}
				}

				echo "<tr><td align=left bgcolor=".$AzulClar."  colspan='4'><font size=3 color='#FFFFFF'>SELECCIONE LA DEVOLUCION:";
				echo "<tr><td bgcolor=".$AguaOsc." width='5%'>&nbsp;</td>";
				echo "<td bgcolor=".$AguaOsc." width='10%'><b>Dev.</b></td>";
				echo "<td bgcolor=".$AguaOsc." width='10%' ><b>Venta</b></td>";
				echo "<td bgcolor=".$AguaOsc." width='35%'><b>Tipo</b></td></tr>";
				for($i=0;$i<count($trans['Doc']);$i++)
				{
					//Trae las devoluciones
					echo "<tr><td width='5%' align=center><input type='radio' name='doc' value='".$trans['Doc'][$i]."'></td>";
					echo "<td width='10%'>".$trans['Doc'][$i]."</td>";
					echo "<td width='10%'>".$trans['Vta'][$i]."</td>";
					echo "<td width='35%'>".$trans['Tipo'][$i]."</td></tr>";
				}

				echo "<input type='hidden' name='wcco' value='$cco'>";
				echo "<tr><td align='center'  bgcolor='".$AzulClar."' colspan='4' ><input type='submit' name='aceptar' value='ACEPTAR'></td></tr>";
			}
			else
			{
				echo "<tr><td colspan='2' ALIGN=CENTER><font size=3 color='#000080' face='arial'><b>No se ha encontrado ninguna devolucion en el rango de fechas</td><tr>";
				echo "<tr><td colspan='2' align='center'><input type='submit' name='aceptar' value='VOLVER'></td><tr>";
			}

		}

		echo "<input type='hidden' name='tipo_tablas' value='$tipo_tablas'>";
		echo "</table></form>";

	}else //imprimir en pantalla la devolucion, algunos programas que lo invocan empiezan desde este punto
	{
		//conculto concepto de devolucion
		$movdev=consultarConcepto();

		//consultamos los datos de la venta
		TraerVenta($tipo_tablas,$doc,$wcco,$conex,&$vennum,&$usu, &$ven, $movdev);
		BuscarEmpresa($ven['Vencod'], $ven['Ventcl'], &$emp); //busco datos de la empresa que se devuelven en $emp
		BuscarCliente($ven['Vennit'], &$cli);

		//TRAER DATOS de la devolución
		TraerArticulos ($tipo_tablas,$doc,$conex,&$articulos,&$total,&$fecha_act, &$hora_act, $movdev, $vennum);
		$resp=$usu;
		TraerResp($conex,&$resp);
		TraerCaja ($tipo_tablas,$wcco,&$caja, $ven['Vencaj'], $conex);

		//identificamos que tipo de certificado es:
		$exp=explode('-',$ven["Ventcl"]);
		$ven["Ventcl"]=trim($exp[0]);
		switch ($ven['Ventcl'])
		{
			case '01': //caso en que sea particular
			$bono=buscarBono ($tipo_tablas,$doc,$wcco,$conex,&$vennum );
			if ($bono)
			{
				$nota=false;
				$anulacion=false;
				$particular=false;
				$bonnum=numeroBono($tipo_tablas,$doc,$wcco,$conex,&$vennum );
				$devtip='BONO A PARTICULAR &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Nro: '.$bonnum;
			}
			else
			{
				$devtip='NOTA CREDITO A PARTICULAR';
				$nota=false;
				$anulacion=false;
				$particular=false;
				$motivo=buscarNota($tipo_tablas,$doc,$wcco,$conex, $vennum );
			}
			break;

			default: //caso en que sea empresa
			// si la empresa genera factura al momento de la venta
			if ($emp['Empfac']=='on')
			{
				$devtip='NOTA CREDITO A EMPRESA';
				$nota=true;
				$anulacion=false;
				$particular=false;
				$bono=false;
				$motivo=buscarNota($tipo_tablas,$doc,$wcco,$conex, $vennum );
			}
			else
			{
				$devtip='ANULACION A EMPRESA';
				$nota=false;
				$anulacion=true;
				$particular=false;
				$bono=false;
				$motivo=buscarAnulacion ($tipo_tablas,$doc,$wcco,$conex,&$vennum );
			}
			break;
		}

		$treintaDias=30*24*60*60; //Conversion de 30 dias a segundos: 30 dias, 24 horas, 60 min , 60 sec
		if(!isset($fecha_act))
		{
			$fecha_act=mktime(date("H"),date("i"),date("s"),date("m"),date("d"),date("Y"));
		}else
		{
			$fecha_act=mktime(0,0,0,intval(substr($fecha_act,5,2)),intval(substr($fecha_act,8,2)),intval(substr($fecha_act,0,4)));
		}

		//$hora_act=date("H:i:s");
		$fecha_fin=date("Y-m-d",($fecha_act+$treintaDias));
		$fecha_act=date("Y-m-d",$fecha_act);
		$fechaPos=$fecha_act;
		$tip_rc='DEV';
		include_once("pos/info_pos.php");

		/*************************************************************
		*						IMPRESIÓN EN PANTALLA
		**********************************************************/
		echo "<table border='0' align='center' width='300'>";
		echo "<tr><td align='center' colspan='5' ><img src='/matrix/images/medical/POS/logo_".$tipo_tablas.".png' WIDTH='510' HEIGHT='200'></td></tr></table>";

		echo $infoPosIni;

		echo "<center><table border='0' width='250'>";
		echo"<tr><td colspan='5'><br></td></tr>";
		echo"<tr><td colspan='5' align='left'><font size=7><b>$devtip</b></td></tr>";
		echo"<tr><td colspan='5' align='left'><font size=6><b>Caja :$caja</b></td></tr>";
		echo"<tr><td colspan='5' align='left'><font size=6><b>Resp. :$resp</b></td></tr>";

		echo "<tr><td colspan=3 align=left><font size=6>Hora: ".$hora_act."</font></td>";
		echo "<td colspan=2 align=right><font size=6>Fecha: ".$fecha_act."</font></td></tr>";
		echo "<tr><td colspan=3 align=left><font size=6>Valido por 30 dias</font></td>";
		echo "<td colspan=2 align=right><font size=6>Vence: ".$fecha_fin."</font></td></tr>";

		echo "<tr><td colspan=5><font size=6>Factura: ".$ven['Vennfa']."</font></td></tr>";
		echo "<tr><td colspan=5><font size=6>Mov. Inv. Nro: ".$doc." Venta Nro: $vennum</font></td></tr>";
		echo "<tr><td colspan=5><font size=6>Responsable: ".$emp['Empnom']."</font></td></tr>";
		echo "<tr><td colspan=5><font size=6>Cedula/Nit: ".$cli['Clidoc']."</font></td></tr>";
		echo "<tr><td colspan=5><font size=6>Beneficiario: ".$cli['Clinom']."</font></td></tr>";
		echo "<tr><td colspan=5><font size=6><br></font></td></tr>";
		echo "<tr><td colspan=5><font size=7><b>Articulos</b></font></td></tr>";
		$art=count($articulos);
		for($i=0;$i<$art;$i++)
		{
			echo "<tr><td colspan=3><font size=6>".$articulos[$i]["Codigo"]."-</font><font size=5>".$articulos[$i]["Nombre"]."</font>";
			echo "<td align='right'><font size=6>Cant.".$articulos[$i]["Cantidad"]."</font></td>";
			echo "<td align='right'><font size=6>$".number_format($articulos[$i]["Valtot"],2,'.',',')."</font></td></tr>";
		}


		echo "<TR><TD colspan=5> ________________________________________________________________________________________________________________
		<br><br></TD></TR>";

		echo "<tr><td colspan=3><font size=7><b>Total </b></font></td>";
		echo "<td align='right' colspan=2><font size=7>".number_format(($total),2,',','.')."</font></td></tr></table>";

		if(!$bono)
		{
			echo "<tr><td colspan=3 align='left'><left><font size=7><b>Motivo </b></font></td>";
			echo "<tr><td colspan=2 align='left'><left><font size=6>".$motivo."</font></td>";
		}

		echo "<br><br>".$infoPosFin;
	}
}

?>
</body>
</html>
