<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Procedimientos Comparativos Para Un Segmento Para Un A�o</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc57.php Ver. 1.00</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
function comparacion($vec1,$vec2)
{
	if($vec1[14] > $vec2[14])
		return 1;
	elseif ($vec1[14] < $vec2[14])
				return -1;
			else
				return 0;
}
function titulos($seg)
{
	switch ($seg)
	{
		case "1Q":
			$seg="TOTAL UNIDADES QUIRURGICAS";
		break;
		case "2H":
			$seg="TOTAL UNIDADES DE HOSPITALIZACION";
		break;
		case "3D":
			$seg="TOTAL UNIDADES DE DIAGNOSTICO";
		break;
		case "4A":
			$seg="TOTAL UNIDADES DE ATENCION AMBULATORIA";
		break;
		case "5O":
			$seg="TOTAL OTRAS UNIDADES";
		break;
		case "7E":
			$seg="TOTAL UNIDADES EXTERNAS";
		break;
	}
	$titulos=$seg;
	return $titulos;
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc57.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wseg) or !isset($wper1)  or !isset($wper2) or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>PROCEDIMIENTOS COMPARATIVOS PARA UN SEGMENTO PARA UN A�O</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A�o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Segmento</td>";
			echo "<td bgcolor=#cccccc align=center>";
			$query = "SELECT segcod,segdes from ".$empresa."_000045 order by segdes";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wseg'>";
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
			$ini = strpos($wseg,"-");
			$wsegm=substr($wseg,$ini+1);
			$wseg=substr($wseg,0,$ini);
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
			$query = "SELECT cierre_real,fecha from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and mes =   ".$wper2;
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			$fecha_cierre=$row[1];
			if($num > 0 and $row[0] == "on" and (integer)substr($row[1],8,2) >= $d[(integer)substr($row[1],5,2)-1])
			{
			$query = "select msecco,Cconom,Ccouni,msemes,sum(msecan) as monto from ".$empresa."_000064,".$empresa."_000005 ";
			$query = $query."  where mseano  = ".$wanop;
			$query = $query."      and msemes  between ".$wper1." and ".$wper2;
			$query = $query."      and msecco between '0' and 'z' ";
			$query = $query."      and msecse = '".$wseg."'";
			$query = $query."      and msecco = ccocod   ";
			$query = $query."    group by msecco,Cconom,Ccouni,msemes  ";
			$query = $query."    order by msecco,msemes";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<table border=1>";
			echo "<tr><td colspan=16 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=16 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=16 align=center>PROCEDIMIENTOS COMPARATIVOS PARA UN SEGMENTO PARA UN A�O</td></tr>";
			echo "<tr><td colspan=16 align=center>SEGMENTO : ".$wsegm."</td></tr>";
			echo "<tr><td colspan=16 align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. " A�O : ".$wanop."<B> ACTUALIZADO A : ".$fecha_cierre."</B></td></tr>";
			$wdat=array();
			$wdatt=array();
			$wmeses=array();
			for ($i=$wper1;$i<=$wper2;$i++)
			{
				switch ($i)
					{
						case 1:
							$wmeses[$i]="ENERO";
							break;
						case 2:
							$wmeses[$i]="FEBRERO";
							break;
						case 3:
							$wmeses[$i]="MARZO";
							break;
						case 4:
							$wmeses[$i]="ABRIL";
							break;
						case 5:
							$wmeses[$i]="MAYO";
							break;
						case 6:
							$wmeses[$i]="JUNIO";
							break;
						case 7:
							$wmeses[$i]="JULIO";
							break;
						case 8:
							$wmeses[$i]="AGOSTO";
							break;
						case 9:
							$wmeses[$i]="SEPTIEMBRE";
							break;
						case 10:
							$wmeses[$i]="OCTUBRE";
							break;
						case 11:
							$wmeses[$i]="NOVIEMBRE";
							break;
						case 12:
							$wmeses[$i]="DICIEMBRE";
							break;
					}
			}
			echo "<tr><td><b>UNIDAD</b></td>";
			for ($i=$wper1;$i<=$wper2;$i++)
				echo "<td align=center><b>".$wmeses[$i]."</b></td>";
			echo "<td align=center><b>ACUMULADO</b></td><td align=right><b>% TIPO UNID.</b></td><td align=right><b>% PART.</b></td>";
			for ($i=1;$i<14;$i++)
				$wdatt[$i]=0;
			$seg=-1;
			$segn="";
			$wdatt[0]="TOTAL";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($row[0] != $segn)
				{
					$seg++;
					$segn=$row[0];
					$wdat[$seg][0]=$row[1];
					if($row[2] == "6OGI")
						$row[2] = "5O";
					$wdat[$seg][14]=$row[2];
					for ($j=1;$j<14;$j++)
						$wdat[$seg][$j]=0;
				}
				$wdat[$seg][$row[3]]+=$row[4];
				$wdat[$seg][13]+=$row[4];
				$wdatt[$row[3]]+=$row[4];
				$wdatt[13]+=$row[4];
			}
			$wtip="";
			usort($wdat,'comparacion');
			for ($i=0;$i<=$seg;$i++)
			{
				if($wdat[$i][14] != $wtip)
				{
					for ($j=1;$j<14;$j++)
						$wdatc[(integer)substr($wdat[$i][14],0,1)][$j]=0;
					$wtip=$wdat[$i][14];
				}
				for ($j=$wper1;$j<=$wper2;$j++)
				{
					$wdatc[(integer)substr($wtip,0,1)][$j]+=$wdat[$i][$j];
					$wdatc[(integer)substr($wtip,0,1)][13]+=$wdat[$i][$j];
				}
			}
			$wtip="";	
			if($num > 0)
			{
			for ($i=0;$i<=$seg;$i++)
			{
				if($wdat[$i][14] != $wtip)
				{
					switch ($wdat[$i][14])
					{
						case "1Q":
							echo "<tr><td bgcolor='#FFFFFF' colspan=12><b>UNIDADES QUIRURGICAS</B></td></tr>";
						break;
						case "2H":
							echo"<tr><td bgcolor='#cccccc'><b>".titulos($wtip)."</B></td>";
							for ($j=$wper1;$j<=$wper2;$j++)
								echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$wdatc[(integer)substr($wtip,0,1)][$j],0,'.',',')."</B></td>";	
							echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$wdatc[(integer)substr($wtip,0,1)][13],0,'.',',')."</B></td>";
							$wpor=100;	
							echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$wpor,2,'.',',')."%</B></td>";
							if($wdatt[13] != 0)
								$wpor=$wdatc[(integer)substr($wtip,0,1)][13] / $wdatt[13]*100;
							else
								$wpor=0;		
							echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$wpor,2,'.',',')."%</B></td></tr>";	
							echo "<tr><td bgcolor='#FFFFFF' colspan=12><b>UNIDADES DE HOSPITALIZACION</B></td></tr>";
						break;
						case "3D":
							echo"<tr><td bgcolor='#cccccc'><b>".titulos($wtip)."</B></td>";
							for ($j=$wper1;$j<=$wper2;$j++)
								echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$wdatc[(integer)substr($wtip,0,1)][$j],0,'.',',')."</B></td>";	
							echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$wdatc[(integer)substr($wtip,0,1)][13],0,'.',',')."</B></td>";
							$wpor=100;	
							echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$wpor,2,'.',',')."%</B></td>";
							if($wdatt[13] != 0)
								$wpor=$wdatc[(integer)substr($wtip,0,1)][13] / $wdatt[13]*100;
							else
								$wpor=0;		
							echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$wpor,2,'.',',')."%</B></td></tr>";	
							echo "<tr><td bgcolor='#FFFFFF' colspan=12><b>UNIDADES DE DIAGNOSTICO</B></td></tr>";
						break;
						case "4A":
							echo"<tr><td bgcolor='#cccccc'><b>".titulos($wtip)."</B></td>";
							for ($j=$wper1;$j<=$wper2;$j++)
								echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$wdatc[(integer)substr($wtip,0,1)][$j],0,'.',',')."</B></td>";	
							echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$wdatc[(integer)substr($wtip,0,1)][13],0,'.',',')."</B></td>";
							$wpor=100;	
							echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$wpor,2,'.',',')."%</B></td>";
							if($wdatt[13] != 0)
								$wpor=$wdatc[(integer)substr($wtip,0,1)][13] / $wdatt[13]*100;
							else
								$wpor=0;		
							echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$wpor,2,'.',',')."%</B></td></tr>";	
							echo "<tr><td bgcolor='#FFFFFF' colspan=12><b>UNIDADES DE ATENCION AMBULATORIA</B></td></tr>";
						break;
						case "5O":
							echo"<tr><td bgcolor='#cccccc'><b>".titulos($wtip)."</B></td>";
							for ($j=$wper1;$j<=$wper2;$j++)
								echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$wdatc[(integer)substr($wtip,0,1)][$j],0,'.',',')."</B></td>";	
							echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$wdatc[(integer)substr($wtip,0,1)][13],0,'.',',')."</B></td>";
							$wpor=100;	
							echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$wpor,2,'.',',')."%</B></td>";
							if($wdatt[13] != 0)
								$wpor=$wdatc[(integer)substr($wtip,0,1)][13] / $wdatt[13]*100;
							else
								$wpor=0;		
							echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$wpor,2,'.',',')."%</B></td></tr>";	
							echo "<tr><td bgcolor='#FFFFFF' colspan=12><b>OTRAS UNIDADES</B></td></tr>";
						break;
						case "7E":
							echo"<tr><td bgcolor='#cccccc'><b>".titulos($wtip)."</B></td>";
							for ($j=$wper1;$j<=$wper2;$j++)
								echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$wdatc[(integer)substr($wtip,0,1)][$j],0,'.',',')."</B></td>";	
							echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$wdatc[(integer)substr($wtip,0,1)][13],0,'.',',')."</B></td>";
							$wpor=100;	
							echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$wpor,2,'.',',')."%</B></td>";
							if($wdatt[13] != 0)
								$wpor=$wdatc[(integer)substr($wtip,0,1)][13] / $wdatt[13]*100;
							else
								$wpor=0;		
							echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$wpor,2,'.',',')."%</B></td></tr>";	
							echo "<tr><td bgcolor='#FFFFFF' colspan=12><b>UNIDADES EXTERNAS</B></td></tr>";
						break;
					}
					$wtip=$wdat[$i][14];
				}
				echo"<tr><td>".$wdat[$i][0]."</td>";
				for ($j=$wper1;$j<=$wper2;$j++)
					echo "<td align=right>".number_format((double)$wdat[$i][$j],0,'.',',')."</td>";			
				echo "<td align=right>".number_format((double)$wdat[$i][13],0,'.',',')."</td>";
				if($wdatc[(integer)substr($wdat[$i][14],0,1)][13] != 0)
					$wpor=$wdat[$i][13]/$wdatc[(integer)substr($wtip,0,1)][13]*100;
				else
					$wpor=0;
				echo "<td align=right>".number_format((double)$wpor,2,'.',',')."%</td>";
				if($wdatt[13] != 0)
					$wpor=$wdat[$i][13] / $wdatt[13]*100;
				else
					$wpor=0;		
				echo "<td align=right>".number_format((double)$wpor,2,'.',',')."%</td></tr>";
			}
			switch ($wtip)
			{
				case "1Q":
					echo "<tr><td bgcolor='#cccccc'><b>TOTAL UNIDADES QUIRURGICAS</B></td>";
				break;
				case "2H":
					echo "<tr><td bgcolor='#cccccc'><b>TOTAL UNIDADES DE HOSPITALIZACION</B></td>";
				break;
				case "3D":
					echo "<tr><td bgcolor='#cccccc'><b>TOTAL UNIDADES DE DIAGNOSTICO</B></td>";
				break;
				case "4A":
					echo "<tr><td bgcolor='#cccccc'><b>TOTAL UNIDADES DE ATENCION AMBULATORIA</B></td>";
				break;
				case "5O":
					echo "<tr><td bgcolor='#cccccc'><b>TOTAL OTRAS UNIDADES</B></td>";
				break;
				case "7E":
					echo "<tr><td bgcolor='#cccccc'><b>TOTAL UNIDADES EXTERNAS</B></td>";
				break;
			}
			for ($j=$wper1;$j<=$wper2;$j++)
				echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$wdatc[(integer)substr($wtip,0,1)][$j],0,'.',',')."</B></td>";	
			echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$wdatc[(integer)substr($wtip,0,1)][13],0,'.',',')."</B></td>";
			$wpor=100;	
			echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$wpor,2,'.',',')."%</B></td>";
			if($wdatt[13] != 0)
				$wpor=$wdatc[(integer)substr($wtip,0,1)][13] / $wdatt[13]*100;
			else
				$wpor=0;		
			echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$wpor,2,'.',',')."%</B></td></tr>";	
			echo"<tr><td bgcolor='#99CCFF'><b>".$wdatt[0]."</b></td>";
			for ($j=$wper1;$j<=$wper2;$j++)
				echo "<td bgcolor='#99CCFF' align=right><b>".number_format((double)$wdatt[$j],0,'.',',')."</b></td>";	
			echo "<td bgcolor='#99CCFF' align=right><b>".number_format((double)$wdatt[13],0,'.',',')."</B></td>";
			$wpor=100;	
			echo "<td bgcolor='#99CCFF' align=right><b>".number_format((double)$wpor,2,'.',',')."%</B></td>";
			$wpor=100;	
			echo "<td bgcolor='#99CCFF' align=right><b>".number_format((double)$wpor,2,'.',',')."%</B></td></tr></table>";
		}
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
