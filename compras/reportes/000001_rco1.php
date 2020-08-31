<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Analisis de las Compras x Articulo</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rco1.php Ver. 1.00</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rco1.php' method=post>";
		if(!isset($wanop) or !isset($wper1)  or !isset($wper2)  or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>ANALISIS DE LAS COMPRAS X ARTICULO</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Año de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$sum=0;
			$pos=0;
			$query = "select Movart,Artnom,Artuni,sum(Movcan),sum(Movval * Movcan) as total from compras_000002,compras_000003 ";
			$query = $query."  where movano = ".$wanop;
			$query = $query."  and movmes between ".$wper1." and ".$wper2;
			$query = $query."  and movart = artcod ";
			$query = $query."  group by movart,artnom,artuni ";
			$query = $query."  order by artuni ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<table border=1>";
			echo "<tr><td colspan=10 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=10 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=10 align=center>ANALISIS DE LAS COMPRAS X ARTICULO</td></tr>";
			echo "<tr><td colspan=10 align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. " AÑO : ".$wanop."</td></tr>";
			echo "<tr><td bgcolor=#999999 align=center><b>POSICION</b></td><td bgcolor=#999999 align=center><b>CODIGO</b></td><td bgcolor=#999999><b>DESCRIPCION</b></td><td bgcolor=#999999 align=center><b>UNIDAD</b></td><td bgcolor=#999999 align=right><b>CANTIDAD<BR>COMPRADA</b></td><td bgcolor=#999999 align=right><b>VALOR UNITARIO<BR>LICITADO</b></td><td bgcolor=#999999 align=right><b>VALOR<BR>COMPRADO</b></td><td bgcolor=#999999 align=right><b>VALOR X<BR>LICITACION</b></td><td bgcolor=#999999 align=right><b>DIFERENCIA</b></td><td bgcolor=#999999 align=right><b>PORCENTAJE</b></td></tr>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$query = "select max(Litvun) from compras_000001 ";
				$query = $query."  where litano =  ".$wanop; 
				$query = $query."  and litart = '".$row[0]."'";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				$row1 = mysql_fetch_array($err1);
				if($row1[0] > 0)
				{
					$vul=$row1[0];
					$vxl=$vul * $row[3];
					$dif=$row[4] - $vxl;
					$sum+= $dif;
					$pos++;
					if(($pos % 2) == 0)
						$color="#cccccc";
					else
						$color="#dddddd";
					$por=abs(($dif / $row[4]) * 100);
					echo "<tr><td bgcolor=".$color." align=center>".$pos."</td><td bgcolor=".$color." align=center>".$row[0]."</td><td bgcolor=".$color.">".$row[1]."</td><td bgcolor=".$color." align=center>".$row[2]."</td><td bgcolor=".$color." align=right>".number_format((double)$row[3],0,'.',',')."</td><td bgcolor=".$color." align=right>".number_format((double)$vul,0,'.',',')."</td><td bgcolor=".$color." align=right>".number_format((double)$row[4],0,'.',',')."</td><td bgcolor=".$color." align=right>".number_format((double)$vxl,0,'.',',')."</td><td bgcolor=".$color." align=right>".number_format((double)$dif,0,'.',',')."</td>";
					if($por >= 10 and $por < 50)
						echo "<td bgcolor=#FFFF00 align=right><font color=#000066>".number_format((double)$por,2,'.',',')."%</font></td></tr>";
					elseif ($por >= 50)
								echo "<td bgcolor=#FFFF00 align=right><font color=#CC0000><b>".number_format((double)$por,2,'.',',')."%</b></font></td></tr>";
							else
								echo "<td bgcolor=#ffffff align=right><font color=#000066>".number_format((double)$por,2,'.',',')."%</font></td></tr>";
				}
			}
			echo "<tr><td bgcolor=#999999 colspan=9><b>TOTAL PERDIDA O UTILIDAD</b></td><td bgcolor=#999999 align=right><b>".number_format((double)$sum,2,'.',',')."</b></td></tr>";
		}
	}
?>
</body>
</html>
