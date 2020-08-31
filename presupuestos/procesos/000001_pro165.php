<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Evaluacion Laboratorio Medico (Linea 29)</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro183.php Ver. 2015-05-29</b></font></td></tr></table>
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
		

		

		echo "<form action='000001_pro165.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wmesi))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>EVALUACION LABORATORIO MEDICO (LINEA 29)</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesi' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$query = "delete from ".$empresa."_000108  ";
			$query = $query."  where Mosano =  ".$wanop;
			$query = $query."    and Mosmes =  ".$wmesi;
			$query = $query."    and Mostip = 'LM' ";
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
			
			$COSTO=array();
			$wanoa = $wanop - 1;
			$query  = "select lpad(costosyp_000116.Labhis,12,'0'),lpad(costosyp_000116.Labing,5,'0'),sum(costosyp_000116.Labmon) ";
			$query .= "  from costosyp_000116 "; 
			$query .= "  where costosyp_000116.Labano = ".$wanop;
			$query .= " 	and costosyp_000116.Labmes between 1 and ".$wmesi; 
			$query .= "  group by 1,2  ";
			$query .= " UNION ALL  ";
			$query .= " select lpad(costosyp_000116.Labhis,12,'0'),lpad(costosyp_000116.Labing,5,'0'),sum(costosyp_000116.Labmon) ";
			$query .= "   from costosyp_000116 ";  
			$query .= "   where costosyp_000116.Labano = ".$wanoa; 
			$query .= " 	and costosyp_000116.Labmes between ".$wmesi." and 12 "; 
			$query .= "  group by 1,2 ";
			$query .= "  order by 1,2 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$COSTO[$i][0]=$row[0].$row[1];
					$COSTO[$i][1]=$row[2];
				}
			}
			$numcos=$num;
			$kla = "";
			$k = 0;
			//                                  0                      1                      2                          3
			$query  = "select costosyp_000108.Moshis,costosyp_000108.Mosing,costosyp_000108.Mosent,sum(costosyp_000108.Mosipr) ";
			$query .= "   from costosyp_000108  "; 
			$query .= "   where costosyp_000108.mosano = ".$wanop; 
			$query .= " 	and costosyp_000108.mosmes = ".$wmesi;  
			$query .= " 	and costosyp_000108.mostip = 'FA' ";
			$query .= " 	and costosyp_000108.moscon = '0001' "; 
			$query .= " 	and costosyp_000108.moscco = '3081' ";
			$query .= "  group by 1,2,3 "; 
			$query .= "  order by 1,2,4 desc ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					if($kla != str_pad($row[0], 12, "0", STR_PAD_LEFT).str_pad($row[1], 5, "0", STR_PAD_LEFT))
					{
						$kla = str_pad($row[0], 12, "0", STR_PAD_LEFT).str_pad($row[1], 5, "0", STR_PAD_LEFT);
						$pos=bi($COSTO,$numcos,$kla);
						if($pos != -1)
						{
							$wcosto = $COSTO[$pos][1];
							$fecha = date("Y-m-d");
							$hora = (string)date("H:i:s");
							$utt = $wcosto * (-1);
							$ctv = $wcosto;
							$utv = $ctv * (-1);
							$query = "insert ".$empresa."_000108 (medico,fecha_data,hora_data,Mosano,Mosmes,Mostip,Moscon,Moslin,Moscco,Mosent,Mospro,Mosdes,Moshis,Mosing,Mosmed,Moscan,Mosipr,Mosite,Mosctt,Mosutt,Mosctv,Mosutv,Mosest,seguridad) values ('".$empresa."','".$fecha."','".$hora."',".$wanop.",".$wmesi.",'LM','0001','29','3081','".$row[2]."','0','','".$row[0]."','".$row[1]."','0',0,0,0,".$wcosto.",".$utt.",".$ctv.",".$utv.",'on','C-".$empresa."')";
							$err2 = mysql_query($query,$conex);
							$k++;
							echo "REGISTROS INSERTADO : ".$k."<br>";
						}
					}
               	}
			}
			echo "<b>NUMERO DE REGISTROS INSERTADOS : ".$k."</b><br>";
        }
}		
?>
</body>
</html>
