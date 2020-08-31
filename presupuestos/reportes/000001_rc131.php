<html>
<head>
  <title>MATRIX</title>
  <link type='text/css' href='../../../include/root/jquery.tooltip.css' rel='stylesheet'>		    
  <script type='text/javascript' src='../../../include/root/jquery-1.3.2.min.js'></script>
  <script type='text/javascript' src='../../../include/root/ui.core.min.js'></script>
  <script type='text/javascript' src='../../../include/root/jquery.tooltip.js'></script>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
	<script type="text/javascript">
<!--
	function ejecutar(path)
	{
		window.open(path,'','width=1024,height=500,status=0,menubar=0,scrollbars=1,toolbar=0,directories=0,resizable=0');
	}
	function tooltipAlertas(pos)
	{
		$('#ALERT[pos] *').tooltip();
	}

//-->
</script>
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Informe Comparativo de Contibucion De Las Unidades Entre A&ntilde;os</font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc131.php Ver. 2017-01-06</b></font></td></tr></table>
</center>

<?php
include_once("conex.php");
function comparacion($vec1,$vec2)
{
	if($vec1[7] > $vec2[7])
		return 1;
	elseif ($vec1[7] < $vec2[7])
				return -1;
			else
				return 0;
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc131.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wccoi) or !isset($wemp) or $wemp == "Seleccione" or !isset($wccof) or !isset($wanoi) or !isset($wperi1)  or !isset($wperi2) or $wperi1 < 1 or $wperi1 > 12 or $wperi2 < 1 or $wperi2 > 12 or $wperi1 > $wperi2 or !isset($wanof) or !isset($wperf1)  or !isset($wperf2) or $wperf1 < 1 or $wperf1 > 12 or $wperf2 < 1 or $wperf2 > 12 or $wperf1 > $wperf2 or !isset($wserv) or (strtoupper ($wserv) != "S" and strtoupper ($wserv) != "N"))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>INFORME COMPARATIVO DE CONTIBUCION DE LAS UNIDADES ENTRE A&Ntilde;OS</td></tr>";
			echo "<tr><td bgcolor=#999999 align=center colspan=2>CENTROS DE COSTOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Centro de Costos Inicial</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wccoi' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Centro de Costos Final</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wccof' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#999999 align=center colspan=2>DATOS INICIALES</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Ano de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanoi' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wperi1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wperi2' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#999999 align=center colspan=2>DATOS FINALES</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Ano de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanof' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wperf1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wperf2' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#999999 align=center colspan=2>COSTOS DE APOYO</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Costos de Apoyo ? (S/N)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wserv' size=1 maxlength=1></td></tr>";
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
			$num1 = -1;
			//                       0                   1       2       3       4          5
			$query  = "select mid(meccpr,1,1) as tip, Meccco, Cconom, Ccouni, Ccoclas, SUM(Mecval) from ".$empresa."_000026,".$empresa."_000005 ";
			$query .= "	where mecano = ".$wanof;
			$query .= "   and mecemp = '".$wemp."'";
			$query .= "	  and mecmes between ".$wperf1." and ".$wperf2;
			$query .= "	  and mid(meccpr,1,1) in ('1','2','3','5','8') ";
			if(strtoupper($wserv) == "N")
				$query .= "	  and meccpr not in ('227','298','299','598','398','399') ";
			$query .= "	  and meccco between '".$wccoi."' and '".$wccof."'";
			$query .= "	  and meccco = ccocod   ";
			$query .= "   and mecemp = ccoemp ";
			$query .= " group by tip,meccco,cconom,ccouni,Ccoclas  ";
			$query .= " order by ccouni, meccco, tip ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$row1=array();
				$wccoant="";
				$num1 = 0;
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					if($row[1] != $wccoant)
					{
						if($i > 0)
							$num1 = $num1 + 1;
						$wccoant=$row[1];
						$row1[$num1][0]=$row[1];
						$row1[$num1][1]=$row[2];
						$row1[$num1][2]=$row[3];
						if(strlen($row[3]) < 3)
							$row1[$num1][7]=$row[3]." ";
						else
							$row1[$num1][7]=$row[3];
						$row1[$num1][3]=0;
					}
					if($row[0] == "1")
						$row1[$num1][3] += $row[5];
					else
						$row1[$num1][3] -= $row[5];
				}
			}
			$num2 = -1;
			//                       0                   1       2       3       4          5
			$query  = "select mid(meccpr,1,1) as tip, Meccco, Cconom, Ccouni, Ccoclas, SUM(Mecval) from ".$empresa."_000026,".$empresa."_000005 ";
			$query .= "	where mecano = ".$wanoi;
			$query .= "   and mecemp = '".$wemp."'";
			$query .= "	  and mecmes between ".$wperi1." and ".$wperi2;
			$query .= "	  and mid(meccpr,1,1) in ('1','2','3','5','8') ";
			if(strtoupper($wserv) == "N")
				$query .= "	  and meccpr not in ('227','298','299','598','398','399') ";
			$query .= "	  and meccco between '".$wccoi."' and '".$wccof."'";
			$query .= "	  and meccco = ccocod   ";
			$query .= "   and mecemp = ccoemp ";
			$query .= " group by tip,meccco,cconom,ccouni,Ccoclas  ";
			$query .= " order by ccouni, meccco, tip ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$row2=array();
				$wccoant="";
				$num2 = 0;
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					if($row[1] != $wccoant)
					{
						if($i > 0)
							$num2 = $num2 + 1;
						$wccoant=$row[1];
						$row2[$num2][0]=$row[1];
						$row2[$num2][1]=$row[2];
						$row2[$num2][2]=$row[3];
						if(strlen($row[3]) < 3)
							$row2[$num2][7]=$row[3]." ";
						else
							$row2[$num2][7]=$row[3];
						$row2[$num2][3]=0;
					}
					if($row[0] == "1")
						$row2[$num2][3] += $row[5];
					else
						$row2[$num2][3] -= $row[5];
				}
			}
			echo "<table border=1>";
			echo "<tr><td colspan=7 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=7 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=7 align=center>INFORME COMPARATIVO DE CONTIBUCION DE LAS UNIDADES ENTRE A&Ntilde;OS</td></tr>";
			echo "<tr><td colspan=7 align=center>PERIODO INICIAL : ".$wanoi."/".$wperi1."-".$wperi2." PERIODO FINAL : ".$wanof."/".$wperf1."-".$wperf2."</td></tr>";
			echo "<tr><td colspan=7 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td><b>CENTRO<BR>DE COSTOS</BR></b></td><td><b>UNIDAD</b></td><td><b>A&Ntilde;O : ".$wanoi."/".$wperi1."-".$wperi2."</b></td><td><b>A&Ntilde;O : ".$wanof."/".$wperf1."-".$wperf2."</b></td><td align=right><b>DIFERENCIA</b></td><td align=right><b>% VARIACION</b></td><td align=right><b>NOTAS</b></td></tr>";
			$wdata=array();
			$k1=0;
			$k2=0;
			$num=-1;
			if ($num1 <  0)
			{
				$k1=1;
				$row1[$k1][0]='9999';
				$row1[$k1][1]=" ";
				$row1[$k1][2]="";
				$row1[$k1][3]=0;
				$kla1="ZZ9999";
			}
			else
			{
				$kla1=substr($row1[$k1][2],0,3).$row1[$k1][0];
				//$k1++;
			}
			if ($num2 <  0)
			{
				$k2=1;
				$row2[$k2][0]='9999';
				$row2[$k2][1]=" ";
				$row2[$k2][2]="";
				$row2[$k2][3]=0;
				$kla2="ZZ9999";
			}
			else
			{
				$kla2=substr($row2[$k2][2],0,3).$row2[$k2][0];
				//$k2++;
			}
			while ($k1 <= $num1 or $k2 <= $num2)
			{
				if($kla1 == $kla2)
				{
					$num = $num + 1;
					$wdata[$num][0]=$row1[$k1][0];
					$wdata[$num][1]=$row1[$k1][1];
					if($row1[$k1][2] == "6OGI")
						$row1[$k1][7] = "5O";
					$wdata[$num][2]=$row1[$k1][2];
					$wdata[$num][3]=$row1[$k1][3];
					$wdata[$num][4]=$row2[$k2][3];
					$wdata[$num][5]=$row2[$k2][3]-$row1[$k1][3];
					$wdata[$num][7]=substr($row1[$k1][7],0,3).$wdata[$num][0];
					if($row1[$k1][3] != 0)
						$wdata[$num][6]=($row2[$k2][3]/$row1[$k1][3])*100;
					else
						$wdata[$num][6]=0;
					$k1++;
					$k2++;
					if($k1 > $num1)
					{
						//$row1[0]="9999";
						$kla1="ZZ9999";
					}
					else
					{
						$kla1=substr($row1[$k1][2],0,3).$row1[$k1][0];
					}
					if($k2 > $num2)
					{
						//$row2[0]="9999";
						$kla2="ZZ9999";
					}
					else
					{
						$kla2=substr($row2[$k2][2],0,3).$row2[$k2][0];
					}
				}
				else if($kla1 < $kla2)
				{
					$num = $num + 1;
					$wdata[$num][0]=$row1[$k1][0];
					$wdata[$num][1]=$row1[$k1][1];
					if($row1[$k1][2] == "6OGI")
						$row1[$k1][7] = "5O";
					$wdata[$num][2]=$row1[$k1][2];
					$wdata[$num][3]=$row1[$k1][3];
					$wdata[$num][4]=0;
					$wdata[$num][5]=0-$row1[$k1][3];
					$wdata[$num][6]=0;
					$wdata[$num][7]=substr($row1[$k1][7],0,3).$wdata[$num][0];
					$k1++;
					if($k1 > $num1)
					{
						//$row1[0]="9999";
						$kla1="ZZ9999";
					}
					else
					{
						$kla1=substr($row1[$k1][2],0,3).$row1[$k1][0];
					}
				}
				else
				{
					$num = $num + 1;
					$wdata[$num][0]=$row2[$k2][0];
					$wdata[$num][1]=$row2[$k2][1];
					if($row2[$k2][2] == "6OGI")
						$row2[$k2][7] = "5O";
					$wdata[$num][2]=$row2[$k2][2];
					$wdata[$num][3]=0;
					$wdata[$num][4]=$row2[$k2][3];
					$wdata[$num][5]=$row2[$k2][3];
					$wdata[$num][6]=0;
					$wdata[$num][7]=substr($row2[$k2][7],0,3).$wdata[$num][0];
					$k2++;
					if($k2 > $num2)
					{
						//$row2[0]="9999";
						$kla2="ZZ9999";
					}
					else
					{
						$kla2=substr($row2[$k2][2],0,3).$row2[$k2][0];
					}
				}
			}
			usort($wdata,'comparacion');
			$wtotal1=array();
			$wtotal2=array();
			$wtott=array();
			$ita=0;
			$unidad="";
			$wtotal1[1]=0;
			$wtotal1[2]=0;
			$wtotal2[1]=0;
			$wtotal2[2]=0;
			$wtott[1]=0;
			$wtott[2]=0;
			$ALERTAS=array();
			for ($i=0;$i<=$num;$i++)
			{
				$query = "SELECT Aeffec, Aefdet from ".$empresa."_000078 ";
				$query .= " where Aefcco = '".$wdata[$i][0]."' ";
				$query .= "   and Aefano = ".$wanof;
				$query .= "   and Aefemp = '".$wemp."'";
				
				$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$numA = mysql_num_rows($err1);
				if($numA > 0)
				{
					$row1 = mysql_fetch_array($err1);
					$ALERTAS[$wdata[$i][0]]=$row1[0]." ".$row1[1];
				}
				else
					$ALERTAS[$wdata[$i][0]]="SIN EXPLICACION";
			}
			$wswtitulo=" ";
			$wgrupo="Todos";
			for ($i=0;$i<=$num;$i++)
			{
				if (substr($wdata[$i][7],0,3) != $unidad)
				{
					if($unidad != "")
					{
						switch ($unidad)
						{
							case "1Q ":
								$wdif=$wtotal1[1]-$wtotal1[2];
								if($wtotal1[2] != 0)
									$wpor=($wtotal1[1]-$wtotal1[2])/$wtotal1[2] *100;
								else
									$wpor=0;
								$path1="/matrix/presupuestos/reportes/000001_rc07.php?wanop=".$wanoi."&wper1=".$wperi1."&wper2=".$wperi2."&wcco1=1000&wcco2=9999&wserv=".$wserv."&call=SIG&wres=D&wgru=1Q&empresa=".$empresa."&wemp=".$wempt;
								$path2="/matrix/presupuestos/reportes/000001_rc07.php?wanop=".$wanof."&wper1=".$wperf1."&wper2=".$wperf2."&wcco1=1000&wcco2=9999&wserv=".$wserv."&call=SIG&wres=D&wgru=1Q&empresa=".$empresa."&wemp=".$wempt;
								echo"<tr><td bgcolor='#cccccc' colspan=2><b>TOTAL UNIDADES QUIRURGICAS</b></td><td bgcolor='#cccccc' onclick='ejecutar(".chr(34).$path1.chr(34).")' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td bgcolor='#cccccc' onclick='ejecutar(".chr(34).$path2.chr(34).")' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td bgcolor='#cccccc' align=right colspan=2><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";;
							break;
							case "2H ":
								$wdif=$wtotal1[1]-$wtotal1[2];
								if($wtotal1[2] != 0)
									$wpor=($wtotal1[1]-$wtotal1[2])/$wtotal1[2] *100;
								else
									$wpor=0;
								$path1="/matrix/presupuestos/reportes/000001_rc07.php?wanop=".$wanoi."&wper1=".$wperi1."&wper2=".$wperi2."&wcco1=1000&wcco2=9999&wserv=".$wserv."&call=SIG&wres=D&wgru=2H&empresa=".$empresa."&wemp=".$wempt;
								$path2="/matrix/presupuestos/reportes/000001_rc07.php?wanop=".$wanof."&wper1=".$wperf1."&wper2=".$wperf2."&wcco1=1000&wcco2=9999&wserv=".$wserv."&call=SIG&wres=D&wgru=2H&empresa=".$empresa."&wemp=".$wempt;
								echo"<tr><td bgcolor='#cccccc' colspan=2><b>TOTAL UNIDADES HOSPITALARIAS</b></td><td bgcolor='#cccccc' onclick='ejecutar(".chr(34).$path1.chr(34).")' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td bgcolor='#cccccc' onclick='ejecutar(".chr(34).$path2.chr(34).")' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td bgcolor='#cccccc' align=right colspan=2><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
							break;
							case "2SF":
								$wdif=$wtotal1[1]-$wtotal1[2];
								if($wtotal1[2] != 0)
									$wpor=($wtotal1[1]-$wtotal1[2])/$wtotal1[2] *100;
								else
									$wpor=0;
								$path1="/matrix/presupuestos/reportes/000001_rc07.php?wanop=".$wanoi."&wper1=".$wperi1."&wper2=".$wperi2."&wcco1=1000&wcco2=9999&wserv=".$wserv."&call=SIG&wres=D&wgru=2SF&empresa=".$empresa."&wemp=".$wempt;
								$path2="/matrix/presupuestos/reportes/000001_rc07.php?wanop=".$wanof."&wper1=".$wperf1."&wper2=".$wperf2."&wcco1=1000&wcco2=9999&wserv=".$wserv."&call=SIG&wres=D&wgru=2SF&empresa=".$empresa."&wemp=".$wempt;
								echo"<tr><td bgcolor='#cccccc' colspan=2><b>TOTAL SERVICIO FARMACEUTICO</b></td><td bgcolor='#cccccc' onclick='ejecutar(".chr(34).$path1.chr(34).")' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td bgcolor='#cccccc' onclick='ejecutar(".chr(34).$path2.chr(34).")' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td bgcolor='#cccccc' align=right colspan=2><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
							break;
							case "3D ":
								$wdif=$wtotal1[1]-$wtotal1[2];
								if($wtotal1[2] != 0)
									$wpor=($wtotal1[1]-$wtotal1[2])/$wtotal1[2] *100;
								else
									$wpor=0;
								$path1="/matrix/presupuestos/reportes/000001_rc07.php?wanop=".$wanoi."&wper1=".$wperi1."&wper2=".$wperi2."&wcco1=1000&wcco2=9999&wserv=".$wserv."&call=SIG&wres=D&wgru=3D&empresa=".$empresa."&wemp=".$wempt;
								$path2="/matrix/presupuestos/reportes/000001_rc07.php?wanop=".$wanof."&wper1=".$wperf1."&wper2=".$wperf2."&wcco1=1000&wcco2=9999&wserv=".$wserv."&call=SIG&wres=D&wgru=3D&empresa=".$empresa."&wemp=".$wempt;
								echo"<tr><td bgcolor='#cccccc' colspan=2><b>TOTAL UNIDADES DE DIAGNOSTICO</b></td><td bgcolor='#cccccc' onclick='ejecutar(".chr(34).$path1.chr(34).")' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td bgcolor='#cccccc' onclick='ejecutar(".chr(34).$path2.chr(34).")' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td bgcolor='#cccccc' align=right colspan=2><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
							break;
							case "4A ":
								$wdif=$wtotal1[1]-$wtotal1[2];
								if($wtotal1[2] != 0)
									$wpor=($wtotal1[1]-$wtotal1[2])/$wtotal1[2] *100;
								else
									$wpor=0;
								$path1="/matrix/presupuestos/reportes/000001_rc07.php?wanop=".$wanoi."&wper1=".$wperi1."&wper2=".$wperi2."&wcco1=1000&wcco2=9999&wserv=".$wserv."&call=SIG&wres=D&wgru=4A&empresa=".$empresa."&wemp=".$wempt;
								$path2="/matrix/presupuestos/reportes/000001_rc07.php?wanop=".$wanof."&wper1=".$wperf1."&wper2=".$wperf2."&wcco1=1000&wcco2=9999&wserv=".$wserv."&call=SIG&wres=D&wgru=4A&empresa=".$empresa."&wemp=".$wempt;
								echo"<tr><td bgcolor='#cccccc' colspan=2><b>TOTAL UNIDAD DE URGENCIAS/EMERGENCIAS Y CONSULTA EXTERNA</b></td><td bgcolor='#cccccc' onclick='ejecutar(".chr(34).$path1.chr(34).")' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td bgcolor='#cccccc' onclick='ejecutar(".chr(34).$path2.chr(34).")' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td bgcolor='#cccccc' align=right colspan=2><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
							break;
							case "7E ":
								$wdif=$wtotal1[1]-$wtotal1[2];
								if($wtotal1[2] != 0)
									$wpor=($wtotal1[1]-$wtotal1[2])/$wtotal1[2] *100;
								else
									$wpor=0;
								$path1="/matrix/presupuestos/reportes/000001_rc07.php?wanop=".$wanoi."&wper1=".$wperi1."&wper2=".$wperi2."&wcco1=1000&wcco2=9999&wserv=".$wserv."&call=SIG&wres=D&wgru=7E&empresa=".$empresa."&wemp=".$wempt;
								$path2="/matrix/presupuestos/reportes/000001_rc07.php?wanop=".$wanof."&wper1=".$wperf1."&wper2=".$wperf2."&wcco1=1000&wcco2=9999&wserv=".$wserv."&call=SIG&wres=D&wgru=7E&empresa=".$empresa."&wemp=".$wempt;
								echo"<tr><td bgcolor='#cccccc' colspan=2><b>TOTAL FARMACIA COMERCIAL</b></td><td bgcolor='#cccccc' onclick='ejecutar(".chr(34).$path1.chr(34).")' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td bgcolor='#cccccc' onclick='ejecutar(".chr(34).$path2.chr(34).")' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td bgcolor='#cccccc' align=right colspan=2><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
							break;
							case "8AC":
								$wdif=$wtotal1[1]-$wtotal1[2];
								if($wtotal1[2] != 0)
									$wpor=($wtotal1[1]-$wtotal1[2])/$wtotal1[2] *100;
								else
									$wpor=0;
								$path1="/matrix/presupuestos/reportes/000001_rc07.php?wanop=".$wanoi."&wper1=".$wperi1."&wper2=".$wperi2."&wcco1=1000&wcco2=9999&wserv=".$wserv."&call=SIG&wres=D&wgru=8AC&empresa=".$empresa."&wemp=".$wempt;
								$path2="/matrix/presupuestos/reportes/000001_rc07.php?wanop=".$wanof."&wper1=".$wperf1."&wper2=".$wperf2."&wcco1=1000&wcco2=9999&wserv=".$wserv."&call=SIG&wres=D&wgru=8AC&empresa=".$empresa."&wemp=".$wempt;
								echo"<tr><td bgcolor='#cccccc' colspan=2><b>TOTAL UNIDADES DE APOYO CLINICA</b></td><td bgcolor='#cccccc' onclick='ejecutar(".chr(34).$path1.chr(34).")' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td bgcolor='#cccccc' onclick='ejecutar(".chr(34).$path2.chr(34).")' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td bgcolor='#cccccc' align=right colspan=2><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
							break;
							case "8AP":
								$wdif=$wtotal1[1]-$wtotal1[2];
								if($wtotal1[2] != 0)
									$wpor=($wtotal1[1]-$wtotal1[2])/$wtotal1[2] *100;
								else
									$wpor=0;
								$path1="/matrix/presupuestos/reportes/000001_rc07.php?wanop=".$wanoi."&wper1=".$wperi1."&wper2=".$wperi2."&wcco1=1000&wcco2=9999&wserv=".$wserv."&call=SIG&wres=D&wgru=8AP&empresa=".$empresa."&wemp=".$wempt;
								$path2="/matrix/presupuestos/reportes/000001_rc07.php?wanop=".$wanof."&wper1=".$wperf1."&wper2=".$wperf2."&wcco1=1000&wcco2=9999&wserv=".$wserv."&call=SIG&wres=D&wgru=8AP&empresa=".$empresa."&wemp=".$wempt;
								echo"<tr><td bgcolor='#cccccc' colspan=2><b>TOTAL UNIDADES DE APOYO PROMOTORA</b></td><td bgcolor='#cccccc' onclick='ejecutar(".chr(34).$path1.chr(34).")' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td bgcolor='#cccccc' onclick='ejecutar(".chr(34).$path2.chr(34).")' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td bgcolor='#cccccc' align=right colspan=2><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
							break;
							case "9IN":
								$wdif=$wtotal1[1]-$wtotal1[2];
								if($wtotal1[2] != 0)
									$wpor=($wtotal1[1]-$wtotal1[2])/$wtotal1[2] *100;
								else
									$wpor=0;
								$path1="/matrix/presupuestos/reportes/000001_rc07.php?wanop=".$wanoi."&wper1=".$wperi1."&wper2=".$wperi2."&wcco1=1000&wcco2=9999&wserv=".$wserv."&call=SIG&wres=D&wgru=9IN&empresa=".$empresa."&wemp=".$wempt;
								$path2="/matrix/presupuestos/reportes/000001_rc07.php?wanop=".$wanof."&wper1=".$wperf1."&wper2=".$wperf2."&wcco1=1000&wcco2=9999&wserv=".$wserv."&call=SIG&wres=D&wgru=9IN&empresa=".$empresa."&wemp=".$wempt;
								echo"<tr><td bgcolor='#cccccc' colspan=2><b>TOTAL UNIDADES INDEPENDIENTES</b></td><td bgcolor='#cccccc' onclick='ejecutar(".chr(34).$path1.chr(34).")' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td bgcolor='#cccccc' onclick='ejecutar(".chr(34).$path2.chr(34).")' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td bgcolor='#cccccc' align=right colspan=2><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
							break;
							case "9OT":
								$wdif=$wtotal1[1]-$wtotal1[2];
								if($wtotal1[2] != 0)
									$wpor=($wtotal1[1]-$wtotal1[2])/$wtotal1[2] *100;
								else
									$wpor=0;
								$path1="/matrix/presupuestos/reportes/000001_rc07.php?wanop=".$wanoi."&wper1=".$wperi1."&wper2=".$wperi2."&wcco1=1000&wcco2=9999&wserv=".$wserv."&call=SIG&wres=D&wgru=9OT&empresa=".$empresa."&wemp=".$wempt;
								$path2="/matrix/presupuestos/reportes/000001_rc07.php?wanop=".$wanof."&wper1=".$wperf1."&wper2=".$wperf2."&wcco1=1000&wcco2=9999&wserv=".$wserv."&call=SIG&wres=D&wgru=9OT&empresa=".$empresa."&wemp=".$wempt;
								echo"<tr><td bgcolor='#cccccc' colspan=2><b>TOTAL OTRAS UNIDADES O SERVICIOS</b></td><td bgcolor='#cccccc' onclick='ejecutar(".chr(34).$path1.chr(34).")' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td bgcolor='#cccccc' onclick='ejecutar(".chr(34).$path2.chr(34).")' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td bgcolor='#cccccc' align=right colspan=2><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
							break;
							case "5O ":
								$wdif=$wtotal1[1]-$wtotal1[2];
								if($wtotal1[2] != 0)
								$wpor=($wtotal1[1]-$wtotal1[2])/$wtotal1[2] *100;
								else
									$wpor=0;
								$path1="/matrix/presupuestos/reportes/000001_rc07.php?wanop=".$wanoi."&wper1=".$wperi1."&wper2=".$wperi2."&wcco1=1000&wcco2=9999&wserv=".$wserv."&call=SIG&wres=D&wgru=5O&empresa=".$empresa."&wemp=".$wempt;
								$path2="/matrix/presupuestos/reportes/000001_rc07.php?wanop=".$wanof."&wper1=".$wperf1."&wper2=".$wperf2."&wcco1=1000&wcco2=9999&wserv=".$wserv."&call=SIG&wres=D&wgru=5O&empresa=".$empresa."&wemp=".$wempt;
								echo"<tr><td  bgcolor='#cccccc' colspan=2><b>TOTAL OTRAS UNIDADES</b></td><td   bgcolor='#cccccc' onclick='ejecutar(".chr(34).$path1.chr(34).")' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td bgcolor='#cccccc' onclick='ejecutar(".chr(34).$path2.chr(34).")' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td bgcolor='#cccccc' align=right colspan=2><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
								$wdif=$wtotal2[1]-$wtotal2[2];
								if($wtotal2[2] != 0)
									$wpor=($wtotal2[1]-$wtotal2[2])/$wtotal2[2] *100;
								else
									$wpor=0;
								echo"<tr><td bgcolor='#99CCFF' colspan=2><b>TOTAL CLINICA</b></td><td  bgcolor='#99CCFF' align=right><b>".number_format((double)$wtotal2[2],0,'.',',')."</b></td><td  bgcolor='#99CCFF' align=right><b>".number_format((double)$wtotal2[1],0,'.',',')."</b></td><td bgcolor='#99CCFF' align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td bgcolor='#99CCFF' align=right colspan=2><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
						}
					}
					switch (substr($wdata[$i][7],0,3))
					{
						case "1Q ":
							echo "<tr><td bgcolor='#FFFFFF' colspan=7><b>UNIDADES QUIRURGICAS</B></td></tr>";
							$wswtitulo="TOTAL UNIDADES QUIRURGICAS";
							$wgrupo="1Q";
						break;
						case "2H ":
							echo "<tr><td bgcolor='#FFFFFF' colspan=7><b>UNIDADES HOSPITALARIAS</B></td></tr>";
							$wswtitulo="TOTAL UNIDADES HOSPITALARIAS";
							$wgrupo="2H";
						break;
						case "2SF":
							echo "<tr><td bgcolor='#FFFFFF' colspan=7><b>SERVICIO FARMACEUTICO</B></td></tr>";
							$wswtitulo="TOTAL SERVICIO FARMACEUTICO";
							$wgrupo="2SF";
						break;
						case "3D ":
							echo "<tr><td bgcolor='#FFFFFF' colspan=7><b>UNIDADES DE DIAGNOSTICO</B></td></tr>";
							$wswtitulo="TOTAL UNIDADES DE DIAGNOSTICO";
							$wgrupo="3D";
						break;
						case "4A ":
							echo "<tr><td bgcolor='#FFFFFF' colspan=7><b>UNIDAD DE URGENCIAS/EMERGENCIAS Y CONSULTA EXTERNA</B></td></tr>";
							$wswtitulo="TOTAL UNIDAD DE URGENCIAS/EMERGENCIAS Y CONSULTA EXTERNA";
							$wgrupo="4A";
						break;
						case "5O ":
							echo "<tr><td bgcolor='#FFFFFF' colspan=7><b>OTRAS UNIDADES</B></td></tr>";
							$wswtitulo="TOTAL OTRAS UNIDADES";
							$wgrupo="5O";
						break;
						case "7E ":
							echo "<tr><td bgcolor='#FFFFFF' colspan=7><b>FARMACIA COMERCIAL</B></td></tr>";
							$wswtitulo="TOTAL FARMACIA COMERCIAL";
							$wgrupo="7E";
						break;
						case "8AC":
							echo "<tr><td bgcolor='#FFFFFF' colspan=7><b>UNIDADES DE APOYO CLINICA</B></td></tr>";
							$wswtitulo="TOTAL UNIDADES DE APOYO CLINICA";
							$wgrupo="8AC";
						break;
						case "8AP":
							echo "<tr><td bgcolor='#FFFFFF' colspan=7><b>UNIDADES DE APOYO PROMOTORA</B></td></tr>";
							$wswtitulo="TOTAL UNIDADES DE APOYO PROMOTORA";
							$wgrupo="8AP";
						break;
						case "9IN":
							echo "<tr><td bgcolor='#FFFFFF' colspan=7><b>UNIDADES INDEPENDIENTES</B></td></tr>";
							$wswtitulo="TOTAL UNIDADES INDEPENDIENTES";
							$wgrupo="9IN";
						break;
						case "9OT":
							echo "<tr><td bgcolor='#FFFFFF' colspan=7><b>OTRAS UNIDADES O SERVICIOS</B></td></tr>";
							$wswtitulo="TOTAL OTRAS UNIDADES O SERVICIOS";
							$wgrupo="9OT";
						break;
						case "9PR":
							echo "<tr><td bgcolor='#FFFFFF' colspan=7><b>PROYECTOS</B></td></tr>";
							$wswtitulo="TOTAL PROYECTOS";
							$wgrupo="9PR";
						break;
					}
					$wtotal1[1]=0;
					$wtotal1[2]=0;
					$unidad=substr($wdata[$i][7],0,3);
				}
				$wtotal1[1]=$wtotal1[1]+$wdata[$i][3];
				$wtotal1[2]=$wtotal1[2]+$wdata[$i][4];
				$wtotal2[1]=$wtotal2[1]+$wdata[$i][3];
				$wtotal2[2]=$wtotal2[2]+$wdata[$i][4];
				$wdif=$wdata[$i][3]-$wdata[$i][4];
				if($wdata[$i][4] != 0)
					$wpor=($wdata[$i][3]-$wdata[$i][4])/$wdata[$i][4]*100;
				else
					$wpor=0;
				if(isset($wdata[$i][4]) and isset($wdata[$i][3]))
				{
					$path1="/matrix/presupuestos/reportes/000001_rc07.php?wanop=".$wanoi."&wper1=".$wperi1."&wper2=".$wperi2."&wcco1=".$wdata[$i][0]."&wcco2=".$wdata[$i][0]."&wserv=".$wserv."&call=SIG&wres=D&wgru=Todos&empresa=".$empresa."&wemp=".$wempt;
					$path2="/matrix/presupuestos/reportes/000001_rc07.php?wanop=".$wanof."&wper1=".$wperf1."&wper2=".$wperf2."&wcco1=".$wdata[$i][0]."&wcco2=".$wdata[$i][0]."&wserv=".$wserv."&call=SIG&wres=D&wgru=Todos&empresa=".$empresa."&wemp=".$wempt;
					$path3="/matrix/presupuestos/procesos/Notas.php?wanop=".$wanof."&wcco=".$wdata[$i][0]."&ok=0&empresa=".$empresa;
					echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right onclick='ejecutar(".chr(34).$path1.chr(34).")'>".number_format((double)$wdata[$i][4],0,'.',',')."</td><td align=right onclick='ejecutar(".chr(34).$path2.chr(34).")'>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right>".number_format((double)$wdif,0,'.',',')."</td><td align=right>".number_format((double)$wpor,2,'.',',')."</td><td id='ALERT[".$wdata[$i][0]."]' title='".$ALERTAS[$wdata[$i][0]]."' onMouseMove='tooltipAlertas(".$wdata[$i][0].")' align=center onclick='ejecutar(".chr(34).$path3.chr(34).")'><IMG SRC='/matrix/images/medical/HCE/Titulo.gif' style='vertical-align:middle;'></IMG></td></tr>";
				}
			}
			if(isset($wtotal1[2]) and isset($wtotal1[1]) and isset($wswtitulo))
			{
				$wdif=$wtotal1[1]-$wtotal1[2];
				if($wtotal1[2] != 0)
					$wpor=($wtotal1[1]-$wtotal1[2])/$wtotal1[2] *100;
				else
					$wpor=0;
				$path1="/matrix/presupuestos/reportes/000001_rc07.php?wanop=".$wanoi."&wper1=".$wperi1."&wper2=".$wperi2."&wcco1=1000&wcco2=9999&wserv=".$wserv."&call=SIG&wres=D&wgru=".$wgrupo."&empresa=".$empresa."&wemp=".$wempt;
				$path2="/matrix/presupuestos/reportes/000001_rc07.php?wanop=".$wanof."&wper1=".$wperf1."&wper2=".$wperf2."&wcco1=1000&wcco2=9999&wserv=".$wserv."&call=SIG&wres=D&wgru=".$wgrupo."&empresa=".$empresa."&wemp=".$wempt;
				echo"<tr><td  bgcolor='#cccccc' colspan=2><b>".$wswtitulo."</b></td><td  bgcolor='#cccccc' onclick='ejecutar(".chr(34).$path1.chr(34).")' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td  bgcolor='#cccccc' onclick='ejecutar(".chr(34).$path2.chr(34).")' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td  bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td  bgcolor='#cccccc' align=right colspan=2><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
			}
			if(isset($wtotal2[2]) and isset($wtotal2[1]))
			{
				$wtotal2[1]=$wtotal2[1];
				$wtotal2[2]=$wtotal2[2];
				$wdif=$wtotal2[1]-$wtotal2[2];
				if($wtotal2[2] != 0)
					$wpor=($wtotal2[1]-$wtotal2[2])/$wtotal2[2] *100;
				else
					$wpor=0;
				echo"<tr><td  bgcolor='#FFCCFF' colspan=2><b>TOTAL PMLA</b></td><td  bgcolor='#FFCCFF' align=right><b>".number_format((double)$wtotal2[2],0,'.',',')."</b></td><td  bgcolor='#FFCCFF' align=right><b>".number_format((double)$wtotal2[1],0,'.',',')."</b></td><td  bgcolor='#FFCCFF' align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td  bgcolor='#FFCCFF' align=right colspan=2><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr></table>";
			}
		}
	}
?>
</body>
</html>
