<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Procedimientos y Conjuntos sin Descripcion o Protocolo</font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc94.php Ver. 2016-03-10</b></font></td></tr></table>
</center>
<?php
include_once("conex.php");
function comparacion($vec1,$vec2)
{
	if($vec1[0] > $vec2[0])
		return 1;
	elseif ($vec1[0] < $vec2[0])
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
		

		

		echo "<form action='000001_rc94.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wcco1) or !isset($wemp) or $wemp == "Seleccione"  or !isset($wcco2))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE COSTOS</td></tr>";
			echo "<tr><td align=center colspan=2>PROCEDIMIENTOS Y CONJUNTOS SIN DESCRIPCION O PROTOCOLO</td></tr>";
			echo "<tr>";
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
			echo "<center><table border=1>";
			echo "<tr><td align=center colspan=6><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
			echo "<tr><td align=center  colspan=6><b>APLICACION DE COSTOS</b></td></tr>";
			echo "<tr><td align=center  colspan=6><b>PROCEDIMIENTOS Y CONJUNTOS SIN DESCRIPCION O PROTOCOLO</b></td></tr>";
			echo "<tr><td colspan=6 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td colspan=6 align=center>UNIDAD INICIAL  : ".$wcco1. " UNIDAD FINAL : ".$wcco2."</td></tr>";
			echo "<tr><td><b>C.C.</b></td><td><b>PROTOCOLO</b></td><td><b>GRUPO</b></td><td><b>DESCRIPCION</b></td></tr>";
			$query = "SELECT  Mprcco, Mprpro, Mprgru, Mprtip from ".$empresa."_000095 ";
			$query = $query." where Mprcco between '".$wcco1."' and '".$wcco2."'";
			$query = $query."   and Mpremp = '".$wemp."' ";
			$query = $query."  group by Mprcco, Mprpro, Mprgru, Mprtip ";
			$query = $query."  order by Mprcco, Mprpro, Mprgru, Mprtip ";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			$query = "SELECT   Procco, Propro, Progru  from ".$empresa."_000100 ";
			$query = $query." where Procco between '".$wcco1."' and '".$wcco2."'";
			$query = $query."   and Proemp = '".$wemp."' ";
			$query = $query."  group by Procco,Propro, Progru ";
			$query = $query."  order by Procco,Propro, Progru ";
			$err2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($err2);
			$query = "SELECT   Pqucco, Pqupro, Pqugru  from ".$empresa."_000099 ";
			$query = $query." where Pqucco between '".$wcco1."' and '".$wcco2."'";
			$query = $query."   and Pquemp = '".$wemp."' ";
			$query = $query."  group by Pqucco, Pqupro, Pqugru ";
			$query = $query."  order by Pqucco, Pqupro,  Pqugru ";
			$err3 = mysql_query($query,$conex);
			$num3 = mysql_num_rows($err3);
			//echo "PRIMERA FASE TERMINADA<BR>";
			$wa=array();
			$nump=-1;
			$numc=-1;
			for ($i=0;$i<$num1;$i++)
			{
				$row1 = mysql_fetch_array($err1);
				if($row1[3] == "P" or $row1[3] == "I")
				{
					$nump++;
					$wpro=$row1[1];
					while(strlen($row1[1]) < 10)
						$row1[1]="0".$row1[1];
					$wap[$nump][0] = $row1[0].$row1[1].$row1[2];
					$wap[$nump][1] = $row1[0];
					$wap[$nump][2] = $wpro;
					$wap[$nump][3] = $row1[2];
					$wap[$nump][4] = $row1[3];
				}
				elseif($row1[3] == "C" or $row1[3] == "I")
				{
					$numc++;
					$wpro=$row1[1];
					while(strlen($row1[1]) < 10)
						$row1[1]="0".$row1[1];
					$wac[$numc][0] = $row1[0].$row1[1].$row1[2];
					$wac[$numc][1] = $row1[0];
					$wac[$numc][2] = $wpro;
					$wac[$numc][3] = $row1[2];
					$wac[$numc][4] = $row1[3];
				}
			}
			if($nump > -1)
				usort($wap,'comparacion');
			if($numc > -1)
				usort($wac,'comparacion');
			for ($i=0;$i<$num2;$i++)
			{
				$row1 = mysql_fetch_array($err2);
				$wpro=$row1[1];
				while(strlen($row1[1]) < 10)
					$row1[1]="0".$row1[1];
				$wa2[$i][0] = $row1[0].$row1[1].$row1[2];
				$wa2[$i][1] = $row1[0];
				$wa2[$i][2] = $wpro;
				$wa2[$i][3] = $row1[2];
			}
			if($num2 > 0)	
				usort($wa2,'comparacion');
			for ($i=0;$i<$num3;$i++)
			{
				$row1 = mysql_fetch_array($err3);
				$wpro=$row1[1];
				while(strlen($row1[1]) < 10)
					$row1[1]="0".$row1[1];
				$wa3[$i][0] = $row1[0].$row1[1].$row1[2];
				$wa3[$i][1] = $row1[0];
				$wa3[$i][2] = $wpro;
				$wa3[$i][3] = $row1[2];
			}
			if($num3 > 0)	
				usort($wa3,'comparacion');
			$wdata=array();
			$k1=-1;
			$k2=-1;
			$num=-1;
			if ($nump >  0)
			{
				$k1++;
				$kl1=$wap[$k1][0];
			}
			else
			{
				$kl1='zzzzzzzzzzzzzzzzzzzzz';
				$k1=1;
			}
			if ($num2 >  0)
			{
				$k2++;
				$kl2=$wa2[$k2][0];
			}
			else
			{
				$kl2='zzzzzzzzzzzzzzzzzzzzz';
				$k2=1;
			}
			while ($k1 <= $nump or $k2 <= $num2)
			{
				//echo $kl1." : ".$kl2."<br>";
				if($kl1== $kl2)
				{
					if($k1 <= $nump and $wap[$k1][4] == "I")
					{
						$num++;
						$wdata[$num][0]=$wap[$k1][1];
						$wdata[$num][1]=$wap[$k1][2];
						$wdata[$num][2]=$wap[$k1][3];
						$wdata[$num][3]="PROTOCOLO INACTIVO";
					}
					$k1++;
					$k2++;
					if($k1 > $nump)
						$kl1="zzzzzzzzzzzzzzzzzzzzz";
					else
					{
						$kl1=$wap[$k1][0];
					}
					if($k2 >= $num2)
						$kl2="zzzzzzzzzzzzzzzzzzzzz";
					else
					{
						$kl2=$wa2[$k2][0];
					}
				}
				else if($kl1 < $kl2)
				{
					$num++;
					$wdata[$num][0]=$wap[$k1][1];
					$wdata[$num][1]=$wap[$k1][2];
					$wdata[$num][2]=$wap[$k1][3];
					$wdata[$num][3]="95 SI - 100 NO";
					if($wap[$k1][4] == "I")
						$wdata[$num][3] .= " PROTOCOLO INACTIVO";
					$k1++;
					if($k1 > $nump)
						$kl1="zzzzzzzzzzzzzzzzzzzzz";
					else
					{
						$kl1=$wap[$k1][0];
					}
				}
				else
				{
					$num++;
					$wdata[$num][0]=$wa2[$k2][1];
					$wdata[$num][1]=$wa2[$k2][2];
					$wdata[$num][2]=$wa2[$k2][3];
					$wdata[$num][3]="100 SI - 95 NO";
					$k2++;
					if($k2 >= $num2)
						$kl2="zzzzzzzzzzzzzzzzzzzzz";
					else
					{
						$kl2=$wa2[$k2][0];
					}
				}
			}
			$k1=-1;
			$k2=-1;
			if ($numc >  0)
			{
				$k1++;
				$kl1=$wac[$k1][0];
			}
			else
			{
				$kl1='zzzzzzzzzzzzzzzzzzzzz';
				$k1=1;
			}
			if ($num3 >  0)
			{
				$k2++;
				$kl2=$wa3[$k2][0];
			}
			else
			{
				$kl2='zzzzzzzzzzzzzzzzzzzzz';
				$k2=1;
			}
			while ($k1 <= $numc or $k2 <= $num3)
			{
				if($kl1== $kl2)
				{
					if($k1 <= $numc and $wac[$k1][4] == "I")
					{
						$num++;
						$wdata[$num][0]=$wac[$k1][1];
						$wdata[$num][1]=$wac[$k1][2];
						$wdata[$num][2]=$wac[$k1][3];
						$wdata[$num][3]="PROTOCOLO INACTIVO";
					}
					$k1++;
					$k2++;
					if($k1 > $numc)
						$kl1="zzzzzzzzzzzzzzzzzzzzz";
					else
					{
						$kl1=$wac[$k1][0];
					}
					if($k2 >= $num3)
						$kl2="zzzzzzzzzzzzzzzzzzzzz";
					else
					{
						$kl2=$wa3[$k2][0];
					}
				}
				else if($kl1 < $kl2)
				{
					$num++;
					$wdata[$num][0]=$wac[$k1][1];
					$wdata[$num][1]=$wac[$k1][2];
					$wdata[$num][2]=$wac[$k1][3];
					$wdata[$num][3]="95 SI - 99 NO";
					if($wac[$k1][4] == "I")
						$wdata[$num][3] .= " PROTOCOLO INACTIVO";
					$k1++;
					if($k1 > $numc)
						$kl1="zzzzzzzzzzzzzzzzzzzzz";
					else
					{
						$kl1=$wac[$k1][0];
					}
				}
				else
				{
					$num++;
					$wdata[$num][0]=$wa3[$k2][1];
					$wdata[$num][1]=$wa3[$k2][2];
					$wdata[$num][2]=$wa3[$k2][3];
					$wdata[$num][3]="99 SI - 95 NO";
					$k2++;
					if($k2 >= $num3)
						$kl2="zzzzzzzzzzzzzzzzzzzzz";
					else
					{
						$kl2=$wa3[$k2][0];
					}
				}
			}
			for ($i=0;$i<=$num;$i++)
			{
				echo "<tr>";
       			echo "<td>".$wdata[$i][0]."</td>";
       			echo "<td>".$wdata[$i][1]."</td>";
       			echo "<td>".$wdata[$i][2]."</td>";
       			echo "<td>".$wdata[$i][3]."</td></tr>";
			}
		}
}
?>
</body>
</html>
