<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Proyeccion Ingresos Serv. Farmaceutico (T33)</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro132.php Ver. 2015-11-06</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
function bisiesto($year)
{
	return(($year % 4 == 0 and $year % 100 != 0) or $year % 400 == 0);
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro132.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wper1) or $wper1 < 1 or $wper1 > 12 or !isset($wemp) or $wemp == "Seleccione")
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTACION</td></tr>";
			echo "<tr><td align=center colspan=2>PROYECCION INGRESOS SERV. FARMACEUTICO (T33)</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>A&ntilde;o de Presupuestacion</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Incremento Nominal</td>";
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
			#INICIO PROGRAMA
			$wemp=substr($wemp,0,strpos($wemp,"-"));
			$query = "SELECT Cierre_Ppto from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and mes = 0 ";
			$query = $query."    and Emp = '".$wemp."' ";
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if($num > 0 and $row[0] == "on")
			{
				$k=0;
				$wanopa=$wanop - 1;
				$query = "delete  from ".$empresa."_000033 ";
				$query = $query."  where dipano = ".$wanop;
				$query = $query."      and diptip = 'FA' ";
				$err = mysql_query($query,$conex);
				$query  = "  CREATE TEMPORARY TABLE if not exists temp1 as ";
				$query .= " select Mopano,Mopmes,Mopcco,'06' as a1,Ipfpor,'I' as a2, sum(Mopcan * Ipfipr) as a3,mopcod ";
				$query .= " from ".$empresa."_000081,".$empresa."_000031  ";
				$query .= " where ipfano = ".$wanopa; 
				$query .= "   and ipfemp = '".$wemp."'";
				$query .= "   and ipfcco = mopcco ";  
				$query .= "   and ipfemp = mopemp "; 
				$query .= "   and mopano = ".$wanop; 
				$query .= "   and Mopcod = '12' ";
				$query .= " group by Mopano,Mopmes,Mopcco,'06',Ipfpor,'I',mopcod ";
				$query .= " order by mopano,mopmes,mopcco ";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()."<br>");
				$query = " select Mopano, Mopmes, '1050', a1, sum(Ipfpor),a2, sum(a3), mopcod ";
				$query .= " from temp1 ";
				$query .= " group by Mopano,Mopmes ";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()."<br>");
				$num = mysql_num_rows($err);
				if($num > 0)
				{
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						$query =" select Inees1, Inees2, Inees3    from ".$empresa."_000010 ";
						$query = $query." where Ineano = ".$wanop; 
						$query = $query."   and Ineemp = '".$wemp."' ";
						$query = $query."   and Inecco = '".$row[2]."'"; 
						$query = $query."   and Inetip = '".$row[5]."'"; 
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						if($num1 > 0)
						{
							$row1 = mysql_fetch_array($err1);
							$wes1=$row1[0];
							$wes2=$row1[1];
							$wes3=$row1[2];
						}
						else
						{
							$wes1=0;
							$wes2=0;
							$wes3=0;
						}
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$wit = $row[6] * $row[4] / 100;
						$wip = $row[6] - $wit;
						if($row[1] >= $wper1)
						{
							$wip1 = $wip * (1 + ($wes1 / 100));
							$wip2 = $wip * (1 + ($wes2 / 100));
							$wip3 = $wip * (1 + ($wes3 / 100));
							$wit1 = $wit * (1 + ($wes1 / 100));
							$wit2 = $wit * (1 + ($wes2 / 100));
							$wit3 = $wit * (1 + ($wes3 / 100));
						}
						else
						{
							$wip1 = $wip;
							$wip2 = $wip;
							$wip3 = $wip;
							$wit1 = $wit;
							$wit2 = $wit;
							$wit3 = $wit;
						}
						$query = "insert ".$empresa."_000033 (medico,fecha_data,hora_data,Dipemp, Dipano, Dipmes, Dipcco, Dipip1, Dipit1, Dipip2, Dipit2, Dipip3, Dipit3, Diptip, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$row[1].",'".$row[2]."',".round($wip1, 0).",".round($wit1, 0).",".round($wip2, 0).",".round($wit2, 0).",".round($wip3, 0).",".round($wit3, 0).",'FA','C-".$empresa."')";
						$err2 = mysql_query($query,$conex);
						if ($err2 != 1)
							echo mysql_errno().":".mysql_error()."<br>";
						else
						{
							$k++;
							echo "REGISTRO INSERTADO  : ".$k."<br>";
						}
					}
					echo "<b>NUMERO DE REGISTROS INSERTADOS : ".$k."</b><br>";
				}
			}
			else
			{
				echo "<center><table border=0 aling=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>EL PERIODO  ESTA CERRADO !! -- NO SE PUEDE EJECUTAR ESTE PROCESO</MARQUEE></FONT>";
				echo "<br><br>";			
			}
   		}
}		
?>
</body>
</html>
