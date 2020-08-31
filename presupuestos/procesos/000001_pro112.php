<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Calculo de Factor Prestacional y Recargos Presupuestados</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro112.php Ver. 2017-06-07</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro112.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wper1) or !isset($wper2) or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2 or !isset($wemp) or $wemp == "Seleccione" or !isset($wtip) or (strtoupper($wtip) != "AN"  and strtoupper($wtip) != "AC"))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTACION</td></tr>";
			echo "<tr><td align=center colspan=2>CALCULO DE FACTOR PRESTACIONAL Y RECARGOS PRESUPUESTADOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Tipo de Proceso (AN (A&ntilde;o Anterior) / AC (A&ntilde;o Actual)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wtip' size=2 maxlength=2></td></tr>";
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
			$k=0;
			if(strtoupper($wtip) == "AN")
				$wanopa =$wanop - 1;
			else
				$wanopa =$wanop;
			$query = " CREATE TEMPORARY TABLE if not exists temp1 as ";
			$query = $query." SELECT Norcco,sum(Normon) as Norcan,sum(Normon*Norpre) as Norpre ,sum(Normon*Norrec) as Norrec from ".$empresa."_000036 ";
			$query = $query."  where Norano = ".$wanopa;
			$query = $query."    and Norfil = '".$wemp."' ";
			$query = $query."    and Norcod in ('0001','0013') ";
			$query = $query."    and Norper between ".$wper1." and ".$wper2;
			$query = $query."    Group by Norano, Norcco ";
			$err = mysql_query($query,$conex);
			$query = " SELECT Norcco, Norcan, Norpre, Norrec from temp1 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$fp=$row[2] / $row[1];
				$re=$row[3] / $row[1];
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
				$query = "update ".$empresa."_000034 set Nompre=".$fp.",Nomrec=".$re." where Nomano=".$wanop." and Nomcco='".$row[0]."' and Nomemp='".$wemp."'";
				$err1 = mysql_query($query,$conex);
				$k++;
				echo "REGISTRO ACTUALIZADO NRO : ".$k."<br>";
			}
			echo "<b>TOTAL REGISTROS ACTUALIZADOS : ".$k."</b>";
		}
	}
?>
</body>
</html>
