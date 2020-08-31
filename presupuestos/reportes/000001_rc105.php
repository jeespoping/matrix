<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Validacion de Centros de Servicios NO Distribuibles</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc105.php Ver. 2016-05-27</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc105.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1) or $wper1 < 1 or $wper1 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>VALIDACION DE CENTROS DE SERVICIOS NO DISTRIBUIBLES</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
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
			$query = "select Sndori,Snddes,Cxccri from ".$empresa."_000106,".$empresa."_000066,".$empresa."_000068 ";
			$query = $query." where cxcano = ".$wanop; 
			$query = $query."   and cxcemp = '".$wemp."'";
			$query = $query."   and cxcmes = ".$wper1;
			$query = $query."   and cxcemp = sndemp ";
			$query = $query."   and cxccco = sndori ";
			$query = $query."   and cxcemp = mcremp ";
			$query = $query."   and cxcano = mcrano ";
			$query = $query."   and cxcmes = mcrmes ";
			$query = $query."   and cxccri = mcrcri  ";
			$query = $query."   and snddes = mcrcco ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<table border=1>";
			echo "<tr><td colspan=8 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=8 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=8 align=center>VALIDACION DE CENTROS DE SERVICIOS NO DISTRIBUIBLES</td></tr>";
			echo "<tr><td colspan=8 align=center><b>EMPRESA : ".$wempt."</b></td></tr>";
			echo "<tr><td colspan=8 align=center>PERIODO  : ".$wper1. " A&Ntilde;O : ".$wanop."</td></tr>";
			echo "<tr><td><b>C.C. ORIGEN</b></td><td><b>C.C. DESTINO</b></td><td><b>CRITERIO</b></td></tr>";
			$wdata=array();
			$k=-1;
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$wsw=0;
				for ($j=0;$j<=$k;$j++)
					if($wdata[$j] == $row[2])
						$wsw=1;
				if($wsw == 0)
				{
					$k++;
					$wdata[$k]=$row[2];
				}
				echo"<tr><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td></tr>";
    		}
    		echo"</table><br><br><br>";
    		echo "<table border=1>";
			echo "<tr><td colspan=8 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=8 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=8 align=center>UNIDADES QUE SE DISTRIBUYEN SEGUN CRITERIOS</td></tr>";
			echo "<tr><td colspan=8 align=center><b>EMPRESA : ".$wempt."</b></td></tr>";
			echo "<tr><td colspan=8 align=center>PERIODO  : ".$wper1. " A&Ntilde;O : ".$wanop."</td></tr>";
			echo "<tr><td><b>C.C.</b></td><td><b>CRITERIO</b></td><td align=right><b>PROCENTAJE</b></td></tr>";
			for ($i=0;$i<=$k;$i++)
			{
				$query = "select Cxccco, Cxccri, Cxcpor from ".$empresa."_000066 ";
				$query = $query." where cxcano = ".$wanop; 
				$query = $query."   and cxcemp = '".$wemp."'";
				$query = $query."   and cxcmes = ".$wper1;
				$query = $query."   and cxccri ='".$wdata[$i]."'";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				for ($j=0;$j<$num1;$j++)
				{
					$row = mysql_fetch_array($err1);
					echo"<tr><td>".$row[0]."</td><td>".$row[1]."</td><td>".number_format($row[2],2,'.',',')."%</td></tr>";
				}
    		}
    		echo"</table>";
		}
	}
?>
</body>
</html>
