<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Generacion de Costos x Actividad Promedio Ponderado</font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro141.php Ver. 2017-06-16</b></font></td></tr></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro141.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wano1) or !isset($wemp) or $wemp == "Seleccione" or !isset($wcco1) or !isset($wcco2) or !isset($wmes1) or $wmes1 < 1 or $wmes1 > 12 or !isset($wano2) or !isset($wmes2) or $wmes2 < 1 or $wmes2 > 12 or !isset($wano3) or !isset($wmes3) or $wmes3 < 1 or $wmes3 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>GENERACION DE COSTOS X ACTIVIDAD PROMEDIO PONDERADO</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wano1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmes1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wano2' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmes2' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad de Proceso Inicial</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad de Proceso Final</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco2' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Grabaci&oacute;n</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wano3' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Grabaci&oacute;n</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmes3' size=2 maxlength=2></td></tr>";
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
			$wres=strtoupper ($wres);
			$query = "SELECT Cierre_costos from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wano2;
			$query = $query."    and Emp = '".$wemp."' ";
			$query = $query."    and mes =   ".$wmes2;
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if($num > 0 and $row[0] == "off")
			{
				$count=0;
				$query  = "Select Cxacco, Cxasub , sum(Gasval) ";
				$query .= "  from ".$empresa."_000083 ,".$empresa."_000087 ";
				$query .= "    where Cxaano between ".$wano1." and ".$wano2;
				$query .= "      and Cxaemp = '".$wemp."' ";
				$query .= " 	 and ((Cxames between 1 and 12 and Cxaano != ".$wano1." and Cxaano != ".$wano2.") ";
				$query .= " 	  or (Cxames >= ".$wmes1." and Cxaano = ".$wano1." and ".$wano1." != ".$wano2.") ";
				$query .= " 	  or (Cxames <= ".$wmes2." and Cxaano = ".$wano2." and ".$wano1." != ".$wano2.") "; 
				$query .= " 	  or (Cxames between ".$wmes1." and ".$wmes2." and ".$wano1." = ".$wano2.")) ";
				$query .= "  	 and Cxaest = 'on' ";
				$query .= "      and Cxacco between '".$wcco1."' and '".$wcco2."' ";
				$query .= "  	 and Cxaemp = Gasemp ";
				$query .= "  	 and Cxacco = Gascco ";
				$query .= "      and Cxaano = Gasano ";
				$query .= "      and Cxames = Gasmes ";
				$query .= " 	 and Cxasub = Gassub ";
				$query .= "    group  by  Cxacco, Cxasub "; 
				$query .= "    order  by  Cxacco, Cxasub ";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				$query  = "Select Cxacco, Cxasub , sum(Mdacan) ";
				$query .= "   from ".$empresa."_000083 ,".$empresa."_000090 ";
				$query .= "    where Cxaano between ".$wano1." and ".$wano2;
				$query .= "      and Cxaemp = '".$wemp."' ";
				$query .= " 	 and ((Cxames between 1 and 12 and Cxaano != ".$wano1." and Cxaano != ".$wano2.") ";
				$query .= " 	  or (Cxames >= ".$wmes1." and Cxaano = ".$wano1." and ".$wano1." != ".$wano2.") ";
				$query .= " 	  or (Cxames <= ".$wmes2." and Cxaano = ".$wano2." and ".$wano1." != ".$wano2.") "; 
				$query .= " 	  or (Cxames between ".$wmes1." and ".$wmes2." and ".$wano1." = ".$wano2.")) ";
				$query .= "  	 and Cxaest = 'on'  ";
				$query .= "      and Cxacco between '".$wcco1."' and '".$wcco2."' ";
				$query .= "  	 and Cxaemp = Mdaemp ";
				$query .= "  	 and Cxacco = Mdacco	 ";
				$query .= "  	 and Cxaano = Mdaano ";
				$query .= "  	 and Cxames = Mdames ";
				$query .= "  	 and Cxasub = Mdasub ";
				$query .= "    group  by  Cxacco, Cxasub "; 
				$query .= "    order  by  Cxacco, Cxasub ";
				$err2 = mysql_query($query,$conex);
				$num2 = mysql_num_rows($err2);
				$i=0;
				$j=0;
				$k=0;
				$data=array();
				if($num1>0)
				{
					$i++;
					$row1 = mysql_fetch_array($err1);
					$sub1 = $row1[1];
					while(strlen($sub1) < 5)
						$sub1 = $sub1."z";
					$kl1=$row1[0].$sub1;
				}
				else
				{
					$kl1="zzzzzzzzz";
					$i=1;
				}
				if($num2>0)
				{
					$j++;
					$row2 = mysql_fetch_array($err2);
					$sub2 = $row2[1];
					while(strlen($sub2) < 5)
						$sub2 = $sub2."z";
					$kl2=$row2[0].$sub2;
				}
				else
				{
					$kl2="zzzzzzzzz";
					$j=1;
				}
				while($i<=$num1 or $j<=$num2)
				{
					echo $kl1." ".$kl2."<br>";
					if($kl1 == $kl2)
					{
						$k++;
						$i++;
						$j++;
						$data[$k][0]=$row1[0];
						$data[$k][1]=$row1[1];
						$data[$k][2]=$row1[2];
						$data[$k][3]=$row2[2];
						if($i > $num1)
							$kl1="zzzzzzzzz";
						else
						{
							$row1 = mysql_fetch_array($err1);
							$sub1 = $row1[1];
							while(strlen($sub1) < 5)
								$sub1 = $sub1."z";
							$kl1=$row1[0].$sub1;
						}
						if($j > $num2)
							$kl2="zzzzzzzzz";
						else
						{
							$row2 = mysql_fetch_array($err2);
							$sub2 = $row2[1];
							while(strlen($sub2) < 5)
								$sub2 = $sub2."z";
							$kl2=$row2[0].$sub2;
						}
					}
					else
						if($kl1 < $kl2)
						{
							$i++;
							if($i > $num1)
								$kl1="zzzzzzzzz";
							else
							{
								$row1 = mysql_fetch_array($err1);
								$sub1 = $row1[1];
								while(strlen($sub1) < 5)
									$sub1 = $sub1."z";
								$kl1=$row1[0].$sub1;
							}
						}
						else
						{
							$j++;
							if($j > $num2)
								$kl2="zzzzzzzzz";
							else
							{
								$row2 = mysql_fetch_array($err2);
								$sub2 = $row2[1];
								while(strlen($sub2) < 5)
									$sub2 = $sub2."z";
								$kl2=$row2[0].$sub2;
							}
						}
				 }
				echo "Numero de Registros : ".$k."<br>";
				for ($i=1;$i<=$k;$i++)
				{
					if($data[$i][3] > 0)
						$wpro = $data[$i][2] / $data[$i][3];
					else
						$wpro = 0;
					$query = "update ".$empresa."_000083 set Cxapro = ".$wpro." where Cxaano=".$wano3." and Cxames=".$wmes3." and Cxacco='".$data[$i][0]."' and Cxasub='".$data[$i][1]."' and Cxaemp='".$wemp."'  ";
					echo $query."<br>";
					$err1 = mysql_query($query,$conex);
					if ($err1 != 1)
						echo mysql_errno().":".mysql_error()."<br>";
					$count++;
					echo "REGISTRO ACTUALIZADO NRO : ".$count."<br>";

				}
				echo "TOTAL REGISTROS ACTUALIZADOS : ".$count;
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
