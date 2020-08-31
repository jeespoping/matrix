<html>
<head>
  	<title>MATRIX</title>
</head>
<body onload=ira() BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
function bt($lin,$arr,$numl)
{
	$bt=0;
	for ($j=0;$j<$numl;$j++)
		if($lin == $arr[$j][0])
			$bt=$j;
	return $bt;
}
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='icompinv' action='icompinv_prueba.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if( !isset($wano) or !isset($wmes) or !isset($wfec) or !isset($wcons) or !isset($wserv) or (strtoupper ($wserv) != "S" and strtoupper ($wserv) != "N"))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>GENERACION DE COMPROBANTE CONTABLE</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Año de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wano' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmes' size=2 maxlength=2></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha Comprobante</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wfec' size=10 maxlength=10></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Consecutivo Inicial</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcons' size=6 maxlength=6></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Comprobante Definitivo? (S/N)</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wserv' size=1 maxlength=1></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$wserv=strtoupper($wserv);
		echo "<table border=0 align=center>";
		echo "<tr><td align=center><IMG SRC='/matrix/images/medical/general/logo_promo.gif'></td></tr>";
		echo "<tr><td align=center bgcolor=#999999><font size=6 face='tahoma'><b>GENERACION DE COMPROBANTE CONTABLE</font> Ver 1.02</b></font></td></tr>";
		$color="#dddddd";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Año : </b>".$wano."</td></tr>";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Mes : </b>".$wmes."</td></tr>";	
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Consecutivo Inicial : </b>".$wcons."</td></tr>";	
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Comprobante Definitivo : </b>".$wserv."</td></tr>";	
		echo "</tr></table><br><br>";
		$query = "Select Cinfue,Cincon,Cincco,Cinnit,Cincue,Cinnat, Cinbaj, sum(Cinval) from  ".$empresa."_000012 ";
		$query .= " where cinano = ".$wano; 
		$query .= "     and cinmes = ".$wmes;
		$query .= " group by cinfue,cincon,cincco,cinnit,Cincue,cinnat  ";
		$query .= " order by cinfue,cincon,cincco,cinnit,Cincue,cinnat ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		$wtotgd=0;
		$wtotgc=0;
		$wcini=$wcons;
		$wcons=$wcons - 1;
		$wkey="";
		echo "<table border=0 align=center>";
		echo "<tr><td align=center bgcolor=#999999><font face='tahoma' size=2><b>CONCEPTO</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>FUENTE</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>DOCUMENTO</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>C. DE C.</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>NIT</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>CUENTA</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>DEBITO</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>CREDITO</b></font></td></tr>";
		if($wserv == "S")
		{
			$conex_o = odbc_connect('contabilidad','','');
			$date=$wfec;
		}
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			if($wkey != $row[0].$row[1])
			{
				$cl=0;
				if($row[0] != substr($wkey,0,2))
					$wcons=0;
				$wcons++;
				$wc=(string)$wcons;
				while(strlen($wc) < 7)
					$wc = "0".$wc;
				$wkey = $row[0].$row[1];
				$wexis=0;
				if($wserv == "S")
				{
					$query="select * from comovenc where movencano = '".substr($date,0,4)."' and movencmes = '".substr($date,5,2)."' and movencfue = '".$row[0]."' and  movencdoc = '".$wc."'";
					$err_o = odbc_do($conex_o,$query);
					while (odbc_fetch_row($err_o) and $wexis == 0)
						$wexis=1;
					if($wexis == 0)
					{
						$query= "insert into comovenc values ('".substr($date,0,4)."','".substr($date,5,2)."','".$row[0]."','".$wc."', '".$key."', '0') ";
						$err_o = odbc_do($conex_o,$query);
					}
				}
				else
					$wexis=1;
			}
			if($i % 2 == 0)
				$color="#9999FF";
			else
				$color="#ffffff";
			if($wexis == 0)
			{
				$cl++;
				$query= "insert into comov values ('".$row[0]."','".$wc."','".$wc."','".substr($date,0,4)."','".substr($date,5,2)."',".$cl.",'".str_replace("-","/",$date)."','".$row[4]."','".$row[2]."', '".$row[3]."' , 'COMPROBANTE DE INVENTARIOS','".$row[5]."',".number_format((double)$row[7],2,'.','').",'',0.00,'0',0.00,0.00,'".$row[6]."','0') ";
				$err_o = odbc_do($conex_o,$query);
			}
			echo "<tr><td bgcolor=".$color."><font face='tahoma' size=2>".$row[1]."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[0]."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wc."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[2]."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[3]."</font></td>";
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[4]."</font></td>";	
			if($row[5] == "1")
			{
				$wtotgd += $row[7];
				echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$row[7],2,'.',',')."</font></td>";	
				echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format(0,2,'.',',')."</font></td></tr>";	
			}
			else
			{
				$wtotgc += $row[7];
				echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format(0,2,'.',',')."</font></td>";	
				echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$row[7],2,'.',',')."</font></td></tr>";	
			}
		}
		echo "<tr><td bgcolor=#999999 align=center colspan=6><font face='tahoma' size=2><b>TOTAL COMPROBANTE</b></font></td><td bgcolor=#999999 align=right><font face='tahoma' size=2><b>$".number_format((double)$wtotgd,2,'.',',')."</b></font></td><td bgcolor=#999999 align=right><font face='tahoma' size=2><b>$".number_format((double)$wtotgc,2,'.',',')."</b></font></td></tr>";	
		echo"</table>";
		
		if( $conex_o )
			odbc_close($conex_o);
		odbc_close_all();
	}
}
?>
</body>
</html>
