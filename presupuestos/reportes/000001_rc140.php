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
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Conciliacion de Traslados y Explicaciones</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc140.php Ver. 2016-03-18</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc140.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wper1) or !isset($wemp) or $wemp == "Seleccione" or !isset($wanop) or !isset($wcco1)  or !isset($wcco2) or $wper1 < 1 or $wper1 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE COSTOS</td></tr>";
			echo "<tr><td align=center colspan=2>CONCILIACION DE TRASLADOS Y EXPLICACIONES</td></tr>";
			echo "<tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o  Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
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
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$wempt = $wemp;
			$wemp = substr($wemp,0,2);
			$wcco2=strtolower ($wcco2);
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=5 bgcolor=#DDDDDD><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
			echo "<tr><td align=center colspan=5 bgcolor=#DDDDDD><b>APLICACION DE COSTOS</b></td></tr>";
			echo "<tr><td align=center colspan=5 bgcolor=#DDDDDD><b>CONCILIACION DE TRASLADOS Y EXPLICACIONES</b></td></tr>";
			echo "<tr><td align=center colspan=5 bgcolor=#DDDDDD>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td colspan=5 align=center bgcolor=#DDDDDD>PERIODO  : ".$wper1. " A&Ntilde;O : ".$wanop."</td></tr>";
			echo "<tr><td colspan=5 align=center bgcolor=#DDDDDD>CC INICIAL  : ".$wcco1. " CC FINAL : ".$wcco2."</td></tr>";
			echo "<tr><td bgcolor=#cccccc><b>C.C.</b></td><td bgcolor=#cccccc><b>RUBRO</b></td><td bgcolor=#cccccc><b>MONTO<BR>EXPLICACIONES</b></td><td bgcolor=#cccccc><b>MONTO<BR>TRASLADOS</b></td><td bgcolor=#cccccc><b>DIFERENCIA</b></td></tr>";
			//                  0       1        2  
 			$query  = "select Almcco, Almcpr, sum(Almcto) as k from ".$empresa."_000002,".$empresa."_000005 ";
			$query .= " where Almano = ".$wanop; 
			$query .= "   and Almemp = '".$wemp."' ";
			$query .= "   and Almmes = ".$wper1;
			$query .= "   and Almcco between '".$wcco1."' and  '".$wcco2."' ";
			$query .= "   and Almemp = Ccoemp ";
			$query .= "   and Almcco = Ccocod ";
			$query .= "   and Ccocos = 'S' ";
			$query .= "  group by Almcco, Almcpr  ";
			$query .= "  order by Almcco, Almcpr ";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			//                  0       1       2       
			$query  = "select Expcco, Expcpr, sum(Expmon) as k from ".$empresa."_000011,".$empresa."_000005,".$empresa."_000139 ";
			$query .= " where Expano = ".$wanop; 
			$query .= "   and Expemp = '".$wemp."' ";
			$query .= "   and Expper = ".$wper1;
			$query .= "   and Expcco between '".$wcco1."' and  '".$wcco2."' ";
			$query .= "   and Expemp = Ccoemp ";
			$query .= "   and Expcco = Ccocod ";
			$query .= "   and Ccocos = 'S' ";
			$query .= "   and Expemp = Pfeemp ";
			$query .= "   and Expcco = Pfecco ";
			$query .= "   and Expcpr = Pferub ";
			$query .= "   and Expnit = Pfenit ";
			$query .= "   and Pfecon = 'on' ";
			$query .= "  group by Expcco, Expcpr  ";
			$query .= "  order by Expcco, Expcpr ";
			$err2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($err2);
			$wdata=array();
			$k1=0;
			$k2=0;
			$num=-1;
			if ($num1 ==  0)
			{
				$key1='ZZZZZZZ';
				$k1=1;
			}
			else
			{
				$row1 = mysql_fetch_array($err1);
				$key1= $row1[0].$row1[1];
				$k1++;
			}
			if ($num2 ==  0)
			{
				$key2='ZZZZZZZ';
				$k2=1;
			}
			else
			{
				$row2 = mysql_fetch_array($err2);
				$key2= $row2[0].$row2[1];
				$k2++;
			}
			while ($k1 <= $num1 or $k2 <= $num2)
			{
				if($key1 == $key2)
				{
					$num++;
					$wdata[$num][0]=$row1[0];
					$wdata[$num][1]=$row1[1];
					$wdata[$num][2]=$row2[2];
					$wdata[$num][3]=$row1[2];
					$wdata[$num][4]=$row2[2] - $row1[2];
					$k1++;
					$k2++;
					if($k1 > $num1)
						$key1="ZZZZZZZ";
					else
					{
						$row1 = mysql_fetch_array($err1);
						$key1=$row1[0].$row1[1];
					}
					if($k2 > $num2)
						$key2="ZZZZZZZ";
					else
					{
						$row2 = mysql_fetch_array($err2);
						$key2=$row2[0].$row2[1];
					}
				}
				else if($key1 < $key2)
				{
					$num++;
					$wdata[$num][0]=$row1[0];
					$wdata[$num][1]=$row1[1];
					$wdata[$num][2]=0;
					$wdata[$num][3]=$row1[2];
					$wdata[$num][4]=0 - $row1[2];
					$k1++;
					if($k1 > $num1)
						$key1="ZZZZZZZ";
					else
					{
						$row1 = mysql_fetch_array($err1);
						$key1=$row1[0].$row1[1];
					}
				}
				else
				{
					$num++;
					$wdata[$num][0]=$row2[0];
					$wdata[$num][1]=$row2[1];
					$wdata[$num][2]=$row2[2];
					$wdata[$num][3]=0;
					$wdata[$num][4]=$row2[2] - 0;
					$k2++;
					if($k2 > $num2)
						$key2="ZZZZZZZ";
					else
					{
						$row2 = mysql_fetch_array($err2);
						$key2=$row2[0].$row2[1];
					}
				}
			}
			for ($i=0;$i<=$num;$i++)
			{
				if($i % 2 == 0)
					$color = "#99CCFF";
				else
					$color = "#FFFFFF";
				$path1="/matrix/presupuestos/reportes/000001_rc148.php?wanop=".$wanop."&wper1=".$wper1."&wper2=".$wper1."&wcco1=".$wdata[$i][0]."&wcco2=".$wdata[$i][0]."&wcodi=".$wdata[$i][1]."&wcodf=".$wdata[$i][1]."&empresa=".$empresa."&wemp=".$wempt;
				$path2="/matrix/presupuestos/reportes/000001_rc08.php?wanop=".$wanop."&wper1=".$wper1."&wper2=".$wper1."&wcco1=".$wdata[$i][0]."&wcco2=".$wdata[$i][0]."&wcodi=".$wdata[$i][1]."&wcodf=".$wdata[$i][1]."&empresa=".$empresa."&wemp=".$wempt;
				echo "<tr><td bgcolor=".$color."> ".$wdata[$i][0]."</td><td bgcolor=".$color."> ".$wdata[$i][1]."</td><td align=right bgcolor=".$color." onclick='ejecutar(".chr(34).$path1.chr(34).")'> ".number_format($wdata[$i][2],0,'.',',')."</td><td align=right bgcolor=".$color." onclick='ejecutar(".chr(34).$path2.chr(34).")'> ".number_format($wdata[$i][3],0,'.',',')."</td><td align=right bgcolor=".$color."> ".number_format($wdata[$i][4],2,'.',',')."</td></tr>";
			}
			echo "</table>";
		}
}
?>
</body>
</html>
