<html>
<head>
  	<title>MATRIX Listado de Asistencia de Socios Ver. 2009-03-09</title>
</head>
<body  BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='Listado' action='listado_asam.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wemp) or !isset($wtip) or !isset($wpar) or !isset($wfec))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center bgcolor=#cccccc colspan=2><b>IMPRESION DE VOTOS ASAMBLEAS DE LAS AMERICAS</b></td></tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Empresa</td>";			
		echo "<td bgcolor=#cccccc>";
		echo "<select name='wemp'>";
		$query = "select Empcod, Empdes from ".$empresa."_000004 order by Empcod";
		$err1 = mysql_query($query,$conex);
		$num1 = mysql_num_rows($err1);
		for ($i=0;$i<$num1;$i++)
		{	
			$row1 = mysql_fetch_array($err1);
			echo "<option>".$row1[0]."-".$row1[1]."</option>";
		}
		echo "</td></tr>";
		echo "<tr><td bgcolor=#cccccc>TIPO DE ASAMBLEA</td>";			
		echo "<td bgcolor=#cccccc><input type='RADIO' name='wtip' value='0' checked> ORDINARIA <input type='RADIO' name='wtip' value='1'> EXTRAORDINARIA </td></tr>";
		echo "<tr><td bgcolor=#cccccc>TIPO DE PARTICIPACION</td>";			
		echo "<td bgcolor=#cccccc><input type='RADIO' name='wpar' value='0' checked> ACCIONES <input type='RADIO' name='wpar' value='1'> % COPROPIEDAD </td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha de la Asamblea</td><td bgcolor=#cccccc align=center><input type='TEXT' name='wfec' size=10 maxlength=10></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center colspan=2><input type='submit' value='IR'></td></tr></table>";
	}
	else
	{
		switch ($wtip)
		{
			case "0":
				$wtit="ORDINARIA";
			break;
			case "1":
				$wtit="EXTRAORDINARIA";
			break;
		}
		switch ($wpar)
		{
			case "0":
				$wdec=0;
				$wtit2="ACCIONES";
			break;
			case "1":
				$wdec=4;
				$wtit2="% COPROPIEDAD";
			break;
		}
		$query = "SELECT  Acccod, Accnom, Accvap, Accvxp, Accpde  from ".$empresa."_000001  where Accact='on' and Accemp='".substr($wemp,0,2)."' order by Accnom";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		$soc=0;
		$k=0;
		$p="";
		$wsw=0;
		if($num > 0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$k++;
				$row = mysql_fetch_array($err);
				$soc += $row[2];
				$color="#FFFFFF";
				if(substr($row[1],0,1) != $p and $wsw == 0)
				{
					$p=substr($row[1],0,1);
					$k=0;
					echo "</table>";
					echo "</div>";
					echo "<div style='page-break-before: always'>";	
					echo "<table border=1 align=center>";
					echo "<tr><td align=center bgcolor=#999999 colspan=10><font size=6 face='courier new'><b>".substr($wemp,strpos($wemp,"-")+1)."</font></b></font></td></tr>";
					echo "<tr><td align=center bgcolor=#cccccc colspan=10><font size=4 face='courier new'><b>ASAMBLEA ".$wtit." DE SOCIOS</b></font></font></td></tr>";
					echo "<tr><td align=center bgcolor=#cccccc colspan=10><font size=4 face='courier new'><b>Fecha : ".$wfec."</b></font></font></td></tr>";
					echo "<tr><td align=center bgcolor=#dddddd><font face='courier new' size=2><b>CEDULA</b></font></td><td align=center bgcolor=#dddddd><font face='courier new' size=2><b>NOMBRE</b></font></td><td align=center bgcolor=#dddddd><font face='courier new' size=2><b>TOTAL<BR>".$wtit2."</b></font></td></td><td align=center bgcolor=#dddddd><font face='courier new' size=2><b>FIRMA</b></font></td><td align=center bgcolor=#dddddd><font face='courier new' size=2><b>FIRMA<br>DELEGADO</b></font></td><td align=center bgcolor=#dddddd><font face='courier new' size=2><b>NECESITA<br>PODER</b></font></td><td align=center bgcolor=#dddddd><font face='courier new' size=2><b>PUEDE<br>REPRESENTAR</b></font></td></tr>";
				}
				$wsw=0;
				echo "<tr><td bgcolor=".$color."><font face='courier new' size=2>".$row[0]."</font></td>";	
				echo "<td bgcolor=".$color."><font face='courier new' size=2>".$row[1]."</font></td>";	
				echo "<td bgcolor=".$color." align=center><font face='courier new' size=2>".number_format((double)$row[2],$wdec,'.',',')."</font></td>";
				echo "<td bgcolor=".$color."><font face='courier new' size=2>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</font></td>";
				echo "<td bgcolor=".$color."><font face='courier new' size=2>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</font></td>";	
				$poder="NO";
				if ($row[3] == "on")
					$poder="SI";
				$delega="NO";
				if ($row[4] == "on")
					$delega="SI";
				echo "<td bgcolor=".$color." align=center><font face='courier new' size=2>".$poder."</font></td>";
				echo "<td bgcolor=".$color." align=center><font face='courier new' size=2>".$delega."</font></td>";
				if($k == 43)
				{
					$wsw=1;
					$k=0;
					echo "</table>";
					echo "</div>";
					echo "<div style='page-break-before: always'>";	
					echo "<table border=1 align=center>";
					echo "<tr><td align=center bgcolor=#999999 colspan=10><font size=6 face='courier new'><b>".substr($wemp,strpos($wemp,"-")+1)."</font></b></font></td></tr>";
					echo "<tr><td align=center bgcolor=#cccccc colspan=10><font size=4 face='courier new'><b>ASAMBLEA ".$wtit." DE SOCIOS</b></font></font></td></tr>";
					echo "<tr><td align=center bgcolor=#cccccc colspan=10><font size=4 face='courier new'><b>Fecha : ".$wfec."</b></font></font></td></tr>";
					echo "<tr><td align=center bgcolor=#dddddd><font face='courier new' size=2><b>CEDULA</b></font></td><td align=center bgcolor=#dddddd><font face='courier new' size=2><b>NOMBRE</b></font></td><td align=center bgcolor=#dddddd><font face='courier new' size=2><b>TOTAL<BR>".$wtit2."</b></font></td></td><td align=center bgcolor=#dddddd><font face='courier new' size=2><b>FIRMA</b></font></td><td align=center bgcolor=#dddddd><font face='courier new' size=2><b>FIRMA<br>DELEGADO</b></font></td><td align=center bgcolor=#dddddd><font face='courier new' size=2><b>NECESITA<br>PODER</b></font></td><td align=center bgcolor=#dddddd><font face='courier new' size=2><b>PUEDE<br>REPRESENTAR</b></font></td></tr>";
				}
			}
			echo "<tr><td bgcolor=#999999 colspan=4><font face='courier new' size=2><b>TOTAL ACCIONISTAS : ".number_format((double)$num,0,'.',',')."</b></font></td><td bgcolor=#999999 colspan=4><font face='courier new' size=2><b>TOTAL ACCIONES : ".number_format((double)$soc,$wdec,'.',',')."</b></font></td></tr>";	
			echo"</table>";
		}
	}
}
?>
</body>
</html>