<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Evaluacion de Insumos y Medicamentos (Linea 1)</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro162.php Ver. 2016-10-25</b></font></td></tr></table>
</center>
<?php
include_once("conex.php");
function bi($d,$n,$k)
{
	$n--;
	if($n > 0)
	{
		$li=0;
		$ls=$n;
		while ($ls - $li > 1)
		{
			$lm=(integer)(($li + $ls) / 2);
			$val=strncmp(strtoupper($k),strtoupper($d[$lm][0]),20);
			if($val == 0)
				return $lm;
			elseif($val < 0)
					$ls=$lm;
				else
					$li=$lm;
		}
		if(strtoupper($k) == strtoupper($d[$li][0]))
			return $li;
		elseif(strtoupper($k) == strtoupper($d[$ls][0]))
					return $ls;
				else
					return -1;
	}
	elseif(isset($d[0][0]) and $d[0][0] == $k)
			return 0;
		else
			return -1;
}

@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro162.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wmesi))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>EVALUACION DE INSUMOS Y MEDICAMENTOS (LINEA 1)</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesi' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			//                  0     1      2      3      4      5      6      7      8      9      10     11     12     13     14
			$query = "select Mosano,Mosmes,Mostip,Moscon,Moslin,Moscco,Mosent,Mospro,Moscan,Mosipr,Minpro,Cfaeva,Moshis,Mosing,Mosmed ";
			$query .= " from ".$empresa."_000108,".$empresa."_000093,".$empresa."_000060 ";
			$query .= "   where mosano = ".$wanop;
			$query .= " 	and mosmes = ".$wmesi;
			$query .= " 	and mostip = 'FA' "; 
			$query .= " 	and moslin = '1' "; 
			$query .= " 	and mosano = minano ";
			$query .= " 	and mosmes = minmes "; 
			$query .= " 	and mospro = mincod ";
			$query .= " 	and minemp = '01' "; 
			$query .= " 	and moscon = cfacod "; 
			$query .= " 	and cfaemp = '01' "; 
			$query .= " union all "; 
			$query .= " select Mosano,Mosmes,Mostip,Moscon,Moslin,Moscco,Mosent,Mospro,Moscan,Mosipr,0,Cfaeva,Moshis,Mosing,Mosmed  ";
			$query .= " from ".$empresa."_000108,".$empresa."_000060 ";
			$query .= "   where mosano = ".$wanop;
			$query .= " 	and mosmes = ".$wmesi;
			$query .= " 	and mostip = 'FA' "; 
			$query .= " 	and moslin = '1' ";  
			$query .= " 	and moscon = cfacod "; 
			$query .= " 	and cfaemp = '01' "; 
			$query .= " 	and Cfaeva != '1' "; 
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$k1=0;
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$query = "select Pripor ";
					$query .= " from ".$empresa."_000126 ";
					$query .= "   where Priano = ".$wanop;
					$query .= " 	and Primes = ".$wmesi;
					$query .= " 	and Pricco = '".$row[5]."' "; 
					$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					if($num1 > 0)
					{
						$row1 = mysql_fetch_array($err1);
						$porc = ($row1[0] / 100);
						
					}
					else
						$porc = 0;
					$wsw=0;
					if($row[11] == "1")
					{
						$wsw=1;
						$wctt = $row[8] * $row[10] + ($row[9] * $porc);
						$wutt = $row[9] - $wctt;
						$wctv = $row[8] * $row[10];
						$wutv = $row[9] - $wctv;
					}
					elseif($row[11] == "2")
					{
						$wsw=1;
						$wctt = ($row[9] * $porc);
						$wutt = $row[9] - $wctt;
						$wctv = 0;
						$wutv = $row[9];
					}
					
					if($wsw == 1)
					{
						$query = "update ".$empresa."_000108 set Mosctt=".$wctt.",Mosutt=".$wutt.",Mosctv=".$wctv.",Mosutv=".$wutv.",Mosest='on' where Mosano=".$wanop." and Mosmes=".$wmesi." and Mostip='".$row[2]."' and Moscon='".$row[3]."' and Moslin='".$row[4]."' and Moscco='".$row[5]."' and Mosent='".$row[6]."' and Mospro='".$row[7]."' and Moshis='".$row[12]."' and Mosing='".$row[13]."' and Mosmed='".$row[14]."' ";
						$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
						$k1++;
						echo "REGISTROS ACTUALIZADO : ".$k1."<br>";
					}
               	}
			}
			echo "<b>NUMERO DE REGISTROS ACTUALIZADOS : ".$k1."</b><br>";
        }
}		
?>
</body>
</html>
