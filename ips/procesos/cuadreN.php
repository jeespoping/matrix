<html>
<head>
  <title>MATRIX - [CUADRE DE CAJA]</title>

   <script type="text/javascript">
   function enter(vari, bandera)
   {
	   if(!vari && !bandera){
	   		var tipoReporte = document.forms.forma1.wtiporep;
	   		var valido = false;

	   		if(tipoReporte[0].checked){ //Por numero de cuadre
				if(document.forma1.consulta && document.forma1.consulta.value != ''){
					valido = true;
				} else {
					alert("Debe ingresar un número de cuadre");
					return;
				}
			}

			if(tipoReporte[1].checked){ //Por fecha de cuadre
				if(document.forma1.wfecini && document.forma1.wfecini.value != '' && document.forma1.wfecfin && document.forma1.wfecfin.value != ''){
					if(document.forma1.consulta){
						document.forma1.consulta.value = document.forma1.wfecini.value + "*" + document.forma1.wfecfin.value;
					}
					valido = true;
				} else {
					alert("Debe ingresar la fecha inicial y final de los cuadres");
					return;
				}
			}
	   	} else {
   			document.forma1.numCua.value=vari;
   			document.forma1.bandera1.value=bandera;
	   	}
   		document.forma1.submit();
   }

   function enter1()
   {
   	document.forma1.submit();
   }

   function enter2(vari, bandera)
   {
   	document.forma1.numCua.value=document.forma1.consulta.value;
   	document.forma1.submit();
   }

function irACuadre(consecutivo){
	document.forms.forma1.wtiporep.value = "n";
	document.forms.forma1.consulta.value = consecutivo;
	document.forma1.submit();
}

function cambiarTamanoLetra(tamanoNuevo){
	var cells = document.getElementsByTagName("font");
	var elemento = "", tamano = "";
	for (var i = 0; i < cells.length; i++) {
		cells[i].setAttribute("size",tamanoNuevo);
	}

}
</script>
</head>

<body>

<?php
include_once("conex.php");
/**
 *====================================================================================================================================================
 * ACTUALIZACIONES
 *====================================================================================================================================================
 *====================================================================================================================================================
 * Agosto 2 de 2013 - Juan C. Hernandez
 *====================================================================================================================================================
 * Se adiciona parametro de seguridad para evitar inyeccion de codigo ===> $seguridad=on
 *====================================================================================================================================================
 * Noviembre 9 de 2009
 *====================================================================================================================================================
 * Se modifica la funcion consultarDocumentosNuevos(), porque cuando se hacen traslados de una caja a otra solo buscaba documentos hechos en la caja
 * del usuario que esta tratando de hacer el cuadre, pero si son documentos trasladados de otra caja no los mostraba por el cco. se cambio la variable
 * $cco por $registro['cco'][$i]=$row[5] en la linea 584.
 *====================================================================================================================================================
 * Julio 8 de 2010
 *====================================================================================================================================================
 * Se corrige query de confirmar password, para que no se puede cuadrar la caja con clave de otro.
 *====================================================================================================================================================
 */


/**
 * PROGRAMA PRINCIPAL DE CUADRE DE CAJA
 *
 * Permite cuadrar una caja del POS o de IPS, basado en los recibos y devoluciones de dinero a esos recibos existenetes en estado S(sin cuadrar),
 * P (Que quedaron pendientes en otro cuadre), I(quedaron parcialmente cuadrados en otro cuadre). De esta manera queda registro del dia,
 * la hora y el dinero que pudo ser cuadrado para una caja, quedando los recibos y devoluciones de dinero en estado C (cuadrado),
 * P o I despues del cuadre, segun corresponda. (para mayor detalle de las devoluciones de dinero remitase a: devolucion.php del POS)
 *
 * Tambien actua como reporte para la consulta de un cuadre previamente realizado por la caja del usuario,
 * desplegando la informacio tras ingresar el numero del cuadre y permitiendo su impresion en impresora tipo POS
 *
 * Tambien puede ser llamado desde otro programa para consultar de forma detallada o resumida un cuadre determinado de una caja determinada
 * En este caso pide como parametros el centro de costos, la caja, el numero de cuadre y el tipo de consulta, posteriormente da la opcion
 * para impresion con impresora tipo POS
 *
 * MODO DE FUNCIONAMIENTO:
 *
 *  Cuando es la primera vez que se entra al programa, se consulta la caja del usuario y como no esta setiado ni consulta
 *  ni bandera, el programa lo que hace es que busca para la caja los registros de los recibos y las devoluciones que esten en la tabla de pendiente
 *  (para documentos que venian quedando pendientes o incompletos de otros cuadres)con la funcion consultarDocumentosAnteriores y
 *  los que se encuentren en estado S, es decir que no venian de cuadres anteriores, mediante la funcion consultarDocumentosNuevos,
 *  con ellos organiza dos vectores clasificados por forma de pago ([$i]). Cuando hay varios registros en la tabla 22 para una forma de pago,
 * 	realiza la sumatoria de los valores, para tratarlo como un solo registro. Las funciones se invocan por forma de pago y retornan un vector llamado registro['nombre del detalle'][contador de documentos]
 *  los cuales posteriormente se organizan en vectores con el nombre del detalle del documento por ejemplo fuente[contador forma de pago][contador de documentos]
 *  Posteriormente a cada unos de esos vectores si existe les calcula
 *  los totales para cada forma de pago y el total para cada caso del vector, se calcula: el valor de recibos,
 *  valor de devoluciones, valor a egresar e inicializa los saldos en cero. Luego manda o organiza todos los valores de los recibos y los
 *  totales en hidden para no hacer mas consultas a la db.  Se pinta un resumen de los totales por forma de pago para cada vector con la funcion
 *  pintarResumido. adicionalmente el valor continuar, para seguir con el cuadre
 *
 *  Una vez presionado el boton continuar, la variable bandera tiene el valor de uno, por lo que el programa en este caso recalcula los totales
 *  de los vectores recibidos por hidden y el saldo fijandose cuales tienen activado el checkbox y los valores parciales ingresados. Una
 *  vez calculados los totales pinta los vectores de forma detallada mediante la funcion pintarDetallado. Cabe anotarse que cuando
 *  no se encuentran bancos para los documentos en la tabla 22 la funcion pintarDetallado pinta el numero de la venta y de la factura
 *  para cada documento (recibos o devoluciones) los cuales son hipervinculos que permiten la cosnulta de la devolucion, cuando si hay
 *  en esa parte de la tabla pinta la lista de bancos a los que se ha llevado esa forma de pago para ese recibo (recordemos que pueden haber
 *  varios bancos si para esa forma de pago del recibo existen varios registros en la tabla 22). Los bancos desplegados son hipervinculos que
 *  habren el programa cambioBanco.php, el cual recibe como parametros el id en la tabla 22, la fuente y numero del recibo, forma de pago
 * 	y descripcion de la forma de pago. Si mediante el programa de cambio de banco se hace una modificacion del banco, hace reload sobre este
 *  programa mandando la variabe bander en 1 y una variable llamada cambvio banco, en este caso este programa hace lo ya esplicado, pero no pinta
 *  los bancos que ha recibido por hidden sino que los vuelve a consultar para captar las modificaciones, mediante la funcion consultarBancos.
 *  Tambien la funcion pintarDetallado si el tipo de la forma de pago esta en on, pinta una entrada de texto para permitir cuadres parciales,
 *  si esta en off, no-
 *  Al final cuando bandera es igual a uno, el programa pinta el checkbox para grabar el cuadre y los cmapos para ingresar la observacion y
 *  la clave, mas el boton continuar. Si se da solo el boton continuar, el programa hace los ya mencionado cuando bandera es igual a 1. Cuando
 * se da continuar con el checkbox activado, el programa despues de calcular los totales nuevo, valida que los saldos no sean mayores que
 *  los valores a engresar en cuyo caso ya no graba y pinta en rojo los valores erroneos, valida que el valor a egresar sea positivo y valida el
 *  password seleccionado, si se han pasado todas las validaciones, entonces realiza la grabacion de la siguiente forma:
 *
 * aumenta el consecutivo del cuadre para la caja
 * graba el encabezado del cuadre en tabla 73
 * graba el detalle para cada documento en la 74
 * si el recibo queda pendiente o incompelto se guarda en la tabla 37 si es nuevo o actualiza si venia de tablas anteriores,
 * si el recibo venia de tablas anteriores y se cuadra todo, se borra de la tabla 37 de pendiente
 * se cambia el estado en la 22 en I, P o C, segun corresponda
 *
 * Una vez el programa graba, realza la consulta de los guardado para garantizar que todo ha quedado bien almacenado. Para ello
 * utiliza las funciones consutarRegistros, la cual mediente el parametro tipo sabe si debe buscar los documentos del cuadre que
 * venian de otrso cuadres o los que son nuevos. Invocando la fucnion de las dos formas se vuelve a armar los dos vectores,
 * posteriormente se vuelven a clacular los totales, armar los hidden y se pinta de forma detallada los registros. esta vez la funcion
 * pintarDetallado tiene un parametro clase que le dice que no pinte los bancos asi los alla sino el numero de la venta y la factura.
 * Adicionalmente se pinta las opciones para imprimir (en impresora tipo POS) o para anular el cuadre.
 *
 * Cuando se realza una busqueda de un cuadre con el programa, se manda la variable consulta que contienen el numero del cuadre buscado,
 * en este caso el sistema utiliza las funciones consutarRegistros,
 * la cual mediente el parametro tipo sabe si debe buscar los documentos del cuadre que venian de otrso cuadres o los que son nuevos.
 * Invocando la fucnion de las dos formas se vuelve arman los dos vectores, posteriormente se calculan los totales,
 * arman los hidden y se pinta de forma detallada los registros. esta vez la funcion pintarDetallado tiene un parametro clase
 * que le dice que no pinte los bancos asi los alla sino el numero de la venta y la factura. Adicionalmente se pinta las opciones
 * para imprimir (en impresora tipo POS) o para anular el cuadre.
 *
 * Cuando se invoca desde otro programa para consultar un cuadre, se manda la variable consulta que contienen el numero del cuadre buscado,
 * el centro de costos, la caja y el tipo de consulta (detallado o resumido). en este caso el sistema utiliza las funciones consutarRegistros,
 * la cual mediente el parametro tipo sabe si debe buscar los documentos del cuadre que venian de otrso cuadres o los que son nuevos.
 * Invocando la fucnion de las dos formas se vuelve arman los dos vectores, posteriormente se calculan los totales,
 * arman los hidden y se pinta de forma detallada los registros o resumida, segun se indique. Esta vez la funcion pintarDetallado
 * tiene un parametro clase que le dice que no pinte los bancos asi los alla sino el numero de la venta y la factura. Adicionalmente se pinta
 * las opciones para imprimir (en impresora tipo POS) o para anular el cuadre si el usuario pertences a la misma caja del cuadre.
 *
 * Cuando se selecciona la opcion anular, se manda la variable bandera con valor igual a 2, en este caso primero mediante la funcion
 * consultarPermiso si ningun documento del cuadre ha sido trasladado, si no se ha vencido el periodo contable y si es el ultimo cuadre.
 * Si cumple con las validaciones, entonces se realiza el siguiente proceso de anulacion:
 * se anula registro por registro del cuadre
 * si el registro no estaba pendiente en otros cuadres y en este quedo pendiente, se borra de la tabla de cuadres
 * si el registro venia de otros cuadres, se acutaliza la tabla de pendientes con el valor que habia quedado pendiente
 * se cambia el estado del documento en la tabla 000022 de acuerdo a si venia (S) o no se otros cuadres y en caso de que si segun si el valor pendiente era igual al valor o no del documento (P o I)
 * se anula el encabezado del cuadre. Finalmente se despliega si fue exitoso o no la anulacion del cuadre
 *
 * Cuando se selecciona la impresion el programa recibe la variable bandera con valor 3. En este caso vuelve a consulta el cuadre
 * Para ello utiliza las funciones consutarRegistros, la cual mediente el parametro tipo sabe si debe buscar los documentos del cuadre que
 * venian de otrso cuadres o los que son nuevos. Invocando la fucnion de las dos formas  arma los dos vectores,
 * posteriormente se vuelven a calcular los totales, y se pinta de forma resumida los valores de cada vector para cada forma de pago.
 * Adicionalmente se imprimen los totales generales del cuadre. Esto se hace mediante las funciones imprimirGrande (imprime encabezado),
 * imprimirTotales e imprimirResumido, que permiten la impresion en impresota tipo POS.
 *
 *
 * @author Carolina Castaño P
 * @version 2007-02-12
 * @created 2007-02-12.
 *
 * @table 000020 SELECT
 * @table 000021 SELECT
 * @table 000022 SELECT, UPDATE
 * @table 000023 SELECT
 * @table 000028 SELECT, UPDATE
 * @table 000030 SELECT
 * @table 000037 INSERT, DELETE, UPDATE
 * @table 000040 SELECT
 * @table 000069 SELECT
 * @table 000073 SELECT, UPDATE, INSERT
 * @table 000074 SELECT, UPDATE, INSERT
 * @table usuarios SELECT
 *
 * @wvar $aviso indica cuando algun registro tiene un valor a egresar mayor al valor pendiente por cuadrar
 * @wvar $bancos vector tridimensional de los bancos (forma de pago, documento, nombre o codigo del banco) para recibos nuevos
 * @wvar $bancosA vector tridimensional de los bancos (forma de pago, documento, nombre o codigo del banco) para recibos que vienen de otros cuadres
 * @wvar $bandera1 indica que actividad se hara en el programa, carga inicial (NULL), pintar detallado (1), anular (2), pintar impresion (3)
 * @wvar $cajCod, codigo de la caja del cuadre+
 * @wvar $cajCod2, codigo de la caja del cuadre
 * @wvar $cajDes descripcion de la caja del cuadre
 * @wvar $cajDes2 descripcion de la caja de usuario
 * @wvar $egresado valor total egresado en un caudre
 * @wvar $egresar vector con valores a egresar por forma de pago para recibos nuevos
 * @wvar $egresarA vector con valores a egresar por forma de pago para recibos que vienen de otros cuadres
 * @wvar $egresarF valor egresado final para vector de documentos nuevos
 * @wvar $egresarFA  valor egresado final para vector de documentos que vienen de otros cuadres
 * @wvar $egresarFG
 * @wvar $egresarFGA
 * @wvar $egresarG
 * @wvar $egresarGA
 * @wvar $egresarT
 * @wvae $e
 *
 */
//////////////////////////////////////////////////////////////FUNCIONES//////////////////////////////////////////////////////////////////
class EncabezadoCuadre{
	var $fechaCuadre = "";
	var $horaCuadre = "";
	var $numeroCuadre = "";
}

/***********************************************FUNCIONES DE PERSISTENCIA*************************************/

/**************************************************************************************************************
 * verifica que una clave si este registrada para el usuario en la tabla de usuarios
 *
 * @param unknown_type $pass, password
 * @param unknown_type $wusuario usuario
 * @return unknown boolean true or false
 **************************************************************************************************************/
function confirmarPassword($pass, $wusuario)
{
	global $conex;
	global $wbasedato;
	global $usu;
	global $clv;

	if( isset( $clv ) ){
		$q="SELECT Cjeusu "
		."FROM ".$wbasedato."_000030 "
		."WHERE Cjecla='$pass' "
		."  AND Cjeusu='$wusuario' ";
	}
	else{
		$q="SELECT Cjeusu "
		."FROM ".$wbasedato."_000030 "
		."WHERE Cjecla='$pass' ";
	}

	$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($err);

	if ($num > 0)
	{
		$row=mysql_fetch_row($err);
		$usu=$row[0];
		//if (strtoupper($wusuario)==strtoupper($usu))
		//{
			return true;
		//}
	}
	return false;
}

/**
 * consulta la caja de un usuario determinado  y los detalles de la caja (codigo, descripcion, consecutivo del cuadre)
 *
 * @param unknown_type $user, usuario
 * @param unknown_type $cco, centro de costos
 * @param unknown_type $cajCod, se retorna codigo de caja
 * @param unknown_type $cajDes, se retorna descripcion de la caja
 * @param unknown_type $cuaAnt, se retorna consecutivo de cuadre que lleva la caja
 * @param unknown_type $resp, se retorna responsable o nombre del usuario
 */
function consultarCaja($user, &$cco, &$cajCod, &$cajDes, &$cuaAnt, &$resp)
{
	//extrae los datos de la caja a la que pertenece el usuario

	global $conex;
	global $wbasedato;

	$q = "select Cjecco, Cjecaj, Cajcod, Cajdes, Cajcua, descripcion "
	    ."  from ".$wbasedato."_000030, ".$wbasedato."_000028, usuarios "
	    ." where Cjeusu = '".$user."' "
	    ."   and Cajcod = MID(Cjecaj, 1,instr(Cjecaj,'-')-1) "
	    ."   and cjeusu = codigo "
	    ."   and activo = 'A' ";
//	    ."   and mid(cjecco,1,instr(cjecco,'-')-1)=cajcco "; echo ".........".$q;
    $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($err);

	if ($num > 0)
	{
		$row=mysql_fetch_array ($err);
		//$cco = substr($row['Cjecco'],0,4);
		$cco = substr($row['Cjecco'],0,strpos($row['Cjecco'],'-'));
		$cajCod = $row['Cajcod'];
		$cajDes = $row['Cajdes'];
		$cuaAnt = $row['Cajcua'];
		$resp = $row['descripcion'];
	}
}

/**
 * Consulta en el encabezado de un caudre quien lo realizo
 *
 * @param unknown_type $cco, centro de costos del cuadre
 * @param unknown_type $cajCod, codigo de la caja del cuadre
 * @param unknown_type $consulta, numero del cuadre
 * @param unknown_type $resp retorna el nombre del responsable del cuadre
 */
function consultarResponsable( $cco, $cajCod, $consulta, &$resp)
{
	//extrae los datos de la caja a la que pertenece el usuario

	global $conex;
	global $wbasedato;

	$q = "SELECT Seguridad "
	."FROM	".$wbasedato."_000073 "
	."WHERE	Cencaj = '".$cajCod."' "
	."AND	cencco= '".$cco."' and  cencua='".$consulta."' ";
	$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	$num = mysql_num_rows($err);

	if ($num > 0)
	{
		$row=mysql_fetch_array($err);
		$exp=explode ('-',$row['Seguridad']);

		$q="select descripcion "
		."from usuarios "
		."where	codigo = '".trim($exp[1])."' ";

		$err2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num2 = mysql_num_rows($err2);

		if ($num2 > 0)
		{
			$row2=mysql_fetch_array ($err2);
			$resp = trim($exp[1]).'-'.$row2['descripcion'];
		} else {
			$resp = trim($exp[1]);
		}
	} else {
		$resp = '';
	}
}

/**
 * Consulta las formas de pago existentes
 *
 * @return unknown vector con formas de pago
 */
function consultarPagos()
{
	global $conex;
	global $wbasedato;

	$q="select fpacod, fpades, fpamcu "
	."from	".$wbasedato."_000023 "
	."order by fpacod";

	$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($err);

	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$pagos['codigo'][$i]=$row[0];
			$pagos['nombre'][$i]=$row[1];
			$pagos['tipo'][$i]=$row[2];
		}
		return $pagos;
	} else {
		return false;
	}
}

/**
 * Consulta todos los documentos que vienen pendientes de otros cuadres y todos sus detalles,los entrega en la variable registro
 *
 * @param unknown_type $codPago, codigo de la forma de pago
 * @param unknown_type $cajCod, codigo de la caja
 * @param unknown_type $cco, centro de costos
 * @return unknown
 */
function consultarDocumentosAnteriores($codPago, $cajCod, $cco)
{
	//consulta los recibos que estan pendientes de cuadrar, por que se quedaron en otros cuadres
	//unicamente debo ir a a la tabla temporal de recibos por cuadrar y consultar el resto de datos de la tabla 22

	global $conex;
	global $wbasedato;

	$q=" select cdefue, cdenum, cdevrf "
	  ."   from ".$wbasedato."_000037 "
	  ."  where cdecco = '".$cco."' "
	  ."    and cdecaj = '".$cajCod."' "
	  ."    and cdefpa = '".$codPago."' "
	  ."    and cdepen ='on' "
	  ."  order by cdefue, cdenum ";
	$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($err);

	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$registro['fuente'][$i]=$row[0];
			$registro['numero'][$i]=$row[1];
			$registro['valorP'][$i]=$row[2];

			//consulto y sumo los pagos con esa forma de pago para ese documento
			$q=" select Count(*) as nBa, sum(rfpvfp),fecha_data, seguridad, rfpcco "
			  ."   from	".$wbasedato."_000022 "
			//."where rfpcco= '".$cco."' "
			  ."  where	rfpfue = '".$registro['fuente'][$i]."' "
			  ."    and rfpnum = '".$registro['numero'][$i]."' "
			  ."    and rfpfpa = '".$codPago."'"
			  ."    and rfpcaf = '".$cajCod."'"
			  ."  GROUP BY rfpnum, rfpfue, rfpcco "
			  ."  ORDER BY rfpfue, rfpnum ";

			$err2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num2 = mysql_num_rows($err2);
			$row2 = mysql_fetch_array($err2);

			$registro['numeroBancos'][$i]=$row2[0];
			$registro['valorS'][$i]=$row2[1];
			$registro['valor'][$i]=$row2[1];
			$registro['fecha'][$i]=$row2[2];
			$registro['ccoOrigen'][$i]=$row2[4];		//Nov 18
			$exp=explode('-',$row2[3]);
			$registro['responsable'][$i]=$exp[1];

			$q=" select rfpban, bannom, ".$wbasedato."_000022.id "
			  ."   from	".$wbasedato."_000022, ".$wbasedato."_000069 "
			  //."  where rfpcco= '".$cco."' "
			  ."  where	rfpfue = '".$registro['fuente'][$i]."' "
			  ."    and rfpnum = '".$registro['numero'][$i]."' "
			  ."    and rfpfpa = '".$codPago."'"
			  ."    and bancod = rfpban "
			  ."    and rfpcaf = '".$cajCod."'";
            $res3 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num3 = mysql_num_rows($res3);

			if ($num3>0)
			{
				for ($j=0;$j<$num3;$j++)
				{
					$row3 = mysql_fetch_array($res3);
					$registro['banco'][$i][$j]=$row3[0]."-".$row3[1];
					$registro['id'][$i][$j]=$row3[2];
				}
			}
			else
			{
				$registro['banco'][$i][0]='';
				$registro['id'][$i][0]='';
				$registro['numeroBancos'][$i]=0;
			}

			//consulto la fuente para devoluciones de dinero
			$q="SELECT Carfue "
			  ."  FROM ".$wbasedato."_000040 "
			  ." WHERE Cardvt = 'on' "
			  ."   and Carest = 'on'";
			$err3 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num3=mysql_num_rows($err);
			if ($num3>0)
			{
				$row3=mysql_fetch_row($err3);
				if ($row3[0]==$registro['fuente'][$i]) //fuente de la transaccion
				{
					$registro['numeroDevoluciones'][$i]='-';
				}
				else
				{
					$registro['numeroDevoluciones'][$i]='+';
				}
			}
			else
			{
				$registro['numeroDevoluciones'][$i]='+';
			}

			//consulto la venta para el recibo
			$q="select rdevta, rdeffa, rdefac "
			  ."  from	".$wbasedato."_000021, ".$wbasedato."_000022 "
			  ." where rdecco = rfpcco "
			  ."   and rdefue = '".$registro['fuente'][$i]."' "
			  ."   and rdenum = '".$registro['numero'][$i]."'"
			  ."   and rdefue = rfpfue "
			  ."   and rdenum = rfpnum "
			  ."   and rfpcaf = '".$cajCod."'";
			$err4 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num4=mysql_num_rows($err4);
			$row4=mysql_fetch_row($err4);
			$registro['ven'][$i]=$row4[0]; //venta del recibo

			If ($registro['ven'][$i]!='')
			{
				if ($row4[1]=='')
				$row4[1]='X';
				if ($row4[2]=='')
				$row4[2]='X-X';

				$registro['ven'][$i]=$row4[0].'/'.$row4[1].'-'.$row4[2];
			}
			else
			{
				$registro['ven'][$i]=$row4[1].'-'.$row4[2];
			}
		}
		return $registro;
	}else
	{
		return false;
	}
}

/**
 * consulta los bancos para cada uno de los documentos de un vector metiendoles de forma tridimensional en el vector bancos
 * la primera indica la forma de pago del docuemento, la segunda el documento, y la tercera la numeracion de los bancos
 *
 * @param unknown_type $pagos, vector con formas de pago
 * @param unknown_type $fuente, vecto de fuentes de los documentos
 * @param unknown_type $numero, vector con el numero de documentos
 * @param unknown_type $cco, centro de costos del documento
 * @return unknown retorna el vector de bancos
 */
function consultarBancos($pagos, $fuente, $numero, $cco)
{
	global $conex;
	global $wbasedato;

	for ($i=0;$i<count($pagos['codigo']);$i++)
	{
		if (isset($fuente[$i]))
		{
			for ($j=0;$j<count($fuente[$i]);$j++)
			{
				$q="select rfpban, bannom "
				."from	".$wbasedato."_000022, ".$wbasedato."_000069 "
				."where rfpcco= '".$cco."' and	rfpfue = '".$fuente[$i][$j]."' "
				." and rfpnum = '".$numero[$i][$j]."' and rfpfpa = '".$pagos['codigo'][$i]."'"
				." and bancod = rfpban and banest = 'on' ";

				$res3 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$num3 = mysql_num_rows($res3);
				for ($k=0;$k<$num3;$k++)
				{
					$row3 = mysql_fetch_array($res3);
					$bancos[$i][$j][$k]=$row3[0]."-".$row3[1];
				}
			}
		}

	}
	return $bancos;
}

/**
 * Entrega los documentos nuevos (que no vienen de otros cuadres)para cuadrar y devuelve todos sus detalles en el vector registro
 *
 * @param unknown_type $codPago, codigo de la forma de pago
 * @param unknown_type $cajCod, codigo de la caja
 * @param unknown_type $cco, centro de costos
 * @return unknown
 */
function consultarDocumentosNuevos($codPago, $cajCod, $cco)
{
	//consulta los recibos que se encuentran en estado sin cuadrar o transladado (S o T)

	global $conex;
	global $wbasedato;

	$q="select rfpfue, rfpnum, sum(rdevca), sum(rfpdev), rfpecu, rfpcco "   //Nov 9 2009 rfpcco
		."from  ".$wbasedato."_000022, ".$wbasedato."_000021  "
	//."where	rfpcco = '".$cco."' "                                       //Nov 9 2009
	."   where	Rfpcaf = '".$cajCod."' "
	."     and	Rfpecu IN ('S','T') "
	."     and	rfpfpa = '".$codPago."' "
	."     and	rfpest = 'on' "
	."     and	rfpfue = rdefue "
	."     and	rfpnum = rdenum "
	."     and	rfpcco = rdecco "
	."   GROUP BY rfpnum, rfpfue, rfpcco "
	."   ORDER BY rdevta, rfpfue, rfpnum  ";
    $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($err);

	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$registro['fuente'][$i]=$row[0];
			$registro['numero'][$i]=$row[1];
			$registro['cco'][$i]=$row[5];                                    //Nov 9 2009

			//Se busca el valor total de la forma de pago
//			$sql = "SELECT
//						SUM(rfpvfp)
//					FROM
//						{$wbasedato}_000022,
//						{$wbasedato}_000021
//					WHERE
//						rfpnum = rdenum
//						AND rfpfue = rdefue
//						AND rdecco = rfpcco
//						AND rfpest = 'on'
//						AND rfpfpa = '$codPago'
//						AND rfpcco = '{$registro['cco'][$i]}'
//						AND rfpfue = '{$registro['fuente'][$i]}'
//						AND rfpnum = '{$registro['numero'][$i]}'
//					GROUP BY rfpnum, rfpfue, rfpcco
//					ORDER BY rdevta, rfpfue, rfpnum
//					";
//
//			$sql = "SELECT
//						SUM(rfpvfp), SUM(rfpdev)
//					FROM
//						{$wbasedato}_000022
//					WHERE
//						rfpest = 'on'
//						AND rfpfpa = '$codPago'
//						AND rfpcco = '{$registro['cco'][$i]}'
//						AND rfpfue = '{$registro['fuente'][$i]}'
//						AND rfpnum = '{$registro['numero'][$i]}'
//					GROUP BY rfpnum, rfpfue, rfpcco
//					ORDER BY rfpfue, rfpnum
//					"; echo "<br><br>Hola $i.........".$sql;
//
//			$res1 = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
//			$numRows = mysql_num_rows( $res1 );
//			if( $numRows > 0 ){
//				$rowsVfp = mysql_fetch_array( $res1 );
//				$row[2] = $rowsVfp[0];
//			}
//			else{
//				$row[2] = 0;
//			}

//			if($row[4]=='T')
//			{
//				$registro['valorP'][$i]=$row[2]-$row[3];
//			}
//			else
//			{
//				$registro['valorP'][$i]=$row[2];
//			}

			//consulto la fecha y responsable del recibo
			$q="select renfec, seguridad "
			."from	".$wbasedato."_000020 "
			."where rencco= '".$registro['cco'][$i]."' and	renfue = '".$registro['fuente'][$i]."' "
			." and rennum = '".$registro['numero'][$i]."' ";

			$err2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num2 = mysql_num_rows($err2);
			$row2 = mysql_fetch_array($err2);
			$registro['fecha'][$i]=$row2[0];
			$exp=explode('-',$row2[1]);
			$registro['responsable'][$i]=$exp[1];

			//consulto y sumo los pagos con esa forma de pago para ese recibo
			$q="select Count(*) as nBa, sum(rfpvfp), sum(rfpdev) "
			."from	".$wbasedato."_000022 "
			."where rfpcco= '".$registro['cco'][$i]."' and	rfpfue = '".$registro['fuente'][$i]."' "
			." and rfpnum = '".$registro['numero'][$i]."' and rfpfpa = '".$codPago."'"
			."GROUP BY rfpnum, rfpfue "
			."ORDER BY rfpfue, rfpnum ";

			$err2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num2 = mysql_num_rows($err2);
			$row2 = mysql_fetch_array($err2);

			$registro['numeroBancos'][$i]=$row2[0];
			if($row[4]=='T')
			{
				$registro['valorS'][$i]=$row2[1]-$row2[2];
				$registro['valor'][$i]=$row2[1]-$row2[2];
				$registro['valorP'][$i]=$row2[1]-$row2[2];	//2009-12-31
			}
			else
			{
				$registro['valorS'][$i]=$row2[1];
				$registro['valor'][$i]=$row2[1];
				$registro['valorP'][$i]=$row2[1];	//2009-12-31
			}

			$q="select rfpban, bannom, ".$wbasedato."_000022.id "
			."from	".$wbasedato."_000022, ".$wbasedato."_000069 "
			."where rfpcco= '".$registro['cco'][$i]."' and	rfpfue = '".$registro['fuente'][$i]."' "
			." and rfpnum = '".$registro['numero'][$i]."' and rfpfpa = '".$codPago."'"
			." and bancod = rfpban  ";

			$res3 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num3 = mysql_num_rows($res3);

			if ($num3>0)
			{
				for ($j=0;$j<$num3;$j++)
				{
					$row3 = mysql_fetch_array($res3);
					$registro['banco'][$i][$j]=$row3[0]."-".$row3[1];
					$registro['id'][$i][$j]=$row3[2];
				}
			}
			else
			{
				$registro['banco'][$i][0]='';
				$registro['id'][$i][0]='';
				$registro['numeroBancos'][$i]=0;
			}

			//consulto la fuente para devoluciones de dinero
			$q="SELECT Carfue "
			."FROM ".$wbasedato."_000040 "
			."WHERE	Cardvt = 'on' "
			."and	Carest = 'on'";
			$err3 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num3=mysql_num_rows($err);
			if ($num3>0)
			{
				$row3=mysql_fetch_row($err3);
				if ($row3[0]==$registro['fuente'][$i]) //fuente de la transaccion
				{
					$registro['numeroDevoluciones'][$i]='-';
				}
				else
				{
					$registro['numeroDevoluciones'][$i]='+';
				}
			}
			else
			{
				$registro['numeroDevoluciones'][$i]='+';
			}

			//consulto la venta para el recibo
			$q="select rdevta, rdeffa, rdefac "
			."from	".$wbasedato."_000021 "
			."where rdecco= '".$registro['cco'][$i]."' and	rdefue = '".$registro['fuente'][$i]."' "
			." and rdenum = '".$registro['numero'][$i]."'";
			$err4 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num4=mysql_num_rows($err4);
			$row4=mysql_fetch_row($err4);
			$registro['ven'][$i]=$row4[0]; //venta del recibo

			If ($registro['ven'][$i]!='')
			{
				if ($row4[1]=='')
				$row4[1]='X';
				if ($row4[2]=='')
				$row4[2]='X-X';

				$registro['ven'][$i]=$row4[0].'/'.$row4[1].'-'.$row4[2];
			}
			else
			{
				$registro['ven'][$i]=$row4[1].'-'.$row4[2];
			}
		}
		return $registro;
	}else
	{
		return false;
	}
}

function grabarEncabezado($numCua, $cajCod, $cco, $egresarF, $saldoF, $wusuario, $comentario)
{
	global $conex;
	global $wbasedato;

	$q= " INSERT INTO ".$wbasedato."_000073 (   Medico       ,   Fecha_data,   Hora_data,          cencua,             cencaj ,     cencco    ,    cenveg    ,     cenvns    ,   cencom ,         cenest    , Seguridad        ) "
	."     VALUES ('".$wbasedato."',   '".date('Y-m-d')."', '".(string)date("H:i:s")."'     ,'".$numCua."', '".$cajCod."', '".$cco."' , '".$egresarF."' ,  '".$saldoF."', '".$comentario."'  , 'on' ,  'C-".$wusuario."') ";


	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO GUARDAR EL ENCABEZADO PARA EL CUADRE DE CAJA ".mysql_error());
}

function grabarPendiente( $numCua, $cajCod, $cco, $numero, $fuente, $pago, $wusuario, $saldo)
{
	//consulto las formas de pago existentes para agrupar

	global $conex;
	global $wbasedato;

	$q= " INSERT INTO ".$wbasedato."_000037 (   Medico       ,   Fecha_data,   Hora_data,          cdecua ,       cdecaj  ,   cdecco   ,         cdefue,       cdenum,       cdefpa ,                 cdevrf,     cdeest, cdepen, Seguridad        ) "
	."         VALUES ('".$wbasedato."',   '".date('Y-m-d')."', '".(string)date("H:i:s")."' , '".$numCua."', '".$cajCod."', '".$cco."' , '".$fuente."' , '".$numero."',  '".$pago."'  ,   '".$saldo."' ,   'on' ,  'on', 'C-".$wusuario."') ";


	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO GRABAR EL RECIBO EN PENDIENTES PARA LA FORMA DE PAGO".mysql_error());
}

/**
 * Cambiar la tabla de pendientes con un nuevo saldo para un documento determinado, esto pasa generalmente
 * cuando se cuadra parcialmente un recibo que venia de cuadres anteriores
 *
 * @param unknown_type $numCua nuemero del cuadre
 * @param unknown_type $cajCod codigo de la caja del cuadre
 * @param unknown_type $cco centro de costos
 * @param unknown_type $numero nuemro del documento
 * @param unknown_type $fuente fuente del documento
 * @param unknown_type $pago forma de pago del documento
 * @param unknown_type $wusuario usuario que graba
 * @param unknown_type $saldo saldo a actualizar en tabla de pendientes
 */
function actualizarPendiente( $numCua, $cajCod, $cco, $numero, $fuente, $pago, $wusuario, $saldo)
{
	//consulto las formas de pago existentes para agrupar

	global $conex;
	global $wbasedato;

	$q= " update ".$wbasedato."_000037 set cdevrf='".$saldo."', Seguridad='C-".$wusuario."' "
	."     where cdecaj='".$cajCod."'  and  cdecco='".$cco."'   and  cdefue='".$fuente."' and  cdenum= '".$numero."'  and  cdefpa='".$pago."'  and  cdeest='on'  and  cdepen='on' ";

	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ACTUALIZAR EL RECIBO EN PENDIENTES PARA LA FORMA DE PAGO".mysql_error());
}

/**
 * Borra un registo de la tabla de pendientes, esto sucede cuando se cuadra totalmente un documento que venia de cuadres anteriores
 *
 * @param unknown_type $cajCod codigo de la caja del cuadre
 * @param unknown_type $cco centro de costos
 * @param unknown_type $numero numero del documento
 * @param unknown_type $fuente fuente del documento
 * @param unknown_type $pago forma de pago
 */
function borrarPendiente($cajCod, $cco, $numero, $fuente, $pago)
{
	//consulto las formas de pago existentes para agrupar

	global $conex;
	global $wbasedato;

	$q= " delete from ".$wbasedato."_000037 "
	."     where cdecaj='".$cajCod."'  and  cdecco='".$cco."'  and  cdefue='".$fuente."' and  cdenum= '".$numero."'  and  cdefpa='".$pago."'  and  cdeest='on' and cdepen='on' ";

	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO BORRAR EL RECIBO EN PENDIENTES PARA LA FORMA DE PAGO".mysql_error());
}


function grabarDetalle( $numCua, $cajCod, $cco, $numero, $fuente, $valor, $pago, $wusuario, $saldo, $valorP, $ccoOrigen )
{
	//consulto las formas de pago existentes para agrupar

	global $conex;
	global $wbasedato;

	$q= " INSERT INTO ".$wbasedato."_000074 (   Medico       ,   Fecha_data,   Hora_data,          cdecua ,       cdecaj  ,    cdecco  ,    cdefue,         cdenum ,        cdefpa ,       cdeveg,         cdevns    ,     cdevas    , cdeest, cdecor    ,  Seguridad        ) "
	."         VALUES ('".$wbasedato."',   '".date('Y-m-d')."', '".(string)date("H:i:s")."' , '".$numCua."', '".$cajCod."', '".$cco."' , '".$fuente."' , '".$numero."',  '".$pago."' ,  '".$valor."' ,  '".$saldo."' ,  '".$valorP."', 'on'  , '$ccoOrigen',  'C-".$wusuario."') ";

	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO GRABAR EL DETALLE DEL CUADRE PARA EL RECIBO Y LA FORMA DE PAGO".mysql_error());
}

/**
 * Consulta el encabezado para un numero de cuadre de una caja
 *
 * @param unknown_type $cajCod, codigo de la caja
 * @param unknown_type $numCua, numero del cuadre
 * @param unknown_type $cco, centro de costos
 * @param unknown_type $egresado, se entrega el total egresado del cuadre
 * @param unknown_type $nuevoSaldo, se entrega el saldo que dejo el cuadre
 * @param unknown_type $comentario, entrega el comentario del cuadres
 * @param unknown_type $fec, entrega la fecha del cuadre
 * @param unknown_type $hora, entrega la hora del cuadre
 * @param unknown_type $estado, entrega el estado del cuadre (si esta anulado o no)
 */
function consultarEncabezado($cajCod, $numCua, $cco, &$egresado, &$nuevoSaldo, &$comentario, &$fec, &$hora, &$estado )
{
	//consulto el encabezado de un trasnlado dado

	global $conex;
	global $wbasedato;

	$q="select cenveg, cenvns, cencom, fecha_data, hora_data, cenest "
	."from	".$wbasedato."_000073 "
	."where	cencua='".$numCua."' and cencaj='".$cajCod."' and cencco='".$cco."'  ";

	$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($err);

	if ($num>0)
	{
		$row = mysql_fetch_array($err);
		$nuevoSaldo=$row[1];
		$egresado=$row[0];
		$comentario=$row[2];
		$fec=$row[3];
		$hora=$row[4];
		$estado=$row[5];
	}else
	{
		$nuevoSaldo='';
		$egresado='';
		$comentario='';
		$fec='';
		$hora='';
		$estado='';
	}
}

/**
 * Consulta los registros de un cuadre y sus detalles, si el tipo es 1consulta los registros que no venian de otros cuadres en caso contrario los que si
 *	retorna varios vectores con los detalles cada vector tiene el primer numeral organizado por forma de pago, el segundo por documento
 *
 * @param unknown_type $cco, centro de costos del cuadre
 * @param unknown_type $cajCod, codigo de la caja
 * @param unknown_type $numCua, numero del cuadre
 * @param unknown_type $fuente, retorna vector de fuentes de los documentos encontrados
 * @param unknown_type $numero, retorna vector de numeros de los documentos encontrados
 * @param unknown_type $fecha, retorna vector con fecha de generacion del documento
 * @param unknown_type $valor, retorna vector con valores de los documentos
 * @param unknown_type $valorS, retorna vector con valor de los recibos (si es devoulcion el valor es cero)
 * @param unknown_type $valorP, retorna vector con valor pendiente por cuadrar del documento que dejo el cuadre
 * @param unknown_type $pagos, retorna vector con las foramas de pago que tienen registros en el cuadre
 * @param unknown_type $responsable, retorna el responsable de cada documento
 * @param unknown_type $numeroBancos, indica cuantos bancos hay por documento
 * @param unknown_type $numeroDevoluciones indica si es recibo (+) o devolucion (-) cada documento
 * @param unknown_type $valorDev, valor de las devoluciones, si es recibo se pone en cero
 * @param unknown_type $radio, se incializan en check
 * @param unknown_type $egresar, valor egresado en el cuadre por documento
 * @param unknown_type $saldo saldo que quedo de cada documento en el cuadre
 * @param unknown_type $tipo, indica si se consultan los que vienen  de otros cuadres o los que no (1)
 * @param unknown_type $radio2, se inicializa todo en check
 * @param unknown_type $ven, numero de la venta y factura de ese documento
 * @return unknown
 */
function consultarRegistros($cco, $cajCod, $numCua, &$fuente, &$numero, &$fecha, &$valor, &$valorS, &$valorP, &$pagos, &$responsable, &$numeroBancos, &$numeroDevoluciones, &$valorDev, &$radio,  &$egresar, &$saldo, $tipo, &$radio2, &$ven, &$ccoOrigen )
{
	//consulto los registros detalle de un encabezao dado y los organizo en vectores según la forma de pago
	global $conex;
	global $wbasedato;

	if ($tipo==1)
	{
		$q=" select A.cdefue, A.cdenum, A.cdeveg, A.cdefpa, A.cdevns, A.cdevas, B.fpades, cdecor "
			."from	".$wbasedato."_000074 A, ".$wbasedato."_000023 B "
			."where	 A.cdecua='".$numCua."' and A.cdecaj='".$cajCod."' and  A.cdecco='".$cco."' and A.cdevas=0 "
			." and B.fpacod=A.cdefpa order by cdefpa, A.id ";
	}
	else
	{
		$q=" select A.cdefue, A.cdenum, A.cdeveg, A.cdefpa, A.cdevns, A.cdevas, B.fpades, cdecor "
			."from	".$wbasedato."_000074 A, ".$wbasedato."_000023 B "
			."where	A.cdecua='".$numCua."' and A.cdecaj='".$cajCod."' and  A.cdecco='".$cco."' and A.cdevas<>0 "
			." and B.fpacod=A.cdefpa order by cdefpa, A.id ";
	}

	$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($err);
	$forma='';
	$contador=0;
	$rotador=0;

	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);

			if ($row[3] != $forma)// la idea es poder agrupar por formas de pago
			{
				if($i!=0)
				{
					$contador++;
					$rotador=0;
				}

				$total[$contador]=0;


				//leno vector pagos
				$pagos['codigo'][$contador]=$row[3];
				$pagos['nombre'][$contador]=$row[6];
				$forma=$row[3];

			}else if ($i!=0)
			{
				$rotador++;
			}

			//consulto la fecha y responsable del documento
			$q="select renfec, seguridad "
			."from	".$wbasedato."_000020 "
			."where rencco= '".$cco."' and	renfue = '".$row[0]."' "
			." and rennum = '".$row[1]."' ";

			$err2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num2 = mysql_num_rows($err2);
			$row2 = mysql_fetch_array($err2);
			$fecha[$contador][$rotador]=$row2[0];
			$responsable[$contador][$rotador]=$row2[1];

			//consulta el valor total de los documentos para la forma de pago
			$q="select sum(rfpvfp) "
			."from ".$wbasedato."_000022 "
			."where	rfpfue = '".$row[0]."' "
			."and	rfpnum = '".$row[1]."' "
			."and	rfpcco = '".$cco."' "
			."and	rfpfpa = '".$row[3]."' "
			."and	rfpest='on' ";

			$err2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num2 = mysql_num_rows($err2);
			$row2 = mysql_fetch_array($err2);

			$fuente[$contador][$rotador]=$row[0];
			$numero[$contador][$rotador]=$row[1];
			$valorS[$contador][$rotador]=$row2[0];
			$valor[$contador][$rotador]=$row2[0];
			$egresar[$contador][$rotador]=$row[2];

			//centro de costos origen
			$ccoOrigen[$contador][$rotador] = $row[7];

			$saldo[$contador][$rotador]=$row[4];
			$valorP[$contador][$rotador]=$row[4]+$row[2];
			$numeroBancos[$contador][$rotador]=0;

			if ($egresar[$contador][$rotador]>0)
			{
				$radio[$contador][$rotador]='checked';
			}
			else
			{
				$radio[$contador][$rotador]='';
			}

			$radio2[$contador][$rotador]='checked';

			//consulto la fuente para devoluciones de dinero
			$q="SELECT Carfue "
			."FROM ".$wbasedato."_000040 "
			."WHERE	Cardvt = 'on' "
			."and	Carest = 'on'";

			$err3 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num3=mysql_num_rows($err);

			if ($num3>0)
			{
				$row3=mysql_fetch_row($err3);
				if ($row3[0]==$fuente[$contador][$rotador]) //fuente de la transaccion
				{
					$numeroDevoluciones[$contador][$rotador]='-';
				}
				else
				{
					$numeroDevoluciones[$contador][$rotador]='+';
				}
			}
			else
			{
				$numeroDevoluciones[$contador][$rotador]='+';
			}

			//consulto la venta para el recibo
			$q="select rdevta, rdeffa, rdefac "
			."from	".$wbasedato."_000021 "
			."where rdecco= '".$cco."' and	rdefue = '".$fuente[$contador][$rotador]."' "
			." and rdenum = '".$numero[$contador][$rotador]."'";

			$err4 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num4=mysql_num_rows($err4);
			$row4=mysql_fetch_row($err4);
			$ven[$contador][$rotador]=$row4[0]; //venta del recibo

			If ($ven[$contador][$rotador]!='')
			{
				if ($row4[1]=='')
				$row4[1]='X';
				if ($row4[2]=='')
				$row4[2]='X-X';

				$ven[$contador][$rotador]=$row4[0].'/'.$row4[1].'-'.$row4[2];
			}
			else
			{
				$ven[$contador][$rotador]=$row4[1].'-'.$row4[2];
			}
		}
		return true;
	}else
	{
		return false;
	}
}

/**
 * Diciembre 02 de 2009
 * Se agrega el centro de costos, de lo contrario se genera problemas para cajas
 * distintas, con el mismo numero de recibo.
 *
 * Cambia el estado del documento por forma de pago, en la tabla 22
 *
 * @param unknown_type $numero, numero deo documento
 * @param unknown_type $fuente, fuente del documento
 * @param unknown_type $estado, estado que se va a poner en la tabla
 * @param unknown_type $caja, caja en la que se encuentra
 * @param unknown_type $pago, forma de pago
 */
function cambiarEstado($numero, $fuente, $estado, $caja,  $pago, $cco )
{
	//cambio el estado de los recibo

	global $conex;
	global $wbasedato;

	$q= "     UPDATE ".$wbasedato."_000022 "
	   ."        SET rfpecu = '".$estado."', "
	   ."            rfpcaf ='".$caja."' "
	   ."      WHERE rfpnum ='".$numero."' "
	   ."        and rfpfue ='".$fuente."' "
	   ."        and rfpest ='on' "
	   ."        and rfpfpa ='".$pago."' "
	   ."        AND rfpcaf = '".$caja."'"
	   ."        AND rfpcco = '".$cco."'";

	$err = mysql_query($q,$conex)or die (mysql_errno()." -NO SE HA PODIDO CAMBIAR EL ESTADO DEL RECIBO PARA LA FORMA DE PAGO ".mysql_error());
}

function incrementarConsecutivo($cajCod, $numCua)
{
	global $conex;
	global $wbasedato;

	$q = "LOCK table ".$wbasedato."_000028 LOW_PRIORITY WRITE";
	$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	/*CONSULTAR el numero de cuadre*/
	$q="select Cajcua from ".$wbasedato."_000028 "
	."WHERE	Cajcod = '".$cajCod."'";
	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR EL CONSECUTIVO PARA EL CUADRE DE CAJA ".mysql_error());

	$row = mysql_fetch_array($err);

	if ($numCua==$row[0]+1)
	{
		/*Aumentar el numero de cuadre*/
		$q="UPDATE ".$wbasedato."_000028 "
		."SET	Cajcua = Cajcua+1 "
		."WHERE	Cajcod = '".$cajCod."'";
		$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO INCREMENTAR EL CONSECUTIVO PARA EL CUADRE DE CAJA ".mysql_error());

		/*CONSULTAR el numero de cuadre*/
		$q="select Cajcua from ".$wbasedato."_000028 "
		."WHERE	Cajcod = '".$cajCod."'";
		$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR EL CONSECUTIVO PARA EL CUADRE DE CAJA ".mysql_error());

		$row = mysql_fetch_array($err);

		$q = " UNLOCK TABLES";
		$err = mysql_query($q,$conex)or die (mysql_errno()." -NO SE HA DESBLOQUEAR LA TABLA DE CAJEROS ".mysql_error());

		return $row[0];
	}
	else
	{
		return false;
	}
}

function consultarPermiso($pagos, $fuente, $numCua, $numero, $cajCod, $cco)
{
	global $conex;
	global $wbasedato;

	$permiso=true;

	/*CONSULTAR el numero de cuadre en que va la caja que realizo el cuadre*/
	$q="select Cajcua from ".$wbasedato."_000028 "
	."WHERE	Cajcod = '".$cajCod."'";
	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR EL CONSECUTIVO PARA EL CUADRE DE CAJA ".mysql_error());

	$row = mysql_fetch_array($err);

	if ($row[0]!=$numCua)
	{
		$permiso=false;
	}
	else
	{
		//valido si los recibos del cuadre no hayn sido trasladados o esten en otro estado o ya este en otra caja
		for ($i=0;$i<count($pagos['codigo']);$i++)
		{
			if (isset($fuente[$i]))
			{
				for ($j=0;$j<count($fuente[$i]);$j++)
				{
					$q="select rfpnum  from	".$wbasedato."_000022 "
					. " where	rfpfue = '".$fuente[$i][$j]."' and	rfpnum = '".$numero[$i][$j]."'  and	rfpcco = '".$cco."' "
					. " and	rfpfpa = '".$pagos['codigo'][$i]."' and rfpest = 'on' and rfpecu <> 'C' and rfpecu <> 'I' and rfpecu <> 'P'";
					$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$num = mysql_num_rows($err);

					if ($num>0)
					{
						$permiso=false;
					}

					$q="select rfpnum  from	".$wbasedato."_000022 "
					. " where	rfpfue = '".$fuente[$i][$j]."' and	rfpnum = '".$numero[$i][$j]."'  and	rfpcco = '".$cco."' "
					. " and	rfpfpa = '".$pagos['codigo'][$i]."' and rfpest = 'on' and rfpcaf<>'".$cajCod."'  ";
					$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$num = mysql_num_rows($err);

					if ($num>0)
					{
						$permiso=false;
					}

				}
			}
		}

		//valido que el cuadre no hay cambiado de periodo contable
		$q = " SELECT fecha_data FROM ".$wbasedato."_000073 WHERE cencua = '".$numCua."' AND cencaj = '".$cajCod."'  AND cencco = '".$cco."' and cenest='on' ";
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$row = mysql_fetch_array($res);

		$exp=explode('-', $row[0]);
		$exp2=explode('-', date('Y-m-d'));
		if ($exp[0]!=$exp2[0] or $exp[1]!=$exp2[1])
		{
			$permiso=false;
		}
	}

	return $permiso;
}

/**
 * Pone en off el estado de la tabla 74 que corresponde a cada uno de los recibos por forma de pago del cuadre
 *
 * @param unknown_type $numCua numero del cuadre
 * @param unknown_type $cajCod codigo de la caja del cuadre
 * @param unknown_type $numero numero del documento
 * @param unknown_type $fuente fuente del documento
 * @param unknown_type $pago forma de pago
 * @param unknown_type $cco centro de costos
 */
function anularRegistro($numCua, $cajCod, $numero, $fuente, $pago, $cco)
{
	global $conex;
	global $wbasedato;

	$q= "   UPDATE ".$wbasedato."_000074 SET cdeest = 'off' "
	."WHERE cdecua = '".$numCua."' and cdecaj = '".$cajCod."' and cdenum = '".$numero."' and cdefue = '".$fuente."' and cdefpa = '".$pago."' and cdecco = '".$cco."' ";

	$res1 = mysql_query($q,$conex)or die (mysql_errno()." -NO SE HA PODIDO ANULAR EL DETALLE DEL CUADRE PARA UN RECIBO ".mysql_error());
}


/**
 * Pone en off un registro de la tabla 73 que corresponde al encabezado de un cuadre
 *
 * @param unknown_type $numCua, numero del cuadre
 * @param unknown_type $cco, centro de costos
 * @param unknown_type $cajCod, codigo de la caja
 */
function anularEncabezado($numCua, $cco, $cajCod)
{
	global $conex;
	global $wbasedato;

	$q= "   UPDATE ".$wbasedato."_000073 SET Cenest = 'off' "
	."WHERE cencua = '".$numCua."' and cencaj = '".$cajCod."' and cencco='".$cco."' ";

	$res1 = mysql_query($q,$conex)or die (mysql_errno()." -NO SE HA PODIDO ANULAR EL ENCABEZADO DEL CUADRE ".mysql_error());
}

/**
 * recorre los recibos o devoluciones y se asegura que si se van a cuadrar, se hagan con su repectiva pareja
 * si esta en la lista por cuadrar
 * @param unknown_type $pagos, vector con las formas de pago
 * @param unknown_type $fuente, vector con la fuente de los documentos
 * @param unknown_type $valor, vector con el valor por documento que se desea totalizar
 * @param unknown_type $total, total por forma de pago
 * @param unknown_type $totalF, total del vector
 * @param unknown_type $radio, indica que documentos se van a cuadrar y cuales no, es decir cuales se tienen en cuenta en los totales
 * @param unknown_type $numeroDevoluciones, indica si el documento suma o resta en el total (las devoluciones restan)
 * @return unknown
 */
function validarParejas($pagos, $fuente, &$radio, $numeroDevoluciones, $numero, $cco)
{
	global $conex;
	global $wbasedato;

	$aviso='';
	for ($i=0;$i<count($pagos['codigo']);$i++)
	{
		if (isset($fuente[$i]))
		{
			for ($j=0;$j<count($fuente[$i]);$j++)
			{
				if (isset ($radio[$i][$j]) )
				{

					if ($numeroDevoluciones[$i][$j]=='+')
					{
						//se debe revisar si tiene devolucion y si esta en la lista debe estar checkiada
						$q="select * "
						."from  ".$wbasedato."_000022  "
						."where	rfpcco = '".$cco."' "
						."and	Rfpfue = '".$fuente[$i][$j]."' "
						."and	rfpnum='".$numero[$i][$j]."' "
						."and	rfpfpa='".$pagos['codigo'][$i]."' "
						."and	rfpdev >0  ";
						$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						$num = mysql_num_rows($err);
//echo $q;
						if ($num>0)
						{
							//se mira si esta checkiado el documento siguiente

							if (!isset ($radio[$i][$j+1]) )
							{
								unset($radio[$i][$j]);
								$aviso='LOS RECIBOS Y DEVOLUCIONES ASOCIADOS DEBEN SER CUADRADOS AL TIEMPO';
							}
						}
					}
					else
					{
						//se mira si esta checkiado el documento anterior
						if (!isset ($radio[$i][$j-1]) )
						{
							unset($radio[$i][$j]);
							$aviso='LOS RECIBOS Y DEVOLUCIONES ASOCIADOS DEBEN SER CUADRADOS AL TIEMPO';
						}
					}
				}
			}
		}
	}
	return $aviso;
}

/***********************************************FUNCIONES DE MODELO*************************************/
/**
 * Permite encontrar un total por forma de pago y del vector entregado, se usa para encontrar el total del valor de los documentos, del valor pendiente, del valor a egresar
 *
 * @param unknown_type $pagos, vector con las formas de pago
 * @param unknown_type $fuente, vector con la fuente de los documentos
 * @param unknown_type $valor, vector con el valor por documento que se desea totalizar
 * @param unknown_type $total, total por forma de pago
 * @param unknown_type $totalF, total del vector
 * @param unknown_type $radio, indica que documentos se van a cuadrar y cuales no, es decir cuales se tienen en cuenta en los totales
 * @param unknown_type $numeroDevoluciones, indica si el documento suma o resta en el total (las devoluciones restan)
 * @return unknown
 */
function calcularTotal($pagos, $fuente, $valor, &$total, &$totalF, $radio, $numeroDevoluciones)
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
					if ($numeroDevoluciones[$i][$j]=='+')
					{
						$total[$i]=$total[$i]+$valor[$i][$j];
					}
					else
					{
						$total[$i]=$total[$i]-$valor[$i][$j];
					}
				}
				else
				{
					$radio[$i][$j]='';
				}
			}
		}
		$totalF=$totalF+$total[$i];
	}
	return $radio;
}

/**
 * Calcula el total del valor de los recibos por un lado y por el otro el valor total de las devoluciones, para un vector entregado
 *
 * @param unknown_type $pagos, vector con las formas de pago
 * @param unknown_type $fuente, vector con las fuentes de los documentos
 * @param unknown_type $valor, valor de los documentos
 * @param unknown_type $valorS, valor si es recibo, si no se le da el valor de cero
 * @param unknown_type $totalS, total de los recibos por forma de pago
 * @param unknown_type $totalFS, total de los recibos para el vector
 * @param unknown_type $valorDev, valor de la devolucion, si es un recibo se pone en cero
 * @param unknown_type $totalDev, total de las devoluciones por forma de pago
 * @param unknown_type $totalFDev, total de las devoluciones en el vector
 * @param unknown_type $radio, indica los documentos que deben ser tomados en cuenta en los totales si esta setiado
 * @param unknown_type $numeroDevoluciones, indica si es devolucion (-) o recibo(+)
 * @return unknown
 */
function calcularTotal2($pagos, $fuente, $valor, &$valorS, &$totalS, &$totalFS, &$valorDev, &$totalDev, &$totalFDev, $radio, $numeroDevoluciones)
{
	$totalFS=0;
	$totalFDev=0;
	for ($i=0;$i<count($pagos['codigo']);$i++)
	{
		$totalS[$i]=0;
		$totalDev[$i]=0;
		if (isset($fuente[$i]))
		{
			for ($j=0;$j<count($fuente[$i]);$j++)
			{
				if (isset ($radio[$i][$j]) )
				{
					$radio[$i][$j]='checked';
					if ($numeroDevoluciones[$i][$j]=='+')
					{
						$valorS[$i][$j]=$valor[$i][$j];
						$valorDev[$i][$j]=0;
						$totalS[$i]=$totalS[$i]+$valor[$i][$j];
					}
					else
					{
						$valorS[$i][$j]=0;
						$valorDev[$i][$j]=$valor[$i][$j];
						$totalDev[$i]=$totalDev[$i]+$valor[$i][$j];
					}
				}
				else
				{
					$radio[$i][$j]='';
					if ($numeroDevoluciones[$i][$j]=='+')
					{
						$valorS[$i][$j]=$valor[$i][$j];
						$valorDev[$i][$j]=0;
					}
					else
					{
						$valorS[$i][$j]=0;
						$valorDev[$i][$j]=$valor[$i][$j];
					}
				}

			}
		}
		$totalFS=$totalFS+$totalS[$i];
		$totalFDev=$totalFDev+$totalDev[$i];
	}
	return $radio;
}

/**
 * calcula el saldo (valor pendiente de cuadrar - valor a cuadrar) de cada uno de los documentos, el saldo por forma de pago y el saldo total del vector de documentos
 *
 * @param unknown_type $pagos, vector con las formas de pago
 * @param unknown_type $fuente, vector con la fuente de los documentos
 * @param unknown_type $egresar, vector con los valores a egresar o cuadrar del los documentos
 * @param unknown_type $valor, vector con los valores pendientes por cuadrar de los documentos
 * @param unknown_type $saldo, se entrega el vector de saldos por documentos
 * @param unknown_type $radio, vectos que indica que documentos se van a cuadrar
 * @param unknown_type $saldoT, vector que se entrega con saldo total por forma de pago
 * @param unknown_type $saldoF, saldo final del vector de documentos
 * @param unknown_type $numeroDevoluciones, indica si el documento suma o resta al saldo por forma de pago y al saldo total (si es una devolucion, resta)
 * @return unknown
 */
function calcularSaldo($pagos, $fuente, &$egresar, $valor, &$saldo, &$radio, &$saldoT, &$saldoF, $numeroDevoluciones)
{
	$aviso=true;
	$saldoF=0;
	for ($i=0;$i<count($pagos['codigo']);$i++)
	{
		$saldoT[$i]=0;
		if (isset($fuente[$i]))
		{
			for ($j=0;$j<count($fuente[$i]);$j++)
			{
				if (isset ($radio[$i][$j]))
				{
					if (isset($pagos['tipo'][$i]) and $pagos['tipo'][$i]!='on')
					{
						$egresar[$i][$j]=$valor[$i][$j];
					}
					$saldo[$i][$j]=$valor[$i][$j]-$egresar[$i][$j];
				}
				else
				{
					$saldo[$i][$j]=$valor[$i][$j];
					$egresar[$i][$j]=0;

				}

				if ($numeroDevoluciones[$i][$j]=='+')
				{
					$saldoT[$i]=$saldoT[$i]+$saldo[$i][$j];
				}
				else
				{
					$saldoT[$i]=$saldoT[$i]-$saldo[$i][$j];
				}
				if ($saldo[$i][$j]<0)
				{
					$aviso=false;
				}

			}
		}
		$saldoF=$saldoF+$saldoT[$i];
	}

	return $aviso;
}
/***********************************************FUNCIONES DE PRESENTACION*************************************/
function pintarEncabezado($tipo, $caja, $consecutivo)
{
	global $wbasedato;
	echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";

	//Cuerpo de la pagina
	echo "<table align='center' border=0>";

	if ($tipo==1)
	{
		//Subtitulo de parámetros de consulta
		echo '<span class="subtituloPagina2">';
		echo "Consulta de cuadre";
		echo "</span>";
		echo "<br>";
		echo "<br>";

		//Numero de cuadre
		echo "<tr><td class='fila1' width=150>Número</td>";
		echo "<td align='center' class='fila2' width=150><INPUT TYPE='text' NAME='consulta' VALUE='' size='10'></td>";
		echo "</tr>";

		//Fecha inicial
		echo "<tr><td class='fila1'>Fecha inicial</td>";
		echo "<td class='fila2' align='center'>";
		if(isset($wfecini) && !empty($wfecini)){
			campoFechaDefecto("wfecini",$wfecini);
		} else {
			campoFecha("wfecini");
		}
		echo "</td></tr>";

		//Fecha final del comprobante
		echo "<tr><td class='fila1'>Fecha final</td>";
		echo "<td class='fila2' align='center'>";
		if(isset($wfecfin) && !empty($wfecfin)){
			campoFechaDefecto("wfecfin",$wfecfin);
		} else {
			campoFecha("wfecfin");
		}
		echo "</td></tr>";

		//Consulta por fecha o por numero de cuadre
		echo "<tr><td class='fila1' colspan=2 align='center'>";
		echo "<input type='radio' id='wtiporep' name='wtiporep' value='n' checked>Por numero&nbsp;|&nbsp;";
		echo "<input type='radio' id='wtiporep' name='wtiporep' value='f'>Por fecha";
		echo "</td></tr>";

		//Boton
		echo "<tr class='fila2'>";
		echo "<td colspan=2 align='center'>";
		echo "<INPUT TYPE='button' NAME='consultaR' VALUE='Consultar' onclick='javascript:enter()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'>";
		echo "</td>";
		echo "</tr>";

		echo "</table>";
	} else {
		//Subtitulo de parámetros de consulta
		echo '<span class="subtituloPagina2">';
		echo "Cuadre $consecutivo para la caja $caja. Fecha de consulta ".date('Y-m-d')." ".date("H:i:s");
		echo "</span>";
	}
	echo "</table></br>";
}

function pintarConsulta($cajCod, $cajDes, $consecutivoT, $fecha, $resp, $tipo, $hora, $estado)
{

	//Subtitulo
	echo "<center>";
	echo '<span class="subtituloPagina2" align=center>';
	echo "<b>Caja: </b>".$cajCod."-".$cajDes."";
	echo "<br>";

	switch ($tipo)
	{
		case 1:
		echo "<b>Cuadre de caja a: </b>".date('Y-m-d');
		echo "<br>";
		echo "<b>N&uacute;mero: </b>".$consecutivoT;
		break;
		case 2:
		echo "<b>Se ha guardado exitosamente el cuadre número: </b>".$consecutivoT;
		echo "<br>";
		echo "<b>Fecha del cuadre: </b>".$fecha." ".$hora;
		echo "<br>";
		echo "<b>Responsable: </b>".$resp;
		break;
		case 3:
		echo "<b>Cuadre n&uacute;mero: </b>".$consecutivoT;
		echo "<br>";
		echo "<b>Fecha del cuadre: </b>".$fecha." ".$hora;
		echo "<br>";
		echo "<b>Responsable: </b>".$resp;
		if ($estado=='off')
		{
			echo "<br>";
			echo "<H1><FONT COLOR='#006699'>ANULADO</FONT></H1>";
		}
		break;
	}
	echo "</span>";
	echo "</center>";
	echo "</br>";
}

function pintarAlert7($mensaje, $cajCod)
{
	echo "<table align='center'>";
	echo "<tr class=encabezadoTabla><td colspan='2'>".ucwords(strtolower($mensaje))."</td></tr>";
	echo "<tr class=fila2><td colspan='2'>Sin recibos por cuadrar</td></tr>";
	echo "</table></br></br>";
}

function pintarAlert1($mensaje)
{
	echo "</table>";
	echo"<form action='' method='post' name='form1' ><CENTER><fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080>";
	echo "<table align='center' border=0 bordercolor=#000080 width=700 style='border:solid;'>";
	echo "<tr><td colspan='2' align=center><font size=3 color='#000080' face='arial' align=center><b>".$mensaje."</td><tr>";
	echo "<tr><td colspan='2' align='center'><input type='button' name='aceptar' value='Finalizar' onclick='javascript:cerrarVentana()'></td><tr>";
	echo "</table></fieldset></form>";
}

function pintarAlert3($mensaje)
{
	echo '<script language="Javascript">';
	echo 'alert ("'.$mensaje.'")';
	echo '</script>';
}

function pintarFormulario($num, $numCua, $cco, $cajCod, $cajDes, $resp, $cuaAnt)
{
	global $wbasedato;
	echo "<form name='forma1' action='' method='post'>";

	echo "<input type='HIDDEN' name='bandera1' value='".$num."'>";
	echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
	echo "<input type='HIDDEN' name='numCua' value='".$numCua."'>";
	echo "<input type='HIDDEN' name='cajCod' value='".$cajCod."'>";
	echo "<input type='HIDDEN' name='cajDes' value='".$cajDes."'>";
	echo "<input type='HIDDEN' name='cco' value='".$cco."'>";
	echo "<input type='HIDDEN' name='resp' value='".$resp."'>";
	echo "<input type='HIDDEN' name='cuaAnt' value='".$cuaAnt."'>";
	echo "<input type='HIDDEN' name='cambioBancos' value='0'>";
}

/**
 * Msanchez:: Nov 19 FYI:
 *
 * Este metodo permite persistir los arreglos compuestos por cada una de las columnas de las consultas ejecutadas, si no se declaran los arreglos aqui, el arreglo consultado en el resumen
 * se perderá en adelante.
 *
 * EL metodo discrimina los vectores de los ingresos anteriores colocando una A despues del nombre, ej:  fuenteA, pagosA... etc
 * @param $fuente
 * @param $numero
 * @param $fecha
 * @param $valor
 * @param $valorS
 * @param $valorP
 * @param $responsable
 * @param $numeroBancos
 * @param $numeroDevoluciones
 * @param $bancos
 * @param $pagos
 * @param $valorDev
 * @param $total
 * @param $totalF
 * @param $totalP
 * @param $totalFP
 * @param $tipo
 * @param $contador
 * @param $radio
 * @param $bandera
 * @param $totalS
 * @param $totalFS
 * @param $totalDev
 * @param $totalFDev
 * @param $id
 * @param $ven
 * @param $ccoOrigen
 * @return unknown_type
 */
function pintarHidden($fuente, $numero, $fecha, $valor, $valorS, $valorP, $responsable, $numeroBancos, $numeroDevoluciones, $bancos, $pagos, $valorDev, $total, $totalF, $totalP, $totalFP, $tipo, $contador, $radio, $bandera, $totalS, $totalFS, $totalDev, $totalFDev, $id, $ven, $ccoOrigen)
{

	if( empty($bancos) ){
        $bancos =  array();
		$bancos[0] = array();
		$bancos[0][0] = array();
		$bancos[0][0][0] = '';
	}

	if( empty($numeroBancos) ){
        $numeroBancos =  array();
		$numeroBancos[0] = '';
		$numeroBancos[0][0] = '';
	}

	if( empty($id) ){
        $id =  array();
		$id[0] = array();
		$id[0][0] = array();
		$id[0][0][0] = '';
	}

	if( empty($id) ){
		$id[0] = '';
		$id[0][0] = '';
		$id[0][0][0] = '';
	}

	if( empty($bancos) ){
		$bancos[0] = array();
		$bancos[0][0] = array();
		$bancos[0][0][0] = array();
	}

	for($i=0; $i < count($pagos['codigo']); $i++)
	{
		if ($tipo=='A')
		{
			echo "<input type='HIDDEN' name='pagosA[codigo][".$i."]' value='".$pagos['codigo'][$i]."'>";
			echo "<input type='HIDDEN' name='pagosA[nombre][".$i."]' value='".$pagos['nombre'][$i]."'>";
			if (isset ($pagos['tipo'][$i]))
			{
				echo "<input type='HIDDEN' name='pagosA[tipo][".$i."]' value='".$pagos['tipo'][$i]."'>";
			}
			echo "<input type='HIDDEN' name='totalA[".$i."]' value='".$total[$i]."'>";
			echo "<input type='HIDDEN' name='totalPA[".$i."]' value='".$totalP[$i]."'>";
			echo "<input type='HIDDEN' name='totalSA[".$i."]' value='".$totalS[$i]."'>";
			echo "<input type='HIDDEN' name='totalDevA[".$i."]' value='".$totalDev[$i]."'>";

			if (isset ($fuente[$i][0]))
			{
				for($j=0; $j < count($fuente[$i]); $j++)
				{

					echo "<input type='HIDDEN' name='fuenteA[".$i."][".$j."]' value='".$fuente[$i][$j]."'>";
					echo "<input type='HIDDEN' name='numeroA[".$i."][".$j."]' value='".$numero[$i][$j]."'>";
					echo "<input type='HIDDEN' name='fechaA[".$i."][".$j."]' value='".$fecha[$i][$j]."'>";
					if(isset($ccoOrigen) && isset($ccoOrigen[$i][$j])){
						echo "<input type='HIDDEN' name='ccoOrigenA[".$i."][".$j."]' value='".$ccoOrigen[$i][$j]."'>"; 				//Msanchez::Nov 19
					} else {
						echo "<input type='HIDDEN' name='ccoOrigenA[".$i."][".$j."]' value=''>"; 				//Msanchez::Nov 19
					}
					echo "<input type='HIDDEN' name='responsableA[".$i."][".$j."]' value='".$responsable[$i][$j]."'>";
					echo "<input type='HIDDEN' name='valorPA[".$i."][".$j."]' value='".$valorP[$i][$j]."'>";
					echo "<input type='HIDDEN' name='valorSA[".$i."][".$j."]' value='".$valorS[$i][$j]."'>";
					echo "<input type='HIDDEN' name='numeroBancosA[".$i."][".$j."]' value='".$numeroBancos[$i][$j]."'>";
					echo "<input type='HIDDEN' name='numeroDevolucionesA[".$i."][".$j."]' value='".$numeroDevoluciones[$i][$j]."'>";
					echo "<input type='HIDDEN' name='venA[".$i."][".$j."]' value='".$ven[$i][$j]."'>";
					echo "<input type='HIDDEN' name='valorA[".$i."][".$j."]' value='".$valor[$i][$j]."'>";
					echo "<input type='HIDDEN' name='valorDevA[".$i."][".$j."]' value='".$valorDev[$i][$j]."'>";

					if ($bandera=='')
					{
						echo "<input type='HIDDEN' name='radioA[".$i."][".$j."]' value='".$radio[$i][$j]."'>";

						if (!isset($saldoA[$i][$j]))
						{
							$saldoA[$i][$j]=0;
						}

						if (!isset($egresarA[$i][$j]))
						{
							$egresarA[$i][$j]=$valorP[$i][$j];
						}

						echo "<input type='HIDDEN' name='egresarA[".$i."][".$j."]' value='".$egresarA[$i][$j]."'>";
						echo "<input type='HIDDEN' name='saldoA[".$i."][".$j."]' value='".$saldoA[$i][$j]."'>";
					}

					for ($k=0;$k<$numeroBancos[$i][$j];$k++)
					{
						echo "<input type='HIDDEN' name='bancosA[".$i."][".$j."][".$k."]' value='".$bancos[$i][$j][$k]."'>";
						echo "<input type='HIDDEN' name='idA[".$i."][".$j."][".$k."]' value='".$id[$i][$j][$k]."'>";
					}

					if ($numeroBancos[$i][$j]==0)
					{
						echo "<input type='HIDDEN' name='bancosA[".$i."][".$j."][0]' value='".$bancos[$i][$j][0]."'>";
						echo "<input type='HIDDEN' name='idA[".$i."][".$j."][0]' value='".$id[$i][$j][0]."'>";
					}
				}
			}
		}
		else
		{
			echo "<input type='HIDDEN' name='pagos[codigo][".$i."]' value='".$pagos['codigo'][$i]."'>";
			echo "<input type='HIDDEN' name='pagos[nombre][".$i."]' value='".$pagos['nombre'][$i]."'>";
			if (isset ($pagos['tipo'][$i]))
			{
				echo "<input type='HIDDEN' name='pagos[tipo][".$i."]' value='".$pagos['tipo'][$i]."'>";
			}
			echo "<input type='HIDDEN' name='total[".$i."]' value='".$total[$i]."'>";
			echo "<input type='HIDDEN' name='totalP[".$i."]' value='".$totalP[$i]."'>";
			echo "<input type='HIDDEN' name='totalS[".$i."]' value='".$totalS[$i]."'>";
			echo "<input type='HIDDEN' name='totalDev[".$i."]' value='".$totalDev[$i]."'>";

			if (isset ($fuente[$i][0]))
			{
				for($j=0; $j < count($fuente[$i]); $j++)
				{

					echo "<input type='HIDDEN' name='fuente[".$i."][".$j."]' value='".$fuente[$i][$j]."'>";
					echo "<input type='HIDDEN' name='numero[".$i."][".$j."]' value='".$numero[$i][$j]."'>";
					echo "<input type='HIDDEN' name='fecha[".$i."][".$j."]' value='".$fecha[$i][$j]."'>";
					if(isset($ccoOrigen) && isset($ccoOrigen[$i][$j])){
						echo "<input type='HIDDEN' name='ccoOrigen[".$i."][".$j."]' value='".$ccoOrigen[$i][$j]."'>"; 				//Msanchez::Nov 19
					} else {
						echo "<input type='HIDDEN' name='ccoOrigen[".$i."][".$j."]' value=''>"; 				//Msanchez::Nov 19
					}
					echo "<input type='HIDDEN' name='responsable[".$i."][".$j."]' value='".$responsable[$i][$j]."'>";
					echo "<input type='HIDDEN' name='valorP[".$i."][".$j."]' value='".$valorP[$i][$j]."'>";
					echo "<input type='HIDDEN' name='valorS[".$i."][".$j."]' value='".$valorS[$i][$j]."'>";
					echo "<input type='HIDDEN' name='numeroBancos[".$i."][".$j."]' value='".$numeroBancos[$i][$j]."'>";
					echo "<input type='HIDDEN' name='numeroDevoluciones[".$i."][".$j."]' value='".$numeroDevoluciones[$i][$j]."'>";
					echo "<input type='HIDDEN' name='ven[".$i."][".$j."]' value='".$ven[$i][$j]."'>";
					echo "<input type='HIDDEN' name='valor[".$i."][".$j."]' value='".$valor[$i][$j]."'>";
					echo "<input type='HIDDEN' name='valorDev[".$i."][".$j."]' value='".$valorDev[$i][$j]."'>";

					if ($bandera=='')
					{
						echo "<input type='HIDDEN' name='radio[".$i."][".$j."]' value='".$radio[$i][$j]."'>";
						if (!isset($saldo[$i][$j]))
						{
							$saldo[$i][$j]=0;
						}

						if (!isset($egresar[$i][$j]))
						{
							$egresar[$i][$j]=$valorP[$i][$j];
						}

						echo "<input type='HIDDEN' name='egresar[".$i."][".$j."]' value='".$egresar[$i][$j]."'>";
						echo "<input type='HIDDEN' name='saldo[".$i."][".$j."]' value='".$saldo[$i][$j]."'>";
					}

					for ($k=0;$k<$numeroBancos[$i][$j];$k++)
					{
						echo "<input type='HIDDEN' name='bancos[".$i."][".$j."][".$k."]' value='".$bancos[$i][$j][$k]."'>";
						echo "<input type='HIDDEN' name='id[".$i."][".$j."][".$k."]' value='".$id[$i][$j][$k]."'>";
					}

					if ($numeroBancos[$i][$j]==0)
					{
						//echo "<input type='HIDDEN' name='bancos[".$i."][".$j."][0]' value='".$bancos[$i][$j][0]."'>";
						echo "<input type='HIDDEN' name='id[".$i."][".$j."][0]' value='".$id[$i][$j][0]."'>";
					}
				}
			}
		}

	}

	if ($tipo=='A')
	{


		echo "<input type='HIDDEN' name='totalFA' value='".$totalF."'>";
		echo "<input type='HIDDEN' name='totalFPA' value='".$totalFP."'>";
		echo "<input type='HIDDEN' name='totalFSA' value='".$totalFS."'>";
		echo "<input type='HIDDEN' name='totalFDevA' value='".$totalFDev."'>";
		echo "<input type='HIDDEN' name='contadorA' value='".$contador."'>";
	}
	else
	{

		echo "<input type='HIDDEN' name='totalF' value='".$totalF."'>";
		echo "<input type='HIDDEN' name='totalFP' value='".$totalFP."'>";
		echo "<input type='HIDDEN' name='totalFS' value='".$totalFS."'>";
		echo "<input type='HIDDEN' name='totalFDev' value='".$totalFDev."'>";
		echo "<input type='HIDDEN' name='contador' value='".$contador."'>";
	}
}

function pintarResumido($titulo, $pagos, $totalP, $totalFP, $total, $totalF, $totalS, $totalFS, $totalDev, $totalFDev, $fuente)
{

	echo "<table align='center' border=0>";
	echo "<tr class=encabezadoTabla>";
	echo "<td colspan='5'>".ucwords(strtolower($titulo))."</td></tr>";
	echo "<tr class=fila1>";
	echo "<td>Forma de pago</td>";
	echo "<td>Valor Recibos</td>";
	echo "<td>Valor Devoluciones</td>";
	echo "<td>Valor Total</td>";
	echo "<td>Valor Pendiente</td></tr>";

	for($i=0; $i < count($pagos['codigo']); $i++)
	{
		if (isset ($fuente[$i]))
		{
			echo "<tr class='fila2'>";
			echo "<td>".$pagos['codigo'][$i]."-".$pagos['nombre'][$i]."</td>";
			echo "<td>".number_format((float)$totalS[$i],0,"",",")."</td>";
			echo "<td>".number_format((float)$totalDev[$i],0,"",",")."</td>";
			echo "<td>".number_format((float)$total[$i],0,"",",")."</td>";
			echo "<td>".number_format((float)$totalP[$i],0,"",",")."</td></tr>";
		}
	}

	echo "<tr class=encabezadoTabla>";
	echo "<td>Total ".ucwords(strtolower($titulo))."</td>";
	echo "<td>".number_format((float)$totalFS,0,"",",")."</td>";
	echo "<td>".number_format((float)$totalFDev,0,"",",")."</td>";
	echo "<td>".number_format((float)$totalF,0,"",",")."</td>";
	echo "<td>".number_format((float)$totalFP,0,"",",")."</td>";
	echo "</table></br>";
}

function imprimirGrande($cajCod, $cajDes, $numCua, $fecha, $hora, $resp, $estado)
{
	global $wbasedato;

	echo "<a href='javascript:void(0);' onclick='javascript:cambiarTamanoLetra(\"2\");'>Impresi&oacute;n pequeña</a>&nbsp;|&nbsp;";
	echo "<a href='javascript:void(0);' onclick='javascript:cambiarTamanoLetra(\"6\");'>Impresi&oacute;n grande</a>";

	echo "<table align='center'>";
	if ($estado=='off')
	{
		echo "<tr><td align='center'><font size=6 text><b>ANULADO</font></b></td></tr>";
	}
	echo "<tr><td>&nbsp;</td></tr>";
	echo "<tr><td><font size=6 text><b>CUADRE DE CAJA NUMERO: ".$numCua."</font></b></td></tr>";
	echo "<tr><td><font size=6 text><b>CAJA: ".$cajCod."-".$cajDes."</font></b></td></tr>";
	echo "<tr><td><font size=6 text><b>FECHA DE CUADRE: ".$fecha." ".$hora."</font></b></td></tr>";
	echo "<tr><td><font size=6 text><b>RESPONSABLE: ".$resp."</font></b></td></tr>";

	echo "</table></br>";
}

function imprimirResumen($titulo, $pagos, $totalP, $totalFP, $egresarT, $egresarF, $saldoT, $saldoF)
{
	echo"<fieldset style='border:solid;border-color:#000080; width=90%' ; color=#000080>";
	echo "<table align='center' border=0>";
	echo "<tr><td  colspan='4' align=center width=90%'><font size=6 text><b>".$titulo."<b></font></td></tr>";
	echo "<tr><td  align='center' width=45%'><font size=6 text >Forma de pago</font></td>";
	echo "<td align='center' width=15%'><font size=6 text>Valor Pendiente</font></td>";
	echo "<td  align='center' width=15%'><font size=6 text>Valor Egreso</font></td>";
	echo "<td  align='center' width=15%'><font size=6 text>Nuevo Saldo</font></td>";

	for($i=0; $i < count($pagos['codigo']); $i++)
	{
		if ($totalP[$i]>0)
		{
			echo "<tr><td  align='center' width=45%'><font size=6 text>".$pagos['codigo'][$i]."-".$pagos['nombre'][$i]."</font></td>";
			echo "<td  align='right' width=15%'><font size=6 text>".number_format((float)$totalP[$i],0,",",".")."</font></td>";
			echo "<td  align='right' width=15%'><font size=6 text>".number_format((float)$egresarT[$i],0,",",".")."</font></td>";
			echo "<td  align='right' width=15%'><font size=6 text>".number_format((float)$saldoT[$i],0,",",".")."</font></td>";
		}
	}

	echo "<tr><td  align='center' width=45%'><font size=6 text><b>TOTAL</b></font></td>";
	echo "<td  align='right' width=15%'><font size=6 text>".number_format((float)$totalFP,0,",",".")."</font></td>";
	echo "<td  align='right' width=15%'><font size=6 text>".number_format((float)$egresarF,0,",",".")."</font></td>";
	echo "<td align='right' width=15%'><font size=6 text>".number_format((float)$saldoF,0,",",".")."</font></td>";
	echo "</table></fieldset></br></br>";
}


function pintarCuadrado($titulo, $pagos, $totalP, $totalFP, $egresarT, $egresarF, $saldoT, $saldoF)
{

	echo "<table align='center' border=0>";
	echo "<tr class=encabezadoTabla>";
	echo "<td align=center colspan='5'>".ucwords(strtolower($titulo))."</td></tr>";
	echo "<tr class=fila1>";
	echo "<td>Forma de pago</td>";
	echo "<td>Valor Pendiente</td>";
	echo "<td>Valor Egreso</td>";
	echo "<td>Nuevo Saldo</td>";

	for($i=0; $i < count($pagos['codigo']); $i++)
	{
		if ($totalP[$i]>0)
		{
			echo "<tr class='fila2'><td>".$pagos['codigo'][$i]."-".$pagos['nombre'][$i]."</td>";
			echo "<td align=right>".number_format((float)$totalP[$i],0,",",".")."</td>";
			echo "<td align=right>".number_format((float)$egresarT[$i],0,",",".")."</td>";
			echo "<td align=right>".number_format((float)$saldoT[$i],0,",",".")."</td>";
		}
	}

	echo "<tr class=encabezadoTabla>";
	echo "<td>Total ".ucwords(strtolower($titulo))."</td>";
	echo "<td align=right>".number_format((float)$totalFP,0,",",".")."</td>";
	echo "<td align=right>".number_format((float)$egresarF,0,",",".")."</td>";
	echo "<td align=right>".number_format((float)$saldoF,0,",",".")."</td>";
	echo "</table></br>";
}

function pintarTotales($totalFP, $egresarF, $saldoF)
{

	echo "<table align='center' border=0>";
	echo "<tr class=encabezadoTabla><td colspan='3'>Totales Cuadre</td></tr>";
	echo "<tr class=fila2>";
	echo "<td>Valor Pendiente</td>";
	echo "<td>Valor Egreso</td>";
	echo "<td>Nuevo Saldo</td></tr>";

	echo "<tr class=encabezadoTabla><td>".number_format((float)$totalFP,0,",",".")."</td>";
	echo "<td>".number_format((float)$egresarF,0,",",".")."</td>";
	echo "<td>".number_format((float)$saldoF,0,",",".")."</td>";
	echo "</table></br>";
}

function imprimirTotales($totalFP, $egresarF, $saldoF, $totalP, $egresarT, $saldoT, $totalPA, $egresarTA, $saldoTA, $pagosA, $pagos, $lista)
{

	echo"<fieldset style='border:solid;border-color:#000080; width=90%' ; color=#000080>";
	echo "<table align='center' border=0>";
	echo "<td  colspan='4' align='center'><font size=6 text><b>TOTALES CUADRE</b></font></td></tr>";
	echo "<tr><td  align='center' width=45%'><font size=6 text >Forma de pago</font></td>";
	echo "<td  align='center' width=15%'><font size=6 text>Valor Pendiente</font></td>";
	echo "<td align='center' width=15%'><font size=6 text>Valor Egreso</font></td>";
	echo "<td  align='center' width=15%'><font size=6 text>Nuevo Saldo</font></td></tr>";

	$contadorA=0;
	$contador=0;
	for($i=0; $i < count($lista['codigo']); $i++)
	{

		if ($lista['codigo'][$i]==$pagosA['codigo'][$contadorA])
		{
			$pendiente=$totalPA[$contadorA];
			$egresado=$egresarTA[$contadorA];
			$saldo=$saldoTA[$contadorA];
			if (($contadorA+1)<count($pagosA['codigo']))
			{
				$contadorA++;
			}
		}
		else
		{
			$pendiente=0;
			$egresado=0;
			$saldo=0;
		}

		if ($lista['codigo'][$i]==$pagos['codigo'][$contador])
		{
			$pendiente=$pendiente+$totalP[$contador];
			$egresado=$egresado+$egresarT[$contador];
			$saldo=$saldo+$saldoT[$contador];
			if (($contador+1)<count($pagos['codigo']))
			{
				$contador++;
			}
		}
		else
		{
			$pendiente=$pendiente+0;
			$egresado=$egresado+0;
			$saldo=$saldo+0;
		}


		echo "<tr><td  align='center' width=45%'><font size=6 text>".$lista['codigo'][$i]."-".$lista['nombre'][$i]."</font></td>";
		echo "<td  align='right' width=15%'><font size=6 text>".number_format((float)$pendiente,0,",",".")."</font></td>";
		echo "<td  align='right' width=15%'><font size=6 text>".number_format((float)$egresado,0,",",".")."</font></td>";
		echo "<td  align='right' width=15%'><font size=6 text>".number_format((float)$saldo,0,",",".")."</font></td>";
	}

	echo "<tr><td  align='center' width=45%'><font size=6 text ><B>TOTALES</B></font></td>";
	echo "<td  align='right' width=15%'><font size=6 text>".number_format((float)$totalFP,0,",",".")."</font></td>";
	echo "<td  align='right' width=15%'><font size=6 text>".number_format((float)$egresarF,0,",",".")."</font></td>";
	echo "<td  align='right' width=15%'><font size=6 text>".number_format((float)$saldoF,0,",",".")."</font></td>";
	echo "</table></fieldset></br></br>";
}

/**
 *
 * @param $titulo
 * @param $fuente
 * @param $numero
 * @param $fecha
 * @param $valor					Corresponde a la columna valor
 * @param $valorS
 * @param $valorP
 * @param $responsable
 * @param $numeroBancos
 * @param $numeroDevoluciones
 * @param $bancos
 * @param $pagos
 * @param $valorDev
 * @param $total
 * @param $totalF
 * @param $totalP
 * @param $totalFP
 * @param $radio
 * @param $totalS
 * @param $totalFS
 * @param $totalDev
 * @param $totalFDev
 * @param $cco
 * @param $clase
 * @param $id
 * @param $egresar
 * @param $saldo
 * @param $saldoT
 * @param $saldoF
 * @param $egresarT
 * @param $egresarF
 * @param $letra
 * @param $ven
 * @param $ccoOrigen
 * @return unknown_type
 */
function pintarDetallado($titulo, $fuente, $numero, $fecha, $valor, $valorS, $valorP, $responsable, $numeroBancos, $numeroDevoluciones, $bancos, $pagos, $valorDev, $total, $totalF, $totalP, $totalFP, $radio,  $totalS, $totalFS, $totalDev, $totalFDev, $cco, $clase, $id, $egresar, $saldo, $saldoT, $saldoF, $egresarT, $egresarF, $letra, $ven, $ccoOrigen)
{
	global $wbasedato;

	if( empty($bancos) ){
        $bancos = array();
		$bancos[0] = array();
		$bancos[0][0] = array();
		$bancos[0][0][0] = array();
	}

	echo "<table align='center' border=0>";
	echo "<tr class=encabezadoTabla><td colspan='13'>".ucwords(strtolower($titulo))."</td></tr>";


	for($i=0;$i<count($pagos['codigo']); $i++)
	{

		if (isset ($fuente[$i][0]))
		{
			echo "<tr class=fila1><td align=center colspan='13'>".$pagos['codigo'][$i]."-".$pagos['nombre'][$i]."</td></tr>";
			echo "<tr class=encabezadoTabla>";
			echo"<td>&nbsp;</td>";
			echo "<td>Fuente</td>";
			echo "<td>Numero</td>";
			echo "<td>Resp</td>";
			echo "<td>Cco</td>";
			echo "<td>Fecha</td>";
			echo "<td>Banco</td>";
			echo "<td>Venta / Factura</td>";
			echo "<td>Valor</td>";
			echo "<td>Saldo Pendiente</td>";
			echo "<td>Valor egreso</td>";
			echo "<td>Nuevo saldo</td>";
			echo "<td>Seleccionar</td></tr>";


			$venAnt='0';
			$class1='fila1';

			for($j=0; $j < count($fuente[$i]); $j++)
			{
				if ($ven[$i][$j]!=$venAnt)
				{
					if ($class1!='fila1')
					{
						$class1='fila1';
						$class2='fila1';
						$class3='fila1';
					}
					else
					{
						$class1='fila2';
						$class2='fila2';
						$class3='fila2';
					}
					$venAnt=$ven[$i][$j];
				}

				if ($numeroBancos[$i][$j]>0)
				{
					$rowspan=count ($bancos[$i][$j]);
				}
				else
				{
					$rowspan=1;
				}

				echo "<tr>";
				echo "<td class='".$class1."' rowspan='".$rowspan."'>".$numeroDevoluciones[$i][$j]."</td>";
				echo "<td class='".$class1."' rowspan='".$rowspan."'>".$fuente[$i][$j]."</td>";
				echo "<td class='".$class1."' rowspan='".$rowspan."'>".$numero[$i][$j]."</td>";
				echo "<td class='".$class1."' rowspan='".$rowspan."'>".$responsable[$i][$j]."</td>";

				if(isset($ccoOrigen) && isset($ccoOrigen[$i][$j]) && count($ccoOrigen) > 0){
					echo "<td class='".$class1."' rowspan='".$rowspan."'>".$ccoOrigen[$i][$j]."</td>";	//Nov 19
				} else {
					echo "<td class='".$class1."' rowspan='".$rowspan."'>".$cco."</td>";		//Nov 19
				}

				echo "<td class='".$class1."' rowspan='".$rowspan."'>".$fecha[$i][$j]."</td>";

				if ($numeroBancos[$i][$j]>0 and $clase==1)
				{
					echo "<td class='".$class3."'><a href='cambioBanco.php?tipoTablas=".$wbasedato."&id=".$id[$i][$j][0]."&cco=".$cco."&fue=".$fuente[$i][$j]."&num=".$numero[$i][$j]."&fpa=".$pagos['codigo'][$i]."&fpaDes=".$pagos['nombre'][$i]."' >".$bancos[$i][$j][0]."</a></td>";
				}
				else
				{
					echo "<td class='".$class3."'>&nbsp;</td>";
				}

				if ($clase==1 and isset($ven[$i][$j]))
				{
					$exp=explode('/',$ven[$i][$j]);

					if(isset($exp[1]))
					{
						$exp1=explode('-',trim($exp[1]));
						echo "<td class='".$class3."' rowspan='".$rowspan."'><a href='copia_factura.php?wnrovta=".trim($exp[0])."&amp;wfuefac=33&amp;wnrofac=".trim($exp1[1])."-".trim($exp1[2])."&amp;wfecini=".date('Y-m-d')."&amp;wfecfin=".date('Y-m-d')."&amp;wbasedato=".$wbasedato."' target='_blank'>".$ven[$i][$j]."</a></td>";
					}
					else
					{
						echo "<td class='".$class2."' rowspan='".$rowspan."'>".$ven[$i][$j]."</td>";
					}
				}else if(isset($ven[$i][$j]))
				{
					echo "<td class='".$class2."' rowspan='".$rowspan."'>".$ven[$i][$j]."</td>";
				}
				else
				{
					echo "<td class='".$class2."' rowspan='".$rowspan."'>&nbsp;</td>";
				}

				echo "<td class='".$class2."' rowspan='".$rowspan."' align=right>".number_format((float)$valor[$i][$j],0,",",".")."</td>";
				echo "<td class='".$class2."' rowspan='".$rowspan."' align=right>".number_format((float)$valorP[$i][$j],0,",",".")."</td>";

				if ($letra=='B')
				{
					if ((isset ($pagos['tipo'][$i]) and $pagos['tipo'][$i]!='on' ) or $clase!=1)
					{
						echo "<td class='".$class2."' rowspan='".$rowspan."' align=right>".number_format((float)$egresar[$i][$j],0,",",".")."</td>";
						echo "<input type='hidden' name='egresar[".$i."][".$j."]' value=".$egresar[$i][$j].">";
						echo "<td class='".$class2."' rowspan='".$rowspan."' align=right>".number_format((float)$saldo[$i][$j],0,",",".")."</td>";
						echo "<input type='hidden' name='saldo[".$i."][".$j."]' value=".$saldo[$i][$j].">";
					}
					else
					{

						if ($saldo[$i][$j]<0)
						{
							echo "<td class='$class2' rowspan='".$rowspan."' align=right><input type='text' name='egresar[".$i."][".$j."]' value=".$egresar[$i][$j]."></td>";
							echo "<td class='$class2' rowspan='".$rowspan."' align=right>".number_format((float)$saldo[$i][$j],0,",",".")."</td>";
						}
						else
						{
							echo "<td class='".$class2."' rowspan='".$rowspan."'><input type='text' name='egresar[".$i."][".$j."]' value=".$egresar[$i][$j]."></td>";
							echo "<td class='".$class2."' rowspan='".$rowspan."' align=right>".number_format((float)$saldo[$i][$j],0,",",".")."</td>";
						}
					}

					if ($clase==1)
					{
						echo "<td class='".$class2."' rowspan='".$rowspan."'><input type='checkbox' name='radio[".$i."][".$j."]' ".$radio[$i][$j]."  align=center></td></tr>";
					}
					else if (isset ($radio[$i][$j]) and $radio[$i][$j]=='checked')
					{
						echo "<td class='".$class2."' rowspan='".$rowspan."' align=center><img src='/matrix/images/medical/IPS/checked.png' ></td>";
					}
					else
					{
						echo "<td class='".$class2."' rowspan='".$rowspan."'>&nbsp;</td></tr>";
					}
				}
				else
				{
					if ((isset ($pagos['tipo'][$i]) and $pagos['tipo'][$i]!='on') or $clase!=1)
					{
						echo "<td class='".$class2."' rowspan='".$rowspan."'>".number_format((float)$egresar[$i][$j],0,",",".")."</td>";
						echo "<input type='hidden' name='egresarA[".$i."][".$j."]' value=".$egresar[$i][$j].">";
						echo "<td class='".$class2."' rowspan='".$rowspan."'>".number_format((float)$saldo[$i][$j],0,",",".")."</td>";
						echo "<input type='hidden' name='saldoA[".$i."][".$j."]' value=".$saldo[$i][$j].">";
					}
					else
					{

						if ($saldo[$i][$j]<0)
						{
							echo "<td class='$class2' rowspan='".$rowspan."'><input type='text' name='egresarA[".$i."][".$j."]' value=".$egresar[$i][$j]."></td>";
							echo "<td class='$class2' rowspan='".$rowspan."'>".number_format((float)$saldo[$i][$j],0,",",".")."</td>";
						}
						else
						{
							echo "<td class='".$class2."' rowspan='".$rowspan."'><input type='text' name='egresarA[".$i."][".$j."]' value=".$egresar[$i][$j]."></td>";
							echo "<td class='".$class2."' rowspan='".$rowspan."'>".number_format((float)$saldo[$i][$j],0,",",".")."</td>";
						}
					}


					if ($clase==1)
					{
						echo "<td class='".$class2."' rowspan='".$rowspan."'><input type='checkbox' name='radioA[".$i."][".$j."]' ".$radio[$i][$j]." ></td></tr>";

					}else if (isset ($radio[$i][$j]) and $radio[$i][$j]=='checked')
					{
						echo "<td class='".$class2."' rowspan='".$rowspan."'><img src='/matrix/images/medical/IPS/checked.png' ></td></tr>";
					}
					else
					{
						echo "<td class='".$class2."' rowspan='".$rowspan."'>&nbsp;</td></tr>";
					}
				}

				for ($k=1;$k<count ($bancos[$i][$j]);$k++)
				{
					echo "<td class='".$class3."'><a href='cambioBanco.php?tipoTablas=".$wbasedato."&id=".$id[$i][$j][0]."&cco=".$cco."&fue=".$fuente[$i][$j]."&num=".$numero[$i][$j]."&fpa=".$pagos['codigo'][$i]."&fpaDes=".$pagos['nombre'][$i]."' >".$bancos[$i][$j][$k]."</a></td>";
				}
			}

			echo "<tr class=fila2>";
			echo "<td>&nbsp;</td>";
			echo "<td>&nbsp;</td>";
			echo "<td>&nbsp;</td>";
			echo "<td>&nbsp;</td>";
			echo "<td>&nbsp;</td>";
			echo "<td>&nbsp;</td>";
			echo "<td>&nbsp;</td>";
			echo "<td>Total forma de pago</td>";
			echo "<td align=right>".number_format((float)$total[$i],0,",",".")."</td>";
			echo "<td align=right>".number_format((float)$totalP[$i],0,",",".")."</td>";
			echo "<td align=right>".number_format((float)$egresarT[$i],0,",",".")."</td>";
			echo "<td align=right>".number_format((float)$saldoT[$i],0,",",".")."</td>";
			echo "<td>&nbsp;</td></tr>";
		}
	}

	echo "<tr class=encabezadoTabla>";
	echo "<td>&nbsp;</td>";
	echo "<td>&nbsp;</td>";
	echo "<td>&nbsp;</td>";
	echo "<td>&nbsp;</td>";
	echo "<td>&nbsp;</td>";
	echo "<td>&nbsp;</td>";
	echo "<td>&nbsp;</td>";
	echo "<td>TOTAL ".number_format((float)$titulo,0,",",".")."</td>";
	echo "<td align=right>".number_format((float)$totalF,0,",",".")."</td>";
	echo "<td align=right>".number_format((float)$totalFP,0,",",".")."</td>";
	echo "<td align=right>".number_format((float)$egresarF,0,",",".")."</td>";
	echo "<td align=right>".number_format((float)$saldoF,0,",",".")."</td>";
	echo "<td>&nbsp;</td></tr>";
	echo "</table></br>";
}

function pintarBoton1($paso, $numCua, $comentario)
{
	global $wbasedato;
	echo "<Br><Br><CENTER>";

	switch ($paso)
	{
		case 1:
		echo "<input type='submit' value='Continuar >>'></CENTER>";
		echo "</form>";
		break;

		case 2:
		echo "<b>Comentario:</b><br><textarea name='comentario' size='80'></textarea></center><br>";
		echo "<b><center>Password para cuadre de caja:  <input type='password' name='pass' size='5'></center><br>";
		echo "<center><input type='checkbox' name='cuadrar'>Cuadrar	&nbsp;";
		echo "<input type='submit' value='Continuar >>'></CENTER>";
		echo "</form>";
		break;

		case 3:
		echo "<b>Comentario:</b><br><textarea name='comentario' size='80'>".$comentario."</textarea></center><br>";
		$fila='enter("'.$numCua.'", 2)';
		echo "<center>";
		echo "<input type='button' value='Anular' onclick='javascript:".$fila."'>&nbsp;|&nbsp;";
		$fila='enter("'.$numCua.'", 3)';
		echo "<input type='button' value='Impresión'  onclick='javascript:".$fila."'>";
		echo "</form>";
		echo "<form name='volver' method='Post' action=''>";
		echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
		echo "<input type='submit' value='Volver'>&nbsp;&nbsp;&nbsp;</CENTER>";
		echo "</form>";
		break;

		case 4:
		echo "<b>Comentario:</b><br><textarea name='comentario' size='80'>".$comentario."</textarea></center><br>";
		echo "<center>";
		$fila='enter("'.$numCua.'", 3)';
		echo "<input type='button' value='IMPRESION'  onclick='javascript:".$fila."'>&nbsp;&nbsp;&nbsp;";
		echo "</form>";
		echo "<form name='volver' method='Post' action=''>";
		echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
		echo "<input type='submit' value='VOLVER' >&nbsp;&nbsp;&nbsp;</CENTER>";
		echo "</form>";
		break;
	}
}

function consultarCuadresPorRangoFechas($codigoCaja, $centroCostos, $fechaInicial, $fechaFinal){
	global $conex;
	global $wbasedato;

	$q = "SELECT
				Fecha_data,Hora_data,Cencua
			FROM
				".$wbasedato."_000073
			WHERE
				Fecha_data BETWEEN '$fechaInicial' AND '$fechaFinal'
				AND Cencaj = '$codigoCaja'
				AND Cencco = '$centroCostos';";

//	echo $q;

	$coleccion = array();

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$cont1 = 0;
	if($num > 0 ){
		while($cont1 < $num)
		{
			$info = mysql_fetch_array($res);

			$registro = new EncabezadoCuadre();

			$registro->fechaCuadre 	= $info['Fecha_data'];
			$registro->horaCuadre	= $info['Hora_data'];
			$registro->numeroCuadre = $info['Cencua'];

			$cont1++;

			$coleccion[] = $registro;
		}
	}
	return $coleccion;
}

/*********************************************** PROGRAMA PRINCIPAL *************************************/
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else {
    /*PARAMETRO DE SEGURIDAD */
    $seguridad = "NO";

	include_once("root/comun.php");

	$conex = obtenerConexionBD("matrix");

	//Esto indica si la clave para cuadrar una caja debe validarse con el usuario
	if( isset($clv) ){
		echo "<INPUT type='hidden' value='1' name='clv' id='clv'>";
	}

	$wactualiz = "Agosto 2 de 2013";
	encabezado("Cuadre de caja",$wactualiz,"logo_".$wbasedato);

	//obtengo los datos de la caja
	if(!isset ($cajCod) or $cajCod == '')
	{
		if (!isset ($wcaja)) //lo mandan de los reportes para consultar el cuadre de una caja determinada
		{
			//consulto la caja del usuario que hace uso del sistema
			consultarCaja(substr($user,2), $cco, $cajCod, $cajDes, $cuaAnt , $resp);
		}
		else
		{
			//capturo y organizo los datos mandados por el reporte para la consulta
			$cco = trim($wcco);
			$exp=explode('-', $wcaja);
			$cajCod = trim($exp[0]);
			$cajDes = trim($exp[1]);
			$cuaAnt = '';
			consultarResponsable( $cco, $cajCod, $consulta, $resp);
		}
	}

	if (!isset ($wcaja)) //lo mandan de los reportes
	{
		$cco2 = $cco;
		$cajCod2 = $cajCod;
		$cajDes2 = $cajDes;
		$cuaAnt2 = $cuaAnt;
	}
	else
	{
		//consulto la caja del usuario del programa
		consultarCaja(substr($user,2), $cco2, $cajCod2, $cajDes2, $cuaAnt2 , $resp2);
	}

	if( /*true ||*/ isset ($cajCod) and $cajCod != '') //una vez encontrada la caja sobre la cual se va a hacer o buscar el cuadre
	{
		if (isset($consulta) and $consulta!='')// se realiza la consulta de un cuadrem la variable consulta tiene el numero del cuadre a consultar
		{
			$pagos=consultarPagos(); //consulto formas de pago

			//se pinta la forma con sus hidden y la opcion de busqueda de cuadres
			pintarFormulario( 1, ($consulta), $cco, $cajCod, $cajDes, $resp, $cuaAnt);
			pintarEncabezado(1,'','');

			/* MSanchez:: Nov 23-09.
			 *
			 * La variable $wtiporep indica si realizo la consulta por numero de cuadre o rango de fechas
			 * wtiporep = n: consulta por numero.
			 * wtiporep = f: consulta por rango de fechas
			 */
			if($wtiporep == "f"){    //Consulta por rangos de fechas
				$vecConsulta = explode("*", $consulta);

				$colCuadres = consultarCuadresPorRangoFechas($cajCod, $cco, $vecConsulta[0],$vecConsulta[1]);

				//Tabla con los cuadres segun el rango de fechas
				if(count($colCuadres) > 0){
					//Subtitulo de parámetros de consulta
					echo "<center>";
					echo '<span class="subtituloPagina2" align=center>';
					echo "Cuadres realizados entre el dia $vecConsulta[0] y $vecConsulta[1]";
					echo "</span>";
					echo "</center>";
					echo "<br>";
					echo "<br>";

					echo "<table align='center'>";
					echo "<tr class=encabezadoTabla align=center>";
					echo "<td>Fecha</td>";
					echo "<td>Hora</td>";
					echo "<td>Consecutivo</td>";
					echo "<td>Accion</td>";
					echo "</tr>";

					$clase = "fila2";
					foreach ($colCuadres as $cuadre){
						if($clase == "fila2"){
							$clase = "fila1";
						} else {
							$clase = "fila2";
						}

						echo "<tr class='$clase' align=center>";

						echo "<td>$cuadre->fechaCuadre</td>";
						echo "<td>$cuadre->horaCuadre</td>";
						echo "<td>$cuadre->numeroCuadre</td>";
						echo "<td><a href='javascript:irACuadre(\"$cuadre->numeroCuadre\")'>Consultar</a></td>";

						echo "</tr>";
					}
					echo "</table>";
				} else {
					pintarAlert1('NO EXISTEN CUADRES REALIZADOS EN EL RANGO DE FECHAS SELECCIONADO');
				}
			}

			if($wtiporep == "n"){	//Consulta por numero de cuadre
				//consulto el encabezado del cuadre (cantidad cuadrada, saldo que dejo sin cuadrar, comentario fecha y hora del cuadre y si esta o no anulado)
				consultarEncabezado($cajCod, $consulta, $cco, $egresado, $nuevoSaldo, $comentario, $fec, $hora, $estado );

				if ($fec!='') //si se encuentra el cuadre
				{
					$numCua=$consulta;
					//consulto los registro del cuadre que no estaban pendientes en un cuadre anterior
					$resu1=consultarRegistros ($cco, $cajCod, $numCua, $fuenteG, $numeroG, $fechaG, $valorG, $valorSG, $valorPG, $pagosG, $responsableG, $numeroBancosG, $numeroDevolucionesG, $valorDevG, $radioG,  $egresarG, $saldoG, '1', $radio2G, $venG, $ccoOrigenG );
					//consulto los registro del cuadre que estaban pendientes en un cuadre anterior
					$resu2=consultarRegistros ($cco, $cajCod, $numCua, $fuenteGA, $numeroGA, $fechaGA, $valorGA, $valorSGA, $valorPGA, $pagosGA, $responsableGA, $numeroBancosGA, $numeroDevolucionesGA, $valorDevGA,  $radioGA,  $egresarGA, $saldoGA, '2', $radio2G, $venGA, $ccoOrigenGA);
					if ($resu1 or $resu2) //si se encuentran registros en almenos una de las dos opciones
					{
						//pinto el titulo del programa con el numero del cuadre por hacer
						pintarConsulta($cajCod, $cajDes, $numCua, $fec, $resp, 3, $hora, $estado);

						if ($resu2) //si se encuentra registros que venian de otro cuadre, se calculan los totales en recibos, devoluciones, en lo egresado y el saldo por forma de pago y en total
						{           //se mandan por hidden cada uno de los valores de los registros
							calcularTotal2($pagosGA, $fuenteGA, $valorGA, $valorSGA, $totalSGA, $totalFSGA, $valorDevGA, $totalDevGA, $totalFDevGA, $radio2G, $numeroDevolucionesGA);
							calcularTotal($pagosGA, $fuenteGA, $valorGA, $totalGA, $totalFGA, $radio2G, $numeroDevolucionesGA);
							calcularTotal($pagosGA, $fuenteGA, $valorPGA, $totalPGA, $totalFPGA, $radio2G, $numeroDevolucionesGA);
							calcularTotal($pagosGA, $fuenteGA, $egresarGA, $egresarTGA, $egresarFGA, $radioGA, $numeroDevolucionesGA);
							calcularTotal($pagosGA, $fuenteGA, $saldoGA, $saldoTGA, $saldoFGA, $radioGA, $numeroDevolucionesGA);
							if(!isset($ccoOrigenGA)){
								$ccoOrigenGA = array();
							}
							pintarHidden($fuenteGA, $numeroGA, $fechaGA, $valorGA, $valorSGA, $valorPGA, $responsableGA, $numeroBancosGA, $numeroDevolucionesGA, '', $pagosGA, $valorDevGA, $totalGA, $totalFGA, $totalPGA, $totalFPGA, 'A', '', $radioGA, 1, $totalSGA, $totalFSGA, $totalDevGA, $totalFDevGA, '', $venGA, $ccoOrigenGA);
							if (!isset($tipCon) or $tipCon=='Detallada') //se pinta de forma detallada cada registo si la consulta es detallada
							{
								pintarDetallado('INGRESOS ANTERIORES', $fuenteGA, $numeroGA, $fechaGA, $valorGA, $valorSGA, $valorPGA, $responsableGA, $numeroBancosGA, $numeroDevolucionesGA, '',    $pagosGA,  $valorDevGA, $totalGA, $totalFGA, $totalPGA, $totalFPGA,  $radioGA,   $totalSGA,   $totalFSGA,  $totalDevGA, $totalFDevGA, $cco, 0,    '', $egresarGA, $saldoGA, $saldoTGA, $saldoFGA, $egresarTGA, $egresarFGA, 'A', $venGA, $ccoOrigenGA);
							}
						}
						else //si no se encuentra registros que venian de otro cuadre, se despliega mensaje cuando no es una consulta resumida
						{
							if (!isset($tipCon) or $tipCon=='Detallada')
							{
								pintarAlert7('INGRESOS ANTERIORES', $cajCod);
							}
						}

						if ($resu1) //para registro que no venian de otro cuadre se calculan los totales en recibos, devoluciones, en lo egresado y el saldo por forma de pago y en total
						{			 //se mandan por hidden cada uno de los valores de los registros
							calcularTotal2($pagosG, $fuenteG, $valorG, $valorSG, $totalSG, $totalFSG, $valorDevG, $totalDevG, $totalFDevG, $radio2G, $numeroDevolucionesG);
							calcularTotal($pagosG, $fuenteG, $valorG, $totalG, $totalFG, $radio2G, $numeroDevolucionesG);
							calcularTotal($pagosG, $fuenteG, $valorPG, $totalPG, $totalFPG, $radio2G, $numeroDevolucionesG);
							calcularTotal($pagosG, $fuenteG, $egresarG, $egresarTG, $egresarFG, $radioG, $numeroDevolucionesG);
							calcularTotal($pagosG, $fuenteG, $saldoG, $saldoTG, $saldoFG, $radioG, $numeroDevolucionesG);
							if(!isset($ccoOrigenG)){
								$ccoOrigenG = array();
							}
							pintarHidden($fuenteG, $numeroG, $fechaG, $valorG, $valorSG, $valorPG, $responsableG, $numeroBancosG, $numeroDevolucionesG, '', $pagosG, $valorDevG, $totalG, $totalFG, $totalPG, $totalFPG, 'B', '', $radioG, 1, $totalSG, $totalFSG, $totalDevG, $totalFDevG, '', $venG, $ccoOrigenG);
							if (!isset($tipCon) or $tipCon=='Detallada')//se pinta de forma detallada cada registo si la consulta es detallada
							{
								pintarDetallado('NUEVOS INGRESOS', $fuenteG, $numeroG, $fechaG, $valorG, $valorSG, $valorPG, $responsableG, $numeroBancosG, $numeroDevolucionesG,  '',    $pagosG, $valorDevG, $totalG, $totalFG, $totalPG, $totalFPG, $radioG, $totalSG,   $totalFSG,  $totalDevG, $totalFDevG, $cco, 0, '',    $egresarG, $saldoG, $saldoTG, $saldoFG, $egresarTG, $egresarFG,  'B', $venG, $ccoOrigenG);
							}
						}
						else //si no se encuentra registros que no venian de otro cuadre, se despliega mensaje cuando no es una consulta resumida
						{
							if (!isset($tipCon) or $tipCon=='Detallada')
							{
								pintarAlert7('NUEVOS INGRESOS', $cajCod);
							}
						}

						//Subtitulo de parámetros de consulta
						echo "<center>";
						echo '<span class="subtituloPagina2" align=center>';
						echo 'Resumen del cuadre';
						echo "</span>";
						echo "</center>";
						echo "<br>";
						echo "<br>";

						if ($resu2) //se pintan totales del cuadre para registros que venian de otros cuadres
						{

							pintarCuadrado('SALDO ANTERIOR',  $pagosGA, $totalPGA, $totalFPGA, $egresarTGA, $egresarFGA, $saldoTGA, $saldoFGA);

						}else
						{
							pintarAlert7('SALDOS ANTERIORES', $cajCod);
							$totalFPGA=0;
							$egresarFGA=0;
							$saldoFGA=0;
						}
						if ($resu1) //se pintan totales del cuadre para registros que no venian de otros cuadres
						{
							pintarCuadrado('NUEVOS INGRESOS',  $pagosG, $totalPG, $totalFPG, $egresarTG, $egresarFG, $saldoTG, $saldoFG);
						}else
						{
							pintarAlert7('NUEVOS INGRESOS', $cajCod);
							$totalFPG=0;
							$egresarFG=0;
							$saldoFG=0;
						}

						//se pinta el total de las dos opciones del cuadre
						pintarTotales(($totalFPG+$totalFPGA), ($egresarFG+$egresarFGA), ($saldoFG+$saldoFGA));

						//si la caja que consulta es la misma caja que hixo el cuadre, se pinta boton de anular el cuadre
						IF ($cajCod==$cajCod2 and $cco==$cco2 )//and $estado=='on'
						{
							pintarBoton1(3, $numCua, $comentario);
						}
						else //solo se pinta la opcion volver
						{
							pintarBoton1(4, $numCua, $comentario);
						}
					} else {//no se encuentran registros en ninguna de las dos opciones
						pintarAlert1('NO EXISTE UN CUADRE REALIZADO EN LA CAJA '.$cajCod.', PARA EL NUMERO '.$numCua);
					}
				} else { //no se obtienen resultados para ese numero de cuadre
					pintarAlert1('NO EXISTE UN CUADRE REALIZADO EN LA CAJA '.$cajCod.', PARA EL NUMERO '.$consulta);
				}
			}

		}else // generación o anulacion de un cuadre de caja
		{
			if (!isset($bandera1))//primera vez que se ingresa al sistema, se muestra el cuadre en forma resumida
			{
				$pagos=consultarPagos(); //consulto formas de pago

				//pinto la forma y la opcion para buscar un caudre
				pintarFormulario( 1, ($cuaAnt+1), $cco, $cajCod, $cajDes, $resp, $cuaAnt);
				pintarEncabezado(1,'','');
				//pinto el numero de cuadre que corresponderia al que se va a hacer
				pintarConsulta($cajCod, $cajDes, ($cuaAnt+1), date('Y-m-d'), $resp, 1, date("H:i:s"), ''); //pinto el titulo del programa con el numero del cuadre por hacer

				$contadorA=0;  //me cuenta cuantas formas de pago no tienen que ser cuadradas
				$contador=0;  //me cuenta cuantas formas de pago no tienen que ser cuadradas

				//realizaremos la distribución por tipo de pago para cada reciboy para las devoluciones de dinero

				for ($i=0;$i<count($pagos['codigo']);$i++)
				{
					//consulto para cada forma de pago los recibos y las devoluciones de dinero
					//caso en qeu viene de cuadres anteriores
					$registro1=consultarDocumentosAnteriores($pagos['codigo'][$i], $cajCod, $cco);
					//caso en que no vienen de cuadres anteriores
					$registro2=consultarDocumentosNuevos($pagos['codigo'][$i], $cajCod, $cco);

					//si encontramos recibos y devoluciones en el caso 1
					if ($registro1)
					{
						//organizo un vector para la forma de pago
						for ($j=0;$j<count($registro1['numero']);$j++)
						{
							$fuenteA[$i][$j]=$registro1['fuente'][$j];
							$numeroA[$i][$j]=$registro1['numero'][$j];
							$fechaA[$i][$j]=$registro1['fecha'][$j];
							$responsableA [$i][$j]=$registro1['responsable'][$j];
							$valorA[$i][$j]=$registro1['valor'][$j];
							$valorPA[$i][$j]=$registro1['valorP'][$j];
							$numeroBancosA[$i][$j]=$registro1['numeroBancos'][$j];
							$numeroDevolucionesA[$i][$j]=$registro1['numeroDevoluciones'][$j];
							$venA[$i][$j]=$registro1['ven'][$j];
							$radioA[$i][$j]='checked';

							$ccoOrigenA[$i][$j]=$registro1['ccoOrigen'][$j];		//Nov 18

							//para un recibo puede haber varios bancos finales en una sola forma de pago, los organizo en vector tridimensional
							for ($k=0;$k<$numeroBancosA[$i][$j];$k++)
							{
								$bancosA[$i][$j][$k]=$registro1['banco'][$j][$k];
								$idA[$i][$j][$k]=$registro1['id'][$j][$k];
							}

							if ($numeroBancosA[$i][$j]==0)
							{
								$bancosA[$i][$j][0]=$registro1['banco'][$j][0];
								$idA[$i][$j][0]=$registro1['id'][$j][0];
							}
							$contadorA++;
						}
					}

					//si encontramos recibos y devoluciones en el caso 2
					if ($registro2)
					{
						//organizo un vector para la forma de pago
						for ($j=0;$j<count($registro2['numero']);$j++)
						{
							$fuente[$i][$j]=$registro2['fuente'][$j];
							$numero[$i][$j]=$registro2['numero'][$j];
							$fecha[$i][$j]=$registro2['fecha'][$j];
							$responsable [$i][$j]=$registro2['responsable'][$j];
							$valor[$i][$j]=$registro2['valor'][$j];
							$valorP[$i][$j]=$registro2['valorP'][$j];
							$numeroBancos[$i][$j]=$registro2['numeroBancos'][$j];
							$numeroDevoluciones[$i][$j]=$registro2['numeroDevoluciones'][$j];
							$ven[$i][$j]=$registro2['ven'][$j];
							$radio[$i][$j]='checked';

							$ccoOrigen[$i][$j]=$registro2['cco'][$j];			//Nov 18

							for ($k=0;$k<$numeroBancos[$i][$j];$k++)
							{
								$bancos[$i][$j][$k]=$registro2['banco'][$j][$k];
								$id[$i][$j][$k]=$registro2['id'][$j][$k];
							}

							if ($numeroBancos[$i][$j]==0)
							{
								$bancos[$i][$j][0]=$registro2['banco'][$j][0];
								$id[$i][$j][0]=$registro2['id'][$j][0];
							}

							$contador++;
						}

					}

				}

				//si se encontraron recibos o devoluciones en alguno de los casos, calculo los totales para cada caso y el saldo
				//adicionalmente mando por hidden cada uno de los datos de los recibos o devoluciones
				//se pinta de forma resumida los totales de cada forma de pago de cada caso
				if ($contadorA>0 or $contador>0)
				{
					if ($contadorA>0)
					{
						// adecuo los valores de seleccion y calculo los totales
						calcularTotal2($pagos, $fuenteA, $valorA, $valorSA, $totalSA, $totalFSA, $valorDevA, $totalDevA, $totalFDevA, $radioA, $numeroDevolucionesA);
						calcularTotal($pagos, $fuenteA, $valorA, $totalA, $totalFA, $radioA, $numeroDevolucionesA);
						$radioA=calcularTotal($pagos, $fuenteA, $valorPA, $totalPA, $totalFPA, $radioA, $numeroDevolucionesA);
						if(!isset($ccoOrigenA)){
							$ccoOrigenA = array();
						}
						pintarHidden($fuenteA, $numeroA, $fechaA, $valorA, $valorSA, $valorPA, $responsableA, $numeroBancosA, $numeroDevolucionesA, $bancosA, $pagos, $valorDevA, $totalA, $totalFA, $totalPA, $totalFPA,'A', $contadorA, $radioA, '', $totalSA, $totalFSA, $totalDevA, $totalFDevA, $idA, $venA, $ccoOrigenA);
						pintarResumido('SALDO ANTERIOR',  $pagos, $totalPA, $totalFPA, $totalA, $totalFA, $totalSA, $totalFSA, $totalDevA, $totalFDevA, $fuenteA);
					}else //se despliega mensaje de que no se encontraron recibos o devoluciones con cuadres previos
					{
						pintarAlert7('SALDOS ANTERIORES', $cajCod);
					}
					if ($contador>0)
					{
						// adecuo los valores de seleccion y calculo los totales
						calcularTotal2($pagos, $fuente, $valor, $valorS, $totalS, $totalFS, $valorDev, $totalDev, $totalFDev, $radio, $numeroDevoluciones);
						calcularTotal($pagos, $fuente, $valor, $total, $totalF, $radio, $numeroDevoluciones);
						$radio=calcularTotal($pagos, $fuente, $valorP, $totalP, $totalFP, $radio, $numeroDevoluciones);
						if(!isset($ccoOrigen)){
							$ccoOrigen = array();
						}
						pintarHidden($fuente, $numero, $fecha, $valor, $valorS, $valorP, $responsable, $numeroBancos, $numeroDevoluciones, $bancos, $pagos, $valorDev, $total, $totalF, $totalP, $totalFP, 'B', $contador, $radio, '', $totalS, $totalFS, $totalDev, $totalFDev, $id, $ven, $ccoOrigen);
						pintarResumido('NUEVOS INGRESOS',  $pagos, $totalP, $totalFP, $total, $totalF, $totalS, $totalFS, $totalDev, $totalFDev, $fuente);

					}else //se despliega mensaje de que no se encontraron recibos o devoluciones sin cuadres previos
					{
						pintarAlert7('NUEVOS INGRESOS', $cajCod);
					}
					pintarBoton1(1, '', ''); //para continuar creando el cuadre
				}
				else //no se encontro ningun recibo o devolucion de dinero por cuadrar
				{
					pintarAlert1('LA CAJA '.$cajCod.' NO TIENE NINGUN RECIBO PENDIENTE PARA CUADRAR');
				}
			}

			if (isset ($bandera1) and $bandera1==1) //despliegue detallado del cuadre de caja y opcion para seleccionar los recibos y devoluciones a cuadrar
			{
				if ($cambioBancos==1)//cuando el programa de cambio de bancos se ejecuta, manda la variable $cambioBancos
				{					//para indicar que hay que volver a consultar los bancos porque han sido modificados
					if (isset ($bancosA))
					{
						unset ($bancosA); //se vuelve a consultar el vector de bancos para cada recibo o devolucion en caso1
						$bancosA=consultarBancos($pagosA, $fuenteA, $numeroA, $cco);
					}
					if (isset ($bancos))
					{
						unset ($bancos); //se vuelve a consultar el vector de bancos para cada recibo o devolucion en caso 2
						$bancos=consultarBancos($pagos, $fuente, $numero, $cco);
					}
				}

				//valores totales o suma de caso 1 y caso 2
				$egresado=0;
				$nuevoSaldo=0;

				//pintamos los titulos del programa y el formulario
				pintarEncabezado(2, $cajCod."-".$cajDes ,$numCua);
				pintarFormulario( 1, $numCua, $cco, $cajCod, $cajDes, $resp, $cuaAnt);

				if (isset ($contadorA) and $contadorA>0)
				{
					// adecuo los valores de seleccion y calculo los totales de egreso y de saldo en caso 1
					// aviso indica si se han seleccionado valores mayores a egresar de lo que es posible
					$mensaje=validarParejas($pagosA, $fuenteA, $radioA, $numeroDevolucionesA, $numeroA, $cco);
					$aviso=calcularSaldo($pagosA, $fuenteA, $egresarA, $valorPA, $saldoA, $radioA, $saldoTA, $saldoFA, $numeroDevolucionesA);
					$radioA=calcularTotal($pagosA, $fuenteA, $egresarA, $egresarTA, $egresarFA, $radioA, $numeroDevolucionesA);
					$egresado=$egresado+$egresarFA;
					$nuevoSaldo=$nuevoSaldo+$saldoFA;
				}

				if (isset ($contador) and $contador>0)
				{
					// adecuo los valores de seleccion y calculo los totales de egreso y de saldo en caso 2
					// aviso indica si se han seleccionado valores mayores a egresar de lo que es posible
					$mensaje=validarParejas($pagos, $fuente, $radio, $numeroDevoluciones, $numero, $cco);
					$aviso=calcularSaldo($pagos, $fuente, $egresar, $valor, $saldo, $radio, $saldoT, $saldoF, $numeroDevoluciones);
					$radio=calcularTotal($pagos, $fuente, $egresar, $egresarT, $egresarF, $radio, $numeroDevoluciones);
					$egresado=$egresado+$egresarF;
					$nuevoSaldo=$nuevoSaldo+$saldoF;
				}

				if ($egresado<0) //el cuadre debe ser siempre con total positivo, en caso contrario no se puede grabar el cuadre
				{
					pintarAlert3('EL VALOR A EGRESAR DEBE SER POSITIVO');
					if (isset($cuadrar))
					{
						unset ($cuadrar);
					}
				}

				if($mensaje!='')
				{
					pintarAlert3($mensaje);
					if (isset($cuadrar))
					{
						unset ($cuadrar);
					}
				}
				if (!$aviso) //en caso de que hayan saldos que superen el valor a egresar
				{			//no puede grabar el cuadre
					pintarAlert3('LOS SALDOS A EGRESAR DEBEN SER MENORES O IGUALES AL VALOR PENDIENTE (ERRORES EN ROJO)');
					if (isset($cuadrar))
					{
						unset ($cuadrar);
					}
				}
				else if (isset($cuadrar)) //en caso de que la variable $cuadrar(checkbox) existe, y se hayan pasado validaciones
				{
					//se graba el cuadre
					$validacion=confirmarPassword($pass, substr($user,2)); //validacion del password del cajero
					IF (!$validacion)
					{
						pintarAlert3('PARA REALIZAR EL CUADRE DEBE INGRESAR EL PASSWORD CORRECTO');
						unset ($cuadrar);
					}
					else //se pasa validacion del password del cajero
					{
						//realizamos las actividades de almacenamiento y cambios de estado en db
						$noconcurrencia=incrementarConsecutivo($cajCod, $numCua);

						if ($noconcurrencia)
						{
							$numCua=$noconcurrencia;
							//guardar el encabezado
							//On grabarEncabezado($numCua, $cajCod, $cco, $egresado, $nuevoSaldo,  substr($user,2), $comentario);
							grabarEncabezado($numCua, $cajCod, $cco, $egresado, $nuevoSaldo,  $usu, $comentario);
							//se almacenan los recibos y devoluciones del cuadre que tenian cuadres anteriores
							if (isset ($contadorA) and $contadorA>0)
							{
								for ($i=0;$i<count($pagosA['codigo']);$i++)
								{
									if (isset($fuenteA[$i]))
									{
										for ($j=0;$j<count($fuenteA[$i]);$j++) //caso 1. venian los recibos de otros cuadres
										{
											///On grabarDetalle($numCua,  $cajCod, $cco, $numeroA[$i][$j], $fuenteA[$i][$j], $egresarA[$i][$j], $pagosA['codigo'][$i], substr($user,2), $saldoA[$i][$j], $valorPA[$i][$j]);
											grabarDetalle($numCua,  $cajCod, $cco, $numeroA[$i][$j], $fuenteA[$i][$j], $egresarA[$i][$j], $pagosA['codigo'][$i], $usu, $saldoA[$i][$j], $valorPA[$i][$j], $ccoOrigenA[$i][$j] );
											if ($saldoA[$i][$j]>0) //si el saldo del recibo o devolucion es mayor a cero
											{
												if ($saldoA[$i][$j]==$valorA[$i][$j]) //si no se cuadro nada del documento se pone en estado pendiente
												{
													cambiarEstado($numeroA[$i][$j], $fuenteA[$i][$j], 'P', $cajCod, $pagosA['codigo'][$i], $ccoOrigenA[$i][$j] );

												}
												else //si se cuadro una parte s epone en estado incompleto
												{
													cambiarEstado($numeroA[$i][$j], $fuenteA[$i][$j], 'I', $cajCod, $pagosA['codigo'][$i], $ccoOrigenA[$i][$j] );
												}
												//se actualiza en la tabla de pendientes (37) el valor pendiente
												actualizarPendiente( $numCua, $cajCod, $cco, $numeroA[$i][$j], $fuenteA[$i][$j], $pagosA['codigo'][$i], substr($user,2), $saldoA[$i][$j]);
											}
											else //si no se dejo saldo para el recibo, se pone en estado cuadrado y se borra de pendiente
											{
												cambiarEstado($numeroA[$i][$j], $fuenteA[$i][$j], 'C', $cajCod, $pagosA['codigo'][$i], $ccoOrigenA[$i][$j] );
												borrarPendiente( $cajCod, $cco, $numeroA[$i][$j], $fuenteA[$i][$j], $pagosA['codigo'][$i]);
											}
										}
									}
								}
							}
							if (isset ($contador) and $contador>0)
							{
								for ($i=0;$i<count($pagos['codigo']);$i++)
								{
									if (isset($fuente[$i])) //caso 2. no venian los recibos de otros cuadres
									{
										for ($j=0;$j<count($fuente[$i]);$j++)
										{
											//On grabarDetalle($numCua,  $cajCod, $cco, $numero[$i][$j], $fuente[$i][$j], $egresar[$i][$j], $pagos['codigo'][$i], substr($user,2), $saldo[$i][$j], '0');
											grabarDetalle($numCua,  $cajCod, $cco, $numero[$i][$j], $fuente[$i][$j], $egresar[$i][$j], $pagos['codigo'][$i], $usu, $saldo[$i][$j], '0', $ccoOrigen[$i][$j] );
											if ($saldo[$i][$j]>0) //si el saldo del cuadre del recibo en mayor a cero
											{
												if ($saldo[$i][$j]==$valor[$i][$j]) //si no se cuadro nada del documento queda en estado pendiente
												{
													cambiarEstado($numero[$i][$j], $fuente[$i][$j], 'P', $cajCod, $pagos['codigo'][$i], $ccoOrigen[$i][$j] );

												}
												else //si se cuadro parte queda en estado incompleto
												{
													cambiarEstado($numero[$i][$j], $fuente[$i][$j], 'I', $cajCod, $pagos['codigo'][$i], $ccoOrigen[$i][$j] );
												}
												//se graba el recibo o la devolucion en la tabla temporal de pendientes (37)
												///On grabarPendiente( $numCua, $cajCod, $cco, $numero[$i][$j], $fuente[$i][$j], $pagos['codigo'][$i], substr($user,2), $saldo[$i][$j]);
												grabarPendiente( $numCua, $cajCod, $cco, $numero[$i][$j], $fuente[$i][$j], $pagos['codigo'][$i], $usu, $saldo[$i][$j]);
											}
											else //si al cuadrar no queda saldo pendiente, el recibo queda en estado cuadrado
											{
												cambiarEstado($numero[$i][$j], $fuente[$i][$j], 'C', $cajCod, $pagos['codigo'][$i], $ccoOrigen[$i][$j] );
											}
										}
									}
								}
							}


							//consultar y pintar el cuadre grabado de forma detallada y resumida
							consultarEncabezado($cajCod, $numCua, $cco, $egresado, $nuevoSaldo, $comentario, $fec, $hora, $estado );
							if ($fec!='')
							{		//consulto los registros que venian de otro cuadre(2) y los que no venian (1)
								$resu1=consultarRegistros ($cco, $cajCod, $numCua, $fuenteG, $numeroG, $fechaG, $valorG, $valorSG, $valorPG, $pagosG, $responsableG, $numeroBancosG, $numeroDevolucionesG, $valorDevG, $radioG,  $egresarG, $saldoG, '1', $radio2G,  $venG, $ccoOrigenG);
								$resu2=consultarRegistros ($cco, $cajCod, $numCua, $fuenteGA, $numeroGA, $fechaGA, $valorGA, $valorSGA, $valorPGA, $pagosGA, $responsableGA, $numeroBancosGA, $numeroDevolucionesGA, $valorDevGA, $radioGA,  $egresarGA, $saldoGA, '2', $radio2G,  $venGA, $ccoOrigenGA);
								if ($resu1 or $resu2)
								{
									pintarConsulta($cajCod, $cajDes, $numCua, $fec, $resp, 2, $hora, $estado);

									if ($resu2)//calculo los totales por forma de pago para los que venian de otro cuadre, envio por hidden
									{			//todos los valores y pinto de forma detallada cada registro
										calcularTotal2($pagosGA, $fuenteGA, $valorGA, $valorSGA, $totalSGA, $totalFSGA, $valorDevGA, $totalDevGA, $totalFDevGA, $radio2G, $numeroDevolucionesGA);
										calcularTotal($pagosGA, $fuenteGA, $valorGA, $totalGA, $totalFGA, $radio2G, $numeroDevolucionesGA);
										calcularTotal($pagosGA, $fuenteGA, $valorPGA, $totalPGA, $totalFPGA, $radio2G, $numeroDevolucionesGA);
										calcularTotal($pagosGA, $fuenteGA, $egresarGA, $egresarTGA, $egresarFGA, $radioGA, $numeroDevolucionesGA);
										calcularTotal($pagosGA, $fuenteGA, $saldoGA, $saldoTGA, $saldoFGA, $radioGA, $numeroDevolucionesGA);
										if(!isset($ccoOrigenGA)){
											$ccoOrigenGA = array();
										}
										pintarHidden($fuenteGA, $numeroGA, $fechaGA, $valorGA, $valorSGA, $valorPGA, $responsableGA, $numeroBancosGA, $numeroDevolucionesGA, '', $pagosGA, $valorDevGA, $totalGA, $totalFGA, $totalPGA, $totalFPGA, 'A', '', $radioGA, 1, $totalSGA, $totalFSGA, $totalDevGA, $totalFDevGA, '', $venGA, $ccoOrigenGA);
										pintarDetallado('INGRESOS ANTERIORES', $fuenteGA, $numeroGA, $fechaGA, $valorGA, $valorSGA, $valorPGA, $responsableGA, $numeroBancosGA, $numeroDevolucionesGA, '', $pagosGA, $valorDevGA, $totalGA, $totalFGA, $totalPGA, $totalFPGA,  $radioGA, $totalSGA,   $totalFSGA,  $totalDevGA, $totalFDevGA, $cco, 0, '', $egresarGA, $saldoGA, $saldoTGA, $saldoFGA, $egresarTGA, $egresarFGA, 'A', $venGA, $ccoOrigenGA);
									}
									else //muestro aviso sino hay registro de docuemtnos que vinieran de otros cuadres
									{

										pintarAlert7('INGRESOS ANTERIORES', $cajCod);
									}

									if ($resu1) //calculo los totales por forma de pago para los que no venian de otro cuadre, envio por hidden
									{			//todos los valores y pinto de forma detallada cada registro
										calcularTotal2($pagosG, $fuenteG, $valorG, $valorSG, $totalSG, $totalFSG, $valorDevG, $totalDevG, $totalFDevG, $radio2G, $numeroDevolucionesG);
										calcularTotal($pagosG, $fuenteG, $valorG, $totalG, $totalFG, $radio2G, $numeroDevolucionesG);
										calcularTotal($pagosG, $fuenteG, $valorPG, $totalPG, $totalFPG, $radio2G, $numeroDevolucionesG);
										calcularTotal($pagosG, $fuenteG, $egresarG, $egresarTG, $egresarFG, $radioG, $numeroDevolucionesG);
										calcularTotal($pagosG, $fuenteG, $saldoG, $saldoTG, $saldoFG, $radioG, $numeroDevolucionesG);
										if(!isset($ccoOrigenG)){
											$ccoOrigenG = array();
										}
										pintarHidden($fuenteG, $numeroG, $fechaG, $valorG, $valorSG, $valorPG, $responsableG, $numeroBancosG, $numeroDevolucionesG, '', $pagosG, $valorDevG, $totalG, $totalFG, $totalPG, $totalFPG, 'B', '', $radioG, 1, $totalSG, $totalFSG, $totalDevG, $totalFDevG, '', $venG, $ccoOrigenG);
										pintarDetallado('NUEVOS INGRESOS', $fuenteG, $numeroG, $fechaG, $valorG, $valorSG, $valorPG, $responsableG, $numeroBancosG, $numeroDevolucionesG,  '',    $pagosG, $valorDevG, $totalG, $totalFG, $totalPG, $totalFPG, $radioG, $totalSG,   $totalFSG,  $totalDevG, $totalFDevG, $cco, 0, '',    $egresarG, $saldoG, $saldoTG, $saldoFG, $egresarTG, $egresarFG,  'B', $venG, $ccoOrigenG);
									}
									else //muestro aviso si no hay registro de docuemtnos que no vinieran de otros cuadres
									{
										pintarAlert7('NUEVOS INGRESOS', $cajCod);
									}

									//Subtitulo de parámetros de consulta
									echo "<center>";
									echo '<span class="subtituloPagina2" align=center>';
									echo 'Resumen del cuadre';
									echo "</span>";
									echo "</center>";
									echo "<br>";
									echo "<br>";

									if ($resu2) //pinto totales por forma de pafo para los que venian de otro cuadre
									{
										pintarCuadrado('SALDO ANTERIOR',  $pagosGA, $totalPGA, $totalFPGA, $egresarTGA, $egresarFGA, $saldoTGA, $saldoFGA);

									}else
									{
										pintarAlert7('SALDOS ANTERIORES', $cajCod);
										$totalFPGA=0;
										$egresarFGA=0;
										$saldoFGA=0;
									}
									if ($resu1) //pinto totales por forma de pafo para los que no venian de otro cuadre
									{
										pintarCuadrado('NUEVOS INGRESOS',  $pagosG, $totalPG, $totalFPG, $egresarTG, $egresarFG, $saldoTG, $saldoFG);
									}else
									{
										pintarAlert7('NUEVOS INGRESOS', $cajCod);
										$totalFPG=0;
										$egresarFG=0;
										$saldoFG=0;
									}
									//pinto totales fgenerales, sumatorio de recibos que venian de otros cuadre y los que no
									pintarTotales(($totalFPG+$totalFPGA), ($egresarFG+$egresarFGA), ($saldoFG+$saldoFGA));

									pintarBoton1(3, $numCua, $comentario); //boton para volver a inciar proceso o anular el cuadre
								}
								else //no se encuentran registros para ese cuadre
								{
									pintarAlert1('NO EXISTE UN CUADRE REALIZADO EN LA CAJA '.$cajCod.', PARA EL NUMERO '.$numCua);
								}
							}else //no se obtienen resultados para ese numero de cuadre
							{
								pintarAlert1('NO EXISTE UN CUADRE REALIZADO EN LA CAJA '.$cajCod.', PARA EL NUMERO '.$numCua);
							}
						}
						else
						{
							pintarAlert1( "El cuadre ".$numCua." fue efectuado mientras usted estaba realizando este cuadre, así que debe volver a empezar.");
						}
					}

				}

				if (!isset($cuadrar)) //en caso de no estar setiada cuadrar, creo los hidden de los registros y vuelvo a pintar con
				{					//los totales actualizados, segun si existen o no documentos que vienen de otros cuadres (A) y documenots que no
					if (isset ($contadorA) and $contadorA>0)
					{
						if(!isset($ccoOrigenA)){
							$ccoOrigenA = array();
						}

						pintarHidden($fuenteA, $numeroA, $fechaA, $valorA, $valorSA, $valorPA, $responsableA, $numeroBancosA, $numeroDevolucionesA, $bancosA, $pagosA, $valorDevA, $totalA, $totalFA, $totalPA, $totalFPA,'A', $contadorA, $radioA, 1, $totalSA, $totalFSA, $totalDevA, $totalFDevA, $idA, $venA, $ccoOrigenA);
						pintarDetallado('SALDOS ANTERIORES', $fuenteA, $numeroA, $fechaA, $valorA, $valorSA, $valorPA, $responsableA, $numeroBancosA, $numeroDevolucionesA, $bancosA, $pagosA, $valorDevA,  $totalA, $totalFA, $totalPA, $totalFPA,  $radioA, $totalSA, $totalFSA, $totalDevA, $totalFDevA, $cco, 1, $idA, $egresarA, $saldoA, $saldoTA, $saldoFA, $egresarTA, $egresarFA,  'A', $venA, $ccoOrigenA);
					}else
					{
						pintarAlert7('SALDOS ANTERIORES', $cajCod);
					}

					if (isset ($contador) and $contador>0)
					{
						if(!isset($ccoOrigen)){
							$ccoOrigen = array();
						}
						pintarHidden($fuente, $numero, $fecha, $valor, $valorS, $valorP, $responsable, $numeroBancos, $numeroDevoluciones, $bancos, $pagos,  $valorDev,  $total, $totalF, $totalP, $totalFP, 'B', $contador, $radio, 1, $totalS, $totalFS, $totalDev, $totalFDev, $id, $ven, $ccoOrigen);
						pintarDetallado('NUEVOS INGRESOS', $fuente, $numero, $fecha, $valor, $valorS, $valorP, $responsable, $numeroBancos, $numeroDevoluciones, $bancos, $pagos, $valorDev, $total, $totalF, $totalP, $totalFP,  $radio, $totalS, $totalFS, $totalDev, $totalFDev, $cco, 1, $id, $egresar, $saldo, $saldoT, $saldoF, $egresarT, $egresarF, 'B', $ven, $ccoOrigen);
					}else
					{

						pintarAlert7('NUEVOS INGRESOS', $cajCod);
					}

					//Subtitulo de parámetros de consulta
					echo "<center>";
					echo '<span class="subtituloPagina2" align=center>';
					echo 'Resumen del cuadre';
					echo "</span>";
					echo "</center>";
					echo "<br>";
					echo "<br>";

					if (isset ($contadorA) and $contadorA>0)
					{

						pintarCuadrado('SALDO ANTERIOR',  $pagosA, $totalPA, $totalFPA, $egresarTA, $egresarFA, $saldoTA, $saldoFA);

					}else
					{

						pintarAlert7('SALDOS ANTERIORES', $cajCod);
					}
					if (isset ($contador) and $contador>0)
					{

						pintarCuadrado('NUEVOS INGRESOS',  $pagos, $totalP, $totalFP, $egresarT, $egresarF, $saldoT, $saldoF);
					}else
					{

						pintarAlert7('NUEVOS INGRESOS', $cajCod);
					}

					pintarBoton1(2, '', ''); //muestra el checkbox para grabar el cuadre y los campos para contraseña y observacion
				}
			}

			if (isset ($bandera1) and $bandera1==2)// 	PROCESO DE ANULACION
			{
				if (isset ($fuenteA)) //si ahy documentos que venian de otros cuadres
				{ 					//valido que pueda anularse el cuadre para esos recibos (que no hayan trasladados), entre otros
					$permisoA=consultarPermiso($pagosA, $fuenteA, $numCua, $numeroA, $cajCod, $cco);
				}
				else
				{
					$permisoA=true;

				}

				if( isset ($fuente) ) //si ahy documentos que no vienen de otros cuadres
				{					//valido que pueda anularse el cuadre para esos recibos (que no hayan trasladados), entre otros
					$permiso = consultarPermiso($pagos, $fuente, $numCua, $numero, $cajCod, $cco);
				}
				else
				{
					$permiso = true;
				}

				if ($permiso and $permisoA) //se realiza anulación
				{
					if (isset ($fuenteA))
					{
						for ($i=0;$i<count($pagosA['codigo']);$i++) //para cada uno de los recibos que venian de otros cuadres
						{
							for ($j=0;$j<count($fuenteA[$i]);$j++)
							{
								if ($saldoA[$i][$j]>0)//si dejaron un saldo mayor a cero en el cuadre
								{
									//solo es necesario cambio si el saldo pendiente no es igual al valor del recibo
									if ($saldoA[$i][$j]!=$valorA[$i][$j]) //en ese caso se cambia el estado a incomplero
									{									// se actualiza el valor pendiente en la tabal de pendiente
										//aca necesito saber si el valor pendiente es el mismo valor de recibo
										if ($valorPA[$i][$j]==$valorA[$i][$j]) //si era el mismo el valor anterior era P
										{
											cambiarEstado($numeroA[$i][$j], $fuenteA[$i][$j], 'P', $cajCod, $pagosA['codigo'][$i], $ccoOrigenA[$i][$j] );
										}
										else //si es diferente el estado anterior era I
										{
											cambiarEstado($numeroA[$i][$j], $fuenteA[$i][$j], 'I', $cajCod, $pagosA['codigo'][$i], $ccoOrigenA[$i][$j] );

										}
										actualizarPendiente( $numCua, $cajCod, $cco, $numeroA[$i][$j], $fuenteA[$i][$j], $pagosA['codigo'][$i], substr($user,2), $valorPA[$i][$j] );
									}
								}
								else //se realizo el cuadre por elvalor pendiente completo
								{
									//aca necesito saber si el valor pendiente es el mismo valor de recibo
									if ($valorPA[$i][$j]==$valorA[$i][$j]) //si era el mismo el valor anterior era P
									{
										cambiarEstado($numeroA[$i][$j], $fuenteA[$i][$j], 'P', $cajCod, $pagosA['codigo'][$i], $ccoOrigenA[$i][$j] );
									}
									else //si es diferente el estado anterior era I
									{
										cambiarEstado($numeroA[$i][$j], $fuenteA[$i][$j], 'I', $cajCod, $pagosA['codigo'][$i], $ccoOrigenA[$i][$j] );

									}
									grabarPendiente( $numCua, $cajCod, $cco, $numeroA[$i][$j], $fuenteA[$i][$j], $pagosA['codigo'][$i], substr($user,2),$valorPA[$i][$j]);
								}
								anularRegistro($numCua, $cajCod, $numeroA[$i][$j], $fuenteA[$i][$j], $pagosA['codigo'][$i], $cco);
							}

						}
					}
					if (isset($fuente)) //registros del cuadre que no vnian de otros cuadres
					{
						for ($i=0;$i<count($pagos['codigo']);$i++)
						{

							for ($j=0;$j<count($fuente[$i]);$j++)
							{
								if ($saldo[$i][$j]>0) //en caso de haber quedado pendiente, este se borra de la tabla
								{
									borrarPendiente($cajCod, $cco, $numero[$i][$j], $fuente[$i][$j], $pagos['codigo'][$i]);
								}

								//Diciembre 03 - 2009
								//Si el codigo de caja del usuario actual es distinto al cco origen, su estado anterior era
								//de traslado
								//Si el cco Origen es vacio significa que el cco es igual al de origen
								if( $cajCod != $ccoOrigen && !empty($ccoOrigen) ){
									cambiarEstado($numero[$i][$j], $fuente[$i][$j], 'T', $cajCod, $pagos['codigo'][$i], $ccoOrigen[$i][$j] );
								}
								else{
									cambiarEstado($numero[$i][$j], $fuente[$i][$j], 'S', $cajCod, $pagos['codigo'][$i], $ccoOrigen[$i][$j] );
								}

								anularRegistro($numCua, $cajCod, $numero[$i][$j], $fuente[$i][$j], $pagos['codigo'][$i], $cco);
							}
						}
					}

					anularEncabezado($numCua, $cco, $cajCod);
					pintarAlert1('EL CUADRE NUMERO '.$numCua. ' PARA LA CAJA '. $cajCod. ' HA SIDO ANULADO');

				}else //mensaje de no poder realizar anulacion por no haber pasado validaciones
				{
					pintarAlert1('EL CUADRE NUMERO '.$numCua. ' PARA LA CAJA '. $cajCod. ' YA NO PUEDE SER ANULADO');
				}
			}
		}
		if (isset ($bandera1) and $bandera1==3)// 	impresion gigante del resumen del cuadre
		{
			//se realiza consulta del cuadre con sus registros que venian de otros cuadres (2) y los que no (1)
			consultarEncabezado($cajCod, $numCua, $cco, $egresado, $nuevoSaldo, $comentario, $fec, $hora, $estado );
			$resu1=consultarRegistros ($cco, $cajCod, $numCua, $fuenteG, $numeroG, $fechaG, $valorG, $valorSG, $valorPG, $pagosG, $responsableG, $numeroBancosG, $numeroDevolucionesG, $valorDevG,  $radioG,  $egresarG, $saldoG, '1', $radio2G, $venG, $origen );
			$resu2=consultarRegistros ($cco, $cajCod, $numCua, $fuenteGA, $numeroGA, $fechaGA, $valorGA, $valorSGA, $valorPGA, $pagosGA, $responsableGA, $numeroBancosGA, $numeroDevolucionesGA, $valorDevGA, $radioGA,  $egresarGA, $saldoGA, '2', $radio2G, $venGA, $origen );
			if ($resu1 or $resu2) //si se encuentran recibos para alguno de los dos casos
			{	//impresion grande del encabezado
				imprimirGrande($cajCod, $cajDes, $numCua, $fec, $hora, $resp, $estado);

				if ($resu2) //si hay documentos que venian de otros cuadres, se recaluclan totales y saldos y se imprime resumen
				{			//y se imprimen totales por forma de pago
					calcularTotal2($pagosGA, $fuenteGA, $valorGA, $valorSGA, $totalSGA, $totalFSGA,$valorDevGA, $totalDevGA, $totalFDevGA, $radio2G, $numeroDevolucionesGA);
					calcularTotal($pagosGA, $fuenteGA, $valorGA, $totalGA, $totalFGA, $radio2G, $numeroDevolucionesGA);
					calcularTotal($pagosGA, $fuenteGA, $valorPGA, $totalPGA, $totalFPGA, $radio2G, $numeroDevolucionesGA);
					calcularTotal($pagosGA, $fuenteGA, $egresarGA, $egresarTGA, $egresarFGA, $radioGA, $numeroDevolucionesGA);
					calcularTotal($pagosGA, $fuenteGA, $saldoGA, $saldoTGA, $saldoFGA, $radioGA, $numeroDevolucionesGA);
					imprimirResumen('SALDOS ANTERIORES',  $pagosGA, $totalPGA, $totalFPGA, $egresarTGA, $egresarFGA, $saldoTGA, $saldoFGA);
				}
				else
				{
					//pintarAlert7('SALDOS ANTERIORES', $cajCod);
					$totalFPGA=0;
					$egresarFGA=0;
					$saldoFGA=0;
					$pagosGA['codigo'][0]='0';
					$pagosGA['nombre'][0]='0';
					$totalPGA[0]=0;
					$egresarTGA[0]=0;
					$saldoTGA[0]=0;

				}
				if ($resu1) //si hay documentos que no venian de otros cuadres, se recaluclan totales y saldos y se imprime resumen
				{			//y se imprimen totales por forma de pago
					calcularTotal2($pagosG, $fuenteG, $valorG, $valorSG, $totalSG, $totalFSG, $valorDevG, $totalDevG, $totalFDevG, $radio2G, $numeroDevolucionesG);
					calcularTotal($pagosG, $fuenteG, $valorG, $totalG, $totalFG, $radio2G, $numeroDevolucionesG);
					calcularTotal($pagosG, $fuenteG, $valorPG, $totalPG, $totalFPG, $radio2G, $numeroDevolucionesG);
					calcularTotal($pagosG, $fuenteG, $egresarG, $egresarTG, $egresarFG, $radioG, $numeroDevolucionesG);
					calcularTotal($pagosG, $fuenteG, $saldoG, $saldoTG, $saldoFG, $radioG, $numeroDevolucionesG);
					imprimirResumen('NUEVOS INGRESOS',  $pagosG, $totalPG, $totalFPG, $egresarTG, $egresarFG, $saldoTG, $saldoFG);
				}
				else
				{
					//pintarAlert7('NUEVOS INGRESOS', $cajCod);
					$totalFPG=0;
					$egresarFG=0;
					$saldoFG=0;
					$pagosG['codigo'][0]='0';
					$pagosG['nombre'][0]='0';
					$totalPG[0]=0;
					$egresarTG[0]=0;
					$saldoTG[0]=0;
				}
				//se imprimen totales por forma de pago
				$lista=consultarPagos();
				imprimirTotales(($totalFPG+$totalFPGA), ($egresarFG+$egresarFGA), ($saldoFG+$saldoFGA), $totalPG, $egresarTG, $saldoTG, $totalPGA, $egresarTGA, $saldoTGA, $pagosGA, $pagosG, $lista);
			}
		}
	}else //si no se encuentra una caja para el usuario, no es posible la utilizacion del programa
	{
		pintarAlert1('EL USUARIO NO TIENE PERMISO PARA REALIZAR CUADRES DE CAJA');
	}
	include_once("free.php");

}
?>
</body>
</html>
