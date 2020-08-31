<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Indicador de Cubrimiento de Costos</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc145.php Ver. 2016-03-22</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc145.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wper1) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper2) or !isset($wperc) or !isset($wanop) or !isset($wcco1)  or !isset($wcco2) or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2 or $wperc < 1 or $wperc > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE COSTOS</td></tr>";
			echo "<tr><td align=center colspan=2>INDICADOR DE CUBRIMIENTO DE COSTOS</td></tr>";
			echo "<tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o  Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco2' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes De Costeo</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wperc' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Empresa de  Proceso</td><td bgcolor=#cccccc align=center>";
			$query = "SELECT Empcod,Empdes  from ".$empresa."_000153 order by Empcod";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wemp'>";
				echo "<option>Seleccione</option>";
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
			$wempt = $wemp;
			$wemp = substr($wemp,0,2);
			$wcco2=strtolower ($wcco2);
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=4 bgcolor=#DDDDDD><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
			echo "<tr><td align=center colspan=4 bgcolor=#DDDDDD><b>APLICACION DE COSTOS</b></td></tr>";
			echo "<tr><td align=center colspan=4 bgcolor=#DDDDDD><b>INDICADOR DE CUBRIMIENTO DE COSTOS</b></td></tr>";
			echo "<tr><td align=center colspan=4 bgcolor=#DDDDDD><b>EMPRESA : ".$wempt."</b></td></tr>";
			echo "<tr><td colspan=4 align=center bgcolor=#DDDDDD>PERIODO FACTURACION : ".$wper1."-".$wper2." A&Ntilde;O : ".$wanop."</td></tr>";
			echo "<tr><td colspan=4 align=center bgcolor=#DDDDDD>MES DE COSTEO : ".$wperc."</td></tr>";
			echo "<tr><td colspan=4 align=center bgcolor=#DDDDDD>CC INICIAL  : ".$wcco1. " CC FINAL : ".$wcco2."</td></tr>";
			echo "<tr><td bgcolor=#cccccc><b>CENTRO DE <BR>COSTOS</b></td><td bgcolor=#cccccc><b>DESCRIPCION</b></td><td bgcolor=#cccccc><b>% BASE<br>CANTIDAD</b></td><td bgcolor=#cccccc><b>% BASE<br>INGRESOS</b></td></tr>";
			//                  0       1      2      3      4         5               6
			$query  = "select Fddcco,Cconom,Fddcod,Fddcon,Pcactp,sum(Fddcan),sum(Fddite + Fddipr) from ".$empresa."_000137,".$empresa."_000005,".$empresa."_000095,".$empresa."_000097,".$empresa."_000131 ";
			$query .= "  where fddano = ".$wanop; 
			$query .= "    and fddemp = '".$wemp."'"; 
			$query .= "    and fddmes between ".$wper1." and  ".$wper2;
			$query .= "    and fddcco between '".$wcco1."' and  '".$wcco2."' ";
			$query .= "    and fddcco = ccocod ";
			$query .= "    and fddemp = ccoemp ";
			$query .= "    and Ccocos = 'S' ";
			$query .= "    and fddcco = mprcco ";
			$query .= "    and fddemp = mpremp ";
			$query .= "    and fddcon = mprcon ";
			$query .= "    and fddcod = mprpro ";
			$query .= "    and mprtip in ('P','C','O') ";  
			$query .= "    and pcaano = ".$wanop;  
			$query .= "    and pcames = ".$wperc; 
			$query .= "    and mprcco = pcacco ";
			$query .= "    and mpremp = pcaemp ";
			$query .= "    and mprgru = Pcagru "; 
			$query .= "    and mprpro = pcacod "; 
			$query .= "    and mprcon = pcacon "; 
			$query .= "    and fddemp = cicemp ";
			$query .= "    and fddano = cicano "; 
			$query .= "    and fddmes = cicmes "; 
			$query .= "    and fddcco = ciccco "; 
			$query .= "    Group by  1,2,3,4,5 ";  
			$query .= " union all ";
			$query .= " select Fddcco,Cconom,Fddcod,Fddcon,-1,sum(Fddcan),sum(Fddite + Fddipr) from ".$empresa."_000137,".$empresa."_000005,".$empresa."_000095,".$empresa."_000131  ";
			$query .= "  where fddano = ".$wanop; 
			$query .= "    and fddemp = '".$wemp."'"; 
			$query .= "    and fddmes between ".$wper1." and  ".$wper2;
			$query .= "    and fddcco between '".$wcco1."' and  '".$wcco2."' ";
			$query .= "    and fddcco = ccocod  ";
			$query .= "    and fddemp = ccoemp ";
			$query .= "    and Ccocos = 'S' "; 
			$query .= "    and fddcco = mprcco  ";
			$query .= "    and fddemp = mpremp ";
			$query .= "    and fddcon = mprcon  ";
			$query .= "    and fddcod = mprpro  ";
			$query .= "    and mprtip in ('P','C','O') ";   
			$query .= "    and fddcod not in (select pcacod from ".$empresa."_000097 where pcaano = ".$wanop." and pcames = ".$wperc." and Pcacco = Mprcco and Pcagru = Mprgru and Pcacod = Mprpro and Pcacon = Mprcon and Pcaemp = '".$wemp."' ) "; 
			$query .= "    and fddemp = cicemp ";
			$query .= "    and fddano = cicano  "; 
			$query .= "    and fddmes = cicmes  "; 
			$query .= "    and fddcco = ciccco ";  
			$query .= "    Group by  1,2,3,4,5 ";
			$query .= "    Order by 1,3,4 "; 
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			$keys="";
			$k=0;
			$ktc=0;
			$kc=0;
			$stc=0;
			$st=0;
			$ktt=0;
			$kttm=0;
			$stt=0;
			$sttm=0;
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($keys != $row[0])
				{
					if($i > 0)
					{
						if($k % 2 == 0)
							$color = "#99CCFF";
						else
							$color = "#FFFFFF";
						$k++;
						$porxcan=$kc / $ktc * 100;
						$porxing=$st / $stc * 100;
						echo "<tr><td bgcolor=".$color."> ".$keys."</td><td bgcolor=".$color."> ".$ccn."</td><td bgcolor=".$color." align=right> ".number_format((double)$porxcan,2,'.',',')."</td><td bgcolor=".$color." align=right> ".number_format((double)$porxing,2,'.',',')."</td></tr>";
					}
					$ktc=0;
					$kc=0;
					$stc=0;
					$st=0;
					$keys=$row[0];
					$ccn=$row[1];
				}
				$ktc += $row[5];
				if($row[4] >= 0)
					$kc += $row[5];
				$stc += $row[6];
				if($row[4] >= 0)
					$st += $row[6];
				$ktt += $row[5];
				if($row[4] >= 0)
					$kttm += $row[5];
				$stt += $row[6];
				if($row[4] >= 0)
					$sttm += $row[6];
			}
			if($k % 2 == 0)
				$color = "#99CCFF";
			else
				$color = "#FFFFFF";
			if($num > 0)
			{
				$porxcan=$kc / $ktc * 100;
				$porxing=$st / $stc * 100;
				echo "<tr><td bgcolor=".$color."> ".$keys."</td><td bgcolor=".$color."> ".$ccn."</td><td bgcolor=".$color." align=right> ".number_format((double)$porxcan,2,'.',',')."</td><td bgcolor=".$color." align=right> ".number_format((double)$porxing,2,'.',',')."</td></tr>";
				$porxcan=$kttm / $ktt * 100;
				$porxing=$sttm / $stt * 100;
			}
			else
			{
				$porxcan=0;
				$porxing=0;	
			}
			$color = "#999999";
			echo "<tr><td bgcolor=".$color."> TOTAL</td><td bgcolor=".$color."> CENTROS DE COSTOS</td><td bgcolor=".$color." align=right> ".number_format((double)$porxcan,2,'.',',')."</td><td bgcolor=".$color." align=right> ".number_format((double)$porxing,2,'.',',')."</td></tr>";
			echo "</table>";
			$query  = "select Fddcco,Cconom,fddano,fddmes from ".$empresa."_000137,".$empresa."_000005,".$empresa."_000095,".$empresa."_000097 ";
			$query .= "    where fddano = ".$wanop; 
			$query .= "      and fddemp = '".$wemp."'"; 
			$query .= "      and fddmes between ".$wper1." and  ".$wper2;
			$query .= "      and fddcco between '".$wcco1."' and  '".$wcco2."' ";
			$query .= "      and fddcco = ccocod  ";
			$query .= "      and fddemp = ccoemp ";
			$query .= "      and Ccocos = 'S' "; 
			$query .= "      and fddemp = mpremp ";
			$query .= "      and fddcco = mprcco  ";
			$query .= "      and fddcon = mprcon  ";
			$query .= "      and fddcod = mprpro "; 
			$query .= "      and mprtip in ('P','C','O')  ";
			$query .= "  	 and pcaano = ".$wanop; 
			$query .= "      and pcames = ".$wper2; 
			$query .= "      and mpremp = pcaemp "; 
			$query .= "      and mprcco = pcacco  "; 
			$query .= "      and mprpro = pcacod ";
			$query .= "      and mprgru = Pcagru "; 
			$query .= "      and mprcon = pcacon "; 
			$query .= "      and fddcco NOT IN (select ciccco from ".$empresa."_000131 where cicano=fddano and cicmes=fddmes and cicemp = '".$wemp."') "; 
			$query .= "      Group by  1,2,3,4  "; 
			$query .= "      Order by 1,3,4  ";
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			echo "<BR><BR><center><table border=0>";
			echo "<tr><td align=center colspan=5 bgcolor=#DDDDDD><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
			echo "<tr><td align=center colspan=5 bgcolor=#DDDDDD><b>INFORME DE INCONSISTENCIAS</b></td></tr>";
			echo "<tr><td align=center colspan=5 bgcolor=#DDDDDD><b>EMPRESA : ".$wempt."</b></td></tr>";
			echo "<tr><td bgcolor=#cccccc><b>CENTRO DE <BR>COSTOS</b></td><td bgcolor=#cccccc><b>DESCRIPCION</b></td><td bgcolor=#cccccc><b>A�O</b></td><td bgcolor=#cccccc><b>MES</b></td><td bgcolor=#cccccc><b>OBSERVACION</b></td></tr>";
			for ($i=0;$i<$num;$i++)
			{
				if($i % 2 == 0)
					$color = "#99CCFF";
				else
					$color = "#FFFFFF";
				$row = mysql_fetch_array($err);
				echo "<tr><td bgcolor=".$color."> ".$row[0]."</td><td bgcolor=".$color."> ".$row[1]."</td><td bgcolor=".$color."> ".$row[2]."</td><td bgcolor=".$color."> ".$row[3]."</td><td bgcolor=".$color.">A�O MES CENTRO DE COSTOS SIN CIERRE</td></tr>";
			}
			$query  = "select Fddcco,Cconom from ".$empresa."_000137,".$empresa."_000005 ";
			$query .= "  where fddano = ".$wanop; 
			$query .= "    and fddemp = '".$wemp."'"; 
			$query .= "    and fddmes between ".$wper1." and  ".$wper2; 
			$query .= "    and fddcco between '".$wcco1."' and  '".$wcco2."' "; 
			$query .= "    and fddcco = ccocod ";
			$query .= "    and fddemp = ccoemp ";
			$query .= "    and Ccocos != 'S' ";
			$query .= "	Group by  1,2  ";
			$query .= " Order by 1 ";
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				if($i % 2 == 0)
					$color = "#99CCFF";
				else
					$color = "#FFFFFF";
				$row = mysql_fetch_array($err);
				echo "<tr><td bgcolor=".$color."> ".$row[0]."</td><td bgcolor=".$color."> ".$row[1]."</td><td bgcolor=".$color."></td><td bgcolor=".$color."></td><td bgcolor=".$color.">CENTRO DE COSTOS NO SE COSTEA EN TABLA 5</td></tr>";
			}
		}
}
?>
</body>
</html>
