<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Informe de Explicaciones De Presupuestos</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc149.php Ver. 2016-02-05</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc149.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1)  or !isset($wper2) or !isset($wcco) or !isset($wcod) or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>INFORME DE EXPLICACIONES DE PRESUPUESTOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
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
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Proceso</td><td bgcolor=#cccccc align=center>";
			$query = "SELECT Cc,cconom  from ".$empresa."_000125,".$empresa."_000005 where empleado ='".$key."'  and cc=ccocod order by Cc";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wcco'>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				echo "</select>";
			}
			echo "</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Rubro Presupuestal</td>";
			$query = "SELECT mgacod,mganom from ".$empresa."_000028 order by mganom";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<td bgcolor=#cccccc align=center><select name='wcod'>";
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
			$meses=array();
			$meses[1]="ENE";
			$meses[2]="FEB";
			$meses[3]="MAR";
			$meses[4]="ABR";
			$meses[5]="MAY";
			$meses[6]="JUN";
			$meses[7]="JUL";
			$meses[8]="AGO";
			$meses[9]="SEP";
			$meses[10]="OCT";
			$meses[11]="NOV";
			$meses[12]="DIC";
			if(strpos($wcod,"-") !== false)
			{
				$wncod=substr($wcod,strpos($wcod,"-")+1);
				$wcod=substr($wcod,0,strpos($wcod,"-"));
			}
			else
				$wncod=$wcod;
			if(strpos($wcco,"-") !== false)
			{
				$wncco=substr($wcco,strpos($wcco,"-")+1);
				$wcco=substr($wcco,0,strpos($wcco,"-"));
			}
			else
				$wncco=$wcco;
			$n=$wper2-$wper1+3;
				
				//             0       1         2     
			$query = "select gasmes,Gasdes,sum(Gasval) ";
			$query = $query."   from ".$empresa."_000012 ";
			$query = $query."   where gascco = '".$wcco."' ";
			$query = $query."     and gascod = '".$wcod."' ";
			$query = $query."     and gasano = ".$wanop;
			$query = $query."     and gasemp = '".$wemp."'";
			$query = $query."     and gasmes between ".$wper1." and ".$wper2; 
			$query = $query."    group by 1,2 ";
			$query = $query."    order by 2,1 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$winf=array();
			echo "<center><table border=0>";
			echo "<tr><td colspan=".$n." align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=".$n." align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=".$n." align=center>INFORME DE EXPLICACIONES DE PRESUPUESTOS</td></tr>";
			echo "<tr><td colspan=".$n." align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. " A&Ntilde;O : ".$wanop."</td></tr>";
			echo "<tr><td colspan=".$n." align=center>UNIDAD : ".$wncco."</td></tr>";
			echo "<tr><td colspan=".$n." align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td colspan=".$n." align=center>RUBRO PRESUPUESTAL : ".$wncod."</td></tr>";
			$color="#CCCCCC";
			echo "<tr><td bgcolor=".$color."><b>EXPLICACION</b></td>";
			for ($i=$wper1;$i<=$wper2;$i++)
				echo "<td bgcolor=".$color."><b>".$meses[$i]."</b></td>";
			echo "<td bgcolor=".$color."><b>TOTAL</b></tr>";
			$expa="";
			$data=array();
			$tot=array();
			$data[0]="";
			$k=0;
			for ($i=$wper1;$i<=$wper2;$i++)
				$data[$i]=0;
			$data[$wper2+1]=0;
			for ($i=$wper1;$i<=$wper2;$i++)
				$tot[$i]=0;
			$tot[$wper2+1]=0;
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($row[1] != $expa)
				{
					if($i > 0)
					{
						$k++;
						if($k % 2 == 0)
							$color="#FFFFFF";
						else
							$color="#99CCFF";
						echo "<tr><td bgcolor=".$color.">".$expa."</td>";
						for ($w=$wper1;$w<=$wper2;$w++)
							echo "<td bgcolor=".$color." align=right>".number_format((double)$data[$w],0,'.',',')."</td>";
						echo "<td bgcolor=".$color." align=right>".number_format((double)$data[$wper2+1],0,'.',',')."</td>";
						echo "</tr>";
					}
					$expa=$row[1];
					for ($w=$wper1;$w<=$wper2;$w++)
						$data[$w]=0;
					$data[$wper2+1]=0;
				}
				$data[$row[0]]=$row[2];
				$data[$wper2+1] += $row[2];
				$tot[$row[0]] += $row[2];
				$tot[$wper2+1] += $row[2];
    		}
    		$k++;
    		if($k % 2 == 0)
				$color="#FFFFFF";
			else
				$color="#99CCFF";
    		echo "<tr><td bgcolor=".$color.">".$expa."</td>";
			for ($w=$wper1;$w<=$wper2;$w++)
				echo "<td bgcolor=".$color." align=right>".number_format((double)$data[$w],0,'.',',')."</td>";
			echo "<td bgcolor=".$color." align=right>".number_format((double)$data[$wper2+1],0,'.',',')."</td>";
			echo "</tr>";
			$color="#CCCCCC";
    		echo "<tr><td bgcolor=".$color."><b>TOTALES GENERALES</b></td>";
			for ($w=$wper1;$w<=$wper2;$w++)
				echo "<td bgcolor=".$color." align=right><b>".number_format((double)$tot[$w],0,'.',',')."</b></td>";
			echo "<td bgcolor=".$color." align=right><b>".number_format((double)$tot[$wper2+1],0,'.',',')."</b></td>";
			echo "</tr>";
			echo "</tabla></center>";
		}
	}
?>
</body>
</html>
