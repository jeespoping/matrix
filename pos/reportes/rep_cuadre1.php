<html>
<head>
  <title>REPORTE CUADRE CAJA - POS</title>
</head>
<body>

<?php
include_once("conex.php");

/**
 * REPORTE DONDE SE DETALLAN LOS CUADRES DE CAJA
 * 
 * Muestra el detalle de un cuadre en cuatro partes: sus saldos anteriores y los nuevos ingresos cada uno en tanto en recibos 
 * de caja (ingresos por venta) y en notas crédito. 
 *  
 * Adicionalmente muestra para cada elmento de las diferentes formas de pago si fue egresado o no, y el valor egresado durante el cuadre.
 * 
 * 
 *  
 * @author Ana María Betancur Vargas
 * @created 2005-08-01
 * @version 2005-02-07
 * 
 * @wvar String[4]	$wcco 		Codigo Centro de costos.
 * @wvar String		$wnomcco 	Nombre del centro de costos.
 * @wvar String		$wcaja		Código Caja objeto del cuadre.
 * @wvar String		$wnomcaja	Nombre Caja  objeto del cuadre.
 * @wvar String[2] 	$Ccofre		Fuente Recibos de Caja Centro de costos.
 * @wvar String[2] 	$Ccofnc		Fuente nota Crédito Centro de costos.
 * @wvar String[5]	$usu		Código del usuario que realiza el cuadre
 * 			
 * @table	000010	SELECT
 * @table	000016	SELECT
 * @table	000021	SELECT
 * @table	000022	SELECT
 * @table	000023	SELECT
 * @table	000028	SELECT
 * @table	000037	SELECT
 * 
 * @modified 2007-05-04 Se cambia el querie que busca los cuadres para que solo busque el de la caja que esta abierta.
 * @modified 2006-02-07 Se pone un order by para que se imprima en pantalla ordenado ascendentemente por número de venta.
 * @modified 2006-02-07 En todos aparece el # de la factura.
 * @modified 2005-10-10 Se cambian las tablas de farstore a $tipo_tablas, y la imagen del logoSe comentan las funciones.
 * @modified 2005-10-10 Se usa el include paleta.php para los colores.
 * @modified 2005-10-10 Se ingresa a la carpeta de POS.
 
 */


$wautor="Ana María Betancur V.";
$wactualiz="2005-02-07";

include_once("paleta.php");
echo "<p align=right><font size=1><b>Autor: ".$wautor."</b></font></p>";
echo "<p align=right><font size=1><b>".$wactualiz."</b></font></p>";





if(!isset($cuadre)) {
	echo '<form action="" method="POST">';
	echo "<center><table border width='390'>";
	echo "<tr><td align=center colspan='2' ><img src='/matrix/images/medical/POS/logo_".$tipo_tablas.".png' WIDTH=388 HEIGHT=70></td></tr>";
	echo "<tr><td align=center colspan='2' bgcolor=".$AzulClar."><font size=3 text color=".$blanco."><b>REPORTE CUADRE DE CAJA </b></font></td></tr>";


	//ACA TRAIGO LAS VARIABLES DE SUCURSAL, CAJA Y TIPO INGRESO QUE REALIZA CADA CAJERO
	if(!isset($fecha1)){
		$date=date("Y-m-d");
		echo "</table><BR><BR><center><table border width='390'>";
		echo "<tr><td align=left bgcolor=".$AzulClar."><font size=3 color='".$blanco."'><b>FECHA DE INICIO </b></font></td>";
		echo "<td align=center  bgcolor=".$AzulClar."><input type='text' name='fecha1' value='".$date."' size='9'></font></td>";
		echo "<tr><td align=left  bgcolor=".$AzulClar."><font size=3 color='".$blanco."'><b>FECHA DE FIN </b></font></td>";
		echo "<td align=center  bgcolor=".$AzulClar."><input type='text' name='fecha2' value='".$date."' size='9'></font></td>";

	}else{
		$q ="SELECT cjecco, cjecaj   "
		."FROM ".$tipo_tablas."_000030 "
		."WHERE cjeusu = '".substr($user,2)."' "
		."AND cjeest = 'on' ";
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



		echo "<tr><td align=center colspan='2' bgcolor=".$AzulClar."><font size=3 text color=".$blanco."><b>REPORTE CUADRE DE CAJA </b></font></td></tr>";
		echo "<tr><td align=center colspan='2' bgcolor=".$AzulClar."><font size=3 text color=".$blanco."><b>$wcaja </b></font></td></tr>";

		echo "<tr><td align=center colspan='2' bgcolor=".$AzulClar."><font size=2 text color=".$blanco."><b>DESDE $fecha1 HASTA $fecha2</b></font></td></tr>";

		echo "</table><BR><BR><center><table border width='390'>";

		$pos=explode("-",$wcaja);
		$wcaja = $pos[0];
		$wnomcaj = $pos[1];
		
		$time['Primero']=time();
		$q="SELECT Fecha_data,Hora_data,Cdecua "
		."FROM ".$tipo_tablas."_000037 "
		."WHERE	Cdeest = 'on' "
		."AND	Cdecco='$wcco' "   
		."AND Cdecaj='".substr($wcaja,0,2)."' "
		."AND	Fecha_data between '$fecha1' and '$fecha2'"
		."group by Cdecua";
		$res = mysql_query($q,$conex);
		$num = mysql_num_rows($res);
		if ($num > 0){
			for($i=0;$i<$num;$i++){
				$row = mysql_fetch_array($res);
				echo "<tr><td align=center ><input type='radio' name='cuadre' value='".$row[2]."'></td><td><font size=3 text color=#003366>Cuadre # ".$row[2].". Realizado el ".$row[0]." a las ".$row[1]."</font></td></tr>";
			}
		}
		$time['Primero']=time()-$time['Primero'];
		
	}
	echo "</table><br><br><table align='center' border='0'><tr><td align='center'><input type='submit' name='aceptar' value='ACEPTAR'></td></tr></table>";
	ECHO "</FORM>";


}
else{
	echo "<center><table border >";
	echo "<tr><td align=center colspan='2' ><img src='/matrix/images/medical/POS/logo_".$tipo_tablas.".png' IDTH=388 HEIGHT=70></td></tr>";
	echo "<tr><td align=center colspan='2' bgcolor=".$AzulClar."><font size=3 text color=".$blanco."><b>REPORTE CUADRE DE CAJA </b></font></td></tr>";
	echo "<tr><td align=center colspan='2' bgcolor=".$AzulClar."><font size=3 text color=".$blanco."><b>$wcaja</b></font></td></tr>";
	echo "</table>";

	echo "<input type='hidden' name='wcaja' value='".$wcaja."'>";
	$pos=explode("-",$wcaja);
	$wcaja = $pos[0];
	$wnomcaj = $pos[1];

	if(!isset($usuario)){
		$time['Segundo']=time();
		$q="SELECT Seguridad, Fecha_data, Hora_data "
		."FROM ".$tipo_tablas."_000037 "
		."WHERE	Cdecua='".$cuadre."' "
		."AND	Cdecaj='".$wcaja."' ";
		$err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);
		if($num > 0){
			$row=mysql_fetch_row($err);
			$ini=explode("-",$row[0]);
			$usuario=$ini[1];
			$fecha=$row[1];
			$hora=$row[2];
		}
		$time['Segundo']=time()-$time['Segundo'];
	}

	
	$q="SELECT Descripcion "
	."FROM usuarios "
	."WHERE Codigo='$usuario'";
	$err=mysql_query($q,$conex);
	$num=mysql_num_rows($err);
	if($num > 0){
		$row=mysql_fetch_row($err);
		$usuario=$row[0];
	}

	
	$q="SELECT Ccofrc, Ccofnc "
	."FROM ".$tipo_tablas."_000003 "
	."WHERE Ccocod	= '".$wcco."' "
	."AND Ccoest	= 'on'";
	$err1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($err1);
	if ($num1 > 0)
	{
		$row1=mysql_fetch_row($err1);
		//$Ccoffp=$row1[0];
		$Ccofre=$row1[0];
		$Ccofnc=$row1[1];
	}


	echo "<BR><BR><center><table border >";
	echo "<tr><td align=center colspan='2' bgcolor=".$AzulClar."><font size=3 text color=".$blanco."><b>CUADRE DE CAJA  # $cuadre</b></font></td></tr>";
	echo "<tr><td align=center colspan='2' bgcolor=".$AzulClar."><font size=3 text color=".$blanco."><b>REALIZADO POR: $usuario </b></font></td></tr>";
	echo "<tr><td align=center colspan='2' bgcolor=".$AzulClar."><font size=3 text color=".$blanco."><b>$fecha $hora </b></font></td></tr>";
	echo "</table>";


	/*SALDOS ANTERIORES EN VENTAS*/
	$time['AV1']=time();
	$table="table_".date("dis");
	$q= "Create table  IF NOT EXISTS ".$table." as "
	."SELECT Cdefue as Fuente, Cdenum as Numero, SUM(Cdevrf) as Saldo "
	."FROM ".$tipo_tablas."_000037 "
	."WHERE	Cdecaj = '".$wcaja."' "
	."and	Cdecco = '".$wcco."' "
	."and	Cdecua = '".($cuadre-1)."' "
	."and	Cdefue	=	'".$Ccofre."' "
	."and	Cdevrf <> 0 "
	."and	Cdeest = 'on' "
	."GROUP BY Cdenum,Cdefue "
	."ORDER BY Cdefue,Cdenum ";
	$err = mysql_query($q,$conex);
	$time['AV1']=time()-$time['AV1'];

	$time['AV2']=time();
	$table1="table1_".date("dis");
	$q= "Create table  IF NOT EXISTS ".$table1." as "
	."SELECT Cdenum, Cdefue, Cdefpa, SUM(Cdevrf) as Cdevrf , Saldo "
	."FROM $table, ".$tipo_tablas."_000037 "
	."WHERE	Cdecaj = '".$wcaja."' "
	."and	Cdecco = '".$wcco."' "
	."and	Cdecua = '".$cuadre."' "
	."and	Cdefue = '".$Ccofre."' "
	."and	Cdenum = Numero "
	."and	Cdeest = 'on' "
	."GROUP BY Cdenum,Cdefue "
	."ORDER BY Cdefue,Cdenum ";
	$err = mysql_query($q,$conex);
	$time['AV2']=time()-$time['AV2'];

	$query = "DROP table ".$table;
	$err = mysql_query($query,$conex);

	$time['AV3']=time();
	$q= "SELECT Cdenum, Cdefue, Cdevrf, Rdevta,  Rdevca, Rdefac, Venvto, Saldo "
	."FROM $table1, ".$tipo_tablas."_000021,".$tipo_tablas."_000016 "
	."WHERE Rdefue = Cdefue "
	."and	Rdenum = Cdenum "
	."and	Rdecco = '".$wcco."' "
	."and	Vennum = Rdevta "
	."and	Vencco = '".$wcco."' ";
	$err=mysql_query($q,$conex);
	$num=mysql_numrows($err);
	if($num>0){
		echo "<BR><table border='1'><tr><td colspan='8' align='center' bgcolor='".$AguaOsc."'><b>SALDOS ANTERIORES EN VENTAS</B></td></tr>";
		echo "<tr>";
		echo "<td bgcolor='".$AguaClar."'><b>Venta</b></td>";
		echo "<td bgcolor='".$AguaClar."'><b>Factura</b></td>";
		echo "<td bgcolor='".$AguaClar."'><b>Fuente-Num</b></td>";
		echo "<td bgcolor='".$AguaClar."'><b>Valor Venta</b></td>";
		echo "<td bgcolor='".$AguaClar."'><b>Valor Pagado</b></td>";
		echo "<td bgcolor='".$AguaClar."'><b>Valor Saldo</b></td>";
		echo "<td bgcolor='".$AguaClar."'><b>Valor se Cuadro</b></td>";
		echo "<td bgcolor='".$AguaClar."'><b>Saldo Restante</b></td>";
		echo "</tr>";
		$tot1=0;
		$tot2=0;
		$tot3=0;
		$tot4=0;
		$tot5=0;
		for($i=0; $i<$num;$i++){
			$row=mysql_fetch_array($err);
			echo "<tr>";
			echo "<td>".$row["Rdevta"]."</td>";
			echo "<td>".$row["Rdefac"]."</td>";
			echo "<td>".$row["Cdefue"]."-".$row["Cdenum"]."</td>";
			echo "<td align='right'>$ ".number_format($row["Venvto"],",","",".")."</td>";
			echo "<td align='right'>$ ".number_format($row["Rdevca"],",","",".")."</td>";
			echo "<td align='right'>$ ".number_format($row["Saldo"],",","",".")."</td>";
			echo "<td align='right'>$ ".number_format(($row["Saldo"]-$row["Cdevrf"]),",","",".")."</td>";
			echo "<td align='right'>$ ".number_format($row["Cdevrf"],",","",".")."</td>";
			echo "</tr>";
			$tot1=$tot1+$row["Venvto"];
			$tot2=$tot2+$row["Rdevca"];
			$tot3=$tot3+$row["Saldo"];
			$tot4=$tot4+$row["Saldo"]-$row["Cdevrf"];
			$tot5=$tot5+$row["Cdevrf"];
		}
		echo "<tr>";
		echo "<td colspan='3' bgcolor='".$AmarQuem."'><b>TOTAL SALDOS</b></td>";
		echo "<td align='right' bgcolor='".$AmarQuem."'><b>$ ".number_format($tot1,",","",".")."</b></td>";
		echo "<td align='right' bgcolor='".$AmarQuem."'><b>$ ".number_format($tot2,",","",".")."</b></td>";
		echo "<td align='right' bgcolor='".$AmarQuem."'><b>$ ".number_format($tot3,",","",".")."</b></td>";
		echo "<td align='right' bgcolor='".$AmarQuem."'><b>$ ".number_format($tot4,",","",".")."</b></td>";
		echo "<td align='right' bgcolor='".$AmarQuem."'><b>$ ".number_format($tot5,",","",".")."</b></td>";
		echo "</tr>";
	}
	$time['AV3']=time()-$time['AV3'];

	echo "</table>";

	$query = "DROP table ".$table1;
	$err = mysql_query($query,$conex);


	/*SALDOS ANTERIORES EN NOTAS CRÉDITO*/
	$time['ANC1']=time();
	$table="table_".date("dis");
	$q= "Create table  IF NOT EXISTS ".$table." as "
	."SELECT Cdefue as Fuente, Cdenum as Numero, SUM(Cdevrf) as Saldo "
	."FROM ".$tipo_tablas."_000037 "
	."WHERE	Cdecaj = '".$wcaja."' "
	."and	Cdecco = '".$wcco."' "
	."and	Cdecua = '".($cuadre-1)."' "
	."and	Cdefue	=	'".$Ccofnc."' "
	."and	Cdevrf <> 0 "
	."and	Cdeest = 'on' "
	."GROUP BY Cdenum,Cdefue "
	."ORDER BY Cdefue,Cdenum ";
	$err = mysql_query($q,$conex);
	$time['ANC1']=time()-$time['ANC1'];
	
	$time['ANC2']=time();
	$table1="table1_".date("dis");
	$q= "Create table  IF NOT EXISTS ".$table1." as "
	."SELECT Cdenum, Cdefue, Cdefpa, SUM(Cdevrf) as Cdevrf , Saldo "
	."FROM $table, ".$tipo_tablas."_000037 "
	."WHERE	Cdecaj = '".$wcaja."' "
	."and	Cdecco = '".$wcco."' "
	."and	Cdecua = '".$cuadre."' "
	."and	Cdefue = '".$Ccofnc."' "
	."and	Cdenum = Numero "
	."and	Cdeest = 'on' "
	."GROUP BY Cdenum,Cdefue "
	."ORDER BY Cdefue,Cdenum ";
	$err = mysql_query($q,$conex);
	$time['ANC2']=time()-$time['ANC2'];
	
	$query = "DROP table ".$table;
	$err = mysql_query($query,$conex);
	
	$time['ANC3']=time();
	$q= "SELECT Cdenum, Cdefue, Cdevrf, Rdevta, Rdefac, Rdevca,  Saldo, Menfac "
	."FROM $table1, ".$tipo_tablas."_000021, ".$tipo_tablas."_000010 "
	."WHERE Rdefue = Cdefue "
	."and	Rdenum = Cdenum "
	."and	Rdecco = '".$wcco."' "
	."and	Mendoc = Rdevta "
	."and	Mencco = '".$wcco."' "
	."and	Mencon = '801' ";
	$err=mysql_query($q,$conex);
	//	echo "233: ".mysql_errno()."=".mysql_error();
	$num=mysql_numrows($err);
	if($num>0){
		echo "<BR><table border='1'><tr><td colspan='8' align='center' bgcolor='".$AguaOsc."'><b>SALDOS ANTERIORES EN NOTAS CRÉDITO</B></td></tr>";
		echo "<tr>";
		echo "<td bgcolor='".$AguaClar."'><b>Dev.</b></td>";
		echo "<td bgcolor='".$AguaClar."'><b>Fuente-Num</b></td>";
		echo "<td bgcolor='".$AguaClar."'><b>Factura</b></td>";
		echo "<td bgcolor='".$AguaClar."'><b>Venta</b></td>";
		echo "<td bgcolor='".$AguaClar."'><b>Valor NC</b></td>";
		echo "<td bgcolor='".$AguaClar."'><b>Valor Saldo</b></td>";
		echo "<td bgcolor='".$AguaClar."'><b>Valor Cuadrado</b></td>";
		echo "<td bgcolor='".$AguaClar."'><b>Saldo Restante</b></td>";
		//echo "<td></td>";
		echo "</tr>";
		$tot1=0;
		$tot2=0;
		$tot3=0;
		$tot4=0;
		for($i=0; $i<$num;$i++){
			$row=mysql_fetch_array($err);
			echo "<tr>";
			echo "<td>".$row["Rdevta"]."</td>";
			echo "<td>".$row["Cdefue"]."-".$row["Cdenum"]."</td>";
			echo "<td>".$row["Rdefac"]."</td>";
			echo "<td>".$row["Menfac"]."</td>";
			echo "<td align='right'>$ ".number_format($row["Rdevca"],",","",".")."</td>";
			echo "<td align='right'>$ ".number_format($row["Saldo"],",","",".")."</td>";
			echo "<td align='right'>$ ".number_format(($row["Saldo"]-$row["Cdevrf"]),",","",".")."</td>";
			echo "<td align='right'>$ ".number_format($row["Cdevrf"],",","",".")."</td>";
			echo "</tr>";
			$tot1=$tot1+$row["Rdevca"];
			$tot2=$tot2+$row["Saldo"];
			$tot3=$tot3+$row["Saldo"]-$row["Cdevrf"];
			$tot4=$tot4+$row["Cdevrf"];
		}
		echo "<tr>";
		echo "<td colspan='4' bgcolor='".$AmarQuem."'><b>TOTAL SALDOS NC</b></td>";
		echo "<td align='right' bgcolor='".$AmarQuem."'><b>$ ".number_format($tot1,",","",".")."</b></td>";
		echo "<td align='right' bgcolor='".$AmarQuem."'><b>$ ".number_format($tot2,",","",".")."</b></td>";
		echo "<td align='right' bgcolor='".$AmarQuem."'><b>$ ".number_format($tot3,",","",".")."</b></td>";
		echo "<td align='right' bgcolor='".$AmarQuem."'><b>$ ".number_format($tot4,",","",".")."</b></td>";
		echo "</tr>";
	}

	echo "</table>";
	$time['ANC3']=time()-$time['ANC3'];
	$query = "DROP table ".$table1;
	$err = mysql_query($query,$conex);



	/*INGRESOS: Recibos que no tengan asociado ningun registro en la tabla de detalle de Cuadre de caja(".$tipo_tablas."_0000037) */
	$time['V1']=time();
	$table="table_".date("dis");
	$q= "Create table  IF NOT EXISTS ".$table." as "
	."SELECT * "
	."FROM ".$tipo_tablas."_000037 "
	."WHERE	Cdecaj = '".$wcaja."' "
	."and	Cdecco = '".$wcco."' "
	."and	Cdecua = '".$cuadre."' "
	."and	Cdefue	=	'".$Ccofre."' "
	."and	Cdeest = 'on' "
	."ORDER BY Cdefue,Cdenum ";
	$err = mysql_query($q,$conex);

	$table1="table1_".date("dis");
	$q= "Create table  IF NOT EXISTS ".$table1." as "
	."SELECT Cdenum,Cdefue,SUM(Cdevrf) as Cdevrf "
	."FROM $table "
	."WHERE NOT	EXISTS (SELECT	* "
	."					FROM	".$tipo_tablas."_000037 "
	."					WHERE 	".$tipo_tablas."_000037.Cdeest = 'on' "
	."					and 	".$tipo_tablas."_000037.Cdecaj = $table.Cdecaj "
	."					and 	".$tipo_tablas."_000037.Cdecco = $table.Cdecco "
	."					and 	".$tipo_tablas."_000037.Cdefue = $table.Cdefue "
	."					and 	".$tipo_tablas."_000037.Cdenum = $table.Cdenum "
	."					and 	".$tipo_tablas."_000037.Cdecua = '".($cuadre-1)."') "
	."GROUP BY Cdenum,Cdefue "
	."ORDER BY Cdefpa ";
	$err = mysql_query($q,$conex);
	$time['V1']=time()-$time['V1'];

	$query = "DROP table ".$table;
	$err = mysql_query($query,$conex);

	//Rdevca-Cdvrf ->lo egresado en el cuadre
	$time['V2']=time();
	$q= "SELECT Cdenum, Cdefue, Cdevrf, Rdevta, Rdefac, Rdevca, Venvto "
	."FROM $table1, ".$tipo_tablas."_000021, ".$tipo_tablas."_000016 "
	."WHERE Rdefue = Cdefue "
	."and	Rdenum = Cdenum "
	."and	Rdecco = '".$wcco."' "
	."and	Vennum = Rdevta "
	."and	Vencco = '".$wcco."' "
	."order by Rdevta ";
	$err=mysql_query($q,$conex);
	//	echo mysql_errno()."=".mysql_error();
	$num=mysql_numrows($err);
	if($num>0) {
		echo "<BR><table border='1'><tr><td colspan='7' align='center' bgcolor='".$AguaOsc."'><b>VENTAS</B></td></tr>";
		echo "<tr>";
		echo "<td bgcolor='".$AguaClar."'><b>Venta</b></td>";
		echo "<td bgcolor='".$AguaClar."'><b>Factura</b></td>";
		echo "<td bgcolor='".$AguaClar."'><b>Fuente-Num</b></td>";
		echo "<td bgcolor='".$AguaClar."'><b>Valor Total</b></td>";
		echo "<td bgcolor='".$AguaClar."'><b>Valor Pagado</b></td>";
		echo "<td bgcolor='".$AguaClar."'><b>Valor Cuadrado</b></td>";
		echo "<td bgcolor='".$AguaClar."'><b>Saldo Restante</b></td>";
		echo "</tr>";
		$tot1=0;
		$tot2=0;
		$tot3=0;
		$tot4=0;
		for($i=0; $i<$num;$i++){
			$row=mysql_fetch_array($err);
			echo "<tr>";
			echo "<td>".$row["Rdevta"]."</td>";
			echo "<td>".$row["Rdefac"]."</td>";
			echo "<td>".$row["Cdefue"]."-".$row["Cdenum"]."</td>";
			//	echo "<td>".$row["Rdevta"]."</td>";
			echo "<td align='right'>$ ".number_format($row["Venvto"],",","",".")."</td>";
			echo "<td align='right'>$ ".number_format($row["Rdevca"],",","",".")."</td>";
			echo "<td align='right'>$ ".number_format(($row["Rdevca"]-$row["Cdevrf"]),",","",".")."</td>";
			echo "<td align='right'>$ ".number_format($row["Cdevrf"],",","",".")."</td>";
			echo "</tr>";
			$tot1=$tot1+$row["Venvto"];
			$tot2=$tot2+$row["Rdevca"];
			$tot3=$tot3+$row["Rdevca"]-$row["Cdevrf"];
			$tot4=$tot4+$row["Cdevrf"];
		}

		echo "<tr>";
		echo "<td colspan='3' bgcolor='".$AmarQuem."'><b>TOTAL VENTAS</b></td>";
		echo "<td align='right' bgcolor='".$AmarQuem."'><b>$ ".number_format($tot1,",","",".")."</b></td>";
		echo "<td align='right' bgcolor='".$AmarQuem."'><b>$ ".number_format($tot2,",","",".")."</b></td>";
		echo "<td align='right' bgcolor='".$AmarQuem."'><b>$ ".number_format($tot3,",","",".")."</b></td>";
		echo "<td align='right' bgcolor='".$AmarQuem."'><b>$ ".number_format($tot4,",","",".")."</b></td>";
		echo "</tr>";
	}
	if(isset($time['V2']))
	$time['V2']=time()-$time['V2'];

	echo "</table>";

	$query = "DROP table ".$table1;
	$err = mysql_query($query,$conex);

	/*NOTA CRÉDITO: Recibos que no tengan asociado ningun registro en la tabla de detalle de Cuadre de caja(".$tipo_tablas."_0000037) */
	$time['NC1']=time();
	$table="table_".date("dis");
	$q= "Create table  IF NOT EXISTS ".$table." as "
	."SELECT * "
	."FROM ".$tipo_tablas."_000037 "
	."WHERE	Cdecaj = '".$wcaja."' "
	."and	Cdecco = '".$wcco."' "
	."and	Cdecua = '".$cuadre."' "
	."and	Cdefue	=	'".$Ccofnc."' "
	."and	Cdeest = 'on' "
	."ORDER BY Cdefue,Cdenum ";
	$err = mysql_query($q,$conex);
	$time['NC1']=time()-$time['NC1'];

	$time['NC2']=time();
	$table1="table1_".date("dis");
	$q= "Create table  IF NOT EXISTS ".$table1." as "
	."SELECT Cdenum,Cdefue,Cdefpa,SUM(Cdevrf) as Cdevrf "
	."FROM ".$table." "
	."WHERE	NOT EXISTS (SELECT	* "
	."					FROM	".$tipo_tablas."_000037 "
	."					WHERE 	".$tipo_tablas."_000037.Cdeest = 'on' "
	."					and 	".$tipo_tablas."_000037.Cdecaj = $table.Cdecaj "
	."					and 	".$tipo_tablas."_000037.Cdecco = $table.Cdecco "
	."					and 	".$tipo_tablas."_000037.Cdefue = $table.Cdefue "
	."					and 	".$tipo_tablas."_000037.Cdenum = $table.Cdenum "
	."					and 	".$tipo_tablas."_000037.Cdecua = '".($cuadre-1)."') "
	."GROUP BY Cdenum,Cdefue "
	."ORDER BY Cdefpa ";
	$err = mysql_query($q,$conex);
	$time['NC2']=time()-$time['NC2'];
	
	$query = "DROP table ".$table;
	$err = mysql_query($query,$conex);

	$time['NC3']=time();
	$q= "SELECT Cdenum, Cdefue, Cdevrf, Rdevta,  Rdevca, Rdefac, Menfac "
	."FROM $table1, ".$tipo_tablas."_000021, ".$tipo_tablas."_000010 "
	."WHERE Rdefue = Cdefue "
	."and	Rdenum = Cdenum "
	."and	Rdecco = '".$wcco."' "
	."and	Mendoc = Rdevta "
	."and	Mencco = '".$wcco."' "
	."and	Mencon = '801' "
	."order by Rdevta ";
	$err=mysql_query($q,$conex);
	//	echo mysql_errno()."=".mysql_error();
	$num=mysql_numrows($err);
	if($num>0) {
		echo "<BR><table border='1'><tr><td colspan='7' align='center' bgcolor='".$AguaOsc."'><b>NOTA CRÉDITO</B></td></tr>";
		echo "<tr>";
		echo "<td bgcolor='".$AguaClar."'><b>Dev.</b></td>";
		echo "<td bgcolor='".$AguaClar."'><b>Fuente-Num</b></td>";
		echo "<td bgcolor='".$AguaClar."'><b>Factura</b></td>";
		echo "<td bgcolor='".$AguaClar."'><b>Venta</b></td>";
		echo "<td bgcolor='".$AguaClar."'><b>Valor NC</b></td>";
		echo "<td bgcolor='".$AguaClar."'><b>Valor Cuadrado</b></td>";
		echo "<td bgcolor='".$AguaClar."'><b>Saldo Restante</b></td>";
		echo "</tr>";
		$tot1=0;
		$tot2=0;
		$tot3=0;
		for($i=0; $i<$num;$i++){
			$row=mysql_fetch_array($err);
			echo "<tr>";
			echo "<td>".$row["Rdevta"]."</td>";
			echo "<td>".$row["Cdefue"]."-".$row["Cdenum"]."</td>";
			echo "<td>".$row["Rdefac"]."</td>";
			echo "<td>".$row["Menfac"]."</td>";
			echo "<td align='right'>$ ".number_format($row["Rdevca"],",","",".")."</td>";
			echo "<td align='right'>$ ".number_format(($row["Rdevca"]-$row["Cdevrf"]),",","",".")."</td>";
			echo "<td align='right'>$ ".number_format($row["Cdevrf"],",","",".")."</td>";
			echo "</tr>";
			$tot1=$tot1+$row["Rdevca"];
			$tot2=$tot2+$row["Rdevca"]-$row["Cdevrf"];
			$tot3=$tot3+$row["Cdevrf"];
		}
		echo "<tr>";
		echo "<td colspan='4' bgcolor='".$AmarQuem."'><b>TOTAL NC VENTAS</b></td>";
		echo "<td align='right' bgcolor='".$AmarQuem."'><b>$ ".number_format($tot1,",","",".")."</b></td>";
		echo "<td align='right' bgcolor='".$AmarQuem."'><b>$ ".number_format($tot2,",","",".")."</b></td>";
		echo "<td align='right' bgcolor='".$AmarQuem."'><b>$ ".number_format($tot3,",","",".")."</b></td>";
		echo "</tr>";
	}
	$time['NC3']=time()-$time['NC3'];

	echo "</table>";
	$query = "DROP table ".$table1;
	$err = mysql_query($query,$conex);
}

?>
</body>
</html>