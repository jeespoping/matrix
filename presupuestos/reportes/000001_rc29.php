<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<script type="text/javascript">
<!--
	function enter()
	{
		document.forms.rc29.submit();
	}
//-->
</script>
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Detalle de Presupuesto x Codigo pptal</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc29.php Ver. 2017-06-21</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form name= 'rc29' action='000001_rc29.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1)  or !isset($wper2) or !isset($wres) or (strtoupper ($wres) != "R" and strtoupper ($wres) != "D") or ((!isset($wcco1)  or !isset($wcco2)) and !isset($wccof)) or !isset($wcodi)  or !isset($wcodf)  or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>DETALLE DE PRESUPUESTO X CODIGO PPTAL</td></tr>";
			if($key=="costosyp" or (isset($call) and $call == "SIG") or (isset($call) and $call == "SIC"))
			{
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
			}
			else
			{
				echo "<tr><td bgcolor=#cccccc align=center>Empresa de  Proceso</td><td bgcolor=#cccccc align=center>";
				$query = "SELECT Empcod,Empdes  from ".$empresa."_000153,".$empresa."_000125 where empleado = '".$key."' and empresa = Empcod group by 1 order by Empcod";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					echo "<select name='wemp' OnChange='enter()'>";
					echo "<option>Seleccione</option>";
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						if(isset($wemp) and substr($wemp,0,strpos($wemp,"-")) == $row[0])
							echo "<option selected>".$row[0]."-".$row[1]."</option>";
						else
							echo "<option>".$row[0]."-".$row[1]."</option>";
					}
					echo "</select>";
				}
				echo "</td></tr>";
			}
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			if($key=="costosyp" or (isset($call) and $call == "SIC"))
			{
				echo "<tr><td bgcolor=#cccccc align=center>Unidad Inicial de Proceso</td>";
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
				echo "<tr><td bgcolor=#cccccc align=center>Unidad Final de Proceso</td>";
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco2' size=4 maxlength=4></td></tr>";
			}
			else
			{
				echo "<tr><td bgcolor=#cccccc align=center>Unidad Proceso</td><td bgcolor=#cccccc align=center>";
				if(isset($wemp) and $wemp != "Seleccione")
				{
					$query = "SELECT Cc,cconom  from ".$empresa."_000125,".$empresa."_000005 where empleado ='".$key."'  and cc=ccocod and Empresa = '".substr($wemp,0,strpos($wemp,"-"))."' group by 1 order by Cc";
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
				}
				echo "</td></tr>";
			}
			echo "<input type='HIDDEN' name= 'call' value='".$call."'>";
			echo "<tr><td bgcolor=#cccccc align=center>Todos los Rubros</td>";
			echo "<td bgcolor=#cccccc align=center><input type='checkbox' name='wtodos'></td></tr>";
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
			echo "<tr><td bgcolor=#cccccc align=center>Resumido o Detallado ? (R/D)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wres' size=1 maxlength=1></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$wempt = $wemp;
			$wemp = substr($wemp,0,2);
			if($wcodin==0)
			{
				$ini = strpos($wcodi,"-");
				$wcodi=substr($wcodi,0,$ini);
			}
			else
				$wcodi=$wcodin;
			if($wcodfn==0)
			{
				$ini = strpos($wcodf,"-");
				$wcodf=substr($wcodf,0,$ini);
			}
			else
				$wcodf=$wcodfn;
			$wres=strtoupper ($wres);
			if(isset($wccof))
			{
				$ini=strpos($wccof,"-");
				$wcco1=substr($wccof,0,$ini);
				$wcco2=substr($wccof,0,$ini);
			}
			$wcco2=strtolower ($wcco2);
			if($wres == "D")
			{
				$query = "SELECT gascco,cconom,gascod,mganom,gasmes,gasval,gasdes from ".$empresa."_000012,".$empresa."_000005,".$empresa."_000028 ";
				$query = $query."  where gasano = ".$wanop;
				$query = $query."    and gasemp = '".$wemp."' ";
				$query = $query."    and gascco between '".$wcco1."' and '".$wcco2."'";
				$query = $query."    and gasmes between ".$wper1." and ".$wper2;
				if(isset($wtodos))
					$query = $query."    and gascod between '0' and 'z' ";
				else
					$query = $query."    and gascod between '".$wcodi."' and '".$wcodf."'";
				$query = $query."    and gascco = ccocod ";
				$query = $query."    and gasemp = ccoemp ";
				$query = $query."    and gascod = mgacod ";
				$query = $query."   order by gascco,gascod,gasmes ";
			}
			else
			{
				$query = "SELECT gascco,cconom,gascod,mganom,gasdes,sum(gasval) as k from ".$empresa."_000012,".$empresa."_000005,".$empresa."_000028 ";
				$query = $query."  where gasano = ".$wanop;
				$query = $query."    and gasemp = '".$wemp."' ";
				$query = $query."    and gascco between '".$wcco1."' and '".$wcco2."'";
				$query = $query."    and gasmes between ".$wper1." and ".$wper2;
				if(isset($wtodos))
					$query = $query."    and gascod between '0' and 'z' ";
				else
					$query = $query."    and gascod between '".$wcodi."' and '".$wcodf."'";
				$query = $query."    and gascco = ccocod ";
				$query = $query."    and gasemp = ccoemp ";
				$query = $query."    and gascod = mgacod ";
				$query = $query."   group by  gascco,cconom,gascod,mganom,gasdes ";
				$query = $query."   order by gascco,gascod,k desc ";
			}
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<table border=1>";
			echo "<tr><td colspan=7 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=7 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=7 align=center>DETALLE DE PRESUPUESTO X CODIGO PPTAL</td></tr>";
			echo "<tr><td colspan=7 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td colspan=7 align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. " A&Ntilde;O : ".$wanop."</td></tr>";
			echo "<tr><td colspan=7 align=center>UNIDAD INICIAL : ".$wcco1. " UNIDAD FINAL : ".$wcco2."</td></tr>";
			echo "<tr><td colspan=7 align=center>RUBRO INICIAL : ".$wcodi. " RUBRO FINAL : ".$wcodf."</td></tr>";
			if($wres == "D")
				echo "<tr><td><b>C. DE C.</b></td><td><b>NOMBRE</b></td><td><b>RUBRO</b></td><td><b>NOMBRE</b></td><td><b>MES</b></td><td align=right><b>VALOR</b></td><td><b>DESCRIPCION</b></td></tr>";
			else
				echo "<tr><td><b>C. DE C.</b></td><td><b>NOMBRE</b></td><td><b>RUBRO</b></td><td><b>NOMBRE</b></td><td align=right><b>VALOR</b></td><td><b>DESCRIPCION</b></td></tr>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($wres == "D")
				{
					if(strlen($row[6]) == 0)
						$row[6]="&nbsp";
					echo"<tr><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td align=right>".number_format((double)$row[5],0,'.',',')."</td><td>".$row[6]."</b></td></tr>";
				}
				else
				{
					if(strlen($row[4]) == 0)
						$row[4]="&nbsp";
					echo"<tr><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td><td>".$row[3]."</td><td align=right>".number_format((double)$row[5],0,'.',',')."</td><td>".$row[4]."</b></td></tr>";
				}
    		}
		}
	}
?>
</body>
</html>
