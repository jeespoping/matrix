<?php
include_once("conex.php");
if( !isset($_SESSION['user']) ){//session muerta en una petición ajax
	echo "<br /><br /><br /><br />
              <div style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;' id='div_sesion_muerta'>
                  [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
             </div>";
      die;
	return;
}
include_once( "root/comun.php" );
require_once("conex.php");
mysql_select_db( "matrix" );
?>
<html>
<head>
	<title>COMPROBANTE DIARIO VENTAS - POS </title>
	<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
	<style type="text/css">
		  .botona{
            font-size:13px;
            font-family:Verdana,Helvetica;
            font-weight:bold;
            color:white;
            background:#638cb5;
            border:0px;
            height:30px;
            margin-left: 1%;
            cursor: pointer;
         }
	</style>
	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
	<script type="text/javascript">//codigo javascript propio
	    $.datepicker.regional['esp'] = {
	        closeText: 'Cerrar',
	        prevText: 'Antes',
	        nextText: 'Despues',
	        monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
	        'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
	        monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
	        'Jul','Ago','Sep','Oct','Nov','Dic'],
	        dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
	        dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
	        dayNamesMin: ['D','L','M','M','J','V','S'],
	        weekHeader: 'Sem.',
	        dateFormat: 'yy-mm-dd',
	        yearSuffix: ''
	    };
	    $.datepicker.setDefaults($.datepicker.regional['esp']);
	</script>
	<script type="text/javascript">
		$(document).ready(function(){
			$("#fecha").datepicker({
		            showOn: "button",
		            buttonImage: "../../images/medical/root/calendar.gif",
		            dateFormat: 'yy-mm-dd',
		            buttonImageOnly: true,
		            changeMonth: true,
		            changeYear: true,
		            reverseYearRange: true,
		            maxDate: new Date()
            });
		});
		function retornar( url ){
			window.location = url;
		}
	</script>
</head>
<body>
<?php

/************************************************
*     PROGRAMA COMPROBANTE DE VENTAS			*
*												*
*************************************************	*/

//==================================================================================================================================
//PROGRAMA						:POS
//AUTOR							:Ana María Betancur V.
//FECHA CREACION				:Agosto 2005
//FECHA ULTIMA ACTUALIZACION 	:
$wactualiz="(2014-10-17)";
//DESCRIPCION					:Realiza un reporte de combrobante de ventas diarias. Muestra los consecutivos y la sumatoria de
//								 los tipos de transacciones. Los valores de los tipos de transacciones, sus ivas y las formas y
//								 medios de pago.
//==================================================================================================================================

//ACTUALIZACIONES
//	octu 17 2014
//		Se cambian los estilos del reporte para que encajen con matrix
//	Sept 20 2005
//		Se crea el submit de la primera parte, no existía.
//	Sept 23 2005
//		Se emplea la nueva variable tipo_tabla para saber en que tablas buscar la información.
//	Sept 26 2005
//		Se modifican los IVAs del RESUMEN DE VENTAS, no estaban bien calculados pues el valor unitario del producto ya incluye IVA.
//	Oct  04 2005
//		Se modifican las notas crédito para que resten dentro de las ventas, y para que tomen la caja a la cual pertenecen las
//		ventas no en la que se realizo la nota crédito (como lo toma el cuadre de caja).
//		Se modifica para que el logo sea dinámico.
//	Oct 17 2005
//		Se modifican todos los movimientos de devolucion de inventario para que aparescan las anulaciones y las notas crédito
//		Se modifica para que en las formas de pago se resten la NC

/**
 * @modified 2007-08-17 Se modifica el query de las facturas automaticas, pues el strlen lo estaba haciendo a Ccopfm y debia ser a Ccopfa, pues cuando el tamaño en caracteres de Ccopfm diferia del de Ccopfa no encontraba los numeros de las facturas del día.
 * @modified 2007-06-04 Cuando no habia ventas, solo notas crédito no estaba imprimiendo las formas de pago de las notas, se realiza un cambio de modo que se aumente el $numFP por cada forma de pago que no existe en la s ventas pero si en las notas crédito.
 * @modified 2007-06-04 Se crea el for en la sección de notas crédito del movimiento de caja.
 * @modified 2007-06-04 Se organizan los datos de los descuentos en las notas crédito, es decir se borra la resta del 2007-05-09
 * @modified 2007-05-09 Se suben los calculos de los subtotales de las ventas ($sub[][]) y de las notas crédito, con el fin de subir el ajuste de los ivas.
 * @modified 2007-05-08 Se modifica la impresión de las notas crédito en el MOVIMIENTO DE VENTAS
 * @modified 2007-05-08 Se modifica el manejo de los descuentos tanto para las ventas como para las notas crédito, para lo cual se crean las variables $desiv, $ncdes, $ncdesiv
 * @modified 2007-05-07 Se crea la tabla de recibos para mejorar los dos queries que estaban causando problemas.
 * @modified 2007-05-07 Todas las tablas creadas se cambian a TEMPORARY y por lo tabnto desaparecen los DROP TABLE...
 * @modified 2007-05-07
 * @modified 2007-03-05 en la impresión de los anticipos tanto para cuotas moderadoras como para copagos se imprimian los copagos, se modifica.
 */

$AzulClar="006699";  //Azul claro
$blanco="FFFFFF"; //Blanca CON FONDO Azul claro
$AmarClar="#FFDBA8";	//Amarillo quemado claro
$AguaOsc="#57C8D5";	//Aguamarina Oscuro
$AmarQuem="#FFCC66";

encabezado( " COMPROBANTE DE INFORME DIARIO ", $wactualiz, "logo_".$tipo_tablas );
echo "<center><table border width='350'>";
/*echo "<tr><td align=center colspan='2' ><img src='/matrix/images/medical/POS/logo_".$tipo_tablas.".png' WIDTH=388 HEIGHT=70></td></tr>";

echo "<tr><td align=center colspan='2' bgcolor=".$AzulClar."><font size=3 text color=#FFFFFF><b>COMPROBANTE DE INFORME DIARIO </b></font></td></tr>";*/

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

	echo "<form name='forma' method='POST' action=''>";
	echo "<input type='HIDDEN' name= 'tipo_tablas' value='".$tipo_tablas."'>";

	/*Buscar la caja*/
	if(!isset($wcco))
	{
		$q =  " SELECT cjecco, cjecaj   "
		."   FROM ".$tipo_tablas."_000030 "
		."  WHERE cjeusu = '".substr($user,2)."'"
		."    AND cjeest = 'on' ";
		$res = mysql_query($q,$conex);
		$num = mysql_num_rows($res);
		if ($num > 0)
		{
			$row = mysql_fetch_array($res);

			$pos = strpos($row[0],"-");
			$wcco = substr($row[0],0,$pos);
			$wnomcco = substr($row[0],$pos+1,strlen($row[0]));
			echo "<input type='HIDDEN' name= 'wcco' value='".$wcco."'>";
			echo "<input type='HIDDEN' name= 'wnomcco' value='".$wnomcco."'>";

			$wcaja=$row[1];
			echo "<input type='HIDDEN' name= 'wcaja' value='".$wcaja."'>";
		}
	}

	echo "<div align='center'><span class='subtituloPagina2'><font size='2'>$wnomcco - $wcaja</font></span></div>";

	/*echo "<tr class='encabezadotabla'><td align=center colspan='2'><font size=3 text color=#FFFFFF><b>$wnomcco</b></font></td></tr>";
	echo "<tr class='encabezadotabla'><td align=center colspan='2'><font size=3 text color=#FFFFFF><b>$wcaja</b></font></td></tr>";*/

	if (!isset($fecha))
	{
		echo "</table><BR><BR><center><table>";
		echo "<tr><td align=left class='encabezadotabla'>FECHA A GENERAR:</td>";
		echo "<td align=center  class='fila2'><input type='text' name='fecha' id='fecha' size='9'></td></table>";
		echo "</table><BR><BR><center><table border='0' width='390'>";
		echo "<td align=center  ><input type='submit' name='aceptar' value='ACEPTAR'></font></td></table>";

	}else {
		//$time1=mktime(date('H'), date('i'), date('s'), date('m'), date('d'),date('Y'));
		/****************************************************************************************
		*										AQUI EMPIEZA EL REPORTE							*
		*****************************************************************************************	*/
		$ini=explode("-",$wcaja);
		$wcaja=$ini[0];
		$wnomcaj=$ini[1];

		$q="SELECT Ccofrc, Ccofnc, Ccopfa, Ccopfm "
		."FROM ".$tipo_tablas."_000003 "
		."WHERE Ccocod	= '".$wcco."' "
		."     AND Ccoest	= 'on'";
		$err1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($err1);
		if ($num1 > 0)
		{
			$row1=mysql_fetch_row($err1);
			$Ccofre=$row1[0];
			$Ccofnc=$row1[1];
			$Ccopfa=$row1[2];
			$Ccopfm=$row1[3];
		}

		/**
		 * Todas las ventas efectuadas en esa caja el día elegido por el ususrio
		 */
		$ventas="ventas_".date("Hms");
		$q="CREATE TEMPORARY TABLE IF NOT EXISTS  $ventas AS "
		."SELECT * "
		."  FROM ".$tipo_tablas."_000016 "
		." WHERE Vencco = '".$wcco."' "
		."   AND Vencaj = '".$wcaja."' "
		."   AND Fecha_data = '$fecha' "
		."   AND Venest = 'on' ";
		$err = mysql_query($q,$conex);
		echo mysql_error();


		$q= 	 "SELECT COUNT(*),MAX(Vennum),MIN(Vennum) "
				."  FROM ".$tipo_tablas."_000016 "
				." WHERE Vencco = '".$wcco."' "
				."   AND Vencaj = '".$wcaja."' "
				."   AND Fecha_data = '$fecha' "
				."   AND Venest = 'on' ";
				$err = mysql_query($q,$conex);
		echo mysql_error();
		$num = mysql_num_rows($err);
		if ($num > 0) {
			$row=mysql_fetch_row($err);
			$vent['venta']['num']=$row[0];
			if($row[0]==0){
				$vent['venta']['fin']="NINGUNO";
				$vent['venta']['ini']="NINGUNO";
			}else{
				$vent['venta']['fin']=$row[1];
				$vent['venta']['ini']=$row[2];
			}
		}



		/**
		 * Nueva tabla a 2007-05-08
		 * Se crea la tabla para disminuir los tiempos de respuesta del reposrte.
		 */
		$recibos="recibos_".date("Hms");
		$q="CREATE TEMPORARY TABLE IF NOT EXISTS  ".$recibos." AS "
		."SELECT Vennum, Rdenum, Rdefue, Rdecco, Rdeest "
		."  FROM ".$ventas.", ".$tipo_tablas."_000021 "
		." WHERE Rdevta = Vennum "
		."   AND Rdefue = '".$Ccofre."' "
		."   AND Rdecco = '".$wcco."' "
		."   AND Rdeest = 'on' ";
		$err = mysql_query($q,$conex);
		echo mysql_error();





		/****TRANSACCIONES*****/

		//Muestra los consecutivos y la sumatoria de los tipos de transacciones.


		/*Info de las facturas Automaticas*/
		$q="SELECT COUNT(*),MAX(Fenfac),MIN(Fenfac) "
		." FROM ".$tipo_tablas."_000018, $ventas "
		."WHERE	".$tipo_tablas."_000018.Fecha_data='$fecha' "
		."     AND 	Fenest = 'on' "
		."     AND	Substring(Fenfac,1,".strlen($Ccopfa).") = '".$Ccopfa."' "//2007-08-16
		."     AND 	Vennfa = Fenfac  "		//Número de la factura
		."     AND	Venffa = Fenffa "		//Fuente de la factura
		."     AND	Venest = 'on' ";
		$err = mysql_query($q,$conex);
		//		echo mysql_errno()."=".mysql_error();
		$num = mysql_num_rows($err);
		if ($num > 0) {
			$row=mysql_fetch_row($err);
			$vent['fact']['num']=$row[0];
			if($row[0]==0){
				$vent['fact']['fin']="NINGUNO";
				$vent['fact']['ini']="NINGUNO";
			}else{
				$vent['fact']['fin']=$row[1];
				$vent['fact']['ini']=$row[2];
			}
		}

		/*Info de las facturas MANUALES*/
		$q="SELECT COUNT(*),MAX(Fenfac),MIN(Fenfac) "
		."FROM ".$tipo_tablas."_000018, $ventas "
		."WHERE	".$tipo_tablas."_000018.Fecha_data='$fecha' "
		."     AND 	Fenest = 'on' "
		."     AND	Substring(Fenfac,1,".strlen($Ccopfm).") = '".$Ccopfm."' "
		."     AND 	Vennfa = Fenfac  "		//Número de la factura
		."     AND	Venffa = Fenffa "		//Fuente de la factura
		."     AND	Venest = 'on' ";
		$err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);
		if ($num > 0) {
			$row=mysql_fetch_row($err);
			$vent['facM']['num']=$row[0];
			if($row[0] == 0){
				$vent['facM']['fin']="NINGUNO";
				$vent['facM']['ini']="NINGUNO";
			}else{
				$vent['facM']['fin']=$row[1];
				$vent['facM']['ini']=$row[2];
			}
		}

		/*Información de los recibos*/
		$vent['rec']['num']="NINGUNO";
		$vent['rec']['fin']="NINGUNO";
		$vent['rec']['ini']="NINGUNO";
		$q="SELECT COUNT(*),MAX(Rennum),MIN(Rennum) "
		."    FROM ".$recibos.", ".$tipo_tablas."_000020 "
		."   WHERE ".$tipo_tablas."_000020.Fecha_data='$fecha' "
		."     AND Rennum = Rdenum "
		."     AND Renfue = '".$Ccofre."' "
		."     AND Rencco = '".$wcco."' "
		."     and Renest = 'on' ";
		$err = mysql_query($q,$conex);
		echo mysql_error();
		$num = mysql_num_rows($err);
		if ($num > 0) {
			$row=mysql_fetch_row($err);
			$vent['rec']['num']=$row[0];
			if($row[0] != 0) {
				$vent['rec']['fin']=$row[1];
				$vent['rec']['ini']=$row[2];
			}
		}



		/*Movimientos de inventario de devolución*/

		$mov3="mov3_".date("Hms");
		$q="CREATE TEMPORARY TABLE IF NOT EXISTS  $mov3 as "
		."SELECT Mendoc , Mendan, ".$tipo_tablas."_000010.Fecha_data , Vennum as Vendev, Venest, Ventcl,Venvto,Vencmo,Vencop "
		."FROM ".$tipo_tablas."_000010  , ".$tipo_tablas."_000016 "
		."WHERE	".$tipo_tablas."_000010.Fecha_data='$fecha' "
		."     AND	Mencon = '801' "
		."     AND	Mencco = '".$wcco."' "
		."     AND	Menest = 'on' "
		."     AND	Vennmo = Mendan "
		."     AND	Vennum = Menfac "
		."     AND	Vencaj = '".$wcaja."' "
		."     AND	Vencco = '".$wcco."' ";
		$err = mysql_query($q,$conex);

		/*Información Notas Crédito**/
		$vent['nc']['num']="NINGUNO";
		$vent['nc']['fin']="NINGUNO";
		$vent['nc']['ini']="NINGUNO";
		$q=" SELECT COUNT(*),MAX(Rennum),MIN(Rennum) "
		."FROM $mov3, ".$tipo_tablas."_000021, ".$tipo_tablas."_000020 "
		."WHERE	Rdevta = Mendoc "
		."     AND	Rdefue = '".$Ccofnc."' "
		."     AND	Rdecco = '".$wcco."' "
		."     AND	Renfue = '".$Ccofnc."' "
		."     AND	Rencco = '".$wcco."' "
		."     AND	Renest = 'on' "
		."     AND	Rdefue = Renfue "
		."     AND	Rdenum = Rennum "
		."     AND	Rdeest = 'on' ";
		$err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);
		if ($num > 0) {
			$row=mysql_fetch_row($err);
			$vent['nc']['num']=$row[0];
			if($row[0] != 0) {
				$vent['nc']['fin']=$row[1];
				$vent['nc']['ini']=$row[2];
			}
		}

		/*Información notas Devolucion*/

		$q="SELECT COUNT(*),MAX(Mendoc),MIN(Mendoc) "
		."FROM $mov3 ";
		$err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);
		if ($num > 0) {
			$row=mysql_fetch_row($err);
			$vent['dev']['num']=$row[0];
			if($row[0] != 0) {
				$vent['dev']['fin']=$row[1];
				$vent['dev']['ini']=$row[2];
			}else{
				$vent['dev']['fin']="NINGUNO";
				$vent['dev']['ini']="NINGUNO";
			}
		}

		/*Información anulaciones*/
		$q="SELECT COUNT(*),MAX(Vendev),MIN(Vendev) "
		."FROM $mov3 "
		."WHERE Venest = 'off' ";
		$err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);
		if ($num > 0) {
			$row=mysql_fetch_row($err);
			$vent['anu']['num']=$row[0];
			if($row[0] != 0) {
				$vent['anu']['fin']=$row[1];
				$vent['anu']['ini']=$row[2];
			}else{
				$vent['anu']['fin']="NINGUNO";
				$vent['anu']['ini']="NINGUNO";
			}
		}


		/****************RESUMEN DE VENTAS*******************/

		/*IVA de las Ventas*/
		/**
		 * En la tabla de ventas
		 * Vdevun: valor unitario de las ventas contiene iva
		 * Vdedes: valor total del descuento no titne iva
		 */

		$des=0;
		$desiv=0;
		$sub['ven']= 0;
		$sub['piv']= 0;
		$sub['tot']= 0;
		$q = " SELECT Vdepiv, SUM(Vdecan*Vdevun), SUM(Vdecan*Vdevun/(Vdepiv+100)), SUM(Vdedes), SUM(Vdedes*Vdepiv/100) "
		."FROM $ventas, ".$tipo_tablas."_000017 "
		."WHERE	Vdenum = Vennum "
		."GROUP BY Vdepiv ";
		$err = mysql_query($q,$conex);
		//		echo mysql_errno()."=".mysql_error();
		$numPiv = mysql_num_rows($err);
		if ($numPiv > 0) {
			for($i=0;$i<$numPiv;$i++){
				$row=mysql_fetch_row($err);
				if($row[0] == 0)
				{
					$vent1[$i]['piv']="Ventas Excluidas";
				}
				else
				{
					$vent1[$i]['piv']="Ventas Gravadas ".$row[0]."%";
				}

				$vent1[$i]['vtot']=$row[1];	// Con iva
				$vent1[$i]['vsiv']=round($row[2]*100); //Sin iva


				$sub['ven']= $sub['ven']+$vent1[$i]['vsiv'];
				$sub['piv']= $sub['piv']+$vent1[$i]['vtot']-$vent1[$i]['vsiv'];
				$sub['tot']= $sub['tot']+$vent1[$i]['vtot'];

				$des=$des+$row[3];//descuento sin iva
				$desiv=$desiv+$row[4];//descuento con iva
			}
		}
		$desiv=round($desiv);

		/*IVA de las Notas Crédito*/

		$mov0="mov0_".date("Ymds");
		$q="CREATE TEMPORARY TABLE IF NOT EXISTS $mov0 AS "
		."SELECT Rdevta,Rdefac,Renfue, Rennum  "
		."FROM ".$tipo_tablas."_000020, ".$tipo_tablas."_000021 "
		."WHERE	".$tipo_tablas."_000020.Fecha_data = '$fecha' "
		."     AND	Renfue = '".$Ccofnc."' "
		."     AND	Rencco = '$wcco' "
		."     AND	Renest = 'on' "
		."     AND	Rdenum = Rennum "
		."     AND	Rdefue = '".$Ccofnc."' "
		."     AND	Rdeest = 'on' ";
		$err=mysql_query($q,$conex);


		$mov="mov_".date("Ymds");
		$q="CREATE TEMPORARY TABLE IF NOT EXISTS $mov AS "
		."SELECT Mendoc, Vendev as Vennum, Renfue, Rennum, ".$mov3.".Fecha_data "
		."FROM $mov0,".$mov3." "//, ".$tipo_tablas."_000016 "
		."WHERE	Mendoc = Rdevta ";
		$err=mysql_query($q,$conex);


		$mov1="mov1_".date("Ymds");
		$q="CREATE TEMPORARY TABLE IF NOT EXISTS $mov1 AS "
		."SELECT Vennum, Mdeart, Mdecan "
		."FROM	$mov, ".$tipo_tablas."_000011 "
		."WHERE	Mdedoc = Mendoc "
		."     AND	Mdecon = '801' "
		."     AND	Mdeest = 'on' ";
		$err=mysql_query($q,$conex);



		$mov2="mov2_".date("Ymds");
		$q="CREATE TEMPORARY TABLE IF NOT EXISTS $mov2 AS "
		."SELECT Mdecan, Vdevun, Vdepiv, Vdedes, Vdenum, Vdecan  "
		."FROM	$mov1, ".$tipo_tablas."_000017 "
		."WHERE Vdenum = Vennum "
		."     AND	Vdeart = Mdeart "
		."     AND	Vdeest = 'on' ";
		$err=mysql_query($q,$conex);


		$ncdes=0;
		$ncdesiv=0;
		$ncsub['ven']= 0;
		$ncsub['piv']= 0;
		$ncsub['tot']= 0;

		$q="SELECT Vdepiv, SUM(Mdecan*Vdevun), SUM(Mdecan*Vdevun/(Vdepiv+100)), SUM((Vdedes/Vdecan)*Mdecan), SUM(((Vdedes/Vdecan)*Mdecan)*Vdepiv/100) "
		."FROM $mov2 "
		."GROUP BY Vdepiv ";
		$err = mysql_query($q,$conex);
		$numNC=mysql_num_rows($err);
		if($numNC >0){
			for($i=0;$i<$numNC;$i++){
				$row=mysql_fetch_row($err);
				if($row[0] == 0)
				$nc[$i]['piv']="Notas Crédito Excluidas";
				else
				$nc[$i]['piv']="Notas Crédito Gravadas ".$row[0]."%";
				$nc[$i]['vtot']=round($row[1]);//con iva
				$nc[$i]['vsiv']=round($row[2])*100;//sin iva

				$ncsub['ven']= $ncsub['ven']+$nc[$i]['vsiv'];
				$ncsub['piv']= $ncsub['piv']+($nc[$i]['vtot']-$nc[$i]['vsiv']);
				$ncsub['tot']= $ncsub['tot']+$nc[$i]['vtot'];

				$ncdes=$ncdes+round($row[3]);//sin iva
				$ncdesiv=$ncdesiv+round($row[4]);//con iva
			}
		}

		/****************MOVIMIENTO DE ANTICIPOS*******************/

		$q="SELECT SUM(Vencop),SUM(Vencmo) "
		."FROM $ventas ";
		$err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);
		if($num > 0) {
			$row=mysql_fetch_row($err);
			$copag=$row[0];
			$cumod=$row[1];
			$totant=$row[0]+$row[1];
		}


		/****************MOVIMIENTO DE CAJA*******************/

		/*Formas de Pago*/
		$totcon=0;
		$totabo=0;
		$totcre=0;

		$q="SELECT SUM(Vencop+Vencmo),SUM(Venvto-Vencmo-Vencop) "
		."FROM $ventas "
		."WHERE Ventcl <>'01-PARTICULAR' ";
		$err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);
		if($num > 0){
			$row=mysql_fetch_row($err);
			//		echo "copagos:".$row[0]."<br>Cuuota mod:".$row[1]."<br>";
			$totabo=$row[0];
			$totcre=$row[1];
		}


		$q="SELECT SUM(Venvto) "
		."FROM $ventas "
		."WHERE Ventcl = '01-PARTICULAR' ";
		$err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);
		if($num > 0){
			$row=mysql_fetch_row($err);
			$totcon=$row[0];
		}

		/**
		 * Resta de lo correspondiente a las NC
		 */

		$q = " SELECT SUM(Vencop+Vencmo),SUM(Venvto-Vencmo-Vencop) "
		."       FROM ".$mov3." "//.", ".$tipo_tablas."_000016 "
		."      WHERE Ventcl <>'01-PARTICULAR' ";
		$err = mysql_query($q,$conex);
		//	echo mysql_error();
		$num = mysql_num_rows($err);
		if($num > 0) {
			$row=mysql_fetch_row($err);
			$totabo=$totabo-$row[0];
			$totcre=$totcre-$row[1];
		}

		$formas="formas_".date("Ymds");
		$q="CREATE TEMPORARY TABLE IF NOT EXISTS $formas AS "
		."SELECT * "
		."FROM ".$mov3." "
		."WHERE Ventcl = '01-PARTICULAR' ";
		$err = mysql_query($q,$conex);


		$q = " SELECT Renvca "
		."       FROM $formas, ".$tipo_tablas."_000021, ".$tipo_tablas."_000020 "
		."      WHERE Rdevta = Mendoc "
		."        AND Rdefue = '".$Ccofnc."' "
		."        AND Rdecco = '".$wcco."' "
		."        AND Renfue = '".$Ccofnc."' "
		."        AND Rencco = '".$wcco."' "
		."        AND Renest = 'on' "
		."        AND Rdefue = Renfue "
		."        AND Rdenum = Rennum "
		."        AND Rdeest = 'on' ";
		$err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);
		if ($num > 0) {
			//2007-05-04 No existía el for lo que creaba un error en el reporte, se crea el for.
			for($i=0;$i<$num;$i++)
			{
				$row=mysql_fetch_row($err);
				$totcon=$totcon-$row[0];
			}

		}


		/****************MEDIOS DE PAGO*******************/

		/**
		 * Recibos de ventas
		 */

		$q="SELECT Fpades,SUM(Rfpvfp),COUNT(*) "
		."FROM ".$recibos.", ".$tipo_tablas."_000022, ".$tipo_tablas."_000023 "
		."WHERE	Rfpfue = '$Ccofre' "
		."     AND	Rfpnum = Rdenum "
		."     AND	Rfpcco = '$wcco' "
		."     AND	Rfpest = 'on' "
		."     AND	Fpacod = Rfpfpa "
		."GROUP BY Rfpfpa "
		."ORDER BY Fpades";
		$err = mysql_query($q,$conex);
		echo mysql_error();
		$numFP=mysql_num_rows($err);
		if($numFP >0){
			for($i=0;$i<$numFP;$i++){
				$row=mysql_fetch_row($err);
				$fp['fpa'][$i]=$row[0];	//Descripcion de la forma de pago
				$fp['val'][$i]=$row[1]; //Valor de la forma de pago
				$fp['tra'][$i]=$row[2];	//Número de recibos con esa forma de pago
				$sumaFpa=$fp['val'][$i];
			}
		}

		/**
		 * Recibos de nc
		 */
		if($numNC >0)
		{
			$q = " SELECT Fpades, SUM(Rfpvfp),COUNT(*)  "
			."       FROM ".$mov.",".$tipo_tablas."_000022, ".$tipo_tablas."_000023 "
			."      WHERE Rfpfue = Renfue "
			."        AND Rfpnum = Rennum "
			."        AND Rfpcco = '$wcco' "
			."        AND Rfpest = 'on' "
			."        AND Fpacod = Rfpfpa "
			."   GROUP BY Rfpfpa "
			."   ORDER BY Fpades";
			$err = mysql_query($q,$conex);
			$num=mysql_num_rows($err);
			if($num >0) {
				for($j=0;$j<$num;$j++){
					$igual=0;$p=0;
					$row=mysql_fetch_row($err);
					do
					{
						if(isset($fp['fpa'][$p]) and $fp['fpa'][$p] == $row[0])
						{
							$fp['val'][$p]=$fp['val'][$p]-$row[1];
							$fp['tra'][$p]=$fp['tra'][$p]+$row[2];
							$igual=1;
						}
						$p++;
					} while($p<$numFP and $igual == 0);

					if($igual==0)
					{
						$fp['fpa'][$numFP]=$row[0];
						$fp['val'][$numFP]=-$row[1];
						$fp['tra'][$numFP]=$row[2];
						$numNC=1;
						$numFP++;//2007-05-04 Cuando no habia ventas no estaba imprimiendo pues no habia formas de pago.
					}
				}
			}
		}
		/**
		  * 2007-05-08
		  * CORRECCIÓN DE REDONDEO DE IVA EN LOS DESCUENTOS
		  *
		  * No contempla si las notas credito tienen problema con los redondeos,
		  *
		  */

		$totVentas=$totabo+$totcon+$totcre;
		$diferencia= $totVentas - ($sub['tot']-$des-$desiv -($ncsub['tot']-($ncdes+$ncdesiv)));
		if($desiv>0)
		{
			$desiv=$desiv-$diferencia;
		}

		/*****************************************************************************************
		*							IMPRESIÓN DEL REPORTE
		******************************************************************************************/
		echo "</table><BR><center><table border='0'  align='center'>";
		echo "<tr><td><font size=2 text ><b>FECHA HORA DE INICIO: $fecha 00:00:00</b></td></tr>";
		echo "<tr><td><font size=2 text ><b>FECHA HORA DE INICIO: $fecha 23:59:59</b></td></tr>";
		echo "<tr><td align='center'>";
		echo "<table border='1'>";
		echo "<tr><td  align='center' colspan='4' class='encabezadotabla'>MOVIMIENTO DE VENTAS</td></tr>";
		echo "<tr class='botona' align='center'><td><b>NUMERACIÓN DE DOCUMENTOS</td>";
		echo "<td><b>INICIAL</b></td>";
		echo "<td><b>FINAL</b></td>";
		echo "<td><b>TOTAL</b></td></tr>";


		echo "<tr class='fila2'><td>Ventas</td>";
		echo "<td>".$vent["venta"]["ini"]."</td>";
		echo "<td>".$vent["venta"]["fin"]."</td>";
		echo "<td>".$vent["venta"]["num"]."</td></tr>";


		echo "<tr class='fila2'><td>Factura Automática</td>";
		echo "<td>".$vent["fact"]["ini"]."</td>";
		echo "<td>".$vent["fact"]["fin"]."</td>";
		echo "<td>".$vent["fact"]["num"]."</td></tr>";


		echo "<tr class='fila2'><td>Factura Manual</td>";
		echo "<td>".$vent["facM"]["ini"]."</td>";
		echo "<td>".$vent["facM"]["fin"]."</td>";
		echo "<td>".$vent["facM"]["num"]."</td></tr>";


		echo "<tr class='fila2'><td>Recibo</td>";
		echo "<td>".$vent["rec"]["ini"]."</td>";
		echo "<td>".$vent["rec"]["fin"]."</td>";
		echo "<td>".$vent["rec"]["num"]."</td></tr>";


		echo "<tr class='fila2'><td>Nota Crédito</td>";
		echo "<td>".$vent["nc"]["ini"]."</td>";
		echo "<td>".$vent["nc"]["fin"]."</td>";
		echo "<td>".$vent["nc"]["num"]."</td></tr>";

		echo "<tr class='fila2'><td>Devolución</td>";
		echo "<td>".$vent["dev"]["ini"]."</td>";
		echo "<td>".$vent["dev"]["fin"]."</td>";
		echo "<td>".$vent["dev"]["num"]."</td></tr>";

		echo "<tr class='fila2'><td>Anulación</td>";
		echo "<td>".$vent["anu"]["ini"]."</td>";
		echo "<td>".$vent["anu"]["fin"]."</td>";
		echo "<td>".$vent["anu"]["num"]."</td></tr>";



		echo "<tr class='botona'><td>RESUMEN DE VENTAS</td>";
		echo "<td><b>VENTA</b></td>";
		echo "<td><b>IVA</b></td>";
		echo "<td><b>TOTAL</b></td></tr>";


		if ($numPiv > 0) {

			for($i=0;$i<$numPiv;$i++){
				echo "<tr class='fila2'><td>".$vent1[$i]['piv']."</td>";
				echo "<td>$".number_format(($vent1[$i]['vsiv']),2,".",",")." </td>";
				echo "<td>$".number_format($vent1[$i]['vtot']-$vent1[$i]['vsiv'],2,".",",")."</td>";
				echo "<td>$".number_format($vent1[$i]['vtot'],2,".",",")."</td>";

			}
			echo "<tr class='fila2'><td>-Descuentos</td>";
			echo "<td>$".number_format($des,2,".",",")."</td>";
			echo "<td>$".number_format($desiv,2,".",",")."</td>";
			echo "<td>$".number_format(($des+$desiv),2,".",",")."</td></tr>";

			$sub['ven']= $sub['ven']-$des;
			$sub['piv']= $sub['piv']-$desiv;
			$sub['tot']= $sub['tot']-($des+$desiv);

			echo "<tr class='botona'><td><b>Subtotal</td>";
			echo "<td><b>$".number_format($sub['ven'],2,".",",")."</b></td>";
			echo "<td><b>$".number_format($sub['piv'],2,".",",")."</b></td>";
			echo "<td><b>$".number_format($sub['tot'],2,".",",")."</b></td></tr>";
		}


		if ($numNC > 0)
		{
			for($i=0;$i<$numNC;$i++)
			{
				echo "<tr><td>".$nc[$i]['piv']."</td>";
				echo "<td>$".number_format($nc[$i]['vsiv'],2,".",",")."</td>";
				echo "<td>$".number_format(($nc[$i]['vtot']-$nc[$i]['vsiv']),2,".",",")."</td>";
				echo "<td>$".number_format($nc[$i]['vtot'],2,".",",")."</td>";
			}

			echo "<tr><td>-Descuentos</td>";
			echo "<td>$".number_format($ncdes,2,".",",")."</td>";
			echo "<td>$".number_format(($ncdesiv),2,".",",")."</td>";
			echo "<td>$".number_format(($ncdes+$ncdesiv),2,".",",")."</td></tr>";

			$ncsub['ven']= $ncsub['ven']-$ncdes;
			$ncsub['piv']= $ncsub['piv']-$ncdesiv;
			$ncsub['tot']= $ncsub['tot']-($ncdesiv+$ncdes);

			echo "<tr><td bgcolor='".$AmarClar."'><b>Subtotal</td>";
			echo "<td bgcolor='".$AmarClar."'><b>$".number_format($ncsub['ven'],2,".",",")."</b></td>";
			echo "<td bgcolor='".$AmarClar."'><b>$".number_format($ncsub['piv'],2,".",",")."</b></td>";
			echo "<td bgcolor='".$AmarClar."'><b>$".number_format($ncsub['tot'],2,".",",")."</b></td></tr>";
		}



		echo "<tr class='botona'><td><b>TOTAL VENTAS</td>";
		echo "<td><b>$".number_format($sub['ven']-$ncsub['ven'],2,".",",")."</b></td>";
		echo "<td><b>$".number_format($sub['piv']-$ncsub['piv'],2,".",",")."</b></td>";
		echo "<td><b>$".number_format($sub['tot']-$ncsub['tot'],2,".",",")."</b></td></tr>";

		echo "<tr class='encabezadotabla'><td  align='center' colspan='4' >MOVIMIENTO DE ANTICIPOS</td></tr>";

		echo "<tr class='fila2'><td colspan='3'>Coopagos</td>";
		echo "<td>$".number_format($copag,2,".",",")."</td>";

		echo "<tr class='fila2'><td colspan='3'>Cuotas moderadoras</td>";
		echo "<td>$".number_format($cumod,2,".",",")."</td>";//2007-03-05

		echo "<tr class='botona'><td colspan='3'>TOTAL ANTICIPOS</td>";
		echo "<td>$".number_format($totant,2,".",",")."</td>";

		echo "<tr class='encabezadotabla'><td  align='center' colspan='4'>MOVIMIENTO DE CAJA</td></tr>";

		echo "<tr class='botona'><td colspan='4'>FORMAS DE PAGO</td>";

		echo "<tr class='fila2'><td colspan='3'>Abonos a Cartera Por Beneficiario</td>";
		echo "<td>$".number_format($totabo,2,".",",")."</td>";

		echo "<tr class='fila2'><td colspan='3'>Ventas de Contado</td>";
		echo "<td>$".number_format($totcon,2,".",",")."</td>";

		echo "<tr class='botona'><td colspan='3'>Total Ventas de Contado</td>";
		echo "<td>$".number_format(($totabo+$totcon),2,".",",")."</td>";

		echo "<tr class='fila2'><td colspan='3'>Ventas a Crédito</td>";
		echo "<td>$".number_format($totcre,2,".",",")."</td>";


		echo "<tr class='botona'><td colspan='3'>TOTAL FORMAS DE PAGO</td>";
		echo "<td>$".number_format(($totabo+$totcon+$totcre),2,".",",")."</td>";
		//echo "<td bgcolor='".$AmarClar."'><b>$".round($totabo+$totcon+$totcre)."</b></td>";

		echo "<tr class='encabezadotabla'><td colspan='2'>MEDIOS DE PAGO</td>";
		echo "<td colspan='1'># TRANSACCIONES</td>";
		echo "<td colspan='1'>TOTAL</td>";
		$totfp=0;
		$tottra=0;
		if ($numFP > 0) {
			for($i=0;$i<$numFP;$i++){
				echo "<tr class='fila2'><td colspan='2'>".ucfirst(strtolower($fp['fpa'][$i]))."</td>";
				echo "<td align='center'>".$fp['tra'][$i]."</td>";
				echo "<td>$".number_format($fp['val'][$i],2,".",",")."</td></tr>";
				$tottra=$tottra+$fp['tra'][$i];
				$totfp=$totfp+$fp['val'][$i];
			}
		}
		echo "<tr class='botona'><td colspan='2'>TOTAL VENTAS</td>";
		echo "<td align='center'>".$tottra."</td>";
		echo "<td>$".number_format($totfp,2,".",",")."</td>";
		//echo "<td bgcolor='".$AmarClar."'><b>$".round($totfp)."</b></td>";
		echo "</td></tr>";
		echo "</table><br>";
		echo "<center><input type='button' value='Retornar' onclick='retornar(\"comprobante.php?tipo_tablas=".$tipo_tablas."\" )'></center>";
		/*	$time1=mktime(date('H'), date('i'), date('s'), date('m'), date('d'),date('Y')) -$time1;

		$q= "INSERT INTO ".$tipo_tablas."_000082 "
		."(medico,fecha_data,hora_data, Cco, Caja, Fecha, Tiempo, Seguridad ) "
		."VALUES ('".$tipo_tablas."', '".date('Y-m-d')."', '".date('H:i:s')."', '".$wcco."','".$wcaja."', '".$fecha."', ".$time1.", 'A-".substr($user,2,strlen($user))."')";
		$err1 = mysql_query($q,$conex);
		*/
	}
	echo "<br><center><input type=button id='cerrar_ventana' value='Cerrar Ventana' onClick='javascript:cerrarVentana()'></center>";
}
?>