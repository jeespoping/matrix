<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Analisis de Sensibilidad de Microorganismos a los Antibioticos</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rb01.php Ver. 1.00</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
function comparacion($vec1,$vec2)
{
	if($vec1[1] > $vec2[1])
		return -1;
	elseif ($vec1[1] < $vec2[1])
				return 1;
			else
				return 0;
}
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rb01.php' method=post>";
		if(!isset($wanop) or !isset($wuni) or !isset($wper1) or $wper1 < 1 or $wper1 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>ANALISIS DE SENSIBILIDAD DE MICROORGANISMOS A LOS ANTIBIOTICOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Año de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Semestre de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><select name='wper1'>";
			for ($i=1;$i<3;$i++)
			{
				echo "<option>".$i."</option>";
			}
			echo "</select></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad</td>";
			echo "<td bgcolor=#cccccc align=center>";
			$query = "SELECT unidad from cominf_000015 where usuario = '".$key."' order by unidad";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wuni'>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."</option>";
				}
				echo "</select>";
			}
			echo "</td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$wuni=strtoupper ($wuni);
			$Gp=array();
			$Gn=array();
			$query = "select Antibiotico from cominf_000014,cominf_000013 ";
			$query = $query."  where Germen  = nombre ";
			$query = $query."      and Tipo = 'GP-Gram Positivo' ";
			$query = $query."      and Activo = 'on' ";
			$query = $query."    Group by Antibiotico ";
			$query = $query."    order by Antibiotico ";
			$err = mysql_query($query,$conex);
			$nump = mysql_num_rows($err);
			for ($i=0;$i<$nump;$i++)
			{
				$row = mysql_fetch_array($err);
				$Gp[$i]=$row[0];
			}
			$query = "select Antibiotico from cominf_000014,cominf_000013 ";
			$query = $query."  where Germen  = nombre ";
			$query = $query."      and Tipo = 'GN-Gram Negativo' ";
			$query = $query."      and Activo = 'on' ";
			$query = $query."    Group by Antibiotico ";
			$query = $query."    order by Antibiotico ";
			$err = mysql_query($query,$conex);
			$numn = mysql_num_rows($err);
			for ($i=0;$i<$numn;$i++)
			{
				$row = mysql_fetch_array($err);
				$Gn[$i]=$row[0];
			}
			$query = "select Microorganismo,Tipo,cominf_000012.Codigo,Sensibilidad,Cultivos from cominf_000011,cominf_000012,cominf_000013 ";
			$query = $query."  where Ano  = ".$wanop;
			$query = $query."      and Semestre  = ".$wper1;
			$query = $query."      and Unidad  = '".$wuni."'";
			$query = $query."      and Antibiotico  = cominf_000012.nombre ";
			$query = $query."      and Microorganismo  = cominf_000013.nombre ";
			$query = $query."      and Activo  = 'on' ";
			$query = $query."    order by Tipo,Microorganismo,Codigo,Sensibilidad,Cultivos ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$segp=-1;
			$key="";
			$segn=-1;
			$wdatp=array();
			$wdatn=array();
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($row[0] != $key)
				{
					if(substr($row[1],0,2) == "GP")
					{
						$segp++;
						$key=$row[0];
						$wdatp[$segp][0]=$row[0];
						$wdatp[$segp][1]=$row[4];
						for ($j=2;$j<=$nump+1;$j++)
						{
							$wdatp[$segp][$j]=0;
							$wdatp[$segp][$j+$nump]=0;
						}
					}
					else
					{
						$segn++;
						$key=$row[0];
						$wdatn[$segn][0]=$row[0];
						$wdatn[$segn][1]=$row[4];
						for ($j=2;$j<=$numn+1;$j++)
						{
							$wdatn[$segn][$j]=0;
							$wdatn[$segn][$j+$numn]=0;
						}
					}
				}
				if(substr($row[1],0,2) == "GP")
				{
					$pos=-1;
					for ($j=0;$j<$nump;$j++)
						if($Gp[$j] == $row[2])
							$pos=$j;
					if($pos != -1)
					{
						$wdatp[$segp][$pos+2]+=$row[3];
						$wdatp[$segp][$pos+$nump+2]+=$row[4];
					}
				}
				else
				{
					$pos=-1;
					for ($j=0;$j<$numn;$j++)
						if($Gn[$j] == $row[2])
							$pos=$j;
					if($pos != -1)
					{
						$wdatn[$segn][$pos+2]+=$row[3];
						$wdatn[$segn][$pos+$numn+2]+=$row[4];
					}
				}
			}
			usort($wdatp,'comparacion');
			usort($wdatn,'comparacion');
			$col=($nump * 2) + 2;
			echo "<table border=1>";
			echo "<tr><td colspan=".$col." align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=".$col." align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=".$col." align=center>ANALISIS DE SENSIBILIDAD DE MICROORGANISMOS A LOS ANTIBIOTICOS</td></tr>";
			echo "<tr><td colspan=".$col." align=center>UNIDAD  : ".$wuni."</td></tr>";
			echo "<tr><td colspan=".$col." align=center>SEMESTRE  : ".$wper1." AÑO : ".$wanop."</td></tr>";
			echo "<tr><td colspan=".$col." align=center><b>GRAM POSITIVO</b></td></tr>";
			echo "<tr><td><b>MICROORGANISMO</b></td><td><b>Nro. Cepas</b></td>";
			for ($i=0;$i<$nump;$i++)
				echo "<td align=center colspan=2><b>".$Gp[$i]."</b></td>";
			echo "</tr>";
			for ($i=0;$i<=$segp;$i++)
			{
				echo"<tr><td>".$wdatp[$i][0]."</td><td align=center>".$wdatp[$i][1]."</td>";
				for ($j=0;$j<$nump;$j++)
				{
					$query = "select Visible from cominf_000014 ";
					$query = $query."  where Germen  = '".$wdatp[$i][0]."'";
					$query = $query."      and Antibiotico  = '".$Gp[$j]."'";
					$err = mysql_query($query,$conex);
					$row = mysql_fetch_array($err);
					if($row[0] == "on")
						if($wdatp[$i][$j+2] != 999)
						{
							if($wdatp[$i][$j+2] > 79)
								$color="#006600";
							elseif ($wdatp[$i][$j+2] > 49)
										$color="#FF9900";
									else
										$color="#CC0000";
							echo"<td align=center bgcolor=#dddddd><font size=5 color=".$color."><b>".$wdatp[$i][$j+2]."</b></font></td><td align=right VALIGN =BASELINE><font size=2>".$wdatp[$i][$j+$nump+2]."</font></td>";
						}
						else
							echo"<td  align=center><font size=5>*</font></td><td align=right  VALIGN =BASELINE><font size=2>".$wdatp[$i][$j+$nump+2]."</font></td>";
					else
						echo"<td bgcolor=#999999 align=center><font size=5>&nbsp</font></td><td align=right bgcolor=#999999><font size=2>&nbsp</font></td>";
				}
				echo "</tr>";	
			}
			echo "</Table><br><br><br>";
			$col=($numn * 2) + 2;
			echo "<table border=1>";	
			echo "<tr><td colspan=".$col." align=center><b>GRAM NEGATIVO</b></td></tr>";
			echo "<tr><td><b>MICROORGANISMO</b></td><td><b>Nro. Cepas</b></td>";
			for ($i=0;$i<$numn;$i++)
				echo "<td align=center colspan=2><b>".$Gn[$i]."</b></td>";
			echo "</tr>";
			for ($i=0;$i<=$segn;$i++)
			{
				echo"<tr><td>".$wdatn[$i][0]."</td><td align=center>".$wdatn[$i][1]."</td>";
				for ($j=0;$j<$numn;$j++)
				{
					$query = "select Visible from cominf_000014 ";
					$query = $query."  where Germen  = '".$wdatn[$i][0]."'";
					$query = $query."      and Antibiotico  = '".substr($Gn[$j],0,7)."'";
					$err = mysql_query($query,$conex);
					$row = mysql_fetch_array($err);
					if($row[0] == "on")
						if($wdatn[$i][$j+2] != 999)
						{
							if($wdatn[$i][$j+2] > 79)
								$color="#006600";
							elseif ($wdatn[$i][$j+2] > 49)
										$color="#FF9900";
									else
										$color="#CC0000";
							echo"<td  align=center bgcolor=#dddddd><font size=5 color=".$color."><b>".$wdatn[$i][$j+2]."</b></font></td><td  align=right VALIGN =BASELINE><font size=2>".$wdatn[$i][$j+$numn+2]."</font></td>";
						}
						else
							echo"<td align=center><font size=5>*</font></td><td align=right VALIGN =BASELINE><font size=2>".$wdatn[$i][$j+$numn+2]."</font></td>";
					else
						echo"<td bgcolor=#999999 align=center><font size=5>&nbsp</font></td><td align=right bgcolor=#999999><font size=2>&nbsp</font></td>";
				}
				echo "</tr>";	
			}
			echo "</Table>";			
		}
	}
?>
</body>
</html>
