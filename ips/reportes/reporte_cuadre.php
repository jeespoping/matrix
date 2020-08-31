<html>
<head>
  <title>CUADRE CAJA - </title>
</head>
<body>
<?php
include_once("conex.php");

/**
 * REPORTE DEL ESTADO DE UNA CAJA ANTERIOR A UN CUADRE 
 * 
 * Muestra el estado de la caja en un momento dado, el cual se divide por saldos nuevos y anteriores
 *  tanto en notas crédito como en ventas normales, cada división se totaliza y sus valores se
 * discriminan por forma de pago.
 *  
 * Funciona como reporte en donde pide como parametros dos fechas para desplegar una lista de cuadres de los cuales el usuario
 * debe elegir uno para ver el estado de la caja antes de que ocurriera ese cuadre, o lo que es lo mismo el estado de la caja despues 
 * del cuadre inmediatamente anterior al elégido.
 * 
 * Si no funciona como reporte, hace parte del cuadre d caja muestra el estado actual de la caja asignada al usuario, y permite el paso
 * al programa principal de cuadre de caja "cuadre_preliminar.php".
 * 
 * @author Ana María Betancur Vargas
 * @created 2005-08-01
 * @version 2006-04-13
 * 
 * @modified 2011-01-28 Se hacen temporales las tablas sql que se crean en este reporte.
 * @modified 2007-01-25 Se comenta todo lo que respecta a $ccant por que parece ser una variable inuitil.
 * @modified 2006-04-13 Se modifica el query de las ventas que crea la tabla table1 por dos querys, tambien varias condiciones que se 
 * efectuaban sobre la tabla $cuadre, ahora se efectuan directamente en el query que genera esta tabla.
 * @modified 2006-04-13 Se cambia el encabezado, para que el nombre sea mas claro y se sepa el programa y la versión
 * @modified 2005-09-26 Se cambian los códigos de los colores
 * @modified 2005-09-26 Se cambian los nombres de las tablas por $tipo_tablas
 * @modified 2005-09-26 Se cambia el nombre del logo por el logo asociado con el $tipo_tablas
 * @modified 2005-10-10 Se crea la tabla temporal $cuadre para mejorrar la velocidad de los querys y se usa la fecha del último 
 * cuadre como referencia.
 * 
 * @table	000003 SELECT
 * @table	000010 SELECT
 * @table	000016 SELECT
 * @table	000021 SELECT
 * @table	000022 SELECT
 * @table	000023 SELECT
 * @table	000028 SELECT
 * @table	000030 SELECT
 * @table	000037 SELECT
 * 
 * @wvar String[4]	$wcco 		Codigo Centro de costos.
 * @wvar String		$wnomcco 	Nombre del centro de costos.
 * @wvar String		$wcaja		Código Caja objeto del cuadre.
 * @wvar String		$wnomcaja	Nombre Caja  objeto del cuadre.
 * @wvar String[2] 	$Ccofre		Fuente Recibos de Caja Centro de costos.
 * @wvar String[2] 	$Ccofnc		Fuente nota Crédito Centro de costos.
 * @wvar Float		$salant		Variable auxiliar que maneja los consolidados de los saldos anteriores, tanto en recibos normales como notas crédito.
 * @wvar Float		$ingresos	Variable auxiliar que maneja los consolidados de los ingresos, tanto en recibos normales como notas crédito.
 * @wvar String[8]	$fecha		Fecha del cuadre anterior.
 * 
 */

$wautor="Ana María Betancur V.";
$wactualiz="2011-01-28";

/**
 * Include que almacena la información de los colores usados para el despliegue en pantalla.
 */
include_once("paleta.php");

echo "<center><table border width='350'>";
echo "<tr><td align=center colspan='2' ><img src='/matrix/images/medical/POS/logo_".$tipo_tablas.".png' WIDTH=388 HEIGHT=70></td></tr>";
echo "<tr><td align=center colspan='2' bgcolor=".$AzulClar."><font size=3 text color=#FFFFFF><b>REPORTE PRELIMINAR CUADRE DE CAJA  </b></font></td></tr>";
echo "<tr><td align=center colspan='2' bgcolor=".$AzulClar."><font size=3 text color=#FFFFFF><b>reportecuadre.php ".$wactualiz."</b></font></td></tr>";

if(!isset($_SESSION['user']))
echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	


	$pos = strpos($user,"-");
	$wusuario = substr($user,$pos+1,strlen($user));

	if(!isset($wcco)) {

		//TRAE LAS VARIABLES DE SUCURSAL, CAJA Y TIPO INGRESO QUE REALIZA CADA usuario
		$q ="SELECT cjecco, cjecaj, cjetin  "
		."FROM ".$tipo_tablas."_000030 "
		."WHERE cjeusu = '".$wusuario."'"
		."AND cjeest = 'on' ";

		$res = mysql_query($q,$conex) or die("error ".mysql_errno()."_ en el query:".$q."_".mysql_error());
		$num = mysql_num_rows($res);
		if ($num > 0)	{
			$row = mysql_fetch_array($res);

			$pos = strpos($row[0],"-");
			$wcco = substr($row[0],0,$pos);
			$wnomcco = substr($row[0],$pos+1,strlen($row[0]));

			$wcaja=$row[1];
			$wtiping = $row[2];

			$q="SELECT Ccofrc, Ccofnc "
			."FROM ".$tipo_tablas."_000003 "
			."WHERE Ccocod	= '".$wcco."' "
			."AND 	Ccoest	= 'on'";
			$err1 = mysql_query($q,$conex) or die("error ".mysql_errno()."_ en el query:".$q."_".mysql_error());
			$num1 = mysql_num_rows($err1);
			if ($num1 > 0)	{
				$row1=mysql_fetch_row($err1);
				//$Ccoffp=$row1[0];
				$Ccofre=$row1[0];
				$Ccofnc=$row1[1];
			}

		}
		else{
			echo "EL USUARIO ESTA INACTIVO O NO TIENE PERMISO PARA FACTURAR";
		}
	}

	if(isset($wcco)){

		echo '<form action="cuadre_preliminar.php" method="POST">';

		echo "<tr><td align=center colspan='2' bgcolor=".$AzulClar."><font size=3 text color=#FFFFFF><b>$wcaja</b></font></td></tr>";
		echo "</TABLE>";

		echo "<input type='hidden' name='wcaja' value='".$wcaja."'>";
		$pos=explode("-",$wcaja);
		$wcaja = $pos[0];
		$wnomcaj = $pos[1];

		echo "<br><br><table border='1' width='400'>";
		echo "<tr><td colspan='2' align='center' bgcolor=".$AguaOsc."><b><font text color=".$AzulText.">SALDO ANTERIOR</font></b></TR>";

		/*BUSCAR EL REMANENTE DE EL ULTIMO CUADRE DE CAJA*/
		$salant=0;
		//$ccant="";
		//En la tabla de cajas trae el último cuadre
		$q="SELECT Cajcua "
		."FROM ".$tipo_tablas."_000028 "
		."WHERE 	Cajcco = '".$wcco."' "
		."and 		Cajcod = '".$wcaja."'";
		$res = mysql_query($q,$conex) or die("error ".mysql_errno()."_ en el query:".$q."_".mysql_error());
		$num = mysql_num_rows($res);
		if ($num > 0)
		{
			$row=mysql_fetch_row($res);
			$cajcua=$row[0];
			if($cajcua[0] != 0) {
				/*Existe un cadre de caja Anterior*/
				
				echo "<input type=hidden name='cajcua' value='".$cajcua."'>";
				$time['CA']=time();
				$q="SELECT SUM(Cdevrf), Cdefpa, Fpades, ".$tipo_tablas."_000037.Fecha_data "
				."FROM ".$tipo_tablas."_000037, ".$tipo_tablas."_000023 "
				."WHERE	Cdecua	=	'".$cajcua."' "	//Pertenece al cuadre de caja anterior
				."and 	Cdecaj	=	'".$wcaja."' "
				."and	Cdecco	=	'".$wcco."' "
				."and	Cdevrf 	<>	0 "	//quedo un valor pendiente
				."and	Cdefue	= '".$Ccofre."' "
				."and 	Fpacod	=	Cdefpa "	//El código de la forma de pago correponde al de el detalle del CuC
				."GROUP BY Cdefpa "
				."ORDER BY Cdefpa ";
				$res1 = mysql_query($q,$conex) or die("error ".mysql_errno()."_ en el query:".$q."_".mysql_error());
				$num1 = mysql_num_rows($res1);
				if ($num1 > 0)	{
					$salant=0;
					echo "<tr><td align=left bgcolor=".$AguaClar."><b><font text color=".$AzulText.">FORMA DE PAGO</font></b>";
					echo "<td align='center' bgcolor=".$AguaClar."><b><font text color=".$AzulText.">VALOR TOTAL</td></tr>";
					for($i=0;$i<$num1;$i++) {
						$row1=mysql_fetch_row($res1);
						echo "<tr><td align=left><font text color=".$AzulClar.">".$row1[1]."-".$row1[2]."</font></b>";
						echo "<td align='right'><font text color=".$AzulClar.">$".number_format($row1[0],",","",".")."</td></tr>";
						$salant+=$row1[0];
						$fecha=$row1[3];
					}
					$time['SubCA']=0;
				}else{
					
					$time['SubCA']=time();
					$q="SELECT DISTINCT(Fecha_data) "
					."FROM ".$tipo_tablas."_000037 "
					."WHERE	Cdecua	=	'".$cajcua."' "	//Pertenece al cuadre de caja anterior
					."and 	Cdecaj	=	'".$wcaja."' "
					."and	Cdecco	=	'".$wcco."' ";
					$err1 = mysql_query($q,$conex) or die("error ".mysql_errno()."_ en el query:".$q."_".mysql_error());
					$num1 = mysql_num_rows($err1);
					if ($num1 > 0)	{
						$row=mysql_fetch_row($err1);
						$fecha=$row[0];
					}
					$time['SubCA']=time()-$time['SubCA'];
				}
				$time['CA']=time()-$time['CA'];
			}else{
				/*NO Existe un cadre de caja Anterior*/
				echo "<input type=hidden name='cajcua' value='0'>";
				$fecha="0000-00-00";
			}
		}else {
			$fecha="0000-00-00";
			echo "<input type=hidden name='cajcua' value='0'>";
		}

		echo "<tr><td align=left bgcolor=".$AmarQuem."><b><font text color=".$AzulText.">TOTAL SALDO ANTERIOR</font></b>";
		echo "<td align='right' bgcolor=".$AmarQuem."><b><font text color=".$AzulText.">$".number_format($salant,0,"",",")."</td></tr>";
		echo "</table>";

		/*BUSCAR EL REMANENTE DE NOTAS CRÉDITO DE EL ULTIMO CUADRE DE CAJA*/
		echo "<br><br><table border='1' width='400'>";
		echo "<tr><td colspan='2' align='center' bgcolor=".$AguaOsc."><b><font text color=".$AzulText.">SALDO ANTERIOR NOTAS CRÉDITO</font></b></TR>";
		$salant=0;
		//$ccant="";
		if (isset($cajcua))	{
			if($cajcua != 0) {
				/*Existe un cadre de caja Anaterior*/
				$time['ANC1']=time();
				$q="SELECT SUM(Cdevrf), Cdefpa, Fpades "
				."FROM ".$tipo_tablas."_000037, ".$tipo_tablas."_000023 "
				."WHERE	Cdecua	=	'".$cajcua."' "	//Pertenece al último cuadre de caja Realizado
				."and 	Cdecaj	=	'".$wcaja."' "
				."and	Cdecco	=	'".$wcco."' "
				."and	Cdevrf 	<>	0 "	//quedo un valor pendiente
				."and	Cdefue	= '".$Ccofnc."' "
				."and 	Fpacod	=	Cdefpa "	//El código de la forma de pago correponde al de el detalle del Cuadre
				."GROUP BY Cdefpa "
				."ORDER BY Cdefpa ";

				$res1 = mysql_query($q,$conex) or die("error ".mysql_errno()."_ en el query:".$q."_".mysql_error());
				$num1 = mysql_num_rows($res1);
				if ($num1 > 0)	{
					$salant=0;
					echo "<tr><td align=left bgcolor=".$AguaClar."><b><font text color=".$AzulText.">FORMA DE PAGO</font></b>";
					echo "<td align='center' bgcolor=".$AguaClar."><b><font text color=".$AzulText.">VALOR TOTAL</td></tr>";
					for($i=0;$i<$num1;$i++) {
						$row1=mysql_fetch_row($res1);
						echo "<tr><td align=left><font text color=".$AzulClar.">".$row1[1]."-".$row1[2]."</font></b>";
						echo "<td align='right'><font text color=".$AzulClar.">$".number_format($row1[0],",","",".")."</td></tr>";
						$salant+=$row1[0];
					}
				}
				$time['ANC1']=time()-$time['ANC1'];
			}else {
				/*NO Existe un cadre de caja Anterior*/
				echo "<input type=hidden name='cajcua' value='0'>";
			}
		}else {
			/*NO Existe un cadre de caja Anterior, es mas no existe la caja*/
			echo "<input type=hidden name='cajcua' value='0'>";
		}

		echo "<tr><td align=left bgcolor=".$AmarQuem."><b><font text color=".$AzulText.">TOTAL SALDO NOTAS CRÉDITO ANTERIORES</font></b>";
		echo "<td align='right' bgcolor=".$AmarQuem."><b><font text color=".$AzulText.">$".number_format($salant,0,"",",")."</td></tr>";
		echo "</table><br><br>";

		//echo "<input type='hidden' name='ccant' value='".$ccant."'>";
		echo "<input type='hidden' name='salant' value='".$salant."'>";


		/*BUSCAR NUEVAS VENTAS*/
		$time['V1']=time();
		$table="1".date("Mdis");
		$q= "CREATE TEMPORARY TABLE IF NOT EXISTS ".$table." as "
		."SELECT Rdenum "
		."FROM	".$tipo_tablas."_000021, ".$tipo_tablas."_000016 "
		."WHERE	Rdeest = 'on'"
		."and	Rdefue = '".$Ccofre."' "
		."and	Rdecco = '".$wcco."' "
		."and	Venfec >= '".$fecha."' "
		."and	Vennum = Rdevta "
		."and	Vencaj = '".$wcaja."' "
		."and	Vencco = '".$wcco."' "
		."and	Venest = 'on' ";
		$res1 = mysql_query($q,$conex) or die("error ".mysql_errno()."_ en el query:".$q."_".mysql_error());
		$time['V1']=time()-$time['V1'];
		

		$time['V2']=time();
		$cuadre="cuadre".date("Mdis");
		$q= "CREATE TEMPORARY TABLE IF NOT EXISTS ".$cuadre." as "
		."SELECT * "
		."FROM ".$tipo_tablas."_000037 "
		."WHERE Fecha_data >= '$fecha' "
		."AND	Cdeest = 'on' "
		."AND 	Cdecaj = '".$wcaja."' "
		."AND 	Cdecco = '".$wcco."' ";
		$res1 = mysql_query($q,$conex) or die("error ".mysql_errno()."_ en el query:".$q."_".mysql_error());
		
		
		$time['V2']=time()-$time['V2'];
	
	
		
		$time['V3']=time();
		$table1=date("Mdis");
		
		$q= "CREATE TEMPORARY TABLE IF NOT EXISTS ".$table1." as "
		."SELECT Rfpfpa, Rfpvfp, Rfpfue, Rfpnum "
		."FROM $table,".$tipo_tablas."_000022 "
		."WHERE	Rfpnum = Rdenum "
		."AND	Rfpfue	= '".$Ccofre."' "
		."AND	Rfpcco	= '".$wcco."' "
		."and	Rfpest = 'on' ORDER BY 4";
		$res1 = mysql_query($q,$conex) or die("error ".mysql_errno()."_ en el query:".$q."_".mysql_error());
		
		
		$table2="2".date("Mdis");		
		$q= "CREATE TEMPORARY TABLE IF NOT EXISTS ".$table2." as "
		."SELECT Rfpfpa, Rfpvfp, Rfpfue, Rfpnum "
		."FROM $table1 "
		."WHERE	NOT EXISTS (SELECT	Cdefue, Cdenum, Cdefpa "
		."					FROM	".$cuadre." "
		."					WHERE 	".$cuadre.".Cdefue = ".$table1.".Rfpfue "
		."					AND		".$cuadre.".Cdenum = ".$table1.".Rfpnum "
		."					AND		".$cuadre.".Cdefpa = ".$table1.".Rfpfpa ) "
		."ORDER BY 4";
		$res1 = mysql_query($q,$conex) or die("error ".mysql_errno()."_ en el query:".$q."_".mysql_error());
		
		$time['V3']=time()-$time['V3'];
	
		
		echo "<table border='1' width='400'>";
		echo "<tr><td colspan='2' align='center' bgcolor=".$AguaOsc."><b><font text color=".$AzulText.">INGRESOS</font></b></TR>";

		$time['V4']=time();
		$q="SELECT Sum(Rfpvfp), Rfpfpa, Fpades "	
		." FROM $table2, ".$tipo_tablas."_000023  "
		."WHERE	Fpacod = Rfpfpa"
		." group by Rfpfpa";
		$res1 = mysql_query($q,$conex) or die("error ".mysql_errno()."_ en el query:".$q."_".mysql_error());
		$num1 = mysql_num_rows($res1);
		$ingresos=0;
		if ($num1 > 0)	{
			echo "<tr><td align=left bgcolor=".$AguaClar."><b><font text color=".$AzulText.">FORMA DE PAGO</font></b>";
			echo "<td align='center' bgcolor=".$AguaClar."><b><font text color=".$AzulText.">VALOR TOTAL</td></tr>";
			for($i=0;$i<$num1;$i++) {
				$row1=mysql_fetch_row($res1);
				echo "<tr><td align=left><font text color=".$AzulClar.">".$row1[1]."-".$row1[2]."</font></b>";
				echo "<td align='right'><font text color=".$AzulClar.">$".number_format($row1[0],",","",".")."</td></tr>";
				$ingresos += $row1[0];
			}
		}
		$time['V4']=time()-$time['V4'];
		
		$query = "DROP table ".$table;
		$err = mysql_query($query,$conex) or die("error ".mysql_errno()."_ en el query:".$query."_".mysql_error());
		$query = "DROP table ".$table1;
		$err = mysql_query($query,$conex) or die("error ".mysql_errno()."_ en el query:".$query."_".mysql_error());;
		$query = "DROP table ".$table2;
		$err = mysql_query($query,$conex) or die("error ".mysql_errno()."_ en el query:".$query."_".mysql_error());;



		echo "<tr><td align=left bgcolor=".$AmarQuem."><b><font text color=".$AzulText.">TOTAL INGRESOS</font></b>";
		echo "<td align='right' bgcolor=".$AmarQuem."><b><font text color=".$AzulText.">$ ".number_format($ingresos,0,"",",")."</td></tr>";
		echo "</table>";


		/*BUSCAR NUEVAS NOTAS CRÉDITO*/
		$time['NC1']=time();
		$table1="1".date("Mdis");
		/*Registros que no hallan estado en otros cuadres*/
		$q= "CREATE TEMPORARY TABLE IF NOT EXISTS ".$table1." as "
		."SELECT Rdevta,Rfpfpa,Rfpvfp,Rfpfue,Rfpnum, ".$tipo_tablas."_000022.Fecha_data "
		."FROM ".$tipo_tablas."_000022, ".$tipo_tablas."_000021 "
		."WHERE	Rfpfue	= '".$Ccofnc."' "
		."and	".$tipo_tablas."_000022.Fecha_data >= '$fecha' "
		."and	Rfpcco	= '".$wcco."' "
		."and	NOT EXISTS (SELECT	Cdefue, Cdenum, Cdefpa "
		."					FROM	".$cuadre." "
		."					WHERE 	".$cuadre.".Cdefue = ".$tipo_tablas."_000022.Rfpfue "
		."					and		".$cuadre.".Cdenum = ".$tipo_tablas."_000022.Rfpnum "
		."					and		".$cuadre.".Cdefpa = ".$tipo_tablas."_000022.Rfpfpa "
		."					and		".$cuadre.".Cdeest = 'on' "
		."					and 	".$cuadre.".Cdecaj = '".$wcaja."' "
		."					and 	".$cuadre.".Cdecco = '".$wcco."' ) "
		."and	Rfpest = 'on' "
		."and	Rdefue = Rfpfue "
		."and	Rdenum = Rfpnum "
		."and	Rdeest = 'on' ";

		$res1 = mysql_query($q,$conex) or die("error ".mysql_errno()."_ en el query:".$q."_".mysql_error());;
		$time['NC1']=time()-$time['NC1'];
		
		$query = "DROP table ".$cuadre;
		$err = mysql_query($query,$conex) or die("error ".mysql_errno()."_ en el query:".$query."_".mysql_error());;

		echo "<BR><BR><table border='1' width='400'>";
		echo "<tr><td colspan='2' align='center' bgcolor=".$AguaOsc."><b><font text color=".$AzulText.">NUEVAS NOTAS CRÉDITO</font></b></TR>";
		
		$time['NC2']=time();
		$q="SELECT Sum(Rfpvfp), Rfpfpa, Fpades "
		." FROM $table1, ".$tipo_tablas."_000023, ".$tipo_tablas."_000010, ".$tipo_tablas."_000016  "
		."WHERE	Fpacod = Rfpfpa "
		."and	Mencon = '801' "
		."and	Mendoc = Rdevta "
		."and	Menest = 'on' "
		."and	Vennum = Menfac "
		."and	Vencaj = '".$wcaja."' "
		." group by Rfpfpa";
		
		
		$res1 = mysql_query($q,$conex) or die("error ".mysql_errno()."_ en el query:".$q."_".mysql_error());;
		echo mysql_error();
		$num1 = mysql_num_rows($res1);
		$ingresos=0;
		if ($num1 > 0)	{
			echo "<tr><td align=left bgcolor=".$AguaClar."><b><font text color=".$AzulText.">FORMA DE PAGO</font></b>";
			echo "<td align='center' bgcolor=".$AguaClar."><b><font text color=".$AzulText.">VALOR TOTAL</td></tr>";
			for($i=0;$i<$num1;$i++) {
				$row1=mysql_fetch_row($res1);
				echo "<tr><td align=left><font text color=".$AzulClar.">".$row1[1]."-".$row1[2]."</font></b>";
				echo "<td align='right'><font text color=".$AzulClar.">$".number_format($row1[0],",","",".")."</td></tr>";
				$ingresos += $row1[0];
			}
		}
		$time['NC2']=time()-$time['NC2'];
		$query = "DROP table ".$table;
		$err = mysql_query($query,$conex) or die("error ".mysql_errno()."_ en el query:".$query."_".mysql_error());;
		$query = "DROP table ".$table1;
		$err = mysql_query($query,$conex) or die("error ".mysql_errno()."_ en el query:".$query."_".mysql_error());

		echo "<tr><td align=left bgcolor=".$AmarQuem."><b><font text color=".$AzulText.">TOTAL NUEVAS NOTAS CRÉDITO</font></b>";
		echo "<td align='right' bgcolor=".$AmarQuem."><b><font text color=".$AzulText.">$".number_format($ingresos,0,"",",")."</td></tr>";
		echo "</table>";

		echo "<input type='HIDDEN' name= 'wcco' value='".$wcco."'>";
		echo "<input type='HIDDEN' name= 'wnomcco' value='".$wnomcco."'>";
		echo "<input type='HIDDEN' name= 'Ccofre' value='".$Ccofre."'>";
		echo "<input type='HIDDEN' name= 'Ccofnc' value='".$Ccofnc."'>";
		echo "<input type='HIDDEN' name= 'tipo_tablas' value='".$tipo_tablas."'>";
		echo "<br><br><input type='submit' name='cuadre' value='REPORTE DETALLADO'>";
	}
}/*
$query = "INSERT INTO cuadre_000001 (medico,fecha_data,hora_data,Tipo_tablas,CajA, Ca, Subca, Anc1, V1, V2, V3, V4, Nc1,Nc2,seguridad)";
$query = $query."values ('cuadre', '".date('Y-m-d')."', '".date('H:i:s')."', '".$tipo_tablas." prueba', '".$wcaja."',".$time['CA'].", ".$time['SubCA'].", ".$time['ANC1'].", ".$time['V1'].", ".$time['V2'].", ".$time['V3'].", ".$time['V4'].", ".$time['NC1'].", ".$time['NC2'].",'A-cuadre')";
$err = mysql_query($query,$conex);
echo mysql_error();*/
include_once("free.php");
?>
</body>
</html>
