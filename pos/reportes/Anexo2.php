<html>
<head>
  	<title>MATRIX Anexo 2 Promedios de Formulas x Medico</title>
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
	echo "<form name='Anexo2' action='Anexo2.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wfini) or !isset($wffin) or !isset($wfac))
	{
		$wcolor="#cccccc";
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>PROMEDIOS DE FORMULAS X MEDICO Ver. 2006-06-30</td></tr>";
		echo "<tr><td  bgcolor=".$wcolor.">Fecha Inicial (AAAA-MM-DD) : </td><td bgcolor=".$wcolor."><INPUT TYPE='text' NAME='wfini' value = '".date("Y-m-d")."'></td></tr>";
		echo "<tr><td  bgcolor=".$wcolor.">Fecha Final (AAAA-MM-DD) : </td><td bgcolor=".$wcolor."><INPUT TYPE='text' NAME='wffin' value = '".date("Y-m-d")."'></td></tr>";
		echo "<tr><td  bgcolor=#cccccc>Factura Nro.</td><td bgcolor=#cccccc><INPUT TYPE='text' NAME='wfac'></td></tr>";
		$query =  " SELECT empres, empnom FROM ".$empresa."_000024  where Empfac= 'off' ORDER BY empcod";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		echo "<tr><td bgcolor=".$wcolor.">Empresa : </td><td bgcolor=".$wcolor."><select name='wemp'>";
		for ($i=0;$i<$num;$i++)
		{
		      $row = mysql_fetch_array($err); 
		      echo "<option>".$row[0]."-".$row[1]."</option>";
		 }
		 echo "</select></td></tr>";
		 $query =  " SELECT ccocod, ccodes  FROM ".$empresa."_000003  ORDER BY ccocod ";
		 $err = mysql_query($query,$conex);
		 $num = mysql_num_rows($err);
		 echo "<tr><td bgcolor=".$wcolor.">Centro de Costo : </td><td bgcolor=".$wcolor."><select name='wcco'>";
		 for ($i=0;$i<$num;$i++)
		 {
			$row = mysql_fetch_array($err); 
			echo "<option>".$row[0]."-".$row[1]."</option>";
		 }
		 echo "</select></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		echo "<table border=0 align=center>";
		echo "<tr><td align=center  colspan=7><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=center bgcolor=#999999 colspan=7><font size=6 face='tahoma'><b>PROMEDIOS DE FORMULAS X MEDICO Ver. 2006-06-30</font></b></font></td></tr>";
		echo "<tr><td align=center bgcolor=#cccccc colspan=7><font size=4 face='tahoma'><b>PERIODO : ".$wfini." - ".$wffin."</b></font></font></td></tr>";
		echo "<tr><td align=center bgcolor=#cccccc colspan=7><font size=4 face='tahoma'><b>EMPRESA : ".$wemp."</b></font></font></td></tr>";
		$query = "select Vmppro, Vmpmed,count(*),sum(Venvto),sum(Vencmo),sum(Venvto - Vencmo) from ".$empresa."_000050,".$empresa."_000016  ";
		$query .= " where vmpvta = vennum  ";
		$query .= " and vennfa = '".$wfac."'";
		$query .= " and venfec between '".$wfini."' and '".$wffin."'  ";
		$query .= " and vencco = '".substr($wcco,0,strpos($wcco,"-"))."'  ";
		$query .= " and vencod = '".substr($wemp,0,strpos($wemp,"-"))."'  ";
		$query .= " and venest = 'on' ";
		$query .= " group by vmppro, vmpmed   ";
		$query .= " order by vmppro, vmpmed ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		$wtotg=0;
		$wcang=0;
		$wtcmg=0;
		$wtbrg=0;
		$wtotp=0;
		$wcanp=0;
		$wtcmp=0;
		$wtbrp=0;
		$wpro="";
		if($num > 0)
		{
			echo "<tr><td align=center bgcolor=#dddddd><font face='tahoma' size=2><b>CODIGO</b></font></td><td align=center bgcolor=#dddddd><font face='tahoma' size=2><b>NOMBRE MEDICO</b></font></td><td align=center bgcolor=#dddddd><font face='tahoma' size=2><b>NRo. FORMULAS</b></font></td><td align=center bgcolor=#dddddd ><font face='tahoma' size=2><b>VALOR</b></font></td><td align=center bgcolor=#dddddd ><font face='tahoma' size=2><b>CUOTA<BR> MODERADORA</b></font></td><td align=center bgcolor=#dddddd ><font face='tahoma' size=2><b>VALOR<BR>NETO</b></font></td><td align=center bgcolor=#dddddd><font face='tahoma' size=2><b>PROMEDIO</b></font></td></tr>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($wpro != $row[0])
				{
					if($wpro != "")
					{
						$wprom=$wtotp / $wcanp;
						echo "<tr><td bgcolor=#dddddd colspan=2><font face='tahoma' size=2><b>TOTAL PROGRAMA :</font></td><td bgcolor=#dddddd align=right><font face='tahoma' size=2><b>".number_format((double)$wcanp,0,'.',',')."</b></font></td><td bgcolor=#dddddd align=right><font face='tahoma' size=2><b>$".number_format((double)$wtbrp,0,'.',',')."</b></font></td><td bgcolor=#dddddd align=right><font face='tahoma' size=2><b>$".number_format((double)$wtcmp,0,'.',',')."</b></font></td><td bgcolor=#dddddd align=right><font face='tahoma' size=2><b>$".number_format((double)$wtotp,0,'.',',')."</b></font></td><td bgcolor=#dddddd align=right><font face='tahoma' size=2><b>".number_format((double)$wprom,2,'.',',')."</b></font></td></tr>";	
					}
					$wtotp=0;
					$wcanp=0;
					$wtcmp=0;
					$wtbrp=0;
					$wpro=$row[0];
					if(substr($wpro,0,1) == "-")
						echo "<tr><td bgcolor=#FFCC66 colspan=7><font face='tahoma' size=2>PROGRAMA : No Especificado</font></td></tr>";
					else
						echo "<tr><td bgcolor=#FFCC66 colspan=7><font face='tahoma' size=2>PROGRAMA : ".$wpro."</font></td></tr>";	
				}
				$wcanp += $row[2];
				$wtotp += $row[3];
				$wcang += $row[2];
				$wtotg += $row[3];
				$wtcmp += $row[4];
				$wtcmg += $row[4];
				$wtbrp += $row[5];
				$wtbrg += $row[5];
				$wprom=$row[3] / $row[2];
				if($i % 2 == 0)
					$color="#9999FF";
				else
					$color="#ffffff";
				if(substr($row[1],0,3) == "NO ")
					$row[1] = "No Especificado-No Especificado";
				echo "<tr><td bgcolor=".$color."><font face='tahoma' size=2>".substr($row[1],0,strrpos($row[1],"-"))."</font></td><td bgcolor=".$color."><font face='tahoma' size=2>".substr($row[1],strrpos($row[1],"-") + 1)."</font></td><td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$row[2],0,'.',',')."</font></td><td bgcolor=".$color." align=right><font face='tahoma' size=2>$".number_format((double)$row[5],0,'.',',')."</font></td><td bgcolor=".$color." align=right><font face='tahoma' size=2>$".number_format((double)$row[4],0,'.',',')."</font></td><td bgcolor=".$color." align=right><font face='tahoma' size=2>$".number_format((double)$row[3],0,'.',',')."</font></td><td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$wprom,2,'.',',')."</font></td></tr>";	
			}
			$wprom=$wtotp / $wcanp;
			echo "<tr><td bgcolor=#dddddd colspan=2><font face='tahoma' size=2><b>TOTAL PROGRAMA :</font></td><td bgcolor=#dddddd align=right><font face='tahoma' size=2><b>".number_format((double)$wcanp,0,'.',',')."</b></font></td><td bgcolor=#dddddd align=right><font face='tahoma' size=2><b>$".number_format((double)$wtbrp,0,'.',',')."</b></font></td><td bgcolor=#dddddd align=right><font face='tahoma' size=2><b>$".number_format((double)$wtcmp,0,'.',',')."</b></font></td><td bgcolor=#dddddd align=right><font face='tahoma' size=2><b>$".number_format((double)$wtotp,0,'.',',')."</b></font></td><td bgcolor=#dddddd align=right><font face='tahoma' size=2><b>".number_format((double)$wprom,2,'.',',')."</b></font></td></tr>";	
			$wprom=$wtotg / $wcang;
			echo "<tr><td bgcolor=#999999 colspan=2><font face='tahoma' size=2><b>TOTAL GENERAL :</b></font></td><td bgcolor=#999999 align=right><font face='tahoma' size=2><b>".number_format((double)$wcang,0,'.',',')."</b></font></td><td bgcolor=#999999 align=right><font face='tahoma' size=2><b>$".number_format((double)$wtbrg,0,'.',',')."</b></font></td><td bgcolor=#999999 align=right><font face='tahoma' size=2><b>$".number_format((double)$wtcmg,0,'.',',')."</b></font></td><td bgcolor=#999999 align=right><font face='tahoma' size=2><b>$".number_format((double)$wtotg,0,'.',',')."</b></font></td><td bgcolor=#999999 align=right><font face='tahoma' size=2><b>".number_format((double)$wprom,2,'.',',')."</b></font></td></tr>";	
		}
		echo"</table>";
	}
}
?>
</body>
</html>