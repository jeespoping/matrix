<html>
<head>
  <title>CUADRE CAJA - FARSTORE</title>
</head>
<body>

<?php
include_once("conex.php");

$wautor="Ana Maria Betancur V.";
$wcol=10;  //Numero de columnas que se tienen o se muestran en pantalla


$wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
$wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro



echo "<p align=right><font size=1><b>Autor: ".$wautor."</b></font></p>";

echo "<center><table border width='350'>";
echo "<tr><td align=center colspan='2' ><img src='/matrix/images/medical/farmastore/logo farmastore.png' WIDTH=388 HEIGHT=70></td></tr>";
echo "<tr><td align=center colspan='2' bgcolor=".$wcf2."><font size=3 text color=#FFFFFF><b>REPORTES DE VENTAS  </b></font></td></tr>";
echo "<tr><td align=center colspan='2' bgcolor=".$wcf2."><font size=3 text color=#FFFFFF><b>CUADRE DE CAJA </b></font></td></tr>";
echo "<tr><td align=center colspan='2' bgcolor=".$wcf2."><font size=3 text color=#FFFFFF><b>$wcaja</b></font></td></tr>";
echo "</table>";

$pos = strpos($user,"-");
$wusuario = substr($user,$pos+1,strlen($user));


/**
* @return void
* @param Matriz de 11xN $valor
* @param String $titulo
* @desc Muestra en panlatalla el contenido de Valor
*/
function Mostrar($valor, $titulo, $name) {

	global $wcf2 ;
	global $wclfg;

	$wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
	$wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro


	$wcf3="#FFDBA8";	//COLOR DEL FONDO 3 PARA RESALTAR -- Amarillo quemado claro
	$wcf4="#A4E1E8";	//COLOR DEL FONDO 4 -- Aguamarina claro
	$wcf5="#57C8D5";	//COLOR DEL FONDO 5 -- Aguamarina Oscuro
	$wclam="#A4E1E8";	//COLOR DE LA LETRA -- Aguamarina Clara
	$wcfao="#FFCC66";



	echo "<table border='1' width='650'>";
	echo "<tr><td colspan='10' align='center' bgcolor=".$wcf2."><b><font text color=".$wclfa.">".$titulo."</font></b></TR>";
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
	for($i=0;$i<$num;$i++) {
		if($valor[$i][6] != $fp){
			$fp= $valor[$i][6];
			if($i != 0){
				//IMPRIMIRLOS RESULTADOS TOTALES
				echo "<tr><td align=left bgcolor=".$wcf3." colspan='5'><b><font text color=".$wclfg.">TOTAL </font></b>";
				echo "<td align='right' bgcolor=".$wcf3."><b><font color=".$wclfg.">".number_format($total,",","",".")."</td>";
				echo "<td align='right' bgcolor=".$wcf3."><b><font color=".$wclfg.">".number_format($anterior,",","",".")."</td>";
				echo "<td align='right' bgcolor=".$wcf3."><b><font color=".$wclfg.">".number_format($egresar,",","",".")."</td>";
				echo "<td align='right' bgcolor=".$wcf3."><b><font color=".$wclfg.">".number_format($nuevo,",","",".")."</td>";
				echo "<td align='right' bgcolor=".$wcf3."><b><font color=".$wclfg."></td>";
				$totTotal = $totTotal + $total;
				$totAnterior = $totAnterior + $anterior;
				$totEgresar = $totEgresar + $egresar;
				$totNuevo = $totNuevo + $nuevo;
				$total=0;
				$anterior=0;
				$egresar=0;
				$nuevo=0;
			}
			echo "<tr><td colspan='10' align='center' bgcolor=".$wcf5."><b><font text color=".$wclfg." >".$fp."-".$valor[$i][7]."</font></b></TR>";
			echo "<tr><td align=left bgcolor=".$wcf4."><b><font text color=".$wclfg.">";
			echo "<td align=left bgcolor=".$wcf4."><b><font text color=".$wclfg.">VENTA</tr>";
			echo "<td align=left bgcolor=".$wcf4."><b><font text color=".$wclfg.">FECHA</tr>";
			echo "<td align=left bgcolor=".$wcf4."><b><font text color=".$wclfg.">FACTURA</tr>";
			echo "<td align=left bgcolor=".$wcf4."><b><font text color=".$wclfg.">FUENTE-N° RECIBO</tr>";
			echo "<td align=left bgcolor=".$wcf4."><b><font text color=".$wclfg.">VALOR TOTAL</tr>";
			echo "<td align=left bgcolor=".$wcf4."><b><font text color=".$wclfg.">SALDO ANTERIOR</tr>";
			echo "<td align=left bgcolor=".$wcf4."><b><font text color=".$wclfg.">VALOR EGRESAR</tr>";
			echo "<td align=left bgcolor=".$wcf4."><b><font text color=".$wclfg.">NUEVO SALDO</tr>";
			echo "<td align=left bgcolor=".$wcf4."><b><font text color=".$wclfg.">EGRESAR</tr>";
		}

		$total = $total + $valor[$i][8];
		$anterior= $anterior + $valor[$i][9];

		/*Link a detalle recibo donde se detalla la venta*/
		echo "<tr><td align=left bgcolor=".$wclfa."><font text color=".$wcf2."><A HREF='detalle_recibo.php?Venfec=".$valor[$i][0]."&amp;Fenfec=".$valor[$i][1]."&amp;Rdefac=".$valor[$i][2]."&amp;Rdefue=".$valor[$i][3]."&amp;Rdenum=".$valor[$i][4]."&amp;Seguridad".$valor[$i][5]."]&amp;Cdefpa=".$valor[$i][6]."&amp;Fpades=".$valor[$i][7]."&amp;Rfpvfp=".$valor[$i][8]."&amp;Cdevrf=".$valor[$i][9]."&amp;Cdevrf=".$valor[$i][10]."' target='blank'>Info</a>";

		echo "<td align=left bgcolor=".$wclfa."><font text color=".$wcf2.">".$valor[$i][0]."</tr>";//# de Venta
		echo "<td align=left bgcolor=".$wclfa."><font text color=".$wcf2.">".$valor[$i][1]."</tr>";//Fecha Venta
		echo "<td align=left bgcolor=".$wclfa."><font text color=".$wcf2.">".$valor[$i][2]."</tr>";//# FActura
		echo "<td align=left bgcolor=".$wclfa."><font text color=".$wcf2.">".$valor[$i][3]."-".$valor[$i][4]."</tr>";//#de fuente y Recibo
		echo "<td align=right bgcolor=".$wclfa."><font text color=".$wcf2.">".number_format($valor[$i][8],",","",".")."</tr>";//Valor total de la Forma de Pacgo
		echo "<td align=right bgcolor=".$wclfa."><font text color=".$wcf2.">".number_format($valor[$i][9],",","",".")."</tr>";//Saldo del Cuadre de caja Anterior

		/*Saldo a Egresar
		Si es del tipo 99=> EFECTIVO Debe permitit digitar la cantidad que desea egresar
		En caso contrario la cantida no es digitada y corresponde al total del saldo Anterior
		*/
		if($valor[$i][6] != '99'){
			echo "<td align='right' bgcolor=".$wclfa."><font text color=".$wcf2.">".$valor[$i][10]."</tr>";
			echo "<input type='hidden' name='".$name."[".$i."][10]' value='".$valor[$i][10]."'>";
		}else{
			echo "<td align='center' bgcolor=".$wclfa."><font text color=".$wcf2."><input type='text' name='".$name."[".$i."][10]' value='".$valor[$i][10]."' size='4'></tr>";
		}




		if(isset($valor[$i][11])) {
			/*Esta Checkeado para egresar*/

			echo "<td align='right' bgcolor=".$wclfa."><font text color=".$wcf2.">".number_format(($valor[$i][9]-$valor[$i][10]),",","",".")."</tr>";
			echo "<td align='center' bgcolor=".$wclfa."><font text color=".$wcf2."><input type='CHECKBOX' name='".$name."[".$i."][11]' value='on' checked></tr>";
			$nuevo = $nuevo + ($valor[$i][9]-$valor[$i][10]); //Saldo anterior menos valor a egresar
			$egresar = $egresar + $valor[$i][10];
		}else{
			echo "<td align='right' bgcolor=".$wclfa."><font text color=".$wcf2.">".number_format($valor[$i][9],",","",".")."</tr>";
			echo "<td align='center' bgcolor=".$wclfa."><font text color=".$wcf2."><input type='CHECKBOX' name='".$name."[".$i."][11]' value='off'></tr>";
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


	}
	echo "<tr><td align=left bgcolor=".$wcf3." colspan='5'><b><font text color=".$wclfg.">TOTAL </font></b>";
	echo "<td align='right' bgcolor=".$wcf3."><b><font text color=".$wclfg.">".number_format($total,",","",".")."</td>";
	echo "<td align='right' bgcolor=".$wcf3."><b><font text color=".$wclfg.">".number_format($anterior,",","",".")."</td>";
	echo "<td align='right' bgcolor=".$wcf3."><b><font text color=".$wclfg.">".number_format($egresar,",","",".")."</td>";
	echo "<td align='right' bgcolor=".$wcf3."><b><font text color=".$wclfg.">".number_format($nuevo,",","",".")."</td>";
	echo "<td align='right' bgcolor=".$wcf3."><b><font text color=".$wclfg."></td>";
	$totTotal = $totTotal + $total;
	$totAnterior = $totAnterior + $anterior;
	$totEgresar = $totEgresar + $egresar;
	$totNuevo = $totNuevo + $nuevo;

	echo "<tr><td align=left bgcolor=".$wcfao." colspan='5'><b><font text color=".$wclfg.">TOTAL $titulo</font></b>";
	echo "<td align='right' bgcolor=".$wcfao."><b><font text color=".$wclfg.">".number_format($totTotal,",","",".")."</td>";
	echo "<td align='right' bgcolor=".$wcfao."><b><font text color=".$wclfg.">".number_format($totAnterior,",","",".")."</td>";
	echo "<td align='right' bgcolor=".$wcfao."><b><font text color=".$wclfg.">".number_format($totEgresar,",","",".")."</td>";
	echo "<td align='right' bgcolor=".$wcfao."><b><font text color=".$wclfg.">".number_format($totNuevo,",","",".")."</td>";
	echo "<td align='right' bgcolor=".$wcfao."><b><font text color=".$wclfg."></td>";
	echo "</table>";
}
//////////////////////////////////////

function grabar($valor,$conex,$wcaja,$wcco,$listo,$usu,$numCua) {

	if($numCua == "") {
		$q = "LOCK table farstore_000028 LOW_PRIORITY WRITE";
		$err = mysql_query($q,$conex);

		/*Traer el numero de cuadre */
		$q="SELECT Cajcua "
		."FROM farstore_000028 "
		."WHERE 	Cajcco = '".$wcco."' "
		."and 		Cajcod = '".$wcaja."'";
		$res = mysql_query($q,$conex);
		$row=mysql_fetch_array($res);
		$numCua= $row["Cajcua"]+1;

		/*Aumentar el numero de cuadre*/
		$q="UPDATE farstore_000028 "
		."SET Cajcua='".$numCua."' "
		."WHERE 	Cajcco = '".$wcco."' "
		."and 		Cajcod = '".$wcaja."'";
		$res = mysql_query($q,$conex);

		$q = " UNLOCK TABLES";
		$err = mysql_query($q,$conex);
	}
	$num = count($valor);
	//	echo "<br>num reg=$num<br>";
	$date = date("Y-m-d");
	$hour = date("h:m:i");
	for($i=0;$i < $num;$i++) {
		if(isset($valor[$i][11])){
			$vrf= $valor[$i][9] - $valor[$i][10];
		}else{
			$vrf=$valor[$i][9];
		}

		$q="INSERT INTO farstore_000037"
		."(medico, Fecha_data, Hora_data, Cdecua, Cdecaj, Cdecco, Cdefue, Cdenum, Cdefpa, Cdevrf, Cdeest, Seguridad)"
		."VALUES ('farstore', '$date','$hour','$numCua', '$wcaja', '$wcco', '".$valor[$i][3]."', '".$valor[$i][4]."', '".$valor[$i][6]."', '$vrf', 'on', 'A-$usu')";
		//		echo $q;
		$res = mysql_query($q,$conex);
		//		ECHO "<br><b>".mysql_errno()."=".mysql_error()."</b><br>";
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
			."FROM farstore_000030 "
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
			}
		}
	}
	echo $cajcua;
	if(!isset($valorAnt) and $cajcua != 0){
		//	Venfec 		: Fecha de la Venta 	-- farstore_000016 ENCAVEZADO VENTAS relaciona Venfac con Fenfac (000018)
		//	Fenfec 		: Fecha de la factura	-- farstore_000018 ENCABEZADO FACTURA relaciona Fenfac con Rdefac (000021)
		//	Rdefac 		: Numero de la factura	-- farstore_000021 DETALLE RECIBO
		//	Rdefue 		: Fuente del recibo		-- farstore_000021 DETALLE RECIBO se relaciona con Rfpfue (farstore_000022)
		//	Rdenum 		: Fuente del recibo		-- farstore_000021 DETALLE RECIBO se relaciona con Rfpnum (farstore_000022)
		//	Seguridad	: Usuario ing. recibo	-- farstore_000021 DETALLE RECIBO
		//	Cdefpa		: Código forma pago		-- farstore_000037 DETALLE CUADRE CAJA relaciona Cdefue,Cdenum,Cdefpa con
		//																				Rfpfue, Rfpnum, Rfpfpa (farstore_000022)
		//	Fpades		: Descrip. forma pago	-- farstore_000023 FORMAS DE PAGO relaciona Fpacod con Cdefpa (farstore_000037)
		//	Rfpvfp		: Valor forma de pago	-- farstore_000022 DETALLE RECIBO FORMA PAGO
		//	Cdevrf		: Saldo recibo en caja	-- farstore_000037 DETALLE CUADRE CAJA

		$q=	"SELECT Cdevrf, Cdefpa, Fpades, Rfpvfp, Rdefue, Rdenum, Rdefac, farstore_000022.Seguridad AS Seguridad, Vennum, Venfec "
		."FROM farstore_000037, farstore_000023, farstore_000022, farstore_000021, farstore_000016 "
		."WHERE	Cdeest	=	'on' "
		."and 	Cdecua	=	'".$cajcua."' "
		."and	Cdecaj	=	'".$wcaja."' "
		."and	Cdecco	=	'".$wcco."' "
		."and	Cdevrf	<>	0 "
		."and	Fpacod	=	Cdefpa "
		."and	Rfpest	=	'on' "
		."and	Rfpfue	=	Cdefue "
		."and	Rfpnum	=	Cdenum "
		."and	Rfpfpa	=	Cdefpa "
		."and	Rdeest	=	'on' "
		."and	Rdefue	=	Cdefue "
		."and	Rdenum	=	Cdenum "
		."and	Venest	=	'on' "
		."and	Vennum	=	Rdevta "
		."	ORDER BY Cdefpa ";
		$res = mysql_query($q,$conex);
		//	ECHO "<br><br><b>".mysql_errno()."=".mysql_error()."</b>";
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
				/*		echo "<br><br>[$i] 0=".$valorAnt[$i][0];
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
	if(isset($valorAnt)){
		echo "<br><br>";
		Mostrar(&$valorAnt,"SALDOS PENDIENTES","valorAnt");
	}

	if (!isset($valor)) {
		$table=date("Mdis");
		/*	$q= "Create table  IF NOT EXISTS ".$table." as "
		."SELECT * "
		."FROM farstore_000022 "
		."WHERE	Rfpfue	= '".$Ccofre."' "
		."and	Rfpcco	= '".$wcco."' "
		."and	NOT EXISTS (SELECT	Cdefue, Cdenum, Cdefpa "
		."					FROM	farstore_000037 "
		."					WHERE 	farstore_000037.Cdefue = farstore_000022.Rfpfue "
		."					and		farstore_000037.Cdenum = farstore_000022.Rfpnum "
		."					and		farstore_000037.Cdefpa = farstore_000022.Rfpfpa "
		."					and		farstore_000037.Cdeest = 'on' "
		."					and 	farstore_000037.Cdecaj = '".$wcaja."' "
		."					and 	farstore_000037.Cdecco = '".$wcco."' ) "
		."and	Rfpest = 'on' ";
		$res1 = mysql_query($q,$conex);*/

		$ini=explode("-",$wcaja);
		$cajcod=$ini[0];
		//		$q= "Create table  IF NOT EXISTS ".$table." as "
		$q="SELECT Rfpfpa, Rfpvfp, Fpades, Rdefue, Rdenum, Rdefac, farstore_000022.Seguridad AS Seguridad, Venfec, Vennum "
		."FROM farstore_000022, farstore_000023, farstore_000021, farstore_000016 "
		."WHERE Rfpfue	= '".$Ccofre."' "
		."and	Rfpcco	= '".$wcco."' "
		."and	NOT EXISTS (SELECT	Cdefue, Cdenum, Cdefpa "
		."					FROM	farstore_000037 "
		."					WHERE 	farstore_000037.Cdefue = farstore_000022.Rfpfue "
		."					and		farstore_000037.Cdenum = farstore_000022.Rfpnum "
		."					and		farstore_000037.Cdefpa = farstore_000022.Rfpfpa "
		."					and		farstore_000037.Cdeest = 'on' "
		."					and 	farstore_000037.Cdecaj = '".$wcaja."' "
		."					and 	farstore_000037.Cdecco = '".$wcco."' ) "
		."and	Rfpest = 'on' "
		."and	Fpacod	=	Rfpfpa "
		."and	Rdeest	=	'on' "
		."and	Rdecco	=	Rfpcco "
		."and	Rdefue	=	Rfpfue "
		."and	Rdenum	=	Rfpnum "
		."and	Venest	=	'on' "
		."and	Vennum	=	Rdevta "
		."and	Vencaj	=	'".$cajcod."' "
		."order by Rfpfpa, Fpades, Vennum";

//			echo $q."<br><br>";
		/**
		//**IMPRIMIR TODO LO DE $table*
		$q="SELECT * FROM $table";
		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);
		if($num1 > 0){
		echo "<table border=1>";
		for($i=0;$i<$num1;$i++) {
		$row1 = mysql_fetch_row($res1);

		$numo = mysql_num_fields($res1);
		//	echo "numo=$numo*<br>";
		echo "<tr>";
		for($j=0;$j < $numo;$j++) {
		echo "<td>".$row1[$j]."</td>";
		}
		echo "</tr>";
		}
		echo "</table>";
		}

		$q="SELECT Rfpfpa, Rfpvfp, Fpades, Rdefue, Rdenum, Rdefac, $table.Seguridad AS Seguridad, Venfec, Vennum "
		."FROM $table, farstore_000023, farstore_000021, farstore_000016 "
		."WHERE Fpacod	=	Rfpfpa "
		."and	Rdeest	=	'on' "
		."and	Rdecco	=	Rfpcco "
		."and	Rdefue	=	Rfpfue "
		."and	Rdenum	=	Rfpnum "
		."and	Venest	=	'on' "
		."and	Vennum	=	Rdevta "
		."order by Rfpfpa, Fpades";
		*/
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
			}
		}
		//	$query = "DROP table ".$table;
		//	$err = mysql_query($query,$conex);
	}
	echo "<br><br>";
	Mostrar(&$valor,"INGRESOS","valor");

	echo "<br><br><table width='350'>";
	if(!isset($listo) or $listo != "ok"){
		/*Aqui falta el manejo de errores del listo*/
		if(isset($pass)){
			echo "<tr><td colspan=2><font text color=".$wclfg."><b>PASSWORD PARA CUADRE DE CAJA:  <input type='password' name='pass' size=3></td>";
		}
		echo "<tr><td align='center'><font text color=".$wclfg."><b>CUADRAR CAJA<input type='checkbox' name='grabar' vaule='off' $checked ></td>";
	}
	else {
		echo "<input type='hidden' name='listo' value='$listo'>";
		echo "<input type='hidden' name='numCua' value='$numCua'>";
		echo "<td align='center'><a href='rep_cuadre.php?cuadre=$numCua&amp;wcco=$wcco&amp;wcaja=$wcaja-$wnomcaj' target='_blank'>Reporte del Cuadre</a></tr><tr>";
	}
	echo "<td align='center'><input type='submit' name='cuadre' value='ACEPTAR'></td></tr></table>";
	ECHO "</FORM>";

}?>

</body>
</html>