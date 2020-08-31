<html>
<head>
  	<title>MATRIX Balance De Articulos Entre Unidades</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Balance De Articulos Entre Unidades</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b>Balance.php Ver. 2008-09-25</b></font></tr></td></table>
</center> 
<?php
include_once("conex.php");
function bi($d,$n,$k,$i)
{
	$n--;
	if($n > 0)
	{
		$li=0;
		$ls=$n;
		while ($ls - $li > 1)
		{
			$lm=(integer)(($li + $ls) / 2);
			if(strtoupper($k) == strtoupper($d[$lm][$i]))
				return $lm;
			elseif(strtoupper($k) < strtoupper($d[$lm][$i]))
						$ls=$lm;
					else
						$li=$lm;
		}
		if(strtoupper($k) == strtoupper($d[$li][$i]))
			return $li;
		elseif(strtoupper($k) == strtoupper($d[$ls][$i]))
					return $ls;
				else
					return -1;
	}
	else
		return -1;
}
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='Balance' action='Balance.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wdias) or !isset($war1) or !isset($war2))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>BALANCE DE ARTICULOS ENTRE UNIDADES</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Meses a Analizar</td><td bgcolor=#cccccc align=center>";
		echo "<select name='wmes'>";
		for ($i=1;$i<13;$i++)
			echo "<option>".$i."</option>";
		echo "</select>";
		echo "</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Dias de Reposicion</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wdias' size=2 maxlength=2></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Articulo Inicial</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='war1' size=8 maxlength=8></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Articulo Final</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='war2' size=8 maxlength=8></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		echo "<table border=0 align=center>";
		echo "<tr><td align=center><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=center bgcolor=#999999><font size=6 face='tahoma'><b>BALANCE DE ARTICULOS ENTRE UNIDADES</font><font size=2> <b>Ver. 2008-09-25</b></font></b></font></td></tr>";
		$color="#dddddd";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Meses a Analizar : </b>".$wmes."</td></tr>";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Dias de Reposicion : </b>".$wdias."</td></tr>";	
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Articulo Inicial : </b>".$war1."</td></tr>";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Articulo Final : </b>".$war2."</td></tr></table>";
		$arti=array();
		$query = "SELECT  Artcod,  Artnom, Artuni  from ".$empresa."_000001 ";
		$query .= "    ORDER BY  Artcod ";
		$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$arti[$i][0]=$row[0];
				$arti[$i][1]=$row[1];
				$arti[$i][2]=$row[2];
			}
		}
		$k1=$num;
		$pdv=array();
		$pdvn=array();
		$query = "SELECT  Pvpcod, Pvppri, Pvpcol  from ".$empresa."_000090 ";
		$query .= " WHERE  Pvpest = 'on' ";
		$query .= "    ORDER BY  Pvppri ";
		$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
		$num = mysql_num_rows($err);
		$uni="(";
		if($num > 0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$pdvn[$i]=$row[0];
				$pdv[$row[0]]=$row[1];
				$pdvc[$i]=$row[2];
				if($i == 0)
					$uni .= chr(34).$row[0].chr(34);
				else
					$uni .= ",".chr(34).$row[0].chr(34);
			}
		}
		$uni .= ")";
		$tpdv=$num;
		$kardex=array();
		$query = "SELECT Karcco, Karcod, Karexi from ".$empresa."_000007 ";
		$query .= " WHERE  Karcco in ".$uni;
		$query .= "    GROUP BY  Karcco, Karcod ";
		$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$kardex[$i][0]=$row[0].$row[1];
				$kardex[$i][1]=$row[0];
				$kardex[$i][2]=$row[1];
				$kardex[$i][3]=$row[2];
			}
		}
		$k=$num;
		echo "<table border=0 align=center>";
		echo "<tr><td align=center rowspan=2 bgcolor=#999999><font face='tahoma' size=2><b>CODIGO</b></font></td>";
		echo "<td align=center rowspan=2 bgcolor=#999999><font face='tahoma' size=2><b>DESCRIPCION</b></font></td>";
		echo "<td align=center rowspan=2 bgcolor=#999999><font face='tahoma' size=2><b>UNIDAD</b></font></td>";
		for ($i=0;$i<$tpdv;$i++)
			echo "<td align=center colspan=2 bgcolor=#999999><font face='tahoma' size=2><b>".$pdvn[$i]."</b></font></td>";
		echo "<td align=center colspan=2 bgcolor=#999999><font face='tahoma' size=2><b>TOTAL</b></font></td>";
		echo "<td align=center rowspan=2 bgcolor=#999999><font face='tahoma' size=2><b>PLAN DE TRANSLADOS</b></font></td>";
		echo "</tr>";
		echo "<tr>";
		//echo "<td align=center colspan=3 bgcolor=#999999>&nbsp</td>";
		for ($i=0;$i<=$tpdv;$i++)
			echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>CANTIDAD<BR>A PEDIR</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>CANTIDAD<BR>SOBRE_STOCK</b></font></td>";
		echo "</tr>";
		$wtot=0;
		$wvap=0;
		$wvst=0;
		$wfec2=date("Y-m-d");
		$wmest=(integer)substr($wfec2,5,2) - $wmes;
		$wanot=(integer)substr($wfec2,0,4);
		if($wmest < 0)
		{
			$wmest=12 + $wmest;
			$wanot=$wanot - 1;
		}
		if($wmest < 10)
			$wfec1=$wanot."-0".$wmest."-".substr($wfec2,8,2);
		else
			$wfec1=$wanot."-".$wmest."-".substr($wfec2,8,2);
		$data=array();
		$query = "SELECT  Mencco, Mdeart, sum(Mdecan) from ".$empresa."_000010,".$empresa."_000011 ";
		$query .= " where Mencon in ('105','802') ";
		$query .= " and Menfec between '".$wfec1."' and '".$wfec2."'";
		$query .= " and Mencco  in ".$uni;
		$query .= " and Mencon  = Mdecon ";
		$query .= " and Mendoc  = Mdedoc ";
		$query .= " and Mdeart between '".$war1."' and '".$war2."'";
		$query .= " group by Mencco, Mdeart ";
		$query .= " order by Mdeart, Mencco ";
		$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		$clave="";
		$clave1="";
		$item=-1;
		if($num > 0)
		{
			$wvap=0;
			$wvst=0;
			$malos=0;
			$buenos=0;
			$muchos=0;
			for ($i=0;$i<$num;$i++)
			{
				$wtot++;
				$row = mysql_fetch_array($err);
				if($row[1] != $clave)
				{
					if($i > 0)
					{
						if($item % 2 == 0)
							$color="#9999FF";
						else
							$color="#ffffff";
						echo "<tr>";
						echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$data[$item][0]."</font></td>";	
						echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$data[$item][1]."</font></td>";	
						echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$data[$item][2]."</font></td>";
						$tfila=($tpdv * 2)+2;
						for ($j=0;$j<$tfila;$j++)
						{
							if($j < $tfila - 2)
							{
								$PC=round((($j + 3) - 2) / 2) - 1;
								$colorS=$pdvc[$PC];
								if($j % 2 == 0)
								{
									$colorF="#CC0000";
									$data[$item][($tfila - 2)+3] += $data[$item][$j+3];
								}
								else
								{
									$colorF="#000066";
									$data[$item][($tfila - 1)+3] += $data[$item][$j+3];
								}
							}
							else
							{
								$colorS=$color;
								if($j % 2 == 0)
									$colorF="#CC0000";
								else
									$colorF="#000066";
							}
							echo "<td bgcolor=".$colorS." align=right><font face='tahoma' size=2 color=".$colorF.">".number_format((double)$data[$item][$j+3],2,'.',',')."</font></td>";	
						}
						echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>";
						$plan="";
						if($data[$item][($tfila - 2)+3] > 0 and $data[$item][($tfila - 1)+3] > 0)
						{
							$muchos++;
							$sobrantes=array();
							for($w=0;$w<$tpdv;$w++)
								$sobrantes[$w]=$data[$item][($w * 2)+4];
							for($w=0;$w<$tpdv;$w++)
							{
								if($data[$item][($w * 2)+3] > 0)
								{
									$z=$tpdv - 1;
									$deficit=$data[$item][($w * 2)+3];
									while($deficit > 0 and $z != $w and $z >= 0)
									{
										if($sobrantes[$z] > 0)
										{
											if($sobrantes[$z] >= $deficit)
											{
												$sobrantes[$z]=$sobrantes[$z]-$deficit;
												$plan .= $pdvn[$z]."->".$pdvn[$w]." : ".$deficit."<br>";
												$deficit=-1;
											}
											else
											{
												$deficit=$deficit - $sobrantes[$z];
												$plan .= $pdvn[$z]."->".$pdvn[$w]." : ".$sobrantes[$z]."<br>";
												$sobrantes[$z]=0;
											}
										}
										$z--;
									}
								}
							}
						}
						echo $plan."</font></td>";
						echo "</tr>";
					}
					$item=$item+1;
					$clave=$row[1];
					$clave1=$row[0].$row[1];
					$pos=bi($arti, $k1, $clave, 0);
					if($pos != -1)
					{
						$data[$item][0]=$arti[$pos][0];
						$data[$item][1]=$arti[$pos][1];
						$data[$item][2]=$arti[$pos][2];
					}
					else
					{
						$data[$item][0]="NO ESPECIFICO";
						$data[$item][1]="NO ESPECIFICO";
						$data[$item][2]="NO ESPECIFICO";
					}
					$tfila=($tpdv * 2)+2;
					for ($j=0;$j<$tfila;$j++)
						$data[$item][$j+3]=0;
					$data[$item][$tfila+3]="";
				}
				if($row[0].$row[1] != $clave1)
					$clave1=$row[0].$row[1];
				$pos=bi($kardex, $k, $clave1, 0);
				if($pos != -1)
				{
					$exi=$kardex[$pos][3];
					$buenos++;
				}
				else
				{
					$exi=0;
					$malos++;
				}
				$cot=$row[2];
				$cop=$cot / $wmes;
				$exm=$cop * ($wdias / 30);
				$exM=$cop + $exm;
				$ptr=($exm + $exM)/ 2;
				$cap=0;
				$sst=0;
				if(($exM - $exi) > 0 and $ptr > $exi)
				{
					$cap=$ptr - $exi;
					$cap=round($cap);
				}
				else
				{
					if($exM < $exi)
					{
						$sst=($exM - $exi)*(-1);
						$sst=round($sst);
					}
				}
				$pos=$pdv[$row[0]];
				$data[$item][($pos+1)*2-1]=$cap;
				$data[$item][($pos+1)*2]=$sst;				
			}
			if($item % 2 == 0)
				$color="#9999FF";
			else
				$color="#ffffff";
			echo "<tr>";
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$data[$item][0]."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$data[$item][1]."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$data[$item][2]."</font></td>";
			$tfila=($tpdv * 2)+2;
			for ($j=0;$j<$tfila;$j++)
			{
				if($j < $tfila - 2)
				{
					$PC=round((($j + 3) - 2) / 2) - 1;
					$colorS=$pdvc[$PC];
					if($j % 2 == 0)
					{
						$colorF="#CC0000";
						$data[$item][($tfila - 2)+3] += $data[$item][$j+3];
					}
					else
					{
						$colorF="#000066";
						$data[$item][($tfila - 1)+3] += $data[$item][$j+3];
					}
				}
				else
				{
					$colorS=$color;
					if($j % 2 == 0)
						$colorF="#CC0000";
					else
						$colorF="#000066";
				}
				echo "<td bgcolor=".$colorS." align=right><font face='tahoma' size=2 color=".$colorF.">".number_format((double)$data[$item][$j+3],2,'.',',')."</font></td>";	
			}
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>";
			$plan="";
			if($data[$item][($tfila - 2)+3] > 0 and $data[$item][($tfila - 1)+3] > 0)
			{
				$muchos++;
				$sobrantes=array();
				for($w=0;$w<$tpdv;$w++)
					$sobrantes[$w]=$data[$item][($w * 2)+4];
				for($w=0;$w<$tpdv;$w++)
				{
					if($data[$item][($w * 2)+3] > 0)
					{
						$z=$tpdv - 1;
						$deficit=$data[$item][($w * 2)+3];
						while($deficit > 0 and $z != $w and $z >= 0)
						{
							if($sobrantes[$z] > 0)
							{
								if($sobrantes[$z] >= $deficit)
								{
									$sobrantes[$z]=$sobrantes[$z]-$deficit;
									$plan .= $pdvn[$z]."->".$pdvn[$w]." : ".$deficit."<br>";
									$deficit=-1;
								}
								else
								{
									$deficit=$deficit - $sobrantes[$z];
									$plan .= $pdvn[$z]."->".$pdvn[$w]." : ".$sobrantes[$z]."<br>";
									$sobrantes[$z]=0;
								}
							}
							$z--;
						}
					}
				}
			}
			echo $plan."</font></td>";
			echo "</tr>";
		}
		echo"</table>";
		//echo "Malos : ".$malos."<br>";
		//echo "Buenos : ".$buenos."<br>";
		//echo "Muchos : ".$muchos."<br>";
	}
}
?>
</body>
</html>
