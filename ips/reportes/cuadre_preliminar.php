<html>
<head>
  <title>CUADRE CAJA - POS</title>
</head>
<body>

<?php
include_once("conex.php");
/**
 * PROGRAMA PRINCIPAL DE PROCESAMIENTO DEL CUADRE DE CAJA
 * 
 * Muestra el detalle del estado de la caja en cuatro partes: sus saldos anteriores y los nuevos ingresos cada uno en tanto en recibos 
 * de caja (ingresos por venta) y en notas crédito.  Dentro de cada una de las cuatro partes lor recibos se desglosan por la forma de 
 * pago, y se ordenan por forma de pago y fecha.
 * Presenta consolidados por forma de pago dentro de las partes,  un consolidado total para cada una de las partes, uno para saldos 
 * anteriores y uno para nuevos ingresos, y finalmente uno total para la caja .
 *  
 * Permite al usuario elegir cuales recibos desea que entren en el cuadre de caja y cuales no, adicionalmente para los recibos en efectivo 
 * permite egresar parcialmente el valor del recibo. Pide contraseña para poder efectuar el cuadre, la busca en la tabla de maestro de 
 * cajeros, si no existe, o si no esta asociada a un usuario que a su vez tenga la caja a cuadrar asignada no permite realizar el cuadre.
 * 
 * Este programa también se encarga de la grabación en tabla 000037 de todas las formas de pago de los recibos que comprenden el cuadre,
 * el valor que quedo por egresar de cada forma de pago se almacena en Cdevrf (Cuadre Detalle valor restante en la forma de pago).
 * 
 * <b>Consideraciones especiales</b><br>
 * No se puede efectuar un cuadre que no tenga ningún recibo, pues produce un bloqueo del sistema, incluso una caida del servidor. 
 * La variable que controla que no suceda esta situación es $cuadreVacio.<br>
 * Debe manejarse con cuidado los consecutivos, a excepción del primer cuadre, si no existe un cuadre anterior al actual el programa entra
 * en fallo y puede ocacionar la caida del servidor. <br>
 * Por el mismo motivo no se pueden cambiar los centros de costos de las caja ni demás parámetros de configuración sin un estudio cuidadoso, 
 * pues puede ocurrir que al buscar un cudre anterior no lo encuentre y se produsca un fallo en el programa y el servidor.
 * 
 *  
 * @author Ana María Betancur Vargas
 * @created 2005-08-01
 * @version 2006-10-04
 * 
 * @wvar String[4]	$wcco 		Codigo Centro de costos.
 * @wvar String		$wnomcco 	Nombre del centro de costos.
 * @wvar String		$wcaja		Código Caja objeto del cuadre.
 * @wvar String		$wnomcaja	Nombre Caja  objeto del cuadre.
 * @wvar String[2] 	$Ccofre		Fuente Recibos de Caja Centro de costos.
 * @wvar String[2] 	$Ccofnc		Fuente nota Crédito Centro de costos.
 * @wvar Integer	$numAnt 	Número del cuadre anterior. * 
 * @wvar Integer	$fechaAnt	Fecha del cuadre Anterior.
 * @wvar Integer	$numCua		Número del cuadre acttual.
 * @wvar Array		$valorAnt	Toda la información acerca de los valores positivos (recibos de caja) que estan perndientes de egresar, es decir, 
 * 								que pertenecen a ningún cuadre anterior.<br>
 *								[0]:Vennum Número de la Venta.<br>
 *								[1]:Venfec Fecha de la venta.<br>
 *								[2]:Rdefac Factura de la venta.<br>
 *								[3]:Rdefue Fuente del recibo.<br>
 *								[4]:Rdenum Número del Recibo.<br>
 *								[5]:Código de quie realizo la venta.<br>
 *								[6]:Cdefpa Código de la Forma de pago.<br>
 *								[7]:Fpades Descripcion de la Forma de pago.<br>
 *								[8]:Rfpvfp Valor de la forma de pago en el recibo.<br>
 *								[9]:Cdevrf Valor pendiente por egresar.<br>
 *								[10]:Valor a Egresar en el cuadre que se esta realizando.<br>
 *								[11]:Booleano que determina si la forma de pago va a ser egresada en el cuadre o no.
 * @wvar Array		$valorAntNC	Toda la información acerca de las notas crédito pendientes por cuadrar, es decir, 
 * 								que pertenecen a ningún cuadre anterior.<br>
 * 								[0]:Número del consecutivo de la devolución (000010.Mendoc).<br>
 *								[1]:Fecha en que se efectuo la NC.<br>
 *								[2]:Número de la venta devuelta en la NC.<br>
 *								[3]:Fuente de la NC.<br>
 *								[4]:Número o consecutivo de la NC.<br>
 *								[5]:Código de quie realizo la NC.<br>
 *								[6]:Cdefpa Código de la Forma de pago.<br>
 *								[7]:Fpades Descripcion de la Forma de pago.<br>
 *								[8]:Rfpvfp Valor de la forma de pago en el recibo.<br>
 *								[9]:Cdevrf Valor pendiente por egresar.<br>
 *								[10]:Valor a Egresar en el cuadre que se esta realizando.<br>
 *								[11]:Booleano que determina si la forma de pago va a ser egresada en el cuadre o no.
 * @wvar Array		$titulos	Vector donde se encuentran los titulos usados para los encabesados de los detalles de los recibos.
 * 								Solo hay tres elementos variables en el vector, el 0, el 2 y el 5, como se ve acontinuación:
 * 								Para saldos Anteriores:<br>
 * 								[0]:Recibos="VENTAS", NC="DEV.".<BR>
 * 								[2]:Recibos="FACTURA", NC="VENTA".<BR>
 * 								[5]:Ambos="SALDO ANTERIOR".<BR>
 * 								Para nuevos ingresos:<br>
 * 								[0]:Recibos="VENTAS", NC="DEV.".<BR>
 * 								[2]:Recibos="FACTURA", NC="VENTA".<BR>
 * 								[5]:Recibos="VALOR A CUADRAR".
 * @wvar Boolean	$cuadreVacio true:El cuadre esta vacio no se puede realizar.<br>
 * 								 false:El cuadre tiene registros, se puede realizar.
 * @wvar Array		$valor		Toda la información acerca de los valores  positivos, es decir,
 * 								recibos de caja que NO pertenecesn a ningun cuadre anterior.<br>
 *								[0]:Vennum Número de la Venta.<br>
 *								[1]:Venfec Fecha de la venta.<br>
 *								[2]:Rdefac Factura de la venta.<br>
 *								[3]:Rdefue Fuente del recibo.<br>
 *								[4]:Rdenum Número del Recibo.<br>
 *								[5]:Código de quie realizo la venta.<br>
 *								[6]:Rfpfpa Código de la Forma de pago.<br>
 *								[7]:Fpades Descripcion de la Forma de pago.<br>
 *								[8]:Rfpvfp Valor de la forma de pago en el recibo.<br>
 *								[9]:Rfpvrf Valor suceptible de ser egresado.<br>
 *								[10]:Valor a Egresar en el cuadre que se esta realizando.<br>
 *								[11]:Booleano que determina si la forma de pago va a ser egresada en el cuadre o no.
 * @wvar Array		$valortNC	Toda la información acerca de las notas crédito que NO pertenecen a ningún cuadre anterior.<br>
 * 								[0]:Número del consecutivo de la devolución (000010.Mendoc).<br>
 *								[1]:Fecha en que se efectuo la NC.<br>
 *								[2]:Número de la venta devuelta en la NC.<br>
 *								[3]:Fuente de la NC.<br>
 *								[4]:Número o consecutivo de la NC.<br>
 *								[5]:Código de quien realizo la NC.<br>
 *								[6]:Rfpfpa Código de la Forma de pago.<br>
 *								[7]:Fpades Descripcion de la Forma de pago.<br>
 *								[8]:Rfpvfp Valor de la forma de pago en el recibo.<br>
 *								[9]:Rfpvrf Valor suceptible de ser egresado.<br>
 *								[10]:Valor a Egresar en el cuadre que se esta realizando.<br>
 *								[11]:Booleano que determina si la forma de pago va a ser egresada en el cuadre o no. 
 * @wvar String[5]	$usu		Código del usuario que realiza el cuadre
 * @wvar String		$pass		Password de la persona que efectua el cuadre.
 * @wvar String		$listo		Informa si el cuadre ya fue realizado.<br>
 * 								"":Cuadre en proceso.<br>
 * 								"ok":Cudre realizado.				
 * @table	000010	SELECT
 * @table	000016	SELECT
 * @table	000021	SELECT
 * @table	000022	SELECT
 * @table	000023	SELECT
 * @table	000028	SELECT, UPDATE
 * @table	000037	SELECT, INSERT
 * 
 * @modified 2011-01-28 Se hacen temporales las tablas sql que se crean en este reporte.
 * @modified 2007-01-25 En el query de busqueda de la fecha del cuadre anterior se añade el "order by 1 ASC".
 * @modified 2006-10-04 Se corrige para que MostrarTotales determine si el cuadre esta vacio o no, lo que retorna se almacena en la variable cuadreVacio
 * @modified 2006-10-04 Si el cuadre efectivamente esta vacio no puede efectuarse
 * @modified 2005-10-23 Se cambia if(!isset($valorAntNC) and isset($salant))  por if(!isset($valorAntNC) and isset($salant) and $cajcua != 0) por
 * @modified 2005-10-23 que no estaba entrando bien en el primer cuadre que se le hacia a una caja.
 * @modified 2005-10-20 Se cambia if(!isset($valorAntNC) and isset($salant) and $salant != 0)  por if(!isset($valorAntNC) and isset($salant)) por
 * @modified 2005-10-20 que no estaba entrando bien
 * @modified 2005-10-17 Se modifica el hypervinculo que acompaña a las ventas de los hypervinculos para que se dirija a copia_factura.php.
 * @modified 2005-10-13 Se modifica para que en caso de que no halla cuadre anterior pornga fechaAnt=0000-00-00
 * @modified 2005-10-13 Se cambian las tablas de farstore a $tipo_tablas en los procesos de notas crédito
 * @modified 2005-10-13 En la  función Mostrar se añade al hypervinculo de Info la valiable $tipo_tablas que también se pone como global
 * @modified 2005-10-10 Se comentan las funciones.
 * @modified 2005-10-10 Se usa el include paleta.php para los colores.
 * @modified 2005-10-10 Se pasan los procesos del include prueba_nc al programa
 * @modified 2005-10-04 Se cambian las tablas de farstore a tipo_tablas, igual que la imagen del logo
 */


$wautor="Ana María Betancur V.";
$wactualiz="2011-01-28";

/**
 * Include que almacena la información de los colores usados para el despliegue en pantalla.
 */
include_once("paleta.php");

echo "<center><table border width='350'>";
echo "<tr><td align=center colspan='2' ><img src='/matrix/images/medical/POS/logo_".$tipo_tablas.".png' WIDTH=388 HEIGHT=70></td></tr>";
echo "<tr><td align=center colspan='2' bgcolor=".$AzulClar."><font size=3 text color=#FFFFFF><b>REPORTES DE VENTAS  </b></font></td></tr>";
echo "<tr><td align=center colspan='2' bgcolor=".$AzulClar."><font size=3 text color=#FFFFFF><b>CUADRE DE CAJA </b></font></td></tr>";
echo "<tr><td align=center colspan='2' bgcolor=".$AzulClar."><font size=3 text color=#FFFFFF><b>$wcaja</b></font></td></tr>";
echo "</table>";

$pos = strpos($user,"-");
$wusuario = substr($user,$pos+1,strlen($user));
$titulos[1]="FECHA";
$titulos[3]="FUENTE-N° RECIBO";
$titulos[4]="VALOR TOTAL";
//$titulos[5]="SALDO ANTERIOR";
$titulos[6]="VALOR EGRESAR";
$titulos[7]="NUEVO SALDO";
$titulos[8]="EGRESAR";

$totales["ANT"]["TOTAL"]=0;
$totales["ANT"]["SALDO"]=0;
$totales["ANT"]["EGRESAR"]=0;
$totales["ANT"]["NUEVO"]=0;

$totales["ANTNC"]["TOTAL"]=0;
$totales["ANTNC"]["SALDO"]=0;
$totales["ANTNC"]["EGRESAR"]=0;
$totales["ANTNC"]["NUEVO"]=0;

$totales["ING"]["TOTAL"]=0;
$totales["ING"]["SALDO"]=0;
$totales["ING"]["EGRESAR"]=0;
$totales["ING"]["NUEVO"]=0;

$totales["NC"]["TOTAL"]=0;
$totales["NC"]["SALDO"]=0;
$totales["NC"]["EGRESAR"]=0;
$totales["NC"]["NUEVO"]=0;

/**
 * Muestra en panlatalla el contenido de Valor
 * 
 * El objetivo principal de la función es desplegar el contenido de $valor en la pantalla, 
 * con sus correspondientes títulos y totales, principales y  secundarios, por cada una de las formas 
 * de pago que contenga la matriz
 * Cuando imprime en pantalla dicionalmente imprime los un hidden para cada dato de $valor y llena el arreglo $totales.
 *
 * @param Array		$valor		Matriz de 11xN que tiene todos los datos de una de de las 4 partes que contempla el cuadre de caja POS.<br>
 * 								[0]:Número de la Venta o Movimiento de inventario de la Devolucion.<br>
 *								[1]:Fecha de la transacción.<br>
 *								[2]:Factura de la venta.<br>
 *								[3]:Fuente del recibo o de la NC.<br>
 *								[4]:Número del Recibo.<br>
 *								[5]:Usuario que realizo la ventoa o la NC.<br>
 *								[6]:Código de la Forma de pago.<br>
 *								[7]:Descripcion de la Forma de pago.<br>
 *								[8]:Valor de la forma de pago en el recibo.<br>
 *								[9]:Valor suceptible de ser egresado.<br>
 *								[10]:Valor a Egresar en el cuadre que se esta realizando.<br>
 *								[11]:Booleano que determina si la forma de pago va a ser egresada en el cuadre o no.
 * @param String	$titulo 	Titulo principal.
 * @param String	$name		Nombre original del arreglo, con el que van a ser enviados los hidden.
 * @param array		$titulos	Nombres de los campos para cada forma de pago
 * @param Array		$totales	Valores consolidados para cada una de las 4 partes que contempla el cuadre de caja POS.
 */
function Mostrar($valor, $titulo, $name, $titulos, $totales) {
	
	global $tipo_tablas;
	global $wcco;
	global $AzulClar ;
	global $AzulText;
	global $blanco;
	global $AmarClar;
	global $AmarQuem;
	global $AguaClar;
	global $AguaOsc;
	global $AmarQuem;

	echo "<table border='1' width='650'>";
	echo "<tr><td colspan='10' align='center' bgcolor=".$AzulClar."><b><font text color=".$blanco.">".$titulo."</font></b></TR>";
	$fp="";
	$total=0;
	$anterior=0;
	$egresar=0;
	$nuevo=0;
	$totTotal=0;
	$totAnterior=0;
	$totEgresar=0;
	$totNuevo=0;
	 
	$num = count($valor);
	//	ECHO "<BR><BR>ADENTRO DE MOSTRAR num=$num <BR>";
	for($i=0;$i<$num;$i++) {
		if($valor[$i][6] != $fp){
			$fp= $valor[$i][6];
			if($i != 0){
				//IMPRIMIRLOS RESULTADOS TOTALES
				echo "<tr><td align=left bgcolor=".$AmarClar." colspan='5'><b><font text color=".$AzulText.">TOTAL </font></b>";
				echo "<td align='right' bgcolor=".$AmarClar."><b><font color=".$AzulText.">".number_format($total,",","",".")."</td>";
				echo "<td align='right' bgcolor=".$AmarClar."><b><font color=".$AzulText.">".number_format($anterior,",","",".")."</td>";
				echo "<td align='right' bgcolor=".$AmarClar."><b><font color=".$AzulText.">".number_format($egresar,",","",".")."</td>";
				echo "<td align='right' bgcolor=".$AmarClar."><b><font color=".$AzulText.">".number_format($nuevo,",","",".")."</td>";
				echo "<td align='right' bgcolor=".$AmarClar."><b><font color=".$AzulText."></td>";
				$totTotal = $totTotal + $total;
				$totAnterior = $totAnterior + $anterior;
				$totEgresar = $totEgresar + $egresar;
				$totNuevo = $totNuevo + $nuevo;
				$total=0;
				$anterior=0;
				$egresar=0;
				$nuevo=0;
			}
			echo "<tr><td colspan='10' align='center' bgcolor=".$AguaOsc."><b><font text color=".$AzulText." >".$fp."-".$valor[$i][7]."</font></b></TR>";
			echo "<tr><td align=left bgcolor=".$AguaClar."><b><font text color=".$AzulText.">";
			echo "<td align=left bgcolor=".$AguaClar."><b><font text size='2' color=".$AzulText.">".$titulos[0]."</td>";
			echo "<td align=left bgcolor=".$AguaClar."><b><font text size='2' color=".$AzulText.">".$titulos[1]."</td>";
			echo "<td align=left bgcolor=".$AguaClar."><b><font text size='2' color=".$AzulText.">".$titulos[2]."</td>";
			echo "<td align=left bgcolor=".$AguaClar."><b><font text size='2' color=".$AzulText.">".$titulos[3]."</td>";
			echo "<td align=left bgcolor=".$AguaClar."><b><font text size='2' color=".$AzulText.">".$titulos[4]."</td>";
			echo "<td align=left bgcolor=".$AguaClar."><b><font text size='2' color=".$AzulText.">".$titulos[5]."</td>";
			echo "<td align=left bgcolor=".$AguaClar."><b><font text size='2' color=".$AzulText.">".$titulos[6]."</td>";
			echo "<td align=left bgcolor=".$AguaClar."><b><font text size='2' color=".$AzulText.">".$titulos[7]."</td>";
			echo "<td align=left bgcolor=".$AguaClar."><b><font text size='2' color=".$AzulText.">".$titulos[8]."</td></tr>";
		}

		$total = $total + $valor[$i][8];
		$anterior= $anterior + $valor[$i][9];

		/*Link a detalle recibo donde se detalla la venta*/
		if($name == 'valorNC' or $name=='valorAntNC') {
		echo "<tr><td align=left bgcolor=".$blanco."><font color=".$AzulText."><A Href='/matrix/POS/procesos/bono_dev.php?doc=".$valor[$i][0]."&amp;wcco=".$wcco."&amp;tipo_tablas=".$tipo_tablas."&amp;devtip=NOTA CRÉDITO' target='blank'>Info</a>";
	}else{
		echo "<tr><td align=left bgcolor=".$blanco."><font color=".$AzulText.">";//-<A HREF='detalle_recibo.php?Vennum=".$valor[$i][0]."&amp;Fenfec=".$valor[$i][1]."&amp;Rdefac=".$valor[$i][2]."&amp;Rdefue=".$valor[$i][3]."&amp;Rdenum=".$valor[$i][4]."&amp;Seguridad=".$valor[$i][5]."]&amp;Cdefpa=".$valor[$i][6]."&amp;Fpades=".$valor[$i][7]."&amp;Rfpvfp=".$valor[$i][8]."&amp;Cdevrf=".$valor[$i][9]."&amp;Cdevrf=".$valor[$i][10]."&amp;tipo_tablas=".$tipo_tablas."' target='blank'>Info</a>"; //Cambio 2005-10-13
		ECHO "<A HREF='/matrix/POS/procesos/copia_factura.php?wnrovta=".$valor[$i][0]."&amp;wfecini=".$valor[$i][1]."&amp;wfecfin=".$valor[$i][1]."&amp;wnrofac=".$valor[$i][2]."&amp;wfuefac=".$valor[$i][3]."&amp;Rdenum=".$valor[$i][4]."&amp;Seguridad=".$valor[$i][5]."]&amp;Cdefpa=".$valor[$i][6]."&amp;Fpades=".$valor[$i][7]."&amp;Rfpvfp=".$valor[$i][8]."&amp;Cdevrf=".$valor[$i][9]."&amp;Cdevrf=".$valor[$i][10]."&amp;wbasedato=".$tipo_tablas."' target='blank'>Info</a>"; //Cambio 2005-10-13
	}
		echo "<td align=left bgcolor=".$blanco."><font text color=".$AzulClar.">".$valor[$i][0]."</tr>";//# de Venta
		echo "<td align=left bgcolor=".$blanco."><font text color=".$AzulClar.">".$valor[$i][1]."</tr>";//Fecha Venta
		echo "<td align=left bgcolor=".$blanco."><font text color=".$AzulClar.">".$valor[$i][2]."</tr>";//# FActura
		echo "<td align=left bgcolor=".$blanco."><font text color=".$AzulClar.">".$valor[$i][3]."-".$valor[$i][4]."</tr>";//#de fuente y Recibo
		echo "<td align=right bgcolor=".$blanco."><font text color=".$AzulClar.">".number_format($valor[$i][8],",","",".")."</tr>";//Valor total de la Forma de Pacgo
		echo "<td align=right bgcolor=".$blanco."><font text color=".$AzulClar.">".number_format($valor[$i][9],",","",".")."</tr>";//Saldo del Cuadre de caja Anterior

		/*Saldo a Egresar
		Si es del tipo 99=> EFECTIVO Debe permitit digitar la cantidad que desea egresar
		En caso contrario la cantida no es digitada y corresponde al total del saldo Anterior
		*/
		if($valor[$i][6] != '99'){
			echo "<td align='right' bgcolor=".$blanco."><font text color=".$AzulClar.">".$valor[$i][10]."</tr>";
			echo "<input type='hidden' name='".$name."[".$i."][10]' value='".$valor[$i][10]."'>";
		}else{
			echo "<td align='center' bgcolor=".$blanco."><font text color=".$AzulClar."><input type='text' name='".$name."[".$i."][10]' value='".$valor[$i][10]."' size='4'></tr>";
		}

		if(isset($valor[$i][11])) {
			/*Esta Checkeado para egresar*/

			echo "<td align='right' bgcolor=".$blanco."><font text color=".$AzulClar.">".number_format(($valor[$i][9]-$valor[$i][10]),",","",".")."</tr>";
			echo "<td align='center' bgcolor=".$blanco."><font text color=".$AzulClar."><input type='CHECKBOX' name='".$name."[".$i."][11]' value='on' checked></tr>";
			$nuevo = $nuevo + ($valor[$i][9]-$valor[$i][10]); //Saldo anterior menos valor a egresar
			$egresar = $egresar + $valor[$i][10];
		}else{
			echo "<td align='right' bgcolor=".$blanco."><font text color=".$AzulClar.">".number_format($valor[$i][9],",","",".")."</tr>";
			echo "<td align='center' bgcolor=".$blanco."><font text color=".$AzulClar."><input type='CHECKBOX' name='".$name."[".$i."][11]' value='off'></tr>";
			$nuevo = $nuevo + $valor[$i][9];
		}

		echo "<input type='hidden' name='".$name."[".$i."][0]' value='".$valor[$i][0]."'>";
		echo "<input type='hidden' name='".$name."[".$i."][1]' value='".$valor[$i][1]."'>";
		echo "<input type='hidden' name='".$name."[".$i."][2]' value='".$valor[$i][2]."'>";
		echo "<input type='hidden' name='".$name."[".$i."][3]' value='".$valor[$i][3]."'>";
		echo "<input type='hidden' name='".$name."[".$i."][4]' value='".$valor[$i][4]."'>";
		echo "<input type='hidden' name='".$name."[".$i."][5]' value='".$valor[$i][5]."'>";
		echo "<input type='hidden' name='".$name."[".$i."][6]' value='".$valor[$i][6]."'>";
		echo "<input type='hidden' name='".$name."[".$i."][7]' value='".$valor[$i][7]."'>";
		echo "<input type='hidden' name='".$name."[".$i."][8]' value='".$valor[$i][8]."'>";
		echo "<input type='hidden' name='".$name."[".$i."][9]' value='".$valor[$i][9]."'>";
		//		echo "<input type='hidden' name='".$name."[".$i."][12]' value='".$valor[$i][12]."'>";
	}
	echo "<tr><td align=left bgcolor=".$AmarClar." colspan='5'><b><font text color=".$AzulText.">TOTAL </font></b>";
	echo "<td align='right' bgcolor=".$AmarClar."><b><font text color=".$AzulText.">".number_format($total,",","",".")."</td>";
	echo "<td align='right' bgcolor=".$AmarClar."><b><font text color=".$AzulText.">".number_format($anterior,",","",".")."</td>";
	echo "<td align='right' bgcolor=".$AmarClar."><b><font text color=".$AzulText.">".number_format($egresar,",","",".")."</td>";
	echo "<td align='right' bgcolor=".$AmarClar."><b><font text color=".$AzulText.">".number_format($nuevo,",","",".")."</td>";
	echo "<td align='right' bgcolor=".$AmarClar."><b><font text color=".$AzulText."></td>";
	$totTotal = $totTotal + $total;
	$totAnterior = $totAnterior + $anterior;
	$totEgresar = $totEgresar + $egresar;
	$totNuevo = $totNuevo + $nuevo;

	echo "<tr><td align=left bgcolor=".$AmarQuem." colspan='5'><b><font text color=".$AzulText.">TOTAL $titulo</font></b>";
	echo "<td align='right' bgcolor=".$AmarQuem."><b><font text color=".$AzulText.">".number_format($totTotal,",","",".")."</td>";
	echo "<td align='right' bgcolor=".$AmarQuem."><b><font text color=".$AzulText.">".number_format($totAnterior,",","",".")."</td>";
	echo "<td align='right' bgcolor=".$AmarQuem."><b><font text color=".$AzulText.">".number_format($totEgresar,",","",".")."</td>";
	echo "<td align='right' bgcolor=".$AmarQuem."><b><font text color=".$AzulText.">".number_format($totNuevo,",","",".")."</td>";
	echo "<td align='right' bgcolor=".$AmarQuem."><b><font text color=".$AzulText."></td>";
	echo "</table>";

	if($name == "valorAnt") {
		$totales["ANT"]["TOTAL"]=$totTotal;
		$totales["ANT"]["SALDO"]=$totAnterior;
		$totales["ANT"]["EGRESAR"]=$totEgresar;
		$totales["ANT"]["NUEVO"]=$totNuevo;
	} else if($name == "valorAntNC") {
		$totales["ANTNC"]["TOTAL"]=$totTotal;
		$totales["ANTNC"]["SALDO"]=$totAnterior;
		$totales["ANTNC"]["EGRESAR"]=$totEgresar;
		$totales["ANTNC"]["NUEVO"]=$totNuevo;
	} else if($name == "valor") {
		$totales["ING"]["TOTAL"]=$totTotal;
		$totales["ING"]["SALDO"]=$totAnterior;
		$totales["ING"]["EGRESAR"]=$totEgresar;
		$totales["ING"]["NUEVO"]=$totNuevo;
	} else if($name == "valorNC") {
		$totales["NC"]["TOTAL"]=$totTotal;
		$totales["NC"]["SALDO"]=$totAnterior;
		$totales["NC"]["EGRESAR"]=$totEgresar;
		$totales["NC"]["NUEVO"]=$totNuevo;
	}


}

/**
 * Despliega en pantalla los totales 
 *
 * @param 	Integer	$num	Determina el tipo de saldos a mostrar.<br>
 * 							0:Totales de Saldos Anteriores.<br>
 * 							1:Totales de los ingresos.<br>
 * 							2:Total global.
 * @return	Boolean	true si el cuadre es vacio, false en otro caso
 */
function MostrarTotales($num) {
	global $totales;


	global $AzulClar ;
	global $AzulText;
	global $blanco;
	global $AmarClar;
	global $AguaClar;
	global $AzulClar ;
	global $AzulText;

	$blanco="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
	$AmarClar="#FFDBA8";	//COLOR DEL FONDO 3 PARA RESALTAR -- Amarillo quemado claro
	$AguaClar="#A4E1E8";	//COLOR DEL FONDO 4 -- Aguamarina claro

	$tot[0]=0;
	$tot[1]=0;
	$tot[2]=0;
	$tot[3]=0;

	if($num == 0 or $num == 2){

		$tot[0]=$totales["ANT"]["TOTAL"]-$totales["ANTNC"]["TOTAL"];
		$tot[1]=$totales["ANT"]["SALDO"]-$totales["ANTNC"]["SALDO"];
		$tot[2]=$totales["ANT"]["EGRESAR"]-$totales["ANTNC"]["EGRESAR"];
		$tot[3]=$totales["ANT"]["NUEVO"]-$totales["ANTNC"]["NUEVO"];

		$titulo="ACUMULADO SALDOS ANTERIORES";
	}
	if($num == 1 or $num == 2) {

		$tot[0]=$tot[0]+$totales["ING"]["TOTAL"]-$totales["NC"]["TOTAL"];
		$tot[1]=$tot[1]+$totales["ING"]["SALDO"]-$totales["NC"]["SALDO"];
		$tot[2]=$tot[2]+$totales["ING"]["EGRESAR"]-$totales["NC"]["EGRESAR"];
		$tot[3]=$tot[3]+$totales["ING"]["NUEVO"]-$totales["NC"]["NUEVO"];
		$titulo="ACUMULADO NUEVOS INGRESOS";
	}
	if($num == 2){
		$titulo="ACUMULADO TOTAL ";
	}
	echo "<table border='1' width='650'>";
	echo "<tr><td colspan='10' align='center' bgcolor=".$AzulClar."><b><font text color=".$blanco.">".$titulo."</font></b></TR>";

	echo "<tr><td align=left bgcolor=".$AguaClar."><b><font text color=".$AzulText.">VALOR TOTAL RECIBO</td>";
	echo "<td align=left bgcolor=".$AguaClar."><b><font text color=".$AzulText.">VALOR SALDO A CUADRAR</td>";
	echo "<td align=left bgcolor=".$AguaClar."><b><font text color=".$AzulText.">VALOR A EGRESAR</td>";
	echo "<td align=left bgcolor=".$AguaClar."><b><font text color=".$AzulText.">VALOR NUEVO SALDO</td></tr>";

	echo "<tr><td align='right' bgcolor=".$AmarClar."><b><font text color=".$AzulText.">$ ".number_format($tot[0],0,"",",")."</td>";
	echo "<td align='right' bgcolor=".$AmarClar."><b><font text color=".$AzulText.">$ ".number_format($tot[1],0,"",",")."</td>";
	echo "<td align='right' bgcolor=".$AmarClar."><b><font text color=".$AzulText.">$ ".number_format($tot[2],0,"",",")."</td>";
	echo "<td align='right' bgcolor=".$AmarClar."><b><font text color=".$AzulText.">$ ".number_format($tot[3],0,"",",")."</td></tr>";

	echo "</table><br><br>";
	if($tot[1] == 0){
		return (true);
	}else {
		return (false);
	}
}

/**
 * Graba el cuadre en la Base de datos
 * 
 * Graba en la base de datos la información correspondiente al cuadre, mas especificamente
 * en la tabla 000037 hace un registro por cada uno de los vectores de la Matriz 11xN $valor.
 * 
 * @return Int		$numCua		Número del cuadre realizado
 * @param Array[][]	$valor		Aqui esta toda la información de los recibos del cuadre.
 *   							[0]:Número de la Venta o Movimiento de inventario de la Devolucion.<br>
 *								[1]:Fecha de la transacción.<br>
 *								[2]:Factura de la venta.<br>
 *								[3]:Fuente del recibo o de la NC.<br>
 *								[4]:Número del Recibo.<br>
 *								[5]:Usuario que realizo la ventoa o la NC.<br>
 *								[6]:Código de la Forma de pago.<br>
 *								[7]:Descripcion de la Forma de pago.<br>
 *								[8]:Valor de la forma de pago en el recibo.<br>
 *								[9]:Valor suceptible de ser egresado.<br>
 *								[10]:Valor a Egresar en el cuadre que se esta realizando.<br>
 *								[11]:Booleano que determina si la forma de pago va a ser egresada en el cuadre o no.
 * @param String	$titulo 	Titulo principal.
 * @param Int		$conex		Id de conexión a la Base de Datos
 * @param String	$wcaja		Codigo de la caja
 * @param Strins	$wcco		Código del Centro de costos
 * @param String	$listo		Indicador de que se realizo el cudre
 * @param String	$usu		Usuario que realiza el cuadre
 * @param Int	$numCua		Número del cuadre
 */
function grabar($valor,$conex,$wcaja,$wcco,$listo,$usu,$numCua) {

	global $tipo_tablas;

	if($numCua == "") {
		$q = "LOCK table ".$tipo_tablas."_000028 LOW_PRIORITY WRITE";
		$err = mysql_query($q,$conex);

		/*Traer el numero de cuadre */
		$q="SELECT Cajcua "
		."FROM ".$tipo_tablas."_000028 "
		."WHERE 	Cajcco = '".$wcco."' "
		."and 		Cajcod = '".$wcaja."'";
		$res = mysql_query($q,$conex);
		$row=mysql_fetch_array($res);
		$numCua= $row["Cajcua"]+1;

		/*Aumentar el numero de cuadre*/
		$q="UPDATE ".$tipo_tablas."_000028 "
		."SET Cajcua='".$numCua."' "
		."WHERE 	Cajcco = '".$wcco."' "
		."and 		Cajcod = '".$wcaja."'";
		$res = mysql_query($q,$conex);

		$q = " UNLOCK TABLES";
		$err = mysql_query($q,$conex);
	}
	$num = count($valor);
	$date = date("Y-m-d");
	$hour = date("H:m:i");
	for($i=0;$i < $num;$i++) {
		if(isset($valor[$i][11])){
			$vrf= $valor[$i][9] - $valor[$i][10];
		}else{
			$vrf=$valor[$i][9];
		}

		$q="INSERT INTO ".$tipo_tablas."_000037"
		."(medico, Fecha_data, Hora_data, Cdecua, Cdecaj, Cdecco, Cdefue, Cdenum, Cdefpa, Cdevrf, Cdeest, Seguridad)"
		."VALUES ('".$tipo_tablas."', '$date','$hour','$numCua', '$wcaja', '$wcco', '".$valor[$i][3]."', '".$valor[$i][4]."', '".$valor[$i][6]."', '$vrf',  'on', 'A-$usu')";
		$res = mysql_query($q,$conex);
	}
	$listo="ok";
	return($numCua);
}

session_start();

if (!isset($user)) {
	if(!isset($_SESSION['user']))
	session_register("user");
}

if(!isset($_SESSION['user']))
echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	


	echo '<form action="" method="POST">';

	echo "<input type='HIDDEN' name= 'wcco' value='".$wcco."'>";
	echo "<input type='HIDDEN' name= 'cajcua' value='".$cajcua."'>";
	echo "<input type='hidden' name='wcaja' value='".$wcaja."'>";
	echo "<input type='HIDDEN' name= 'Ccofre' value='".$Ccofre."'>";
	echo "<input type='HIDDEN' name= 'Ccofnc' value='".$Ccofnc."'>";

	$pos=explode("-",$wcaja);
	$wcaja = $pos[0];
	$wnomcaj = $pos[1];

	$checked="";

	if(isset($grabar) ){
		
		$checked="checked";
		/*Ingresar contraseña*/
		if(!isset($pass)){
			$pass="";
		}else{
			/*Confirmar contraseña*/
			$q="SELECT Cjeusu "
			."FROM ".$tipo_tablas."_000030 "
			."WHERE Cjecla='$pass' ";
			$err = mysql_query($q,$conex);
			$num = mysql_num_rows($err);
			if ($num > 0)
			{
				$numCua="";
				$row=mysql_fetch_row($err);
				$usu=$row[0];
				if(isset($valor)){
					$numCua=grabar(&$valor,$conex,$wcaja,$wcco,&$listo,$usu,$numCua);
				}
				if(isset($valorAnt)){
					$numCua=grabar(&$valorAnt,$conex,$wcaja,$wcco,&$listo,$usu,$numCua);
				}
				if(isset($valorAntNC)){
					$numCua=grabar(&$valorAntNC,$conex,$wcaja,$wcco,&$listo,$usu,$numCua);
				}
				if(isset($valorNC)){
					$numCua=grabar(&$valorNC,$conex,$wcaja,$wcco,&$listo,$usu,$numCua);
				}
			}
		}
	}
	
	if(!isset($valorAnt) and $cajcua != 0) {
		//	Venfec 		: Fecha de la Venta 	-- ".$tipo_tablas."_000016 ENCAVEZADO VENTAS relaciona Venfac con Fenfac (000018)
		//	Fenfec 		: Fecha de la factura	-- ".$tipo_tablas."_000018 ENCABEZADO FACTURA relaciona Fenfac con Rdefac (000021)
		//	Rdefac 		: Numero de la factura	-- ".$tipo_tablas."_000021 DETALLE RECIBO
		//	Rdefue 		: Fuente del recibo		-- ".$tipo_tablas."_000021 DETALLE RECIBO se relaciona con Rfpfue (".$tipo_tablas."_000022)
		//	Rdenum 		: Fuente del recibo		-- ".$tipo_tablas."_000021 DETALLE RECIBO se relaciona con Rfpnum (".$tipo_tablas."_000022)
		//	Seguridad	: Usuario ing. recibo	-- ".$tipo_tablas."_000021 DETALLE RECIBO
		//	Cdefpa		: Código forma pago		-- ".$tipo_tablas."_000037 DETALLE CUADRE CAJA relaciona Cdefue,Cdenum,Cdefpa con
		//																				Rfpfue, Rfpnum, Rfpfpa (".$tipo_tablas."_000022)
		//	Fpades		: Descrip. forma pago	-- ".$tipo_tablas."_000023 FORMAS DE PAGO relaciona Fpacod con Cdefpa (".$tipo_tablas."_000037)
		//	Rfpvfp		: Valor forma de pago	-- ".$tipo_tablas."_000022 DETALLE RECIBO FORMA PAGO
		//	Cdevrf		: Saldo recibo en caja	-- ".$tipo_tablas."_000037 DETALLE CUADRE CAJA
		if(!isset($numCua)) {
			$q="SELECT MAX(Cdecua) "
			."FROM ".$tipo_tablas."_000037 "
			."WHERE ".$tipo_tablas."_000037.Cdeest = 'on' "
			."and 	".$tipo_tablas."_000037.Cdecaj = '".$wcaja."' "
			."and 	".$tipo_tablas."_000037.Cdecco = '".$wcco."' ";
			$err = mysql_query($q,$conex);
			/*		echo $q."<br><br>";
			ECHO "<br><br>315 : <b>".mysql_errno()."=".mysql_error()."</b>";*/
			$num = mysql_num_rows($err);
			if ($num > 0)	{
				$row=mysql_fetch_array ($err);
				$numCua=$row[0]+1;
				$numAnt=$row[0];
			}else{
				$numCua=1;
				$numAnt=0;
			}
		}else{
			$numAnt=$numCua-1;
		}

		
		
		$q="SELECT DISTINCT(Fecha_data)  "
		."FROM	".$tipo_tablas."_000037 "
		."WHERE	Cdeest = 'on' "
		."and 	Cdecaj = '".$wcaja."' "
		."and 	Cdecco = '".$wcco."' "
		."and	Cdecua	= '".$numAnt."' "
		."order by 1 ASC";		
		$res = mysql_query($q,$conex);
		$num = mysql_num_rows($res);
		if ($num > 0) {
			$row=mysql_fetch_array($res);
			$fechaAnt=$row[0];
		}else{
			$fechaAnt='0000-00-00';
		}

		/********************************************************
		*						SALDOS ANTERIORES				*
		*********************************************************/

		/*Saldos de ventas anteirores*/
		
		
		$salant=date("Mdis");
		$q= "CREATE TEMPORARY TABLE IF NOT EXISTS ".$salant." as "
		."SELECT * "
		."FROM	".$tipo_tablas."_000037 "
		."WHERE	Cdeest = 'on' "
		."and	Cdecua	= '".$numAnt."' "
		."and 	Cdecaj = '".$wcaja."' "
		."and 	Cdecco = '".$wcco."' "
		//	."and	Cdefue	= '".$Ccofre."' "
		."and	Cdevrf	<>	0 ";
		$res1 = mysql_query($q,$conex);
		
		$q=	"SELECT Cdevrf, Cdefpa, Fpades, SUM(Rfpvfp) AS Rfpvfp, Rdefue, Rdenum, Rdefac, ".$tipo_tablas."_000022.Seguridad AS Seguridad, Vennum, Venfec 		, $salant.id as id "
		."FROM $salant, ".$tipo_tablas."_000023, ".$tipo_tablas."_000022, ".$tipo_tablas."_000021, ".$tipo_tablas."_000016 "
		."WHERE	Fpacod	=	Cdefpa "
		."and	Cdefue	= '".$Ccofre."' "//2005-10-06
		."and	Rfpest	=	'on' "
		."and	Rfpfue	=	Cdefue "
		."and	Rfpnum	=	Cdenum "
		."and	Rfpcco = '".$wcco."' "
		."and	Rfpfpa	=	Cdefpa "
		."and	Rdeest	=	'on' "
		."and	Rdefue	=	Cdefue "
		."and	Rdenum	=	Cdenum "
		."and	Rdecco = '".$wcco."' "
		."and	Venest	=	'on' "
		."and	Vencco = '".$wcco."' "
		."and	Vennum	=	Rdevta "
		."GROUP BY Rfpfpa, Rfpnum, Rfpfue "
		."	ORDER BY Cdefpa,Vennum ";
		$res = mysql_query($q,$conex);
		$num = mysql_num_rows($res);
		if ($num > 0)
		{
			for($i=0;$i<$num;$i++) {
				$row=mysql_fetch_array ($res);
				$valorAnt[$i][0]=$row["Vennum"];
				$valorAnt[$i][1]=$row["Venfec"];
				$valorAnt[$i][2]=$row["Rdefac"];
				$valorAnt[$i][3]=$row["Rdefue"];
				$valorAnt[$i][4]=$row["Rdenum"];
				$valorAnt[$i][5]=$row["Seguridad"];
				$valorAnt[$i][6]=$row["Cdefpa"];
				$valorAnt[$i][7]=$row["Fpades"];
				$valorAnt[$i][8]=$row["Rfpvfp"];
				$valorAnt[$i][9]=$row["Cdevrf"];
				$valorAnt[$i][10]=$row["Cdevrf"]; 	//Valor a Egresar
				$valorAnt[$i][11]="on";			//Si se va a egresar o no
				/*			//$valor[$i][12]=$row["id"]; 	//Valor a Egresar
				echo "<br><br><b>[$i] 0=".$valorAnt[$i][0]."</b>";
				echo "<br>[$i] 1=".$valorAnt[$i][1];
				echo "<br>[$i] 2=".$valorAnt[$i][2];
				echo "<br>[$i] 3=".$valorAnt[$i][3];
				echo "<br>[$i] 4=".$valorAnt[$i][4];
				echo "<br>[$i] 5=".$valorAnt[$i][5];
				echo "<br>[$i] 6=".$valorAnt[$i][6];
				echo "<br>[$i] 7=".$valorAnt[$i][7];
				echo "<br>[$i] 8=".$valorAnt[$i][8];
				echo "<br>[$i] 9=".$valorAnt[$i][9];
				echo "<br>[$i] 10=".$valorAnt[$i][10];
				echo "<br>[$i] 11=".$valorAnt[$i][11];	*/

			}
		}

	}
	elseif ($cajcua == 0) { //CAmbio 2005-10-13
		$fechaAnt='0000-00-00';
	}
	if(isset($valorAnt)){
		$titulos[0]="VENTAS";
		$titulos[2]="FACTURA";
		$titulos[5]="SALDO ANTERIOR";

		echo "<br><br>";
		Mostrar(&$valorAnt,"SALDOS PENDIENTES","valorAnt",&$titulos,&$totales);
	}
	
	if(!isset($valorAntNC) and isset($salant) and $cajcua !=0 ) {
			
		$table1="NCA1_".date("Mdis");
		$q= "CREATE TEMPORARY TABLE IF NOT EXISTS ".$table1." as "
		."SELECT Cdecua, Cdecaj, Cdecco, Cdefue, Cdenum, Cdefpa, Cdevrf, Rdefac, Rdevta "
		."FROM	$salant, ".$tipo_tablas."_000021 "
		."WHERE	Cdefue = '".$Ccofnc."' "
		."and	Rdefue =  '".$Ccofnc."' "
		."and 	Rdecco = '".$wcco."' "
		."and	Rdenum = Cdenum "
		."and	Rdeest = 'on' ";
		$res1 = mysql_query($q,$conex);
	
		$table="NCA_".date("Mdis");
		$q= "CREATE TEMPORARY TABLE IF NOT EXISTS ".$table." as "
		."SELECT Cdecua, Cdecaj, Cdecco, Cdefue, Cdenum, Cdefpa, Cdevrf, Rdefac, Mendoc as Devnum, Menfac as Vennum, Menusu as Usuario,  Fecha_data as Fecha "
		."FROM	$table1, ".$tipo_tablas."_000010 "
		."WHERE	Mencon = '801' "
		."and 	Mencco = '".$wcco."' "
		."and	Mendoc = Rdevta "
		."and	Menest = 'on' ";
		$res1 = mysql_query($q,$conex);
	
		$q=	"SELECT Cdecua, Cdecaj, Cdecco, Cdefue, Cdenum, Cdefpa, Cdevrf, Devnum, Vennum, Usuario, Fecha, Rdefac, Fpades, SUM(Rfpvfp) AS Rfpvfp "
		."FROM $table, ".$tipo_tablas."_000023, ".$tipo_tablas."_000022  "
		."WHERE	Fpacod	=	Cdefpa "
		."and	Rfpest	=	'on' "
		."and	Rfpcco =	Cdecco "
		."and	Rfpfue	=	Cdefue "
		."and	Rfpnum	=	Cdenum "
		."and	Rfpfpa	=	Cdefpa "
		."GROUP BY  Rfpfpa, Rfpnum, Rfpfue "
		."ORDER BY Cdefpa,Vennum ";
		$err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);
		if ($num > 0)
		{
			for($i=0;$i<$num;$i++) {
				$row=mysql_fetch_array ($err);
				$valorAntNC[$i][0]=$row["Devnum"];
				$valorAntNC[$i][1]=$row["Fecha"];
				$valorAntNC[$i][2]=$row["Vennum"];
				$valorAntNC[$i][3]=$row["Cdefue"];
				$valorAntNC[$i][4]=$row["Cdenum"];
				$valorAntNC[$i][5]=$row["Usuario"];
				$valorAntNC[$i][6]=$row["Cdefpa"];
				$valorAntNC[$i][7]=$row["Fpades"];
				$valorAntNC[$i][8]=$row["Rfpvfp"];
				$valorAntNC[$i][9]=$row["Cdevrf"];
				$valorAntNC[$i][10]=$row["Cdevrf"]; 	//Valor a Egresar
				$valorAntNC[$i][11]="on";			//Si se va a egresar o no
			}
		}
		$query = "DROP table ".$table1;
		$err = mysql_query($query,$conex);
		$query = "DROP table ".$table;
		$err = mysql_query($query,$conex);

	}

	if(isset($valorAntNC)){
		$titulos[0]="DEV.";
		$titulos[2]="VENTA";
		$titulos[5]="SALDO ANTERIOR";
		echo "<br><br>";
		Mostrar(&$valorAntNC, "SALDO ANTERIOR DE NOTAS CRÉDITO ", "valorAntNC", &$titulos, &$totales);
	}
	echo "<br><br>";
	$cuadreVacio= MostrarTotales(0);

	if(isset($salant)){
		$query = "DROP table ".$salant;
		$err = mysql_query($query,$conex);
	}

	/********************************************************
	*					INGRESOS: VENTAS					*
	*********************************************************/

	$cuadre="cuadre".date("Mdis");
	$q= "CREATE TEMPORARY TABLE IF NOT EXISTS ".$cuadre." as "
	."SELECT * "
	."FROM ".$tipo_tablas."_000037 "
	."WHERE Fecha_data >= '$fechaAnt' "
	."and 	Cdecco = '".$wcco."' "
	."and 	Cdecaj = '".$wcaja."' "
	."and	Cdeest = 'on' ";
	$res1 = mysql_query($q,$conex);
	
	if (!isset($valor)) {
		$table=date("Mdis");
		$q= "CREATE TEMPORARY TABLE IF NOT EXISTS ".$table." as "
		."SELECT * "
		."FROM ".$tipo_tablas."_000022 "
		."WHERE	Rfpfue	= '".$Ccofre."' "
		."and	".$tipo_tablas."_000022.Fecha_data >= '".$fechaAnt."' "
		."and	Rfpcco	= '".$wcco."' "
		."and	NOT EXISTS (SELECT	Cdefue, Cdenum, Cdefpa "
		."					FROM	".$cuadre." "
		."					WHERE 	".$cuadre.".Cdefue = ".$tipo_tablas."_000022.Rfpfue "
		."					and		".$cuadre.".Cdenum = ".$tipo_tablas."_000022.Rfpnum "
		."					and		".$cuadre.".Cdefpa = ".$tipo_tablas."_000022.Rfpfpa )"
		."and	Rfpest = 'on' ";
		$res1 = mysql_query($q,$conex);
		
			
		
		$ini=explode("-",$wcaja);
		$cajcod=$ini[0];

		$q="SELECT Rfpfpa, SUM(Rfpvfp) AS Rfpvfp, Fpades, Rdefue, Rdenum, Rdefac, $table.Seguridad AS Seguridad, Venfec, Vennum, $table.id as id "
		."FROM $table, ".$tipo_tablas."_000023, ".$tipo_tablas."_000021, ".$tipo_tablas."_000016 "
		."WHERE	Fpacod	=	Rfpfpa "
		."and	Rdeest	=	'on' "
		."and	Rdecco	=	Rfpcco "
		."and	Rdefue	=	Rfpfue "
		."and	Rdenum	=	Rfpnum "
		."and	Venest	=	'on' "
		."and	Vencco = '".$wcco."' "
		."and	Vennum	=	Rdevta "
		."and	Vencaj	=	'".$cajcod."' "
		."GROUP BY Rfpfpa, Rfpnum, Rfpfue "
		."order by Rfpfpa, Fpades, Vennum ";

		$res = mysql_query($q,$conex);
		$num = mysql_num_rows($res);

		if ($num > 0)
		{
			for($i=0;$i<$num;$i++) {
				$row=mysql_fetch_array ($res);
				$valor[$i][0]=$row["Vennum"];
				$valor[$i][1]=$row["Venfec"];
				$valor[$i][2]=$row["Rdefac"];
				$valor[$i][3]=$row["Rdefue"];
				$valor[$i][4]=$row["Rdenum"];
				$valor[$i][5]=$row["Seguridad"];
				$valor[$i][6]=$row["Rfpfpa"];
				$valor[$i][7]=$row["Fpades"];
				$valor[$i][8]=$row["Rfpvfp"];
				$valor[$i][9]=$row["Rfpvfp"];
				$valor[$i][10]=$row["Rfpvfp"]; 	//Valor a Egresar
				$valor[$i][11]="on";			//Si se va a egresar o no
				$valor[$i][12]=$row["id"]; 	//Valor a Egresar
			}
		}
		$query = "DROP table ".$table;
		$err = mysql_query($query,$conex);
	}


	if(isset($valor)){
		$titulos[0]="VENTAS";
		$titulos[2]="FACTURA";
		$titulos[5]="VALOR A CUADRAR";
		Mostrar(&$valor,"INGRESOS","valor",&$titulos,&$totales);
	}


	/*NUEVAS NOTAS CRÉDITO*/
	if(!isset($valorNC)) {

		$table="nueva_NC".date("Mdis");
		$q= "CREATE TEMPORARY TABLE IF NOT EXISTS ".$table." as "
		."SELECT * "
		."FROM ".$tipo_tablas."_000022 "
		."WHERE	Rfpfue	= '". $Ccofnc."' "
		."and	".$tipo_tablas."_000022.Fecha_data >= '$fechaAnt' "
		."and	Rfpcco	= '".$wcco."' "
		."and	NOT EXISTS (SELECT	Cdefue, Cdenum, Cdefpa "
		."					FROM	".$cuadre." "
		."					WHERE 	".$cuadre.".Cdefue = ".$tipo_tablas."_000022.Rfpfue "
		."					and		".$cuadre.".Cdenum = ".$tipo_tablas."_000022.Rfpnum "
		."					and		".$cuadre.".Cdefpa = ".$tipo_tablas."_000022.Rfpfpa  ) "
		."and	Rfpest = 'on' ";
		$res1 = mysql_query($q,$conex);

		$ini=explode("-",$wcaja);
		$cajcod=$ini[0];


		$q="SELECT Rfpfpa, SUM(Rfpvfp) AS Rfpvfp, Fpades, Rdefue, Rdenum, Rdefac, $table.Seguridad AS Seguridad, ".$tipo_tablas."_000010.Fecha_data as Fecha , Menfac as Vennum, Mendoc as Devnum, $table.id as id "
		."FROM $table, ".$tipo_tablas."_000023, ".$tipo_tablas."_000021, ".$tipo_tablas."_000010, ".$tipo_tablas."_000016 "
		."WHERE	Fpacod = Rfpfpa "
		."and	Rdeest = 'on' "
		."and	Rdecco = '".$wcco."' "
		."and	Rdefue = Rfpfue "
		."and	Rdenum = Rfpnum "
		."and	Mencco = '".$wcco."' "
		."and	Mencon = '801' "
		."and	Mendoc = Rdevta "
		."and	Menest = 'on' "
		."and	Vencco = '".$wcco."' "
		."and	Vennum = Menfac "
		."and	Vencaj = '".$wcaja."' "
		."GROUP BY Rfpfpa, Rfpnum, Rfpfue "
		."order by Rfpfpa, Fpades, Vennum ";
		$res = mysql_query($q,$conex);
		$num = mysql_num_rows($res);
		if ($num > 0)
		{
			for($i=0;$i<$num;$i++) {
				$row=mysql_fetch_array ($res);
				$valorNC[$i][0]=$row["Devnum"];
				$valorNC[$i][1]=$row["Fecha"];
				$valorNC[$i][2]=$row["Vennum"];
				$valorNC[$i][3]=$row["Rdefue"];
				$valorNC[$i][4]=$row["Rdenum"];
				$valorNC[$i][5]=$row["Seguridad"];
				$valorNC[$i][6]=$row["Rfpfpa"];
				$valorNC[$i][7]=$row["Fpades"];
				$valorNC[$i][8]=$row["Rfpvfp"];
				$valorNC[$i][9]=$row["Rfpvfp"];
				$valorNC[$i][10]=$row["Rfpvfp"]; 	//valorNC a Egresar
				$valorNC[$i][11]="on";			//Si se va a egresar o no
				//$valorNC[$i][12]=$row["id"]; 	//valorNC a Egresar

			}
		}
		$query = "DROP table ".$table;
		$err = mysql_query($query,$conex);
	}
	if(isset($valorNC)){

		$titulos[0]="DEV.";
		$titulos[2]="VENTA";
		$titulos[5]="VALOR A CUADRAR";
		echo "<br><br>";
		Mostrar(&$valorNC,"NOTAS CRÉDITO NUEVAS","valorNC",&$titulos,&$totales);
		
	}

	echo "<br><br>";
	$cuadreVacio= MostrarTotales(1);
	echo "<br><br>";
	$cuadreVacio=MostrarTotales(2);

	$query = "DROP table ".$cuadre;
	$err = mysql_query($query,$conex);

	if($cuadreVacio){
		echo "<font text color=#FF0000><B>NO HAY VALORES A SER CUADRADOS EN LA CAJA, <BR> NO PUEDE EFECTUAR EL CUADRE</B></FONT>";	
	}else{
	echo "<br><br><table width='350'>";
	if(!isset($listo) or $listo != "ok"){
		/*Aqui falta el manejo de errores del listo*/
		if(isset($pass)){
			echo "<tr><td colspan=2><font text color=".$AzulText."><b>PASSWORD PARA CUADRE DE CAJA:  <input type='password' name='pass' size=3></td>";
		}
		echo "<tr><td align='center'><font text color=".$AzulText."><b>CUADRAR CAJA<input type='checkbox' name='grabar' vaule='off' $checked ></td>";
	}
	else {
		echo "<input type='hidden' name='listo' value='$listo'>";
		echo "<input type='hidden' name='numCua' value='$numCua'>";

		echo "<td align='center'><a href='rep_cuadre.php?cuadre=$numCua&amp;wcco=$wcco&amp;wcaja=$wcaja-$wnomcaj&amp;tipo_tablas=$tipo_tablas' target='_blank'>Reporte del Cuadre</a></tr><tr>";
		
	}
	echo "<input type='hidden' name='tipo_tablas' value='$tipo_tablas'>";
	echo "<input type='hidden' name='fechaAnt' value='$fechaAnt'>";
	echo "<td align='center'><input type='submit' name='cuadre' value='ACEPTAR'></td></tr></table>";
	ECHO "</FORM>";
	}

}?>

</body>
</html>
