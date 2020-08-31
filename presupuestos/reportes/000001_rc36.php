<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Evaluacion Semanal de Ingresos</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc36.php Ver. 1.00</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc36.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wper1) or $wper1 < 1 or $wper1 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>EVALUACION SEMANAL DE INGRESOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A�o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$d=array();
			$d[0]=31;
			$d[1]=28;
			$d[2]=31;
			$d[3]=30;
			$d[4]=31;
			$d[5]=30;
			$d[6]=31;
			$d[7]=31;
			$d[8]=30;
			$d[9]=31;
			$d[10]=30;
			$d[11]=31;
			$Graph="/matrix/images/medical/presupuestos/rc36_".$wanop."_".$wper1.".jpg";
			$query = "SELECT cierre_ingresos,fecha from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and mes =   ".$wper1;
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			$fecha_cierre=$row[1];
			$dias=(integer)substr($row[1],8,2);
			$mes=(integer)substr($row[1],5,2)-1;
			if($num > 0 and $row[0] == "on")
			{
			$datos=array();
			$query = "select mensaje from ".$empresa."_000001 ";
			$query = $query."  where codigo = 1";
			$query = $query."  and ano = ".$wanop;
			$query = $query."  and mes = ".$wper1;
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$wmen="";
			if($num > 0)
			{
				$row = mysql_fetch_array($err);
				$wmen=$row[0];
			}
			$query = "select sum(Resmon) from ".$empresa."_000043 ";
			$query = $query."  where resano = ".$wanop;
			$query = $query."    and resper = ".$wper1;
			$query = $query."    and rescpr = '100'"; 
			$err = mysql_query($query,$conex);
			$row = mysql_fetch_array($err);
			$datos[1][0]=$row[0];
			$query = "select sum(Resmon) from ".$empresa."_000043 ";
			$query = $query."  where resano = ".$wanop;
			$query = $query."    and resper = ".$wper1;
			$query = $query."    and rescpr = '900'"; 
			$err = mysql_query($query,$conex);
			$row = mysql_fetch_array($err);
			$datos[2][0]=$row[0];
			$query = "select sum(Mioinp),sum(Mioint) from ".$empresa."_000063 ";
			$query = $query."  where mioano = ".$wanop;
			$query = $query."      and miomes = ".$wper1;
			$err = mysql_query($query,$conex);
			$row = mysql_fetch_array($err);
			$datos[4][0]=$row[0];
			$datos[5][0]=$row[1];
			$datos[0][0]=$datos[1][0]+$datos[2][0];
			$datos[3][0]=$datos[4][0]+$datos[5][0];
			$datos[6][0]=1/$d[$mes]*$dias*100;
			$datos[7][0]=$datos[3][0]/$datos[0][0]*100;
			$datos[8][0]=$datos[4][0]/$datos[1][0]*100;
			$datos[9][0]=$datos[5][0]/$datos[2][0]*100;
			$datos[10][0]=$datos[0][0]/$d[$mes];
			$datos[11][0]=$datos[3][0]/$dias;
			$datos[12][0]=($datos[10][0]-$datos[11][0])*-1;
			$datos[13][0]=$datos[11][0]*$d[$mes];
			$datos[14][0]=$datos[13][0]/$datos[0][0]*100;
			$datos[0][1]="INGRESOS PRESUPUESTADOS";
			$datos[1][1]="INGRESOS PROPIOS";
			$datos[2][1]="INGRESOS PARA TERCEROS";
			$datos[3][1]="INGRESOS REALES AL ".$fecha_cierre;
			$datos[4][1]="INGRESOS PROPIOS";
			$datos[5][1]="INGRESOS PARA TERCEROS";
			$datos[6][1]="EJECUCI�N ESPERADA";
			$datos[7][1]="EVOLUCI�N DE LA FACTURACI�N P.M.L.A.";
			$datos[8][1]="INGRESOS PROPIOS";
			$datos[9][1]="INGRESOS PARA TERCEROS";
			$datos[10][1]="INGRESOS PROMEDIO D�A PRESUPUESTADO";
			$datos[11][1]="INGRESOS PROMEDIO D�A REAL";
			$datos[12][1]="DIFERENCIA";
			$datos[13][1]="PROYECCI�N INGRESOS CON BASE PROMEDIO";
			$datos[14][1]="EJECUCION PROYECTADA CON BASE PROMEDIO";
			echo "<table border=1>";
			echo "<tr><td colspan=2 align=right  bgcolor=#dddddd><A HREF='/matrix/presupuestos/reportes/000001_rc37.php?wanop=".$wanop."&wper1=".$wper1."&wres=D&wter=N' target='_blank'><b>Ver Detalle</b></a></td></tr>";
			echo "<tr><td colspan=2 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=2 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=2 align=center>EVALUACION SEMANAL DE INGRESOS</td></tr>";
			echo "<tr><td colspan=2 align=center>PERIODO  : ".$wper1. " A�O : ".$wanop."<B> ACTUALIZADO A : ".$fecha_cierre."</B></td></tr>";
			for ($i=0;$i<15;$i++)
			{
				switch ($i)
				{
					case 0:
						echo"<tr><td bgcolor=#99CCFF><b>".$datos[$i][1]."</b></td><td bgcolor=#99CCFF align=right><b>".number_format((double)$datos[$i][0],0,'.',',')."</b></td></tr>";
					break;
					case 3:
						echo"<tr><td bgcolor=#FFCC66><b>".$datos[$i][1]."</b></td><td bgcolor=#FFCC66 align=right><b>".number_format((double)$datos[$i][0],0,'.',',')."</b></td></tr>";
					break;
					case 6:
						echo"<tr><td colspan=2>&nbsp</td></tr>";
						echo"<tr><td>".$datos[$i][1]."</td><td align=right>".number_format((double)$datos[$i][0],2,'.',',')."%</td></tr>";
					break;
					case 7:
						echo"<tr><td>".$datos[$i][1]."</td><td align=right>".number_format((double)$datos[$i][0],2,'.',',')."%</td></tr>";
					break;
					case 8:
						echo"<tr><td>".$datos[$i][1]."</td><td align=right>".number_format((double)$datos[$i][0],2,'.',',')."%</td></tr>";
					break;
					case 9:
						echo"<tr><td>".$datos[$i][1]."</td><td align=right>".number_format((double)$datos[$i][0],2,'.',',')."%</td></tr>";
					break;
					case 14:
						echo"<tr><td>".$datos[$i][1]."</td><td align=right>".number_format((double)$datos[$i][0],2,'.',',')."%</td></tr>";
					break;
					default:
						echo"<tr><td>".$datos[$i][1]."</td><td align=right>".number_format((double)$datos[$i][0],0,'.',',')."</td></tr>";
					break;
				}
			}
			echo"<tr><td colspan=2>".$wmen."</td></tr>";
			echo "<tr><td colspan=2 align=center><IMG SRC=".$Graph."></td></tr></table>";
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
