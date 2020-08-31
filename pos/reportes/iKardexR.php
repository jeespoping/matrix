<html>
<head>
  	<title>MATRIX Impresion del Kardex</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Impresion del Kardex Resumida</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b>iKardexR.php Ver. 2015-11-03</b></font></tr></td></table>
</center> 
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='iKardexR' action='iKardexR.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wano) or !isset($wmes))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>IMPRESION DEL KARDEX RESUMIDA</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wano' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmes' size=2 maxlength=2></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$wind=strtoupper ($wind);
		if($wmes == 1)
		{
			$wmesa = 12;
			$wanoa = $wano - 1;
		}
		else
		{
			$wmesa = $wmes -1;
			$wanoa = $wano;
		}
		echo "<table border=0 align=center>";
		echo "<tr><td align=center><IMG SRC='/matrix/images/medical/pos/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=center bgcolor=#999999><font size=6 face='tahoma'><b>IMPRESION DEL KARDEX RESUMIDA</font></b></font></td></tr>";
		$color="#dddddd";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>A&Ntilde;O DE PROCESO : </b>".$wano."</td></tr>";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>MES DE PROCESO : </b>".$wmes."</td></tr>";	
		//                 0       1           2            3            4            5            6            7            8            9           10            11
		$query = "SELECT Ccocod, Ccodes, sum(Kxmcsi), sum(Kxmvsi), sum(Kxmcen), sum(Kxmven), sum(Kxmcsa), sum(Kxmvsa), sum(Kxmcsf), sum(Kxmvsf), sum(Kxmcdi), sum(Kxmvdi) from ".$empresa."_000012, ".$empresa."_000003 ";
		$query .= " where  Kxmano = ".$wano;
		$query .= "   and  Kxmmes = ".$wmes;
		$query .= "   and  Kxmcco = Ccocod";
		$query .= " group by Ccocod, Ccodes ";
		$query .= " order by Ccocod ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		echo "<br><br><table border=0 align=center>";
		echo "<tr><td align=center bgcolor=#999999 colspan=2><font face='tahoma' size=2><b>CENTROS DE</b></font></td>";
		echo "<td align=center bgcolor=#999999 colspan=2><font face='tahoma' size=2><b>SALDO INICIAL</b></font></td>";
		echo "<td align=center bgcolor=#999999 colspan=2><font face='tahoma' size=2><b>+ ENTRADAS</b></font></td>";
		echo "<td align=center bgcolor=#999999 colspan=2><font face='tahoma' size=2><b>- SALIDAS</b></font></td>";
		echo "<td align=center bgcolor=#999999 colspan=2><font face='tahoma' size=2><b>SALDO FINAL</b></font></td>";
		echo "<td align=center bgcolor=#999999 colspan=2><font face='tahoma' size=2><b>DIFERENCIAS</b></font></td>";
		echo "<tr><td align=center bgcolor=#999999 colspan=2><font face='tahoma' size=2><b>COSTOS</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>CANTIDAD</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>VALOR</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>CANTIDAD</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>VALOR</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>CANTIDAD</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>VALOR</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>CANTIDAD</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>VALOR</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>CANTIDAD</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>VALOR</b></font></td>";
		$wtot=array();
		$DATA=array();
		for ($i=0;$i<10;$i++)
		$wtot[$i]=0;
		$NR=-1;
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			if(substr($row[0],0,1) != "5" or $row[0] == "5050")
			{
				$NR++;
				if($row[0] == "1270")
					$MD = $NR;
				$DATA[$NR][0] = $row[0];
				$DATA[$NR][1] = $row[1];
				$DATA[$NR][2] = $row[2];
				$DATA[$NR][3] = $row[3];
				$DATA[$NR][4] = $row[4];
				$DATA[$NR][5] = $row[5];
				$DATA[$NR][6] = $row[6];
				$DATA[$NR][7] = $row[7];
				$DATA[$NR][8] = $row[8];
				$DATA[$NR][9] = $row[9];
				$DATA[$NR][10] = $row[10];
				$DATA[$NR][11] = $row[11];
				$wtot[0] +=$row[2];
				$wtot[1] +=$row[3];
				$wtot[2] +=$row[4];
				$wtot[3] +=$row[5];
				$wtot[4] +=$row[6];
				$wtot[5] +=$row[7];
				$wtot[6] +=$row[8];
				$wtot[7] +=$row[9];
				$wtot[8] +=$row[10];
				$wtot[9] +=$row[11];
			}
			else
			{
				$DATA[$MD][2] += $row[2];
				$DATA[$MD][3] += $row[3];
				$DATA[$MD][4] += $row[4];
				$DATA[$MD][5] += $row[5];
				$DATA[$MD][6] += $row[6];
				$DATA[$MD][7] += $row[7];
				$DATA[$MD][8] += $row[8];
				$DATA[$MD][9] += $row[9];
				$DATA[$MD][10] += $row[10];
				$DATA[$MD][11] += $row[11];
				$wtot[0] +=$row[2];
				$wtot[1] +=$row[3];
				$wtot[2] +=$row[4];
				$wtot[3] +=$row[5];
				$wtot[4] +=$row[6];
				$wtot[5] +=$row[7];
				$wtot[6] +=$row[8];
				$wtot[7] +=$row[9];
				$wtot[8] +=$row[10];
				$wtot[9] +=$row[11];
			}
		}
		for ($i=0;$i<=$NR;$i++)
		{
			if($i % 2 == 0)
				$color="#9999FF";
			else
				$color="#ffffff";
			echo "<tr>";
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$DATA[$i][0]."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$DATA[$i][1]."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$DATA[$i][2],2,'.',',')."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$DATA[$i][3],2,'.',',')."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$DATA[$i][4],2,'.',',')."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$DATA[$i][5],2,'.',',')."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$DATA[$i][6],2,'.',',')."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$DATA[$i][7],2,'.',',')."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$DATA[$i][8],2,'.',',')."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$DATA[$i][9],2,'.',',')."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$DATA[$i][10],4,'.',',')."</font></td>";
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$DATA[$i][11],4,'.',',')."</font></td>";	
			echo "</tr>";
		}
		echo "<tr>";
		echo "<td bgcolor=#999999 colspan=2><font face='tahoma' size=2 ><b>TOTALES</b></font></td>";	
		echo "<td bgcolor=#999999 align=right><font face='tahoma' size=2><b>".number_format((double)$wtot[0],2,'.',',')."</b></font></td>";	
		echo "<td bgcolor=#999999 align=right><font face='tahoma' size=2><b>".number_format((double)$wtot[1],2,'.',',')."</b></font></td>";	
		echo "<td bgcolor=#999999 align=right><font face='tahoma' size=2><b>".number_format((double)$wtot[2],2,'.',',')."</b></font></td>";	
		echo "<td bgcolor=#999999 align=right><font face='tahoma' size=2><b>".number_format((double)$wtot[3],2,'.',',')."</b></font></td>";	
		echo "<td bgcolor=#999999 align=right><font face='tahoma' size=2><b>".number_format((double)$wtot[4],2,'.',',')."</b></font></td>";	
		echo "<td bgcolor=#999999 align=right><font face='tahoma' size=2><b>".number_format((double)$wtot[5],2,'.',',')."</b></font></td>";	
		echo "<td bgcolor=#999999 align=right><font face='tahoma' size=2><b>".number_format((double)$wtot[6],2,'.',',')."</b></font></td>";	
		echo "<td bgcolor=#999999 align=right><font face='tahoma' size=2><b>".number_format((double)$wtot[7],2,'.',',')."</b></font></td>";	
		echo "<td bgcolor=#999999 align=right><font face='tahoma' size=2><b>".number_format((double)$wtot[8],4,'.',',')."</b></font></td>";	
		echo "<td bgcolor=#999999 align=right><font face='tahoma' size=2><b>".number_format((double)$wtot[9],4,'.',',')."</b></font></td>";	
		echo "</tr>";
		echo"</table>";
	}
}
?>
</body>
</html>
