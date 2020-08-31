<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Informe de Costos x Subgasto y Subproceso</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc143.php Ver. 2016-03-10</b></font></tr></td></table>
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
			//echo $k." ".$d[$li][$i]." ".$d[$ls][$i]." ".$d[$lm][$i]." ".$li." ".$ls." ".$lm."<br>";
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
		

		

		echo "<form action='000001_rc143.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1) or $wper1 < 1 or $wper1 > 12 or !isset($wcco1) or !isset($wgas) or ($wgas == "0-SELECCIONE" and $wgasn == '0') or !isset($wsub) or ($wsub == "0-SELECCIONE" and $wsubn == '0'))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>INFORME DE COSTOS X SUBGASTO Y SUBPROCESO</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Centro Costos</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc>Gasto</td><td bgcolor=#cccccc><input type='TEXT' name='wgasn' size=3 maxlength=10 value='0'>";
			$query = "SELECT Cogcod, Cognom  from ".$empresa."_000079 order by Cogcod ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wgas'>";
				echo "<option>0-SELECCIONE</option>";
				echo "<option>*-TODOS</option>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				echo "</select>";
			}
			echo "</td></tr>";
			echo "<tr><td bgcolor=#cccccc>Suproceso</td><td bgcolor=#cccccc><input type='TEXT' name='wsubn' size=3 maxlength=10 value='0'>";
			$query = "SELECT Subcod, Subdes  from ".$empresa."_000104 order by Subcod ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wsub'>";
				echo "<option>0-SELECCIONE</option>";
				echo "<option>*-TODOS</option>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				echo "</select>";
			}
			echo "</td></tr>";
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
			if($wgasn == '0')
				$wgas=substr($wgas,0,strpos($wgas,"-"));
			else
				$wgas=$wgasn;
			if($wsubn == '0')
				$wsub=substr($wsub,0,strpos($wsub,"-"));
			else
				$wsub=$wsubn;
			$dins=array();
			$query = "SELECT  Inscod, Insdes from ".$empresa."_000089 where Insemp = '".$wemp."' ";
			$query .= "     ORDER BY  Inscod ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$totins=$num;
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$dins[$i][0]=$row[0];
				$dins[$i][1]=$row[1];
			}
			$dnit=array();
			$query = "select Expnit,Expnte from ".$empresa."_000011 where Expemp = '".$wemp."' ";
		    //$query .= "  where expano = ".$wanop; 
			//$query .= "    and expper = ".$wper1; 
			$query .= "   group by expnit ";
			$query .= "   order by expnit ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$totnit=$num;
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$dnit[$i][0]=$row[0];
				$dnit[$i][1]=$row[1];
			}
			$dind=array();
			$query = "select Cogcod, Cognom  from ".$empresa."_000079 ";
			$query .= "   order by Cogcod ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$totind=$num;
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$dind[$i][0]=$row[0];
				$dind[$i][1]=$row[1];
			}
			$dotr=array();
			$query = "select Almcod, Almdes from ".$empresa."_000002 where Almemp = '".$wemp."' ";
		    //$query .= "  where Almano = ".$wanop; 
			//$query .= "    and Almmes = ".$wper1; 
			$query .= "   group by Almcod ";
			$query .= "   order by Almcod ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$tototr=$num;
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$dotr[$i][0]=$row[0];
				$dotr[$i][1]=$row[1];
			}
			
			if($wgas != "201" and $wgas != "203")
			{
				if($wgas != "*" and $wsub != "*")
				{
					$WTIPO=0;
					echo "<center><table border=0>";
					echo "<tr><td align=center colspan=10><font size=2>PROMOTORA MEDICA LAS AMERICAS S.A.</font></td></tr>";
					echo "<tr><td align=center colspan=10><font size=2>DIRECCION DE INFORMATICA</font></td></tr>";
					echo "<tr><td align=center colspan=10><font size=2>INFORME DE COSTOS X SUBGASTO Y SUBPROCESO</font></td></tr>";
					echo "<tr><td colspan=10 align=center>EMPRESA : ".$wempt."</td></tr>";
					echo "<tr><td align=center bgcolor=#999999 colspan=10><font size=2><b>A&Ntilde;O : ".$wanop." MES : ".$wper1."</b></font></td></tr>";
					echo "<tr><td align=center bgcolor=#999999 colspan=10><font size=2><b>GASTO : ".$wgas." SUBPROCESO : ".$wsub."</b></font></td></tr>";
					echo "<tr><td bgcolor=#CCCCCC align=center><b>SUBGASTO</b></td><td bgcolor=#CCCCCC align=center><b>DESCRIPCION</b></td><td bgcolor=#CCCCCC align=center><b>COSTO</b></td></tr>";
					//                  0                     1      
					$query  = "select Rcdsga,(Mgaval * Rcdpor / 100 * Mdrpor)  from ".$empresa."_000101,".$empresa."_000005,".$empresa."_000091,".$empresa."_000092 ";
					$query .= " where rcdano =  ".$wanop;
					$query .= "   and rcdemp = '".$wemp."' ";
					$query .= "   and rcdmes = ".$wper1;  
					$query .= "   and rcdcco = '".$wcco1."'"; 
					$query .= "   and rcdgas = '".$wgas."'"; 
					$query .= "   and Rcdtip = 'D' ";
					$query .= "   and rcdcco = ccocod ";
					$query .= "   and rcdemp = ccoemp ";
					$query .= "   and rcdemp = Mdremp ";
					$query .= "   and rcdano = Mdrano ";
					$query .= "   and rcdmes = Mdrmes ";
					$query .= "   and rcdcco = Mdrcco ";
					$query .= "   and Rcddri = Mdrcod ";
					$query .= "   and Mdrsub = '".$wsub."'";
					$query .= "   and rcdemp = Mgaemp ";
					$query .= "   and rcdano = Mgaano ";
					$query .= "   and rcdmes = Mgaper "; 
					$query .= "   and rcdcco = Mgacco ";
					$query .= "   and rcdgas = Mgagas ";
					$query .= "   and rcdsga = Mgasga ";
					$query .= "  UNION ALL  ";
					$query .= "  select Rcdsga,(Mgaval * Rcdpor / 100) from ".$empresa."_000101,".$empresa."_000005,".$empresa."_000092  ";
					$query .= "  where rcdano = ".$wanop;
					$query .= "    and rcdemp = '".$wemp."' ";
					$query .= "    and rcdmes = ".$wper1;  
					$query .= "    and rcdcco = '".$wcco1."'"; 
					$query .= "    and rcdgas = '".$wgas."'"; 
					$query .= "    and Rcddri = '".$wsub."'";
					$query .= "    and Rcdtip = 'S' ";
					$query .= "    and rcdcco = ccocod ";
					$query .= "    and rcdemp = ccoemp ";
					$query .= "    and rcdemp = Mgaemp ";
					$query .= "    and rcdano = Mgaano ";
					$query .= "    and rcdmes = Mgaper ";
					$query .= "    and rcdcco = Mgacco ";
					$query .= "    and rcdgas = Mgagas ";
					$query .= "    and rcdsga = Mgasga ";
					$query .= "  order by 1 ";
				}
				elseif($wgas == "*" and $wsub != "*")
					{
						$WTIPO=1;
						echo "<center><table border=0>";
						echo "<tr><td align=center colspan=11><font size=2>PROMOTORA MEDICA LAS AMERICAS S.A.</font></td></tr>";
						echo "<tr><td align=center colspan=11><font size=2>DIRECCION DE INFORMATICA</font></td></tr>";
						echo "<tr><td align=center colspan=11><font size=2>INFORME DE COSTOS X SUBGASTO Y SUBPROCESO</font></td></tr>";
						echo "<tr><td colspan=11 align=center>EMPRESA : ".$wempt."</td></tr>";
						echo "<tr><td align=center bgcolor=#999999 colspan=11><font size=2><b>A&Ntilde;O : ".$wanop." MES : ".$wper1."</b></font></td></tr>";
						echo "<tr><td align=center bgcolor=#999999 colspan=11><font size=2><b>GASTO : ".$wgas." SUBPROCESO : ".$wsub."</b></font></td></tr>";
						echo "<tr><td bgcolor=#CCCCCC align=center><b>GASTO</b></td><td bgcolor=#CCCCCC align=center><b>SUBGASTO</b></td><td bgcolor=#CCCCCC align=center><b>DESCRIPCION</b></td><td bgcolor=#CCCCCC align=center><b>COSTO</b></td></tr>";
						//                  0       1              2      
						$query  = "select rcdgas,Rcdsga,(Mgaval * Rcdpor / 100 * Mdrpor)  from ".$empresa."_000101,".$empresa."_000005,".$empresa."_000091,".$empresa."_000092 ";
						$query .= " where rcdano =  ".$wanop;
						$query .= "   and rcdemp = '".$wemp."' ";
						$query .= "   and rcdmes = ".$wper1;  
						$query .= "   and rcdcco = '".$wcco1."'"; 
						$query .= "   and Rcdtip = 'D' ";
						$query .= "   and rcdcco = ccocod ";
						$query .= "   and rcdemp = ccoemp ";
						$query .= "   and rcdemp = Mdremp ";
						$query .= "   and rcdano = Mdrano ";
						$query .= "   and rcdmes = Mdrmes ";
						$query .= "   and rcdcco = Mdrcco ";
						$query .= "   and Rcddri = Mdrcod ";
						$query .= "   and Mdrsub = '".$wsub."'";
						$query .= "   and rcdemp = Mgaemp ";
						$query .= "   and rcdano = Mgaano ";
						$query .= "   and rcdmes = Mgaper "; 
						$query .= "   and rcdcco = Mgacco ";
						$query .= "   and rcdgas = Mgagas ";
						$query .= "   and rcdsga = Mgasga ";
						$query .= "  UNION ALL  ";
						$query .= "  select rcdgas,Rcdsga,(Mgaval * Rcdpor / 100) from ".$empresa."_000101,".$empresa."_000005,".$empresa."_000092  ";
						$query .= "  where rcdano = ".$wanop;
						$query .= "    and rcdemp = '".$wemp."' ";
						$query .= "    and rcdmes = ".$wper1;  
						$query .= "    and rcdcco = '".$wcco1."'"; 
						$query .= "    and Rcddri = '".$wsub."'";
						$query .= "    and Rcdtip = 'S' ";
						$query .= "    and rcdcco = ccocod ";
						$query .= "    and rcdemp = ccoemp ";
						$query .= "    and rcdemp = Mgaemp ";
						$query .= "    and rcdano = Mgaano ";
						$query .= "    and rcdmes = Mgaper ";
						$query .= "    and rcdcco = Mgacco ";
						$query .= "    and rcdgas = Mgagas ";
						$query .= "    and rcdsga = Mgasga ";
						$query .= "  order by 1,2 ";
					}
					elseif($wgas != "*" and $wsub == "*")
						{
							$WTIPO=1;
							echo "<center><table border=0>";
							echo "<tr><td align=center colspan=11><font size=2>PROMOTORA MEDICA LAS AMERICAS S.A.</font></td></tr>";
							echo "<tr><td align=center colspan=11><font size=2>DIRECCION DE INFORMATICA</font></td></tr>";
							echo "<tr><td align=center colspan=11><font size=2>INFORME DE COSTOS X SUBGASTO Y SUBPROCESO</font></td></tr>";
							echo "<tr><td colspan=11 align=center>EMPRESA : ".$wempt."</td></tr>";
							echo "<tr><td align=center bgcolor=#999999 colspan=11><font size=2><b>A&Ntilde;O : ".$wanop." MES : ".$wper1."</b></font></td></tr>";
							echo "<tr><td align=center bgcolor=#999999 colspan=11><font size=2><b>GASTO : ".$wgas." SUBPROCESO : ".$wsub."</b></font></td></tr>";
							echo "<tr><td bgcolor=#CCCCCC align=center><b>SUPPROCESO</b></td><td bgcolor=#CCCCCC align=center><b>SUBGASTO</b></td><td bgcolor=#CCCCCC align=center><b>DESCRIPCION</b></td><td bgcolor=#CCCCCC align=center><b>COSTO</b></td></tr>";
							//                  0       1              2     
							$query  = "select Mdrsub,Rcdsga,(Mgaval * Rcdpor / 100 * Mdrpor)  from ".$empresa."_000101,".$empresa."_000005,".$empresa."_000091,".$empresa."_000092 ";
							$query .= " where rcdano =  ".$wanop;
							$query .= "   and rcdemp = '".$wemp."' ";
							$query .= "   and rcdmes = ".$wper1;  
							$query .= "   and rcdcco = '".$wcco1."'"; 
							$query .= "   and rcdgas = '".$wgas."'"; 
							$query .= "   and Rcdtip = 'D' ";
							$query .= "   and rcdcco = ccocod ";
							$query .= "   and rcdemp = ccoemp ";
							$query .= "   and rcdemp = Mdremp ";
							$query .= "   and rcdano = Mdrano ";
							$query .= "   and rcdmes = Mdrmes ";
							$query .= "   and rcdcco = Mdrcco ";
							$query .= "   and Rcddri = Mdrcod ";
							$query .= "   and rcdemp = Mgaemp ";
							$query .= "   and rcdano = Mgaano ";
							$query .= "   and rcdmes = Mgaper "; 
							$query .= "   and rcdcco = Mgacco ";
							$query .= "   and rcdgas = Mgagas ";
							$query .= "   and rcdsga = Mgasga ";
							$query .= "  UNION ALL  ";
							$query .= "  select Rcddri,Rcdsga,(Mgaval * Rcdpor / 100) from ".$empresa."_000101,".$empresa."_000005,".$empresa."_000092  ";
							$query .= "  where rcdano = ".$wanop;
							$query .= "  and rcdmes = ".$wper1;  
							$query .= "  and rcdcco = '".$wcco1."'"; 
							$query .= "  and rcdgas = '".$wgas."'"; 
							$query .= "  and Rcdtip = 'S' ";
							$query .= "  and rcdcco = ccocod ";
							$query .= "  and rcdemp = ccoemp ";
							$query .= "  and rcdemp = Mgaemp ";
							$query .= "  and rcdano = Mgaano ";
							$query .= "  and rcdmes = Mgaper ";
							$query .= "  and rcdcco = Mgacco ";
							$query .= "  and rcdgas = Mgagas ";
							$query .= "  and rcdsga = Mgasga ";
							$query .= "  order by 1,2 ";
						}
						elseif($wgas == "*" and $wsub == "*")
							{
								$WTIPO=2;
								echo "<center><table border=0>";
								echo "<tr><td align=center colspan=12><font size=2>PROMOTORA MEDICA LAS AMERICAS S.A.</font></td></tr>";
								echo "<tr><td align=center colspan=12><font size=2>DIRECCION DE INFORMATICA</font></td></tr>";
								echo "<tr><td align=center colspan=12><font size=2>INFORME DE COSTOS X SUBGASTO Y SUBPROCESO</font></td></tr>";
								echo "<tr><td colspan=12 align=center>EMPRESA : ".$wempt."</td></tr>";
								echo "<tr><td align=center bgcolor=#999999 colspan=12><font size=2><b>A&Ntilde;O : ".$wanop." MES : ".$wper1."</b></font></td></tr>";
								echo "<tr><td align=center bgcolor=#999999 colspan=12><font size=2><b>GASTO : ".$wgas." SUBPROCESO : ".$wsub."</b></font></td></tr>";
								echo "<tr><td bgcolor=#CCCCCC align=center><b>GASTO</b></td><td bgcolor=#CCCCCC align=center><b>SUBPROCESO</b></td><td bgcolor=#CCCCCC align=center><b>SUBGASTO</b></td><td bgcolor=#CCCCCC align=center><b>DESCRIPCION</b></td><td bgcolor=#CCCCCC align=center><b>COSTO</b></td></tr>";
								//                  0       1    2          3      
								$query  = "select rcdgas,Mdrsub,Rcdsga,(Mgaval * Rcdpor / 100 * Mdrpor)  from ".$empresa."_000101,".$empresa."_000005,".$empresa."_000091,".$empresa."_000092 ";
								$query .= " where rcdano =  ".$wanop;
								$query .= "   and rcdemp = '".$wemp."' ";
								$query .= "   and rcdmes = ".$wper1;  
								$query .= "   and rcdcco = '".$wcco1."'"; 
								$query .= "   and Rcdtip = 'D' ";
								$query .= "   and rcdcco = ccocod ";
								$query .= "   and rcdemp = ccoemp ";
								$query .= "   and rcdemp = Mdremp ";
								$query .= "   and rcdano = Mdrano ";
								$query .= "   and rcdmes = Mdrmes ";
								$query .= "   and rcdcco = Mdrcco ";
								$query .= "   and Rcddri = Mdrcod ";
								$query .= "   and rcdemp = Mgaemp ";
								$query .= "   and rcdano = Mgaano ";
								$query .= "   and rcdmes = Mgaper "; 
								$query .= "   and rcdcco = Mgacco ";
								$query .= "   and rcdgas = Mgagas ";
								$query .= "   and rcdsga = Mgasga ";
								$query .= "  UNION ALL  ";
								$query .= "  select rcdgas,Rcddri,Rcdsga,(Mgaval * Rcdpor / 100) from ".$empresa."_000101,".$empresa."_000005,".$empresa."_000092  ";
								$query .= "  where rcdano = ".$wanop;
								$query .= "    and rcdemp = '".$wemp."' ";
								$query .= "    and rcdmes = ".$wper1;  
								$query .= "    and rcdcco = '".$wcco1."'"; 
								$query .= "    and Rcdtip = 'S' ";
								$query .= "    and rcdcco = ccocod ";
								$query .= "    and rcdemp = ccoemp ";
								$query .= "    and rcdemp = Mgaemp ";
								$query .= "    and rcdano = Mgaano ";
								$query .= "    and rcdmes = Mgaper ";
								$query .= "    and rcdcco = Mgacco ";
								$query .= "    and rcdgas = Mgagas ";
								$query .= "    and rcdsga = Mgasga ";
								$query .= "  order by 1 ";
							}
				//echo $query."<br>";	
	 			$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				$ttotal=0;
				for ($i=0;$i<$num;$i++)
				{
					if($i % 2 == 0)
						$color="#99CCFF";
					else
						$color="#ffffff";
					$row = mysql_fetch_array($err);
					$pos=bi($dins,$totins,$row[$WTIPO],0);
					if($pos == -1)
					{
						$pos=bi($dnit,$totnit,$row[$WTIPO],0);
						if($pos == -1)
						{
							$pos=bi($dind,$totind,$row[$WTIPO],0);
							if($pos == -1)
							{
								$pos=bi($dotr,$tototr,$row[$WTIPO],0);
								if($pos == -1)
									$nomsubg="CODIGO DEL SUBGASTO NO DETERMINADO!!!";
								else
									$nomsubg=$dotr[$pos][1];
							}
							else
								$nomsubg=$dind[$pos][1];	
						}
						else
							$nomsubg=$dnit[$pos][1];
					}
					else
						$nomsubg=$dins[$pos][1]; 
					if($WTIPO==0)
					{
						$col=2;
						$ttotal += $row[1];
						echo "<tr><td bgcolor=".$color."><font size=2>".$row[0]."</font></td><td bgcolor=".$color."><font size=2>".$nomsubg."</font></td><td bgcolor=".$color." align=right><font size=2>".number_format($row[1],2,'.',',')."</font></td></tr>";
					}
					elseif($WTIPO==1)
						{
							$col=3;
							$ttotal += $row[2];
							echo "<tr><td bgcolor=".$color."><font size=2>".$row[0]."</font></td><td bgcolor=".$color."><font size=2>".$row[1]."</font></td><td bgcolor=".$color."><font size=2>".$nomsubg."</font></td><td bgcolor=".$color." align=right><font size=2>".number_format($row[2],2,'.',',')."</font></td></tr>";
						}
						else
						{
							$col=4;
							$ttotal += $row[3];
							echo "<tr><td bgcolor=".$color."><font size=2>".$row[0]."</font></td><td bgcolor=".$color."><font size=2>".$row[1]."</font></td><td bgcolor=".$color."><font size=2>".$row[2]."</font></td><td bgcolor=".$color."><font size=2>".$nomsubg."</font></td><td bgcolor=".$color." align=right><font size=2>".number_format($row[3],2,'.',',')."</font></td></tr>";
						}
	    		}
	    		echo "<tr><td bgcolor=#CCCCCC colspan=".$col." align=center><B>TOTAL GENERAL</B></td><td bgcolor=#CCCCCC align=right><font size=2><B>".number_format($ttotal,2,'.',',')."</B></font></td></tr>";
    		}
    		elseif($wgas == "201")
    			{
	    			if($wsub != "*")
					{
						$WTIPO=0;
						echo "<center><table border=0>";
						echo "<tr><td align=center colspan=10><font size=2>PROMOTORA MEDICA LAS AMERICAS S.A.</font></td></tr>";
						echo "<tr><td align=center colspan=10><font size=2>DIRECCION DE INFORMATICA</font></td></tr>";
						echo "<tr><td align=center colspan=10><font size=2>INFORME DE COSTOS X SUBGASTO Y SUBPROCESO</font></td></tr>";
						echo "<tr><td colspan=10 align=center>EMPRESA : ".$wempt."</td></tr>";
						echo "<tr><td align=center bgcolor=#999999 colspan=10><font size=2><b>A&Ntilde;O : ".$wanop." MES : ".$wper1."</b></font></td></tr>";
						echo "<tr><td align=center bgcolor=#999999 colspan=10><font size=2><b>SUBPROCESO : ".$wsub."</b></font></td></tr>";
		    			echo "<tr><td bgcolor=#CCCCCC align=center><b>OFICIO</b></td><td bgcolor=#CCCCCC align=center><b>DESCRIPCION</b></td><td bgcolor=#CCCCCC align=center><b>PORCENTAJE</b></td><td bgcolor=#CCCCCC align=center><b>COSTO</b></td></tr>";
		    			$query  = "select Mnoofi,Carnom, Pdipor * 100,(Mnopag * Pdipor) from ".$empresa."_000094,".$empresa."_000098,".$empresa."_000004 ";
						$query .= "  where Mnoano = ".$wanop;
						$query .= "    and Mnoemp = '".$wemp."' ";
						$query .= "    and Mnomes = ".$wper1;  
						$query .= "    and Mnocco = '".$wcco1."'";   
						$query .= "    and Mnoemp = Pdiemp  ";
						$query .= "    and Mnoano = Pdiano  ";
						$query .= "    and Mnomes = Pdimes  ";
						$query .= "    and Mnocco = Pdicco  ";
						$query .= "    and Mnoofi = Pdiofi  ";
						$query .= "    and Pdisub = '".$wsub."'"; 
						$query .= "    and Mnoofi = Carcod  ";
						$query .= "    and Mnoemp = Caremp  ";
						$query .= "  order by 1 ";
					}
					else
					{
						$WTIPO=1;
						echo "<center><table border=0>";
						echo "<tr><td align=center colspan=11><font size=2>PROMOTORA MEDICA LAS AMERICAS S.A.</font></td></tr>";
						echo "<tr><td align=center colspan=11><font size=2>DIRECCION DE INFORMATICA</font></td></tr>";
						echo "<tr><td align=center colspan=11><font size=2>INFORME DE COSTOS X SUBGASTO Y SUBPROCESO</font></td></tr>";
						echo "<tr><td colspan=11 align=center>EMPRESA : ".$wempt."</td></tr>";
						echo "<tr><td align=center bgcolor=#999999 colspan=11><font size=2><b>A&Ntilde;O : ".$wanop." MES : ".$wper1."</b></font></td></tr>";
						echo "<tr><td align=center bgcolor=#999999 colspan=11><font size=2><b>SUBPROCESO : ".$wsub."</b></font></td></tr>";
		    			echo "<tr><td bgcolor=#CCCCCC align=center><b>SUBPROCESO</b></td><td bgcolor=#CCCCCC align=center><b>OFICIO</b></td><td bgcolor=#CCCCCC align=center><b>DESCRIPCION</b></td><td bgcolor=#CCCCCC align=center><b>PORCENTAJE</b></td><td bgcolor=#CCCCCC align=center><b>COSTO</b></td></tr>";
		    			$query  = "select Pdisub,Mnoofi,Carnom, Pdipor * 100,(Mnopag * Pdipor) from ".$empresa."_000094,".$empresa."_000098,".$empresa."_000004 ";
						$query .= "  where Mnoano = ".$wanop;
						$query .= "    and Mnoemp = '".$wemp."' ";
						$query .= "    and Mnomes = ".$wper1;  
						$query .= "    and Mnocco = '".$wcco1."'";   
						$query .= "    and Mnoemp = Pdiemp  ";
						$query .= "    and Mnoano = Pdiano  ";
						$query .= "    and Mnomes = Pdimes  ";
						$query .= "    and Mnocco = Pdicco  ";
						$query .= "    and Mnoofi = Pdiofi  ";
						$query .= "    and Mnoofi = Carcod  ";
						$query .= "    and Mnoemp = Caremp  ";
						$query .= "  order by 1,2 ";
					}
					//echo $query."<br>";	
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					$ttotal=0;
					for ($i=0;$i<$num;$i++)
					{
						if($i % 2 == 0)
							$color="#99CCFF";
						else
							$color="#ffffff";
						$row = mysql_fetch_array($err);
						if($WTIPO==0)
						{
							$col=3;
							$ttotal += $row[3];
							echo "<tr><td bgcolor=".$color."><font size=2>".$row[0]."</font></td><td bgcolor=".$color."><font size=2>".$row[1]."</font></td><td bgcolor=".$color." align=right><font size=2>".number_format($row[2],2,'.',',')."%</font></td><td bgcolor=".$color." align=right><font size=2>".number_format($row[3],2,'.',',')."</font></td></tr>";
						}
						else
						{
							$col=4;
							$ttotal += $row[4];
							echo "<tr><td bgcolor=".$color."><font size=2>".$row[0]."</font></td><td bgcolor=".$color."><font size=2>".$row[1]."</font></td><td bgcolor=".$color."><font size=2>".$row[2]."</font></td><td bgcolor=".$color." align=right><font size=2>".number_format($row[3],2,'.',',')."%</font></td><td bgcolor=".$color." align=right><font size=2>".number_format($row[4],2,'.',',')."</font></td></tr>";
						}
	    			}
	    			echo "<tr><td bgcolor=#CCCCCC colspan=".$col." align=center><B>TOTAL GENERAL</B></td><td bgcolor=#CCCCCC align=right><font size=2><B>".number_format($ttotal,2,'.',',')."</B></font></td></tr>";
    			}
    			elseif($wgas == "203")
	    			{
		    			if($wsub != "*")
						{
							$WTIPO=0;
							echo "<center><table border=0>";
							echo "<tr><td align=center colspan=10><font size=2>PROMOTORA MEDICA LAS AMERICAS S.A.</font></td></tr>";
							echo "<tr><td align=center colspan=10><font size=2>DIRECCION DE INFORMATICA</font></td></tr>";
							echo "<tr><td align=center colspan=10><font size=2>INFORME DE COSTOS X SUBGASTO Y SUBPROCESO</font></td></tr>";
							echo "<tr><td colspan=10 align=center>EMPRESA : ".$wempt."</td></tr>";
							echo "<tr><td align=center bgcolor=#999999 colspan=10><font size=2><b>A&Ntilde;O : ".$wanop." MES : ".$wper1."</b></font></td></tr>";
							echo "<tr><td align=center bgcolor=#999999 colspan=10><font size=2><b>SUBPROCESO : ".$wsub."</b></font></td></tr>";
			    			echo "<tr><td bgcolor=#CCCCCC align=center><b>ACITVO</b></td><td bgcolor=#CCCCCC align=center><b>DESCRIPCION</b></td><td bgcolor=#CCCCCC align=center><b>PORCENTAJE</b></td><td bgcolor=#CCCCCC align=center><b>COSTO</b></td></tr>";
			    			$query  = "select Depcod,Acfdes,Dacpor*100,(Depvde * Dacpor) from ".$empresa."_000076,".$empresa."_000084,".$empresa."_000075 ";
							$query .= "  where Depano = ".$wanop; 
							$query .= "    and Depemp = '".$wemp."' ";
							$query .= "    and Depmes = ".$wper1;  
							$query .= "    and Depcco = '".$wcco1."'";   
							$query .= "    and Depemp = Dacemp  ";
							$query .= "    and Depcco = Daccco  ";
							$query .= "    and Depcod = Daccod  ";
							$query .= "    and Dacsub = '".$wsub."'"; 
							$query .= "    and Depcod = Acfcod  ";
							$query .= "    and Depemp = Acfemp  ";
							$query .= "  order by 1 ";
						}
						else
						{
							$WTIPO=1;
							echo "<center><table border=0>";
							echo "<tr><td align=center colspan=11><font size=2>PROMOTORA MEDICA LAS AMERICAS S.A.</font></td></tr>";
							echo "<tr><td align=center colspan=11><font size=2>DIRECCION DE INFORMATICA</font></td></tr>";
							echo "<tr><td align=center colspan=11><font size=2>INFORME DE COSTOS X SUBGASTO Y SUBPROCESO</font></td></tr>";
							echo "<tr><td colspan=11 align=center>EMPRESA : ".$wempt."</td></tr>";
							echo "<tr><td align=center bgcolor=#999999 colspan=11><font size=2><b>A&Ntilde;O : ".$wanop." MES : ".$wper1."</b></font></td></tr>";
							echo "<tr><td align=center bgcolor=#999999 colspan=11><font size=2><b>SUBPROCESO : ".$wsub."</b></font></td></tr>";
			    			echo "<tr><td bgcolor=#CCCCCC align=center><b>SUBPROCESO</b></td><td bgcolor=#CCCCCC align=center><b>ACITVO</b></td><td bgcolor=#CCCCCC align=center><b>DESCRIPCION</b></td><td bgcolor=#CCCCCC align=center><b>PORCENTAJE</b></td><td bgcolor=#CCCCCC align=center><b>COSTO</b></td></tr>";
			    			$query  = "select Dacsub,Depcod,Acfdes,Dacpor*100,(Depvde * Dacpor) from ".$empresa."_000076,".$empresa."_000084,".$empresa."_000075 ";
							$query .= "  where Depano = ".$wanop; 
							$query .= "    and Depemp = '".$wemp."' ";
							$query .= "    and Depmes = ".$wper1;  
							$query .= "    and Depcco = '".$wcco1."'"; 
							$query .= "    and Depemp = Dacemp  ";  
							$query .= "    and Depcco = Daccco  ";
							$query .= "    and Depcod = Daccod  ";
							$query .= "    and Depcod = Acfcod  ";
							$query .= "    and Depemp = Acfemp  ";
							$query .= "  order by 1,2 ";
						}
						//echo $query."<br>";	
						$err = mysql_query($query,$conex);
						$num = mysql_num_rows($err);
						$ttotal=0;
						for ($i=0;$i<$num;$i++)
						{
							if($i % 2 == 0)
								$color="#99CCFF";
							else
								$color="#ffffff";
							$row = mysql_fetch_array($err);
							if($WTIPO==0)
							{
								$col=3;
								$ttotal += $row[3];
								echo "<tr><td bgcolor=".$color."><font size=2>".$row[0]."</font></td><td bgcolor=".$color."><font size=2>".$row[1]."</font></td><td bgcolor=".$color." align=right><font size=2>".number_format($row[2],2,'.',',')."%</font></td><td bgcolor=".$color." align=right><font size=2>".number_format($row[3],2,'.',',')."</font></td></tr>";
							}
							else
							{
								$col=4;
								$ttotal += $row[4];
								echo "<tr><td bgcolor=".$color."><font size=2>".$row[0]."</font></td><td bgcolor=".$color."><font size=2>".$row[1]."</font></td><td bgcolor=".$color."><font size=2>".$row[2]."</font></td><td bgcolor=".$color." align=right><font size=2>".number_format($row[3],2,'.',',')."%</font></td><td bgcolor=".$color." align=right><font size=2>".number_format($row[4],2,'.',',')."</font></td></tr>";
							}
		    			}
		    			echo "<tr><td bgcolor=#CCCCCC colspan=".$col." align=center><B>TOTAL GENERAL</B></td><td bgcolor=#CCCCCC align=right><font size=2><B>".number_format($ttotal,2,'.',',')."</B></font></td></tr>";
	    			}
    		echo "</table></center>";
		}
	}
?>
</body>
</html>
