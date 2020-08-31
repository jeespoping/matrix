<html>
<head>
  <title>MATRIX</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"></head>
<body BGCOLOR="">
<font size=2>
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Generacion Ordenes de Compra en Archivos Planos</font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> Ordenes_Plano.php Ver. 2008-11-12</b></font></td></tr></table>
</center>
<?php
include_once("conex.php");

	session_start();
	if(!isset($_SESSION['user']))
		echo "Error Usuario NO Registrado La Sesion Se Ha Cerrado";
	else
	{
		$key = substr($user,2,strlen($user));
		echo "<input type='HIDDEN' name= 'key' value='".$key."'>";
		echo "<form name='Control' action='Ordenes_Plano.php' method=post>";
		

		

		echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($word))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>GENERACION DE ORDENES DE COMPRA EN ARCHIVOS PLANOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Orden de Compra</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='word' size=7 maxlength=7></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$datafile="../../planos/UVGLOBAL_".$word.".txt";
			$query  = "SELECT Mencco from ".$empresa."_000010 ";
			$query .= " where Mencon = '900' ";
			$query .= "   and Mendoc = '".$word."' ";
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error()."<br>");
			$row = mysql_fetch_array($err);
			$bodega=$row[0];
			echo "<center><table border=1>";				
			echo "<tr><td align=center rowspan=4><IMG SRC='/MATRIX/images/medical/root/logo1.jpg' ></td>";				
			echo "<td align=center bgcolor=#dddddd><font size=5><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></font></td></tr>";
			echo "<tr><td  bgcolor=#dddddd colspan=2  align=center><font size=4>GENERACION DE ORDENES DE COMPRA EN ARCHIVOS PLANOS</font></td></tr>";
			echo "<tr><td bgcolor=#dddddd  colspan=2  align=center><font size=2>ORDEN DE COMPRA NRO. : ".$word."</font></td></tr>";
			//                  0        1      2       3       4      5
			$query  = "SELECT Mdeart, Artnom, Mdecan, Mtavan, Mtafec, Mtavac  from ".$empresa."_000011, ".$empresa."_000001,".$empresa."_000026 ";
			$query .= " where Mdecon = '900' ";
			$query .= "   and Mdedoc = '".$word."' ";
			$query .= "   and Mdeart = Artcod ";
			$query .= "   and Artcod = MID(Mtaart,1,LOCATE('-',Mtaart)-1)";
			$query .= "   and MID(Mtatar,1,LOCATE('-',Mtatar)-1) = 'TP' ";
			$query .= "   and MID(Mtacco,1,LOCATE('-',Mtacco)-1) = '".$bodega."' ";
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error()."<br>");
			$num = mysql_num_rows($err);
			if ($num > 0)
			{
			 	$file = fopen($datafile,"w+");
				 for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);	
					$registro=$row[0];
					$registro=$registro.",".str_replace(",","-",$row[1]);
					$registro=$registro.",".$row[2];
					if($row[4] >= date("Y-m-d"))
						$registro=$registro.",".$row[3];
					else
						$registro=$registro.",".$row[5];
					$registro=$registro.chr(13).chr(10);
	  				fwrite ($file,$registro);
	  			}
	   			fclose ($file);
	   			$ruta="..\..\planos";
	   			echo "<tr><td bgcolor=#dddddd  colspan=2 align=center><b><A href=".$ruta.">Haga Click Para Bajar el Archivo</A></b></td></tr></center>";
	   		}
	   	}
   	}
?>
</font>
</body>
</html>