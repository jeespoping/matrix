<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Calculo Costos Variables Historicos (T112)</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro115.php Ver. 2017-11-23</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro115.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wper1)  or !isset($wper2) or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2 or !isset($wemp) or $wemp == "Seleccione")
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTACION</td></tr>";
			echo "<tr><td align=center colspan=2>CALCULO COSTOS VARIABLES HISTORICOS (T122)</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Base de Presupuestaci&oacute;n</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
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
			$wemp=substr($wemp,0,strpos($wemp,"-"));
			$wanob = $wanop + 1;
			$query = "SELECT Cierre_Ppto from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanob;
			$query = $query."    and mes = 0 ";
			$query = $query."    and Emp = '".$wemp."' ";
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if($num > 0 and $row[0] == "on")
			{
				$k=0;
				$query = "SELECT Mcvcco, Mcvcpr, Mcvtip  from ".$empresa."_000112 ";
				$query = $query."  where Mcvano = ".$wanop;
				$query = $query."    and Mcvemp = '".$wemp."' ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					if($row[1] == "200" and $row[2] == "0")
					{
						$wind=0;
						$wpcm1 = 0;
						$query = "SELECT sum(Mecval) as suma1 from ".$empresa."_000026 ";
						$query = $query." where Meccco = '".$row[0]."'";
						$query = $query."   and Mecmes between ".$wper1." and ".$wper2;
						$query = $query."   and Mecano = ".$wanop;
						$query = $query."   and Mecemp = '".$wemp."' ";
						$query = $query."   and Meccpr = '200'";
						$err2 = mysql_query($query,$conex);
						$num2 = mysql_num_rows($err2);
						if ($num2 > 0)
						{
							$row2 = mysql_fetch_array($err2);
							$wpcm1 =$row2[0];
						}
						$wpcm2 = 0;
						$query = "SELECT sum(Mioinp) as suma1 from ".$empresa."_000063,".$empresa."_000060 ";
						$query = $query." where Miocco = '".$row[0]."'";
						$query = $query."   and Mioano = ".$wanop;
						$query = $query."   and Mioemp = '".$wemp."' ";
						$query = $query."   and Miomes between ".$wper1." and ".$wper2;
						$query = $query."   and Miocfa = Cfacod ";
						$query = $query."   and Mioemp = Cfaemp ";
						$query = $query."   and Cfaclas = '06' ";
						$err2 = mysql_query($query,$conex);
						$num2 = mysql_num_rows($err2);
						if ($num2 > 0)
						{
							$row2 = mysql_fetch_array($err2);
							$wpcm2 =$row2[0];
						}
						if ($wpcm2 != 0)
							$wind = $wpcm1 / $wpcm2 * 100;
						else
							$wind=0;
						$query = "update ".$empresa."_000112 set Mcvval=".$wind."  where Mcvano=".$wanop." and Mcvcco='".$row[0]."' and Mcvcpr='".$row[1]."' and Mcvtip='".$row[2]."' and Mcvemp='".$wemp."'";
						$err2 = mysql_query($query,$conex);
						$k++;
						echo "REGISTRO ACTUALIZADO CPR 200 : ".$k."<br>";
					}
					if ($row[1] == "217" or $row[1] == "235")
					{
						$wind1=0;
						$query = "SELECT sum(Mecval)  from ".$empresa."_000026 ";
						$query = $query."  where Mecano = ".$wanop;
						$query = $query."    and Mecemp = '".$wemp."' ";
						$query = $query."    and Mecmes between ".$wper1." and  ".$wper2;
						$query = $query."    and Meccco = '".$row[0]."'";
						$query = $query."    and Meccpr = '".$row[1]."'";
						$err1 = mysql_query($query,$conex);
						$row1 = mysql_fetch_array($err1);
						if($row1[0] > 0)
							$wind1=$row1[0];
						$wind2=0;
						$query = "SELECT sum(Morcan)  from ".$empresa."_000032 ";
						$query = $query."  where Morano = ".$wanop;
						$query = $query."    and Moremp = '".$wemp."' ";
						$query = $query."    and Mormes between ".$wper1." and  ".$wper2;
						$query = $query."    and Morcco = '".$row[0]."'";
						$query = $query."    and Morcco not in (select ccocod from ".$empresa."_000005 where ccouni = '2H' and ccoemp = '".$wemp."')";
						$query = $query."    and Mortip=  'P' ";
						$query = $query." union ";
						$query = $query." SELECT sum(Morcan)  from ".$empresa."_000032 ";
						$query = $query."  where Morano = ".$wanop;
						$query = $query."    and Moremp = '".$wemp."' ";
						$query = $query."    and Mormes between ".$wper1." and  ".$wper2;
						$query = $query."    and Morcco = '".$row[0]."'";
						$query = $query."    and Morcco  in (select ccocod from ".$empresa."_000005 where ccouni = '2H' and ccoemp = '".$wemp."')";
						$query = $query."    and Morcod =  '12' ";
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						for ($j=0;$j<$num1;$j++)
						{
							$row1 = mysql_fetch_array($err1);
							if($row1[0] != 0)
								$wind2=$row1[0];
						}
						if($wind2 != 0)
							$wind = $wind1 / $wind2;
						else
							$wind = 0;	
						$query = "update ".$empresa."_000112 set Mcvval=".$wind."  where Mcvano=".$wanop." and Mcvcco='".$row[0]."' and Mcvcpr='".$row[1]."' and Mcvtip='".$row[2]."' and Mcvemp='".$wemp."'";
						$err2 = mysql_query($query,$conex);
						$k++;
						echo "REGISTRO ACTUALIZADO CPR 217 y 235 : ".$k."<br>";
					}
					if($row[1] == "202" or $row[1] == "257" or $row[1] == "218" or $row[1] == "248" or $row[1] == "250" or ($row[1] == "200" and $row[2] == "1"))
					{
						$wind1=0;
						$query = "SELECT sum(Mecval)  from ".$empresa."_000026 ";
						$query = $query."  where Mecano = ".$wanop;
						$query = $query."    and Mecemp = '".$wemp."' ";
						$query = $query."    and Mecmes between ".$wper1." and  ".$wper2;
						$query = $query."    and Meccco = '".$row[0]."'";
						$query = $query."    and Meccpr = '".$row[1]."'";
						$err1 = mysql_query($query,$conex);
						$row1 = mysql_fetch_array($err1);
						if($row1[0] > 0)
							$wind1=$row1[0];
						$wind2=0;
						$query = "SELECT sum(Mecval)  from ".$empresa."_000026 ";
						$query = $query."  where Mecano = ".$wanop;
						$query = $query."    and Mecemp = '".$wemp."' ";
						$query = $query."    and Mecmes between ".$wper1." and  ".$wper2;
						$query = $query."    and Meccco = '".$row[0]."'";
						$query = $query."    and Meccpr = '100' ";
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						for ($j=0;$j<$num1;$j++)
						{
							$row1 = mysql_fetch_array($err1);
							if($row1[0] != 0)
								$wind2=$row1[0];
						}
						if($wind2 != 0)
							$wind = $wind1 / $wind2 * 100;
						else
							$wind = 0;	
						$query = "update ".$empresa."_000112 set Mcvval=".$wind."  where Mcvano=".$wanop." and Mcvcco='".$row[0]."' and Mcvcpr='".$row[1]."' and Mcvtip='".$row[2]."' and Mcvemp='".$wemp."'";
						$err2 = mysql_query($query,$conex);
						$k++;
						echo "REGISTRO ACTUALIZADO 202 o 257 o 200 Y TIP 1  : ".$k."<br>";
					}
					if($row[0] == "1082" and $row[1] == "200" and $row[2] == "2")
					{
						$wind1=0;
						$query = "SELECT sum(Mecval)  from ".$empresa."_000026 ";
						$query = $query."  where Mecano = ".$wanop;
						$query = $query."    and Mecemp = '".$wemp."' ";
						$query = $query."    and Mecmes between ".$wper1." and  ".$wper2;
						$query = $query."    and Meccco = '".$row[0]."'";
						$query = $query."    and Meccpr = '".$row[1]."'";
						$err1 = mysql_query($query,$conex);
						$row1 = mysql_fetch_array($err1);
						if($row1[0] > 0)
							$wind1=$row1[0];
						$wind2=0;
						$query = "SELECT sum(Mecval)  from ".$empresa."_000026 ";
						$query = $query."  where Mecano = ".$wanop;
						$query = $query."    and Mecemp = '".$wemp."' ";
						$query = $query."    and Mecmes between ".$wper1." and  ".$wper2;
						$query = $query."    and Meccpr = '100' ";
						$query = $query."    and Meccco >= '1000'";
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						for ($j=0;$j<$num1;$j++)
						{
							$row1 = mysql_fetch_array($err1);
							if($row1[0] != 0)
								$wind2=$row1[0];
						}
						if($wind2 != 0)
							$wind = $wind1 / $wind2 * 100;
						else
							$wind = 0;	
						$query = "update ".$empresa."_000112 set Mcvval=".$wind."  where Mcvano=".$wanop." and Mcvcco='".$row[0]."' and Mcvcpr='".$row[1]."' and Mcvtip='".$row[2]."' and Mcvemp='".$wemp."'";
						$err2 = mysql_query($query,$conex);
						$k++;
						echo "REGISTRO ACTUALIZADO  200 Y TIP 2 : ".$k."<br>";
					}
					if($row[0] == "2048" and ($row[1] == "364" or $row[1] == "600" ) and $row[2] == "0")
					{
						$wind1=0;
						$query = "SELECT sum(Mecval)  from ".$empresa."_000026 ";
						$query = $query."  where Mecano = ".$wanop;
						$query = $query."    and Mecemp = '".$wemp."' ";
						$query = $query."    and Mecmes between ".$wper1." and  ".$wper2;
						$query = $query."    and Meccpr = '".$row[1]."'";
						$query = $query."    and Meccco >= '1000'";
						$err1 = mysql_query($query,$conex);
						$row1 = mysql_fetch_array($err1);
						if($row1[0] > 0)
							$wind1=$row1[0];
						$wind2=0;
						$query = "SELECT sum(Mecval)  from ".$empresa."_000026 ";
						$query = $query."  where Mecano = ".$wanop;
						$query = $query."    and Mecemp = '".$wemp."' ";
						$query = $query."    and Mecmes between ".$wper1." and  ".$wper2;
						$query = $query."    and Meccpr = '100' ";
						$query = $query."    and Meccco >= '1000'";
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						for ($j=0;$j<$num1;$j++)
						{
							$row1 = mysql_fetch_array($err1);
							if($row1[0] != 0)
								$wind2=$row1[0];
						}
						if($wind2 != 0)
							$wind = $wind1 / $wind2 * 100;
						else
							$wind = 0;	
						$query = "update ".$empresa."_000112 set Mcvval=".$wind."  where Mcvano=".$wanop." and Mcvcco='".$row[0]."' and Mcvcpr='".$row[1]."' and Mcvtip='".$row[2]."' and Mcvemp='".$wemp."'";
						$err2 = mysql_query($query,$conex);
						$k++;
						echo "REGISTRO ACTUALIZADO CPR 364 o 600 : ".$k."<br>";
					}
					if($row[1] == "750" and $row[2] == "0")
					{
						$wind1=0;
						$query = "SELECT sum(Mecval)  from ".$empresa."_000026 ";
						$query = $query."  where Mecano = ".$wanop;
						$query = $query."    and Mecemp = '".$wemp."' ";
						$query = $query."    and Mecmes between ".$wper1." and  ".$wper2;
						$query = $query."    and Meccco = '".$row[0]."'";
						$query = $query."    and Meccpr = '750'";
						$err1 = mysql_query($query,$conex);
						$row1 = mysql_fetch_array($err1);
						if($row1[0] != 0)
							$wind1=$row1[0];
						$wind2=0;
						$query = "SELECT sum(Mecval)  from ".$empresa."_000026 ";
						$query = $query."  where Mecano = ".$wanop;
						$query = $query."    and Mecemp = '".$wemp."' ";
						$query = $query."    and Mecmes between ".$wper1." and  ".$wper2;
						$query = $query."    and Meccco = '".$row[0]."'";
						$query = $query."    and Meccpr = '100' ";
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						for ($j=0;$j<$num1;$j++)
						{
							$row1 = mysql_fetch_array($err1);
							if($row1[0] != 0)
								$wind2=$row1[0];
						}
						if($wind2 != 0)
							$wind = $wind1 / $wind2 * 100;
						else
							$wind = 0;	
						$query = "update ".$empresa."_000112 set Mcvval=".$wind."  where Mcvano=".$wanop." and Mcvcco='".$row[0]."' and Mcvcpr='".$row[1]."' and Mcvtip='".$row[2]."' and Mcvemp='".$wemp."'";
						$err2 = mysql_query($query,$conex);
						$k++;
						echo "REGISTRO ACTUALIZADO CRP 750 : ".$k."<br>";
					}
					if($row[0] == "2048" and $row[1] == "295" and $row[2] == "0")
					{
						$wind1=0;
						$query = "SELECT sum(Mecval)  from ".$empresa."_000026 ";
						$query = $query."  where Mecano = ".$wanop;
						$query = $query."    and Mecemp = '".$wemp."' ";
						$query = $query."    and Mecmes between ".$wper1." and  ".$wper2;
						$query = $query."    and Meccpr = '".$row[1]."'";
						$query = $query."    and Meccco >= '1000'";
						$err1 = mysql_query($query,$conex);
						$row1 = mysql_fetch_array($err1);
						if($row1[0] != 0)
							$wind1=$row1[0];
						$wind2=0;
						$query = "SELECT sum(Mecval)  from ".$empresa."_000026 ";
						$query = $query."  where Mecano = ".$wanop;
						$query = $query."    and Mecemp = '".$wemp."' ";
						$query = $query."    and Mecmes between ".$wper1." and  ".$wper2;
						$query = $query."    and Meccpr = '201' ";
						$query = $query."    and Meccco >= '1000'";
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						for ($j=0;$j<$num1;$j++)
						{
							$row1 = mysql_fetch_array($err1);
							if($row1[0] != 0)
								$wind2=$row1[0];
						}
						if($wind2 != 0)
							$wind = $wind1 / $wind2 * 100;
						else
							$wind = 0;	
						$query = "update ".$empresa."_000112 set Mcvval=".$wind."  where Mcvano=".$wanop." and Mcvcco='".$row[0]."' and Mcvcpr='".$row[1]."' and Mcvtip='".$row[2]."' and Mcvemp='".$wemp."'";
						$err2 = mysql_query($query,$conex);
						$k++;
						echo "REGISTRO ACTUALIZADO CPR 295 : ".$k."<br>";
					}
					if($row[0] == "2048" and $row[1] == "395" and $row[2] == "0")
					{
						$wind1=0;
						$query = "SELECT sum(Mecval)  from ".$empresa."_000026 ";
						$query = $query."  where Mecano = ".$wanop;
						$query = $query."    and Mecemp = '".$wemp."' ";
						$query = $query."    and Mecmes between ".$wper1." and  ".$wper2;
						$query = $query."    and Meccpr = '".$row[1]."'";
						$query = $query."    and Meccco >= '1000'";
						$err1 = mysql_query($query,$conex);
						$row1 = mysql_fetch_array($err1);
						if($row1[0] != 0)
							$wind1=$row1[0];
						$wind2=0;
						$query = "SELECT sum(Mecval)  from ".$empresa."_000026 ";
						$query = $query."  where Mecano = ".$wanop;
						$query = $query."    and Mecemp = '".$wemp."' ";
						$query = $query."    and Mecmes between ".$wper1." and  ".$wper2;
						$query = $query."    and Meccpr = '301' ";
						$query = $query."    and Meccco >= '1000'";
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						for ($j=0;$j<$num1;$j++)
						{
							$row1 = mysql_fetch_array($err1);
							if($row1[0] != 0)
								$wind2=$row1[0];
						}
						if($wind2 != 0)
							$wind = $wind1 / $wind2 * 100;
						else
							$wind = 0;	
						$query = "update ".$empresa."_000112 set Mcvval=".$wind."  where Mcvano=".$wanop." and Mcvcco='".$row[0]."' and Mcvcpr='".$row[1]."' and Mcvtip='".$row[2]."' and Mcvemp='".$wemp."'";
						$err2 = mysql_query($query,$conex);
						$k++;
						echo "REGISTRO ACTUALIZADO CPR 395  : ".$k."<br>";
					}
					if($row[0] == "2048" and $row[1] == "895" and $row[2] == "0")
					{
						$wind1=0;
						$query = "SELECT sum(Mecval)  from ".$empresa."_000026 ";
						$query = $query."  where Mecano = ".$wanop;
						$query = $query."    and Mecemp = '".$wemp."' ";
						$query = $query."    and Mecmes between ".$wper1." and  ".$wper2;
						$query = $query."    and Meccpr = '".$row[1]."'";
						$query = $query."    and Meccco >= '1000'";
						$err1 = mysql_query($query,$conex);
						$row1 = mysql_fetch_array($err1);
						if($row1[0] != 0)
							$wind1=$row1[0];
						$wind2=0;
						$query = "SELECT sum(Mecval)  from ".$empresa."_000026 ";
						$query = $query."  where Mecano = ".$wanop;
						$query = $query."    and Mecemp = '".$wemp."' ";
						$query = $query."    and Mecmes between ".$wper1." and  ".$wper2;
						$query = $query."    and Meccpr = '801' ";
						$query = $query."    and Meccco >= '1000'";
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						for ($j=0;$j<$num1;$j++)
						{
							$row1 = mysql_fetch_array($err1);
							if($row1[0] != 0)
								$wind2=$row1[0];
						}
						if($wind2 != 0)
							$wind = $wind1 / $wind2 * 100;
						else
							$wind = 0;	
						$query = "update ".$empresa."_000112 set Mcvval=".$wind."  where Mcvano=".$wanop." and Mcvcco='".$row[0]."' and Mcvcpr='".$row[1]."' and Mcvtip='".$row[2]."' and Mcvemp='".$wemp."'";
						$err2 = mysql_query($query,$conex);
						$k++;
						echo "REGISTRO ACTUALIZADO CPR 895 : ".$k."<br>";
					}
					if($row[0] == "1949" and $row[1] == "296" and $row[2] == "0")
					{
						$wind1=0;
						$query = "SELECT sum(Mecval)  from ".$empresa."_000026 ";
						$query = $query."  where Mecano = ".$wanop;
						$query = $query."    and Mecemp = '".$wemp."' ";
						$query = $query."    and Mecmes between ".$wper1." and  ".$wper2;
						$query = $query."    and Meccpr = '".$row[1]."'";
						$query = $query."    and Meccco >= '1000'";
						$err1 = mysql_query($query,$conex);
						$row1 = mysql_fetch_array($err1);
						if($row1[0] != 0)
							$wind1=$row1[0];
						$wind2=0;
						$query = "SELECT sum(Mecval)  from ".$empresa."_000026 ";
						$query = $query."  where Mecano = ".$wanop;
						$query = $query."    and Mecemp = '".$wemp."' ";
						$query = $query."    and Mecmes between ".$wper1." and  ".$wper2;
						$query = $query."    and Meccpr = '100' ";
						$query = $query."    and Meccco >= '1000'";
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						for ($j=0;$j<$num1;$j++)
						{
							$row1 = mysql_fetch_array($err1);
							if($row1[0] != 0)
								$wind2=$row1[0];
						}
						if($wind2 != 0)
							$wind = $wind1 / $wind2 * 100;
						else
							$wind = 0;	
						$query = "update ".$empresa."_000112 set Mcvval=".$wind."  where Mcvano=".$wanop." and Mcvcco='".$row[0]."' and Mcvcpr='".$row[1]."' and Mcvtip='".$row[2]."' and Mcvemp='".$wemp."'";
						$err2 = mysql_query($query,$conex);
						$k++;
						echo "REGISTRO ACTUALIZADO CPR 296  : ".$k."<br>";
					}
					if($row[0] == "1949" and $row[1] == "102" and $row[2] == "0")
					{
						$wind1=0;
						$query = "SELECT sum(Mecval)  from ".$empresa."_000026 ";
						$query = $query."  where Mecano = ".$wanop;
						$query = $query."    and Mecemp = '".$wemp."' ";
						$query = $query."    and Mecmes between ".$wper1." and  ".$wper2;
						$query = $query."    and Meccpr = '".$row[1]."'";
						$query = $query."    and Meccco >= '1000'";
						$err1 = mysql_query($query,$conex);
						$row1 = mysql_fetch_array($err1);
						if($row1[0] != 0)
							$wind1=$row1[0];
						$wind2=0;
						$query = "SELECT sum(Mecval)  from ".$empresa."_000026 ";
						$query = $query."  where Mecano = ".$wanop;
						$query = $query."    and Mecemp = '".$wemp."' ";
						$query = $query."    and Mecmes between ".$wper1." and  ".$wper2;
						$query = $query."    and Meccpr = '100' ";
						$query = $query."    and Meccco >= '1000'";
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						for ($j=0;$j<$num1;$j++)
						{
							$row1 = mysql_fetch_array($err1);
							if($row1[0] != 0)
								$wind2=$row1[0];
						}
						if($wind2 != 0)
							$wind = $wind1 / $wind2 * 100;
						else
							$wind = 0;	
						$query = "update ".$empresa."_000112 set Mcvval=".$wind."  where Mcvano=".$wanop." and Mcvcco='".$row[0]."' and Mcvcpr='".$row[1]."' and Mcvtip='".$row[2]."' and Mcvemp='".$wemp."'";
						$err2 = mysql_query($query,$conex);
						$k++;
						echo "REGISTRO ACTUALIZADO CPR 102 : ".$k."<br>";
					}
					if($row[0] == "1949" and $row[1] == "112" and $row[2] == "0")
					{
						$wind1=0;
						$query = "SELECT sum(Mecval)  from ".$empresa."_000026 ";
						$query = $query."  where Mecano = ".$wanop;
						$query = $query."    and Mecemp = '".$wemp."' ";
						$query = $query."    and Mecmes between ".$wper1." and  ".$wper2;
						$query = $query."    and Meccpr = '".$row[1]."'";
						$query = $query."    and Meccco >= '1000'";
						$err1 = mysql_query($query,$conex);
						$row1 = mysql_fetch_array($err1);
						if($row1[0] != 0)
							$wind1=$row1[0];
						$wind2=0;
						$query = "SELECT sum(Mecval)  from ".$empresa."_000026 ";
						$query = $query."  where Mecano = ".$wanop;
						$query = $query."    and Mecemp = '".$wemp."' ";
						$query = $query."    and Mecmes between ".$wper1." and  ".$wper2;
						$query = $query."    and Meccpr = '100' ";
						$query = $query."    and Meccco >= '1000'";
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						for ($j=0;$j<$num1;$j++)
						{
							$row1 = mysql_fetch_array($err1);
							if($row1[0] != 0)
								$wind2=$row1[0];
						}
						if($wind2 != 0)
							$wind = $wind1 / $wind2 * 100;
						else
							$wind = 0;	
						$query = "update ".$empresa."_000112 set Mcvval=".$wind."  where Mcvano=".$wanop." and Mcvcco='".$row[0]."' and Mcvcpr='".$row[1]."' and Mcvtip='".$row[2]."' and Mcvemp='".$wemp."'";
						$err2 = mysql_query($query,$conex);
						$k++;
						echo "REGISTRO ACTUALIZADO CPR 112 : ".$k."<br>";
					}
					if($row[0] == "1949" and $row[1] == "103" and $row[2] == "0")
					{
						$wind1=0;
						$query = "SELECT sum(Mecval)  from ".$empresa."_000026 ";
						$query = $query."  where Mecano = ".$wanop;
						$query = $query."    and Mecemp = '".$wemp."' ";
						$query = $query."    and Mecmes between ".$wper1." and  ".$wper2;
						$query = $query."    and Meccpr = '".$row[1]."'";
						$query = $query."    and Meccco >= '1000'";
						$err1 = mysql_query($query,$conex);
						$row1 = mysql_fetch_array($err1);
						if($row1[0] != 0)
							$wind1=$row1[0];
						$wind2=0;
						$query = "SELECT sum(Mecval)  from ".$empresa."_000026 ";
						$query = $query."  where Mecano = ".$wanop;
						$query = $query."    and Mecemp = '".$wemp."' ";
						$query = $query."    and Mecmes between ".$wper1." and  ".$wper2;
						$query = $query."    and Meccpr = '100' ";
						$query = $query."    and Meccco >= '1000'";
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						for ($j=0;$j<$num1;$j++)
						{
							$row1 = mysql_fetch_array($err1);
							if($row1[0] != 0)
								$wind2=$row1[0];
						}
						if($wind2 != 0)
							$wind = $wind1 / $wind2 * 100;
						else
							$wind = 0;	
						$query = "update ".$empresa."_000112 set Mcvval=".$wind."  where Mcvano=".$wanop." and Mcvcco='".$row[0]."' and Mcvcpr='".$row[1]."' and Mcvtip='".$row[2]."' and Mcvemp='".$wemp."'";
						$err2 = mysql_query($query,$conex);
						$k++;
						echo "REGISTRO ACTUALIZADO CPR 103 :  ".$k."<br>";
					}
					if($row[0] == "1949" and $row[1] == "113" and $row[2] == "0")
					{
						$wind1=0;
						$query = "SELECT sum(Mecval)  from ".$empresa."_000026 ";
						$query = $query."  where Mecano = ".$wanop;
						$query = $query."    and Mecemp = '".$wemp."' ";
						$query = $query."    and Mecmes between ".$wper1." and  ".$wper2;
						$query = $query."    and Meccpr = '".$row[1]."'";
						$query = $query."    and Meccco >= '1000'";
						$err1 = mysql_query($query,$conex);
						$row1 = mysql_fetch_array($err1);
						if($row1[0] != 0)
							$wind1=$row1[0];
						$wind2=0;
						$query = "SELECT sum(Mecval)  from ".$empresa."_000026 ";
						$query = $query."  where Mecano = ".$wanop;
						$query = $query."    and Mecemp = '".$wemp."' ";
						$query = $query."    and Mecmes between ".$wper1." and  ".$wper2;
						$query = $query."    and Meccpr = '100' ";
						$query = $query."    and Meccco >= '1000'";
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						for ($j=0;$j<$num1;$j++)
						{
							$row1 = mysql_fetch_array($err1);
							if($row1[0] != 0)
								$wind2=$row1[0];
						}
						if($wind2 != 0)
							$wind = $wind1 / $wind2 * 100;
						else
							$wind = 0;	
						$query = "update ".$empresa."_000112 set Mcvval=".$wind."  where Mcvano=".$wanop." and Mcvcco='".$row[0]."' and Mcvcpr='".$row[1]."' and Mcvtip='".$row[2]."' and Mcvemp='".$wemp."'";
						$err2 = mysql_query($query,$conex);
						$k++;
						echo "REGISTRO ACTUALIZADO CPR 113 :  ".$k."<br>";
					}
					if($row[0] == "1949" and $row[1] == "104" and $row[2] == "0")
					{
						$wind1=0;
						$query = "SELECT sum(Mecval)  from ".$empresa."_000026 ";
						$query = $query."  where Mecano = ".$wanop;
						$query = $query."    and Mecemp = '".$wemp."' ";
						$query = $query."    and Mecmes between ".$wper1." and  ".$wper2;
						$query = $query."    and Meccpr = '".$row[1]."'";
						$query = $query."    and Meccco >= '1000'";
						$err1 = mysql_query($query,$conex);
						$row1 = mysql_fetch_array($err1);
						if($row1[0] != 0)
							$wind1=$row1[0];
						$wind2=0;
						$query = "SELECT sum(Mecval)  from ".$empresa."_000026 ";
						$query = $query."  where Mecano = ".$wanop;
						$query = $query."    and Mecemp = '".$wemp."' ";
						$query = $query."    and Mecmes between ".$wper1." and  ".$wper2;
						$query = $query."    and Meccpr = '100' ";
						$query = $query."    and Meccco >= '1000'";
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						for ($j=0;$j<$num1;$j++)
						{
							$row1 = mysql_fetch_array($err1);
							if($row1[0] != 0)
								$wind2=$row1[0];
						}
						if($wind2 != 0)
							$wind = $wind1 / $wind2 * 100;
						else
							$wind = 0;	
						$query = "update ".$empresa."_000112 set Mcvval=".$wind."  where Mcvano=".$wanop." and Mcvcco='".$row[0]."' and Mcvcpr='".$row[1]."' and Mcvtip='".$row[2]."' and Mcvemp='".$wemp."'";
						$err2 = mysql_query($query,$conex);
						$k++;
						echo "REGISTRO ACTUALIZADO CPR 104 : ".$k."<br>";
					}
					if($row[0] == "1949" and $row[1] == "425" and $row[2] == "0")
					{
						$wind1=0;
						$query = "SELECT sum(Mecval)  from ".$empresa."_000026 ";
						$query = $query."  where Mecano = ".$wanop;
						$query = $query."    and Mecemp = '".$wemp."' ";
						$query = $query."    and Mecmes between ".$wper1." and  ".$wper2;
						$query = $query."    and Meccpr = '".$row[1]."'";
						$query = $query."    and Meccco >= '1000'";
						$err1 = mysql_query($query,$conex);
						$row1 = mysql_fetch_array($err1);
						if($row1[0] != 0)
							$wind1=$row1[0];
						$wind2=0;
						$query = "SELECT sum(Mecval)  from ".$empresa."_000026 ";
						$query = $query."  where Mecano = ".$wanop;
						$query = $query."    and Mecemp = '".$wemp."' ";
						$query = $query."    and Mecmes between ".$wper1." and  ".$wper2;
						$query = $query."    and Meccpr = '100' ";
						$query = $query."    and Meccco >= '1000'";
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						for ($j=0;$j<$num1;$j++)
						{
							$row1 = mysql_fetch_array($err1);
							if($row1[0] != 0)
								$wind2=$row1[0];
						}
						if($wind2 != 0)
							$wind = $wind1 / $wind2 * 100;
						else
							$wind = 0;	
						$query = "update ".$empresa."_000112 set Mcvval=".$wind."  where Mcvano=".$wanop." and Mcvcco='".$row[0]."' and Mcvcpr='".$row[1]."' and Mcvtip='".$row[2]."' and Mcvemp='".$wemp."'";
						$err2 = mysql_query($query,$conex);
						$k++;
						echo "REGISTRO ACTUALIZADO CPR 425 : ".$k."<br>";
					}
				}
				echo "<B>TOTAL REGISTROS ACTUALIZADOS : ".$k."</B>";
			}
			else
			{
				echo "<center><table border=0 aling=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>EL PERIODO  ESTA CERRADO !! -- NO SE PUEDE EJECUTAR ESTE PROCESO</MARQUEE></FONT>";
				echo "<br><br>";			
			}
		}
	}
?>
</body>
</html>
