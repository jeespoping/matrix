<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Evaluacion de Procedimientos Costeados (Lineas 3,5,7,9,11,13)</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro164.php Ver. 2016-10-25</b></font></td></tr></table>
</center>
<?php
include_once("conex.php");

@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro164.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wmesi) or !isset($wanoc) or !isset($wmesc))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>EVALUACION DE PROCEDIMIENTOS COSTEADOS (LINEAS 3,5,7,9,11,13)</td></tr>";
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
			//                  0      1      2       3      4     5     6      7      8     9    10     11     12       13  
			$query = "select Moscco,Mostip,Moslin,Moscon,Mospro,Mosent,Moshis,Mosing,Mosmed,'T',Mosipr,Moscan,Cfaeva,sum(Pcapro) ";
			$query .= " from ".$empresa."_000108,".$empresa."_000060,".$empresa."_000097 "; 
			$query .= "   where mosano = ".$wanop;
			$query .= " 	and mosmes = ".$wmesi;
			$query .= " 	and mostip = 'FA' ";
			$query .= " 	and moslin in ('5','7','9','11','13')  ";
			$query .= " 	and moscon = cfacod  ";
			$query .= " 	and cfaemp = '01'  "; 
			$query .= " 	and moscco = pcacco ";  
			$query .= " 	and mospro = pcacod ";  
			$query .= " 	and moscon = pcacon  "; 
			$query .= " 	and pcaemp = '01' ";
			$query .= " 	and pcaano = ".$wanoc;
			$query .= " 	and pcames = ".$wmesc;
			$query .= " group by 1,2,3,4,5,6,7,8,9,10,11,12,13  ";
			$query .= " union all  ";
			$query .= " select Moscco,Mostip,Moslin,Moscon,Mospro,Mosent,Moshis,Mosing,Mosmed,'V',Mosipr,Moscan,Cfaeva,sum(Cvapro) ";
			$query .= " from ".$empresa."_000108,".$empresa."_000060,".$empresa."_000082 ";
			$query .= "   where mosano = ".$wanop;
			$query .= " 	and mosmes = ".$wmesi; 
			$query .= " 	and mostip = 'FA' ";
			$query .= " 	and moslin in ('5','7','9','11','13')  ";
			$query .= " 	and moscon = cfacod  "; 
			$query .= " 	and cfaemp = '01'  "; 
			$query .= " 	and moscco = cvacco ";   
			$query .= " 	and mospro = cvacod ";   
			$query .= " 	and moscon = cvacon ";  
			$query .= " 	and cvaemp = '01' "; 
			$query .= " 	and cvaano = ".$wanoc;
			$query .= " 	and cvames = ".$wmesc;
			$query .= " group by 1,2,3,4,5,6,7,8,9,10,11,12,13 "; 
			$query .= " order by 1,2,3,4,5,6,7,8,9,10"; 
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$k1=0;
			$key = "";
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					//echo "clave : ".$key." reg : ".$row[0].$row[1].$row[2].$row[3].$row[4].$row[5].$row[6].$row[7].$row[8].$row[9]."<br>";
					if($row[0].$row[1].$row[2].$row[3].$row[4].$row[5].$row[6].$row[7].$row[8] != $key and $row[9] == "T")
					{
						if($i > 0 and $wsw >= 1)
						{
							$wsw2=0;
							if($data[12] == "0")
							{
								$wsw2=1;
								$wctt = 0;
								$wutt = 0;
								$wctv = 0;
								$wutv = 0;
							}
							elseif($data[12] == "1")
							{
								$wsw2=1;
								$wctt = $data[11] * $data[13];
								$wutt = $data[10] - $wctt;
								$wctv = $data[11] * $data[14];
								$wutv = $data[10] - $wctv;
							}
							elseif($data[12] == "2")
							{
								$wsw2=1;
								$wctt = 0;
								$wutt = $data[10];
								$wctv = 0;
								$wutv = $data[10];
							}	
							if($wsw2 == 1)
							{	
								$wsw=0;		
								$query = "update ".$empresa."_000108 set Mosctt=".$wctt.",Mosutt=".$wutt.",Mosctv=".$wctv.",Mosutv=".$wutv.",Mosest='on' where Mosano=".$wanop." and Mosmes=".$wmesi." and Mostip='".$data[1]."' and Moscon='".$data[3]."' and Moslin='".$data[2]."' and Moscco='".$data[0]."' and Mosent='".$data[5]."' and Mospro='".$data[4]."' and Moshis='".$data[6]."' and Mosing='".$data[7]."' and Mosmed='".$data[8]."'  ";
								//echo $query."<br>";
								$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
								$k1++;
								echo "REGISTROS ACTUALIZADO : ".$k1."<br>";
							}
						}
						$wsw=1;
						$data=array();
						$data[0] = $row[0]; // Moscco
						$data[1] = $row[1]; // Mostip
						$data[2] = $row[2]; // Moslin
						$data[3] = $row[3]; // Moscon
						$data[4] = $row[4]; // Mospro
						$data[5] = $row[5]; // Mosent
						$data[6] = $row[6]; // Moshis
						$data[7] = $row[7]; // Mosing
						$data[8] = $row[8]; // Mosmed
						$data[9] = $row[9]; // Tipo
						$data[10] = $row[10]; // Mosipr
						$data[11] = $row[11]; // Moscan
						$data[12] = $row[12]; // Cfeeva
						$data[13] = $row[13]; // Pcapro
						$data[14] = 0; // Cvapro
						$key = $row[0].$row[1].$row[2].$row[3].$row[4].$row[5].$row[6].$row[7].$row[8];
					}
					elseif($row[0].$row[1].$row[2].$row[3].$row[4].$row[5].$row[6].$row[7].$row[8] == $key and $row[9] == "V")
						{
							$wsw++;
							$data[14] = $row[13];
						}
               	}
               	if($wsw >= 1)
				{
					$wsw2=0;
					if($data[12] == "0")
					{
						$wsw2=1;
						$wctt = 0;
						$wutt = 0;
						$wctv = 0;
						$wutv = 0;
					}
					elseif($data[12] == "1")
					{
						$wsw2=1;
						$wctt = $data[11] * $data[13];
						$wutt = $data[10] - $wctt;
						$wctv = $data[11] * $data[14];
						$wutv = $data[10] - $wctv;
					}
					elseif($data[12] == "2")
					{
						$wsw2=1;
						$wctt = 0;
						$wutt = $data[10];
						$wctv = 0;
						$wutv = $data[10];
					}	
					if($wsw2 == 1)
					{	
						$wsw=0;		
						$query = "update ".$empresa."_000108 set Mosctt=".$wctt.",Mosutt=".$wutt.",Mosctv=".$wctv.",Mosutv=".$wutv.",Mosest='on' where Mosano=".$wanop." and Mosmes=".$wmesi." and Mostip='".$data[1]."' and Moscon='".$data[3]."' and Moslin='".$data[2]."' and Moscco='".$data[0]."' and Mosent='".$data[5]."' and Mospro='".$data[4]."' and Moshis='".$data[6]."' and Mosing='".$data[7]."' and Mosmed='".$data[8]."'  ";
						//echo $query."<br>";
						$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
						$k1++;
						echo "REGISTROS ACTUALIZADO : ".$k1."<br>";
					}
				}
			}
			echo "<b>NUMERO DE REGISTROS ACTUALIZADOS : ".$k1."</b><br>";
        }
}		
?>
</body>
</html>
