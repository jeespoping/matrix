<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Generacion de Documento Para Venta Total x Centro de Costos</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> VTotal.php Ver. 2015-12-29</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='VTotal.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wcco))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>GENERACION DE DOCUMENTO PARA VENTA TOTAL X CENTRO DE COSTOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Centro de Costos</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			// BLOQUEO DE TABLAS A TRABAJAR
			$query  = "lock table ".$empresa."_000008 LOW_PRIORITY WRITE ";
			$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO CONSECUTIVO");
			
			// CONSULTA DE CONSECUTIVO E INCREMENTO
			$query = "select Concon from ".$empresa."_000008 where Concod='803'";
			$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO CONSECUTIVO");
			$row = mysql_fetch_array($err1);
			$wdoct=$row[0] + 1;
			
			// ACTUALIZACION DE CONSECUTIVO
			$query =  " update ".$empresa."_000008 set Concon = Concon + 1 where Concod='803'";
			$err1 = mysql_query($query,$conex) or die("ERROR INCREMENTANDO CONSECUTIVO");
			
			// DESBLOQUEO DE TABLAS A TRABAJAR
			$query = " UNLOCK TABLES";													
			$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO ENCABEZADO Y CONSECUTIVO");
			
			// INSERCION DE DATOS DEL ENCABEZADO DEL DOCUMENTO	
			$medico = "farpmla";
			$fecha = date("Y-m-d");
			$hora = (string)date("H:i:s");
			$ano = date("Y");
			$mes = date("m");
			$seguridad="C-".$medico;
			$nit = "860013570";
			$query  = "insert into ".$empresa."_000010 (medico,fecha_data,hora_data, Menano, Menmes, Mendoc, Mencon, Menfec, Mencco, Menccd, Mendan, Menpre, Mennit, Menusu, Menfac, Menobs, Menest, Seguridad) ";
			$query .= " VALUES ('".$medico."','".$fecha."','".$hora."',".$ano.",".$mes.",'".$wdoct."','803','".$fecha."','".$wcco."','0','',0,'".$nit."','".$key."','','VENTAL TOTAL A CAFAM','on','".$seguridad."') ";
			$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()." ERROR GRABANDO ENCABEZADO DEL DOCUMENTO");
			
			// INSERCION DE DATOS DEL DETALLE DEL DOCUMENTO	
			$query  = "insert into ".$empresa."_000011 (medico,fecha_data,hora_data, Mdecon, Mdedoc, Mdeart, Mdecan, Mdevto, Mdepiv, Mdefve, Mdenlo, Mdeest, Seguridad) ";
			$query .= "select '".$medico."','".$fecha."','".$hora."','803','".$wdoct."',Artcod,Karexi,(Karexi*Karpro),Artiva,'0000-00-00','00','on','".$seguridad."' from ".$empresa."_000007,".$empresa."_000001 ";
			$query .= " where karcod = artcod ";
			$query .= "   and karcco = '".$wcco."'"; 
			$query .= "   and karexi > 0 ";
			$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()." ERROR GRABANDO DETALLE DEL DOCUMENTO");
			
			// ACUALIZACION DE KARDEX DE INVENTARIOS
			$query  = "UPDATE ".$empresa."_000007 set karexi = 0 ";
			$query .= " where karcco = '".$wcco."'"; 
			$query .= "   and karexi > 0 ";
			$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()." ERROR GRABANDO DETALLE DEL DOCUMENTO");
			
			echo "<table border=1>";
			echo "<tr><td bgcolor=#dddddd align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td bgcolor=#dddddd align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td bgcolor=#dddddd align=center>GENERACION DE DOCUMENTO PARA VENTA TOTAL X CENTRO DE COSTOS</td></tr>";
			echo "<tr><td bgcolor=#dddddd align=center><b>SE GENERO EL DOCUMENTO : ".$wdoct." DEL CONCEPTO : ".$concepto."</b></td></tr>";
			echo "<tr><td bgcolor=#dddddd align=center><input type='submit' value='ENTER'></td></tr>";
		}
	}
?>
</body>
</html>
