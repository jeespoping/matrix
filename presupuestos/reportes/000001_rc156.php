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
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Ejecucion Presupuestal Programas de Gastos Especiales</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc156.php Ver. 2016-02-05</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc156.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wano) or !isset($wemp) or $wemp == "Seleccione" or !isset($wmesi) or (isset($wmesi) and ($wmesi < 1 or $wmesi > 12)) or !isset($wmesf) or (isset($wmesf) and ($wmesf < 1 or $wmesf > 12)))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>EJECUCION PRESUPUESTAL PROGRAMAS DE GASTOS ESPECIALES</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
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
			$query = "SELECT Cierre_real from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wano;
			$query = $query."    and mes =   ".$wmesf;
			$query = $query."    and Emp = '".$wemp."' ";
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if($num > 0 and $row[0] == "on")
			{
			$query = "SELECT Exppro,Prgdes, sum(Expmon)  from ".$empresa."_000011,".$empresa."_000127 ";
			$query = $query."  where Expano = ".$wano;
			$query = $query."    and Expemp = '".$wemp."'";
			$query = $query."    and Expper between ".$wmesi." and ".$wmesf;
			if(!isset($wall))
				$query .= "   and Exppro = '".substr($wprog,0,strpos($wprog,"-"))."' ";
			$query = $query."    and Exppro = Prgcod ";
			$query = $query." group by Exppro,Prgdes ";
			$query = $query." order by Exppro ";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			$query = "SELECT Gasprg,Prgdes, sum(Gasval)  from ".$empresa."_000012,".$empresa."_000127 ";
			$query = $query."  where Gasano = ".$wano;
			$query = $query."    and Gasemp = '".$wemp."'";
			$query = $query."    and Gasmes between ".$wmesi." and ".$wmesf;
			if(!isset($wall))
				$query .= "   and Gasprg = '".substr($wprog,0,strpos($wprog,"-"))."' ";
			$query = $query."    and Gasprg = Prgcod ";
			$query = $query." group by Gasprg,Prgdes ";
			$query = $query." order by Gasprg ";
			$err2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($err2);
			echo "<center><table border=1>";
			echo "<tr><td colspan=6 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=6 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=6 align=center>EJECUCION PRESUPUESTAL PROGRAMAS DE GASTOS ESPECIALES</td></tr>";
			echo "<tr><td colspan=6 align=center>PERIODO INICIAL : ".$wmesi." PERIODO FINAL : ".$wmesf. " A&Ntilde;O : ".$wano."</td></tr>";
			echo "<tr><td colspan=6 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td bgcolor=#cccccc><b>CODIGO</b></td><td bgcolor=#cccccc><b>DESCRIPCION</b></td><td align=right bgcolor=#cccccc><b>VALOR REAL</b></td><td align=right bgcolor=#cccccc><b>VALOR PRESUPUESTADO</b></td><td align=right bgcolor=#cccccc><b>% CUMPLIMINETO</b></td><td align=right bgcolor=#cccccc><b>DIFERENCIA</b></td></tr>";
			$wdata=array();
			$k1=0;
			$k2=0;
			$num=-1;
			if ($num1 ==  0)
			{
				$key1="ZZ";
				$k1=1;
			}
			else
			{
				$row1 = mysql_fetch_array($err1);
				$key1= $row1[0];
				$k1++;
			}
			if ($num2 ==  0)
			{
				$key2="ZZ";
				$k2=1;
			}
			else
			{
				$row2 = mysql_fetch_array($err2);
				$key2= $row2[0];
				$k2++;
			}
			while ($k1 <= $num1 or $k2 <= $num2)
			{
				if($key1 == $key2)
				{
					$num++;
					$wdata[$num][0]=$row1[0];
					$wdata[$num][1]=$row1[1];
					$wdata[$num][2]=$row1[2];
					$wdata[$num][3]=$row2[2];
					$wdata[$num][4]=$row1[2]/$row2[2] * 100;
					$wdata[$num][5]=$row2[2] - $row1[2];
					$k1++;
					$k2++;
					if($k1 > $num1)
						$key1="ZZ";
					else
					{
						$row1 = mysql_fetch_array($err1);
						$key1=$row1[0];
					}
					if($k2 > $num2)
						$key2="ZZ";
					else
					{
						$row2 = mysql_fetch_array($err2);
						$key2=$row2[0];
					}
				}
				else if($key1 < $key2)
				{
					$num++;
					$wdata[$num][0]=$row1[0];
					$wdata[$num][1]=$row1[1];
					$wdata[$num][2]=$row1[2];
					$wdata[$num][3]=0;
					$wdata[$num][4]=0;
					$wdata[$num][5]=0 - $row1[2];
					$k1++;
					if($k1 > $num1)
						$key1="ZZ";
					else
					{
						$row1 = mysql_fetch_array($err1);
						$key1=$row1[0];
					}
				}
				else
				{
					$num++;
					$wdata[$num][0]=$row2[0];
					$wdata[$num][1]=$row2[1];
					$wdata[$num][2]=0;
					$wdata[$num][3]=$row2[2];
					$wdata[$num][4]=0;
					$wdata[$num][5]=$row2[2];
					$k2++;
					if($k2 > $num2)
						$key2="ZZ";
					else
					{
						$row2 = mysql_fetch_array($err2);
						$key2=$row2[0];
					}
				}
			}
			for ($i=0;$i<=$num;$i++)
			{
				$wprog=$wdata[$i][0]."-".$wdata[$i][1];
				$path1="/matrix/presupuestos/reportes/000001_rc125.php?wano=".$wano."&wmesi=".$wmesi."&wmesf=".$wmesf."&wprog=".$wprog."&empresa=".$empresa;
				$path2="/matrix/presupuestos/reportes/000001_rc117.php?wanop=".$wano."&wper1=".$wmesi."&wper2=".$wmesf."&wprog=".$wprog."&wres=D&empresa=".$empresa;
				echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right onclick='ejecutar(".chr(34).$path1.chr(34).")'>".number_format((double)$wdata[$i][2],0,'.',',')."</td><td align=right onclick='ejecutar(".chr(34).$path2.chr(34).")'>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][4],2,'.',',')." %</td><td align=right>".number_format((double)$wdata[$i][5],0,'.',',')."</td></tr>";
			}
			echo"</table></center>";
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
