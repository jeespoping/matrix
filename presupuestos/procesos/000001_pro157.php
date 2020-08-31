<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Distribucion Horas Variables con Criterio</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro157.php Ver. 2012-06-28</b></font></td></tr></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='000001_pro157.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	

	

	if(!isset($wanop) or !isset($wper1) or $wper1 < 1 or $wper1 > 12)
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>DISTRIBUCION HORAS VARIABLES CON CRITERIO</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o  Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Mes Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$wsw=0;
		$query  = "select ".$empresa."_000144.Msetip ";
		$query .= "  from ".$empresa."_000145,".$empresa."_000144 ";
		$query .= "   where ".$empresa."_000145.mhsano = ".$wanop;
		$query .= " 	and ".$empresa."_000145.mhsmes = ".$wper1;
		$query .= " 	and ".$empresa."_000145.mhscco = ".$empresa."_000144.msecco ";
		$query .= " 	and ".$empresa."_000145.mhsser = ".$empresa."_000144.mseser ";
		$query .= " 	and ".$empresa."_000145.mhsemp = ".$empresa."_000144.mseusu ";
		$query .= " 	and ".$empresa."_000144.Msetip not in (select ".$empresa."_000068.Mcrcri from ".$empresa."_000068 where ".$empresa."_000068.Mcrano = ".$wanop." and ".$empresa."_000068.Mcrmes = ".$wper1." and ".$empresa."_000068.Mcrtip = 'R' group by 1) ";
		$query .= "    group by 1 ";
		$err = mysql_query($query,$conex) or die("ERROR : ".mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			$wsw=1;
			$color1="#E8EEF7";
			$color2="#CC99FF";
			echo "<center><table border=0>";
			echo "<tr><td>CRITERIOS SIN MOVIMIENTO EN LA TABLA 68</td></tr>";
			for ($i=0;$i<$num;$i++)
			{
				if($i % 2 == 0)
					$color = $color1;
				else
					$color = $color2;
				$row = mysql_fetch_array($err);
				echo "<tr><td bgcolor=".$color." align=center>".$row[0]."</td></tr>";
			}
			echo "</table>";	
		}
		$query  = "select ".$empresa."_000068.Mcrcri,round(sum(".$empresa."_000068.Mcrpor),2) ";
		$query .= "   from ".$empresa."_000145,".$empresa."_000144,".$empresa."_000068 ";
		$query .= "    where ".$empresa."_000145.mhsano = ".$wanop;
		$query .= "  	and ".$empresa."_000145.mhsmes = ".$wper1; 
		$query .= "  	and ".$empresa."_000145.mhscco = ".$empresa."_000144.msecco ";
		$query .= "  	and ".$empresa."_000145.mhsser = ".$empresa."_000144.mseser ";
		$query .= "  	and ".$empresa."_000145.mhsemp = ".$empresa."_000144.mseusu ";
		$query .= "  	and ".$empresa."_000068.Mcrano = ".$wanop; 
		$query .= "  	and ".$empresa."_000068.Mcrmes = ".$wper1;
		$query .= "  	and ".$empresa."_000068.Mcrtip = 'R' "; 
		$query .= "  	and ".$empresa."_000068.Mcrcri = ".$empresa."_000144.Msetip ";
		$query .= "    group by 1 ";
		$err = mysql_query($query,$conex) or die("ERROR : ".mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			$wsw1=0;
			$wsw2=0;
			$color1="#E8EEF7";
			$color2="#CC99FF";
			for ($i=0;$i<$num;$i++)
			{
				if($i % 2 == 0)
					$color = $color1;
				else
					$color = $color2;
				$row = mysql_fetch_array($err);
				if($row[1] < 1)
				{
					if($wsw2 == 0)
					{
						echo "<center><table border=0>";
						echo "<tr><td colspan=2>CRITERIOS CON MOVIMIENTO INFERIOR AL 100%</td></tr>";
						$wsw2=1;
					}
					$wsw1=1;
					echo "<tr><td bgcolor=".$color." align=center>".$row[0]."</td><td bgcolor=".$color." align=center>".$row[1]."</td></tr>";
				}
			}
			if($wsw2 == 0)
				echo "</table>";	
		}
		if($wsw == 0 or $wsw1 == 0)
		{
			//                               0                      1                      2                               3                          4                  5
			$query  = "select ".$empresa."_000145.Mhscco,".$empresa."_000068.Mcrcco,".$empresa."_000145.Mhsser,(".$empresa."_000145.Mhshot * ".$empresa."_000068.Mcrpor),'R', ".$empresa."_000145.Mhsemp ";
			$query .= "   from ".$empresa."_000145,".$empresa."_000144,".$empresa."_000068 ";
			$query .= "   where ".$empresa."_000145.mhsano = ".$wanop; 
			$query .= " 	and ".$empresa."_000145.mhsmes = ".$wper1; 
			$query .= " 	and ".$empresa."_000145.mhscco = ".$empresa."_000144.msecco ";
			$query .= " 	and ".$empresa."_000145.mhsser = ".$empresa."_000144.mseser ";
			$query .= " 	and ".$empresa."_000145.mhsemp = ".$empresa."_000144.mseusu ";
			$query .= " 	and ".$empresa."_000068.Mcrano = ".$wanop;  
			$query .= " 	and ".$empresa."_000068.Mcrmes = ".$wper1; 
			$query .= " 	and ".$empresa."_000068.Mcrtip = 'R' ";  
			$query .= " 	and ".$empresa."_000068.Mcrcri = ".$empresa."_000144.Msetip ";
			$err = mysql_query($query,$conex) or die("ERROR : ".mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$query = "delete from ".$empresa."_000072  ";
					$query = $query."  where Mseano =  ".$wanop;
					$query = $query."    and Msemes =  ".$wper1;
					$query = $query."    and Msecco = '".$row[0]."'";
					$query = $query."    and Mseccd = '".$row[1]."'";
					$query = $query."    and Msecod = '".$row[2]."'";
					$query = $query."    and Msetip = 'R'";
					$err1 = mysql_query($query,$conex) or die("Error en el Borrado de la tabla 72");
					
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$query = "insert ".$empresa."_000072 (medico,fecha_data,hora_data,Mseano, Msemes, Msecco, Mseccd, Msecod, Msecan, Msetip, Mseusu, seguridad) values ('".$empresa."','".$fecha."','".$hora."',".$wanop.",".$wper1.",'".$row[0]."','".$row[1]."','".$row[2]."',".$row[3].",'".$row[4]."','".$row[5]."','C-".$empresa."')";
					$err1 = mysql_query($query,$conex) or die("Error en la Insercion tabla 72 ".mysql_errno().":".mysql_error());
					$count++;
					echo "REGISTROS INSERTADOS : ".$count."<BR>";
				}
			}
		}
	}
}
?>
</body>
</html>
