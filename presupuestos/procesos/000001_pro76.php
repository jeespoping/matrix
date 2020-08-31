<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Consolidacion Movimiento  de Ingresos Convenios (Destino)</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro76.php Ver. 1.01</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro76.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wmesi))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE CONVENIOS</td></tr>";
			echo "<tr><td align=center colspan=2>CONSOLIDACION MOVIMIENTO  DE INGRESOS CONVENIOS (DESTINO)</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>A�o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesi' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$k=0;
			#INICIO PROGRAMA
			$query = "DELETE from ".$empresa."_000109 ";
			$query = $query." where motano = ".$wanop;
			$query = $query."   and motmes = ".$wmesi;
			$err = mysql_query($query,$conex);
			$query = "SELECT Mosano, Mosmes, Moscco, Moscon, Moscod, SUM(Moscan), SUM(Mosfto), SUM(Mosfte),Mosemp, Mostip, Moslin, '0'  from ".$empresa."_000108 ";
			$query = $query." where Mosano = ".$wanop;
			$query = $query."   and Mosmes = ".$wmesi;
			$query = $query."   and Moslin =  '1' ";
			$query = $query."   Group by  Mosano, Mosmes, Moscco, Moscon, Moscod,Mosemp, Mostip, Moslin ";
			$query = $query." UNION  ";
			$query = $query." select Mioano,Miomes,Miocco,'0','0','0',sum(Mioito),sum(Mioint),Empcin,'0',Cfalin,'0' ";
			$query = $query." from ".$empresa."_000063,".$empresa."_000061,".$empresa."_000060  ";
			$query = $query." where mioano = ".$wanop;
			$query = $query." and miomes = ".$wmesi;
			$query = $query." and miocfa = cfacod  ";
			$query = $query." and cfalin != '1' ";
			$query = $query." and cfaclas != 'no'  ";
			$query = $query." and mionit = epmcod  ";
			$query = $query." and empeva = 'S' ";
			$query = $query." group by mioano,miomes,miocco,empcin,cfalin ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$query = "insert ".$empresa."_000109 (medico,fecha_data,hora_data,Motano, Motmes, Motcco, Motcon, Motcod, Motcan, Motfto, Motfte, Motemp, Mottip, Motlin, Motcos ,Motest, seguridad) values ('".$empresa."','".$fecha."','".$hora."',".$wanop.",".$wmesi.",'".$row[2]."','".$row[3]."','".$row[4]."',".$row[5].",".$row[6].",".$row[7].",'".$row[8]."','".$row[9]."','".$row[10]."',".$row[11].",'0','C-".$empresa."')";
           			$err2 = mysql_query($query,$conex);
           			$k++;
           			echo "REGISTROS INSERTADO : ".$k."<br>";
               	}
			}
			echo "<b>NUMERO DE REGISTROS INSERTADOS : ".$k."</b><br>";
        }
}		
?>
</body>
</html>
