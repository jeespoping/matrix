 <html>
<head>
  <title>DEVOLUCION - POS</title>
</head>
<body>
<?php
include_once("conex.php");

/************************************************
*     PROGRAMA DEVOLUCIÓN DE VENTAS				*
*												*
*************************************************/

//==================================================================================================================================
//PROGRAMA						:POS
//AUTOR							:Ana María Betancur V.
$wautor="Ana María Betancur V.";
//FECHA CREACIÓN				:Agosto 2005
//FECHA ULTIMA ACTUALIZACIÓN 	:
$wactualiz="(Versión Feb 06 de 2005)";
//DESCRIPCIÓN					:Permite devolver articulos de una venta, tanto como nota crédito (devolución de dinero o anulación
//								 de la venta) o como simple devolución.
//																
//								TRANSACCIONES.
//								Devolucion: aumenta el kardex y genera un movimieto de inventario.
//								Anulacion: IDEM pero cambia a off el estado de la venta.
//								NC estandar: hace una devolucion y ademas genera en las tablas de recibo (20,21,22) registros por el
//								 valor de el dinero recibido en la venta.
//								NC de empresa con factura: genera una devolución pero el encabezado de los recibos (000020) pone el
//								valor total de la venta así como en el detalle (000021) y en el recibo de forma de pago (000022)el
//								valor de la cuota moderadora si hay, si no hace ningun registro en esa tabla.
//
//								Verifica que se pueda realizar la transaccion segun el tipo de empresa.
//
//								Es decir si es particular se puede hacer tanto devolucion como nota credito estandar PARCIALES, por lo
//								tanto debe verificar que no si se realizo alguna de las dos y ajustar las cantidades de los articulos.
//
//								Si es a una empresa las transacciones se hacen por el total de la venta. 
//
//								Si es a una empresa que no genera factura al momento de la venta puede ser o una nota credito estandar 
//								si existe cuota moderadora o una anulación si no existe tal, la diferencia entre una anulación. 
//
//								Si ademas es una venta por nomina verifica que no se haya realizado ningun pago para esa venta y 
//								pone el estado en off, es decir genera devolucion, nota credito a empresa con factura y anulación del 
//								cargo en nomina.

//ACTUALIZACIONES
//	Ene 04 2005
//		Se crea otro tipod e devolucion cuando es una venta por empresa que genrea factura al momento de la venta
//		debe hacerse una nota credito sin forma de pago por el valor de la venta total, en caso tal de que esa venta tuviera cuota
//		moderadora se hgace la forma de pago por el valor de esta
//		Se verifica que si es una eompresa que genera fatura ablocada esta no se halla hecho, que si es una venta por descuento
//		de nomina no haya pagado ninguna de la cuotas. Para ello se crea la funcion Realizable que se encarga de saber si se puede
//		o no efectuar la devolucion
//	Dic 28 2005
//		Se modifica para que tome los descuantos y los puntos, ambas cosas tomadas de la tabla 000017
//		La totalidad de los puntos se almacena en totPun, en cambio el descuento se va sumando al total de la devolución.
//	Oct 04 2005
//		Se modifican las tablas de farstore a $tipo_tablas, variables que le llega al programa desde el href que lo abre.
//		Se modifican los hypervinculos de Imprimir Bono para que sean consecuentes con las modificaciones en bono_dev.php.
//	Oct 17 2005
//		Se modifica las imagenes que aun apuntaban a FARMASTORE, ya apuntan a las carpetas de POS.
//	Oct 18 2005
//		Se introduce dentro de $des_tip el número de la nota crédito, en caso de que sea nota crédito.
//==================================================================================================================================
//TABLAS	
//	pos_000001
//	pos_000003
//	pos_000008
//	pos_000010
//	pos_000011
//	pos_000016
//	pos_000017
//	pos_000019
//	pos_000024
//	pos_000029
//	pos_000030
//	pos_000040
//	pos_000046

/**
 * Enter description here...
 *
 * @param unknown_type $conex
 * @param unknown_type $tipo_tablas
 * @param unknown_type $Vennum
 * @param unknown_type $tcl
 * @param unknown_type $devtip
 * @param unknown_type $des_devtip
 * @param unknown_type $Vencmod
 * @param unknown_type $Vencod
 * @param unknown_type $Vennit
 * @param unknown_type $nomina
 * @param unknown_type $cli
 * @param unknown_type $emp
 * @return unknown
 */
function Realizable($conex,$tipo_tablas,$Vennum,$tcl,$devtip,$des_devtip,$Vencmod,$Vencod,$Vennit,$Ventcl,$nomina,$cli,$emp) {

	$q= "select * from ".$tipo_tablas."_000041 "
	."where Clidoc	= '".$Vennit."'";
	$err=mysql_query($q,$conex);
	$num=mysql_num_rows($err);
	if($num>0){
		$cli=mysql_fetch_array($err);
	}else{
		$q= "select * from ".$tipo_tablas."_000041 "
		."where Clidoc	= '9999'";
		$err=mysql_query($q,$conex);
		$num=mysql_num_rows($err);
		if($num>0){
			$cli=mysql_fetch_array($err);
		}
	}

	$q= "select * from ".$tipo_tablas."_000024 "
	."where Empcod = ".$Vencod." "
	."AND	Empest = 'on'";
	$err=mysql_query($q,$conex);
	$num=mysql_num_rows($err);
	if($num>0){
		$emp=mysql_fetch_array($err);
	}

	if($Ventcl != '01') 	{
		$q="SELECT * "
		."FROM ".$tipo_tablas."_000029 "
		."WHERE Temcod='".$Ventcl."' ";
		$err=mysql_query($q,$conex);
		$num=mysql_num_rows($err);
		if($num>0) {
			$row=mysql_fetch_array($err);
			if($row['Temche'] == 'on'){
				$nomina=true;
				/*Verificar que no se halla pagado ninguna de las cuotas de la venta*/
				$q="SELECT Pnocuo, Pnocup "
				."FROM ".$tipo_tablas."_000046 "
				."WHERE Pnovta='".$Vennum."' "
				."AND	Pnoest = 'on'";
				$err1=mysql_query($q,$conex);
				$num1=mysql_num_rows($err1);
				if($num1>0) {
					$row1=mysql_fetch_array($err1);
					if($row1['Pnocuo'] != $row1['Pnocup']){
						echo "YA SE EFECTUO UNO O MAS PAGOS DE LAVENTA POR NOMINA<BR>";
						return (false);
					}
				}
			}else
			$nomina=false;


			/*FALTA ($Vencmod > 0 and $emp['Empfac'] == 'on')
			es decir fue facturada por un valor pero tiene cmod por otro*/
			if($Vencmod == 0) {
				IF(	$emp['Empfac'] == 'off') {
					/*No genera factura durante la vnta si no una factura ablocada*/
					$tcl="E0";	// Es una devolución unicamente SIN NC
					$devtip="DEV";
					$des_devtip="ANULACIÓN";
					/*Buscar que no se halla hecho la factura ablocada*/
					$q="SELECT Fdefac "
					."FROM ".$tipo_tablas."_000019 "
					."WHERE Fdenve = '".$Vennum."' "
					."AND	Fdeest = 'on' ";
					$err=mysql_query($q,$conex);
					$num=mysql_num_rows($err);
					if($num>0){
						/*Ya se realizo la factura ablocada para esta venta*/
						echo "YA FUE REALIZADA LA FACTURA ABLOCADA PARA LA VENTA<BR>";
						return(false);
					}
				}else{
					/*NOTA CRÉDITO SIN FORMA DE PAGO
					$emp['Empfac'] == 'on'*/
					$tcl="E2";	// Es una nota crédito
					$devtip="NC";
					$des_devtip="NOTA CRÉDITO EMPRESA";
				}
			}elseIF ($emp['Empfac'] == 'off') {
				/*NOTA CRÉDITO CON FORMA DE PAGO*/
				$tcl="E1";	// Es una nota crédito
				$devtip="NC";
				$des_devtip="NOTA CRÉDITO EMPRESA";
				/*Buscar que no se halla hecho la factura ablocada*/
				$q="SELECT Fdefac "
				."FROM ".$tipo_tablas."_000019 "
				."WHERE Fdenve = '".$Vennum."' "
				."AND	Fdeest = 'on' ";
				$err=mysql_query($q,$conex);
				$num=mysql_num_rows($err);
				if($num>0){
					/*Ya se realizo la factura ablocada para esta venta*/
					echo "YA FUE REALIZADA LA FACTURA ABLOCADA PARA LA VENTA<BR>";
					return(false);
				}
			}
		}else {
			/*NO EXISTE EL TIPO DE EMPRESA*/
			echo "NO EXISTE EL TIPO DE EMPRESA<BR>";
			return (false);
		}
	}else{
		$tcl="P";		// Es a particular puede ser devol

		//	$devtip="";
	}
	return (true);
}

/**
	* @return void
	* @param Array $row
	*	tiene todos los valores del encabezado de la venta a ser devuelta
	*	es decir los valores de un registro de la tabla ".$tipo_tablas."_000016
	*	$vde[$i][0]=$row["Artcod"];		//Codigo del Articulo
	*	$vde[$i][1]=$row["Artnom"];		//Nombre del Articulo
	*	$vde[$i][2]=$row["Vdecan"];		//Cantidad
	*	$vde[$i][3]=$row["Vdevun"];		//Valor unitario
	*	$vde[$i][4]=$row["Vdepiv"];		//% de IVA
	*	$vde[$i][5]=$row["Vdecan"]*(1+($row["Vdepiv"]/100))*$row["Vdevun"];	//Valor total para el articulo
	*	$vde[$i][6]='off';				//Si va a ser devuelto o no
	* @desc Función que muestra en pantalla el encabezado de la venta consignada en $row
	*/
function encabezado ($row, $cli, $emp) {
	$wcolspan=6;

	$wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
	$wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
	$wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
	$wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro

	$wcf3="#FFDBA8";	//COLOR DEL FONDO 3 PARA RESALTAR -- Amarillo quemado claro
	$wcf4="#A4E1E8";	//COLOR DEL FONDO 4 -- Aguamarina claro
	$wcf5="#57C8D5";	//COLOR DEL FONDO 5 -- Aguamarina Oscuro
	$wclam="#A4E1E8";	//COLOR DE LA LETRA -- Aguamarina Clara

	echo "<br><br><center><table border='1' width='600'>";
	echo "<tr><td align=center colspan='$wcolspan' bgcolor=".$wcf2."><font size=3 size=3 size=3 face='arial' color=#FFFFFF><b>VENTA # ".$row["Vennum"]."</b></td></tr>";
	echo "<tr><td colspan='$wcolspan' align='center' bgcolor=".$wcf5."><b><font size=3 face='arial' color=".$wclfg." >INFORMACIÓN DEL CLIENTE</td></tr>";

	echo "<tr><td colspan='".($wcolspan/3)."'><font size=2 face='arial' color=".$wcf2."><b>Beneficiario:</b> ".$cli["Clinom"]."</td>";
	echo "<td colspan='".($wcolspan/3)."'><font size=2 face='arial' color=".$wcf2."><b>Documento:</b> ".$cli["Clidoc"]."</td>";
	echo "<td colspan='".($wcolspan/3)."'><font size=2 face='arial' color=".$wcf2."><b>Telefono:</b> ".$cli["Clite1"]."</tr>";


	echo "<tr><td colspan='".($wcolspan/3)."'><font size=2 face='arial' color=".$wcf2."><b>Responsable:</b> ".$emp["Empnom"]."</td>";
	echo "<td colspan='".($wcolspan/3)."'><font size=2 face='arial' color=".$wcf2."><b>Código / Tarifa Empresa:</b> ".$row["Vencod"]." / ".$emp["Emptar"]." </td>";
	echo "<td colspan='".($wcolspan/3)."'><font size=2 face='arial' color=".$wcf2."><b>NIT:</b> ".$emp["Empnit"]."</td></tr>";

	echo "<tr><td colspan='$wcolspan' align='center' bgcolor=".$wcf5."><b><font size=3 face='arial' color=".$wclfg." >INFORMACIÓN VENTA</td></tr>";


	echo "<tr><td colspan='".($wcolspan/3)."'><font size=2 face='arial' color=".$wcf2."><b>Concepto:</b> ".$row["Vencon"]."-".$row["Condes"]."</td>";
	echo "<td colspan='".($wcolspan/3)."'><font size=2 face='arial' color=".$wcf2."><b>CC:</b> ".$row["Vencco"]."-".$row["Ccodes"]."</td>";
	echo "<td colspan='".($wcolspan/3)."'><font size=2 face='arial' color=".$wcf2."><b>Fecha:</b> ".$row["Venfec"]."</td>";

	echo "<tr><td colspan='".($wcolspan/3)."'><font size=2 face='arial' color=".$wcf2."><b>Valor Total:</b> $".number_format($row["Venvto"],2,'.',',')."</td>";
	echo "<td colspan='".($wcolspan/3)."'><font size=2 face='arial' color=".$wcf2."><b>Valor IVA:</b> $".number_format($row["Venviv"],2,'.',',')."</td>";
	echo "<td colspan='".($wcolspan/3)."'><font size=2 face='arial' color=".$wcf2."><b>Valor Copago:</b> $".number_format($row["Vencop"],2,'.',',')."</td></tr>";

	echo "<tr><td colspan='".($wcolspan/3)."'><font size=2 face='arial' color=".$wcf2."><b>Cuota Moderadora:</b> $".number_format($row["Vencmo"],2,'.',',')."</td>";
	echo "<td colspan='".($wcolspan/3)."'><font size=2 face='arial' color=".$wcf2."><b>Valor Descuento: $</b>".number_format($row["Vendes"],2,'.',',')."</td>";
	echo "<td colspan='".($wcolspan/3)."'><font size=2 face='arial' color=".$wcf2."><b>Recargo: $</b> ".number_format($row["Venrec"],2,'.',',')."</td></tr></table><br>";
}

/**
	* @return void
	* @param string $name	Tiene el nombre real del array en donde están las variables
	* @param Arra[][] $vde	Array asociativo con las  con las variables de los detalles de la venta
	*						traidos de la tabla : ".$tipo_tablas."_000017
	* @desc Imprime en pantalla los articulos a devolver
	*/
function detalle($name, $vde, $tcl, $venCmod,$totPun) {
	$wcolspan=7;
	$wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
	$wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
	$wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
	$wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro

	$wcf3="#FFDBA8";	//COLOR DEL FONDO 3 PARA RESALTAR -- Amarillo quemado claro
	$wcf4="#A4E1E8";	//COLOR DEL FONDO 4 -- Aguamarina claro
	$wcf5="#57C8D5";	//COLOR DEL FONDO 5 -- Aguamarina Oscuro
	$wclam="#A4E1E8";	//COLOR DE LA LETRA -- Aguamarina Clara

	echo "<table border='1'><tr ><td colspan='$wcolspan' align='center' bgcolor=".$wcf5."><b><font size=3 face='arial' color=".$wclfg." >DETALLE DE ARTICULOS<b></td></tr>";
	echo "<tr><td align=left bgcolor=".$wcf4."><b><font size=2 face='arial' color=".$wclfg.">CÓDIGO</TD>";
	echo "<td align=left bgcolor=".$wcf4."><b><font size=2 face='arial' color=".$wclfg.">NOMBRE</TD>";
	echo "<td align=left bgcolor=".$wcf4."><b><font size=2 face='arial' color=".$wclfg.">CANT.</TD>";
	echo "<td align=left bgcolor=".$wcf4."><b><font size=2 face='arial' color=".$wclfg.">VALOR UNIDAD</TD>";
	echo "<td align=left bgcolor=".$wcf4."><b><font size=2 face='arial' color=".$wclfg.">% IVA</TD>";
	echo "<td align=left bgcolor=".$wcf4."><b><font size=2 face='arial' color=".$wclfg.">VALOR TOTAL</TD>";
	echo "<td align=left bgcolor=".$wcf4."><b><font size=2 face='arial' color=".$wclfg.">DEVOLVER</TD>";
	$num= count($vde);
	$total=0;
	$totPun=0;
	for($i=0;$i<$num;$i++){
		echo "<tr><td align=left bgcolor=".$wclfa."><font size=2 face='arial' color=".$wcf2.">".$vde[$i][0]."</TD>";
		echo "<td align=left bgcolor=".$wclfa."><font size=1 face='arial' color=".$wcf2.">".$vde[$i][1]."</TD>";

		if($vde[$i][7] >0 and $tcl == "P"){
			echo "<td align=left bgcolor=".$wclfa."><font size=2 face='arial' color=".$wcf2."><input type='text' name='".$name."[".$i."][2]' value='".$vde[$i][2]."' size='1'></TD>";
		}else{
			echo "<td align=left bgcolor=".$wclfa."><font size=2 face='arial' color=".$wcf2.">".$vde[$i][2]."</td>";
			echo "<input type='hidden' name='".$name."[".$i."][2]' value='".$vde[$i][2]."'>";
		}
		echo "<td align=left bgcolor=".$wclfa."><font size=2 face='arial' color=".$wcf2.">".number_format($vde[$i][3],0,'',',')."</TD>";
		echo "<td align=left bgcolor=".$wclfa."><font size=2 face='arial' color=".$wcf2.">".$vde[$i][4]."</TD>";
		echo "<td align='right' bgcolor=".$wclfa."><font size=2 face='arial' color=".$wcf2.">".number_format(($vde[$i][3]*$vde[$i][2]),2,'.',',')."</TD>";

		if(isset($vde[$i][6])){
			$totPun=$totPun+($vde[$i][8]*$vde[$i][7]);
			if($tcl == "P"){
				echo "<td align=center bgcolor=".$wclfa."><b><font size=2 face='arial' color=".$wcf2."><input type='checkbox' name='".$name."[".$i."][6]' checked></TD>";
				$total=$total+($vde[$i][3]*$vde[$i][2]);
			}else{
				echo "<td align=center bgcolor=".$wclfa."><b><font size=2 face='arial' color=".$wcf2.">SELECCIONADO</TD>";
				echo "<input type='hidden' name='".$name."[".$i."][6]' value='on'>";
			}
		}else if($vde[$i][7] > 0) {
			echo "<td align=center bgcolor=".$wclfa."><b><font size=2 face='arial' color=".$wcf2."><input type='checkbox' name='".$name."[".$i."][6]'></TD>";
		} else {
			echo "<td align=center bgcolor=".$wclfa."><b><font size=2 face='arial' color=".$wcf2."><b>x<b></TD>";
		}
		echo "<input type='hidden' name='".$name."[".$i."][0]' value='".$vde[$i][0]."'>";
		echo "<input type='hidden' name='".$name."[".$i."][1]' value='".$vde[$i][1]."'>";
		echo "<input type='hidden' name='".$name."[".$i."][4]' value='".$vde[$i][4]."'>";
		echo "<input type='hidden' name='".$name."[".$i."][5]' value='".$vde[$i][5]."'>";
		echo "<input type='hidden' name='".$name."[".$i."][7]' value='".$vde[$i][7]."'>";
		echo "<input type='hidden' name='".$name."[".$i."][3]' value='".$vde[$i][3]."'>";
		echo "<input type='hidden' name='".$name."[".$i."][8]' value='".$vde[$i][8]."'>";
	}
	if($tcl != "P")
	$total=$venCmod;
	echo "</tr><tr><td colspan=5 bgcolor='$wcf3'><b><font size=2 face='arial' color=".$wclfg.">TOTAL</b></td><td  bgcolor='$wcf3' align='right'><b><font size=2 face='arial' color=".$wclfg.">$ ".number_format($total,2,'.',',')."</b></td ><td bgcolor='$wcf3'>&nbsp;</td></tr>";
	echo "</table>";
	return($total);
}

/**
	* @return void
	* @param	int	$fin
	*				fin=0 -> No se ha grabado la DEV, imprimir radio devtip
	*				fin=1 -> Ya se grabo la devolución no imprimir radio devtip
	* @param	int	$wpass
	*				wpass=0 -> No se ha solicitado devolver no hacer nada
	*				wpass=1 -> Pedir el password por medio de un input type=text
	*				wpass=2 -> La contraseña ingresada esta errada
	*				wpass=3 -> Ya ingreso la contraseña pero no ha escogidoescogio el devtip
	* @desc Imprime en pantalla las opciones al usuario para devolver, hacer nota crédito, ingresar password
	*		o simplemente ACEPTAR para que se calculen los nuevos valores.
	*/
function finalForma($fin,$wpass) {
	global $tcl;
	global $devtip;
	global $pass;
	global $wclfg;
	//	echo "fin=$fin<br>wpass=$wpass<br>";
	echo "<br><table border='0' align='center'><TR>";
	if($wpass == 2) {
		echo "<td colspan='2'><font size=2 face='arial' color=".$wclfg." >PASSWORD ERRADO INTENTELO NUEVAMENTE</td></tr><tr>";
	}else if($wpass == 3) {
		//echo "<input type='hidden' name='pass' value='$pass'>";
		echo "<td><font size=2 face='arial' color='".$wclfg."'>NOTA CREDITO <input type='radio' name='devtip' value='NC'></td>";
		echo "<td><font size=2 face='arial' color='".$wclfg."'>DEVOLUCIÓN <input type='radio' name='devtip' value='DEV'></td>";
	}else if($wpass == 1) {
		echo "<td colspan='3'><font size=2 face='arial' color='".$wclfg."'>INGRESE SU PASSWORD ";
		echo "<input type='password' name='pass' size='3'></td></tr><tr>";
		if($devtip != "DEV" or $tcl != 'P'){
			echo "<td colspan='3'><font size=2 face='arial' color='".$wclfg."'>INGRESE LA EL MOTIVO DE LA TRANSACCIÓN <br>(es obligatorio excepto cuando sea una simple devolución por parte del cliente)<br> ";
			echo '<textarea ALIGN="CENTER"  ROWS="3" name="motivo" cols="50" style="font-family: Arial; font-size:14"></textarea></font>';
			echo "</td></tr><tr>";
		}
	}elseif ($wpass== 4){

		echo "<td colspan='3'><font size=2 face='arial' color='".$wclfg."'>INGRESE LA EL MOTIVO DE LA TRANSACCIÓN <br>(es obligatorio excepto cuando sea una simple devolución por parte del cliente)<br> ";
		echo '<textarea ALIGN="CENTER"  ROWS="3" name="motivo" cols="50" style="font-family: Arial; font-size:14"></textarea></font>';
		echo "</td></tr><tr>";
	}else if($wpass == 5) {
		echo "<td colspan='3'><font size=2 face='arial' color='".$wclfg."'>INGRESE SU PASSWORD ";
		echo "<input type='password' name='pass' size='3'></td></tr><tr>";

	}

	if($fin == 0 and $wpass == 0) {
		/**No se ha finalizado el proceso, debe dar las opciones de Nota credito y devolución *
		* echo "<td><font size=2 face='arial' color=".$wclfg.">NOTA CREDITO <input type='radio' name='devtip' value='NC'></td>";
		* echo "<td><font size=2 face='arial' color=".$wclfg.">DEVOLUCIÓN <input type='radio' name='devtip' value='DEV'></td>";
		*/
		echo "<td><font size=2 face='arial' color=".$wclfg.">REALIZAR TRANSACCIÓN <input type='checkbox' name='realizar'></td>";
	}
	echo "<td ><input type='submit' name='aceptar' value='ACEPTAR'></TR></TABLE>";
}

/**
	* @return void
	* @param Arr[] [] $vde
	* @desc Valida que la cantidad a devolver no exceda lo que en realidad puede devolver.
	*/
function ValidarCant($vde){
	$j=0;
	$art="";
	$val="";
	for($i=0; $i < count($vde); $i++){
		if($vde[$i][2] > $vde[$i][7]){
			$warning='on';
			$art=$art."<br>".$vde[$i][0]."-".$vde[$i][1]."(Cant. errada = ".$vde[$i][2]." Cant. Máx= ".$vde[$i][7].")";
			$j++;
			$vde[$i][2] = $vde[$i][7];
		}
	}
	if(isset($warning)){
		echo "<table border='1' width='600'><tr><td ><font size=2 face='arial' color='#FF0000'>A continuación se relacionan los articulos, con su código y descripción, cuya cantidad a devolver excede la permitida, pues supera la diferencia entre la cantidad vendida y  las cantidades de las devoluciones anteriores a la actual.<b>$art</b><br> Por ello fueron fueron modificados a la máxima cantidad posible a devolver. Por favor verifique!!!</td></tr></table><br><br>";

	}
}


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
	

	


	$pos = strpos($user,"-");
	$wusuario = substr($user,$pos+1,strlen($user));


	echo "<form action='' method='POST'>";

	//ACA TRAIGO LAS VARIABLES DE SUCURSAL, CAJA Y TIPO INGRESO QUE REALIZA CADA CAJERO
	if(!isset($wcco)) {
		$q =  " SELECT Cjecco,Cjecaj "
		."   FROM ".$tipo_tablas."_000030 "
		."  WHERE cjeusu = '".$wusuario."'"
		."    AND cjeest = 'on' ";
		$err=mysql_query($q,$conex);
		$num=mysql_num_rows($err);
		if($num>0)
		{
			$row=mysql_fetch_array($err);
			$ini=explode("-",$row[0]);
			$wcco=$ini[0];
			$wnomcco=$ini[1];

			echo "<input type='HIDDEN' name='wcco' value='".$wcco."'>";
			echo "<input type='HIDDEN' name='wnomcco' value='".$wnomcco."'>";

		}
		else {
			echo "Usuario no registrado";
		}
	}else	{
		echo "<input type='HIDDEN' name='wcco' value='".$wcco."'>";
		echo "<input type='HIDDEN' name='wnomcco' value='".$wnomcco."'>";
	}



	$wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
	$wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
	$wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
	$wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro

	$wcf3="#FFDBA8";	//COLOR DEL FONDO 3 PARA RESALTAR -- Amarillo quemado claro
	$wcf4="#A4E1E8";	//COLOR DEL FONDO 4 -- Aguamarina claro
	$wcf5="#57C8D5";	//COLOR DEL FONDO 5 -- Aguamarina Oscuro
	$wclam="#A4E1E8";	//COLOR DE LA LETRA -- Aguamarina Clara



	/******************************************************************
	AQUI EMPIEZA EL PROGRAMA
	*/

	echo "<p align=right><font size=1><b>Autor: ".$wautor."</b></font></p>";

	echo "<center><table border width='350'>";
	echo "<tr><td align=center colspan='2' ><img src='/matrix/images/medical/POS/logo_".$tipo_tablas.".png' WIDTH=388 HEIGHT=70></td></tr>";
	echo "<tr><td align=center colspan='2' bgcolor=".$wcf2."><font size=3 text color=#FFFFFF><b>PROCESOS DE VENTA  </b></font></td></tr>";
	echo "<tr><td align=center colspan='2' bgcolor=".$wcf2."><font size=3 text color=#FFFFFF><b>DEVOLUCIÓN DE ARTICULOS</B></font></td></tr>";
	echo "</table>";
	if(!isset($Vennum)) {
		/*INGRESAR EL NÚMERO DE VENTA A DEVOLVER*/
		echo "<table border width='350'>";
		echo "<tr><td bgcolor=".$wcf2."><font size=3 text color=#FFFFFF><b>INGRESE EL NÚMERO DE LA VENTA:</B></td>";
		echo "<td bgcolor=".$wcf2."><input type='text' name='Vennum' size='2'></td>";
		echo "<tr><td align='center' bgcolor=".$wcf2." colspan='2'><input type='submit' name='aceptar' value='ACEPTAR'>";

	}else{


		echo "<input type='hidden' name='Vennum' value='$Vennum'>";

		$q= "SELECT Vennum,Ventcl, Vencmo, Vennmo, ".$tipo_tablas."_000016.id as Vennid, Venest,Vennit, Vencod, Vencon, Vencco, Venfec, Venvto, Venviv,Vencop, Vendes,Venrec,Vencaj,Condes,Ccodes "
		."FROM ".$tipo_tablas."_000016, ".$tipo_tablas."_000008, ".$tipo_tablas."_000003  "
		."WHERE Vencco 	= '".$wcco."' "
		."and 	Vennum 	= '".$Vennum."' "
		."and	Concod	= Vencon ";
		$err=mysql_query($q,$conex);
		$num=mysql_num_rows($err);
		if($num>0)	{
			$ven=mysql_fetch_array($err);
			$venCmod=$ven["Vencmo"];
			$Ventcl= substr($ven['Ventcl'],0,2);
			$Vennmo=$ven["Vennmo"];
			$venId=$ven["Vennid"];
			$wcaja=$ven["Vencaj"];
			$Venvto=$ven["Venvto"];
			echo "<input type='HIDDEN' name='wcaja' value='".$wcaja."'>";

			if(!isset($tcl))
			$tcl="";
			if(!isset($devtip))
			$devtip="";
			if(!isset($des_devtip))
			$des_devtip="";
			$nomina=false;
			$cli=array();
			$emp=array();
			if($ven["Venest"] != 'off' and
			Realizable($conex,$tipo_tablas,$Vennum,&$tcl,&$devtip,&$des_devtip,&$venCmod,$ven['Vencod'],$ven['Vennit'],substr($ven['Ventcl'],0,2),&$nomina,&$cli,&$emp))	{

				echo "<input type='hidden' name='nomina' value='".$nomina."'>";
				/*		if($tcl == "P" and !isset($realizar))
				unset($devtip);*/

				/*BUSCAR SI EXISTE UN MOV DE INVENTARIO DE DEVOLUCIÓN PARA ESTA VENTA*/
				if(!isset($devPrevia)){
					$q="SELECT Mendoc "
					."FROM ".$tipo_tablas."_000010 "
					."WHERE	Menfac = '$Vennum' "
					."AND	Mencon = '801'";
					$err=mysql_query($q,$conex);
					$num=mysql_num_rows($err);
					if($num>0){
						$devPrevia="on";
					}else {
						$devPrevia="";
					}
				}

				/**Buscar el cliente para el encabezado*/

				encabezado(&$ven, &$cli, &$emp);

				if(!isset($vde)) {
					$q="SELECT Vdeart, Vdevun, SUM(Vdecan) as Vdecan, Vdepiv, Vdedes,Vdepun,Artnom "
					."FROM ".$tipo_tablas."_000017, ".$tipo_tablas."_000001  "
					."WHERE Vdenum 	= '".$Vennum."' "
					."and	Vdeest	= 'on' "
					."and	Artcod	= Vdeart "
					."GROUP BY Vdeart "
					."order by Vdeart, Vdecan ";
					$err1=mysql_query($q,$conex);
					$numart=mysql_num_rows($err1);
					if($numart>0)	{
						for($i=0;$i<$numart;$i++){
							$row1=mysql_fetch_array($err1);
							$vde[$i][0]=$row1["Vdeart"];		//Codigo del Articulo
							$vde[$i][1]=$row1["Artnom"];		//Nombre del Articulo
							$vde[$i][2]=$row1["Vdecan"];		//Cantidad
							$descuento=round(((($row1["Vdepiv"]/100+1)*$row1["Vdedes"])/$row1["Vdecan"]),0);
							$vde[$i][3]=$row1["Vdevun"]-$descuento;		//Valor unitario
							$vde[$i][4]=$row1["Vdepiv"];		//% de IVA
							$vde[$i][5]=$row1["Vdecan"]*$row1["Vdevun"];	//Valor total para el articulo
							if($tcl == "P") {
								//	$vde[$i][6]='off';				//Si va a ser devuelto o no
								unset($vde[$i][6]);
							}else{
								$vde[$i][6]='on';				//Todos los Artículos debén ser devueltos
							}
							$vde[$i][7]=$row1["Vdecan"];		//Cantidad MAxima no exceder
							$vde[$i][8]=$row1["Vdepun"]/$row1["Vdecan"];		//Puntos
							if($devPrevia != ""){
								if($tcl == "P") {
									/*Existe una devolución previa para esa venta*/
									$q="SELECT Mdecan "
									."FROM ".$tipo_tablas."_000011,".$tipo_tablas."_000010 "
									."WHERE	Menfac = '$Vennum' "
									."AND	Mencon = '801'"
									."AND	Mdedoc = Mendoc "
									."AND	Mdecon = Mencon "
									."AND	Mdeart = '".$vde[$i][0]."' ";
									$err2=mysql_query($q,$conex);
									$num2=mysql_num_rows($err2);
									if($num2 > 0){
										for($j=0;$j<$num2;$j++){
											$row2=mysql_fetch_row($err2);
											$vde[$i][2]=$vde[$i][2]-$row2[0];		//Cantidad
										}
									}
									$vde[$i][7]=$vde[$i][2];
								}else{
									$vde[$i][7]=0; // Si existe una devolución previa y es a empresa se devuelve todo
								}
							}
						}
					}
				}
				echo "<input type='hidden' name='numart' value='".$numart."'>";


				echo "<br><br><table align='center'><tr>";
				ValidarCant(&$vde);
				$totPun=0;
				$total=detalle("vde", &$vde, $tcl, $venCmod,&$totPun);

				if(isset($realizar)) {
					echo "<input type=hidden name='realizar' value='$realizar'>";
					if($fin ==0) {
						/*Eescogio o el sistema escogio el tipo de transacción y no se
						ha realizado la transacción*/
					//	echo "devtip=$devtip<br>";
						if($devtip == "" and $tcl == "P" ){ 
							echo "<input type=hidden name='fin' value=0><br>";
							finalForma(0,3);
						}else{
							if((!isset($des_devtip) or $des_devtip=="") and isset($devtip)) {
								if($devtip == "DEV")
								$des_devtip="DEVOLUCIÓN";
								else
								$des_devtip="NOTA CRÉDITO";
							}
							echo "<input type=hidden name='devtip' value='$devtip'>";
							echo "<input type=hidden name='des_devtip' value='$des_devtip'>";

							if(!isset($pass)) {
								/*No ha ingresado el password*/
								echo "<input type=hidden name='fin' value=0><br>";
								finalForma(0,1);//ingresar PASSWORD
							}else if(!isset($motivo) or (isset($motivo) and $motivo != "")) {
								/*Confirmar contraseña*/
								$q="SELECT Cjeusu "
								."FROM ".$tipo_tablas."_000030 "
								."WHERE Cjecla='$pass' ";
								$err = mysql_query($q,$conex);
								$num = mysql_num_rows($err);
								if ($num > 0)
								{
									$row=mysql_fetch_row($err);
									$usu=$row[0];
									include_once("pos/Grabar_devolucion.php");
									if($ok_NC == true) {
										if($devtip != "DEV" or $tcl != "P"){
											if($devtip != "DEV")
											$des_devtip=$des_devtip." ".$rennum;
											//$motivo="amp;motivo=".$motivo;
										}else{
											$motivo="";
										}
										if($tcl == 'E2' and $venCmod >0){
											echo"<td><a href='bono_dev.php?wcco=$wcco&amp;doc=".$mendoc."&amp;vta=".$ven["Vennum"]."&amp;tipo_tablas=$tipo_tablas&amp;devtip=$des_devtip&amp;motivo=$motivo' target='_blank'>Imprimir Nota Credito/a></td></tr></table>";
											echo"<td><a href='bono_dev.php?wcco=$wcco&amp;doc=".$mendoc."&amp;vta=".$ven["Vennum"]."&amp;tipo_tablas=$tipo_tablas&amp;devtip=NOTA CRÉDITO CUOTA MODERADORA&amp;motivo=$motivo' target='_blank'>Imprimir Bono Cuota Moderadora</a></td></tr></table>";
										}else{
											echo"<td><a href='bono_dev.php?wcco=$wcco&amp;doc=".$mendoc."&amp;vta=".$ven["Vennum"]."&amp;tipo_tablas=$tipo_tablas&amp;devtip=$des_devtip&amp;motivo=$motivo' target='_blank'>Imprimir Bono</a></td></tr></table>";
										}
									}
									echo "<input type=hidden name='fin' value=1>";
									echo "<input type=hidden name='usu' value='$usu'>";
									echo "<input type=hidden name='mendoc' value='$mendoc'>";
									echo "<input type=hidden name='motivo' value='$motivo'>";
									echo "<input type=hidden name='ok_NC' value='$ok_NC'>";
									finalForma(1,0);
								} else {
									echo "<input type=hidden name='fin' value=0><br>";
									if(isset($motivo))
									echo "<input type=hidden name='motivo' value='$motivo'><br>";
									finalForma(0,5);//Password EQUIVOCADO
								}
							}else {
								echo "<input type=hidden name='fin' value=0><br>";
								echo "<input type=hidden name='pass' value='$pass'><br>";
								finalForma(0,4);//Motivo Vacio
							}
						}
					}
					else{
						/*YA SE GRABO LA DEVOLUCIÓN*/
						echo "<input type=hidden name='usu' value='$usu'>";
						echo "<input type=hidden name='devtip' value='$devtip'>";
						echo "<input type=hidden name='des_devtip' value='$des_devtip'>";
						echo "<input type=hidden name='fin' value=1>";
						echo "<input type=hidden name='mendoc' value='$mendoc'>";
						echo "<input type=hidden name='ok_NC' value='$ok_NC'>";
						if($ok_NC == true){
							if($tcl == 'E2' and $venCmod >0){
								echo"<td><a href='bono_dev.php?wcco=$wcco&amp;doc=".$mendoc."&amp;vta=".$ven["Vennum"]."&amp;tipo_tablas=$tipo_tablas&amp;devtip=$des_devtip&amp;motivo=$motivo' target='_blank'>Imprimir Nota Credito/a></td></tr></table>";
								echo"<td><a href='bono_dev.php?wcco=$wcco&amp;doc=".$mendoc."&amp;vta=".$ven["Vennum"]."&amp;tipo_tablas=$tipo_tablas&amp;devtip=NOTA CRÉDITO CUOTA MODERADORA&amp;motivo=$motivo' target='_blank'>Imprimir Bono Cuota Moderadora</a></td></tr></table>";
							}else{
								echo"<td><a href='bono_dev.php?wcco=$wcco&amp;doc=".$mendoc."&amp;vta=".$ven["Vennum"]."&amp;tipo_tablas=$tipo_tablas&amp;devtip=$des_devtip&amp;motivo=$motivo' target='_blank'>Imprimir Bono</a></td></tr></table>";
							}
						}

						finalForma(1,0);
					}
				} else {
					echo "<input type=hidden name=fin value=0><br>";
					finalForma(0,0);
				}

			}else{
				echo "LA VENTA HA SIDO ANULADA";
			}
		}else{
			echo "LA VENTA NO EXISTE ";
		}
	}
}

?>
</body>
</html>
