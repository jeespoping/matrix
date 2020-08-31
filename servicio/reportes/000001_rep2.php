<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Informe Mensual de Ordenes de Cirugia</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rep2.php Ver. 2012-10-17</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
function busesp($dat,$esp,$n)
{
	for ($j=0;$j<$n;$j++)
		 if(strcmp(substr($dat[$j][0],0,strpos($dat[$j][0],"-")),substr($esp,0,strpos($esp,"-"))) == 0)
		 	return $j;
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rep2.php' method=post>";
		if(!isset($wanop) or !isset($wmesp) or $wmesp < 1 or $wmesp > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>INFORME MENSUAL DE ORDENES DE CIRUGIA</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesp' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$query  = "select subcodigo,descripcion from det_selecciones where medico='servcli' and codigo='002' ";
			$err = mysql_query($query,$conex);
			$nume = mysql_num_rows($err);
			$data=array();
			$total=array();
			for ($i=1;$i<=$nume;$i++)
			{
				$row = mysql_fetch_array($err);
				$data[$i][0] = $row[1];
				$total[$i][0] = $row[1];
				for ($j=1;$j<=12;$j++)
				{
					$data[$i][$j] = 0;
					$total[$i][$j] = 0;
				}
			}
			$data[99][0] = "TOTAL EMPRESA";
			$total[99][0] = "TOTAL GENERAL";
			for ($j=1;$j<=12;$j++)
			{
				$data[99][$j] = 0;
				$total[99][$j] = 0;
			}
			$query  = "select Empresa,Especialidad,Fecha_Recepcion,Fecha_ejecucion,estado from servcli_000001 ";
			$query .= " where estado = '0-RECIBIDA' ";
			$query .= " union all ";
			$query .= " select Empresa,Especialidad,Fecha_Recepcion,Fecha_ejecucion,estado from servcli_000001 ";
			$query .= "  where estado = '1-CANCELADA' "; 
			$query .= "    and year(Fecha_ejecucion) = ".$wanop; 
			$query .= "    and month(Fecha_ejecucion) = ".$wmesp;
			$query .= " union all ";
			$query .= " select Empresa,Especialidad,Fecha_Recepcion,Fecha_ejecucion,estado from servcli_000001 ";
			$query .= "  where estado = '2-REALIZADA' "; 
			$query .= "    and year(Fecha_ejecucion) = ".$wanop; 
			$query .= "    and month(Fecha_ejecucion) = ".$wmesp;
			$query .= " order by 1,2,5 ";

			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$went="";
			$wesp=-1;
			$indext=array();
			$n=-1;
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($went != strtoupper($row[0]))
				{
					if($went != "")
					{
						echo "<div style='page-break-before: always'>";
						echo "<table border=1>";
						echo "<tr><td colspan=13 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
						echo "<tr><td colspan=13 align=center>DIRECCION DE INFORMATICA</td></tr>";
						echo "<tr><td colspan=13 align=center>INFORME MENSUAL DE ORDENES DE CIRUGIA</td></tr>";
						echo "<tr><td colspan=13 align=center>ENTIDAD : ".$went."</td></tr>";
						echo "<tr><td colspan=13 align=center>MES  : ".$wmesp. " A&ntilde;O : ".$wanop."</td></tr>";
						echo "<tr><td rowspan=3 bgcolor=#dddddd><b>ESPECIALIDAD</b></td><td colspan=12 align=center bgcolor=#999999><b>ORDENES</b></td></tr>";
						echo "<tr><td colspan=4 align=center bgcolor=#99CCFF><b>RECIBIDAS</b></td><td colspan=4 align=center bgcolor=#00CC00><b>REALIZADAS</b></td><td colspan=4 align=center bgcolor=#FF0000><b>ANULADAS</b></td></tr>";
						echo "<tr><td align=center bgcolor=#cccccc><b>ANT</b></td><td align=center bgcolor=#cccccc><b>MES</b></td><td align=center bgcolor=#cccccc><b>TOTAL</b></td><td align=center bgcolor=#cccccc><b>%</b></td><td align=center bgcolor=#cccccc><b>ANT</b></td><td align=center bgcolor=#cccccc><b>MES</b></td><td align=center bgcolor=#cccccc><b>TOTAL</b></td><td align=center bgcolor=#cccccc><b>%</b></td><td align=center bgcolor=#cccccc><b>ANT</b></td><td align=center bgcolor=#cccccc><b>MES</b></td><td align=center bgcolor=#cccccc><b>TOTAL</b></td><td align=center bgcolor=#cccccc><b>%</b></td></tr>";
						for ($w=1;$w<=$nume;$w++)
						{
							if($data[99][3] != 0)
								$data[$w][4]=$data[$w][3]/ $data[99][3] * 100;
							else
								$data[$w][4]=0;
							if($data[99][7] != 0)
								$data[$w][8]=$data[$w][7]/ $data[99][7] * 100;
							else
								$data[$w][8]=0;
							if($data[99][11] != 0)
								$data[$w][12]=$data[$w][11]/ $data[99][11] * 100;
							else
								$data[$w][12]=0;
						}
						if($data[99][3] != 0)
							$data[99][4]=$data[99][3]/ $data[99][3] * 100;
						else
							$data[99][4]=0;
						if($data[99][7] != 0)
							$data[99][8]=$data[99][7]/ $data[99][7] * 100;
						else
							$data[99][8]=0;
						if($data[99][11] != 0)
							$data[99][12]=$data[99][11]/ $data[99][11] * 100;
						else
							$data[9][12]=0;
						for ($w=1;$w<=$nume;$w++)
							echo "<tr><td>".$data[$w][0]."</td><td align=right>".number_format((double)$data[$w][1],0,'.',',')."</td><td align=right bgcolor=#dddddd>".number_format((double)$data[$w][2],0,'.',',')."</td><td align=right>".number_format((double)$data[$w][3],0,'.',',')."</td><td align=right bgcolor=#dddddd>".number_format((double)$data[$w][4],2,'.',',')."%</td><td align=right>".number_format((double)$data[$w][5],0,'.',',')."</td><td align=right bgcolor=#dddddd>".number_format((double)$data[$w][6],0,'.',',')."</td><td align=right>".number_format((double)$data[$w][7],0,'.',',')."</td><td align=right bgcolor=#dddddd>".number_format((double)$data[$w][8],2,'.',',')."%</td><td align=right>".number_format((double)$data[$w][9],0,'.',',')."</td><td align=right bgcolor=#dddddd>".number_format((double)$data[$w][10],0,'.',',')."</td><td align=right>".number_format((double)$data[$w][11],0,'.',',')."</td><td align=right bgcolor=#dddddd>".number_format((double)$data[$w][12],2,'.',',')."%</td></td></tr>";
						echo "<tr><td>".$data[99][0]."</td><td align=right>".number_format((double)$data[99][1],0,'.',',')."</td><td align=right bgcolor=#dddddd>".number_format((double)$data[99][2],0,'.',',')."</td><td align=right>".number_format((double)$data[99][3],0,'.',',')."</td><td align=right bgcolor=#dddddd>".number_format((double)$data[99][4],2,'.',',')."%</td><td align=right>".number_format((double)$data[99][5],0,'.',',')."</td><td align=right bgcolor=#dddddd>".number_format((double)$data[99][6],0,'.',',')."</td><td align=right>".number_format((double)$data[99][7],0,'.',',')."</td><td align=right bgcolor=#dddddd>".number_format((double)$data[99][8],2,'.',',')."%</td><td align=right>".number_format((double)$data[99][9],0,'.',',')."</td><td align=right bgcolor=#dddddd>".number_format((double)$data[99][10],0,'.',',')."</td><td align=right>".number_format((double)$data[99][11],0,'.',',')."</td><td align=right bgcolor=#dddddd>".number_format((double)$data[99][12],2,'.',',')."%</td></td></tr>";
						echo"</table><br><br>";
					}
					$went=strtoupper($row[0]);
					for ($w=1;$w<=$nume;$w++)
						for ($j=1;$j<=12;$j++)
							$data[$w][$j] = 0;
					for ($j=1;$j<=12;$j++)
						$data[99][$j] = 0;
				}

				$wesp = (integer)substr($row[1],0,2);
				switch(substr($row[4],0,1))
				{
					case "0":
						$wfecha = strtotime($row[2]);
						if((date("m",$wfecha) < $wmesp and date("Y",$wfecha) == $wanop) or (date("Y",$wfecha) < $wanop))
						{
							$data[$wesp][1] += 1;
							$data[$wesp][3] += 1;
							$data[99][1] += 1;
							$data[99][3] += 1;
							$total[$wesp][1] += 1;
							$total[$wesp][3] += 1;
							$total[99][1] += 1;
							$total[99][3] += 1;
						}
						elseif(date("m",$wfecha) == $wmesp and date("Y",$wfecha) == $wanop)
						{
							$data[$wesp][2] += 1;
							$data[$wesp][3] += 1;
							$data[99][2] += 1;
							$data[99][3] += 1;
							$total[$wesp][2] += 1;
							$total[$wesp][3] += 1;
							$total[99][2] += 1;
							$total[99][3] += 1;
						}
					case "1":
						$wfecha = strtotime($row[2]);
						if((date("m",$wfecha) < $wmesp and date("Y",$wfecha) == $wanop) or (date("Y",$wfecha) < $wanop))
						{
							$data[$wesp][9] += 1;
							$data[$wesp][11] += 1;
							$data[99][9] += 1;
							$data[99][11] += 1;
							$total[$wesp][9] += 1;
							$total[$wesp][11] += 1;
							$total[99][9] += 1;
							$total[99][11] += 1;
						}
						elseif(date("m",$wfecha) == $wmesp and date("Y",$wfecha) == $wanop)
						{
							$data[$wesp][10] += 1;
							$data[$wesp][11] += 1;
							$data[99][10] += 1;
							$data[99][11] += 1;
							$total[$wesp][10] += 1;
							$total[$wesp][11] += 1;
							$total[99][10] += 1;
							$total[99][11] += 1;
						}
					case "2":
						$wfecha = strtotime($row[2]);
						if((date("m",$wfecha) < $wmesp and date("Y",$wfecha) == $wanop) or (date("Y",$wfecha) < $wanop))
						{
							$data[$wesp][5] += 1;
							$data[$wesp][7] += 1;
							$data[99][5] += 1;
							$data[99][7] += 1;
							$total[$wesp][5] += 1;
							$total[$wesp][7] += 1;
							$total[99][5] += 1;
							$total[99][7] += 1;
						}
						elseif(date("m",$wfecha) == $wmesp and date("Y",$wfecha) == $wanop)
						{
							$data[$wesp][6] += 1;
							$data[$wesp][7] += 1;
							$data[99][6] += 1;
							$data[99][7] += 1;
							$total[$wesp][6] += 1;
							$total[$wesp][7] += 1;
							$total[99][6] += 1;
							$total[99][7] += 1;
						}
					}
    		}
			echo "<div style='page-break-before: always'>";
			echo "<table border=1>";
			echo "<tr><td colspan=13 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=13 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=13 align=center>INFORME MENSUAL DE ORDENES DE CIRUGIA</td></tr>";
			echo "<tr><td colspan=13 align=center>ENTIDAD : ".$went."</td></tr>";
			echo "<tr><td colspan=13 align=center>MES  : ".$wmesp. " A&ntilde;O : ".$wanop."</td></tr>";
			echo "<tr><td rowspan=3 bgcolor=#dddddd><b>ESPECIALIDAD</b></td><td colspan=12 align=center bgcolor=#999999><b>ORDENES</b></td></tr>";
			echo "<tr><td colspan=4 align=center bgcolor=#99CCFF><b>RECIBIDAS</b></td><td colspan=4 align=center bgcolor=#00CC00><b>REALIZADAS</b></td><td colspan=4 align=center bgcolor=#FF0000><b>ANULADAS</b></td></tr>";
			echo "<tr><td align=center bgcolor=#cccccc><b>ANT</b></td><td align=center bgcolor=#cccccc><b>MES</b></td><td align=center bgcolor=#cccccc><b>TOTAL</b></td><td align=center bgcolor=#cccccc><b>%</b></td><td align=center bgcolor=#cccccc><b>ANT</b></td><td align=center bgcolor=#cccccc><b>MES</b></td><td align=center bgcolor=#cccccc><b>TOTAL</b></td><td align=center bgcolor=#cccccc><b>%</b></td><td align=center bgcolor=#cccccc><b>ANT</b></td><td align=center bgcolor=#cccccc><b>MES</b></td><td align=center bgcolor=#cccccc><b>TOTAL</b></td><td align=center bgcolor=#cccccc><b>%</b></td></tr>";
			for ($w=1;$w<=$nume;$w++)
			{
				if($data[99][3] != 0)
					$data[$w][4]=$data[$w][3]/ $data[99][3] * 100;
				else
					$data[$w][4]=0;
				if($data[99][7] != 0)
					$data[$w][8]=$data[$w][7]/ $data[99][7] * 100;
				else
					$data[$w][8]=0;
				if($data[99][11] != 0)
					$data[$w][12]=$data[$w][11]/ $data[99][11] * 100;
				else
					$data[$w][12]=0;
			}
			if($data[99][3] != 0)
				$data[99][4]=$data[99][3]/ $data[99][3] * 100;
			else
				$data[99][4]=0;
			if($data[99][7] != 0)
				$data[99][8]=$data[99][7]/ $data[99][7] * 100;
			else
				$data[99][8]=0;
			if($data[99][11] != 0)
				$data[99][12]=$data[99][11]/ $data[99][11] * 100;
			else
				$data[9][12]=0;
			for ($w=1;$w<=$nume;$w++)
				echo "<tr><td>".$data[$w][0]."</td><td align=right>".number_format((double)$data[$w][1],0,'.',',')."</td><td align=right bgcolor=#dddddd>".number_format((double)$data[$w][2],0,'.',',')."</td><td align=right>".number_format((double)$data[$w][3],0,'.',',')."</td><td align=right bgcolor=#dddddd>".number_format((double)$data[$w][4],2,'.',',')."%</td><td align=right>".number_format((double)$data[$w][5],0,'.',',')."</td><td align=right bgcolor=#dddddd>".number_format((double)$data[$w][6],0,'.',',')."</td><td align=right>".number_format((double)$data[$w][7],0,'.',',')."</td><td align=right bgcolor=#dddddd>".number_format((double)$data[$w][8],2,'.',',')."%</td><td align=right>".number_format((double)$data[$w][9],0,'.',',')."</td><td align=right bgcolor=#dddddd>".number_format((double)$data[$w][10],0,'.',',')."</td><td align=right>".number_format((double)$data[$w][11],0,'.',',')."</td><td align=right bgcolor=#dddddd>".number_format((double)$data[$w][12],2,'.',',')."%</td></td></tr>";
			echo "<tr><td>".$data[99][0]."</td><td align=right>".number_format((double)$data[99][1],0,'.',',')."</td><td align=right bgcolor=#dddddd>".number_format((double)$data[99][2],0,'.',',')."</td><td align=right>".number_format((double)$data[99][3],0,'.',',')."</td><td align=right bgcolor=#dddddd>".number_format((double)$data[99][4],2,'.',',')."%</td><td align=right>".number_format((double)$data[99][5],0,'.',',')."</td><td align=right bgcolor=#dddddd>".number_format((double)$data[99][6],0,'.',',')."</td><td align=right>".number_format((double)$data[99][7],0,'.',',')."</td><td align=right bgcolor=#dddddd>".number_format((double)$data[99][8],2,'.',',')."%</td><td align=right>".number_format((double)$data[99][9],0,'.',',')."</td><td align=right bgcolor=#dddddd>".number_format((double)$data[99][10],0,'.',',')."</td><td align=right>".number_format((double)$data[99][11],0,'.',',')."</td><td align=right bgcolor=#dddddd>".number_format((double)$data[99][12],2,'.',',')."%</td></td></tr>";
			echo"</table><br><br>";

			echo "<div style='page-break-before: always'>";
			echo "<table border=1>";
			echo "<tr><td colspan=13 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=13 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=13 align=center>INFORME MENSUAL DE ORDENES DE CIRUGIA</td></tr>";
			echo "<tr><td colspan=13 align=center>TOTAL GENERAL</td></tr>";
			echo "<tr><td colspan=13 align=center>MES  : ".$wmesp. " A&ntilde;O : ".$wanop."</td></tr>";
			echo "<tr><td rowspan=3 bgcolor=#dddddd><b>ESPECIALIDAD</b></td><td colspan=12 align=center bgcolor=#999999><b>ORDENES</b></td></tr>";
			echo "<tr><td colspan=4 align=center bgcolor=#99CCFF><b>RECIBIDAS</b></td><td colspan=4 align=center bgcolor=#00CC00><b>REALIZADAS</b></td><td colspan=4 align=center bgcolor=#FF0000><b>ANULADAS</b></td></tr>";
			echo "<tr><td align=center bgcolor=#cccccc><b>ANT</b></td><td align=center bgcolor=#cccccc><b>MES</b></td><td align=center bgcolor=#cccccc><b>TOTAL</b></td><td align=center bgcolor=#cccccc><b>%</b></td><td align=center bgcolor=#cccccc><b>ANT</b></td><td align=center bgcolor=#cccccc><b>MES</b></td><td align=center bgcolor=#cccccc><b>TOTAL</b></td><td align=center bgcolor=#cccccc><b>%</b></td><td align=center bgcolor=#cccccc><b>ANT</b></td><td align=center bgcolor=#cccccc><b>MES</b></td><td align=center bgcolor=#cccccc><b>TOTAL</b></td><td align=center bgcolor=#cccccc><b>%</b></td></tr>";
			for ($w=1;$w<=$nume;$w++)
			{
				if($total[99][3] != 0)
					$total[$w][4]=$total[$w][3]/ $total[99][3] * 100;
				else
					$total[$w][4]=0;
				if($total[99][7] != 0)
					$total[$w][8]=$total[$w][7]/ $total[99][7] * 100;
				else
					$total[$w][8]=0;
				if($total[99][11] != 0)
					$total[$w][12]=$total[$w][11]/ $total[99][11] * 100;
				else
					$total[$w][12]=0;
			}
			if($total[99][3] != 0)
				$total[99][4]=$total[99][3]/ $total[99][3] * 100;
			else
				$total[99][4]=0;
			if($total[99][7] != 0)
				$total[99][8]=$total[99][7]/ $total[99][7] * 100;
			else
				$total[99][8]=0;
			if($total[99][11] != 0)
				$total[99][12]=$total[99][11]/ $total[99][11] * 100;
			else
				$total[9][12]=0;
			for ($w=1;$w<=$nume;$w++)
				echo "<tr><td>".$total[$w][0]."</td><td align=right>".number_format((double)$total[$w][1],0,'.',',')."</td><td align=right bgcolor=#dddddd>".number_format((double)$total[$w][2],0,'.',',')."</td><td align=right>".number_format((double)$total[$w][3],0,'.',',')."</td><td align=right bgcolor=#dddddd>".number_format((double)$total[$w][4],2,'.',',')."%</td><td align=right>".number_format((double)$total[$w][5],0,'.',',')."</td><td align=right bgcolor=#dddddd>".number_format((double)$total[$w][6],0,'.',',')."</td><td align=right>".number_format((double)$total[$w][7],0,'.',',')."</td><td align=right bgcolor=#dddddd>".number_format((double)$total[$w][8],2,'.',',')."%</td><td align=right>".number_format((double)$total[$w][9],0,'.',',')."</td><td align=right bgcolor=#dddddd>".number_format((double)$total[$w][10],0,'.',',')."</td><td align=right>".number_format((double)$total[$w][11],0,'.',',')."</td><td align=right bgcolor=#dddddd>".number_format((double)$total[$w][12],2,'.',',')."%</td></td></tr>";
			echo "<tr><td>".$total[99][0]."</td><td align=right>".number_format((double)$total[99][1],0,'.',',')."</td><td align=right bgcolor=#dddddd>".number_format((double)$total[99][2],0,'.',',')."</td><td align=right>".number_format((double)$total[99][3],0,'.',',')."</td><td align=right bgcolor=#dddddd>".number_format((double)$total[99][4],2,'.',',')."%</td><td align=right>".number_format((double)$total[99][5],0,'.',',')."</td><td align=right bgcolor=#dddddd>".number_format((double)$total[99][6],0,'.',',')."</td><td align=right>".number_format((double)$total[99][7],0,'.',',')."</td><td align=right bgcolor=#dddddd>".number_format((double)$total[99][8],2,'.',',')."%</td><td align=right>".number_format((double)$total[99][9],0,'.',',')."</td><td align=right bgcolor=#dddddd>".number_format((double)$total[99][10],0,'.',',')."</td><td align=right>".number_format((double)$total[99][11],0,'.',',')."</td><td align=right bgcolor=#dddddd>".number_format((double)$total[99][12],2,'.',',')."%</td></td></tr>";
			echo"</table><br><br>";
		}
	}
?>
</body>
</html>
