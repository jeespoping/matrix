<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<script type="text/javascript">
<!--
	function ejecutar(path)
	{
		window.open(path,'','width=1024,height=500,status=0,menubar=0,scrollbars=1,toolbar=0,directories=0,resizable=0');
	}
	function enter()
	{
		document.forms.rc26.submit();
	}
//-->
</script>
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Informe Comparativo de Ingresos Entre A&ntilde;os</font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc26.php Ver. 2017-03-22</b></font></td></tr></table>
</center>

<?php
include_once("conex.php");
function comparacion($vec1,$vec2)
{
	if($vec1[9] > $vec2[9])
		return 1;
	elseif ($vec1[9] < $vec2[9])
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
		

		

		echo "<form name='rc26' action='000001_rc26.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp1) or $wemp1 == "Seleccione" or !isset($wper1)  or !isset($wper2)  or !isset($wres)  or !isset($wter) or (strtoupper ($wres) != "R" and strtoupper ($wres) != "D") or (strtoupper ($wter) != "S" and strtoupper ($wter) != "N")  or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>INFORME COMPARATIVO DE INGRESOS ENTRE A&Ntilde;OS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Resumido o Detallado ? (R/D)</td>";
			if(isset($wres))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wres' size=1 maxlength=1 value=".$wres."></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wres' size=1 maxlength=1></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Imprimir Solamente Terceros ? (S/N)</td>";
			if(isset($wter))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wter' size=1 maxlength=1 value=".$wter."></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wter' size=1 maxlength=1></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Empresa de  Proceso</td><td bgcolor=#cccccc align=center>";
			$query = "SELECT Empcod,Empdes  from ".$empresa."_000153 order by Empcod";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wemp1'>";
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
			$wempt = $wemp1;
			$wemp1 = substr($wemp1,0,2);
			echo "<input type='HIDDEN' name= 'wanop' value='".$wanop."'>";
			echo "<input type='HIDDEN' name= 'wper1' value='".$wper1."'>";
			echo "<input type='HIDDEN' name= 'wper2' value='".$wper2."'>";
			echo "<input type='HIDDEN' name= 'wres' value='".$wres."'>";
			echo "<input type='HIDDEN' name= 'wter' value='".$wter."'>";
			echo "<input type='HIDDEN' name= 'wemp1' value='".$wempt."'>";
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
			$wter=strtoupper ($wter);
			$query = "SELECT cierre_ingresos,fecha from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and mes =   ".$wper2;
			$query = $query."    and Emp = '".$wemp1."' ";
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			$fecha_cierre=$row[1];
			if($num > 0 and $row[0] == "on" and (integer)substr($row[1],8,2) >= $d[(integer)substr($row[1],5,2)-1])
			{
			$wanopa=$wanop-1;
			if($wter == "N")
				if(isset($wemp))
					$query = "select Miocco,Cconom,Ccouni,sum(Mioinp),sum(Mioint) from ".$empresa."_000063,".$empresa."_000005,".$empresa."_000061  ";
				else
					$query = "select Miocco,Cconom,Ccouni,sum(Mioinp),sum(Mioint) from ".$empresa."_000063,".$empresa."_000005 ";
			else
				$query = "select Miocco,Cconom,Ccouni,sum(Mioint),sum(Mioint) from ".$empresa."_000063,".$empresa."_000005 ";
			$query = $query."  where mioano = ".$wanop;
			$query = $query."    and mioemp = '".$wemp1."'";
			$query = $query."    and miomes between ".$wper1." and ".$wper2;
			if(isset($wemp))
			{
				$query = $query."      and miocco between '".$wcco1."' and '".$wcco2."'";
				$query = $query."	   and miocco = Ccocod ";
				$query = $query."      and mioemp = ccoemp   ";
				if($wgru != "Todos")
				{
					$query = $query."    and Ccouni = '".$wgru."' ";
				}
				$query = $query."      and mionit = epmcod   ";
				$query = $query."      and mioemp = empemp   ";
				$query = $query."      and Empcin = '".$wemp."' ";
			}
			else
			{
				$query = $query."      and miocco = ccocod   ";
				$query = $query."      and mioemp = ccoemp   ";
			}
			$query = $query."    group by miocco,cconom,ccouni  ";
			$query = $query."    order by ccouni,miocco ";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			if($wter == "N")
				if(isset($wemp))
					$query = "select Miocco,Cconom,Ccouni,sum(Mioinp),sum(Mioint) from ".$empresa."_000063,".$empresa."_000005,".$empresa."_000061  ";
				else
					$query = "select Miocco,Cconom,Ccouni,sum(Mioinp),sum(Mioint) from ".$empresa."_000063,".$empresa."_000005 ";
			else
				$query = "select Miocco,Cconom,Ccouni,sum(Mioint),sum(Mioint) from ".$empresa."_000063,".$empresa."_000005 ";
			$query = $query."  where mioano = ".$wanopa;
			$query = $query."    and mioemp = '".$wemp1."'";
			$query = $query."    and miomes between ".$wper1." and ".$wper2;
			if(isset($wemp))
			{
				$query = $query."      and miocco between '".$wcco1."' and '".$wcco2."'";
				$query = $query."	   and miocco = Ccocod ";
				$query = $query."      and mioemp = ccoemp   ";
				if($wgru != "Todos")
				{
					$query = $query."    and Ccouni = '".$wgru."' ";
				}
				$query = $query."      and mionit = epmcod   ";
				$query = $query."      and mioemp = empemp   ";
				$query = $query."      and Empcin = '".$wemp."' ";
			}
			else
			{
				$query = $query."      and miocco = ccocod   ";
				$query = $query."      and mioemp = ccoemp   ";
			}
			$query = $query."    group by miocco,cconom,ccouni  ";
			$query = $query."    order by ccouni,miocco ";
			$err2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($err2);
			echo "<table border=1>";
			echo "<tr><td bgcolor=#cccccc colspan=5 align=right><b>DETALLE X : </b>";
			if(!isset($tipo))
			{
				$tipo=0;
				echo "<input type='RADIO' name=tipo checked value=0 onclick='enter()'><b>Segmento</b>";
				echo "<input type='RADIO' name=tipo value=1 onclick='enter()'><b>Concepto</b>";
				echo "<input type='RADIO' name=tipo value=2 onclick='enter()'><b>Grupo</b>";
				echo "<input type='RADIO' name=tipo value=3 onclick='enter()'><b>Meses</b></td></tr>";
			}
			else
			{
				switch ($tipo)
				{
					case 0:
						echo "<input type='RADIO' name=tipo checked value=0 onclick='enter()'><b>Segmento</b>";
						echo "<input type='RADIO' name=tipo value=1 onclick='enter()'><b>Concepto</b>";
						echo "<input type='RADIO' name=tipo value=2 onclick='enter()'><b>Grupo</b>";
						echo "<input type='RADIO' name=tipo value=3 onclick='enter()'><b>Meses</b></td></tr>";
					break;
					case 1:
						echo "<input type='RADIO' name=tipo value=0 onclick='enter()'><b>Segmento</b>";
						echo "<input type='RADIO' name=tipo checked value=1 onclick='enter()'><b>Concepto</b>";
						echo "<input type='RADIO' name=tipo value=2 onclick='enter()'><b>Grupo</b>";
						echo "<input type='RADIO' name=tipo value=3 onclick='enter()'><b>Meses</b></td></tr>";
					break;
					case 2:
						echo "<input type='RADIO' name=tipo value=0 onclick='enter()'><b>Segmento</b>";
						echo "<input type='RADIO' name=tipo value=1 onclick='enter()'><b>Concepto</b>";
						echo "<input type='RADIO' name=tipo checked value=2 onclick='enter()'><b>Grupo</b>";
						echo "<input type='RADIO' name=tipo value=3 onclick='enter()'><b>Meses</b></td></tr>";
					break;
					case 3:
						echo "<input type='RADIO' name=tipo value=0 onclick='enter()'><b>Segmento</b>";
						echo "<input type='RADIO' name=tipo value=1 onclick='enter()'>$path<b>Concepto</b>";
						echo "<input type='RADIO' name=tipo value=2 onclick='enter()'><b>Grupo</b>";
						echo "<input type='RADIO' name=tipo checked value=3 onclick='enter()'><b>Meses</b></td></tr>";
					break;
				}
			}
			echo "<tr><td colspan=5 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=5 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=5 align=center>INFORME COMPARATIVO DE INGRESOS ENTRE A&Ntilde;OS</td></tr>";
			echo "<tr><td colspan=5 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td colspan=5 align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. "<B> ACTUALIZADO A : ".$fecha_cierre."</B></td></tr>";
			if(isset($wemp))
			{
				$query = "SELECT Empcin, Empdes  from ".$empresa."_000061  ";
				$query = $query."  where Empcin = '".$wemp."'";
				$query = $query."    and Empemp = '".$wemp1."'";
				$query = $query."  Group by 1 ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				$row = mysql_fetch_array($err);
				echo "<tr><td colspan=5 align=center>C.C. INICIAL : ".$wcco1." C.C. FINAL : ".$wcco2. "</td></tr>";
				echo "<tr><td colspan=5 align=center>EMPRESA : ".$wemp."-".$row[1]."</td></tr>";
				echo "<tr><td colspan=5 align=center>GRUPO DE UNIDADES : ".$wgru."</td></tr>";
			}
			echo "<tr><td><b>UNIDAD</b></td><td><b>A&Ntilde;O : ".$wanopa."</b></td><td><b>A&Ntilde;O : ".$wanop."</b></td><td align=right><b>DIFERENCIA</b></td><td align=right><b>% VARIACION</b></td></tr>";
			$wdata=array();
			$k1=0;
			$k2=0;
			$num=-1;
			if ($num1 ==  0)
			{
				$k1=1;
				$row1[0]='9999';
				$row1[1]=" ";
				$row1[2]="";
				$row1[3]=0;
				$row1[4]=0;
				$kla1="ZZ9999";
			}
			else
			{
				$row1 = mysql_fetch_array($err1);
				$kla1=substr($row1[2],0,2).$row1[0];
				$k1++;
			}
			if ($num2 ==  0)
			{
				$k2=1;
				$row2[0]='9999';
				$row2[1]=" ";
				$row2[2]="";
				$row2[3]=0;
				$row2[4]=0;
				$kla2="ZZ9999";
			}
			else
			{
				$row2 = mysql_fetch_array($err2);
				$kla2=substr($row2[2],0,2).$row2[0];
				$k2++;
			}
			while ($k1 <= $num1 or $k2 <= $num2)
			{
				if($kla1 == $kla2)
				{
					$num++;
					$wdata[$num][0]=$row1[0];
					$wdata[$num][1]=$row1[0]."-".$row1[1];
					if($row1[2] == "6OGI")
						$row1[2] = "5O";
					$wdata[$num][2]=$row1[2];
					$wdata[$num][3]=$row1[3];
					$wdata[$num][4]=$row2[3];
					$wdata[$num][5]=$row2[3]-$row1[3];
					$wdata[$num][7]=$row1[4];
					$wdata[$num][8]=$row2[4];
					$wdata[$num][9]=substr($wdata[$num][2],0,2).$wdata[$num][0];
					if($row1[3] != 0)
						$wdata[$num][6]=($row2[3]/$row1[3])*100;
					else
						$wdata[$num][6]=0;
					$k1++;
					$k2++;
					if($k1 > $num1)
					{
						$row1[0]="9999";
						$kla1="ZZ9999";
					}
					else
					{
						$row1 = mysql_fetch_array($err1);
						$kla1=substr($row1[2],0,2).$row1[0];
					}
					if($k2 > $num2)
					{
						$row2[0]="9999";
						$kla2="ZZ9999";
					}
					else
					{
						$row2 = mysql_fetch_array($err2);
						$kla2=substr($row2[2],0,2).$row2[0];
					}
				}
				else if($kla1 < $kla2)
				{
					$num++;
					$wdata[$num][0]=$row1[0];
					$wdata[$num][1]=$row1[0]."-".$row1[1];
					if($row1[2] == "6OGI")
						$row1[2] = "5O";
					$wdata[$num][2]=$row1[2];
					$wdata[$num][3]=$row1[3];
					$wdata[$num][4]=0;
					$wdata[$num][5]=0-$row1[3];
					$wdata[$num][6]=0;
					$wdata[$num][7]=$row1[4];
					$wdata[$num][8]=0;
					$wdata[$num][9]=substr($wdata[$num][2],0,2).$wdata[$num][0];
					$k1++;
					if($k1 > $num1)
					{
						$row1[0]="9999";
						$kla1="ZZ9999";
					}
					else
					{
						$row1 = mysql_fetch_array($err1);
						$kla1=substr($row1[2],0,2).$row1[0];
					}
				}
				else
				{
					$num++;
					$wdata[$num][0]=$row2[0];
					$wdata[$num][1]=$row2[0]."-".$row2[1];
					if($row2[2] == "6OGI")
						$row2[2] = "5O";
					$wdata[$num][2]=$row2[2];
					$wdata[$num][3]=0;
					$wdata[$num][4]=$row2[3];
					$wdata[$num][5]=$row2[3];
					$wdata[$num][6]=0;
					$wdata[$num][7]=0;
					$wdata[$num][8]=$row2[4];
					$wdata[$num][9]=substr($wdata[$num][2],0,2).$wdata[$num][0];
					$k2++;
					if($k2 > $num2)
					{
						$row2[0]="9999";
						$kla2="ZZ9999";
					}
					else
					{
						$row2 = mysql_fetch_array($err2);
						$kla2=substr($row2[2],0,2).$row2[0];
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
			for ($i=0;$i<=$num;$i++)
			{
				if ($wdata[$i][2] != $unidad)
				{
					if($unidad != "")
					{
						switch ($unidad)
						{
							case "1Q":
								$wdif=$wtotal1[1]-$wtotal1[2];
								if($wtotal1[2] != 0)
									$wpor=($wtotal1[1]-$wtotal1[2])/$wtotal1[2] *100;
								else
									$wpor=0;
								echo"<tr><td bgcolor='#cccccc'><b>TOTAL UNIDADES QUIRURGICAS</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";;
							break;
							case "2H":
								$wdif=$wtotal1[1]-$wtotal1[2];
								if($wtotal1[2] != 0)
									$wpor=($wtotal1[1]-$wtotal1[2])/$wtotal1[2] *100;
								else
									$wpor=0;
								echo"<tr><td bgcolor='#cccccc'><b>TOTAL UNIDADES HOSPITALARIAS</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
							break;
							case "2SF":
								$wdif=$wtotal1[1]-$wtotal1[2];
								if($wtotal1[2] != 0)
									$wpor=($wtotal1[1]-$wtotal1[2])/$wtotal1[2] *100;
								else
									$wpor=0;
								echo"<tr><td bgcolor='#cccccc'><b>TOTAL SERVICIO FARMACEUTICO</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
							break;
							case "3D":
								$wdif=$wtotal1[1]-$wtotal1[2];
								if($wtotal1[2] != 0)
									$wpor=($wtotal1[1]-$wtotal1[2])/$wtotal1[2] *100;
								else
									$wpor=0;
								echo"<tr><td bgcolor='#cccccc'><b>TOTAL UNIDADES DE DIAGNOSTICO</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
							break;
							case "4A":
								$wdif=$wtotal1[1]-$wtotal1[2];
								if($wtotal1[2] != 0)
									$wpor=($wtotal1[1]-$wtotal1[2])/$wtotal1[2] *100;
								else
									$wpor=0;
								echo"<tr><td bgcolor='#cccccc'><b>TOTAL UNIDAD DE URGENCIAS/EMERGENCIAS Y CONSULTA EXTERNA</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
							break;
							case "7E":
								$wdif=$wtotal1[1]-$wtotal1[2];
								if($wtotal1[2] != 0)
									$wpor=($wtotal1[1]-$wtotal1[2])/$wtotal1[2] *100;
								else
									$wpor=0;
								echo"<tr><td bgcolor='#cccccc'><b>TOTAL FARMACIA COMERCIAL</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
							break;
							case "5O":
								$wdif=$wtotal1[1]-$wtotal1[2];
								if($wtotal1[2] != 0)
									$wpor=($wtotal1[1]-$wtotal1[2])/$wtotal1[2] *100;
								else
										$wpor=0;
								echo"<tr><td  bgcolor='#cccccc'><b>TOTAL OTRAS UNIDADES</b></td><td   bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
								if($wter == "N")
								{
									$wdif=$wtotal2[1]-$wtotal2[2];
									if($wtotal2[2] != 0)
										$wpor=($wtotal2[1]-$wtotal2[2])/$wtotal2[2] *100;
									else
										$wpor=0;
									echo"<tr><td bgcolor='#99CCFF'><b>TOTAL CLINICA</b></td><td  bgcolor='#99CCFF' align=right><b>".number_format((double)$wtotal2[2],0,'.',',')."</b></td><td  bgcolor='#99CCFF' align=right><b>".number_format((double)$wtotal2[1],0,'.',',')."</b></td><td bgcolor='#99CCFF' align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td bgcolor='#99CCFF' align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
								}
						}
					}
					switch ($wdata[$i][2])
					{
						case "1Q":
							echo "<tr><td bgcolor='#FFFFFF' colspan=5><b>UNIDADES QUIRURGICAS</B></td></tr>";
						break;
						case "2H":
							echo "<tr><td bgcolor='#FFFFFF' colspan=5><b>UNIDADES HOSPITALARIAS</B></td></tr>";
						break;
						case "2SF":
							echo "<tr><td bgcolor='#FFFFFF' colspan=5><b>SERVICIO FARMACEUTICO</B></td></tr>";
						break;
						case "3D":
							echo "<tr><td bgcolor='#FFFFFF' colspan=5><b>UNIDADES DE DIAGNOSTICO</B></td></tr>";
						break;
						case "4A":
							echo "<tr><td bgcolor='#FFFFFF' colspan=5><b>UNIDAD DE URGENCIAS/EMERGENCIAS Y CONSULTA EXTERNA</B></td></tr>";
						break;
						case "5O":
							echo "<tr><td bgcolor='#FFFFFF' colspan=5><b>OTRAS UNIDADES</B></td></tr>";
						break;
						case "7E":
							echo "<tr><td bgcolor='#FFFFFF' colspan=5><b>FARMACIA COMERCIAL</B></td></tr>";
						break;
					}
					$wtotal1[1]=0;
					$wtotal1[2]=0;
					$unidad=$wdata[$i][2];
				}
				$wtotal1[1]=$wtotal1[1]+$wdata[$i][3];
				$wtotal1[2]=$wtotal1[2]+$wdata[$i][4];
				$wtotal2[1]=$wtotal2[1]+$wdata[$i][3];
				$wtotal2[2]=$wtotal2[2]+$wdata[$i][4];
				$wtott[1]=$wtott[1]+$wdata[$i][7];
				$wtott[2]=$wtott[2]+$wdata[$i][8];
				$wdif=$wdata[$i][3]-$wdata[$i][4];
				if($wdata[$i][4] != 0)
					$wpor=($wdata[$i][3]-$wdata[$i][4])/$wdata[$i][4]*100;
				else
					$wpor=0;
				if(isset($wdata[$i][4]) and isset($wdata[$i][3]) and $wres == "D")
				{
					switch ($tipo)
					{
						case 0:
							$path1="/matrix/presupuestos/reportes/000001_rc30.php?wanop=".$wanopa."&wper1=".$wper1."&wper2=".$wper2."&wccof=".$wdata[$i][0]."-"."&empresa=".$empresa."&wemp=".$wempt."&wgru=Todos";
							$path2="/matrix/presupuestos/reportes/000001_rc30.php?wanop=".$wanop."&wper1=".$wper1."&wper2=".$wper2."&wccof=".$wdata[$i][0]."-"."&empresa=".$empresa."&wemp=".$wempt."&wgru=Todos";
						break;
						case 1:
							$path1="/matrix/presupuestos/reportes/000001_rc32.php?wanop=".$wanopa."&wper1=".$wper1."&wper2=".$wper2."&wccof=".$wdata[$i][0]."-"."&empresa=".$empresa."&wemp=".$wempt;
							$path2="/matrix/presupuestos/reportes/000001_rc32.php?wanop=".$wanop."&wper1=".$wper1."&wper2=".$wper2."&wccof=".$wdata[$i][0]."-"."&empresa=".$empresa."&wemp=".$wempt;
						break;
						case 2:
							$path1="/matrix/presupuestos/reportes/000001_rc31.php?wanop=".$wanopa."&wper1=".$wper1."&wper2=".$wper2."&wccof=".$wdata[$i][0]."-"."&empresa=".$empresa."&wemp=".$wempt;
							$path2="/matrix/presupuestos/reportes/000001_rc31.php?wanop=".$wanop."&wper1=".$wper1."&wper2=".$wper2."&wccof=".$wdata[$i][0]."-"."&empresa=".$empresa."&wemp=".$wempt;
						break;
						case 3:
							$path1="/matrix/presupuestos/reportes/000001_rc128.php?wanop=".$wanopa."&wper1=".$wper1."&wper2=".$wper2."&wccof=".$wdata[$i][0]."-"."&empresa=".$empresa."&wemp=".$wempt;
							$path2="/matrix/presupuestos/reportes/000001_rc128.php?wanop=".$wanop."&wper1=".$wper1."&wper2=".$wper2."&wccof=".$wdata[$i][0]."-"."&empresa=".$empresa."&wemp=".$wempt;
						break;
					}
					if(isset($wemp))
						echo"<tr><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][4],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right>".number_format((double)$wdif,0,'.',',')."</td><td align=right>".number_format((double)$wpor,2,'.',',')."</td></tr>";
					else
						echo"<tr><td>".$wdata[$i][1]."</td><td align=right onclick='ejecutar(".chr(34).$path1.chr(34).")'>".number_format((double)$wdata[$i][4],0,'.',',')."</td><td align=right onclick='ejecutar(".chr(34).$path2.chr(34).")'>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right>".number_format((double)$wdif,0,'.',',')."</td><td align=right>".number_format((double)$wpor,2,'.',',')."</td></tr>";
				}
			}
			if($unidad == "5O")
				$wtextt="TOTAL OTRAS UNIDADES";
			elseif($unidad == "7E")
				$wtextt="TOTAL FARMACIA COMERCIAL";
					else
						$wtextt="TOTAL INDEFINIDO";
			if(isset($wtotal1[2]) and isset($wtotal1[1]))
			{
				$wdif=$wtotal1[1]-$wtotal1[2];
				if($wtotal1[2] != 0)
					$wpor=($wtotal1[1]-$wtotal1[2])/$wtotal1[2] *100;
				else
					$wpor=0;
				echo"<tr><td  bgcolor='#cccccc'><b>".$wtextt."</b></td><td  bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td  bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td  bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td  bgcolor='#cccccc' align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
			}
			if(isset($wtotal2[2]) and isset($wtotal2[1]))
			{
				$wdif=$wtotal2[1]-$wtotal2[2];
				if($wtotal2[2] != 0)
					$wpor=($wtotal2[1]-$wtotal2[2])/$wtotal2[2] *100;
				else
					$wpor=0;
				if ($wter == "N")
					echo"<tr><td  bgcolor='#FFCC99'><b>TOTAL INGRESOS PROPIOS</b></td><td  bgcolor='#FFCC99' align=right><b>".number_format((double)$wtotal2[2],0,'.',',')."</b></td><td  bgcolor='#FFCC99' align=right><b>".number_format((double)$wtotal2[1],0,'.',',')."</b></td><td  bgcolor='#FFCC99' align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td  bgcolor='#FFCC99' align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
				else
					echo"<tr><td  bgcolor='#FFCC99'><b>TOTAL INGRESOS PARA TERCEROS</b></td><td  bgcolor='#FFCC99' align=right><b>".number_format((double)$wtotal2[2],0,'.',',')."</b></td><td  bgcolor='#FFCC99' align=right><b>".number_format((double)$wtotal2[1],0,'.',',')."</b></td><td  bgcolor='#FFCC99' align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td  bgcolor='#FFCC99' align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
			}
			if ($wter == "N")
			{
				$row1[0]=$wtott[1];
				$row2[0]=$wtott[2];
				$wdif=$row1[0]-$row2[0];
				if($row2[0] != 0)
					$wpor=($row1[0]-$row2[0])/$row2[0]*100;
				else
					$wpor=0;
				echo"<tr><td  bgcolor='#CCFFFF'><b>TOTAL INGRESOS PARA TERCEROS</b></td><td  bgcolor='#CCFFFF' align=right><b>".number_format((double)$row2[0],0,'.',',')."</b></td><td  bgcolor='#CCFFFF' align=right><b>".number_format((double)$row1[0],0,'.',',')."</b></td><td  bgcolor='#CCFFFF' align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td  bgcolor='#CCFFFF' align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
				if(isset($wtotal2[2]) and isset($wtotal2[1]))
				{
					$wtotal2[1]=$wtotal2[1]+$row1[0];
					$wtotal2[2]=$wtotal2[2]+$row2[0];
					$wdif=$wtotal2[1]-$wtotal2[2];
				if($wtotal2[2] != 0)
					$wpor=($wtotal2[1]-$wtotal2[2])/$wtotal2[2] *100;
					else
						$wpor=0;
					echo"<tr><td  bgcolor='#FFCCFF'><b>TOTAL INGRESOS PMLA</b></td><td  bgcolor='#FFCCFF' align=right><b>".number_format((double)$wtotal2[2],0,'.',',')."</b></td><td  bgcolor='#FFCCFF' align=right><b>".number_format((double)$wtotal2[1],0,'.',',')."</b></td><td  bgcolor='#FFCCFF' align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td  bgcolor='#FFCCFF' align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
				}
			}
			$query = "select Eintex from ".$empresa."_000128 ";
			$query = $query."  where Einano = ".$wanop;
			$query = $query."    and Einemp = '".$wemp1."'";
			$query = $query."    and Einmei = ".$wper1;
			$query = $query."    and Einmef = ".$wper2;
			$query = $query."    and Einrep = 26 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$row = mysql_fetch_array($err);
				echo"<tr><td  bgcolor='#cccccc' colspan=5><b><font color='#CC0000'>EXPLICACIONES :</FONT></B></td></tr>";
				echo"<tr><td  bgcolor='#FFFFFF' colspan=5>".$row[0]."</td></tr>";
			}
			echo"</table>";
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
