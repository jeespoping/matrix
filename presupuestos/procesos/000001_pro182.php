<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Evaluacion de Cama Hospitalaria (Linea 3)</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro182.php Ver. 2016-10-25</b></font></td></tr></table>
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
	

	

	echo "<form action='000001_pro182.php' method=post>";
	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wanop) or !isset($wmesi) or !isset($wanoc) or !isset($wmesc))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>EVALUACION DE CAMA HOSPITALARIA (LINEA 3)</td></tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
		echo "<td bgcolor=#cccccc align=center>Mes de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesi' size=2 maxlength=2></td></tr>";
		echo "<td bgcolor=#cccccc align=center>A&ntilde;o de Costeo</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanoc' size=4 maxlength=4></td></tr>";
		echo "<td bgcolor=#cccccc align=center>Mes de Costeo</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesc' size=2 maxlength=2></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{	
		$wanoa = $wanop - 1;
		$wmesa = $wmesi + 1;
		$query = "delete  from ".$empresa."_000108 where Mosano=".$wanop." and Mosmes=".$wmesi." and Mostip='CH' and Moslin='3' ";
		$err = mysql_query($query,$conex);
		
		$query = "update ".$empresa."_000113 set Mhoanp=0, Mhomep=0  ";
		$query = $query."  where Mhoanp =  ".$wanop;
		$query = $query."    and Mhomep =  ".$wmesi;
		$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
		
		$CTOT=array();
		$query  = "SELECT Pcacco,Pcacod,Pcapro from ".$empresa."_000097 where Pcaemp='01' and Pcaano=".$wanoc." and Pcames=".$wmesc." and Pcagru in ('HOS','CDC','CAM')"; 
		$query .= "    order by 1,2 ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$CTOT[$i][0]=$row[0].$row[1];
				$CTOT[$i][1]=$row[2];
			}
		}
		$numctot=$num;
		
		$CVAR=array();
		$query  = "SELECT Cvacco,Cvacod,Cvapro from ".$empresa."_000082 where Cvaemp='01' and Cvaano=".$wanoc." and Cvames=".$wmesc." and Cvagru in ('HOS','CDC','CAM')"; 
		$query .= "    order by 1,2 ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$CVAR[$i][0]=$row[0].$row[1];
				$CVAR[$i][1]=$row[2];
			}
		}
		$numcvar=$num;
		
		$clave = "";

				
		//                  0       1       2        3       4       5          6           7        8         
		$query  = "select Mhohis, Mhoing, Mhocco, Mhotip, Mosent, Mhonom, sum(Mhocan),sum(mosipr), Moscon ";
		$query .= "  from ".$empresa."_000108,".$empresa."_000113 ";
		$query .= "   where mosano = ".$wanop; 
		$query .= " 	and mosmes = ".$wmesi;
		$query .= " 	and moslin = '3' ";   
		$query .= " 	and mostip = 'FA' ";  
		$query .= " 	and mhohis = moshis ";
		$query .= " 	and mhoing = mosing ";
		$query .= " 	and mhotip = mospro ";
		$query .= " 	and mhocco = moscco ";
		$query .= " 	and mhoanp = 0 ";
		$query .= " 	and mhomep = 0 ";
		$query .= "  Group by 1,2,3,4,5,6,9  ";
		$query .= "  order by 1,2,3,4,8 desc ";		
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			$k = 0;
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				
				$pos=bi($CTOT,$numctot,$row[2].$row[3]);
				if($pos != -1)
				{
					$wcost = $CTOT[$pos][1];
				}
				else
				{
					$wcost = 0;
				}
				$pos=bi($CVAR,$numcvar,$row[2].$row[3]);
				if($pos != -1)
				{
					$wcosv = $CVAR[$pos][1];
				}
				else
				{
					$wcosv = 0;
				}
				
				$wtipo="CH";
				$westado="on";
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
				$wipr = 0;
				$wite = 0;
				$wctt = $wcost * $row[6];
				$wutt = -$wctt;
				$wctv = $wcosv * $row[6];
				$wutv = - $wctv;
				
				if($clave != $row[0].$row[1].$row[2].$row[3])
				{

					if($wcco == "1020")
						$wcon = "2009";
					else
						$wcon = "0035";
					$query = "insert ".$empresa."_000108 (medico,fecha_data,hora_data,Mosano,Mosmes,Mostip,Moscon,Moslin,Moscco,Mosent,Mospro,Mosdes,Moshis,Mosing,Mosmed,Moscan,Mosipr,Mosite,Mosctt,Mosutt,Mosctv,Mosutv,Mosest,seguridad) values ('".$empresa."','".$fecha."','".$hora."',".$wanop.",".$wmesi.",'".$wtipo."','".$wcon."','3','".$row[2]."','".$row[4]."','".$row[3]."','".$row[5]."','".$row[0]."','".$row[1]."','0',".$row[6].",".$wipr.",".$wite.",".$wctt.",".$wutt.",".$wctv.",".$wutv.",'".$westado."','C-".$empresa."')";
					//echo $query."<br>";
					$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$k++;
					echo "REGISTRO INSERTADO NRO : ".$k."<br>";
					$query  = "update  ".$empresa."_000113 set Mhoanp=".$wanop.", Mhomep=".$wmesi."  ";
					$query .= "   where Mhoano = ".$wanop;
					$query .= " 	and Mhomes between 1 and ".$wmesi; 
					$query .= " 	and Mhoanp = 0 "; 
					$query .= " 	and Mhomep = 0 "; 
					$query .= " 	and Mhohis = '".$row[0]."'";
					$query .= " 	and Mhoing = '".$row[1]."'";
					$err2 = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
					
					$query  = "update  ".$empresa."_000113 set Mhoanp=".$wanop.", Mhomep=".$wmesi."  ";
					$query .= "   where Mhoano = ".$wanoa;
					$query .= " 	and Mhomes between ".$wmesa." and 12 "; 
					$query .= " 	and Mhoanp = 0 "; 
					$query .= " 	and Mhomep = 0 "; 
					$query .= " 	and Mhohis = '".$row[0]."'";
					$query .= " 	and Mhoing = '".$row[1]."'";
					$err2 = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
					$clave = $row[0].$row[1].$row[2].$row[3];
				}
			}
		}
		echo "<b>NUMERO DE REGISTROS INSERTADOS : ".$k."</b><br>";
	}
}		
?>
</body>
</html>
