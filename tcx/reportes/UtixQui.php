<html><head><title>MATRIX</title></head><body BGCOLOR=""><BODY TEXT="#000066"><center><table border=0 align=center><tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>ASIGNACION DE HORAS X QUIROFANO</font></a></tr></td><tr><td align=center bgcolor="#cccccc"><font size=2> <b> UtixQui.php Ver 2009-03-24</b></font></tr></td></table></center><?php
include_once("conex.php");function comparacion($vec1,$vec2){	if($vec1[2] > $vec2[2])		return -1;	elseif ($vec1[2] < $vec2[2])				return 1;			else				return 0;} session_start(); if(!isset($_SESSION['user'])) echo "error"; else { 	$key = substr($user,2,strlen($user));	
	
	echo "<form action='UtixQui.php' method=post>";	if(!isset($v0) or !isset($v1))	{		echo  "<center><table border=0>";		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";		echo "<tr><td colspan=2 align=center><b>ASIGNACION DE HORAS X QUIROFANO</b></td></tr>";		echo  "<tr><td bgcolor=#cccccc align=center>Fecha Inicial Inicial</td>";		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='v0' size=10 maxlength=10></td></tr>";		echo  "<tr><td bgcolor=#cccccc align=center>Fecha Inicial Final</td>";		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='v1' size=10 maxlength=10></td></tr>";		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";	}	else	{		$f1=(integer)substr($v1,0,4)*360 +(integer)substr($v1,5,2)*30 + (integer)substr($v1,8,2);		$f2=(integer)substr($v0,0,4)*360 +(integer)substr($v0,5,2)*30 + (integer)substr($v0,8,2);		$wdias=$f1 - $f2;		$wdias++;		$CIR = array();		$query = "select Quicod from tcx_000012 ";		$query .= " order by Quicod ";		$err = mysql_query($query,$conex);		$num = mysql_num_rows($err);		$nq = $num;		for ($i=0;$i<$num;$i++)		{			$row = mysql_fetch_array($err);			$CIR[$i + 1][0] = $row[0];			$CIR[$i + 1][1] = 0;			$CIR[$i + 1][2] = 0;			$CIR[$i + 1][3] = 0;			$CIR[$i + 1][4] = 0;		}		//                 0       1           2                3                 4              5              6		$query = "select Mcitur, Mciqui, mid(Mcihfi,1,2), mid(Mcihfi,4,1), mid(Mcihin,1,2), mid(Mcihin,4,1), count(*) from tcx_000008 ";		$query .= " where mcifec between  '".$v0."' and  '".$v1."' ";		$query .= "  group by 1,2,3,4,5,6 ";		$query .= "  order by Mcitur ";		$err = mysql_query($query,$conex);		$num = mysql_num_rows($err);		for ($i=0;$i<$num;$i++)		{			$row = mysql_fetch_array($err);			$CIR[$row[1]][1] += 1;			$CIR[$row[1]][2] += $row[6];			$CIR[$row[1]][3] += (((integer)$row[2] + (integer)$row[3] / 6) - ((integer)$row[4] + (integer)$row[5] / 6));		}		echo "<center><table border=1>";		echo "<tr><td colspan=5 align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";		echo "<tr><td colspan=5 align=center><b>DIRECCION DE INFORMATICA</b></td></tr>";		echo "<tr><td colspan=5 align=center><b>ASIGNACION DE HORAS X QUIROFANO</b></td></tr>";		echo "<tr><td colspan=5 align=center><b>Desde : ".$v0." Hasta ".$v1."</b></td></tr>";		echo "<tr>";		echo "<td align=center bgcolor=#cccccc><b>QUIROFANO</b></td>";		echo "<td align=center bgcolor=#cccccc><b>NUMERO DE<br>TURNOS</b></td>";		echo "<td align=center bgcolor=#cccccc><b>NUMERO DE<br>CIRUGIAS</b></td>";		echo "<td align=center bgcolor=#cccccc><b>HORAS X<br>TURNO</b></td>";		echo "<td align=center bgcolor=#cccccc><b>HORAS PROM.<br>DIA</b></td>";		echo "</tr>"; 		$t=array();		$t[1] = 0;		$t[2] = 0;		$t[3] = 0;		$t[4] = 0;		for ($i=1;$i<=$nq;$i++)		{			$CIR[$i][4] = $CIR[$i][3] / $wdias;			$t[1] += $CIR[$i][1];			$t[2] += $CIR[$i][2];			$t[3] += $CIR[$i][3];			$t[4] += $CIR[$i][4];			echo "<tr>";			echo "<td align=center>".$CIR[$i][0]."</td>";			echo "<td align=right>".number_format($CIR[$i][1],0,'.',',')."</td>";			echo "<td align=right>".number_format($CIR[$i][2],0,'.',',')."</td>";			echo "<td align=right>".number_format($CIR[$i][3],0,'.',',')."</td>";			echo "<td align=right>".number_format($CIR[$i][4],4,'.',',')."</td>";			echo "</tr>"; 		}		echo "<tr><td bgcolor=#999999 align=center><b>TOTAL</b></td>";		echo "<td bgcolor=#999999 align=right><b>".number_format($t[1],0,'.',',')."</b></td>";		echo "<td bgcolor=#999999 align=right><b>".number_format($t[2],0,'.',',')."</b></td>";		echo "<td bgcolor=#999999 align=right><b>".number_format($t[3],0,'.',',')."</b></td>";		echo "<td bgcolor=#999999 align=right><b>".number_format($t[4],4,'.',',')."</b></td>";		echo "</tr>"; 		$porc = $t[3] / ($nq * 12 * $wdias) * 100;		echo "<tr><td align=center colspan=5 bgcolor=#99CCFF align=center><b>PROCENTAJE TOTAL DE UTILIZACION : ".number_format($porc,2,'.',',')." </b></td></tr>";		echo "</table></enter>"; 	}}?></body></html>