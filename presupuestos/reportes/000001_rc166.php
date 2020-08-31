<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Detalle Facturaci&oacute;n x Entidad - Concepto - CCO</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc166.php Ver. 2016-02-19</b></font></tr></td></table>
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
		

		

		echo "<form action='000001_rc166.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wano1) or !isset($wemp) or $wemp == "Seleccione" or !isset($wano2) or !isset($wper1) or !isset($wper2)  or !isset($went) or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>DETALLE FACTURACI&Oacute;N X ENTIDAD - CONCEPTO - CCO</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wano1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wano2' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Entidad</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='went1' size=3 maxlength=3>-<select name='went'>";
			$query = "SELECT empcin,empdes from ".$empresa."_000061  Group by 1 order by empdes ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($j=0;$j<$num;$j++)
			{	
				$row = mysql_fetch_array($err);
				echo "<option>".$row[0]."-".$row[1]."</option>";
			}	
			echo "</td></tr>";
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
			if($went1 == "")
				$ENT = substr($went,0,strpos($went,"-"));
			else
				$ENT = $went1;
			
			$CIN="(";
			$query  = "select Epmcod from ".$empresa."_000061 ";
			$query .= " where Empcin = '".$ENT."'";
			$query .= "   and Empemp = '".$wemp."'";
			$err2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($err2);
			if($num2 > 0)
			{
				for ($i=0;$i<$num2;$i++)
				{
					$row2 = mysql_fetch_array($err2);
					if($i == 0)
						$CIN .= "'".$row2[0]."'";
					else
						$CIN .= ",'".$row2[0]."'";
				}
			}
			$CIN .= ")";
			
			//                  0      1     2      3      4      5      6      7      8        9
			$query = "select mionit,empcin,empdes,miocco,cconom,precod,prelin,miocfa,cfades,sum(mioito) from ".$empresa."_000063,".$empresa."_000060,".$empresa."_000003,".$empresa."_000061,".$empresa."_000005 ";
			$query = $query."  where mioano = ".$wano1;
			$query = $query."    and mioemp = '".$wemp."'";
			$query = $query."    and miomes between ".$wper1." and ".$wper2;
			$query = $query."    and mionit in ".$CIN;
			$query = $query."    and miocfa = cfacod ";
			$query = $query."    and mioemp = cfaemp ";
			$query = $query."    and cfaclas = precod ";
			$query = $query."    and mionit = epmcod ";
			$query = $query."    and mioemp = empemp ";
			$query = $query."    and miocco = ccocod ";
			$query = $query."    and mioemp = ccoemp ";
			$query = $query." Group by 2,4,6,8 ";
			$query = $query." order by 2,4,6,8 ";
			$err1 = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
			$num1 = mysql_num_rows($err1);
			$query = "select mionit,empcin,empdes,miocco,cconom,precod,prelin,miocfa,cfades,sum(mioito) from ".$empresa."_000063,".$empresa."_000060,".$empresa."_000003,".$empresa."_000061,".$empresa."_000005 ";
			$query = $query."  where mioano = ".$wano2;
			$query = $query."    and mioemp = '".$wemp."'";
			$query = $query."    and miomes between ".$wper1." and ".$wper2;
			$query = $query."    and mionit in ".$CIN;
			$query = $query."    and miocfa = cfacod ";
			$query = $query."    and mioemp = cfaemp ";
			$query = $query."    and cfaclas = precod ";
			$query = $query."    and mionit = epmcod ";
			$query = $query."    and mioemp = empemp ";
			$query = $query."    and miocco = ccocod ";
			$query = $query."    and mioemp = ccoemp ";
			$query = $query." Group by 2,4,6,8 ";
			$query = $query." order by 2,4,6,8 ";
			$err2 = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
			$num2 = mysql_num_rows($err2);
			echo "<table border=1>";
			echo "<tr><td colspan=11 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=11 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=11 align=center>DETALLE FACTURACI&Oacute;N X ENTIDAD - CONCEPTO - CCO</td></tr>";
			echo "<tr><td colspan=11 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td colspan=11 align=center><font size=2>ENTIDAD :<b> ".$went1."</b>:<b> ".$went."</b></font></td></tr>";
			echo "<tr><td colspan=11 align=center><b>A&Ntilde;O INICIAL: ".$wano1." A&Ntilde;O FINAL : ".$wano2."</b></td></tr>";
			echo "<tr><td colspan=11 align=center><b>MES INICIAL: ".$wper1." MES FINAL : ".$wper2."</b></td></tr>";
			echo "<tr><td><b>Codigo<br>Empresa</b></td><td><b>Nombre<br>Empresa</b></td><td><b>Centro de<br>Costos</b></td><td><b>Decripcion</b></td><td><b>Clase de<br>Linea</b></td><td><b>Decripcion</b></td><td><b>Concepto</b></td><td><b>Nombre<br>Concepto</b></td><td><b>Ingreso<br>Total ".$wano1."</b></td><td><b>Ingreso<br>Total ".$wano2."</b></td></tr>";
			$wdata=array();
			$k1=0;
			$k2=0;
			$num=-1;
			if ($num1 ==  0)
			{
				$k1=1;
				$kla1="zzzzzzzzzzzz";
			}
			else
			{
				$row1 = mysql_fetch_array($err1);
				$kla1=$row1[1].$row1[3].$row1[5].$row1[7];
				$k1++;
			}
			if ($num2 ==  0)
			{
				$k2=1;
				$kla2="zzzzzzzzzzzz";
			}
			else
			{
				$row2 = mysql_fetch_array($err2);
				$kla2=$row2[1].$row2[3].$row2[5].$row2[7];
				$k2++;
			}
			while ($k1 <= $num1 or $k2 <= $num2)
			{
				if($kla1 == $kla2)
				{
					$num++;
					$wdata[$num][0]=$row1[1];
					$wdata[$num][1]=$row1[2];
					$wdata[$num][2]=$row1[3];
					$wdata[$num][3]=$row1[4];
					$wdata[$num][4]=$row1[5];
					$wdata[$num][5]=$row1[6];
					$wdata[$num][6]=$row1[7];
					$wdata[$num][7]=$row1[8];
					$wdata[$num][8]=$row1[9];
					$wdata[$num][9]=$row2[9];
					$k1++;
					$k2++;
					if($k1 > $num1)
						$kla1="zzzzzzzzzzzz";
					else
					{
						$row1 = mysql_fetch_array($err1);
						$kla1=$row1[1].$row1[3].$row1[5].$row1[7];
					}
					if($k2 > $num2)
						$kla2="zzzzzzzzzzzz";
					else
					{
						$row2 = mysql_fetch_array($err2);
						$kla2=$row2[1].$row2[3].$row2[5].$row2[7];
					}
				}
				else if($kla1 < $kla2)
				{
					$num++;
					$wdata[$num][0]=$row1[1];
					$wdata[$num][1]=$row1[2];
					$wdata[$num][2]=$row1[3];
					$wdata[$num][3]=$row1[4];
					$wdata[$num][4]=$row1[5];
					$wdata[$num][5]=$row1[6];
					$wdata[$num][6]=$row1[7];
					$wdata[$num][7]=$row1[8];
					$wdata[$num][8]=$row1[9];
					$wdata[$num][9]=0;
					$k1++;
					if($k1 > $num1)
						$kla1="zzzzzzzzzzzz";
					else
					{
						$row1 = mysql_fetch_array($err1);
						$kla1=$row1[1].$row1[3].$row1[5].$row1[7];
					}
				}
				else
				{
					$num++;
					$wdata[$num][0]=$row2[1];
					$wdata[$num][1]=$row2[2];
					$wdata[$num][2]=$row2[3];
					$wdata[$num][3]=$row2[4];
					$wdata[$num][4]=$row2[5];
					$wdata[$num][5]=$row2[6];
					$wdata[$num][6]=$row2[7];
					$wdata[$num][7]=$row2[8];
					$wdata[$num][8]=0;
					$wdata[$num][9]=$row2[9];
					$k2++;
					if($k2 > $num2)
						$kla2="zzzzzzzzzzzz";
					else
					{
						$row2 = mysql_fetch_array($err2);
						$kla2=$row2[1].$row2[3].$row2[5].$row2[7];
					}
				}
			}
			$wtotal1=0;
			$wtotal2=0;
			for ($i=0;$i<=$num;$i++)
				echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td>".$wdata[$i][2]."</td><td>".$wdata[$i][3]."</td><td>".$wdata[$i][4]."</td><td>".$wdata[$i][5]."</td><td>".$wdata[$i][6]."</td><td>".$wdata[$i][7]."</td><td align=right>".number_format((double)$wdata[$i][8],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][9],0,'.',',')."</td></tr>";
		}
	}
?>
</body>
</html>
