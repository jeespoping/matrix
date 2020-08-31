<html>
<head>
  	<title>MATRIX Resumen de Asistencia de Socios</title>
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
	echo "<form name='Resumen' action='Resumen.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wemp) or !isset($wtip) or !isset($wpar) or !isset($wano) or !isset($wmes))
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
		echo "<tr><td bgcolor=#cccccc>Año Asamblea</td><td bgcolor=#cccccc align=center><input type='TEXT' name='wano' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc>Mes Asamblea</td><td bgcolor=#cccccc align=center><input type='TEXT' name='wmes' size=2 maxlength=2></td></tr>";
		echo "<tr><td bgcolor=#cccccc>TIPO DE ASAMBLEA</td>";			
		echo "<td bgcolor=#cccccc><input type='RADIO' name='wtip' value='0' checked> ORDINARIA <input type='RADIO' name='wtip' value='1'> EXTRAORDINARIA </td></tr>";
		echo "<tr><td bgcolor=#cccccc>TIPO DE PARTICIPACION</td>";			
		echo "<td bgcolor=#cccccc><input type='RADIO' name='wpar' value='0' checked> ACCIONES <input type='RADIO' name='wpar' value='1'> % COPROPIEDAD </td></tr>";
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
				$wtit2="ACCI.";
			break;
			case "1":
				$wdec=4;
				$wtit2="% COPR.";
			break;
		}
		$query  = " select Acccod, Accnom, Accvap,'NO','NO','N' FROM asamblea_000001 ";
    	$query .= " 	where Acccod not in (select Movcac from asamblea_000005 where movemp = '".substr($wemp,0,2)."' and movano = ".$wano." and movmes = ".$wmes." and movcpa = 'PR') ";
     	$query .= " 	  and Accact = 'on' ";
     	$query .= " 	  and Accemp = '".substr($wemp,0,2)."' ";
		$query .= " union  ";
		$query .= " select Acccod, Accnom, Accvap, Movcpa, Movdel, Movval FROM asamblea_000001,asamblea_000005  ";
	    $query .= " 	where Acccod = Movcac ";
	    $query .= "  	  and Accact = 'on' ";
	    $query .= "       and Accemp = '".substr($wemp,0,2)."' ";
	    $query .= "       and Accemp = movemp ";
	    $query .= "       and movano = ".$wano;
	    $query .= "       and movmes = ".$wmes; 
	    $query .= "       and movcpa = 'PR' ";
		$query .= " 	order by 2 ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		$soc=0;
		$socp=0;
		$nump=0;
		$socd=0;
		$numd=0;
		$k=0;
		$p=0;
		if($num > 0)
		{
			$p++;
			echo "<div style='page-break-before: always'>";	
			echo "<table border=1 align=center>";
			echo "<tr><td align=center bgcolor=#999999 colspan=6><font size=6 face='courier new'><b>".substr($wemp,strpos($wemp,"-")+1)."</font></b></font></td></tr>";
			echo "<tr><td align=center bgcolor=#cccccc colspan=6><font size=4 face='courier new'><b>ASAMBLEA ".$wtit." DE SOCIOS</b></font></font></td></tr>";
			echo "<tr><td align=center bgcolor=#cccccc colspan=6><font size=4 face='courier new'><b>Fecha : ".date("d-m-Y")." Pag.".$p."</b></font></font></td></tr>";
			echo "<tr><td align=center bgcolor=#dddddd><font face='courier new' size=2><b>IDENT.</b></font></td><td align=center bgcolor=#dddddd><font face='courier new' size=2><b>NOMBRE</b></font></td><td align=center bgcolor=#dddddd><font face='courier new' size=2><b>TOT<BR>".$wtit2."</b></font></td></td><td align=center bgcolor=#dddddd><font face='courier new' size=2><b>ASIST</b></font></td><td align=center bgcolor=#dddddd><font face='courier new' size=2><b>DELEGO<br>EN</b></font></td><td align=center bgcolor=#dddddd><font face='courier new' size=2><b>ASIST<br>FINAL</b></font></td></tr>";
			for ($i=0;$i<$num;$i++)
			{
				$k++;
				$row = mysql_fetch_array($err);
				$soc += $row[2];
				$color="#FFFFFF";
				if(strlen($row[1]) > 30)
					$row[1]=substr($row[1],0,40)."<br>".substr($row[1],41);
				echo "<tr><td bgcolor=".$color."><font face='courier new' size=2>".$row[0]."</font></td>";	
				echo "<td bgcolor=".$color."><font face='courier new' size=2>".substr($row[1],0,40)."</font></td>";	
				echo "<td bgcolor=".$color." align=center><font face='courier new' size=2>".number_format((double)$row[2],$wdec,'.',',')."</font></td>";
				if($row[3] == "PR")
				{
					$nump++;
					$socp += $row[2];
					echo "<td bgcolor=".$color." align=center><font face='courier new' size=2>SI</font></td>";	
				}
				else
					echo "<td bgcolor=".$color." align=center><font face='courier new' size=2>NO</font></td>";
				if($row[4] != "NO")	
				{
					$numd++;
					$socd += $row[2];
					$query  = " select Acccod, Accnom FROM asamblea_000001 where Acccod = '".$row[4]."'";
					$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					if($num1 > 0)
					{
						$row1 = mysql_fetch_array($err1);
						//echo "<td bgcolor=".$color."><font face='courier new' size=2>".$row1[0]."-".$row1[1]."</font></td>";
						echo "<td bgcolor=".$color."><font face='courier new' size=2>".substr($row1[1],0,40)."</font></td>";
					}
					else
						echo "<td bgcolor=".$color."><font face='courier new' size=2>".$row[4]."</font></td>";	
				}
				else
					echo "<td bgcolor=".$color."><font face='courier new' size=2>".$row[4]."</font></td>";	
				if($row[5] == "S")
					echo "<td bgcolor=".$color." align=center><font face='courier new' size=2>SI</font></td>";	
				else
					echo "<td bgcolor=".$color." align=center><font face='courier new' size=2>NO</font></td>";
				if($k == 39)
				{
					$k=0;
					$p++;
					echo "</table>";
					echo "</div>";
					echo "<div style='page-break-before: always'>";	
					echo "<br><table border=1 align=center>";
					echo "<tr><td align=center bgcolor=#999999 colspan=6><font size=6 face='courier new'><b>".substr($wemp,strpos($wemp,"-")+1)."</font></b></font></td></tr>";
					echo "<tr><td align=center bgcolor=#cccccc colspan=6><font size=4 face='courier new'><b>ASAMBLEA ".$wtit." DE SOCIOS</b></font></font></td></tr>";
					echo "<tr><td align=center bgcolor=#cccccc colspan=6><font size=4 face='courier new'><b>Fecha : ".date("d-m-Y")." Pag.".$p."</b></font></font></td></tr>";
					echo "<tr><td align=center bgcolor=#dddddd><font face='courier new' size=2><b>IDENT.</b></font></td><td align=center bgcolor=#dddddd><font face='courier new' size=2><b>NOMBRE</b></font></td><td align=center bgcolor=#dddddd><font face='courier new' size=2><b>TOT<BR>".$wtit2."</b></font></td></td><td align=center bgcolor=#dddddd><font face='courier new' size=2><b>ASIST</b></font></td><td align=center bgcolor=#dddddd><font face='courier new' size=2><b>DELEGO<br>EN</b></font></td><td align=center bgcolor=#dddddd><font face='courier new' size=2><b>ASIST<br>FINAL</b></font></td></tr>";
				}
			}
			$porp=$socp/$soc * 100;
			$pord=$socd/$socp * 100;
			echo "<tr><td bgcolor=#999999 colspan=6><font face='courier new' size=2><b>TOTAL ACCIONISTAS : ".number_format((double)$num,0,'.',',')." - TOTAL ACCIONES : ".number_format((double)$soc,$wdec,'.',',')."</b></font></td></tr>";	
			echo "<tr><td bgcolor=#999999 colspan=6><font face='courier new' size=2><b>TOTAL ACCIONISTAS PRESENTES: ".number_format((double)$nump,0,'.',',')." - TOTAL ACCIONES PRESENTES: ".number_format((double)$socp,$wdec,'.',',')." - % RESPECTO AL TOTAL: ".number_format((double)$porp,2,'.',',')."%</b></font></td></tr>";	
			echo "<tr><td bgcolor=#999999 colspan=6><font face='courier new' size=2><b>TOTAL ACCIONISTAS DELEGADAS: ".number_format((double)$numd,0,'.',',')." - TOTAL ACCIONES DELEGADAS: ".number_format((double)$socd,$wdec,'.',',')." - % RESPECTO A LAS PRESENTES: ".number_format((double)$pord,2,'.',',')."%</b></font></td></tr>";	
			echo"</table>";
		}
	}
}
?>
</body>
</html>