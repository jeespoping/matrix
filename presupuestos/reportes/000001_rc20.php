<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Informe de Resultados en Cuentas en Participacion</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc20.php Ver. 2017-01-13</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc20.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione"  or !isset($wper1)  or !isset($wper2) or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>INFORME DE RESULTADOS EN CUENTAS EN PARTICIPACION</td></tr>";
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
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$wempt = $wemp;
			$wemp = substr($wemp,0,2);
			$query = "SELECT cierre_real from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and mes =   ".$wper2;
			$query = $query."    and Emp = '".$wemp."' ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if($num > 0 and $row[0] == "on")
			{
			echo "<center><table border=1>";
			echo "<tr><td align=center colspan=6><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
			echo "<tr><td align=center  colspan=6><b>APLICACION DE PRESUPUESTOS</b></td></tr>";
			echo "<tr><td align=center  colspan=6><b>INFORME DE RESULTADOS EN CUENTAS EN PARTICIPACION</b></td></tr>";
			echo "<tr><td colspan=6 align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. " A&Ntilde;O : ".$wanop."</td></tr>";
			echo "<tr><td colspan=6 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td><b>CODIGO C.C.</b></td><td><b>NOMBRE C.C.</b></td><td><b>% PARTICIPACION</b></td><td><b>RESULTADOS UNIDAD</b></td><td><b>PARTICIPACION TERCEROS</b></td><td><b>PARTICIPACION PMLA</b></td></tr>";
			$query = "SELECT cupcco,cconom,meccpr,cuppor,sum(mecval) as wmonto from ".$empresa."_000026,".$empresa."_000057,".$empresa."_000005 ";
			$query = $query."  where mecano = ".$wanop;
			$query = $query."    and mecemp = '".$wemp."'";
			$query = $query."    and mecmes between ".$wper1." and ".$wper2;
			$query = $query."    and meccco = cupcco";
			$query = $query."    and mecemp = cupemp ";
			$query = $query."    and meccco = ccocod";
			$query = $query."    and mecemp = ccoemp ";
			$query = $query."   group by cupcco,cconom,meccpr,cuppor";
			$query = $query."   order by cupcco,meccpr,cuppor";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$wtotal=array();
			$wtotG=array();
			for ($j=0;$j<3;$j++)
				$wtotG[$j]=0;
			$wccoant="0";
			$wccnant="";
			$wporant=0;
			$count=0;
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($row[0] != $wccoant)
				{
					if($wccoant != "0")
					{
						$wtotal[10] = $wtotal[1] - $wtotal[2] - $wtotal[3] + $wtotal[4] - $wtotal[5] - $wtotal[6] + $wtotal[7] - $wtotal[8];
						$wpart=$wtotal[10]*($wporant/100);
						$wpmla=$wtotal[10]-$wpart;
						$wtotG[0]=$wtotG[0]+$wtotal[10];
						$wtotG[1]=$wtotG[1]+$wpart;
						$wtotG[2]=$wtotG[2]+$wpmla;
						 echo "<tr>";
	   					 echo "<td>".$wccoant."</td>";
	   					 echo "<td>".$wccnant."</td>";
	   					 echo "<td align=right>".number_format($wporant,2,'.',',')."%</td>";
	   					 echo "<td align=right>".number_format($wtotal[10],0,'.',',')."</td>";
	   					 echo "<td align=right>".number_format($wpart,0,'.',',')."</td>";
	   					 echo "<td align=right>".number_format($wpmla,0,'.',',')."</td></tr>";
					}
					$wccoant = $row[0];
					$wporant = $row[3];
					$wccnant = $row[1];;
					for ($j=0;$j<9;$j++)
						$wtotal[$j]=0;
				}
				$it=(integer)substr($row[2],0,1);
				if($it != 7)
					$wtotal[$it]=$wtotal[$it]+$row[4];
                 else
                 	if($row[0] == "700")
                 		$wtotal[7]=$wtotal[7]+$row[4];

    		}
    		$wtotal[10] = $wtotal[1] - $wtotal[2] - $wtotal[3] + $wtotal[4] - $wtotal[5] - $wtotal[6] + $wtotal[7] - $wtotal[8];
			$wpart=$wtotal[10]*($wporant/100);
			$wpmla=$wtotal[10]-$wpart;
			$wtotG[0]=$wtotG[0]+$wtotal[10];
			$wtotG[1]=$wtotG[1]+$wpart;
			$wtotG[2]=$wtotG[2]+$wpmla;
			 echo "<tr>";
			 echo "<td>".$wccoant."</td>";
			 echo "<td>".$wccnant."</td>";
			 echo "<td align=right>".number_format($wporant,2,'.',',')."%</td>";
			 echo "<td align=right>".number_format($wtotal[10],0,'.',',')."</td>";
			 echo "<td align=right>".number_format($wpart,0,'.',',')."</td>";
			 echo "<td align=right>".number_format($wpmla,0,'.',',')."</td></tr>";
			 echo "<tr>";
			 echo "<td>&nbsp</td>";
			 echo "<td><B>TOTALES</B></td>";
			 echo "<td align=right>&nbsp</td>";
			 echo "<td align=right><B>".number_format($wtotG[0],0,'.',',')."</B></td>";
			 echo "<td align=right><B>".number_format($wtotG[1],0,'.',',')."</B></td>";
			 echo "<td align=right><B>".number_format($wtotG[2],0,'.',',')."</B></td></tr>";
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
