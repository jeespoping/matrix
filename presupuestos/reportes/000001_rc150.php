<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Codigos Facturados Sin Costear (T108)</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc150.php Ver. 2017-08-24</b></font></tr></td></table>
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
			//echo " Medio : ".$lm." valor: ".$d[$lm][$i]."<br>";
			$val=strncmp(strtoupper($k),strtoupper($d[$lm][$i]),20);
			//if(strtoupper($k) == strtoupper($d[$lm][$i]))
			if($val == 0)
				return $lm;
			elseif($val < 0)
					$ls=$lm;
				else
					$li=$lm;
			//if($k == "10750739126F")
				//echo $val." ".$k." ".$d[$li][$i]." ".$d[$ls][$i]." ".$d[$lm][$i]." ".$li." ".$ls." ".$lm."<br>";
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

@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc150.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1) or !isset($wperc) or !isset($wcco1) or !isset($wcco2) or $wper1 < 1 or $wper1 > 12 or !isset($wper2) or $wper2 < 1 or $wper2 > 12 or $wperc < 1 or $wperc > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>CODIGOS FACTURADOS SIN COSTEAR (T108)</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Año de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco2' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes De Costeo</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wperc' size=2 maxlength=2></td></tr>";
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
			$query  = "select mprcco, mprcon, mprpro, Mprgru, mprtip from  ".$empresa."_000095 where mprcco between '".$wcco1."' and '".$wcco2."' and mpremp = '".$wemp."'  "; 
			$query .= " group by 1,2,3 ";
			$query .= " order by 1,2,3 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$t95=array();
			if ($num>0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$t95[$i][0]=$row[0].$row[1].$row[2];
					$t95[$i][1]=$row[3];
					$t95[$i][2]=$row[4];
				}
			}
			$tott95=$num;
			
			$query  = "select Pcacco,Pcagru,Pcacod,Pcacon from  ".$empresa."_000097 where Pcaano=".$wanop." and Pcames=".$wperc." and Pcacco between '".$wcco1."' and '".$wcco2."' and pcaemp = '".$wemp."' "; 
			$query .= " group by 1,2,3,4 ";
			$query .= " order by 1,2,3,4 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$t97=array();
			if ($num>0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					while (strlen($row[1]) < 4)
						$row[1]=$row[1]." ";
					while (strlen($row[2]) < 6)
						$row[2]=$row[2]." ";
					$t97[$i][0]=$row[0].$row[1].$row[2].$row[3];
				}
			}
			$tott97=$num;
			
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=8><font size=2>PROMOTORA MEDICA LAS AMERICAS S.A.</font></td></tr>";
			echo "<tr><td align=center colspan=8><font size=2>DIRECCION DE INFORMATICA</font></td></tr>";
			echo "<tr><td align=center colspan=8><font size=2>CODIGOS FACTURADOS SIN COSTEAR (T108)</font></td></tr>";
			echo "<tr><td align=center colspan=8><font size=2>EMPRESA : ".$wempt."</font></td></tr>";
			echo "<tr><td align=center bgcolor=#999999 colspan=8><font size=2><b>AÑO : ".$wanop." MES ".$wper1." - ".$wper2."</b></font></td></tr>";
			echo "<tr><td bgcolor=#CCCCCC align=center><b>ITEM</b></td><td bgcolor=#CCCCCC align=center><b>CCO</b></td><td bgcolor=#CCCCCC align=center><b>CONCEPTO</b></td><td bgcolor=#CCCCCC align=center><b>NOM. CONCEPTO</b></td><td bgcolor=#CCCCCC align=center><b>CODIGO</b></td><td bgcolor=#CCCCCC align=center><b>DESCRIPCION</b></td><td bgcolor=#CCCCCC align=center><b>GRUPO</b></td><td bgcolor=#CCCCCC align=center><b>CANTIDAD</b></td></tr>";
			//                  0      1      2       3        
			$query  = "select Moscco,Moscon,Mospro,sum(Moscan)  from ".$empresa."_000108,".$empresa."_000005  ";
			$query .= "    where Mosano = ".$wanop; 
			$query .= "  	 and Mosmes between ".$wper1." and ".$wper2;
			$query .= "  	 and Moscco between '".$wcco1."' and '".$wcco2."'";
			$query .= "  	 and Moscon not in ('0616','0626','0168','0169')  ";
			$query .= "  	 and Moscco = Ccocod "; 
			$query .= "  	 and Ccoemp = '".$wemp."'";  
			$query .= "  	 and Ccocos = 'S' "; 
			$query .= "  group by 1,2,3 ";
			$query .= "  Order by 1,4 desc ";
 			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			$lin=0;
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$pos=bi($t95,$tott95,$row[0].$row[1].$row[2],0);
				if($pos != -1)
				{
					if($t95[$pos][2] == "C" or $t95[$pos][2] == "P" or $t95[$pos][2] == "O")
					{
						$posG=$pos;
						while (strlen($t95[$pos][1]) < 4)
							$t95[$pos][1]=$t95[$pos][1]." ";
						while (strlen($row[2]) < 6)
							$row[2]=$row[2]." ";
						$pos=bi($t97,$tott97,$row[0].$t95[$pos][1].$row[2].$row[1],0);
						if($pos == -1)
						{
							$lin++;
							if($lin % 2 == 0)
								$color="#99CCFF";
							else
								$color="#ffffff";
							
							$query = "select Cfades  from ".$empresa."_000060 ";
							$query .= "  where Cfacod = '".$row[1]."'";
							$query .= "    and Cfaemp = '".$wemp."'"; 
				 			$err1 = mysql_query($query,$conex);
							$num1 = mysql_num_rows($err1);
							if($num1 > 0)
							{
								$row1 = mysql_fetch_array($err1);
								$desc=$row1[0];
							}
							else
								$desc="";
							$query = "select Exanom  from ".$empresa."_000117,".$empresa."_000005 ";
							$query .= "  where Exacod ='".$row[2]."' ";
							$query .= "    and Exaemp = '".$wemp."'"; 
							$query .= "    and Ccocod ='".$row[0]."' ";
							$query .= "    and Exatip = Ccotip ";
							$query .= "    and Exaemp = Ccoemp ";
				 			$err1 = mysql_query($query,$conex);
							$num1 = mysql_num_rows($err1);
							if($num1 > 0)
							{
								$row1 = mysql_fetch_array($err1);
								$deso=$row1[0];
							}
							else
								$deso="";
							echo "<tr><td bgcolor=#dddddd><font size=2>".$lin."</font></td><td bgcolor=".$color."><font size=2>".$row[0]."</font></td><td bgcolor=".$color."><font size=2>".$row[1]."</font></td><td bgcolor=".$color."><font size=2>".$desc."</font></td><td bgcolor=".$color."><font size=2>".$row[2]."</font></td><td bgcolor=".$color."><font size=2>".$deso."</font></td><td bgcolor=".$color."><font size=2>".$t95[$posG][1]."</font></td><td bgcolor=".$color." align=right><font size=2>".number_format((double)$row[3],2,'.',',')."</font></td></tr>";
						}
					}
				}
				else
				{	
					$lin++;				
					if($lin % 2 == 0)
						$color="#99CCFF";
					else
						$color="#ffffff";
					
					$query = "select Cfades  from ".$empresa."_000060 ";
					$query .= "  where Cfacod = '".$row[1]."'";
					$query .= "    and Cfaemp = '".$wemp."'"; 
		 			$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					if($num1 > 0)
					{
						$row1 = mysql_fetch_array($err1);
						$desc=$row1[0];
					}
					else
						$desc="";
					$query = "select Exanom  from ".$empresa."_000117,".$empresa."_000005 ";
					$query .= "  where Exacod ='".$row[2]."' ";
					$query .= "    and Exaemp = '".$wemp."'"; 
					$query .= "    and Ccocod ='".$row[0]."' ";
					$query .= "    and Exatip = Ccotip ";
					$query .= "    and Exaemp = Ccoemp ";
		 			$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					if($num1 > 0)
					{
						$row1 = mysql_fetch_array($err1);
						$deso=$row1[0];
					}
					else
						$deso="";
					echo "<tr><td bgcolor=#dddddd><font size=2>".$lin."</font></td><td bgcolor=".$color."><font size=2>".$row[0]."</font></td><td bgcolor=".$color."><font size=2>".$row[1]."</font></td><td bgcolor=".$color."><font size=2>".$desc."</font></td><td bgcolor=".$color."><font size=2>".$row[2]."</font></td><td bgcolor=".$color."><font size=2>".$deso."</font></td><td bgcolor=".$color."><font size=2></font></td><td bgcolor=".$color." align=right><font size=2>".number_format((double)$row[3],2,'.',',')."</font></td></tr>";
				}
    		}
    		echo "</table></center>";
		}
	}
?>
</body>
</html>
