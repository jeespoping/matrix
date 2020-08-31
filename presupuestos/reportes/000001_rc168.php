<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Detalle Movimiento Tercero</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc168.php Ver. 2015-03-20</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc168.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wnit) or !isset($wgru) or !isset($wrub) or !isset($wper1)  or !isset($wper2) or !isset($wcco1) or !isset($wcco2) or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>DETALLE MOVIMIENTO TERCERO</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco2' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Nit</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wnit' size=15 maxlength=15></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Grupo</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wgru' size=3 maxlength=3></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Rublo</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wrub' size=3 maxlength=3></td></tr>";
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
			$wempt = $wemp;
			$wemp = substr($wemp,0,2);	
			//                 0      1      2      3      4    
			$query = "SELECT expcco,cconom,expper,expexp,expmon from ".$empresa."_000011,".$empresa."_000005 ";
			$query = $query."  where expano = ".$wanop;
			$query = $query."    and expcco between '".$wcco1."' and '".$wcco2."'";
			$query = $query."    and expper between ".$wper1." and ".$wper2;
			$query = $query."    and expnit = '".$wnit."' ";
			$query = $query." 	 and expemp = '".$wemp."' ";  
			$query = $query."    and expcpr = '".$wrub."' ";
			$query = $query."    and expcco = ccocod ";
			$query = $query." 	 and expemp = ccoemp ";
			if($wgru != "Todos")
				$query = $query." 	 and ccouni = '".$wgru."' ";  
			$query = $query."   order by 1,3 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$tot = 0;
			echo "<table border=1>";
			echo "<tr><td colspan=4 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=4 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=4 align=center>DETALLE MOVIMIENTO TERCERO</td></tr>";
			echo "<tr><td colspan=4 align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. " A&Ntilde;O : ".$wanop."</td></tr>";
			echo "<tr><td colspan=4 align=center>UNIDAD INICIAL : ".$wcco1. " UNIDAD FINAL : ".$wcco2."</td></tr>";
			echo "<tr><td colspan=4 align=center>TERCERO : ".$wnit."</td></tr>";
			echo "<tr><td colspan=4 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td><b>C.COSTOS</b></td><td><b>PERIODO</b></td><td><b>EXPLICACION</b></td><td align=right><b>VALOR</b></td></tr>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$tot += $row[4];
				echo "<tr><td>".$row[0]."-".$row[1]."</td><td>".$row[2]."</td><td>".$row[3]."</td><td align=right>".number_format((double)$row[4],0,'.',',')."</td>";
			}
			echo "<tr><td bgcolor=#999999 colspan=3><b>TOTAL GENERAL</b></td><td bgcolor=#999999 align=right><b>".number_format((double)$tot,2,'.',',')."</b></td>";	
		}
	}
?>
</body>
</html>
