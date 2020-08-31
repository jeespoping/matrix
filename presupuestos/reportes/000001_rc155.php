<html>
<head>
  <title>MATRIX</title>
  <style type="text/css">
	.tipo3R{color:#000066;background:#CCCCCC;font-size:9pt;font-family:Arial;font-weight:bold;text-align:left;border-style:none;}
   </style>
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
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Reporte de Evaluacion x Grupos</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc155.php Ver. 2011-07-14</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
function comparacion($vec1,$vec2)
{
	if($vec1[15] > $vec2[15])
		return 1;
	elseif ($vec1[15] < $vec2[15])
				return -1;
			else
				return 0;
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc155.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop)  or !isset($wper1)  or !isset($wper2) or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2 or !isset($wtip) or (strtoupper ($wtip) != "T" and strtoupper ($wtip) != "V"))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>REPORTE DE EVALUACION X GRUPOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#999999 align=center colspan=2>Grupos de Empresas</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Seleccion</td><td bgcolor=#cccccc align=center>Grupo Empresarial</td></tr>";
			$query = "SELECT Empgru from ".$empresa."_000061 group by 1 order by 1";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<tr><td bgcolor=#cccccc align=center><input type='checkbox' name='sel[".$i."]'></td><td bgcolor=#cccccc align=center><input type='text' name='grupo[".$i."]' size='50' readonly='readonly' value='".$row[0]."' class=tipo3R></td></tr>";
				}
				echo "<input type='HIDDEN' name= 'num' value='".$num."' id='num'>";
			}
			echo "<tr><td bgcolor=#cccccc align=center>Tipo de Costo ? (T/V)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wtip' size=1 maxlength=1></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$entidades="IN (";
			$nent=0;
			for ($i=0;$i<=$num;$i++)
			{
				if(isset($sel[$i]))
				{
					$query = "SELECT Empcin from ".$empresa."_000061 where Empgru = '".$grupo[$i]."' group by 1 ";
					$err = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err);
					if ($num1>0)
					{
						for ($j=0;$j<$num1;$j++)
						{
							$row = mysql_fetch_array($err);
							if($nent == 0)
							{
								$entidades .= chr(34).$row[0].chr(34);
								$nent++;
							}
							else
								$entidades .= chr(44).chr(34).$row[0].chr(34);
						}
					}
				}
			}
			$entidades .= ")";
			$tipo="TOTALES";
			if(strtoupper ($wtip) == "V")
				$tipo="VARIABLE";
			
			echo "<CENTER><table border=0>";
			echo "<tr><td colspan=4 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=4 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=4 align=center>REPORTE DE EVALUACION X GRUPOS</td></tr>";
			echo "<tr><td colspan=4 align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. " A&Ntilde;O : ".$wanop."</td></tr>";
			echo "<tr><td colspan=4 align=center>TIPO DE COSTOS : ".$tipo."</td></tr>";
			echo "<tr><td bgcolor=#cccccc><b>GRUPO</b></td><td bgcolor=#cccccc><b>INGRESOS<BR>TOTALES</b></td><td bgcolor=#cccccc><b>UTILIDAD</b></td><td bgcolor=#cccccc><b>MARGEN DE<BR>RENTABILIDAD</b></td></tr>";
			
			$query  = " CREATE TEMPORARY TABLE if not exists temp as ";
			$query .= "select  Empcin, Empgru from ".$empresa."_000061  where Empcin ".$entidades." group by 1,2 ";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			
			$query = "CREATE UNIQUE INDEX Emp_idx on TEMP (Empcin(4))";
			$err = mysql_query($query,$conex)  or die(mysql_errno().":".mysql_error());
			
			if(strtoupper ($wtip) == "T")
				$query = "select temp.Empgru,sum(Mosipr),sum(Mosutt) from ".$empresa."_000108,temp ";
			else
				$query = "select temp.Empgru,sum(Mosipr),sum(Mosutv) from ".$empresa."_000108,temp ";
			$query .= "	where mosano = ".$wanop;
			$query .= "	  and mosmes between ".$wper1." and ".$wper2; 
			$query .= "	  and mosent ".$entidades;
			$query .= "	  and mosent = Empcin ";
			$query .= " group by 1 ";
			$err = mysql_query($query,$conex)  or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			$wtot1=0;
			$wtot2=0;
			
			//$path="/matrix/presupuestos/reportes/000001_rc85.php?wanop=".$wanop."&wper1=".$j."&wper2=".$j."&wcco1=".$wcco1."&wsubp=".$wdat[$i][0]."-".$wdat[$i][1]."&wres=2";
			//echo "<td align=right onclick='ejecutar(".chr(34).$path.chr(34).")'>".number_format((double)$wdat[$i][$j+1],2,'.',',')."</td>";	

			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$margen = $row[2] / $row[1] * 100;
				$wtot1 += $row[1];
				$wtot2 += $row[2];
				if($i % 2 == 0)
					$color="#C3D9FF";
				else
					$color="#E8EEF7";
				echo"<tr><td bgcolor=".$color.">".$row[0]."</td><td bgcolor=".$color." align=right>".number_format((double)$row[1],0,'.',',')."</td><td bgcolor=".$color." align=right>".number_format((double)$row[2],0,'.',',')."</td><td bgcolor=".$color." align=right>".number_format((double)$margen,2,'.',',')."</td></tr>";
			}
			$margen = $wtot1 / $wtot2 * 100;
			$color="#999999";
			echo"<tr><td bgcolor=".$color.">TOTAL GENERAL</td><td bgcolor=".$color." align=right>".number_format((double)$wtot1,0,'.',',')."</td><td bgcolor=".$color." align=right>".number_format((double)$wtot2,0,'.',',')."</td><td bgcolor=".$color." align=right>".number_format((double)$margen,2,'.',',')."</td></tr>";
			echo "</table></CENTER>";	
		}
	}
?>
</body>
</html>
