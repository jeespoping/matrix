<?php
//2019-02-13 camilo zapata: se corrige el envio de variables por referencia.
?>
<html>
<head>
  	<title>MATRIX Programa de Inventarios</title>
	<style type="text/css">
	<!--
		.BlueThing
		{
			background: #99CCFF;
		}

		.SilverThing
		{
			background: #CCCCCC;
		}

		.GrayThing
		{
			background: #CCCCCC;
		}

	//-->
	</style>
</head>
<body onload=ira() BGCOLOR="FFFFFF" oncontextmenu = "return false" onselectstart = "return true" ondragstart = "return false">
<BODY TEXT="#000066">
<script type="text/javascript">
<!--
	function enter()
	{
		document.forms.inventario.submit();
	}
	function teclado()
	{
		if ((event.keyCode < 48 || event.keyCode > 57  || event.keyCode == 13) & event.keyCode != 46) event.returnValue = false;
	}
	function teclado1()
	{
		if ((event.keyCode < 48 || event.keyCode > 57 ) & event.keyCode != 46 & event.keyCode != 13)  event.returnValue = false;
	}
	function teclado2()
	{
		if ((event.keyCode < 48 || event.keyCode > 57 ) & (event.keyCode < 65 || event.keyCode > 90 ) & (event.keyCode < 97 || event.keyCode > 122 ) & event.keyCode != 13) event.returnValue = false;
	}
	function teclado3()
	{
		if ((event.keyCode < 48 || event.keyCode > 57 ) & (event.keyCode < 65 || event.keyCode > 90 ) & (event.keyCode < 97 || event.keyCode > 122 ) & event.keyCode != 13 & event.keyCode != 45) event.returnValue = false;
	}
	function ejecutar(pro,path)
	{
		if(pro == 1){
			window.open(path,'','fullscreen=1,status=0,menubar=0,scrollbars=1,toolbar =0,directories =0,resizable=0');}
		if(pro == 2){
			window.open(path,'','fullscreen=1,status=0,menubar=0,scrollbars=1,toolbar =0,directories =0,resizable=0');}
		if(pro == 3){
			window.open(path,'','fullscreen=1,status=0,menubar=0,scrollbars=1,toolbar =0,directories =0,resizable=0');}
		if(pro == 4){
			window.open(path,'','fullscreen=1,status=0,menubar=0,scrollbars=1,toolbar =0,directories =0,resizable=0');}
		if(pro == 5){
			window.open(path,'','fullscreen=1,status=0,menubar=0,scrollbars=1,toolbar =0,directories =0,resizable=0');}
		if(pro == 6){
			window.open(path,'','fullscreen=1,status=0,menubar=0,scrollbars=1,toolbar =0,directories =0,resizable=0');}
		if(pro == 7){
			window.open(path,'','fullscreen=1,status=0,menubar=0,scrollbars=1,toolbar =0,directories =0,resizable=0');}
		if(pro == 8){
			window.open(path,'','fullscreen=1,status=0,menubar=0,scrollbars=1,toolbar =0,directories =0,resizable=0');}
	}
//-->
</script>
<?php
include_once("conex.php");
/**********************************************************************************************************************
	   PROGRAMA : inventario.php
	   Fecha de Liberacion : 28/07/2005
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2008-10-20

	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface grafica que permite grabar el movimiento de inventarios de un almacen de
	   cadena, a traves de conceptos debidamente parametrizados que pueden ser definidos por el usuario segun sea su necesidad.


	   REGISTRO DE MODIFICACIONES :
	   .2008-10-20
	   		**Se corrigio el programa para mostrar correctamente la primera linea  de los articulos seleccionados cuando no tienen fecha de vencimiento
	   		y numero de lote ya que estaba ingresando la posicion 4 del arreglo de articulos (Valor Total) y no la 5 (Valor Unitario).

	   		**Se adiciono la utilidad de que cuando un articulo de la lista ya existe se posiciona en ella para modificarlo y asi hacer mas rapida
	   		la digitacion de ciertos conceptos tales como la entrada de mercancias.

	   .2008-10-08
	   		Se corrigio el programa para los conceptos en los que se mueve cantidad y no se mueve costo, ya que el comportamiento era incorrecto.
	   		Se corrigio en el programa la presentacion de las existencias x centro de costos ya que estaba mostrandolas de forma erronea.

	   .2008-09-22
	   		Se agrega al programa la funcion para anulacion de documentos que no afecten costo y cantidad, si un documento ya fue utilizado
	   		como documento anexo no se puede anular. En las ordenes de compras se pueden concatenar documentos siempre que estos no esten
	   		anulados.
	   		Se modifico el programa para que en lugar de pedir el valor total pida le valor unitario y calcule el valor total.
	   		Se adiciono la columna de unidad de medida.
	   		Se modificaron algunos querys que estaban haciendo acceso a datos tipo caracter como si fueran numericos.
	   		Se adiciono el campo de observaciones por movimiento que permite registar explicaciones que soporten la grabacion del movimiento
	   		si aplican.
	   		Se modifico el programa para que el detalle de los documentos puede mostrar valores negativos como los que maneja el concepto 202.

	   .2008-02-29
	   		Se agrega al programa la funcion para la validacion de año bisiesto que no se habia incluido en el codigo.

	   .2007-04-18
	   		Se modifica el programa para mostrar un mensaje de advertencia cuando el movimiento genere en el centro de costos al que afecte
	   		un costo promedio desfasado un 10% por encima o por debajo del costo anterior. Sin embargo el movimiento se graba en la base de
	   		datos.

	   .2007-04-11
	   		Se modifica el programa para excluir del movimiento de inventarios los articulos que estan inactivos.

	   .2007-03-30
	   		Se modifica el programa para desplegar 6 digitos decimales en el calculo del ajuste cuando las salidas quedan en cantidad cero (0)
	   		y dejan valor, se requiere un ajuste para que su valor llegue a cero siempre que el valor sea mayor a 0.00009 en case contrario el
	   		ajuste es realizado.

	   .2007-03-05
	   		Se modifica el programa para grabar en el detalle de movimiento de la tabla 11 el valor total + el valor iva si las condiciones de
	   		operacion indican que el IVA debe ser tenido en cuenta como un mayor valor. Este proceso solo opera para los concentos de entrada o
	   		de indicador 1. Lo anterior NO habia sido tenido en cuenta para articulos que no tuvieran  registro en el kardex que se encontraban
	   		ignorando este proceso.

	   .2007-02-27
	   		Se modifica el programa para VALIDAR que los conceptos de ENTRADA cuyo valor digitado este en "off" es decir los articulos entren al
	   		promedio tengan el Kardex en el centro de costo origen especificado un valor promedio superior a cero.
	   		En caso contrario el programa marca error y sugiere  un ajuste en VALOR previo.

	   .2007-02-20
	   		Se modifica el programa para grabar en el detalle de movimiento de la tabla 11 el valor total + el valor iva si las condiciones de
	   		operacion indican que el IVA debe ser tenido en cuenta como un mayor valor. Este proceso solo opera para los concentos de entrada o
	   		de indicador 1.

	   .2007-01-18
	   		Se modifico la funcion de validacion de movimiento, adicionando las siguientes comprobaciones:
	   			1. Se verifica si hay un movimiento de salida con valor digitado cuyas existencias son cero. Este
	   			   movimiento requiere de un ajuste previo igual a (existencias * promedio) - valot total del articulo.
	   			   Este caso se presenta cuando se hacen devoluciones al proveedor y las existencias quedan en cero.
	   			2. Se verifica que No se generen negativos en existencia y cantidad antes de emprender la grabacion.
	   			   Este proceso se realizaba durante la grabacion generando el documento con algunos articulos.
	   			3. Se verifica la existencia de informacion en el Kardex para el articulo y centro de costos.
	   		Las anteriores validaciones aplican para los movimientos de salida de inventarios.

	   .2006-11-23
	   		Se adiciono al archivo 000008 de conceptos de inventario el campo Coniva que indica si al costo de la mercancia se adiciona el valor del IVA.
	   		Los posibles Valores de este campo son off No incluye IVA / on Si incluye IVA.
	   		El calculo del costo promedio incluye el IVA siempre y cuando las variables Coniva y Ccoiva esten ambas en on.

	   .2006-11-20
	   		Se adiciono al archivo 000003 de centros de costos el campo Ccoiva que indica si al costo de la mercancia se adiciona el valor del IVA.
	   		Los posibles Valores de este campo son off No incluye IVA / on Si incluye IVA.

	   .2006-07-05
	   		Release de Version 2.06
	   		Se modifico programa para mostrar en el detalle del documento las existencias actuales en el centro de costos origen del articulo
	   		que se esta digitando. Esta columna muestra el mensaje "N/A" (No Aplica) cuando el documento es consultado ya que estas existencias
	   		no tienen utilidad despues de grabado el documento.

	   .2005-11-08
	   		Release de Version 2.05
	   		Se modifico en el programa el llamado al include del cierre para generalizarlo x empresa en el subdirectorio POS.

	   .2005-10-29
	   		Release de Version 2.04
	   		Se modifico en el programa para que la seleccion de centros de costos origen y destino, fuera controlada x usuario.
	   		Igualmente se modifico la condicion de aparicion de la barra de procesos para todas las operaciones diferentes a consulta ok=3, esto implica
	   		que si se consulta un documento y se cambia la operacion a proceso, aparece la barra de procesos.

	   .2005-10-27
	   		Release de Version 2.03
	   		Se modifico en el programa la entrada de informacion de los articulos con fecha de vencimiento y numero de lote que estaba desplazada.

	    .2005-10-13
	   		Release de Version 2.02
	   		Se modifico en el programa el hipervinculo de la opcion imprimir documento para hacerla dinamica x empresa.

	   .2005-10-12
	   		Release de Version 2.01
	   		Se modifico el programa para incluir la variable Empresa como global en las subrutinas que escriben en la base de datos.

	   .2005-10-06
	   		Release de Version 2.00
	   		Se modifico el programa de la version *** 1.26 a la 2.00 *** .
	   		Se cambio el llamado de las tablas de la base de datos para generalizar el programa, de tal forma que pueda ser utilizado por cualquier empresa.
	   		Se cambio tambien el llamado en el panel de procesos para obtener la misma generalizacion a traves de JavaScript.
	   		La nueva ubicacion del programa sera en el directorio /matrix/pos/procesos/inventario.php.
	   		Se cambia el Powred by a MATRIX.

	   .2005-08-24
			Release de Version 1.26
			Se cre� el checkbox de marcaci�n de todos los art�culos como ayuda a movimientos de devoluci�n de mercanc�a (101) en donde solamente
			unos pocos art�culos de devuelven en entradas de mercanc�a con detalles de muchos art�culos.
			Se cambia color de la version.
			Se cambia el Powred by a Pedro Ortiz Tamayo.

		.2005-08-11
			Release de Versi�n 1.25
			Se modifico la validacion de la multiplicacion del valor unitario por la cantidad validando que la cantidad sea diferente de cero

		.2005-08-10
			Release de Versi�n 1.24
			Se le agrego al programa la validacion de tarifas do convenio x articulo para conceptos que no afecten cantidad o costo en le kardex
			(Ordenes de Compra)

		.2005-08-09
			Release de Versi�n 1.22  1.23
			Se modific� el mensaje 15 de la diferencia entre los valores promedio del documento actual y el documento anexo agregandole el codigo del
			articulo que presenta este error.

		.2005-08-03
			Release de Versi�n 1.21
			Se modific� el mensaje 26 de los  negativos, indicando el valor  negativo generado en existencias o en valor promedio.

		.2005-07-28
			Release de Versi�n 1.21
			Se cre� en el �rea de JavaScript la funci�n teclado3() para que permitiera la entrada del ascii 45 equivalente al caracter "-" necesario en las
			fechas de vencimiento.
			Se verifica la existencia de la variable $estado que verifica la entrada ilegal al programa.
			Sino existe implica el probable cierre de la seci�n x inactividad por lo que se reinicializa.

	   .2005-04-01
	   		Release de Versi�n Beta.

***********************************************************************************************************************/
function bisiesto($year)
{
	//si es multiplo de 4 y no es multiplo de 100 o es multiplo de 400*/
	return(($year % 4 == 0 and $year % 100 != 0) or $year % 400 == 0);
}

function validar1($chain)
{
	// Funcion que permite validar la estructura de un numero Real
	$decimal ="^(\+|-)?([[:digit:]]+)\.([[:digit:]]+)$";
	// if (ereg($decimal,$chain,$occur))
	if (preg_match('/^(\+|-)?([[:digit:]]+)\.([[:digit:]]+)$/',$chain,$occur))
		if(substr($occur[2],0,1)==0 and strlen($occur[2])!=1)
			return false;
		else
			return true;
	else
		return false;
}
function validar2($chain)
{
	// Funcion que permite validar la estructura de un numero Entero
	$regular="^(\+|-)?([[:digit:]]+)$";
	// if (ereg($regular,$chain,$occur))
	if (preg_match('/^(\+|-)?([[:digit:]]+)$/',$chain,$occur))
		if(substr($occur[2],0,1)==0 and strlen($occur[2])!=1)
			return false;
		else
			return true;
	else
		return false;
}
function validar3($chain)
{
	// Funcion que permite validar la estructura de una fecha
	$fecha="^([[:digit:]]{4})-([[:digit:]]{1,2})-([[:digit:]]{1,2})$";
	// if(ereg($fecha,$chain,$occur))
	if (preg_match('/^([[:digit:]]{4})-([[:digit:]]{1,2})-([[:digit:]]{1,2})$/',$chain,$occur))
	{
		if($occur[2] < 0 or $occur[2] > 12)
			return false;
		if(($occur[3] < 0 or $occur[3] > 31) or
		  ($occur[2] == 4 and  $occur[3] > 30) or
		  ($occur[2] == 6 and  $occur[3] > 30) or
		  ($occur[2] == 9 and  $occur[3] > 30) or
		  ($occur[2] == 11 and $occur[3] > 30) or
		  ($occur[2] == 2 and  $occur[3] > 29 and bisiesto($occur[1])) or
		  ($occur[2] == 2 and  $occur[3] > 28 and !bisiesto($occur[1])))
			return false;
		return true;
	}
	else
		return false;
}
function validar4($chain)
{
	// Funcion que permite validar la estructura de un dato alfanumerico
	$regular="^([=a-zA-Z0-9' '��@/#-.;_<>])+$";
	
	// return (ereg($regular,$chain));
	return (preg_match('/^([=a-zA-Z0-9\' \'@\/#-.;_<>])+$/',$chain,$occur));
}
function validar5($chain)
{
	// Funcion que permite validar la estructura de un dato numerico
	$regular="^([0-9:])+$";
	// return (ereg($regular,$chain));
	return (preg_match('/^([0-9:])+$/',$chain,$occur));
}
function valgen($j,$ano,$mes,$doc,$con,$fecha,$ccoo,$ccod,$dant,$ret,$prov,$datos,$status,$conex,$fac,&$werr,&$e)
{
	global $empresa;
	// Funcion de validacion de datos previos a la grabacion
	if($con == "0")
	{
		// No selecciono el concepto
		$e=$e+1;
		$werr[$e][0]="NO ESCOGIO EL CONCEPTO";
		$werr[$e][1]=2;
		$werr[$e][2]=1;
	}
	else
	{
		// si el usuario escogio el concepto, lee de la tabla de conceptos sus caracteristicas:
		//ccd(Centro de Costos Destino)
		//dan(Documento Anexo)
		//pro(Proveedor)
		//can(Concepto Anexo)
		//cao(Concepto Anexo Obligatorio)
		//vdi(Valor Digitado x Articulo)
		//aca(El Concepto Afecta Cantidad)
		//aco(El Concepto Afecta Costo)
		//dfa(Digita Documento Soporte)
		$query = "select Conccd, Condan, Conpro, Concan, Concao,Convdi, Conaca, Conaco, Condfa, Conind from ".$empresa."_000008 where Concod='".$con."'";
		$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
		$row = mysql_fetch_array($err);
		// ***** Adicion 2007-01-18 *****
		$tip = $row[9];
		$aca = $row[6];
		$aco = $row[7];
		$vdi = $row[5];
		// *****
	}
	if($doc != "" and ((isset($row[6]) and $row[6] == "on") or (isset($row[7]) and $row[7] == "on")))
	{
		//Verifica si el documento ya existe y afecta costo o cantidad, lo que implica que no es modificable
		$e=$e+1;
		$werr[$e][0]="ESTE DOCUMENTO YA EXISTE, NO LO PUEDE GRABAR NUEVAMENTE";
		$werr[$e][1]=2;
		$werr[$e][2]=2;
	}
	if($status != 1)
	{
		//Verifica se entro al programa directamente sin pasa x el login de MATRIX
		$e=$e+1;
		$werr[$e][0]="ENTRO DE FORMA ILEGAL AL PROGRAMA";
		$werr[$e][1]=2;
		$werr[$e][2]=3;
	}
	if($ccoo == "0")
	{
		$e=$e+1;
		$werr[$e][0]="NO ESCOGIO EL CENTRO DE COSTOS ORIGEN";
		$werr[$e][1]=1;
		$werr[$e][2]=4;
	}
	if($ccod == "0"  and isset($row[0]) and $row[0] == "on")
	{
		$e=$e+1;
		$werr[$e][0]="NO ESCOGIO EL CENTRO DE COSTOS DESTINO Y ES REQUERIDO";
		$werr[$e][1]=1;
		$werr[$e][2]=5;
	}
	if((isset($row[4]) and $row[4] == "on") and ($dant == ""  or strpos($dant,":") !== false))
	{
			$e=$e+1;
			$werr[$e][0]="NO DIGITO EL DOCUMENTO ANEXO Y ES OBLIGATORIO O SU DIGITACION ES INCORRECTA";
			$werr[$e][1]=1;
			$werr[$e][2]=6;
	}
	if($fac == ""  and isset($row[8]) and $row[8] == "on")
	{
		$e=$e+1;
		$werr[$e][0]="NO DIGITO LA DOC. SOPORTE Y ES REQUERIDA ";
		$werr[$e][1]=1;
		$werr[$e][2]=7;
	}
	if($prov == "0"  and isset($row[2]) and $row[2] == "on")
	{
		$e=$e+1;
		$werr[$e][0]="NO ESCOGIO EL PROVEEDOR Y ES REQUERIDO";
		$werr[$e][1]=1;
		$werr[$e][2]=8;
	}
	if(strpos($ret,".") === false)
		if(strlen($ret) == 0)
			$ret=$ret."0.0";
		else
			$ret=$ret.".0";
	$wanoval=date("Y");
	if(!validar2($ano) or $ano != $wanoval)
	{
		$e=$e+1;
		$werr[$e][0]="EL A&NtildeO ESTA INCORRECTO";
		$werr[$e][1]=1;
		$werr[$e][2]=9;
	}
	$wmesval=date("m");
	if(!validar2($mes) or $mes != $wmesval)
	{
		$e=$e+1;
		$werr[$e][0]="EL MES ESTA INCORRECTO";
		$werr[$e][1]=1;
		$werr[$e][2]=10;
	}
	if(!validar1($ret))
	{
		$e=$e+1;
		$werr[$e][0]="LA RETENCI&Oacute;N ESTA INCORRECTA";
		$werr[$e][1]=1;
		$werr[$e][2]=11;
	}
	if(!validar3($fecha) or $fecha != date("Y-m-d"))
	{
		$e=$e+1;
		$werr[$e][0]="LA FECHA ESTA INCORRECTA";
		$werr[$e][1]=1;
		$werr[$e][2]=12;
	}
	if($j < 0)
	{
		$e=$e+1;
		$werr[$e][0]="NO HA DIGITADO EL DETALLE DEL DOCUMENTO";
		$werr[$e][1]=1;
		$werr[$e][2]=13;
	}
	for ($i=0;$i<=$j;$i++)
	{
		if($con != "0"  and $row[1] == "on" and $row[4] == "on" and $dant != "")
		{
			if(strpos($dant,":") === false)
			{
				$query = "SELECT Mdecan, Mencco, Mennit, Mdevto  from ".$empresa."_000010,".$empresa."_000011 ";
				$query = $query."  where Mendoc = '".$dant."'";
				$query = $query."      and  Mencon ='".$row[3]."'";
				$query = $query."      and  Mencon = Mdecon ";
				$query = $query."      and  Mendoc = Mdedoc ";
				$query = $query."      and  Mdeart ='".$datos[$i][0]."'";
				$err1 = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
				$num1 = mysql_num_rows($err1);
				if($num1 == 1)
				{
					$row1 = mysql_fetch_array($err1);
					$wcanF=$row1[0];
					$wccoa=$row1[1];
					$wnita=$row1[2];
					$wvalt=$row1[3];
					if($row[4] == "on")
					{
						$query = "SELECT sum(Mdecan)  from ".$empresa."_000010,".$empresa."_000011 ";
						$query = $query."  where Mendan ='".$dant."'";
						$query = $query."      and  Mencon ='".$con."'";
						$query = $query."      and  Mencon = Mdecon ";
						$query = $query."      and  Mendoc = Mdedoc ";
						$query = $query."      and  Mdeart ='".$datos[$i][0]."'";
						$err1 = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
						$row1 = mysql_fetch_array($err1);
						$wcanO=$row1[0]+$datos[$i][2];
						if($wcanO > $wcanF)
						{
							$wfal= $wcanF-$row1[0];
							$e=$e+1;
							$werr[$e][0]="LA CANTIDAD EN DOCUMENTO ANEXO ES SUPERADA X LOS DOCUMENTOS EN CONCEPTO ".$con."  EN ARTICULO : ".$datos[$i][0]."  DIFERENCIA : ".$wfal;
							$werr[$e][1]=2;
							$werr[$e][2]=14;
						}
					}
					$wproa= $wvalt /$wcanF;
					$wproa=number_format((double)$wproa,4,'.','');
					if($wproa != number_format((double)$datos[$i][5],4,'.','') and $row[5] == "on")
					{
						$e=$e+1;
						$werr[$e][0]="ELVALOR PROMEDIO DEL DOCUMENTO ANEXO ES DIFERENTE DEL DIGITADO : ".$wproa." EN ARTICULO : ".$datos[$i][0]." ";
						$werr[$e][1]=2;
						$werr[$e][2]=15;
					}
					if(($wccoa != $ccoo or ($wnita != $prov and $row[2] == "on")) and $row[4] == "on")
					{
						$e=$e+1;
						$werr[$e][0]="EL CENTRO DE COSTOS O EL PROVEEDOR SON DIFERENTES A LOS DEL DOCUMENTO ANEXO CON PROVEEDOR : ".$wnita."   C.C. ORIGEN : ".$wccoa;
						$werr[$e][1]=1;
						$werr[$e][2]=16;
					}
				}
				else
				{
					$e=$e+1;
					$werr[$e][0]="NO EXISTE EL DOCUMENTO ANEXO ".$dant." PARA EL CONCEPTO : ".$row[3]." Y EL ARTICULO : ".$datos[$i][0]." ";
					$werr[$e][1]=2;
					$werr[$e][2]=17;
				}
			}
			else
			{
				$e=$e+1;
				$werr[$e][0]="EL DOCUMENTO ANEXO MULTIPLE NO PUEDE SER USADO CUANDO ES OBLIGATORIO ";
				$werr[$e][1]=2;
				$werr[$e][2]=18;
			}
		}
		else
		{
			if((isset($row[4]) and $row[4] == "on") and ($dant == ""  or strpos($dant,":") !== false))
			{
				$e=$e+1;
				$werr[$e][0]="NO DIGITO EL DOCUMENTO ANEXO  O TIENE ERRORES ".$dant." PARA EL CONCEPTO : ".$row[3]." Y EL ARTICULO : ".$datos[$i][0]."";
				$werr[$e][1]=2;
				$werr[$e][2]=19;
			}
		}
		if(isset($row[8]) and $row[8] == "on" and $fac != "")
		{
			$query = "SELECT Mendoc  from ".$empresa."_000010,".$empresa."_000011 ";
			$query = $query."  where Menfac ='".$fac."'";
			$query = $query."      and  Mencon ='".$con."'";
			$query = $query."      and  Mencon = Mdecon ";
			$query = $query."      and  Mendoc = Mdedoc ";
			$query = $query."      and  Mdeart ='".$datos[$i][0]."'";
			$err1 = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
			$num1 = mysql_num_rows($err1);
			if($num1 > 0)
			{
				$row1 = mysql_fetch_array($err1);
				$e=$e+1;
				$werr[$e][0]="EL ARTICULO : ".$datos[$i][0]." YA INGRESO CON LA MISMA DOC. SOPORTE : ".$fac."  -- EN EL DOCUMENTO : ".$row1[0]." CON EL CONCEPTO : ".$con;
				$werr[$e][1]=2;
				$werr[$e][2]=20;
			}
		}
		if(strpos($datos[$i][2],".") === false)
			$datos[$i][2]=$datos[$i][2].".0";
		if(strpos($datos[$i][3],".") === false)
			$datos[$i][3]=$datos[$i][3].".0";
		if(strpos($datos[$i][4],".") === false)
			$datos[$i][4]=$datos[$i][4].".0";
		if(!validar1($datos[$i][2]))
		{
			$e=$e+1;
			$werr[$e][0]="LA CANTIDAD ESTA INCORRECTA EN ARTICULO : ".$datos[$i][0];
			$werr[$e][1]=1;
			$werr[$e][2]=21;
		}
		if(!validar1($datos[$i][3]))
		{
			$e=$e+1;
			$werr[$e][0]="EL % DE IVA ESTA INCORRECTO EN ARTICULO : ".$datos[$i][0];
			$werr[$e][1]=1;
			$werr[$e][2]=22;
		}
		if(!validar1($datos[$i][4]))
		{
			$e=$e+1;
			$werr[$e][0]="EL VALOR TOTAL ESTA INCORRECTO EN ARTICULO : ".$datos[$i][0];
			$werr[$e][1]=1;
			$werr[$e][2]=23;
		}

		$query = "select Artfvn  from ".$empresa."_000001 where Artcod='".$datos[$i][0]."'";
		$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
		$row2 = mysql_fetch_array($err);
		if(!validar3($datos[$i][7]) and $row2[0] == "on")
		{
			$e=$e+1;
			$werr[$e][0]="LA FECHA ESTA INCORRECTA EN ARTICULO : ".$datos[$i][0]." Y ES REQUERIDA";
			$werr[$e][1]=1;
			$werr[$e][2]=24;
		}
		if(!validar4($datos[$i][8]) and $row2[0] == "on")
		{
			$e=$e+1;
			$werr[$e][0]="EL NRO DE LOTE ESTA INCORRECTO EN ARTICULO : ".$datos[$i][0]." Y ES REQUERIDO";
			$werr[$e][1]=1;
			$werr[$e][2]=25;
		}
		// ***** Adicion 2007-01-18 ****
		if($con != "0" and ($tip == "-1" or $tip == "0"))
		{
			$query = "lock table ".$empresa."_000007 LOW_PRIORITY WRITE ";
			$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO KARDEX ");
			$query = "select Karexi, Karpro, Karvuc, Karfuc from  ".$empresa."_000007 where Karcod='".$datos[$i][0]."' and Karcco='".$ccoo."'";
			$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO KARDEX ");
			$num1 = mysql_num_rows($err1);
			if($num1 > 0)
			{
				$row1 = mysql_fetch_array($err1);
				$exi=$row1[0];
				$tot=$row1[1] * $exi;
				$proA=$tot;
				if($aca == "on")
				{
					$exi=$exi - $datos[$i][2];
					$pro=$row1[1];
				}
				if($aco == "on")
				{
					if($vdi == "on")
					{
						$tot= $tot - $datos[$i][4];
					}
					else
						$tot=$tot - ($row1[1] * $datos[$i][2]);
					if($exi != 0)
						$pro= $tot / $exi ;
					else
					{
						$pro= $tot;
						if($pro == 0)
							$pro= $row1[1];
					}
				}
				if($exi >= 0 and $pro >= 0)
				{
					if($exi == 0 and $vdi == "on" and $tot > 0.00009)
					{
						$e=$e+1;
						$werr[$e][0]="ERROR EL MOVIMIENTO DEL ARTICULO  : ".$datos[$i][0]." GENERA SALIDAS CON EXISTENCIAS EN CERO (0) Y VALOR SIN AJUSTE  ---  DIGITE EL AJUSTE PRIMERO POR : $".number_format((double)$tot,12,'.','')." *** VALIDACION";
						$werr[$e][1]=2;
						$werr[$e][2]=30;
					}
				}
				else
				{
					$e=$e+1;
					$werr[$e][0]="ERROR EL MOVIMIENTO DEL ARTICULO  : ".$datos[$i][0]." GENERA NEGATIVOS EN CANTIDAD O VALOR ---- EXISTENCIAS NEGATIVAS GENERADAS : ".$exi."  PROMEDIO NEGATIVO GENERADO : ".$pro." *** VALIDACION ";
					$werr[$e][1]=2;
					$werr[$e][2]=26;
				}
			}
			else
			{
				$e=$e+1;
				$werr[$e][0]="ERROR EL ARTICULO  : ".$datos[$i][0]." NO TIENE REGISTROS EN EL CENTRO DE COSTOS : ".$ccoo." NO SE PUEDE REALIZAR EL MOVIMIENTO *** VALIDACION";
				$werr[$e][1]=2;
				$werr[$e][2]=31;
			}
			$query = "UNLOCK TABLES";
			$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO KARDEX Y MAESTRO");
		}
		// *****
		// ***** Adicion 2007-02-27 ****
		if($con != "0" and $tip == "1" AND $vdi == "off")
		{
			$query = "lock table ".$empresa."_000007 LOW_PRIORITY WRITE ";
			$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO KARDEX ");
			$query = "select Karpro from  ".$empresa."_000007 where Karcod='".$datos[$i][0]."' and Karcco='".$ccoo."'";
			$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO KARDEX ");
			$num1 = mysql_num_rows($err1);
			if($num1 > 0)
			{
				$row1 = mysql_fetch_array($err1);
				$pro=$row1[0];
			}
			else
				$pro=0;
			if($pro == 0)
			{
				$e=$e+1;
				$werr[$e][0]="ERROR EL ARTICULO  : ".$datos[$i][0]." EN EL CENTRO DE COSTOS : ".$ccoo." NO TIENE COSTO PROMEDIO O ES CERO, REALICE UN AJUSTE EN VALOR PREVIO A ESTA TRANSACCION *** VALIDACION";
				$werr[$e][1]=2;
				$werr[$e][2]=32;
			}
			$query = "UNLOCK TABLES";
			$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO KARDEX Y MAESTRO");
		}
		// *****
	}
	if($e == -1)
		return true;
	else
		return false;
}
function agrupar($j,&$data)
{
	$w=-1;
	for ($i=0;$i<=$j;$i++)
	{
		$wfind=-1;
		for ($k1=0;$k1<=$w;$k1++)
			if($data[$k1][0] == $data[$i][0])
				$wfind=$k1;
		if($wfind == -1)
		{
			$w=$w+1;
			$wartant = $data[$i][0];
			$data[$w][0]=$data[$i][0];
			$data[$w][1]=$data[$i][1];
			$data[$w][2]=$data[$i][2];
			$data[$w][3]=$data[$i][3];
			$data[$w][4]=$data[$i][4];
			if($data[$i][2] != 0)
				$data[$w][5]=$data[$i][4] / $data[$i][2];
			else
				$data[$w][5]=$data[$i][4];
			$data[$w][6]=($data[$i][3] / 100) * $data[$i][4];
			$data[$w][7]=$data[$i][7];
			$data[$w][8]=$data[$i][8];
			$data[$w][9]=$data[$i][9];
			$data[$w][10]=$data[$i][10];
		}
		else
		{
			$data[$wfind][2] += $data[$i][2];
			$data[$wfind][4] += $data[$i][4];
		}
	}
	return $w;
}
function actualizar_kardex($ind,$vdi,$auc,$aca,$aco,$key,$conex,$i,$data,$wccoo,$wccod,$wcont,$wdoct,&$werr,&$e,&$wrg,&$wiva,&$warning,&$wa)
{
	global $empresa;
	switch ($ind)
	{
		case "1":
			$query = "lock table ".$empresa."_000007 LOW_PRIORITY WRITE, ".$empresa."_000011 LOW_PRIORITY WRITE, ".$empresa."_000003 LOW_PRIORITY WRITE, ".$empresa."_000008 LOW_PRIORITY WRITE";
			$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO KARDEX Y DETALLE DE MOVIMIENTO");
			$query = "select Karexi, Karpro, Karvuc, Karfuc from  ".$empresa."_000007 where Karcod='".$data[$i][0]."' and Karcco='".$wccoo."'";
			$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO KARDEX ");
			$num1 = mysql_num_rows($err1);
			if($num1 > 0)
			{
				$row1 = mysql_fetch_array($err1);
				$exi=$row1[0];
				$tot=$row1[1] * $exi;
				// ***** Adicion 2007-04-18 *****
				$proa=$row1[1];
				//******
				if($aca == "on")
				{
					$exi=$exi + $data[$i][2];
					$pro=$row1[1];
				}
				if($aco == "on")
				{
					if($vdi == "on")
					{
						//La variable $wccoo contiene el centro de costos origen y la variable $wcont contiene el concepto de inventario
						$query = "select Ccoiva from  ".$empresa."_000003 where Ccocod='".$wccoo."' ";
						$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO CENTROS DE COSTO: ".mysql_errno().":".mysql_error());
						$row1 = mysql_fetch_array($err1);
						$query = "select Coniva from  ".$empresa."_000008 where Concod='".$wcont."' ";
						$err2 = mysql_query($query,$conex) or die("ERROR CONSULTANDO MAESTRO DE CONCEPTOS: ".mysql_errno().":".mysql_error());
						$row2 = mysql_fetch_array($err2);
						if($row1[0] == "on" and $row2[0] == "on" )
						{
							//La variable $data[$i][6] contiene el valor del IVA
							$tot=$tot + $data[$i][4] + $data[$i][6];
							$wiva="on";
						}
						else
							$tot=$tot + $data[$i][4];
					}
					else
					{
						$query = "select Karexi, Karpro, Karvuc, Karfuc from  ".$empresa."_000007 where Karcod='".$data[$i][0]."' and Karcco='".$wccod."'";
						$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO KARDEX ");
						$num1 = mysql_num_rows($err1);
						$row1 = mysql_fetch_array($err1);
						$tot=$tot + ($row1[1] * $data[$i][2]);
					}
					if($exi != 0)
						$pro= $tot / $exi ;
					else
						$pro= $tot;
					// ***** Adicion 2007-04-18 *****
					if($proa > 0)
						$calc=abs((($pro / $proa) - 1)* 100);
					else
						$calc=0;
					if($calc > 10)
					{
						$wa=$wa+1;
						$warning[$wa]="EL ARTICULO  : ".$data[$i][0]." EN EL CENTRO DE COSTOS : ".$wccoo." GENERO UN COSTO PROMEDIO : <FONT COLOR=#990000>".number_format((double)$pro,4,'.','')."</FONT> DESFASADO<FONT SIZE=6> 10% </FONT>DEL ANTERIOR : <FONT COLOR=#990000>".number_format((double)$proa,4,'.','')."</FONT> REVISE!!!";
					}
					//*****
				}
				if($auc == "on")
				{
					$fecha = date("Y-m-d");
					$valuc=$data[$i][4] / $data[$i][2];
					$query =  " update ".$empresa."_000007 set Karexi = ".number_format((double)$exi,4,'.','').",Karpro=".number_format((double)$pro,4,'.','').",Karvuc=".number_format((double)$valuc,2,'.','').",Karfuc='".$fecha."'  where Karcod='".$data[$i][0]."' and Karcco='".$wccoo."'";
				}
				else
					$query =  " update ".$empresa."_000007 set Karexi = ".number_format((double)$exi,4,'.','').",Karpro=".number_format((double)$pro,4,'.','')."  where Karcod='".$data[$i][0]."' and Karcco='".$wccoo."'";
				$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO KARDEX");
				$wrg++;
			}
			else
			{
				$exi=0;
				$tot=0;
				if($aca == "on")
				{
					$exi= $data[$i][2];
					$pro=0;
				}
				if($aco == "on")
				{
					if($vdi == "on")
					{
						//Si el articulo NO tiene registro para ese centro de costos - articulo la validacion del IVA que no existia
						//se realiza en esta parte del codigo.
						//La variable $wccoo contiene el centro de costos origen y la variable $wcont contiene el concepto de inventario
						$query = "select Ccoiva from  ".$empresa."_000003 where Ccocod='".$wccoo."' ";
						$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO CENTROS DE COSTO: ".mysql_errno().":".mysql_error());
						$row1 = mysql_fetch_array($err1);
						$query = "select Coniva from  ".$empresa."_000008 where Concod='".$wcont."' ";
						$err2 = mysql_query($query,$conex) or die("ERROR CONSULTANDO MAESTRO DE CONCEPTOS: ".mysql_errno().":".mysql_error());
						$row2 = mysql_fetch_array($err2);
						if($row1[0] == "on" and $row2[0] == "on" )
						{
							//La variable $data[$i][6] contiene el valor del IVA
							$tot=$data[$i][4] + $data[$i][6];
							$wiva="on";
						}
						else
							$tot=$data[$i][4];
					}
					else
					{
						$query = "select Karexi, Karpro, Karvuc, Karfuc from  ".$empresa."_000007 where Karcod='".$data[$i][0]."' and Karcco='".$wccod."'";
						$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO KARDEX ");
						$num1 = mysql_num_rows($err1);
						$row1 = mysql_fetch_array($err1);
						$tot=($row1[1] * $data[$i][2]);
					}
					if($exi != 0)
						$pro= $tot / $exi ;
					else
						$pro= $tot;
				}
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
				if($auc == "on")
				{
					$valuc=$data[$i][4] / $data[$i][2];
					$query = "insert ".$empresa."_000007 (medico,fecha_data,hora_data, Karcod, Karcco, Karexi, Karpro, Karvuc, Karmax, Karmin, Karpor, Karfuc, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$data[$i][0]."','".$wccoo."',".number_format((double)$exi,4,'.','').",".number_format((double)$pro,4,'.','').",".number_format((double)$valuc,2,'.','').",0,0,0,'".$fecha."','C-".$empresa."')";
				}
				else
					$query = "insert ".$empresa."_000007 (medico,fecha_data,hora_data, Karcod, Karcco, Karexi, Karpro, Karvuc, Karmax, Karmin, Karpor, Karfuc, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$data[$i][0]."','".$wccoo."',".number_format((double)$exi,4,'.','').",".number_format((double)$pro,4,'.','').",0,0,0,0,'0000-00-00','C-".$empresa."')";
				$err1 = mysql_query($query,$conex) or die("ERROR INICIALIZANDO KARDEX : ".mysql_errno().":".mysql_error());
				$wrg++;
			}
			if(strpos($data[$i][2],".") === false)
				$data[$i][2]=$data[$i][2].".0";
			if(strpos($data[$i][3],".") === false)
				$data[$i][3]=$data[$i][3].".0";
			if(strpos($data[$i][4],".") === false)
				$data[$i][4]=$data[$i][4].".0";
			if($aca == "off")
				$data[$i][2]=0;
			if($aco == "off")
				$data[$i][4]=0;
			return true;
		break;
		case "-1":
			$query = "lock table ".$empresa."_000007 LOW_PRIORITY WRITE, ".$empresa."_000011 LOW_PRIORITY WRITE";
			$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO KARDEX Y DETALLE DE MOVIMIENTO");
			$query = "select Karexi, Karpro, Karvuc, Karfuc from  ".$empresa."_000007 where Karcod='".$data[$i][0]."' and Karcco='".$wccoo."'";
			$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO KARDEX ");
			$num1 = mysql_num_rows($err1);
			if($num1 > 0)
			{
				$row1 = mysql_fetch_array($err1);
				$exi=$row1[0];
				$tot=$row1[1] * $exi;
				// ***** Adicion 2007-04-18 *****
				$proa=$row1[1];
				//*****
				if($aca == "on")
				{
					$exi=$exi - $data[$i][2];
					$pro=$row1[1];
				}
				if($aco == "on")
				{

					if($vdi == "on")
						$tot= $tot - $data[$i][4];
					else
						$tot=$tot - ($row1[1] * $data[$i][2]);
					if($exi != 0)
						$pro= $tot / $exi ;
					else
					{
						$pro= $tot;
						if($pro == 0)
							$pro= $row1[1];
					}
					// ***** Adicion 2007-04-18 *****
					if($proa > 0)
						$calc=abs((($pro / $proa) - 1)* 100);
					else
						$calc=0;
					if($calc > 10)
					{
						$wa=$wa+1;
						$warning[$wa]="EL ARTICULO  : ".$data[$i][0]." EN EL CENTRO DE COSTOS : ".$wccoo." GENERO UN COSTO PROMEDIO : <FONT COLOR=#990000>".number_format((double)$pro,4,'.','')."</FONT> DESFASADO<FONT SIZE=6> 10% </FONT>DEL ANTERIOR : <FONT COLOR=#990000>".number_format((double)$proa,4,'.','')."</FONT> REVISE!!!";
					}
					//*****
				}
				if($exi >= 0 and $pro >= 0)
				{
					$query =  " update ".$empresa."_000007 set Karexi = ".number_format((double)$exi,4,'.','').",Karpro=".number_format((double)$pro,4,'.','')."  where Karcod='".$data[$i][0]."' and Karcco='".$wccoo."'";
					$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO KARDEX");
					$wrg++;
					if($aca == "off")
						$data[$i][2]=0;
					if($aco == "off")
						$data[$i][4]=0;
					return true;
				}
				else
				{
					$e=$e+1;
					$werr[$e][0]="ERROR NO SE GRABO EL MOVIMIENTO DEL ARTICULO  : ".$data[$i][0]." GENERA NEGATIVOS EN CANTIDAD O VALOR ---- EXISTENCIAS NEGATIVAS GENERADAS : ".$exi."  PROMEDIO NEGATIVO GENERADO : ".$pro." ";
					$werr[$e][1]=2;
					$werr[$e][2]=26;
					return false;
				}
			}
		break;
	}
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='inventario' action='Inventario.php' method=post>";




	include_once("pos/cierre.php");
	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if($wswscie == 0)
	{
		if(isset($ok) and $ok == 9)
		{
			//session_register("estado");
			$_SESSION['estado']="1";
			$estado='1';
			$ok=0;
		}
		if(isset($ok) and $ok == 2)
		{
			$werr=array();
			$e=-1;
			$warning=array();
			$wa=-1;
			if(isset($cantidad))
			{
				$data[$j][2] = $cantidad;
				$j=agrupar($j,$data);
			}
			if(!isset($data[0][0]))
			{
				$j=-1;
				$data=array();
			}
			if(!isset($estado))
				$estado="1";
			if(valgen($j,$wanot,$wmest,$wdoct,substr($wcont,0,strpos($wcont,"-")),$wfechat,substr($wccoo,0,strpos($wccoo,"-")),substr($wccod,0,strpos($wccod,"-")),$wdant,$wrett,substr($wprovt,0,strrpos($wprovt,"-")),$data,$estado,$conex,$wfact,$werr,$e))
			{

				if(strpos($wrett,".") === false)
					if(strlen($wrett) == 0)
						$wrett=$wrett."0.0";
					else
						$wrett=$wrett.".0";
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
				$query = "select Conind, Conaca, Conaco,Conauc,Convdi  from ".$empresa."_000008 where Concod='".substr($wcont,0,strpos($wcont,"-"))."'";
				$err1 = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
				$row1 = mysql_fetch_array($err1);
				$ind=$row1[0];
				$aca=$row1[1];
				$aco=$row1[2];
				$auc=$row1[3];
				$vdi=$row1[4];
				$query = "lock table ".$empresa."_000008 LOW_PRIORITY WRITE, ".$empresa."_000010 LOW_PRIORITY WRITE, ".$empresa."_000011 LOW_PRIORITY WRITE ";
				$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO CONSECUTIVO");
				$wupd = 0;
				if($aca == "off" and $aco == "off" and $wdoct != "")
				{
					$arrcon=array();
					$query = "select Concod from ".$empresa."_000008 where Concan='".substr($wcont,0,strpos($wcont,"-"))."'";
					$err1 = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
					$num1 = mysql_num_rows($err1);
					if ($num1 > 0)
					{
						for ($i=0;$i<$num1;$i++)
						{
							$row1 = mysql_fetch_array($err1);
							$arrcon[$i]=$row1[0];
						}
					}
					$query = "select count(*)  from ".$empresa."_000010  where Mendan ='".$wdoct."' and Mencon in('";
					for ($m=0;$m<sizeof($arrcon)-1;$m++)
						$query .=$arrcon[$m]."','";
					$query .=$arrcon[sizeof($arrcon)-1]."')";
					$err1 = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
					$row = mysql_fetch_array($err1);
					$totalanex=$row[0];
					$query = "select Menfac  from ".$empresa."_000010  where Mencon='".substr($wcont,0,strpos($wcont,"-"))."' and Mendoc ='".$wdoct."'";
					$err1 = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
					$row = mysql_fetch_array($err1);
					if(isset($null) or $row[0] == "NULO")
						if($totalanex == 0)
							$wfact="NULO";
						else
						{
							$e=$e+1;
							$werr[$e][0]="ERROR EL DOCUMENTO ANEXO YA FUE UTILIZADO COMO SOPORTE DE OTRO DOCUMENTO. NO SE PUEDE ANULAR!!!! ";
							$werr[$e][1]=2;
							$werr[$e][2]=31;
						}
					$wupd = 1;
					$query = "DELETE  from ".$empresa."_000010  where Mencon='".substr($wcont,0,strpos($wcont,"-"))."' and Mendoc ='".$wdoct."'";
					$err1 = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
					$query = "DELETE  from ".$empresa."_000011  where Mdecon='".substr($wcont,0,strpos($wcont,"-"))."' and Mdedoc ='".$wdoct."'";
					$err1 = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
				}
				else
				{
					$query = "select Concon from ".$empresa."_000008 where Concod='".substr($wcont,0,strpos($wcont,"-"))."'";
					$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO CONSECUTIVO");
					$row = mysql_fetch_array($err1);
					$wdoct=$row[0] + 1;
				}
				$query = "insert ".$empresa."_000010 (medico,fecha_data,hora_data, Menano, Menmes, Mendoc, Mencon, Menfec, Mencco, Menccd, Mendan, Menpre, Mennit,Menusu, Menfac, Menobs, Menest, seguridad) values ('".$empresa."','".$fecha."','".$hora."',".$wanot.",".$wmest.",".$wdoct.",'".substr($wcont,0,strpos($wcont,"-"))."','".$wfechat."','".substr($wccoo,0,strpos($wccoo,"-"))."','".substr($wccod,0,strpos($wccod,"-"))."','".$wdant."',".$wrett.",'".substr($wprovt,0,strrpos($wprovt,"-"))."','".$key."','".$wfact."','".$wobser."','on ','C-".$empresa."')";
				$err1 = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
				if ($err1 != 1)
				{
					$e=$e+1;
					$werr[$e][0]="ERROR EN GRABACION DEL ENCABEZADO DEL DOCUMENTO : ".mysql_errno().":".mysql_error();
					$werr[$e][1]=2;
					$werr[$e][2]=27;
					$query = " UNLOCK TABLES";
					$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO ENCABEZADO Y CONSECUTIVO");
				}
				else
				{
					$wdocg=$wdoct;
					$wcong=$wcont;
					echo "<input type='HIDDEN' name= 'wdocg' value='".$wdocg."'>";
					echo "<input type='HIDDEN' name= 'wcong' value='".$wcong."'>";
					if($wupd == 0)
					{
						$query =  " update ".$empresa."_000008 set Concon = Concon + 1 where Concod='".substr($wcont,0,strpos($wcont,"-"))."'";
						$err1 = mysql_query($query,$conex) or die("ERROR INCREMENTANDO CONSECUTIVO");
					}
					$query = " UNLOCK TABLES";
					$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO");
					$wrg=0;
					for ($i=0;$i<=$j;$i++)
					{
						$wswG=0;
						if($aca == "off" and $aco == "off")
						{
							$wswG=1;
							$ind=9;
						}
						$wiva="off";
						// ***** Modificacion 2007-04-18  se aumentaron los parametros &$warning,&$wa *****
						switch ($ind)
						{
							case "1":
								if(actualizar_kardex("1",$vdi,$auc,$aca,$aco,$key,$conex,$i,$data,substr($wccoo,0,strpos($wccoo,"-")),substr($wccod,0,strpos($wccod,"-")),substr($wcont,0,strpos($wcont,"-")),$wdoct,$werr,$e,$wrg,$wiva,$warning,$wa))
									$wswG=1;
							break;
							case "-1":
								if(actualizar_kardex("-1",$vdi,$auc,$aca,$aco,$key,$conex,$i,$data,substr($wccoo,0,strpos($wccoo,"-")),substr($wccod,0,strpos($wccod,"-")),substr($wcont,0,strpos($wcont,"-")),$wdoct,$werr,$e,$wrg,$wiva,$warning,$wa))
									$wswG=1;
							break;
							case "0":
								if(actualizar_kardex("-1",$vdi,$auc,$aca,$aco,$key,$conex,$i,$data,substr($wccoo,0,strpos($wccoo,"-")),substr($wccod,0,strpos($wccod,"-")),substr($wcont,0,strpos($wcont,"-")),$wdoct,$werr,$e,$wrg,$wiva,$warning,$wa))
									if(actualizar_kardex("1",$vdi,$auc,$aca,$aco,$key,$conex,$i,$data,substr($wccod,0,strpos($wccod,"-")),substr($wccoo,0,strpos($wccoo,"-")),substr($wcont,0,strpos($wcont,"-")),$wdoct,$werr,$e,$wrg,$wiva,$warning,$wa))
										$wswG=1;
							break;
						}
						//******
						if($wswG == 1)
						{
							$fecha = date("Y-m-d");
							$hora = (string)date("H:i:s");
							if($vdi == "off")
							{
								$query = "select Karpro from  ".$empresa."_000007 where Karcod='".$data[$i][0]."' and Karcco='".substr($wccoo,0,strpos($wccoo,"-"))."'";
								$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO KARDEX ");
								$num1 = mysql_num_rows($err1);
								$row1 = mysql_fetch_array($err1);
								$data[$i][4]= ($row1[0] * $data[$i][2]);
							}
							//La variable $data[$i][6] contiene el valor del IVA y $wiva indica si al detalle del movimiento se le adiciona el IVA
							if($wiva == "on")
								$data[$i][4] = $data[$i][4] + $data[$i][6];
				    		$query = "insert ".$empresa."_000011 (medico,fecha_data,hora_data, Mdecon, Mdedoc, Mdeart, Mdecan, Mdevto, Mdepiv, Mdefve, Mdenlo, Mdeest, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".substr($wcont,0,strpos($wcont,"-"))."',".$wdoct.",'".$data[$i][0]."',".$data[$i][2].",".$data[$i][4].",".$data[$i][3].",'".$data[$i][7]."','".$data[$i][8]."','on ','C-".$empresa."')";
							$err1 = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
							if ($err1 != 1)
							{
								$e=$e+1;
								$werr[$e][0]="ERROR EN GRABACION DEL ARTICULO ".$data[$i][0]." : ".mysql_errno().":".mysql_error();
								$werr[$e][1]=2;
								$werr[$e][2]=28;
							}
						}
						else
						{
							$e=$e+1;
							$werr[$e][0]="ERROR NO SE GRABO EL MOVIMIENTO DEL ARTICULO  : ".$data[$i][0]." ERRORES EN ACTUALIZACION DE KARDEX";
							$werr[$e][1]=2;
							$werr[$e][2]=29;
						}
						$query = " UNLOCK TABLES";
						$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO KARDEX Y DETALLE");
						if($wrg == 0)
						{
							$query =  " update ".$empresa."_000010 set Menest ='off'  where Mendoc='".$wdoct."' and Mencon='".substr($wcont,0,strpos($wcont,"-"))."'";
							$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO ENCABEZADO");
						}
					}
					$ok=0;
					$estado="2";
				}
				if($ok != 0)
					$ok = 1;
			}
			else
				$ok=1;
		}
		if(isset($ok) and $ok == 0)
		{
			$wanot=substr(date("Y-m-d"),0,4);
			$wmest=substr(date("Y-m-d"),5,2);
			if($wmest < "10")
				$wmest=str_replace("0","",$wmest);
			$wfechat=date("Y-m-d");
			$wdoct="";
			$wcodp="";
			$wdant="";
			$wfact="";
			$wrett="";
			$wobser="";
			$wcont = "0-NO APLICA";
			$wccoo = "0-NO APLICA";
			$wccod = "0-NO APLICA";
			$wprovt = "0-NO APLICA";
			$wartt = "NO SELECCIONADO-0";
			$wtip=1;
			$wbus="";
			$wb=1;
			$j=-1;
			$data=array();
			$ok=1;
			$west="P";
			$wdana="0";
			echo "<input type='HIDDEN' name= 'wdana' value='".$wdana."'>";
		}
		if(isset($ok)  and $ok == 3)
		{
			$estado="1";
			if(isset($querys))
			{
				$querys=stripslashes($querys);
				$qa=$querys;
			}
			else
			{
				$a=substr($wartt,strrpos($wartt,"-")+1);
				if($wbus =="")
				{
					//                  0       1        2       3       4       5       6       7       8       9      10      11     12
					$querys = "SELECT Menano, Menmes, Mendoc, Mencon, Menfec, Mencco, Menccd, Mendan, Menpre, Mennit, Menfac, Menest, Menobs  from ".$empresa."_000010,".$empresa."_000008 ";
					if($wanot != "")
						$querys .="   where Menano = ".$wanot;
					else
						$querys .="   where Menano > 0 ";
					if($wmest != "")
						$querys .="   and Menmes = ".$wmest;
					if($wcodp != "")
						$querys .="   and Mendoc = '".$wcodp."'";
					if($wcont != "0-NO APLICA")
						$querys .="   and Mencon = '".substr($wcont,0,strpos($wcont,"-"))."'";
					$querys .="   and Mencon = Concod ";
					$querys .="   and Conmve = 'off' ";
					if($wfechat != "")
						if (strpos($wfechat,":") === false)
							$querys .="   and Menfec = '".$wfechat."'";
						else
							$querys .="   and Menfec between '".substr($wfechat,0,10)."' and '".substr($wfechat,11,10)."'";
					if($wccoo != "0-NO APLICA")
						$querys .="   and Mencco = '".substr($wccoo,0,strpos($wccoo,"-"))."'";
					if($wccod != "0-NO APLICA")
						$querys .="   and Menccd = '".substr($wccod,0,strpos($wccod,"-"))."'";
					if($wdant != "")
						$querys .="   and Mendan = '".$wdant."'";
					if($wfact != "")
						$querys .="   and Menfac = '".$wfact."'";
					if($wrett != "")
						$querys .="   and Menpre = ".$wrett;
					if($wprovt != "0-NO APLICA")
						$querys .="   and Mennit = '".substr($wprovt,0,strrpos($wprovt,"-"))."'";
				}
				else
				{
					//                  0       1        2       3       4       5       6       7       8       9      10      11     12
					$querys = "SELECT Menano, Menmes, Mendoc, Mencon, Menfec, Mencco, Menccd, Mendan, Menpre, Mennit, Menfac, Menest, Menobs  from ".$empresa."_000010,".$empresa."_000008,".$empresa."_000011,".$empresa."_000001 ";
					if($wanot != "")
						$querys .="   where Menano = ".$wanot;
					else
						$querys .="   where Menano > 0 ";
					if($wmest != "")
						$querys .="   and Menmes = ".$wmest;
					if($wcodp != "")
						$querys .="   and Mendoc = '".$wcodp."'";
					if($wcont != "0-NO APLICA")
						$querys .="   and Mencon = '".substr($wcont,0,strpos($wcont,"-"))."'";
					$querys .="   and Mencon = Concod ";
					$querys .="   and Conmve = 'off' ";
					if($wfechat != "")
						if (strpos($wfechat,":") === false)
							$querys .="   and Menfec = '".$wfechat."'";
						else
							$querys .="   and Menfec between '".substr($wfechat,0,10)."' and '".substr($wfechat,11,10)."'";
					if($wccoo != "0-NO APLICA")
						$querys .="   and Mencco = '".substr($wccoo,0,strpos($wccoo,"-"))."'";
					if($wccod != "0-NO APLICA")
						$querys .="   and Menccd = '".substr($wccod,0,strpos($wccod,"-"))."'";
					if($wdant != "")
						$querys .="   and Mendan = '".$wdant."'";
					if($wfact != "")
						$querys .="   and Menfac = '".$wfact."'";
					if($wrett != "")
						$querys .="   and Menpre = ".$wrett;
					if($wprovt != "0-NO APLICA")
						$querys .="   and Mennit = '".substr($wprovt,0,strrpos($wprovt,"-"))."'";
					$querys .="   and Mdecon = Mencon ";
					$querys .="   and Mdedoc = Mendoc ";
					$querys .="   and Mdeart  = Artcod ";
					if(isset($wtip) and $wtip == 1)
					{
						$query = "SELECT Artcod, Artnom from ".$empresa."_000001 where Artcod='".$wbus."'";
						$err1 = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
						$num = mysql_num_rows($err1);
						if ($num == 0)
						{
							$query = "SELECT Axpart from ".$empresa."_000009 where Axpcpr='".$wbus."'";
							$err1 = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
							$num = mysql_num_rows($err1);
							if ($num>0)
							{
								$row = mysql_fetch_array($err1);
								$wbus=substr($row[0],0,strpos($row[0],"-"));
							}
						}
						$querys .="   and Artcod = '".$wbus."'";
					}
					else
						$querys .="   and Artnom like '%".$wbus."%'";
				}
				$querys .=" Order by  Mencon, Mendoc ";
				$err = mysql_query($querys,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
				$numero = mysql_num_rows($err);
				$numero=$numero - 1;
			}
			if ($numero>=0)
			{
				if(isset($qa))
				{
					$qa=str_replace(chr(34),chr(39),$qa);
					$qa=substr($qa,0,strpos($qa," limit "));
					$querys=$qa;
				}
				if(isset($qa) and $qa == $querys)
					if(isset($wb) and $wb == 1)
					{
						$wpos = $wpos  + 1;
						if ($wpos > $numero)
							$wpos=$numero;
					}
					else
					{
						$wpos = $wpos  - 1;
						if ($wpos < 0)
							$wpos=0;
					}
				else
					$wpos=0;
					$wp=$wpos+1;
				//echo "Registro Nro : ".$wp."<br>";
				$querys = $querys." limit ".$wpos.",1";
				$err = mysql_query($querys,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
				$querys=str_replace(chr(39),chr(34),$querys);
				echo "<input type='HIDDEN' name= 'querys' value='".$querys."'>";
				echo "<input type='HIDDEN' name= 'wpos' value='".$wpos."'>";
				echo "<input type='HIDDEN' name= 'numero' value='".$numero."'>";
				$row = mysql_fetch_array($err);
				$wanot=$row[0];
				$wmest=$row[1];
				$wdoct=$row[2];
				$west=$row[11];
				$wobser=$row[12];
				$query = "SELECT Concod, Condes   from ".$empresa."_000008  where  Conmin = 'on' and Concod ='".$row[3]."'";
				$err1 = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
				$row1 = mysql_fetch_array($err1);
				$wcont=$row1[0]."-".$row1[1];
				$wfechat=$row[4];
				$query = "SELECT Ccocod, Ccodes    from ".$empresa."_000003  where Ccocod='".$row[5]."'";
				$err1 = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
				$row1 = mysql_fetch_array($err1);
				$wccoo=$row1[0]."-".$row1[1];
				$query = "SELECT Ccocod, Ccodes    from ".$empresa."_000003  where Ccocod='".$row[6]."'";
				$err1 = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
				$row1 = mysql_fetch_array($err1);
				$wccod=$row1[0]."-".$row1[1];
				$wdant=$row[7];
				$wrett=$row[8];
				$wfact=$row[10];
				$query = "SELECT Pronit, Pronom     from ".$empresa."_000006  where Pronit='".$row[9]."'";
				$err1 = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
				$row1 = mysql_fetch_array($err1);
				$wprovt=$row1[0]."-".$row1[1];
				$query = "SELECT Mdeart, Mdecan, Mdevto, Mdepiv, Mdefve, Mdenlo  from ".$empresa."_000011  where Mdecon='".$row[3]."' and Mdedoc ='".$row[2]."' order by id ";
				$err1 = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
				$num1 = mysql_num_rows($err1);
				$data=array();
				for ($i=0;$i<$num1;$i++)
				{
					$row1 = mysql_fetch_array($err1);
					$data[$i][0]=$row1[0];
					$query = "SELECT Artnom, Artuni  from ".$empresa."_000001  where Artcod='".$row1[0]."'";
					$err2 = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
					$row2 = mysql_fetch_array($err2);
					$data[$i][1]=$row2[0];
					$data[$i][2]=$row1[1];
					$data[$i][3]=$row1[3];
					$data[$i][4]=$row1[2];
					if($data[$i][2] != 0)
						$data[$i][5]=$data[$i][4] / $data[$i][2];
					else
						$data[$i][5]=$data[$i][4];
					$data[$i][6]=($data[$i][3] / 100) * $data[$i][4];
					$data[$i][7]=$row1[4];
					$data[$i][8]=$row1[5];
					$data[$i][9]=$row2[1];
					if(substr($wccoo,0,strpos($wccoo,"-")) != "0")
					{
						$query = "SELECT Karexi  from ".$empresa."_000007 where Karcod='".$row1[0]."' and Karcco='".substr($wccoo,0,strpos($wccoo,"-"))."'";
						$err2 = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
						$num2 = mysql_num_rows($err2);
					}
					else
						$num2=0;
					if($num2 >0)
					{
						$row2 = mysql_fetch_array($err2);
						$data[$i][10]=$row2[0];
					}
					else
						$data[$i][10]=0;
				}
				$j=$num1-1;
			}
		}
		echo "<input type='HIDDEN' name= 'wdoct' value='".$wdoct."'>";
		echo "<table border=0 align=center>";
		echo "<tr><td align=center colspan=5><IMG SRC='/matrix/images/medical/pos/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=right colspan=5><font size=2>Powered by :  MATRIX </font></td></tr>";
		if($ok == 3)
		{
			if(isset($wp))
			{
				$estado="1";
				$n=$numero +1 ;
				echo "<tr><td align=right colspan=5><font size=2><b>Registro Nro. ".$wp." De ".$n."</b></font></td></tr>";
			}
			else
				echo "<tr><td align=right colspan=5><font size=2 color='#CC0000'><b>Consulta Sin Registros</b></font></td></tr>";
		}
		if($ok == 1)
		{
			if(!isset($wdana))
				$wdana="0";
			$estado="1";
			if(substr($wcont,0,strpos($wcont,"-")) != 0)
			{
				$query = "select Condan, Concan  from ".$empresa."_000008 where Concod='".substr($wcont,0,strpos($wcont,"-"))."'";
				$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
				$row = mysql_fetch_array($err);
				if($row[0] == "on" and $wdant != "" and $wdant != $wdana and (is_numeric($wdant) or strpos($wdant,":") !== false))
				{
					$docs=explode(":",$wdant);
					$query = "SELECT count(*) from ".$empresa."_000010  where Mencon='".$row[1]."' and Mendoc in('";
					for ($m=0;$m<sizeof($docs)-1;$m++)
						$query .=$docs[$m]."','";
					$query .=$docs[sizeof($docs)-1]."')";
					$err2 = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
					$row2 = mysql_fetch_array($err2);
					$doc1=$row2[0];
					$query = "SELECT count(*) from ".$empresa."_000010  where Mencon='".$row[1]."' and Menfac != 'NULO' and Mendoc in('";
					for ($m=0;$m<sizeof($docs)-1;$m++)
						$query .=$docs[$m]."','";
					$query .=$docs[sizeof($docs)-1]."')";
					$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$row2 = mysql_fetch_array($err2);
					$doc2=$row2[0];
					if($doc1 == $doc2)
					{
						if(strpos($wdant,":") === false)
						{
							$query = "SELECT Mencco, Menccd, Mennit  from ".$empresa."_000010 ";
							$query .=" where Mendoc = '".$wdant."'";
							$query .="   and Mencon = '".$row[1]."'";
							$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
							$row2 = mysql_fetch_array($err2);
							$query = "SELECT Ccocod, Ccodes    from ".$empresa."_000003  where Ccocod='".$row2[0]."'";
							$err1 = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
							$row1 = mysql_fetch_array($err1);
							$wccoo=$row1[0]."-".$row1[1];
							$query = "SELECT Ccocod, Ccodes    from ".$empresa."_000003  where Ccocod='".$row2[1]."'";
							$err1 = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
							$row1 = mysql_fetch_array($err1);
							$wccod=$row1[0]."-".$row1[1];
							$query = "SELECT Pronit, Pronom     from ".$empresa."_000006  where Pronit='".$row2[2]."'";
							$err1 = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
							$row1 = mysql_fetch_array($err1);
							$wprovt=$row1[0]."-".$row1[1];
						}
						$query = "select Conaca, Conaco   from ".$empresa."_000008 where Concod='".$row[1]."'";
						$err2 = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
						$row2 = mysql_fetch_array($err2);
						if($row2[0] == "off" and $row2[1] == "off")
						{
							if(strpos($wdant,":") === false)
								$query = "SELECT Mdeart, Mdecan, Mdevto, Mdepiv, Mdefve, Mdenlo  from ".$empresa."_000011  where Mdecon='".$row[1]."' and Mdedoc ='".$wdant."' ";
							else
							{
								$docs=explode(":",$wdant);
								$query = "SELECT Mdeart, Mdecan, Mdevto, Mdepiv, Mdefve, Mdenlo  from ".$empresa."_000011  where Mdecon='".$row[1]."' and Mdedoc in(";
								for ($m=0;$m<sizeof($docs)-1;$m++)
									$query .=$docs[$m].",";
								$query .=$docs[sizeof($docs)-1].")";
							}
						}
						else
							$query = "SELECT Mdeart, Mdecan, Mdevto, Mdepiv, Mdefve, Mdenlo  from ".$empresa."_000011  where Mdecon='".$row[1]."' and Mdedoc ='".$wdant."'";
						$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
						$num1 = mysql_num_rows($err1);
						$data=array();
						if($num1 > 0)
						{
							$wdana=$wdant;
							for ($i=0;$i<$num1;$i++)
							{
								$row1 = mysql_fetch_array($err1);
								$data[$i][0]=$row1[0];
								$query = "SELECT Artnom, Artuni  from ".$empresa."_000001  where Artcod='".$row1[0]."'";
								$err2 = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
								$row2 = mysql_fetch_array($err2);
								$data[$i][1]=$row2[0];
								$data[$i][2]=$row1[1];
								$data[$i][3]=$row1[3];
								$data[$i][4]=$row1[2];
								if($data[$i][2] != 0)
									$data[$i][5]=$data[$i][4] / $data[$i][2];
								else
									$data[$i][5]=$data[$i][4];
								$data[$i][6]=($data[$i][3] / 100) * $data[$i][4];
								$data[$i][7]=$row1[4];
								$data[$i][8]=$row1[5];
								$data[$i][9]=$row2[1];
								if(substr($wccoo,0,strpos($wccoo,"-")) != "0")
								{
									$query = "SELECT Karexi  from ".$empresa."_000007 where Karcod='".$row1[0]."' and Karcco='".substr($wccoo,0,strpos($wccoo,"-"))."'";
									$err2 = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
									$num2 = mysql_num_rows($err2);
								}
								else
									$num2=0;
								if($num2 > 0)
								{
									$row2 = mysql_fetch_array($err2);
									$data[$i][10]=$row2[0];
								}
								else
									$data[$i][10]=0;
							}
						}
						else
							$wdana="0";
						$j=$num1-1;
					}
					else
					{
						$werr=array();
						$e=-1;
						$e=$e+1;
						$werr[$e][0]="ERROR AL MENOS UN DOCUMENTO ANEXO ESTA ANULADO!!!. POR FAVOR REVISE";
						$werr[$e][1]=2;
						$werr[$e][2]=30;
					}
				}
			}
			echo "<input type='HIDDEN' name= 'wdana' value='".$wdana."'>";
		}
		echo "<tr><td align=center bgcolor=#000066 colspan=5><font color=#ffffff size=6><b>MOVIMIENTO DE INVENTARIOS</font><font color=#33CCFF size=4>&nbsp&nbsp&nbspVer. 2008-10-20</font></b></font></td></tr>";
		$color="#dddddd";
		$color1="#000099";
		$color2="#006600";
		$color3="#cc0000";
		$color4="#CC99FF";
		$color5="#99CCFF";
		$color6="#FF9966";
		$color7="#cccccc";
		$color8="9966FF";
		echo "<tr><td bgcolor=".$color." align=center>A&NtildeO : <input type='TEXT' name='wanot' size=4 maxlength=4 value=".$wanot."></td>";
		echo "<td bgcolor=".$color." align=center>MES : <input type='TEXT' name='wmest' size=2 maxlength=2 value=".$wmest."></td>";
		echo "<td bgcolor=".$color." align=center>DOCUMENTO Nro : <font size=6><b>".$wdoct."</b></font>&nbsp&nbsp<input type='TEXT' name='wcodp' size=7 maxlength=7 value=".$wcodp."></td>";
		echo "<td bgcolor=".$color." align=center>CONCEPTO : ";
		$query = "SELECT Rcucon, Condes   from ".$empresa."_000013,".$empresa."_000008  where Rcuusu='".$key."' and Rcuest='on' and Rcucon= Concod and Conmin = 'on'  and Conest='on' order by Rcucon";
		$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			//echo "<select name='wcont' OnChange='enter()'>";
			echo "<select name='wcont'>";
			echo "<option>0-NO APLICA</option>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($wcont == $row[0]."-".$row[1])
					echo "<option selected>".$row[0]."-".$row[1]."</option>";
				else
					echo "<option>".$row[0]."-".$row[1]."</option>";
			}
			echo "</select>";
		}
		echo "</td>";
		echo "<td bgcolor=".$color." align=center>FECHA : <input type='TEXT' name='wfechat' size=10 maxlength=21 value=".$wfechat."></td></tr>";
		echo "<tr><td bgcolor=".$color." align=center>C.C. ORIGEN : ";
		$query = "SELECT Ccucco, Ccodes    from ".$empresa."_000054, ".$empresa."_000003  where Ccuusu='".$key."' and Ccutip='O' and Ccuest='on' and Ccucco=Ccocod order by Ccucco";
		$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<select name='wccoo'>";
			echo "<option>0-NO APLICA</option>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($wccoo == $row[0]."-".$row[1])
					echo "<option selected>".$row[0]."-".$row[1]."</option>";
				else
					echo "<option>".$row[0]."-".$row[1]."</option>";
			}
			echo "</select>";
		}
		echo "</td>";
		echo "<td bgcolor=".$color." align=center>C.C. DESTINO : ";
		$query = "SELECT Ccucco, Ccodes    from ".$empresa."_000054, ".$empresa."_000003  where Ccuusu='".$key."' and Ccutip='D' and Ccuest='on' and Ccucco=Ccocod order by Ccucco";
		$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<select name='wccod'>";
			echo "<option>0-NO APLICA</option>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($wccod == $row[0]."-".$row[1])
					echo "<option selected>".$row[0]."-".$row[1]."</option>";
				else
					echo "<option>".$row[0]."-".$row[1]."</option>";
			}
			echo "</select>";
		}
		echo "</td>";
		echo "<td bgcolor=".$color." align=center colspan=2>DOCUMENTO ANEXO : <input type='TEXT' name='wdant' size=7 maxlength=30 value=".$wdant."></td>";
		switch ($west)
		{
			case "P":
				echo "<td bgcolor=".$color." align=center>ESTADO : <font color=".$color1." size=6><b>P</b></td>";
			break;
			case "on":
				echo "<td bgcolor=".$color." align=center>ESTADO : <font color=".$color2." size=6><b>A</b></td>";
			break;
			case "off":
				echo "<td bgcolor=".$color." align=center>ESTADO : <font color=".$color3." size=6><b>I</b></td>";
			break;
		}
		echo "</tr>";
		echo "<tr><td bgcolor=".$color." align=center>% DE RETENCI&Oacute;N  : <input type='TEXT' name='wrett' size=7 maxlength=7 value=".$wrett."></td>";
		echo "<td bgcolor=".$color." align=center colspan=3>PROVEEDOR : ";
		$query = "SELECT Pronit, Pronom     from ".$empresa."_000006  order by Pronom";
		$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<select name='wprovt'>";
			echo "<option>0-NO APLICA</option>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($wprovt == $row[0]."-".$row[1])
					echo "<option selected>".$row[0]."-".$row[1]."</option>";
				else
					echo "<option>".$row[0]."-".$row[1]."</option>";
			}
			echo "</select>";
		}
		echo "</td><td bgcolor=".$color." align=center>DOC. SOPORTE : <input type='TEXT' name='wfact' size=10 maxlength=20 value=".$wfact."></td></tr>";
		echo "<tr><td bgcolor=#dddddd align=center>";
		if(isset($wtip))
			if($wtip == 1)
				echo "<input type='RADIO' name=wtip value=1 checked> CODIGO <input type='RADIO' name=wtip value=2> NOMBRE </td>";
			else
				echo "<input type='RADIO' name=wtip value=1> CODIGO <input type='RADIO' name=wtip value=2 checked> NOMBRE </td>";
		else
			echo "<input type='RADIO' name=wtip value=1 checked> CODIGO <input type='RADIO' name=wtip value=2> NOMBRE </td>";
		?>
		<script>
			function ira(){document.inventario.wbus.focus();}
		</script>
		<?php
		echo "<td bgcolor=#dddddd colspan=3 align=center> CRITERIO  : <input type='TEXT' name='wbus' size=20 maxlength=40>&nbsp&nbsp";
		if(isset($wbus) and $wbus != "")
		{
			if($wtip == 1)
			{
				// SE SELECCIONAN UNICAMENTE LOS ARTICULOS ACTIVOS MODIFICACION REALIZADA EN 2007-04-11
				$query = "SELECT Artcod, Artnom from ".$empresa."_000001 where Artcod='".$wbus."' and Artest='on' ";
				$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					echo "<select name='wartt'>";
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						if($ok == 3 and $wartt == $row[0]."-".$row[1])
							echo "<option selected>".$row[0]."-".$row[1]."</option>";
						else
							echo "<option>".$row[1]."-".$row[0]."</option>";
					}
					echo "</select>";
				}
				else
				{
					// ***** AQUI BUSQUEDA X CODIGO DE PROVEEDOR *****
					$query = "SELECT Axpart from ".$empresa."_000009 where Axpcpr='".$wbus."'";
					$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
					$num = mysql_num_rows($err);
					if ($num>0)
					{
						echo "<select name='wartt'>";
						for ($i=0;$i<$num;$i++)
						{
							$row = mysql_fetch_array($err);
							$wbus=substr($row[0],0,strpos($row[0],"-"));
							if($ok == 3 and $wartt == $row[0])
								echo "<option selected>".$row[0]."</option>";
							else
								echo "<option>".substr($row[0],strpos($row[0],"-")+1)."-".substr($row[0],0,strpos($row[0],"-"))."</option>";
						}
						echo "</select>";
					}
					else
					{
						echo "<select name='wartt'>";
						echo "<option>NO SELECCIONADO-0</option>";
						echo "</select>";
					}
				}
			}
			else
			{
				// SE SELECCIONAN UNICAMENTE LOS ARTICULOS ACTIVOS MODIFICACION REALIZADA EN 2007-04-11
				$query = "SELECT Artcod, Artnom     from ".$empresa."_000001 where Artnom like '%".$wbus."%' and Artest='on' order by Artnom";
				$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					echo "<select name='wartt'>";
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						if($ok == 3 and $wartt == $row[0]."-".$row[1])
							echo "<option selected>".$row[0]."-".$row[1]."</option>";
						else
							echo "<option>".$row[1]."-".$row[0]."</option>";
					}
					echo "<option>NO SELECCIONADO-0</option>";
					echo "</select>";
				}
				else
				{
					echo "<select name='wartt'>";
					echo "<option>NO SELECCIONADO-0</option>";
					echo "</select>";
				}
			}
		}
		else
		{
			if($ok == 3 and isset($wartt))
			{
				echo "<select name='wartt'>";
				echo "<option>".$wartt."</option>";
				echo "</select>";
			}
			else
			{
				echo "<select name='wartt'>";
				echo "<option>NO SELECCIONADO-0</option>";
				echo "</select>";
			}
		}
		echo "</td><td bgcolor=#dddddd align=center><b>Anular</b><input type='checkbox' name='null'></td></tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc valign=center colspan=5 align=center>OBSERVACIONES :<br><textarea name='wobser' cols=100 rows=3 class=tipo3>".$wobser."</textarea></td>";
		echo "</tr>";
		switch ($ok)
		{
			case 1:
				echo "<tr><td bgcolor=#cccccc align=center><input type='RADIO' name=ok value=0 onclick='enter()'><b>INICIAR</b></td><td bgcolor=#cccccc align=center><input type='RADIO' name=ok value=1 checked onclick='enter()'><b>PROCESO&nbsp &nbspMarcar Todos</b><input type='checkbox' name='all'></td><td bgcolor=#cccccc align=center colspan=2><input type='RADIO' name=ok value=3 onclick='enter()'><b>CONSULTAR</b>";
			break;
			case 3:
				echo "<tr><td bgcolor=#cccccc align=center><input type='RADIO' name=ok value=0 onclick='enter()'><b>INICIAR</b></td><td bgcolor=#cccccc align=center><input type='RADIO' name=ok value=1 onclick='enter()'><b>PROCESO&nbsp &nbspMarcar Todos</b><input type='checkbox' name='all'></td><td bgcolor=#cccccc align=center colspan=2><input type='RADIO' name=ok value=3 checked onclick='enter()'><b>CONSULTAR</b>";
			break;
		}
		if(isset($wb))
			if($wb == 1)
				echo "<input type='RADIO' name=wb value=1 checked onclick='enter()'> Adelante <input type='RADIO' name=wb value=2 onclick='enter()'> Atras</td><td bgcolor=#999999 align=center><input type='RADIO' name=ok value=2 onclick='enter()'><b>GRABAR</b></td></tr>";
			else
				echo "<input type='RADIO' name=wb value=1 onclick='enter()'> Adelante <input type='RADIO' name=wb value=2 checked onclick='enter()'> Atras</td><td bgcolor=#999999 align=center><input type='RADIO' name=ok value=2 onclick='enter()'><b>GRABAR</b></td></tr>";
		else
			echo "<input type='RADIO' name=wb value=1 checked> Adelante <input type='RADIO' name=wb value=2> Atras</td><td bgcolor=#999999 align=center><input type='RADIO' name=ok value=2 onclick='enter()'><b>GRABAR</b></td></tr>";
		echo "<tr><td bgcolor=#999999 colspan=5 align=center><input type='submit' value='OK'></td></tr>";
		echo"</table><br><br>";
		if(isset($werr))
		{
			if($e > -1)
			{
				echo "<center><table border=0 aling=center>";
				//echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' width=50%  ></td><tr></table></center>";
				for ($i=0;$i<=$e;$i++)
				{
					if($werr[$i][1] == 1)
						echo "<tr><td bgcolor=".$color4."><IMG SRC='/matrix/images/medical/root/Malo.ico'>&nbsp&nbsp<font color=#000000 face='tahoma'><b>ERROR Nro. ".$werr[$i][2]."</B></font></TD><TD bgcolor=".$color4."><font color=#000000 face='tahoma'><b>".$werr[$i][0]."</b></font></td></tr>";
					else
						echo "<tr><td bgcolor=".$color6."><IMG SRC='/matrix/images/medical/root/pesimo.ico'>&nbsp&nbsp<font color=#000000 face='tahoma'><b>ERROR Nro. ".$werr[$i][2]."</B></font></TD><TD bgcolor=".$color6."><font color=#000000 face='tahoma'><b>".$werr[$i][0]."</b></font></td></tr>";
				}
				echo "</table><br><br></center>";
			}
			else
			{
				echo "<center><table border=0 aling=center>";
				echo "<tr><td bgcolor=".$color5."><IMG SRC='/matrix/images/medical/root/feliz.ico'>&nbsp&nbsp<font color=#000000 face='tahoma'><b>CORRECTO !!!</b></font></TD><TD bgcolor=".$color5."><font size=6 color=#000000 face='tahoma'><b>Doc : ".$wdocg."&nbsp &nbsp  Cpto : ".substr($wcong,strpos($wcong,"-")+1)." </b></font></td></tr>";
				//echo "<font size=3><MARQUEE BEHAVIOR=SLIDE BGCOLOR=#CCCCFF LOOP=-1>LOS DATOS FUERON GRABADOS OK!!!! ---&nbsp&nbsp<font size=7 color=#000000><b>Doc : ".$wdocg." Cpto : ".$wcong." </b></font></MARQUEE></FONT>";
				echo "</table><br><br></center>";
			}
		}
		if(isset($warning))
		{
			if($wa > -1)
			{
				echo "<center><table border=0 aling=center>";
				//echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' width=50%  ></td><tr></table></center>";
				for ($i=0;$i<=$wa;$i++)
					echo "<tr><td bgcolor=".$color7."><IMG SRC='/matrix/images/medical/root/warning.ico'>&nbsp&nbsp<font color=#000000 face='tahoma'><b>ADVERTENCIA !!!</b></font></TD><TD bgcolor=".$color7."><font color=#000000 face='tahoma'><b>".$warning[$i]."</b></font></td></tr>";
				echo "</table><br><br></center>";
			}
		}
		if($wdoct != "" and $ok == 3)
		{
			echo "<table border=0 aling=center>";
			echo "<tr><td bgcolor=".$color7." align=center><A HREF='/matrix/pos/reportes/impmov.php?empresa=".$empresa."&con=".$wcont."&doc=".$wdoct."' target = '_blank'><font face='tahoma'><b>Imprimir</b></font></td><tr></table>";
		}
		else
		{
			$path1="/matrix/pos/reportes/impmovxa.php?empresa=".$empresa."";
			$path2="/matrix/pos/reportes/impkardex.php?empresa=".$empresa."";
			$path3="/matrix/pos/procesos/help.php?empresa=".$empresa."";
			$path4="/matrix/registro.php?call=1&Form=000001-".$empresa."-C-Maestro de Articulos&Frm=0&tipo=P&key=".$empresa."";
			$path5="/matrix/registro.php?call=1&Form=000002-".$empresa."-C-Unidades de Medida&Frm=0&tipo=P&key=".$empresa."";
			$path6="/matrix/registro.php?call=1&Form=000006-".$empresa."-C-Maestro de Proveedores&Frm=0&tipo=P&key=".$empresa."";
			$path7="/matrix/registro.php?call=1&Form=000026-".$empresa."-C-Maestro de Tarifas&Frm=0&tipo=P&key=".$empresa."";
			$path8="/matrix/registro.php?call=1&Form=000009-".$empresa."-C-Articulos x Proveedor&Frm=0&tipo=P&key=".$empresa."";
			echo "<table border=0 align=center>";
			echo "<tr><td bgcolor=".$color7." rowspan=2><IMG SRC='/matrix/images/medical/root/procesos.ico'  alt='PROCESOS'></td><td onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)." bgcolor=".$color7." align=center onclick='ejecutar(1,".chr(34).$path1.chr(34).")'><font face='tahoma'><b>Movimiento X Articulo</b></font></td><td onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)." bgcolor=".$color7." align=center onclick='ejecutar(3,".chr(34).$path3.chr(34).")'><font face='tahoma'><b>Ayuda en Linea</b></font></td>";
			echo "<td onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)." bgcolor=".$color7." align=center onclick='ejecutar(5,".chr(34).$path5.chr(34).")'><font face='tahoma'><b>Unidades de Medida</b></font></td><td onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)." bgcolor=".$color7." align=center onclick='ejecutar(6,".chr(34).$path6.chr(34).")'><font face='tahoma'><b>Maestro de Proveedores</b></font></td></tr>";
			echo "<tr><td onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)." bgcolor=".$color7." align=center onclick='ejecutar(2,".chr(34).$path2.chr(34).")'><font face='tahoma'><b>Kardex X Articulo</b></font></td><td onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)." bgcolor=".$color7." align=center onclick='ejecutar(4,".chr(34).$path4.chr(34).")'><font face='tahoma'><b>Maestro de Articulos</b></font></td>";
			echo "<td onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)." bgcolor=".$color7." align=center onclick='ejecutar(7,".chr(34).$path7.chr(34).")'><font face='tahoma'><b>Maestro de Tarifas</b></font></td><td onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)." bgcolor=".$color7." align=center onclick='ejecutar(8,".chr(34).$path8.chr(34).")'><font face='tahoma'><b>Codigos de Barra</b></font></td></tr></table>";
		}
		echo "<table border=0 align=center>";
		$w=-1;
		$data1=array();
		// ******************* CICLOS DE CONSOLIDACION Y CALCULO DE VALORES **********************
		for ($i=0;$i<=$j;$i++)
		{
			if( empty( $data[$i][5] ) )
				$data[$i][5] = 0;
			if(!isset($data[$i][2]))
					$data[$i][2]=$cantidad;
			if(isset($data[$i][5]) and $data[$i][5] != 0 and $data[$i][2] !=0)
				$data[$i][4]=$data[$i][2] * $data[$i][5];
			else
				$data[$i][4]=0;
			if(!isset($del[$i]) and (($data[$i][2] > 0 and $data[$i][5] != 0) or ($data[$i][2] >= 0 and $data[$i][5] != 0) or ($data[$i][2] > 0 and $data[$i][5] == 0)))
			{
				$w=$w+1;
				$data1[$w][0]=$data[$i][0];
				$data1[$w][1]=$data[$i][1];
				$data1[$w][2]=$data[$i][2];
				$data1[$w][3]=$data[$i][3];
				$data1[$w][5]=$data[$i][5];
				if($data[$i][2] != 0)
					$data1[$w][4]=$data[$i][5] * $data[$i][2];
				else
					$data1[$w][4]=$data[$i][5];
				$data1[$w][6]=($data[$i][3] / 100) * $data[$i][4];
				$data1[$w][7]=$data[$i][7];
				$data1[$w][8]=$data[$i][8];
				$data1[$w][9]=$data[$i][9];
				$data1[$w][10]=$data[$i][10];
			}
		}
		$j=$w;
		for ($i=0;$i<=$j;$i++)
		{
			$data[$i]=$data1[$i];
			if($data[$i][0] == $wbus)
				$position=$i;
		}
		$j=agrupar($j,$data);
		// ******************* FIN DE CICLOS DE CONSOLIDACION Y CALCULO DE VALORES **********************
		if(isset($wartt))
		{
			echo "<tr><td align=center bgcolor=#999999 colspan=15><font color=#000066 size=5><b>DETALLE DEL DOCUMENTO</b></font></td></tr>";
			echo "<tr><td align=center bgcolor=#000066><font color=#ffffff >NRO ITEM</font></td><td align=center bgcolor=#000066><font color=#ffffff >ELIMINAR</b></font></td><td align=center bgcolor=#000066><font color=#ffffff >MODIFICAR</b></font></td><td align=center bgcolor=#000066><font color=#ffffff >CODIGO</b></font></td><td align=center bgcolor=#000066><font color=#ffffff >DESCRIPCION</b></font></td><td align=center bgcolor=#000066><font color=#ffffff >UNIDAD DE<BR>MEDIDA</b></font></td><td align=center bgcolor=#000066><font color=#ffffff >EXISTENCIAS<br>ACTUALES</b></font></td><td align=center bgcolor=#000066 ><font color=#ffffff >CANTIDAD</b></font></td><td align=center bgcolor=#000066><font color=#ffffff >% IVA </b></font><td align=center bgcolor=#000066><font color=#ffffff >VLR UNITARIO</b></font></td><td align=center bgcolor=#000066><font color=#ffffff >VLR TOTAL</b></font></td></td><td align=center bgcolor=#000066><font color=#ffffff >VLR IVA</b></font></td><td align=center bgcolor=#000066><font color=#ffffff >FECHA VENCIMIENTO</b></font></td><td align=center bgcolor=#000066><font color=#ffffff >NRo. LOTE</b></font></td><td align=center bgcolor=#000066><font color=#ffffff >VLR ULT. COMPRA</b></font></td></tr>";
			$wtotg=0;
			$wtotiva=0;
			$query = "select Condfa,Conaca, Conaco,Conind    from ".$empresa."_000008 where Concod='".substr($wcont,0,strpos($wcont,"-"))."'";
			$err5 = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
			$row5 = mysql_fetch_array($err5);
			// ******************* CICLO DE DIBUJO DE ARTICULOS SELECCIONADOS Y VERIFICACION DE CAMBIO DE VALORES Y CANTIDADES **********************
			for ($i=0;$i<=$j;$i++)
			{
				if($i % 2 == 0)
					$color="#dddddd";
				else
					$color="#cccccc";
				if($row5[1] == "off" and $row5[2] == "on")
					$data[$i][2]=0;
				if($row5[2] == "off" and $row5[1] == "on"  or ($row5[1] == "on" and $row5[2] == "on" and $row5[3] == "0"))
					$data[$i][5]=0;
				if($data[$i][2] != 0)
					$data[$i][4]=$data[$i][5] * $data[$i][2];
				else
					$data[$i][4]=$data[$i][5];
				$data[$i][6]= ($data[$i][3] / 100) * $data[$i][4];
				$w=$i+1;
				$wtotg += $data[$i][4];
				$wtotiva += $data[$i][6];
				echo "<tr><td bgcolor=".$color." align=center>".$w."</td>";
				if(isset($all) and $ok == 1)
					echo "<td bgcolor=".$color." align=center><input type='checkbox' name='del[".$i."]' checked></td>";
				else
					echo "<td bgcolor=".$color." align=center><input type='checkbox' name='del[".$i."]'></td>";
				echo "<td bgcolor=".$color." align=center><input type='RADIO' name=position value=".$i." onclick='enter()'></td>";
				echo "<td bgcolor=".$color.">".$data[$i][0]."</td>";
				echo "<td bgcolor=".$color.">".$data[$i][1]."</td>";
				echo "<td bgcolor=".$color.">".$data[$i][9]."</td>";
				echo "<td bgcolor=".$color." align=right>".number_format((double)$data[$i][10],2,'.',',')."</td>";
				if( (!isset($position) or $i != $position))
				{
					echo "<td bgcolor=".$color." align=right>".number_format((double)$data[$i][2],2,'.',',')."</td>";
					echo "<td bgcolor=".$color." align=right>".number_format((double)$data[$i][3],0,'.',',')."%</td>";
					echo "<td bgcolor=".$color." align=right>$".number_format((double)$data[$i][5],4,'.',',')."</td>";
					echo "<td bgcolor=".$color." align=right>$".number_format((double)$data[$i][4],2,'.',',')."</td>";
					echo "<td bgcolor=".$color." align=right>$".number_format((double)$data[$i][6],2,'.',',')."</td>";
					echo "<td bgcolor=".$color." align=center>".$data[$i][7]."</td>";
					echo "<td bgcolor=".$color." align=right>".$data[$i][8]."</td>";
					$query = "SELECT Karvuc  from ".$empresa."_000007 where Karcod='".$data[$i][0]."' and Karcco='".substr($wccoo,0,strpos($wccoo,"-"))."'";
					$err1 = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
					$num1 = mysql_num_rows($err1);
					if ($num1>0)
					{
						$row1 = mysql_fetch_array($err1);
						$wvaluc=$row1[0];
					}
					else
						$wvaluc=0;
					echo "<td bgcolor=".$color." align=right>$".number_format((double)$wvaluc,2,'.',',')."</td></tr>";
				}
				else
				{
					//echo "Posicion : ".$position."<br>";
					$query = "SELECT Artfvn  from ".$empresa."_000001 where Artcod='".$data[$i][0]."'";
					$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
					$num = mysql_num_rows($err);
					if ($num>0)
					{
						$row = mysql_fetch_array($err);
						$fvnl=$row[0];
						?>
						<script>
							function ira(){document.inventario.cantidad.focus();}
						</script>
						<?php
						echo "<td bgcolor=".$color." align=center><input type='TEXT' name='cantidad' size=7 maxlength=7 value=".$data[$position][2]." onkeypress='teclado()'</td>";
						echo "<td bgcolor=".$color." align=center>".$data[$j][3]."%</td>";
						if(isset($fvnl) and $fvnl == "on")
						{
							echo "<td bgcolor=".$color." align=center><input type='TEXT' name='data[".$position."][5]' size=7 maxlength=18 value=".$data[$position][5]." onkeypress='teclado()'></td>";
							echo "<td bgcolor=".$color." align=center>&nbsp</td>";
							echo "<td bgcolor=".$color." align=center>&nbsp</td>";
							echo "<td bgcolor=".$color." align=center><input type='TEXT' name='data[".$position."][7]' size=10 maxlength=10 value='".$data[$position][7]."' onkeypress='teclado3()'></td>";
							echo "<td bgcolor=".$color." align=center><input type='TEXT' name='data[".$position."][8]' size=15 maxlength=15 value='".$data[$position][8]."' onkeypress='teclado2()'></td>";
							echo "<td bgcolor=".$color." align=center>&nbsp</td></tr>";
						}
						else
						{
							echo "<td bgcolor=".$color." align=center><input type='TEXT' name='data[".$position."][5]' size=7 maxlength=18 value=".$data[$position][5]."  onkeypress='teclado1()'></td>";
							echo "<td bgcolor=".$color." align=center>&nbsp</td>";
							echo "<td bgcolor=".$color." align=center>&nbsp</td>";
							echo "<td bgcolor=".$color." align=center>".$data[$position][7]."</td>";
							echo "<td bgcolor=".$color." align=center>".$data[$position][8]."</td>";
							echo "<td bgcolor=".$color." align=center>&nbsp</td></tr>";
						}
					}
					$modificar="n";
				}
			}
			// ******************* FIN CICLO DE DIBUJO DE ARTICULOS SELECCIONADOS Y VERIFICACION DE CAMBIO DE VALORES Y CANTIDADES **********************
			//if($wtotg > 0)
				echo "<tr><td bgcolor=#999999 align=center colspan=10><font color=#000066><b>TOTALES</b></font></td><td bgcolor=#999999 align=right><font color=#000066><b>$".number_format((double)$wtotg,2,'.',',')."</b></font></td><td bgcolor=#999999 align=right><font color=#000066><b>$".number_format((double)$wtotiva,2,'.',',')."</b></font></td><td bgcolor=#999999 align=center colspan=3>&nbsp</td></tr>";
			// ******************* ENTRADA DE ARTICULOS NUEVOS A LA LISTA DE SELECCIONADOS **********************
			if(($wartt != "NO SELECCIONADO-0" or ($wtip == 1 and $wbus != "")) and $ok == 1 and !isset($position))
			{
				$s=0;
				if($wtip == 1)
				{
					// SE SELECCIONAN UNICAMENTE LOS ARTICULOS ACTIVOS MODIFICACION REALIZADA EN 2007-04-11
					$query = "SELECT Artcod, Artnom,Artiva,Artfvn,Artuni  from ".$empresa."_000001 where Artcod='".$wbus."' and Artest='on' ";
					$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
					$num = mysql_num_rows($err);
					if ($num>0)
					{
						$j=$j+1;
						$row = mysql_fetch_array($err);
						$query = "SELECT Karexi from ".$empresa."_000007 where Karcod='".$wbus."' and Karcco='".substr($wccoo,0,strpos($wccoo,"-"))."'";
						$err1 = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
						$num1 = mysql_num_rows($err1);
						if($num1 > 0)
						{
							$row1 = mysql_fetch_array($err1);
							$wexis=$row1[0];
						}
						else
							$wexis=0;
						$data[$j][0]=$row[0];
						$data[$j][1]=$row[1];
						$data[$j][9]=$row[4];
						$data[$j][10]=$wexis;
						$data[$j][3]=number_format((double)$row[2],0,'.',',');
						$fvnl=$row[3];
						if($fvnl == "on")
						{
							$data[$j][7]=date("Y-m-d");
							$data[$j][7]=(string)((integer)substr($data[$j][7],0,4)+1).substr($data[$j][7],4);
							$data[$j][8]="";
						}
						else
						{
							$data[$j][7]="0000-00-00";
							$data[$j][8]=".";
						}
						$s=1;
					}
				}
				else
				{
					$j=$j+1;
					$data[$j][0]=substr($wartt,strrpos($wartt,"-") + 1);
					$wbus=$data[$j][0];
					$data[$j][1]=substr($wartt,0,strrpos($wartt,"-"));
					$query = "SELECT Artcod, Artnom,Artiva,Artfvn,Artuni  from ".$empresa."_000001 where Artcod='".$wbus."'";
					$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
					$num = mysql_num_rows($err);
					if ($num>0)
					{
						$row = mysql_fetch_array($err);
						$query = "SELECT Karexi from ".$empresa."_000007 where Karcod='".$wbus."' and Karcco='".substr($wccoo,0,strpos($wccoo,"-"))."'";
						$err1 = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
						$num1 = mysql_num_rows($err1);
						if($num1 > 0)
						{
							$row1 = mysql_fetch_array($err1);
							$wexis=$row1[0];
						}
						else
							$wexis=0;
						$data[$j][0]=$row[0];
						$data[$j][1]=$row[1];
						$data[$j][9]=$row[4];
						$data[$j][10]=$wexis;
						$data[$j][3]=number_format((double)$row[2],0,'.',',');
						$fvnl=$row[3];
						if($fvnl == "on")
						{
							$data[$j][7]=date("Y-m-d");
							$data[$j][7]=(string)((integer)substr($data[$j][7],0,4)+1).substr($data[$j][7],4);
							$data[$j][8]="";
						}
						else
						{
							$data[$j][7]="0000-00-00";
							$data[$j][8]=".";
						}
						$s=1;
					}
				}
				if($s == 1)
				{
					if($j % 2 == 0)
						$color="#dddddd";
					else
						$color="#cccccc";
					echo "<tr><td bgcolor=".$color." align=center>Nuevo</td>";
					echo "<td bgcolor=".$color." align=center>&nbsp</td>";
					echo "<td bgcolor=".$color." align=center>&nbsp</td>";
					echo "<td bgcolor=".$color.">".$data[$j][0]."</td>";
					echo "<td bgcolor=".$color.">".$data[$j][1]."</td>";
					echo "<td bgcolor=".$color.">".$data[$j][9]."</td>";
					echo "<td bgcolor=".$color." align=right>".number_format((double)$data[$j][10],2,'.',',')."</td>";
					?>
					<script>
						function ira(){document.inventario.cantidad.focus();}
					</script>
					<?php
					echo "<td bgcolor=".$color." align=center><input type='TEXT' name='cantidad' size=7 maxlength=7  onkeypress='teclado()'></td>";
					echo "<td bgcolor=".$color." align=center>".$data[$j][3]."%</td>";
					$conv=0;
					if(substr($wcont,0,strpos($wcont,"-")) != "0")
					{
						$query = "select Conaca, Conaco   from ".$empresa."_000008 where Concod='".substr($wcont,0,strpos($wcont,"-"))."'";
						$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
						$row = mysql_fetch_array($err);
						if($row[0] == "off" and $row[1] == "off")
						{
							$wpv=substr($wprovt,0,strpos($wprovt,"-"));
							$query = "SELECT  Covval, Covfin, Covffi   from ".$empresa."_000043 ";
							$query .="  where Covpro='".$wpv."'";
							$query .="    and Covart='".$wbus."'";
							$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
							$num = mysql_num_rows($err);
							if($num > 0)
							{
								$row = mysql_fetch_array($err);
								if(date("Y-m-d") >= $row[1] and date("Y-m-d") <= $row[2])
									$conv=$row[0];
							}
						}
					}
					if(isset($fvnl) and $fvnl == "on")
					{
						if($conv == 0)
						{
							echo "<td bgcolor=".$color." align=center><input type='TEXT' name='data[".$j."][5]' size=7 maxlength=18 onkeypress='teclado1()'></td>";
						}
						else
						{
							echo "<td bgcolor=".$color." align=center><input type='TEXT' name='data[".$j."][5]' size=7 value ".number_format((double)$conv,2,'.','')." maxlength=18 onkeypress='teclado1()'></td>";
						}
						echo "<td bgcolor=".$color." align=center>&nbsp</td>";
						echo "<td bgcolor=".$color." align=center>&nbsp</td>";
						echo "<td bgcolor=".$color." align=center><input type='TEXT' name='data[".$j."][7]' size=10 maxlength=10 value='".$data[$j][7]."' onkeypress='teclado3()'></td>";
						echo "<td bgcolor=".$color." align=center><input type='TEXT' name='data[".$j."][8]' size=15 maxlength=15 value='".$data[$j][8]."' onkeypress='teclado2()'></td>";
						$query = "SELECT Karvuc  from ".$empresa."_000007 where Karcod='".$data[$i][0]."' and Karcco='".substr($wccoo,0,strpos($wccoo,"-"))."'";
						$err1 = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
						$num1 = mysql_num_rows($err1);
						if ($num1>0)
						{
							$row1 = mysql_fetch_array($err1);
							$wvaluc=$row1[0];
						}
						else
							$wvaluc=0;
						echo "<td bgcolor=".$color." align=right>$".number_format((double)$wvaluc,2,'.',',')."</td></tr>";
					}
					else
					{
						if($conv == 0)
						{
							echo "<td bgcolor=".$color." align=center><input type='TEXT' name='data[".$j."][5]' size=7 maxlength=18 onkeypress='teclado1()'></td>";
							echo "<td bgcolor=".$color." align=center>&nbsp</td>";
						}
						else
						{
							echo "<td bgcolor=".$color." align=center><input type='TEXT' name='data[".$j."][5]' size=7 value ".number_format((double)$conv,2,'.','')." maxlength=18 onkeypress='teclado1()'></td>";
						}
						echo "<td bgcolor=".$color." align=center>&nbsp</td>";
						echo "<td bgcolor=".$color." align=center>".$data[$j][7]."</td>";
						echo "<td bgcolor=".$color." align=center>".$data[$j][8]."</td>";
						$query = "SELECT Karvuc  from ".$empresa."_000007 where Karcod='".$data[$i][0]."' and Karcco='".substr($wccoo,0,strpos($wccoo,"-"))."'";
						$err1 = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
						$num1 = mysql_num_rows($err1);
						if ($num1>0)
						{
							$row1 = mysql_fetch_array($err1);
							$wvaluc=$row1[0];
						}
						else
							$wvaluc=0;
						echo "<td bgcolor=".$color." align=right>$".number_format((double)$wvaluc,2,'.',',')."</td></tr>";
					}
				}
			}
			// ******************* FIN DE ENTRADA DE ARTICULOS NUEVOS A LA LISTA DE SELECCIONADOS **********************
			for ($i=0;$i<=$j;$i++)
			{
				echo "<input type='HIDDEN' name= 'data[".$i."][0]' value='".$data[$i][0]."'>";
				echo "<input type='HIDDEN' name= 'data[".$i."][1]' value='".$data[$i][1]."'>";
				echo "<input type='HIDDEN' name= 'data[".$i."][3]' value='".$data[$i][3]."'>";
				echo "<input type='HIDDEN' name= 'data[".$i."][9]' value='".$data[$i][9]."'>";
				echo "<input type='HIDDEN' name= 'data[".$i."][10]' value='".$data[$i][10]."'>";
				if(isset($data[$i][2]) and (!isset($position) or (isset($position) and $position != $i)))
				{
					if(!isset($data[$i][4]))
						$data[$i][4]=0;
					if(!isset($data[$i][5]))
						$data[$i][5]=0;
					if(!isset($data[$i][6]))
						$data[$i][6]=0;
					echo "<input type='HIDDEN' name= 'data[".$i."][2]' value=".$data[$i][2].">";
					echo "<input type='HIDDEN' name= 'data[".$i."][3]' value=".$data[$i][3].">";
					echo "<input type='HIDDEN' name= 'data[".$i."][4]' value=".$data[$i][4].">";
					echo "<input type='HIDDEN' name= 'data[".$i."][5]' value=".$data[$i][5].">";
					echo "<input type='HIDDEN' name= 'data[".$i."][6]' value=".$data[$i][6].">";
					echo "<input type='HIDDEN' name= 'data[".$i."][7]' value=".$data[$i][7].">";
					echo "<input type='HIDDEN' name= 'data[".$i."][8]' value=".$data[$i][8].">";
				}
				elseif($fvnl == "off")
				{
					echo "<input type='HIDDEN' name= 'data[".$i."][7]' value=".$data[$i][7].">";
					echo "<input type='HIDDEN' name= 'data[".$i."][8]' value=".$data[$i][8].">";
				}
			}
			echo"</table>";
		}
		echo "<input type='HIDDEN' name= 'j' value='".$j."'>";
		echo "<input type='HIDDEN' name= 'west' value='".$west."'>";
	}
	else
	{
		echo "CIERRE GENERADO <BR>";
		echo "<input type='HIDDEN' name= 'ok' value='".$ok."'>";
		echo "<br><input type='submit' value='CONTINUAR'>";
	}
}
?>
</body>
</html>
