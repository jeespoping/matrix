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
		document.forms.rc06.submit();
	}
//-->
</script>
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Informe de Explicaciones</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc06.php Ver. 2018-06-14</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form name= 'rc06' action='000001_rc06.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or !isset($wgru) or $wemp == "Seleccione" or !isset($wper1)  or !isset($wper2) or ((!isset($wcco1)  or !isset($wcco2)) and !isset($wccof)) or !isset($wcodi) or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>INFORME DE EXPLICACIONES</td></tr>";
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
			/*
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
			echo "</td></tr>";*/
			echo "<tr><td bgcolor=#cccccc align=center>Grupo de Unidades</td><td bgcolor=#cccccc align=center>";
			$query = "SELECT ccouni  from ".$empresa."_000161 group by 1 order by 1";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wgru'>";
				echo "<option>Todos</option>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."</option>";
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
				$ini = strpos($wcodi,"-");
				$wcodi=substr($wcodi,0,$ini);
			}
			else
				$wcodi=$wcodin;
			/*
			if($wcodfn==0)
			{
				$ini = strpos($wcodf,"-");
				$wcodf=substr($wcodf,0,$ini);
			}
			else
				$wcodf=$wcodfn;
			*/
			if(isset($wccof))
			{
				$ini=strpos($wccof,"-");
				$wcco1=substr($wccof,0,$ini);
				$wcco2=substr($wccof,0,$ini);
			}
			$wcco2=strtolower ($wcco2);
			$query = "SELECT cierre_real from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and mes =   ".$wper2;
			$query = $query."    and Emp = '".$wemp."' ";
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if($num > 0 and $row[0] == "on")
			{
				$query = "SELECT mganom from ".$empresa."_000028 ";
				$query = $query."  where mgacod = '".$wcodi."'";
				$err1 = mysql_query($query,$conex);
				$row1 = mysql_fetch_array($err1);
					//             0       1      2     3      4      5      6      7      8      9
				//$query = "SELECT expcco,expper,mcunom,cconom,expcue,expnte,expexp,expmon,expcpr,Expnit from ".$empresa."_000011,".$empresa."_000005,".$empresa."_000024 ";
				$query = "SELECT expcco,expper,'no',cconom,expcue,expnte,expexp,expmon,expcpr,Expnit from ".$empresa."_000011,".$empresa."_000005 ";
				$query = $query."  where expano = ".$wanop;
				$query = $query."    and expemp = '".$wemp."'";
				$query = $query."    and expcco between '".$wcco1."' and '".$wcco2."'";
				$query = $query."    and expper between ".$wper1." and ".$wper2;
				/*
				if(isset($wtodos))
					$query = $query."    and expcpr between '0' and 'z' ";
				else
				*/
				$tot = 0;
				$query = $query."    and expcpr = '".$wcodi."' ";
				$query = $query."    and expcco = ccocod ";
				$query = $query."    and expemp = ccoemp ";
				if($wgru != "Todos")
				{
					$query .= "    and Ccouni = '".$wgru."' ";
				}
				//$query = $query."    and expcue = mcucue ";
				$query = $query."   order by expcco,expper,expcue";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				$winf=array();
				echo "<table border=1>";
				echo "<tr><td colspan=15 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
				echo "<tr><td colspan=15 align=center>DIRECCION DE INFORMATICA</td></tr>";
				echo "<tr><td colspan=15 align=center>INFORME DE EXPLICACIONES</td></tr>";
				echo "<tr><td colspan=15 align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. " A&Ntilde;O : ".$wanop."</td></tr>";
				echo "<tr><td colspan=15 align=center>UNIDAD INICIAL : ".$wcco1. " UNIDAD FINAL : ".$wcco2."</td></tr>";
				echo "<tr><td colspan=15 align=center>RUBRO PRESUPUESTAL : ".$wcodi. " - ".$row1[0]."</td></tr>";
				echo "<tr><td colspan=15 align=center>EMPRESA : ".$wempt."</td></tr>";
				echo "<tr><td colspan=15 align=center>GRUPO DE UNIDADES : ".$wgru."</td></tr>";
				echo "<tr><td><b>C.COSTOS</b></td><td><b>DESCRIPCION</b></td><td><b>PERIODO</b></td><td><b>TERCERO</b></td><td><b>EXPLICACION</b></td><td align=right><b>VALOR</b></td></tr>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					for ($j=0;$j<8;$j++)
					{			
						$winf[$i][$j]=$row[$j];
					}
					$winf[$i][8]=$row[9];
	    		}
	    		sort($winf);
	    		for ($i=0;$i<$num;$i++)
				{
					if(substr($winf[$i][2],0,1) == "S")
						echo "<tr><td>".$winf[$i][0]."</td><td>".$winf[$i][3]."</td><td>".$winf[$i][1]."</td><td>".$winf[$i][5]."</td><td>".$winf[$i][6]."</td><td align=right>".number_format((double)$winf[$i][7],2,'.',',')."</td>";
					else
						echo "<tr><td>".$winf[$i][0]."</td><td>".$winf[$i][3]."</td><td>".$winf[$i][1]."</td><td>".$winf[$i][5]."</td><td>".$winf[$i][6]."</td><td align=right>".number_format((double)$winf[$i][7],2,'.',',')."</td>";
					$tot += $winf[$i][7];
				}
				echo "<tr><td bgcolor=#999999 colspan=5><b>TOTAL GENERAL</b></td><td bgcolor=#999999 align=right><b>".number_format((double)$tot,2,'.',',')."</b></td>";	
		}
		else
		{
			echo "<center><table border=0 aling=center>";
			echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
			echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>EL PERIODO NO!! ESTA CERRADO -- LLAME A COSTOS Y PRSUPUESTOS</MARQUEE></FONT>";
			echo "<br><br>";			
		}
		}
	}
?>
</body>
</html>
