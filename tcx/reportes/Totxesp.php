<html><head><title>MATRIX</title></head><body BGCOLOR=""><BODY TEXT="#000066"><center><table border=0 align=center><tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>TOTAL CIRUGIAS X ESPECIALIDAD</font></a></tr></td><tr><td align=center bgcolor="#cccccc"><font size=2><b> Totxesp.php Ver 2009-03-06</b></font></tr></td></table></center><?php
include_once("conex.php"); session_start(); if(!isset($_SESSION['user'])) echo "error"; else { 	$key = substr($user,2,strlen($user));	
	
	echo "<form action='Totxesp.php' method=post>";	if(!isset($v0) or !isset($v1))	{		echo  "<center><table border=0>";		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";		echo "<tr><td colspan=2 align=center><b>TOTAL CIRUGIAS X ESPECIALIDAD</b></td></tr>";		echo  "<tr><td bgcolor=#cccccc align=center>Fecha Inicial Inicial</td>";		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='v0' size=10 maxlength=10></td></tr>";		echo  "<tr><td bgcolor=#cccccc align=center>Fecha Inicial Final</td>";		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='v1' size=10 maxlength=10></td></tr>";		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";	}	else	{		$ESP=array();		$e=array();		$query  = "select Espcod, Espdet from tcx_000005 ";		$query .= "  order by Espcod ";		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());		$num = mysql_num_rows($err);		$ne = $num;		for ($i=0;$i<$num;$i++)		{			$row = mysql_fetch_array($err);			$e[$i] = $row[0];			$ESP[(integer)$row[0]][0]=$row[1];			$ESP[(integer)$row[0]][1]=0;			$ESP[(integer)$row[0]][2]=0;		}		//                  0       1       2       3       4            5                6               7                8 		$query  = "select mcitur, Mcicod, Mmemed, Espcod, Espdet, mid(Mcihfi,1,2), mid(Mcihfi,4,1), mid(Mcihin,1,2), mid(Mcihin,4,1) from tcx_000008, tcx_000010, tcx_000006, tcx_000005 ";		$query .= " where Mcifec between '".$v0."' and  '".$v1."' ";		$query .= "   and Mcitur = Mmetur ";		$query .= "   and Mmemed = Medcod ";		$query .= "   and Medesp = Espcod  ";		$query .= " Group by mcitur,Mcicod,Mmemed ";		$query .= "  order by mcitur,Mcicod,Espcod ";		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());		$num = mysql_num_rows($err);		echo "<center><table border=1>";		echo "<tr><td colspan=5 align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";		echo "<tr><td colspan=5 align=center><b>DIRECCION DE INFORMATICA</b></td></tr>";		echo "<tr><td colspan=5 align=center><b>TOTAL CIRUGIAS X ESPECIALIDAD</b></td></tr>";		echo "<tr><td colspan=5 align=center><b>Desde : ".$v0." Hasta ".$v1."</b></td></tr>";		echo "<tr>";		echo "<td bgcolor=#cccccc><b>POSICION</b></td>";		echo "<td bgcolor=#cccccc><b>CODIGO</b></td>";		echo "<td bgcolor=#cccccc><b>DESCRIPCION</b></td>";		echo "<td align=right bgcolor=#cccccc><b>CANTIDAD</b></td>";		echo "<td align=right bgcolor=#cccccc><b>TIEMPO<BR>PROMEDIO</b></td>";		echo "</tr>"; 		$Tot=0;		$kant="";		for ($i=0;$i<$num;$i++)		{			$row = mysql_fetch_array($err);			if($row[0].$row[1] != $kant)			{				if($i > 0)				{					$ESP[(integer)$Esp][1] += 1;					$ESP[(integer)$Esp][2] += (((integer)$whfi+ (integer)$whff / 6) - ((integer)$whii + (integer)$whif / 6));				}				$whfi=$row[5];				$whff=$row[6];				$whii=$row[7];				$whif=$row[8];				$kant = $row[0].$row[1];				$Esp="021";			}			if($row[3] != "021")				$Esp = $row[3];		}		$ESP[(integer)$Esp][1] += 1;		for ($i=0;$i<$ne;$i++)		{			echo "<tr>";			$j = $i + 1;			echo "<td align=center>".$j."</td>";			echo "<td>".$e[$i]."</td>";			echo "<td>".$ESP[(integer)$e[$i]][0]."</td>";			$Tot+=$ESP[(integer)$e[$i]][1];			if($ESP[(integer)$e[$i]][1] > 0)				$ESP[(integer)$e[$i]][2] = $ESP[(integer)$e[$i]][2] / $ESP[(integer)$e[$i]][1];			else				$ESP[(integer)$e[$i]][2] = 0;			echo "<td align=right>".number_format($ESP[(integer)$e[$i]][1],0,'.',',')."</td>";			echo "<td align=right>".number_format($ESP[(integer)$e[$i]][2],4,'.',',')."</td>";			echo "</tr>"; 		}		echo "<tr><td  bgcolor=#999999 colspan=3 align=center><b>TOTALES</b></td>";		echo "<td bgcolor=#999999 align=right><b>".number_format($Tot,0,'.',',')."</b></td>";		echo "<td bgcolor=#999999><b>&nbsp</b></td>";		echo "</tr>"; 		echo "</table></center>"; 	}}?></body></html>