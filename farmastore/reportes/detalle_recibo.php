<html>
<head>
  <title>DETALLE VENTA - FARSTORE</title>
</head>
<body>
<?php
include_once("conex.php");
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
	

	


	$wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
	$wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
	$wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
	$wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro

	$wcf3="#FFDBA8";	//COLOR DEL FONDO 3 PARA RESALTAR -- Amarillo quemado claro
	$wcf4="#A4E1E8";	//COLOR DEL FONDO 4 -- Aguamarina claro
	$wcf5="#57C8D5";	//COLOR DEL FONDO 5 -- Aguamarina Oscuro
	$wclam="#A4E1E8";	//COLOR DE LA LETRA -- Aguamarina Clara

	$wcolspan=6;
	echo "<center><table border width='350'>";
	echo "<tr><td align=center colspan='2' ><img src='/matrix/images/medical/farmastore/logo farmastore.png' WIDTH=388 HEIGHT=70></td></tr>";
	echo "<tr><td align=center colspan='2' bgcolor=".$wcf2."><font size=3 size=3 face='arial' color=#FFFFFF><b>REPORTES DE VENTAS  </b></font size=3></td></tr>";
	echo "<tr><td align=center colspan='2' bgcolor=".$wcf2."><font size=3 size=3 face='arial' color=#FFFFFF><b>DETALLE DE RECIBO </b></font size=3></td></tr>";
	echo "</table><br><br>";

	//	Venfec 		: Fecha de la Venta 	-- farstore_000016 ENCAVEZADO VENTAS relaciona Venfac con Fenfac (000018)
	//	Vennum 		: Numero de la venta	-- farstore_000018 ENCABEZADO FACTURA relaciona Fenfac con Rdefac (000021)
	//	Rdefac 		: Numero de la factura	-- farstore_000021 DETALLE RECIBO
	//	Rdefue 		: Fuente del recibo		-- farstore_000021 DETALLE RECIBO se relaciona con Rfpfue (farstore_000022)
	//	Rdenum 		: Fuente del recibo		-- farstore_000021 DETALLE RECIBO se relaciona con Rfpnum (farstore_000022)
	//	Seguridad	: Usuario ing. recibo	-- farstore_000021 DETALLE RECIBO
	//	Cdefpa		: Código forma pago		-- farstore_000037 DETALLE CUADRE CAJA relaciona Cdefue,Cdenum,Cdefpa con
	//																				Rfpfue, Rfpnum, Rfpfpa (farstore_000022)
	//	Fpades		: Descrip. forma pago	-- farstore_000023 FORMAS DE PAGO relaciona Fpacod con Cdefpa (farstore_000037)
	//	Rfpvfp		: Valor forma de pago	-- farstore_000022 DETALLE RECIBO FORMA PAGO
	//	Cdevrf		: Saldo recibo en caja	-- farstore_000037 DETALLE CUADRE CAJA
	
	
	$q=	"SELECT * "
	."FROM farstore_000016, farstore_000003, farstore_000008, farstore_000041 , usuarios "
	."WHERE Vennfa	= '".$Rdefac."' "
	."and	Ccocod	= Vencco "
	."and	Concod	= Vencon "
	."and	Clidoc	= Vennit "
	."and	codigo	= Venusu ";
	$res = mysql_query($q,$conex);
	$row=mysql_fetch_array($res);
	echo "<center><table border='1' width='600'>";
	echo "<tr><td align=center colspan='$wcolspan' bgcolor=".$wcf2."><font size=3 size=3 size=3 face='arial' color=#FFFFFF><b>VENTA # ".$row["Vennum"]."</b></td></tr>";
	echo "<tr><td colspan='$wcolspan' align='center' bgcolor=".$wcf5."><b><font size=3 face='arial' color=".$wclfg." >INFORMACIÓN DEL CLIENTE</td></tr>";

	echo "<tr><td colspan='".($wcolspan/3)."'><font size=2 face='arial' color=".$wcf2."><b>Nombre:</b> ".$row["Clinom"]."</td>";
	echo "<td colspan='".($wcolspan/3)."'><font size=2 face='arial' color=".$wcf2."><b>Código Empresa:</b> ".$row["Vencod"]."</td>";
	echo "<td colspan='".($wcolspan/3)."'><font size=2 face='arial' color=".$wcf2."><b>NIT:</b> ".$row["Vencod"]."</td></tr>";

	echo "<tr><td colspan='".($wcolspan/3)."'><font size=2 face='arial' color=".$wcf2."><b>Dirección:</b> ".$row["Clidir"]."</td>";
	echo "<td colspan='".($wcolspan/3)."'><font size=2 face='arial' color=".$wcf2."><b>Telefono:</b> ".$row["Clite1"]."</td>";
	echo "<td colspan='".($wcolspan/3)."'><font size=2 face='arial' color=".$wcf2."><b>Email:</b> ".$row["Climai"]."</tr>";
	

	echo "<tr><td colspan='$wcolspan' align='center' bgcolor=".$wcf5."><b><font size=3 face='arial' color=".$wclfg." >INFORMACIÓN VENTA</td></tr>";

	echo "<tr><td colspan='".($wcolspan/3)."'><font size=2 face='arial' color=".$wcf2."><b>Concepto:</b> ".$row["Vencon"]."-".$row["Condes"]."</td>";
	echo "<td colspan='".($wcolspan-2)."'><font size=2 face='arial' color=".$wcf2."><b>CC:</b> ".$row["Vencco"]."-".$row["Ccodes"]."</td><tr>";
	echo "<tr><td colspan='".($wcolspan-1)."'><font size=2 face='arial' color=".$wcf2."><b>Responsable Venta:</b> ".$row["descripcion"]."</td>";
	echo "<td colspan='".($wcolspan/6)."'><font size=2 face='arial' color=".$wcf2."><b>Fecha:</b> ".$row["Venfec"]."</td>";

	echo "<tr><td colspan='".($wcolspan/3)."'><font size=2 face='arial' color=".$wcf2."><b>Valor Total:</b> $".$row["Venvto"]."</td>";
	echo "<td colspan='".($wcolspan/3)."'><font size=2 face='arial' color=".$wcf2."><b>Valor IVA:</b> $".$row["Venviv"]."</td>";
	echo "<td colspan='".($wcolspan/3)."'><font size=2 face='arial' color=".$wcf2."><b>Valor Copago:</b> $".$row["Vencop"]."</td></tr>";

	echo "<tr><td colspan='".($wcolspan/3)."'><font size=2 face='arial' color=".$wcf2."><b>Cuota Moderadora:</b> $".$row["Vencmo"]."</td>";
	echo "<td colspan='".($wcolspan/3)."'><font size=2 face='arial' color=".$wcf2."><b>Valor Descuento: $</b>".$row["Vendes"]."</td>";
	echo "<td colspan='".($wcolspan/3)."'><font size=2 face='arial' color=".$wcf2."><b>Recargo: $</b> ".$row["Venrec"]."</td></tr>";
		
	
	echo "<tr ><td colspan='$wcolspan' align='center' bgcolor=".$wcf5."><b><font size=3 face='arial' color=".$wclfg." >DETALLE DE ARTICULOS<b></td></tr>";
	echo "<tr><td align=left bgcolor=".$wcf4."><b><font size=2 face='arial' color=".$wclfg.">CÓDIGO</TD>";
	echo "<td align=left bgcolor=".$wcf4."><b><font size=2 face='arial' color=".$wclfg.">NOMBRE</TD>";
	echo "<td align=left bgcolor=".$wcf4."><b><font size=2 face='arial' color=".$wclfg.">CANT.</TD>";
	echo "<td align=left bgcolor=".$wcf4."><b><font size=2 face='arial' color=".$wclfg.">VALOR UNIDAD</TD>";
	echo "<td align=left bgcolor=".$wcf4."><b><font size=2 face='arial' color=".$wclfg.">% IVA</TD>";
	echo "<td align=left bgcolor=".$wcf4."><b><font size=2 face='arial' color=".$wclfg.">VALOR TOTAL</TD>";

	$q=	"SELECT * "
	."FROM farstore_000017, farstore_000001 "
	."WHERE	Vdenum	= ".$Rdenum." "
	."and	Artcod	= Vdeart";
	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);
	if ($num > 0)
	{
		for($i=0;$i<$num;$i++) {
			$row=mysql_fetch_array ($res);
			echo "<tr><td><font size=2 face='arial' color=".$wcf2.">".$row["Artcod"]."</TD>";
			echo "<td><font size=2 face='arial' color=".$wcf2." >".$row["Artnom"]."</TD>";
			echo "<td><font size=2 face='arial' color=".$wcf2.">".$row["Vdecan"]."</TD>";
			echo "<td><font size=2 face='arial' color=".$wcf2.">$".$row["Vdevun"]."</TD>";
			echo "<td><font size=2 face='arial' color=".$wcf2.">".$row["Vdepiv"]."</TD>";
			echo "<td><font size=2 face='arial' color=".$wcf2.">$".($row["Vdecan"]*(1+($row["Vdepiv"]/100))*$row["Vdevun"])."</TD>";
		}
	}
	echo "</table>";

	$q= "SELECT Fenfec, Renvca, Rdevca "
	."FROM  farstore_000018, farstore_000020, farstore_000021 "
	."WHERE Fenfac	= '".$Rdefac."' "
	."and	Rdefue	= '".$Rdefue."' "
	."and	Rdenum	= '".$Rdenum."' "
	."and	Rdefac	= '".$Rdefac."' "
	."and	Renfue	= '".$Rdefue."' "
	."and	Rennum	= '".$Rdenum."' ";

	$res = mysql_query($q,$conex);

	$num = mysql_num_rows($res);
	if($num > 0){
		$row=mysql_fetch_array($res);
		$Fenfec=$row[0];
		$Renvca=$row[1];
		$Rdevca=$row[2];
	}else{

		$q= "SELECT Renvca, Rdevca "
		."FROM  farstore_000020, farstore_000021 "
		."WHERE Rdefue	= '".$Rdefue."' "
		."and	Rdenum	= '".$Rdenum."' "
		."and	Rdefac	= '".$Rdefac."' "
		."and	Renfue	= '".$Rdefue."' "
		."and	Rennum	= '".$Rdenum."' ";
		$res = mysql_query($q,$conex);
		$num = mysql_num_rows($res);
		if($num > 0){
			$row=mysql_fetch_array($res);
			$Fenfec=0;
			$Renvca=$row[0];
			$Rdevca=$row[1];
		}
	}

	echo "<br><br><center><table border='1' width='600'>";
	echo "<tr><td align=center colspan='$wcolspan' bgcolor=".$wcf2."><font size=3 size=3 size=3 face='arial' color=#FFFFFF><b>RECIBO DE CAJA</b></td></tr>";
	echo "<tr><td colspan='".($wcolspan/3)."'><font size=2 face='arial' color=".$wcf2."><b>Fuente:</b> ".$Rdefue."</td>";
	echo "<td colspan='".($wcolspan/3)."'><font size=2 face='arial' color=".$wcf2."><b>Número:</b> ".$Rdenum."</td>";
	echo "<td colspan='".($wcolspan/3)."'><font size=2 face='arial' color=".$wcf2."><b>Factura:</b> ".$Rdefac."</td></tr>";

	echo "<tr><td colspan='".($wcolspan/3)."'><font size=2 face='arial' color=".$wcf2."><b>Fecha Factura :</b> ".$Fenfec."</td>";
	echo "<td colspan='".($wcolspan/3)."'><font size=2 face='arial' color=".$wcf2."><b>Valor de la factura:</b> ".$Renvca."</td>";
	echo "<td colspan='".($wcolspan/3)."'><font size=2 face='arial' color=".$wcf2."><b>Valor total del Recibo:</b> $".$Rdevca."</td></tr>";

	echo "<tr><td colspan='$wcolspan' align='center' bgcolor=".$wcf5."><b><font size=3 face='arial' color=".$wclfg." >FORMAS DE PAGO</td></tr>";

	echo "<tr><td align=left bgcolor=".$wcf4." colspan='".($wcolspan/3)."'><b><font size=2 face='arial' color=".$wclfg.">FORMA DE PAGO</TD>";
	echo "<td align=left bgcolor=".$wcf4." colspan='".($wcolspan/6)."'><b><font size=2 face='arial' color=".$wclfg.">VALOR</TD>";
	echo "<td align=left bgcolor=".$wcf4." colspan='".($wcolspan/6)."'><b><font size=2 face='arial' color=".$wclfg.">DOC. ANEXO</TD>";
	echo "<td align=left bgcolor=".$wcf4." colspan='".($wcolspan/3)."'><b><font size=2 face='arial' color=".$wclfg.">OBSERVACIONES</TD>";

	$q="SELECT Rfpfpa, Rfpvfp, Rfpdan, Rfpobs, Fpades  "
	."FROM farstore_000022, farstore_000023 "
	."WHERE	Rfpfue	= '".$Rdefue."' "
	."and		Rfpnum	= '".$Rdenum."' "
	."and		Fpacod	= Rfpfpa";

	$res = mysql_query($q,$conex);
	//	ECHO "<br><b>".mysql_errno()."=".mysql_error()."</b><br>";
	$num = mysql_num_rows($res);
	if ($num > 0)
	{
		for($i=0;$i<$num;$i++) {
			$row=mysql_fetch_array($res);
			echo "<tr>";
			echo "<td  colspan='".($wcolspan/3)."'><font size=2 face='arial' color=".$wcf2."> ".$row["Rfpfpa"]."-".$row["Fpades"]." </TD>";
			echo "<td colspan='".($wcolspan/6)."'><font size=2 face='arial' color=".$wcf2.">$".$row["Rfpvfp"]."</TD>";
			echo "<td colspan='".($wcolspan/6)."'><font size=2 face='arial' color=".$wcf2.">".$row["Rfpdan"]."</TD>";
			echo "<td colspan='".($wcolspan/3)."'><font size=2 face='arial' color=".$wcf2.">".$row["Rfpobs"]."</TD></tr>";
		}
	}
}


?>
</body>
</html>