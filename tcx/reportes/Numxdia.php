<html><head><title>MATRIX</title></head><body BGCOLOR=""><BODY TEXT="#000066"><center><table border=0 align=center><tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>CIRUGIAS X DIA DE LA SEMANA </font></a></td></tr><tr><td align=center bgcolor="#cccccc"><font size=2> <b> Numxdia.php Ver. 2008-09-04</b></font></td></tr></table></center><?php
include_once("conex.php"); session_start(); if(!isset($_SESSION['user'])) echo "error"; else { 	$key = substr($user,2,strlen($user));	
	
	echo "<form action='Numxdia.php' method=post>";	if(!isset($v0) or !isset($v1) or !isset($v2))	{		echo  "<center><table border=0>";		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";		echo "<tr><td colspan=2 align=center><b>CIRUGIAS X DIA DE LA SEMANA </b></td></tr>";		echo  "<tr><td bgcolor=#cccccc align=center>Fecha Inicial</td>";		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='v0' size=10 maxlength=10></td></tr>";		echo  "<tr><td bgcolor=#cccccc align=center>Fecha Final</td>";		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='v1' size=10 maxlength=10></td></tr>";		echo "<tr><td bgcolor=#cccccc align=center>Medico</td>";
		echo "<td bgcolor=#cccccc align=center>";
		$query = "SELECT Medcod, Mednom   from tcx_000006 order by Mednom ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<select name='v2'>";
			echo "<option>00-TODOS</option>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				echo "<option>".$row[0]."-".$row[1]."</option>";
			}
			echo "</select>";
		}
		echo "</td></tr>";		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";	}	else	{
		if(substr($v2,0,strpos($v2,"-")) == "00")
		{
			$query = "select '00','TODOS',dayname(tcx_000011.turfec),count(*) from tcx_000011 ";
			$query .= "  where tcx_000011.turfec between  '".$v0."' and  '".$v1."'";
			$query .= "    group by 1,2,3";
		}
		else
		{			$query = "select tcx_000006.Medcod,tcx_000006.Mednom,dayname(tcx_000011.turfec),count(*) from tcx_000011,tcx_000010,tcx_000006";
			$query .= "  where tcx_000011.turfec between  '".$v0."' and  '".$v1."'";
			$query .= "    and tcx_000011.turtur = tcx_000010.mmetur ";
			$query .= "    and tcx_000010.Mmemed = '".substr($v2,0,strpos($v2,"-"))."'";
			$query .= "    and tcx_000010.Mmemed = tcx_000006.medcod ";
			$query .= "    group by 1,2,3";
		}		$err = mysql_query($query,$conex);		$num = mysql_num_rows($err);
		if($num > 0)
		{
			$t=array();
			$day=array();
			$nday=array();
			$nday[0]="Sunday";
			$nday[1]="Monday";
			$nday[2]="Tuesday";
			$nday[3]="Wednesday";
			$nday[4]="Thursday";
			$nday[5]="Friday";
			$nday[6]="Saturday";
			$day["Sunday"][0]="Domingo";
			$day["Sunday"][1]=0;
			$day["Monday"][0]="Lunes";
			$day["Monday"][1]=0;
			$day["Tuesday"][0]="Martes";
			$day["Tuesday"][1]=0;
			$day["Wednesday"][0]="Miercoles";
			$day["Wednesday"][1]=0;
			$day["Thursday"][0]="Jueves";
			$day["Thursday"][1]=0;
			$day["Friday"][0]="Viernes";
			$day["Friday"][1]=0;
			$day["Saturday"][0]="Sabado";
			$day["Saturday"][1]=0;
			$t[0] = 0;			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);				$day[$row[2]][1] = $day[$row[2]][1]+$row[3];
				$t[0]+=$row[3];
			}			echo "<center><table border=1>";
			echo "<tr><td colspan=2 align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
			echo "<tr><td colspan=2 align=center><b>DIRECCION DE INFORMATICA</b></td></tr>";
		    echo "<tr><td colspan=2 align=center><b>CIRUGIAS X DIA DE LA SEMANA </b></td></tr>";
			echo "<tr><td colspan=2 align=center><b>X MEDICO</b></td></tr>";
			echo "<tr><td colspan=2 align=center><b>".$row[0]."-".$row[1]."</b></td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc><b>Dia <br> Semana</b></td>";
			echo "<td align=right bgcolor=#cccccc><b>Numero</b></td>";
			echo "</tr>";
			for ($i=0;$i<7;$i++)
			{
				echo "<tr>";
				echo "<td>".$day[$nday[$i]][0]."</td>";
				echo "<td align=right>".number_format($day[$nday[$i]][1],2,'.',',')."</td>";
				echo "</tr>"; 			}
			echo "<tr><td  bgcolor=#99CCFF align=center><b>TOTALES</b></td><td bgcolor=#99CCFF align=right><b>".number_format($t[0],2,'.',',')."</b></td></tr>";
			echo "</table></center>";
		}	}}?></body></html>