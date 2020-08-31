<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<script type="text/javascript">
<!--
	function enter()
	{
		document.forms.rc17.submit();
	}
//-->
</script>
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Procedimientos Comparativos Entre A&Ntilde;os X Unidad</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc17.php Ver. 2017-06-21</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form name= 'rc17' action='000001_rc17.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1)  or !isset($wper2) or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>PROCEDIMIENTOS COMPARATIVOS ENTRE A&Ntilde;OS X UNIDAD</td></tr>";
			if($key=="costosyp" or (isset($call) and $call == "SIG") or (isset($call) and $call == "SIC"))
			{
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
			}
			else
			{
				echo "<tr><td bgcolor=#cccccc align=center>Empresa de  Proceso</td><td bgcolor=#cccccc align=center>";
				$query = "SELECT Empcod,Empdes  from ".$empresa."_000153,".$empresa."_000125 where empleado = '".$key."' and empresa = Empcod group by 1 order by Empcod";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					echo "<select name='wemp' OnChange='enter()'>";
					echo "<option>Seleccione</option>";
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						if(isset($wemp) and substr($wemp,0,strpos($wemp,"-")) == $row[0])
							echo "<option selected>".$row[0]."-".$row[1]."</option>";
						else
							echo "<option>".$row[0]."-".$row[1]."</option>";
					}
					echo "</select>";
				}
				echo "</td></tr>";
			}
			echo "<tr><td bgcolor=#cccccc align=center>A&Ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			if($key != "costosyp" and (!isset($call) or $call != "SIC"))
			{
				echo "<tr><td bgcolor=#cccccc align=center>Unidad Proceso</td><td bgcolor=#cccccc align=center>";
				if(isset($wemp) and $wemp != "Seleccione")
				{
					$query = "SELECT Cc,cconom  from ".$empresa."_000125,".$empresa."_000005 where empleado ='".$key."'  and cc=ccocod and Empresa = '".substr($wemp,0,strpos($wemp,"-"))."' group by 1 order by Cc";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					if ($num>0)
					{
						echo "<select name='wccof'>";
						for ($i=0;$i<$num;$i++)
						{
							$row = mysql_fetch_array($err);
							echo "<option>".$row[0]."-".$row[1]."</option>";
						}
						echo "</select>";
					}
				}
				echo "</td></tr>";
			}
			echo "<tr><td bgcolor=#cccccc align=center>Tipo de Estadistica</td><td bgcolor=#cccccc align=center>";
			echo "<select name='wtie'>";
			echo "<option>P-Principal</option>";
			echo "<option>S-Secundario</option>";
			echo "<option>T-Todos</option>";
			echo "</select>";
			echo "</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Tipo de Reporte</td><td bgcolor=#cccccc align=center>";
			echo "<select name='wtir'>";
			echo "<option>D-Detallado</option>";
			echo "<option>R-Resumido</option>";
			echo "</select>";
			echo "</td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$wempt = $wemp;
			$wemp = substr($wemp,0,2);
			$wtien = substr($wtie,2);
			$wtirn = substr($wtir,2);
			$wtie = substr($wtie,0,1);
			$wtir = substr($wtir,0,1);
			$wcco = substr($wccof,0,strpos($wccof,"-"));
			$query = "SELECT cierre_real from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and mes = ".$wper2;
			$query = $query."    and Emp = '".$wemp."' ";
			
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if($num > 0 and $row[0] == "on")
			{
				$wmeses = $wper2 - $wper1 + 1;
				$wanopa = $wanop - 1;
				switch ($wtir)
				{
					case "D":
						//                  0      1     2      3      4           5
						$query = "SELECT Morcco,Cconom,Morcod,Prodes,Mortip,sum(Morcan) from ".$empresa."_000032,".$empresa."_000059,".$empresa."_000005 ";
						$query = $query."  where Morano = ".$wanopa;
						$query = $query."    and moremp = '".$wemp."' ";
						$query = $query."    and Mormes between ".$wper1." and ".$wper2;
						if($wtie == "P" or $wtie == "S")
							$query = $query."    and Mortip = '".$wtie."' ";
						$query = $query."    and Morcod = Procod ";
						$query = $query."    and moremp = proemp ";
						if(isset($wcco) and $wcco != "")
							$query = $query."    and Morcco = '".$wcco."' ";
						$query = $query."    and Morcco = Ccocod ";
						$query = $query."    and moremp = Ccoemp ";
						$query = $query." group by 1,2,3,4,5 ";
						$query = $query." order by 1,3 ";
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						$query = "SELECT Morcco,Cconom,Morcod,Prodes,Mortip,sum(Morcan) from ".$empresa."_000032,".$empresa."_000059,".$empresa."_000005 ";
						$query = $query."  where Morano = ".$wanop;
						$query = $query."    and moremp = '".$wemp."' ";
						$query = $query."    and Mormes between ".$wper1." and ".$wper2;
						if($wtie == "P" or $wtie == "S")
							$query = $query."    and Mortip = '".$wtie."' ";
						$query = $query."    and Morcod = Procod ";
						$query = $query."    and moremp = proemp ";
						if(isset($wcco) and $wcco != "")
							$query = $query."    and Morcco = '".$wcco."' ";
						$query = $query."    and Morcco = Ccocod ";
						$query = $query."    and moremp = Ccoemp ";
						$query = $query." group by 1,2,3,4,5 ";
						$query = $query." order by 1,3 ";
					
						$err2 = mysql_query($query,$conex);
						$num2 = mysql_num_rows($err2);
						echo "<center><table border=1>";
						echo "<tr><td colspan=9 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
						echo "<tr><td colspan=9 align=center>DIRECCION DE INFORMATICA</td></tr>";
						echo "<tr><td colspan=9 align=center>PROCEDIMIENTOS COMPARATIVOS ENTRE A&Ntilde;OS X UNIDAD</td></tr>";
						echo "<tr><td colspan=9 align=center>EMPRESA : ".$wempt."</td></tr>";
						if(isset($wccof))
							echo "<tr><td colspan=9 align=center>CENTRO DE COSTOS : ".$wccof."</td></tr>";
						echo "<tr><td colspan=9 align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. " A&Ntilde;O ANTERIOR : ".$wanopa." A&Ntilde;O ACTUAL : ".$wanop."</td></tr>";
						echo "<tr><td colspan=9 align=center>TIPO DE REPORTE : ".$wtirn." TIPO DE ESTADISTICA : ".$wtien."</td></tr>";
						echo "<tr><td bgcolor=#cccccc><b>CENTRO DE<BR>COSTOS</b></td><td bgcolor=#cccccc><b>NOMBRE<BR>CENTRO DE COSTOS</b></td><td bgcolor=#cccccc><b>TIPO<BR>ESTADISTICA</b></td><td bgcolor=#cccccc><b>PROCEDIMIENTO</b></td><td bgcolor=#cccccc><b>NOMBRE<BR>PROCEDIMIENTO</b></td><td align=right bgcolor=#cccccc><b>CANTIDAD : ".$wanopa."</b></td><td align=right bgcolor=#cccccc><b>CANTIDAD : ".$wanop."</b></td><td align=right bgcolor=#cccccc><b>VARIACION</b></td><td align=right bgcolor=#cccccc><b>DIFERENCIA</b></td></tr>";
						$wdata=array();
						$k1=0;
						$k2=0;
						$num=-1;
						if ($num1 ==  0)
						{
							$key1='zzzzzzz';
							$k1=1;
						}
						else
						{
							$row1 = mysql_fetch_array($err1);
							$key1= $row1[0].$row1[2];
							$k1++;
						}
						if ($num2 ==  0)
						{
							$key2='zzzzzzz';
							$k2=1;
						}
						else
						{
							$row2 = mysql_fetch_array($err2);
							$key2= $row2[0].$row2[2];
							$k2++;
						}
						while ($k1 <= $num1 or $k2 <= $num2)
						{
							if($key1 == $key2)
							{
								$num++;
								$wdata[$num][0]=$row1[0];
								if($row1[2] == "26" or $row1[2] == "27" or $row1[2] == "34")
								{
									$row1[5]=$row1[5]/$wmeses;
									$row2[5]=$row2[5]/$wmeses;
								}
								$wdata[$num][1]=$row1[1];
								$wdata[$num][2]=$row1[2];
								$wdata[$num][3]=$row1[3];
								$wdata[$num][4]=$row1[4];
								$wdata[$num][5]=$row1[5];
								$wdata[$num][6]=$row2[5];
								if($row1[5] - 1 > 0)
									$wdata[$num][7]=($row2[5]/$row1[5] - 1) * 100;
								else
									$wdata[$num][7]=0;
								$wdata[$num][8]=$row2[5] - $row1[5];
								$k1++;
								$k2++;
								if($k1 > $num1)
									$key1="zzzzzzz";
								else
								{
									$row1 = mysql_fetch_array($err1);
									$key1= $row1[0].$row1[2];
								}
								if($k2 > $num2)
									$key2="zzzzzzz";
								else
								{
									$row2 = mysql_fetch_array($err2);
									$key2= $row2[0].$row2[2];
								}
							}
							elseif($key1 < $key2)
								{
									$num++;
									$wdata[$num][0]=$row1[0];
									if($row1[2] == "26" or $row1[2] == "27" or $row1[2] == "34")
										$row1[5]=$row1[5]/$wmeses;
									$wdata[$num][1]=$row1[1];
									$wdata[$num][2]=$row1[2];
									$wdata[$num][3]=$row1[3];
									$wdata[$num][4]=$row1[4];
									$wdata[$num][5]=$row1[5];
									$wdata[$num][6]=0;
									if($row1[5] - 1 > 0)
										$wdata[$num][7]=(0/$row1[5] - 1) * 100;
									else
										$wdata[$num][7]=0;
									$wdata[$num][8]=0 - $row1[5];
									$k1++;
									if($k1 > $num1)
										$key1="zzzzzzz";
									else
									{
										$row1 = mysql_fetch_array($err1);
										$key1= $row1[0].$row1[2];
									}
								}
								else
								{
									$num++;
									$wdata[$num][0]=$row2[0];
									if($row1[2] == "26" or $row1[2] == "27" or $row1[2] == "34")
										$row2[5]=$row2[5]/$wmeses;
									$wdata[$num][1]=$row2[1];
									$wdata[$num][2]=$row2[2];
									$wdata[$num][3]=$row2[3];
									$wdata[$num][4]=$row2[4];
									$wdata[$num][5]=0;
									$wdata[$num][6]=$row2[5];
									$wdata[$num][7]=0;
									$wdata[$num][8]=$row2[5];
									$k2++;
									if($k2 > $num2)
										$key2="zzzzzzz";
									else
									{
										$row2 = mysql_fetch_array($err2);
										$key2= $row2[0].$row2[2];
									}
								}
					}
					for ($i=0;$i<=$num;$i++)
					{
						echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td>".$wdata[$i][4]."</td><td>".$wdata[$i][2]."</td><td>".$wdata[$i][3]."</td><td align=right>".number_format((double)$wdata[$i][5],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][6],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][7],2,'.',',')." %</td><td align=right>".number_format((double)$wdata[$i][8],0,'.',',')."</td></tr>";
					}
					echo "</table></center>";
				break;
				case "R":
					//                     0      1        2        
						$query = "SELECT Morcco,Cconom,sum(Morcan) from ".$empresa."_000032,".$empresa."_000005 ";
						$query = $query."  where Morano = ".$wanopa;
						$query = $query."    and moremp = '".$wemp."' ";
						$query = $query."    and Mormes between ".$wper1." and ".$wper2;
						$query = $query."    and Mortip = 'P' ";
						$query = $query."    and Morcco = Ccocod ";
						$query = $query."    and moremp = Ccoemp ";
						$query = $query." group by 1,2 ";
						$query = $query." order by 1 ";
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						$query = "SELECT Morcco,Cconom,sum(Morcan) from ".$empresa."_000032,".$empresa."_000005 ";
						$query = $query."  where Morano = ".$wanop;
						$query = $query."    and moremp = '".$wemp."' ";
						$query = $query."    and Mormes between ".$wper1." and ".$wper2;
						$query = $query."    and Mortip = 'P' ";
						$query = $query."    and Morcco = Ccocod ";
						$query = $query."    and moremp = Ccoemp ";
						$query = $query." group by 1,2 ";
						$query = $query." order by 1 ";
						$err2 = mysql_query($query,$conex);
						$num2 = mysql_num_rows($err2);
						echo "<center></center><table border=1>";
						echo "<tr><td colspan=6 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
						echo "<tr><td colspan=6 align=center>DIRECCION DE INFORMATICA</td></tr>";
						echo "<tr><td colspan=6 align=center>PROCEDIMIENTOS COMPARATIVOS ENTRE A&Ntilde;OS X UNIDAD</td></tr>";
						echo "<tr><td colspan=9 align=center>EMPRESA : ".$wempt."</td></tr>";
						echo "<tr><td colspan=6 align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. " A&Ntilde;O ANTERIOR : ".$wanopa." A&Ntilde;O ACTUAL : ".$wanop."</td></tr>";
						echo "<tr><td colspan=6 align=center>TIPO DE REPORTE : ".$wtirn." TIPO DE ESTADISTICA : ".$wtien."</td></tr>";
						echo "<tr><td bgcolor=#cccccc><b>CENTRO DE<BR>COSTOS</b></td><td bgcolor=#cccccc><b>NOMBRE<BR>CENTRO DE COSTOS</b></td><td align=right bgcolor=#cccccc><b>CANTIDAD : ".$wanopa."</b></td><td align=right bgcolor=#cccccc><b>CANTIDAD : ".$wanop."</b></td><td align=right bgcolor=#cccccc><b>VARIACION</b></td><td align=right bgcolor=#cccccc><b>DIFERENCIA</b></td></tr>";
						$wdata=array();
						$k1=0;
						$k2=0;
						$num=-1;
						if ($num1 ==  0)
						{
							$key1='zzzz';
							$k1=1;
						}
						else
						{
							$row1 = mysql_fetch_array($err1);
							$key1= $row1[0];
							$k1++;
						}
						if ($num2 ==  0)
						{
							$key2='zzzz';
							$k2=1;
						}
						else
						{
							$row2 = mysql_fetch_array($err2);
							$key2= $row2[0];
							$k2++;
						}
						while ($k1 <= $num1 or $k2 <= $num2)
						{
							if($key1 == $key2)
							{
								$num++;
								$wdata[$num][0]=$row1[0];
								$wdata[$num][1]=$row1[1];
								$wdata[$num][2]=$row1[2];
								$wdata[$num][3]=$row2[2];
								if($row1[2] - 1 > 0)
									$wdata[$num][4]=($row2[2]/$row1[2] - 1) * 100;
								else
									$wdata[$num][4]=0;
								$wdata[$num][5]=$row2[2] - $row1[2];
								$k1++;
								$k2++;
								if($k1 > $num1)
									$key1="zzzz";
								else
								{
									$row1 = mysql_fetch_array($err1);
									$key1= $row1[0];
								}
								if($k2 > $num2)
									$key2="zzzz";
								else
								{
									$row2 = mysql_fetch_array($err2);
									$key2= $row2[0];
								}
							}
							elseif($key1 < $key2)
								{
									$num++;
									$wdata[$num][0]=$row1[0];
									$wdata[$num][1]=$row1[1];
									$wdata[$num][2]=$row1[2];
									$wdata[$num][3]=0;
									if($row1[2] - 1 > 0)
										$wdata[$num][4]=(0/$row1[2] - 1) * 100;
									else
										$wdata[$num][4]=0;
									$wdata[$num][5]=0 - $row1[2];
									$k1++;
									if($k1 > $num1)
										$key1="zzzz";
									else
									{
										$row1 = mysql_fetch_array($err1);
										$key1= $row1[0];
									}
								}
								else
								{
									$num++;
									$wdata[$num][0]=$row2[0];
									$wdata[$num][1]=$row2[1];
									$wdata[$num][2]=0;
									$wdata[$num][3]=$row2[2];
									$wdata[$num][4]=0;
									$wdata[$num][5]=$row2[2];
									$k2++;
									if($k2 > $num2)
										$key2="zzzz";
									else
									{
										$row2 = mysql_fetch_array($err2);
										$key2= $row2[0];
									}
								}
					}
					for ($i=0;$i<=$num;$i++)
					{
						echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][4],2,'.',',')." %</td><td align=right>".number_format((double)$wdata[$i][4],0,'.',',')."</td></tr>";
					}
					echo "</table></center>";
				break;
			}
		}
		else
		{
			echo "<center><table border=0 aling=center>";
			echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
			echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>EL PERIODO NO!! ESTA CERRADO -- LLAME A COSTOS Y PRSUPUESTOS</MARQUEE></FONT>";
			echo "<br><br>";			
		}
		}
	}
?>
</body>
</html>
