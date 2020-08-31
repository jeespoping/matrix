<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Detalle de Programas de Gastos</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc117.php Ver. 2016-03-04</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc117.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wccoi) or !isset($wccof) or !isset($wper1) or !isset($wper2) or !isset($wres) or (strtoupper ($wres) != "R" and strtoupper ($wres) != "D") or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>DETALLE DE PROGRAMAS DE GASTOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Centro de Costos Inicial</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wccoi' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Centro de Costos Final</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wccof' size=4 maxlength=4></td></tr>";
			echo "<input type='HIDDEN' name= 'call' value='".$call."'>";
			echo "<tr><td bgcolor=#cccccc align=center>Todos los Programas</td>";
			echo "<td bgcolor=#cccccc align=center><input type='checkbox' name='wtodos'></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Programa Especifico de Gastos</td><td bgcolor=#cccccc align=center>";
			$query = "SELECT Prgcod, Prgdes  from ".$empresa."_000127 order by Prgdes";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wprog'>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				echo "</select>";
			}
			echo "</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Resumido o Detallado ? (R/D)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wres' size=1 maxlength=1></td></tr>";
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
			$wres=strtoupper ($wres);
			$query = "SELECT Prgcod, Prgdes ,Gascod, Mganom, Gascco, Cconom, Gasdes, SUM(Gasval) from ".$empresa."_000127,".$empresa."_000012,".$empresa."_000028,".$empresa."_000005 ";
			if(isset($wtodos))
				$query = $query."  where Prgcod > '0' ";
			else
				$query = $query."  where Prgcod = '".substr($wprog,0,strpos($wprog,"-"))."' ";
			$query = $query."    and Prgcod = Gasprg ";
			$query = $query."    and Gasano = ".$wanop;
			$query = $query."    and Gasemp = '".$wemp."' ";
			$query = $query."    and Gasmes between ".$wper1." and ".$wper2;
			$query = $query."    and Gascco between '".$wccoi."' and '".$wccof."'";
			$query = $query."    and Gascod = Mgacod ";
			$query = $query."    and Gascco = Ccocod ";
			$query = $query."    and Gasemp = ccoemp ";
			$query = $query."  group by Prgcod, Prgdes ,Gascod, Mganom, Gascco, Cconom, Gasdes ";
			$query = $query."  order by Prgcod, Gascod ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<table border=1>";
			echo "<tr><td colspan=8 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=8 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=8 align=center>DETALLE DE PROGRAMAS DE GASTOS</td></tr>";
			echo "<tr><td colspan=8 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td colspan=8 align=center>A&Ntilde;O DE PROCESO : ".$wanop."</td></tr>";
			if(isset($wtodos))
				echo "<tr><td colspan=8 align=center>PROGRAMA : Todos</td></tr>";
			else
				echo "<tr><td colspan=8 align=center>PROGRAMA : ".$wprog."</td></tr>";
			if($wres == "D")
				echo "<tr><td bgcolor=#cccccc><b>Codigo<br>Programa</b></td><td bgcolor=#cccccc><b>descripcion<br>Programa</b></td><td bgcolor=#cccccc><b>Codigo<br>Rubro</b></td><td bgcolor=#cccccc><b>Descripcion<br>Rubro</b></td><td bgcolor=#cccccc><b>C.C.</b></td><td bgcolor=#cccccc><b>Descripcion<br> Centro Costos</b></td><td bgcolor=#cccccc><b>Explicacion</b></td><td align=right bgcolor=#cccccc><b>Monto</b></td></tr>";
			else
				echo "<tr><td bgcolor=#cccccc><b>Codigo<br>Programa</b></td><td bgcolor=#cccccc><b>descripcion<br>Programa</b></td><td  bgcolor=#cccccc colspan=6><b>Monto</b></td></tr>";
			$wruba="";
			$wproa="";
			$wproan="";
			$wtotr = 0;
			$wtotp = 0;
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($wruba != $row[2] and $wres == "D")
				{
					if($i > 0)
						echo "<tr><td colspan=7 bgcolor=#FFCC66><b>TOTAL RUBRO</b></td><td align=right bgcolor=#FFCC66><b>".number_format((double)$wtotr,0,'.',',')."</b></td></tr>";
					$wruba=$row[2];
					$wtotr=0;
				}
				if($wproa != $row[0])
				{
					if($i > 0)
						if($wres == "D")
							echo "<tr><td colspan=7 bgcolor=#99CCFF><b>TOTAL PROGRAMA</b></td><td align=right bgcolor=#99CCFF><b>".number_format((double)$wtotp,0,'.',',')."</b></td></tr>";
						else
							echo "<tr><td bgcolor=#99CCFF><b>TOTAL PROGRAMA : ".$wproa."</td><td bgcolor=#99CCFF><b>".$wproan."</b></td><td align=right bgcolor=#99CCFF colspan=6><b>".number_format((double)$wtotp,0,'.',',')."</b></td></tr>";
					$wproa=$row[0];
					$wproan=$row[1];
					$wtotp=0;
				}
				$wtotr += $row[7];
				$wtotp += $row[7];
				if($wres == "D")
					echo"<tr><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td>".$row[5]."</td><td>".$row[6]."</td><td align=right>".number_format((double)$row[7],0,'.',',')."</td></tr>";
    		}
    		if($wres == "D")
				if($i > 0)
					echo "<tr><td colspan=7 bgcolor=#FFCC66><b>TOTAL RUBRO</b></td><td align=right bgcolor=#FFCC66><b>".number_format((double)$wtotr,0,'.',',')."</b></td></tr>";
			if($i > 0)
				if($wres == "D")
					echo "<tr><td colspan=7 bgcolor=#99CCFF><b>TOTAL PROGRAMA</b></td><td align=right bgcolor=#99CCFF><b>".number_format((double)$wtotp,0,'.',',')."</b></td></tr>";
				else
					echo "<tr><td bgcolor=#99CCFF><b>TOTAL PROGRAMA : ".$wproa."</td><td bgcolor=#99CCFF><b>".$wproan."</b></td><td align=right bgcolor=#99CCFF colspan=6><b>".number_format((double)$wtotp,0,'.',',')."</b></td></tr>";
		}
	}
?>
</body>
</html>
