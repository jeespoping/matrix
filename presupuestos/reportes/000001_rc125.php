<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<script type="text/javascript">
<!--
	function ejecutar(path)
	{
		window.open(path,'','width=1024,height=500,status=0,menubar=0,scrollbars=1,toolbar=0,directories=0,resizable=0');
	}
//-->
</script>
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Reporte de Costos Reales x Programas</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc125.php Ver. 2016-02-05</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");

@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc125.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wano) or !isset($wemp) or $wemp == "Seleccione" or !isset($wmesi) or (isset($wmesi) and ($wmesi < 1 or $wmesi > 12)) or !isset($wmesf) or (isset($wmesf) and ($wmesf < 1 or $wmesf > 12)))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>REPORTE DE COSTOS REALES X PROGRAMAS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Inicial</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wano' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesi' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesf' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Todos ?</td>";
			echo "<td bgcolor=#cccccc align=center><input type='checkbox' name='wall'></td></tr>";
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
			echo "<tr><td bgcolor=#cccccc align=center>Programas</td><td bgcolor=#cccccc align=center>";
			$query = "SELECT Prgcod, Prgdes from ".$empresa."_000127 order by Prgcod";
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
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$wempt = $wemp;
			$wemp = substr($wemp,0,2);
			$query  = "select Exppro,Prgdes,Expcpr,Mganom,Expper,sum(Expmon) from ".$empresa."_000011,".$empresa."_000127,".$empresa."_000028 ";
			$query .= " where expano = ".$wano;
			$query .= "   and expemp = '".$wemp."'";
			$query .= "   and expper between ".$wmesi." and ".$wmesf;
			if(!isset($wall))
				$query .= "   and exppro = '".substr($wprog,0,strpos($wprog,"-"))."' ";
			$query .= "   and exppro = prgcod ";
			$query .= "   and expcpr = mgacod ";  
			$query .= " group by Exppro,Prgdes,Expcpr,Mganom,Expper ";
			$query .= " order by exppro,expcpr,Expper ";
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$ncol=$wmesf - $wmesi + 4;
				echo "<table border=0 align=center>";
				echo "<tr><td colspan=".$ncol." align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
				echo "<tr><td colspan=".$ncol." align=center>DIRECCION DE INFORMATICA</td></tr>";
				echo "<tr><td colspan=".$ncol." align=center>REPORTE DE COSTOS REALES X PROGRAMAS</td></tr>";
				echo "<tr><td colspan=".$ncol." align=center>EMPRESA : ".$wempt."</td></tr>";
				echo "<tr><td colspan=".$ncol." align=center>DESDE EL MES ".$wmesi." A&Ntilde;O ".$wano." HASTA EL MES ".$wmesf." A&Ntilde;O ".$wano."</td></tr>";
				if(isset($wall))
					echo "<tr><td colspan=".$ncol." align=center>TODOS LOS PROGRAMAS</td></tr>";
				else
					echo "<tr><td colspan=".$ncol." align=center>PROGRAMA : ".$wprog."</td></tr>";
				$wruba="";
				$wruban="";
				$wproa="";
				$wproan="";
				$data=array();
				$totp=array();
				echo "<tr><td bgcolor=#cccccc><b>RUBRO</b></td><td bgcolor=#cccccc><b>DESCRIPCION</b></td>";
				for ($i=$wmesi;$i<=$wmesf;$i++)
				{
					echo "<td bgcolor=#cccccc align=center><b>".$i."/".$wano."</b></td>";
				}
				echo "<td bgcolor=#cccccc align=center><b>TOTAL RUBRO</b></td>";
				echo "</tr>";
				$FILA=0;
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					if($wruba != $row[2] or $wproa != $row[0])
					{
						if($i > 0)
						{
							$FILA++;
							if($FILA % 2 == 0)
								$color="#99CCFF";
							else
								$color="#ffffff";
							echo "<tr><td bgcolor=".$color.">".$wruba."</td><td bgcolor=".$color.">".$wruban."</td>";
							for ($j=$wmesi;$j<=$wmesf;$j++)
							{
								$path="/matrix/presupuestos/reportes/000001_rc126.php?wpro=".$wproa."&wrub=".$wruba."&wano=".$wano."&wmesf=".$j."&empresa=".$empresa;
								echo "<td bgcolor=".$color." align=right onclick='ejecutar(".chr(34).$path.chr(34).")'>".number_format((double)$data[$j],0,'.',',')."</td>";
							}
							$path="/matrix/presupuestos/reportes/000001_rc126.php?wpro=".$wproa."&wrub=".$wruba."&wano=".$wano."&wmesi=".$wmesi."&wmesf=".$wmesf."&empresa=".$empresa;
							echo "<td bgcolor=".$color." align=right onclick='ejecutar(".chr(34).$path.chr(34).")'>".number_format((double)$data[$wmesf + 1],0,'.',',')."</td>";
							echo "</tr>";
						}
						for ($j=$wmesi;$j<=$wmesf;$j++)
							$data[$j]=0;
						$data[$wmesf + 1]=0;
						$wruba=$row[2];
						$wruban=$row[3];
					}
					if($wproa != $row[0])
					{
						if($i > 0)
						{
							echo "<tr><td colspan=2 bgcolor=#cccccc><b>TOTAL PROGRAMA</td>";
							for ($j=$wmesi;$j<=$wmesf;$j++)
							{
								$path="/matrix/presupuestos/reportes/000001_rc126.php?wpro=".$wproa."&wano=".$wano."&wmesf=".$j."&empresa=".$empresa;
								echo "<td bgcolor=#cccccc align=right onclick='ejecutar(".chr(34).$path.chr(34).")'><b>".number_format((double)$totp[$j],0,'.',',')."</b></td>";
							}
							$path="/matrix/presupuestos/reportes/000001_rc126.php?wpro=".$wproa."&wano=".$wano."&wmesi=".$wmesi."&wmesf=".$wmesf."&empresa=".$empresa;
							echo "<td bgcolor=#cccccc align=right onclick='ejecutar(".chr(34).$path.chr(34).")'><b>".number_format((double)$totp[$wmesf + 1],0,'.',',')."</b></td>";
							echo "</tr>";
						}
						for ($j=$wmesi;$j<=$wmesf;$j++)
							$totp[$j]=0;
						$totp[$wmesf + 1]=0;
						$wproa=$row[0];
						$wproan=$row[1];			
						echo "<tr><td colspan=".$ncol." bgcolor=#FFCC66><b>PROGRAMA ".$wproa."-".$wproan."</b></td></tr>";
					}
					$data[$row[4]] += $row[5];
					$data[$wmesf + 1] += $row[5];
					$totp[$row[4]] += $row[5];
					$totp[$wmesf + 1] += $row[5];
				}
				$FILA++;
				if($FILA % 2 == 0)
					$color="#99CCFF";
				else
					$color="#ffffff";
				echo "<tr><td bgcolor=".$color.">".$wruba."</td><td bgcolor=".$color.">".$wruban."</td>";
				for ($j=$wmesi;$j<=$wmesf;$j++)
				{
					$path="/matrix/presupuestos/reportes/000001_rc126.php?wpro=".$wproa."&wrub=".$wruba."&wano=".$wano."&wmesf=".$j."&empresa=".$empresa;
					echo "<td bgcolor=".$color." align=right onclick='ejecutar(".chr(34).$path.chr(34).")'>".number_format((double)$data[$j],0,'.',',')."</td>";
				}
				$path="/matrix/presupuestos/reportes/000001_rc126.php?wpro=".$wproa."&wrub=".$wruba."&wano=".$wano."&wmesi=".$wmesi."&wmesf=".$wmesf."&empresa=".$empresa;
				echo "<td bgcolor=".$color." align=right onclick='ejecutar(".chr(34).$path.chr(34).")'>".number_format((double)$data[$wmesf + 1],0,'.',',')."</td>";
				echo "</tr>";
				echo "<tr><td colspan=2 bgcolor=#cccccc><b>TOTAL PROGRAMA</b></td>";
				for ($j=$wmesi;$j<=$wmesf;$j++)
				{
					$path="/matrix/presupuestos/reportes/000001_rc126.php?wpro=".$wproa."&wano=".$wano."&wmesf=".$j."&empresa=".$empresa;
					echo "<td bgcolor=#cccccc align=right onclick='ejecutar(".chr(34).$path.chr(34).")'><b>".number_format((double)$totp[$j],0,'.',',')."</b></td>";
				}
				$path="/matrix/presupuestos/reportes/000001_rc126.php?wpro=".$wproa."&wano=".$wano."&wmesi=".$wmesi."&wmesf=".$wmesf."&empresa=".$empresa;
				echo "<td bgcolor=#cccccc align=right onclick='ejecutar(".chr(34).$path.chr(34).")'><b>".number_format((double)$totp[$wmesf + 1],0,'.',',')."</b></td>";
				echo "</tr>";
			}
		}
	}
?>
</body>
</html>
