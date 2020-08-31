<html><head><title>MATRIX</title></head><body BGCOLOR=""><BODY TEXT="#000066"><center><table border=0 align=center><tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>CIRUGIAS URGENTES Y NOCTURNAS</font></a></tr></td><tr><td align=center bgcolor="#cccccc"><font size=2> <b>Nocurg.php Ver. 2008-05-15</b></font></tr></td></table></center><?php
include_once("conex.php");function ver($chain){	if(strpos($chain,"-") === false)		return $chain;	else		return substr($chain,0,strpos($chain,"-"));} session_start(); if(!isset($_SESSION['user'])) echo "error"; else { 	$key = substr($user,2,strlen($user));	
	
	echo "<form action='Nocurg.php' method=post>";	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";	if(!isset($v0) or !isset($v1))	{		echo "<center><table border=0>";		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";		echo "<tr><td colspan=2 align=center><b>CIRUGIAS URGENTES Y NOCTURNAS</b></td></tr>";		echo "<tr><td bgcolor=#cccccc align=center>Fecha Inicial</td>";		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='v0' size=10 maxlength=10></td></tr>";		echo "<tr><td bgcolor=#cccccc align=center>Fecha Final</td>";		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='v1' size=10 maxlength=10></td></tr>";		echo "<tr><td bgcolor=#cccccc colspan=2 align=center><input type='RADIO' name=x value=0> Primer Nivel <input type='RADIO' name=x value=1> Segundo Nivel <input type='RADIO' name=x value=2> Todos </td></tr>";		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";	}	else	{		$query = "SELECT Month(Turfec) , Turtcx, Count(*) ";		$query .= " FROM ".$empresa."_000011 ";		$query .= " WHERE Turfec Between '".$v0."' And '".$v1."' ";		$query .= "   AND Turtip = 'U' ";
		if ($x == 0)
			$query .= " and Turqui between  1 and  10 ";
		elseif ($x == 1)
				$query .= " and Turqui > 10 ";		$query .= "   GROUP BY Month(Turfec), Turtcx ";		$query .= "   ORDER BY Month(Turfec), Turtcx ";		$err = mysql_query($query,$conex);		$num = mysql_num_rows($err);		echo "<table border=1>";		echo "<tr><td colspan=3 align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";		echo "<tr><td colspan=3 align=center><b>DIRECCION DE INFORMATICA</b></td></tr>";		echo "<tr><td colspan=3 align=center><b>CIRUGIAS URGENTES ENTRE FECHAS</b></td></tr>";		echo "<tr><td colspan=3 align=center><b>Desde : ".$v0." Hasta ".$v1."</b></td></tr>";
		switch ($x)
		{
			case 0:
				echo "<tr><td colspan=3 align=center><b>QUIROFANOS PRIMER NIVEL</b></td></tr>";
			break;
			case 1:
				echo "<tr><td colspan=3 align=center><b>QUIROFANOS SEGUNDO NIVEL</b></td></tr>";
			break;
			case 2:
				echo "<tr><td colspan=3 align=center><b>TODOS LOS QUIROFANOS</b></td></tr>";
			break;
		}		echo "<tr>";		echo "<td bgcolor=#cccccc align=center><b>MES</b></td>";		echo "<td bgcolor=#cccccc align=center><b>TIPO DE <BR>CIRUGIA</b></td>";		echo "<td align=right bgcolor=#cccccc><b>CANTIDAD</b></td>";		echo "</tr>"; 		$t=array();		$t[0] = 0;		$t[1] = 0;		$t[2] = 0;		for ($i=0;$i<$num;$i++)		{			$row = mysql_fetch_array($err);			echo "<tr>";			echo "<td align=center>".$row[0]."</td>";			echo "<td align=center>".$row[1]."</td>";			$t[2]+=$row[2];			echo "<td align=right>".number_format($row[2],2,'.',',')."</td>";			echo "</tr>"; 		}		echo "<tr><td  bgcolor=#99CCFF colspan=2 align=center><b>TOTALES</b></td>";		echo "<td bgcolor=#99CCFF align=right><b>".number_format($t[2],2,'.',',')."</b></td>";		echo "</tr>"; 		echo "</table><br><br>"; 				$query = "SELECT '00-07 am', Year(Turfec), Month(Turfec), Count(*) ";		$query .= " FROM ".$empresa."_000011 ";		$query .= " WHERE Turfec Between '".$v0."' And '".$v1."' ";		$query .= "   AND turhin Between '00:00' And '06:30' ";
		if ($x == 0)
			$query .= " and Turqui between  1 and  10 ";
		elseif ($x == 1)
				$query .= " and Turqui > 10 ";		$query .= " GROUP BY Year(turfec), Month(turfec) ";		$query .= " UNION ALL ";		$query .= " SELECT '19-21 am',  Year(Turfec), Month(Turfec), Count(*) ";		$query .= " FROM ".$empresa."_000011 ";		$query .= " WHERE Turfec Between '".$v0."' And '".$v1."' ";		$query .= "   AND Turhin Between '19:00' And '20:30' ";
		if ($x == 0)
			$query .= " and Turqui between  1 and  10 ";
		elseif ($x == 1)
				$query .= " and Turqui > 10 ";		$query .= " GROUP BY Year(Turfec), Month(Turfec) ";		$query .= " UNION ALL ";		$query .= " SELECT '21-24 am',  Year(Turfec), Month(Turfec), Count(*) ";		$query .= " FROM ".$empresa."_000011 ";		$query .= " WHERE Turfec Between '".$v0."' And '".$v1."' ";		$query .= "   AND Turhin Between '21:00' And '23:30' ";
		if ($x == 0)
			$query .= " and Turqui between  1 and  10 ";
		elseif ($x == 1)
				$query .= " and Turqui > 10 ";		$query .= " GROUP BY Year(Turfec), Month(Turfec) ";		$err = mysql_query($query,$conex);		$num = mysql_num_rows($err);		echo "<br><br><table border=1>";		echo "<tr><td colspan=4 align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";		echo "<tr><td colspan=4 align=center><b>DIRECCION DE INFORMATICA</b></td></tr>";		echo "<tr><td colspan=4 align=center><b>CIRUGIAS NOCTURNAS ENTRE FECHAS</b></td></tr>";		echo "<tr><td colspan=4 align=center><b>Desde : ".$v0." Hasta ".$v1."</b></td></tr>";
		switch ($x)
		{
			case 0:
				echo "<tr><td colspan=4 align=center><b>QUIROFANOS PRIMER NIVEL</b></td></tr>";
			break;
			case 1:
				echo "<tr><td colspan=4 align=center><b>QUIROFANOS SEGUNDO NIVEL</b></td></tr>";
			break;
			case 2:
				echo "<tr><td colspan=4 align=center><b>TODOS LOS QUIROFANOS</b></td></tr>";
			break;
		}		echo "<tr>";		echo "<td bgcolor=#cccccc align=center><b>HORARIO</b></td>";		echo "<td bgcolor=#cccccc align=center><b>AÑO</b></td>";		echo "<td bgcolor=#cccccc align=center><b>MES</b></td>";		echo "<td align=right bgcolor=#cccccc><b>CANTIDAD</b></td>";		echo "</tr>"; 		$t=array();		$t[0] = 0;		$t[1] = 0;		$t[2] = 0;		$t[3] = 0;		for ($i=0;$i<$num;$i++)		{			$row = mysql_fetch_array($err);			echo "<tr>";			echo "<td align=center>".$row[0]."</td>";			echo "<td align=center>".$row[1]."</td>";			echo "<td align=center>".$row[2]."</td>";			$t[3]+=$row[3];			echo "<td align=right>".number_format($row[3],2,'.',',')."</td>";			echo "</tr>"; 		}		echo "<tr><td  bgcolor=#99CCFF colspan=3 align=center><b>TOTALES</b></td>";		echo "<td bgcolor=#99CCFF align=right><b>".number_format($t[3],2,'.',',')."</b></td>";		echo "</tr>"; 		echo "</table>"; 	}}?></body></html>