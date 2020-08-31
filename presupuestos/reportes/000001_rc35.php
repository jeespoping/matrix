<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Informe Comparativo de Ingresos Mes x Mes</font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc35.php Ver. 2016-02-19</b></font></td></tr></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc35.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1)  or !isset($wper2)  or !isset($wres)  or (strtoupper ($wres) != "R" and strtoupper ($wres) != "D") or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>INFORME COMPARATIVO DE INGRESOS MES X MES</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Resumido o Detallado ? (R/D)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wres' size=1 maxlength=1></td></tr>";
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
			$d=array();
			$d[0]=31;
			$d[1]=28;
			$d[2]=31;
			$d[3]=30;
			$d[4]=31;
			$d[5]=30;
			$d[6]=31;
			$d[7]=31;
			$d[8]=30;
			$d[9]=31;
			$d[10]=30;
			$d[11]=31;
			$wres=strtoupper ($wres);
			$query = "SELECT cierre_ingresos,fecha from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and mes =   ".$wper2;
			$query = $query."    and Emp = '".$wemp."' ";
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			$fecha_cierre=$row[1];
			if($num > 0 and $row[0] == "on" and (integer)substr($row[1],8,2) >= $d[(integer)substr($row[1],5,2)-1])
			{
			$query = "select Miocco,Cconom,Ccouni,miomes,sum(Mioinp),sum(Mioint) from ".$empresa."_000063,".$empresa."_000005 ";
			$query = $query."  where mioano = ".$wanop;
			$query = $query."    and mioemp = '".$wemp."'";
			$query = $query."    and miomes between ".$wper1." and ".$wper2;
			$query = $query."    and miocco between '0' and 'z' ";
			$query = $query."    and miocco = ccocod   ";
			$query = $query."    and mioemp = ccoemp   ";
			$query = $query."   group by miocco,cconom,ccouni,miomes  ";
			$query = $query."   order by ccouni,miocco,miomes ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<table border=1>";
			echo "<tr><td colspan=14 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=14 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=14 align=center>INFORME COMPARATIVO DE INGRESOS MES X MES</td></tr>";
			echo "<tr><td colspan=14 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td colspan=14 align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. " A&Ntilde;O : ".$wanop."<B> ACTUALIZADO A : ".$fecha_cierre."</B></td></tr>";
			echo "<tr><td colspan=14 align=center>INFORMACION EN MILLONES DE PESOS</td></tr>";
			$wdatau=array();
			$wdatac=array();
			$wdatat=array();
			$wtotcli=array();
			$wmeses=array();
			for ($i=$wper1;$i<=$wper2;$i++)
			{
				switch ($i)
					{
						case 1:
							$wmese[$i]="ENERO";
							break;
						case 2:
							$wmese[$i]="FEBRERO";
							break;
						case 3:
							$wmese[$i]="MARZO";
							break;
						case 4:
							$wmese[$i]="ABRIL";
							break;
						case 5:
							$wmese[$i]="MAYO";
							break;
						case 6:
							$wmese[$i]="JUNIO";
							break;
						case 7:
							$wmese[$i]="JULIO";
							break;
						case 8:
							$wmese[$i]="AGOSTO";
							break;
						case 9:
							$wmese[$i]="SEPTIEMBRE";
							break;
						case 10:
							$wmese[$i]="OCTUBRE";
							break;
						case 11:
							$wmese[$i]="NOVIEMBRE";
							break;
						case 12:
							$wmese[$i]="DICIEMBRE";
							break;
					}
			}
			echo "<tr><td><b>UNIDAD</b></td>";
			for ($i=$wper1;$i<=$wper2;$i++)
				echo "<td align=right><b>".$wmese[$i]."</b></td>";
			echo "<td align=right><b>ACUMULADO</b></td></tr>";
			for ($i=0;$i<14;$i++)
			{
				$wdatau[$i]=0;
				$wdatac[$i]=0;
				$wdatat[$i]=0;
				$wtotcli[$i]=0;
			}
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if ($i == 0)
				{
					$wcco=$row[0];
					$wcconom=$row[1];
					$wunidad=$row[2];
					switch ($row[2])
					{
						case "1Q":
							echo "<tr><td bgcolor='#FFFFFF' colspan=14><b>UNIDADES QUIRURGICAS</B></td></tr>";
						break;
						case "2H":
							echo "<tr><td bgcolor='#FFFFFF' colspan=14><b>UNIDADES DE HOSPITALIZACION</B></td></tr>";
						break;
						case "3D":
							echo "<tr><td bgcolor='#FFFFFF' colspan=14><b>UNIDADES DE DIAGNOSTICO</B></td></tr>";
						break;
						case "4A":
							echo "<tr><td bgcolor='#FFFFFF' colspan=14><b>UNIDADES DE ATENCION AMBULATORIA</B></td></tr>";
						break;
						case "5O":
							echo "<tr><td bgcolor='#FFFFFF' colspan=14><b>OTRAS UNIDADES</B></td></tr>";
						break;
					}
				}
				if($row[0] != $wcco)
				{
					if($wres == "D")
					{
						echo"<tr><td>".$wcconom."</td>";
						for ($j=$wper1;$j<=$wper2;$j++)
						{
							$wdatac[$j]=$wdatac[$j]/1000000;
							echo "<td align=right>".number_format((double)$wdatac[$j],2,'.',',')."</td>";
						}
						echo "<td align=right>".number_format((double)$wdatac[13],2,'.',',')."</td></tr>";
					}
					for ($j=0;$j<14;$j++)
						$wdatac[$j]=0;
					$wcco=$row[0];
					$wcconom=$row[1];
				}
				if ($row[2] != $wunidad)
				{
					switch ($wunidad)
					{
							case "1Q":
								echo"<tr><td bgcolor='#cccccc'><b>TOTAL UNIDADES QUIRURGICAS</b></td>";
								for ($j=$wper1;$j<=$wper2;$j++)
								{
									$wdatau[$j]=$wdatau[$j]/1000000;
									echo "<td align=right bgcolor='#cccccc'><b>".number_format((double)$wdatau[$j],2,'.',',')."</b></td>";
								}
								echo "<td align=right bgcolor='#cccccc'><b>".number_format((double)$wdatau[13],2,'.',',')."</b></td></tr>";
							break;
							case "2H":
								echo"<tr><td bgcolor='#cccccc'><b>TOTAL UNIDADES DE HOSPITALIZACION</b></td>";
								for ($j=$wper1;$j<=$wper2;$j++)
								{
									$wdatau[$j]=$wdatau[$j]/1000000;
									echo "<td align=right bgcolor='#cccccc'><b>".number_format((double)$wdatau[$j],2,'.',',')."</b></td>";
								}
								echo "<td align=right bgcolor='#cccccc'><b>".number_format((double)$wdatau[13],2,'.',',')."</b></td></tr>";
							break;
							case "3D":
								echo"<tr><td bgcolor='#cccccc'><b>TOTAL UNIDADES DE DIAGNOSTICO</b></td>";
								for ($j=$wper1;$j<=$wper2;$j++)
								{
									$wdatau[$j]=$wdatau[$j]/1000000;
									echo "<td align=right bgcolor='#cccccc'><b>".number_format((double)$wdatau[$j],2,'.',',')."</b></td>";
								}
								echo "<td align=right bgcolor='#cccccc'><b>".number_format((double)$wdatau[13],2,'.',',')."</b></td></tr>";
							break;
							case "4A":
								echo"<tr><td bgcolor='#cccccc'><b>TOTAL UNIDADES DE ATENCION AMBULATORIA</b></td>";
								for ($j=$wper1;$j<=$wper2;$j++)
								{
									$wdatau[$j]=$wdatau[$j]/1000000;
									echo "<td align=right bgcolor='#cccccc'><b>".number_format((double)$wdatau[$j],2,'.',',')."</b></td>";
								}
								echo "<td align=right bgcolor='#cccccc'><b>".number_format((double)$wdatau[13],2,'.',',')."</b></td></tr>";
							break;
							case "7E":
								echo"<tr><td bgcolor='#cccccc'><b>TOTAL UNIDADES EXTERNAS</b></td>";
								for ($j=$wper1;$j<=$wper2;$j++)
								{
									$wdatau[$j]=$wdatau[$j]/1000000;
									echo "<td align=right bgcolor='#cccccc'><b>".number_format((double)$wdatau[$j],2,'.',',')."</b></td>";
								}
								echo "<td align=right bgcolor='#cccccc'><b>".number_format((double)$wdatau[13],2,'.',',')."</b></td></tr>";
							break;
							default:
								if($wunidad != "5O")
								{
									echo"<tr><td  bgcolor='#cccccc'><b>TOTAL OTRAS UNIDADES</b></td>";
									for ($j=$wper1;$j<=$wper2;$j++)
									{
										$wdatau[$j]=$wdatau[$j]/1000000;
										echo "<td align=right bgcolor='#cccccc'><b>".number_format((double)$wdatau[$j],2,'.',',')."</b></td>";
									}
									echo "<td align=right bgcolor='#cccccc'><b>".number_format((double)$wdatau[13],2,'.',',')."</b></td></tr>";
									echo"<tr><td bgcolor='#99CCFF'><b>TOTAL CLINICA</b></td>";
									for ($j=$wper1;$j<=$wper2;$j++)
									{
										$wparc=$wtotcli[$j]/1000000;
										echo "<td align=right bgcolor='#99CCFF'><b>".number_format((double)$wparc,2,'.',',')."</b></td>";
									}
									echo "<td align=right bgcolor='#99CCFF'><b>".number_format((double)$wtotcli[13],2,'.',',')."</b></td></tr>";
								}
					}
					switch ($row[2])
					{
						case "1Q":
							echo "<tr><td bgcolor='#FFFFFF' colspan=14><b>UNIDADES QUIRURGICAS</B></td></tr>";
						break;
						case "2H":
							echo "<tr><td bgcolor='#FFFFFF' colspan=14><b>UNIDADES DE HOSPITALIZACION</B></td></tr>";
						break;
						case "3D":
							echo "<tr><td bgcolor='#FFFFFF' colspan=14><b>UNIDADES DE DIAGNOSTICO</B></td></tr>";
						break;
						case "4A":
							echo "<tr><td bgcolor='#FFFFFF' colspan=14><b>UNIDADES DE ATENCION AMBULATORIA</B></td></tr>";
						break;
						case "5O":
							echo "<tr><td bgcolor='#FFFFFF' colspan=14><b>OTRAS UNIDADES</B></td></tr>";
						break;
						default:
							if($wunidad != "5O")
								echo "<tr><td bgcolor='#FFFFFF' colspan=14><b>UNIDADES EXTERNAS</B></td></tr>";
						break;
					}
					if($wunidad != "5O")
					{
						for ($j=0;$j<14;$j++)
						{
							$wdatau[$j]=0;
							$wdatac[$j]=0;
						}
					}
					$wcco=$row[0];
					$wunidad=$row[2];
				}
				$wdatau[$row[3]]=$wdatau[$row[3]]+$row[4];
				$wdatau[13]=$wdatau[13]+($row[4] / 1000000);
				$wdatac[$row[3]]=$wdatac[$row[3]]+$row[4];
				$wdatac[13]=$wdatac[13]+($row[4] / 1000000);
				$wtotcli[$row[3]]=$wtotcli[$row[3]]+$row[4];
				$wtotcli[13]=$wtotcli[13]+($row[4] / 1000000);
				$wdatat[$row[3]]=$wdatat[$row[3]]+$row[5];
				$wdatat[13]=$wdatat[13]+($row[5] / 1000000);
			}
			if($wres == "D")
			{
				echo"<tr><td>".$wcconom."</td>";
				for ($j=$wper1;$j<=$wper2;$j++)
				{
					$wdatac[$j]=$wdatac[$j]/1000000;
					echo "<td align=right>".number_format((double)$wdatac[$j],2,'.',',')."</td>";
				}
				echo "<td align=right>".number_format((double)$wdatac[13],2,'.',',')."</td></tr>";
			}
			echo"<tr><td bgcolor='#cccccc'><b>TOTAL UNIDADES EXTERNAS</b></td>";
			for ($j=$wper1;$j<=$wper2;$j++)
			{
				$wdatau[$j]=$wdatau[$j]/1000000;
				echo "<td align=right bgcolor='#cccccc'><b>".number_format((double)$wdatau[$j],2,'.',',')."</b></td>";
			}
			echo "<td align=right bgcolor='#cccccc'><b>".number_format((double)$wdatau[13],2,'.',',')."</b></td></tr>";
			echo"<tr><td  bgcolor='#FFCC99'><b>TOTAL INGRESOS PROPIOS</b></td>";
			for ($j=$wper1;$j<=$wper2;$j++)
			{
				$wtotcli[$j]=$wtotcli[$j]/1000000;
				echo "<td align=right bgcolor='#FFCC99'><b>".number_format((double)$wtotcli[$j],2,'.',',')."</b></td>";
			}
			echo "<td align=right bgcolor='#FFCC99'><b>".number_format((double)$wtotcli[13],2,'.',',')."</b></td></tr>";
			echo"<tr><td  bgcolor='#CCFFFF'><b>TOTAL INGRESOS PARA TERCEROS</b></td>";
			for ($j=$wper1;$j<=$wper2;$j++)
			{
				$wdatat[$j]=$wdatat[$j]/1000000;
				echo "<td align=right  bgcolor='#CCFFFF'><b>".number_format((double)$wdatat[$j],2,'.',',')."</b></td>";
			}
			echo "<td align=right  bgcolor='#CCFFFF'><b>".number_format((double)$wdatat[13],2,'.',',')."</b></td></tr>";
			echo"<tr><td  bgcolor='#FFCCFF'><b>TOTAL INGRESOS PMLA</b></td>";
			for ($j=$wper1;$j<=$wper2;$j++)
			{
				$wtotcli[$j]=$wtotcli[$j]+$wdatat[$j];
				echo "<td align=right  bgcolor='#FFCCFF'><b>".number_format((double)$wtotcli[$j],2,'.',',')."</b></td>";
			}
			$wtotcli[13]=$wtotcli[13]+$wdatat[13];
			echo "<td align=right  bgcolor='#FFCCFF'><b>".number_format((double)$wtotcli[13],2,'.',',')."</b></td></tr>";
		}
		else
		{
			echo "<center><table border=0 aling=center>";
			echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
			echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>EL PERIODO NO!! ESTA CERRADO -- LLAME A COSTOS Y PRSUPUESTOS</MARQUEE></FONT>";
			echo "<br><br>";			
		}
		}
	}
?>
</body>
</html>
