<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Informe de Explicaciones Para Costos</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc148.php Ver. 2016-03-18</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc148.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1)  or !isset($wper2) or ((!isset($wcco1)  or !isset($wcco2)) and !isset($wccof)) or !isset($wcodi) or !isset($wcodf) or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>INFORME DE EXPLICACIONES PARA COSTOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			if($key=="costosyp" or (isset($call) and $call == "SIG") or (isset($call) and $call=="SIC"))
			{
				echo "<tr><td bgcolor=#cccccc align=center>Unidad Inicial de Proceso</td>";
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
				echo "<tr><td bgcolor=#cccccc align=center>Unidad Final de Proceso</td>";
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco2' size=4 maxlength=4></td></tr>";
			}
			else
			{
				echo "<tr><td bgcolor=#cccccc align=center>Unidad Proceso</td><td bgcolor=#cccccc align=center>";
				$query = "SELECT Cc,cconom  from ".$empresa."_000125,".$empresa."_000005 where empleado ='".$key."'  and cc=ccocod order by Cc";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					echo "<select name='wccof'>";
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						echo "<option>".$row[0]."-".$row[1]."</option>";
					}
					echo "</select>";
				}
				echo "</td></tr>";
			}
			echo "<input type='HIDDEN' name= 'call' value='".$call."'>";
			/*
			echo "<tr><td bgcolor=#cccccc align=center>Todos los Rubros</td>";
			echo "<td bgcolor=#cccccc align=center><input type='checkbox' name='wtodos'></td></tr>";
			*/
			echo "<tr><td bgcolor=#cccccc align=center>Rubro Presupuestal Inicial</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcodin' size=3 maxlength=3 value=0>";
			$query = "SELECT mgacod,mganom from ".$empresa."_000028 order by mganom";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wcodi'>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				echo "</select>";
			}
			echo "</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Rubro Presupuestal Final</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcodfn' size=3 maxlength=3 value=0>";
			$query = "SELECT mgacod,mganom from ".$empresa."_000028 order by mganom";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wcodf'>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				echo "</select>";
			}
			echo "</td></tr>";
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
			if($wcodin==0)
			{
				if(strpos($wcodi,"-") != false)
				{
					$ini = strpos($wcodi,"-");
					$wcodi=substr($wcodi,0,$ini);
				}
			}
			else
				$wcodi=$wcodin;
			
			if($wcodfn==0)
			{
				if(strpos($wcodf,"-") != false)
				{
					$ini = strpos($wcodf,"-");
					$wcodf=substr($wcodf,0,$ini);
				}
			}
			else
				$wcodf=$wcodfn;
			
			if(isset($wccof))
			{
				$ini=strpos($wccof,"-");
				$wcco1=substr($wccof,0,$ini);
				$wcco2=substr($wccof,0,$ini);
			}
			$wcco2=strtolower ($wcco2);
			$query = "SELECT mganom from ".$empresa."_000028 ";
			$query = $query."  where mgacod = '".$wcodi."'";
			$err1 = mysql_query($query,$conex);
			$row1 = mysql_fetch_array($err1);
				//             0       1     2      3      4      5      6      7   
			$query = "SELECT expcco,expper,expcue,expnte,expexp,expmon,expcpr,Expnit from ".$empresa."_000011";
			$query = $query."  where expano = ".$wanop;
			$query = $query."    and expemp = '".$wemp."' ";
			$query = $query."    and expcco between '".$wcco1."' and '".$wcco2."'";
			$query = $query."    and expper between ".$wper1." and ".$wper2;
			$query = $query."    and expcpr between '".$wcodi."' and '".$wcodf."'";
			$query = $query."   order by expcco,expper,expcpr ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$winf=array();
			echo "<table border=1>";
			echo "<tr><td colspan=8 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=8 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=8 align=center>INFORME DE EXPLICACIONES PARA COSTOS</td></tr>";
			echo "<tr><td colspan=8 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td colspan=8 align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. " A&Ntilde;O : ".$wanop."</td></tr>";
			echo "<tr><td colspan=8 align=center>UNIDAD INICIAL : ".$wcco1. " UNIDAD FINAL : ".$wcco2."</td></tr>";
			echo "<tr><td colspan=8 align=center>RUBROS PRESUPUESTALES : ".$wcodi. " - ".$wcodf."</td></tr>";
			echo "<tr><td><b>C.COSTOS</b></td></td><td><b>PERIODO</b></td><td><b>CUENTA</b></td><td><b>NIT</b></td><td><b>NOMBRE</b></td><td><b>RUBRO PPAL</b></td><td><b>EXPLICACION</b></td><td align=right><b>VALOR</b></td></tr>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				echo "<tr><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td><td>".$row[7]."</td><td>".$row[3]."</td><td>".$row[6]."</td><td>".$row[4]."</td><td align=right>".number_format((double)$row[5],2,'.',',')."</td>";
			}
		}
	}
?>
</body>
</html>
