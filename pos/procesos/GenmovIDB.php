<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Generacion de Movimiento de Inventarios Inter_Base de Datos</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> GenmovIDB.php Ver. 2010-09-21</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='GenmovIDB.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'nit' value='".$nit."'>";
		echo "<center><input type='HIDDEN' name= 'empresa1' value='".$empresa1."'>";
		echo "<center><input type='HIDDEN' name= 'empresa2' value='".$empresa2."'>";
		echo "<center><input type='HIDDEN' name= 'concepto1' value='".$concepto1."'>";
		echo "<center><input type='HIDDEN' name= 'concepto2' value='".$concepto2."'>";
		if(!isset($wnum) or !isset($wpor) or !isset($wccod) or (isset($wccod) and $wccod == "0-NO APLICA"))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>GENERACION DE MOVIMIENTO DE INVENTARIOS INTER_BASE DE DATOS</td></tr>";
			echo "<tr><td align=center colspan=2>Usuario En Operacion : ".$key."</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Numero de Documento de Concepto ".$concepto1."</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wnum' size=8 maxlength=8></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>C.C. DESTINO : </td><td bgcolor=#cccccc align=center>";
			$query = "SELECT Ccucco, Ccodes from ".$empresa2."_000054, ".$empresa2."_000003  where Ccuusu='".$key."' and Ccutip='D' and Ccuest='on' and Ccucco=Ccocod order by Ccucco";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wccod'>";
				echo "<option>0-NO APLICA</option>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				echo "</select>";
			}
			echo "</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Porcentaje de Recargo</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wpor' size=5 maxlength=5></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			// BLOQUEO DE TABLAS A TRABAJAR
			$query  = "lock table ".$empresa2."_000008 LOW_PRIORITY WRITE ";
			$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO CONSECUTIVO");
			
			// CONSULTA DE CONSECUTIVO E INCREMENTO
			$query = "select Concon from ".$empresa2."_000008 where Concod='".$concepto2."'";
			$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO CONSECUTIVO");
			$row = mysql_fetch_array($err1);
			$wdoct=$row[0] + 1;
			
			// ACTUALIZACION DE CONSECUTIVO
			$query =  " update ".$empresa2."_000008 set Concon = Concon + 1 where Concod='".$concepto2."'";
			$err1 = mysql_query($query,$conex) or die("ERROR INCREMENTANDO CONSECUTIVO");
			
			// DESBLOQUEO DE TABLAS A TRABAJAR
			$query = " UNLOCK TABLES";													
			$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO ENCABEZADO Y CONSECUTIVO");
			
			// INSERCION DE DATOS DEL ENCABEZADO DEL DOCUMENTO	
			$medico=$empresa2;
			$fecha = date("Y-m-d");
			$hora = (string)date("H:i:s");
			$ano = date("Y");
			$mes = date("m");
			$seguridad="C-".$medico;
			$query  = "insert into ".$empresa2."_000010 (medico,fecha_data,hora_data, Menano, Menmes, Mendoc, Mencon, Menfec, Mencco, Menccd, Mendan, Menpre, Mennit, Menusu, Menfac, Menobs, Menest, Seguridad) ";
			$query .= "select '".$medico."','".$fecha."','".$hora."',".$ano.",".$mes.",'".$wdoct."','".$concepto2."','".$fecha."','".substr($wccod,0,strpos($wccod,"-"))."','0','',0,'".$nit."','".$key."','','DOCUMENTO GENERADO ELECTRONICAMENTE','off','".$seguridad."' from ".$empresa1."_000010  ";
			$query .= "where Mencon = '".$concepto1."'"; 
			$query .= "  and Mendoc = '".$wnum."'";
			$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()." ERROR GRABANDO ENCABEZADO DEL DOCUMENTO");
			
			// INSERCION DE DATOS DEL DETALLE DEL DOCUMENTO	
			$query  = "insert into ".$empresa2."_000011 (medico,fecha_data,hora_data, Mdecon, Mdedoc, Mdeart, Mdecan, Mdevto, Mdepiv, Mdefve, Mdenlo, Mdeest, Seguridad) ";
			$query .= "select '".$medico."','".$fecha."','".$hora."','".$concepto2."','".$wdoct."', Mdeart, Mdecan, Mdevto* ".$wpor.", Mdepiv,'0000-00-00','.','on','".$seguridad."' from ".$empresa1."_000011 ";
			$query .= "where Mdecon = '".$concepto1."'"; 
			$query .= "  and Mdedoc = '".$wnum."'";
			$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()." ERROR GRABANDO DETALLE DEL DOCUMENTO");
			
			
			
			echo "<table border=1>";
			echo "<tr><td bgcolor=#dddddd align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td bgcolor=#dddddd align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td bgcolor=#dddddd align=center>GENERACION DE MOVIMIENTO DE INVENTARIOS INTER_BASE DE DATOS</td></tr>";
			echo "<tr><td bgcolor=#dddddd align=center><b>SE GENERO EL DOCUMENTO : ".$wdoct." DEL CONCEPTO : ".$concepto2."</b></td></tr>";
			echo "<tr><td bgcolor=#dddddd align=center><input type='submit' value='ENTER'></td></tr>";
		}
	}
?>
</body>
</html>
