<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Montaje de Informacion Maestro de Personal</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b>Conciliacion.php Ver. 2008-12-30</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
function bisiesto($year)
{
	//si es multiplo de 4 y no es multiplo de 100 o es multiplo de 400*/
	return(($year % 4 == 0 and $year % 100 != 0) or $year % 400 == 0);
}
function bi($d,$n,$k,$i)
{
	//$n--;
	if($n > 0)
	{
		$li=0;
		$ls=$n;
		while ($ls - $li > 1)
		{
			$lm=(integer)(($li + $ls) / 2);
			if(strtoupper($k) == strtoupper($d[$lm][$i]))
				return $lm;
			elseif(strtoupper($k) < strtoupper($d[$lm][$i]))
						$ls=$lm;
					else
						$li=$lm;
		}
		if(strtoupper($k) == strtoupper($d[$li][$i]))
			return $li;
		elseif(strtoupper($k) == strtoupper($d[$ls][$i]))
					return $ls;
				else
					return -1;
	}
	else
		return -1;
}
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='Conciliacion.php' method=post>";
	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	

	

	if(!isset($wanopi) or !isset($wanopf) or !isset($wmesi) or !isset($wmesf) or $wmesi < "01" or $wmesi > "12" or $wmesf < "01" or $wmesf > "12" or strlen($wmesi) != 2 or strlen($wmesf) != 2)
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>COMPARACION DE PRESTAMOS EN FARMASTORE VS NOVEDADES DE NOMINA</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Año Inicial de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanopi' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Año Final de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanopf' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesi' size=2 maxlength=2></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Mes Final</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesf' size=2 maxlength=2></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Empresa</td>";
		$query = "SELECT Nomnom, Nomdes from ".$empresa."_000036 group by Nomnom order by Nomnom ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<td bgcolor=#cccccc align=center><select name='wemp'>";
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
		$conex_o = odbc_connect(substr($wemp,0,strpos($wemp,"-")),'','');
		$query  = "select pagcod, sum(pagval) from nopag ";
		$query .= " where pagano between '".$wanopi."' and '".$wanopf."' "; 
		$query .= " and pagmes between '".$wmesi."' and '".$wmesf."' "; 
		$query .= " and pagcon = '5450' ";
		$query .= " group by pagcod "; 
		$query .= " order by pagcod "; 
		$err_o = odbc_do($conex_o,$query);
		$campos= odbc_num_fields($err_o);
		$k1=-1;
		$odbc1=array();
		while (odbc_fetch_row($err_o))
		{
			$k1 = $k1 + 1;
			for($m=1;$m<=$campos;$m++)
			{
				$odbc1[$k1][$m-1]=odbc_result($err_o,$m);
			}
		}
		echo "<B>REGISTROS EN NOMINA DEDUCCIONES: ".$k1."</B><BR>";
		$query  = "select pagcod, sum(pagval) from nopag ";
		$query .= " where pagano between '".$wanopi."' and '".$wanopf."' "; 
		$query .= " and pagmes between '".$wmesi."' and '".$wmesf."' "; 
		$query .= " and pagcon = '0060' ";
		$query .= " group by pagcod "; 
		$query .= " order by pagcod "; 
		$err_o = odbc_do($conex_o,$query);
		$campos= odbc_num_fields($err_o);
		$k2=-1;
		$odbc2=array();
		while (odbc_fetch_row($err_o))
		{
			$k2 = $k2 + 1;
			for($m=1;$m<=$campos;$m++)
			{
				$odbc2[$k2][$m-1]=odbc_result($err_o,$m);
			}
		}
		echo "<B>REGISTROS EN NOMINA PAGOS: ".$k2."</B><BR>";
		$dias=array();
		$dias[1]="31";
		if(bisiesto((integer)$wanopf))
			$dias[2]="29";
		else
			$dias[2]="28";
		$dias[3]="31";
		$dias[4]="30";
		$dias[5]="31";
		$dias[6]="30";
		$dias[7]="31";
		$dias[8]="31";
		$dias[9]="30";
		$dias[10]="31";
		$dias[11]="30";
		$dias[12]="31";
		$wfechai=$wanopi."-".$wmesi."-01";
		$wfechaf=$wanopf."-".$wmesf."-".$dias[(integer)$wmesf];
		$tot=array();
		$tot[0] = 0;
		$tot[1] = 0;
		$tot[2] = 0;
		//                  0       1                        2                    
		$query  = "select Pnocod,Pnonom,sum((Pnoval / Pnocuo)*(Pnocuo - Pnocup)) from ".$empresa."_000046  ";
		$query .= " where pnofec between '".$wfechai."' and '".$wfechaf."' "; 
		$query .= "   and pnoemp = '".substr($wemp,0,strpos($wemp,"-"))."' ";
		$query .= " group by pnocod,Pnonom";
		$query .= " order by pnocod ";
		//echo $query."<br>";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
        $num = mysql_num_rows($err);
        echo "<center><table border=0>";				
		echo "<td colspan=5 bgcolor=#cccccc align=center><font size=5><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></font></td></tr>";
		echo "<tr><td colspan=5 bgcolor=#cccccc align=center><font size=4>COMPARACION DE PRESTAMOS EN FARMASTORE VS NOVEDADES DE NOMINA</font><td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=5 align=center>AÑOs : ".$wanopi."-".$wanopf." MES INICIAL : ".$wmesi." MES FINAL : ".$wmesf."</td></tr>";
		echo "<tr><td colspan=5 bgcolor=#cccccc align=center>EMPRESA: ".$wemp."</td></tr>";
		echo "<tr><td bgcolor=#999999 align=left><b>CODIGO</b></td><td bgcolor=#999999 align=left><b>NOMBRE</b></td><td bgcolor=#999999 align=right><b>VALOR<BR>PRESTAMO</b></td><td bgcolor=#999999 align=right><b>VALOR<BR>NOMINA</b></td><td bgcolor=#999999 align=right><b>DIFERENCIA<BR>ABSOLUTA</b></td></tr>";
        for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$pos=bi($odbc1,$k1,$row[0],0);
			if($pos != -1)
				$val=$odbc1[$pos][1];
			else
				$val=0;
			$pos=bi($odbc2,$k2,$row[0],0);
			if($pos != -1)
				$val -= $odbc2[$pos][1];
			$dif=$row[2] - $val;
			if($i % 2 == 0)
				$color="#99CCFF";
			else
				$color="#FFFFFF";
			$tot[0] += $row[2];
			$tot[1] += $val;
			$tot[2] += $dif;
			echo "<tr><td bgcolor=".$color." align=left>".$row[0]."</td><td bgcolor=".$color." align=left>".$row[1]."</td><td bgcolor=".$color." align=right>".number_format((double)$row[2],0,'.',',')."</td><td bgcolor=".$color." align=right>".number_format((double)$val,0,'.',',')."</td><td bgcolor=".$color." align=right>".number_format((double)$dif,0,'.',',')."</td></tr>";
		}
		$color="#999999";
		echo "<tr><td bgcolor=".$color." colspan=2 align=left>TOTALES</td><td bgcolor=".$color." align=right>".number_format((double)$tot[0],0,'.',',')."</td><td bgcolor=".$color." align=right>".number_format((double)$tot[1],0,'.',',')."</td><td bgcolor=".$color." align=right>".number_format((double)$tot[2],0,'.',',')."</td></tr>";
		$color="#FFFFFF";
		echo "<tr><td bgcolor=".$color." colspan=4 align=left>NRO. EMPLEADOS: </td><td bgcolor=".$color." align=right>".number_format((double)$num,0,'.',',')."</td></tr>";
		
	}
}
?>
</body>
</html>