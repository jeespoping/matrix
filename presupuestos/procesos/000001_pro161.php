<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Seleccion Movimiento de Notas (T118 a T108)</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro161.php Ver. 2017-03-01</b></font></td></tr></table>
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

function buscar_parametros($PAR,$num,$con,$cco)
{
	for ($i=0;$i<$num;$i++)
	{
		if($PAR[$i][0] > 0 and $PAR[$i][1] > 0 and $PAR[$i][0] == $cco and $PAR[$i][1] == $con)
			return $i;
		elseif($PAR[$i][0] == 0 and $PAR[$i][1] > 0 and $PAR[$i][1] == $con)
				return $i;
			elseif($PAR[$i][0] > 0 and $PAR[$i][1] == 0 and $PAR[$i][0] == $cco)
					return $i;
	}
	return -1;
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro161.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wmesi))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>SELECCION MOVIMIENTO DE NOTAS (T118 A T108)</td></tr>";
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
			$query = $query."    and (Mostip = 'NC' ";
			$query = $query."     or  Mostip = 'ND') ";
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
			
			$LINEA=array();
			$query  = "SELECT Cfacod, Cfalin from ".$empresa."_000060  where Cfaemp='01' "; 
			$query .= "    order by 1 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$LINEA[$i][0]=$row[0];
					$LINEA[$i][1]=$row[1];
				}
			}
			$numlin=$num;
			
			$ENTIDAD=array();
			$query  = "SELECT Epmcod, Empcin from ".$empresa."_000061 where Empemp='01' "; 
			$query .= "    order by 1 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$ENTIDAD[$i][0]=$row[0];
					$ENTIDAD[$i][1]=$row[1];
				}
			}
			$nument=$num;

			$k=0;
			$k1=0;
			$query = "SELECT Parcci, Parcon, Parccf  from ".$empresa."_000146 ";
			$query = $query." where Parest = 'on' ";
			$query = $query."   and Paremp = '01' ";
			$query = $query." Order by Parseg ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$PAR=array();
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$PAR[$i][0] = $row[0];
					$PAR[$i][1] = $row[1];
					$PAR[$i][2] = $row[2];
				}
			}
			$numP = $num;
			
			$query = "SELECT lpad(Rclced,15,'0'), Rclcon, Rcllin  from ".$empresa."_000110 ";
			$query = $query." where Rclest = 'on' ";
			$query = $query." Order by 1,2 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$RCL=array();
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$RCL[$i][0] = $row[0].$row[1];
					$RCL[$i][1] = $row[2];
				}
			}
			$numRel = $num;
			
			
			//                  0        1        2       3       4      5        6         7             8
			$query = "SELECT Notfue , Notemp , Notcco ,Notcon ,Notmed ,Nothis, Noting, sum(Notinp) , sum(Notint) FROM ".$empresa."_000118 ";
			$query = $query." WHERE Notano = ".$wanop;
			$query = $query."   AND Notmes = ".$wmesi;
			$query = $query." GROUP BY 1,2,3,4,5,6,7 ";
			$query = $query." ORDER BY 1,2,3,4,5,6,7 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$pos = buscar_parametros($PAR,$numP,$row[3],$row[2]);
					if($pos != -1)
						$row[2] = $PAR[$pos][2];
					
					$pos=bi($RCL,$numRel,str_pad($row[4], 15, "0", STR_PAD_LEFT).$row[3]);
					if($pos != -1)
					{
						$wlin = $RCL[$pos][1];
					}
					else
					{
						$pos=bi($LINEA,$numlin,$row[3]);
						if($pos != -1)
						{
							$wlin = $LINEA[$pos][1];
						}
						else
						{
							$wlin = "00";
						}
					}
					$pos=bi($ENTIDAD,$nument,$row[1]);
					if($pos != -1)
					{
						$wemp = $ENTIDAD[$pos][1];
					}
					else
					{
						$wemp = "991";
					}
					if($row[0] == '27')
					{
						$row[7] = $row[7] * (-1);
						$row[8] = $row[8] * (-1);
					}
					if($row[0] == '25')
						$wfue="ND";
					else
						$wfue="NC";
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$query = "insert ".$empresa."_000108 (medico,fecha_data,hora_data,Mosano,Mosmes,Mostip,Moscon,Moslin,Moscco,Mosent,Mospro,Mosdes,Moshis,Mosing,Mosmed,Moscan,Mosipr,Mosite,Mosctt,Mosutt,Mosctv,Mosutv,Mosest,seguridad) values ('".$empresa."','".$fecha."','".$hora."',".$wanop.",".$wmesi.",'".$wfue."','".$row[3]."','".$wlin."','".$row[2]."','".$wemp."','0',' ','".$row[5]."','".$row[6]."','".$row[4]."',0,".$row[7].",".$row[8].",0,".$row[7].",0,".$row[7].",'on','C-".$empresa."')";
					$err2 = mysql_query($query,$conex);
					if($err2 != 1)
					{
						$query = "update ".$empresa."_000108 set Mosipr=Mosipr+".$row[7].",Mosite=Mosite+".$row[8].",Mosutt=Mosutt+".$row[7].",Mosutv=Mosutv+".$row[7]." where Mosano=".$wanop." and Mosmes=".$wmesi." and Mostip='".$wfue."' and Moscon='".$row[3]."' and Moslin='".$wlin."' and Moscco='".$row[2]."' and Mosent='".$wemp."' and Mospro='0' and Moshis='".$row[5]."' and Mosing='".$row[6]."' and Mosmed='".$row[4]."' ";
						$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
						$k1++;
						echo "REGISTROS ACTUALIZADO : ".$k1."<br>";
					}
					else
					{
						$k++;
						echo "REGISTROS INSERTADOS : ".$k."<br>";
					}
               	}
			}
			echo "<b>NUMERO DE REGISTROS INSERTADOS : ".$k."</b><br>";
			echo "<b>NUMERO DE REGISTROS ACTUALIZADOS : ".$k1."</b><br>";
        }
}		
?>
</body>
</html>
