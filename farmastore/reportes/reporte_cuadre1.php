<html>
<head>
  <title>CUADRE CAJA - FARSTORE</title>
</head>
<body>
<?php
include_once("conex.php");

$wautor="Ana Maria Betancur V.";
$wcol=10;  //Numero de columnas que se tienen o se muestran en pantalla

$wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
$wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
$wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
$wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro

$wcf3="#FFDBA8";	//COLOR DEL FONDO 3 PARA RESALTAR -- Amarillo quemado claro
$wcf4="#A4E1E8";	//COLOR DEL FONDO 4 -- Aguamarina claro
$wcf5="#57C8D5";	//COLOR DEL FONDO 5 -- Aguamarina Oscuro
$wclam="#A4E1E8";	//COLOR DE LA LETRA -- Aguamarina Clara 


echo "<p align=right><font size=1><b>Autor: ".$wautor."</b></font></p>";

echo "<center><table border width='350'>";
echo "<tr><td align=center colspan='2' ><img src='/matrix/images/medical/farmastore/logo farmastore.png' WIDTH=388 HEIGHT=70></td></tr>";
echo "<tr><td align=center colspan='2' bgcolor=".$wcf2."><font size=3 text color=#FFFFFF><b>REPORTES DE VENTAS  </b></font></td></tr>";
echo "<tr><td align=center colspan='2' bgcolor=".$wcf2."><font size=3 text color=#FFFFFF><b>CUADRE DE CAJA </b></font></td></tr>";


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

	if(!isset($wcco)) {

		//ACA TRAIGO LAS VARIABLES DE SUCURSAL, CAJA Y TIPO INGRESO QUE REALIZA CADA CAJERO
		$q =  " SELECT cjecco, cjecaj, cjetin  "
		."   FROM farstore_000030 "
		."  WHERE cjeusu = '".$wusuario."'"
		."    AND cjeest = 'on' ";

		$res = mysql_query($q,$conex);
		$num = mysql_num_rows($res);
		if ($num > 0)
		{
		//	echo '<form action="" method="POST">';

			$row = mysql_fetch_array($res);

			$pos = strpos($row[0],"-");
			$wcco = substr($row[0],0,$pos);
			$wnomcco = substr($row[0],$pos+1,strlen($row[0]));
	/*		echo "<input type='HIDDEN' name= 'wcco' value='".$wcco."'>";
			echo "<input type='HIDDEN' name= 'wnomcco' value='".$wnomcco."'>";*/
			
			$wcaja=$row[1];
			//echo "<input type='HIDDEN' name= 'wcaja' value='".$wcaja."'>";
			$wtiping = $row[2];
			//echo "<input type='HIDDEN' name= 'wtiping' value='".$wtiping."'>";
			
			
			$q="SELECT Ccoffp, Ccofre "
			."FROM farstore_000003 "
			."WHERE Ccocod	= '".$wcco."' "
			."AND Ccoest	= 'on'";
			$err1 = mysql_query($q,$conex);
			$num1 = mysql_num_rows($res);
			if ($num1 > 0)
			{
				$row1=mysql_fetch_row($err1);
				$Ccoffp=$row1[0];
				$Ccofre=$row1[1];
			}
			//echo "<input type='HIDDEN' name= 'Ccofre' value='".$Ccofre."'>";
			
		}
		else{
			echo "EL USUARIO ESTA INACTIVO O NO TIENE PERMISO PARA FACTURAR";
		}
	}
	
	if(isset($wcco)){
		echo '<form action="cuadre_preliminar.php" method="POST">';
		
		echo "<tr><td align=center colspan='2' bgcolor=".$wcf2."><font size=3 text color=#FFFFFF><b>$wcaja</b></font></td></tr>";
		echo "</TABLE>";
		
		echo "<input type='hidden' name='wcaja' value='".$wcaja."'>";
		$pos=explode("-",$wcaja);
		$wcaja = $pos[0];
		$wnomcaj = $pos[1];
		 
		echo "<br><br><table border='1' width='400'>";
		echo "<tr><td colspan='2' align='center' bgcolor=".$wcf5."><b><font text color=".$wclfg.">SALDO ANTERIOR</font></b></TR>";
		
		/*BUSCAR EL REMANENTE DE EL ULTIMO CUADRE DE CAJA*/
		$salant=0;
		$ccant="";
		$q="SELECT Cajcua "
	 	."FROM farstore_000028 "
	 	."WHERE 	Cajcco = '".$wcco."' "
	 	."and 		Cajcod = '".$wcaja."'";
		$res = mysql_query($q,$conex);
		$num = mysql_num_rows($res);
		if ($num > 0)
		{
			$row=mysql_fetch_row($res);
			if($row[0] != 0){
				echo "<input type=hidden name='cajcua' value='".$row[0]."'>";
				$q="SELECT SUM(Cdevrf), Cdefpa, Fpades "
				."FROM farstore_000037, farstore_000023 "
				."WHERE	Cdecua	=	'".$row[0]."' "	//Pertenece al cuadre de caja anterior
				."and 	Cdecaj	=	'".$wcaja."' "
				."and	Cdecco	=	'".$wcco."' "
				."and	Cdevrf 	<>	0 "	//quedo un valor pendiente
				."and 	Fpacod	=	Cdefpa "	//El código de la forma de pago correponde al de el detalle del CuC
				."GROUP BY Cdefpa "
				."ORDER BY Cdefpa ";
				
				$res1 = mysql_query($q,$conex);
		//		echo "<br>$q<br>".mysql_errno()."=".mysql_error()."<br>";	
				$num1 = mysql_num_rows($res1);
	//			echo "<br>num=$num1<br>";
				if ($num1 > 0)
				{
					$salant=0;
					echo "<tr><td align=left bgcolor=".$wcf4."><b><font text color=".$wclfg.">FORMA DE PAGO</font></b>";
					echo "<td align='center' bgcolor=".$wcf4."><b><font text color=".$wclfg.">VALOR TOTAL</td></tr>";
					for($i=0;$i<$num1;$i++) {
						$row1=mysql_fetch_row($res1);
						echo "<tr><td align=left><font text color=".$wcf2.">".$row1[1]."-".$row1[2]."</font></b>";
						echo "<td align='right'><font text color=".$wcf2.">$".number_format($row1[0],",","",".")."</td></tr>";
						$salant+=$row1[0];
					}
				}
			}else{
				echo "<input type=hidden name='cajcua' value='0'>";
			}
		}else {
			echo "<input type=hidden name='cajcua' value='0'>";
		}
		
		echo "<tr><td align=left bgcolor=".$wcf3."><b><font text color=".$wclfg.">TOTAL SALDO ANTERIOR</font></b>";
		echo "<td align='right' bgcolor=".$wcf3."><b><font text color=".$wclfg.">$".number_format($salant,",","",".")."</td></tr>";
		echo "</table><br><br>";
		
		echo "<input type='hidden' name='ccant' value='".$ccant."'>";
		echo "<input type='hidden' name='salant' value='".$salant."'>";
		
		$ini=explode("-",$wcaja);
		$cajcod=$ini[0];

		$table=date("Mdis");
		$q= "Create table  IF NOT EXISTS ".$table." as "
		."SELECT Rfpfpa,Rfpvfp "
		."FROM farstore_000022,farstore_000021, farstore_000016 "
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
		."and Rdefue = Rfpfue "
		."				and		Rdenum = Rfpnum "
		."				and		Vennum = Rdevta "
		."and Vencaj = '".$cajcod."' "
		/*."and	EXISTS	(SELECT	Rdefue, Rdenum, Vennum, Vencaj "
		."				FROM 	farstore_000021, farstore_000016 "
		."				WHERE	farstore_000021.Rdefue = farstore_000022.Rfpfue "
		."				and		farstore_000021.Rdenum = farstore_000022.Rfpnum "
		."				and		farstore_000016.Vennum = farstore_000021.Rdevta "
		."				and		farstore_000016.Vencaj = '".$cajcod."' ) "*/
		."and	Rfpest = 'on' ";
	
		
		$res1 = mysql_query($q,$conex);
		
	/*	echo "<br>$q<br>".mysql_errno()."=".mysql_error()."<br>";	
	//	IMPRIME TODA LA TABLA TEMPORAL
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
		
//		echo mysql_errno()."=".mysql_error()."<br>";
*/
		echo "<table border='1' width='400'>";
		echo "<tr><td colspan='2' align='center' bgcolor=".$wcf5."><b><font text color=".$wclfg.">INGRESOS</font></b></TR>";
		
		$q="SELECT Sum(Rfpvfp), Rfpfpa, Fpades "
		." FROM $table, farstore_000023  "
		."WHERE	Fpacod = Rfpfpa"
		." group by Rfpfpa";
		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);
		
		$ingresos=0;
		if ($num1 > 0)
		{
			echo "<tr><td align=left bgcolor=".$wcf4."><b><font text color=".$wclfg.">FORMA DE PAGO</font></b>";
			echo "<td align='center' bgcolor=".$wcf4."><b><font text color=".$wclfg.">VALOR TOTAL</td></tr>";
			for($i=0;$i<$num1;$i++) {
				$row1=mysql_fetch_row($res1);
				echo "<tr><td align=left><font text color=".$wcf2.">".$row1[1]."-".$row1[2]."</font></b>";
				echo "<td align='right'><font text color=".$wcf2.">$".number_format($row1[0],",","",".")."</td></tr>";
				$ingresos += $row1[0];								
			}
		}
		/*echo "<input type='hidden' name='table' value='".$table."'>";*/
		$query = "DROP table ".$table;
		$err = mysql_query($query,$conex);
		
		echo "<tr><td align=left bgcolor=".$wcf3."><b><font text color=".$wclfg.">TOTAL INGRESOS</font></b>";
		echo "<td align='right' bgcolor=".$wcf3."><b><font text color=".$wclfg.">$".number_format($ingresos,",","",".")."</td></tr>";
		echo "</table>";
		
		echo "<input type='HIDDEN' name= 'wcco' value='".$wcco."'>";
		echo "<input type='HIDDEN' name= 'wnomcco' value='".$wnomcco."'>";
		echo "<input type='HIDDEN' name= 'Ccofre' value='".$Ccofre."'>";
		echo "<br><br><input type='submit' name='cuadre' value='REPORTE DETALLADO'>";		
	}
}

?>
</body>
</html>