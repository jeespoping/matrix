<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Duplicar Maestro de Personal (T34 a T34)</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro197.php Ver. 2017-07-05</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='000001_pro197.php' method=post>";
	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	

	

	if(!isset($wanop) or !isset($wanod) or !isset($wporc) or !isset($wemp) or $wemp == "Seleccione")
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>DUPLICAR MAESTRO DE PERSONAL (T34 A T34)</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o  a Duplicar</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o  Destino</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanod' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Procentaje de Incremento</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wporc' size=5 maxlength=5></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Empresa de  Proceso</td><td bgcolor=#cccccc align=center>";
		$query = "SELECT Empcod,Empdes  from ".$empresa."_000153 order by Empcod";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<select name='wemp'>";
			echo "<option>Seleccione</option>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				echo "<option>".$row[0]."-".$row[1]."</option>";
			}
			echo "</select>";
		}
		echo "</td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$wemp=substr($wemp,0,strpos($wemp,"-"));
		$query = "SELECT Cierre_Ppto from ".$empresa."_000048  ";
		$query = $query."  where ano = ".$wanod;
		$query = $query."    and mes = 0 ";
		$query = $query."    and Emp = '".$wemp."' ";
		$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
		$num = mysql_num_rows($err);
		$row = mysql_fetch_array($err);
		if($num > 0 and $row[0] == "on")
		{
			$query = "delete from ".$empresa."_000034  ";
			$query = $query."  where nomano =  ".$wanod;
			$query = $query."    and nomemp = '".$wemp."' ";
			$err = mysql_query($query,$conex);
			
			//                 0       1        2       3      4       5       6       7       8       9       10      11     12       13      14     15       16
			$query = "SELECT Nomemp, Nomano, Nomcco, Nomcod, Nomofi, Nomnom, Nomhco, Nommin, Nommfi, Nombas, Nompre, Nomrec, Nomaju, Nommaj, Nombom, Nomobs, Nomtip from ".$empresa."_000034  ";
			$query = $query."  where nomano =  ".$wanop;
			$query = $query."    and nomemp = '".$wemp."' ";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				for($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					if($wporc != 0)
					{
						$row[9] = $row[9] * (1 + ($wporc / 100));
						$row[9] = round($row[9],1);
					}
					$row[7] = 1;
					$row[8] = 12;
					$query = "insert ".$empresa."_000034 (medico, fecha_data, hora_data,Nomemp, Nomano, Nomcco, Nomcod, Nomofi, Nomnom, Nomhco, Nommin, Nommfi, Nombas, Nompre, Nomrec, Nomaju, Nommaj, Nombom, Nomobs, Nomtip, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanod.",'".$row[2]."','".$row[3]."','".$row[4]."','".$row[5]."',".$row[6].",".$row[7].",".$row[8].",".$row[9].",".$row[10].",".$row[11].",".$row[12].",".$row[13].",".$row[14].",'".$row[15]."','".$row[16]."','C-".$empresa."')";
					//echo $query."<br>";
					$err1 = mysql_query($query,$conex);
					if ($err1 != 1)
						echo mysql_errno().":".mysql_error()."<br>";
					else
					{
						$k++;
						echo "REGISTRO INSERTADO  : ".$k."<br>";
					}
				}
				echo "<B>REGISTROS ADICIONADOS : ".$k."</B><BR>";
			}
		}
		else
		{
			echo "<center><table border=0 aling=center>";
			echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
			echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>EL PERIODO  ESTA CERRADO !! -- NO SE PUEDE EJECUTAR ESTE PROCESO</MARQUEE></FONT>";
			echo "<br><br>";			
		}
	}
}
?>
</body>
</html>
