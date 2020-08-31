<html>
<head>
<title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Estadisticas Birads</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> Estmamo.php Ver 2012-02-07</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
 @session_start();
 if(!isset($_SESSION['user']))
 echo "error";
 else
 { 
	$key = substr($user,2,strlen($user));
	

	

	echo "<form action='Estmamo.php' method=post>";
	if(!isset($v0) or !isset($v1))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td colspan=2 align=center><b>ESTADISTICAS X BIRADS</b></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha Inicial Inicial</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='v0' size=10 maxlength=10></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha Inicial Final</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='v1' size=10 maxlength=10></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$DATA=array();
		$DATA[0][0]="Categoria 0";
		$DATA[0][1]="No Concluyente. Requiere Estudios Adicionales";
		$DATA[1][0]="Categoria I";
		$DATA[1][1]="Negativa";
		$DATA[2][0]="Categoria II";
		$DATA[2][1]="Hallazgo Benigno";
		$DATA[3][0]="Categoria III";
		$DATA[3][1]="Hallazgo Probablemente Benigno";
		$DATA[4][0]="Categoria IV";
		$DATA[4][1]="Hallazgo Sospechoso";
		$DATA[5][0]="Categoria V";
		$DATA[5][1]="Hallazgo Altamente Sospechoso De Malignidad";
		$DATA[6][0]="Categoria VI";
		$DATA[6][1]="Malignidad Conocida";
		for ($i=0;$i<7;$i++)
			for ($j=2;$j<7;$j++)
				$DATA[$i][$j]=0;
				
		//                 0           
		$query = "select Mamdat from agfa_000009 ";
		$query .= " where Mamfec between '".$v0."' and '".$v1."'";
		$query .= " order by Mamfec";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$MAMO = explode("|", $row[0]);
				for ($j=0;$j<count($MAMO);$j++)
				{
					$TEMP=explode(":", $MAMO[$j]);
					if($TEMP[0] > 60 and $TEMP[0] < 68)
					{
						$DATA[(integer)$TEMP[0]-61][(integer)$TEMP[1]+1]++;
						if($TEMP[1] > 1)
							$DATA[(integer)$TEMP[0]-61][6]++;
					}
				}
			}
		}

		echo "<center><table border=0>";
		echo "<tr><td colspan=6 align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
		echo "<tr><td colspan=6 align=center><b>DIRECCION DE INFORMATICA</b></td></tr>";
		echo "<tr><td colspan=6 align=center><b>ESTADISTICAS X BIRADS</b></td></tr>";
		echo "<tr><td colspan=6 align=center><b>Desde : ".$v0." Hasta ".$v1."</b></td></tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc><b>CATEGORIAS</b></td>";
		echo "<td bgcolor=#cccccc><b>DESCRIPCION</b></td>";
		//echo "<td bgcolor=#cccccc><b>SIN HALLAZGOS</b></td>";
		echo "<td bgcolor=#cccccc><b>DERECHA</b></td>";
		echo "<td bgcolor=#cccccc><b>IZQUIERDA</b></td>";
		echo "<td bgcolor=#cccccc><b>BILATERAL</b></td>";
		echo "<td bgcolor=#cccccc align=center><b>TOTAL<br>HALLAZGOS</b></td>";
		echo "</tr>"; 
		$TOT=array();
		$TOT[0]=0;
		$TOT[1]=0;
		$TOT[2]=0;
		$TOT[3]=0;
		for ($i=0;$i<7;$i++)
		{
			if($i % 2 == 0)
				$color="#99CCFF";
			else
				$color="#FFFFFF";
			$TOT[0] += $DATA[$i][3];
			$TOT[1] += $DATA[$i][4];
			$TOT[2] += $DATA[$i][5];
			$TOT[3] += $DATA[$i][6];
			echo "<tr>";
			echo "<td bgcolor=".$color.">".$DATA[$i][0]."</td>";
			echo "<td bgcolor=".$color.">".$DATA[$i][1]."</td>";
			//echo "<td bgcolor=".$color." align=right>".number_format($DATA[$i][2],0,'.',',')."</td>";
			echo "<td bgcolor=".$color." align=right>".number_format($DATA[$i][3],0,'.',',')."</td>";
			echo "<td bgcolor=".$color." align=right>".number_format($DATA[$i][4],0,'.',',')."</td>";
			echo "<td bgcolor=".$color." align=right>".number_format($DATA[$i][5],0,'.',',')."</td>";
			echo "<td bgcolor=".$color." align=right><b>".number_format($DATA[$i][6],0,'.',',')."</b></td>";
			echo "</tr>"; 
		}
		$color="#cccccc";
		echo "<tr>";
		echo "<td bgcolor=".$color." colspan=2>TOTAL GENERAL</td>";
		echo "<td bgcolor=".$color." align=right>".number_format($TOT[0],0,'.',',')."</td>";
		echo "<td bgcolor=".$color." align=right>".number_format($TOT[1],0,'.',',')."</td>";
		echo "<td bgcolor=".$color." align=right>".number_format($TOT[2],0,'.',',')."</td>";
		echo "<td bgcolor=".$color." align=right><b>".number_format($TOT[3],0,'.',',')."</b></td>";
		echo "</tr>"; 
		echo "</table></center>";
	}
}
?>
</body>
</html>
