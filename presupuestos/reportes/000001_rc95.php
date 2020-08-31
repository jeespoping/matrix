<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Reporte de Control Consolidacion Procedimientos</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc95.php Ver. 2016-03-10</b></font></tr></td></table>
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
		

		

		echo "<form action='000001_rc95.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione")
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE COSTOS</td></tr>";
			echo "<tr><td align=center colspan=2>REPORTE DE CONTROL CONSOLIDACION PROCEDIMIENTOS</td></tr>";
			echo "<tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
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
			echo "<center><table border=1>";
			echo "<tr><td align=center colspan=6><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
			echo "<tr><td align=center  colspan=6><b>APLICACION DE COSTOS</b></td></tr>";
			echo "<tr><td align=center  colspan=6><b>REPORTE DE CONTROL CONSOLIDACION PROCEDIMIENTOS</b></td></tr>";
			echo "<tr><td colspan=6 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td><b>C.C.</b></td><td><b>PROTOCOLO</b></td><td><b>CONCEPTO</b></td><td><b>ENTIDAD</b></td><td><b>NOMBRE</b></td></tr>";
			$query = "SELECT  Conccg, Conprg   from ".$empresa."_000080 ";
			$query = $query." where Conemp = '".$wemp."' ";
			$query = $query."   group by  Conccg, Conprg ";
			$query = $query."   order by Conccg, Conprg ";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			$query = "SELECT  Mprcco, Mprpro  from ".$empresa."_000095 ";
			$query = $query." where Mpremp = '".$wemp."' ";
			$query = $query."   group by Mprcco, Mprpro ";
			$query = $query."   order by Mprcco, Mprpro ";
			$err2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($err2);
			echo "PRIMERA FASE TERMINADA<BR>";
			$wap=array();
			$wa2=array();
			$nump=-1;
			for ($i=0;$i<$num1;$i++)
			{
				$row1 = mysql_fetch_array($err1);
				$nump++;
				$wpro=$row1[1];
				while(strlen($row1[1]) < 10)
					$row1[1]="0".$row1[1];
				$wap[$nump][0] = $row1[0].$row1[1];
				$wap[$nump][1] = $row1[0];
				$wap[$nump][2] = $wpro;
			}
			if($nump > -1)
				usort($wap,'comparacion');
			for ($i=0;$i<$num2;$i++)
			{
				$row1 = mysql_fetch_array($err2);
				$wpro=$row1[1];
				while(strlen($row1[1]) < 10)
					$row1[1]="0".$row1[1];
				$wa2[$i][0] = $row1[0].$row1[1];
				$wa2[$i][1] = $row1[0];
				$wa2[$i][2] = $wpro;
			}
			if($num2 > 0)	
				usort($wa2,'comparacion');
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
				$kl1='zzzzzzzzzzzzzz';
				$k1=1;
			}
			if ($num2 >  0)
			{
				$k2++;
				$kl2=$wa2[$k2][0];
			}
			else
			{
				$kl2='zzzzzzzzzzzzzz';
				$k2=1;
			}
			while ($k1 <= $nump or $k2 <= $num2)
			{
				if($kl1== $kl2)
				{
					$k1++;
					$k2++;
					if($k1 > $nump)
						$kl1="zzzzzzzzzzzzzz";
					else
					{
						$kl1=$wap[$k1][0];
					}
					if($k2 >= $num2)
						$kl2="zzzzzzzzzzzzzz";
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
					$wdata[$num][2]="&nbsp";
					$wdata[$num][3]="&nbsp";
					$wdata[$num][4]="PROCEDIMIENTO NO MATRICULADO";
					$k1++;
					if($k1 > $nump)
						$kl1="zzzzzzzzzzzzzz";
					else
					{
						$kl1=$wap[$k1][0];
					}
				}
				else
				{
					$k2++;
					if($k2 >= $num2)
						$kl2="zzzzzzzzzzzzzz";
					else
					{
						$kl2=$wa2[$k2][0];
					}
				}
			}
			$query = "SELECT  Conccn, Conpro  from ".$empresa."_000080 ";
			$query = $query." where Conemp = '".$wemp."' ";
			$query = $query."   group by  Conccn, Conpro ";
			$query = $query."   order by  Conccn, Conpro ";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			$query = "SELECT  Mprcco, Mprpro  from ".$empresa."_000095 ";
			$query = $query." where Mpremp = '".$wemp."' ";
			$query = $query."   group by Mprcco, Mprpro ";
			$query = $query."   order by Mprcco, Mprpro ";
			$err2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($err2);
			echo "SEGUNDA FASE TERMINADA<BR>";
			$wap=array();
			$wa2=array();
			$nump=-1;
			for ($i=0;$i<$num1;$i++)
			{
				$row1 = mysql_fetch_array($err1);
				$nump++;
				$wpro=$row1[1];
				while(strlen($row1[1]) < 10)
					$row1[1]="0".$row1[1];
				$wap[$nump][0] = $row1[0].$row1[1];
				$wap[$nump][1] = $row1[0];
				$wap[$nump][2] = $wpro;
			}
			if($nump > -1)
				usort($wap,'comparacion');
			for ($i=0;$i<$num2;$i++)
			{
				$row1 = mysql_fetch_array($err2);
				$wpro=$row1[1];
				while(strlen($row1[1]) < 10)
					$row1[1]="0".$row1[1];
				$wa2[$i][0] = $row1[0].$row1[1];
				$wa2[$i][1] = $row1[0];
				$wa2[$i][2] = $wpro;
			}
			if($num2 > 0)	
				usort($wa2,'comparacion');
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
				$kl1='zzzzzzzzzzzzzz';
				$k1=1;
			}
			if ($num2 >  0)
			{
				$k2++;
				$kl2=$wa2[$k2][0];
			}
			else
			{
				$kl2='zzzzzzzzzzzzzz';
				$k2=1;
			}
			while ($k1 <= $nump or $k2 <= $num2)
			{
				if($kl1== $kl2)
				{
					$k1++;
					$k2++;
					if($k1 > $nump)
						$kl1="zzzzzzzzzzzzzz";
					else
					{
						$kl1=$wap[$k1][0];
					}
					if($k2 >= $num2)
						$kl2="zzzzzzzzzzzzzz";
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
					$wdata[$num][2]="&nbsp";
					$wdata[$num][3]="&nbsp";
					$wdata[$num][4]="PROCEDIMIENTO NO MATRICULADO";
					$k1++;
					if($k1 > $nump)
						$kl1="zzzzzzzzzzzzzz";
					else
					{
						$kl1=$wap[$k1][0];
					}
				}
				else
				{
					$k2++;
					if($k2 >= $num2)
						$kl2="zzzzzzzzzzzzzz";
					else
					{
						$kl2=$wa2[$k2][0];
					}
				}
			}
			for ($i=0;$i<=$num;$i++)
			{
				echo "<tr>";
       			echo "<td>".$wdata[$i][0]."</td>";
       			echo "<td>".$wdata[$i][1]."</td>";
       			echo "<td>".$wdata[$i][2]."</td>";
       			echo "<td>".$wdata[$i][3]."</td>";
       			echo "<td>".$wdata[$i][4]."</td></tr>";
			}
		}
}
?>
</body>
</html>
