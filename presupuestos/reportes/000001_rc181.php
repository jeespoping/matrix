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
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Facturacion Comparativa por Concepto</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc181.php Ver. 2017-07-27</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
function comparacion($vec1,$vec2)
{
	global $fil,$col;
	if($vec1[$fil][$col] > $vec2[$fil][$col])
		return -1;
	elseif ($vec1[$fil][$col] < $vec2[$fil][$col])
				return 1;
			else
				return 0;
}
//Estado De Resultados Comparativo Por Periodo
function periodo($mes,$factor)
{
	// Funcion que permite dividir el año en periodos dados x el factor y saber el mes en que periodo se encuentra
    if($mes % $factor == 0)
    	$mes--;
	$periodo=($mes + ($factor - ($mes % $factor)))/$factor;
	return $periodo;
}
function ver($chain)
{
	if(strpos($chain,"-") === false)
		return $chain;
	else
		return substr($chain,0,strpos($chain,"-"));
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form name='r130' action='000001_rc181.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanoi) or !isset($wemp) or $wemp == "Seleccione" or !isset($wtipi) or (isset($wtipi) and strtoupper($wtipi) != "P" and strtoupper($wtipi) != "T" and strtoupper($wtipi) != "G") or !isset($wanof) or !isset($wmesi) or !isset($wmesf) or !isset($wcco1)  or !isset($wcco2) or (isset($wmesi) and ($wmesi < 1 or $wmesi > 12)) or (isset($wmesf) and ($wmesf < 1 or $wmesf > 12)))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>FACTURACION COMPARATIVA POR CONCEPTO</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Inicial</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanoi' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesi' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Final</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanof' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesf' size=2 maxlength=2></td></tr>";
			if($key=="costosyp" or (isset($call) and $call == "SIG") or (isset($call) and $call == "SIC"))
			{
				echo "<tr><td bgcolor=#cccccc align=center>Unidad Inicial de Proceso</td>";
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
				echo "<tr><td bgcolor=#cccccc align=center>Unidad Final de Proceso</td>";
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco2' size=4 maxlength=4></td></tr>";
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
				echo "<tr><td bgcolor=#cccccc align=center>Unidad Proceso</td><td bgcolor=#cccccc align=center>";
				$query = "SELECT Cc,cconom  from ".$empresa."_000125,".$empresa."_000161 where empleado ='".$key."'  and cc=ccocod group by 1 order by Cc";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					echo "<select name='wcco1'>";
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						echo "<option>".$row[0]."-".$row[1]."</option>";
					}
					echo "</select>";
				}
				echo "</td></tr>";
				$wcco2="NO";
				echo "<center><input type='HIDDEN' name= 'wcco2' value='".$wcco2."'>";
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
			echo "<tr><td bgcolor=#cccccc align=center>Tipo Ingreso (P - Propio / T - Tercero / G - Total)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wtipi' size=1 maxlength=1></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Forma de Agrupacion</td>";
			echo "<td bgcolor=#cccccc>";
			echo "<input type='RADIO' name=wfactor value=1 checked> Mensual<br>";
			echo "<input type='RADIO' name=wfactor value=3> Trimestral<br>";
			echo "<input type='RADIO' name=wfactor value=4> Cuatrimestral<br>";
			echo "<input type='RADIO' name=wfactor value=6> Semestral<br>";
			echo "<input type='RADIO' name=wfactor value=12> Anual";
			echo "</td></tr>";
			
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$wempt = $wemp;
			$wemp = substr($wemp,0,2);
			if(!isset($wcco2) or $wcco2 == "NO")
			{
				$wcco1 = ver($wcco1);
				$wcco2 = $wcco1;
			}
			$meses=array();
			$meses[1][1]="1";
			$meses[1][2]="2";
			$meses[1][3]="3";
			$meses[1][4]="4";
			$meses[1][5]="5";
			$meses[1][6]="6";
			$meses[1][7]="7";
			$meses[1][8]="8";
			$meses[1][9]="9";
			$meses[1][10]="10";
			$meses[1][11]="11";
			$meses[1][12]="12";
			
			$meses[3][1]="1-2-3";
			$meses[3][2]="4-5-6";
			$meses[3][3]="7-8-9";
			$meses[3][4]="10-11-12";
			$meses[4][1]="1-2-3-4";
			$meses[4][2]="5-6-7-8";
			$meses[4][3]="9-10-11-12";
			$meses[6][1]="1-2-3-4-5-6";
			$meses[6][2]="7-8-9-10-11-12";
			$meses[12][1]="1-2-3-4-5-6-7-8-9-10-11-12";
			switch (strtoupper($wtipi))
			{
				case "P":
					$wval="Mioinp";
				break;
				case "T":
					$wval="Mioint";
				break;
				case "G":
					$wval="Mioito";
				break;
			}
			//                   0       1       2      3             4   
			$query  = "select Miocfa, Cfades, Mioano, Miomes, sum(".$wval.") from ".$empresa."_000063,".$empresa."_000060 ";
			$query .= "	where ((Mioano =  ".$wanoi;
			$query .= "	  and Miomes >= ".$wmesi." and Mioano < ".$wanof.") ";
			$query .= "	   or (Mioano > ".$wanoi;
			$query .= "	  and  Mioano < ".$wanof.") ";
			$query .= "	   or (Mioano = ".$wanof;
			$query .= "	  and  Miomes <= ".$wmesf." and Mioano > ".$wanoi.") ";
			$query .= "	   or (Mioano = ".$wanoi."  and Mioano = ".$wanof; 
			$query .= "	  and  Miomes >= ".$wmesi." and Miomes <= ".$wmesf.")) ";
			$query .= "   and Mioemp = '".$wemp."'";
			$query .= "	  and Miocco between '".$wcco1."' and '".$wcco2."'";
			$query .= "	  and Miocfa = Cfacod ";
			$query .= "	  and Mioemp = Cfaemp ";
			$query .= "	group by 1,2,3,4  ";
			$query .= "	order by 1,3,4  ";
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$wtipa="";
				$wcpra="";
				$wcpran="";
				$data=array();
				$tot=array();
				$tott=array();
				$totp=array();
				$totp1=array();
				$totp2=array();
				$cpr=array();
				$cprn=array();
				$ncol=2;
				$ncolor=0;
				$wpaso=0;
				$inc=periodo($wmesi,$wfactor);
				$inf=periodo($wmesf,$wfactor);
				$iter=12 / $wfactor;
				for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
				{
					while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
					{
						$ncol++;
						$inc +=1;
					}
					$inc=1;
				}
				echo "<table border=0 align=center>";
				echo "<tr><td colspan=".$ncol." align=center><font size=2>PROMOTORA MEDICA LAS AMERICAS S.A.</font></td></tr>";
				echo "<tr><td colspan=".$ncol." align=center><font size=2><font size=3>DIRECCION DE INFORMATICA</font></td></tr>";
				echo "<tr><td colspan=".$ncol." align=center><font size=2>FACTURACION COMPARATIVA POR CONCEPTO</font></td></tr>";
				echo "<tr><td colspan=".$ncol." align=center><font size=2>DESDE EL MES ".$wmesi." A&Ntilde;O ".$wanoi." HASTA EL MES ".$wmesf." A&Ntilde;O ".$wanof."</font></td></tr>";
				echo "<tr><td colspan=".$ncol." align=center><font size=2>UNIDAD INICIAL : ".$wcco1. " UNIDAD FINAL : ".$wcco2."</td></tr>";
				echo "<tr><td colspan=".$ncol." align=center>EMPRESA : ".$wempt."</td></tr>";
				switch ($wfactor)
				{
					case 1:
						$wp="/M";
					break;
					case 3:
						$wp="/T";
					break;
					case 4:
						$wp="/C";
					break;
					case 6:
						$wp="/S";
					break;
					case 12:
						$wp="";
					break;
				}
				echo "<tr><td bgcolor=#cccccc><font size=2><b>CODIGO<BR>CONCEPTO</b></font></td><td bgcolor=#cccccc><font size=2><b>DESCRIPCION</b></font></td>";
				$inc=periodo($wmesi,$wfactor);
				$inf=periodo($wmesf,$wfactor);
				$iter=12 / $wfactor;
				for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
				{
					while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
					{
						$ano=$i+$wanoi;
						if($wfactor != 12)
							echo "<td bgcolor=#cccccc align=center><font size=2><b>".$ano.$wp.$inc."</b></font></td>";
						else
							echo "<td bgcolor=#cccccc align=center><font size=2><b>".$ano."</b></font></td>";
						$inc +=1;
					}
					$inc=1;
				}
				echo "</tr>";
				
				$ncol=2;
				$inc=periodo($wmesi,$wfactor);
				$inf=periodo($wmesf,$wfactor);
				$iter=12 / $wfactor;
				for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
				{
					while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
					{
						$ncol++;
						$inc +=1;
					}
					$inc=1;
				}
				
				$clave="";
				$s=-1;
				for ($j=0;$j<$num;$j++)
				{
					$row = mysql_fetch_array($err);
					$fil=$row[2] - $wanoi;
					$col=periodo($row[3],$wfactor);
					if($clave != $row[0])
					{
						$s++;
						$clave = $row[0];
					}
					//echo "plano :".$s." Fila : ".$fil." Columna : ".$col." Valor . ".$row[4]."<br>";
					if(!isset($data[$s][$fil][$col]))
						$data[$s][$fil][$col] = 0;
					$data[$s][$fil][$col] += $row[4];
					$cpr[$s][0] = $row[0];
					$cpr[$s][1] = $row[1];
					if(!isset($tot[$fil][$col]))
						$tot[$fil][$col] = 0;
					$tot[$fil][$col] += $row[4];
				}
				for ($w=0;$w<=$s;$w++)
				{
					$data[$w][$fil+1][0] =  $cpr[$w][0];
					$data[$w][$fil+1][1] =  $cpr[$w][1];
				}
				$inc=periodo($wmesi,$wfactor);
				$inf=periodo($wmesf,$wfactor);
				$iter=12 / $wfactor;
				usort($data,'comparacion');
				for ($w=0;$w<=$s;$w++)
				{
					$ncolor++;
					if($ncolor % 2 == 0)
						$color="#FFFFFF";
					else
						$color="#99CCFF";
					echo "<tr><td bgcolor=".$color."><font size=2>".$data[$w][$fil+1][0]."</font></td><td bgcolor=".$color."><font size=2>".$data[$w][$fil+1][1]."</font></td>";
					$inc=periodo($wmesi,$wfactor);
					$inf=periodo($wmesf,$wfactor);
					for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
					{
						while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
						{
							echo "<td bgcolor=".$color." align=right><font size=2>".number_format((double)$data[$w][$i][$inc],0,'.',',')."</font></td>";
							$inc +=1;
						}
						$inc=1;
					}
				}
				echo "</tr>";
				$color="#CCCCCC";
				echo "<tr><td bgcolor=".$color." colspan=2><font size=2>TOTALES</font></td>";
				$inc=periodo($wmesi,$wfactor);
				$inf=periodo($wmesf,$wfactor);
				for ($i=($wanoi-$wanoi);$i<=($wanof-$wanoi);$i++)
				{
					while((($i+$wanoi) < $wanof and $inc <= $iter) or (($i+$wanoi) == $wanof and $inc <= $inf))
					{
						echo "<td bgcolor=".$color." align=right><font size=2>".number_format((double)$tot[$i][$inc],0,'.',',')."</font></td>";
						$inc +=1;
					}
					$inc=1;
				}
				echo "</tr>";
				echo "<tr><td colspan=".$ncol." align=center><font size=2>Nota: Los montos de ingresoso que aqui se presentan incluyen ademas de<br> la facturacion del periodo notas credito y debito, cargos pendientes por facturar que suman y restan.</font></td></tr>";
				echo "</table>";
			}
		}
	}
?>
</body>
</html>
