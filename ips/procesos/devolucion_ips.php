<?php
include_once("conex.php");
 /************************************************************************************
 ************** IMPORTANTE - LEER ANTES DE MODIFICAR O USAR ESTE SCRIPT **************
 *************************************************************************************
 * Se debe tener en cuenta que este script graba datos en unix, especificamente en	 *
 * las tablas fanovacc y fasalacc, por esto en local este archivo siempre debe tener *
 * estos nombres de tabla modificados a fanovacc1 y fasalacc1  que son tablas de 	 *
 * pruebas, de modo que no se grabe en tablas de datos reales en Unix los datos de	 *
 * prueba que se hagan por ventas en modo local. 									 *
 * Solo cuando se vaya a subir el archivo a producción se deben poner los nombres de *
 * tablas reales: fanovacc y fasalacc								 				 *
 *************************************************************************************/
 //---
 //---> 2019-02-14 camilo zapata: modificación de query que busca si la venta existe puesto que el join con la tabla 3 de uvglobal no existía. buscar "ccocod = concod"
?>
<html>
<head>
  <title>DEVOLUCION - POS</title>

    <script type="text/javascript">

    function enter(val)
    {
    	document.forma.bandera.value=val;
    	document.forma.submit();
    }

  </script>

</head>
<body>
<?php

/**
 * PROGRAMA PRINCIPAL DE DEVOLUCIÓN DE VENTAS
 *
 * Permite devolver articulos de una venta, generando un movimiento de inventario de devolucion.
 * Adicionalemnte se realizan uno o varios de los siguientes casos:
 *
 * Facturas:
 * 	 Ventas que generan factura (particulares y empresas que generan facturas al momento de la venta). En este caso se realiza una nota credito
 * 	 Ventas que no generan factura (empresas que no generan factura al momento de la venta). En este caso se realiza una anulacion a la venta
 *
 * Devolucion de dinero:
 *   Con bono:  para particulares que realizan una devolución comun. En este caso se crea un bono
 * 	 Con devolucion de dinero (nota a particulares realizada por empleado o empresas con cuota moderadora mayor a cero). En este caso se realiza una devolucion de dinero
 *   Sin devolucion de dinero (empresas con cuota moderadora igual a cero). No se realiza nada
 *
 * Con devolucion de nomina:
 *   Para empresas que decuentan por nomina. Se realiza devolucion de nomina
 *
 * Con devolucion de Puntos:
 *   Para la venta a clientes con tarjeta de puntos. Se realiza devolucion de puntos
 *
 *
 * MODO DE FUNCIONAMIENTO:
 *
 *  Cuando es la primera vez que se entra al programa, no esta setiado el centro de costos del Usuario ($wcco),
 *  se consigue el centro de costos para el usuario, y se valida que este registrado como cajero en la tabla 30 para ese centro de costos,
 *  en caso de no estar registrado no permite el uso del programa, desplegando mensaje (pintarAlerta()).
 *  Una vez se consigue el centro de costos, el sistema inicializa el formulario html y pregunta por el numero de la venta
 *
 *  Cuando se ingresa el numero de la venta, esta setiado el centro de costos($wcco) y el numero de la venta ($Vennum),
 *  pero no se ha mandado la variable bandera. En esta caso el sistema consulta el concepto de movimiento de inventario de devoluciones
 *  que sera el utilizado para grabar la devolucion de inventario en la base de datos y valida que el numero de la venta ingresado exista.
 *  Si el numero de la venta no existe despliega un mensaje de alerta (pintarAlerta), en caso contrario
 *  busca los datos del cliente y del responsable (empresa o particular) y según el tipo de responsable realizará
 *  las siguientes validaciones:
 *
 *  Particular:
 * 		Que no hayan sido devueltos ya todos los articulos de la venta (se pueden hacer devoluciones parciales)
 *      El resto de validaciones se ponen en true (significa que las demas las pasa por ser particular)
 *
 * 	Empresa:
 *    busca si la empresa descuenta de nomina a empleado y valida que en caso de que si, no se haya pagado la primera cuota
 * 	  Si la empresa genera factura al momento de la venta se valida que no se haya generado ya la factura
 *    Si la empresa SI genera factura al momento de la venta se valida que la empresa no haya pagado ya la factura, es decir
 *    que no tenga ni recibos, ni abonos, ni notas credito (en tabla 18)
 *    Se valida que no haya una devolucion previa (no se pueden hacer devoluciones parciales)
 *
 *   En ambos casos se consultan los articulos de la venta y las cantidades, para particular se descuentan los articulos
 *   de devoluciones anteriores para establecer las cantidades maximas a devolver.
 * 	 Cuando se han pasado todas las validaciones, el programa despliega la información de la venta y los artículos.
 *   Para particular, el usuario tiene o no la posibilidad de elegir que artículos, y en que cantidad (respetando las cantidades maximas),
 *   En caso de que no sea una venta partícular, la transacción se realiza por el total de los artículos.
 *
 *   Al presionar el botón aceptar, estan setiados el centro de costos ($wcco), el numero de la venta y bandera con valor igual a 3
 *   En este caso el programa realiza la validacion para particulares de que la cantidad seleccionada no sobrepase la cantidad permitida
 *   para cada articulo de la venta y recalcula los totales verificando que se incluya al menos un articulo.
 * 	 Si se ha activado el checkbox realizar y se pasa la validacion para particulares anterirmente mencionada,
 *   el programa le presenta la opcion para particular de realizar nota crédito (genera devolucion de dinero) o una devolución (genera bono).
 *   El programa presenta para todos los tipos de cliente la pantalla para ingresar el pasword (para cada usuario)
 *   y el motivo (obligatorio excepto para devolucion comun a particular).
 *
 *   En caso de que todo este setiado y la bandera sea igual a 5, el sistema realiza la validacion de password y motivo
 *   y en caso de pasarlas se realizan las transacciones de la siguiente manera:
 *
 *  1. Si hay factura se hace una nota credito (afecta tablas 20,21,18 y 65)
 * 	2. Se realiza el movimiento de inventario (afecta tablas 10, 11 y 7)
 *  3. Si no hay factura, se realiza anulacion (afecta tablas 55, 16 y 17)
 *  4. Si hay devolucion de dinero, se realiza devolucion de dinero (afecta tablas 20, 21 y 22)
 *  5. Si se ha escogido la opcion devolucion para particular, se realiza bono (afecta tabla 55)
 *  6. Si utiliza nomina, se cancela la deuda en nomina (afecta tabla 46 )
 *  7. Si utiliza puntos, se devuelven los puntos (afecta tabla 59 y 60 )
 *
 * @author Carolina Castaño P
 * @version 2011-10-04
 * @created 2007-02-12.
 *
 * @table 000001 SELECT
 * @table 000003 SELECT, UPDATE
 * @table 000007 SELECT, UPDATE
 * @table 000008 SELECT, UPDATE
 * @table 000010 SELECT, INSERT
 * @table 000011 SELECT, INSERT
 * @table 000016 SELECT, UPDATE
 * @table 000017 SELECT, UPDATE
 * @table 000018 SELECT, UPDATE
 * @table 000020 INSERT
 * @table 000021 SELECT, INSERT
 * @table 000022 SELECT, INSERT, UPDATE
 * @table 000040 SELECT, UPDATE
 * @table 000046 UPDATE
 * @table 000055 INSERT
 * @table 000059 SELECT, UPDATE
 * @table 000060 UPDATE
 *
 *
 * @wvar $articulos variable que es true cuando hay articulos de la venta, para ser devueltos
 * 							 en particulares cuando no se han devueltos todos los articulos (determinado por la funcion validarDisponibilidad)
 *                           en empresas cuando no se han hecho devoluciones previas a la venta (determinado por la funcion validarDevoluciones)
 *
 * @wvar $bandera lleva el hilo del programa presentando las diferentes opciones:
 * 				 no setiada: se realizan las validaciones a la venta para permitir la devolucion
 *               3:  se realiza validacion sobre articulos escogidos para devolucion y se calculan totales
 * 			     5:  se realiza validacion sobre el password y el motivo y si se psasn se ejecuta la transaccion
 * @wvar $cli  vectos con todos los datos del cliente
 * 			['Clidoc'] documento del cliente
 * 			['Clinom'] nombre del cliente
 * 			['Clitip'] tipo de cliente
 * 			['Clipun'] puntos del cliente
			['Clite1'] telefono del cliente
 * @wvar $cliente variable que en true indica que se ha encontrado un cliente para la venta
 * @wvar $emp vectro con todos los datos de la empresa
 *          ['Empcod'] codigo del responsable
 * 			['Empnit'] nit del responsable
 * 			['Empnom'] nombre del responsable
 * 			['Emptem'] tipo de empresa del respnsable
 * 			['Empfac'] genera factura o no
 * 			['Emptar'] tarifa asignada
 * @wvar $empresa variable que en true indica que se ha encontrado la empresa responsable de la venta
 * @wvar $existe  variable que en true indica que la vneta existe
 * @wvar $exp para almacenar explodes
 * @wvar $motivo variable que guarda el motivo de la devolucion ingresado por el usuario
 * @wvar $movdev variable que guarda el concpeto utilizado para registrar movimiento por devoluciones en el inventario
 * @wvar $nomina variable que en true indica que se pasa la validacion de nomina, porque no se utiliza o porque no se ha pagado la primera cuota
 * @wvar $numart numero de articulos de la venta
 * @wvar $numdev numero del movimiento de inventario de devoluicion
 * @wvar $pagada  variable que en true indica que pasa validacion de no haber sido pagada,
 * 				  se realiza validacion cuando es empresa que genera factura, de resto se pone en true por defecto
 * @wvar $pass variable que cuarda la contraseña para hacer la devolucion
 * @wvar $permiso variable que en true indica que el usuario esta autorizado para hacer devoluciones
 * @wvar $permitido variable que en true indica que los articulos pasan validacion de no exceder las cantidades maximas,
 * 				    se realiza validacion cuando es particular, de resto se pone en true por defecto
 * @wvar $radio almacena la decision del usuario de hacer devolucion(con bono) o nota credito (con devolucion de dinero), cuando es particular
 * @wvar $realizar almacena la desicion del usuario de seguir con el proceso una vez seleccionados los articulos
 * @wvar $total, valor total de los articulos a devolver para la vneta
 * @wvar $totPun, total de puntos a devolver para esa venta
 * @wvar $validacion1, variable que en true indica que pasa validacion de tener motivo (no es obligatorio para devolucion comun con bono)
 * @wvar $validacion2 variable que en true indica que pasa validacion de password
 * @wvar $vde, vector con detalle de la venta por articulo
 * 				['cod'][$i] codigo del articulo
 * 				['nom'][$i] nombre del articulo
 * 				['iva'][$i] iva para el articulo
 * 				['valArt'][$i] valor total pagado en ese articulo
 * 				['can'][$i] cantidad ventida del articulo
 * 				['valUni'][$i] valor unitario del articulo
 * 				['pun'][$i] puntos causados por el articulo
 * 				['canDev'][$i] cnatidad real que se va a devolver del articulo
 * 				['contar'][$i] existe si el articulo va a ser devuelto
 * @wvar $ven, vector con los datos de la venta
 * 				['Vennum'] numero de la venta
 * 				['Ventcl'] tipo de cliente de la venta (01 particular, else empresas)
 * 				['Vencmo'] valor de la cuota moderadora
 * 				['Vennmo'] numero del movimiento de inventario de la vneta
 * 				['Vennid'] id del registro de la venta en la 16
 * 				['Venest'] estado de la venta
 * 				['Vennit'] nit del responsable de la venta
 * 				['Vencod'] codigo del responsable de la vneta
 * 				['Vencon'] concepto de inventario del movimiento de la venta
 * 				['Vencco'] centro de costos de la venta
 * 				['Venfec'] fecha en que se realizo la vnet
 * 				['Venvto'] valor bruto de la vneta
 * 				['Venviv'] valor del iva de la vneta
 * 				['Vencop'] valor del copago de la venta
 * 				['Vendes'] valor total del descuento
 * 				['Venrec'] valor de recargo
 * 				['Vencaj'] caja que realizo la venta
 * 				['Condes'] descripcion del concpeto del movimiento de inventario
 * 				['nomina'] si la venta utiliza nomina o no
 * 				['Ccodes'] nombre del centro de costos de la venta
 * @wvar $Vennum, numero de la venta
 * @wvar $wcco, codigo del centro de costos
 * @wvar $wnomcco, nombre del centro de costos
 */


//////////////////////////////////////////////////////////////ACTUALIZACIONES///////////////////////////////////////////////////
//
//2012-06-04 Se modificó la función realizarBono de forma que si el articulo del bono no mueve inventario se calcula el valor
//			 del bono y se graba e el campo Traval de la tabla 000055 - Mario Cadavid
//

//
//2016-03-16 Felipe Alvarez Sanchez : Se modifica el programa para que la devolucion de la empresa uvglobal se maneje deacuerdo al proceso nuevo que se lleva alla , pues
//           el movimiento de inventarios cambio para ellos .  Al hacer una venta se traslada el saldo a un inventario virtual teniendo que ser
//           cambiado este script , para que al momento de devolver se haga un traslado desde el virtual al inventario real y asi se haga la devolucion del
//           articulo
//
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


//////////////////////////////////////////////////////////////FUNCIONES//////////////////////////////////////////////////////////////////

/**************************************funciones de presentacion*/

/**
 * Despliega diferentes mensajes sobre la pantalla, con un boton para cerrar la ventana
 *
 * @param unknown_type $mensaje, mesaje que se quiere desplegar en pantalla
 */
function pintarAlerta($mensaje)
{
	echo "</br></br>";
	echo "<div align='center'><b>".$mensaje."</b></div>";
	//echo "</br></br>";
	//echo "<div align='center'><input type=button value='Volver' onclick='javascript:history.back();'></div>";
	echo "</br></br>";
	echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
}

/**
 * Despliega una aviso que se desplaza sobre la ventana
 *
 * @param unknown_type $mensaje mesaje que se quiere desplegar en pantalla
 */
function pintarAviso($mensaje)
{
	echo "<div align='center'><span style='width:470px' class='fondoVioleta'> &nbsp;&nbsp;&nbsp;&nbsp; <b>".$mensaje."</b> &nbsp;&nbsp;&nbsp;&nbsp; </span></div></br></br>";

}

/**
 * Abre la forma del programa enviando por hidden codigo y nombre de centro de costos
 *
 * @param unknown_type $wcco codigo del centro de costos
 * @param unknown_type $wnomcco nombre del centro de costos
 */
function pintarInicio($wcco,$wnomcco)
{
	echo "<form name='forma' action='' method='POST'>";
	echo "<input type='HIDDEN' name='wcco' value='".$wcco."'>";
	echo "<input type='HIDDEN' name='wnomcco' value='".$wnomcco."'>";
}

/**
 * Despliega tabla para el ingreso del numero de la venta para la cual se va a hacer devolucion
 *
 * @param unknown_type $wcf2, color del fondo de la tabla
 */
function pintarIngresoVenta($wcf2)
{
	echo "<table border=0 align=center width=400>";
	echo "<tr><td class='fila2' align=center height=29><b>Ingrese el número de la venta</b></td></tr>";
	echo "<tr><td class='fila2' align=center height=29><input type='text' name='Vennum' size='10'></td></tr>";
	echo "<tr><td class='fila2' align=center height=29><input type='submit' name='aceptar' value='Aceptar'></tr></table>";
}

function pintarEncabezado($wcf2, $wcf5, $wclfg, $row, $cli, $emp)
{
	$wcolspan=6;
	echo "<br><center><table border='0' width='90%'>";
	echo "<tr><td align=center colspan='$wcolspan' class=encabezadoTabla><b>VENTA # ".$row["Vennum"]."</b></td></tr>";
	echo "<tr><td colspan='$wcolspan' align='center' class=titulo><b>INFORMACIÓN DEL CLIENTE</td></tr>";

	echo "<tr class=fila2><td colspan='".($wcolspan/3)."'><b>Beneficiario:</b> ".$cli["Clinom"]."</td>";
	echo "<td colspan='".($wcolspan/3)."'><b>Documento:</b> ".$cli["Clidoc"]."</td>";
	echo "<td colspan='".($wcolspan/3)."'><b>Telefono:</b> ".$cli["Clite1"]."</tr>";


	echo "<tr class=fila2><td colspan='".($wcolspan/3)."'><b>Responsable:</b> ".$emp["Empnom"]."</td>";
	echo "<td colspan='".($wcolspan/3)."'><b>Código / Tarifa Empresa:</b> ".$row["Vencod"]." / ".$emp["Emptar"]." </td>";
	echo "<td colspan='".($wcolspan/3)."'><b>NIT:</b> ".$emp["Empnit"]."</td></tr>";

	echo "<tr><td colspan='$wcolspan' align='center' class=titulo><b>INFORMACIÓN VENTA</td></tr>";


	echo "<tr class=fila2><td colspan='".($wcolspan/3)."'><b>Concepto:</b> ".$row["Vencon"]."-".$row["Condes"]."</td>";
	echo "<td colspan='".($wcolspan/3)."'><b>CC:</b> ".$row["Vencco"]."-".$row["Ccodes"]."</td>";
	echo "<td colspan='".($wcolspan/3)."'><b>Fecha:</b> ".$row["Venfec"]."</td>";

	echo "<tr class=fila2><td colspan='".($wcolspan/3)."'><b>Valor Total:</b> $".number_format($row["Venvto"],2,'.',',')."</td>";
	echo "<td colspan='".($wcolspan/3)."'><b>Valor IVA:</b> $".number_format($row["Venviv"],2,'.',',')."</td>";
	echo "<td colspan='".($wcolspan/3)."'><b>Valor Copago:</b> $".number_format($row["Vencop"],2,'.',',')."</td></tr>";

	echo "<tr class=fila2><td colspan='".($wcolspan/3)."'><b>Cuota Moderadora:</b> $".number_format($row["Vencmo"],2,'.',',')."</td>";
	echo "<td colspan='".($wcolspan/3)."'><b>Valor Descuento: $</b>".number_format($row["Vendes"],2,'.',',')."</td>";
	echo "<td colspan='".($wcolspan/3)."'><b>Recargo: $</b> ".number_format($row["Venrec"],2,'.',',')."</td></tr></table><br>";

	echo "<input type='hidden' name='emp[0]' value='".$emp['Empcod']."'>";
	echo "<input type='hidden' name='emp[1]' value='".$emp['Empnit']."'>";
	echo "<input type='hidden' name='emp[2]' value='".$emp['Empnom']."'>";
	echo "<input type='hidden' name='emp[3]' value='".$emp['Emptem']."'>";
	echo "<input type='hidden' name='emp[4]' value='".$emp['Empfac']."'>";
	echo "<input type='hidden' name='emp[5]' value='".$emp['Emptar']."'>";
	echo "<input type='hidden' name='cli[0]' value='".$cli['Clidoc']."'>";
	echo "<input type='hidden' name='cli[1]' value='".$cli['Clinom']."'>";
	echo "<input type='hidden' name='cli[2]' value='".$cli['Clitip']."'>";
	echo "<input type='hidden' name='cli[3]' value='".$cli['Clipun']."'>";
	echo "<input type='hidden' name='cli[4]' value='".$cli['Clite1']."'>";

}

function pintarDetalle($wcf4, $wcf5, $wclfg, $wclfa, $wcf2, $wcf3, $vde, $ven, $total, $cambiable, $totPun)
{
	$wcolspan=7;
	echo "<table border='0' align='center'><tr ><td colspan='$wcolspan' align='center' class=titulo><b>DETALLE DE ARTICULOS<b></td></tr>";
	echo "<tr><td align=left class=encabezadoTabla><b>CÓDIGO</TD>";
	echo "<td align=left class=encabezadoTabla><b>NOMBRE</TD>";
	echo "<td align=left class=encabezadoTabla><b>CANT.</TD>";
	echo "<td align=left class=encabezadoTabla><b>VALOR UNIDAD</TD>";
	echo "<td align=left class=encabezadoTabla><b>% IVA</TD>";
	echo "<td align=left class=encabezadoTabla><b>VALOR TOTAL</TD>";
	echo "<td align=left class=encabezadoTabla><b>DEVOLVER</TD>";

	$num= count($vde['cod']);
	$totalaux =0;
	for($i=0;$i<$num;$i++)
	{
		if ($cambiable=='on')
		{
			echo "<tr><td align=left class=fila2>".$vde['cod'][$i]."</TD>";
			echo "<td align=left class=fila2>".$vde['nom'][$i]."</TD>";

			if($vde['can'][$i] >0 and $ven['Ventcl'] == '01')
			{
				if (isset ($vde['color'][$i]))
				{
					echo "<td align=center bgcolor='red' ><input type='text' name='vde[7][".$i."]' value='".$vde['canDev'][$i]."' size='1'></TD>";
				}
				else
				{
					echo "<td align=center class=fila2><input type='text' name='vde[7][".$i."]' value='".$vde['canDev'][$i]."' size='1'></TD>";
				}
			}else
			{
				echo "<td align=center class=fila2>".$vde['can'][$i]."</td>";
				echo "<input type='hidden' name='vde[7][".$i."]' value='".$vde['canDev'][$i]."'>";
			}

			echo "<td align=right class=fila2>".number_format($vde['valUni'][$i],0,'',',')."</TD>";
			echo "<td align=right class=fila2>".$vde['iva'][$i]."</TD>";
			echo "<td align=right class=fila2>".number_format(($vde['canDev'][$i]*$vde['valUni'][$i]),2,'.',',')."</TD>";


			if(isset($vde['contar'][$i]))
			{
				if($vde['contar'][$i]=='off')
				{
					echo "<td align=center class=fila2><b>SELECCIONADO</TD>";
					echo "<input type='hidden' name='vde[8][".$i."]' value='off'>";
					$totalaux = $totalaux + ($vde['canDev'][$i]*$vde['valUni'][$i]);
				}
				else
				{
					echo "<td align=center class=fila2><b><input type='checkbox' name='vde[8][".$i."]' value='on' checked></TD>";
				}

			}else if($vde['can'][$i] > 0)
			{
				echo "<td align=center class=fila2><b><input type='checkbox' name='vde[8][".$i."]' value='on'></TD>";
			}
			else
			{
				echo "<td align=center class=fila2><b><b>x<b></TD>";
			}
		}
		else
		{
			if(isset($vde['contar'][$i]))
			{
				echo "<tr><td align=left class=fila2>".$vde['cod'][$i]."</TD>";
				echo "<td align=left class=fila2>".$vde['nom'][$i]."</TD>";
				echo "<td align=right class=fila2>".$vde['canDev'][$i]."</td>";
				echo "<input type='hidden' name='vde[7][".$i."]' value='".$vde['canDev'][$i]."'>";
				echo "<td align=right class=fila2>".number_format($vde['valUni'][$i],0,'',',')."</TD>";
				echo "<td align=right class=fila2>".$vde['iva'][$i]."</TD>";
				echo "<td align='right' class=fila2>".number_format(($vde['canDev'][$i]*$vde['valUni'][$i]),2,'.',',')."</TD>";
				echo "<td align='right' class=fila2>OK.</TD>";
				echo "<input type='hidden' name='vde[8][".$i."]' value='".$vde['contar'][$i]."'>";
				$totalaux = $totalaux + ($vde['canDev'][$i]*$vde['valUni'][$i]);
			}
			else
			{
				echo "<input type='hidden' name='vde[7][".$i."]' value='".$vde['canDev'][$i]."'>";
			}
		}

		echo "<input type='hidden' name='vde[0][".$i."]' value='".$vde['cod'][$i]."'>";
		echo "<input type='hidden' name='vde[1][".$i."]' value='".$vde['nom'][$i]."'>";
		echo "<input type='hidden' name='vde[2][".$i."]' value='".$vde['iva'][$i]."'>";
		echo "<input type='hidden' name='vde[3][".$i."]' value='".$vde['valArt'][$i]."'>";
		echo "<input type='hidden' name='vde[4][".$i."]' value='".$vde['can'][$i]."'>";
		echo "<input type='hidden' name='vde[5][".$i."]' value='".$vde['valUni'][$i]."'>";
		echo "<input type='hidden' name='vde[6][".$i."]' value='".$vde['pun'][$i]."'>";
	}

	//envio de los datos de la venta
	echo "<input type=\"hidden\" name='Vennum' value=\"".$ven['Vennum']."\">";
	echo "<input type=\"hidden\" name=\"ven[0]\" value=\"".$ven['Vennum']."\">";
	echo "<input type=\"hidden\" name=\"ven[1]\" value=\"".$ven['Ventcl']."\">";
	echo "<input type=\"hidden\" name=\"ven[2]\" value=\"".$ven['Vencmo']."\">";
	echo "<input type=\"hidden\" name=\"ven[3]\" value=\"".$ven['Vennmo']."\">";
	echo "<input type=\"hidden\" name=\"ven[4]\" value=\"".$ven['Vennid']."\">";
	echo "<input type=\"hidden\" name=\"ven[5]\" value=\"".$ven['Venest']."\">";
	echo "<input type=\"hidden\" name=\"ven[6]\" value=\"".$ven['Vennit']."\">";
	echo "<input type=\"hidden\" name=\"ven[7]\" value=\"".$ven['Vencod']."\">";
	echo "<input type=\"hidden\" name=\"ven[8]\" value=\"".$ven['Vencon']."\">";
	echo "<input type=\"hidden\" name=\"ven[9]\" value=\"".$ven['Vencco']."\">";
	echo "<input type=\"hidden\" name=\"ven[10]\" value=\"".$ven['Venfec']."\">";
	echo "<input type=\"hidden\" name=\"ven[11]\" value=\"".$ven['Venvto']."\">";
	echo "<input type=\"hidden\" name=\"ven[12]\" value=\"".$ven['Venviv']."\">";
	echo "<input type=\"hidden\" name=\"ven[13]\" value=\"".$ven['Vencop']."\">";
	echo "<input type=\"hidden\" name=\"ven[14]\" value=\"".$ven['Vendes']."\">";
	echo "<input type=\"hidden\" name=\"ven[15]\" value=\"".$ven['Venrec']."\">";
	echo "<input type=\"hidden\" name=\"ven[16]\" value=\"".$ven['Vencaj']."\">";
	echo "<input type=\"hidden\" name=\"ven[17]\" value=\"".$ven['Condes']."\">";
	echo "<input type=\"hidden\" name=\"ven[18]\" value=\"".$ven['nomina']."\">";
	echo "<input type=\"hidden\" name=\"ven[19]\" value=\"".$ven['Ccodes']."\">";

	echo "<input type='hidden' name='total' value='".$total."'>";
	echo "<input type='hidden' name='totPun' value='".$totPun."'>";

	if($total == 0)
	{
		 if ($totalaux !=0)
		 {
			echo "</tr><tr><td colspan=5 class=encabezadoTabla><b>TOTAL</b></td><td  class=encabezadoTabla align='right'><b>$ ".number_format($totalaux,2,'.',',')."</b></td ><td class=encabezadoTabla>&nbsp;</td></tr>";

		 }
		 else
		 {
			echo "</tr><tr><td colspan=5 class=encabezadoTabla><b>TOTAL</b></td><td  class=encabezadoTabla align='right'><b>$ ".number_format($total,2,'.',',')."</b></td ><td class=encabezadoTabla>&nbsp;</td></tr>";

		 }
	}
	else
	{
		echo "</tr><tr><td colspan=5 class=encabezadoTabla><b>TOTAL</b></td><td  class=encabezadoTabla align='right'><b>$ ".number_format($total,2,'.',',')."</b></td ><td class=encabezadoTabla>&nbsp;</td></tr>";
	}
	echo "</table>";
}

function pintarFinalForma($pag, $wclfg)
{
	echo "<br><table border='0' align='center'><tr>";

	switch ($pag)
	{
		case 3:
		echo "<input type='hidden' name='bandera' value='3'>";
		echo "<td class=fila2 align='center'><input type='checkbox' name='realizar'> <b> Realizar transacción</b></td></tr>";
		echo "<tr><td align='center'><input type='submit' name='aceptar' value='ACEPTAR'></td></tr></table>";
		break;

		case 4:
		echo "<input type='hidden' name='bandera' value='5'>";
		echo "<td class=fila2><input type='radio' name='radio' value='NC'> NOTA CRÉDITO</td>";
		echo "<td class=fila2><input type='radio' name='radio' value='DV' checked> DEVOLUCIÓN </td></tr>";
		echo "<tr><td colspan=2>&nbsp;</td></tr>";
		echo "<tr class=textoNormal><td colspan='2'>INGRESE SU PASSWORD: ";
		echo "<input type='password' name='pass' size='5'></td></tr>";
		echo "<tr><td colspan=2>&nbsp;</td></tr>";
		echo "<tr><td colspan='2' class=textoNormal>INGRESE EL MOTIVO DE LA TRANSACCIÓN: <br>(es obligatorio excepto cuando sea una simple devolución por parte del cliente)<br> ";
		echo '<textarea ALIGN="CENTER"  ROWS="3" name="motivo" cols="50" style="font-family: Arial; font-size:14"></textarea></font>';
		echo "</td></tr>";
		echo "<tr><td colspan=2>&nbsp;</td></tr>";
		echo "<tr><td colspan=2 align=center><input type='button' name='volver' value='VOLVER' onclick='enter(3)'></td></tr>";
		echo "<tr><td colspan=2 align=center><br /><input type='submit' name='aceptar' value='ACEPTAR'></td></tr></table>";
		break;

		case 5:
		echo "<input type='hidden' name='bandera' value='5'>";
		echo "<td colspan='2' class=textoNormal>INGRESE SU PASSWORD: ";
		echo "<input type='password' name='pass' size='5'></td></tr>";
		echo "<tr><td colspan=2>&nbsp;</td></tr>";
		echo "<tr><td colspan='2' class=textoNormal>INGRESE EL MOTIVO DE LA TRANSACCIÓN: <br>(es obligatorio excepto cuando sea una simple devolución por parte del cliente)<br> ";
		echo '<textarea ALIGN="CENTER"  ROWS="3" name="motivo" cols="50" style="font-family: Arial; font-size:14"></textarea></font>';
		echo "</td></tr>";
		echo "<tr><td colspan=2>&nbsp;</td></tr>";
		echo "<tr><td colspan=2 align=center><input type='button' name='volver' value='VOLVER' onclick='enter(3)'></td></tr>";
		echo "<tr><td colspan=2 align=center><br /><input type='submit' name='aceptar' value='ACEPTAR'></td></tr></table>";
		break;

		case 6:
		echo "<tr><td align=center><input type='button' name='aceptar' value='CERRAR' onclick='javascript:window.close()'></td></tr></table>";
		break;
	}

	echo "</form >";
}

function pintarImpresion($wcco, $numdev )
{
	global $conex;
	global $tipo_tablas;
	$tipo_tablas=strtolower($tipo_tablas);

	if ($tipo_tablas =='uvglobal')
	{
		echo "<br><br><br><table border='0' align='center'><tr>";
		//echo"<td><a href='rep_Devol.php?wcco=$wcco&amp;doc=".$numdev."&amp;tipo_tablas=$tipo_tablas' target='_blank'>IMPRIMIR COMPROBANTE</a></td></tr></table>";
		echo"<td class=textoNormal ><font style='font-size:15pt' >¡VENTA ANULADA SATISFACTORIAMENTE!</font></td></tr></table>";
	}
	else
	{
		echo "<br><br><br><table border='0' align='center'><tr>";
		//echo"<td><a href='rep_Devol.php?wcco=$wcco&amp;doc=".$numdev."&amp;tipo_tablas=$tipo_tablas' target='_blank'>IMPRIMIR COMPROBANTE</a></td></tr></table>";
		echo"<td class=textoNormal><a href='bono_dev.php?tipo_tablas=$tipo_tablas' target='_blank'>IMPRIMIR COMPROBANTE</a></td></tr></table>";
	}
}
/************************************funciones de persistencia*/

function permisoUsuario($wusuario, &$wcco, &$wnomcco)
{
	global $conex;
	global $tipo_tablas;
	$tipo_tablas=strtolower($tipo_tablas);

	$q =  " SELECT Cjecco,Cjecaj "
	."   FROM ".$tipo_tablas."_000030 "
	."  WHERE cjeusu = '".$wusuario."'"
	."    AND cjeest = 'on' ";
	$err=mysql_query($q,$conex);
	$num=mysql_num_rows($err) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );
	if($num>0)
	{
		$row=mysql_fetch_array($err);
		$ini=explode("-",$row[0]);
		$wcco=$ini[0]; //numero del centro de costos
		$wnomcco=$ini[1]; //nombre del centro de costos
		return true;
	}
	else
	{
		return false;
	}
}

function buscarVenta($Vennum, $wcco, &$ven)
{
	global $conex;
	global $tipo_tablas;
	$tipo_tablas=strtolower($tipo_tablas);

	//busco la venta ingresada
	$q= "SELECT Vennum,Ventcl, Vencmo, Vennmo, ".$tipo_tablas."_000016.id as Vennid, Venest,Vennit, Vencod, Vencon, Vencco, Venfec, Venvto, Venviv,Vencop, Vendes,Venrec,Vencaj,Condes,Ccodes "
	."FROM ".$tipo_tablas."_000016, ".$tipo_tablas."_000008, ".$tipo_tablas."_000003  "
	."WHERE Vencco 	= '".$wcco."' "
	."and 	Vennum 	= '".$Vennum."' "
	."and	Concod	= Vencon "
    ."and   ccocod = concod";

	//On
	echo $q."<br>";



	$err=mysql_query($q,$conex);
	$num=mysql_num_rows($err);
	if($num>0)	//SI LA VENTA EXISTE
	{
		$ven=mysql_fetch_array($err);
		return true;
	}
	else
	{
		return false;
	}
}

function buscarCliente($Vennit, &$cli)
{
	global $conex;
	global $tipo_tablas;
	$tipo_tablas=strtolower($tipo_tablas);

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

function buscarEmpresa($Vencod, $Ventcl, &$emp)
{
	global $conex;
	global $tipo_tablas;
	$tipo_tablas=strtolower($tipo_tablas);

	$q= "select Empcod, Empnit, Empnom, Emptem, Empfac, Emptar from ".$tipo_tablas."_000024 "
	."where Empcod = ".$Vencod." "
	."AND	Substr(Emptem,1,2) = '".$Ventcl."' ";
	//."AND	Empest = 'on'";

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

function validarNomina ($Ventcl, $Vennum, &$conNomina)
{
	global $conex;
	global $tipo_tablas;
	$tipo_tablas=strtolower($tipo_tablas);

	$q="SELECT * "
	."FROM ".$tipo_tablas."_000029 "
	."WHERE Temcod='".$Ventcl."' ";
	$err=mysql_query($q,$conex);
	$num=mysql_num_rows($err);
	if($num>0)
	{
		$row=mysql_fetch_array($err);
		if($row['Temche'] == 'on')
		{
			$conNomina=true;
			/*Verificar que no se halla pagado ninguna de las cuotas de la venta*/
			$q="SELECT Pnocuo, Pnocup "
			."FROM ".$tipo_tablas."_000046 "
			."WHERE Pnovta='".$Vennum."' "
			."AND	Pnoest = 'on'";
			$err1=mysql_query($q,$conex);
			$num1=mysql_num_rows($err1);
			if($num1>0)
			{
				$row1=mysql_fetch_array($err1);
				if($row1['Pnocuo'] != $row1['Pnocup'])
				{
					return false;
				}
			}
		}
		else
		{
			$conNomina=false;
		}
	}
	return true;
}

function validarAblocada ($Vennum, $wcco)
{
	global $conex;
	global $tipo_tablas;
	$tipo_tablas=strtolower($tipo_tablas);

	$q="SELECT Fdefac "
	."FROM ".$tipo_tablas."_000019, ".$tipo_tablas."_000018 "
	."WHERE Fdenve = '".$Vennum."' "
	."AND	Fdeest = 'on' and fenfac=fdefac and fenffa=fdeffa and fenest='on' ";

	//deberia tener  and fencco='".$wcco."'

	$err=mysql_query($q,$conex);
	$num=mysql_num_rows($err);
	if($num>0)
	{
		return false;
	}

	return true;
}

function validarPagada ($Vennum, $wcco)
{
	global $conex;
	global $tipo_tablas;
	$tipo_tablas=strtolower($tipo_tablas);

	$q="SELECT fensal "
	."FROM ".$tipo_tablas."_000019, ".$tipo_tablas."_000018 "
	."WHERE fdenve='".$Vennum."' and fdeest='on'  "
	."AND	fenffa = fdeffa and fenfac=fdefac and fenabo=0 and fenrbo=0 and fenvnc=0 ";

	//and fencco='".$wcco."'
	$err=mysql_query($q,$conex);
	$num=mysql_num_rows($err);
	if($num>0)
	{
		return true;
	}
	return false;
}

function validarDevoluciones ($Vennum, $movdev)
{
	global $conex;
	global $tipo_tablas;
	$tipo_tablas=strtolower($tipo_tablas);

	$q="SELECT Mendoc "
	."FROM ".$tipo_tablas."_000010 "
	."WHERE	Menfac = '$Vennum' "
	."AND	Mencon = '".$movdev."'";
	$err=mysql_query($q,$conex);
	$num=mysql_num_rows($err);
	if($num>0)
	{
		return false;
	}
	return true;
}


function buscarArticulos ($Vennum, &$vde, $val, &$totPun)
{
	global $conex;
	global $tipo_tablas;
	$tipo_tablas=strtolower($tipo_tablas);

	$q="SELECT Vdeart, Vdevun, SUM(Vdecan) as Vdecan, Vdepiv, Vdedes,Vdepun, Artnom "
	."FROM ".$tipo_tablas."_000017, ".$tipo_tablas."_000001  "
	."WHERE Vdenum 	= '".$Vennum."' "
	."and	Vdeest	= 'on' "
	."and	Artcod	= Vdeart "
	."GROUP BY Vdeart "
	."order by Vdeart, Vdecan ";
	$err1=mysql_query($q,$conex);
	$numart=mysql_num_rows($err1);

	if($numart>0)
	{
		$totPun=0;
		for($i=0;$i<$numart;$i++)
		{
			$row1=mysql_fetch_array($err1);
			$vde['cod'][$i]=$row1["Vdeart"];		//Codigo del Articulo
			$vde['nom'][$i]=$row1["Artnom"];		//Nombre del Articulo
			$vde['canDev'][$i]=$row1["Vdecan"];	   //Cantidad donde se albergara el valor digitado por el usuario
			$vde['can'][$i]=$row1["Vdecan"];	   //cantidad real del articulo
			$descuento=round(((($row1["Vdepiv"]/100+1)*$row1["Vdedes"])/$row1["Vdecan"]),0);
			$vde['valUni'][$i]=$row1["Vdevun"]-$descuento;		//Valor unitario
			$vde['iva'][$i]=$row1["Vdepiv"];		//% de IVA
			$vde['valArt'][$i]=$row1["Vdecan"]*$row1["Vdevun"];	//Valor total para el articulo
			$vde['pun'][$i]=$row1["Vdepun"]/$row1["Vdecan"];		//Puntos
			if($val=='off')
			{
				$vde['contar'][$i]=$val; //check box si estara en on () o en off
			}

			$totPun=$totPun+($vde['can'][$i]*$vde['pun'][$i]);
		}
	}

	return $numart;
}


function validarDisponibilidad ($Vennum, $numart, &$vde, $movdev)
{
	global $conex;
	global $tipo_tablas;
	$tipo_tablas=strtolower($tipo_tablas);

	$contador=$numart;
	for($i=0;$i<$numart;$i++)
	{
		/*Busco si Existe una devolución previa para esa venta y ese articulo*/
		$q="SELECT Mdecan "
		."FROM ".$tipo_tablas."_000011,".$tipo_tablas."_000010 "
		."WHERE	Menfac = '$Vennum' "
		."AND	Mencon = '".$movdev."' "
		."AND	Mdedoc = Mendoc "
		."AND	Mdecon = Mencon "
		."AND	Mdeart = '".$vde['cod'][$i]."' ";
		$err2=mysql_query($q,$conex);
		$num2=mysql_num_rows($err2);


		if($num2 > 0)
		{
			for($j=0;$j<$num2;$j++)
			{
				$row2=mysql_fetch_row($err2);
				$vde['can'][$i]=$vde['can'][$i]-$row2[0];		//Cantidad
				$vde['canDev'][$i]=$vde['can'][$i];
			}

			if ($vde['can'][$i]==0)
			{
				$contador=$contador-1;
			}
		}
	}

	if ($contador<=0)
	{
		return false;
	}
	return true;
}

function confirmarPassword($pass, $wusuario)
{
	global $conex;
	global $tipo_tablas;
	$tipo_tablas=strtolower($tipo_tablas);

	$q="SELECT Cjeusu "
	."FROM ".$tipo_tablas."_000030 "
	."WHERE Cjecla='$pass' ";
	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);
	if ($num > 0)
	{
		$row=mysql_fetch_row($err);
		$usu=$row[0];
		if ($wusuario==$usu)
		{
			return true;
		}
	}
	return false;
}

function consultarConcepto()
{
	global $conex;
	global $tipo_tablas;
	$tipo_tablas=strtolower($tipo_tablas);

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

function realizarNota($wcco, $wnomcco, $ven, $wusuario, $motivo, $vde, $emp, $total, $numdev)
{
	global $conex;
	global $tipo_tablas;
	$tipo_tablas=strtolower($tipo_tablas);
	global $wbasedato_mov;
	global $wbasedato;
	global $consulta_unix;

	//INCREMENTO Y CONSULTA DE LA FUENTE Y EL CONSECUTIVO DE NOTAS CREDITO

	$q = "lock table ".$tipo_tablas."_000003 LOW_PRIORITY WRITE";
	$err = mysql_query($q,$conex);

	$q="UPDATE ".$tipo_tablas."_000003 "
	."SET Cconci = Cconci +1 "
	."WHERE Ccocod= '$wcco' "
	."and Ccoest = 'on'  and cconci < cconcf ";

	$err = mysql_query($q,$conex) or die (mysql_errno()." - NO SE HA PODIDO INCREMENTAR EL CONSECUTIVO PARA NOTAS CREDITO, PUEDE SER QUE SE HA LLEGADO AL TOPE MAXIMO DE INCREMENTO ".mysql_error());

	//Busco la fuente para la nota segun el centro de costos

	$q="SELECT Cconci, Ccofnc, Ccofrc, Cconcf "
	."FROM ".$tipo_tablas."_000003 "
	."WHERE	Ccocod = '$wcco' "
	."and	Ccoest = 'on'";
	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR LA FUENTE PARA LA NOTA CREDITO ".mysql_error());

	$num=mysql_num_rows($err)or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR LA FUENTE PARA LA NOTA CREDITO ".mysql_error());

	if($num>0)
	{
		$row=mysql_fetch_row($err);
		$con=$row[0]; //numero de la transaccion
		$fue=$row[1]; //fuente de la transaccion
	}

	$q = " UNLOCK TABLES";
	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO DESBLOQUEAR LA TABLA 000003 ".mysql_error());


	//CONSULTO LA FACTURA AFECTADA DE ESA VENTA EN LA TABLA 19
	$q="SELECT vennfa, venffa from ".$tipo_tablas."_000016 "
	."WHERE	vennum = '".$ven['Vennum']."' and vencco='".$wcco."' "
	."and	venest = 'on' ";
	$err=mysql_query($q,$conex)or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR LA FACTURA DE LA VENTA PARA REALIZAR LA NOTA CREDITO ".mysql_error());
	$num=mysql_num_rows($err)or die (mysql_errno()." -NO SE HA ENCONTRADO LA FACTURA DE LA VENTA PARA REALIZAR LA NOTA CREDITO ".mysql_error());
	if($num>0)
	{
		$row=mysql_fetch_row($err);
		$numfac=$row[0];
		$fuefac=$row[1];
	}

	//GRABO EL DETALLE DE LA NOTA CREDITO
	if ($ven['Ventcl']=='01')
	{
		$q= " INSERT INTO ".$tipo_tablas."_000020 (   Medico       ,   Fecha_data,                Hora_data,              renfue,   rennum,                     renvca              ,     rencod      ,   rennom      ,   rencaj    ,              renusu      ,   rencco    , renest ,     renfec,                renobs,        Seguridad       ) "
		."                               VALUES ('".$tipo_tablas."', '".date('Y-m-d')."','".(string)date("H:i:s")."' ,'".$fue."', ".$con.",     ".number_format(($total),0,'.','').",'".$emp['Empcod']."','".$emp['Empnom']."','".$ven['Vencaj']."','".$wusuario."',   '".$wcco."',     'on', '".date('Y-m-d')."'   , '".$motivo."' ,  'C-".$wusuario."')";
		$valncfac = number_format(($total*-1),0,'.','');
	}
	else
	{
		$q= " INSERT INTO ".$tipo_tablas."_000020 (   Medico       ,   Fecha_data,                Hora_data,              renfue,   rennum,                     renvca              ,     rencod      ,   rennom      ,   rencaj    ,              renusu      ,   rencco    , renest ,     renfec,                renobs,        Seguridad       ) "
		."                               VALUES ('".$tipo_tablas."', '".date('Y-m-d')."','".(string)date("H:i:s")."' ,'".$fue."', ".$con.",     ".number_format(($ven['Venvto']),0,'.','').",'".$emp['Empcod']."','".$emp['Empnom']."','".$ven['Vencaj']."','".$wusuario."',   '".$wcco."',     'on', '".date('Y-m-d')."'   , '".$motivo."' ,  'C-".$wusuario."')";
		$totalven = $ven['Venvto'];
		$valncfac = number_format(($totalven*-1),0,'.','');
	}
	//echo $q."<br>";

	$resins = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO GRABAR EL ENCABEZADO DE LA NOTA CREDITO ".mysql_error());

	//GRABO EL DETALLE DE LA NOTA CREDITO
	$q= " INSERT INTO ".$tipo_tablas."_000021 (   Medico       , Fecha_data,               Hora_data,                rdefue,  rdenum ,    rdecco       ,   rdefac    ,    rdevta ,          rdevca , rdeest, rdecon, rdevco, rdeffa,         rdesfa, rdehis, rdeing,    rdeccc,  rdemiv,   Seguridad        ) "
	."                            	VALUES ('".$tipo_tablas."','".date('Y-m-d')."','".(string)date("H:i:s")."' ,'".$fue."', '".$con."', '".$wcco."','".$numfac."', '".$ven['Vennum']."' , '',   'on' ,     '',       0,  '".$fuefac."',     0,     '' ,   '',          '',  '".$numdev."', 'C-".$wusuario."')";

	$resins = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO GRABAR EL ENCABEZADO DE LA NOTA CREDITO".mysql_error());


	//GRABO LOS CONCEPTOS DE LA NOTA EN LA TABLA 65, una linea por articulo

	for ($i=0;$i<count($vde[0]);$i++)
	{
		if (isset ($vde['contar'][$i]))
		{
			$val=$vde['canDev'][$i]*$vde['valUni'][$i];

			//consulto el grupo del articulo

			$q=" SELECT mid(artgru,1,instr(artgru,'-')-1) from ".$tipo_tablas."_000001 "
			  ."  WHERE	artcod = '".$vde['cod'][$i]."' "
			  ."    and	artest = 'on' ";
            $err=mysql_query($q,$conex)or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR EL GRUPO PARA UN ARTICULO DEVUELTO ".mysql_error());
			$num=mysql_num_rows($err)or die (mysql_errno()." -NO SE HA ENCONTRADO EL GRUPO PARA UN ARTICULO DEVUELTO ".mysql_error());
			if($num>0)
			{
				$row=mysql_fetch_row($err);
				$grupo=$row[0];
			}

			$q= " SELECT id from ".$tipo_tablas."_000065 "
			   ."  WHERE Fdecon = '".$grupo."' "
			   ."    and Fdedoc = '".$con."' "
			   ."    and Fdefue = '".$fue."' "
			   ."    and Fdeest = 'on' ";
			$err=mysql_query($q,$conex);
			$num=mysql_num_rows($err);
			if($num>0)
			{
				$row=mysql_fetch_row($err);

				$q= "   UPDATE ".$tipo_tablas."_000065 "
				."      SET fdevco = fdevco+'".$val."' "
				."    where id='".$row[0]."' ";
			}
			else
			{
				$wconfac=$grupo;

				$q= " INSERT INTO ".$tipo_tablas."_000065 (   Medico         ,    Fecha_data      ,    Hora_data                ,   fdefue ,    fdedoc , fdeest,    fdecco  ,    fdecon      ,    fdevco  , fdeter, fdepte, fdesal, Seguridad        ) "
				."                                 VALUES ('".$tipo_tablas."', '".date('Y-m-d')."', '".(string)date("H:i:s")."' ,'".$fue."', '".$con."', 'on'  , '".$wcco."', '".$wconfac."' , '".$val."' , ''    , 0     , ''    , 'C-".$wusuario."') ";
			}
			$resins = mysql_query($q,$conex) or die (mysql_errno()." -NO SE PUDO GRABAR CORRECTAMENTE UNO DE LOS CONCEPTOS DE LA NOTA CREDITO ".mysql_error());
		}
	}


	//afecto la tabla 18 con la nota credito
	if($ven['Ventcl']=='01')
	{
		$q= "  UPDATE ".$tipo_tablas."_000018 "
		."     SET fenvnc = fenvnc + ".$total
		."        ,fendev = fendev + ".$total
		."   WHERE fenfac = '".$numfac."' and fenffa='".$fuefac."'  ";
	}
	else
	{
		$q= "  UPDATE ".$tipo_tablas."_000018 "
		."     SET fenvnc = fenvnc + ".$ven['Venvto']
		."        ,fendev = fendev + ".$ven['Vencmo']
		."        ,fensal = fensal - ".$ven['Venvto']
		."   WHERE fenfac = '".$numfac."' and fenffa='".$fuefac."'  ";
	}

	$resupd = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO AFECTAR LA FACTURA CON EL VALOR DE LA NOTA CREDITO ".mysql_error());

	if(isset($consulta_unix) && $consulta_unix=="1")
	{

		//////////////////////////////////////////////
		// GRABACION DE NOTA COMO NOVEDAD EN UNIX 	//
		//////////////////////////////////////////////
		conexionOdbc($conex, $wbasedato_mov, $conexUnix, 'facturacion');

		// Consulto las notas crédito de facturación
		$q =   "   SELECT Carfue "
			  ."     FROM ".$wbasedato."_000040 "
			  ."  	WHERE Carncr = 'on' "
			  ."	  AND Carcfa = 'on' "
			  ."	  AND Carest = 'on' ";
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

		$continua = "0";
		while($row = mysql_fetch_array($res))
		{
			if(trim($row['Carfue'])==trim($fue))
				$continua = "1";
		}
		//echo $continua."<br>";
		if($continua=="1")
		{
			// Obtengo historia y número de accidente
			$q =   "   SELECT Orihis, Fennac "
				  ."     FROM ".$wbasedato."_000018, root_000037 "
				  ."  	WHERE Fenffa = '".$fuefac."' "
				  ."	  AND Fenfac = '".$numfac."' "
				  ."	  AND Fenest = 'on' "
				  ."	  AND Fendpa = Oriced ";
			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$row = mysql_fetch_array($res);
			$wpachis = $row['Orihis'];
			$wacc = $row['Fennac'];

			if($wpachis && $wacc && $wpachis!="" && $wacc!="")
			{

				// Consulto si ya hay registro de novedades para definir la secuencia de novedad
				$q = "   SELECT MAX(novaccsec*1)
						   FROM fanovacc
						  WHERE novacchis = '".$wpachis."'
							AND novaccacc = '".$wacc."'
						  GROUP BY novacchis, novaccacc ";
				//$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				//$accsec = mysql_num_rows($res);
				$err_o = odbc_exec($conexUnix,$q);
				odbc_fetch_row($err_o);
				$accsec = odbc_result($err_o,1);
				$accsec = $accsec+1;

				// Consulto el número del responsable
				$q = "   SELECT salaccnre
						   FROM fasalacc, inemp
						  WHERE salacchis = '".$wpachis."'
							AND salaccacc = '".$wacc."'
							AND salacccer = empcod
							AND empnit = '".$emp['Empnit']."' ";
				$err_nre = odbc_do($conexUnix,$q);
				odbc_fetch_row($err_nre);
				$accnre = odbc_result($err_nre,1);

				// Obtengo NIT y Nombre de la empresa
				$q = " SELECT cfgnit, cfgnom, cfgcco "
					."   FROM ".$wbasedato."_000049 "
					."  WHERE cfgcco = '".$wcco."'";
				$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$row = mysql_fetch_array($res);

				if($row)
				{
					$acccin = $row[0];
					$accnin = $row[1];
				}
				else
				{
					$acccin = "";
					$accnin = "";
				}

				// Grabo datos en la tabla de novedades
				$q = "   INSERT
						   INTO fanovacc
								( novacchis, novaccacc, novaccsec, novacctip, novaccfac, novaccfec, novacccin, novaccnin, novaccnre, novaccval, novaccudu, novaccfad )
						 VALUES
								( '".$wpachis."','".$wacc."' ,'".$accsec."' ,'M','".$numfac."','".date('Y-m-d')."','".$acccin."','".$accnin."','".$accnre."',".$valncfac.",'".$wusuario."','".date('Y-m-d H:i:s')."') ";
				$err_o = odbc_do($conexUnix,$q);
				//$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

				if($wpachis && $wpachis!="" && $wpachis!=" ")
				{
					// Actualizo datos en tabla de saldo del accidente
					$q = "   UPDATE fasalacc
								SET salaccrem = salaccrem+$valncfac,
									salaccsal = salaccsal+$valncfac
							  WHERE salacchis = '".$wpachis."'
								AND salaccacc = '".$wacc."'
								AND salaccnre = '".$accnre."' ";
					//echo $q."<br>";
					//$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$err_o = odbc_do($conexUnix,$q);
				}
			}
		}
	}
}

function realizarAnulacion($wcco, $ven, $wusuario, $motivo, $numdev)
{
	global $conex;
	global $tipo_tablas;
	$tipo_tablas=strtolower($tipo_tablas);

	//INCREMENTO Y CONSULTA DE LA FUENTE Y EL CONSECUTIVO DE NOTAS CREDITO

	$q = "lock table ".$tipo_tablas."_000040 LOW_PRIORITY WRITE";
	$err = mysql_query($q,$conex);

	$q="UPDATE ".$tipo_tablas."_000040 "
	."SET carcon = carcon +1 "
	."WHERE caravt= 'on' "
	."and carest = 'on'";

	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO INCREMENTAR EL CONSECUTIVO PARA ANULACION ".mysql_error());

	//Busco la fuente para la ANULACION

	$q="SELECT Carfue, Carcon, Cardes "
	."FROM ".$tipo_tablas."_000040 "
	."WHERE	caravt= 'on' "
	."and	carest = 'on'";
	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR LA FUENTE PARA LA NOTA ANULACION ".mysql_error());

	$num=mysql_num_rows($err)or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR LA FUENTE PARA LA ANULACION ".mysql_error());

	if($num>0)
	{
		$row=mysql_fetch_row($err);
		$con=$row[1]; //numero de la transaccion
		$fue=$row[0]; //fuente de la transaccion
		$des=$row[2];//nombre de la transaccion
	}

	$q = " UNLOCK TABLES";
	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO DESBLOQUEAR LA TABLA 000040 ".mysql_error());


	//GRABO LA ANULACION EN LA TABLA 55
	$q="INSERT INTO ".$tipo_tablas."_000055 (            medico,          fecha_data,                   Hora_data,              Trafec,                      Trahor,  Tracco,                  Traven,              Tratip,     Tradev, Trafue, Tranum, Tradis,    Tramot, Traest, Seguridad)  "
	." VALUES ('".$tipo_tablas."', '".date('Y-m-d')."', '".(string)date("H:i:s")."', '".date('Y-m-d')."', '".(string)date("H:i:s")."', '".$wcco."','".$ven['Vennum']."', '".$fue."-".$des."',  '$numdev', '$fue', '$con', 'off','$motivo', 'on', 'C-".$wusuario."')";

	$resins = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO GRABAR lA ANULACION ".mysql_error());


	//REALIZO LA ANULACION DE LA VENTA EN TABLAS 16 Y 17

	$q= "  UPDATE ".$tipo_tablas."_000016 "
	."     SET venest = 'off' "
	."   WHERE vennum = '".$ven['Vennum']."' and vencco='".$wcco."'  ";


	$resupd = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ANULAR EL ENCABEZADO DE LA VENTA ".mysql_error());

	$q= "  UPDATE ".$tipo_tablas."_000017 "
	."     SET vdeest = 'off' "
	."   WHERE vdenum = '".$ven['Vennum']."'";

	$resupd = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ANULAR EL DETALLE DE LA VENTA ".mysql_error());
}

function devolverDinero($movdev, $total, $ven, $wcco, $wnomcco, $wusuario, $motivo, $emp, $numdev)
{
	global $conex;
	global $tipo_tablas;

	//INCREMENTO Y CONSULTA DE LA FUENTE Y EL CONSECUTIVO DE DEVOLUCIONES DE DINERO

	$q = "lock table ".$tipo_tablas."_000040 LOW_PRIORITY WRITE";
	$err = mysql_query($q,$conex);

	$q="UPDATE ".$tipo_tablas."_000040 "
	."SET Carcon = Carcon +1 "
	."WHERE Cardvt= 'on' "
	."and Carest = 'on' ";

	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO INCREMENTAR EL CONSECUTIVO PARA DEVOLUCIONES DE DINERO".mysql_error());

	//Busco la fuente para la nota segun el centro de costos

	$q="SELECT Carfue, Carcon "
		."FROM ".$tipo_tablas."_000040 "
		."WHERE	Cardvt = 'on' "
		."and	Carest = 'on'";
	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR LA FUENTE PARA LA DEVOLUCION DE DINERO ".mysql_error());

	$num=mysql_num_rows($err)or die (mysql_errno()." -NO SE HA ENCONTRADO LA FUENTE PARA LA DEVOLUCION DE DINERO ".mysql_error());

	$row=mysql_fetch_row($err);
	$con=$row[1]; //numero de la transaccion
	$fue=$row[0]; //fuente de la transaccion

	$q = " UNLOCK TABLES";
	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO DESBLOQUEAR LA TABLA 000040 ".mysql_error());


	//GRABO EL ENCABEZADO DE LA DEVOLUCION DE DINERO

	if ($ven['Ventcl']=='01')
	{
		$q= " INSERT INTO ".$tipo_tablas."_000020 (   Medico       ,   Fecha_data,                Hora_data,              renfue,   rennum,                     renvca              ,     rencod      ,   rennom      ,   rencaj    ,              renusu      ,   rencco    , renest ,     renfec,                renobs,        Seguridad       ) "
		."                               VALUES ('".$tipo_tablas."', '".date('Y-m-d')."','".(string)date("H:i:s")."' ,'".$fue."', ".$con.",     ".number_format(($total),0,'.','').",'".$emp['Empcod']."','".$emp['Empnom']."','".$ven['Vencaj']."','".$wusuario."',   '".$wcco."',     'on', '".date('Y-m-d')."'   , '".$motivo."' ,  'C-".$wusuario."')";
	}
	else
	{
		$q= " INSERT INTO ".$tipo_tablas."_000020 (   Medico       ,   Fecha_data,                Hora_data,              renfue,   rennum,                     renvca              ,     rencod      ,   rennom      ,   rencaj    ,              renusu      ,   rencco    , renest ,     renfec,                renobs,        Seguridad       ) "
		."                               VALUES ('".$tipo_tablas."', '".date('Y-m-d')."','".(string)date("H:i:s")."' ,'".$fue."', ".$con.",     ".number_format(($ven['Vencmo']),0,'.','').",'".$emp['Empcod']."','".$emp['Empnom']."','".$ven['Vencaj']."','".$wusuario."',   '".$wcco."',     'on', '".date('Y-m-d')."'   , '".$motivo."' ,  'C-".$wusuario."')";
	}

	$resins = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO GRABAR EL ENCABEZADO DE LA DEVOLUCION DE DINERO ".mysql_error());

	//GRABO EL DETALLE DE LA DEVOLUCION DE DINERO
	if ($ven['Ventcl']=='01')
	{
		//CONSULTO LA FACTURA AFECTADA DE ESA VENTA EN LA TABLA 19 y la caja que la realizo
		$q="SELECT vennfa, venffa, vencaj from ".$tipo_tablas."_000016 "
		."WHERE	vennum = '".$ven['Vennum']."' and vencco='".$wcco."' "
		."and	venest = 'on' ";

		$err=mysql_query($q,$conex)or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR LA FACTURA DE LA VENTA PARA REALIZAR LA NOTA CREDITO ".mysql_error());
		$num=mysql_num_rows($err)or die (mysql_errno()." -NO SE HA ENCONTRADO LA FACTURA DE LA VENTA PARA REALIZAR LA NOTA CREDITO ".mysql_error());

		$row=mysql_fetch_row($err);
		$numfac=$row[0];
		$fuefac=$row[1];
		$vencaj=$row[2];

		$q= " INSERT INTO ".$tipo_tablas."_000021 (   Medico       , Fecha_data,               Hora_data,                rdefue,  rdenum ,    rdecco       ,   rdefac    ,             rdevta ,     rdevca , rdeest, rdecon, rdevco,          rdeffa, rdesfa,  rdehis, rdeing,    rdeccc,   rdemiv,    Seguridad        ) "
		."                            	VALUES ('".$tipo_tablas."','".date('Y-m-d')."','".(string)date("H:i:s")."' ,'".$fue."', '".$con."', '".$wcco."','".$numfac."', '".$ven['Vennum']."', '".$total."',  'on' ,     '',       0,  '".$fuefac."',      0,     '' ,   '',          '',  '".$numdev."', 'C-".$wusuario."')";

	}
	else
	{
		if ($emp['Empfac']=='on')
		{
			//CONSULTO LA FACTURA AFECTADA DE ESA VENTA EN LA TABLA 16 y la caja que la realizo
			$q="SELECT vennfa, venffa, vencaj from ".$tipo_tablas."_000016 "
			."WHERE	vennum = '".$ven['Vennum']."' and vencco='".$wcco."' "
			."and	venest = 'on' ";

			$err=mysql_query($q,$conex)or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR LA FACTURA DE LA VENTA PARA REALIZAR LA NOTA CREDITO ".mysql_error());
			$num=mysql_num_rows($err)or die (mysql_errno()." -NO SE HA ENCONTRADO LA FACTURA DE LA VENTA PARA REALIZAR LA NOTA CREDITO ".mysql_error());

			$row=mysql_fetch_row($err);
			$numfac=$row[0];
			$fuefac=$row[1];
			$vencaj=$row[2];

			$q= " INSERT INTO ".$tipo_tablas."_000021 (   Medico       , Fecha_data,               Hora_data,                rdefue,  rdenum ,    rdecco       ,   rdefac    ,           rdevta ,             rdevca , rdeest,   rdecon,  rdevco,        rdeffa, rdesfa, rdehis, rdeing, rdeccc,  rdemiv,     Seguridad        ) "
			."                            	VALUES ('".$tipo_tablas."','".date('Y-m-d')."','".(string)date("H:i:s")."' ,'".$fue."', '".$con."', '".$wcco."','".$numfac."','".$ven['Vennum']."', '".$ven['Vencmo']."',   'on' ,     '',       0,  '".$fuefac."',     0,    '' ,   '',       '',  '".$numdev."', 'C-".$wusuario."')";

		}
		else
		{
			//CONSULTO LA FACTURA AFECTADA DE ESA VENTA EN LA TABLA 16 y la caja que la realizo
			$q="SELECT vennfa, venffa, vencaj from ".$tipo_tablas."_000016 "
			."WHERE	vennum = '".$ven['Vennum']."' and vencco='".$wcco."' "
			."and	venest = 'off' ";

			$err=mysql_query($q,$conex)or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR LA FACTURA DE LA VENTA PARA REALIZAR LA NOTA CREDITO ".mysql_error());

			$row=mysql_fetch_row($err);
			$vencaj=$row[2];

			$q= " INSERT INTO ".$tipo_tablas."_000021 (   Medico       , Fecha_data,               Hora_data,                rdefue,     rdenum ,     rdecco ,   rdefac,             rdevta ,                rdevca, rdeest, rdecon, rdevco, rdeffa, rdesfa, rdehis, rdeing, rdeccc,  rdemiv,     Seguridad        ) "
			."                            	VALUES ('".$tipo_tablas."','".date('Y-m-d')."','".(string)date("H:i:s")."' ,'".$fue."', '".$con."', '".$wcco."',       '', '".$ven['Vennum']."', '".$ven['Vencmo']."',   'on' ,    '',      0,     '',      0,     '' ,   '',  '',  '".$numdev."', 'C-".$wusuario."')";

		}
	}

	$resins = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO GRABAR EL ENCABEZADO DE LA DEVOLUCION DE DINERO".mysql_error());


	//GRABO LAS FORMAS DE PAGO DE LA DEVOLUCION DE DINERO
	/*Dado que un clíente puede pagar con varias formas de pago, el orden establecido para determinar a cual se empieza
	a descontar en una nota crédito parcial, es de primero el efectivo y despues segun el ingreso en db*/

	$fp[0]='99';

	$q="SELECT fpacod "
	."FROM ".$tipo_tablas."_000023 "
	."WHERE	fpaest = 'on' and fpacod<>'99' "
	." order by id ";
	$err = mysql_query($q,$conex);
	$numfp=mysql_num_rows($err);
	for ($i=1; $i<=$numfp; $i++)
	{
		$row=mysql_fetch_row($err);
		$fp[$i]=$row[0];
	}

	if ($ven['Ventcl']!='01')
	{
		$total=$ven['Vencmo'];
	}

	//consulto en la tabla 3 la fuente para los recibos para ese centro de costos
	$q="SELECT Ccofrc "
	."FROM ".$tipo_tablas."_000003 "
	."WHERE	Ccocod = '$wcco' "
	."and	Ccoest = 'on'";
	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR LA FUENTE PARA LOS RECIBOS ".mysql_error());

	$num=mysql_num_rows($err)or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR LA FUENTE PARA LOS RECIBOS ".mysql_error());
	$row=mysql_fetch_row($err);
	$fre=$row[0]; //fuente de los recibos para el centro de costos

	$i=0;
	$paro=$numfp;

	while($total!=0 and $i<=$paro )
	{
		//investigo si para la forma de pago hay recibos asociados a la venta
		$q="SELECT SUM(Rfpvfp-rfpdev) "
		."FROM ".$tipo_tablas."_000021, ".$tipo_tablas."_000022 "
		."WHERE Rdevta = '".$ven['Vennum']."' "
		."and	Rdefue = '".$fre."' "
		."and	Rdecco = '".$wcco."' "
		."AND	Rdeest = 'on' "
		."AND 	Rfpfue = Rdefue "
		."AND	Rfpnum = Rdenum "
		."and	Rfpcco = '".$wcco."' "
		."and	Rfpfpa = '".$fp[$i]."' "
		."AND	Rfpest = 'on' "
		."GROUP BY Rfpfpa ";

		$err = mysql_query($q,$conex);
		$numfp=mysql_num_rows($err);

		if($num>0)
		{
			$row=mysql_fetch_array($err);
			$restar=$row[0];
		}
		else
		{
			$restar=0;
		}

		if ($restar>0)
		{
			if($restar > $total)
			{
				$restar=$total;
				$total=0;
			}else
			{
				$total=$total-$restar;
			}

			$q="INSERT INTO ".$tipo_tablas."_000022 (medico,          fecha_data,                   Hora_data, Rfpfue,         Rfpnum,       Rfpfpa,     Rfpvfp,         Rfpdan,        Rfpobs , Rfpest,       Rfpcco, rfpcaf, rfpecu, Seguridad)  "
			." VALUES                   ('".$tipo_tablas."', '".date('Y-m-d')."','".(string)date("H:i:s")."',  '".$fue."', '".$con."', '".$fp[$i]."', ".$restar.", '".$movdev."', '".$motivo."',    'on', '".$wcco."', '".$vencaj."',    'S', 'C-".$wusuario."')";

			$err = mysql_query($q,$conex)or die (mysql_errno()." -NO SE HA PODIDO GRABAR LA DEVOLUCION DE DINERO PARA UNA FORMA DE PAGO".mysql_error());

			//tambien se graba para el recibo para la forma de pago el valor de la devolucion,
			//para los traslados y las consignaciones
			//investigo si para la forma de pago hay recibos asociados a la venta
			$q="SELECT Rfpvfp-rfpdev, B.id "
			."FROM ".$tipo_tablas."_000021 A, ".$tipo_tablas."_000022 B "
			."WHERE Rdevta = '".$ven['Vennum']."' "
			."and	Rdefue = '".$fre."' "
			."and	Rdecco = '".$wcco."' "
			."AND	Rdeest = 'on' "
			."AND 	Rfpfue = Rdefue "
			."AND	Rfpnum = Rdenum "
			."and	Rfpcco = '".$wcco."' "
			."and	Rfpfpa = '".$fp[$i]."' "
			."AND	Rfpest = 'on' "
			."AND	Rfpvfp-rfpdev > 0 ";

			$erract = mysql_query($q,$conex);
			$numact=mysql_num_rows($erract);

			if($numact>0)
			{
				$devol=$restar;
				for ($j=0; $j<$numact; $j++)
				{
					$rowact=mysql_fetch_array($erract);
					if($rowact[0] > $devol)
					{
						$rowact[0]=$devol;
						$devol=0;
					}else
					{
						$devol=$devol-$rowact[0];
					}

					//hago el update del recibo para la forma de pago
					$q="UPDATE ".$tipo_tablas."_000022 "
					."SET rfpdev = rfpdev + ".$rowact[0]." "
					."WHERE id= ".$rowact[1]." ";

					$errREC = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO INCREMENTAR EL CONSECUTIVO PARA ANULACION ".mysql_error());

					if($devol<=0)
					{
						$j=$numact;
					}
				}
			}
		}

		$i++;
	}
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//FUNCION PARA CALCULAR EL VALOR TOTAL DE UN BONO
function consulta_total_bono($vennum,$vde)
{
	global $conex;
	global $tipo_tablas;

	$total=0;

	for ($i=0;$i<count($vde[0]);$i++)
	{
		if (isset ($vde['contar'][$i]))
		{

			$q="SELECT Vdevun,  Vdepiv, Vdecan, Vdedes"
			."    FROM ".$tipo_tablas."_000017 "
			."   WHERE Vdenum = '".$vennum."' "
			."	   AND Vdeart = '".$vde['cod'][$i]."' ";

			//echo $q."<br>";

			$err=mysql_query($q,$conex);
			$num=mysql_num_rows($err);

			if($num > 0)
			{
				$row = mysql_fetch_array($err);
				$descuento=round(((($row["Vdepiv"]/100+1)*$row["Vdedes"])/$row["Vdecan"]),0);
				$articulos["Valuni"]=$row["Vdevun"]-$descuento;
				$articulos["Valtot"]=$articulos["Valuni"]*$vde['can'][$i];
				$total=$total+$articulos["Valtot"];
			}
		}
	}

	//echo $total."<br>";
	return $total;
}

function realizarBono($wcco, $ven, $wusuario, $motivo, $numdev, $vde)
{
	global $conex;
	global $tipo_tablas;
	$tipo_tablas=strtolower($tipo_tablas);

	//INCREMENTO Y CONSULTA DE LA FUENTE Y EL CONSECUTIVO DE NOTAS CREDITO

	$q = "lock table ".$tipo_tablas."_000040 LOW_PRIORITY WRITE";
	$err = mysql_query($q,$conex);

	$q="UPDATE ".$tipo_tablas."_000040 "
	."SET carcon = carcon +1 "
	."WHERE cardca= 'on' "
	."and carest = 'on'";

	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO INCREMENTAR EL CONSECUTIVO PARA BONOS".mysql_error());

	//Busco la fuente para los bonos

	$q="SELECT Carfue, Carcon, Cardes "
	."FROM ".$tipo_tablas."_000040 "
	."WHERE	cardca= 'on' "
	."and	carest = 'on'";
	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR LA FUENTE PARA LBONOS ".mysql_error());

	$num=mysql_num_rows($err)or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR LA FUENTE PARA BONOS ".mysql_error());

	if($num>0)
	{
		$row=mysql_fetch_row($err);
		$con=$row[1]; //numero de la transaccion
		$fue=$row[0]; //fuente de la transaccion
		$des=$row[2];//nombre de la transaccion
	}

	$q = " UNLOCK TABLES";
	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO DESBLOQUEAR LA TABLA 000040 ".mysql_error());

	// 2012-06-04
	// Si el articulo del bono no mueve inventario se debe calcular el valor del bono
	$valor_bono = 0;
	if(!isset($numdev) || !$numdev || $numdev==0 || $numdev=="" || $numdev==" ")
	{
		$numdev = 0;
		$valor_bono = consulta_total_bono($ven['Vennum'],$vde);
	}

	consulta_total_bono($ven['Vennum'],$vde);

	//GRABO EL BONO EN LA TABLA 55

	$q="INSERT INTO ".$tipo_tablas."_000055 (            medico,          fecha_data,                   Hora_data,              Trafec,                      Trahor,  Tracco,                  Traven,              Tratip,     Tradev, Trafue, Tranum, Tradis,    Tramot, Traval, Traest, Seguridad)  "
	." VALUES ('".$tipo_tablas."', '".date('Y-m-d')."', '".(string)date("H:i:s")."', '".date('Y-m-d')."', '".(string)date("H:i:s")."', '".$wcco."','".$ven['Vennum']."', '".$fue."-".$des."',  '$numdev', '$fue', '$con', 'off','$motivo', $valor_bono, 'on', 'C-".$wusuario."')";

	$resins = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO GRABAR EL BONO ".mysql_error());

}


function devolverNomina($Vennum)
{
	global $conex;
	global $tipo_tablas;
	$tipo_tablas=strtolower($tipo_tablas);

	$q="UPDATE ".$tipo_tablas."_000046 "
	."SET	Pnoest='off' "
	."WHERE	Pnovta='".$Vennum."' ";
	$err=mysql_query($q,$conex)or die (mysql_errno()." -NO SE HA PODIDO CANCELAR LA NOMINA ".mysql_error());

}

function devolverInventario($wcco, $ven, $wusuario, $vde, $movdev,$wemp_pmla)
{

	// if($ven['Vennmo'] == consultarAliasPorAplicacion($conex, $wemp_pmla, "ConceptodeTraslado"))
	// {

	// }

	global $conex;
	global $tipo_tablas;
	$tipo_tablas=strtolower($tipo_tablas);
	$numdev=0;
	$entre = 'no';
	if($tipo_tablas == 'uvglobal')
	{

		// busco el concepto de venta.
		$q="Select concod "
		."FROM ".$tipo_tablas."_000008 "
		."WHERE	conmve	= 'on' "
		."and	conest	= 'on' ";
		$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR EL CODIGO DEL MOVIMIENTO DE INVENTARIO EN VENTAS ".mysql_error());
		$num = mysql_num_rows($err) or die (mysql_errno()." -NO SE HA ENCONTRADO EL CODIGO DEL MOVIMIENTO DE INVENTARIO EN VENTAS  ".mysql_error());
		$row=mysql_fetch_array($err);
		$movven=$row['0'];

		// utilizo esta validacion para saber si la venta es una venta ordinario con el concepto 802
		// o se hizo una venta con un traslado al centro de costos virtual  entonces el concepto afectado seria el 002
		// que corresponde a un concepto de traslado
		for($i=0;$i<count($vde['cod']);$i++)
		{

				$q="SELECT Mdevto,Mdecan "
					."FROM ".$tipo_tablas."_000011  "
					."WHERE	Mdedoc = '".$ven['Vennmo']."' "
					."AND	Mdecon = '".$movven."' "
					."AND	Mdeart = '".$vde['cod'][$i]."' ";

			$res = mysql_query($q,$conex);
			$siMdevto='no'; // inicialmente la variable siMdevto si hay movimiento se inicia en no
			if(  $row = mysql_fetch_array($res))
			{

					$siMdevto='si'; // si encontro  movimiento con el concenpto de venta predefinido  no hace nada , luego
									// y trabaja con este concepto

			}
		}
		if($siMdevto =='si')
		{
			// si encontro  movimiento con el concenpto de venta predefinido  no hace nada
			// y trabaja con este concepto

		}
		else
		{
			// de lo contrario mira en root_000051 cual es el concepto de traslado
			$auxmovven = consultarAliasPorAplicacion($conex, $wemp_pmla, "ConceptodeTraslado");
			// echo $auxmovven;
				for($i=0;$i<count($vde['cod']);$i++)
				{

					// Hace una busqueda , con el parametro auxmoven que contiene el concepto de traslado
					$q="SELECT Mdevto,Mdecan "
							."FROM ".$tipo_tablas."_000011  "
							."WHERE	Mdedoc = '".$ven['Vennmo']."' "
							."AND	Mdecon = '".$auxmovven."' "
							."AND	Mdeart = '".$vde['cod'][$i]."' ";

					$res = mysql_query($q,$conex);
					if(  $row = mysql_fetch_array($res))
					{
						 // si encuentra algo pone las variables  en si y a movven = lo pone a valer el concepto de traslado
						 $siMdevto='si';
						 $entre = 'si';
						 $movven=$auxmovven;

					}
				}
		}

	// tambien tengo que validar si la venta tiene factura ya porque si tiene factura no se puede devolver con un traslado
	// 002 sino que se tendria que devolver con un 801  y seguiria el proceso normal
	// $qconsultafactura = "SELECT * FROM ".$tipo_tablas."_000016
						  // WHERE  Vennum = '".$ven['Vennum']." ";
	// $res = mysql_query($qconsultafactura,$conex);
	// while(  $row = mysql_fetch_array($res))
	// {
		// $rowcon = $row['Mencon'];
		// $rowfac = $row['Vennfa'];
	// }

	//busco el codigo para el movimiento de venta
		$q="SELECT concod "
		   ." FROM ".$tipo_tablas."_000008 "
		   ."WHERE	conmve	= 'on' "
		   ."  AND	conest	= 'on' ";

		$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR EL CODIGO DEL MOVIMIENTO DE INVENTARIO EN VENTAS ".mysql_error());
		$num = mysql_num_rows($err) or die (mysql_errno()." -NO SE HA ENCONTRADO EL CODIGO DEL MOVIMIENTO DE INVENTARIO EN VENTAS  ".mysql_error());
		$row=mysql_fetch_array($err);
		$movimientoventa=$row['0'];
	if($movimientoventa == $rowcon)
	{
		// quiere decir que el movimiento corresponde a una venta
		$entre = 'no';
	}
	else
	{
		if($rowfac!='')
		{
			$entre ='no';
			//$consultarfac = "SELECT Mendoc FROM ".$tipo_tablas."_000010 WHERE  Menfac  = '".$ven['Vennum']."'";

		}
	}

	}




	if($entre =='no')
	{
		//busco el codigo para el movimiento de venta
		$q="Select concod "
		."FROM ".$tipo_tablas."_000008 "
		."WHERE	conmve	= 'on' "
		."and	conest	= 'on' ";
		$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR EL CODIGO DEL MOVIMIENTO DE INVENTARIO EN VENTAS ".mysql_error());
		$num = mysql_num_rows($err) or die (mysql_errno()." -NO SE HA ENCONTRADO EL CODIGO DEL MOVIMIENTO DE INVENTARIO EN VENTAS  ".mysql_error());
		$row=mysql_fetch_array($err);
		$movven=$row['0'];
		//echo "Cod Movimiento de inventario: $movven <br>";
	}
	else
	{
		// el movimiento de venta y el de devolucion serian el mismo pues solo se estaria haciendo un traslado
		// entre centros de costos.
		$movven = $movven;
		$movdev = $movven;
 	}

	// GRABAR EL ENCABEZADO DEL MOVIMIENTO DE INVENTARIO

	$q = "lock table ".$tipo_tablas."_000008 LOW_PRIORITY WRITE";
	$err = mysql_query($q,$conex);

	$q = "UPDATE ".$tipo_tablas."_000008 "
	."SET Concon = Concon +1  "
	."WHERE Concod = '".$movdev."'";
	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO INCREMENTAR EL CONSECUTIVO PARA EL MOVIMIENTO DE INVENTARIO DE DEVOLUCIONES".mysql_error());

	$q = "SELECT Concon "
	."FROM ".$tipo_tablas."_000008 "
	."WHERE Concod='".$movdev."'";
	$err = mysql_query($q,$conex)or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR EL CONSECUTIVO PARA EL MOVIMIENTO DE INVENTARIO DE DEVOLUCIONES".mysql_error());
	$num= mysql_num_rows($err)or die (mysql_errno()." -NO SE HA ENCONTRADO EL CONSECUTIVO PARA EL MOVIMIENTO DE INVENTARIO DE DEVOLUCIONES".mysql_error());

	$row=mysql_fetch_row($err);
	$numdev=$row[0];
	//echo "Consecutivo Movimiento de inventario: $numdev <br>";

	$q = " UNLOCK TABLES";
	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO DESBLOQUEAR LA TABLA DE CONSECUTIVOS PARA LOS MOVIMIENTOS DE INVENTARIO".mysql_error());

	$q = "lock table ".$tipo_tablas."_000010 LOW_PRIORITY WRITE";
	$err = mysql_query($q,$conex);

	if($entre == 'si')
	{
		$q="INSERT INTO ".$tipo_tablas."_000010 (medico,          fecha_data,                  Hora_data,           Menano,          Menmes,        Mendoc,        Mencon,       Menfec,        Mencco, Menccd,               Mendan, Menpre, Mennit,          Menusu,               Menfac, Menest, Seguridad, Mendev) "
		." VALUES (                  '".$tipo_tablas."', '".date('Y-m-d')."', '".(string)date("H:i:s")."', '".date('Y')."', '".date('m')."', '".$numdev."', '".$movdev."', '".date('Y-m-d')."', '". $wcco."',    '0', '".$ven['Vennmo']."',    '0',    '0', '".$wusuario."',  '".$ven['Vennum']."',  'on', 'C-".$wusuario."' , 'on')";

	}
	else
	{
		$q="INSERT INTO ".$tipo_tablas."_000010 (medico,          fecha_data,                  Hora_data,           Menano,          Menmes,        Mendoc,        Mencon,       Menfec,        Mencco, Menccd,               Mendan, Menpre, Mennit,          Menusu,               Menfac, Menest, Seguridad ) "
		." VALUES (                  '".$tipo_tablas."', '".date('Y-m-d')."', '".(string)date("H:i:s")."', '".date('Y')."', '".date('m')."', '".$numdev."', '".$movdev."', '".date('Y-m-d')."', '". $wcco."',    '0', '".$ven['Vennmo']."',    '0',    '0', '".$wusuario."',  '".$ven['Vennum']."',  'on', 'C-".$wusuario."' )";
	}
	$err=mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO INGRESAR EL ENCABEZADO DEL MOVIMIENTO DE INVENTARIO".mysql_error());

	$q = " UNLOCK TABLES";
	$err = mysql_query($q,$conex)or die (mysql_errno()." -NO SE HA PODIDO DESBLOQUEAR LA TABLA DE ENCABEZADO PARA EL MOVIMIENTO DE INVENTARIO".mysql_error());


	//REALIZAMOS EL MOVIMIENTO DE INVENTARIO PARA CADA UNO DE LOS ARTICULOS
    for($i=0;$i<count($vde['cod']);$i++)
	{
     //====================================================================================================
     //Aca se verifica por  cada uno de los articulos si este mueve inventarios o no
     $q = " SELECT gruinv "
         ."   FROM ".$tipo_tablas."_000001, ".$tipo_tablas."_000004 "
         ."  WHERE artcod = '".$vde['cod'][$i]."'"
         ."    AND mid(artgru,1,instr(artgru,'-')-1) = grucod ";
     $res = mysql_query($q,$conex)or die (mysql_errno()." - FALTA EL GRUPO EN EL ARTICULO ".mysql_error());
     $rowinv=mysql_fetch_row($res);
     $wmueinv=$rowinv[0];
     //====================================================================================================

     if (strtoupper($wmueinv)=="ON")   //Entra solo para los articulos que mueven inventario
		{
		if(isset($vde['contar'][$i]) and ($vde['canDev'][$i]>0))
		{
			/*Buscar precio VENTA de movimiento de inventario*/




			 $q="SELECT Mdevto,Mdecan "
			."FROM ".$tipo_tablas."_000011  "
			."WHERE	Mdedoc = '".$ven['Vennmo']."' "
			."and	Mdecon = '".$movven."' "
			."and	Mdeart = '".$vde['cod'][$i]."' ";

			$err = mysql_query($q,$conex)or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR EL DETALLE DE MOVIMIENTO DE VENTA DEL ARTICULO ".mysql_error());
			$num = mysql_num_rows($err) or die (mysql_errno()." -NO SE HA ENCONTRADO EL DETALLE DE MOVIMIENTO DE VENTA DEL ARTICULO ".mysql_error());
			$row=mysql_fetch_row($err);
			$valorUni=$row[0]/$row[1]; //valor unitario sin iva

			//SE BLOQUE TEMPORALMENTE LA TABLA PARA MODIFICAR EL KARDEX
			$q = "lock table ".$tipo_tablas."_000007 LOW_PRIORITY WRITE";
			$err = mysql_query($q,$conex);


			//--------------------- apartir de aqui vendria el nuevo moviemiento
			if($entre == 'si')
			{
				$q = " UNLOCK TABLES";
				$err = mysql_query($q,$conex)or die (mysql_errno()." -NO SE HA PODIDO DESBLOQUEAR LA TABLA DE ENCABEZADO PARA EL MOVIMIENTO DE INVENTARIO".mysql_error());



				$q = "SELECT Mencco,Menccd FROM  ".$tipo_tablas."_000010 WHERE Mendoc = '".$ven['Vennmo']."' AND  Mencon = '".$movven."' ";
				// si entre == si quiere decir que se hizo una venta partida y cuando se realizo la venta solo se hizo un traslado
				// entonces se tiene que hacer un movimiento distinto a restituir los productos en el centro de costos, lo que se tiene que
				// hacer es  sacar del centro de costos virtual al fisico de nuevo.
				$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$row = mysql_fetch_array($res);
				$origen = $row['Mencco'];
				$destino = $row['Menccd'];

				$update = "UPDATE ".$tipo_tablas."_000010  SET  Mencco = '".$destino."' , Menccd='".$origen."' WHERE Mendoc = '".$numdev."'  AND   Mencon = '".$movven."' ";
				$err=mysql_query($update,$conex)or die (mysql_errno()." -error :".$update."-".mysql_error());

				//---Encuentro los nuevos valor promedio ponderado del articulo.
				//Recalculo el costo promedio del articulo en el centro de costos destino

				$q= "SELECT karexi, karpro "
					."  FROM ".$tipo_tablas."_000007 "
					." WHERE karcco = '".$destino."'"
					."   AND karcod = '".$vde['cod'][$i]."'";
				$res_cos = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$row_cos = mysql_fetch_array($res_cos);

				$wexist_actuales_origen = $row_cos[0];
				$wcosto_pro_actual_origen = $row_cos[1];

				$q7= "SELECT karexi, karpro "
					."  FROM ".$tipo_tablas."_000007 "
					." WHERE karcco = '".$origen."'"
					."   AND karcod = '".$vde['cod'][$i]."'";
				$res_cos = mysql_query($q7,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q7." - ".mysql_error());
				$row_cos = mysql_fetch_array($res_cos);

				$wexist_actuales_destino = $row_cos[0];
				$wcosto_pro_actual_destino = $row_cos[1];

				//Nuevo costo promedio del articulo en el centro de costos a grabar
				/*$wnuevocospro = (($wexist_actuales_destino * $wcosto_pro_actual_destino) + ($wcantidad * $wcosto_pro_actual_origen))/($wexist_actuales_destino + $wcantidad);
				$wnuevocospro = round($wnuevocospro,4);*/

                //Nuevo costo promedio del articulo en el centro de costos a grabar
                $divisor      = $wexist_actuales_destino + $wcantidad;
                $wnuevocospro = ( $divisor == 0 ) ? 0 : (($wexist_actuales_destino * $wcosto_pro_actual_destino) + ($wcantidad * $wcosto_pro_actual_origen))/$divisor;
                $wnuevocospro = round($wnuevocospro,4);




				// se descuenta la cantidad en el centro de costos virtual.
				$q= "  UPDATE ".$tipo_tablas."_000007 "
					."    SET karexi = karexi - ".$vde['canDev'][$i]
					."  WHERE karcco = '".$destino."'"
					."    AND karcod = '".$vde['cod'][$i]."'";
				$err=mysql_query($q,$conex)or die (mysql_errno()." -NO SE HA PODIDO ACTUALIZAR EL KARDEX DE INVENTARIO PARA EL ARTICULO".mysql_error());



				// Se modifican las cantidades en el centro de costos fisico
				$q="UPDATE ".$tipo_tablas."_000007 "
				."    SET   Karexi= Karexi+".$vde['canDev'][$i]." ,"
				."          Karpro = ".$wnuevocospro."  "
				."  WHERE	Karcod	= '".$vde['cod'][$i]."' "
				."    AND	Karcco	= '".$origen."' ";
				$err=mysql_query($q,$conex)or die (mysql_errno()." -NO SE HA PODIDO ACTUALIZAR EL KARDEX DE INVENTARIO PARA EL ARTICULO".mysql_error());

				//$res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

				$q="INSERT INTO ".$tipo_tablas."_000011 (medico,          fecha_data,                    Hora_data,    Mdecon,           Mdedoc,                 Mdeart,                  Mdecan,                               Mdevto,            Mdepiv,           Mdefve, Mdenlo, Mdeest, Seguridad) "
				."  VALUES                  ('".$tipo_tablas."', '".date('Y-m-d')."', '".(string)date("H:i:s")."', '".$movdev."', '".$numdev."', '".$vde['cod'][$i]."', '".$vde['canDev'][$i]."', '".($valorUni*$vde['canDev'][$i])."', '".$vde['iva'][$i]."', '0000-00-00',    '0', 'on', 'C-".$wusuario."')";
				$err=mysql_query($q,$conex) or die (mysql_errno()." -NO SE PUDO GRABAR EL DETALLE DEL MOVIMIENTO DE INVENTARIO PARA EL ARTICULO".mysql_error());




			}
			else
			{
				/*Buscar el valor promedio actual del artículo*/
				$q="Select * "
				."FROM ".$tipo_tablas."_000007 "
				."WHERE	Karcod	= '".$vde['cod'][$i]."' "
				."and	Karcco	= '$wcco' ";
				$err = mysql_query($q,$conex)or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR EL ARTICULO EN EL KARDEX DE INVENTARIO".mysql_error());
				$num = mysql_num_rows($err)or die (mysql_errno()." -NO SE HA ENCONTRADO EL ARTICULO EN EL KARDEX DE INVENTARIO ".mysql_error());
				$row=mysql_fetch_array($err);

				/*Nuevo valor promedio:
				[(Valor promedio actual*Número de existencias actuales)+(Valor promedio artículo en venta* número de articulos a devolver)]/
				(Número actual de existencias+ número de artículos a devolver)*/
				$karpro=(($row['Karpro']*$row['Karexi'])+ ($vde['canDev'][$i]*$valorUni))/($row['Karexi']+$vde['canDev'][$i]);

				/*Modificar número de existencias y valor ptomedio*/
				$q="UPDATE ".$tipo_tablas."_000007 "
				."SET Karpro=".$karpro." ,Karexi= Karexi+".$vde['canDev'][$i]." "
				."WHERE	Karcod	= '".$vde['cod'][$i]."' "
				."and	Karcco	= '$wcco' ";
				$err=mysql_query($q,$conex)or die (mysql_errno()." -NO SE HA PODIDO ACTUALIZAR EL KARDEX DE INVENTARIO PARA EL ARTICULO".mysql_error());

				$q = " UNLOCK TABLES";
				$err=mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO DESBLOQUEAR LA TABAL DEL KARDEX DE INVENTARIO".mysql_error());


				//GRABAR EL DETALLE DEL MOVIMIENTO DE INVENTARIO

				$q="INSERT INTO ".$tipo_tablas."_000011 (medico,          fecha_data,                    Hora_data,    Mdecon,           Mdedoc,                 Mdeart,                  Mdecan,                               Mdevto,            Mdepiv,           Mdefve, Mdenlo, Mdeest, Seguridad) "
				."  VALUES                  ('".$tipo_tablas."', '".date('Y-m-d')."', '".(string)date("H:i:s")."', '".$movdev."', '".$numdev."', '".$vde['cod'][$i]."', '".$vde['canDev'][$i]."', '".($valorUni*$vde['canDev'][$i])."', '".$vde['iva'][$i]."', '0000-00-00',    '0', 'on', 'C-".$wusuario."')";
				$err=mysql_query($q,$conex) or die (mysql_errno()." -NO SE PUDO GRABAR EL DETALLE DEL MOVIMIENTO DE INVENTARIO PARA EL ARTICULO".mysql_error());

			}
		}
       }
	}

	return $numdev;
}

function devolverPuntos($Vennum, $totPun, $wusuario)
{
	global $conex;
	global $tipo_tablas;
	$tipo_tablas=strtolower($tipo_tablas);

	$q="SELECT Punind, Pundto "
	."FROM ".$tipo_tablas."_000059 "
	."WHERE	Punvta = '".$Vennum."' "
	."AND	Puntip = 'C' ";
	$err=mysql_query($q,$conex);
	$num=mysql_num_rows($err);
	if($num>0)
	{
		for($i=0;$i<$num;$i++)
		{
			$row=mysql_fetch_array($err);

			$q1="INSERT INTO ".$tipo_tablas."_000059 (medico,         fecha_data,                    Hora_data,               Punind,              Pundto,   Punfec,           Puntip,  Puncan,     Punvta, Seguridad)  "
			. " VALUES                ('".$tipo_tablas."', '".date('Y-m-d')."', '".(string)date("H:i:s")."', '".$row['Punind']."','".$row['Pundto']."',  '".date('Y-m-d')."', 'D',  $totPun, '".$Vennum."', 'C-".$wusuario."')";

			$err1=mysql_query($q1,$conex) or die (mysql_errno()." -NO SE PUDO GRABAR EL MOVIMIENTO DE PUNTOS".mysql_error());

			$q1="UPDATE ".$tipo_tablas."_000060 "
			."SET	Salsal = Salsal - ".$totPun.", Saldev = Saldev + ".$totPun." "
			."WHERE Saldto = '".$row['Pundto']."' ";
			$err1=mysql_query($q1,$conex)or die (mysql_errno()." -NO SE PUDO ACTUALIZAR EL SALDO DE PUNTOS".mysql_error());

		}
	}
}

function consultar_fosyga($wemp_pmla)
{
  global $conex;
  $consulta = "0";

  $q = " SELECT Detval "
	  ."   FROM root_000051 "
	  ."  WHERE Detemp = '".$wemp_pmla."'"
	  ."    AND Detapl = 'fosyga' ";
  $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
  $num = mysql_num_rows($res);

  if(isset($num) && $num>0)
	$consulta = "1";

  return $consulta;
}

//////////////////////////////////////////////////////////////FIN FUNCIONES//////////////////////////////////////////////////////////////////

//////////////////////////////////////////////////////////////INICIACION DE VARIABLES////////////////////////////////////////////////////////


session_start();

// Inicia la sessión del usuario
if (!isset($user))
    if(!isset($_SESSION['user']))
      session_register("user");

// Si el usuario no está registrado muestra el mensaje de error
if(!isset($_SESSION['user']))
    echo "error";
else	// Si el usuario está registrado inicia el programa
{

  include ("root/comun.php");

  $conex = obtenerConexionBD("matrix");

  // Llamo la función que me indica si se van a consultar datos desde Unix en la facturación por SOAT
  $consulta_unix = consultar_fosyga($wemp_pmla);

  // Declaro campo de formulario que me indica si se van a consultar datos desde Unix en la facturación por SOAT
  echo "<input type='HIDDEN' name='consulta_unix' value='".$consulta_unix."'>";

  if(isset($consulta_unix) && $consulta_unix=="1")
  {
	$wbasedato_mov = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
  }
  else
  {
	$wbasedato_mov = "";
  }

  $institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

  $wbasedato = $institucion->baseDeDatos;
  $wentidad = $institucion->nombre;

  $tipo_tablas = $wbasedato;

  // Obtengo los datos del usuario
  $pos = strpos($user,"-");
  $wusuario = substr($user,$pos+1,strlen($user));

  // Obtener titulo de la página con base en el concepto
  $titulo = "Proceso de Venta - Devolución de Artículos";

  $wfecha = date("Y-m-d");

  // Aca se coloca la ultima fecha de actualización
  $wactualiz = " Diciembr 01 de 2015 ";

  //**********************************************//
  //********** P R I N C I P A L *****************//
  //**********************************************//

  // Obtener titulo de la página con base en el concepto
  $titulo = "Devolución de artículos";

  $wfecha = date("Y-m-d");

	//inciaciacion de colores
	$wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
	$wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
	$wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
	$wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro

	$wcf3="#FFDBA8";	//COLOR DEL FONDO 3 PARA RESALTAR -- Amarillo quemado claro
	$wcf4="#A4E1E8";	//COLOR DEL FONDO 4 -- Aguamarina claro
	$wcf5="#57C8D5";	//COLOR DEL FONDO 5 -- Aguamarina Oscuro
	$wclam="#A4E1E8";	//COLOR DE LA LETRA -- Aguamarina Clara

	//identifico codigo de nomina del usuario
	$key = substr($user,2,strlen($user));
	$pos = strpos($user,"-");
	$wusuario = substr($user,$pos+1,strlen($user)); //codigo del usuario de matrix

	// Se muestra el encabezado del programa
	encabezado($titulo,$wactualiz, "clinica");

	////////////////////////////////////////////////////////PROGRAMA//////////////////////////////////////////////////////////


	if(!isset($wcco))//primera vez que se entra, porque es una variable que se manda por hidden
	{
		//consigo el centro de costos para el usuario, quien debe estar registrado como cajero en la tabla 30
		$permiso=permisoUsuario($wusuario, $wcco, $wnomcco);

		if(!$permiso)
		{
			//aviso que la persona no tiene autorizacion para realizar la transaccion
			pintarAlerta('EL USUARIO ESTA INACTIVO O NO TIENE PERMISO PARA REALIZAR ESTAS TRANSACCIONES');
			unset($wcco);
		}
	}

	if (isset($wcco))//cualquier entrada, si fue la primer es porque hubo permiso
	{
		pintarInicio($wcco,$wnomcco); //inicializa la forma y manda por hidden el centro de costos

		if(!isset($Vennum)) //SI NO SE HA INGRESADO EL NUMERO DE LA VENTA, SE PRESENTA TABLA DE INGRESO
		{
			pintarIngresoVenta($wcf2);
		}
		else //SE TIENE EL NUMERO DE LA VENTA
		{
			//consulto el concepto de movimiento de inventario de devoluciones
			//el cual sera usado para validar si ya hay devoluciones y para guardar las nuevas
			$movdev =consultarConcepto();

			if (!isset ($bandera)) //primera vez que se entra al progrma
			{
				//echo "entro por buscar venta";
				$existe=buscarVenta($Vennum, $wcco, $ven); //busco la venta ingresada

				if ($existe)//se encontro la venta ingresada
				{
					if($ven["Venest"] != 'off')//si se encontro la venta y esta activa
					{
						//////////////////////REALIZO VALIDACIONES SEGUN EL TIPO DE CLIENTE DE LA VENTA///////////////////////

						$exp=explode('-',$ven["Ventcl"]);
						$ven["Ventcl"]=trim($exp[0]);
						switch ($ven["Ventcl"])
						{
							case '01': //caso en que sea particular
							$cliente=BuscarCliente($ven['Vennit'], $cli); //busco los datos del cliente que se devuelven en $cli
							$empresa=BuscarEmpresa($ven['Vencod'], $ven['Ventcl'], $emp); //busco datos de la empresa que en este caso es particular
							$nomina=true; //pasa validacion de nomina
							$ven['nomina']=false; //indica que no tiene en cuenta la nomina
							$pagada=true; //pasa validacion de pagada
							$numart=buscarArticulos($Vennum, $vde, 'on', $totPun); //busco los articulos para la venta
							$articulos=validarDisponibilidad($Vennum, $numart, $vde, $movdev); //me fijo que no hayan sido devueltos todos los articulos
							$total=0; //inicialmente el particular no tendra ningun articulo seccionado para devolucion
							if (!$articulos) //si no hay articulos que se puedan devolver
							{
								pintarAlerta('TODOS LOS ARTICULOS YA FUERON DEVUELTOS');
							}
							break;

							default: //caso en que sea empresa
							$empresa=BuscarEmpresa($ven['Vencod'], $ven['Ventcl'], $emp); //busco datos de la empresa que se devuelven en $emp

							if (!$empresa) //si la empresa no es encontrada
							{
								pintarAlerta('LA EMPRESA DE LA VENTA NO SE ENCUENTRA REGISTRADA O ESTA INACTIVA');
								$cliente=false;
								$nomina=false;
								$pagada=false;
								$articulos=false;
							}
							else
							{
								$cliente=BuscarCliente($ven['Vennit'], $cli); //busco los datos del cliente en este caso la empresa
								$nomina=validarNomina($ven['Ventcl'], $ven['Vennum'], $ven['nomina']); //busco si la empresa descuenta de nomina a empleados
								//y valido que en caso de que si no se haya pagado la primera cuota
								if (!$nomina)//si no se pasa validacion de nomina
								{
									pintarAlerta( "YA SE EFECTUO UNO O MAS PAGOS DE LA VENTA POR NOMINA");
									$pagada=false;
									$articulos=false;
								}
								else
								{
									//validacion dependiendo si la empresa genera factura al momento de la venta
									if($wemp_pmla =='06' and  $emp['Empfac'] == 'off')
									{
										//validarsifactura
										$select = "SELECT Vennfa
													 FROM ".$tipo_tablas."_000016
													WHERE Vennum='".$ven['Vennum']."'";
										$res = mysql_query($select,$conex) or die (mysql_errno()."  ".mysql_error());

										if($row= mysql_fetch_array($res))
										{
											if ( $row['Vennfa'] !='')
											{
												pintarAlerta( "LA VENTA YA CUENTA CON UNA FACTURA");
												$articulos=false;
												$tienefactra   = 'si';
											}
											else
											{


												 $query_saldo = "SELECT  Rdevta,Rdevca,Rdenum
																   FROM  ".$tipo_tablas."_000021
																  WHERE  Rdevta='".$ven['Vennum']."' AND Rdeest='on'";

												 $res2 = mysql_query($query_saldo,$conex);
												 $vrecibido = 0;
												 $nrecibido = '';
												 $p=0;
												 while($row3 = mysql_fetch_array($res2))
												 {
													$p++;
													$vrecibido =$vrecibido + $row3['Rdevca'] ;
													$nrecibido = $nrecibido.",".$row3['Rdenum'];

												 }
												 if ( $p!=0)
												 {
													pintarAlerta( "LA VENTA YA CUENTA CON RECIBOS"." (Numero de documento: ".substr($nrecibido,1)."  ). DEBEN SER ANULADOS PRIMERO");
													$articulos=false;

												 }
												 else
												 {
													$pagada = true;

												 }

												 if ($pagada)
												{
													//validamos que no haya una devolucion previa
													$articulos=validarDevoluciones($ven['Vennum'], $movdev);
													if (!$articulos)
													{
														pintarAlerta( "YA SE REALIZÓ UNA DEVOLUCION PREVIA");
													}
													else
													{
														$numart=buscarArticulos($Vennum, $vde, 'off', $totPun);//busco los articulos de la venta
														$total=$ven['Vencmo']; //el total sera eld e la cuota moderadora
													}
										}



											}

										}



									}
									else
									{
										if ($emp['Empfac']=='off' )
										{
											//la empresa no genera factura al momento de la venta
											//se valida que no exista ya la factura
											$pagada=validarAblocada($ven['Vennum'], $wcco);
											if (!$pagada)//si no se pasa validacion de nomina
											{
												pintarAlerta( "LA VENTA YA CUENTA CON UNA FACTURA ABLOCADA");
												$articulos=false;
											}
										}
										else
										{
											//la empresa Si genera factura al momento de la venta
											//se valida que la empresa no haya pagado ya la factura
											$pagada=validarPagada($ven['Vennum'], $wcco);
											if (!$pagada)//si no se pasa validacion de nomina
											{
												pintarAlerta( "LA FACTURA YA HA SIDO PAGADA POR LA EMPRESA");
												$articulos=false;
											}
										}

										if ($pagada)
										{
											//validamos que no haya una devolucion previa
											$articulos=validarDevoluciones($ven['Vennum'], $movdev);
											if (!$articulos)
											{
												pintarAlerta( "YA SE REALIZÓ UNA DEVOLUCION PREVIA");
											}
											else
											{
												$numart=buscarArticulos($Vennum, $vde, 'off', $totPun);//busco los articulos de la venta
												$total=$ven['Vencmo']; //el total sera eld e la cuota moderadora
											}
										}

									}

								}
							}
							break;
						}

						//////////////////////SI SE HAN PASADO LAS VALIDACIONES SEGUN EL TIPO DE CLIENTE DE LA VENTA///////////////////////

						if ($cliente and $empresa and $nomina and $pagada and $articulos)
						{
							pintarEncabezado($wcf2, $wcf5, $wclfg, $ven, $cli, $emp);
							pintarDetalle($wcf4, $wcf5, $wclfg, $wclfa, $wcf2, $wcf3, $vde, $ven, $total, 'on' , $totPun);
							pintarFinalForma(3, $wclfg);
						}
					}
					else //la venta se encuentra anulada
					{
						pintarIngresoVenta($wcf2);
						pintarAlerta('LA VENTA INGRESADA SE ENCUENTRA EN ESTADO ANULADO');
					}
				}
				else //la venta no existe en base de datos
				{
					pintarIngresoVenta($wcf2);
					pintarAlerta('LA VENTA INGRESADA NO EXISTE');
				}
			}
			else //convierto los numero a nombres en los vectores, para facilitar lectura
			{
				$ven['Vennum']=$ven[0];
				$ven['Ventcl']=$ven[1];
				$ven['Vencmo']=$ven[2];
				$ven['Vennmo']=$ven[3];
				$ven['Vennid']=$ven[4];
				$ven['Venest']=$ven[5];
				$ven['Vennit']=$ven[6];
				$ven['Vencod']=$ven[7];
				$ven['Vencon']=$ven[8];
				$ven['Vencco']=$ven[9];
				$ven['Venfec']=$ven[10];
				$ven['Venvto']=$ven[11];
				$ven['Venviv']=$ven[12];
				$ven['Vencop']=$ven[13];
				$ven['Vendes']=$ven[14];
				$ven['Venrec']=$ven[15];
				$ven['Vencaj']=$ven[16];
				$ven['Condes']=$ven[17];
				$ven['nomina']=$ven[18];
				$ven['Ccodes']=$ven[19];

				for ($i=0;$i<count($vde[0]);$i++)
				{
					$vde['cod'][$i]=$vde[0][$i];
					$vde['nom'][$i]=$vde[1][$i];

					$vde['iva'][$i]=$vde[2][$i];
					$vde['valArt'][$i]=$vde[3][$i];
					$vde['can'][$i]=$vde[4][$i];
					$vde['valUni'][$i]=$vde[5][$i];
					$vde['pun'][$i]=$vde[6][$i];

					if (isset($vde[7][$i]))
					{
						$vde['canDev'][$i]=$vde[7][$i];
					}

					if (isset($vde[8][$i]))
					{
						$vde['contar'][$i]=$vde[8][$i];
					}
				}

				//convierto tambien para datos de cliente y empresa
				$emp['Empcod']=$emp[0];
				$emp['Empnit']=$emp[1];
				$emp['Empnom']=$emp[2];
				$emp['Emptem']=$emp[3];
				$emp['Empfac']=$emp[4];
				$emp['Emptar']=$emp[5];
				$cli['Clidoc']=$cli[0];
				$cli['Clinom']=$cli[1];
				$cli['Clitip']=$cli[2];
				$cli['Clipun']=$cli[3];
				$cli['Clite1']=$cli[4];
			}

			if (isset ($bandera) and $bandera==3) //ya le dieron submit una vez al boton aceptar
			{

				//valido las cantidades de los articulos si es particular y recalculo el total
				if ($ven['Ventcl']=='01')
				{
					$permitido=true;
					$num= count($vde['cod']);
					$total=0;
					$totPun=0;
					for($i=0;$i<$num;$i++)
					{
						if (isset($vde['contar'][$i]))
						{
							if ($vde['can'][$i]<$vde['canDev'][$i]) //si no pasa un articulo validacion de cantidad
							{
								$permitido=false;
								$vde['color'][$i]='on';
								unset($vde['contar'][$i]);
								$vde['canDev'][$i]=$vde['can'][$i];
							}
							else
							{
								$totPun=$totPun+($vde['canDev'][$i]*$vde['pun'][$i]);
							}

							$total=$total+($vde['canDev'][$i]*$vde['valUni'][$i]);
						}
					}
					if ($total==0)
					{
						$permitido=false;
					}
				}
				else
				{
					$permitido=true;
				}

				if ($permitido)
				{
					//unicamente se vuelve a desplegar todo con el final recalculado
					pintarEncabezado($wcf2, $wcf5, $wclfg, $ven, $cli, $emp);

					//dependiendo del tipo de cliente y si se dio realizar, se despliega la opcion final
					if (isset($realizar)) //unicamente se recalcula el total y se vuelve a desplegar todo
					{
						pintarDetalle($wcf4, $wcf5, $wclfg, $wclfa, $wcf2, $wcf3, $vde, $ven, $total, 'off', $totPun);
						if ($ven['Ventcl']=='01')
						{
							pintarFinalForma(4, $wclfg); //presenta opcion para seleccionar si es nota credito o devolucion
						}                                //y el campo de observacion y password
						else
						{
							pintarFinalForma(5, $wclfg); //presenta el campo de observacion y password
						}
					}
					else
					{
						pintarDetalle($wcf4, $wcf5, $wclfg, $wclfa, $wcf2, $wcf3, $vde, $ven, $total, 'on', $totPun);
						pintarFinalForma(3, $wclfg); //vuelve a mostrar la misma pantalla con el total recalculado
					}
				}
				else
				{
					if ($total>0)
					{
						pintarAviso('LOS ARTICULOS SEÑALADOS EXCEDEN LAS CANTIDADES POSIBLES');
					}
					else
					{
						pintarAviso('DEBE SELECCIONAR ALGUN ARTICULO PARA DEVOLVER');
					}
					$total=0;
					pintarEncabezado($wcf2, $wcf5, $wclfg, $ven, $cli, $emp);
					pintarDetalle($wcf4, $wcf5, $wclfg, $wclfa, $wcf2, $wcf3, $vde, $ven, $total, 'on', $totPun);
					pintarFinalForma(3, $wclfg);
				}
			}

			if (isset($bandera) and $bandera==5)
			{
				//valido que esta lleno el motivo, es obligatorio excepto cuanto en devolucion a particular
				$validacion1=true;
				if ((!isset($radio) or $radio=='NC') and (!isset($motivo) or $motivo==''))
				{
					$validacion1=false;
					pintarAviso('DEBE INGRESAR UN MOTIVO PARA LA DEVOLUCION');
					pintarEncabezado($wcf2, $wcf5, $wclfg, $ven, $cli, $emp);
					pintarDetalle($wcf4, $wcf5, $wclfg, $wclfa, $wcf2, $wcf3, $vde, $ven, $total, 'off', $totPun);
					IF ($ven['Ventcl'])
					{
						pintarFinalForma(4, $wclfg);
					}
					else
					{
						pintarFinalForma(5, $wclfg);
					}
				}
				$validacion2=confirmarPassword($pass, $wusuario);

				if ($validacion1 and $validacion2)
				{
					for($i=0;$i<count($vde['cod']);$i++)
					   {
					    //====================================================================================================
					    //Aca se verifica por  cada uno de los articulos si este mueve inventarios o no, esto lo hago por cada
					    //articulo, pero si encuentro uno que si mueve salgo del FOR por con uno que mueve, ya se crearia el
					    //documento de devolucion con todos los demas que muevan inventario si es que los hay.
					    $q = " SELECT gruinv "
					        ."   FROM ".$tipo_tablas."_000001, ".$tipo_tablas."_000004 "
					        ."  WHERE artcod = '".$vde['cod'][$i]."'"
					        ."    AND mid(artgru,1,instr(artgru,'-')-1) = grucod ";
					    $res = mysql_query($q,$conex)or die (mysql_errno()." - FALTA EL GRUPO EN EL ARTICULO ".mysql_error());
					    $rowinv=mysql_fetch_row($res);
					    $wmueinv=$rowinv[0];

					    if (strtoupper($wmueinv)=="ON")
				          {
						   //se realizaran las transacciones necesarias para la devolucion
						   $numdev=devolverInventario($wcco, $ven, $wusuario, $vde, $movdev,$wemp_pmla);
						   break;
					      }
					    //====================================================================================================
				       }

					if(!isset($numdev) || !$numdev)
						$numdev = 0;

					if($wemp_pmla == '06' and  $emp['Empfac'] == 'off' )
					{
						//echo "entro3";
						realizarAnulacion($wcco, $ven, $wusuario, $motivo, $numdev);

					}
					else
					{


						if ($emp['Empfac']=='on') //empresa que genera factura o particular
						{
							//echo "entro2";
							realizarNota($wcco, $wnomcco, $ven, $wusuario, $motivo, $vde, $emp, $total, $numdev);
						}
						else //empresa que no genera factira
						{
							//echo "entro1";
							realizarAnulacion($wcco, $ven, $wusuario, $motivo, $numdev);
						}

					}

					if (((isset($radio)and $radio=='NC') or ($ven['Ventcl']!='01' and $ven['Vencmo']>0))) //nota credito a particular
					{                                                                                   //o empresa con cuota moderadora
						devolverDinero($movdev, $total, $ven, $wcco, $wnomcco, $wusuario, $motivo, $emp, $numdev);
					}
					else if (isset($radio)and $radio=='DV')
					{
						realizarBono($wcco, $ven, $wusuario, $motivo, $numdev, $vde);
					}

					if($ven['nomina'])
					{
						devolverNomina($ven['Vennum']);
					}

					if($totPun>0)
					{
						devolverPuntos($ven['Vennum'], $totPun, $wusuario);
					}
					pintarEncabezado($wcf2, $wcf5, $wclfg, $ven, $cli, $emp);
					pintarDetalle($wcf4, $wcf5, $wclfg, $wclfa, $wcf2, $wcf3, $vde, $ven, $total, 'off', $totPun);
					pintarImpresion($wcco, $numdev, $ven['Vennum']);
					pintarFinalForma(6, $wclfg);
				}
				else if ($validacion1)
				{
					pintarAviso('DEBE INGRESAR UN PASSWORD CORRECTO');
					pintarEncabezado($wcf2, $wcf5, $wclfg, $ven, $cli, $emp);
					pintarDetalle($wcf4, $wcf5, $wclfg, $wclfa, $wcf2, $wcf3, $vde, $ven, $total, 'off', $totPun);
					IF ($ven['Ventcl'])
					{
						pintarFinalForma(4, $wclfg);
					}
					else
					{
						pintarFinalForma(5, $wclfg);
					}
				}
			}
		}
	}
}
?>
</body>
</html>
