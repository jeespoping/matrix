<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Centro de Servicios Comparativo Distribuido Inicial x Mes</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc83.php Ver. 2016-05-27</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
function comparacion($vec1,$vec2)
{
	if($vec1[15] > $vec2[15])
		return 1;
	elseif ($vec1[15] < $vec2[15])
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
		

		

		echo "<form action='000001_rc83.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wres) or (strtoupper ($wres) != "R" and strtoupper ($wres) != "P")  or !isset($wper1)  or !isset($wper2)  or ((!isset($wcco1)  or !isset($wcco2)) and !isset($wccof)) or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>CENTRO DE SERVICIOS COMPARATIVO DISTRIBUIDO INICIAL X MES</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			if($key=="costosyp" or (isset($call) and $call == "SIG") or (isset($call) and strtoupper ($call) == "SIC"))
			{
				echo "<tr><td bgcolor=#cccccc align=center>Unidad Inicial de Proceso</td>";
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
				echo "<tr><td bgcolor=#cccccc align=center>Unidad Final de Proceso</td>";
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco2' size=4 maxlength=4></td></tr>";
			}
			else
			{
				echo "<tr><td bgcolor=#cccccc align=center>Unidad Proceso</td><td bgcolor=#cccccc align=center>";
				$query = "SELECT Cc,cconom  from ".$empresa."_000125,".$empresa."_000005 where empleado ='".$key."'  and cc=ccocod order by Cc";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					echo "<select name='wccof'>";
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						echo "<option>".$row[0]."-".$row[1]."</option>";
					}
					echo "</select>";
				}
				echo "</td></tr>";
			}
			echo "<input type='HIDDEN' name= 'call' value='".$call."'>";
			echo "<tr><td bgcolor=#cccccc align=center>Tipo de Informacion (R - Real / P - Presupuestada)</td>";
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
			if(isset($wccof))
			{
				$ini=strpos($wccof,"-");
				$wcco1=substr($wccof,0,$ini);
				$wcco2=substr($wccof,0,$ini);
			}
			$wcco2=strtolower ($wcco2);
			$wres=strtoupper ($wres);
			$query = "select Rescco,cconom, Resmes,Restic  ,sum(Resmon)  as monto from ".$empresa."_000069,".$empresa."_000005 ";
			$query = $query."  where Resano  = ".$wanop;
			$query = $query."    and Resemp = '".$wemp."'";
			$query = $query."    and Resmes  between ".$wper1." and ".$wper2;
			$query = $query."    and Resccd between '".$wcco1."' and '".$wcco2."'";
			$query = $query."    and Restip = '".$wres."'";
			$query = $query."    and Resemp = ccoemp ";
			$query = $query."    and Rescco = ccocod ";
			$query = $query."   group by  Rescco,cconom, Resmes,Restic     ";
			$query = $query."   order by Rescco, Resmes";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<table border=1>";
			echo "<tr><td colspan=16 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=16 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=16 align=center><b>EMPRESA : ".$wempt."</b></td></tr>";
			echo "<tr><td colspan=16 align=center>CENTRO DE SERVICIOS COMPARATIVO DISTRIBUIDO INICIAL X MES</td></tr>";
			echo "<tr><td colspan=16 align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. " A&Ntilde;O : ".$wanop."</td></tr>";
			$wdat=array();
			$wdatt=array();
			$wmeses=array();
			for ($i=$wper1;$i<=$wper2;$i++)
			{
				switch ($i)
					{
						case 1:
							$wmeses[$i]="ENERO";
							break;
						case 2:
							$wmeses[$i]="FEBRERO";
							break;
						case 3:
							$wmeses[$i]="MARZO";
							break;
						case 4:
							$wmeses[$i]="ABRIL";
							break;
						case 5:
							$wmeses[$i]="MAYO";
							break;
						case 6:
							$wmeses[$i]="JUNIO";
							break;
						case 7:
							$wmeses[$i]="JULIO";
							break;
						case 8:
							$wmeses[$i]="AGOSTO";
							break;
						case 9:
							$wmeses[$i]="SEPTIEMBRE";
							break;
						case 10:
							$wmeses[$i]="OCTUBRE";
							break;
						case 11:
							$wmeses[$i]="NOVIEMBRE";
							break;
						case 12:
							$wmeses[$i]="DICIEMBRE";
							break;
					}
			}
			echo "<tr><td><b>C.C.</b></td><td><b>DESCRIPCION</b></td>";
			for ($i=$wper1;$i<=$wper2;$i++)
				echo "<td align=center><b>".$wmeses[$i]."</b></td>";
			echo "</tr>";
			for ($i=2;$i<15;$i++)
				$wdatt[$i]=0;
			$seg=-1;
			$segn="";
			$wdatt[0]="TOTAL";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($row[0] != $segn)
				{
					$seg++;
					$segn=$row[0];
					$wdat[$seg][0]=$row[0];
					$wdat[$seg][1]=$row[1];
					$wdat[$seg][15]=$row[3];
					for ($j=2;$j<15;$j++)
						$wdat[$seg][$j]=0;
				}
				$wdat[$seg][$row[2]+1]+=$row[4];
				$wdat[$seg][13]+=$row[4];
				$wdatt[$row[2]+1]+=$row[4];
				$wdatt[13]+=$row[4];
			}
			usort($wdat,'comparacion');
			$wst=array();
			for ($j=$wper1;$j<=$wper2;$j++)
				$wst[$j]=0;
			$wtic="D";
			if($num > 0)
			{
				for ($i=0;$i<=$seg;$i++)
				{
					if($wdat[$i][15] != $wtic)
					{
						echo"<tr><td bgcolor='#FFCC66' colspan=2><b>TOTAL DISTRIBUIDOS</b></td>";
						for ($j=$wper1;$j<=$wper2;$j++)
							echo "<td align=right bgcolor='#FFCC66'><b>".number_format((double)$wst[$j],2,'.',',')."</b></td>";	
						echo "</tr>";	
						for ($j=$wper1;$j<=$wper2;$j++)
							$wst[$j]=0;
						$wtic=$wdat[$i][15];
					}
					echo"<tr><td>".$wdat[$i][0]."</td><td>".$wdat[$i][1]."</td>";
					for ($j=$wper1;$j<=$wper2;$j++)
					{
						$wst[$j]+=$wdat[$i][$j+1];
						echo "<td align=right>".number_format((double)$wdat[$i][$j+1],2,'.',',')."</td>";	
					}			
					echo "</tr>";	
				}
				if($wtic == "D")
					echo"<tr><td bgcolor='#FFCC66' colspan=2><b>TOTAL DISTRIBUIDOS</b></td>";
				else
					echo"<tr><td bgcolor='#FFCC66' colspan=2><b>TOTAL VARIABLES</b></td>";
				for ($j=$wper1;$j<=$wper2;$j++)
					echo "<td align=right bgcolor='#FFCC66'><b>".number_format((double)$wst[$j],2,'.',',')."</b></td>";	
				echo "</tr>";	
				echo"<tr><td bgcolor='#99CCFF' colspan=2><b>".$wdatt[0]."</b></td>";
				for ($j=$wper1;$j<=$wper2;$j++)
					echo "<td bgcolor='#99CCFF' align=right><b>".number_format((double)$wdatt[$j+1],2,'.',',')."</b></td>";		
				echo "</tr></table>";				
			}
		}
	}
?>
</body>
</html>
