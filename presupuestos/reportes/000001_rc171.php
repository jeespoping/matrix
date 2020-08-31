<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<script type="text/javascript">
<!--
	function ejecutar(path)
	{
		window.open(path,'','width=1024,height=500,status=0,menubar=0,scrollbars=1,toolbar=0,directories=0,resizable=0');
	}
//-->
</script>
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Costos Comparativos x Unidad de Negocio (CP)</font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc171.php Ver. 2017-08-24</b></font></td></tr></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc171.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione"  or !isset($wpv) or !isset($wco) or !isset($wpro1) or !isset($wpro2)  or (strtoupper ($wco) != "N" and strtoupper ($wco) != "S") or !isset($wcco1) or !isset($wcco2) or !isset($wper1)  or !isset($wper2)   or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>COSTOS COMPARATIVOS X UNIDAD DE NEGOCIO (CP)</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco2' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Procedimiento Inicial</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wpro1' size=10 maxlength=10 value=0></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Procedimiento Final</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wpro2' size=10 maxlength=10 value=z></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Comentarios(S - Si / N - No)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wco' size=1 maxlength=1></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Porcentaje de Variacion</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wpv' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Empresa de  Proceso</td><td bgcolor=#cccccc align=center>";
			if($key=="costosyp" or (isset($call) and $call == "SIG") or (isset($call) and strtoupper ($call) == "SIC"))
				$query = "SELECT Empcod,Empdes  from ".$empresa."_000153 group by 1 order by Empcod";
			else
				$query = "SELECT Empcod,Empdes  from ".$empresa."_000153,".$empresa."_000125 where empleado = '".$key."' and empresa = Empcod group by 1 order by Empcod";
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
			$wtable= date("YmdHis");
			$wtable=" temp_".$wtable;
			$query = "Create table  IF NOT EXISTS ".$wtable." as ";
			$query = $query."select  Mprcco,Mprpro, Mprnom, Mprgru, Mprpor, Mprtip, Mprcon from ".$empresa."_000095 ";
			$query = $query."  where Mprcco  between '".$wcco1."' and '".$wcco2."'";
			$query = $query."    and Mprpro  between '".$wpro1."' and '".$wpro2."'";
			$query = $query."    and Mpremp = '".$wemp."'"; 
			$err = mysql_query($query,$conex);
			$windex="temp_".date("His");
			//$query = "CREATE UNIQUE INDEX ".$windex." on ".$wtable."(Mprcco(4),Mprpro(10),Mprgru(3),Mprpor)";
			$query = "CREATE INDEX ".$windex." on ".$wtable."(Mprcco(4),Mprpro(10),Mprgru(3),Mprpor)";
			$err = mysql_query($query,$conex) or die("Error en la creacion del index ".$query);
			//                  0       1       2       3       4       5         6        7       8       9       10      11
			$query = "select  Mprcco, cconom, Mprgru, grudes, Mprpro, Mprnom ,  Mprpor,  Pcpmes, Pcpctp, Pcppro, Mprtip, Mprcon from ".$empresa."_000155,".$wtable.",".$empresa."_000005,".$empresa."_000088  ";
			$query = $query."  where Pcpano  = ".$wanop;
			$query = $query."    and Pcpemp = '".$wemp."'"; 
			$query = $query."    and Pcpmes between ".$wper1." and ".$wper2;
			$query = $query."    and Pcpcco between '".$wcco1."' and '".$wcco2."'";
			$query = $query."    and Pcpcco = Mprcco ";
			$query = $query."    and Pcpcod = Mprpro ";
			$query = $query."    and Pcpgru = Mprgru ";
			$query = $query."    and Pcpcon = Mprcon ";
			$query = $query."    and Pcpcco = ccocod ";
			$query = $query."    and Pcpemp = ccoemp ";
			$query = $query."    and Mprgru = Grucod ";
			$query = $query."    and Pcpemp = Gruemp ";
			$query = $query."   order by Mprcco, Mprgru, Mprpro, Mprcon, Pcpmes ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<table border=1>";
			echo "<tr><td colspan=17 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=17 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=17 align=center>COSTOS COMPARATIVOS X UNIDAD DE NEGOCIO (CP)</td></tr>";
			echo "<tr><td colspan=17 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td colspan=17 align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. " A&Ntilde;O : ".$wanop."</td></tr>";
			$wdat=array();
			$wmeses=array();
			for ($i=$wper1;$i<=$wper2;$i++)
			{
				switch ($i)
					{
						case 1:
							$wmeses[$i]="ENE";
							break;
						case 2:
							$wmeses[$i]="FEB";
							break;
						case 3:
							$wmeses[$i]="MAR";
							break;
						case 4:
							$wmeses[$i]="ABR";
							break;
						case 5:
							$wmeses[$i]="MAY";
							break;
						case 6:
							$wmeses[$i]="JUN";
							break;
						case 7:
							$wmeses[$i]="JUL";
							break;
						case 8:
							$wmeses[$i]="AGO";
							break;
						case 9:
							$wmeses[$i]="SEP";
							break;
						case 10:
							$wmeses[$i]="OCT";
							break;
						case 11:
							$wmeses[$i]="NOV";
							break;
						case 12:
							$wmeses[$i]="DIC";
							break;
					}
			}
			echo "<tr><td><b>COD. PROCEDIMIENTO</b></td><td><b>NOM. PROCEDIMIENTO</b></td><td><b>CONCEPTO</b></td><td><b>% TERCERO</b></td><td><b>COSTO<BR>PROMEDIO</b></td><td><b>TMN</b></td>";
			for ($i=$wper1;$i<=$wper2;$i++)
				echo "<td align=center><b>".$wmeses[$i]."</b></td>";
			echo "</tr>";
			$seg=-1;
			$segn="";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($row[0].$row[2].$row[4].$row[11].(string)$row[6] != $segn)
				{
					$seg++;
					$segn=$row[0].$row[2].$row[4].$row[11].(string)$row[6];
					$wdat[$seg][0]=$row[0];
					$wdat[$seg][1]=$row[1];
					$wdat[$seg][2]=$row[2];
					$wdat[$seg][3]=$row[3];
					$wdat[$seg][4]=$row[4];
					$wdat[$seg][5]=$row[5];
					$wdat[$seg][6]=$row[6] * 100;
					for ($j=7;$j<19;$j++)
						$wdat[$seg][$j]=0;
				}
				$wdat[$seg][$row[7]+6]+=$row[8];
				$wdat[$seg][19] = $row[9];
				if($row[6] == 1)
					$wdat[$seg][20] = $row[9];
				else
					$wdat[$seg][20] = $row[9] / (1 - $row[6]);
				$wdat[$seg][21] = $row[10];
				$wdat[$seg][22] = $row[11];
			}
			if($num > 0)
			{
				$wcco = "";
				$wlin="";
				for ($i=0;$i<=$seg;$i++)
				{
					if ($wcco != $wdat[$i][0])
					{
						echo"<tr><td bgcolor=#99CCFF colspan=17>".$wdat[$i][0]."-".$wdat[$i][1]."</td></tr>";
						$wcco = $wdat[$i][0];
					}
					if ($wlin != $wdat[$i][2])
					{
						echo"<tr><td bgcolor=#FFCC66 colspan=17>".$wdat[$i][2]."-".$wdat[$i][3]."</td></tr>";
						$wlin = $wdat[$i][2];
					}
					if(strtoupper($wco) == "S")
						echo"<tr><td><img src='../../images/medical/presupuestos/P.PNG' alt='COD. PROCEDIMIENTO' />".$wdat[$i][4]."</td><td><img src='../../images/medical/presupuestos/P.PNG' alt='NOM. PROCEDIMIENTO' />".$wdat[$i][5]."</td><td><img src='../../images/medical/presupuestos/P.PNG' alt='CONCEPTO' />".$wdat[$i][22]."</td><td  align=right><img src='../../images/medical/presupuestos/P.PNG' alt='% TERCERO' />".number_format((double)$wdat[$i][6],2,'.',',')."</td><td  align=right><img src='../../images/medical/presupuestos/P.PNG' alt='COSTO PROMEDIO' />".number_format((double)$wdat[$i][19],2,'.',',')."</td><td  align=right><img src='../../images/medical/presupuestos/P.PNG' alt='TMN' />".number_format((double)$wdat[$i][20],2,'.',',')."</td>";
					else
						echo"<tr><td>".$wdat[$i][4]."</td><td>".$wdat[$i][5]."</td><td>".$wdat[$i][22]."</td><td align=right>".number_format((double)$wdat[$i][6],2,'.',',')."</td><td  align=right>".number_format((double)$wdat[$i][19],2,'.',',')."</td><td  align=right>".number_format((double)$wdat[$i][20],2,'.',',')."</td>";
					for ($j=$wper1;$j<=$wper2;$j++)
						if($j==$wper1 or $wdat[$i][$j+6-1] != 0)
						{
							if( $j > $wper1 and abs((($wdat[$i][$j+6] / $wdat[$i][$j+6-1]) - 1) * 100) > $wpv)
							{
								if($wdat[$i][21] == "P")
								{
									$path="/matrix/presupuestos/reportes/000001_rc183.php?wanop=".$wanop."&wper1=".$j."&wcco1=".$wcco."&wpro=".$wdat[$i][4]."&wgru=".$wlin."&wserv=S&wcon=".$wdat[$i][22]."&empresa=".$empresa."&wemp=".$wempt;
									if(strtoupper($wco) == "S")
										echo "<td align=right bgcolor=#FF9900 onclick='ejecutar(".chr(34).$path.chr(34).")'><img src='../../images/medical/presupuestos/P.PNG' alt='".$wmeses[$j]."' />".number_format((double)$wdat[$i][$j+6],0,'.',',')."</td>";
									else
										echo "<td align=right bgcolor=#FF9900 onclick='ejecutar(".chr(34).$path.chr(34).")'>".number_format((double)$wdat[$i][$j+6],0,'.',',')."</td>";
								}
								else
								{
									$path="/matrix/presupuestos/reportes/000001_rc92.php?wanop=".$wanop."&wper1=".$j."&wcco1=".$wcco."&wpro=".$wdat[$i][4]."&wgru=".$wlin."&wcon=".$wdat[$i][22]."&wserv=S&empresa=".$empresa."&wemp=".$wempt;
									if(strtoupper($wco) == "S")
										echo "<td align=right bgcolor=#FF9900 onclick='ejecutar(".chr(34).$path.chr(34).")'><img src='../../images/medical/presupuestos/P.PNG' alt='".$wmeses[$j]."' />".number_format((double)$wdat[$i][$j+6],0,'.',',')."</td>";
									else
										echo "<td align=right bgcolor=#FF9900 onclick='ejecutar(".chr(34).$path.chr(34).")'>".number_format((double)$wdat[$i][$j+6],0,'.',',')."</td>";
								}
							}
							else
							{
								if($wdat[$i][21] == "P")
								{
									$path="/matrix/presupuestos/reportes/000001_rc183.php?wanop=".$wanop."&wper1=".$j."&wcco1=".$wcco."&wpro=".$wdat[$i][4]."&wgru=".$wlin."&wserv=S&wcon=".$wdat[$i][22]."&empresa=".$empresa."&wemp=".$wempt;
									if(strtoupper($wco) == "S")
										echo "<td align=right bgcolor=#ffffff onclick='ejecutar(".chr(34).$path.chr(34).")'><img src='../../images/medical/presupuestos/P.PNG' alt='".$wmeses[$j]."' />".number_format((double)$wdat[$i][$j+6],0,'.',',')."</td>";
									else
										echo "<td align=right bgcolor=#ffffff onclick='ejecutar(".chr(34).$path.chr(34).")'>".number_format((double)$wdat[$i][$j+6],0,'.',',')."</td>";
								}
								else
								{
									$path="/matrix/presupuestos/reportes/000001_rc92.php?wanop=".$wanop."&wper1=".$j."&wcco1=".$wcco."&wpro=".$wdat[$i][4]."&wgru=".$wlin."&wcon=".$wdat[$i][22]."&wserv=S&empresa=".$empresa."&wemp=".$wempt;
									if(strtoupper($wco) == "S")
										echo "<td align=right bgcolor=#ffffff onclick='ejecutar(".chr(34).$path.chr(34).")'><img src='../../images/medical/presupuestos/P.PNG' alt='".$wmeses[$j]."' />".number_format((double)$wdat[$i][$j+6],0,'.',',')."</td>";
									else
										echo "<td align=right bgcolor=#ffffff onclick='ejecutar(".chr(34).$path.chr(34).")'>".number_format((double)$wdat[$i][$j+6],0,'.',',')."</td>";
								}
							}	
						}	
						else
							if(strtoupper($wco) == "S")
								echo "<td align=right bgcolor=#ffffff><img src='../../images/medical/presupuestos/P.PNG' alt='".$wmeses[$j]."' />".number_format((double)$wdat[$i][$j+6],0,'.',',')."</td>";	
							else
								echo "<td align=right bgcolor=#ffffff>".number_format((double)$wdat[$i][$j+6],0,'.',',')."</td>";	
					echo "</tr>";	
				}
				echo "</tr></table>";				
			}
			$query = "DROP table ".$wtable;
			$err = mysql_query($query,$conex);
		}
	}
?>
</body>
</html>
