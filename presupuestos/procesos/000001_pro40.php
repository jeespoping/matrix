<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Proceso de Distribucion de Centros de Servicio Real</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro40.php Ver. 2015-11-20</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
function bi($d,$n,$k)
{
	$n--;
	if($n > 0)
	{
		$li=0;
		$ls=$n;
		while ($ls - $li > 1)
		{
			$lm=(integer)(($li + $ls) / 2);
			$val=strncmp(strtoupper($k),strtoupper($d[$lm][0]),20);
			if($val == 0)
				return $lm;
			elseif($val < 0)
					$ls=$lm;
				else
					$li=$lm;
		}
		if(strtoupper($k) == strtoupper($d[$li][0]))
			return $li;
		elseif(strtoupper($k) == strtoupper($d[$ls][0]))
					return $ls;
				else
					return -1;
	}
	elseif(isset($d[0][0]) and $d[0][0] == $k)
			return 0;
		else
			return -1;
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro40.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1) or !isset($witer) or !isset($wres) or (strtoupper ($wres) != "R" and strtoupper ($wres) != "P"))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>PROCESO DE DISTRIBUCION DE CENTROS DE SERVICIO REAL</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Numero de Iteraciones</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='witer' size=2 maxlength=2></td></tr>";
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
			$wemp=substr($wemp,0,strpos($wemp,"-"));
			$wres=strtoupper ($wres);
			$count=0;
			$query = "delete from ".$empresa."_000069 ";
			$query = $query."  where resano = ".$wanop;
			$query = $query."    and resmes = ".$wper1;
			$query = $query."    and resemp = '".$wemp."' ";
			$query = $query."    and restic = 'D' ";
			$query = $query."    and restip = '".$wres."'";
			$err = mysql_query($query,$conex);
			
			$MONDIS=array();
			$query  = "SELECT Resccd,sum(Resmon) from ".$empresa."_000069 "; 
			$query .= "   where Resano = ".$wanop;
			$query .= " 	and Resmes = ".$wper1;
			$query .= "     and Resemp = '".$wemp."' ";
			$query .= " 	and Restip = 'R' ";
			$query .= " 	and Restic = 'V' ";
			$query .= "    group by Resccd ";
			$query .= "    order by Resccd ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$MONDIS[$i][0]=$row[0];
					$MONDIS[$i][1]=$row[1];
				}
			}
			$numdis=$num;
			echo $numdis."<br>";
			
			$query = "SELECT cxccco,cxccri,cxcpor,madmon from ".$empresa."_000066,".$empresa."_000067 ";
			$query = $query."  where cxcano = ".$wanop;
			$query = $query."    and cxcmes = ".$wper1;
			$query = $query."    and cxcemp = '".$wemp."' ";
			$query = $query."    and cxctip = '".$wres."'";
			$query = $query."    and cxcano = madano";
			$query = $query."    and cxcmes = madmes";
			$query = $query."    and cxcemp = mademp";
			$query = $query."    and cxctip = madtip";
			$query = $query."    and cxccco = madcco";
			$query = $query."   order by cxccco,cxccri";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$col=$num;
			$ccocri=array();
			$pormon=array();
			$wmonto=0;
			$wmontom="";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($wmontom != $row[0])
				{
					$pos=bi($MONDIS,$numdis,$row[0]);
					if($pos != -1)
					{
						$wadd = $MONDIS[$pos][1];
					}
					else
					{
						$wadd = 0;
					}
					$wmonto+=($row[3]+$wadd);
					$wmontom=$row[0];
				}
				$ccocri[$i+1][0]=$row[0];
				$ccocri[$i+1][1]=$row[1];
				$pormon[$i+1][0]=$row[2];
				$pormon[$i+1][1]=($row[3]+$wadd);
			}
			$query = "SELECT mcrcco,mcrcri,mcrpor from ".$empresa."_000068 ";
			$query = $query."  where mcrano = ".$wanop;
			$query = $query."    and mcrmes = ".$wper1;
			$query = $query."    and mcremp = '".$wemp."' ";
			$query = $query."    and mcrtip = '".$wres."'";
			$query = $query."   order by mcrcco,mcrcri";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$data=array();
			$cc=-1;
			$ccn="";
			echo "<b>Monto a Distribuir : ".number_format((double)$wmonto,2,'.',',')."</b><br>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($ccn != $row[0])
				{
					$cc++;
					$data[$cc][0][0]=$row[0];
					$ccn=$row[0];
					for ($j=0;$j<$col;$j++)
						$data[$cc][$j+1][0]=0;
				}
				for ($j=0;$j<$col;$j++)
				{
					if($ccocri[$j+1][1] == $row[1])
					{
						$data[$cc][$j+1][0]=$row[2];
					}
				}
			}
			for ($k=1;$k<=$witer;$k++)
			{
				for ($i=0;$i<$cc+1;$i++)
				{
					for ($j=1;$j<=$col;$j++)
						$data[$i][$j][$k]=$pormon[$j][0]*$pormon[$j][1]*$data[$i][$j][0];
				}
				for ($i=1;$i<=$col;$i++)
				{
					$wsw=0;
					for ($j=0;$j<$cc+1;$j++)
						if ($data[$j][0][0] == $ccocri[$i][0])
						{
							$fil=$j;
							$wsw=1;
						 }
					$suma=0;
					 if($wsw == 1)
						for ($j=1;$j<=$col;$j++)
							$suma+=$data[$fil][$j][$k];
					$pormon[$i][1]=$suma;
				}
			}
			$suma=0;
			for ($i=0;$i<$cc+1;$i++)
			{
				for ($k=1;$k<=$witer;$k++)
					for ($j=1;$j<=$col;$j++)
					{
						$wsw=0;
						for ($w=1;$w<=$col;$w++)
							if ($data[$i][0][0] == $ccocri[$w][0])
								$wsw=1;
						if($wsw == 0)
							$suma+=$data[$i][$j][$k];
					}
			}
			echo "<br><b>Monto Distibuido : ".number_format((double)$suma,2,'.',',')."</b><br><br>";
			for ($i=0;$i<$cc+1;$i++)
			{
				$suma=0;
				$ccoant="";
				for ($j=1;$j<=$col;$j++)
				{
					if ($ccocri[$j][0] != $ccoant)
					{
						if ($ccoant != "")
						{
							echo "Centro Origen : ".$ccoant." Centro Destino : ".$data[$i][0][0]." Monto Distibuido : ".number_format((double)$suma,2,'.',',')."<br>";
							if($suma > 0)
							{
								$fecha = date("Y-m-d");
								$hora = (string)date("H:i:s");
        						$query = "insert ".$empresa."_000069 (medico,fecha_data,hora_data,resemp,resano,resmes,rescco,resccd,resmon,restic,restip,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wper1.",'".$ccoant."','".$data[$i][0][0]."',".$suma.",'D','".$wres."','C-".$empresa."')";
								$err1 = mysql_query($query,$conex);
								$count++; 
							}
						}
						$suma=0;
						$ccoant=$ccocri[$j][0];
					}
					for ($k=1;$k<=$witer;$k++)
						$suma+=$data[$i][$j][$k];
				}
				echo "Centro Origen : ".$ccoant." Centro Destino : ".$data[$i][0][0]." Monto Distibuido : ".number_format((double)$suma,2,'.',',')."<br>";
				if($suma > 0)
				{
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
        			$query = "insert ".$empresa."_000069 (medico,fecha_data,hora_data,resemp,resano,resmes,rescco,resccd,resmon,restic,restip,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wper1.",'".$ccoant."','".$data[$i][0][0]."',".$suma.",'D','".$wres."','C-".$empresa."')";
					$err1 = mysql_query($query,$conex);
					$count++; 
				}
			}
			echo "REGISTROS INSERTADOS . ".$count."<br>";
		}
	}
?>
</body>
</html>
